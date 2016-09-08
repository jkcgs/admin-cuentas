(function(){
    'use strict';

    angular
        .module('app')
        .service('external', ['$http', external]);

    function external($http){
        return {
            getCreditDebts: function(){
                return $http.get('api.php?external/credit_debts');
            },

            getBankAccounts: function() {
                return $http.get('api.php?external/fetch_banks');
            }
        };
    }
})();