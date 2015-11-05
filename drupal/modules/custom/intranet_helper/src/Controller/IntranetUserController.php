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

}
