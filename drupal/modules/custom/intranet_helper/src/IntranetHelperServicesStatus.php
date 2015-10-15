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

    $storage = \Drupal::entityManager()->getStorage('node');
    $query = \Drupal::entityQuery('node')
      ->condition('status', 1)
      ->condition('type', 'day_off')
      ->condition('field_employee.entity.name', 'admin');

    $nids = $query->execute();

    $days_off = $storage->loadMultiple($nids);
    $entity->field_dayoff = array();
    foreach ($days_off as $nid => $day) {
      $entity->field_dayoff->value = new SpesialData(array(
        "start_date" => strtotime($day->field_start_date->value),
        "end_date" => strtotime($day->field_end_date->value),
        "state" => $day->field_state->value,
      ));
    }

  }

}