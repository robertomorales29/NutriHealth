document.addEventListener('DOMContentLoaded', () => {
    const roleSelect = document.querySelector('[data-role-select]');
    const specialistSelect = document.querySelector('[data-specialist-select]');
    const typeInput = document.querySelector('[data-type-input]');
    const dateInput = document.querySelector('[data-date-input]');
    const slotsBox = document.querySelector('[data-slots-box]');
    const patientSelect = document.querySelector('[data-patient-select]');

    function filterSpecialists() {
        if (!roleSelect || !specialistSelect) return;
        const role = roleSelect.value;
        [...specialistSelect.options].forEach(option => {
            if (!option.value) {
                option.hidden = false;
                return;
            }
            option.hidden = option.dataset.role !== role;
        });
        specialistSelect.value = '';
        if (typeInput) typeInput.value = role;
        clearSlots();
    }

    function clearSlots(message = 'Selecciona especialista y fecha para ver horarios disponibles.') {
        if (!slotsBox) return;
        slotsBox.innerHTML = `<div class="text-muted small">${message}</div>`;
    }

    async function loadSlots() {
        if (!specialistSelect || !dateInput || !slotsBox) return;
        const especialistaId = specialistSelect.value;
        const fecha = dateInput.value;
        const pacienteId = patientSelect ? patientSelect.value : '';
        if (!especialistaId || !fecha) {
            clearSlots();
            return;
        }

        slotsBox.innerHTML = '<div class="text-muted small">Cargando horarios...</div>';
        const params = new URLSearchParams({ especialista_id: especialistaId, fecha });
        if (pacienteId) params.append('paciente_id', pacienteId);

        try {
            const response = await fetch(`${window.NH_BASE_URL || './'}api/disponibilidad.php?${params.toString()}`);
            const data = await response.json();
            if (!data.ok) {
                clearSlots(data.message || 'No se pudieron cargar horarios.');
                return;
            }
            if (!data.slots.length) {
                clearSlots('No hay horarios disponibles para esa fecha.');
                return;
            }
            slotsBox.innerHTML = data.slots.map(slot => {
                const label = slot.substring(0, 5);
                return `<label class="slot-btn me-2 mb-2"><input type="radio" name="hora" value="${slot}" required><span>${label}</span></label>`;
            }).join('');
        } catch (error) {
            clearSlots('Error de conexión al consultar disponibilidad.');
        }
    }

    if (roleSelect) roleSelect.addEventListener('change', filterSpecialists);
    if (specialistSelect) specialistSelect.addEventListener('change', loadSlots);
    if (dateInput) dateInput.addEventListener('change', loadSlots);
    if (patientSelect) patientSelect.addEventListener('change', loadSlots);
    filterSpecialists();

    const password = document.querySelector('#password');
    const confirm = document.querySelector('#password_confirm');
    if (password && confirm) {
        confirm.addEventListener('input', () => {
            confirm.setCustomValidity(password.value === confirm.value ? '' : 'Las contraseñas no coinciden');
        });
    }
});
