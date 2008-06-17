<?php foreach($pages as $page): ?>
  <entry>
    <title><?php echo $page ?></title>
    <link><?php echo getURL($page) ?></link>
    <summary><?php echo do_filters('page_description',BQL::describe($page)) ?></summary>
  </entry>
<?php endforeach ?>