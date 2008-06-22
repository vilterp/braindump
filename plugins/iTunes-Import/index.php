<p class="notice">Export your iTunes library information by going to File > Export. Now, find the file (Music.xml) and press import.</p>

<form method="POST" action="<?php echo get_url('special/iTunes-Import/process') ?>" enctype="multipart/form-data">
  <input type="file" name="file" /> 
  <input type="submit" value="Import &raquo;" />
</form>