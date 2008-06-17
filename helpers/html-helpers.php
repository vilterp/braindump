<?php
// html options string from associative array
function html_options($options) {
  if(is_string($options)) {
    return " $options";
  } elseif(is_array($options)) {
    $final = " ";
    foreach($options as $key=>$value) {
      $final .= "$key=\"$value\" ";
    }
    return substr($final,0,strlen($final)-1);
  } else {
    return '';
  }
}
function link_tag($url,$text,$options=NULL) {
  return "<a href='$url'".html_options($options).">$text</a>";
}
?>