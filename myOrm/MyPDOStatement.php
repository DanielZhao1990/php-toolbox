<?php
/**
 * Created by PhpStorm.
 * User: daniel
 * Date: 2015/11/26
 * Time: 15:17
 */
namespace toolbox\myOrm;
class MyPDOStatement extends \PDOStatement
{

    protected $_debugValues = null;
    protected $lastSQL = null;
    protected function __construct()
    {
        // need this empty construct()!
    }

    public function execute($values = array())
    {
        $this->_debugValues = $values;
        if (SHOW_SQL) {
            $this->lastSQL=$this->_debugQuery();
            echo $this->lastSQL;
        }
        try {
            $t = parent::execute($values);
            // maybe do some logging here?
        } catch (\PDOException $e) {
            // maybe do some logging here?
            throw $e;
        }

        return $t;
    }

    public function _debugQuery($replaced = true)
    {
        $q = $this->queryString;
        if (!$replaced) {
            return $q;
        }
        return preg_replace_callback('/:([0-9a-z_]+)/i', array($this, '_debugReplace'), $q);
    }

    public function getLastQuery()
    {
        return  $this->lastSQL;
    }

    protected function _debugReplace($m)
    {
        $v = $this->_debugValues[$m[1]];
        if ($v === null) {
            return "NULL";
        }
        if (!is_numeric($v)) {
            $v = str_replace("'", "''", $v);
        }

        return "'" . $v . "'";
    }
}
