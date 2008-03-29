<?php
class revision extends DatabaseObject {
  function __construct($id=NULL) {
    parent::__construct('revisions','id',$id);
  }
  function connect() {
    $this->has_one('page');
  }
}
?>
