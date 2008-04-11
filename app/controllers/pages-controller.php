<?php
class pages_controller {
  function __construct() {
    $this->page = new page(page::id_from_name($GLOBALS['ident']));
    if(is_null($this->page->name)) $this->page->name = $GLOBALS['ident'];
  }
  // views
  function index() {
    // TODO: semantic custom query goodness
    global $pages;
    $pages = $this->page->find_all(array('order by'=>'name'),false);
  }
  function show() {
    $GLOBALS['page'] = $this->page;
  }
  function edit() {
    $GLOBALS['page'] = $this->page;
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
    $triples_in_input = array();
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
        if(!triple::exists($triple->from_id,$triple->rel,$triple->to_id)) {
          // save the triple if it doesn't already exist
          $triple->save();
        }
      }
    }
    redirect('pages/show/'.$this->page->name); // whew!
  }
  function delete() {
    $this->page->delete_all();
    redirect('pages');
  }
}
?>
