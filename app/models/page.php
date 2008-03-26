<?php
class page extends DatabaseObject {
  function __construct($name=NULL) {
    parent::__construct('pages','id',page::id_from_name($name));
  }
  function connect() {
    $this->has_many('link','to_id');
    $this->has_many('revision','page_id');
  }
  // helpers...
  function id_from_name($name) {
    return $GLOBALS['db']->selectOne('id',array('name'=>$name));
  }
}
?>
