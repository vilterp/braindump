<?php foreach($pages as $page): ?>
  <entry>
    <title><?php echo $page ?></title>
    <link href="<?php echo page_url($page) ?>"/>
    <?php foreach(BQL::get($page) as $attr=>$value): ?>
      <?php $tag = 'braindump'.$attr ?>
      <?php if(is_string($value)): ?>
        <?php $tag = 'braindump:'.$attr ?>
        <<?php echo $tag ?>><?php echo $value ?></<?php echo $tag ?>>
      <?php else: ?>
        <?php foreach($value as $item): ?>
          <?php $tag = 'braindump:'.singularize($attr) ?>
          <<?php echo $tag ?>><?php echo $item ?></<?php echo $tag ?>>
        <?php endforeach ?>
      <?php endif ?>
    <?php endforeach ?>
    <content type="xhtml" xml:lang="en">
      <div xmlns="http://www.w3.org/1999/xhtml">
        <?php echo do_filters('page_description',BQL::describe($page)) ?>
        
      </div>
    </content>
  </entry>
<?php endforeach ?>