<?php
namespace App\Utils;

class DummyData
{
    /**
     * Jadwal dummy memuat status campuran agar badge dan timeline bisa diuji.
     */
    public static function getRecruitmentSchedule(): array
    {
        return [
            ['tanggal' => '2026-03-10', 'kegiatan' => 'Sosialisasi Rekrutmen Petugas', 'status' => 'completed'],
            ['tanggal' => '2026-03-15', 'kegiatan' => 'Pendaftaran Online', 'status' => 'ongoing'],
            ['tanggal' => '2026-03-25', 'kegiatan' => 'Seleksi Administrasi', 'status' => 'upcoming'],
            ['tanggal' => '2026-04-05', 'kegiatan' => 'Tes Tertulis (CBT)', 'status' => 'upcoming'],
            ['tanggal' => '2026-04-12', 'kegiatan' => 'Wawancara dan Microteaching', 'status' => 'upcoming'],
            ['tanggal' => '2026-04-20', 'kegiatan' => 'Pelatihan Petugas Gelombang 1', 'status' => 'upcoming'],
        ];
    }

    /**
     * Lowongan dummy mencakup kuota kecil dan tenggat berbeda untuk uji pencarian.
     */
    public static function getLowongan(): array
    {
        return [
            ['id' => 1, 'posisi' => 'Petugas Pencacah Lapangan (PCL)', 'wilayah' => 'Kecamatan Sumbersari', 'tipe' => 'PCL', 'kuota' => 24, 'deadline' => '2026-03-28'],
            ['id' => 2, 'posisi' => 'Petugas Pemeriksa Lapangan (PML)', 'wilayah' => 'Kecamatan Patrang', 'tipe' => 'PML', 'kuota' => 6, 'deadline' => '2026-03-26'],
            ['id' => 3, 'posisi' => 'Petugas Pencacah Lapangan (PCL)', 'wilayah' => 'Kecamatan Kaliwates', 'tipe' => 'PCL', 'kuota' => 18, 'deadline' => '2026-03-30'],
            ['id' => 4, 'posisi' => 'Petugas Pencacah Lapangan (PCL)', 'wilayah' => 'Kecamatan Silo', 'tipe' => 'PCL', 'kuota' => 3, 'deadline' => '2026-03-24'],
        ];
    }

    /**
     * Data pendaftaran dummy dipakai untuk uji lookup status tanpa DB aktif.
     */
    public static function getPendaftaranPetugas(): array
    {
        return [
            [
                'id' => 1,
                'nama_lengkap' => 'Ayu Nabila Putri',
                'nik' => '3509305501010001',
                'email' => 'ayu.nabila@example.id',
                'no_hp' => '081233445566',
                'alamat' => 'Jl. Mastrip No. 14, Sumbersari, Jember',
                'posisi' => 'PCL',
                'wilayah' => 'Sumbersari',
                'status' => 'pending',
                'created_at' => '2026-03-17 08:14:00',
            ],
            [
                'id' => 2,
                'nama_lengkap' => 'Rizky Fadilah',
                'nik' => '3509291209980002',
                'email' => 'rizky.fadilah@example.id',
                'no_hp' => '082145678901',
                'alamat' => 'Perumahan Bumi Tegal Besar, Kaliwates, Jember',
                'posisi' => 'PML',
                'wilayah' => 'Kaliwates',
                'status' => 'verified',
                'created_at' => '2026-03-16 14:45:00',
            ],
            [
                'id' => 3,
                'nama_lengkap' => 'Siti Rahmawati',
                'nik' => '3509074309970003',
                'email' => 'siti.rahmawati@example.id',
                'no_hp' => '085733210987',
                'alamat' => 'Dusun Krajan, Silo, Jember',
                'posisi' => 'PCL',
                'wilayah' => 'Silo',
                'status' => 'rejected',
                'created_at' => '2026-03-15 10:20:00',
            ],
        ];
    }

