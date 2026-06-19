<?php
/**
 * Lurnixe Health Card System - Admin Footer Template
 * June 2026
 */
?>
        </div> <!-- End of container-fluid -->
    </div> <!-- End of page-content-wrapper -->
</div> <!-- End of wrapper -->

<!-- Global AJAX Loader Overlay (Floating spinner) -->
<div id="ajaxLoaderOverlay" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0, 0, 0, 0.5); z-index: 99999; justify-content: center; align-items: center; flex-direction: column;">
    <div class="spinner-border text-success" role="status" style="width: 3.5rem; height: 3.5rem;">
        <span class="visually-hidden">Loading...</span>
    </div>
    <p class="text-white mt-3 fw-bold small">Please wait...</p>
</div>

<!-- JQuery & Bootstrap JS -->
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

<!-- Chart.js (for analytics rendering) -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<!-- DataTables core + styling integration -->
<script src="https://cdn.datatables.net/1.13.7/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.7/js/dataTables.bootstrap5.min.js"></script>

<!-- SweetAlert2 (for elegant alerts & prompts) -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<!-- Custom Admin JS -->
<script src="<?php echo BASE_URL; ?>assets/js/admin.js?v=1.0"></script>

<script>
    // Sidebar toggle script hook
    $("#menu-toggle").click(function(e) {
        e.preventDefault();
        $("#wrapper").toggleClass("toggled");
    });
</script>
</body>
</html>
