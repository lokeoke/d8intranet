<?php

namespace Drupal\intranet_helper;

use Drupal\Core\Database\Database;
use Symfony\Component\HttpFoundation\Request;
use Drupal\user\Entity\User;
use Drupal\file\Entity\File;

class IntranetHelperServicesApi {

  public function getCheckedInUsers() {
    $result = array();
    $checked_in_users = Database::getConnection()->select('user__field_user_check_in_and_out', 'c')
      ->fields('c')
      ->condition('field_user_check_in_and_out_check_in', strtotime(date('d F Y')), '>=')
      ->isNull('field_user_check_in_and_out_check_out')
      ->execute()
      ->fetchAll();

    foreach ($checked_in_users as $key => $user) {
      $account = User::load($user->entity_id);

      // Return not checked out users.
      $result[$key]['uid'] = $user->entity_id;
      $result[$key]['field_first_name'] = $account->field_first_name->value;
      $result[$key]['field_last_name'] = $account->field_last_name->value;
      $result[$key]['field_image'] = $account->user_picture->target_id ? file_create_url(File::load($account->user_picture->target_id)->uri->value) : NULL;
      $result[$key]['time'] = $user->field_user_check_in_and_out_check_in;
    }

    return $result;
  }

  public function isUserCheckedIn($uid) {
    $result = Database::getConnection()->select('user__field_user_check_in_and_out', 'c')
      ->fields('c')
      ->condition('entity_id', $uid)
      ->condition('field_user_check_in_and_out_check_in', strtotime(date('d F Y')), '>=')
      ->isNull('field_user_check_in_and_out_check_out')
      ->execute()
      ->fetchAll();

    return (boolean) $result;
  }

  public function checkUserIn(Request $request, $uid) {
    $result = array('status' => FALSE);
    $account = User::load($uid);

    if ($account) {
      $new_field_value_index = count($account->field_user_check_in_and_out);

      // Set check-in value only if it is first user's check-in or
      // user has been already checked-out.
      if ($new_field_value_index == 0 || $account->field_user_check_in_and_out->get($new_field_value_index - 1)->check_out) {
        $account->field_user_check_in_and_out->set($new_field_value_index, array(
          'check_in' => $request->server->get('REQUEST_TIME'),
          'check_out' => NULL
        ));
        $account->save();
        $result = array('status' => TRUE);
      }
    }

    return $result;
  }

  public function checkUserOut(Request $request, $uid) {
    $result = array('status' => FALSE);
    $account = User::load($uid);

    if ($account) {
      $field_value_index = count($account->field_user_check_in_and_out) - 1;

      // Set check-out time only if user has been checked-in and
      // hasn't been checked-out yet.
      if ($account->field_user_check_in_and_out->get($field_value_index)->check_in && !$account->field_user_check_in_and_out->get($field_value_index)->check_out) {
        $account->field_user_check_in_and_out->set($field_value_index, array(
          'check_in' => $account->field_user_check_in_and_out->get($field_value_index)->check_in,
          'check_out' => $request->server->get('REQUEST_TIME')
        ));
        $account->save();
        $result = array('status' => TRUE);
      }
    }

    return $result;
  }

  public function getUserState($uid) {
    $account = User::load($uid);
    $checked_in = $this->isUserCheckedIn($uid);

    // If field "Jira not required" checked then '$jira' should be TRUE.
    if ((boolean) $account->field_jira_required->value) {
      $jira = (boolean) $account->field_jira_required->value;
    }
    else {
      // TODO: get '$jira' value from jira.
    }

    return array(
      'logged' => $uid ? TRUE : FALSE,
      'jira' => $jira,
      'field_image' => $account->user_picture->target_id ? file_create_url(File::load($account->user_picture->target_id)->uri->value) : NULL,
      'uid' => $uid,
      'checked_in' => $checked_in
    );
  }

}
