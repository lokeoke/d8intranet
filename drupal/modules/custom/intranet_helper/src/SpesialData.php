<?php

namespace Drupal\intranet_helper;


class SpesialData {
  function __construct(array $data) {
    foreach($data as $key => $value) {
      $this->{$key} = $value;
    }
  }
  function __toString() {
    return serialize($this);
  }
}