<?php
// FIXME: underscore?
function get_link($text,$url='',$options=NULL) {
  return "<a href='".get_url($url)."'".html_options($options).">$text</a>";
}
function get_url($url,$format=NULL) {
  if($GLOBALS['config']['clean_urls']) {
    $final = baseURL().$url;
  } else {
    $final = baseURL()."index.php/".$url;
  }
  if($format) return $final."?format=$format"; else return $final;
}
function baseURL() {
  return $GLOBALS['config']['base_url'];
}
function ipLink($ip,$options=NULL) {
  return "<a href='http://ws.arin.net/cgi-bin/whois.pl?queryinput=$ip'".html_options($options).">$ip</a>";
}
?>
