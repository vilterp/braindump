<?php
define('BD_VERSION','0.2');
// app paths
define('PATH_TO_CONFIG','app/config.yaml');
define('PATH_TO_ROUTES','app/routes.php');
define('PATH_TO_MODELS','app/models');
define('PATH_TO_VIEWS','app/views/');
define('PATH_TO_CONTROLLERS','app/controllers/');
define('PATH_TO_APP_HELPERS','app/helpers/');
// system paths
define('ROOT',realpath(dirname(__FILE__)."/../").'/');
define('PATH_TO_CORE','core/');
define('PATH_TO_SCHEMA_CACHE','core/db/logs/schema-cache.txt');
define('PATH_TO_QUERY_LOG','core/db/logs/query-log.txt');
define('PATH_TO_HELPERS','helpers/');
define('PATH_TO_LIB','lib/');
include 'core/utils.php';
// get core
include_dir(PATH_TO_CORE);
// for timer, etc
do_hooks('absolute_beginning');
// get helpers, lib
include_dir(PATH_TO_HELPERS);
include_dir(PATH_TO_LIB);
// get config
$config = Spyc::YAMLLoad(PATH_TO_CONFIG);
// set error reporting (defined in config)
error_reporting($config['error_reporting']);
// connect to database
if(!empty($config['database'])) {
  $db = new Database(
    $config['database']['driver'],
    $config['database']['info'],
    $config['database']['log_queries'],
    $config['database']['cache_schema']
  );
}
debug_dump($db->query('create table pages (id numeric, name text)'));
debug_dump($db->query('create table triples (subject_id numeric, predicate_id numeric, object_id numeric)'));
// load application
include_dir(PATH_TO_MODELS);
include_dir(PATH_TO_APP_HELPERS);
?>