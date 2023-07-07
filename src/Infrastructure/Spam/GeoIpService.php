<?php

namespace App\Infrastructure\Spam;

use GeoIp2\Exception\AddressNotFoundException;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use GeoIp2\Database\Reader;

class GeoIpService
{

    private ?Reader $reader = null;

    public function __construct(
        #[Autowire('%geoip_database%')]
        private readonly string $dbPath
    ) {
    }

    public function getLocation(string $ip): ?GeoIpRecord
    {
        try {
            $record = $this->getReader()->country($ip);
            if (!$record->country->isoCode) {
                return null;
            }
            return new GeoIpRecord(
                country: $record->country->isoCode,
            );
        } catch (AddressNotFoundException $e) {
            return null;
        }
    }

    private function getReader(): Reader
    {
        if (!$this->reader) {
            $this->reader = new Reader($this->dbPath);
        }
        return $this->reader;
    }
}
