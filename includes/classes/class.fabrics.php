<?
class fabric extends report{
	private $_By_OrderGmtscolorAndBodypart='By_OrderGmtscolorAndBodypart';
	private $_By_FabriccostidGmtsItemOrderAndGmtscolor='By_FabriccostidGmtsItemOrderAndGmtscolor';
	private $_By_FabriccostidAndGmtscolor='By_FabriccostidAndGmtscolor';
	private $_By_OrderLibYarnCountDeterIdAndGmtscolor='By_OrderLibYarnCountDeterIdAndGmtscolor';
	private $_By_OrderFabriccostidGmtscolorAndDiaWidth='By_OrderFabriccostidGmtscolorAndDiaWidth';
	private $_By_OrderFabriccostidGmtscolorDiaWidthAndRemarks='By_OrderFabriccostidGmtscolorDiaWidthAndRemarks';
	private $_By_OrderAndFabriccostid='By_OrderAndFabriccostid';
	private $_By_OrderCountryAndFabriccostid='By_OrderCountryAndFabriccostid';
	private $_By_Fabriccostid='By_Fabriccostid';
	private $_By_OrderFabriccostidAndGmtscolor='By_OrderFabriccostidAndGmtscolor';
	private $_knit='knit';
	private $_woven='woven';
	private $_finish='finish';
	private $_grey='grey';
	
	private $_query="";
	private $_dataArray=array();
	// class constructor
	function __construct(condition $condition){
		parent::__construct($condition);
		$this->_setQuery();
		$this->_setData();
	}// end class constructor
	
