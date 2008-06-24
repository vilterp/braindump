<?php
$runtime['controller'] = 'pages';
$runtime['action'] = $runtime['url'][0];
$runtime['ident'] = $runtime['url'][1];
// maps to /action/ident (controller is always pages)

if($runtime['action'] == 'special') {
  $runtime['ident'] = unhyphenate($runtime['ident']);
  if(count($runtime['url']) == 3) { // for sub-special pages
    // special/[plugin]/[sub_special_page]
    $runtime['sub_special_page'] = unhyphenate($runtime['url'][2]);
  }
}

$defaults['controller'] = 'pages'; // FIXME: unecessary...
$defaults['format'] = 'html';
?>