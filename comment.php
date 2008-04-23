<?php
class Comment extends DatabaseObject {
  function connect() {
    $this->belongs_to('post');
  }
}
?>