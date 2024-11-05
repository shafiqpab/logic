<?
//error_reporting(E_ALL | E_STRICT);
//ini_set('error_reporting', E_ALL);
//ini_set('display_errors',1);
//ini_set('log_errors', 1); 
//ini_set('error_log', dirname(__FILE__) . '/error_log.txt'); // change as required
//require_once('common.php');

class report{
	protected $_By_Job='By_Job';
	protected $_By_Order='By_Order';
	
	protected $_By_OrderAndCountry='By_OrderAndCountry';
	protected $_By_OrderAndGmtsitem='By_OrderAndGmtsitem';
	protected $_By_OrderAndGmtscolor='By_OrderAndGmtscolor';
	protected $_By_OrderAndGmtssize='By_OrderAndGmtssize';
	
	protected $_By_orderCountryAndGmtsitem='By_orderCountryAndGmtsitem';
	protected $_By_OrderCountryAndGmtscolor='By_OrderCountryAndGmtscolor';
	protected $_By_OrderCountryAndGmtssize='By_OrderCountryAndGmtssize';
	protected $_By_OrderGmtsitemAndGmtscolor='By_OrderGmtsitemAndGmtscolor';
	protected $_By_OrderGmtsitemAndGmtssize='By_OrderGmtsitemAndGmtssize';
	protected $_By_OrderGmtscolorAndGmtssize='By_OrderGmtscolorAndGmtssize';
	
	protected $_By_orderCountryGmtsitemAndGmtscolor='By_orderCountryGmtsitemAndGmtscolor';
	protected $_By_orderCountryGmtsitemAndGmtssize='By_orderCountryGmtsitemAndGmtssize';
	protected $_By_orderCountryGmtscolorAndGmtssize='By_orderCountryGmtscolorAndGmtssize';
	protected $_By_orderGmtsitemGmtscolorAndGmtssize='By_orderGmtsitemGmtscolorAndGmtssize';
	
	protected $_By_orderCountryGmtsitemGmtscolorAndGmtssize='By_orderCountryGmtsitemGmtscolorAndGmtssize';
	
	protected $_jobs=array();
	protected $_poIds=array();
	protected $_gmtsitemRatioArray=array();
	protected $_costingPerQtyArr=array();
	
	// class constructor
	function __construct($jobs,$type){
		if($type=='job'){
			$this->_setJobs($jobs);
			if(count($this->_jobs)<=0){
				echo "Pass valid job\jobs";
				die;
			}
		}
		if($type=='po'){
			$this->_setPoIds($jobs);
			if(count($this->_poIds)<=0){
				echo "Pass valid po\pos";
				die;
			}else{
				$this->_setJobForPo($this->_poIds);
			}
		}
		
		$this->_setGmtsitemRatioArr();
		$this->_setCostingPerArr();
	}// end class constructor
	
	private function _setJobForPo($poIds){
		$pocond=$this->_setPoIdsString($poIds, 'id');
		$jobForPo=return_library_array( "select id,job_no_mst from wo_po_break_down where  1=1 ".$pocond."", "id", "job_no_mst",'');
		$pocond=$this->_setJobs($jobForPo);
	}
	
	private function _setJobs($jobs){
		if(is_array($jobs)){
			$jobs=array_unique($jobs);
			foreach($jobs as $jobkey=> $jobvalue){
				if($this->_isValidJob($jobvalue)){
				$this->_jobs[]=$jobvalue;
				}
			}
		}else{
			$this->_setJobs(explode(",",$jobs));
		}
	}
	private function _isValidJob($job){
		$pattern = "/^([a-zA-Z])+\-[0-9]+\-[0-9]{5}/i";
		$job=str_replace(array("'", "\""),"",$job);
		if (preg_match($pattern,$job)){ 
			return true;
		}else{
			return false;
		}
	}
	
	public function getJobs(){
		return implode(",",$this->_jobs);
	}
	
