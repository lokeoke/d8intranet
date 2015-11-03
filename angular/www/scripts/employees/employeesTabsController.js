'use strict';

/**
 * @ngdoc function
 * @name d8intranetApp.controller:AboutCtrl
 * @description
 * # AboutCtrl
 * Controller of the d8intranetApp
 */
angular.module('d8intranetApp')
  .controller('employeesController', function ($scope) {

    $scope.tabs = [{
      title: 'Teams',
      url: 'templates/teams.html'
    }, {
      title: 'Employees list',
      url: 'templates/people.html'
    }];

    $scope.currentTab = 'templates/teams.html';

    $scope.onClickTab = function (tab) {
      $scope.currentTab = tab.url;
    };

    $scope.isActiveTab = function (tabUrl) {
      return tabUrl == $scope.currentTab;
    };

  });
