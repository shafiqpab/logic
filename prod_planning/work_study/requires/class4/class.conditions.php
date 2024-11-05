<?
class condition{
	 protected  $company_name_cond = '';
	 protected  $buyer_name_cond = '';
	 protected  $job_no_prefix_num_cond = '';
	 protected  $job_no_cond = '';
	 protected  $job_year_cond = '';
	 protected  $style_ref_no_cond = '';
	 protected  $location_name_cond = '';
	 protected  $region_cond = '';
	 protected  $team_leader_cond = '';
	 protected  $dealing_marchant_cond = '';
	 protected  $agent_name_cond = '';
	 protected  $season_cond = '';
	 protected  $factory_marchant_cond = '';
	 protected  $approvel_his_cond = '';
	 protected  $approval_from_cond = '';
	 
	 protected $po_id_cond='';
	 protected $po_id_cond_in='';
	 protected $po_number_cond='';
	 protected $pub_shipment_date_cond='';
	 protected $pack_handover_date_cond='';
	 protected $po_received_date_cond='';
	 protected $shipment_date_cond='';
	 protected $pp_meeting_date_cond='';
	 protected $factory_received_date_cond='';
	 protected $insert_date_cond='';
	 protected $job_insert_date_cond='';
	 protected $is_confirmed_cond='';
	 protected $grouping_cond='';
	 protected $file_no_cond='';
	 protected $po_shiping_status_cond='';
	 
	 protected $po_color_size_id_cond='';
	 protected $item_number_id_cond='';
	 protected $country_id_cond='';
	 protected $cutup_date_cond='';
	 protected $cutup_cond='';
	 protected $country_ship_date_cond='';
	 protected $size_number_id_cond='';
	 protected $color_number_id_cond='';
	 protected $color_size_shiping_status_cond='';
	 protected $country_type_cond='';
	 protected $file_year_cond='';
	 
	 
	 protected  $cond="";
	 
	 protected $_gmtsitemRatioArray=array();
	 protected $_costingPerQtyArr=array();
	 protected $_costingPerQtyArrById=array();
	 function __construct(){
		 return $this;
	 }
	 
	 public function company_name($value){
		 $this->company_name_cond=' and a.company_name '.$value;
		 $this->cond.=$this->company_name_cond;
		 return $this;
	 }
	 public function buyer_name($value){
		 $this->buyer_name_cond=' and a.buyer_name '.$value;
		 $this->cond.=$this->buyer_name_cond;
		 return $this;
	 }
	 public function job_no_prefix_num($value){
		 $this->job_no_prefix_num_cond=' and a.job_no_prefix_num '.$value;
		 $this->cond.=$this->job_no_prefix_num_cond;
		 return $this;
	 }
	 public function job_no($value){
		 $this->job_no_cond=' and a.job_no '.$value;
		 $this->cond.=$this->job_no_cond;
		 return $this;
	 }
	 
	 public function jobid_in($value){
		$jobIds=chop($value,','); $jobcond_in="";$app_his_jobcond_in="";
		$job_ids=count(array_unique(explode(",",$value)));
		if($job_ids>1000)
		{
			$jobcond_in=" and (";
			$app_his_jobcond_in=" and (";
			$jobIdsArr=array_chunk(explode(",",$jobIds),999);
			foreach($jobIdsArr as $ids)
			{
				$ids=implode(",",$ids);
				$jobcond_in.=" a.id in($ids) or"; 
				$app_his_jobcond_in.=" a.job_id in($ids) or"; 
			}
			$jobcond_in=chop($jobcond_in,'or ');
			$jobcond_in.=")";
			$app_his_jobcond_in=chop($app_his_jobcond_in,'or ');
			$app_his_jobcond_in.=")";
		}
		else { 
			$jobIds=implode(",",array_unique(explode(",",$jobIds)));
			$jobcond_in=" and a.id in($jobIds)";
			$app_his_jobcond_in=" and a.job_id in($jobIds)";
	      }
		
		  if($this->approvel_his_cond)
		  {
			$this->app_his_jobid_cond_in=$app_his_jobcond_in;
			$this->cond.=$this->app_his_jobid_cond_in;
		  }
		  else{
			$this->jobid_cond_in=$jobcond_in;
			$this->cond.=$this->jobid_cond_in;
		  }
		

		// $this->jobid_cond_in=$jobcond_in;
		// $this->cond.=$this->jobid_cond_in;
		// $this->app_his_jobid_cond_in=$app_his_jobcond_in;
		// $this->cond.=$this->app_his_jobid_cond_in;
		
		return $this;
	}
	 
