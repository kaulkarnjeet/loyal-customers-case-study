#!/usr/bin/env php
<?php

require_once __DIR__ . '/src/LoyalCustomerFinder.php';

/**
 * CLI Entry point for loyal customer detection.
 * 
 * -----------------------------------------------
 * 🔍 Questions for the Product Owner
 * -----------------------------------------------
 * - Should unique pages be counted across both days or per day?
 * - Are malformed rows to be skipped silently?
 * - Should we filter bots or system visits?
 * - Should output format be plain or structured (JSON, CSV)?
 */


/**
 * -----------------------------------------------
 * CASE STUDY: STRATEGY EXPLANATION
 * -----------------------------------------------
 * Why did I choose this strategy?
 * - The problem involves log processing where we need to check visit overlap and page uniqueness.
 * - I chose to use associative arrays (hash maps) for each customer to store their visited pages.
 * - Then I compared customers who exist in both days and checked if they visited at least 2 unique pages.
 * - The logic is encapsulated inside an object-oriented class for separation of concerns and reusability.
 *
 * --------------------------------------------------------
 * How does this strategy affect performance/legibility?
 * --------------------------------------------------------
 * - Performance: The script reads files line-by-line using fgets(), which is memory-efficient for large logs.
 * - Using hash maps allows fast lookup and deduplication of visited pages.
 * - Legibility: The object-oriented structure makes the code modular, easier to test, and maintain.
 * - Method names like parseLogFile() and getLoyalCustomers() are self-explanatory and encourage readability.
 *
 * ---------------------------------------------------------------
 * Would I ask the Product Owner for more details before starting?
 * ---------------------------------------------------------------
 * - What is actual purpose of timestamp in log? Because we are not using it in our logic to check loyal customer.
 * - Should a loyal customer have visited 2+ unique pages across both days or 2+ each day?
 * - Are page and customer IDs case-sensitive?
 * - Is the output expected to be plain text, JSON, or a file? For now I assume that JSON is expected.
 * - Are bots or repeat visits to the same page relevant to the definition of "loyal"?
 * - Why we did not saving previous page from where user visited in logs? Because I think it will also help us to find loyal customer.
 * - Should we scale this for N days instead of just two?
 */

if ($argc !== 3) {
    echo "Usage: php find_loyal_customers.php <day1_log.txt> <day2_log.txt>\n";
    exit(1);
}

$day1 = $argv[1];
$day2 = $argv[2];

if (!file_exists($day1) || !file_exists($day2)) {
    echo "One or both files not found.\n";
    exit(1);
}

try {
    $finder = new LoyalCustomerFinder($day1, $day2);
    $loyalCustomers = $finder->getLoyalCustomers();

    echo "Loyal Customers:\n";
    echo json_encode($loyalCustomers);
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
    exit(1);
}
