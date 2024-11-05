<?
class trims extends report{
	private $_By_JobAndPrecostdtlsid='By_JobAndPrecostdtlsid';
	private $_By_JobAndItemid='By_JobAndItemid';
	private $_By_JobItemidAndDescription='By_JobItemidAndDescription';
	
	private $_By_OrderAndPrecostdtlsid='By_OrderAndPrecostdtlsid';
	private $_By_OrderAndItemid='By_OrderAndItemid';
	private $_By_OrderItemidAndDescription='By_OrderItemidAndDescription';
	
	private $_By_OrderCountryAndPrecostdtlsid='By_OrderCountryAndPrecostdtlsid';
	private $_By_OrderCountryAndItemid='By_OrderCountryAndItemid';
	private $_By_OrderCountryItemidAndDescription='By_OrderCountryItemidAndDescription';
	
	private $_By_Itemid='By_Itemid';
	private $_By_ItemidAndDescription='By_ItemidAndDescription';
	
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
		$this->_query='select a.job_no AS "job_no",a.total_set_qnty AS "total_set_qnty",b.id AS "id",c.item_number_id AS "item_number_id",c.country_id AS "country_id",c.color_number_id AS "color_number_id",c.size_number_id "size_number_id",c.order_quantity AS "order_quantity" ,c.plan_cut_qnty AS "plan_cut_qnty" ,d.id AS "pre_cost_dtls_id",d.trim_group AS "trim_group",d.description AS "description" ,d.cons_uom AS "cons_uom",d.cons_dzn_gmts "cons_dzn_gmts",d.rate AS "rate",d.amount AS "amount", e.cons AS "cons",e.tot_cons AS "tot_cons",e.country_id AS "country_id_trims" from wo_po_details_master a, wo_po_break_down b,wo_po_color_size_breakdown c,wo_pre_cost_trim_cost_dtls d, wo_pre_cost_trim_co_cons_dtls e where 1=1 '.$this->cond.' and a.id=b.job_id and a.id=c.job_id and a.id=d.job_id and a.id=e.job_id and b.id=c.po_break_down_id and b.id=e.po_break_down_id  and d.id=e.wo_pre_cost_trim_cost_dtls_id and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 and c.is_deleted=0 and c.status_active=1';//order by b.id,d.id
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
		$preCostDtlsId='';
		$trimGroup='';
		$description='';
		$cons_dzn_gmts='';
		$rate='';
		
		$cons='';
		$tot_cons='';
		$country_id_trims='';
		
		//$gmtsitemRatioArray=$this->_gmtsitemRatioArray;
		//$costingPerArray=$this->_costingPerArr;
		$Qty=array();
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
			$trimGroup=$row[csf('trim_group')];
			$description=$row[csf('description')];
			$cons_dzn_gmts=$row[csf('cons_dzn_gmts')];
			$rate=$row[csf('rate')];
			
			$cons=$row[csf('cons')];
			$tot_cons=$row[csf('tot_cons')];
			$country_id_trims=$row[csf('country_id_trims')];*/
			
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
			$trimGroup=$row['trim_group'];
			$description=$row['description'];
			$cons_dzn_gmts=$row['cons_dzn_gmts'];
			$rate=$row['rate'];
			
			$cons=$row['cons'];
			$tot_cons=$row['tot_cons'];
			$country_id_trims=$row['country_id_trims'];
			
			
			//$costingPerQty=$this->_costingPer($costingPerArray[$jobNo]);
			//$set_item_ratio=$gmtsitemRatioArray[$jobNo][$itemNumberId];
			//$costingPerQty=$this->_costingPer($this->_costingPerArr[$jobNo]);
			
			$costingPerQty=$this->_costingPerQtyArr[$jobNo];
			//$set_item_ratio=$this->_gmtsitemRatioArray[$jobNo][$itemNumberId];
			
			$reqqnty=0;
			if($country_id_trims==0 || $country_id_trims==''){
			    $reqqnty=$this->_calculateQty($orderQuantity,$costingPerQty,$total_set_qnty,$cons);
			}
			else{
				$country_id_array=explode(",",$country_id_trims);
				if (in_array($countryId, $country_id_array)) {
					$reqqnty=$this->_calculateQty($orderQuantity,$costingPerQty,$total_set_qnty,$cons);
				}
			}
			
