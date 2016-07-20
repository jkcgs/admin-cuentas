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
            templateUrl: 'app/views/accounts.html'
        });
    }

    function AccountsController($scope, accounts){
        $scope.loaded = false;
        $scope.saving = false;
        $scope.accounts = [];

        // Modal setup
        $scope.accform = {};
        $scope.modalTitle = "";
        $scope.modalSubmit = "";

        // Initial form for modal
        // Billing date is next month if day is > 5
        var billingDate = new Date();
        if(billingDate.getDate() > 5) {
            var month = billingDate.getMonth();
            billingDate.setMonth(month == 11 ? 0 : month+1);
        }
        $scope.masterForm = {
            fecha_compra: new Date(),
            fecha_facturacion: billingDate,
            num_cuotas: 0
        };

        $scope.getSumUnpaid = function() {
            var total = 0;
            for(var i = 0; i < $scope.accounts.length; i++){
                if($scope.accounts[i].pagado != "0") continue;
                total += parseInt($scope.accounts[i].monto);
            }
            return total;
        };

        function getByID(id) {
            for(var i = 0; i < $scope.accounts.length; i++) {
                var account = $scope.accounts[i];
                if(account.id == id) {
                    return account;
                }
            }

            return null;
        }

        function getIndexByID(id) {
            for(var i = 0; i < $scope.accounts.length; i++) {
                var account = $scope.accounts[i];
                if(account.id == id) {
                    return i;
                }
            }

            return null;
        }

        $scope.showAdd = function() {
            $scope.modalTitle = "Agregar cuenta";
            $scope.modalSubmit = "Agregar";
            $scope.formReset();
            $('#modal-cuenta').modal('show');
        };

        $scope.showEdit = function(id) {
            var account = angular.copy($scope.getByID(id));
            if(!account) {
                alert("Cuenta no encontrada");
                return;
            }

            $scope.modalTitle = "Editar cuenta #" + id;
            $scope.modalSubmit = "Guardar";
            $scope.formReset();

            // Preformatting
            account.monto_original = parseFloat(account.monto_original);
            account.monto = parseFloat(account.monto);
            account.num_cuotas = parseInt(account.num_cuotas);
            account.fecha_compra = new Date(account.fecha_compra);
            account.fecha_facturacion = new Date(account.fecha_facturacion);

            $scope.accform = account;
            $('#modal-cuenta').modal('show');
        };

        // Reset form
        $scope.formReset = function() {
            $scope.accform = angular.copy($scope.masterForm);
        };

        $scope.sendForm = function() {
            $scope.saving = true;
            $('#modal-cuenta [type="submit"]').addClass("btn-loading");
            if($scope.accform.id) {
                accounts.edit($scope.accform).success(function(res){
                    $scope.saving = false;
                    $('#modal-cuenta [type="submit"]').removeClass("btn-loading");
                    if(!res.success) {
                        alert("Error: " + res.message);
                        return;
                    }

                    var idx = $scope.getIndexByID($scope.accform.id);
                    $scope.accounts[idx] = res.data;
                });
                return;
            } else {
                accounts.add($scope.accform).success(function(res){
                    $scope.saving = false;
                    $('#modal-cuenta [type="submit"]').removeClass("btn-loading");
                    if(!res.success) {
                        alert(res.message);
                        return;
                    }

                    $scope.accounts.push(res.data);
                });
            }
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