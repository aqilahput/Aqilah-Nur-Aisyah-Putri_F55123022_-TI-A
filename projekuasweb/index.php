<?php
// Mulai Session
session_start();

// Konfigurasi koneksi ke database
$host = "localhost";
$user = "root";
$pass = "";
$db   = "projekuas";
$conn = mysqli_connect($host, $user, $pass, $db);

// Periksa koneksi
if (!$conn) {
    die("Koneksi gagal: " . mysqli_connect_error());
}

// Set Cookie
if (!isset($_COOKIE['pengunjung'])) {
    setcookie('pengunjung', 'sudah berkunjung', time() + 3600);
    $welcome_message = "Selamat datang di halaman CRUD Mahasiswa!";
} else {
    $welcome_message = "Selamat datang kembali!";
}

// Tambah Data (Create)
if (isset($_POST['tambah'])) {
    $nama   = $_POST['namamahasiswa'];
    $npm    = $_POST['npm'];
    $prodi  = $_POST['prodi'];
    $email  = $_POST['email'];

    // Upload Gambar
    $gambar = $_FILES['gambar']['aqilah.jpg'];
    $tmp_name = $_FILES['gambar']['tmp_name'];
    move_uploaded_file($tmp_name, "images/$gambar");


    $query = "INSERT INTO datamahasiswa (namamahasiswa, npm, prodi, email, gambar) 
              VALUES ('$nama', '$npm', '$prodi', '$email', '$gambar')";
    mysqli_query($conn, $query);

    $_SESSION['message'] = "Data berhasil ditambahkan!";
    header("Location: index.php");
}

// Hapus Data (Delete)
if (isset($_GET['hapus'])) {
    $id = $_GET['hapus'];
    mysqli_query($conn, "DELETE FROM datamahasiswa WHERE id = $id");

    $_SESSION['message'] = "Data berhasil dihapus!";
    header("Location: index.php");
}

// Edit Data (Update)
if (isset($_POST['update'])) {
    $id     = $_POST['id'];
    $nama   = $_POST['namamahasiswa'];
    $npm    = $_POST['npm'];
    $prodi  = $_POST['prodi'];
    $email  = $_POST['email'];

    // Upload Gambar (opsional jika diubah)
    if (!empty($_FILES['gambar']['name'])) {
        $gambar = $_FILES['gambar']['name'];
        $tmp_name = $_FILES['gambar']['tmp_name'];
        move_uploaded_file($tmp_name, "images/$gambar");

        $query = "UPDATE datamahasiswa SET 
                  namamahasiswa = '$nama', 
                  npm = '$npm', 
                  prodi = '$prodi', 
                  email = '$email', 
                  gambar = '$gambar'
                  WHERE id = $id";
    } else {
        $query = "UPDATE datamahasiswa SET 
                  namamahasiswa = '$nama', 
                  npm = '$npm', 
                  prodi = '$prodi', 
                  email = '$email'
                  WHERE id = $id";
    }

    mysqli_query($conn, $query);
    $_SESSION['message'] = "Data berhasil diupdate!";
    header("Location: index.php");
}

// Ambil Data (Read)
$result = mysqli_query($conn, "SELECT * FROM datamahasiswa");

// Ambil data untuk edit jika ada ID dari parameter
$edit = null;
if (isset($_GET['edit'])) {
    $id = $_GET['edit'];
    $edit_result = mysqli_query($conn, "SELECT * FROM datamahasiswa WHERE id = $id");
    $edit = mysqli_fetch_assoc($edit_result);
}

// Array Contoh Data Mahasiswa
$mahasiswa = [
    ["nama" => "Aqilah", "npm" => "123456"],
    ["nama" => "Alifah", "npm" => "234567"],
];

