<?php
// FIXME: underscore?
function getLink($text,$url='',$options='') {
  return "<a href='".getURL($url)."' ".html_options($options).">$text</a>";
}
function getURL($url) {
  if($GLOBALS['config']['enable_mod_rewrite']) {
    return baseURL().$url;
  } else {
    return baseURL()."index.php/".$url;
  }
}
function baseURL() {
  return $GLOBALS['config']['base_url'];
}
function ipLink($ip) {
  return "<a href='http://ws.arin.net/cgi-bin/whois.pl?queryinput=$ip'>$ip</a>";
}
?>
