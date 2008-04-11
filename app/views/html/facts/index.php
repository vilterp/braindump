<?php if ($facts): ?>
  <ul>
    <?php foreach ($facts as $fact): ?>
      <?php if (!$fact['changed_at_revision']): ?>
        <?php $to_page = page::name_from_id($fact['to_id']) ?>
        <?php $from_page = page::name_from_id($fact['from_id']) ?>
        <li>
          <?php echo getLink($to_page,"pages/show/$to_page") ?> is the 
          <?php echo $fact['rel'] ?> of 
          <?php echo getLink($from_page,"pages/show/$from_page") ?>
          <?php // TODO: a/an for multiple facts with same rel & from_page ?>
        </li>
      <?php endif ?>
    <?php endforeach ?>
  </ul>
<?php else: ?>
  <p>I know nothing.</p>
<?php endif ?>
