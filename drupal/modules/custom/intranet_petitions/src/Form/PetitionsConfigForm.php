<?php

namespace Drupal\intranet_petitions\Form;

use Drupal\Core\Form\ConfigFormBase;
use Drupal\Core\Form\FormStateInterface;

class PetitionsConfigForm extends ConfigFormBase {
  public function getFormId() {
    return 'intranet_petitions_config_form';
  }

  public function buildForm(array $form, FormStateInterface $form_state) {
    $config = $this->config('intranet_petitions.settings');

    $form['intranet_petitions_expired_days'] = [
      '#type' => 'number',
      '#title' => $this->t('Number of days petition life'),
      '#default_value' => $config->get('intranet_petitions_expired_days'),
    ];

    $form['intranet_petitions_likes_level'] = [
      '#type' => 'number',
      '#title' => $this->t('Number of likes to change petition status on "Scored"'),
      '#default_value' => $config->get('intranet_petitions_likes_level'),
    ];

    return parent::buildForm($form, $form_state);
  }

  public function submitForm(array &$form, FormStateInterface $form_state) {
    $config = $this->config('intranet_petitions.settings');
    $config->set('intranet_petitions_expired_days', $form_state->getValue('intranet_petitions_expired_days'));
    $config->set('intranet_petitions_likes_level', $form_state->getValue('intranet_petitions_likes_level'));
    $config->save();
    parent::submitForm($form, $form_state);
  }

  public function validateForm(array &$form, FormStateInterface $form_state) {
    if ($form_state->getValue('intranet_petitions_expired_days') <= 0) {
      $form_state->setErrorByName('intranet_petitions_expired_days', $this->t('Number of days should be > 0.'));
    }

    if ($form_state->getValue('intranet_petitions_likes_level') <= 0) {
      $form_state->setErrorByName('intranet_petitions_likes_level', $this->t('Number of likes level should be > 0.'));
    }
  }

  public function getEditableConfignames() {
    return array('intranet_petitions.settings');
  }
}
