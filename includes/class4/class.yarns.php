<?
class yarn extends report{
	private $_query="";
	private $_query2="";
	private $_dataArray=array();
	private $_dataArray2=array();
	private $_stripeColorReqArray=array();
	private $_is_sweater=0;
	private $_is_approval_histry=0;
	// class constructor
	function __construct(condition $condition,$is_sweater=0,$is_approval_histry=0){
		parent::__construct($condition);
		$this->_is_sweater=$is_sweater;
		$this->_is_approval_histry=$is_approval_histry;
		$this->_setQuery();
		$this->_setData();
		$this->_setStripeColorArray();
	}// end class constructor
	
	private function _setQuery(){
		//$jobcond=$this->_setJobsString($this->_jobs,'a.job_no');
		if($this->_is_approval_histry==2 && $this->_is_sweater==0 ){

			$this->_query='select a.job_no AS "job_no", a.garments_nature AS "garments_nature", b.po_id AS "id", c.item_number_id AS "item_number_id", c.country_id AS "country_id", c.color_number_id AS "color_number_id", c.size_number_id AS "size_number_id", c.order_quantity AS "order_quantity", c.plan_cut_qnty AS "plan_cut_qnty", c.country_ship_date AS "country_ship_date", d.pre_cost_fabric_cost_dtls_id AS "pre_cost_dtls_id", d.fab_nature_id AS "fab_nature_id", d.construction AS "construction", d.gsm_weight AS "gsm_weight", d.fab_nature_id AS "fab_nature_id", d.color_type_id AS "color_type_id", d.budget_on AS "budget_on", e.cons AS "cons", e.requirment AS "requirment", f.pre_cost_fab_yarn_cost_dtls_id AS "yarn_id", f.count_id AS "count_id", f.copm_one_id AS "copm_one_id", f.percent_one AS "percent_one", f.type_id AS "type_id", f.color AS "color", f.cons_ratio AS "cons_ratio", f.cons_qnty AS "cons_qnty", f.avg_cons_qnty AS "avg_cons_qnty", f.rate AS "rate", f.amount AS "amount", f.supplier_id AS "supplier_id" from wo_po_dtls_mst_his a, wo_po_break_down_his b,wo_po_color_size_his c, wo_pre_cost_fabric_cost_dtls_h d, wo_pre_fab_avg_con_dtls_h e, wo_pre_cost_fab_yarn_cst_dtl_h f where 1=1 '.$this->cond.' and a.job_id=b.job_id and a.approved_no=b.approved_no and a.approval_page=b.approval_page and a.job_id=c.job_id and a.approved_no=c.approved_no and a.approval_page=c.approval_page and a.job_id=d.job_id  and a.approved_no=d.approved_no and a.approval_page=d.approval_page and a.job_id=e.job_id and a.approved_no=e.approved_no and a.approval_page=e.approval_page and a.job_id=f.job_id and a.approved_no=f.approved_no and a.approval_page=f.approval_page and b.po_id=c.po_break_down_id and d.pre_cost_fabric_cost_dtls_id=e.pre_cost_fabric_cost_dtls_id and c.po_break_down_id=e.po_break_down_id and  c.item_number_id= d.item_number_id and c.color_number_id=e.color_number_id and c.size_number_id=e.gmts_sizes and d.pre_cost_fabric_cost_dtls_id=f.fabric_cost_dtls_id and e.cons !=0 and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 and c.is_deleted=0 and c.status_active=1 and d.is_deleted=0 and d.status_active=1 and e.is_deleted=0 and e.status_active=1 and f.is_deleted=0 and f.status_active=1';//order by b.id,d.id
		
	 $this->_query2='select h.pre_cost_fabric_cost_dtls_id AS "pre_cost_fabric_cost_dtls_id", h.color_number_id AS "gmts_color_id", h.stripe_color AS "stripe_color", h.measurement AS "fabreq" from wo_po_dtls_mst_his a,wo_pre_stripe_color_h h where h.yarn_dyed=1 and h.status_active=1 '.$this->jobtablecond.' and a.id=h.job_id  and a.approved_no=h.approved_no and a.approval_page=h.approval_page ';
		}
		else{
			$this->_query='select a.job_no AS "job_no", a.garments_nature AS "garments_nature", b.id AS "id", c.item_number_id AS "item_number_id", c.country_id AS "country_id", c.color_number_id AS "color_number_id", c.size_number_id AS "size_number_id", c.order_quantity AS "order_quantity", c.plan_cut_qnty AS "plan_cut_qnty", c.country_ship_date AS "country_ship_date", d.id AS "pre_cost_dtls_id", d.fab_nature_id AS "fab_nature_id", d.construction AS "construction", d.gsm_weight AS "gsm_weight", d.fab_nature_id AS "fab_nature_id", d.color_type_id AS "color_type_id", d.budget_on AS "budget_on", e.cons AS "cons", e.requirment AS "requirment", f.id AS "yarn_id", f.count_id AS "count_id", f.copm_one_id AS "copm_one_id", f.percent_one AS "percent_one", f.type_id AS "type_id", f.color AS "color", f.cons_ratio AS "cons_ratio", f.cons_qnty AS "cons_qnty", f.avg_cons_qnty AS "avg_cons_qnty", f.rate AS "rate", f.amount AS "amount", f.supplier_id AS "supplier_id" from wo_po_details_master a, wo_po_break_down b,wo_po_color_size_breakdown c, wo_pre_cost_fabric_cost_dtls d, wo_pre_cos_fab_co_avg_con_dtls e, wo_pre_cost_fab_yarn_cost_dtls f where 1=1 '.$this->cond.' and a.id=b.job_id and a.id=c.job_id and a.id=d.job_id and a.id=e.job_id and a.id=f.job_id and b.id=c.po_break_down_id and d.id=e.pre_cost_fabric_cost_dtls_id and c.po_break_down_id=e.po_break_down_id and  c.item_number_id= d.item_number_id and c.color_number_id=e.color_number_id and c.size_number_id=e.gmts_sizes and d.id=f.fabric_cost_dtls_id and e.cons !=0 and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 and c.is_deleted=0 and c.status_active=1 and d.is_deleted=0 and d.status_active=1 and e.is_deleted=0 and e.status_active=1 and f.is_deleted=0 and f.status_active=1';//order by b.id,d.id
		
	 $this->_query2='select h.pre_cost_fabric_cost_dtls_id AS "pre_cost_fabric_cost_dtls_id", h.color_number_id AS "gmts_color_id", h.stripe_color AS "stripe_color", h.measurement AS "fabreq" from wo_po_details_master a,wo_pre_stripe_color h where yarn_dyed=1 and h.status_active=1 '.$this->jobtablecond.' and a.id=h.job_id';
		}
		

	}
	
	public function getQuery(){
		return $this->_query;
	}
	
	private function _setData() {
		$this->_dataArray=sql_select($this->_query);
		$this->_dataArray2=sql_select($this->_query2);
		return $this;
	}
	
	public function getData() {
		return $this->_dataArray;
	}
	public function _setStripeColorArray(){
		foreach($this->_dataArray2 as $row){
			$pre_cost_fabric_cost_dtls_id=$row['pre_cost_fabric_cost_dtls_id'];
			$gmts_color_id=$row['gmts_color_id'];
			$stripe_color=$row['stripe_color'];
			$fabreq=$row['fabreq'];
			//echo $pre_cost_fabric_cost_dtls_id.'='.$gmts_color_id.'='.$stripe_color.'='.$fabreq.'<br>';
			
			$this->_stripeColorReqArray[$pre_cost_fabric_cost_dtls_id][$gmts_color_id][$stripe_color]=$fabreq;
		}
	}
	
