<?php
class pages_controller {
  
  // main views
  
  function index() {
    // TODO: semantic custom query goodness
    pass_var('pages',BQL::_list($_GET['criteria']));
    pass_var('show_box',(!empty($_GET['criteria'])));
  }
  function show() {
    global $runtime;
    pass_var('page',new Page($runtime['ident']));
  }
  function special() {
    global $runtime;
    $runtime['view'] = "special_pages/$runtime[ident].php";
  }
  function dump() {}
  function import() {}
  function process_import() {
    $file = Spyc::YAMLLoad($_FILES['file']['tmp_name']);
    foreach($file['data'] as $page=>$data) {
      if($data['metadata'])
        foreach($data['metadata'] as $attribute=>$value)
          BQL::set($page,$attribute,$value);
      BQL::describe($page,$data['description']);
    }
    flash('Pages sucessfully imported.'); # FIXME: doesn't work
    redirect('');
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
    echo do_filters('page_body',$_POST['value']);
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
