<?php if ($facts): ?>
  <ul>
    <?php foreach ($facts as $fact): ?>
      <li>
        <?php echo $fact->object->getLink() ?> is the 
        <?php echo $fact->predicate->getLink() ?> of 
        <?php echo $fact->subject->getLink() ?>
      </li>
    <?php endforeach ?>
  </ul>
<?php else: ?>
  <p>I know nothing.</p>
<?php endif ?>