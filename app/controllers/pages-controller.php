<?php
class pages_controller {
  
  // main views
  
  function index() {
    pass_var('pages',BQL::_list($_GET['criteria']));
    pass_var('show_box',(!empty($_GET['criteria'])));
  }
  function show() {
    global $runtime;
    pass_var('page',new Page($runtime['ident']));
  }
  function special() {
    global $runtime;
    isset($runtime['sub_special_page']) ?
      $special_page = $runtime['sub_special_page'] : 
      $special_page = 'index.php';
    $runtime['view'] = PATH_TO_PLUGINS."$runtime[ident]/$special_page.php";
    $info = get_plugin_info($runtime['ident']);
    if($info['pages'][$special_page]['layout'] === false) no_layout();
  }
  function dump() {
    global $runtime;
    $runtime['format'] = 'dump'; // meh...
  }
  
  // for AJAX in place edit
  
  function just_description() {
    no_layout();
    global $runtime;
    echo BQL::describe($runtime['ident']);
  }
  function save_description() {
    no_layout();
    global $runtime;
    // FIXME: it shouldn't use $_POST['value'] - something more specific
    BQL::describe($runtime['ident'],$_POST['value']);
    echo do_filters('page_description',$_POST['value']);
  }
  function just_meta() { // loaded into edit box
    no_layout();
    global $runtime;
    print_metadata(BQL::get($runtime['ident']));
  }
  // FIXME: is it necessary to re-get the page here?
  function save_metadata() {
    no_layout();
    global $runtime;
    save_metadata($runtime['ident'],parse_metadata($_POST['value']));
    print_metadata(BQL::get($runtime['ident']),true);
  }
  
  function delete() {
    BQL::_unset($runtime['ident']);
    redirect('');
  }
  // delete the entire db. useful sometimes.
  function delete_everything() {
    global $db;
    $db->delete('pages');
    $db->delete('triples');
    redirect('');
  }
  
  function redirect() { // for the goto box
    redirect("show/$_GET[name]");
  }
}
?>
