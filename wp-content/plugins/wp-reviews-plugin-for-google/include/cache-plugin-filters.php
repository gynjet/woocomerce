<?php
defined('ABSPATH') or die('No script kiddies please!');
if (!function_exists('trustindex_exclude_js')) {
function trustindex_exclude_js($list) {
$list []= 'trustindex.io';
$list []= 'https://cdn.trustindex.io/';
$list []= 'https://cdn.trustindex.io/loader.js';
$list []= 'https://cdn.trustindex.io/loader-cert.js';
$list []= 'https://cdn.trustindex.io/loader-feed.js';
return $list;
}
}
add_filter('rocket_minify_excluded_external_js', 'trustindex_exclude_js');
add_filter('rocket_exclude_js', 'trustindex_exclude_js');
add_filter('rocket_delay_js_exclusions', 'trustindex_exclude_js');
add_filter('litespeed_optimize_js_excludes', 'trustindex_exclude_js');
add_filter('sgo_javascript_combine_excluded_external_paths', 'trustindex_exclude_js');
add_filter('sgo_css_combine_exclude', function($list) {
foreach (array (
 0 => 'facebook',
 1 => 'google',
 2 => 'tripadvisor',
 3 => 'yelp',
 4 => 'booking',
 5 => 'amazon',
 6 => 'arukereso',
 7 => 'airbnb',
 8 => 'hotels',
 9 => 'opentable',
 10 => 'foursquare',
 11 => 'capterra',
 12 => 'szallashu',
 13 => 'thumbtack',
 14 => 'expedia',
 15 => 'zillow',
 16 => 'wordpressPlugin',
 17 => 'aliexpress',
 18 => 'alibaba',
 19 => 'sourceForge',
 20 => 'ebay',
) as $platform) {
$list []= 'ti-widget-css-'. $platform;
}
foreach (array (
 0 => 'instagram',
 1 => 'facebook',
 2 => 'youtube',
 3 => 'google',
 4 => 'twitter',
 5 => 'tiktok',
 6 => 'pinterest',
 7 => 'vimeo',
) as $platform) {
$list []= 'trustindex-feed-widget-css-'. $platform;
}
return $list;
});
add_filter('rocket_rucss_safelist', function($list) {
$list []= 'trustindex-(.*).css';
$list []= '.ti-widget';
return $list;
});
add_filter('script_loader_tag', function($tag) {
if (strpos($tag, 'trustindex') !== false && strpos($tag, '/loader') !== false) {
$tag = preg_replace('/ (crossorigin|integrity|consent-required|consent-by|consent-id)=[\'"][^\'"]+[\'"]/m', '', $tag);
$tag = str_replace([
'consent-original-src-_',
'consent-original-type-_',
'type="application/consent"',
], [
'src',
'type',
'',
], $tag);
}
return $tag;
}, 9999999);
add_filter('style_loader_tag', function($tag) {
if (strpos($tag, 'trustindex') !== false && (strpos($tag, '/assets/widget-presetted-css/') !== false || strpos($tag, '/ti-preview-box.css') !== false)) {
$tag = preg_replace('/ (crossorigin|integrity|consent-required|consent-by|consent-id)=[\'"][^\'"]+[\'"]/m', '', $tag);
$tag = str_replace([
'consent-original-src-_',
'consent-original-type-_',
'type="application/consent"',
], [
'src',
'type',
'',
], $tag);
}
return $tag;
}, 9999999);
$localizationFiles = get_option('litespeed.conf.optm-localize_domains');
$isJson = false;
if (is_string($localizationFiles)) {
$localizationFiles = json_decode($localizationFiles, true);
$isJson = true;
}
if ($localizationFiles && is_array($localizationFiles)) {
$isUpdated = false;
foreach ($localizationFiles as $i => $url) {
if (strpos($url, '#') !== 0 && strpos($url, '.trustindex.') !== false) {
$localizationFiles[$i] = '# '.$url;
$isUpdated = true;
}
}
if ($isUpdated) {
if ($isJson) {
$localizationFiles = json_encode($localizationFiles);
}
update_option('litespeed.conf.optm-localize_domains', $localizationFiles, true);
}
}
?>
