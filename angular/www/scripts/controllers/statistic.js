'use strict';

/**
 * @ngdoc function
 * @name d8intranetApp.controller:AboutCtrl
 * @description
 * # AboutCtrl
 * Controller of the d8intranetApp
 */


angular.module('d8intranetApp')

  .controller('StatisticController', function ($scope, $http, getJsonData, formatUserData) {

    //function processFields(fieldType, futureObjField, param) {
    //  var obj = [];
    //  angular.forEach(fieldType, function (param) {
    //    var month = formatUserData.getMonthNumber(param.start_date);
    //    $scope.months[month].futureObjField = formatUserData.formatStatisticsData(futureObjField, param);
    //
    //    obj.push($scope.months[month].futureObjField);
    //
    //  });
    //
    //  return obj;
    //}


    getJsonData.getUsers().then(function (data) {
      $scope.users = data;
      var calendarMonths = {};

      formatUserData.formattedUser($scope.users);

      $scope.months = formatUserData.setMonths(calendarMonths);


      $scope.$watch('teamFilter', function (newValue, oldValue) {
        $scope.filterBy = newValue;
      });

      // Create teams array for filtering
      $scope.teams = [];
      angular.forEach($scope.users, function (emplyee) {
        if (!$scope.teams[emplyee.field_team[0].value]) {
          $scope.teams[emplyee.field_team[0].value] = emplyee.field_team[0]
        }
      });

      $scope.teams = $scope.teams.filter(function (n) {
        return n != undefined
      });

      $scope.teams.unshift({"team_name": 'all', "value": "0"});
      $scope.teamFilter = {selectedOption: $scope.teams[0].value};
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
        title: 'Duty Journey',
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
