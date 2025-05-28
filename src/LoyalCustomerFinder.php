<?php

class LoyalCustomerFinder
{
    private string $file1;
    private string $file2;

    public function __construct(string $file1, string $file2)
    {
        $this->file1 = $file1;
        $this->file2 = $file2;
    }

    public function getLoyalCustomers(): array
    {
        $day1Data = $this->parseLogFile($this->file1);
        $day2Data = $this->parseLogFile($this->file2);

        $loyalCustomers = [];

        foreach ($day1Data as $customerId => $pages1) {
            if (isset($day2Data[$customerId])) {
                $pages2 = $day2Data[$customerId];
                $totalPages = array_unique(array_merge($pages1, $pages2));

                if (count($totalPages) >= 2) {
                    $loyalCustomers[] = $customerId;
                }
            }
        }

        return $loyalCustomers;
    }

    private function parseLogFile(string $filePath): array
    {
        $customers = [];

        $handle = fopen($filePath, 'r');
        if (!$handle) {
            throw new RuntimeException("Cannot open file: $filePath");
        }

        while (($line = fgets($handle)) !== false) {
            $parts = preg_split('/\s+/', trim($line));
            if (count($parts) !== 3) continue;

            [, $pageId, $customerId] = $parts;

            if (!isset($customers[$customerId])) {
                $customers[$customerId] = [];
            }

            $customers[$customerId][$pageId] = true;
        }

        fclose($handle);

        // Flatten page hash sets to lists
        foreach ($customers as $customerId => $pages) {
            $customers[$customerId] = array_keys($pages);
        }

        return $customers;
    }
}
