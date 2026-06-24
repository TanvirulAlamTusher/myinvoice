/*---------sidebar toggle--------*/

    document.addEventListener('DOMContentLoaded', function () {

        const toggleBtn = document.getElementById('sidebarToggle');
        const layout = document.getElementById('layout');
        const backdrop = document.querySelector('.sidebar-backdrop');
        const mobileQuery = window.matchMedia('(max-width: 900px)');

        if(toggleBtn && layout){

            function sidebarIsOpen() {
                return mobileQuery.matches
                    ? layout.classList.contains('sidebar-open')
                    : !layout.classList.contains('sidebar-hide');
            }

            function syncSidebarState() {
                toggleBtn.setAttribute('aria-expanded', String(sidebarIsOpen()));
            }

            function closeSidebar() {
                if (mobileQuery.matches) {
                    layout.classList.remove('sidebar-open');
                } else {
                    layout.classList.add('sidebar-hide');
                }

                syncSidebarState();
            }

            toggleBtn.addEventListener('click', () => {

                if (mobileQuery.matches) {
                    layout.classList.toggle('sidebar-open');
                } else {
                    layout.classList.toggle('sidebar-hide');
                }

                syncSidebarState();

            });

            backdrop?.addEventListener('click', closeSidebar);

            document.addEventListener('keydown', (event) => {
                if (event.key === 'Escape' && mobileQuery.matches) {
                    closeSidebar();
                }
            });

            mobileQuery.addEventListener('change', () => {
                layout.classList.remove('sidebar-open', 'sidebar-hide');
                syncSidebarState();
            });

            syncSidebarState();

        }

    });






document.addEventListener('DOMContentLoaded', function () {

    document.querySelectorAll('.sidebar-menu-toggle').forEach(toggle => {

        toggle.addEventListener('click', function () {

            const parent = this.closest('.menu-group');

            parent.classList.toggle('open');
            this.setAttribute('aria-expanded', parent.classList.contains('open') ? 'true' : 'false');

        });

    });

});
/*================= delete modal js ================= */

    function openDeleteModal(url, name = 'this item') {

        const modal = document.getElementById('deleteModal');
        const form = document.getElementById('deleteForm');
        const itemName = document.getElementById('deleteItemName');

        form.action = url;
        itemName.textContent = name;

        modal.classList.remove('hidden');
    }

    function closeDeleteModal() {
        document.getElementById('deleteModal').classList.add('hidden');
    }

    // close on outside click
    document.addEventListener('click', function (e) {
        const modal = document.getElementById('deleteModal');
        if (e.target === modal) {
            closeDeleteModal();
        }
    });


/*---------toast notification--------*/

function toast(message, type = 'success', timeout = 3000) {

    const container = document.getElementById('toast-container');

    const el = document.createElement('div');
    el.className = `toast toast-${type}`;

    el.innerHTML = `
        <span>${message}</span>
        <span class="toast-close">&times;</span>
    `;

    // 👇 ADD HERE (before append OR after append both works)
    el.style.opacity = 0;

    container.appendChild(el);

    // 👇 fade in effect
    setTimeout(() => {
        el.style.opacity = 1;
    }, 10);

    // close button
    el.querySelector('.toast-close').onclick = () => el.remove();

    // auto remove
    setTimeout(() => {
        el.remove();
    }, timeout);
}

