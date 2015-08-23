<?php

namespace pointybeard\Kickstarter\ExportParser\Lib;

class RecordIterator implements \Iterator
{
    /**
     * @var resource Stream resource to file in archive
     */
    protected $stream;

    /**
     * @var array Field headers from the CSV data
     */
    protected $headers;

    /**
     * @var int Tracks which row in the data we are up to
     */
    protected $position;

    /**
     * @var object Holds an instance of->className containing the current row of data
     */
    protected $current;

    /**
     * @var int
     *          Used by the current() method. Allows current() to be called multiple times
     *          without advancing the cursor
     */
    protected $lastPosition;

    protected $chunkSize;

    /**
     * The constructor accepts the `$className`, `$statement`.
     *
     * @param string        $className
     * @param \PDOStatement $statement
     */
    public function __construct($stream, $chunkSize = 8192)
    {
        if (!is_resource($stream)) {
            throw new ZipArchiveException('RecordIterator requires a valid file stream.');
        }

        $this->chunkSize = $chunkSize;
        $this->stream = $stream;
        $this->className = $className;
        $this->current = null;
        $this->position = 0;
        $this->lastPosition = -1;

        $row = $this->fetchRow();

        $this->headers = array_map(function ($a) {return trim(preg_replace('/[^a-zA-Z -_]/i', '', $a));}, $row);
    }

    public function __destruct()
    {
        if (is_resource($this->stream)) {
            fclose($this->stream);
        }
    }

    protected function fetchRow()
    {
        $row = fgetcsv($this->stream, $this->chunkSize);
        if (count($row) == 1) {
            throw new ZipArchiveException('Data does not appear to be valid CSV. Please check contents.');
        }

        $this->position += $this->chunkSize;

        return array_map(function ($a) {return strlen(trim($a)) == 0 ? null : $a;}, $row);
    }

    /**
     * Returns the array containing field headers.
     *
     * @return array
     */
    public function headers()
    {
        return $this->headers;
    }

    /**
     * Create a new instance of $this->className and by calling
     * the fetch() method on $this->statement;.
     *
     * @return object
     */
    public function current()
    {
        // Check if the lastPosition is different to the current position.
        // If it is, then get a new object and update lastPosition.
        if ($this->lastPosition !== $this->position) {
            // Not always same number of data to headers due to survey not being completed by some members.
            $data = [];
            foreach ($this->fetchRow() as $key => $val) {
                $data[$this->headers[$key]] = $val;
            }

            $this->current = new Models\Record();
            foreach ($data as $key => $value) {
                $this->current->setField($key, $value);
            }
            $this->lastPosition = $this->position;
        }

        return $this->current;
    }

    /**
     * returns the current cursor position.
     *
     * @return int
     */
    public function key()
    {
        return $this->position;
    }

    /**
     * Increments the current cursor position. Also makes sure that
     * position is not create that the number of rows in data.
     *
     * @return bool
     */
    public function next()
    {
        $this->position += $this->chunkSize;

        return true;
    }

    /**
     * Executes the statement again, resetting the data and
     * changing the position to 0.
     */
    public function rewind()
    {
        return true;
    }

    /**
     * Checks that position is less than the total number of rows
     * in the data set.
     *
     * @return bool
     */
    public function valid()
    {
        return !feof($this->stream);
    }
}
