(function() {
'use strict';

    angular
        .module('app')
        .controller('AccountsController', AccountsController);

    AccountsController.$inject = ['$rootScope', 'Accounts'];
    function AccountsController($rootScope, Accounts) {
        var vm = this;
        vm.loaded = false;
        vm.saving = false;
        vm.accounts = [];
        vm.accountForm = {};
        vm.modalTitle = "";
        vm.modalSubmit = "";

        vm.getSumUnpaid = getSumUnpaid;
        vm.createDebt = createDebt;
        vm.showAdd = showAdd;
        vm.showEdit = showEdit;
        vm.cloneAccount = cloneAccount;
        vm.sendForm = sendForm;
        vm.delAccount = delAccount;
        vm.setPaid = setPaid;
        vm.setUnpaidPaid = setUnpaidPaid;

        vm.masterForm = {
            nombre: "",
            descripcion: "",
            fecha_compra: new Date(),
            fecha_facturacion: getInitialBilling(),
            monto_original: 0,
            divisa_original: "CLP",
            monto: 0,
            num_cuotas: 0,
            pagado: 0,
            info: "",
            id: null
        };

        activate();

        ////////////////

        function activate() {
            Accounts.get().success(function(data){
                if(data.hasOwnProperty("success") && !data.success) {
                    $rootScope.appError = "No se pudo cargar las cuentas: " + data.message;
                    vm.loaded = true;

                    return;
                }

                vm.accounts = data;
                vm.loaded = true;
            });
            
            // If there is new account data to process, emit a broadcast received
            // from the function below
            angular.element(window).ready(function() {
                if($rootScope.accAddData) {
                    var data = angular.copy($rootScope.accAddData);

                    $rootScope.accAddData = null;
                    $rootScope.$emit("addAccountAction", data);
                }
            });

            // Receives new account data and shows the corresponding modal
            $rootScope.$on('addAccountAction', function(ev, data) {
                $('.modal').modal("hide");
                var formData = {
                    nombre: data.comercio,
                    fecha_compra: new Date(data.fecha),
                    monto_original: data.valor,
                    divisa_original: "CLP",
                    monto: data.valor,
                    cuotas: data.cuotas,
                    info: data.documento ? "Documento: " + data.documento : ""
                };

                vm.showAdd(formData);
            });
            
            $rootScope.$on('accountsError', function(){
                vm.saving = false;
            });
        }

        //////************////////

        function cloneAccount(id) {
            var account = getByID(id);
            if(!account) {
                alert("Cuenta no encontrada");
                return;
            }

            vm.showAdd(account);
        }

        function createDebt(id) {
            var account = getByID(id);
            if(!account) {
                alert("Cuenta no encontrada");
                return;
            }

            var debtData = {
                descripcion: account.descripcion,
                monto: parseInt(account.monto),
                fecha: new Date(),
                pagada: 0
            };

            $rootScope.addDebt(debtData);
        }

        function dataPreprocess(data) {
            if(data === null) {
                return null;
            }

            var account = $.extend(vm.masterForm, data);
            // Preformatting
            account.monto_original = parseFloat(account.monto_original);
            account.monto = parseFloat(account.monto);
            account.num_cuotas = parseInt(account.num_cuotas);
            account.fecha_compra = new Date(account.fecha_compra);
            account.fecha_facturacion = new Date(account.fecha_facturacion);

            return account;
        }

        function delAccount(id) {
            var accIndex = getIndexByID(id);
            
            if(accIndex === null) {
                alert("La cuenta no existe");
                return;
            }

            if(!confirm("¿Realmente quieres eliminar esta cuenta? Esta acción es irreversible")) {
                return;
            }

            vm.saving = true;
            $('[data-del-id="'+id+'"]').addClass("btn-loading");

            accounts.del(id).success(function(res){
                vm.saving = false;
                $('[data-del-id="'+id+'"]').removeClass("btn-loading");
                if(!res.success) {
                    alert(res.message);
                    return;
                }

                vm.accounts.splice(accIndex, 1);
            });
        }

        function formReset() {
            vm.accountForm = angular.copy(vm.masterForm);
        }

        function getByID(id) {
            for(var i = 0; i < vm.accounts.length; i++) {
                var account = vm.accounts[i];
                if(account.id == id) {
                    return account;
                }
            }

            return null;
        }

        function getIndexByID(id) {
            for(var i = 0; i < vm.accounts.length; i++) {
                var account = vm.accounts[i];
                if(account.id == id) {
                    return i;
                }
            }

            return null;
        }

        function getInitialBilling() {
            var initialBilling = new Date();
            if(initialBilling.getDate() > 5) {
                var month = initialBilling.getMonth();
                initialBilling.setMonth(month == 11 ? 0 : (month + 1));
            }

            return initialBilling;
        }

        function getSumUnpaid() {
            var total = 0;
            for(var i = 0; i < vm.accounts.length; i++){
                if(vm.accounts[i].pagado != "0") {
                    continue;
                }

                total += parseInt(vm.accounts[i].monto);
            }
            return total;
        }

        // Add and edit
        function sendForm() {
            var form = angular.copy(vm.accountForm);
            if(!form.divisa_original) {
                form.divisa_original = "CLP";
            }

            form.fecha_compra = dateToForm(form.fecha_compra);
            form.fecha_facturacion = dateToForm(form.fecha_facturacion, false);

            vm.saving = true;
            $('#modal-cuenta [type="submit"]').addClass("btn-loading");
            if(form.id) {
                Accounts.edit(form).success(function(res){
                    vm.saving = false;
                    $('#modal-cuenta [type="submit"]').removeClass("btn-loading");
                    if(!res.success) {
                        alert("Error: " + res.message);
                        return;
                    }

                    var idx = getIndexByID(form.id);
                    vm.accounts[idx] = res.data;
                    $('#modal-cuenta').modal('hide');
                });
                return;
            } else {
                Accounts.add(form).success(function(res){
                    vm.saving = false;
                    $('#modal-cuenta [type="submit"]').removeClass("btn-loading");
                    if(!res.success) {
                        alert("Error: " + res.message);
                        return;
                    }

                    $('#modal-cuenta').modal('hide');
                    vm.accounts.splice(0, 0, res.data);
                });
            }
        }

        function setPaid(id) {
            if(vm.saving || !getByID(id)) {
                return;
            }


            var $b = $("[data-asp-id='"+id+"']");
            $b.addClass("btn-loading");
            vm.saving = true;
            Accounts.setPaid([id]).success(function(res){
                vm.saving = false;
                $b.removeClass("btn-loading");

                if(!res.success) {
                    alert("Error: " + res.message);
                    return;
                }

               getByID(id).pagado = "1";
            });
        }

        function setUnpaidPaid() {
            if(vm.saving) {
                return;
            }

            var unpaid = vm.accounts.filter(function(acc){
                return acc.pagado === "0";
            });

            var unpaIDs = unpaid.map(function(acc){
                return acc.id;
            });

            var $b = $("#acc-set-unpaid-paid");
            $b.addClass("btn-loading");
            vm.saving = true;
            Accounts.setPaid(unpaIDs).success(function(res){
                vm.saving = false;
                $b.removeClass("btn-loading");

                if(!res.success) {
                    alert("Error: " + res.message);
                    return;
                }

                unpaIDs.forEach(function(id) {
                    getByID(id).pagado = "1";
                }, this);
            });
        }

        function showAdd(filldata) {
            formReset();
            if(typeof filldata !== "undefined") {
                vm.accountForm = dataPreprocess(filldata);
            }
            
            vm.modalTitle = "Agregar cuenta";
            vm.modalSubmit = "Agregar";
            
            $('#modal-cuenta').modal('show');
        }

        function showEdit(id) {
            var account = dataPreprocess(getByID(id));
            if(!account) {
                alert("Cuenta no encontrada");
                return;
            }

            vm.modalTitle = "Editar cuenta #" + id;
            vm.modalSubmit = "Guardar";
            formReset();

            vm.accountForm = account;
            $('#modal-cuenta').modal('show');
        }
    }
})();