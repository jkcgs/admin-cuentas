(function(){
    'use strict';

    angular
        .module('app')
        .service('external', ['$http', external]);

    function external($http){
        return {
            getCreditDebts: function(){
                return $http.get('api/?external/credit_debts');
            },

            getBankAccounts: function() {
                return $http.get('api/?external/fetch_banks');
            },

            getDolar: function() {
                return $http.get('api/?external/dolar');
            }
        };
    }
})();