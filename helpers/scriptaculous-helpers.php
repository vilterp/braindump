<?php
// return js snippets that trigger various effects
// return vs. echo...?
function load_scriptaculous() {
  load_js('scriptaculous/prototype.js');
  load_js('scriptaculous/scriptaculous.js');
}
function toggle_link($element,$link_text,$options='') {
  echo link_tag('#',$link_text,array('onclick'=>"Effect.toggle('".$element."','slide'".js_options($options).")"));
}
function edit_in_place($element,$save_url,$options='') {
  echo script_tag("new Ajax.InPlaceEditor('$element','".getURL($save_url)."'".js_options($options).");");
}
function visual_effect($effect,$element='',$options='') {
  if(empty($element)) { // element attribute
    return "new Effect.$effect(this".js_options($options).")";
  } else { // inside script tag
    echo script_tag("new Effect.$effect('$element'".js_options($options).");");
  }
}
?>