	private function _calculateYarnQty($plan_cut_qnty,$costingPerQty,$set_item_ratio,$cons_qnty){
	  //return $reqyarnqnty =($plan_cut_qnty/($costingPerQty*$set_item_ratio))*$cons_qnty;
	  return $reqyarnqnty =($plan_cut_qnty/$set_item_ratio)*($cons_qnty/$costingPerQty);
	}
	
	private function _calculateYarnAmount($reqyarnqnty,$rate){
	 return $yarnamount=$reqyarnqnty*$rate;
	}
	
	private function _setYarnQty($level){
		
		$jobNo='';
		$itemNumberId='';
		$planPutQnty=0;
		$orderQnty=0;
		$countryShipDate='';
		$requirment=0;$fabnatureid='';$colornumberid='';$garments_natureId='';$color_type_id='';
		$poId='';$supplierId='';
		$countryId='';
		$yarnId='';
		$countId='';
		$copmOneId='';
		$percentOne='';
		$color='';
		$typeId='';
		$consRatio=0;
		$rate='';
		//$gmtsitemRatioArray=$this->_gmtsitemRatioArray;
		//$costingPerArray=$this->_costingPerQtyArr;
		$budget_on=2;
		$yarnQty=array();
		
		foreach($this->_dataArray as $row)
		{
			$jobNo=$row['job_no'];
			$itemNumberId=$row['item_number_id'];
			$colornumberid=$row['color_number_id'];
			$orderQnty=$row['order_quantity'];
			$planPutQnty=$row['plan_cut_qnty'];
			$countryShipDate=$row['country_ship_date'];
			
			$construction=$row['construction'];
			$gsm_weight=$row['gsm_weight'];
			$color_type_id=$row['color_type_id'];
			$poId=$row['id'];
			$supplierId=$row['supplier_id'];
			$countryId=$row['country_id'];
			$precostdtlsid=$row['pre_cost_dtls_id'];
			$fabnatureid=$row['fab_nature_id'];
			$garments_natureId=$row['garments_nature'];
			$yarnId=$row['yarn_id'];
			$countId=$row['count_id'];
			$copmOneId=$row['copm_one_id'];
			$percentOne=$row['percent_one'];
			$color=$row['color'];
			$typeId=$row['type_id'];
			$consRatio=$row['cons_ratio'];
			$rate=$row["rate"];
			$budget_on=$row['budget_on'];
			if($budget_on==0 || $budget_on=="") $budget_on=2;
			//echo $precostdtlsid.'DXXAAAAAAAAAAAAAA';
			//$consQnty=$row[csf('cons_qnty')];
			 
			if($garments_natureId==100) //Sweater
			{
				$lbs_val=2.20462;
				$stripe_cons_dzn=$this->_stripeColorReqArray[$precostdtlsid][$colornumberid][$color];
			//	print_r($stripe_cons_dzn);
				$requirment=$stripe_cons_dzn*$lbs_val;
			}
			else
			{
				$requirment=$row['requirment'];
			}
			
			
			$consQnty=$requirment*$consRatio/100;
			
			$costingPerQty=$this->_costingPerQtyArr[$jobNo];
			$set_item_ratio=$this->_gmtsitemRatioArray[$jobNo][$itemNumberId];
			if($budget_on==1)
				$reqyarnqnty =$this->_calculateYarnQty($orderQnty,$costingPerQty,$set_item_ratio,$consQnty);
			else
				$reqyarnqnty =$this->_calculateYarnQty($planPutQnty,$costingPerQty,$set_item_ratio,$consQnty);
			//echo $reqyarnqnty.'='.$requirment.'D';
			if($level=='job_wise'){
				$yarnQty[$jobNo]+=$reqyarnqnty;
			}
			elseif($level=='By_YarnId'){
				$yarnQty[$yarnId]+=$reqyarnqnty;
			}
			elseif($level=='By_FabricId'){
				$yarnQty[$precostdtlsid]+=$reqyarnqnty;
			}
			elseif($level=='JobCountCompositionColorAndTypeWise'){
				$yarnQty[$jobNo][$countId][$copmOneId][$color][$typeId]+=$reqyarnqnty;
			}
			elseif($level=='JobCountCompositionPercentAndTypeWise'){
				$yarnQty[$jobNo][$countId][$copmOneId][$percentOne][$typeId]+=$reqyarnqnty;
			}
			elseif($level=='order_wise'){
				$yarnQty[$poId]+=$reqyarnqnty;
			}
			elseif($level=='orderColorType_wise'){
				$yarnQty[$poId][$color_type_id]+=$reqyarnqnty;
			}
			elseif($level=='order_and_gmtsItem_wise'){
				$yarnQty[$poId][$itemNumberId]+=$reqyarnqnty;
			}
			elseif($level=='order_and_gmtsColor_wise'){
				$yarnQty[$poId][$color]+=$reqyarnqnty;
			}
			elseif($level=='order_and_country_wise'){
				$yarnQty[$poId][$countryId]+=$reqyarnqnty;
			}
			elseif($level=='OrderCountCompositionColorAndTypeWise'){
				$yarnQty[$poId][$countId][$copmOneId][$color][$typeId]+=$reqyarnqnty;
			}
			elseif($level=='OrderCountCompositionPercentAndTypeWise'){
				$yarnQty[$poId][$countId][$copmOneId][$percentOne][$typeId]+=$reqyarnqnty;
			}
			elseif($level=='OrderCountAndCompositionWise'){
				$yarnQty[$poId][$countId][$copmOneId]+=$reqyarnqnty;
			}
			elseif($level=='CountCompositionAndTypeWise'){
				$yarnQty[$countId][$copmOneId][$percentOne][$typeId]+=$reqyarnqnty;
			}
			elseif($level=='order_and_gmtsItem_fabricId_Count_Composition_AndType_wise'){
				$yarnQty[$poId][$itemNumberId][$precostdtlsid][$countId][$copmOneId][$typeId]+=$reqyarnqnty;
			}
			elseif($level=='order_and_construction_gsm_weight_Count_wise'){
				$yarnQty[$poId][$construction][$gsm_weight][$countId]+=$reqyarnqnty;
			}
			elseif($level=='CountCompositionPercentTypeColorAndRateWise'){
				$yarnQty[$countId][$copmOneId][$percentOne][$typeId][$color]["$rate"]+=$reqyarnqnty;
			}
			else{
				return null;
			}
		}
		return $yarnQty;
	}
	
