<?php
// get the juicy part of the url
// ex: /index.php/class/function/id/ => array('class','function','id')
function parse_request($request,$return_format=false) {
  $start = parse_url($request);
  $path = $start['path'];
  $pathinfo = pathinfo($path);
  global $runtime, $config, $defaults;
  if($return_format) {
    // use extension as format, if none revert to default
    if(isset($pathinfo['extension']) && $pathinfo['basename'] !== 'index.php') {
      $format = $pathinfo['extension'];
    } else {
      $format = $defaults['format'];
    }
    return $format;
  }
  $root = explode('/',$config['base_url']);
  $req = explode('/',$start['path']);
  $diff = array_diff($req,$root);
  // take out the 'index.php'
  if(!$config['enable_mod_rewrite']) unset($diff[array_search('index.php',$diff)]);
  if($pathinfo['basename'] == $pathinfo['filename']) $diff[] = $pathinfo['filename'];
  return array_flatten($diff);
}
?>