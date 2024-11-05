<?

class job extends report{
	
	private $_query="";
	private $_dataArray=array();
	
	function __construct(condition $condition){
		parent::__construct($condition);
		$this->_setQuery();
		$this->_setData();
	}// end class constructor
	
	private function _setQuery(){
		$this->_query='select job.*, job.id AS "id" from wo_po_details_master job where 1=1 '.$this->cond.'   and job.is_deleted=0 and job.status_active=1';//order by b.id,d.id
	}
	
	public function getQuery(){
		return $this->_query;
	}
	
	private function _setData() {
		$this->_dataArray=pdo_select($this->_query,'JOB_NO','');
		return $this;
	}
	
	public function getData() {
		return $this->_dataArray;
	}
	public function getJobs() {
		return $this->_dataArray;
	}
}
?>