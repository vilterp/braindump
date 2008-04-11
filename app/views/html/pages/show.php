<p id="page_controls">
  <?php echo getLink('Edit',"pages/edit/$page->name",array('accesskey'=>'e')) ?>
</p>
<?php if($page->links_from): ?>
  <div id="page_metadata">
    <?php foreach($page->links_from as $link): ?>
      <?php if (!$link->changed_at_revision): ?>
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
    <?php if ($page->body): ?>
      <?php echo do_filters('page_body',$page->body) ?>
    <?php else: ?>
      <p><em>double click to add a description</em></p>
    <?php endif ?>
  </div>
<?php endif ?>
<?php if($page->links_to): ?>
  <div id="page_links_to">
    <?php foreach ($page->links_to as $link): ?>
      <?php if (!$link->changed_at_revision): ?>
        <?php echo $link->rel ?> of 
        <?php $from_page = page::name_from_id($link->from_id) ?>
        <?php echo getLink($from_page,"pages/show/$from_page") ?>
        <br />
      <?php endif ?>
    <?php endforeach ?>
  </div>
  <div id="types">
    <ul>
      <?php $types = $page->get_types_by_links_to() ?>
      <?php foreach($types as $type): ?>
        <li><?php echo $type ?>
          <?php $attributes = page::get_attributes_for_type($type) ?>
          <ul>
            <?php foreach ($attributes as $attribute): ?>
              <li><?php echo $attribute ?></li>
            <?php endforeach ?>
          </ul>
        </li>
      <?php endforeach ?>
    </ul>
  </div>
<?php endif ?>