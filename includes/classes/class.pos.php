<?

class po extends report{
	
	private $_query="";
	private $_dataArray=array();
	
	function __construct(condition $condition){
		parent::__construct($condition);
		$this->_setQuery();
		$this->_setData();
	}// end class constructor
	
	private function _setQuery(){
		$this->_query='select job.*, po.*,po.id AS "po_id" from wo_po_details_master job, wo_po_break_down po where job.job_no=po.job_no_mst '.$this->cond.'   and job.is_deleted=0 and job.status_active=1';//order by b.id,d.id
	}
	
	public function getQuery(){
		return $this->_query;
	}
	
	private function _setData() {
		$this->_dataArray=sql_select($this->_query,'','');
		return $this;
	}
	
	public function getData() {
		return $this->_dataArray;
	}
	public function getpos() {
		return $this->_dataArray;
	}
}
?>