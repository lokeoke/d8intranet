<?php
/**
 * Created by PhpStorm.
 * User: spheresh
 * Date: 10/17/15
 * Time: 6:01 PM
 */

namespace Drupal\intranet_jira;


class IntranetJiraWorkLogResponse {

  private $response;
  private $task;
  /**
   * @var IntranetJiraStorage
   */
  private $storage;

  public function __construct($response, $task) {
    $this->response = $response;
    $this->task = $task;

    $this->storage = \Drupal::service("intranet_jira.storage");
  }

  public function save() {
    foreach ($this->response->worklogs as $worklog) {
      $worklog_class = new IntranetJiraWorklog($worklog, $this->task);

      $this->storage->storeWorkLog($worklog_class);
      //IntranetJiraStorageTask::storeWorkLog($worklog_class);
    }

  }
}