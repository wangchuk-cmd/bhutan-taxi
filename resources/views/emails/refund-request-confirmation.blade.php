<!DOCTYPE html>
<html>
<body style="font-family: Arial, sans-serif; color: #1f2937; line-height: 1.6;">
    <h2>Refund Request Submitted</h2>
    <p>Your refund request has been received and is now under review.</p>
    <ul>
        <li><strong>Refund ID:</strong> #{{ $refundRequest->id }}</li>
        <li><strong>Booking ID:</strong> #{{ $refundRequest->booking_id }}</li>
        <li><strong>Amount:</strong> Nu. {{ number_format($refundRequest->amount, 2) }}</li>
        <li><strong>Status:</strong> {{ ucfirst($refundRequest->status) }}</li>
    </ul>
    <p>We will notify you once the review is complete.</p>
</body>
</html>