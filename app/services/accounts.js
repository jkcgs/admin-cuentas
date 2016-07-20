(function(){
    'use strict';

    angular
        .module('app')
        .service('accounts', ['$http', accounts]);

    function accounts($http){
        this.get = get;
        this.add = add;
        this.edit = edit;

        function get(id){
            var ep = 'api.php?accounts/get';
            if(typeof id != "undefined") {
                ep += '?id=' + id;
            }

            return $http.get(ep);
        }

        function add(data) {
            var ep = 'api.php?accounts/add';
            return $http.get(ep);
        }

        function edit(id){
            if(typeof id == "undefined") {
                alert("accounts.edit: Must send ID");
                return;
            }

            var ep = 'api.php?accounts/edit&id='+id;
            return $http.get(ep);
        }

    }

}());