<?php
require '../vendor/autoload.php';
use Dompdf\Dompdf;
include '../config/koneksi.php';

$dompdf = new Dompdf();
$user_id = $_SESSION['user_id'];

// CSS untuk styling
$style = "
<style>
    table { 
        width: 100%; 
        border-collapse: collapse; 
        margin: 20px 0; 
    }
    table th, table td { 
        border: 1px solid black; 
        padding: 8px; 
    }
    table th { 
        background-color: #f5f5f5; 
    }
    h3 { 
        text-align: center; 
        margin: 20px 0; 
    }
    .info-peminjam { 
        margin: 20px 0; 
    }
    .info-peminjam table { 
        width: auto; 
        border: none; 
    }
    .info-peminjam table td { 
        border: none; 
        padding: 3px 10px 3px 0; 
    }
</style>";

// Judul dan Info Peminjam
$content = $style . '
<h3>RIWAYAT PEMINJAMAN SARANA</h3>

<div class="info-peminjam">
    <table>
        <tr>
            <td width="100">Nama</td>
            <td width="10">:</td>
            <td>' . $_SESSION['nama_lengkap'] . '</td>
        </tr>
        <tr>
            <td>Kelas</td>
            <td>:</td>
            <td>' . $_SESSION['kelas'] . '</td>
        </tr>
        <tr>
            <td>Tanggal</td>
            <td>:</td>
            <td>' . date('d/m/Y') . '</td>
        </tr>
    </table>
</div>

<table>
    <tr>
        <th style="width: 5%;">No</th>
        <th style="width: 25%;">Nama Sarana</th>
        <th style="width: 10%;">Jumlah</th>
        <th style="width: 20%;">Tanggal Pinjam</th>
        <th style="width: 20%;">Tanggal Kembali</th>
        <th style="width: 20%;">Status</th>
    </tr>';

$data = mysqli_query($koneksi, "SELECT p.*, s.nama_sarana FROM peminjaman p 
    JOIN sarana s ON p.sarana_id=s.sarana_id 
    WHERE p.user_id = $user_id 
    ORDER BY p.tanggal_pinjam DESC");
$no = 1;

while ($d = mysqli_fetch_array($data)) {
    $tanggal_pinjam = date('d/m/Y', strtotime($d['tanggal_pinjam']));
    $tanggal_kembali = $d['tanggal_kembali'] ? date('d/m/Y', strtotime($d['tanggal_kembali'])) : '-';
    
    $content .= "<tr>
        <td style='text-align: center;'>$no</td>
        <td>{$d['nama_sarana']}</td>
        <td style='text-align: center;'>{$d['jumlah_pinjam']}</td>
        <td style='text-align: center;'>$tanggal_pinjam</td>
        <td style='text-align: center;'>$tanggal_kembali</td>
        <td style='text-align: center;'>{$d['status']}</td>
    </tr>";
    $no++;
}

$content .= '</table>';

// Tanda tangan
$content .= '
<div style="text-align: right; margin-top: 30px;">
    <p>Bekasi, ' . date('d/m/Y') . '</p>
    <p>Peminjam,</p>
    <br><br><br>
    <p><u>' . $_SESSION['nama_lengkap'] . '</u></p>
    <p>Kelas: ' . $_SESSION['kelas'] . '</p>
</div>';

$dompdf->loadHtml($content);
$dompdf->setPaper('A4', 'portrait');
$dompdf->render();
$dompdf->stream('riwayat_peminjaman.pdf', array('Attachment' => false)); 