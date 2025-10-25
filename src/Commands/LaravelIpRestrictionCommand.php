<?php

namespace DigitalCoreHub\LaravelIpRestriction\Commands;

use Illuminate\Console\Command;
use DigitalCoreHub\LaravelIpRestriction\LaravelIpRestriction;

class LaravelIpRestrictionCommand extends Command
{
    public $signature = 'ip-restriction:list 
                        {--clear-cache : Clear the IP restriction cache}';

    public $description = 'Manage IP restrictions for Laravel applications';

    public function handle(LaravelIpRestriction $ipRestriction): int
    {
        if ($this->option('clear-cache')) {
            $ipRestriction->clearCache();
            $this->info('IP restriction cache cleared successfully.');
            return self::SUCCESS;
        }

        $this->info('Laravel IP Restriction - Allowed IPs:');
        $this->newLine();

        $allowedIps = config('ip-restriction.allowed_ips', []);
        
        if (empty($allowedIps)) {
            $this->warn('No allowed IPs configured.');
            $this->comment('Add IPs to config/ip-restriction.php file.');
            return self::SUCCESS;
        }

        $this->table(
            ['IP Address', 'Type', 'Status'],
            collect($allowedIps)->map(function ($ip) use ($ipRestriction) {
                $type = str_contains($ip, '/') ? 'CIDR Range' : 'Single IP';
                $status = $ipRestriction->isIpAllowed($ip) ? '✅ Active' : '❌ Invalid';
                
                return [$ip, $type, $status];
            })
        );

        $this->newLine();
        $this->comment('Use --clear-cache to clear the IP restriction cache.');

        return self::SUCCESS;
    }
}
