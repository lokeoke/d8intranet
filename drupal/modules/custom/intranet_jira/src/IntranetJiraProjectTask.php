<?php
/**
 * Created by PhpStorm.
 * User: spheresh
 * Date: 10/16/15
 * Time: 6:01 PM
 */

namespace Drupal\intranet_jira;


class IntranetJiraProjectTask {
  private $task;
  /**
   * IntranetJiraProjectTask constructor.
   * @param mixed $task
   */
  public function __construct($task) {
    $this->task = $task;
  }

  public function timeExists() {
    return false;
  }

  public function timeUpdated() {
    return false;
  }

}