	 public function job_year($value){
		 $this->job_year_cond=$value;
		 $this->cond.=$this->job_year_cond;
		 return $this;
	 }
	 public function style_ref_no($value){
		 $this->style_ref_no_cond=' and a.style_ref_no '.$value;
		 $this->cond.=$this->style_ref_no_cond;
		 return $this;
	 }
	 public function location_name($value){
		 $this->location_name_cond=' and a.location_name '.$value;
		 $this->cond.=$this->location_name_cond;
		 return $this;
	 }
	 public function region($value){
		 $this->region_cond=' and a.region '.$value;
		 $this->cond.=$this->region_cond;
		 return $this;
	 }
	 public function team_leader($value){
		 $this->team_leader_cond=' and a.team_leader '.$value;
		 $this->cond.=$this->team_leader_cond;
		 return $this;
	 }
	 public function dealing_marchant($value){
		 $this->dealing_marchant_cond=' and a.dealing_marchant '.$value;
		 $this->cond.=$this->dealing_marchant_cond;
		 return $this;
	 }
	 public function agent_name($value){
		 $this->agent_name_cond=' and a.agent_name '.$value;
		 $this->cond.=$this->agent_name_cond;
		 return $this;
	 }
	 public function season($value){
		 $this->season_cond=' and a.season_buyer_wise '.$value;
		 $this->cond.=$this->season_cond;
		 return $this;
	 }
	 public function factory_marchant($value){
		 $this->factory_marchant_cond=' and a.factory_marchant '.$value;
		 $this->cond.=$this->factory_marchant_cond;
		 return $this;
	 }
	 public function approved_no($value){ //For approavl histry version wise//Must be call it
		$this->approvel_his_cond=' and a.approved_no '.$value;
		$this->cond.=$this->approvel_his_cond;
		return $this;
	}
	public function approval_from($value){
		$this->approval_from_cond=' and a.approval_page '.$value;
		$this->cond.=$this->approval_from_cond;
		return $this;
	}
	
	 
	 public function po_id($value){
		 $this->po_id_cond=' and b.id '.$value;
		 $this->cond.=$this->po_id_cond;
		 return $this;
	 }
	  public function po_id_in($value){
			$poIds=chop($value,','); $po_cond_in="";$his_po_cond_in="";
			$po_ids=count(array_unique(explode(",",$value)));
			$poIds_str=implode(",",array_unique(explode(",",$value)));
			$poIds=chop($poIds_str,',');
			if($po_ids>1000)
			{
				$po_cond_in=" and (";
				$his_po_cond_in=" and (";
				$poIdsArr=array_chunk(explode(",",$poIds),999);
				foreach($poIdsArr as $ids)
				{
					$ids=implode(",",$ids);
					$po_cond_in.=" b.id in($ids) or"; 
					$his_po_cond_in.=" b.po_id in($ids) or"; 
				}
				$po_cond_in=chop($po_cond_in,'or ');
				$po_cond_in.=")";
				$his_po_cond_in=chop($his_po_cond_in,'or ');
				$his_po_cond_in.=")";
			}
			else
			{
				$po_cond_in=" and b.id in($poIds)";
				$his_po_cond_in=" and b.po_id in($poIds)";
			}
		 
			if($this->approvel_his_cond) //For approavl histry version wise
			{
				$this->app_his_po_id_cond_in=$his_po_cond_in;
				$this->cond.=$this->app_his_po_id_cond_in;
			}
			else{
				$this->po_id_cond_in=$po_cond_in;
				$this->cond.=$this->po_id_cond_in;
			}
			 	// $this->po_id_cond_in=$po_cond_in;
				// $this->cond.=$this->po_id_cond_in;
				// $this->app_his_po_id_cond_in=$his_po_cond_in;
				// $this->cond.=$this->app_his_po_id_cond_in;

		 return $this;
	 }
	 public function po_number($value){
		 $this->po_number_cond=' and b.po_number '.$value;
		 $this->cond.=$this->po_number_cond;
		 return $this;
	 }
	  public function file_year($value){
		 $this->file_year_cond=' and b.file_year '.$value;
		 $this->cond.=$this->file_year_cond;
		 return $this;
	 }
	 public function pub_shipment_date($value){
		 $this->pub_shipment_date_cond=' and b.pub_shipment_date '.$value;
		 $this->cond.=$this->pub_shipment_date_cond;
		 return $this;
	 }
	  public function pack_handover_date($value){
		 $this->pack_handover_date_cond=' and b.pack_handover_date '.$value;
		 $this->cond.=$this->pack_handover_date_cond;
		 return $this;
	 }
	 public function po_received_date($value){
		 $this->po_received_date_cond=' and b.po_received_date '.$value;
		 $this->cond.=$this->po_received_date_cond;
		 return $this;
	 }
	 public function shipment_date($value){
		 $this->shipment_date_cond=' and b.shipment_date '.$value;
		 $this->cond.=$this->shipment_date_cond;
		 return $this;
	 }
	 public function pp_meeting_date($value){
		 $this->pp_meeting_date_cond=' and b.pp_meeting_date '.$value;
		 $this->cond.=$this->pp_meeting_date_cond;
		 return $this;
	 }
	 public function factory_received_date($value){
		 $this->factory_received_date_cond=' and b.factory_received_date '.$value;
		 $this->cond.=$this->factory_received_date_cond;
		 return $this;
	 }
	  public function insert_date($value){
		 $this->insert_date_cond=' and b.insert_date '.$value;
		 $this->cond.=$this->insert_date_cond;
		 return $this;
	 }
	  public function job_insert_date($value){
		 $this->job_insert_date_cond=' and a.insert_date '.$value;
		 $this->cond.=$this->job_insert_date_cond;
		 return $this;
	 }
	 
