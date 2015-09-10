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

    $scope.isActive = function (viewLocation) {
      return viewLocation === $location.path();
    };
  });
