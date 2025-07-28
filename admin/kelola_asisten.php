<?php
// admin/kelola_asisten.php
session_start();
if (!isset($_SESSION['admin_loggedin'])) {
    header('Location: ../login_admin.php');
    exit;
}
include '../api/koneksi.php';
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Kelola Data Asisten</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body class="bg-gray-100">

<div class="container mx-auto p-8">
    <a href="dashboard.php" class="text-blue-500 hover:text-blue-700">&larr; Kembali ke Dashboard</a>
    <h1 class="text-3xl font-bold my-4">Manajemen Data Asisten</h1>

    <!-- Form Tambah Asisten -->
    <div class="bg-white p-6 rounded-lg shadow-lg mb-8">
        <h2 class="text-2xl font-semibold mb-4">Tambah Asisten Baru</h2>
        <form action="proses_tambah_asisten.php" method="POST" class="space-y-4">
            <div>
                <label for="nama" class="block text-sm font-semibold text-gray-700">Nama Lengkap</label>
                <input type="text" name="nama" id="nama" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm" required>
            </div>
            <div>
                <label for="nim" class="block text-sm font-semibold text-gray-700">NIM (akan digunakan sebagai Username)</label>
                <input type="text" name="nim" id="nim" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm" required>
            </div>
             <div>
                <label for="angkatan" class="block text-sm font-semibold text-gray-700">Angkatan</label>
                <input type="number" name="angkatan" id="angkatan" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm" required>
            </div>
            
            <!-- Input untuk Password -->
            <div>
                <label for="password" class="block text-sm font-semibold text-gray-700">Password</label>
                <input type="password" name="password" id="password" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm" required>
            </div>
            
            <div class="mt-6">
                <button type="submit" class="py-2 px-4 bg-green-600 hover:bg-green-700 text-white font-medium rounded-md">Simpan Asisten</button>
            </div>
        </form>
    </div>

    <!-- Daftar Asisten -->
    <div class="bg-white p-6 rounded-lg shadow-lg">
        <h2 class="text-2xl font-semibold mb-4">Daftar Asisten</h2>
        <div class="overflow-x-auto">
            <table class="min-w-full bg-white">
                <thead class="bg-gray-200">
                    <tr>
                        <th class="py-2 px-4 text-left">Nama</th>
                        <th class="py-2 px-4 text-left">NIM (Username)</th>
                        <th class="py-2 px-4 text-left">Angkatan</th>
                        <th class="py-2 px-4 text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    // Kita tidak perlu menampilkan kolom username lagi karena sudah diwakili NIM
                    $sql = "SELECT id_asisten, nama, nim, angkatan FROM asisten ORDER BY angkatan DESC, nama ASC";
                    $result = $conn->query($sql);
                    if ($result->num_rows > 0) {
                        while($row = $result->fetch_assoc()) {
                            echo "<tr class='border-b'>";
                            echo "<td class='py-2 px-4'>" . htmlspecialchars($row['nama']) . "</td>";
                            echo "<td class='py-2 px-4 font-mono text-sm'>" . htmlspecialchars($row['nim']) . "</td>";
                            echo "<td class='py-2 px-4'>" . htmlspecialchars($row['angkatan']) . "</td>";
                            echo "<td class='py-2 px-4 text-center'>";
                            echo "<button onclick=\"hapusData(" . $row['id_asisten'] . ", 'asisten')\" class='bg-red-500 text-white px-3 py-1 rounded-md text-sm'><i class='fas fa-trash-alt'></i> Hapus</button>";
                            echo "</td>";
                            echo "</tr>";
                        }
                    } else {
                        echo "<tr><td colspan='4' class='text-center py-4'>Belum ada data asisten.</td></tr>";
                    }
                    ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<script>
function hapusData(id, tipe) {
    if (!confirm(`Apakah Anda yakin ingin menghapus data ${tipe} ini?`)) return;
    fetch('../api/hapus_data.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ id: id, tipe: tipe })
    })
    .then(response => response.json())
    .then(data => {
        alert(data.pesan);
        if (data.status === 'success') location.reload();
    })
    .catch(error => console.error('Error:', error));
}
</script>

</body>
</html>
