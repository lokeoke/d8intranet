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
      '#title' => $this->t('Number of days to review the petition'),
      '#default_value' => $config->get('intranet_petitions_expired_days'),
      '#element_validate' => array('element_validate_integer_positive'),
    ];
    return parent::buildForm($form, $form_state);
  }

  public function submitForm(array &$form, FormStateInterface $form_state) {
    $config = $this->config('intranet_petitions.settings');
    $config->set('intranet_petitions_expired_days', $form_state->getValue('intranet_petitions_expired_days'));
    $config->save();
    parent::submitForm($form, $form_state);
  }

  public function validateForm(array &$form, FormStateInterface $form_state) {
    if ($form_state->getValue('intranet_petitions_expired_days') <= 0) {
      $form_state->setErrorByName('intranet_petitions_expired_days', $this->t('Number of days should be > 0.'));
    }
  }

  public function getEditableConfignames() {
    return array('intranet_petitions.settings');
  }
}
