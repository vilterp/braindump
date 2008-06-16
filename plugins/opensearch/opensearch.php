<?php
function insert_opensearch_link() {
  echo '<link rel="search" type="application/opensearchdescription+xml" title="braindump" href="'.getURL('special/opensearch/description').'" />'."\n";
}

add_hook('head','insert_opensearch_link');
?>