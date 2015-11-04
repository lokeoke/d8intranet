'use strict';

/**
 * @ngdoc function
 * @name d8intranetApp.controller:AboutCtrl
 * @description
 * # AboutCtrl
 * Controller of the d8intranetApp
 */


angular.module('d8intranetApp')

    .controller('StatisticController', function ($scope, $http, getJsonData, config, formatUserData, getHolidays) {

      getHolidays.getDays(config.holidaysUrl).then(function (data) {
        $scope.holidays = data;

        getJsonData.getUsers().then(function (data) {
          $scope.users = data;

          var holidaysList = [];

          angular.forEach($scope.holidays, function (holiday) {
            holidaysList.push(new Date(holiday.field_holiday_date));
          });

          var calendarMonths = {};
          formatUserData.formattedUser($scope.users, holidaysList);

          $scope.months = formatUserData.setMonths(calendarMonths);

          $scope.$watch('teamFilter', function (newValue, oldValue) {
            $scope.filterBy = newValue;
          });

          $scope.teams = [];

          function isInArray(element, index, array) {
            return element.name != this.name;
          }

          angular.forEach($scope.users, function (employee) {
            var tempObj = {
              "name": employee.field_team[0].name,
              "target_id": parseInt(employee.field_team[0].target_id)
            };
            if ($scope.teams.length == 0 || $scope.teams.every(isInArray, tempObj)) {
              $scope.teams.push(tempObj);
            }
          });

          $scope.teams.unshift( {"name": 'all', "target_id": 0} );
          $scope.teamFilter = { selectedOption: $scope.teams[0].target_id }
        });
      });


      $scope.tabs = [
        {
          title: 'Vacations',
          url: 'templates/vacations.html'
        },
        {
          title: 'Days off',
          url: 'templates/daysoff.html'
        },
        {
          title: 'Sick days',
          url: 'templates/sick.html'
        },
        {
          title: 'Business trips',
          url: 'templates/journey.html'
        },
        {
          title: 'Remote work',
          url: 'templates/remote.html'
        },
        {
          title: 'Work off',
          url: 'templates/workoff.html'
        }
      ];

      $scope.currentTab = 'templates/vacations.html';

      $scope.onClickTab = function (tab) {
        $scope.currentTab = tab.url;
      };

      $scope.isActiveTab = function (tabUrl) {
        return tabUrl == $scope.currentTab;
      };
    });
