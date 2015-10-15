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
  public function formElement(FieldItemListInterface $items, $delta, array $element, array &$form, FormStateInterface $form_state) {
    $element['start_date'] = array(
      '#type' => 'date',
      '#title' => t('Start date'),
      '#default_value' => !empty($items[$delta]->start_date),
      '#date_increment' => 1,
      '#date_timezone' => drupal_get_user_timezone(),
    );

    $element['end_date'] = array(
      '#type' => 'date',
      '#title' => t('End date'),
      '#default_value' => !empty($items[$delta]->end_date),
      '#date_increment' => 1,
      '#date_timezone' => drupal_get_user_timezone(),
    );

    $element['state'] = array(
      '#type' => 'select',
      '#title' => t('State'),
      '#options' => array('paid-leave', 'unpaid-leave'),
      '#default_value' => !empty($items[$delta]->state),
    );

    return $element;
  }
}
