window.setSelectedPUCOption = (data) => {
    const selectPuc = document.querySelector('.idPuc');

    // Limpiar el select
    selectPuc.innerHTML = '';

    // Crear la opción específica y marcarla como seleccionada
    const selectedOption = document.createElement('option');
    selectedOption.value = data.id_puc;
    selectedOption.textContent = `${data.number_count} - ${data.count}`;
    selectedOption.selected = true;

    selectPuc.appendChild(selectedOption);
}
