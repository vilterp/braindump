<?php global $no_criteria; // urgh ?>
<a href="#" id="criteria_box_toggle_link" class="control" accesskey="f"><?php if($no_criteria) echo 'Filter'; else echo 'Hide'; ?></a>
<div id="criteria_form"<?php if($no_criteria) echo ' style="display: none;"'; ?>>
  <form method="get" action="">
    list pages where 
    <input type="text" name="criteria" id="criteria_input" size="20" value="<?php echo $_GET['criteria'] ?>" />
    <input type="submit" value="Update"> 
    <a href="#" class="control" id="criteria_box_clear_link"<?php if($no_criteria) echo ' style="display: none;"' ?>>clear</a>
  </form>
</div>
