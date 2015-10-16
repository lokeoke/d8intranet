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

      $scope.availableEmployees = {available: {'count': 0}};

      var haveStatus;

      angular.forEach($scope.users, function (user) {

        if (user.statuses.length > 0) {

          angular.forEach(user.statuses, function (status) {
            var statusKey = status.value;
            var statusName = cardName(statusKey);

            $scope.states[statusName] = $scope.states[statusName] || {'count': 0};
            $scope.states[statusName][user.uid[0].value] = user;
            $scope.states[statusName]['count'] += 1;

          });
        }

        else {
          if (!haveStatus) {
            $scope.availableEmployees.available[user.uid[0].value] = user;
            $scope.availableEmployees.available['count'] += 1;
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
            return status;
            break;
        }
      }
    });
  });
