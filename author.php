<?php
class author extends DatabaseObject {
  function connect() {
    $this->belongs_to('post','author_id');
  }
}