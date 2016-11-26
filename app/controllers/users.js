(function() {
'use strict';

    angular
        .module('app')
        .controller('UsersController', UsersController);

    UsersController.$inject = ['$rootScope', '$timeout', 'users'];
    function UsersController($rootScope, $timeout, users) {
        var vm = this;
        vm.users = [];

        activate();

        ////////////////

        function activate() {
            $rootScope.$on('loginAction', function(data){
                if(!data.logged || !data.sessionInfo.is_admin) {
                    return false;
                }

                load();
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

        $timeout(function(){
            load();
        });
    }
})();