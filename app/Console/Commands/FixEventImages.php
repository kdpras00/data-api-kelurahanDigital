<?php

namespace App\Console\Commands;

use App\Models\Event;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

class FixEventImages extends Command
{
    protected $signature = 'fix:event-images';
    protected $description = 'Copy event images from storage/app/public to public/storage for XAMPP compatibility';

    public function handle()
    {
        $this->info('Starting to fix event images...');
        
        $events = Event::all();
        $fixed = 0;
        $skipped = 0;
        $errors = 0;

        foreach ($events as $event) {
            if ($event->thumbnail) {
                // Skip if it's a URL
                if (str_starts_with($event->thumbnail, 'http://') || 
                    str_starts_with($event->thumbnail, 'https://')) {
                    $this->warn("Skipping URL: {$event->thumbnail}");
                    $skipped++;
                    continue;
                }

                $sourcePath = storage_path('app/public/' . $event->thumbnail);
                $destinationPath = public_path('storage/' . $event->thumbnail);

                // Check if source file exists
                if (!File::exists($sourcePath)) {
                    $this->error("Source file not found: {$sourcePath}");
                    $errors++;
                    continue;
                }

                // Check if destination already exists
                if (File::exists($destinationPath)) {
                    $this->comment("Already exists: {$destinationPath}");
                    $skipped++;
                    continue;
                }

                try {
                    // Ensure destination directory exists
                    File::ensureDirectoryExists(dirname($destinationPath));
                    
                    // Copy file
                    File::copy($sourcePath, $destinationPath);
                    
                    $this->info("✓ Copied: {$event->thumbnail}");
                    $fixed++;
                } catch (\Exception $e) {
                    $this->error("Failed to copy {$event->thumbnail}: {$e->getMessage()}");
                    $errors++;
                }
            }
        }

        $this->newLine();
        $this->info("=== Summary ===");
        $this->info("Fixed: {$fixed}");
        $this->info("Skipped: {$skipped}");
        $this->info("Errors: {$errors}");
        $this->info("Total events: {$events->count()}");

        return Command::SUCCESS;
    }
}

