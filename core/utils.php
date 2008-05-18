<?php
function include_dir($directory) {
  foreach(scandir("$directory/") as $file) {
    // make sure it's not a hidden file, folder, or non-php file
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
  fwrite($log,$message."\n");
  fclose($log);
}
?>