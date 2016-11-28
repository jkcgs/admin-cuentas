(function() {
'use strict';

    angular
        .module('app')
        .controller('LoginController', LoginController);

    LoginController.$inject = ['$rootScope', '$location', 'Login'];
    function LoginController($rootScope, $location, Login) {
        var vm = this;
        vm.username = "";
        vm.password = "";
        vm.loading = false;
        vm.errorMessage = false;
        vm.doLogin = doLogin;

        ////////////////

        function doLogin() {
            vm.loading = true;
            Login.doLogin(vm.username, vm.password)
                .then(
                    function(res){
                        if(!res.data.success) {
                            var error = res.data.message || res.data;
                            alert("Error: " + error);
                        } else {
                            $location.path("!/accounts");
                        }
                    },

                    function(reason) {
                        $rootScope.appError = reason;
                    }
                )
                .finally(function(){
                    vm.loading = false;
                });
        }
    }
})();