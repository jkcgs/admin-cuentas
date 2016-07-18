(function(){
    'use strict';

    angular
        .module('app')
        .service('session', ['$http', session]);

    function session($http){
        this.isLogged = function (){
            return $http.get('api.php?session/logged');
        };

        this.login = function(user, pass) {
            var d = {username: user, password: btoa(pass)};
            return $http.post('api.php?session/login', $.param(d));
        };

        this.logout = function() {
            return $http.get('api.php?session/logout');
        }
    }

}());