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
  public function get() {
    return $this->task;
  }
  public function setTime() {
    $time = IntranetJiraStorageTask::getTime($this);
    if (!$time->check()) {
      IntranetJiraStorageTask::setTime($this);
    }
    else {
      IntranetJiraStorageTask::updateTime($this);
    }
  }

  /**
   * @return bool If time is already stored
   */
  public function timeExists() {
    $time = IntranetJiraStorageTask::getTime($this);
    /**
     * @TODO Rename check function
     */
    return ($time->check() ? FALSE : TRUE);
  }
  public function getUpdated() {
    return strtotime(substr($this->task->fields->updated, 0 ,19));
  }

  public function getName() {
    return $this->task->id;
  }
  public function getHumanName  () {
    return $this->task->key;
  }
  public function timeUpdated() {
    $time = IntranetJiraStorageTask::getTime($this);

    if(!$time->check() && $this->getUpdated() != $time->getTime()) {
      return FALSE;
    }
    return TRUE;
  }

  public function refreshItems() {
    $jira = \Drupal::service("intranet_jira.api_rest");
    $worklog = $jira->getWorklog($this);
    $worklog->save();
    /*foreach ($worklog->worklogs as $entry) {
      $shortDate = substr($entry->started, 0, 10);
      # keep a worklog entry on $key item,
      # iff within the search time period
      if ($entry->author->name == $user_name && $shortDate >= $fromDate && $shortDate <= $toDate) {
        $response[$issue->key] += $entry->timeSpentSeconds;
      }
    }*/
  }

}