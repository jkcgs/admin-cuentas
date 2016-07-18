(function(){
    'use strict';

    angular
        .module('app.accounts', ['ngRoute'])
        .config(config)
        .controller('AccountsController', ['$scope', 'accounts', AccountsController]);
    
    config.$inject = ['$routeProvider', '$locationProvider'];
    function config($routeProvider, $locationProvider) {
        $routeProvider
        .when('/accounts', {
            controller: 'AccountsController',
            templateUrl: 'app/views/accounts.html',
            css: 'assets/css/accounts.css'
        });
    }

    function AccountsController($scope, accounts){
        $scope.loaded = false;
        $scope.accounts = [];

        $scope.getSumUnpaid = function() {
            var total = 0;
            for(var i = 0; i < $scope.accounts.length; i++){
                if($scope.accounts[i].pagado != "0") continue;
                total += parseInt($scope.accounts[i].monto);
            }
            return total;
        };

        init();
        function init() {
            accounts.get().success(function(data){
                $scope.accounts = data;
                $scope.loaded = true;
            });
        }
    }

}());