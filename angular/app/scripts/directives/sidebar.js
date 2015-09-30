'use strict';
/**
 * @ngdoc function
 * @name d8intranetApp.directive:inputEffects
 * @description
 * # inputEffects
 * Directive of the d8intranetApp
 */

angular.module('d8intranetApp')
  .directive('rollMenu', ['$document', function ($document) {
    return {
      link: function (scope, element) {
        $(element).on('click', function () {
          $('.global-wrapper').toggleClass('rolled-bar');
        })
      }
    }
  }])

  .directive('ngBlur', ['$document', function ($document) {
    return {
      link: function (scope, element, attrs) {
        element.bind('blur', function () {
          scope.inputSelected = true;
          element[0].value ? scope.inputSelected = true : scope.inputSelected = false
        })
      }
    }
  }])

  .directive('chosen', function () {
    var linker = function (scope, element, attr) {
      scope.$watch('teamFilter', function () {
        element.trigger('chosen:updated');
      });
      // Enable Choosen jQuery plugin for styling select element
      element.chosen();
    };

    return {
      restrict: 'A',
      link: linker
    }
  })

  .directive('vacationsLeft', function () {
    var progressLeft = function (scope, element, attrs) {
      element.height((attrs.daysLeft / attrs.daysTotal) * 100 + '%');
      $('.vacation-days-left').css('bottom', ((attrs.daysLeft / attrs.daysTotal) * 100 + '%'));
    };

    return {
      restrict: 'A',
      link: progressLeft
    }
  })

  .directive('showTableData', function () {
      var hideShowElement = function(scope, element, attrs) {
        element.bind('click', function(){
          if($(this).next().hasClass('show-table-data')) {
            $(this).removeClass('active-table-data');
            $(this).next().removeClass('show-table-data');
          }
          else {
            $('.year-table').find('.months').removeClass('show-table-data');
            $('.year-table').find('.user-name').removeClass('active-table-data');
            $(this).addClass('active-table-data');
            $(this).next().addClass('show-table-data');
          }
        });
      };

      return {
        restrict: 'A',
        link: hideShowElement
      }
  })
;
