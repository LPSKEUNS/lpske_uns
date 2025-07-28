<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login - LPSKE</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        body {
            font-family: 'Inter', sans-serif;
            /* Mengatur gambar latar belakang */
            background-image: url('https://safiragrup.com/wp-content/uploads/2023/05/pintu-masuk-unversitas-sebelas-maret-uns-1024x588.jpg'); /* Ganti URL ini dengan URL gambar Anda */
            background-size: cover; /* Memastikan gambar menutupi seluruh area */
            background-position: center; /* Memusatkan gambar */
            background-repeat: no-repeat; /* Mencegah gambar berulang */
            background-attachment: fixed; /* Membuat gambar tetap saat scroll */
            display: flex;
            align-items: center;
            justify-content: center;
            min-height: 100vh;
            position: relative; /* Diperlukan untuk overlay */
        }

        /* Overlay gelap untuk membuat teks lebih mudah dibaca */
        body::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(0, 0, 0, 0.6); /* Warna overlay hitam dengan opasitas 60% */
            z-index: 0;
        }

        /* Memastikan konten form berada di atas overlay */
        .form-container {
            position: relative;
            z-index: 10;
        }
    </style>
</head>
<body class="">
    <div class="w-full max-w-md form-container">
        <form action="proses_login.php" method="POST" class="bg-white bg-opacity-90 backdrop-filter backdrop-blur-sm shadow-2xl rounded-xl px-8 pt-8 pb-10 mb-4 border border-gray-200">
            <h2 class="text-3xl font-extrabold text-gray-900 text-center mb-8">Portal Admin <span class="text-blue-600">LPSKE</span></h2>
            
            <?php 
                if(isset($_GET['error'])) {
                    echo '<p class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-6" role="alert">
                            <strong class="font-bold">Login Gagal!</strong>
                            <span class="block sm:inline">Username atau password salah.</span>
                          </p>';
                }
            ?>

            <div class="mb-6">
                <label class="block text-gray-700 text-sm font-bold mb-2" for="username">
                    Username
                </label>
                <div class="relative">
                    <input class="shadow-sm appearance-none border border-gray-300 rounded-lg w-full py-3 px-4 pl-10 text-gray-700 leading-tight focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition duration-200" id="username" name="username" type="text" placeholder="Masukkan Username Anda" required>
                    <i class="fas fa-user absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400"></i>
                </div>
            </div>
            <div class="mb-8">
                <label class="block text-gray-700 text-sm font-bold mb-2" for="password">
                    Password
                </label>
                <div class="relative">
                    <input class="shadow-sm appearance-none border border-gray-300 rounded-lg w-full py-3 px-4 pl-10 text-gray-700 mb-3 leading-tight focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition duration-200" id="password" name="password" type="password" placeholder="******************" required>
                    <i class="fas fa-lock absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400"></i>
                </div>
            </div>
            
            <div class="flex items-center justify-between">
                <button class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-3 px-8 rounded-full focus:outline-none focus:shadow-outline shadow-lg transition-all duration-300 hover:shadow-xl hover:-translate-y-1" type="submit">
                    Login
                </button>
                <a href="index.html" class="inline-flex items-center justify-center bg-gray-300 hover:bg-gray-400 text-gray-800 font-bold py-3 px-6 rounded-full shadow-lg transition-all duration-300 hover:shadow-xl hover:-translate-y-1">
                    <i class="fas fa-home mr-2"></i> Home
                </a>
            </div>
        </form>
        <p class="text-center text-gray-100 text-sm mt-6">
            &copy;2025 LPSKE. All rights reserved.
        </p>
    </div>
</body>
</html>
