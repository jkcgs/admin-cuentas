(function(){
    'use strict';

    angular.module('app', [
        'ngRoute',
        'ngCookies',
        'routeStyles'
    ])
    .service('authInterceptor', authInterceptor)
    .config(config)
    .run(run)
    .directive('menubar', menubar)
    .filter('formatMoney', formatMoney);
 
    config.$inject = ['$routeProvider', '$locationProvider', '$httpProvider'];
    function config($routeProvider, $locationProvider, $httpProvider) {
        $httpProvider.defaults.headers.post['Content-Type'] = 'application/x-www-form-urlencoded;charset=utf-8';
        $httpProvider.interceptors.push('authInterceptor');
        $locationProvider.hashPrefix('!');
        $routeProvider.otherwise({ redirectTo: '/login' });

        $httpProvider.interceptors.push(function($q) {
            return {
                'responseError': function(response) {
                    if(response.headers()['content-type'] == "application/json") {
                        alert("Error: " + response.data.message);
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
                location.hash = "!/";
            });
        };

        $rootScope.addAccount = function(data) {
            $rootScope.accAddData = data;
            $('#ext-debts').modal("hide");

            if($rootScope.path == "/accounts") {
                $route.reload();
            } else {
                location.hash = "!/accounts";
            }
        };

        $rootScope.$on('$locationChangeStart', function(event){
            $rootScope.path = $location.path();
            session.isLogged();
        });  
        
        session.isLogged();
    }

    authInterceptor.$inject = ['$q'];
    function authInterceptor($q) {
        return {
            'responseError': function(response) {
                if (response.status == 401){
                    location.hash = "!/login";
                }
                return $q.reject(response);
            }
        };
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