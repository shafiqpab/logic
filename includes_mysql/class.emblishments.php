<?
class emblishment extends report{
	private $_By_JobAndEmblishmentid='By_JobAndEmblishmentid';
	private $_By_JobAndEmbname='By_JobAndEmbname';
	private $_By_JobAndEmbtype='By_JobAndEmbtype';
	private $_By_JobEmbnameAndEmbtype='By_JobEmbnameAndEmbtype';
	
	private $_By_OrderAndEmblishmentid='By_OrderAndEmblishmentid';
	private $_By_OrderAndEmbname='By_OrderAndEmbname';
	private $_By_OrderAndEmbtype='By_OrderAndEmbtype';
	private $_By_OrderEmbnameAndEmbtype='By_OrderEmbnameAndEmbtype';
	
	private $_By_OrderCountryAndEmblishmentid='By_OrderCountryAndEmblishmentid';
	private $_By_OrderCountryAndEmbname='By_OrderCountryAndEmbname';
	private $_By_OrderCountryAndEmbtype='By_OrderCountryAndEmbtype';
	private $_By_OrderCountryEmbnameAndEmbtype='By_OrderCountryEmbnameAndEmbtype';
	
	private $_query="";
	private $_dataArray=array();
	// class constructor
	function __construct($jobs,$type){
		parent::__construct($jobs,$type);
		$this->_setQuery();
		$this->_setData();
	}// end class constructor
	
