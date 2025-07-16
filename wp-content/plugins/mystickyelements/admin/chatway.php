<?php
// You may comment this out IF you're sure the function exists.
require_once ABSPATH . 'wp-admin/includes/plugin-install.php';
remove_all_filters('plugins_api');
$plugins_allowedtags = array(
			'a'       => array(
				'href'   => array(),
				'title'  => array(),
				'target' => array(),
			),
			'abbr'    => array( 'title' => array() ),
			'acronym' => array( 'title' => array() ),
			'code'    => array(),
			'pre'     => array(),
			'em'      => array(),
			'strong'  => array(),
			'ul'      => array(),
			'ol'      => array(),
			'li'      => array(),
			'p'       => array(),
			'br'      => array(),
		);

// Chatway Plugins
$args = [
    'slug'   => 'chatway-live-chat',
    'fields' => [
        'short_description' => true,
        'icons'             => true,
        'reviews'           => false,
// excludes all reviews
    ],
];
$data = plugins_api('plugin_information', $args);

$chatway_plugin = array();
if ($data && ! is_wp_error($data)) {
    $chatway_plugin['chatway']       = $data;
    $chatway_plugin['chatway']->name = 'Free Live Chat, WordPress Website Chat Plugin, Support Chat App: Chatway';
    $chatway_plugin['chatway']->short_description = 'Live chat with your website’s visitors through your WordPress website. With Chatway – live chat app, you can do just that and much more!';
}
if( class_exists( 'Chatway' ) ) {
	$social_channels['chatway_enable'] = 1;
}

