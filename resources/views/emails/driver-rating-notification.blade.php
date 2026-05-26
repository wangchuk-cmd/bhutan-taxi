<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>New Rating Received</title>
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
        .rating-details {
            background-color: white;
            padding: 20px;
            border-radius: 5px;
            margin: 20px 0;
            border-left: 4px solid #ffc107;
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
        .rating-value {
            font-size: 28px;
            color: #ffc107;
            font-weight: bold;
            text-align: center;
            margin: 15px 0;
        }
        .star {
            color: #ffc107;
        }
        .review-section {
            background-color: #f5f5f5;
            padding: 15px;
            border-radius: 5px;
            margin-top: 15px;
        }
        .review-text {
            font-style: italic;
            color: #555;
        }
        .footer {
            text-align: center;
            margin-top: 30px;
            font-size: 12px;
            color: #6c757d;
        }
        .btn {
            display: inline-block;
            padding: 10px 20px;
            background-color: #198754;
            color: white;
            text-decoration: none;
            border-radius: 5px;
            margin-top: 15px;
        }
        .btn:hover {
            background-color: #157347;
        }
    </style>
</head>
<body>
    <div class="header">
        <h2>⭐ New Rating Received!</h2>
        <p>A passenger has rated your service</p>
    </div>

    <div class="content">
        <p>Hello {{ $rating->driver->user->name }},</p>

        <p>Great news! You've received a new rating from a passenger.</p>

        <div class="rating-details">
            <div class="detail-row">
                <span class="label">Passenger:</span>
                <span class="value">Anonymous Passenger</span>
            </div>

            <div class="detail-row">
                <span class="label">Trip Date:</span>
                <span class="value">{{ $booking->trip->departure_datetime->format('M d, Y H:i') }}</span>
            </div>

            <div class="rating-value">
                @for($i = 0; $i < $rating->rating; $i++)
                    <span class="star">★</span>
                @endfor
                @for($i = $rating->rating; $i < 5; $i++)
                    <span style="color: #ddd;">★</span>
                @endfor
                <br>
                <span style="font-size: 18px;">{{ $rating->rating }} out of 5</span>
            </div>

            @if($rating->review)
            <div class="review-section">
                <p style="margin: 0; font-weight: bold; margin-bottom: 10px;">Passenger's Comment:</p>
                <p class="review-text">{{ $rating->review }}</p>
            </div>
            @endif

            <div class="detail-row" style="margin-top: 20px;">
                <span class="label">Route:</span>
                <span class="value">{{ $booking->trip->origin_dzongkhag }} → {{ $booking->trip->destination_dzongkhag }}</span>
            </div>
        </div>

        <p style="text-align: center;">
            <a href="{{ route('driver.profile') }}" class="btn">View Your Profile</a>
        </p>

        <p>Thank you for your excellent service! Keep up the great work!</p>

        <div class="footer">
            <p>&copy; {{ now()->year }} Bhutan Taxi. All rights reserved.</p>
            <p>This is an automated email. Please do not reply to this message.</p>
        </div>
    </div>
</body>
</html>