    public static function findPendaftaranByKeyword(string $keyword): ?array
    {
        $keyword = strtolower(trim($keyword));

        foreach (self::getPendaftaranPetugas() as $pendaftaran) {
            if ($keyword === strtolower($pendaftaran['nik']) || $keyword === strtolower($pendaftaran['email'])) {
                return $pendaftaran;
            }
        }

        return null;
    }

    /**
     * Wilayah dummy menyertakan area dengan keterisian rendah dan hampir penuh.
     */
    public static function getWilayah(): array
    {
        return [
            ['id' => 1, 'kode_kecamatan' => '3509300', 'nama_kecamatan' => 'Sumbersari', 'kebutuhan_pcl' => 22, 'kebutuhan_pml' => 5, 'terisi_pcl' => 18, 'terisi_pml' => 4, 'lat' => -8.1737, 'lng' => 113.7131],
            ['id' => 2, 'kode_kecamatan' => '3509290', 'nama_kecamatan' => 'Kaliwates', 'kebutuhan_pcl' => 20, 'kebutuhan_pml' => 4, 'terisi_pcl' => 16, 'terisi_pml' => 4, 'lat' => -8.1667, 'lng' => 113.7000],
            ['id' => 3, 'kode_kecamatan' => '3509310', 'nama_kecamatan' => 'Patrang', 'kebutuhan_pcl' => 18, 'kebutuhan_pml' => 4, 'terisi_pcl' => 11, 'terisi_pml' => 3, 'lat' => -8.1500, 'lng' => 113.7167],
            ['id' => 4, 'kode_kecamatan' => '3509070', 'nama_kecamatan' => 'Silo', 'kebutuhan_pcl' => 12, 'kebutuhan_pml' => 2, 'terisi_pcl' => 3, 'terisi_pml' => 1, 'lat' => -8.2000, 'lng' => 113.8500],
            ['id' => 5, 'kode_kecamatan' => '3509060', 'nama_kecamatan' => 'Tempurejo', 'kebutuhan_pcl' => 10, 'kebutuhan_pml' => 2, 'terisi_pcl' => 0, 'terisi_pml' => 0, 'lat' => -8.2833, 'lng' => 113.6833],
            ['id' => 6, 'kode_kecamatan' => '3509030', 'nama_kecamatan' => 'Puger', 'kebutuhan_pcl' => 18, 'kebutuhan_pml' => 4, 'terisi_pcl' => 14, 'terisi_pml' => 3, 'lat' => -8.3333, 'lng' => 113.4500],
            ['id' => 7, 'kode_kecamatan' => '3509180', 'nama_kecamatan' => 'Tanggul', 'kebutuhan_pcl' => 13, 'kebutuhan_pml' => 3, 'terisi_pcl' => 7, 'terisi_pml' => 2, 'lat' => -8.1667, 'lng' => 113.4667],
            ['id' => 8, 'kode_kecamatan' => '3509240', 'nama_kecamatan' => 'Kalisat', 'kebutuhan_pcl' => 14, 'kebutuhan_pml' => 3, 'terisi_pcl' => 12, 'terisi_pml' => 2, 'lat' => -8.1500, 'lng' => 113.7500],
        ];
    }

