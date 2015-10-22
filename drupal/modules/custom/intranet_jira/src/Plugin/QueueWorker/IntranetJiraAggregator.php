<?php

namespace Drupal\intranet_jira\Plugin\QueueWorker;

use Drupal\Core\Queue\QueueWorker;
use Drupal\Core\Queue\QueueWorkerBase;
use Drupal\intranet_jira\IntranetJiraProjectTask;

/**
 * Updates a feed's items.
 *
 * @QueueWorker(
 *   id = "intranet_jira_aggregator",
 *   title = @Translation("Intranet Jira Aggregator"),
 *   cron = {"time" = 60}
 * )
 */
class IntranetJiraAggregator extends QueueWorkerBase {

  /**
   * @param mixed $project_task
   */
  public function processItem($project_task) {

    // Actually this part is not using
    // @see IntranetJiraApiRest::forceUpdate()
    if ($project_task instanceof IntranetJiraProjectTask) {
      $logger = \Drupal::logger('queue');
      $logger->info("Jira %task in process", array("%task" => $project_task->getHumanName()));

      /**
       * @var IntranetJiraProjectTask $data
       */
      $project_task->setTime();
      $project_task->refreshItems();
    }
  }

}