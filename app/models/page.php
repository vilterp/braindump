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
  // saving helpers
  function save_meta($input) {
    BQL::query("unset '$this'");
    foreach($input as $item) {
      BQL::query("set '$item[key]' of '$this' to '$item[value]'");
    }
  }
  // helpers...
  function print_meta($withlinks=false) {
    if($this->links_out) {
      foreach($this->links_out as $link) {
        if($withlinks) {
          echo "$link->predicate: ".$link->object->getLink()."<br />\n";
        } else {
          echo "$link->predicate: $link->object\n";
        }
      }
    }
  }
  function parse_meta($input) {
    $pairs = array();
    foreach(explode("\n",$input) as $line) {
      if(!empty($line)) {
        $pair = explode(':',$line);
        array_push($pairs,
          array(
            'key' => trim($pair[0]),
            'value' => trim($pair[1])
          )
        );
      }
    }
    return $pairs;
  }
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
