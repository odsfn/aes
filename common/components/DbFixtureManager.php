<?php
Yii::import('system.test.CDbFixtureManager');
/**
 * Adds functionality to extend common fixtures by test-scope fixtures. 
 * 
 * For example: 
 * 
 * $fixtures = array(
 *  'fixtureA' => 'FixtureAModel',
 *  'fixtureB' => ':fixture_b_table',
 *  'fixtureC' => array(':fixture_c_table', 'fixture_part1', 'path2/to/fixture_part2'[, 'path2/to/fixture_part3']) 
 * );
 * @author Vasiliy Pedak <truvazia@gmail.com>
 */
class DbFixtureManager extends CDbFixtureManager {
    
    protected $_schema;
    protected $_db;
    protected $_fixtures;
    protected $_rows;                     // fixture name, row alias => row
    protected $_records;			// fixture name, row alias => record (or class name)
    
    
    protected function getSchema() 
    {
        if(!$this->_schema)
            $this->_schema = $this->getDbConnection()->getSchema();
        
        return $this->_schema;
    }

    protected function readFixturesFolder($folderPath) 
    {
        $schema=$this->getSchema();
        $folder=opendir($folderPath);
        $suffixLen=strlen($this->initScriptSuffix);
        while($file=readdir($folder))
        {
                if($file==='.' || $file==='..' || $file===$this->initScript)
                        continue;
                $path=$folderPath.DIRECTORY_SEPARATOR.$file;
                if(substr($file,-4)==='.php' && is_file($path) && substr($file,-$suffixLen)!==$this->initScriptSuffix)
                {
                        $tableName=substr($file,0,-4);
                        if($schema->getTable($tableName)!==null)
                        {
                            if(!isset($this->_fixtures[$tableName]))
                                $this->_fixtures[$tableName] = array();
                            
                            $this->_fixtures[$tableName][]=$path;
                        }
                }
                elseif(is_dir($path))
                {
                    $this->readFixturesFolder($path);
                }
        }
        closedir($folder);        
    }

    public function getFixtures()
    {
            if($this->_fixtures===null)
            {
                    $this->_fixtures=array();
                    $this->readFixturesFolder($this->basePath);
            }
            
            return $this->_fixtures;
    }
        
    public function loadFixture($tableName, $withSubfolders = false, $rowsFrom = null)
    {
            $rows=array();
            $schema=$this->getDbConnection()->getSchema();
            $builder=$schema->getCommandBuilder();
            $table=$schema->getTable($tableName);

            foreach($this->readFixtureRows($tableName, $withSubfolders, $rowsFrom) as $alias=>$row)
            {
                $builder->createInsertCommand($table,$row)->execute();
                $primaryKey=$table->primaryKey;
                if($table->sequenceName!==null)
                {
                    if(is_string($primaryKey) && !isset($row[$primaryKey]))
                        $row[$primaryKey]=$builder->getLastInsertID($table);
                    elseif(is_array($primaryKey))
                    {
                        foreach($primaryKey as $pk)
                        {
                            if(!isset($row[$pk]))
                            {
                                $row[$pk]=$builder->getLastInsertID($table);
                                break;
                            }
                        }
                    }
                }
                $rows[$alias]=$row;
            }
            return $rows;
    }
    
    protected function readFixtureRows($tableName, $withSubfolders = false, $rowsFrom = null) 
    {
        $rows = array();
        
        foreach($this->fixtures[$tableName] as $fileName)
        {
            if(!is_file($fileName))
                continue;
            
            if(!$withSubfolders)
            {
                $relPath = str_replace($this->basePath . DIRECTORY_SEPARATOR, '', $fileName);
                if(strstr($relPath, DIRECTORY_SEPARATOR))
                    continue;
            }
            elseif(is_array($rowsFrom))
            {
                $searchPath = str_replace(array($this->basePath . DIRECTORY_SEPARATOR, '.php'), '', $fileName);
                if(!in_array($searchPath, $rowsFrom))
                    continue;
            }
            
            $rows = array_merge($rows, require($fileName));
        }
        
        return $rows;
    }
    
    public function load($fixtures)
    {
        $schema=$this->getDbConnection()->getSchema();
        $schema->checkIntegrity(false);

        $this->_rows=array();
        $this->_records=array();
        foreach($fixtures as $fixtureName=>$params)
        {
            $mergePath = null;
            $withSubfolders = false;
            
            if(is_array($params))
            {
                $tableName = array_shift($params);
                $mergePath = $params;
                $withSubfolders = true;
            }
            else
                $tableName = $params;
            
            if($tableName[0]===':')
            {
                $tableName=substr($tableName,1);
                unset($modelClass);
            }
            else
            {
                $modelClass=Yii::import($tableName,true);
                $tableName=CActiveRecord::model($modelClass)->tableName();
            }
            if(($prefix=$this->getDbConnection()->tablePrefix)!==null)
                $tableName=preg_replace('/{{(.*?)}}/',$prefix.'\1',$tableName);
            $this->resetTable($tableName);
            
            try{
                $rows=$this->loadFixture($tableName, $withSubfolders, $mergePath);
            }catch(Exception $e){
                throw new Exception('Loading of fixture "' . $tableName . '" failed. ' . print_r($mergePath, true), null, $e);
            }
            
            if(is_array($rows) && is_string($tableName))
            {
                $this->_rows[$fixtureName]=$rows;
                if(isset($modelClass))
                {
                    foreach(array_keys($rows) as $alias)
                        $this->_records[$fixtureName][$alias]=$modelClass;
                }
            }
        }

        $schema->checkIntegrity(true);
    }
    
    /**
     * Returns the fixture data rows.
     * The rows will have updated primary key values if the primary key is auto-incremental.
     * @param string $name the fixture name
     * @return array the fixture data rows. False is returned if there is no such fixture data.
     */
    public function getRows($name)
    {
            if(isset($this->_rows[$name]))
                    return $this->_rows[$name];
            else
                    return false;
    }

    /**
     * Returns the specified ActiveRecord instance in the fixture data.
     * @param string $name the fixture name
     * @param string $alias the alias for the fixture data row
     * @return CActiveRecord the ActiveRecord instance. False is returned if there is no such fixture row.
     */
    public function getRecord($name,$alias)
    {
            if(isset($this->_records[$name][$alias]))
            {
                    if(is_string($this->_records[$name][$alias]))
                    {
                            $row=$this->_rows[$name][$alias];
                            $model=CActiveRecord::model($this->_records[$name][$alias]);
                            $key=$model->getTableSchema()->primaryKey;
                            if(is_string($key))
                                    $pk=$row[$key];
                            else
                            {
                                    foreach($key as $k)
                                            $pk[$k]=$row[$k];
                            }
                            $this->_records[$name][$alias]=$model->findByPk($pk);
                    }
                    return $this->_records[$name][$alias];
            }
            else
                    return false;
    }    
}
