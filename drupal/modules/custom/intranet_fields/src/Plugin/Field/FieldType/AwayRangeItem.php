<?php

/**
 * @file
 * Contains \Drupal\intranet_fields\Plugin\field\field_type\AwayRangeItem.
 */

namespace Drupal\intranet_fields\Plugin\Field\FieldType;

use Drupal\Core\Field\FieldItemBase;
use Drupal\Core\TypedData\DataDefinition;
use Drupal\Core\Field\FieldStorageDefinitionInterface;

/**
 * Plugin implementation of the 'away_range' field type.
 *
 * @FieldType(
 *   id = "away_range",
 *   label = @Translation("Away range"),
 *   description = @Translation("Stores time of away status."),
 *   category = @Translation("FFW"),
 *   default_widget = "away_range_default",
 *   default_formatter = "away_range_default",
 * )
 */
class AwayRangeItem extends FieldItemBase {
  public static function schema(FieldStorageDefinitionInterface $field_definition) {
    return array(
      'columns' => array(
        'away_from' => array(
          'description' => 'Away from',
          'type' => 'int',
        ),
        'away_to' => array(
          'description' => 'Away to',
          'type' => 'int',
        ),
        'away_time' => array(
          'description' => 'Away time',
          'type' => 'int',
        ),
      ),
    );
  }

  public static function propertyDefinitions(FieldStorageDefinitionInterface $field_definition) {
    $properties['away_from'] = DataDefinition::create('integer')
      ->setLabel(t('Away from'));
    $properties['away_to'] = DataDefinition::create('integer')
      ->setLabel(t('Away to'));
    $properties['away_time'] = DataDefinition::create('integer')
      ->setLabel(t('Away time'));

    return $properties;
  }

  /**
   * {@inheritdoc}
   */
  public function isEmpty() {
    $away_from = $this->get('away_from')->getValue();
    $away_to = $this->get('away_to')->getValue();
    $away_time = $this->get('away_time')->getValue();

    return ($away_from === NULL || $away_from === '') && ($away_to === NULL || $away_to === '') && ($away_time === NULL || $away_time === '');
  }
}
