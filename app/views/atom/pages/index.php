<?php foreach($pages as $page): ?>
  <entry>
    <title><?php echo $page ?></title>
    <link href="<?php echo page_url($page) ?>"/>
    <content type="xhtml" xml:lang="en">
      <div xmlns="http://www.w3.org/1999/xhtml">
        <?php echo print_metadata(Graph::get($page),true) ?>
        <?php echo do_filters('page_description',Graph::describe($page)) ?>
        
      </div>
    </content>
  </entry>
<?php endforeach ?>