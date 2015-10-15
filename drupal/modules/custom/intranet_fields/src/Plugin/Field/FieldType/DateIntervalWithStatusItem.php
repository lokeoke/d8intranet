<?php

/**
 * @file
 * Contains \Drupal\intranet_fields\Plugin\field\field_type\CountryItem.
 */

namespace Drupal\intranet_fields\Plugin\Field\FieldType;

use Drupal\Core\Field\FieldItemBase;
use Drupal\Core\TypedData\DataDefinition;
use Drupal\Core\Field\FieldStorageDefinitionInterface;

/**
 * Plugin implementation of the 'date_interval_with_status' field type.
 *
 * @FieldType(
 *   id = "date_interval_with_status",
 *   label = @Translation("Date interval with status"),
 *   description = @Translation("Stores date interval with status."),
 *   category = @Translation("FFW"),
 *   default_widget = "date_interval_with_status_default",
 *   default_formatter = "boolean"
 * )
 */
class DateIntervalWithStatusItem extends FieldItemBase {
  public static function schema(FieldStorageDefinitionInterface $field_definition) {
    return array(
      'columns' => array(
        'start_date' => array(
          'description' => 'Start date',
          'type' => 'varchar',
          'length' => 20,
        ),
        'end_date' => array(
          'description' => 'End date',
          'type' => 'varchar',
          'length' => 20,
        ),
        'state' => array(
          'description' => 'State',
          'type' => 'varchar',
          'length' => 255,
        ),
      ),
    );
  }

  public static function propertyDefinitions(FieldStorageDefinitionInterface $field_definition) {
    $properties['start_date'] = DataDefinition::create('datetime_iso8601')
      ->setLabel(t('Start date'));
    $properties['end_date'] = DataDefinition::create('datetime_iso8601')
      ->setLabel(t('End date'));
    $properties['state'] = DataDefinition::create('string')
      ->setLabel(t('State'));

    return $properties;
  }

  /**
   * {@inheritdoc}
   */
  public function isEmpty() {
    $start_date = $this->get('start_date')->getValue();
    $end_date = $this->get('end_date')->getValue();
    $state = $this->get('state')->getValue();

    return ($start_date === NULL || $start_date === '') && ($end_date === NULL || $end_date === '') && ($state === NULL || $state === '');
  }
}