$style = 'block';
$image_style = 'none';
if ( !isset($social_channels['chatway_enable']) || ( isset($social_channels['chatway_enable']) && $social_channels['chatway_enable'] != 1 )){
	$style = 'none';
	$image_style = 'block';
}
?>
<div id="mystickyelements-tab-live-chatway" class="mystickyelements-tab-live-chatway mystickyelements-options" style="display: <?php echo ( isset($widget_tab_index) && $widget_tab_index == 'mystickyelements-live-chatway' ) ? 'block' : 'none'; ?>;">

	<div class="myStickyelements-header-title myStickyelements-chatway-header">
		<h3><?php _e('Live Chat', 'mystickyelements'); ?></h3>		
		<div class="myStickyelements-chatway-left">
		<?php if( class_exists( 'Chatway' ) ) : ?>
			<div class="mystickyelements-header-sub-title">
				<h4><?php esc_html_e('You already have Chatway installed for live chat. ', 'mystickyelements');?></h4>
			</div>
			<p>			
				<?php esc_html_e( 'If you wish to disable the Live Chat for a specific page and only display the MySticky Elements widget, you can disable it from here.', 'mystickyelements' ); ?>
			</p>
		<?php else : ?>
			
			<div class="mystickyelements-header-sub-title">
				<h4><?php esc_html_e('Need better way to connect with visitors?', 'mystickyelements');?></h4>
			</div>
			<p>
				<?php echo wp_kses(__( 'Add the Chatway Live Chat widget to your website and effectively communicate with visitors with features such as:', 'mystickyelements' ), $plugins_allowedtags); ?>
			</p>
			<ul>
				<li><?php esc_html_e('Unlimited conversations, email, and Facebook Messenger integrations', 'mystickyelements');?></li>
				<li><?php esc_html_e('Team collaboration with agents', 'mystickyelements');?></li>
				<li><?php esc_html_e('Canned responses, private notes, reminders, and more', 'mystickyelements');?></li>
				<li><?php esc_html_e('iOS & Android apps available', 'mystickyelements');?></li>
			</ul>			
		<?php endif;?>
		
			<div class="mystickyelement-tab-boxes-btn-wrap">
				<?php if( class_exists( 'Chatway' ) ) : ?>
					<a href="<?php echo admin_url('admin.php?page=chatway')?>" target="_blank" class="btn">
						<svg width="20" height="24" viewBox="0 0 20 24" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M7.3669 22.7087L9.00454 19.846L10.1913 21.7047C10.1913 21.7047 9.43739 21.5704 8.75067 21.8989C8.06394 22.2273 7.3669 22.7087 7.3669 22.7087Z" fill="#0038A5"/><path d="M6.19341 21.3436C6.06426 21.0492 5.7976 20.838 5.48147 20.7796L1.5873 20.0607C0.667542 19.8909 0 19.0888 0 18.1535V6.53588C0 5.10561 0.700916 3.76614 1.87601 2.95077L4.38973 1.20656C5.38282 0.517475 6.60698 0.246381 7.79816 0.451756L16.7802 2.00039C18.6407 2.32116 20 3.93487 20 5.82278V14.6237C20 15.6655 19.5809 16.6635 18.8372 17.393L15.6382 20.5305C14.4251 21.7202 12.6985 22.2263 11.0351 21.8798L9.17661 21.4926C8.84529 21.4235 8.50196 21.5322 8.27074 21.7794L7.48924 22.6146C7.25139 22.8689 6.83107 22.797 6.6912 22.4782L6.19341 21.3436Z" fill="#0446DE"/><path d="M4.26402 4.3534C2.31122 3.95658 0.484924 5.44908 0.484924 7.4418V17.3662C0.484924 18.3011 1.15191 19.1029 2.07118 19.2732L5.92902 19.9876C6.25151 20.0473 6.52196 20.266 6.64786 20.5688L6.99399 21.4014C7.09887 21.6537 7.4341 21.7046 7.60906 21.4947L8.27676 20.6939C8.47749 20.4531 8.78223 20.3242 9.0948 20.3479L12.1623 20.5803C13.71 20.6976 15.0304 19.4734 15.0304 17.9213V8.12613C15.0304 7.2039 14.3809 6.40923 13.4772 6.22558L4.26402 4.3534Z" fill="#0038A5"/><path d="M4.05471 4.34384C2.85779 4.11172 1.74609 5.02853 1.74609 6.24776V16.4525C1.74609 17.4163 2.45394 18.2339 3.40788 18.3719L6.05423 18.7546C6.37641 18.8012 6.6537 19.0064 6.79253 19.3008L7.1644 20.0896C7.26724 20.3424 7.60161 20.396 7.77835 20.188L8.3385 19.538C8.55472 19.2871 8.88406 19.1639 9.21187 19.2113L12.8133 19.7322C13.9827 19.9014 15.0303 18.9944 15.0303 17.8128V8.07175C15.0303 7.14301 14.3719 6.34464 13.4601 6.16783L4.05471 4.34384Z" fill="white"/><path d="M10.9095 14.5922L5.31137 13.6108C4.90406 13.5394 4.57266 13.8652 4.73023 14.2475C5.24204 15.4894 6.67158 17.4417 9.20419 16.7908C9.72572 16.6567 10.9053 15.9787 11.2377 15.0756C11.3207 14.85 11.1463 14.6337 10.9095 14.5922Z" fill="#0446DE"/><ellipse cx="5.50291" cy="9.96607" rx="0.992567" ry="1.70154" transform="rotate(-4.90348 5.50291 9.96607)" fill="#0446DE"/><ellipse cx="10.7489" cy="10.9349" rx="0.992567" ry="1.70154" transform="rotate(-4.90348 10.7489 10.9349)" fill="#0446DE"/></svg>
						<?php esc_html_e('Manage Live Chat','mystickyelements');?>
					</a>

				<?php else: ?>
					<a href="<?php echo admin_url('admin.php?page=install-chatway-plugin')?>" target="_blank" class="btn">
						<svg width="20" height="24" viewBox="0 0 20 24" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M7.3669 22.7087L9.00454 19.846L10.1913 21.7047C10.1913 21.7047 9.43739 21.5704 8.75067 21.8989C8.06394 22.2273 7.3669 22.7087 7.3669 22.7087Z" fill="#0038A5"/><path d="M6.19341 21.3436C6.06426 21.0492 5.7976 20.838 5.48147 20.7796L1.5873 20.0607C0.667542 19.8909 0 19.0888 0 18.1535V6.53588C0 5.10561 0.700916 3.76614 1.87601 2.95077L4.38973 1.20656C5.38282 0.517475 6.60698 0.246381 7.79816 0.451756L16.7802 2.00039C18.6407 2.32116 20 3.93487 20 5.82278V14.6237C20 15.6655 19.5809 16.6635 18.8372 17.393L15.6382 20.5305C14.4251 21.7202 12.6985 22.2263 11.0351 21.8798L9.17661 21.4926C8.84529 21.4235 8.50196 21.5322 8.27074 21.7794L7.48924 22.6146C7.25139 22.8689 6.83107 22.797 6.6912 22.4782L6.19341 21.3436Z" fill="#0446DE"/><path d="M4.26402 4.3534C2.31122 3.95658 0.484924 5.44908 0.484924 7.4418V17.3662C0.484924 18.3011 1.15191 19.1029 2.07118 19.2732L5.92902 19.9876C6.25151 20.0473 6.52196 20.266 6.64786 20.5688L6.99399 21.4014C7.09887 21.6537 7.4341 21.7046 7.60906 21.4947L8.27676 20.6939C8.47749 20.4531 8.78223 20.3242 9.0948 20.3479L12.1623 20.5803C13.71 20.6976 15.0304 19.4734 15.0304 17.9213V8.12613C15.0304 7.2039 14.3809 6.40923 13.4772 6.22558L4.26402 4.3534Z" fill="#0038A5"/><path d="M4.05471 4.34384C2.85779 4.11172 1.74609 5.02853 1.74609 6.24776V16.4525C1.74609 17.4163 2.45394 18.2339 3.40788 18.3719L6.05423 18.7546C6.37641 18.8012 6.6537 19.0064 6.79253 19.3008L7.1644 20.0896C7.26724 20.3424 7.60161 20.396 7.77835 20.188L8.3385 19.538C8.55472 19.2871 8.88406 19.1639 9.21187 19.2113L12.8133 19.7322C13.9827 19.9014 15.0303 18.9944 15.0303 17.8128V8.07175C15.0303 7.14301 14.3719 6.34464 13.4601 6.16783L4.05471 4.34384Z" fill="white"/><path d="M10.9095 14.5922L5.31137 13.6108C4.90406 13.5394 4.57266 13.8652 4.73023 14.2475C5.24204 15.4894 6.67158 17.4417 9.20419 16.7908C9.72572 16.6567 10.9053 15.9787 11.2377 15.0756C11.3207 14.85 11.1463 14.6337 10.9095 14.5922Z" fill="#0446DE"/><ellipse cx="5.50291" cy="9.96607" rx="0.992567" ry="1.70154" transform="rotate(-4.90348 5.50291 9.96607)" fill="#0446DE"/><ellipse cx="10.7489" cy="10.9349" rx="0.992567" ry="1.70154" transform="rotate(-4.90348 10.7489 10.9349)" fill="#0446DE"/></svg>
						<?php esc_html_e('Add Live Chat','mystickyelements');?>
					</a>
				<?php endif;?>
				<span class="small-text"><?php esc_html_e('You can skip this step by saving the widget','mystickyelements');?></span>
			</div>
		</div> 
		
		<div class="myStickyelements-chatway-right">
			<img src="<?php echo MYSTICKYELEMENTS_URL ?>/images/chatway.png" class="recommended-chatway-plugin" alt="Chatway Plugin" />
		</div>
	</div>
</div>