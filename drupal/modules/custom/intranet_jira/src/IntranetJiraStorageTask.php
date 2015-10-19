<?php
/**
 * @file
 * IntranetJiraStorageTask class.
 */

namespace Drupal\intranet_jira;


use Symfony\Component\Validator\Constraints\True;
use Drupal\Core;

/**
 * Class IntranetJiraStorageTask
 * @deprecated
 */
class IntranetJiraStorageTask implements IntranetJiraStorageTaskInterface {
  /**
   * @param IntranetJiraWorklog $workinglog
   */
  public static function storeWorkLog($workinglog) {
    $task = $workinglog->getTask();
    \Drupal::configFactory()->getEditable('modulename.settings')
      ->set('cache.workinglog.' . $workinglog->getId(), $workinglog->timeSpent())
      ->save();

  }
  public static function updateTime($task) {
    IntranetJiraStorageTask::setTime($task);
  }
  public static function setTime($task) {
    \Drupal::configFactory()->getEditable('modulename.settings')
      ->set('cache.tasks.' . $task->getName(), $task->getUpdated())
      ->save();


  }

  /**
   * @param \Drupal\intranet_jira\IntranetJiraProjectTask $task
   * @return \Drupal\intranet_jira\IntranetJiraTime
   */
  public static function getTime(IntranetJiraProjectTask $task) {

    $config = \Drupal::config('modulename.settings');
    $value = $config->get('cache.tasks.' . $task->getName());


    return new IntranetJiraTime((object)array('value' => $value));
  }
}