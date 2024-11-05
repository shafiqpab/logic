<?
class commercial extends report{
	private $_By_JobAndPrecostdtlsid='By_JobAndPrecostdtlsid';
	private $_By_JobAndItemid='By_JobAndItemid';
	
	
	private $_By_OrderAndPrecostdtlsid='By_OrderAndPrecostdtlsid';
	private $_By_OrderAndItemid='By_OrderAndItemid';
	
	
	private $_By_OrderCountryAndPrecostdtlsid='By_OrderCountryAndPrecostdtlsid';
	private $_By_OrderCountryAndItemid='By_OrderCountryAndItemid';
	
	
	private $_query="";
	private $_dataArray=array();
	// class constructor
	function __construct(condition $condition){
		parent::__construct($condition);
		$this->_setQuery();
		$this->_setData();
	}// end class constructor
	
	private function _setQuery(){
		//$jobcond=$this->_setJobsString($this->_jobs,'a.job_no');
		//$pocond=$this->_setPoIdsString($this->_poIds, 'b.id');
		$this->_query='select a.job_no AS "job_no",a.total_set_qnty AS "total_set_qnty",b.id AS "id",c.item_number_id AS "item_number_id",c.country_id AS "country_id",c.color_number_id AS "color_number_id",c.size_number_id AS "size_number_id",c.order_quantity AS "order_quantity" ,c.plan_cut_qnty As "plan_cut_qnty" ,d.id AS " pre_cost_dtls_id",d.item_id AS "item_id",d.rate AS "rate",d. amount AS "amount"    from wo_po_details_master a, wo_po_break_down b,wo_po_color_size_breakdown c,wo_pre_cost_comarci_cost_dtls d where 1=1 '.$this->cond.' and a.id=b.job_id and a.id=c.job_id and a.id=d.job_id  and b.id=c.po_break_down_id and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 and c.is_deleted=0 and c.status_active=1';//order by b.id,d.id
	}
	
	public function getQuery(){
		return $this->_query;
	}
	
	private function _setData() {
		$this->_dataArray=$this->condition->sql_select($this->_query);
		return $this;
	}
	
	public function getData() {
		return $this->_dataArray;
	}
	private function _calculateQty($plan_cut_qnty,$costingPerQty,$total_set_qnty,$cons_qnty){
	  //return $reqyarnqnty =def_number_format(($plan_cut_qnty/($costingPerQty*$set_item_ratio))*$cons_qnty,5,"");
	  return $reqyarnqnty =($plan_cut_qnty/$total_set_qnty)*($cons_qnty/$costingPerQty);
	}
	