	private function _setYarnAmount($level){
		$jobNo='';
		$itemNumberId='';
		$orderQnty=$planPutQnty=0;
		$countryShipDate=0;
		$requirment=0;
		$rate=0;
		$poId='';$supplierId='';
		$countryId='';
		$yarnId='';
		$countId='';
		$copmOneId='';
		$percentOne='';
		$color='';
		$typeId='';$fabnatureid='';$colornumberid='';$garments_natureId='';$color_type_id='';
		$consRatio=0;
		$budget_on=2;
		//$gmtsitemRatioArray=$this->_gmtsitemRatioArray;
		//$costingPerArray=$this->_costingPerQtyArr;
		$yarnamount_arr=array();
		foreach($this->_dataArray as $row)
		{
			$jobNo=$row['job_no'];
			$itemNumberId=$row['item_number_id'];
			$colornumberid=$row['color_number_id'];
			$orderQnty=$row['order_quantity'];
			$planPutQnty=$row['plan_cut_qnty'];
			$countryShipDate=$row['country_ship_date'];
		//	$requirment=$row['requirment'];
			$rate=$row["rate"];
			$poId=$row['id'];
			$supplierId=$row['supplier_id'];
			$countryId=$row['country_id'];
			$precostdtlsid=$row['pre_cost_dtls_id'];
			$fabnatureid=$row['fab_nature_id'];
			$color_type_id=$row['color_type_id'];
			$garments_natureId=$row['garments_nature'];
			$yarnId=$row['yarn_id'];
			$countId=$row['count_id'];
			$copmOneId=$row['copm_one_id'];
			$percentOne=$row['percent_one'];
			$color=$row['color'];
			$typeId=$row['type_id'];
			$consRatio=$row['cons_ratio'];
			$budget_on=$row['budget_on'];
			if($budget_on==0 || $budget_on=="") $budget_on=2;
			//echo $fabnatureid.'DSSSSSSSSSSSSSS';
			if($garments_natureId==100) //Sweater
			{
				$lbs_val=2.20462;
				$stripe_cons_dzn=$this->_stripeColorReqArray[$precostdtlsid][$colornumberid][$color];
				$requirment=$stripe_cons_dzn*$lbs_val;
			}
			else
			{
				$requirment=$row['requirment'];
			}
			
			
			//$consQnty=$row[csf('cons_qnty')];
			$consQnty=$requirment*$consRatio/100;
			
			//$costingPerQty=$this->_costingPer($costingPerArray[$jobNo]);
			//$set_item_ratio=$gmtsitemRatioArray[$jobNo][$itemNumberId];
			//$costingPerQty=$this->_costingPer($this->_costingPerQtyArr[$jobNo]);
			$costingPerQty=$this->_costingPerQtyArr[$jobNo];
			$set_item_ratio=$this->_gmtsitemRatioArray[$jobNo][$itemNumberId];
			if($budget_on==1)
				$reqyarnqnty =$this->_calculateYarnQty($orderQnty,$costingPerQty,$set_item_ratio,$consQnty);
			else
				$reqyarnqnty =$this->_calculateYarnQty($planPutQnty,$costingPerQty,$set_item_ratio,$consQnty);
			$yarnamount=$this->_calculateYarnAmount($reqyarnqnty,$rate);
			if($level=='job_wise'){
				$yarnamount_arr[$jobNo]+=$yarnamount;
			}
			elseif($level=='By_YarnId'){
				$yarnamount_arr[$yarnId]+=$yarnamount;
			}
			elseif($level=='By_FabricId'){
				$yarnamount_arr[$precostdtlsid]+=$yarnamount;
			}
			elseif($level=='JobCountCompositionColorAndTypeWise'){
				$yarnamount_arr[$jobNo][$countId][$copmOneId][$color][$typeId]+=$yarnamount;
			}
			elseif($level=='JobCountCompositionColorAndTypeSupplierWise'){
				$yarnamount_arr[$jobNo][$countId][$copmOneId][$color][$typeId][$supplierId]+=$yarnamount;
			}
			elseif($level=='JobCountCompositionPercentAndTypeWise'){
				$yarnamount_arr[$jobNo][$countId][$copmOneId][$percentOne][$typeId]+=$yarnamount;
			}
			
			elseif($level=='order_wise'){
				$yarnamount_arr[$poId]+=$yarnamount;
			}
			elseif($level=='orderColorType_wise'){
				$yarnamount_arr[$poId][$color_type_id]+=$yarnamount;
			}
			elseif($level=='order_and_gmtsItem_wise'){
				$yarnamount_arr[$poId][$itemNumberId]+=$yarnamount;
			}
			elseif($level=='order_and_gmtsColor_wise'){
				$yarnamount_arr[$poId][$color]+=$yarnamount;
			}
			elseif($level=='order_and_country_wise'){
				$yarnamount_arr[$poId][$countryId]+=$yarnamount;
			}
			elseif($level=='OrderCountCompositionColorAndTypeWise'){
				$yarnamount_arr[$poId][$countId][$copmOneId][$color][$typeId]+=$yarnamount;
			}
			elseif($level=='OrderCountCompositionPercentAndTypeWise'){ //
				$yarnamount_arr[$poId][$countId][$copmOneId][$percentOne][$typeId]+=$yarnamount;
			}
			elseif($level=='OrderCountAndCompositionWise'){ //OrderCountAndCompositionWise
				$yarnamount_arr[$poId][$countId][$copmOneId]+=$yarnamount;
			}
			
			elseif($level=='CountCompositionAndTypeWise'){
				$yarnamount_arr[$countId][$copmOneId][$percentOne][$typeId]+=$yarnamount;
			}
			elseif($level=='CountCompositionPercentTypeColorAndRateWise'){
				$yarnamount_arr[$countId][$copmOneId][$percentOne][$typeId][$color]["$rate"]+=$yarnamount;
			}
			else{
				return null;
			}
		}
		return $yarnamount_arr;
	}
	
