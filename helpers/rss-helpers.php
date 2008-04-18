<?php
class RSSFeed {
  function __construct($attributes=array(),$items=array()) {
    foreach($attributes as $attribute=>$value) {
      $this->$attribute = $value;
    }
    $this->items = array();
    foreach($items as $item) {
      array_push($this->items,$item);
    }
  }
  function add_attribute($key,$value) {
    $this->$key = $value;
  }
  function add_item($item) {
    if(is_array($item)) {
      $the_item = new RSSItem($item);
      array_push($this->items,$the_item);
    } else {
      array_push($this->items,$item);
    }
  }
  function generate($return=true) {
    $final = "<?xml version=\"1.0\" encoding=\"UTF-8\"?>
<rss version=\"2.0\" xmlns:dc=\"http://purl.org/dc/elements/1.1/\">
  <channel>
  	<title>$this->title</title>
  	<link>$this->link</link>
  	<language>$this->language</language>
  	<description>$this->description</description>\n";
  	foreach($this->items as $item) {
  	  $final .= $item->generate();
  	}
    	$final .= "  </channel>
</rss>";
    if($return) {
      return $final;
    } else {
      echo $final;
    }
  }
  function dump($attributes,$items) {
    $feed = new RSSFeed($attributes,$items);
    return $feed->generate();
  }
}
class RSSItem {
  function __construct($attributes=array()) {
    foreach($attributes as $attribute=>$value) {
      $this->$attribute = $value;
    }
  }
  function add_attribute($key,$value) {
    $this->$key = $value;
  }
  function generate($return=true) {
    $final = "  <item>
    <title>$this->title</title>
    <description><![CDATA[$this->description]]></description>
    <pubDate>".date("r",$this->date)."</pubDate>
    <dc:creator>$this->creator</dc:creator>
    <link>$this->link</link>
  </item>\n";
    if($return) {
      return $final;
    } else {
      echo $final;
    }
  }
}
?>