<?php
defined('ABSPATH') or die('No script kiddies please!');
?>
<ul class="ti-step-list">
<?php foreach ($stepList as $i => $name): ?>
<?php if ($i > 0): ?>
<span class="ti-step-arrow"></span>
<?php endif; ?>
<?php
$classList = [];
if ($stepDone >= $i + 1) {
$classList []= 'ti-done';
}
else if ($stepDone == $i) {
$classList []= 'ti-active';
}
if ($stepCurrent === $i + 1) {
$classList []= 'ti-current';
}
?>
<li class="<?php echo esc_attr(implode(' ', $classList)); ?>">
<a href="<?php echo esc_attr(str_replace('%step%', $i + 1, $stepUrl)); ?>">
<span><?php echo esc_html($i + 1); ?></span>
<div><?php echo esc_html($name); ?></div>
</a>
</li>
<?php endforeach; ?>
<div class="ti-step-buttons <?php if (isset($stepRightButton)): ?>ti-has-multiple<?php endif; ?>">
<?php if ($stepCurrent > 1): ?>
<a href="<?php echo esc_attr(str_replace('%step%', $stepCurrent - 1, $stepUrl)); ?>" class="ti-btn ti-btn-default"><?php echo esc_html(__('Back', 'trustindex-plugin')); ?></a>
<?php endif; ?>
<?php if (isset($stepRightButton)): ?>
<a href="<?php echo esc_attr(str_replace('%step%', $stepCurrent - 1, $stepUrl)); ?>" class="ti-btn <?php echo esc_attr($stepRightButton['class']); ?>"><?php echo esc_attr($stepRightButton['text']); ?></a>
<?php endif; ?>
</div>
</ul>