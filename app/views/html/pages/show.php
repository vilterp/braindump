<p id="page_controls">
  <?php echo getLink('Edit',"pages/edit/$page->name",array('accesskey'=>'e')) ?> | 
  <?php echo getLink('Revisions',"revisions/list/$page->name") ?>
</p>
<?php //var_dump($page->links_from) ?>
<?php if($page->links_from): ?>
  <div id="page_metadata">
    <?php foreach($page->links_from as $link): ?>
      <?php if (!$link->changed_since): ?>
        <span class="link_rel"><?php echo $link->rel ?>:</span> 
        <?php $page_name = page::name_from_id($link->to_id) ?>
        <span class="link_value"><?php echo getLink($page_name,"pages/show/$page_name") ?></span>
        <br />
      <?php endif ?>
    <?php endforeach ?>
  </div>
<?php endif ?>
<?php if($page->in_db): ?>
  <div id="page_body">
    <?php echo do_filters('page_body',$page->body()) ?>
  </div>
<?php endif ?>
<?php if($page->links_to): ?>
  <div id="page_links_to">
    <?php foreach ($page->links_to as $link): ?>
      <?php echo $link->rel ?> of 
      <?php $from_page = page::name_from_id($link->from_id) ?>
      <?php echo getLink($from_page,"pages/show/$from_page") ?>
      <br />
    <?php endforeach ?>
  </div>
<?php endif ?>