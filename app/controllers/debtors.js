(function(){
    'use strict';

    angular
        .module('app')
        .config(config)
        .controller('DebtorsController', ['$rootScope', '$scope', 'debts', DebtorsController]);
    
    config.$inject = ['$routeProvider', '$locationProvider', '$httpProvider'];
    function config($routeProvider, $locationProvider, $httpProvider) {
        $routeProvider
        .when('/debtors', {
            controller: 'DebtorsController',
            templateUrl: 'app/views/debtors.html'
        });

        $httpProvider.interceptors.push(function($q, $timeout) {
            return {
                // Reset loading elements on error
                'responseError': function(response) {
                    var btnls = [
                        '#modal-debtor [type="submit"]',
                        '#modal-debt [type="submit"]',
                        '[data-debtor-del]',
                        '[data-debt-del]',
                        '[data-debt-ptb]'
                    ];

                    $(btnls.join(", ")).removeClass("btn-loading");

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

    function DebtorsController($rootScope, $scope, debts){
        $scope.loaded = false;
        $scope.saving = false;
        $scope.debtors = [];
        $scope.debts = [];
        $scope.options = {
            showAllDebtors: false
        };
        
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
            return $scope.options.showAllDebtors || $scope.sumDebt(debtor.id) > 0;
        };

        function getDebtorByID(id) {
            for(var i = 0; i < $scope.debtors.length; i++) {
                var debtor = $scope.debtors[i];
                if(debtor.id == id) {
                    return debtor;
                }
            }

            return null;
        }

        function getDebtorIdxByID(id) {
            for(var i = 0; i < $scope.debtors.length; i++) {
                var debtor = $scope.debtors[i];
                if(debtor.id == id) {
                    return i;
                }
            }

            return null;
        }

        function getDebtByID(id) {
            for(var i = 0; i < $scope.debts.length; i++) {
                var debt = $scope.debts[i];
                if(debt.id == id) {
                    return debt;
                }
            }

            return null;
        }

        function getDebtIdxByID(id) {
            for(var i = 0; i < $scope.debts.length; i++) {
                var debt = $scope.debts[i];
                if(debt.id == id) {
                    return i;
                }
            }

            return null;
        }

        $scope.togglePaid = function(id) {
            if($scope.loading) {
                return;
            }

            var debt = getDebtByID(id);
            var idx = getDebtIdxByID(id);
            
            if(debt === null) {
                alert("La deuda no existe");
                return;
            }

            var pagada = debt.pagada=="1";

            $scope.loading = true;
            $('[data-debt-ptb="'+id+'"]').addClass("btn-loading");
            debts.setPaid(id, !pagada).success(function(data){
                $scope.loading = false;
                $('[data-debt-ptb="'+id+'"]').removeClass("btn-loading");

                if(!data.success) {
                    alert("Error: " + data.message);
                } else {
                    $scope.debts[idx].pagada = (!pagada) ? "1" : "0";
                }
            });
        };

        // Modal debtors
        $scope.debtorModal = {
            title: "Agregar deudor",
            submitText: "Agregar",
            dataMaster: {
                nombre: "",
                descripcion: ""
            }
        };

        $scope.debtorFormReset = function() {
            $scope.debtorData = angular.copy($scope.debtorModal.dataMaster);
        };
        
        $scope.debtorShowAdd = function() {
            $scope.debtorModal.title = "Agregar deudor";
            $scope.debtorModal.submitText = "Agregar";
            $scope.debtorFormReset();
            $('#modal-debtor').modal('show');
        };

        $scope.debtorShowEdit = function(id) {
            var debtor = angular.copy(getDebtorByID(id));
            if(!debtor) {
                alert("Deudor no encontrado");
                return;
            }

            $scope.debtorModal.title = "Editar cuenta #" + id;
            $scope.debtorModal.submitText = "Guardar";
            $scope.debtorFormReset();

            $scope.debtorData = debtor;
            $('#modal-debtor').modal('show');
        };
        
        // Add and edit
        $scope.sendDebtorForm = function() {
            var form = angular.copy($scope.debtorData);

            $scope.saving = true;
            $('#modal-debtor [type="submit"]').addClass("btn-loading");

            if(form.id) {
                debts.editDebtor(form).success(function(res){
                    $scope.saving = false;
                    $('#modal-debtor [type="submit"]').removeClass("btn-loading");
                    if(!res.success) {
                        alert("Error: " + res.message);
                        return;
                    }

                    var idx = getDebtorIdxByID(form.id);
                    $scope.debtors[idx] = res.data;
                    $('#modal-debtor').modal('hide');
                });
                return;
            } else {
                debts.addDebtor(form).success(function(res){
                    $scope.saving = false;
                    $('#modal-debtor [type="submit"]').removeClass("btn-loading");
                    if(!res.success) {
                        alert("Error: " + res.message);
                        return;
                    }

                    $('#modal-debtor').modal('hide');
                    $scope.debtors.splice(0, 0, res.data);
                });
            }
        };
        
        $scope.deleteDebtor = function(id) {
            var idx = getDebtorIdxByID(id);
            
            if(idx === null) {
                alert("El deudor no existe");
                return;
            }

            if(!confirm("¿Realmente quieres eliminar este deudor? Esta acción es irreversible, sus deudas serán eliminadas.")) {
                return;
            }

            $scope.saving = true;
            $('[data-debtor-del="'+id+'"]').addClass("btn-loading");

            debts.deleteDebtor(id).success(function(res){
                $scope.saving = false;
                $('[data-debtor-del="'+id+'"]').removeClass("btn-loading");
                if(!res.success) {
                    alert(res.message);
                    return;
                }

                $scope.debtors.splice(idx, 1);
                $scope.debts = $scope.debts.filter(function(obj){
                    return obj.deudor != id;
                });
            });
        };

        // Modal debts
        $scope.debtModal = {
            title: "Agregar deuda",
            submitText: "Agregar",
            dataMaster: {
                descripcion: "",
                deudor: "",
                fecha: new Date(),
                id: "",
                monto: 0,
                pagada: 0
            }
        };

        $scope.debtFormReset = function() {
            $scope.debtData = angular.copy($scope.debtModal.dataMaster);
        };
        
        $scope.debtShowAdd = function() {
            $scope.debtorModal.title = "Agregar deuda";
            $scope.debtorModal.submitText = "Agregar";
            $scope.debtFormReset();
            $('#modal-debt').modal('show');
        };

        $scope.debtShowEdit = function(id) {
            var debt = angular.copy(getDebtByID(id));
            if(!debt) {
                alert("Deuda no encontrada");
                return;
            }

            $scope.debtModal.title = "Editar deuda #" + id;
            $scope.debtModal.submitText = "Guardar";
            $scope.debtFormReset();

            debt.fecha = new Date(debt.fecha);
            debt.monto = parseInt(debt.monto);
            $scope.debtData = debt;
            $('#modal-debt').modal('show');
        };
        
        // Add and edit
        $scope.sendDebtForm = function() {
            var form = angular.copy($scope.debtData);
            form.fecha = dateToForm(form.fecha);
            $scope.saving = true;
            $('#modal-debt [type="submit"]').addClass("btn-loading");

            if(form.id) {
                debts.editDebt(form).success(function(res){
                    $scope.saving = false;
                    $('#modal-debt [type="submit"]').removeClass("btn-loading");
                    if(!res.success) {
                        alert("Error: " + res.message);
                        return;
                    }

                    var idx = getDebtIdxByID(form.id);
                    $scope.debts[idx] = res.data;
                    $('#modal-debt').modal('hide');
                });
                return;
            } else {
                debts.addDebt(form).success(function(res){
                    $scope.saving = false;
                    $('#modal-debt [type="submit"]').removeClass("btn-loading");
                    if(!res.success) {
                        alert("Error: " + res.message);
                        return;
                    }

                    $('#modal-debt').modal('hide');
                    $scope.debts.splice(0, 0, res.data);
                });
            }
        };

        $scope.deleteDebt = function(id) {
            var idx = getDebtIdxByID(id);
            
            if(idx === null) {
                alert("La deuda no existe");
                return;
            }

            if(!confirm("¿Realmente quieres eliminar esta deuda? Esta acción es irreversible.")) {
                return;
            }

            $scope.saving = true;
            $('[data-debt-del="'+id+'"]').addClass("btn-loading");

            debts.deleteDebt(id).success(function(res){
                $scope.saving = false;
                $('[data-debt-del="'+id+'"]').removeClass("btn-loading");
                if(!res.success) {
                    alert(res.message);
                    return;
                }

                $scope.debts.splice(idx, 1);
            });
        };

        angular.element(window).ready(function() {
            if($rootScope.debtAddData) {
                var data = angular.copy($rootScope.debtAddData);
                $rootScope.debtAddData = null;
                $scope.debtShowAdd();

                $scope.debtData = $.extend($scope.debtData, data);
            }
        });

    } // DebtorsController
}());