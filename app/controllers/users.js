(function() {
'use strict';

    angular
        .module('app')
        .controller('UsersController', UsersController);

    UsersController.$inject = ['$rootScope', 'users'];
    function UsersController($rootScope, users) {
        var vm = this;
        vm.users = [];

        activate();

        ////////////////

        function activate() {
            users.getAll().then(
                function(users) {
                    vm.users = users;
                },

                function(reason) {
                    alert("No se pudo cargar los usuarios: " + reason);
                }
            );
        }
    }
})();