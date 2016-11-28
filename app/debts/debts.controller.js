(function() {
'use strict';

    angular
        .module('app')
        .controller('DebtsController', DebtsController);

    DebtsController.$inject = ['$rootScope', 'Debts'];
    function DebtsController($rootScope, Debts) {
        var vm = this;
        vm.loaded = false;
        vm.saving = false;
        vm.debtors = [];
        vm.debts = [];
        vm.options = {
            showAllDebtors: false
        };
        vm.debtorModal = {
            title: "Agregar deudor",
            submitText: "Agregar",
            dataMaster: {
                nombre: "",
                descripcion: ""
            }
        };
        vm.debtModal = {
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

        vm.debtorDelete = debtorDelete;
        vm.debtorFormReset = debtorFormReset;
        vm.debtorSendForm = debtorSendForm;
        vm.debtorShowAdd = debtorShowAdd;
        vm.debtorShowEdit = debtorShowEdit;

        vm.debtDelete = debtDelete;
        vm.debtFormReset = debtFormReset;
        vm.debtSendForm = debtSendForm;
        vm.debtShowAdd = debtShowAdd;
        vm.debtShowEdit = debtShowEdit;
        vm.debtsPaidFor = debtsPaidFor;

        vm.hasDebt = hasDebt;
        vm.sumDebt = sumDebt;
        vm.sumDebtTotal = sumDebtTotal;
        vm.togglePaid = togglePaid;

        activate();

        ////////////////

        function activate() {
            Debts.getMerged().success(function(data){
                vm.loaded = true;

                if(!data.success) {
                    return;
                }

                vm.debtors = data.debtors;
                vm.debts = data.debts;
            });
            
            angular.element(window).ready(function() {
                if($rootScope.debtAddData) {
                    $('.modal').modal("hide");

                    var data = angular.copy($rootScope.debtAddData);
                    $rootScope.debtAddData = null;
                    vm.debtData = $.extend(vm.debtData, data);

                    vm.debtShowAdd();
                }
            });

            $rootScope.$on("accountsError", function(){
                vm.saving = false;
            });

            $rootScope.$on("addDebtAction", function(data){
            $('.modal').modal("hide");
                vm.debtData = $.extend(vm.debtData, data);
                vm.debtShowAdd();
            });
        }

        function debtorDelete(id) {
            var idx = getDebtorIdxByID(id);
            
            if(idx === null) {
                alert("El deudor no existe");
                return;
            }

            if(!confirm("¿Realmente quieres eliminar este deudor? Esta acción es irreversible, sus deudas serán eliminadas.")) {
                return;
            }

            vm.saving = true;
            $('[data-debtor-del="'+id+'"]').addClass("btn-loading");

            Debts.deleteDebtor(id).success(function(res){
                vm.saving = false;
                $('[data-debtor-del="'+id+'"]').removeClass("btn-loading");
                if(!res.success) {
                    alert(res.message);
                    return;
                }

                vm.debtors.splice(idx, 1);
                vm.debts = vm.debts.filter(function(obj){
                    return obj.deudor != id;
                });
            });
        };
        
        function debtorFormReset() {
            vm.debtorData = angular.copy(vm.debtorModal.dataMaster);
        }

        function debtorSendForm() {
            var form = angular.copy(vm.debtorData);

            vm.saving = true;
            $('#modal-debtor [type="submit"]').addClass("btn-loading");

            if(form.id) {
                Debts.editDebtor(form).success(function(res){
                    vm.saving = false;
                    $('#modal-debtor [type="submit"]').removeClass("btn-loading");
                    if(!res.success) {
                        alert("Error: " + res.message);
                        return;
                    }

                    var idx = getDebtorIdxByID(form.id);
                    vm.debtors[idx] = res.data;
                    $('#modal-debtor').modal('hide');
                });
                return;
            } else {
                Debts.addDebtor(form).success(function(res){
                    vm.saving = false;
                    $('#modal-debtor [type="submit"]').removeClass("btn-loading");
                    if(!res.success) {
                        alert("Error: " + res.message);
                        return;
                    }

                    $('#modal-debtor').modal('hide');
                    vm.debtors.splice(0, 0, res.data);
                });
            }
        }

        function debtorShowAdd() {
            vm.debtorModal.title = "Agregar deudor";
            vm.debtorModal.submitText = "Agregar";
            vm.debtorFormReset();
            $('#modal-debtor').modal('show');
        }

        function debtorShowEdit(id) {
            var debtor = angular.copy(getDebtorByID(id));
            if(!debtor) {
                alert("Deudor no encontrado");
                return;
            }

            vm.debtorModal.title = "Editar cuenta #" + id;
            vm.debtorModal.submitText = "Guardar";
            vm.debtorFormReset();

            vm.debtorData = debtor;
            $('#modal-debtor').modal('show');
        }

        function debtDelete(id) {
            var idx = getDebtIdxByID(id);
            
            if(idx === null) {
                alert("La deuda no existe");
                return;
            }

            if(!confirm("¿Realmente quieres eliminar esta deuda? Esta acción es irreversible.")) {
                return;
            }

            vm.saving = true;
            $('[data-debt-del="'+id+'"]').addClass("btn-loading");

            Debts.deleteDebt(id).success(function(res){
                vm.saving = false;
                $('[data-debt-del="'+id+'"]').removeClass("btn-loading");
                if(!res.success) {
                    alert(res.message);
                    return;
                }

                vm.debts.splice(idx, 1);
            });
        }

        function debtFormReset() {
            vm.debtData = angular.copy(vm.debtModal.dataMaster);
        }

        function debtSendForm() {
            var form = angular.copy(vm.debtData);
            form.fecha = dateToForm(form.fecha);
            vm.saving = true;
            $('#modal-debt [type="submit"]').addClass("btn-loading");

            if(form.id) {
                Debts.editDebt(form).success(function(res){
                    vm.saving = false;
                    $('#modal-debt [type="submit"]').removeClass("btn-loading");
                    if(!res.success) {
                        alert("Error: " + res.message);
                        return;
                    }

                    var idx = getDebtIdxByID(form.id);
                    vm.debts[idx] = res.data;
                    $('#modal-debt').modal('hide');
                });
                return;
            } else {
                Debts.addDebt(form).success(function(res){
                    vm.saving = false;
                    $('#modal-debt [type="submit"]').removeClass("btn-loading");
                    if(!res.success) {
                        alert("Error: " + res.message);
                        return;
                    }

                    $('#modal-debt').modal('hide');
                    vm.debts.splice(0, 0, res.data);
                });
            }
        };
        
        function debtShowAdd() {
            vm.debtorModal.title = "Agregar deuda";
            vm.debtorModal.submitText = "Agregar";
            vm.debtFormReset();
            $('#modal-debt').modal('show');
        }

        function debtShowEdit(id) {
            var debt = angular.copy(getDebtByID(id));
            if(!debt) {
                alert("Deuda no encontrada");
                return;
            }

            vm.debtModal.title = "Editar deuda #" + id;
            vm.debtModal.submitText = "Guardar";
            vm.debtFormReset();

            debt.fecha = new Date(debt.fecha);
            debt.monto = parseInt(debt.monto);
            vm.debtData = debt;
            $('#modal-debt').modal('show');
        };

        function debtsPaidFor(id) {
            var debtor = getDebtorByID(id);
            
            if(debtor === null) {
                alert("El deudor no existe");
                return;
            }

            vm.saving = true;
            $('[data-setpf-id="'+id+'"]').addClass("btn-loading");

            Debts.setPaidFor(id, 1).success(function(res){
                vm.saving = false;
                $('[data-setpf-id="'+id+'"]').removeClass("btn-loading");
                if(!res.success) {
                    alert(res.message);
                    return;
                }

                for(var i = 0; i < vm.debts.length; i++) {
                    if(vm.debts[i].deudor == id) {
                        vm.debts[i].pagada = "1";
                    }
                }
            });
        }

        function getDebtorByID(id) {
            for(var i = 0; i < vm.debtors.length; i++) {
                var debtor = vm.debtors[i];
                if(debtor.id == id) {
                    return debtor;
                }
            }

            return null;
        }

        function getDebtByID(id) {
            for(var i = 0; i < vm.debts.length; i++) {
                var debt = vm.debts[i];
                if(debt.id == id) {
                    return debt;
                }
            }

            return null;
        }

        function getDebtIdxByID(id) {
            for(var i = 0; i < vm.debts.length; i++) {
                var debt = vm.debts[i];
                if(debt.id == id) {
                    return i;
                }
            }

            return null;
        }

        function getDebtorIdxByID(id) {
            for(var i = 0; i < vm.debtors.length; i++) {
                var debtor = vm.debtors[i];
                if(debtor.id == id) {
                    return i;
                }
            }

            return null;
        }

        function hasDebt(debtor) {
            return vm.options.showAllDebtors || vm.sumDebt(debtor.id) > 0;
        }

        function sumDebt(id) {
            var sum = 0;
            for(var i = 0; i < vm.debts.length; i++) {
                var debt = vm.debts[i];
                if(debt.deudor != id || debt.pagada != "0") {
                    continue;
                }

                sum += parseInt(vm.debts[i].monto);
            }

            return sum;
        }

        function sumDebtTotal() {
            var sum = 0;
            for(var i = 0; i < vm.debts.length; i++) {
                var debt = vm.debts[i];
                if(debt.pagada != "0") continue;
                sum += parseInt(vm.debts[i].monto);
            }

            return sum;
        }

        function togglePaid(id) {
            if(vm.saving) {
                return;
            }

            var debt = getDebtByID(id);
            var idx = getDebtIdxByID(id);
            
            if(debt === null) {
                alert("La deuda no existe");
                return;
            }

            var pagada = debt.pagada=="1";

            vm.saving = true;
            $('[data-debt-ptb="'+id+'"]').addClass("btn-loading");
            Debts.setPaid(id, !pagada).success(function(data){
                vm.saving = false;
                $('[data-debt-ptb="'+id+'"]').removeClass("btn-loading");

                if(!data.success) {
                    alert("Error: " + data.message);
                } else {
                    vm.debts[idx].pagada = (!pagada) ? "1" : "0";
                }
            });
        }
    }
})();