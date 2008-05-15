<?php
interface DatabaseDriver {
  function connect($info);
  function query($query);
  function get_tables();
  function get_columns($table);
}
?>