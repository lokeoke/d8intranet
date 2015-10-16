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

  public function checkIn(Request $request) {
    $user = \Drupal::service('current_user');
    $uid = $user->id();
    $response = new JsonResponse(array('status' => (boolean) $uid));

    if ($uid) {
      $account = User::load($uid);
      $account->field_user_check_in->set(count($account->field_user_check_in->getValue()), $request->server->get('REQUEST_TIME'));
      $account->save();
    }

    return $response;
  }

}
