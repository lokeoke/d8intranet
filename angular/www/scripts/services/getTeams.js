'use strict';
/**
 * @ngdoc function
 * @name d8intranetApp.filter:filterByPosition
 * @description
 * # filterByPosition
 * Filter of the d8intranetApp
 */

angular.module('d8intranetApp')
    .service('getTeamsList', function ($http) {

      var getTeamsList = {
        getTeams: function (url) {
          var promise = $http.get(url).then(function (response) {
            // The then function here is an opportunity to modify the response
            // The return value gets picked up by the then in the controller.
            var teams = response.data;
            return teams;
          });
          // Return the promise to the controller
          return promise;
        }

      };

      return getTeamsList;
    });
