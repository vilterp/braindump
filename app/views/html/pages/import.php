<p class="notice">Find a .dump.yaml file and import its contents into this braindump.</p>

<form method="POST" action="<?php echo get_url('process_import') ?>" enctype="multipart/form-data">
  <input type="file" name="file" /> 
  <input type="submit" value="Import &raquo;" />
</form>