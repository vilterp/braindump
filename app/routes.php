<?php
// take $url, define $class, $action, $ident by end of file

$controller = $url[0];
$action = $url[1];
$ident = $url[2];
// default: maps to index.php/class/action/ident

$defaults['controller'] = 'pages';
$defaults['format'] = 'html';
?>