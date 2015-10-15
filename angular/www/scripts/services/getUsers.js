'use strict';
/**
 * @ngdoc function
 * @name d8intranetApp.filter:filterByPosition
 * @description
 * # filterByPosition
 * Filter of the d8intranetApp
 */

angular.module('d8intranetApp')
  .service('getJsonData', function ($http, $timeout, $rootScope) {
    var url = 'http://angular.d8pp.dev/admin/user-list';
    var users = [];
    var promise;


    var getJsonData = {
      getUsers: function () {
        return this.requestUsers().then(function(users){
          angular.forEach(users, function (user) {
            // This live hack was added for compatibility
            // This function takes default value and add the user value to it
            $.extend(true, userDefaultSettings, user);
            // This store changes to user's object
            // I guess that it cna be changed to more correct approach
            $.extend(true, user, userDefaultSettings);
            return user;
          });
          return users;
        })
      },
      requestUsers: function () {

        if (users.length > 0) {
          console.log('I have users');
          $rootScope.dataLoaded = true;
          return promise;
        }
        else {
          console.log('Don\'t have users... request');
          $rootScope.dataLoaded = false;
          return getJsonData.async(url);
        }
      },
      async: function (url) {
        // $http returns a promise, which has a then function, which also returns a promise
        promise = $http.get(url).then(function (response) {
          console.log(response)
          console.log('Getting users');
          // The then function here is an opportunity to modify the response
          // The return value gets picked up by the then in the controller.
          users = response.data;

          // Clear users each 2 minutes
          $timeout(function () {
            users = [];
          }, '120000');

          console.log('Users are sent');
          return users;
        },
          function errorCallback(response) {
            // TODO add fallback code for different statuses
            console.log(response);
        });
        $rootScope.dataLoaded = true;
        // Return the promise to the controller
        return promise;
      }
    };

    return getJsonData;
  });


var userDefaultSettings = {
  "uid": [{"value": "4"}],
  "uuid": [{"value": "37cc971e-1ef0-49f7-9358-88edda295464"}],
  "langcode": [{"value": "en"}],
  "preferred_langcode": [{"value": "en"}],
  "preferred_admin_langcode": [],
  "name": [{"value": "propraw"}],
  "mail": [{"value": "propraw@example.com"}],
  "timezone": [{"value": "UTC"}],
  "status": [{"value": "1"}],
  "created": [{"value": "1438871090"}],
  "changed": [{"value": "1438871090"}],
  "access": [{"value": "0"}],
  "login": [{"value": "0"}],
  "init": [],
  "roles": [],
  "default_langcode": [{"value": "1"}],
  /*"statuses": [
    {
      "value": "day_off"
    }
  ],*/
  "field_came_to_propeople": [{"value": 1323648000}],
  "field_first_name": [{"value": "Artem"}],
  "field_last_name": [{"value": "Miroshnik"}],
  "field_job_title": [{"value": "Drupal tech lead"}],
  "field_user_position": [{"value": "Department Lead"}],
  "field_user_birthday": [{"value": "30.01.1992"}],
  "field_user_image": [{"value": "../images/anonymus.png"}],
  "field_user_email": [{"value": "artyom.miroshnik@propeople.com.ua"}],
  "field_github_id": [{"value": "www.github.com"}],
  "field_drupal_org_id": [{"value": "www.drupal.org"}],
  "field_user_skype": [{"value": "miroshnik92"}],
  "field_phone_number": [{"value": "+38 095 123 12 12"}],
  "field_position": [{"value": 2}],
  "field_team": [{"value": 3, "team_name": "Departments leads"}],
  "field_work": [{"value": 1}],
  /*"field_vacation": [
    {
      "start_date": 1420070400,
      "end_date": 1420156800,
      "state": "unpaid-leave"
    },
    {
      "start_date": 1423699200,
      "end_date": 1424044800,
      "state": "paid-leave"
    }
  ],*/
  /*"field_dayoff": [
    {
      "value": {
        "start_date": 1428710400,
        "end_date": 1428710400,
        "state": "paid-leave"
      }
    },
    {
      "value": {
        "start_date": 1424044800,
        "end_date": 1424044800,
        "state": "unpaid-leave"
      }
    },
    {
      "value": {
        "start_date": 1429660800,
        "end_date": 1429660800,
        "state": "unpaid-leave"
      }
    }
  ]*/
};
