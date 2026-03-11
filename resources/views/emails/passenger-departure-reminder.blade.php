<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Departure Reminder</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            color: #333;
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
        }
        .header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 30px 20px;
            text-align: center;
            border-radius: 5px 5px 0 0;
        }
        .header h1 {
            margin: 0;
            font-size: 24px;
        }
        .alert-badge {
            background-color: #ffc107;
            color: #000;
            padding: 8px 20px;
            border-radius: 20px;
            display: inline-block;
            font-weight: bold;
            margin-bottom: 15px;
            font-size: 14px;
        }
        .content {
            background-color: #f8f9fa;
            padding: 30px;
            border: 1px solid #dee2e6;
            border-radius: 0 0 5px 5px;
        }
        .reminder-box {
            background-color: #fff3cd;
            border-left: 4px solid #ffc107;
            padding: 20px;
            margin: 20px 0;
            border-radius: 5px;
        }
        .reminder-box h3 {
            margin-top: 0;
            color: #856404;
        }
        .trip-details {
            background-color: white;
            padding: 20px;
            border-radius: 5px;
            margin: 20px 0;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        .detail-row {
            display: flex;
            justify-content: space-between;
            padding: 12px 0;
            border-bottom: 1px solid #dee2e6;
        }
        .detail-row:last-child {
            border-bottom: none;
        }
        .label {
            font-weight: bold;
            color: #6c757d;
        }
        .value {
            color: #212529;
            font-weight: 600;
        }
        .countdown {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 20px;
            border-radius: 10px;
            text-align: center;
            margin: 20px 0;
        }
        .countdown-time {
            font-size: 48px;
            font-weight: bold;
            margin: 10px 0;
        }
        .checklist {
            background-color: white;
            padding: 20px;
            border-radius: 5px;
            margin: 20px 0;
        }
        .checklist h4 {
            margin-top: 0;
            color: #0d6efd;
        }
        .checklist-item {
            padding: 10px 0;
            border-bottom: 1px solid #dee2e6;
        }
        .checklist-item:last-child {
            border-bottom: none;
        }
        .checklist-item i {
            color: #198754;
            margin-right: 10px;
        }
        .button {
            display: inline-block;
            padding: 14px 30px;
            background-color: #0d6efd;
            color: white;
            text-decoration: none;
            border-radius: 5px;
            margin-top: 20px;
            font-weight: bold;
        }
        .footer {
            text-align: center;
            margin-top: 30px;
            color: #6c757d;
            font-size: 14px;
        }
        .contact-box {
            background-color: white;
            padding: 20px;
            border-radius: 5px;
            margin: 20px 0;
            border: 2px solid #0d6efd;
        }
    </style>
</head>
<body>
    <div class="header">
        <div class="alert-badge">⏰ DEPARTURE ALERT</div>
        <h1>Your Trip is Departing Soon!</h1>
    </div>
    
    <div class="content">
        <div class="countdown">
            <div style="font-size: 14px; opacity: 0.9;">DEPARTING IN</div>
            <div class="countdown-time">1 HOUR</div>
            <div style="font-size: 14px; opacity: 0.9;">{{ $booking->trip->departure_datetime->format('h:i A') }}</div>
        </div>
        
        <div class="reminder-box">
            <h3>🚖 Get Ready!</h3>
            <p style="margin: 0;">Your taxi to <strong>{{ $booking->trip->destination_dzongkhag }}</strong> departs in approximately 1 hour. Please be at the pickup location on time.</p>
        </div>
        
        <div class="trip-details">
            <h3 style="margin-top: 0; color: #0d6efd;">Trip Details</h3>
            
            <div class="detail-row">
                <span class="label">Booking ID:</span>
                <span class="value">#{{ $booking->id }}</span>
            </div>
            
            <div class="detail-row">
                <span class="label">Route:</span>
                <span class="value">{{ $booking->trip->origin_dzongkhag }} → {{ $booking->trip->destination_dzongkhag }}</span>
            </div>
            
            <div class="detail-row">
                <span class="label">Departure Time:</span>
                <span class="value">{{ $booking->trip->departure_datetime->format('M d, Y h:i A') }}</span>
            </div>
            
            <div class="detail-row">
                <span class="label">Seats Booked:</span>
                <span class="value">{{ $booking->seats_booked }}</span>
            </div>
            
            <div class="detail-row">
                <span class="label">Booking Type:</span>
                <span class="value">{{ ucfirst($booking->booking_type) }}</span>
            </div>
        </div>
        
        <div class="contact-box">
            <h4 style="margin-top: 0; color: #0d6efd;">📞 Driver Contact</h4>
            
            <div class="detail-row">
                <span class="label">Driver Name:</span>
                <span class="value">{{ $booking->trip->driver->user->name }}</span>
            </div>
            
            <div class="detail-row">
                <span class="label">Phone Number:</span>
                <span class="value" style="font-size: 18px; color: #0d6efd;">{{ $booking->trip->driver->user->phone_number }}</span>
            </div>
            
            <div class="detail-row">
                <span class="label">Vehicle:</span>
                <span class="value">{{ $booking->trip->driver->vehicle_type }}</span>
            </div>
            
            <div class="detail-row">
                <span class="label">License Plate:</span>
                <span class="value">{{ $booking->trip->driver->taxi_plate_number }}</span>
            </div>
        </div>
        
        <div class="checklist">
            <h4>✅ Pre-Departure Checklist</h4>
            <div class="checklist-item">
                <i>✓</i> Arrive at pickup location 10 minutes early
            </div>
            <div class="checklist-item">
                <i>✓</i> Have your booking confirmation ready
            </div>
            <div class="checklist-item">
                <i>✓</i> Keep driver's contact number handy
            </div>
            <div class="checklist-item">
                <i>✓</i> Check weather conditions for your journey
            </div>
            <div class="checklist-item">
                <i>✓</i> Ensure you have all necessary travel documents
            </div>
        </div>
        
        <p><strong>Important:</strong> If you're running late or facing any issues, please contact your driver immediately using the phone number above.</p>
        
        <div style="text-align: center;">
            <a href="{{ url('/bookings/' . $booking->id) }}" class="button">View Booking Details</a>
        </div>
    </div>
    
    <div class="footer">
        <p><strong>Have a safe journey!</strong></p>
        <p>Bhutan Taxi Booking System</p>
        <p style="font-size: 12px; color: #adb5bd;">This is an automated reminder. Please do not reply to this message.</p>
    </div>
</body>
</html>
