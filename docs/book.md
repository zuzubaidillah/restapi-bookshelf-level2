# Book API Spec (api spesifikasi)

## Book API spesifikasi

### BOOK mengambil semua data

Endpoint :  GET /smkti/restApi-bookshelf-level2/api/book

Bussiness Logic:
- client request params q
  - artinya client ingin menampilkan data berdasarkan kata kunci params q
  - lakukan pengambilan data berdasarkan kolom *title* LIKE %params.q%
- client request params status [all, sudah, belum]
  - all artinya menampilkan data secara keseluruhan
  - sudah artinya menampilkan data kolom *isComplete* = 1 (sudah dibaca)
  - belum artinya menampilkan data kolom *isComplete* = 0 (belum dibaca)
- dam menampilkan data buku berdasarkan *creator_id*
- dam menampilkan data buku berdasarkan *deleted_at IS NULL*
- response data

Headers :
- Authorization : Bearer {{token}}

Request params :

```text
status:all // isRequired [all, sudah, belum]
q:buku // optional | akan mencari judul buku
```

Response Body : GET 

```json
{
    "data": [
        {
            "id": 1147,
            "title": "Buku Akuntansi",
            "year": 2020,
            "author": "Hadi",
            "isComplete": 0,
            "file": "uploads/65cb758c638fb.pdf",
            "created_at": "2024-02-13 13:58:36",
            "updated_at": null,
            "deleted_at": null,
            "creator_id": 2,
            "updator_id": null,
            "creator_name": "jane marie"
        }
    ]
}
```

Response Body Error(400) : berikut yang akan tampil ada beberapa response

```json
{
  "message": "parmas status harus dikirim"
}
```

```json
{
  "message": "parmas status tidak sesuai. harus (all,belum,sudah)"
}
```

Response Body Error(500) : jika ada salah kode php atau salah proses koneksi ke database

```json
{
  "message": "SQLSTATE[HY000] [1049] Unknown database 'bookshelf-acak'"
}
```

### BOOK-menambahkan data

Endpoint :  POST /smkti/restApi-bookshelf-level2/api/book

Bussiness logic :
- verifikasi request body client
- cek title/judul yang sama
- response data yang baru saja disimpan

Headers :
- Authorization : Bearer {{token}}

Request Body : POST

```json
{
    "title": "Buku Akuntansi", // isRequired
    "year": 2021, // isRequired
    "author": "Hadi", // isRequired
    "file": (binary), // isRequired
    "isComplete": 0 // optional, if not request default value 0
}
```

Response Body Success(200) : ketika request body sesuai

```json
{
    "data": {
        "id": 1147,
        "title": "Buku Akuntansi",
        "year": 2020,
        "author": "Hadi",
        "isComplete": 0,
        "file": "uploads/65cb758c638fb.pdf",
        "created_at": "2024-02-13 13:58:36",
        "updated_at": null,
        "deleted_at": null,
        "creator_id": 2,
        "updator_id": null
    }
}
```

Response Body Error(400) : ketika request tidak sesuai

```json
{
    "errors": {
        "title": "Judul buku diperlukan.",
        "year": "Tahun harus berupa angka.",
        "author": "Nama penulis diperlukan.",
        "file": "File buku diperlukan.."
    },
    "error": "request tidak lengkap",
    "message": "request tidak lengkap"
}
```

Response Body Error(400) : ketika title sudah ada

```json
{
  "message": "Judul buku sudah ada. di buat oleh pengguna ZUZ"
}
```

Response Body Error(400) : ketika tipe file tidak sesuai

```json
{
    "message": "Tipe file tidak diperbolehkan. Hanya jpg, jpeg, png, dan pdf."
}
```

Response Body Error(400) : ketika ukuran file terlalu besar

```json
{
    "message": "Ukuran file terlalu besar. Maksimal 2MB."
}
```

Response Body Error(500) : jika ada salah kode php atau salah proses koneksi ke database

```json
{
  "message": "SQLSTATE[HY000] [1049] Unknown database 'bookshelf-acak'"
}
```

### BOOK-detail

Endpoint :  GET /smkti/restApi-bookshelf-level2/api/book{{book_id}}

Headers :
- Authorization : Bearer {{token}}

Request Body : GET

Response Body Success(200) : ketika request body sesuai

