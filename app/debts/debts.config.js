(function(){
    'use strict';

    angular
        .module('app')
        .config(DebtsConfig);

    /** @ngInject */
    DebtsConfig.$inject = ['$routeProvider', '$locationProvider', '$httpProvider'];
    function DebtsConfig($routeProvider, $locationProvider, $httpProvider){
        $routeProvider
        .when('/debts', {
            controller: 'DebtsController',
            controllerAs: 'vm',
            templateUrl: 'app/debts/debts.html'
        });

        $httpProvider.interceptors.push(function($q, $timeout, $rootScope) {
            return {
                // Reset loading elements on error
                'responseError': function(response) {
                    var btnls = [
                        '#modal-debtor [type="submit"]',
                        '#modal-debt [type="submit"]',
                        '[data-debtor-del]',
                        '[data-debt-del]',
                        '[data-debt-ptb]'
                    ];

                    $(btnls.join(", ")).removeClass("btn-loading");

                    $rootScope.$emit("accountsError");
                    return $q.reject(response);
                }
            };
        });
    }

}());