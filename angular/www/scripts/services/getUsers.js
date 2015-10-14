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
    var url = '../../jsons/users.json';
    var users = [];
    var promise;


    var getJsonData = {
      getUsers: function () {

        if (users.length > 0) {
          console.log('I have users');
          return promise;
        }
        else {
          console.log('Don\'t have users... request');
          console.log('in')
          $rootScope.dataLoaded = false;
          return getJsonData.async(url);
        }
      },
      async: function (url) {
        // $http returns a promise, which has a then function, which also returns a promise
        promise = $http.get(url).then(function (response) {
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
        });
        $rootScope.dataLoaded = true;
        // Return the promise to the controller
        return promise;
      }
    };

    return getJsonData;
  });
