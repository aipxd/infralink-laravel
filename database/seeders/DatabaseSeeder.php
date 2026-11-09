<?php

namespace Database\Seeders;

use App\Models\Attachment;
use App\Models\BlockedSlot;
use App\Models\Booking;
use App\Models\BookingStatusLog;
use App\Models\Facility;
use App\Models\FacilityCategory;
use App\Models\FacilityPhoto;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Users
        $admin = User::firstOrCreate(
            ['email' => 'admin@infralink.ac.id'],
            [
                'name' => 'Administrator Kampus',
                'password' => Hash::make('password123'),
                'identity_number' => '198501152010121001',
                'unit_name' => 'Biro Sarana dan Prasarana Kampus',
                'phone' => '081234567890',
                'role' => 'ADMIN',
                'is_active' => true,
            ]
        );

        $approver = User::firstOrCreate(
            ['email' => 'approver@infralink.ac.id'],
            [
                'name' => 'Dr. Hendra Saputra, M.T.',
                'password' => Hash::make('password123'),
                'identity_number' => '197904202005011002',
                'unit_name' => 'Subbag Pengelolaan Fasilitas',
                'phone' => '081298765432',
                'role' => 'APPROVER',
                'is_active' => true,
            ]
        );

        $pemohon1 = User::firstOrCreate(
            ['email' => 'mahasiswa@infralink.ac.id'],
            [
                'name' => 'Adibta Ilham (BEM)',
                'password' => Hash::make('password123'),
                'identity_number' => '2301010042',
                'unit_name' => 'BEM Fakultas Ilmu Komputer',
                'phone' => '082155554321',
                'role' => 'PEMOHON',
                'is_active' => true,
            ]
        );

        $pemohon2 = User::firstOrCreate(
            ['email' => 'hima@infralink.ac.id'],
            [
                'name' => 'Siti Nurhaliza (HIMASISFO)',
                'password' => Hash::make('password123'),
                'identity_number' => '2301010088',
                'unit_name' => 'Himpunan Mahasiswa Sistem Informasi',
                'phone' => '085711223344',
                'role' => 'PEMOHON',
                'is_active' => true,
            ]
        );

        // 2. Categories
        $catAula = FacilityCategory::firstOrCreate(['name' => 'Aula & Auditorium']);
        $catSeminar = FacilityCategory::firstOrCreate(['name' => 'Ruang Seminar & Rapat']);
        $catLab = FacilityCategory::firstOrCreate(['name' => 'Laboratorium']);
        $catOlahraga = FacilityCategory::firstOrCreate(['name' => 'Sarana Olahraga']);

        // 3. Facilities
        $facilitiesData = [
            [
                'category_id' => $catAula->id,
                'name' => 'Auditorium Utama Kampus',
                'building' => 'Gedung Rektorat Terpadu',
                'location_detail' => 'Lantai 3, Sayap Barat',
                'capacity' => 500,
                'description' => 'Auditorium berkapasitas besar dengan sound system line array profesional, 2 proyektor laser 10.000 lumens, AC sentral, dan backstage.',
                'usage_rules' => '1. Tidak diperkenankan membawa makanan berkuah ke area karpet.\n2. Wajib membersihkan sampah setelah acara selesai.\n3. Dilarang merokok dan vape di dalam auditorium.',
                'open_time' => '08:00',
                'close_time' => '21:00',
                'max_duration_minutes' => 480, // 8 jam
                'is_active' => true,
                'photos' => [
                    'https://images.unsplash.com/photo-1517457373958-b7bdd4587205?auto=format&fit=crop&w=1000&q=80',
                    'https://images.unsplash.com/photo-1540575467063-178a50c2df87?auto=format&fit=crop&w=1000&q=80',
                ],
            ],
            [
                'category_id' => $catSeminar->id,
                'name' => 'Ruang Seminar Smart Class',
                'building' => 'Gedung Pascasarjana & Riset',
                'location_detail' => 'Lantai 2, Ruang 204',
                'capacity' => 80,
                'description' => 'Ruangan seminar modern dengan interactive smartboard display, mic wireless, podium pembicara, dan kursi tiering ergonomis.',
                'usage_rules' => '1. Mengembalikan posisi kursi dan meja sesuai layout semula.\n2. Matikan proyektor dan AC sebelum meninggalkan ruangan.',
                'open_time' => '07:30',
                'close_time' => '18:00',
                'max_duration_minutes' => 240, // 4 jam
                'is_active' => true,
                'photos' => [
                    'https://images.unsplash.com/photo-1497366216548-37526070297c?auto=format&fit=crop&w=1000&q=80',
                    'https://images.unsplash.com/photo-1517245386807-bb43f82c33c4?auto=format&fit=crop&w=1000&q=80',
                ],
            ],
            [
                'category_id' => $catSeminar->id,
                'name' => 'Ruang Rapat Senat Eksekutif',
                'building' => 'Gedung Rektorat Terpadu',
                'location_detail' => 'Lantai 2, Ruang Rapat Utama',
                'capacity' => 35,
                'description' => 'Meja bundar konferensi eksekutif dengan microphone konferensi delegasi per meja, display TV 75 inch, dan koneksi video call Zoom Rooms.',
                'usage_rules' => '1. Penggunaan khusus untuk rapat ormawa formal, sidang, atau diskusi akademik tingkat pimpinan.\n2. Wajib menjaga keheningan koridor sekitar.',
                'open_time' => '08:00',
                'close_time' => '17:00',
                'max_duration_minutes' => 180, // 3 jam
                'is_active' => true,
                'photos' => [
                    'https://images.unsplash.com/photo-1505373877841-8d25f7d46678?auto=format&fit=crop&w=1000&q=80',
                ],
            ],
            [
                'category_id' => $catLab->id,
                'name' => 'Laboratorium Multimedia & AI',
                'building' => 'Gedung Fakultas Ilmu Komputer',
                'location_detail' => 'Lantai 4, Lab 4.2',
                'capacity' => 40,
                'description' => '40 Unit PC Workstation high-end dengan GPU RTX 4070, Gigabit LAN, Gigabit Internet, proyektor, dan AC ganda.',
                'usage_rules' => '1. Dilarang menginstal software bajakan atau mengubah konfigurasi jaringan.\n2. Tidak boleh membawa makanan/minuman ke meja komputer.',
                'open_time' => '08:00',
                'close_time' => '20:00',
                'max_duration_minutes' => 240, // 4 jam
                'is_active' => true,
                'photos' => [
                    'https://images.unsplash.com/photo-1562774053-701939374585?auto=format&fit=crop&w=1000&q=80',
                ],
            ],
            [
                'category_id' => $catOlahraga->id,
                'name' => 'Lapangan Futsal & Badminton Indoor',
                'building' => 'Student Activity Center (SAC)',
                'location_detail' => 'Gelanggang Olahraga Kampus',
                'capacity' => 50,
                'description' => 'Lapangan indoor beralas vinyl standar internasional lengkap dengan lampu sorot LED, jaring pengaman, dan ruang ganti pemain.',
                'usage_rules' => '1. Wajib mengenakan sepatu olahraga non-marking.\n2. Menjaga kebersihan dan membuang botol air pada tempat sampah yang disediakan.',
                'open_time' => '07:00',
                'close_time' => '21:00',
                'max_duration_minutes' => 180, // 3 jam
                'is_active' => true,
                'photos' => [
                    'https://images.unsplash.com/photo-1574629810360-7efbbe195018?auto=format&fit=crop&w=1000&q=80',
                ],
            ],
        ];

        $createdFacilities = [];
        foreach ($facilitiesData as $item) {
            $photos = $item['photos'];
            unset($item['photos']);

            $facility = Facility::firstOrCreate(
                ['name' => $item['name']],
                $item
            );
            $createdFacilities[] = $facility;

            foreach ($photos as $idx => $photoUrl) {
                FacilityPhoto::firstOrCreate(
                    [
                        'facility_id' => $facility->id,
                        'file_path' => $photoUrl,
                    ],
                    [
                        'is_primary' => ($idx === 0),
                    ]
                );
            }
        }

        // 4. Sample Bookings
        $tomorrow = Carbon::now('Asia/Jakarta')->addDays(2)->format('Y-m-d');
        $nextWeek = Carbon::now('Asia/Jakarta')->addDays(5)->format('Y-m-d');

        // Booking 1: Disetujui
        $booking1 = Booking::firstOrCreate(
            [
                'facility_id' => $createdFacilities[0]->id,
                'booking_date' => $tomorrow,
                'start_time' => '09:00',
                'end_time' => '13:00',
            ],
            [
                'user_id' => $pemohon1->id,
                'event_name' => 'Seminar Nasional Technopreneurship 2026',
                'purpose' => 'Seminar tahunan BEM FIK mengundang pembicara industri teknologi dan pameran startup karya mahasiswa.',
                'participants' => 350,
                'contact_phone' => '082155554321',
                'status' => 'DISETUJUI',
                'decided_by' => $approver->id,
                'decided_at' => Carbon::now('Asia/Jakarta')->subHours(4),
                'decision_note' => 'Disetujui. Harap berkoordinasi dengan teknisi sound system H-1 acara.',
            ]
        );

        BookingStatusLog::firstOrCreate([
            'booking_id' => $booking1->id,
            'to_status' => 'DIAJUKAN',
            'changed_by' => $pemohon1->id,
            'note' => 'Pengajuan peminjaman fasilitas dibuat.',
            'created_at' => Carbon::now('Asia/Jakarta')->subHours(6),
        ]);

        BookingStatusLog::firstOrCreate([
            'booking_id' => $booking1->id,
            'from_status' => 'DIAJUKAN',
            'to_status' => 'DISETUJUI',
            'changed_by' => $approver->id,
            'note' => 'Disetujui. Harap berkoordinasi dengan teknisi sound system H-1 acara.',
            'created_at' => Carbon::now('Asia/Jakarta')->subHours(4),
        ]);

        // Booking 2: Diajukan (Menunggu persetujuan)
        $booking2 = Booking::firstOrCreate(
            [
                'facility_id' => $createdFacilities[1]->id,
                'booking_date' => $nextWeek,
                'start_time' => '10:00',
                'end_time' => '12:30',
            ],
            [
                'user_id' => $pemohon2->id,
                'event_name' => 'Workshop UI/UX Design with Figma',
                'purpose' => 'Pelatihan intensif desain antarmuka aplikasi mobile dan web untuk mahasiswa semester 3.',
                'participants' => 60,
                'contact_phone' => '085711223344',
                'status' => 'DIAJUKAN',
            ]
        );

        BookingStatusLog::firstOrCreate([
            'booking_id' => $booking2->id,
            'to_status' => 'DIAJUKAN',
            'changed_by' => $pemohon2->id,
            'note' => 'Pengajuan peminjaman fasilitas dibuat.',
            'created_at' => Carbon::now('Asia/Jakarta')->subHours(1),
        ]);

        // 5. Sample Blocked Slot
        $blockDate = Carbon::now('Asia/Jakarta')->addDays(3)->format('Y-m-d');
        BlockedSlot::firstOrCreate(
            [
                'facility_id' => $createdFacilities[3]->id,
                'block_date' => $blockDate,
                'start_time' => '08:00',
                'end_time' => '16:00',
            ],
            [
                'reason' => 'Maintenance berkala instalasi listrik dan update sistem operasi laboratorium.',
                'created_by' => $admin->id,
                'created_at' => Carbon::now('Asia/Jakarta'),
            ]
        );
    }
}
