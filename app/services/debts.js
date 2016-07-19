(function(){
    'use strict';

    angular
        .module('app')
        .service('debts', ['$http', debts]);

    function debts($http){
        this.getMerged = function(){
            var url = 'api.php?debts/get_merged';
            return $http.get(url);
        };
    }

}());