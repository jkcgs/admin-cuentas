(function(){
    'use strict';

    angular
        .module('app.debtors', ['ngRoute'])
        .config(config)
        .controller('DebtorsController', ['$scope', DebtorsController]);
    
    config.$inject = ['$routeProvider', '$locationProvider'];
    function config($routeProvider, $locationProvider) {
        $routeProvider
        .when('/debtors', {
            controller: 'DebtorsController',
            templateUrl: 'app/views/debtors.html'
        });
    }

    function DebtorsController($scope){
        init();
        function init() {
            
        }
    }

}());