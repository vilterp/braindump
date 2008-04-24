<?php
class comment extends DatabaseObject {
  function connect() {
    $this->belongs_to('post');
  }
}
?>