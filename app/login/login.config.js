(function(){
    'use strict';

    angular
        .module('app')
        .config(LoginConfig);

    /** @ngInject */
    function LoginConfig($routeProvider, $locationProvider){
        $routeProvider
            .when('/login', {
                controller: 'LoginController',
                controllerAs: 'vm',
                templateUrl: 'app/login/login.html',
                css: 'assets/css/login.css'
            });
    }

}());