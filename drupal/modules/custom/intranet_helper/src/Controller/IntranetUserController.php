<?php

namespace Drupal\intranet_helper\Controller;

use Drupal\Core\Controller\ControllerBase;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Drupal\user\Entity\User;

class IntranetUserController extends ControllerBase {

  protected $sessionManager;
  protected $userAuth;
  protected $userStorage;

  public function __construct() {
    $this->sessionManager = \Drupal::service('session_manager');
    $this->userAuth = \Drupal::getContainer()->get('user.auth');
    $this->userStorage = \Drupal::getContainer()->get('entity.manager')->getStorage('user');
  }

  public function view(Request $request) {
    $users = User::loadMultiple();


    foreach ($users as &$user) {
      $user = get_object_vars($user);

      /*$user['statuses'] = [
        'day_off' => 0,
        'sick' => 1,
        'business_trip' => 0,
        'remote_work' => 1,
        'vacation' => 0,
      ];*/
    }

    return new JsonResponse(['users' => $users]);
  }

  public function login(Request $request) {
    $name = $request->request->get('name');
    $password = trim($request->request->get('pass'));
    $uid = $this->userAuth->authenticate($name, $password);
    $account = $this->userStorage->load($uid);

    if ($account) {
      user_login_finalize($account);

      $response = new JsonResponse(array(
        'status' => 'ok',
        'data' => array(
          'sessionName' => $this->sessionManager->getName(),
          'sessionId' => $this->sessionManager->getId(),
        ),
      ));
    }
    else {
      $response = new JsonResponse(array(
        'status' => 'error',
        'data' => Null
      ));
    }

    return $response;

  }

}
