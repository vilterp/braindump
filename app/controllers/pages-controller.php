<?php
class pages_controller {
  
  // main views
  
  function index() { // FIXME: should be 'list'?
    // TODO: make sure this isn't cached (in any format)
    pass_var('pages',BQL::_list($_GET['criteria']));
    pass_var('no_criteria',empty($_GET['criteria']));
  }
  function dump() { // like index, but full page data passed
    global $config;
    $metadata = array(
      'criteria' => $_GET['criteria'],
      'time' => date('c'), # ISO 8601
      'site_url' => $config['base_url'],
      'dump_schema' => 0.1
    );
    pass_var('metadata',$metadata);
    
    $pages = array();
    foreach(BQL::_list($_GET['criteria']) as $page) {
      $pages[$page] = array(
        'metadata' => BQL::get($page),
        'description' => BQL::describe($page)
      );
    }
    pass_var('pages',$pages);
  }
  function show() {
    global $runtime;
    pass_var('page',new Page($runtime['ident']));
  }
  function special() {
    global $runtime;
    
    isset($runtime['sub_special_page']) ?
      $special_page = $runtime['sub_special_page'] : 
      $special_page = 'index';
      
    $base_path = PATH_TO_PLUGINS.$runtime['ident'];
    if(file_exists("$base_path/$special_page.php")) {
      $runtime['view'] = "$base_path/$special_page.php";
    } elseif(file_exists("$base_path/_$special_page.php")) {
      $runtime['view'] = "$base_path/_$special_page.php";
      no_layout(); // no layout if underscore in front of filename
    }
    
    $runtime['ident'] = unhyphenate($runtime['ident']);
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
