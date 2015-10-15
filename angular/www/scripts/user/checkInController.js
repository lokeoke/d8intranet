'use strict';

/**
 * @ngdoc function
 * @name d8intranetApp.controller:MainCtrl
 * @description
 * # MainCtrl
 * Controller of the d8intranetApp
 */
angular.module('d8intranetApp')

  .controller('CheckInController', function ($scope, $http, $rootScope, $compile, config) {

    //$rootScope.messageRequest = false;

    //console.log($rootScope.messageRequest)

    $scope.checkIn = function () {

      if (!$rootScope.jira) {
        $rootScope.showModalWindow = true;

        $rootScope.modalHeaderTitle = 'Whoa!';
        $rootScope.modalMessageType = 'error';
        $rootScope.modalMessageSubtitle = 'Seems don\'t filled JIRA at yesterday!';
        $rootScope.modalMessage = 'Please fill JIRA and reload page.';
      }

      else {
        $rootScope.dataLoaded = false;

        console.log($rootScope.dataLoaded)

        var dataObj = {"message": "Yo! Bitch"};
        var res = $http.post(config.checkInUrl, dataObj);

        res.success(function (data, status, headers, config) {
          $scope.message = data;
          $rootScope.dataLoaded = true;
        });

        res.error(function (data, status, headers, config) {
          console.log("failure message: " + JSON.stringify({data: data}));

          $rootScope.showModalWindow = true;

          $rootScope.modalHeaderTitle = 'Whoops!';
          $rootScope.modalMessageType = 'error';
          $rootScope.modalMessageSubtitle = 'Can\'t get data from server';
          $rootScope.modalMessage = 'Please contact system administrator or developers :)';

          $rootScope.dataLoaded = true;
        });
      }
    };
  });
