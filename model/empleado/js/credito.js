document.getElementById('creditoForm').addEventListener('submit', function(event) {
    const monto = parseFloat(document.getElementById('monto').value);
    const cuotas = parseInt(document.getElementById('cuotas').value, 10);
    const errors = [];

    if (monto < 500000) {
        errors.push('El monto mínimo es de 500,000 pesos colombianos.');
    }

    if (cuotas > 36) {
        errors.push('El número máximo de cuotas es de 36.');
    }

    if (errors.length > 0) {
        alert(errors.join('\n'));
        event.preventDefault(); // Evita el envío del formulario
    }
});