    /**
     * Pengumuman dummy menjaga kontrak field untuk view dan API doc tetap sama.
     */
    public static function getPengumuman(): array
    {
        return [
            [
                'id' => 1,
                'judul' => 'Pengumuman Hasil Seleksi Administrasi PCL Gelombang 1',
                'konten' => 'Peserta yang lolos seleksi administrasi wajib mengikuti CBT sesuai jadwal terlampir.',
                'tipe' => 'hasil_seleksi',
                'file_lampiran' => 'pengumuman_hasil_administrasi_pcl_gel1.pdf',
                'file' => 'pengumuman_hasil_administrasi_pcl_gel1.pdf',
                'is_published' => 1,
                'published_at' => '2026-03-18 09:00:00',
                'tanggal' => '2026-03-18',
            ],
            [
                'id' => 2,
                'judul' => 'Jadwal Pelatihan Petugas SE2026 Gelombang 1',
                'konten' => 'Pelatihan dilaksanakan secara hybrid dengan sesi daring dan luring terpisah.',
                'tipe' => 'jadwal',
                'file_lampiran' => 'jadwal_pelatihan_petugas_gel1.pdf',
                'file' => 'jadwal_pelatihan_petugas_gel1.pdf',
                'is_published' => 1,
                'published_at' => '2026-03-19 07:30:00',
                'tanggal' => '2026-03-19',
            ],
            [
                'id' => 3,
                'judul' => 'Informasi Pembaruan Persyaratan Unggah Dokumen',
                'konten' => 'Ukuran berkas maksimal 5 MB dan dokumen buram akan diminta revisi.',
                'tipe' => 'info',
                'file_lampiran' => null,
                'file' => null,
                'is_published' => 1,
                'published_at' => '2026-03-14 16:45:00',
                'tanggal' => '2026-03-14',
            ],
        ];
    }

    /**
     * Pelatihan dummy mencampur sesi daring/luring, single-day, dan tanpa Zoom.
     */
    public static function getPelatihan(): array
    {
        return [
            [
                'id' => 1,
                'judul' => 'Pelatihan CAPI SE2026 Gelombang 1',
                'tipe' => 'online',
                'tanggal_mulai' => '2026-04-15',
                'tanggal_selesai' => '2026-04-15',
                'waktu_mulai' => '08:00:00',
                'waktu_selesai' => '11:30:00',
                'tempat' => 'Zoom Meeting BPS Jember',
                'deskripsi' => 'Pengenalan alur login, listing, dan sinkronisasi CAPI.',
                'zoom_link' => 'https://zoom.example.id/sise2026-gel1',
                'status' => 'scheduled',
                'peserta' => 45,
            ],
            [
                'id' => 2,
                'judul' => 'Klinik Validasi Anomali Lapangan',
                'tipe' => 'online',
                'tanggal_mulai' => '2026-04-18',
                'tanggal_selesai' => null,
                'waktu_mulai' => '13:00:00',
                'waktu_selesai' => '15:00:00',
                'tempat' => 'Ruang Virtual QA',
                'deskripsi' => 'Sesi tanya jawab cepat untuk kasus duplikasi dan usaha tutup.',
                'zoom_link' => '',
                'status' => 'scheduled',
                'peserta' => 18,
            ],
            [
                'id' => 3,
                'judul' => 'Classroom Training PCL Gelombang 1',
                'tipe' => 'offline',
                'tanggal_mulai' => '2026-04-20',
                'tanggal_selesai' => '2026-04-21',
                'waktu_mulai' => '08:00:00',
                'waktu_selesai' => '16:00:00',
                'tempat' => 'Aula BPS Kabupaten Jember',
                'deskripsi' => 'Simulasi wawancara dan role play kunjungan usaha.',
                'zoom_link' => '',
                'status' => 'scheduled',
                'peserta' => 60,
            ],
            [
                'id' => 4,
                'judul' => 'Briefing Monitoring Mingguan Korwil',
                'tipe' => 'offline',
                'tanggal_mulai' => '2026-03-12',
                'tanggal_selesai' => '2026-03-12',
                'waktu_mulai' => '09:00:00',
                'waktu_selesai' => '10:30:00',
                'tempat' => 'Ruang Rapat Lt. 2',
                'deskripsi' => 'Review progres mingguan dan distribusi tindak lanjut.',
                'zoom_link' => '',
                'status' => 'completed',
                'peserta' => 14,
            ],
        ];
    }

