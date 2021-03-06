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
    .config(function ($routeProvider, $httpProvider, $compileProvider, $locationProvider) {
      $routeProvider
          .when('/', {
            url: '/dashboard',
            templateUrl: 'views/dashboard.html',
            controller: 'dashboardController'
          })
          .when('/employees', {
            url: '/employees',
            templateUrl: 'views/employees.html',
            controller: 'employeesController'
          })
          .when('/user', {
            url: "/user",
            templateUrl: 'views/user.html',
            controller: 'singleUserController'
          })
          .when('/user/:userId', {
            url: '/user/:userId',
            templateUrl: 'views/user.html',
            controller: 'ShowUserCtrl'
          })
          .when('/vacation', {
            url: '/statistic',
            templateUrl: 'views/statistic.html',
            controller: 'StatisticController'
          })
          .when('/petitions', {
            url: '/petitions',
            templateUrl: 'views/petitions.html',
            controller: 'petitionsController'
          })
          .when('/petitions/:nid', {
            url: '/petitions/:nid',
            templateUrl: 'templates/petition.html',
            controller: 'singlePetitionsController'
          })
          .when('/documents', {
            url: '/documents',
            templateUrl: 'views/documents.html',
            controller: 'documentsController'
          })
          .when('/documents/:nid', {
            url: '/documents/:nid',
            templateUrl: 'templates/document.html',
            controller: 'singleDocController'
          })
          .otherwise({
            redirectTo: '/dashboard',
            templateUrl: 'views/dashboard.html'
          });

      $httpProvider.defaults.headers.common.Accept = 'application/hal+json';

      $compileProvider.aHrefSanitizationWhitelist(/^\s*(https?|mailto|skype|chrome-extension):/);

      $locationProvider.html5Mode({
        enabled: false,
        requireBase: false
      });
    })

    .constant('config', {
      // Main routes to pages
      frontUrl: '#dashboard',
      employeesUrl: '#employees',
      vacationsUrl: '#vacation',
      documentsUrl: '#documents',
      petitionsUrl: '#petitions',
      // Petitions list
      petitionsListUrl: 'admin/api/petition',
      petitionsPostUrl: 'admin/api/petition/create',

      documentsListUrl: 'admin/api/document',
      checkInUrl: 'admin/api/user/check-in',
      checkOutUrl: 'admin/api/user/check-out',
      checkedInList: 'admin/api/user/checked-in',
      presenceState: 'admin/api/user/change-presence-state',
      checkedOutList: 'admin/api/user/checked-out',
      teamsUrl: 'admin/api/user/team',
      loginPathUrl: 'admin/user/login?redirect=true',
      logoutPathUrl: 'admin/user/logout?redirect=true',
      status: 'admin/api/user/check-state',
      holidaysUrl: 'admin/api/holiday/' + new Date().getFullYear(),
      totalVacation: '20'
    })

    .controller('GetDateCtrl', function ($scope) {
      $scope.date = new Date();
    })

;




