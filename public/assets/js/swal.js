// GLOBAL SWEETALERT

// Notifikasi sukses (pesan dari session)
function showSuccess(message) {
    Swal.fire({
        icon: 'success',
        title: 'Berhasil',
        text: message,
        timer: 1800,
        showConfirmButton: false
    });
}

// Notifikasi error
function showError(message) {
    Swal.fire({
        icon: 'error',
        title: 'Gagal',
        text: message,
        timer: 2000,
        showConfirmButton: false
    });
}

// Konfirmasi hapus (global)
function confirmDelete(callback) {
    Swal.fire({
        title: 'Hapus Data?',
        text: 'Data yang dihapus tidak dapat dikembalikan.',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#3085d6',
        confirmButtonText: 'Hapus'
    }).then((result) => {
        if (result.isConfirmed) {
            callback(); // eksekusi submit form
        }
    });
}

// Auto binding untuk semua form delete
document.addEventListener('DOMContentLoaded', () => {
    document.querySelectorAll('form[data-confirm-delete]').forEach(form => {
        form.addEventListener('submit', function (e) {
            e.preventDefault();
            confirmDelete(() => form.submit());
        });
    });
});
