<?php

namespace App\Console\Commands;

use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;
 use App\Models\Subscription;

#[Signature('app:expire-subscription')]
#[Description('Command description')]
class ExpireSubscription extends Command
{
    /**
     * Execute the console command.
     */
  

public function handle()
{
    Subscription::where('expired_at', '<', now())
        ->where('status', 'active')
        ->update([
            'status' => 'expired'
        ]);

    $this->info('Subscription expired updated');
}
}
