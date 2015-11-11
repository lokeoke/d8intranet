<?php

namespace Drupal\intranet_helper;

use Drupal\Core\Database\Database;
use Drupal\node\Entity\Node;
use Drupal\user\Entity\User;
use Drupal\taxonomy\Entity\Term;
use Drupal\file\Entity\File;

class IntranetHelperServicesApi {

  const TEAM_VOCABULARY_NAME = 'teams';
  private $termStorage;
  private $currentUser;
  private $intranetPetitionsConfig;

  public function __construct() {
    $this->termStorage = \Drupal::entityManager()->getStorage('taxonomy_term');
    $this->currentUser = \Drupal::service('current_user');
    $this->intranetPetitionsConfig = \Drupal::configFactory()->getEditable('intranet_petitions.settings');
  }

  public function getCheckedInUsers($todayUsers = TRUE) {
    $result = array();
    $checked_in_users = Database::getConnection()->select('user__field_user_check_in_and_out', 'c')
      ->fields('c')
      ->isNull('field_user_check_in_and_out_check_out');

    // Get checked in of current day. Otherwise get ALL checked in users.
    if ($todayUsers) {
      $checked_in_users = $checked_in_users->condition('field_user_check_in_and_out_check_in', strtotime(date('d F Y')), '>=');
    }

    $checked_in_users = $checked_in_users->execute()->fetchAll();

    foreach ($checked_in_users as $key => $user) {
      $account = User::load($user->entity_id);

      // Return not checked out users.
      $result[$key]['uid'] = $user->entity_id;
      $result[$key]['field_first_name'] = $account->field_first_name->value;
      $result[$key]['field_last_name'] = $account->field_last_name->value;
      $result[$key]['field_presence_status'] = $account->field_presence_status->value;
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

        // Exclude users with some statuses.
        // (vacation, sick, day_off, business_trip).
        if (!in_array($account->field_user_status->value, $this->getExcludedUserStatuses())) {
          $result[$key]['uid'] = $uid;
          $result[$key]['field_first_name'] = $account->field_first_name->value;
          $result[$key]['field_last_name'] = $account->field_last_name->value;
          $result[$key]['field_image'] = $account->user_picture->target_id ? file_create_url(File::load($account->user_picture->target_id)->uri->value) : NULL;
        }
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

  public function getExcludedUserStatuses() {
    return array(
      'vacation',
      'business_trip',
      'sick',
      'day_off',
    );
  }

  public function checkUserIn($time, $uid) {
    $result = array(
      'status' => FALSE,
      'message' => t('Invalid user.')->render(),
    );
    $account = User::load($uid);

    if ($account) {
      $user_status = $account->field_user_status->value;

      if (!empty($account->field_user_status) && in_array($user_status, $this->getExcludedUserStatuses())) {
        $result['status'] = FALSE;
        $result['message'] = t('You can not check-in because your status is "' . $user_status . '".')->render();
      }
      else {
        $result['message'] = t('You should to check-out first.')->render();
        $new_field_value_index = count($account->field_user_check_in_and_out);

        // Set check-in value only if it is first user's check-in or
        // user has been already checked-out.
        if ($new_field_value_index == 0 || $account->field_user_check_in_and_out->get($new_field_value_index - 1)->check_out) {
          $account->field_user_check_in_and_out->set($new_field_value_index, array(
            'check_in' => $time,
            'check_out' => NULL
          ));
          $account->save();
          $result['status'] = TRUE;
          $result['message'] = t('Successful check-in.')->render();
        }
      }
    }

    return $result;
  }

  public function checkUserOut($time, $uid) {
    $result = array(
      'status' => FALSE,
      'message' => t('Invalid user.')->render(),
    );
    $account = User::load($uid);

    if ($account) {
      $result['message'] = t('You should to check-in first.')->render();
      $field_value_index = count($account->field_user_check_in_and_out) - 1;

      // Set check-out time only if user has been checked-in and
      // hasn't been checked-out yet.
      if ($account->field_user_check_in_and_out->get($field_value_index)->check_in && !$account->field_user_check_in_and_out->get($field_value_index)->check_out) {
        $account->field_user_check_in_and_out->set($field_value_index, array(
          'check_in' => $account->field_user_check_in_and_out->get($field_value_index)->check_in,
          'check_out' => $time
        ));
        $account->save();
        $result['status'] = TRUE;
        $result['message'] = t('Successful check-out.')->render();
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
      'field_presence_status' => $account->field_presence_status->value,
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

  public function changePresenceStatus($uid) {
    $account = User::load($uid);

    if ($account->field_presence_status->value == 'available') {
      $account->field_presence_status->set(0, 'away');
    }
    else {
      $account->field_presence_status->set(0, 'available');
    }

    $account->save();

    return array(
      'status' => TRUE,
      'message' => t('Presence state has been changed on ' . $account->field_presence_status->value . ' successfully.')->render(),
    );
  }

  private function isUserAlreadyLikePetition($node, $user) {
    $result = FALSE;

    if (isset($node->field_likes)) {
      foreach ($node->field_likes->getValue() as $key => $userHash) {
        if ($userHash['value'] == $this->getUserHash($user)) {
          $result = $key;

          break;
        }
      }
    }

    return $result;
  }

  private function getUserHash($user) {
    return md5($user->id() . $user->getUserName() . $user->getEmail());
  }

  private function like(Node $node, $hash) {
    $node->field_likes->set(count($node->field_likes), $hash);
  }

  private function unLike(Node $node, $key) {
    $node->field_likes->set($key, NULL);
  }

  private function updateLikesStatus(Node $node) {
    $count = 0;
    $likesLevel = (int) $this->intranetPetitionsConfig->get('intranet_petitions_likes_level');

    foreach ($node->field_likes as $like) {
      if ($like->value) {
        $count++;
      }
    }

    if ($count >= $likesLevel) {
      // Voting is complete.
      $node->field_like_status->set(0, 'scored');
    }
    else {
      // Voting is not complete.
      $node->field_like_status->set(0, 'vote');
    }
  }

  public function likePetition($nid) {
    $result = array(
      'status' => FALSE,
      'message' => t('Petition has not been liked.')->render(),
    );
    $node = Node::load($nid);

    if ($node) {
      $nodeType = $node->getType();

      if ($nodeType == 'petition') {
        // Anonymous users can not like petitions.
        $account = User::load($this->currentUser->id());

        if (!$account->hasRole('anonymous')) {
          $key = $this->isUserAlreadyLikePetition($node, $this->currentUser);

          // If user did not like this petition yet.
          if ($key === FALSE) {
            // Like this petition.
            $this->like($node, $this->getUserHash($this->currentUser));

            $result['status'] = TRUE;
            $result['message'] = t('Petition has been liked successfully.')->render();
          }
          else {
            // Unlike this petition.
            $this->unLike($node, $key);

            $result['status'] = TRUE;
            $result['message'] = t('Petition has been unliked successfully.')->render();
          }

          $this->updateLikesStatus($node);
          $node->save();
        }
        else {
          $result['message'] = t('Anonymous user can not like petitions.')->render();
        }
      }
      else {
        $result['message'] = t('You can not like ' . $nodeType . '.')->render();
      }
    }
    else {
      $result['message'] = t('Petition with nid = ' . $nid . ' does not exists.')->render();
    }

    return $result;
  }

  public function createPetition($requestContent, $requestHeaders) {
    $result = array(
      'status' => TRUE,
      'message' => t('Petition has been saved.')->render(),
    );

    if (0 === strpos($requestHeaders->get('Content-Type'), 'application/json')) {
      $data = json_decode($requestContent, true);

      if (!empty($data['title'])) {
        $node = Node::create(array(
          'type' => 'petition',
          'title' => $data['title'],
          'uid' => 0,
          'status' => 1,
          'body' => array(
            array(
              'value' => !empty($data['description']) ? $data['description'] : '',
            ),
          ),
        ));

        $node->save();
      }
      else {
        $result['status'] = FALSE;
        $result['message'] = t('Petition title can not be empty.')->render();
      }
    }
    else {
      $result['status'] = FALSE;
      $result['message'] = t('Content-type must be application/json.')->render();
    }

    return $result;
  }

}
