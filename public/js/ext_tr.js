var rip = new Ractive({
    el: "#extrip-cont",
    template: "#tpl-extrip",

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

        $.getJSON('?ext=ripley', function(data){
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

        $("#ext-rip").modal("show");
    },

    update: function() {
        var _t = this;
        this.load(function(e){
            _t.set('errorMsg', e ? e.message : false);
        });
    }
});

var map_meses = {
    ene: "01", feb: "02", mar: "03", abr: "04", may: "05", jun: "06", jul: "07",
    ago: "08", sep: "09", oct: "10", nov: "11", dic: "12"
};

rip.on('crearcuenta', function(e, idx){
    var x = rip.get("cuentas")[idx];
    location.hash = "cuentas";

    // Formatear fechas de compra y facturación
    var ft = new Date(x.fecha), fm = ft.getMonth(), fd = ft.getDate();
    if(fm < 10) fm = "0" + fm; if(fd < 10) fd = "0" + fd;
    var fc = ft.getFullYear() + "-" + fm + "-" + fd;
    var hoy = new Date(),
        mfact = (hoy.getDate() > 5 ? hoy.getMonth() + 1 : hoy.getMonth());
    if(mfact < 10) mfact = "0" + mfact;

    $("#ext-rip").modal("hide");
    console.log('fecha compra', fc);
    cuentas.mostrar_agregar({
        nombre: x.comercio,
        monto_original: x.monto,
        cuotas: x.cuotas,
        divisa_original: "CLP",
        monto: x.valor,
        fecha_compra: fc,
        fecha_facturacion: hoy.getFullYear() + "-" + mfact,
        pagado: "0"
    });
});
