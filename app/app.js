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
                        var error = "Error desconocido";
                        if("data" in response) {
                            if("message" in response.data) {
                                error = response.data.message;
                            } else {
                                error = response.data;
                            }
                        } else if("message" in response) {
                            error = response.message;
                        } else if(typeof response == "string") {
                            error = response;
                        }
                        
                        console.log("Error: " + error);
                    }
                    
                    return $q.reject(response);
                }
            };
        });
    }

    run.$inject = ['$rootScope', '$location', '$route', 'session', 'external'];
    function run($rootScope, $location, $route, session, external) {
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
                fetchDolar(false);

                if(error) {
                    $rootScope.appError = error.message || error;
                } else if(logged && path == "/login") {
                    $location.path("/accounts");
                }
            });
        });

        var fetchDolar = function(daemon) {
            if(typeof daemon == "undefined") daemon = true;

            // No cargar dolar si no se ha iniciado sesión
            if($rootScope.logged) {
                external.getDolar().success(function(res, status){
                    $rootScope.dolar = res.data;
                    if(daemon) setTimeout(fetchDolar, 10000);
                });
            } else {
                if(daemon) setTimeout(fetchDolar, 10000);
            }
        }
        fetchDolar();
    }

    menubar.$inject = ['$timeout'];
    function menubar($timeout) {
        $timeout(function(){
            angular.element(document).ready(function() {
                $(document).click(function (event) {
                    var clickover = $(event.target);
                    var _opened = $(".navbar-collapse").hasClass("in");
                    if (_opened === true && !clickover.hasClass("navbar-toggle")) {
                        $('.collapse').collapse('hide');
                    }
                });
            });
        });

        return {
            templateUrl: "app/views/directives/menubar.html",
            scope: {
                'dolar': '@'
            }
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