(function(){
    'use strict';

    angular
        .module('app.login', ['ngRoute'])
        .config(config)
        .controller('LoginController', ['$scope', 'session', LoginController]);
    
    config.$inject = ['$routeProvider', '$locationProvider'];
    function config($routeProvider, $locationProvider) {
        $routeProvider
            .when('/login', {
                controller: 'LoginController',
                templateUrl: 'app/views/login.html',
                css: 'assets/css/login.css'
            });
    }

    function LoginController($scope, session){
        $scope.username = "";
        $scope.password = "";

        $scope.send = function() {
            session.login($scope.username, $scope.password)
                .then(function(res){
                    if(!res.data.success) {
                        return alert("Error: " + res.data.message);
                    }

                    location.hash = "!/accounts";
                });
        };

        init();
        function init(){
            session.isLogged().then(function(res){
                if(res.data.data.logged) {
                    location.hash = "!/accounts";
                }
            });
        }
    }

}());