<?php
// useful for activity feeds
function split_by_day($items,$time_key,$date_format="l, n/j/Y") {
  if(!is_array($items)){return false;};
  $interval = 86400;
  $temp = array();
  foreach($items as $item) {
    $day = $item->$time_key/$interval;
    settype($day,'int');
    if(is_null($temp[$day])){$temp[$day]=array();};
    array_push($temp[$day],$item);
  }
  $result = array();
  $today = date($date_format,round(time())); // today
  $yesterday = date($date_format,round(time())-$interval); // yesterday
  foreach($temp as $day) {
    $time = $day[0]->$time_key;
    
    if(date($date_format,$time) == $today) {
      $date = "Today";
    } elseif(date($date_format,$time) == $yesterday) {
      $date = "Yesterday";
    } else {
      $date = date($date_format,$time);
    }
    
    $result[$date] = $day;
  }
  return $result;
}
// like "Wednesday, 12/31/1969 @ 7:00pm"
function full_date($time) {
  return date("l, n/j/Y @ g:ia",$time);
}
function just_time($time) {
  return date("g:ia",$time);
}
function just_date($time) {
  return date("l, n/j/Y",$time);
}
?>