<?php

namespace Drupal\intranet_helper\Controller;

use Drupal\Core\Controller\ControllerBase;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

class IntranetPetitionController extends ControllerBase {

  protected $intranetApi;

  public function __construct() {
    $this->intranetApi = \Drupal::service('intranet_helper.services.api');
  }

  public function likePetition($nid) {
    return new JsonResponse($this->intranetApi->likePetition($nid));
  }

  public function createPetition(Request $request) {
    $title = $request->request->get('title');
    $description = $request->request->get('description');

    return new JsonResponse($this->intranetApi->createPetition($title, $description));
  }

}
