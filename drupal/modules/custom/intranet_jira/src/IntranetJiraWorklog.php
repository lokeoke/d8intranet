<?php
/**
 * Created by PhpStorm.
 * User: spheresh
 * Date: 10/17/15
 * Time: 6:23 PM
 */

namespace Drupal\intranet_jira;


class IntranetJiraWorklog {
  private $worklog;
  private $task;
  /**
   * IntranetJiraWorklog constructor.
   * @param $worklog
   */
  public function __construct($worklog, $task) {
    $this->worklog = $worklog;
    $this->task = $task;
  }
  public function getId() {
    return $this->worklog->id;
  }
  public function timeSpent() {
    return $this->worklog->timeSpentSeconds;
  }
  public function author() {
    return $this->worklog->author->name;
  }
  public function started() {
    return substr($this->worklog->started, 0, 10);
  }


}