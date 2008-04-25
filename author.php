<?php
class author extends DatabaseObject {
  function connect() {
    $this->has_one('post','author_id');
  }
}