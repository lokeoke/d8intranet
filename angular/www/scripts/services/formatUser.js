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

        // Format data
        formatStatisticsData: function (formattedObject, statisticType, holidays) {

          var datesStatesCollection = {};
          var holidaysList = holidays;

          var month = formatUserData.getMonthNumber(formatUserData.reformatDate(statisticType.start_date));
          var dateStart = formatUserData.getDateNumber(formatUserData.reformatDate(statisticType.start_date));
          var dateEnd = formatUserData.getDateNumber(formatUserData.reformatDate(statisticType.end_date));

          // If future formatted object is undefined, create it
          if (formattedObject[month] == undefined) {
            formattedObject[month] = {
              totalMonthDays: 0,
              multipleDates: [], // this property will be an array to collect multiple data
              businessDays: ''
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

            if (holidaysList != undefined) {
              formattedObject[month].businessDays++
            }

          }

          // Calculate total days
          // output range between two dates
          else {
              // Other dates
              formattedObject[month].totalMonthDays += dateEnd - dateStart + 1;
              datesStatesCollection[month].date = dateStart + '-' + dateEnd;
              datesStatesCollection[month].statusDate = statisticType.state;
              formattedObject[month].multipleDates.push(datesStatesCollection[month]);
            // Vacations only
            // We need to calculate only business days not all calendar
            if (holidaysList != undefined) {
                formattedObject[month].businessDays = formatUserData.calcBusinessDays(statisticType.start_date, statisticType.end_date, holidays);
            }
          }

          return formattedObject[month];
        },


        calcBusinessDays: function (start, end, hlDays) {

          var holidays = hlDays;

          // This makes no effort to account for holidays
          // Counts end day, does not count start day
          // make copies we can normalize without changing passed in objects
          var start = new Date(start),
              end = new Date(end),
              day,
              totalDays = 0,
              paidCount = 0;


          // normalize both start and end to beginning of the day
          start.setHours(0, 0, 0, 0);
          end.setHours(0, 0, 0, 0);

          for (var i =0; i < holidays.length; i++) {
            var holiday = holidays[i].getDay();

            if (holidays[i] >= new Date(start) && holidays[i] <= new Date(end) && holiday != 0 && holiday != 6) {
              paidCount ++;
            }
          }

          var current = new Date(start);
          current.setDate(current.getDate());

          // loop through each day, checking
          while (current <= end) {
            day = current.getDay();
            if (day >= 1 && day <= 5) {
              ++totalDays;
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
        setMonths: function (calendarMonths) {
          var months = ["Jan", "Feb", "Mar", "Apr", "May", "June",
            "July", "Aug", "Sep", "Oct", "Nov", "Dec"];

          for (var i = 0; i < 12; i++) {
            calendarMonths[i] = {"monthName": months[i]};
          }

          return calendarMonths;
        },


        formattedUser: function (users, holidays) {
          // Go through all users in JSON
          angular.forEach(users, function (employee) {

            var calendarMonths = {},
                sickDays = {},
                journeyDays = {},
                remoteWorkDays = {},
                dayOffDays = {},
                workOffDays = {},
                vacationDays = {};

            employee.timeRanges = {};

            var months = formatUserData.setMonths(calendarMonths);

            // Get Vacation days
            // ---------------------------------------------------------------------
            angular.forEach(employee.field_user_vacation, function (vacation) {
              var month = formatUserData.getMonthNumber(formatUserData.reformatDate(vacation.start_date));
              months[month].vacationDays = formatUserData.formatStatisticsData(vacationDays, vacation, holidays);
            });


            // Get DaysOff days
            // ---------------------------------------------------------------------
            angular.forEach(employee.field_user_dayoff, function (dayoff) {
              var month = formatUserData.getMonthNumber(formatUserData.reformatDate(dayoff.start_date));
              months[month].dayOffDays = formatUserData.formatStatisticsData(dayOffDays, dayoff);
            });

            // Get Sick days
            // ---------------------------------------------------------------------
            angular.forEach(employee.field_user_sick, function (sick) {
              var month = formatUserData.getMonthNumber(formatUserData.reformatDate(sick.start_date));
              months[month].sickDays = formatUserData.formatStatisticsData(sickDays, sick);
            });

            // Get Duty Journey days
            // -------------------------------------------------------------------
            angular.forEach(employee.field_user_duty_journey, function (journey) {
              var month = formatUserData.getMonthNumber(formatUserData.reformatDate(journey.start_date));
              months[month].journeyDays = formatUserData.formatStatisticsData(journeyDays, journey);
            });

            // Get Remote work days
            // -------------------------------------------------------------------
            angular.forEach(employee.field_user_remote_work, function (remoteWork) {
              var month = formatUserData.getMonthNumber(formatUserData.reformatDate(remoteWork.start_date));
              months[month].remoteWorkDays = formatUserData.formatStatisticsData(remoteWorkDays, remoteWork);
            });

            // Get WorkOff work days
            // -------------------------------------------------------------------
            angular.forEach(employee.field_user_work_off, function (workOff) {
              var month = formatUserData.getMonthNumber(formatUserData.reformatDate(workOff.start_date));
              months[month].workOffDays = formatUserData.formatStatisticsData(workOffDays, workOff);
            });

            // Add new property into main employee object
            // which collect all table data for each user.
            employee.calendarMonths = months;

            // Add property with sum of Vacations, Days off, etc. fields
            employee.timeRanges.totalVacation = formatUserData.getTotalDays(months, 'vacationDays');
            employee.timeRanges.totalDaysOff = formatUserData.getTotalDays(months, 'dayOffDays');
            employee.timeRanges.totalWorkOff = formatUserData.getTotalDays(months, 'workOffDays');
            employee.timeRanges.totalSick = formatUserData.getTotalDays(months, 'sickDays');
            employee.timeRanges.totalJourney = formatUserData.getTotalDays(months, 'journeyDays');
            employee.timeRanges.totalRemoteWork = formatUserData.getTotalDays(months, 'remoteWorkDays');
          });
        },

        reformatDate: function (stringDate) {
          return new Date(stringDate).getTime() / 1000;
        },


        // -----------------------------------------------------------------------
        // Method which return month number
        // Accepted parameter - Timestamp, example: 1420070400 [01/01/2015]
        // return 01

        getMonthNumber: function (timestamp) {
          return new Date(timestamp * 1000).getMonth();
        },

        // -----------------------------------------------------------------------
        // Method which return date number
        // Accepted parameter - Timestamp, example: 1420070400 [01/01/2015]
        // return 01

        getDateNumber: function (timestamp) {
          return new Date(timestamp * 1000).getDate();
        },

        // -----------------------------------------------------------------------
        // Method which return date number
        // Accepted parameter - Timestamp, example: 1420070400 [01/01/2015]
        // return 2015

        getYearNumber: function (timestamp) {
          return new Date(timestamp * 1000).getFullYear();
        }
      };
      return formatUserData;
    });
