document.addEventListener('DOMContentLoaded', function() {
    const montoInput = document.getElementById('monto');
    const cuotasInput = document.getElementById('cuotas');
    const valorCuotasSpan = document.getElementById('valorCuotas');

    montoInput.addEventListener('input', calcularValorCuotas);
    cuotasInput.addEventListener('input', calcularValorCuotas);

    function calcularValorCuotas() {
        const monto = parseFloat(montoInput.value);
        const cuotas = parseInt(cuotasInput.value);

        if (isNaN(monto) || isNaN(cuotas) || monto <= 0 || cuotas <= 0) {
            valorCuotasSpan.textContent = 'Ingrese un monto y cantidad de cuotas válidos';
            return;
        }

        const valorCuota = monto / cuotas;
        valorCuotasSpan.textContent = `$${valorCuota.toFixed(2)} por cuota`;
    }
});
