<?php

namespace Drupal\intranet_helper\Controller;

use Drupal\Core\Controller\ControllerBase;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

class IntranetUserController extends ControllerBase {

  protected $intranetApi;

  public function __construct() {
    $this->intranetApi = \Drupal::service('intranet_helper.services.api');
  }

  public function checkIn(Request $request) {
    return new JsonResponse($this->intranetApi->checkUserIn($request->server->get('REQUEST_TIME'), \Drupal::service('current_user')->id()));
  }

  public function checkOut(Request $request) {
    return new JsonResponse($this->intranetApi->checkUserOut($request->server->get('REQUEST_TIME'), \Drupal::service('current_user')->id()));
  }

  public function checkedIn(Request $request) {
    return new JsonResponse($this->intranetApi->getCheckedInUsers());
  }

  public function checkedOut(Request $request) {
    return new JsonResponse($this->intranetApi->getCheckedOutUsers());
  }

  public function checkState(Request $request) {
    return new JsonResponse($this->intranetApi->getUserState(\Drupal::service('current_user')->id()));
  }

  public function teams(Request $request) {
    return new JsonResponse($this->intranetApi->getTeams());
  }

  public function changePresenceStatus() {
    return new JsonResponse($this->intranetApi->changePresenceStatus(\Drupal::service('current_user')->id()));
  }

  public function userYears() {
    $stats = ['vacation', 'dayoff', 'sick', 'duty_journey', 'remote_work', 'work_off'];

    $db = \Drupal::database();

    $min = $max = date("Y");

    foreach ($stats as $stat) {
      $query = $db->select('user__field_user_' . $stat);
      $query->addExpression('MIN(YEAR(field_user_' . $stat . '_start_date))');
      $query->addExpression('MAX(YEAR(field_user_' . $stat . '_end_date))');
      $stat_min = $query->execute()->fetchField(0);
      $stat_max = $query->execute()->fetchField(1);

      if ($stat_min < $min) {
        $min = $stat_min;
      }

      if ($stat_max > $max) {
        $max = $stat_max;
      }
    }

    return new JsonResponse([$min, $max]);
  }

}
