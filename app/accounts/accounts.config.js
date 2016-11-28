(function(){
    'use strict';

    angular
        .module('app')
        .config(AccountsConfig);

    /** @ngInject */
    AccountsConfig.$inject = ['$routeProvider', '$locationProvider', '$httpProvider'];
    function AccountsConfig($routeProvider, $locationProvider, $httpProvider){
        $routeProvider
        .when('/accounts', {
            controller: 'AccountsController',
            controllerAs: 'vm',
            templateUrl: 'app/accounts/accounts.html'
        });

        $httpProvider.interceptors.push(function($q, $timeout, $rootScope) {
            return {
                // Reset loading elements on error
                'responseError': function(response) {
                    $('#modal-cuenta [type="submit"]').removeClass("btn-loading");
                    $('[data-del-id]').removeClass("btn-loading");

                    $rootScope.$emit("accountsError");
                    return $q.reject(response);
                }
            };
        });
    }

}());