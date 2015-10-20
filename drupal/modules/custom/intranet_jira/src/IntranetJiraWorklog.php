<?php
/**
 * Created by PhpStorm.
 * User: spheresh
 * Date: 10/17/15
 * Time: 6:23 PM
 */

namespace Drupal\intranet_jira;

use stdClass;

class IntranetJiraWorklog {
  /**
   * @var \stdClass
   */
  private $worklog;

  /**
   * @var \Drupal\intranet_jira\IntranetJiraProjectTask
   */
  private $task;

  /**
   * @param \stdClass $worklog
   * @param \Drupal\intranet_jira\IntranetJiraProjectTask $task
   */
  public function __construct(stdClass $worklog, IntranetJiraProjectTask $task) {
    $this->worklog = $worklog;
    $this->task = $task;
  }

  /**
   * @return \Drupal\intranet_jira\IntranetJiraProjectTask
   */
  public function getTask() {
    return $this->task;
  }
  public function getWork() {
    return $this->worklog;
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