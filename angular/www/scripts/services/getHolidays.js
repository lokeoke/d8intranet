'use strict';
/**
 * @ngdoc function
 * @name d8intranetApp.filter:filterByPosition
 * @description
 * # filterByPosition
 * Filter of the d8intranetApp
 */

angular.module('d8intranetApp')
    .service('getHolidays', function ($http) {

      var getHolidays = {
        getDays: function (url) {
          var promise = $http.get(url).then(function (response) {
            // The then function here is an opportunity to modify the response
            // The return value gets picked up by the then in the controller.
            var holidays = response.data;
            return holidays;
          });
          // Return the promise to the controller
          return promise;
        }

      };

      return getHolidays;
    });
