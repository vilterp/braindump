<?php
// this and the triple class are pretty much just collections of static 
// functions now...
class page {
  // helpers
  function exists($page_name) {
    $id = $GLOBALS['db']->select_one('pages','id',array('name'=>$page_name));
    if($id) return (int) $id; else return false;
  }
  function create_if_doesnt_exist($page_name) {
    if($id = self::exists($page_name)) {
      return $id;
    } else {
      global $db;
      $db->insert('pages',array('name'=>$page_name));
      return (int) $db->select_one('pages','id',array('name'=>$page_name));
    }
  }
  function name_from_id($id) {
    if(empty($id)) return NULL;
    if(is_array($id)) {
      $ids = array();
      foreach($id as $page) {
        array_push($ids,self::name_from_id($page));
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
        array_push($names,self::id_from_name($page));
      }
      return $names;
    } else {
      return (int) $GLOBALS['db']->select_one('pages','id',array('name'=>$name));
    }
  }
}
?>
