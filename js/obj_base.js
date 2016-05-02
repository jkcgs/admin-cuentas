Ractive.DEBUG = false;
var ObjBase = Ractive.extend({
    data: function(){
        return {
            loading: false,
            working: false,
            formatearDinero: formatearDinero
        };
    },

    mostrar_agregar: function(objfill) {
        if(this.form) this.form.reset();
        objfill = objfill || {};

        var m = $(this.modal);
        $(".modal-title", m).text(this.strings.t_agregar);
        $(".btn-success", m).text(this.strings.t_agregar_btn);
        m.modal('show');

        var _t = this;
        this.form.onsubmit = function(){ _t.agregar(); };

        for(var e in objfill) {
            if(!objfill.hasOwnProperty(e)) continue;
            if(!e in this.form) continue;
            
            if(this.form[e].type == "checkbox") {
                this.form[e].checked = objfill[e] == 1;
            } else {
                this.form[e].value = objfill[e];
            }
        }
    },

    mostrar_editar: function(id) {
        var e = this.dget(id);
        if(e == null) {
            alert("El elemento seleccionado no existe");
        }
        
        var m = $(this.modal);
        $(".modal-title", m).text(this.strings.t_editar + id);
        $(".btn-success", m).text("Guardar");

        var _t = this, _i = id;
        this.form.onsubmit = function(){ _t.editar(_i); };

        for(var i in this.form_elements) {
            if(!this.form_elements.hasOwnProperty(i)) continue;
            var x = this.form_elements[i];

            if(this.form[x].type == "checkbox") {
                this.form[x].checked = e[x] == 1;
            } else {
                var defval = ('form_defaults' in this && x in this.form_defaults) ? this.form_defaults[x] : '';
                this.form[x].value = e[x] || defval;
            }
        }
        
        m.modal('show');
    },

    agregar: function(){
        if(!this.form.checkValidity() || this.get('working')) return;
        var _t = this;
        
        $("[type=submit]", this.form).addClass("btn-loading").prop("disabled", true);
        this.set('working', true);

        $.post("acciones/?"+this.acciones.agregar, $(this.form).serialize(), function(data){
            if(typeof data !== "object") {
                try{
                    JSON.parse(data);
                } catch(e) {
                    alert("Error JSON: " + e.message + "\n" + data);
                    return;
                }
            }
            if(data.error) {
                alert("Error: " + data.error);
            } else {
                _t.splice(_t.obj_name, 0, 0, data);
                $(_t.modal).modal('hide');
            }
            
        })
        .error(function(req, status, err){
            alert("Error: " + err.message + "\n" + req.responseText);
        })
        .always(function(){
            _t.set('working', false);
            $("[type=submit]", _t.form).removeClass("btn-loading").prop("disabled", false);
        });
    },

    editar: function(id){
        if(!this.form.checkValidity() || this.get('working')) return;
        var _t = this;
        
        $("[type=submit]", this.form).addClass("btn-loading").prop("disabled", true);
        this.set('working', true);

        $.post("acciones/?"+this.acciones.editar+"&id="+id, $(this.form).serialize(), function(data){
            if(typeof data !== "object") {
                try{
                    JSON.parse(data);
                } catch(e) {
                    alert("Error JSON: " + e.message + "\n" + data);
                    return;
                }
            }

            if(data.error) {
                alert("Error: " + data.error);
                if(data.hasOwnProperty('ne') && data.ne) {
                    _t.ddel(id);
                    $(_t.modal).modal('hide');
                }
            } else {
                _t.dset(id, data);
                $(_t.modal).modal('hide');
            }
            
        })
        .error(function(req, status, err){
            alert("Error: " + err.message + "\n" + req.responseText);
        })
        .always(function(){
            _t.set('working', false);
            $("[type=submit]", _t.modal).removeClass("btn-loading").prop("disabled", false);
        });
    },

    ddel: function(id) {
        var d = this.get(this.obj_name);
        for(var i = 0; i < d.length; i++) {
            var e = d[i];
            if(e.hasOwnProperty("id") && e.id == id) {
                this.splice(this.obj_name, i, 1);
                break;
            }
        }
    },

    dget: function(id) {
        var d = this.get(this.obj_name);
        for(var i = 0; i < d.length; i++) {
            var e = d[i];
            if(e.hasOwnProperty("id") && e.id == id) {
                return e;
            }
        }
        
        return null;
    },
    
    dset: function(id, data) {
        var d = this.get(this.obj_name);
        for(var i = 0; i < d.length; i++) {
            var e = d[i];
            if(e.hasOwnProperty("id") && e.id == id) {
                data.id = e.id; // no cambiar id
                this.set(this.obj_name+'.'+i, data);
                break;
            }
        }
    },

    jget: function(url, callback) {
        if(this.working) return;
        var _t = this;

        _t.set('working', true);
        $.get(url, function(data){
            if(data.error) {
                callback(new Error(data.error), data);
            } else {
                if(!data.hasOwnProperty('message')) {
                    var e = new Error("No se recibió OK");
                    callback(e, data);
                    console.log(e, data);
                } else {
                    callback(null, data);
                }
            }
        })
        .error(function(req, status, err){
            callback(new Error(""))
            alert(err.message, req.responseText);
        })
        .always(function(){
            _t.set('working', false);
        });
    },

    onrender: function(){
        var _t = this;

        this.on('borrar', function(e, id){
            if(this.working) return;
            if(!confirm('¿Realmente quieres eliminar este elemento?')) return;
            
            _t.set('working', true);
            $.get("acciones/?"+_t.acciones.borrar+"&id="+id, function(data){
                if(data.error) {
                    alert("Error: " + data.error);
                    if(data.hasOwnProperty('ne') && data.ne) {
                        _t.ddel(id);
                    }
                } else {
                    if(!data.hasOwnProperty('message')) {
                        alert("Error: No se recibió OK\n"+data);
                    } else {
                        _t.ddel(id);
                    }
                }
            })
            .error(function(req, status, err){
                alert("Error: " + err.message + "\n" + req.responseText);
            })
            .always(function(){
                _t.set('working', false);
            });
        });

        this.on('editar', function(e, id){ _t.mostrar_editar(id); });

        this.set('loading', true);
        $.get('acciones/?'+this.acciones.get, function(data){
            if(typeof data !== "object") {
                try{
                    JSON.parse(data);
                } catch(e) {
                    alert("Error JSON: " + e.message + "\n" + data);
                    return;
                }
            }

            if(data.error) {
                alert("Error: " + data.error);
            } else {
                _t.set(_t.obj_name, data);
            }
            
            _t.set('loading', false);
        });
    }
});
