<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
if ( ! class_exists( 'TVC_Admin_DB_Helper' ) ) {
	Class TVC_Admin_DB_Helper{
		public function __construct() {
			$this->includes();
		}
		public function includes() {
	  		//require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );    
		}

		public function tvc_row_count($table, $field_name = "*"){
			if($table ==""){
				return;
			}else{
				global $wpdb;
				$tablename = esc_sql($wpdb->prefix.$table);
				$field_name = esc_sql($field_name);
				$sql = $wpdb->prepare("select count(%s) from `$tablename`", $field_name);
				return $wpdb->get_var($sql);
			}			
		}

		public function tvc_add_row($table, $t_data = array(), $format = array()){
			if($table =="" || $t_data == ""){
				return;
			}else{
				global $wpdb;
				$tablename = esc_sql($wpdb->prefix .$table);
				return $wpdb->insert($tablename, $t_data, $format);
			}
		}

		public function tvc_update_row($table, $t_data, $where){
			if($table =="" || $t_data == "" ||  $where == ""){
				return;
			}else{
				global $wpdb;
				$tablename = esc_sql($wpdb->prefix .$table);
				return $wpdb->update($tablename, $t_data, $where);
			}
		}

		public function tvc_check_row($table, $key, $value){
			global $wpdb;
			if($table =="" ||  $value == "" || $key == ""){
				return;
			}else{
				$tablename = esc_sql($wpdb->prefix .$table);
				$sql = $wpdb->prepare("select count(*) from `$tablename` where {$key}=%s", $value);
				return $wpdb->get_var($sql);
			}
		}

		public function tvc_get_results_in_array($table, $where, $fields, $concat = false, $operator= ""){
			global $wpdb;
			if($table =="" ||  $where == "" || $fields == ""){
				return;
			}else{
				$where = explode("=",$where);
				$key = $where[0];
				$val = trim($where[1]);
				$tablename = esc_sql($wpdb->prefix .$table);
				if( $operator == "IN" ) {
					if($concat == true){
						$fields = implode(',\'_\',', $fields);
						$sql = $wpdb->prepare("select CONCAT(%s) as p_c_id from `$tablename` where {$key} IN {$val}", $fields);
						return $wpdb->get_col($sql);
					}else{
						$fields = esc_sql( implode('`,`', $fields) );
						$sql = $wpdb->prepare("select `$fields` from %i where {$key} IN {$val}", $tablename);
						return $wpdb->get_results($sql, ARRAY_A);
					}
				}else { // when $operator is '='
					if($concat == true){
						$fields = implode(',\'_\',', $fields);
						$sql = $wpdb->prepare("select CONCAT(%s) as p_c_id from `$tablename` where {$key}=%s", $fields, $val);
						return $wpdb->get_col($sql);
					}else{
						$fields = esc_sql( implode('`,`', $fields) );
						$sql = $wpdb->prepare("select `$fields` from `$tablename` where {$key} = %s", $val);
						return $wpdb->get_results($sql, ARRAY_A);
					}	
				}	

			}
		}

		public function tvc_get_results_in_array_feed($table, $where, $fields, $concat = false)
		{
			global $wpdb;
			if ($table == "" || $where == "" || $fields == "") {
				return;
			} else {
				$tablename = esc_sql($wpdb->prefix . $table);
				if ($concat == true) {
					$fields = implode(',\'_\',', $fields);
					$sql = $wpdb->prepare("select CONCAT($fields) as p_c_id from `$tablename` where $where", []);
					return $wpdb->get_col($sql);
				} else {
					$fields = esc_sql(implode('`,`', $fields));
					$sql = $wpdb->prepare("select `$fields` from `$tablename` where $where", []);
					return $wpdb->get_results($sql, ARRAY_A);
				}
			}
		}

		public function tvc_get_results($table){
			global $wpdb;
			if($table =="" ){
				return;
			}else {
				$tablename = esc_sql($wpdb->prefix .$table);				
				$sql = $wpdb->prepare("select * from %i", $tablename);
				return $wpdb->get_results($sql);										
			}
		}

		public function tvc_get_last_row($table, $fields=null){
			if($table ==""){
				return;
			}else{
				global $wpdb;
				$tablename = esc_sql($wpdb->prefix .$table);
				$sql = $wpdb->prepare("select * from %i ORDER BY id DESC LIMIT 1", $tablename);
				if($fields){
					$fields = implode('`,`', $fields);
					$sql = $wpdb->prepare("select `$fields` from %i ORDER BY id DESC LIMIT 1", $tablename);
				}
				return $wpdb->get_row($sql,ARRAY_A);
			}
		}

		public function tvc_get_counts_groupby($table, $fields_by){
			global $wpdb;
			if($table =="" ||  $fields_by == ""){
				return;
			}else{
				$tablename = esc_sql($wpdb->prefix .$table);
				$sql = $wpdb->prepare("select`$fields_by`, count(*) as count from %i GROUP BY `$fields_by` ORDER BY count DESC ", $tablename);
				return $wpdb->get_results($sql, ARRAY_A);
			}
		}	

		public function tvc_safe_truncate_table($table){
			global $wpdb;
			$query = $wpdb->prepare( 'SHOW TABLES LIKE %s', '%'.$wpdb->esc_like( $table ).'%' );   
			if ( $wpdb->get_var( $query ) === $table ) {
				$table = esc_sql($table);
				$wpdb->query("TRUNCATE TABLE `$table`");
			}
		}
	}
}