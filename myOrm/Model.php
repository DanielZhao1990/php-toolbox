<?php
/**
 * 基本的Mysql连接工具
 * User: daniel
 * Date: 2015/7/21
 * Time: 10:05
 */
namespace toolbox\myOrm;
// $model = new Model("testtable");
// $data['b'] = '测试开始';
// $data['c'] = 1.0;
// $model->add($data);
// // $data['b'] = '测试';
// echo $model->update($data) . "\n";

// var_dump($model->find($data['a']));

// $datas = $model->select();
// var_dump($datas);

class Model
{
    private $tableName;
    private $driver;
    private $columns;
    public $pk;
    // private $priKeyName;
    private $showLog = true;

    const TABLE_COLUMN_Field = "Field";
    const TABLE_COLUMN_Type = "Type";
    const TABLE_COLUMN_Null = "Null";
    const TABLE_COLUMN_Key = "Key";
    const TABLE_COLUMN_Key_VALUE_PRIVATE = "PRI";
    const TABLE_COLUMN_Extra = "Extra";

    public function __construct($tableName = "")
    {
        $this->tableName = $tableName;
        $this->driver = DB::getDriver();
        $res = $this->driver->query("show columns from $tableName");
        foreach ($res as $key => $value) {
            $columnName = $value[Model::TABLE_COLUMN_Field];
            if ($value[Model::TABLE_COLUMN_Key] == Model::TABLE_COLUMN_Key_VALUE_PRIVATE) {//设置主键
                $this->pk = $columnName;
            }
            $value[Model::TABLE_COLUMN_Null] = $value[Model::TABLE_COLUMN_Null] == "NO" ? false : true;
            $this->columns[$columnName] = $value;
        }
        //定制结果集(表名things)

    }

    public function add(&$data)
    {
        //定制结果集(表名things)
        $value_map = $this->getValueMap($data);
        $sql = "INSERT INTO $this->tableName ";
        $pdo = $this->driver->getPDO();
        $keys = "(";
        $values = "(";
        if (count($value_map) > 0) {
            foreach ($value_map as $key => $value) {
                $keys .= "$key,";
                $values .= ":$key,";
            }
            $keys = substr($keys, 0, strlen($keys) - 1);
            $values = substr($values, 0, strlen($values) - 1);
            $keys .= ")";
            $values .= ")";
        }
        $sql = $sql . $keys . " VALUES " . $values;
        $sth = $pdo->prepare($sql);
        $sth->execute($value_map);
        $id = $pdo->lastInsertId();
        $this->checkError();
        if ($id > 0) {
            $data[$this->pk] = $id;
        }
        return $id;
    }

    public function showLog($data)
    {
        if ($this->showLog) {
            echo $data;
            echo "\n";
        }
    }

    /**
     * 查找所有的item，select * from....
     * @return [type] [description]
     */
    public function select()
    {
        $sql = "select * from $this->tableName";
        $res = $this->driver->query($sql);
        $this->checkError();
        return $res;
    }

    public function find($id)
    {
        $sql = "select * from $this->tableName where $this->pk = '$id'";
        $res = $this->driver->query($sql);
        $this->checkError();
        if ($res&&count($res)>0) {
            return $res[0];
        } else {
            return null;
        }
    }

    public function rawQuery($sql)
    {
        $res = mysql_query($sql);
        $this->checkError();
        $array = array();
        echo "SQL:$sql\n";
        if ($res) {
            while ($row = mysql_fetch_assoc($res)) {
                $array[] = $row;
            }
            return $array;
        } else {
            return null;
        }
    }

    public function update($data)
    {
        //定制结果集(表名things)
        $pk = $this->pk;
        if (isset($data[$pk])) {
            $value_map = $this->getValueMap($data);
            $sql = "UPDATE $this->tableName set ";
            $where = " where $pk = '$data[$pk]'";
            $updateSql = "";
            if (count($value_map) > 0) {
                foreach ($value_map as $fieldName => $fValue) {
                    if ($fieldName == $pk) {
                        continue;
                    }
                    $updateSql .= "$fieldName='$fValue',";
                }
                $updateSql = substr($updateSql, 0, strlen($updateSql) - 1);
            }
            $sql = $sql . $updateSql . $where;
            return ($this->driver->query($sql));
        }
    }

    public function saveOrUpdate($data)
    {
        //定制结果集(表名things)
        $pk = $this->pk;
        if (isset($data[$pk])) {
            $id = $data[$pk];
            $res = $this->find($id);
            if ($res) {
                $this->update($data);
            } else {
                $this->add($data);
            }

        } else {
            $this->add($data);
        }
    }

    /**
     * 清除掉非本表字段
     * @param $data
     * @param $value_map
     * @return mixed
     */
    public function getValueMap($data)
    {
        $value_map = array();
        foreach ($this->columns as $key => $value) {
            if (isset($data[$key])) {
                $value_map[$key] = $data[$key];
            }
        }
        return $value_map;
    }

    public function checkError()
    {
//		if (mysql_errno() > 0) {
//			echo "MYSQL_ERROR --- " . mysql_error() . "\n";
//		}
    }

}
