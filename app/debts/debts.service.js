(function() {
'use strict';

    angular
        .module('app')
        .service('Debts', Debts);

    Debts.$inject = ['$http'];
    function Debts($http) {
        this.getMerged = getMerged;
        this.addDebt = addDebt;
        this.editDebt = editDebt;
        this.setPaid = setPaid;
        this.setPaidFor = setPaidFor;
        this.deleteDebt = deleteDebt;
        this.addDebtor = addDebtor;
        this.editDebtor = editDebtor;
        this.deleteDebtor = deleteDebtor;
        
        ////////////////
        function getMerged(){
            var url = 'api/?debts/get_merged';
            return $http.get(url);
        }

        function addDebt(data) {
            var url = 'api/?debts/add';
            return $http.post(url, $.param(data));
        }
        
        function editDebt(data) {
            var url = 'api/?debts/edit&id='+data.id;
            return $http.post(url, $.param(data));
        }

        function setPaid(id, paid) {
            paid = paid ? "1" : "0";

            var url = 'api/?debts/paid&id='+id+'&paid='+paid;
            return $http.get(url);
        }

        function setPaidFor(id, paid) {
            paid = paid ? "1" : "0";

            var url = 'api/?debts/paidFor&id='+id+'&paid='+paid;
            return $http.get(url);
        }
        
        function deleteDebt(id) {
            var url = 'api/?debts/delete&id='+id;
            return $http.get(url);
        }

        function addDebtor(data) {
            var url = 'api/?debtors/add';
            return $http.post(url, $.param(data));
        }
        
        function editDebtor(data) {
            var url = 'api/?debtors/edit&id='+data.id;
            return $http.post(url, $.param(data));
        }
        
        function deleteDebtor(id) {
            var url = 'api/?debtors/delete&id='+id;
            return $http.get(url);
        }
    }
})();