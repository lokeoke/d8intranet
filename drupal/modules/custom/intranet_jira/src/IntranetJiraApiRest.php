<?php

/**
 * @file
 * Contains Drupal\intranet_jira\IntranetJiraApiRest.
 */

namespace Drupal\intranet_jira;

use Drupal\jira_rest\Controller\JiraRestController;
use Drupal\jira_rest\JiraRestException;
use Drupal\jira_rest\JiraRestService;

/**
 * Class IntranetJiraApiRest.
 *
 * @package Drupal\intranet_jira
 */
class IntranetJiraApiRest {

  /**
   * @var JiraRestService
   */
  protected $jira_service;

  /**
   * @var JiraRestController
   */
  protected $jira;

  /**
   * @param \Drupal\jira_rest\JiraRestService $jira_service
   */
  public function __construct(JiraRestService $jira_service) {
    $this->jira_service = $jira_service;

    $container = \Drupal::getContainer();
    $this->jira = JiraRestController::create($container);


  }
  public function forceUpdate($day = 0) {
    /**
     * @var IntranetJiraProjectTask $project_task
     */
    foreach ($this->loadMultiple($day) as $project_task) {

      if (!$project_task->timeExists() || !$project_task->timeUpdated()) {
        $project_task->setTime();
        $project_task->refreshItems();
      }
    }
  }
  /**
   * Return all projects by criteria
   *
   * @return \Drupal\intranet_jira\IntranetJiraResponse
   * @throws \Drupal\jira_rest\JiraRestException
   * @throws \Exception
   */
  public function loadMultiple($day = 0) {

    $search = new IntranetJiraApiSearch();

    /*$search->addProjects(array("PUI"));*/
    $search->addUpdated($day, +1);

    return $this->search($search);

  }

  /**
   * Jira rest get search issue function
   *
   * @param $search
   * @return \Drupal\intranet_jira\IntranetJiraResponse
   * @throws \Drupal\jira_rest\JiraRestException
   * @throws \Exception
   */
  private function search($search) {
    try {

      $response = $this->jira->jira_rest_searchissue((string)$search);
      return new IntranetJiraResponse($response);

    } catch (JiraRestException $e) {
      watchdog_exception($e);
    }
  }

  /**
   * Jira rest get worklog function
   *
   * @param $task
   * @return \Drupal\intranet_jira\IntranetJiraWorkLogResponse
   * @throws \Drupal\jira_rest\JiraRestException
   * @throws \Exception
   */
  public function getWorklog($task) {
    try {

      $response = $this->jira->jira_rest_get_worklog($task->getHumanName());
      return new IntranetJiraWorkLogResponse($response, $task);

    } catch (JiraRestException $e) {
      watchdog_exception($e);
    }
  }
}
