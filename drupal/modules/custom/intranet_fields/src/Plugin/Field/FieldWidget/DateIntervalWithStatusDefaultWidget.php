<?php

namespace Drupal\intranet_fields\Plugin\Field\FieldWidget;

use Drupal;
use Drupal\Core\Field\FieldItemListInterface;
use Drupal\Core\Field\WidgetBase;
use Drupal\Core\Form\FormStateInterface;

/**
 * Plugin implementation of the 'statuses_default' widget.
 *
 * @FieldWidget(
 *   id = "date_interval_with_status_default",
 *   label = @Translation("Date interval with status widget"),
 *   field_types = {
 *     "date_interval_with_status"
 *   }
 * )
 */
class DateIntervalWithStatusDefaultWidget extends WidgetBase {

  public static function defaultSettings() {
    return array(
       'always_paid' => FALSE,
       'single_day_shortcut' => FALSE,
    );
  }

  public function settingsForm(array $form, FormStateInterface $form_state) {
    $element['always_paid'] = array(
      '#type' => 'checkbox',
      '#title' => t('Always paid'),
      '#default_value' => $this->getSetting('always_paid'),
    );

    $element['single_day_shortcut'] = array(
      '#type' => 'checkbox',
      '#title' => t('Single day shortcut'),
      '#default_value' => $this->getSetting('single_day_shortcut'),
    );

    return $element;
  }

  public function settingsSummary() {
    $summary = array();

    $always_paid = $this->getSetting('always_paid');
    $single_day_shortcut = $this->getSetting('single_day_shortcut');
    $summary[] = t('Always paid: @always_paid', array('@always_paid' => ($always_paid ? t('Yes') : t('No'))));
    $summary[] = t('Single day shortcut: @single_day', array('@single_day' => ($single_day_shortcut ? t('Yes') : t('No'))));

    return $summary;
  }

  public function formElement(FieldItemListInterface $items, $delta, array $element, array &$form, FormStateInterface $form_state) {
    $single_day_shortcut = $this->getSetting('single_day_shortcut');

    if ($single_day_shortcut) {
      $element['single_day_shortcut'] = array(
        '#type' => 'checkbox',
        '#title' => t('Single day'),
        '#default_value' => !empty($items[$delta]->start_date) && $items[$delta]->start_date == $items[$delta]->end_date,
      );
    }

    $element['start_date'] = array(
      '#type' => 'date',
      '#title' => t('Start date'),
      '#default_value' => $items[$delta]->start_date,
      '#date_increment' => 1,
      '#date_timezone' => drupal_get_user_timezone(),
    );

    $single_date_shortcut_name = $items->getName() . "[$delta]" . "[single_day_shortcut]";

    $element['end_date'] = array(
      '#type' => 'date',
      '#title' => t('End date'),
      '#default_value' => $items[$delta]->end_date,
      '#date_increment' => 1,
      '#date_timezone' => drupal_get_user_timezone(),
      '#states' => array(
        'invisible' => array(
          ":input[name='$single_date_shortcut_name']" => array('checked' => TRUE),
        ),
      ),
    );

    $always_paid = $this->getSetting('always_paid');

    $element['state'] = array(
      '#type' => $always_paid ? 'value' : 'select',
      '#default_value' => $always_paid ? 'paid-leave' : $items[$delta]->state,
      '#title' => t('State'),
      '#options' => array(
        'paid-leave' => 'paid-leave',
        'unpaid-leave' => 'unpaid-leave'
      ),
    );

    return $element;
  }

  public function massageFormValues(array $values, array $form, FormStateInterface $form_state) {
    foreach ($values as &$value) {
      if ($value['single_day_shortcut']) {
        $value['end_date'] = $value['start_date'];
      }
    }
    return $values;
  }

}