	private function _setYarnQtyAndAmount($level){
		
		$jobNo='';
		$itemNumberId='';
		$planPutQnty=0;
		$countryShipDate=0;
		$requirment=0;
		$rate=0;
		$poId='';
		$countryId='';
		$yarnId='';
		$countId='';$supplierId='';$garments_natureId='';$color_type_id='';
		$copmOneId='';
		$percentOne='';
		$color='';
		$typeId='';
		$consRatio=0;
		$sizeNumberId='';$budget_on=2;$orderQnty=0;
		
		//$gmtsitemRatioArray=$this->_gmtsitemRatioArray;
		//$costingPerArray=$this->_costingPerQtyArr;
		$yarnqtyandamount_arr=array();
		foreach($this->_dataArray as $row)
		{
			/*$jobNo=$row[csf('job_no')];
			$itemNumberId=$row[csf('item_number_id')];
			$planPutQnty=$row[csf('plan_cut_qnty')];
			$requirment=$row[csf('requirment')];
			$rate=$row[csf("rate")];
			$poId=$row[csf('id')];
			$countryId=$row[csf('country_id')];
			$countId=$row[csf('count_id')];
			$copmOneId=$row[csf('copm_one_id')];
			$percentOne=$row[csf('percent_one')];
			$color=$row[csf('color')];
			$typeId=$row[csf('type_id')];
			$consRatio=$row[csf('cons_ratio')];
			$sizeNumberId=$row[csf('size_number_id')];*/
			
			$jobNo=$row['job_no'];
			$itemNumberId=$row['item_number_id'];
			$fab_nature_id=$row['fab_nature_id'];
			$color_type_id=$row['color_type_id'];
			$color_number_id=$row['color_number_id'];
			$planPutQnty=$row['plan_cut_qnty'];
			$orderQnty=$row['order_quantity'];
			$countryShipDate=$row['country_ship_date'];
			$requirment=$row['requirment'];
			$rate=$row["rate"];
			$poId=$row['id'];
			$supplierId=$row['supplier_id'];
			$countryId=$row['country_id'];
			$precostdtlsid=$row['pre_cost_dtls_id'];
			$yarnId=$row['yarn_id'];
			$countId=$row['count_id'];
			$garments_natureId=$row['garments_nature'];
			$copmOneId=$row['copm_one_id'];
			$percentOne=$row['percent_one'];
			$color=$row['color'];
			$typeId=$row['type_id'];
			$consRatio=$row['cons_ratio'];
			$sizeNumberId=$row['size_number_id'];
			//echo $fab_nature_id.'DS';
			if($garments_natureId==100) //Sweater
			{
				$lbs_val=2.20462;
				$stripe_cons_dzn=$this->_stripeColorReqArray[$precostdtlsid][$color_number_id][$color];
			//	print_r($stripe_cons_dzn);
				$requirment=$stripe_cons_dzn*$lbs_val;
			}
			else
			{
				$requirment=$row['requirment'];
			}
			
			
			//$consQnty=$row[csf('cons_qnty')];
			$consQnty=$requirment*$consRatio/100;
			
			//$costingPerQty=$this->_costingPer($costingPerArray[$jobNo]);
			//$set_item_ratio=$gmtsitemRatioArray[$jobNo][$itemNumberId];
			
			$costingPerQty=$this->_costingPerQtyArr[$jobNo];
			$set_item_ratio=$this->_gmtsitemRatioArray[$jobNo][$itemNumberId];
			
			
			if($budget_on==1)
			{
				$reqyarnqnty =$this->_calculateYarnQty($orderQnty,$costingPerQty,$set_item_ratio,$consQnty);
			}
			else{
				$reqyarnqnty =$this->_calculateYarnQty($planPutQnty,$costingPerQty,$set_item_ratio,$consQnty);
			}
			$yarnamount=$this->_calculateYarnAmount($reqyarnqnty,$rate);
			if($level=='job_wise'){
				$yarnqtyandamount_arr[$jobNo]['qty']+=$reqyarnqnty;
				$yarnqtyandamount_arr[$jobNo]['amount']+=$yarnamount;
			}
			elseif($level=='By_YarnId'){
				$yarnqtyandamount_arr[$yarnId]['qty']+=$reqyarnqnty;
				$yarnqtyandamount_arr[$yarnId]['amount']+=$yarnamount;
			}
			elseif($level=='By_FabricId'){
				$yarnqtyandamount_arr[$precostdtlsid]['qty']+=$reqyarnqnty;
				$yarnqtyandamount_arr[$precostdtlsid]['amount']+=$yarnamount;
			}
			elseif($level=='JobCountCompositionColorAndTypeWise'){
				$yarnqtyandamount_arr[$jobNo][$countId][$copmOneId][$color][$typeId]['qty']+=$reqyarnqnty;
				$yarnqtyandamount_arr[$jobNo][$countId][$copmOneId][$color][$typeId]['amount']+=$yarnamount;
			}
			elseif($level=='JobCountCompositionColorAndTypeSupplierWise'){
				$yarnqtyandamount_arr[$jobNo][$countId][$copmOneId][$color][$typeId][$supplierId]['qty']+=$reqyarnqnty;
				$yarnqtyandamount_arr[$jobNo][$countId][$copmOneId][$color][$typeId][$supplierId]['amount']+=$yarnamount;
			}
			elseif($level=='JobCountCompositionPercentAndTypeWise'){
				$yarnqtyandamount_arr[$jobNo][$countId][$copmOneId][$percentOne][$typeId]['qty']+=$reqyarnqnty;
				$yarnqtyandamount_arr[$jobNo][$countId][$copmOneId][$percentOne][$typeId]['amount']+=$yarnamount;
			}
			
			elseif($level=='order_wise'){
				$yarnqtyandamount_arr[$poId]['qty']+=$reqyarnqnty;
				$yarnqtyandamount_arr[$poId]['amount']+=$yarnamount;
			}
			elseif($level=='orderColorType_wise'){
				$yarnqtyandamount_arr[$poId][$color_type_id]['qty']+=$reqyarnqnty;
				$yarnqtyandamount_arr[$poId][$color_type_id]['amount']+=$yarnamount;
			}
			elseif($level=='order_and_gmtsItem_wise'){
				$yarnqtyandamount_arr[$poId][$itemNumberId]['qty']+=$reqyarnqnty;
				$yarnqtyandamount_arr[$poId][$itemNumberId]['amount']+=$yarnamount;
			}
			elseif($level=='order_and_gmtsColor_wise'){
				$yarnqtyandamount_arr[$poId][$color]['qty']+=$reqyarnqnty;
				$yarnqtyandamount_arr[$poId][$color]['amount']+=$yarnamount;
			}
			elseif($level=='order_and_country_wise'){
				$yarnqtyandamount_arr[$poId][$countryId]['qty']+=$reqyarnqnty;
				$yarnqtyandamount_arr[$poId][$countryId]['amount']+=$yarnamount;
			}
			elseif($level=='OrderCountCompositionColorAndTypeWise'){
				$yarnqtyandamount_arr[$poId][$countId][$copmOneId][$color][$typeId]['qty']+=$reqyarnqnty;
				$yarnqtyandamount_arr[$poId][$countId][$copmOneId][$color][$typeId]['amount']+=$yarnamount;
			}
			elseif($level=='OrderCountCompositionPercentAndTypeWise'){ 
				$yarnqtyandamount_arr[$poId][$countId][$copmOneId][$percentOne][$typeId]['qty']+=$reqyarnqnty;
				$yarnqtyandamount_arr[$poId][$countId][$copmOneId][$percentOne][$typeId]['amount']+=$yarnamount;
			}
			elseif($level=='OrderCountAndCompositionWise'){ 
				$yarnqtyandamount_arr[$poId][$countId][$copmOneId]['qty']+=$reqyarnqnty;
				$yarnqtyandamount_arr[$poId][$countId][$copmOneId]['amount']+=$yarnamount;
			}
			elseif($level=='OrderCountryshipdateCountCompositionPercentAndTypeWise'){
				$yarnqtyandamount_arr[$poId][$countryShipDate][$countId][$copmOneId][$percentOne][$typeId]['qty']+=$reqyarnqnty;
				$yarnqtyandamount_arr[$poId][$countryShipDate][$countId][$copmOneId][$percentOne][$typeId]['amount']+=$yarnamount;
			}
			
			elseif($level=='CountCompositionAndTypeWise'){
				$yarnqtyandamount_arr[$countId][$copmOneId][$percentOne][$typeId]['qty']+=$reqyarnqnty;
				$yarnqtyandamount_arr[$countId][$copmOneId][$percentOne][$typeId]['amount']+=$yarnamount;
			}
			elseif($level=='CountCompositionPercentTypeColorAndRateWise'){
				$yarnqtyandamount_arr[$countId][$copmOneId][$percentOne][$typeId][$color]["$rate"]['qty']+=$reqyarnqnty;
				$yarnqtyandamount_arr[$countId][$copmOneId][$percentOne][$typeId][$color]["$rate"]['amount']+=$yarnamount;
			}
			elseif($level=='OrderCountCompositionColorTypeAndConsumptionWiseDataArray'){
				$yarnqtyandamount_arr[$poId][$countId][$copmOneId][$percentOne][$color][$typeId]["$consQnty"]['qty']+=$reqyarnqnty;
				$yarnqtyandamount_arr[$poId][$countId][$copmOneId][$percentOne][$color][$typeId]["$consQnty"]['rate']=$rate;
				$yarnqtyandamount_arr[$poId][$countId][$copmOneId][$percentOne][$color][$typeId]["$consQnty"]['amount']+=$yarnamount;
				$yarnqtyandamount_arr[$poId][$countId][$copmOneId][$percentOne][$color][$typeId]["$consQnty"]['planPutQnty']+=$planPutQnty;
				$yarnqtyandamount_arr[$poId][$countId][$copmOneId][$percentOne][$color][$typeId]["$consQnty"]['fabCons']=$requirment;
				$yarnqtyandamount_arr[$poId][$countId][$copmOneId][$percentOne][$color][$typeId]["$consQnty"]['yratio']=$consRatio;
				$yarnqtyandamount_arr[$poId][$countId][$copmOneId][$percentOne][$color][$typeId]["$consQnty"]['gmtsSize'][$sizeNumberId]=$sizeNumberId;
			}
			
			elseif($level=='OrderCountCompositionPercentTypeColorAndRateWise'){
				$yarnqtyandamount_arr[$poId][$countId][$copmOneId][$percentOne][$typeId][$color]["$rate"]['qty']+=$reqyarnqnty;
				$yarnqtyandamount_arr[$poId][$countId][$copmOneId][$percentOne][$typeId][$color]["$rate"]['amount']+=$yarnamount;
			}
			else{
				return null;
			}
		}
		return $yarnqtyandamount_arr;
	}
	
