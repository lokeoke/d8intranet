<?php

namespace Drupal\intranet_helper\Form;

use Drupal\migrate\Plugin\migrate\destination\Null;
use Drupal\user\Form\UserLoginForm;
use Drupal\Core\Flood\FloodInterface;
use Drupal\Core\Form\FormStateInterface;
use Drupal\user\UserAuthInterface;
use Drupal\user\UserStorageInterface;
use Drupal\Core\Render\RendererInterface;
use Symfony\Component\HttpFoundation\JsonResponse;

class FluffyLoginForm extends UserLoginForm {

  protected $sessionManager;

  public function __construct(FloodInterface $flood, UserStorageInterface $user_storage, UserAuthInterface $user_auth, RendererInterface $renderer) {
    parent::__construct($flood, $user_storage, $user_auth, $renderer);

    $this->sessionManager = \Drupal::service('session_manager');
  }

  public function buildForm(array $form, FormStateInterface $form_state) {
    $form = parent::buildForm($form, $form_state);

    // Skip default validation.
    unset($form['#validate']);

    return $form;
  }

  public function submitForm(array &$form, FormStateInterface $form_state) {
    $name = $form_state->getValue('name');
    $password = trim($form_state->getValue('pass'));
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

    $form_state->setResponse($response);
  }
}
