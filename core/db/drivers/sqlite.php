<?php
// TODO: regiser for REXEXP?
// FIXME: don't know why 'implements DatabaseDriver' screws it up...
class SQLite_Driver {
  function connect($info) {
    // using PDO cuz it does SQLite 3
    $this->handle = new PDO("sqlite:".ROOT.$info['path']);
  }
  function query($query,$fetch_mode) {
    $result = $this->handle->query($query);
    if($result) {
      if($fetch_mode == 'fetch') {
        return $result->fetch(PDO::FETCH_ASSOC);
      } elseif($fetch_mode == 'all') {
        return $result->fetchAll(PDO::FETCH_ASSOC);
      } elseif($fetch_mode == 'column') {
        return $result->fetchColumn(PDO::FETCH_ASSOC);
      } elseif($fetch_mode == 'object') {
        return $result->fetchObject(PDO::FETCH_ASSOC);
      } 
    }
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