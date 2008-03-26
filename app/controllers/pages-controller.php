<?php
class pages_controller {
  function __construct() {
    $this->page = new page($_GLOBALS['ident']);
  }
  function all() {
    global $pages;
    $pages = $this->find_all(array('order by')=>$name);
  }
}
?>