	 public function is_confirmed($value){
		 $this->is_confirmed_cond=' and b.is_confirmed '.$value;
		 $this->cond.=$this->is_confirmed_cond;
		 return $this;
	 }
	 public function grouping($value){
		 $this->grouping_cond=' and b.grouping '.$value;
		 $this->cond.=$this->grouping_cond;
		 return $this;
	 }
	 public function file_no($value){
		 $this->file_no_cond=' and b.file_no '.$value;
		 $this->cond.=$this->file_no_cond;
		 return $this;
	 }
	 public function po_shiping_status($value){
		 $this->po_shiping_status_cond=' and b.shiping_status '.$value;
		 $this->cond.=$this->po_shiping_status_cond;
		 return $this;
	 }
	 
	 public function po_color_size_id($value){
		 $this->po_color_size_id_cond=' and c.id '.$value;
		 $this->cond.=$this->po_color_size_id_cond;
		 return $this;
	 }
	 public function item_number_id($value){
		 $this->item_number_id_cond=' and c.item_number_id '.$value;
		 $this->cond.=$this->item_number_id_cond;
		 return $this;
	 }
	public function country_id($value){
		 $this->country_id_cond=' and c.country_id '.$value;
		 $this->cond.=$this->country_id_cond;
		 return $this;
	 }
	public function cutup_date($value){
		 $this->cutup_date_cond=' and c.cutup_date'.$value;
		 $this->cond.=$this->cutup_date_cond;
		 return $this;
	 }
	 public function cutup($value){
		 $this->cutup_cond=' and c.cutup '.$value;
		 $this->cond.=$this->cutup_cond;
		 return $this;
	 }
	 public function country_ship_date($value){
		 $this->country_ship_date_cond=' and c.country_ship_date '.$value;
		 $this->cond.=$this->country_ship_date_cond;
		 return $this;
	 }
	 public function size_number_id($value){
		 $this->size_number_id_cond=' and c.size_number_id '.$value;
		 $this->cond.=$this->size_number_id_cond;
		 return $this;
	 }
	 public function color_number_id($value){
		 $this->color_number_id_cond=' and c.color_number_id '.$value;
		 $this->cond.=$this->color_number_id_cond;
		 return $this;
	 }
	 public function color_size_shiping_status($value){
		 $this->color_size_shiping_status_cond=' and c.shiping_status '.$value;
		 $this->cond.=$this->color_size_shiping_status_cond;
		 return $this;
	 }
	 public function country_type($value){
		 $this->country_type_cond=' and c.country_type '.$value;
		 $this->cond.=$this->country_type_cond;
		 return $this;
	 }
	
