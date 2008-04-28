<?php
class tag extends DatabaseObject {
  function connect() {
    $this->belongs_to_many('post');
  }
}
?>