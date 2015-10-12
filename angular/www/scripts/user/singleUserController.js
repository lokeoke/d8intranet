'use strict';

/**
 * @ngdoc function
 * @name d8intranetApp.controller:MainCtrl
 * @description
 * # MainCtrl
 * Controller of the d8intranetApp
 */
angular.module('d8intranetApp')
  .controller('singleUserController', function ($scope, $http, $routeParams, getJsonData, formatUserData, config) {

    getJsonData.getUsers().then(function (data) {
      $scope.users = data;

      formatUserData.formattedUser($scope.users);

      $scope.user = {};
      var cameToCompany = '';

      angular.forEach($scope.users, function (user, title) {
        var calendarMonths = {};

        // Get current user
        if (user.uid[0].value == $routeParams.userId) {
          $scope.user = user;
          $scope.filteredKeys = {};


          var stamp = user.field_came_to_propeople[0].value;

          cameToCompany = formatUserData.getYearNumber(stamp) + ',' + formatUserData.getMonthNumber(stamp) + ',' + formatUserData.getDateNumber(stamp);

          var today = new Date(),
              past = new Date(cameToCompany);

          angular.forEach(user.timeRanges, function (value, key) {
            $scope.filteredKeys[setStiaticTitle(key)] = value;
          });


          // Get current days of user vacation
          var totalMonthOfWork = calcDate(today, past);

          $scope.totalVacationDays = 0;

          // If user month of work less than 12 month
          // Get current period of user work and subtract current days of vacation
          if (totalMonthOfWork < 12) {
            $scope.totalVacationDays = Math.floor(getWorkPeriod(calcDate(today, past)));

            if ($scope.totalVacationDays > $scope.filteredKeys['Vacation']) {
              $scope.vacationDaysLeft = $scope.totalVacationDays - $scope.filteredKeys['Vacation'];
            }
            else {
              $scope.vacationDaysLeft = 0;
            }
          }
          else {
            $scope.totalVacationDays = config.totalVacation;
            $scope.vacationDaysLeft = config.totalVacation - $scope.filteredKeys['Vacation'];
          }
          return $scope.user;
        }
      });

      function setStiaticTitle(title) {
        switch (title) {
          case 'totalVacation':
            return 'Vacation';
            break;
          case 'totalDaysOff':
            return 'Day off';
            break;
          case 'totalSick':
            return 'Sick days';
            break;
          case 'totalJourney':
            return 'Duty Journey';
            break;
          case 'totalRemoteWork':
            return 'Remote work';
            break;
          case 'totalWorkOff':
            return 'Work Off';
            break;
          default:
            return '';
            break;
        }
      }

      // Create new variable where we replace all '-' with ','
      //var formattedDate = ;
      var today = new Date();
      var past = new Date(cameToCompany);
      var months;
      var vacationForNow = 0;

      // Calculate amount of months from user started to work in company
      function calcDate(date1, date2) {

        var diff = Math.floor(date1.getTime() - date2.getTime());
        var day = 1000 * 60 * 60 * 24;

        var days = Math.floor(diff / day);
        var years = Math.floor(months / 12);
        months = Math.floor(days / 31);
        return months;
      }

      // Get amount of Years and Months of user work in company
      function getWorkPeriod(month) {
        $scope.yearsOfWork = Math.floor(month / 12);
        $scope.monthsOfWork = month % 12;

        // Return count of available days
        // 18 days divide by 12 month and multiply by month of work
        return config.totalVacation / 12 * $scope.monthsOfWork;
      }

      getWorkPeriod(calcDate(today, past));
    });
  })

  .controller('ShowUserCtrl', function ($scope, $routeParams) {
    $scope.user_id = $routeParams.userId;
  });
