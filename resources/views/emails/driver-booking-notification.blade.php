<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>New Booking Notification</title>
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
            background-color: #198754;
            color: white;
            padding: 20px;
            text-align: center;
            border-radius: 5px 5px 0 0;
        }
        .content {
            background-color: #f8f9fa;
            padding: 30px;
            border: 1px solid #dee2e6;
            border-radius: 0 0 5px 5px;
        }
        .booking-details {
            background-color: white;
            padding: 20px;
            border-radius: 5px;
            margin: 20px 0;
        }
        .detail-row {
            display: flex;
            justify-content: space-between;
            padding: 10px 0;
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
        }
        .alert {
            background-color: #fff3cd;
            border: 1px solid #ffc107;
            padding: 15px;
            border-radius: 5px;
            margin: 20px 0;
        }
        .button {
            display: inline-block;
            padding: 12px 30px;
            background-color: #198754;
            color: white;
            text-decoration: none;
            border-radius: 5px;
            margin-top: 20px;
        }
        .footer {
            text-align: center;
            margin-top: 30px;
            color: #6c757d;
            font-size: 14px;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>🚖 New Booking Alert!</h1>
    </div>
    
    <div class="content">
        <p>Dear {{ $booking->trip->driver->user->name }},</p>
        
        <p>You have received a new booking for your trip. Please review the details below:</p>
        
        <div class="alert">
            <strong>⚠️ Action Required:</strong> Please confirm your availability and prepare for this trip.
        </div>
        
        <div class="booking-details">
            <h3 style="margin-top: 0; color: #198754;">Trip Details</h3>
            
            <div class="detail-row">
                <span class="label">Booking ID:</span>
                <span class="value">#{{ $booking->id }}</span>
            </div>
            
            <div class="detail-row">
                <span class="label">Route:</span>
                <span class="value">{{ $booking->trip->origin_dzongkhag }} → {{ $booking->trip->destination_dzongkhag }}</span>
            </div>
            
            <div class="detail-row">
                <span class="label">Departure:</span>
                <span class="value">{{ $booking->trip->departure_datetime->format('M d, Y h:i A') }}</span>
            </div>
            
            <div class="detail-row">
                <span class="label">Booking Type:</span>
                <span class="value">{{ ucfirst($booking->booking_type) }}</span>
            </div>
            
            <div class="detail-row">
                <span class="label">Seats Booked:</span>
                <span class="value">{{ $booking->seats_booked }}</span>
            </div>
            
            <div class="detail-row">
                <span class="label">Amount:</span>
                <span class="value"><strong>Nu. {{ number_format($booking->total_amount) }}</strong></span>
            </div>
        </div>
        
        <div class="booking-details">
            <h3 style="margin-top: 0; color: #198754;">Booking Information</h3>
            
            <!-- BOOKER SECTION -->
            <div style="background-color: #e7f3ff; padding: 15px; border-radius: 5px; margin-bottom: 15px; border-left: 4px solid #0d6efd;">
                <div style="font-size: 12px; color: #0d6efd; font-weight: bold; margin-bottom: 10px;">📝 BOOKER</div>
                <div class="detail-row">
                    <span class="label">Name:</span>
                    <span class="value">{{ $booking->passenger->name ?? 'N/A' }}</span>
                </div>
                <div class="detail-row">
                    <span class="label">Phone:</span>
                    <span class="value">{{ $booking->passenger->phone_number ?? 'N/A' }}</span>
                </div>
                <div class="detail-row">
                    <span class="label">Email:</span>
                    <span class="value">{{ $booking->passenger->email ?? 'N/A' }}</span>
                </div>
            </div>
            
            <!-- PASSENGERS SECTION -->
            @if(is_array($booking->passengers_info) && count($booking->passengers_info) > 0)
            <div style="background-color: #f0f9ff; padding: 15px; border-radius: 5px; border-left: 4px solid #198754;">
                <div style="font-size: 12px; color: #198754; font-weight: bold; margin-bottom: 10px;">👥 PASSENGERS ({{ count($booking->passengers_info) }})</div>
                <ul style="margin: 0; padding-left: 20px;">
                    @foreach($booking->passengers_info as $index => $p)
                    <li style="margin-bottom: 8px;">
                        <strong>{{ $p['name'] ?? 'N/A' }}</strong><br>
                        <span style="color: #6c757d; font-size: 12px;">📞 {{ $p['phone'] ?? 'N/A' }}</span>
                    </li>
                    @endforeach
                </ul>
            </div>
            @endif
        </div>
        
        <p><strong>Please Note:</strong> Contact the passenger before the scheduled departure time to confirm pickup details.</p>
        
        <div style="text-align: center;">
            <a href="{{ url('/driver/trips') }}" class="button">View Trip Details</a>
        </div>
    </div>
    
    <div class="footer">
        <p>Bhutan Taxi Driver Portal</p>
        <p style="font-size: 12px; color: #adb5bd;">This is an automated email. Please do not reply to this message.</p>
    </div>
</body>
</html>
