<?php
function dir_contents($directory) {
  $contents = array();
  foreach(scandir($directory) as $item) {
    if(strpos($item,'.') !== 0) $contents[] = $item;
  }
  return $contents;
}
function include_dir($directory) {
  foreach(dir_contents($directory) as $file) {
    // make sure it's not a hidden file, a folder, or a non-php file
    if(!is_dir($file) && strpos($file,'.php')) {
      require_once "$directory/$file";
    }
  }
}
function factory($classname) {
  return eval("return new $classname();");
}
function debug_dump($var) {
  echo "<pre>\n";
  var_dump($var);
  echo "</pre>\n";
}
function write_to_log($message) {
  // TODO: print stack trace when something goes wrong
  global $config;
  if($config['keep_log']) {
    $log = fopen(PATH_TO_LOG,'a');
    if(is_string($message)) {
      fwrite($log,$message."\n");
    } else {
      fwrite($log,json_encode($message)."\n"); // just a good way to serialize objects
    }
    fclose($log);
  }
}
function add_trailing_slash($path) {
  if(!strripos($path,'/')+1 == strlen($path)) { // if it doesn't have one
    return $path.'/'; // add it
  } else {
    return $path;
  }
}
function array_flatten($a) {
  $final = array();
  foreach($a as $key=>$item) {
    if(!empty($item)) $final[] = $item;
  }
  return $final;
}
function strip_extension($filename) {
  $split = explode('.',$filename);
  return $split[0];
}
?>