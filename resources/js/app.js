// MBG Nutrisi - Main JS

document.addEventListener('DOMContentLoaded', function () {
    // Sidebar toggle
    const toggle = document.getElementById('sidebarToggle');
    const sidebar = document.getElementById('sidebar');

    if (toggle && sidebar) {
        toggle.addEventListener('click', () => {
            sidebar.classList.toggle('open');
        });

        document.addEventListener('click', (e) => {
            if (!sidebar.contains(e.target) && !toggle.contains(e.target)) {
                sidebar.classList.remove('open');
            }
        });
    }
});

// Cluster colors
const CLUSTER_COLORS = ['#2563EB', '#F59E0B', '#10B981', '#8B5CF6'];
const CLUSTER_LABELS = ['Tinggi Protein', 'Tinggi Karbohidrat', 'Seimbang & Bergizi', 'Tinggi Serat & Vitamin'];

function getClusterBadgeHtml(cluster, label) {
    const color = CLUSTER_COLORS[cluster] || '#6B7280';
    return `<span class="cluster-badge" style="background:${color}18; color:${color};">
        <span class="cluster-dot" style="background:${color};"></span>${label}
    </span>`;
}