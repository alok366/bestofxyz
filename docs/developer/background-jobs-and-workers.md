# Background Jobs, Queues & Scheduled Workers

BestOfXYZ handles asynchronous work through two distinct mechanisms:
1. **Background Queue Workers**: Real-time asynchronous jobs (emails, webhooks, analytics dispatch).
2. **Scheduled Cronjobs**: Batch periodic execution (daily stats calculation, rank snapshotting).

---

## 1. Background Queue System

### 1.1 Architecture
The queue layer is located in `system/App/Workers/`. Workers consume jobs pushed to Redis or database queues.

```
[Application Request]
       │
       ▼ (dispatch job)
  [Redis / Database Queue]
       │
       ▼ (poll & execute)
[Worker Daemon: php artisan worker]
```

### 1.2 Starting Workers
In local development, you can start a worker directly:
```bash
php artisan worker
```

In production, run workers under a process supervisor such as **Supervisor** or **Systemd**:

```ini
[program:bestofxyz-worker]
process_name=%(program_name)s_%(process_num)02d
command=php /var/www/bestofxyz/public_html/artisan worker
autostart=true
autorestart=true
user=www-data
numprocs=2
redirect_stderr=true
stdout_logfile=/var/www/bestofxyz/public_html/logs/worker.log
```

### 1.3 Managing Failed Jobs
If a job throws an unhandled exception, it is pushed to the `failed_jobs` table:
```bash
# List all failed jobs
php artisan queue:failed

# Retry specific job ID
php artisan queue:retry 12

# Retry all failed jobs
php artisan queue:retry all
```

---

## 2. Scheduled Cronjobs

Scheduled jobs live in the [`cronjobs/`](../../cronjobs) directory.

### 2.1 Available Cron Scripts
- `cronjobs/EmailQueueProcessor.php`: Flushes outgoing batched notification emails.
- `cronjobs/RefreshDailyCustomerLoginStats.php`: Aggregates customer activity and daily engagement metrics.

### 2.2 Crontab Configuration
Add the following entries to your system crontab (`crontab -e`):

```cron
# Process email queues every minute
* * * * * php /var/www/bestofxyz/public_html/cronjobs/EmailQueueProcessor.php >> /var/www/bestofxyz/public_html/logs/cron-email.log 2>&1

# Refresh daily stats at midnight
0 0 * * * php /var/www/bestofxyz/public_html/cronjobs/RefreshDailyCustomerLoginStats.php >> /var/www/bestofxyz/public_html/logs/cron-stats.log 2>&1
```
