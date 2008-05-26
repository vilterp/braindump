<?php // FIXME: this should be a special page. ?>
<?php if ($facts): ?>
  <ul>
    <?php foreach ($facts as $fact): ?>
      <li>
        <?php // FIXME: too verbose... ?>
        <?php echo pagelink(page::name_from_id($fact['object_id'])) ?> is the 
        <?php echo pagelink(page::name_from_id($fact['predicate_id'])) ?> of 
        <?php echo pagelink(page::name_from_id($fact['subject_id'])) ?>
      </li>
    <?php endforeach ?>
  </ul>
<?php else: ?>
  <p>I know nothing.</p>
<?php endif ?>