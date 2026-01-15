<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Inspeccion;
use Carbon\Carbon;

class CheckInspectionExpiration extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'inspections:check-expiration';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Check and expire inspections pending signature for more than 24 hours';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Checking for expired inspections...');

        $inspections = Inspeccion::where('status', 'pending_signature')
            ->with([
                    'signatures' => function ($query) {
                        $query->where('role', 'inspector');
                    }
                ])
            ->get();

        $count = 0;

        foreach ($inspections as $inspection) {
            $inspectorSig = $inspection->signatures->first();

            if ($inspectorSig) {
                $signedAt = $inspectorSig->signed_at;

                if ($signedAt->addHours(24)->isPast()) {
                    $inspection->status = 'expired';
                    $inspection->expires_at = Carbon::now();
                    $inspection->save();

                    $this->info("Inspection ID {$inspection->id} expired.");
                    $count++;
                }
            }
        }

        $this->info("Done. {$count} inspections expired.");
    }
}
