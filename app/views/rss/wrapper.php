<?php
content_type("application/rss+xml");
echo "<?xml version=\"1.0\" encoding=\"UTF-8\"?>\n";
?>
<rss version="2.0" xmlns:dc="http://purl.org/dc/elements/1.1/">
  <channel>
  	<title></title>
  	<link></link>
  	<language>en-us</language>
  	<description></description>
  	<?php include $view ?>
	</channel>
</rss>