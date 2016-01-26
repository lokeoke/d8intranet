'use strict';
/**
 * @ngdoc function
 * @name d8intranetApp.filter:filterByPosition
 * @description
 * # filterByPosition
 * Filter of the d8intranetApp
 */

angular.module('d8intranetApp')
  .service('formatUserData', function (getHolidays, config) {

    var formatUserData = {
      // -----------------------------------------------------------------------
      // Method which return total days of vacations, sick days, etc. for single users
      //
      // @parameters:
      //   statisticsType [object] -  which contain formatted data
      //   datesType [string] - field which contain type of data which should
      //                        be formatted, for example field 'remoteWorkDays';
      getTotalDays: function (statisticsType, datesType) {
        var totalDaysYear = 0;
        var totalVacationYear = 0;

        angular.forEach(statisticsType, function (totalDays) {
          if (totalDays != undefined) {
            angular.forEach(totalDays[datesType], function (value, key) {

              if (key == 'businessDays') {
                totalVacationYear += value;
              }

              else if (key == 'totalMonthDays') {
                totalDaysYear += value;
              }
            });
          }
        });

        return {
          totalDaysYear: totalDaysYear,
          totalVacationYear: totalVacationYear
        }
      },

      /**
       * Split date range into months ranges.
       *
       * @param {Object} statisticType
       *   Contain formatted date range and state.
       *
       * @returns {Array}
       *   Date ranges slit by months.
       */
      getMonthRanges: function (statisticType) {
        var dateStart = new Date(statisticType.start_date);
        var dateEnd = new Date(statisticType.end_date);

        var timeStart = dateStart.getTime();
        var timeEnd = dateEnd.getTime();

        var out = [];
        var milestones = [timeStart];

        var dateEndMonth = new Date(dateStart);
        dateEndMonth.setHours(0, 0, 0, 0);
        dateEndMonth.setDate(1);
        dateEndMonth.setMonth(dateEndMonth.getMonth() + 1);
        var timeEndMonth = dateEndMonth.getTime();

        while (timeEndMonth < timeEnd) {
          milestones.push(timeEndMonth);
          dateEndMonth.setMonth(dateEndMonth.getMonth() + 1);
          timeEndMonth = dateEndMonth.getTime();
        }

        milestones.push(timeEnd);

        var count = milestones.length;
        for (var i = 1; i < count; i++) {
          out.push({
            'start_date': milestones[i - 1],
            'end_date': milestones[i] - 1,
            'state': statisticType.state
          });
        }

        return out;
      },

      // Format data
      formatStatisticsData: function (formattedObject, statisticType, holidays) {

        var datesStatesCollection = {};

        var year = formatUserData.getYearNumber(formatUserData.reformatDate(statisticType.start_date));
        var month = formatUserData.getMonthNumber(formatUserData.reformatDate(statisticType.start_date));
        var dateStart = formatUserData.getDateNumber(formatUserData.reformatDate(statisticType.start_date));
        var dateEnd = formatUserData.getDateNumber(formatUserData.reformatDate(statisticType.end_date));

        // If future formatted object is undefined, create it
        if (formattedObject[year] == undefined) {
          formattedObject[year] = {};
        }
        if (formattedObject[year][month] == undefined) {
          formattedObject[year][month] = {
            totalMonthDays: 0,
            multipleDates: [], // this property will be an array to collect multiple data
            businessDays: 0
          };
        }

        datesStatesCollection[year] = {};
        datesStatesCollection[year][month] = {
          date: '',
          statusDate: ''
        };

        // If user has only ony booked date
        if (dateStart == dateEnd) {
          // Add date and status into new object
          // in case if there are several dates in one month
          datesStatesCollection[year][month].date = dateStart;
          datesStatesCollection[year][month].statusDate = statisticType.state;

          // Add object into new property - multiple dates
          // object goes into array
          formattedObject[year][month].multipleDates.push(datesStatesCollection[year][month]);
          formattedObject[year][month].totalMonthDays++;

          if (holidays != undefined) {
            formattedObject[year][month].businessDays++
          }

        }

        // Calculate total days
        // output range between two dates
        else {
          // Other dates
          formattedObject[year][month].totalMonthDays += dateEnd - dateStart + 1;
          datesStatesCollection[year][month].date = dateStart + '-' + dateEnd;
          datesStatesCollection[year][month].statusDate = statisticType.state;
          formattedObject[year][month].multipleDates.push(datesStatesCollection[year][month]);
          // Vacations only
          // We need to calculate only business days not all calendar
          if (holidays != undefined) {
            formattedObject[year][month].businessDays += formatUserData.calcBusinessDays(statisticType.start_date, statisticType.end_date, holidays);
          }
        }
        return formattedObject[year][month];
      },


      calcBusinessDays: function (start, end, holidays) {
        // This makes no effort to account for holidays
        // Counts end day, does not count start day
        // make copies we can normalize without changing passed in objects
        var startDate = new Date(start),
          endDate = new Date(end),
          day,
          totalDays = 0,
          paidCount = 0;


        // normalize both start and end to beginning of the day
        startDate.setHours(0, 0, 0, 0);
        endDate.setHours(0, 0, 0, 0);

        for (var i = 0; i < holidays.length; i++) {
          var holiday = holidays[i].getDay();

          if (holidays[i] >= startDate && holidays[i] <= endDate && holiday != 0 && holiday != 6) {
            paidCount++;
          }
        }

        var current = new Date(startDate);

        // loop through each day, checking
        while (current <= endDate) {
          day = current.getDay();
          if (day >= 1 && day <= 5) {
            totalDays++;
          }
          current.setDate(current.getDate() + 1);
        }

        return totalDays - paidCount;
      },

      // -----------------------------------------------------------------------
      // Method which return object with 12 month names
      //
      // @parameters:
      //   calendarMonths [object] -  which will be filled with property
      //    - monthName: Contain month short name;
      setMonths: function () {
        var calendarMonths = [];
        var months = ["Jan", "Feb", "Mar", "Apr", "May", "June",
          "July", "Aug", "Sep", "Oct", "Nov", "Dec"];

        for (var i = 0; i < 12; i++) {
          calendarMonths[i] = {"monthName": months[i]};
        }

        return calendarMonths;
      },

      setYears: function (yearsRange) {
        var calendarYears = [];

        for (var i = yearsRange[0]; i <= yearsRange[1]; i++) {
          calendarYears[i] = formatUserData.setMonths();
        }

        return calendarYears;
      },


      formattedUser: function (users, holidays, yearsRange) {
        // Go through all users in JSON
        angular.forEach(users, function (employee) {

          var sickDays = {},
            journeyDays = {},
            remoteWorkDays = {},
            dayOffDays = {},
            workOffDays = {},
            vacationDays = {};

          var years = formatUserData.setYears(yearsRange.data);

          // Get Vacation days
          // ---------------------------------------------------------------------
          angular.forEach(employee.field_user_vacation, function (vacation) {
            var vacationRanges = formatUserData.getMonthRanges(vacation);

            angular.forEach(vacationRanges, function (vacation) {
              var year = formatUserData.getYearNumber(formatUserData.reformatDate(vacation.start_date));
              var month = formatUserData.getMonthNumber(formatUserData.reformatDate(vacation.start_date));
              years[year][month].vacationDays = formatUserData.formatStatisticsData(vacationDays, vacation, holidays);
            });
          });

          // Get DaysOff days
          // ---------------------------------------------------------------------
          angular.forEach(employee.field_user_dayoff, function (dayOff) {
            var dayOffRanges = formatUserData.getMonthRanges(dayOff);

            angular.forEach(dayOffRanges, function (dayOff) {
              var year = formatUserData.getYearNumber(formatUserData.reformatDate(dayOff.start_date));
              var month = formatUserData.getMonthNumber(formatUserData.reformatDate(dayOff.start_date));
              years[year][month].dayOffDays = formatUserData.formatStatisticsData(dayOffDays, dayOff);
            });
          });

          // Get Sick days
          // ---------------------------------------------------------------------
          angular.forEach(employee.field_user_sick, function (sick) {
            var sickRanges = formatUserData.getMonthRanges(sick);

            angular.forEach(sickRanges, function (sick) {
              var year = formatUserData.getYearNumber(formatUserData.reformatDate(sick.start_date));
              var month = formatUserData.getMonthNumber(formatUserData.reformatDate(sick.start_date));
              years[year][month].sickDays = formatUserData.formatStatisticsData(sickDays, sick);
            });
          });

          // Get Duty Journey days
          // -------------------------------------------------------------------
          angular.forEach(employee.field_user_duty_journey, function (journey) {
            var journeyRanges = formatUserData.getMonthRanges(journey);

            angular.forEach(journeyRanges, function (journey) {
              var year = formatUserData.getYearNumber(formatUserData.reformatDate(journey.start_date));
              var month = formatUserData.getMonthNumber(formatUserData.reformatDate(journey.start_date));
              years[year][month].journeyDays = formatUserData.formatStatisticsData(journeyDays, journey);
            });
          });

          // Get Remote work days
          // -------------------------------------------------------------------
          angular.forEach(employee.field_user_remote_work, function (remoteWork) {
            var remoteWorkRanges = formatUserData.getMonthRanges(remoteWork);

            angular.forEach(remoteWorkRanges, function (remoteWork) {
              var year = formatUserData.getYearNumber(formatUserData.reformatDate(remoteWork.start_date));
              var month = formatUserData.getMonthNumber(formatUserData.reformatDate(remoteWork.start_date));
              years[year][month].remoteWorkDays = formatUserData.formatStatisticsData(remoteWorkDays, remoteWork);
            });
          });

          // Get WorkOff work days
          // -------------------------------------------------------------------
          angular.forEach(employee.field_user_work_off, function (workOff) {
            var workOffRanges = formatUserData.getMonthRanges(workOff);

            angular.forEach(workOffRanges, function (workOff) {
              var year = formatUserData.getYearNumber(formatUserData.reformatDate(workOff.start_date));
              var month = formatUserData.getMonthNumber(formatUserData.reformatDate(workOff.start_date));
              years[year][month].workOffDays = formatUserData.formatStatisticsData(workOffDays, workOff);
            });
          });

          // Add new property into main employee object
          // which collect all table data for each user.
          employee.calendarYears = years;

          // Add property with sum of Vacations, Days off, etc. fields
          employee.timeRanges = [];

          angular.forEach(years, function (months, year) {
            if (employee.timeRanges[year] == undefined) {
              employee.timeRanges[year] = {};
            }
            employee.timeRanges[year].totalVacation = formatUserData.getTotalDays(months, 'vacationDays');
            employee.timeRanges[year].totalDaysOff = formatUserData.getTotalDays(months, 'dayOffDays');
            employee.timeRanges[year].totalWorkOff = formatUserData.getTotalDays(months, 'workOffDays');
            employee.timeRanges[year].totalSick = formatUserData.getTotalDays(months, 'sickDays');
            employee.timeRanges[year].totalJourney = formatUserData.getTotalDays(months, 'journeyDays');
            employee.timeRanges[year].totalRemoteWork = formatUserData.getTotalDays(months, 'remoteWorkDays');
          });


        });
      },

      reformatDate: function (stringDate) {
        return new Date(stringDate).getTime();
      },


      // -----------------------------------------------------------------------
      // Method which return month number
      // Accepted parameter - Timestamp, example: 1420070400 [01/01/2015]
      // return 01

      getMonthNumber: function (timestamp) {
        return new Date(timestamp).getMonth();
      },

      // -----------------------------------------------------------------------
      // Method which return date number
      // Accepted parameter - Timestamp, example: 1420070400 [01/01/2015]
      // return 01

      getDateNumber: function (timestamp) {
        return new Date(timestamp).getDate();
      },

      // -----------------------------------------------------------------------
      // Method which return date number
      // Accepted parameter - Timestamp, example: 1420070400 [01/01/2015]
      // return 2015

      getYearNumber: function (timestamp) {
        return new Date(timestamp).getFullYear();
      }
    };
    return formatUserData;
  });
