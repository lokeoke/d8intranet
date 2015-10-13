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
      var barHeight = (attrs.daysLeft / attrs.daysTotal) * 100 + '%';
      element.height(barHeight);
      $('.vacation-days-left').css('bottom', ((attrs.daysLeft / attrs.daysTotal) * 100 + '%'));
    };

    return {
      restrict: 'A',
      link: progressLeft
    }
  })

  .directive('tableHeadline', function() {
    return {
      restrict: 'A',
      templateUrl: 'templates/tableHeader.html'
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

.directive('modal', function(){
    return{
      template: '<div class="modal view-animate">' +
                  '<div class="modal-dialog slide-top">' +
                    '<div class="modal-content">' +
                      '<div class="modal-header">' +
                        '<h4 class="modal-title">{{ title }}</h4>' +
                      '</div>' +
                      '<div class="modal-body" ng-transclude>' +  '</div>' +
                      '<div class="modal-footer">' +
                      '<button class="close-modal" ng-click="closeModal();">Got it!</button>'+
                      '</div>' +
                    '</div>' +
                  '</div>'+
                '</div>',
      restrict: 'E',
      transclude: true,
      replace: true,
      scope: true,
      link: function posLink(scope, element, attrs) {
        scope.title = attrs.title;

        scope.$watch(attrs.visible, function(value){
          if(!value) {
            element.hide();
          }
          else {
            element.show();
          }
        });

      }
    }
  })
;
