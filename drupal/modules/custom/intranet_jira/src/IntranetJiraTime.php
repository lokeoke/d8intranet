<?php
/**
 * Created by PhpStorm.
 * User: spheresh
 * Date: 10/17/15
 * Time: 11:23 PM
 */

namespace Drupal\intranet_jira;


class IntranetJiraTime {

  /**
   * IntranetJiraTime constructor.
   * @param mixed $node
   */
  public function __construct($node) {
    $this->time = $node;
  }

  public function check() {
    return is_null($this->time->value);
  }

  public function getTime() {
    return $this->time->value;
  }
}