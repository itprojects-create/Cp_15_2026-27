# VESIT Final Year Capstone Project — Smart Campus Companion 🎓

> **Group No:** 15  
> **Institution:** Vivekanand Education Society's Institute of Technology (VESIT), Chembur, Mumbai  
> **Affiliation:** University of Mumbai  
> **Academic Years:** Semester 6 & Semester 7  

---

## 👥 Project Team Members

- **Krish Bhojwani** (Lead Developer / Primary Author)
- **Dev Chandiramani** (Co-Author)
- **Sameer Jagiasi** (Co-Author)
- **Jatin Lilani** (Co-Author)

---

## 📂 Repository Structure

```plaintext
.
├── sem 6/
│   ├── Synopsis GRP 15.pdf                       # Project Synopsis (Problem statement, objectives, scope)
│   ├── SmartCampusCompanion_Updated v3.pdf        # Sem 6 Progress Report & Architectural Draft
│   └── Capstone Project LogBook by GRP 15.pdf    # Sem 6 Project Logbook
│
└── sem 7/
    ├── SmartCampusCompanion.BLACKBOOK.pdf        # Official Final Year Dissertation / Black Book
    ├── Research Paper (1).pdf                    # Published / Submitted Research Paper
    ├── Capstone Project LogBook by GRP 15.pdf    # Sem 7 Project Logbook
    └── campus-companion/                         # Production Web Application Source Code
        ├── app/                                  # Laravel Models, Services, Controllers, Console Commands
        ├── resources/views/livewire/             # Livewire Volt Single-File Reactive UI Components
        ├── database/                             # Migrations, Seeders & Factory Definitions
        ├── config/                               # Application configurations
        ├── routes/                               # Route definitions (web.php, auth.php, console.php)
        ├── tests/Feature/                        # Comprehensive automated test suite
        ├── public/screenshots/                   # UI previews and system screenshots
        ├── README.md                             # Application setup and developer documentation
        ├── detail-of-campus-companion.md         # In-depth technical specification and flowcharts
        └── users.md                              # Pre-seeded test accounts and login credentials
```

---

## 💻 About the Project: Smart Campus Companion

**Smart Campus Companion** is an enterprise campus management, master scheduling, indoor spatial navigation, and grievance redressal platform engineered using **Laravel 12**, **Livewire 3 (Volt)**, **Tailwind CSS v4**, **Alpine.js**, and **PostgreSQL (Neon)**.

### Core Modules:
1. **Intelligent Master Timetable Engine**: Algorithmic clash detection ($\max(S_1, S_2) < \min(E_1, E_2)$) and bulk multi-sheet Excel ingestion.
2. **Context-Aware Clash Recovery**: Real-time room conflict detection with spatial alternative classroom recommendations.
3. **Multi-Tier Notice Broadcast & Accountability Tracking**: Granular notice scoping with student read receipts and automatic 48-hour reminder dispatchers.
4. **Photographic Grievance Redressal**: Student complaint ticketing with photo proof, automatic HOD alerting, and technician assignment.
5. **Interactive 2D Campus & Multi-Floor Spatial Navigation**: Scalable 2D grid coordinates ($X, Y, W, H$) linked to active classrooms with live lecture status badges.

For detailed setup instructions, please refer to [`sem 7/campus-companion/README.md`](sem%207/campus-companion/README.md).
