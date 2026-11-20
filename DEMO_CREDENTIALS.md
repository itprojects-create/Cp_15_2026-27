# 🔑 Campus Companion — Master Demo Credentials File

> **System URL:** `http://localhost:8000/login`  
> **Global Default Password:** `password` (Identical for ALL 314 accounts)  
> **Project:** VESIT Capstone Project (Group 15) — Smart Campus Companion

---

## 🎯 Top Demo Accounts (Quick Copy-Paste)

Use these 4 primary accounts to demonstrate all distinct role views to evaluators:

| Persona / Role | Full Name | Email Address | Password | Department / Group | Features to Demonstrate |
| :--- | :--- | :--- | :---: | :--- | :--- |
| **Super Admin** | Admin | `admin@campus.com` | `password` | Institution Admin | Master Dashboard, User CRUD, Department & Class Management, Global Grievance Oversight, Floor/Map Editor |
| **Timetable Committee** | Timetable Manager | `timetable@campus.com` | `password` | Institutional Scheduler | Master Timetable Matrix, Bulk Excel Importer, Room Allocation & Conflict Engine |
| **Faculty / HOD** | IT Teacher 1 | `informationtechnology_teacher1@mail.com` | `password` | Information Technology | Faculty Weekly Schedule, Live Clash Detection & Spatial Room Suggestions, Notice Read Receipts, Grievance Resolution |
| **Student** | SYA Student 1 | `informationtechnology_sya_1@student.com` | `password` | IT (SY Div A) | Student Timetable, Architectural 2D Floor Blueprint & Leaflet GIS Map, Photographic Grievance Filing, Scoped Notices |

---

## 🏛️ Administrators & Schedulers (3 Accounts)

| User ID | Account Name | Role Slug | Email Address | Password |
| :---: | :--- | :---: | :--- | :---: |
| **1** | Admin | `admin` | `admin@campus.com` | `password` |
| **313** | Timetable Committee | `timetable_commity` | `committee@campus.com` | `password` |
| **314** | Timetable Manager | `timetable_commity` | `timetable@campus.com` | `password` |

---

## 👨‍🏫 Department Faculty & HODs (11 Accounts)

| User ID | Faculty Name | Department | Email Address | Password |
| :---: | :--- | :--- | :--- | :---: |
| **64** | Information Technology Teacher 1 | Information Technology | `informationtechnology_teacher1@mail.com` | `password` |
| **65** | Information Technology Teacher 2 | Information Technology | `informationtechnology_teacher2@mail.com` | `password` |
| **2** | Computer Engineering Teacher 1 | Computer Engineering | `computerengineering_teacher1@mail.com` | `password` |
| **3** | Computer Engineering Teacher 2 | Computer Engineering | `computerengineering_teacher2@mail.com` | `password` |
| **188** | Artificial Intelligence Teacher 1 | Artificial Intelligence | `artificialintelligence_teacher1@mail.com` | `password` |
| **189** | Artificial Intelligence Teacher 2 | Artificial Intelligence | `artificialintelligence_teacher2@mail.com` | `password` |
| **126** | Electronics Teacher 1 | Electronics | `electronics_teacher1@mail.com` | `password` |
| **127** | Electronics Teacher 2 | Electronics | `electronics_teacher2@mail.com` | `password` |
| **250** | Robotics Teacher 1 | Robotics | `robotics_teacher1@mail.com` | `password` |
| **251** | Robotics Teacher 2 | Robotics | `robotics_teacher2@mail.com` | `password` |
| **315** | krish | Electronics | `k@gmail.com` | `password` |

---

## 🎓 Student Accounts by Department (300 Accounts Total)

> **Naming Rule:** `<department>_<year_division>_<number>@student.com`  
> **Password for all:** `password`

### 1. Information Technology (IT)
- **SY Division A (Recommended for Student Demo):**
  - `informationtechnology_sya_1@student.com` (User ID: 86)
  - `informationtechnology_sya_2@student.com`
- **FY Division A:** `informationtechnology_fya_1@student.com` to `10`
- **FY Division B:** `informationtechnology_fyb_1@student.com` to `10`
- **SY Division B:** `informationtechnology_syb_1@student.com` to `10`
- **TY Division A:** `informationtechnology_tya_1@student.com` to `10`
- **TY Division B:** `informationtechnology_tyb_1@student.com` to `10`

### 2. Computer Engineering (CMPN)
- **FY Division B:** `computerengineering_fyb_1@student.com` (User ID: 14)
- **SY Division A:** `computerengineering_sya_1@student.com` (User ID: 24)
- **TY Division A:** `computerengineering_tya_1@student.com` (User ID: 44)
- **TY Division B:** `computerengineering_tyb_1@student.com` (User ID: 54)

### 3. Artificial Intelligence (AI & DS)
- **FY Division A:** `artificialintelligence_fya_1@student.com` (User ID: 190)
- **SY Division A:** `artificialintelligence_sya_1@student.com` (User ID: 210)
- **TY Division A:** `artificialintelligence_tya_1@student.com` (User ID: 230)

### 4. Electronics Engineering (ETRX)
- **FY Division A:** `electronics_fya_1@student.com` (User ID: 128)
- **SY Division A:** `electronics_sya_1@student.com` (User ID: 148)
- **TY Division A:** `electronics_tya_1@student.com` (User ID: 168)

### 5. Robotics & Automation
- **FY Division A:** `robotics_fya_1@student.com` (User ID: 252)
- **SY Division A:** `robotics_sya_1@student.com` (User ID: 272)
- **TY Division A:** `robotics_tya_1@student.com` (User ID: 292)

---

## ⚡ Emergency Terminal Commands

```bash
# Start backend local server
cd "sem 7/campus-companion"
php artisan serve

# Run test suite to verify 0 errors
php artisan test

# Trigger midnight unread notice reminder notifications
php artisan reminders:send
```
