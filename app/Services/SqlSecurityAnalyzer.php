<?php

namespace App\Services;

class SqlSecurityAnalyzer
{
    /**
     * Forbidden SQL keywords that represent DDL, DML mutations, or administrative execution.
     */
    protected static array $forbiddenKeywords = [
        'INSERT', 'UPDATE', 'DELETE', 'DROP', 'ALTER', 'TRUNCATE', 'CREATE',
        'RENAME', 'GRANT', 'REVOKE', 'CALL', 'EXEC', 'EXECUTE', 'LOAD',
        'INTO', 'OUTFILE', 'DUMPFILE', 'SHUTDOWN', 'INFORMATION_SCHEMA'
    ];

    /**
     * Analyze developer-supplied SQL and throw exception if security checks fail.
     *
     * @param string $sql
     * @return bool
     * @throws \InvalidArgumentException
     */
    public static function validateSelectQuery(string $sql): bool
    {
        $cleanSql = trim($sql);

        if (empty($cleanSql)) {
            throw new \InvalidArgumentException('SQL query cannot be empty.');
        }

        // 1. Block multiple SQL statements (semicolons separating queries)
        if (str_contains($cleanSql, ';')) {
            $statements = array_filter(array_map('trim', explode(';', $cleanSql)));
            if (count($statements) > 1) {
                throw new \InvalidArgumentException('Multiple SQL statements are strictly prohibited for security.');
            }
        }

        // 2. Ensure query starts with SELECT or WITH (CTE)
        $uppercaseSql = strtoupper($cleanSql);
        if (!str_starts_with($uppercaseSql, 'SELECT') && !str_starts_with($uppercaseSql, 'WITH')) {
            throw new \InvalidArgumentException('Only SELECT queries are allowed in Developer Studio.');
        }

        // 3. Scan for forbidden keywords
        foreach (self::$forbiddenKeywords as $keyword) {
            $pattern = '/\b' . $keyword . '\b/i';
            if (preg_match($pattern, $cleanSql)) {
                throw new \InvalidArgumentException("Forbidden SQL keyword detected: '{$keyword}'.");
            }
        }

        return true;
    }
}
