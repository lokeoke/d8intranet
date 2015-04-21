'use strict';

/**
 * @ngdoc function
 * @name d8intranetApp.controller:UsersCtrl
 * @description
 * # UsersCtrl
 * Controller of the d8intranetApp
 */
angular.module('d8intranetApp')
  .controller('UsersCtrl', function ($scope, $routeParams, $http) {
    $http.get('http://d8server.local/user').then(function(response) {
      console.log('NodeCtrl GET response', response);
      var data = response.data;
      $scope.type = data.type[0].target_id;
      $scope.title = data.title[0].value;
      $scope.body = data.body[0].value;
      console.log('NodeCtrl GET $scope', $scope);
    });
  });
