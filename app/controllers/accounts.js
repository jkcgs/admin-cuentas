(function(){
    'use strict';

    angular
        .module('app')
        .config(config)
        .controller('AccountsController', ['$rootScope', '$scope', 'accounts', AccountsController]);
    
    config.$inject = ['$routeProvider', '$locationProvider', '$httpProvider'];
    function config($routeProvider, $locationProvider, $httpProvider) {
        $routeProvider
        .when('/accounts', {
            controller: 'AccountsController',
            templateUrl: 'app/views/accounts.html'
        });

        $httpProvider.interceptors.push(function($q, $timeout) {
            return {
                // Reset loading elements on error
                'responseError': function(response) {
                    $('#modal-cuenta [type="submit"]').removeClass("btn-loading");
                    $('[data-del-id]').removeClass("btn-loading");

                    var scope = angular.element($('[ng-view]')).scope();
                    $timeout(function(){
                        scope.$apply(function(){
                            scope.saving = false;
                        });
                    }, 0);
                    
                    return $q.reject(response);
                }
            };
        });
    }

    function AccountsController($rootScope, $scope, accounts){
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
            nombre: "",
            descripcion: "",
            fecha_compra: new Date(),
            fecha_facturacion: billingDate,
            monto_original: 0,
            divisa_original: "CLP",
            monto: 0,
            num_cuotas: 0,
            pagado: 0,
            info: "",
            id: null
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

        function dataPreprocess(data) {
            if(data === null) {
                return null;
            }

            var account = $.extend($scope.masterForm, data);
            // Preformatting
            account.monto_original = parseFloat(account.monto_original);
            account.monto = parseFloat(account.monto);
            account.num_cuotas = parseInt(account.num_cuotas);
            account.fecha_compra = new Date(account.fecha_compra);
            account.fecha_facturacion = new Date(account.fecha_facturacion);

            return account;
        }

        // Reset form
        var formReset = function() {
            $scope.accform = angular.copy($scope.masterForm);
        };

        $scope.showAdd = function(filldata) {
            formReset();
            if(typeof filldata !== "undefined") {
                $scope.accform = dataPreprocess(filldata);
            }
            
            $scope.modalTitle = "Agregar cuenta";
            $scope.modalSubmit = "Agregar";
            
            $('#modal-cuenta').modal('show');
        };

        $scope.showEdit = function(id) {
            var account = dataPreprocess(getByID(id));
            if(!account) {
                alert("Cuenta no encontrada");
                return;
            }

            $scope.modalTitle = "Editar cuenta #" + id;
            $scope.modalSubmit = "Guardar";
            formReset();

            $scope.accform = account;
            $('#modal-cuenta').modal('show');
        };

        $scope.cloneAcc = function(id) {
            var account = getByID(id);
            if(!account) {
                alert("Cuenta no encontrada");
                return;
            }

            $scope.showAdd(account);
        };

        // Add and edit
        $scope.sendForm = function() {
            var form = angular.copy($scope.accform);
            if(!form.divisa_original) {
                form.divisa_original = "CLP";
            }

            form.fecha_compra = dateToForm(form.fecha_compra);
            form.fecha_facturacion = dateToForm(form.fecha_facturacion, false);

            $scope.saving = true;
            $('#modal-cuenta [type="submit"]').addClass("btn-loading");
            if(form.id) {
                accounts.edit(form).success(function(res){
                    $scope.saving = false;
                    $('#modal-cuenta [type="submit"]').removeClass("btn-loading");
                    if(!res.success) {
                        alert("Error: " + res.message);
                        return;
                    }

                    var idx = getIndexByID(form.id);
                    $scope.accounts[idx] = res.data;
                    $('#modal-cuenta').modal('hide');
                });
                return;
            } else {
                accounts.add(form).success(function(res){
                    $scope.saving = false;
                    $('#modal-cuenta [type="submit"]').removeClass("btn-loading");
                    if(!res.success) {
                        alert("Error: " + res.message);
                        return;
                    }

                    $('#modal-cuenta').modal('hide');
                    $scope.accounts.splice(0, 0, res.data);
                });
            }
        };

        $scope.delAccount = function(id) {
            var accIndex = getIndexByID(id);
            
            if(accIndex === null) {
                alert("La cuenta no existe");
                return;
            }

            if(!confirm("¿Realmente quieres eliminar esta cuenta? Esta acción es irreversible")) {
                return;
            }

            $scope.saving = true;
            $('[data-del-id="'+id+'"]').addClass("btn-loading");

            accounts.del(id).success(function(res){
                $scope.saving = false;
                $('[data-del-id="'+id+'"]').removeClass("btn-loading");
                if(!res.success) {
                    alert(res.message);
                    return;
                }

                $scope.accounts.splice(accIndex, 1);
            });
        };

        init();
        function init() {
            accounts.get().success(function(data){
                $scope.accounts = data;
                $scope.loaded = true;
            });
        }

        angular.element(window).ready(function() {
            if($rootScope.accAddData) {
                var data = angular.copy($rootScope.accAddData);
                $rootScope.accAddData = null;

                var formData = {
                    nombre: data.comercio,
                    fecha_compra: new Date(data.fecha),
                    monto_original: data.valor,
                    divisa_original: "CLP",
                    monto: data.valor,
                    cuotas: data.cuotas,
                    info: data.documento ? "Documento: " + data.documento : ""
                };

                $scope.showAdd(formData);
            }
        });
    }
}());