    /**
     * Materi dummy menutup semua tipe ikon yang dipakai view.
     */
    public static function getMateri(): array
    {
        $materi = [
            ['id' => 1, 'judul' => 'Pedoman Pencacahan SE2026', 'kategori' => 'Pedoman', 'tipe' => 'PDF', 'file_path' => 'pedoman_pencacahan_se2026.pdf', 'file_size' => 4200000, 'downloads' => 856],
            ['id' => 2, 'judul' => 'Template Quality Control Anomali', 'kategori' => 'Template', 'tipe' => 'XLSX', 'file_path' => 'template_qc_anomali_se2026.xlsx', 'file_size' => 248000, 'downloads' => 91],
            ['id' => 3, 'judul' => 'Bahan Tayang KBLI 2020 untuk PML', 'kategori' => 'Pelatihan', 'tipe' => 'PPTX', 'file_path' => 'bahan_tayang_kbli_2020_pml.pptx', 'file_size' => 3100000, 'downloads' => 134],
            ['id' => 4, 'judul' => 'Video Simulasi Wawancara Responden Sulit', 'kategori' => 'Video', 'tipe' => 'MP4', 'file_path' => 'video_simulasi_wawancara_responden_sulit.mp4', 'file_size' => 15800000, 'downloads' => 47],
        ];

        return array_map([self::class, 'withMateriMeta'], $materi);
    }

    /**
     * QnA dummy memastikan forum tetap bisa diuji tanpa tabel qna terisi.
     */
    public static function getQnaPelatihan(int $pelatihanId): array
    {
        $rows = [
            ['id' => 1, 'pelatihan_id' => 1, 'pertanyaan' => 'Apakah sinkronisasi bisa dilakukan saat koneksi putus-putus?', 'jawaban' => 'Bisa. Data akan masuk antrean lokal dan dikirim ulang saat perangkat kembali online.', 'votes' => 7, 'user_nama' => 'Dina PCL'],
            ['id' => 2, 'pelatihan_id' => 1, 'pertanyaan' => 'Bagaimana jika usaha tutup permanen tetapi alamat masih aktif di daftar?', 'jawaban' => null, 'votes' => 3, 'user_nama' => 'Bagus PML'],
            ['id' => 3, 'pelatihan_id' => 2, 'pertanyaan' => 'Apakah kasus duplikasi lintas blok sensus harus dilaporkan dua kali?', 'jawaban' => 'Tidak. Laporkan satu kali dengan referensi blok terkait di deskripsi anomali.', 'votes' => 5, 'user_nama' => 'Rina Operator'],
        ];

        return array_values(array_filter($rows, function ($row) use ($pelatihanId) {
            return (int) $row['pelatihan_id'] === $pelatihanId;
        }));
    }

    /**
     * Anomali dummy mencampur seluruh status workflow agar dashboard ringkas teruji.
     */
    public static function getAnomaly(): array
    {
        return [
            ['id' => 1, 'judul' => 'SLS 004 tidak ditemukan di lapangan', 'wilayah' => 'Kec. Silo', 'status' => 'reported', 'tanggal' => '2026-05-10', 'pelapor' => 'Ahmad PCL'],
            ['id' => 2, 'judul' => 'Duplikasi usaha kuliner pada dua blok sensus', 'wilayah' => 'Kec. Kaliwates', 'status' => 'review', 'tanggal' => '2026-05-11', 'pelapor' => 'Rizky PML'],
            ['id' => 3, 'judul' => 'Usaha besar belum memiliki KBLI rinci', 'wilayah' => 'Kec. Patrang', 'status' => 'resolved', 'tanggal' => '2026-05-12', 'pelapor' => 'Nadia Operator'],
            ['id' => 4, 'judul' => 'Laporan bukti tidak lengkap untuk usaha musiman', 'wilayah' => 'Kec. Tempurejo', 'status' => 'rejected', 'tanggal' => '2026-05-13', 'pelapor' => 'Fajar PCL'],
        ];
    }

