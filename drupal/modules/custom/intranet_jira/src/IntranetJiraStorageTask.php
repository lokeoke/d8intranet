<?php
/**
 * @file
 * IntranetJiraStorageTask class.
 */

namespace Drupal\intranet_jira;


use Symfony\Component\Validator\Constraints\True;
use Drupal\Core;


class IntranetJiraStorageTask implements IntranetJiraStorageTaskInterface {
  /**
   * @param IntranetJiraWorklog $workinglog
   */
  public static function storeWorkLog($workinglog) {
    \Drupal::configFactory()->getEditable('modulename.settings')
      ->set('cache.workinglog.' . $workinglog->getId(), $workinglog->timeSpent())
      ->save();

  }
  public static function updateTime($task) {
    $node = IntranetJiraStorageTask::getTime($task);
    $node->field_jira_updated = substr($task->get()->fields->updated, 0, 19);
    $node->save();
  }
  public static function setTime($task) {
    $node = (object)array();
    $node->field_jira_key = $task->get()->key;
    $node->field_jira_id = $task->get()->id;
    $node->field_jira_summary = $task->get()->fields->summary;
    $node->field_jira_updated = substr($task->get()->fields->updated, 0, 19);
    $node->title = sprintf("%s - %s", $node->field_jira_key, $node->field_jira_summary);
    $edit_node = (array)$node;
    $edit_node += array(
      'nid' => NULL,
      'type' => 'jiratask',
      'uid' => 1,
      'revision' => 0,
      'status' => TRUE,
      'promote' => 0,
      'created' => REQUEST_TIME,
    );

    $node = entity_create('node', $edit_node);
    $node->save();


  }

  /**
   * @param \Drupal\intranet_jira\IntranetJiraProjectTask $task
   * @return \Drupal\intranet_jira\IntranetJiraTime
   */
  public static function getTime(IntranetJiraProjectTask $task) {

    $storage = \Drupal::entityManager()->getStorage('node');
    $query = \Drupal::entityQuery('node')
      ->condition('status', 1)
      ->condition('type', 'jiratask')
      ->condition('field_jira_key', $task->getHumanName());

    $nids = $query->execute();
    $node = array_shift($storage->loadMultiple($nids));
    return new IntranetJiraTime($node);
  }
}