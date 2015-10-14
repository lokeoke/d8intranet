<?php
/**
 * @file
 * Contains \Drupal\jira_rest\Form\JiraRestSettingsForm.
 */



namespace Drupal\jira_rest\Form;


use Drupal\Core\Form\ConfigFormBase;
use Drupal\Core\Form\FormStateInterface;

use Symfony\Component\DependencyInjection\ContainerInterface;

class JiraRestSettingsForm extends ConfigFormBase {

  /**
   * {@inheritdoc}
   */
  public function getFormID() {
    return 'jira_rest.settings.form';
  }

  /**
   * {@inheritdoc}
   */
  protected function getEditableConfigNames() {
    return ['jira_rest.settings'];
  }

  /**
   * {@inheritdoc}.
   */
  public function buildForm(array $form, FormStateInterface $form_state) {

    $form = parent::buildForm($form, $form_state);

    $config = $this->config('jira_rest.settings');


    $form['instanceurl'] = array(
      '#type' => 'textfield',
      '#title' => t('URL of the JIRA instance'),
      '#default_value' => $config->get('jira_rest.instanceurl'),
      '#description' => t("Enter the URL of your JIRA instance (e.g. https://yourjira.com:8443)"),
      '#required' => TRUE,
    );

    $form['username'] = array(
      '#type' => 'textfield',
      '#title' => t('Username of the default user to connect to JIRA'),
      '#default_value' => $config->get('jira_rest.username'),
      '#description' => t("Enter the username used as default to connect to you JIRA instance (e.g. admin)"),
    );

    $form['password'] = array(
      '#type' => 'password',
      '#title' => t('Password of the default user to connect to JIRA'),
      '#default_value' => $config->get('jira_rest.password'),
      '#description' => t("Enter the password of the default user to connect to JIRA"),
    );

    $form['close_issue_transition_id'] = array(
      '#type' => 'textfield',
      '#title' => t('the default transition ID to close an issue'),
      '#default_value' =>$config->get('jira_rest.close_issue_transition_id'),
      '#size' => 4,
      '#description' => t("Enter the default transition ID to close an issue with jira_rest_closeissuefixed()"),
      '#required' => TRUE,
    );

    $form['resolve_issue_transition_id'] = array(
      '#type' => 'textfield',
      '#title' => t('default transition ID to resolve an issue'),
      '#default_value' =>$config->get('jira_rest.resolve_issue_transition_id'),
      '#size' => 4,
      '#description' => t("Enter the default transition ID to resolve an issue with jira_rest_resolveissuefixed()"),
      '#required' => TRUE,
    );



    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function validateForm(array &$form,  FormStateInterface $form_state) {

    $config = $this->config('jira_rest.settings');

    $formValues = $form_state->getValues();




    $jira_url = $formValues['instanceurl'];
    if ((strpos(strrev($jira_url), strrev('/')) === 0)) {
      $form_state->setErrorByName('instanceurl', $this->t('URL must not end with "/"'));
    }

    if(!is_numeric($formValues['close_issue_transition_id'])) {
      $form_state->setErrorByName('close_issue_transition_id', $this->t('Transition id must be a numeric value'));
    }

    if(!is_numeric($formValues['resolve_issue_transition_id'])) {
      $form_state->setErrorByName('resolve_issue_transition_id', $this->t('Transition id must be a numeric value'));
    }

    //CHECK may not be needed for d8, unsets userdata if username left empty
    if (empty($formValues['username'])) {
      unset($formValues['username']);
      $config->clear('username');
      unset($formValues['password']);
      $config->clear('password');
    }


  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {

    $config = $this->config('jira_rest.settings');
    $formValues = $form_state->getValues();

    $config->set('jira_rest.instanceurl', $formValues['instanceurl']);
    $config->set('jira_rest.username', $formValues['username']);
    $config->set('jira_rest.password', $formValues['password']);
    $config->set('jira_rest.close_issue_transition_id', $formValues['close_issue_transition_id']);
    $config->set('jira_rest.resolve_issue_transition_id', $formValues['resolve_issue_transition_id']);
    $config->save();

    return parent::submitForm($form, $form_state);
  }

}

