<?php

namespace Drupal\intranet_helper;


class IntranetHelperServicesStatus {

  public function setStatus($entity) {
    $statuses = array(
      'day_off',
      'sick',
      'business_trip',
      'remote_work',
      'vacation',
    );
    $rand = rand(0, count($statuses)-1);
    $entity->statuses = array($statuses[$rand]);

    // @TODO
    $days_off = $this->getNidsByType('day_off', $entity->name->value);

    $entity->field_dayoff = array();
    foreach ($days_off as $nid => $day) {
      // @TODO
      $entity->field_dayoff[] = new SpesialData(array(
        "start_date" => strtotime($day->field_start_date->value),
        "end_date" => strtotime($day->field_end_date->value),
        "state" => $day->field_state->value,
      ));
    }

    // @TODO
    $vacation = $this->getNidsByType('vacation', $entity->name->value);

    $entity->field_vacation = array();
    foreach ($vacation as $nid => $day) {
      // @TODO
      $entity->field_vacation[] = new SpesialData(array(
        "start_date" => strtotime($day->field_start_date->value),
        "end_date" => strtotime($day->field_end_date->value),
        "state" => $day->field_state->value,
      ));
    }


  }

  function getNidsByType($type, $user_name) {
    $storage = \Drupal::entityManager()->getStorage('node');
    $query = \Drupal::entityQuery('node')
      ->condition('status', 1)
      ->condition('type', $type)
      ->condition('field_employee.entity.name', $user_name);

    $nids = $query->execute();

    return $storage->loadMultiple($nids);
  }

}
