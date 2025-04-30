import './bootstrap';
import Chart from 'chart.js/auto';

// Ensure DOM is loaded
document.addEventListener('DOMContentLoaded', function () {
    // Specialist Workload Chart (Bar)
    const workloadCtx = document.getElementById('workloadChart')?.getContext('2d');
    if (workloadCtx && window.dashboardData.specialistWorkloads) {
        new Chart(workloadCtx, {
            type: 'bar',
            data: {
                labels: window.dashboardData.specialistWorkloads.map(item => item.name),
                datasets: [{
                    label: 'Active Assignments',
                    data: window.dashboardData.specialistWorkloads.map(item => item.workload),
                    backgroundColor: 'rgba(54, 162, 235, 0.6)',
                    borderColor: 'rgba(54, 162, 235, 1)',
                    borderWidth: 1,
                }],
            },
            options: {
                responsive: true,
                scales: {
                    y: {
                        beginAtZero: true,
                        title: {
                            display: true,
                            text: 'Number of Assignments',
                        },
                        ticks: {
                            stepSize: 1,
                        },
                    },
                    x: {
                        title: {
                            display: true,
                            text: 'Specialists',
                        },
                    },
                },
                plugins: {
                    legend: {
                        display: false,
                    },
                },
            },
        });
    }

    // Problem Status Distribution Chart (Pie)
    const statusCtx = document.getElementById('statusChart')?.getContext('2d');
    if (statusCtx && window.dashboardData.statusDistribution) {
        new Chart(statusCtx, {
            type: 'pie',
            data: {
                labels: ['Open', 'Assigned', 'Resolved', 'Unsolvable'],
                datasets: [{
                    data: [
                        window.dashboardData.statusDistribution.open,
                        window.dashboardData.statusDistribution.assigned,
                        window.dashboardData.statusDistribution.resolved,
                        window.dashboardData.statusDistribution.unsolvable,
                    ],
                    backgroundColor: [
                        'rgba(255, 99, 132, 0.6)',  // Open
                        'rgba(255, 206, 86, 0.6)',  // Assigned
                        'rgba(75, 192, 192, 0.6)', // Resolved
                        'rgba(153, 102, 255, 0.6)', // Unsolvable
                    ],
                    borderColor: [
                        'rgba(255, 99, 132, 1)',
                        'rgba(255, 206, 86, 1)',
                        'rgba(75, 192, 192, 1)',
                        'rgba(153, 102, 255, 1)',
                    ],
                    borderWidth: 1,
                }],
            },
            options: {
                responsive: true,
                plugins: {
                    legend: {
                        position: 'top',
                    },
                },
            },
        });
    }

    // Existing role/specialties toggle logic
    const roleSelect = document.getElementById('role');
    const specialtiesDiv = document.getElementById('specialties');

    if (roleSelect && specialtiesDiv) {
        function toggleSpecialties() {
            specialtiesDiv.style.display = roleSelect.value === 'specialist' ? 'block' : 'none';
        }

        roleSelect.addEventListener('change', toggleSpecialties);
        toggleSpecialties();
    }
});