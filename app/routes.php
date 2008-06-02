<?php
$runtime['controller'] = 'pages';
$runtime['action'] = $runtime['url'][0];
$runtime['ident'] = $runtime['url'][1];
// maps to /action/ident
// controller always pages

$defaults['controller'] = 'pages'; // FIXME: unecessary...
$defaults['format'] = 'html';
?>