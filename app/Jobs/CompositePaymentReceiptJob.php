<?php

namespace App\Jobs;

use App\Http\Services\Utility\CompositeReceiptingBinderService;
use App\Http\Services\Payments\CompositePaymentReceiptService;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\App;
use App\Jobs\BaseJob;

class CompositePaymentReceiptJob extends BaseJob
{

    public function __construct(
        private array $allocationData
    ) {
        // Store the data needed for processing
    }

    public function handle(CompositeReceiptingBinderService $serviceBinder): void {
        try {
            // Setup service bindings FIRST
            $serviceBinder->bind($this->allocationData['urlPrefix'],$this->allocationData['menu_id']);
            
            // Now resolve the service (after bindings are set)
            $receiptService = App::make(CompositePaymentReceiptService::class);
            
            // Process the receipt
            $receiptService->create($this->allocationData);
            
        } catch (\Throwable $e) {
            // Log error and potentially retry
            Log::error('Receipt composite payment job failed: ' . $e->getMessage(), [
                'allocation_data' => $this->allocationData,
                'trace' => $e->getTraceAsString()
            ]);
            
            throw $e; // Re-throw to trigger job failure handling
        }
    }

}