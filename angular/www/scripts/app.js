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
      .when('/dashboard', {
        url: "/dashboard",
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
    frontUrl: "#dashboard",
    employeesUrl: "#employees",
    vacationsUrl: "#vacation",
    checkInUrl: "admin/api/user/check-in",
    checkOutUrl: "admin/api/user/check-out",
    checkedInList: "admin/api/user/checked-in",
    loginPathUrl: "admin/user/login?redirect=true",
    logoutPathUrl: "admin/user/logout?redirect=true",
    status: "admin/api/user/check-state",
    totalVacation: "20"
  })

  .controller('GetDateCtrl', function ($scope) {
    $scope.date = new Date();
  })

;




