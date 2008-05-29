<?php
function include_dir($directory) {
  foreach(scandir("$directory/") as $file) {
    // make sure it's not a hidden file, a folder, or a non-php file
    if(!strpos($file,".") == 0 && !is_dir($file) && strpos($file,'.php')) {
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
  $log = fopen(PATH_TO_LOG,'a');
  if(is_string($message)) {
    fwrite($log,$message."\n");
  } else {
    fwrite($log,json_encode($message)."\n"); // just a good way to serialize objects
  }
  fclose($log);
}
function add_trailing_slash($path) {
  if(!strripos($path,'/')+1 == strlen($path)) { // if it doesn't have one
    return $path.'/'; // add it
  } else {
    return $path;
  }
}
// http://www.php.net/manual/en/ref.array.php#82161
function array_flatten($a) {
  foreach($a as $k=>$v) $a[$k]=(array)$v;
  if(count($a) > 0) {
    return call_user_func_array(array_merge,$a);
  } else {
    return $array;
  }
}
?>