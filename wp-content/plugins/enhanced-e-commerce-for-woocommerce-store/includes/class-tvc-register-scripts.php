<?php
/**
 * TVC Register Scripts Class.
 *
 * @package TVC Product Feed Manager/Classes
 */
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}
if ( ! class_exists( 'TVC_Register_Scripts' ) ) :
  /**
   * Register Scripts Class
   */
  class TVC_Register_Scripts {
    public function __construct() {    
        // only load the next hooks when on the Settings page
      if ( isset($_GET['page']) && strpos(sanitize_text_field(wp_unslash($_GET['page'])), 'conversios') !== false) {
        add_action( 'admin_enqueue_scripts', array( $this, 'tvc_register_required_options_page_scripts' ) );
      }
    } 
    
    /**
     * Registers all required java scripts for the feed manager Settings page.
     */
    public function tvc_register_required_options_page_scripts() {
      // enqueue notice handling script
      ?>
<script>
var tvc_ajax_url = '<?php echo esc_js(admin_url( 'admin-ajax.php' )); ?>';
</script>
<?php
    }
  }
// End of TVC_Register_Scripts class
endif;
$my_ajax_registration_class = new TVC_Register_Scripts();
?>