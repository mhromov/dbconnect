<?php

/**
 * db connection
 */
class DbConnect
{
    public $connect;
    public $query;
    public $table;
    public $time;

    public function __construct($config)
    {
        if (!isset($config['host']))
            $config['host'] = 'localhost';
        if (!isset($config['port']) && !isset($config['socket'])) {
            $this->connect = mysqli_connect($config['host'], $config['username'], $config['password'], $config['db_name']);
        } else if (!isset($config['socket'])) {
            $this->connect = mysqli_connect("localhost", $config['username'], $config['password'], $config['db_name'], $config['port']);
        } else {
            $this->connect = mysqli_connect("localhost", $config['username'], $config['password'], $config['db_name'], $config['port'], $config['socket']);
        }
        mysqli_set_charset($this->connect, "utf8");
        $this->table = '';
        return $this;
    }

    public static function saveImagef($file, $filename, $new_width = 0, $ext)
    {
        if ($_FILES['filename']['tmp_name'] != "") {
            $result = self::saveImage($file['tmp_name'], $new_width, $filename, $ext);
        } else {
            $result['success'] = 0;
            $result['error'] = 'File tmp_name is empty';
        }
        return $result;
    }

    public static function saveImage($tmp_name, $new_width, $filename, $ext)
    {
        list($width, $height) = getimagesize($tmp_name);
        if ($width <= 0 || $height <= 0) {
            $result['success'] = 0;
            $result['error'] = 'Wrong size. Height=' . $height . ', width=' . $width;
            return $result;
        }
        if ($new_width == 0 || $new_width > $width) $new_width = $width;
        $new_height = (int)($height * $new_width / $width);
        $filename .= $ext;
        $thumb = imagecreatetruecolor($new_width, $new_height);
        switch ($ext) {
            case '.jpeg':
                $source = @imagecreatefromjpeg($tmp_name);
                break;

            case '.jpg':
                $source = @imagecreatefromjpeg($tmp_name);
                break;

            case '.gif':
                $source = @imagecreatefromgif($tmp_name);
                break;

            case '.png':
                $source = @imagecreatefrompng($tmp_name);
                break;

            case '.bmp':
                $source = @imagecreatefromwbmp($tmp_name);
                break;
            default:
                $result['success'] = 0;
                $result['error'] = 'Wrong ext';
                return $result;
        }
        imagecopyresized($thumb, $source, 0, 0, 0, 0, $new_width, $new_height, $width, $height);
        switch ($ext) {
            case '.jpeg':
                $success = imagejpeg($thumb, $filename);
                break;

            case '.jpg':
                $success = imagejpeg($thumb, $filename);
                break;

            case '.gif':
                $success = imagegif($thumb, $filename);
                break;

            case '.png':
                $success = imagepng($thumb, $filename);
                break;

            case '.bmp':
                $success = imagewbmp($thumb, $filename);
                break;
            default:
                $result['success'] = 0;
                $result['error'] = 'Wrong extention';
                return $result;
                break;
        }
        $result['success'] = 1;
        $result['width'] = $new_width;
        $result['height'] = $new_height;
        $result['filename'] = $filename;
        return $result;
    }

