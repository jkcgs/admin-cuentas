(function(){
    'use strict';

    angular.module('app', [
        'ngRoute',
        'ngCookies',
        'routeStyles'
    ])
    .config(config)
    .run(run)
    .directive('menubar', menubar)
    .filter('formatMoney', formatMoney);
 
    config.$inject = ['$routeProvider', '$locationProvider', '$httpProvider'];
    function config($routeProvider, $locationProvider, $httpProvider) {
        $httpProvider.defaults.headers.post['Content-Type'] = 'application/x-www-form-urlencoded;charset=utf-8';
        $locationProvider.hashPrefix('!');
        $routeProvider.otherwise({ redirectTo: '/login' });

        $httpProvider.interceptors.push(function($q) {
            return {
                'responseError': function(response) {
                    if (response.status == 401){
                        location.hash = "!/login";
                    } else {
                        var error = response.data.message || response.data || "Error desconocido";
                        alert("Error: " + error);
                    }
                    
                    return $q.reject(response);
                }
            };
        });
    }

    run.$inject = ['$rootScope', '$location', '$route', 'session'];
    function run($rootScope, $location, $route, session) {
        $rootScope.logout = function() {
            session.logout().then(function(){
                $location.path("/login");
            });
        };

        $rootScope.addAccount = function(data) {
            $rootScope.accAddData = data;
            $('.modal').modal("hide");

            if($rootScope.path == "/accounts") {
                $route.reload();
            } else {
                $location.path("/accounts");
            }
        };

        $rootScope.addDebt = function(data) {
            $rootScope.debtAddData = data;
            $('.modal').modal("hide");

            if($rootScope.path == "/debtors") {
                $route.reload();
            } else {
                $location.path("/debtors");
            }
        };

        $rootScope.$on('$locationChangeSuccess', function(event){
            var path = $location.path();
            $rootScope.path = path;

            session.isLogged(function(logged, error){
                if(error) {
                    $rootScope.appError = error.message || error;
                } else if(logged && path == "/login") {
                    $location.path("/accounts");
                }
            });
        });
    }

    function menubar() {
        return {
            templateUrl: "app/views/directives/menubar.html"
        };
    }

    function formatMoney(){
        return function(s){
            if(typeof s == "number") s = s.toString();
            else if(s === "") s = "0";

            return "$" + s.reverse().match(/[0-9]{1,3}/g).join('.').reverse();
        };
    }
    
}());