<?php
class pages_controller {
  function __construct() {
    global $runtime;
    $this->page = new page(page::id_from_name($runtime['ident']));
    if(is_null($this->page->name)) $this->page->name = $runtime['ident'];
  }
  
  // main views
  
  function index() {
    // TODO: semantic custom query goodness
    $GLOBALS['pages'] = $this->page->find_all(array('order by'=>'name'),false);
  }
  function show() {
    set_var('page',$this->page);
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
  function just_meta() {
    no_layout();
    echo $this->page->meta();
  }
  function save_meta() {
    no_layout();
    // $GLOBALS['db']->print_queries = true;
    $this->page->save_meta(parse_meta($_POST['value']),$this->page->id);
    print_meta($this->page->links_from);
  }
  
  // for search bar
  
  function redirect() { // for the goto box
    redirect("pages/show/".$_GET['name']);
  }
  
  function delete() {
    $this->page->delete_all();
    redirect('pages');
  }
  function delete_everything() {
    global $db;
    $db->delete('pages');
    $db->delete('triples');
    redirect('pages');
  }
}
?>
