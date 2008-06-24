<?php
function insert_atom_link() {
  // FIXME: this link is broken somehow...
  global $runtime;
  if($runtime['action'] == 'index') {
    echo '<link rel="alternate" type="application/atom+xml" href="'.get_url("?format=atom&amp;criteria=".urlencode($_GET[criteria])).'"/>'."\n";
  }
}

add_hook('head','insert_atom_link');
?>