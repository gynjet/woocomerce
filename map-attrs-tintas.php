<?php
if (php_sapi_name()!=='cli') exit;
$limit  = isset($argv[1])?(int)$argv[1]:120;
$offset = isset($argv[2])?(int)$argv[2]:0;

$TAX_COLOR='pa_cor'; $TAX_CLASS='pa_classificacao-da-tinta'; $TAX_VOL='pa_tamanho-de-refil';

$C = [
  'Preto'=>['/\bPreto\b/i','/\bBlack\b/i','/\bBK\b/i'],
  'Ciano'=>['/\bCiano\b/i','/\bCyan\b/i','/\bC[ií]an[o]?\b/i'],
  'Magenta'=>['/\bMagenta\b/i'],
  'Yellow'=>['/\bYellow\b/i','/\bAmarelo\b/i','/\bY\b/i'],
  'Light Ciano'=>['/\bLight\s*C[ií]an[o]?\b/i','/\bLC\b/i'],
  'Light Magenta'=>['/\bLight\s*Magenta\b/i','/\bLM\b/i'],
  'Kit 4 Cores'=>['/\bKit\s*4\s*Cores\b/i','/\bCMYK\b/i'],
];
$K = [
  'Corante'=>['/\bCorante\b/i'],
  'Pigmentada'=>['/\bPigmentad[ao]\b/i','/\bPigment\b/i'],
  'Sublimática'=>['/\bSublim[aá]tic[ao]\b/i'],
  'DTF'=>['/\bDTF\b/i'],
  'UV'=>['/\bUV\b/i'],
];
$V = [
  '70 ml'=>['/\b70\s?m?l\b/i'],
  '100 ml'=>['/\b100\s?m?l\b/i'],
  '250 ml'=>['/\b250\s?m?l\b/i'],
  '500 ml'=>['/\b500\s?m?l\b/i'],
  '1 L'=>['/\b1\s?L\b/i','/\b1000\s?m?l\b/i'],
];

function first($map,$hay){ foreach($map as $k=>$ps){ foreach($ps as $p){ if(preg_match($p,$hay)) return $k; } } return null; }
function ensure_term_id($tax,$name){ if(!$name) return 0; $e=term_exists($name,$tax); if($e&&!is_wp_error($e)) return (int)$e["term_id"]; $r=wp_insert_term($name,$tax); return is_wp_error($r)?0:(int)$r["term_id"]; }

$q = new WP_Query([
  'post_type'=>'product','post_status'=>'publish','fields'=>'ids',
  'posts_per_page'=>$limit,'offset'=>$offset,'no_found_rows'=>true,
  'tax_query'=>[['taxonomy'=>'product_cat','field'=>'slug','terms'=>['tintas','bulk-ink','fluido','fluidos-e-solucoes'],'operator'=>'IN','include_children'=>true]]
]);

foreach($q->posts as $pid){
  $t = get_the_title($pid) ?: '';
  $cor = first($C,$t); $cla = first($K,$t); $vol = first($V,$t);

  $meta = get_post_meta($pid,'_product_attributes',true); if(!is_array($meta)) $meta=[];
  $apply=function($tax,$name) use (&$meta,$pid){
    if(!$name) return;
    $id = ensure_term_id($tax,$name);
    wp_set_object_terms($pid,$id,$tax,false);
    $meta[$tax]=['name'=>$tax,'value'=>'','position'=>0,'is_visible'=>1,'is_variation'=>0,'is_taxonomy'=>1];
  };
  if($cor) $apply($TAX_COLOR,$cor);
  if($cla) $apply($TAX_CLASS,$cla);
  if($vol) $apply($TAX_VOL,$vol);
  update_post_meta($pid,'_product_attributes',$meta);
}
echo "OK tintas batch offset=$offset limit=$limit\n";
