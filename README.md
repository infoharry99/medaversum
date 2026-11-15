# medaversum

# Medhaversum Gurukulam &bull; Complete CRM & Management System

> **A Universe of Infinite Potential**  
> Centralized enterprise operations platform for Medhaversum Gurukulam: student admissions, neuroscience courses, fee ledger, biometric/daily attendance, cognitive progress, and certificate issuance.

---

## 🌟 Key Highlights & Modules

- **Executive Dashboard:** Live real-time KPIs (Total Students, Active Students, Revenue Collected, Installment Dues, Daily Collections, and Chart.js analytics).
- **Students Master:** Comprehensive student dossier, unique student ID generator (`MG-2026-XXXX`), profile photos, multi-tab progress tracking, and downloadable PDF dossiers.
- **Enquiry & Admissions Pipeline:** Multi-stage enquiry conversion workflow (`New` $\rightarrow$ `Contacted` $\rightarrow$ `Follow-up` $\rightarrow$ `Interested` $\rightarrow$ `Converted`), counseling records, and one-click student admission.
- **Courses & Curriculum:**
  - *Subconscious Mind Activation* (₹25,000)
  - *Photographic Memory* (₹20,000)
  - *Brain Waves Mapping* (₹18,000)
- **Batches & Timetable:** Cohorts, morning/evening schedules, trainer allocations, seat capacities, and calendar view.
- **Fees & Financial Ledger:** Installment tracking, partial/full payments, payment receipts with QR/barcode references, GST-compliant downloadable PDF receipts, and audit adjustments.
- **Attendance Management:** Dual-tracking modules for students and faculty staff with monthly percentages and leave logs.
- **Cognitive Progress & Certification:** Multi-parameter assessment tracking, progress comparison, certificate generator with tamper-proof verification codes (`/verify/{code}`).
- **Staff & Faculty Directory:** Departmental faculty roster, roles, designations, and payroll tracking.
- **Centralized Media Library:** Secure categorization for workshops, student media, classroom activities, and certificates.
- **Reports & Business Intelligence:** Filterable, exportable CSV/PDF reports across admissions, finances, attendance, and faculty performance.

---

## 🛠️ Technology Stack

- **Backend:** Laravel 12.x, PHP 8.2+
- **Database:** MySQL / MariaDB (UTF-8 MB4)
- **Frontend:** Blade, Bootstrap 5.3, JavaScript, jQuery
- **Typography:** Plus Jakarta Sans
- **Charts & Graphs:** Chart.js
- **PDF Generation:** Barryvdh Laravel DomPDF
- **Alerts & Modals:** SweetAlert2 & Bootstrap Modals

---

## 🚀 Quick Setup & Installation

1. **Clone the repository:**
   ```bash
   git clone https://github.com/infoharry99/medaversum.git
   cd medaversum
   ```

2. **Install PHP & Frontend Dependencies:**
   ```bash
   composer install
   npm install && npm run build
   ```

3. **Configure Environment:**
   ```bash
   cp .env.example .env
   php artisan key:generate
   ```

4. **Database Migration & Seeding:**
   ```bash
   # Configure DB_DATABASE, DB_USERNAME, DB_PASSWORD in .env
   php artisan migrate --seed
   ```

5. **Create Storage Symbolic Link:**
   ```bash
   php artisan storage:link
   ```

6. **Start Local Server:**
   ```bash
   php artisan serve
   ```
   Open `http://127.0.0.1:8000` in your web browser.

---

## 🔐 Default Demo Accounts

| Role | Email | Password |
| :--- | :--- | :--- |
| **Super Admin** | `admin@medaversum.com` | `password` |
| **Academic Counselor** | `pooja@medaversum.com` | `password` |
| **Senior Neuro Trainer** | `shastri@medaversum.com` | `password` |
| **Finance Manager** | `rohit@medaversum.com` | `password` |

---

## 🧪 Automated Testing

Execute the comprehensive test suite (16 tests, 55 assertions covering features, permissions, and PDF generators):
```bash
php artisan test
```

---

&copy; 2026 **Medhaversum Gurukulam**. All rights reserved.
