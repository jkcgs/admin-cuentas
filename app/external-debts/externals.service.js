(function() {
'use strict';

    angular
        .module('app')
        .service('ExternalsService', ExternalsService);

    ExternalsService.$inject = ['$http'];
    function ExternalsService($http) {
        this.getCreditDebts = getCreditDebts;
        this.getBankAccounts = getBankAccounts;
        this.getDolar = getDolar;
        this.getAccounts = getAccounts;
        this.addAccount = addAccount;
        this.deleteAccount = deleteAccount;
        
        ////////////////
        function getCreditDebts(){
            return $http.get('api/?bank_accounts/load/2');
        }

        function getBankAccounts() {
            return $http.get('api/?bank_accounts/load/1');
        }

        function getDolar() {
            return $http.get('api/?external/dolar');
        }

        function getAccounts() {
            return $http.get('api/?bank_accounts/get_all');
        }

        function addAccount(data) {
            return $http.post('api/?bank_accounts/add', $.param(data));
        }

        function deleteAccount(id) {
            return $http.get('api/?bank_accounts/delete/' + id);
        }
    }
})();