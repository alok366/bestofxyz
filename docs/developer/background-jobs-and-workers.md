# Background Tasks & Scheduled Cronjobs

BestOfXYZ handles asynchronous and batch processing through periodic scheduled cron tasks located in `workers/cron/`.

---

## 2. Scheduled Cronjobs

Scheduled jobs live in the `workers/cron/` directory.

### 2.1 Available Cron Scripts
- `workers/cron/recompute-hot-scores.php`: Recalculates `hot_score` based on net votes and submission age.
- `workers/cron/snapshot-ranks.php`: Takes daily snapshots of category resource rankings.
- `workers/cron/reconcile-scores.php`: Reconciles cached score sums with vote tables.
- `workers/cron/check-pending-promotions.php`: Promotes pending resources once score thresholds are met.
- `workers/cron/prune-snapshots.php`: Prunes aged historical snapshot entries.

### 2.2 Crontab Configuration
Add the following entries to your system crontab (`crontab -e`):

```cron
# Recompute hot scores every 15 minutes
*/15 * * * * php /var/www/bestofxyz/public_html/workers/cron/recompute-hot-scores.php >> /var/www/bestofxyz/public_html/logs/cron-scores.log 2>&1

# Daily rank snapshot at midnight
0 0 * * * php /var/www/bestofxyz/public_html/workers/cron/snapshot-ranks.php >> /var/www/bestofxyz/public_html/logs/cron-ranks.log 2>&1
```