			if($level==$this->_By_Job){
				if(isset($Qty[$jobNo])){
					$Qty[$jobNo]+=$reqqnty;
				}
				else{
					$Qty[$jobNo]=$reqqnty;
				}
			}
			elseif($level==$this->_By_JobAndPrecostdtlsid){
				if(isset($Qty[$jobNo][$preCostDtlsId])){
					$Qty[$jobNo][$preCostDtlsId]+=$reqqnty;
				}
				else{
					$Qty[$jobNo][$preCostDtlsId]=$reqqnty;
				}
			}
			elseif($level==$this->_By_JobAndItemid){
				if(isset($Qty[$jobNo][$trimGroup])){
					$Qty[$jobNo][$trimGroup]+=$reqqnty;
				}
				else{
					$Qty[$jobNo][$trimGroup]=$reqqnty;
				}
			}
			
			elseif($level==$this->_By_JobItemidAndDescription){
				if(isset($Qty[$jobNo][$trimGroup][$description])){
					$Qty[$jobNo][$trimGroup][$description]+=$reqqnty;
				}
				else{
					$Qty[$jobNo][$trimGroup][$description]=$reqqnty;
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
			elseif($level==$this->_By_OrderAndPrecostdtlsid){
				if(isset($Qty[$poId][$preCostDtlsId])){
					$Qty[$poId][$preCostDtlsId]+=$reqqnty;
				}
				else{
					$Qty[$poId][$preCostDtlsId]=$reqqnty;
				}
			}
			elseif($level==$this->_By_OrderAndItemid){
				if(isset($Qty[$poId][$trimGroup])){
					$Qty[$poId][$trimGroup]+=$reqqnty;
				}
				else{
					$Qty[$poId][$trimGroup]=$reqqnty;
				}
			}
			
			elseif($level==$this->_By_OrderItemidAndDescription){
				if(isset($Qty[$poId][$trimGroup][$description])){
					$Qty[$poId][$trimGroup][$description]+=$reqqnty;
				}
				else{
					$Qty[$poId][$trimGroup][$description]=$reqqnty;
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
			elseif($level==$this->_By_OrderCountryAndPrecostdtlsid){
				if(isset($Qty[$poId][$countryId][$preCostDtlsId])){
					$Qty[$poId][$countryId][$preCostDtlsId]+=$reqqnty;
				}
				else{
					$Qty[$poId][$countryId][$preCostDtlsId]=$reqqnty;
				}
			}
			elseif($level==$this->_By_OrderCountryAndItemid){
				if(isset($Qty[$poId][$countryId][$trimGroup])){
					$Qty[$poId][$countryId][$trimGroup]+=$reqqnty;
				}
				else{
					$Qty[$poId][$countryId][$trimGroup]=$reqqnty;
				}
			}
			elseif($level==$this->_By_OrderCountryItemidAndDescription){
				if(isset($Qty[$poId][$countryId][$trimGroup][$description])){
					$Qty[$poId][$countryId][$trimGroup][$description]+=$reqqnty;
				}
				else{
					$Qty[$poId][$countryId][$trimGroup][$description]=$reqqnty;
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
			elseif($level==$this->_By_Itemid){
				if(isset($Qty[$trimGroup])){
					$Qty[$trimGroup]+=$reqqnty;
				}
				else{
					$Qty[$trimGroup]=$reqqnty;
				}
			}
			elseif($level==$this->_By_ItemidAndDescription){
				if(isset($Qty[$trimGroup][$description])){
					$Qty[$trimGroup][$description]+=$reqqnty;
				}
				else{
					$Qty[$trimGroup][$description]=$reqqnty;
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
		$preCostDtlsId='';
		$trimGroup='';
		$description='';
		$cons_dzn_gmts='';
		$rate='';
		
		$cons='';
		$tot_cons='';
		$country_id_trims='';
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
			$trimGroup=$row[csf('trim_group')];
			$description=$row[csf('description')];
			$cons_dzn_gmts=$row[csf('cons_dzn_gmts')];
			$rate=$row[csf('rate')];
			
			$cons=$row[csf('cons')];
			$tot_cons=$row[csf('tot_cons')];
			$country_id_trims=$row[csf('country_id_trims')];*/
			
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
			$trimGroup=$row['trim_group'];
			$description=$row['description'];
			$cons_dzn_gmts=$row['cons_dzn_gmts'];
			$rate=$row['rate'];
			
			$cons=$row['cons'];
			$tot_cons=$row['tot_cons'];
			$country_id_trims=$row['country_id_trims'];
			
			//$costingPerQty=$this->_costingPer($costingPerArray[$jobNo]);
			//$set_item_ratio=$gmtsitemRatioArray[$jobNo][$itemNumberId];
			
			//$costingPerQty=$this->_costingPer($this->_costingPerArr[$jobNo]);
			$costingPerQty=$this->_costingPerQtyArr[$jobNo];
			//$set_item_ratio=$this->_gmtsitemRatioArray[$jobNo][$itemNumberId];
			
			$reqqnty=0;
			$amount=0;
			if($country_id_trims==0 || $country_id_trims==''){
			$reqqnty=$this->_calculateQty($orderQuantity,$costingPerQty,$total_set_qnty,$cons);
			$amount=$this->_calculateAmount($reqqnty,$rate);
			}
			else{
				$country_id_array=explode(",",$country_id_trims);
				if (in_array($countryId, $country_id_array)) {
					$reqqnty=$this->_calculateQty($orderQuantity,$costingPerQty,$total_set_qnty,$cons);
					$amount=$this->_calculateAmount($reqqnty,$rate);
				}
			}
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
				if(isset($Amount[$jobNo][$trimGroup])){
					$Amount[$jobNo][$trimGroup]+=$amount;
				}
				else{
					$Amount[$jobNo][$trimGroup]=$amount;
				}
			}
			
			elseif($level==$this->_By_JobItemidAndDescription){
				if(isset($Amount[$jobNo][$trimGroup][$description])){
					$Amount[$jobNo][$trimGroup][$description]+=$amount;
				}
				else{
					$Amount[$jobNo][$trimGroup][$description]=$amount;
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
				if(isset($Amount[$poId][$trimGroup])){
					$Amount[$poId][$trimGroup]+=$amount;
				}
				else{
					$Amount[$poId][$trimGroup]=$amount;
				}
			}
			
			elseif($level==$this->_By_OrderItemidAndDescription){
				if(isset($Amount[$poId][$trimGroup][$description])){
					$Amount[$poId][$trimGroup][$description]+=$amount;
				}
				else{
					$Amount[$poId][$trimGroup][$description]=$amount;
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
				if(isset($Amount[$poId][$countryId][$trimGroup])){
					$Amount[$poId][$countryId][$trimGroup]+=$amount;
				}
				else{
					$Amount[$poId][$countryId][$trimGroup]=$amount;
				}
			}
			
			elseif($level==$this->_By_OrderCountryItemidAndDescription){
				if(isset($Amount[$poId][$countryId][$trimGroup][$description])){
					$Amount[$poId][$countryId][$trimGroup][$description]+=$amount;
				}
				else{
					$Amount[$poId][$countryId][$trimGroup][$description]=$amount;
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
			elseif($level==$this->_By_Itemid){
				if(isset($Amount[$trimGroup])){
					$Amount[$trimGroup]+=$amount;
				}
				else{
					$Amount[$trimGroup]=$amount;
				}
			}
			elseif($level==$this->_By_ItemidAndDescription){
				if(isset($Amount[$trimGroup][$description])){
					$Amount[$trimGroup][$description]+=$amount;
				}
				else{
					$Amount[$trimGroup][$description]=$amount;
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
	public function getQty_by_jobAndPrecostdtlsid($jobNo,$preCostDtlsId){
		$Qty=$this->_setQty($this->_By_JobAndPrecostdtlsid);
		return $Qty[$jobNo][$preCostDtlsId];
	}
	
	public function getQtyArray_by_jobAndPrecostdtlsid(){
		$Qty=$this->_setQty($this->_By_JobAndPrecostdtlsid);
		return $Qty;
		
	}
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
	//Qty
	public function getQty_by_jobAndItemid($jobNo,$trimGroup){
		$Qty=$this->_setQty($this->_By_JobAndItemid);
		return $Qty[$jobNo][$trimGroup];
	}
	
	public function getQtyArray_by_jobAndItemid(){
		$Qty=$this->_setQty($this->_By_JobAndItemid);
		return $Qty;
		
	}
	//Amount
	public function getAmount_by_jobAndItemid($jobNo,$trimGroup){
		$Amount=$this->_setAmount($this->_By_JobAndItemid);
		return $Amount[$jobNo][$trimGroup];
	}
	
	public function getAmountArray_by_jobAndItemid(){
		$Amount=$this->_setAmount($this->_By_JobAndItemid);
		return $Amount;
	}
	
	
	
	//Job, Fabric and Process wise
	//Qty
	public function getQty_by_jobItemidAndDescription($jobNo,$trimGroup,$description){
		$Qty=$this->_setQty($this->_By_JobItemidAndDescription);
		return $Qty[$jobNo][$trimGroup][$description];
	}
	
	public function getQtyArray_by_jobItemidAndDescription(){
		$Qty=$this->_setQty($this->_By_JobItemidAndDescription);
		return $Qty;
		
	}
	//Amount
	public function getAmount_by_jobItemidAndDescription($jobNo,$trimGroup,$description){
		$Amount=$this->_setAmount($this->_By_JobItemidAndDescription);
		return $Amount[$jobNo][$trimGroup][$description];
	}
	
	public function getAmountArray_by_jobItemidAndDescription(){
		$Amount=$this->_setAmount($this->_By_JobItemidAndDescription);
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
	public function getAmount_by_order($poId){
		$Amount=$this->_setAmount($this->_By_Order);
		return $Amount[$poId];
	}
	
	public function getAmountArray_by_order(){
		$Amount=$this->_setAmount($this->_By_Order);
		return $Amount;
	}
	
	
	//Order and Conversion Id wise
	//Qty
	public function getQty_by_orderAndPrecostdtlsid($poId,$preCostDtlsId){
		$Qty=$this->_setQty($this->_By_OrderAndPrecostdtlsid);
		return $Qty[$poId][$preCostDtlsId];
	}
	
	public function getQtyArray_by_orderAndPrecostdtlsid(){
		$Qty=$this->_setQty($this->_By_OrderAndPrecostdtlsid);
		return $Qty;
		
	}
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
	//Qty
	public function getQty_by_orderAndItemid($poId,$trimGroup){
		$Qty=$this->_setQty($this->_By_OrderAndItemid);
		return $Qty[$poId][$trimGroup];
	}
	
	public function getQtyArray_by_orderAndItemid(){
		$Qty=$this->_setQty($this->_By_OrderAndItemid);
		return $Qty;
		
	}
	//Amount
	public function getAmount_by_orderAndItemid($poId,$trimGroup){
		$Amount=$this->_setAmount($this->_By_OrderAndItemid);
		return $Amount[$poId][$trimGroup];
	}
	
	public function getAmountArray_by_orderAndItemid(){
		$Amount=$this->_setAmount($this->_By_OrderAndItemid);
		return $Amount;
	}
	//Order Fabric and Process wise
	//Qty
	public function getQty_by_orderItemidAndDescription($poId,$trimGroup,$description){
		$Qty=$this->_setQty($this->_By_OrderItemidAndDescription);
		return $Qty[$poId][$trimGroup][$description];
	}
	
	public function getQtyArray_by_orderItemidAndDescription(){
		$Qty=$this->_setQty($this->_By_OrderItemidAndDescription);
		return $Qty;
		
	}
	//Amount
	public function getAmount_by_orderItemidAndDescription($poId,$trimGroup,$description){
		$Amount=$this->_setAmount($this->_By_OrderItemidAndDescription);
		return $Amount[$poId][$trimGroup][$description];
	}
	
	public function getAmountArray_by_orderItemidAndDescription(){
		$Amount=$this->_setAmount($this->_By_OrderItemidAndDescription);
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
	public function getQty_by_orderCountryAndPrecostdtlsid($poId,$countryId,$preCostDtlsId){
		$Qty=$this->_setQty($this->_By_OrderCountryAndPrecostdtlsid);
		return $Qty[$poId][$countryId][$preCostDtlsId];
	}
	
	public function getQtyArray_by_orderCountryAndPrecostdtlsid(){
		$Qty=$this->_setQty($this->_By_OrderCountryAndPrecostdtlsid);
		return $Qty;
		
	}
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
	//Qty
	public function getQty_by_orderCountryAndItemid($poId,$countryId,$trimGroup){
		$Qty=$this->_setQty($this->_By_OrderCountryAndItemid);
		return $Qty[$poId][$countryId][$trimGroup];
	}
	
	public function getQtyArray_by_orderCountryAndItemid(){
		$Qty=$this->_setQty($this->_By_OrderCountryAndItemid);
		return $Qty;
		
	}
	//Amount
	public function getAmount_by_orderCountryAndItemid($poId,$countryId,$trimGroup){
		$Amount=$this->_setAmount($this->_By_OrderCountryAndItemid);
		return $Amount[$poId][$countryId][$trimGroup];
	}
	
	public function getAmountArray_by_orderCountryAndItemid(){
		$Amount=$this->_setAmount($this->_By_OrderCountryAndItemid);
		return $Amount;
	}
	
	
	
	//Order,Country Fabric and Process wise
	//Qty
	public function getQty_by_orderCountryItemidAndDescription($poId,$countryId,$trimGroup,$description){
		$Qty=$this->_setQty($this->_By_OrderCountryItemidAndDescription);
		return $Qty[$poId][$countryId][$trimGroup][$description];
	}
	
	public function getQtyArray_by_orderCountryItemidAndDescription(){
		$Qty=$this->_setQty($this->_By_OrderCountryItemidAndDescription);
		return $Qty;
		
	}
	//Amount
	public function getAmount_by_orderCountryItemidAndDescription($poId,$countryId,$trimGroup,$description){
		$Amount=$this->_setAmount($this->_By_OrderCountryItemidAndDescription);
		return $Amount[$poId][$countryId][$trimGroup][$description];
	}
	
	public function getAmountArray_by_orderCountryItemidAndDescription(){
		$Amount=$this->_setAmount($this->_By_OrderCountryItemidAndDescription);
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
	
	// Item wise
	//Qty
	public function getQty_by_itemid($trimGroup){
		$Qty=$this->_setQty($this->_By_Itemid);
		return $Qty[$trimGroup];
	}
	
	public function getQtyArray_by_itemid(){
		$Qty=$this->_setQty($this->_By_Itemid);
		return $Qty;
		
	}
	//Amount
	public function getAmount_by_itemid($trimGroup){
		$Amount=$this->_setAmount($this->_By_Itemid);
		return $Amount[$trimGroup];
	}
	
	public function getAmountArray_by_itemid(){
		$Amount=$this->_setAmount($this->_By_Itemid);
		return $Amount;
	}
	
	// Item and description wise
	//Qty
	public function getQty_by_itemidAndDescription($trimGroup){
		$Qty=$this->_setQty($this->_By_ItemidAndDescription);
		return $Qty[$trimGroup];
	}
	
	public function getQtyArray_by_itemidAndDescription(){
		$Qty=$this->_setQty($this->_By_ItemidAndDescription);
		return $Qty;
		
	}
	//Amount
	public function getAmount_by_itemidAndDescription($trimGroup){
		$Amount=$this->_setAmount($this->_By_ItemidAndDescription);
		return $Amount[$trimGroup];
	}
	
	public function getAmountArray_by_itemidAndDescription(){
		$Amount=$this->_setAmount($this->_By_ItemidAndDescription);
		return $Amount;
	}
	function __destruct() {
		parent::__destruct();
		unset($this->_dataArray);
	}
}
?>