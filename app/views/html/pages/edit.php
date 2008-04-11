<?php if ($page->in_db): ?>
  <p>
    <?php echo getLink('Show',"pages/show/$page->name",array('accesskey'=>'s')) ?>
  </p>
<?php else: ?>
  <p class="message">
    New page
  </p>
<?php endif ?>
<?php echo form_tag("pages/save/$page->name") ?>
  <input type="text" name="page_name" value="<?php echo $GLOBALS['ident'] ?>"> Page Name<br />
  <textarea name="page_metadata" rows="20" cols="50"><?php echo $page->meta() ?></textarea>
  <textarea name="page_body" rows="20" cols="50"><?php echo $page->body ?></textarea><br />
  <input type="submit" value="Save" accesskey="s">
</form>
<small><?php echo getLink('Delete This Page','pages/delete/'.$page->name) ?></small>
