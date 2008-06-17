<?php
// FIXME: filename should be site name if no criteria
empty($_GET['criteria']) ? $name = 'pages' : $name = $_GET['criteria'];
// FIXME: .dump.yaml? .dump? .yaml?
force_download($name.'.dump.yaml');

$metadata = array(
  'criteria' => $_GET['criteria'],
  'time' => date('c'), # ISO 8601
  'site_url' => $config['base_url'],
  'dump_schema' => 0.1
);

$data = array();
foreach(BQL::_list($_GET['criteria']) as $page) {
  $data[$page] = array(
    'metadata' => BQL::get($page),
    'description' => BQL::describe($page)
  );
}

echo Spyc::YAMLDump(array(
  'metadata' => $metadata,
  'data' => $data
));

?>