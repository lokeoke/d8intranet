'use strict';

/**
 * @ngdoc function
 * @name d8intranetApp.controller:MainCtrl
 * @description
 * # MainCtrl
 * Controller of the d8intranetApp
 */
angular.module('d8intranetApp')
  .controller('UserCtrl', function ($scope, $http, $routeParams, getJsonData, config) {

    getJsonData.getUsers().then(function (d) {

      function getDateNumber(timestamp) {
        return new Date(timestamp * 1000).getDate();
      }

      function getMonthNumber(timestamp) {
        return new Date(timestamp * 1000).getMonth();
      }

      function getYearNumber(timestamp) {
        return new Date(timestamp * 1000).getFullYear();
      }

      $scope.users = d;
      $scope.user = {};
      var cameToCompany = '';
      var currentVacation;

      angular.forEach($scope.users, function (user, title) {
        // Get current user
        if (user.uid[0].value == $routeParams.userId) {
          $scope.user = user;

          var stamp = user.field_came_to_propeople[0].value;

          cameToCompany = getYearNumber(stamp) + ',' + getMonthNumber(stamp) + ',' + getDateNumber(stamp);

          var today = new Date();
          var past = new Date(cameToCompany);

          $scope.filteredKeys = {};

          angular.forEach(user, function (value, title) {

            if (stateTitle(title)) {
              $scope.filteredKeys[stateTitle(title)] = value;

              if (title == 'field_vacation') {
                var totalVacationDays = 0;
                for (var k = 0; k < value.length; k++) {
                  totalVacationDays += getDateNumber(value[k].end_vacation) - getDateNumber(value[k].start_vacation);
                }
                $scope.filteredKeys[stateTitle(title)] = totalVacationDays;
              }

              else if (title == 'field_duty_journey') {
                var journeyDays = 0;
                for (var j = 0; j < value.length; j++) {
                  journeyDays = getDateNumber(value[j].end_date) - getDateNumber(value[j].end_date);
                }
                $scope.filteredKeys[stateTitle(title)] = journeyDays;
              }
              else {
                $scope.filteredKeys[stateTitle(title)] = value.length;
              }

            }
          });

          // Get current days of user vacation
          var totalMonthOfWork = calcDate(today, past);
          $scope.totalVacationDays = 0;

          // If user month of work less than 12 month
          // Get current period of user work and subtract current days of vacation
          if (totalMonthOfWork < 12) {
            $scope.totalVacationDays = Math.floor(getWorkPeriod(calcDate(today, past)));
            $scope.vacationDaysLeft = $scope.totalVacationDays - $scope.filteredKeys['Vacation'];
          }
          else {
            $scope.totalVacationDays = config.totalVacation;
            $scope.vacationDaysLeft = config.totalVacation - $scope.filteredKeys['Vacation'];
          }
          return $scope.user;
        }
      });

      function stateTitle(title) {
        switch (title) {
          case 'field_vacation':
            return 'Vacation';
            break;
          case 'field_dayoff':
            return 'Day off';
            break;
          case 'field_sick':
            return 'Sick days';
            break;
          case 'field_duty_journey':
            return 'Duty Journey';
            break;
          case 'field_remote_work':
            return 'Remote work';
            break;
          case 'field_work_off':
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
