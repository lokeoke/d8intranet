<?php

namespace Drupal\intranet_helper;

use Drupal\Core\Database\Database;
use Drupal\user\Entity\User;
use Drupal\taxonomy\Entity\Term;
use Drupal\file\Entity\File;

class IntranetHelperServicesApi {

  const TEAM_VOCABULARY_NAME = 'teams';
  protected $termStorage;

  public function __construct() {
    $this->termStorage = \Drupal::entityManager()->getStorage('taxonomy_term');
  }

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

  public function getCheckedOutUsers() {
    $users = User::loadMultiple();
    $checked_in_users = $this->getCheckedInUsers();
    $result = array();

    // Unset checked in users.
    foreach ($checked_in_users as $user) {
      unset($users[$user['uid']]);
    }

    // Set up result array.
    foreach ($users as $key => $user) {
      $uid = $user->id();

      if (!in_array($uid, array(0, 1))) {
        $account = User::load($uid);
        $result[$key]['uid'] = $uid;
        $result[$key]['field_first_name'] = $account->field_first_name->value;
        $result[$key]['field_last_name'] = $account->field_last_name->value;
        $result[$key]['field_image'] = $account->user_picture->target_id ? file_create_url(File::load($account->user_picture->target_id)->uri->value) : NULL;
      }
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

    return reset($result);
  }

  public function checkUserIn($time, $uid) {
    $result = array('status' => FALSE);
    $account = User::load($uid);

    if ($account) {
      $new_field_value_index = count($account->field_user_check_in_and_out);

      // Set check-in value only if it is first user's check-in or
      // user has been already checked-out.
      if ($new_field_value_index == 0 || $account->field_user_check_in_and_out->get($new_field_value_index - 1)->check_out) {
        $account->field_user_check_in_and_out->set($new_field_value_index, array(
          'check_in' => $time,
          'check_out' => NULL
        ));
        $account->save();
        $result = array('status' => TRUE);
      }
    }

    return $result;
  }

  public function checkUserOut($time, $uid) {
    $result = array('status' => FALSE);
    $account = User::load($uid);

    if ($account) {
      $field_value_index = count($account->field_user_check_in_and_out) - 1;

      // Set check-out time only if user has been checked-in and
      // hasn't been checked-out yet.
      if ($account->field_user_check_in_and_out->get($field_value_index)->check_in && !$account->field_user_check_in_and_out->get($field_value_index)->check_out) {
        $account->field_user_check_in_and_out->set($field_value_index, array(
          'check_in' => $account->field_user_check_in_and_out->get($field_value_index)->check_in,
          'check_out' => $time
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
      // @TODO TEST MODE
      $need_update = TRUE;
      if($need_update == TRUE) {
        $logger = \Drupal::logger('state');
        $logger->info("Looks like %name is needed in force updating", array("%name" => $account->name->value));

        $jira_rest = \Drupal::service("intranet_jira.api_rest");
        // maybe a little hard for site
        $jira_rest->forceUpdate();

        $logger = \Drupal::logger('state');
        $logger->info("%name loading new value", array("%name" => $account->name->value));

        $storage = \Drupal::service("intranet_jira.storage");
        $res = $storage->getLoggedTime($account->name->value);
        $account->field_jira_worklog = $res;

      }
      $jira = (boolean) $account->field_jira_worklog->value;
    }

    return array(
      'logged' => $uid ? TRUE : FALSE,
      'jira' => $jira,
      'field_image' => $account->user_picture->target_id ? file_create_url(File::load($account->user_picture->target_id)->uri->value) : NULL,
      'uid' => $uid,
      'checked_in' => $checked_in
    );
  }

  public function getTeamUserIds($teamId) {
    $query = \Drupal::entityQuery('user')
      ->condition('field_team', $teamId);
    $uids = $query->execute();

    return array_values($uids);
  }

  public function getTeam($teamId) {
    $result = array();
    $team = Term::load($teamId);

    if ($team) {
      $result['team_title'] = $team->name->value;
      $result['team_type'] = $team->field_team_type->value;
      $result['weight'] = $team->getWeight();

      // Generate list of team members.
      foreach ($this->getTeamUserIds($teamId) as $user_key => $uid) {
        $account = User::load($uid);

        // Exclude admin and anonymous users.
        if (!in_array($account->id(), array(0, 1))) {
          $result['members'][$user_key]['uid'] = $account->id();
          $result['members'][$user_key]['field_first_name'] = $account->field_first_name->value;
          $result['members'][$user_key]['field_last_name'] = $account->field_last_name->value;
          $result['members'][$user_key]['field_job_title'] = $account->field_job_title->value;
          $result['members'][$user_key]['field_image'] = $account->user_picture->target_id ? file_create_url(File::load($account->user_picture->target_id)->uri->value) : NULL;
          $result['members'][$user_key]['field_position'] = Term::load($account->field_position->target_id)->name->value;
        }
      }
    }

    return $result;
  }

  public function getTeams() {
    $result = array();

    // Get all existing teams.
    $teams = $this->termStorage->loadTree(self::TEAM_VOCABULARY_NAME);

    // Generate teams.
    foreach ($teams as $key => $team) {
      $result[] = $this->getTeam($team->tid);
    }

    return $result;
  }

}
