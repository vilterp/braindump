<?php
$file = Spyc::YAMLLoad($_FILES['file']['tmp_name']);
foreach($file['data'] as $page=>$data) {
  if($data['metadata'])
    foreach($data['metadata'] as $attribute=>$value)
      BQL::set($page,$attribute,$value);
  BQL::describe($page,$data['description']);
}
flash('Pages sucessfully imported.'); # FIXME: doesn't work
redirect('');
?>