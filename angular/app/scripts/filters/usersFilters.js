'use strict';
/**
 * @ngdoc function
 * @name d8intranetApp.filter:filterByPosition
 * @description
 * # filterByPosition
 * Filter of the d8intranetApp
 */

angular.module('d8intranetApp')
  .filter('filterByPosition', function () {
    return function (user, position) {
      var filtered = [];
      angular.forEach(user, function (value) {
        if (value.position == position) {
          filtered.push(value)
        }
      });
      return filtered;
    }
  })

  .filter('filterByTeam', function () {
    return function (user, team) {
      var filtered = [];
      angular.forEach(user, function (value) {
        if (value.team == team) {
          filtered.push(value)
        }
      });
      return filtered;
    }
  })

  .filter('plural', function () {
    return function (string) {

      // Get number from the string
      var matches = string.match(/\d+/g);

      if(matches == undefined || matches == null) {
        return string;
      }

      else {
        var lastDigit = matches % 10;

        if(lastDigit === 1) {
          return string;
        }

        if(lastDigit >= 2) {
          return string + 's';

        }
      }
    }
  });
