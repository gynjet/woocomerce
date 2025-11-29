<?php
namespace Elementor;
use TrustindexPlugin_Google;
use Elementor\Widget_Base;
defined('ABSPATH') or die('No script kiddies please!');
class TrustrindexElementorWidget_Google extends Widget_Base {
private $pluginManagerInstance;
private $shortName;
private $platformName;
public function __construct($data = [], $args = null) {
parent::__construct($data, $args);
$this->pluginManagerInstance = $args[0];
$this->shortName = $this->pluginManagerInstance->getShortName();
$this->platformName = $this->pluginManagerInstance->get_platform_name($this->shortName);
}
public function get_name() {
return $this->platformName;
}
public function get_title() {
return $this->platformName .' '. __('Reviews', 'trustindex-plugin') . ' - Trustindex';
}
public function get_icon() {
return 'eicon-star';
}
public function get_categories() {
return ['trustindex', 'general'];
}
protected function register_controls(): void
{
$this->start_controls_section('content_section', [ 'label' => __('Widget type', 'trustindex-plugin') ]);
$this->add_control('type', [
'type' => \Elementor\Controls_Manager::CHOOSE,
'label' => '',
'label_block' => true,
'show_label' => false,
'options' => [
'free' => [
'title' => '',
'icon' => '',
'label' => ucfirst(__('free', 'trustindex-plugin')) . ' widget',
],
'pro' => [
'title' => '',
'icon' => '',
'label' => 'PRO widget',
],
],
'default' => 'free',
]
);
if (defined('\Elementor\Controls_Manager::NOTICE')) {
$this->add_control('custom_panel_notice', [
'type' => \Elementor\Controls_Manager::NOTICE,
'notice_type' => 'success',
'heading' => __('UPGRADE to PRO Features', 'trustindex-plugin'),
'content' => sprintf(__('Automatic review update, creating unlimited review widgets, downloading and displaying all reviews, %d review platforms available!', 'trustindex-plugin'), 135)
. '<br /><br /><a href="https://www.trustindex.io/?a=sys&c=wp-google-elementor" target="_blank">'.__('Create a Free Account for More Features', 'trustindex-plugin').'</a>',
'condition' => [ 'type' => 'free' ],
]);
}
$this->add_control('embed_code', [
'type' => \Elementor\Controls_Manager::TEXTAREA,
'label' => 'Widget shortcode',
'placeholder' => sprintf(__('Paste the widget shortcode from the Advanced Widget Editor on the %s.', 'trustindex-plugin'), 'admin.trusintdex.io'),
'label_block' => true,
'condition' => [ 'type' => 'pro' ],
]);
$this->end_controls_section();
}
protected function render()
{
$settings = $this->get_settings_for_display();
$shortcode = '['.$this->pluginManagerInstance->get_shortcode_name().' no-registration='.$this->pluginManagerInstance->getShortName().']';
if ('pro' === $settings['type'] && $settings['embed_code']) {
$shortcode = $settings['embed_code'];
}
echo do_shortcode($shortcode);
}
}
if (!class_exists('Elementor\Control_Choose2')) {
class Control_Choose2 extends Control_Choose {
public function content_template() {
$control_uid_input_type = '{{value}}';
?>
<div class="elementor-control-field">
<label class="elementor-control-title">{{{ data.label }}}</label>
<div class="elementor-control-input-wrapper">
<div class="elementor-choices">
<# _.each( data.options, function( options, value ) { #>
<input id="<?php $this->print_control_uid( $control_uid_input_type ); ?>" type="radio" name="elementor-choose-{{ data.name }}-{{ data._cid }}" value="{{ value }}">
<label class="elementor-choices-label elementor-control-unit-1 tooltip-target" for="<?php $this->print_control_uid( $control_uid_input_type ); ?>" data-tooltip="{{ options.title }}" title="{{ options.title }}">
<# if ( options.icon ) { #>
<i class="{{ options.icon }}" aria-hidden="true"></i>
<# } #>
<span class="elementor-screen-only">{{{ options.title }}}</span>
{{{ options.label }}}
</label>
<# } ); #>
</div>
</div>
</div>
<# if ( data.description ) { #>
<div class="elementor-control-field-description">{{{ data.description }}}</div>
<# } #>
<?php
}
}
}
