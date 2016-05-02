<?php defined("INCLUDED") or die("nel"); ?>
    <!-- Formulario agregar/editar deudores -->
    <form action="javascript:;" method="POST" class="modal fade" id="agregar-deudor" name="agregar_deudor" role="form">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                    <h4 class="modal-title">Agregar deudor</h4>
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
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-success">Agregar</button>
                </div>
            </div>
        </div>
    </form>