<?php
function insert_opensearch_link() {
  echo '<link rel="search" type="application/opensearchdescription+xml" title="braindump" href="'.get_url('special/opensearch/description').'" />'."\n";
}

add_hook('head','insert_opensearch_link');
?>