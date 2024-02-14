# Auth API Spec (api spesifikasi)

## Register User API spesifikasi

Endpoint :  POST /smkti/restApi-bookshelf-level2/api/auth/registrasi

Request Body :

```json
{
  "name": "Ronaldo",
  "email": "ronaldo@gmail.com",
  "password": "ronaldo"
}
```

Response Body Success(200) : ketika request body sesuai

```json
{
  "message": "Registrasi berhasil",
  "data": {
    "id": 1,
    "name": "Ronaldo",
    "email": "ronaldo@gmail.com",
    "file": null,
    "created_at": "2024-01-17 13:58:07",
    "updated_at": null,
    "deleted_at": null
  }
}
```

Response Body Error(400) : ketika request tidak sesuai

```json
{
  "message": "Data tidak lengkap harus diisi"
}
```

Response Body Error(500) : jika ada salah kode php atau salah proses koneksi ke database

```json
{
  "message": "SQLSTATE[HY000] [1049] Unknown database 'bookshelf-acak'"
}
```

## Login User API spesifikasi

Endpoint :  POST /smkti/restApi-bookshelf-level2/api/auth/login

Request Body :

```json
{
  "email": "ronaldo@gmail.com",
  "password": "ronaldo"
}
```

Response Body Success(200) :

```json
{
  "data": {
    "id": 1,
    "name": "Ronaldo",
    "email": "ronaldo@gmail.com",
    "file": null,
    "created_at": "2024-01-17 13:58:07",
    "updated_at": null,
    "deleted_at": null
  },
  "message": "Login berhasil",
  "token": "eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpc3MiOiJodHRwOi8veW91cmRvbWFpbi5jb20iLCJhdWQiOiJodHRwOi8veW91cmRvbWFpbi5jb20iLCJpYXQiOjE3MDU1MDEzNjMsImV4cCI6MTcwNTUwMTQ4MywidXNlcl9pZCI6MX0.khWRvPvQJhgpRuBW0KYAaScGgN-uoRly8_CnPL-WgEE"
}
```

Response Body Error(400) : ketika request body tidak sesuai

```json
{
  "message": "Data tidak lengkap harus diisi"
}
```

Response Body Error(400) : ketika salah memasukan email / password

```json
{
  "message": "login gagal, cek email dan password"
}
```

Response Body Error(500) : jika ada salah kode php atau salah proses koneksi ke database

```json
{
  "message": "SQLSTATE[HY000] [1049] Unknown database 'bookshelf-acak'"
}
```

## Get Current User API

Endpoint : GET /smkti/restApi-bookshelf-level2/api/auth/current

Headers :
- Authorization : Bearer {{token}}

Response Body Success(200): ketika token sesuai

```json
{
  "data": {
    "id": 1,
    "name": "Ronaldo",
    "email": "ronaldo@gmail.com",
    "file": null,
    "created_at": "2024-01-17 13:58:07",
    "updated_at": null,
    "deleted_at": null
  }
}
```

Response Body Error(400):

```json
{
  "message": "Token telah kedaluwarsa"
}
```

```json
{
    "message": "Token tidak valid"
}
```

```json
{
    "message": "Akses ditolak. Token tidak ditemukan."
}
```

```json
{
    "message": "users tidak ditemukan"
}
```

```json
{
    "message": "Token tidak valid: ...."
}
```