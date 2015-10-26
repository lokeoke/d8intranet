'use strict';

/**
 * @ngdoc function
 * @name d8intranetApp.controller:AboutCtrl
 * @description
 * # AboutCtrl
 * Controller of the d8intranetApp
 */
angular.module('d8intranetApp')
  .controller('teamsController', function ($scope, $http,$rootScope,  getTeamsList, config) {

      getTeamsList.getTeams(config.teamsUrl).then(function(d){
        $scope.teams = d;
        $rootScope.dataLoaded = true;
      });
  });
