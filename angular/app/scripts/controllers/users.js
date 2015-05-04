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
    $http.get('http://d8pp.dev/user-list').then(function(response) {
      console.log('NodeCtrl GET response', response);
      var data = response.data;
      $scope.users = data;
      console.log('NodeCtrl GET $scope', $scope);
    });
  });
