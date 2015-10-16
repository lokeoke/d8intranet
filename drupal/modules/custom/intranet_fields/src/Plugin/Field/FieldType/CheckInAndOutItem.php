<?php

/**
 * @file
 * Contains \Drupal\intranet_fields\Plugin\field\field_type\CheckInAndOutItem.
 */

namespace Drupal\intranet_fields\Plugin\Field\FieldType;

use Drupal\Core\Field\FieldItemBase;
use Drupal\Core\TypedData\DataDefinition;
use Drupal\Core\Field\FieldStorageDefinitionInterface;

/**
 * Plugin implementation of the 'check_in_and_out' field type.
 *
 * @FieldType(
 *   id = "check_in_and_out",
 *   label = @Translation("Check in and out"),
 *   description = @Translation("Stores check in and out time."),
 *   category = @Translation("FFW"),
 *   default_widget = "check_in_and_out_default",
 * )
 */
class CheckInAndOutItem extends FieldItemBase {
  public static function schema(FieldStorageDefinitionInterface $field_definition) {
    return array(
      'columns' => array(
        'check_in' => array(
          'description' => 'Check in',
          'type' => 'int',
        ),
        'check_out' => array(
          'description' => 'Check put',
          'type' => 'int',
        ),
      ),
    );
  }

  public static function propertyDefinitions(FieldStorageDefinitionInterface $field_definition) {
    $properties['check_in'] = DataDefinition::create('integer')
      ->setLabel(t('Check in'));
    $properties['check_out'] = DataDefinition::create('integer')
      ->setLabel(t('Check put'));

    return $properties;
  }

  /**
   * {@inheritdoc}
   */
  public function isEmpty() {
    $check_in = $this->get('check_in')->getValue();
    $check_out = $this->get('check_out')->getValue();

    return ($check_in === NULL || $check_in === '') && ($check_out === NULL || $check_out === '');
  }
}
