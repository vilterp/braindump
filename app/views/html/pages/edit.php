<p><?php echo getLink('Show',"pages/show/$page->name",array('accesskey'=>'s')) ?></p>
<?php echo form_tag("pages/save/$page->name") ?>
  <input type="text" name="page_name" value="<?php echo $page->name ?>"> Page Name<br />
  <textarea name="rev_body" rows="20" cols="50"><?php echo $page->body() ?></textarea><br />
  <input type="submit" value="Save" accesskey="s"> as 
  <input type="text" name="rev_author" value="<?php echo $_COOKIE['author'] // this is really ugly ?>">
</form>
