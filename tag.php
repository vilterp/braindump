<?php
class tag extends DatabaseObject {
  function connect() {
    $this->has_one('post');
    $this->has_one('tag');
    $this->has_many_through('post','posts_tags','tag_id','post_id');
  }
}
?>