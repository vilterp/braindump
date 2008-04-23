<?php
function factory($classname) {eval("return new $classname()");};
include 'db-object.php';
include 'db-core.php';
include 'post.php';
include 'comment.php';

include 'test.php';
?>