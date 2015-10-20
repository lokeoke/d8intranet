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

  const SETTINGS_NAME = 'modulename.settings';
  const SETTINGS_NAME_PREFIX = 'cache.tasks.';

  /**
   * Helper function
   * It shoul be deleted in the features
   * (use it just for testing)
   *
   * @return string
   */
  public function index() {
    \Drupal::configFactory()->getEditable(self::SETTINGS_NAME)
      ->delete();
    return [
      '#type' => 'markup',
      '#markup' => $this->t('Intranet jira cache: cleared')
    ];
  }

}
