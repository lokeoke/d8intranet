<?php

/**
 * @file
 * Contains \Drupal\intranet_helper\Normalizer\TeamNormalizer.
 */

namespace Drupal\intranet_helper\Normalizer;

use Drupal\serialization\Normalizer\EntityReferenceFieldItemNormalizer;

/**
 * Converts the Drupal entity reference item object to HAL array structure.
 */
class TermReferenceNormalizer extends EntityReferenceFieldItemNormalizer {

  public function normalize($field_item, $format = NULL, array $context = array()) {
    $values = parent::normalize($field_item, $format, $context);

    if (($entity = $field_item->get('entity')->getValue()) && ($entity->url('canonical'))) {
      $values['target_id'] = $entity->id();

      if (!is_null($entity->name) && $entity->name->value) {
        $values['name'] = $entity->name->value;
      }
    }

    return $values;
  }

}
