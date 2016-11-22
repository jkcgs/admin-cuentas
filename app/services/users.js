(function() {
'use strict';

    angular
        .module('app')
        .factory('users', users);

    users.$inject = ['$http'];
    function users($http) {
        var service = {
            getAll: getAll
        };
        
        return service;

        ////////////////

        function getAll() {
            return $http.get("api/?admin/get_users").then(
                function(res) {
                    return res.data.data;
                }
            );
        }
    }
})();