(function(){
    'use strict';

    angular
        .module('app')
        .service('accounts', ['$http', accounts]);

    function accounts($http){
        this.get = get;
        this.add = add;
        this.edit = edit;
        this.del = del;

        function get(id){
            var ep = 'api.php?accounts/get';
            if(typeof id != "undefined") {
                ep += '?id=' + id;
            }

            return $http.get(ep);
        }

        function add(data) {
            var ep = 'api.php?accounts/add';
            return $http.post(ep, $.param(data));
        }

        function edit(data){
            var ep = 'api.php?accounts/edit&id='+data.id;
            return $http.post(ep, $.param(data));
        }

        function del(id) {
            var ep = 'api.php?accounts/delete&id='+id;
            return $http.get(ep);
        }
    }

}());