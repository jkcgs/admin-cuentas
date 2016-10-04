(function() {
    'use strict';

    angular
        .module('app')
            .controller('ExternalDebts', ['$scope', 'external', ExternalDebts]);

    function ExternalDebts($scope, external) {

        $scope.loadingDebts = false;
        $scope.dataDebts = null;
        $scope.errorDebts = false;

        $scope.loadingAccs = false;
        $scope.dataAccs = null;
        $scope.errorAccs = false;

        $scope.accounts = [];
        $scope.addAccountType = "1";
        
        external.getAccounts().then(function(res) {
            $scope.accountsData = res.data.data;
        });

        $scope.loadDebts = function() {
            if ($scope.loadingDebts) return;

            $scope.loadingDebts = true;
            $scope.errorDebts = false;
            external.getCreditDebts().success(function(res) {
                $scope.loadingDebts = false;
                if (!res.success) {
                    $scope.errorDebts = res.message;
                    return;
                }

                if(res.data.length > 0) {
                    $scope.dataDebts = res.data;
                } else {
                    $scope.errorDebts = "No se han configurado cuentas remotas.";
                }
            }).error(function(res) {
                $scope.errorDebts = "No se pudo cargar los datos. Por favor intenta nuevamente.";
                $scope.loadingDebts = false;
            });
        };

        $scope.loadAccs = function() {
            if ($scope.loadingAccs) return;

            $scope.loadingAccs = true;
            $scope.errorAccs = false;
            external.getBankAccounts().success(function(res) {
                $scope.loadingAccs = false;
                if (!res.success) {
                    $scope.errorAccs = res.message;
                    return;
                }

                if(res.data.length > 0) {
                    $scope.dataAccs = res.data;
                } else {
                    $scope.errorAccs = "No se han configurado cuentas remotas.";
                }
            }).error(function(res) {
                $scope.errorAccs = "No se pudo cargar los datos. Por favor intenta nuevamente.";
                $scope.loadingAccs = false;
            });
        };

        $scope.load = function() {
            $scope.loadDebts();
            $scope.loadAccs();
        };

        $scope.loadCurrent = function() {
            var el = document.querySelector('#ext-debts .tab-pane.active');
            if(!el) return;

            if(el.id == "ext-credito") {
                $scope.loadDebts();
            } else if(el.id == "ext-saldos") {
                $scope.loadAccs();
            }
        };

        $scope.init = function() {
            $('#ext-debts').on('show.bs.modal', function(e) {
                if ($scope.dataDebts === null){
                    $scope.loadDebts();
                }
                if ($scope.dataAccs === null){
                    $scope.loadAccs();
                }
            });
            
            $('a[role=tab]').click(function(e) {
                e.preventDefault();
                $(this).tab('show');
            });
        };

        $scope.addAccount = function() {
            if(!$scope.addAccountForm.$valid) {
                return;
            }

            var data = $scope.addAccountData;
            if(data.password != data.repass) {
                alert("Las contraseñas no son iguales!");
                return;
            }

            delete data.repass;

            external.addAccount(data).success(function(res){
                if(!res.success) {
                    alert("No se pudo agregar la cuenta: " + res.message);
                    return;
                }

                $scope.accountsData.accounts.push(res.data);
                angular.element("[name=addAccountForm]")[0].reset();
            }).error(function(reason){
                alert("No se pudo agregar la cuenta: " + reason);
            });
        };

        $scope.deleteAccount = function(id) {
            external.deleteAccount(id).success(function(res) {
                if(!res.success) {
                    alert("Error al eliminar: " + res.message);
                    return;
                }

                for(var i = 0; i < $scope.accountsData.accounts.length; i++) {
                    if($scope.accountsData.accounts[i].id == id) {
                        $scope.accountsData.accounts.splice(i, 1);
                        break;
                    }
                }
            });
        };
    }

}());