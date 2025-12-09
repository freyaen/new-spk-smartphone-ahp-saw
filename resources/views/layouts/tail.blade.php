    <!-- Main scripts -->
    <script src="{{ asset('vendors/bundle.js') }}"></script>

    <!-- DataTable -->
    <script src="{{ asset('vendors/dataTable/datatables.min.js') }}"></script>

    <!-- App scripts -->
    <script src="{{ asset('assets/js/app.min.js') }}"></script>

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="{{ asset('assets/js/swal.js') }}"></script>

    <script>
        @if(session('success'))
        showSuccess("{{ session('success') }}");
        @endif

        @if(session('error'))
        showError("{{ session('error') }}");
        @endif

    </script>

    </body>

    </html>
