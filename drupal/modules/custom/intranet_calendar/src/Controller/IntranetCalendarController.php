<?php

/**
 * @file
 * Contains \Drupal\intranet_controller\Controller\IntranetCalendarController
 */

namespace Drupal\intranet_controller\Controller;

use Drupal\Core\Controller\ControllerBase;
use Drupal\user\Entity\User;


class IntranetCalendarController extends ControllerBase {

  public function getUserDates(User $user, $type = 'vacation') {
    $vacation_dates = $user->field_vacation->getValue();
  }

}
