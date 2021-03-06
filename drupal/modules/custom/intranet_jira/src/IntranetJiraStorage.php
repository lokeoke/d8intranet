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
class IntranetJiraStorage {

  /**
   * @var \Drupal\Core\Entity\EntityStorageInterface
   */
  private $storage;

  const SETTINGS_NAME = 'intranet_jira.settings';
  const SETTINGS_NAME_PREFIX = 'cache.tasks.';

  public static function getProjectTask(IntranetJiraProjectTask $task) {

    $value = \Drupal::state()->get(self::SETTINGS_NAME) ?: array();
    // @TODO FIX NOTICE
    return new IntranetJiraTime((object)array('value' => $value[$task->getName()]));

  }

  public static function saveProjectTask($task) {

    $value = \Drupal::state()->get(self::SETTINGS_NAME) ?: array();
    $value[$task->getName()] = $task->getUpdated();
    \Drupal::state()->set(self::SETTINGS_NAME, $value);

  }
  /**
   * Constructor.
   */
  public function __construct() {
    $this->storage = \Drupal::entityManager()->getStorage('node');
  }
  public function getLoggedTime($jira_name){
    if(date('N',$format = strtotime( "today" )) == 1) {
      $format = strtotime( "previous friday" );
    }
    else {
      $format = strtotime( "-1 day" );
    }
    $format =  date("Y-m-d", $format);
    $query = \Drupal::entityQuery('node')
      ->condition('type', 'jira_worklog')
      ->condition('field_jira_author', $jira_name)
      ->condition('field_jira_started', $format);
    $nids = $query->execute();
    return (count($nids) > 0 ? TRUE : FALSE);
  }
  /**
   * @param IntranetJiraWorklog $worklog_class
   */
  public function storeWorkLog($worklog_class) {
    $logger = \Drupal::logger('store');

    if(time() - (86400 * 4) > strtotime($worklog_class->started())) {
      return;
    }
    $logger->info("Jira work log %task in process", array("%task" => $worklog_class->getId()));
    $query = \Drupal::entityQuery('node')
      ->condition('type', 'jira_worklog')
      ->condition('field_jira_id', $worklog_class->getId());
    $nids = $query->execute();

    // if worklog is not exists
    if(count($nids) == 0) {

      $edit_node = $this->defaultNode("jira_worklog");
      $edit_node['title'] = sprintf("%s (%s)", $worklog_class->getId(), $worklog_class->timeSpent());
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
