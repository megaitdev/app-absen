<?php

namespace App\Jobs;

use App\Http\Controllers\Report\ReportController;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\Http\Controllers\Resource\ScriptController;
use App\Http\Controllers\Resource\CssController;

class generateReportJob implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new job instance.
     */
    public function __construct()
    {
        //
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $reportController = new ReportController(new ScriptController(), new CssController());
        $reportController->generateReportEmployee();
    }
}
