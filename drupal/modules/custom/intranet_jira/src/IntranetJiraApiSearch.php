<?php
/**
 * Created by PhpStorm.
 * User: spheresh
 * Date: 10/16/15
 * Time: 6:06 PM
 */

namespace Drupal\intranet_jira;

/**
 * Class IntranetJiraApiSearch
 * @package Drupal\intranet_jira
 */
class IntranetJiraApiSearch {

  /**
   * @var array
   */
  private $conditional;

  /**
   * IntranetJiraApiSearch constructor.
   */
  public function __construct() {

  }

  /**
   * Add a project conditional
   *
   * @param $projects
   */
  public function addProjects($projects) {
    if(is_array($projects)) {
      $projects = implode(',', $projects);
    }
    $this->conditional[] = "project in ($projects)";
  }

  /**
   * Calculate condition by relative day (ex. +1 day)
   *
   * @param $day
   * @return \DateTime
   */
  public function calcDay($day) {
    $action = "date" . (($day >= 0) ? "_add" : "_sub");
    $day = abs($day);
    $interval = (string)$day . " days";
    $date = date_create();
    $action($date, date_interval_create_from_date_string($interval));
    return $date;
  }

  /**
   * Add a relative day conditional
   * @param $from
   * @param int $to
   */
  public function addUpdated($from, $to = 0) {
    $from = $this->calcDay($from);
    $to = $this->calcDay($to);
    $this->conditional[] = "updated >= \"" . date_format($from, 'Y-m-d') . "\"";
    $this->conditional[] = "updated < \"" . date_format($to, 'Y-m-d') . "\"";
  }

  /**
   * Convert to string
   *
   * @return string
   */
  public function __toString() {
    return implode(" AND ", $this->conditional);
  }
}