	private function _setQuery(){
		$this->_query='select a.job_no AS "job_no",b.id AS "id",c.item_number_id AS "item_number_id",c.country_id AS "country_id",c.color_number_id AS "color_number_id",c.size_number_id AS "size_number_id",c.order_quantity AS "order_quantity" ,c.plan_cut_qnty AS "plan_cut_qnty" ,d.id AS "pre_cost_dtls_id",d.body_part_id AS "body_part_id",d.fab_nature_id AS "fab_nature_id",d.fabric_source AS "fabric_source",d.rate AS "drate",d.lib_yarn_count_deter_id AS "lib_yarn_count_deter_id",d.uom AS "uom",e.dia_width AS "dia_width",e.remarks AS "remarks",e.cons AS "cons",e.requirment AS "requirment",e.rate AS "rate" from wo_po_details_master a, wo_po_break_down b,wo_po_color_size_breakdown c,wo_pre_cost_fabric_cost_dtls d,wo_pre_cos_fab_co_avg_con_dtls e  where 1=1 '.$this->cond.' and a.job_no=b.job_no_mst and a.job_no=c.job_no_mst and a.job_no=d.job_no and a.job_no=e.job_no  and b.id=c.po_break_down_id and d.id=e.pre_cost_fabric_cost_dtls_id and c.po_break_down_id=e.po_break_down_id and  c.item_number_id= d.item_number_id and c.color_number_id=e.color_number_id and c.size_number_id=e.gmts_sizes    and e.cons !=0   and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 and c.is_deleted=0 and c.status_active=1';//order by b.id,d.id
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
	private function _calculateQty($plan_cut_qnty,$costingPerQty,$set_item_ratio,$cons_qnty){
	  return $reqyarnqnty =($plan_cut_qnty/$set_item_ratio)*($cons_qnty/$costingPerQty);
	}
	
	private function _calculateAmount($reqyarnqnty,$rate){
	 return $amount=$reqyarnqnty*$rate;
	}
	
	private function _setQty_knitAndwoven_greyAndfinish($level){
		$jobNo='';
		$poId='';
		$itemNumberId='';
		$countryId='';
		$colorId='';
		$sizeId='';
		$planPutQnty=0;
		$preCostDtlsId='';
		$bodypartId='';
		$libYarnCountDeterId='';
		$diaWidth='';
		$remarks='';
		$cons=0;
		$requirment=0;
		$Qty=array();
		
		foreach($this->_dataArray as $row)
		{
			$jobNo=$row['job_no'];
			$poId=$row['id'];
			$itemNumberId=$row['item_number_id'];
			$countryId=$row['country_id'];
			$colorId=$row['color_number_id'];
			$sizeId=$row['size_number_id'];
			$planPutQnty=$row['plan_cut_qnty'];
			$preCostDtlsId=$row['pre_cost_dtls_id'];
			$bodypartId=$row['body_part_id'];
			$fabNatureId=$row['fab_nature_id'];
			$libYarnCountDeterId=$row['lib_yarn_count_deter_id'];
			$diaWidth=$row['dia_width'];
			$remarks=$row['remarks'];
			$cons=$row['cons'];
			$requirment=$row['requirment'];
			$uom=$row['uom'];
			$costingPerQty=$this->_costingPerQtyArr[$jobNo];
			$set_item_ratio=$this->_gmtsitemRatioArray[$jobNo][$itemNumberId];
			$reqqnty_finish =$this->_calculateQty($planPutQnty,$costingPerQty,$set_item_ratio,$cons);
			$reqqnty_grey =$this->_calculateQty($planPutQnty,$costingPerQty,$set_item_ratio,$requirment);
			
			if($level==$this->_By_Job){
				if($fabNatureId==2){
					if(isset($Qty[$this->_knit][$this->_finish][$jobNo][$uom])){
						$Qty[$this->_knit][$this->_finish][$jobNo][$uom]+=$reqqnty_finish;
					}
					else{
						$Qty[$this->_knit][$this->_finish][$jobNo][$uom]=$reqqnty_finish;
					}
					if(isset($Qty[$this->_knit][$this->_grey][$jobNo][$uom])){
						$Qty[$this->_knit][$this->_grey][$jobNo][$uom]+=$reqqnty_grey;
					}
					else{
						$Qty[$this->_knit][$this->_grey][$jobNo][$uom]=$reqqnty_grey;
					}
				}
				elseif($fabNatureId==3){
					if(isset($Qty[$this->_woven][$this->_finish][$jobNo][$uom])){
						$Qty[$this->_woven][$this->_finish][$jobNo][$uom]+=$reqqnty_finish;
					}
					else{
						$Qty[$this->_woven][$this->_finish][$jobNo][$uom]=$reqqnty_finish;
					}
					if(isset($Qty[$this->_woven][$this->_grey][$jobNo][$uom])){
						$Qty[$this->_woven][$this->_grey][$jobNo][$uom]+=$reqqnty_grey;
					}
					else{
						$Qty[$this->_woven][$this->_grey][$jobNo][$uom]=$reqqnty_grey;
					}
				}
			}
			elseif($level==$this->_By_Order){
				if($fabNatureId==2){
					if(isset($Qty[$this->_knit][$this->_finish][$poId][$uom])){
						$Qty[$this->_knit][$this->_finish][$poId][$uom]+=$reqqnty_finish;
					}
					else{
						$Qty[$this->_knit][$this->_finish][$poId][$uom]=$reqqnty_finish;
					}
					if(isset($Qty[$this->_knit][$this->_grey][$poId][$uom])){
						$Qty[$this->_knit][$this->_grey][$poId][$uom]+=$reqqnty_grey;
					}
					else{
						$Qty[$this->_knit][$this->_grey][$poId][$uom]=$reqqnty_grey;
					}
				}
				elseif($fabNatureId==3){
					if(isset($Qty[$this->_woven][$this->_finish][$poId][$uom])){
						$Qty[$this->_woven][$this->_finish][$poId][$uom]+=$reqqnty_finish;
					}
					else{
						$Qty[$this->_woven][$this->_finish][$poId][$uom]=$reqqnty_finish;
					}
					if(isset($Qty[$this->_woven][$this->_grey][$poId][$uom])){
						$Qty[$this->_woven][$this->_grey][$poId][$uom]+=$reqqnty_grey;
					}
					else{
						$Qty[$this->_woven][$this->_grey][$poId][$uom]=$reqqnty_grey;
					}
				}
			}
			elseif($level==$this->_By_OrderAndCountry){
				if($fabNatureId==2){
					if(isset($Qty[$this->_knit][$this->_finish][$poId][$countryId][$uom])){
						$Qty[$this->_knit][$this->_finish][$poId][$countryId][$uom]+=$reqqnty_finish;
					}
					else{
						$Qty[$this->_knit][$this->_finish][$poId][$countryId][$uom]=$reqqnty_finish;
					}
					if(isset($Qty[$this->_knit][$this->_grey][$poId][$countryId][$uom])){
						$Qty[$this->_knit][$this->_grey][$poId][$countryId][$uom]+=$reqqnty_grey;
					}
					else{
						$Qty[$this->_knit][$this->_grey][$poId][$countryId][$uom]=$reqqnty_grey;
					}
				}
				elseif($fabNatureId==3){
					if(isset($Qty[$this->_woven][$this->_finish][$poId][$countryId][$uom])){
						$Qty[$this->_woven][$this->_finish][$poId][$countryId][$uom]+=$reqqnty_finish;
					}
					else{
						$Qty[$this->_woven][$this->_finish][$poId][$countryId][$uom]=$reqqnty_finish;
					}
					if(isset($Qty[$this->_woven][$this->_grey][$poId][$countryId][$uom])){
						$Qty[$this->_woven][$this->_grey][$poId][$countryId][$uom]+=$reqqnty_grey;
					}
					else{
						$Qty[$this->_woven][$this->_grey][$poId][$countryId][$uom]=$reqqnty_grey;
					}
				}
			}
			elseif($level==$this->_By_OrderAndGmtsitem){
				if($fabNatureId==2){
					if(isset($Qty[$this->_knit][$this->_finish][$poId][$itemNumberId][$uom])){
						$Qty[$this->_knit][$this->_finish][$poId][$itemNumberId][$uom]+=$reqqnty_finish;
					}
					else{
						$Qty[$this->_knit][$this->_finish][$poId][$itemNumberId][$uom]=$reqqnty_finish;
					}
					if(isset($Qty[$this->_knit][$this->_grey][$poId][$itemNumberId][$uom])){
					$Qty[$this->_knit][$this->_grey][$poId][$itemNumberId][$uom]+=$reqqnty_grey;
					}
					else{
						$Qty[$this->_knit][$this->_grey][$poId][$itemNumberId][$uom]=$reqqnty_grey;
					}
				}
				elseif($fabNatureId==3){
					if(isset($Qty[$this->_woven][$this->_finish][$poId][$itemNumberId][$uom])){
						$Qty[$this->_woven][$this->_finish][$poId][$itemNumberId][$uom]+=$reqqnty_finish;
					}
					else{
						$Qty[$this->_woven][$this->_finish][$poId][$itemNumberId][$uom]+=$reqqnty_finish;
					}
					if(isset($Qty[$this->_woven][$this->_grey][$poId][$itemNumberId][$uom])){
						$Qty[$this->_woven][$this->_grey][$poId][$itemNumberId][$uom]+=$reqqnty_grey;
					}
					else{
						$Qty[$this->_woven][$this->_grey][$poId][$itemNumberId][$uom]=$reqqnty_grey;
					}
				}
			}
			elseif($level==$this->_By_OrderAndGmtscolor){
				if($fabNatureId==2){
					if(isset($Qty[$this->_knit][$this->_finish][$poId][$colorId][$uom])){
						$Qty[$this->_knit][$this->_finish][$poId][$colorId][$uom]+=$reqqnty_finish;
					}
					else{
						$Qty[$this->_knit][$this->_finish][$poId][$colorId][$uom]=$reqqnty_finish;
					}
					if(isset($Qty[$this->_knit][$this->_grey][$poId][$colorId][$uom])){
						$Qty[$this->_knit][$this->_grey][$poId][$colorId][$uom]+=$reqqnty_grey;
					}
					else{
						$Qty[$this->_knit][$this->_grey][$poId][$colorId][$uom]=$reqqnty_grey;
					}
				}
				elseif($fabNatureId==3){
					if(isset($Qty[$this->_woven][$this->_finish][$poId][$colorId][$uom])){
						$Qty[$this->_woven][$this->_finish][$poId][$colorId][$uom]+=$reqqnty_finish;
					}
					else{
						$Qty[$this->_woven][$this->_finish][$poId][$colorId][$uom]=$reqqnty_finish;
					}
					if(isset($Qty[$this->_woven][$this->_finish][$poId][$colorId][$uom])){
						$Qty[$this->_woven][$this->_grey][$poId][$colorId][$uom]+=$reqqnty_grey;
					}
					else{
						$Qty[$this->_woven][$this->_grey][$poId][$colorId][$uom]=$reqqnty_grey;
					}
				}
			}
			elseif($level==$this->_By_OrderAndGmtssize){
				if($fabNatureId==2){
					if(isset($Qty[$this->_knit][$this->_finish][$poId][$sizeId][$uom])){
						$Qty[$this->_knit][$this->_finish][$poId][$sizeId][$uom]+=$reqqnty_finish;
					}
					else{
						$Qty[$this->_knit][$this->_finish][$poId][$sizeId][$uom]=$reqqnty_finish;
					}
					if(isset($Qty[$this->_knit][$this->_grey][$poId][$sizeId][$uom])){
						$Qty[$this->_knit][$this->_grey][$poId][$sizeId][$uom]+=$reqqnty_grey;
					}
					else{
						$Qty[$this->_knit][$this->_grey][$poId][$sizeId][$uom]=$reqqnty_grey;
					}
				}
				elseif($fabNatureId==3){
					if(isset($Qty[$this->_woven][$this->_finish][$poId][$sizeId][$uom])){
						$Qty[$this->_woven][$this->_finish][$poId][$sizeId][$uom]+=$reqqnty_finish;
					}
					else{
						$Qty[$this->_woven][$this->_finish][$poId][$sizeId][$uom]=$reqqnty_finish;
					}
					if(isset($Qty[$this->_woven][$this->_grey][$poId][$sizeId][$uom])){
						$Qty[$this->_woven][$this->_grey][$poId][$sizeId][$uom]+=$reqqnty_grey;
					}
					else{
						$Qty[$this->_woven][$this->_grey][$poId][$sizeId][$uom]=$reqqnty_grey;
					}
				}
			}
			elseif($level==$this->_By_orderCountryAndGmtsitem){
				if($fabNatureId==2){
					if(isset($Qty[$this->_knit][$this->_finish][$poId][$countryId][$itemNumberId][$uom])){
						$Qty[$this->_knit][$this->_finish][$poId][$countryId][$itemNumberId][$uom]+=$reqqnty_finish;
					}
					else{
						$Qty[$this->_knit][$this->_finish][$poId][$countryId][$itemNumberId][$uom]=$reqqnty_finish;
					}
					if(isset($Qty[$this->_knit][$this->_grey][$poId][$countryId][$itemNumberId][$uom])){
						$Qty[$this->_knit][$this->_grey][$poId][$countryId][$itemNumberId][$uom]+=$reqqnty_grey;
					}
					else{
						$Qty[$this->_knit][$this->_grey][$poId][$countryId][$itemNumberId][$uom]=$reqqnty_grey;
					}
				}
				elseif($fabNatureId==3){
					if(isset($Qty[$this->_woven][$this->_finish][$poId][$countryId][$itemNumberId][$uom])){
						$Qty[$this->_woven][$this->_finish][$poId][$countryId][$itemNumberId][$uom]+=$reqqnty_finish;
					}
					else{
						$Qty[$this->_woven][$this->_finish][$poId][$countryId][$itemNumberId][$uom]=$reqqnty_finish;
					}
					if(isset($Qty[$this->_woven][$this->_grey][$poId][$countryId][$itemNumberId][$uom])){
						$Qty[$this->_woven][$this->_grey][$poId][$countryId][$itemNumberId][$uom]+=$reqqnty_grey;
					}
					else{
						$Qty[$this->_woven][$this->_grey][$poId][$countryId][$itemNumberId][$uom]=$reqqnty_grey;
					}
				}
			}
			elseif($level==$this->_By_OrderCountryAndGmtscolor){
				if($fabNatureId==2){
					if(isset($Qty[$this->_knit][$this->_finish][$poId][$countryId][$colorId][$uom])){
						$Qty[$this->_knit][$this->_finish][$poId][$countryId][$colorId][$uom]+=$reqqnty_finish;
					}
					else{
						$Qty[$this->_knit][$this->_finish][$poId][$countryId][$colorId][$uom]=$reqqnty_finish;
					}
					if(isset($Qty[$this->_knit][$this->_grey][$poId][$countryId][$colorId][$uom])){
						$Qty[$this->_knit][$this->_grey][$poId][$countryId][$colorId][$uom]+=$reqqnty_grey;
					}
					else{
						$Qty[$this->_knit][$this->_grey][$poId][$countryId][$colorId][$uom]=$reqqnty_grey;
					}
				}
				elseif($fabNatureId==3){
					if(isset($Qty[$this->_woven][$this->_finish][$poId][$countryId][$colorId][$uom])){
						$Qty[$this->_woven][$this->_finish][$poId][$countryId][$colorId][$uom]+=$reqqnty_finish;
					}
					else{
						$Qty[$this->_woven][$this->_finish][$poId][$countryId][$colorId][$uom]=$reqqnty_finish;
					}
					if(isset($Qty[$this->_woven][$this->_grey][$poId][$countryId][$colorId][$uom])){
						$Qty[$this->_woven][$this->_grey][$poId][$countryId][$colorId][$uom]+=$reqqnty_grey;
					}
					else{
						$Qty[$this->_woven][$this->_grey][$poId][$countryId][$colorId][$uom]=$reqqnty_grey;
					}
				}
			}
			elseif($level==$this->_By_OrderCountryAndGmtssize){
				if($fabNatureId==2){
					if(isset($Qty[$this->_knit][$this->_finish][$poId][$countryId][$sizeId][$uom])){
						$Qty[$this->_knit][$this->_finish][$poId][$countryId][$sizeId][$uom]+=$reqqnty_finish;
					}
					else{
						$Qty[$this->_knit][$this->_finish][$poId][$countryId][$sizeId][$uom]=$reqqnty_finish;
					}
					if(isset($Qty[$this->_knit][$this->_grey][$poId][$countryId][$sizeId][$uom])){
						$Qty[$this->_knit][$this->_grey][$poId][$countryId][$sizeId][$uom]+=$reqqnty_grey;
					}
					else{
						$Qty[$this->_knit][$this->_grey][$poId][$countryId][$sizeId][$uom]=$reqqnty_grey;
					}
				}
				elseif($fabNatureId==3){
					if(isset($Qty[$this->_woven][$this->_finish][$poId][$countryId][$sizeId][$uom])){
						$Qty[$this->_woven][$this->_finish][$poId][$countryId][$sizeId][$uom]+=$reqqnty_finish;
					}
					else{
						$Qty[$this->_woven][$this->_finish][$poId][$countryId][$sizeId][$uom]=$reqqnty_finish;
					}
					if(isset($Qty[$this->_woven][$this->_grey][$poId][$countryId][$sizeId][$uom])){
						$Qty[$this->_woven][$this->_grey][$poId][$countryId][$sizeId][$uom]+=$reqqnty_grey;
					}
					else{
						$Qty[$this->_woven][$this->_grey][$poId][$countryId][$sizeId][$uom]=$reqqnty_grey;
					}
				}
			}
			elseif($level==$this->_By_OrderGmtsitemAndGmtscolor){
				if($fabNatureId==2){
					if(isset($Qty[$this->_knit][$this->_finish][$poId][$itemNumberId][$colorId][$uom])){
						$Qty[$this->_knit][$this->_finish][$poId][$itemNumberId][$colorId][$uom]+=$reqqnty_finish;
					}
					else{
						$Qty[$this->_knit][$this->_finish][$poId][$itemNumberId][$colorId][$uom]=$reqqnty_finish;
					}
					if(isset($Qty[$this->_knit][$this->_grey][$poId][$itemNumberId][$colorId][$uom])){
						$Qty[$this->_knit][$this->_grey][$poId][$itemNumberId][$colorId][$uom]+=$reqqnty_grey;
					}
					else{
						$Qty[$this->_knit][$this->_grey][$poId][$itemNumberId][$colorId][$uom]=$reqqnty_grey;
					}
				}
				elseif($fabNatureId==3){
					if(isset($Qty[$this->_woven][$this->_finish][$poId][$itemNumberId][$colorId][$uom])){
						$Qty[$this->_woven][$this->_finish][$poId][$itemNumberId][$colorId][$uom]+=$reqqnty_finish;
					}
					else{
						$Qty[$this->_woven][$this->_finish][$poId][$itemNumberId][$colorId][$uom]=$reqqnty_finish;
					}
					if(isset($Qty[$this->_woven][$this->_grey][$poId][$itemNumberId][$colorId][$uom])){
						$Qty[$this->_woven][$this->_grey][$poId][$itemNumberId][$colorId][$uom]+=$reqqnty_grey;
					}
					else{
						$Qty[$this->_woven][$this->_grey][$poId][$itemNumberId][$colorId][$uom]=$reqqnty_grey;
					}
				}
			}
			elseif($level==$this->_By_OrderGmtsitemAndGmtssize){
				if($fabNatureId==2){
					if(isset($Qty[$this->_knit][$this->_finish][$poId][$itemNumberId][$sizeId][$uom])){
						$Qty[$this->_knit][$this->_finish][$poId][$itemNumberId][$sizeId][$uom]+=$reqqnty_finish;
					}
					else{
						$Qty[$this->_knit][$this->_finish][$poId][$itemNumberId][$sizeId][$uom]=$reqqnty_finish;
					}
					if(isset($Qty[$this->_knit][$this->_grey][$poId][$itemNumberId][$sizeId][$uom])){
						$Qty[$this->_knit][$this->_grey][$poId][$itemNumberId][$sizeId][$uom]+=$reqqnty_grey;
					}
					else{
						$Qty[$this->_knit][$this->_grey][$poId][$itemNumberId][$sizeId][$uom]=$reqqnty_grey;
					}
				}
				elseif($fabNatureId==3){
					if(isset($Qty[$this->_woven][$this->_finish][$poId][$itemNumberId][$sizeId][$uom])){
						$Qty[$this->_woven][$this->_finish][$poId][$itemNumberId][$sizeId][$uom]+=$reqqnty_finish;
					}
					else{
						$Qty[$this->_woven][$this->_finish][$poId][$itemNumberId][$sizeId][$uom]=$reqqnty_finish;
					}
					if(isset($Qty[$this->_woven][$this->_grey][$poId][$itemNumberId][$sizeId][$uom])){
						$Qty[$this->_woven][$this->_grey][$poId][$itemNumberId][$sizeId][$uom]+=$reqqnty_grey;
					}
					else{
						$Qty[$this->_woven][$this->_grey][$poId][$itemNumberId][$sizeId][$uom]=$reqqnty_grey;
					}
				}
			}
			elseif($level==$this->_By_OrderGmtscolorAndGmtssize){
				if($fabNatureId==2){
					if(isset($Qty[$this->_knit][$this->_finish][$poId][$colorId][$sizeId][$uom])){
						$Qty[$this->_knit][$this->_finish][$poId][$colorId][$sizeId][$uom]+=$reqqnty_finish;
					}
					else{
						$Qty[$this->_knit][$this->_finish][$poId][$colorId][$sizeId][$uom]=$reqqnty_finish;
					}
					if(isset($Qty[$this->_knit][$this->_grey][$poId][$colorId][$sizeId][$uom])){
						$Qty[$this->_knit][$this->_grey][$poId][$colorId][$sizeId][$uom]+=$reqqnty_grey;
					}
					else{
						$Qty[$this->_knit][$this->_grey][$poId][$colorId][$sizeId][$uom]=$reqqnty_grey;
					}
				}
				elseif($fabNatureId==3){
					if(isset($Qty[$this->_woven][$this->_finish][$poId][$colorId][$sizeId][$uom])){
						$Qty[$this->_woven][$this->_finish][$poId][$colorId][$sizeId][$uom]+=$reqqnty_finish;
					}
					else{
						$Qty[$this->_woven][$this->_finish][$poId][$colorId][$sizeId][$uom]=$reqqnty_finish;
					}
					if(isset($Qty[$this->_woven][$this->_grey][$poId][$colorId][$sizeId][$uom]))
					{
						$Qty[$this->_woven][$this->_grey][$poId][$colorId][$sizeId][$uom]+=$reqqnty_grey;
					}
					else{
						$Qty[$this->_woven][$this->_grey][$poId][$colorId][$sizeId][$uom]=$reqqnty_grey;
					}
				}
			}
			//==========================================================
			elseif($level==$this->_By_OrderGmtscolorAndBodypart){
				if($fabNatureId==2){
					if(isset($Qty[$this->_knit][$this->_finish][$poId][$colorId][$bodypartId][$uom])){
						$Qty[$this->_knit][$this->_finish][$poId][$colorId][$bodypartId][$uom]+=$reqqnty_finish;
					}
					else{
						$Qty[$this->_knit][$this->_finish][$poId][$colorId][$bodypartId][$uom]=$reqqnty_finish;
					}
					if(isset($Qty[$this->_knit][$this->_grey][$poId][$colorId][$bodypartId][$uom])){
						$Qty[$this->_knit][$this->_grey][$poId][$colorId][$bodypartId][$uom]+=$reqqnty_grey;
					}
					else{
						$Qty[$this->_knit][$this->_grey][$poId][$colorId][$bodypartId][$uom]=$reqqnty_grey;
					}
				}
				elseif($fabNatureId==3){
					if(isset($Qty[$this->_woven][$this->_finish][$poId][$colorId][$bodypartId][$uom])){
						$Qty[$this->_woven][$this->_finish][$poId][$colorId][$bodypartId][$uom]+=$reqqnty_finish;
					}
					else{
						$Qty[$this->_woven][$this->_finish][$poId][$colorId][$bodypartId][$uom]=$reqqnty_finish;
					}
					if(isset($Qty[$this->_woven][$this->_grey][$poId][$colorId][$bodypartId][$uom]))
					{
						$Qty[$this->_woven][$this->_grey][$poId][$colorId][$bodypartId][$uom]+=$reqqnty_grey;
					}
					else{
						$Qty[$this->_woven][$this->_grey][$poId][$colorId][$bodypartId][$uom]=$reqqnty_grey;
					}
				}
			}
			elseif($level==$this->_By_FabriccostidGmtsItemOrderAndGmtscolor){
				if($fabNatureId==2){
					if(isset($Qty[$this->_knit][$this->_finish][$preCostDtlsId][$itemNumberId][$poId][$colorId][$uom])){
						$Qty[$this->_knit][$this->_finish][$preCostDtlsId][$itemNumberId][$poId][$colorId][$uom]+=$reqqnty_finish;
					}
					else{
						$Qty[$this->_knit][$this->_finish][$preCostDtlsId][$itemNumberId][$poId][$colorId][$uom]=$reqqnty_finish;
					}
					if(isset($Qty[$this->_knit][$this->_grey][$preCostDtlsId][$itemNumberId][$poId][$colorId][$uom])){
						$Qty[$this->_knit][$this->_grey][$preCostDtlsId][$itemNumberId][$poId][$colorId][$uom]+=$reqqnty_grey;
					}
					else{
						$Qty[$this->_knit][$this->_grey][$preCostDtlsId][$itemNumberId][$poId][$colorId][$uom]=$reqqnty_grey;
					}
				}
				elseif($fabNatureId==3){
					if(isset($Qty[$this->_woven][$this->_finish][$preCostDtlsId][$itemNumberId][$poId][$colorId][$uom])){
						$Qty[$this->_woven][$this->_finish][$preCostDtlsId][$itemNumberId][$poId][$colorId][$uom]+=$reqqnty_finish;
					}
					else{
						$Qty[$this->_woven][$this->_finish][$preCostDtlsId][$itemNumberId][$poId][$colorId][$uom]=$reqqnty_finish;
					}
					if(isset($Qty[$this->_woven][$this->_grey][$preCostDtlsId][$itemNumberId][$poId][$colorId][$uom]))
					{
						$Qty[$this->_woven][$this->_grey][$preCostDtlsId][$itemNumberId][$poId][$colorId][$uom]+=$reqqnty_grey;
					}
					else{
						$Qty[$this->_woven][$this->_grey][$preCostDtlsId][$itemNumberId][$poId][$colorId][$uom]=$reqqnty_grey;
					}
				}
			}
			elseif($level==$this->_By_FabriccostidAndGmtscolor){
				if($fabNatureId==2){
					if(isset($Qty[$this->_knit][$this->_finish][$preCostDtlsId][$colorId][$uom])){
						$Qty[$this->_knit][$this->_finish][$preCostDtlsId][$colorId][$uom]+=$reqqnty_finish;
					}
					else{
						$Qty[$this->_knit][$this->_finish][$preCostDtlsId][$colorId][$uom]=$reqqnty_finish;
					}
					if(isset($Qty[$this->_knit][$this->_grey][$preCostDtlsId][$colorId][$uom])){
						$Qty[$this->_knit][$this->_grey][$preCostDtlsId][$colorId][$uom]+=$reqqnty_grey;
					}
					else{
						$Qty[$this->_knit][$this->_grey][$preCostDtlsId][$colorId][$uom]=$reqqnty_grey;
					}
				}
				elseif($fabNatureId==3){
					if(isset($Qty[$this->_woven][$this->_finish][$preCostDtlsId][$colorId][$uom])){
						$Qty[$this->_woven][$this->_finish][$preCostDtlsId][$colorId][$uom]+=$reqqnty_finish;
					}
					else{
						$Qty[$this->_woven][$this->_finish][$preCostDtlsId][$colorId][$uom]=$reqqnty_finish;
					}
					if(isset($Qty[$this->_woven][$this->_grey][$preCostDtlsId][$colorId][$uom]))
					{
						$Qty[$this->_woven][$this->_grey][$preCostDtlsId][$colorId][$uom]+=$reqqnty_grey;
					}
					else{
						$Qty[$this->_woven][$this->_grey][$preCostDtlsId][$colorId][$uom]=$reqqnty_grey;
					}
				}
			}
			elseif($level==$this->_By_OrderLibYarnCountDeterIdAndGmtscolor){
				if($fabNatureId==2){
					if(isset($Qty[$this->_knit][$this->_finish][$poId][$libYarnCountDeterId][$colorId][$uom])){
						$Qty[$this->_knit][$this->_finish][$poId][$libYarnCountDeterId][$colorId][$uom]+=$reqqnty_finish;
					}
					else{
						$Qty[$this->_knit][$this->_finish][$poId][$libYarnCountDeterId][$colorId][$uom]=$reqqnty_finish;
					}
					if(isset($Qty[$this->_knit][$this->_grey][$poId][$libYarnCountDeterId][$colorId][$uom])){
						$Qty[$this->_knit][$this->_grey][$poId][$libYarnCountDeterId][$colorId][$uom]+=$reqqnty_grey;
					}
					else{
						$Qty[$this->_knit][$this->_grey][$poId][$libYarnCountDeterId][$colorId][$uom]=$reqqnty_grey;
					}
				}
				elseif($fabNatureId==3){
					if(isset($Qty[$this->_woven][$this->_finish][$poId][$libYarnCountDeterId][$colorId][$uom])){
						$Qty[$this->_woven][$this->_finish][$poId][$libYarnCountDeterId][$colorId][$uom]+=$reqqnty_finish;
					}
					else{
						$Qty[$this->_woven][$this->_finish][$poId][$libYarnCountDeterId][$colorId][$uom]=$reqqnty_finish;
					}
					if(isset($Qty[$this->_woven][$this->_grey][$poId][$libYarnCountDeterId][$colorId][$uom]))
					{
						$Qty[$this->_woven][$this->_grey][$poId][$libYarnCountDeterId][$colorId][$uom]+=$reqqnty_grey;
					}
					else{
						$Qty[$this->_woven][$this->_grey][$poId][$libYarnCountDeterId][$colorId][$uom]=$reqqnty_grey;
					}
				}
			}
			elseif($level==$this->_By_OrderFabriccostidGmtscolorAndDiaWidth){
				if($fabNatureId==2){
					if(isset($Qty[$this->_knit][$this->_finish][$poId][$preCostDtlsId][$colorId][$diaWidth][$uom])){
						$Qty[$this->_knit][$this->_finish][$poId][$preCostDtlsId][$colorId][$diaWidth][$uom]+=$reqqnty_finish;
					}
					else{
						$Qty[$this->_knit][$this->_finish][$poId][$preCostDtlsId][$colorId][$diaWidth][$uom]=$reqqnty_finish;
					}
					if(isset($Qty[$this->_knit][$this->_grey][$poId][$preCostDtlsId][$colorId][$diaWidth][$uom])){
						$Qty[$this->_knit][$this->_grey][$poId][$preCostDtlsId][$colorId][$diaWidth][$uom]+=$reqqnty_grey;
					}
					else{
						$Qty[$this->_knit][$this->_grey][$poId][$preCostDtlsId][$colorId][$diaWidth][$uom]=$reqqnty_grey;
					}
				}
				elseif($fabNatureId==3){
					if(isset($Qty[$this->_woven][$this->_finish][$poId][$preCostDtlsId][$colorId][$diaWidth][$uom])){
						$Qty[$this->_woven][$this->_finish][$poId][$preCostDtlsId][$colorId][$diaWidth][$uom]+=$reqqnty_finish;
					}
					else{
						$Qty[$this->_woven][$this->_finish][$poId][$preCostDtlsId][$colorId][$diaWidth][$uom]=$reqqnty_finish;
					}
					if(isset($Qty[$this->_woven][$this->_grey][$poId][$preCostDtlsId][$colorId][$diaWidth][$uom]))
					{
						$Qty[$this->_woven][$this->_grey][$poId][$preCostDtlsId][$colorId][$diaWidth][$uom]+=$reqqnty_grey;
					}
					else{
						$Qty[$this->_woven][$this->_grey][$poId][$preCostDtlsId][$colorId][$diaWidth][$uom]=$reqqnty_grey;
					}
				}
			}
			elseif($level==$this->_By_OrderFabriccostidGmtscolorDiaWidthAndRemarks){
				if($fabNatureId==2){
					if(isset($Qty[$this->_knit][$this->_finish][$poId][$preCostDtlsId][$colorId][$diaWidth][$remarks][$uom])){
						$Qty[$this->_knit][$this->_finish][$poId][$preCostDtlsId][$colorId][$diaWidth][$remarks][$uom]+=$reqqnty_finish;
					}
					else{
						$Qty[$this->_knit][$this->_finish][$poId][$preCostDtlsId][$colorId][$diaWidth][$remarks][$uom]=$reqqnty_finish;
					}
					if(isset($Qty[$this->_knit][$this->_grey][$poId][$preCostDtlsId][$colorId][$diaWidth][$remarks][$uom])){
						$Qty[$this->_knit][$this->_grey][$poId][$preCostDtlsId][$colorId][$diaWidth][$remarks][$uom]+=$reqqnty_grey;
					}
					else{
						$Qty[$this->_knit][$this->_grey][$poId][$preCostDtlsId][$colorId][$diaWidth][$remarks][$uom]=$reqqnty_grey;
					}
				}
				elseif($fabNatureId==3){
					if(isset($Qty[$this->_woven][$this->_finish][$poId][$preCostDtlsId][$colorId][$diaWidth][$remarks][$uom])){
						$Qty[$this->_woven][$this->_finish][$poId][$preCostDtlsId][$colorId][$diaWidth][$remarks][$uom]+=$reqqnty_finish;
					}
					else{
						$Qty[$this->_woven][$this->_finish][$poId][$preCostDtlsId][$colorId][$diaWidth][$remarks][$uom]=$reqqnty_finish;
					}
					if(isset($Qty[$this->_woven][$this->_grey][$poId][$preCostDtlsId][$colorId][$diaWidth][$remarks][$uom]))
					{
						$Qty[$this->_woven][$this->_grey][$poId][$preCostDtlsId][$colorId][$diaWidth][$remarks][$uom]+=$reqqnty_grey;
					}
					else{
						$Qty[$this->_woven][$this->_grey][$poId][$preCostDtlsId][$colorId][$diaWidth][$remarks][$uom]=$reqqnty_grey;
					}
				}
			}
			//=======================
			elseif($level==$this->_By_orderCountryGmtsitemAndGmtscolor){
				if($fabNatureId==2){
					if(isset($Qty[$this->_knit][$this->_finish][$poId][$countryId][$itemNumberId][$colorId][$uom])){
						$Qty[$this->_knit][$this->_finish][$poId][$countryId][$itemNumberId][$colorId][$uom]+=$reqqnty_finish;
					}
					else{
						$Qty[$this->_knit][$this->_finish][$poId][$countryId][$itemNumberId][$colorId][$uom]=$reqqnty_finish;
					}
					if(isset($Qty[$this->_knit][$this->_grey][$poId][$countryId][$itemNumberId][$colorId][$uom])){
						$Qty[$this->_knit][$this->_grey][$poId][$countryId][$itemNumberId][$colorId][$uom]+=$reqqnty_grey;
					}
					else{
						$Qty[$this->_knit][$this->_grey][$poId][$countryId][$itemNumberId][$colorId][$uom]=$reqqnty_grey;
					}
				}
				elseif($fabNatureId==3){
					if(isset($Qty[$this->_woven][$this->_finish][$poId][$countryId][$itemNumberId][$colorId][$uom])){
						$Qty[$this->_woven][$this->_finish][$poId][$countryId][$itemNumberId][$colorId][$uom]+=$reqqnty_finish;
					}
					else{
						$Qty[$this->_woven][$this->_finish][$poId][$countryId][$itemNumberId][$colorId][$uom]=$reqqnty_finish;
					}
					if(isset($Qty[$this->_woven][$this->_grey][$poId][$countryId][$itemNumberId][$colorId][$uom])){
						$Qty[$this->_woven][$this->_grey][$poId][$countryId][$itemNumberId][$colorId][$uom]+=$reqqnty_grey;
					}
					else{
						$Qty[$this->_woven][$this->_grey][$poId][$countryId][$itemNumberId][$colorId][$uom]=$reqqnty_grey;
					}
				}
			}
			elseif($level==$this->_By_orderCountryGmtsitemAndGmtssize){
				if($fabNatureId==2){
					if(isset($Qty[$this->_knit][$this->_finish][$poId][$countryId][$itemNumberId][$sizeId][$uom])){
						$Qty[$this->_knit][$this->_finish][$poId][$countryId][$itemNumberId][$sizeId][$uom]+=$reqqnty_finish;
					}
					else{
						$Qty[$this->_knit][$this->_finish][$poId][$countryId][$itemNumberId][$sizeId][$uom]=$reqqnty_finish;
					}
					if(isset($Qty[$this->_knit][$this->_grey][$poId][$countryId][$itemNumberId][$sizeId][$uom])){
						$Qty[$this->_knit][$this->_grey][$poId][$countryId][$itemNumberId][$sizeId][$uom]+=$reqqnty_grey;
					}
					else{
						$Qty[$this->_knit][$this->_grey][$poId][$countryId][$itemNumberId][$sizeId][$uom]=$reqqnty_grey;
					}
				}
				elseif($fabNatureId==3){
					if(isset($Qty[$this->_woven][$this->_finish][$poId][$countryId][$itemNumberId][$sizeId][$uom])){
						$Qty[$this->_woven][$this->_finish][$poId][$countryId][$itemNumberId][$sizeId][$uom]+=$reqqnty_finish;
					}
					else{
						$Qty[$this->_woven][$this->_finish][$poId][$countryId][$itemNumberId][$sizeId][$uom]=$reqqnty_finish;
					}
					if(isset($Qty[$this->_woven][$this->_grey][$poId][$countryId][$itemNumberId][$sizeId][$uom])){
						$Qty[$this->_woven][$this->_grey][$poId][$countryId][$itemNumberId][$sizeId][$uom]+=$reqqnty_grey;
					}
					else{
						$Qty[$this->_woven][$this->_grey][$poId][$countryId][$itemNumberId][$sizeId][$uom]=$reqqnty_grey;
					}
				}
			}
			elseif($level==$this->_By_orderCountryGmtscolorAndGmtssize){
				if($fabNatureId==2){
					if(isset($Qty[$this->_knit][$this->_finish][$poId][$countryId][$colorId][$sizeId][$uom])){
						$Qty[$this->_knit][$this->_finish][$poId][$countryId][$colorId][$sizeId][$uom]+=$reqqnty_finish;
					}
					else{
						$Qty[$this->_knit][$this->_finish][$poId][$countryId][$colorId][$sizeId][$uom]=$reqqnty_finish;
					}
					if(isset($Qty[$this->_knit][$this->_grey][$poId][$countryId][$colorId][$sizeId][$uom])){
						$Qty[$this->_knit][$this->_grey][$poId][$countryId][$colorId][$sizeId][$uom]+=$reqqnty_grey;
					}
					else{
						$Qty[$this->_knit][$this->_grey][$poId][$countryId][$colorId][$sizeId][$uom]=$reqqnty_grey;
					}
				}
				elseif($fabNatureId==3){
					if(isset($Qty[$this->_woven][$this->_finish][$poId][$countryId][$colorId][$sizeId][$uom])){
						$Qty[$this->_woven][$this->_finish][$poId][$countryId][$colorId][$sizeId][$uom]+=$reqqnty_finish;
					}
					else{
						$Qty[$this->_woven][$this->_finish][$poId][$countryId][$colorId][$sizeId][$uom]=$reqqnty_finish;
					}
					if(isset($Qty[$this->_woven][$this->_grey][$poId][$countryId][$colorId][$sizeId][$uom])){
						$Qty[$this->_woven][$this->_grey][$poId][$countryId][$colorId][$sizeId][$uom]+=$reqqnty_grey;
					}
					else{
						$Qty[$this->_woven][$this->_grey][$poId][$countryId][$colorId][$sizeId][$uom]=$reqqnty_grey;
					}
				}
			}
			elseif($level==$this->_By_orderGmtsitemGmtscolorAndGmtssize){
				if($fabNatureId==2){
					if(isset($Qty[$this->_knit][$this->_finish][$poId][$itemNumberId][$colorId][$sizeId][$uom])){
						$Qty[$this->_knit][$this->_finish][$poId][$itemNumberId][$colorId][$sizeId][$uom]+=$reqqnty_finish;
					}
					else{
						$Qty[$this->_knit][$this->_finish][$poId][$itemNumberId][$colorId][$sizeId][$uom]=$reqqnty_finish;
					}
					if(isset($Qty[$this->_knit][$this->_grey][$poId][$itemNumberId][$colorId][$sizeId][$uom])){
						$Qty[$this->_knit][$this->_grey][$poId][$itemNumberId][$colorId][$sizeId][$uom]+=$reqqnty_grey;
					}
					else{
						$Qty[$this->_knit][$this->_grey][$poId][$itemNumberId][$colorId][$sizeId][$uom]=$reqqnty_grey;
					}
				}
				elseif($fabNatureId==3){
					if(isset($Qty[$this->_woven][$this->_finish][$poId][$itemNumberId][$colorId][$sizeId][$uom])){
						$Qty[$this->_woven][$this->_finish][$poId][$itemNumberId][$colorId][$sizeId][$uom]+=$reqqnty_finish;
					}
					else{
						$Qty[$this->_woven][$this->_finish][$poId][$itemNumberId][$colorId][$sizeId][$uom]=$reqqnty_finish;
					}
					if(isset($Qty[$this->_woven][$this->_grey][$poId][$itemNumberId][$colorId][$sizeId][$uom])){
						$Qty[$this->_woven][$this->_grey][$poId][$itemNumberId][$colorId][$sizeId][$uom]+=$reqqnty_grey;
					}
					else{
						$Qty[$this->_woven][$this->_grey][$poId][$itemNumberId][$colorId][$sizeId][$uom]=$reqqnty_grey;
					}
				}
			}
			elseif($level==$this->_By_orderCountryGmtsitemGmtscolorAndGmtssize){
				if($fabNatureId==2){
					if(isset($Qty[$this->_knit][$this->_finish][$poId][$countryId][$itemNumberId][$colorId][$sizeId][$uom])){
						$Qty[$this->_knit][$this->_finish][$poId][$countryId][$itemNumberId][$colorId][$sizeId][$uom]+=$reqqnty_finish;
					}
					else{
						$Qty[$this->_knit][$this->_finish][$poId][$countryId][$itemNumberId][$colorId][$sizeId][$uom]=$reqqnty_finish;
					}
					if(isset($Qty[$this->_knit][$this->_grey][$poId][$countryId][$itemNumberId][$colorId][$sizeId][$uom])){
						$Qty[$this->_knit][$this->_grey][$poId][$countryId][$itemNumberId][$colorId][$sizeId][$uom]+=$reqqnty_grey;
					}
					else{
						$Qty[$this->_knit][$this->_grey][$poId][$countryId][$itemNumberId][$colorId][$sizeId][$uom]=$reqqnty_grey;
					}
				}
				elseif($fabNatureId==3){
					if(isset($Qty[$this->_woven][$this->_finish][$poId][$countryId][$itemNumberId][$colorId][$sizeId][$uom])){
						$Qty[$this->_woven][$this->_finish][$poId][$countryId][$itemNumberId][$colorId][$sizeId][$uom]+=$reqqnty_finish;
					}
					else{
						$Qty[$this->_woven][$this->_finish][$poId][$countryId][$itemNumberId][$colorId][$sizeId][$uom]=$reqqnty_finish;
					}
					if(isset($Qty[$this->_woven][$this->_grey][$poId][$countryId][$itemNumberId][$colorId][$sizeId][$uom])){
						$Qty[$this->_woven][$this->_grey][$poId][$countryId][$itemNumberId][$colorId][$sizeId][$uom]+=$reqqnty_grey;
					}
					else{
						$Qty[$this->_woven][$this->_grey][$poId][$countryId][$itemNumberId][$colorId][$sizeId][$uom]=$reqqnty_grey;
					}
				}
			}
			elseif($level==$this->_By_OrderAndFabriccostid){
				if($fabNatureId==2){
					if(isset($Qty[$this->_knit][$this->_finish][$poId][$preCostDtlsId][$uom])){
						$Qty[$this->_knit][$this->_finish][$poId][$preCostDtlsId][$uom]+=$reqqnty_finish;
					}
					else{
						$Qty[$this->_knit][$this->_finish][$poId][$preCostDtlsId][$uom]=$reqqnty_finish;
					}
					if(isset($Qty[$this->_knit][$this->_grey][$poId][$preCostDtlsId][$uom])){
						$Qty[$this->_knit][$this->_grey][$poId][$preCostDtlsId][$uom]+=$reqqnty_grey;
					}
					else{
						$Qty[$this->_knit][$this->_grey][$poId][$preCostDtlsId][$uom]=$reqqnty_grey;
					}
				}
				elseif($fabNatureId==3){
					if(isset($Qty[$this->_woven][$this->_finish][$poId][$preCostDtlsId][$uom])){
						$Qty[$this->_woven][$this->_finish][$poId][$preCostDtlsId][$uom]+=$reqqnty_finish;
					}
					else{
						$Qty[$this->_woven][$this->_finish][$poId][$preCostDtlsId][$uom]=$reqqnty_finish;
					}
					if(isset($Qty[$this->_woven][$this->_grey][$poId][$preCostDtlsId][$uom])){
						$Qty[$this->_woven][$this->_grey][$poId][$preCostDtlsId][$uom]+=$reqqnty_grey;
					}
					else{
						$Qty[$this->_woven][$this->_grey][$poId][$preCostDtlsId][$uom]=$reqqnty_grey;
					}
				}
			}
			elseif($level==$this->_By_OrderFabriccostidAndGmtscolor){
				if($fabNatureId==2){
					if(isset($Qty[$this->_knit][$this->_finish][$poId][$preCostDtlsId][$colorId][$uom])){
						$Qty[$this->_knit][$this->_finish][$poId][$preCostDtlsId][$colorId][$uom]+=$reqqnty_finish;
					}
					else{
						$Qty[$this->_knit][$this->_finish][$poId][$preCostDtlsId][$colorId][$uom]=$reqqnty_finish;
					}
					if(isset($Qty[$this->_knit][$this->_grey][$poId][$preCostDtlsId][$colorId][$uom])){
						$Qty[$this->_knit][$this->_grey][$poId][$preCostDtlsId][$colorId][$uom]+=$reqqnty_grey;
					}
					else{
						$Qty[$this->_knit][$this->_grey][$poId][$preCostDtlsId][$colorId][$uom]=$reqqnty_grey;
					}
				}
				elseif($fabNatureId==3){
					if(isset($Qty[$this->_woven][$this->_finish][$poId][$preCostDtlsId][$colorId][$uom])){
						$Qty[$this->_woven][$this->_finish][$poId][$preCostDtlsId][$colorId][$uom]+=$reqqnty_finish;
					}
					else{
						$Qty[$this->_woven][$this->_finish][$poId][$preCostDtlsId][$colorId][$uom]=$reqqnty_finish;
					}
					if(isset($Qty[$this->_woven][$this->_grey][$poId][$preCostDtlsId][$colorId][$uom])){
						$Qty[$this->_woven][$this->_grey][$poId][$preCostDtlsId][$colorId][$uom]+=$reqqnty_grey;
					}
					else{
						$Qty[$this->_woven][$this->_grey][$poId][$preCostDtlsId][$colorId][$uom]=$reqqnty_grey;
					}
				}
			}
			elseif($level==$this->_By_OrderCountryAndFabriccostid){
				if($fabNatureId==2){
					if(isset($Qty[$this->_knit][$this->_finish][$poId][$countryId][$preCostDtlsId][$uom])){
						$Qty[$this->_knit][$this->_finish][$poId][$countryId][$preCostDtlsId][$uom]+=$reqqnty_finish;
					}
					else{
						$Qty[$this->_knit][$this->_finish][$poId][$countryId][$preCostDtlsId][$uom]=$reqqnty_finish;
					}
					if(isset($Qty[$this->_knit][$this->_grey][$poId][$countryId][$preCostDtlsId][$uom])){
						$Qty[$this->_knit][$this->_grey][$poId][$countryId][$preCostDtlsId][$uom]+=$reqqnty_grey;
					}
					else{
						$Qty[$this->_knit][$this->_grey][$poId][$countryId][$preCostDtlsId][$uom]=$reqqnty_grey;
					}
				}
				elseif($fabNatureId==3){
					if(isset($Qty[$this->_woven][$this->_finish][$poId][$countryId][$preCostDtlsId][$uom])){
						$Qty[$this->_woven][$this->_finish][$poId][$countryId][$preCostDtlsId][$uom]+=$reqqnty_finish;
					}
					else{
						$Qty[$this->_woven][$this->_finish][$poId][$countryId][$preCostDtlsId][$uom]=$reqqnty_finish;
					}
					if(isset($Qty[$this->_woven][$this->_grey][$poId][$countryId][$preCostDtlsId][$uom])){
						$Qty[$this->_woven][$this->_grey][$poId][$countryId][$preCostDtlsId][$uom]+=$reqqnty_grey;
					}
					else{
						$Qty[$this->_woven][$this->_grey][$poId][$countryId][$preCostDtlsId][$uom]=$reqqnty_grey;
					}
				}
			}
			elseif($level==$this->_By_Fabriccostid){
				if($fabNatureId==2){
					if(isset($Qty[$this->_knit][$this->_finish][$preCostDtlsId][$uom])){
						$Qty[$this->_knit][$this->_finish][$preCostDtlsId][$uom]+=$reqqnty_finish;
					}
					else{
						$Qty[$this->_knit][$this->_finish][$preCostDtlsId][$uom]=$reqqnty_finish;
					}
					if(isset($Qty[$this->_knit][$this->_grey][$preCostDtlsId][$uom])){
						$Qty[$this->_knit][$this->_grey][$preCostDtlsId][$uom]+=$reqqnty_grey;
					}
					else{
						$Qty[$this->_knit][$this->_grey][$preCostDtlsId][$uom]=$reqqnty_grey;
					}
				}
				elseif($fabNatureId==3){
					if(isset($Qty[$this->_woven][$this->_finish][$preCostDtlsId][$uom])){
						$Qty[$this->_woven][$this->_finish][$preCostDtlsId][$uom]+=$reqqnty_finish;
					}
					else{
						$Qty[$this->_woven][$this->_finish][$preCostDtlsId][$uom]=$reqqnty_finish;
					}
					if(isset($Qty[$this->_woven][$this->_grey][$preCostDtlsId][$uom])){
						$Qty[$this->_woven][$this->_grey][$preCostDtlsId][$uom]+=$reqqnty_grey;
					}
					else{
						$Qty[$this->_woven][$this->_grey][$preCostDtlsId][$uom]=$reqqnty_grey;
					}
				}
			}
			else{
				return null;
			}
		}
		return $Qty;
	}
	
	private function _setAmount_knitAndwoven_greyAndfinish($level){
		$jobNo='';
		$itemNumberId='';
		$planPutQnty=0;
		$bodypartId='';
		$requirment=0;
		$poId='';
		$Amount=array();
		foreach($this->_dataArray as $row)
		{
			$jobNo=$row['job_no'];
			$poId=$row['id'];
			$itemNumberId=$row['item_number_id'];
			$countryId=$row['country_id'];
			$colorId=$row['color_number_id'];
			$sizeId=$row['size_number_id'];
			$planPutQnty=$row['plan_cut_qnty'];
			$preCostDtlsId=$row['pre_cost_dtls_id'];
			$bodypartId=$row['body_part_id'];
			$fabNatureId=$row['fab_nature_id'];
			$libYarnCountDeterId=$row['lib_yarn_count_deter_id'];
			$diaWidth=$row['dia_width'];
			$remarks=$row['remarks'];
			$cons=$row['cons'];
			$requirment=$row['requirment'];
			$rate=$row['rate'];
			$uom=$row['uom'];
			$costingPerQty=$this->_costingPerQtyArr[$jobNo];
			$set_item_ratio=$this->_gmtsitemRatioArray[$jobNo][$itemNumberId];
			
			$reqqnty_finish =$this->_calculateQty($planPutQnty,$costingPerQty,$set_item_ratio,$cons);
			$reqqnty_grey =$this->_calculateQty($planPutQnty,$costingPerQty,$set_item_ratio,$requirment);
			
			$amount_finish=$this->_calculateAmount($reqqnty_finish,$rate);
			$amount_grey=$this->_calculateAmount($reqqnty_grey,$rate);
			
			if($level==$this->_By_Job){
				if($fabNatureId==2){
					if(isset($Amount[$this->_knit][$this->_finish][$jobNo][$uom])){
						$Amount[$this->_knit][$this->_finish][$jobNo][$uom]+=$amount_finish;
					}
					else{
						$Amount[$this->_knit][$this->_finish][$jobNo][$uom]=$amount_finish;
					}
					if(isset($Amount[$this->_knit][$this->_grey][$jobNo][$uom])){
						$Amount[$this->_knit][$this->_grey][$jobNo][$uom]+=$amount_grey;
					}
					else{
						$Amount[$this->_knit][$this->_grey][$jobNo][$uom]=$amount_grey;
					}
				}
				elseif($fabNatureId==3){
					if(isset($Amount[$this->_woven][$this->_finish][$jobNo][$uom])){
						$Amount[$this->_woven][$this->_finish][$jobNo][$uom]+=$amount_finish;
					}
					else{
						$Amount[$this->_woven][$this->_finish][$jobNo][$uom]=$amount_finish;
					}
					if(isset($Amount[$this->_woven][$this->_grey][$jobNo][$uom])){
						$Amount[$this->_woven][$this->_grey][$jobNo][$uom]+=$amount_grey;
					}
					else{
						$Amount[$this->_woven][$this->_grey][$jobNo][$uom]=$amount_grey;
					}
				}
			}
			elseif($level==$this->_By_Order){
				if($fabNatureId==2){
					if(isset($Amount[$this->_knit][$this->_finish][$poId][$uom])){
						$Amount[$this->_knit][$this->_finish][$poId][$uom]+=$amount_finish;
					}
					else{
						$Amount[$this->_knit][$this->_finish][$poId][$uom]=$amount_finish;
					}
					if(isset($Amount[$this->_knit][$this->_grey][$poId][$uom])){
						$Amount[$this->_knit][$this->_grey][$poId][$uom]+=$amount_grey;
					}
					else{
						$Amount[$this->_knit][$this->_grey][$poId][$uom]=$amount_grey;
					}
				}
				elseif($fabNatureId==3){
					if(isset($Amount[$this->_woven][$this->_finish][$poId][$uom])){
						$Amount[$this->_woven][$this->_finish][$poId][$uom]+=$amount_finish;
					}
					else{
						$Amount[$this->_woven][$this->_finish][$poId][$uom]=$amount_finish;
					}
					if(isset($Amount[$this->_woven][$this->_grey][$poId][$uom])){
						$Amount[$this->_woven][$this->_grey][$poId][$uom]+=$amount_grey;
					}
					else{
						$Amount[$this->_woven][$this->_grey][$poId][$uom]=$amount_grey;
					}
				}
			}
			elseif($level==$this->_By_OrderAndCountry){
				if($fabNatureId==2){
					if(isset($Amount[$this->_knit][$this->_finish][$poId][$countryId][$uom])){
						$Amount[$this->_knit][$this->_finish][$poId][$countryId][$uom]+=$amount_finish;
					}
					else{
						$Amount[$this->_knit][$this->_finish][$poId][$countryId][$uom]=$amount_finish;
					}
					if(isset($Amount[$this->_knit][$this->_grey][$poId][$countryId][$uom])){
						$Amount[$this->_knit][$this->_grey][$poId][$countryId][$uom]+=$amount_grey;
					}
					else{
						$Amount[$this->_knit][$this->_grey][$poId][$countryId][$uom]=$amount_grey;
					}
				}
				elseif($fabNatureId==3){
					if(isset($Amount[$this->_woven][$this->_finish][$poId][$countryId][$uom])){
						$Amount[$this->_woven][$this->_finish][$poId][$countryId][$uom]+=$amount_finish;
					}
					else{
						$Amount[$this->_woven][$this->_finish][$poId][$countryId][$uom]=$amount_finish;
					}
					if(isset($Amount[$this->_woven][$this->_grey][$poId][$countryId][$uom])){
						$Amount[$this->_woven][$this->_grey][$poId][$countryId][$uom]+=$amount_grey;
					}
					else{
						$Amount[$this->_woven][$this->_grey][$poId][$countryId][$uom]=$amount_grey;
					}
				}
			}
			elseif($level==$this->_By_OrderAndGmtsitem){
				if($fabNatureId==2){
					if(isset($Amount[$this->_knit][$this->_finish][$poId][$itemNumberId][$uom])){
						$Amount[$this->_knit][$this->_finish][$poId][$itemNumberId][$uom]+=$amount_finish;
					}
					else{
						$Amount[$this->_knit][$this->_finish][$poId][$itemNumberId][$uom]=$amount_finish;
					}
					if(isset($Amount[$this->_knit][$this->_grey][$poId][$itemNumberId][$uom])){
					$Amount[$this->_knit][$this->_grey][$poId][$itemNumberId][$uom]+=$amount_grey;
					}
					else{
						$Amount[$this->_knit][$this->_grey][$poId][$itemNumberId][$uom]=$amount_grey;
					}
				}
				elseif($fabNatureId==3){
					if(isset($Amount[$this->_woven][$this->_finish][$poId][$itemNumberId][$uom])){
						$Amount[$this->_woven][$this->_finish][$poId][$itemNumberId][$uom]+=$amount_finish;
					}
					else{
						$Amount[$this->_woven][$this->_finish][$poId][$itemNumberId][$uom]+=$amount_finish;
					}
					if(isset($Amount[$this->_woven][$this->_grey][$poId][$itemNumberId][$uom])){
						$Amount[$this->_woven][$this->_grey][$poId][$itemNumberId][$uom]+=$amount_grey;
					}
					else{
						$Amount[$this->_woven][$this->_grey][$poId][$itemNumberId][$uom]=$amount_grey;
					}
				}
			}
			elseif($level==$this->_By_OrderAndGmtscolor){
				if($fabNatureId==2){
					if(isset($Amount[$this->_knit][$this->_finish][$poId][$colorId][$uom])){
						$Amount[$this->_knit][$this->_finish][$poId][$colorId][$uom]+=$amount_finish;
					}
					else{
						$Amount[$this->_knit][$this->_finish][$poId][$colorId][$uom]=$amount_finish;
					}
					if(isset($Amount[$this->_knit][$this->_grey][$poId][$colorId][$uom])){
						$Amount[$this->_knit][$this->_grey][$poId][$colorId][$uom]+=$amount_grey;
					}
					else{
						$Amount[$this->_knit][$this->_grey][$poId][$colorId][$uom]=$amount_grey;
					}
				}
				elseif($fabNatureId==3){
					if(isset($Amount[$this->_woven][$this->_finish][$poId][$colorId][$uom])){
						$Amount[$this->_woven][$this->_finish][$poId][$colorId][$uom]+=$amount_finish;
					}
					else{
						$Amount[$this->_woven][$this->_finish][$poId][$colorId][$uom]=$amount_finish;
					}
					if(isset($Amount[$this->_woven][$this->_finish][$poId][$colorId][$uom])){
						$Amount[$this->_woven][$this->_grey][$poId][$colorId][$uom]+=$amount_grey;
					}
					else{
						$Amount[$this->_woven][$this->_grey][$poId][$colorId][$uom]=$amount_grey;
					}
				}
			}
			elseif($level==$this->_By_OrderAndGmtssize){
				if($fabNatureId==2){
					if(isset($Amount[$this->_knit][$this->_finish][$poId][$sizeId][$uom])){
						$Amount[$this->_knit][$this->_finish][$poId][$sizeId][$uom]+=$amount_finish;
					}
					else{
						$Amount[$this->_knit][$this->_finish][$poId][$sizeId][$uom]=$amount_finish;
					}
					if(isset($Amount[$this->_knit][$this->_grey][$poId][$sizeId][$uom])){
						$Amount[$this->_knit][$this->_grey][$poId][$sizeId][$uom]+=$amount_grey;
					}
					else{
						$Amount[$this->_knit][$this->_grey][$poId][$sizeId][$uom]=$amount_grey;
					}
				}
				elseif($fabNatureId==3){
					if(isset($Amount[$this->_woven][$this->_finish][$poId][$sizeId][$uom])){
						$Amount[$this->_woven][$this->_finish][$poId][$sizeId][$uom]+=$amount_finish;
					}
					else{
						$Amount[$this->_woven][$this->_finish][$poId][$sizeId][$uom]=$amount_finish;
					}
					if(isset($Amount[$this->_woven][$this->_grey][$poId][$sizeId][$uom])){
						$Amount[$this->_woven][$this->_grey][$poId][$sizeId][$uom]+=$amount_grey;
					}
					else{
						$Amount[$this->_woven][$this->_grey][$poId][$sizeId][$uom]=$amount_grey;
					}
				}
			}
			elseif($level==$this->_By_orderCountryAndGmtsitem){
				if($fabNatureId==2){
					if(isset($Amount[$this->_knit][$this->_finish][$poId][$countryId][$itemNumberId][$uom])){
						$Amount[$this->_knit][$this->_finish][$poId][$countryId][$itemNumberId][$uom]+=$amount_finish;
					}
					else{
						$Amount[$this->_knit][$this->_finish][$poId][$countryId][$itemNumberId][$uom]=$amount_finish;
					}
					if(isset($Amount[$this->_knit][$this->_grey][$poId][$countryId][$itemNumberId][$uom])){
						$Amount[$this->_knit][$this->_grey][$poId][$countryId][$itemNumberId][$uom]+=$amount_grey;
					}
					else{
						$Amount[$this->_knit][$this->_grey][$poId][$countryId][$itemNumberId][$uom]=$amount_grey;
					}
				}
				elseif($fabNatureId==3){
					if(isset($Amount[$this->_woven][$this->_finish][$poId][$countryId][$itemNumberId][$uom])){
						$Amount[$this->_woven][$this->_finish][$poId][$countryId][$itemNumberId][$uom]+=$amount_finish;
					}
					else{
						$Amount[$this->_woven][$this->_finish][$poId][$countryId][$itemNumberId][$uom]=$amount_finish;
					}
					if(isset($Amount[$this->_woven][$this->_grey][$poId][$countryId][$itemNumberId][$uom])){
						$Amount[$this->_woven][$this->_grey][$poId][$countryId][$itemNumberId][$uom]+=$amount_grey;
					}
					else{
						$Amount[$this->_woven][$this->_grey][$poId][$countryId][$itemNumberId][$uom]=$amount_grey;
					}
				}
			}
			elseif($level==$this->_By_OrderCountryAndGmtscolor){
				if($fabNatureId==2){
					if(isset($Amount[$this->_knit][$this->_finish][$poId][$countryId][$colorId][$uom])){
						$Amount[$this->_knit][$this->_finish][$poId][$countryId][$colorId][$uom]+=$amount_finish;
					}
					else{
						$Amount[$this->_knit][$this->_finish][$poId][$countryId][$colorId][$uom]=$amount_finish;

					}
					if(isset($Amount[$this->_knit][$this->_grey][$poId][$countryId][$colorId][$uom])){
						$Amount[$this->_knit][$this->_grey][$poId][$countryId][$colorId][$uom]+=$amount_grey;
					}
					else{
						$Amount[$this->_knit][$this->_grey][$poId][$countryId][$colorId][$uom]=$amount_grey;
					}
				}
				elseif($fabNatureId==3){
					if(isset($Amount[$this->_woven][$this->_finish][$poId][$countryId][$colorId][$uom])){
						$Amount[$this->_woven][$this->_finish][$poId][$countryId][$colorId][$uom]+=$amount_finish;
					}
					else{
						$Amount[$this->_woven][$this->_finish][$poId][$countryId][$colorId][$uom]=$amount_finish;
					}
					if(isset($Amount[$this->_woven][$this->_grey][$poId][$countryId][$colorId][$uom])){
						$Amount[$this->_woven][$this->_grey][$poId][$countryId][$colorId][$uom]+=$amount_grey;
					}
					else{
						$Amount[$this->_woven][$this->_grey][$poId][$countryId][$colorId][$uom]=$amount_grey;
					}
				}
			}
			elseif($level==$this->_By_OrderCountryAndGmtssize){
				if($fabNatureId==2){
					if(isset($Amount[$this->_knit][$this->_finish][$poId][$countryId][$sizeId][$uom])){
						$Amount[$this->_knit][$this->_finish][$poId][$countryId][$sizeId][$uom]+=$amount_finish;
					}
					else{
						$Amount[$this->_knit][$this->_finish][$poId][$countryId][$sizeId][$uom]=$amount_finish;
					}
					if(isset($Amount[$this->_knit][$this->_grey][$poId][$countryId][$sizeId][$uom])){
						$Amount[$this->_knit][$this->_grey][$poId][$countryId][$sizeId][$uom]+=$amount_grey;
					}
					else{
						$Amount[$this->_knit][$this->_grey][$poId][$countryId][$sizeId][$uom]=$amount_grey;
					}
				}
				elseif($fabNatureId==3){
					if(isset($Amount[$this->_woven][$this->_finish][$poId][$countryId][$sizeId][$uom])){
						$Amount[$this->_woven][$this->_finish][$poId][$countryId][$sizeId][$uom]+=$amount_finish;
					}
					else{
						$Amount[$this->_woven][$this->_finish][$poId][$countryId][$sizeId][$uom]=$amount_finish;
					}
					if(isset($Amount[$this->_woven][$this->_grey][$poId][$countryId][$sizeId][$uom])){
						$Amount[$this->_woven][$this->_grey][$poId][$countryId][$sizeId][$uom]+=$amount_grey;
					}
					else{
						$Amount[$this->_woven][$this->_grey][$poId][$countryId][$sizeId][$uom]=$amount_grey;
					}
				}
			}
			elseif($level==$this->_By_OrderGmtsitemAndGmtscolor){
				if($fabNatureId==2){
					if(isset($Amount[$this->_knit][$this->_finish][$poId][$itemNumberId][$colorId][$uom])){
						$Amount[$this->_knit][$this->_finish][$poId][$itemNumberId][$colorId][$uom]+=$amount_finish;
					}
					else{
						$Amount[$this->_knit][$this->_finish][$poId][$itemNumberId][$colorId][$uom]=$amount_finish;
					}
					if(isset($Amount[$this->_knit][$this->_grey][$poId][$itemNumberId][$colorId][$uom])){
						$Amount[$this->_knit][$this->_grey][$poId][$itemNumberId][$colorId][$uom]+=$amount_grey;
					}
					else{
						$Amount[$this->_knit][$this->_grey][$poId][$itemNumberId][$colorId][$uom]=$amount_grey;
					}
				}
				elseif($fabNatureId==3){
					if(isset($Amount[$this->_woven][$this->_finish][$poId][$itemNumberId][$colorId][$uom])){
						$Amount[$this->_woven][$this->_finish][$poId][$itemNumberId][$colorId][$uom]+=$amount_finish;
					}
					else{
						$Amount[$this->_woven][$this->_finish][$poId][$itemNumberId][$colorId][$uom]=$amount_finish;
					}
					if(isset($Amount[$this->_woven][$this->_grey][$poId][$itemNumberId][$colorId][$uom])){
						$Amount[$this->_woven][$this->_grey][$poId][$itemNumberId][$colorId][$uom]+=$amount_grey;
					}
					else{
						$Amount[$this->_woven][$this->_grey][$poId][$itemNumberId][$colorId][$uom]=$amount_grey;
					}
				}
			}
			elseif($level==$this->_By_OrderGmtsitemAndGmtssize){
				if($fabNatureId==2){
					if(isset($Amount[$this->_knit][$this->_finish][$poId][$itemNumberId][$sizeId][$uom])){
						$Amount[$this->_knit][$this->_finish][$poId][$itemNumberId][$sizeId][$uom]+=$amount_finish;
					}
					else{
						$Amount[$this->_knit][$this->_finish][$poId][$itemNumberId][$sizeId][$uom]=$amount_finish;
					}
					if(isset($Amount[$this->_knit][$this->_grey][$poId][$itemNumberId][$sizeId][$uom])){
						$Amount[$this->_knit][$this->_grey][$poId][$itemNumberId][$sizeId][$uom]+=$amount_grey;
					}
					else{
						$Amount[$this->_knit][$this->_grey][$poId][$itemNumberId][$sizeId][$uom]=$amount_grey;
					}
				}
				elseif($fabNatureId==3){
					if(isset($Amount[$this->_woven][$this->_finish][$poId][$itemNumberId][$sizeId][$uom])){
						$Amount[$this->_woven][$this->_finish][$poId][$itemNumberId][$sizeId][$uom]+=$amount_finish;
					}
					else{
						$Amount[$this->_woven][$this->_finish][$poId][$itemNumberId][$sizeId][$uom]=$amount_finish;
					}
					if(isset($Amount[$this->_woven][$this->_grey][$poId][$itemNumberId][$sizeId][$uom])){
						$Amount[$this->_woven][$this->_grey][$poId][$itemNumberId][$sizeId][$uom]+=$amount_grey;
					}
					else{
						$Amount[$this->_woven][$this->_grey][$poId][$itemNumberId][$sizeId][$uom]=$amount_grey;
					}
				}
			}
			elseif($level==$this->_By_OrderGmtscolorAndGmtssize){
				if($fabNatureId==2){
					if(isset($Amount[$this->_knit][$this->_finish][$poId][$colorId][$sizeId][$uom])){
						$Amount[$this->_knit][$this->_finish][$poId][$colorId][$sizeId][$uom]+=$amount_finish;
					}
					else{
						$Amount[$this->_knit][$this->_finish][$poId][$colorId][$sizeId][$uom]=$amount_finish;
					}
					if(isset($Amount[$this->_knit][$this->_grey][$poId][$colorId][$sizeId][$uom])){
						$Amount[$this->_knit][$this->_grey][$poId][$colorId][$sizeId][$uom]+=$amount_grey;
					}
					else{
						$Amount[$this->_knit][$this->_grey][$poId][$colorId][$sizeId][$uom]=$amount_grey;
					}
				}
				elseif($fabNatureId==3){
					if(isset($Amount[$this->_woven][$this->_finish][$poId][$colorId][$sizeId][$uom])){
						$Amount[$this->_woven][$this->_finish][$poId][$colorId][$sizeId][$uom]+=$amount_finish;
					}
					else{
						$Amount[$this->_woven][$this->_finish][$poId][$colorId][$sizeId][$uom]=$amount_finish;
					}
					if(isset($Amount[$this->_woven][$this->_grey][$poId][$colorId][$sizeId][$uom]))
					{
						$Amount[$this->_woven][$this->_grey][$poId][$colorId][$sizeId][$uom]+=$amount_grey;
					}
					else{
						$Amount[$this->_woven][$this->_grey][$poId][$colorId][$sizeId][$uom]=$amount_grey;
					}
				}
			}
			elseif($level==$this->_By_OrderGmtscolorAndBodypart){
				if($fabNatureId==2){
					if(isset($Amount[$this->_knit][$this->_finish][$poId][$colorId][$bodypartId][$uom])){
						$Amount[$this->_knit][$this->_finish][$poId][$colorId][$bodypartId][$uom]+=$amount_finish;
					}
					else{
						$Amount[$this->_knit][$this->_finish][$poId][$colorId][$bodypartId][$uom]=$amount_finish;
					}
					if(isset($Amount[$this->_knit][$this->_grey][$poId][$colorId][$bodypartId][$uom])){
						$Amount[$this->_knit][$this->_grey][$poId][$colorId][$bodypartId][$uom]+=$amount_grey;
					}
					else{
						$Amount[$this->_knit][$this->_grey][$poId][$colorId][$bodypartId][$uom]=$amount_grey;
					}
				}
				elseif($fabNatureId==3){
					if(isset($Amount[$this->_woven][$this->_finish][$poId][$colorId][$bodypartId][$uom])){
						$Amount[$this->_woven][$this->_finish][$poId][$colorId][$bodypartId][$uom]+=$amount_finish;
					}
					else{
						$Amount[$this->_woven][$this->_finish][$poId][$colorId][$bodypartId][$uom]=$amount_finish;
					}
					if(isset($Amount[$this->_woven][$this->_grey][$poId][$colorId][$bodypartId][$uom]))
					{
						$Amount[$this->_woven][$this->_grey][$poId][$colorId][$bodypartId][$uom]+=$amount_grey;
					}
					else{
						$Amount[$this->_woven][$this->_grey][$poId][$colorId][$bodypartId][$uom]=$amount_grey;
					}
				}
			}
			elseif($level==$this->_By_orderCountryGmtsitemAndGmtscolor){
				if($fabNatureId==2){
					if(isset($Amount[$this->_knit][$this->_finish][$poId][$countryId][$itemNumberId][$colorId][$uom])){
						$Amount[$this->_knit][$this->_finish][$poId][$countryId][$itemNumberId][$colorId][$uom]+=$amount_finish;
					}
					else{
						$Amount[$this->_knit][$this->_finish][$poId][$countryId][$itemNumberId][$colorId][$uom]=$amount_finish;
					}
					if(isset($Amount[$this->_knit][$this->_grey][$poId][$countryId][$itemNumberId][$colorId][$uom])){
						$Amount[$this->_knit][$this->_grey][$poId][$countryId][$itemNumberId][$colorId][$uom]+=$amount_grey;
					}
					else{
						$Amount[$this->_knit][$this->_grey][$poId][$countryId][$itemNumberId][$colorId][$uom]=$amount_grey;
					}
				}
				elseif($fabNatureId==3){
					if(isset($Amount[$this->_woven][$this->_finish][$poId][$countryId][$itemNumberId][$colorId][$uom])){
						$Amount[$this->_woven][$this->_finish][$poId][$countryId][$itemNumberId][$colorId][$uom]+=$amount_finish;
					}
					else{
						$Amount[$this->_woven][$this->_finish][$poId][$countryId][$itemNumberId][$colorId][$uom]=$amount_finish;
					}
					if(isset($Amount[$this->_woven][$this->_grey][$poId][$countryId][$itemNumberId][$colorId][$uom])){
						$Amount[$this->_woven][$this->_grey][$poId][$countryId][$itemNumberId][$colorId][$uom]+=$amount_grey;
					}
					else{
						$Amount[$this->_woven][$this->_grey][$poId][$countryId][$itemNumberId][$colorId][$uom]=$amount_grey;
					}
				}
			}
			elseif($level==$this->_By_orderCountryGmtsitemAndGmtssize){
				if($fabNatureId==2){
					if(isset($Amount[$this->_knit][$this->_finish][$poId][$countryId][$itemNumberId][$sizeId][$uom])){
						$Amount[$this->_knit][$this->_finish][$poId][$countryId][$itemNumberId][$sizeId][$uom]+=$amount_finish;
					}
					else{
						$Amount[$this->_knit][$this->_finish][$poId][$countryId][$itemNumberId][$sizeId][$uom]=$amount_finish;
					}
					if(isset($Amount[$this->_knit][$this->_grey][$poId][$countryId][$itemNumberId][$sizeId][$uom])){
						$Amount[$this->_knit][$this->_grey][$poId][$countryId][$itemNumberId][$sizeId][$uom]+=$amount_grey;
					}
					else{
						$Amount[$this->_knit][$this->_grey][$poId][$countryId][$itemNumberId][$sizeId][$uom]=$amount_grey;
					}
				}
				elseif($fabNatureId==3){
					if(isset($Amount[$this->_woven][$this->_finish][$poId][$countryId][$itemNumberId][$sizeId][$uom])){
						$Amount[$this->_woven][$this->_finish][$poId][$countryId][$itemNumberId][$sizeId][$uom]+=$amount_finish;
					}
					else{
						$Amount[$this->_woven][$this->_finish][$poId][$countryId][$itemNumberId][$sizeId][$uom]=$amount_finish;
					}
					if(isset($Amount[$this->_woven][$this->_grey][$poId][$countryId][$itemNumberId][$sizeId][$uom])){
						$Amount[$this->_woven][$this->_grey][$poId][$countryId][$itemNumberId][$sizeId][$uom]+=$amount_grey;
					}
					else{
						$Amount[$this->_woven][$this->_grey][$poId][$countryId][$itemNumberId][$sizeId][$uom]=$amount_grey;
					}
				}
			}
			elseif($level==$this->_By_orderCountryGmtscolorAndGmtssize){
				if($fabNatureId==2){
					if(isset($Amount[$this->_knit][$this->_finish][$poId][$countryId][$colorId][$sizeId][$uom])){
						$Amount[$this->_knit][$this->_finish][$poId][$countryId][$colorId][$sizeId][$uom]+=$amount_finish;
					}
					else{
						$Amount[$this->_knit][$this->_finish][$poId][$countryId][$colorId][$sizeId][$uom]=$amount_finish;
					}
					if(isset($Amount[$this->_knit][$this->_grey][$poId][$countryId][$colorId][$sizeId][$uom])){
						$Amount[$this->_knit][$this->_grey][$poId][$countryId][$colorId][$sizeId][$uom]+=$amount_grey;
					}
					else{
						$Amount[$this->_knit][$this->_grey][$poId][$countryId][$colorId][$sizeId][$uom]=$amount_grey;
					}
				}
				elseif($fabNatureId==3){
					if(isset($Amount[$this->_woven][$this->_finish][$poId][$countryId][$colorId][$sizeId][$uom])){
						$Amount[$this->_woven][$this->_finish][$poId][$countryId][$colorId][$sizeId][$uom]+=$amount_finish;
					}
					else{
						$Amount[$this->_woven][$this->_finish][$poId][$countryId][$colorId][$sizeId][$uom]=$amount_finish;
					}
					if(isset($Amount[$this->_woven][$this->_grey][$poId][$countryId][$colorId][$sizeId][$uom])){
						$Amount[$this->_woven][$this->_grey][$poId][$countryId][$colorId][$sizeId][$uom]+=$amount_grey;
					}
					else{
						$Amount[$this->_woven][$this->_grey][$poId][$countryId][$colorId][$sizeId][$uom]=$amount_grey;
					}
				}
			}
			elseif($level==$this->_By_orderGmtsitemGmtscolorAndGmtssize){
				if($fabNatureId==2){
					if(isset($Amount[$this->_knit][$this->_finish][$poId][$itemNumberId][$colorId][$sizeId][$uom])){
						$Amount[$this->_knit][$this->_finish][$poId][$itemNumberId][$colorId][$sizeId][$uom]+=$amount_finish;
					}
					else{
						$Amount[$this->_knit][$this->_finish][$poId][$itemNumberId][$colorId][$sizeId][$uom]=$amount_finish;
					}
					if(isset($Amount[$this->_knit][$this->_grey][$poId][$itemNumberId][$colorId][$sizeId][$uom])){
						$Amount[$this->_knit][$this->_grey][$poId][$itemNumberId][$colorId][$sizeId][$uom]+=$amount_grey;
					}
					else{
						$Amount[$this->_knit][$this->_grey][$poId][$itemNumberId][$colorId][$sizeId][$uom]=$amount_grey;
					}
				}
				elseif($fabNatureId==3){
					if(isset($Amount[$this->_woven][$this->_finish][$poId][$itemNumberId][$colorId][$sizeId][$uom])){
						$Amount[$this->_woven][$this->_finish][$poId][$itemNumberId][$colorId][$sizeId][$uom]+=$amount_finish;
					}
					else{
						$Amount[$this->_woven][$this->_finish][$poId][$itemNumberId][$colorId][$sizeId][$uom]=$amount_finish;
					}
					if(isset($Amount[$this->_woven][$this->_grey][$poId][$itemNumberId][$colorId][$sizeId][$uom])){
						$Amount[$this->_woven][$this->_grey][$poId][$itemNumberId][$colorId][$sizeId][$uom]+=$amount_grey;
					}
					else{
						$Amount[$this->_woven][$this->_grey][$poId][$itemNumberId][$colorId][$sizeId][$uom]=$amount_grey;
					}
				}
			}
			elseif($level==$this->_By_orderCountryGmtsitemGmtscolorAndGmtssize){
				if($fabNatureId==2){
					if(isset($Amount[$this->_knit][$this->_finish][$poId][$countryId][$itemNumberId][$colorId][$sizeId][$uom])){
						$Amount[$this->_knit][$this->_finish][$poId][$countryId][$itemNumberId][$colorId][$sizeId][$uom]+=$amount_finish;
					}
					else{
						$Amount[$this->_knit][$this->_finish][$poId][$countryId][$itemNumberId][$colorId][$sizeId][$uom]=$amount_finish;
					}
					if(isset($Amount[$this->_knit][$this->_grey][$poId][$countryId][$itemNumberId][$colorId][$sizeId][$uom])){
						$Amount[$this->_knit][$this->_grey][$poId][$countryId][$itemNumberId][$colorId][$sizeId][$uom]+=$amount_grey;
					}
					else{
						$Amount[$this->_knit][$this->_grey][$poId][$countryId][$itemNumberId][$colorId][$sizeId][$uom]=$amount_grey;
					}
				}
				elseif($fabNatureId==3){
					if(isset($Amount[$this->_woven][$this->_finish][$poId][$countryId][$itemNumberId][$colorId][$sizeId][$uom])){
						$Amount[$this->_woven][$this->_finish][$poId][$countryId][$itemNumberId][$colorId][$sizeId][$uom]+=$amount_finish;
					}
					else{
						$Amount[$this->_woven][$this->_finish][$poId][$countryId][$itemNumberId][$colorId][$sizeId][$uom]=$amount_finish;
					}
					if(isset($Amount[$this->_woven][$this->_grey][$poId][$countryId][$itemNumberId][$colorId][$sizeId][$uom])){
						$Amount[$this->_woven][$this->_grey][$poId][$countryId][$itemNumberId][$colorId][$sizeId][$uom]+=$amount_grey;
					}
					else{
						$Amount[$this->_woven][$this->_grey][$poId][$countryId][$itemNumberId][$colorId][$sizeId][$uom]=$amount_grey;
					}
				}
			}
			elseif($level==$this->_By_OrderAndFabriccostid){
				if($fabNatureId==2){
					if(isset($Amount[$this->_knit][$this->_finish][$poId][$preCostDtlsId][$uom])){
						$Amount[$this->_knit][$this->_finish][$poId][$preCostDtlsId][$uom]+=$amount_finish;
					}
					else{
						$Amount[$this->_knit][$this->_finish][$poId][$preCostDtlsId][$uom]=$amount_finish;
					}
					if(isset($Amount[$this->_knit][$this->_grey][$poId][$preCostDtlsId][$uom])){
						$Amount[$this->_knit][$this->_grey][$poId][$preCostDtlsId][$uom]+=$amount_grey;
					}
					else{
						$Amount[$this->_knit][$this->_grey][$poId][$preCostDtlsId][$uom]=$amount_grey;
					}
				}
				elseif($fabNatureId==3){
					if(isset($Amount[$this->_woven][$this->_finish][$poId][$preCostDtlsId][$uom])){
						$Amount[$this->_woven][$this->_finish][$poId][$preCostDtlsId][$uom]+=$amount_finish;
					}
					else{
						$Amount[$this->_woven][$this->_finish][$poId][$preCostDtlsId][$uom]=$amount_finish;
					}
					if(isset($Amount[$this->_woven][$this->_grey][$poId][$preCostDtlsId][$uom])){
						$Amount[$this->_woven][$this->_grey][$poId][$preCostDtlsId][$uom]+=$amount_grey;
					}
					else{
						$Amount[$this->_woven][$this->_grey][$poId][$preCostDtlsId][$uom]=$amount_grey;
					}
				}
			}
			elseif($level==$this->_By_OrderFabriccostidAndGmtscolor){
				if($fabNatureId==2){
					if(isset($Amount[$this->_knit][$this->_finish][$poId][$preCostDtlsId][$colorId][$uom])){
						$Amount[$this->_knit][$this->_finish][$poId][$preCostDtlsId][$colorId][$uom]+=$amount_finish;
					}
					else{
						$Amount[$this->_knit][$this->_finish][$poId][$preCostDtlsId][$colorId][$uom]=$amount_finish;
					}
					if(isset($Amount[$this->_knit][$this->_grey][$poId][$preCostDtlsId][$colorId][$uom])){
						$Amount[$this->_knit][$this->_grey][$poId][$preCostDtlsId][$colorId][$uom]+=$amount_grey;
					}
					else{
						$Amount[$this->_knit][$this->_grey][$poId][$preCostDtlsId][$colorId][$uom]=$amount_grey;
					}
				}
				elseif($fabNatureId==3){
					if(isset($Amount[$this->_woven][$this->_finish][$poId][$preCostDtlsId][$colorId][$uom])){
						$Amount[$this->_woven][$this->_finish][$poId][$preCostDtlsId][$colorId][$uom]+=$amount_finish;
					}
					else{
						$Amount[$this->_woven][$this->_finish][$poId][$preCostDtlsId][$colorId][$uom]=$amount_finish;
					}
					if(isset($Amount[$this->_woven][$this->_grey][$poId][$preCostDtlsId][$colorId][$uom])){
						$Amount[$this->_woven][$this->_grey][$poId][$preCostDtlsId][$colorId][$uom]+=$amount_grey;
					}
					else{
						$Amount[$this->_woven][$this->_grey][$poId][$preCostDtlsId][$colorId][$uom]=$amount_grey;
					}
				}
			}
			elseif($level==$this->_By_OrderFabriccostidGmtscolorAndDiaWidth){
				if($fabNatureId==2){
					if(isset($Amount[$this->_knit][$this->_finish][$poId][$preCostDtlsId][$colorId][$diaWidth][$uom])){
						$Amount[$this->_knit][$this->_finish][$poId][$preCostDtlsId][$colorId][$diaWidth][$uom]+=$amount_finish;
					}
					else{
						$Amount[$this->_knit][$this->_finish][$poId][$preCostDtlsId][$colorId][$diaWidth][$uom]=$amount_finish;
					}
					if(isset($Amount[$this->_knit][$this->_grey][$poId][$preCostDtlsId][$colorId][$diaWidth][$uom])){
						$Amount[$this->_knit][$this->_grey][$poId][$preCostDtlsId][$colorId][$diaWidth][$uom]+=$amount_grey;
					}
					else{
						$Amount[$this->_knit][$this->_grey][$poId][$preCostDtlsId][$colorId][$diaWidth][$uom]=$amount_grey;
					}
				}
				elseif($fabNatureId==3){
					if(isset($Amount[$this->_woven][$this->_finish][$poId][$preCostDtlsId][$colorId][$diaWidth][$uom])){
						$Amount[$this->_woven][$this->_finish][$poId][$preCostDtlsId][$colorId][$diaWidth][$uom]+=$amount_finish;
					}
					else{
						$Amount[$this->_woven][$this->_finish][$poId][$preCostDtlsId][$colorId][$diaWidth][$uom]=$amount_finish;
					}
					if(isset($Amount[$this->_woven][$this->_grey][$poId][$preCostDtlsId][$colorId][$diaWidth][$uom]))
					{
						$Amount[$this->_woven][$this->_grey][$poId][$preCostDtlsId][$colorId][$diaWidth][$uom]+=$amount_grey;
					}
					else{
						$Amount[$this->_woven][$this->_grey][$poId][$preCostDtlsId][$colorId][$diaWidth][$uom]=$amount_grey;
					}
				}
			}
			elseif($level==$this->_By_OrderFabriccostidGmtscolorDiaWidthAndRemarks){
				if($fabNatureId==2){
					if(isset($Amount[$this->_knit][$this->_finish][$poId][$preCostDtlsId][$colorId][$diaWidth][$remarks][$uom])){
						$Amount[$this->_knit][$this->_finish][$poId][$preCostDtlsId][$colorId][$diaWidth][$remarks][$uom]+=$amount_finish;
					}
					else{
						$Amount[$this->_knit][$this->_finish][$poId][$preCostDtlsId][$colorId][$diaWidth][$remarks][$uom]=$amount_finish;
					}
					if(isset($Amount[$this->_knit][$this->_grey][$poId][$preCostDtlsId][$colorId][$diaWidth][$remarks][$uom])){
						$Amount[$this->_knit][$this->_grey][$poId][$preCostDtlsId][$colorId][$diaWidth][$remarks][$uom]+=$amount_grey;
					}
					else{
						$Amount[$this->_knit][$this->_grey][$poId][$preCostDtlsId][$colorId][$diaWidth][$remarks][$uom]=$amount_grey;
					}
				}
				elseif($fabNatureId==3){
					if(isset($Amount[$this->_woven][$this->_finish][$poId][$preCostDtlsId][$colorId][$diaWidth][$remarks][$uom])){
						$Amount[$this->_woven][$this->_finish][$poId][$preCostDtlsId][$colorId][$diaWidth][$remarks][$uom]+=$amount_finish;
					}
					else{
						$Amount[$this->_woven][$this->_finish][$poId][$preCostDtlsId][$colorId][$diaWidth][$remarks][$uom]=$amount_finish;
					}
					if(isset($Amount[$this->_woven][$this->_grey][$poId][$preCostDtlsId][$colorId][$diaWidth][$remarks][$uom]))
					{
						$Amount[$this->_woven][$this->_grey][$poId][$preCostDtlsId][$colorId][$diaWidth][$remarks][$uom]+=$amount_grey;
					}
					else{
						$Amount[$this->_woven][$this->_grey][$poId][$preCostDtlsId][$colorId][$diaWidth][$remarks][$uom]=$amount_grey;
					}
				}
			}


			//===================
			elseif($level==$this->_By_OrderCountryAndFabriccostid){
				if($fabNatureId==2){
					if(isset($Amount[$this->_knit][$this->_finish][$poId][$countryId][$preCostDtlsId][$uom])){
						$Amount[$this->_knit][$this->_finish][$poId][$countryId][$preCostDtlsId][$uom]+=$amount_finish;
					}
					else{
						$Amount[$this->_knit][$this->_finish][$poId][$countryId][$preCostDtlsId][$uom]=$amount_finish;
					}
					if(isset($Amount[$this->_knit][$this->_grey][$poId][$countryId][$preCostDtlsId][$uom])){
						$Amount[$this->_knit][$this->_grey][$poId][$countryId][$preCostDtlsId][$uom]+=$amount_grey;
					}
					else{
						$Amount[$this->_knit][$this->_grey][$poId][$countryId][$preCostDtlsId][$uom]=$amount_grey;
					}
				}
				elseif($fabNatureId==3){
					if(isset($Amount[$this->_woven][$this->_finish][$poId][$countryId][$preCostDtlsId][$uom])){
						$Amount[$this->_woven][$this->_finish][$poId][$countryId][$preCostDtlsId][$uom]+=$amount_finish;
					}
					else{
						$Amount[$this->_woven][$this->_finish][$poId][$countryId][$preCostDtlsId][$uom]=$amount_finish;
					}
					if(isset($Amount[$this->_woven][$this->_grey][$poId][$countryId][$preCostDtlsId][$uom])){
						$Amount[$this->_woven][$this->_grey][$poId][$countryId][$preCostDtlsId][$uom]+=$amount_grey;
					}
					else{
						$Amount[$this->_woven][$this->_grey][$poId][$countryId][$preCostDtlsId][$uom]=$amount_grey;
					}
				}
			}
			elseif($level==$this->_By_Fabriccostid){
				if($fabNatureId==2){
					if(isset($Amount[$this->_knit][$this->_finish][$preCostDtlsId][$uom])){
						$Amount[$this->_knit][$this->_finish][$preCostDtlsId][$uom]+=$amount_finish;
					}
					else{
						$Amount[$this->_knit][$this->_finish][$preCostDtlsId][$uom]=$amount_finish;
					}
					if(isset($Amount[$this->_knit][$this->_grey][$preCostDtlsId][$uom])){
						$Amount[$this->_knit][$this->_grey][$preCostDtlsId][$uom]+=$amount_grey;
					}
					else{
						$Amount[$this->_knit][$this->_grey][$preCostDtlsId][$uom]=$amount_grey;
					}
				}
				elseif($fabNatureId==3){
					if(isset($Amount[$this->_woven][$this->_finish][$preCostDtlsId][$uom])){
						$Amount[$this->_woven][$this->_finish][$preCostDtlsId][$uom]+=$amount_finish;
					}
					else{
						$Amount[$this->_woven][$this->_finish][$preCostDtlsId][$uom]=$amount_finish;
					}
					if(isset($Amount[$this->_woven][$this->_grey][$preCostDtlsId][$uom])){
						$Amount[$this->_woven][$this->_grey][$preCostDtlsId][$uom]+=$amount_grey;
					}
					else{
						$Amount[$this->_woven][$this->_grey][$preCostDtlsId][$uom]=$amount_grey;
					}
				}
			}
			else{
				return null;
			}
			
		}
		return $Amount;
	}
	
	private function _setQty_knitAndwoven_greyAndfinish_production($level){
		$jobNo='';
		$itemNumberId='';
		$planPutQnty=0;
		$bodypartId='';
		$requirment=0;
		$poId='';
		
		$Qty=array();
		foreach($this->_dataArray as $row)
		{
			$jobNo=$row['job_no'];
			$poId=$row['id'];
			$itemNumberId=$row['item_number_id'];
			$colorId=$row['color_number_id'];
			$sizeId=$row['size_number_id'];
			$countryId=$row['country_id'];
			$planPutQnty=$row['plan_cut_qnty'];
			
			
			$bodypartId=$row['body_part_id'];
			$fabNatureId=$row['fab_nature_id'];
			$fabricSourceId=$row['fabric_source'];
			$cons=$row['cons'];
			$requirment=$row['requirment'];
			$uom=$row['uom'];
			$costingPerQty=$this->_costingPerQtyArr[$jobNo];
			$set_item_ratio=$this->_gmtsitemRatioArray[$jobNo][$itemNumberId];
			
			$reqqnty_finish =$this->_calculateQty($planPutQnty,$costingPerQty,$set_item_ratio,$cons);
			$reqqnty_grey =$this->_calculateQty($planPutQnty,$costingPerQty,$set_item_ratio,$requirment);
			
			if($level==$this->_By_Job){
				if($fabNatureId==2 && $fabricSourceId==1){
					if(isset($Qty[$this->_knit][$this->_finish][$jobNo][$uom])){
						$Qty[$this->_knit][$this->_finish][$jobNo][$uom]+=$reqqnty_finish;
					}
					else{
						$Qty[$this->_knit][$this->_finish][$jobNo][$uom]=$reqqnty_finish;
					}
					if(isset($Qty[$this->_knit][$this->_grey][$jobNo][$uom])){
						$Qty[$this->_knit][$this->_grey][$jobNo][$uom]+=$reqqnty_grey;
					}
					else{
						$Qty[$this->_knit][$this->_grey][$jobNo][$uom]=$reqqnty_grey;
					}
				}
				elseif($fabNatureId==3 && $fabricSourceId==1){
					if(isset($Qty[$this->_woven][$this->_finish][$jobNo][$uom])){
						$Qty[$this->_woven][$this->_finish][$jobNo][$uom]+=$reqqnty_finish;
					}
					else{
						$Qty[$this->_woven][$this->_finish][$jobNo][$uom]=$reqqnty_finish;
					}
					if(isset($Qty[$this->_woven][$this->_grey][$jobNo][$uom])){
						$Qty[$this->_woven][$this->_grey][$jobNo][$uom]+=$reqqnty_grey;
					}
					else{
						$Qty[$this->_woven][$this->_grey][$jobNo][$uom]=$reqqnty_grey;
					}
				}
			}
			elseif($level==$this->_By_Order){
				if($fabNatureId==2 && $fabricSourceId==1){
					if(isset($Qty[$this->_knit][$this->_finish][$poId][$uom])){
						$Qty[$this->_knit][$this->_finish][$poId][$uom]+=$reqqnty_finish;
					}
					else{
						$Qty[$this->_knit][$this->_finish][$poId][$uom]=$reqqnty_finish;
					}
					if(isset($Qty[$this->_knit][$this->_grey][$poId][$uom])){
						$Qty[$this->_knit][$this->_grey][$poId][$uom]+=$reqqnty_grey;
					}
					else{
						$Qty[$this->_knit][$this->_grey][$poId][$uom]=$reqqnty_grey;
					}
				}
				elseif($fabNatureId==3 && $fabricSourceId==1){
					if(isset($Qty[$this->_woven][$this->_finish][$poId][$uom])){
						$Qty[$this->_woven][$this->_finish][$poId][$uom]+=$reqqnty_finish;
					}
					else{
						$Qty[$this->_woven][$this->_finish][$poId][$uom]=$reqqnty_finish;
					}
					if(isset($Qty[$this->_woven][$this->_grey][$poId][$uom])){
						$Qty[$this->_woven][$this->_grey][$poId][$uom]+=$reqqnty_grey;
					}
					else{
						$Qty[$this->_woven][$this->_grey][$poId][$uom]=$reqqnty_grey;
					}
				}
			}
			elseif($level==$this->_By_OrderAndCountry){
				if($fabNatureId==2 && $fabricSourceId==1){
					if(isset($Qty[$this->_knit][$this->_finish][$poId][$countryId][$uom])){
						$Qty[$this->_knit][$this->_finish][$poId][$countryId][$uom]+=$reqqnty_finish;
					}
					else{
						$Qty[$this->_knit][$this->_finish][$poId][$countryId][$uom]=$reqqnty_finish;
					}
					if(isset($Qty[$this->_knit][$this->_grey][$poId][$countryId][$uom])){
						$Qty[$this->_knit][$this->_grey][$poId][$countryId][$uom]+=$reqqnty_grey;
					}
					else{
						$Qty[$this->_knit][$this->_grey][$poId][$countryId][$uom]=$reqqnty_grey;
					}
				}
				elseif($fabNatureId==3 && $fabricSourceId==1){
					if(isset($Qty[$this->_woven][$this->_finish][$poId][$countryId][$uom])){
						$Qty[$this->_woven][$this->_finish][$poId][$countryId][$uom]+=$reqqnty_finish;
					}
					else{
						$Qty[$this->_woven][$this->_finish][$poId][$countryId][$uom]=$reqqnty_finish;
					}
					if(isset($Qty[$this->_woven][$this->_grey][$poId][$countryId][$uom])){
						$Qty[$this->_woven][$this->_grey][$poId][$countryId][$uom]+=$reqqnty_grey;
					}
					else{
						$Qty[$this->_woven][$this->_grey][$poId][$countryId][$uom]=$reqqnty_grey;
					}
				}
			}
			elseif($level==$this->_By_OrderAndGmtsitem){
				if($fabNatureId==2 && $fabricSourceId==1){
					if(isset($Qty[$this->_knit][$this->_finish][$poId][$itemNumberId][$uom])){
						$Qty[$this->_knit][$this->_finish][$poId][$itemNumberId][$uom]+=$reqqnty_finish;
					}
					else{
						$Qty[$this->_knit][$this->_finish][$poId][$itemNumberId][$uom]=$reqqnty_finish;
					}
					if(isset($Qty[$this->_knit][$this->_grey][$poId][$itemNumberId][$uom])){
					$Qty[$this->_knit][$this->_grey][$poId][$itemNumberId][$uom]+=$reqqnty_grey;
					}
					else{
						$Qty[$this->_knit][$this->_grey][$poId][$itemNumberId][$uom]=$reqqnty_grey;
					}
				}
				elseif($fabNatureId==3 && $fabricSourceId==1){
					if(isset($Qty[$this->_woven][$this->_finish][$poId][$itemNumberId][$uom])){
						$Qty[$this->_woven][$this->_finish][$poId][$itemNumberId][$uom]+=$reqqnty_finish;
					}
					else{
						$Qty[$this->_woven][$this->_finish][$poId][$itemNumberId][$uom]+=$reqqnty_finish;
					}
					if(isset($Qty[$this->_woven][$this->_grey][$poId][$itemNumberId][$uom])){
						$Qty[$this->_woven][$this->_grey][$poId][$itemNumberId][$uom]+=$reqqnty_grey;
					}
					else{
						$Qty[$this->_woven][$this->_grey][$poId][$itemNumberId][$uom]=$reqqnty_grey;
					}
				}
			}
			elseif($level==$this->_By_OrderAndGmtscolor){
				if($fabNatureId==2 && $fabricSourceId==1){
					if(isset($Qty[$this->_knit][$this->_finish][$poId][$colorId][$uom])){
						$Qty[$this->_knit][$this->_finish][$poId][$colorId][$uom]+=$reqqnty_finish;
					}
					else{
						$Qty[$this->_knit][$this->_finish][$poId][$colorId][$uom]=$reqqnty_finish;
					}
					if(isset($Qty[$this->_knit][$this->_grey][$poId][$colorId][$uom])){
						$Qty[$this->_knit][$this->_grey][$poId][$colorId][$uom]+=$reqqnty_grey;
					}
					else{
						$Qty[$this->_knit][$this->_grey][$poId][$colorId][$uom]=$reqqnty_grey;
					}
				}
				elseif($fabNatureId==3 && $fabricSourceId==1){
					if(isset($Qty[$this->_woven][$this->_finish][$poId][$colorId][$uom])){
						$Qty[$this->_woven][$this->_finish][$poId][$colorId][$uom]+=$reqqnty_finish;
					}
					else{
						$Qty[$this->_woven][$this->_finish][$poId][$colorId][$uom]=$reqqnty_finish;
					}
					if(isset($Qty[$this->_woven][$this->_finish][$poId][$colorId][$uom])){
						$Qty[$this->_woven][$this->_grey][$poId][$colorId][$uom]+=$reqqnty_grey;
					}
					else{
						$Qty[$this->_woven][$this->_grey][$poId][$colorId][$uom]=$reqqnty_grey;
					}
				}
			}
			elseif($level==$this->_By_OrderAndGmtssize){
				if($fabNatureId==2 && $fabricSourceId==1){
					if(isset($Qty[$this->_knit][$this->_finish][$poId][$sizeId][$uom])){
						$Qty[$this->_knit][$this->_finish][$poId][$sizeId][$uom]+=$reqqnty_finish;
					}
					else{
						$Qty[$this->_knit][$this->_finish][$poId][$sizeId][$uom]=$reqqnty_finish;
					}
					if(isset($Qty[$this->_knit][$this->_grey][$poId][$sizeId][$uom])){
						$Qty[$this->_knit][$this->_grey][$poId][$sizeId][$uom]+=$reqqnty_grey;
					}
					else{
						$Qty[$this->_knit][$this->_grey][$poId][$sizeId][$uom]=$reqqnty_grey;
					}
				}
				elseif($fabNatureId==3 && $fabricSourceId==1){
					if(isset($Qty[$this->_woven][$this->_finish][$poId][$sizeId][$uom])){
						$Qty[$this->_woven][$this->_finish][$poId][$sizeId][$uom]+=$reqqnty_finish;
					}
					else{
						$Qty[$this->_woven][$this->_finish][$poId][$sizeId][$uom]=$reqqnty_finish;
					}
					if(isset($Qty[$this->_woven][$this->_grey][$poId][$sizeId][$uom])){
						$Qty[$this->_woven][$this->_grey][$poId][$sizeId][$uom]+=$reqqnty_grey;
					}
					else{
						$Qty[$this->_woven][$this->_grey][$poId][$sizeId][$uom]=$reqqnty_grey;
					}
				}
			}
			elseif($level==$this->_By_orderCountryAndGmtsitem){
				if($fabNatureId==2 && $fabricSourceId==1){
					if(isset($Qty[$this->_knit][$this->_finish][$poId][$countryId][$itemNumberId][$uom])){
						$Qty[$this->_knit][$this->_finish][$poId][$countryId][$itemNumberId][$uom]+=$reqqnty_finish;
					}
					else{
						$Qty[$this->_knit][$this->_finish][$poId][$countryId][$itemNumberId][$uom]=$reqqnty_finish;
					}
					if(isset($Qty[$this->_knit][$this->_grey][$poId][$countryId][$itemNumberId][$uom])){
						$Qty[$this->_knit][$this->_grey][$poId][$countryId][$itemNumberId][$uom]+=$reqqnty_grey;
					}
					else{
						$Qty[$this->_knit][$this->_grey][$poId][$countryId][$itemNumberId][$uom]=$reqqnty_grey;
					}
				}
				elseif($fabNatureId==3 && $fabricSourceId==1){
					if(isset($Qty[$this->_woven][$this->_finish][$poId][$countryId][$itemNumberId][$uom])){
						$Qty[$this->_woven][$this->_finish][$poId][$countryId][$itemNumberId][$uom]+=$reqqnty_finish;
					}
					else{
						$Qty[$this->_woven][$this->_finish][$poId][$countryId][$itemNumberId][$uom]=$reqqnty_finish;
					}
					if(isset($Qty[$this->_woven][$this->_grey][$poId][$countryId][$itemNumberId][$uom])){
						$Qty[$this->_woven][$this->_grey][$poId][$countryId][$itemNumberId][$uom]+=$reqqnty_grey;
					}
					else{
						$Qty[$this->_woven][$this->_grey][$poId][$countryId][$itemNumberId][$uom]=$reqqnty_grey;
					}
				}
			}
			elseif($level==$this->_By_OrderCountryAndGmtscolor){
				if($fabNatureId==2 && $fabricSourceId==1){
					if(isset($Qty[$this->_knit][$this->_finish][$poId][$countryId][$colorId][$uom])){
						$Qty[$this->_knit][$this->_finish][$poId][$countryId][$colorId][$uom]+=$reqqnty_finish;
					}
					else{
						$Qty[$this->_knit][$this->_finish][$poId][$countryId][$colorId][$uom]=$reqqnty_finish;

					}
					if(isset($Qty[$this->_knit][$this->_grey][$poId][$countryId][$colorId][$uom])){
						$Qty[$this->_knit][$this->_grey][$poId][$countryId][$colorId][$uom]+=$reqqnty_grey;
					}
					else{
						$Qty[$this->_knit][$this->_grey][$poId][$countryId][$colorId][$uom]=$reqqnty_grey;
					}
				}
				elseif($fabNatureId==3 && $fabricSourceId==1){
					if(isset($Qty[$this->_woven][$this->_finish][$poId][$countryId][$colorId][$uom])){
						$Qty[$this->_woven][$this->_finish][$poId][$countryId][$colorId][$uom]+=$reqqnty_finish;
					}
					else{
						$Qty[$this->_woven][$this->_finish][$poId][$countryId][$colorId][$uom]=$reqqnty_finish;
					}
					if(isset($Qty[$this->_woven][$this->_grey][$poId][$countryId][$colorId][$uom])){
						$Qty[$this->_woven][$this->_grey][$poId][$countryId][$colorId][$uom]+=$reqqnty_grey;
					}
					else{
						$Qty[$this->_woven][$this->_grey][$poId][$countryId][$colorId][$uom]=$reqqnty_grey;
					}
				}
			}
			elseif($level==$this->_By_OrderCountryAndGmtssize){
				if($fabNatureId==2 && $fabricSourceId==1){
					if(isset($Qty[$this->_knit][$this->_finish][$poId][$countryId][$sizeId][$uom])){
						$Qty[$this->_knit][$this->_finish][$poId][$countryId][$sizeId][$uom]+=$reqqnty_finish;
					}
					else{
						$Qty[$this->_knit][$this->_finish][$poId][$countryId][$sizeId][$uom]=$reqqnty_finish;
					}
					if(isset($Qty[$this->_knit][$this->_grey][$poId][$countryId][$sizeId][$uom])){
						$Qty[$this->_knit][$this->_grey][$poId][$countryId][$sizeId][$uom]+=$reqqnty_grey;
					}
					else{
						$Qty[$this->_knit][$this->_grey][$poId][$countryId][$sizeId][$uom]=$reqqnty_grey;
					}
				}
				elseif($fabNatureId==3 && $fabricSourceId==1){
					if(isset($Qty[$this->_woven][$this->_finish][$poId][$countryId][$sizeId][$uom])){
						$Qty[$this->_woven][$this->_finish][$poId][$countryId][$sizeId][$uom]+=$reqqnty_finish;
					}
					else{
						$Qty[$this->_woven][$this->_finish][$poId][$countryId][$sizeId][$uom]=$reqqnty_finish;
					}
					if(isset($Qty[$this->_woven][$this->_grey][$poId][$countryId][$sizeId][$uom])){
						$Qty[$this->_woven][$this->_grey][$poId][$countryId][$sizeId][$uom]+=$reqqnty_grey;
					}
					else{
						$Qty[$this->_woven][$this->_grey][$poId][$countryId][$sizeId][$uom]=$reqqnty_grey;
					}
				}
			}
			elseif($level==$this->_By_OrderGmtsitemAndGmtscolor){
				if($fabNatureId==2 && $fabricSourceId==1){
					if(isset($Qty[$this->_knit][$this->_finish][$poId][$itemNumberId][$colorId][$uom])){
						$Qty[$this->_knit][$this->_finish][$poId][$itemNumberId][$colorId][$uom]+=$reqqnty_finish;
					}
					else{
						$Qty[$this->_knit][$this->_finish][$poId][$itemNumberId][$colorId][$uom]=$reqqnty_finish;
					}
					if(isset($Qty[$this->_knit][$this->_grey][$poId][$itemNumberId][$colorId][$uom])){
						$Qty[$this->_knit][$this->_grey][$poId][$itemNumberId][$colorId][$uom]+=$reqqnty_grey;
					}
					else{
						$Qty[$this->_knit][$this->_grey][$poId][$itemNumberId][$colorId][$uom]=$reqqnty_grey;
					}
				}
				elseif($fabNatureId==3 && $fabricSourceId==1){
					if(isset($Qty[$this->_woven][$this->_finish][$poId][$itemNumberId][$colorId][$uom])){
						$Qty[$this->_woven][$this->_finish][$poId][$itemNumberId][$colorId][$uom]+=$reqqnty_finish;
					}
					else{
						$Qty[$this->_woven][$this->_finish][$poId][$itemNumberId][$colorId][$uom]=$reqqnty_finish;
					}
					if(isset($Qty[$this->_woven][$this->_grey][$poId][$itemNumberId][$colorId][$uom])){
						$Qty[$this->_woven][$this->_grey][$poId][$itemNumberId][$colorId][$uom]+=$reqqnty_grey;
					}
					else{
						$Qty[$this->_woven][$this->_grey][$poId][$itemNumberId][$colorId][$uom]=$reqqnty_grey;
					}
				}
			}
			elseif($level==$this->_By_OrderGmtsitemAndGmtssize){
				if($fabNatureId==2 && $fabricSourceId==1){
					if(isset($Qty[$this->_knit][$this->_finish][$poId][$itemNumberId][$sizeId][$uom])){
						$Qty[$this->_knit][$this->_finish][$poId][$itemNumberId][$sizeId][$uom]+=$reqqnty_finish;
					}
					else{
						$Qty[$this->_knit][$this->_finish][$poId][$itemNumberId][$sizeId][$uom]=$reqqnty_finish;
					}
					if(isset($Qty[$this->_knit][$this->_grey][$poId][$itemNumberId][$sizeId][$uom])){
						$Qty[$this->_knit][$this->_grey][$poId][$itemNumberId][$sizeId][$uom]+=$reqqnty_grey;
					}
					else{
						$Qty[$this->_knit][$this->_grey][$poId][$itemNumberId][$sizeId][$uom]=$reqqnty_grey;
					}
				}
				elseif($fabNatureId==3 && $fabricSourceId==1){
					if(isset($Qty[$this->_woven][$this->_finish][$poId][$itemNumberId][$sizeId][$uom])){
						$Qty[$this->_woven][$this->_finish][$poId][$itemNumberId][$sizeId][$uom]+=$reqqnty_finish;
					}
					else{
						$Qty[$this->_woven][$this->_finish][$poId][$itemNumberId][$sizeId][$uom]=$reqqnty_finish;
					}
					if(isset($Qty[$this->_woven][$this->_grey][$poId][$itemNumberId][$sizeId][$uom])){
						$Qty[$this->_woven][$this->_grey][$poId][$itemNumberId][$sizeId][$uom]+=$reqqnty_grey;
					}
					else{
						$Qty[$this->_woven][$this->_grey][$poId][$itemNumberId][$sizeId][$uom]=$reqqnty_grey;
					}
				}
			}
			elseif($level==$this->_By_OrderGmtscolorAndGmtssize){
				if($fabNatureId==2 && $fabricSourceId==1){
					if(isset($Qty[$this->_knit][$this->_finish][$poId][$colorId][$sizeId][$uom])){
						$Qty[$this->_knit][$this->_finish][$poId][$colorId][$sizeId][$uom]+=$reqqnty_finish;
					}
					else{
						$Qty[$this->_knit][$this->_finish][$poId][$colorId][$sizeId][$uom]=$reqqnty_finish;
					}
					if(isset($Qty[$this->_knit][$this->_grey][$poId][$colorId][$sizeId][$uom])){
						$Qty[$this->_knit][$this->_grey][$poId][$colorId][$sizeId][$uom]+=$reqqnty_grey;
					}
					else{
						$Qty[$this->_knit][$this->_grey][$poId][$colorId][$sizeId][$uom]=$reqqnty_grey;
					}
				}
				elseif($fabNatureId==3 && $fabricSourceId==1){
					if(isset($Qty[$this->_woven][$this->_finish][$poId][$colorId][$sizeId][$uom])){
						$Qty[$this->_woven][$this->_finish][$poId][$colorId][$sizeId][$uom]+=$reqqnty_finish;
					}
					else{
						$Qty[$this->_woven][$this->_finish][$poId][$colorId][$sizeId][$uom]=$reqqnty_finish;
					}
					if(isset($Qty[$this->_woven][$this->_grey][$poId][$colorId][$sizeId][$uom]))
					{
						$Qty[$this->_woven][$this->_grey][$poId][$colorId][$sizeId][$uom]+=$reqqnty_grey;
					}
					else{
						$Qty[$this->_woven][$this->_grey][$poId][$colorId][$sizeId][$uom]=$reqqnty_grey;
					}
				}
			}
			elseif($level==$this->_By_OrderGmtscolorAndBodypart){
				if($fabNatureId==2 && $fabricSourceId==1){
					if(isset($Qty[$this->_knit][$this->_finish][$poId][$colorId][$bodypartId][$uom])){
						$Qty[$this->_knit][$this->_finish][$poId][$colorId][$bodypartId][$uom]+=$reqqnty_finish;
					}
					else{
						$Qty[$this->_knit][$this->_finish][$poId][$colorId][$bodypartId][$uom]=$reqqnty_finish;
					}
					if(isset($Qty[$this->_knit][$this->_grey][$poId][$colorId][$bodypartId][$uom])){
						$Qty[$this->_knit][$this->_grey][$poId][$colorId][$bodypartId][$uom]+=$reqqnty_grey;
					}
					else{
						$Qty[$this->_knit][$this->_grey][$poId][$colorId][$bodypartId][$uom]=$reqqnty_grey;
					}
				}
				elseif($fabNatureId==3 && $fabricSourceId==1){
					if(isset($Qty[$this->_woven][$this->_finish][$poId][$colorId][$bodypartId][$uom])){
						$Qty[$this->_woven][$this->_finish][$poId][$colorId][$bodypartId][$uom]+=$reqqnty_finish;
					}
					else{
						$Qty[$this->_woven][$this->_finish][$poId][$colorId][$bodypartId][$uom]=$reqqnty_finish;
					}
					if(isset($Qty[$this->_woven][$this->_grey][$poId][$colorId][$bodypartId][$uom]))
					{
						$Qty[$this->_woven][$this->_grey][$poId][$colorId][$bodypartId][$uom]+=$reqqnty_grey;
					}
					else{
						$Qty[$this->_woven][$this->_grey][$poId][$colorId][$bodypartId][$uom]=$reqqnty_grey;
					}
				}
			}
			elseif($level==$this->_By_orderCountryGmtsitemAndGmtscolor){
				if($fabNatureId==2 && $fabricSourceId==1){
					if(isset($Qty[$this->_knit][$this->_finish][$poId][$countryId][$itemNumberId][$colorId][$uom])){
						$Qty[$this->_knit][$this->_finish][$poId][$countryId][$itemNumberId][$colorId][$uom]+=$reqqnty_finish;
					}
					else{
						$Qty[$this->_knit][$this->_finish][$poId][$countryId][$itemNumberId][$colorId][$uom]=$reqqnty_finish;
					}
					if(isset($Qty[$this->_knit][$this->_grey][$poId][$countryId][$itemNumberId][$colorId][$uom])){
						$Qty[$this->_knit][$this->_grey][$poId][$countryId][$itemNumberId][$colorId][$uom]+=$reqqnty_grey;
					}
					else{
						$Qty[$this->_knit][$this->_grey][$poId][$countryId][$itemNumberId][$colorId][$uom]=$reqqnty_grey;
					}
				}
				elseif($fabNatureId==3 && $fabricSourceId==1){
					if(isset($Qty[$this->_woven][$this->_finish][$poId][$countryId][$itemNumberId][$colorId][$uom])){
						$Qty[$this->_woven][$this->_finish][$poId][$countryId][$itemNumberId][$colorId][$uom]+=$reqqnty_finish;
					}
					else{
						$Qty[$this->_woven][$this->_finish][$poId][$countryId][$itemNumberId][$colorId][$uom]=$reqqnty_finish;
					}
					if(isset($Qty[$this->_woven][$this->_grey][$poId][$countryId][$itemNumberId][$colorId][$uom])){
						$Qty[$this->_woven][$this->_grey][$poId][$countryId][$itemNumberId][$colorId][$uom]+=$reqqnty_grey;
					}
					else{
						$Qty[$this->_woven][$this->_grey][$poId][$countryId][$itemNumberId][$colorId][$uom]=$reqqnty_grey;
					}
				}
			}
			elseif($level==$this->_By_orderCountryGmtsitemAndGmtssize){
				if($fabNatureId==2 && $fabricSourceId==1){
					if(isset($Qty[$this->_knit][$this->_finish][$poId][$countryId][$itemNumberId][$sizeId][$uom])){
						$Qty[$this->_knit][$this->_finish][$poId][$countryId][$itemNumberId][$sizeId][$uom]+=$reqqnty_finish;
					}
					else{
						$Qty[$this->_knit][$this->_finish][$poId][$countryId][$itemNumberId][$sizeId][$uom]=$reqqnty_finish;
					}
					if(isset($Qty[$this->_knit][$this->_grey][$poId][$countryId][$itemNumberId][$sizeId][$uom])){
						$Qty[$this->_knit][$this->_grey][$poId][$countryId][$itemNumberId][$sizeId][$uom]+=$reqqnty_grey;
					}
					else{
						$Qty[$this->_knit][$this->_grey][$poId][$countryId][$itemNumberId][$sizeId][$uom]=$reqqnty_grey;
					}
				}
				elseif($fabNatureId==3 && $fabricSourceId==1){
					if(isset($Qty[$this->_woven][$this->_finish][$poId][$countryId][$itemNumberId][$sizeId][$uom])){
						$Qty[$this->_woven][$this->_finish][$poId][$countryId][$itemNumberId][$sizeId][$uom]+=$reqqnty_finish;
					}
					else{
						$Qty[$this->_woven][$this->_finish][$poId][$countryId][$itemNumberId][$sizeId][$uom]=$reqqnty_finish;
					}
					if(isset($Qty[$this->_woven][$this->_grey][$poId][$countryId][$itemNumberId][$sizeId][$uom])){
						$Qty[$this->_woven][$this->_grey][$poId][$countryId][$itemNumberId][$sizeId][$uom]+=$reqqnty_grey;
					}
					else{
						$Qty[$this->_woven][$this->_grey][$poId][$countryId][$itemNumberId][$sizeId][$uom]=$reqqnty_grey;
					}
				}
			}
			elseif($level==$this->_By_orderCountryGmtscolorAndGmtssize){
				if($fabNatureId==2 && $fabricSourceId==1){
					if(isset($Qty[$this->_knit][$this->_finish][$poId][$countryId][$colorId][$sizeId][$uom])){
						$Qty[$this->_knit][$this->_finish][$poId][$countryId][$colorId][$sizeId][$uom]+=$reqqnty_finish;
					}
					else{
						$Qty[$this->_knit][$this->_finish][$poId][$countryId][$colorId][$sizeId][$uom]=$reqqnty_finish;
					}
					if(isset($Qty[$this->_knit][$this->_grey][$poId][$countryId][$colorId][$sizeId][$uom])){
						$Qty[$this->_knit][$this->_grey][$poId][$countryId][$colorId][$sizeId][$uom]+=$reqqnty_grey;
					}
					else{
						$Qty[$this->_knit][$this->_grey][$poId][$countryId][$colorId][$sizeId][$uom]=$reqqnty_grey;
					}
				}
				elseif($fabNatureId==3 && $fabricSourceId==1){
					if(isset($Qty[$this->_woven][$this->_finish][$poId][$countryId][$colorId][$sizeId][$uom])){
						$Qty[$this->_woven][$this->_finish][$poId][$countryId][$colorId][$sizeId][$uom]+=$reqqnty_finish;
					}
					else{
						$Qty[$this->_woven][$this->_finish][$poId][$countryId][$colorId][$sizeId][$uom]=$reqqnty_finish;
					}
					if(isset($Qty[$this->_woven][$this->_grey][$poId][$countryId][$colorId][$sizeId][$uom])){
						$Qty[$this->_woven][$this->_grey][$poId][$countryId][$colorId][$sizeId][$uom]+=$reqqnty_grey;
					}
					else{
						$Qty[$this->_woven][$this->_grey][$poId][$countryId][$colorId][$sizeId][$uom]=$reqqnty_grey;
					}
				}
			}
			elseif($level==$this->_By_orderGmtsitemGmtscolorAndGmtssize){
				if($fabNatureId==2 && $fabricSourceId==1){
					if(isset($Qty[$this->_knit][$this->_finish][$poId][$itemNumberId][$colorId][$sizeId][$uom])){
						$Qty[$this->_knit][$this->_finish][$poId][$itemNumberId][$colorId][$sizeId][$uom]+=$reqqnty_finish;
					}
					else{
						$Qty[$this->_knit][$this->_finish][$poId][$itemNumberId][$colorId][$sizeId][$uom]=$reqqnty_finish;
					}
					if(isset($Qty[$this->_knit][$this->_grey][$poId][$itemNumberId][$colorId][$sizeId][$uom])){
						$Qty[$this->_knit][$this->_grey][$poId][$itemNumberId][$colorId][$sizeId][$uom]+=$reqqnty_grey;
					}
					else{
						$Qty[$this->_knit][$this->_grey][$poId][$itemNumberId][$colorId][$sizeId][$uom]=$reqqnty_grey;
					}
				}
				elseif($fabNatureId==3 && $fabricSourceId==1){
					if(isset($Qty[$this->_woven][$this->_finish][$poId][$itemNumberId][$colorId][$sizeId][$uom])){
						$Qty[$this->_woven][$this->_finish][$poId][$itemNumberId][$colorId][$sizeId][$uom]+=$reqqnty_finish;
					}
					else{
						$Qty[$this->_woven][$this->_finish][$poId][$itemNumberId][$colorId][$sizeId][$uom]=$reqqnty_finish;
					}
					if(isset($Qty[$this->_woven][$this->_grey][$poId][$itemNumberId][$colorId][$sizeId][$uom])){
						$Qty[$this->_woven][$this->_grey][$poId][$itemNumberId][$colorId][$sizeId][$uom]+=$reqqnty_grey;
					}
					else{
						$Qty[$this->_woven][$this->_grey][$poId][$itemNumberId][$colorId][$sizeId][$uom]=$reqqnty_grey;
					}
				}
			}
			elseif($level==$this->_By_orderCountryGmtsitemGmtscolorAndGmtssize){
				if($fabNatureId==2 && $fabricSourceId==1){
					if(isset($Qty[$this->_knit][$this->_finish][$poId][$countryId][$itemNumberId][$colorId][$sizeId][$uom])){
						$Qty[$this->_knit][$this->_finish][$poId][$countryId][$itemNumberId][$colorId][$sizeId][$uom]+=$reqqnty_finish;
					}
					else{
						$Qty[$this->_knit][$this->_finish][$poId][$countryId][$itemNumberId][$colorId][$sizeId][$uom]=$reqqnty_finish;
					}
					if(isset($Qty[$this->_knit][$this->_grey][$poId][$countryId][$itemNumberId][$colorId][$sizeId][$uom])){
						$Qty[$this->_knit][$this->_grey][$poId][$countryId][$itemNumberId][$colorId][$sizeId][$uom]+=$reqqnty_grey;
					}
					else{
						$Qty[$this->_knit][$this->_grey][$poId][$countryId][$itemNumberId][$colorId][$sizeId][$uom]=$reqqnty_grey;
					}
				}
				elseif($fabNatureId==3 && $fabricSourceId==1){
					if(isset($Qty[$this->_woven][$this->_finish][$poId][$countryId][$itemNumberId][$colorId][$sizeId][$uom])){
						$Qty[$this->_woven][$this->_finish][$poId][$countryId][$itemNumberId][$colorId][$sizeId][$uom]+=$reqqnty_finish;
					}
					else{
						$Qty[$this->_woven][$this->_finish][$poId][$countryId][$itemNumberId][$colorId][$sizeId][$uom]=$reqqnty_finish;
					}
					if(isset($Qty[$this->_woven][$this->_grey][$poId][$countryId][$itemNumberId][$colorId][$sizeId][$uom])){
						$Qty[$this->_woven][$this->_grey][$poId][$countryId][$itemNumberId][$colorId][$sizeId][$uom]+=$reqqnty_grey;
					}
					else{
						$Qty[$this->_woven][$this->_grey][$poId][$countryId][$itemNumberId][$colorId][$sizeId][$uom]=$reqqnty_grey;
					}
				}
			}
			else{
				return null;
			}
			
		}
		return $Qty;
	}
	
	private function _setQty_knitAndwoven_greyAndfinish_purchase($level){
		$jobNo='';
		$itemNumberId='';
		$planPutQnty=0;
		$bodypartId='';
		$requirment=0;
		$poId='';
		//$gmtsitemRatioArray=$this->_gmtsitemRatioArray;
		//$costingPerArray=$this->_costingPerArr;
		$Qty=array();
		foreach($this->_dataArray as $row)
		{
			/*$jobNo=$row[csf('job_no')];
			$poId=$row[csf('id')];
			$itemNumberId=$row[csf('item_number_id')];
			$countryId=$row[csf('country_id')];
			$colorId=$row[csf('color_number_id')];
			$sizeId=$row[csf('size_number_id')];
			$planPutQnty=$row[csf('plan_cut_qnty')];
			$bodypartId=$row[csf('body_part_id')];
			$fabNatureId=$row[csf('fab_nature_id')];
			$fabricSourceId=$row[csf('fabric_source')];
			$cons=$row[csf('cons')];
			$requirment=$row[csf('requirment')];*/
			
			$jobNo=$row['job_no'];
			$poId=$row['id'];
			$itemNumberId=$row['item_number_id'];
			$colorId=$row['color_number_id'];
			$sizeId=$row['size_number_id'];
			$countryId=$row['country_id'];
			$planPutQnty=$row['plan_cut_qnty'];
			
			
			$bodypartId=$row['body_part_id'];
			$fabNatureId=$row['fab_nature_id'];
			$fabricSourceId=$row['fabric_source'];
			$cons=$row['cons'];
			$requirment=$row['requirment'];
			$uom=$row['uom'];
			
			//$costingPerQty=$this->_costingPer($costingPerArray[$jobNo]);
			//$set_item_ratio=$gmtsitemRatioArray[$jobNo][$itemNumberId];
			//$costingPerQty=$this->_costingPer($this->_costingPerArr[$jobNo]);
			$costingPerQty=$this->_costingPerQtyArr[$jobNo];
			$set_item_ratio=$this->_gmtsitemRatioArray[$jobNo][$itemNumberId];
			
			$reqqnty_finish =$this->_calculateQty($planPutQnty,$costingPerQty,$set_item_ratio,$cons);
			$reqqnty_grey =$this->_calculateQty($planPutQnty,$costingPerQty,$set_item_ratio,$requirment);
			
			if($level==$this->_By_Job){
				if($fabNatureId==2 && $fabricSourceId==2){
					if(isset($Qty[$this->_knit][$this->_finish][$jobNo][$uom])){
						$Qty[$this->_knit][$this->_finish][$jobNo][$uom]+=$reqqnty_finish;
					}
					else{
						$Qty[$this->_knit][$this->_finish][$jobNo][$uom]=$reqqnty_finish;
					}
					if(isset($Qty[$this->_knit][$this->_grey][$jobNo][$uom])){
						$Qty[$this->_knit][$this->_grey][$jobNo][$uom]+=$reqqnty_grey;
					}
					else{
						$Qty[$this->_knit][$this->_grey][$jobNo][$uom]=$reqqnty_grey;
					}
				}
				elseif($fabNatureId==3 && $fabricSourceId==2){
					if(isset($Qty[$this->_woven][$this->_finish][$jobNo][$uom])){
						$Qty[$this->_woven][$this->_finish][$jobNo][$uom]+=$reqqnty_finish;
					}
					else{
						$Qty[$this->_woven][$this->_finish][$jobNo][$uom]=$reqqnty_finish;
					}
					if(isset($Qty[$this->_woven][$this->_grey][$jobNo][$uom])){
						$Qty[$this->_woven][$this->_grey][$jobNo][$uom]+=$reqqnty_grey;
					}
					else{
						$Qty[$this->_woven][$this->_grey][$jobNo][$uom]=$reqqnty_grey;
					}
				}
			}
			elseif($level==$this->_By_Order){
				if($fabNatureId==2 && $fabricSourceId==2){
					if(isset($Qty[$this->_knit][$this->_finish][$poId][$uom])){
						$Qty[$this->_knit][$this->_finish][$poId][$uom]+=$reqqnty_finish;
					}
					else{
						$Qty[$this->_knit][$this->_finish][$poId][$uom]=$reqqnty_finish;
					}
					if(isset($Qty[$this->_knit][$this->_grey][$poId][$uom])){
						$Qty[$this->_knit][$this->_grey][$poId][$uom]+=$reqqnty_grey;
					}
					else{
						$Qty[$this->_knit][$this->_grey][$poId][$uom]=$reqqnty_grey;
					}
				}
				elseif($fabNatureId==3 && $fabricSourceId==2){
					if(isset($Qty[$this->_woven][$this->_finish][$poId][$uom])){
						$Qty[$this->_woven][$this->_finish][$poId][$uom]+=$reqqnty_finish;
					}
					else{
						$Qty[$this->_woven][$this->_finish][$poId][$uom]=$reqqnty_finish;
					}
					if(isset($Qty[$this->_woven][$this->_grey][$poId][$uom])){
						$Qty[$this->_woven][$this->_grey][$poId][$uom]+=$reqqnty_grey;
					}
					else{
						$Qty[$this->_woven][$this->_grey][$poId][$uom]=$reqqnty_grey;
					}
				}
			}
			elseif($level==$this->_By_OrderAndCountry){
				if($fabNatureId==2 && $fabricSourceId==2){
					if(isset($Qty[$this->_knit][$this->_finish][$poId][$countryId][$uom])){
						$Qty[$this->_knit][$this->_finish][$poId][$countryId][$uom]+=$reqqnty_finish;
					}
					else{
						$Qty[$this->_knit][$this->_finish][$poId][$countryId][$uom]=$reqqnty_finish;
					}
					if(isset($Qty[$this->_knit][$this->_grey][$poId][$countryId][$uom])){
						$Qty[$this->_knit][$this->_grey][$poId][$countryId][$uom]+=$reqqnty_grey;
					}
					else{
						$Qty[$this->_knit][$this->_grey][$poId][$countryId][$uom]=$reqqnty_grey;
					}
				}
				elseif($fabNatureId==3 && $fabricSourceId==2){
					if(isset($Qty[$this->_woven][$this->_finish][$poId][$countryId][$uom])){
						$Qty[$this->_woven][$this->_finish][$poId][$countryId][$uom]+=$reqqnty_finish;
					}
					else{
						$Qty[$this->_woven][$this->_finish][$poId][$countryId][$uom]=$reqqnty_finish;
					}
					if(isset($Qty[$this->_woven][$this->_grey][$poId][$countryId][$uom])){
						$Qty[$this->_woven][$this->_grey][$poId][$countryId][$uom]+=$reqqnty_grey;
					}
					else{
						$Qty[$this->_woven][$this->_grey][$poId][$countryId][$uom]=$reqqnty_grey;
					}
				}
			}
			elseif($level==$this->_By_OrderAndGmtsitem){
				if($fabNatureId==2 && $fabricSourceId==2){
					if(isset($Qty[$this->_knit][$this->_finish][$poId][$itemNumberId][$uom])){
						$Qty[$this->_knit][$this->_finish][$poId][$itemNumberId][$uom]+=$reqqnty_finish;
					}
					else{
						$Qty[$this->_knit][$this->_finish][$poId][$itemNumberId][$uom]=$reqqnty_finish;
					}
					if(isset($Qty[$this->_knit][$this->_grey][$poId][$itemNumberId][$uom])){
					$Qty[$this->_knit][$this->_grey][$poId][$itemNumberId][$uom]+=$reqqnty_grey;
					}
					else{
						$Qty[$this->_knit][$this->_grey][$poId][$itemNumberId][$uom]=$reqqnty_grey;
					}
				}
				elseif($fabNatureId==3 && $fabricSourceId==2){
					if(isset($Qty[$this->_woven][$this->_finish][$poId][$itemNumberId][$uom])){
						$Qty[$this->_woven][$this->_finish][$poId][$itemNumberId][$uom]+=$reqqnty_finish;
					}
					else{

						$Qty[$this->_woven][$this->_finish][$poId][$itemNumberId][$uom]+=$reqqnty_finish;
					}
					if(isset($Qty[$this->_woven][$this->_grey][$poId][$itemNumberId][$uom])){
						$Qty[$this->_woven][$this->_grey][$poId][$itemNumberId][$uom]+=$reqqnty_grey;
					}
					else{
						$Qty[$this->_woven][$this->_grey][$poId][$itemNumberId][$uom]=$reqqnty_grey;
					}
				}
			}
			elseif($level==$this->_By_OrderAndGmtscolor){
				if($fabNatureId==2 && $fabricSourceId==2){
					if(isset($Qty[$this->_knit][$this->_finish][$poId][$colorId][$uom])){
						$Qty[$this->_knit][$this->_finish][$poId][$colorId][$uom]+=$reqqnty_finish;
					}
					else{
						$Qty[$this->_knit][$this->_finish][$poId][$colorId][$uom]=$reqqnty_finish;
					}
					if(isset($Qty[$this->_knit][$this->_grey][$poId][$colorId][$uom])){
						$Qty[$this->_knit][$this->_grey][$poId][$colorId][$uom]+=$reqqnty_grey;
					}
					else{
						$Qty[$this->_knit][$this->_grey][$poId][$colorId][$uom]=$reqqnty_grey;
					}
				}
				elseif($fabNatureId==3 && $fabricSourceId==2){
					if(isset($Qty[$this->_woven][$this->_finish][$poId][$colorId][$uom])){
						$Qty[$this->_woven][$this->_finish][$poId][$colorId][$uom]+=$reqqnty_finish;
					}
					else{
						$Qty[$this->_woven][$this->_finish][$poId][$colorId][$uom]=$reqqnty_finish;
					}
					if(isset($Qty[$this->_woven][$this->_finish][$poId][$colorId][$uom])){
						$Qty[$this->_woven][$this->_grey][$poId][$colorId][$uom]+=$reqqnty_grey;
					}
					else{
						$Qty[$this->_woven][$this->_grey][$poId][$colorId][$uom]=$reqqnty_grey;
					}
				}
			}
			elseif($level==$this->_By_OrderAndGmtssize){
				if($fabNatureId==2 && $fabricSourceId==2){
					if(isset($Qty[$this->_knit][$this->_finish][$poId][$sizeId][$uom])){
						$Qty[$this->_knit][$this->_finish][$poId][$sizeId][$uom]+=$reqqnty_finish;
					}
					else{
						$Qty[$this->_knit][$this->_finish][$poId][$sizeId][$uom]=$reqqnty_finish;
					}
					if(isset($Qty[$this->_knit][$this->_grey][$poId][$sizeId][$uom])){
						$Qty[$this->_knit][$this->_grey][$poId][$sizeId][$uom]+=$reqqnty_grey;
					}
					else{
						$Qty[$this->_knit][$this->_grey][$poId][$sizeId][$uom]=$reqqnty_grey;
					}
				}
				elseif($fabNatureId==3 && $fabricSourceId==2){
					if(isset($Qty[$this->_woven][$this->_finish][$poId][$sizeId][$uom])){
						$Qty[$this->_woven][$this->_finish][$poId][$sizeId][$uom]+=$reqqnty_finish;
					}
					else{
						$Qty[$this->_woven][$this->_finish][$poId][$sizeId][$uom]=$reqqnty_finish;
					}
					if(isset($Qty[$this->_woven][$this->_grey][$poId][$sizeId][$uom])){
						$Qty[$this->_woven][$this->_grey][$poId][$sizeId][$uom]+=$reqqnty_grey;
					}
					else{
						$Qty[$this->_woven][$this->_grey][$poId][$sizeId][$uom]=$reqqnty_grey;
					}
				}
			}
			elseif($level==$this->_By_orderCountryAndGmtsitem){
				if($fabNatureId==2 && $fabricSourceId==2){
					if(isset($Qty[$this->_knit][$this->_finish][$poId][$countryId][$itemNumberId][$uom])){
						$Qty[$this->_knit][$this->_finish][$poId][$countryId][$itemNumberId][$uom]+=$reqqnty_finish;
					}
					else{
						$Qty[$this->_knit][$this->_finish][$poId][$countryId][$itemNumberId][$uom]=$reqqnty_finish;
					}
					if(isset($Qty[$this->_knit][$this->_grey][$poId][$countryId][$itemNumberId][$uom])){
						$Qty[$this->_knit][$this->_grey][$poId][$countryId][$itemNumberId][$uom]+=$reqqnty_grey;
					}
					else{
						$Qty[$this->_knit][$this->_grey][$poId][$countryId][$itemNumberId][$uom]=$reqqnty_grey;
					}
				}
				elseif($fabNatureId==3 && $fabricSourceId==2){
					if(isset($Qty[$this->_woven][$this->_finish][$poId][$countryId][$itemNumberId][$uom])){
						$Qty[$this->_woven][$this->_finish][$poId][$countryId][$itemNumberId][$uom]+=$reqqnty_finish;
					}
					else{
						$Qty[$this->_woven][$this->_finish][$poId][$countryId][$itemNumberId][$uom]=$reqqnty_finish;
					}
					if(isset($Qty[$this->_woven][$this->_grey][$poId][$countryId][$itemNumberId][$uom])){
						$Qty[$this->_woven][$this->_grey][$poId][$countryId][$itemNumberId][$uom]+=$reqqnty_grey;
					}
					else{
						$Qty[$this->_woven][$this->_grey][$poId][$countryId][$itemNumberId][$uom]=$reqqnty_grey;
					}
				}
			}
			elseif($level==$this->_By_OrderCountryAndGmtscolor){
				if($fabNatureId==2 && $fabricSourceId==2){
					if(isset($Qty[$this->_knit][$this->_finish][$poId][$countryId][$colorId][$uom])){
						$Qty[$this->_knit][$this->_finish][$poId][$countryId][$colorId][$uom]+=$reqqnty_finish;
					}
					else{
						$Qty[$this->_knit][$this->_finish][$poId][$countryId][$colorId][$uom]=$reqqnty_finish;

					}
					if(isset($Qty[$this->_knit][$this->_grey][$poId][$countryId][$colorId][$uom])){
						$Qty[$this->_knit][$this->_grey][$poId][$countryId][$colorId][$uom]+=$reqqnty_grey;
					}
					else{
						$Qty[$this->_knit][$this->_grey][$poId][$countryId][$colorId][$uom]=$reqqnty_grey;
					}
				}
				elseif($fabNatureId==3 && $fabricSourceId==2){
					if(isset($Qty[$this->_woven][$this->_finish][$poId][$countryId][$colorId][$uom])){
						$Qty[$this->_woven][$this->_finish][$poId][$countryId][$colorId][$uom]+=$reqqnty_finish;
					}
					else{
						$Qty[$this->_woven][$this->_finish][$poId][$countryId][$colorId][$uom]=$reqqnty_finish;
					}
					if(isset($Qty[$this->_woven][$this->_grey][$poId][$countryId][$colorId][$uom])){
						$Qty[$this->_woven][$this->_grey][$poId][$countryId][$colorId][$uom]+=$reqqnty_grey;
					}
					else{
						$Qty[$this->_woven][$this->_grey][$poId][$countryId][$colorId][$uom]=$reqqnty_grey;
					}
				}
			}
			elseif($level==$this->_By_OrderCountryAndGmtssize){
				if($fabNatureId==2 && $fabricSourceId==2){
					if(isset($Qty[$this->_knit][$this->_finish][$poId][$countryId][$sizeId][$uom])){
						$Qty[$this->_knit][$this->_finish][$poId][$countryId][$sizeId][$uom]+=$reqqnty_finish;
					}
					else{
						$Qty[$this->_knit][$this->_finish][$poId][$countryId][$sizeId][$uom]=$reqqnty_finish;
					}
					if(isset($Qty[$this->_knit][$this->_grey][$poId][$countryId][$sizeId][$uom])){
						$Qty[$this->_knit][$this->_grey][$poId][$countryId][$sizeId][$uom]+=$reqqnty_grey;
					}
					else{
						$Qty[$this->_knit][$this->_grey][$poId][$countryId][$sizeId][$uom]=$reqqnty_grey;
					}
				}
				elseif($fabNatureId==3 && $fabricSourceId==2){
					if(isset($Qty[$this->_woven][$this->_finish][$poId][$countryId][$sizeId][$uom])){
						$Qty[$this->_woven][$this->_finish][$poId][$countryId][$sizeId][$uom]+=$reqqnty_finish;
					}
					else{
						$Qty[$this->_woven][$this->_finish][$poId][$countryId][$sizeId][$uom]=$reqqnty_finish;
					}
					if(isset($Qty[$this->_woven][$this->_grey][$poId][$countryId][$sizeId][$uom])){
						$Qty[$this->_woven][$this->_grey][$poId][$countryId][$sizeId][$uom]+=$reqqnty_grey;
					}
					else{
						$Qty[$this->_woven][$this->_grey][$poId][$countryId][$sizeId][$uom]=$reqqnty_grey;
					}
				}
			}
			elseif($level==$this->_By_OrderGmtsitemAndGmtscolor){
				if($fabNatureId==2 && $fabricSourceId==2){
					if(isset($Qty[$this->_knit][$this->_finish][$poId][$itemNumberId][$colorId][$uom])){
						$Qty[$this->_knit][$this->_finish][$poId][$itemNumberId][$colorId][$uom]+=$reqqnty_finish;
					}
					else{
						$Qty[$this->_knit][$this->_finish][$poId][$itemNumberId][$colorId][$uom]=$reqqnty_finish;
					}
					if(isset($Qty[$this->_knit][$this->_grey][$poId][$itemNumberId][$colorId][$uom])){
						$Qty[$this->_knit][$this->_grey][$poId][$itemNumberId][$colorId][$uom]+=$reqqnty_grey;
					}
					else{
						$Qty[$this->_knit][$this->_grey][$poId][$itemNumberId][$colorId][$uom]=$reqqnty_grey;
					}
				}
				elseif($fabNatureId==3 && $fabricSourceId==2){
					if(isset($Qty[$this->_woven][$this->_finish][$poId][$itemNumberId][$colorId][$uom])){
						$Qty[$this->_woven][$this->_finish][$poId][$itemNumberId][$colorId][$uom]+=$reqqnty_finish;
					}
					else{
						$Qty[$this->_woven][$this->_finish][$poId][$itemNumberId][$colorId][$uom]=$reqqnty_finish;
					}
					if(isset($Qty[$this->_woven][$this->_grey][$poId][$itemNumberId][$colorId][$uom])){
						$Qty[$this->_woven][$this->_grey][$poId][$itemNumberId][$colorId][$uom]+=$reqqnty_grey;
					}
					else{
						$Qty[$this->_woven][$this->_grey][$poId][$itemNumberId][$colorId][$uom]=$reqqnty_grey;
					}
				}
			}
			elseif($level==$this->_By_OrderGmtsitemAndGmtssize){
				if($fabNatureId==2 && $fabricSourceId==2){
					if(isset($Qty[$this->_knit][$this->_finish][$poId][$itemNumberId][$sizeId][$uom])){
						$Qty[$this->_knit][$this->_finish][$poId][$itemNumberId][$sizeId][$uom]+=$reqqnty_finish;
					}
					else{
						$Qty[$this->_knit][$this->_finish][$poId][$itemNumberId][$sizeId][$uom]=$reqqnty_finish;
					}
					if(isset($Qty[$this->_knit][$this->_grey][$poId][$itemNumberId][$sizeId][$uom])){
						$Qty[$this->_knit][$this->_grey][$poId][$itemNumberId][$sizeId][$uom]+=$reqqnty_grey;
					}
					else{
						$Qty[$this->_knit][$this->_grey][$poId][$itemNumberId][$sizeId][$uom]=$reqqnty_grey;
					}
				}
				elseif($fabNatureId==3 && $fabricSourceId==2){
					if(isset($Qty[$this->_woven][$this->_finish][$poId][$itemNumberId][$sizeId][$uom])){
						$Qty[$this->_woven][$this->_finish][$poId][$itemNumberId][$sizeId][$uom]+=$reqqnty_finish;
					}
					else{
						$Qty[$this->_woven][$this->_finish][$poId][$itemNumberId][$sizeId][$uom]=$reqqnty_finish;
					}
					if(isset($Qty[$this->_woven][$this->_grey][$poId][$itemNumberId][$sizeId][$uom])){
						$Qty[$this->_woven][$this->_grey][$poId][$itemNumberId][$sizeId][$uom]+=$reqqnty_grey;
					}
					else{
						$Qty[$this->_woven][$this->_grey][$poId][$itemNumberId][$sizeId][$uom]=$reqqnty_grey;
					}
				}
			}
			elseif($level==$this->_By_OrderGmtscolorAndGmtssize){
				if($fabNatureId==2 && $fabricSourceId==2){
					if(isset($Qty[$this->_knit][$this->_finish][$poId][$colorId][$sizeId][$uom])){
						$Qty[$this->_knit][$this->_finish][$poId][$colorId][$sizeId][$uom]+=$reqqnty_finish;
					}
					else{
						$Qty[$this->_knit][$this->_finish][$poId][$colorId][$sizeId][$uom]=$reqqnty_finish;
					}
					if(isset($Qty[$this->_knit][$this->_grey][$poId][$colorId][$sizeId][$uom])){
						$Qty[$this->_knit][$this->_grey][$poId][$colorId][$sizeId][$uom]+=$reqqnty_grey;
					}
					else{
						$Qty[$this->_knit][$this->_grey][$poId][$colorId][$sizeId][$uom]=$reqqnty_grey;
					}
				}
				elseif($fabNatureId==3 && $fabricSourceId==2){
					if(isset($Qty[$this->_woven][$this->_finish][$poId][$colorId][$sizeId][$uom])){
						$Qty[$this->_woven][$this->_finish][$poId][$colorId][$sizeId][$uom]+=$reqqnty_finish;
					}
					else{
						$Qty[$this->_woven][$this->_finish][$poId][$colorId][$sizeId][$uom]=$reqqnty_finish;
					}
					if(isset($Qty[$this->_woven][$this->_grey][$poId][$colorId][$sizeId][$uom]))
					{
						$Qty[$this->_woven][$this->_grey][$poId][$colorId][$sizeId][$uom]+=$reqqnty_grey;
					}
					else{
						$Qty[$this->_woven][$this->_grey][$poId][$colorId][$sizeId][$uom]=$reqqnty_grey;
					}
				}
			}
			elseif($level==$this->_By_OrderGmtscolorAndBodypart){
				if($fabNatureId==2 && $fabricSourceId==2){
					if(isset($Qty[$this->_knit][$this->_finish][$poId][$colorId][$bodypartId][$uom])){
						$Qty[$this->_knit][$this->_finish][$poId][$colorId][$bodypartId][$uom]+=$reqqnty_finish;
					}
					else{
						$Qty[$this->_knit][$this->_finish][$poId][$colorId][$bodypartId][$uom]=$reqqnty_finish;
					}
					if(isset($Qty[$this->_knit][$this->_grey][$poId][$colorId][$bodypartId][$uom])){
						$Qty[$this->_knit][$this->_grey][$poId][$colorId][$bodypartId][$uom]+=$reqqnty_grey;
					}
					else{
						$Qty[$this->_knit][$this->_grey][$poId][$colorId][$bodypartId][$uom]=$reqqnty_grey;
					}
				}
				elseif($fabNatureId==3 && $fabricSourceId==2){
					if(isset($Qty[$this->_woven][$this->_finish][$poId][$colorId][$bodypartId][$uom])){
						$Qty[$this->_woven][$this->_finish][$poId][$colorId][$bodypartId][$uom]+=$reqqnty_finish;
					}
					else{
						$Qty[$this->_woven][$this->_finish][$poId][$colorId][$bodypartId][$uom]=$reqqnty_finish;
					}
					if(isset($Qty[$this->_woven][$this->_grey][$poId][$colorId][$bodypartId][$uom]))
					{
						$Qty[$this->_woven][$this->_grey][$poId][$colorId][$bodypartId][$uom]+=$reqqnty_grey;
					}
					else{
						$Qty[$this->_woven][$this->_grey][$poId][$colorId][$bodypartId][$uom]=$reqqnty_grey;
					}
				}
			}
			elseif($level==$this->_By_orderCountryGmtsitemAndGmtscolor){
				if($fabNatureId==2 && $fabricSourceId==2){
					if(isset($Qty[$this->_knit][$this->_finish][$poId][$countryId][$itemNumberId][$colorId][$uom])){
						$Qty[$this->_knit][$this->_finish][$poId][$countryId][$itemNumberId][$colorId][$uom]+=$reqqnty_finish;
					}
					else{
						$Qty[$this->_knit][$this->_finish][$poId][$countryId][$itemNumberId][$colorId][$uom]=$reqqnty_finish;
					}
					if(isset($Qty[$this->_knit][$this->_grey][$poId][$countryId][$itemNumberId][$colorId][$uom])){
						$Qty[$this->_knit][$this->_grey][$poId][$countryId][$itemNumberId][$colorId][$uom]+=$reqqnty_grey;
					}
					else{
						$Qty[$this->_knit][$this->_grey][$poId][$countryId][$itemNumberId][$colorId][$uom]=$reqqnty_grey;
					}
				}
				elseif($fabNatureId==3 && $fabricSourceId==2){
					if(isset($Qty[$this->_woven][$this->_finish][$poId][$countryId][$itemNumberId][$colorId][$uom])){
						$Qty[$this->_woven][$this->_finish][$poId][$countryId][$itemNumberId][$colorId][$uom]+=$reqqnty_finish;
					}
					else{
						$Qty[$this->_woven][$this->_finish][$poId][$countryId][$itemNumberId][$colorId][$uom]=$reqqnty_finish;
					}
					if(isset($Qty[$this->_woven][$this->_grey][$poId][$countryId][$itemNumberId][$colorId][$uom])){
						$Qty[$this->_woven][$this->_grey][$poId][$countryId][$itemNumberId][$colorId][$uom]+=$reqqnty_grey;
					}
					else{
						$Qty[$this->_woven][$this->_grey][$poId][$countryId][$itemNumberId][$colorId][$uom]=$reqqnty_grey;
					}
				}
			}
			elseif($level==$this->_By_orderCountryGmtsitemAndGmtssize){
				if($fabNatureId==2 && $fabricSourceId==2){
					if(isset($Qty[$this->_knit][$this->_finish][$poId][$countryId][$itemNumberId][$sizeId][$uom])){
						$Qty[$this->_knit][$this->_finish][$poId][$countryId][$itemNumberId][$sizeId][$uom]+=$reqqnty_finish;
					}
					else{
						$Qty[$this->_knit][$this->_finish][$poId][$countryId][$itemNumberId][$sizeId][$uom]=$reqqnty_finish;
					}
					if(isset($Qty[$this->_knit][$this->_grey][$poId][$countryId][$itemNumberId][$sizeId][$uom])){
						$Qty[$this->_knit][$this->_grey][$poId][$countryId][$itemNumberId][$sizeId][$uom]+=$reqqnty_grey;
					}
					else{
						$Qty[$this->_knit][$this->_grey][$poId][$countryId][$itemNumberId][$sizeId][$uom]=$reqqnty_grey;
					}
				}
				elseif($fabNatureId==3 && $fabricSourceId==2){
					if(isset($Qty[$this->_woven][$this->_finish][$poId][$countryId][$itemNumberId][$sizeId][$uom])){
						$Qty[$this->_woven][$this->_finish][$poId][$countryId][$itemNumberId][$sizeId][$uom]+=$reqqnty_finish;
					}
					else{
						$Qty[$this->_woven][$this->_finish][$poId][$countryId][$itemNumberId][$sizeId][$uom]=$reqqnty_finish;
					}
					if(isset($Qty[$this->_woven][$this->_grey][$poId][$countryId][$itemNumberId][$sizeId][$uom])){
						$Qty[$this->_woven][$this->_grey][$poId][$countryId][$itemNumberId][$sizeId][$uom]+=$reqqnty_grey;
					}
					else{
						$Qty[$this->_woven][$this->_grey][$poId][$countryId][$itemNumberId][$sizeId][$uom]=$reqqnty_grey;
					}
				}
			}
			elseif($level==$this->_By_orderCountryGmtscolorAndGmtssize){
				if($fabNatureId==2 && $fabricSourceId==2){
					if(isset($Qty[$this->_knit][$this->_finish][$poId][$countryId][$colorId][$sizeId][$uom])){
						$Qty[$this->_knit][$this->_finish][$poId][$countryId][$colorId][$sizeId][$uom]+=$reqqnty_finish;
					}
					else{
						$Qty[$this->_knit][$this->_finish][$poId][$countryId][$colorId][$sizeId][$uom]=$reqqnty_finish;
					}
					if(isset($Qty[$this->_knit][$this->_grey][$poId][$countryId][$colorId][$sizeId][$uom])){
						$Qty[$this->_knit][$this->_grey][$poId][$countryId][$colorId][$sizeId][$uom]+=$reqqnty_grey;
					}
					else{
						$Qty[$this->_knit][$this->_grey][$poId][$countryId][$colorId][$sizeId][$uom]=$reqqnty_grey;
					}
				}
				elseif($fabNatureId==3 && $fabricSourceId==2){
					if(isset($Qty[$this->_woven][$this->_finish][$poId][$countryId][$colorId][$sizeId][$uom])){
						$Qty[$this->_woven][$this->_finish][$poId][$countryId][$colorId][$sizeId][$uom]+=$reqqnty_finish;
					}
					else{
						$Qty[$this->_woven][$this->_finish][$poId][$countryId][$colorId][$sizeId][$uom]=$reqqnty_finish;
					}
					if(isset($Qty[$this->_woven][$this->_grey][$poId][$countryId][$colorId][$sizeId][$uom])){
						$Qty[$this->_woven][$this->_grey][$poId][$countryId][$colorId][$sizeId][$uom]+=$reqqnty_grey;
					}
					else{
						$Qty[$this->_woven][$this->_grey][$poId][$countryId][$colorId][$sizeId][$uom]=$reqqnty_grey;
					}
				}
			}
			elseif($level==$this->_By_orderGmtsitemGmtscolorAndGmtssize){
				if($fabNatureId==2 && $fabricSourceId==2){
					if(isset($Qty[$this->_knit][$this->_finish][$poId][$itemNumberId][$colorId][$sizeId][$uom])){
						$Qty[$this->_knit][$this->_finish][$poId][$itemNumberId][$colorId][$sizeId][$uom]+=$reqqnty_finish;
					}
					else{
						$Qty[$this->_knit][$this->_finish][$poId][$itemNumberId][$colorId][$sizeId][$uom]=$reqqnty_finish;
					}
					if(isset($Qty[$this->_knit][$this->_grey][$poId][$itemNumberId][$colorId][$sizeId][$uom])){
						$Qty[$this->_knit][$this->_grey][$poId][$itemNumberId][$colorId][$sizeId][$uom]+=$reqqnty_grey;
					}
					else{
						$Qty[$this->_knit][$this->_grey][$poId][$itemNumberId][$colorId][$sizeId][$uom]=$reqqnty_grey;
					}
				}
				elseif($fabNatureId==3 && $fabricSourceId==2){
					if(isset($Qty[$this->_woven][$this->_finish][$poId][$itemNumberId][$colorId][$sizeId][$uom])){
						$Qty[$this->_woven][$this->_finish][$poId][$itemNumberId][$colorId][$sizeId][$uom]+=$reqqnty_finish;
					}
					else{
						$Qty[$this->_woven][$this->_finish][$poId][$itemNumberId][$colorId][$sizeId][$uom]=$reqqnty_finish;
					}
					if(isset($Qty[$this->_woven][$this->_grey][$poId][$itemNumberId][$colorId][$sizeId][$uom])){
						$Qty[$this->_woven][$this->_grey][$poId][$itemNumberId][$colorId][$sizeId][$uom]+=$reqqnty_grey;
					}
					else{
						$Qty[$this->_woven][$this->_grey][$poId][$itemNumberId][$colorId][$sizeId][$uom]=$reqqnty_grey;
					}
				}
			}
			elseif($level==$this->_By_orderCountryGmtsitemGmtscolorAndGmtssize){
				if($fabNatureId==2 && $fabricSourceId==2){
					if(isset($Qty[$this->_knit][$this->_finish][$poId][$countryId][$itemNumberId][$colorId][$sizeId][$uom])){
						$Qty[$this->_knit][$this->_finish][$poId][$countryId][$itemNumberId][$colorId][$sizeId][$uom]+=$reqqnty_finish;
					}
					else{
						$Qty[$this->_knit][$this->_finish][$poId][$countryId][$itemNumberId][$colorId][$sizeId][$uom]=$reqqnty_finish;
					}
					if(isset($Qty[$this->_knit][$this->_grey][$poId][$countryId][$itemNumberId][$colorId][$sizeId][$uom])){
						$Qty[$this->_knit][$this->_grey][$poId][$countryId][$itemNumberId][$colorId][$sizeId][$uom]+=$reqqnty_grey;
					}
					else{
						$Qty[$this->_knit][$this->_grey][$poId][$countryId][$itemNumberId][$colorId][$sizeId][$uom]=$reqqnty_grey;
					}
				}
				elseif($fabNatureId==3 && $fabricSourceId==2){
					if(isset($Qty[$this->_woven][$this->_finish][$poId][$countryId][$itemNumberId][$colorId][$sizeId][$uom])){
						$Qty[$this->_woven][$this->_finish][$poId][$countryId][$itemNumberId][$colorId][$sizeId][$uom]+=$reqqnty_finish;
					}
					else{
						$Qty[$this->_woven][$this->_finish][$poId][$countryId][$itemNumberId][$colorId][$sizeId][$uom]=$reqqnty_finish;
					}
					if(isset($Qty[$this->_woven][$this->_grey][$poId][$countryId][$itemNumberId][$colorId][$sizeId][$uom])){
						$Qty[$this->_woven][$this->_grey][$poId][$countryId][$itemNumberId][$colorId][$sizeId][$uom]+=$reqqnty_grey;
					}
					else{
						$Qty[$this->_woven][$this->_grey][$poId][$countryId][$itemNumberId][$colorId][$sizeId][$uom]=$reqqnty_grey;
					}
				}
			}
			else{
				return null;
			}
			
		}
		return $Qty;
	}
	
	private function _setQty_knitAndwoven_greyAndfinish_buyerSupplied($level){
		$jobNo='';
		$itemNumberId='';
		$planPutQnty=0;
		$bodypartId='';
		$requirment=0;
		$poId='';
		$Qty=array();
		foreach($this->_dataArray as $row)
		{
			$jobNo=$row['job_no'];
			$poId=$row['id'];
			$itemNumberId=$row['item_number_id'];
			$colorId=$row['color_number_id'];
			$sizeId=$row['size_number_id'];
			$countryId=$row['country_id'];
			$planPutQnty=$row['plan_cut_qnty'];
			
			
			$bodypartId=$row['body_part_id'];
			$fabNatureId=$row['fab_nature_id'];
			$fabricSourceId=$row['fabric_source'];
			$cons=$row['cons'];
			$requirment=$row['requirment'];
			$uom=$row['uom'];
			$costingPerQty=$this->_costingPerQtyArr[$jobNo];
			$set_item_ratio=$this->_gmtsitemRatioArray[$jobNo][$itemNumberId];
			
			$reqqnty_finish =$this->_calculateQty($planPutQnty,$costingPerQty,$set_item_ratio,$cons);
			$reqqnty_grey =$this->_calculateQty($planPutQnty,$costingPerQty,$set_item_ratio,$requirment);
			
			if($level==$this->_By_Job){
				if($fabNatureId==2 && $fabricSourceId==3){
					if(isset($Qty[$this->_knit][$this->_finish][$jobNo][$uom])){
						$Qty[$this->_knit][$this->_finish][$jobNo][$uom]+=$reqqnty_finish;
					}
					else{
						$Qty[$this->_knit][$this->_finish][$jobNo][$uom]=$reqqnty_finish;
					}
					if(isset($Qty[$this->_knit][$this->_grey][$jobNo][$uom])){
						$Qty[$this->_knit][$this->_grey][$jobNo][$uom]+=$reqqnty_grey;
					}
					else{
						$Qty[$this->_knit][$this->_grey][$jobNo][$uom]=$reqqnty_grey;
					}
				}
				elseif($fabNatureId==3 && $fabricSourceId==3){
					if(isset($Qty[$this->_woven][$this->_finish][$jobNo][$uom])){
						$Qty[$this->_woven][$this->_finish][$jobNo][$uom]+=$reqqnty_finish;
					}
					else{
						$Qty[$this->_woven][$this->_finish][$jobNo][$uom]=$reqqnty_finish;
					}
					if(isset($Qty[$this->_woven][$this->_grey][$jobNo][$uom])){
						$Qty[$this->_woven][$this->_grey][$jobNo][$uom]+=$reqqnty_grey;
					}
					else{
						$Qty[$this->_woven][$this->_grey][$jobNo][$uom]=$reqqnty_grey;
					}
				}
			}
			elseif($level==$this->_By_Order){
				if($fabNatureId==2 && $fabricSourceId==3){
					if(isset($Qty[$this->_knit][$this->_finish][$poId][$uom])){
						$Qty[$this->_knit][$this->_finish][$poId][$uom]+=$reqqnty_finish;
					}
					else{
						$Qty[$this->_knit][$this->_finish][$poId][$uom]=$reqqnty_finish;
					}
					if(isset($Qty[$this->_knit][$this->_grey][$poId][$uom])){
						$Qty[$this->_knit][$this->_grey][$poId][$uom]+=$reqqnty_grey;
					}
					else{
						$Qty[$this->_knit][$this->_grey][$poId][$uom]=$reqqnty_grey;
					}
				}
				elseif($fabNatureId==3 && $fabricSourceId==3){
					if(isset($Qty[$this->_woven][$this->_finish][$poId][$uom])){
						$Qty[$this->_woven][$this->_finish][$poId][$uom]+=$reqqnty_finish;
					}
					else{
						$Qty[$this->_woven][$this->_finish][$poId][$uom]=$reqqnty_finish;
					}
					if(isset($Qty[$this->_woven][$this->_grey][$poId][$uom])){
						$Qty[$this->_woven][$this->_grey][$poId][$uom]+=$reqqnty_grey;
					}
					else{
						$Qty[$this->_woven][$this->_grey][$poId][$uom]=$reqqnty_grey;
					}
				}
			}
			elseif($level==$this->_By_OrderAndCountry){
				if($fabNatureId==2 && $fabricSourceId==3){
					if(isset($Qty[$this->_knit][$this->_finish][$poId][$countryId][$uom])){
						$Qty[$this->_knit][$this->_finish][$poId][$countryId][$uom]+=$reqqnty_finish;
					}
					else{
						$Qty[$this->_knit][$this->_finish][$poId][$countryId][$uom]=$reqqnty_finish;
					}
					if(isset($Qty[$this->_knit][$this->_grey][$poId][$countryId][$uom])){
						$Qty[$this->_knit][$this->_grey][$poId][$countryId][$uom]+=$reqqnty_grey;
					}
					else{
						$Qty[$this->_knit][$this->_grey][$poId][$countryId][$uom]=$reqqnty_grey;
					}
				}
				elseif($fabNatureId==3 && $fabricSourceId==3){
					if(isset($Qty[$this->_woven][$this->_finish][$poId][$countryId][$uom])){
						$Qty[$this->_woven][$this->_finish][$poId][$countryId][$uom]+=$reqqnty_finish;
					}
					else{
						$Qty[$this->_woven][$this->_finish][$poId][$countryId][$uom]=$reqqnty_finish;
					}
					if(isset($Qty[$this->_woven][$this->_grey][$poId][$countryId][$uom])){
						$Qty[$this->_woven][$this->_grey][$poId][$countryId][$uom]+=$reqqnty_grey;
					}
					else{
						$Qty[$this->_woven][$this->_grey][$poId][$countryId][$uom]=$reqqnty_grey;
					}
				}
			}
			elseif($level==$this->_By_OrderAndGmtsitem){
				if($fabNatureId==2 && $fabricSourceId==3){
					if(isset($Qty[$this->_knit][$this->_finish][$poId][$itemNumberId][$uom])){
						$Qty[$this->_knit][$this->_finish][$poId][$itemNumberId][$uom]+=$reqqnty_finish;
					}
					else{
						$Qty[$this->_knit][$this->_finish][$poId][$itemNumberId][$uom]=$reqqnty_finish;
					}
					if(isset($Qty[$this->_knit][$this->_grey][$poId][$itemNumberId][$uom])){
					$Qty[$this->_knit][$this->_grey][$poId][$itemNumberId][$uom]+=$reqqnty_grey;
					}
					else{
						$Qty[$this->_knit][$this->_grey][$poId][$itemNumberId][$uom]=$reqqnty_grey;
					}
				}
				elseif($fabNatureId==3 && $fabricSourceId==3){
					if(isset($Qty[$this->_woven][$this->_finish][$poId][$itemNumberId][$uom])){
						$Qty[$this->_woven][$this->_finish][$poId][$itemNumberId][$uom]+=$reqqnty_finish;
					}
					else{


						$Qty[$this->_woven][$this->_finish][$poId][$itemNumberId][$uom]+=$reqqnty_finish;
					}
					if(isset($Qty[$this->_woven][$this->_grey][$poId][$itemNumberId][$uom])){
						$Qty[$this->_woven][$this->_grey][$poId][$itemNumberId][$uom]+=$reqqnty_grey;
					}
					else{
						$Qty[$this->_woven][$this->_grey][$poId][$itemNumberId][$uom]=$reqqnty_grey;
					}
				}
			}
			elseif($level==$this->_By_OrderAndGmtscolor){
				if($fabNatureId==2 && $fabricSourceId==3){
					if(isset($Qty[$this->_knit][$this->_finish][$poId][$colorId][$uom])){
						$Qty[$this->_knit][$this->_finish][$poId][$colorId][$uom]+=$reqqnty_finish;
					}
					else{
						$Qty[$this->_knit][$this->_finish][$poId][$colorId][$uom]=$reqqnty_finish;
					}
					if(isset($Qty[$this->_knit][$this->_grey][$poId][$colorId][$uom])){
						$Qty[$this->_knit][$this->_grey][$poId][$colorId][$uom]+=$reqqnty_grey;
					}
					else{
						$Qty[$this->_knit][$this->_grey][$poId][$colorId][$uom]=$reqqnty_grey;
					}
				}
				elseif($fabNatureId==3 && $fabricSourceId==3){
					if(isset($Qty[$this->_woven][$this->_finish][$poId][$colorId][$uom])){
						$Qty[$this->_woven][$this->_finish][$poId][$colorId][$uom]+=$reqqnty_finish;
					}
					else{
						$Qty[$this->_woven][$this->_finish][$poId][$colorId][$uom]=$reqqnty_finish;
					}
					if(isset($Qty[$this->_woven][$this->_finish][$poId][$colorId][$uom])){
						$Qty[$this->_woven][$this->_grey][$poId][$colorId][$uom]+=$reqqnty_grey;
					}
					else{
						$Qty[$this->_woven][$this->_grey][$poId][$colorId][$uom]=$reqqnty_grey;
					}
				}
			}
			elseif($level==$this->_By_OrderAndGmtssize){
				if($fabNatureId==2 && $fabricSourceId==3){
					if(isset($Qty[$this->_knit][$this->_finish][$poId][$sizeId][$uom])){
						$Qty[$this->_knit][$this->_finish][$poId][$sizeId][$uom]+=$reqqnty_finish;
					}
					else{
						$Qty[$this->_knit][$this->_finish][$poId][$sizeId][$uom]=$reqqnty_finish;
					}
					if(isset($Qty[$this->_knit][$this->_grey][$poId][$sizeId][$uom])){
						$Qty[$this->_knit][$this->_grey][$poId][$sizeId][$uom]+=$reqqnty_grey;
					}
					else{
						$Qty[$this->_knit][$this->_grey][$poId][$sizeId][$uom]=$reqqnty_grey;
					}
				}
				elseif($fabNatureId==3 && $fabricSourceId==3){
					if(isset($Qty[$this->_woven][$this->_finish][$poId][$sizeId][$uom])){
						$Qty[$this->_woven][$this->_finish][$poId][$sizeId][$uom]+=$reqqnty_finish;
					}
					else{
						$Qty[$this->_woven][$this->_finish][$poId][$sizeId][$uom]=$reqqnty_finish;
					}
					if(isset($Qty[$this->_woven][$this->_grey][$poId][$sizeId][$uom])){
						$Qty[$this->_woven][$this->_grey][$poId][$sizeId][$uom]+=$reqqnty_grey;
					}
					else{
						$Qty[$this->_woven][$this->_grey][$poId][$sizeId][$uom]=$reqqnty_grey;
					}
				}
			}
			elseif($level==$this->_By_orderCountryAndGmtsitem){
				if($fabNatureId==2 && $fabricSourceId==3){
					if(isset($Qty[$this->_knit][$this->_finish][$poId][$countryId][$itemNumberId][$uom])){
						$Qty[$this->_knit][$this->_finish][$poId][$countryId][$itemNumberId][$uom]+=$reqqnty_finish;
					}
					else{
						$Qty[$this->_knit][$this->_finish][$poId][$countryId][$itemNumberId][$uom]=$reqqnty_finish;
					}
					if(isset($Qty[$this->_knit][$this->_grey][$poId][$countryId][$itemNumberId][$uom])){
						$Qty[$this->_knit][$this->_grey][$poId][$countryId][$itemNumberId][$uom]+=$reqqnty_grey;
					}
					else{
						$Qty[$this->_knit][$this->_grey][$poId][$countryId][$itemNumberId][$uom]=$reqqnty_grey;
					}
				}
				elseif($fabNatureId==3 && $fabricSourceId==3){
					if(isset($Qty[$this->_woven][$this->_finish][$poId][$countryId][$itemNumberId][$uom])){
						$Qty[$this->_woven][$this->_finish][$poId][$countryId][$itemNumberId][$uom]+=$reqqnty_finish;
					}
					else{
						$Qty[$this->_woven][$this->_finish][$poId][$countryId][$itemNumberId][$uom]=$reqqnty_finish;
					}
					if(isset($Qty[$this->_woven][$this->_grey][$poId][$countryId][$itemNumberId][$uom])){
						$Qty[$this->_woven][$this->_grey][$poId][$countryId][$itemNumberId][$uom]+=$reqqnty_grey;
					}
					else{
						$Qty[$this->_woven][$this->_grey][$poId][$countryId][$itemNumberId][$uom]=$reqqnty_grey;
					}
				}
			}
			elseif($level==$this->_By_OrderCountryAndGmtscolor){
				if($fabNatureId==2 && $fabricSourceId==3){
					if(isset($Qty[$this->_knit][$this->_finish][$poId][$countryId][$colorId][$uom])){
						$Qty[$this->_knit][$this->_finish][$poId][$countryId][$colorId][$uom]+=$reqqnty_finish;
					}
					else{
						$Qty[$this->_knit][$this->_finish][$poId][$countryId][$colorId][$uom]=$reqqnty_finish;

					}
					if(isset($Qty[$this->_knit][$this->_grey][$poId][$countryId][$colorId][$uom])){
						$Qty[$this->_knit][$this->_grey][$poId][$countryId][$colorId][$uom]+=$reqqnty_grey;
					}
					else{
						$Qty[$this->_knit][$this->_grey][$poId][$countryId][$colorId][$uom]=$reqqnty_grey;
					}
				}
				elseif($fabNatureId==3 && $fabricSourceId==3){
					if(isset($Qty[$this->_woven][$this->_finish][$poId][$countryId][$colorId][$uom])){
						$Qty[$this->_woven][$this->_finish][$poId][$countryId][$colorId][$uom]+=$reqqnty_finish;
					}
					else{
						$Qty[$this->_woven][$this->_finish][$poId][$countryId][$colorId][$uom]=$reqqnty_finish;
					}
					if(isset($Qty[$this->_woven][$this->_grey][$poId][$countryId][$colorId][$uom])){
						$Qty[$this->_woven][$this->_grey][$poId][$countryId][$colorId][$uom]+=$reqqnty_grey;
					}
					else{
						$Qty[$this->_woven][$this->_grey][$poId][$countryId][$colorId][$uom]=$reqqnty_grey;
					}
				}
			}
			elseif($level==$this->_By_OrderCountryAndGmtssize){
				if($fabNatureId==2 && $fabricSourceId==3){
					if(isset($Qty[$this->_knit][$this->_finish][$poId][$countryId][$sizeId][$uom])){
						$Qty[$this->_knit][$this->_finish][$poId][$countryId][$sizeId][$uom]+=$reqqnty_finish;
					}
					else{
						$Qty[$this->_knit][$this->_finish][$poId][$countryId][$sizeId][$uom]=$reqqnty_finish;
					}
					if(isset($Qty[$this->_knit][$this->_grey][$poId][$countryId][$sizeId][$uom])){
						$Qty[$this->_knit][$this->_grey][$poId][$countryId][$sizeId][$uom]+=$reqqnty_grey;
					}
					else{
						$Qty[$this->_knit][$this->_grey][$poId][$countryId][$sizeId][$uom]=$reqqnty_grey;
					}
				}
				elseif($fabNatureId==3 && $fabricSourceId==3){
					if(isset($Qty[$this->_woven][$this->_finish][$poId][$countryId][$sizeId][$uom])){
						$Qty[$this->_woven][$this->_finish][$poId][$countryId][$sizeId][$uom]+=$reqqnty_finish;
					}
					else{
						$Qty[$this->_woven][$this->_finish][$poId][$countryId][$sizeId][$uom]=$reqqnty_finish;
					}
					if(isset($Qty[$this->_woven][$this->_grey][$poId][$countryId][$sizeId][$uom])){
						$Qty[$this->_woven][$this->_grey][$poId][$countryId][$sizeId][$uom]+=$reqqnty_grey;
					}
					else{
						$Qty[$this->_woven][$this->_grey][$poId][$countryId][$sizeId][$uom]=$reqqnty_grey;
					}
				}
			}
			elseif($level==$this->_By_OrderGmtsitemAndGmtscolor){
				if($fabNatureId==2 && $fabricSourceId==3){
					if(isset($Qty[$this->_knit][$this->_finish][$poId][$itemNumberId][$colorId][$uom])){
						$Qty[$this->_knit][$this->_finish][$poId][$itemNumberId][$colorId][$uom]+=$reqqnty_finish;
					}
					else{
						$Qty[$this->_knit][$this->_finish][$poId][$itemNumberId][$colorId][$uom]=$reqqnty_finish;
					}
					if(isset($Qty[$this->_knit][$this->_grey][$poId][$itemNumberId][$colorId][$uom])){
						$Qty[$this->_knit][$this->_grey][$poId][$itemNumberId][$colorId][$uom]+=$reqqnty_grey;
					}
					else{
						$Qty[$this->_knit][$this->_grey][$poId][$itemNumberId][$colorId][$uom]=$reqqnty_grey;
					}
				}
				elseif($fabNatureId==3 && $fabricSourceId==3){
					if(isset($Qty[$this->_woven][$this->_finish][$poId][$itemNumberId][$colorId][$uom])){
						$Qty[$this->_woven][$this->_finish][$poId][$itemNumberId][$colorId][$uom]+=$reqqnty_finish;
					}
					else{
						$Qty[$this->_woven][$this->_finish][$poId][$itemNumberId][$colorId][$uom]=$reqqnty_finish;
					}
					if(isset($Qty[$this->_woven][$this->_grey][$poId][$itemNumberId][$colorId][$uom])){
						$Qty[$this->_woven][$this->_grey][$poId][$itemNumberId][$colorId][$uom]+=$reqqnty_grey;
					}
					else{
						$Qty[$this->_woven][$this->_grey][$poId][$itemNumberId][$colorId][$uom]=$reqqnty_grey;
					}
				}
			}
			elseif($level==$this->_By_OrderGmtsitemAndGmtssize){
				if($fabNatureId==2 && $fabricSourceId==3){
					if(isset($Qty[$this->_knit][$this->_finish][$poId][$itemNumberId][$sizeId][$uom])){

						$Qty[$this->_knit][$this->_finish][$poId][$itemNumberId][$sizeId][$uom]+=$reqqnty_finish;
					}
					else{
						$Qty[$this->_knit][$this->_finish][$poId][$itemNumberId][$sizeId][$uom]=$reqqnty_finish;
					}
					if(isset($Qty[$this->_knit][$this->_grey][$poId][$itemNumberId][$sizeId][$uom])){
						$Qty[$this->_knit][$this->_grey][$poId][$itemNumberId][$sizeId][$uom]+=$reqqnty_grey;
					}
					else{
						$Qty[$this->_knit][$this->_grey][$poId][$itemNumberId][$sizeId][$uom]=$reqqnty_grey;
					}
				}
				elseif($fabNatureId==3 && $fabricSourceId==3){
					if(isset($Qty[$this->_woven][$this->_finish][$poId][$itemNumberId][$sizeId][$uom])){
						$Qty[$this->_woven][$this->_finish][$poId][$itemNumberId][$sizeId][$uom]+=$reqqnty_finish;
					}
					else{
						$Qty[$this->_woven][$this->_finish][$poId][$itemNumberId][$sizeId][$uom]=$reqqnty_finish;
					}
					if(isset($Qty[$this->_woven][$this->_grey][$poId][$itemNumberId][$sizeId][$uom])){
						$Qty[$this->_woven][$this->_grey][$poId][$itemNumberId][$sizeId][$uom]+=$reqqnty_grey;
					}
					else{
						$Qty[$this->_woven][$this->_grey][$poId][$itemNumberId][$sizeId][$uom]=$reqqnty_grey;
					}
				}
			}
			elseif($level==$this->_By_OrderGmtscolorAndGmtssize){
				if($fabNatureId==2 && $fabricSourceId==3){
					if(isset($Qty[$this->_knit][$this->_finish][$poId][$colorId][$sizeId][$uom])){
						$Qty[$this->_knit][$this->_finish][$poId][$colorId][$sizeId][$uom]+=$reqqnty_finish;
					}
					else{
						$Qty[$this->_knit][$this->_finish][$poId][$colorId][$sizeId][$uom]=$reqqnty_finish;
					}
					if(isset($Qty[$this->_knit][$this->_grey][$poId][$colorId][$sizeId][$uom])){
						$Qty[$this->_knit][$this->_grey][$poId][$colorId][$sizeId][$uom]+=$reqqnty_grey;
					}
					else{
						$Qty[$this->_knit][$this->_grey][$poId][$colorId][$sizeId][$uom]=$reqqnty_grey;
					}
				}
				elseif($fabNatureId==3 && $fabricSourceId==3){
					if(isset($Qty[$this->_woven][$this->_finish][$poId][$colorId][$sizeId][$uom])){
						$Qty[$this->_woven][$this->_finish][$poId][$colorId][$sizeId][$uom]+=$reqqnty_finish;
					}
					else{
						$Qty[$this->_woven][$this->_finish][$poId][$colorId][$sizeId][$uom]=$reqqnty_finish;
					}
					if(isset($Qty[$this->_woven][$this->_grey][$poId][$colorId][$sizeId][$uom]))
					{
						$Qty[$this->_woven][$this->_grey][$poId][$colorId][$sizeId][$uom]+=$reqqnty_grey;
					}
					else{
						$Qty[$this->_woven][$this->_grey][$poId][$colorId][$sizeId][$uom]=$reqqnty_grey;
					}
				}
			}
			elseif($level==$this->_By_OrderGmtscolorAndBodypart){
				if($fabNatureId==2 && $fabricSourceId==3){
					if(isset($Qty[$this->_knit][$this->_finish][$poId][$colorId][$bodypartId][$uom])){
						$Qty[$this->_knit][$this->_finish][$poId][$colorId][$bodypartId][$uom]+=$reqqnty_finish;
					}
					else{
						$Qty[$this->_knit][$this->_finish][$poId][$colorId][$bodypartId][$uom]=$reqqnty_finish;
					}
					if(isset($Qty[$this->_knit][$this->_grey][$poId][$colorId][$bodypartId][$uom])){
						$Qty[$this->_knit][$this->_grey][$poId][$colorId][$bodypartId][$uom]+=$reqqnty_grey;
					}
					else{
						$Qty[$this->_knit][$this->_grey][$poId][$colorId][$bodypartId][$uom]=$reqqnty_grey;
					}
				}
				elseif($fabNatureId==3 && $fabricSourceId==3){
					if(isset($Qty[$this->_woven][$this->_finish][$poId][$colorId][$bodypartId][$uom])){
						$Qty[$this->_woven][$this->_finish][$poId][$colorId][$bodypartId][$uom]+=$reqqnty_finish;
					}
					else{
						$Qty[$this->_woven][$this->_finish][$poId][$colorId][$bodypartId][$uom]=$reqqnty_finish;
					}
					if(isset($Qty[$this->_woven][$this->_grey][$poId][$colorId][$bodypartId][$uom]))
					{
						$Qty[$this->_woven][$this->_grey][$poId][$colorId][$bodypartId][$uom]+=$reqqnty_grey;
					}
					else{
						$Qty[$this->_woven][$this->_grey][$poId][$colorId][$bodypartId][$uom]=$reqqnty_grey;
					}
				}
			}
			elseif($level==$this->_By_orderCountryGmtsitemAndGmtscolor){
				if($fabNatureId==2 && $fabricSourceId==3){
					if(isset($Qty[$this->_knit][$this->_finish][$poId][$countryId][$itemNumberId][$colorId][$uom])){
						$Qty[$this->_knit][$this->_finish][$poId][$countryId][$itemNumberId][$colorId][$uom]+=$reqqnty_finish;
					}
					else{
						$Qty[$this->_knit][$this->_finish][$poId][$countryId][$itemNumberId][$colorId][$uom]=$reqqnty_finish;
					}
					if(isset($Qty[$this->_knit][$this->_grey][$poId][$countryId][$itemNumberId][$colorId][$uom])){
						$Qty[$this->_knit][$this->_grey][$poId][$countryId][$itemNumberId][$colorId][$uom]+=$reqqnty_grey;
					}
					else{
						$Qty[$this->_knit][$this->_grey][$poId][$countryId][$itemNumberId][$colorId][$uom]=$reqqnty_grey;
					}
				}
				elseif($fabNatureId==3 && $fabricSourceId==3){
					if(isset($Qty[$this->_woven][$this->_finish][$poId][$countryId][$itemNumberId][$colorId][$uom])){
						$Qty[$this->_woven][$this->_finish][$poId][$countryId][$itemNumberId][$colorId][$uom]+=$reqqnty_finish;
					}
					else{
						$Qty[$this->_woven][$this->_finish][$poId][$countryId][$itemNumberId][$colorId][$uom]=$reqqnty_finish;
					}
					if(isset($Qty[$this->_woven][$this->_grey][$poId][$countryId][$itemNumberId][$colorId][$uom])){
						$Qty[$this->_woven][$this->_grey][$poId][$countryId][$itemNumberId][$colorId][$uom]+=$reqqnty_grey;
					}
					else{
						$Qty[$this->_woven][$this->_grey][$poId][$countryId][$itemNumberId][$colorId][$uom]=$reqqnty_grey;
					}
				}
			}
			elseif($level==$this->_By_orderCountryGmtsitemAndGmtssize){
				if($fabNatureId==2 && $fabricSourceId==3){
					if(isset($Qty[$this->_knit][$this->_finish][$poId][$countryId][$itemNumberId][$sizeId][$uom])){
						$Qty[$this->_knit][$this->_finish][$poId][$countryId][$itemNumberId][$sizeId][$uom]+=$reqqnty_finish;
					}
					else{
						$Qty[$this->_knit][$this->_finish][$poId][$countryId][$itemNumberId][$sizeId][$uom]=$reqqnty_finish;
					}
					if(isset($Qty[$this->_knit][$this->_grey][$poId][$countryId][$itemNumberId][$sizeId][$uom])){
						$Qty[$this->_knit][$this->_grey][$poId][$countryId][$itemNumberId][$sizeId][$uom]+=$reqqnty_grey;
					}
					else{
						$Qty[$this->_knit][$this->_grey][$poId][$countryId][$itemNumberId][$sizeId][$uom]=$reqqnty_grey;
					}
				}
				elseif($fabNatureId==3 && $fabricSourceId==3){
					if(isset($Qty[$this->_woven][$this->_finish][$poId][$countryId][$itemNumberId][$sizeId][$uom])){
						$Qty[$this->_woven][$this->_finish][$poId][$countryId][$itemNumberId][$sizeId][$uom]+=$reqqnty_finish;
					}
					else{
						$Qty[$this->_woven][$this->_finish][$poId][$countryId][$itemNumberId][$sizeId][$uom]=$reqqnty_finish;
					}
					if(isset($Qty[$this->_woven][$this->_grey][$poId][$countryId][$itemNumberId][$sizeId][$uom])){
						$Qty[$this->_woven][$this->_grey][$poId][$countryId][$itemNumberId][$sizeId][$uom]+=$reqqnty_grey;
					}
					else{
						$Qty[$this->_woven][$this->_grey][$poId][$countryId][$itemNumberId][$sizeId][$uom]=$reqqnty_grey;
					}
				}
			}
			elseif($level==$this->_By_orderCountryGmtscolorAndGmtssize){
				if($fabNatureId==2 && $fabricSourceId==3){
					if(isset($Qty[$this->_knit][$this->_finish][$poId][$countryId][$colorId][$sizeId][$uom])){
						$Qty[$this->_knit][$this->_finish][$poId][$countryId][$colorId][$sizeId][$uom]+=$reqqnty_finish;
					}
					else{
						$Qty[$this->_knit][$this->_finish][$poId][$countryId][$colorId][$sizeId][$uom]=$reqqnty_finish;
					}
					if(isset($Qty[$this->_knit][$this->_grey][$poId][$countryId][$colorId][$sizeId][$uom])){
						$Qty[$this->_knit][$this->_grey][$poId][$countryId][$colorId][$sizeId][$uom]+=$reqqnty_grey;
					}
					else{
						$Qty[$this->_knit][$this->_grey][$poId][$countryId][$colorId][$sizeId][$uom]=$reqqnty_grey;
					}
				}
				elseif($fabNatureId==3 && $fabricSourceId==3){
					if(isset($Qty[$this->_woven][$this->_finish][$poId][$countryId][$colorId][$sizeId][$uom])){
						$Qty[$this->_woven][$this->_finish][$poId][$countryId][$colorId][$sizeId][$uom]+=$reqqnty_finish;
					}
					else{
						$Qty[$this->_woven][$this->_finish][$poId][$countryId][$colorId][$sizeId][$uom]=$reqqnty_finish;
					}
					if(isset($Qty[$this->_woven][$this->_grey][$poId][$countryId][$colorId][$sizeId][$uom])){
						$Qty[$this->_woven][$this->_grey][$poId][$countryId][$colorId][$sizeId][$uom]+=$reqqnty_grey;
					}
					else{
						$Qty[$this->_woven][$this->_grey][$poId][$countryId][$colorId][$sizeId][$uom]=$reqqnty_grey;
					}
				}
			}
			elseif($level==$this->_By_orderGmtsitemGmtscolorAndGmtssize){
				if($fabNatureId==2 && $fabricSourceId==3){
					if(isset($Qty[$this->_knit][$this->_finish][$poId][$itemNumberId][$colorId][$sizeId][$uom])){
						$Qty[$this->_knit][$this->_finish][$poId][$itemNumberId][$colorId][$sizeId][$uom]+=$reqqnty_finish;
					}
					else{
						$Qty[$this->_knit][$this->_finish][$poId][$itemNumberId][$colorId][$sizeId][$uom]=$reqqnty_finish;
					}
					if(isset($Qty[$this->_knit][$this->_grey][$poId][$itemNumberId][$colorId][$sizeId][$uom])){
						$Qty[$this->_knit][$this->_grey][$poId][$itemNumberId][$colorId][$sizeId][$uom]+=$reqqnty_grey;
					}
					else{
						$Qty[$this->_knit][$this->_grey][$poId][$itemNumberId][$colorId][$sizeId][$uom]=$reqqnty_grey;
					}
				}
				elseif($fabNatureId==3 && $fabricSourceId==3){
					if(isset($Qty[$this->_woven][$this->_finish][$poId][$itemNumberId][$colorId][$sizeId][$uom])){
						$Qty[$this->_woven][$this->_finish][$poId][$itemNumberId][$colorId][$sizeId][$uom]+=$reqqnty_finish;
					}
					else{
						$Qty[$this->_woven][$this->_finish][$poId][$itemNumberId][$colorId][$sizeId][$uom]=$reqqnty_finish;
					}
					if(isset($Qty[$this->_woven][$this->_grey][$poId][$itemNumberId][$colorId][$sizeId][$uom])){
						$Qty[$this->_woven][$this->_grey][$poId][$itemNumberId][$colorId][$sizeId][$uom]+=$reqqnty_grey;
					}
					else{
						$Qty[$this->_woven][$this->_grey][$poId][$itemNumberId][$colorId][$sizeId][$uom]=$reqqnty_grey;
					}
				}
			}
			elseif($level==$this->_By_orderCountryGmtsitemGmtscolorAndGmtssize){
				if($fabNatureId==2 && $fabricSourceId==3){
					if(isset($Qty[$this->_knit][$this->_finish][$poId][$countryId][$itemNumberId][$colorId][$sizeId][$uom])){
						$Qty[$this->_knit][$this->_finish][$poId][$countryId][$itemNumberId][$colorId][$sizeId][$uom]+=$reqqnty_finish;
					}
					else{
						$Qty[$this->_knit][$this->_finish][$poId][$countryId][$itemNumberId][$colorId][$sizeId][$uom]=$reqqnty_finish;
					}
					if(isset($Qty[$this->_knit][$this->_grey][$poId][$countryId][$itemNumberId][$colorId][$sizeId][$uom])){
						$Qty[$this->_knit][$this->_grey][$poId][$countryId][$itemNumberId][$colorId][$sizeId][$uom]+=$reqqnty_grey;
					}
					else{
						$Qty[$this->_knit][$this->_grey][$poId][$countryId][$itemNumberId][$colorId][$sizeId][$uom]=$reqqnty_grey;
					}
				}
				elseif($fabNatureId==3 && $fabricSourceId==3){
					if(isset($Qty[$this->_woven][$this->_finish][$poId][$countryId][$itemNumberId][$colorId][$sizeId][$uom])){
						$Qty[$this->_woven][$this->_finish][$poId][$countryId][$itemNumberId][$colorId][$sizeId][$uom]+=$reqqnty_finish;
					}
					else{
						$Qty[$this->_woven][$this->_finish][$poId][$countryId][$itemNumberId][$colorId][$sizeId][$uom]=$reqqnty_finish;
					}
					if(isset($Qty[$this->_woven][$this->_grey][$poId][$countryId][$itemNumberId][$colorId][$sizeId][$uom])){
						$Qty[$this->_woven][$this->_grey][$poId][$countryId][$itemNumberId][$colorId][$sizeId][$uom]+=$reqqnty_grey;
					}
					else{
						$Qty[$this->_woven][$this->_grey][$poId][$countryId][$itemNumberId][$colorId][$sizeId][$uom]=$reqqnty_grey;
					}
				}
			}
			else{
				return null;
			}
			
		}
		return $Qty;
	}
	
	public function unsetDataArray(){
		$this->_dataArray=array();
	}
	//Job wise
	//Qty
	public function getQty_by_job_knitAndwoven_greyAndfinish($jobNo){
		$Qty=$this->_setQty_knitAndwoven_greyAndfinish($this->_By_Job);
		return $Qty[$jobNo];
	}
	
	public function getQtyArray_by_job_knitAndwoven_greyAndfinish(){
		$Qty=$this->_setQty_knitAndwoven_greyAndfinish($this->_By_Job);
		return $Qty;
	}
	
	public function getQty_by_job_knitAndwoven_greyAndfinish_production($jobNo){
		$Qty=$this->_setQty_knitAndwoven_greyAndfinish_production($this->_By_Job);
		return $Qty[$jobNo];
	}
	
	public function getQtyArray_by_job_knitAndwoven_greyAndfinish_production(){
		$Qty=$this->_setQty_knitAndwoven_greyAndfinish_production($this->_By_Job);
		return $Qty;
	}
	
	public function getQty_by_job_knitAndwoven_greyAndfinish_purchase($jobNo){
		$Qty=$this->_setQty_knitAndwoven_greyAndfinish_purchase($this->_By_Job);
		return $Qty[$jobNo];
	}
	
	public function getQtyArray_by_job_knitAndwoven_greyAndfinish_purchase(){
		$Qty=$this->_setQty_knitAndwoven_greyAndfinish_purchase($this->_By_Job);
		return $Qty;
	}
	
	public function getQty_by_job_knitAndwoven_greyAndfinish_buyerSupplied($jobNo){
		$Qty=$this->_setQty_knitAndwoven_greyAndfinish_buyerSupplied($this->_By_Job);
		return $Qty[$jobNo];
	}
	
	public function getQtyArray_by_job_knitAndwoven_greyAndfinish_buyerSupplied(){
		$Qty=$this->_setQty_knitAndwoven_greyAndfinish_buyerSupplied($this->_By_Job);
		return $Qty;
	}
	
	//Amount
	public function getAmount_by_job_knitAndwoven_greyAndfinish($jobNo){
		$Amount=$this->_setAmount_knitAndwoven_greyAndfinish($this->_By_Job);
		return $Amount[$jobNo];
	}
	
	public function getAmountArray_by_job_knitAndwoven_greyAndfinish(){
		$Amount=$this->_setAmount_knitAndwoven_greyAndfinish($this->_By_Job);
		return $Amount;
	}
	
	
	// Order wise
	//Qty
	public function getQty_by_order_knitAndwoven_greyAndfinish($poId){
		$Qty=$this->_setQty_knitAndwoven_greyAndfinish($this->_By_Order);
		return $Qty[$poId];
	}
	
	public function getQtyArray_by_order_knitAndwoven_greyAndfinish(){
		$Qty=$this->_setQty_knitAndwoven_greyAndfinish($this->_By_Order);
		return $Qty;
	}
	
	public function getQty_by_order_knitAndwoven_greyAndfinish_production($poId){
		$Qty=$this->_setQty_knitAndwoven_greyAndfinish_production($this->_By_Order);
		return $Qty[$poId];
	}
	
	public function getQtyArray_by_order_knitAndwoven_greyAndfinish_production(){
		$Qty=$this->_setQty_knitAndwoven_greyAndfinish_production($this->_By_Order);
		return $Qty;
	}
	
	public function getQty_by_order_knitAndwoven_greyAndfinish_purchase($poId){
		$Qty=$this->_setQty_knitAndwoven_greyAndfinish_purchase($this->_By_Order);
		return $Qty[$poId];
	}
	
	public function getQtyArray_by_order_knitAndwoven_greyAndfinish_purchase(){
		$Qty=$this->_setQty_knitAndwoven_greyAndfinish_purchase($this->_By_Order);
		return $Qty;
	}
	
	public function getQty_by_order_knitAndwoven_greyAndfinish_buyerSupplied($poId){
		$Qty=$this->_setQty_knitAndwoven_greyAndfinish_buyerSupplied($this->_By_Order);
		return $Qty[$poId];
	}
	
	public function getQtyArray_by_order_knitAndwoven_greyAndfinish_buyerSupplied(){
		$Qty=$this->_setQty_knitAndwoven_greyAndfinish_buyerSupplied($this->_By_Order);
		return $Qty;
	}
	//Amount
	public function getAmount_by_order_knitAndwoven_greyAndfinish($poId){
		$Amount=$this->_setAmount_knitAndwoven_greyAndfinish($this->_By_Order);
		return $Amount[$poId];
	}
	
	public function getAmountArray_by_order_knitAndwoven_greyAndfinish(){
		$Amount=$this->_setAmount_knitAndwoven_greyAndfinish($this->_By_Order);
		return $Amount;
	}
	
	
	// Order and Country wise
	//Qty
	public function getQty_by_orderAndCountry_knitAndwoven_greyAndfinish($poId,$countryId){
		$Qty=$this->_setQty_knitAndwoven_greyAndfinish($this->_By_OrderAndCountry);
		return $Qty[$poId][$countryId];
	}
	
	public function getQtyArray_by_orderAndCountry_knitAndwoven_greyAndfinish(){
		$Qty=$this->_setQty_knitAndwoven_greyAndfinish($this->_By_OrderAndCountry);
		return $Qty;
	}
	
	public function getQty_by_orderAndCountry_knitAndwoven_greyAndfinish_production($poId,$countryId){
		$Qty=$this->_setQty_knitAndwoven_greyAndfinish_production($this->_By_OrderAndCountry);
		return $Qty[$poId][$countryId];
	}
	
	public function getQtyArray_by_orderAndCountry_knitAndwoven_greyAndfinish_production(){
		$Qty=$this->_setQty_knitAndwoven_greyAndfinish_production($this->_By_OrderAndCountry);
		return $Qty;
	}
	public function getQty_by_orderAndCountry_knitAndwoven_greyAndfinish_purchase($poId,$countryId){
		$Qty=$this->_setQty_knitAndwoven_greyAndfinish_purchase($this->_By_OrderAndCountry);
		return $Qty[$poId][$countryId];
	}
	
	public function getQtyArray_by_orderAndCountry_knitAndwoven_greyAndfinish_purchase(){
		$Qty=$this->_setQty_knitAndwoven_greyAndfinish_purchase($this->_By_OrderAndCountry);
		return $Qty;
	}
	
	public function getQty_by_orderAndCountry_knitAndwoven_greyAndfinish_buyerSupplied($poId,$countryId){
		$Qty=$this->_setQty_knitAndwoven_greyAndfinish_buyerSupplied($this->_By_OrderAndCountry);
		return $Qty[$poId][$countryId];
	}
	
	public function getQtyArray_by_orderAndCountry_knitAndwoven_greyAndfinish_buyerSupplied(){
		$Qty=$this->_setQty_knitAndwoven_greyAndfinish_buyerSupplied($this->_By_OrderAndCountry);
		return $Qty;
	}
	//Amount
	public function getAmount_by_orderAndCountry_knitAndwoven_greyAndfinish($poId,$countryId){
		$Amount=$this->_setAmount_knitAndwoven_greyAndfinish($this->_By_OrderAndCountry);
		return $Amount[$poId][$countryId];
	}
	public function getAmountArray_by_orderAndCountry_knitAndwoven_greyAndfinish(){
		$Amount=$this->_setAmount_knitAndwoven_greyAndfinish($this->_By_OrderAndCountry);
		return $Amount;
	}
	
	// Order and Gmts Item wise
	//Qty
	public function getQty_by_orderAndGmtsitem_knitAndwoven_greyAndfinish($poId,$gmtsItem){
		$Qty=$this->_setQty_knitAndwoven_greyAndfinish($this->_By_OrderAndGmtsitem);
		return $Qty[$poId][$gmtsItem];
	}
	
	public function getQtyArray_by_orderAndGmtsitem_knitAndwoven_greyAndfinish(){
		$Qty=$this->_setQty_knitAndwoven_greyAndfinish($this->_By_OrderAndGmtsitem);
		return $Qty;
	}
	public function getQty_by_orderAndGmtsitem_knitAndwoven_greyAndfinish_production($poId,$gmtsItem){
		$Qty=$this->_setQty_knitAndwoven_greyAndfinish_production($this->_By_OrderAndGmtsitem);
		return $Qty[$poId][$gmtsItem];
	}
	
	public function getQtyArray_by_orderAndGmtsitem_knitAndwoven_greyAndfinish_production(){
		$Qty=$this->_setQty_knitAndwoven_greyAndfinish_production($this->_By_OrderAndGmtsitem);
		return $Qty;
	}
	public function getQty_by_orderAndGmtsitem_knitAndwoven_greyAndfinish_purchase($poId,$gmtsItem){
		$Qty=$this->_setQty_knitAndwoven_greyAndfinish_purchase($this->_By_OrderAndGmtsitem);
		return $Qty[$poId][$gmtsItem];
	}
	
	public function getQtyArray_by_orderAndGmtsitem_knitAndwoven_greyAndfinish_purchase(){
		$Qty=$this->_setQty_knitAndwoven_greyAndfinish_purchase($this->_By_OrderAndGmtsitem);
		return $Qty;
	}
	public function getQty_by_orderAndGmtsitem_knitAndwoven_greyAndfinish_buyerSupplied($poId,$gmtsItem){
		$Qty=$this->_setQty_knitAndwoven_greyAndfinish_buyerSupplied($this->_By_OrderAndGmtsitem);
		return $Qty[$poId][$gmtsItem];
	}
	
	public function getQtyArray_by_orderAndGmtsitem_knitAndwoven_greyAndfinish_buyerSupplied(){
		$Qty=$this->_setQty_knitAndwoven_greyAndfinish_buyerSupplied($this->_By_OrderAndGmtsitem);
		return $Qty;
	}
	//Amount
	public function getAmount_by_orderAndGmtsitem_knitAndwoven_greyAndfinish($poId,$gmtsItem){
		$Amount=$this->_setAmount_knitAndwoven_greyAndfinish($this->_By_OrderAndGmtsitem);
		return $Amount[$poId][$gmtsItem];
	}
	
	public function getAmountArray_by_orderAndGmtsitem_knitAndwoven_greyAndfinish(){
		$Amount=$this->_setAmount_knitAndwoven_greyAndfinish($this->_By_OrderAndGmtsitem);
		return $Amount;
	}
	
	// Order and Gmts Color wise
	//Qty
	public function getQty_by_orderAndGmtscolor_knitAndwoven_greyAndfinish($poId,$colorId){
		$Qty=$this->_setQty_knitAndwoven_greyAndfinish($this->_By_OrderAndGmtscolor);
		return $Qty[$poId][$colorId];
	}
	
	public function getQtyArray_by_orderAndGmtscolor_knitAndwoven_greyAndfinish(){
		$Qty=$this->_setQty_knitAndwoven_greyAndfinish($this->_By_OrderAndGmtscolor);
		return $Qty;
	}
	public function getQty_by_orderAndGmtscolor_knitAndwoven_greyAndfinish_production($poId,$colorId){
		$Qty=$this->_setQty_knitAndwoven_greyAndfinish_production($this->_By_OrderAndGmtscolor);
		return $Qty[$poId][$colorId];
	}
	
	public function getQtyArray_by_orderAndGmtscolor_knitAndwoven_greyAndfinish_production(){
		$Qty=$this->_setQty_knitAndwoven_greyAndfinish_production($this->_By_OrderAndGmtscolor);
		return $Qty;
	}
	public function getQty_by_orderAndGmtscolor_knitAndwoven_greyAndfinish_purchase($poId,$colorId){
		$Qty=$this->_setQty_knitAndwoven_greyAndfinish_purchase($this->_By_OrderAndGmtscolor);
		return $Qty[$poId][$colorId];
	}
	
	public function getQtyArray_by_orderAndGmtscolor_knitAndwoven_greyAndfinish_purchase(){
		$Qty=$this->_setQty_knitAndwoven_greyAndfinish_purchase($this->_By_OrderAndGmtscolor);
		return $Qty;
	}
	public function getQty_by_orderAndGmtscolor_knitAndwoven_greyAndfinish_buyerSupplied($poId,$colorId){
		$Qty=$this->_setQty_knitAndwoven_greyAndfinish_buyerSupplied($this->_By_OrderAndGmtscolor);
		return $Qty[$poId][$colorId];
	}
	
	public function getQtyArray_by_orderAndGmtscolor_knitAndwoven_greyAndfinish_buyerSupplied(){
		$Qty=$this->_setQty_knitAndwoven_greyAndfinish_buyerSupplied($this->_By_OrderAndGmtscolor);
		return $Qty;
	}
	//Amount
	public function getAmount_by_orderAndGmtscolor_knitAndwoven_greyAndfinish($poId,$colorId){
		$Amount=$this->_setAmount_knitAndwoven_greyAndfinish($this->By_OrderAndGmtscolor);
		return $Amount[$poId][$colorId];
	}
	
	public function getAmountArray_by_orderAndGmtscolor_knitAndwoven_greyAndfinish(){
		$Amount=$this->_setAmount_knitAndwoven_greyAndfinish($this->_By_OrderAndGmtscolor);
		return $Amount;
	}
	
	// Order and Gmts Size wise
	//Qty
	public function getQty_by_orderAndGmtssize_knitAndwoven_greyAndfinish($poId,$sizeId){
		$Qty=$this->_setQty_knitAndwoven_greyAndfinish($this->_By_OrderAndGmtssize);
		return $Qty[$poId][$sizeId];
	}
	
	public function getQtyArray_by_orderAndGmtssize_knitAndwoven_greyAndfinish(){
		$Qty=$this->_setQty_knitAndwoven_greyAndfinish($this->_By_OrderAndGmtssize);
		return $Qty;
	}
	public function getQty_by_orderAndGmtssize_knitAndwoven_greyAndfinish_production($poId,$sizeId){
		$Qty=$this->_setQty_knitAndwoven_greyAndfinish_production($this->_By_OrderAndGmtssize);
		return $Qty[$poId][$sizeId];
	}
	
	public function getQtyArray_by_orderAndGmtssize_knitAndwoven_greyAndfinish_production(){
		$Qty=$this->_setQty_knitAndwoven_greyAndfinish_production($this->_By_OrderAndGmtssize);
		return $Qty;
	}
	public function getQty_by_orderAndGmtssize_knitAndwoven_greyAndfinish_purchase($poId,$sizeId){
		$Qty=$this->_setQty_knitAndwoven_greyAndfinish_purchase($this->_By_OrderAndGmtssize);
		return $Qty[$poId][$sizeId];
	}
	
	public function getQtyArray_by_orderAndGmtssize_knitAndwoven_greyAndfinish_purchase(){
		$Qty=$this->_setQty_knitAndwoven_greyAndfinish_purchase($this->_By_OrderAndGmtssize);
		return $Qty;
	}
	public function getQty_by_orderAndGmtssize_knitAndwoven_greyAndfinish_buyerSupplied($poId,$sizeId){
		$Qty=$this->_setQty_knitAndwoven_greyAndfinish_buyerSupplied($this->_By_OrderAndGmtssize);
		return $Qty[$poId][$sizeId];
	}
	
	public function getQtyArray_by_orderAndGmtssize_knitAndwoven_greyAndfinish_buyerSupplied(){
		$Qty=$this->_setQty_knitAndwoven_greyAndfinish_buyerSupplied($this->_By_OrderAndGmtssize);
		return $Qty;
	}
	//Amount
	public function getAmount_by_orderAndGmtssize_knitAndwoven_greyAndfinish($poId,$sizeId){
		$Amount=$this->_setAmount_knitAndwoven_greyAndfinish($this->_By_OrderAndGmtssize);
		return $Amount[$poId][$sizeId];
	}
	
	public function getAmountArray_by_orderAndGmtssize_knitAndwoven_greyAndfinish(){
		$Amount=$this->_setAmount_knitAndwoven_greyAndfinish($this->_By_OrderAndGmtssize);
		return $Amount;
	}
	
	
	
	// Order,Country and Gmts Item wise
	//Qty
	public function getQty_by_orderCountryAndGmtsitem_knitAndwoven_greyAndfinish($poId,$countryId,$gmtsItem){
		$Qty=$this->_setQty_knitAndwoven_greyAndfinish($this->_By_orderCountryAndGmtsitem);
		return $Qty[$poId][$countryId][$gmtsItem];
	}
	
	public function getQtyArray_by_orderCountryAndGmtsitem_knitAndwoven_greyAndfinish(){
		$Qty=$this->_setQty_knitAndwoven_greyAndfinish($this->_By_orderCountryAndGmtsitem);
		return $Qty;
	}
	
	public function getQty_by_orderCountryAndGmtsitem_knitAndwoven_greyAndfinish_production($poId,$countryId,$gmtsItem){
		$Qty=$this->_setQty_knitAndwoven_greyAndfinish_production($this->_By_orderCountryAndGmtsitem);
		return $Qty[$poId][$countryId][$gmtsItem];
	}
	
	public function getQtyArray_by_orderCountryAndGmtsitem_knitAndwoven_greyAndfinish_production(){
		$Qty=$this->_setQty_knitAndwoven_greyAndfinish_production($this->_By_orderCountryAndGmtsitem);
		return $Qty;
	}
	public function getQty_by_orderCountryAndGmtsitem_knitAndwoven_greyAndfinish_purchase($poId,$countryId,$gmtsItem){
		$Qty=$this->_setQty_knitAndwoven_greyAndfinish_purchase($this->_By_orderCountryAndGmtsitem);
		return $Qty[$poId][$countryId][$gmtsItem];
	}
	
	public function getQtyArray_by_orderCountryAndGmtsitem_knitAndwoven_greyAndfinish_purchase(){
		$Qty=$this->_setQty_knitAndwoven_greyAndfinish_purchase($this->_By_orderCountryAndGmtsitem);
		return $Qty;
	}
	public function getQty_by_orderCountryAndGmtsitem_knitAndwoven_greyAndfinish_buyerSupplied($poId,$countryId,$gmtsItem){
		$Qty=$this->_setQty_knitAndwoven_greyAndfinish_buyerSupplied($this->_By_orderCountryAndGmtsitem);
		return $Qty[$poId][$countryId][$gmtsItem];
	}
	
	public function getQtyArray_by_orderCountryAndGmtsitem_knitAndwoven_greyAndfinish_buyerSupplied(){
		$Qty=$this->_setQty_knitAndwoven_greyAndfinish_buyerSupplied($this->_By_orderCountryAndGmtsitem);
		return $Qty;
	}
	//Amount
	public function getAmount_by_orderCountryAndGmtsitem_knitAndwoven_greyAndfinish($poId,$countryId,$gmtsItem){
		$Amount=$this->_setAmount_knitAndwoven_greyAndfinish($this->_By_orderCountryAndGmtsitem);
		return $Amount[$poId][$countryId][$gmtsItem];
	}
	public function getAmountArray_by_orderCountryAndGmtsitem_knitAndwoven_greyAndfinish(){
		$Amount=$this->_setAmount_knitAndwoven_greyAndfinish($this->_By_orderCountryAndGmtsitem);
		return $Amount;
	}
	// Order and Country And Color Wise
	//Qty
	public function getQty_by_orderCountryAndGmtscolor_knitAndwoven_greyAndfinish($poId,$countryId,$colorId){
		$Qty=$this->_setQty_knitAndwoven_greyAndfinish($this->_By_OrderCountryAndGmtscolor);
		return $Qty[$poId][$countryId][$colorId];
	}
	
	public function getQtyArray_by_orderCountryAndGmtscolor_knitAndwoven_greyAndfinish(){
		$Qty=$this->_setQty_knitAndwoven_greyAndfinish($this->_By_OrderCountryAndGmtscolor);
		return $Qty;
	}
	
	public function getQty_by_orderCountryAndGmtscolor_knitAndwoven_greyAndfinish_production($poId,$countryId,$colorId){
		$Qty=$this->_setQty_knitAndwoven_greyAndfinish_production($this->_By_OrderCountryAndGmtscolor);
		return $Qty[$poId][$countryId][$colorId];
	}
	
	public function getQtyArray_by_orderCountryAndGmtscolor_knitAndwoven_greyAndfinish_production(){
		$Qty=$this->_setQty_knitAndwoven_greyAndfinish_production($this->_By_OrderCountryAndGmtscolor);
		return $Qty;
	}
	public function getQty_by_orderCountryAndGmtscolor_knitAndwoven_greyAndfinish_purchase($poId,$countryId,$colorId){
		$Qty=$this->_setQty_knitAndwoven_greyAndfinish_purchase($this->_By_OrderCountryAndGmtscolor);
		return $Qty[$poId][$countryId][$colorId];
	}
	
	public function getQtyArray_by_orderCountryAndGmtscolor_knitAndwoven_greyAndfinish_purchase(){
		$Qty=$this->_setQty_knitAndwoven_greyAndfinish_purchase($this->_By_OrderCountryAndGmtscolor);
		return $Qty;
	}
	
	public function getQty_by_orderCountryAndGmtscolor_knitAndwoven_greyAndfinish_buyerSupplied($poId,$countryId,$colorId){
		$Qty=$this->_setQty_knitAndwoven_greyAndfinish_buyerSupplied($this->_By_OrderCountryAndGmtscolor);
		return $Qty[$poId][$countryId][$colorId];
	}
	
	public function getQtyArray_by_orderCountryAndGmtscolor_knitAndwoven_greyAndfinish_buyerSupplied(){
		$Qty=$this->_setQty_knitAndwoven_greyAndfinish_buyerSupplied($this->_By_OrderCountryAndGmtscolor);
		return $Qty;
	}
	//Amount
	public function getAmount_by_orderCountryAndGmtscolor_knitAndwoven_greyAndfinish($poId,$countryId,$colorId){
		$Amount=$this->_setAmount_knitAndwoven_greyAndfinish($this->_By_OrderCountryAndGmtscolor);
		return $Amount[$poId][$countryId][$colorId];
	}
	public function getAmountArray_by_orderCountryAndGmtscolor_knitAndwoven_greyAndfinish(){
		$Amount=$this->_setAmount_knitAndwoven_greyAndfinish($this->_By_OrderCountryAndGmtscolor);
		return $Amount;
	}
	
	// Order and Country And Size Wise
	//Qty
	public function getQty_by_orderCountryAndGmtssize_knitAndwoven_greyAndfinish($poId,$countryId,$sizeId){
		$Qty=$this->_setQty_knitAndwoven_greyAndfinish($this->_By_OrderCountryAndGmtssize);
		return $Qty[$poId][$countryId][$sizeId];
	}
	
	public function getQtyArray_by_orderCountryAndGmtssize_knitAndwoven_greyAndfinish(){
		$Qty=$this->_setQty_knitAndwoven_greyAndfinish($this->_By_OrderCountryAndGmtssize);
		return $Qty;
	}
	
	public function getQty_by_orderCountryAndGmtssize_knitAndwoven_greyAndfinish_production($poId,$countryId,$sizeId){
		$Qty=$this->_setQty_knitAndwoven_greyAndfinish_production($this->_By_OrderCountryAndGmtssize);
		return $Qty[$poId][$countryId][$sizeId];
	}
	
	public function getQtyArray_by_orderCountryAndGmtssize_knitAndwoven_greyAndfinish_production(){
		$Qty=$this->_setQty_knitAndwoven_greyAndfinish_production($this->_By_OrderCountryAndGmtssize);
		return $Qty;
	}
	public function getQty_by_orderCountryAndGmtssize_knitAndwoven_greyAndfinish_purchase($poId,$countryId,$sizeId){
		$Qty=$this->_setQty_knitAndwoven_greyAndfinish_purchase($this->_By_OrderCountryAndGmtssize);
		return $Qty[$poId][$countryId][$sizeId];
	}
	
	public function getQtyArray_by_orderCountryAndGmtssize_knitAndwoven_greyAndfinish_purchase(){
		$Qty=$this->_setQty_knitAndwoven_greyAndfinish_purchase($this->_By_OrderCountryAndGmtssize);
		return $Qty;
	}
	
	public function getQty_by_orderCountryAndGmtssize_knitAndwoven_greyAndfinish_buyerSupplied($poId,$countryId,$sizeId){
		$Qty=$this->_setQty_knitAndwoven_greyAndfinish_buyerSupplied($this->_By_OrderCountryAndGmtssize);
		return $Qty[$poId][$countryId][$sizeId];
	}
	
	public function getQtyArray_by_orderCountryAndGmtssize_knitAndwoven_greyAndfinish_buyerSupplied(){
		$Qty=$this->_setQty_knitAndwoven_greyAndfinish_buyerSupplied($this->_By_OrderCountryAndGmtssize);
		return $Qty;
	}
	//Amount
	public function getAmount_by_orderCountryAndGmtssize_knitAndwoven_greyAndfinish($poId,$countryId,$sizeId){
		$Amount=$this->_setAmount_knitAndwoven_greyAndfinish($this->_By_OrderCountryAndGmtssize);
		return $Amount[$poId][$countryId][$sizeId];
	}
	public function getAmountArray_by_orderCountryAndGmtssize_knitAndwoven_greyAndfinish(){
		$Amount=$this->_setAmount_knitAndwoven_greyAndfinish($this->_By_OrderCountryAndGmtssize);
		return $Amount;
	}
	
	// Order and Gmts Item And Color Wise
	//Qty
	public function getQty_by_orderGmtsitemAndGmtscolor_knitAndwoven_greyAndfinish($poId,$gmtsItem,$colorId){
		$Qty=$this->_setQty_knitAndwoven_greyAndfinish($this->_By_OrderGmtsitemAndGmtscolor);
		return $Qty[$poId][$gmtsItem][$colorId];
	}
	
	public function getQtyArray_by_orderGmtsitemAndGmtscolor_knitAndwoven_greyAndfinish(){
		$Qty=$this->_setQty_knitAndwoven_greyAndfinish($this->_By_OrderGmtsitemAndGmtscolor);
		return $Qty;
	}
	
	public function getQty_by_orderGmtsitemAndGmtscolor_knitAndwoven_greyAndfinish_production($poId,$gmtsItem,$colorId){
		$Qty=$this->_setQty_knitAndwoven_greyAndfinish_production($this->_By_OrderGmtsitemAndGmtscolor);
		return $Qty[$poId][$gmtsItem][$colorId];
	}
	
	public function getQtyArray_by_orderGmtsitemAndGmtscolor_knitAndwoven_greyAndfinish_production(){
		$Qty=$this->_setQty_knitAndwoven_greyAndfinish_production($this->_By_OrderGmtsitemAndGmtscolor);
		return $Qty;
	}
	public function getQty_by_orderGmtsitemAndGmtscolor_knitAndwoven_greyAndfinish_purchase($poId,$gmtsItem,$colorId){
		$Qty=$this->_setQty_knitAndwoven_greyAndfinish_purchase($this->_By_OrderGmtsitemAndGmtscolor);
		return $Qty[$poId][$gmtsItem][$colorId];
	}
	
	public function getQtyArray_by_orderGmtsitemAndGmtscolor_knitAndwoven_greyAndfinish_purchase(){
		$Qty=$this->_setQty_knitAndwoven_greyAndfinish_purchase($this->_By_OrderGmtsitemAndGmtscolor);
		return $Qty;
	}
	
	public function getQty_by_orderGmtsitemAndGmtscolor_knitAndwoven_greyAndfinish_buyerSupplied($poId,$gmtsItem,$colorId){
		$Qty=$this->_setQty_knitAndwoven_greyAndfinish_buyerSupplied($this->_By_OrderGmtsitemAndGmtscolor);
		return $Qty[$poId][$gmtsItem][$colorId];
	}
	
	public function getQtyArray_by_orderGmtsitemAndGmtscolor_knitAndwoven_greyAndfinish_buyerSupplied(){
		$Qty=$this->_setQty_knitAndwoven_greyAndfinish_buyerSupplied($this->_By_OrderGmtsitemAndGmtscolor);
		return $Qty;
	}
	//Amount
	public function getAmount_by_orderGmtsitemAndGmtscolor_knitAndwoven_greyAndfinish($poId,$gmtsItem,$colorId){
		$Amount=$this->_setAmount_knitAndwoven_greyAndfinish($this->_By_OrderGmtsitemAndGmtscolor);
		return $Amount[$poId][$gmtsItem][$colorId];
	}
	public function getAmountArray_by_orderGmtsitemAndGmtscolor_knitAndwoven_greyAndfinish(){
		$Amount=$this->_setAmount_knitAndwoven_greyAndfinish($this->_By_OrderGmtsitemAndGmtscolor);
		return $Amount;
	}
	
	// Order and Gmts Item And Size Wise=============================================================================
	//Qty
	public function getQty_by_orderGmtsitemAndGmtssize_knitAndwoven_greyAndfinish($poId,$gmtsItem,$sizeId){
		$Qty=$this->_setQty_knitAndwoven_greyAndfinish($this->_By_OrderGmtsitemAndGmtssize);
		return $Qty[$poId][$gmtsItem][$sizeId];
	}
	
	public function getQtyArray_by_orderGmtsitemAndGmtssize_knitAndwoven_greyAndfinish(){
		$Qty=$this->_setQty_knitAndwoven_greyAndfinish($this->_By_OrderGmtsitemAndGmtssize);
		return $Qty;
	}
	
	public function getQty_by_orderGmtsitemAndGmtssize_knitAndwoven_greyAndfinish_production($poId,$gmtsItem,$sizeId){
		$Qty=$this->_setQty_knitAndwoven_greyAndfinish_production($this->_By_OrderGmtsitemAndGmtssize);
		return $Qty[$poId][$gmtsItem][$sizeId];
	}
	
	public function getQtyArray_by_orderGmtsitemAndGmtssize_knitAndwoven_greyAndfinish_production(){
		$Qty=$this->_setQty_knitAndwoven_greyAndfinish_production($this->_By_OrderGmtsitemAndGmtssize);
		return $Qty;
	}
	public function getQty_by_orderGmtsitemAndGmtssize_knitAndwoven_greyAndfinish_purchase($poId,$gmtsItem,$sizeId){
		$Qty=$this->_setQty_knitAndwoven_greyAndfinish_purchase($this->_By_OrderGmtsitemAndGmtssize);
		return $Qty[$poId][$gmtsItem][$sizeId];
	}
	
	public function getQtyArray_by_orderGmtsitemAndGmtssize_knitAndwoven_greyAndfinish_purchase(){
		$Qty=$this->_setQty_knitAndwoven_greyAndfinish_purchase($this->_By_OrderGmtsitemAndGmtssize);
		return $Qty;
	}
	
	public function getQty_by_orderGmtsitemAndGmtssize_knitAndwoven_greyAndfinish_buyerSupplied($poId,$gmtsItem,$sizeId){
		$Qty=$this->_setQty_knitAndwoven_greyAndfinish_buyerSupplied($this->_By_OrderGmtsitemAndGmtssize);
		return $Qty[$poId][$gmtsItem][$sizeId];
	}
	
	public function getQtyArray_by_orderGmtsitemAndGmtssize_knitAndwoven_greyAndfinish_buyerSupplied(){
		$Qty=$this->_setQty_knitAndwoven_greyAndfinish_buyerSupplied($this->_By_OrderGmtsitemAndGmtssize);
		return $Qty;
	}
	//Amount
	public function getAmount_by_orderGmtsitemAndGmtssize_knitAndwoven_greyAndfinish($poId,$gmtsItem,$sizeId){
		$Amount=$this->_setAmount_knitAndwoven_greyAndfinish($this->_By_OrderGmtsitemAndGmtssize);
		return $Amount[$poId][$gmtsItem][$sizeId];
	}
	public function getAmountArray_by_orderGmtsitemAndGmtssize_knitAndwoven_greyAndfinish(){
		$Amount=$this->_setAmount_knitAndwoven_greyAndfinish($this->_By_OrderGmtsitemAndGmtssize);
		return $Amount;
	}
	
	// Order and Gmts Color And Size Wise=============================================================================
	//Qty
	public function getQty_by_orderGmtscolorAndGmtssize_knitAndwoven_greyAndfinish($poId,$colorId,$sizeId){
		$Qty=$this->_setQty_knitAndwoven_greyAndfinish($this->_By_OrderGmtscolorAndGmtssize);
		return $Qty[$poId][$colorId][$sizeId];
	}
	
	public function getQtyArray_by_orderGmtscolorAndGmtssize_knitAndwoven_greyAndfinish(){
		$Qty=$this->_setQty_knitAndwoven_greyAndfinish($this->_By_OrderGmtscolorAndGmtssize);
		return $Qty;
	}
	
	public function getQty_by_orderGmtscolorAndGmtssize_knitAndwoven_greyAndfinish_production($poId,$colorId,$sizeId){
		$Qty=$this->_setQty_knitAndwoven_greyAndfinish_production($this->_By_OrderGmtscolorAndGmtssize);
		return $Qty[$poId][$colorId][$sizeId];
	}
	
	public function getQtyArray_by_orderGmtscolorAndGmtssize_knitAndwoven_greyAndfinish_production(){
		$Qty=$this->_setQty_knitAndwoven_greyAndfinish_production($this->_By_OrderGmtscolorAndGmtssize);
		return $Qty;
	}
	public function getQty_by_orderGmtscolorAndGmtssize_knitAndwoven_greyAndfinish_purchase($poId,$colorId,$sizeId){
		$Qty=$this->_setQty_knitAndwoven_greyAndfinish_purchase($this->_By_OrderGmtscolorAndGmtssize);
		return $Qty[$poId][$colorId][$sizeId];
	}
	
	public function getQtyArray_by_orderGmtscolorAndGmtssize_knitAndwoven_greyAndfinish_purchase(){
		$Qty=$this->_setQty_knitAndwoven_greyAndfinish_purchase($this->_By_OrderGmtscolorAndGmtssize);
		return $Qty;
	}
	
	public function getQty_by_orderGmtscolorAndGmtssize_knitAndwoven_greyAndfinish_buyerSupplied($poId,$colorId,$sizeId){
		$Qty=$this->_setQty_knitAndwoven_greyAndfinish_buyerSupplied($this->_By_OrderGmtscolorAndGmtssize);
		return $Qty[$poId][$colorId][$sizeId];
	}
	
	public function getQtyArray_by_orderGmtscolorAndGmtssize_knitAndwoven_greyAndfinish_buyerSupplied(){
		$Qty=$this->_setQty_knitAndwoven_greyAndfinish_buyerSupplied($this->_By_OrderGmtscolorAndGmtssize);
		return $Qty;
	}
	//Amount
	public function getAmount_by_orderGmtscolorAndGmtssize_knitAndwoven_greyAndfinish($poId,$colorId,$sizeId){
		$Amount=$this->_setAmount_knitAndwoven_greyAndfinish($this->_By_OrderGmtscolorAndGmtssize);
		return $Amount[$poId][$colorId][$sizeId];
	}
	public function getAmountArray_by_orderGmtscolorAndGmtssize_knitAndwoven_greyAndfinish(){
		$Amount=$this->_setAmount_knitAndwoven_greyAndfinish($this->_By_OrderGmtscolorAndGmtssize);
		return $Amount;
	}
	
	// Order and Gmts Color And Body Part Wise=============================================================================
	//Qty
	public function getQty_by_orderGmtscolorAndBodypart_knitAndwoven_greyAndfinish($poId,$colorId,$bodyPartId){
		$Qty=$this->_setQty_knitAndwoven_greyAndfinish($this->_By_OrderGmtscolorAndBodypart);
		return $Qty[$poId][$colorId][$bodyPartId];
	}
	
	public function getQtyArray_by_orderGmtscolorAndBodypart_knitAndwoven_greyAndfinish(){
		$Qty=$this->_setQty_knitAndwoven_greyAndfinish($this->_By_OrderGmtscolorAndBodypart);
		return $Qty;
	}
	
	public function getQty_by_orderGmtscolorAndBodypart_knitAndwoven_greyAndfinish_production($poId,$colorId,$bodyPartId){
		$Qty=$this->_setQty_knitAndwoven_greyAndfinish_production($this->_By_OrderGmtscolorAndBodypart);
		return $Qty[$poId][$colorId][$bodyPartId];
	}
	
	public function getQtyArray_by_orderGmtscolorAndBodypart_knitAndwoven_greyAndfinish_production(){
		$Qty=$this->_setQty_knitAndwoven_greyAndfinish_production($this->_By_OrderGmtscolorAndBodypart);
		return $Qty;
	}
	public function getQty_by_orderGmtscolorAndBodypart_knitAndwoven_greyAndfinish_purchase($poId,$colorId,$bodyPartId){
		$Qty=$this->_setQty_knitAndwoven_greyAndfinish_purchase($this->_By_OrderGmtscolorAndBodypart);
		return $Qty[$poId][$colorId][$bodyPartId];
	}
	
	public function getQtyArray_by_orderGmtscolorAndBodypart_knitAndwoven_greyAndfinish_purchase(){
		$Qty=$this->_setQty_knitAndwoven_greyAndfinish_purchase($this->_By_OrderGmtscolorAndBodypart);
		return $Qty;
	}
	
	public function getQty_by_orderGmtscolorAndBodypart_knitAndwoven_greyAndfinish_buyerSupplied($poId,$colorId,$bodyPartId){
		$Qty=$this->_setQty_knitAndwoven_greyAndfinish_buyerSupplied($this->_By_OrderGmtscolorAndBodypart);
		return $Qty[$poId][$colorId][$bodyPartId];
	}
	
	public function getQtyArray_by_orderGmtscolorAndBodypart_knitAndwoven_greyAndfinish_buyerSupplied(){
		$Qty=$this->_setQty_knitAndwoven_greyAndfinish_buyerSupplied($this->_By_OrderGmtscolorAndBodypart);
		return $Qty;
	}
	//Amount
	public function getAmount_by_orderGmtscolorAndBodypart_knitAndwoven_greyAndfinish($poId,$colorId,$bodyPartId){
		$Amount=$this->_setAmount_knitAndwoven_greyAndfinish($this->_By_OrderGmtscolorAndBodypart);
		return $Amount[$poId][$colorId][$bodyPartId];
	}
	public function getAmountArray_by_orderGmtscolorAndBodypart_knitAndwoven_greyAndfinish(){
		$Amount=$this->_setAmount_knitAndwoven_greyAndfinish($this->_By_OrderGmtscolorAndBodypart);
		return $Amount;
	}
	
	// Order,Country,Gmts Item  and Gmts Color wise
	//Qty
	public function getQty_by_orderCountryGmtsitemAndGmtscolor_knitAndwoven_greyAndfinish($poId,$countryId,$gmtsItem,$colorId){
		$Qty=$this->_setQty_knitAndwoven_greyAndfinish($this->_By_orderCountryGmtsitemAndGmtscolor);
		return $Qty[$poId][$countryId][$gmtsItem][$colorId];
	}
	
	public function getQtyArray_by_orderCountryGmtsitemAndGmtscolor_knitAndwoven_greyAndfinish(){
		$Qty=$this->_setQty_knitAndwoven_greyAndfinish($this->_By_orderCountryGmtsitemAndGmtscolor);
		return $Qty;
	}
	
	public function getQty_by_orderCountryGmtsitemAndGmtscolor_knitAndwoven_greyAndfinish_production($poId,$countryId,$gmtsItem,$colorId){
		$Qty=$this->_setQty_knitAndwoven_greyAndfinish_production($this->_By_orderCountryGmtsitemAndGmtscolor);
		return $Qty[$poId][$countryId][$gmtsItem][$colorId];
	}
	
	public function getQtyArray_by_orderCountryGmtsitemAndGmtscolor_knitAndwoven_greyAndfinish_production(){
		$Qty=$this->_setQty_knitAndwoven_greyAndfinish_production($this->_By_orderCountryGmtsitemAndGmtscolor);
		return $Qty;
	}
	public function getQty_by_orderCountryGmtsitemAndGmtscolor_knitAndwoven_greyAndfinish_purchase($poId,$countryId,$gmtsItem,$colorId){
		$Qty=$this->_setQty_knitAndwoven_greyAndfinish_purchase($this->_By_orderCountryGmtsitemAndGmtscolor);
		return $Qty[$poId][$countryId][$gmtsItem][$colorId];
	}
	
	public function getQtyArray_by_orderCountryGmtsitemAndGmtscolor_knitAndwoven_greyAndfinish_purchase(){
		$Qty=$this->_setQty_knitAndwoven_greyAndfinish_purchase($this->_By_orderCountryGmtsitemAndGmtscolor);
		return $Qty;
	}
	public function getQty_by_orderCountryGmtsitemAndGmtscolor_knitAndwoven_greyAndfinish_buyerSupplied($poId,$countryId,$gmtsItem,$colorId){
		$Qty=$this->_setQty_knitAndwoven_greyAndfinish_buyerSupplied($this->_By_orderCountryGmtsitemAndGmtscolor);
		return $Qty[$poId][$countryId][$gmtsItem][$colorId];
	}
	
	public function getQtyArray_by_orderCountryGmtsitemAndGmtscolor_knitAndwoven_greyAndfinish_buyerSupplied(){
		$Qty=$this->_setQty_knitAndwoven_greyAndfinish_buyerSupplied($this->_By_orderCountryGmtsitemAndGmtscolor);
		return $Qty;
	}
	//Amount
	public function getAmount_by_orderCountryGmtsitemAndGmtscolor_knitAndwoven_greyAndfinish($poId,$countryId,$gmtsItem,$colorId){
		$Amount=$this->_setAmount_knitAndwoven_greyAndfinish($this->_By_orderCountryGmtsitemAndGmtscolor);
		return $Amount[$poId][$countryId][$gmtsItem][$colorId];
	}
	public function getAmountArray_by_orderCountryGmtsitemAndGmtscolor_knitAndwoven_greyAndfinish(){
		$Amount=$this->_setAmount_knitAndwoven_greyAndfinish($this->_By_orderCountryGmtsitemAndGmtscolor);
		return $Amount;
	}
	
	// Order,Country,Gmts Item  and Gmts Size wise
	//Qty
	public function getQty_by_orderCountryGmtsitemAndGmtssize_knitAndwoven_greyAndfinish($poId,$countryId,$gmtsItem,$sizeId){
		$Qty=$this->_setQty_knitAndwoven_greyAndfinish($this->_By_orderCountryGmtsitemAndGmtssize);
		return $Qty[$poId][$countryId][$gmtsItem][$sizeId];
	}
	
	public function getQtyArray_by_orderCountryGmtsitemAndGmtssize_knitAndwoven_greyAndfinish(){
		$Qty=$this->_setQty_knitAndwoven_greyAndfinish($this->_By_orderCountryGmtsitemAndGmtssize);
		return $Qty;
	}
	
	public function getQty_by_orderCountryGmtsitemAndGmtssize_knitAndwoven_greyAndfinish_production($poId,$countryId,$gmtsItem,$sizeId){
		$Qty=$this->_setQty_knitAndwoven_greyAndfinish_production($this->_By_orderCountryGmtsitemAndGmtssize);
		return $Qty[$poId][$countryId][$gmtsItem][$sizeId];
	}
	
	public function getQtyArray_by_orderCountryGmtsitemAndGmtssize_knitAndwoven_greyAndfinish_production(){
		$Qty=$this->_setQty_knitAndwoven_greyAndfinish_production($this->_By_orderCountryGmtsitemAndGmtssize);
		return $Qty;
	}
	public function getQty_by_orderCountryGmtsitemAndGmtssize_knitAndwoven_greyAndfinish_purchase($poId,$countryId,$gmtsItem,$sizeId){
		$Qty=$this->_setQty_knitAndwoven_greyAndfinish_purchase($this->_By_orderCountryGmtsitemAndGmtssize);
		return $Qty[$poId][$countryId][$gmtsItem][$sizeId];
	}
	
	public function getQtyArray_by_orderCountryGmtsitemAndGmtssize_knitAndwoven_greyAndfinish_purchase(){
		$Qty=$this->_setQty_knitAndwoven_greyAndfinish_purchase($this->_By_orderCountryGmtsitemAndGmtssize);
		return $Qty;
	}
	public function getQty_by_orderCountryGmtsitemAndGmtssize_knitAndwoven_greyAndfinish_buyerSupplied($poId,$countryId,$gmtsItem,$sizeId){
		$Qty=$this->_setQty_knitAndwoven_greyAndfinish_buyerSupplied($this->_By_orderCountryGmtsitemAndGmtssize);
		return $Qty[$poId][$countryId][$gmtsItem][$sizeId];
	}
	
	public function getQtyArray_by_orderCountryGmtsitemAndGmtssize_knitAndwoven_greyAndfinish_buyerSupplied(){
		$Qty=$this->_setQty_knitAndwoven_greyAndfinish_buyerSupplied($this->_By_orderCountryGmtsitemAndGmtssize);
		return $Qty;
	}
	//Amount
	public function getAmount_by_orderCountryGmtsitemAndGmtssize_knitAndwoven_greyAndfinish($poId,$countryId,$gmtsItem,$sizeId){
		$Amount=$this->_setAmount_knitAndwoven_greyAndfinish($this->_By_orderCountryGmtsitemAndGmtssize);
		return $Amount[$poId][$countryId][$gmtsItem][$sizeId];
	}
	public function getAmountArray_by_orderCountryGmtsitemAndGmtssize_knitAndwoven_greyAndfinish(){
		$Amount=$this->_setAmount_knitAndwoven_greyAndfinish($this->_By_orderCountryGmtsitemAndGmtssize);
		return $Amount;
	}
	
	// Order,Country,Gmts Color  and Gmts Size wise
	//Qty
	public function getQty_by_orderCountryGmtscolorAndGmtssize_knitAndwoven_greyAndfinish($poId,$countryId,$colorId,$sizeId){
		$Qty=$this->_setQty_knitAndwoven_greyAndfinish($this->_By_orderCountryGmtscolorAndGmtssize);
		return $Qty[$poId][$countryId][$colorId][$sizeId];
	}
	
	public function getQtyArray_by_orderCountryGmtscolorAndGmtssize_knitAndwoven_greyAndfinish(){
		$Qty=$this->_setQty_knitAndwoven_greyAndfinish($this->_By_orderCountryGmtscolorAndGmtssize);
		return $Qty;
	}
	
	public function getQty_by_orderCountryGmtscolorAndGmtssize_knitAndwoven_greyAndfinish_production($poId,$countryId,$colorId,$sizeId){
		$Qty=$this->_setQty_knitAndwoven_greyAndfinish_production($this->_By_orderCountryGmtscolorAndGmtssize);
		return $Qty[$poId][$countryId][$colorId][$sizeId];
	}
	
	public function getQtyArray_by_orderCountryGmtscolorAndGmtssize_knitAndwoven_greyAndfinish_production(){
		$Qty=$this->_setQty_knitAndwoven_greyAndfinish_production($this->_By_orderCountryGmtscolorAndGmtssize);
		return $Qty;
	}
	public function getQty_by_orderCountryGmtscolorAndGmtssize_knitAndwoven_greyAndfinish_purchase($poId,$countryId,$colorId,$sizeId){
		$Qty=$this->_setQty_knitAndwoven_greyAndfinish_purchase($this->_By_orderCountryGmtscolorAndGmtssize);
		return $Qty[$poId][$countryId][$colorId][$sizeId];
	}
	
	public function getQtyArray_by_orderCountryGmtscolorAndGmtssize_knitAndwoven_greyAndfinish_purchase(){
		$Qty=$this->_setQty_knitAndwoven_greyAndfinish_purchase($this->_By_orderCountryGmtscolorAndGmtssize);
		return $Qty;
	}
	public function getQty_by_orderCountryGmtscolorAndGmtssize_knitAndwoven_greyAndfinish_buyerSupplied($poId,$countryId,$colorId,$sizeId){
		$Qty=$this->_setQty_knitAndwoven_greyAndfinish_buyerSupplied($this->_By_orderCountryGmtscolorAndGmtssize);
		return $Qty[$poId][$countryId][$colorId][$sizeId];
	}
	
	public function getQtyArray_by_orderCountryGmtscolorAndGmtssize_knitAndwoven_greyAndfinish_buyerSupplied(){
		$Qty=$this->_setQty_knitAndwoven_greyAndfinish_buyerSupplied($this->_By_orderCountryGmtscolorAndGmtssize);
		return $Qty;
	}
	//Amount
	public function getAmount_by_orderCountryGmtscolorAndGmtssize_knitAndwoven_greyAndfinish($poId,$countryId,$colorId,$sizeId){
		$Amount=$this->_setAmount_knitAndwoven_greyAndfinish($this->_By_orderCountryGmtscolorAndGmtssize);
		return $Amount[$poId][$countryId][$colorId][$sizeId];
	}
	public function getAmountArray_by_orderCountryGmtscolorAndGmtssize_knitAndwoven_greyAndfinish(){
		$Amount=$this->_setAmount_knitAndwoven_greyAndfinish($this->_By_orderCountryGmtscolorAndGmtssize);
		return $Amount;
	}
	
	// Order,Gmts item,Gmts Color  and Gmts Size wise
	//Qty
	public function getQty_by_orderGmtsitemGmtscolorAndGmtssize_knitAndwoven_greyAndfinish($poId,$gmtsItem,$colorId,$sizeId){
		$Qty=$this->_setQty_knitAndwoven_greyAndfinish($this->_By_orderGmtsitemGmtscolorAndGmtssize);
		return $Qty[$poId][$gmtsItem][$colorId][$sizeId];
	}
	
	public function getQtyArray_by_orderGmtsitemGmtscolorAndGmtssize_knitAndwoven_greyAndfinish(){
		$Qty=$this->_setQty_knitAndwoven_greyAndfinish($this->_By_orderGmtsitemGmtscolorAndGmtssize);
		return $Qty;
	}
	
	public function getQty_by_orderGmtsitemGmtscolorAndGmtssize_knitAndwoven_greyAndfinish_production($poId,$gmtsItem,$colorId,$sizeId){
		$Qty=$this->_setQty_knitAndwoven_greyAndfinish_production($this->_By_orderGmtsitemGmtscolorAndGmtssize);
		return $Qty[$poId][$gmtsItem][$colorId][$sizeId];
	}
	
	public function getQtyArray_by_orderGmtsitemGmtscolorAndGmtssize_knitAndwoven_greyAndfinish_production(){
		$Qty=$this->_setQty_knitAndwoven_greyAndfinish_production($this->_By_orderGmtsitemGmtscolorAndGmtssize);
		return $Qty;
	}
	public function getQty_by_orderGmtsitemGmtscolorAndGmtssize_knitAndwoven_greyAndfinish_purchase($poId,$gmtsItem,$colorId,$sizeId){
		$Qty=$this->_setQty_knitAndwoven_greyAndfinish_purchase($this->_By_orderGmtsitemGmtscolorAndGmtssize);
		return $Qty[$poId][$gmtsItem][$colorId][$sizeId];
	}
	
	public function getQtyArray_by_orderGmtsitemGmtscolorAndGmtssize_knitAndwoven_greyAndfinish_purchase(){
		$Qty=$this->_setQty_knitAndwoven_greyAndfinish_purchase($this->_By_orderGmtsitemGmtscolorAndGmtssize);
		return $Qty;
	}
	public function getQty_by_orderGmtsitemGmtscolorAndGmtssize_knitAndwoven_greyAndfinish_buyerSupplied($poId,$gmtsItem,$colorId,$sizeId){
		$Qty=$this->_setQty_knitAndwoven_greyAndfinish_buyerSupplied($this->_By_orderGmtsitemGmtscolorAndGmtssize);
		return $Qty[$poId][$gmtsItem][$colorId][$sizeId];
	}
	
	public function getQtyArray_by_orderGmtsitemGmtscolorAndGmtssize_knitAndwoven_greyAndfinish_buyerSupplied(){
		$Qty=$this->_setQty_knitAndwoven_greyAndfinish_buyerSupplied($this->_By_orderGmtsitemGmtscolorAndGmtssize);
		return $Qty;
	}
	//Amount
	public function getAmount_by_orderGmtsitemGmtscolorAndGmtssize_knitAndwoven_greyAndfinish($poId,$gmtsItem,$colorId,$sizeId){
		$Amount=$this->_setAmount_knitAndwoven_greyAndfinish($this->_By_orderGmtsitemGmtscolorAndGmtssize);
		return $Amount[$poId][$gmtsItem][$colorId][$sizeId];
	}
	public function getAmountArray_by_orderGmtsitemGmtscolorAndGmtssize_knitAndwoven_greyAndfinish(){
		$Amount=$this->_setAmount_knitAndwoven_greyAndfinish($this->_By_orderGmtsitemGmtscolorAndGmtssize);
		return $Amount;
	}
	
	// Order,Country,Gmts Item, Gmts Color and Gmts size wise
	//Qty
	public function getQty_by_orderCountryGmtsitemGmtscolorAndGmtssize_knitAndwoven_greyAndfinish($poId,$countryId,$gmtsItem,$colorId,$sizeId){
		$Qty=$this->_setQty_knitAndwoven_greyAndfinish($this->_By_orderCountryGmtsitemGmtscolorAndGmtssize);
		return $Qty[$poId][$countryId][$gmtsItem][$colorId][$sizeId];
	}
	
	public function getQtyArray_by_orderCountryGmtsitemGmtscolorAndGmtssize_knitAndwoven_greyAndfinish(){
		$Qty=$this->_setQty_knitAndwoven_greyAndfinish($this->_By_orderCountryGmtsitemGmtscolorAndGmtssize);
		return $Qty;
	}
	
	public function getQty_by_orderCountryGmtsitemGmtscolorAndGmtssize_knitAndwoven_greyAndfinish_production($poId,$countryId,$gmtsItem,$colorId,$sizeId){
		$Qty=$this->_setQty_knitAndwoven_greyAndfinish_production($this->_By_orderCountryGmtsitemGmtscolorAndGmtssize);
		return $Qty[$poId][$countryId][$gmtsItem][$colorId][$sizeId];
	}
	
	public function getQtyArray_by_orderCountryGmtsitemGmtscolorAndGmtssize_knitAndwoven_greyAndfinish_production(){
		$Qty=$this->_setQty_knitAndwoven_greyAndfinish_production($this->_By_orderCountryGmtsitemGmtscolorAndGmtssize);
		return $Qty;
	}
	public function getQty_by_orderCountryGmtsitemGmtscolorAndGmtssize_knitAndwoven_greyAndfinish_purchase($poId,$countryId,$gmtsItem,$colorId,$sizeId){
		$Qty=$this->_setQty_knitAndwoven_greyAndfinish_purchase($this->_By_orderCountryGmtsitemGmtscolorAndGmtssize);
		return $Qty[$poId][$countryId][$gmtsItem][$colorId][$sizeId];
	}
	
	public function getQtyArray_by_orderCountryGmtsitemGmtscolorAndGmtssize_knitAndwoven_greyAndfinish_purchase(){
		$Qty=$this->_setQty_knitAndwoven_greyAndfinish_purchase($this->_By_orderCountryGmtsitemGmtscolorAndGmtssize);
		return $Qty;
	}
	public function getQty_by_orderCountryGmtsitemGmtscolorAndGmtssize_knitAndwoven_greyAndfinish_buyerSupplied($poId,$countryId,$gmtsItem,$colorId,$sizeId){
		$Qty=$this->_setQty_knitAndwoven_greyAndfinish_buyerSupplied($this->_By_orderCountryGmtsitemGmtscolorAndGmtssize);
		return $Qty[$poId][$countryId][$gmtsItem][$colorId][$sizeId];
	}
	
	public function getQtyArray_by_orderCountryGmtsitemGmtscolorAndGmtssize_knitAndwoven_greyAndfinish_buyerSupplied(){
		$Qty=$this->_setQty_knitAndwoven_greyAndfinish_buyerSupplied($this->_By_orderCountryGmtsitemGmtscolorAndGmtssize);
		return $Qty;
	}
	//Amount
	public function getAmount_by_orderCountryGmtsitemGmtscolorAndGmtssize_knitAndwoven_greyAndfinish($poId,$countryId,$gmtsItem,$colorId,$sizeId){
		$Amount=$this->_setAmount_knitAndwoven_greyAndfinish($this->_By_orderCountryGmtsitemGmtscolorAndGmtssize);
		return $Amount[$poId][$countryId][$gmtsItem][$colorId][$sizeId];
	}
	public function getAmountArray_by_orderCountryGmtsitemGmtscolorAndGmtssize_knitAndwoven_greyAndfinish(){
		$Amount=$this->_setAmount_knitAndwoven_greyAndfinish($this->_By_orderCountryGmtsitemGmtscolorAndGmtssize);
		return $Amount;
	}
	//Qty
	public function getQtyArray_by_FabriccostidGmtsItemOrderAndGmtscolor_knitAndwoven_greyAndfinish(){
		$Qty=$this->_setQty_knitAndwoven_greyAndfinish($this->_By_FabriccostidGmtsItemOrderAndGmtscolor);
		return $Qty;
	}
	public function getQtyArray_by_FabriccostidAndGmtscolor_knitAndwoven_greyAndfinish(){
		$Qty=$this->_setQty_knitAndwoven_greyAndfinish($this->_By_FabriccostidAndGmtscolor);
		return $Qty;
	}
	public function getQtyArray_by_OrderLibYarnCountDeterIdAndGmtscolor_knitAndwoven_greyAndfinish(){
		$Qty=$this->_setQty_knitAndwoven_greyAndfinish($this->_By_OrderLibYarnCountDeterIdAndGmtscolor);
		return $Qty;
	}
	
	public function getQtyArray_by_OrderFabriccostidGmtscolorAndDiaWidth_knitAndwoven_greyAndfinish(){
		$Qty=$this->_setQty_knitAndwoven_greyAndfinish($this->_By_OrderFabriccostidGmtscolorAndDiaWidth);
		return $Qty;
	}
	
	public function getAmountArray_by_OrderFabriccostidGmtscolorAndDiaWidth_knitAndwoven_greyAndfinish(){
		$Amount=$this->_setAmount_knitAndwoven_greyAndfinish($this->_By_OrderFabriccostidGmtscolorAndDiaWidth);
		return $Amount;
	}
	
	
	public function getQtyArray_by_OrderFabriccostidGmtscolorDiaWidthAndRemarks_knitAndwoven_greyAndfinish(){
		$Qty=$this->_setQty_knitAndwoven_greyAndfinish($this->_By_OrderFabriccostidGmtscolorDiaWidthAndRemarks);
		return $Qty;
	}
	
	public function getAmountArray_by_OrderFabriccostidGmtscolorDiaWidthAndRemarks_knitAndwoven_greyAndfinish(){
		$Amount=$this->_setAmount_knitAndwoven_greyAndfinish($this->_By_OrderFabriccostidGmtscolorDiaWidthAndRemarks);
		return $Amount;
	}
	
	
	public function getQtyArray_by_orderAndFabriccostid_knitAndwoven_greyAndfinish(){
		$Qty=$this->_setQty_knitAndwoven_greyAndfinish($this->_By_OrderAndFabriccostid);
		return $Qty;
	}
	public function getAmountArray_by_orderAndFabriccostid_knitAndwoven_greyAndfinish(){
			$Amount=$this->_setAmount_knitAndwoven_greyAndfinish($this->_By_OrderAndFabriccostid);
			return $Amount;
		}
		
		
	public function getQtyArray_by_orderFabriccostidAndGmtscolor_knitAndwoven_greyAndfinish(){
		$Qty=$this->_setQty_knitAndwoven_greyAndfinish($this->_By_OrderFabriccostidAndGmtscolor);
		return $Qty;
	}
	public function getAmountArray_by_orderFabriccostidAndGmtscolor_knitAndwoven_greyAndfinish(){
			$Amount=$this->_setAmount_knitAndwoven_greyAndfinish($this->_By_OrderFabriccostidAndGmtscolor);
			return $Amount;
	}
		
		
	
	public function getQtyArray_by_OrderCountryAndFabriccostid_knitAndwoven_greyAndfinish(){
			$Qty=$this->_setQty_knitAndwoven_greyAndfinish($this->_By_OrderCountryAndFabriccostid);
			return $Qty;
		}
	public function getAmountArray_by_OrderCountryAndFabriccostid_knitAndwoven_greyAndfinish(){
			$Amount=$this->_setAmount_knitAndwoven_greyAndfinish($this->_By_OrderCountryAndFabriccostid);
			return $Amount;
		}
		
		
	public function getQtyArray_by_Fabriccostid_knitAndwoven_greyAndfinish(){
		$Qty=$this->_setQty_knitAndwoven_greyAndfinish($this->_By_Fabriccostid);
		return $Qty;
	}
	public function getAmountArray_by_Fabriccostid_knitAndwoven_greyAndfinish(){
			$Amount=$this->_setAmount_knitAndwoven_greyAndfinish($this->_By_Fabriccostid);
			return $Amount;
	}
	
	
	function __destruct() {
		parent::__destruct();
		unset($this->_dataArray);
	}
}
?>