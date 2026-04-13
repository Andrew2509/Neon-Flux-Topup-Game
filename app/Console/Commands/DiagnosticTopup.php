<?php

namespace App\Console\Commands;

use App\Models\Category;
use App\Models\Order;
use App\Models\Provider;
use App\Models\Service;
use App\Services\IPaymuService;
use App\Services\TokovoucherService;
use Illuminate\Console\Command;

class DiagnosticTopup extends Command
{
    protected $signature = 'app:diagnostic-topup';
    protected $description = 'Health check for the top-up system, including connection, settings, and product audit.';

    public function handle()
    {
        $this->header('Top-up System Diagnostic Tool');

        // 1. Connection Checks
        $this->section('1. API Connectivity Checks');
        
        $this->checkTokovoucher();
        $this->checkIPaymu();

        // 2. Audit Products
        $this->section('2. Product Configuration Audit');
        $this->auditProducts();

        // 3. System Health
        $this->section('3. System Health Check');
        $this->checkSystemHealth();

        $this->info("\nDiagnostic complete.");
    }

    private function header($text)
    {
        $this->newLine();
        $this->line('<bg=blue;fg=white;options=bold> ' . str_pad($text, 60) . ' </>');
        $this->newLine();
    }

    private function section($text)
    {
        $this->newLine();
        $this->line('<fg=yellow;options=bold>' . $text . '</>');
        $this->line(str_repeat('-', 60));
    }

    private function checkTokovoucher()
    {
        $this->info("Checking Tokovoucher API...");
        $tv = new TokovoucherService();
        $balance = $tv->checkBalance();

        if ($balance !== null) {
            $this->line("  [OK] Tokovoucher Connected. Balance: Rp " . number_format($balance, 0, ',', '.'));
        } else {
            $this->error("  [FAIL] Tokovoucher API Error. Check your credentials in 'providers' table.");
        }
    }

    private function checkIPaymu()
    {
        $this->info("Checking iPaymu API...");
        $ipaymu = new IPaymuService();
        $res = $ipaymu->getBalance();

        if (isset($res['Status']) && $res['Status'] == 200) {
            $balance = $res['Data']['MerchantBalance'] ?? 0;
            $this->line("  [OK] iPaymu Connected. Merchant Balance: Rp " . number_format($balance, 0, ',', '.'));
        } else {
            $this->error("  [FAIL] iPaymu API Error. Status: " . ($res['Status'] ?? 'Unknown'));
        }
    }

    private function auditProducts()
    {
        $totalServices = Service::count();
        $activeServices = Service::where('status', 'Aktif')->count();
        $badServices = Service::whereNull('product_code')->orWhere('product_code', '')->count();
        $inactiveCategories = Category::where('status', '!=', 'Aktif')->count();

        $this->line("  Total Services: " . $totalServices);
        $this->line("  Active Services: " . $activeServices);
        
        if ($badServices > 0) {
            $this->warn("  [WARN] Found " . $badServices . " services with missing product codes!");
        } else {
            $this->line("  [OK] All services have product codes.");
        }

        if ($inactiveCategories > 0) {
            $this->warn("  [INFO] " . $inactiveCategories . " categories are currently inactive/non-visible.");
        }

        // Check for common misconfigurations
        $missingProvider = Service::whereNull('provider')->orWhere('provider', '')->count();
        if ($missingProvider > 0) {
            $this->warn("  [WARN] " . $missingProvider . " services are missing a designated Provider.");
        }
    }

    private function checkSystemHealth()
    {
        // 1. Storage writable
        if (is_writable(storage_path('logs'))) {
            $this->line("  [OK] Storage directory is writable.");
        } else {
            $this->error("  [FAIL] Storage directory is NOT writable!");
        }

        // 2. Pending Orders
        $pending = Order::where('status', 'pending_payment')->count();
        if ($pending > 50) {
            $this->warn("  [WARN] There are " . $pending . " pending payment orders. Some might be abandoned or callback failed.");
        } else {
            $this->line("  [OK] Pending orders: " . $pending);
        }

        // 3. Queue Check (Optional, depends on setup)
        $this->line("  [INFO] Ensure your queue worker is running to process 'ProcessSupplierOrder' jobs.");
    }
}
