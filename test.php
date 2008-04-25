<?php
$post = new post(1);
foreach($post->comments as $comment) {
  var_dump($comment);
}
?>
