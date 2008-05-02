<div id="page_metadata">
  <?php if($page->links_out): ?>
    <?php print_meta($page->links_out) ?>
  <?php else: ?>
    <p><em>double click to add metadata</em></p>
  <?php endif ?>
</div>
  <div id="page_body">
    <?php if ($page->body): ?>
      <?php echo do_filters('page_body',$page->body) ?>
    <?php else: ?>
      <p><em>double click to add a description</em></p>
    <?php endif ?>
  </div>
<?php if($page->links_in): ?>
  <div id="page_links_to">
    <?php foreach ($page->links_in as $link): ?>
      <?php echo $link->rel ?> of 
      <?php $from_page = page::name_from_id($link->from_id) ?>
      <?php echo getLink($from_page,"pages/show/$from_page") ?>
      <br />
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