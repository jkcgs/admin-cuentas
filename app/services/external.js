(function(){
    'use strict';

    angular
        .module('app')
        .service('external', ['$http', external]);

    function external($http){
        return {
            getCreditDebts: function(){
                return $http.get('api/?bank_accounts/2');
            },

            getBankAccounts: function() {
                return $http.get('api/?bank_accounts/1');
            },

            getDolar: function() {
                return $http.get('api/?external/dolar');
            }
        };
    }
})();