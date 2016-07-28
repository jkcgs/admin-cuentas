(function(){
    'use strict';

    angular
        .module('app')
        .service('session', ['$http', '$rootScope', session]);

    function session($http, $rootScope){
        return {
            isLogged: function (){
                return $http.get('api.php?session/logged').success(function(res){
                    if(res.success && res.data.logged) {
                        $rootScope.$emit('loggedIn');
                        $rootScope.logged = true;
                    } else if(!res.success) {
                        $rootScope.logged = false;
                        if($rootScope.path != "/login") {
                            location.hash = "!/login";
                        }
                    }
                });
            },

            login: function(user, pass) {
                var d = {username: user, password: btoa(pass)};
                return $http.post('api.php?session/login', $.param(d))
                .success(function(res){
                    if(res.success && res.data.logged) {
                        $rootScope.$emit('loggedIn');
                        $rootScope.logged = true;
                    }
                });
            },

            logout: function() {
                return $http.get('api.php?session/logout').success(function(){
                    $rootScope.logged = false;
                });
            }
        };        
    }
}());