	 public function getCond(){
		  return $this->cond;
	 }
	 public function getJobTableCond(){
		//app_his_jobid_cond_in
		// if($this->approvel_his_cond)
		// {
		// 	$jobid_his_cond=$app_his_jobid_cond_in;
		// 	$poid_his_cond=$app_his_po_id_cond_in;
		// 	//echo "B";
		// }
		// else{
		// 	$jobid_his_cond=$jobid_cond_in;
		// 	$poid_his_cond=$po_id_cond_in;
		// 	//echo "A";
		// }
		// if($this->approvel_his_cond)
		// {
		//  	return $this->company_name_cond.$this->buyer_name_cond.$this->job_no_prefix_num_cond.$this->job_no_cond.$this->job_year_cond.$this->style_ref_no_cond.$this->location_name_cond.$this->region_cond.$this->team_leader_cond.$this->dealing_marchant_cond.$this->agent_name_cond.$this->season_cond.$this->approvel_his_cond.$this->factory_marchant_cond.$this->approval_from_cond.$this->$app_his_jobid_cond_in.$this->$app_his_po_id_cond_in;
		// }
		// else
		// {
		// 	return $this->company_name_cond.$this->buyer_name_cond.$this->job_no_prefix_num_cond.$this->job_no_cond.$this->job_year_cond.$this->style_ref_no_cond.$this->location_name_cond.$this->region_cond.$this->team_leader_cond.$this->dealing_marchant_cond.$this->agent_name_cond.$this->season_cond.$this->approvel_his_cond.$this->factory_marchant_cond.$this->approval_from_cond.$this->$jobid_cond_in.$this->$po_id_cond_in;
		// }
		return $this->company_name_cond.$this->buyer_name_cond.$this->job_no_prefix_num_cond.$this->job_no_cond.$this->job_year_cond.$this->style_ref_no_cond.$this->location_name_cond.$this->region_cond.$this->team_leader_cond.$this->dealing_marchant_cond.$this->agent_name_cond.$this->season_cond.$this->approvel_his_cond.$this->factory_marchant_cond.$this->approval_from_cond;
		 
	 }
	 public function init(){
		 $this->_setGmtsitemRatioArr();
		 $this->_setCostingPerArr();
		 $this->_setTrimsTypeArr();
		 return $this;
	 }
	public  function _setGmtsitemRatioArr(){
		if($this->approvel_his_cond)
		{
			$gmtsitemRatioSql=sql_select('select a.job_no AS "job_no",b.gmts_item_id AS "gmts_item_id" ,b.set_item_ratio AS "set_item_ratio" from wo_po_dtls_mst_his a, wo_po_dtls_item_set_his b where 1=1 '.$this->getJobTableCond().' and a.approved_no=b.approved_no and a.job_id=b.job_id and a.approval_page=b.approval_page ','','');
		}
		else
		{
			$gmtsitemRatioSql=sql_select('select a.job_no AS "job_no",b.gmts_item_id AS "gmts_item_id" ,b.set_item_ratio AS "set_item_ratio" from wo_po_details_master a, wo_po_details_mas_set_details b where 1=1 '.$this->getJobTableCond().' and a.id=b.job_id','','');
		}
		foreach($gmtsitemRatioSql as $gmtsitemRatioSqlRow)
		{
			$this->_gmtsitemRatioArray[$gmtsitemRatioSqlRow['job_no']][$gmtsitemRatioSqlRow['gmts_item_id']]=$gmtsitemRatioSqlRow['set_item_ratio'];	
		}
		return $this;
	}
	
	private function _setCostingPerArr(){
		if($this->approvel_his_cond)
		{
			$costingPerArr=sql_select( 'select a.id as "job_id", a.job_no AS "job_no",b.costing_per AS "costing_per" from  wo_po_dtls_mst_his a, wo_pre_cost_mst_histry b where 1=1 '.$this->getJobTableCond().'  and a.approved_no=b.approved_no and a.job_id=b.job_id and a.approval_page=b.approval_page', '', '');
		}
		else
		{
			$costingPerArr=sql_select( 'select a.id as "job_id", a.job_no AS "job_no",b.costing_per AS "costing_per" from  wo_po_details_master a, wo_pre_cost_mst b where 1=1 '.$this->getJobTableCond().' and a.id=b.job_id', '', '');
		}
		foreach($costingPerArr as $costingPerArrRow)
		{
			$costingPer=$costingPerArrRow['costing_per'];
			$costingPerQty=12;
			if($costingPer==1) $costingPerQty=12;
			else if($costingPer==2) $costingPerQty=1;	
			else if($costingPer==3) $costingPerQty=24;
			else if($costingPer==4) $costingPerQty=36;
			else if($costingPer==5) $costingPerQty=48;
			else $costingPerQty=12;
			
			$this->_costingPerQtyArr[$costingPerArrRow['job_no']]=$costingPerQty;	
			$this->_costingPerQtyArrById[$costingPerArrRow['job_id']]=$costingPerQty;	
		}
	}
	public  function _setTrimsTypeArr(){
		$lib_trimSql=sql_select('select a.id AS "trim_id",a.trim_type AS "trim_type" from lib_item_group a where 1=1 and a.trim_type>0 and a.item_category=4 and a.status_active=1','','');
		//print_r($lib_trimSql);
		//echo 'select a.id  AS "trim_id",a.trim_type AS "trim_type" from lib_item_group a where 1=1 and a.trim_type>0 and a.item_category=4 and a.status_active=1';
		foreach($lib_trimSql as $row)
		{
			$this->_trimTypeArray[$row['trim_id']]=$row['trim_type'];	
		}
		return $this;
		//echo  $this;
	}
	
	public  function getCostingPerArr(){
		return $this->_costingPerQtyArr;
	}
	public  function getGmtsitemRatioArr(){
		return $this->_gmtsitemRatioArray;
	}
	public  function getTrimsTypeArr(){
		return $this->_trimTypeArray;
	}

	public  function getCostingPerArr_by_id(){
		return $this->_costingPerQtyArrById;
	}
}
?>