<?php
class page extends DatabaseObject {
  function connect() {
    $this->has_many('triple','subject_id','links_out');
    $this->has_many('triple','object_id','links_in');
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
    $existing_triples = array();
    foreach($input as $item) { // go through links
      $triple = new triple();
      $triple->subject_id = (int) $this->id;
      $triple->object_id = page::create_if_doesnt_exist($item['value']);
      $triple->predicate_id = page::create_if_doesnt_exist($item['key']);
      if($existing_triple = 
        triple::exists($triple->subject_id,$triple->predicate_id,$triple->object_id)) {
        array_push($existing_triples,$existing_triple);
      } else {
        // save the triple if it doesn't already exist
        //$triple->save();
        array_push($existing_triples,$triple->id);
      }
    }
    if($existing_triples) // delete triples not in input
      $GLOBALS['db']->delete('triples',"subject_id = ".$page_id." AND id != ".
        implode(" AND id != ",$existing_triples));
    // reload newly created links into the page object
    $this->connect();
  }
  // helpers...
  function meta() {
    if($this->links_from) {
      $final = '';
      for($i=0; $i<count($this->links_from); $i++) {
        $link = $this->links_from[$i];
        $final .= $link->rel.': '.page::name_from_id($link->to_id);
        if($i+1 < count($this->links_from)) $final .= "\n";
      }
      return $final;
    } else {
      return NULL;
    }
  }
  function exists($page_name) {
    return (int) $GLOBALS['db']->select_row('pages',array('name'=>$page_name));
  }
  function create_if_doesnt_exist($page_name) {
    if($id = page::exists($page_name)) {
      return $id;
    } else {
      $new_page = new Page();
      $new_page->name = $page_name;
      $new_page->save();
      return $new_page->id;
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