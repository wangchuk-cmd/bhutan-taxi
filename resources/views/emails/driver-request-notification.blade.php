<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>New Driver Registration Request</title>
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
            background-color: #dc3545;
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
        .driver-details {
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
            background-color: #dc3545;
            color: white;
            text-decoration: none;
            border-radius: 5px;
            margin: 10px 5px;
        }
        .button.approve {
            background-color: #198754;
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
        <h1>👤 New Driver Registration</h1>
    </div>
    
    <div class="content">
        <p>Dear Admin,</p>
        
        <p>A new driver has registered on the Bhutan Taxi platform and is awaiting approval. Please review the driver's details below:</p>
        
        <div class="alert">
            <strong>⚠️ Action Required:</strong> Please verify the driver's credentials and approve or reject the registration.
        </div>
        
        <div class="driver-details">
            <h3 style="margin-top: 0; color: #dc3545;">Driver Information</h3>
            
            <div class="detail-row">
                <span class="label">Driver ID:</span>
                <span class="value">#{{ $driver->id }}</span>
            </div>
            
            <div class="detail-row">
                <span class="label">Name:</span>
                <span class="value">{{ $driver->user->name }}</span>
            </div>
            
            <div class="detail-row">
                <span class="label">Phone:</span>
                <span class="value">{{ $driver->user->phone_number }}</span>
            </div>
            
            <div class="detail-row">
                <span class="label">Email:</span>
                <span class="value">{{ $driver->user->email }}</span>
            </div>
            
            <div class="detail-row">
                <span class="label">License Number:</span>
                <span class="value">{{ $driver->license_number }}</span>
            </div>
            
            <div class="detail-row">
                <span class="label">Status:</span>
                <span class="value">{{ ucfirst($driver->status) }}</span>
            </div>
        </div>
        
        <div class="driver-details">
            <h3 style="margin-top: 0; color: #dc3545;">Vehicle Information</h3>
            
            <div class="detail-row">
                <span class="label">Vehicle Type:</span>
                <span class="value">{{ $driver->vehicle_type }}</span>
            </div>
            
            <div class="detail-row">
                <span class="label">Taxi Plate Number:</span>
                <span class="value">{{ $driver->taxi_plate_number }}</span>
            </div>
            
            <div class="detail-row">
                <span class="label">Verification Status:</span>
                <span class="value">{{ $driver->verified ? 'Verified' : 'Pending Verification' }}</span>
            </div>
        </div>
        
        <p><strong>Next Steps:</strong> Login to the admin portal to review the driver's documents and approve or reject the registration.</p>
        
        <div style="text-align: center;">
            <a href="{{ url('/admin/drivers/' . $driver->id) }}" class="button approve">Review & Approve</a>
            <a href="{{ url('/admin/drivers') }}" class="button">View All Drivers</a>
        </div>
    </div>
    
    <div class="footer">
        <p>Bhutan Taxi Admin Portal</p>
        <p style="font-size: 12px; color: #adb5bd;">This is an automated email. Please do not reply to this message.</p>
    </div>
</body>
</html>
