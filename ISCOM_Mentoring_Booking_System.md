# ISCOM Mentoring Booking System

Sistem booking jadwal mentoring untuk program **ISCOM (Information System Community)**.

Aplikasi ini dibuat untuk memudahkan mahasiswa dalam melakukan pendaftaran mentoring, memilih jadwal yang tersedia, melakukan konfirmasi booking, serta melihat status apakah mereka masuk ke dalam daftar peserta mentoring.

---

## 🎯 Tujuan Sistem

Sistem ini memiliki tujuan utama untuk:

- Memudahkan mahasiswa melakukan booking mentoring.
- Menyediakan daftar jadwal mentoring yang tersedia.
- Memastikan mahasiswa dapat memilih jadwal yang sesuai.
- Menampilkan status booking mahasiswa.
- Menampilkan daftar mahasiswa yang berhasil masuk dalam list mentoring.

---

# 🔄 Alur Utama Sistem

```text
                    ┌─────────────────┐
                    │     BERANDA     │
                    │  Mentoring ISCOM │
                    └────────┬────────┘
                             │
                             ▼
                  ┌─────────────────────┐
                  │   BOOKING MENTORING │
                  └──────────┬──────────┘
                             │
                             ▼
                  ┌─────────────────────┐
                  │  1. ISI DATA DIRI   │
                  │                     │
                  │ Nama                │
                  │ NIM                 │
                  │ Program Studi       │
                  │ Semester            │
                  │ WhatsApp            │
                  │ Email               │
                  └──────────┬──────────┘
                             │
                             ▼
                  ┌─────────────────────┐
                  │  2. PILIH JADWAL    │
                  │                     │
                  │ Pilih jadwal yang   │
                  │ tersedia dan sesuai │
                  └──────────┬──────────┘
                             │
                             ▼
                  ┌─────────────────────┐
                  │   3. KONFIRMASI     │
                  │                     │
                  │ Cek data + jadwal   │
                  │                     │
                  │ [Konfirmasi Booking]│
                  └──────────┬──────────┘
                             │
                             ▼
                  ┌─────────────────────┐
                  │   BOOKING TERCATAT  │
                  └──────────┬──────────┘
                             │
                             ▼
                  ┌─────────────────────┐
                  │   4. CEK STATUS     │
                  └──────────┬──────────┘
                             │
                 ┌───────────┴───────────┐
                 ▼                       ▼
          ┌──────────────┐        ┌─────────────────┐
          │ MASUK LIST   │        │ TIDAK MASUK LIST│
          └──────┬───────┘        └────────┬────────┘
                 │                         │
                 ▼                         ▼
       ┌──────────────────┐       ┌──────────────────┐
       │ "Booking kamu    │       │ "Mohon maaf,     │
       │ diterima."       │       │ kamu belum masuk │
       └────────┬─────────┘       │ list mentoring   │
                │                 │ ISCOM di lab."   │
                ▼                 └──────────────────┘
       ┌──────────────────┐
       │ DAFTAR MENTORING │
       │                  │
       │ Nama             │
       │ NIM              │
       │ Program Studi    │
       │ Jadwal            │
       │ Status           │
       └──────────────────┘
```

---

# 📄 Struktur Halaman

## 1. Beranda

Halaman utama website Mentoring ISCOM.

### Isi:

- Logo ISCOM
- Navigasi
  - Beranda
  - Jadwal
  - Booking
  - Status
- Intro singkat tentang Mentoring ISCOM
- Tombol **Booking Mentoring**
- Tombol **Lihat Jadwal**
- Preview jadwal mentoring yang tersedia

### Tujuan:

Memberikan akses cepat kepada mahasiswa untuk melakukan booking atau melihat jadwal.

---

## 2. Booking Mentoring

Halaman utama untuk melakukan pendaftaran mentoring.

Proses booking terdiri dari **3 tahap**.

### Step 1 — Data Diri

Mahasiswa mengisi:

- Nama Lengkap
- NIM
- Program Studi
- Semester
- Nomor WhatsApp
- Email

Tombol:

- `Kembali`
- `Lanjutkan`

