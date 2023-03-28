<?php

require_once 'DatabaseInterface.php';

class Database implements DatabaseInterface
    {
        protected string | float $host_server ,$db_name , $username , $password , $where_in ="" , $limit,
          $table , $where = " true " , $select = " * " , $join = "";
        protected PDO $conn ;
        protected static ?Database $instance = null;

        const INNER_JOIN = "INNER JOIN";
        const RIGHT_JOIN = "RIGHT JOIN";
        const LEFT_JOIN = "LEFT JOIN";
        private function __construct(array $config = [])
        {
            $this->host_server =  $config['host_server'];
            $this->db_name =  $config['db_name'];
            $this->username =  $config['username'];
            $this->password =  $config['password'];
            $this->connect();
        }

    /**
     * @param array $config
     * @return static
     */
        public static function getInstance(array $config = []):static
        {
            if (empty(self::$instance))
            {
                self::$instance = new Database($config);
            }
            return self::$instance;
        }

    /**
     * @return string
     */
        private function connect():string
        {
            try {
                $this->conn = new PDO("mysql:host=$this->host_server;
                                    dbname=$this->db_name",
                $this->username, $this->password);
                $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
                return "Connected successfully";
            } catch(PDOException $e) {
                return "Connection failed: " . $e->getMessage();
            }
        }

        /**
         * @param string $tableName
         * @return $this
         */
        public function table(string $tableName):static
        {
            $this->table = $tableName;
            return $this;
        }

        /**
         * @param string $query
         * @param array $data
         * @return bool|string
         */

        private function query(string $query , array $data): bool|string
        {
            try {
                $stmt = $this->conn->prepare($query);
                return $stmt->execute($data);
            }catch (PDOException $exception)
            {
               throw new PDOException($exception->getMessage());
            }
        }

    /**
     * @param array $data
     * @return bool|string
     */
        public function create(array $data): bool|string
        {
            if (count($data) > 0)
            {
                $keyData = array_keys($data);
                $implodeStrKeyData =  implode("," , $keyData);
                $implodeParamValueRequest = implode(", :",$keyData);
                $queryStr = "INSERT INTO $this->table ({$implodeStrKeyData}) VALUES (:{$implodeParamValueRequest})";
                 $this->query($queryStr , $data);
                 return $this->conn->lastInsertId();
            }else{
                return "param create không phải là array hoặc array rỗng";
            }
        }

    /**
     * @param array $data
     * @return bool
     */
        public function update(array $data):bool
        {
            if (count($data) > 0)
            {
                $keyValue = array_keys($data);
                $paramStr = '';
                foreach ($keyValue as $value)
                {
                    $paramStr.=$value."=:".$value." , ";
                }
                $paramStr = trim($paramStr ," , ");
                $updateStr = "UPDATE {$this->table} SET {$paramStr} Where {$this->where} {$this->where_in}";
                return $this->query($updateStr , $data);
            }
            return false;

        }

    /**
     * @param string $field
     * @param string|null $comparison
     * @param float|string $value
     * @return $this
     */
        public function where(string $field , ?string $comparison ,float|string $value = 0):static
        {
            if (!empty($field) && !empty($comparison && !empty($value)))
            {
                $this->where.= " and ". $field. ' ' .$comparison . ' '."'" .$value."'";
            }else if (!empty($field) && !empty($comparison) && $value == 0)
            {
                $this->where.= " and ". $field." = '" .$comparison."'";
            }
            return $this;
        }

    /**
     * @param string $field
     * @param array $params
     * @return $this
     */
        public function whereIn(string $field ,array $params):static
        {
            $implodeParams = implode(",",$params);
            $this->where_in.= " and ".$field." IN (".$implodeParams.")";
            return $this;
        }

    /**
     * @param ...$params
     * @return $this
     */
         public function select(...$params): static
         {
            $field = '';
            if (isset($params[0]) && is_array($params[0]))
            {
                $field.=implode(",",$params[0]);
            }else
            {
                $field.=implode(",",$params);
            }
            $this->select = $field;
            return $this;
        }

    /**
     * @param string $query
     * @return bool|array
     */
        private function getData(string $query): bool|array
        {
            try {
                $stmt = $this->conn->query($query);
                return $stmt->fetchAll(PDO::FETCH_ASSOC);
            }catch (PDOException $exception)
            {
                throw new PDOException($exception->getMessage());
            }
        }

    /**
     * @return string
     */
        private function queryStr():string
        {
            $this->limit = !empty($this->limit) ? " LIMIT ".$this->limit:"";
            $query = "SELECT {$this->select} FROM {$this->table} {$this->join} 
                      WHERE {$this->where}{$this->where_in}{$this->limit}";
            return $query;
        }

        public function get():array
        {
            $query = $this->queryStr();
            return $this->getData($query);
        }

    /**
     * @return array
     */
        public function first():array
        {
            $query = $this->queryStr();
            $data = $this->getData($query);
            if (count($data) > 0)
            {
                foreach ($data as $value)
                {
                    return $value;
                }
            }
            return [];
        }

    /**
     * @param float $countRow
     * @return $this
     */
        public function limit(float $countRow):static
        {
            $this->limit = $countRow;
            return $this;
        }

    /**
     * @return bool
     */
        public function delete(): bool
        {
            $query = "DELETE FROM {$this->table} WHERE {$this->where}{$this->where_in}";
            $stmt = $this->conn->prepare($query);
            try {
                return $stmt->execute();
            }catch (PDOException $exception)
            {
                throw new PDOException($exception->getMessage());
            }
        }

        private function baseJoin(string $tableJoin, string $primaryTableBoard,
                                  string $comparison, string $foreignTableB ,string $typeJoin):string
        {
            return " $typeJoin ".$tableJoin." ON ".$primaryTableBoard." ".$comparison." ".$foreignTableB;
        }
        public function join(string $tableJoin, string $primaryTableBoard,
                             string $comparison, string $foreignTableB): static
        {
            $queryJoin = $this->baseJoin($tableJoin , $primaryTableBoard , $comparison , $foreignTableB ,self::INNER_JOIN);
            $this->join.=$queryJoin;
            return $this;
        }

    public function leftJoin(string $tableJoin, string $primaryTableBoard,
                         string $comparison, string $foreignTableB): static
    {
        $queryJoin = $this->baseJoin($tableJoin , $primaryTableBoard , $comparison , $foreignTableB ,self::LEFT_JOIN);
        $this->join.=$queryJoin;
        return $this;
    }
    public function rightJoin(string $tableJoin, string $primaryTableBoard,
                             string $comparison, string $foreignTableB): static
    {
        $queryJoin = $this->baseJoin($tableJoin , $primaryTableBoard , $comparison , $foreignTableB ,self::RIGHT_JOIN);
        $this->join.=$queryJoin;
        return $this;
    }
}