'use strict';

/**
 * @ngdoc function
 * @name d8intranetApp.controller:AboutCtrl
 * @description
 * # AboutCtrl
 * Controller of the d8intranetApp
 */
angular.module('d8intranetApp')
  .controller('teamsController', function ($scope, $http, getJsonData, $rootScope) {

    // Teams requests
    $http({
      method: 'GET',
      url: '../../jsons/teams.json'
    }).success(function (response) {
      $scope.teamsList = response.teams;
      $rootScope.dataLoaded = true;
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



  });
