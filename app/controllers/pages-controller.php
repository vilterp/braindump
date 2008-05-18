<?php
class pages_controller {
  
  // main views
  
  function index() {
    // TODO: semantic custom query goodness
    pass_var('pages',BQL::query('list'));
  }
  function show() {
    global $runtime;
    // FIXME: this runs id_from_name several times - inefficient
    $page->links_out = BQL::query("get $runtime[ident]");
    $page->links_in = BQL::query("backlinks to $runtime[ident]");
    $page->description = BQL::query("describe $runtime[ident]");
    pass_var('page',$page);
  }
  
  // for AJAX in place edit
  
  function just_body() {
    no_layout();
    global $runtime;
    echo BQL::query("describe $runtime[ident]");
  }
  // FIXME: it shouldn't use $_POST['value'] - something more descriptive
  function save_body() {
    no_layout();
    global $runtime;
    BQL::query("describe $runtime[ident] as ".$_POST['value']);
    echo do_filters('page_body',$_POST['value']);
  }
  function just_meta() { // loaded into edit box
    no_layout();
    global $runtime;
    print_meta(BQL::query("get $runtime[ident]"));
  }
  // FIXME: is it necessary to re-get the page here?
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
