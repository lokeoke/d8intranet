'use strict';

/**
 * @ngdoc function
 * @name d8intranetApp.controller:MainCtrl
 * @description
 * # MainCtrl
 * Controller of the d8intranetApp
 */
angular.module('d8intranetApp')
    .controller('sidebarController', function (config, checkState, $scope, $rootScope, $cookies, $location, getCheckedInUsers) {
      $scope.menuStates = {};
      $scope.menuStates.activeItem = '#' + ($location.$$url).slice(1);

      $scope.loginUrl = config.loginPathUrl;
      $scope.logoutUrl = config.loginPathUrl;

      $scope.menuIsOpen = false;

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
        }
      ];


      $scope.setActiveMenuItem = function (menuItemUrl, menuState) {
        $scope.menuStates.activeItem = menuItemUrl;
        $scope.menuIsOpen = menuState;
      };

      $rootScope.jira = '';

      checkState.getState(config.status).then(function (data) {
        $scope.logged = data.logged;
        $rootScope.checkedIn = data.checked_in;
        $rootScope.jira = data.jira;
        $scope.currentUserId = data.uid;
        $scope.currentUserPic = data.field_image;

        $scope.sidebarLoaded = true;
      });

    });