	private function _calculateAmount($plan_cut_qnty,$costingPerQty,$total_set_qnty,$req_amount){
	return $amount =($req_amount/$costingPerQty)*($plan_cut_qnty/$total_set_qnty);
	 //$amount/$order_price_per_dzn*$ord_qty;
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
		$preCostDtlsId='';
		$itemId='';
		$charge_unit='';
		$amount='';
		//$gmtsitemRatioArray=$this->_gmtsitemRatioArray;
		//$costingPerArray=$this->_costingPerArr;
		$Amount=array();
		//foreach($this->_dataArray as $row)
		while($row=mysql_fetch_assoc($this->_dataArray))
		{
			/*$jobNo=$row[csf('job_no')];
			$poId=$row[csf('id')];
			$itemNumberId=$row[csf('item_number_id')];
			$countryId=$row[csf('country_id')];
			$colorId=$row[csf('color_number_id')];
			$sizeId=$row[csf('size_number_id')];
			$planPutQnty=$row[csf('plan_cut_qnty')];
			$orderQuantity=$row[csf('order_quantity')];
			$total_set_qnty=$row[csf('total_set_qnty')];
			$preCostDtlsId=$row[csf('pre_cost_dtls_id')];
			$itemId=$row[csf('item_id')];
			$charge_unit=$row[csf('rate')];
			$req_amount=$row[csf('amount')];*/
			
			$jobNo=$row['job_no'];
			$poId=$row['id'];
			$itemNumberId=$row['item_number_id'];
			$countryId=$row['country_id'];
			$colorId=$row['color_number_id'];
			$sizeId=$row['size_number_id'];
			$planPutQnty=$row['plan_cut_qnty'];
			$orderQuantity=$row['order_quantity'];
			$total_set_qnty=$row['total_set_qnty'];
			$preCostDtlsId=$row['pre_cost_dtls_id'];
			$itemId=$row['item_id'];
			$charge_unit=$row['rate'];
			$req_amount=$row['amount'];

			
			//$costingPerQty=$this->_costingPer($costingPerArray[$jobNo]);
			//$set_item_ratio=$gmtsitemRatioArray[$jobNo][$itemNumberId];
			//$costingPerQty=$this->_costingPer($this->_costingPerArr[$jobNo]);
			$costingPerQty=$this->_costingPerQtyArr[$jobNo];
			//$set_item_ratio=$this->_gmtsitemRatioArray[$jobNo][$itemNumberId];
			
			$amount=$this->_calculateAmount($orderQuantity,$costingPerQty,$total_set_qnty,$req_amount);
			
			if($level==$this->_By_Job){
				if(isset($Amount[$jobNo])){
					$Amount[$jobNo]+=$amount;
				}
				else{
					$Amount[$jobNo]=$amount;
				}
			}
			elseif($level==$this->_By_JobAndPrecostdtlsid){
				if(isset($Amount[$jobNo][$preCostDtlsId])){
					$Amount[$jobNo][$preCostDtlsId]+=$amount;
				}
				else{
					$Amount[$jobNo][$preCostDtlsId]=$amount;
				}
			}
			elseif($level==$this->_By_JobAndItemid){
				if(isset($Amount[$jobNo][$itemId])){
					$Amount[$jobNo][$itemId]+=$amount;
				}
				else{
					$Amount[$jobNo][$itemId]=$amount;
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
			elseif($level==$this->_By_OrderAndPrecostdtlsid){
				if(isset($Amount[$poId][$preCostDtlsId])){
					$Amount[$poId][$preCostDtlsId]+=$amount;
				}
				else{
					$Amount[$poId][$preCostDtlsId]=$amount;
				}
			}
			elseif($level==$this->_By_OrderAndItemid){
				if(isset($Amount[$poId][$itemId])){
					$Amount[$poId][$itemId]+=$amount;
				}
				else{
					$Amount[$poId][$itemId]=$amount;
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
			elseif($level==$this->_By_OrderCountryAndPrecostdtlsid){
				if(isset($Amount[$poId][$countryId][$preCostDtlsId])){
					$Amount[$poId][$countryId][$preCostDtlsId]+=$amount;
				}
				else{
					$Amount[$poId][$countryId][$preCostDtlsId]=$amount;
				}
			}
			elseif($level==$this->_By_OrderCountryAndItemid){
				if(isset($Amount[$poId][$countryId][$itemId])){
					$Amount[$poId][$countryId][$itemId]+=$amount;
				}
				else{
					$Amount[$poId][$countryId][$itemId]=$amount;
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
	//Amount
	public function getAmount_by_jobAndPrecostdtlsid($jobNo,$preCostDtlsId){
		$Amount=$this->_setAmount($this->_By_JobAndPrecostdtlsid);
		return $Amount[$jobNo][$preCostDtlsId];
	}
	
	public function getAmountArray_by_jobAndPrecostdtlsid(){
		$Amount=$this->_setAmount($this->_By_JobAndPrecostdtlsid);
		return $Amount;
	}
	
	
	//Job and Fabric wise
	//Amount
	public function getAmount_by_jobAndItemid($jobNo,$itemId){
		$Amount=$this->_setAmount($this->_By_JobAndItemid);
		return $Amount[$jobNo][$itemId];
	}
	
	public function getAmountArray_by_jobAndItemid(){
		$Amount=$this->_setAmount($this->_By_JobAndItemid);
		return $Amount;
	}
	
	
	
	
	// Order wise
	
	//Amount
	public function getAmount_by_order($poId){
		$Amount=$this->_setAmount($this->_By_Order);
		return $Amount[$poId];
	}
	
	public function getAmountArray_by_order(){
		$Amount=$this->_setAmount($this->_By_Order);
		return $Amount;
	}
	
	
	//Order and Conversion Id wise
	//Amount
	public function getAmount_by_orderAndPrecostdtlsid($poId,$preCostDtlsId){
		$Amount=$this->_setAmount($this->_By_OrderAndPrecostdtlsid);
		return $Amount[$poId][$preCostDtlsId];
	}
	
	public function getAmountArray_by_orderAndPrecostdtlsid(){
		$Amount=$this->_setAmount($this->_By_OrderAndPrecostdtlsid);
		return $Amount;
	}
	
	
	//Order and Fabric wise
	//Amount
	public function getAmount_by_orderAndItemid($poId,$itemId){
		$Amount=$this->_setAmount($this->_By_OrderAndItemid);
		return $Amount[$poId][$itemId];
	}
	
	public function getAmountArray_by_orderAndItemid(){
		$Amount=$this->_setAmount($this->_By_OrderAndItemid);
		return $Amount;
	}
	
	
	
	
	
	// Order and Country wise
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
	//Amount
	public function getAmount_by_orderCountryAndPrecostdtlsid($poId,$countryId,$preCostDtlsId){
		$Amount=$this->_setAmount($this->_By_OrderCountryAndPrecostdtlsid);
		return $Amount[$poId][$countryId][$preCostDtlsId];
	}
	
	public function getAmountArray_by_orderCountryAndPrecostdtlsid(){
		$Amount=$this->_setAmount($this->_By_OrderCountryAndPrecostdtlsid);
		return $Amount;
	}
	
	
	//Order,Country and Fabric wise
	//Amount
	public function getAmount_by_orderCountryAndItemid($poId,$countryId,$itemId){
		$Amount=$this->_setAmount($this->_By_OrderCountryAndItemid);
		return $Amount[$poId][$countryId][$itemId];
	}
	
	public function getAmountArray_by_orderCountryAndItemid(){
		$Amount=$this->_setAmount($this->_By_OrderCountryAndItemid);
		return $Amount;
	}
	
	
	
	// Order and Gmts Item wise
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
		unset($this->_dataArray);
	}
}
?>