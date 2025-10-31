# Instructions to Fix Console Errors

## Problem
Console shows errors like:
```
001166?text=quis:1 Failed to load resource: net::ERR_NAME_NOT_RESOLVED
```

This is caused by old seeder data using invalid external image URLs.

## Solution

Run these commands to re-seed the database with fixed image URLs:

```bash
cd kelurahan-digital

# Fresh migration and seed (WARNING: This will delete all data!)
php artisan migrate:fresh --seed
```

**⚠️ WARNING:** This will delete ALL existing data in the database!

## Alternative (If you want to keep user data)

If you want to keep user accounts but refresh other data:

```bash
# Delete only social assistance data
php artisan tinker

# Then run in tinker:
\App\Models\SocialAssistanceRecipient::truncate();
\App\Models\SocialAssistance::truncate();
\App\Models\Event::truncate();
\App\Models\JobVacancy::truncate();

# Exit tinker
exit

# Re-seed specific tables
php artisan db:seed --class=SocialAssistanceSeeder
php artisan db:seed --class=EventSeeder
php artisan db:seed --class=JobVacancySeeder
```

## Changes Made

Fixed image URLs in these factories:
- ✅ `SocialAssistanceFactory.php` - Now uses placehold.co
- ✅ `HeadOfFamilyFactory.php` - Now uses placehold.co
- ✅ `EventFactory.php` - Now uses placehold.co
- ✅ `JobVacancyFactory.php` - Now uses placehold.co
- ✅ `FamilyMemberFactory.php` - Now uses placehold.co

All now use reliable `https://placehold.co` instead of random external URLs.

