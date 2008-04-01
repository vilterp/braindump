<?php
class revision extends DatabaseObject {
  function __construct($id=NULL) {
    parent::__construct('revisions','id',$id);
  }
  function connect() {
    $this->has_one('page');
    $this->has_many('link','as_of_revision','links_set');
    $this->has_many('link','changed_in_revision','links_changed');
  }
}
?>
