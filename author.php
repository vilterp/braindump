<?php
class author extends DatabaseObject {
  function connect() {
    $this->has_many('post');
  }
}