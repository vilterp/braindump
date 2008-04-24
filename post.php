<?php
class post extends DatabaseObject {
  function connect() {
    $this->has_one('author');
    //$this->has_many('comment');
  }
}
?>