<?php

namespace Drupal\intranet_helper\Controller;

use Behat\Mink\Exception\Exception;
use Drupal\Core\Controller\ControllerBase;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Drupal\user\Entity\User;
use \Drupal\jira_rest\Controller\JiraRestController;


class JiraRestWorkLogController extends ControllerBase {

  public function view(Request $request) {
    try {


      //$jira_rest = \Drupal::service('jira_rest.jira_rest_service');

      $container = \Drupal::getContainer();
      $jira = JiraRestController::create($container);

      $author_name = $jira
        ->config('jira_rest.settings')
        ->get('jira_rest.username');
      $interval = "-2d";
      $res = $jira->jira_rest_searchissue("updated >= " . $interval . " AND assignee in (" . $author_name . ")");

    } catch(JiraRestException $e) {
      $responce['messages'][] = $e->getMessage();
    }
      $responce = array();

      $interval = abs(intval($interval));
      $sub_days = "$interval days";
      $date = date_create();
      $toDate = date_format($date, 'Y-m-d');
      date_sub($date, date_interval_create_from_date_string($sub_days));
      $fromDate = date_format($date, 'Y-m-d');

      foreach ($res->issues as $issue) {
        $worklog = $jira->jira_rest_get_worklog($issue->key);
        foreach ($worklog->worklogs as $entry) {
          $shortDate = substr($entry->started, 0, 10);
          # keep a worklog entry on $key item,
          # iff within the search time period
          if ($entry->author->name == $author_name && $shortDate >= $fromDate && $shortDate <= $toDate)
            $responce[$issue->key] += $entry->timeSpentSeconds;
        }
      }
    return new JsonResponse($responce);
  }

}
