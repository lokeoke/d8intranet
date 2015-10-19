<?php
/**
 * $file
 * Describe IntranetJiraTime class
 */

namespace Drupal\intranet_jira;

/**
 * Class IntranetJiraTime
 * @package Drupal\intranet_jira
 */
class IntranetJiraTime {

  /**
   * IntranetJiraTime constructor.
   * @param $time
   */
  public function __construct($time) {
    $this->time = $time;
  }

  /**
   * Check that the value is empty
   * @return bool
   */
  public function isNonExists() {
    return is_null($this->time->value);
  }

  /**
   * Retrun the value of time
   * @return string
   */
  public function getValue() {
    return $this->time->value;
  }
}