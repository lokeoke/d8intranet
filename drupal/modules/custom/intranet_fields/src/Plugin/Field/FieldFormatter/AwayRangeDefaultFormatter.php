<?php

/**
 * @file
 * Contains \Drupal\intranet_fields\Plugin\Field\FieldFormatter\AwayRangeDefaultFormatter.
 */

namespace Drupal\intranet_fields\Plugin\Field\FieldFormatter;

use Drupal\Core\Field\FormatterBase;
use Drupal\Core\Field\FieldItemListInterface;

/**
 * Plugin implementation of the 'away_range_default' formatter.
 *
 * @FieldFormatter(
 *   id = "away_range_default",
 *   label = @Translation("Away time"),
 *   field_types = {
 *     "away_range",
 *   },
 * )
 */
class AwayRangeDefaultFormatter extends FormatterBase {

  /**
   * {@inheritdoc}
   */
  public function viewElements(FieldItemListInterface $items, $langcode) {
    $elements = array();

    // The ProcessedText element already handles cache context & tag bubbling.
    // @see \Drupal\filter\Element\ProcessedText::preRenderText()
    foreach ($items as $delta => $item) {
      $elements[$delta] = array(
        '#markup' => !empty($item->away_time) ? $item->away_time . 's' : '?',
      );
    }

    return $elements;
  }

}
