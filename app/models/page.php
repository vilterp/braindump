<?php
class page extends DatabaseObject {
  function connect() {
    $this->has_many('triple','to_id','links_to');
    $this->has_many('triple','from_id','links_from');
  }
  // money functions
  function get_attribute($attribute) {
    $triple = new triple();
    $page = $triple->find_one(array('from_id'=>$this->id,'rel'=>$attribute))->to_id;
    return $this->name_from_id($page);
  }
  function get_type() {
    return $this->get_attribute('type');
  }
  function get_types_by_links_to() {
    $types = array();
    foreach($this->links_to as $link) {
      $link_page = new page(page::id_from_name($link->rel));
      if($link_page->in_db) array_push($types,$link_page->get_attribute('to type'));
    }
    return $types;
  }
  function get_attributes_for_type($type) {
    $triple = new triple();
    $pages = $triple->find(array(
      'from_id' => page::id_from_name($type),
      'rel' => 'attribute'
    ));
    $answers = array();
    foreach($pages as $triple) {
      array_push($answers,page::name_from_id($triple->to_id));
    }
    return $answers;
  }
  // helpers...
  function meta() {
    if($this->links_from) {
      $final = '';
      foreach($this->links_from as $link) {
        if(!$link->changed_at_revision) {
          $final .= $link->rel.': '.page::name_from_id($link->to_id)."\n";
        }
      }
      return $final;
    } else {
      return NULL;
    }
  }
  function exists($page_name) {
    return $GLOBALS['db']->select_row('pages',array('name'=>$page_name));
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
      return $GLOBALS['db']->select_one('pages','id',array('name'=>$name));
    }
  }
}
?>
