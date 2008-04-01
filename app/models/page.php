<?php
class page extends DatabaseObject {
  function __construct($id=NULL) {
    parent::__construct('pages','id',$id);
  }
  function connect() {
    $this->has_many('revision');
    $this->has_many('link','to_id','links_to');
    $this->has_many('link','from_id','links_from');
  }
  // helpers...
  function body() {
    if(empty($this->revisions)) return NULL;
    return $this->revisions[count($this->revisions)-1]->body;
  }
  function meta() {
    if($this->links_from) {
      $final = '';
      foreach($this->links_from as $link) {
        if(!$link->changed_in_revision) {
          $final .= $link->rel.': '.page::name_from_id($link->to_id)."\n";
        }
      }
      return $final;
    } else {
      return NULL;
    }
  }
  function exists($page_name) {
    return $GLOBALS['db']->pages->selectRow(array('name'=>$page_name));
  }
  function name_from_id($id) {
    if(empty($id)) return NULL;
    return $GLOBALS['db']->pages->selectOne('name',array('id'=>$id));    
  }
  function id_from_name($name) {
    if(empty($name)) return NULL;
    return $GLOBALS['db']->pages->selectOne('id',array('name'=>$name));
  }
}
?>
