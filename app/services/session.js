(function(){
    'use strict';

    angular
        .module('app')
        .service('session', ['$http', '$rootScope', session]);

    function session($http, $rootScope){
        return {
            isLogged: function (callback){
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
            },

            login: function(user, pass) {
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
            },

            logout: function() {
                return $http.get('api/?session/logout').then(function(res){
                    $rootScope.logged = false;
                    return res;
                });
            }
        };        
    }
}());