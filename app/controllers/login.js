(function(){
    'use strict';

    angular
        .module('app')
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
        $scope.loading = false;
        $scope.appError = false;

        $scope.send = function() {
            $scope.loading = true;
            session.login($scope.username, $scope.password)
                .then(function(res){
                    if(!res.data.success) {
                        var error = res.data.message || res.data;
                        return alert("Error: " + error);
                    }

                    location.hash = "!/accounts";
                }).finally(function(){
                    $scope.loading = false;
                });
        };

        init();
        function init(){
            session.isLogged().then(function(res){
                if(res.success && res.data.data.logged) {
                    location.hash = "!/accounts";
                } else {
                    $scope.appError = res.data.message;
                }    
            });
        }
    }

}());