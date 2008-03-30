<?php
class page extends DatabaseObject {
  function __construct($id=NULL) {
    parent::__construct('pages','id',$id);
  }
  function connect() {
    $this->has_many('revision');
    $this->links = links_from($this->name);
    $this->links_to = links_to($this->name);
  }
  // helpers...
  function body() {
    if(empty($this->revisions)) return NULL;
    return $this->revisions[count($this->revisions)-1]->body;
  }
  function meta() {
    if($page->links) {
      $final = '';
      foreach($this->links as $link) {
        $final .= "$link->rel: $link->to_page\n";
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
