(function(){
    'use strict';

    angular.module('app', [
        'ngRoute',
        'ngCookies',
        'routeStyles',
        'app.login',
        'app.accounts',
        'app.debtors'
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

    run.$inject = ['$rootScope', '$location', 'session'];
    function run($rootScope, $location, session) {
        $rootScope.logout = function() {
            session.logout().then(function(){
                location.hash = "!/";
            });
        };

        $rootScope.$on('$locationChangeStart', function(event){
            $rootScope.path = $location.path();
        });        
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