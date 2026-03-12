<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Trip Departure Reminder</title>
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
            background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
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
            background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
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
        .passenger-list {
            background-color: white;
            padding: 20px;
            border-radius: 5px;
            margin: 20px 0;
            border: 2px solid #dc3545;
        }
        .passenger-item {
            padding: 15px;
            border-bottom: 1px solid #dee2e6;
            margin-bottom: 10px;
        }
        .passenger-item:last-child {
            border-bottom: none;
            margin-bottom: 0;
        }
        .passenger-name {
            font-weight: bold;
            font-size: 16px;
            color: #212529;
        }
        .passenger-contact {
            color: #6c757d;
            font-size: 14px;
            margin-top: 5px;
        }
        .checklist {
            background-color: white;
            padding: 20px;
            border-radius: 5px;
            margin: 20px 0;
        }
        .checklist h4 {
            margin-top: 0;
            color: #dc3545;
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
            background-color: #dc3545;
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
        .stats-row {
            display: flex;
            justify-content: space-around;
            background-color: white;
            padding: 20px;
            border-radius: 5px;
            margin: 20px 0;
        }
        .stat-item {
            text-align: center;
        }
        .stat-number {
            font-size: 32px;
            font-weight: bold;
            color: #dc3545;
        }
        .stat-label {
            font-size: 12px;
            color: #6c757d;
            text-transform: uppercase;
        }
    </style>
</head>
<body>
    <div class="header">
        <div class="alert-badge">⏰ TRIP ALERT</div>
        <h1>Your Trip Departs in 1 Hour!</h1>
    </div>
    
    <div class="content">
        <div class="countdown">
            <div style="font-size: 14px; opacity: 0.9;">DEPARTURE IN</div>
            <div class="countdown-time">1 HOUR</div>
            <div style="font-size: 14px; opacity: 0.9;">{{ $trip->departure_datetime->format('h:i A') }}</div>
        </div>
        
        <div class="reminder-box">
            <h3>🚖 Driver Reminder</h3>
            <p style="margin: 0;">Your trip from <strong>{{ $trip->origin_dzongkhag }}</strong> to <strong>{{ $trip->destination_dzongkhag }}</strong> departs in 1 hour. Please prepare your vehicle and confirm passenger details.</p>
        </div>
        
        <div class="stats-row">
            <div class="stat-item">
                <div class="stat-number">{{ $trip->bookings()->where('status', 'active')->where('payment_status', 'paid')->count() }}</div>
                <div class="stat-label">Confirmed</div>
            </div>
            <div class="stat-item">
                <div class="stat-number">{{ $trip->bookings()->where('status', 'active')->where('payment_status', 'paid')->sum('seats_booked') }}</div>
                <div class="stat-label">Total Seats</div>
            </div>
            <div class="stat-item">
                <div class="stat-number">{{ $trip->available_seats }}</div>
                <div class="stat-label">Available</div>
            </div>
        </div>
        
        <div class="trip-details">
            <h3 style="margin-top: 0; color: #dc3545;">Trip Information</h3>
            
            <div class="detail-row">
                <span class="label">Trip ID:</span>
                <span class="value">#{{ $trip->id }}</span>
            </div>
            
            <div class="detail-row">
                <span class="label">Route:</span>
                <span class="value">{{ $trip->origin_dzongkhag }} → {{ $trip->destination_dzongkhag }}</span>
            </div>
            
            <div class="detail-row">
                <span class="label">Departure:</span>
                <span class="value">{{ $trip->departure_datetime->format('M d, Y h:i A') }}</span>
            </div>
            
            @if($trip->route && $trip->route->distance_km)
            <div class="detail-row">
                <span class="label">Distance:</span>
                <span class="value">{{ $trip->route->distance_km }} km</span>
            </div>
            @endif
            
            @if($trip->route && $trip->route->estimated_time)
            <div class="detail-row">
                <span class="label">Est. Duration:</span>
                <span class="value">{{ $trip->route->estimated_time }}</span>
            </div>
            @endif
        </div>
        
        @php
            $confirmedBookings = $trip->bookings()->with('passenger')->where('status', 'active')->where('payment_status', 'paid')->get();
        @endphp
        
        @if($confirmedBookings->count() > 0)
        <div class="passenger-list">
            <h4 style="margin-top: 0; color: #dc3545;">👥 Confirmed Bookings ({{ $confirmedBookings->count() }})</h4>
            
            @foreach($confirmedBookings as $booking)
            <div class="passenger-item">
                <!-- BOOKER INFO -->
                <div style="background-color: #e7f3ff; padding: 10px; border-radius: 5px; margin-bottom: 10px; border-left: 4px solid #0d6efd;">
                    <div style="font-size: 12px; color: #0d6efd; font-weight: bold; margin-bottom: 5px;">📝 BOOKER</div>
                    <div class="passenger-name" style="color: #0d6efd;">
                        {{ $booking->passenger->name ?? 'N/A' }}
                        @if($booking->booking_type === 'full')
                        <span style="background-color: #ffc107; color: #000; padding: 2px 8px; border-radius: 3px; font-size: 11px; margin-left: 8px;">FULL TAXI</span>
                        @endif
                    </div>
                    <div class="passenger-contact">
                        📞 {{ $booking->passenger->phone_number ?? 'N/A' }} 
                        @if($booking->passenger->email)
                        | ✉️ {{ $booking->passenger->email }}
                        @endif
                    </div>
                    <div class="passenger-contact">
                        🎫 Booking #{{ $booking->id }} | {{ $booking->seats_booked }} seat(s)
                    </div>
                </div>
                
                <!-- PASSENGERS LIST -->
                @if(is_array($booking->passengers_info) && count($booking->passengers_info) > 0)
                <div style="background-color: #f0f9ff; padding: 10px; border-radius: 5px; border-left: 4px solid #198754;">
                    <div style="font-size: 12px; color: #198754; font-weight: bold; margin-bottom: 8px;">👥 PASSENGERS ({{ count($booking->passengers_info) }})</div>
                    @foreach($booking->passengers_info as $index => $p)
                    <div style="padding: 5px 0; border-bottom: 1px solid #dee2e6;">
                        <strong>{{ $index + 1 }}. {{ $p['name'] ?? 'N/A' }}</strong><br>
                        <span style="color: #6c757d; font-size: 12px;">📞 {{ $p['phone'] ?? 'N/A' }}</span>
                    </div>
                    @endforeach
                </div>
                @endif
            </div>
            @endforeach
        </div>
        @else
        <div class="reminder-box" style="background-color: #f8d7da; border-left-color: #dc3545;">
            <h3 style="color: #721c24;">⚠️ No Confirmed Bookings</h3>
            <p style="margin: 0;">There are currently no confirmed passengers for this trip.</p>
        </div>
        @endif
        
        <div class="checklist">
            <h4>✅ Pre-Departure Checklist for Driver</h4>
            <div class="checklist-item">
                <i>✓</i> Verify vehicle is clean and in good condition
            </div>
            <div class="checklist-item">
                <i>✓</i> Check fuel level and tire pressure
            </div>
            <div class="checklist-item">
                <i>✓</i> Confirm all safety equipment is present
            </div>
            <div class="checklist-item">
                <i>✓</i> Review passenger list and contact information
            </div>
            <div class="checklist-item">
                <i>✓</i> Plan your route and check road conditions
            </div>
            <div class="checklist-item">
                <i>✓</i> Arrive at pickup location 15 minutes early
            </div>
        </div>
        
        <p><strong>Note:</strong> Please contact passengers if there are any changes or delays. Ensure you have all necessary documents for the journey.</p>
        
        <div style="text-align: center;">
            <a href="{{ url('/driver/trips') }}" class="button">View Trip Details</a>
        </div>
    </div>
    
    <div class="footer">
        <p><strong>Safe travels and drive carefully!</strong></p>
        <p>Bhutan Taxi Driver Portal</p>
        <p style="font-size: 12px; color: #adb5bd;">This is an automated reminder. Please do not reply to this message.</p>
    </div>
</body>
</html>
