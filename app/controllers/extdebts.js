(function(){
    'use strict';

    angular
        .module('app')
        .controller('ExternalDebts', ['$timeout', 'external', ExternalDebts])

    function ExternalDebts($timeout, external){
        var vm = this;

        vm.loading = false;
        vm.data = null;
        vm.error = false;

        vm.load = function() {
            if(vm.loading) return;

            vm.loading = true;
            external.getCreditDebts().success(function(res){
                if(!res.success) {
                    vm.error = res.message;
                    return;
                }

                vm.data = res.data;
                vm.loading = false;
            });
        };

        $timeout(function(){
            $('#ext-debts').on('show.bs.modal', function(e) {
                if(vm.data === null) vm.load();
            });
        }, 200);
        
    }

}());