    /**
     * Sektor dummy dibuat tidak seragam agar visual progress dan alert lebih realistis.
     */
    public static function getSektorProgress(): array
    {
        return [
            ['sektor' => 'Perdagangan Besar & Eceran', 'progres' => 85, 'total' => 150230],
            ['sektor' => 'Industri Pengolahan', 'progres' => 62, 'total' => 45600],
            ['sektor' => 'Penyediaan Akomodasi & Makan Minum', 'progres' => 45, 'total' => 88900],
            ['sektor' => 'Jasa Pendidikan', 'progres' => 92, 'total' => 12400],
            ['sektor' => 'Transportasi & Pergudangan', 'progres' => 30, 'total' => 25700],
        ];
    }

    public static function getSuratKeputusan(): array
    {
        return [
            ['id' => 1, 'nomor_sk' => 'SK/001/3509/SE2026/III/2026', 'judul' => 'Penetapan Panitia Pelaksana SISE2026 Kabupaten Jember', 'tanggal_sk' => '2026-03-01', 'status' => 'published'],
            ['id' => 2, 'nomor_sk' => 'SK/014/3509/SE2026/III/2026', 'judul' => 'Penunjukan Koordinator Pelatihan Petugas Lapangan', 'tanggal_sk' => '2026-03-14', 'status' => 'draft'],
            ['id' => 3, 'nomor_sk' => 'SK/021/3509/SE2026/IV/2026', 'judul' => 'Perubahan Alokasi Wilayah Kerja Kecamatan Tempurejo', 'tanggal_sk' => '2026-04-02', 'status' => 'archived'],
        ];
    }

    /**
     * Surat masuk memuat status berbeda dan perihal panjang untuk uji truncation tabel.
     */
    public static function getSuratMasuk(): array
    {
        return [
            ['id' => 1, 'nomor_surat' => 'B-112/35000/PP.01/2026', 'pengirim' => 'BPS Provinsi Jawa Timur', 'perihal' => 'Pedoman Pelaksanaan Rekrutmen Petugas Lapangan Sensus Ekonomi 2026', 'tanggal_terima' => '2026-03-03', 'status' => 'selesai'],
            ['id' => 2, 'nomor_surat' => '445/198/35.09.414/2026', 'pengirim' => 'Kecamatan Silo', 'perihal' => 'Permohonan Sosialisasi Tambahan di Wilayah Blank Spot', 'tanggal_terima' => '2026-03-09', 'status' => 'disposisi'],
            ['id' => 3, 'nomor_surat' => '420/77/35.09.201/2026', 'pengirim' => 'SMKN 1 Jember', 'perihal' => 'Konfirmasi Tempat Pelatihan Gelombang 1', 'tanggal_terima' => '2026-03-11', 'status' => 'baru'],
            ['id' => 4, 'nomor_surat' => 'B-221/35000/PP.02/2026', 'pengirim' => 'BPS Provinsi Jawa Timur', 'perihal' => 'Permintaan Klarifikasi Anomali Daftar Usaha Musiman', 'tanggal_terima' => '2026-03-15', 'status' => 'proses'],
        ];
    }

    public static function getSuratKeluar(): array
    {
        return [
            ['id' => 1, 'nomor_surat' => '35090.01/SE2026/III/2026/015', 'tujuan' => 'Seluruh KSK Kabupaten Jember', 'perihal' => 'Instruksi Rekrutmen Tahap Verifikasi Administrasi', 'tanggal_surat' => '2026-03-05', 'status' => 'sent'],
            ['id' => 2, 'nomor_surat' => '35090.01/SE2026/III/2026/021', 'tujuan' => 'BPS Provinsi Jawa Timur', 'perihal' => 'Laporan Kesiapan Pelatihan Petugas Gelombang 1', 'tanggal_surat' => '2026-03-12', 'status' => 'draft'],
            ['id' => 3, 'nomor_surat' => '35090.01/SE2026/IV/2026/004', 'tujuan' => 'Camat Tempurejo', 'perihal' => 'Permohonan Dukungan Sosialisasi Wilayah Akses Terbatas', 'tanggal_surat' => '2026-04-01', 'status' => 'archived'],
        ];
    }

