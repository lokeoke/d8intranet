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

        console.log('check IN');

        if (!$rootScope.jira) {

          modalWindow.setMessage(
              'error',
              'Oops..it seems that you haven\'t filled in a JIRA for the past day.',
              'Please fill it in and refresh the page.'
          );
        }

        else {
          //$rootScope.dataLoaded = false;

          var dataObj = {"message": "Check me in"};
          var res = $http.post(config.checkInUrl, dataObj);

          res.success(function (data) {
            $scope.message = data;

            if (data.status) {
              modalWindow.setMessage(
                  'success',
                  'Check-in is done.',
                  'You have successfully signed in.'
              );

              getCheckedInUsers.getCheckedIn(config.checkedInList).then(function (d) {
                $rootScope.checkedInUserList = d;
              });

              checkState.getState(config.status).then(function (data) {
                $rootScope.checkedIn = data.checked_in;
              });

            }
            else {
              modalWindow.setMessage(
                  'error',
                  'Something went wrong!',
                  'Please report about an issue, thanks :)'
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
              'You have successfully signed out!'
          );

          getCheckedInUsers.getCheckedIn(config.checkedInList).then(function (d) {
            $rootScope.checkedInUserList = d;
          });

          checkState.getState(config.status).then(function (data) {
            $rootScope.checkedIn = data.checked_in;
          });
        });

        req.error(function (data, status, headers) {
          modalWindow.setMessage(
              'error',
              'Something went wrong!',
              'Please report about an issue, thanks :)'
          );
        });
      }
    });
