<?php

namespace Drupal\intranet_helper\Controller;

use Drupal\Core\Controller\ControllerBase;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Drupal\user\Entity\User;
use Drupal\Core\Database\Database;
use Drupal\file\Entity\File;

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
    $response = new JsonResponse();

    if ($uid) {
      $account = User::load($uid);
      $new_field_value_index = count($account->field_user_check_in_and_out);

      // Set check-in value only if it is first user's check-in or
      // user has been already checked-out.
      if ($new_field_value_index == 0 || $account->field_user_check_in_and_out->get($new_field_value_index - 1)->check_out) {
        $account->field_user_check_in_and_out->set($new_field_value_index, array(
          'check_in' => $request->server->get('REQUEST_TIME'),
          'check_out' => NULL
        ));
        $account->save();
        $response->setData(array('status' => TRUE));
      }
      else {
        $response->setData(array('status' => FALSE));
      }
    }

    return $response;
  }

  public function checkOut(Request $request) {
    $user = \Drupal::service('current_user');
    $uid = $user->id();
    $response = new JsonResponse();

    if ($uid) {
      $account = User::load($uid);
      $field_value_index = count($account->field_user_check_in_and_out) - 1;

      // Set check-out time only if user has been checked-in and
      // hasn't been checked-out yet.
      if ($account->field_user_check_in_and_out->get($field_value_index)->check_in && !$account->field_user_check_in_and_out->get($field_value_index)->check_out) {
        $account->field_user_check_in_and_out->set($field_value_index, array(
          'check_in' => $account->field_user_check_in_and_out->get($field_value_index)->check_in,
          'check_out' => $request->server->get('REQUEST_TIME')
        ));
        $account->save();
        $response->setData(array('status' => TRUE));
      }
      else {
        $response->setData(array('status' => FALSE));
      }
    }

    return $response;
  }

  public function checkedIn(Request $request) {
    $users = array();

    $result = Database::getConnection()->select('user__field_user_check_in_and_out', 'c')
      ->fields('c')
      ->condition('field_user_check_in_and_out_check_in', strtotime(date('d F Y')), '>=')
      ->isNull('field_user_check_in_and_out_check_out')
      ->execute()
      ->fetchAll();

    foreach ($result as $key => $row) {
      $account = User::load($row->entity_id);

      // Return not checked out users.
      $users[$key]['field_first_name'] = $account->field_first_name->value;
      $users[$key]['field_last_name'] = $account->field_last_name->value;
      $users[$key]['field_image'] = file_create_url(File::load($account->user_picture->target_id)->uri->value);
      $users[$key]['time'] = $row->field_user_check_in_and_out_check_in;
    }

    return new JsonResponse($users);
  }

  public function checkState(Request $request) {
    $user = \Drupal::service('current_user');

    $uid = $user->id();
    $account = User::load($uid);

    $checked_in = Database::getConnection()->select('user__field_user_check_in_and_out', 'c')
      ->fields('c')
      ->condition('entity_id', $uid)
      ->condition('field_user_check_in_and_out_check_in', strtotime(date('d F Y')), '>=')
      ->isNull('field_user_check_in_and_out_check_out')
      ->execute()
      ->fetchAll();

    // If field "Jira not required" checked then '$jira' should be TRUE.
    if ((boolean) $account->field_jira_required->value) {
      $jira = (boolean) $account->field_jira_required->value;
    }
    else {
      // TODO: get '$jira' value from jira.
    }

    return new JsonResponse(array(
      'logged' => $uid ? TRUE : FALSE,
      'jira' => $jira,
      'field_image' => $account->user_picture->target_id ? file_create_url(File::load($account->user_picture->target_id)->uri->value) : '../images/anonymus.png',
      'uid' => $uid,
      'checked_in' => (boolean) $checked_in
    ));
  }

}
