document.addEventListener('DOMContentLoaded', function () {
    const montoInput = document.getElementById('monto');
    const cuotasInput = document.getElementById('cuotas');
    const valorCuotasSpan = document.getElementById('valorCuotas');

    function calcularValorCuotas(monto, cuotas, interesAnual = 12) {
        const interesMensual = interesAnual / 12 / 100;
        if (interesMensual === 0) {
            return monto / cuotas;
        }
        const valorCuota = monto * (interesMensual * Math.pow(1 + interesMensual, cuotas)) / (Math.pow(1 + interesMensual, cuotas) - 1);
        return valorCuota;
    }

    function actualizarValorCuotas() {
        const monto = parseFloat(montoInput.value);
        const cuotas = parseInt(cuotasInput.value, 10);
        if (!isNaN(monto) && !isNaN(cuotas) && cuotas > 0) {
            const valorCuotas = calcularValorCuotas(monto, cuotas);
            valorCuotasSpan.textContent = valorCuotas.toFixed(2);
        } else {
            valorCuotasSpan.textContent = '';
        }
    }

    montoInput.addEventListener('input', actualizarValorCuotas);
    cuotasInput.addEventListener('input', actualizarValorCuotas);

    document.getElementById('creditoForm').addEventListener('submit', function (event) {
        const monto = parseFloat(montoInput.value);
        const cuotas = parseInt(cuotasInput.value, 10);

        if (monto < 500000) {
            alert('El monto mínimo es de 500,000 pesos colombianos.');
            event.preventDefault();
        }

        if (cuotas > 36) {
            alert('El número máximo de cuotas es de 36.');
            event.preventDefault();
        }
    });
});
