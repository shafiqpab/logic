<?
class fabric extends report{
	private $_By_OrderGmtscolorAndBodypart='By_OrderGmtscolorAndBodypart';
	private $_By_FabriccostidGmtsItemOrderAndGmtscolor='By_FabriccostidGmtsItemOrderAndGmtscolor';
	private $_By_FabriccostidAndGmtscolor='By_FabriccostidAndGmtscolor';
	private $_By_FabriccostidAndFabriccolor='By_FabriccostidAndFabriccolor';
	private $_By_OrderLibYarnCountDeterIdAndGmtscolor='By_OrderLibYarnCountDeterIdAndGmtscolor';
	private $_By_OrderLibYarnCountDeterIdGmtscolorAndColorType='By_OrderLibYarnCountDeterIdGmtscolorAndColorType';
	private $_By_JobIdLibYarnCountDeterIdAndGmtscolor='By_JobIdLibYarnCountDeterIdAndGmtscolor';
	
	private $_By_OrderFabriccolorAndDeterminId='By_OrderFabriccolorAndDeterminId';
	
	private $_By_OrderFabricSourceAndBodyType='By_OrderFabricSourceAndBodyType';
	private $_By_OrderFabricSourceAndBodyTypeFabColor='By_OrderFabricSourceAndBodyTypeFabColor';
	
	private $_By_OrderFabricSourceBodyTypeFabColorDeterminIdGsmAndDiaWidth='By_OrderFabricSourceBodyTypeFabColorDeterminIdGsmAndDiaWidth';
	private $_By_OrderItemIdBodyIdColorDeterminIdGsmAndDiaWidth='By_OrderItemIdBodyIdColorDeterminIdGsmAndDiaWidth';
	 

	private $_By_OrderFabriccostidFabriccolorAndDiaWidth='By_OrderFabriccostidFabriccolorAndDiaWidth';
	private $_By_OrderGmtsColorFabricColorAndFabricSouce='By_OrderGmtsColorFabricColorAndFabricSouce';
	private $_By_OrderFabriccostidGmtscolorAndDiaWidth='By_OrderFabriccostidGmtscolorAndDiaWidth';
	
	private $_By_OrderFabriccostidGmtscolorGmtsSizeAndItemSize='By_OrderFabriccostidGmtscolorGmtsSizeAndItemSize';
	
	private $_By_OrderBodypartDeterminIdAndGmtscolor='By_OrderBodypartDeterminIdAndGmtscolor';
	private $_By_OrderBodypartDeterminIdAndDiaWidth='By_OrderBodypartDeterminIdAndDiaWidth';
	
	private $_By_OrderItemidCountrydateBodypartDeterminIdDiatypeDiaAndGsm='By_OrderItemidCountrydateBodypartDeterminIdDiatypeDiaAndGsm';
	
	private $_By_JobColorBodyTypeDeterminIdGsmAndDiaWidth='By_JobColorBodyTypeDeterminIdGsmAndDiaWidth';
	private $_By_JobColorFabricColorBodyTypeDeterminIdGsmAndDiaWidth='By_JobColorFabricColorBodyTypeDeterminIdGsmAndDiaWidth';
	private $_By_JobColorFabricColorGmtsSizeBodyTypeDeterminIdGsmAndDiaWidth='By_JobColorFabricColorGmtsSizeBodyTypeDeterminIdGsmAndDiaWidth';
	
	private $_By_JobColorFabricColorBodyPartIdDeterminIdGsmAndDiaWidth='By_JobColorFabricColorBodyPartIdDeterminIdGsmAndDiaWidth';
	private $_By_JobColorBodyTypeDeterminIdAndDiaWidth='By_JobColorBodyTypeDeterminIdAndDiaWidth';
	private $_By_JobColorFabColorBodyTypeDeterminIdAndDiaWidth='By_JobColorFabColorBodyTypeDeterminIdAndDiaWidth';
	private $_By_JobFabSourceColorFabColorBodyTypeDeterminIdAndDiaWidth='By_JobFabSourceColorFabColorBodyTypeDeterminIdAndDiaWidth';
	
	private $_By_OrderFabColorGsmDeterminIdAndDiaWidth='By_OrderFabColorGsmDeterminIdAndDiaWidth';
	private $_By_OrderFabColorGsmDeterminIdBodyPartAndDiaWidth='By_OrderFabColorGsmDeterminIdBodyPartAndDiaWidth';
	//job,po,fab color,yarn count deter id,dia,gsm
	private $_By_OrderFabriccostidGmtscolorDiaWidthAndRemarks='By_OrderFabriccostidGmtscolorDiaWidthAndRemarks';
	private $_By_OrderFabriccostidGmtscolorDiaWidthAndRemarksSourcing='By_OrderFabriccostidGmtscolorDiaWidthAndRemarksSourcing';
	private $_By_OrderAndFabriccostid='By_OrderAndFabriccostid';
	private $_By_OrderAndFabriccostidSupplier='By_OrderAndFabriccostidSupplier';
	private $_By_OrderAndConfirmedid='By_OrderAndConfirmedid';
	private $_By_OrderConfirmedidAndCountryid='By_OrderConfirmedidAndCountryid';
	private $_By_OrderCountryAndFabriccostid='By_OrderCountryAndFabriccostid';
	private $_By_Fabriccostid='By_Fabriccostid';
	private $_By_FabriccostidSourcing='By_FabriccostidSourcing';
	private $_By_OrderFabriccostidAndGmtscolor='By_OrderFabriccostidAndGmtscolor';
	private $_By_OrderColorTypeAndGmtscolor='By_OrderColorTypeAndGmtscolor';
	private $_By_OrderColorType='By_OrderColorType';
		
	private $_By_JobAndGmtscolor='By_JobAndGmtscolor';
	private $_By_CountryAndCutupdate='By_CountryAndCutupdate';
	private $_By_CountryCutupdateAndGmtsColor='By_CountryCutupdateAndGmtsColor';
	private $_By_OrderAndFabricSource='By_OrderAndFabricSource';
	private $_By_OrderAndColorTypeFabricSource='By_OrderAndColorTypeFabricSource';
	private $_By_OrderAndSourceId='By_OrderAndSourceId';
	private $_By_JobFabricIdItemBobyPartGmtsColorGsmDia='By_JobFabricIdItemBobyPartGmtsColorGsmDia';
	private $_knit='knit';
	private $_woven='woven';
	private $_sweater='sweater';
	private $_finish='finish';
	private $_grey='grey';
	
	private $_query="";
	private $_dataArray=array();
	private $_is_sweater=0;
	private $_is_approval_histry=0;
	
	// class constructor
	function __construct(condition $condition,$is_sweater=0,$is_approval_histry=0){
		parent::__construct($condition);
		$this->_is_sweater=$is_sweater;
		$this->_is_approval_histry=$is_approval_histry;
		$this->_setQuery();
		$this->_setData();

	}// end class constructor 
	
	private function _setQuery(){
		if($this->_is_approval_histry==2 && $this->_is_sweater==0 ){
			$this->_query='SELECT a.id AS "job_id", a.job_no AS "job_no", b.po_id AS "id", b.is_confirmed AS "is_confirmed", c.item_number_id AS "item_number_id", c.country_id AS "country_id", c.cutup_date AS "cutup_date", c.color_number_id AS "color_number_id", c.size_number_id AS "size_number_id", c.order_quantity AS "order_quantity", c.plan_cut_qnty AS "plan_cut_qnty", c.country_ship_date AS "country_ship_date", d.pre_cost_fabric_cost_dtls_id AS "pre_cost_dtls_id", d.body_part_id AS "body_part_id", d.body_part_type AS "body_part_type", d.fab_nature_id AS "fab_nature_id", d.color_type_id AS "color_type_id", d.fabric_source AS "fabric_source", d.source_id as "source_id", d.rate AS "drate", d.construction AS "construction", d.lib_yarn_count_deter_id AS "lib_yarn_count_deter_id", d.gsm_weight AS "gsm_weight", d.width_dia_type AS "width_dia_type", d.uom AS "uom", d.budget_on AS "budget_on", e.dia_width AS "dia_width", TRIM(e.remarks) AS "remarks", e.item_size AS "item_size", e.cons AS "cons", e.requirment AS "requirment", e.rate AS "rate", e.sourcing_rate AS "sourcing_rate", g.contrast_color_id AS "contrast_color_id", h.stripe_color "stripe_color", h.fabreq as "fabreq", d.nominated_supp_multi AS "nominated_supp" from wo_po_dtls_mst_his a join wo_po_break_down_his b on a.job_id=b.job_id and a.approved_no=b.approved_no and a.approval_page=b.approval_page join wo_po_color_size_his c on a.job_id=c.job_id and a.approved_no=c.approved_no and a.approval_page=c.approval_page and b.po_id=c.po_break_down_id join wo_pre_cost_fabric_cost_dtls_h d on a.job_id=d.job_id  and a.approved_no=d.approved_no and a.approval_page=d.approval_page and  c.item_number_id= d.item_number_id and d.is_deleted=0 and d.status_active=1 join wo_pre_fab_avg_con_dtls_h e on a.job_id=e.job_id and d.pre_cost_fabric_cost_dtls_id=e.pre_cost_fabric_cost_dtls_id and a.approved_no=e.approved_no and a.approval_page=e.approval_page and e.approval_page=d.approval_page and c.po_break_down_id=e.po_break_down_id and c.color_number_id=e.color_number_id and c.size_number_id=e.gmts_sizes and e.is_deleted=0 and e.status_active=1 and e.cons !=0 left join wo_pre_fab_concolor_dtls_h g on a.job_id=g.job_id and a.approved_no=g.approved_no and d.pre_cost_fabric_cost_dtls_id=g.pre_cost_fabric_cost_dtls_id and g.is_deleted=0 and g.status_active=1  and c.color_number_id=g.gmts_color_id and e.color_number_id =g.gmts_color_id left join wo_pre_stripe_color_h h on a.job_id=h.job_id and a.approved_no=h.approved_no and c.item_number_id= h.item_number_id and d.pre_cost_fabric_cost_dtls_id=h.pre_cost_fabric_cost_dtls_id and e.color_number_id =h.color_number_id and e.po_break_down_id=h.po_break_down_id and e.gmts_sizes=h.size_number_id where 1=1 '.$this->cond.' and e.cons !=0  and a.status_active=1 and b.is_deleted=0 and b.status_active=1 and c.is_deleted=0 and c.status_active=1';
		//order by b.id,d.id 
		}
		else{
		  if($this->_is_sweater==1){
			$this->_query='SELECT a.id AS "job_id", a.job_no AS "job_no", b.id AS "id", b.is_confirmed AS "is_confirmed", c.item_number_id AS "item_number_id", c.country_id AS "country_id", c.cutup_date AS "cutup_date", c.color_number_id AS "color_number_id", c.size_number_id AS "size_number_id", c.order_quantity AS "order_quantity", c.plan_cut_qnty AS "plan_cut_qnty", c.country_ship_date AS "country_ship_date", d.id AS "pre_cost_dtls_id", d.body_part_id AS "body_part_id", d.body_part_type AS "body_part_type", d.fab_nature_id AS "fab_nature_id", d.color_type_id AS "color_type_id", d.fabric_source AS "fabric_source", d.source_id as "source_id", d.rate AS "drate", d.construction AS "construction", d.lib_yarn_count_deter_id AS "lib_yarn_count_deter_id", d.gsm_weight AS "gsm_weight", d.width_dia_type AS "width_dia_type", d.uom AS "uom", d.budget_on AS "budget_on", e.dia_width AS "dia_width", TRIM(e.remarks) AS "remarks", e.item_size AS "item_size", e.cons AS "cons", e.requirment AS "requirment", e.rate AS "rate", e.sourcing_rate AS "sourcing_rate", g.contrast_color_id AS "contrast_color_id", h.stripe_color "stripe_color", h.fabreq as "fabreq", d.nominated_supp AS "nominated_supp" from wo_po_details_master a join wo_po_break_down b on a.id=b.job_id join wo_po_color_size_breakdown c on a.id=c.job_id and b.id=c.po_break_down_id join wo_pre_cost_fabric_cost_dtls d on a.id=d.job_id and  c.item_number_id= d.item_number_id and d.is_deleted=0 and d.status_active=1 join wo_pre_cos_fab_co_avg_con_dtls e on a.id=e.job_id and d.id=e.pre_cost_fabric_cost_dtls_id and c.po_break_down_id=e.po_break_down_id and c.color_number_id=e.color_number_id and c.size_number_id=e.gmts_sizes and e.is_deleted=0 and e.status_active=1 and e.cons !=0 left join wo_pre_cos_fab_co_color_dtls g on a.id=g.job_id and d.id=g.pre_cost_fabric_cost_dtls_id and g.is_deleted=0 and g.status_active=1  and c.color_number_id=g.gmts_color_id and e.color_number_id =g.gmts_color_id left join wo_pre_stripe_color h on a.id=h.job_id and c.item_number_id= h.item_number_id and d.id=h.pre_cost_fabric_cost_dtls_id and e.color_number_id =h.color_number_id and e.po_break_down_id=h.po_break_down_id and e.gmts_sizes=h.size_number_id where 1=1 '.$this->cond.' and e.cons !=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 and c.is_deleted=0 and c.status_active=1';
		//order by b.id,d.id
		}else{
			$this->_query='SELECT a.id AS "job_id", a.job_no AS "job_no", b.id AS "id", b.is_confirmed AS "is_confirmed", c.item_number_id AS "item_number_id", c.country_id AS "country_id", c.cutup_date AS "cutup_date", c.color_number_id AS "color_number_id", c.size_number_id AS "size_number_id", c.order_quantity AS "order_quantity", c.plan_cut_qnty AS "plan_cut_qnty", c.country_ship_date AS "country_ship_date", d.id AS "pre_cost_dtls_id", d.body_part_id AS "body_part_id", d.body_part_type AS "body_part_type", d.fab_nature_id AS "fab_nature_id", d.color_type_id AS "color_type_id", d.fabric_source AS "fabric_source", d.source_id as "source_id", d.rate AS "drate", d.construction AS "construction", d.lib_yarn_count_deter_id AS "lib_yarn_count_deter_id", d.gsm_weight AS "gsm_weight", d.width_dia_type AS "width_dia_type", d.uom AS "uom", d.budget_on AS "budget_on", e.dia_width AS "dia_width", TRIM(e.remarks) AS "remarks", e.item_size AS "item_size", e.cons AS "cons", e.requirment AS "requirment", e.rate AS "rate", e.sourcing_rate AS "sourcing_rate", g.contrast_color_id AS "contrast_color_id", h.stripe_color "stripe_color", h.fabreq as "fabreq", d.nominated_supp AS "nominated_supp" from wo_po_details_master a join wo_po_break_down b on a.id=b.job_id join wo_po_color_size_breakdown c on a.id=c.job_id and b.id=c.po_break_down_id join wo_pre_cost_fabric_cost_dtls d on a.id=d.job_id and  c.item_number_id= d.item_number_id and d.is_deleted=0 and d.status_active=1 join wo_pre_cos_fab_co_avg_con_dtls e on a.id=e.job_id and d.id=e.pre_cost_fabric_cost_dtls_id and c.po_break_down_id=e.po_break_down_id and c.color_number_id=e.color_number_id and c.size_number_id=e.gmts_sizes and e.is_deleted=0 and e.status_active=1 and e.cons !=0 left join wo_pre_cos_fab_co_color_dtls g on a.id=g.job_id and d.id=g.pre_cost_fabric_cost_dtls_id and g.is_deleted=0 and g.status_active=1  and c.color_number_id=g.gmts_color_id and e.color_number_id =g.gmts_color_id left join wo_pre_stripe_color h on a.id=h.job_id and c.item_number_id= h.item_number_id and d.id=h.pre_cost_fabric_cost_dtls_id and e.color_number_id =h.color_number_id and e.po_break_down_id=h.po_break_down_id and e.gmts_sizes=h.size_number_id where 1=1 '.$this->cond.' and e.cons !=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 and c.is_deleted=0 and c.status_active=1';
		}
	  }
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
		if($plan_cut_qnty && $costingPerQty && $set_item_ratio && $cons_qnty){
		  return $reqyarnqnty =($plan_cut_qnty/$set_item_ratio)*($cons_qnty/$costingPerQty);
		}
	}
	
	private function _calculateAmount($reqyarnqnty,$rate){
		if($reqyarnqnty && $rate){
			 return $amount=$reqyarnqnty*$rate;
		}
	}
	
	private function _setQty_knitAndwoven_greyAndfinish($level){
		$jobNo='';
		$jobId='';
		$poId='';$nominated_supp='';
		$itemNumberId='';$sourceId='';
		$countryId='';
		$cutupDate='';
		$colorId='';
		$colorTypeId='';
		$sizeId='';
		$planPutQnty=0;
		$orderQuantity=0;
		$preCostDtlsId='';
		$bodypartId='';
		$libYarnCountDeterId='';
		$gsmweight='';$bodyparttypeId='';
		$diaWidth='';$isConfirmed='';
		$remarks='';$item_size='';
		$cons=0;
		$requirment=0;
		$fabric_source='';
		$budget_on=2; $FabGmtColorId='';
		$Qty=array();
		
		foreach($this->_dataArray as $row)
		{
			$jobNo=$row['job_no'];
			$jobId=$row['job_id'];
			$poId=$row['id'];
			$nominated_supp=$row['nominated_supp'];
			$isConfirmed=$row['is_confirmed'];
			$itemNumberId=$row['item_number_id'];
			$item_size=$row['item_size'];
			$sourceId=$row['source_id'];
			$countryId=$row['country_id'];
			$cutupDate=$row['cutup_date'];
			$countryShipDate=$row['country_ship_date'];
			$widthDiaType=$row['width_dia_type'];
			$colorId=$row['color_number_id'];
			$FabricColorId=$row['contrast_color_id'];
			$colorTypeId=$row['color_type_id'];
			$sizeId=$row['size_number_id'];
			$planPutQnty=$row['plan_cut_qnty'];
			$orderQuantity=$row['order_quantity'];
			$preCostDtlsId=$row['pre_cost_dtls_id'];
			$bodypartId=$row['body_part_id'];
			$bodyparttypeId=$row['body_part_type'];
			$gsmweight=$row['gsm_weight'];
		
			if($FabricColorId=='')
			{
				$FabGmtColorId=$colorId;
			}
			else $FabGmtColorId=$FabricColorId;
			
			$fabNatureId=$row['fab_nature_id'];
			$libYarnCountDeterId=$row['lib_yarn_count_deter_id'];
			$diaWidth=$row['dia_width'];
			$remarks=$row['remarks'];
			$cons=$row['cons'];
			$requirment=$row['requirment'];
			$uom=$row['uom'];
			$fabric_source=$row['fabric_source'];
			$fabricSourceId=$row['fabric_source'];
			$budget_on=$row['budget_on'];
			if($budget_on==0 || $budget_on=="") $budget_on=2;
			if($this->_is_sweater==0){
				if($row['stripe_color']){
					$requirment=$row['fabreq'];
				}else{
					$requirment=$row['requirment'];
				}
			}
			
			$fabricColorId=$row['stripe_color'];
			
			if(!$fabricColorId){
				$fabricColorId=$row['contrast_color_id'];
			}
			if(!$fabricColorId){
				$fabricColorId=$row['color_number_id'];
			}
			
			$costingPerQty=$this->_costingPerQtyArr[$jobNo];
			$set_item_ratio=$this->_gmtsitemRatioArray[$jobNo][$itemNumberId];
			
			$reqqnty_finish_orderqty =$this->_calculateQty($orderQuantity,$costingPerQty,$set_item_ratio,$cons);
			$reqqnty_grey_orderqty =$this->_calculateQty($orderQuantity,$costingPerQty,$set_item_ratio,$requirment);
			if($budget_on==1)
			{
				$reqqnty_finish =$this->_calculateQty($orderQuantity,$costingPerQty,$set_item_ratio,$cons);
				$reqqnty_grey =$this->_calculateQty($orderQuantity,$costingPerQty,$set_item_ratio,$requirment);
			}
			else
			{
				$reqqnty_finish =$this->_calculateQty($planPutQnty,$costingPerQty,$set_item_ratio,$cons);
				$reqqnty_grey =$this->_calculateQty($planPutQnty,$costingPerQty,$set_item_ratio,$requirment);
			}
			
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
				elseif($fabNatureId==100){
					if(isset($Qty[$this->_sweater][$this->_finish][$jobNo][$uom])){
						$Qty[$this->_sweater][$this->_finish][$jobNo][$uom]+=$reqqnty_finish;
					}
					else{
						$Qty[$this->_sweater][$this->_finish][$jobNo][$uom]=$reqqnty_finish;
					}
					if(isset($Qty[$this->_sweater][$this->_grey][$jobNo][$uom])){
						$Qty[$this->_sweater][$this->_grey][$jobNo][$uom]+=$reqqnty_grey;
					}
					else{
						$Qty[$this->_sweater][$this->_grey][$jobNo][$uom]=$reqqnty_grey;
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
				elseif($fabNatureId==100){
					if(isset($Qty[$this->_sweater][$this->_finish][$poId][$uom])){
						$Qty[$this->_sweater][$this->_finish][$poId][$uom]+=$reqqnty_finish;
					}
					else{
						$Qty[$this->_sweater][$this->_finish][$poId][$uom]=$reqqnty_finish;
					}
					if(isset($Qty[$this->_sweater][$this->_grey][$poId][$uom])){
						$Qty[$this->_sweater][$this->_grey][$poId][$uom]+=$reqqnty_grey;
					}
					else{
						$Qty[$this->_sweater][$this->_grey][$poId][$uom]=$reqqnty_grey;
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
				elseif($fabNatureId==100){
					if(isset($Qty[$this->_sweater][$this->_finish][$poId][$countryId][$uom])){
						$Qty[$this->_sweater][$this->_finish][$poId][$countryId][$uom]+=$reqqnty_finish;
					}
					else{
						$Qty[$this->_sweater][$this->_finish][$poId][$countryId][$uom]=$reqqnty_finish;
					}
					if(isset($Qty[$this->_sweater][$this->_grey][$poId][$countryId][$uom])){
						$Qty[$this->_sweater][$this->_grey][$poId][$countryId][$uom]+=$reqqnty_grey;
					}
					else{
						$Qty[$this->_sweater][$this->_grey][$poId][$countryId][$uom]=$reqqnty_grey;
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
				elseif($fabNatureId==100){
					if(isset($Qty[$this->_sweater][$this->_finish][$poId][$itemNumberId][$uom])){
						$Qty[$this->_sweater][$this->_finish][$poId][$itemNumberId][$uom]+=$reqqnty_finish;
					}
					else{
						$Qty[$this->_sweater][$this->_finish][$poId][$itemNumberId][$uom]+=$reqqnty_finish;
					}
					if(isset($Qty[$this->_sweater][$this->_grey][$poId][$itemNumberId][$uom])){
						$Qty[$this->_sweater][$this->_grey][$poId][$itemNumberId][$uom]+=$reqqnty_grey;
					}
					else{
						$Qty[$this->_sweater][$this->_grey][$poId][$itemNumberId][$uom]=$reqqnty_grey;
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
				elseif($fabNatureId==100){
					if(isset($Qty[$this->_sweater][$this->_finish][$poId][$colorId][$uom])){
						$Qty[$this->_sweater][$this->_finish][$poId][$colorId][$uom]+=$reqqnty_finish;
					}
					else{
						$Qty[$this->_sweater][$this->_finish][$poId][$colorId][$uom]=$reqqnty_finish;
					}
					if(isset($Qty[$this->_sweater][$this->_finish][$poId][$colorId][$uom])){
						$Qty[$this->_sweater][$this->_grey][$poId][$colorId][$uom]+=$reqqnty_grey;
					}
					else{
						$Qty[$this->_sweater][$this->_grey][$poId][$colorId][$uom]=$reqqnty_grey;
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
				elseif($fabNatureId==100){
					if(isset($Qty[$this->_sweater][$this->_finish][$poId][$sizeId][$uom])){
						$Qty[$this->_sweater][$this->_finish][$poId][$sizeId][$uom]+=$reqqnty_finish;
					}
					else{
						$Qty[$this->_sweater][$this->_finish][$poId][$sizeId][$uom]=$reqqnty_finish;
					}
					if(isset($Qty[$this->_sweater][$this->_grey][$poId][$sizeId][$uom])){
						$Qty[$this->_sweater][$this->_grey][$poId][$sizeId][$uom]+=$reqqnty_grey;
					}
					else{
						$Qty[$this->_sweater][$this->_grey][$poId][$sizeId][$uom]=$reqqnty_grey;
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
				elseif($fabNatureId==100){
					if(isset($Qty[$this->_sweater][$this->_finish][$poId][$countryId][$itemNumberId][$uom])){
						$Qty[$this->_sweater][$this->_finish][$poId][$countryId][$itemNumberId][$uom]+=$reqqnty_finish;
					}
					else{
						$Qty[$this->_sweater][$this->_finish][$poId][$countryId][$itemNumberId][$uom]=$reqqnty_finish;
					}
					if(isset($Qty[$this->_sweater][$this->_grey][$poId][$countryId][$itemNumberId][$uom])){
						$Qty[$this->_sweater][$this->_grey][$poId][$countryId][$itemNumberId][$uom]+=$reqqnty_grey;
					}
					else{
						$Qty[$this->_sweater][$this->_grey][$poId][$countryId][$itemNumberId][$uom]=$reqqnty_grey;
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
				elseif($fabNatureId==100){
					if(isset($Qty[$this->_sweater][$this->_finish][$poId][$countryId][$colorId][$uom])){
						$Qty[$this->_sweater][$this->_finish][$poId][$countryId][$colorId][$uom]+=$reqqnty_finish;
					}
					else{
						$Qty[$this->_sweater][$this->_finish][$poId][$countryId][$colorId][$uom]=$reqqnty_finish;
					}
					if(isset($Qty[$this->_sweater][$this->_grey][$poId][$countryId][$colorId][$uom])){
						$Qty[$this->_sweater][$this->_grey][$poId][$countryId][$colorId][$uom]+=$reqqnty_grey;
					}
					else{
						$Qty[$this->_sweater][$this->_grey][$poId][$countryId][$colorId][$uom]=$reqqnty_grey;
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
				elseif($fabNatureId==100){
					if(isset($Qty[$this->_sweater][$this->_finish][$poId][$countryId][$sizeId][$uom])){
						$Qty[$this->_sweater][$this->_finish][$poId][$countryId][$sizeId][$uom]+=$reqqnty_finish;
					}
					else{
						$Qty[$this->_sweater][$this->_finish][$poId][$countryId][$sizeId][$uom]=$reqqnty_finish;
					}
					if(isset($Qty[$this->_sweater][$this->_grey][$poId][$countryId][$sizeId][$uom])){
						$Qty[$this->_sweater][$this->_grey][$poId][$countryId][$sizeId][$uom]+=$reqqnty_grey;
					}
					else{
						$Qty[$this->_sweater][$this->_grey][$poId][$countryId][$sizeId][$uom]=$reqqnty_grey;
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
				elseif($fabNatureId==100){
					if(isset($Qty[$this->_sweater][$this->_finish][$poId][$itemNumberId][$colorId][$uom])){
						$Qty[$this->_sweater][$this->_finish][$poId][$itemNumberId][$colorId][$uom]+=$reqqnty_finish;
					}
					else{
						$Qty[$this->_sweater][$this->_finish][$poId][$itemNumberId][$colorId][$uom]=$reqqnty_finish;
					}
					if(isset($Qty[$this->_sweater][$this->_grey][$poId][$itemNumberId][$colorId][$uom])){
						$Qty[$this->_sweater][$this->_grey][$poId][$itemNumberId][$colorId][$uom]+=$reqqnty_grey;
					}
					else{
						$Qty[$this->_sweater][$this->_grey][$poId][$itemNumberId][$colorId][$uom]=$reqqnty_grey;
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
				elseif($fabNatureId==100){
					if(isset($Qty[$this->_sweater][$this->_finish][$poId][$itemNumberId][$sizeId][$uom])){
						$Qty[$this->_sweater][$this->_finish][$poId][$itemNumberId][$sizeId][$uom]+=$reqqnty_finish;
					}
					else{
						$Qty[$this->_sweater][$this->_finish][$poId][$itemNumberId][$sizeId][$uom]=$reqqnty_finish;
					}
					if(isset($Qty[$this->_sweater][$this->_grey][$poId][$itemNumberId][$sizeId][$uom])){
						$Qty[$this->_sweater][$this->_grey][$poId][$itemNumberId][$sizeId][$uom]+=$reqqnty_grey;
					}
					else{
						$Qty[$this->_sweater][$this->_grey][$poId][$itemNumberId][$sizeId][$uom]=$reqqnty_grey;
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
				elseif($fabNatureId==100){
					if(isset($Qty[$this->_sweater][$this->_finish][$poId][$colorId][$sizeId][$uom])){
						$Qty[$this->_sweater][$this->_finish][$poId][$colorId][$sizeId][$uom]+=$reqqnty_finish;
					}
					else{
						$Qty[$this->_sweater][$this->_finish][$poId][$colorId][$sizeId][$uom]=$reqqnty_finish;
					}
					if(isset($Qty[$this->_sweater][$this->_grey][$poId][$colorId][$sizeId][$uom]))
					{
						$Qty[$this->_sweater][$this->_grey][$poId][$colorId][$sizeId][$uom]+=$reqqnty_grey;
					}
					else{
						$Qty[$this->_sweater][$this->_grey][$poId][$colorId][$sizeId][$uom]=$reqqnty_grey;
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
				elseif($fabNatureId==100){
					if(isset($Qty[$this->_sweater][$this->_finish][$poId][$colorId][$bodypartId][$uom])){
						$Qty[$this->_sweater][$this->_finish][$poId][$colorId][$bodypartId][$uom]+=$reqqnty_finish;
					}
					else{
						$Qty[$this->_sweater][$this->_finish][$poId][$colorId][$bodypartId][$uom]=$reqqnty_finish;
					}
					if(isset($Qty[$this->_sweater][$this->_grey][$poId][$colorId][$bodypartId][$uom]))
					{
						$Qty[$this->_sweater][$this->_grey][$poId][$colorId][$bodypartId][$uom]+=$reqqnty_grey;
					}
					else{
						$Qty[$this->_sweater][$this->_grey][$poId][$colorId][$bodypartId][$uom]=$reqqnty_grey;
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
				elseif($fabNatureId==100){
					if(isset($Qty[$this->_sweater][$this->_finish][$preCostDtlsId][$itemNumberId][$poId][$colorId][$uom])){
						$Qty[$this->_sweater][$this->_finish][$preCostDtlsId][$itemNumberId][$poId][$colorId][$uom]+=$reqqnty_finish;
					}
					else{
						$Qty[$this->_sweater][$this->_finish][$preCostDtlsId][$itemNumberId][$poId][$colorId][$uom]=$reqqnty_finish;
					}
					if(isset($Qty[$this->_sweater][$this->_grey][$preCostDtlsId][$itemNumberId][$poId][$colorId][$uom]))
					{
						$Qty[$this->_sweater][$this->_grey][$preCostDtlsId][$itemNumberId][$poId][$colorId][$uom]+=$reqqnty_grey;
					}
					else{
						$Qty[$this->_sweater][$this->_grey][$preCostDtlsId][$itemNumberId][$poId][$colorId][$uom]=$reqqnty_grey;
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
				elseif($fabNatureId==100){
					if(isset($Qty[$this->_sweater][$this->_finish][$preCostDtlsId][$colorId][$uom])){
						$Qty[$this->_sweater][$this->_finish][$preCostDtlsId][$colorId][$uom]+=$reqqnty_finish;
					}
					else{
						$Qty[$this->_sweater][$this->_finish][$preCostDtlsId][$colorId][$uom]=$reqqnty_finish;
					}
					if(isset($Qty[$this->_sweater][$this->_grey][$preCostDtlsId][$colorId][$uom]))
					{
						$Qty[$this->_sweater][$this->_grey][$preCostDtlsId][$colorId][$uom]+=$reqqnty_grey;
					}
					else{
						$Qty[$this->_sweater][$this->_grey][$preCostDtlsId][$colorId][$uom]=$reqqnty_grey;
					}
				}
			}
			elseif($level==$this->_By_FabriccostidAndFabriccolor){ // zakaria joy
				if($fabNatureId==2){
					if(isset($Qty[$this->_knit][$this->_finish]['order'][$preCostDtlsId][$fabricColorId])){
						$Qty[$this->_knit][$this->_finish]['order'][$preCostDtlsId][$fabricColorId]+=$reqqnty_finish_orderqty;
					}
					else{
						$Qty[$this->_knit][$this->_finish]['order'][$preCostDtlsId][$fabricColorId]=$reqqnty_finish_orderqty;
					}

					if(isset($Qty[$this->_knit][$this->_finish]['budget'][$preCostDtlsId][$fabricColorId])){
						$Qty[$this->_knit][$this->_finish]['budget'][$preCostDtlsId][$fabricColorId]+=$reqqnty_finish;
					}
					else{
						$Qty[$this->_knit][$this->_finish]['budget'][$preCostDtlsId][$fabricColorId]=$reqqnty_finish;
					}

					if(isset($Qty[$this->_knit][$this->_grey]['order'][$preCostDtlsId][$fabricColorId])){
						$Qty[$this->_knit][$this->_grey]['order'][$preCostDtlsId][$fabricColorId]+=$reqqnty_grey_orderqty;
					}
					else{
						$Qty[$this->_knit][$this->_grey]['order'][$preCostDtlsId][$fabricColorId]=$reqqnty_grey_orderqty;
					}

					if(isset($Qty[$this->_knit][$this->_grey]['budget'][$preCostDtlsId][$fabricColorId])){
						$Qty[$this->_knit][$this->_grey]['budget'][$preCostDtlsId][$fabricColorId]+=$reqqnty_grey;
					}
					else{
						$Qty[$this->_knit][$this->_grey]['budget'][$preCostDtlsId][$fabricColorId]=$reqqnty_grey;
					}
				}
				elseif($fabNatureId==3){
					if(isset($Qty[$this->_woven][$this->_finish]['order'][$preCostDtlsId][$fabricColorId])){
						$Qty[$this->_woven][$this->_finish]['order'][$preCostDtlsId][$fabricColorId]+=$reqqnty_finish_orderqty;
					}
					else{
						$Qty[$this->_woven][$this->_finish]['order'][$preCostDtlsId][$fabricColorId]=$reqqnty_finish_orderqty;
					}

					if(isset($Qty[$this->_woven][$this->_finish]['budget'][$preCostDtlsId][$fabricColorId])){
						$Qty[$this->_woven][$this->_finish]['budget'][$preCostDtlsId][$fabricColorId]+=$reqqnty_finish;
					}
					else{
						$Qty[$this->_woven][$this->_finish]['budget'][$preCostDtlsId][$fabricColorId]=$reqqnty_finish;
					}

					if(isset($Qty[$this->_woven][$this->_grey]['order'][$preCostDtlsId][$fabricColorId]))
					{
						$Qty[$this->_woven][$this->_grey]['order'][$preCostDtlsId][$fabricColorId]+=$reqqnty_grey_orderqty;
					}
					else{
						$Qty[$this->_woven][$this->_grey]['order'][$preCostDtlsId][$fabricColorId]=$reqqnty_grey_orderqty;
					}
					
					if(isset($Qty[$this->_woven][$this->_grey]['budget'][$preCostDtlsId][$fabricColorId]))
					{
						$Qty[$this->_woven][$this->_grey]['budget'][$preCostDtlsId][$fabricColorId]+=$reqqnty_grey;
					}
					else{
						$Qty[$this->_woven][$this->_grey]['budget'][$preCostDtlsId][$fabricColorId]=$reqqnty_grey;
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
				elseif($fabNatureId==100){
					if(isset($Qty[$this->_sweater][$this->_finish][$poId][$libYarnCountDeterId][$colorId][$uom])){
						$Qty[$this->_sweater][$this->_finish][$poId][$libYarnCountDeterId][$colorId][$uom]+=$reqqnty_finish;
					}
					else{
						$Qty[$this->_sweater][$this->_finish][$poId][$libYarnCountDeterId][$colorId][$uom]=$reqqnty_finish;
					}
					if(isset($Qty[$this->_sweater][$this->_grey][$poId][$libYarnCountDeterId][$colorId][$uom]))
					{
						$Qty[$this->_sweater][$this->_grey][$poId][$libYarnCountDeterId][$colorId][$uom]+=$reqqnty_grey;
					}
					else{
						$Qty[$this->_sweater][$this->_grey][$poId][$libYarnCountDeterId][$colorId][$uom]=$reqqnty_grey;
					}
				}
			}
			elseif($level==$this->_By_OrderLibYarnCountDeterIdGmtscolorAndColorType){
				if($fabNatureId==2){
					if(isset($Qty[$this->_knit][$this->_finish][$poId][$libYarnCountDeterId][$colorId][$colorTypeId][$uom])){
						$Qty[$this->_knit][$this->_finish][$poId][$libYarnCountDeterId][$colorId][$colorTypeId][$uom]+=$reqqnty_finish;
					}
					else{
						$Qty[$this->_knit][$this->_finish][$poId][$libYarnCountDeterId][$colorId][$colorTypeId][$uom]=$reqqnty_finish;
					}
					if(isset($Qty[$this->_knit][$this->_grey][$poId][$libYarnCountDeterId][$colorId][$colorTypeId][$uom])){
						$Qty[$this->_knit][$this->_grey][$poId][$libYarnCountDeterId][$colorId][$colorTypeId][$uom]+=$reqqnty_grey;
					}
					else{
						$Qty[$this->_knit][$this->_grey][$poId][$libYarnCountDeterId][$colorId][$colorTypeId][$uom]=$reqqnty_grey;
					}
				}
				elseif($fabNatureId==3){
					if(isset($Qty[$this->_woven][$this->_finish][$poId][$libYarnCountDeterId][$colorId][$colorTypeId][$uom])){
						$Qty[$this->_woven][$this->_finish][$poId][$libYarnCountDeterId][$colorId][$colorTypeId][$uom]+=$reqqnty_finish;
					}
					else{
						$Qty[$this->_woven][$this->_finish][$poId][$libYarnCountDeterId][$colorId][$colorTypeId][$uom]=$reqqnty_finish;
					}
					if(isset($Qty[$this->_woven][$this->_grey][$poId][$libYarnCountDeterId][$colorId][$colorTypeId][$uom]))
					{
						$Qty[$this->_woven][$this->_grey][$poId][$libYarnCountDeterId][$colorId][$colorTypeId][$uom]+=$reqqnty_grey;
					}
					else{
						$Qty[$this->_woven][$this->_grey][$poId][$libYarnCountDeterId][$colorId][$colorTypeId][$uom]=$reqqnty_grey;
					}
				}
				elseif($fabNatureId==100){
					if(isset($Qty[$this->_sweater][$this->_finish][$poId][$libYarnCountDeterId][$colorId][$colorTypeId][$uom])){
						$Qty[$this->_sweater][$this->_finish][$poId][$libYarnCountDeterId][$colorId][$colorTypeId][$uom]+=$reqqnty_finish;
					}
					else{
						$Qty[$this->_sweater][$this->_finish][$poId][$libYarnCountDeterId][$colorId][$colorTypeId][$uom]=$reqqnty_finish;
					}
					if(isset($Qty[$this->_sweater][$this->_grey][$poId][$libYarnCountDeterId][$colorId][$colorTypeId][$uom]))
					{
						$Qty[$this->_sweater][$this->_grey][$poId][$libYarnCountDeterId][$colorId][$colorTypeId][$uom]+=$reqqnty_grey;
					}
					else{
						$Qty[$this->_sweater][$this->_grey][$poId][$libYarnCountDeterId][$colorId][$colorTypeId][$uom]=$reqqnty_grey;
					}
				}
			}
			elseif($level==$this->_By_JobIdLibYarnCountDeterIdAndGmtscolor){
				if($fabNatureId==2){
					if(isset($Qty[$this->_knit][$this->_finish][$jobId][$libYarnCountDeterId][$colorId][$FabGmtColorId])){
						$Qty[$this->_knit][$this->_finish][$jobId][$libYarnCountDeterId][$colorId][$FabGmtColorId]+=$reqqnty_finish;
					}
					else{
						$Qty[$this->_knit][$this->_finish][$jobId][$libYarnCountDeterId][$colorId][$FabGmtColorId]+=$reqqnty_finish;
					}
					if(isset($Qty[$this->_knit][$this->_grey][$jobId][$libYarnCountDeterId][$colorId][$FabGmtColorId]))
					{
						$Qty[$this->_knit][$this->_grey][$jobId][$libYarnCountDeterId][$colorId][$FabGmtColorId]+=$reqqnty_grey;
					}
					else{
						$Qty[$this->_knit][$this->_grey][$jobId][$libYarnCountDeterId][$colorId][$FabGmtColorId]+=$reqqnty_grey;
					}
				}
				if($fabNatureId==3){
					if(isset($Qty[$this->_woven][$this->_finish][$jobId][$libYarnCountDeterId][$colorId][$FabGmtColorId])){
						$Qty[$this->_woven][$this->_finish][$jobId][$libYarnCountDeterId][$colorId][$FabGmtColorId]+=$reqqnty_finish;
					}
					else{
						$Qty[$this->_woven][$this->_finish][$jobId][$libYarnCountDeterId][$colorId][$FabGmtColorId]+=$reqqnty_finish;
					}
					if(isset($Qty[$this->_woven][$this->_grey][$jobId][$libYarnCountDeterId][$colorId][$FabGmtColorId]))
					{
						$Qty[$this->_woven][$this->_grey][$jobId][$libYarnCountDeterId][$colorId][$FabGmtColorId]+=$reqqnty_grey;
					}
					else{
						$Qty[$this->_woven][$this->_grey][$jobId][$libYarnCountDeterId][$colorId][$FabGmtColorId]+=$reqqnty_grey;
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
				elseif($fabNatureId==100){
					if(isset($Qty[$this->_sweater][$this->_finish][$poId][$preCostDtlsId][$colorId][$diaWidth][$uom])){
						$Qty[$this->_sweater][$this->_finish][$poId][$preCostDtlsId][$colorId][$diaWidth][$uom]+=$reqqnty_finish;
					}
					else{
						$Qty[$this->_sweater][$this->_finish][$poId][$preCostDtlsId][$colorId][$diaWidth][$uom]=$reqqnty_finish;
					}
					if(isset($Qty[$this->_sweater][$this->_grey][$poId][$preCostDtlsId][$colorId][$diaWidth][$uom]))
					{
						$Qty[$this->_sweater][$this->_grey][$poId][$preCostDtlsId][$colorId][$diaWidth][$uom]+=$reqqnty_grey;
					}
					else{
						$Qty[$this->_sweater][$this->_grey][$poId][$preCostDtlsId][$colorId][$diaWidth][$uom]=$reqqnty_grey;
					}
				}
			}
			elseif($level==$this->_By_OrderFabriccostidGmtscolorGmtsSizeAndItemSize){
				if($fabNatureId==2){
					if(isset($Qty[$this->_knit][$this->_finish][$poId][$preCostDtlsId][$colorId][$sizeId][$item_size][$uom])){
						$Qty[$this->_knit][$this->_finish][$poId][$preCostDtlsId][$colorId][$sizeId][$item_size][$uom]+=$reqqnty_finish;
					}
					else{
						$Qty[$this->_knit][$this->_finish][$poId][$preCostDtlsId][$colorId][$sizeId][$item_size][$uom]=$reqqnty_finish;
					}
					if(isset($Qty[$this->_knit][$this->_grey][$poId][$preCostDtlsId][$colorId][$sizeId][$item_size][$uom])){
						$Qty[$this->_knit][$this->_grey][$poId][$preCostDtlsId][$colorId][$sizeId][$item_size][$uom]+=$reqqnty_grey;
					}
					else{
						$Qty[$this->_knit][$this->_grey][$poId][$preCostDtlsId][$colorId][$sizeId][$item_size][$uom]=$reqqnty_grey;
					}
				}
				elseif($fabNatureId==3){
					if(isset($Qty[$this->_woven][$this->_finish][$poId][$preCostDtlsId][$colorId][$sizeId][$item_size][$uom])){
						$Qty[$this->_woven][$this->_finish][$poId][$preCostDtlsId][$colorId][$sizeId][$item_size][$uom]+=$reqqnty_finish;
					}
					else{
						$Qty[$this->_woven][$this->_finish][$poId][$preCostDtlsId][$colorId][$sizeId][$item_size][$uom]=$reqqnty_finish;
					}
					if(isset($Qty[$this->_woven][$this->_grey][$poId][$preCostDtlsId][$colorId][$sizeId][$item_size][$uom]))
					{
						$Qty[$this->_woven][$this->_grey][$poId][$preCostDtlsId][$colorId][$sizeId][$item_size][$uom]+=$reqqnty_grey;
					}
					else{
						$Qty[$this->_woven][$this->_grey][$poId][$preCostDtlsId][$colorId][$sizeId][$item_size][$uom]=$reqqnty_grey;
					}
				}
				elseif($fabNatureId==100){
					if(isset($Qty[$this->_sweater][$this->_finish][$poId][$preCostDtlsId][$colorId][$sizeId][$item_size][$uom])){
						$Qty[$this->_sweater][$this->_finish][$poId][$preCostDtlsId][$colorId][$sizeId][$item_size][$uom]+=$reqqnty_finish;
					}
					else{
						$Qty[$this->_sweater][$this->_finish][$poId][$preCostDtlsId][$colorId][$sizeId][$item_size][$uom]=$reqqnty_finish;
					}
					if(isset($Qty[$this->_sweater][$this->_grey][$poId][$preCostDtlsId][$colorId][$sizeId][$item_size][$uom]))
					{
						$Qty[$this->_sweater][$this->_grey][$poId][$preCostDtlsId][$colorId][$sizeId][$item_size][$uom]+=$reqqnty_grey;
					}
					else{
						$Qty[$this->_sweater][$this->_grey][$poId][$preCostDtlsId][$colorId][$sizeId][$item_size][$uom]=$reqqnty_grey;
					}
				}
			}
			elseif($level==$this->_By_OrderFabriccostidFabriccolorAndDiaWidth){
				if($fabNatureId==2){
					if(isset($Qty[$this->_knit][$this->_finish][$poId][$preCostDtlsId][$colorId][$fabricColorId][$diaWidth][$uom])){
						$Qty[$this->_knit][$this->_finish][$poId][$preCostDtlsId][$colorId][$fabricColorId][$diaWidth][$uom]+=$reqqnty_finish;
					}
					else{
						$Qty[$this->_knit][$this->_finish][$poId][$preCostDtlsId][$colorId][$fabricColorId][$diaWidth][$uom]=$reqqnty_finish;
					}
					if(isset($Qty[$this->_knit][$this->_grey][$poId][$preCostDtlsId][$colorId][$fabricColorId][$diaWidth][$uom])){
						$Qty[$this->_knit][$this->_grey][$poId][$preCostDtlsId][$colorId][$fabricColorId][$diaWidth][$uom]+=$reqqnty_grey;
					}
					else{
						$Qty[$this->_knit][$this->_grey][$poId][$preCostDtlsId][$colorId][$fabricColorId][$diaWidth][$uom]=$reqqnty_grey;
					}
				}
				elseif($fabNatureId==3){
					if(isset($Qty[$this->_woven][$this->_finish][$poId][$preCostDtlsId][$colorId][$fabricColorId][$diaWidth][$uom])){
						$Qty[$this->_woven][$this->_finish][$poId][$preCostDtlsId][$colorId][$fabricColorId][$diaWidth][$uom]+=$reqqnty_finish;
					}
					else{
						$Qty[$this->_woven][$this->_finish][$poId][$preCostDtlsId][$colorId][$fabricColorId][$diaWidth][$uom]=$reqqnty_finish;
					}
					if(isset($Qty[$this->_woven][$this->_grey][$poId][$preCostDtlsId][$colorId][$fabricColorId][$diaWidth][$uom]))
					{
						$Qty[$this->_woven][$this->_grey][$poId][$preCostDtlsId][$colorId][$fabricColorId][$diaWidth][$uom]+=$reqqnty_grey;
					}
					else{
						$Qty[$this->_woven][$this->_grey][$poId][$preCostDtlsId][$colorId][$fabricColorId][$diaWidth][$uom]=$reqqnty_grey;
					}
				}
				elseif($fabNatureId==100){
					if(isset($Qty[$this->_sweater][$this->_finish][$poId][$preCostDtlsId][$colorId][$fabricColorId][$diaWidth][$uom])){
						$Qty[$this->_sweater][$this->_finish][$poId][$preCostDtlsId][$colorId][$fabricColorId][$diaWidth][$uom]+=$reqqnty_finish;
					}
					else{
						$Qty[$this->_sweater][$this->_finish][$poId][$preCostDtlsId][$colorId][$fabricColorId][$diaWidth][$uom]=$reqqnty_finish;
					}
					if(isset($Qty[$this->_sweater][$this->_grey][$poId][$preCostDtlsId][$colorId][$fabricColorId][$diaWidth][$uom]))
					{
						$Qty[$this->_sweater][$this->_grey][$poId][$preCostDtlsId][$colorId][$fabricColorId][$diaWidth][$uom]+=$reqqnty_grey;
					}
					else{
						$Qty[$this->_sweater][$this->_grey][$poId][$preCostDtlsId][$colorId][$fabricColorId][$diaWidth][$uom]=$reqqnty_grey;
					}
				}
			}
			elseif($level==$this->_By_OrderFabriccolorAndDeterminId){
				if($fabNatureId==2){
					if(isset($Qty[$this->_knit][$this->_finish][$poId][$FabricColorId][$libYarnCountDeterId][$uom])){
						$Qty[$this->_knit][$this->_finish][$poId][$FabricColorId][$libYarnCountDeterId][$uom]+=$reqqnty_finish;
					}
					else{
						$Qty[$this->_knit][$this->_finish][$poId][$FabricColorId][$libYarnCountDeterId][$uom]=$reqqnty_finish;
					}
					if(isset($Qty[$this->_knit][$this->_grey][$poId][$FabricColorId][$libYarnCountDeterId][$uom])){
						$Qty[$this->_knit][$this->_grey][$poId][$FabricColorId][$libYarnCountDeterId][$uom]+=$reqqnty_grey;
					}
					else{
						$Qty[$this->_knit][$this->_grey][$poId][$FabricColorId][$libYarnCountDeterId][$uom]=$reqqnty_grey;
					}
				}
				elseif($fabNatureId==3){
					if(isset($Qty[$this->_woven][$this->_finish][$poId][$FabricColorId][$libYarnCountDeterId][$uom])){
						$Qty[$this->_woven][$this->_finish][$poId][$FabricColorId][$libYarnCountDeterId][$uom]+=$reqqnty_finish;
					}
					else{
						$Qty[$this->_woven][$this->_finish][$poId][$FabricColorId][$libYarnCountDeterId][$uom]=$reqqnty_finish;
					}
					if(isset($Qty[$this->_woven][$this->_grey][$poId][$FabricColorId][$libYarnCountDeterId][$uom]))
					{
						$Qty[$this->_woven][$this->_grey][$poId][$FabricColorId][$libYarnCountDeterId][$uom]+=$reqqnty_grey;
					}
					else{
						$Qty[$this->_woven][$this->_grey][$poId][$FabricColorId][$libYarnCountDeterId][$uom]=$reqqnty_grey;
					}
				}
				elseif($fabNatureId==100){
					if(isset($Qty[$this->_sweater][$this->_finish][$poId][$FabricColorId][$libYarnCountDeterId][$uom])){
						$Qty[$this->_sweater][$this->_finish][$poId][$FabricColorId][$libYarnCountDeterId][$uom]+=$reqqnty_finish;
					}
					else{
						$Qty[$this->_sweater][$this->_finish][$poId][$FabricColorId][$libYarnCountDeterId][$uom]=$reqqnty_finish;
					}
					if(isset($Qty[$this->_sweater][$this->_grey][$poId][$FabricColorId][$libYarnCountDeterId][$uom]))
					{
						$Qty[$this->_sweater][$this->_grey][$poId][$FabricColorId][$libYarnCountDeterId][$uom]+=$reqqnty_grey;
					}
					else{
						$Qty[$this->_sweater][$this->_grey][$poId][$FabricColorId][$libYarnCountDeterId][$uom]=$reqqnty_grey;
					}
				}
			}
			elseif($level==$this->_By_OrderFabricSourceAndBodyType){
				if($fabNatureId==2){
					if(isset($Qty[$this->_knit][$this->_finish][$poId][$fabricSourceId][$bodyparttypeId][$uom])){
						$Qty[$this->_knit][$this->_finish][$poId][$fabricSourceId][$bodyparttypeId][$uom]+=$reqqnty_finish;
					}
					else{
						$Qty[$this->_knit][$this->_finish][$poId][$fabricSourceId][$bodyparttypeId][$uom]=$reqqnty_finish;
					}
					if(isset($Qty[$this->_knit][$this->_grey][$poId][$fabricSourceId][$bodyparttypeId][$uom])){
						$Qty[$this->_knit][$this->_grey][$poId][$fabricSourceId][$bodyparttypeId][$uom]+=$reqqnty_grey;
					}
					else{
						$Qty[$this->_knit][$this->_grey][$poId][$fabricSourceId][$bodyparttypeId][$uom]=$reqqnty_grey;
					}
				}
				elseif($fabNatureId==3){
					if(isset($Qty[$this->_woven][$this->_finish][$poId][$fabricSourceId][$bodyparttypeId][$uom])){
						$Qty[$this->_woven][$this->_finish][$poId][$fabricSourceId][$bodyparttypeId][$uom]+=$reqqnty_finish;
					}
					else{
						$Qty[$this->_woven][$this->_finish][$poId][$fabricSourceId][$bodyparttypeId][$uom]=$reqqnty_finish;
					}
					if(isset($Qty[$this->_woven][$this->_grey][$poId][$fabricSourceId][$bodyparttypeId][$uom]))
					{
						$Qty[$this->_woven][$this->_grey][$poId][$fabricSourceId][$bodyparttypeId][$uom]+=$reqqnty_grey;
					}
					else{
						$Qty[$this->_woven][$this->_grey][$poId][$fabricSourceId][$bodyparttypeId][$uom]=$reqqnty_grey;
					}
				}
				elseif($fabNatureId==100){
					if(isset($Qty[$this->_sweater][$this->_finish][$poId][$fabricSourceId][$bodyparttypeId][$uom])){
						$Qty[$this->_sweater][$this->_finish][$poId][$fabricSourceId][$bodyparttypeId][$uom]+=$reqqnty_finish;
					}
					else{
						$Qty[$this->_sweater][$this->_finish][$poId][$fabricSourceId][$bodyparttypeId][$uom]=$reqqnty_finish;
					}
					if(isset($Qty[$this->_sweater][$this->_grey][$poId][$fabricSourceId][$bodyparttypeId][$uom]))
					{
						$Qty[$this->_sweater][$this->_grey][$poId][$fabricSourceId][$bodyparttypeId][$uom]+=$reqqnty_grey;
					}
					else{
						$Qty[$this->_sweater][$this->_grey][$poId][$fabricSourceId][$bodyparttypeId][$uom]=$reqqnty_grey;
					}
				}
			}
			elseif($level==$this->_By_OrderFabricSourceAndBodyTypeFabColor){
				if($fabNatureId==2){
					if(isset($Qty[$this->_knit][$this->_finish][$poId][$fabricSourceId][$bodyparttypeId][$FabGmtColorId][$uom])){
						$Qty[$this->_knit][$this->_finish][$poId][$fabricSourceId][$bodyparttypeId][$FabGmtColorId][$uom]+=$reqqnty_finish;
					}
					else{
						$Qty[$this->_knit][$this->_finish][$poId][$fabricSourceId][$bodyparttypeId][$FabGmtColorId][$uom]=$reqqnty_finish;
					}
					if(isset($Qty[$this->_knit][$this->_grey][$poId][$fabricSourceId][$bodyparttypeId][$FabGmtColorId][$uom])){
						$Qty[$this->_knit][$this->_grey][$poId][$fabricSourceId][$bodyparttypeId][$FabGmtColorId][$uom]+=$reqqnty_grey;
					}
					else{
						$Qty[$this->_knit][$this->_grey][$poId][$fabricSourceId][$bodyparttypeId][$FabGmtColorId][$uom]=$reqqnty_grey;
					}
				}
				elseif($fabNatureId==3){
					if(isset($Qty[$this->_woven][$this->_finish][$poId][$fabricSourceId][$bodyparttypeId][$FabGmtColorId][$uom])){
						$Qty[$this->_woven][$this->_finish][$poId][$fabricSourceId][$bodyparttypeId][$FabGmtColorId][$uom]+=$reqqnty_finish;
					}
					else{
						$Qty[$this->_woven][$this->_finish][$poId][$fabricSourceId][$bodyparttypeId][$FabGmtColorId][$uom]=$reqqnty_finish;
					}
					if(isset($Qty[$this->_woven][$this->_grey][$poId][$fabricSourceId][$bodyparttypeId][$FabGmtColorId][$uom]))
					{
						$Qty[$this->_woven][$this->_grey][$poId][$fabricSourceId][$bodyparttypeId][$FabGmtColorId][$uom]+=$reqqnty_grey;
					}
					else{
						$Qty[$this->_woven][$this->_grey][$poId][$fabricSourceId][$bodyparttypeId][$FabGmtColorId][$uom]=$reqqnty_grey;
					}
				}
				elseif($fabNatureId==100){
					if(isset($Qty[$this->_sweater][$this->_finish][$poId][$fabricSourceId][$bodyparttypeId][$FabGmtColorId][$uom])){
						$Qty[$this->_sweater][$this->_finish][$poId][$fabricSourceId][$bodyparttypeId][$FabGmtColorId][$uom]+=$reqqnty_finish;
					}
					else{
						$Qty[$this->_sweater][$this->_finish][$poId][$fabricSourceId][$bodyparttypeId][$FabGmtColorId][$uom]=$reqqnty_finish;
					}
					if(isset($Qty[$this->_sweater][$this->_grey][$poId][$fabricSourceId][$bodyparttypeId][$FabGmtColorId][$uom]))
					{
						$Qty[$this->_sweater][$this->_grey][$poId][$fabricSourceId][$bodyparttypeId][$FabGmtColorId][$uom]+=$reqqnty_grey;
					}
					else{
						$Qty[$this->_sweater][$this->_grey][$poId][$fabricSourceId][$bodyparttypeId][$FabGmtColorId][$uom]=$reqqnty_grey;
					}
				}
			}
			
			elseif($level==$this->_By_OrderFabricSourceBodyTypeFabColorDeterminIdGsmAndDiaWidth){
				if($fabNatureId==2){
					if(isset($Qty[$this->_knit][$this->_finish][$poId][$fabricSourceId][$bodyparttypeId][$FabGmtColorId][$libYarnCountDeterId][$gsmweight][$diaWidth][$uom])){
						$Qty[$this->_knit][$this->_finish][$poId][$fabricSourceId][$bodyparttypeId][$FabGmtColorId][$libYarnCountDeterId][$gsmweight][$diaWidth][$uom]+=$reqqnty_finish;
					}
					else{
						$Qty[$this->_knit][$this->_finish][$poId][$fabricSourceId][$bodyparttypeId][$FabGmtColorId][$libYarnCountDeterId][$gsmweight][$diaWidth][$uom]=$reqqnty_finish;
					}
					if(isset($Qty[$this->_knit][$this->_grey][$poId][$fabricSourceId][$bodyparttypeId][$FabGmtColorId][$libYarnCountDeterId][$gsmweight][$diaWidth][$uom])){
						$Qty[$this->_knit][$this->_grey][$poId][$fabricSourceId][$bodyparttypeId][$FabGmtColorId][$libYarnCountDeterId][$gsmweight][$diaWidth][$uom]+=$reqqnty_grey;
					}
					else{
						$Qty[$this->_knit][$this->_grey][$poId][$fabricSourceId][$bodyparttypeId][$FabGmtColorId][$libYarnCountDeterId][$gsmweight][$diaWidth][$uom]=$reqqnty_grey;
					}
				}
				elseif($fabNatureId==3){
					if(isset($Qty[$this->_woven][$this->_finish][$poId][$fabricSourceId][$bodyparttypeId][$FabGmtColorId][$libYarnCountDeterId][$gsmweight][$diaWidth][$uom])){
						$Qty[$this->_woven][$this->_finish][$poId][$fabricSourceId][$bodyparttypeId][$FabGmtColorId][$libYarnCountDeterId][$gsmweight][$diaWidth][$uom]+=$reqqnty_finish;
					}
					else{
						$Qty[$this->_woven][$this->_finish][$poId][$fabricSourceId][$bodyparttypeId][$FabGmtColorId][$libYarnCountDeterId][$gsmweight][$diaWidth][$uom]=$reqqnty_finish;
					}
					if(isset($Qty[$this->_woven][$this->_grey][$poId][$fabricSourceId][$bodyparttypeId][$FabGmtColorId][$libYarnCountDeterId][$gsmweight][$diaWidth][$uom]))
					{
						$Qty[$this->_woven][$this->_grey][$poId][$fabricSourceId][$bodyparttypeId][$FabGmtColorId][$libYarnCountDeterId][$gsmweight][$diaWidth][$uom]+=$reqqnty_grey;
					}
					else{
						$Qty[$this->_woven][$this->_grey][$poId][$fabricSourceId][$bodyparttypeId][$FabGmtColorId][$libYarnCountDeterId][$gsmweight][$diaWidth][$uom]=$reqqnty_grey;
					}
				}
				elseif($fabNatureId==100){
					if(isset($Qty[$this->_sweater][$this->_finish][$poId][$fabricSourceId][$bodyparttypeId][$FabGmtColorId][$libYarnCountDeterId][$gsmweight][$diaWidth][$uom])){
						$Qty[$this->_sweater][$this->_finish][$poId][$fabricSourceId][$bodyparttypeId][$FabGmtColorId][$libYarnCountDeterId][$gsmweight][$diaWidth][$uom]+=$reqqnty_finish;
					}
					else{
						$Qty[$this->_sweater][$this->_finish][$poId][$fabricSourceId][$bodyparttypeId][$FabGmtColorId][$libYarnCountDeterId][$gsmweight][$diaWidth][$uom]=$reqqnty_finish;
					}
					if(isset($Qty[$this->_sweater][$this->_grey][$poId][$fabricSourceId][$bodyparttypeId][$FabGmtColorId][$libYarnCountDeterId][$gsmweight][$diaWidth][$uom]))
					{
						$Qty[$this->_sweater][$this->_grey][$poId][$fabricSourceId][$bodyparttypeId][$FabGmtColorId][$libYarnCountDeterId][$gsmweight][$diaWidth][$uom]+=$reqqnty_grey;
					}
					else{
						$Qty[$this->_sweater][$this->_grey][$poId][$fabricSourceId][$bodyparttypeId][$FabGmtColorId][$libYarnCountDeterId][$gsmweight][$diaWidth][$uom]=$reqqnty_grey;
					}
				}
			}
			elseif($level==$this->_By_OrderItemIdBodyIdColorDeterminIdGsmAndDiaWidth){
				if($fabNatureId==2){
					if(isset($Qty[$this->_knit][$this->_finish][$poId][$itemNumberId][$bodypartId][$colorId][$libYarnCountDeterId][$gsmweight][$diaWidth][$uom])){
						$Qty[$this->_knit][$this->_finish][$poId][$itemNumberId][$bodypartId][$colorId][$libYarnCountDeterId][$gsmweight][$diaWidth][$uom]+=$reqqnty_finish;
					}
					else{
						$Qty[$this->_knit][$this->_finish][$poId][$itemNumberId][$bodypartId][$colorId][$libYarnCountDeterId][$gsmweight][$diaWidth][$uom]=$reqqnty_finish;
					}
					if(isset($Qty[$this->_knit][$this->_grey][$poId][$itemNumberId][$bodypartId][$colorId][$libYarnCountDeterId][$gsmweight][$diaWidth][$uom])){
						$Qty[$this->_knit][$this->_grey][$poId][$itemNumberId][$bodypartId][$colorId][$libYarnCountDeterId][$gsmweight][$diaWidth][$uom]+=$reqqnty_grey;
					}
					else{
						$Qty[$this->_knit][$this->_grey][$poId][$itemNumberId][$bodypartId][$colorId][$libYarnCountDeterId][$gsmweight][$diaWidth][$uom]=$reqqnty_grey;
					}
				}
				elseif($fabNatureId==3){
					if(isset($Qty[$this->_woven][$this->_finish][$poId][$itemNumberId][$bodypartId][$colorId][$libYarnCountDeterId][$gsmweight][$diaWidth][$uom])){
						$Qty[$this->_woven][$this->_finish][$poId][$itemNumberId][$bodypartId][$colorId][$libYarnCountDeterId][$gsmweight][$diaWidth][$uom]+=$reqqnty_finish;
					}
					else{
						$Qty[$this->_woven][$this->_finish][$poId][$itemNumberId][$bodypartId][$colorId][$libYarnCountDeterId][$gsmweight][$diaWidth][$uom]=$reqqnty_finish;
					}
					if(isset($Qty[$this->_woven][$this->_grey][$poId][$itemNumberId][$bodypartId][$colorId][$libYarnCountDeterId][$gsmweight][$diaWidth][$uom]))
					{
						$Qty[$this->_woven][$this->_grey][$poId][$itemNumberId][$bodypartId][$colorId][$libYarnCountDeterId][$gsmweight][$diaWidth][$uom]+=$reqqnty_grey;
					}
					else{
						$Qty[$this->_woven][$this->_grey][$poId][$itemNumberId][$bodypartId][$colorId][$libYarnCountDeterId][$gsmweight][$diaWidth][$uom]=$reqqnty_grey;
					}
				}
				elseif($fabNatureId==100){
					if(isset($Qty[$this->_sweater][$this->_finish][$poId][$itemNumberId][$bodypartId][$colorId][$libYarnCountDeterId][$gsmweight][$diaWidth][$uom])){
						$Qty[$this->_sweater][$this->_finish][$poId][$itemNumberId][$bodypartId][$colorId][$libYarnCountDeterId][$gsmweight][$diaWidth][$uom]+=$reqqnty_finish;
					}
					else{
						$Qty[$this->_sweater][$this->_finish][$poId][$itemNumberId][$bodypartId][$colorId][$libYarnCountDeterId][$gsmweight][$diaWidth][$uom]=$reqqnty_finish;
					}
					if(isset($Qty[$this->_sweater][$this->_grey][$poId][$itemNumberId][$bodypartId][$colorId][$libYarnCountDeterId][$gsmweight][$diaWidth][$uom]))
					{
						$Qty[$this->_sweater][$this->_grey][$poId][$itemNumberId][$bodypartId][$colorId][$libYarnCountDeterId][$gsmweight][$diaWidth][$uom]+=$reqqnty_grey;
					}
					else{
						$Qty[$this->_sweater][$this->_grey][$poId][$itemNumberId][$bodypartId][$colorId][$libYarnCountDeterId][$gsmweight][$diaWidth][$uom]=$reqqnty_grey;
					}
				}
			}
			elseif($level==$this->_By_OrderGmtsColorFabricColorAndFabricSouce){
				if($fabNatureId==2){
					if(isset($Qty[$this->_knit][$this->_finish][$poId][$colorId][$FabricColorId][$fabricSourceId][$uom])){
						$Qty[$this->_knit][$this->_finish][$poId][$colorId][$FabricColorId][$fabricSourceId][$uom]+=$reqqnty_finish;
					}
					else{
						$Qty[$this->_knit][$this->_finish][$poId][$colorId][$FabricColorId][$fabricSourceId][$uom]=$reqqnty_finish;
					}
					if(isset($Qty[$this->_knit][$this->_grey][$poId][$colorId][$FabricColorId][$fabricSourceId][$uom])){
						$Qty[$this->_knit][$this->_grey][$poId][$colorId][$FabricColorId][$fabricSourceId][$uom]+=$reqqnty_grey;
					}
					else{
						$Qty[$this->_knit][$this->_grey][$poId][$colorId][$FabricColorId][$fabricSourceId][$uom]=$reqqnty_grey;
					}
				}
				elseif($fabNatureId==3){
					if(isset($Qty[$this->_woven][$this->_finish][$poId][$colorId][$FabricColorId][$fabricSourceId][$uom])){
						$Qty[$this->_woven][$this->_finish][$poId][$colorId][$FabricColorId][$fabricSourceId][$uom]+=$reqqnty_finish;
					}
					else{
						$Qty[$this->_woven][$this->_finish][$poId][$colorId][$FabricColorId][$fabricSourceId][$uom]=$reqqnty_finish;
					}
					if(isset($Qty[$this->_woven][$this->_grey][$poId][$colorId][$FabricColorId][$fabricSourceId][$uom]))
					{
						$Qty[$this->_woven][$this->_grey][$poId][$colorId][$FabricColorId][$fabricSourceId][$uom]+=$reqqnty_grey;
					}
					else{
						$Qty[$this->_woven][$this->_grey][$poId][$colorId][$FabricColorId][$fabricSourceId][$uom]=$reqqnty_grey;
					}
				}
				elseif($fabNatureId==100){
					if(isset($Qty[$this->_sweater][$this->_finish][$poId][$colorId][$FabricColorId][$fabricSourceId][$uom])){
						$Qty[$this->_sweater][$this->_finish][$poId][$colorId][$FabricColorId][$fabricSourceId][$uom]+=$reqqnty_finish;
					}
					else{
						$Qty[$this->_sweater][$this->_finish][$poId][$colorId][$FabricColorId][$fabricSourceId][$uom]=$reqqnty_finish;
					}
					if(isset($Qty[$this->_sweater][$this->_grey][$poId][$colorId][$FabricColorId][$fabricSourceId][$uom]))
					{
						$Qty[$this->_sweater][$this->_grey][$poId][$colorId][$FabricColorId][$fabricSourceId][$uom]+=$reqqnty_grey;
					}
					else{
						$Qty[$this->_sweater][$this->_grey][$poId][$colorId][$FabricColorId][$fabricSourceId][$uom]=$reqqnty_grey;
					}
				}
			}
			elseif($level==$this->_By_OrderBodypartDeterminIdAndGmtscolor){
				if($fabNatureId==2){
					if(isset($Qty[$this->_knit][$this->_finish][$poId][$bodypartId][$libYarnCountDeterId][$fabricColorId][$uom])){
						$Qty[$this->_knit][$this->_finish][$poId][$bodypartId][$libYarnCountDeterId][$fabricColorId][$uom]+=$reqqnty_finish;
					}
					else{
						$Qty[$this->_knit][$this->_finish][$poId][$bodypartId][$libYarnCountDeterId][$fabricColorId][$uom]=$reqqnty_finish;
					}
					if(isset($Qty[$this->_knit][$this->_grey][$poId][$bodypartId][$libYarnCountDeterId][$fabricColorId][$uom])){
						$Qty[$this->_knit][$this->_grey][$poId][$bodypartId][$libYarnCountDeterId][$fabricColorId][$uom]+=$reqqnty_grey;
					}
					else{
						$Qty[$this->_knit][$this->_grey][$poId][$bodypartId][$libYarnCountDeterId][$fabricColorId][$uom]=$reqqnty_grey;
					}
				}
				elseif($fabNatureId==3){
					if(isset($Qty[$this->_woven][$this->_finish][$poId][$bodypartId][$libYarnCountDeterId][$fabricColorId][$uom])){
						$Qty[$this->_woven][$this->_finish][$poId][$bodypartId][$libYarnCountDeterId][$fabricColorId][$uom]+=$reqqnty_finish;
					}
					else{
						$Qty[$this->_woven][$this->_finish][$poId][$bodypartId][$libYarnCountDeterId][$fabricColorId][$uom]=$reqqnty_finish;
					}
					if(isset($Qty[$this->_woven][$this->_grey][$poId][$bodypartId][$libYarnCountDeterId][$fabricColorId][$uom]))
					{
						$Qty[$this->_woven][$this->_grey][$poId][$bodypartId][$libYarnCountDeterId][$fabricColorId][$uom]+=$reqqnty_grey;
					}
					else{
						$Qty[$this->_woven][$this->_grey][$poId][$bodypartId][$libYarnCountDeterId][$fabricColorId][$uom]=$reqqnty_grey;
					}
				}
				elseif($fabNatureId==100){
					if(isset($Qty[$this->_sweater][$this->_finish][$poId][$bodypartId][$libYarnCountDeterId][$fabricColorId][$uom])){
						$Qty[$this->_sweater][$this->_finish][$poId][$bodypartId][$libYarnCountDeterId][$fabricColorId][$uom]+=$reqqnty_finish;
					}
					else{
						$Qty[$this->_sweater][$this->_finish][$poId][$bodypartId][$libYarnCountDeterId][$fabricColorId][$uom]=$reqqnty_finish;
					}
					if(isset($Qty[$this->_sweater][$this->_grey][$poId][$bodypartId][$libYarnCountDeterId][$fabricColorId][$uom]))
					{
						$Qty[$this->_sweater][$this->_grey][$poId][$bodypartId][$libYarnCountDeterId][$fabricColorId][$uom]+=$reqqnty_grey;
					}
					else{
						$Qty[$this->_sweater][$this->_grey][$poId][$bodypartId][$libYarnCountDeterId][$fabricColorId][$uom]=$reqqnty_grey;
					}
				}
			}
			
			elseif($level==$this->_By_OrderBodypartDeterminIdAndDiaWidth){//By_OrderItemidCountrydateBodypartDeterminIdDiatypeDiaAndGsm
				if($fabNatureId==2){
					if(isset($Qty[$this->_knit][$this->_finish][$poId][$bodypartId][$libYarnCountDeterId][$diaWidth][$uom])){
						$Qty[$this->_knit][$this->_finish][$poId][$bodypartId][$libYarnCountDeterId][$diaWidth][$uom]+=$reqqnty_finish;
					}
					else{
						$Qty[$this->_knit][$this->_finish][$poId][$bodypartId][$libYarnCountDeterId][$diaWidth][$uom]=$reqqnty_finish;
					}
					if(isset($Qty[$this->_knit][$this->_grey][$poId][$bodypartId][$libYarnCountDeterId][$diaWidth][$uom])){
						$Qty[$this->_knit][$this->_grey][$poId][$bodypartId][$libYarnCountDeterId][$diaWidth][$uom]+=$reqqnty_grey;
					}
					else{
						$Qty[$this->_knit][$this->_grey][$poId][$bodypartId][$libYarnCountDeterId][$diaWidth][$uom]=$reqqnty_grey;
					}
				}
				elseif($fabNatureId==3){
					if(isset($Qty[$this->_woven][$this->_finish][$poId][$bodypartId][$libYarnCountDeterId][$diaWidth][$uom])){
						$Qty[$this->_woven][$this->_finish][$poId][$bodypartId][$libYarnCountDeterId][$diaWidth][$uom]+=$reqqnty_finish;
					}
					else{
						$Qty[$this->_woven][$this->_finish][$poId][$bodypartId][$libYarnCountDeterId][$diaWidth][$uom]=$reqqnty_finish;
					}
					if(isset($Qty[$this->_woven][$this->_grey][$poId][$bodypartId][$libYarnCountDeterId][$diaWidth][$uom]))
					{
						$Qty[$this->_woven][$this->_grey][$poId][$bodypartId][$libYarnCountDeterId][$diaWidth][$uom]+=$reqqnty_grey;
					}
					else{
						$Qty[$this->_woven][$this->_grey][$poId][$bodypartId][$libYarnCountDeterId][$diaWidth][$uom]=$reqqnty_grey;
					}
				}
				elseif($fabNatureId==100){
					if(isset($Qty[$this->_sweater][$this->_finish][$poId][$bodypartId][$libYarnCountDeterId][$diaWidth][$uom])){
						$Qty[$this->_sweater][$this->_finish][$poId][$bodypartId][$libYarnCountDeterId][$diaWidth][$uom]+=$reqqnty_finish;
					}
					else{
						$Qty[$this->_sweater][$this->_finish][$poId][$bodypartId][$libYarnCountDeterId][$diaWidth][$uom]=$reqqnty_finish;
					}
					if(isset($Qty[$this->_sweater][$this->_grey][$poId][$bodypartId][$libYarnCountDeterId][$diaWidth][$uom]))
					{
						$Qty[$this->_sweater][$this->_grey][$poId][$bodypartId][$libYarnCountDeterId][$diaWidth][$uom]+=$reqqnty_grey;
					}
					else{
						$Qty[$this->_sweater][$this->_grey][$poId][$bodypartId][$libYarnCountDeterId][$diaWidth][$uom]=$reqqnty_grey;
					}
				}
			}
			elseif($level==$this->_By_OrderItemidCountrydateBodypartDeterminIdDiatypeDiaAndGsm){//By_OrderItemidCountrydateBodypartDeterminIdDiatypeDiaAndGsm
				if($fabNatureId==2){
					if(isset($Qty[$this->_knit][$this->_finish][$poId][$itemNumberId][$countryShipDate][$bodypartId][$libYarnCountDeterId][$widthDiaType][$diaWidth][$gsmweight][$uom])){
						$Qty[$this->_knit][$this->_finish][$poId][$itemNumberId][$countryShipDate][$bodypartId][$libYarnCountDeterId][$widthDiaType][$diaWidth][$gsmweight][$uom]+=$reqqnty_finish;
					}
					else{
						$Qty[$this->_knit][$this->_finish][$poId][$itemNumberId][$countryShipDate][$bodypartId][$libYarnCountDeterId][$widthDiaType][$diaWidth][$gsmweight][$uom]=$reqqnty_finish;
					}
					if(isset($Qty[$this->_knit][$this->_grey][$poId][$itemNumberId][$countryShipDate][$bodypartId][$libYarnCountDeterId][$widthDiaType][$diaWidth][$gsmweight][$uom])){
						$Qty[$this->_knit][$this->_grey][$poId][$itemNumberId][$countryShipDate][$bodypartId][$libYarnCountDeterId][$widthDiaType][$diaWidth][$gsmweight][$uom]+=$reqqnty_grey;
					}
					else{
						$Qty[$this->_knit][$this->_grey][$poId][$itemNumberId][$countryShipDate][$bodypartId][$libYarnCountDeterId][$widthDiaType][$diaWidth][$gsmweight][$uom]=$reqqnty_grey;
					}
				}
				elseif($fabNatureId==3){
					if(isset($Qty[$this->_woven][$this->_finish][$poId][$itemNumberId][$countryShipDate][$bodypartId][$libYarnCountDeterId][$widthDiaType][$diaWidth][$gsmweight][$uom])){
						$Qty[$this->_woven][$this->_finish][$poId][$itemNumberId][$countryShipDate][$bodypartId][$libYarnCountDeterId][$widthDiaType][$diaWidth][$gsmweight][$uom]+=$reqqnty_finish;
					}
					else{
						$Qty[$this->_woven][$this->_finish][$poId][$itemNumberId][$countryShipDate][$bodypartId][$libYarnCountDeterId][$widthDiaType][$diaWidth][$gsmweight][$uom]=$reqqnty_finish;
					}
					if(isset($Qty[$this->_woven][$this->_grey][$poId][$itemNumberId][$countryShipDate][$bodypartId][$libYarnCountDeterId][$widthDiaType][$diaWidth][$gsmweight][$uom]))
					{
						$Qty[$this->_woven][$this->_grey][$poId][$itemNumberId][$countryShipDate][$bodypartId][$libYarnCountDeterId][$widthDiaType][$diaWidth][$gsmweight][$uom]+=$reqqnty_grey;
					}
					else{
						$Qty[$this->_woven][$this->_grey][$poId][$itemNumberId][$countryShipDate][$bodypartId][$libYarnCountDeterId][$widthDiaType][$diaWidth][$gsmweight][$uom]=$reqqnty_grey;
					}
				}
				elseif($fabNatureId==100){
					if(isset($Qty[$this->_sweater][$this->_finish][$poId][$itemNumberId][$countryShipDate][$bodypartId][$libYarnCountDeterId][$widthDiaType][$diaWidth][$gsmweight][$uom])){
						$Qty[$this->_sweater][$this->_finish][$poId][$itemNumberId][$countryShipDate][$bodypartId][$libYarnCountDeterId][$widthDiaType][$diaWidth][$gsmweight][$uom]+=$reqqnty_finish;
					}
					else{
						$Qty[$this->_sweater][$this->_finish][$poId][$itemNumberId][$countryShipDate][$bodypartId][$libYarnCountDeterId][$widthDiaType][$diaWidth][$gsmweight][$uom]=$reqqnty_finish;
					}
					if(isset($Qty[$this->_sweater][$this->_grey][$poId][$itemNumberId][$countryShipDate][$bodypartId][$libYarnCountDeterId][$widthDiaType][$diaWidth][$gsmweight][$uom]))
					{
						$Qty[$this->_sweater][$this->_grey][$poId][$itemNumberId][$countryShipDate][$bodypartId][$libYarnCountDeterId][$widthDiaType][$diaWidth][$gsmweight][$uom]+=$reqqnty_grey;
					}
					else{
						$Qty[$this->_sweater][$this->_grey][$poId][$itemNumberId][$countryShipDate][$bodypartId][$libYarnCountDeterId][$widthDiaType][$diaWidth][$gsmweight][$uom]=$reqqnty_grey;
					}
				}
			}
			
			elseif($level==$this->_By_JobColorBodyTypeDeterminIdGsmAndDiaWidth){
				if($fabNatureId==2){
					if(isset($Qty[$this->_knit][$this->_finish][$jobNo][$colorId][$bodyparttypeId][$libYarnCountDeterId][$gsmweight][$diaWidth][$fabricSourceId][$uom])){
						$Qty[$this->_knit][$this->_finish][$jobNo][$colorId][$bodyparttypeId][$libYarnCountDeterId][$gsmweight][$diaWidth][$fabricSourceId][$uom]+=$reqqnty_finish;
					}
					else{
						$Qty[$this->_knit][$this->_finish][$jobNo][$colorId][$bodyparttypeId][$libYarnCountDeterId][$gsmweight][$diaWidth][$fabricSourceId][$uom]=$reqqnty_finish;
					}
					if(isset($Qty[$this->_knit][$this->_grey][$jobNo][$colorId][$bodyparttypeId][$libYarnCountDeterId][$gsmweight][$diaWidth][$fabricSourceId][$uom])){
						$Qty[$this->_knit][$this->_grey][$jobNo][$colorId][$bodyparttypeId][$libYarnCountDeterId][$gsmweight][$diaWidth][$fabricSourceId][$uom]+=$reqqnty_grey;
					}
					else{
						$Qty[$this->_knit][$this->_grey][$jobNo][$colorId][$bodyparttypeId][$libYarnCountDeterId][$gsmweight][$diaWidth][$fabricSourceId][$uom]=$reqqnty_grey;
					}
				}
				elseif($fabNatureId==3){
					if(isset($Qty[$this->_woven][$this->_finish][$jobNo][$colorId][$bodyparttypeId][$libYarnCountDeterId][$gsmweight][$diaWidth][$fabricSourceId][$uom])){
						$Qty[$this->_woven][$this->_finish][$jobNo][$colorId][$bodyparttypeId][$libYarnCountDeterId][$gsmweight][$diaWidth][$fabricSourceId][$uom]+=$reqqnty_finish;
					}
					else{
						$Qty[$this->_woven][$this->_finish][$jobNo][$colorId][$bodyparttypeId][$libYarnCountDeterId][$gsmweight][$diaWidth][$fabricSourceId][$uom]=$reqqnty_finish;
					}
					if(isset($Qty[$this->_woven][$this->_grey][$jobNo][$colorId][$bodyparttypeId][$libYarnCountDeterId][$gsmweight][$diaWidth][$fabricSourceId][$uom]))
					{
						$Qty[$this->_woven][$this->_grey][$jobNo][$colorId][$bodyparttypeId][$libYarnCountDeterId][$gsmweight][$diaWidth][$fabricSourceId][$uom]+=$reqqnty_grey;
					}
					else{
						$Qty[$this->_woven][$this->_grey][$jobNo][$colorId][$bodyparttypeId][$libYarnCountDeterId][$gsmweight][$diaWidth][$fabricSourceId][$uom]=$reqqnty_grey;
					}
				}
				elseif($fabNatureId==100){
					if(isset($Qty[$this->_sweater][$this->_finish][$jobNo][$colorId][$bodyparttypeId][$libYarnCountDeterId][$gsmweight][$diaWidth][$fabricSourceId][$uom])){
						$Qty[$this->_sweater][$this->_finish][$jobNo][$colorId][$bodyparttypeId][$libYarnCountDeterId][$gsmweight][$diaWidth][$fabricSourceId][$uom]+=$reqqnty_finish;
					}
					else{
						$Qty[$this->_sweater][$this->_finish][$jobNo][$colorId][$bodyparttypeId][$libYarnCountDeterId][$gsmweight][$diaWidth][$fabricSourceId][$uom]=$reqqnty_finish;
					}
					if(isset($Qty[$this->_sweater][$this->_grey][$jobNo][$colorId][$bodyparttypeId][$libYarnCountDeterId][$gsmweight][$diaWidth][$fabricSourceId][$uom]))
					{
						$Qty[$this->_sweater][$this->_grey][$jobNo][$colorId][$bodyparttypeId][$libYarnCountDeterId][$gsmweight][$diaWidth][$fabricSourceId][$uom]+=$reqqnty_grey;
					}
					else{
						$Qty[$this->_sweater][$this->_grey][$jobNo][$colorId][$bodyparttypeId][$libYarnCountDeterId][$gsmweight][$diaWidth][$fabricSourceId][$uom]=$reqqnty_grey;
					}
				}
			}
			elseif($level==$this->_By_JobColorFabricColorBodyTypeDeterminIdGsmAndDiaWidth){
				if($fabNatureId==2){
					if(isset($Qty[$this->_knit][$this->_finish][$jobNo][$colorId][$fabricColorId][$bodyparttypeId][$libYarnCountDeterId][$gsmweight][$diaWidth][$fabricSourceId][$uom])){
						$Qty[$this->_knit][$this->_finish][$jobNo][$colorId][$fabricColorId][$bodyparttypeId][$libYarnCountDeterId][$gsmweight][$diaWidth][$fabricSourceId][$uom]+=$reqqnty_finish;
					}
					else{
						$Qty[$this->_knit][$this->_finish][$jobNo][$colorId][$fabricColorId][$bodyparttypeId][$libYarnCountDeterId][$gsmweight][$diaWidth][$fabricSourceId][$uom]=$reqqnty_finish;
					}
					if(isset($Qty[$this->_knit][$this->_grey][$jobNo][$colorId][$fabricColorId][$bodyparttypeId][$libYarnCountDeterId][$gsmweight][$diaWidth][$fabricSourceId][$uom])){
						$Qty[$this->_knit][$this->_grey][$jobNo][$colorId][$fabricColorId][$bodyparttypeId][$libYarnCountDeterId][$gsmweight][$diaWidth][$fabricSourceId][$uom]+=$reqqnty_grey;
					}
					else{
						$Qty[$this->_knit][$this->_grey][$jobNo][$colorId][$fabricColorId][$bodyparttypeId][$libYarnCountDeterId][$gsmweight][$diaWidth][$fabricSourceId][$uom]=$reqqnty_grey;
					}
				}
				elseif($fabNatureId==3){
					if(isset($Qty[$this->_woven][$this->_finish][$jobNo][$colorId][$fabricColorId][$bodyparttypeId][$libYarnCountDeterId][$gsmweight][$diaWidth][$fabricSourceId][$uom])){
						$Qty[$this->_woven][$this->_finish][$jobNo][$colorId][$fabricColorId][$bodyparttypeId][$libYarnCountDeterId][$gsmweight][$diaWidth][$fabricSourceId][$uom]+=$reqqnty_finish;
					}
					else{
						$Qty[$this->_woven][$this->_finish][$jobNo][$colorId][$fabricColorId][$bodyparttypeId][$libYarnCountDeterId][$gsmweight][$diaWidth][$fabricSourceId][$uom]=$reqqnty_finish;
					}
					if(isset($Qty[$this->_woven][$this->_grey][$jobNo][$colorId][$fabricColorId][$bodyparttypeId][$libYarnCountDeterId][$gsmweight][$diaWidth][$fabricSourceId][$uom]))
					{
						$Qty[$this->_woven][$this->_grey][$jobNo][$colorId][$fabricColorId][$bodyparttypeId][$libYarnCountDeterId][$gsmweight][$diaWidth][$fabricSourceId][$uom]+=$reqqnty_grey;
					}
					else{
						$Qty[$this->_woven][$this->_grey][$jobNo][$colorId][$fabricColorId][$bodyparttypeId][$libYarnCountDeterId][$gsmweight][$diaWidth][$fabricSourceId][$uom]=$reqqnty_grey;
					}
				}
				elseif($fabNatureId==100){
					if(isset($Qty[$this->_sweater][$this->_finish][$jobNo][$colorId][$fabricColorId][$bodyparttypeId][$libYarnCountDeterId][$gsmweight][$diaWidth][$fabricSourceId][$uom])){
						$Qty[$this->_sweater][$this->_finish][$jobNo][$colorId][$fabricColorId][$bodyparttypeId][$libYarnCountDeterId][$gsmweight][$diaWidth][$fabricSourceId][$uom]+=$reqqnty_finish;
					}
					else{
						$Qty[$this->_sweater][$this->_finish][$jobNo][$colorId][$fabricColorId][$bodyparttypeId][$libYarnCountDeterId][$gsmweight][$diaWidth][$fabricSourceId][$uom]=$reqqnty_finish;
					}
					if(isset($Qty[$this->_sweater][$this->_grey][$jobNo][$colorId][$fabricColorId][$bodyparttypeId][$libYarnCountDeterId][$gsmweight][$diaWidth][$fabricSourceId][$uom]))
					{
						$Qty[$this->_sweater][$this->_grey][$jobNo][$colorId][$fabricColorId][$bodyparttypeId][$libYarnCountDeterId][$gsmweight][$diaWidth][$fabricSourceId][$uom]+=$reqqnty_grey;
					}
					else{
						$Qty[$this->_sweater][$this->_grey][$jobNo][$colorId][$fabricColorId][$bodyparttypeId][$libYarnCountDeterId][$gsmweight][$diaWidth][$fabricSourceId][$uom]=$reqqnty_grey;
					}
				}
			}
			elseif($level==$this->_By_JobColorFabricColorGmtsSizeBodyTypeDeterminIdGsmAndDiaWidth){
				if($fabNatureId==2){
					if(isset($Qty[$this->_knit][$this->_finish][$jobNo][$colorId][$fabricColorId][$sizeId][$bodyparttypeId][$libYarnCountDeterId][$gsmweight][$diaWidth][$fabricSourceId][$uom])){
						$Qty[$this->_knit][$this->_finish][$jobNo][$colorId][$fabricColorId[$sizeId]][$bodyparttypeId][$libYarnCountDeterId][$gsmweight][$diaWidth][$fabricSourceId][$uom]+=$reqqnty_finish;
					}
					else{
						$Qty[$this->_knit][$this->_finish][$jobNo][$colorId][$fabricColorId][$sizeId][$bodyparttypeId][$libYarnCountDeterId][$gsmweight][$diaWidth][$fabricSourceId][$uom]=$reqqnty_finish;
					}
					if(isset($Qty[$this->_knit][$this->_grey][$jobNo][$colorId][$fabricColorId][$sizeId][$bodyparttypeId][$libYarnCountDeterId][$gsmweight][$diaWidth][$fabricSourceId][$uom])){
						$Qty[$this->_knit][$this->_grey][$jobNo][$colorId][$fabricColorId][$sizeId][$bodyparttypeId][$libYarnCountDeterId][$gsmweight][$diaWidth][$fabricSourceId][$uom]+=$reqqnty_grey;
					}
					else{
						$Qty[$this->_knit][$this->_grey][$jobNo][$colorId][$fabricColorId][$sizeId][$bodyparttypeId][$libYarnCountDeterId][$gsmweight][$diaWidth][$fabricSourceId][$uom]=$reqqnty_grey;
					}
				}
				elseif($fabNatureId==3){
					if(isset($Qty[$this->_woven][$this->_finish][$jobNo][$colorId][$fabricColorId][$sizeId][$bodyparttypeId][$libYarnCountDeterId][$gsmweight][$diaWidth][$fabricSourceId][$uom])){
						$Qty[$this->_woven][$this->_finish][$jobNo][$colorId][$fabricColorId][$sizeId][$bodyparttypeId][$libYarnCountDeterId][$gsmweight][$diaWidth][$fabricSourceId][$uom]+=$reqqnty_finish;
					}
					else{
						$Qty[$this->_woven][$this->_finish][$jobNo][$colorId][$fabricColorId][$sizeId][$bodyparttypeId][$libYarnCountDeterId][$gsmweight][$diaWidth][$fabricSourceId][$uom]=$reqqnty_finish;
					}
					if(isset($Qty[$this->_woven][$this->_grey][$jobNo][$colorId][$fabricColorId][$sizeId][$bodyparttypeId][$libYarnCountDeterId][$gsmweight][$diaWidth][$fabricSourceId][$uom]))
					{
						$Qty[$this->_woven][$this->_grey][$jobNo][$colorId][$fabricColorId][$sizeId][$bodyparttypeId][$libYarnCountDeterId][$gsmweight][$diaWidth][$fabricSourceId][$uom]+=$reqqnty_grey;
					}
					else{
						$Qty[$this->_woven][$this->_grey][$jobNo][$colorId][$fabricColorId][$sizeId][$bodyparttypeId][$libYarnCountDeterId][$gsmweight][$diaWidth][$fabricSourceId][$uom]=$reqqnty_grey;
					}
				}
				elseif($fabNatureId==100){
					if(isset($Qty[$this->_sweater][$this->_finish][$jobNo][$colorId][$fabricColorId][$sizeId][$bodyparttypeId][$libYarnCountDeterId][$gsmweight][$diaWidth][$fabricSourceId][$uom])){
						$Qty[$this->_sweater][$this->_finish][$jobNo][$colorId][$fabricColorId][$sizeId][$bodyparttypeId][$libYarnCountDeterId][$gsmweight][$diaWidth][$fabricSourceId][$uom]+=$reqqnty_finish;
					}
					else{
						$Qty[$this->_sweater][$this->_finish][$jobNo][$colorId][$fabricColorId][$sizeId][$bodyparttypeId][$libYarnCountDeterId][$gsmweight][$diaWidth][$fabricSourceId][$uom]=$reqqnty_finish;
					}
					if(isset($Qty[$this->_sweater][$this->_grey][$jobNo][$colorId][$fabricColorId][$sizeId][$bodyparttypeId][$libYarnCountDeterId][$gsmweight][$diaWidth][$fabricSourceId][$uom]))
					{
						$Qty[$this->_sweater][$this->_grey][$jobNo][$colorId][$fabricColorId][$sizeId][$bodyparttypeId][$libYarnCountDeterId][$gsmweight][$diaWidth][$fabricSourceId][$uom]+=$reqqnty_grey;
					}
					else{
						$Qty[$this->_sweater][$this->_grey][$jobNo][$colorId][$fabricColorId][$sizeId][$bodyparttypeId][$libYarnCountDeterId][$gsmweight][$diaWidth][$fabricSourceId][$uom]=$reqqnty_grey;
					}
				}
			}
			elseif($level==$this->_By_JobColorFabricColorBodyPartIdDeterminIdGsmAndDiaWidth){
				if($fabNatureId==2){
					if(isset($Qty[$this->_knit][$this->_finish][$jobNo][$colorId][$fabricColorId][$bodypartId][$libYarnCountDeterId][$gsmweight][$diaWidth][$fabricSourceId][$uom])){
						$Qty[$this->_knit][$this->_finish][$jobNo][$colorId][$fabricColorId][$bodypartId][$libYarnCountDeterId][$gsmweight][$diaWidth][$fabricSourceId][$uom]+=$reqqnty_finish;
					}
					else{
						$Qty[$this->_knit][$this->_finish][$jobNo][$colorId][$fabricColorId][$bodypartId][$libYarnCountDeterId][$gsmweight][$diaWidth][$fabricSourceId][$uom]=$reqqnty_finish;
					}
					if(isset($Qty[$this->_knit][$this->_grey][$jobNo][$colorId][$fabricColorId][$bodypartId][$libYarnCountDeterId][$gsmweight][$diaWidth][$fabricSourceId][$uom])){
						$Qty[$this->_knit][$this->_grey][$jobNo][$colorId][$fabricColorId][$bodypartId][$libYarnCountDeterId][$gsmweight][$diaWidth][$fabricSourceId][$uom]+=$reqqnty_grey;
					}
					else{
						$Qty[$this->_knit][$this->_grey][$jobNo][$colorId][$fabricColorId][$bodypartId][$libYarnCountDeterId][$gsmweight][$diaWidth][$fabricSourceId][$uom]=$reqqnty_grey;
					}
				}
				elseif($fabNatureId==3){
					if(isset($Qty[$this->_woven][$this->_finish][$jobNo][$colorId][$fabricColorId][$bodypartId][$libYarnCountDeterId][$gsmweight][$diaWidth][$fabricSourceId][$uom])){
						$Qty[$this->_woven][$this->_finish][$jobNo][$colorId][$fabricColorId][$bodypartId][$libYarnCountDeterId][$gsmweight][$diaWidth][$fabricSourceId][$uom]+=$reqqnty_finish;
					}
					else{
						$Qty[$this->_woven][$this->_finish][$jobNo][$colorId][$fabricColorId][$bodypartId][$libYarnCountDeterId][$gsmweight][$diaWidth][$fabricSourceId][$uom]=$reqqnty_finish;
					}
					if(isset($Qty[$this->_woven][$this->_grey][$jobNo][$colorId][$fabricColorId][$bodypartId][$libYarnCountDeterId][$gsmweight][$diaWidth][$fabricSourceId][$uom]))
					{
						$Qty[$this->_woven][$this->_grey][$jobNo][$colorId][$fabricColorId][$bodypartId][$libYarnCountDeterId][$gsmweight][$diaWidth][$fabricSourceId][$uom]+=$reqqnty_grey;
					}
					else{
						$Qty[$this->_woven][$this->_grey][$jobNo][$colorId][$fabricColorId][$bodypartId][$libYarnCountDeterId][$gsmweight][$diaWidth][$fabricSourceId][$uom]=$reqqnty_grey;
					}
				}
				elseif($fabNatureId==100){
					if(isset($Qty[$this->_sweater][$this->_finish][$jobNo][$colorId][$fabricColorId][$bodypartId][$libYarnCountDeterId][$gsmweight][$diaWidth][$fabricSourceId][$uom])){
						$Qty[$this->_sweater][$this->_finish][$jobNo][$colorId][$fabricColorId][$bodypartId][$libYarnCountDeterId][$gsmweight][$diaWidth][$fabricSourceId][$uom]+=$reqqnty_finish;
					}
					else{
						$Qty[$this->_sweater][$this->_finish][$jobNo][$colorId][$fabricColorId][$bodypartId][$libYarnCountDeterId][$gsmweight][$diaWidth][$fabricSourceId][$uom]=$reqqnty_finish;
					}
					if(isset($Qty[$this->_sweater][$this->_grey][$jobNo][$colorId][$fabricColorId][$bodypartId][$libYarnCountDeterId][$gsmweight][$diaWidth][$fabricSourceId][$uom]))
					{
						$Qty[$this->_sweater][$this->_grey][$jobNo][$colorId][$fabricColorId][$bodypartId][$libYarnCountDeterId][$gsmweight][$diaWidth][$fabricSourceId][$uom]+=$reqqnty_grey;
					}
					else{
						$Qty[$this->_sweater][$this->_grey][$jobNo][$colorId][$fabricColorId][$bodypartId][$libYarnCountDeterId][$gsmweight][$diaWidth][$fabricSourceId][$uom]=$reqqnty_grey;
					}
				}
			}
			elseif($level==$this->_By_JobColorBodyTypeDeterminIdAndDiaWidth){
				if($fabNatureId==2){
					if(isset($Qty[$this->_knit][$this->_finish][$jobNo][$colorId][$bodyparttypeId][$libYarnCountDeterId][$diaWidth][$uom])){
						$Qty[$this->_knit][$this->_finish][$jobNo][$colorId][$bodyparttypeId][$libYarnCountDeterId][$diaWidth][$uom]+=$reqqnty_finish;
					}
					else{
						$Qty[$this->_knit][$this->_finish][$jobNo][$colorId][$bodyparttypeId][$libYarnCountDeterId][$diaWidth][$uom]=$reqqnty_finish;
					}
					if(isset($Qty[$this->_knit][$this->_grey][$jobNo][$colorId][$bodyparttypeId][$libYarnCountDeterId][$diaWidth][$uom])){
						$Qty[$this->_knit][$this->_grey][$jobNo][$colorId][$bodyparttypeId][$libYarnCountDeterId][$diaWidth][$uom]+=$reqqnty_grey;
					}
					else{
						$Qty[$this->_knit][$this->_grey][$jobNo][$colorId][$bodyparttypeId][$libYarnCountDeterId][$diaWidth][$uom]=$reqqnty_grey;
					}
				}
				elseif($fabNatureId==3){
					if(isset($Qty[$this->_woven][$this->_finish][$jobNo][$colorId][$bodyparttypeId][$libYarnCountDeterId][$diaWidth][$uom])){
						$Qty[$this->_woven][$this->_finish][$jobNo][$colorId][$bodyparttypeId][$libYarnCountDeterId][$diaWidth][$uom]+=$reqqnty_finish;
					}
					else{
						$Qty[$this->_woven][$this->_finish][$jobNo][$colorId][$bodyparttypeId][$libYarnCountDeterId][$diaWidth][$uom]=$reqqnty_finish;
					}
					if(isset($Qty[$this->_woven][$this->_grey][$jobNo][$colorId][$bodyparttypeId][$libYarnCountDeterId][$diaWidth][$uom]))
					{
						$Qty[$this->_woven][$this->_grey][$jobNo][$colorId][$bodyparttypeId][$libYarnCountDeterId][$diaWidth][$uom]+=$reqqnty_grey;
					}
					else{
						$Qty[$this->_woven][$this->_grey][$jobNo][$colorId][$bodyparttypeId][$libYarnCountDeterId][$diaWidth][$uom]=$reqqnty_grey;
					}
				}
				elseif($fabNatureId==100){
					if(isset($Qty[$this->_sweater][$this->_finish][$jobNo][$colorId][$bodyparttypeId][$libYarnCountDeterId][$diaWidth][$uom])){
						$Qty[$this->_sweater][$this->_finish][$jobNo][$colorId][$bodyparttypeId][$libYarnCountDeterId][$diaWidth][$uom]+=$reqqnty_finish;
					}
					else{
						$Qty[$this->_sweater][$this->_finish][$jobNo][$colorId][$bodyparttypeId][$libYarnCountDeterId][$diaWidth][$uom]=$reqqnty_finish;
					}
					if(isset($Qty[$this->_sweater][$this->_grey][$jobNo][$colorId][$bodyparttypeId][$libYarnCountDeterId][$diaWidth][$uom]))
					{
						$Qty[$this->_sweater][$this->_grey][$jobNo][$colorId][$bodyparttypeId][$libYarnCountDeterId][$diaWidth][$uom]+=$reqqnty_grey;
					}
					else{
						$Qty[$this->_sweater][$this->_grey][$jobNo][$colorId][$bodyparttypeId][$libYarnCountDeterId][$diaWidth][$uom]=$reqqnty_grey;
					}
				}
			}
			elseif($level==$this->_By_JobColorFabColorBodyTypeDeterminIdAndDiaWidth){  
				if($fabNatureId==2){
					if(isset($Qty[$this->_knit][$this->_finish][$jobNo][$colorId][$FabricColorId][$bodyparttypeId][$libYarnCountDeterId][$diaWidth][$uom])){
						$Qty[$this->_knit][$this->_finish][$jobNo][$colorId][$FabricColorId][$bodyparttypeId][$libYarnCountDeterId][$diaWidth][$uom]+=$reqqnty_finish;
					}
					else{
						$Qty[$this->_knit][$this->_finish][$jobNo][$colorId][$FabricColorId][$bodyparttypeId][$libYarnCountDeterId][$diaWidth][$uom]=$reqqnty_finish;
					}
					if(isset($Qty[$this->_knit][$this->_grey][$jobNo][$colorId][$FabricColorId][$bodyparttypeId][$libYarnCountDeterId][$diaWidth][$uom])){
						$Qty[$this->_knit][$this->_grey][$jobNo][$colorId][$FabricColorId][$bodyparttypeId][$libYarnCountDeterId][$diaWidth][$uom]+=$reqqnty_grey;
					}
					else{
						$Qty[$this->_knit][$this->_grey][$jobNo][$colorId][$FabricColorId][$bodyparttypeId][$libYarnCountDeterId][$diaWidth][$uom]=$reqqnty_grey;
					}
				}
				elseif($fabNatureId==3){
					if(isset($Qty[$this->_woven][$this->_finish][$jobNo][$colorId][$FabricColorId][$bodyparttypeId][$libYarnCountDeterId][$diaWidth][$uom])){
						$Qty[$this->_woven][$this->_finish][$jobNo][$colorId][$FabricColorId][$bodyparttypeId][$libYarnCountDeterId][$diaWidth][$uom]+=$reqqnty_finish;
					}
					else{
						$Qty[$this->_woven][$this->_finish][$jobNo][$colorId][$FabricColorId][$bodyparttypeId][$libYarnCountDeterId][$diaWidth][$uom]=$reqqnty_finish;
					}
					if(isset($Qty[$this->_woven][$this->_grey][$jobNo][$colorId][$FabricColorId][$bodyparttypeId][$libYarnCountDeterId][$diaWidth][$uom]))
					{
						$Qty[$this->_woven][$this->_grey][$jobNo][$colorId][$FabricColorId][$bodyparttypeId][$libYarnCountDeterId][$diaWidth][$uom]+=$reqqnty_grey;
					}
					else{
						$Qty[$this->_woven][$this->_grey][$jobNo][$colorId][$FabricColorId][$bodyparttypeId][$libYarnCountDeterId][$diaWidth][$uom]=$reqqnty_grey;
					}
				}
				elseif($fabNatureId==100){
					if(isset($Qty[$this->_sweater][$this->_finish][$jobNo][$colorId][$FabricColorId][$bodyparttypeId][$libYarnCountDeterId][$diaWidth][$uom])){
						$Qty[$this->_sweater][$this->_finish][$jobNo][$colorId][$FabricColorId][$bodyparttypeId][$libYarnCountDeterId][$diaWidth][$uom]+=$reqqnty_finish;
					}
					else{
						$Qty[$this->_sweater][$this->_finish][$jobNo][$colorId][$FabricColorId][$bodyparttypeId][$libYarnCountDeterId][$diaWidth][$uom]=$reqqnty_finish;
					}
					if(isset($Qty[$this->_sweater][$this->_grey][$jobNo][$colorId][$FabricColorId][$bodyparttypeId][$libYarnCountDeterId][$diaWidth][$uom]))
					{
						$Qty[$this->_sweater][$this->_grey][$jobNo][$colorId][$FabricColorId][$bodyparttypeId][$libYarnCountDeterId][$diaWidth][$uom]+=$reqqnty_grey;
					}
					else{
						$Qty[$this->_sweater][$this->_grey][$jobNo][$colorId][$FabricColorId][$bodyparttypeId][$libYarnCountDeterId][$diaWidth][$uom]=$reqqnty_grey;
					}
				}
			}
			elseif($level==$this->_By_JobFabSourceColorFabColorBodyTypeDeterminIdAndDiaWidth){  
				if($fabNatureId==2){
					if(isset($Qty[$this->_knit][$this->_finish][$jobNo][$fabricSourceId][$colorId][$FabricColorId][$bodyparttypeId][$libYarnCountDeterId][$diaWidth][$uom])){
						$Qty[$this->_knit][$this->_finish][$jobNo][$fabricSourceId][$colorId][$FabricColorId][$bodyparttypeId][$libYarnCountDeterId][$diaWidth][$uom]+=$reqqnty_finish;
					}
					else{
						$Qty[$this->_knit][$this->_finish][$jobNo][$fabricSourceId][$colorId][$FabricColorId][$bodyparttypeId][$libYarnCountDeterId][$diaWidth][$uom]=$reqqnty_finish;
					}
					if(isset($Qty[$this->_knit][$this->_grey][$jobNo][$fabricSourceId][$colorId][$FabricColorId][$bodyparttypeId][$libYarnCountDeterId][$diaWidth][$uom])){
						$Qty[$this->_knit][$this->_grey][$jobNo][$fabricSourceId][$colorId][$FabricColorId][$bodyparttypeId][$libYarnCountDeterId][$diaWidth][$uom]+=$reqqnty_grey;
					}
					else{
						$Qty[$this->_knit][$this->_grey][$jobNo][$fabricSourceId][$colorId][$FabricColorId][$bodyparttypeId][$libYarnCountDeterId][$diaWidth][$uom]=$reqqnty_grey;
					}
				}
				elseif($fabNatureId==3){
					if(isset($Qty[$this->_woven][$this->_finish][$jobNo][$fabricSourceId][$colorId][$FabricColorId][$bodyparttypeId][$libYarnCountDeterId][$diaWidth][$uom])){
						$Qty[$this->_woven][$this->_finish][$jobNo][$fabricSourceId][$colorId][$FabricColorId][$bodyparttypeId][$libYarnCountDeterId][$diaWidth][$uom]+=$reqqnty_finish;
					}
					else{
						$Qty[$this->_woven][$this->_finish][$jobNo][$fabricSourceId][$colorId][$FabricColorId][$bodyparttypeId][$libYarnCountDeterId][$diaWidth][$uom]=$reqqnty_finish;
					}
					if(isset($Qty[$this->_woven][$this->_grey][$jobNo][$fabricSourceId][$colorId][$FabricColorId][$bodyparttypeId][$libYarnCountDeterId][$diaWidth][$uom]))
					{
						$Qty[$this->_woven][$this->_grey][$jobNo][$fabricSourceId][$colorId][$FabricColorId][$bodyparttypeId][$libYarnCountDeterId][$diaWidth][$uom]+=$reqqnty_grey;
					}
					else{
						$Qty[$this->_woven][$this->_grey][$jobNo][$fabricSourceId][$colorId][$FabricColorId][$bodyparttypeId][$libYarnCountDeterId][$diaWidth][$uom]=$reqqnty_grey;
					}
				}
				elseif($fabNatureId==100){
					if(isset($Qty[$this->_sweater][$this->_finish][$jobNo][$fabricSourceId][$colorId][$FabricColorId][$bodyparttypeId][$libYarnCountDeterId][$diaWidth][$uom])){
						$Qty[$this->_sweater][$this->_finish][$jobNo][$fabricSourceId][$colorId][$FabricColorId][$bodyparttypeId][$libYarnCountDeterId][$diaWidth][$uom]+=$reqqnty_finish;
					}
					else{
						$Qty[$this->_sweater][$this->_finish][$jobNo][$fabricSourceId][$colorId][$FabricColorId][$bodyparttypeId][$libYarnCountDeterId][$diaWidth][$uom]=$reqqnty_finish;
					}
					if(isset($Qty[$this->_sweater][$this->_grey][$jobNo][$fabricSourceId][$colorId][$FabricColorId][$bodyparttypeId][$libYarnCountDeterId][$diaWidth][$uom]))
					{
						$Qty[$this->_sweater][$this->_grey][$jobNo][$fabricSourceId][$colorId][$FabricColorId][$bodyparttypeId][$libYarnCountDeterId][$diaWidth][$uom]+=$reqqnty_grey;
					}
					else{
						$Qty[$this->_sweater][$this->_grey][$jobNo][$fabricSourceId][$colorId][$FabricColorId][$bodyparttypeId][$libYarnCountDeterId][$diaWidth][$uom]=$reqqnty_grey;
					}
				}
			}
			elseif($level==$this->_By_OrderFabColorGsmDeterminIdAndDiaWidth){
				if($fabNatureId==2){
					if(isset($Qty[$this->_knit][$this->_finish][$poId][$FabGmtColorId][$gsmweight][$libYarnCountDeterId][$diaWidth][$uom])){
						$Qty[$this->_knit][$this->_finish][$poId][$FabGmtColorId][$gsmweight][$libYarnCountDeterId][$diaWidth][$uom]+=$reqqnty_finish;
					}
					else{
						$Qty[$this->_knit][$this->_finish][$poId][$FabGmtColorId][$gsmweight][$libYarnCountDeterId][$diaWidth][$uom]=$reqqnty_finish;
					}
					if(isset($Qty[$this->_knit][$this->_grey][$poId][$FabGmtColorId][$gsmweight][$libYarnCountDeterId][$diaWidth][$uom])){
						$Qty[$this->_knit][$this->_grey][$poId][$FabGmtColorId][$gsmweight][$libYarnCountDeterId][$diaWidth][$uom]+=$reqqnty_grey;
					}
					else{
						$Qty[$this->_knit][$this->_grey][$poId][$FabGmtColorId][$gsmweight][$libYarnCountDeterId][$diaWidth][$uom]=$reqqnty_grey;
					}
				}
				elseif($fabNatureId==3){
					if(isset($Qty[$this->_woven][$this->_finish][$poId][$FabGmtColorId][$gsmweight][$libYarnCountDeterId][$diaWidth][$uom])){
						$Qty[$this->_woven][$this->_finish][$poId][$FabGmtColorId][$gsmweight][$libYarnCountDeterId][$diaWidth][$uom]+=$reqqnty_finish;
					}
					else{
						$Qty[$this->_woven][$this->_finish][$poId][$FabGmtColorId][$gsmweight][$libYarnCountDeterId][$diaWidth][$uom]=$reqqnty_finish;
					}
					if(isset($Qty[$this->_woven][$this->_grey][$poId][$FabGmtColorId][$gsmweight][$libYarnCountDeterId][$diaWidth][$uom]))
					{
						$Qty[$this->_woven][$this->_grey][$poId][$FabGmtColorId][$gsmweight][$libYarnCountDeterId][$diaWidth][$uom]+=$reqqnty_grey;
					}
					else{
						$Qty[$this->_woven][$this->_grey][$poId][$FabGmtColorId][$gsmweight][$libYarnCountDeterId][$diaWidth][$uom]=$reqqnty_grey;
					}
				}
				elseif($fabNatureId==100){
					if(isset($Qty[$this->_sweater][$this->_finish][$poId][$FabGmtColorId][$gsmweight][$libYarnCountDeterId][$diaWidth][$uom])){
						$Qty[$this->_sweater][$this->_finish][$poId][$FabGmtColorId][$gsmweight][$libYarnCountDeterId][$diaWidth][$uom]+=$reqqnty_finish;
					}
					else{
						$Qty[$this->_sweater][$this->_finish][$poId][$FabGmtColorId][$gsmweight][$libYarnCountDeterId][$diaWidth][$uom]=$reqqnty_finish;
					}
					if(isset($Qty[$this->_sweater][$this->_grey][$poId][$FabGmtColorId][$gsmweight][$libYarnCountDeterId][$diaWidth][$uom]))
					{
						$Qty[$this->_sweater][$this->_grey][$poId][$FabGmtColorId][$gsmweight][$libYarnCountDeterId][$diaWidth][$uom]+=$reqqnty_grey;
					}
					else{
						$Qty[$this->_sweater][$this->_grey][$poId][$FabGmtColorId][$gsmweight][$libYarnCountDeterId][$diaWidth][$uom]=$reqqnty_grey;
					}
				}
			}
			elseif($level==$this->_By_OrderFabColorGsmDeterminIdBodyPartAndDiaWidth){
				if($fabNatureId==2){
					if(isset($Qty[$this->_knit][$this->_finish][$poId][$FabGmtColorId][$gsmweight][$libYarnCountDeterId][$bodyparttypeId][$diaWidth][$uom])){
						$Qty[$this->_knit][$this->_finish][$poId][$FabGmtColorId][$gsmweight][$libYarnCountDeterId][$bodyparttypeId][$diaWidth][$uom]+=$reqqnty_finish;
					}
					else{
						$Qty[$this->_knit][$this->_finish][$poId][$FabGmtColorId][$gsmweight][$libYarnCountDeterId][$bodyparttypeId][$diaWidth][$uom]=$reqqnty_finish;
					}
					if(isset($Qty[$this->_knit][$this->_grey][$poId][$FabGmtColorId][$gsmweight][$libYarnCountDeterId][$bodyparttypeId][$diaWidth][$uom])){
						$Qty[$this->_knit][$this->_grey][$poId][$FabGmtColorId][$gsmweight][$libYarnCountDeterId][$bodyparttypeId][$diaWidth][$uom]+=$reqqnty_grey;
					}
					else{
						$Qty[$this->_knit][$this->_grey][$poId][$FabGmtColorId][$gsmweight][$libYarnCountDeterId][$bodyparttypeId][$diaWidth][$uom]=$reqqnty_grey;
					}
				}
				elseif($fabNatureId==3){
					if(isset($Qty[$this->_woven][$this->_finish][$poId][$FabGmtColorId][$gsmweight][$libYarnCountDeterId][$bodyparttypeId][$diaWidth][$uom])){
						$Qty[$this->_woven][$this->_finish][$poId][$FabGmtColorId][$gsmweight][$libYarnCountDeterId][$bodyparttypeId][$diaWidth][$uom]+=$reqqnty_finish;
					}
					else{
						$Qty[$this->_woven][$this->_finish][$poId][$FabGmtColorId][$gsmweight][$libYarnCountDeterId][$bodyparttypeId][$diaWidth][$uom]=$reqqnty_finish;
					}
					if(isset($Qty[$this->_woven][$this->_grey][$poId][$FabGmtColorId][$gsmweight][$libYarnCountDeterId][$bodyparttypeId][$diaWidth][$uom]))
					{
						$Qty[$this->_woven][$this->_grey][$poId][$FabGmtColorId][$gsmweight][$libYarnCountDeterId][$bodyparttypeId][$diaWidth][$uom]+=$reqqnty_grey;
					}
					else{
						$Qty[$this->_woven][$this->_grey][$poId][$FabGmtColorId][$gsmweight][$libYarnCountDeterId][$bodyparttypeId][$diaWidth][$uom]=$reqqnty_grey;
					}
				}
				elseif($fabNatureId==100){
					if(isset($Qty[$this->_sweater][$this->_finish][$poId][$FabGmtColorId][$gsmweight][$libYarnCountDeterId][$bodyparttypeId][$diaWidth][$uom])){
						$Qty[$this->_sweater][$this->_finish][$poId][$FabGmtColorId][$gsmweight][$libYarnCountDeterId][$bodyparttypeId][$diaWidth][$uom]+=$reqqnty_finish;
					}
					else{
						$Qty[$this->_sweater][$this->_finish][$poId][$FabGmtColorId][$gsmweight][$libYarnCountDeterId][$bodyparttypeId][$diaWidth][$uom]=$reqqnty_finish;
					}
					if(isset($Qty[$this->_sweater][$this->_grey][$poId][$FabGmtColorId][$gsmweight][$libYarnCountDeterId][$bodyparttypeId][$diaWidth][$uom]))
					{
						$Qty[$this->_sweater][$this->_grey][$poId][$FabGmtColorId][$gsmweight][$libYarnCountDeterId][$bodyparttypeId][$diaWidth][$uom]+=$reqqnty_grey;
					}
					else{
						$Qty[$this->_sweater][$this->_grey][$poId][$FabGmtColorId][$gsmweight][$libYarnCountDeterId][$bodyparttypeId][$diaWidth][$uom]=$reqqnty_grey;
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
				elseif($fabNatureId==100){
					if(isset($Qty[$this->_sweater][$this->_finish][$poId][$preCostDtlsId][$colorId][$diaWidth][$remarks][$uom])){
						$Qty[$this->_sweater][$this->_finish][$poId][$preCostDtlsId][$colorId][$diaWidth][$remarks][$uom]+=$reqqnty_finish;
					}
					else{
						$Qty[$this->_sweater][$this->_finish][$poId][$preCostDtlsId][$colorId][$diaWidth][$remarks][$uom]=$reqqnty_finish;
					}
					if(isset($Qty[$this->_sweater][$this->_grey][$poId][$preCostDtlsId][$colorId][$diaWidth][$remarks][$uom]))
					{
						$Qty[$this->_sweater][$this->_grey][$poId][$preCostDtlsId][$colorId][$diaWidth][$remarks][$uom]+=$reqqnty_grey;
					}
					else{
						$Qty[$this->_sweater][$this->_grey][$poId][$preCostDtlsId][$colorId][$diaWidth][$remarks][$uom]=$reqqnty_grey;
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
				elseif($fabNatureId==100){
					if(isset($Qty[$this->_sweater][$this->_finish][$poId][$countryId][$itemNumberId][$colorId][$uom])){
						$Qty[$this->_sweater][$this->_finish][$poId][$countryId][$itemNumberId][$colorId][$uom]+=$reqqnty_finish;
					}
					else{
						$Qty[$this->_sweater][$this->_finish][$poId][$countryId][$itemNumberId][$colorId][$uom]=$reqqnty_finish;
					}
					if(isset($Qty[$this->_sweater][$this->_grey][$poId][$countryId][$itemNumberId][$colorId][$uom])){
						$Qty[$this->_sweater][$this->_grey][$poId][$countryId][$itemNumberId][$colorId][$uom]+=$reqqnty_grey;
					}
					else{
						$Qty[$this->_sweater][$this->_grey][$poId][$countryId][$itemNumberId][$colorId][$uom]=$reqqnty_grey;
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
				elseif($fabNatureId==100){
					if(isset($Qty[$this->_sweater][$this->_finish][$poId][$countryId][$itemNumberId][$sizeId][$uom])){
						$Qty[$this->_sweater][$this->_finish][$poId][$countryId][$itemNumberId][$sizeId][$uom]+=$reqqnty_finish;
					}
					else{
						$Qty[$this->_sweater][$this->_finish][$poId][$countryId][$itemNumberId][$sizeId][$uom]=$reqqnty_finish;
					}
					if(isset($Qty[$this->_sweater][$this->_grey][$poId][$countryId][$itemNumberId][$sizeId][$uom])){
						$Qty[$this->_sweater][$this->_grey][$poId][$countryId][$itemNumberId][$sizeId][$uom]+=$reqqnty_grey;
					}
					else{
						$Qty[$this->_sweater][$this->_grey][$poId][$countryId][$itemNumberId][$sizeId][$uom]=$reqqnty_grey;
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
				elseif($fabNatureId==100){
					if(isset($Qty[$this->_sweater][$this->_finish][$poId][$countryId][$colorId][$sizeId][$uom])){
						$Qty[$this->_sweater][$this->_finish][$poId][$countryId][$colorId][$sizeId][$uom]+=$reqqnty_finish;
					}
					else{
						$Qty[$this->_sweater][$this->_finish][$poId][$countryId][$colorId][$sizeId][$uom]=$reqqnty_finish;
					}
					if(isset($Qty[$this->_sweater][$this->_grey][$poId][$countryId][$colorId][$sizeId][$uom])){
						$Qty[$this->_sweater][$this->_grey][$poId][$countryId][$colorId][$sizeId][$uom]+=$reqqnty_grey;
					}
					else{
						$Qty[$this->_sweater][$this->_grey][$poId][$countryId][$colorId][$sizeId][$uom]=$reqqnty_grey;
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
				elseif($fabNatureId==100){
					if(isset($Qty[$this->_sweater][$this->_finish][$poId][$itemNumberId][$colorId][$sizeId][$uom])){
						$Qty[$this->_sweater][$this->_finish][$poId][$itemNumberId][$colorId][$sizeId][$uom]+=$reqqnty_finish;
					}
					else{
						$Qty[$this->_sweater][$this->_finish][$poId][$itemNumberId][$colorId][$sizeId][$uom]=$reqqnty_finish;
					}
					if(isset($Qty[$this->_sweater][$this->_grey][$poId][$itemNumberId][$colorId][$sizeId][$uom])){
						$Qty[$this->_sweater][$this->_grey][$poId][$itemNumberId][$colorId][$sizeId][$uom]+=$reqqnty_grey;
					}
					else{
						$Qty[$this->_sweater][$this->_grey][$poId][$itemNumberId][$colorId][$sizeId][$uom]=$reqqnty_grey;
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
				elseif($fabNatureId==100){
					if(isset($Qty[$this->_sweater][$this->_finish][$poId][$countryId][$itemNumberId][$colorId][$sizeId][$uom])){
						$Qty[$this->_sweater][$this->_finish][$poId][$countryId][$itemNumberId][$colorId][$sizeId][$uom]+=$reqqnty_finish;
					}
					else{
						$Qty[$this->_sweater][$this->_finish][$poId][$countryId][$itemNumberId][$colorId][$sizeId][$uom]=$reqqnty_finish;
					}
					if(isset($Qty[$this->_sweater][$this->_grey][$poId][$countryId][$itemNumberId][$colorId][$sizeId][$uom])){
						$Qty[$this->_sweater][$this->_grey][$poId][$countryId][$itemNumberId][$colorId][$sizeId][$uom]+=$reqqnty_grey;
					}
					else{
						$Qty[$this->_sweater][$this->_grey][$poId][$countryId][$itemNumberId][$colorId][$sizeId][$uom]=$reqqnty_grey;
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
				elseif($fabNatureId==100){
					if(isset($Qty[$this->_sweater][$this->_finish][$poId][$preCostDtlsId][$uom])){
						$Qty[$this->_sweater][$this->_finish][$poId][$preCostDtlsId][$uom]+=$reqqnty_finish;
					}
					else{
						$Qty[$this->_sweater][$this->_finish][$poId][$preCostDtlsId][$uom]=$reqqnty_finish;
					}
					if(isset($Qty[$this->_sweater][$this->_grey][$poId][$preCostDtlsId][$uom])){
						$Qty[$this->_sweater][$this->_grey][$poId][$preCostDtlsId][$uom]+=$reqqnty_grey;
					}
					else{
						$Qty[$this->_sweater][$this->_grey][$poId][$preCostDtlsId][$uom]=$reqqnty_grey;
					}
				}
			}
			elseif($level==$this->_By_OrderAndFabriccostidSupplier){
				if($fabNatureId==2){
					if(isset($Qty[$this->_knit][$this->_finish][$poId][$preCostDtlsId][$nominated_supp][$uom])){
						$Qty[$this->_knit][$this->_finish][$poId][$preCostDtlsId][$nominated_supp][$uom]+=$reqqnty_finish;
					}
					else{
						$Qty[$this->_knit][$this->_finish][$poId][$preCostDtlsId][$nominated_supp][$uom]=$reqqnty_finish;
					}
					if(isset($Qty[$this->_knit][$this->_grey][$poId][$preCostDtlsId][$nominated_supp][$uom])){
						$Qty[$this->_knit][$this->_grey][$poId][$preCostDtlsId][$nominated_supp][$uom]+=$reqqnty_grey;
					}
					else{
						$Qty[$this->_knit][$this->_grey][$poId][$preCostDtlsId][$nominated_supp][$uom]=$reqqnty_grey;
					}
				}
				elseif($fabNatureId==3){
					if(isset($Qty[$this->_woven][$this->_finish][$poId][$preCostDtlsId][$nominated_supp][$uom])){
						$Qty[$this->_woven][$this->_finish][$poId][$preCostDtlsId][$nominated_supp][$uom]+=$reqqnty_finish;
					}
					else{
						$Qty[$this->_woven][$this->_finish][$poId][$preCostDtlsId][$nominated_supp][$uom]=$reqqnty_finish;
					}
					if(isset($Qty[$this->_woven][$this->_grey][$poId][$preCostDtlsId][$nominated_supp][$uom])){
						$Qty[$this->_woven][$this->_grey][$poId][$preCostDtlsId][$nominated_supp][$uom]+=$reqqnty_grey;
					}
					else{
						$Qty[$this->_woven][$this->_grey][$poId][$preCostDtlsId][$nominated_supp][$uom]=$reqqnty_grey;
					}
				}
				elseif($fabNatureId==100){
					if(isset($Qty[$this->_sweater][$this->_finish][$poId][$preCostDtlsId][$nominated_supp][$uom])){
						$Qty[$this->_sweater][$this->_finish][$poId][$preCostDtlsId][$nominated_supp][$uom]+=$reqqnty_finish;
					}
					else{
						$Qty[$this->_sweater][$this->_finish][$poId][$preCostDtlsId][$nominated_supp][$uom]=$reqqnty_finish;
					}
					if(isset($Qty[$this->_sweater][$this->_grey][$poId][$preCostDtlsId][$nominated_supp][$uom])){
						$Qty[$this->_sweater][$this->_grey][$poId][$preCostDtlsId][$nominated_supp][$uom]+=$reqqnty_grey;
					}
					else{
						$Qty[$this->_sweater][$this->_grey][$poId][$preCostDtlsId][$nominated_supp][$uom]=$reqqnty_grey;
					}
				}
			}
			
				elseif($level==$this->_By_OrderAndConfirmedid){
				if($fabNatureId==2){
					if(isset($Qty[$this->_knit][$this->_finish][$poId][$isConfirmed][$uom])){
						$Qty[$this->_knit][$this->_finish][$poId][$isConfirmed][$uom]+=$reqqnty_finish;
					}
					else{
						$Qty[$this->_knit][$this->_finish][$poId][$isConfirmed][$uom]=$reqqnty_finish;
					}
					if(isset($Qty[$this->_knit][$this->_grey][$poId][$isConfirmed][$uom])){
						$Qty[$this->_knit][$this->_grey][$poId][$isConfirmed][$uom]+=$reqqnty_grey;
					}
					else{
						$Qty[$this->_knit][$this->_grey][$poId][$isConfirmed][$uom]=$reqqnty_grey;
					}
				}
				elseif($fabNatureId==3){
					if(isset($Qty[$this->_woven][$this->_finish][$poId][$isConfirmed][$uom])){
						$Qty[$this->_woven][$this->_finish][$poId][$isConfirmed][$uom]+=$reqqnty_finish;
					}
					else{
						$Qty[$this->_woven][$this->_finish][$poId][$isConfirmed][$uom]=$reqqnty_finish;
					}
					if(isset($Qty[$this->_woven][$this->_grey][$poId][$isConfirmed][$uom])){
						$Qty[$this->_woven][$this->_grey][$poId][$isConfirmed][$uom]+=$reqqnty_grey;
					}
					else{
						$Qty[$this->_woven][$this->_grey][$poId][$isConfirmed][$uom]=$reqqnty_grey;
					}
				}
				elseif($fabNatureId==100){
					if(isset($Qty[$this->_sweater][$this->_finish][$poId][$isConfirmed][$uom])){
						$Qty[$this->_sweater][$this->_finish][$poId][$isConfirmed][$uom]+=$reqqnty_finish;
					}
					else{
						$Qty[$this->_sweater][$this->_finish][$poId][$isConfirmed][$uom]=$reqqnty_finish;
					}
					if(isset($Qty[$this->_sweater][$this->_grey][$poId][$isConfirmed][$uom])){
						$Qty[$this->_sweater][$this->_grey][$poId][$isConfirmed][$uom]+=$reqqnty_grey;
					}
					else{
						$Qty[$this->_sweater][$this->_grey][$poId][$isConfirmed][$uom]=$reqqnty_grey;
					}
				}
			}
			elseif($level==$this->_By_OrderConfirmedidAndCountryid){
				if($fabNatureId==2){
					if(isset($Qty[$this->_knit][$this->_finish][$poId][$isConfirmed][$countryId][$uom])){
						$Qty[$this->_knit][$this->_finish][$poId][$isConfirmed][$countryId][$uom]+=$reqqnty_finish;
					}
					else{
						$Qty[$this->_knit][$this->_finish][$poId][$isConfirmed][$countryId][$uom]=$reqqnty_finish;
					}
					if(isset($Qty[$this->_knit][$this->_grey][$poId][$isConfirmed][$countryId][$uom])){
						$Qty[$this->_knit][$this->_grey][$poId][$isConfirmed][$countryId][$uom]+=$reqqnty_grey;
					}
					else{
						$Qty[$this->_knit][$this->_grey][$poId][$isConfirmed][$countryId][$uom]=$reqqnty_grey;
					}
				}
				elseif($fabNatureId==3){
					if(isset($Qty[$this->_woven][$this->_finish][$poId][$isConfirmed][$countryId][$uom])){
						$Qty[$this->_woven][$this->_finish][$poId][$isConfirmed][$countryId][$uom]+=$reqqnty_finish;
					}
					else{
						$Qty[$this->_woven][$this->_finish][$poId][$isConfirmed][$countryId][$uom]=$reqqnty_finish;
					}
					if(isset($Qty[$this->_woven][$this->_grey][$poId][$isConfirmed][$countryId][$uom])){
						$Qty[$this->_woven][$this->_grey][$poId][$isConfirmed][$countryId][$uom]+=$reqqnty_grey;
					}
					else{
						$Qty[$this->_woven][$this->_grey][$poId][$isConfirmed][$countryId][$uom]=$reqqnty_grey;
					}
				}
				elseif($fabNatureId==100){
					if(isset($Qty[$this->_sweater][$this->_finish][$poId][$isConfirmed][$countryId][$uom])){
						$Qty[$this->_sweater][$this->_finish][$poId][$isConfirmed][$countryId][$uom]+=$reqqnty_finish;
					}
					else{
						$Qty[$this->_sweater][$this->_finish][$poId][$isConfirmed][$Countryid][$uom]=$reqqnty_finish;
					}
					if(isset($Qty[$this->_sweater][$this->_grey][$poId][$isConfirmed][$countryId][$uom])){
						$Qty[$this->_sweater][$this->_grey][$poId][$isConfirmed][$countryId][$uom]+=$reqqnty_grey;
					}
					else{
						$Qty[$this->_sweater][$this->_grey][$poId][$isConfirmed][$countryId][$uom]=$reqqnty_grey;
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
				elseif($fabNatureId==100){
					if(isset($Qty[$this->_sweater][$this->_finish][$poId][$preCostDtlsId][$colorId][$uom])){
						$Qty[$this->_sweater][$this->_finish][$poId][$preCostDtlsId][$colorId][$uom]+=$reqqnty_finish;
					}
					else{
						$Qty[$this->_sweater][$this->_finish][$poId][$preCostDtlsId][$colorId][$uom]=$reqqnty_finish;
					}
					if(isset($Qty[$this->_sweater][$this->_grey][$poId][$preCostDtlsId][$colorId][$uom])){
						$Qty[$this->_sweater][$this->_grey][$poId][$preCostDtlsId][$colorId][$uom]+=$reqqnty_grey;
					}
					else{
						$Qty[$this->_sweater][$this->_grey][$poId][$preCostDtlsId][$colorId][$uom]=$reqqnty_grey;
					}
				}
			}
			elseif($level==$this->_By_OrderColorTypeAndGmtscolor){ 
				if($fabNatureId==2){
					if(isset($Qty[$this->_knit][$this->_finish][$poId][$colorTypeId][$colorId][$uom])){
						$Qty[$this->_knit][$this->_finish][$poId][$colorTypeId][$colorId][$uom]+=$reqqnty_finish;
					}
					else{
						$Qty[$this->_knit][$this->_finish][$poId][$colorTypeId][$colorId][$uom]=$reqqnty_finish;
					}
					if(isset($Qty[$this->_knit][$this->_grey][$poId][$colorTypeId][$colorId][$uom])){
						$Qty[$this->_knit][$this->_grey][$poId][$colorTypeId][$colorId][$uom]+=$reqqnty_grey;
					}
					else{
						$Qty[$this->_knit][$this->_grey][$poId][$colorTypeId][$colorId][$uom]=$reqqnty_grey;
					}
				}
				elseif($fabNatureId==3){
					if(isset($Qty[$this->_woven][$this->_finish][$poId][$colorTypeId][$colorId][$uom])){
						$Qty[$this->_woven][$this->_finish][$poId][$colorTypeId][$colorId][$uom]+=$reqqnty_finish;
					}
					else{
						$Qty[$this->_woven][$this->_finish][$poId][$colorTypeId][$colorId][$uom]=$reqqnty_finish;
					}
					if(isset($Qty[$this->_woven][$this->_grey][$poId][$colorTypeId][$colorId][$uom])){
						$Qty[$this->_woven][$this->_grey][$poId][$colorTypeId][$colorId][$uom]+=$reqqnty_grey;
					}
					else{
						$Qty[$this->_woven][$this->_grey][$poId][$colorTypeId][$colorId][$uom]=$reqqnty_grey;
					}
				}
				elseif($fabNatureId==100){
					if(isset($Qty[$this->_sweater][$this->_finish][$poId][$colorTypeId][$colorId][$uom])){
						$Qty[$this->_sweater][$this->_finish][$poId][$colorTypeId][$colorId][$uom]+=$reqqnty_finish;
					}
					else{
						$Qty[$this->_sweater][$this->_finish][$poId][$colorTypeId][$colorId][$uom]=$reqqnty_finish;
					}
					if(isset($Qty[$this->_sweater][$this->_grey][$poId][$colorTypeId][$colorId][$uom])){
						$Qty[$this->_sweater][$this->_grey][$poId][$colorTypeId][$colorId][$uom]+=$reqqnty_grey;
					}
					else{
						$Qty[$this->_sweater][$this->_grey][$poId][$colorTypeId][$colorId][$uom]=$reqqnty_grey;
					}
				}
			}
			elseif($level==$this->_By_OrderColorType){ 
				if($fabNatureId==2){
					if(isset($Qty[$this->_knit][$this->_finish][$poId][$colorTypeId][$uom])){
						$Qty[$this->_knit][$this->_finish][$poId][$colorTypeId][$uom]+=$reqqnty_finish;
					}
					else{
						$Qty[$this->_knit][$this->_finish][$poId][$colorTypeId][$uom]=$reqqnty_finish;
					}
					if(isset($Qty[$this->_knit][$this->_grey][$poId][$colorTypeId][$uom])){
						$Qty[$this->_knit][$this->_grey][$poId][$colorTypeId][$uom]+=$reqqnty_grey;
					}
					else{
						$Qty[$this->_knit][$this->_grey][$poId][$colorTypeId][$uom]=$reqqnty_grey;
					}
				}
				elseif($fabNatureId==3){
					if(isset($Qty[$this->_woven][$this->_finish][$poId][$colorTypeId][$uom])){
						$Qty[$this->_woven][$this->_finish][$poId][$colorTypeId][$uom]+=$reqqnty_finish;
					}
					else{
						$Qty[$this->_woven][$this->_finish][$poId][$colorTypeId][$uom]=$reqqnty_finish;
					}
					if(isset($Qty[$this->_woven][$this->_grey][$poId][$colorTypeId][$uom])){
						$Qty[$this->_woven][$this->_grey][$poId][$colorTypeId][$uom]+=$reqqnty_grey;
					}
					else{
						$Qty[$this->_woven][$this->_grey][$poId][$colorTypeId][$uom]=$reqqnty_grey;
					}
				}
				elseif($fabNatureId==100){
					if(isset($Qty[$this->_sweater][$this->_finish][$poId][$colorTypeId][$uom])){
						$Qty[$this->_sweater][$this->_finish][$poId][$colorTypeId][$uom]+=$reqqnty_finish;
					}
					else{
						$Qty[$this->_sweater][$this->_finish][$poId][$colorTypeId][$uom]=$reqqnty_finish;
					}
					if(isset($Qty[$this->_sweater][$this->_grey][$poId][$colorTypeId][$uom])){
						$Qty[$this->_sweater][$this->_grey][$poId][$colorTypeId][$uom]+=$reqqnty_grey;
					}
					else{
						$Qty[$this->_sweater][$this->_grey][$poId][$colorTypeId][$uom]=$reqqnty_grey;
					}
				}
			}
			
			elseif($level==$this->_By_JobAndGmtscolor){  //Job
				if($fabNatureId==2){
					if(isset($Qty[$this->_knit][$this->_finish][$jobNo][$colorId][$fabricSourceId][$uom])){
						$Qty[$this->_knit][$this->_finish][$jobNo][$colorId][$fabricSourceId][$uom]+=$reqqnty_finish;
					}
					else{
						$Qty[$this->_knit][$this->_finish][$jobNo][$colorId][$fabricSourceId][$uom]=$reqqnty_finish;
					}
					if(isset($Qty[$this->_knit][$this->_grey][$jobNo][$colorId][$fabricSourceId][$uom])){
						$Qty[$this->_knit][$this->_grey][$jobNo][$colorId][$fabricSourceId][$uom]+=$reqqnty_grey;
					}
					else{
						$Qty[$this->_knit][$this->_grey][$jobNo][$colorId][$fabricSourceId][$uom]=$reqqnty_grey;
					}
				}
				elseif($fabNatureId==3){
					if(isset($Qty[$this->_woven][$this->_finish][$jobNo][$colorId][$fabricSourceId][$uom])){
						$Qty[$this->_woven][$this->_finish][$jobNo][$colorId][$fabricSourceId][$uom]+=$reqqnty_finish;
					}
					else{
						$Qty[$this->_woven][$this->_finish][$jobNo][$colorId][$fabricSourceId][$uom]=$reqqnty_finish;
					}
					if(isset($Qty[$this->_woven][$this->_grey][$jobNo][$colorId][$fabricSourceId][$uom])){
						$Qty[$this->_woven][$this->_grey][$jobNo][$colorId][$fabricSourceId][$uom]+=$reqqnty_grey;
					}
					else{
						$Qty[$this->_woven][$this->_grey][$jobNo][$colorId][$fabricSourceId][$uom]=$reqqnty_grey;
					}
				}
				elseif($fabNatureId==100){
					if(isset($Qty[$this->_sweater][$this->_finish][$jobNo][$colorId][$fabricSourceId][$uom])){
						$Qty[$this->_sweater][$this->_finish][$jobNo][$colorId][$fabricSourceId][$uom]+=$reqqnty_finish;
					}
					else{
						$Qty[$this->_sweater][$this->_finish][$jobNo][$colorId][$fabricSourceId][$uom]=$reqqnty_finish;
					}
					if(isset($Qty[$this->_sweater][$this->_grey][$jobNo][$colorId][$fabricSourceId][$uom])){
						$Qty[$this->_sweater][$this->_grey][$jobNo][$colorId][$fabricSourceId][$uom]+=$reqqnty_grey;
					}
					else{
						$Qty[$this->_sweater][$this->_grey][$jobNo][$colorId][$fabricSourceId][$uom]=$reqqnty_grey;
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
				elseif($fabNatureId==100){
					if(isset($Qty[$this->_sweater][$this->_finish][$poId][$countryId][$preCostDtlsId][$uom])){
						$Qty[$this->_sweater][$this->_finish][$poId][$countryId][$preCostDtlsId][$uom]+=$reqqnty_finish;
					}
					else{
						$Qty[$this->_sweater][$this->_finish][$poId][$countryId][$preCostDtlsId][$uom]=$reqqnty_finish;
					}
					if(isset($Qty[$this->_sweater][$this->_grey][$poId][$countryId][$preCostDtlsId][$uom])){
						$Qty[$this->_sweater][$this->_grey][$poId][$countryId][$preCostDtlsId][$uom]+=$reqqnty_grey;
					}
					else{
						$Qty[$this->_sweater][$this->_grey][$poId][$countryId][$preCostDtlsId][$uom]=$reqqnty_grey;
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
				elseif($fabNatureId==100){
					if(isset($Qty[$this->_sweater][$this->_finish][$preCostDtlsId][$uom])){
						$Qty[$this->_sweater][$this->_finish][$preCostDtlsId][$uom]+=$reqqnty_finish;
					}
					else{
						$Qty[$this->_sweater][$this->_finish][$preCostDtlsId][$uom]=$reqqnty_finish;
					}
					if(isset($Qty[$this->_sweater][$this->_grey][$preCostDtlsId][$uom])){
						$Qty[$this->_sweater][$this->_grey][$preCostDtlsId][$uom]+=$reqqnty_grey;
					}
					else{
						$Qty[$this->_sweater][$this->_grey][$preCostDtlsId][$uom]=$reqqnty_grey;
					}
				}
			}
			elseif($level==$this->_By_OrderAndFabricSource){
				if($fabNatureId==2){
					if(isset($Qty[$this->_knit][$this->_finish][$poId][$fabric_source][$uom])){
						$Qty[$this->_knit][$this->_finish][$poId][$fabric_source][$uom]+=$reqqnty_finish;
					}
					else{
						$Qty[$this->_knit][$this->_finish][$poId][$fabric_source][$uom]=$reqqnty_finish;
					}
					if(isset($Qty[$this->_knit][$this->_grey][$poId][$fabric_source][$uom])){
						$Qty[$this->_knit][$this->_grey][$poId][$fabric_source][$uom]+=$reqqnty_grey;
					}
					else{
						$Qty[$this->_knit][$this->_grey][$poId][$fabric_source][$uom]=$reqqnty_grey;
					}
				}
				elseif($fabNatureId==3){
					if(isset($Qty[$this->_woven][$this->_finish][$poId][$fabric_source][$uom])){
						$Qty[$this->_woven][$this->_finish][$poId][$fabric_source][$uom]+=$reqqnty_finish;
					}
					else{
						$Qty[$this->_woven][$this->_finish][$poId][$fabric_source][$uom]=$reqqnty_finish;
					}
					if(isset($Qty[$this->_woven][$this->_grey][$poId][$fabric_source][$uom])){
						$Qty[$this->_woven][$this->_grey][$poId][$fabric_source][$uom]+=$reqqnty_grey;
					}
					else{
						$Qty[$this->_woven][$this->_grey][$poId][$fabric_source][$uom]=$reqqnty_grey;
					}
				}
				elseif($fabNatureId==100){
					if(isset($Qty[$this->_sweater][$this->_finish][$poId][$fabric_source][$uom])){
						$Qty[$this->_sweater][$this->_finish][$poId][$fabric_source][$uom]+=$reqqnty_finish;
					}
					else{
						$Qty[$this->_sweater][$this->_finish][$poId][$fabric_source][$uom]=$reqqnty_finish;
					}
					if(isset($Qty[$this->_sweater][$this->_grey][$poId][$fabric_source][$uom])){
						$Qty[$this->_sweater][$this->_grey][$poId][$fabric_source][$uom]+=$reqqnty_grey;
					}
					else{
						$Qty[$this->_sweater][$this->_grey][$poId][$fabric_source][$uom]=$reqqnty_grey;
					}
				}
			}
			elseif($level==$this->_By_OrderAndColorTypeFabricSource){
				if($fabNatureId==2){
					if(isset($Qty[$this->_knit][$this->_finish][$poId][$colorTypeId][$fabric_source][$uom])){
						$Qty[$this->_knit][$this->_finish][$poId][$colorTypeId][$fabric_source][$uom]+=$reqqnty_finish;
					}
					else{
						$Qty[$this->_knit][$this->_finish][$poId][$colorTypeId][$fabric_source][$uom]=$reqqnty_finish;
					}
					if(isset($Qty[$this->_knit][$this->_grey][$poId][$colorTypeId][$fabric_source][$uom])){
						$Qty[$this->_knit][$this->_grey][$poId][$colorTypeId][$fabric_source][$uom]+=$reqqnty_grey;
					}
					else{
						$Qty[$this->_knit][$this->_grey][$poId][$colorTypeId][$fabric_source][$uom]=$reqqnty_grey;
					}
				}
				elseif($fabNatureId==3){
					if(isset($Qty[$this->_woven][$this->_finish][$poId][$colorTypeId][$fabric_source][$uom])){
						$Qty[$this->_woven][$this->_finish][$poId][$colorTypeId][$fabric_source][$uom]+=$reqqnty_finish;
					}
					else{
						$Qty[$this->_woven][$this->_finish][$poId][$colorTypeId][$fabric_source][$uom]=$reqqnty_finish;
					}
					if(isset($Qty[$this->_woven][$this->_grey][$poId][$colorTypeId][$fabric_source][$uom])){
						$Qty[$this->_woven][$this->_grey][$poId][$colorTypeId][$fabric_source][$uom]+=$reqqnty_grey;
					}
					else{
						$Qty[$this->_woven][$this->_grey][$poId][$colorTypeId][$fabric_source][$uom]=$reqqnty_grey;
					}
				}
				elseif($fabNatureId==100){
					if(isset($Qty[$this->_sweater][$this->_finish][$poId][$colorTypeId][$fabric_source][$uom])){
						$Qty[$this->_sweater][$this->_finish][$poId][$colorTypeId][$fabric_source][$uom]+=$reqqnty_finish;
					}
					else{
						$Qty[$this->_sweater][$this->_finish][$poId][$colorTypeId][$fabric_source][$uom]=$reqqnty_finish;
					}
					if(isset($Qty[$this->_sweater][$this->_grey][$poId][$colorTypeId][$fabric_source][$uom])){
						$Qty[$this->_sweater][$this->_grey][$poId][$colorTypeId][$fabric_source][$uom]+=$reqqnty_grey;
					}
					else{
						$Qty[$this->_sweater][$this->_grey][$poId][$colorTypeId][$fabric_source][$uom]=$reqqnty_grey;
					}
				}
			}
			elseif($level==$this->_By_OrderAndSourceId){ 
				if($fabNatureId==2){
					if(isset($Qty[$this->_knit][$this->_finish][$poId][$sourceId][$uom])){
						$Qty[$this->_knit][$this->_finish][$poId][$sourceId][$uom]+=$reqqnty_finish;
					}
					else{
						$Qty[$this->_knit][$this->_finish][$poId][$sourceId][$uom]=$reqqnty_finish;
					}
					if(isset($Qty[$this->_knit][$this->_grey][$poId][$sourceId][$uom])){
						$Qty[$this->_knit][$this->_grey][$poId][$sourceId][$uom]+=$reqqnty_grey;
					}
					else{
						$Qty[$this->_knit][$this->_grey][$poId][$sourceId][$uom]=$reqqnty_grey;
					}
				}
				elseif($fabNatureId==3){
					if(isset($Qty[$this->_woven][$this->_finish][$poId][$sourceId][$uom])){
						$Qty[$this->_woven][$this->_finish][$poId][$sourceId][$uom]+=$reqqnty_finish;
					}
					else{
						$Qty[$this->_woven][$this->_finish][$poId][$sourceId][$uom]=$reqqnty_finish;
					}
					if(isset($Qty[$this->_woven][$this->_grey][$poId][$sourceId][$uom])){
						$Qty[$this->_woven][$this->_grey][$poId][$sourceId][$uom]+=$reqqnty_grey;
					}
					else{
						$Qty[$this->_woven][$this->_grey][$poId][$sourceId][$uom]=$reqqnty_grey;
					}
				}
				elseif($fabNatureId==100){
					if(isset($Qty[$this->_sweater][$this->_finish][$poId][$sourceId][$uom])){
						$Qty[$this->_sweater][$this->_finish][$poId][$sourceId][$uom]+=$reqqnty_finish;
					}
					else{
						$Qty[$this->_sweater][$this->_finish][$poId][$sourceId][$uom]=$reqqnty_finish;
					}
					if(isset($Qty[$this->_sweater][$this->_grey][$poId][$sourceId][$uom])){
						$Qty[$this->_sweater][$this->_grey][$poId][$sourceId][$uom]+=$reqqnty_grey;
					}
					else{
						$Qty[$this->_sweater][$this->_grey][$poId][$sourceId][$uom]=$reqqnty_grey;
					}
				}
			}
			elseif($level==$this->_By_CountryAndCutupdate){
				if($fabNatureId==2){
					if(isset($Qty[$this->_knit][$this->_finish][$countryId][$cutupDate][$uom])){
						$Qty[$this->_knit][$this->_finish][$countryId][$cutupDate][$uom]+=$reqqnty_finish;
					}
					else{
						$Qty[$this->_knit][$this->_finish][$countryId][$cutupDate][$uom]=$reqqnty_finish;
					}
					if(isset($Qty[$this->_knit][$this->_grey][$countryId][$cutupDate][$uom])){
						$Qty[$this->_knit][$this->_grey][$countryId][$cutupDate][$uom]+=$reqqnty_grey;
					}
					else{
						$Qty[$this->_knit][$this->_grey][$countryId][$cutupDate][$uom]=$reqqnty_grey;
					}
				}
				elseif($fabNatureId==3){
					if(isset($Qty[$this->_woven][$this->_finish][$countryId][$cutupDate][$uom])){
						$Qty[$this->_woven][$this->_finish][$countryId][$cutupDate][$uom]+=$reqqnty_finish;
					}
					else{
						$Qty[$this->_woven][$this->_finish][$countryId][$cutupDate][$uom]=$reqqnty_finish;
					}
					if(isset($Qty[$this->_woven][$this->_grey][$countryId][$cutupDate][$uom])){
						$Qty[$this->_woven][$this->_grey][$countryId][$cutupDate][$uom]+=$reqqnty_grey;
					}
					else{
						$Qty[$this->_woven][$this->_grey][$countryId][$cutupDate][$uom]=$reqqnty_grey;
					}
				}
				elseif($fabNatureId==100){
					if(isset($Qty[$this->_sweater][$this->_finish][$countryId][$cutupDate][$uom])){
						$Qty[$this->_sweater][$this->_finish][$countryId][$cutupDate][$uom]+=$reqqnty_finish;
					}
					else{
						$Qty[$this->_sweater][$this->_finish][$countryId][$cutupDate][$uom]=$reqqnty_finish;
					}
					if(isset($Qty[$this->_sweater][$this->_grey][$countryId][$cutupDate][$uom])){
						$Qty[$this->_sweater][$this->_grey][$countryId][$cutupDate][$uom]+=$reqqnty_grey;
					}
					else{
						$Qty[$this->_sweater][$this->_grey][$countryId][$cutupDate][$uom]=$reqqnty_grey;
					}
				}
			}
			elseif($level==$this->_By_CountryCutupdateAndGmtsColor){
				if($fabNatureId==2){
					if(isset($Qty[$this->_knit][$this->_finish][$countryId][$cutupDate][$colorId][$uom])){
						$Qty[$this->_knit][$this->_finish][$countryId][$cutupDate][$colorId][$uom]+=$reqqnty_finish;
					}
					else{
						$Qty[$this->_knit][$this->_finish][$countryId][$cutupDate][$colorId][$uom]=$reqqnty_finish;
					}
					if(isset($Qty[$this->_knit][$this->_grey][$countryId][$cutupDate][$colorId][$uom])){
						$Qty[$this->_knit][$this->_grey][$countryId][$cutupDate][$colorId][$uom]+=$reqqnty_grey;
					}
					else{
						$Qty[$this->_knit][$this->_grey][$countryId][$cutupDate][$colorId][$uom]=$reqqnty_grey;
					}
				}
				elseif($fabNatureId==3){
					if(isset($Qty[$this->_woven][$this->_finish][$countryId][$cutupDate][$colorId][$uom])){
						$Qty[$this->_woven][$this->_finish][$countryId][$cutupDate][$colorId][$uom]+=$reqqnty_finish;
					}
					else{
						$Qty[$this->_woven][$this->_finish][$countryId][$cutupDate][$colorId][$uom]=$reqqnty_finish;
					}
					if(isset($Qty[$this->_woven][$this->_grey][$countryId][$cutupDate][$colorId][$uom])){
						$Qty[$this->_woven][$this->_grey][$countryId][$cutupDate][$colorId][$uom]+=$reqqnty_grey;
					}
					else{
						$Qty[$this->_woven][$this->_grey][$countryId][$cutupDate][$colorId][$uom]=$reqqnty_grey;
					}
				}
				elseif($fabNatureId==100){
					if(isset($Qty[$this->_sweater][$this->_finish][$countryId][$cutupDate][$colorId][$uom])){
						$Qty[$this->_sweater][$this->_finish][$countryId][$cutupDate][$colorId][$uom]+=$reqqnty_finish;
					}
					else{
						$Qty[$this->_sweater][$this->_finish][$countryId][$cutupDate][$colorId][$uom]=$reqqnty_finish;
					}
					if(isset($Qty[$this->_sweater][$this->_grey][$countryId][$cutupDate][$colorId][$uom])){
						$Qty[$this->_sweater][$this->_grey][$countryId][$cutupDate][$colorId][$uom]+=$reqqnty_grey;
					}
					else{
						$Qty[$this->_sweater][$this->_grey][$countryId][$cutupDate][$colorId][$uom]=$reqqnty_grey;
					}
				}
			}
			elseif($level==$this->_By_JobFabricIdItemBobyPartGmtsColorGsmDia){
				if($fabNatureId==2){
					if(isset($Qty[$this->_knit][$this->_finish][$jobNo][$preCostDtlsId][$itemNumberId][$bodypartId][$colorId][$gsmweight][$diaWidth])){
						$Qty[$this->_knit][$this->_finish][$jobNo][$preCostDtlsId][$itemNumberId][$bodypartId][$colorId][$gsmweight][$diaWidth]+=$reqqnty_finish;
					}
					else{
						$Qty[$this->_knit][$this->_finish][$jobNo][$preCostDtlsId][$itemNumberId][$bodypartId][$colorId][$gsmweight][$diaWidth]=$reqqnty_finish;
					}
					if(isset($Qty[$this->_knit][$this->_grey][$jobNo][$preCostDtlsId][$itemNumberId][$bodypartId][$colorId][$gsmweight][$diaWidth])){
						$Qty[$this->_knit][$this->_grey][$jobNo][$preCostDtlsId][$itemNumberId][$bodypartId][$colorId][$gsmweight][$diaWidth]+=$reqqnty_grey;
					}
					else{
						$Qty[$this->_knit][$this->_grey][$jobNo][$preCostDtlsId][$itemNumberId][$bodypartId][$colorId][$gsmweight][$diaWidth]=$reqqnty_grey;
					}
				}
				elseif($fabNatureId==3){
					if(isset($Qty[$this->_woven][$this->_finish][$jobNo][$preCostDtlsId][$itemNumberId][$bodypartId][$colorId][$gsmweight][$diaWidth])){
						$Qty[$this->_woven][$this->_finish][$jobNo][$preCostDtlsId][$itemNumberId][$bodypartId][$colorId][$gsmweight][$diaWidth]+=$reqqnty_finish;
					}
					else{
						$Qty[$this->_woven][$this->_finish][$jobNo][$preCostDtlsId][$itemNumberId][$bodypartId][$colorId][$gsmweight][$diaWidth]=$reqqnty_finish;
					}
					if(isset($Qty[$this->_woven][$this->_grey][$jobNo][$preCostDtlsId][$itemNumberId][$bodypartId][$colorId][$gsmweight][$diaWidth])){
						$Qty[$this->_woven][$this->_grey][$jobNo][$preCostDtlsId][$itemNumberId][$bodypartId][$colorId][$gsmweight][$diaWidth]+=$reqqnty_grey;
					}
					else{
						$Qty[$this->_woven][$this->_grey][$jobNo][$preCostDtlsId][$itemNumberId][$bodypartId][$colorId][$gsmweight][$diaWidth]=$reqqnty_grey;
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
		$itemNumberId='';$isConfirmed='';$sourceId='';
		$planPutQnty=0; $orderQuantity=0;
		$bodypartId='';
		$requirment=0;
		$poId='';$nominated_supp='';$item_size='';
		$fabric_source='';$FabGmtColorId='';
		$budget_on=2;
		$Amount=array();
		foreach($this->_dataArray as $row)
		{
			$jobId = $row['job_id'];
			$jobNo=$row['job_no'];
			$poId=$row['id'];
			$nominated_supp=$row['nominated_supp'];
			$isConfirmed=$row['is_confirmed'];
			$itemNumberId=$row['item_number_id'];
			$item_size=$row['item_size'];
			$sourceId=$row['source_id'];
			$countryId=$row['country_id'];
			$colorId=$row['color_number_id'];
			$FabricColorId=$row['contrast_color_id'];
			$colorTypeId=$row['color_type_id'];
			$sizeId=$row['size_number_id'];
			$planPutQnty=$row['plan_cut_qnty'];
			$orderQuantity=$row['order_quantity'];
			$preCostDtlsId=$row['pre_cost_dtls_id'];
			$bodypartId=$row['body_part_id'];
			$fabNatureId=$row['fab_nature_id'];
			$libYarnCountDeterId=$row['lib_yarn_count_deter_id'];
			$diaWidth=$row['dia_width'];
			$bodyparttypeId=$row['body_part_type'];
			$gsmweight=$row['gsm_weight'];
			$countryShipDate=$row['country_ship_date'];
			$widthDiaType=$row['width_dia_type'];
			$remarks=trim($row['remarks']);
			$cons=$row['cons'];
			$requirment=$row['requirment'];
			$rate=$row['rate'];	
			$sourcing_rate=$row['sourcing_rate'];
			$uom=$row['uom'];
			$fabric_source=$row['fabric_source'];
			$budget_on=$row['budget_on'];
			if($FabricColorId=='')
			{
				$FabGmtColorId=$colorId;
			}
			else $FabGmtColorId=$FabricColorId;
			
			
			$fabricColorId=$row['stripe_color'];
			
			if(!$fabricColorId){
				$fabricColorId=$row['contrast_color_id'];
			}
			if(!$fabricColorId){
				$fabricColorId=$row['color_number_id'];
			}
			
			
			if($budget_on==0 || $budget_on=="") $budget_on=2;
			$costingPerQty=$this->_costingPerQtyArr[$jobNo];
			$set_item_ratio=$this->_gmtsitemRatioArray[$jobNo][$itemNumberId];
			
			if($budget_on==1)
			{
				$reqqnty_finish =$this->_calculateQty($orderQuantity,$costingPerQty,$set_item_ratio,$cons);
				$reqqnty_grey =$this->_calculateQty($orderQuantity,$costingPerQty,$set_item_ratio,$requirment);
			}
			else
			{
				$reqqnty_finish =$this->_calculateQty($planPutQnty,$costingPerQty,$set_item_ratio,$cons);
				$reqqnty_grey =$this->_calculateQty($planPutQnty,$costingPerQty,$set_item_ratio,$requirment);
			}
			$amount_finish=$this->_calculateAmount($reqqnty_finish,$rate);
			$amount_grey=$this->_calculateAmount($reqqnty_grey,$rate);
			
			$sourcing_amount_finish=$this->_calculateAmount($reqqnty_finish,$sourcing_rate);
			$sourcing_amount_grey=$this->_calculateAmount($reqqnty_grey,$sourcing_rate);
			
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
				elseif($fabNatureId==100){
					if(isset($Amount[$this->_sweater][$this->_finish][$jobNo][$uom])){
						$Amount[$this->_sweater][$this->_finish][$jobNo][$uom]+=$amount_finish;
					}
					else{
						$Amount[$this->_sweater][$this->_finish][$jobNo][$uom]=$amount_finish;
					}
					if(isset($Amount[$this->_sweater][$this->_grey][$jobNo][$uom])){
						$Amount[$this->_sweater][$this->_grey][$jobNo][$uom]+=$amount_grey;
					}
					else{
						$Amount[$this->_sweater][$this->_grey][$jobNo][$uom]=$amount_grey;
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
				elseif($fabNatureId==100){
					if(isset($Amount[$this->_sweater][$this->_finish][$poId][$uom])){
						$Amount[$this->_sweater][$this->_finish][$poId][$uom]+=$amount_finish;
					}
					else{
						$Amount[$this->_sweater][$this->_finish][$poId][$uom]=$amount_finish;
					}
					if(isset($Amount[$this->_sweater][$this->_grey][$poId][$uom])){
						$Amount[$this->_sweater][$this->_grey][$poId][$uom]+=$amount_grey;
					}
					else{
						$Amount[$this->_sweater][$this->_grey][$poId][$uom]=$amount_grey;
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
				elseif($fabNatureId==100){
					if(isset($Amount[$this->_sweater][$this->_finish][$poId][$countryId][$uom])){
						$Amount[$this->_sweater][$this->_finish][$poId][$countryId][$uom]+=$amount_finish;
					}
					else{
						$Amount[$this->_sweater][$this->_finish][$poId][$countryId][$uom]=$amount_finish;
					}
					if(isset($Amount[$this->_sweater][$this->_grey][$poId][$countryId][$uom])){
						$Amount[$this->_sweater][$this->_grey][$poId][$countryId][$uom]+=$amount_grey;
					}
					else{
						$Amount[$this->_sweater][$this->_grey][$poId][$countryId][$uom]=$amount_grey;
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
				elseif($fabNatureId==100){
					if(isset($Amount[$this->_sweater][$this->_finish][$poId][$itemNumberId][$uom])){
						$Amount[$this->_sweater][$this->_finish][$poId][$itemNumberId][$uom]+=$amount_finish;
					}
					else{
						$Amount[$this->_sweater][$this->_finish][$poId][$itemNumberId][$uom]+=$amount_finish;
					}
					if(isset($Amount[$this->_sweater][$this->_grey][$poId][$itemNumberId][$uom])){
						$Amount[$this->_sweater][$this->_grey][$poId][$itemNumberId][$uom]+=$amount_grey;
					}
					else{
						$Amount[$this->_sweater][$this->_grey][$poId][$itemNumberId][$uom]=$amount_grey;
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
				elseif($fabNatureId==100){
					if(isset($Amount[$this->_sweater][$this->_finish][$poId][$colorId][$uom])){
						$Amount[$this->_sweater][$this->_finish][$poId][$colorId][$uom]+=$amount_finish;
					}
					else{
						$Amount[$this->_sweater][$this->_finish][$poId][$colorId][$uom]=$amount_finish;
					}
					if(isset($Amount[$this->_sweater][$this->_finish][$poId][$colorId][$uom])){
						$Amount[$this->_sweater][$this->_grey][$poId][$colorId][$uom]+=$amount_grey;
					}
					else{
						$Amount[$this->_sweater][$this->_grey][$poId][$colorId][$uom]=$amount_grey;
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
				elseif($fabNatureId==100){
					if(isset($Amount[$this->_sweater][$this->_finish][$poId][$sizeId][$uom])){
						$Amount[$this->_sweater][$this->_finish][$poId][$sizeId][$uom]+=$amount_finish;
					}
					else{
						$Amount[$this->_sweater][$this->_finish][$poId][$sizeId][$uom]=$amount_finish;
					}
					if(isset($Amount[$this->_sweater][$this->_grey][$poId][$sizeId][$uom])){
						$Amount[$this->_sweater][$this->_grey][$poId][$sizeId][$uom]+=$amount_grey;
					}
					else{
						$Amount[$this->_sweater][$this->_grey][$poId][$sizeId][$uom]=$amount_grey;
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
				elseif($fabNatureId==100){
					if(isset($Amount[$this->_sweater][$this->_finish][$poId][$countryId][$itemNumberId][$uom])){
						$Amount[$this->_sweater][$this->_finish][$poId][$countryId][$itemNumberId][$uom]+=$amount_finish;
					}
					else{
						$Amount[$this->_sweater][$this->_finish][$poId][$countryId][$itemNumberId][$uom]=$amount_finish;
					}
					if(isset($Amount[$this->_sweater][$this->_grey][$poId][$countryId][$itemNumberId][$uom])){
						$Amount[$this->_sweater][$this->_grey][$poId][$countryId][$itemNumberId][$uom]+=$amount_grey;
					}
					else{
						$Amount[$this->_sweater][$this->_grey][$poId][$countryId][$itemNumberId][$uom]=$amount_grey;
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
				elseif($fabNatureId==100){
					if(isset($Amount[$this->_sweater][$this->_finish][$poId][$countryId][$colorId][$uom])){
						$Amount[$this->_sweater][$this->_finish][$poId][$countryId][$colorId][$uom]+=$amount_finish;
					}
					else{
						$Amount[$this->_sweater][$this->_finish][$poId][$countryId][$colorId][$uom]=$amount_finish;
					}
					if(isset($Amount[$this->_sweater][$this->_grey][$poId][$countryId][$colorId][$uom])){
						$Amount[$this->_sweater][$this->_grey][$poId][$countryId][$colorId][$uom]+=$amount_grey;
					}
					else{
						$Amount[$this->_sweater][$this->_grey][$poId][$countryId][$colorId][$uom]=$amount_grey;
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
				elseif($fabNatureId==100){
					if(isset($Amount[$this->_sweater][$this->_finish][$poId][$countryId][$sizeId][$uom])){
						$Amount[$this->_sweater][$this->_finish][$poId][$countryId][$sizeId][$uom]+=$amount_finish;
					}
					else{
						$Amount[$this->_sweater][$this->_finish][$poId][$countryId][$sizeId][$uom]=$amount_finish;
					}
					if(isset($Amount[$this->_sweater][$this->_grey][$poId][$countryId][$sizeId][$uom])){
						$Amount[$this->_sweater][$this->_grey][$poId][$countryId][$sizeId][$uom]+=$amount_grey;
					}
					else{
						$Amount[$this->_sweater][$this->_grey][$poId][$countryId][$sizeId][$uom]=$amount_grey;
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
				elseif($fabNatureId==100){
					if(isset($Amount[$this->_sweater][$this->_finish][$poId][$itemNumberId][$colorId][$uom])){
						$Amount[$this->_sweater][$this->_finish][$poId][$itemNumberId][$colorId][$uom]+=$amount_finish;
					}
					else{
						$Amount[$this->_sweater][$this->_finish][$poId][$itemNumberId][$colorId][$uom]=$amount_finish;
					}
					if(isset($Amount[$this->_sweater][$this->_grey][$poId][$itemNumberId][$colorId][$uom])){
						$Amount[$this->_sweater][$this->_grey][$poId][$itemNumberId][$colorId][$uom]+=$amount_grey;
					}
					else{
						$Amount[$this->_sweater][$this->_grey][$poId][$itemNumberId][$colorId][$uom]=$amount_grey;
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
				elseif($fabNatureId==100){
					if(isset($Amount[$this->_sweater][$this->_finish][$poId][$itemNumberId][$sizeId][$uom])){
						$Amount[$this->_sweater][$this->_finish][$poId][$itemNumberId][$sizeId][$uom]+=$amount_finish;
					}
					else{
						$Amount[$this->_sweater][$this->_finish][$poId][$itemNumberId][$sizeId][$uom]=$amount_finish;
					}
					if(isset($Amount[$this->_sweater][$this->_grey][$poId][$itemNumberId][$sizeId][$uom])){
						$Amount[$this->_sweater][$this->_grey][$poId][$itemNumberId][$sizeId][$uom]+=$amount_grey;
					}
					else{
						$Amount[$this->_sweater][$this->_grey][$poId][$itemNumberId][$sizeId][$uom]=$amount_grey;
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
				elseif($fabNatureId==100){
					if(isset($Amount[$this->_sweater][$this->_finish][$poId][$colorId][$sizeId][$uom])){
						$Amount[$this->_sweater][$this->_finish][$poId][$colorId][$sizeId][$uom]+=$amount_finish;
					}
					else{
						$Amount[$this->_sweater][$this->_finish][$poId][$colorId][$sizeId][$uom]=$amount_finish;
					}
					if(isset($Amount[$this->_sweater][$this->_grey][$poId][$colorId][$sizeId][$uom]))
					{
						$Amount[$this->_sweater][$this->_grey][$poId][$colorId][$sizeId][$uom]+=$amount_grey;
					}
					else{
						$Amount[$this->_sweater][$this->_grey][$poId][$colorId][$sizeId][$uom]=$amount_grey;
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
				elseif($fabNatureId==100){
					if(isset($Amount[$this->_sweater][$this->_finish][$poId][$colorId][$bodypartId][$uom])){
						$Amount[$this->_sweater][$this->_finish][$poId][$colorId][$bodypartId][$uom]+=$amount_finish;
					}
					else{
						$Amount[$this->_sweater][$this->_finish][$poId][$colorId][$bodypartId][$uom]=$amount_finish;
					}
					if(isset($Amount[$this->_sweater][$this->_grey][$poId][$colorId][$bodypartId][$uom]))
					{
						$Amount[$this->_sweater][$this->_grey][$poId][$colorId][$bodypartId][$uom]+=$amount_grey;
					}
					else{
						$Amount[$this->_sweater][$this->_grey][$poId][$colorId][$bodypartId][$uom]=$amount_grey;
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
				elseif($fabNatureId==100){
					if(isset($Amount[$this->_sweater][$this->_finish][$poId][$countryId][$itemNumberId][$colorId][$uom])){
						$Amount[$this->_sweater][$this->_finish][$poId][$countryId][$itemNumberId][$colorId][$uom]+=$amount_finish;
					}
					else{
						$Amount[$this->_sweater][$this->_finish][$poId][$countryId][$itemNumberId][$colorId][$uom]=$amount_finish;
					}
					if(isset($Amount[$this->_sweater][$this->_grey][$poId][$countryId][$itemNumberId][$colorId][$uom])){
						$Amount[$this->_sweater][$this->_grey][$poId][$countryId][$itemNumberId][$colorId][$uom]+=$amount_grey;
					}
					else{
						$Amount[$this->_sweater][$this->_grey][$poId][$countryId][$itemNumberId][$colorId][$uom]=$amount_grey;
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
				elseif($fabNatureId==100){
					if(isset($Amount[$this->_sweater][$this->_finish][$poId][$countryId][$itemNumberId][$sizeId][$uom])){
						$Amount[$this->_sweater][$this->_finish][$poId][$countryId][$itemNumberId][$sizeId][$uom]+=$amount_finish;
					}
					else{
						$Amount[$this->_sweater][$this->_finish][$poId][$countryId][$itemNumberId][$sizeId][$uom]=$amount_finish;
					}
					if(isset($Amount[$this->_sweater][$this->_grey][$poId][$countryId][$itemNumberId][$sizeId][$uom])){
						$Amount[$this->_sweater][$this->_grey][$poId][$countryId][$itemNumberId][$sizeId][$uom]+=$amount_grey;
					}
					else{
						$Amount[$this->_sweater][$this->_grey][$poId][$countryId][$itemNumberId][$sizeId][$uom]=$amount_grey;
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
				elseif($fabNatureId==100){
					if(isset($Amount[$this->_sweater][$this->_finish][$poId][$countryId][$colorId][$sizeId][$uom])){
						$Amount[$this->_sweater][$this->_finish][$poId][$countryId][$colorId][$sizeId][$uom]+=$amount_finish;
					}
					else{
						$Amount[$this->_sweater][$this->_finish][$poId][$countryId][$colorId][$sizeId][$uom]=$amount_finish;
					}
					if(isset($Amount[$this->_sweater][$this->_grey][$poId][$countryId][$colorId][$sizeId][$uom])){
						$Amount[$this->_sweater][$this->_grey][$poId][$countryId][$colorId][$sizeId][$uom]+=$amount_grey;
					}
					else{
						$Amount[$this->_sweater][$this->_grey][$poId][$countryId][$colorId][$sizeId][$uom]=$amount_grey;
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
				elseif($fabNatureId==100){
					if(isset($Amount[$this->_sweater][$this->_finish][$poId][$itemNumberId][$colorId][$sizeId][$uom])){
						$Amount[$this->_sweater][$this->_finish][$poId][$itemNumberId][$colorId][$sizeId][$uom]+=$amount_finish;
					}
					else{
						$Amount[$this->_sweater][$this->_finish][$poId][$itemNumberId][$colorId][$sizeId][$uom]=$amount_finish;
					}
					if(isset($Amount[$this->_sweater][$this->_grey][$poId][$itemNumberId][$colorId][$sizeId][$uom])){
						$Amount[$this->_sweater][$this->_grey][$poId][$itemNumberId][$colorId][$sizeId][$uom]+=$amount_grey;
					}
					else{
						$Amount[$this->_sweater][$this->_grey][$poId][$itemNumberId][$colorId][$sizeId][$uom]=$amount_grey;
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
				elseif($fabNatureId==100){
					if(isset($Amount[$this->_sweater][$this->_finish][$poId][$countryId][$itemNumberId][$colorId][$sizeId][$uom])){
						$Amount[$this->_sweater][$this->_finish][$poId][$countryId][$itemNumberId][$colorId][$sizeId][$uom]+=$amount_finish;
					}
					else{
						$Amount[$this->_sweater][$this->_finish][$poId][$countryId][$itemNumberId][$colorId][$sizeId][$uom]=$amount_finish;
					}
					if(isset($Amount[$this->_sweater][$this->_grey][$poId][$countryId][$itemNumberId][$colorId][$sizeId][$uom])){
						$Amount[$this->_sweater][$this->_grey][$poId][$countryId][$itemNumberId][$colorId][$sizeId][$uom]+=$amount_grey;
					}
					else{
						$Amount[$this->_sweater][$this->_grey][$poId][$countryId][$itemNumberId][$colorId][$sizeId][$uom]=$amount_grey;
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
				elseif($fabNatureId==100){
					if(isset($Amount[$this->_sweater][$this->_finish][$poId][$preCostDtlsId][$uom])){
						$Amount[$this->_sweater][$this->_finish][$poId][$preCostDtlsId][$uom]+=$amount_finish;
					}
					else{
						$Amount[$this->_sweater][$this->_finish][$poId][$preCostDtlsId][$uom]=$amount_finish;
					}
					if(isset($Amount[$this->_sweater][$this->_grey][$poId][$preCostDtlsId][$uom])){
						$Amount[$this->_sweater][$this->_grey][$poId][$preCostDtlsId][$uom]+=$amount_grey;
					}
					else{
						$Amount[$this->_sweater][$this->_grey][$poId][$preCostDtlsId][$uom]=$amount_grey;
					}
				}
			}
			elseif($level==$this->_By_OrderAndFabriccostidSupplier){
				if($fabNatureId==2){
					if(isset($Amount[$this->_knit][$this->_finish][$poId][$preCostDtlsId][$nominated_supp][$uom])){
						$Amount[$this->_knit][$this->_finish][$poId][$preCostDtlsId][$nominated_supp][$uom]+=$amount_finish;
					}
					else{
						$Amount[$this->_knit][$this->_finish][$poId][$preCostDtlsId][$nominated_supp][$uom]=$amount_finish;
					}
					if(isset($Amount[$this->_knit][$this->_grey][$poId][$preCostDtlsId][$nominated_supp][$uom])){
						$Amount[$this->_knit][$this->_grey][$poId][$preCostDtlsId][$nominated_supp][$uom]+=$amount_grey;
					}
					else{
						$Amount[$this->_knit][$this->_grey][$poId][$preCostDtlsId][$nominated_supp][$uom]=$amount_grey;
					}
				}
				elseif($fabNatureId==3){
					if(isset($Amount[$this->_woven][$this->_finish][$poId][$preCostDtlsId][$nominated_supp][$uom])){
						$Amount[$this->_woven][$this->_finish][$poId][$preCostDtlsId][$nominated_supp][$uom]+=$amount_finish;
					}
					else{
						$Amount[$this->_woven][$this->_finish][$poId][$preCostDtlsId][$nominated_supp][$uom]=$amount_finish;
					}
					if(isset($Amount[$this->_woven][$this->_grey][$poId][$preCostDtlsId][$nominated_supp][$uom])){
						$Amount[$this->_woven][$this->_grey][$poId][$preCostDtlsId][$nominated_supp][$uom]+=$amount_grey;
					}
					else{
						$Amount[$this->_woven][$this->_grey][$poId][$preCostDtlsId][$nominated_supp][$uom]=$amount_grey;
					}
				}
				elseif($fabNatureId==100){
					if(isset($Amount[$this->_sweater][$this->_finish][$poId][$preCostDtlsId][$nominated_supp][$uom])){
						$Amount[$this->_sweater][$this->_finish][$poId][$preCostDtlsId][$nominated_supp][$uom]+=$amount_finish;
					}
					else{
						$Amount[$this->_sweater][$this->_finish][$poId][$preCostDtlsId][$nominated_supp][$uom]=$amount_finish;
					}
					if(isset($Amount[$this->_sweater][$this->_grey][$poId][$preCostDtlsId][$nominated_supp][$uom])){
						$Amount[$this->_sweater][$this->_grey][$poId][$preCostDtlsId][$nominated_supp][$uom]+=$amount_grey;
					}
					else{
						$Amount[$this->_sweater][$this->_grey][$poId][$preCostDtlsId][$nominated_supp][$uom]=$amount_grey;
					}
				}
			}
			elseif($level==$this->_By_OrderAndConfirmedid){
				if($fabNatureId==2){
					if(isset($Amount[$this->_knit][$this->_finish][$poId][$isConfirmed][$uom])){
						$Amount[$this->_knit][$this->_finish][$poId][$isConfirmed][$uom]+=$amount_finish;
					}
					else{
						$Amount[$this->_knit][$this->_finish][$poId][$isConfirmed][$uom]=$amount_finish;
					}
					if(isset($Amount[$this->_knit][$this->_grey][$poId][$isConfirmed][$uom])){
						$Amount[$this->_knit][$this->_grey][$poId][$isConfirmed][$uom]+=$amount_grey;
					}
					else{
						$Amount[$this->_knit][$this->_grey][$poId][$isConfirmed][$uom]=$amount_grey;
					}
				}
				elseif($fabNatureId==3){
					if(isset($Amount[$this->_woven][$this->_finish][$poId][$isConfirmed][$uom])){
						$Amount[$this->_woven][$this->_finish][$poId][$isConfirmed][$uom]+=$amount_finish;
					}
					else{
						$Amount[$this->_woven][$this->_finish][$poId][$isConfirmed][$uom]=$amount_finish;
					}
					if(isset($Amount[$this->_woven][$this->_grey][$poId][$isConfirmed][$uom])){
						$Amount[$this->_woven][$this->_grey][$poId][$isConfirmed][$uom]+=$amount_grey;
					}
					else{
						$Amount[$this->_woven][$this->_grey][$poId][$isConfirmed][$uom]=$amount_grey;
					}
				}
				elseif($fabNatureId==100){
					if(isset($Amount[$this->_sweater][$this->_finish][$poId][$isConfirmed][$uom])){
						$Amount[$this->_sweater][$this->_finish][$poId][$isConfirmed][$uom]+=$amount_finish;
					}
					else{
						$Amount[$this->_sweater][$this->_finish][$poId][$isConfirmed][$uom]=$amount_finish;
					}
					if(isset($Amount[$this->_sweater][$this->_grey][$poId][$isConfirmed][$uom])){
						$Amount[$this->_sweater][$this->_grey][$poId][$isConfirmed][$uom]+=$amount_grey;
					}
					else{
						$Amount[$this->_sweater][$this->_grey][$poId][$isConfirmed][$uom]=$amount_grey;
					}
				}
			}
			elseif($level==$this->_By_OrderConfirmedidAndCountryid){
				if($fabNatureId==2){
					if(isset($Amount[$this->_knit][$this->_finish][$poId][$isConfirmed][$countryId][$uom])){
						$Amount[$this->_knit][$this->_finish][$poId][$isConfirmed][$countryId][$uom]+=$amount_finish;
					}
					else{
						$Amount[$this->_knit][$this->_finish][$poId][$isConfirmed][$countryId][$uom]=$amount_finish;
					}
					if(isset($Amount[$this->_knit][$this->_grey][$poId][$isConfirmed][$countryId][$uom])){
						$Amount[$this->_knit][$this->_grey][$poId][$isConfirmed][$countryId][$uom]+=$amount_grey;
					}
					else{
						$Amount[$this->_knit][$this->_grey][$poId][$isConfirmed][$countryId][$uom]=$amount_grey;
					}
				}
				elseif($fabNatureId==3){
					if(isset($Amount[$this->_woven][$this->_finish][$poId][$isConfirmed][$countryId][$uom])){
						$Amount[$this->_woven][$this->_finish][$poId][$isConfirmed][$countryId][$uom]+=$amount_finish;
					}
					else{
						$Amount[$this->_woven][$this->_finish][$poId][$isConfirmed][$countryId][$uom]=$amount_finish;
					}
					if(isset($Amount[$this->_woven][$this->_grey][$poId][$isConfirmed][$countryId][$uom])){
						$Amount[$this->_woven][$this->_grey][$poId][$isConfirmed][$countryId][$uom]+=$amount_grey;
					}
					else{
						$Amount[$this->_woven][$this->_grey][$poId][$isConfirmed][$countryId][$uom]=$amount_grey;
					}
				}
				elseif($fabNatureId==100){
					if(isset($Amount[$this->_sweater][$this->_finish][$poId][$isConfirmed][$countryId][$uom])){
						$Amount[$this->_sweater][$this->_finish][$poId][$isConfirmed][$countryId][$uom]+=$amount_finish;
					}
					else{
						$Amount[$this->_sweater][$this->_finish][$poId][$isConfirmed][$countryId][$uom]=$amount_finish;
					}
					if(isset($Amount[$this->_sweater][$this->_grey][$poId][$isConfirmed][$countryId][$uom])){
						$Amount[$this->_sweater][$this->_grey][$poId][$isConfirmed][$countryId][$uom]+=$amount_grey;
					}
					else{
						$Amount[$this->_sweater][$this->_grey][$poId][$isConfirmed][$countryId][$uom]=$amount_grey;
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
				elseif($fabNatureId==100){
					if(isset($Amount[$this->_sweater][$this->_finish][$poId][$preCostDtlsId][$colorId][$uom])){
						$Amount[$this->_sweater][$this->_finish][$poId][$preCostDtlsId][$colorId][$uom]+=$amount_finish;
					}
					else{
						$Amount[$this->_sweater][$this->_finish][$poId][$preCostDtlsId][$colorId][$uom]=$amount_finish;
					}
					if(isset($Amount[$this->_sweater][$this->_grey][$poId][$preCostDtlsId][$colorId][$uom])){
						$Amount[$this->_sweater][$this->_grey][$poId][$preCostDtlsId][$colorId][$uom]+=$amount_grey;
					}
					else{
						$Amount[$this->_sweater][$this->_grey][$poId][$preCostDtlsId][$colorId][$uom]=$amount_grey;
					}
				}
			}
			elseif($level==$this->_By_OrderColorTypeAndGmtscolor){
				if($fabNatureId==2){
					if(isset($Amount[$this->_knit][$this->_finish][$poId][$colorTypeId][$colorId][$uom])){
						$Amount[$this->_knit][$this->_finish][$poId][$colorTypeId][$colorId][$uom]+=$amount_finish;
					}
					else{
						$Amount[$this->_knit][$this->_finish][$poId][$colorTypeId][$colorId][$uom]=$amount_finish;
					}
					if(isset($Amount[$this->_knit][$this->_grey][$poId][$colorTypeId][$colorId][$uom])){
						$Amount[$this->_knit][$this->_grey][$poId][$colorTypeId][$colorId][$uom]+=$amount_grey;
					}
					else{
						$Amount[$this->_knit][$this->_grey][$poId][$colorTypeId][$colorId][$uom]=$amount_grey;
					}
				}
				elseif($fabNatureId==3){
					if(isset($Amount[$this->_woven][$this->_finish][$poId][$colorTypeId][$colorId][$uom])){
						$Amount[$this->_woven][$this->_finish][$poId][$colorTypeId][$colorId][$uom]+=$amount_finish;
					}
					else{
						$Amount[$this->_woven][$this->_finish][$poId][$colorTypeId][$colorId][$uom]=$amount_finish;
					}
					if(isset($Amount[$this->_woven][$this->_grey][$poId][$colorTypeId][$colorId][$uom])){
						$Amount[$this->_woven][$this->_grey][$poId][$colorTypeId][$colorId][$uom]+=$amount_grey;
					}
					else{
						$Amount[$this->_woven][$this->_grey][$poId][$colorTypeId][$colorId][$uom]=$amount_grey;
					}
				}
				elseif($fabNatureId==100){
					if(isset($Amount[$this->_sweater][$this->_finish][$poId][$colorTypeId][$colorId][$uom])){
						$Amount[$this->_sweater][$this->_finish][$poId][$colorTypeId][$colorId][$uom]+=$amount_finish;
					}
					else{
						$Amount[$this->_sweater][$this->_finish][$poId][$colorTypeId][$colorId][$uom]=$amount_finish;
					}
					if(isset($Amount[$this->_sweater][$this->_grey][$poId][$colorTypeId][$colorId][$uom])){
						$Amount[$this->_sweater][$this->_grey][$poId][$colorTypeId][$colorId][$uom]+=$amount_grey;
					}
					else{
						$Amount[$this->_sweater][$this->_grey][$poId][$colorTypeId][$colorId][$uom]=$amount_grey;
					}
				}
			}
			elseif($level==$this->_By_OrderColorType){
				if($fabNatureId==2){
					if(isset($Amount[$this->_knit][$this->_finish][$poId][$colorTypeId][$uom])){
						$Amount[$this->_knit][$this->_finish][$poId][$colorTypeId][$uom]+=$amount_finish;
					}
					else{
						$Amount[$this->_knit][$this->_finish][$poId][$colorTypeId][$uom]=$amount_finish;
					}
					if(isset($Amount[$this->_knit][$this->_grey][$poId][$colorTypeId][$uom])){
						$Amount[$this->_knit][$this->_grey][$poId][$colorTypeId][$uom]+=$amount_grey;
					}
					else{
						$Amount[$this->_knit][$this->_grey][$poId][$colorTypeId][$uom]=$amount_grey;
					}
				}
				elseif($fabNatureId==3){
					if(isset($Amount[$this->_woven][$this->_finish][$poId][$colorTypeId][$uom])){
						$Amount[$this->_woven][$this->_finish][$poId][$colorTypeId][$uom]+=$amount_finish;
					}
					else{
						$Amount[$this->_woven][$this->_finish][$poId][$colorTypeId][$uom]=$amount_finish;
					}
					if(isset($Amount[$this->_woven][$this->_grey][$poId][$colorTypeId][$uom])){
						$Amount[$this->_woven][$this->_grey][$poId][$colorTypeId][$uom]+=$amount_grey;
					}
					else{
						$Amount[$this->_woven][$this->_grey][$poId][$colorTypeId][$uom]=$amount_grey;
					}
				}
				elseif($fabNatureId==100){
					if(isset($Amount[$this->_sweater][$this->_finish][$poId][$colorTypeId][$uom])){
						$Amount[$this->_sweater][$this->_finish][$poId][$colorTypeId][$uom]+=$amount_finish;
					}
					else{
						$Amount[$this->_sweater][$this->_finish][$poId][$colorTypeId][$uom]=$amount_finish;
					}
					if(isset($Amount[$this->_sweater][$this->_grey][$poId][$colorTypeId][$uom])){
						$Amount[$this->_sweater][$this->_grey][$poId][$colorTypeId][$uom]+=$amount_grey;
					}
					else{
						$Amount[$this->_sweater][$this->_grey][$poId][$colorTypeId][$uom]=$amount_grey;
					}
				}
			}
			elseif($level==$this->_By_JobAndGmtscolor){
				if($fabNatureId==2){
					if(isset($Amount[$this->_knit][$this->_finish][$jobNo][$colorId][$fabricSourceId][$uom])){
						$Amount[$this->_knit][$this->_finish][$jobNo][$colorId][$fabricSourceId][$uom]+=$amount_finish;
					}
					else{
						$Amount[$this->_knit][$this->_finish][$jobNo][$colorId][$fabricSourceId][$uom]=$amount_finish;
					}
					if(isset($Amount[$this->_knit][$this->_grey][$jobNo][$colorId][$fabricSourceId][$uom])){
						$Amount[$this->_knit][$this->_grey][$jobNo][$colorId][$fabricSourceId][$uom]+=$amount_grey;
					}
					else{
						$Amount[$this->_knit][$this->_grey][$jobNo][$colorId][$fabricSourceId][$uom]=$amount_grey;
					}
				}
				elseif($fabNatureId==3){
					if(isset($Amount[$this->_woven][$this->_finish][$jobNo][$colorId][$fabricSourceId][$uom])){
						$Amount[$this->_woven][$this->_finish][$jobNo][$colorId][$fabricSourceId][$uom]+=$amount_finish;
					}
					else{
						$Amount[$this->_woven][$this->_finish][$jobNo][$colorId][$fabricSourceId][$uom]=$amount_finish;
					}
					if(isset($Amount[$this->_woven][$this->_grey][$jobNo][$colorId][$fabricSourceId][$uom])){
						$Amount[$this->_woven][$this->_grey][$jobNo][$colorId][$fabricSourceId][$uom]+=$amount_grey;
					}
					else{
						$Amount[$this->_woven][$this->_grey][$jobNo][$colorId][$fabricSourceId][$uom]=$amount_grey;
					}
				}
				elseif($fabNatureId==100){
					if(isset($Amount[$this->_sweater][$this->_finish][$jobNo][$colorId][$fabricSourceId][$uom])){
						$Amount[$this->_sweater][$this->_finish][$jobNo][$colorId][$fabricSourceId][$uom]+=$amount_finish;
					}
					else{
						$Amount[$this->_sweater][$this->_finish][$jobNo][$colorId][$fabricSourceId][$uom]=$amount_finish;
					}
					if(isset($Amount[$this->_sweater][$this->_grey][$jobNo][$colorId][$fabricSourceId][$uom])){
						$Amount[$this->_sweater][$this->_grey][$jobNo][$colorId][$fabricSourceId][$uom]+=$amount_grey;
					}
					else{
						$Amount[$this->_sweater][$this->_grey][$jobNo][$colorId][$fabricSourceId][$uom]=$amount_grey;
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
				elseif($fabNatureId==100){
					if(isset($Amount[$this->_sweater][$this->_finish][$poId][$preCostDtlsId][$colorId][$diaWidth][$uom])){
						$Amount[$this->_sweater][$this->_finish][$poId][$preCostDtlsId][$colorId][$diaWidth][$uom]+=$amount_finish;
					}
					else{
						$Amount[$this->_sweater][$this->_finish][$poId][$preCostDtlsId][$colorId][$diaWidth][$uom]=$amount_finish;
					}
					if(isset($Amount[$this->_sweater][$this->_grey][$poId][$preCostDtlsId][$colorId][$diaWidth][$uom]))
					{
						$Amount[$this->_sweater][$this->_grey][$poId][$preCostDtlsId][$colorId][$diaWidth][$uom]+=$amount_grey;
					}
					else{
						$Amount[$this->_sweater][$this->_grey][$poId][$preCostDtlsId][$colorId][$diaWidth][$uom]=$amount_grey;
					}
				}
			}
			
			elseif($level==$this->_By_OrderFabriccostidGmtscolorGmtsSizeAndItemSize){
				if($fabNatureId==2){
					if(isset($Amount[$this->_knit][$this->_finish][$poId][$preCostDtlsId][$colorId][$sizeId][$item_size][$uom])){
						$Amount[$this->_knit][$this->_finish][$poId][$preCostDtlsId][$colorId][$sizeId][$item_size][$uom]+=$amount_finish;
					}
					else{
						$Amount[$this->_knit][$this->_finish][$poId][$preCostDtlsId][$colorId][$sizeId][$item_size][$uom]=$amount_finish;
					}
					if(isset($Amount[$this->_knit][$this->_grey][$poId][$preCostDtlsId][$colorId][$sizeId][$item_size][$uom])){
						$Amount[$this->_knit][$this->_grey][$poId][$preCostDtlsId][$colorId][$sizeId][$item_size][$uom]+=$amount_grey;
					}
					else{
						$Amount[$this->_knit][$this->_grey][$poId][$preCostDtlsId][$colorId][$sizeId][$item_size][$uom]=$amount_grey;
					}
				}
				elseif($fabNatureId==3){
					if(isset($Amount[$this->_woven][$this->_finish][$poId][$preCostDtlsId][$colorId][$sizeId][$item_size][$uom])){
						$Amount[$this->_woven][$this->_finish][$poId][$preCostDtlsId][$colorId][$sizeId][$item_size][$uom]+=$amount_finish;
					}
					else{
						$Amount[$this->_woven][$this->_finish][$poId][$preCostDtlsId][$colorId][$sizeId][$item_size][$uom]=$amount_finish;
					}
					if(isset($Amount[$this->_woven][$this->_grey][$poId][$preCostDtlsId][$colorId][$sizeId][$item_size][$uom]))
					{
						$Amount[$this->_woven][$this->_grey][$poId][$preCostDtlsId][$colorId][$sizeId][$item_size][$uom]+=$amount_grey;
					}
					else{
						$Amount[$this->_woven][$this->_grey][$poId][$preCostDtlsId][$colorId][$sizeId][$item_size][$uom]=$amount_grey;
					}
				}
				elseif($fabNatureId==100){
					if(isset($Amount[$this->_sweater][$this->_finish][$poId][$preCostDtlsId][$colorId][$sizeId][$item_size][$uom])){
						$Amount[$this->_sweater][$this->_finish][$poId][$preCostDtlsId][$colorId][$sizeId][$item_size][$uom]+=$amount_finish;
					}
					else{
						$Amount[$this->_sweater][$this->_finish][$poId][$preCostDtlsId][$colorId][$sizeId][$item_size][$uom]=$amount_finish;
					}
					if(isset($Amount[$this->_sweater][$this->_grey][$poId][$preCostDtlsId][$colorId][$sizeId][$item_size][$uom]))
					{
						$Amount[$this->_sweater][$this->_grey][$poId][$preCostDtlsId][$colorId][$sizeId][$item_size][$uom]+=$amount_grey;
					}
					else{
						$Amount[$this->_sweater][$this->_grey][$poId][$preCostDtlsId][$colorId][$sizeId][$item_size][$uom]=$amount_grey;
					}
				}
			}		
				
			elseif($level==$this->_By_OrderFabriccostidFabriccolorAndDiaWidth){
				if($fabNatureId==2){
					if(isset($Amount[$this->_knit][$this->_finish][$poId][$preCostDtlsId][$colorId][$fabricColorId][$diaWidth][$uom])){
						$Amount[$this->_knit][$this->_finish][$poId][$preCostDtlsId][$colorId][$fabricColorId][$diaWidth][$uom]+=$amount_finish;
					}
					else{
						$Amount[$this->_knit][$this->_finish][$poId][$preCostDtlsId][$colorId][$fabricColorId][$diaWidth][$uom]=$amount_finish;
					}
					if(isset($Amount[$this->_knit][$this->_grey][$poId][$preCostDtlsId][$colorId][$fabricColorId][$diaWidth][$uom])){
						$Amount[$this->_knit][$this->_grey][$poId][$preCostDtlsId][$colorId][$fabricColorId][$diaWidth][$uom]+=$amount_grey;
					}
					else{
						$Amount[$this->_knit][$this->_grey][$poId][$preCostDtlsId][$colorId][$fabricColorId][$diaWidth][$uom]=$amount_grey;
					}
				}
				elseif($fabNatureId==3){
					if(isset($Amount[$this->_woven][$this->_finish][$poId][$preCostDtlsId][$colorId][$fabricColorId][$diaWidth][$uom])){
						$Amount[$this->_woven][$this->_finish][$poId][$preCostDtlsId][$colorId][$fabricColorId][$diaWidth][$uom]+=$amount_finish;
					}
					else{
						$Amount[$this->_woven][$this->_finish][$poId][$preCostDtlsId][$colorId][$fabricColorId][$diaWidth][$uom]=$amount_finish;
					}
					if(isset($Amount[$this->_woven][$this->_grey][$poId][$preCostDtlsId][$colorId][$fabricColorId][$diaWidth][$uom]))
					{
						$Amount[$this->_woven][$this->_grey][$poId][$preCostDtlsId][$colorId][$fabricColorId][$diaWidth][$uom]+=$amount_grey;
					}
					else{
						$Amount[$this->_woven][$this->_grey][$poId][$preCostDtlsId][$colorId][$fabricColorId][$diaWidth][$uom]=$amount_grey;
					}
				}
				elseif($fabNatureId==100){
					if(isset($Amount[$this->_sweater][$this->_finish][$poId][$preCostDtlsId][$colorId][$fabricColorId][$diaWidth][$uom])){
						$Amount[$this->_sweater][$this->_finish][$poId][$preCostDtlsId][$colorId][$fabricColorId][$diaWidth][$uom]+=$amount_finish;
					}
					else{
						$Amount[$this->_sweater][$this->_finish][$poId][$preCostDtlsId][$colorId][$fabricColorId][$diaWidth][$uom]=$amount_finish;
					}
					if(isset($Amount[$this->_sweater][$this->_grey][$poId][$preCostDtlsId][$colorId][$fabricColorId][$diaWidth][$uom]))
					{
						$Amount[$this->_sweater][$this->_grey][$poId][$preCostDtlsId][$colorId][$fabricColorId][$diaWidth][$uom]+=$amount_grey;
					}
					else{
						$Amount[$this->_sweater][$this->_grey][$poId][$preCostDtlsId][$colorId][$fabricColorId][$diaWidth][$uom]=$amount_grey;
					}
				}
			}
			elseif($level==$this->_By_OrderFabriccolorAndDeterminId){
				if($fabNatureId==2){
					if(isset($Amount[$this->_knit][$this->_finish][$poId][$FabricColorId][$libYarnCountDeterId][$uom])){
						$Amount[$this->_knit][$this->_finish][$poId][$FabricColorId][$libYarnCountDeterId][$uom]+=$amount_finish;
					}
					else{
						$Amount[$this->_knit][$this->_finish][$poId][$FabricColorId][$libYarnCountDeterId][$uom]=$amount_finish;
					}
					if(isset($Amount[$this->_knit][$this->_grey][$poId][$FabricColorId][$libYarnCountDeterId][$uom])){
						$Amount[$this->_knit][$this->_grey][$poId][$FabricColorId][$libYarnCountDeterId][$uom]+=$amount_grey;
					}
					else{
						$Amount[$this->_knit][$this->_grey][$poId][$FabricColorId][$libYarnCountDeterId][$uom]=$amount_grey;
					}
				}
				elseif($fabNatureId==3){
					if(isset($Amount[$this->_woven][$this->_finish][$poId][$FabricColorId][$libYarnCountDeterId][$uom])){
						$Amount[$this->_woven][$this->_finish][$poId][$FabricColorId][$libYarnCountDeterId][$uom]+=$amount_finish;
					}
					else{
						$Amount[$this->_woven][$this->_finish][$poId][$FabricColorId][$libYarnCountDeterId][$uom]=$amount_finish;
					}
					if(isset($Amount[$this->_woven][$this->_grey][$poId][$FabricColorId][$libYarnCountDeterId][$uom]))
					{
						$Amount[$this->_woven][$this->_grey][$poId][$FabricColorId][$libYarnCountDeterId][$uom]+=$amount_grey;
					}
					else{
						$Amount[$this->_woven][$this->_grey][$poId][$FabricColorId][$libYarnCountDeterId][$uom]=$amount_grey;
					}
				}
				elseif($fabNatureId==100){
					if(isset($Amount[$this->_sweater][$this->_finish][$poId][$FabricColorId][$libYarnCountDeterId][$uom])){
						$Amount[$this->_sweater][$this->_finish][$poId][$FabricColorId][$libYarnCountDeterId][$uom]+=$amount_finish;
					}
					else{
						$Amount[$this->_sweater][$this->_finish][$poId][$FabricColorId][$libYarnCountDeterId][$uom]=$amount_finish;
					}
					if(isset($Amount[$this->_sweater][$this->_grey][$poId][$FabricColorId][$libYarnCountDeterId][$uom]))
					{
						$Amount[$this->_sweater][$this->_grey][$poId][$FabricColorId][$libYarnCountDeterId][$uom]+=$amount_grey;
					}
					else{
						$Amount[$this->_sweater][$this->_grey][$poId][$FabricColorId][$libYarnCountDeterId][$uom]=$amount_grey;
					}
				}
			}
			elseif($level==$this->_By_OrderFabricSourceAndBodyType){
				if($fabNatureId==2){
					if(isset($Amount[$this->_knit][$this->_finish][$poId][$fabricSourceId][$bodyparttypeId][$uom])){
						$Amount[$this->_knit][$this->_finish][$poId][$fabricSourceId][$bodyparttypeId][$uom]+=$amount_finish;
					}
					else{
						$Amount[$this->_knit][$this->_finish][$poId][$fabricSourceId][$bodyparttypeId][$uom]=$amount_finish;
					}
					if(isset($Amount[$this->_knit][$this->_grey][$poId][$fabricSourceId][$bodyparttypeId][$uom])){
						$Amount[$this->_knit][$this->_grey][$poId][$fabricSourceId][$bodyparttypeId][$uom]+=$amount_grey;
					}
					else{
						$Amount[$this->_knit][$this->_grey][$poId][$fabricSourceId][$bodyparttypeId][$uom]=$amount_grey;
					}
				}
				elseif($fabNatureId==3){
					if(isset($Amount[$this->_woven][$this->_finish][$poId][$fabricSourceId][$bodyparttypeId][$uom])){
						$Amount[$this->_woven][$this->_finish][$poId][$fabricSourceId][$bodyparttypeId][$uom]+=$amount_finish;
					}
					else{
						$Amount[$this->_woven][$this->_finish][$poId][$fabricSourceId][$bodyparttypeId][$uom]=$amount_finish;
					}
					if(isset($Amount[$this->_woven][$this->_grey][$poId][$fabricSourceId][$bodyparttypeId][$uom]))
					{
						$Amount[$this->_woven][$this->_grey][$poId][$fabricSourceId][$bodyparttypeId][$uom]+=$amount_grey;
					}
					else{
						$Amount[$this->_woven][$this->_grey][$poId][$fabricSourceId][$bodyparttypeId][$uom]=$amount_grey;
					}
				}
				elseif($fabNatureId==100){
					if(isset($Amount[$this->_sweater][$this->_finish][$poId][$fabricSourceId][$bodyparttypeId][$uom])){
						$Amount[$this->_sweater][$this->_finish][$poId][$fabricSourceId][$bodyparttypeId][$uom]+=$amount_finish;
					}
					else{
						$Amount[$this->_sweater][$this->_finish][$poId][$fabricSourceId][$bodyparttypeId][$uom]=$amount_finish;
					}
					if(isset($Amount[$this->_sweater][$this->_grey][$poId][$fabricSourceId][$bodyparttypeId][$uom]))
					{
						$Amount[$this->_sweater][$this->_grey][$poId][$fabricSourceId][$bodyparttypeId][$uom]+=$amount_grey;
					}
					else{
						$Amount[$this->_sweater][$this->_grey][$poId][$fabricSourceId][$bodyparttypeId][$uom]=$amount_grey;
					}
				}
			}
			elseif($level==$this->_By_OrderFabricSourceAndBodyTypeFabColor){
				if($fabNatureId==2){
					if(isset($Amount[$this->_knit][$this->_finish][$poId][$fabricSourceId][$bodyparttypeId][$FabGmtColorId][$uom])){
						$Amount[$this->_knit][$this->_finish][$poId][$fabricSourceId][$bodyparttypeId][$FabGmtColorId][$uom]+=$amount_finish;
					}
					else{
						$Amount[$this->_knit][$this->_finish][$poId][$fabricSourceId][$bodyparttypeId][$FabGmtColorId][$uom]=$amount_finish;
					}
					if(isset($Amount[$this->_knit][$this->_grey][$poId][$fabricSourceId][$bodyparttypeId][$FabGmtColorId][$uom])){
						$Amount[$this->_knit][$this->_grey][$poId][$fabricSourceId][$bodyparttypeId][$FabGmtColorId][$uom]+=$amount_grey;
					}
					else{
						$Amount[$this->_knit][$this->_grey][$poId][$fabricSourceId][$bodyparttypeId][$FabGmtColorId][$uom]=$amount_grey;
					}
				}
				elseif($fabNatureId==3){
					if(isset($Amount[$this->_woven][$this->_finish][$poId][$fabricSourceId][$bodyparttypeId][$FabGmtColorId][$uom])){
						$Amount[$this->_woven][$this->_finish][$poId][$fabricSourceId][$bodyparttypeId][$FabGmtColorId][$uom]+=$amount_finish;
					}
					else{
						$Amount[$this->_woven][$this->_finish][$poId][$fabricSourceId][$bodyparttypeId][$FabGmtColorId][$uom]=$amount_finish;
					}
					if(isset($Amount[$this->_woven][$this->_grey][$poId][$fabricSourceId][$bodyparttypeId][$FabGmtColorId][$uom]))
					{
						$Amount[$this->_woven][$this->_grey][$poId][$fabricSourceId][$bodyparttypeId][$FabGmtColorId][$uom]+=$amount_grey;
					}
					else{
						$Amount[$this->_woven][$this->_grey][$poId][$fabricSourceId][$bodyparttypeId][$FabGmtColorId][$uom]=$amount_grey;
					}
				}
				elseif($fabNatureId==100){
					if(isset($Amount[$this->_sweater][$this->_finish][$poId][$fabricSourceId][$bodyparttypeId][$FabGmtColorId][$uom])){
						$Amount[$this->_sweater][$this->_finish][$poId][$fabricSourceId][$bodyparttypeId][$FabGmtColorId][$uom]+=$amount_finish;
					}
					else{
						$Amount[$this->_sweater][$this->_finish][$poId][$fabricSourceId][$bodyparttypeId][$FabGmtColorId][$uom]=$amount_finish;
					}
					if(isset($Amount[$this->_sweater][$this->_grey][$poId][$fabricSourceId][$bodyparttypeId][$FabGmtColorId][$uom]))
					{
						$Amount[$this->_sweater][$this->_grey][$poId][$fabricSourceId][$bodyparttypeId][$FabGmtColorId][$uom]+=$amount_grey;
					}
					else{
						$Amount[$this->_sweater][$this->_grey][$poId][$fabricSourceId][$bodyparttypeId][$FabGmtColorId][$uom]=$amount_grey;
					}
				}
			}
			elseif($level==$this->_By_OrderFabricSourceBodyTypeFabColorDeterminIdGsmAndDiaWidth){
				if($fabNatureId==2){
					if(isset($Amount[$this->_knit][$this->_finish][$poId][$fabricSourceId][$bodyparttypeId][$FabGmtColorId][$libYarnCountDeterId][$gsmweight][$diaWidth][$uom])){
						$Amount[$this->_knit][$this->_finish][$poId][$fabricSourceId][$bodyparttypeId][$FabGmtColorId][$libYarnCountDeterId][$gsmweight][$diaWidth][$uom]+=$amount_finish;
					}
					else{
						$Amount[$this->_knit][$this->_finish][$poId][$fabricSourceId][$bodyparttypeId][$FabGmtColorId][$libYarnCountDeterId][$gsmweight][$diaWidth][$uom]=$amount_finish;
					}
					if(isset($Amount[$this->_knit][$this->_grey][$poId][$fabricSourceId][$bodyparttypeId][$FabGmtColorId][$libYarnCountDeterId][$gsmweight][$diaWidth][$uom])){
						$Amount[$this->_knit][$this->_grey][$poId][$fabricSourceId][$bodyparttypeId][$FabGmtColorId][$libYarnCountDeterId][$gsmweight][$diaWidth][$uom]+=$amount_grey;
					}
					else{
						$Amount[$this->_knit][$this->_grey][$poId][$fabricSourceId][$bodyparttypeId][$FabGmtColorId][$libYarnCountDeterId][$gsmweight][$diaWidth][$uom]=$amount_grey;
					}
				}
				elseif($fabNatureId==3){
					if(isset($Amount[$this->_woven][$this->_finish][$poId][$fabricSourceId][$bodyparttypeId][$FabGmtColorId][$libYarnCountDeterId][$gsmweight][$diaWidth][$uom])){
						$Amount[$this->_woven][$this->_finish][$poId][$fabricSourceId][$bodyparttypeId][$FabGmtColorId][$libYarnCountDeterId][$gsmweight][$diaWidth][$uom]+=$amount_finish;
					}
					else{
						$Amount[$this->_woven][$this->_finish][$poId][$fabricSourceId][$bodyparttypeId][$FabGmtColorId][$libYarnCountDeterId][$gsmweight][$diaWidth][$uom]=$amount_finish;
					}
					if(isset($Amount[$this->_woven][$this->_grey][$poId][$fabricSourceId][$bodyparttypeId][$FabGmtColorId][$libYarnCountDeterId][$gsmweight][$diaWidth][$uom]))
					{
						$Amount[$this->_woven][$this->_grey][$poId][$fabricSourceId][$bodyparttypeId][$FabGmtColorId][$libYarnCountDeterId][$gsmweight][$diaWidth][$uom]+=$amount_grey;
					}
					else{
						$Amount[$this->_woven][$this->_grey][$poId][$fabricSourceId][$bodyparttypeId][$FabGmtColorId][$libYarnCountDeterId][$gsmweight][$diaWidth][$uom]=$amount_grey;
					}
				}
				elseif($fabNatureId==100){
					if(isset($Amount[$this->_sweater][$this->_finish][$poId][$fabricSourceId][$bodyparttypeId][$FabGmtColorId][$libYarnCountDeterId][$gsmweight][$diaWidth][$uom])){
						$Amount[$this->_sweater][$this->_finish][$poId][$fabricSourceId][$bodyparttypeId][$FabGmtColorId][$libYarnCountDeterId][$gsmweight][$diaWidth][$uom]+=$amount_finish;
					}
					else{
						$Amount[$this->_sweater][$this->_finish][$poId][$fabricSourceId][$bodyparttypeId][$FabGmtColorId][$libYarnCountDeterId][$gsmweight][$diaWidth][$uom]=$amount_finish;
					}
					if(isset($Amount[$this->_sweater][$this->_grey][$poId][$fabricSourceId][$bodyparttypeId][$FabGmtColorId][$libYarnCountDeterId][$gsmweight][$diaWidth][$uom]))
					{
						$Amount[$this->_sweater][$this->_grey][$poId][$fabricSourceId][$bodyparttypeId][$FabGmtColorId][$libYarnCountDeterId][$gsmweight][$diaWidth][$uom]+=$amount_grey;
					}
					else{
						$Amount[$this->_sweater][$this->_grey][$poId][$fabricSourceId][$bodyparttypeId][$FabGmtColorId][$libYarnCountDeterId][$gsmweight][$diaWidth][$uom]=$amount_grey;
					}
				}
			}
			elseif($level==$this->_By_OrderItemIdBodyIdColorDeterminIdGsmAndDiaWidth){
				if($fabNatureId==2){
					if(isset($Amount[$this->_knit][$this->_finish][$poId][$itemNumberId][$bodypartId][$colorId][$libYarnCountDeterId][$gsmweight][$diaWidth][$uom])){
						$Amount[$this->_knit][$this->_finish][$poId][$itemNumberId][$bodypartId][$colorId][$libYarnCountDeterId][$gsmweight][$diaWidth][$uom]+=$amount_finish;
					}
					else{
						$Amount[$this->_knit][$this->_finish][$poId][$itemNumberId][$bodypartId][$colorId][$libYarnCountDeterId][$gsmweight][$diaWidth][$uom]=$amount_finish;
					}
					if(isset($Amount[$this->_knit][$this->_grey][$poId][$itemNumberId][$bodypartId][$colorId][$libYarnCountDeterId][$gsmweight][$diaWidth][$uom])){
						$Amount[$this->_knit][$this->_grey][$poId][$itemNumberId][$bodypartId][$colorId][$libYarnCountDeterId][$gsmweight][$diaWidth][$uom]+=$amount_grey;
					}
					else{
						$Amount[$this->_knit][$this->_grey][$poId][$itemNumberId][$bodypartId][$colorId][$libYarnCountDeterId][$gsmweight][$diaWidth][$uom]=$amount_grey;
					}
				}
				elseif($fabNatureId==3){
					if(isset($Amount[$this->_woven][$this->_finish][$poId][$itemNumberId][$bodypartId][$colorId][$libYarnCountDeterId][$gsmweight][$diaWidth][$uom])){
						$Amount[$this->_woven][$this->_finish][$poId][$itemNumberId][$bodypartId][$colorId][$libYarnCountDeterId][$gsmweight][$diaWidth][$uom]+=$amount_finish;
					}
					else{
						$Amount[$this->_woven][$this->_finish][$poId][$itemNumberId][$bodypartId][$colorId][$libYarnCountDeterId][$gsmweight][$diaWidth][$uom]=$amount_finish;
					}
					if(isset($Amount[$this->_woven][$this->_grey][$poId][$itemNumberId][$bodypartId][$colorId][$libYarnCountDeterId][$gsmweight][$diaWidth][$uom]))
					{
						$Amount[$this->_woven][$this->_grey][$poId][$itemNumberId][$bodypartId][$colorId][$libYarnCountDeterId][$gsmweight][$diaWidth][$uom]+=$amount_grey;
					}
					else{
						$Amount[$this->_woven][$this->_grey][$poId][$itemNumberId][$bodypartId][$colorId][$libYarnCountDeterId][$gsmweight][$diaWidth][$uom]=$amount_grey;
					}
				}
				elseif($fabNatureId==100){
					if(isset($Amount[$this->_sweater][$this->_finish][$poId][$itemNumberId][$bodypartId][$colorId][$libYarnCountDeterId][$gsmweight][$diaWidth][$uom])){
						$Amount[$this->_sweater][$this->_finish][$poId][$itemNumberId][$bodypartId][$colorId][$libYarnCountDeterId][$gsmweight][$diaWidth][$uom]+=$amount_finish;
					}
					else{
						$Amount[$this->_sweater][$this->_finish][$poId][$itemNumberId][$bodypartId][$colorId][$libYarnCountDeterId][$gsmweight][$diaWidth][$uom]=$amount_finish;
					}
					if(isset($Amount[$this->_sweater][$this->_grey][$poId][$itemNumberId][$bodypartId][$colorId][$libYarnCountDeterId][$gsmweight][$diaWidth][$uom]))
					{
						$Amount[$this->_sweater][$this->_grey][$poId][$itemNumberId][$bodypartId][$colorId][$libYarnCountDeterId][$gsmweight][$diaWidth][$uom]+=$amount_grey;
					}
					else{
						$Amount[$this->_sweater][$this->_grey][$poId][$itemNumberId][$bodypartId][$colorId][$libYarnCountDeterId][$gsmweight][$diaWidth][$uom]=$amount_grey;
					}
				}
			}
			elseif($level==$this->_By_OrderGmtsColorFabricColorAndFabricSouce){
				if($fabNatureId==2){
					if(isset($Amount[$this->_knit][$this->_finish][$poId][$colorId][$FabricColorId][$fabricSourceId][$uom])){
						$Amount[$this->_knit][$this->_finish][$poId][$colorId][$FabricColorId][$fabricSourceId][$uom]+=$amount_finish;
					}
					else{
						$Amount[$this->_knit][$this->_finish][$poId][$colorId][$FabricColorId][$fabricSourceId][$uom]=$amount_finish;
					}
					if(isset($Amount[$this->_knit][$this->_grey][$poId][$colorId][$FabricColorId][$fabricSourceId][$uom])){
						$Amount[$this->_knit][$this->_grey][$poId][$colorId][$FabricColorId][$fabricSourceId][$uom]+=$amount_grey;
					}
					else{
						$Amount[$this->_knit][$this->_grey][$poId][$colorId][$FabricColorId][$fabricSourceId][$uom]=$amount_grey;
					}
				}
				elseif($fabNatureId==3){
					if(isset($Amount[$this->_woven][$this->_finish][$poId][$colorId][$FabricColorId][$fabricSourceId][$uom])){
						$Amount[$this->_woven][$this->_finish][$poId][$colorId][$FabricColorId][$fabricSourceId][$uom]+=$amount_finish;
					}
					else{
						$Amount[$this->_woven][$this->_finish][$poId][$colorId][$FabricColorId][$fabricSourceId][$uom]=$amount_finish;
					}
					if(isset($Amount[$this->_woven][$this->_grey][$poId][$colorId][$FabricColorId][$fabricSourceId][$uom]))
					{
						$Amount[$this->_woven][$this->_grey][$poId][$colorId][$FabricColorId][$fabricSourceId][$uom]+=$amount_grey;
					}
					else{
						$Amount[$this->_woven][$this->_grey][$poId][$colorId][$FabricColorId][$fabricSourceId][$uom]=$amount_grey;
					}
				}
				elseif($fabNatureId==100){
					if(isset($Amount[$this->_sweater][$this->_finish][$poId][$colorId][$FabricColorId][$fabricSourceId][$uom])){
						$Amount[$this->_sweater][$this->_finish][$poId][$colorId][$FabricColorId][$fabricSourceId][$uom]+=$amount_finish;
					}
					else{
						$Amount[$this->_sweater][$this->_finish][$poId][$colorId][$FabricColorId][$fabricSourceId][$uom]=$amount_finish;
					}
					if(isset($Amount[$this->_sweater][$this->_grey][$poId][$colorId][$FabricColorId][$fabricSourceId][$uom]))
					{
						$Amount[$this->_sweater][$this->_grey][$poId][$colorId][$FabricColorId][$fabricSourceId][$uom]+=$amount_grey;
					}
					else{
						$Amount[$this->_sweater][$this->_grey][$poId][$colorId][$FabricColorId][$fabricSourceId][$uom]=$amount_grey;
					}
				}
			}
			elseif($level==$this->_By_OrderBodypartDeterminIdAndGmtscolor){
				if($fabNatureId==2){
					if(isset($Amount[$this->_knit][$this->_finish][$poId][$bodypartId][$libYarnCountDeterId][$fabricColorId][$uom])){
						$Amount[$this->_knit][$this->_finish][$poId][$bodypartId][$libYarnCountDeterId][$fabricColorId][$uom]+=$amount_finish;
					}
					else{
						$Amount[$this->_knit][$this->_finish][$poId][$bodypartId][$libYarnCountDeterId][$fabricColorId][$uom]=$amount_finish;
					}
					if(isset($Amount[$this->_knit][$this->_grey][$poId][$bodypartId][$libYarnCountDeterId][$fabricColorId][$uom])){
						$Amount[$this->_knit][$this->_grey][$poId][$bodypartId][$libYarnCountDeterId][$fabricColorId][$uom]+=$amount_grey;
					}
					else{
						$Amount[$this->_knit][$this->_grey][$poId][$bodypartId][$libYarnCountDeterId][$fabricColorId][$uom]=$amount_grey;
					}
				}
				elseif($fabNatureId==3){
					if(isset($Amount[$this->_woven][$this->_finish][$poId][$bodypartId][$libYarnCountDeterId][$fabricColorId][$uom])){
						$Amount[$this->_woven][$this->_finish][$poId][$bodypartId][$libYarnCountDeterId][$fabricColorId][$uom]+=$amount_finish;
					}
					else{
						$Amount[$this->_woven][$this->_finish][$poId][$bodypartId][$libYarnCountDeterId][$fabricColorId][$uom]=$amount_finish;
					}
					if(isset($Amount[$this->_woven][$this->_grey][$poId][$bodypartId][$libYarnCountDeterId][$fabricColorId][$uom]))
					{
						$Amount[$this->_woven][$this->_grey][$poId][$bodypartId][$libYarnCountDeterId][$fabricColorId][$uom]+=$amount_grey;
					}
					else{
						$Amount[$this->_woven][$this->_grey][$poId][$bodypartId][$libYarnCountDeterId][$fabricColorId][$uom]=$amount_grey;
					}
				}
				elseif($fabNatureId==100){
					if(isset($Amount[$this->_sweater][$this->_finish][$poId][$bodypartId][$libYarnCountDeterId][$fabricColorId][$uom])){
						$Amount[$this->_sweater][$this->_finish][$poId][$bodypartId][$libYarnCountDeterId][$fabricColorId][$uom]+=$amount_finish;
					}
					else{
						$Amount[$this->_sweater][$this->_finish][$poId][$bodypartId][$libYarnCountDeterId][$fabricColorId][$uom]=$amount_finish;
					}
					if(isset($Amount[$this->_sweater][$this->_grey][$poId][$bodypartId][$libYarnCountDeterId][$fabricColorId][$uom]))
					{
						$Amount[$this->_sweater][$this->_grey][$poId][$bodypartId][$libYarnCountDeterId][$fabricColorId][$uom]+=$amount_grey;
					}
					else{
						$Amount[$this->_sweater][$this->_grey][$poId][$bodypartId][$libYarnCountDeterId][$fabricColorId][$uom]=$amount_grey;
					}
				}
			}
			elseif($level==$this->_By_OrderBodypartDeterminIdAndDiaWidth){
				if($fabNatureId==2){
					if(isset($Amount[$this->_knit][$this->_finish][$poId][$bodypartId][$libYarnCountDeterId][$diaWidth][$uom])){
						$Amount[$this->_knit][$this->_finish][$poId][$bodypartId][$libYarnCountDeterId][$diaWidth][$uom]+=$amount_finish;
					}
					else{
						$Amount[$this->_knit][$this->_finish][$poId][$bodypartId][$libYarnCountDeterId][$diaWidth][$uom]=$amount_finish;
					}
					if(isset($Amount[$this->_knit][$this->_grey][$poId][$bodypartId][$libYarnCountDeterId][$diaWidth][$uom])){
						$Amount[$this->_knit][$this->_grey][$poId][$bodypartId][$libYarnCountDeterId][$diaWidth][$uom]+=$amount_grey;
					}
					else{
						$Amount[$this->_knit][$this->_grey][$poId][$bodypartId][$libYarnCountDeterId][$diaWidth][$uom]=$amount_grey;
					}
				}
				elseif($fabNatureId==3){
					if(isset($Amount[$this->_woven][$this->_finish][$poId][$bodypartId][$libYarnCountDeterId][$diaWidth][$uom])){
						$Amount[$this->_woven][$this->_finish][$poId][$bodypartId][$libYarnCountDeterId][$diaWidth][$uom]+=$amount_finish;
					}
					else{
						$Amount[$this->_woven][$this->_finish][$poId][$bodypartId][$libYarnCountDeterId][$diaWidth][$uom]=$amount_finish;
					}
					if(isset($Amount[$this->_woven][$this->_grey][$poId][$bodypartId][$libYarnCountDeterId][$diaWidth][$uom]))
					{
						$Amount[$this->_woven][$this->_grey][$poId][$bodypartId][$libYarnCountDeterId][$diaWidth][$uom]+=$amount_grey;
					}
					else{
						$Amount[$this->_woven][$this->_grey][$poId][$bodypartId][$libYarnCountDeterId][$diaWidth][$uom]=$amount_grey;
					}
				}
				elseif($fabNatureId==100){
					if(isset($Amount[$this->_sweater][$this->_finish][$poId][$bodypartId][$libYarnCountDeterId][$diaWidth][$uom])){
						$Amount[$this->_sweater][$this->_finish][$poId][$bodypartId][$libYarnCountDeterId][$diaWidth][$uom]+=$amount_finish;
					}
					else{
						$Amount[$this->_sweater][$this->_finish][$poId][$bodypartId][$libYarnCountDeterId][$diaWidth][$uom]=$amount_finish;
					}
					if(isset($Amount[$this->_sweater][$this->_grey][$poId][$bodypartId][$libYarnCountDeterId][$diaWidth][$uom]))
					{
						$Amount[$this->_sweater][$this->_grey][$poId][$bodypartId][$libYarnCountDeterId][$diaWidth][$uom]+=$amount_grey;
					}
					else{
						$Amount[$this->_sweater][$this->_grey][$poId][$bodypartId][$libYarnCountDeterId][$diaWidth][$uom]=$amount_grey;
					}
				}
			}
			elseif($level==$this->_By_OrderItemidCountrydateBodypartDeterminIdDiatypeDiaAndGsm){
				if($fabNatureId==2){
					if(isset($Amount[$this->_knit][$this->_finish][$poId][$itemNumberId][$countryShipDate][$bodypartId][$libYarnCountDeterId][$widthDiaType][$diaWidth][$gsmweight][$uom])){
						$Amount[$this->_knit][$this->_finish][$poId][$itemNumberId][$countryShipDate][$bodypartId][$libYarnCountDeterId][$widthDiaType][$diaWidth][$gsmweight][$uom]+=$amount_finish;
					}
					else{
						$Amount[$this->_knit][$this->_finish][$poId][$itemNumberId][$countryShipDate][$bodypartId][$libYarnCountDeterId][$widthDiaType][$diaWidth][$gsmweight][$uom]=$amount_finish;
					}
					if(isset($Amount[$this->_knit][$this->_grey][$poId][$itemNumberId][$countryShipDate][$bodypartId][$libYarnCountDeterId][$widthDiaType][$diaWidth][$gsmweight][$uom])){
						$Amount[$this->_knit][$this->_grey][$poId][$itemNumberId][$countryShipDate][$bodypartId][$libYarnCountDeterId][$widthDiaType][$diaWidth][$gsmweight][$uom]+=$amount_grey;
					}
					else{
						$Amount[$this->_knit][$this->_grey][$poId][$itemNumberId][$countryShipDate][$bodypartId][$libYarnCountDeterId][$widthDiaType][$diaWidth][$gsmweight][$uom]=$amount_grey;
					}
				}
				elseif($fabNatureId==3){
					if(isset($Amount[$this->_woven][$this->_finish][$poId][$itemNumberId][$countryShipDate][$bodypartId][$libYarnCountDeterId][$widthDiaType][$diaWidth][$gsmweight][$uom])){
						$Amount[$this->_woven][$this->_finish][$poId][$itemNumberId][$countryShipDate][$bodypartId][$libYarnCountDeterId][$widthDiaType][$diaWidth][$gsmweight][$uom]+=$amount_finish;
					}
					else{
						$Amount[$this->_woven][$this->_finish][$poId][$itemNumberId][$countryShipDate][$bodypartId][$libYarnCountDeterId][$widthDiaType][$diaWidth][$gsmweight][$uom]=$amount_finish;
					}
					if(isset($Amount[$this->_woven][$this->_grey][$poId][$bodypartId][$libYarnCountDeterId][$diaWidth][$uom]))
					{
						$Amount[$this->_woven][$this->_grey][$poId][$itemNumberId][$countryShipDate][$bodypartId][$libYarnCountDeterId][$widthDiaType][$diaWidth][$gsmweight][$uom]+=$amount_grey;
					}
					else{
						$Amount[$this->_woven][$this->_grey][$poId][$itemNumberId][$countryShipDate][$bodypartId][$libYarnCountDeterId][$widthDiaType][$diaWidth][$gsmweight][$uom]=$amount_grey;
					}
				}
				elseif($fabNatureId==100){
					if(isset($Amount[$this->_sweater][$this->_finish][$poId][$itemNumberId][$countryShipDate][$bodypartId][$libYarnCountDeterId][$widthDiaType][$diaWidth][$gsmweight][$uom])){
						$Amount[$this->_sweater][$this->_finish][$poId][$itemNumberId][$countryShipDate][$bodypartId][$libYarnCountDeterId][$widthDiaType][$diaWidth][$gsmweight][$uom]+=$amount_finish;
					}
					else{
						$Amount[$this->_sweater][$this->_finish][$poId][$itemNumberId][$countryShipDate][$bodypartId][$libYarnCountDeterId][$widthDiaType][$diaWidth][$gsmweight][$uom]=$amount_finish;
					}
					if(isset($Amount[$this->_sweater][$this->_grey][$poId][$itemNumberId][$countryShipDate][$bodypartId][$libYarnCountDeterId][$widthDiaType][$diaWidth][$gsmweight][$uom]))
					{
						$Amount[$this->_sweater][$this->_grey][$poId][$itemNumberId][$countryShipDate][$bodypartId][$libYarnCountDeterId][$widthDiaType][$diaWidth][$gsmweight][$uom]+=$amount_grey;
					}
					else{
						$Amount[$this->_sweater][$this->_grey][$poId][$itemNumberId][$countryShipDate][$bodypartId][$libYarnCountDeterId][$widthDiaType][$diaWidth][$gsmweight][$uom]=$amount_grey;
					}
				}
			}
			
			elseif($level==$this->_By_JobColorBodyTypeDeterminIdGsmAndDiaWidth){
				if($fabNatureId==2){
					if(isset($Amount[$this->_knit][$this->_finish][$jobNo][$colorId][$bodyparttypeId][$libYarnCountDeterId][$gsmweight][$diaWidth][$fabricSourceId][$uom])){
						$Amount[$this->_knit][$this->_finish][$jobNo][$colorId][$bodyparttypeId][$libYarnCountDeterId][$gsmweight][$diaWidth][$fabricSourceId][$uom]+=$amount_finish;
					}
					else{
						$Amount[$this->_knit][$this->_finish][$jobNo][$colorId][$bodyparttypeId][$libYarnCountDeterId][$gsmweight][$diaWidth][$fabricSourceId][$uom]=$amount_finish;
					}
					if(isset($Amount[$this->_knit][$this->_grey][$jobNo][$colorId][$bodyparttypeId][$libYarnCountDeterId][$gsmweight][$diaWidth][$fabricSourceId][$uom])){
						$Amount[$this->_knit][$this->_grey][$jobNo][$colorId][$bodyparttypeId][$libYarnCountDeterId][$gsmweight][$diaWidth][$fabricSourceId][$uom]+=$amount_grey;
					}
					else{
						$Amount[$this->_knit][$this->_grey][$jobNo][$colorId][$bodyparttypeId][$libYarnCountDeterId][$gsmweight][$diaWidth][$fabricSourceId][$uom]=$amount_grey;
					}
				}
				elseif($fabNatureId==3){
					if(isset($Amount[$this->_woven][$this->_finish][$jobNo][$colorId][$bodyparttypeId][$libYarnCountDeterId][$gsmweight][$diaWidth][$fabricSourceId][$uom])){
						$Amount[$this->_woven][$this->_finish][$jobNo][$colorId][$bodyparttypeId][$libYarnCountDeterId][$gsmweight][$diaWidth][$fabricSourceId][$uom]+=$amount_finish;
					}
					else{
						$Amount[$this->_woven][$this->_finish][$jobNo][$colorId][$bodyparttypeId][$libYarnCountDeterId][$gsmweight][$diaWidth][$fabricSourceId][$uom]=$amount_finish;
					}
					if(isset($Amount[$this->_woven][$this->_grey][$jobNo][$colorId][$bodyparttypeId][$libYarnCountDeterId][$gsmweight][$diaWidth][$fabricSourceId][$uom]))
					{
						$Amount[$this->_woven][$this->_grey][$jobNo][$colorId][$bodyparttypeId][$libYarnCountDeterId][$gsmweight][$diaWidth][$fabricSourceId][$uom]+=$amount_grey;
					}
					else{
						$Amount[$this->_woven][$this->_grey][$jobNo][$colorId][$bodyparttypeId][$libYarnCountDeterId][$gsmweight][$diaWidth][$fabricSourceId][$uom]=$amount_grey;
					}
				}
				elseif($fabNatureId==100){
					if(isset($Amount[$this->_sweater][$this->_finish][$jobNo][$colorId][$bodyparttypeId][$libYarnCountDeterId][$gsmweight][$fabricSourceId][$diaWidth][$uom])){
						$Amount[$this->_sweater][$this->_finish][$jobNo][$colorId][$bodyparttypeId][$libYarnCountDeterId][$gsmweight][$diaWidth][$fabricSourceId][$uom]+=$amount_finish;
					}
					else{
						$Amount[$this->_sweater][$this->_finish][$jobNo][$colorId][$bodyparttypeId][$libYarnCountDeterId][$gsmweight][$diaWidth][$fabricSourceId][$uom]=$amount_finish;
					}
					if(isset($Amount[$this->_sweater][$this->_grey][$jobNo][$colorId][$bodyparttypeId][$libYarnCountDeterId][$gsmweight][$diaWidth][$fabricSourceId][$uom]))
					{
						$Amount[$this->_sweater][$this->_grey][$jobNo][$colorId][$bodyparttypeId][$libYarnCountDeterId][$gsmweight][$diaWidth][$fabricSourceId][$uom]+=$amount_grey;
					}
					else{
						$Amount[$this->_sweater][$this->_grey][$jobNo][$colorId][$bodyparttypeId][$libYarnCountDeterId][$gsmweight][$diaWidth][$fabricSourceId][$uom]=$amount_grey;
					}
				}
			}
			elseif($level==$this->_By_JobColorFabricColorBodyTypeDeterminIdGsmAndDiaWidth){
				if($fabNatureId==2){
					if(isset($Amount[$this->_knit][$this->_finish][$jobNo][$colorId][$fabricColorId][$bodyparttypeId][$libYarnCountDeterId][$gsmweight][$diaWidth][$fabricSourceId][$uom])){
						$Amount[$this->_knit][$this->_finish][$jobNo][$colorId][$fabricColorId][$bodyparttypeId][$libYarnCountDeterId][$gsmweight][$diaWidth][$fabricSourceId][$uom]+=$amount_finish;
					}
					else{
						$Amount[$this->_knit][$this->_finish][$jobNo][$colorId][$fabricColorId][$bodyparttypeId][$libYarnCountDeterId][$gsmweight][$diaWidth][$fabricSourceId][$uom]=$amount_finish;
					}
					if(isset($Amount[$this->_knit][$this->_grey][$jobNo][$colorId][$fabricColorId][$bodyparttypeId][$libYarnCountDeterId][$gsmweight][$diaWidth][$fabricSourceId][$uom])){
						$Amount[$this->_knit][$this->_grey][$jobNo][$colorId][$fabricColorId][$bodyparttypeId][$libYarnCountDeterId][$gsmweight][$diaWidth][$fabricSourceId][$uom]+=$amount_grey;
					}
					else{
						$Amount[$this->_knit][$this->_grey][$jobNo][$colorId][$fabricColorId][$bodyparttypeId][$libYarnCountDeterId][$gsmweight][$diaWidth][$fabricSourceId][$uom]=$amount_grey;
					}
				}
				elseif($fabNatureId==3){
					if(isset($Amount[$this->_woven][$this->_finish][$jobNo][$colorId][$fabricColorId][$bodyparttypeId][$libYarnCountDeterId][$gsmweight][$diaWidth][$fabricSourceId][$uom])){
						$Amount[$this->_woven][$this->_finish][$jobNo][$colorId][$fabricColorId][$bodyparttypeId][$libYarnCountDeterId][$gsmweight][$diaWidth][$fabricSourceId][$uom]+=$amount_finish;
					}
					else{
						$Amount[$this->_woven][$this->_finish][$jobNo][$colorId][$fabricColorId][$bodyparttypeId][$libYarnCountDeterId][$gsmweight][$diaWidth][$fabricSourceId][$uom]=$amount_finish;
					}
					if(isset($Amount[$this->_woven][$this->_grey][$jobNo][$colorId][$fabricColorId][$bodyparttypeId][$libYarnCountDeterId][$gsmweight][$diaWidth][$fabricSourceId][$uom]))
					{
						$Amount[$this->_woven][$this->_grey][$jobNo][$colorId][$fabricColorId][$bodyparttypeId][$libYarnCountDeterId][$gsmweight][$diaWidth][$fabricSourceId][$uom]+=$amount_grey;
					}
					else{
						$Amount[$this->_woven][$this->_grey][$jobNo][$colorId][$fabricColorId][$bodyparttypeId][$libYarnCountDeterId][$gsmweight][$diaWidth][$fabricSourceId][$uom]=$amount_grey;
					}
				}
				elseif($fabNatureId==100){
					if(isset($Amount[$this->_sweater][$this->_finish][$jobNo][$colorId][$fabricColorId][$bodyparttypeId][$libYarnCountDeterId][$gsmweight][$fabricSourceId][$diaWidth][$uom])){
						$Amount[$this->_sweater][$this->_finish][$jobNo][$colorId][$fabricColorId][$bodyparttypeId][$libYarnCountDeterId][$gsmweight][$diaWidth][$fabricSourceId][$uom]+=$amount_finish;
					}
					else{
						$Amount[$this->_sweater][$this->_finish][$jobNo][$colorId][$fabricColorId][$bodyparttypeId][$libYarnCountDeterId][$gsmweight][$diaWidth][$fabricSourceId][$uom]=$amount_finish;
					}
					if(isset($Amount[$this->_sweater][$this->_grey][$jobNo][$colorId][$fabricColorId][$bodyparttypeId][$libYarnCountDeterId][$gsmweight][$diaWidth][$fabricSourceId][$uom]))
					{
						$Amount[$this->_sweater][$this->_grey][$jobNo][$colorId][$fabricColorId][$bodyparttypeId][$libYarnCountDeterId][$gsmweight][$diaWidth][$fabricSourceId][$uom]+=$amount_grey;
					}
					else{
						$Amount[$this->_sweater][$this->_grey][$jobNo][$colorId][$fabricColorId][$bodyparttypeId][$libYarnCountDeterId][$gsmweight][$diaWidth][$fabricSourceId][$uom]=$amount_grey;
					}
				}
			}
			elseif($level==$this->_By_JobColorFabricColorGmtsSizeBodyTypeDeterminIdGsmAndDiaWidth){
				if($fabNatureId==2){
					if(isset($Amount[$this->_knit][$this->_finish][$jobNo][$colorId][$fabricColorId][$sizeId][$bodyparttypeId][$libYarnCountDeterId][$gsmweight][$diaWidth][$fabricSourceId][$uom])){
						$Amount[$this->_knit][$this->_finish][$jobNo][$colorId][$fabricColorId][$sizeId][$bodyparttypeId][$libYarnCountDeterId][$gsmweight][$diaWidth][$fabricSourceId][$uom]+=$amount_finish;
					}
					else{
						$Amount[$this->_knit][$this->_finish][$jobNo][$colorId][$fabricColorId][$sizeId][$bodyparttypeId][$libYarnCountDeterId][$gsmweight][$diaWidth][$fabricSourceId][$uom]=$amount_finish;
					}
					if(isset($Amount[$this->_knit][$this->_grey][$jobNo][$colorId][$fabricColorId][$sizeId][$bodyparttypeId][$libYarnCountDeterId][$gsmweight][$diaWidth][$fabricSourceId][$uom])){
						$Amount[$this->_knit][$this->_grey][$jobNo][$colorId][$fabricColorId][$sizeId][$bodyparttypeId][$libYarnCountDeterId][$gsmweight][$diaWidth][$fabricSourceId][$uom]+=$amount_grey;
					}
					else{
						$Amount[$this->_knit][$this->_grey][$jobNo][$colorId][$fabricColorId][$sizeId][$bodyparttypeId][$libYarnCountDeterId][$gsmweight][$diaWidth][$fabricSourceId][$uom]=$amount_grey;
					}
				}
				elseif($fabNatureId==3){
					if(isset($Amount[$this->_woven][$this->_finish][$jobNo][$colorId][$fabricColorId][$sizeId][$bodyparttypeId][$libYarnCountDeterId][$gsmweight][$diaWidth][$fabricSourceId][$uom])){
						$Amount[$this->_woven][$this->_finish][$jobNo][$colorId][$fabricColorId][$sizeId][$bodyparttypeId][$libYarnCountDeterId][$gsmweight][$diaWidth][$fabricSourceId][$uom]+=$amount_finish;
					}
					else{
						$Amount[$this->_woven][$this->_finish][$jobNo][$colorId][$fabricColorId][$sizeId][$bodyparttypeId][$libYarnCountDeterId][$gsmweight][$diaWidth][$fabricSourceId][$uom]=$amount_finish;
					}
					if(isset($Amount[$this->_woven][$this->_grey][$jobNo][$colorId][$fabricColorId][$sizeId][$bodyparttypeId][$libYarnCountDeterId][$gsmweight][$diaWidth][$fabricSourceId][$uom]))
					{
						$Amount[$this->_woven][$this->_grey][$jobNo][$colorId][$fabricColorId][$sizeId][$bodyparttypeId][$libYarnCountDeterId][$gsmweight][$diaWidth][$fabricSourceId][$uom]+=$amount_grey;
					}
					else{
						$Amount[$this->_woven][$this->_grey][$jobNo][$colorId][$fabricColorId][$sizeId][$bodyparttypeId][$libYarnCountDeterId][$gsmweight][$diaWidth][$fabricSourceId][$uom]=$amount_grey;
					}
				}
				elseif($fabNatureId==100){
					if(isset($Amount[$this->_sweater][$this->_finish][$jobNo][$colorId][$fabricColorId][$sizeId][$bodyparttypeId][$libYarnCountDeterId][$gsmweight][$fabricSourceId][$diaWidth][$uom])){
						$Amount[$this->_sweater][$this->_finish][$jobNo][$colorId][$fabricColorId][$sizeId][$bodyparttypeId][$libYarnCountDeterId][$gsmweight][$diaWidth][$fabricSourceId][$uom]+=$amount_finish;
					}
					else{
						$Amount[$this->_sweater][$this->_finish][$jobNo][$colorId][$fabricColorId][$sizeId][$bodyparttypeId][$libYarnCountDeterId][$gsmweight][$diaWidth][$fabricSourceId][$uom]=$amount_finish;
					}
					if(isset($Amount[$this->_sweater][$this->_grey][$jobNo][$colorId][$fabricColorId][$sizeId][$bodyparttypeId][$libYarnCountDeterId][$gsmweight][$diaWidth][$fabricSourceId][$uom]))
					{
						$Amount[$this->_sweater][$this->_grey][$jobNo][$colorId][$fabricColorId][$sizeId][$bodyparttypeId][$libYarnCountDeterId][$gsmweight][$diaWidth][$fabricSourceId][$uom]+=$amount_grey;
					}
					else{
						$Amount[$this->_sweater][$this->_grey][$jobNo][$colorId][$fabricColorId][$sizeId][$bodyparttypeId][$libYarnCountDeterId][$gsmweight][$diaWidth][$fabricSourceId][$uom]=$amount_grey;
					}
				}
			}
			elseif($level==$this->_By_JobColorFabricColorBodyPartIdDeterminIdGsmAndDiaWidth){
				if($fabNatureId==2){
					if(isset($Amount[$this->_knit][$this->_finish][$jobNo][$colorId][$fabricColorId][$bodypartId][$libYarnCountDeterId][$gsmweight][$diaWidth][$fabricSourceId][$uom])){
						$Amount[$this->_knit][$this->_finish][$jobNo][$colorId][$fabricColorId][$bodypartId][$libYarnCountDeterId][$gsmweight][$diaWidth][$fabricSourceId][$uom]+=$amount_finish;
					}
					else{
						$Amount[$this->_knit][$this->_finish][$jobNo][$colorId][$fabricColorId][$bodypartId][$libYarnCountDeterId][$gsmweight][$diaWidth][$fabricSourceId][$uom]=$amount_finish;
					}
					if(isset($Amount[$this->_knit][$this->_grey][$jobNo][$colorId][$fabricColorId][$bodypartId][$libYarnCountDeterId][$gsmweight][$diaWidth][$fabricSourceId][$uom])){
						$Amount[$this->_knit][$this->_grey][$jobNo][$colorId][$fabricColorId][$bodypartId][$libYarnCountDeterId][$gsmweight][$diaWidth][$fabricSourceId][$uom]+=$amount_grey;
					}
					else{
						$Amount[$this->_knit][$this->_grey][$jobNo][$colorId][$fabricColorId][$bodypartId][$libYarnCountDeterId][$gsmweight][$diaWidth][$fabricSourceId][$uom]=$amount_grey;
					}
				}
				elseif($fabNatureId==3){
					if(isset($Amount[$this->_woven][$this->_finish][$jobNo][$colorId][$fabricColorId][$bodypartId][$libYarnCountDeterId][$gsmweight][$diaWidth][$fabricSourceId][$uom])){
						$Amount[$this->_woven][$this->_finish][$jobNo][$colorId][$fabricColorId][$bodypartId][$libYarnCountDeterId][$gsmweight][$diaWidth][$fabricSourceId][$uom]+=$amount_finish;
					}
					else{
						$Amount[$this->_woven][$this->_finish][$jobNo][$colorId][$fabricColorId][$bodypartId][$libYarnCountDeterId][$gsmweight][$diaWidth][$fabricSourceId][$uom]=$amount_finish;
					}
					if(isset($Amount[$this->_woven][$this->_grey][$jobNo][$colorId][$fabricColorId][$bodypartId][$libYarnCountDeterId][$gsmweight][$diaWidth][$fabricSourceId][$uom]))
					{
						$Amount[$this->_woven][$this->_grey][$jobNo][$colorId][$fabricColorId][$bodypartId][$libYarnCountDeterId][$gsmweight][$diaWidth][$fabricSourceId][$uom]+=$amount_grey;
					}
					else{
						$Amount[$this->_woven][$this->_grey][$jobNo][$colorId][$fabricColorId][$bodypartId][$libYarnCountDeterId][$gsmweight][$diaWidth][$fabricSourceId][$uom]=$amount_grey;
					}
				}
				elseif($fabNatureId==100){
					if(isset($Amount[$this->_sweater][$this->_finish][$jobNo][$colorId][$fabricColorId][$bodypartId][$libYarnCountDeterId][$gsmweight][$fabricSourceId][$diaWidth][$uom])){
						$Amount[$this->_sweater][$this->_finish][$jobNo][$colorId][$fabricColorId][$bodypartId][$libYarnCountDeterId][$gsmweight][$diaWidth][$fabricSourceId][$uom]+=$amount_finish;
					}
					else{
						$Amount[$this->_sweater][$this->_finish][$jobNo][$colorId][$fabricColorId][$bodypartId][$libYarnCountDeterId][$gsmweight][$diaWidth][$fabricSourceId][$uom]=$amount_finish;
					}
					if(isset($Amount[$this->_sweater][$this->_grey][$jobNo][$colorId][$fabricColorId][$bodypartId][$libYarnCountDeterId][$gsmweight][$diaWidth][$fabricSourceId][$uom]))
					{
						$Amount[$this->_sweater][$this->_grey][$jobNo][$colorId][$fabricColorId][$bodypartId][$libYarnCountDeterId][$gsmweight][$diaWidth][$fabricSourceId][$uom]+=$amount_grey;
					}
					else{
						$Amount[$this->_sweater][$this->_grey][$jobNo][$colorId][$fabricColorId][$bodypartId][$libYarnCountDeterId][$gsmweight][$diaWidth][$fabricSourceId][$uom]=$amount_grey;
					}
				}
			}
			elseif($level==$this->_By_JobColorBodyTypeDeterminIdAndDiaWidth){
				if($fabNatureId==2){
					if(isset($Amount[$this->_knit][$this->_finish][$jobNo][$colorId][$bodyparttypeId][$libYarnCountDeterId][$diaWidth][$uom])){
						$Amount[$this->_knit][$this->_finish][$jobNo][$colorId][$bodyparttypeId][$libYarnCountDeterId][$diaWidth][$uom]+=$amount_finish;
					}
					else{
						$Amount[$this->_knit][$this->_finish][$jobNo][$colorId][$bodyparttypeId][$libYarnCountDeterId][$diaWidth][$uom]=$amount_finish;
					}
					if(isset($Amount[$this->_knit][$this->_grey][$jobNo][$colorId][$bodyparttypeId][$libYarnCountDeterId][$diaWidth][$uom])){
						$Amount[$this->_knit][$this->_grey][$jobNo][$colorId][$bodyparttypeId][$libYarnCountDeterId][$diaWidth][$uom]+=$amount_grey;
					}
					else{
						$Amount[$this->_knit][$this->_grey][$jobNo][$colorId][$bodyparttypeId][$libYarnCountDeterId][$diaWidth][$uom]=$amount_grey;
					}
				}
				elseif($fabNatureId==3){
					if(isset($Amount[$this->_woven][$this->_finish][$jobNo][$colorId][$bodyparttypeId][$libYarnCountDeterId][$diaWidth][$uom])){
						$Amount[$this->_woven][$this->_finish][$jobNo][$colorId][$bodyparttypeId][$libYarnCountDeterId][$diaWidth][$uom]+=$amount_finish;
					}
					else{
						$Amount[$this->_woven][$this->_finish][$jobNo][$colorId][$bodyparttypeId][$libYarnCountDeterId][$diaWidth][$uom]=$amount_finish;
					}
					if(isset($Amount[$this->_woven][$this->_grey][$jobNo][$colorId][$bodyparttypeId][$libYarnCountDeterId][$diaWidth][$uom]))
					{
						$Amount[$this->_woven][$this->_grey][$jobNo][$colorId][$bodyparttypeId][$libYarnCountDeterId][$diaWidth][$uom]+=$amount_grey;
					}
					else{
						$Amount[$this->_woven][$this->_grey][$jobNo][$colorId][$bodyparttypeId][$libYarnCountDeterId][$diaWidth][$uom]=$amount_grey;
					}
				}
				elseif($fabNatureId==100){
					if(isset($Amount[$this->_sweater][$this->_finish][$jobNo][$colorId][$bodyparttypeId][$libYarnCountDeterId][$diaWidth][$uom])){
						$Amount[$this->_sweater][$this->_finish][$jobNo][$colorId][$bodyparttypeId][$libYarnCountDeterId][$diaWidth][$uom]+=$amount_finish;
					}
					else{
						$Amount[$this->_sweater][$this->_finish][$jobNo][$colorId][$bodyparttypeId][$libYarnCountDeterId][$diaWidth][$uom]=$amount_finish;
					}
					if(isset($Amount[$this->_sweater][$this->_grey][$jobNo][$colorId][$bodyparttypeId][$libYarnCountDeterId][$diaWidth][$uom]))
					{
						$Amount[$this->_sweater][$this->_grey][$jobNo][$colorId][$bodyparttypeId][$libYarnCountDeterId][$diaWidth][$uom]+=$amount_grey;
					}
					else{
						$Amount[$this->_sweater][$this->_grey][$jobNo][$colorId][$bodyparttypeId][$libYarnCountDeterId][$diaWidth][$uom]=$amount_grey;
					}
				}
			}
			elseif($level==$this->_By_JobColorFabColorBodyTypeDeterminIdAndDiaWidth){
				if($fabNatureId==2){
					if(isset($Amount[$this->_knit][$this->_finish][$jobNo][$colorId][$FabricColorId][$bodyparttypeId][$libYarnCountDeterId][$diaWidth][$uom])){
						$Amount[$this->_knit][$this->_finish][$jobNo][$colorId][$FabricColorId][$bodyparttypeId][$libYarnCountDeterId][$diaWidth][$uom]+=$amount_finish;
					}
					else{
						$Amount[$this->_knit][$this->_finish][$jobNo][$colorId][$FabricColorId][$bodyparttypeId][$libYarnCountDeterId][$diaWidth][$uom]=$amount_finish;
					}
					if(isset($Amount[$this->_knit][$this->_grey][$jobNo][$colorId][$FabricColorId][$bodyparttypeId][$libYarnCountDeterId][$diaWidth][$uom])){
						$Amount[$this->_knit][$this->_grey][$jobNo][$colorId][$FabricColorId][$bodyparttypeId][$libYarnCountDeterId][$diaWidth][$uom]+=$amount_grey;
					}
					else{
						$Amount[$this->_knit][$this->_grey][$jobNo][$colorId][$FabricColorId][$bodyparttypeId][$libYarnCountDeterId][$diaWidth][$uom]=$amount_grey;
					}
				}
				elseif($fabNatureId==3){
					if(isset($Amount[$this->_woven][$this->_finish][$jobNo][$colorId][$FabricColorId][$bodyparttypeId][$libYarnCountDeterId][$diaWidth][$uom])){
						$Amount[$this->_woven][$this->_finish][$jobNo][$colorId][$FabricColorId][$bodyparttypeId][$libYarnCountDeterId][$diaWidth][$uom]+=$amount_finish;
					}
					else{
						$Amount[$this->_woven][$this->_finish][$jobNo][$colorId][$FabricColorId][$bodyparttypeId][$libYarnCountDeterId][$diaWidth][$uom]=$amount_finish;
					}
					if(isset($Amount[$this->_woven][$this->_grey][$jobNo][$colorId][$FabricColorId][$bodyparttypeId][$libYarnCountDeterId][$diaWidth][$uom]))
					{
						$Amount[$this->_woven][$this->_grey][$jobNo][$colorId][$FabricColorId][$bodyparttypeId][$libYarnCountDeterId][$diaWidth][$uom]+=$amount_grey;
					}
					else{
						$Amount[$this->_woven][$this->_grey][$jobNo][$colorId][$FabricColorId][$bodyparttypeId][$libYarnCountDeterId][$diaWidth][$uom]=$amount_grey;
					}
				}
				elseif($fabNatureId==100){
					if(isset($Amount[$this->_sweater][$this->_finish][$jobNo][$colorId][$FabricColorId][$bodyparttypeId][$libYarnCountDeterId][$diaWidth][$uom])){
						$Amount[$this->_sweater][$this->_finish][$jobNo][$colorId][$FabricColorId][$bodyparttypeId][$libYarnCountDeterId][$diaWidth][$uom]+=$amount_finish;
					}
					else{
						$Amount[$this->_sweater][$this->_finish][$jobNo][$colorId][$FabricColorId][$bodyparttypeId][$libYarnCountDeterId][$diaWidth][$uom]=$amount_finish;
					}
					if(isset($Amount[$this->_sweater][$this->_grey][$jobNo][$colorId][$FabricColorId][$bodyparttypeId][$libYarnCountDeterId][$diaWidth][$uom]))
					{
						$Amount[$this->_sweater][$this->_grey][$jobNo][$colorId][$FabricColorId][$bodyparttypeId][$libYarnCountDeterId][$diaWidth][$uom]+=$amount_grey;
					}
					else{
						$Amount[$this->_sweater][$this->_grey][$jobNo][$colorId][$FabricColorId][$bodyparttypeId][$libYarnCountDeterId][$diaWidth][$uom]=$amount_grey;
					}
				}
			}
			elseif($level==$this->_By_JobFabSourceColorFabColorBodyTypeDeterminIdAndDiaWidth){
				if($fabNatureId==2){
					if(isset($Amount[$this->_knit][$this->_finish][$jobNo][$fabricSourceId][$colorId][$FabricColorId][$bodyparttypeId][$libYarnCountDeterId][$diaWidth][$uom])){
						$Amount[$this->_knit][$this->_finish][$jobNo][$fabricSourceId][$colorId][$FabricColorId][$bodyparttypeId][$libYarnCountDeterId][$diaWidth][$uom]+=$amount_finish;
					}
					else{
						$Amount[$this->_knit][$this->_finish][$jobNo][$fabricSourceId][$colorId][$FabricColorId][$bodyparttypeId][$libYarnCountDeterId][$diaWidth][$uom]=$amount_finish;
					}
					if(isset($Amount[$this->_knit][$this->_grey][$jobNo][$fabricSourceId][$colorId][$FabricColorId][$bodyparttypeId][$libYarnCountDeterId][$diaWidth][$uom])){
						$Amount[$this->_knit][$this->_grey][$jobNo][$fabricSourceId][$colorId][$FabricColorId][$bodyparttypeId][$libYarnCountDeterId][$diaWidth][$uom]+=$amount_grey;
					}
					else{
						$Amount[$this->_knit][$this->_grey][$jobNo][$fabricSourceId][$colorId][$FabricColorId][$bodyparttypeId][$libYarnCountDeterId][$diaWidth][$uom]=$amount_grey;
					}
				}
				elseif($fabNatureId==3){
					if(isset($Amount[$this->_woven][$this->_finish][$jobNo][$fabricSourceId][$colorId][$FabricColorId][$bodyparttypeId][$libYarnCountDeterId][$diaWidth][$uom])){
						$Amount[$this->_woven][$this->_finish][$jobNo][$fabricSourceId][$colorId][$FabricColorId][$bodyparttypeId][$libYarnCountDeterId][$diaWidth][$uom]+=$amount_finish;
					}
					else{
						$Amount[$this->_woven][$this->_finish][$jobNo][$fabricSourceId][$colorId][$FabricColorId][$bodyparttypeId][$libYarnCountDeterId][$diaWidth][$uom]=$amount_finish;
					}
					if(isset($Amount[$this->_woven][$this->_grey][$jobNo][$fabricSourceId][$colorId][$FabricColorId][$bodyparttypeId][$libYarnCountDeterId][$diaWidth][$uom]))
					{
						$Amount[$this->_woven][$this->_grey][$jobNo][$fabricSourceId][$colorId][$FabricColorId][$bodyparttypeId][$libYarnCountDeterId][$diaWidth][$uom]+=$amount_grey;
					}
					else{
						$Amount[$this->_woven][$this->_grey][$jobNo][$fabricSourceId][$colorId][$FabricColorId][$bodyparttypeId][$libYarnCountDeterId][$diaWidth][$uom]=$amount_grey;
					}
				}
				elseif($fabNatureId==100){
					if(isset($Amount[$this->_sweater][$this->_finish][$jobNo][$fabricSourceId][$colorId][$FabricColorId][$bodyparttypeId][$libYarnCountDeterId][$diaWidth][$uom])){
						$Amount[$this->_sweater][$this->_finish][$jobNo][$fabricSourceId][$colorId][$FabricColorId][$bodyparttypeId][$libYarnCountDeterId][$diaWidth][$uom]+=$amount_finish;
					}
					else{
						$Amount[$this->_sweater][$this->_finish][$jobNo][$fabricSourceId][$colorId][$FabricColorId][$bodyparttypeId][$libYarnCountDeterId][$diaWidth][$uom]=$amount_finish;
					}
					if(isset($Amount[$this->_sweater][$this->_grey][$jobNo][$fabricSourceId][$colorId][$FabricColorId][$bodyparttypeId][$libYarnCountDeterId][$diaWidth][$uom]))
					{
						$Amount[$this->_sweater][$this->_grey][$jobNo][$fabricSourceId][$colorId][$FabricColorId][$bodyparttypeId][$libYarnCountDeterId][$diaWidth][$uom]+=$amount_grey;
					}
					else{
						$Amount[$this->_sweater][$this->_grey][$jobNo][$fabricSourceId][$colorId][$FabricColorId][$bodyparttypeId][$libYarnCountDeterId][$diaWidth][$uom]=$amount_grey;
					}
				}
			}
			elseif($level==$this->_By_OrderFabColorGsmDeterminIdAndDiaWidth){
				if($fabNatureId==2){
					if(isset($Amount[$this->_knit][$this->_finish][$poId][$FabGmtColorId][$gsmweight][$libYarnCountDeterId][$diaWidth][$uom])){
						$Amount[$this->_knit][$this->_finish][$poId][$FabGmtColorId][$gsmweight][$libYarnCountDeterId][$diaWidth][$uom]+=$amount_finish;
					}
					else{
						$Amount[$this->_knit][$this->_finish][$poId][$FabGmtColorId][$gsmweight][$libYarnCountDeterId][$diaWidth][$uom]=$amount_finish;
					}
					if(isset($Amount[$this->_knit][$this->_grey][$poId][$FabGmtColorId][$gsmweight][$libYarnCountDeterId][$diaWidth][$uom])){
						$Amount[$this->_knit][$this->_grey][$poId][$FabGmtColorId][$gsmweight][$libYarnCountDeterId][$diaWidth][$uom]+=$amount_grey;
					}
					else{
						$Amount[$this->_knit][$this->_grey][$poId][$FabGmtColorId][$gsmweight][$libYarnCountDeterId][$diaWidth][$uom]=$amount_grey;
					}
				}
				elseif($fabNatureId==3){
					if(isset($Amount[$this->_woven][$this->_finish][$poId][$FabGmtColorId][$gsmweight][$libYarnCountDeterId][$diaWidth][$uom])){
						$Amount[$this->_woven][$this->_finish][$poId][$FabGmtColorId][$gsmweight][$libYarnCountDeterId][$diaWidth][$uom]+=$amount_finish;
					}
					else{
						$Amount[$this->_woven][$this->_finish][$poId][$FabGmtColorId][$gsmweight][$libYarnCountDeterId][$diaWidth][$uom]=$amount_finish;
					}
					if(isset($Amount[$this->_woven][$this->_grey][$poId][$FabGmtColorId][$gsmweight][$libYarnCountDeterId][$diaWidth][$uom]))
					{
						$Amount[$this->_woven][$this->_grey][$poId][$FabGmtColorId][$gsmweight][$libYarnCountDeterId][$diaWidth][$uom]+=$amount_grey;
					}
					else{
						$Amount[$this->_woven][$this->_grey][$poId][$FabGmtColorId][$gsmweight][$libYarnCountDeterId][$diaWidth][$uom]=$amount_grey;
					}
				}
				elseif($fabNatureId==100){
					if(isset($Amount[$this->_sweater][$this->_finish][$poId][$FabGmtColorId][$gsmweight][$libYarnCountDeterId][$diaWidth][$uom])){
						$Amount[$this->_sweater][$this->_finish][$poId][$FabGmtColorId][$gsmweight][$libYarnCountDeterId][$diaWidth][$uom]+=$amount_finish;
					}
					else{
						$Amount[$this->_sweater][$this->_finish][$poId][$FabGmtColorId][$gsmweight][$libYarnCountDeterId][$diaWidth][$uom]=$amount_finish;
					}
					if(isset($Amount[$this->_sweater][$this->_grey][$poId][$FabGmtColorId][$gsmweight][$libYarnCountDeterId][$diaWidth][$uom]))
					{
						$Amount[$this->_sweater][$this->_grey][$poId][$FabGmtColorId][$gsmweight][$libYarnCountDeterId][$diaWidth][$uom]+=$amount_grey;
					}
					else{
						$Amount[$this->_sweater][$this->_grey][$poId][$FabGmtColorId][$gsmweight][$libYarnCountDeterId][$diaWidth][$uom]=$amount_grey;
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
				elseif($fabNatureId==100){
					if(isset($Amount[$this->_sweater][$this->_finish][$poId][$preCostDtlsId][$colorId][$diaWidth][$remarks][$uom])){
						$Amount[$this->_sweater][$this->_finish][$poId][$preCostDtlsId][$colorId][$diaWidth][$remarks][$uom]+=$amount_finish;
					}
					else{
						$Amount[$this->_sweater][$this->_finish][$poId][$preCostDtlsId][$colorId][$diaWidth][$remarks][$uom]=$amount_finish;
					}
					if(isset($Amount[$this->_sweater][$this->_grey][$poId][$preCostDtlsId][$colorId][$diaWidth][$remarks][$uom]))
					{
						$Amount[$this->_sweater][$this->_grey][$poId][$preCostDtlsId][$colorId][$diaWidth][$remarks][$uom]+=$amount_grey;
					}
					else{
						$Amount[$this->_sweater][$this->_grey][$poId][$preCostDtlsId][$colorId][$diaWidth][$remarks][$uom]=$amount_grey;
					}
				}
			}
			elseif($level==$this->_By_OrderFabriccostidGmtscolorDiaWidthAndRemarksSourcing){ //For Sourcing
				if($fabNatureId==2){
					if(isset($Amount[$this->_knit][$this->_finish][$poId][$preCostDtlsId][$colorId][$diaWidth][$remarks][$uom])){
						$Amount[$this->_knit][$this->_finish][$poId][$preCostDtlsId][$colorId][$diaWidth][$remarks][$uom]+=$sourcing_amount_finish;
					}
					else{
						$Amount[$this->_knit][$this->_finish][$poId][$preCostDtlsId][$colorId][$diaWidth][$remarks][$uom]=$sourcing_amount_finish;
					}
					if(isset($Amount[$this->_knit][$this->_grey][$poId][$preCostDtlsId][$colorId][$diaWidth][$remarks][$uom])){
						$Amount[$this->_knit][$this->_grey][$poId][$preCostDtlsId][$colorId][$diaWidth][$remarks][$uom]+=$sourcing_amount_grey;
					}
					else{
						$Amount[$this->_knit][$this->_grey][$poId][$preCostDtlsId][$colorId][$diaWidth][$remarks][$uom]=$sourcing_amount_grey;
					}
				}
				elseif($fabNatureId==3){
					if(isset($Amount[$this->_woven][$this->_finish][$poId][$preCostDtlsId][$colorId][$diaWidth][$remarks][$uom])){
						$Amount[$this->_woven][$this->_finish][$poId][$preCostDtlsId][$colorId][$diaWidth][$remarks][$uom]+=$sourcing_amount_finish;
					}
					else{
						$Amount[$this->_woven][$this->_finish][$poId][$preCostDtlsId][$colorId][$diaWidth][$remarks][$uom]=$sourcing_amount_finish;
					}
					if(isset($Amount[$this->_woven][$this->_grey][$poId][$preCostDtlsId][$colorId][$diaWidth][$remarks][$uom]))
					{
						$Amount[$this->_woven][$this->_grey][$poId][$preCostDtlsId][$colorId][$diaWidth][$remarks][$uom]+=$sourcing_amount_grey;
					}
					else{
						$Amount[$this->_woven][$this->_grey][$poId][$preCostDtlsId][$colorId][$diaWidth][$remarks][$uom]=$sourcing_amount_grey;
					}
				}
				elseif($fabNatureId==100){
					if(isset($Amount[$this->_sweater][$this->_finish][$poId][$preCostDtlsId][$colorId][$diaWidth][$remarks][$uom])){
						$Amount[$this->_sweater][$this->_finish][$poId][$preCostDtlsId][$colorId][$diaWidth][$remarks][$uom]+=$sourcing_amount_finish;
					}
					else{
						$Amount[$this->_sweater][$this->_finish][$poId][$preCostDtlsId][$colorId][$diaWidth][$remarks][$uom]=$sourcing_amount_finish;
					}
					if(isset($Amount[$this->_sweater][$this->_grey][$poId][$preCostDtlsId][$colorId][$diaWidth][$remarks][$uom]))
					{
						$Amount[$this->_sweater][$this->_grey][$poId][$preCostDtlsId][$colorId][$diaWidth][$remarks][$uom]+=$sourcing_amount_grey;
					}
					else{
						$Amount[$this->_sweater][$this->_grey][$poId][$preCostDtlsId][$colorId][$diaWidth][$remarks][$uom]=$sourcing_amount_grey;
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
				elseif($fabNatureId==100){
					if(isset($Amount[$this->_sweater][$this->_finish][$poId][$countryId][$preCostDtlsId][$uom])){
						$Amount[$this->_sweater][$this->_finish][$poId][$countryId][$preCostDtlsId][$uom]+=$amount_finish;
					}
					else{
						$Amount[$this->_sweater][$this->_finish][$poId][$countryId][$preCostDtlsId][$uom]=$amount_finish;
					}
					if(isset($Amount[$this->_sweater][$this->_grey][$poId][$countryId][$preCostDtlsId][$uom])){
						$Amount[$this->_sweater][$this->_grey][$poId][$countryId][$preCostDtlsId][$uom]+=$amount_grey;
					}
					else{
						$Amount[$this->_sweater][$this->_grey][$poId][$countryId][$preCostDtlsId][$uom]=$amount_grey;
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
				elseif($fabNatureId==100){
					if(isset($Amount[$this->_sweater][$this->_finish][$preCostDtlsId][$uom])){
						$Amount[$this->_sweater][$this->_finish][$preCostDtlsId][$uom]+=$amount_finish;
					}
					else{
						$Amount[$this->_sweater][$this->_finish][$preCostDtlsId][$uom]=$amount_finish;
					}
					if(isset($Amount[$this->_sweater][$this->_grey][$preCostDtlsId][$uom])){
						$Amount[$this->_sweater][$this->_grey][$preCostDtlsId][$uom]+=$amount_grey;
					}
					else{
						$Amount[$this->_sweater][$this->_grey][$preCostDtlsId][$uom]=$amount_grey;
					}
				}
			}
			elseif($level==$this->_By_FabriccostidSourcing){
				if($fabNatureId==2){
					if(isset($Amount[$this->_knit][$this->_finish][$preCostDtlsId][$uom])){
						$Amount[$this->_knit][$this->_finish][$preCostDtlsId][$uom]+=$sourcing_amount_finish;
					}
					else{
						$Amount[$this->_knit][$this->_finish][$preCostDtlsId][$uom]=$sourcing_amount_finish;
					}
					if(isset($Amount[$this->_knit][$this->_grey][$preCostDtlsId][$uom])){
						$Amount[$this->_knit][$this->_grey][$preCostDtlsId][$uom]+=$sourcing_amount_grey;
					}
					else{
						$Amount[$this->_knit][$this->_grey][$preCostDtlsId][$uom]=$sourcing_amount_grey;
					}
				}
				elseif($fabNatureId==3){
					if(isset($Amount[$this->_woven][$this->_finish][$preCostDtlsId][$uom])){
						$Amount[$this->_woven][$this->_finish][$preCostDtlsId][$uom]+=$sourcing_amount_finish;
					}
					else{
						$Amount[$this->_woven][$this->_finish][$preCostDtlsId][$uom]=$sourcing_amount_finish;
					}
					if(isset($Amount[$this->_woven][$this->_grey][$preCostDtlsId][$uom])){
						$Amount[$this->_woven][$this->_grey][$preCostDtlsId][$uom]+=$sourcing_amount_grey;
					}
					else{
						$Amount[$this->_woven][$this->_grey][$preCostDtlsId][$uom]=$sourcing_amount_grey;
					}
				}
				elseif($fabNatureId==100){
					if(isset($Amount[$this->_sweater][$this->_finish][$preCostDtlsId][$uom])){
						$Amount[$this->_sweater][$this->_finish][$preCostDtlsId][$uom]+=$sourcing_amount_finish;
					}
					else{
						$Amount[$this->_sweater][$this->_finish][$preCostDtlsId][$uom]=$sourcing_amount_finish;
					}
					if(isset($Amount[$this->_sweater][$this->_grey][$preCostDtlsId][$uom])){
						$Amount[$this->_sweater][$this->_grey][$preCostDtlsId][$uom]+=$sourcing_amount_grey;
					}
					else{
						$Amount[$this->_sweater][$this->_grey][$preCostDtlsId][$uom]=$sourcing_amount_grey;
					}
				}
			}
			elseif($level==$this->_By_OrderAndFabricSource){
				if($fabNatureId==2){
					if(isset($Amount[$this->_knit][$this->_finish][$poId][$fabric_source][$uom])){
						$Amount[$this->_knit][$this->_finish][$poId][$fabric_source][$uom]+=$amount_finish;
					}
					else{
						$Amount[$this->_knit][$this->_finish][$poId][$fabric_source][$uom]=$amount_finish;
					}
					if(isset($Amount[$this->_knit][$this->_grey][$poId][$fabric_source][$uom])){
						$Amount[$this->_knit][$this->_grey][$poId][$fabric_source][$uom]+=$amount_grey;
					}
					else{
						$Amount[$this->_knit][$this->_grey][$poId][$fabric_source][$uom]=$amount_grey;
					}
				}
				elseif($fabNatureId==3){
					if(isset($Amount[$this->_woven][$this->_finish][$poId][$fabric_source][$uom])){
						$Amount[$this->_woven][$this->_finish][$poId][$fabric_source][$uom]+=$amount_finish;
					}
					else{
						$Amount[$this->_woven][$this->_finish][$poId][$fabric_source][$uom]=$amount_finish;
					}
					if(isset($Amount[$this->_woven][$this->_grey][$poId][$fabric_source][$uom])){
						$Amount[$this->_woven][$this->_grey][$poId][$fabric_source][$uom]+=$amount_grey;
					}
					else{
						$Amount[$this->_woven][$this->_grey][$poId][$fabric_source][$uom]=$amount_grey;
					}
				}
				elseif($fabNatureId==100){
					if(isset($Amount[$this->_sweater][$this->_finish][$poId][$fabric_source][$uom])){
						$Amount[$this->_sweater][$this->_finish][$poId][$fabric_source][$uom]+=$amount_finish;
					}
					else{
						$Amount[$this->_sweater][$this->_finish][$poId][$fabric_source][$uom]=$amount_finish;
					}
					if(isset($Amount[$this->_sweater][$this->_grey][$poId][$fabric_source][$uom])){
						$Amount[$this->_sweater][$this->_grey][$poId][$fabric_source][$uom]+=$amount_grey;
					}
					else{
						$Amount[$this->_sweater][$this->_grey][$poId][$fabric_source][$uom]=$amount_grey;
					}
				}
			}
			elseif($level==$this->_By_OrderAndColorTypeFabricSource){
				if($fabNatureId==2){
					if(isset($Amount[$this->_knit][$this->_finish][$poId][$colorTypeId][$fabric_source][$uom])){
						$Amount[$this->_knit][$this->_finish][$poId][$colorTypeId][$fabric_source][$uom]+=$amount_finish;
					}
					else{
						$Amount[$this->_knit][$this->_finish][$poId][$colorTypeId][$fabric_source][$uom]=$amount_finish;
					}
					if(isset($Amount[$this->_knit][$this->_grey][$poId][$colorTypeId][$fabric_source][$uom])){
						$Amount[$this->_knit][$this->_grey][$poId][$colorTypeId][$fabric_source][$uom]+=$amount_grey;
					}
					else{
						$Amount[$this->_knit][$this->_grey][$poId][$colorTypeId][$fabric_source][$uom]=$amount_grey;
					}
				}
				elseif($fabNatureId==3){
					if(isset($Amount[$this->_woven][$this->_finish][$poId][$colorTypeId][$fabric_source][$uom])){
						$Amount[$this->_woven][$this->_finish][$poId][$colorTypeId][$fabric_source][$uom]+=$amount_finish;
					}
					else{
						$Amount[$this->_woven][$this->_finish][$poId][$colorTypeId][$fabric_source][$uom]=$amount_finish;
					}
					if(isset($Amount[$this->_woven][$this->_grey][$poId][$colorTypeId][$fabric_source][$uom])){
						$Amount[$this->_woven][$this->_grey][$poId][$colorTypeId][$fabric_source][$uom]+=$amount_grey;
					}
					else{
						$Amount[$this->_woven][$this->_grey][$poId][$colorTypeId][$fabric_source][$uom]=$amount_grey;
					}
				}
				elseif($fabNatureId==100){
					if(isset($Amount[$this->_sweater][$this->_finish][$poId][$colorTypeId][$fabric_source][$uom])){
						$Amount[$this->_sweater][$this->_finish][$poId][$colorTypeId][$fabric_source][$uom]+=$amount_finish;
					}
					else{
						$Amount[$this->_sweater][$this->_finish][$poId][$colorTypeId][$fabric_source][$uom]=$amount_finish;
					}
					if(isset($Amount[$this->_sweater][$this->_grey][$poId][$colorTypeId][$fabric_source][$uom])){
						$Amount[$this->_sweater][$this->_grey][$poId][$colorTypeId][$fabric_source][$uom]+=$amount_grey;
					}
					else{
						$Amount[$this->_sweater][$this->_grey][$poId][$colorTypeId][$fabric_source][$uom]=$amount_grey;
					}
				}
			}
			elseif($level==$this->_By_OrderAndSourceId){
				if($fabNatureId==2){
					if(isset($Amount[$this->_knit][$this->_finish][$poId][$sourceId][$uom])){
						$Amount[$this->_knit][$this->_finish][$poId][$sourceId][$uom]+=$amount_finish;
					}
					else{
						$Amount[$this->_knit][$this->_finish][$poId][$sourceId][$uom]=$amount_finish;
					}
					if(isset($Amount[$this->_knit][$this->_grey][$poId][$sourceId][$uom])){
						$Amount[$this->_knit][$this->_grey][$poId][$sourceId][$uom]+=$amount_grey;
					}
					else{
						$Amount[$this->_knit][$this->_grey][$poId][$sourceId][$uom]=$amount_grey;
					}
				}
				elseif($fabNatureId==3){
					if(isset($Amount[$this->_woven][$this->_finish][$poId][$sourceId][$uom])){
						$Amount[$this->_woven][$this->_finish][$poId][$sourceId][$uom]+=$amount_finish;
					}
					else{
						$Amount[$this->_woven][$this->_finish][$poId][$sourceId][$uom]=$amount_finish;
					}
					if(isset($Amount[$this->_woven][$this->_grey][$poId][$sourceId][$uom])){
						$Amount[$this->_woven][$this->_grey][$poId][$sourceId][$uom]+=$amount_grey;
					}
					else{
						$Amount[$this->_woven][$this->_grey][$poId][$sourceId][$uom]=$amount_grey;
					}
				}
				elseif($fabNatureId==100){
					if(isset($Amount[$this->_sweater][$this->_finish][$poId][$sourceId][$uom])){
						$Amount[$this->_sweater][$this->_finish][$poId][$sourceId][$uom]+=$amount_finish;
					}
					else{
						$Amount[$this->_sweater][$this->_finish][$poId][$sourceId][$uom]=$amount_finish;
					}
					if(isset($Amount[$this->_sweater][$this->_grey][$poId][$sourceId][$uom])){
						$Amount[$this->_sweater][$this->_grey][$poId][$sourceId][$uom]+=$amount_grey;
					}
					else{
						$Amount[$this->_sweater][$this->_grey][$poId][$sourceId][$uom]=$amount_grey;
					}
				}
			}
			
			elseif($level==$this->_By_JobIdLibYarnCountDeterIdAndGmtscolor){
				if($fabNatureId==2){
					if(isset($Qty[$this->_knit][$this->_finish][$jobId][$libYarnCountDeterId][$colorId][$FabGmtColorId])){
						$Amount[$this->_knit][$this->_finish][$jobId][$libYarnCountDeterId][$colorId][$FabGmtColorId]+=$amount_finish;
					}
					else{
						$Amount[$this->_knit][$this->_finish][$jobId][$libYarnCountDeterId][$colorId][$FabGmtColorId]+=$amount_finish;
					}
					if(isset($Qty[$this->_knit][$this->_grey][$jobId][$libYarnCountDeterId][$colorId][$FabGmtColorId]))
					{
						$Amount[$this->_knit][$this->_grey][$jobId][$libYarnCountDeterId][$colorId][$FabGmtColorId]+=$amount_grey;
					}
					else{
						$Amount[$this->_knit][$this->_grey][$jobId][$libYarnCountDeterId][$colorId][$FabGmtColorId]+=$amount_grey;
					}
				}
				if($fabNatureId==3){
					if(isset($Qty[$this->_woven][$this->_finish][$jobId][$libYarnCountDeterId][$colorId][$FabGmtColorId])){
						$Amount[$this->_woven][$this->_finish][$jobId][$libYarnCountDeterId][$colorId][$FabGmtColorId]+=$amount_finish;
					}
					else{
						$Amount[$this->_woven][$this->_finish][$jobId][$libYarnCountDeterId][$colorId][$FabGmtColorId]+=$amount_finish;
					}
					if(isset($Qty[$this->_woven][$this->_grey][$jobId][$libYarnCountDeterId][$colorId][$FabGmtColorId]))
					{
						$Amount[$this->_woven][$this->_grey][$jobId][$libYarnCountDeterId][$colorId][$FabGmtColorId]+=$amount_grey;
					}
					else{
						$Amount[$this->_woven][$this->_grey][$jobId][$libYarnCountDeterId][$colorId][$FabGmtColorId]+=$amount_grey;
					}
				}
			}
			elseif($level==$this->_By_JobIdYarnCountIdGmtsAndFabricColor){
				if($fabNatureId==3){
					if(isset($Qty[$this->_woven][$this->_finish][$jobId][$libYarnCountDeterId][$colorId][$FabGmtColorId])){
						$Amount[$this->_woven][$this->_finish][$jobId][$libYarnCountDeterId][$colorId][$FabGmtColorId]+=$sourcing_amount_finish;
					}
					else{
						$Amount[$this->_woven][$this->_finish][$jobId][$libYarnCountDeterId][$colorId][$FabGmtColorId]+=$sourcing_amount_finish;
					}
					if(isset($Qty[$this->_woven][$this->_grey][$jobId][$libYarnCountDeterId][$colorId][$FabGmtColorId]))
					{
						$Amount[$this->_woven][$this->_grey][$jobId][$libYarnCountDeterId][$colorId][$FabGmtColorId]+=$sourcing_amount_grey;
					}
					else{
						$Amount[$this->_woven][$this->_grey][$jobId][$libYarnCountDeterId][$colorId][$FabGmtColorId]+=$sourcing_amount_grey;
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
		$bodypartId='';$isConfirmed='';
		$requirment=0;$sourceId='';
		$poId='';$nominated_supp='';
		$budget_on=2;$FabGmtColorId='';
		
		$Qty=array();

		foreach($this->_dataArray as $row)
		{
			$jobNo=$row['job_no'];
			$poId=$row['id'];
			$nominated_supp=$row['nominated_supp'];
			$isConfirmed=$row['is_confirmed'];
			$itemNumberId=$row['item_number_id'];
			$sourceId=$row['source_id'];
			$colorId=$row['color_number_id'];
			$FabricColorId=$row['contrast_color_id'];
			$sizeId=$row['size_number_id'];
			$countryId=$row['country_id'];
			$planPutQnty=$row['plan_cut_qnty'];
			$orderQuantity=$row['order_quantity'];
			$diaWidth=$row['dia_width'];
			if($FabricColorId=='')
			{
				$FabGmtColorId=$colorId;
			}
			else $FabGmtColorId=$FabricColorId;
			
			
			
			$bodypartId=$row['body_part_id'];
			$fabNatureId=$row['fab_nature_id'];
			$fabricSourceId=$row['fabric_source'];
			$countryShipDate=$row['country_ship_date'];
			$widthDiaType=$row['width_dia_type'];
			$dia_width=$row['dia_width'];
			
			$cons=$row['cons'];
			$requirment=$row['requirment'];
			$uom=$row['uom'];
			$budget_on=$row['budget_on'];
			if($budget_on==0 || $budget_on=="") $budget_on=2;
			$costingPerQty=$this->_costingPerQtyArr[$jobNo];
			$set_item_ratio=$this->_gmtsitemRatioArray[$jobNo][$itemNumberId];
			if($budget_on==2)
			{
				$reqqnty_finish =$this->_calculateQty($planPutQnty,$costingPerQty,$set_item_ratio,$cons);
				$reqqnty_grey =$this->_calculateQty($planPutQnty,$costingPerQty,$set_item_ratio,$requirment);
			}
			if($budget_on==1)
			{
				$reqqnty_finish =$this->_calculateQty($orderQuantity,$costingPerQty,$set_item_ratio,$cons);
				$reqqnty_grey =$this->_calculateQty($orderQuantity,$costingPerQty,$set_item_ratio,$requirment);
			}
			//$reqqnty_finish =$this->_calculateQty($planPutQnty,$costingPerQty,$set_item_ratio,$cons);
			//$reqqnty_grey =$this->_calculateQty($planPutQnty,$costingPerQty,$set_item_ratio,$requirment);
			
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
				elseif($fabNatureId==100 && $fabricSourceId==1){
					if(isset($Qty[$this->_sweater][$this->_finish][$jobNo][$uom])){
						$Qty[$this->_sweater][$this->_finish][$jobNo][$uom]+=$reqqnty_finish;
					}
					else{
						$Qty[$this->_sweater][$this->_finish][$jobNo][$uom]=$reqqnty_finish;
					}
					if(isset($Qty[$this->_sweater][$this->_grey][$jobNo][$uom])){
						$Qty[$this->_sweater][$this->_grey][$jobNo][$uom]+=$reqqnty_grey;
					}
					else{
						$Qty[$this->_sweater][$this->_grey][$jobNo][$uom]=$reqqnty_grey;
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
				elseif($fabNatureId==100 && $fabricSourceId==1){
					if(isset($Qty[$this->_sweater][$this->_finish][$poId][$uom])){
						$Qty[$this->_sweater][$this->_finish][$poId][$uom]+=$reqqnty_finish;
					}
					else{
						$Qty[$this->_sweater][$this->_finish][$poId][$uom]=$reqqnty_finish;
					}
					if(isset($Qty[$this->_sweater][$this->_grey][$poId][$uom])){
						$Qty[$this->_sweater][$this->_grey][$poId][$uom]+=$reqqnty_grey;
					}
					else{
						$Qty[$this->_sweater][$this->_grey][$poId][$uom]=$reqqnty_grey;
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
				elseif($fabNatureId==100 && $fabricSourceId==1){
					if(isset($Qty[$this->_sweater][$this->_finish][$poId][$countryId][$uom])){
						$Qty[$this->_sweater][$this->_finish][$poId][$countryId][$uom]+=$reqqnty_finish;
					}
					else{
						$Qty[$this->_sweater][$this->_finish][$poId][$countryId][$uom]=$reqqnty_finish;
					}
					if(isset($Qty[$this->_sweater][$this->_grey][$poId][$countryId][$uom])){
						$Qty[$this->_sweater][$this->_grey][$poId][$countryId][$uom]+=$reqqnty_grey;
					}
					else{
						$Qty[$this->_sweater][$this->_grey][$poId][$countryId][$uom]=$reqqnty_grey;
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
				elseif($fabNatureId==100 && $fabricSourceId==1){
					if(isset($Qty[$this->_sweater][$this->_finish][$poId][$itemNumberId][$uom])){
						$Qty[$this->_sweater][$this->_finish][$poId][$itemNumberId][$uom]+=$reqqnty_finish;
					}
					else{
						$Qty[$this->_sweater][$this->_finish][$poId][$itemNumberId][$uom]+=$reqqnty_finish;
					}
					if(isset($Qty[$this->_sweater][$this->_grey][$poId][$itemNumberId][$uom])){
						$Qty[$this->_sweater][$this->_grey][$poId][$itemNumberId][$uom]+=$reqqnty_grey;
					}
					else{
						$Qty[$this->_sweater][$this->_grey][$poId][$itemNumberId][$uom]=$reqqnty_grey;
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
				elseif($fabNatureId==100 && $fabricSourceId==1){
					if(isset($Qty[$this->_sweater][$this->_finish][$poId][$colorId][$uom])){
						$Qty[$this->_sweater][$this->_finish][$poId][$colorId][$uom]+=$reqqnty_finish;
					}
					else{
						$Qty[$this->_sweater][$this->_finish][$poId][$colorId][$uom]=$reqqnty_finish;
					}
					if(isset($Qty[$this->_sweater][$this->_finish][$poId][$colorId][$uom])){
						$Qty[$this->_sweater][$this->_grey][$poId][$colorId][$uom]+=$reqqnty_grey;
					}
					else{
						$Qty[$this->_sweater][$this->_grey][$poId][$colorId][$uom]=$reqqnty_grey;
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
				elseif($fabNatureId==100 && $fabricSourceId==1){
					if(isset($Qty[$this->_sweater][$this->_finish][$poId][$sizeId][$uom])){
						$Qty[$this->_sweater][$this->_finish][$poId][$sizeId][$uom]+=$reqqnty_finish;
					}
					else{
						$Qty[$this->_sweater][$this->_finish][$poId][$sizeId][$uom]=$reqqnty_finish;
					}
					if(isset($Qty[$this->_sweater][$this->_grey][$poId][$sizeId][$uom])){
						$Qty[$this->_sweater][$this->_grey][$poId][$sizeId][$uom]+=$reqqnty_grey;
					}
					else{
						$Qty[$this->_sweater][$this->_grey][$poId][$sizeId][$uom]=$reqqnty_grey;
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
				elseif($fabNatureId==100 && $fabricSourceId==1){
					if(isset($Qty[$this->_sweater][$this->_finish][$poId][$countryId][$itemNumberId][$uom])){
						$Qty[$this->_sweater][$this->_finish][$poId][$countryId][$itemNumberId][$uom]+=$reqqnty_finish;
					}
					else{
						$Qty[$this->_sweater][$this->_finish][$poId][$countryId][$itemNumberId][$uom]=$reqqnty_finish;
					}
					if(isset($Qty[$this->_sweater][$this->_grey][$poId][$countryId][$itemNumberId][$uom])){
						$Qty[$this->_sweater][$this->_grey][$poId][$countryId][$itemNumberId][$uom]+=$reqqnty_grey;
					}
					else{
						$Qty[$this->_sweater][$this->_grey][$poId][$countryId][$itemNumberId][$uom]=$reqqnty_grey;
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
				elseif($fabNatureId==100 && $fabricSourceId==1){
					if(isset($Qty[$this->_sweater][$this->_finish][$poId][$countryId][$colorId][$uom])){
						$Qty[$this->_sweater][$this->_finish][$poId][$countryId][$colorId][$uom]+=$reqqnty_finish;
					}
					else{
						$Qty[$this->_sweater][$this->_finish][$poId][$countryId][$colorId][$uom]=$reqqnty_finish;
					}
					if(isset($Qty[$this->_sweater][$this->_grey][$poId][$countryId][$colorId][$uom])){
						$Qty[$this->_sweater][$this->_grey][$poId][$countryId][$colorId][$uom]+=$reqqnty_grey;
					}
					else{
						$Qty[$this->_sweater][$this->_grey][$poId][$countryId][$colorId][$uom]=$reqqnty_grey;
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
				elseif($fabNatureId==100 && $fabricSourceId==1){
					if(isset($Qty[$this->_sweater][$this->_finish][$poId][$countryId][$sizeId][$uom])){
						$Qty[$this->_sweater][$this->_finish][$poId][$countryId][$sizeId][$uom]+=$reqqnty_finish;
					}
					else{
						$Qty[$this->_sweater][$this->_finish][$poId][$countryId][$sizeId][$uom]=$reqqnty_finish;
					}
					if(isset($Qty[$this->_sweater][$this->_grey][$poId][$countryId][$sizeId][$uom])){
						$Qty[$this->_sweater][$this->_grey][$poId][$countryId][$sizeId][$uom]+=$reqqnty_grey;
					}
					else{
						$Qty[$this->_sweater][$this->_grey][$poId][$countryId][$sizeId][$uom]=$reqqnty_grey;
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
				elseif($fabNatureId==100 && $fabricSourceId==1){
					if(isset($Qty[$this->_sweater][$this->_finish][$poId][$itemNumberId][$colorId][$uom])){
						$Qty[$this->_sweater][$this->_finish][$poId][$itemNumberId][$colorId][$uom]+=$reqqnty_finish;
					}
					else{
						$Qty[$this->_sweater][$this->_finish][$poId][$itemNumberId][$colorId][$uom]=$reqqnty_finish;
					}
					if(isset($Qty[$this->_sweater][$this->_grey][$poId][$itemNumberId][$colorId][$uom])){
						$Qty[$this->_sweater][$this->_grey][$poId][$itemNumberId][$colorId][$uom]+=$reqqnty_grey;
					}
					else{
						$Qty[$this->_sweater][$this->_grey][$poId][$itemNumberId][$colorId][$uom]=$reqqnty_grey;
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
				elseif($fabNatureId==100 && $fabricSourceId==1){
					if(isset($Qty[$this->_sweater][$this->_finish][$poId][$itemNumberId][$sizeId][$uom])){
						$Qty[$this->_sweater][$this->_finish][$poId][$itemNumberId][$sizeId][$uom]+=$reqqnty_finish;
					}
					else{
						$Qty[$this->_sweater][$this->_finish][$poId][$itemNumberId][$sizeId][$uom]=$reqqnty_finish;
					}
					if(isset($Qty[$this->_sweater][$this->_grey][$poId][$itemNumberId][$sizeId][$uom])){
						$Qty[$this->_sweater][$this->_grey][$poId][$itemNumberId][$sizeId][$uom]+=$reqqnty_grey;
					}
					else{
						$Qty[$this->_sweater][$this->_grey][$poId][$itemNumberId][$sizeId][$uom]=$reqqnty_grey;
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
				elseif($fabNatureId==100 && $fabricSourceId==1){
					if(isset($Qty[$this->_sweater][$this->_finish][$poId][$colorId][$sizeId][$uom])){
						$Qty[$this->_sweater][$this->_finish][$poId][$colorId][$sizeId][$uom]+=$reqqnty_finish;
					}
					else{
						$Qty[$this->_sweater][$this->_finish][$poId][$colorId][$sizeId][$uom]=$reqqnty_finish;
					}
					if(isset($Qty[$this->_sweater][$this->_grey][$poId][$colorId][$sizeId][$uom]))
					{
						$Qty[$this->_sweater][$this->_grey][$poId][$colorId][$sizeId][$uom]+=$reqqnty_grey;
					}
					else{
						$Qty[$this->_sweater][$this->_grey][$poId][$colorId][$sizeId][$uom]=$reqqnty_grey;
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
				elseif($fabNatureId==100 && $fabricSourceId==1){
					if(isset($Qty[$this->_sweater][$this->_finish][$poId][$colorId][$bodypartId][$uom])){
						$Qty[$this->_sweater][$this->_finish][$poId][$colorId][$bodypartId][$uom]+=$reqqnty_finish;
					}
					else{
						$Qty[$this->_sweater][$this->_finish][$poId][$colorId][$bodypartId][$uom]=$reqqnty_finish;
					}
					if(isset($Qty[$this->_sweater][$this->_grey][$poId][$colorId][$bodypartId][$uom]))
					{
						$Qty[$this->_sweater][$this->_grey][$poId][$colorId][$bodypartId][$uom]+=$reqqnty_grey;
					}
					else{
						$Qty[$this->_sweater][$this->_grey][$poId][$colorId][$bodypartId][$uom]=$reqqnty_grey;
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
				elseif($fabNatureId==100 && $fabricSourceId==1){
					if(isset($Qty[$this->_sweater][$this->_finish][$poId][$countryId][$itemNumberId][$colorId][$uom])){
						$Qty[$this->_sweater][$this->_finish][$poId][$countryId][$itemNumberId][$colorId][$uom]+=$reqqnty_finish;
					}
					else{
						$Qty[$this->_sweater][$this->_finish][$poId][$countryId][$itemNumberId][$colorId][$uom]=$reqqnty_finish;
					}
					if(isset($Qty[$this->_sweater][$this->_grey][$poId][$countryId][$itemNumberId][$colorId][$uom])){
						$Qty[$this->_sweater][$this->_grey][$poId][$countryId][$itemNumberId][$colorId][$uom]+=$reqqnty_grey;
					}
					else{
						$Qty[$this->_sweater][$this->_grey][$poId][$countryId][$itemNumberId][$colorId][$uom]=$reqqnty_grey;
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
				elseif($fabNatureId==100 && $fabricSourceId==1){
					if(isset($Qty[$this->_sweater][$this->_finish][$poId][$countryId][$itemNumberId][$sizeId][$uom])){
						$Qty[$this->_sweater][$this->_finish][$poId][$countryId][$itemNumberId][$sizeId][$uom]+=$reqqnty_finish;
					}
					else{
						$Qty[$this->_sweater][$this->_finish][$poId][$countryId][$itemNumberId][$sizeId][$uom]=$reqqnty_finish;
					}
					if(isset($Qty[$this->_sweater][$this->_grey][$poId][$countryId][$itemNumberId][$sizeId][$uom])){
						$Qty[$this->_sweater][$this->_grey][$poId][$countryId][$itemNumberId][$sizeId][$uom]+=$reqqnty_grey;
					}
					else{
						$Qty[$this->_sweater][$this->_grey][$poId][$countryId][$itemNumberId][$sizeId][$uom]=$reqqnty_grey;
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
				elseif($fabNatureId==100 && $fabricSourceId==1){
					if(isset($Qty[$this->_sweater][$this->_finish][$poId][$countryId][$colorId][$sizeId][$uom])){
						$Qty[$this->_sweater][$this->_finish][$poId][$countryId][$colorId][$sizeId][$uom]+=$reqqnty_finish;
					}
					else{
						$Qty[$this->_sweater][$this->_finish][$poId][$countryId][$colorId][$sizeId][$uom]=$reqqnty_finish;
					}
					if(isset($Qty[$this->_sweater][$this->_grey][$poId][$countryId][$colorId][$sizeId][$uom])){
						$Qty[$this->_sweater][$this->_grey][$poId][$countryId][$colorId][$sizeId][$uom]+=$reqqnty_grey;
					}
					else{
						$Qty[$this->_sweater][$this->_grey][$poId][$countryId][$colorId][$sizeId][$uom]=$reqqnty_grey;
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
				elseif($fabNatureId==100 && $fabricSourceId==1){
					if(isset($Qty[$this->_sweater][$this->_finish][$poId][$itemNumberId][$colorId][$sizeId][$uom])){
						$Qty[$this->_sweater][$this->_finish][$poId][$itemNumberId][$colorId][$sizeId][$uom]+=$reqqnty_finish;
					}
					else{
						$Qty[$this->_sweater][$this->_finish][$poId][$itemNumberId][$colorId][$sizeId][$uom]=$reqqnty_finish;
					}
					if(isset($Qty[$this->_sweater][$this->_grey][$poId][$itemNumberId][$colorId][$sizeId][$uom])){
						$Qty[$this->_sweater][$this->_grey][$poId][$itemNumberId][$colorId][$sizeId][$uom]+=$reqqnty_grey;
					}
					else{
						$Qty[$this->_sweater][$this->_grey][$poId][$itemNumberId][$colorId][$sizeId][$uom]=$reqqnty_grey;
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
				elseif($fabNatureId==100 && $fabricSourceId==1){
					if(isset($Qty[$this->_sweater][$this->_finish][$poId][$countryId][$itemNumberId][$colorId][$sizeId][$uom])){
						$Qty[$this->_sweater][$this->_finish][$poId][$countryId][$itemNumberId][$colorId][$sizeId][$uom]+=$reqqnty_finish;
					}
					else{
						$Qty[$this->_sweater][$this->_finish][$poId][$countryId][$itemNumberId][$colorId][$sizeId][$uom]=$reqqnty_finish;
					}
					if(isset($Qty[$this->_sweater][$this->_grey][$poId][$countryId][$itemNumberId][$colorId][$sizeId][$uom])){
						$Qty[$this->_sweater][$this->_grey][$poId][$countryId][$itemNumberId][$colorId][$sizeId][$uom]+=$reqqnty_grey;
					}
					else{
						$Qty[$this->_sweater][$this->_grey][$poId][$countryId][$itemNumberId][$colorId][$sizeId][$uom]=$reqqnty_grey;
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
		$bodypartId='';$isConfirmed='';
		$requirment=0;
		$poId='';$nominated_supp='';$sourceId='';
		$dia_width='';$FabGmtColorId='';
		$budget_on=2;
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
			$nominated_supp=$row['nominated_supp'];
			$isConfirmed=$row['is_confirmed'];
			$itemNumberId=$row['item_number_id'];
			$sourceId=$row['source_id'];
			$colorId=$row['color_number_id'];
			$FabricColorId=$row['contrast_color_id'];
			$sizeId=$row['size_number_id'];
			$countryId=$row['country_id'];
			$planPutQnty=$row['plan_cut_qnty'];
			$orderQuantity=$row['order_quantity'];
			$countryShipDate=$row['country_ship_date'];
			$widthDiaType=$row['width_dia_type'];
			$diaWidth=$row['dia_width'];
			if($FabricColorId=='')
			{
				$FabGmtColorId=$colorId;
			}
			else $FabGmtColorId=$FabricColorId;
			
			$bodypartId=$row['body_part_id'];
			$preCostDtlsId=$row['pre_cost_dtls_id'];
			$libYarnCountDeterId=$row['lib_yarn_count_deter_id'];
			$fabNatureId=$row['fab_nature_id'];
			$dia_width=$row['dia_width'];
			$fabricSourceId=$row['fabric_source'];
			$cons=$row['cons'];
			$requirment=$row['requirment'];
			$uom=$row['uom'];
			$budget_on=$row['budget_on'];
			if($budget_on==0 || $budget_on=="") $budget_on=2;
			//$costingPerQty=$this->_costingPer($costingPerArray[$jobNo]);
			//$set_item_ratio=$gmtsitemRatioArray[$jobNo][$itemNumberId];
			//$costingPerQty=$this->_costingPer($this->_costingPerArr[$jobNo]);
			$costingPerQty=$this->_costingPerQtyArr[$jobNo];
			$set_item_ratio=$this->_gmtsitemRatioArray[$jobNo][$itemNumberId];
			
			//$reqqnty_finish =$this->_calculateQty($planPutQnty,$costingPerQty,$set_item_ratio,$cons);
			//$reqqnty_grey =$this->_calculateQty($planPutQnty,$costingPerQty,$set_item_ratio,$requirment);
			if($budget_on==2)
			{
				$reqqnty_finish =$this->_calculateQty($planPutQnty,$costingPerQty,$set_item_ratio,$cons);
				$reqqnty_grey =$this->_calculateQty($planPutQnty,$costingPerQty,$set_item_ratio,$requirment);
			}
			if($budget_on==1)
			{
				$reqqnty_finish =$this->_calculateQty($orderQuantity,$costingPerQty,$set_item_ratio,$cons);
				$reqqnty_grey =$this->_calculateQty($orderQuantity,$costingPerQty,$set_item_ratio,$requirment);
			}
			
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
				elseif($fabNatureId==100 && $fabricSourceId==2){
					if(isset($Qty[$this->_sweater][$this->_finish][$jobNo][$uom])){
						$Qty[$this->_sweater][$this->_finish][$jobNo][$uom]+=$reqqnty_finish;
					}
					else{
						$Qty[$this->_sweater][$this->_finish][$jobNo][$uom]=$reqqnty_finish;
					}
					if(isset($Qty[$this->_sweater][$this->_grey][$jobNo][$uom])){
						$Qty[$this->_sweater][$this->_grey][$jobNo][$uom]+=$reqqnty_grey;
					}
					else{
						$Qty[$this->_sweater][$this->_grey][$jobNo][$uom]=$reqqnty_grey;
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
				elseif($fabNatureId==100 && $fabricSourceId==2){
					if(isset($Qty[$this->_sweater][$this->_finish][$poId][$uom])){
						$Qty[$this->_sweater][$this->_finish][$poId][$uom]+=$reqqnty_finish;
					}
					else{
						$Qty[$this->_sweater][$this->_finish][$poId][$uom]=$reqqnty_finish;
					}
					if(isset($Qty[$this->_sweater][$this->_grey][$poId][$uom])){
						$Qty[$this->_sweater][$this->_grey][$poId][$uom]+=$reqqnty_grey;
					}
					else{
						$Qty[$this->_sweater][$this->_grey][$poId][$uom]=$reqqnty_grey;
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
				elseif($fabNatureId==100 && $fabricSourceId==2){
					if(isset($Qty[$this->_sweater][$this->_finish][$poId][$countryId][$uom])){
						$Qty[$this->_sweater][$this->_finish][$poId][$countryId][$uom]+=$reqqnty_finish;
					}
					else{
						$Qty[$this->_sweater][$this->_finish][$poId][$countryId][$uom]=$reqqnty_finish;
					}
					if(isset($Qty[$this->_sweater][$this->_grey][$poId][$countryId][$uom])){
						$Qty[$this->_sweater][$this->_grey][$poId][$countryId][$uom]+=$reqqnty_grey;
					}
					else{
						$Qty[$this->_sweater][$this->_grey][$poId][$countryId][$uom]=$reqqnty_grey;
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
				elseif($fabNatureId==100 && $fabricSourceId==2){
					if(isset($Qty[$this->_sweater][$this->_finish][$poId][$itemNumberId][$uom])){
						$Qty[$this->_sweater][$this->_finish][$poId][$itemNumberId][$uom]+=$reqqnty_finish;
					}
					else{

						$Qty[$this->_sweater][$this->_finish][$poId][$itemNumberId][$uom]+=$reqqnty_finish;
					}
					if(isset($Qty[$this->_sweater][$this->_grey][$poId][$itemNumberId][$uom])){
						$Qty[$this->_sweater][$this->_grey][$poId][$itemNumberId][$uom]+=$reqqnty_grey;
					}
					else{
						$Qty[$this->_sweater][$this->_grey][$poId][$itemNumberId][$uom]=$reqqnty_grey;
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
				elseif($fabNatureId==100 && $fabricSourceId==2){
					if(isset($Qty[$this->_sweater][$this->_finish][$poId][$colorId][$uom])){
						$Qty[$this->_sweater][$this->_finish][$poId][$colorId][$uom]+=$reqqnty_finish;
					}
					else{
						$Qty[$this->_sweater][$this->_finish][$poId][$colorId][$uom]=$reqqnty_finish;
					}
					if(isset($Qty[$this->_sweater][$this->_finish][$poId][$colorId][$uom])){
						$Qty[$this->_sweater][$this->_grey][$poId][$colorId][$uom]+=$reqqnty_grey;
					}
					else{
						$Qty[$this->_sweater][$this->_grey][$poId][$colorId][$uom]=$reqqnty_grey;
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
				elseif($fabNatureId==100 && $fabricSourceId==2){
					if(isset($Qty[$this->_sweater][$this->_finish][$poId][$sizeId][$uom])){
						$Qty[$this->_sweater][$this->_finish][$poId][$sizeId][$uom]+=$reqqnty_finish;
					}
					else{
						$Qty[$this->_sweater][$this->_finish][$poId][$sizeId][$uom]=$reqqnty_finish;
					}
					if(isset($Qty[$this->_sweater][$this->_grey][$poId][$sizeId][$uom])){
						$Qty[$this->_sweater][$this->_grey][$poId][$sizeId][$uom]+=$reqqnty_grey;
					}
					else{
						$Qty[$this->_sweater][$this->_grey][$poId][$sizeId][$uom]=$reqqnty_grey;
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
				elseif($fabNatureId==100 && $fabricSourceId==2){
					if(isset($Qty[$this->_sweater][$this->_finish][$poId][$countryId][$itemNumberId][$uom])){
						$Qty[$this->_sweater][$this->_finish][$poId][$countryId][$itemNumberId][$uom]+=$reqqnty_finish;
					}
					else{
						$Qty[$this->_sweater][$this->_finish][$poId][$countryId][$itemNumberId][$uom]=$reqqnty_finish;
					}
					if(isset($Qty[$this->_sweater][$this->_grey][$poId][$countryId][$itemNumberId][$uom])){
						$Qty[$this->_sweater][$this->_grey][$poId][$countryId][$itemNumberId][$uom]+=$reqqnty_grey;
					}
					else{
						$Qty[$this->_sweater][$this->_grey][$poId][$countryId][$itemNumberId][$uom]=$reqqnty_grey;
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
				elseif($fabNatureId==100 && $fabricSourceId==2){
					if(isset($Qty[$this->_sweater][$this->_finish][$poId][$countryId][$colorId][$uom])){
						$Qty[$this->_sweater][$this->_finish][$poId][$countryId][$colorId][$uom]+=$reqqnty_finish;
					}
					else{
						$Qty[$this->_sweater][$this->_finish][$poId][$countryId][$colorId][$uom]=$reqqnty_finish;
					}
					if(isset($Qty[$this->_sweater][$this->_grey][$poId][$countryId][$colorId][$uom])){
						$Qty[$this->_sweater][$this->_grey][$poId][$countryId][$colorId][$uom]+=$reqqnty_grey;
					}
					else{
						$Qty[$this->_sweater][$this->_grey][$poId][$countryId][$colorId][$uom]=$reqqnty_grey;
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
				elseif($fabNatureId==100 && $fabricSourceId==2){
					if(isset($Qty[$this->_sweater][$this->_finish][$poId][$countryId][$sizeId][$uom])){
						$Qty[$this->_sweater][$this->_finish][$poId][$countryId][$sizeId][$uom]+=$reqqnty_finish;
					}
					else{
						$Qty[$this->_sweater][$this->_finish][$poId][$countryId][$sizeId][$uom]=$reqqnty_finish;
					}
					if(isset($Qty[$this->_sweater][$this->_grey][$poId][$countryId][$sizeId][$uom])){
						$Qty[$this->_sweater][$this->_grey][$poId][$countryId][$sizeId][$uom]+=$reqqnty_grey;
					}
					else{
						$Qty[$this->_sweater][$this->_grey][$poId][$countryId][$sizeId][$uom]=$reqqnty_grey;
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
				elseif($fabNatureId==100 && $fabricSourceId==2){
					if(isset($Qty[$this->_sweater][$this->_finish][$poId][$itemNumberId][$colorId][$uom])){
						$Qty[$this->_sweater][$this->_finish][$poId][$itemNumberId][$colorId][$uom]+=$reqqnty_finish;
					}
					else{
						$Qty[$this->_sweater][$this->_finish][$poId][$itemNumberId][$colorId][$uom]=$reqqnty_finish;
					}
					if(isset($Qty[$this->_sweater][$this->_grey][$poId][$itemNumberId][$colorId][$uom])){
						$Qty[$this->_sweater][$this->_grey][$poId][$itemNumberId][$colorId][$uom]+=$reqqnty_grey;
					}
					else{
						$Qty[$this->_sweater][$this->_grey][$poId][$itemNumberId][$colorId][$uom]=$reqqnty_grey;
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
				elseif($fabNatureId==100 && $fabricSourceId==2){
					if(isset($Qty[$this->_sweater][$this->_finish][$poId][$itemNumberId][$sizeId][$uom])){
						$Qty[$this->_sweater][$this->_finish][$poId][$itemNumberId][$sizeId][$uom]+=$reqqnty_finish;
					}
					else{
						$Qty[$this->_sweater][$this->_finish][$poId][$itemNumberId][$sizeId][$uom]=$reqqnty_finish;
					}
					if(isset($Qty[$this->_sweater][$this->_grey][$poId][$itemNumberId][$sizeId][$uom])){
						$Qty[$this->_sweater][$this->_grey][$poId][$itemNumberId][$sizeId][$uom]+=$reqqnty_grey;
					}
					else{
						$Qty[$this->_sweater][$this->_grey][$poId][$itemNumberId][$sizeId][$uom]=$reqqnty_grey;
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
				elseif($fabNatureId==100 && $fabricSourceId==2){
					if(isset($Qty[$this->_sweater][$this->_finish][$poId][$colorId][$sizeId][$uom])){
						$Qty[$this->_sweater][$this->_finish][$poId][$colorId][$sizeId][$uom]+=$reqqnty_finish;
					}
					else{
						$Qty[$this->_sweater][$this->_finish][$poId][$colorId][$sizeId][$uom]=$reqqnty_finish;
					}
					if(isset($Qty[$this->_sweater][$this->_grey][$poId][$colorId][$sizeId][$uom]))
					{
						$Qty[$this->_sweater][$this->_grey][$poId][$colorId][$sizeId][$uom]+=$reqqnty_grey;
					}
					else{
						$Qty[$this->_sweater][$this->_grey][$poId][$colorId][$sizeId][$uom]=$reqqnty_grey;
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
				elseif($fabNatureId==100 && $fabricSourceId==2){
					if(isset($Qty[$this->_sweater][$this->_finish][$poId][$colorId][$bodypartId][$uom])){
						$Qty[$this->_sweater][$this->_finish][$poId][$colorId][$bodypartId][$uom]+=$reqqnty_finish;
					}
					else{
						$Qty[$this->_sweater][$this->_finish][$poId][$colorId][$bodypartId][$uom]=$reqqnty_finish;
					}
					if(isset($Qty[$this->_sweater][$this->_grey][$poId][$colorId][$bodypartId][$uom]))
					{
						$Qty[$this->_sweater][$this->_grey][$poId][$colorId][$bodypartId][$uom]+=$reqqnty_grey;
					}
					else{
						$Qty[$this->_sweater][$this->_grey][$poId][$colorId][$bodypartId][$uom]=$reqqnty_grey;
					}
				}
			}
			
			elseif($level==$this->_By_OrderBodypartDeterminIdAndGmtscolor){
				if($fabNatureId==2 && $fabricSourceId==2){
					if(isset($Qty[$this->_knit][$this->_finish][$poId][$bodypartId][$libYarnCountDeterId][$fabricColorId][$uom])){
						$Qty[$this->_knit][$this->_finish][$poId][$bodypartId][$libYarnCountDeterId][$fabricColorId][$uom]+=$reqqnty_finish;
					}
					else{
						$Qty[$this->_knit][$this->_finish][$poId][$bodypartId][$libYarnCountDeterId][$fabricColorId][$uom]=$reqqnty_finish;
					}
					if(isset($Qty[$this->_knit][$this->_grey][$poId][$bodypartId][$libYarnCountDeterId][$fabricColorId][$uom])){
						$Qty[$this->_knit][$this->_grey][$poId][$bodypartId][$libYarnCountDeterId][$fabricColorId][$uom]+=$reqqnty_grey;
					}
					else{
						$Qty[$this->_knit][$this->_grey][$poId][$bodypartId][$libYarnCountDeterId][$fabricColorId][$uom]=$reqqnty_grey;
					}
				}
				elseif($fabNatureId==3 && $fabricSourceId==2){
					if(isset($Qty[$this->_woven][$this->_finish][$poId][$bodypartId][$libYarnCountDeterId][$fabricColorId][$uom])){
						$Qty[$this->_woven][$this->_finish][$poId][$bodypartId][$libYarnCountDeterId][$fabricColorId][$uom]+=$reqqnty_finish;
					}
					else{
						$Qty[$this->_woven][$this->_finish][$poId][$bodypartId][$libYarnCountDeterId][$fabricColorId][$uom]=$reqqnty_finish;
					}
					if(isset($Qty[$this->_woven][$this->_grey][$poId][$bodypartId][$libYarnCountDeterId][$fabricColorId][$uom]))
					{
						$Qty[$this->_woven][$this->_grey][$poId][$bodypartId][$libYarnCountDeterId][$fabricColorId][$uom]+=$reqqnty_grey;
					}
					else{
						$Qty[$this->_woven][$this->_grey][$poId][$bodypartId][$libYarnCountDeterId][$fabricColorId][$uom]=$reqqnty_grey;
					}
				}
				elseif($fabNatureId==100 && $fabricSourceId==2){
					if(isset($Qty[$this->_sweater][$this->_finish][$poId][$bodypartId][$libYarnCountDeterId][$fabricColorId][$uom])){
						$Qty[$this->_sweater][$this->_finish][$poId][$bodypartId][$libYarnCountDeterId][$fabricColorId][$uom]+=$reqqnty_finish;
					}
					else{
						$Qty[$this->_sweater][$this->_finish][$poId][$bodypartId][$libYarnCountDeterId][$fabricColorId][$uom]=$reqqnty_finish;
					}
					if(isset($Qty[$this->_sweater][$this->_grey][$poId][$bodypartId][$libYarnCountDeterId][$fabricColorId][$uom]))
					{
						$Qty[$this->_sweater][$this->_grey][$poId][$bodypartId][$libYarnCountDeterId][$fabricColorId][$uom]+=$reqqnty_grey;
					}
					else{
						$Qty[$this->_sweater][$this->_grey][$poId][$bodypartId][$libYarnCountDeterId][$fabricColorId][$uom]=$reqqnty_grey;
					}
				}
			}
			elseif($level==$this->_By_OrderBodypartDeterminIdAndDiaWidth){
				if($fabNatureId==2 && $fabricSourceId==2){
					if(isset($Qty[$this->_knit][$this->_finish][$poId][$bodypartId][$libYarnCountDeterId][$diaWidth][$uom])){
						$Qty[$this->_knit][$this->_finish][$poId][$bodypartId][$libYarnCountDeterId][$diaWidth][$uom]+=$reqqnty_finish;
					}
					else{
						$Qty[$this->_knit][$this->_finish][$poId][$bodypartId][$libYarnCountDeterId][$diaWidth][$uom]=$reqqnty_finish;
					}
					if(isset($Qty[$this->_knit][$this->_grey][$poId][$bodypartId][$libYarnCountDeterId][$diaWidth][$uom])){
						$Qty[$this->_knit][$this->_grey][$poId][$bodypartId][$libYarnCountDeterId][$diaWidth][$uom]+=$reqqnty_grey;
					}
					else{
						$Qty[$this->_knit][$this->_grey][$poId][$bodypartId][$libYarnCountDeterId][$diaWidth][$uom]=$reqqnty_grey;
					}
				}
				elseif($fabNatureId==3 && $fabricSourceId==2){
					if(isset($Qty[$this->_woven][$this->_finish][$poId][$bodypartId][$libYarnCountDeterId][$diaWidth][$uom])){
						$Qty[$this->_woven][$this->_finish][$poId][$bodypartId][$libYarnCountDeterId][$diaWidth][$uom]+=$reqqnty_finish;
					}
					else{
						$Qty[$this->_woven][$this->_finish][$poId][$bodypartId][$libYarnCountDeterId][$diaWidth][$uom]=$reqqnty_finish;
					}
					if(isset($Qty[$this->_woven][$this->_grey][$poId][$bodypartId][$libYarnCountDeterId][$diaWidth][$uom]))
					{
						$Qty[$this->_woven][$this->_grey][$poId][$bodypartId][$libYarnCountDeterId][$diaWidth][$uom]+=$reqqnty_grey;
					}
					else{
						$Qty[$this->_woven][$this->_grey][$poId][$bodypartId][$libYarnCountDeterId][$diaWidth][$uom]=$reqqnty_grey;
					}
				}
				elseif($fabNatureId==100 && $fabricSourceId==2){
					if(isset($Qty[$this->_sweater][$this->_finish][$poId][$bodypartId][$libYarnCountDeterId][$diaWidth][$uom])){
						$Qty[$this->_sweater][$this->_finish][$poId][$bodypartId][$libYarnCountDeterId][$diaWidth][$uom]+=$reqqnty_finish;
					}
					else{
						$Qty[$this->_sweater][$this->_finish][$poId][$bodypartId][$libYarnCountDeterId][$diaWidth][$uom]=$reqqnty_finish;
					}
					if(isset($Qty[$this->_sweater][$this->_grey][$poId][$bodypartId][$libYarnCountDeterId][$diaWidth][$uom]))
					{
						$Qty[$this->_sweater][$this->_grey][$poId][$bodypartId][$libYarnCountDeterId][$diaWidth][$uom]+=$reqqnty_grey;
					}
					else{
						$Qty[$this->_sweater][$this->_grey][$poId][$bodypartId][$libYarnCountDeterId][$diaWidth][$uom]=$reqqnty_grey;
					}
				}
			}
			elseif($level==$this->_By_OrderItemidCountrydateBodypartDeterminIdDiatypeDiaAndGsm){
				if($fabNatureId==2 && $fabricSourceId==2){
					if(isset($Qty[$this->_knit][$this->_finish][$poId][$itemNumberId][$countryShipDate][$bodypartId][$libYarnCountDeterId][$widthDiaType][$diaWidth][$gsmweight][$uom])){
						$Qty[$this->_knit][$this->_finish][$poId][$itemNumberId][$countryShipDate][$bodypartId][$libYarnCountDeterId][$widthDiaType][$diaWidth][$gsmweight][$uom]+=$reqqnty_finish;
					}
					else{
						$Qty[$this->_knit][$this->_finish][$poId][$itemNumberId][$countryShipDate][$bodypartId][$libYarnCountDeterId][$widthDiaType][$diaWidth][$gsmweight][$uom]=$reqqnty_finish;
					}
					if(isset($Qty[$this->_knit][$this->_grey][$poId][$itemNumberId][$countryShipDate][$bodypartId][$libYarnCountDeterId][$widthDiaType][$diaWidth][$gsmweight][$uom])){
						$Qty[$this->_knit][$this->_grey][$poId][$itemNumberId][$countryShipDate][$bodypartId][$libYarnCountDeterId][$widthDiaType][$diaWidth][$gsmweight][$uom]+=$reqqnty_grey;
					}
					else{
						$Qty[$this->_knit][$this->_grey][$poId][$itemNumberId][$countryShipDate][$bodypartId][$libYarnCountDeterId][$widthDiaType][$diaWidth][$gsmweight][$uom]=$reqqnty_grey;
					}
				}
				elseif($fabNatureId==3 && $fabricSourceId==2){
					if(isset($Qty[$this->_woven][$this->_finish][$poId][$itemNumberId][$countryShipDate][$bodypartId][$libYarnCountDeterId][$widthDiaType][$diaWidth][$gsmweight][$uom])){
						$Qty[$this->_woven][$this->_finish][$poId][$itemNumberId][$countryShipDate][$bodypartId][$libYarnCountDeterId][$widthDiaType][$diaWidth][$gsmweight][$uom]+=$reqqnty_finish;
					}
					else{
						$Qty[$this->_woven][$this->_finish][$poId][$itemNumberId][$countryShipDate][$bodypartId][$libYarnCountDeterId][$widthDiaType][$diaWidth][$gsmweight][$uom]=$reqqnty_finish;
					}
					if(isset($Qty[$this->_woven][$this->_grey][$poId][$itemNumberId][$countryShipDate][$bodypartId][$libYarnCountDeterId][$widthDiaType][$diaWidth][$gsmweight][$uom]))
					{
						$Qty[$this->_woven][$this->_grey][$poId][$itemNumberId][$countryShipDate][$bodypartId][$libYarnCountDeterId][$widthDiaType][$diaWidth][$gsmweight][$uom]+=$reqqnty_grey;
					}
					else{
						$Qty[$this->_woven][$this->_grey][$poId][$itemNumberId][$countryShipDate][$bodypartId][$libYarnCountDeterId][$widthDiaType][$diaWidth][$gsmweight][$uom]=$reqqnty_grey;
					}
				}
				elseif($fabNatureId==100 && $fabricSourceId==2){
					if(isset($Qty[$this->_sweater][$this->_finish][$poId][$itemNumberId][$countryShipDate][$bodypartId][$libYarnCountDeterId][$widthDiaType][$diaWidth][$gsmweight][$uom])){
						$Qty[$this->_sweater][$this->_finish][$poId][$itemNumberId][$countryShipDate][$bodypartId][$libYarnCountDeterId][$widthDiaType][$diaWidth][$gsmweight][$uom]+=$reqqnty_finish;
					}
					else{
						$Qty[$this->_sweater][$this->_finish][$poId][$itemNumberId][$countryShipDate][$bodypartId][$libYarnCountDeterId][$widthDiaType][$diaWidth][$gsmweight][$uom]=$reqqnty_finish;
					}
					if(isset($Qty[$this->_sweater][$this->_grey][$poId][$itemNumberId][$countryShipDate][$bodypartId][$libYarnCountDeterId][$widthDiaType][$diaWidth][$gsmweight][$uom]))
					{
						$Qty[$this->_sweater][$this->_grey][$poId][$itemNumberId][$countryShipDate][$bodypartId][$libYarnCountDeterId][$widthDiaType][$diaWidth][$gsmweight][$uom]+=$reqqnty_grey;
					}
					else{
						$Qty[$this->_sweater][$this->_grey][$poId][$itemNumberId][$countryShipDate][$bodypartId][$libYarnCountDeterId][$widthDiaType][$diaWidth][$gsmweight][$uom]=$reqqnty_grey;
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
				elseif($fabNatureId==100 && $fabricSourceId==2){
					if(isset($Qty[$this->_sweater][$this->_finish][$poId][$countryId][$itemNumberId][$colorId][$uom])){
						$Qty[$this->_sweater][$this->_finish][$poId][$countryId][$itemNumberId][$colorId][$uom]+=$reqqnty_finish;
					}
					else{
						$Qty[$this->_sweater][$this->_finish][$poId][$countryId][$itemNumberId][$colorId][$uom]=$reqqnty_finish;
					}
					if(isset($Qty[$this->_sweater][$this->_grey][$poId][$countryId][$itemNumberId][$colorId][$uom])){
						$Qty[$this->_sweater][$this->_grey][$poId][$countryId][$itemNumberId][$colorId][$uom]+=$reqqnty_grey;
					}
					else{
						$Qty[$this->_sweater][$this->_grey][$poId][$countryId][$itemNumberId][$colorId][$uom]=$reqqnty_grey;
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
				elseif($fabNatureId==100 && $fabricSourceId==2){
					if(isset($Qty[$this->_sweater][$this->_finish][$poId][$countryId][$itemNumberId][$sizeId][$uom])){
						$Qty[$this->_sweater][$this->_finish][$poId][$countryId][$itemNumberId][$sizeId][$uom]+=$reqqnty_finish;
					}
					else{
						$Qty[$this->_sweater][$this->_finish][$poId][$countryId][$itemNumberId][$sizeId][$uom]=$reqqnty_finish;
					}
					if(isset($Qty[$this->_sweater][$this->_grey][$poId][$countryId][$itemNumberId][$sizeId][$uom])){
						$Qty[$this->_sweater][$this->_grey][$poId][$countryId][$itemNumberId][$sizeId][$uom]+=$reqqnty_grey;
					}
					else{
						$Qty[$this->_sweater][$this->_grey][$poId][$countryId][$itemNumberId][$sizeId][$uom]=$reqqnty_grey;
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
				elseif($fabNatureId==100 && $fabricSourceId==2){
					if(isset($Qty[$this->_sweater][$this->_finish][$poId][$countryId][$colorId][$sizeId][$uom])){
						$Qty[$this->_sweater][$this->_finish][$poId][$countryId][$colorId][$sizeId][$uom]+=$reqqnty_finish;
					}
					else{
						$Qty[$this->_sweater][$this->_finish][$poId][$countryId][$colorId][$sizeId][$uom]=$reqqnty_finish;
					}
					if(isset($Qty[$this->_sweater][$this->_grey][$poId][$countryId][$colorId][$sizeId][$uom])){
						$Qty[$this->_sweater][$this->_grey][$poId][$countryId][$colorId][$sizeId][$uom]+=$reqqnty_grey;
					}
					else{
						$Qty[$this->_sweater][$this->_grey][$poId][$countryId][$colorId][$sizeId][$uom]=$reqqnty_grey;
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
				elseif($fabNatureId==100 && $fabricSourceId==2){
					if(isset($Qty[$this->_sweater][$this->_finish][$poId][$itemNumberId][$colorId][$sizeId][$uom])){
						$Qty[$this->_sweater][$this->_finish][$poId][$itemNumberId][$colorId][$sizeId][$uom]+=$reqqnty_finish;
					}
					else{
						$Qty[$this->_sweater][$this->_finish][$poId][$itemNumberId][$colorId][$sizeId][$uom]=$reqqnty_finish;
					}
					if(isset($Qty[$this->_sweater][$this->_grey][$poId][$itemNumberId][$colorId][$sizeId][$uom])){
						$Qty[$this->_sweater][$this->_grey][$poId][$itemNumberId][$colorId][$sizeId][$uom]+=$reqqnty_grey;
					}
					else{
						$Qty[$this->_sweater][$this->_grey][$poId][$itemNumberId][$colorId][$sizeId][$uom]=$reqqnty_grey;
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
				elseif($fabNatureId==100 && $fabricSourceId==2){
					if(isset($Qty[$this->_sweater][$this->_finish][$poId][$countryId][$itemNumberId][$colorId][$sizeId][$uom])){
						$Qty[$this->_sweater][$this->_finish][$poId][$countryId][$itemNumberId][$colorId][$sizeId][$uom]+=$reqqnty_finish;
					}
					else{
						$Qty[$this->_sweater][$this->_finish][$poId][$countryId][$itemNumberId][$colorId][$sizeId][$uom]=$reqqnty_finish;
					}
					if(isset($Qty[$this->_sweater][$this->_grey][$poId][$countryId][$itemNumberId][$colorId][$sizeId][$uom])){
						$Qty[$this->_sweater][$this->_grey][$poId][$countryId][$itemNumberId][$colorId][$sizeId][$uom]+=$reqqnty_grey;
					}
					else{
						$Qty[$this->_sweater][$this->_grey][$poId][$countryId][$itemNumberId][$colorId][$sizeId][$uom]=$reqqnty_grey;
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
		$itemNumberId='';$isConfirmed='';
		$planPutQnty=0;
		$bodypartId='';$sourceId="";
		$requirment=0;
		$poId='';$nominated_supp='';
		$budget_on=2;
		$Qty=array();
		foreach($this->_dataArray as $row)
		{
			$jobNo=$row['job_no'];
			$poId=$row['id'];
			$nominated_supp=$row['nominated_supp'];
			$isConfirmed=$row['is_confirmed'];
			$itemNumberId=$row['item_number_id'];
			$sourceId=$row['source_id'];
			$colorId=$row['color_number_id'];
			$sizeId=$row['size_number_id'];
			$countryId=$row['country_id'];
			$planPutQnty=$row['plan_cut_qnty'];
			$orderQuantity=$row['order_quantity'];
			$countryShipDate=$row['country_ship_date'];
			$widthDiaType=$row['width_dia_type'];
			
			$bodypartId=$row['body_part_id'];
			$diaWidth=$row['dia_width'];
			$fabNatureId=$row['fab_nature_id'];
			$fabricSourceId=$row['fabric_source'];
			$cons=$row['cons'];
			$requirment=$row['requirment'];
			$uom=$row['uom'];
			$budget_on=$row['budget_on'];
			if($budget_on==0 || $budget_on=="") $budget_on=2;
			$costingPerQty=$this->_costingPerQtyArr[$jobNo];
			$set_item_ratio=$this->_gmtsitemRatioArray[$jobNo][$itemNumberId];
			
			if($budget_on==2)
			{
				$reqqnty_finish =$this->_calculateQty($planPutQnty,$costingPerQty,$set_item_ratio,$cons);
				$reqqnty_grey =$this->_calculateQty($planPutQnty,$costingPerQty,$set_item_ratio,$requirment);
			}
			if($budget_on==1)
			{
				$reqqnty_finish =$this->_calculateQty($orderQuantity,$costingPerQty,$set_item_ratio,$cons);
				$reqqnty_grey =$this->_calculateQty($orderQuantity,$costingPerQty,$set_item_ratio,$requirment);
			}
			//$reqqnty_finish =$this->_calculateQty($planPutQnty,$costingPerQty,$set_item_ratio,$cons);
			//$reqqnty_grey =$this->_calculateQty($planPutQnty,$costingPerQty,$set_item_ratio,$requirment);
			
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
				elseif($fabNatureId==100 && $fabricSourceId==3){
					if(isset($Qty[$this->_sweater][$this->_finish][$jobNo][$uom])){
						$Qty[$this->_sweater][$this->_finish][$jobNo][$uom]+=$reqqnty_finish;
					}
					else{
						$Qty[$this->_sweater][$this->_finish][$jobNo][$uom]=$reqqnty_finish;
					}
					if(isset($Qty[$this->_sweater][$this->_grey][$jobNo][$uom])){
						$Qty[$this->_sweater][$this->_grey][$jobNo][$uom]+=$reqqnty_grey;
					}
					else{
						$Qty[$this->_sweater][$this->_grey][$jobNo][$uom]=$reqqnty_grey;
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
				elseif($fabNatureId==100 && $fabricSourceId==3){
					if(isset($Qty[$this->_sweater][$this->_finish][$poId][$uom])){
						$Qty[$this->_sweater][$this->_finish][$poId][$uom]+=$reqqnty_finish;
					}
					else{
						$Qty[$this->_sweater][$this->_finish][$poId][$uom]=$reqqnty_finish;
					}
					if(isset($Qty[$this->_sweater][$this->_grey][$poId][$uom])){
						$Qty[$this->_sweater][$this->_grey][$poId][$uom]+=$reqqnty_grey;
					}
					else{
						$Qty[$this->_sweater][$this->_grey][$poId][$uom]=$reqqnty_grey;
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
				elseif($fabNatureId==100 && $fabricSourceId==3){
					if(isset($Qty[$this->_sweater][$this->_finish][$poId][$countryId][$uom])){
						$Qty[$this->_sweater][$this->_finish][$poId][$countryId][$uom]+=$reqqnty_finish;
					}
					else{
						$Qty[$this->_sweater][$this->_finish][$poId][$countryId][$uom]=$reqqnty_finish;
					}
					if(isset($Qty[$this->_sweater][$this->_grey][$poId][$countryId][$uom])){
						$Qty[$this->_sweater][$this->_grey][$poId][$countryId][$uom]+=$reqqnty_grey;
					}
					else{
						$Qty[$this->_sweater][$this->_grey][$poId][$countryId][$uom]=$reqqnty_grey;
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
				elseif($fabNatureId==100 && $fabricSourceId==3){
					if(isset($Qty[$this->_sweater][$this->_finish][$poId][$itemNumberId][$uom])){
						$Qty[$this->_sweater][$this->_finish][$poId][$itemNumberId][$uom]+=$reqqnty_finish;
					}
					else{


						$Qty[$this->_sweater][$this->_finish][$poId][$itemNumberId][$uom]+=$reqqnty_finish;
					}
					if(isset($Qty[$this->_sweater][$this->_grey][$poId][$itemNumberId][$uom])){
						$Qty[$this->_sweater][$this->_grey][$poId][$itemNumberId][$uom]+=$reqqnty_grey;
					}
					else{
						$Qty[$this->_sweater][$this->_grey][$poId][$itemNumberId][$uom]=$reqqnty_grey;
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
				elseif($fabNatureId==100 && $fabricSourceId==3){
					if(isset($Qty[$this->_sweater][$this->_finish][$poId][$colorId][$uom])){
						$Qty[$this->_sweater][$this->_finish][$poId][$colorId][$uom]+=$reqqnty_finish;
					}
					else{
						$Qty[$this->_sweater][$this->_finish][$poId][$colorId][$uom]=$reqqnty_finish;
					}
					if(isset($Qty[$this->_sweater][$this->_finish][$poId][$colorId][$uom])){
						$Qty[$this->_sweater][$this->_grey][$poId][$colorId][$uom]+=$reqqnty_grey;
					}
					else{
						$Qty[$this->_sweater][$this->_grey][$poId][$colorId][$uom]=$reqqnty_grey;
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
				elseif($fabNatureId==100 && $fabricSourceId==3){
					if(isset($Qty[$this->_sweater][$this->_finish][$poId][$sizeId][$uom])){
						$Qty[$this->_sweater][$this->_finish][$poId][$sizeId][$uom]+=$reqqnty_finish;
					}
					else{
						$Qty[$this->_sweater][$this->_finish][$poId][$sizeId][$uom]=$reqqnty_finish;
					}
					if(isset($Qty[$this->_sweater][$this->_grey][$poId][$sizeId][$uom])){
						$Qty[$this->_sweater][$this->_grey][$poId][$sizeId][$uom]+=$reqqnty_grey;
					}
					else{
						$Qty[$this->_sweater][$this->_grey][$poId][$sizeId][$uom]=$reqqnty_grey;
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

				elseif($fabNatureId==100 && $fabricSourceId==3){
					if(isset($Qty[$this->_sweater][$this->_finish][$poId][$countryId][$itemNumberId][$uom])){
						$Qty[$this->_sweater][$this->_finish][$poId][$countryId][$itemNumberId][$uom]+=$reqqnty_finish;
					}
					else{
						$Qty[$this->_sweater][$this->_finish][$poId][$countryId][$itemNumberId][$uom]=$reqqnty_finish;
					}
					if(isset($Qty[$this->_sweater][$this->_grey][$poId][$countryId][$itemNumberId][$uom])){
						$Qty[$this->_sweater][$this->_grey][$poId][$countryId][$itemNumberId][$uom]+=$reqqnty_grey;
					}
					else{
						$Qty[$this->_sweater][$this->_grey][$poId][$countryId][$itemNumberId][$uom]=$reqqnty_grey;
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
				elseif($fabNatureId==100 && $fabricSourceId==3){
					if(isset($Qty[$this->_sweater][$this->_finish][$poId][$countryId][$colorId][$uom])){
						$Qty[$this->_sweater][$this->_finish][$poId][$countryId][$colorId][$uom]+=$reqqnty_finish;
					}
					else{
						$Qty[$this->_sweater][$this->_finish][$poId][$countryId][$colorId][$uom]=$reqqnty_finish;
					}
					if(isset($Qty[$this->_sweater][$this->_grey][$poId][$countryId][$colorId][$uom])){
						$Qty[$this->_sweater][$this->_grey][$poId][$countryId][$colorId][$uom]+=$reqqnty_grey;
					}
					else{
						$Qty[$this->_sweater][$this->_grey][$poId][$countryId][$colorId][$uom]=$reqqnty_grey;
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
				elseif($fabNatureId==100 && $fabricSourceId==3){
					if(isset($Qty[$this->_sweater][$this->_finish][$poId][$countryId][$sizeId][$uom])){
						$Qty[$this->_sweater][$this->_finish][$poId][$countryId][$sizeId][$uom]+=$reqqnty_finish;
					}
					else{
						$Qty[$this->_sweater][$this->_finish][$poId][$countryId][$sizeId][$uom]=$reqqnty_finish;
					}
					if(isset($Qty[$this->_sweater][$this->_grey][$poId][$countryId][$sizeId][$uom])){
						$Qty[$this->_sweater][$this->_grey][$poId][$countryId][$sizeId][$uom]+=$reqqnty_grey;
					}
					else{
						$Qty[$this->_sweater][$this->_grey][$poId][$countryId][$sizeId][$uom]=$reqqnty_grey;
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
				elseif($fabNatureId==100 && $fabricSourceId==3){
					if(isset($Qty[$this->_sweater][$this->_finish][$poId][$itemNumberId][$colorId][$uom])){
						$Qty[$this->_sweater][$this->_finish][$poId][$itemNumberId][$colorId][$uom]+=$reqqnty_finish;
					}
					else{
						$Qty[$this->_sweater][$this->_finish][$poId][$itemNumberId][$colorId][$uom]=$reqqnty_finish;
					}
					if(isset($Qty[$this->_sweater][$this->_grey][$poId][$itemNumberId][$colorId][$uom])){
						$Qty[$this->_sweater][$this->_grey][$poId][$itemNumberId][$colorId][$uom]+=$reqqnty_grey;
					}
					else{
						$Qty[$this->_sweater][$this->_grey][$poId][$itemNumberId][$colorId][$uom]=$reqqnty_grey;
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
				elseif($fabNatureId==100 && $fabricSourceId==3){
					if(isset($Qty[$this->_sweater][$this->_finish][$poId][$itemNumberId][$sizeId][$uom])){
						$Qty[$this->_sweater][$this->_finish][$poId][$itemNumberId][$sizeId][$uom]+=$reqqnty_finish;
					}
					else{
						$Qty[$this->_sweater][$this->_finish][$poId][$itemNumberId][$sizeId][$uom]=$reqqnty_finish;
					}
					if(isset($Qty[$this->_sweater][$this->_grey][$poId][$itemNumberId][$sizeId][$uom])){
						$Qty[$this->_sweater][$this->_grey][$poId][$itemNumberId][$sizeId][$uom]+=$reqqnty_grey;
					}
					else{
						$Qty[$this->_sweater][$this->_grey][$poId][$itemNumberId][$sizeId][$uom]=$reqqnty_grey;
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
				elseif($fabNatureId==100 && $fabricSourceId==3){
					if(isset($Qty[$this->_sweater][$this->_finish][$poId][$colorId][$sizeId][$uom])){
						$Qty[$this->_sweater][$this->_finish][$poId][$colorId][$sizeId][$uom]+=$reqqnty_finish;
					}
					else{
						$Qty[$this->_sweater][$this->_finish][$poId][$colorId][$sizeId][$uom]=$reqqnty_finish;
					}
					if(isset($Qty[$this->_sweater][$this->_grey][$poId][$colorId][$sizeId][$uom]))
					{
						$Qty[$this->_sweater][$this->_grey][$poId][$colorId][$sizeId][$uom]+=$reqqnty_grey;
					}
					else{
						$Qty[$this->_sweater][$this->_grey][$poId][$colorId][$sizeId][$uom]=$reqqnty_grey;
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
				elseif($fabNatureId==100 && $fabricSourceId==3){
					if(isset($Qty[$this->_sweater][$this->_finish][$poId][$colorId][$bodypartId][$uom])){
						$Qty[$this->_sweater][$this->_finish][$poId][$colorId][$bodypartId][$uom]+=$reqqnty_finish;
					}
					else{
						$Qty[$this->_sweater][$this->_finish][$poId][$colorId][$bodypartId][$uom]=$reqqnty_finish;
					}
					if(isset($Qty[$this->_sweater][$this->_grey][$poId][$colorId][$bodypartId][$uom]))
					{
						$Qty[$this->_sweater][$this->_grey][$poId][$colorId][$bodypartId][$uom]+=$reqqnty_grey;
					}
					else{
						$Qty[$this->_sweater][$this->_grey][$poId][$colorId][$bodypartId][$uom]=$reqqnty_grey;
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
				elseif($fabNatureId==100 && $fabricSourceId==3){
					if(isset($Qty[$this->_sweater][$this->_finish][$poId][$countryId][$itemNumberId][$colorId][$uom])){
						$Qty[$this->_sweater][$this->_finish][$poId][$countryId][$itemNumberId][$colorId][$uom]+=$reqqnty_finish;
					}
					else{
						$Qty[$this->_sweater][$this->_finish][$poId][$countryId][$itemNumberId][$colorId][$uom]=$reqqnty_finish;
					}
					if(isset($Qty[$this->_sweater][$this->_grey][$poId][$countryId][$itemNumberId][$colorId][$uom])){
						$Qty[$this->_sweater][$this->_grey][$poId][$countryId][$itemNumberId][$colorId][$uom]+=$reqqnty_grey;
					}
					else{
						$Qty[$this->_sweater][$this->_grey][$poId][$countryId][$itemNumberId][$colorId][$uom]=$reqqnty_grey;
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
				elseif($fabNatureId==100 && $fabricSourceId==3){
					if(isset($Qty[$this->_sweater][$this->_finish][$poId][$countryId][$itemNumberId][$sizeId][$uom])){
						$Qty[$this->_sweater][$this->_finish][$poId][$countryId][$itemNumberId][$sizeId][$uom]+=$reqqnty_finish;
					}
					else{
						$Qty[$this->_sweater][$this->_finish][$poId][$countryId][$itemNumberId][$sizeId][$uom]=$reqqnty_finish;
					}
					if(isset($Qty[$this->_sweater][$this->_grey][$poId][$countryId][$itemNumberId][$sizeId][$uom])){
						$Qty[$this->_sweater][$this->_grey][$poId][$countryId][$itemNumberId][$sizeId][$uom]+=$reqqnty_grey;
					}
					else{
						$Qty[$this->_sweater][$this->_grey][$poId][$countryId][$itemNumberId][$sizeId][$uom]=$reqqnty_grey;
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
				elseif($fabNatureId==100 && $fabricSourceId==3){
					if(isset($Qty[$this->_sweater][$this->_finish][$poId][$countryId][$colorId][$sizeId][$uom])){
						$Qty[$this->_sweater][$this->_finish][$poId][$countryId][$colorId][$sizeId][$uom]+=$reqqnty_finish;
					}
					else{
						$Qty[$this->_sweater][$this->_finish][$poId][$countryId][$colorId][$sizeId][$uom]=$reqqnty_finish;
					}
					if(isset($Qty[$this->_sweater][$this->_grey][$poId][$countryId][$colorId][$sizeId][$uom])){
						$Qty[$this->_sweater][$this->_grey][$poId][$countryId][$colorId][$sizeId][$uom]+=$reqqnty_grey;
					}
					else{
						$Qty[$this->_sweater][$this->_grey][$poId][$countryId][$colorId][$sizeId][$uom]=$reqqnty_grey;
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
				elseif($fabNatureId==100 && $fabricSourceId==3){
					if(isset($Qty[$this->_sweater][$this->_finish][$poId][$itemNumberId][$colorId][$sizeId][$uom])){
						$Qty[$this->_sweater][$this->_finish][$poId][$itemNumberId][$colorId][$sizeId][$uom]+=$reqqnty_finish;
					}
					else{
						$Qty[$this->_sweater][$this->_finish][$poId][$itemNumberId][$colorId][$sizeId][$uom]=$reqqnty_finish;
					}
					if(isset($Qty[$this->_sweater][$this->_grey][$poId][$itemNumberId][$colorId][$sizeId][$uom])){
						$Qty[$this->_sweater][$this->_grey][$poId][$itemNumberId][$colorId][$sizeId][$uom]+=$reqqnty_grey;
					}
					else{
						$Qty[$this->_sweater][$this->_grey][$poId][$itemNumberId][$colorId][$sizeId][$uom]=$reqqnty_grey;
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
				elseif($fabNatureId==100 && $fabricSourceId==3){
					if(isset($Qty[$this->_sweater][$this->_finish][$poId][$countryId][$itemNumberId][$colorId][$sizeId][$uom])){
						$Qty[$this->_sweater][$this->_finish][$poId][$countryId][$itemNumberId][$colorId][$sizeId][$uom]+=$reqqnty_finish;
					}
					else{
						$Qty[$this->_sweater][$this->_finish][$poId][$countryId][$itemNumberId][$colorId][$sizeId][$uom]=$reqqnty_finish;
					}
					if(isset($Qty[$this->_sweater][$this->_grey][$poId][$countryId][$itemNumberId][$colorId][$sizeId][$uom])){
						$Qty[$this->_sweater][$this->_grey][$poId][$countryId][$itemNumberId][$colorId][$sizeId][$uom]+=$reqqnty_grey;
					}
					else{
						$Qty[$this->_sweater][$this->_grey][$poId][$countryId][$itemNumberId][$colorId][$sizeId][$uom]=$reqqnty_grey;
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
	public function getQtyArray_by_OrderLibYarnCountDeterIdGmtscolorAndColorType_knitAndwoven_greyAndfinish(){
		$Qty=$this->_setQty_knitAndwoven_greyAndfinish($this->_By_OrderLibYarnCountDeterIdGmtscolorAndColorType);
		return $Qty;
	}

	public function getQtyArray_by_FabriccostidAndFabriccolor_knitAndwoven_greyAndfinish(){
		$Qty=$this->_setQty_knitAndwoven_greyAndfinish($this->_By_FabriccostidAndFabriccolor);
		return $Qty;
	}
	//Qty
	public function getQtyArray_by_JobIdLibYarnCountDeterIdAndGmtscolor_knitAndwoven_greyAndfinish(){
		$Qty=$this->_setQty_knitAndwoven_greyAndfinish($this->_By_JobIdLibYarnCountDeterIdAndGmtscolor);
		return $Qty;
	}
	//amount
	public function getAmountArray_by_JobIdLibYarnCountDeterIdAndGmtscolor_knitAndwoven_greyAndfinish(){
		$Qty=$this->_setAmount_knitAndwoven_greyAndfinish($this->_By_JobIdLibYarnCountDeterIdAndGmtscolor);
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
	//Order Fab Id Color Size Item size
	public function getQtyArray_by_OrderFabriccostidGmtscolorGmtsSizeAndItemSize_knitAndwoven_greyAndfinish(){
		$Qty=$this->_setQty_knitAndwoven_greyAndfinish($this->_By_OrderFabriccostidGmtscolorGmtsSizeAndItemSize);
		return $Qty;
	}
	
	public function getAmountArray_by_OrderFabriccostidGmtscolorGmtsSizeAndItemSize_knitAndwoven_greyAndfinish(){
		$Amount=$this->_setAmount_knitAndwoven_greyAndfinish($this->_By_OrderFabriccostidGmtscolorGmtsSizeAndItemSize);
		return $Amount;
	}
	
	public function getAmountArr_by_JobIdYarnCountIdGmtsAndFabricColor_source(){
		$Qty=$this->_setAmount_knitAndwoven_greyAndfinish($this->_By_JobIdYarnCountIdGmtsAndFabricColor);
		return $Qty;
	}
	//Fabric Color 
	public function getQtyArray_by_OrderFabriccostidFabriccolorAndDiaWidth_knitAndwoven_greyAndfinish(){
		$Qty=$this->_setQty_knitAndwoven_greyAndfinish($this->_By_OrderFabriccostidFabriccolorAndDiaWidth);
		return $Qty;
	}
	
	public function getAmountArray_by_OrderFabriccostidFabriccolorAndDiaWidth_knitAndwoven_greyAndfinish(){
		$Amount=$this->_setAmount_knitAndwoven_greyAndfinish($this->_By_OrderFabriccostidFabriccolorAndDiaWidth);
		return $Amount;
	}
	//order Fabric Color and determin id
	public function getQtyArray_by_OrderFabriccolorAndDeterminId_knitAndwoven_greyAndfinish(){
		$Qty=$this->_setQty_knitAndwoven_greyAndfinish($this->_By_OrderFabriccolorAndDeterminId);
		return $Qty;
	}
	
	public function getAmountArray_by_OrderFabriccolorAndDeterminId_knitAndwoven_greyAndfinish(){
		$Amount=$this->_setAmount_knitAndwoven_greyAndfinish($this->_By_OrderFabriccolorAndDeterminId);
		return $Amount;
	}
	
	//order Fabric Source and Body Type id
	public function getQtyArray_by_OrderFabricSourceAndBodyType_knitAndwoven_greyAndfinish(){
		$Qty=$this->_setQty_knitAndwoven_greyAndfinish($this->_By_OrderFabricSourceAndBodyType);
		return $Qty;
	}
	
	public function getAmountArray_by_OrderFabricSourceAndBodyType_knitAndwoven_greyAndfinish(){
		$Amount=$this->_setAmount_knitAndwoven_greyAndfinish($this->_By_OrderFabricSourceAndBodyType);
		return $Amount;
	}
	
	//order Fabric Source and Body Type id Fab Color
	public function getQtyArray_by_OrderFabricSourceAndBodyTypeFabColor_knitAndwoven_greyAndfinish(){
		$Qty=$this->_setQty_knitAndwoven_greyAndfinish($this->_By_OrderFabricSourceAndBodyTypeFabColor);
		return $Qty;
	}
	
	public function getAmountArray_by_OrderFabricSourceAndBodyTypeFabColor_knitAndwoven_greyAndfinish(){
		$Amount=$this->_setAmount_knitAndwoven_greyAndfinish($this->_By_OrderFabricSourceAndBodyTypeFabColor);
		return $Amount;
	}
	
	//order Fabric Source and Body Type id FabColor Determin GSM Dia
	public function getQtyArray_by_OrderFabricSourceBodyTypeFabColorDeterminIdGsmAndDiaWidth_knitAndwoven_greyAndfinish(){
		$Qty=$this->_setQty_knitAndwoven_greyAndfinish($this->_By_OrderFabricSourceBodyTypeFabColorDeterminIdGsmAndDiaWidth);
		return $Qty;
	}
	
	public function getAmountArray_by_OrderFabricSourceBodyTypeFabColorDeterminIdGsmAndDiaWidth_knitAndwoven_greyAndfinish(){
		$Amount=$this->_setAmount_knitAndwoven_greyAndfinish($this->_By_OrderFabricSourceBodyTypeFabColorDeterminIdGsmAndDiaWidth);
		return $Amount;
	}
	
		//order ,Item  and Body  id,Color Determin GSM Dia
	public function getQtyArray_by_OrderItemIdBodyIdColorDeterminIdGsmAndDiaWidth_knitAndwoven_greyAndfinish(){
		$Qty=$this->_setQty_knitAndwoven_greyAndfinish($this->_By_OrderItemIdBodyIdColorDeterminIdGsmAndDiaWidth);
		return $Qty;
	}
	
	public function getAmountArray_by_OrderItemIdBodyIdColorDeterminIdGsmAndDiaWidth_knitAndwoven_greyAndfinish(){
		$Amount=$this->_setAmount_knitAndwoven_greyAndfinish($this->_By_OrderItemIdBodyIdColorDeterminIdGsmAndDiaWidth);
		return $Amount;
	}
	
	
	public function getQtyArray_by_OrderGmtsColorFabricColorAndFabricSouce_greyAndfinish(){
		$Qty=$this->_setQty_knitAndwoven_greyAndfinish($this->_By_OrderGmtsColorFabricColorAndFabricSouce);
		return $Qty;
	}
	
	public function getAmountArray_by_OrderGmtsColorFabricColorAndFabricSouce_greyAndfinish(){
		$Amount=$this->_setAmount_knitAndwoven_greyAndfinish($this->_By_OrderGmtsColorFabricColorAndFabricSouce);
		return $Amount;
	}
	
	//PO Fab Id,Body partid,Determin Id,Gmt Color
	public function getQtyArray_by_OrderBodypartDeterminIdAndGmtscolor_knitAndwoven_greyAndfinish(){
		$Qty=$this->_setQty_knitAndwoven_greyAndfinish($this->_By_OrderBodypartDeterminIdAndGmtscolor);
		return $Qty;
	}
	public function getAmountArray_by_OrderBodypartDeterminIdAndGmtscolor_knitAndwoven_greyAndfinish(){
		$Amount=$this->_setAmount_knitAndwoven_greyAndfinish($this->_By_OrderBodypartDeterminIdAndGmtscolor);
		return $Amount;
	}
	
	//PO Fab Id,Body partid,Determin Id,Dia
	public function getQtyArray_by_OrderBodypartDeterminIdAndDiaWidth_knitAndwoven_greyAndfinish(){
		$Qty=$this->_setQty_knitAndwoven_greyAndfinish($this->_By_OrderBodypartDeterminIdAndDiaWidth);
		return $Qty;
	}
	
	public function getAmountArray_by_OrderBodypartDeterminIdAndDiaWidth_knitAndwoven_greyAndfinish(){
		$Amount=$this->_setAmount_knitAndwoven_greyAndfinish($this->_By_OrderBodypartDeterminIdAndDiaWidth);
		return $Amount;
	}
	
	//PO Item CountryShip Date,Body partid,Determin Id,Dia Type,Dia,GSM
	public function getQtyArray_by_OrderItemidCountrydateBodypartDeterminIdDiatypeDiaAndGsm_knitAndwoven_greyAndfinish(){
		$Qty=$this->_setQty_knitAndwoven_greyAndfinish($this->_By_OrderItemidCountrydateBodypartDeterminIdDiatypeDiaAndGsm);
		return $Qty;
	}
	
	public function getAmountArray_by_OrderItemidCountrydateBodypartDeterminIdDiatypeDiaAndGsm_knitAndwoven_greyAndfinish(){
		$Amount=$this->_setAmount_knitAndwoven_greyAndfinish($this->_By_OrderItemidCountrydateBodypartDeterminIdDiatypeDiaAndGsm);
		return $Amount;
	}
	
	//Jobno,Color,Body Part id,Deter Id,Dia,Gsm
	public function getQtyArray_by_JobColorBodyTypeDeterminIdGsmAndDiaWidth_knitAndwoven_greyAndfinish(){
		$Qty=$this->_setQty_knitAndwoven_greyAndfinish($this->_By_JobColorBodyTypeDeterminIdGsmAndDiaWidth);
		return $Qty;
	}
	
	public function getAmountArray_by_JobColorBodyTypeDeterminIdGsmAndDiaWidth_knitAndwoven_greyAndfinish(){
		$Amount=$this->_setAmount_knitAndwoven_greyAndfinish($this->_By_JobColorBodyTypeDeterminIdGsmAndDiaWidth);
		return $Amount;
	}
	//Jobno,Color,fabric color, Body Part Type,Deter Id,Dia,Gsm
	public function getQtyArray_by_JobColorFabricColorBodyTypeDeterminIdGsmAndDiaWidth_knitAndwoven_greyAndfinish(){
		$Qty=$this->_setQty_knitAndwoven_greyAndfinish($this->_By_JobColorFabricColorBodyTypeDeterminIdGsmAndDiaWidth);
		return $Qty;
	}
	
	public function getAmountArray_by_JobColorFabricColorBodyTypeDeterminIdGsmAndDiaWidth_knitAndwoven_greyAndfinish(){
		$Amount=$this->_setAmount_knitAndwoven_greyAndfinish($this->_By_JobColorFabricColorBodyTypeDeterminIdGsmAndDiaWidth);
		return $Amount;
	}
	//Jobno,Color,fabric color,Gmts Size, Body Part Type,Deter Id,Dia,Gsm
	public function getQtyArray_by_JobColorFabricColorGmtsSizeBodyTypeDeterminIdGsmAndDiaWidth_knitAndwoven_greyAndfinish(){
		$Qty=$this->_setQty_knitAndwoven_greyAndfinish($this->_By_JobColorFabricColorGmtsSizeBodyTypeDeterminIdGsmAndDiaWidth);
		return $Qty;
	}
	
	public function getAmountArray_by_JobColorFabricColorGmtsSizeBodyTypeDeterminIdGsmAndDiaWidth_knitAndwoven_greyAndfinish(){
		$Amount=$this->_setAmount_knitAndwoven_greyAndfinish($this->_By_JobColorFabricColorGmtsSizeBodyTypeDeterminIdGsmAndDiaWidth);
		return $Amount;
	}
	//Jobno,Color,fabric color, Body Part id,Deter Id,Dia,Gsm
	public function getQtyArray_by_JobColorFabricColorBodyPartIdDeterminIdGsmAndDiaWidth_knitAndwoven_greyAndfinish(){
		$Qty=$this->_setQty_knitAndwoven_greyAndfinish($this->_By_JobColorFabricColorBodyPartIdDeterminIdGsmAndDiaWidth);
		return $Qty;
	}
	
	public function getAmountArray_by_JobColorFabricColorBodyPartIdDeterminIdGsmAndDiaWidth_knitAndwoven_greyAndfinish(){
		$Amount=$this->_setAmount_knitAndwoven_greyAndfinish($this->_By_JobColorFabricColorBodyPartIdDeterminIdGsmAndDiaWidth);
		return $Amount;
	}
	//Jobno,Color,Body Part id,Deter Id,Dia
	public function getQtyArray_by_JobColorBodyTypeDeterminIdAndDiaWidth_knitAndwoven_greyAndfinish(){
		$Qty=$this->_setQty_knitAndwoven_greyAndfinish($this->_By_JobColorBodyTypeDeterminIdAndDiaWidth);
		return $Qty;
	}
	
	public function getAmountArray_by_JobColorBodyTypeDeterminIdAndDiaWidth_knitAndwoven_greyAndfinish(){
		$Amount=$this->_setAmount_knitAndwoven_greyAndfinish($this->_By_JobColorBodyTypeDeterminIdAndDiaWidth);
		return $Amount;
	}
	
	//Job no,Color,Fab Color, Body Part id,Deter Id,Dia
	public function getQtyArray_by_JobColorFabColorBodyTypeDeterminIdAndDiaWidth_knitAndwoven_greyAndfinish(){
		$Qty=$this->_setQty_knitAndwoven_greyAndfinish($this->_By_JobColorFabColorBodyTypeDeterminIdAndDiaWidth);
		return $Qty;
	}
	
	public function getAmountArray_by_JobColorFabColorBodyTypeDeterminIdAndDiaWidth_knitAndwoven_greyAndfinish(){
		$Amount=$this->_setAmount_knitAndwoven_greyAndfinish($this->_By_JobColorFabColorBodyTypeDeterminIdAndDiaWidth);
		return $Amount;
	}
	//Job no,Fab Source ,Color,Fab Color, Body Part id,Deter Id,Dia
	public function getQtyArray_by_JobFabSourceColorFabColorBodyTypeDeterminIdAndDiaWidth_knitAndwoven_greyAndfinish(){
		$Qty=$this->_setQty_knitAndwoven_greyAndfinish($this->_By_JobFabSourceColorFabColorBodyTypeDeterminIdAndDiaWidth);
		return $Qty;
	}
	
	public function getAmountArray_by_JobFabSourceColorFabColorBodyTypeDeterminIdAndDiaWidth_knitAndwoven_greyAndfinish(){
		$Amount=$this->_setAmount_knitAndwoven_greyAndfinish($this->_By_JobFabSourceColorFabColorBodyTypeDeterminIdAndDiaWidth);
		return $Amount;
	}
	//Job no,Fab Color, GSM,Deter Id,Dia
	//By_OrderFabColorGsmDeterminIdAndDiaWidth
	public function getQtyArray_by_OrderFabColorGsmDeterminIdAndDiaWidth(){
		$Qty=$this->_setQty_knitAndwoven_greyAndfinish($this->_By_OrderFabColorGsmDeterminIdAndDiaWidth);
		return $Qty;
	}
	
	public function getAmountArray_by_OrderFabColorGsmDeterminIdAndDiaWidth(){
		$Amount=$this->_setAmount_knitAndwoven_greyAndfinish($this->_By_OrderFabColorGsmDeterminIdAndDiaWidth);
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
	//For Sourcing
		public function getAmountArray_by_OrderFabriccostidGmtscolorDiaWidthAndRemarksSourcing_knitAndwoven_greyAndfinish(){
		$Amount=$this->_setAmount_knitAndwoven_greyAndfinish($this->_By_OrderFabriccostidGmtscolorDiaWidthAndRemarksSourcing);
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
		
		//Supplier
	public function getQtyArray_by_orderAndFabriccostidSupplier_knitAndwoven_greyAndfinish(){
		$Qty=$this->_setQty_knitAndwoven_greyAndfinish($this->_By_OrderAndFabriccostidSupplier);
		return $Qty;
	}
	public function getAmountArray_by_orderAndFabriccostidSupplier_knitAndwoven_greyAndfinish(){
		$Amount=$this->_setAmount_knitAndwoven_greyAndfinish($this->_By_OrderAndFabriccostidSupplier);
		return $Amount;
	}
		
		
	public function getQtyArray_by_orderAndConfirmedid_knitAndwoven_greyAndfinish(){
		$Qty=$this->_setQty_knitAndwoven_greyAndfinish($this->_By_OrderAndConfirmedid);
		return $Qty;
	}
	public function getAmountArray_by_orderAndConfirmedid_knitAndwoven_greyAndfinish(){
		$Amount=$this->_setAmount_knitAndwoven_greyAndfinish($this->_By_OrderAndConfirmedid);
		return $Amount;
	}
		
	public function getQtyArray_by_orderConfirmedidAndCountryid_knitAndwoven_greyAndfinish(){
		$Qty=$this->_setQty_knitAndwoven_greyAndfinish($this->_By_OrderConfirmedidAndCountryid);
		return $Qty;
	}
	public function getAmountArray_by_orderConfirmedidAndCountryid_knitAndwoven_greyAndfinish(){
		$Amount=$this->_setAmount_knitAndwoven_greyAndfinish($this->_By_OrderConfirmedidAndCountryid);
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
	
	//PO Color Type&Color
	public function getQtyArray_by_orderColorTypeAndGmtscolor_knitAndwoven_greyAndfinish(){
		$Qty=$this->_setQty_knitAndwoven_greyAndfinish($this->_By_OrderColorTypeAndGmtscolor);
		return $Qty;
	}
	public function getAmountArray_by_orderColorTypeAndGmtscolor_knitAndwoven_greyAndfinish(){
		$Amount=$this->_setAmount_knitAndwoven_greyAndfinish($this->_By_OrderColorTypeAndGmtscolor);
		return $Amount;
	}
	//PO Color Type&Color
	public function getQtyArray_by_orderColorType_knitAndwoven_greyAndfinish(){
		$Qty=$this->_setQty_knitAndwoven_greyAndfinish($this->_By_OrderColorType);
		return $Qty;
	}
	public function getAmountArray_by_orderColorType_knitAndwoven_greyAndfinish(){
		$Amount=$this->_setAmount_knitAndwoven_greyAndfinish($this->_By_OrderColorType);
		return $Amount;
	}
	//JobNo &Color
	public function getQtyArray_by_JobAndGmtscolor_knitAndwoven_greyAndfinish(){
		$Qty=$this->_setQty_knitAndwoven_greyAndfinish($this->_By_JobAndGmtscolor);
		return $Qty;
	}
	public function getAmountArray_by_JobAndGmtscolor_knitAndwoven_greyAndfinish(){
		$Amount=$this->_setAmount_knitAndwoven_greyAndfinish($this->_By_JobAndGmtscolor);
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
	public function getAmountArray_by_FabriccostidSourcing_knitAndwoven_greyAndfinish(){
		$Amount=$this->_setAmount_knitAndwoven_greyAndfinish($this->_By_FabriccostidSourcing);
		return $Amount;
	}
	
	
	public function getQtyArray_by_CountryAndCutupdate_knitAndwoven_greyAndfinish(){
		$Qty=$this->_setQty_knitAndwoven_greyAndfinish($this->_By_CountryAndCutupdate);
		return $Qty;
	}
	public function getQtyArray_by_CountryCutupdateAndGmtsColor_knitAndwoven_greyAndfinish(){
		$Qty=$this->_setQty_knitAndwoven_greyAndfinish($this->_By_CountryCutupdateAndGmtsColor);
		return $Qty;
	}
	
	public function getQtyArray_by_orderAndFabricSource_knitAndwoven_greyAndfinish(){
		$Qty=$this->_setQty_knitAndwoven_greyAndfinish($this->_By_OrderAndFabricSource);
		return $Qty;
	}
	
	public function getAmountArray_by_orderAndFabricSource_knitAndwoven_greyAndfinish(){
		$Amount=$this->_setAmount_knitAndwoven_greyAndfinish($this->_By_OrderAndFabricSource);
		return $Amount;
	}
	//Order color Type, Fab source
	public function getQtyArray_by_orderAndColorTypeFabricSource_knitAndwoven_greyAndfinish(){
		$Qty=$this->_setQty_knitAndwoven_greyAndfinish($this->_By_OrderAndColorTypeFabricSource);
		return $Qty;
	}
	
	public function getAmountArray_by_orderAndColorTypeFabricSource_knitAndwoven_greyAndfinish(){
		$Amount=$this->_setAmount_knitAndwoven_greyAndfinish($this->_By_OrderAndColorTypeFabricSource);
		return $Amount;
	}
	
	public function getQtyArray_by_orderAndSourceId_knitAndwoven_greyAndfinish(){
		$Qty=$this->_setQty_knitAndwoven_greyAndfinish($this->_By_OrderAndSourceId);
		return $Qty;
	}
	
	public function getAmountArray_by_orderAndSourceId_knitAndwoven_greyAndfinish(){
		$Amount=$this->_setAmount_knitAndwoven_greyAndfinish($this->_By_OrderAndSourceId);
		return $Amount;
	}

	public function getQtyArray_by_OrderFabColorGsmDeterminIdBodyPartAndDiaWidth(){
		$Qty=$this->_setQty_knitAndwoven_greyAndfinish($this->_By_OrderFabColorGsmDeterminIdBodyPartAndDiaWidth);
		return $Qty;
	}
	public function getQtyArray_by_JobFabricIdItemBobyPartGmtsColorGsmDia_greyAndfinish(){
		$Qty=$this->_setQty_knitAndwoven_greyAndfinish($this->_By_JobFabricIdItemBobyPartGmtsColorGsmDia);
		return $Qty;
	}
	
	
	
	function __destruct() {
		parent::__destruct();
		unset($this->_dataArray);
	}
}
?>