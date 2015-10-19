<?php
/**
 * Created by PhpStorm.
 * User: spheresh
 * Date: 10/17/15
 * Time: 2:54 PM
 */

namespace Drupal\intranet_jira;


interface IntranetJiraStorageTaskInterface {

  public static function storeWorkLog($workinglog);

  public static function updateTime($task);

  public static function setTime($task);

  public static function getTime(IntranetJiraProjectTask $task);

}