---

### Step 2 — Pilih Jadwal

Mahasiswa melihat jadwal mentoring yang tersedia.

Setiap jadwal menampilkan:

- Tanggal
- Hari
- Jam
- Mentor
- Lokasi
- Sisa slot
- Status ketersediaan

Contoh:

```text
Kamis, 24 Oktober
13.30 - 15.30 WIB

Mentor:
Kak Aditya W.

Lokasi:
Lab Komputer C301

Sisa:
4 slot

[Pilih Jadwal]
```

Mahasiswa memilih jadwal yang sesuai dengan waktu mereka.

---

### Step 3 — Konfirmasi

Sistem menampilkan kembali data yang telah diisi.

#### Data Diri

- Nama
- NIM
- Program Studi
- Semester
- WhatsApp
- Email

#### Jadwal

- Tanggal
- Jam
- Mentor
- Lokasi

Pertanyaan:

> **Pastikan data dan jadwalmu sudah benar.**

Tombol:

- `Kembali`
- `Konfirmasi Booking`

---

# ✅ 3. Booking Berhasil

Setelah mahasiswa melakukan konfirmasi, sistem mencatat booking.

Tampilkan:

> **Booking berhasil.**

> Bookingmu sudah tercatat. Cek kembali statusmu untuk melihat hasilnya.

Informasi:

- Nama
- Jadwal
- Mentor
- Lokasi
- Status

Status awal:

```text
Menunggu Konfirmasi
```

Tombol:

`Cek Status`

---

# 📊 4. Status Booking

Mahasiswa dapat melihat status booking mereka.

Terdapat beberapa kemungkinan status.

## Status: Pending

```text
🟠 Menunggu Konfirmasi

Booking kamu sudah tercatat dan sedang diproses.
```

---

## Status: Accepted

```text
🟢 Booking kamu diterima.
```

Tampilkan:

- Nama
- NIM
- Program Studi
- Tanggal
- Jam
- Mentor
- Lokasi

Tombol:

`Lihat Daftar Mentoring`

Mahasiswa yang memiliki status **Accepted** akan masuk ke daftar mentoring.

---

## Status: Rejected

```text
Mohon maaf, kamu belum masuk list mentoring ISCOM di lab.
```

Tombol:

`Cek Lagi`

Status ini tidak perlu menggunakan desain error yang agresif. Gunakan tampilan yang informatif dan tetap ramah.

---

# 👥 5. Daftar Mentoring

Halaman ini menampilkan mahasiswa yang telah masuk ke dalam list mentoring.

### Data yang ditampilkan:

| No | Nama | NIM | Program Studi | Jadwal | Status |
|---|---|---|---|---|---|
| 1 | Nama Mahasiswa | 2208100XXX | Sistem Informasi | 24 Okt, 13.30 | Accepted |
| 2 | Nama Mahasiswa | 2208100XXX | Informatika | 25 Okt, 09.00 | Accepted |

Pada perangkat mobile, tabel diubah menjadi **card list** agar mudah dibaca.

---

# 🗓️ Jadwal Mentoring

Halaman untuk melihat seluruh jadwal mentoring yang tersedia.

Setiap jadwal menampilkan:

- Tanggal
- Waktu
- Mentor
- Lokasi
- Sisa slot
- Status

Contoh status:

```text
🟢 Tersedia
🟠 Hampir Penuh
⚫ Penuh
```

Jika jadwal masih tersedia:

`Pilih Jadwal`

---

# 🗃️ Status Booking

Status booking memiliki alur:

```text
PENDING
   │
   ├───────────────┐
   ▼               ▼
ACCEPTED        REJECTED
   │               │
   ▼               ▼
Masuk List       Tidak Masuk List
Mentoring        Mentoring
```

### Status

| Status | Keterangan |
|---|---|
| `pending` | Booking sudah dibuat dan menunggu konfirmasi |
| `accepted` | Mahasiswa diterima dan masuk daftar mentoring |
| `rejected` | Mahasiswa tidak masuk daftar mentoring |

---

# 🗄️ Gambaran Database

