<?php
// FIXME: underscore?
function get_link($text,$url='',$options=NULL) {
  return "<a href='".get_url($url)."'".html_options($options).">$text</a>";
}
function get_url($url,$format=NULL) {
  if($GLOBALS['config']['clean_urls']) {
    $final = base_url().$url;
  } else {
    $final = base_url()."index.php/".$url;
  }
  if($format) return $final."?format=$format"; else return $final;
}
function base_url() {
  return $GLOBALS['config']['base_url'];
}
?>
