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

        $timeout(function() {
            $('#ext-debts').on('show.bs.modal', function(e) {
                if (vm.dataDebts === null) vm.loadDebts();
            });
            
            $('#ext-debts a[role=tab]').click(function(e) {
                e.preventDefault();
                $(this).tab('show');
            });
        }, 200);


    }

}());