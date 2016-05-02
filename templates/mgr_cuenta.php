<?php defined("INCLUDED") or die("nel"); ?>
    <!-- Formulario agregar/editar cuentas -->
    <form action="javascript:;" method="POST" class="modal fade" id="agregar-cuenta" name="agregar_cuenta" role="form">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                    <h4 class="modal-title">Agregar cuenta</h4>
                </div>
                <div class="modal-body">
                    <div class="form-horizontal">
                        <div class="form-group">
                            <label for="nombre" class="col-sm-3 control-label">Nombre</label>
                            <div class="col-sm-9">
                                <input type="text" name="nombre" id="nombre" class="form-control" value="" required="required">
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="descripcion" class="col-sm-3 control-label">Descripción</label>
                            <div class="col-sm-9">
                                <input type="text" name="descripcion" id="descripcion" class="form-control" placeholder="Sin descripción" value="">
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="fecha-compra" class="col-sm-3 control-label">Fecha compra</label>
                            <div class="col-sm-9">
                                <input type="date" name="fecha_compra" id="fecha-compra" class="form-control" value="" required="required">
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="fecha-facturacion" class="col-sm-3 control-label">Facturación</label>
                            <div class="col-sm-9">
                                <input type="month" name="fecha_facturacion" id="fecha-facturacion" class="form-control" value="" required="required">
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="monto-original" class="col-sm-3 control-label">Monto original</label>
                            <div class="col-sm-7">
                                <input type="number" name="monto_original" id="monto-original" step="any" min="0" class="form-control" placeholder="Monto (ej: 2099, 3.99)">
                            </div>
                            <div class="col-sm-2">
                                <input type="text" class="form-control" name="divisa_original" list="divisas" placeholder="CLP" maxlength="3" minlength="3" pattern="[A-Z]{3}" oninput="this.value = this.value.toUpperCase();" value="CLP">
                                <datalist id="divisas">
                                    <option value="CLP">
                                    <option value="USD">
                                    <option value="EUR">
                                    <option value="GBP">
                                </datalist>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="monto" class="col-sm-3 control-label">Monto a pagar</label>
                            <div class="col-sm-9">
                                <input type="number" step="any" name="monto" id="monto" class="form-control" value="" required="required">
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="cuotas" class="col-sm-3 control-label">Cuotas</label>
                            <div class="col-sm-9">
                                <input type="number" name="cuotas" id="cuotas" class="form-control" value="0" required="required" min="-1">
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="pagado" class="col-sm-3 control-label">Pagado</label>
                            <div class="col-sm-9">
                                <div class="checkbox">
                                    <label>
                                        <input type="checkbox" name="pagado" id="pagado" value="1">
                                    </label>
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="informacion" class="col-sm-3 control-label">Info. adicional</label>
                            <div class="col-sm-9">
                                <textarea name="info" id="informacion" class="form-control" rows="3" placeholder="Sin información adicional"></textarea>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-success">Agregar</button>
                </div>
            </div>
        </div>
    </form>