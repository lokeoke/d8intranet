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

  .directive('mobileMenu', ['$document', function ($document) {
    return {
      link: function (scope, element) {
        $(element).on('click', function () {
          $('.bt-menu').toggleClass('bt-menu-open');
        })
      }
    }
  }])

  .directive('ngBlur', ['$document', function($document) {
    return {
      link: function (scope, element, attrs) {
        element.bind('blur', function(){
          scope.inputSelected = true;
          element[0].value ? scope.inputSelected = true : scope.inputSelected = false
        })
      }
    }
  }])


  .directive('chosen', function() {
    var linker = function(scope, element, attr) {
      scope.$watch('teamFilter', function(){
        element.trigger('chosen:updated');
      });

      element.chosen();
    };

    return {
      restrict: 'A',
      link: linker
    }
  })
;
