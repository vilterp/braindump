<?php
class pages_controller {
  function __construct() {
    $this->page = new page(page::id_from_name($GLOBALS['ident']));
    if(is_null($this->page->name)) $this->page->name = $GLOBALS['ident'];
  }
  function index() {
    $this->all();
    load_view('list.php');
  }
  // views
  function all() {
    global $pages;
    $pages = $this->page->find_all(array('order by'=>'name'),false);
  }
  function show() {
    $GLOBALS['page'] = $this->page;
  }
  function edit() {
    $GLOBALS['page'] = $this->page;
  }
  // action
  function save() {
    $this->page->name = $_POST['page_name'];
    $this->page->save();
    $revision = new revision();
    $revision->page_id = $this->page->id; // hmm...
    $revision->time = time();
    $revision->body = $_POST['rev_body'];
    $revision->save();
    $metadata = explode("\n",$_POST['rev_metadata']);
    foreach($metadata as $item) {
      $link = new link();
      $link->as_of_revision = $revision->id;
      $link->from_id = $this->page->id;
      $split = explode(':',$item);
      if(!page::exists(trim($split[1]))) {
        $to_page = new page();
        $to_page->name = trim($split[1]);
        $to_page->save();
        $to_id = $to_page->id;
      } else {
        $to_id = page::id_from_name(trim($split[1])); 
      }
      $link->to_id = $to_id;
      $link->rel = trim($split[0]);
      $link->save();
    }
    // shouldn't go!!
    //redirect("pages/show/".$this->page->name);
  }
  function delete() {
    $this->page->delete_all();
    redirect("pages/show/".$this->page->name);
  }
}
?>