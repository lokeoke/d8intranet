'use strict';

/**
 * @ngdoc function
 * @name d8intranetApp.controller:MainCtrl
 * @description
 * # MainCtrl
 * Controller of the d8intranetApp
 */
angular.module('d8intranetApp')
  .controller('sidebarController', function (config, $scope, $rootScope, $location) {
    $scope.menuStates = {};
    $scope.menuStates.activeItem = '#'+($location.$$url).slice(1);

    $scope.menuItemsList = [
      {
        icon: 'dashboard',
        title: 'Dashboard',
        url: config.frontUrl
      },
      {
        icon: 'employees',
        title: 'Employees',
        url: config.employeesUrl
      },
      {
        icon: 'rest',
        title: 'Statistic',
        url: config.vacationsUrl
      },
      {
        icon: 'logout',
        title: 'Logout'
      }
    ];

    $scope.menuIsOpen = false;

    $scope.setActiveMenuItem = function(menuItemUrl, menuState) {
      $scope.menuStates.activeItem = menuItemUrl;
      $scope.menuIsOpen = menuState;
    };
  });
