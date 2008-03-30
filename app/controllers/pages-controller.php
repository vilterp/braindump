<?php
class pages_controller {
  function __construct() {
    $this->page = new page(page::id_from_name($GLOBALS['ident']));
    if(is_null($this->page->name)) $this->page->name = $GLOBALS['ident'];
    if(!$this->page->in_db) {
      $this->page->links = links_from($this->page->name);
      $this->page->links_to = links_to($this->page->name);
    }
  }
  function index() {
    $this->all();
    load_view('list.php');
  }
  // views
  function all() {
    global $pages;
    $pages = $this->page->find_all(array('order by'=>'name'));
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
      $link->from_page = $this->page->name;
      $split = explode(':',$item);
      $link->to_page = trim($split[1]);
      $link->rel = trim($split[0]);
      $link->save();
    }
    redirect("pages/show/".$this->page->name);
  }
  function delete() {
    $this->page->delete_all();
    redirect("pages/show/".$this->page->name);
  }
}
?>