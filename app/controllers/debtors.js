(function(){
    'use strict';

    angular
        .module('app.debtors', ['ngRoute'])
        .config(config)
        .controller('DebtorsController', ['$scope', 'debts', DebtorsController]);
    
    config.$inject = ['$routeProvider', '$locationProvider'];
    function config($routeProvider, $locationProvider) {
        $routeProvider
        .when('/debtors', {
            controller: 'DebtorsController',
            templateUrl: 'app/views/debtors.html'
        });
    }

    function DebtorsController($scope, debts){
        $scope.loaded = false;
        $scope.debtors = [];
        $scope.debts = [];
        
        init();
        function init() {
            debts.getMerged().success(function(data){
                $scope.loaded = true;

                if(!data.success) {
                    alert(data.message);
                    return;
                }

                $scope.debtors = data.debtors;
                $scope.debts = data.debts;
            });
        }

        $scope.sumDebt = function(id) {
            var sum = 0;
            for(var i = 0; i < $scope.debts.length; i++) {
                var debt = $scope.debts[i];
                if(debt.deudor != id || debt.pagada != "0") {
                    continue;
                }

                sum += parseInt($scope.debts[i].monto);
            }

            return sum;
        };

        $scope.sumDebtTotal = function() {
            var sum = 0;
            for(var i = 0; i < $scope.debts.length; i++) {
                var debt = $scope.debts[i];
                if(debt.pagada != "0") continue;
                sum += parseInt($scope.debts[i].monto);
            }

            return sum;
        };

        $scope.hasDebt = function(debtor) {
            return $scope.sumDebt(debtor.id) > 0;
        };
    }

}());