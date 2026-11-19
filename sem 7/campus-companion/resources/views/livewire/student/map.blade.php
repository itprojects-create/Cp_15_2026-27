<?php

use Livewire\Component;
use Livewire\Attributes\Layout;

return new #[Layout('components.layouts.student')] class extends Component {
    // Mode switcher: 'blueprint' (Image 2 Architectural Blueprint) or 'leaflet' (OpenStreetMap)
    public string $viewMode = 'blueprint';

    // Floor Level for blueprint: 0 = Ground, 1 = 1st Floor (IT & CMPN - Image 2), 2 = 2nd Floor, 3 = 3rd Floor
    public int $selectedLevel = 1;

    // Search and Category filters
    public string $search = '';
    public string $selectedCategory = 'all';

    // Selected Room ID (default to cr-102)
    public ?string $selectedRoomId = 'cr-102';

    // Detailed room registry matching Image 2 Blueprint
    public array $blueprintRooms = [
        'cr-101' => [
            'id' => 'cr-101',
            'name' => 'Smart Lecture Hall CR-101',
            'code' => 'CR-101',
            'category' => 'classroom',
            'category_label' => 'Lecture Hall',
            'level' => 1,
            'wing' => 'West Wing (1st Floor)',
            'capacity' => '75 Students',
            'department' => 'Information Technology',
            'current_lecture' => 'IT SY Div A - Data Structures & Algorithms',
            'instructor' => 'Prof. V. Sharma',
            'timing' => '09:30 AM - 10:30 AM',
            'status' => 'Lecture Active',
            'status_color' => 'emerald',
            'description' => 'Tiered acoustic smart classroom equipped with dual 4K laser projection, automated lecture capture cameras, and collaborative perimeter workstations.',
            'facilities' => ['Dual 4K Laser Displays', 'Lecture Capture System', 'Acoustical Baffles', 'Perimeter Workstations', 'WiFi 6E High-Speed'],
        ],
        'cr-102' => [
            'id' => 'cr-102',
            'name' => 'Interactive Seminar Room CR-102',
            'code' => 'CR-102',
            'category' => 'classroom',
            'category_label' => 'Seminar Room',
            'level' => 1,
            'wing' => 'West Wing (1st Floor)',
            'capacity' => '65 Students',
            'department' => 'Computer Engineering',
            'current_lecture' => 'CMPN TY Div B - Machine Learning & Neural Networks',
            'instructor' => 'Dr. R. Patil',
            'timing' => '10:30 AM - 11:30 AM',
            'status' => 'Lecture Active',
            'status_color' => 'emerald',
            'description' => 'Collaborative seminar room featuring an interactive digital whiteboard, central island conference modules, and HD video-telephony arrays.',
            'facilities' => ['Interactive Touch Whiteboard', 'Island Group Tables', 'HD Polycom Video System', 'Smart Mics', 'Air Conditioned'],
        ],
        'cr-103' => [
            'id' => 'cr-103',
            'name' => 'Robotics & Embedded Studio CR-103',
            'code' => 'CR-103',
            'category' => 'classroom',
            'category_label' => 'Studio Lab',
            'level' => 1,
            'wing' => 'West Wing (1st Floor)',
            'capacity' => '60 Students',
            'department' => 'Information Technology',
            'current_lecture' => 'IT Final Year - Capstone Project Review',
            'instructor' => 'Dr. M. Kulkarni',
            'timing' => '11:30 AM - 12:30 PM',
            'status' => 'Project Review',
            'status_color' => 'amber',
            'description' => 'Hands-on embedded systems studio equipped with Raspberry Pi 5 benches, FPGA development boards, digital oscilloscopes, and IoT prototyping kits.',
            'facilities' => ['Raspberry Pi 5 Testbeds', 'Digital Storage Oscilloscopes', 'FPGA Kits', 'Hardware Storage Pod', 'Soldering Station'],
        ],
        'faculty-office' => [
            'id' => 'faculty-office',
            'name' => 'Faculty Cabins & HOD Office',
            'code' => 'FAC-104',
            'category' => 'office',
            'category_label' => 'Faculty Wing',
            'level' => 1,
            'wing' => 'Central Wing (1st Floor)',
            'capacity' => '18 Faculty Cabins',
            'department' => 'Information Technology & CMPN',
            'current_lecture' => 'Student Consultation & Doubt Clearing Hours',
            'instructor' => 'Department Faculty Members',
            'timing' => '10:00 AM - 04:30 PM',
            'status' => 'Faculty Available',
            'status_color' => 'emerald',
            'description' => 'Quiet faculty office complex containing partitioned consultation cabins, academic advisory desks, and student doubt resolution stations.',
            'facilities' => ['Faculty Consultation Cabins', 'Department Notice Board', 'HOD Conference Lounge', 'Student Queue Terminal', 'High-Speed LAN'],
        ],
        'staff-cabins' => [
            'id' => 'staff-cabins',
            'name' => 'Research Scholars & Server Room',
            'code' => 'SRV-105',
            'category' => 'office',
            'category_label' => 'Server & Research',
            'level' => 1,
            'wing' => 'Central Wing (1st Floor)',
            'capacity' => '12 Research Scholars',
            'department' => 'Information Technology',
            'current_lecture' => 'Network Operations & Data Center Monitoring',
            'instructor' => 'Systems Administrator',
            'timing' => '24/7 Monitored',
            'status' => 'Operational',
            'status_color' => 'emerald',
            'description' => 'Secure facility housing the department blade servers, fiber distribution panels, and dedicated cubicles for postgraduate research fellows.',
            'facilities' => ['Campus Blade Servers', '10Gbps Fiber Backbone', 'Precision AC Cooling', 'Biometric Access Control', 'UPS Backup'],
        ],
        'restrooms' => [
            'id' => 'restrooms',
            'name' => 'Department Restroom Facilities',
            'code' => 'WC-106',
            'category' => 'utilities',
            'category_label' => 'Amenities',
            'level' => 1,
            'wing' => 'Central Wing (1st Floor)',
            'capacity' => 'Executive Multi-Stall',
            'department' => 'Campus Infrastructure',
            'current_lecture' => 'Maintained & Sanitized by Campus Facilities',
            'instructor' => 'Facilities Supervisor',
            'timing' => '07:00 AM - 08:00 PM',
            'status' => 'Open & Cleaned',
            'status_color' => 'emerald',
            'description' => 'Hygienic male and female restroom suites featuring automated infrared sensor faucets, soap dispensers, and accessible mobility cubicles.',
            'facilities' => ['Infrared Sensor Taps', 'Hand Dryers', 'Accessible Cubicles', 'Exhaust Ventilation', 'Sanitized Daily'],
        ],
        'conf-hall' => [
            'id' => 'conf-hall',
            'name' => 'Departmental Conference Hall',
            'code' => 'CONF-107',
            'category' => 'classroom',
            'category_label' => 'Conference Hall',
            'level' => 1,
            'wing' => 'Central South (1st Floor)',
            'capacity' => '45 Executive Seats',
            'department' => 'Information Technology',
            'current_lecture' => 'Academic Advisory Board & Syllabus Review',
            'instructor' => 'Dean of Academics',
            'timing' => '02:00 PM - 03:30 PM',
            'status' => 'Scheduled Session',
            'status_color' => 'amber',
            'description' => 'Executive boardroom style conference suite with central audiovisual control, interactive teleconferencing, and presidential dais seating.',
            'facilities' => ['Dual Presentation Displays', 'Acoustical Wall Paneling', 'Executive Ergonomic Chairs', 'Podium Microphone Array', 'WiFi 6E'],
        ],
        'comp-lab-1' => [
            'id' => 'comp-lab-1',
            'name' => 'Advanced Programming Lab 1',
            'code' => 'LAB-108',
            'category' => 'lab',
            'category_label' => 'Computer Lab',
            'level' => 1,
            'wing' => 'East Wing (1st Floor)',
            'capacity' => '40 Workstations',
            'department' => 'Computer Engineering',
            'current_lecture' => 'SE CMPN Div A - Object Oriented Programming Lab',
            'instructor' => 'Prof. A. Nair',
            'timing' => '08:30 AM - 10:30 AM',
            'status' => 'Lab Active',
            'status_color' => 'emerald',
            'description' => 'High-throughput software laboratory with 40 dual-boot workstations, Ubuntu Linux / Windows 11, local Git repository server, and automated code runner.',
            'facilities' => ['40x Core i7 Workstations', 'Dual-Boot OS', 'Local Gitea Server', 'Overhead Projector', 'Central Gigabit Switch'],
        ],
        'comp-lab-2' => [
            'id' => 'comp-lab-2',
            'name' => 'AI & Deep Learning Lab 2',
            'code' => 'LAB-109',
            'category' => 'lab',
            'category_label' => 'Computer Lab',
            'level' => 1,
            'wing' => 'East Wing (1st Floor)',
            'capacity' => '40 GPU Nodes',
            'department' => 'Information Technology',
            'current_lecture' => 'TE IT Div B - Deep Learning & NLP Practicals',
            'instructor' => 'Prof. S. Jagiasi',
            'timing' => '10:30 AM - 12:30 PM',
            'status' => 'Lab Active',
            'status_color' => 'emerald',
            'description' => 'Flagship artificial intelligence laboratory outfitted with 40 Nvidia RTX GPU development workstations, PyTorch/TensorFlow environments, and CUDA acceleration.',
            'facilities' => ['40x Nvidia RTX 4080 GPU Nodes', 'PyTorch & CUDA Toolkit', 'Dual 4K Instructor Screens', 'Gigabit Isolated VLAN', 'UPS Protection'],
        ],
        'comp-lab-3' => [
            'id' => 'comp-lab-3',
            'name' => 'Cloud & Cybersecurity Lab 3',
            'code' => 'LAB-110',
            'category' => 'lab',
            'category_label' => 'Computer Lab',
            'level' => 1,
            'wing' => 'East Wing (1st Floor)',
            'capacity' => '38 Workstations',
            'department' => 'Information Technology',
            'current_lecture' => 'BE IT Div A - Cyber Forensics & Defense',
            'instructor' => 'Dr. K. Bhojwani',
            'timing' => '01:30 PM - 03:30 PM',
            'status' => 'Lab Active',
            'status_color' => 'emerald',
            'description' => 'Specialized security lab equipped with isolated virtualization sandboxes, Kali Linux penetration testing tools, and private OpenStack cloud nodes.',
            'facilities' => ['38x High-RAM Workstations', 'Isolated Security Sandbox', 'Kali Linux & Wireshark Tooling', 'Hardware Firewalls', 'Cloud Sandbox'],
        ],
        'staircase-core-b' => [
            'id' => 'staircase-core-b',
            'name' => 'Grand Staircase - Core B',
            'code' => 'STAIR-B',
            'category' => 'stairs',
            'category_label' => 'Vertical Transit',
            'level' => 1,
            'wing' => 'East Wing Core (Levels 0-4)',
            'capacity' => 'Wide Dual Flight Stairs',
            'department' => 'Campus Infrastructure',
            'current_lecture' => 'Primary Vertical Circulation & Fire Exit Route',
            'instructor' => 'Safety Protocol Officer',
            'timing' => 'Unrestricted Access',
            'status' => 'Clear & Monitored',
            'status_color' => 'emerald',
            'description' => 'Architectural grand dual-flight staircase connecting Ground Level to Floor 4. Features non-slip treads, tactile guidance flooring, and fire egress doors.',
            'facilities' => ['Dual-Flight Treads', 'Emergency Photoluminescent Guides', 'Fire Egress Double Doors', 'Tactile Paving', 'CCTV Monitored'],
        ],
        'reception-foyer' => [
            'id' => 'reception-foyer',
            'name' => 'East Foyer & Information Desk',
            'code' => 'FOYER-111',
            'category' => 'office',
            'category_label' => 'Information Desk',
            'level' => 1,
            'wing' => 'East Wing Entrance',
            'capacity' => 'Reception & Waiting Area',
            'department' => 'Student Affairs',
            'current_lecture' => 'Student Inquiries & Guest Assistance',
            'instructor' => 'Reception Staff',
            'timing' => '08:00 AM - 06:00 PM',
            'status' => 'Staff On Duty',
            'status_color' => 'emerald',
            'description' => 'Greeting foyer with interactive digital wayfinding kiosk, schedule screens, and visitor registration counter for the department wing.',
            'facilities' => ['Digital Wayfinding Kiosk', 'Live Schedule Dashboard', 'Visitor Sign-In Counter', 'Comfort Waiting Lounge', 'Wheelchair Ramp'],
        ],
    ];

    public array $leafletLocations = [
        [
            'id' => 'vesit-academic',
            'name' => 'VESIT Academic Flagship (IT & Computer Dept)',
            'code' => 'ACAD-01',
            'category' => 'academic',
            'lat' => 19.0464,
            'lng' => 72.8893,
            'hours' => '08:00 AM - 06:30 PM',
            'status' => 'Open Now',
            'description' => 'The main academic building housing Information Technology, Computer Engineering, and AI & DS departments with 30+ smart classrooms and GPU computing labs.',
            'facilities' => ['24 Smart Classrooms', 'AI/ML GPU Lab', 'Robotics Arena', 'Faculty Cabins', 'Department Library'],
        ],
        [
            'id' => 'vesit-admin',
            'name' => 'Central Administrative Complex',
            'code' => 'ADMIN-HQ',
            'category' => 'admin',
            'lat' => 19.0468,
            'lng' => 72.8887,
            'hours' => '09:00 AM - 05:00 PM',
            'status' => 'Open Now',
            'description' => 'Administrative headquarters accommodating the Principal\'s Office, Registrar, Examination Cell, Accounts, and Student Welfare Directorate.',
            'facilities' => ['Principal\'s Office', 'Admissions Counter', 'Accounts Desk', 'Examination Cell', 'Visitor Lobby'],
        ],
        [
            'id' => 'vesit-auditorium',
            'name' => 'Dr. Hashu Advani Memorial Auditorium',
            'code' => 'AUDI-1200',
            'category' => 'recreation',
            'lat' => 19.0457,
            'lng' => 72.8888,
            'hours' => '09:00 AM - 07:00 PM',
            'status' => 'Events Active',
            'description' => '1,200-seat state-of-the-art acoustic auditorium featuring Dolby surround sound, dual laser projection, and orchestra facilities.',
            'facilities' => ['1,200 Cushioned Seats', 'Dolby Atmos Sound', 'VIP Green Rooms', 'Dual Laser Screens', 'Press Gallery'],
        ],
        [
            'id' => 'vesit-library',
            'name' => 'Central Digital Library & Research Center',
            'code' => 'LIB-DIGITAL',
            'category' => 'academic',
            'lat' => 19.0465,
            'lng' => 72.8898,
            'hours' => '08:00 AM - 08:30 PM',
            'status' => 'Open Now',
            'description' => 'A three-story digital repository holding 65,000+ technical volumes, IEEE/ACM digital terminal suites, and quiet research cubicles.',
            'facilities' => ['65,000+ Books', 'IEEE Xplore Terminals', 'Silent Study Pods', 'E-Journals Access', 'Book Issue Counter'],
        ],
        [
            'id' => 'vesit-cafeteria',
            'name' => 'Campus Bistro & Food Court',
            'code' => 'CAFE-COURT',
            'category' => 'recreation',
            'lat' => 19.0458,
            'lng' => 72.8895,
            'hours' => '07:30 AM - 07:30 PM',
            'status' => 'Open Now',
            'description' => 'Spacious multi-cuisine dining hall with fresh vegetarian meals, espresso counter, juice station, and outdoor shaded pergolas.',
            'facilities' => ['Multi-Cuisine Buffet', 'Espresso Bar', 'Juice Counter', 'Outdoor Shaded Pergola', 'Stationery Store'],
        ],
        [
            'id' => 'vesit-sports',
            'name' => 'Athletics Arena & Sports Complex',
            'code' => 'SPORTS-OVAL',
            'category' => 'sports',
            'lat' => 19.0454,
            'lng' => 72.8903,
            'hours' => '06:00 AM - 08:00 PM',
            'status' => 'Open Now',
            'description' => 'Comprehensive athletic facility with 400m synthetic running track, natural turf football field, basketball court, and modern fitness center.',
            'facilities' => ['400m Synthetic Track', 'Turf Football Pitch', 'Basketball Courts', 'Fitness Center', 'Locker Rooms'],
        ],
        [
            'id' => 'vesit-gate',
            'name' => 'Main Campus Security Gate (Chembur)',
            'code' => 'GATE-01',
            'category' => 'admin',
            'lat' => 19.0469,
            'lng' => 72.8881,
            'hours' => '24 Hours Open',
            'status' => 'Security Active',
            'description' => 'Main campus vehicular and pedestrian entrance off Collector\'s Colony road with automated RFID boom barriers and visitor checkpoint.',
            'facilities' => ['Automated Boom Barrier', 'RFID Pass Scanner', '24/7 Security Guard Post', 'Visitor Sign-In', 'CCTV Surveillance'],
        ],
    ];

    public function selectRoom($id)
    {
        $this->selectedRoomId = $id;
    }

    public function setLevel($level)
    {
        $this->selectedLevel = $level;
        if ($level === 1) {
            $this->selectedRoomId = 'cr-102';
        }
    }

    public function setCategory($cat)
    {
        $this->selectedCategory = $cat;
    }

    public function getSelectedRoomProperty()
    {
        if ($this->viewMode === 'leaflet') {
            return collect($this->leafletLocations)->firstWhere('id', $this->selectedRoomId) 
                ?? $this->leafletLocations[0];
        }

        return $this->blueprintRooms[$this->selectedRoomId] ?? $this->blueprintRooms['cr-102'];
    }

    public function getFilteredRoomsProperty()
    {
        return collect($this->blueprintRooms)->filter(function ($room) {
            $matchesCat = $this->selectedCategory === 'all' || $room['category'] === $this->selectedCategory;
            $matchesSearch = empty($this->search) ||
                str_contains(strtolower($room['name']), strtolower($this->search)) ||
                str_contains(strtolower($room['code']), strtolower($this->search)) ||
                str_contains(strtolower($room['department']), strtolower($this->search)) ||
                str_contains(strtolower($room['description']), strtolower($this->search));

            return $matchesCat && $matchesSearch;
        });
    }
};
?>