	private function _setQuery(){
		$jobcond=$this->_setJobsString($this->_jobs,'a.job_no');
		$pocond=$this->_setPoIdsString($this->_poIds, 'b.id');
		$this->_query="select a.job_no,a.total_set_qnty,b.id,c.item_number_id,c.country_id,c.color_number_id,c.size_number_id,c.order_quantity ,c.plan_cut_qnty ,d.id as emblishment_id,d.emb_name,d.emb_type,d.cons_dzn_gmts,d.rate,d. amount   from wo_po_details_master a, wo_po_break_down b,wo_po_color_size_breakdown c,wo_pre_cost_embe_cost_dtls d  where 1=1 ".$jobcond." ".$pocond." and d.emb_name in(1,2,4,5) and cons_dzn_gmts>0 and  a.job_no=b.job_no_mst and a.job_no=c.job_no_mst and a.job_no=d.job_no  and b.id=c.po_break_down_id   and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 and c.is_deleted=0 and c.status_active=1 order by b.id,emblishment_id";

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
	private function _calculateQty($plan_cut_qnty,$costingPerQty,$total_set_qnty,$cons_qnty){
	  //return $reqyarnqnty =def_number_format(($plan_cut_qnty/($costingPerQty*$total_set_qnty))*$cons_qnty,5,"");
	  return $reqyarnqnty =($plan_cut_qnty/$total_set_qnty)*($cons_qnty/$costingPerQty);
	}
	
	private function _calculateAmount($reqyarnqnty,$rate){
	 return $amount=$reqyarnqnty*$rate;
	}
	
	private function _setQty($level){
		$jobNo='';
		$poId='';
		$itemNumberId='';
		$countryId='';
		$colorId='';
		$sizeId='';
		$planPutQnty='';
		$orderQuantity='';
		$total_set_qnty='';
		$emblishmentId='';
		$embnameId='';
		$embtypeId='';
		$req_qnty='';
		$avg_req_qnty='';
		$charge_unit='';
		//$gmtsitemRatioArray=$this->_gmtsitemRatioArray;
		//$costingPerArray=$this->_costingPerArr;
		$Qty=array();
		foreach($this->_dataArray as $row)
		{
			$jobNo=$row[csf('job_no')];
			$poId=$row[csf('id')];
			$itemNumberId=$row[csf('item_number_id')];
			$countryId=$row[csf('country_id')];
			$colorId=$row[csf('color_number_id')];
			$sizeId=$row[csf('size_number_id')];
			$planPutQnty=$row[csf('plan_cut_qnty')];
			$orderQuantity=$row[csf('order_quantity')];
			$total_set_qnty=$row[csf('total_set_qnty')];
			$emblishmentId=$row[csf('emblishment_id')];
			$embnameId=$row[csf('emb_name')];
			$embtypeId=$row[csf('emb_type')];
			$req_qnty=$row[csf('cons_dzn_gmts')];
			$charge_unit=$row[csf('rate')];
			
			//$costingPerQty=$this->_costingPer($costingPerArray[$jobNo]);
			//$set_item_ratio=$gmtsitemRatioArray[$jobNo][$itemNumberId];
			//$costingPerQty=$this->_costingPer($this->_costingPerArr[$jobNo]);
			$costingPerQty=$this->_costingPerQtyArr[$jobNo];
			//$set_item_ratio=$this->_gmtsitemRatioArray[$jobNo][$itemNumberId];
			
			$reqqnty=$this->_calculateQty($planPutQnty,$costingPerQty,$total_set_qnty,$req_qnty);
			
			if($level==$this->_By_Job){
				if(isset($Qty[$jobNo])){
					$Qty[$jobNo]+=$reqqnty;
				}
				else{
					$Qty[$jobNo]=$reqqnty;
				}
			}
			elseif($level==$this->_By_JobAndEmblishmentid){
				if(isset($Qty[$jobNo][$emblishmentId])){
					$Qty[$jobNo][$emblishmentId]+=$reqqnty;
				}
				else{
					$Qty[$jobNo][$emblishmentId]=$reqqnty;
				}
			}
			elseif($level==$this->_By_JobAndEmbname){
				if(isset($Qty[$jobNo][$embnameId])){
					$Qty[$jobNo][$embnameId]+=$reqqnty;
				}
				else{
					$Qty[$jobNo][$embnameId]=$reqqnty;
				}
			}
			elseif($level==$this->_By_JobAndEmbtype){
				if(isset($Qty[$jobNo][$embtypeId])){
					$Qty[$jobNo][$embtypeId]+=$reqqnty;
				}
				else{
					$Qty[$jobNo][$embtypeId]=$reqqnty;
				}
			}
			elseif($level==$this->_By_JobEmbnameAndEmbtype){
				if(isset($Qty[$jobNo][$embnameId][$embtypeId])){
					$Qty[$jobNo][$embnameId][$embtypeId]+=$reqqnty;
				}
				else{
					$Qty[$jobNo][$embnameId][$embtypeId]=$reqqnty;
				}
			}
			elseif($level==$this->_By_Order){
				if(isset($Qty[$poId])){
					$Qty[$poId]+=$reqqnty;
				}
				else{
					$Qty[$poId]=$reqqnty;
				}
			}
			elseif($level==$this->_By_OrderAndEmblishmentid){
				if(isset($Qty[$poId][$emblishmentId])){
					$Qty[$poId][$emblishmentId]+=$reqqnty;
				}
				else{
					$Qty[$poId][$emblishmentId]=$reqqnty;
				}
			}
			elseif($level==$this->_By_OrderAndEmbname){
				if(isset($Qty[$poId][$embnameId])){
					$Qty[$poId][$embnameId]+=$reqqnty;
				}
				else{
					$Qty[$poId][$embnameId]=$reqqnty;
				}
			}
			elseif($level==$this->_By_OrderAndEmbtype){
				if(isset($Qty[$poId][$embtypeId])){
					$Qty[$poId][$embtypeId]+=$reqqnty;
				}
				else{
					$Qty[$poId][$embtypeId]=$reqqnty;
				}
			}
			elseif($level==$this->_By_OrderEmbnameAndEmbtype){
				if(isset($Qty[$poId][$embnameId][$embtypeId])){
					$Qty[$poId][$embnameId][$embtypeId]+=$reqqnty;
				}
				else{
					$Qty[$poId][$embnameId][$embtypeId]=$reqqnty;
				}
			}
			elseif($level==$this->_By_OrderAndCountry){
				if(isset($Qty[$poId][$countryId])){
					$Qty[$poId][$countryId]+=$reqqnty;
				}
				else{
					$Qty[$poId][$countryId]=$reqqnty;
				}
			}
			elseif($level==$this->_By_OrderCountryAndEmblishmentid){
				if(isset($Qty[$poId][$countryId][$emblishmentId])){
					$Qty[$poId][$countryId][$emblishmentId]+=$reqqnty;
				}
				else{
					$Qty[$poId][$countryId][$emblishmentId]=$reqqnty;
				}
			}
			elseif($level==$this->_By_OrderCountryAndEmbname){
				if(isset($Qty[$poId][$countryId][$embnameId])){
					$Qty[$poId][$countryId][$embnameId]+=$reqqnty;
				}
				else{
					$Qty[$poId][$countryId][$embnameId]=$reqqnty;
				}
			}
			elseif($level==$this->_By_OrderCountryAndEmbtype){
				if(isset($Qty[$poId][$countryId][$embtypeId])){
					$Qty[$poId][$countryId][$embtypeId]+=$reqqnty;
				}
				else{
					$Qty[$poId][$countryId][$embtypeId]=$reqqnty;
				}
			}
			elseif($level==$this->_By_OrderCountryEmbnameAndEmbtype){
				if(isset($Qty[$poId][$countryId][$embnameId][$embtypeId])){
					$Qty[$poId][$countryId][$embnameId][$embtypeId]+=$reqqnty;
				}
				else{
					$Qty[$poId][$countryId][$embnameId][$embtypeId]=$reqqnty;
				}
			}
			elseif($level==$this->_By_OrderAndGmtsitem){
				if(isset($Qty[$poId][$itemNumberId])){
					$Qty[$poId][$itemNumberId]+=$reqqnty;
				}
				else{
					$Qty[$poId][$itemNumberId]=$reqqnty;
				}
			}
			elseif($level==$this->_By_OrderAndGmtscolor){
				if(isset($Qty[$poId][$colorId])){
					$Qty[$poId][$colorId]+=$reqqnty;
				}
				else{
					$Qty[$poId][$colorId]=$reqqnty;
				}
			}
			elseif($level==$this->_By_OrderAndGmtssize){
				if(isset($Qty[$poId][$sizeId])){
					$Qty[$poId][$sizeId]+=$reqqnty;
				}
				else{
					$Qty[$poId][$sizeId]=$reqqnty;
				}
					
			}
			elseif($level==$this->_By_orderCountryAndGmtsitem){
				if(isset($Qty[$poId][$countryId][$itemNumberId])){
					$Qty[$poId][$countryId][$itemNumberId]+=$reqqnty;
				}
				else{
					$Qty[$poId][$countryId][$itemNumberId]=$reqqnty;
				}
			}
			elseif($level==$this->_By_OrderCountryAndGmtscolor){
				if(isset($Qty[$poId][$countryId][$colorId])){
					$Qty[$poId][$countryId][$colorId]+=$reqqnty;
				}
				else{
					$Qty[$poId][$countryId][$colorId]=$reqqnty;
				}
			}
			elseif($level==$this->_By_OrderCountryAndGmtssize){
				if(isset($Qty[$poId][$countryId][$sizeId])){
					$Qty[$poId][$countryId][$sizeId]+=$reqqnty;
				}
				else{
					$Qty[$poId][$countryId][$sizeId]=$reqqnty;
				}
			}
			elseif($level==$this->_By_OrderGmtsitemAndGmtscolor){
				if(isset($Qty[$poId][$itemNumberId][$colorId])){
					$Qty[$poId][$itemNumberId][$colorId]+=$reqqnty;
				}
				else{
					$Qty[$poId][$itemNumberId][$colorId]=$reqqnty;
				}
			}
			elseif($level==$this->_By_OrderGmtsitemAndGmtssize){
				if(isset($Qty[$poId][$itemNumberId][$sizeId])){
					$Qty[$poId][$itemNumberId][$sizeId]+=$reqqnty;
				}
				else{
					$Qty[$poId][$itemNumberId][$sizeId]=$reqqnty;
				}
			}
			elseif($level==$this->_By_OrderGmtscolorAndGmtssize){
				if(isset($Qty[$poId][$colorId][$sizeId])){
					$Qty[$poId][$colorId][$sizeId]+=$reqqnty;
				}
				else{
					$Qty[$poId][$colorId][$sizeId]=$reqqnty;
				}
			}
			elseif($level==$this->_By_orderCountryGmtsitemAndGmtscolor){
				if(isset($Qty[$poId][$countryId][$itemNumberId][$colorId])){
					$Qty[$poId][$countryId][$itemNumberId][$colorId]+=$reqqnty;
				}
				else{
					$Qty[$poId][$countryId][$itemNumberId][$colorId]=$reqqnty;
				}
			}
			elseif($level==$this->_By_orderCountryGmtsitemAndGmtssize){
				if(isset($Qty[$poId][$countryId][$itemNumberId][$sizeId])){
					$Qty[$poId][$countryId][$itemNumberId][$sizeId]+=$reqqnty;
				}
				else{
					$Qty[$poId][$countryId][$itemNumberId][$sizeId]=$reqqnty;
				}
			}
			elseif($level==$this->_By_orderCountryGmtscolorAndGmtssize){
				if(isset($Qty[$poId][$countryId][$colorId][$sizeId])){
					$Qty[$poId][$countryId][$colorId][$sizeId]+=$reqqnty;
				}
				else{
					$Qty[$poId][$countryId][$colorId][$sizeId]=$reqqnty;
				}
			}
			elseif($level==$this->_By_orderGmtsitemGmtscolorAndGmtssize){
				if(isset($Qty[$poId][$itemNumberId][$colorId][$sizeId])){
					$Qty[$poId][$itemNumberId][$colorId][$sizeId]+=$reqqnty;
				}
				else{
					$Qty[$poId][$itemNumberId][$colorId][$sizeId]=$reqqnty;
				}
			}
			elseif($level==$this->_By_orderCountryGmtsitemGmtscolorAndGmtssize){
					if(isset($Qty[$poId][$countryId][$itemNumberId][$colorId][$sizeId])){
						$Qty[$poId][$countryId][$itemNumberId][$colorId][$sizeId]+=$reqqnty;
					}
					else{
						$Qty[$poId][$countryId][$itemNumberId][$colorId][$sizeId]=$reqqnty;
					}
			}
			else{
				return null;
			}
		}
		return $Qty;
	}
	
	private function _setAmount($level){
		$jobNo='';
		$poId='';
		$itemNumberId='';
		$countryId='';
		$colorId='';
		$sizeId='';
		$planPutQnty='';
		$orderQuantity='';
		$total_set_qnty='';
		
		$emblishmentId='';
		$embnameId='';
		$embtypeId='';
		$req_qnty='';
		$avg_req_qnty='';
		$charge_unit='';
		//$gmtsitemRatioArray=$this->_gmtsitemRatioArray;
		//$costingPerArray=$this->_costingPerArr;
		$Amount=array();
		
		foreach($this->_dataArray as $row)
		{
			$jobNo=$row[csf('job_no')];
			$poId=$row[csf('id')];
			$itemNumberId=$row[csf('item_number_id')];
			$countryId=$row[csf('country_id')];
			$colorId=$row[csf('color_number_id')];
			$sizeId=$row[csf('size_number_id')];
			$planPutQnty=$row[csf('plan_cut_qnty')];
			$orderQuantity=$row[csf('order_quantity')];
			$total_set_qnty=$row[csf('total_set_qnty')];
			$emblishmentId=$row[csf('emblishment_id')];
			$embnameId=$row[csf('emb_name')];
			$embtypeId=$row[csf('emb_type')];
			$req_qnty=$row[csf('cons_dzn_gmts')];
			$charge_unit=$row[csf('rate')];
			
			//$costingPerQty=$this->_costingPer($costingPerArray[$jobNo]);
			//$set_item_ratio=$gmtsitemRatioArray[$jobNo][$itemNumberId];
			//$costingPerQty=$this->_costingPer($this->_costingPerArr[$jobNo]);
			$costingPerQty=$this->_costingPerQtyArr[$jobNo];
			//$set_item_ratio=$this->_gmtsitemRatioArray[$jobNo][$itemNumberId];
			
			$reqqnty=$this->_calculateQty($planPutQnty,$costingPerQty,$total_set_qnty,$req_qnty);
			$amount=$this->_calculateAmount($reqqnty,$charge_unit);
			
			if($level==$this->_By_Job){
				if(isset($Amount[$jobNo])){
					$Amount[$jobNo]+=$amount;
				}
				else{
					$Amount[$jobNo]=$amount;
				}
			}
			elseif($level==$this->_By_JobAndEmblishmentid){
				if(isset($Amount[$jobNo][$emblishmentId])){
					$Amount[$jobNo][$emblishmentId]+=$reqqnty;
				}
				else{
					$Amount[$jobNo][$emblishmentId]=$reqqnty;
				}
			}
			elseif($level==$this->_By_JobAndEmbname){
				if(isset($Amount[$jobNo][$embnameId])){
					$Amount[$jobNo][$embnameId]+=$amount;
				}
				else{
					$Amount[$jobNo][$embnameId]=$amount;
				}
			}
			elseif($level==$this->_By_JobAndEmbtype){
				if(isset($Amount[$jobNo][$embtypeId])){
					$Amount[$jobNo][$embtypeId]+=$amount;
				}
				else{
					$Amount[$jobNo][$embtypeId]=$amount;
				}
			}
			elseif($level==$this->_By_JobEmbnameAndEmbtype){
				if(isset($Amount[$jobNo][$embnameId][$embtypeId])){
					$Amount[$jobNo][$embnameId][$embtypeId]+=$amount;
				}
				else{
					$Amount[$jobNo][$embnameId][$embtypeId]=$amount;
				}
			}
			elseif($level==$this->_By_Order){
				if(isset($Amount[$poId])){
					$Amount[$poId]+=$amount;
				}
				else{
					$Amount[$poId]=$amount;
				}
			}
			elseif($level==$this->_By_OrderAndEmblishmentid){
				if(isset($Amount[$poId][$emblishmentId])){
					$Amount[$poId][$emblishmentId]+=$amount;
				}
				else{
					$Amount[$poId][$emblishmentId]=$amount;
				}
			}
			elseif($level==$this->_By_OrderAndEmbname){
				if(isset($Amount[$poId][$embnameId])){
					$Amount[$poId][$embnameId]+=$amount;
				}
				else{
					$Amount[$poId][$embnameId]=$amount;
				}
			}
			elseif($level==$this->_By_OrderAndEmbtype){
				if(isset($Amount[$poId][$embtypeId])){
					$Amount[$poId][$embtypeId]+=$amount;
				}
				else{
					$Amount[$poId][$embtypeId]=$amount;
				}
			}
			elseif($level==$this->_By_OrderEmbnameAndEmbtype){
				if(isset($Amount[$poId][$embnameId][$embtypeId])){
					$Amount[$poId][$embnameId][$embtypeId]+=$amount;
				}
				else{
					$Amount[$poId][$embnameId][$embtypeId]=$amount;
				}
			}
			elseif($level==$this->_By_OrderAndCountry){
				if(isset($Amount[$poId][$countryId])){
					$Amount[$poId][$countryId]+=$amount;
				}
				else{
					$Amount[$poId][$countryId]=$amount;
				}
			}
			elseif($level==$this->_By_OrderCountryAndEmblishmentid){
				if(isset($Amount[$poId][$countryId][$emblishmentId])){
					$Amount[$poId][$countryId][$emblishmentId]+=$amount;
				}
				else{
					$Amount[$poId][$countryId][$emblishmentId]=$amount;
				}
			}
			elseif($level==$this->_By_OrderCountryAndEmbname){
				if(isset($Amount[$poId][$countryId][$embnameId])){
					$Amount[$poId][$countryId][$embnameId]+=$amount;
				}
				else{
					$Amount[$poId][$countryId][$embnameId]=$amount;
				}
			}
			elseif($level==$this->_By_OrderCountryAndEmbtype){
				if(isset($Amount[$poId][$countryId][$embtypeId])){
					$Amount[$poId][$countryId][$embtypeId]+=$amount;
				}
				else{
					$Amount[$poId][$countryId][$embtypeId]=$amount;
				}
			}
			elseif($level==$this->_By_OrderCountryEmbnameAndEmbtype){
				if(isset($Amount[$poId][$countryId][$embnameId][$embtypeId])){
					$Amount[$poId][$countryId][$embnameId][$embtypeId]+=$amount;
				}
				else{
					$Amount[$poId][$countryId][$embnameId][$embtypeId]=$amount;
				}
			}
			elseif($level==$this->_By_OrderAndGmtsitem){
				if(isset($Amount[$poId][$itemNumberId])){
					$Amount[$poId][$itemNumberId]+=$amount;
				}
				else{
					$Amount[$poId][$itemNumberId]=$amount;
				}
			}
			elseif($level==$this->_By_OrderAndGmtscolor){
				if(isset($Amount[$poId][$colorId])){
					$Amount[$poId][$colorId]+=$amount;
				}
				else{
					$Amount[$poId][$colorId]=$amount;
				}
			}
			elseif($level==$this->_By_OrderAndGmtssize){
				if(isset($Amount[$poId][$sizeId])){
					$Amount[$poId][$sizeId]+=$amount;
				}
				else{
					$Amount[$poId][$sizeId]=$amount;
				}
					
			}
			elseif($level==$this->_By_orderCountryAndGmtsitem){
				if(isset($Amount[$poId][$countryId][$itemNumberId])){
					$Amount[$poId][$countryId][$itemNumberId]+=$amount;
				}
				else{
					$Amount[$poId][$countryId][$itemNumberId]=$amount;
				}
			}
			elseif($level==$this->_By_OrderCountryAndGmtscolor){
				if(isset($Amount[$poId][$countryId][$colorId])){
					$Amount[$poId][$countryId][$colorId]+=$amount;
				}
				else{
					$Amount[$poId][$countryId][$colorId]=$amount;
				}
			}
			elseif($level==$this->_By_OrderCountryAndGmtssize){
				if(isset($Amount[$poId][$countryId][$sizeId])){
					$Amount[$poId][$countryId][$sizeId]+=$amount;
				}
				else{
					$Amount[$poId][$countryId][$sizeId]=$amount;
				}
			}
			elseif($level==$this->_By_OrderGmtsitemAndGmtscolor){
				if(isset($Amount[$poId][$itemNumberId][$colorId])){
					$Amount[$poId][$itemNumberId][$colorId]+=$amount;
				}
				else{
					$Amount[$poId][$itemNumberId][$colorId]=$amount;
				}
			}
			elseif($level==$this->_By_OrderGmtsitemAndGmtssize){
				if(isset($Amount[$poId][$itemNumberId][$sizeId])){
					$Amount[$poId][$itemNumberId][$sizeId]+=$amount;
				}
				else{
					$Amount[$poId][$itemNumberId][$sizeId]=$amount;
				}
			}
			elseif($level==$this->_By_OrderGmtscolorAndGmtssize){
				if(isset($Amount[$poId][$colorId][$sizeId])){
					$Amount[$poId][$colorId][$sizeId]+=$amount;
				}
				else{
					$Amount[$poId][$colorId][$sizeId]=$amount;
				}
			}
			elseif($level==$this->_By_orderCountryGmtsitemAndGmtscolor){
				if(isset($Amount[$poId][$countryId][$itemNumberId][$colorId])){
					$Amount[$poId][$countryId][$itemNumberId][$colorId]+=$amount;
				}
				else{
					$Amount[$poId][$countryId][$itemNumberId][$colorId]=$amount;
				}
			}
			elseif($level==$this->_By_orderCountryGmtsitemAndGmtssize){
				if(isset($Amount[$poId][$countryId][$itemNumberId][$sizeId])){
					$Amount[$poId][$countryId][$itemNumberId][$sizeId]+=$amount;
				}
				else{
					$Amount[$poId][$countryId][$itemNumberId][$sizeId]=$amount;
				}
			}
			elseif($level==$this->_By_orderCountryGmtscolorAndGmtssize){
				if(isset($Amount[$poId][$countryId][$colorId][$sizeId])){
					$Amount[$poId][$countryId][$colorId][$sizeId]+=$amount;
				}
				else{
					$Amount[$poId][$countryId][$colorId][$sizeId]=$amount;
				}
			}
			elseif($level==$this->_By_orderGmtsitemGmtscolorAndGmtssize){
				if(isset($Amount[$poId][$itemNumberId][$colorId][$sizeId])){
					$Amount[$poId][$itemNumberId][$colorId][$sizeId]+=$amount;
				}
				else{
					$Amount[$poId][$itemNumberId][$colorId][$sizeId]=$amount;
				}
			}
			elseif($level==$this->_By_orderCountryGmtsitemGmtscolorAndGmtssize){
					if(isset($Amount[$poId][$countryId][$itemNumberId][$colorId][$sizeId])){
						$Amount[$poId][$countryId][$itemNumberId][$colorId][$sizeId]+=$amount;
					}
					else{
						$Amount[$poId][$countryId][$itemNumberId][$colorId][$sizeId]=$amount;
					}
			}
			else{
				return null;
			}
			
		}
		return $Amount;
	}
	
	public function unsetDataArray(){
		$this->_dataArray=array();
	}
	
	//Job wise
	//Qty
	public function getQty_by_job($jobNo){
		$Qty=$this->_setQty($this->_By_Job);
		return $Qty[$jobNo];
	}
	
	public function getQtyArray_by_job(){
		$Qty=$this->_setQty($this->_By_Job);
		return $Qty;
		
	}
	//Amount
	public function getAmount_by_job($jobNo){
		$Amount=$this->_setAmount($this->_By_Job);
		return $Amount[$jobNo];
	}
	
	public function getAmountArray_by_job(){
		$Amount=$this->_setAmount($this->_By_Job);
		return $Amount;
	}
	//Job and Conversion Id wise
	//Qty
	public function getQty_by_jobAndEmblishmentid($jobNo,$emblishmentId){
		$Qty=$this->_setQty($this->_By_JobAndEmblishmentid);
		return $Qty[$jobNo][$emblishmentId];
	}
	
	public function getQtyArray_by_jobAndEmblishmentid(){
		$Qty=$this->_setQty($this->_By_JobAndEmblishmentid);
		return $Qty;
		
	}
	//Amount
	public function getAmount_by_jobAndEmblishmentid($jobNo,$emblishmentId){
		$Amount=$this->_setAmount($this->_By_JobAndEmblishmentid);
		return $Amount[$jobNo][$emblishmentId];
	}
	
	public function getAmountArray_by_jobAndEmblishmentid(){
		$Amount=$this->_setAmount($this->_By_JobAndEmblishmentid);
		return $Amount;
	}
	
	
	//Job and Fabric wise
	//Qty
	public function getQty_by_jobAndEmbname($jobNo,$embname){
		$Qty=$this->_setQty($this->_By_JobAndEmbname);
		return $Qty[$jobNo][$embname];
	}
	
	public function getQtyArray_by_jobAndEmbname(){
		$Qty=$this->_setQty($this->_By_JobAndEmbname);
		return $Qty;
		
	}
	//Amount
	public function getAmount_by_jobAndEmbname($jobNo,$embname){
		$Amount=$this->_setAmount($this->_By_JobAndEmbname);
		return $Amount[$jobNo][$embname];
	}
	
	public function getAmountArray_by_jobAndEmbname(){
		$Amount=$this->_setAmount($this->_By_JobAndEmbname);
		return $Amount;
	}
	
	//Job and Process wise
	//Qty
	public function getQty_by_jobAndEmbtype($jobNo,$embtype){
		$Qty=$this->_setQty($this->_By_JobAndEmbtype);
		return $Qty[$jobNo][$embtype];
	}
	
	public function getQtyArray_by_jobAndEmbtype(){
		$Qty=$this->_setQty($this->_By_JobAndEmbtype);
		return $Qty;
		
	}
	//Amount
	public function getAmount_by_jobAndEmbtype($jobNo,$embtype){
		$Amount=$this->_setAmount($this->_By_JobAndEmbtype);
		return $Amount[$jobNo][$embtype];
	}
	
	public function getAmountArray_by_jobAndEmbtype(){
		$Amount=$this->_setAmount($this->_By_JobAndEmbtype);
		return $Amount;
	}
	
	//Job, Fabric and Process wise
	//Qty
	public function getQty_by_jobEmbnameAndEmbtype($jobNo,$embname,$embtype){
		$Qty=$this->_setQty($this->_By_JobEmbnameAndEmbtype);
		return $Qty[$jobNo][$embname][$embtype];
	}
	
	public function getQtyArray_by_jobEmbnameAndEmbtype(){
		$Qty=$this->_setQty($this->_By_JobEmbnameAndEmbtype);
		return $Qty;
		
	}
	//Amount
	public function getAmount_by_jobEmbnameAndEmbtype($jobNo,$embname,$embtype){
		$Amount=$this->_setAmount($this->_By_JobEmbnameAndEmbtype);
		return $Amount[$jobNo][$embname][$embtype];
	}
	
	public function getAmountArray_by_jobEmbnameAndEmbtype(){
		$Amount=$this->_setAmount($this->_By_JobEmbnameAndEmbtype);
		return $Amount;
	}
	
	
	// Order wise
	//Qty
	public function getQty_by_order($poId){
		$Qty=$this->_setQty($this->_By_Order);
		return $Qty[$poId];
	}
	
	public function getQtyArray_by_order(){
		$Qty=$this->_setQty($this->_By_Order);
		return $Qty;
	}
	
	//Amount
	public function getAmount($poId){
		$Amount=$this->_setAmount($this->_By_Order);
		return $Amount[$poId];
	}
	
	public function getAmountArray_by_order(){
		$Amount=$this->_setAmount($this->_By_Order);
		return $Amount;
	}
	
	
	//Order and Conversion Id wise
	//Qty
	public function getQty_by_orderAndEmblishmentid($poId,$emblishmentId){
		$Qty=$this->_setQty($this->_By_OrderAndEmblishmentid);
		return $Qty[$poId][$emblishmentId];
	}
	
	public function getQtyArray_by_orderAndEmblishmentid(){
		$Qty=$this->_setQty($this->_By_OrderAndEmblishmentid);
		return $Qty;
		
	}
	//Amount
	public function getAmount_by_orderAndEmblishmentid($poId,$emblishmentId){
		$Amount=$this->_setAmount($this->_By_OrderAndEmblishmentid);
		return $Amount[$poId][$emblishmentId];
	}
	
	public function getAmountArray_by_orderAndEmblishmentid(){
		$Amount=$this->_setAmount($this->_By_OrderAndEmblishmentid);
		return $Amount;
	}
	
	
	//Order and Fabric wise
	//Qty
	public function getQty_by_orderAndEmbname($poId,$embname){
		$Qty=$this->_setQty($this->_By_OrderAndEmbname);
		return $Qty[$poId][$embname];
	}
	
	public function getQtyArray_by_orderAndEmbname(){
		$Qty=$this->_setQty($this->_By_OrderAndEmbname);
		return $Qty;
		
	}
	//Amount
	public function getAmount_by_orderAndEmbname($poId,$embname){
		$Amount=$this->_setAmount($this->_By_OrderAndEmbname);
		return $Amount[$poId][$embname];
	}
	
	public function getAmountArray_by_orderAndEmbname(){
		$Amount=$this->_setAmount($this->_By_OrderAndEmbname);
		return $Amount;
	}
	
	//Order and Process wise
	//Qty
	public function getQty_by_orderAndEmbtype($poId,$embtype){
		$Qty=$this->_setQty($this->_By_OrderAndEmbtype);
		return $Qty[$poId][$embtype];
	}
	
	public function getQtyArray_by_orderAndEmbtype(){
		$Qty=$this->_setQty($this->_By_OrderAndEmbtype);
		return $Qty;
		
	}
	//Amount
	public function getAmount_by_orderAndEmbtype($poId,$embtype){
		$Amount=$this->_setAmount($this->_By_OrderAndEmbtype);
		return $Amount[$poId][$embtype];
	}
	
	public function getAmountArray_by_orderAndEmbtype(){
		$Amount=$this->_setAmount($this->_By_OrderAndEmbtype);
		return $Amount;
	}
	
	//Order Fabric and Process wise
	//Qty
	public function getQty_by_orderEmbnameAndEmbtype($poId,$embname,$embtype){
		$Qty=$this->_setQty($this->_By_OrderEmbnameAndEmbtype);
		return $Qty[$poId][$embname][$embtype];
	}
	
	public function getQtyArray_by_orderEmbnameAndEmbtype(){
		$Qty=$this->_setQty($this->_By_OrderEmbnameAndEmbtype);
		return $Qty;
		
	}
	//Amount
	public function getAmount_by_orderEmbnameAndEmbtype($poId,$embname,$embtype){
		$Amount=$this->_setAmount($this->_By_OrderEmbnameAndEmbtype);
		return $Amount[$poId][$embname][$embtype];
	}
	
	public function getAmountArray_by_orderEmbnameAndEmbtype(){
		$Amount=$this->_setAmount($this->_By_OrderEmbnameAndEmbtype);
		return $Amount;
	}
	
	
	
	// Order and Country wise
	//Qty
	public function getQty_by_orderAndCountry($poId,$countryId){
		$Qty=$this->_setQty($this->_By_OrderAndCountry);
		return $Qty[$poId][$countryId];
	}
	
	public function getQtyArray_by_orderAndCountry(){
		$Qty=$this->_setQty($this->_By_OrderAndCountry);
		return $Qty;
	}
	//Amount
	public function getAmount_by_orderAndCountry($poId,$countryId){
		$Amount=$this->_setAmount($this->_By_OrderAndCountry);
		return $Amount[$poId][$countryId];
	}
	public function getAmountArray_by_orderAndCountry(){
		$Amount=$this->_setAmount($this->_By_OrderAndCountry);
		return $Amount;
	}
	
	
	
	
	
	//Order,Country and Conversion Id wise
	//Qty
	public function getQty_by_orderCountryAndEmblishmentid($poId,$countryId,$emblishmentId){
		$Qty=$this->_setQty($this->_By_OrderCountryAndEmblishmentid);
		return $Qty[$poId][$countryId][$emblishmentId];
	}
	
	public function getQtyArray_by_orderCountryAndEmblishmentid(){
		$Qty=$this->_setQty($this->_By_OrderCountryAndEmblishmentid);
		return $Qty;
		
	}
	//Amount
	public function getAmount_by_orderCountryAndEmblishmentid($poId,$countryId,$emblishmentId){
		$Amount=$this->_setAmount($this->_By_OrderCountryAndEmblishmentid);
		return $Amount[$poId][$countryId][$emblishmentId];
	}
	
	public function getAmountArray_by_orderCountryAndEmblishmentid(){
		$Amount=$this->_setAmount($this->_By_OrderCountryAndEmblishmentid);
		return $Amount;
	}
	
	
	//Order,Country and Fabric wise
	//Qty
	public function getQty_by_orderCountryAndEmbname($poId,$countryId,$embname){
		$Qty=$this->_setQty($this->_By_OrderCountryAndEmbname);
		return $Qty[$poId][$countryId][$embname];
	}
	
	public function getQtyArray_by_orderCountryAndEmbname(){
		$Qty=$this->_setQty($this->_By_OrderCountryAndEmbname);
		return $Qty;
		
	}
	//Amount
	public function getAmount_by_orderCountryAndEmbname($poId,$countryId,$embname){
		$Amount=$this->_setAmount($this->_By_OrderCountryAndEmbname);
		return $Amount[$poId][$countryId][$embname];
	}
	
	public function getAmountArray_by_orderCountryAndEmbname(){
		$Amount=$this->_setAmount($this->_By_OrderCountryAndEmbname);
		return $Amount;
	}
	
	//Order,Country and Process wise
	//Qty
	public function getQty_by_orderCountryAndEmbtype($poId,$countryId,$embtype){
		$Qty=$this->_setQty($this->_By_OrderCountryAndEmbtype);
		return $Qty[$poId][$countryId][$embtype];
	}
	
	public function getQtyArray_by_orderCountryAndEmbtype(){
		$Qty=$this->_setQty($this->_By_OrderCountryAndEmbtype);
		return $Qty;
		
	}
	//Amount
	public function getAmount_by_orderCountryAndEmbtype($poId,$countryId,$embtype){
		$Amount=$this->_setAmount($this->_By_OrderCountryAndEmbtype);
		return $Amount[$poId][$countryId][$embtype];
	}
	
	public function getAmountArray_by_orderCountryAndEmbtype(){
		$Amount=$this->_setAmount($this->_By_OrderCountryAndEmbtype);
		return $Amount;
	}
	
	//Order,Country Fabric and Process wise
	//Qty
	public function getQty_by_orderCountryEmbnameAndEmbtype($poId,$countryId,$embname,$embtype){
		$Qty=$this->_setQty($this->_By_OrderCountryEmbnameAndEmbtype);
		return $Qty[$poId][$countryId][$embname][$embtype];
	}
	
	public function getQtyArray_by_orderCountryEmbnameAndEmbtype(){
		$Qty=$this->_setQty($this->_By_OrderCountryEmbnameAndEmbtype);
		return $Qty;
		
	}
	//Amount
	public function getAmount_by_orderCountryEmbnameAndEmbtype($poId,$countryId,$embname,$embtype){
		$Amount=$this->_setAmount($this->_By_OrderCountryEmbnameAndEmbtype);
		return $Amount[$poId][$countryId][$embname][$embtype];
	}
	
	public function getAmountArray_by_orderCountryEmbnameAndEmbtype(){
		$Amount=$this->_setAmount($this->_By_OrderCountryEmbnameAndEmbtype);
		return $Amount;
	}
	// Order and Gmts Item wise
	//Qty
	public function getQty_by_orderAndGmtsitem($poId,$gmtsItem){
		$Qty=$this->_setQty($this->_By_OrderAndGmtsitem);
		return $Qty[$poId][$gmtsItem];
	}
	
	public function getQtyArray_by_orderAndGmtsitem(){
		$Qty=$this->_setQty($this->_By_OrderAndGmtsitem);
		return $Qty;
	}
	//Amount
	public function getAmount_by_orderAndGmtsitem($poId,$gmtsItem){
		$Amount=$this->_setAmount($this->_By_OrderAndGmtsitem);
		return $Amount[$poId][$gmtsItem];
	}
	
	public function getAmountArray_by_orderAndGmtsitem(){
		$Amount=$this->_setAmount($this->_By_OrderAndGmtsitem);
		return $Amount;
	}
	
	// Order and Gmts Color wise
	//Qty
	public function getQty_by_orderAndGmtscolor($poId,$colorId){
		$Qty=$this->_setQty($this->_By_OrderAndGmtscolor);
		return $Qty[$poId][$colorId];
	}
	
	public function getQtyArray_by_orderAndGmtscolor(){
		$Qty=$this->_setQty($this->_By_OrderAndGmtscolor);
		return $Qty;
	}
	//Amount
	public function getAmount_by_orderAndGmtscolor($poId,$colorId){
		$Amount=$this->_setAmount($this->By_OrderAndGmtscolor);
		return $Amount[$poId][$colorId];
	}
	
	public function getAmountArray_by_orderAndGmtscolor(){
		$Amount=$this->_setAmount($this->_By_OrderAndGmtscolor);
		return $Amount;
	}
	
	// Order and Gmts Size wise
	//Qty
	public function getQty_by_orderAndGmtssize($poId,$sizeId){
		$Qty=$this->_setQty($this->_By_OrderAndGmtssize);
		return $Qty[$poId][$sizeId];
	}
	
	public function getQtyArray_by_orderAndGmtssize(){
		$Qty=$this->_setQty($this->_By_OrderAndGmtssize);
		return $Qty;
	}
	//Amount
	public function getAmount_by_orderAndGmtssize($poId,$sizeId){
		$Amount=$this->_setAmount($this->_By_OrderAndGmtssize);
		return $Amount[$poId][$sizeId];
	}
	
	public function getAmountArray_by_orderAndGmtssize(){
		$Amount=$this->_setAmount($this->_By_OrderAndGmtssize);
		return $Amount;
	}
	
	
	
	// Order,Country and Gmts Item wise
	//Qty
	public function getQty_by_orderCountryAndGmtsitem($poId,$countryId,$gmtsItem){
		$Qty=$this->_setQty($this->_By_orderCountryAndGmtsitem);
		return $Qty[$poId][$countryId][$gmtsItem];
	}
	
	public function getQtyArray_by_orderCountryAndGmtsitem(){
		$Qty=$this->_setQty($this->_By_orderCountryAndGmtsitem);
		return $Qty;
	}
	//Amount
	public function getAmount_by_orderCountryAndGmtsitem($poId,$countryId,$gmtsItem){
		$Amount=$this->_setAmount($this->_By_orderCountryAndGmtsitem);
		return $Amount[$poId][$countryId][$gmtsItem];
	}
	public function getAmountArray_by_orderCountryAndGmtsitem(){
		$Amount=$this->_setAmount($this->_By_orderCountryAndGmtsitem);
		return $Amount;
	}
	
	// Order and Country And Color Wise
	//Qty
	public function getQty_by_orderCountryAndGmtscolor($poId,$countryId,$colorId){
		$Qty=$this->_setQty($this->_By_OrderCountryAndGmtscolor);
		return $Qty[$poId][$countryId][$colorId];
	}
	
	public function getQtyArray_by_orderCountryAndGmtscolor(){
		$Qty=$this->_setQty($this->_By_OrderCountryAndGmtscolor);
		return $Qty;
	}
	//Amount
	public function getAmount_by_orderCountryAndGmtscolor($poId,$countryId,$colorId){
		$Amount=$this->_setAmount($this->_By_OrderCountryAndGmtscolor);
		return $Amount[$poId][$countryId][$colorId];
	}
	public function getAmountArray_by_orderCountryAndGmtscolor(){
		$Amount=$this->_setAmount($this->_By_OrderCountryAndGmtscolor);
		return $Amount;
	}
	
	// Order and Country And Size Wise
	//Qty
	public function getQty_by_orderCountryAndGmtssize($poId,$countryId,$sizeId){
		$Qty=$this->_setQty($this->_By_OrderCountryAndGmtssize);
		return $Qty[$poId][$countryId][$sizeId];
	}
	
	public function getQtyArray_by_orderCountryAndGmtssize(){
		$Qty=$this->_setQty($this->_By_OrderCountryAndGmtssize);
		return $Qty;
	}
	//Amount
	public function getAmount_by_orderCountryAndGmtssize($poId,$countryId,$sizeId){
		$Amount=$this->_setAmount($this->_By_OrderCountryAndGmtssize);
		return $Amount[$poId][$countryId][$sizeId];
	}
	public function getAmountArray_by_orderCountryAndGmtssize(){
		$Amount=$this->_setAmount($this->_By_OrderCountryAndGmtssize);
		return $Amount;
	}
	
	// Order and Gmts Item And Color Wise
	//Qty
	public function getQty_by_orderGmtsitemAndGmtscolor($poId,$gmtsItem,$colorId){
		$Qty=$this->_setQty($this->_By_OrderGmtsitemAndGmtscolor);
		return $Qty[$poId][$gmtsItem][$colorId];
	}
	
	public function getQtyArray_by_orderGmtsitemAndGmtscolor(){
		$Qty=$this->_setQty($this->_By_OrderGmtsitemAndGmtscolor);
		return $Qty;
	}
	
	
	//Amount
	public function getAmount_by_orderGmtsitemAndGmtscolor($poId,$gmtsItem,$colorId){
		$Amount=$this->_setAmount($this->_By_OrderGmtsitemAndGmtscolor);
		return $Amount[$poId][$gmtsItem][$colorId];
	}
	public function getAmountArray_by_orderGmtsitemAndGmtscolor(){
		$Amount=$this->_setAmount($this->_By_OrderGmtsitemAndGmtscolor);
		return $Amount;
	}
	
	// Order and Gmts Item And Size Wise=============================================================================
	//Qty
	public function getQty_by_orderGmtsitemAndGmtssize($poId,$gmtsItem,$sizeId){
		$Qty=$this->_setQty($this->_By_OrderGmtsitemAndGmtssize);
		return $Qty[$poId][$gmtsItem][$sizeId];
	}
	
	public function getQtyArray_by_orderGmtsitemAndGmtssize(){
		$Qty=$this->_setQty($this->_By_OrderGmtsitemAndGmtssize);
		return $Qty;
	}
	
	
	//Amount
	public function getAmount_by_orderGmtsitemAndGmtssize($poId,$gmtsItem,$sizeId){
		$Amount=$this->_setAmount($this->_By_OrderGmtsitemAndGmtssize);
		return $Amount[$poId][$gmtsItem][$sizeId];
	}
	public function getAmountArray_by_orderGmtsitemAndGmtssize(){
		$Amount=$this->_setAmount($this->_By_OrderGmtsitemAndGmtssize);
		return $Amount;
	}
	
	// Order and Gmts Color And Size Wise=============================================================================
	//Qty
	public function getQty_by_orderGmtscolorAndGmtssize($poId,$colorId,$sizeId){
		$Qty=$this->_setQty($this->_By_OrderGmtscolorAndGmtssize);
		return $Qty[$poId][$colorId][$sizeId];
	}
	
	public function getQtyArray_by_orderGmtscolorAndGmtssize(){
		$Qty=$this->_setQty($this->_By_OrderGmtscolorAndGmtssize);
		return $Qty;
	}
	
	
	//Amount
	public function getAmount_by_orderGmtscolorAndGmtssize($poId,$colorId,$sizeId){
		$Amount=$this->_setAmount($this->_By_OrderGmtscolorAndGmtssize);
		return $Amount[$poId][$colorId][$sizeId];
	}
	public function getAmountArray_by_orderGmtscolorAndGmtssize(){
		$Amount=$this->_setAmount($this->_By_OrderGmtscolorAndGmtssize);
		return $Amount;
	}
	
	// Order,Country,Gmts Item  and Gmts Color wise
	//Qty
	public function getQty_by_orderCountryGmtsitemAndGmtscolor($poId,$countryId,$gmtsItem,$colorId){
		$Qty=$this->_setQty($this->_By_orderCountryGmtsitemAndGmtscolor);
		return $Qty[$poId][$countryId][$gmtsItem][$colorId];
	}
	
	public function getQtyArray_by_orderCountryGmtsitemAndGmtscolor(){
		$Qty=$this->_setQty($this->_By_orderCountryGmtsitemAndGmtscolor);
		return $Qty;
	}
	
	
	//Amount
	public function getAmount_by_orderCountryGmtsitemAndGmtscolor($poId,$countryId,$gmtsItem,$colorId){
		$Amount=$this->_setAmount($this->_By_orderCountryGmtsitemAndGmtscolor);
		return $Amount[$poId][$countryId][$gmtsItem][$colorId];
	}
	public function getAmountArray_by_orderCountryGmtsitemAndGmtscolor(){
		$Amount=$this->_setAmount($this->_By_orderCountryGmtsitemAndGmtscolor);
		return $Amount;
	}
	
	// Order,Country,Gmts Item  and Gmts Size wise
	//Qty
	public function getQty_by_orderCountryGmtsitemAndGmtssize($poId,$countryId,$gmtsItem,$sizeId){
		$Qty=$this->_setQty($this->_By_orderCountryGmtsitemAndGmtssize);
		return $Qty[$poId][$countryId][$gmtsItem][$sizeId];
	}
	
	public function getQtyArray_by_orderCountryGmtsitemAndGmtssize(){
		$Qty=$this->_setQty($this->_By_orderCountryGmtsitemAndGmtssize);
		return $Qty;
	}
	
	
	//Amount
	public function getAmount_by_orderCountryGmtsitemAndGmtssize($poId,$countryId,$gmtsItem,$sizeId){
		$Amount=$this->_setAmount($this->_By_orderCountryGmtsitemAndGmtssize);
		return $Amount[$poId][$countryId][$gmtsItem][$sizeId];
	}
	public function getAmountArray_by_orderCountryGmtsitemAndGmtssize(){
		$Amount=$this->_setAmount($this->_By_orderCountryGmtsitemAndGmtssize);
		return $Amount;
	}
	
	// Order,Country,Gmts Color  and Gmts Size wise
	//Qty
	public function getQty_by_orderCountryGmtscolorAndGmtssize($poId,$countryId,$colorId,$sizeId){
		$Qty=$this->_setQty($this->_By_orderCountryGmtscolorAndGmtssize);
		return $Qty[$poId][$countryId][$colorId][$sizeId];
	}
	
	public function getQtyArray_by_orderCountryGmtscolorAndGmtssize(){
		$Qty=$this->_setQty($this->_By_orderCountryGmtscolorAndGmtssize);
		return $Qty;
	}
	
	
	//Amount
	public function getAmount_by_orderCountryGmtscolorAndGmtssize($poId,$countryId,$colorId,$sizeId){
		$Amount=$this->_setAmount($this->_By_orderCountryGmtscolorAndGmtssize);
		return $Amount[$poId][$countryId][$colorId][$sizeId];
	}
	public function getAmountArray_by_orderCountryGmtscolorAndGmtssize(){
		$Amount=$this->_setAmount($this->_By_orderCountryGmtscolorAndGmtssize);
		return $Amount;
	}
	
	// Order,Gmts item,Gmts Color  and Gmts Size wise
	//Qty
	public function getQty_by_orderGmtsitemGmtscolorAndGmtssize($poId,$gmtsItem,$colorId,$sizeId){
		$Qty=$this->_setQty($this->_By_orderGmtsitemGmtscolorAndGmtssize);
		return $Qty[$poId][$gmtsItem][$colorId][$sizeId];
	}
	
	public function getQtyArray_by_orderGmtsitemGmtscolorAndGmtssize(){
		$Qty=$this->_setQty($this->_By_orderGmtsitemGmtscolorAndGmtssize);
		return $Qty;
	}
	
	
	//Amount
	public function getAmount_by_orderGmtsitemGmtscolorAndGmtssize($poId,$gmtsItem,$colorId,$sizeId){
		$Amount=$this->_setAmount($this->_By_orderGmtsitemGmtscolorAndGmtssize);
		return $Amount[$poId][$gmtsItem][$colorId][$sizeId];
	}
	public function getAmountArray_by_orderGmtsitemGmtscolorAndGmtssize(){
		$Amount=$this->_setAmount($this->_By_orderGmtsitemGmtscolorAndGmtssize);
		return $Amount;
	}
	
	// Order,Country,Gmts Item, Gmts Color and Gmts size wise
	//Qty
	public function getQty_by_orderCountryGmtsitemGmtscolorAndGmtssize($poId,$countryId,$gmtsItem,$colorId,$sizeId){
		$Qty=$this->_setQty($this->_By_orderCountryGmtsitemGmtscolorAndGmtssize);
		return $Qty[$poId][$countryId][$gmtsItem][$colorId][$sizeId];
	}
	
	public function getQtyArray_by_orderCountryGmtsitemGmtscolorAndGmtssize(){
		$Qty=$this->_setQty($this->_By_orderCountryGmtsitemGmtscolorAndGmtssize);
		return $Qty;
	}
	
	
	//Amount
	public function getAmount_by_orderCountryGmtsitemGmtscolorAndGmtssize($poId,$countryId,$gmtsItem,$colorId,$sizeId){
		$Amount=$this->_setAmount($this->_By_orderCountryGmtsitemGmtscolorAndGmtssize);
		return $Amount[$poId][$countryId][$gmtsItem][$colorId][$sizeId];
	}
	public function getAmountArray_by_orderCountryGmtsitemGmtscolorAndGmtssize(){
		$Amount=$this->_setAmount($this->_By_orderCountryGmtsitemGmtscolorAndGmtssize);
		return $Amount;
	}
	function __destruct() {
		parent::__destruct();
	}
}



/*$st= microtime(true);
echo $st;
echo "<br/>";
$jobs=array('FAL-15-00657','FAL-15-00650','FAL-15-00650');
$jobs=array('5477');
$fabric= new emblishment($jobs,'po');

echo $fabric->getQuery();
echo "<br/>";
print_r($fabric->getQtyArray_by_job());
echo "<br/>";
print_r($fabric->getQtyArray_by_jobAndEmblishmentid());
echo "<br/>";
print_r($fabric->getQtyArray_by_jobAndEmbname());
echo "<br/>";
print_r($fabric->getQtyArray_by_jobAndEmbtype());
echo "<br/>";
print_r($fabric->getQtyArray_by_jobEmbnameAndEmbtype());
echo "<br/>";
print_r($fabric->getQtyArray_by_order());
echo "<br/>";

print_r($fabric->getQtyArray_by_orderAndEmblishmentid());
echo "<br/>";
print_r($fabric->getQtyArray_by_orderAndEmbname());
echo "<br/>";
print_r($fabric->getQtyArray_by_orderAndEmbtype());
echo "<br/>";
print_r($fabric->getQtyArray_by_orderEmbnameAndEmbtype());
echo "<br/>";


print_r($fabric->getQtyArray_by_orderAndCountry());
echo "<br/>";

print_r($fabric->getQtyArray_by_orderCountryAndEmblishmentid());
echo "<br/>";
print_r($fabric->getQtyArray_by_orderCountryAndEmbname());
echo "<br/>";
print_r($fabric->getQtyArray_by_orderCountryAndEmbtype());
echo "<br/>";
print_r($fabric->getQtyArray_by_orderCountryEmbnameAndEmbtype());
echo "<br/>";


print_r($fabric->getQtyArray_by_orderAndGmtsitem());
echo "<br/>";
print_r($fabric->getQtyArray_by_orderAndGmtscolor());
echo "<br/>";
print_r($fabric->getQtyArray_by_orderAndGmtssize());
echo "<br/>";

print_r($fabric->getQtyArray_by_orderCountryAndGmtsitem());
echo "<br/>";
print_r($fabric->getQtyArray_by_orderCountryAndGmtscolor());
echo "<br/>";
print_r($fabric->getQtyArray_by_orderCountryAndGmtssize());
echo "<br/>";
print_r($fabric->getQtyArray_by_orderGmtsitemAndGmtscolor());
echo "<br/>";
print_r($fabric->getQtyArray_by_orderGmtsitemAndGmtssize());
echo "<br/>";
print_r($fabric->getQtyArray_by_orderGmtscolorAndGmtssize());
echo "<br/>";
print_r($fabric->getQtyArray_by_orderCountryGmtsitemAndGmtscolor());
echo "<br/>";
print_r($fabric->getQtyArray_by_orderCountryGmtsitemAndGmtssize());
echo "<br/>";
print_r($fabric->getQtyArray_by_orderCountryGmtscolorAndGmtssize());
echo "<br/>";
print_r($fabric->getQtyArray_by_orderGmtsitemGmtscolorAndGmtssize());
echo "<br/>";
print_r($fabric->getQtyArray_by_orderCountryGmtsitemGmtscolorAndGmtssize());





echo "<br/>Amount <br/>";
print_r($fabric->getAmountArray_by_job());
echo "<br/>";
print_r($fabric->getAmountArray_by_jobAndEmblishmentid());
echo "<br/>";
print_r($fabric->getAmountArray_by_jobAndEmbname());
echo "<br/>";
print_r($fabric->getAmountArray_by_jobAndEmbtype());
echo "<br/>";
print_r($fabric->getAmountArray_by_jobEmbnameAndEmbtype());
echo "<br/>";



print_r($fabric->getAmountArray_by_order());
echo "<br/>";
print_r($fabric->getAmountArray_by_orderAndEmblishmentid());
echo "<br/>";
print_r($fabric->getAmountArray_by_orderAndEmbname());
echo "<br/>";
print_r($fabric->getAmountArray_by_orderAndEmbtype());
echo "<br/>";
print_r($fabric->getAmountArray_by_orderEmbnameAndEmbtype());
echo "<br/>";



print_r($fabric->getAmountArray_by_orderAndcountry());
echo "<br/>";

print_r($fabric->getAmountArray_by_orderCountryAndEmblishmentid());
echo "<br/>";
print_r($fabric->getAmountArray_by_orderCountryAndEmbname());
echo "<br/>";
print_r($fabric->getAmountArray_by_orderCountryAndEmbtype());
echo "<br/>";
print_r($fabric->getAmountArray_by_orderCountryEmbnameAndEmbtype());
echo "<br/>";

print_r($fabric->getAmountArray_by_orderAndGmtsitem());
echo "<br/>";
print_r($fabric->getAmountArray_by_orderAndGmtscolor());
echo "<br/>";
print_r($fabric->getAmountArray_by_orderAndGmtssize());

echo "<br/>";
print_r($fabric->getAmountArray_by_orderCountryAndGmtsitem());
echo "<br/>";
print_r($fabric->getAmountArray_by_orderCountryAndGmtscolor());
echo "<br/>";
print_r($fabric->getAmountArray_by_orderCountryAndGmtssize());
echo "<br/>";
print_r($fabric->getAmountArray_by_orderGmtsitemAndGmtscolor());
echo "<br/>";
print_r($fabric->getAmountArray_by_orderGmtsitemAndGmtssize());
echo "<br/>";
print_r($fabric->getAmountArray_by_orderGmtscolorAndGmtssize());
echo "<br/>";
print_r($fabric->getAmountArray_by_orderCountryGmtsitemAndGmtscolor());
echo "<br/>";
print_r($fabric->getAmountArray_by_orderCountryGmtsitemAndGmtssize());
echo "<br/>";
print_r($fabric->getAmountArray_by_orderCountryGmtscolorAndGmtssize());
echo "<br/>";
print_r($fabric->getAmountArray_by_orderGmtsitemGmtscolorAndGmtssize());
echo "<br/>";
print_r($fabric->getAmountArray_by_orderCountryGmtsitemGmtscolorAndGmtssize());



echo "<br/>";
$et= microtime(true);
echo $et;
echo "<br/>";
echo $et-$st;
echo "<br/>";
echo "Limit 1: " . memory_get_peak_usage() . "\n";*/
?>
