(function(){
    'use strict';

    angular
        .module('app')
        .service('debts', ['$http', debts]);

    function debts($http){
        return {
            getMerged: function(){
                var url = 'api/?debts/get_merged';
                return $http.get(url);
            },

            addDebt: function(data) {
                var url = 'api/?debts/add';
                return $http.post(url, $.param(data));
            },
            
            editDebt: function(data) {
                var url = 'api/?debts/edit&id='+data.id;
                return $http.post(url, $.param(data));
            },

            setPaid: function(id, paid) {
                paid = paid ? "1" : "0";

                var url = 'api/?debts/paid&id='+id+'&paid='+paid;
                return $http.get(url);
            },

            setPaidFor: function(id, paid) {
                paid = paid ? "1" : "0";

                var url = 'api/?debts/paidFor&id='+id+'&paid='+paid;
                return $http.get(url);
            },
            
            deleteDebt: function(id) {
                var url = 'api/?debts/delete&id='+id;
                return $http.get(url);
            },

            addDebtor: function(data) {
                var url = 'api/?debtors/add';
                return $http.post(url, $.param(data));
            },
            
            editDebtor: function(data) {
                var url = 'api/?debtors/edit&id='+data.id;
                return $http.post(url, $.param(data));
            },
            
            deleteDebtor: function(id) {
                var url = 'api/?debtors/delete&id='+id;
                return $http.get(url);
            }
        };
    }

}());