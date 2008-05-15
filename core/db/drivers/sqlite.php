<?php
class SQLite_Driver implements DatabaseDriver {
  function connect($info) {
    $this->handle = new PDO("sqlite:$info[path]");
    return $this->handle;
  }
  function query($query) {
    return $this->handle->query($query);
  }
  function get_tables() {
    return $this->handle->query('SELECT * FROM sqlite_master')->fetch(PDO::FETCH_ASSOC);
  }
  function get_columns($table) {
    $columns = array();
    $result = $this->handle->query("PRAGMA table_info($table)")->fetch(PDO::FETCH_ASSOC);
    foreach($result as $column) {
      $columns[] = $column['name'];
    }
    return $columns;
  }
}
?>