# MediSlot — Doctor Appointment Booking System

A full-stack internship project: **HTML + CSS + JavaScript + PHP + MySQL (phpMyAdmin)**.

Patients can register, search doctors by specialization, and book a time slot.
Admins can add/edit/delete doctors and confirm, complete, or cancel appointments.

---

## 1. Requirements

- XAMPP / WAMP / MAMP (Apache + MySQL + PHP 8+)
- A browser
- phpMyAdmin (comes with XAMPP/WAMP)

## 2. Folder setup

1. Install **XAMPP** (or WAMP) if you don't have it: https://www.apachefriends.org
2. Copy the whole `appointment-booking-system` folder into your server's web root:
   - XAMPP (Windows): `C:\xampp\htdocs\appointment-booking-system`
   - XAMPP (Mac/Linux): `/Applications/XAMPP/htdocs/appointment-booking-system`
   - WAMP: `C:\wamp64\www\appointment-booking-system`
3. Start **Apache** and **MySQL** from the XAMPP/WAMP control panel.

## 3. Create the database

1. Open **phpMyAdmin**: http://localhost/phpmyadmin
2. Click **Import** → **Choose File** → select `database.sql` from this project.
3. Click **Go**. This creates the `appointment_system` database with 3 tables
   (`users`, `doctors`, `appointments`) and sample doctors.

   *(Alternative: open the SQL tab in phpMyAdmin, paste the contents of
   `database.sql`, and click Go.)*

## 4. Configure the DB connection

Open `config.php` and check these values match your MySQL setup
(defaults below work for a fresh XAMPP install):

```php
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'appointment_system');
```

## 5. Run the project

Open your browser and visit:

```
http://localhost/appointment-booking-system/
```

## 6. Login details

**Patient** — register your own account via the "Sign Up" page.

**Admin** (pre-seeded):
```
Email:    admin@medislot.com
Password: admin123
```

> If the admin login fails (some PHP builds hash passwords slightly
> differently), just fix it in phpMyAdmin:
> 1. Go to the `users` table → find the admin row → **Edit**.
> 2. Set the `password` column to the output of this one-time script.
>    Create a file `hash.php` anywhere in the project with:
>    ```php
>    <?php echo password_hash('admin123', PASSWORD_DEFAULT);
>    ```
>    Visit it in the browser, copy the hash, paste it into the `password`
>    field in phpMyAdmin, then delete `hash.php`.

## 7. Project structure

```
appointment-booking-system/
├── database.sql              # Import this in phpMyAdmin
├── config.php                # DB connection + session helpers
├── index.php                 # Landing page
├── register.php               # Patient sign up
├── login.php                  # Patient + Admin login
├── logout.php
├── doctors.php                # Browse / search / filter doctors
├── book_appointment.php       # Booking form for a chosen doctor
├── my_appointments.php        # Patient's bookings + cancel
├── cancel_appointment.php
├── css/style.css              # All styling (design system)
├── js/script.js               # Validation, filters, nav toggle
├── includes/
│   ├── header.php             # Shared navbar
│   └── footer.php             # Shared footer
└── admin/
    ├── dashboard.php          # Stats overview
    ├── manage_doctors.php     # Add / edit / delete doctors
    └── manage_appointments.php# Confirm / complete / cancel bookings
```

## 8. How the booking flow works

1. Patient registers/logs in (`users` table, `role = 'patient'`).
2. Patient browses `doctors.php`, filters by specialization, clicks **Book slot**.
3. `book_appointment.php` shows the doctor's fee/availability and a date+time
   form. On submit, it checks that slot isn't already taken, then inserts a
   row into `appointments` with `status = 'pending'`.
4. Admin logs in (`role = 'admin'`), opens **Manage Appointments**, and can
   change status: `pending → confirmed → completed`, or `cancelled`.
5. Patient sees the live status any time on `my_appointments.php` and can
   cancel a pending/confirmed booking themselves.

## 9. Features covered (good for your report/viva)

- Session-based authentication with hashed passwords (`password_hash` / `password_verify`)
- Role-based access control (patient vs admin) via `require_login()` / `require_admin()`
- Prepared statements everywhere (SQL injection protection)
- Server-side + client-side form validation
- Double-booking prevention (same doctor/date/time check)
- Admin CRUD for doctors, appointment status workflow
- Responsive, accessible UI with a consistent design system (custom CSS, no framework)
- Live client-side search/filter for the doctor directory (vanilla JS)

## 10. Ideas to extend (bonus points)

- Email/SMS reminders (PHPMailer)
- Doctor photo upload (there's already an `uploads/` folder and a `photo` column ready)
- Pagination on the doctor list
- Appointment calendar view (FullCalendar.js)
- Patient profile edit page