	public function unsetDataArray(){
		$this->_dataArray=array();
	}
	
	public function getJobWiseYarnQty($jobNo){
		$jobWiseYarnQty=$this->_setYarnQty('job_wise');
		return $jobWiseYarnQty[$jobNo];
	}
	
	public function getJobWiseYarnQtyArray(){
		$jobWiseYarnQty=$this->_setYarnQty('job_wise');
		return $jobWiseYarnQty;
	}
	public function get_By_Precostdtlsid_YarnQtyArray(){
		$YarnIdWiseYarnQty=$this->_setYarnQty('By_YarnId');
		return $YarnIdWiseYarnQty;
	}
	public function get_By_Precostfabricdtlsid_YarnQtyArray(){
		$FabricIdWiseYarnQty=$this->_setYarnQty('By_FabricId');
		return $FabricIdWiseYarnQty;
	}
	
	
	public function getJobCountCompositionColorAndTypeWiseYarnQty($jobNo,$count,$composition,$color,$type){
		$jobCountCompositionColorAndTypeWiseYarnQty=$this->_setYarnQty('JobCountCompositionColorAndTypeWise');
		return $jobCountCompositionColorAndTypeWiseYarnQty[$jobNo][$count][$composition][$color][$type];
	}
	public function getJobCountCompositionColorAndTypeWiseYarnQtyArray(){
		$jobCountCompositionColorAndTypeWiseYarnQty=$this->_setYarnQty('JobCountCompositionColorAndTypeWise');
		return $jobCountCompositionColorAndTypeWiseYarnQty;
	}
	public function getJobCountCompositionColorAndTypeSupplierWiseYarnQty($jobNo,$count,$composition,$color,$type,$supplierId){
		$jobCountCompositionColorAndTypeSupplierWiseYarnQty=$this->_setYarnQty('JobCountCompositionColorAndTypeSupplierWise');
		return $jobCountCompositionColorAndTypeSupplierWiseYarnQty[$jobNo][$count][$composition][$color][$type][$supplierId];
	}
	public function getJobCountCompositionColorAndTypeSupplierWiseYarnQtyArray(){
		$jobCountCompositionColorAndTypeSupplierWiseYarnQty=$this->_setYarnQty('JobCountCompositionColorAndTypeSupplierWise');
		return $jobCountCompositionColorAndTypeSupplierWiseYarnQty;
	}
	
	public function get_order_and_gmtsItem_fabricId_Count_Composition_AndType_wise_QtyArray(){
		$jobCountCompositionColorAndTypeWiseYarnQty=$this->_setYarnQty('order_and_gmtsItem_fabricId_Count_Composition_AndType_wise');
		return $jobCountCompositionColorAndTypeWiseYarnQty;
	}
	public function get_order_and_construction_gsm_weight_Count_wise_QtyArray(){
		$jobCountCompositionColorAndTypeWiseYarnQty=$this->_setYarnQty('order_and_construction_gsm_weight_Count_wise');
		return $jobCountCompositionColorAndTypeWiseYarnQty;
	}
	
	public function getJobCountCompositionColorAndTypeWiseYarnAmount($jobNo,$count,$composition,$color,$type){
		$jobCountCompositionColorAndTypeWiseYarnAmount=$this->_setYarnAmount('JobCountCompositionColorAndTypeWise');
		return $jobCountCompositionColorAndTypeWiseYarnAmount[$jobNo][$count][$composition][$color][$type];
	}
	
	public function getJobCountCompositionColorAndTypeWiseYarnAmountArray(){
		$jobCountCompositionColorAndTypeWiseYarnAmount=$this->_setYarnAmount('JobCountCompositionColorAndTypeWise');
		return $jobCountCompositionColorAndTypeWiseYarnAmount;
	}
	public function getJobCountCompositionColorAndTypeWiseYarnQtyAndAmount($jobNo,$count,$composition,$color,$type){
		$jobCountCompositionColorAndTypeYarnQtyAndAount=$this->_setYarnQtyAndAmount('JobCountCompositionColorAndTypeWise');
		return $jobCountCompositionColorAndTypeYarnQtyAndAount[$jobNo][$count][$composition][$color][$type];
		
	}
	
	public function getJobCountCompositionColorAndTypeWiseYarnQtyAndAmountArray(){
		$jobAndGmtsItemWiseYarnQtyAndAount=$this->_setYarnQtyAndAmount('JobCountCompositionColorAndTypeWise');
		return $jobAndGmtsItemWiseYarnQtyAndAount;
	}
	
	//Job Count,Comp, Color,Type,Supplier wise
	public function getJobCountCompositionColorAndTypeSupplierWiseYarnAmount($jobNo,$count,$composition,$color,$type,$supplierId){
		$jobCountCompositionColorAndTypeSupplierWiseYarnAmount=$this->_setYarnAmount('JobCountCompositionColorAndTypeSupplierWise');
		return $jobCountCompositionColorAndTypeSupplierWiseYarnAmount[$jobNo][$count][$composition][$color][$type][$supplierId];
	}
	
	public function getJobCountCompositionColorAndTypeSupplierWiseYarnAmountArray(){
		$jobCountCompositionColorAndTypeSupplierWiseYarnAmount=$this->_setYarnAmount('JobCountCompositionColorAndTypeSupplierWise');
		return $jobCountCompositionColorAndTypeSupplierWiseYarnAmount;
	}
	public function getJobCountCompositionColorAndTypeSupplierWiseYarnQtyAndAmount($jobNo,$count,$composition,$color,$type,$supplierId){
		$jobCountCompositionColorAndTypeSupplierYarnQtyAndAmount=$this->_setYarnQtyAndAmount('JobCountCompositionColorAndTypeSupplierWise');
		return $jobCountCompositionColorAndTypeSupplierYarnQtyAndAmount[$jobNo][$count][$composition][$color][$type][$supplierId];
		
	}
	
