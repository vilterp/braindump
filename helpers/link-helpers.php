<?php
function getLink($url,$text) {
  return "<a href='".getURL($url)."'>$text</a>";
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