    public static function getMemoList(): array
    {
        return [
            ['id' => 1, 'nomor' => 'MEMO/001/2026', 'judul' => 'Rapat Koordinasi Tim SE2026', 'tipe' => 'undangan', 'tanggal' => '2026-03-20', 'waktu' => '09:00', 'tempat' => 'Aula BPS Jember', 'konfirmasi' => 12],
            ['id' => 2, 'nomor' => 'MEMO/002/2026', 'judul' => 'Memorandum Kesiapan Peralatan Pencacahan', 'tipe' => 'memo', 'tanggal' => '2026-03-18', 'waktu' => '-', 'tempat' => '-', 'konfirmasi' => 0],
            ['id' => 3, 'nomor' => 'MEMO/003/2026', 'judul' => 'Undangan Sosialisasi SE2026 ke Kecamatan', 'tipe' => 'undangan', 'tanggal' => '2026-03-25', 'waktu' => '10:00', 'tempat' => 'Pendopo Kabupaten', 'konfirmasi' => 28],
            ['id' => 4, 'nomor' => 'MEMO/004/2026', 'judul' => 'Catatan Perbaikan Daftar Usaha Musiman', 'tipe' => 'memo', 'tanggal' => '2026-03-27', 'waktu' => '-', 'tempat' => '-', 'konfirmasi' => 0],
        ];
    }

    public static function getLaporanKegiatan(): array
    {
        return [
            ['id' => 1, 'judul' => 'Rapat Koordinasi Tim SBR Jember', 'tanggal' => '2026-03-05', 'lokasi' => 'Aula BPS Jember', 'status' => 'approved'],
            ['id' => 2, 'judul' => 'Sosialisasi SE2026 Kec. Sumbersari', 'tanggal' => '2026-03-08', 'lokasi' => 'Kec. Sumbersari', 'status' => 'submitted'],
            ['id' => 3, 'judul' => 'Pelatihan CAPI SE2026 Gelombang 1', 'tanggal' => '2026-03-12', 'lokasi' => 'Ruang Rapat Lt. 2', 'status' => 'draft'],
            ['id' => 4, 'judul' => 'Monitoring Wilayah Blank Spot Kecamatan Tempurejo', 'tanggal' => '2026-05-03', 'lokasi' => 'Tempurejo', 'status' => 'revision'],
        ];
    }

    public static function getNotulenRapat(): array
    {
        return [
            ['id' => 1, 'judul' => 'Rakor Persiapan Lapangan SE2026', 'tanggal' => '2026-03-10', 'pimpinan' => 'Kepala BPS Jember', 'peserta' => 15, 'tindak_lanjut' => 3],
            ['id' => 2, 'judul' => 'Evaluasi Rekrutmen PCL/PML', 'tanggal' => '2026-03-07', 'pimpinan' => 'Kasi Distribusi', 'peserta' => 8, 'tindak_lanjut' => 5],
            ['id' => 3, 'judul' => 'Pembahasan Logistik dan Peralatan', 'tanggal' => '2026-03-03', 'pimpinan' => 'KSK Koordinator', 'peserta' => 12, 'tindak_lanjut' => 2],
        ];
    }

    public static function getDocumentationVideos(): array
    {
        return [
            ['id' => 1, 'judul' => 'Pelatihan CAPI SE2026 - Sesi 1: Pengenalan Aplikasi', 'tanggal' => '2026-03-05', 'durasi' => '1:45:00', 'views' => 234],
            ['id' => 2, 'judul' => 'Workshop KBLI 2020 untuk Petugas Lapangan', 'tanggal' => '2026-03-08', 'durasi' => '2:10:00', 'views' => 189],
            ['id' => 3, 'judul' => 'Teknik Wawancara dan Pencacahan SE2026', 'tanggal' => '2026-03-10', 'durasi' => '1:30:00', 'views' => 312],
            ['id' => 4, 'judul' => 'Penggunaan GPS dan Peta Digital', 'tanggal' => '2026-03-12', 'durasi' => '0:55:00', 'views' => 156],
        ];
    }

