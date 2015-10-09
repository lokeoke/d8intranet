'use strict';

/**
 * @ngdoc function
 * @name d8intranetApp.controller:AboutCtrl
 * @description
 * # AboutCtrl
 * Controller of the d8intranetApp
 */
angular.module('d8intranetApp')
  .controller('EmployeesCtrl', function ($scope, $http, $filter, getJsonData) {
    // Teams requests
    $http({
      method: 'GET',
      url: '../../jsons/teams.json'
    }).success(function (response) {
      $scope.teamsList = response.teams;

      $scope.regularTeams = {};
      $scope.members = {};

      // Created new object for all teams
      // In case if lead team (director, dep lead) create separate object
      // It's necessary to have separate leads and other teams for output
      angular.forEach($scope.teamsList, function (team) {
        angular.forEach(team, function (key, value) {
          $scope.regularTeams[value] = key;
        });
      });
    });


    getJsonData.getUsers().then(function (d) {
      $scope.people = d;

      $scope.$watch('teamFilter', function (newValue, oldValue) {
        $scope.filterBy = newValue;
      });


      $scope.updateSearchField = function(name){
        $scope.filterKeyword = name;
      };



      $scope.teams = [];
      angular.forEach($scope.people, function(emplyee){
        if (!$scope.teams[emplyee.field_team[0].value]) {
           $scope.teams[emplyee.field_team[0].value] = emplyee.field_team[0]
        }
      });


      $scope.teams = $scope.teams.filter(function(n){ return n != undefined });
      $scope.teams.unshift({"team_name":'all', "value": "0"});
      $scope.teamFilter = {selectedOption : $scope.teams[0].value}
    });



    $scope.tabs = [{
      title: 'Teams',
      url: 'views/teams.html'
    }, {
      title: 'People',
      url: 'views/people.html'
    }];

    $scope.currentTab = 'views/teams.html';

    $scope.onClickTab = function (tab) {
      $scope.currentTab = tab.url;
    };

    $scope.isActiveTab = function (tabUrl) {
      return tabUrl == $scope.currentTab;
    };

  });
