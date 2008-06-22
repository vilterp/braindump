<?php
// FIXME: filename should be site name if no criteria
empty($_GET['criteria']) ? $name = 'pages' : $name = $_GET['criteria'];

// FIXME: .dump.yaml? .dump? .yaml?
force_download($name.'.dump.yaml');

echo Spyc::YAMLDump(array(
  'metadata' => $metadata,
  'data' => $pages
));

?>