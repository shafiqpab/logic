<?php
	class OracleDatabase {
		
		private $connection;
		public function __construct() {
            
            try{
                $this->connection = oci_pconnect('PLATFORM_LEATHER', 'PLATFORM_LEATHER', '//182.160.107.70:6935/logicdb');
            }
            catch(Exception $ex)
            {
                $this->connection = null;
            }
		}

        public function sql_select($strQuery)
        {
            
            $result = oci_parse($this->connection, $strQuery);
            oci_execute($result,OCI_NO_AUTO_COMMIT);
            $rows = array();
             while($summ=oci_fetch_assoc($result))
             {
                $rows[] = $summ;
             }
            return $rows;
            die;
        }
		
	}
?>