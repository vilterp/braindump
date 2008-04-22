<?php
// useful for activity feeds
function split_by_day($items,$time_key,$date_format="l, n/j/Y") {
  if(!is_array($items)) return false;
  $interval = 86400;
  $temp = array();
  foreach($items as $item) {
    $day = $item->$time_key/$interval;
    settype($day,'int');
    if(is_null($temp[$day])) $temp[$day]=array();
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
// from the comments at the bottom of the PHP documentation for time()
// returns 'n units ago'
function timeDiff($time, $opt = array()) {
    // The default values
    $defOptions = array(
        'to' => 0,
        'parts' => 1,
        'precision' => 'second',
        'distance' => TRUE,
        'separator' => ', '
    );
    $opt = array_merge($defOptions, $opt);
    // Default to current time if no to point is given
    (!$opt['to']) && ($opt['to'] = time());
    // Init an empty string
    $str = '';
    // To or From computation
    $diff = ($opt['to'] > $time) ? $opt['to']-$time : $time-$opt['to'];
    // An array of label => periods of seconds;
    $periods = array(
        'decade' => 315569260,
        'year' => 31556926,
        'month' => 2629744,
        'week' => 604800,
        'day' => 86400,
        'hour' => 3600,
        'minute' => 60,
        'second' => 1
    );
    // Round to precision
    if ($opt['precision'] != 'second') 
        $diff = round(($diff/$periods[$opt['precision']])) * $periods[$opt['precision']];
    // Report the value is 'less than 1 ' precision period away
    (0 == $diff) && ($str = 'less than 1 '.$opt['precision']);
    // Loop over each period
    foreach ($periods as $label => $value) {
        // Stitch together the time difference string
        (($x=floor($diff/$value))&&$opt['parts']--) && $str.=($str?$opt['separator']:'').($x.' '.$label.($x>1?'s':''));
        // Stop processing if no more parts are going to be reported.
        if ($opt['parts'] == 0 || $label == $opt['precision']) break;
        // Get ready for the next pass
        $diff -= $x*$value;
    }
    $opt['distance'] && $str.=($str&&$opt['to']>$time)?' ago':' away';
    return $str;
}
function superTimeDiff($time,$opt = array()) {
  return "<span title='".date("l, n/j/Y @ g:ia",$time)."'>".timeDiff($time,$opt)."</span>";
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