```json
{
    "data": {
        "id": 1147,
        "title": "Buku Akuntansi",
        "year": 2020,
        "author": "Hadi",
        "isComplete": 0,
        "file": "uploads/65cb758c638fb.pdf",
        "created_at": "2024-02-13 13:58:36",
        "updated_at": null,
        "deleted_at": null,
        "creator_id": 2,
        "updator_id": null,
        "creator_name": "jane marie"
    }
}
```

Response Body Error(400) : ketika parameter(params) {{book_id}} tidak ditemukan pada table book

```json
{
    "message": "Buku id 1147 tidak ditemukan"
}
```

Response Body Error(400) : ketika request tidak sesuai
```json
{
    "message": "Token telah kedaluwarsa"
}

{
    "message": "Token tidak valid"
}

{
    "message": "Akses ditolak. Token tidak ditemukan."
}

{
    "message": "users tidak ditemukan"
}

{
    "message": "Token tidak valid: $e->getMessage()"
}
```

Response Body Error(500) : jika ada salah kode php atau salah proses koneksi ke database

```json
{
  "message": "SQLSTATE[HY000] [1049] Unknown database 'bookshelf-acak'"
}
```

### BOOK-ubah data

Endpoint :  PUT /smkti/restApi-bookshelf-level2/api/book/{{book_id}}

Headers :
- Authorization : Bearer {{token}}

Request Body : PUT

```json
{
    "title": "Buku Akuntansi update", // isRequired
    "year": 2021, // isRequired
    "author": "Hadi update", // isRequired
    "isComplete": 0 // optional, if not request default value 0
}
```

Response Body Success(200) : ketika request body sesuai

```json
{
    "message": "Buku berhasil diubah",
    "data": {
        "id": 1147,
        "title": "Buku Akuntansi update",
        "year": 2021,
        "author": "Hadi update",
        "isComplete": 0,
        "file": "uploads/65cb758c638fb.pdf",
        "created_at": "2024-02-13 13:58:36",
        "updated_at": "2024-02-13 14:11:16",
        "deleted_at": null,
        "creator_id": 2,
        "updator_id": 2
    }
}
```

Response Body Error(400) : ketika request body tidak sesuai

```json
{
    "errors": {
        "title": "Judul buku diperlukan.",
        "year": "Tahun harus berupa angka.",
        "author": "Nama penulis diperlukan."
    },
    "error": "request tidak lengkap",
    "message": "request tidak lengkap"
}
```

Response Body Error(400) : ketika parameter(params) {{book_id}} tidak ditemukan pada table book

```json
{
    "message": "Buku id 1147 tidak ditemukan"
}
```

Response Body Error(400) : ketika request body title sudah ada di table book

```json
{
    "message": "Judul buku sudah ada. di buat oleh pengguna HADI"
}
```

Response Body Error(400) : ketika proses update data di table book bermasalah

```json
{
    "message": "Buku Gagal diubah"
}
```

Response Body Error(400) : ketika request header Token tidak sesuai

```json
{
    "message": "Token telah kedaluwarsa"
}

{
    "message": "Token tidak valid"
}

{
    "message": "Akses ditolak. Token tidak ditemukan."
}

{
    "message": "users tidak ditemukan"
}

{
    "message": "Token tidak valid: $e->getMessage()"
}
```

Response Body Error(500) : jika ada salah kode php atau salah proses koneksi ke database

```json
{
  "message": "SQLSTATE[HY000] [1049] Unknown database 'bookshelf-acak'"
}
```

### BOOK-membaca buku

Endpoint :  PUT /smkti/restApi-bookshelf-level2/api/book/{{book_id}}/read-book

Headers :
- Authorization : Bearer {{token}}

Request Body :

```json
{
    "isComplete": 1 // isRequired
}
```

Response Body Success(200) : ketika request body sesuai dan book_id sesuai dengan table book

```json
{
    "message": "Buku telah komplete dibaca",
    "data": {
        "id": 1147,
        "title": "Buku Akuntansi update",
        "year": 2021,
        "author": "Hadi update",
        "isComplete": 1,
        "file": "uploads/65cb758c638fb.pdf",
        "created_at": "2024-02-13 13:58:36",
        "updated_at": "2024-02-13 14:13:23",
        "deleted_at": null,
        "creator_id": 2,
        "updator_id": 2
    }
}
```

