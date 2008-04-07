<?php
class Database {
  function __construct($filename,$print_queries=false,$cache_schema) {
    $this->filename = $filename;
    $this->print_queries = $print_queries;
    // get actual db
    $this->db = new SQLiteDatabase($filename);
    // get list of tables
    $this->tables = array();
    if(file_exists(PATH_TO_CORE.'schema-cache.xml') && $cache_schema) {
      $schema_cache_exists = true;
      $this->load_schema_cache(); // load cache if it's there
    } else { // otherwise query the database to get schema info
      $result = $this->runquery('SELECT * FROM sqlite_master')->fetchAll();
      foreach($result as $table) {
        array_push($this->tables,$table['name']);
      }
    }
    // initialize table sub-objects
    foreach($this->tables as $table) {
      $this->$table = new DatabaseTable($this->db,$table,$print_queries,$this->cache);
    }
    // save schema
    if(!$schema_cache_exists && $cache_schema) {
      $this->save_schema_cache();
    }
  }
  function load_schema_cache() {
    $this->cache = simplexml_load_file(PATH_TO_CORE.'schema-cache.xml');
    foreach($this->cache->children() as $table) {
      array_push($this->tables,$table['name']);
    }
  }
  function save_schema_cache() {
    $cache = new SimpleXMLElement("<database></database>");
    foreach($this->tables as $table) {
      $table_element = $cache->addChild('table');
      $table_element['name'] = $table;
      foreach($this->$table->columns as $column) {
        $column_element = $table_element->addChild('column');
        $column_element['name'] = $column;
      }
    }
    file_put_contents(PATH_TO_CORE.'schema-cache.xml',$cache->asXML());
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
  function __construct($handle,$name,$print_queries=false,$cache) {
    $this->db = $handle;
    $this->name = $name;
    $this->print_queries = $print_queries;
    // get columns
    $this->columns = array();
    if(!$cache) { // query database for column info
      $columns = $this->runquery("PRAGMA table_info('$name')")->fetchAll();
      foreach($columns as $column) {
        array_unshift($this->columns,$column['name']);
      }
    } else { // use cache for columns (too many loops!)
      foreach($cache->children() as $table) {
        if($table['name'] == $name) {
          foreach($table->children() as $column) {
            array_push($this->columns,$column['name']);
          }
        }
      }
    }
  }
  function runquery($querystring) {
    if($this->print_queries) {
      echo $querystring."<br />\n";
    }
    return $this->db->query(stripslashes(trim($querystring)));
  }
  function select($params='',$options='') {
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
    $result = $this->runquery($querystring)->fetchSingle(SQLITE_ASSOC);
    if(!$result) {
      return NULL;
    } else {
      return $result;
    }
  }
  function insert($data) {
    $keys = array();
    $values = array();
    // split $data into $keys, $values
    foreach($data as $key=>$value) {
      array_push($keys,$key);
      if(is_string($value)) {
        array_push($values,"'".sqlite_escape_string($value)."'"); // kludgy...
      } elseif(is_null($value)) {
        array_push($values,'NULL');
      } else {
        array_push($values,$value);
      }
    }
    $querystring = "INSERT INTO $this->name (".implode(", ",$keys).") VALUES (".implode(", ",$values).")";
    $this->runquery($querystring);
  }
  function update($data,$params='') {
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
    $querystring = "UPDATE $this->name SET $the_data ".where_clause($params);
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
  if(empty($params)) return '';
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