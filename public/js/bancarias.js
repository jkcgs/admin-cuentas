var bancarias = new Ractive({
    el: "#cont-bancarias",
    template: "#tpl-bancarias",

    data: {
        loading: false,
        errorMsg: false,
        cuentas: []
    },
    
    load: function(callback){
        if(this.get('loading')) return;
        this.set('loading', true);
        this.set('errorMsg', false);
        var _t = this;

        $.getJSON('acciones/?external/bancoestado', function(data){
            if(data.error) {
                callback(new Error(data.error), data);
                return;
            }

            if(!data.hasOwnProperty('message')) {
                var errorMsg = data.hasOwnProperty('error') ? data.error : "No se recibió OK",
                    e = new Error(errorMsg);

                callback(e);
                console.log(e, data);
                return;
            }

            _t.set('cuentas', data.data);
            callback(null);
        })
        .error(function(req, status, err){
            callback(new Error("Error al cargar datos externos"));
        })
        .always(function(){
            _t.set('loading', false);
        });
    },

    show: function() {
        if(this.get('cuentas').length == 0) {
            this.update();
        }

        $("#bancarias").modal("show");
    },

    update: function() {
        var _t = this;
        this.load(function(e){
            _t.set('errorMsg', e ? e.message : false);
        });
    }
});
