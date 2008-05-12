<?php
// FIXME: path to schema cache file should be a parameter somehow...
// it would be good to keep this file usable on its own...
// TODO: mysql? multiple database drivers?
// FIXME: use ternary (?) operator for return values
class Database {
  function __construct($filename,$log_queries=false,$cache_schema=false) {
    $this->log_queries= $log_queries;
    if($this->log_queries) 
      $this->write_to_log("\n".$_SERVER['REQUEST_METHOD'].' '.$_SERVER['REQUEST_URI']."\n");
    // get actual db
    $this->db = new SQLiteDatabase($filename);
    // get schema information
    if(file_exists(PATH_TO_SCHEMA_CACHE) && $cache_schema) {
      // load cache if it's there
      $schema_cache_exists = true;
      $this->load_schema_cache();
    } else {
      // otherwise query the database to get schema info
      $this->load_schema();
      // save loaded info in caching turned on
      if($cache_schema) $this->save_schema_cache();
    }
    $this->tables = array_keys($this->schema);
  }
  
  /* schema caching/loading */
  
  function load_schema() {
    // get tables
    $tables_result = $this->run_query('SELECT * FROM sqlite_master')->fetchAll();
    foreach($tables_result as $table) {
      // get columns
      $columns_result = $this->run_query("PRAGMA table_info($table[name])")->fetchAll();
      $this->schema[$table['name']] = array();
      foreach($columns_result as $column) {
        array_push($this->schema[$table['name']],$column['name']);
      }
    }
  }
  function load_schema_cache() {
    $this->schema = (array) unserialize(file_get_contents(PATH_TO_SCHEMA_CACHE));
    $this->tables = array_keys($this->schema);
  }
  function save_schema_cache() {
    file_put_contents(PATH_TO_SCHEMA_CACHE,serialize($this->schema));
  }
  
  function get_high_key($tablename,$column='id') {
    $highkey = $GLOBALS['db']->select_one($tablename,$column,'',array('order by'=>"$column DESC"));
    if(!$highkey) $highkey = 0;
    return $highkey;
  }
  
  function write_to_log($query) {
    $log = fopen(PATH_TO_QUERY_LOG,'a');
    fwrite($log,$query."\n");
    fclose($log);
  }
  
  /* SQL generation & querying */
  
  // everything goes through here eventually
  function run_query($querystring) {
    if($this->log_queries) {
      $this->write_to_log($querystring);
    }
    return $this->db->query(stripslashes(trim($querystring)));
  }
  
  function select($tablename,$params='',$options='') {
    $querystring = "SELECT * FROM $tablename ".$this->where_clause($params)." ".$this->sql_options($options);
    $result = $this->run_query($querystring)->fetchAll(SQLITE_ASSOC);
    if(count($result) > 0) {
      return $result;
    } else {
      return false;
    }
  }
  // select one row
  function select_row($tablename,$params='',$options='') {
    $querystring = "SELECT DISTINCT * FROM $tablename ".$this->where_clause($params)." ".$this->sql_options($options);
    return $this->run_query($querystring)->fetch(SQLITE_ASSOC);
  }
  // select specified columns
  function select_column($tablename,$column,$params,$options='') {
    $querystring = "SELECT $column FROM $tablename ".$this->where_clause($params)." ".$this->sql_options($options);
    return $this->run_query($querystring)->fetch(SQLITE_ASSOC);
    if(count($result) > 0) {
      return $result;
    } else {
      return false;
    }
  }
  // select one cell
  function select_one($tablename,$column,$params='',$options='') {
    $querystring = "SELECT $column FROM $tablename ".$this->where_clause($params)." ".$this->sql_options($options);
    $result = $this->run_query($querystring)->fetchSingle(SQLITE_ASSOC);
    if(!$result) {
      return NULL;
    } else {
      return $result;
    }
  }
  function insert($tablename,$data) {
    $keys = array();
    $values = array();
    // split $data into $keys, $values
    foreach($data as $key=>$value) {
      array_push($keys,$key);
      if(is_string($value)) {
        array_push($values,"'".sqlite_escape_string($value)."'"); // a little kludgy...
      } elseif(is_null($value)) {
        array_push($values,'NULL');
      } else {
        array_push($values,$value);
      }
    }
    $querystring = "INSERT INTO $tablename (".implode(", ",$keys).") VALUES (".implode(", ",$values).")";
    $this->run_query($querystring);
  }
  function update($tablename,$data,$params='') {
    if(is_string($data)) {
      $the_data = $data;
    } else {
      $pairs = array();
      // key/value pairs to update
      foreach($data as $key=>$value) {
        if(is_string($value)) {
          array_push($pairs,"$key = '".sqlite_escape_string($value)."'");
        } elseif(is_null($value)) {
          array_push($paris,"$key = NULL");
        } else {
          array_push($pairs,"$key = $value");
        }
      }
      $the_data = implode(', ',$pairs);
    }
    $querystring = "UPDATE $tablename SET $the_data ".$this->where_clause($params);
    $this->run_query($querystring);
  }
  function delete($tablename,$params='') {
    $querystring = "DELETE FROM $tablename ".$this->where_clause($params);
    $this->run_query($querystring);
  }
  
  /* utility functions */
  
  // generates an SQL WHERE clause from an associative array or string
  function where_clause($params=NULL) {
    if(empty($params)) return '';
    if(is_string($params)) {
      return "WHERE $params";
    } else {
      // if it's an array, make an SQL string out of the key/value pairs
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
    if(is_null($options)) return '';
    $final = '';
    foreach($options as $key=>$value) {
      $final .= strtoupper($key).' '.$value.' ';
    }
    return trim($final);
  }
}
?>
