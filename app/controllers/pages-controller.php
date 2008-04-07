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
    load_view('list.php');
  }
  function show() {
    $GLOBALS['page'] = $this->page;
  }
  function edit() {
    $GLOBALS['page'] = $this->page;
  }
  function redirect() { // for the goto box
    $name = $_GET['name'];
    if(page::exists($name)) {
      redirect("pages/show/$name");
    } else {
      redirect("pages/edit/$name");
    }
  }
  // action
  function save() {
    // save page
    $this->page->name = $_POST['page_name'];
    $this->page->save();
    // save new revision
    $revision = new revision();
    $revision->page_id = $this->page->id; // hmm...
    $revision->time = time();
    $revision->body = $_POST['rev_body'];
    $revision->save();
    // save triples
    // TODO: abstract in triple API helper...
    $metadata = explode("\n",$_POST['rev_metadata']);
    $triples_in_input = array();
    foreach($metadata as $item) { // go through links
      if(!empty($item)) {
        $triple = new triple();
        $triple->set_at_revision = $revision->id;
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
        $triple->changed_at_revision = NULL;
        if($existing = triple::exists($triple->from_id,$triple->rel,$triple->to_id)) {
          // if the link already exists, save its id in the array
          array_push($triples_in_input,$existing);
        } else {
          // otherwise, save it
          $triple->save();
          array_push($triples_in_input,$triple->id);
        }
      }
    }
    // keep track of this revision id for triples not in the input 
    // (meaning they've been changed in this revision)
    if(count($triples_in_input) > 0) {
      $GLOBALS['db']->triples->update("changed_at_revision = $revision->id",
      'from_id = '.$this->page->id.' AND changed_at_revision = NULL 
      AND id != '.implode(' AND id != ',$triples_in_input));
    }
    redirect("pages/show/".$this->page->name); // whew!
  }
  function delete() {
    $this->page->delete_all();
    redirect("pages/show/".$this->page->name);
  }
}
?>