    public static function saveImageSquared($tmp_name, $new_width, $filename, $ext)
    {
        list($width, $height) = getimagesize($tmp_name);
        if ($width <= 0 || $height <= 0) {
            $result['success'] = 0;
            $result['error'] = 'Wrong size. Height=' . $height . ', width=' . $width;
            return $result;
        }
        $min = min($width, $height);
        if ($new_width == 0 || $new_width > $width) $new_width = $width;
        $new_height = (int)($height * $new_width / $width);
        $new_width = min($new_height, $new_width);
        $new_height = $new_width;
        $filename .= $ext;
        $thumb = imagecreatetruecolor($new_width, $new_height);
        switch ($ext) {
            case '.jpeg':
                $source = @imagecreatefromjpeg($tmp_name);
                break;

            case '.jpg':
                $source = @imagecreatefromjpeg($tmp_name);
                break;

            case '.gif':
                $source = @imagecreatefromgif($tmp_name);
                break;

            case '.png':
                $source = @imagecreatefrompng($tmp_name);
                break;

            case '.bmp':
                $source = @imagecreatefromwbmp($tmp_name);
                break;
            default:
                $result['success'] = 0;
                $result['error'] = 'Wrong ext';
                return $result;
        }
        imagecopyresized($thumb, $source, 0, 0, ($width - $min) / 2, ($height - $min) / 2, $new_width, $new_height, $min, $min);
        switch ($ext) {
            case '.jpeg':
                $success = imagejpeg($thumb, $filename);
                break;

            case '.jpg':
                $success = imagejpeg($thumb, $filename);
                break;

            case '.gif':
                $success = imagegif($thumb, $filename);
                break;

            case '.png':
                $success = imagepng($thumb, $filename);
                break;

            case '.bmp':
                $success = imagewbmp($thumb, $filename);
                break;
            default:
                $result['success'] = 0;
                $result['error'] = 'Wrong extention';
                return $result;
                break;
        }
        $result['success'] = 1;
        $result['width'] = $new_width;
        $result['filename'] = $filename;
        return $result;
    }

    public function printMes()
    {
        if ($this->connect) {
            echo "Ok" . "<br>";
        } else {
            echo "Error: " . mysqli_error($this->connect) . "<br>";
        }
    }

    public function remove($table, $where = '', $limit = 0)
    {
        $time = microtime(true);
        if ($table === '') {
            $table = $this->table;
        } else {
            $this->table = $table;
        }
        if ($this->table === '') {
            return ['success' => 0, 'error' => "No table chosen"];
        }
        if (!$this->connect) {
            return ['success' => 0, 'error' => "No connection to db"];
        }

        $query = "DELETE FROM $table";
        if ($where != '') {
            $query .= " WHERE $where";
        }
        if ($limit != 0) {
            $query .= " LIMIT $limit";
        }
        $this->query = $query;
        $result = mysqli_query($this->connect, $query);
        $this->time = microtime(true) - $time;
//         echo "QUERY=$query\n";
        if (!$result)
            return ['success' => 0, 'error' => "Error inserting. Query='$query'. Error: " . mysqli_error($this->connect)];
        else
            return ['success' => true];
    }

    public function insert($table, $params)
    {
//         print_r($params);
        $time = microtime(true);
        if ($table === '') {
            $table = $this->table;
        } else {
            $this->table = $table;
        }
        if ($this->table === '') {
            return ['success' => 0, 'error' => "No table chosen"];
        }
        if (!$this->connect) {
            return ['success' => 0, 'error' => "No connection to db"];
        }
        if (count($params) > 0) {
            $keys = '';
            $values = '';
            $flag = false;
            foreach ($params as $key => $param) {
                if ($flag) {
                    $keys .= ',';
                    $values .= ',';
                }
                $keys .= $this->stringPrep($key);
                $values .= "'" . $this->stringPrep($param) . "'";
                $flag = true;
            }
            $query = "INSERT INTO $table ($keys) VALUES ($values)";
            /*
             if ($where!='') {
             $query.=" WHERE $where";
             }
             if ($limit!=0) {
             $query.=" LIMIT $limit";
             }
             */
            $this->query = $query;
            $result = mysqli_query($this->connect, $query);
            $this->time = microtime(true) - $time;
//             echo "QUERY=$query\n";
            if (!$result)
                return ['success' => 0, 'error' => "Error inserting. Query='$query'. Error: " . mysqli_error($this->connect)];
            else
                return ['success' => true, 'id' => mysqli_insert_id($this->connect)];
        }
        return ['success' => 0, 'error' => "No params"];
    }

    public function stringPrep($text)
    {
        return mysqli_real_escape_string($this->connect, $text);
    }

