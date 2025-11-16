📘 Booking System — Laravel 11 (Performance-Optimized)

A fully functional Booking Management System built using Laravel 11, featuring advanced time-slot conflict detection, email verification, and high-performance booking validation capable of handling millions of records.

🚀 Features
🔐 Authentication

User registration with First Name, Last Name, Email & Password.

Email verification before login.

Login disabled until email is verified.

Logout included.

Breeze-based starter.

📅 Booking Module
Booking Form Fields:

Customer Name

Customer Email

Booking Date

Booking Type

Full Day

Half Day

Custom Time

Booking Slot (First Half / Second Half) → Visible only for Half Day

From Time / To Time → Visible only for Custom

Dynamic UI Logic:

Frontend validation

Time fields auto-toggle based on booking type

Real-time form validation

AJAX submission

Proper error display

🧠 Backend Validation & Business Rules
🟦 Booking Types
Type	Meaning
Full Day	00:00 – 23:59
Half Day – First Half	00:00 – 11:59
Half Day – Second Half	12:00 – 23:59
Custom	User-defined times
🛑 Overlap Prevention Logic
A booking is blocked if:

Full Day exists on the same date

Requested half-day overlaps any custom/full-day

Custom time overlaps any custom/full-day/half-day

Half-day slot overlaps custom/full-day

SQL Logic:
NOT (end_time <= ? OR start_time >= ?)


Smart, fast, index-supported.

⚡ High Performance Design
✔ Redis Distributed Lock

Prevents race-condition double-booking during high load.

✔ MySQL Indexing

booking_date

start_time

end_time

booking_type

customer_email

✔ Query-level Optimization

Only checks bookings for that date (huge performance gain).

🏗 Tech Stack

Laravel 11

MySQL

Redis

Tailwind CSS

Breeze Auth

AJAX (Fetch API)

📥 Installation
git clone <repo-url>
cd booking-system

composer install
cp .env.example .env
php artisan key:generate

Configure .env:
DB_DATABASE=booking
DB_USERNAME=root
DB_PASSWORD=

QUEUE_CONNECTION=sync
CACHE_STORE=redis
SESSION_DRIVER=redis

🔧 Migrate & Seed
php artisan migrate


(Optional for test users)

php artisan db:seed --class=UserSeeder

▶ Run Application
php artisan serve

📌 API Endpoint
POST /bookings
Payload:
{
  "customer_name": "John Doe",
  "customer_email": "john@example.com",
  "booking_date": "2025-01-20",
  "booking_type": "custom",
  "from_time": "10:00",
  "to_time": "12:00"
}

🧪 Validation Errors (Example)
{
  "status": "error",
  "message": "The booking overlaps with an existing booking."
}

📸 Screenshots

(Add your screenshots here)

👤 Author

Charmi Mistry
PHP & Laravel Developer