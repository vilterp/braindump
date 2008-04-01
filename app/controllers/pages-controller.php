<?php
class pages_controller {
  function __construct() {
    $this->page = new page(page::id_from_name($GLOBALS['ident']));
    if(is_null($this->page->name)) $this->page->name = $GLOBALS['ident'];
  }
  // views
  function index() {
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
    // save links
    $metadata = explode("\n",$_POST['rev_metadata']);
    array_pop($metadata);
    $good_links = array();
    foreach($metadata as $item) { // go through links
      echo "---- a link! ----<br />";
      $link = new link();
      $link->as_of_revision = $revision->id;
      $link->from_id = $this->page->id;
      $split = explode(':',$item);
      if(!page::exists(trim($split[1]))) { // if the to page doesn't exist, make it
        $to_page = new page();
        $to_page->name = trim($split[1]);
        $to_page->save();
        $to_id = $to_page->id;
      } else { // otherwise, get its id
        $to_id = page::id_from_name(trim($split[1])); 
      }
      $link->to_id = $to_id;
      $link->rel = trim($split[0]);
      $link->changed_since = 0;
      if($good_link = link::exists($link->from_id,$link->rel,$link->to_id)) {
        // if the link already exists, save its id in the array
        array($good_links,$good_link);
      } else {
        // otherwise, save it
        $link->save();
        array_push($good_links,$link->id);
      }
    }
    // set 'changed_since' to 1 for links not in the input (meaning they've been changed)
    $GLOBALS['db']->links->update('changed_since = 1',
      'from_id = '.$this->page->id.' AND id != '.implode(' AND id != ',$good_links));
    //redirect("pages/show/".$this->page->name); // whew! finally
  }
  function delete() {
    $this->page->delete_all();
    redirect("pages/show/".$this->page->name);
  }
}
?>
