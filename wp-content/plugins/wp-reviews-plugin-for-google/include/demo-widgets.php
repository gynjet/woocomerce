<?php
defined('ABSPATH') or die('No script kiddies please!');
?>
<div class="ti-preview-boxes-container">
<div class="ti-full-width">
<div class="ti-box ti-preview-boxes">
<div class="ti-box-inner">
<div class="ti-box-header ti-box-header-normal">
<?php echo __('Example Widget', 'trustindex-plugin'); ?>:
<strong><?php echo esc_html(__($pluginManager::$widget_templates['templates'][4]['name'], 'trustindex-plugin')); ?></strong>
 (<?php echo esc_html(__($pluginManager::$widget_styles['light-background']['name'], 'trustindex-plugin')); ?>)
</div>
<div class="preview"><?php echo $pluginManagerInstance->renderWidgetFrontend('2d9bf9019f8d93ad1430e9135'); ?></div>
</div>
</div>
</div>
<?php
$demoList = [
5 => 'light-minimal',
36 => 'ligth-border',
34 => 'drop-shadow',
13 => 'dark-background',
15 => 'drop-shadow',
37 => 'ligth-border',
33 => 'light-minimal',
16 => 'drop-shadow',
31 => 'soft',
54 => 'light-background',
6 => 'light-background',
18 => 'light-background',
8 => 'light-background',
97 => 'light-minimal',
98 => 'light-minimal',
99 => 'light-minimal',
100 => 'light-minimal',
101 => 'ligth-border',
102 => 'ligth-border',
103 => 'ligth-border',
104 => 'ligth-border',
27 => 'ligth-border',
26 => 'ligth-border',
29 => 'drop-shadow',
30 => 'dark-background',
60 => 'light-background',
25 => 'light-background',
32 => 'dark-background',
109 => 'drop-shadow',
110 => 'drop-shadow',
111 => 'drop-shadow',
22 => 'light-background',
23 => 'ligth-border',
55 => 'light-minimal',
11 => 'drop-shadow',
12 => 'light-minimal'
];
foreach ($demoList as $layout => $style): ?>
<?php
$template = $pluginManager::$widget_templates['templates'][ $layout ];
$className = 'ti-full-width';
if (in_array($template['type'], [ 'badge', 'button', 'floating', 'popup', 'sidebar', 'top-rated-badge' ])) {
$className = 'ti-half-width';
}
?>
<div class="<?php echo esc_attr($className); ?>">
<div class="ti-box ti-preview-boxes" data-layout-id="<?php echo esc_attr($layout); ?>" data-set-id="<?php echo esc_attr($style); ?>">
<div class="ti-box-inner">
<div class="ti-box-header ti-box-header-normal">
<?php echo __('Example Widget', 'trustindex-plugin'); ?>:
<strong><?php echo esc_html(__($template['name'], 'trustindex-plugin')); ?></strong>
 (<?php echo esc_html(__($pluginManager::$widget_styles[ $style ]['name'], 'trustindex-plugin')); ?>)
</div>
<div class="preview"><?php echo $pluginManagerInstance->renderWidgetAdmin(true, true, ['style-id' => $layout, 'set-id' => $style]); ?></div>
</div>
</div>
</div>
<?php endforeach; ?>
</div>