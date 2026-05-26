# Driver Rating System - Implementation Guide

## 📋 Overview
A complete passenger rating system for the Bhutan Taxi application. Customers can rate drivers 1-5 stars with an optional text review after completing a trip. The system automatically notifies drivers when they receive ratings.

## ✅ Features Implemented

### Core Functionality
- ⭐ 1-5 star rating system
- 📝 Optional text reviews (max 500 characters)
- 🔄 Update existing ratings
- 📧 Email notifications to drivers
- 🔒 Authorization controls (only passengers can rate their own trips)
- 📊 Average rating calculation and tracking
- ⏰ Only available after trip completion

### User Interface
- Interactive star selector with hover effects
- Character counter for reviews
- Trip details display on rating form
- Rating display on booking details page
- Trip completion status check before allowing ratings

### Performance Optimization
- Cached average_rating and rating_count in drivers table
- Automatic updates via model events
- Efficient queries with eager loading

## 📁 Files Created/Modified

### New Files
```
database/migrations/
  - 2026_03_06_214809_create_ratings_table.php
  - 2026_03_06_214810_add_rating_fields_to_drivers_table.php

app/Models/
  - Rating.php

app/Http/Controllers/
  - RatingController.php

app/Mail/
  - DriverRatingNotification.php

resources/views/
  - ratings/show.blade.php
  - emails/driver-rating-notification.blade.php
```

### Modified Files
```
app/Models/
  - Booking.php (added rating() relationship)
  - Driver.php (added ratings functionality)
  - User.php (added ratingsGiven() relationship)

routes/web.php (added rating routes)

resources/views/booking/show.blade.php (added rating button)
```

## 🚀 Setup Instructions

### 1. Run Migrations
```bash
php artisan migrate
```

This will:
- Create the `ratings` table
- Add `average_rating` and `rating_count` columns to drivers table

### 2. Clear Cache (Optional)
```bash
php artisan cache:clear
```

### 3. Testing

#### Manual Testing Flow
1. Create a booking for a past trip
2. Navigate to booking details
3. Click "Rate Driver" button
4. Select a rating (1-5 stars)
5. Optionally add a review
6. Submit the form
7. Verify:
   - Rating is saved
   - Email is sent to driver
   - Driver's average rating is updated
   - "Update Rating" button appears if you revisit

#### API Testing (if needed)
```
GET /api/drivers/{driver_id}/ratings - Get driver's ratings
GET /api/drivers/{driver_id}/profile - Get driver profile with ratings
```

## 🔧 API Reference

### RatingController Methods

#### `show($bookingId)`
Displays the rating form for a completed booking.
- **Authorization**: Only booking passenger
- **Checks**: Booking completed (trip in past), user owns booking
- **Response**: Blade view with rating form

#### `store(Request $request, $bookingId)`
Stores or updates a rating.
- **Validation**: rating (1-5), review (optional, max 500 chars)
- **Authorization**: Only booking passenger
- **Actions**: 
  - Creates/updates rating
  - Triggers email notification
  - Updates driver's cached ratings
- **Response**: Redirect to booking show with success message

#### `getDriverRatings($driverId)` [API]
Gets paginated ratings for a driver.
- **Response**: JSON with average_rating, rating_count, ratings array

#### `getDriverProfile($driverId)` [API]
Gets driver profile with rating stats and recent reviews.
- **Response**: JSON with driver info, average_rating, rating_count, recent_ratings

## 📊 Database Schema

### ratings table
```
id: bigint (primary key)
booking_id: bigint (foreign key → bookings)
driver_id: bigint (foreign key → drivers)
passenger_id: bigint (foreign key → users)
rating: integer (1-5)
review: text (nullable)
created_at: timestamp
updated_at: timestamp
```

### drivers table (added columns)
```
average_rating: decimal(3,2) default 0
rating_count: integer default 0
```

## 🔐 Security Features

1. **Authorization Checks**
   - Only passengers can rate their own bookings
   - Only completed trips can be rated
   - Trip must have occurred in the past

2. **Input Validation**
   - Rating: required, integer, 1-5
   - Review: optional, max 500 characters

3. **Data Integrity**
   - Foreign key constraints with cascade delete
   - Unique booking-rating relationship (updateOrCreate pattern)

## 📧 Email Notifications

### DriverRatingNotification
Sent to driver when they receive a rating.

**Subject**: New Rating Received - ⭐ - Bhutan Taxi

**Content**:
- Passenger name
- Trip details (date, route)
- Star rating display
- Passenger's review (if provided)
- Link to driver profile

## 🎨 Frontend Components

### Rating Form (`ratings/show.blade.php`)
- Trip details banner
- Interactive 5-star selector
- Review textarea with character counter
- Guidelines for rating selection
- Submit and back buttons
- Form validation display

### Booking Details Integration
- "Rate Driver" button (visible for completed trips)
- Shows existing rating if already rated
- "Update Rating" option for returning users

## 📱 User Experience Flow

```
Passenger Completes Trip
        ↓
Trip Departure Time Passes
        ↓
"Rate Driver" Button Appears on Booking Details
        ↓
Click Button → Rating Form Opens
        ↓
Select Stars + Optional Review
        ↓
Submit Rating
        ↓
Success Message + Redirect
        ↓
Driver Receives Email Notification
        ↓
Driver Profile Updated with Rating
```

## 🔄 Model Relationships

```
Driver (1) ←→ (Many) Rating
   ↓
   ├─→ ratings()
   ├─→ getAverageRating()
   ├─→ getRatingCount()
   └─→ updateRatingStats()

Booking (1) ←→ (1) Rating
   └─→ rating()

User (1) ←→ (Many) Rating [as passenger]
   └─→ ratingsGiven()

Rating
   ├─→ driver()
   ├─→ booking()
   ├─→ passenger()
   ├─→ forDriver($driverId)
   └─→ fromPassenger($passengerId)
```

## 📈 Performance Considerations

1. **Cached Metrics**: Average rating and count are cached in drivers table
2. **Eager Loading**: Relations are loaded eagerly to prevent N+1 queries
3. **Event Listeners**: Automatic cache updates via model events
4. **Indexes**: Foreign keys automatically indexed

## 🛠️ Maintenance

### Rebuilding Rating Cache
If needed to recalculate driver ratings:
```php
// In tinker or migration
Driver::all()->each(fn($driver) => $driver->updateRatingStats());
```

### Viewing Recent Ratings
```php
// Get latest ratings
$ratings = Rating::latest()->take(10)->get();

// Get ratings for specific driver
$driverRatings = Driver::find($id)->ratings()->latest()->paginate(10);
```

## 🎯 Future Enhancements

- [ ] Display driver average rating on trip listings
- [ ] Driver profile page showing rating breakdown (1-5 star count)
- [ ] Passenger review display on driver profile
- [ ] Filter trips by minimum driver rating
- [ ] Rating trends/analytics for admin
- [ ] Review moderation system
- [ ] Helpful/unhelpful voting on reviews
- [ ] Dispute resolution for ratings

## ⚠️ Notes

- Ratings are optional for drivers (only passengers rate)
- Once a trip is completed (departure time passed), passengers can rate
- Ratings can be updated multiple times
- Email notifications are queued and sent asynchronously
- All timestamps are in UTC (adjust in config as needed)

## 📞 Support

For issues or questions about the rating system implementation, refer to:
- Model files for business logic
- RatingController for request handling
- Migration files for database schema
- Email template for notification design
