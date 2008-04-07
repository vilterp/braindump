<?php
class revision extends DatabaseObject {
  function __construct($id=NULL) {
    parent::__construct('revisions','id',$id);
  }
  function connect() {
    $this->has_one('page');
    $this->has_many('triple','set_at_revision','triples_set');
    $this->has_many('triple','changed_at_revision','triples_changed');
  }
}
?>
