# Laravel Task Scheduler Setup

## Email Departure Reminders

This application sends automatic email reminders to passengers and drivers 1 hour before trip departure.

## How It Works

The system runs a scheduled command every 10 minutes that:
1. Checks for trips departing in approximately 1 hour (55-65 minutes)
2. Sends email reminders to the driver with trip details and passenger list
3. Sends email reminders to all confirmed passengers with booking details and driver contact

## Local Development

To test the scheduler in development, run:

```bash
php artisan schedule:work
```

This will run the scheduler every minute, checking for tasks to execute.

To manually test the reminder command:

```bash
php artisan reminders:send-departure
```

## Production Setup (Windows Server with XAMPP)

### Option 1: Windows Task Scheduler (Recommended for Windows)

1. Open **Task Scheduler** (search in Start menu)
2. Click **Create Basic Task**
3. Name: "Laravel Scheduler - Bhutan Taxi"
4. Trigger: Daily, start at 00:00
5. Action: Start a program
   - Program/script: `C:\xampp\php\php.exe`
   - Arguments: `C:\xampp\htdocs\bhutan-taxi\artisan schedule:run`
6. Open the task properties and go to **Triggers** tab
7. Edit the trigger and check **Repeat task every: 1 minute**
8. Set duration to **Indefinitely**
9. Check **Enabled**
10. Save

### Option 2: Create a Batch Script

Create `scheduler.bat` in `C:\xampp\htdocs\bhutan-taxi\`:

```batch
@echo off
cd C:\xampp\htdocs\bhutan-taxi
C:\xampp\php\php.exe artisan schedule:run >> NUL 2>&1
```

Then add this to Windows Task Scheduler to run every minute.

### Option 3: NSSM (Non-Sucking Service Manager)

Download NSSM and install the scheduler as a Windows service:

```cmd
nssm install LaravelScheduler "C:\xampp\php\php.exe" "C:\xampp\htdocs\bhutan-taxi\artisan schedule:work"
nssm set LaravelScheduler AppDirectory "C:\xampp\htdocs\bhutan-taxi"
nssm start LaravelScheduler
```

## Production Setup (Linux Server)

Add to your crontab:

```bash
crontab -e
```

Add this line:

```
* * * * * cd /path/to/bhutan-taxi && php artisan schedule:run >> /dev/null 2>&1
```

## Verification

To verify the scheduler is working:

1. Check the Laravel log file: `storage/logs/laravel.log`
2. Run the list command: `php artisan schedule:list`
3. Manually trigger: `php artisan reminders:send-departure`

## Email Configuration

Ensure your `.env` file has proper SMTP settings:

```env
MAIL_MAILER=smtp
MAIL_HOST=smtp.gmail.com
MAIL_PORT=587
MAIL_USERNAME=your-email@gmail.com
MAIL_PASSWORD="your app password"
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS="your-email@gmail.com"
MAIL_FROM_NAME="Bhutan Taxi Booking"
```

## Testing with a Specific Time

To test reminders for a specific trip, temporarily modify the departure time in the database:

```sql
UPDATE trips 
SET departure_datetime = DATE_ADD(NOW(), INTERVAL 60 MINUTE) 
WHERE id = 1;
```

Then run:
```bash
php artisan reminders:send-departure
```

## Scheduled Tasks Summary

| Command | Schedule | Purpose |
|---------|----------|---------|
| `reminders:send-departure` | Every 10 minutes | Send departure reminders to passengers and drivers |

## Troubleshooting

**Emails not sending:**
- Check `.env` SMTP credentials
- Verify `storage/logs/laravel.log` for errors
- Test SMTP connection manually
- Ensure firewall allows SMTP port (587/465)

**Scheduler not running:**
- Verify cron/task is set up correctly
- Check PHP path in task configuration
- Ensure proper file permissions
- Check system logs

**Command not found:**
- Run `php artisan optimize:clear`
- Check command is in `app/Console/Commands/`
- Verify namespace and class name

## Support

For issues, check:
1. Laravel logs: `storage/logs/laravel.log`
2. Web server error logs
3. Windows Event Viewer (for task scheduler issues)
