'use strict';
/**
 * @ngdoc function
 * @name d8intranetApp.filter:filterByPosition
 * @description
 * # filterByPosition
 * Filter of the d8intranetApp
 */

angular.module('d8intranetApp')
  .service('modalWindow', function ($rootScope) {

    var modalWindow = {
      setMessage: function (modalType, modalMessageSubtitle, modalMessage) {
        $rootScope.showModalWindow = true;

        if(modalType == 'error') {
          $rootScope.modalMessageType = 'error';
        }

        else if  (modalType == 'success'){
          $rootScope.modalMessageType = 'success';
        }

        else if  (modalType == 'warning'){
          $rootScope.modalMessageType = 'warning';
        }

        $rootScope.modalMessageSubtitle = modalMessageSubtitle;
        $rootScope.modalMessage = modalMessage;
      }
    };

    return modalWindow;
  });
