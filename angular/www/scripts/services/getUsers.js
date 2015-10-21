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
    var url = '/admin/api/user';
    var users = [];
    var promise;

    var getJsonData = {
      getUsers: function () {
        if (users.length > 0) {
          $rootScope.dataLoaded = true;
          return promise;
        }
        else {
          $rootScope.dataLoaded = false;
          return getJsonData.async(url);
        }
      },
      async: function (url) {
        // $http returns a promise, which has a then function, which also returns a promise
        promise = $http.get(url).then(function (response) {
          //console.log('Getting users');
          // The then function here is an opportunity to modify the response
          // The return value gets picked up by the then in the controller.
          users = response.data;

          // Clear users each 2 minutes
          $timeout(function () {
            users = [];
          }, '120000');
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
