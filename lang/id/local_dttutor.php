<?php
// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

/**
 * Indonesian language strings for Tutor-IA plugin.
 *
 * @package    local_dttutor
 * @copyright  2025 Datacurso
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

$string['avatar'] = 'Avatar Tutor-IA';
$string['avatar_desc'] = 'Pilih avatar yang akan ditampilkan pada tombol chat mengambang Tutor-IA. Jika tidak ada yang dipilih atau file tidak ada, Avatar 1 akan digunakan secara default.';
$string['avatar_position'] = 'Posisi avatar';
$string['avatar_position_desc'] = 'Konfigurasikan di mana tombol avatar mengambang Tutor-IA akan ditampilkan. Pilih posisi sudut yang telah ditentukan atau sesuaikan koordinat X,Y yang tepat. Pratinjau langsung menunjukkan bagaimana tampilannya.';
$string['cachedef_sessions'] = 'Cache untuk sesi chat Tutor-IA';
$string['close'] = 'Tutup Tutor IA';
$string['custom_prompt'] = 'Prompt khusus';
$string['custom_prompt_desc'] = 'Instruksi khusus untuk mengontrol perilaku tutor AI. Gunakan bidang ini untuk memberikan pedoman spesifik, nada, atau batasan pengetahuan untuk tutor.';
$string['customavatar'] = 'Avatar khusus';
$string['customavatar_desc'] = 'Unggah gambar avatar khusus Anda sendiri. Ini akan menggantikan avatar yang telah dipilih sebelumnya.';
$string['customavatar_dimensions'] = 'Dimensi yang direkomendasikan: 200x200 piksel. Format yang didukung: PNG, JPG, JPEG, SVG. Ukuran file maksimum: 512KB.';
$string['drawer_side'] = 'Sisi pembukaan laci';
$string['drawer_side_help'] = 'Pilih dari sisi mana laci chat akan dibuka. Ini independen dari posisi tombol avatar.';
$string['drawer_side_left'] = 'Buka dari kiri';
$string['drawer_side_right'] = 'Buka dari kanan';
$string['dttutor:use'] = 'Gunakan Tutor-IA';
$string['enabled'] = 'Aktifkan Chat';
$string['enabled_desc'] = 'Aktifkan atau nonaktifkan chat Tutor-IA secara global';
$string['error_api_not_configured'] = 'Konfigurasi API tidak ada. Silakan periksa pengaturan Anda.';
$string['error_api_request_failed'] = 'Kesalahan permintaan API: {$a}';
$string['error_cache_unavailable'] = 'Layanan chat sementara tidak tersedia. Silakan coba refresh halaman.';
$string['error_http_code'] = 'Kesalahan HTTP {$a}';
$string['error_invalid_api_response'] = 'Respons API tidak valid';
$string['error_invalid_coordinates'] = 'Koordinat tidak valid. Silakan gunakan nilai CSS yang valid (mis: 10px, 2rem, 50%)';
$string['error_invalid_position'] = 'Data posisi tidak valid';
$string['offtopic_detection_enabled'] = 'Aktifkan deteksi topik di luar konteks';
$string['offtopic_detection_enabled_desc'] = 'Saat diaktifkan, tutor AI akan mendeteksi dan merespons pesan di luar konteks sesuai dengan tingkat keketatan yang dikonfigurasi di bawah.';
$string['offtopic_strictness'] = 'Keketatan deteksi di luar konteks';
$string['offtopic_strictness_desc'] = 'Kontrol seberapa ketat deteksi topik di luar konteks. Permisif memungkinkan lebih banyak fleksibilitas, sedangkan ketat memberlakukan percakapan yang hanya terkait dengan kursus.';
$string['offtopic_strictness_moderate'] = 'Moderat';
$string['offtopic_strictness_permissive'] = 'Permisif';
$string['offtopic_strictness_strict'] = 'Ketat';
$string['open'] = 'Buka Tutor IA';
$string['pluginname'] = 'Tutor IA';
$string['position_custom'] = 'Posisi khusus';
$string['position_left'] = 'Sudut kiri bawah';
$string['position_preset'] = 'Posisi yang telah ditentukan';
$string['position_right'] = 'Sudut kanan bawah';
$string['position_x'] = 'Posisi horizontal (X)';
$string['position_x_help'] = 'Jarak dari tepi kiri. Contoh: 2rem, 20px, 5%. Gunakan nilai negatif untuk memposisikan dari tepi kanan.';
$string['position_y'] = 'Posisi vertikal (Y)';
$string['position_y_help'] = 'Jarak dari tepi bawah. Contoh: 6rem, 80px, 10%. Gunakan nilai negatif untuk memposisikan dari tepi atas.';
$string['preview'] = 'Pratinjau Langsung';
$string["ref_bottom"] = "Bawah";
$string["ref_left"] = "Kiri";
$string["ref_right"] = "Kanan";
$string["ref_top"] = "Atas";
$string["reference_edge_x"] = "Tepi referensi horizontal";
$string["reference_edge_y"] = "Tepi referensi vertikal";
$string['sendmessage'] = 'Kirim pesan';
$string['sessionnotready'] = 'Sesi Tutor-IA belum siap. Silakan coba lagi.';
$string['student'] = 'Siswa';
$string['teacher'] = 'Guru';
$string['tutorcustomization'] = 'Kustomisasi Tutor';
$string['tutorname_default'] = 'Tutor AI';
$string['tutorname_setting'] = 'Nama tutor';
$string['tutorname_setting_desc'] = 'Konfigurasikan nama yang akan ditampilkan di header chat. Anda dapat menggunakan {teachername} untuk menampilkan nama guru sebenarnya dari kursus, atau memasukkan nama khusus. Contoh: "{teachername}" akan menampilkan "Budi Santoso", "Asisten AI" akan menampilkan "Asisten AI".';
$string['typemessage'] = 'Ketik pesan Anda...';
$string['unauthorized'] = 'Akses tidak sah';
$string['welcomemessage'] = 'Halo! Saya adalah asisten AI Anda. Bagaimana saya dapat membantu Anda hari ini?';
$string['welcomemessage_default'] = 'Halo! Saya {teachername}, asisten AI Anda. Bagaimana saya dapat membantu Anda hari ini?';
$string['welcomemessage_setting'] = 'Pesan selamat datang';
$string['welcomemessage_setting_desc'] = 'Sesuaikan pesan selamat datang yang ditampilkan saat membuka chat. Anda dapat menggunakan placeholder: {teachername}, {coursename}, {username}, {firstname}';
