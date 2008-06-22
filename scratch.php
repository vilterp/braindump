<?php
include 'core/common.php';

debug_dump(page::create_if_doesnt_exist('Pete'));

debug_dump(array_search('Pete',page::$id_cache['Pete']));

debug_dump(page::id_from_name('Pete'));

debug_dump(page::$id_cache);

?>