$hello = "Hello, World!";
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CRUD Data Mahasiswa</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 20px;
            background-color: #f4f4f9;
        }
        h1, h2 {
            color: #333;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        table th, table td {
            border: 1px solid #ccc;
            padding: 10px;
            text-align: center;
        }
        table th {
            background-color: #007BFF;
            color: white;
        }
        table tr:nth-child(even) {
            background-color: #f9f9f9;
        }
        form {
            margin-top: 20px;
            padding: 20px;
            background: #fff;
            border: 1px solid #ddd;
            border-radius: 5px;
        }
        form input, form button {
            margin: 5px 0;
            padding: 10px;
            width: calc(100% - 22px);
        }
        form button {
            background-color: #007BFF;
            color: white;
            border: none;
            cursor: pointer;
        }
        form button:hover {
            background-color: #0056b3;
        }
        a {
            text-decoration: none;
            color: #007BFF;
        }
        a:hover {
            text-decoration: underline;
        }
        img {
            width: 100px;
            height: auto;
            border-radius: 5px;
        }
    </style>
</head>
<body>
    <h1><?= $hello; ?></h1>
    <p><?= $welcome_message; ?></p>

    <!-- Session Message -->
    <?php if (isset($_SESSION['message'])): ?>
        <p style="color:green;">
            <?= $_SESSION['message']; unset($_SESSION['message']); ?>
        </p>
    <?php endif; ?>

    <!-- Form Tambah / Edit -->
    <h2><?php echo isset($edit) ? "Edit Data" : "Tambah Data"; ?></h2>
    <form method="POST" enctype="multipart/form-data">
        <?php if (isset($edit)) : ?>
            <input type="hidden" name="id" value="<?= $edit['id']; ?>">
        <?php endif; ?>
        Nama Mahasiswa: <input type="text" name="namamahasiswa" value="<?= $edit['namamahasiswa'] ?? ''; ?>" required><br>
        NPM: <input type="text" name="npm" value="<?= $edit['npm'] ?? ''; ?>" required><br>
        Prodi: <input type="text" name="prodi" value="<?= $edit['prodi'] ?? ''; ?>" required><br>
        Email: <input type="email" name="email" value="<?= $edit['email'] ?? ''; ?>" required><br>
        Gambar: <input type="file" name="gambar"><br>
        <button type="submit" name="<?= isset($edit) ? 'update' : 'tambah'; ?>">
            <?= isset($edit) ? "Update Data" : "Tambah Data"; ?>
        </button>
    </form>

    <!-- Tabel Data Mahasiswa -->
    <table>
        <tr>
            <th>ID</th>
            <th>Nama Mahasiswa</th>
            <th>NPM</th>
            <th>Prodi</th>
            <th>Email</th>
            <th>Gambar</th>
            <th>Aksi</th>
        </tr>
        <?php while ($row = mysqli_fetch_assoc($result)) : ?>
        <tr>
            <td><?= $row['id']; ?></td>
            <td><?= $row['namamahasiswa']; ?></td>
            <td><?= $row['npm']; ?></td>
            <td><?= $row['prodi']; ?></td>
            <td><?= $row['email']; ?></td>
            <td><img src="images/<?= $row['gambar']; ?>" alt="Gambar"></td>
            <td>
                <a href="index.php?edit=<?= $row['id']; ?>">Edit</a> |
                <a href="index.php?hapus=<?= $row['id']; ?>" onclick="return confirm('Yakin ingin menghapus?');">Hapus</a>
            </td>
        </tr>
        <?php endwhile; ?>
    </table>

    <!-- Pengulangan -->
    <h2>Contoh Pengulangan</h2>
    <?php
        for ($i = 1; $i <= 5; $i++) {
            echo "<p>Pengulangan For ke-$i</p>";
        }
        $j = 1;
        while ($j <= 5) {
            echo "<p>Pengulangan While ke-$j</p>";
            $j++;
        }
        $k = 1;
        do {
            echo "<p>Pengulangan Do While ke-$k</p>";
            $k++;
        } while ($k <= 5);

        foreach ($mahasiswa as $mhs) {
            echo "<p>Nama: {$mhs['nama']}, NPM: {$mhs['npm']}</p>";
        }
    ?>
</body>
</html>
