<div id="page_metadata">
  <?php if($page->links_out): ?>
    <?php foreach($page->links_out as $link): ?>
      <span class="link_rel"><?php echo $link->predicate->name ?>:</span> 
      <span class="link_value">
        <?php echo getLink($link->object->name,"pages/show/".$link->object->name) ?>
      </span>
      <br />
    <?php endforeach ?>
  <?php else: ?>
    <p class="edit_prompt">double click to add metadata</p>
  <?php endif ?>
</div>
  <div id="page_body">
    <?php if ($page->body): ?>
      <?php echo do_filters('page_body',$page->body) ?>
    <?php else: ?>
      <p class="edit_prompt">double click to add a description</p>
    <?php endif ?>
  </div>
<?php if($page->links_in): ?>
  <div id="page_links_in">
    <?php foreach ($page->links_in as $link): ?>
      <?php echo $link->predicate->name ?> of 
      <?php echo getLink($link->subject->name,"pages/show/".$link->object->name) ?>
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