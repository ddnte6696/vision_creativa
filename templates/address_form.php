<?php
// templates/address_form.php
// Este es un componente reutilizable para el formulario de dirección.
?>
<form id="address-form">
    <h3>Añadir/Editar Dirección</h3>
    <input type="hidden" name="address_id" id="address_id">
    <div class="form-group">
        <label for="calle">Calle y Número</label>
        <input type="text" id="calle" name="calle" required>
    </div>
    <div class="form-group">
        <label for="colonia">Colonia</label>
        <input type="text" id="colonia" name="colonia" required>
    </div>
    <div class="form-group-grid">
        <div class="form-group">
            <label for="ciudad">Ciudad</label>
            <input type="text" id="ciudad" name="ciudad" required>
        </div>
        <div class="form-group">
            <label for="estado">Estado</label>
            <input type="text" id="estado" name="estado" required>
        </div>
        <div class="form-group">
            <label for="cp">Código Postal</label>
            <input type="text" id="cp" name="cp" required>
        </div>
    </div>
    <div class="form-group">
        <label for="referencias">Referencias Adicionales</label>
        <textarea id="referencias" name="referencias" rows="3"></textarea>
    </div>
    <div class="form-actions">
        <button type="submit" class="cta-button">Guardar Dirección</button>
        <button type="button" id="cancel-address-btn" class="cta-button-secondary">Cancelar</button>
    </div>
</form>