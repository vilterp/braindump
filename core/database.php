<?php
// FIXME: use ternary (?) operator for return values
// FIXME: split query generation code into another file?
class Database {  
  
  protected $handle;
  
  function __construct($connection_info) {
    // connect
    $this->handle = new PDO('sqlite:'.ROOT.$connection_info);
    $this->handle->sqliteCreateFunction('regexp','preg_match');
  }
  
  function get_high_key($tablename,$column='id') {
    $highkey = $GLOBALS['db']->select_one($tablename,$column,'',array('order by'=>"$column DESC"));
    if(!$highkey) $highkey = 0;
    return $highkey;
  }
  
  /* SQL generation & querying */
  
  // everything goes through here eventually
  function query($querystring) {
    // log the query
    global $config;
    if($config['keep_log']) {
      write_to_log($querystring);
    }
    // run the query
    $result = $this->handle->query(stripslashes(trim($querystring)));
    if($result) $result->setFetchMode(PDO::FETCH_ASSOC);
    return $result;
  }
  
  function select($tablename,$params='',$options='') {
    $querystring = "SELECT * FROM $tablename ".$this->where_clause($params)." ".$this->sql_options($options);
    $result = $this->query($querystring)->fetchAll();
    if(count($result) > 0) {
      return $result;
    } else {
      return false;
    }
  }
  // select one row
  function select_row($tablename,$params='',$options='') {
    $querystring = "SELECT DISTINCT * FROM $tablename ".$this->where_clause($params)." ".$this->sql_options($options);
    return $this->query($querystring)->fetch();
  }
  // select specified columns
  function select_column($tablename,$column,$params,$options='') {
    $querystring = "SELECT $column FROM $tablename ".$this->where_clause($params)." ".$this->sql_options($options);
    $result = $this->query($querystring)->fetchAll();
    if(count($result) > 0) {
      $rows = array();
      foreach($result as $row) {
        $rows[] = $row[$column];
      }
      return $rows;
    } else {
      return false;
    }
  }
  // select one cell
  function select_one($tablename,$column,$params='',$options='') {
    $querystring = "SELECT $column FROM $tablename ".$this->where_clause($params)." ".$this->sql_options($options);
    $result = $this->query($querystring)->fetch();
    if(!$result) {
      return NULL;
    } else {
      return $result[$column];
    }
  }
  function insert($tablename,$data) {
    $keys = array();
    $values = array();
    // split $data into $keys, $values
    foreach($data as $key=>$value) {
      $keys[] = $key;
      if(is_int($value)) {
        $values[] = $value;
      } else {
        $values[] = $this->handle->quote($value);
      }
    }
    $querystring = "INSERT INTO $tablename (".implode(", ",$keys).") VALUES (".implode(", ",$values).")";
    $this->query($querystring);
  }
  function update($tablename,$data,$params='') {
    if(is_string($data)) {
      $the_data = $data;
    } else {
      $pairs = array();
      // key/value pairs to update
      foreach($data as $key=>$value) {
        if(is_int($value)) {
          $pairs[] = "$key = $value";
        } else {
          $pairs[] = "$key = ".$this->handle->quote($value);
        }
      }
      $the_data = implode(', ',$pairs);
    }
    $querystring = "UPDATE $tablename SET $the_data ".$this->where_clause($params);
    $this->query($querystring);
  }
  function delete($tablename,$params='') {
    $querystring = "DELETE FROM $tablename ".$this->where_clause($params);
    $this->query($querystring);
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
        if(is_int($value)) {
          $pairs[] = "$key = $value";
        } else {
          $pairs[] = "$key = ".$this->handle->quote($value);
        }
      }
      return 'WHERE '.implode(" AND ",$pairs);
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
