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
    return new JsonResponse($this->intranetApi->checkUserIn($request, \Drupal::service('current_user')->id()));
  }

  public function checkOut(Request $request) {
    return new JsonResponse($this->intranetApi->checkUserOut($request, \Drupal::service('current_user')->id()));
  }

  public function checkedIn(Request $request) {
    return new JsonResponse($this->intranetApi->getCheckedInUsers());
  }

  public function checkState(Request $request) {
    return new JsonResponse($this->intranetApi->getUserState(\Drupal::service('current_user')->id()));
  }

}
