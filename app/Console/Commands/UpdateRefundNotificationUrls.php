<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Notification;

class UpdateRefundNotificationUrls extends Command
{
    protected $signature = 'notifications:update-refund-urls';
    protected $description = 'Update existing refund notifications to point to the refunds admin page with refundId param';

    public function handle()
    {
        $this->info('Searching refund notifications...');
        $query = Notification::whereIn('type', ['refund_request', 'refund_review', 'refund_approved', 'refund_rejected']);
        $count = $query->count();
        $this->info("Found {$count} refund notifications");

        $updated = 0;

        foreach ($query->cursor() as $notification) {
            $data = $notification->data ?? [];

            $needUpdate = false;

            // If url is missing or points to admin.bookings.show, update it
            if (empty($data['url']) || str_contains($data['url'], '/admin/bookings')) {
                // Try to extract refund id from message (e.g. "New refund request #123 for booking #45 ...")
                $refundId = null;
                if (preg_match('/refund request #?(\d+)/i', $notification->message, $m)) {
                    $refundId = (int) $m[1];
                }

                // Fallback: if message contains "refund #123"
                if (!$refundId && preg_match('/refund #?(\d+)/i', $notification->message, $m2)) {
                    $refundId = (int) $m2[1];
                }

                if ($refundId) {
                    $data['url'] = route('admin.refunds') . '?refundId=' . $refundId;
                    $needUpdate = true;
                } else {
                    // If we cannot determine exact refund id, point to refunds list
                    $data['url'] = route('admin.refunds');
                    $needUpdate = true;
                }
            }

            if ($needUpdate) {
                $notification->data = $data;
                $notification->save();
                $updated++;
+                $this->info("Updated notification {$notification->id}");
            }
        }

        $this->info("Done. Updated {$updated} notifications.");
        return 0;
    }
}
