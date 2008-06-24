<rdf:Description>
   <rdf:label><?php echo $page->name ?></rdf:label>
   <rdf:about><?php echo page_url($page->name) ?></rdf:about>
   <rdf:resource><?php echo page_url($page->name,'rdf') ?></rdf:resource>
  <?php if($page->metadata): ?>
    <?php foreach($page->metadata as $attr => $value): ?>
      <?php if(is_string($value)): ?>
        <?php $tag = "braindump:$attr" ?>
        <<?php echo $tag ?>><?php echo $value ?></<?php echo $tag ?>>
      <?php else: ?>
        <?php $tag = 'braindump:'.singularize($attr) ?>
        <<?php echo $tag ?>>
          <rdf:bag>
            <?php foreach($value as $item): ?>
              <rdf:li><?php echo $item ?></rdf:li>
            <?php endforeach ?>
          </rdf:bag>
        </<?php echo $tag ?>>
      <?php endif ?>
    <?php endforeach ?>
  <?php endif ?>
  <dc:description>
    <div xmlns="http://www.w3.org/1999/xhtml">
      <?php echo do_filters('page_description',$page->description) ?>
    </div>
  </dc:description>
</rdf:Description>
