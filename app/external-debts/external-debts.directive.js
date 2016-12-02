(function() {
    'use strict';

    angular
        .module('app')
        .directive('externalDebts', ExternalDebts);

    function ExternalDebts() {
        var directive = {
            bindToController: true,
            controller: ExternalDebtsController,
            controllerAs: 'nyan',
            link: link,
            restrict: 'A',
            templateUrl: 'app/external-debts/external-debts.html'
        };

        return directive;
    }

    ////////////

    ExternalDebtsController.$inject = ['$scope', '$document', '$timeout', 'ExternalsService'];
    function ExternalDebtsController($scope, $document, $timeout, ExternalsService) {
        var vm = this;
        vm.loadingDebts = false;
        vm.dataDebts = null;
        vm.errorDebts = false;
        vm.loadingAccs = false;
        vm.dataAccs = null;
        vm.errorAccs = false;
        vm.accounts = [];
        vm.addAccountType = "1";
        
        vm.addAccount = addAccount;
        vm.deleteAccount = deleteAccount;
        vm.loadAccs = loadAccs;
        vm.loadDebts = loadDebts;
        vm.loadCurrent = loadCurrent;

        activate();

        ////////////////

        function activate() {
            ExternalsService.getAccounts().then(function(res) {
                vm.accountsData = res.data.data;
            });

            angular.element(window).ready(function(){
                $timeout(function(){
                    $scope.$on('$viewContentLoaded', function(){
                        $document.find('#ext-debts').on('show.bs.modal', function(e) {                        
                            if (vm.dataDebts === null){
                                vm.loadDebts();
                            } else {
                                console.log("wut");
                            }
                            if (vm.dataAccs === null){
                                vm.loadAccs();
                            }
                        });
                        
                        $document.find('a[role=tab]').on('click', function(e) {
                            e.preventDefault();
                            $(this).tab('show');
                        });
                    }, 1000);
                });
            });
            
        }
        
        function addAccount() {
            if(!vm.addAccountForm.$valid) {
                return;
            }

            var data = vm.addAccountData;
            if(data.password != data.repass) {
                alert("Las contraseñas no son iguales!");
                return;
            }

            delete data.repass;

            ExternalsService.addAccount(data)
            .success(function(res){
                if(!res.success) {
                    alert("No se pudo agregar la cuenta: " + res.message);
                    return;
                }

                vm.accountsData.accounts.push(res.data);
                angular.element("[name=addAccountForm]")[0].reset();
            })
            .error(function(reason){
                alert("No se pudo agregar la cuenta: " + reason);
            });
        }
        
        function deleteAccount(id) {
            ExternalsService.deleteAccount(id)
            .success(function(res) {
                if(!res.success) {
                    alert("Error al eliminar: " + res.message);
                    return;
                }

                for(var i = 0; i < vm.accountsData.accounts.length; i++) {
                    if(vm.accountsData.accounts[i].id == id) {
                        vm.accountsData.accounts.splice(i, 1);
                        break;
                    }
                }
            })
            .error(function(reason) {
                alert("Error al eliminar: " + reason);
            });
        }

        function loadAccs() {
            if (vm.loadingAccs) return;

            vm.loadingAccs = true;
            vm.errorAccs = false;
            ExternalsService.getBankAccounts()
            .success(function(res) {
                vm.loadingAccs = false;
                if (!res.success) {
                    vm.errorAccs = res.message;
                    return;
                }

                if(res.data.length > 0) {
                    vm.dataAccs = res.data;
                }
            })
            .error(function(reason) {
                vm.errorAccs = "No se pudo cargar los datos. Por favor intenta nuevamente.";
                vm.loadingAccs = false;
            });
        }

        function loadDebts() {
            if (vm.loadingDebts) return;

            vm.loadingDebts = true;
            vm.errorDebts = false;

            ExternalsService.getCreditDebts()
            .success(function(res) {
                vm.loadingDebts = false;
                if (!res.success) {
                    vm.errorDebts = res.message;
                    return;
                }

                if(res.data.length > 0) {
                    vm.dataDebts = res.data;
                }
            })
            .error(function(reason) {
                vm.errorDebts = "No se pudo cargar los datos. Por favor intenta nuevamente.";
                vm.loadingDebts = false;
            });
        }

        function loadCurrent() {
            var el = document.querySelector('#ext-debts .tab-pane.active');
            if(!el) return;

            if(el.id == "ext-credito") {
                vm.loadDebts();
            } else if(el.id == "ext-saldos") {
                vm.loadAccs();
            }
        }
    }
})();