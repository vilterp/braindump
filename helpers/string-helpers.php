<?php
// limits a string to a number of words, adds ... at end
function limit_string($string,$words) {
  $split = explode(' ',$string);
  if(count($split) <= $words) return implode(' ',$split);
  $limited = array_chunk($split,$words);
  return implode(' ',$limited[0])."...";
}
// the next two functions, singularize and pluralize,
// are from the Picora PHP framework (http://livepipe.net/projects/picora/)
// Copyright (c) 2007 LivePipe LLC 
// and are themselves ports of the ruby inflector in Ruby on Rails,
// Copyright (c) 2005 David Heinemeier Hansson.
function singularize($str){
	//Singularize rules from Rails::ActiveSupport::inflections.rb
	//Copyright (c) 2005 David Heinemeier Hansson
	$uncountable = array('equipment','information','rice','money','species','series','fish','sheep');
	if(in_array(strtolower($str),$uncountable))
		return $str;
	$irregulars = array(
		'people'=>'person',
		'men'=>'man',
		'children'=>'child',
		'sexes'=>'sex',
		'moves'=>'move'
	);
	if(in_array(strtolower($str),array_keys($irregulars)))
		return $irregulars[$str];
	foreach(array(
		'/(quiz)zes$/i'=>'\1',
		'/(matr)ices$/i'=>'\1ix',
		'/(vert|ind)ices$/i'=>'\1ex',
		'/^(ox)en/i'=>'\1',
		'/(alias|status)es$/i'=>'\1',
		'/([octop|vir])i$/i'=>'\1us',
		'/(cris|ax|test)es$/i'=>'\1is',
		'/(shoe)s$/i'=>'\1',
		'/(o)es$/i'=>'\1',
		'/(bus)es$/i'=>'\1',
		'/([m|l])ice$/i'=>'\1ouse',
		'/(x|ch|ss|sh)es$/i'=>'\1',
		'/(m)ovies$/i'=>'\1ovie',
		'/(s)eries$/i'=>'\1eries',
		'/([^aeiouy]|qu)ies$/i'=>'\1y',
		'/([lr])ves$/i'=>'\1f',
		'/(tive)s$/i'=>'\1',
		'/(hive)s$/i'=>'\1',
		'/([^f])ves$/i'=>'\1fe',
		'/(^analy)ses$/i'=>'\1sis',
		'/((a)naly|(b)a|(d)iagno|(p)arenthe|(p)rogno|(s)ynop|(t)he)ses$/i'=>'\1\2sis',
		'/([ti])a$/i'=>'\1um',
		'/(n)ews$/i'=>'\1ews',
		'/s$/i'=>''
	) as $match => $replace)
		if(preg_match($match,$str))
			return preg_replace($match,$replace,$str);
	return $str;
}
function pluralize($str){
	//Singularize rules from Rails::ActiveSupport::inflections.rb
	//Copyright (c) 2005 David Heinemeier Hansson
	$uncountable = array('equipment','information','rice','money','species','series','fish','sheep');
	if(in_array(strtolower($str),$uncountable))
		return $str;
	$irregulars = array(
		'person'=>'people',
		'man'=>'men',
		'child'=>'children',
		'sex'=>'sexes',
		'move'=>'moves'
	);
	if(in_array(strtolower($str),array_keys($irregulars)))
		return $irregulars[$str];
	foreach(array(
		'/(quiz)$/i'=>'\1zes',
		'/^(ox)$/i'=>'\1en',
		'/([m|l])ouse$/i'=>'\1ice',
		'/(matr|vert|ind)ix|ex$/i'=>'\1ices',
		'/(x|ch|ss|sh)$/i'=>'\1es',
		'/([^aeiouy]|qu)ies$/i'=>'\1y',
		'/([^aeiouy]|qu)y$/i'=>'\1ies',
		'/(hive)$/i'=>'\1s',
		'/(?:([^f])fe|([lr])f)$/i'=>'\1\2ves',
		'/sis$/i'=>'ses',
		'/([ti])um$/i'=>'\1a',
		'/(buffal|tomat)o$/i'=>'\1oes',
		'/(bu)s$/i'=>'\1ses',
		'/(alias|status)$/i'=>'\1es',
		'/(octop|vir)us$/i'=>'\1i',
		'/(ax|test)is$/i'=>'\1es',
		'/s$/i'=>'s',
		'/$/'=> 's'
	) as $match => $replace)
		if(preg_match($match,$str))
			return preg_replace($match,$replace,$str);
	return $str;
}
function is_singular($word) {
  return singularize($word) == $word;
}
function is_plural($word) {
  return pluralize($word) == $word;
}
// 'apples, oranges, and milk' => array('apples','oranges','milk')
function english_to_array($sentence) {
  $final = array();
  $and_split = explode(' and ',$sentence);
  foreach(explode(',',$and_split[0]) as $item) {
    if(!empty($item)) $final[] = trim($item);
  }
  if($and_split[1]) $final[] = $and_split[1];
  return $final;
}
// array('apples','oranges','milk') => 'apples, oranges, and milk'
function array_to_english($array) {
  if(count($array) == 2) return "$array[0] and $array[1]";
  $final = "";
  for($i=0;$i<count($array)-1;$i++) {
    $final .= $array[$i].', ';
  }
  $final .= ' and '.$array[count($array)-1];
  return $final;
}
?>