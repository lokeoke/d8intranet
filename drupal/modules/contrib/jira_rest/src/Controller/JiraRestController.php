<?php
/**
 * @file
 * Contains \Drupal\jira_rest\Controller\JiraRestController.
 * providing methods for creating, editing,
 * searching JIRA issues out of Drupal via REST.
 */

namespace Drupal\jira_rest\Controller;


use Drupal\jira_rest\JiraRestException;
use Drupal\Core\Controller\ControllerBase;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Jira_RestController.
 */
class JiraRestController extends ControllerBase {

  protected $jiraRestService;

  /**
   * Class constructor.
   */
  public function __construct($jiraRestService) {
    $this->jiraRestService = $jiraRestService;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('jira_rest.jira_rest_service')
    );
  }


  /**
   * just for testing.
   */
  public function test() {

    $result = $this->jira_rest_searchissue('');

    $issue = reset($result->issues);
    return array(
      '#markup' => t('Controller action test sucessfull, found a jira issue, with key:' . $issue->key),
    );


  }

  /**
   * Helper function for getting curl resource.
   *
   * @param $options
   * @param $url
   * @return resource
   */
  public function jira_rest_get_curl_resource($options, $url) {

    $this->jira_rest_default_curl_options($options);

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $options['jira_url'] . $url);
    curl_setopt($ch, CURLOPT_USERPWD, $options['username'] . ':' . $options['password']);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, $options['curl_returntransfer']);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, $options['curl_ssl_verifyhost']);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, $options['curl_ssl_verifypeer']);
    return $ch;
  }

  /**
   * Default credentials for Jira user/server and options for curl.
   * Different options can be set for a REST method by passing an array containing the options to change.
   *
   * @param $options array
   *   reference to option array of the method call, adds default values if not set:
   *     username string jira user name
   *     password string jira user password
   *     jira_url string jira server url including full path to REST API
   *     curl_returntransfer int
   *     curl_ssl_verifyhost int
   *     curl_ssl_verifypeer boolean
   */
  public function jira_rest_default_curl_options(&$options) {

    $config = $this->config('jira_rest.settings');

    $options += array(
      'username' => $config->get('jira_rest.username'),
      'password' => $config->get('jira_rest.password'),
      'jira_url' => $config->get('jira_rest.instanceurl') . '/rest/api/latest',
      'curl_returntransfer' => 1,
      'curl_ssl_verifyhost' => 0,
      'curl_ssl_verifypeer' => false,
    );
  }

  /**
   * Decodes cURL response.
   *
   * @param $ch
   * @param array $options
   * @return mixed
   * @throws JiraRestException
   */
  public function jira_rest_curl_execute($ch, $options = array()) {

    // default options
    $options += array(
      'json_decode' => true,
    );

    $response_raw = curl_exec($ch);
    if($response_raw === FALSE) {
      throw new JiraRestException('Jira offline or no response for this action from Jira.');
    }

    if($options['json_decode']) {
      $response = json_decode($response_raw);
    }
    else {
      $response = $response_raw;
    }

    $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    switch ($http_code) {
      case '401':
        throw new JiraRestException(t('Jira: 401 Unauthorized!'));
        break;

      case '403':
        throw new JiraRestException(t('Jira: 403 Access Denied!'));
        break;

      default:
        if ($http_code >= 400) {
          throw new JiraRestException(t('Jira: HTTP status code %code , response was %response', array('%code' => $http_code, '%response' => $response_raw)));
        }
        break;
    }

    return $response;
  }


  /**
   * Searches JIRA issues filtered by the given JQL string.
   *
   * Usage example returning issues not resolved/closed from PROJECTNAME:
   *
   * $foundissues = jira_rest_searchissue("project=PROJECTNAME&status!=RESOLVED&status!=CLOSED");
   *
   * @param string $jql_string
   *   the JQLString (filtering the searched issues)
   * @param array $options
   *   overwrites default values of specified options (see official JIRA REST API documentation for further information):
   *     maxResults int maximum number of issues returned
   *     startAt int index of the first issue to return (0-based)
   *     fields string comma-separated list of fields that should be returned
   *     expand string comma-separated list of parameters to extend
   *     validateQuery boolean whether to validate the JQL query
   *     @see jira_rest_default_curl_options
   *
   * @return object
   *   the issues found, can be accessed via array->issues
   *   some examples for getting information from a single issue:
   *   $issuekey = $issue->key;
   *   $parentkey = $issue->fields->parent->key;
   *   $customfielddata = $issue->fields->customfield_10404;
   *
   * @throws JiraRestException
   */
  public function jira_rest_searchissue($jql_string, $options = array()) {
    // default options
    $options += array(
      'maxResults' => 999,
      'startAt' => 0,
      'fields' => '',
      'expand' => '',
      'validateQuery' => true,
    );

    // build rest query
    $jira_query = '/search?jql=' . urlencode($jql_string) . '&maxResults=' . $options['maxResults'];
    $jira_query .= '&startAt=' . $options['startAt'] . '&validateQuery=' . $options['validateQuery'];

    if(!empty($options['fields'])) {
      $jira_query .= '&fields=' . urlencode($options['fields']);
    }

    if(!empty($options['expand'])) {
      $jira_query .= '&expand=' . urlencode($options['expand']);
    }

    $ch = $this->jira_rest_get_curl_resource($options, $jira_query);
    return $this->jira_rest_curl_execute($ch);
  }


  /**
   * Searches JIRA issues filtered by the given JQL string.
   * This function returns all query results and performs multiple REST calls
   * if the number of results exceeds the given maxResults size.
   * In contrast to jira_rest_searchissue() an array of all issues is returned!
   *
   * Usage example returning issues not resolved/closed from PROJECTNAME:
   *
   * $foundissues = jira_rest_searchissue_all_issues("project=PROJECTNAME&status!=RESOLVED&status!=CLOSED");
   *
   * @param string $jql_string
   *   the JQLString (filtering the searched issues)
   * @param array $options
   *   overwrites default values of specified options (see official JIRA REST API documentation for further information):
   *     startAt int index of the first issue to return (0-based)
   *     @see jira_rest_searchissue
   *
   * @return array
   *   the issues found, can be accessed directly from the array
   *
   *   some examples for getting information from a single issue:
   *   $issuekey = $issue->key;
   *   $parentkey = $issue->fields->parent->key;
   *   $customfielddata = $issue->fields->customfield_10404;
   */
  public function jira_rest_searchissue_all_issues($jql_string, $options = array()) {
    $jira_issues = array();

    // default options
    $options += array(
      'startAt' => 0,
    );

    // retrieve all query results with multiple requests
    do {
      $jira_response = $this->jira_rest_searchissue($jql_string, $options);

      $jira_issues = array_merge($jira_issues, $jira_response->issues);

      $options['startAt'] += $jira_response->maxResults;
    } while($jira_response->total > $options['startAt']);

    return $jira_issues;
  }

  /**
   * Returns a full representation of the issue for the given issue key.
   *
   * @see https://docs.atlassian.com/jira/REST/6.2.4/#d2e3822
   *
   * @param $issue_key string
   *   the issue id or key
   * @param array $options
   * @return object
   *   an object representing all metadata for the given id or key
   * @throws JiraRestException
   */
  public function jira_rest_getfullissue($issue_key, $options = array()) {
    $ch = $this->jira_rest_get_curl_resource($options, '/issue/' . urlencode($issue_key));
    return $this->jira_rest_curl_execute($ch);
  }

  /**
   * Creates a JIRA issue.
   *
   * example for $issuedata parameter
   *   $issuedata =
   *    array('fields'=>array(
   *     'project'=>array('id'=>$projectid,),
   *     'summary'=>$summary,
   *     'description'=>$description,
   *     'priority'=>array('id'=>$priorityid,),
   *     'issuetype'=>array('id'=>$issuetypeid),
   *     'customfield_xxx'=>$dataforcustomfield,
   *     ),
   *   );
   *
   * @param $issue_data array
   *   array containing data for issue fields
   * @param array $options
   *
   * @return object
   *   the issue created
   *   access e.g.
   *   $createdissue_id = $response_obj->id;
   *   $createdissue_key = $response_obj->key;
   *
   * @throws JiraRestException
   */
  public function jira_rest_createissue($issue_data, $options = array()) {
    $ch = $this->jira_rest_get_curl_resource($options, '/issue/');
    curl_setopt($ch, CURLOPT_POST, TRUE);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($issue_data));
    curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-type: application/json'));
    return $this->jira_rest_curl_execute($ch);
  }

  /**
   * Updates/Edits a JIRA issue.
   *
   *
   * @param array $issue_data
   *   array containing data for updating/editing issuefields
   * @param string $issue_key
   *   the key string identifying issue to be updated
   * @param array $options
   *
   * @return object
   *   the issue updated
   *
   * @throws JiraRestException
   */
  public function jira_rest_updateissue($issue_data, $issue_key, $options = array()) {
    $ch = $this->jira_rest_get_curl_resource($options, '/issue/' . $issue_key);
    curl_setopt($ch, CURLOPT_POST, TRUE);
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'PUT');
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($issue_data));
    curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-type: application/json'));
    return $this->jira_rest_curl_execute($ch);
  }

  /**
   * Attaches files to jira issue.
   * A wrapper function for jira_rest_attachfiletoissue(), extending it's functionality.
   *
   * jira_rest_attachfiletoissue() requires to be in the folder where the file resides during execution otherwise
   * passing a full path e.g. /home/username/file will not upload the file properly.
   * The wrapper function resolves this issue by going inside the file's folder and then uploading it using only the filename.
   *
   * @param $files array
   *   An array containing one or more absolute file paths
   * @param $issue_key
   *   The jira issue key or id, to which file/files should be attached
   * @param array $options
   *     delete_files boolean If set to true will delete files from disk after successful upload
   *     force_delete boolean If set to true will delete files from disk after upload attempt not considering successful or not
   *
   * @return array|bool
   *   TRUE on successful upload of all files or an array consisting of the failed to upload files
   *
   * @throws JiraRestException
   */
  public function jira_rest_attachmultiplefilestoissue_absolutepath($files, $issue_key, $options = array()) {
    $failed = array();

    foreach($files as $filepath) {
      $response = jira_rest_attachfiletoissue_absolutepath($filepath, $issue_key, $options);
      if(!$response) {
        $failed[] = $filepath;
      }
    }

    if($failed) {
      return $failed;
    }
    else {
      return true;
    }
  }

  /**
   * Attaches file to jira issue.
   * A wrapper function for jira_rest_attachfiletoissue(), extending it's functionality.
   *
   * jira_rest_attachfiletoissue() requires to be in the folder where the file resides during execution otherwise
   * passing a full path e.g. /home/username/file will not upload the file properly.
   * The wrapper function resolves this issue by going inside the file's folder and then uploading it using only the filename.
   *
   * @param string $filepath
   *   An absolute path to file
   * @param string $issue_key
   *   The jira issue key or id, to which file/files should be attached
   * @param array $options
   *
   * @return boolean
   *   On success TRUE, on failure FALSE
   *
   * @throws JiraRestException
   */
  public function jira_rest_attachfiletoissue_absolutepath($filepath, $issue_key, $options = array()) {

    // default options
    $options += array(
      'delete_files' => false,
      'force_delete' => false,
    );

    if (file_exists($filepath)) {
      //Setting $force_delete implies also removing files when upload successful
      if ($options['force_delete'] == true && $options['delete_files'] == false) {
        $options['delete_files'] = true;
      }

      //save current php path
      $olddir = getcwd();

      //chdir to folder, in which the pdf resides, otherwise file can't be uploaded using full path
      chdir(dirname($filepath));
      $filename = basename($filepath);

      $ret = false;

      if (jira_rest_attachfiletoissue($filename, $issue_key, $options)) {
        //delete files after successful upload
        if ($options['delete_files']) {
          if (!unlink($filepath)) {
            throw new JiraRestException(t("Couldn't remove file @pdf from disk on successful upload", array('@pdf' => $filepath)));
          }
        }

        $ret = true;
      }
      else {
        //force delete files after failed upload
        if ($options['force_delete']) {
          if (!unlink($filepath)) {
            throw new JiraRestException(t("Couldn't remove file @pdf from disk on unsuccessful upload", array('@pdf' => $filepath)));
          }
        }
      }

      //go back to origin dir
      chdir($olddir);

      return $ret;
    }
    else {
      throw new JiraRestException("File doesn't exists: $filepath");
    }
  }

  /**
   * Attaches file to jira issue.
   *
   * @param $filename
   * @param $issue_key
   * @param array $options
   *
   * @return bool
   *
   * @throws JiraRestException
   */
  public function jira_rest_attachfiletoissue($filename, $issue_key, $options = array()) {
    $ch = $this->jira_rest_get_curl_resource($options, '/issue/' . $issue_key . '/attachments');
    curl_setopt($ch, CURLOPT_HTTPHEADER, array('X-Atlassian-Token: nocheck'));
    curl_setopt($ch, CURLOPT_POSTFIELDS, array('file' => "@$filename"));
    $response = $this->jira_rest_curl_execute($ch);
    if (gettype($response) == 'array' && isset($response[0]->filename) && $response[0]->filename == $filename) {
      return true;
    }

    return false;
  }

  /**
   * Close issue with resolution 'fixed'.
   *
   * @param $issue_key
   * @param array $transition_data
   * @param array $options
   *
   * @return mixed
   */
  public function jira_rest_closeissuefixed($issue_key, $transition_data = array(), $options = array()) {

    $transition_data += array(
      "update" => array('comment' => array(array('add' => array('body' => 'closed by drupal service')))),
      "fields" => array("resolution" => array('name' => 'Fixed')),
      "transition" => array('id' => variable_get('jira_rest_close_issue_transition_id', '2')),
    );

    return jira_rest_issue_transition($issue_key, $transition_data, $options);
  }


  /**
   * Resolve issue with resolution 'fixed'.
   *
   * @param $issue_key
   * @param array $transition_data
   * @param array $options
   *
   * @return mixed
   */
  public function jira_rest_resolveissuefixed($issue_key, $transition_data = array(), $options = array()) {

    $transition_data += array(
      "update" => array('comment' => array(array('add' => array('body' => 'resolved by drupal service')))),
      "fields" => array("resolution" => array('name' => 'Fixed')),
      "transition" => array('id' => variable_get('jira_rest_resolve_issue_transition_id', '5')),
    );

    return jira_rest_issue_transition($issue_key, $transition_data, $options);
  }

  /**
   * @param $issue_key
   * @param array $transition_data
   * @param array $options
   *
   * @return mixed
   *
   * @throws JiraRestException
   */
  public function jira_rest_issue_transition($issue_key, $transition_data = array(), $options = array()) {

    if(empty($transition_data)) {
      return false;
    }

    $ch = $this->jira_rest_get_curl_resource($options, '/issue/' . $issue_key . '/transitions');
    curl_setopt($ch, CURLOPT_POST, TRUE);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($transition_data));
    curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-type: application/json'));

    return $this->jira_rest_curl_execute($ch);
  }


  /**
   * Returns all work log entries that belong to the given issue id.
   * If an id is specified in the options array, only the corespondent work log entry is returned.
   *
   * @param int $issue_id
   *   issue id (or key) for which the work log shall be returned
   * @param array $options (optional)
   *   overwrites default value of specified options
   *     id int return one work log entry with corresponding id
   *     @see jira_rest_default_curl_options
   *
   * @return object
   *   if no work log entry id was provided, an object containing an array of all work log entries is returned
   *   else a work log entry object is returned
   *
   *   example for iterating over all work log entries of the issue:
   *   $worklog = jira_rest_get_worklog(12345);
   *   foreach($worklog->worklogs as $worklog_entry) {
   *     print $worklog_entry->timeSpent;
   *   }
   *
   *   some examples for getting information from a single work log entry:
   *   $created = $worklog_entry->created;
   *   $author_name = $worklog_entry->author->displayName;
   *
   * @throws JiraRestException
   */
  public function jira_rest_get_worklog($issue_id, $options = array()) {
    // default options
    $options += array(
      'id' => -1,
    );

    $jira_query = '/issue/' . urlencode($issue_id) . '/worklog';

    if($options['id'] != -1) {
      $jira_query .= '/' . (int) $options['id'];
    }

    $ch = $this->jira_rest_get_curl_resource($options, $jira_query);
    return $this->jira_rest_curl_execute($ch);
  }


  /**
   * Returns all projects that are visible to the current Jira user.
   * If an id is specified in the options array, only the corespondent project data is returned.
   *
   * @param array $options (optional)
   *   overwrites default value of specified options
   *     id int return project with corresponding id
   *     @see jira_rest_default_curl_options
   *
   * @return mixed
   *   if no project id was provided, an array containing all projects (object) is returned
   *   else a project object is returned (contains extended information compared to REST call without id)
   *
   *   some examples for getting information from a single project object:
   *   $project_key = $project->key;
   *   $project_name = $project->name;
   *
   * @throws JiraRestException
   */
  public function jira_rest_get_project($options = array()) {
    // default options
    $options += array(
      'id' => -1,
    );

    $jira_query = '/project';

    if($options['id'] != -1) {
      $jira_query .= '/' . (int) $options['id'];
    }

    $ch = $this->jira_rest_get_curl_resource($options, $jira_query);
    return $this->jira_rest_curl_execute($ch);
  }


  /**
   * Returns all status that are available in the specified jira ressource.
   *
   * @param array $options
   *   overwrites default value of specified options
   *     @see jira_rest_default_curl_options
   *
   * @return array
   *   returns an array of status objects that exist in the specified jira ressource
   *
   *   an example how to iterate over the results:
   *
   *   $jira_status = jira_rest_get_status();
   *   foreach($jira_status as $status) {
   *     print $status->name;
   *   }
   *
   * @throws JiraRestException
   */
  public function jira_rest_get_status($options = array()) {
    $ch = $this->jira_rest_get_curl_resource($options, '/status');
    return $this->jira_rest_curl_execute($ch);
  }


  /**
   * Retrieves the content of an attachment from a specified url using the jira credentials.
   *
   * An example to retrieve the attachment of an issue:
   *   $fullissue = jira_rest_getfullissue($issue->key);
   *   $attachment = reset($fullissue->fields->attachment);
   *   $attachment_url = $attachment->content;
   *   $content = jira_rest_download_attachment($attachment_url);
   *
   * @param string $attachment_url
   *   Full url of the attachment.
   * @param array $options
   *   overwrites default value of specified options
   *     @see jira_rest_default_curl_options
   *
   * @return mixed
   *   returns false on failure or the result on success
   *
   * @throws JiraRestException
   */
  public function jira_rest_download_attachment($attachment_url, $options = array()) {

    // replace jira url with file url for this call
    $options['jira_url'] = $attachment_url;

    $ch = $this->jira_rest_get_curl_resource($options, '');
    return $this->jira_rest_curl_execute($ch, array('json_decode' => false));
  }
}








