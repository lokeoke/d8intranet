'use strict';

/**
 * @ngdoc function
 * @name d8intranetApp.controller:AboutCtrl
 * @description
 * # AboutCtrl
 * Controller of the d8intranetApp
 */
angular.module('d8intranetApp')
  .controller('peopleListController', function ($scope, $http, $filter, getJsonData) {
    getJsonData.getUsers().then(function (d) {
      $scope.people = d;


      $scope.$watch('teamFilter', function (newValue, oldValue) {
        $scope.filterBy = newValue;
      });

      $scope.updateSearchField = function(name){
        $scope.filterKeyword = name;
      };

      $scope.teams = [];

      angular.forEach($scope.people, function(employee){
        //console.log(employee);

        if (!$scope.teams[employee.field_team.target_id]) {
          $scope.teams[employee.field_team.target_id] = employee.field_team.target_id
        }
      });

      $scope.teams = $scope.teams.filter(function(n){ return n != undefined });
      $scope.teams.unshift({"team_name":'all', "value": "0"});
      $scope.teamFilter = {selectedOption : $scope.teams.target_id}
    });
  });


