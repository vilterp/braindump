<?php
include 'core/common.php';

debug_dump($db->query("select * from pages where name REGEXP '/skill(s|z)/'")->fetchAll());

?>