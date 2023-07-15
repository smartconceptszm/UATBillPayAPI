<?php

namespace App\Jobs;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Bus\Queueable;

abstract class BaseJob implements ShouldQueue
{
   /*
   |--------------------------------------------------------------------------
   | Queueable Jobs
   |--------------------------------------------------------------------------
   |
   | This job base class provides a central location to place any logic that
   | is shared across all of your jobs. The trait included with the class
   | provides access to the "queueOn" and "delay" queue helper methods.
   |
   */
   use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
}