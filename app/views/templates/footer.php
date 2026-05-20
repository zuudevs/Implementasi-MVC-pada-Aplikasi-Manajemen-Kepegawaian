    </main><!-- /.page-content -->

    <footer style="padding:14px 28px; border-top:1px solid var(--border); background:var(--card-bg); margin-top:auto;">
        <div class="d-flex justify-content-between align-items-center flex-wrap gap-2" style="font-size:12px; color:var(--text-muted);">
            <span>&copy; <?= date('Y') ?> <?= APP_NAME ?> &mdash; Sistem Informasi Manajemen Kepegawaian</span>
            <span>Kelompok 14 &middot; Teknologi Informasi</span>
        </div>
    </footer>

</div><!-- /#main -->

<!-- Bootstrap 5 JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

<script>
    // Mobile sidebar toggle
    const sidebar = document.getElementById('sidebar');
    document.getElementById('sidebarToggle')?.addEventListener('click', () => {
        sidebar.classList.toggle('open');
    });

    // Close sidebar on outside click (mobile)
    document.addEventListener('click', (e) => {
        if (window.innerWidth < 768 && sidebar.classList.contains('open')) {
            if (!sidebar.contains(e.target) && !e.target.closest('#sidebarToggle')) {
                sidebar.classList.remove('open');
            }
        }
    });

    // Auto-dismiss flash alerts after 5 s
    document.querySelectorAll('.alert').forEach(el => {
        setTimeout(() => {
            const bsAlert = bootstrap.Alert.getOrCreateInstance(el);
            bsAlert.close();
        }, 5000);
    });

    // Rupiah input formatting
    document.querySelectorAll('[data-rupiah]').forEach(input => {
        const format = v => v.replace(/\D/g, '').replace(/\B(?=(\d{3})+(?!\d))/g, '.');
        input.addEventListener('input', () => {
            const raw = input.value.replace(/\./g, '');
            input.value = format(raw);
        });
    });
</script>

</body>
</html>
