# Project Overview: Vue.js + Laravel Integration

## Table of Contents
1. [Architecture Overview](#architecture-overview)
2. [Technology Stack](#technology-stack)
3. [Data Flow](#data-flow)
4. [Database Structure](#database-structure)
5. [Authentication Flow](#authentication-flow)
6. [Changes Made](#changes-made)
7. [API Endpoints](#api-endpoints)

---

## Architecture Overview

```
┌─────────────────────────────────────────────────────────────────┐
│                        FRONTEND (Vue 3)                         │
│  ┌─────────────┐  ┌─────────────┐  ┌─────────────────────────┐ │
│  │ LoginForm   │  │RegisterForm │  │    DashboardLayout      │ │
│  │   .vue      │  │   .vue      │  │       .vue              │ │
│  └──────┬──────┘  └──────┬──────┘  └───────────┬─────────────┘ │
│         │                │                     │               │
│         └────────┬───────┘                     │               │
│                  ▼                             ▼               │
│            ┌─────────────────────────────────────────┐          │
│            │          Pinia Store (auth.js)          │          │
│            │   - user state                          │          │
│            │   - token management                   │          │
│            │   - login/logout actions               │          │
│            └──────────────────┬──────────────────────┘          │
│                               │                                  │
│                               ▼                                  │
│            ┌─────────────────────────────────────────┐          │
│            │          API Service (api.js)          │          │
│            │   - Axios configured                   │          │
│            │   - Bearer token header                │          │
│            └──────────────────┬──────────────────────┘          │
└───────────────────────────────┼───────────────────────────────────┘
                                │ HTTP (JSON)
                                ▼
┌───────────────────────────────────────────────────────────────────┐
│                      BACKEND (Laravel 13)                        │
│  ┌─────────────────────────────────────────────────────────────┐ │
│  │                    API Routes (api.php)                      │ │
│  │   POST /api/register → AuthController@register              │ │
│  │   POST /api/login   → AuthController@login                  │ │
│  │   POST /api/logout  → AuthController@logout                  │ │
│  │   GET  /api/user    → Return authenticated user              │ │
│  │   GET/POST /api/notes → NoteController (resource)           │ │
│  └─────────────────────────────────────────────────────────────┘ │
│                               │                                   │
│                               ▼                                   │
│  ┌─────────────────────────────────────────────────────────────┐ │
│  │              Controllers (app/Http/Controllers)              │ │
│  │   - AuthController    (register, login, logout)              │ │
│  │   - NoteController   (CRUD operations)                       │ │
│  └─────────────────────────────────────────────────────────────┘ │
│                               │                                   │
│                               ▼                                   │
│  ┌─────────────────────────────────────────────────────────────┐ │
│  │                 Models (app/Models)                          │ │
│  │   - User    (HasApiTokens trait)                            │ │
│  │   - Note    (noteable relationship)                         │ │
│  └─────────────────────────────────────────────────────────────┘ │
│                               │                                   │
│                               ▼                                   │
│  ┌─────────────────────────────────────────────────────────────┐ │
│  │              Database (SQLite)                               │ │
│  │   - users table                                             │ │
│  │   - notes table                                             │ │
│  │   - personal_access_tokens table                            │ │
│  │   - cache table                                            │ │
│  │   - jobs table                                              │ │
│  └─────────────────────────────────────────────────────────────┘ │
└───────────────────────────────────────────────────────────────────┘
```

---

## Technology Stack

### Frontend
| Technology | Purpose |
|------------|---------|
| Vue 3 | UI Framework (Composition API) |
| Vite | Build tool & dev server |
| Pinia | State management |
| Axios | HTTP client |
| Vue Router | Client-side routing |

### Backend
| Technology | Purpose |
|------------|---------|
| Laravel 13 | PHP Framework |
| Laravel Sanctum | API Token authentication |
| SQLite | Database (file-based) |
| Eloquent ORM | Database abstraction |

---

## Data Flow

### Registration Flow
```
User fills RegisterForm
       │
       ▼
auth.store.register(name, email, password)
       │
       ▼
axios.post('/api/register', {name, email, password})
       │
       ▼
Laravel: AuthController@register
       │
       ▼
Validate request → Create User (Hash::make password)
       │
       ▼
Return JSON: { message: 'User registered successfully', user }
       │
       ▼
Vue: Store catches success → User can now login
```

### Login Flow
```
User fills LoginForm
       │
       ▼
auth.store.login(email, password)
       │
       ▼
axios.post('/api/login', {email, password})
       │
       ▼
Laravel: AuthController@login
       │
       ▼
Validate request → Find User → Hash::check(password)
       │
       ▼
$token = $user->createToken('api-token')->plainTextToken
       │
       ▼
Return JSON: { user, token }
       │
       ▼
Vue: Store saves token to localStorage
     Sets axios default header: Bearer {token}
     Sets isLoggedIn = true
```

### Authenticated Request Flow
```
Vue component needs protected data
       │
       ▼
axios.get('/api/notes', { headers: { Authorization: 'Bearer {token}' } })
       │
       ▼
Laravel Middleware: auth:sanctum
       │
       ▼
Validate token → Get user from token
       │
       ▼
Controller executes → Returns data
```

---

## Database Structure

### Database Location
```
backend/database/database.sqlite
```

> **Note:** This is a file-based SQLite database. No MySQL/PostgreSQL server required.

### Tables

#### 1. `users` table
| Column | Type | Description |
|--------|------|-------------|
| id | INTEGER | Primary key |
| name | TEXT | User's full name |
| email | TEXT | Unique email |
| password | TEXT | Hashed password (bcrypt) |
| email_verified_at | DATETIME | Email verification timestamp |
| created_at | DATETIME | Creation timestamp |
| updated_at | DATETIME | Update timestamp |

**Example Query:**
```sql
SELECT id, name, email, created_at FROM users;
```

#### 2. `notes` table
| Column | Type | Description |
|--------|------|-------------|
| id | INTEGER | Primary key |
| user_id | INTEGER | Foreign key to users |
| title | TEXT | Note title |
| content | TEXT | Note content |
| created_at | DATETIME | Creation timestamp |
| updated_at | DATETIME | Update timestamp |

**Relationship:** `Note belongsTo User`, `User hasMany Notes`

#### 3. `personal_access_tokens` table
| Column | Type | Description |
|--------|------|-------------|
| id | INTEGER | Primary key |
| tokenable_type | TEXT | Model class (App\Models\User) |
| tokenable_id | INTEGER | User ID |
| name | TEXT | Token name (e.g., 'api-token') |
| token | TEXT | Hashed token (64 chars) |
| abilities | TEXT | JSON array of permissions |
| last_used_at | DATETIME | Last token usage |
| expires_at | DATETIME | Token expiration |
| created_at | DATETIME | Creation timestamp |
| updated_at | DATETIME | Update timestamp |

---

## Authentication Flow

### How Token Authentication Works

1. **User Registration**
   - User submits name, email, password
   - Laravel hashes password using `Hash::make()`
   - User record created in `users` table

2. **User Login**
   - User submits email, password
   - Laravel finds user by email
   - Laravel verifies password using `Hash::check()`
   - Laravel creates API token using `createToken()`
   - Token stored in `personal_access_tokens` table
   - Token returned to frontend

3. **Subsequent Requests**
   - Vue sends request with `Authorization: Bearer {token}`
   - Laravel validates token from `personal_access_tokens`
   - If valid, request proceeds
   - If invalid/invalid, returns 401 Unauthorized

4. **Logout**
   - Laravel deletes the token from `personal_access_tokens`
   - Frontend removes token from localStorage

---

## Changes Made

### 1. Installed Laravel Sanctum
```bash
composer require laravel/sanctum
```
**Purpose:** Enable API token-based authentication

### 2. Updated User Model
**File:** `backend/app/Models/User.php`

```php
// Added import
use Laravel\Sanctum\HasApiTokens;

// Added trait to class
class User extends Authenticatable
{
    use HasFactory, Notifiable, HasApiTokens;  // ← Added HasApiTokens
}
```

**Purpose:** Enable `createToken()` method for generating API tokens

### 3. Created Personal Access Tokens Table
**File:** `backend/database/migrations/2026_04_28_184315_create_personal_access_tokens_table.php`

```php
Schema::create('personal_access_tokens', function (Blueprint $table) {
    $table->id();
    $table->morphs('tokenable');        // tokenable_id, tokenable_type
    $table->string('name');              // Token name
    $table->string('token', 64)->unique(); // Hashed token
    $table->text('abilities')->nullable(); // Permissions
    $table->timestamp('last_used_at')->nullable();
    $table->timestamp('expires_at')->nullable();
    $table->timestamps();
});
```

**Purpose:** Store API tokens for authenticated users

---

## API Endpoints

### Authentication
| Method | Endpoint | Description | Auth Required |
|--------|----------|-------------|---------------|
| POST | `/api/register` | Register new user | No |
| POST | `/api/login` | Login user | No |
| POST | `/api/logout` | Logout user | Yes |
| GET | `/api/user` | Get current user | Yes |

### Notes (Protected)
| Method | Endpoint | Description | Auth Required |
|--------|----------|-------------|---------------|
| GET | `/api/notes` | List all notes | Yes |
| POST | `/api/notes` | Create note | Yes |
| GET | `/api/notes/{id}` | Get single note | Yes |
| PUT | `/api/notes/{id}` | Update note | Yes |
| DELETE | `/api/notes/{id}` | Delete note | Yes |
| POST | `/api/notes/search` | Search notes | Yes |

---

## Environment Configuration

**File:** `backend/.env`

```env
# Database
DB_CONNECTION=sqlite
DB_DATABASE=database/database.sqlite

# App
APP_NAME=Laravel
APP_ENV=local
APP_DEBUG=true
APP_URL=http://localhost:8000

# Frontend (for CORS)
FRONTEND_URL=http://localhost:5173
```

---

## Summary

| Aspect | Details |
|--------|---------|
| **Database** | SQLite (`backend/database/database.sqlite`) |
| **Auth Type** | Laravel Sanctum (Token-based) |
| **User Storage** | `users` table |
| **Token Storage** | `personal_access_tokens` table |
| **Notes Storage** | `notes` table (linked to users) |
| **API Port** | `http://localhost:8000` |
| **Frontend Port** | `http://localhost:5173` |

---

*Document generated: April 29, 2026*