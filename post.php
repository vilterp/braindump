<?php
class Post extends DatabaseObject {
  function connect() {
    $this->has_many('comment');
  }
}
?>