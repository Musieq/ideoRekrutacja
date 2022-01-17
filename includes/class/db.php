<?php

class DB {

    protected mysqli $connection;
    protected object $query;
    protected bool $queryClosed = true;
    protected bool $showErrors = true;

    public function __construct($dbHost, $dbUser, $dbPassword, $dbName) {
        $this->connection = new mysqli($dbHost, $dbUser, $dbPassword, $dbName);

        $this->connection->set_charset('utf8');

        if ($this->connection->connect_error) {
            $this->error('Failed to connect to MySQL - ' . $this->connection->connect_error);
        }
    }

    public function query($stmt): object {
        if (!$this->queryClosed) {
            $this->query->close();
            $this->queryClosed = true;
        }

        if ($this->connection->prepare($stmt)) {
            $this->query = $this->connection->prepare($stmt);

            // If more than 1 argument is passed to the function, it is prepared statement
            if (func_num_args() > 1) {
                $args = func_get_args();
                $args = array_slice($args, 1);
                $argsArray = [];
                $types = '';

                foreach ($args as $arg) {
                    if (is_array($arg)) {
                        $argsArray = $arg;
                        foreach ($arg as $arrArg) {
                            $types .= $this->_gettype($arrArg);
                        }
                    } else {
                        $argsArray[] = $arg;
                        $types .= $this->_gettype($arg);
                    }
                }

                $this->query->bind_param($types, ...$argsArray);
            }

            // Execute query
            $this->query->execute();

            if ($this->query->errno) {
                $this->error('Unable to process MySQL query. Check your params - ' . $this->query->error);
            }

            $this->queryClosed = false;

        } else {
            $this->error('Unable to prepare MySQL statement. Check your syntax - ' . $this->connection->error);
        }

        // Return object - results ($query)
        return $this;
    }

    public function fetchAll(): array {
        $results = $this->query->get_result();
        $this->query->close();
        $this->queryClosed = true;

        return $results->fetch_all(MYSQLI_ASSOC);
    }


    public function fetchArray(): array {
        $results = $this->query->get_result();
        $this->query->close();
        $this->queryClosed = true;

        $allRows = [];
        // Get all rows
        while($row = $results->fetch_array(MYSQLI_NUM)){
            $allRows[] = $row;
        }

        return $allRows;
    }

    public function numRows(): int {
        $this->query->store_result();
        $numRows = $this->query->num_rows;

        $this->query->close();
        $this->queryClosed = true;

        return $numRows;
    }

    public function affectedRows(): int {
        return $this->query->affected_rows;
    }

    public function lastInsertID(): int {
        return $this->connection->insert_id;
    }

    protected function error($error) {
        if ($this->showErrors) {
            exit($error);
        }
    }

    private function _gettype($var): string {
        if (is_string($var)) return 's';
        if (is_float($var)) return 'd';
        if (is_int($var)) return 'i';
        return 'b';
    }
}
