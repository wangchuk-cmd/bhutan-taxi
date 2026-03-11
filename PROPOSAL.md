# Bhutan Intercity Taxi Booking System (BITBS)
## Project Proposal & Technical Documentation

---

## Table of Contents

1. [Executive Summary](#1-executive-summary)
2. [Project Overview](#2-project-overview)
3. [Problem Statement](#3-problem-statement)
4. [Proposed Solution](#4-proposed-solution)
5. [System Architecture](#5-system-architecture)
6. [User Roles & Permissions](#6-user-roles--permissions)
7. [Features & Functionality](#7-features--functionality)
8. [Database Design](#8-database-design)
9. [Technical Stack](#9-technical-stack)
10. [Security Implementation](#10-security-implementation)
11. [Payment Integration](#11-payment-integration)
12. [Installation Guide](#12-installation-guide)
13. [API Endpoints](#13-api-endpoints)
14. [Future Enhancements](#14-future-enhancements)
15. [Project Timeline](#15-project-timeline)
16. [Budget Estimation](#16-budget-estimation)
17. [Conclusion](#17-conclusion)

---

## 1. Executive Summary

The **Bhutan Intercity Taxi Booking System (BITBS)** is a Progressive Web Application (PWA) designed to revolutionize intercity transportation in Bhutan. The system connects passengers with registered taxi drivers, enabling seamless booking of shared or private taxi rides between Bhutan's 20 Dzongkhags (districts).

### Key Highlights:
- **Multi-role Platform**: Supports Passengers, Drivers, and Administrators
- **Dual Booking Mode**: Shared rides (per-seat) and Full Taxi hiring
- **Real-time Management**: Live trip scheduling and booking management
- **Secure Payments**: Integration with Bhutanese payment gateways (mBoB, BNB)
- **PWA Technology**: Works offline, installable on mobile devices
- **Bilingual Support**: Ready for Dzongkha and English interfaces

### Project Information:
| Attribute | Details |
|-----------|---------|
| Project Name | Bhutan Intercity Taxi Booking System |
| Version | 1.0.0 |
| Technology | Laravel 12.x (PHP 8.2+) |
| Database | MySQL 8.0 |
| Platform | Progressive Web Application (PWA) |
| Target Users | Bhutanese Citizens, Tourists, Taxi Drivers |

---

## 2. Project Overview

### 2.1 Vision
To create a safe, reliable, and efficient intercity transportation booking platform that connects travelers across all 20 Dzongkhags of Bhutan.

### 2.2 Mission
- Digitize the traditional taxi booking process
- Provide fair pricing and transparent transactions
- Ensure passenger safety through verified drivers
- Support the local taxi business ecosystem

### 2.3 Scope
The system covers:
- All 20 Dzongkhags of Bhutan
- Intercity taxi services (not intra-city)
- Shared and private taxi bookings
- Driver registration and verification
- Online and cash payment options
- Administrative oversight and reporting

### 2.4 Dzongkhags Covered
```
1. Bumthang          11. Punakha
2. Chhukha           12. Samdrup Jongkhar
3. Dagana            13. Samtse
4. Gasa              14. Sarpang
5. Haa               15. Thimphu
6. Lhuentse          16. Trashigang
7. Mongar            17. Trashiyangtse
8. Paro              18. Trongsa
9. Pemagatshel       19. Tsirang
10. Zhemgang         20. Wangdue Phodrang
```

---

## 3. Problem Statement

### 3.1 Current Challenges

**For Passengers:**
- No centralized platform for finding intercity taxis
- Uncertain pricing leading to overcharging
- Difficulty finding shared rides to split costs
- No guarantee of driver verification or safety
- Limited information about departure times

**For Drivers:**
- Cannot efficiently fill empty seats
- No advance booking system
- Revenue loss from empty return trips
- No platform to build customer trust

**For Authorities:**
- No oversight of intercity taxi operations
- Difficulty tracking complaints
- No data for transportation planning

### 3.2 Impact
- Inefficient transportation network
- Higher costs for travelers
- Reduced income for drivers
- Safety concerns for passengers

---

## 4. Proposed Solution

### 4.1 Solution Overview

BITBS addresses these challenges through a comprehensive digital platform:

```
┌─────────────────────────────────────────────────────────────┐
│                  BITBS ECOSYSTEM                            │
├─────────────────────────────────────────────────────────────┤
│                                                              │
│   ┌───────────┐    ┌───────────┐    ┌───────────┐          │
│   │ PASSENGER │◄──►│  SYSTEM   │◄──►│  DRIVER   │          │
│   │    APP    │    │  SERVER   │    │    APP    │          │
│   └───────────┘    └─────┬─────┘    └───────────┘          │
│                          │                                   │
│                    ┌─────▼─────┐                            │
│                    │   ADMIN   │                            │
│                    │   PANEL   │                            │
│                    └───────────┘                            │
│                                                              │
└─────────────────────────────────────────────────────────────┘
```

### 4.2 Key Benefits

| Stakeholder | Benefits |
|-------------|----------|
| **Passengers** | Easy booking, transparent pricing, verified drivers, seat-sharing option |
| **Drivers** | Advance bookings, maximized seat occupancy, digital payment collection |
| **Administrators** | Full oversight, data analytics, complaint management |
| **Government** | Transportation data, regulatory compliance, revenue tracking |

---

## 5. System Architecture

### 5.1 Architecture Diagram

```
┌─────────────────────────────────────────────────────────────────┐
│                        PRESENTATION LAYER                        │
├─────────────────────────────────────────────────────────────────┤
│  ┌──────────────┐  ┌──────────────┐  ┌──────────────┐          │
│  │  Passenger   │  │    Driver    │  │    Admin     │          │
│  │   Views      │  │    Views     │  │    Views     │          │
│  │ (Blade/PWA)  │  │ (Blade/PWA)  │  │   (Blade)    │          │
│  └──────────────┘  └──────────────┘  └──────────────┘          │
└─────────────────────────────────────────────────────────────────┘
                              │
                              ▼
┌─────────────────────────────────────────────────────────────────┐
│                        APPLICATION LAYER                         │
├─────────────────────────────────────────────────────────────────┤
│  ┌──────────────┐  ┌──────────────┐  ┌──────────────┐          │
│  │    Auth      │  │   Booking    │  │   Payment    │          │
│  │  Controller  │  │  Controller  │  │  Controller  │          │
│  └──────────────┘  └──────────────┘  └──────────────┘          │
│  ┌──────────────┐  ┌──────────────┐  ┌──────────────┐          │
│  │   Driver     │  │    Admin     │  │ Notification │          │
│  │  Controller  │  │  Controller  │  │  Controller  │          │
│  └──────────────┘  └──────────────┘  └──────────────┘          │
└─────────────────────────────────────────────────────────────────┘
                              │
                              ▼
┌─────────────────────────────────────────────────────────────────┐
│                         BUSINESS LAYER                           │
├─────────────────────────────────────────────────────────────────┤
│  ┌──────────────┐  ┌──────────────┐  ┌──────────────┐          │
│  │    User      │  │    Trip      │  │   Booking    │          │
│  │    Model     │  │    Model     │  │    Model     │          │
│  └──────────────┘  └──────────────┘  └──────────────┘          │
│  ┌──────────────┐  ┌──────────────┐  ┌──────────────┐          │
│  │   Payment    │  │   Payout     │  │  Complaint   │          │
│  │    Model     │  │    Model     │  │    Model     │          │
│  └──────────────┘  └──────────────┘  └──────────────┘          │
└─────────────────────────────────────────────────────────────────┘
                              │
                              ▼
┌─────────────────────────────────────────────────────────────────┐
│                          DATA LAYER                              │
├─────────────────────────────────────────────────────────────────┤
│                     MySQL Database                               │
│  ┌────────┐ ┌────────┐ ┌────────┐ ┌────────┐ ┌────────┐       │
│  │ users  │ │drivers │ │ trips  │ │bookings│ │payments│       │
│  └────────┘ └────────┘ └────────┘ └────────┘ └────────┘       │
│  ┌────────┐ ┌────────┐ ┌────────┐ ┌────────┐ ┌────────┐       │
│  │payouts │ │routes  │ │notifs  │ │complnts│ │settings│       │
│  └────────┘ └────────┘ └────────┘ └────────┘ └────────┘       │
└─────────────────────────────────────────────────────────────────┘
```

### 5.2 Technology Components

| Layer | Technology | Purpose |
|-------|------------|---------|
| Frontend | Blade Templates, Bootstrap 5, JavaScript | User Interface |
| PWA | Service Worker, Web Manifest | Offline capability, Installation |
| Backend | Laravel 12.x, PHP 8.2 | Business Logic, API |
| Database | MySQL 8.0 | Data Persistence |
| Cache | Laravel Cache (File/Redis) | Performance Optimization |
| Session | Database Sessions | User State Management |

---

## 6. User Roles & Permissions

### 6.1 Role Hierarchy

```
                    ┌─────────────┐
                    │    ADMIN    │
                    │ (Full Access)│
                    └──────┬──────┘
                           │
           ┌───────────────┼───────────────┐
           │               │               │
    ┌──────▼──────┐ ┌──────▼──────┐ ┌──────▼──────┐
    │   DRIVER    │ │  PASSENGER  │ │    GUEST    │
    │ (Trip Mgmt) │ │  (Booking)  │ │ (View Only) │
    └─────────────┘ └─────────────┘ └─────────────┘
```

### 6.2 Permission Matrix

| Feature | Guest | Passenger | Driver | Admin |
|---------|:-----:|:---------:|:------:|:-----:|
| View Trips | ✓ | ✓ | ✓ | ✓ |
| Search Trips | ✓ | ✓ | ✓ | ✓ |
| Book Trips | ✗ | ✓ | ✗ | ✓ |
| Cancel Booking | ✗ | ✓ | ✗ | ✓ |
| Make Payment | ✗ | ✓ | ✗ | ✓ |
| Create Trips | ✗ | ✗ | ✓ | ✓ |
| Manage Own Trips | ✗ | ✗ | ✓ | ✓ |
| View Payouts | ✗ | ✗ | ✓ | ✓ |
| Verify Drivers | ✗ | ✗ | ✗ | ✓ |
| Manage Routes | ✗ | ✗ | ✗ | ✓ |
| View Reports | ✗ | ✗ | ✗ | ✓ |
| Manage Settings | ✗ | ✗ | ✗ | ✓ |
| Process Payouts | ✗ | ✗ | ✗ | ✓ |
| Handle Complaints | ✗ | ✗ | ✗ | ✓ |

### 6.3 Role Descriptions

#### 6.3.1 Guest User
- Can browse available trips
- Can search by origin, destination, and date
- Must register to book

#### 6.3.2 Passenger
- Full trip browsing and searching
- Book shared or full taxi rides
- Input multiple passenger details
- Make payments (mBoB, BNB, Cash)
- View booking history
- Download payment receipts (PDF)
- Cancel bookings with refund eligibility
- Submit feedback/complaints
- Receive notifications

#### 6.3.3 Driver
- Create and manage trips
- Set pricing (per-seat and full taxi)
- View passenger lists
- Track earnings and payouts
- Update profile and vehicle info
- Submit feedback/complaints

#### 6.3.4 Administrator
- Complete system oversight
- Verify driver registrations
- Manage routes (distances, estimated times)
- Create bookings for passengers (admin booking)
- Process driver payouts
- Handle complaints and feedback
- Generate reports (CSV export)
- Configure system settings
- Manage user roles

---

## 7. Features & Functionality

### 7.1 Passenger Features

#### 7.1.1 Trip Search & Discovery
```
┌─────────────────────────────────────────────────┐
│              TRIP SEARCH FORM                   │
├─────────────────────────────────────────────────┤
│  From: [Thimphu        ▼]                       │
│  To:   [Paro           ▼]                       │
│  Date: [2026-03-07     📅]                      │
│  Seats:[2              ▼]                       │
│                                                  │
│  [🔍 Search Available Trips]                    │
└─────────────────────────────────────────────────┘
```

**Features:**
- Search by origin/destination Dzongkhag
- Filter by date
- Specify number of seats needed
- Real-time trip availability
- View driver ratings and vehicle info

#### 7.1.2 Booking System

**Shared Booking (Per-Seat):**
- Book 1 or more seats
- Pay only for seats booked
- Other seats available for other passengers
- Cost-effective option

**Full Taxi Booking:**
- Hire entire vehicle
- No other passengers
- Flexible for groups
- Premium pricing

**Passenger Information Collection:**
```json
{
  "passengers_info": [
    {
      "name": "Dorji Wangchuk",
      "phone": "17123456",
      "cid": "10101001234"
    },
    {
      "name": "Pema Choden",
      "phone": "17234567",
      "cid": "10101001235"
    }
  ]
}
```

#### 7.1.3 Payment System
- **Payment Timeout**: Configurable timeout (default 300 seconds)
- **Live Countdown**: Visual timer showing remaining time
- **Multiple Methods**: mBoB, BNB, Cash
- **Auto-expiration**: Bookings expire if payment not completed

#### 7.1.4 Receipt Generation
- PDF download capability
- Includes all booking details
- QR code for verification
- Professional format

### 7.2 Driver Features

#### 7.2.1 Trip Management
```
┌─────────────────────────────────────────────────┐
│              CREATE NEW TRIP                    │
├─────────────────────────────────────────────────┤
│  From: [Thimphu        ▼]                       │
│  To:   [Paro           ▼]                       │
│  Date & Time: [2026-03-07 08:00]               │
│  Total Seats: [4]                               │
│  Price/Seat:  [Nu. 500]                        │
│  Full Taxi:   [Nu. 1,800] (auto-calculated)    │
│                                                  │
│  [✓ Create Trip]                               │
└─────────────────────────────────────────────────┘
```

**Features:**
- Create unlimited trips
- Set departure date/time
- Configure pricing
- Auto-calculate full taxi price
- View passenger lists
- Cancel trips with notifications

#### 7.2.2 Earnings & Payouts
- Track trip earnings
- View service charge deductions
- Request payouts
- View payout history

### 7.3 Admin Features

#### 7.3.1 Dashboard Analytics
```
┌────────────────────────────────────────────────────────────┐
│                    ADMIN DASHBOARD                          │
├────────────────────────────────────────────────────────────┤
│  ┌──────────┐  ┌──────────┐  ┌──────────┐  ┌──────────┐   │
│  │ Trips    │  │ Bookings │  │ Revenue  │  │ Drivers  │   │
│  │   125    │  │    89    │  │ Nu.45K   │  │    32    │   │
│  │ Active   │  │ This Week│  │ This Week│  │ Verified │   │
│  └──────────┘  └──────────┘  └──────────┘  └──────────┘   │
│                                                             │
│  [Revenue Chart]         [Booking Trends]                  │
│  ████████████            📊 Line Graph                     │
│  ████████                                                  │
│  ██████                                                    │
└────────────────────────────────────────────────────────────┘
```

#### 7.3.2 Reports & Export
**Available Reports:**
- Trips Report (CSV)
- Bookings Report (CSV)
- Payments Report (CSV)
- Drivers Report (CSV)
- Payouts Report (CSV)

**Date Filtering Options:**
| Filter Type | Usage |
|-------------|-------|
| Single Date | Enter Date From only → exact date |
| Date Range | Enter Date From + Date To → inclusive range |
| Specific Days | Enter days (e.g., 7,9,10) → non-consecutive dates |

#### 7.3.3 Refund Management
- Track cancelled bookings with refund requests
- View pending refunds badge count
- Mark refunds as processed
- Update booking/payment statuses

#### 7.3.4 Settings Configuration
| Setting | Description | Default |
|---------|-------------|---------|
| Service Charge (%) | Platform fee deducted from driver earnings | 10% |
| Payment Timeout (seconds) | Time allowed for payment completion | 300 |
| Min Booking Lead Time (hours) | How far in advance bookings must be made | 2 |

---

## 8. Database Design

### 8.1 Entity Relationship Diagram

```
┌─────────────┐         ┌─────────────┐         ┌─────────────┐
│    USERS    │         │   DRIVERS   │         │   ROUTES    │
├─────────────┤         ├─────────────┤         ├─────────────┤
│ id          │◄───────►│ id          │         │ id          │
│ name        │    1:1  │ user_id     │         │ origin      │
│ phone_number│         │ license_no  │         │ destination │
│ email       │         │ plate_no    │         │ distance_km │
│ password    │         │ vehicle_type│         │ est_time    │
│ role        │         │ verified    │         └──────┬──────┘
└──────┬──────┘         │ active      │                │
       │                └──────┬──────┘                │
       │                       │                       │
       │                       │ 1:N                   │
       │                       ▼                       │
       │                ┌─────────────┐                │
       │                │    TRIPS    │◄───────────────┘
       │                ├─────────────┤         N:1
       │                │ id          │
       │         1:N    │ driver_id   │
       │     ┌─────────►│ route_id    │
       │     │          │ origin      │
       │     │          │ destination │
       │     │          │ departure   │
       │     │          │ total_seats │
       │     │          │ available   │
       │     │          │ price_seat  │
       │     │          │ full_price  │
       │     │          │ status      │
       │     │          └──────┬──────┘
       │     │                 │
       │     │                 │ 1:N
       │     │                 ▼
       │     │          ┌─────────────┐         ┌─────────────┐
       │     │          │  BOOKINGS   │────────►│  PAYMENTS   │
       │     │          ├─────────────┤   1:N   ├─────────────┤
       └─────┼─────────►│ id          │         │ id          │
             │     N:1  │ trip_id     │         │ booking_id  │
             │          │ passenger_id│         │ amount      │
             │          │ pass_info   │         │ status      │
             │          │ booking_type│         │ method      │
             │          │ seats_booked│         │ txn_time    │
             │          │ pay_status  │         └─────────────┘
             │          │ pay_time    │
             │          │ booking_time│
             │          │ cancel_time │
             │          │ refund_stat │
             │          │ status      │
             │          └─────────────┘
             │
             │          ┌─────────────┐
             └─────────►│   PAYOUTS   │
                   1:N  ├─────────────┤
                        │ id          │
                        │ driver_id   │
                        │ trip_id     │
                        │ total_amount│
                        │ service_chg │
                        │ payout_amt  │
                        │ status      │
                        │ paid_at     │
                        └─────────────┘

┌─────────────┐         ┌─────────────┐         ┌─────────────┐
│ COMPLAINTS  │         │NOTIFICATIONS│         │  SETTINGS   │
├─────────────┤         ├─────────────┤         ├─────────────┤
│ id          │         │ id          │         │ id          │
│ user_id     │         │ user_id     │         │ key         │
│ type        │         │ title       │         │ value       │
│ subject     │         │ message     │         │ description │
│ message     │         │ type        │         └─────────────┘
│ status      │         │ read_at     │
└─────────────┘         └─────────────┘
```

### 8.2 Table Specifications

#### 8.2.1 Users Table
| Column | Type | Description |
|--------|------|-------------|
| id | BIGINT PK | Auto-increment primary key |
| name | VARCHAR(255) | Full name (validated format) |
| phone_number | VARCHAR(8) UNIQUE | Bhutanese phone (17/77 prefix) |
| email | VARCHAR(255) NULLABLE | Optional email |
| password | VARCHAR(255) | Bcrypt hashed |
| role | ENUM | passenger, driver, admin |
| remember_token | VARCHAR(100) | For "remember me" |
| created_at | TIMESTAMP | Registration date |
| updated_at | TIMESTAMP | Last update |

#### 8.2.2 Drivers Table
| Column | Type | Description |
|--------|------|-------------|
| id | BIGINT PK | Auto-increment primary key |
| user_id | BIGINT FK | References users.id |
| license_number | VARCHAR(50) UNIQUE | Driving license number |
| taxi_plate_number | VARCHAR(20) UNIQUE | Vehicle plate number |
| vehicle_type | VARCHAR(50) | sedan, suv, van |
| verified | BOOLEAN | Admin verification status |
| active | BOOLEAN | Account active status |
| created_at | TIMESTAMP | Registration date |
| updated_at | TIMESTAMP | Last update |

#### 8.2.3 Trips Table
| Column | Type | Description |
|--------|------|-------------|
| id | BIGINT PK | Auto-increment primary key |
| driver_id | BIGINT FK | References drivers.id |
| route_id | BIGINT FK NULLABLE | References routes.id |
| origin_dzongkhag | VARCHAR(100) | Departure location |
| destination_dzongkhag | VARCHAR(100) | Arrival location |
| departure_datetime | DATETIME | Trip start time |
| total_seats | INT | Total available seats |
| available_seats | INT | Currently available |
| price_per_seat | DECIMAL(10,2) | Per-seat pricing |
| full_taxi_price | DECIMAL(10,2) | Full vehicle price |
| status | ENUM | active, cancelled, completed |
| created_at | TIMESTAMP | Trip creation date |
| updated_at | TIMESTAMP | Last update |

#### 8.2.4 Bookings Table
| Column | Type | Description |
|--------|------|-------------|
| id | BIGINT PK | Auto-increment primary key |
| trip_id | BIGINT FK | References trips.id |
| passenger_id | BIGINT FK | References users.id |
| passengers_info | JSON | Array of passenger details |
| booking_type | ENUM | shared, full |
| seats_booked | INT | Number of seats |
| total_amount | DECIMAL(10,2) | Total booking cost |
| payment_status | ENUM | pending, paid, failed, expired |
| payment_time | TIMESTAMP NULLABLE | When payment completed |
| booking_time | TIMESTAMP | When booking created |
| cancellation_time | TIMESTAMP NULLABLE | When cancelled |
| refund_status | ENUM | none, pending, refunded |
| status | ENUM | pending, confirmed, completed, cancelled |
| created_at | TIMESTAMP | Record creation |
| updated_at | TIMESTAMP | Last update |

#### 8.2.5 Payments Table
| Column | Type | Description |
|--------|------|-------------|
| id | BIGINT PK | Auto-increment primary key |
| booking_id | BIGINT FK | References bookings.id |
| amount | DECIMAL(10,2) | Payment amount |
| status | ENUM | pending, completed, failed, refunded |
| payment_method | ENUM | mbob, bnb, cash |
| transaction_id | VARCHAR(100) NULLABLE | External transaction ID |
| transaction_time | TIMESTAMP | Payment timestamp |
| created_at | TIMESTAMP | Record creation |
| updated_at | TIMESTAMP | Last update |

#### 8.2.6 Payouts Table
| Column | Type | Description |
|--------|------|-------------|
| id | BIGINT PK | Auto-increment primary key |
| driver_id | BIGINT FK | References drivers.id |
| trip_id | BIGINT FK | References trips.id |
| total_amount | DECIMAL(10,2) | Gross earnings |
| service_charge | DECIMAL(10,2) | Platform fee |
| payout_amount | DECIMAL(10,2) | Net to driver |
| status | ENUM | pending, completed |
| paid_at | TIMESTAMP NULLABLE | Payout date |
| created_at | TIMESTAMP | Record creation |
| updated_at | TIMESTAMP | Last update |

---

## 9. Technical Stack

### 9.1 Backend Technologies

| Technology | Version | Purpose |
|------------|---------|---------|
| PHP | 8.2+ | Server-side language |
| Laravel | 12.x | PHP Framework |
| Composer | 2.x | Dependency management |
| Eloquent ORM | - | Database abstraction |
| Laravel Sanctum | - | API authentication |

### 9.2 Frontend Technologies

| Technology | Version | Purpose |
|------------|---------|---------|
| Blade | - | Templating engine |
| Bootstrap | 5.3.2 | CSS framework |
| Bootstrap Icons | 1.11.x | Icon library |
| JavaScript (ES6) | - | Client-side logic |
| Fetch API | - | AJAX requests |

### 9.3 Database

| Technology | Version | Purpose |
|------------|---------|---------|
| MySQL | 8.0 | Relational database |
| phpMyAdmin | - | Database management |

### 9.4 Development Tools

| Tool | Purpose |
|------|---------|
| XAMPP | Local development server |
| VS Code | Code editor |
| Git | Version control |
| Artisan | Laravel CLI |
| npm | Frontend package management |

### 9.5 PWA Components

| Component | Purpose |
|-----------|---------|
| manifest.json | App metadata, icons |
| Service Worker | Offline caching |
| Icons (192x192, 512x512) | App icons |

---

## 10. Security Implementation

### 10.1 Authentication & Authorization

```php
// Middleware Protection
Route::middleware(['auth', 'admin'])->group(function () {
    // Admin routes protected
});

Route::middleware(['auth', 'driver'])->group(function () {
    // Driver routes protected
});
```

### 10.2 Security Features

| Feature | Implementation |
|---------|----------------|
| Password Hashing | Bcrypt (Laravel default) |
| CSRF Protection | @csrf tokens on all forms |
| XSS Prevention | Blade {{ }} auto-escaping |
| SQL Injection | Eloquent prepared statements |
| Session Security | Encrypted, database-stored |
| Route Protection | Middleware authentication |
| Role Verification | Custom middleware (admin, driver) |

### 10.3 Input Validation

```php
// Phone Number Validation
'phone_number' => [
    'required',
    'regex:/^(17|77)[0-9]{6}$/',  // Bhutanese format
    'unique:users'
]

// Name Validation
'name' => [
    'required',
    'regex:/^[A-Za-z\s]+$/',  // Letters and spaces only
    'min:2',
    'max:100'
]
```

### 10.4 Payment Security
- Payment timeout prevents indefinite pending bookings
- Transaction IDs logged for all payments
- Admin can track and resolve payment issues

---

## 11. Payment Integration

### 11.1 Supported Payment Methods

| Method | Type | Status |
|--------|------|--------|
| mBoB | Mobile Banking (BoB) | Integrated |
| BNB | Bhutan National Bank | Integrated |
| Cash | Physical payment | Supported |

### 11.2 Payment Flow

```
┌──────────────────────────────────────────────────────────────┐
│                    PAYMENT FLOW DIAGRAM                       │
├──────────────────────────────────────────────────────────────┤
│                                                               │
│  1. INITIATE          2. COUNTDOWN         3. VERIFY         │
│  ┌──────────┐         ┌──────────┐         ┌──────────┐     │
│  │ Booking  │───────►│ Payment  │───────►│ Confirm  │      │
│  │ Created  │         │  Page    │         │ Payment  │      │
│  └──────────┘         │ (Timer)  │         └────┬─────┘      │
│                       └────┬─────┘              │             │
│                            │                    ▼             │
│                            │              ┌──────────┐        │
│                            │              │ SUCCESS  │        │
│                            │              │ Booking  │        │
│                            │              │ Confirmed│        │
│                            │              └──────────┘        │
│                            │                                  │
│                            │ Timeout                          │
│                            ▼                                  │
│                       ┌──────────┐                           │
│                       │ EXPIRED  │                           │
│                       │ Booking  │                           │
│                       │ Cancelled│                           │
│                       └──────────┘                           │
│                                                               │
└──────────────────────────────────────────────────────────────┘
```

### 11.3 Payment Timeout Configuration

**Admin Settings:**
- Payment timeout can be configured (60-600 seconds)
- Default: 300 seconds (5 minutes)
- Visual countdown displayed to user
- Auto-expiration when timeout reached

---

## 12. Installation Guide

### 12.1 System Requirements

| Requirement | Minimum Version |
|-------------|-----------------|
| PHP | 8.2+ |
| MySQL | 8.0+ |
| Composer | 2.0+ |
| Node.js | 18.0+ (for assets) |
| Apache/Nginx | Latest stable |

### 12.2 Installation Steps

```bash
# 1. Clone the repository
git clone https://github.com/your-org/bhutan-taxi.git
cd bhutan-taxi

# 2. Install PHP dependencies
composer install

# 3. Install Node dependencies (optional, for frontend build)
npm install

# 4. Create environment file
cp .env.example .env

# 5. Generate application key
php artisan key:generate

# 6. Configure database in .env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=bhutan_taxi_system
DB_USERNAME=root
DB_PASSWORD=

# 7. Run migrations
php artisan migrate

# 8. Seed initial data (optional)
php artisan db:seed

# 9. Create storage link
php artisan storage:link

# 10. Start development server
php artisan serve
```

### 12.3 Production Deployment

```bash
# Optimize for production
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan optimize

# Set proper permissions
chmod -R 755 storage
chmod -R 755 bootstrap/cache
```

---

## 13. API Endpoints

### 13.1 Public Endpoints

| Method | Endpoint | Description |
|--------|----------|-------------|
| GET | / | Home page |
| GET | /search | Search trips |
| GET | /trip/{id} | Trip details |
| GET | /api/trips/search | JSON trip search |

### 13.2 Authentication Endpoints

| Method | Endpoint | Description |
|--------|----------|-------------|
| GET | /login | Login form |
| POST | /login | Process login |
| GET | /register | Registration form |
| POST | /register | Process registration |
| GET | /driver/register | Driver registration |
| POST | /driver/register | Process driver registration |
| POST | /logout | Logout user |

### 13.3 Passenger Endpoints

| Method | Endpoint | Description |
|--------|----------|-------------|
| GET | /booking/create/{tripId} | Booking form |
| POST | /booking/store | Create booking |
| GET | /my-bookings | List user bookings |
| GET | /booking/{id} | Booking details |
| GET | /booking/{id}/receipt | Download receipt |
| POST | /booking/{id}/cancel | Cancel booking |
| GET | /payment/{bookingId} | Payment page |
| POST | /payment/{bookingId}/complete | Complete payment |
| POST | /payment/{bookingId}/timeout | Payment timeout |
| GET | /notifications | User notifications |
| GET | /feedback | Feedback form |
| POST | /feedback | Submit feedback |

### 13.4 Driver Endpoints

| Method | Endpoint | Description |
|--------|----------|-------------|
| GET | /driver/dashboard | Driver dashboard |
| GET | /driver/trips | Driver's trips |
| GET | /driver/trips/create | Create trip form |
| POST | /driver/trips | Store new trip |
| GET | /driver/trips/{id}/edit | Edit trip form |
| PUT | /driver/trips/{id} | Update trip |
| POST | /driver/trips/{id}/cancel | Cancel trip |
| GET | /driver/trips/{id}/passengers | Passenger list |
| GET | /driver/payouts | Payouts list |
| GET | /driver/profile | Profile page |
| PUT | /driver/profile | Update profile |

### 13.5 Admin Endpoints

| Method | Endpoint | Description |
|--------|----------|-------------|
| GET | /admin/dashboard | Admin dashboard |
| GET | /admin/drivers | Manage drivers |
| POST | /admin/drivers/{id}/verify | Verify driver |
| GET | /admin/trips | Manage trips |
| GET | /admin/bookings | Manage bookings |
| GET | /admin/payouts | Manage payouts |
| POST | /admin/payouts/{id}/process | Process payout |
| GET | /admin/complaints | Manage complaints |
| GET | /admin/reports | Reports page |
| GET | /admin/reports/export/trips | Export trips CSV |
| GET | /admin/reports/search/trips | Search trips JSON |
| GET | /admin/settings | Settings page |
| POST | /admin/settings | Update settings |

---

## 14. Future Enhancements

### 14.1 Phase 2 Features

| Feature | Description | Priority |
|---------|-------------|----------|
| SMS Notifications | Send booking confirmations via SMS | High |
| Driver Ratings | Passengers rate drivers after trips | High |
| Real-time Tracking | GPS tracking of trips | Medium |
| Multi-language | Dzongkha language support | High |
| Push Notifications | PWA push notification support | Medium |

### 14.2 Phase 3 Features

| Feature | Description | Priority |
|---------|-------------|----------|
| Dynamic Pricing | Adjust prices based on demand | Medium |
| Loyalty Program | Reward frequent passengers | Low |
| Corporate Accounts | Business travel management | Medium |
| Analytics Dashboard | Advanced reporting tools | Medium |
| API for Partners | Third-party integrations | Low |

### 14.3 Mobile App

| Platform | Technology | Timeline |
|----------|------------|----------|
| Android | React Native / Flutter | Phase 4 |
| iOS | React Native / Flutter | Phase 4 |

---

## 15. Project Timeline

### 15.1 Development Phases

```
Phase 1: Foundation (Completed)
├── Database Design
├── User Authentication
├── Role-based Access
├── Basic CRUD Operations
└── Duration: 4 weeks

Phase 2: Core Features (Completed)
├── Trip Management
├── Booking System
├── Payment Integration
├── Driver Portal
└── Duration: 6 weeks

Phase 3: Admin & Reports (Completed)
├── Admin Dashboard
├── Reports & Export
├── Settings Management
├── Complaint Handling
└── Duration: 4 weeks

Phase 4: Enhancement (Current)
├── Performance Optimization
├── Security Hardening
├── PWA Implementation
├── User Testing
└── Duration: 3 weeks

Phase 5: Deployment
├── Production Setup
├── DNS Configuration
├── SSL Certificate
├── Go-Live
└── Duration: 1 week
```

### 15.2 Gantt Chart Overview

```
Week:  1  2  3  4  5  6  7  8  9  10 11 12 13 14 15 16 17 18
       ▓▓▓▓▓▓▓▓▓▓▓▓         Foundation
                  ▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓   Core Features
                                    ▓▓▓▓▓▓▓▓▓▓▓▓   Admin & Reports
                                                ▓▓▓▓▓▓▓  Enhancement
                                                      ▓▓   Deployment
```

---

## 16. Budget Estimation

### 16.1 Development Costs

| Item | Estimated Cost (Nu.) |
|------|---------------------|
| Backend Development | 150,000 |
| Frontend Development | 100,000 |
| Database Design | 30,000 |
| Testing & QA | 40,000 |
| Documentation | 20,000 |
| **Subtotal** | **340,000** |

### 16.2 Infrastructure Costs (Annual)

| Item | Est. Cost (Nu./Year) |
|------|---------------------|
| Cloud Hosting | 24,000 |
| Domain Name | 1,500 |
| SSL Certificate | Free (Let's Encrypt) |
| Backup Storage | 6,000 |
| **Subtotal** | **31,500** |

### 16.3 Maintenance Costs (Annual)

| Item | Est. Cost (Nu./Year) |
|------|---------------------|
| Bug Fixes | 30,000 |
| Security Updates | 20,000 |
| Feature Updates | 50,000 |
| Support | 24,000 |
| **Subtotal** | **124,000** |

### 16.4 Total Project Cost

| Category | Cost (Nu.) |
|----------|-----------|
| Development (One-time) | 340,000 |
| Infrastructure (Year 1) | 31,500 |
| Maintenance (Year 1) | 124,000 |
| **Grand Total (Year 1)** | **495,500** |

---

## 17. Conclusion

### 17.1 Summary

The **Bhutan Intercity Taxi Booking System** provides a comprehensive solution for:

✅ **Passengers**: Easy booking, transparent pricing, verified drivers
✅ **Drivers**: Digital platform, advance bookings, fair earnings
✅ **Administrators**: Full control, reporting, compliance
✅ **Government**: Data insights, regulated transportation

### 17.2 Key Deliverables

1. Fully functional web application (PWA)
2. Multi-role user management system
3. Complete booking and payment flow
4. Administrative control panel
5. Reporting and export capabilities
6. Technical documentation
7. Source code repository
8. Deployment guide

### 17.3 Contact Information

| Role | Contact |
|------|---------|
| Project Manager | [Name] |
| Lead Developer | [Name] |
| Technical Support | support@bhutantaxi.bt |
| General Inquiries | info@bhutantaxi.bt |

---

## Appendix A: Glossary

| Term | Definition |
|------|------------|
| **Dzongkhag** | District in Bhutan (20 total) |
| **PWA** | Progressive Web Application |
| **mBoB** | Mobile Bank of Bhutan |
| **BNB** | Bhutan National Bank |
| **CID** | Citizenship Identity Card |
| **Shared Booking** | Per-seat booking with other passengers |
| **Full Taxi** | Exclusive vehicle hire |
| **Payout** | Driver earnings minus service charge |
| **Refund** | Return of payment for cancelled booking |

---

## Appendix B: System Requirements Checklist

- [ ] PHP 8.2+ installed
- [ ] MySQL 8.0+ installed
- [ ] Composer 2.x installed
- [ ] Apache/Nginx configured
- [ ] SSL certificate (production)
- [ ] Backup system configured
- [ ] Email service configured
- [ ] Payment gateway credentials

---

## Appendix C: Testing Checklist

### Functional Testing
- [ ] User registration (passenger)
- [ ] User registration (driver)
- [ ] User login/logout
- [ ] Trip search functionality
- [ ] Booking creation (shared)
- [ ] Booking creation (full taxi)
- [ ] Payment processing
- [ ] Booking cancellation
- [ ] Receipt download
- [ ] Driver trip creation
- [ ] Admin dashboard access
- [ ] Report generation

### Security Testing
- [ ] SQL injection attempts
- [ ] XSS vulnerability checks
- [ ] CSRF token validation
- [ ] Password strength
- [ ] Session security
- [ ] Role access restrictions

---

**Document Version:** 1.0  
**Last Updated:** March 7, 2026  
**Authors:** Development Team  
**Status:** Approved

---

*© 2026 Bhutan Intercity Taxi Booking System. All rights reserved.*
