<?php

use Illuminate\Database\Query\Expression;

class DataseriesService {

    const SERVICE_NAME = "dataseriesService";

    /**
     * Checks that a dataseries exists with the given id
     *
     * @param $dataseriesId
     * @return bool
     */
    public function dataseriesExists($dataseriesId) {
        $dataseries = Dataseries::find($dataseriesId);
        if (is_array($dataseries)) {
            return false;
        }
        return !is_null(Dataseries::find($dataseriesId));
    }

    /**
     * Add a new reading value to the given dataseries. Timestamps are filled in automatically.
     *
     * @param integer $dataseriesId
     * @param string $value
     * @return Reading the created reading
     */
    public function addReading($dataseriesId, $value) {
        $reading = new Reading;
        $reading['dataseries_id'] = $dataseriesId;
        $reading->value = $value;

        $reading->save();

        return $reading;
    }

    /**
     * Return dataseries summaries for all dataseries stored in the database. The records will
     * have the following attributes:
     * - id (dataseries id)
     * - name
     * - description
     * - label
     * - current_value (the last value stored for the dataseries)
     * - min_value
     * - max_value
     *
     * @param DateTime $startTime Earliest time when readings are included in summaries
     * @return mixed Summaries for all dataseries
     */
    public function getAllDataseriesSummaries($startTime = NULL) {
        if (is_null($startTime)) {
            $startTime = new DateTime('now');
            $startTime->modify('-30 days');
        }

        $dataseriesSummaryQuery = $this->getDataseriesSummaryQuery();

        // Apparently "you cannot use a named parameter marker of the same name more
        // than once in a prepared statement". http://php.net/manual/en/pdo.prepare.php
        $queryParameters = array(
            'startTime1' => $startTime->getTimestamp(),
            'startTime2' => $startTime->getTimestamp(),
            'startTime3' => $startTime->getTimestamp()
        );

        $results = DB::select($dataseriesSummaryQuery, $queryParameters);

        return $results;
    }

    /**
     * @param $dataseriesId
     * @return array
     */
    public function getDataseries($dataseriesId) {
        $dataseriesIdColumnName = $this->getForeignKeyColumnName(Dataseries::TABLE_NAME);
        $readingTableName = $this->getPrefixedTableName(Reading::TABLE_NAME);
        $dataseriesTableName = $this->getPrefixedTableName(Dataseries::TABLE_NAME);

        $sql = "
            SELECT 
                s.id AS id, 
                s.name AS name,
                s.description AS description,
                s.label,
                MIN(CAST(r.value AS DECIMAL(20,5))) AS min_value, 
                MAX(CAST(r.value AS DECIMAL(20,5))) AS max_value
            FROM   {$readingTableName} r
            LEFT JOIN {$dataseriesTableName} s ON (s.id = r.{$dataseriesIdColumnName})
            WHERE  r.{$dataseriesIdColumnName} = ? ";

        $results =  DB::select($sql, array($dataseriesId));

        if (count($results) > 0) {
            return $results[0];
        }

        return $results;
    }

    /**
     * Return the average readings for the given dataseries. The returned array has the format:
     * <code>
     * {
     *  "dataseriesId": $dataseriesId,
     *  "readings": [ {"x": <created_at as millisec after the epoc>, "y": <reading value>}, ...]
     * }
     * </code>
     *
     * @param $dataseriesId
     * @param int $numberOfDays
     * @return mixed
     */
    public function getDataseriesAverages($dataseriesId, $numberOfDays = 50) {
        $dataseriesIdColumnName = $this->getForeignKeyColumnName(Dataseries::TABLE_NAME);
        $readingTableName = $this->getPrefixedTableName(Reading::TABLE_NAME);

        $sql = "
          SELECT UNIX_TIMESTAMP(created_at)*1000 AS x, average_value AS y FROM (
            SELECT
            DATE(r.created_at) AS created_at,
            AVG(CAST(r.value AS DECIMAL(20,5))) AS average_value
            FROM   {$readingTableName} r
            WHERE  r.{$dataseriesIdColumnName} = ?
            GROUP BY DATE(r.created_at)
            ORDER BY DATE(r.created_at) DESC LIMIT ?
          ) AS A ORDER BY A.created_at ASC; ";

        $results =  DB::select($sql, array($dataseriesId, $numberOfDays));

        return array(
            'dataseriesId' => $dataseriesId,
            'readings' => $results);
    }

