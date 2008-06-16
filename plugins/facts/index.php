<?php // FIXME: group multiples together ?>
<?php $facts = $GLOBALS['db']->select('triples'); ?>
<?php if ($facts): ?>
  <ul>
    <?php foreach ($facts as $fact): ?>
      <li>
        <?php // FIXME: urg too much code... ?>
        <?php echo pagelink(page::name_from_id($fact['object_id'])) ?> is the 
        <?php echo pagelink(page::name_from_id($fact['predicate_id'])) ?> of 
        <?php echo pagelink(page::name_from_id($fact['subject_id'])) ?>
      </li>
    <?php endforeach ?>
  </ul>
<?php else: ?>
  <p class="notice">I know nothing.</p>
<?php endif ?>