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
 *   id = "check_in_and_out_default",
 *   label = @Translation("Check in and out widget"),
 *   field_types = {
 *     "check_in_and_out"
 *   }
 * )
 */
class CheckInAndOutItemDefaultWidget extends WidgetBase {
  public function formElement(FieldItemListInterface $items, $delta, array $element, array &$form, FormStateInterface $form_state) {
    $element['check_in'] = array(
      '#type' => 'textfield',
      '#title' => t('Check in'),
      '#default_value' => $items[$delta]->check_in,
    );

    $element['check_out'] = array(
      '#type' => 'textfield',
      '#title' => t('Check out'),
      '#default_value' => $items[$delta]->check_out,
    );

    return $element;
  }
}
