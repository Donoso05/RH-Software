document.addEventListener('DOMContentLoaded', function () {
    const montoInput = document.getElementById('monto'); // Campo de entrada para el monto solicitado
    const cuotasInput = document.getElementById('cuotas'); // Campo de entrada para la cantidad de cuotas
    const valorCuotasSpan = document.getElementById('valorCuotas'); // Elemento donde se mostrará el valor de cada cuota

    /**
     * Función para calcular el valor de las cuotas sin intereses.
     * 
     * @param {number} monto - El monto del préstamo.
     * @param {number} cuotas - La cantidad de cuotas.
     * @return {number} - El valor de cada cuota.
     */
    function calcularValorCuotas(monto, cuotas) {
        return monto / cuotas;
    }

    /**
     * Función para actualizar el valor de las cuotas en la interfaz de usuario.
     */
    function actualizarValorCuotas() {
        const monto = parseFloat(montoInput.value); // Obtener el valor del monto solicitado
        const cuotas = parseInt(cuotasInput.value, 10); // Obtener el valor de la cantidad de cuotas
        if (!isNaN(monto) && !isNaN(cuotas) && cuotas > 0) { // Verificar que los valores sean números válidos
            const valorCuotas = calcularValorCuotas(monto, cuotas); // Calcular el valor de las cuotas
            valorCuotasSpan.textContent = valorCuotas.toFixed(2); // Mostrar el valor de las cuotas con 2 decimales
        } else {
            valorCuotasSpan.textContent = ''; // Limpiar el valor de las cuotas si los valores no son válidos
        }
    }

    // Agregar eventos para actualizar el valor de las cuotas en tiempo real cuando el usuario cambia el monto o las cuotas
    montoInput.addEventListener('input', actualizarValorCuotas);
    cuotasInput.addEventListener('input', actualizarValorCuotas);

    // Validaciones al enviar el formulario
    document.getElementById('creditoForm').addEventListener('submit', function (event) {
        const monto = parseFloat(montoInput.value); // Obtener el valor del monto solicitado
        const cuotas = parseInt(cuotasInput.value, 10); // Obtener el valor de la cantidad de cuotas

        // Validar que el monto mínimo sea de 500,000 pesos colombianos
        if (monto < 500000) {
            alert('El monto mínimo es de 500,000 pesos colombianos.');
            event.preventDefault(); // Evitar el envío del formulario
        }

        // Validar que la cantidad máxima de cuotas sea 36
        if (cuotas > 36) {
            alert('El número máximo de cuotas es de 36.');
            event.preventDefault(); // Evitar el envío del formulario
        }
    });
});
