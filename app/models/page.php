<?php
class page extends DatabaseObject {
  function connect() {
    $this->has_many('triple','subject_id','links_out');
    $this->has_many('triple','object_id','links_in');
  }
  function __toString() {
    return $this->name;
  }
  function getLink() { // => <a href='pages/show/[name]'>[name]</a>
    return getLink($this->name,"pages/show/$this->name");
  }
  static function link($name) {
    return getLink($name,"pages/show/$name");
  }
  // helpers
  function exists($page_name) {
    $id = $GLOBALS['db']->select_one('pages','id',array('name'=>$page_name));
    if($id) return (int) $id; else return false;
  }
  function create_if_doesnt_exist($page_name) {
    if($id = page::exists($page_name)) {
      return $id;
    } else {
      $new_page = new Page();
      $new_page->name = $page_name;
      $new_page->save();
      return (int) $new_page->id;
    }
  }
  function name_from_id($id) {
    if(empty($id)) return NULL;
    if(is_array($id)) {
      $ids = array();
      foreach($id as $page) {
        array_push($ids,page::name_from_id($page));
      }
      return $ids;
    }
    return $GLOBALS['db']->select_one('pages','name',array('id'=>$id));    
  }
  function id_from_name($name) {
    if(empty($name)) return NULL;
    if(is_array($name)) {
      $names = array();
      foreach($name as $page) {
        array_push($names,page::id_from_name($page));
      }
      return $names;
    } else {
      return (int) $GLOBALS['db']->select_one('pages','id',array('name'=>$name));
    }
  }
}
?>
