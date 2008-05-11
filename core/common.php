<?php
define('BD_VERSION','0.2');
// app paths
define('PATH_TO_CONFIG','app/config.php');
define('PATH_TO_ROUTES','app/routes.php');
define('PATH_TO_MODELS','app/models');
define('PATH_TO_VIEWS','app/views/');
define('PATH_TO_CONTROLLERS','app/controllers/');
define('PATH_TO_APP_HELPERS','app/helpers/');
// system paths
define('PATH_TO_CORE','core/');
define('PATH_TO_SCHEMA_CACHE','core/schema-cache.txt');
define('PATH_TO_QUERY_LOG','core/query-log.txt');
define('PATH_TO_HELPERS','helpers/');
define('PATH_TO_LIB','lib/');
function include_dir($directory) {
  foreach(scandir("$directory/") as $file) {
    // make sure it's not a hidden file, folder, or non-php file
    if(!strpos($file,".") == 0 && !is_dir($file) && strpos($file,'.php')) {
      require_once "$directory/$file";
    }
  }
}
function factory($classname) {return eval("return new $classname();");};
// get core
include_dir(PATH_TO_CORE);
// for timer, etc
do_hooks('absolute_beginning');
// get helpers, lib
include_dir(PATH_TO_HELPERS);
include_dir(PATH_TO_LIB);
// get config
include PATH_TO_CONFIG;
// set error reporting (defined in config)
error_reporting($config['error_reporting']);
// connect to database
if(!empty($config['database']['path'])) {
  $db = new Database(
    $config['database']['path'],
    $config['database']['log_queries'],
    $config['database']['cache_schema']
  );
}
// load application
include_dir(PATH_TO_MODELS);
include_dir(PATH_TO_APP_HELPERS);
?>