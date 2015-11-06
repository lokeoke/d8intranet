<?php

namespace Drupal\intranet_fields\Plugin\Field\FieldWidget;

use Drupal;
use Drupal\Core\Field\FieldItemListInterface;
use Drupal\Core\Field\WidgetBase;
use Drupal\Core\Form\FormStateInterface;

/**
 * Plugin implementation of the 'away_range_default' widget.
 *
 * @FieldWidget(
 *   id = "away_range_default",
 *   label = @Translation("Away range widget"),
 *   field_types = {
 *     "away_range"
 *   }
 * )
 */
class AwayRangeDefaultWidget extends WidgetBase {
  public function formElement(FieldItemListInterface $items, $delta, array $element, array &$form, FormStateInterface $form_state) {
    $element['away_from'] = array(
      '#type' => 'textfield',
      '#title' => t('Away from'),
      '#default_value' => $items[$delta]->away_from,
    );

    $element['away_to'] = array(
      '#type' => 'textfield',
      '#title' => t('Away to'),
      '#default_value' => $items[$delta]->away_to,
    );

    $element['away_time'] = array(
      '#type' => 'textfield',
      '#title' => t('Away time'),
      '#default_value' => $items[$delta]->away_time,
    );

    return $element;
  }
}
