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

      function isInArray(element) {
        return element.name != this.name;
      }

      angular.forEach($scope.people, function(employee){
        var tempObj = {"name": employee.field_team[0].name, "target_id": parseInt(employee.field_team[0].target_id)};
        if ($scope.teams.length == 0 || $scope.teams.every(isInArray, tempObj)) {
          $scope.teams.push(tempObj);
        }
      });

      $scope.teams.unshift({"name":'all', "target_id": 0});
      $scope.teamFilter = {selectedOption : $scope.teams[0].target_id}
    });
  });


