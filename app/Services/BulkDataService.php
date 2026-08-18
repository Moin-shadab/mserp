<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;

class BulkDataService
{
    /**
     * Process high-volume bulk array insertions in single-transaction chunks.
     * Reduces 100,000 requests down to ~20 database transaction queries.
     *
     * @param string $table Physical DB table name
     * @param array $records Array of associative row arrays
     * @param int $chunkSize Number of rows per transaction batch (default 2500)
     * @return array Ingestion stats
     */
    public static function processBulkInsert(string $table, array $records, int $chunkSize = 2500): array
    {
        if (empty($records)) {
            return [
                'success' => true,
                'total_inserted' => 0,
                'chunks_processed' => 0,
                'execution_time_ms' => 0
            ];
        }

        $startTime = microtime(true);
        $totalInserted = 0;
        $chunksProcessed = 0;

        $chunks = array_chunk($records, max(1, $chunkSize));

        DB::transaction(function () use ($table, $chunks, &$totalInserted, &$chunksProcessed) {
            foreach ($chunks as $chunk) {
                $now = now();
                foreach ($chunk as &$row) {
                    if (!isset($row['created_at'])) {
                        $row['created_at'] = $now;
                    }
                    if (!isset($row['updated_at'])) {
                        $row['updated_at'] = $now;
                    }
                }
                unset($row);

                DB::table($table)->insert($chunk);
                $totalInserted += count($chunk);
                $chunksProcessed++;
            }
        });

        $executionTimeMs = round((microtime(true) - $startTime) * 1000, 2);

        return [
            'success' => true,
            'total_inserted' => $totalInserted,
            'chunks_processed' => $chunksProcessed,
            'execution_time_ms' => $executionTimeMs
        ];
    }
}
