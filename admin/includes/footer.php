<?php
/**
 * Lurnixe Health Card System - Secure Admin Footer Template
 * June 2026
 */
?>
            <!-- Admin Panel Footer -->
            <footer class="mt-auto py-3 text-center border-top border-secondary bg-transparent">
                <span class="text-white small">&copy; <?php echo date('Y'); ?> Lurnixe Health Admin Panel. All Rights Reserved.</span>
            </footer>
        </div> <!-- End of container-fluid -->
    </div> <!-- End of page-content-wrapper -->
</div> <!-- End of wrapper -->

<!-- Global AJAX Loader Modal (Floating spinner) -->
<div class="modal fade" id="ajaxLoaderModal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-hidden="true" style="z-index: 9999;">
    <div class="modal-dialog modal-dialog-centered modal-sm" style="max-width: 150px;">
        <div class="modal-content border-0 bg-transparent shadow-none text-center">
            <div class="spinner-border text-success" role="status" style="width: 3.5rem; height: 3.5rem;">
                <span class="visually-hidden">Loading...</span>
            </div>
            <p class="text-white mt-3 fw-bold small">Please wait...</p>
        </div>
    </div>
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

<!-- Custom Admin JS (Absolute path as requested) -->
<script src="<?php echo BASE_URL; ?>assets/js/admin.js?v=1.2"></script>

<script>
    // Sidebar toggle script hook for responsive sidebar
    $("#menu-toggle").click(function(e) {
        e.preventDefault();
        $(".sidebar").toggleClass("active");
    });
</script>
</body>
</html>