	private function _setPoIds($poIds){
		if(is_array($poIds)){
			$poIds=array_unique($poIds);
			foreach($poIds as $poIdskey=> $poIdsvalue){
				if($this->_isValidPoIds($poIdsvalue)){
				$this->_poIds[]=$poIdsvalue;
				}
			}
		}else{
			$this->_setPoIds(explode(",",$poIds));
		}
	}
	
	private function _isValidPoIds($poIds){
		if ((is_int($poIds) || ctype_digit($poIds)) && (int)$poIds > 0){ 
			return true;
		}else{
			return false;
		}
	}
	
	public function getPoIds(){
		return implode(",",$this->_poIds);
	}

	protected  function _setJobsString($jobs, $field){
		$jobs=array_chunk($jobs,1000, true);
		$jobcond='';
		$i=0;
		foreach($jobs as $jobkey=> $jobvalue){
			$jobsString='';
			foreach($jobvalue as $job){
				$job=str_replace("'","",$job);
				$jobsString.="'".$job."',";
			}
			
			if($i==0){
				$jobcond=" and $field in(".rtrim($jobsString,',').")";
			}else{
				$jobcond.=" or $field in(".rtrim($jobsString,',').")";
			}
			$i++;
		}
		return $jobcond;
	}
	
	protected function _setPoIdsString($poIds, $field){
		$poIds=array_chunk($poIds,1000, true);
		$poIdscond='';
		$i=0;
		foreach($poIds as $poIdskey=> $poIdsvalue){
			$poIdssString='';
			foreach($poIdsvalue as $poId){
				$poId=str_replace("'","",$poId);
				$poIdssString.=$poId.",";
			}
			
			if($i==0){
				$poIdscond=" and $field in(".rtrim($poIdssString,',').")";
			}else{
				$poIdscond.=" or $field in(".rtrim($poIdssString,',').")";
			}
			$i++;
		}
		return $poIdscond;
	}
	
	private  function _setGmtsitemRatioArr(){
		$jobcond=$this->_setJobsString($this->_jobs,'job_no');
		$gmtsitemRatioArray=array();
		$gmtsitemRatioSql=sql_select("select job_no,gmts_item_id,set_item_ratio from wo_po_details_mas_set_details where 1=1 ".$jobcond."",'','');
		foreach($gmtsitemRatioSql as $gmtsitemRatioSqlRow)
		{
		$gmtsitemRatioArray[$gmtsitemRatioSqlRow[csf('job_no')]][$gmtsitemRatioSqlRow[csf('gmts_item_id')]]=$gmtsitemRatioSqlRow[csf('set_item_ratio')];	
		}
		$this->_gmtsitemRatioArray=$gmtsitemRatioArray; 
	}
	
	public  function getGmtsitemRatioArr(){
		return $this->_gmtsitemRatioArray;
	}
	
	private function _setCostingPerArr(){
		  $jobcond=$this->_setJobsString($this->_jobs,'job_no');
		  $costingPerArr=sql_select( "select job_no,costing_per from wo_pre_cost_mst where 1=1 ".$jobcond."", '', '');
		  foreach($costingPerArr as $costingPerArrRow)
		  {
			    $costingPer=$costingPerArrRow[costing_per];
				$costingPerQty=12;
				if($costingPer==1){
					$costingPerQty=12	;
				}
				elseif($costingPer==2){
					$costingPerQty=1;	
				}
				elseif($costingPer==3){
					$costingPerQty=24	;
				}
				elseif($costingPer==4){
					$costingPerQty=36	;
				}
				elseif($costingPer==5){
					$costingPerQty=48;
				}
				else{
					$costingPerQty=12;
				}
			   $this->_costingPerQtyArr[$costingPerArrRow[csf('job_no')]]=$costingPerQty;	
		  }
		  //$this->_costingPerArr=$costingPerArr;
	}
	
	public function getCostingPerQtyArr(){
		return $this->_costingPerQtyArr;
	}
	public function _costingPerQty($JobNo){
		return $this->_costingPerQtyArr[$JobNo];
	}
	
	function __destruct() {
		unset($this->_jobs);
		unset($this->_poIds);
		unset($this->_gmtsitemRatioArray);
		unset($this->_costingPerArr);
	}
}
?>
