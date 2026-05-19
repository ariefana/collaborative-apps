# ColabDoc - Real-Time Collaborative Document Editor

ColabDoc adalah aplikasi editor dokumen kolaboratif mirip Google Docs yang dirancang khusus untuk memungkinkan beberapa pengguna (multi-user) menulis dan mengedit dokumen yang sama secara bersamaan (real-time). Proyek ini dibangun untuk memenuhi tugas kuliah.

## 🚀 Fitur Utama

* **Multi-User Real-Time Editing:** Sinkronisasi ketikan teks antar-pengguna secara instan tanpa tumpang tindih menggunakan engine **Tiptap Editor** dan protokol **Laravel Reverb WebSockets**.
* **Presence System (User Online):** Menampilkan daftar pengguna yang sedang aktif membuka dokumen secara *live* dalam bentuk *Avatar Stack* (inisial nama).
* **Auto-Save dengan Mekanisme Debounce:** Mengamankan data ketikan ke database MySQL secara otomatis 3 detik setelah pengguna berhenti mengetik untuk efisiensi resource server.
* **Version History & Restore:** Menyimpan snapshot setiap perubahan dokumen. Pengguna dapat melihat riwayat revisi dan mengembalikan (*restore*) isi dokumen ke versi sebelumnya.
* **Manajemen Dokumen (CRUD):** Fitur membuat dokumen baru serta menghapus dokumen yang dilengkapi pengamanan hak akses (hanya pemilik asli yang dapat menghapus dokumen).
* **Interface Minimalis:** Desain halaman utama (*landing page*) dan *dashboard* yang bersih, rapi, dan responsif menggunakan **Tailwind CSS**.

## 🛠️ Tech Stack

* **Backend:** Laravel 13 (PHP 8.3)
* **Frontend:** Vue.js 3 & Tailwind CSS
* **Bridge:** Inertia.js
* **WebSocket Server:** Laravel Reverb
* **Rich Text Editor Engine:** Tiptap Editor (ProseMirror)
* **Database:** MySQL
---

## 🚀 Cara Menjalankan Project

### 1. Clone Repository

```bash
git clone https://github.com/ariefana/collaborative-apps.git
cd tugas-crud-laravel12-arief
```

### 2. Install Dependency

```bash
composer install
```

### 3. Instalasi Dependensi Frontend (JavaScript)

```bash
npm install
```

### 4. Konfigurasi Environment File
Salin file .env.example menjadi .env:
```bash
cp .env.example .env
```
Buka file .env dan sesuaikan pengaturan database MySQL Anda:
```bash
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=collaborative_doc
DB_USERNAME=root
DB_PASSWORD=
```
Pastikan juga konfigurasi Laravel Reverb sudah terisi otomatis di bagian bawah file .env Anda setelah menjalankan perintah instalasi Reverb.

### 5. Generate Application Key & Jalankan Migration

```bash
php artisan key:generate
php artisan migrate
```

### 6. Menjalankan Aplikasi
Buka 3 tab terminal terpisah dan jalankan perintah berikut:

* Terminal 1 (Aplikasi Laravel)

    ```bash
    php artisan serve
    ```
* Terminal 2 (Compiler Frontend Vue)

    ```bash
    npm run dev
    ```
* Terminal 3 (WebSocket Server Reverb)

    ```bash
    php artisan reverb:start
    ```

### Akses aplikasi melalui browser di alamat http://127.0.0.1:8000.

---

## 📄 Lisensi

Project ini dibuat untuk keperluan pembelajaran.

---

## 👨‍💻 Author

**Nama :  Arief Mudasir**

NIM : 240180021

---

TERIMA KASIH SALAM SISTEM NFORMASI

