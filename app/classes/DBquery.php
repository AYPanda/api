<?php

namespace classes;
/**
 * Работа с БД
 * // Выборка одного значения
 * $count = $db->queryValue('SELECT COUNT(*) FROM users');
 *
 * // Выборка набора записей
 * $users = $db->queryRows('SELECT * FROM users WHERE name LIKE ?', array('%username%'));
 *
 * // Выборка одной записи
 * $user = $db->queryRow('SELECT * FROM users WHERE id=:id', array(':id' => 123));
 *
 * // Добавление записи (INSERT) и получение значения поля AUTO_INCREMENT
 * $newUserId = $db->insert('users', array('name' => 'NewUserName', 'password' => 'zzxxcc'));
 *
 * // Изменение записи (UPDATE)
 * $db->update('users', array('name' => 'UpdatedName'), 'id=:id', array(':id' => $newUserId));
 */
class DBquery
{
    private $db;

    function __construct($user = 'root', $pass = 'fktiby123')
    {

        $db = new \PDO('mysql:host=db_d;dbname=diplom;charset=utf8', $user, $pass);
        $this->db = $db;
    }

    /**
     * Добавление записи в таблицу
     * @param  [string] $table        [Название таблицы]
     * @param  [array] $fields       [description]
     * @param  [type] $insertParams [description]
     * @return [type]               [description]
     */
    public function insert($table, $fields, $insertParams = null)
    {
        try {
            $result = null;
            $names = '';
            $vals = '';
            foreach ($fields as $name => $val) {
                if (isset($names[0])) {
                    $names .= ', ';
                    $vals .= ', ';
                }
                $names .= $name;
                $vals .= ':' . $name;
            }
            $ignore = isset($insertParams['ignore']) && $insertParams['ignore'] ? 'IGNORE' : '';
            $sql = "INSERT $ignore INTO " . $table . ' (' . $names . ') VALUES (' . $vals . ')';
            $rs = $this->db->prepare($sql);
            foreach ($fields as $name => $val) {
                $rs->bindValue(':' . $name, $val);
            }
            if ($rs->execute()) {
                $result = $this->db->lastInsertId(null);
            }
            return $result;
        } catch (Exception $e) {
            $this->report($e);
        }
    }

    public function where($table, $where = null, $isFirst = false, $select = "*", $params = null) {
        try {
            if ($where == null) {
                $sql = "SELECT $select FROM $table";
            } else {
                $sql = "SELECT $select FROM $table WHERE $where";
            }
            if ($isFirst) {
                return $this->queryRow($sql, $params);
            } else {
                return $this->queryRows($sql,$params);
            }
        } catch (\Exception $e) {
            $this->report($e);
        }
    }

    // Returns true/false
    public function updateParams($table, array $fields, $where, array $params = null) {
        try {
            $sql = 'UPDATE ' . $table . ' SET ';
            $first = true;
            foreach ($fields as $name => $val) {
                if (!$first) {
                    $first = false;
                    $sql .= ', ';
                }
                $first = false;
                $sql .= $name . ' = '.$val;
            }
            if (!is_array($params)) {
                $params = array();
            }
            $sql .= ' WHERE ' . $where;
            $rs = $this->db->prepare($sql);
            $result = $rs->execute($params);
            return $result;
        } catch(Exception $e) {
            $this->report($e);
        }
    }
    /**
     * [update description]
     * @param  [type] $table  [description]
     * @param  [type] $fields [description]
     * @param  [type] $where  [description]
     * @param  [type] $params [description]
     * @return [type]         [true/false]
     */
    public function update($sql, $params = null)
    {
        try {
            $rs = $this->db->prepare($sql);
            $result = $rs->execute($params);
            return $result;
        } catch (Exception $e) {
            $this->report($e);
        }
    }