    public function get($table, $where = '', $limit = 0, $sort = '', $select = [])
    {
        $time = microtime(true);
        if ($table === '') {
            $table = $this->table;
        } else {
            $this->table = $table;
        }
        if ($this->table === '') {
            return ['success' => 0, 'error' => "No table chosen"];
        }
        if (!$this->connect) {
            return ['success' => 0, 'error' => "No connection to db"];
        }
        if (isset($select[0])) {
            $cells = '';
            $flag = false;
            foreach ($select as $sel) {
                if ($flag)
                    $cells .= ',';
                $cells .= $sel;
                $flag = true;
            }
        } else {
            $cells = '*';
        }
        $query = "SELECT $cells FROM $table";
        if ($where != '') {
            $query .= " WHERE $where";
        }
        if ($sort != '') {
            $query .= " ORDER BY $sort";
        }
        $answer['success'] = true;
        if (intval($limit) > 0)
            $query .= " LIMIT $limit";
//         echo "\nQUERY=$query\n";
        $result = mysqli_query($this->connect, $query);
        if (!$result)
            return ['success' => 0, 'error' => "Error getting. Error: " . mysqli_error($this->connect)];
        $answer = [];
        $i = 0;
        while ($r = mysqli_fetch_array($result, MYSQLI_ASSOC)) {
            $answer[$i] = $r;
            $i++;
        }
        $this->query = $query;
        $this->time = microtime(true) - $time;
        return $answer;
    }

    public function time()
    {
        echo "Time:<br> ";
        echo (int)($this->time * 10000) / 10.;
        echo "ms <br>";
    }

    public function plusOne($table, $param, $where = '', $limit = 0, $minus = false)
    {
        $time = microtime(true);
        if ($table === '') {
            $table = $this->table;
        } else {
            $this->table = $table;
        }
        if ($this->table === '') {
            return ['success' => 0, 'error' => "No table chosen"];
        }
        if (!$this->connect) {
            return ['success' => 0, 'error' => "No connection to db"];
        }
        if (count($param) > 0) {
            if ($minus) {
                $query = "UPDATE $table SET $param=$param-1";
            } else {
                $query = "UPDATE $table SET $param=$param+1";
            }
            if ($where != '') {
                $query .= " WHERE $where";
            }
            if (intval($limit) > 0) {
                $query .= " LIMIT $limit";
            }
            $this->query = $query;
            $result = mysqli_query($this->connect, $query);
            $this->time = microtime(true) - $time;
            if (!$result)
                return ['success' => 0, 'error' => "Error updating. Error: " . mysqli_error($this->connect)];
            else
                return ['success' => true];
        }
        return ['success' => 0, 'error' => "No param"];
    }

    public function update($table, $params, $where = '', $limit = 0)
    {
        $time = microtime(true);
        if ($table === '') {
            $table = $this->table;
        } else {
            $this->table = $table;
        }
        if ($this->table === '') {
            return ['success' => 0, 'error' => "No table chosen"];
        }
        if (!$this->connect) {
            return ['success' => 0, 'error' => "No connection to db"];
        }
        if (count($params) > 0) {
            $keys = '';
            $values = '';
            $flag = false;
            foreach ($params as $key => $param) {
                if ($flag) {
                    $values .= ',';
                }
                $values .= $this->stringPrep($key) . "='" . $this->stringPrep($param) . "'";
                $flag = true;
            }
            $query = "UPDATE $table SET $values";
            if ($where != '') {
                $query .= " WHERE $where";
            }
            if (intval($limit) > 0) {
                $query .= " LIMIT $limit";
            }
            $this->query = $query;
            //         echo "\nQUERY=$query\n";
            $result = mysqli_query($this->connect, $query);
            $this->time = microtime(true) - $time;
            if (!$result)
                return ['success' => 0, 'error' => "Error updating. Error: " . mysqli_error($this->connect)];
            else
                return ['success' => true];
        }
        return ['success' => 0, 'error' => "No params"];
    }

    public function query($query)
    {
        $time = microtime(true);
        $result = mysqli_query($this->connect, $query);
//        $this->insert()
        if (!$result)
            return ['success' => 0, 'error' => "Error getting. Error: " . mysqli_error($this->connect)];
        $answer = [];
        $i = 0;
        while ($r = mysqli_fetch_array($result, MYSQLI_ASSOC)) {
            $answer[$i] = $r;
            $i++;
        }
        $this->query = $query;
        $this->time = microtime(true) - $time;
        return $answer;
    }

    public function close()
    {
        mysqli_close($this->connect);
    }

}

//----- END CLASS DBCONNECT
