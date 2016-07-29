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
        $scope.errorMessage = false;

        $scope.send = function() {
            $scope.loading = true;
            session.login($scope.username, $scope.password)
                .then(function(res){
                    if(!res.data.success) {
                        var error = res.data.message || res.data;
                        alert("Error: " + error);
                    } else {
                        location.hash = "!/accounts";
                    }
                })
                .catch(function(reason){
                    $scope.appError = reason;
                })
                .finally(function(){
                    $scope.loading = false;
                });
        };
    }
}());