@push('styles')
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin="" />
<style>
    /* ═══════════════════════════════════════════════════════════════════════
       BULLETPROOF THEME SYSTEM (Light in Light Mode, Dark in Dark Mode)
       ═══════════════════════════════════════════════════════════════════════ */

    /* Active Button Class: Always High Contrast Lime Yellow */
    .btn-map-active {
        background-color: #d2e823 !important;
        color: #000000 !important;
        border: 1px solid #b5cc10 !important;
        font-weight: 800 !important;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1) !important;
    }
    .btn-map-active *,
    .btn-map-active span,
    .btn-map-active svg {
        color: #000000 !important;
        stroke: #000000 !important;
        font-weight: 800 !important;
    }
    html.dark .btn-map-active,
    [data-theme="dark"] .btn-map-active {
        background-color: #d2e823 !important;
        color: #000000 !important;
        border: 1px solid #d2e823 !important;
        font-weight: 800 !important;
        box-shadow: 0 0 14px rgba(210, 232, 35, 0.35) !important;
    }
    html.dark .btn-map-active *,
    html.dark .btn-map-active span,
    html.dark .btn-map-active svg,
    [data-theme="dark"] .btn-map-active *,
    [data-theme="dark"] .btn-map-active span,
    [data-theme="dark"] .btn-map-active svg {
        color: #000000 !important;
        stroke: #000000 !important;
        font-weight: 800 !important;
    }

    /* Inactive Button Class: Light in Light mode, Dark in Dark mode */
    .btn-map-inactive {
        background-color: #ffffff !important;
        color: #334155 !important;
        border: 1px solid #cbd5e1 !important;
        font-weight: 600 !important;
    }
    .btn-map-inactive:hover {
        background-color: #f1f5f9 !important;
        color: #0f172a !important;
        border-color: #94a3b8 !important;
    }
    .btn-map-inactive *,
    .btn-map-inactive span,
    .btn-map-inactive svg {
        color: #334155 !important;
        stroke: #334155 !important;
    }
    .btn-map-inactive:hover *,
    .btn-map-inactive:hover span,
    .btn-map-inactive:hover svg {
        color: #0f172a !important;
        stroke: #0f172a !important;
    }
    html.dark .btn-map-inactive,
    [data-theme="dark"] .btn-map-inactive {
        background-color: #0b1329 !important;
        color: #94a3b8 !important;
        border: 1px solid rgba(255, 255, 255, 0.08) !important;
        font-weight: 600 !important;
    }
    html.dark .btn-map-inactive:hover,
    [data-theme="dark"] .btn-map-inactive:hover {
        background-color: #1e293b !important;
        color: #ffffff !important;
        border-color: rgba(255, 255, 255, 0.2) !important;
    }
    html.dark .btn-map-inactive *,
    html.dark .btn-map-inactive span,
    html.dark .btn-map-inactive svg,
    [data-theme="dark"] .btn-map-inactive *,
    [data-theme="dark"] .btn-map-inactive span,
    [data-theme="dark"] .btn-map-inactive svg {
        color: #94a3b8 !important;
        stroke: #94a3b8 !important;
    }
    html.dark .btn-map-inactive:hover *,
    html.dark .btn-map-inactive:hover span,
    html.dark .btn-map-inactive:hover svg,
    [data-theme="dark"] .btn-map-inactive:hover *,
    [data-theme="dark"] .btn-map-inactive:hover span,
    [data-theme="dark"] .btn-map-inactive:hover svg {
        color: #ffffff !important;
        stroke: #ffffff !important;
    }

    /* Right Details Card Banner Header: Light in Light mode, Dark in Dark mode */
    .detail-banner-header {
        background: linear-gradient(135deg, #f8fafc 0%, #f1f5f9 100%) !important;
        border: 1px solid #e2e8f0 !important;
        color: #0f172a !important;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.05) !important;
    }
    .detail-banner-header h2,
    .detail-banner-header .banner-title {
        color: #0f172a !important;
        text-shadow: none !important;
        font-weight: 800 !important;
    }
    .detail-banner-header .badge-tag {
        background-color: #e2e8f0 !important;
        color: #1e293b !important;
        border: 1px solid #cbd5e1 !important;
    }
    .detail-banner-header .status-tag {
        color: #059669 !important;
        font-weight: 700 !important;
    }
    html.dark .detail-banner-header,
    [data-theme="dark"] .detail-banner-header {
        background: linear-gradient(135deg, #0b1329 0%, #070d18 100%) !important;
        border: 1px solid rgba(255, 255, 255, 0.15) !important;
        color: #ffffff !important;
        box-shadow: none !important;
    }
    html.dark .detail-banner-header h2,
    html.dark .detail-banner-header .banner-title,
    [data-theme="dark"] .detail-banner-header h2,
    [data-theme="dark"] .detail-banner-header .banner-title {
        color: #ffffff !important;
        text-shadow: 0 1px 3px rgba(0, 0, 0, 0.6) !important;
    }
    html.dark .detail-banner-header .badge-tag,
    [data-theme="dark"] .detail-banner-header .badge-tag {
        background-color: rgba(255, 255, 255, 0.1) !important;
        color: #ffffff !important;
        border: 1px solid rgba(255, 255, 255, 0.2) !important;
    }
    html.dark .detail-banner-header .status-tag,
    [data-theme="dark"] .detail-banner-header .status-tag {
        color: #34d399 !important;
        font-weight: 700 !important;
    }

    /* ═══════════════════════════════════════════════════════════════════════
       ARCHITECTURAL SVG BLUEPRINT STYLES (100% Reliable In All Modes)
       ═══════════════════════════════════════════════════════════════════════ */
    .arch-canvas { fill: #f8fafc !important; }
    .arch-wall-frame { stroke: #334155 !important; fill: none !important; }
    .arch-room { fill: #ffffff !important; stroke: #334155 !important; }
    .arch-lab { fill: #e0f2fe !important; stroke: #334155 !important; }
    .arch-furniture { fill: #bae6fd !important; stroke: #64748b !important; }
    .arch-furniture-slate { fill: #e2e8f0 !important; stroke: #64748b !important; }
    .arch-cube { fill: #38bdf8 !important; stroke: #0284c7 !important; }
    .arch-stair-tread { stroke: #94a3b8 !important; }
    .arch-stair-landing { fill: #7dd3fc !important; stroke: #64748b !important; }
    .blueprint-label { fill: #0f172a !important; stroke: none !important; }
    .blueprint-sublabel { fill: #64748b !important; stroke: none !important; }

    /* SVG DARK MODE */
    html.dark .arch-canvas, [data-theme="dark"] .arch-canvas { fill: #070d18 !important; }
    html.dark .arch-wall-frame, [data-theme="dark"] .arch-wall-frame { stroke: #38bdf8 !important; }
    html.dark .arch-room, [data-theme="dark"] .arch-room { fill: #0c1c30 !important; stroke: #38bdf8 !important; }
    html.dark .arch-lab, [data-theme="dark"] .arch-lab { fill: #0c253d !important; stroke: #38bdf8 !important; }
    html.dark .arch-furniture, [data-theme="dark"] .arch-furniture { fill: #0284c7 !important; stroke: #0369a1 !important; }
    html.dark .arch-furniture-slate, [data-theme="dark"] .arch-furniture-slate { fill: #1e293b !important; stroke: #0284c7 !important; }
    html.dark .arch-cube, [data-theme="dark"] .arch-cube { fill: #0284c7 !important; stroke: #38bdf8 !important; }
    html.dark .arch-stair-tread, [data-theme="dark"] .arch-stair-tread { stroke: #38bdf8 !important; }
    html.dark .arch-stair-landing, [data-theme="dark"] .arch-stair-landing { fill: #0369a1 !important; stroke: #38bdf8 !important; }
    html.dark .blueprint-label, [data-theme="dark"] .blueprint-label { fill: #f8fafc !important; stroke: none !important; }
    html.dark .blueprint-sublabel, [data-theme="dark"] .blueprint-sublabel { fill: #94a3b8 !important; stroke: none !important; }

    .blueprint-canvas {
        background-color: #f8fafc !important;
        background-image: 
            linear-gradient(rgba(148, 163, 184, 0.18) 1px, transparent 1px),
            linear-gradient(90deg, rgba(148, 163, 184, 0.18) 1px, transparent 1px) !important;
        background-size: 20px 20px !important;
        transition: all 0.3s ease;
    }
    html.dark .blueprint-canvas, [data-theme="dark"] .blueprint-canvas {
        background-color: #070d18 !important;
        background-image: 
            linear-gradient(rgba(56, 189, 248, 0.08) 1px, transparent 1px),
            linear-gradient(90deg, rgba(56, 189, 248, 0.08) 1px, transparent 1px) !important;
    }

    html.dark #leaflet-map .leaflet-tile-pane,
    [data-theme="dark"] #leaflet-map .leaflet-tile-pane {
        filter: invert(100%) hue-rotate(180deg) brightness(95%) contrast(90%);
    }

    /* Room Poly Styling & Active Glow */
    .room-poly {
        cursor: pointer;
    }
    .room-poly text,
    .room-poly.active-room text,
    svg text.blueprint-label,
    svg text.blueprint-sublabel {
        stroke: none !important;
        stroke-width: 0 !important;
        filter: none !important;
    }
    .room-poly:hover rect.base-rect {
        stroke: #0284c7 !important;
        stroke-width: 2.5px !important;
    }
    .room-poly.active-room rect.base-rect {
        stroke: #0284c7 !important;
        stroke-width: 3px !important;
        filter: drop-shadow(0 2px 8px rgba(2, 132, 199, 0.35)) !important;
    }
    html.dark .room-poly.active-room rect.base-rect,
    [data-theme="dark"] .room-poly.active-room rect.base-rect {
        stroke: #d2e823 !important;
        stroke-width: 3.5px !important;
        filter: drop-shadow(0 0 12px rgba(210, 232, 35, 0.75)) !important;
    }

    /* Strict In-Boundary Text Labels: No Overflow */
    .blueprint-label {
        font-family: 'DM Sans', -apple-system, sans-serif;
        font-size: 8.5px;
        font-weight: 800;
        text-anchor: middle;
        pointer-events: none;
        letter-spacing: 0.02em;
    }
    .blueprint-sublabel {
        font-family: 'DM Sans', -apple-system, sans-serif;
        font-size: 7.5px;
        font-weight: 700;
        text-anchor: middle;
        pointer-events: none;
        letter-spacing: 0.01em;
    }

    @keyframes bp-pulse {
        0% { transform: scale(1); opacity: 0.95; }
        50% { transform: scale(1.15); opacity: 0.5; }
        100% { transform: scale(1); opacity: 0.95; }
    }
    .badge-pulse {
        animation: bp-pulse 2s cubic-bezier(0.4, 0, 0.6, 1) infinite;
        transform-origin: center;
    }
</style>
@endpush

<div class="space-y-6">

    <!-- HERO HEADER -->
    <div class="relative overflow-hidden rounded-3xl bg-white dark:bg-gradient-to-br dark:from-[#0f172a] dark:via-[#0b1120] dark:to-[#080c14] border border-slate-200 dark:border-white/5 p-6 md:p-8 shadow-sm dark:shadow-xl transition-all">
        <div class="absolute inset-0 opacity-10 dark:opacity-20 pointer-events-none" style="background-image: radial-gradient(currentColor 1px, transparent 1px); background-size: 24px 24px;"></div>
        <div class="absolute -top-12 -right-12 w-64 h-64 rounded-full bg-lime-500/10 dark:bg-[#d2e823]/10 blur-3xl pointer-events-none"></div>

        <div class="relative flex flex-col md:flex-row md:items-center justify-between gap-6">
            <div>
                <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-lime-500/10 dark:bg-[#d2e823]/10 border border-lime-500/25 dark:border-[#d2e823]/25 text-lime-700 dark:text-[#d2e823] text-xs font-bold mb-3">
                    <span class="w-2 h-2 rounded-full bg-lime-500 dark:bg-[#d2e823] animate-pulse"></span>
                    VESIT Spatial Wayfinding &amp; Floor Blueprint
                </div>
                <h1 class="font-display font-extrabold text-3xl md:text-4xl text-slate-900 dark:text-white tracking-tight leading-tight">
                    Campus Map &amp; Architectural Blueprint
                </h1>
                <p class="text-slate-600 dark:text-slate-400 text-sm mt-1.5 max-w-2xl font-medium">
                    Indoor architectural floor blueprint of classrooms, AI computing labs, faculty cabins, and OpenStreetMap satellite campus grounds.
                </p>
            </div>

            <!-- DUAL VIEW SWITCHER: BULLETPROOF HIGH CONTRAST -->
            <div class="flex items-center bg-slate-100 dark:bg-black/50 p-1.5 rounded-2xl border border-slate-200 dark:border-white/10 shrink-0 shadow-sm">
                <button type="button" wire:click="$set('viewMode', 'blueprint')"
                    class="flex items-center gap-2 px-4 py-2.5 rounded-xl text-xs font-extrabold transition-all {{ $viewMode === 'blueprint' ? 'btn-map-active shadow-md' : 'btn-map-inactive' }}">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" /></svg>
                    <span>Floor Blueprint (Image 2)</span>
                </button>
                <button type="button" wire:click="$set('viewMode', 'leaflet')"
                    class="flex items-center gap-2 px-4 py-2.5 rounded-xl text-xs font-extrabold transition-all {{ $viewMode === 'leaflet' ? 'btn-map-active shadow-md' : 'btn-map-inactive' }}">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-.553-.894L15 4m0 13V4m0 0L9 7" /></svg>
                    <span>Leaflet.js &amp; OpenStreetMap</span>
                </button>
            </div>
        </div>
    </div>

    <!-- FILTER & LEVEL CONTROL BAR -->
    <div class="bg-white dark:bg-[#0f172a]/70 border border-slate-200 dark:border-white/5 p-4 rounded-2xl flex flex-col md:flex-row gap-4 items-center justify-between shadow-sm">
        
        <div class="relative w-full md:w-80">
            <svg class="absolute left-3.5 top-1/2 -translate-y-1/2 w-4 h-4 text-slate-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
            <input type="text" wire:model.live.debounce.200ms="search" placeholder="Search room, lab, or code (e.g. CR-102)..."
                class="w-full bg-slate-50 dark:bg-[#080c14] border border-slate-200 dark:border-white/10 rounded-xl pl-10 pr-4 py-2.5 text-slate-900 dark:text-white text-xs font-medium focus:outline-none focus:border-[#d2e823] placeholder-slate-400 dark:placeholder-slate-500 transition">
        </div>

        @if($viewMode === 'blueprint')
            <!-- Floor Level Pills -->
            <div class="flex items-center gap-1.5 flex-wrap">
                <span class="text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider mr-1">Floor Level:</span>
                @foreach([
                    0 => 'Ground Level',
                    1 => '1st Floor (IT & CMPN)',
                    2 => '2nd Floor (Robotics)',
                    3 => '3rd Floor (Library)'
                ] as $lvl => $lvlTitle)
                    <button type="button" wire:click="setLevel({{ $lvl }})"
                        class="px-3 py-1.5 rounded-xl text-xs font-bold transition-all
                        {{ $selectedLevel === $lvl ? 'btn-map-active shadow-sm' : 'btn-map-inactive' }}">
                        <span>{{ $lvlTitle }}</span>
                    </button>
                @endforeach
            </div>

            <!-- Category Pills -->
            <div class="flex items-center gap-1.5 flex-wrap">
                @foreach([
                    'all' => 'All Zones',
                    'classroom' => 'Classrooms',
                    'lab' => 'Computer Labs',
                    'office' => 'Faculty Offices',
                ] as $ckey => $clabel)
                    <button type="button" wire:click="setCategory('{{ $ckey }}')"
                        class="px-3 py-1.5 rounded-xl text-xs font-semibold transition-all
                        {{ $selectedCategory === $ckey ? 'bg-blue-600 text-white shadow-sm' : 'btn-map-inactive' }}">
                        <span>{{ $clabel }}</span>
                    </button>
                @endforeach
            </div>
        @else
            <div class="flex items-center gap-3 text-xs font-semibold text-slate-600 dark:text-slate-400">
                <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-emerald-500/10 text-emerald-600 dark:text-emerald-400 border border-emerald-500/20">
                    <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span>
                    Live GPS: 19.0461° N, 72.8890° E (VESIT Chembur)
                </span>
                <span class="text-slate-400">OpenStreetMap Live Tile Layer</span>
            </div>
        @endif
    </div>

    <!-- MAIN INTERACTIVE SPATIAL SECTION -->
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 items-start">

        <!-- MAP CANVAS COLUMN (8 COLS) -->
        <div class="lg:col-span-8 bg-white dark:bg-[#0f172a]/70 border border-slate-200 dark:border-white/5 rounded-3xl p-5 flex flex-col shadow-sm">
            
            <div class="flex items-center justify-between mb-3 px-1 text-xs">
                <div class="flex items-center gap-2 font-bold text-slate-700 dark:text-slate-300">
                    <span class="w-2.5 h-2.5 rounded-full {{ $viewMode === 'blueprint' ? 'bg-[#7c3aed]' : 'bg-emerald-500' }} animate-pulse"></span>
                    <span>
                        {{ $viewMode === 'blueprint' ? 'Architectural Floor Blueprint — Level 1 (IT & Computer Dept)' : 'VESIT Chembur Campus Grounds — OpenStreetMap' }}
                    </span>
                </div>
                <div class="flex items-center gap-2 text-slate-400 font-mono text-[11px]">
                    <span class="px-2 py-0.5 rounded-md bg-slate-100 dark:bg-white/5 border border-slate-200 dark:border-white/10">
                        {{ $viewMode === 'blueprint' ? 'SCALE: 1:100 ARCHITECTURAL' : 'ZOOM: 17.5 • CHEMBUR' }}
                    </span>
                </div>
            </div>

            <div class="w-full relative rounded-2xl overflow-hidden border border-slate-200 dark:border-white/10 shadow-lg">

                <!-- 1. BLUEPRINT VIEW CONTAINER -->
                <div id="blueprint-container" class="{{ $viewMode === 'blueprint' ? 'block' : 'hidden' }} w-full blueprint-canvas relative select-none" style="aspect-ratio: 16/9.5;">

                    <!-- FLOATING BLUEPRINT LEGEND -->
                    <div class="absolute top-3 right-3 z-10 bg-white/95 dark:bg-slate-900/95 backdrop-blur-md border border-slate-200 dark:border-white/10 rounded-xl p-2.5 shadow-md flex items-center gap-3 text-[10px] font-bold text-slate-700 dark:text-slate-300 pointer-events-none">
                        <div class="flex items-center gap-1.5">
                            <span class="w-3.5 h-3.5 rounded-full bg-[#7c3aed] flex items-center justify-center text-white text-[8px]">🎓</span>
                            <span>Classroom</span>
                        </div>
                        <div class="flex items-center gap-1.5">
                            <span class="w-3.5 h-3.5 rounded-full bg-[#7c3aed] flex items-center justify-center text-white text-[8px]">📦</span>
                            <span>AI Lab</span>
                        </div>
                        <div class="flex items-center gap-1.5">
                            <span class="w-3.5 h-3.5 rounded-full bg-[#7c3aed] flex items-center justify-center text-white text-[8px]">🪜</span>
                            <span>Stairs Core</span>
                        </div>
                        <div class="flex items-center gap-1.5">
                            <span class="w-3.5 h-3.5 rounded-md bg-[#10b981] text-white flex items-center justify-center text-[8px]">1</span>
                            <span>Entrance</span>
                        </div>
                    </div>

                    <!-- ARCHITECTURAL BLUEPRINT SVG (BULLETPROOF CLASSES) -->
                    <svg viewBox="0 0 1000 560" class="w-full h-full">
                        <!-- Canvas Solid Floor Base -->
                        <rect x="0" y="0" width="1000" height="560" class="arch-canvas" rx="8" />

                        <!-- Outer Blueprint Wall Frame -->
                        <rect x="25" y="25" width="940" height="505" rx="8" class="arch-wall-frame" stroke-width="3.5" stroke-linecap="round" stroke-linejoin="round" />

                        <!-- ================= LEFT WING: CLASSROOMS (IMAGE 2) ================= -->
                        
                        <!-- --- CLASSROOM 101 (TOP LEFT) --- -->
                        <g wire:click="selectRoom('cr-101')" class="room-poly {{ $selectedRoomId === 'cr-101' ? 'active-room' : '' }}">
                            <rect x="30" y="30" width="180" height="135" rx="4" class="base-rect arch-room" stroke-width="1.5" />
                            
                            <rect x="42" y="40" width="22" height="10" rx="2" class="arch-furniture" stroke-width="0.8" />
                            <rect x="70" y="40" width="22" height="10" rx="2" class="arch-furniture" stroke-width="0.8" />
                            <rect x="98" y="40" width="22" height="10" rx="2" class="arch-furniture" stroke-width="0.8" />
                            <rect x="126" y="40" width="22" height="10" rx="2" class="arch-furniture" stroke-width="0.8" />
                            <rect x="154" y="40" width="22" height="10" rx="2" class="arch-furniture" stroke-width="0.8" />

                            <rect x="194" y="62" width="10" height="20" rx="2" class="arch-furniture" stroke-width="0.8" />
                            <rect x="194" y="88" width="10" height="20" rx="2" class="arch-furniture" stroke-width="0.8" />
                            <rect x="194" y="114" width="10" height="20" rx="2" class="arch-furniture" stroke-width="0.8" />

                            <rect x="50" y="125" width="22" height="14" rx="2" class="arch-furniture" stroke-width="0.8" />

                            <text x="120" y="95" class="blueprint-label">CR-101 • Lecture Hall</text>
                            <text x="120" y="110" class="blueprint-sublabel">Cap: 75 • IT Dept</text>
                        </g>

                        <!-- --- CLASSROOM 102 (MIDDLE LEFT - WITH PURPLE GRAD CAP BADGE) --- -->
                        <g wire:click="selectRoom('cr-102')" class="room-poly {{ $selectedRoomId === 'cr-102' ? 'active-room' : '' }}">
                            <rect x="30" y="180" width="180" height="145" rx="4" class="base-rect arch-room" stroke-width="1.5" />

                            <rect x="80" y="240" width="16" height="26" rx="2" class="arch-furniture" stroke-width="0.8" />
                            <rect x="144" y="240" width="16" height="26" rx="2" class="arch-furniture" stroke-width="0.8" />
                            <rect x="102" y="208" width="36" height="14" rx="2" class="arch-furniture" stroke-width="0.8" />
                            <rect x="102" y="284" width="36" height="14" rx="2" class="arch-furniture" stroke-width="0.8" />
                            <rect x="194" y="210" width="10" height="26" rx="2" class="arch-furniture" stroke-width="0.8" />
                            <rect x="194" y="250" width="10" height="26" rx="2" class="arch-furniture" stroke-width="0.8" />

                            <rect x="102" y="235" width="36" height="36" rx="4" class="arch-furniture-slate" stroke-width="0.8" />

                            <!-- EXACT IMAGE 2 FEATURE: PURPLE CIRCULAR BADGE WITH GRADUATION CAP -->
                            <circle cx="120" cy="253" r="14" fill="#7c3aed" class="{{ $selectedRoomId === 'cr-102' ? 'badge-pulse' : '' }}" />
                            <g transform="translate(112, 245) scale(0.65)">
                                <path d="M12 3L1 9L12 15L21 10.09V17H23V9M5 13.18V17.18L12 21L19 17.18V13.18L12 17L5 13.18Z" fill="#ffffff" />
                            </g>

                            <!-- In-Boundary Room Label -->
                            <text x="120" y="200" class="blueprint-label">CR-102 • Seminar Hall</text>
                            <text x="120" y="315" class="blueprint-sublabel">CMPN Dept • ML Session</text>
                        </g>

                        <!-- --- CLASSROOM 103 (BOTTOM LEFT - WITH PURPLE BADGE & SOLID CUBE) --- -->
                        <g wire:click="selectRoom('cr-103')" class="room-poly {{ $selectedRoomId === 'cr-103' ? 'active-room' : '' }}">
                            <rect x="30" y="340" width="180" height="180" rx="4" class="base-rect arch-room" stroke-width="1.5" />

                            <rect x="80" y="415" width="16" height="26" rx="2" class="arch-furniture" stroke-width="0.8" />
                            <rect x="144" y="415" width="16" height="26" rx="2" class="arch-furniture" stroke-width="0.8" />
                            <rect x="102" y="385" width="36" height="14" rx="2" class="arch-furniture" stroke-width="0.8" />
                            <rect x="102" y="458" width="36" height="14" rx="2" class="arch-furniture" stroke-width="0.8" />
                            <rect x="194" y="375" width="10" height="26" rx="2" class="arch-furniture" stroke-width="0.8" />
                            <rect x="194" y="415" width="10" height="26" rx="2" class="arch-furniture" stroke-width="0.8" />

                            <rect x="102" y="410" width="36" height="36" rx="4" class="arch-furniture-slate" stroke-width="0.8" />

                            <circle cx="120" cy="428" r="14" fill="#7c3aed" class="{{ $selectedRoomId === 'cr-103' ? 'badge-pulse' : '' }}" />
                            <g transform="translate(112, 420) scale(0.65)">
                                <path d="M12 3L1 9L12 15L21 10.09V17H23V9M5 13.18V17.18L12 21L19 17.18V13.18L12 17L5 13.18Z" fill="#ffffff" />
                            </g>

                            <!-- SOLID BLUE CUBE -->
                            <rect x="44" y="472" width="36" height="36" rx="3" class="arch-cube" stroke-width="1" />
                            <text x="62" y="494" text-anchor="middle" fill="#ffffff" font-size="7.5" font-weight="900" stroke="none">IOT</text>

                            <!-- In-Boundary Room Label -->
                            <text x="120" y="372" class="blueprint-label">CR-103 • Robotics Studio</text>
                            <text x="120" y="490" class="blueprint-sublabel">Cap: 60 • Embedded Lab</text>
                        </g>

                        <!-- ================= ENTRANCE PIN 1 (IMAGE 2) ================= -->
                        <g transform="translate(230, 32)">
                            <rect x="0" y="0" width="28" height="28" rx="8" fill="#ffffff" stroke="#cbd5e1" stroke-width="1.5" filter="drop-shadow(0 2px 4px rgba(0,0,0,0.1))" />
                            <path d="M14 6L8 8.5V13C8 17.5 14 21 14 21C14 21 20 17.5 20 13V8.5L14 6Z" fill="#10b981" />
                            <rect x="4" y="34" width="20" height="20" rx="4" fill="#ffffff" stroke="#10b981" stroke-width="2" />
                            <text x="14" y="48" text-anchor="middle" fill="#10b981" font-size="11" font-weight="900" stroke="none">1</text>
                        </g>

                        <!-- ================= MIDDLE WING: FACULTY, RESTROOMS & CONFERENCE ================= -->

                        <!-- --- FACULTY CABINS & ADMIN --- -->
                        <g wire:click="selectRoom('faculty-office')" class="room-poly {{ $selectedRoomId === 'faculty-office' ? 'active-room' : '' }}">
                            <rect x="270" y="65" width="95" height="175" rx="3" class="base-rect arch-room" stroke-width="1.5" />

                            <rect x="272" y="67" width="38" height="34" rx="2" class="arch-cube" stroke-width="1" />

                            <rect x="286" y="112" width="56" height="10" rx="2" class="arch-furniture" stroke-width="0.8" />
                            <rect x="286" y="132" width="45" height="10" rx="2" class="arch-furniture" stroke-width="0.8" />

                            <circle cx="300" cy="180" r="14" fill="#7c3aed" class="{{ $selectedRoomId === 'faculty-office' ? 'badge-pulse' : '' }}" />
                            <g transform="translate(292, 172) scale(0.65)">
                                <path d="M12 2C8.13 2 5 5.13 5 9C5 14.25 12 22 12 22C12 22 19 14.25 19 9C19 5.13 15.87 2 12 2ZM12 11.5C10.62 11.5 9.5 10.38 9.5 9C9.5 7.62 10.62 6.5 12 6.5C13.38 6.5 14.5 7.62 14.5 9C14.5 10.38 13.38 11.5 12 11.5Z" fill="#ffffff" />
                            </g>

                            <text x="317" y="160" class="blueprint-label">Faculty Cabins</text>
                            <text x="317" y="222" class="blueprint-sublabel">HOD &amp; Staff</text>
                        </g>

                        <!-- ================= ENTRANCE PIN 2 (IMAGE 2) ================= -->
                        <g transform="translate(382, 85)">
                            <rect x="0" y="0" width="28" height="28" rx="8" fill="#ffffff" stroke="#cbd5e1" stroke-width="1.5" filter="drop-shadow(0 2px 4px rgba(0,0,0,0.1))" />
                            <path d="M9 8L19 14L9 20V8Z" fill="#10b981" />
                            <rect x="4" y="34" width="20" height="20" rx="4" fill="#ffffff" stroke="#10b981" stroke-width="2" />
                            <text x="14" y="48" text-anchor="middle" fill="#10b981" font-size="11" font-weight="900" stroke="none">2</text>
                        </g>

                        <!-- --- RESEARCH STAFF CABINS & SERVER ROOM --- -->
                        <g wire:click="selectRoom('staff-cabins')" class="room-poly {{ $selectedRoomId === 'staff-cabins' ? 'active-room' : '' }}">
                            <rect x="444" y="65" width="85" height="175" rx="3" class="base-rect arch-room" stroke-width="1.5" />
                            
                            <rect x="450" y="75" width="18" height="14" rx="2" class="arch-furniture" stroke-width="0.8" />
                            <circle cx="516" cy="85" r="4" class="arch-furniture" />
                            <circle cx="516" cy="100" r="4" class="arch-furniture" />
                            <circle cx="516" cy="115" r="4" class="arch-furniture" />
                            <circle cx="516" cy="130" r="4" class="arch-furniture" />

                            <rect x="452" y="150" width="68" height="12" rx="2" class="arch-furniture-slate" stroke-width="0.8" />

                            <text x="486" y="180" class="blueprint-label">Staff Cabins</text>
                            <text x="486" y="195" class="blueprint-sublabel">Server Hub</text>
                        </g>

                        <!-- --- RESTROOMS / WASHROOMS (IMAGE 2 STALLS) --- -->
                        <g wire:click="selectRoom('restrooms')" class="room-poly {{ $selectedRoomId === 'restrooms' ? 'active-room' : '' }}">
                            <rect x="365" y="315" width="65" height="98" rx="3" class="base-rect arch-room" stroke-width="1.5" />
                            <line x1="365" y1="365" x2="430" y2="365" class="arch-inner-wall" stroke-width="1.5" />
                            <rect x="375" y="325" width="22" height="16" rx="8" class="arch-furniture" stroke-width="0.8" />
                            <rect x="375" y="375" width="22" height="16" rx="8" class="arch-furniture" stroke-width="0.8" />
                            <text x="397" y="356" class="blueprint-sublabel">WC (M)</text>

                            <rect x="438" y="315" width="80" height="98" rx="3" class="base-rect arch-room" stroke-width="1.5" />
                            <line x1="438" y1="365" x2="518" y2="365" class="arch-inner-wall" stroke-width="1.5" />
                            <rect x="488" y="325" width="22" height="16" rx="8" class="arch-furniture" stroke-width="0.8" />
                            <rect x="488" y="375" width="22" height="16" rx="8" class="arch-furniture" stroke-width="0.8" />
                            <text x="466" y="356" class="blueprint-sublabel">WC (F)</text>
                        </g>

                        <!-- --- DEPARTMENTAL CONFERENCE HALL --- -->
                        <g wire:click="selectRoom('conf-hall')" class="room-poly {{ $selectedRoomId === 'conf-hall' ? 'active-room' : '' }}">
                            <rect x="270" y="430" width="254" height="95" rx="4" class="base-rect arch-room" stroke-width="1.5" />

                            <rect x="274" y="482" width="36" height="36" rx="2" class="arch-cube" stroke-width="1" />

                            <rect x="342" y="462" width="65" height="42" rx="3" class="arch-furniture" stroke-width="0.8" />
                            <rect x="432" y="462" width="65" height="42" rx="3" class="arch-furniture" stroke-width="0.8" />

                            <text x="410" y="448" class="blueprint-label">Department Conference Hall</text>
                        </g>

                        <!-- ================= RIGHT WING: COMPUTER LABS STACK (IMAGE 2) ================= -->
                        
                        <!-- --- COMPUTER LAB 1 --- -->
                        <g wire:click="selectRoom('comp-lab-1')" class="room-poly {{ $selectedRoomId === 'comp-lab-1' ? 'active-room' : '' }}">
                            <rect x="540" y="80" width="102" height="88" rx="4" class="base-rect arch-lab" stroke-width="1.5" />
                            <rect x="548" y="92" width="10" height="64" rx="2" class="arch-furniture" stroke-width="0.8" />
                            <rect x="624" y="92" width="10" height="64" rx="2" class="arch-furniture" stroke-width="0.8" />

                            <text x="591" y="125" class="blueprint-label">Comp Lab 1</text>
                            <text x="591" y="139" class="blueprint-sublabel">40 Terminals</text>
                        </g>

                        <!-- --- COMPUTER LAB 2 (WITH PURPLE 3D CUBE BADGE) --- -->
                        <g wire:click="selectRoom('comp-lab-2')" class="room-poly {{ $selectedRoomId === 'comp-lab-2' ? 'active-room' : '' }}">
                            <rect x="540" y="180" width="102" height="88" rx="4" class="base-rect arch-lab" stroke-width="1.5" />
                            <rect x="548" y="192" width="10" height="64" rx="2" class="arch-furniture" stroke-width="0.8" />

                            <circle cx="630" cy="224" r="14" fill="#7c3aed" class="{{ $selectedRoomId === 'comp-lab-2' ? 'badge-pulse' : '' }}" />
                            <g transform="translate(622, 216) scale(0.65)">
                                <path d="M21 16.5C21 16.88 20.79 17.21 20.47 17.38L12.57 21.82C12.41 21.94 12.21 22 12 22C11.79 22 11.59 21.94 11.43 21.82L3.53 17.38C3.21 17.21 3 16.88 3 16.5V7.5C3 7.12 3.21 6.79 3.53 6.62L11.43 2.18C11.59 2.06 11.79 2 12 2C12.21 2 12.41 2.06 12.57 2.18L20.47 6.62C20.79 6.79 21 7.12 21 7.5V16.5Z" fill="#ffffff" />
                            </g>

                            <text x="585" y="222" class="blueprint-label">Comp Lab 2 (AI)</text>
                            <text x="585" y="236" class="blueprint-sublabel">RTX 4080 Nodes</text>
                        </g>

                        <!-- --- COMPUTER LAB 3 --- -->
                        <g wire:click="selectRoom('comp-lab-3')" class="room-poly {{ $selectedRoomId === 'comp-lab-3' ? 'active-room' : '' }}">
                            <rect x="540" y="280" width="102" height="88" rx="4" class="base-rect arch-lab" stroke-width="1.5" />
                            <rect x="548" y="292" width="10" height="64" rx="2" class="arch-furniture" stroke-width="0.8" />
                            <rect x="624" y="292" width="10" height="64" rx="2" class="arch-furniture" stroke-width="0.8" />

                            <text x="591" y="325" class="blueprint-label">Comp Lab 3</text>
                            <text x="591" y="339" class="blueprint-sublabel">Cloud &amp; Security</text>
                        </g>

                        <!-- ================= ENTRANCE PIN 3 (IMAGE 2) ================= -->
                        <g transform="translate(718, 130)">
                            <rect x="0" y="0" width="28" height="28" rx="8" fill="#ffffff" stroke="#cbd5e1" stroke-width="1.5" filter="drop-shadow(0 2px 4px rgba(0,0,0,0.1))" />
                            <path d="M9 8L19 14L9 20V8Z" fill="#10b981" />
                            <rect x="4" y="34" width="20" height="20" rx="4" fill="#ffffff" stroke="#10b981" stroke-width="2" />
                            <text x="14" y="48" text-anchor="middle" fill="#10b981" font-size="11" font-weight="900" stroke="none">3</text>
                        </g>

                        <!-- ================= GRAND STAIRCASE CORE B (IMAGE 2) ================= -->
                        <g wire:click="selectRoom('staircase-core-b')" class="room-poly {{ $selectedRoomId === 'staircase-core-b' ? 'active-room' : '' }}">
                            <rect x="772" y="80" width="174" height="290" rx="4" class="base-rect arch-room" stroke-width="1.5" />

                            @for($step = 0; $step < 14; $step++)
                                <line x1="772" y1="{{ 88 + ($step * 20) }}" x2="946" y2="{{ 88 + ($step * 20) }}" class="arch-stair-tread" stroke-width="1.5" />
                            @endfor

                            <!-- Central Staircase Landing Strip -->
                            <rect x="836" y="80" width="46" height="290" class="arch-stair-landing" stroke-width="1.5" />

                            <circle cx="859" cy="225" r="14" fill="#7c3aed" class="{{ $selectedRoomId === 'staircase-core-b' ? 'badge-pulse' : '' }}" />
                            <g transform="translate(851, 217) scale(0.65)">
                                <path d="M19 3H16V6H13V9H10V12H7V15H4V18H1V21H23V3H19ZM21 19H3.41L5 17.41V17H7V14H10V11H13V8H16V5H19V19H21Z" fill="#ffffff" />
                            </g>

                            <text x="859" y="345" class="blueprint-label">Staircase Core B</text>
                            <text x="859" y="358" class="blueprint-sublabel">Levels 0-4 Exit</text>
                        </g>

                        <!-- ================= RECEPTION FOYER DESK (IMAGE 2) ================= -->
                        <g wire:click="selectRoom('reception-foyer')" class="room-poly {{ $selectedRoomId === 'reception-foyer' ? 'active-room' : '' }}">
                            <rect x="830" y="420" width="66" height="32" rx="4" class="arch-furniture" stroke-width="1" />

                            <circle cx="863" cy="436" r="13" fill="#7c3aed" class="{{ $selectedRoomId === 'reception-foyer' ? 'badge-pulse' : '' }}" />
                            <g transform="translate(856, 429) scale(0.6)">
                                <path d="M20 18C21.1 18 22 17.1 22 16V6C22 4.9 21.1 4 20 4H4C2.9 4 2 4.9 2 6V16C2 17.1 2.9 18 4 18H0V20H24V18H20ZM4 6H20V16H4V6Z" fill="#ffffff" />
                            </g>
                            <g transform="translate(856, 429) scale(0.6)">
                                <path d="M20 18C21.1 18 22 17.1 22 16V6C22 4.9 21.1 4 20 4H4C2.9 4 2 4.9 2 6V16C2 17.1 2.9 18 4 18H0V20H24V18H20ZM4 6H20V16H4V6Z" fill="#ffffff" />
                            </g>

                            <text x="863" y="475" class="blueprint-label">Main Foyer Desk</text>
                        </g>

                        <!-- Corridor Connectors -->
                        <line x1="210" y1="30" x2="210" y2="525" class="arch-wall-frame" stroke-width="3" />
                        <line x1="530" y1="65" x2="530" y2="370" class="arch-wall-frame" stroke-width="3" />
                        <line x1="645" y1="80" x2="645" y2="525" class="arch-wall-frame" stroke-width="3" />
                    </svg>
                </div>

                <!-- 2. LEAFLET VIEW CONTAINER -->
                <div id="leaflet-container" class="{{ $viewMode === 'leaflet' ? 'block' : 'hidden' }} w-full h-full min-h-[480px] bg-slate-100 dark:bg-slate-950 relative select-none" style="aspect-ratio: 16/9.5;">
                    <div id="leaflet-map" wire:ignore class="w-full h-full min-h-[480px] z-0"></div>
                </div>

            </div>
        </div>

        <!-- RIGHT DETAIL CARD COLUMN (4 COLS) -->
        <div class="lg:col-span-4 bg-white dark:bg-[#0f172a]/70 border border-slate-200 dark:border-white/5 rounded-3xl p-6 shadow-sm flex flex-col justify-between">
            @if($this->selectedRoom)
                <div class="space-y-5">
                    
                    <!-- Top Room Header Banner: 100% High Contrast In Both Light and Dark -->
                    <div class="detail-banner-header relative overflow-hidden rounded-2xl p-5 shadow-sm">
                        <div class="flex items-center justify-between mb-3">
                            <span class="badge-tag px-2.5 py-1 rounded-lg text-[10px] uppercase font-extrabold tracking-wider">
                                {{ $this->selectedRoom['category_label'] ?? $this->selectedRoom['category'] }}
                            </span>
                            <span class="px-2.5 py-1 rounded-lg text-[10px] font-mono font-extrabold bg-[#d2e823] text-black">
                                {{ $this->selectedRoom['code'] }}
                            </span>
                        </div>
                        <h2 class="text-xl font-bold font-display leading-tight banner-title">{{ $this->selectedRoom['name'] }}</h2>
                        <div class="flex items-center gap-2 mt-2 text-xs status-tag font-semibold">
                            <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span>
                            {{ $this->selectedRoom['status'] ?? 'Open Now' }}
                        </div>
                    </div>

                    <!-- Room Overview -->
                    <div>
                        <h4 class="text-xs font-bold uppercase tracking-wider text-slate-400 mb-1.5">Overview</h4>
                        <p class="text-slate-600 dark:text-slate-300 text-xs leading-relaxed">
                            {{ $this->selectedRoom['description'] }}
                        </p>
                    </div>

                    <!-- Room Schedule & Capacity -->
                    <div class="grid grid-cols-2 gap-3">
                        <div class="bg-slate-50 dark:bg-[#080c14] p-3.5 rounded-2xl border border-slate-200/80 dark:border-white/5">
                            <p class="text-slate-400 text-[10px] uppercase font-bold tracking-wider">
                                {{ isset($this->selectedRoom['current_lecture']) ? 'Live Session' : 'Operating Hours' }}
                            </p>
                            <p class="text-slate-900 dark:text-white text-xs font-bold mt-1 truncate">
                                {{ $this->selectedRoom['timing'] ?? $this->selectedRoom['hours'] }}
                            </p>
                        </div>
                        <div class="bg-slate-50 dark:bg-[#080c14] p-3.5 rounded-2xl border border-slate-200/80 dark:border-white/5">
                            <p class="text-slate-400 text-[10px] uppercase font-bold tracking-wider">Capacity &amp; Wing</p>
                            <p class="text-slate-900 dark:text-white text-xs font-bold mt-1 truncate">
                                {{ $this->selectedRoom['capacity'] ?? 'Campus Amenity' }}
                            </p>
                        </div>
                    </div>

                    @if(isset($this->selectedRoom['current_lecture']))
                        <div class="p-3.5 rounded-2xl bg-lime-500/10 dark:bg-[#d2e823]/10 border border-lime-500/25 dark:border-[#d2e823]/25 text-xs">
                            <div class="font-bold text-slate-900 dark:text-white flex items-center gap-2">
                                <span class="w-2 h-2 rounded-full bg-lime-500 dark:bg-[#d2e823]"></span>
                                {{ $this->selectedRoom['current_lecture'] }}
                            </div>
                            <div class="text-slate-600 dark:text-slate-400 text-[11px] mt-1">
                                Instructor: <span class="font-semibold text-slate-800 dark:text-slate-200">{{ $this->selectedRoom['instructor'] }}</span>
                            </div>
                        </div>
                    @endif

                    <!-- Facilities List -->
                    <div>
                        <h4 class="text-xs font-bold uppercase tracking-wider text-slate-400 mb-2">Equipped Facilities</h4>
                        <div class="flex flex-wrap gap-1.5">
                            @foreach($this->selectedRoom['facilities'] as $fac)
                                <span class="px-2.5 py-1 rounded-xl text-[11px] font-semibold bg-slate-100 dark:bg-white/5 border border-slate-200 dark:border-white/10 text-slate-700 dark:text-slate-300">
                                    ✓ {{ $fac }}
                                </span>
                            @endforeach
                        </div>
                    </div>

                    <!-- Action Button -->
                    <div class="pt-2">
                        @if($viewMode === 'leaflet')
                            <button type="button" wire:click="$set('viewMode', 'blueprint')"
                                class="w-full flex items-center justify-center gap-2 py-3 px-4 rounded-xl text-xs font-bold bg-[#d2e823] text-black hover:opacity-90 active:scale-[0.98] transition shadow-md shadow-lime-500/20">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" /></svg>
                                <span>Inspect Indoor Blueprint (Image 2) &rarr;</span>
                            </button>
                        @else
                            <button type="button" wire:click="$set('viewMode', 'leaflet')"
                                class="w-full flex items-center justify-center gap-2 py-3 px-4 rounded-xl text-xs font-bold btn-map-active hover:opacity-90 active:scale-[0.98] transition shadow-md">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-.553-.894L15 4m0 13V4m0 0L9 7" /></svg>
                                <span>Switch to OpenStreetMap View</span>
                            </button>
                        @endif
                    </div>
                </div>
            @endif
        </div>

    </div>

</div>

@push('scripts')
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>
<script>
    document.addEventListener('livewire:navigated', setupStudentLeaflet);
    document.addEventListener('DOMContentLoaded', setupStudentLeaflet);

    let studentLeafletInstance = null;

    function setupStudentLeaflet() {
        const mapEl = document.getElementById('leaflet-map');
        if (!mapEl || studentLeafletInstance) return;

        const vesitCoords = [19.0461, 72.8890];

        try {
            studentLeafletInstance = L.map('leaflet-map', {
                center: vesitCoords,
                zoom: 17.5,
                zoomControl: true
            });

            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                maxZoom: 19,
                attribution: '&copy; OpenStreetMap contributors • VESIT Spatial'
            }).addTo(studentLeafletInstance);

            const markers = @json($leafletLocations);

            markers.forEach(loc => {
                const marker = L.circleMarker([loc.lat, loc.lng], {
                    radius: 9,
                    fillColor: '#d2e823',
                    color: '#0f172a',
                    weight: 2.5,
                    opacity: 1,
                    fillOpacity: 0.95
                }).addTo(studentLeafletInstance);

                marker.bindPopup(`
                    <div style="font-family:'DM Sans',sans-serif; min-width:180px; padding:4px;">
                        <span style="background:#0f172a; color:#d2e823; font-size:10px; font-weight:800; padding:2px 6px; border-radius:4px;">${loc.code}</span>
                        <h4 style="margin:6px 0 2px 0; font-size:13px; font-weight:800; color:#0f172a;">${loc.name}</h4>
                        <p style="margin:0 0 6px 0; font-size:11px; color:#64748b;">${loc.category.toUpperCase()}</p>
                        <p style="margin:0; font-size:11px; color:#334155; line-height:1.4;">${loc.description}</p>
                    </div>
                `);

                marker.on('click', () => {
                    @this.selectRoom(loc.id);
                });
            });

            setTimeout(() => {
                studentLeafletInstance.invalidateSize();
            }, 300);
        } catch (e) {
            console.warn('Teacher Leaflet error:', e);
        }
    }

    Livewire.hook('commit', ({ succeed }) => {
        succeed(() => {
            setTimeout(() => {
                if (studentLeafletInstance) {
                    studentLeafletInstance.invalidateSize();
                } else {
                    setupStudentLeaflet();
                }
            }, 150);
        });
    });
</script>
@endpush
