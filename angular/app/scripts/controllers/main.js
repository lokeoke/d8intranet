'use strict';

/**
 * @ngdoc function
 * @name d8intranetApp.controller:MainCtrl
 * @description
 * # MainCtrl
 * Controller of the d8intranetApp
 */
angular.module('d8intranetApp')

  .constant('config', {
    frontUrl: "#main",
    employeesUrl: "#employees",
    vacationsUrl: "#vacation",
    checkInUrl: "http://drupal.d8pp.dev:8888/checkin"
  })

  .controller('MainCtrl', function ($scope, $http, getJsonData, $rootScope) {

    getJsonData.getUsers().then(function (d) {
      $scope.users = d;

      $scope.states = {};

      angular.forEach($scope.users, function (user) {
        angular.forEach(user.statuses[0], function (key, statusName) {
          if (key == 1) {

            if (typeof $scope.states[cardName(statusName)] != 'undefined') {
              $scope.states[cardName(statusName)][user.uid[0].value] = user;
              $scope.states[cardName(statusName)]['count'] += 1;
            } else {

              $scope.states[cardName(statusName)] = {};
              $scope.states[cardName(statusName)][user.uid[0].value] = user;
              $scope.states[cardName(statusName)]['count'] = 1;

            }
          }
        });
      });

      function cardName(status) {
        switch (status) {
          case 'day_off':
            return 'Day off';
            break;
          case 'sick':
            return 'Sick';
            break;
          case 'business_trip':
            return 'Business Trip';
            break;
          case 'remote_work':
            return 'Remote work';
            break;
          case 'vacation':
            return 'Vacation';
            break;
          default:
            return '';
            break;
        }
      }
    });
  })

  .controller('pageLoad', function ($scope, $cookies) {
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
      $cookies.get('current-theme') == 'light' ? $scope.isDark = false : $scope.isDark = true;
    }

    $scope.setCookieData = function () {
      // Switch theme
      $scope.isDark = !$scope.isDark;
      // Get current cookie value
      var themeCookie = $cookies.get('current-theme');

      // If we have value light in cooke set the value to dark
      if (themeCookie == 'light') {
        $cookies.put('current-theme', 'dark', {'expires': expireDate});
      }
      // If we have value dark in cooke set the value tp light
      else {
        $cookies.put('current-theme', 'light', {'expires': expireDate});
      }
    }
  })

  .controller('checkIn', function ($scope, $http, $rootScope) {

    var colors = new Array([62, 35, 255], [60, 255, 60], [255, 35, 98], [45, 175, 230], [255, 0, 255], [255, 128, 0]);

    var step = 0;
//color table indices for:
// current color left
// next color left
// current color right
// next color right
    var colorIndices = [0, 1, 2, 3];

//transition speed
    var gradientSpeed = 0.002;

    function updateGradient() {

      if ($ === undefined) return;

      var c0_0 = colors[colorIndices[0]];
      var c0_1 = colors[colorIndices[1]];
      var c1_0 = colors[colorIndices[2]];
      var c1_1 = colors[colorIndices[3]];

      var istep = 1 - step;
      var r1 = Math.round(istep * c0_0[0] + step * c0_1[0]);
      var g1 = Math.round(istep * c0_0[1] + step * c0_1[1]);
      var b1 = Math.round(istep * c0_0[2] + step * c0_1[2]);
      var color1 = "rgb(" + r1 + "," + g1 + "," + b1 + ")";

      var r2 = Math.round(istep * c1_0[0] + step * c1_1[0]);
      var g2 = Math.round(istep * c1_0[1] + step * c1_1[1]);
      var b2 = Math.round(istep * c1_0[2] + step * c1_1[2]);
      var color2 = "rgb(" + r2 + "," + g2 + "," + b2 + ")";

      $('#gradient').css({
        background: "-webkit-gradient(linear, left top, right top, from(" + color1 + "), to(" + color2 + "))"
      }).css({
        background: "-moz-linear-gradient(left, " + color1 + " 0%, " + color2 + " 100%)"
      });

      step += gradientSpeed;
      if (step >= 1) {
        step %= 1;
        colorIndices[0] = colorIndices[1];
        colorIndices[2] = colorIndices[3];

        //pick two new target color indices
        //do not pick the same as the current one
        colorIndices[1] = ( colorIndices[1] + Math.floor(1 + Math.random() * (colors.length - 1))) % colors.length;
        colorIndices[3] = ( colorIndices[3] + Math.floor(1 + Math.random() * (colors.length - 1))) % colors.length;

      }
    }

    $rootScope.messageRequest = false;

    $scope.checkIn = function () {
      $rootScope.messageRequest = true;
      setInterval(updateGradient, 10);

      var dataObj = {"message": "Yo! Bitch"};

      var res = $http.post(constant.checkInUrl, dataObj);

      res.success(function (data, status, headers, config) {
        $scope.message = data;
        $rootScope.messageRequest = false;
      });

      res.error(function (data, status, headers, config) {
        console.log("failure message: " + JSON.stringify({data: data}));
        $rootScope.messageRequest = false;
      });
    };
  })

  .controller('GetDateCtrl', function ($scope) {
    $scope.date = new Date();
  });

