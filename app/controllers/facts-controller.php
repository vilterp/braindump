<?php
class facts_controller {
  function index() {
    $GLOBALS['facts'] = factory('triple')->find_all();
  }
}
?>