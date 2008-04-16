<?php
class pages_controller {
  function __construct() {
    $this->page = new page(page::id_from_name($GLOBALS['ident']));
    if(is_null($this->page->name)) $this->page->name = $GLOBALS['ident'];
  }
  // views
  function index() {
    // TODO: semantic custom query goodness
    $GLOBALS['pages'] = $this->page->find_all(array('order by'=>'name'),false);
  }
  function show() {
    set_var('page',$this->page);
  }
  function just_body() {
    no_layout();
    echo $this->page->body;
  }
  function edit() {
    set_var('page',$this->page);
  }
  function redirect() { // for the goto box
    $name = $_GET['name'];
    page::exists($name) ? redirect("pages/show/$name") : redirect("pages/edit/$name");
  }
  // action
  function save() {
    $GLOBALS['db']->print_queries = true;
    // save page
    $this->page->name = $_POST['page_name'];
    $this->page->body = $_POST['page_body'];
    $this->page->save();
    // save triples
    // TODO: abstract in triple API helper...
    $metadata = explode("\n",$_POST['page_metadata']);
    $existing_triples = array();
    foreach($metadata as $item) { // go through links
      if(!empty($item)) {
        $triple = new triple();
        $triple->from_id = $this->page->id;
        $split = explode(':',$item);
        if(!page::exists(trim($split[1]))) { // if the to page doesn't exist, make it
          $to_page = new page();
          $to_page->name = trim($split[1]);
          $to_page->save();
          $to_id = $to_page->id;
        } else { // otherwise, get its id
          $to_id = page::id_from_name(trim($split[1])); 
        }
        $triple->to_id = $to_id;
        $triple->rel = trim($split[0]);
        if($existing_triple = 
          triple::exists($triple->from_id,$triple->rel,$triple->to_id)) {
          array_push($existing_triples,$existing_triple);
        } else {
          // save the triple if it doesn't already exist
          $triple->save();
          array_push($existing_triples,$triple->id);
        }
      }
    }
    if($existing_triples) // delete triples not in input
      $GLOBALS['db']->delete('triples',"from_id = ".$this->page->id." AND id != ".
        implode("AND id != ",$existing_triples));
    redirect('pages/show/'.$this->page->name); // whew!
  }
  function save_body() {
    no_layout();
    $this->page->body = $_POST['value'];
    $this->page->save();
    echo do_filters('page_body',$this->page->body);
  }
  function delete() {
    $this->page->delete_all();
    redirect('pages');
  }
}
?>
