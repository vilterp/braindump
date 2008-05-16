<?php
interface DatabaseDriver {
  public function connect($info);
  public function query($query);
  public function get_tables();
  public function get_columns($table);
}
?>