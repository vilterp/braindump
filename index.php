<?php
include 'string-helpers.php';
include 'db-object.php';
include 'db-core.php';
include 'post.php';
include 'comment.php';
include 'author.php';
include 'tag.php';

define('PATH_TO_SCHEMA_CACHE','schema-cache.txt');

$db = new Database('database.sqlite',true,true);

include 'test.php';
?>