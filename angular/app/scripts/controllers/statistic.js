'use strict';

/**
 * @ngdoc function
 * @name d8intranetApp.controller:AboutCtrl
 * @description
 * # AboutCtrl
 * Controller of the d8intranetApp
 */
angular.module('d8intranetApp')
  .controller('VacationCtrl', function ($scope, $http, getJsonData) {

    getJsonData.getUsers().then(function (d) {
      $scope.users = d;

      function getMonthNumber(timestamp) {
        return new Date(timestamp * 1000).getMonth();
      }

      function getDateNumber(timestamp) {
        return new Date(timestamp * 1000).getDate();
      }

      // Format data
      function formatStatisticsData(formattedObject, statisticType) {
        var datesStatesCollection = {};

        var totalDays = 0;
        var month = getMonthNumber(statisticType.start_date);
        var dateStart = getDateNumber(statisticType.start_date);
        var dateEnd = getDateNumber(statisticType.end_date);

        // If future formatted object is undefined, create it
        if (formattedObject[month] == undefined) {
          formattedObject[month] = {
            totalMonthDays: 0,
            multipleDates: [] // this property will be an array to collect multiple data
          };
        }

        datesStatesCollection[month] = {
          date: '',
          statusDate: ''
        };

        // If user has only ony booked date
        if (dateStart == dateEnd) {
          // Add date and status into new object
          // in case if there are several dates in one month
          datesStatesCollection[month].date = dateStart;
          datesStatesCollection[month].statusDate = statisticType.state;

          // Add object into new property - multiple dates
          // object goes into array
          formattedObject[month].multipleDates.push(datesStatesCollection[month]);
          formattedObject[month].totalMonthDays++;
        }

        // Calculate total days
        // output range between two dates
        else {
          formattedObject[month].totalMonthDays += dateEnd - dateStart + 1;
          datesStatesCollection[month].date = dateStart + '-' + dateEnd;
          datesStatesCollection[month].statusDate = statisticType.state;
          formattedObject[month].multipleDates.push(datesStatesCollection[month]);
        }

        return formattedObject[month];
      }

      // -----------------------------------------------------------------------
      // Function which return total days for single users
      //
      // @parameters:
      //   statisticsType [object] -  which contain formatted data
      //   datesType [string] - field which contain type of data which should
      //                        be formatted, for example field 'remoteWorkDays';

      function getTotalDays(statisticsType, datesType) {
        var totalDaysYear = 0;

        angular.forEach(statisticsType, function (totalDays) {
          if (totalDays != undefined) {
            angular.forEach(totalDays[datesType], function (value, key) {
              if (key == 'totalMonthDays') {
                totalDaysYear += value;
              }
            });
          }
        });

        return totalDaysYear;
      }

      // Go through all users in JSON
      angular.forEach($scope.users, function (employee) {
        $scope.members = employee;

        var calendarMonths = {},
          sickDays = {},
          journeyDays = {},
          remoteWorkDays = {},
          dayOffDays = {},
          workOffDays = {},
          vacationDays = {},
          months = ["Jan", "Feb", "Mar", "Apr", "May", "June", "July", "Aug", "Sep", "Oct", "Nov", "Dec"];

        for (var i = 0; i < 12; i++) {
          calendarMonths[i] = {"monthName": months[i]};
        }

        // Get Vacation days
        // ---------------------------------------------------------------------
        angular.forEach($scope.members.field_vacation, function (vacation) {
          var month = getMonthNumber(vacation.start_date);
          calendarMonths[month].vacationDays = formatStatisticsData(vacationDays, vacation);
        });

        // Get DaysOff days
        // ---------------------------------------------------------------------
        angular.forEach($scope.members.field_dayoff, function (dayoff) {
          var month = getMonthNumber(dayoff.start_date);
          calendarMonths[month].dayOffDays = formatStatisticsData(dayOffDays, dayoff);
        });
        //
        // Get Sick days
        // ---------------------------------------------------------------------
        angular.forEach($scope.members.field_sick, function (sick) {
          var month = getMonthNumber(sick.start_date);
          calendarMonths[month].sickDays = formatStatisticsData(sickDays, sick);
        });

        // Get Duty Journey days
        // -------------------------------------------------------------------
        angular.forEach($scope.members.field_duty_journey, function (journey) {
          var month = getMonthNumber(journey.start_date);
          calendarMonths[month].journeyDays = formatStatisticsData(journeyDays, journey);
        });

        // Get Remote work days
        // -------------------------------------------------------------------
        angular.forEach($scope.members.field_remote_work, function (remoteWork) {
          var month = getMonthNumber(remoteWork.start_date);
          calendarMonths[month].remoteWorkDays = formatStatisticsData(remoteWorkDays, remoteWork);
        });

        // Get WorkOff work days
        // -------------------------------------------------------------------
        angular.forEach($scope.members.field_work_off, function (workOff) {
          var month = getMonthNumber(workOff.start_date);
          calendarMonths[month].workOffDays = formatStatisticsData(workOffDays, workOff);
        });

        // Add new property into main employee object
        // which collect all table data for each user.
        employee.calendarMonths = calendarMonths;

        // Add property with sum of Vacations, Days off, etc. fields
        employee.totalVacation = getTotalDays(calendarMonths, 'vacationDays');
        employee.totalDaysOff = getTotalDays(calendarMonths, 'dayOffDays');
        employee.totalWorkOff = getTotalDays(calendarMonths, 'workOffDays');
        employee.totalSick = getTotalDays(calendarMonths, 'sickDays');
        employee.totalJourney = getTotalDays(calendarMonths, 'journeyDays');
        employee.totalRemoteWork = getTotalDays(calendarMonths, 'remoteWorkDays');

      });

      $scope.$watch('teamFilter', function (newValue, oldValue) {
        $scope.filterBy = newValue;
      });

      // Create teams array for filtering
      $scope.teams = [];
      angular.forEach($scope.users, function (emplyee) {
        if (!$scope.teams[emplyee.field_team[0].value]) {
          $scope.teams[emplyee.field_team[0].value] = emplyee.field_team[0]
        }
      });

      $scope.teams = $scope.teams.filter(function (n) {
        return n != undefined
      });

      $scope.teams.unshift({"team_name": 'all', "value": "0"});
      $scope.teamFilter = {selectedOption: $scope.teams[0].value};
    });

    $scope.tabs = [
      {
        title: 'Vacations',
        url: 'views/vacations.html'
      },
      {
        title: 'Days off',
        url: 'views/dayoffs.html'
      },
      {
        title: 'Sick days',
        url: 'views/sick_days.html'
      },
      {
        title: 'Duty Journey',
        url: 'views/journey.html'
      },
      {
        title: 'Remote work',
        url: 'views/remote.html'
      },
      {
        title: 'Work off',
        url: 'views/work_off.html'
      }
    ];

    $scope.currentTab = 'views/vacations.html';

    $scope.onClickTab = function (tab) {
      $scope.currentTab = tab.url;
    };

    $scope.isActiveTab = function (tabUrl) {
      return tabUrl == $scope.currentTab;
    };

  });
