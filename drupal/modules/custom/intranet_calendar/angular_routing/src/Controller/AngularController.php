<?php

/**
 * @file
 * Contains \Drupal\agnular_routing\Controller\AngularController
 */

namespace Drupal\agnular_routing\Controller;

use Drupal\Core\Controller\ControllerBase;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

class AngularController extends ControllerBase {

  /**
   * Builds absolute or relative URL base on route and params.
   *
   * @param string $route
   *   Router machine name.
   * @param Request $request
   *
   * @return \Symfony\Component\HttpFoundation\Response|static
   */
  public function generateDrupalUrl($route, Request $request) {
    $params = $request->get('route_params');

    $url = $this->getUrlGenerator()->generateFromRoute($route, $params);

    return JsonResponse::create(array('url' => $url));
  }

}