Response Body Error(400) : ketika request body tidak sesuai

```json
{
    "errors": {
        "isComplete": "komplete membaca diperlukan."
    },
    "error": "request tidak lengkap",
    "message": "request tidak lengkap"
}
```

Response Body Error(400) : ketika parameter(params) {{book_id}} tidak ditemukan pada table book

```json
{
    "message": "Buku id 1147 tidak ditemukan"
}
```

Response Body Error(400) : ketika proses update data di table book bermasalah

```json
{
    "message": "Buku Gagal diubah"
}
```

Response Body Error(400) : ketika request header Token tidak sesuai

```json
{
    "message": "Token telah kedaluwarsa"
}

{
    "message": "Token tidak valid"
}

{
    "message": "Akses ditolak. Token tidak ditemukan."
}

{
    "message": "users tidak ditemukan"
}

{
    "message": "Token tidak valid: $e->getMessage()"
}
```


Response Body Error(500) : jika ada salah kode php atau salah proses koneksi ke database

```json
{
  "message": "SQLSTATE[HY000] [1049] Unknown database 'bookshelf-acak'"
}
```

### BOOK-merubah file

Endpoint :  POST /smkti/restApi-bookshelf-level2/api/book/{{book_id}}/file

Headers :
- Authorization : Bearer {{token}}

Request Body : POST

```json
{
    "file": (binary)
}
```

Response Body Success(200) : ketika request body sesuai

```json
{
    "data": {
        "id": 1147,
        "title": "Buku Akuntansi update",
        "year": 2021,
        "author": "Hadi update",
        "isComplete": 1,
        "file": "uploads/65cb7a152d9da.pdf",
        "created_at": "2024-02-13 13:58:36",
        "updated_at": "2024-02-13 14:13:23",
        "deleted_at": null,
        "creator_id": 2,
        "updator_id": 2
    }
}
```

Response Body Error(400) : ketika request body tidak sesuai

```json
{
    "errors": {
        "file": "File harus diisi."
    },
    "error": "request tidak lengkap",
    "message": "request tidak lengkap"
}
```

Response Body Error(400) : ketika tipe file tidak sesuai

```json
{
    "message": "Tipe file tidak diperbolehkan. Hanya jpg, jpeg, png, dan pdf."
}
```

Response Body Error(400) : ketika ukuran file terlalu besar

```json
{
    "message": "Ukuran file terlalu besar. Maksimal 2MB."
}
```

Response Body Error(400) : ketika request tidak sesuai

```json
{
    "message": "Token telah kedaluwarsa"
}

{
    "message": "Token tidak valid"
}

{
    "message": "Akses ditolak. Token tidak ditemukan."
}

{
    "message": "users tidak ditemukan"
}

{
    "message": "Token tidak valid: $e->getMessage()"
}
```

Response Body Error(500) : jika ada salah kode php atau salah proses koneksi ke database

```json
{
  "message": "SQLSTATE[HY000] [1049] Unknown database 'bookshelf-acak'"
}
```

### BOOK-hapus data dan file

Endpoint :  DELETE /smkti/restApi-bookshelf-level2/api/book/{{book_id}}

Headers :
- Authorization : Bearer {{token}}

Response Body Success(200) : ketika request body sesuai

```json
{
    "message": "Buku berhasil dihapus "
}
```

Response Body Error(400) : ketika parameter(params) {{book_id}} tidak ditemukan pada table book

```json
{
    "message": "Buku id 1147 tidak ditemukan"
}
```

Response Body Error(400) : ketika proses hapus data di table book bermasalah

```json
{
    "message": "Buku Gagal dihapus"
}
```

Response Body Error(400) : ketika kesalahan token

```json
{
    "message": "Token telah kedaluwarsa"
}

{
    "message": "Token tidak valid"
}

{
    "message": "Akses ditolak. Token tidak ditemukan."
}

{
    "message": "users tidak ditemukan"
}

{
    "message": "Token tidak valid: $e->getMessage()"
}
```

Response Body Error(500) : jika ada salah kode php atau salah proses koneksi ke database

```json
{
  "message": "SQLSTATE[HY000] [1049] Unknown database 'bookshelf-acak'"
}
```
