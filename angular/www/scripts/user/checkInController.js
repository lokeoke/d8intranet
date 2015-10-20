'use strict';

/**
 * @ngdoc function
 * @name d8intranetApp.controller:MainCtrl
 * @description
 * # MainCtrl
 * Controller of the d8intranetApp
 */
angular.module('d8intranetApp')

    .controller('CheckInController', function ($scope, $http, $rootScope, $compile, $cookies, $timeout, config, getCheckedInUsers, checkState) {

      getCheckedInUsers.getCheckedIn(config.checkedInList).then(function (data) {
        $rootScope.checkedInUserList = data;
      });

      $scope.checkIn = function () {

        if (!$rootScope.jira) {
          $rootScope.showModalWindow = true;

          $rootScope.modalHeaderTitle = 'Whoa!';
          $rootScope.modalMessageType = 'error';
          $rootScope.modalMessageSubtitle = 'Seems don\'t filled JIRA at yesterday!';
          $rootScope.modalMessage = 'Please fill JIRA and reload page.';
        }

        else {
          //$rootScope.dataLoaded = false;

          var dataObj = {"message": "Check me in"};
          var res = $http.post(config.checkInUrl, dataObj);

          res.success(function (data) {
            $scope.message = data;
            //$rootScope.dataLoaded = true;
            $rootScope.showModalWindow = true;

            if (data.status) {
              $rootScope.modalMessageType = 'success';
              $rootScope.modalMessageSubtitle = 'Checkin success';
              $rootScope.modalMessage = 'You have successfully checked in.';

              getCheckedInUsers.getCheckedIn(config.checkedInList).then(function (d) {
                $rootScope.checkedInUserList = d;
              });

              checkState.getState(config.status).then(function (data) {
                $rootScope.checkedIn = data.checked_in;
              });

            }
            else {
              $rootScope.modalMessageType = 'error';
              $rootScope.modalMessageSubtitle = 'You are not authorized for check in.';
              $rootScope.modalMessage = 'Please login and try again';
            }

          });

          res.error(function (data, status, headers, config) {
            console.log("failure message: " + JSON.stringify({data: data}));

            $rootScope.showModalWindow = true;
            $rootScope.modalMessageType = 'error';
            $rootScope.modalMessageSubtitle = 'Can\'t get data from server';
            $rootScope.modalMessage = 'Please contact system administrator or developers :)';

            //$rootScope.dataLoaded = true;
          });
        }
      };

      $scope.checkOut = function () {
        var requestObject = {"message": "Check me out!"};
        var req = $http.post(config.checkOutUrl, requestObject);

        $rootScope.showModalWindow = false;

        req.success(function (data) {
          getCheckedInUsers.getCheckedIn(config.checkedInList).then(function (d) {
            $rootScope.checkedInUserList = d;
          });
          checkState.getState(config.status).then(function (data) {
            $rootScope.checkedIn = data.checked_in;
          });
        });

        req.error(function (data, status, headers) {
          console.log(data);
        });
      }
    });
