'use strict';

/**
 * @ngdoc function
 * @name d8intranetApp.controller:MainCtrl
 * @description
 * # MainCtrl
 * Controller of the d8intranetApp
 */
angular.module('d8intranetApp')

  .controller('dashboardController', function ($scope, $http, getJsonData) {

    $scope.pageClass = 'page-home';

    getJsonData.getUsers().then(function (d) {
      $scope.users = d;

      $scope.states = {};
      $scope.availableEmployees = {};

      var haveStatus;

      angular.forEach($scope.users, function (user) {
        haveStatus = false;

        angular.forEach(user.statuses[0], function (key, statusName) {
          if (key == 1) {

            haveStatus = true;

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

        if(!haveStatus) {
          if (typeof $scope.availableEmployees.available != 'undefined') {
            $scope.availableEmployees.available[user.uid[0].value] = user;
            $scope.availableEmployees.available['count'] += 1;
          }
          else {
            $scope.availableEmployees.available = {};
            $scope.availableEmployees.available[user.uid[0].value] = user;
            $scope.availableEmployees.available['count'] = 1;
          }
        }
      });

      $scope.availableEmployees.available['title'] = 'Available employees';

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
  });
