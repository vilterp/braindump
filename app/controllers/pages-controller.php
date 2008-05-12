<?php
class pages_controller {
  function __construct() {
    /*
    global $runtime;
    $this->page = factory('page')->find_one_by_name($runtime['ident']);
    if(is_null($this->page->name)) {
      $this->page = new Page();
      $this->page->name = $runtime['ident'];
    }
    */
    echo BQL::query('get braindump');
  }
  
  // main views
  
  function index() {
    // TODO: semantic custom query goodness
    $GLOBALS['pages'] = $this->page->find_all(array('order by'=>'name'),false);
  }
  function show() {
    pass_var('page',$this->page);
  }
  
  // for AJAX in place edit
  
  function just_body() {
    no_layout();
    echo $this->page->body;
  }
  function save_body() {
    no_layout();
    $this->page->body = $_POST['value'];
    $this->page->save();
    echo do_filters('page_body',$this->page->body);
  }
  function just_meta() { // loaded into edit box
    no_layout();
    $this->page->print_meta();
  }
  function save_meta() {
    no_layout();
    $this->page->save_meta(page::parse_meta($_POST['value']));
    $this->page->print_meta(true);
  }
  
  function delete() {
    $this->page->delete_all();
    redirect('pages');
  }
  // delete the entire db. useful sometimes.
  function delete_everything() {
    global $db;
    $db->delete('pages');
    $db->delete('triples');
    redirect('pages');
  }
  
  function redirect() { // for the goto box
    redirect("pages/show/".$_GET['name']);
  }
}
?>
