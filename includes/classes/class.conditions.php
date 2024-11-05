<?
class condition{
	 protected  $company_name_cond = '';
	 protected  $buyer_name_cond = '';
	 protected  $job_no_prefix_num_cond = '';
	 protected  $job_no_cond = '';
	 protected  $style_ref_no_cond = '';
	 protected  $location_name_cond = '';
	 protected  $region_cond = '';
	 protected  $team_leader_cond = '';
	 protected  $dealing_marchant_cond = '';
	 protected  $agent_name_cond = '';
	 protected  $season_cond = '';
	 protected  $factory_marchant_cond = '';
	 
	 protected $po_id_cond='';
	 protected $po_number_cond='';
	 protected $pub_shipment_date_cond='';
	 protected $po_received_date_cond='';
	 protected $shipment_date_cond='';
	 protected $pp_meeting_date_cond='';
	 protected $factory_received_date_cond='';
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
	 
	 
	 protected  $cond="";
	 
	 protected $_gmtsitemRatioArray=array();
	 protected $_costingPerQtyArr=array();
	 function __construct(){
		 return $this;
	 }
	 
	 public function company_name($value){
		 $this->company_name_cond=' and job.company_name '.$value;
		 $this->cond.=$this->company_name_cond;
		 return $this;
	 }
	 public function buyer_name($value){
		 $this->buyer_name_cond=' and job.buyer_name '.$value;
		 $this->cond.=$this->buyer_name_cond;
		 return $this;
	 }
	 public function job_no_prefix_num($value){
		 $this->job_no_prefix_num_cond=' and job.job_no_prefix_num '.$value;
		 $this->cond.=$this->job_no_prefix_num_cond;
		 return $this;
	 }
	 public function job_no($value){
		 $this->job_no_cond=' and job.job_no '.$value;
		 $this->cond.=$this->job_no_cond;
		 return $this;
	 }
	 public function style_ref_no($value){
		 $this->style_ref_no_cond=' and job.style_ref_no '.$value;
		 $this->cond.=$this->style_ref_no_cond;
		 return $this;
	 }
	 public function location_name($value){
		 $this->location_name_cond=' and job.location_name '.$value;
		 $this->cond.=$this->location_name_cond;
		 return $this;
	 }
	 public function region($value){
		 $this->region_cond=' and job.region '.$value;
		 $this->cond.=$this->region_cond;
		 return $this;
	 }
	 public function team_leader($value){
		 $this->team_leader_cond=' and job.team_leader '.$value;
		 $this->cond.=$this->team_leader_cond;
		 return $this;
	 }
	 public function dealing_marchant($value){
		 $this->dealing_marchant_cond=' and job.dealing_marchant '.$value;
		 $this->cond.=$this->dealing_marchant_cond;
		 return $this;
	 }
	 public function agent_name($value){
		 $this->agent_name_cond=' and job.agent_name '.$value;
		 $this->cond.=$this->agent_name_cond;
		 return $this;
	 }
	 public function season($value){
		 $this->season_cond=' and job.season '.$value;
		 $this->cond.=$this->season_cond;
		 return $this;
	 }
	 public function factory_marchant($value){
		 $this->factory_marchant_cond=' and job.factory_marchant '.$value;
		 $this->cond.=$this->factory_marchant_cond;
		 return $this;
	 }
	 
	 
	 
	 public function po_id($value){
		 $this->po_id_cond=' and b.id '.$value;
		 $this->cond.=$this->po_id_cond;
		 return $this;
	 }
	 public function po_number($value){
		 $this->po_number_cond=' and b.po_number '.$value;
		 $this->cond.=$this->po_number_cond;
		 return $this;
	 }
	 public function pub_shipment_date($value){
		 $this->pub_shipment_date_cond=' and b.pub_shipment_date '.$value;
		 $this->cond.=$this->pub_shipment_date_cond;
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
		 return $this->company_name_cond.$this->buyer_name_cond.$this->job_no_prefix_num_cond.$this->job_no_cond.$this->style_ref_no_cond.$this->location_name_cond.$this->region_cond.$this->team_leader_cond.$this->dealing_marchant_cond.$this->agent_name_cond.$this->season_cond.$this->factory_marchant_cond;
		 
	 }
	 public function init(){
		 $this->_setGmtsitemRatioArr();
		 $this->_setCostingPerArr();
		 return $this;
	 }
	public  function _setGmtsitemRatioArr(){
		$gmtsitemRatioSql=sql_select('select a.job_no AS "job_no",b.gmts_item_id AS "gmts_item_id" ,b.set_item_ratio AS "set_item_ratio" from wo_po_details_master a, wo_po_details_mas_set_details b where 1=1 '.$this->getJobTableCond().' and a.job_no=b.job_no','','');
		foreach($gmtsitemRatioSql as $gmtsitemRatioSqlRow)
		{
			$this->_gmtsitemRatioArray[$gmtsitemRatioSqlRow['job_no']][$gmtsitemRatioSqlRow['gmts_item_id']]=$gmtsitemRatioSqlRow['set_item_ratio'];	
		}
		return $this;
	}
	
	private function _setCostingPerArr(){
		$costingPerArr=sql_select( 'select a.job_no AS "job_no",b.costing_per AS "costing_per" from  wo_po_details_master a, wo_pre_cost_mst b where 1=1 '.$this->getJobTableCond().' and a.job_no=b.job_no', '', '');
		foreach($costingPerArr as $costingPerArrRow)
		{
			$costingPer=$costingPerArrRow['costing_per'];
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
			$this->_costingPerQtyArr[$costingPerArrRow['job_no']]=$costingPerQty;	
		}
	}
	public  function getCostingPerArr(){
		return $this->_costingPerQtyArr;
	}
	public  function getGmtsitemRatioArr(){
		return $this->_gmtsitemRatioArray;
	}
}
?>