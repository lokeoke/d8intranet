'use strict';

/**
 * @ngdoc function
 * @name d8intranetApp.controller:MainCtrl
 * @description
 * # Cookie controller
 * Controller of the d8intranetApp
 */
angular.module('d8intranetApp')

  .controller('modalController', function ($scope, $rootScope) {
    $rootScope.showModalWindow = false;

    $scope.closeModal = function() {
      $rootScope.showModalWindow = false;
    }

  });
