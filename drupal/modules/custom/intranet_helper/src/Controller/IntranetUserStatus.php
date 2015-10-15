<?php
/**
 * Created by PhpStorm.
 * User: spheresh
 * Date: 10/15/15
 * Time: 6:58 PM
 */

namespace Drupal\intranet_helper\Controller;


use Drupal\Core\Controller\ControllerBase;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

class IntranetUserStatus extends ControllerBase {

  public function status(Request $request) {
    $user = \Drupal::service("current_user");

    $uid = $user->id();
    return new JsonResponse(array(
      "logged" => $uid ? TRUE : FALSE,
      "jira" => false,
      "field_image" => $user->field_image->value ? $user->field_image->value : "../images/anonymus.png",
      "uid" => $uid
    ));
  }

}