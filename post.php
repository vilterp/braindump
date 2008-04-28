<?php
class post extends DatabaseObject {
  function connect() {
    $this->belongs_to('author');
    $this->has_many('comment');
    $this->has_many_through('tag');
  }
}
?>