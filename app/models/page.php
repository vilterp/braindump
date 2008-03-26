<?php
class page extends DatabaseObject {
  function __construct($id=NULL) {
    parent::__construct('pages','id',$id);
  }
  function connect() {
    $this->has_many('link','to_id');
    $this->has_many('revision','page_id');
  }
}
?>
