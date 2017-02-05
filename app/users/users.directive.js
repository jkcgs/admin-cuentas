(function() {
    'use strict';

    angular
        .module('app')
        .directive('users', Users);

    function Users() {
        var directive = {
            bindToController: true,
            controller: UsersController,
            controllerAs: 'vm',
            restrict: 'A',
            templateUrl: 'app/users/users.html'
        };

        return directive;
    }

    ////////////////

    UsersController.$inject = ['$rootScope', '$timeout', 'users'];
    function UsersController($rootScope, $timeout, users) {
        var vm = this;
        vm.users = [];
        vm.doEdit = doEdit;
        vm.saveUser = saveUser;
        vm.masterUsuario = {
            id: null,
            user: "",
            password: "",
            repassword: "",
            enabled: "1",
            is_admin: "0"
        };
        
        vm.editUserData = angular.copy(vm.masterUsuario);
        vm.nuevoUsuario = angular.copy(vm.masterUsuario);

        activate();

        ////////////////

        function activate() {
            $rootScope.$on('loginAction', function(data){
                load();
            });

            $rootScope.$on('logoutAction', function(){
                vm.users = [];
            });

            $timeout(function(){
                if($rootScope.logged && $rootScope.sessionInfo.is_admin) {
                    load();
                }
            });            
        }

        function load() {
            users.getAll().then(
                function(users) {
                    vm.users = users;
                },

                function(reason) {
                    alert("No se pudo cargar los usuarios: " + reason);
                }
            );
        }

        ///////////////

        function doEdit(id) {
            for(var i = 0; i < vm.users.length; i++) {
                if(vm.users[i].id === id) {
                    vm.userEditData = angular.copy(vm.users[i]);
                    break;
                }
            }
        }

        function saveUser() {
            vm.userEditData = angular.copy(vm.masterUsuario);
        }
    }
})();