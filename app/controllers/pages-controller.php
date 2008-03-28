<?php
class pages_controller {
  function __construct() {
    $this->page = new page(page::id_from_name($GLOBALS['ident']));
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
    $revision->ip = $_SERVER['REMOTE_ADDR'];
    $revision->author = $_POST['rev_author'];
    $revision->body = $_POST['rev_body'];
    $revision->save();
    setcookie('author',$revision->author,time()+60*60*24*30); // agghh!! not working!!
    redirect("pages/show/".$this->page->name);
  }
  function delete() {
    $this->page->delete_all();
  }
}
?>