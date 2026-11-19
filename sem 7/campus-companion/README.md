# Smart Campus Companion 🏫✨

> An intelligent, role-governed campus management, master scheduling, indoor spatial navigation, and grievance redressal ecosystem developed for **Vivekanand Education Society's Institute of Technology (VESIT)**.

---

## 🚀 Key Highlights & Capabilities

- 🗓️ **Intelligent Master Timetable & Conflict Engine**: Mathematical interval overlap detection ($\max(S_1, S_2) < \min(E_1, E_2)$) preventing classroom double-booking and teacher clashes. Includes bulk XLSX master grid parsing (`Shuchkin/SimpleXLSX`).
- 🔄 **Context-Aware Clash Recovery**: Faculty-facing clash reporter that detects room overlaps in real time and queries spatial floor blocks to suggest unoccupied rooms on the same floor/department.
- 📢 **Multi-Tier Notice Broadcast & Accountability Tracking**: College-wide, departmental, and class-division scoped announcements with unread tracking (`notice_reads`) and automated 48h escalation reminders (`reminders:send`).
- 🛠️ **Photographic Grievance Redressal**: Issue ticketing with photo proofs, automated routing to Department Heads (HOD), administrative assignment, and status updates (`pending` $\rightarrow$ `in_progress` $\rightarrow$ `resolved`).
- 🗺️ **Interactive 2D Campus & Multi-Floor Indoor Navigation**: SVG-based campus layout coupled with 2D blueprint grid layouts ($X, Y, W, H$) linked to active classrooms, live lecture badges, and OpenStreetMap toggle.
- 🎨 **Modern Design & Accessibility**: Reactive dark/light modes, glassmorphic UI, Syne & DM Sans typography, responsive layouts, and interactive canvas particle backgrounds.

---

## 🛠️ Tech Stack

| Layer | Technology |
|---|---|
| **Backend** | [Laravel 12](https://laravel.com) (PHP ^8.2) |
| **Reactivity** | [Livewire 3 (Volt)](https://livewire.laravel.com) & [Flux UI](https://fluxui.dev) |
| **Frontend Styling** | [Tailwind CSS v4](https://tailwindcss.com) & Alpine.js |
| **Database** | [PostgreSQL 16 (Neon Serverless)](https://neon.tech) / MySQL / SQLite |
| **Excel Parser** | `shuchkin/simplexlsx` |
| **Icons** | Font Awesome 6.5 & Flux Icons |
| **Testing** | PHPUnit 11 & Pest |

---

## 📋 Quick Start Guide

### 1. Prerequisites
- PHP `>= 8.2` (with `pdo`, `pdo_pgsql` or `pdo_sqlite`, `fileinfo`, `gd`, `zip` extensions)
- Composer `>= 2.5`
- Node.js `>= 20.x` & npm

### 2. Installation

```bash
# 1. Install PHP dependencies
composer install

# 2. Install Node dependencies
npm install

# 3. Environment configuration
cp .env.example .env
php artisan key:generate

# 4. Database configuration
# Update .env with your PostgreSQL / Neon or SQLite credentials

# 5. Run migrations & seed demo data
php artisan migrate --seed

# 6. Start development servers
composer run dev
```

*The `composer run dev` command starts the Laravel artisan server, queue listener, and Vite compiler simultaneously.*

---

## 🔑 Demo & Test Accounts

All pre-seeded demo accounts use the default password: **`password`**.  
For a full list of 314 student, teacher, and admin accounts across all departments, see [`users.md`](users.md).

| Role | Email | Password | Department / Scope |
|---|---|---|---|
| **Super Admin** | `admin@campus.com` | `password` | College-wide Full Access |
| **Timetable Committee** | `timetable@campus.com` | `password` | Master Timetable & Floor Allocations |
| **IT Teacher (HOD)** | `informationtechnology_teacher1@mail.com` | `password` | IT Department & Complaints |
| **CMPN Teacher** | `computerengineering_teacher1@mail.com` | `password` | Computer Engineering |
| **IT Student (SY A)** | `informationtechnology_sya_1@student.com` | `password` | Student Dashboard & Timetable |
| **CMPN Student (FY B)** | `computerengineering_fyb_1@student.com` | `password` | Scoped CMPN Portal |

---

## 👥 Authors & Team

Developed by **Final Year Project Group 15** at **VESIT (University of Mumbai)**:

- **Krish Bhojwani** — Lead Developer / Primary Author
- **Dev Chandiramani** — Co-Author
- **Sameer Jagiasi** — Co-Author
- **Jatin Lilani** — Co-Author

---

## 📄 Documentation & Black Book
- Detailed Technical Specification: [`detail-of-campus-companion.md`](detail-of-campus-companion.md)
- User Roster & Credentials: [`users.md`](users.md)
- License: [MIT License](LICENSE)
