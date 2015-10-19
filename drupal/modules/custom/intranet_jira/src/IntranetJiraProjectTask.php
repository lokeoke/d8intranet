<?php
/**
 * Created by PhpStorm.
 * User: spheresh
 * Date: 10/16/15
 * Time: 6:01 PM
 */

namespace Drupal\intranet_jira;

/**
 * Class IntranetJiraProjectTask
 * @package Drupal\intranet_jira
 */
class IntranetJiraProjectTask {

  /**
   * @var mixed
   */
  private $task;

  /**
   * @var mixed
   */
  private $storage;

  /**
   * IntranetJiraProjectTask constructor.
   * @param mixed $task
   */
  public function __construct($task) {
    $this->task = $task;
    $this->storage = \Drupal::service("intranet_jira.storage");
  }

  public function get() {
    return $this->task;
  }

  /**
   * @TODO change to $this->storage
   */
  public function setTime() {
    $time = IntranetJiraStorageTask::getTime($this);
    if (!$time->check()) {
      IntranetJiraStorageTask::setTime($this);
    }
    else {
      IntranetJiraStorageTask::updateTime($this);
    }
  }

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
  }

}