	public function getJobCountCompositionColorAndTypeSupplierWiseYarnQtyAndAmountArray(){
		$jobAndGmtsItemWiseYarnQtyAndAount=$this->_setYarnQtyAndAmount('JobCountCompositionColorAndTypeSupplierWise');
		return $jobAndGmtsItemWiseYarnQtyAndAount;
	}
	
	//======================================================
	public function getJobCountCompositionPercentAndTypeWiseYarnQty($jobNo,$count,$composition,$percentOne,$type){
		$jobCountCompositionColorAndTypeWiseYarnQty=$this->_setYarnQty('JobCountCompositionPercentAndTypeWise');
		return $jobCountCompositionColorAndTypeWiseYarnQty[$jobNo][$count][$composition][$percentOne][$type];
	}
	public function getJobCountCompositionPercentAndTypeWiseYarnQtyArray(){
		$jobCountCompositionColorAndTypeWiseYarnQty=$this->_setYarnQty('JobCountCompositionPercentAndTypeWise');
		return $jobCountCompositionColorAndTypeWiseYarnQty;
	}
	
	public function getJobCountCompositionPercentAndTypeWiseYarnAmount($jobNo,$count,$composition,$percentOne,$type){
		$jobCountCompositionColorAndTypeWiseYarnAmount=$this->_setYarnAmount('JobCountCompositionPercentAndTypeWise');
		return $jobCountCompositionColorAndTypeWiseYarnAmount[$jobNo][$count][$composition][$percentOne][$type];
	}
	
	public function getJobCountCompositionPercentAndTypeWiseYarnAmountArray(){
		$jobCountCompositionColorAndTypeWiseYarnAmount=$this->_setYarnAmount('JobCountCompositionPercentAndTypeWise');
		return $jobCountCompositionColorAndTypeWiseYarnAmount;
	}
	public function getJobCountCompositionPercentAndTypeWiseYarnQtyAndAmount($jobNo,$count,$composition,$percentOne,$type){
		$jobCountCompositionColorAndTypeYarnQtyAndAount=$this->_setYarnQtyAndAmount('JobCountCompositionPercentAndTypeWise');
		return $jobCountCompositionColorAndTypeYarnQtyAndAount[$jobNo][$count][$composition][$percentOne][$type];
		
	}
	
	public function getJobCountCompositionPercentAndTypeWiseYarnQtyAndAmountArray(){
		$jobAndGmtsItemWiseYarnQtyAndAount=$this->_setYarnQtyAndAmount('JobCountCompositionPercentAndTypeWise');
		return $jobAndGmtsItemWiseYarnQtyAndAount;
	}
	
	
	
	//=============================================
	
	
	
	public function getJobWiseYarnAmount($jobNo){
		$jobWiseYarnAmount=$this->_setYarnAmount('job_wise');
		return $jobWiseYarnAmount[$jobNo];
	}
	
	
	public function getJobWiseYarnAmountArray(){
		$jobWiseYarnAmount=$this->_setYarnAmount('job_wise');
		return $jobWiseYarnAmount;
	}
	public function get_By_Precostdtlsid_YarnAmountArray(){
		$YarnIdWiseYarnAmount=$this->_setYarnAmount('By_YarnId');
		return $YarnIdWiseYarnAmount;
	}
	public function get_By_Precostfabricdtlsid_YarnAmountArray(){
		$FabricIdWiseYarnAmount=$this->_setYarnAmount('By_FabricId');
		return $FabricIdWiseYarnAmount;
	}
	
	public function getJobWiseYarnQtyAndAmount($jobNo){
		$jobWiseYarnQtyAndAount=$this->_setYarnQtyAndAmount('job_wise');
		return $jobWiseYarnQtyAndAount[$jobNo];
		
	}
	
	public function getJobWiseYarnQtyAndAmountArray(){
		$jobWiseYarnQtyAndAount=$this->_setYarnQtyAndAmount('job_wise');
		return $jobWiseYarnQtyAndAount;
		
	}
	public function get_By_Precostdtlsid_YarnQtyAmountArray(){
		$YarnIdWiseYarnQtyAndAount=$this->_setYarnQtyAndAmount('By_YarnId');
		return $YarnIdWiseYarnQtyAndAount;
	}
	public function get_By_Precostfabricdtlsid_YarnQtyAmountArray(){
		$FabricIdWiseYarnQtyAndAount=$this->_setYarnQtyAndAmount('By_FabricId');
		return $FabricIdWiseYarnQtyAndAount;
	}
	// order wise
	public function getOrderWiseYarnQty($poId){
		$orderWiseYarnQty=$this->_setYarnQty('order_wise');
		return $orderWiseYarnQty[$poId];
	}
	
	public function getOrderWiseYarnQtyArray(){
		$orderWiseYarnQty=$this->_setYarnQty('order_wise');
		return $orderWiseYarnQty;
	}
	
	public function getOrderWiseYarnAmount($poId){
		$orderWiseYarnAmount=$this->_setYarnAmount('order_wise');
		return $orderWiseYarnAmount[$poId];
	}
	
	public function getOrderWiseYarnAmountArray(){
		$orderWiseYarnAmount=$this->_setYarnAmount('order_wise');
		return $orderWiseYarnAmount;
	}
	public function getOrderWiseYarnQtyAndAmount($poId){
		$orderWiseYarnQtyAndAount=$this->_setYarnQtyAndAmount('order_wise');
		return $orderWiseYarnQtyAndAount[$poId];
		
	}
	public function getOrderWiseYarnQtyAndAmountArray(){
		$orderWiseYarnQtyAndAount=$this->_setYarnQtyAndAmount('order_wise');
		return $orderWiseYarnQtyAndAount;
		
	}
	// order n Color Type wise
	public function getOrderColorTypeWiseYarnQty($poId,$color_type_id){
		$orderWiseYarnQty=$this->_setYarnQty('orderColorType_wise');
		return $orderWiseYarnQty[$poId][$color_type_id];
	}
	
	public function getOrderColorTypeWiseYarnQtyArray(){
		$orderWiseYarnQty=$this->_setYarnQty('orderColorType_wise');
		return $orderWiseYarnQty;
	}
	
	public function getOrderColorTypeWiseYarnAmount($poId,$color_type_id){
		$orderWiseYarnAmount=$this->_setYarnAmount('orderColorType_wise');
		return $orderWiseYarnAmount[$poId][$color_type_id];
	}
	
	public function getOrderColorTypeWiseYarnAmountArray(){
		$orderWiseYarnAmount=$this->_setYarnAmount('orderColorType_wise');
		return $orderWiseYarnAmount;
	}
	public function getOrderColorTypeWiseYarnQtyAndAmount($poId,$color_type_id){
		$orderWiseYarnQtyAndAount=$this->_setYarnQtyAndAmount('orderColorType_wise');
		return $orderWiseYarnQtyAndAount[$poId][$color_type_id];
		
	}
	public function getOrderColorTypeWiseYarnQtyAndAmountArray(){
		$orderWiseYarnQtyAndAount=$this->_setYarnQtyAndAmount('orderColorType_wise');
		return $orderWiseYarnQtyAndAount;
		
	}
	// order and Gmts Item wise
	public function getOrderAndGmtsItemWiseYarnQty($poId,$gmtsItem){
		$orderAndGmtsItemWiseYarnQty=$this->_setYarnQty('order_and_gmtsItem_wise');
		return $orderAndGmtsItemWiseYarnQty[$poId][$gmtsItem];
	}
	
