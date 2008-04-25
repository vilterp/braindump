<?php
class DatabaseObjectIterator implements Countable, ArrayAccess, Iterator {
  function __construct($items) {
    $this->items = $items;
  }
  // countable
  function count() {
    return count($this->items);
  }
  // arrayAccess
  function offsetExists($offset) {
    return $this->items->offsetExists($offset);
  }
  function offsetGet($offset) {
    return $this->items[$offset];
  }
  function offsetSet($offset,$value) {
    return true;
  }
  function offsetUnset($offset) {
    return true;
  }
  // iterator
  function current() {
    return $this->items->current();
  }
  function key() {
    return $this->items->key();
  }
  function next() {
    return $this->items->next();
  }
  function rewind() {
    return $this->items->rewind();
  }
  function valid() {
    return $this->items->valid();
  }
}
?>