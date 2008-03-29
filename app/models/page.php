<?php
class page extends DatabaseObject {
  function __construct($id=NULL) {
    parent::__construct('pages','id',$id);
  }
  function connect() {
    $this->has_many('link','to_id');
    $this->has_many('revision');
  }
  // helpers...
  function body() {
    if(empty($this->revisions)) return NULL;
    return $this->revisions[count($this->revisions)-1]->body;
  }
  function exists($page_name) {
    return $GLOBALS['db']->pages->selectRow(array('name'=>$page_name));
  }
  function id_from_name($name) {
    if(empty($name)) return NULL;
    return $GLOBALS['db']->pages->selectOne('id',array('name'=>$name));
  }
}
?>
