String.prototype.reverse = function() {
    if(this.toString() === "") return this;

    return this.split('').reverse().join('');
};

Promise.prototype.finally = function(onResolveOrReject) {
    return this.catch(function(reason){
        return reason;
    }).then(onResolveOrReject);
};

function checkData(res, mustHaveData) {
    if(typeof res.success == "undefined") {
        console.log("Datos desconocidos recibidos", res);
        throw new Error("Datos incorrectos recibidos");
    }

    if(!res.success) {
        if(typeof res.message == "undefined") {
            throw new Error("Error desconocido");
        } else {
            throw new Error(res.message);
        }
    }

    if(mustHaveData && typeof res.data == "undefined") {
        throw new Error("No se recibieron los datos esperados");
    }

    return res;
}

function strPadding(str, pad, num) {
    if(str.length >= num) {
        return str;
    }

    var p = new Array(num - str.length);
    p = p.fill(pad);
    return p.join("") + str;
}

function zeroFill(num, amount) {
    return strPadding(num+"", "0", amount);
}

function dateToForm(date, withDay) {
    if(typeof withDay == "undefined") withDay = true;

    var d = date.getFullYear() + "-" + zeroFill(date.getMonth()+1, 2);
    if(withDay) {
         d += "-" + zeroFill(date.getDate(), 2);
    }

    return d;
}