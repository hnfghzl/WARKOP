document.addEventListener('DOMContentLoaded', function () {
	const sidebar = document.getElementById('adminSidebar');
	const toggle = document.getElementById('sidebarToggle');
	const backdrop = document.getElementById('sidebarBackdrop');

	if (!sidebar || !toggle || !backdrop) {
		return;
	}

	const closeSidebar = function () {
		sidebar.classList.add('-translate-x-full');
		backdrop.classList.add('hidden');
	};

	toggle.addEventListener('click', function () {
		sidebar.classList.toggle('-translate-x-full');
		backdrop.classList.toggle('hidden');
	});

	backdrop.addEventListener('click', closeSidebar);
});
