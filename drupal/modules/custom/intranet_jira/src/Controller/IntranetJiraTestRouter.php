<?php

/**
 * @file
 * Contains Drupal\intranet_jira\Controller\IntranetJiraTestRouter.
 */

namespace Drupal\intranet_jira\Controller;

use Drupal\Core\Controller\ControllerBase;
use Drupal\intranet_jira\IntranetJiraApiRest;
use Drupal\intranet_jira\IntranetJiraProjectTask;
use Drupal\intranet_jira\Plugin\QueueWorker\IntranetJiraAggregator;

/**
 * Class IntranetJiraTestRouter.
 *
 * @package Drupal\intranet_jira\Controller
 */
class IntranetJiraTestRouter extends ControllerBase {
  /**
   * Index.
   *
   * @return string
   *   Return Hello string.
   */
  public function index() {
    if(1) {
      \Drupal::configFactory()->getEditable('modulename.settings')
        ->delete();

    }

    /**
     * @var IntranetJiraApiRest $jira
     */
    $jira = \Drupal::service("intranet_jira.api_rest");

    /**
     * @var IntranetJiraProjectTask $project_task
     */
    foreach ($jira->loadMultiple() as $project_task) {

      if (!$project_task->timeExists() || !$project_task->timeUpdated()) {
        IntranetJiraAggregator::processItem($project_task);
      }
    }
    return [
        '#type' => 'markup',
        '#markup' => $this->t('Implement method: index')
    ];
  }

}
