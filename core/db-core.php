<?php
// FIXME: path to schema cache file should be a parameter somehow...
// it would be good to keep this file usable on its own...
// TODO: mysql? multiple database drivers?
class Database {
  function __construct($filename,$print_queries=false,$cache_schema=false) {
    $this->print_queries = $print_queries;
    // get actual db
    $this->db = new SQLiteDatabase($filename);
    // get schema information
    if(file_exists(PATH_TO_CORE.'schema-cache.yaml') && $cache_schema) {
      // load cache if it's there
      $schema_cache_exists = true;
      $this->load_schema_cache();
    } else {
      // otherwise query the database to get schema info
      $this->load_schema();
      // save loaded info in caching turned on
      if($cache_schema) $this->save_schema();
    }
    $this->tables = array_keys($this->schema);
  }
  
  /* schema caching/loading */
  
  function load_schema() {
    // get tables
    $tables_result = $this->runquery('SELECT * FROM sqlite_master')->fetchAll();
    foreach($tables_result as $table) {
      // get columns
      $columns_result = $this->runquery("PRAGMA table_info($table[name])")->fetchAll();
      $this->schema[$table['name']] = array();
      foreach($columns_result as $column) {
        array_push($this->schema[$table['name']],$column['name']);
      }
    }
  }
  function load_schema_cache() {
    $this->schema = Spyc::YAMLLoad(PATH_TO_CORE.'schema-cache.yaml');
    $this->tables = array_keys($this->schema);
  }
  function save_schema_cache() {
    file_put_contents(PATH_TO_CORE.'schema-cache.yaml',Spyc::YAMLDump($this->schema));
  }
  
  /* SQL generation & querying */
  
  // everything goes through here eventually
  function runquery($querystring) {
    if($this->print_queries) {
      echo $querystring."<br />\n";
    }
    return $this->db->query(stripslashes(trim($querystring)));
  }
  
  function select($tablename,$params='',$options='') {
    $querystring = "SELECT * FROM $tablename ".$this->where_clause($params)." ".$this->sql_options($options);
    $result = $this->runquery($querystring)->fetchAll(SQLITE_ASSOC);
    if(count($result) > 0) {
      return $result;
    } else {
      return false;
    }
  }
  // select one row
  function selectRow($tablename,$params='',$options='') {
    $querystring = "SELECT DISTINCT * FROM $tablename ".$this->where_clause($params)." ".$this->sql_options($options);
    return $this->runquery($querystring)->fetch(SQLITE_ASSOC);
  }
  // select specified columns
  function selectColumns($tablename,$columns,$options='') {
    $querystring = "SELECT ".implode(', ',$columns)." FROM $tablename ".$this->where_clause($params)." ".$this->sql_options($options);
    return $this->runquery($querystring)->fetch(SQLITE_ASSOC);
    if(count($result) > 0) {
      return $result;
    } else {
      return false;
    }
  }
  // select one cell
  function selectOne($tablename,$column,$params='',$options='') {
    $querystring = "SELECT $column FROM $tablename ".$this->where_clause($params)." ".$this->sql_options($options);
    $result = $this->runquery($querystring)->fetchSingle(SQLITE_ASSOC);
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
    $this->runquery($querystring);
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
    $this->runquery($querystring);
  }
  function delete($tablename,$params='') {
    $querystring = "DELETE FROM $tablename ".$this->where_clause($params);
    $this->runquery($querystring);
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
    $final = '';
    foreach($options as $key=>$value) {
      $final .= strtoupper($key).' '.$value.' ';
    }
    return trim($final);
  }
}
?>