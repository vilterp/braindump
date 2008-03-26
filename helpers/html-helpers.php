<?php
// html options string from associative array
function html_options($options='') {
  if(is_string($options)){return $options;};
  $final = "";
  foreach($options as $key=>$value) {
    $final .= "$key=\"$value\"";
  }
  return $final;
}
// <select> element from array of items
function selector($items,$name,$selected=NULL,$options='') {
  echo "<select name='$name' ".html_options($options).">\n";
  foreach($items as $item) {
    if($item == $selected) {
      echo "<option value='$item' selected>$item</option>\n";
    } else {
      echo "<option value='$item'>$item</option>\n";
    }
  }
  echo "</select>\n";
}
function link_tag($url,$text,$options='') {
  return "<a href='$url' ".html_options($options).">$text</a>";
}
function script_tag($script) {
  return "<script type=\"text/javascript\" charset=\"utf-8\">
$script
</script>\n";
}
function form_tag($submit_url) {
  return "<form method='post' action='".getURL($submit_url)."'>\n";
}
function js_options($options='') {
  if(empty($options)) return NULL;
  if(is_string($options)) {
    return ',{'.$options.'}';
  } else {
    $opt = array();
    foreach($options as $key=>$value) {
      array_push($opt,"$key: $value");
    }
    return ",{".implode(',',$opt)."}";
  }
}
?>