    /**
     * Return the readings for the given dataseries. The returned array has the format:
     * <code>
     * {
     *  "dataseriesId": $dataseriesId,
     *  "readings": [ {"x": <created_at as millisec after the epoc>, "y": <reading value>}, ...]
     * }
     * </code>
     *
     * If the $fromTime parameter is not given, the earliest time for the readings is limited
     * to 30 days before the current time.
     *
     * @param $dataseriesId
     * @param DateTime $fromTime Earliest time when readings are included in the response
     * @return array Array containg $dataseriesId and the readings as an array of (x,y) values
     */
    public function findReadings($dataseriesId, $fromTime = NULL) {
        $fromTime = $this->getDateTimeOrModifiedFromNow($fromTime, '-30 days');

        $dataseriesIdColumnName = $this->getForeignKeyColumnName(Dataseries::TABLE_NAME);

        $readings = DB::table(Reading::TABLE_NAME)
            ->where($dataseriesIdColumnName, '=', $dataseriesId)
            ->where('created_at', '>=', $fromTime->getTimestamp())
            ->orderBy('created_at', 'DESC')
            ->limit(500)
            ->get(['created_at', 'value']);

        $readingData = [];
        foreach (array_reverse($readings) as $key => $row) {
            $readingData[] = array('x' => strtotime($row->created_at) * 1000, 'y' => $row->value);
        }

        return array(
            'dataseriesId' => $dataseriesId,
            'readings' => $readingData);
    }

    /**
     * Get the readings from a dataseries in an array
     * @param $dataseriesId
     * @param null|DateTime $fromTime
     * @return array
     */
    public function getDataseriesReadings($dataseriesId, $fromTime = NULL) {
        $fromTime = $this->getDateTimeOrModifiedFromNow($fromTime, '-30 days');

        $dataseriesIdColumnName = $this->getForeignKeyColumnName(Dataseries::TABLE_NAME);

        $readings = DB::table(Reading::TABLE_NAME)
            ->addSelect('created_at')
            ->addSelect('value')
            ->where($dataseriesIdColumnName, '=', $dataseriesId)
            ->where('created_at', '>=', $fromTime->getTimestamp())
            ->orderBy('created_at', 'DESC')
            ->limit(500)
            ->get();

        return array_reverse($readings);
    }

    /**
     * @param DateTime $dateTime
     * @param null|string $modification
     * @return DateTime
     */
    private function getDateTimeOrModifiedFromNow($dateTime, $modification = NULL) {
        if (is_null($dateTime)) {
            $dateTime = new DateTime('now');
            if (!is_array($modification)) {
                $dateTime->modify($modification);
            }
        }
        return $dateTime;
    }

    /**
     * This query has been tested with MySQL only. It might need changes to get it working with
     * other databases.
     *
     * Requires optimization if there are a lot of dataseries in the system.
     *
     * @return mixed The query for getting the summaries
     */
    private function getDataseriesSummaryQuery() {
        $dataseriesIdColumnName = $this->getForeignKeyColumnName(Dataseries::TABLE_NAME);
        $readingTableName = $this->getPrefixedTableName(Reading::TABLE_NAME);
        $dataseriesTableName = $this->getPrefixedTableName(Dataseries::TABLE_NAME);

        $query = "
            SELECT
                r.{$dataseriesIdColumnName} AS id,
                s.name AS name,
                s.description AS description,
                s.label AS label,
                CAST(r.value AS DECIMAL(20,5)) AS current_value,
                CAST(minmax.minval AS DECIMAL(20,5)) AS min_value,
                CAST(minmax.maxval AS DECIMAL(20,5)) AS max_value
            FROM {$readingTableName} r
            INNER JOIN {$dataseriesTableName} s ON (s.id = r.{$dataseriesIdColumnName})
            INNER JOIN (
                    SELECT t.{$dataseriesIdColumnName} AS {$dataseriesIdColumnName}, MAX(t.created_at) AS created_at
                    FROM   {$readingTableName} t
                    WHERE  t.created_at >= :startTime1
                    GROUP BY t.${dataseriesIdColumnName}
                ) AS u ON u.{$dataseriesIdColumnName} = r.{$dataseriesIdColumnName} AND u.created_at = r.created_at
            INNER JOIN (
                    SELECT 
                          {$dataseriesIdColumnName}, 
                          MIN(CAST(value AS DECIMAL(20,5))) AS minval, 
                          MAX(CAST(value AS DECIMAL(20,5))) AS maxval
                    FROM   {$readingTableName}
                    WHERE  created_at >= :startTime2
                    GROUP BY {$dataseriesIdColumnName}
                ) AS minmax ON (minmax.{$dataseriesIdColumnName} = r.{$dataseriesIdColumnName})
            WHERE r.created_at >= :startTime3 ";

        return DB::raw($query);
    }

    public static function getForeignKeyColumnName($tableName) {
        return $tableName . "_id";
    }

    private function getPrefixedTableName($tableName) {
        return DB::getTablePrefix() . $tableName;
    }
}
