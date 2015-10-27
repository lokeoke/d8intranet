'use strict';
/**
 * @ngdoc function
 * @name d8intranetApp.filter:filterByPosition
 * @description
 * # filterByPosition
 * Filter of the d8intranetApp
 */

angular.module('d8intranetApp')
    .service('getCheckedInUsers', function ($http) {

      var getCheckedInUsers = {
        getCheckedIn: function (url) {
          var promise = $http.get(url).then(function (response) {
            // The then function here is an opportunity to modify the response
            // The return value gets picked up by the then in the controller.
            var checkedIn = response.data;
            return checkedIn;
          });
          // Return the promise to the controller
          return promise;
        },

        getCheckedOut: function(url) {
          var promise = $http.get(url).then(function(response) {
              var checkedOut = response.data;
            return checkedOut;
          });

          return promise;
        },

        getObjectSize: function(obj) {
           return Object.keys(obj).length;
        }

      };

      return getCheckedInUsers;
    });
