'use strict';

/**
 * @ngdoc function
 * @name d8intranetApp.controller:AboutCtrl
 * @description
 * # AboutCtrl
 * Controller of the d8intranetApp
 */
angular.module('d8intranetApp')
  .controller('EmployeesCtrl', function ($scope) {
    $scope.users = [
      { name: "Denis Bondarenko"},
      { name: "Mikhail Sokolovskiy"}
    ];

    $scope.filterFunction = function(element) {
      return element.name.match(/^Ma/) ? true : false;
    };

    $scope.tabs = [{
      title: 'Teams',
      url: 'views/teams.html'
    }, {
      title: 'People',
      url: 'views/people.html'
    }];

    $scope.currentTab = 'views/teams.html';

    $scope.onClickTab = function (tab) {
      $scope.currentTab = tab.url;
    };

    $scope.isActiveTab = function(tabUrl) {
      return tabUrl == $scope.currentTab;
    }
  })
  .directive('inputEffects', ['$document', function($document) {
    return {
      link: function(scope, element, attr) {
        element.on('keyup blur', function () {
          var currentValue = $(this).val();
          var $parentContainer = $(this).parent();

          if (currentValue.length > 0) {
            $parentContainer.addClass('input-filled');
          } else {
            $parentContainer.removeClass('input-filled');
          }
        });
      }
    };
  }]);
