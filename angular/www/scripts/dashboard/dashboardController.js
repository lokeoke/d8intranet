'use strict';

/**
 * @ngdoc function
 * @name d8intranetApp.controller:MainCtrl
 * @description
 * # MainCtrl
 * Controller of the d8intranetApp
 */
angular.module('d8intranetApp')

    .controller('dashboardController', function ($scope, $http, getJsonData, config, getCheckedInUsers, $interval, $rootScope, formatUserData) {

      $scope.pageClass = 'page-home';

      getJsonData.getUsers().then(function (d) {
        $scope.users = d;
        $scope.states = {};
        $scope.availableEmployees = {available: {'count': 0}};

        var haveStatus;

        var today = new Date();
        var currentDate = today.getDate();
        var currentMonth = today.getMonth() + 1;

        $scope.currentYear = today.getFullYear();

        $scope.birthDaysInCurrentMonth = [];
        $scope.birthDaysToday = [];

        console.log($scope.currentYear);


        angular.forEach($scope.users, function (user) {

          // Users birthday
          var userBirthInMonth = new Date(formatUserData.reformatDate(user.field_user_birthday[0].value)).getMonth() + 1;
          var userBirthDayDate = new Date(formatUserData.reformatDate(user.field_user_birthday[0].value)).getDate();


          // Birthdays in current month
          if (userBirthInMonth == currentMonth) {
            $scope.birthDaysInCurrentMonth.push(user);
          }
          // Birthday today
          if (userBirthInMonth == currentMonth && currentDate == userBirthDayDate) {
            $scope.birthDaysToday.push(user);
          }


          // User statuses
          if (user.field_user_status[0] != undefined) {

            var statusKey = user.field_user_status[0].value;
            var statusName = cardName(statusKey);

            $scope.states[statusName] = $scope.states[statusName] || {'count': 0};
            $scope.states[statusName][user.uid[0].value] = user;
            $scope.states[statusName]['count'] += 1;

            // TODO Refactor code bellow
            if (statusKey == 'vacation') {
              var vacationDatesArray = [];
              angular.forEach(user.field_user_vacation, function (dates) {
                vacationDatesArray.push(new Date(dates.end_date));
              });

              $scope.states[statusName][user.uid[0].value]['vacationTillDate'] =
                  $scope.states[statusName][user.uid[0].value]['vacationTillDate']
                  || new Date(Math.max.apply(null, vacationDatesArray));

            }

            if (statusKey == 'business_trip') {
              var tripDatesArray = [];
              angular.forEach(user.field_user_duty_journey, function (dates) {
                tripDatesArray.push(new Date(dates.end_date));
              });

              $scope.states[statusName][user.uid[0].value]['tripTillDate'] =
                  $scope.states[statusName][user.uid[0].value]['tripTillDate']
                  || new Date(Math.max.apply(null, tripDatesArray));
            }
            // TODO end

          }
          else {
            $scope.availableEmployees.available[user.uid[0].value] = user;
            $scope.availableEmployees.available['count'] += 1;
          }
        });





        $scope.availableEmployees.available['title'] = 'Available employees';

        function cardName(status) {
          switch (status) {
            case 'day_off':
              return 'Day off';
              break;
            case 'sick':
              return 'Sick';
              break;
            case 'business_trip':
              return 'Business Trip';
              break;
            case 'remote_work':
              return 'Remote work';
              break;
            case 'vacation':
              return 'Vacation';
              break;
            case 'work_off':
              return 'Work off';
              break;
            default:
              return status;
              break;
          }
        }
      });

      function getCheckedUsers() {
        getCheckedInUsers.getCheckedOut(config.checkedOutList).then(function (data) {
          $rootScope.checkedOutUserList = data;
          $rootScope.checkedOutUserCount = getCheckedInUsers.getObjectSize(data);
        });

        getCheckedInUsers.getCheckedIn(config.checkedInList).then(function (data) {
          $rootScope.checkedInUserList = data;
        });
      }

      getCheckedUsers();

      $interval(function () {
        getCheckedUsers();
      }, '15000');
    });
