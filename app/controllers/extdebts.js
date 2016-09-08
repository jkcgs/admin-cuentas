(function() {
    'use strict';

    angular
        .module('app')
        .controller('ExternalDebts', ['$rootScope', '$timeout', 'external', ExternalDebts])

    function ExternalDebts($rootScope, $timeout, external) {
        var vm = this;

        vm.loadingDebts = false;
        vm.dataDebts = null;
        vm.errorDebts = false;

        vm.loadingAccs = false;
        vm.dataAccs = null;
        vm.errorAccs = false;

        vm.loadDebts = function() {
            if (vm.loadingDebts) return;

            vm.loadingDebts = true;
            vm.errorDebts = false;
            external.getCreditDebts().success(function(res) {
                vm.loadingDebts = false;
                if (!res.success) {
                    vm.errorDebts = res.message;
                    return;
                }

                vm.dataDebts = res.data;
            }).error(function(res) {
                vm.errorDebts = "No se pudo cargar los datos. Por favor intenta nuevamente.";
                vm.loadingDebts = false;
            });
        };

        vm.loadAccs = function() {
            if (vm.loadingAccs) return;

            vm.loadingAccs = true;
            vm.errorAccs = false;
            external.getBankAccounts().success(function(res) {
                vm.loadingAccs = false;
                if (!res.success) {
                    vm.errorAccs = res.message;
                    return;
                }

                vm.dataAccs = res.data;
            }).error(function(res) {
                vm.errorAccs = "No se pudo cargar los datos. Por favor intenta nuevamente.";
                vm.loadingAccs = false;
            });
        };

        vm.load = function() {
            vm.loadDebts();
            vm.loadAccs();
        };

        $timeout(function() {
            $('#ext-debts').on('show.bs.modal', function(e) {
                if (vm.dataDebts === null){
                    vm.loadDebts();
                }
                if (vm.dataAccs === null){
                    vm.loadAccs();
                }
            });
            
            $('#ext-debts a[role=tab]').click(function(e) {
                e.preventDefault();
                $(this).tab('show');
            });
        }, 200);


    }

}());