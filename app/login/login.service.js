(function() {
'use strict';

    angular
        .module('app')
        .service('Login', Login);

    Login.$inject = ['$http', '$rootScope'];
    function Login($http, $rootScope) {
        this.isLogged = isLogged;
        this.doLogin = doLogin;
        this.logout = logout;
        
        ////////////////
        function isLogged (callback){
            callback = callback || function(){};

            $http.get('api/?session/logged').then(function(response){
                try {
                    var res = checkData(response.data, true);
                    $rootScope.logged = res.data.logged;
                    $rootScope.sessionInfo = res.data.user;

                    callback($rootScope.logged, null);
                } catch(e) {
                    $rootScope.logged = false;
                    callback(false, e);
                }
            }).catch(function(cause){
                $rootScope.logged = false;
                callback(false, cause);
            });
        }

        function doLogin(user, pass) {
            var d = {username: user, password: btoa(pass)};
            return $http.post('api/?session/login', $.param(d))
                .then(function(res){
                    $rootScope.logged = !!res.success;
                    $rootScope.sessionInfo = res.data.user;

                    $rootScope.$emit('loginAction', {
                        logged: $rootScope.logged,
                        sessionInfo: $rootScope.sessionInfo
                    });

                    return res;
                });
        }

        function logout() {
            return $http.get('api/?session/logout').then(function(res){
                $rootScope.logged = false;
                $rootScope.sessionInfo = null;

                $rootScope.$emit('logoutAction');
                return res;
            });
        }
    }
})();