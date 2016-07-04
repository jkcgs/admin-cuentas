var cuentas = new ObjBase({
    el: "#cont-cuentas",
    template: "#tpl-cuentas",
    data: {
        cuentas: [],
        pagina_actual: 0,
        n_display: 20,
        no_pagadas: function(event) {
            return cuentas.get('cuentas').filter(function(e){
                return 'pagado' in e && e.pagado != "1";
            });
        },
        sumar_np: function(event) {
            var np = cuentas.get('no_pagadas')();
            var suma = 0;
            for(var i = 0; i < np.length; i++) {
                suma += parseFloat(np[i].monto);
            }

            return suma;
        },

        get_page: function(p) {
            var n = cuentas.get('n_display');
            if(p*n >= cuentas.get('cuentas').length) {
                p = 0;
            }

            return cuentas.get('cuentas').slice(p*n, p*n+n);
        },

        get_page_numbers: function() {
            return new Array(Math.ceil(cuentas.get('cuentas').length/cuentas.get('n_display')));
        }
    },

    obj_name: "cuentas",
    modal: "#agregar-cuenta",
    strings: {
        t_agregar: "Agregar cuenta",
        t_agregar_btn: "Agregar",
        t_editar: "Editar cuenta #",
    },
    acciones: {
        agregar: "agregar_cuenta",
        editar: "editar_cuenta",
        borrar: "borrar&t=cuentas",
        get: "get&t=cuentas"
    },
    form: document.agregar_cuenta,
    form_elements: [
        'nombre', 'descripcion', 'fecha_compra', 'fecha_facturacion', 'monto_original',
        'divisa_original', 'monto', 'cuotas', 'pagado', 'info'
    ],
    form_defaults: {
        'cuotas': 0,
        'divisa_original': 'CLP',
        'fecha_compra': inFechaHoy()
    },

    pagar_nopagadas: function(){
        if(this.get('working')) return;
        if(!confirm("¿Realmente quieres marcarlas como pagadas?")) return;
        var indexes = [], ids = [], _t = this;
        var cuentas = this.get('cuentas');
        for(var i = 0; i < cuentas.length; i++) {
            var c = cuentas[i];
            if(c.pagado != "1") {
                indexes.push(i);
                ids.push(c.id);
            }
        }

        if(indexes.length <= 0) return;

        $('#btn-pagar').addClass("btn-loading");
        this.jget('acciones/?marcar_pagadas&ids='+ids.join(','), function(err, data){
            $('#btn-pagar').removeClass("btn-loading");
            if(err) {
                alert("Error: " + err.message);
                return;
            }

            for(var i = 0; i < indexes.length; i++) {
                _t.set('cuentas.'+i+'.pagado', "1");
            }
        });
    }
});

cuentas.on('creardeuda', function(e, id){
    var x = cuentas.dget(id);
    location.hash = "deudores";
    deudas.mostrar_agregar({
        descripcion: x.nombre + " " + x.descripcion,
        fecha: x.fecha_compra,
        monto: x.monto
    });
});

cuentas.on('pagina', function(e, p) {
    cuentas.set('pagina_actual', p);
});

cuentas.on('pagina_ant', function(e) {
    var p = cuentas.get('pagina_actual');
    cuentas.set('pagina_actual', p <= 0 ? 0 : p-1);
});

cuentas.on('pagina_sig', function(e) {
    var p = cuentas.get('pagina_actual'),
        l = cuentas.get('get_page_numbers')().length-1;
    cuentas.set('pagina_actual', p >= l ? l : p+1);
});