window.loadPUC = (dataExpense) => {
  const selectPuc = document.querySelector('.idPuc');
  selectPuc.innerHTML = `<option disabled selected>Seleccionar</option>`;

  // Verificar si los datos ya están en sessionStorage
  let pucData = sessionStorage.getItem('pucData');

  const renderSelect = (data) => {
    const grupos = {};

    data.forEach(item => {
      const num = item.number_count.toString();

      // Ignorar cuentas de un solo dígito (ej. 4, 5)
      if (num.length === 1) return;

      if (num.length === 2) {
        // Guardar el nombre del grupo
        grupos[num] = {
          label: `${item.number_count} - ${item.count}`,
          options: []
        };
      } else if (num.length > 2) {
        const grupoKey = num.substring(0, 2);
        if (!grupos[grupoKey]) {
          grupos[grupoKey] = {
            label: `Grupo ${grupoKey}`,
            options: []
          };
        }

        // Filtrar cuentas ya registradas en dataExpense
        const yaRegistrado = dataExpense.some(e => e.id_puc == item.id_puc);
        if (!yaRegistrado) {
          grupos[grupoKey].options.push({
            id: item.id_puc,
            text: `${item.number_count} - ${item.count}`
          });
        }
      }
    });

    for (const key in grupos) {
      const grupo = grupos[key];
      if (grupo.options.length > 0) {
        const optgroup = document.createElement('optgroup');
        optgroup.label = grupo.label;

        grupo.options.forEach(opt => {
          const option = document.createElement('option');
          option.value = opt.id;
          option.textContent = opt.text;
          optgroup.appendChild(option);
        });

        selectPuc.appendChild(optgroup);
      }
    }
  };

  // Si ya hay datos en sessionStorage, úsalos
  if (pucData) {
    renderSelect(JSON.parse(pucData));
  } else {
    // Si no, haz el fetch y guarda en sessionStorage
    fetch('/api/puc')
      .then(response => response.json())
      .then(data => {
        sessionStorage.setItem('pucData', JSON.stringify(data));
        renderSelect(data);
      })
      .catch(error => {
        console.error('Error cargando datos PUC:', error);
      });
  }
}
