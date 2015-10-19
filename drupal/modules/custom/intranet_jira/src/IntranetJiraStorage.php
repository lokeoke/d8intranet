<?php

/**
 * @file
 * Contains Drupal\intranet_jira\IntranetJiraStorage.
 */

namespace Drupal\intranet_jira;


/**
 * Class IntranetJiraStorage.
 *
 * @package Drupal\intranet_jira
 */
class IntranetJiraStorage implements IntranetJiraStorageInterface {

  /**
   * @var \Drupal\Core\Entity\EntityStorageInterface
   */
  private $storage;

  /**
   * Constructor.
   */
  public function __construct() {
    $this->storage = \Drupal::entityManager()->getStorage('node');
  }

  /**
   * @param IntranetJiraWorklog $worklog_class
   */
  public function storeWorkLog($worklog_class) {

    $query = \Drupal::entityQuery('node')
      ->condition('type', 'jira_worklog')
      ->condition('field_jira_id', $worklog_class->getId());
    $nids = $query->execute();

    // if worklog is not exists
    if(count($nids) == 0) {

      $edit_node = $this->defaultNode("jira_worklog");
      $edit_node['title'] = sprintf("%s (%s)", $worklog_class->author(), $worklog_class->timeSpent());
      $edit_node['field_jira_id'] = $worklog_class->getId();
      $edit_node['field_jira_author'] = $worklog_class->author();
      $edit_node['field_jira_time'] = $worklog_class->timeSpent();
      $edit_node['field_jira_started'] = $worklog_class->started();
      $node = entity_create('node', $edit_node);
      $node->save();


    }
    else {
      // @TODO Update worklog node if it will be needed
    }
  }

  /**
   * @param $string
   * @return array
   */
  private function defaultNode($string) {
    return array(
      'nid' => NULL,
      'type' => $string,
      'uid' => 1,
      'revision' => 0,
      'status' => 0,
      'promote' => 0,
      'created' => REQUEST_TIME,
    );

  }


}
