<?php // this code is butt ugly, but it works...

// get the juicy part of the url
// ex: /index.php/class/function/id/ => array('class','function','id')
function parse_request($input_url) {
  global $format, $config;
  $url = array();
  $start = split("\?",$input_url);
  if($config['clean_urls']) {
    $lastitem = array_reverse(split('/',$config['base_url']));
    $urlsplit = split($lastitem[1],$start[0]);
  } else {
    $urlsplit = split("index.php",$start[0]);
  }
  if(count($urlsplit) == 2) { // if there is an 'index.php' in the url
    $urlsplit2 = split("/",$urlsplit[1]);
    foreach($urlsplit2 as $item) {
      if(!empty($item) || $item == "0") {
        array_push($url,urldecode($item));
      }
    }
  } else { // root of app
    $url = array();
  }
  return $url;
}
?>