Secara sederhana, sistem membutuhkan beberapa data utama:

```text
MAHASISWA
   │
   │
   ▼
BOOKING
   │
   ├──────────► JADWAL
   │
   ▼
STATUS BOOKING
   │
   ├── pending
   ├── accepted
   └── rejected
```

### Relasi utama

```text
Mahasiswa
    │
    │ 1 : N
    ▼
Booking
    │
    │ N : 1
    ▼
Jadwal
```

Satu mahasiswa dapat memiliki booking, sedangkan satu jadwal dapat memiliki beberapa booking.

---

# 🎨 Identitas Visual

Gunakan logo **ISCOM (Information System Community)** sebagai referensi utama.

### Warna

| Warna | Hex | Penggunaan |
|---|---|---|
| ISCOM Blue | `#1674B8` | Primary / navigasi / heading |
| ISCOM Orange | `#F4512A` | CTA / tombol utama |
| ISCOM Green | `#8BC34A` | Success / accepted / available |
| White | `#FFFFFF` | Background utama |
| Light Gray | `#F7F8FA` | Background section |

### Prinsip penggunaan warna

- **Blue** menjadi warna utama struktur interface.
- **Orange** digunakan untuk CTA penting.
- **Green** hanya digunakan sebagai aksen status berhasil/tersedia.
- Jangan menggunakan ketiga warna secara berlebihan dalam satu area.
- Hindari gradient besar.
- Gunakan whitespace yang cukup.

---

# 📱 Responsive Design

Website harus responsive pada:

- Desktop
- Tablet
- Mobile

### Desktop

Gunakan layout:

```text
Navbar
   ↓
Hero
   ↓
Jadwal
   ↓
Footer
```

Form dapat menggunakan dua kolom jika ruang mencukupi.

---

### Mobile

Gunakan layout satu kolom:

```text
Navbar
   ↓
Hero
   ↓
CTA
   ↓
Jadwal
   ↓
Footer
```

Form:

```text
Nama
[________________]

NIM
[________________]

Program Studi
[________________]

Semester
[________________]

WhatsApp
[________________]

Email
[________________]

[ Lanjutkan ]
```

### Ketentuan Mobile

- Tidak boleh ada horizontal overflow.
- Tombol harus mudah ditekan.
- Form menggunakan satu kolom.
- Schedule card ditampilkan secara vertikal.
- Tabel daftar mentoring berubah menjadi card.
- Navigasi menggunakan hamburger menu atau navigasi mobile.
- Progress booking tetap mudah dibaca.
- Jangan hanya mengecilkan layout desktop.

---

# 🧭 User Journey

Secara sederhana:

```text
Beranda
   │
   ▼
Booking Mentoring
   │
   ▼
Isi Data Diri
   │
   ▼
Pilih Jadwal
   │
   ▼
Konfirmasi
   │
   ▼
Booking Tercatat
   │
   ▼
Cek Status
   │
   ├───────────────┐
   ▼               ▼
Accepted         Rejected
   │               │
   ▼               ▼
Daftar           Belum Masuk
Mentoring        List
```

---

# 🎯 Prinsip Utama

> **Less is more.**

Website tidak perlu memiliki banyak fitur atau informasi tambahan.

Setiap elemen yang ditampilkan harus membantu mahasiswa melakukan salah satu dari hal berikut:

1. **Mengisi data**
2. **Memilih jadwal**
3. **Mengonfirmasi booking**
4. **Mengecek status**
5. **Melihat daftar mentoring**

Jika sebuah elemen tidak membantu kelima hal tersebut, pertimbangkan untuk menghapusnya.

---

# 🚀 MVP

Untuk versi pertama, fitur yang wajib dibuat:

- [ ] Beranda
- [ ] Daftar jadwal mentoring
- [ ] Form data diri
- [ ] Pemilihan jadwal
- [ ] Konfirmasi booking
- [ ] Penyimpanan booking
- [ ] Status booking
- [ ] Daftar mentoring
- [ ] Responsive mobile
- [ ] Database
- [ ] Integrasi frontend dengan backend
