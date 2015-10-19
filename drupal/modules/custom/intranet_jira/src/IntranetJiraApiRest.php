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
class IntranetJiraApiRest implements IntranetJiraApiRestInterface {

  /**
   * Drupal\jira_rest\JiraRestService definition
   */
  protected $jira_rest_jira_rest_service;

  /**
   * Constructor.
   * @param \Drupal\jira_rest\JiraRestService $jira_rest_jira_rest_service
   */
  public function __construct(JiraRestService $jira_service) {
    $this->jira_service = $jira_service;

    $container = \Drupal::getContainer();
    $this->jira = JiraRestController::create($container);


  }

  /**
   * @return \Drupal\intranet_jira\IntranetJiraResponse
   */
  public function loadMultiple() {
    // project in (PUI) AND updated >= "2015-10-16" AND updated < "2015-10-17"
    $search = new IntranetJiraApiSearch();

    //$search->addProjects(array("PUI"));

    $search->addUpdated(-1, 0);

    return $this->search($search);

  }

  private function search($search) {
    try {

      $response = $this->jira->jira_rest_searchissue((string)$search);
      return new IntranetJiraResponse($response);



    } catch (JiraRestException $e) {

      throw $e;
    }
  }
  public function getWorklog($task) {
    try {

      $response = $this->jira->jira_rest_get_worklog($task->getHumanName());
      return new IntranetJiraWorkLogResponse($response, $task);



    } catch (JiraRestException $e) {
      throw $e;
    }
  }
}
