'use strict';

/**
 * @ngdoc function
 * @name d8intranetApp.controller:MainCtrl
 * @description
 * # MainCtrl
 * Controller of the d8intranetApp
 */
angular.module('d8intranetApp')
  .controller('SidebarCtrl', function (config, $scope, $location) {
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
        title: 'My vacation',
        url: config.vacationsUrl
      },
      {
        icon: 'logout',
        title: 'Logout'
      }
    ];

    $scope.setActiveMenuItem = function(menuItemUrl) {
      $scope.menuStates.activeItem = menuItemUrl;
    }

  });
