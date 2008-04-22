<?php
// get the juicy part of the url
// ex: /index.php/class/function/id/ => array('class','function','id')
function parse_request($input_url,$strip_extensions=true) {
  global $format;
  $url = array();
  $start = split("\?",$input_url);
  if($config['enable_mod_rewrite']) {
    $lastitem = array_reverse(split('/',$config['base_url']));
    $urlsplit = split($lastitem[1],$start[0]);
  } else {
    $urlsplit = split("index.php",$start[0]);
  }
  if(count($urlsplit) == 2) { // if there is an 'index.php' in the url
    $urlsplit2 = split("/",$urlsplit[1]);
    foreach($urlsplit2 as $item) {
      if(!empty($item) || $item == "0") {
        $text = urldecode($item);
        if($strip_extensions) {
          $dot_split = explode('.',$text);
          array_push($url,$dot_split[0]); 
        } else {
          array_push($url,$item);
        }
      }
    }
  } else { // root of app
    $url = array();
  }
  return $url;
}
// get the format from the file extension of the last item
// eg. users/bob.yaml
// very RESTful
function parse_format($url) {
  global $defaults;
  $backwards = array_reverse($url);
  $last = $backwards[0];
  $dot_split = explode('.',$last);
  $extension = $dot_split[1];
  if(is_null($extension)) {
    $format = $defaults['format'];
  } else {
    $format = $extension;
  }
  return $format;
}
?>