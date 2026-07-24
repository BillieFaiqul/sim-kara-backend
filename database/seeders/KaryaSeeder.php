<?php

namespace Database\Seeders;

use App\Models\Karya;
use App\Models\User;
use Illuminate\Database\Seeder;

class KaryaSeeder extends Seeder
{
    public function run(): void
    {
        $dosenUsers = User::where('role', 'dosen')->get();
        $mahasiswaUsers = User::where('role', 'mahasiswa')->get();

        // Sample Karya data
        $karyaData = [
            [
                'judul' => 'Optimalisasi Desain Rangka Sepeda Listrik',
                'jenis' => 'Publikasi',
                'level' => 'Internasional',
                'tahun' => 2024,
                'deskripsi' => 'Penelitian tentang optimalisasi desain rangka untuk sepeda listrik dengan fokus pada efisiensi energi dan kenyamanan pengemudi.',
                'status' => 'verified',
                'tanggal_submit' => '2024-07-20',
                'user_id' => $dosenUsers->first()->id,
            ],
            [
                'judul' => 'Analisa Performa Motor Biodisel',
                'jenis' => 'Penelitian',
                'level' => 'Nasional',
                'tahun' => 2024,
                'deskripsi' => 'Studi komparatif performa motor diesel dengan bahan bakar biodisel dari berbagai sumber.',
                'status' => 'verified',
                'tanggal_submit' => '2024-07-21',
                'user_id' => $dosenUsers->last()->id,
            ],
            [
                'judul' => 'Sistem Manajemen Gudang Otomatis',
                'jenis' => 'Publikasi',
                'level' => 'Lokal',
                'tahun' => 2024,
                'deskripsi' => 'Pengembangan sistem otomasi untuk manajemen gudang menggunakan IoT dan machine learning.',
                'status' => 'submitted',
                'tanggal_submit' => '2024-07-19',
                'user_id' => $mahasiswaUsers->first()->id ?? $dosenUsers->first()->id,
            ],
            [
                'judul' => 'Sensor Suhu Berbasis IoT',
                'jenis' => 'Penelitian',
                'level' => 'Nasional',
                'tahun' => 2023,
                'deskripsi' => 'Pengembangan sensor suhu presisi tinggi menggunakan teknologi IoT untuk aplikasi industri.',
                'status' => 'rejected',
                'tanggal_submit' => '2024-07-18',
                'alasan_reject' => 'Dokumen tidak lengkap, silakan tambahkan sertifikat penelitian',
                'user_id' => $mahasiswaUsers->last()->id ?? $dosenUsers->last()->id,
            ],
            [
                'judul' => 'Pelatihan Pereparasian Mesin',
                'jenis' => 'Pengabdian',
                'level' => 'Lokal',
                'tahun' => 2024,
                'deskripsi' => 'Program pelatihan berkelanjutan untuk masyarakat tentang perbaikan dan perawatan mesin.',
                'status' => 'verified',
                'tanggal_submit' => '2024-07-15',
                'user_id' => $dosenUsers->first()->id,
            ],
            [
                'judul' => 'Aplikasi Mobile Manajemen Proyek',
                'jenis' => 'Artikel',
                'level' => 'Lokal',
                'tahun' => 2024,
                'deskripsi' => 'Pengembangan aplikasi mobile untuk memudahkan manajemen proyek tim.',
                'status' => 'verified',
                'tanggal_submit' => '2024-07-16',
                'user_id' => $mahasiswaUsers->first()->id ?? $dosenUsers->first()->id,
            ],
            [
                'judul' => 'Sistem Keamanan Berbasis AI',
                'jenis' => 'HKI',
                'level' => 'Internasional',
                'tahun' => 2024,
                'deskripsi' => 'Inovasi sistem keamanan menggunakan artificial intelligence untuk deteksi ancaman real-time.',
                'status' => 'verified',
                'tanggal_submit' => '2024-07-12',
                'user_id' => $dosenUsers->last()->id,
            ],
            [
                'judul' => 'Analisa Efisiensi Mesin Pembakaran Internal',
                'jenis' => 'Publikasi',
                'level' => 'Nasional',
                'tahun' => 2024,
                'deskripsi' => 'Riset mendalam tentang peningkatan efisiensi bahan bakar pada mesin pembakaran internal modern.',
                'status' => 'verified',
                'tanggal_submit' => '2024-07-10',
                'user_id' => $dosenUsers->first()->id,
            ],
            [
                'judul' => 'Desain Turbo Charger Efisien',
                'jenis' => 'Publikasi',
                'level' => 'Nasional',
                'tahun' => 2024,
                'deskripsi' => 'Publikasi tentang desain turbo charger dengan efisiensi tinggi untuk mesin modern.',
                'status' => 'verified',
                'tanggal_submit' => '2024-07-09',
                'user_id' => $dosenUsers->first()->id,
            ],
            [
                'judul' => 'Penelitian Material Logam Ringan',
                'jenis' => 'Penelitian',
                'level' => 'Internasional',
                'tahun' => 2024,
                'deskripsi' => 'Penelitian pengembangan material logam ringan untuk aplikasi aerospace.',
                'status' => 'verified',
                'tanggal_submit' => '2024-07-08',
                'user_id' => $dosenUsers->last()->id,
            ],
            [
                'judul' => 'Program Pelatihan Maintenance Mesin',
                'jenis' => 'Pengabdian',
                'level' => 'Lokal',
                'tahun' => 2024,
                'deskripsi' => 'Program pengabdian kepada masyarakat tentang perawatan dan maintenance mesin industri.',
                'status' => 'verified',
                'tanggal_submit' => '2024-07-07',
                'user_id' => $dosenUsers->first()->id,
            ],
            [
                'judul' => 'Penghargaan Inovasi Energi Terbarukan',
                'jenis' => 'Prestasi',
                'level' => 'Nasional',
                'tahun' => 2024,
                'deskripsi' => 'Penghargaan dari KEMENRISTEKDIKTI untuk inovasi energi terbarukan.',
                'status' => 'verified',
                'tanggal_submit' => '2024-07-06',
                'user_id' => $dosenUsers->last()->id,
            ],
            [
                'judul' => 'Draft Publikasi Baru',
                'jenis' => 'Publikasi',
                'level' => 'Lokal',
                'tahun' => 2024,
                'deskripsi' => 'Publikasi yang masih dalam tahap draft.',
                'status' => 'draft',
                'user_id' => $mahasiswaUsers->last()->id ?? $dosenUsers->first()->id,
            ],
        ];

        foreach ($karyaData as $data) {
            Karya::create($data);
        }
    }
}
