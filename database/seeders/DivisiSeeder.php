<?php

namespace Database\Seeders;

use App\Models\Divisi;
use Illuminate\Database\Seeder;

class DivisiSeeder extends Seeder
{
    // Contoh awal. Job desk bisa diubah di halaman Kelola Divisi.
    public function run(): void
    {
        $data = [
            'Subbagian Umum' => 'Membantu pengarsipan dokumen, administrasi surat, inventarisasi barang, dan pelayanan tamu.',
            'Statistik Sosial' => 'Membantu pengolahan dan entri data survei sosial, pengecekan kualitas data, serta penyusunan tabel statistik sosial.',
            'Statistik Produksi' => 'Membantu pengolahan data survei pertanian dan industri, validasi data, serta penyusunan tabel statistik produksi.',
            'Statistik Distribusi dan Jasa' => 'Membantu pengolahan data harga, perdagangan, dan jasa, serta rekap dan validasi hasil survei.',
            'Neraca Wilayah dan Analisis Statistik' => 'Membantu penyusunan data PDRB, analisis statistik sederhana, dan pembuatan publikasi analisis.',
            'Integrasi Pengolahan dan Diseminasi Statistik' => 'Membantu pengolahan data, pengelolaan website dan layanan data, serta desain publikasi dan infografis.',
        ];

        foreach ($data as $nama => $jobdesk) {
            Divisi::firstOrCreate(['nama' => $nama], ['jobdesk' => $jobdesk]);
        }
    }
}