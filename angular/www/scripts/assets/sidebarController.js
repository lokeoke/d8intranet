'use strict';

/**
 * @ngdoc function
 * @name d8intranetApp.controller:MainCtrl
 * @description
 * # MainCtrl
 * Controller of the d8intranetApp
 */
angular.module('d8intranetApp')
  .controller('sidebarController', function (config, checkState, $scope, $rootScope, $location) {
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
        title: 'Logout',
        url: config.logoutUrl
      }
    ];

    $scope.loginUrl = config.loginPathUrl;

    $scope.menuIsOpen = false;

    $scope.setActiveMenuItem = function(menuItemUrl, menuState) {
      $scope.menuStates.activeItem = menuItemUrl;
      $scope.menuIsOpen = menuState;
    };

    $rootScope.jira = '';

    checkState.getState(config.status).then(function(data){
      $scope.logged = data.logged;
      $rootScope.jira = data.jira;
      $scope.currentUserId = data.uid;
      $scope.currentUserPic = data.field_image;
    })
  });
