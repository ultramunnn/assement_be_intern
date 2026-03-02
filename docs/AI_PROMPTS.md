# AI Prompt Log (Wajib)

Dokumen ini berisi seluruh prompt yang digunakan saat memakai bantuan Artificial Intelligence (AI) selama pengerjaan project ini.

---

## Prompt log

### 2026-03-02 — ChatGPT (share link)

**Link**

```
https://chatgpt.com/share/69a5b0c5-5f90-8006-af4f-415d70e925f4
```

**Prompt**

```
sekarang posisikan anda sebagai backend developer professional selama 5 tahun. bantu saya dalam mengerjakan assesment untuk magang ini. sekarang buat skema di db diagram dulu
```

**Output yang dipakai**

```
DESAIN RELASI DATABASE (Production Ready)

Berikut skema untuk kamu masukkan ke dbdiagram.io

📦 TABLE: tests
Table tests {
  id bigint [pk]
  title varchar
  description text
  duration_minutes int
  created_at timestamp
  updated_at timestamp
}
📦 TABLE: passages
Table passages {
  id bigint [pk]
  test_id bigint [ref: > tests.id]
  title varchar
  content text
  created_at timestamp
  updated_at timestamp
}

Relasi:

1 Test → banyak Passage

📦 TABLE: questions
Table questions {
  id bigint [pk]
  passage_id bigint [ref: > passages.id]
  question_text text
  question_type varchar
  correct_answer varchar
  score int
  created_at timestamp
  updated_at timestamp
}

💡 question_type contoh:

multiple_choice

true_false

short_answer

📦 TABLE: options
Table options {
  id bigint [pk]
  question_id bigint [ref: > questions.id]
  option_label varchar
  option_text text
  is_correct boolean
  created_at timestamp
  updated_at timestamp
}

Relasi:

1 Question → banyak Options

📦 TABLE: users (Laravel default)
Table users {
  id bigint [pk]
  name varchar
  email varchar
  password varchar
  created_at timestamp
  updated_at timestamp
}

Role bisa:

admin

user

📦 TABLE: user_attempts
Table user_attempts {
  id bigint [pk]
  user_id bigint [ref: > users.id]
  test_id bigint [ref: > tests.id]
  total_score int
  started_at timestamp
  finished_at timestamp
  created_at timestamp
  updated_at timestamp
}

Relasi:

1 User → banyak attempt

1 Test → banyak attempt

📦 TABLE: user_answers
Table user_answers {
  id bigint [pk]
  attempt_id bigint [ref: > user_attempts.id]
  question_id bigint [ref: > questions.id]
  selected_option_id bigint [ref: > options.id]
  is_correct boolean
  created_at timestamp
  updated_at timestamp
}

Relasi:

1 Attempt → banyak UserAnswer

1 Question → banyak UserAnswer

🔥 RELATION FLOW (SIMPEL)
users
   ↓
user_attempts
   ↓
user_answers
   ↓
questions
   ↓
options
🎯 Kenapa Struktur Ini Profesional?

Karena:

✅ Bisa banyak test
✅ Bisa banyak user
✅ Bisa simpan histori attempt
✅ Bisa analisis soal mana paling sering salah
✅ Bisa tambah fitur analytics nanti
✅ Siap untuk RBAC
```

**Prompt**

```
- rekomendasi stack apa yang perlu saya install di libary laravel
- sekarang lanjut ke migrations
- lanjut ke model
- sekarang buat service service nya menggunakkan api sanctum dan rbac spatie
- sekarang lanjut ke controller
- sekarang testing api di postman
- lanjut test postman seluruh endpoint nya admin dan user controllernya sudah lengkap test,passage, question, user answer, user attempt
```

**Output yang dipakai**

- Implementasi migrations, model, service, controller testing endpoint REST API (tests/passages/questions/options/attempts/answers)
- rekomendasi stack laravel
-  routing api.php
- Panduan testing endpoint via Postman


Dan tambahan untuk membantu error saya menggunakkan tools  
- Tool: OpenAI Codex CLI (atau “ChatGPT via Codex CLI”) 
dan masalahnya terminalnya ke close jadi promt nya sudah hilang semua