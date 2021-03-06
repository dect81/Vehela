<?php

    abstract Class ActiveRecord{

        protected $_dbObj;
        protected $tableName;

        public function __construct(){}

        public function save(){

            if(!$this->IsItNewRecord()){

                $Record = $this->getById($this->id);

                $class_vars = get_class_vars(get_class($this));

                foreach($class_vars as $key => $variable){
                    if($key<>'tableName' and $key<>'_dbObj')
                        $Record[$key]=$this->$key;
                }

                $query = "UPDATE {$this->tableName} SET ";

                foreach($Record as $key => $value){
                    if($key<>'id')
                        $query .= "{$key} = '{$value}', ";
                }

                $query = substr($query,0,strlen($query)-2);
                $query .= "where id = {$this->id}";

            }

            else {

                foreach($this as $key => $value){
                    if($key<>'tableName' and $key<>'_dbObj')
                        $Record[$key]=$this->$key;
                }

                $query = "INSERT INTO {$this->tableName} (";
                $columns = null;
                $values = null;

                unset($Record['id']);

                foreach($Record as $column => $value){
                    if($key<>'id'){
                        $columns .= "{$column}, ";
                        $values .= "'{$value}', ";
                    }

                }

                $query .= $columns;
                $query = substr($query,0,strlen($query)-2);
                $query .= ') VALUES (';

                $query .= $values;
                $query = substr($query,0,strlen($query)-2);
                $query .= ')';


            }

            $this->_dbObj->query($query);

        }

        public function deleteById($id){
            if(Registry::get('QuickPass'))
                Registry::get('DBQueue')->add(array(
                    'table'=>$this->tableName,
                    'function'=>'deleteById',
                    'query'=>"DELETE FROM {$this->tableName} where id = $id"
                ));
            /*
            $this->_dbObj->query("DELETE from {$this->tableName} where id = $id");*/
        }

        public function getById($id){

            if(Registry::get('QuickPass')) {
                Registry::get('DBQueue')->add(array(
                    'table'=>$this->tableName,
                    'function'=>'getById',
                    'query'=>"SELECT * FROM {$this->tableName} where id = $id"
                ));
                return 1;
            }
            else {
                $DBQueue = Registry::get('DBQueue');
                return $DBQueue->results[$this->tableName]['getById'][$id];
            }

        }

        public function getAll(){

            if(Registry::get('QuickPass'))
                Registry::get('DBQueue')->add(array(
                    'table'=>$this->tableName,
                    'function'=>'getAll',
                    'query'=>"SELECT * FROM {$this->tableName}"
                ));

            /*
             *
             *
                $Records = $this->_dbObj->query("SELECT * FROM {$this->tableName}");
                $Records = $Records->fetchAll(PDO::FETCH_CLASS);
                return $Records;

            */
        }

        private function IsItNewRecord(){

            if(!empty($this->id))
                $Record = $this->_dbObj->query("SELECT count(*) FROM {$this->tableName} where id = {$this->id}");
            else
                return 1;

            $Record = $Record->fetch(PDO::FETCH_ASSOC);
            return !$Record['count(*)'];

        }


    }



?>