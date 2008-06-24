<?php
class page {
  
  public $name;
  public $metadata;
  public $description;
  public $backlinks;
  
  static $id_cache = array();
  
  function __construct($name=NULL) { // FIXME: is this page class useful?
    if(!is_null($name)) {
      $this->name = $name;
      $this->metadata = Graph::get($name);
      $this->description = Graph::describe($name);
      $this->backlinks = Graph::backlinks($name);
    }
  }
  
  // helpers
  function exists($page_name) {
    return page::id_from_name($page_name); // returns false if doesn't exist, id otherwise
  }
  function create_if_doesnt_exist($page_name) {
    if($id = self::exists($page_name)) {
      return $id;
    } else {
      global $db;
      $db->insert('pages',array('name'=>$page_name));
      $new_id = $db->select_one('pages','id',array('name'=>$page_name));
      self::$id_cache[$new_id] = $page_name;
      return (int) $new_id;
    }
  }
  function name_from_id($id) {
    if(array_key_exists($id,self::$id_cache)) return self::$id_cache[$id];
    $result = $GLOBALS['db']->select_one('pages','name',array('id'=>$id));
    self::$id_cache[$id] = $result;
    return $result;
  }
  function id_from_name($name) {
    if(in_array($name,self::$id_cache)) return array_search($name,self::$id_cache);
    global $db;
    $result = (int) $db->query("SELECT id FROM pages WHERE name LIKE ".$db->handle->quote($name))->fetchColumn();
    if($result == 0) {
      return false;
    } else {
      self::$id_cache[$result] = $name;
      return $result;
    }
  }
}
?>
