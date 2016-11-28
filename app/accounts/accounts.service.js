(function() {
'use strict';

    angular
        .module('app')
        .service('Accounts', Accounts);

    Accounts.$inject = ['$http'];
    function Accounts($http) {
        this.get = get;
        this.add = add;
        this.edit = edit;
        this.del = del;
        this.setPaid = setPaid;

        ////////////
        function add(data) {
            var ep = 'api/?accounts/add';
            return $http.post(ep, $.param(data));
        }

        function del(id) {
            var ep = 'api/?accounts/delete&id='+id;
            return $http.get(ep);
        }

        function edit(data){
            var ep = 'api/?accounts/edit&id='+data.id;
            return $http.post(ep, $.param(data));
        }

        function get(id){
            var ep = 'api/?accounts/get';
            if(typeof id != "undefined") {
                ep += '?id=' + id;
            }

            return $http.get(ep);
        }

        function setPaid(ids) {
            var ep = 'api/?accounts/set_paid';
            return $http.post(ep, $.param({"ids": ids}));
        }
    }
})();