document.addEventListener('DOMContentLoaded', () => {
    // Toast: tutup manual atau otomatis setelah beberapa detik.
    document.querySelectorAll('[data-toast]').forEach((toast) => {
        const dismiss = () => {
            toast.style.transition = 'opacity 0.2s ease';
            toast.style.opacity = '0';
            setTimeout(() => toast.remove(), 200);
        };

        toast.querySelector('[data-toast-close]')?.addEventListener('click', dismiss);
        setTimeout(dismiss, 6000);
    });

    // Escape menutup modal, kecuali sedang memproses.
    document.addEventListener('keydown', (event) => {
        if (event.key !== 'Escape') {
            return;
        }

        document.querySelectorAll('.modal-root:not(.hidden)').forEach((modal) => {
            if (modal.dataset.busy === 'true') {
                return;
            }
            modal.closeModal?.();
        });
    });

    // Jaga fokus keyboard tetap di dalam modal yang terbuka.
    document.addEventListener('keydown', (event) => {
        if (event.key !== 'Tab') {
            return;
        }

        const modal = document.querySelector('.modal-root:not(.hidden)');
        if (!modal) {
            return;
        }

        const focusable = [...modal.querySelectorAll(
            'a[href], button:not([disabled]), input:not([disabled]), select:not([disabled]), textarea:not([disabled]), [tabindex]:not([tabindex="-1"])',
        )].filter((element) => element.offsetParent !== null);

        if (focusable.length === 0) {
            return;
        }

        const first = focusable[0];
        const last = focusable[focusable.length - 1];

        if (event.shiftKey && document.activeElement === first) {
            event.preventDefault();
            last.focus();
        } else if (!event.shiftKey && document.activeElement === last) {
            event.preventDefault();
            first.focus();
        }
    });
});
