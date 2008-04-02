<?php
class link extends DatabaseObject {
  function __construct($id=NULL) {
    parent::__construct('links','id',$id);
  }
  function connect() {
    $this->has_one('page','to_id','to_page');
    $this->has_one('page','from_id','from_page');
    $this->has_one('revision','as_of_revision');
  }
  // helpers
  function exists($from_id,$rel,$to_id) {
    return $GLOBALS['db']->links->selectOne('id',"from_id=$from_id AND rel='$rel' AND to_id=$to_id AND changed_in_revision = NULL");
  }
}
?>
