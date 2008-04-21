<?php
// take $runtime['url'], define $class, $runtime['action'], $runtime['ident'] by end of file

$runtime['controller'] = $runtime['url'][0];
$runtime['action'] = $runtime['url'][1];
$runtime['ident'] = $runtime['url'][2];
// default: maps to index.php/class/action/ident

$defaults['controller'] = 'pages';
$defaults['format'] = 'html';
?>