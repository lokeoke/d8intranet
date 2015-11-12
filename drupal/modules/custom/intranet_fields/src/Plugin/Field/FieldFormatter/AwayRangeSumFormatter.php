<?php

/**
 * @file
 * Contains \Drupal\intranet_fields\Plugin\Field\FieldFormatter\AwayRangeSumFormatter.
 */

namespace Drupal\intranet_fields\Plugin\Field\FieldFormatter;

use Drupal\Core\Field\FormatterBase;
use Drupal\Core\Field\FieldItemListInterface;

/**
 * Plugin implementation of the 'away_range_default' formatter.
 *
 * @FieldFormatter(
 *   id = "away_range_sum",
 *   label = @Translation("Away sum"),
 *   field_types = {
 *     "away_range",
 *   },
 * )
 */
class AwayRangeSumFormatter extends FormatterBase {

  /**
   * {@inheritdoc}
   */
  public function viewElements(FieldItemListInterface $items, $langcode) {
    $elements = array();
    $sum = 0;

    foreach ($items as $delta => $item) {
      $sum += !empty($item->away_time) ? $item->away_time : 0;

      if ($delta == count($items) - 1) {
        $elements[]['#markup'] = $sum . 's';
      }
    }

    return $elements;
  }

}
