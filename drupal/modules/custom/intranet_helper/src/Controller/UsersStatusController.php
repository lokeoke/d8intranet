<?php

namespace Drupal\intranet_helper\Controller;

use Drupal\Core\Controller\ControllerBase;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Drupal\user\Entity\User;

class UsersStatusController extends ControllerBase {

  public function view(Request $request) {
    $users = User::loadMultiple();
var_dump($users);
    foreach ($users as &$user) {
      $user->status = [
        'day_off' => 0,
        'sick' => 1,
        'business_trip' => 0,
        'remote_work' => 1,
        'vacation' => 0,
      ];
    }

    return new JsonResponse(['users' => $users]);
  }

}
