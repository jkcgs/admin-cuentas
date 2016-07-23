(function(){
    'use strict';

    angular
        .module('app')
        .service('debts', ['$http', debts]);

    function debts($http){
        return {
            getMerged: function(){
                var url = 'api.php?debts/get_merged';
                return $http.get(url);
            },

            addDebt: function(data) {
                var url = 'api.php?debts/add';
                return $http.post(url, $.param(data));
            },
            
            editDebt: function(data) {
                var url = 'api.php?debts/edit&id='+data.id;
                return $http.post(url, $.param(data));
            },

            setPaid: function(id, paid) {
                paid = paid ? "1" : "0";

                var url = 'api.php?debts/paid&id='+id+'&paid='+paid;
                return $http.get(url);
            },
            
            deleteDebt: function(id) {
                var url = 'api.php?debts/delete&id='+id;
                return $http.get(url);
            },

            addDebtor: function(data) {
                var url = 'api.php?debtors/add';
                return $http.post(url, $.param(data));
            },
            
            editDebtor: function(data) {
                var url = 'api.php?debtors/edit&id='+data.id;
                return $http.post(url, $.param(data));
            },
            
            deleteDebtor: function(id) {
                var url = 'api.php?debtors/delete&id='+id;
                return $http.get(url);
            }
        };
    }

}());