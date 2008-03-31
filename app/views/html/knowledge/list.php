<ul>
  <?php foreach ($facts as $fact): ?>
    <?php $to_page = page::name_from_id($fact['to_id']) ?>
    <?php $from_page = page::name_from_id($fact['from_id']) ?>
    <li>
      <?php echo getLink($to_page,"pages/show/$to_page") ?> is the 
      <?php echo $fact['rel'] ?> of 
      <?php echo getLink($from_page,"pages/show/$from_page") ?>
    </li>
  <?php endforeach ?>
</ul>
