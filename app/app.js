(function(){
    'use strict';

    angular.module('app', [
        'ngRoute',
        'routeStyles'
    ])
    .config(config)
    .run(run)
    .filter('formatMoney', formatMoney);
 
    config.$inject = ['$routeProvider', '$locationProvider', '$httpProvider'];
    function config($routeProvider, $locationProvider, $httpProvider) {
        $httpProvider.defaults.headers.post['Content-Type'] = 'application/x-www-form-urlencoded;charset=utf-8';
        $locationProvider.hashPrefix('!');
        $routeProvider.otherwise({ redirectTo: '/login' });

        $httpProvider.interceptors.push(function($q, $location) {
            return {
                'responseError': function(response) {
                    if (response.status == 401){
                        $location.path("/login");
                        return $q.reject("Sesión no iniciada");
                    }

                    var error = "Error desconocido";
                    if (typeof response == "string") {
                        error = response;
                    } else if ("data" in response) {
                        if (typeof response.data == "object" && "message" in response.data) {
                            error = response.data.message;
                        } else if(response.data.trim() == "File not found.") {
                            error = "El backend no está funcionando.";
                        } else {
                            error = response.data;
                        }
                    } else if("message" in response) {
                        error = response.message;
                        if(error.indexOf("Unexpected token <") === 0) {
                            error = "Error en backend. Contacta a un administrador.";
                        }
                    }

                    return $q.reject(error);
                }
            };
        });
    }

    run.$inject = ['$rootScope', '$location', '$route', '$timeout', 'Login', 'ExternalsService'];
    function run($rootScope, $location, $route, $timeout, Login, ExternalsService) {
        $rootScope.addAccount = addAccount;
        $rootScope.addDebt = addDebt;
        $rootScope.logout = logout;
        $rootScope.$on('$locationChangeSuccess', onLocationChange);

        //////////////////

        function addAccount(data) {
            if($rootScope.path == "/accounts") {
                $rootScope.$emit("addAccountAction", data);
            } else {
                $rootScope.accAddData = data;
                $location.path("/accounts");
            }
        }

        function addDebt(data) {
            if($rootScope.path == "/debtors") {
                $rootScope.$emit("addDebtAction", data);
            } else {
                $rootScope.debtAddData = data;
                $location.path("/debtors");
            }
        }

        function logout() {
            Login.logout().then(function(){
                $location.path("/login");
            });
        }

        function onLocationChange(event){
            var path = $location.path();
            $rootScope.path = path;

            Login.isLogged(function(logged, error){
                fetchDolar(false);

                if(error) {
                    $rootScope.appError = error.message || error;
                } else if(logged && path == "/login") {
                    $location.path("/accounts");
                }
            });
        }

        var fetchDolar = function(daemon) {
            if(typeof daemon == "undefined") daemon = true;

            // No cargar dolar si no se ha iniciado sesión
            if($rootScope.logged) {
                ExternalsService.getDolar().success(function(res, status){
                    $rootScope.dolar = res.data;
                    if(daemon) setTimeout(fetchDolar, 10000);
                });
            } else {
                if(daemon) setTimeout(fetchDolar, 10000);
            }
        };
        fetchDolar();

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
    }

    function formatMoney(){
        return function(s){
            if(typeof s == "number") s = s.toString();
            else if(s === "") s = "0";

            return "$" + s.reverse().match(/[0-9]{1,3}/g).join('.').reverse();
        };
    }
    
}());