<?php

/**
 * Fired during plugin deactivation
 *
 * @link       test.com
 * @since      1.0.0
 *
 * @package    Enhanced_Ecommerce_Google_Analytics
 * @subpackage Enhanced_Ecommerce_Google_Analytics/includes
 */

/**
 * Fired during plugin deactivation.
 *
 * This class defines all code necessary to run during the plugin's deactivation.
 *
 * @since      1.0.0
 * @package    Enhanced_Ecommerce_Google_Analytics
 * @subpackage Enhanced_Ecommerce_Google_Analytics/includes
 * @author     Tatvic
 */
class Enhanced_Ecommerce_Google_Analytics_Deactivator
{

	/**
	 * Short Description. (use period)
	 *
	 * Long Description.
	 *
	 * @since    1.0.0
	 */
	public static function deactivate()
	{
		if (!current_user_can('activate_plugins')) {
			return;
		}
		$TVC_Admin_Helper = new TVC_Admin_Helper();
		$TVC_Admin_Helper->update_app_status("0");
		$TVC_Admin_Helper->app_activity_detail("deactivate");
	}
}