<?php
class pages_controller {
  
  // main views
  
  function index() {
    // TODO: semantic custom query goodness
    pass_var('pages',BQL::query('list'));
  }
  function show() {
    global $runtime;
    pass_var('page',BQL::query("get $runtime[ident]"));
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
    global $runtime;
    print_meta(BQL::query("get $runtime[ident]"));
  }
  function save_meta() {
    no_layout();
    global $runtime;
    save_meta($runtime['ident'],parse_meta($_POST['value']));
    print_meta(BQL::query("get $runtime[ident]"),true);
  }
  
  function delete() {
    BQL::query("unset $runtime[ident]");
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
