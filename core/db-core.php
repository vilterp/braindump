<?php
// TODO: standardize/clean up
class Database {
  function __construct($filename,$print_queries=false) {
    $this->filename = $filename;
    $this->print_queries = $print_queries;
    // get actual db
    $this->db = new SQLiteDatabase($filename);
    // get list of tables
    $result = $this->runquery('SELECT * FROM sqlite_master')->fetchAll();
    $this->tables = array();
    foreach($result as $table) {
      array_push($this->tables,$table['name']);
    }
    // initialize table sub-objects
    foreach($this->tables as $table) {
      $this->$table = new DatabaseTable($this->db,$table,$print_queries);
    }
  }
  function size() {
    return round(filesize($this->filename)/1024);
  }
  // actually run the queries
  function runquery($querystring) {
    if($this->print_queries) {
      echo $querystring."<br />\n";
    }
    return $this->db->query(stripslashes(trim($querystring)));
  }
  // other way to access db w/out going through table objects...
  function select($table,$params='',$options='') {
    return $this->$table->select($params,$options);
  }
  function selectRow($table,$params='',$options='') {
    return $this->$table->selectRow($params,$options);
  }
  function selectColumns($table,$columns,$params='',$options='') {
    return $this->$table->selectColumns($columns,$params,$options);
  }
  function selectOne($table,$column,$params='',$options='') {
    return $this->$table->selectOne($column,$params,$options);
  }
  function insert($table,$data) {
    return $this->$table->insert($data);
  }
  function update($table,$data,$params) {
    return $this->$table->update($data,$params);
  }
  function delete($table,$params) {
    return $this->$table->delete($params);
  }
}
class DatabaseTable {
  function __construct($handle,$name,$print_queries=false) {
    $this->db = $handle;
    $this->name = $name;
    $this->print_queries = $print_queries;
    // get columns
    $this->columns = array();
    $columns = $this->runquery("PRAGMA table_info('$name')")->fetchAll();
    foreach($columns as $column) {
      array_unshift($this->columns,$column['name']);
    }
  }
  function runquery($querystring) {
    if($this->print_queries) {
      echo $querystring."<br />\n";
    }
    return $this->db->query(stripslashes(trim($querystring)));
  }
  function select($params='',$options) {
    $querystring = "SELECT * FROM $this->name ".where_clause($params)." ".sql_options($options);
    $result = $this->runquery($querystring)->fetchAll(SQLITE_ASSOC);
    if(count($result) > 0) {
      return $result;
    } else {
      return false;
    }
  }
  // select one row
  function selectRow($params='',$options='') {
    $querystring = "SELECT DISTINCT * FROM $this->name ".where_clause($params)." ".sql_options($options);
    return $this->runquery($querystring)->fetch(SQLITE_ASSOC);
  }
  // select specified columns
  function selectColumns($columns,$options='') {
    $querystring = "SELECT ".implode(', ',$columns)." FROM $this->name ".where_clause($params)." ".sql_options($options);
    return $this->runquery($querystring)->fetch(SQLITE_ASSOC);
    if(count($result) > 0) {
      return $result;
    } else {
      return false;
    }
  }
  // select one cell
  function selectOne($column,$params='',$options='') {
    $querystring = "SELECT $column FROM $this->name ".where_clause($params)." ".sql_options($options);
    return $this->runquery($querystring)->fetchSingle(SQLITE_ASSOC);
  }
  function insert($data) {
    $keys = array();
    $values = array();
    // split $data into $keys, $values
    foreach($data as $key=>$value) {
      array_push($keys,$key);
      if(is_string($value)) {
        array_push($values,"'".sqlite_escape_string($value)."'"); // kludgy...
      } else {
        array_push($values,$value);
      }
    }
    $querystring = "INSERT INTO $this->name (".implode(", ",$keys).") VALUES (".implode(", ",$values).")";
    $this->runquery($querystring);
  }
  function update($data,$params='') {
    $pairs = array();
    // key/value pairs to update
    foreach($data as $key=>$value) {
      if(is_string($value)) {
        array_push($pairs,"$key = '$value'");
      } else {
        array_push($pairs,"$key = $value");
      }
    }
    $querystring = "UPDATE $this->name SET ".implode(", ",$pairs)." ".where_clause($params);
    $this->runquery($querystring);
  }
  function delete($params='') {
    $querystring = "DELETE FROM $this->name ".where_clause($params);
    $this->runquery($querystring);
  }
}

// functions - Database class depends on these

function where_clause($params=NULL) {
  // generates an SQL WHERE clause from an associative array or string
  if(empty($params)) {return '';};
  if(is_string($params)) {
    // if it's a string just return 'WHERE' + that
    return "WHERE $params";
  } else {
    // otherwise, make an SQL string out of the key/value pairs
    $pairs = array();
    foreach($params as $key=>$value) {
      if(is_string($value)) {
        array_push($pairs,"$key = '$value'"); 
      } else {
        array_push($pairs,"$key = $value"); 
      }
    }
    $finished = 'WHERE ';
    return $finished.implode(" AND ",$pairs);
  }
}
function sql_options($options='') {
  if(is_string($options)) return $options;
  $final = '';
  foreach($options as $key=>$value) {
    $final .= strtoupper($key).' '.$value.' ';
  }
  return trim($final);
}
?>