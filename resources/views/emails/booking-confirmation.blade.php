<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Booking Confirmation</title>
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
            background-color: #0d6efd;
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
        .button {
            display: inline-block;
            padding: 12px 30px;
            background-color: #0d6efd;
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
        <h1>🚖 Booking Confirmed!</h1>
    </div>
    
    <div class="content">
        <p>Dear {{ $booking->passenger->name ?? 'Booker' }},</p>
        
        <p>Your taxi booking has been successfully confirmed. Please find your booking details below:</p>
        
        <div class="booking-details">
            <h3 style="margin-top: 0; color: #0d6efd;">Booking Details</h3>
            
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
                <span class="label">Seats:</span>
                <span class="value">{{ $booking->seats_booked }}</span>
            </div>
            
            <div class="detail-row">
                <span class="label">Total Amount:</span>
                <span class="value"><strong>Nu. {{ number_format($booking->total_amount) }}</strong></span>
            </div>
            
            <div class="detail-row">
                <span class="label">Payment Status:</span>
                <span class="value">{{ ucfirst($booking->payment_status) }}</span>
            </div>
        </div>
        
        <!-- PASSENGERS SECTION -->
        @if(is_array($booking->passengers_info) && count($booking->passengers_info) > 0)
        <div class="booking-details" style="background-color: #f0f9ff; border-left: 4px solid #198754;">
            <h3 style="margin-top: 0; color: #198754;">Passengers ({{ count($booking->passengers_info) }})</h3>
            @foreach($booking->passengers_info as $index => $p)
            <div style="padding: 10px 0; border-bottom: 1px solid #dee2e6;">
                <div style="font-weight: bold;">{{ $index + 1 }}. {{ $p['name'] ?? 'N/A' }}</div>
                <div style="color: #6c757d; font-size: 14px;">📞 {{ $p['phone'] ?? 'N/A' }}</div>
            </div>
            @endforeach
        </div>
        @endif
        
        <div class="booking-details">
            <h3 style="margin-top: 0; color: #0d6efd;">Driver Information</h3>
            
            <div class="detail-row">
                <span class="label">Driver Name:</span>
                <span class="value">{{ $booking->trip->driver->user->name }}</span>
            </div>
            
            <div class="detail-row">
                <span class="label">Contact:</span>
                <span class="value">{{ $booking->trip->driver->user->phone_number }}</span>
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
        
        <p><strong>Important:</strong> Please arrive at the pickup location 10 minutes before the scheduled departure time.</p>
        
        <p>If you need to cancel this booking, please do so at least 24 hours before departure to be eligible for a refund.</p>
        
        <div style="text-align: center;">
            <a href="{{ url('/bookings/' . $booking->id) }}" class="button">View Booking Details</a>
        </div>
    </div>
    
    <div class="footer">
        <p>Thank you for choosing Bhutan Taxi!</p>
        <p>For any queries, please contact us.</p>
        <p style="font-size: 12px; color: #adb5bd;">This is an automated email. Please do not reply to this message.</p>
    </div>
</body>
</html>
