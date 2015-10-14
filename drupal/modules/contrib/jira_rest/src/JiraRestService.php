<?php

/**
 * @file
 * Contains Drupal\jira_rest\JiraRestService.
 */

namespace Drupal\jira_rest;

class JiraRestService {
  
  protected $demo_value;
  
  public function __construct() {
    $this->demo_value = 'just for testing';
  }
  
  public function getDemoValue() {
    return $this->demo_value;
  }


  
}
