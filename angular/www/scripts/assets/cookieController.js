'use strict';

/**
 * @ngdoc function
 * @name d8intranetApp.controller:MainCtrl
 * @description
 * # Cookie controller
 * Controller of the d8intranetApp
 */
angular.module('d8intranetApp')

    .controller('cookieController', function ($scope, $cookies) {
      var expireDate = new Date();

      // Setup cookie live to 1 year
      expireDate.setDate(expireDate.getDate() + 12);

      // If we don't have any cookie yet - set cookie to light theme
      if ($cookies.get('current-theme') === undefined) {
        $cookies.put('current-theme', 'light', {'expires': expireDate});
        $scope.isDark = false;
      }
      else {
        // If we have light in cooke set the light theme
        $cookies.get('current-theme') === 'light' ? $scope.isDark = false : $scope.isDark = true;
      }

      $scope.setCookieData = function () {
        // Switch theme
        $scope.isDark = !$scope.isDark;

        // Get current cookie value
        var themeCookie = $cookies.get('current-theme');

        // If we have value light in cooke set the value to dark
        if (themeCookie === 'light') {
          $cookies.put('current-theme', 'dark', {'expires': expireDate});
        }
        // If we have value dark in cooke set the value tp light
        else {
          $cookies.put('current-theme', 'light', {'expires': expireDate});
        }
      };

    });
