</div>
    </div>
    
    <script>
        // Auto hide alerts after 5 seconds
        setTimeout(function() {
            const alerts = document.querySelectorAll('.alert');
            alerts.forEach(function(alert) {
                alert.style.transition = 'opacity 0.5s';
                alert.style.opacity = '0';
                setTimeout(function() {
                    alert.remove();
                }, 500);
            });
        }, 5000);
        
        // Confirm before delete
        function confirmDelete(message) {
            return confirm(message || 'Apakah Anda yakin ingin menghapus data ini?');
        }
        
        // Format number to rupiah
        function formatRupiah(angka, prefix = 'Rp ') {
            const number_string = angka.toString().replace(/[^,\d]/g, '');
            const split = number_string.split(',');
            const sisa = split[0].length % 3;
            let rupiah = split[0].substr(0, sisa);
            const ribuan = split[0].substr(sisa).match(/\d{3}/gi);
            
            if (ribuan) {
                const separator = sisa ? '.' : '';
                rupiah += separator + ribuan.join('.');
            }
            
            rupiah = split[1] != undefined ? rupiah + ',' + split[1] : rupiah;
            return prefix + rupiah;
        }
    </script>
</body>
</html>