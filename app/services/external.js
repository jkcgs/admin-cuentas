(function(){
    'use strict';

    angular
        .module('app')
        .service('external', ['$http', external]);

    function external($http){
        return {
            getCreditDebts: function(){
                return $http.get('api/?bank_accounts/load/2');
            },

            getBankAccounts: function() {
                return $http.get('api/?bank_accounts/load/1');
            },

            getDolar: function() {
                return $http.get('api/?external/dolar');
            },

            getAccounts: function() {
                return $http.get('api/?bank_accounts/get_all');
            },

            addAccount: function(data) {
                return $http.post('api/?bank_accounts/add', $.param(data));
            },

            deleteAccount: function(id) {
                return $http.get('api/?bank_accounts/delete/' + id);
            }
        };
    }
})();