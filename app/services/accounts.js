(function(){
    'use strict';

    angular
        .module('app')
        .service('accounts', ['$http', accounts]);

    function accounts($http){
        this.get = get;

        function get(id){
            var ep = 'api.php?accounts/get';
            if(typeof id != "undefined") {
                ep += '?id=' + id;
            }

            return $http.get(ep);
        }
    }

}());