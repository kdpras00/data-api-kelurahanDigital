<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\SocialAssistanceRecipient;
use App\Models\User;

echo "=== Checking Latest Social Assistance Submissions ===\n\n";

// Get current authenticated user (head of family)
$headOfFamilyUser = User::where('email', 'headoffamily@gmail.com')->first();

if (!$headOfFamilyUser) {
    echo "Head of family user not found!\n";
    exit;
}

echo "Logged in as: {$headOfFamilyUser->name} ({$headOfFamilyUser->email})\n";
echo "Head of Family ID: " . ($headOfFamilyUser->headOfFamily->id ?? 'N/A') . "\n\n";

// Get latest 10 submissions
$latestSubmissions = SocialAssistanceRecipient::with(['socialAssistance', 'headOfFamily.user'])
    ->latest()
    ->take(10)
    ->get();

echo "Latest 10 Submissions in Database:\n";
echo str_repeat("=", 80) . "\n";

if ($latestSubmissions->isEmpty()) {
    echo "No submissions found!\n";
} else {
    foreach ($latestSubmissions as $index => $submission) {
        echo ($index + 1) . ". ID: {$submission->id}\n";
        echo "   Social Assistance: " . ($submission->socialAssistance->name ?? 'N/A') . "\n";
        echo "   Submitted by: " . ($submission->headOfFamily->user->name ?? 'N/A') . "\n";
        echo "   Amount: Rp " . number_format($submission->amount, 0, ',', '.') . "\n";
        echo "   Bank: {$submission->bank}\n";
        echo "   Account: {$submission->account_number}\n";
        echo "   Status: {$submission->status}\n";
        echo "   Reason: {$submission->reason}\n";
        echo "   Created: {$submission->created_at}\n";
        echo "   " . str_repeat("-", 76) . "\n";
    }
}

// Check submissions from headoffamily@gmail.com specifically
if ($headOfFamilyUser->headOfFamily) {
    $mySubmissions = SocialAssistanceRecipient::with(['socialAssistance'])
        ->where('head_of_family_id', $headOfFamilyUser->headOfFamily->id)
        ->latest()
        ->take(5)
        ->get();
    
    echo "\n\nMy Submissions (headoffamily@gmail.com):\n";
    echo str_repeat("=", 80) . "\n";
    
    if ($mySubmissions->isEmpty()) {
        echo "No submissions found for this user!\n";
    } else {
        foreach ($mySubmissions as $index => $submission) {
            $saName = $submission->socialAssistance ? $submission->socialAssistance->name : 'N/A';
            echo ($index + 1) . ". {$saName} - Rp " . 
                 number_format($submission->amount, 0, ',', '.') . 
                 " ({$submission->created_at})\n";
        }
    }
}

echo "\n\nTotal submissions in database: " . SocialAssistanceRecipient::count() . "\n";

