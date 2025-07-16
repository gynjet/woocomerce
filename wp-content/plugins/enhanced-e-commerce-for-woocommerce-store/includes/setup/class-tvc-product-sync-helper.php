<?php
if (!class_exists('TVCProductSyncHelper')) {
	class TVCProductSyncHelper
	{
		protected $merchantId;
		protected $accountId;
		protected $currentCustomerId;
		protected $subscriptionId;
		protected $country;
		protected $site_url;
		protected $category_wrapper_obj;
		protected $TVC_Admin_Helper;
		protected $TVC_Admin_DB_Helper;
		public function __construct()
		{
			$this->includes();
			$this->add_table_in_db();
			$this->TVC_Admin_Helper = new TVC_Admin_Helper();
			$this->TVC_Admin_DB_Helper = new TVC_Admin_DB_Helper();
			$this->category_wrapper_obj = new Tatvic_Category_Wrapper();
			$this->merchantId = $this->TVC_Admin_Helper->get_merchantId();
			$this->accountId = $this->TVC_Admin_Helper->get_main_merchantId();
			$this->currentCustomerId = 1; //$this->TVC_Admin_Helper->get_currentCustomerId();
			$this->subscriptionId = $this->TVC_Admin_Helper->get_subscriptionId();
			$this->country = $this->TVC_Admin_Helper->get_woo_country();
			$this->site_url = "admin.php?page=conversios-google-shopping-feed&tab=";
			add_action('admin_init', array($this, 'add_table_in_db'));
		}
		public function includes()
		{
			if (!class_exists('Tatvic_Category_Wrapper')) {
				require_once(__DIR__ . '/tatvic-category-wrapper.php');
			}
		}
		/*
		 * careate table batch wise for product sync
		 */
		public function add_table_in_db()
		{
			global $wpdb;
			$tablename = esc_sql($wpdb->prefix . "ee_product_sync_profile");
			if ($wpdb->get_var($wpdb->prepare('SHOW TABLES LIKE %s', '%'.$wpdb->esc_like($tablename).'%')) === $tablename) {
				$result = $wpdb->get_row($wpdb->prepare("SHOW COLUMNS FROM {$wpdb->prefix}ee_product_sync_profile WHERE FIELD = %s", "update_date"));
				if ($result->Type == 'date') {
					$wpdb->query($wpdb->prepare("ALTER TABLE %i Modify `update_date` DATETIME NULL", $tablename));
				}
			} else {
				$sql_create = "CREATE TABLE `$tablename` ( `id` BIGINT(20) NOT NULL AUTO_INCREMENT , `profile_title` VARCHAR(100) NULL , `g_cat_id` INT(10) NULL , `g_attribute_mapping` LONGTEXT NOT NULL , `update_date` DATETIME NULL , `status` INT(1) NOT NULL DEFAULT '1', PRIMARY KEY (`id`) );";
				if (maybe_create_table($tablename, $sql_create)) {
				}
			}

			$tablename = esc_sql($wpdb->prefix . "ee_prouct_pre_sync_data");
			if ($wpdb->get_var($wpdb->prepare('SHOW TABLES LIKE %s', '%'.$wpdb->esc_like($tablename).'%')) === $tablename) {
				$result = $wpdb->get_row($wpdb->prepare("SHOW COLUMNS FROM {$wpdb->prefix}ee_prouct_pre_sync_data WHERE FIELD = %s", "update_date"));
				if ($result->Type == 'date') {
					$wpdb->query($wpdb->prepare("ALTER TABLE %i Modify `update_date` DATETIME NULL", $tablename));
				}
				
				$pre_sync_result = $wpdb->get_var($wpdb->prepare("SHOW COLUMNS FROM {$wpdb->prefix}ee_prouct_pre_sync_data LIKE %s", '%'.$wpdb->esc_like('feedId').'%'));
				if ($pre_sync_result == '') {
					$wpdb->query($wpdb->prepare("ALTER TABLE %i ADD `feedId` int(11) NULL  AFTER `status`", $tablename));
				}

			} else {
				$sql_create = "CREATE TABLE `$tablename` ( `id` BIGINT(20) NOT NULL AUTO_INCREMENT , `w_product_id` BIGINT(20) NOT NULL , `w_cat_id` INT(10) NOT NULL , `g_cat_id` INT(10) NOT NULL , `product_sync_profile_id` INT(10) NOT NULL ,`create_date` DATETIME NULL DEFAULT CURRENT_TIMESTAMP, `update_date` DATETIME NULL , `status` INT(1) NOT NULL DEFAULT '0', `feedId` int(11) NULL , PRIMARY KEY (`id`) );";
				if (maybe_create_table($tablename, $sql_create)) {
				}
			}
		}

		public function get_product_category($product_id)
		{
			$output = [];
			$terms_ids = wp_get_post_terms($product_id, 'product_cat', array('fields' => 'ids'));
			// Loop though terms ids (product categories)
			foreach ($terms_ids as $term_id) {
				$term_names = [];
				// Loop through product category ancestors
				foreach (get_ancestors($term_id, 'product_cat') as $ancestor_id) {
					$term_names[] = get_term($ancestor_id, 'product_cat')->name;
					if (isset($output[$ancestor_id]) && $output[$ancestor_id] != "") {
						unset($output[$ancestor_id]);
					}
				}
				$term_names[] = get_term($term_id, 'product_cat')->name;
				// Add the formatted ancestors with the product category to main array
				$output[$term_id] = implode(' > ', $term_names);
			}
			$output = array_values($output);
			return $output;
		}
		/*
		 * careate products object for product sync
		 */
		public function tvc_get_map_product_attribute($products, $tvc_currency, $merchantId, $product_batch_size = 100)
		{
			try {
				if (!empty($products)) {
					global $wpdb;
					$tve_table_prefix = $wpdb->prefix;
					$items = [];
					$validProducts = [];
					$skipProducts = [];
					$product_ids = [];
					$deletedIds = [];
					$batchId = time();
					$sync_profile = $this->TVC_Admin_DB_Helper->tvc_get_results('ee_product_sync_profile');
					// set profile id in array key
					$sync_profile_data = array();
					if (!empty($sync_profile)) {
						foreach ($sync_profile as $key => $value) {
							$sync_profile_data[$value->id] = $value;
						}
					}
					if (empty($sync_profile_data)) {
						return array("error" => true, "message" => esc_html__("No product sync profiles find.", "enhanced-e-commerce-for-woocommerce-store"));
					}
					if (empty($products)) {
						return array("error" => true, "message" => esc_html__("Products not found.", "enhanced-e-commerce-for-woocommerce-store"));
					}
					$products_sync = 0;
					foreach ($products as $postkey => $postvalue) {
						$product_ids[] = $postvalue->w_product_id;
						$postmeta = [];
						$postmeta = $this->TVC_Admin_Helper->tvc_get_post_meta($postvalue->w_product_id);
						$prd = wc_get_product($postvalue->w_product_id);
						$postObj = (object) array_merge((array) get_post($postvalue->w_product_id), (array) $postmeta);
						$permalink = esc_url(get_permalink($postvalue->w_product_id));
						$product = array(
							//'offer_id'=>sanitize_text_field($postvalue->w_product_id),
							'channel' => 'online',
							'link' => esc_url(get_permalink($postvalue->w_product_id)),
							'google_product_category' => sanitize_text_field($postvalue->g_cat_id)
						);

						$temp_product = array();
						$fixed_att_select_list = array("gender", "age_group", "shipping", "tax", "content_language", "target_country", "condition");
						$formArray = "";
						if (isset($sync_profile_data[$postvalue->product_sync_profile_id]) && $sync_profile_data[$postvalue->product_sync_profile_id]->g_attribute_mapping) {
							$g_attribute_mapping = $sync_profile_data[$postvalue->product_sync_profile_id]->g_attribute_mapping;
							$formArray = json_decode($g_attribute_mapping, true);
						}

						if (empty($formArray)) {
							return array("error" => true, "message" => esc_html__("Product sync profile not found.", "enhanced-e-commerce-for-woocommerce-store"));
						}
						//$formArray = json_decode($postvalue->g_attribute_mapping, true);
						foreach ($fixed_att_select_list as $fixed_key) {
							if (isset($formArray[$fixed_key]) && $formArray[$fixed_key] != "") {
								if ($fixed_key == "shipping" && $formArray[$fixed_key] != "") {
									$temp_product[$fixed_key]['price']['value'] = sanitize_text_field($formArray[$fixed_key]);
									$temp_product[$fixed_key]['price']['currency'] = sanitize_text_field($tvc_currency);
									$temp_product[$fixed_key]['country'] = (isset($formArray['target_country'])) ? sanitize_text_field($formArray['target_country']) : '';
								} else if ($fixed_key == "tax" && $formArray[$fixed_key] != "") {
									$temp_product['taxes']['rate'] = sanitize_text_field($formArray[$fixed_key]);
									$temp_product['taxes']['country'] = isset($formArray['target_country']) ? sanitize_text_field($formArray['target_country']) : '';
								} else if ($formArray[$fixed_key] != "") {
									$temp_product[$fixed_key] = sanitize_text_field($formArray[$fixed_key]);
								}
							}
							unset($formArray[$fixed_key]);
						}

						$product = array_merge($temp_product, $product);

						if ($prd->get_type() == "variable") {
							/*$variation_attributes = $prd->get_variation_attributes();*/
							//$p_variations = $prd->get_available_variations(); 
							$p_variations = $prd->get_children();
							if (!empty($p_variations)) {
								foreach ($p_variations as $v_key => $variation_id) {
									$variation = wc_get_product($variation_id);
									if (empty($variation)) {
										continue;
									}
									$variation_description = wc_format_content($variation->get_description());
									unset($product['customAttributes']);
									$postmeta_var = (object) $this->TVC_Admin_Helper->tvc_get_post_meta($variation_id);
									$formArray_val = $formArray['title'];
									$product['title'] = (isset($postObj->$formArray_val)) ? sanitize_text_field($postObj->$formArray_val) : get_the_title($postvalue->w_product_id);
									$tvc_temp_desc_key = $formArray['description'];
									if ($tvc_temp_desc_key == 'post_excerpt' || $tvc_temp_desc_key == 'post_content' || $tvc_temp_desc_key == '') {
										$product['description'] = ($variation_description != "") ? sanitize_text_field($variation_description) : sanitize_text_field($postObj->$tvc_temp_desc_key);
									} else {
										$product['description'] = sanitize_text_field($postObj->$tvc_temp_desc_key);
									}
									$product['item_group_id'] = $postvalue->w_product_id;
									$productTypes = $this->get_product_category($postvalue->w_product_id);
									if (!empty($productTypes)) {
										$product['productTypes'] = $productTypes;
									}
									$image_id = $variation->get_image_id();
									$variation_permalink = esc_url(get_permalink($variation_id));
									$product['link'] = $variation_permalink != '' ? $variation_permalink : $permalink;
									$product['image_link'] = esc_url(wp_get_attachment_image_url($image_id, 'full'));
									$variation_attributes = $variation->get_variation_attributes();

									if (!empty($variation_attributes)) {
										foreach ($variation_attributes as $va_key => $va_value) {
											$va_key = str_replace("_", " ", $va_key);
											if (strpos($va_key, 'color') !== false) {
												$product['color'] = $va_value;
											} else if (strpos($va_key, 'size') !== false) {
												$product['sizes'] = $va_value;
											} else {
												$va_key = str_replace("attribute", "", $va_key);
												$product['customAttributes'][] = array("name" => $va_key, "value" => $va_value);
											}
										}
									}

									foreach ($formArray as $key => $value) {
										if ($key == 'id') {
											$product[$key] = isset($postmeta_var->$value) ? $postmeta_var->$value : $variation_id;
											$product['offer_id'] = isset($postmeta_var->$value) ? $postmeta_var->$value : $variation_id;
										} elseif ($key == 'gtin' && (isset($postmeta_var->$value) || isset($postObj->$value))) {
											$product[$key] = isset($postmeta_var->$value) ? $postmeta_var->$value : $postObj->$value;
										} elseif ($key == 'mpn' && (isset($postmeta_var->$value) || isset($postObj->$value))) {
											$product[$key] = isset($postmeta_var->$value) ? $postmeta_var->$value : $postObj->$value;
										} elseif ($key == 'price') {
											if (isset($postmeta_var->$value) && $postmeta_var->$value > 0) {
												$product[$key]['value'] = $postmeta_var->$value;
											} else if (isset($postmeta_var->_regular_price) && $postmeta_var->_regular_price && $postmeta_var->_regular_price > 0) {
												$product[$key]['value'] = $postmeta_var->_regular_price;
											} else if (isset($postmeta_var->_price) && $postmeta_var->_price && $postmeta_var->_price > 0) {
												$product[$key]['value'] = $postmeta_var->_price;
											} else if (isset($postmeta_var->_sale_price) && $postmeta_var->_sale_price && $postmeta_var->_sale_price > 0) {
												$product[$key]['value'] = $postmeta_var->_sale_price;
											} else {
												unset($product[$key]);
											}
											if (isset($product[$key]['value']) && $product[$key]['value'] > 0) {
												$product[$key]['currency'] = sanitize_text_field($tvc_currency);
											} else {
												$skipProducts[$postmeta_var->ID] = $postmeta_var;
											}
										} else if ($key == 'sale_price') {
											if (isset($postmeta_var->$value) && $postmeta_var->$value > 0) {
												$product[$key]['value'] = $postmeta_var->$value;
											} else if (isset($postmeta_var->_sale_price) && $postmeta_var->_sale_price && $postmeta_var->_sale_price > 0) {
												$product[$key]['value'] = $postmeta_var->_sale_price;
											} else {
												unset($product[$key]);
											}
											if (isset($product[$key]['value']) && $product[$key]['value'] > 0) {
												$product[$key]['currency'] = sanitize_text_field($tvc_currency);
											}
										} else if ($key == 'availability') {
											$tvc_find = array("instock", "outofstock", "onbackorder");
											$tvc_replace = array("in stock", "out of stock", "preorder");
											if (isset($postmeta_var->$value) && $postmeta_var->$value != "") {
												$stock_status = $postmeta_var->$value;
												$stock_status = str_replace($tvc_find, $tvc_replace, $stock_status);
												$product[$key] = sanitize_text_field($stock_status);
											} else {
												$stock_status = $postmeta_var->_stock_status;
												$stock_status = str_replace($tvc_find, $tvc_replace, $stock_status);
												$product[$key] = sanitize_text_field($stock_status);
											}
										} else if (in_array($key, array("brand"))) { //list of cutom option added (Pro user only)                    
											$product_brand = "";
											$is_custom_attr_brand = false;
											$woo_attr_list = json_decode(wp_json_encode($this->TVC_Admin_Helper->getTableData($tve_table_prefix . 'woocommerce_attribute_taxonomies', ['attribute_name'])), true);
											if (!empty($woo_attr_list)) {
												foreach ($woo_attr_list as $key_attr => $value_attr) {
													if (isset($value_attr['field']) && $value_attr['field'] == $value) {
														$is_custom_attr_brand = true;
														$product_brand = $this->TVC_Admin_Helper->get_custom_taxonomy_name($postvalue->w_product_id, "pa_" . $value);
													}
												}
											}
											if ($is_custom_attr_brand == false && $product_brand == "") {
												$product_brand = $this->TVC_Admin_Helper->add_additional_option_val_in_map_product_attribute($key, $postvalue->w_product_id);
											}
											if ($product_brand != "") {
												$product[$key] = sanitize_text_field($product_brand);
											}
										} else if (isset($postmeta_var->$value) && $postmeta_var->$value != "") {
											$product[$key] = sanitize_text_field($postmeta_var->$value);
										}
									}
									$item = [
										'merchant_id' => sanitize_text_field($merchantId),
										'batch_id' => sanitize_text_field(++$batchId),
										'method' => 'insert',
										'product' => $product
									];
									$items[] = $item;
									$validProducts[] = $postvalue;
								}
							} else {
								//Delete the variant product which does not have children
								$deletedIds[] = $postvalue->w_product_id;
							}

						} else {
							//simpleproduct: 
							$image_id = $prd->get_image_id();
							$product['image_link'] = esc_url(wp_get_attachment_image_url($image_id, 'full'));
							$productTypes = $this->get_product_category($postvalue->w_product_id);
							if (!empty($productTypes)) {
								$product['productTypes'] = $productTypes;
							}
							//$product['productTypes'] = "Apparel & Accessories";   
							foreach ($formArray as $key => $value) {
								if ($key == 'id') {
									$product[$key] = isset($postObj->$value) ? $postObj->$value : $postvalue->w_product_id;
									$product['offer_id'] = isset($postObj->$value) ? $postObj->$value : $postvalue->w_product_id;
								} elseif ($key == 'price') {
									if (isset($postObj->$value) && $postObj->$value > 0) {
										$product[$key]['value'] = $postObj->$value;
									} else if (isset($postObj->_regular_price) && $postObj->_regular_price && $postObj->_regular_price > 0) {
										$product[$key]['value'] = $postObj->_regular_price;
									} else if (isset($postObj->_price) && $postObj->_price && $postObj->_price > 0) {
										$product[$key]['value'] = $postObj->_price;
									} else if (isset($postObj->_sale_price) && $postObj->_sale_price && $postObj->_sale_price > 0) {
										$product[$key]['value'] = $postObj->_sale_price;
									}
									if (isset($product[$key]['value']) && $product[$key]['value'] > 0) {
										$product[$key]['currency'] = sanitize_text_field($tvc_currency);
									} else {
										$skipProducts[$postObj->ID] = $postObj;
									}
								} else if ($key == 'sale_price') {
									if (isset($postObj->$value) && $postObj->$value > 0) {
										$product[$key]['value'] = $postObj->$value;
									} else if (isset($postObj->_sale_price) && $postObj->_sale_price && $postObj->_sale_price > 0) {
										$product[$key]['value'] = $postObj->_sale_price;
									}
									if (isset($product[$key]['value']) && $product[$key]['value'] > 0) {
										$product[$key]['currency'] = sanitize_text_field($tvc_currency);
									}
								} else if ($key == 'availability') {
									$tvc_find = array("instock", "outofstock", "onbackorder");
									$tvc_replace = array("in stock", "out of stock", "preorder");
									if (isset($postObj->$value) && $postObj->$value != "") {
										$stock_status = $postObj->$value;
										$stock_status = str_replace($tvc_find, $tvc_replace, $stock_status);
										$product[$key] = sanitize_text_field($stock_status);
									} else {
										$stock_status = $postObj->_stock_status;
										$stock_status = str_replace($tvc_find, $tvc_replace, $stock_status);
										$product[$key] = sanitize_text_field($stock_status);
									}
								} else if (in_array($key, array("brand"))) {
									//list of cutom option added
									$product_brand = "";
									$is_custom_attr_brand = false;
									$woo_attr_list = json_decode(wp_json_encode($this->TVC_Admin_Helper->getTableData($tve_table_prefix . 'woocommerce_attribute_taxonomies', ['attribute_name'])), true);
									if (!empty($woo_attr_list)) {
										foreach ($woo_attr_list as $key_attr => $value_attr) {
											if (isset($value_attr['field']) && $value_attr['field'] == $value) {
												$is_custom_attr_brand = true;
												$product_brand = $this->TVC_Admin_Helper->get_custom_taxonomy_name($postvalue->w_product_id, "pa_" . $value);
											}
										}
									}
									if ($is_custom_attr_brand == false && $product_brand == "") {
										$product_brand = $this->TVC_Admin_Helper->add_additional_option_val_in_map_product_attribute($key, $postvalue->w_product_id);
									}
									if ($product_brand != "") {
										$product[$key] = sanitize_text_field($product_brand);
									}
								} else if (isset($postObj->$value) && $postObj->$value != "") {
									$product[$key] = $postObj->$value;
								}

							}
							$item = [
								'merchant_id' => sanitize_text_field($merchantId),
								'batch_id' => sanitize_text_field(++$batchId),
								'method' => 'insert',
								'product' => $product
							];
							$items[] = $item;
							$validProducts[] = $postvalue;
						}

						$products_sync++;
						if (count($items) >= $product_batch_size) {
							return array('error' => false, 'items' => $items, 'valid_products' => $validProducts, 'deleted_products' => $deletedIds, 'skip_products' => $skipProducts, 'product_ids' => $product_ids, 'last_sync_product_id' => $postvalue->id, 'products_sync' => $products_sync);
						}
					}
					return array('error' => false, 'items' => $items, 'valid_products' => $validProducts, 'deleted_products' => $deletedIds, 'skip_products' => $skipProducts, 'product_ids' => $product_ids, 'last_sync_product_id' => $postvalue->id, 'products_sync' => $products_sync);
				}
			} catch (Exception $e) {
				$this->TVC_Admin_Helper->plugin_log($e->getMessage(), 'product_sync');
			}
		}
		/*
		 * batch wise sync product, its call from ajax fuction
		 */
		public function call_batch_wise_sync_product($last_sync_product_id = null, $product_batch_size = 100)
		{
			if (!class_exists('CustomApi')) {
				require_once(ENHANCAD_PLUGIN_DIR . 'includes/setup/CustomApi.php');
			}
			$CustomApi = new CustomApi();
			$product_count = $this->TVC_Admin_DB_Helper->tvc_row_count('ee_prouct_pre_sync_data');
			//$count = 0;
			$pre_last_sync_product_id = sanitize_text_field($last_sync_product_id);
			if ($product_count > 0) {
				$tvc_currency = sanitize_text_field($this->TVC_Admin_Helper->get_woo_currency());
				$merchantId = sanitize_text_field($this->merchantId);
				$customerId = sanitize_text_field($this->currentCustomerId);
				$accountId = sanitize_text_field($this->accountId);
				$subscriptionId = sanitize_text_field($this->subscriptionId);
				$last_sync_product_id = sanitize_text_field(($last_sync_product_id > 0) ? $last_sync_product_id : 0);
				global $wpdb;
				$tablename = $wpdb->prefix . 'ee_prouct_pre_sync_data';

				$last_sync_product_id = esc_sql(intval($last_sync_product_id));
				$product_batch_size = esc_sql(intval($product_batch_size));
				$products = $wpdb->get_results($wpdb->prepare("select * from {$wpdb->prefix}ee_prouct_pre_sync_data where `id` > %d LIMIT %d", $last_sync_product_id, $product_batch_size), OBJECT);
				$entries = [];
				if (!empty($products)) {
					$TVC_Admin_Auto_Product_sync_Helper = new TVC_Admin_Auto_Product_sync_Helper();
					$TVC_Admin_Auto_Product_sync_Helper->update_last_sync_in_db_batch_wise($products, '1');
					$p_map_attribute = $this->tvc_get_map_product_attribute($products, $tvc_currency, $merchantId, $product_batch_size);
					if (!empty($p_map_attribute) && isset($p_map_attribute['items']) && !empty($p_map_attribute['items'])) {
						// call product sync API
						$data = [
							'merchant_id' => sanitize_text_field($accountId),
							'account_id' => sanitize_text_field($merchantId),
							'subscription_id' => sanitize_text_field($subscriptionId),
							'entries' => $p_map_attribute['items']
						];
						$response = $CustomApi->products_sync($data);

						//$last_sync_product_id =end($products)->id;
						$last_sync_product_id = $p_map_attribute['last_sync_product_id'];
						if ($response->error == false) {
							//"data"=> $p_map_attribute['items']
							//$products_sync =count($products);
							$products_sync = $p_map_attribute['products_sync'];
							return array('error' => false, 'products_sync' => $products_sync, 'skip_products' => $p_map_attribute['skip_products'], 'last_sync_product_id' => $last_sync_product_id, "products" => $products, "p_map_attribute" => $p_map_attribute);
						} else {
							return array('error' => true, 'message' => esc_attr($response->message), "products" => $products, "p_map_attribute" => $p_map_attribute);
						}
						// End call product sync API
						$sync_product_ids = (isset($p_map_attribute['product_ids'])) ? $p_map_attribute['product_ids'] : "";
					} else if (!empty($p_map_attribute['message'])) {
						return array('error' => true, 'message' => esc_attr($p_map_attribute['message']));
					}
				}
			}

		}

		/*
		 * Batch wise sync product, its call from ajax fuction
		 */
		public function call_batch_wise_auto_sync_product()
		{
			$ee_additional_data = $this->TVC_Admin_Helper->get_ee_additional_data();
			try {
				global $wpdb;
				$startTime = new DateTime();
				if (!class_exists('CustomApi')) {
					require_once(ENHANCAD_PLUGIN_DIR . 'includes/setup/CustomApi.php');
				}
				$CustomApi = new CustomApi();
				$tablename = esc_sql($wpdb->prefix . "ee_prouct_pre_sync_data");
				$product_count = $wpdb->get_var($wpdb->prepare("SELECT COUNT(*) as a from %i where status = 0", $tablename));
				if ($product_count > 0) {
					$product_batch_size = (isset($ee_additional_data['product_sync_batch_size']) && $ee_additional_data['product_sync_batch_size']) ? $ee_additional_data['product_sync_batch_size'] : 100;
					$tvc_currency = sanitize_text_field($this->TVC_Admin_Helper->get_woo_currency());
					$merchantId = sanitize_text_field($this->merchantId);
					$accountId = sanitize_text_field($this->accountId);
					$subscriptionId = sanitize_text_field($this->subscriptionId);
					$product_batch_size = esc_sql(intval($product_batch_size));
					//$product_batch_size = 1000;
					$products = $wpdb->get_results($wpdb->prepare("select * from  {$wpdb->prefix}ee_prouct_pre_sync_data where `status` = 0 ORDER BY id ASC LIMIT %d", $product_batch_size), OBJECT);
					if (!empty($products)) {
						$p_map_attribute = $this->tvc_get_map_product_attribute($products, $tvc_currency, $merchantId, $product_batch_size);

						//Delete the variant product which does not have children
						if (!empty($p_map_attribute) && isset($p_map_attribute['deleted_products']) && !empty($p_map_attribute['deleted_products'])) {
							$dids = esc_sql(implode(', ', $p_map_attribute['deleted_products']));
							$wpdb->query($wpdb->prepare("DELETE from %i where `w_product_id` in ($dids)", $tablename));
						}
						$TVC_Admin_Auto_Product_sync_Helper = new TVC_Admin_Auto_Product_sync_Helper();
						$TVC_Admin_Auto_Product_sync_Helper->update_last_sync_in_db_batch_wise($p_map_attribute['valid_products'], '1'); //Add data in sync product database
						if (!empty($p_map_attribute) && isset($p_map_attribute['items']) && !empty($p_map_attribute['items'])) {
							// call product sync API
							$data = [
								'merchant_id' => sanitize_text_field($accountId),
								'account_id' => sanitize_text_field($merchantId),
								'subscription_id' => sanitize_text_field($subscriptionId),
								'entries' => $p_map_attribute['items']
							];
							$this->TVC_Admin_Helper->plugin_log("Before product sync API Call for " . count($p_map_attribute['items']) . " products", 'product_sync');
							$response = $CustomApi->products_sync($data);
							$endTime = new DateTime();
							$diff = $endTime->diff($startTime);
							$this->TVC_Admin_Helper->plugin_log("Products sync API duration time " . $diff->i . " minutes" . $diff->s . " seconds", 'product_sync');
							$responseData['time_duration'] = $diff;
							update_option("ee_prod_response", serialize($responseData));

							// Update status in pre sync product database
							$TVC_Admin_Auto_Product_sync_Helper->update_product_status_pre_sync_data($p_map_attribute['last_sync_product_id']);

							if ($response->error == false) {								
								$products_sync = $p_map_attribute['products_sync'];

								$ee_additional_data['product_sync_alert'] = NULL;
								$this->TVC_Admin_Helper->set_ee_additional_data($ee_additional_data);
								return array('error' => false, 'message' => esc_attr("Sync successfully"), "products" => $products, "p_map_attribute" => $p_map_attribute, 'products_sync' => $products_sync, 'skip_products' => $p_map_attribute['skip_products']);
							} else {
								if (isset($response->message) && $response->message != "") {
									$this->TVC_Admin_Helper->plugin_log($response->message, 'product_sync');
									//$ee_additional_data['product_sync_alert'] = $response->message;
									//$TVC_Admin_Helper->set_ee_additional_data($ee_additional_data);
								}
								return array('error' => true, 'message' => isset($response->message) ? esc_attr($response->message) : "", "products" => $products, "p_map_attribute" => $p_map_attribute);
							}
						} else if (!empty($p_map_attribute['message'])) {
							return array('error' => true, 'message' => esc_attr($p_map_attribute['message']));
						}
					}
				} else {
					// add scheduled cron job
					// as_unschedule_all_actions( 'auto_product_sync_process_scheduler' );
				}
			} catch (Exception $e) {
				$ee_additional_data['product_sync_alert'] = $e->getMessage();
				$this->TVC_Admin_Helper->set_ee_additional_data($ee_additional_data);
				$this->TVC_Admin_Helper->plugin_log($e->getMessage(), 'product_sync');
			}
		}

		public function wooCommerceAttributes()
		{
			global $wpdb;
			$tve_table_prefix = $wpdb->prefix;
			$column1 = json_decode(wp_json_encode($this->TVC_Admin_Helper->getTableColumns($tve_table_prefix . 'posts')), true);
			$column2 = json_decode(wp_json_encode($this->TVC_Admin_Helper->getTableData($tve_table_prefix . 'postmeta', ['meta_key'])), true);
			$column3 = json_decode(wp_json_encode($this->TVC_Admin_Helper->getTableData($tve_table_prefix . 'woocommerce_attribute_taxonomies', ['attribute_name'])), true);

			return array_merge($column1, $column2, $column3);
		}

		public function call_batch_wise_auto_sync_product_feed_ee($feedId)
		{
			$conv_additional_data = $this->TVC_Admin_Helper->get_ee_additional_data();
			$this->TVC_Admin_Helper->plugin_log("EE call_batch_wise_auto_sync_product_feed", 'product_sync');
			$google_detail = $this->TVC_Admin_Helper->get_ee_options_data();
			if (isset($google_detail['setting'])) {
				if ($google_detail['setting']) {
					$googleDetail = $google_detail['setting'];
				}
			}
			$ee_options = $this->TVC_Admin_Helper->get_ee_options_settings();
			try {
				global $wpdb;
				$startTime = new DateTime();
				if (!class_exists('CustomApi')) {
					require_once ENHANCAD_PLUGIN_DIR . 'includes/setup/CustomApi.php';
				}
				$CustomApi = new CustomApi();
				$tablename = esc_sql($wpdb->prefix . "ee_prouct_pre_sync_data");
				$product_count = $wpdb->get_var($wpdb->prepare("SELECT COUNT(*) as a from {$wpdb->prefix}ee_prouct_pre_sync_data where `feedId` = %d AND `status` = 0", $feedId));

				if ($product_count > 0) {
					$TVC_Admin_DB_Helper = new TVC_Admin_DB_Helper();
					$where = '`id` = ' . esc_sql($feedId);
					$filed = array(
						'attributes',
						'channel_ids',
						'product_id_prefix',
						'tiktok_catalog_id',
						'target_country'
					);
					$result = $TVC_Admin_DB_Helper->tvc_get_results_in_array("ee_product_feed", $where, $filed);
					$product_batch_size = (isset($conv_additional_data['product_sync_batch_size']) && $conv_additional_data['product_sync_batch_size']) ? $conv_additional_data['product_sync_batch_size'] : 100;
					$tvc_currency = sanitize_text_field($this->TVC_Admin_Helper->get_woo_currency());
					$merchantId = sanitize_text_field($this->merchantId);
					$accountId = sanitize_text_field($this->accountId);
					$subscriptionId = sanitize_text_field($this->subscriptionId);
					$product_batch_size = esc_sql(intval($product_batch_size));
					$products = $wpdb->get_results($wpdb->prepare("select * from  `$tablename` where `feedId` = %d AND `status` = 0 LIMIT %d", [$feedId, $product_batch_size]), OBJECT);
					$feed_attribute = json_decode($result[0]['attributes']);
					$feed_attribute->target_country = $result[0]['target_country'];
					if (!empty($products)) {
						$p_map_attribute = $this->conv_get_feed_wise_map_product_attribute($products, $tvc_currency, $merchantId, $product_batch_size, $feed_attribute, $result[0]['product_id_prefix']);

						//Delete the variant product which does not have children
						if (!empty($p_map_attribute) && isset($p_map_attribute['deleted_products']) && !empty($p_map_attribute['deleted_products'])) {
							$tablename = esc_sql($wpdb->prefix . "ee_prouct_pre_sync_data");
							$dids = esc_sql(implode(', ', $p_map_attribute['deleted_products']));
							$wpdb->query($wpdb->prepare("DELETE from %i where `w_product_id` in ($dids)", $tablename));
						}
						$TVC_Admin_Auto_Product_sync_Helper = new TVC_Admin_Auto_Product_sync_Helper();
						$TVC_Admin_Auto_Product_sync_Helper->update_last_sync_in_db_batch_wise($p_map_attribute['valid_products'], $feedId); //Add data in sync product database
						if (!empty($p_map_attribute) && isset($p_map_attribute['items']) && !empty($p_map_attribute['items'])) {
							// call product sync API
							$data = [
								'merchant_id' => sanitize_text_field($accountId),
								'account_id' => sanitize_text_field($merchantId),
								'subscription_id' => sanitize_text_field($subscriptionId),
								'store_feed_id' => sanitize_text_field($feedId),
								'is_on_gmc' => strpos($result[0]['channel_ids'], '1') !== false ? true : false,
								'is_on_microsoft' => strpos($result[0]['channel_ids'], '4') !== false ? true : false,
								'is_on_tiktok' => strpos($result[0]['channel_ids'], '3') !== false ? true : false,
								'tiktok_catalog_id' => $result[0]['tiktok_catalog_id'],
								'tiktok_business_id' => sanitize_text_field($this->TVC_Admin_Helper->get_tiktok_business_id()),
								'is_on_facebook' => strpos($result[0]['channel_ids'], '2') !== false ? true : false,
								'business_id' =>  strpos($result[0]['channel_ids'], '2') !== false ? $ee_options['facebook_setting']['fb_business_id'] : '',
								'catalog_id' =>  strpos($result[0]['channel_ids'], '2') !== false ? $ee_options['facebook_setting']['fb_catalog_id'] : '',
								'ms_catalog_id' =>  strpos($result[0]['channel_ids'], '4') !== false ? $ee_options['ms_catalog_id'] : '',
								'ms_store_id' =>  strpos($result[0]['channel_ids'], '4') !== false ? $ee_options['microsoft_merchant_center_id'] : '',
								'entries' => $p_map_attribute['items']
							];

							$this->TVC_Admin_Helper->plugin_log("EE Before product sync API Call for " . is_array($p_map_attribute['items']) ? count($p_map_attribute['items']) : 0 . " products", 'product_sync');

							/**************************** API Call to GMC ****************************************************************************/
							/***
							 * check API One value for count is hard written, Check with Chirag before deploying very important.
							 * 
							 * 
							 * Important
							 * 
							 */
							$response = $CustomApi->feed_wise_products_sync($data);
							$endTime = new DateTime();
							$diff = $endTime->diff($startTime);
							$this->TVC_Admin_Helper->plugin_log("Products sync API duration time " . $diff->i . " minutes" . $diff->s . " seconds", 'product_sync');
							$responseData['time_duration'] = $diff;
							update_option("ee_prod_response", serialize($responseData));
							
							// Update status in pre sync product database
							$TVC_Admin_Auto_Product_sync_Helper->update_product_status_pre_sync_data_ee($products, $feedId);

							if ($response->error == false) {								
								$products_sync = $p_map_attribute['products_sync'];

								$conv_additional_data['product_sync_alert'] = NULL;
								$this->TVC_Admin_Helper->set_ee_additional_data($conv_additional_data);
								$feed_data = array(
									"product_sync_alert" => NULL,
								);
								$TVC_Admin_DB_Helper->tvc_update_row("ee_product_feed", $feed_data, array("id" => $feedId));
								return array('error' => false, 'message' => esc_attr("Sync successfully"), "products" => $products, "p_map_attribute" => $p_map_attribute, 'products_sync' => $products_sync, 'skip_products' => $p_map_attribute['skip_products']);
							} else {
								if (isset($response->message) && $response->message != "") {
									$this->TVC_Admin_Helper->plugin_log($response->message, 'product_sync');
									$conv_additional_data['product_sync_alert'] = $response->message;
									$this->TVC_Admin_Helper->set_ee_additional_data($conv_additional_data);
									$feed_data = array(
										"product_sync_alert" => $response->message,
									);
									$TVC_Admin_DB_Helper->tvc_update_row("ee_product_feed", $feed_data, array("id" => $feedId));
								}
								return array('error' => true, 'message' => isset($response->message) ? esc_attr($response->message) : "", "products" => $products, "p_map_attribute" => $p_map_attribute);
							}
						} else if (!empty($p_map_attribute['message'])) {
							return array('error' => true, 'message' => esc_attr($p_map_attribute['message']));
						}
					}
				} else {
					// add scheduled cron job					
					as_unschedule_all_actions('auto_feed_wise_product_sync_process_scheduler_ee', array("feedId" => $feedId));
				}
			} catch (Exception $e) {
				$feed_data = array(
					"product_sync_alert" => $e->getMessage(),
				);
				$TVC_Admin_DB_Helper->tvc_update_row("ee_product_feed", $feed_data, array("id" => $feedId));
				$conv_additional_data['product_sync_alert'] = $e->getMessage();
				$this->TVC_Admin_Helper->set_ee_additional_data($conv_additional_data);
				$this->TVC_Admin_Helper->plugin_log($e->getMessage(), 'product_sync');
			}

		}

		public function conv_get_feed_wise_map_product_attribute($products, $tvc_currency, $merchantId, $product_batch_size = 100, $feed_attribute = '', $prefix = '')
		{
			try {
				if (!empty($products)) {
					global $wpdb;
					$tve_table_prefix = $wpdb->prefix;
					$plan_id = sanitize_text_field($this->TVC_Admin_Helper->get_plan_id());
					$items = [];
					$validProducts = [];
					$skipProducts = [];
					$product_ids = [];
					$deletedIds = [];
					$batchId = time();
					$sync_profile = $this->TVC_Admin_DB_Helper->tvc_get_results('ee_product_sync_profile');
					// set profile id in array key
					$sync_profile_data = array();
					if (!empty($sync_profile)) {
						foreach ($sync_profile as $key => $value) {
							$sync_profile_data[$value->id] = $value;
						}
					}
					if (empty($sync_profile_data)) {
						return array("error" => true, "message" => esc_html__("No product sync profiles find.", "enhanced-e-commerce-for-woocommerce-store"));
					}
					if (empty($products)) {
						return array("error" => true, "message" => esc_html__("Products not found.", "enhanced-e-commerce-for-woocommerce-store"));
					}
					$products_sync = 0;
					$last_sync_id = 0;
					foreach ($products as $postkey => $postvalue) {
						$last_sync_id = $postvalue->w_product_id;
						$product_ids[] = $postvalue->w_product_id;
						$postmeta = [];
						$postmeta = $this->TVC_Admin_Helper->tvc_get_post_meta($postvalue->w_product_id);
						$prd = wc_get_product($postvalue->w_product_id);
						$postObj = (object) array_merge((array) get_post($postvalue->w_product_id), (array) $postmeta);
						$permalink = esc_url(get_permalink($postvalue->w_product_id));
						$product = array(
							'channel' => 'online',
							'link' => esc_url(get_permalink($postvalue->w_product_id)),
							'google_product_category' => sanitize_text_field($postvalue->g_cat_id) != '0' ? sanitize_text_field($postvalue->g_cat_id) : ''
						);

						$temp_product = array();
						$fixed_att_select_list = array("gender", "age_group", "shipping", "tax", "content_language", "target_country", "condition", "adult", "is_bundle", "identifier_exists");
						$formArray = (array) $feed_attribute;

						if (empty($formArray)) {
							return array("error" => true, "message" => esc_html__("Feed wise Product attribute not found.", "enhanced-e-commerce-for-woocommerce-store"));
						}
						foreach ($fixed_att_select_list as $fixed_key) {
							if (isset($formArray[$fixed_key]) && $formArray[$fixed_key] != "") {
								if ($fixed_key == "shipping" && $formArray[$fixed_key] != "") {
									$temp_product[$fixed_key]['price']['value'] = sanitize_text_field($formArray[$fixed_key]);
									$temp_product[$fixed_key]['price']['currency'] = sanitize_text_field($tvc_currency);
									$temp_product[$fixed_key]['country'] = isset($formArray['target_country']) ? sanitize_text_field($formArray['target_country']) : '';
								} else if ($fixed_key == "tax" && $formArray[$fixed_key] != "") {
									$temp_product['taxes']['rate'] = sanitize_text_field($formArray[$fixed_key]);
									$temp_product['taxes']['country'] = isset($formArray['target_country']) ? sanitize_text_field($formArray['target_country']) : '';
								} else if ($formArray[$fixed_key] != "") {
									$temp_product[$fixed_key] = sanitize_text_field($formArray[$fixed_key]);
								}
							}
							unset($formArray[$fixed_key]);
						}

						$product = array_merge($temp_product, $product);
						$conv_additional_data = $this->TVC_Admin_Helper->get_ee_additional_data();
						$product_id_prefix = $prefix;

						if ($prd->get_type() == "variable") {
							$p_variations = $prd->get_children();
							if (!empty($p_variations)) {
								foreach ($p_variations as $v_key => $variation_id) {
									$variation = wc_get_product($variation_id);
									if (empty($variation)) {
										continue;
									}
									if ($variation->get_stock_status() != 'instock') {
										//Delete outstock product 
										$deletedIds[] = $postvalue->w_product_id;
										continue;
									}
									$variation_description = wc_format_content($variation->get_description());
									unset($product['customAttributes']);
									$postmeta_var = (object) $this->TVC_Admin_Helper->tvc_get_post_meta($variation_id);
									$formArray_val = $formArray['title'];
									$product['title'] = (isset($postObj->$formArray_val)) ? sanitize_text_field($postObj->$formArray_val) : get_the_title($postvalue->w_product_id);
									$tvc_temp_desc_key = $formArray['description'];
									if ($tvc_temp_desc_key == 'post_excerpt' || $tvc_temp_desc_key == 'post_content') {
										$product['description'] = ($variation_description != "") ? sanitize_text_field($variation_description) : sanitize_text_field($postObj->$tvc_temp_desc_key);
									} else {
										$product['description'] = sanitize_text_field($postObj->$tvc_temp_desc_key);
									}

									$product['item_group_id'] = $postvalue->w_product_id;
									$productTypes = $this->get_product_category($postvalue->w_product_id);
									if (!empty($productTypes)) {
										$product['productTypes'] = $productTypes;
									}
									$image_id = $variation->get_image_id();
									$product['image_link'] = esc_url(wp_get_attachment_image_url($image_id, 'full'));
									$variation_permalink = esc_url(get_permalink($variation_id));
									$product['link'] = $variation_permalink != '' ? $variation_permalink : $permalink;
									$variation_attributes = $variation->get_variation_attributes();
									if (!empty($variation_attributes)) {
										foreach ($variation_attributes as $va_key => $va_value) {
											$va_key = str_replace("_", " ", $va_key);
											if (strpos($va_key, 'color') !== false) {
												$product['color'] = $va_value;
											} else if (strpos($va_key, 'size') !== false) {
												$product['sizes'] = $va_value;
											} else {
												$va_key = str_replace("attribute", "", $va_key);
												$product['customAttributes'][] = array("name" => $va_key, "value" => $va_value);
											}
										}
									}

									foreach ($formArray as $key => $value) {
										if ($key == 'id') {
											if (!empty($product_id_prefix && $product_id_prefix != '')) {
												$product[$key] = sanitize_text_field(isset($postmeta_var->$value) ? $product_id_prefix . $postmeta_var->$value : $product_id_prefix . $variation_id);
												$product['offer_id'] = sanitize_text_field(isset($postmeta_var->$value) ? $product_id_prefix . $postmeta_var->$value : $product_id_prefix . $variation_id);
											} else {
												$product[$key] = sanitize_text_field(isset($postmeta_var->$value) ? $postmeta_var->$value : $variation_id);
												$product['offer_id'] = sanitize_text_field(isset($postmeta_var->$value) ? $postmeta_var->$value : $variation_id);
											}
										} elseif ($key == 'gtin' && (isset($postmeta_var->$value) || isset($postObj->$value))) {
											$product[$key] = sanitize_text_field(isset($postmeta_var->$value) ? $postmeta_var->$value : $postObj->$value);
										} elseif ($key == 'mpn' && (isset($postmeta_var->$value) || isset($postObj->$value))) {
											$product[$key] = sanitize_text_field(isset($postmeta_var->$value) ? $postmeta_var->$value : $postObj->$value);
										} elseif ($key == 'price') {
											if (isset($postmeta_var->$value) && $postmeta_var->$value > 0) {
												$product[$key]['value'] = sanitize_text_field($postmeta_var->$value);
											} else if (isset($postmeta_var->_regular_price) && $postmeta_var->_regular_price && $postmeta_var->_regular_price > 0) {
												$product[$key]['value'] = sanitize_text_field($postmeta_var->_regular_price);
											} else if (isset($postmeta_var->_price) && $postmeta_var->_price && $postmeta_var->_price > 0) {
												$product[$key]['value'] = sanitize_text_field($postmeta_var->_price);
											} else if (isset($postmeta_var->_sale_price) && $postmeta_var->_sale_price && $postmeta_var->_sale_price > 0) {
												$product[$key]['value'] = sanitize_text_field($postmeta_var->_sale_price);
											} else {
												unset($product[$key]);
											}
											if (isset($product[$key]['value']) && $product[$key]['value'] > 0) {
												$product[$key]['currency'] = sanitize_text_field($tvc_currency);
											} 
											// else {
											// 	$skipProducts[$product->ID] = $postmeta_var;
											// }
										} else if ($key == 'sale_price') {
											if (isset($postmeta_var->$value) && $postmeta_var->$value > 0) {
												$product[$key]['value'] = $postmeta_var->$value;
											} else if (isset($postmeta_var->_sale_price) && $postmeta_var->_sale_price && $postmeta_var->_sale_price > 0) {
												$product[$key]['value'] = $postmeta_var->_sale_price;
											} else {
												unset($product[$key]);
											}
											if (isset($product[$key]['value']) && $product[$key]['value'] > 0) {
												$product[$key]['currency'] = sanitize_text_field($tvc_currency);
											}
										} else if ($key == 'availability') {
											$tvc_find = array("instock", "outofstock", "onbackorder");
											$tvc_replace = array("in stock", "out of stock", "preorder");
											if (isset($postmeta_var->$value) && $postmeta_var->$value != "") {
												$stock_status = $postmeta_var->$value;
												$stock_status = str_replace($tvc_find, $tvc_replace, $stock_status);
												$product[$key] = sanitize_text_field($stock_status);
											} else {
												$stock_status = $postmeta_var->_stock_status;
												$stock_status = str_replace($tvc_find, $tvc_replace, $stock_status);
												$product[$key] = sanitize_text_field($stock_status);
											}
										} else if (in_array($key, array("brand"))) { //list of cutom option added (Pro user only)                    
											$product_brand = "";
											$is_custom_attr_brand = false;
											$woo_attr_list = json_decode(wp_json_encode($this->TVC_Admin_Helper->getTableData($tve_table_prefix . 'woocommerce_attribute_taxonomies', ['attribute_name'])), true);
											if (!empty($woo_attr_list)) {
												foreach ($woo_attr_list as $key_attr => $value_attr) {
													if (isset($value_attr['field']) && $value_attr['field'] == $value) {
														$is_custom_attr_brand = true;
														$product_brand = $this->TVC_Admin_Helper->get_custom_taxonomy_name($postvalue->w_product_id, "pa_" . $value);
													}
												}
											}
											if ($is_custom_attr_brand == false && $product_brand == "") {
												$product_brand = $this->TVC_Admin_Helper->add_additional_option_val_in_map_product_attribute($key, $postvalue->w_product_id);
											}
											if ($product_brand != "") {
												$product[$key] = sanitize_text_field($product_brand);
											}
										} else if($key == 'product_weight'){
											if(isset($postmeta_var->$value) && $postmeta_var->$value != ""){
												$product[$key]['value'] = sanitize_text_field($postmeta_var->$value);
												$product[$key]['unit'] = get_option('woocommerce_weight_unit');
											}
											
										} else if($key == 'shipping_weight'){
											if(isset($postmeta_var->$value) && $postmeta_var->$value != ""){
											  $product[$key]['value'] = sanitize_text_field($postmeta_var->$value);
											  $product[$key]['unit'] = get_option('woocommerce_weight_unit');
											}
											
										} else if (isset($postmeta_var->$value) && $postmeta_var->$value != "") {
											$product[$key] = sanitize_text_field($postmeta_var->$value);
										}
									}
									$category_mapping = array(
										'google_product_category' => $postvalue->g_cat_id != '0' ? sanitize_text_field($postvalue->g_cat_id) : '',
										'facebook_product_category' => $postvalue->g_cat_id != '0' ? sanitize_text_field($postvalue->g_cat_id) : '',
									);
									$item = [
										'merchant_id' => sanitize_text_field($merchantId),
										'batch_id' => sanitize_text_field(++$batchId),
										'method' => sanitize_text_field('insert'),
										'product' => $product,
										'category_mapping' => $category_mapping,
									];
									$items[] = $item;

								}
								$validProducts[] = $postvalue;
							} else {
								//Delete the variant product which does not have children
								$deletedIds[] = $postvalue->w_product_id;
							}

						} else {
							//simpleproduct: 
							if ($prd->get_stock_status() != 'instock') {
								//Delete outstock product 
								$deletedIds[] = $postvalue->w_product_id;
								continue;
							}
							$image_id = $prd->get_image_id();
							$product['image_link'] = esc_url(wp_get_attachment_image_url($image_id, 'full'));
							$productTypes = $this->get_product_category($postvalue->w_product_id);
							if (!empty($productTypes)) {
								$product['productTypes'] = $productTypes;
							}
							foreach ($formArray as $key => $value) {
								if ($key == 'id') {
									if (!empty($product_id_prefix)) {
										$product[$key] = sanitize_text_field(isset($postObj->$value) ? $product_id_prefix . $postObj->$value : $product_id_prefix . $postvalue->w_product_id);
										$product['offer_id'] = sanitize_text_field(isset($postObj->$value) ? $product_id_prefix . $postObj->$value : $product_id_prefix . $postvalue->w_product_id);
									} else {
										$product[$key] = sanitize_text_field(isset($postObj->$value) ? $postObj->$value : $postvalue->w_product_id);
										$product['offer_id'] = sanitize_text_field(isset($postObj->$value) ? $postObj->$value : $postvalue->w_product_id);
									}
								} elseif ($key == 'price') {
									if (isset($postObj->$value) && $postObj->$value > 0) {
										$product[$key]['value'] = sanitize_text_field($postObj->$value);
									} else if (isset($postObj->_regular_price) && $postObj->_regular_price && $postObj->_regular_price > 0) {
										$product[$key]['value'] = sanitize_text_field($postObj->_regular_price);
									} else if (isset($postObj->_price) && $postObj->_price && $postObj->_price > 0) {
										$product[$key]['value'] = sanitize_text_field($postObj->_price);
									} else if (isset($postObj->_sale_price) && $postObj->_sale_price && $postObj->_sale_price > 0) {
										$product[$key]['value'] = sanitize_text_field($postObj->_sale_price);
									}
									if (isset($product[$key]['value']) && $product[$key]['value'] > 0) {
										$product[$key]['currency'] = sanitize_text_field($tvc_currency);
									} 
									// else {
									// 	$skipProducts[$postObj->ID] = $postObj;
									// }
								} else if ($key == 'sale_price') {
									if (isset($postObj->$value) && $postObj->$value > 0) {
										$product[$key]['value'] = $postObj->$value;
									} else if (isset($postObj->_sale_price) && $postObj->_sale_price && $postObj->_sale_price > 0) {
										$product[$key]['value'] = $postObj->_sale_price;
									}
									if (isset($product[$key]['value']) && $product[$key]['value'] > 0) {
										$product[$key]['currency'] = sanitize_text_field($tvc_currency);
									}
								} else if ($key == 'availability') {
									$tvc_find = array("instock", "outofstock", "onbackorder");
									$tvc_replace = array("in stock", "out of stock", "preorder");
									if (isset($postObj->$value) && $postObj->$value != "") {
										$stock_status = $postObj->$value;
										$stock_status = str_replace($tvc_find, $tvc_replace, $stock_status);
										$product[$key] = sanitize_text_field($stock_status);
									} else {
										$stock_status = $postObj->_stock_status;
										$stock_status = str_replace($tvc_find, $tvc_replace, $stock_status);
										$product[$key] = sanitize_text_field($stock_status);
									}
								} else if (in_array($key, array("brand"))) {
									//list of cutom option added
									$product_brand = "";
									$is_custom_attr_brand = false;
									$woo_attr_list = json_decode(wp_json_encode($this->TVC_Admin_Helper->getTableData($tve_table_prefix . 'woocommerce_attribute_taxonomies', ['attribute_name'])), true);
									if (!empty($woo_attr_list)) {
										foreach ($woo_attr_list as $key_attr => $value_attr) {
											if (isset($value_attr['field']) && $value_attr['field'] == $value) {
												$is_custom_attr_brand = true;
												$product_brand = $this->TVC_Admin_Helper->get_custom_taxonomy_name($postvalue->w_product_id, "pa_" . $value);
											}
										}
									}
									if ($is_custom_attr_brand == false && $product_brand == "") {
										$product_brand = $this->TVC_Admin_Helper->add_additional_option_val_in_map_product_attribute($key, $postvalue->w_product_id);
									}
									if ($product_brand != "") {
										$product[$key] = sanitize_text_field($product_brand);
									}
								} else if($key == 'product_weight'){
									if(isset($postObj->$value) && $postObj->$value != ""){
										$product[$key]['value'] = sanitize_text_field($postObj->$value);
										$product[$key]['unit'] = get_option('woocommerce_weight_unit');
									}
									
								} else if($key == 'shipping_weight'){
									if(isset($postObj->$value) && $postObj->$value != ""){
									  $product[$key]['value'] = sanitize_text_field($postObj->$value);
									  $product[$key]['unit'] = get_option('woocommerce_weight_unit');
									}
									
								} else if($key == 'product_shipping_class') {
									$product[$key] = $prd->get_shipping_class();
								} else if (isset($postObj->$value) && $postObj->$value != "") {
									$product[$key] = $postObj->$value;
								}
							}
							$category_mapping = array(
								'google_product_category' => $postvalue->g_cat_id != '0' ? sanitize_text_field($postvalue->g_cat_id) : '',
								'facebook_product_category' => $postvalue->g_cat_id != '0' ? sanitize_text_field($postvalue->g_cat_id) : '',
							);
							$item = [
								'merchant_id' => sanitize_text_field($merchantId),
								'batch_id' => sanitize_text_field(++$batchId),
								'method' => sanitize_text_field('insert'),
								'product' => $product,
								'category_mapping' => $category_mapping
							];
							$items[] = $item;
							$validProducts[] = $postvalue;
						}

						$products_sync++;
						if (count($items) >= $product_batch_size) {
							return array('error' => false, 'items' => $items, 'valid_products' => $validProducts, 'deleted_products' => $deletedIds, 'skip_products' => $skipProducts, 'product_ids' => $product_ids, 'last_sync_product_id' => $postvalue->id, 'products_sync' => $products_sync);
						}
					}
					return array('error' => false, 'items' => $items, 'valid_products' => $validProducts, 'deleted_products' => $deletedIds, 'skip_products' => $skipProducts, 'product_ids' => $product_ids, 'last_sync_product_id' => $last_sync_id, 'products_sync' => $products_sync);
				}
			} catch (Exception $e) {
				$this->TVC_Admin_Helper->plugin_log($e->getMessage(), 'product_sync');
			}
		}

		public function manualProductSync($feedId)
		{
			$TVC_Admin_Helper = new TVC_Admin_Helper();
			$TVC_Admin_Helper->plugin_log("Manual process start to sync product " . gmdate('Y-m-d H:i:s', current_time('timestamp')) . " feed Id " . $feedId, 'product_sync'); // Add logs 
			$TVC_Admin_DB_Helper = new TVC_Admin_DB_Helper();
			$ee_options = $TVC_Admin_Helper->get_ee_options_settings();
			try {
				global $wpdb;
				$where = '`id` = ' . esc_sql($feedId);
				$filed = ['feed_name', 'channel_ids', 'auto_sync_interval', 'auto_schedule', 'categories', 'attributes', 'filters', 'include_product', 'exclude_product', 'is_mapping_update', 'product_id_prefix', 'tiktok_catalog_id', 'target_country'];
				$result = $TVC_Admin_DB_Helper->tvc_get_results_in_array("ee_product_feed", $where, $filed);
				if (!empty($result) && isset($result) && $result[0]['is_mapping_update'] == '1') {
					$TVC_Admin_Helper->plugin_log("Found Feed Id", 'product_sync'); // Add logs
					$product_id_prefix = $result[0]['product_id_prefix'];
					$filters = json_decode($result[0]['filters']);
					$filters_count = is_array($filters) ? count($filters) : '';
					$categories = is_null($result[0]['categories']) ? '' : json_decode($result[0]['categories']);
					$attributes = json_decode($result[0]['attributes']);
					$attributes->target_country = $result[0]['target_country'];
					$include = $result[0]['include_product'] != '' ? explode(",", $result[0]['include_product']) : '';
					$exclude = explode(",", $result[0]['exclude_product']);
					$where = array();
					// $conditionprod = '';
					// $whereSKUJoin = '';
					// $wherePriJoin = '';
					// $condition = $conditionSKU = $conditionContent = $conditionExcerpt = $conditionPrice = $conditionRegPrice = $whereStockJoin = $conditionStock = '';
					// $product_cat1 = $product_cat2 = $product_id1 = $product_id2 = $whereCond = $whereCondsku = $whereCondcontent = $whereExcerpt = $whereCondregPri = $whereCondPri = $wherestock = array();

					$in_category_ids = array();
					$not_in_category_ids = array();
					$stock_status_to_fetch = array();
					$not_stock_status_to_fetch = $product_ids_to_exclude = $product_ids_to_include = $search = array();
					if ($filters_count != '') {
						for ($i = 0; $i < $filters_count; $i++) {
						switch ($filters[$i]->attr) {
							case 'product_cat':
							if ($filters[$i]->condition == "=") {
								array_push($in_category_ids,$filters[$i]->value);
							} else if ($filters[$i]->condition == "!=") {
								array_push($not_in_category_ids,$filters[$i]->value);
							}
							break;
							case '_stock_status':
							if (!empty($filters[$i]->condition) && $filters[$i]->condition == "=") {
								array_push($stock_status_to_fetch,$filters[$i]->value);
							} else if (!empty($filters[$i]->condition) && $filters[$i]->condition == "!=") {
								array_push($not_stock_status_to_fetch,$filters[$i]->value);
							}
							break;
							case 'ID':
							if ($filters[$i]->condition == "=") {
								array_push($product_ids_to_include,$filters[$i]->value);
							} else if ($filters[$i]->condition == "!=") {
								array_push($product_ids_to_exclude,$filters[$i]->value);                
							}
							break;							
						}
						}
					}


					if ($include == '') {
						$tax_query = array();
						if (!empty($in_category_ids)) {
							$tax_query[] = array(
								'taxonomy' => 'product_cat',
								'field'    => 'term_id',
								'terms'    => $in_category_ids,
								'operator' => 'IN', // Retrieve products in any of the specified categories
							);
						}
						if (!empty($not_in_category_ids)) {
							$tax_query[] = array(
								'taxonomy' => 'product_cat',
								'field'    => 'term_id',
								'terms'    => $not_in_category_ids,
								'operator' => 'NOT IN', // Exclude products in any of the specified categories
							);
						}
						if(!empty($in_category_ids) && !empty($not_in_category_ids)) {
							$tax_query = array('relation' => 'AND');
						}
						$meta_query = array();
						if (!empty($stock_status_to_fetch)) {
						$meta_query[] = array(
							'key'     => '_stock_status',
							'value'   => $stock_status_to_fetch,
							'compare' => 'IN', // Include products with these stock statuses
						);
						}
					
						// Add not_stock_status_to_fetch condition
						if (!empty($not_stock_status_to_fetch)) {
							$meta_query[] = array(
								'key'     => '_stock_status',
								'value'   => $not_stock_status_to_fetch,
								'compare' => 'NOT IN', // Exclude products with these stock statuses
							);
						}
						if(!empty($stock_status_to_fetch) && !empty($not_stock_status_to_fetch)) {
							$meta_query = array('relation' => 'AND');
						}
						$args = array(
						'post_type'      => 'product',
						'posts_per_page' => 100,
						'post_status'    => 'publish',
						's'              => $search,
						'offset'         => 0,
						'tax_query'      => $tax_query, // Dynamic tax query
						'meta_query'     => $meta_query,            
						'post__not_in'   => $product_ids_to_exclude,
						'post__in'       => $product_ids_to_include,
						);
			
						$products  = new WP_Query( $args );
						$allResult = array();
						if($products->have_posts()) {
							while ( $products->have_posts() ) { 
								$products->the_post();  
								$allResult[]['ID'] = get_the_ID();
								// array_push($allResult, get_the_ID());                    
							}          
						}
						
					} else {
						
						$TVC_Admin_Helper->plugin_log("Only include product", 'product_sync'); // Add logs               
						foreach ($include as $val) {
							$allResult[]['ID'] = $val;
						}
					}
				} else {
					$TVC_Admin_Helper->plugin_log("Empty result for feed id = " . $feedId, 'product_sync'); // Add logs 
				}
				if (!empty($allResult)) {
					$TVC_Admin_Helper->plugin_log("woow 103", 'product_sync');
					$all_cat = [];
					if($categories) {
						foreach ($categories as $cat_key => $cat_val) {
							$all_cat[$cat_key] = $cat_key;
						}
					}
					
					$totProduct = 0;
					$a = 0;
					$object = [];
					foreach ($allResult as $postvalue) {
						$have_cat = false; // variable to get any mapped category with product
						if (!in_array($postvalue['ID'], $exclude)) {
							//get all mapped categories with product
							$terms = get_the_terms(sanitize_text_field($postvalue['ID']), 'product_cat');
							if ( $terms && ! is_wp_error( $terms ) ) {
								foreach ($terms as $key => $term) {
									$cat_id = $term->term_id;
									if (isset($all_cat[$cat_id]) && $term->term_id == $all_cat[$cat_id] && $have_cat == false) {
										$cat_matched_id = $term->term_id;
										$have_cat = true;
									}
								}
							}
							if ($have_cat == true) {
								$totProduct++;
								$object[] = (object) ['w_product_id' => $postvalue['ID'], 'w_cat_id' => $cat_matched_id, 'g_cat_id' => $categories->$cat_matched_id->id];
							}else {
								$totProduct++;
								$object[] = (object) ['w_product_id' => $postvalue['ID'], 'w_cat_id' => $cat_id, 'g_cat_id' => ''];
							}
						}
					}
					$tvc_currency = sanitize_text_field($TVC_Admin_Helper->get_woo_currency());
					$merchantId = sanitize_text_field($TVC_Admin_Helper->get_merchantId());
					$accountId = sanitize_text_field($TVC_Admin_Helper->get_main_merchantId());
					$subscriptionId = sanitize_text_field(sanitize_text_field($TVC_Admin_Helper->get_subscriptionId()));
					$product_batch_size = 100;
					//map each product with category and attribute
					$p_map_attribute = $this->conv_get_feed_wise_map_product_attribute($object, $tvc_currency, $merchantId, $product_batch_size, $attributes, $product_id_prefix);					
					
					$TVC_Admin_Auto_Product_sync_Helper = new TVC_Admin_Auto_Product_sync_Helper();
					$TVC_Admin_Auto_Product_sync_Helper->update_last_sync_in_db_batch_wise($p_map_attribute['valid_products'], $feedId);
					if (!empty($p_map_attribute) && isset($p_map_attribute['items']) && !empty($p_map_attribute['items'])) {
						$TVC_Admin_Helper->plugin_log("woow 108", 'product_sync');
						$data = [
							'merchant_id' => sanitize_text_field($accountId),
							'account_id' => sanitize_text_field($merchantId),
							'subscription_id' => sanitize_text_field($subscriptionId),
							'store_feed_id' => sanitize_text_field($feedId),
							'is_on_gmc' => strpos($result[0]['channel_ids'], '1') !== false ? true : false,
							'is_on_microsoft' => strpos($result[0]['channel_ids'], '4') !== false ? true : false,
							'is_on_tiktok' => strpos($result[0]['channel_ids'], '3') !== false ? true : false,
							'tiktok_catalog_id' => $result[0]['tiktok_catalog_id'],
							'tiktok_business_id' => sanitize_text_field($TVC_Admin_Helper->get_tiktok_business_id()),
							'is_on_facebook' => strpos($result[0]['channel_ids'], '2') !== false ? true : false,
							'business_id' =>  strpos($result[0]['channel_ids'], '2') !== false ? $ee_options['facebook_setting']['fb_business_id'] : '',
							'catalog_id' =>  strpos($result[0]['channel_ids'], '2') !== false ? $ee_options['facebook_setting']['fb_catalog_id'] : '',
							'ms_catalog_id' =>  strpos($result[0]['channel_ids'], '4') !== false ? $ee_options['ms_catalog_id'] : '',
							'ms_store_id' =>  strpos($result[0]['channel_ids'], '4') !== false ? $ee_options['microsoft_merchant_center_id'] : '',
							'entries' => $p_map_attribute['items']
						];
						/**************************** API Call to GMC ****************************************************************************/
						$CustomApi = new CustomApi();
						$response = $CustomApi->feed_wise_products_sync($data,'call_by-includes/setup/class-tvc-product-sync-helper.php 1224');
						$endTime = new DateTime();
						$startTime = new DateTime();
						$diff = $endTime->diff($startTime);
						$responseData['time_duration'] = $diff;
						update_option("ee_prod_response", serialize($response));
						if ($response->error == false) {
							$TVC_Admin_Helper->plugin_log("woow 109 success", 'product_sync');
							$feed_data = array(
								"product_sync_alert" => NULL,
								"total_product" => $totProduct,
							);
							$TVC_Admin_DB_Helper->tvc_update_row("ee_product_feed", $feed_data, array("id" => $feedId));
							$syn_data = array(
								'status' => 1
							);
							$TVC_Admin_DB_Helper->tvc_update_row("ee_product_sync_data", $syn_data, array("feedId" => $feedId));
							$sync_message = esc_html__("By API Initiated, products are being synced to Merchant Center.Do not refresh.", "enhanced-e-commerce-for-woocommerce-store");
							$sync_message = esc_html( $sync_message );
							$sync_progressive_data = array("sync_message" => $sync_message);
							return array('status' => 'success', "sync_progressive_data" => $sync_progressive_data);
							exit;
						} else {

							if ( is_string($response->message) ){
								$message = $response->message;
							}else {
								$message = 'Error in Sync:' . json_encode($response->message);
							}

							if( strpos( $message, 'limit' ) !== false && strpos( $message, 'maximum' ) !== false ) {
								//Your maximum limit to add product is over.
								update_option( 'ee_conv_total_synced_product_count', 101 );
							}

							$TVC_Admin_Helper->plugin_log("Error in sync 1010:" . $message , 'product_sync');

							return array('status' => 'error', "message" => $message);
							exit;
						}
					}
				}
				$TVC_Admin_Helper->plugin_log("woow 104 success", 'product_sync');
			} catch (Exception $e) {
				
				$feed_data = array(
					"product_sync_alert" => $e->getMessage(),
					"is_mapping_update" => false,
				);
				$TVC_Admin_DB_Helper->tvc_update_row("ee_product_feed", $feed_data, array("id" => $feedId));
				$TVC_Admin_Helper->plugin_log($e->getMessage(), 'product_sync');
			}
		}

		public function superFeedProductSync($feedId){
			$TVC_Admin_DB_Helper = new TVC_Admin_DB_Helper();
			$TVC_Admin_Helper = new TVC_Admin_Helper();		
			$ee_options = $TVC_Admin_Helper->get_ee_options_settings();
			global $wpdb;
			$where = '`id` = ' . esc_sql($feedId);
			$filed = ['feed_name', 'channel_ids', 'auto_sync_interval', 'auto_schedule', 'categories', 'attributes', 'filters', 'include_product', 'exclude_product', 'is_mapping_update', 'product_id_prefix', 'tiktok_catalog_id'];
			$result = $TVC_Admin_DB_Helper->tvc_get_results_in_array("ee_product_feed", $where, $filed);
			$categories = json_decode($result[0]['categories']);
			$attributes = json_decode($result[0]['attributes']);
			$product_id_prefix = $result[0]['product_id_prefix'];
			
			$query = $wpdb->prepare(
				"SELECT {$wpdb->prefix}posts.ID
				FROM {$wpdb->prefix}posts
				LEFT JOIN {$wpdb->prefix}postmeta pm1 ON pm1.post_id = {$wpdb->prefix}posts.ID
				WHERE {$wpdb->prefix}posts.post_type = %s
				AND {$wpdb->prefix}posts.post_status = %s
				AND pm1.meta_key LIKE %s
				AND pm1.meta_value LIKE %s
				GROUP BY {$wpdb->prefix}posts.ID
				ORDER BY DATE({$wpdb->prefix}posts.post_modified) DESC
				LIMIT 100",
				'product',
				'publish',
				'_stock_status',
				'instock'
			);
			
			$allResult = $wpdb->get_results($query, ARRAY_A);
			
			$TVC_Admin_Helper->plugin_log("Get all result", 'product_sync');
			if (!empty($allResult)) {
				$all_cat = [];
				foreach ($categories as $cat_key => $cat_val) {
					$all_cat[$cat_key] = $cat_key;
				}
				foreach ($allResult as $postvalue) {
					$terms = get_the_terms(sanitize_text_field($postvalue['ID']), 'product_cat');
					if ( $terms && ! is_wp_error( $terms ) ) {
						foreach ($terms as $key => $term) {							
							$cat_matched_id = $term->term_id;
						}	
					}					
					//$object[] = (object) ['w_product_id' => $postvalue['ID'], 'w_cat_id' => $cat_matched_id, 'g_cat_id' => $categories->$cat_matched_id->id];
					$object[] = (object) ['w_product_id' => $postvalue['ID'], 'w_cat_id' => $cat_matched_id, 'g_cat_id' => 0];
				}

				//add/update data in default profile
				$profile_data = array("profile_title" => esc_sql("Super AI Feed"), "g_attribute_mapping" => wp_json_encode($attributes), "update_date" => gmdate('Y-m-d'));
				if ($TVC_Admin_DB_Helper->tvc_row_count("ee_product_sync_profile") == 0) {
					$TVC_Admin_DB_Helper->tvc_add_row("ee_product_sync_profile", $profile_data, array("%s", "%s", "%s"));
				} else {
					$TVC_Admin_DB_Helper->tvc_update_row("ee_product_sync_profile", $profile_data, array("id" => 1));
				}

				$tvc_currency = sanitize_text_field($TVC_Admin_Helper->get_woo_currency());
				$merchantId = sanitize_text_field($TVC_Admin_Helper->get_merchantId());
				$accountId = sanitize_text_field($TVC_Admin_Helper->get_main_merchantId());
				$subscriptionId = sanitize_text_field(sanitize_text_field($TVC_Admin_Helper->get_subscriptionId()));
				$product_batch_size = 100;
				$p_map_attribute = $this->conv_get_feed_wise_map_product_attribute($object, $tvc_currency, $merchantId, $product_batch_size, $attributes, $product_id_prefix);
				$TVC_Admin_Auto_Product_sync_Helper = new TVC_Admin_Auto_Product_sync_Helper();
				$TVC_Admin_Auto_Product_sync_Helper->update_last_sync_in_db_batch_wise($p_map_attribute['valid_products'], $feedId);
				if (!empty($p_map_attribute) && isset($p_map_attribute['items']) && !empty($p_map_attribute['items'])) {					
					$data = [
						'merchant_id' => sanitize_text_field($accountId),
						'account_id' => sanitize_text_field($merchantId),
						'subscription_id' => sanitize_text_field($subscriptionId),
						'store_feed_id' => sanitize_text_field($feedId),
						'is_on_gmc' => strpos($result[0]['channel_ids'], '1') !== false ? true : false,
						'is_on_microsoft' => strpos($result[0]['channel_ids'], '4') !== false ? true : false,
						'is_on_tiktok' => strpos($result[0]['channel_ids'], '3') !== false ? true : false,
						'tiktok_catalog_id' => $result[0]['tiktok_catalog_id'],
						'tiktok_business_id' => sanitize_text_field($TVC_Admin_Helper->get_tiktok_business_id()),
						'is_on_facebook' => strpos($result[0]['channel_ids'], '2') !== false ? true : false,
						'business_id' =>  strpos($result[0]['channel_ids'], '2') !== false ? $ee_options['facebook_setting']['fb_business_id'] : '',
						'catalog_id' =>  strpos($result[0]['channel_ids'], '2') !== false ? $ee_options['facebook_setting']['fb_catalog_id'] : '',
						'ms_catalog_id' =>  strpos($result[0]['channel_ids'], '4') !== false ? $ee_options['ms_catalog_id'] : '',
						'ms_store_id' =>  strpos($result[0]['channel_ids'], '4') !== false ? $ee_options['microsoft_merchant_center_id'] : '',
						'entries' => $p_map_attribute['items']
					];

					/**************************** API Call to GMC ****************************************************************************/
					$CustomApi = new CustomApi();
					$response = $CustomApi->feed_wise_products_sync($data);
					$endTime = new DateTime();
					$startTime = new DateTime();
					$diff = $endTime->diff($startTime);
					$responseData['time_duration'] = $diff;
					update_option("ee_prod_response", serialize($responseData));
					if ($response->error == false) {
						$feed_data = array(
							"product_sync_alert" => NULL,
							"total_product" => count($p_map_attribute['items']),
						);
						$TVC_Admin_DB_Helper->tvc_update_row("ee_product_feed", $feed_data, array("id" => $feedId));
						$syn_data = array(
							'status' => 1
						);
						$TVC_Admin_DB_Helper->tvc_update_row("ee_product_sync_data", $syn_data, array("feedId" => $feedId));
						$sync_message = esc_html__("Via API Initiated, products are being synced to Merchant Center. Do not refresh.", "enhanced-e-commerce-for-woocommerce-store");
						$sync_message = esc_html( $sync_message );
						$sync_progressive_data = array("sync_message" => $sync_message);
						$TVC_Admin_Helper->plugin_log(count($p_map_attribute['items']).' Product Synced', 'product_sync');
						return array('status' => 'success', "sync_progressive_data" => $sync_progressive_data);
						exit;
					} else {
						$TVC_Admin_Helper->plugin_log($response->message, 'product_sync');
						return array('error' => true, 'message' => esc_attr('Error in Sync...'));
						exit;
					}
				}	
			}else {
				$TVC_Admin_Helper->plugin_log("No data found", 'product_sync');
			}
		}	

	}
}