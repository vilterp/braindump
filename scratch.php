<?php
include 'core/common.php';

$input = "creator: pedro";

$page = new Page(1);

debug_dump($page->save_meta(page::parse_meta($input)));

?>