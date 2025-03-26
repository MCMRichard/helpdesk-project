import './bootstrap';

document.addEventListener('DOMContentLoaded', function () {
    const roleSelect = document.getElementById('role');
    const specialtiesDiv = document.getElementById('specialties');

    function toggleSpecialties() {
        specialtiesDiv.style.display = roleSelect.value === 'specialist' ? 'block' : 'none';
    }

    roleSelect.addEventListener('change', toggleSpecialties);
    toggleSpecialties();
});