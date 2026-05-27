<!DOCTYPE html>
<html>
<body style="font-family: Arial, sans-serif; color: #1f2937; line-height: 1.6;">
    <h2>New Refund Request</h2>
    <p>A customer submitted a refund request that requires admin review.</p>
    <ul>
        <li><strong>Refund ID:</strong> #{{ $refundRequest->id }}</li>
        <li><strong>Booking ID:</strong> #{{ $refundRequest->booking_id }}</li>
        <li><strong>Passenger:</strong> {{ $refundRequest->booking->passenger->name ?? 'Unknown' }}</li>
        <li><strong>Amount:</strong> Nu. {{ number_format($refundRequest->amount, 2) }}</li>
        <li><strong>Transaction ID:</strong> {{ $refundRequest->transaction_id ?? 'N/A' }}</li>
        <li><strong>Status:</strong> {{ ucfirst($refundRequest->status) }}</li>
    </ul>
    <p><strong>Reason:</strong> {{ $refundRequest->reason }}</p>
    <p>Please review the transaction and process the refund in the admin panel.</p>
</body>
</html>