	public function getOrderAndGmtsItemWiseYarnQtyArray(){
		$orderAndGmtsItemWiseYarnQty=$this->_setYarnQty('order_and_gmtsItem_wise');
		return $orderAndGmtsItemWiseYarnQty;
	}
	public function getOrderAndGmtsItemWiseYarnAmount($poId,$gmtsItem){
		$orderAndGmtsItemWiseYarnAmount=$this->_setYarnAmount('order_and_gmtsItem_wise');
		return $orderAndGmtsItemWiseYarnAmount[$poId][$gmtsItem];
	}
	
	public function getOrderAndGmtsItemWiseYarnAmountArray(){
		$orderAndGmtsItemWiseYarnAmount=$this->_setYarnAmount('order_and_gmtsItem_wise');
		return $orderAndGmtsItemWiseYarnAmount;
	}
	public function getOrderAndGmtsItemWiseYarnQtyAndAmount($poId,$gmtsItem){
		$orderAndGmtsItemWiseYarnQtyAndAount=$this->_setYarnQtyAndAmount('order_and_gmtsItem_wise');
		return $orderAndGmtsItemWiseYarnQtyAndAount[$poId][$gmtsItem];
		
	}
	public function getOrderAndGmtsItemWiseYarnQtyAndAmountArray(){
		$orderAndGmtsItemWiseYarnQtyAndAount=$this->_setYarnQtyAndAmount('order_and_gmtsItem_wise');
		return $orderAndGmtsItemWiseYarnQtyAndAount;
		
	}
	
	
	//order and Color
	
	public function getOrderAndGmtsColorWiseYarnQty($poId,$color){
		$orderAndGmtsColorWiseYarnQty=$this->_setYarnQty('order_and_gmtsColor_wise');
		return $orderAndGmtsColorWiseYarnQty[$poId][$color];
	}
	
	public function getOrderAndGmtsColorWiseYarnQtyArray(){
		$orderAndGmtsColorWiseYarnQty=$this->_setYarnQty('order_and_gmtsColor_wise');
		return $orderAndGmtsColorWiseYarnQty;
	}
	public function getOrderAndGmtsColorWiseYarnAmount($poId,$color){
		$orderAndGmtsColorWiseYarnAmount=$this->_setYarnAmount('order_and_gmtsColor_wise');
		return $orderAndGmtsColorWiseYarnAmount[$poId][$color];
	}
	
	public function getOrderAndGmtsColorWiseYarnAmountArray(){
		$orderAndGmtsColorWiseYarnAmount=$this->_setYarnAmount('order_and_gmtsColor_wise');
		return $orderAndGmtsColorWiseYarnAmount;
	}
	public function getOrderAndGmtsColorWiseYarnQtyAndAmount($poId,$color){
		$orderAndGmtsColorWiseYarnQtyAndAount=$this->_setYarnQtyAndAmount('order_and_gmtsColor_wise');
		return $orderAndGmtsColorWiseYarnQtyAndAount[$poId][$color];
		
	}
	public function getOrderAndGmtsColorWiseYarnQtyAndAmountArray(){
		$orderAndGmtsColorWiseYarnQtyAndAount=$this->_setYarnQtyAndAmount('order_and_gmtsColor_wise');
		return $orderAndGmtsColorWiseYarnQtyAndAount;
		
	}
	
	
	
	// order and Countrywise
	public function getOrderAndCountryWiseYarnQty($poId,$countryId){
		$orderAndCountryYarnQty=$this->_setYarnQty('order_and_country_wise');
		return $orderAndCountryYarnQty[$poId][$countryId];
	}
	
	public function getOrderAndCountryWiseYarnQtyArray(){
		$orderAndCountryYarnQty=$this->_setYarnQty('order_and_country_wise');
		return $orderAndCountryYarnQty;
	}
	                
	public function getOrderAndCountryWiseYarnAmount($poId,$countryId){
		$orderAndCountryYarnAmount=$this->_setYarnAmount('order_and_country_wise');
		return $orderAndCountryYarnAmount[$poId][$countryId];
	}
	                
	public function getOrderAndCountryWiseYarnAmountArray(){
		$orderAndCountryYarnAmount=$this->_setYarnAmount('order_and_country_wise');
		return $orderAndCountryYarnAmount;
	}
	public function getOrderAndCountryWiseYarnQtyAndAmount($poId,$countryId){
		$orderAndCountryYarnQtyAndAount=$this->_setYarnQtyAndAmount('order_and_country_wise');
		return $orderAndCountryYarnQtyAndAount[$poId][$countryId];
		
	}
	public function getOrderAndCountryWiseYarnQtyAndAmountArray(){
		$orderAndCountryYarnQtyAndAount=$this->_setYarnQtyAndAmount('order_and_country_wise');
		return $orderAndCountryYarnQtyAndAount;
		
	}
	// order,Count,Composition,color,type wise
	public function getOrderCountCompositionColorAndTypeWiseYarnQty($poId,$count,$composition,$color,$type){
		$orderCountCompositionColorAndTypeWiseYarnQty=$this->_setYarnQty('OrderCountCompositionColorAndTypeWise');
		return $orderCountCompositionColorAndTypeWiseYarnQty[$poId][$count][$composition][$color][$type];
	}
	
	public function getOrderCountCompositionColorAndTypeWiseYarnQtyArray(){
		$orderCountCompositionColorAndTypeWiseYarnQty=$this->_setYarnQty('OrderCountCompositionColorAndTypeWise');
		return $orderCountCompositionColorAndTypeWiseYarnQty;
	}
	public function getOrderCountCompositionColorAndTypeWiseYarnAmount($poId,$count,$composition,$color,$type){
		$orderCountCompositionColorAndTypeWiseYarnAmount=$this->_setYarnAmount('OrderCountCompositionColorAndTypeWise');
		return $orderCountCompositionColorAndTypeWiseYarnAmount[$poId][$count][$composition][$color][$type];
	}
	
	public function getOrderCountCompositionColorAndTypeWiseYarnAmountArray(){
		$orderCountCompositionColorAndTypeWiseYarnAmount=$this->_setYarnAmount('OrderCountCompositionColorAndTypeWise');
		return $orderCountCompositionColorAndTypeWiseYarnAmount;
	}
	public function getOrderCountCompositionColorAndTypeWiseYarnQtyAndAmount($poId,$count,$composition,$color,$type){
		$orderCountCompositionColorAndTypeYarnQtyAndAount=$this->_setYarnQtyAndAmount('OrderCountCompositionColorAndTypeWise');
		return $orderCountCompositionColorAndTypeYarnQtyAndAount[$poId][$count][$composition][$color][$type];
		
	}
	
	public function getOrderCountCompositionColorAndTypeWiseYarnQtyAndAmountArray(){
		$orderAndGmtsItemWiseYarnQtyAndAount=$this->_setYarnQtyAndAmount('OrderCountCompositionColorAndTypeWise');
		return $orderAndGmtsItemWiseYarnQtyAndAount;
	}
	
