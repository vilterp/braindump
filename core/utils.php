<?php
// FIXME: should this be in helpers/ ?
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
?>