<?php

namespace Drupal\intranet_helper;


class SpesialData {
  function __construct(array $data) {
    foreach($data as $key => $value) {
      $this->{$key} = $value;
    }
  }

  // This object was create for the sake of this function
  function __toString() {
    return serialize($this);
  }
}
