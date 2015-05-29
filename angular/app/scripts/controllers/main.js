'use strict';

/**
 * @ngdoc function
 * @name d8intranetApp.controller:MainCtrl
 * @description
 * # MainCtrl
 * Controller of the d8intranetApp
 */
angular.module('d8intranetApp')
  .controller('MainCtrl', function ($scope, $http) {
    $http({
      method: 'GET',
      url: '../../jsons/employees.json'
    }).success(function (response) {
      $scope.users = response.users;
      console.log($scope.users);

      $scope.states = [];
      $scope.employees = [];

      angular.forEach(response.users, function(user) {
        angular.forEach(user.status[0], function(key, statusName) {
          if(key == 1) {
            $scope.states.push(statusName)
          }
        });

      });

      console.log($scope.states);


      //$scope.employees = [];
      //angular.forEach(response.users, function (cards, index) {
      //  angular.forEach(cards.users, function (users, index) {
      //    $scope.employees.push(users);
      //  });
      //});

      //console.log($scope.employees);
    });

    $scope.usersFilter = function(item) {
      console.log(item);
      return item.userStatus === 'sick' || item.userStatus === 'dayoff'
    }


  })

  .controller('GetDateCtrl', function ($scope) {
    $scope.date = new Date();
  });

