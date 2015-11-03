'use strict';

/**
 * @ngdoc function
 * @name d8intranetApp.controller:MainCtrl
 * @description
 * # MainCtrl
 * Controller of the d8intranetApp
 */
angular.module('d8intranetApp')

    .controller('CheckInController', function ($scope, $http, $rootScope, $compile, $cookies, $timeout, modalWindow, config, getCheckedInUsers, checkState) {

      getCheckedInUsers.getCheckedIn(config.checkedInList).then(function (data) {
        $rootScope.checkedInUserList = data;
      });

      $scope.checkIn = function () {
        if (!$rootScope.jira) {

          modalWindow.setMessage(
              'error',
              'Oops..it seems that you haven\'t filled in a JIRA for the past day.',
              'Please fill it in and refresh the page.'
          );
        }

        else {

          var dataObj = {"message": "Check me in"};
          var res = $http.post(config.checkInUrl, dataObj);

          res.success(function (data) {
            $scope.message = data;

            if (data.status) {
              modalWindow.setMessage(
                  'success',
                  'Check-in is done.',
                  data.message
              );

              getCheckedInUsers.getCheckedIn(config.checkedInList).then(function (d) {
                $rootScope.checkedInUserList = d;
              });

              getCheckedInUsers.getCheckedOut(config.checkedOutList).then(function(d){
                $rootScope.checkedOutUserList = d;
              });

              checkState.getState(config.status).then(function (data) {
                $rootScope.checkedIn = data.checked_in;
              });

            }
            else {
              modalWindow.setMessage(
                  'error',
                  'Something went wrong!',
                  data.message
              );
            }
          });

          res.error(function (data) {
            //console.log("failure message: " + JSON.stringify({data: data}));
            modalWindow.setMessage(
                'error',
                'Can\'t get data from server',
                'Please contact system administrator or developers :)'
            );
          });
        }
      };

      $scope.checkOut = function () {
        var requestObject = {"message": "Check me out!"};
        var req = $http.post(config.checkOutUrl, requestObject);

        req.success(function (data) {

          modalWindow.setMessage(
              'success',
              'Check-out is done.',
              data.message
          );

          getCheckedInUsers.getCheckedIn(config.checkedInList).then(function (d) {
            $rootScope.checkedInUserList = d;
          });

          getCheckedInUsers.getCheckedOut(config.checkedOutList).then(function(d){
            $rootScope.checkedOutUserList = d;
            $rootScope.checkedOutUserCount = getCheckedInUsers.getObjectSize(d);
          });

          checkState.getState(config.status).then(function (data) {
            $rootScope.checkedIn = data.checked_in;
          });
        });

        req.error(function (data, status, headers) {
          modalWindow.setMessage(
              'error',
              'Something went wrong!',
              data.message
          );
        });
      }
    });
