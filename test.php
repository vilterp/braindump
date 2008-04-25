<?php
$post = new post(1);
?>
<h3>Comments</h3>
<ul>
  <?php foreach ($post->comments as $comment): ?>
    <li>
      <strong><?php echo $comment->name ?></strong> says:
      <p><?php echo $comment->body ?></p>
    </li>
  <?php endforeach ?>
</ul>
<h3>Tags</h3>
<ul>
  <?php foreach ($post->tags as $tag): ?>
    <li><?php echo $tag->name ?></li>
  <?php endforeach ?>
</ul>
<?php $post->delete_all() ?>