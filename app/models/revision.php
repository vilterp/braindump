<?php
class revision extends DatabaseObject {
  function connect() {
    $this->has_one('page');
    $this->has_many('triple','set_at_revision','triples_set');
    $this->has_many('triple','changed_at_revision','triples_changed');
  }
}
?>