	// order,Count,Composition wise
	public function getOrderCountCompositionPercentAndTypeWiseYarnQty($poId,$count,$composition,$percentOne,$type){ 
		$orderCountCompositionAndTypeWiseYarnQty=$this->_setYarnQty('OrderCountCompositionPercentAndTypeWise');
		return $orderCountCompositionAndTypeWiseYarnQty[$poId][$count][$composition][$percentOne][$type];
	}
	public function getOrderCountCompositionPercentAndTypeWiseYarnQtyArray(){
		$orderCountCompositionAndTypeWiseYarnQty=$this->_setYarnQty('OrderCountCompositionPercentAndTypeWise');
		return $orderCountCompositionAndTypeWiseYarnQty;
	}
	// order,Count,Composition wise
	public function getOrderCountAndCompositionWiseYarnQty($poId,$count,$composition){ //OrderCountAndCompositionWise
		$orderCountAndCompositionWiseYarnQty=$this->_setYarnQty('OrderCountAndCompositionWise');
		return $orderCountAndCompositionWiseYarnQty[$poId][$count][$composition];
	}
	public function getOrderCountAndCompositionWiseYarnQtyArray(){
		$orderCountAndCompositionWiseYarnQty=$this->_setYarnQty('OrderCountAndCompositionWise');
		return $orderCountAndCompositionWiseYarnQty;
	}
	
	public function getOrderCountCompositionPercentAndTypeWiseYarnAmount($poId,$count,$composition,$percentOne,$type){
		$orderCountCompositionAndTypeWiseYarnAmount=$this->_setYarnAmount('OrderCountCompositionPercentAndTypeWise');
		return $orderCountCompositionAndTypeWiseYarnAmount[$poId][$count][$composition][$percentOne][$type];
	}
	
	public function getOrderCountCompositionPercentAndTypeWiseYarnAmountArray(){
		$orderCountCompositionAndTypeWiseYarnAmount=$this->_setYarnAmount('OrderCountCompositionPercentAndTypeWise');
		return $orderCountCompositionAndTypeWiseYarnAmount;
	}
	public function getOrderCountAndCompositionWiseYarnAmount($poId,$count,$composition){
		$orderCountAndCompositionWiseYarnAmount=$this->_setYarnAmount('OrderCountAndCompositionWise');
		return $orderCountAndCompositionWiseYarnAmount[$poId][$count][$composition];
	}
	
	public function getOrderCountAndCompositionWiseYarnAmountArray(){
		$orderCountAndCompositionWiseYarnAmount=$this->_setYarnAmount('OrderCountAndCompositionWise');
		return $orderCountAndCompositionWiseYarnAmount;
	}
	
	public function getOrderCountCompositionPercentAndTypeWiseYarnQtyAndAmount($poId,$count,$composition,$percentOne,$type){
		$orderCountCompositionAndTypeYarnQtyAndAount=$this->_setYarnQtyAndAmount('OrderCountCompositionPercentAndTypeWise');
		return $orderCountCompositionAndTypeYarnQtyAndAount[$poId][$count][$composition][$percentOne][$type];
		
	}
	
	public function getOrderCountCompositionPercentAndTypeWiseYarnQtyAndAmountArray(){
		$orderAndGmtsItemWiseYarnQtyAndAount=$this->_setYarnQtyAndAmount('OrderCountCompositionPercentAndTypeWise');
		return $orderAndGmtsItemWiseYarnQtyAndAount;
	}
	
	public function getOrdeCountryshipdaterCountCompositionPercentAndTypeWiseYarnQtyAndAmountArray(){
		$orderAndGmtsItemWiseYarnQtyAndAount=$this->_setYarnQtyAndAmount('OrderCountryshipdateCountCompositionPercentAndTypeWise');
		return $orderAndGmtsItemWiseYarnQtyAndAount;
	}
	
	
	
	// order,Count,Composition,color,type and comsumption wise Data Array
	public function getOrderCountCompositionColorTypeAndConsumptionWiseYarnDataArray(){
		$orderCountCompositionColorAndTypeWiseYarnQty=$this->_setYarnQtyAndAmount('OrderCountCompositionColorTypeAndConsumptionWiseDataArray');
		return $orderCountCompositionColorAndTypeWiseYarnQty;
	}
	
	
	// count, composition  and type
	public function getCountCompositionAndTypeWiseYarnQty($count,$composition,$type){
		$CountCompositionAndTypeWiseYarnQty=$this->_setYarnQty('CountCompositionAndTypeWise');
		return $CountCompositionAndTypeWiseYarnQty[$count][$composition][$type];
	}
	public function getCountCompositionAndTypeWiseYarnQtyArray(){
		$CountCompositionAndTypeWiseYarnQty=$this->_setYarnQty('CountCompositionAndTypeWise');
		return $CountCompositionAndTypeWiseYarnQty;
	}
	public function getCountCompositionPercentTypeColorAndRateWiseYarnQtyArray(){
		$CountCompositionPercentTypeColorAndRateWiseYarnQty=$this->_setYarnQty('CountCompositionPercentTypeColorAndRateWise');
		return $CountCompositionPercentTypeColorAndRateWiseYarnQty;
	}
	
	
	
	public function getCountCompositionAndTypeWiseYarnAmount($count,$composition,$type){
		$CountCompositionAndTypeWiseYarnAmount=$this->_setYarnAmount('CountCompositionAndTypeWise');
		return $CountCompositionAndTypeWiseYarnAmount[$count][$composition][$type];
	}
	
	public function getCountCompositionAndTypeWiseYarnAmountArray(){
		$CountCompositionAndTypeWiseYarnAmount=$this->_setYarnAmount('CountCompositionAndTypeWise');
		return $CountCompositionAndTypeWiseYarnAmount;
	}
	
	public function getCountCompositionPercentTypeColorAndRateWiseYarnAmountArray(){
		$CountCompositionPercentTypeColorAndRateWiseYarnAmount=$this->_setYarnAmount('CountCompositionPercentTypeColorAndRateWise');
		return $CountCompositionPercentTypeColorAndRateWiseYarnAmount;
	}
	
	public function getCountCompositionAndTypeWiseYarnQtyAndAmount($count,$composition,$type){
		$CountCompositionAndTypeYarnQtyAndAount=$this->_setYarnQtyAndAmount('CountCompositionAndTypeWise');
		return $CountCompositionAndTypeYarnQtyAndAount[$count][$composition][$type];
		
	}
	public function getCountCompositionAndTypeWiseYarnQtyAndAmountArray(){
		$CountCompositionAndTypeYarnQtyAndAount=$this->_setYarnQtyAndAmount('CountCompositionAndTypeWise');
		return $CountCompositionAndTypeYarnQtyAndAount;
	}
	
	public function getCountCompositionPercentTypeColorAndRateWiseYarnQtyAndAmountArray(){
		$CountCompositionPercentTypeColorAndRateWiseYarnQtyAndAount=$this->_setYarnQtyAndAmount('CountCompositionPercentTypeColorAndRateWise');
		return $CountCompositionPercentTypeColorAndRateWiseYarnQtyAndAount;
	}
	public function getOrderCountCompositionPercentTypeColorAndRateWiseYarnQtyAndAmountArray(){
		$CountCompositionPercentTypeColorAndRateWiseYarnQtyAndAount=$this->_setYarnQtyAndAmount('OrderCountCompositionPercentTypeColorAndRateWise');
		return $CountCompositionPercentTypeColorAndRateWiseYarnQtyAndAount;
	}
	
	
	
	function __destruct() {
		parent::__destruct();
		unset($this->_dataArray);
		unset($this->_dataArray2);
		
	}
}
?>