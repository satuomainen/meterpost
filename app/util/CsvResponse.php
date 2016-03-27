<?php
use Illuminate\Support\Facades\Response;

/**
 * Class CsvResponse
 *
 * Creates CSV file download streams from a controller
 */
class CsvResponse extends Response {

    /**
     * Example usage in a controller method that should return a CSV file:
     * <code>
     *  $allUsers = DB::table('users')
     *      ->addSelect('username')
     *      ->addSelect('email')
     *      ->addSelect('plaintext_password')
     *      ->get();
     *  $headings = array('User name', 'Email address', 'Password');
     *  return CsvResponse::asCsv($headings, $allUsers, 'users.csv');
     * </code>
     *
     * @param $arrayOfHeadings  Array of strings printed as the first row.
     * @param $arrayOfValueObjects Assumes this is an array objects like rows from a database, not an associative array
     * @param $downloadFilename
     * @param string $delimiter
     * @param string $endOfRecord
     * @param array $httpHeaders
     * @return \Symfony\Component\HttpFoundation\StreamedResponse
     */
    public static function asCsv($arrayOfHeadings,
                                 $arrayOfValueObjects,
                                 $downloadFilename,
                                 $delimiter = ";",
                                 $endOfRecord = "\n",
                                 $httpHeaders = array()) {

        return static::stream(function () use ($arrayOfHeadings, $arrayOfValueObjects, $delimiter, $endOfRecord) {
            /**
             * RFC 4180: "If double-quotes are used to enclose fields, then a double-quote appearing
             * inside a field must be escaped by preceding it with another double quote."
             * @param $str
             * @return string
             */
            $escapeQuotesAndQuote = function ($str) {
                return '"' . str_replace('"', '""', $str) . '"';
            };

            // Output the headings
            $headings = array_map($escapeQuotesAndQuote, $arrayOfHeadings);
            echo implode($delimiter, $headings) . $endOfRecord;

            // Output the values
            foreach ($arrayOfValueObjects as $key => $obj) {
                $values = array_map($escapeQuotesAndQuote, get_object_vars($obj));
                echo implode($delimiter, $values) . $endOfRecord;
            }
        }, 200, array_merge(array(
            'Content-type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename=' . $downloadFilename,
        ), $httpHeaders));
    }
}
