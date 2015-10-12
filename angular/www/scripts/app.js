'use strict';

/**
 * @ngdoc overview
 * @name d8intranetApp
 * @description
 * # d8intranetApp
 *
 * Main module of the application.
 */
angular.module('d8intranetApp', [
  'ngAnimate',
  'ngCookies',
  'ngResource',
  'ngRoute',
  'ngSanitize',
  'ngTouch',
  'ui.router'
])
  .config(function ($routeProvider, $httpProvider, $compileProvider) {
    $routeProvider
      .when('/', {
        url: "/main",
        templateUrl: 'views/dashboard.html',
        controller: 'dashboardController'
      })
      .when('/employees', {
        url: "/employees",
        templateUrl: 'views/employees.html',
        controller: 'employeesController'
      })
      .when('/user', {
        url: "/user",
        templateUrl: 'views/user.html',
        controller: 'singleUserController'
      })
      .when('/user/:userId', {
        url: "/user/:userId",
        templateUrl: 'views/user.html',
        controller: 'ShowUserCtrl'
      })
      .when('/vacation', {
        url: "/statistic",
        templateUrl: 'views/statistic.html',
        controller: 'StatisticController'
      })
      .otherwise({
        redirectTo: '/dashboard',
        templateUrl: 'views/dashboard.html'
      });

    $httpProvider.defaults.headers.common.Accept = 'application/hal+json';

    $compileProvider.aHrefSanitizationWhitelist(/^\s*(https?|mailto|skype|chrome-extension):/);
  })

  .constant('config', {
    frontUrl: "#main",
    employeesUrl: "#employees",
    vacationsUrl: "#vacation",
    checkInUrl: "http://drupal.d8pp.dev:8888/checkin",
    totalVacation: "20"
  })
  
  .controller('GetDateCtrl', function ($scope) {
    $scope.date = new Date();
  });