    /**
     * Выборка одной записи
     * @param  [type] $query  [description]
     * @param  [type] $params [description]
     * @return [type]         [description]
     */
    public function queryValue($query, $params = null)
    {
        try {
            $result = null;
            $stmt = $this->db->prepare($query);
            if ($stmt->execute($params)) {
                $result = $stmt->fetchColumn();
                $stmt->closeCursor();
            }
            return $result;
        } catch (Exception $e) {
            $this->report($e);
        }
    }

    /**
     * Выборка нескольких записей
     * @param  [type] $query  [description]
     * @param  [type] $params [description]
     * @return [type]         [description]
     */
    public function queryValues($query, $params = null)
    {
        try {
            $result = null;
            $stmt = $this->db->prepare($query);
            if ($stmt->execute($params)) {
                $result = array();
                while ($row = $stmt->fetch(\PDO::FETCH_NUM)) {
                    $result[] = $row[0];
                }
            }
            return $result;
        } catch (Exception $e) {
            $this->report($e);
        }
    }

    public function queryRow($query, $params = null, $fetchStyle = \PDO::FETCH_ASSOC, $classname = null)
    {
        $rows = $this->queryRowOrRows(true, $query, $params, $fetchStyle, $classname);
        return $rows;
    }

    public function queryRows($query, $params = null, $fetchStyle = \PDO::FETCH_ASSOC, $classname = null)
    {
        $rows = $this->queryRowOrRows(false, $query, $params, $fetchStyle, $classname);
        return $rows;
    }

    public function queryRowFile($filepath, $params = null, $fetchStyle = \PDO::FETCH_ASSOC, $classname = null)
    {
        if (file_exists($filepath)) {
            $query = file_get_contents($filepath);
            $rows = $this->queryRowOrRows(true, $query, $params, $fetchStyle, $classname);
            return $rows;
        } else {
            return "Файл не найден";
        }

    }

    public function queryRowsFile($filepath, $params = null, $fetchStyle = \PDO::FETCH_ASSOC, $classname = null)
    {
        if (file_exists($filepath)) {
            $query = file_get_contents($filepath);
            $rows = $this->queryRowOrRows(false, $query, $params, $fetchStyle, $classname);
            return $rows;
        } else {
	        return "Файл не найден";
        }
    }

    private function queryRowOrRows($singleRow, $query, $params = null, $fetchStyle = \PDO::FETCH_ASSOC, $classname = null)
    {
        try {
            $result = null;
            $stmt = $this->db->prepare($query);
            if ($classname) {
                $stmt->setFetchMode($fetchStyle, $classname);
            } else {
                $stmt->setFetchMode($fetchStyle);
            }
            if ($stmt->execute($params)) {
                $result = $singleRow ? $stmt->fetch() : $stmt->fetchAll();
                $stmt->closeCursor();
            }
            return $result;
        } catch (Exception $e) {
            $this->report($e);
        }
    }

    public function quote($str)
    {
        return $this->db->quote($str);
    }

    public function quoteArray($arr)
    {
        $result = array();
        foreach ($arr as $val) {
            $result[] = $this->db->quote($val);
        }
        return $result;
    }

    public function quoteImplodeArray($arr)
    {
        return implode(',', $this->quoteArray($arr));
    }

    // returns true/false
    public function sql($query, $params = null)
    {
        try {
            $result = null;
            $stmt = $this->db->prepare($query);
            return $stmt->execute($params);
        } catch (Exception $e) {
            $this->report($e);
        }
    }

    private function report($e)
    {
        throw $e;
    }

    public function returnDBh()
    {
        return $this->db;
    }

    public function query($sql, $params = [])
    {
        $sth = $this->db->prepare($sql);
        $sth->execute($params);
        return $sth->fetchAll(\PDO::FETCH_OBJ);
    }

    public function getLengthColumn($table)
    {
        $select = $this->db->query("SHOW COLUMNS FROM `$table`");
        foreach ($select as $key => $value) {
            $arrLength[$value['Field']] = preg_replace('~[^0-9]+~', '', $value['Type']);
        }
        return $arrLength;
    }
}