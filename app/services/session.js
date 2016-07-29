(function(){
    'use strict';

    angular
        .module('app')
        .service('session', ['$http', '$rootScope', session]);

    function session($http, $rootScope){
        return {
            isLogged: function (callback){
                callback = callback || function(){};

                $http.get('api.php?session/logged').then(function(response){
                    var res = checkData(response.data, true);
                    if(res.data.logged) {
                        $rootScope.logged = true;
                    } else {
                        $rootScope.logged = false;
                    }

                    callback($rootScope.logged, null);
                }).catch(function(cause){
                    console.error("Could not check session status");
                    console.error(cause);
                    $rootScope.logged = false;
                    callback(false, cause);
                });
            },

            login: function(user, pass) {
                var d = {username: user, password: btoa(pass)};
                return $http.post('api.php?session/login', $.param(d))
                    .then(function(res){
                        $rootScope.logged = !!res.success;
                        return res;
                    });
            },

            logout: function() {
                return $http.get('api.php?session/logout').then(function(res){
                    $rootScope.logged = false;
                    return res;
                });
            }
        };        
    }
}());