    public static function getDocumentationAlbums(): array
    {
        return [
            ['id' => 1, 'judul' => 'Workshop KBLI 2020 - 20-21 April 2026', 'tanggal' => '2026-04-20', 'foto_count' => 24, 'peserta' => 60],
            ['id' => 2, 'judul' => 'Classroom Training PCL Gelombang 1', 'tanggal' => '2026-04-15', 'foto_count' => 18, 'peserta' => 45],
            ['id' => 3, 'judul' => 'Sosialisasi SE2026 di Kecamatan', 'tanggal' => '2026-03-25', 'foto_count' => 32, 'peserta' => 85],
            ['id' => 4, 'judul' => 'Pelatihan Kecamatan Tempurejo dengan Koneksi Terbatas', 'tanggal' => '2026-04-23', 'foto_count' => 7, 'peserta' => 12],
        ];
    }

    public static function getDocumentationPhotos(): array
    {
        return [
            ['id' => 1, 'nama' => 'SE2026_SOSIALISASI_SUMBERSARI_001.jpg', 'kegiatan' => 'Sosialisasi Kec. Sumbersari', 'tanggal' => '2026-03-05', 'size' => '2.4 MB'],
            ['id' => 2, 'nama' => 'SE2026_PELATIHAN_CAPI_001.jpg', 'kegiatan' => 'Pelatihan CAPI Gel. 1', 'tanggal' => '2026-03-08', 'size' => '1.8 MB'],
            ['id' => 3, 'nama' => 'SE2026_RAKOR_PANITIA_001.jpg', 'kegiatan' => 'Rakor Panitia SE2026', 'tanggal' => '2026-03-10', 'size' => '3.1 MB'],
            ['id' => 4, 'nama' => 'SE2026_PENCACAHAN_PATRANG_001.jpg', 'kegiatan' => 'Pencacahan Kec. Patrang', 'tanggal' => '2026-05-02', 'size' => '2.0 MB'],
            ['id' => 5, 'nama' => 'SE2026_MONITORING_KALIWATES_001.jpg', 'kegiatan' => 'Monitoring Kec. Kaliwates', 'tanggal' => '2026-05-05', 'size' => '1.5 MB'],
            ['id' => 6, 'nama' => 'SE2026_EVALUASI_MINGGUAN_001.jpg', 'kegiatan' => 'Evaluasi Mingguan', 'tanggal' => '2026-05-10', 'size' => '2.2 MB'],
        ];
    }

    public static function getDocumentationMeetings(): array
    {
        return [
            ['id' => 1, 'judul' => 'Rakor Persiapan SE2026', 'tanggal' => '2026-03-10', 'notulen' => true, 'hadir' => 15, 'foto' => 5],
            ['id' => 2, 'judul' => 'Evaluasi Rekrutmen Petugas', 'tanggal' => '2026-03-07', 'notulen' => true, 'hadir' => 8, 'foto' => 3],
            ['id' => 3, 'judul' => 'Pembahasan Alokasi Wilayah', 'tanggal' => '2026-03-03', 'notulen' => false, 'hadir' => 12, 'foto' => 8],
        ];
    }

    /**
     * Ikon file ditambahkan di sini agar semua sumber materi memakai kontrak yang sama.
     */
    private static function withMateriMeta(array $materi): array
    {
        $iconMap = [
            'PDF' => 'fa-file-pdf text-red-500',
            'PPT' => 'fa-file-powerpoint text-orange-500',
            'PPTX' => 'fa-file-powerpoint text-orange-500',
            'XLS' => 'fa-file-excel text-green-500',
            'XLSX' => 'fa-file-excel text-green-500',
            'MP4' => 'fa-file-video text-blue-500',
        ];

        $tipe = strtoupper($materi['tipe'] ?? '');
        $materi['icon'] = $iconMap[$tipe] ?? 'fa-file-alt text-slate-500';

        return $materi;
    }
}
