</main>

    <!-- FOOTER -->
    <footer class="sb-footer">
        <div class="container d-flex flex-column flex-md-row justify-content-between align-items-center gap-2">
            <div>
                <strong>Starbucks POS System</strong> &copy; <?php echo date('Y'); ?> &bull; Sistem Kasir & Manajemen Penjualan
            </div>
            <div class="text-muted" style="font-size:0.75rem;">
                Zona Waktu Operasional: WITA
            </div>
        </div>
        <div class="text-muted" style="font-size:0.75rem;">
            Dibuat untuk Manajemen Kasir &bull; Versi 2.0
        </div>
    </div>
</footer>

    <script src="/starbuck/assets/js/bootsrap.js"></script>
    <script>
        // Jam real-time navbar selalu menggunakan zona waktu Makassar (WITA).
        function updateLiveClock() {
            var clockEl = document.getElementById('sbClockText');
            if (clockEl) {
                var time = new Intl.DateTimeFormat('id-ID', {
                    timeZone: 'Asia/Makassar',
                    hour: '2-digit',
                    minute: '2-digit',
                    second: '2-digit',
                    hourCycle: 'h23'
                }).format(new Date());
                clockEl.textContent = time + ' WITA';
            }
        }
        updateLiveClock();
        window.setInterval(updateLiveClock, 1000);

    // Fitur Live Search Tabel Universal
    function setupTableSearch(inputId, tableId) {
        var input = document.getElementById(inputId);
        var table = document.getElementById(tableId);
        if (!input || !table) return;

        input.addEventListener('keyup', function () {
            var filter = input.value.toLowerCase();
            var rows = table.getElementsByTagName('tbody')[0].getElementsByTagName('tr');
            var visibleCount = 0;

            for (var i = 0; i < rows.length; i++) {
                var text = rows[i].textContent.toLowerCase();
                if (text.indexOf(filter) > -1) {
                    rows[i].style.display = '';
                    visibleCount++;
                } else {
                    rows[i].style.display = 'none';
                }
            }

            // Tampilkan info bila tidak ditemukan
            var emptyRow = document.getElementById('searchEmptyRow');
            if (emptyRow) {
                emptyRow.style.display = (visibleCount === 0) ? '' : 'none';
            }
        });
    }
</script>
</body>

</html>
