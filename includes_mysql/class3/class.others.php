<?
class other extends report{
	private $_lab_test='lab_test';
	private $_inspection='inspection';
	private $_cm_cost='cm_cost';
	private $_freight='freight';
	private $_currier_pre_cost='currier_pre_cost';
	private $_certificate_pre_cost='certificate_pre_cost';
	private $_common_oh='common_oh';
	private $_depr_amor_pre_cost='depr_amor_pre_cost';
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
		$this->_query='select a.job_no AS "job_no",a.total_set_qnty AS "total_set_qnty",b.id AS "id",c.item_number_id AS "item_number_id",c.country_id AS "country_id",c.color_number_id AS "color_number_id",c.size_number_id AS "size_number_id",c.order_quantity AS "order_quantity" ,c.plan_cut_qnty AS "plan_cut_qnty" ,d.id AS "pre_cost_dtls_id",d.lab_test AS "lab_test",d.inspection AS "inspection" ,d.cm_cost AS "cm_cost",d.freight AS "freight",d.currier_pre_cost AS "currier_pre_cost",d.certificate_pre_cost AS "certificate_pre_cost",d.common_oh AS "common_oh",d.depr_amor_pre_cost AS "depr_amor_pre_cost"  from wo_po_details_master a, wo_po_break_down b,wo_po_color_size_breakdown c,wo_pre_cost_dtls d where 1=1  '.$this->cond.' and  a.id=b.job_id and a.id=c.job_id and a.id=d.job_id and b.id=c.po_break_down_id  and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 and c.is_deleted=0 and c.status_active=1';//order by b.id,d.id
		
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
			
			$lab_test=$row[csf('lab_test')];
			$inspection=$row[csf('inspection')];
			$cm_cost=$row[csf('cm_cost')];
			$freight=$row[csf('freight')];
			$currier_pre_cost=$row[csf('currier_pre_cost')];
			$certificate_pre_cost=$row[csf('certificate_pre_cost')];
			$common_oh=$row[csf('common_oh')];
			$depr_amor_pre_cost=$row[csf('depr_amor_pre_cost')];*/
			
			$jobNo=$row['job_no'];
			$poId=$row['id'];
			$itemNumberId=$row['item_number_id'];
			$countryId=$row['country_id'];
			$colorId=$row['color_number_id'];
			$sizeId=$row['size_number_id'];
			$planPutQnty=$row['plan_cut_qnty'];
			$orderQuantity=$row['order_quantity'];
			$total_set_qnty=$row['total_set_qnty'];
			
			$lab_test=$row['lab_test'];
			$inspection=$row['inspection'];
			$cm_cost=$row['cm_cost'];
			$freight=$row['freight'];
			$currier_pre_cost=$row['currier_pre_cost'];
			$certificate_pre_cost=$row['certificate_pre_cost'];
			$common_oh=$row['common_oh'];
			$depr_amor_pre_cost=$row['depr_amor_pre_cost'];

			
			//$costingPerQty=$this->_costingPer($costingPerArray[$jobNo]);
			//$set_item_ratio=$gmtsitemRatioArray[$jobNo][$itemNumberId];
			//$costingPerQty=$this->_costingPer($this->_costingPerArr[$jobNo]);
			$costingPerQty=$this->_costingPerQtyArr[$jobNo];
			//$set_item_ratio=$this->_gmtsitemRatioArray[$jobNo][$itemNumberId];
			
			$lab_test_amount=$this->_calculateAmount($orderQuantity,$costingPerQty,$total_set_qnty,$lab_test);
			$inspection_amount=$this->_calculateAmount($orderQuantity,$costingPerQty,$total_set_qnty,$inspection);
			$cm_cost_amount=$this->_calculateAmount($orderQuantity,$costingPerQty,$total_set_qnty,$cm_cost);
			$freight_amount=$this->_calculateAmount($orderQuantity,$costingPerQty,$total_set_qnty,$freight);
			$currier_pre_cost_amount=$this->_calculateAmount($orderQuantity,$costingPerQty,$total_set_qnty,$currier_pre_cost);
			$common_oh_amount=$this->_calculateAmount($orderQuantity,$costingPerQty,$total_set_qnty,$common_oh);
			$depr_amor_pre_cost_amount=$this->_calculateAmount($orderQuantity,$costingPerQty,$total_set_qnty,$depr_amor_pre_cost);
			$certificate_pre_cost_amount=$this->_calculateAmount($orderQuantity,$costingPerQty,$total_set_qnty,$certificate_pre_cost);
			
			if($level==$this->_By_Job){
				if(isset($Amount[$jobNo][$this->_lab_test])){
					$Amount[$jobNo][$this->_lab_test]+=$lab_test_amount;
				}
				else{
					$Amount[$jobNo][$this->_lab_test]=$lab_test_amount;
				}
				
				if(isset($Amount[$jobNo][$this->_inspection])){
					$Amount[$jobNo][$this->_inspection]+=$inspection_amount;
				}
				else{
					$Amount[$jobNo][$this->_inspection]=$inspection_amount;
				}
				
				if(isset($Amount[$jobNo][$this->_cm_cost])){
					$Amount[$jobNo][$this->_cm_cost]+=$cm_cost_amount;
				}
				else{
					$Amount[$jobNo][$this->_cm_cost]=$cm_cost_amount;
				}
				
				if(isset($Amount[$jobNo][$this->_freight])){
					$Amount[$jobNo][$this->_freight]+=$freight_amount;
				}
				else{
					$Amount[$jobNo][$this->_freight]=$freight_amount;
				}
				
				if(isset($Amount[$jobNo][$this->_currier_pre_cost])){
					$Amount[$jobNo][$this->_currier_pre_cost]+=$currier_pre_cost_amount;
				}
				else{
					$Amount[$jobNo][$this->_currier_pre_cost]=$currier_pre_cost_amount;
				}
				
				if(isset($Amount[$jobNo][$this->_common_oh])){
					$Amount[$jobNo][$this->_common_oh]+=$common_oh_amount;
				}
				else{
					$Amount[$jobNo][$this->_common_oh]=$common_oh_amount;
				}
				
				if(isset($Amount[$jobNo][$this->_depr_amor_pre_cost])){
					$Amount[$jobNo][$this->_depr_amor_pre_cost]+=$depr_amor_pre_cost_amount;
				}
				else{
					$Amount[$jobNo][$this->_depr_amor_pre_cost]=$depr_amor_pre_cost_amount;
				}
				if(isset($Amount[$jobNo][$this->_certificate_pre_cost])){
					$Amount[$jobNo][$this->_certificate_pre_cost]+=$certificate_pre_cost_amount;
				}
				else{
					$Amount[$jobNo][$this->_certificate_pre_cost]=$certificate_pre_cost_amount;
				}
			}
			elseif($level==$this->_By_Order){
				if(isset($Amount[$poId][$this->_lab_test])){
					$Amount[$poId][$this->_lab_test]+=$lab_test_amount;
				}
				else{
					$Amount[$poId][$this->_lab_test]=$lab_test_amount;
				}
				
				if(isset($Amount[$poId][$this->_inspection])){
					$Amount[$poId][$this->_inspection]+=$inspection_amount;
				}
				else{
					$Amount[$poId][$this->_inspection]=$inspection_amount;
				}
				
				if(isset($Amount[$poId][$this->_cm_cost])){
					$Amount[$poId][$this->_cm_cost]+=$cm_cost_amount;
				}
				else{
					$Amount[$poId][$this->_cm_cost]=$cm_cost_amount;
				}
				
				if(isset($Amount[$poId][$this->_freight])){
					$Amount[$poId][$this->_freight]+=$freight_amount;
				}
				else{
					$Amount[$poId][$this->_freight]=$freight_amount;
				}
				
				if(isset($Amount[$poId][$this->_currier_pre_cost])){
					$Amount[$poId][$this->_currier_pre_cost]+=$currier_pre_cost_amount;
				}
				else{
					$Amount[$poId][$this->_currier_pre_cost]=$currier_pre_cost_amount;
				}
				
				if(isset($Amount[$poId][$this->_common_oh])){
					$Amount[$poId][$this->_common_oh]+=$common_oh_amount;
				}
				else{
					$Amount[$poId][$this->_common_oh]=$common_oh_amount;
				}
				
				if(isset($Amount[$poId][$this->_depr_amor_pre_cost])){
					$Amount[$poId][$this->_depr_amor_pre_cost]+=$depr_amor_pre_cost_amount;
				}
				else{
					$Amount[$poId][$this->_depr_amor_pre_cost]=$depr_amor_pre_cost_amount;
				}
				if(isset($Amount[$poId][$this->_certificate_pre_cost])){
					$Amount[$poId][$this->_certificate_pre_cost]+=$certificate_pre_cost_amount;
				}
				else{
					$Amount[$poId][$this->_certificate_pre_cost]=$certificate_pre_cost_amount;
				}
			}
			elseif($level==$this->_By_OrderAndCountry){
				if(isset($Amount[$poId][$countryId][$this->_lab_test])){
					$Amount[$poId][$countryId][$this->_lab_test]+=$lab_test_amount;
				}
				else{
					$Amount[$poId][$countryId][$this->_lab_test]=$lab_test_amount;
				}
				
				if(isset($Amount[$poId][$countryId][$this->_inspection])){
					$Amount[$poId][$countryId][$this->_inspection]+=$inspection_amount;
				}
				else{
					$Amount[$poId][$countryId][$this->_inspection]=$inspection_amount;
				}
				
				if(isset($Amount[$poId][$countryId][$this->_cm_cost])){
					$Amount[$poId][$countryId][$this->_cm_cost]+=$cm_cost_amount;
				}
				else{
					$Amount[$poId][$countryId][$this->_cm_cost]=$cm_cost_amount;
				}
				
				if(isset($Amount[$poId][$countryId][$this->_freight])){
					$Amount[$poId][$countryId][$this->_freight]+=$freight_amount;
				}
				else{
					$Amount[$poId][$countryId][$this->_freight]=$freight_amount;
				}
				
				if(isset($Amount[$poId][$countryId][$this->_currier_pre_cost])){
					$Amount[$poId][$countryId][$this->_currier_pre_cost]+=$currier_pre_cost_amount;
				}
				else{
					$Amount[$poId][$countryId][$this->_currier_pre_cost]=$currier_pre_cost_amount;
				}
				
				if(isset($Amount[$poId][$countryId][$this->_common_oh])){
					$Amount[$poId][$countryId][$this->_common_oh]+=$common_oh_amount;
				}
				else{
					$Amount[$poId][$countryId][$this->_common_oh]=$common_oh_amount;
				}
				
				if(isset($Amount[$poId][$countryId][$this->_depr_amor_pre_cost])){
					$Amount[$poId][$countryId][$this->_depr_amor_pre_cost]+=$depr_amor_pre_cost_amount;
				}
				else{
					$Amount[$poId][$countryId][$this->_depr_amor_pre_cost]=$depr_amor_pre_cost_amount;
				}
				if(isset($Amount[$poId][$countryId][$this->_certificate_pre_cost])){
					$Amount[$poId][$countryId][$this->_certificate_pre_cost]+=$certificate_pre_cost_amount;
				}
				else{
					$Amount[$poId][$countryId][$this->_certificate_pre_cost]=$certificate_pre_cost_amount;
				}
			}
			elseif($level==$this->_By_OrderAndGmtsitem){
				if(isset($Amount[$poId][$itemNumberId][$this->_lab_test])){
					$Amount[$poId][$itemNumberId][$this->_lab_test]+=$lab_test_amount;
				}
				else{
					$Amount[$poId][$itemNumberId][$this->_lab_test]=$lab_test_amount;
				}
				
				if(isset($Amount[$poId][$itemNumberId][$this->_inspection])){
					$Amount[$poId][$itemNumberId][$this->_inspection]+=$inspection_amount;
				}
				else{
					$Amount[$poId][$itemNumberId][$this->_inspection]=$inspection_amount;
				}
				
				if(isset($Amount[$poId][$itemNumberId][$this->_cm_cost])){
					$Amount[$poId][$itemNumberId][$this->_cm_cost]+=$cm_cost_amount;
				}
				else{
					$Amount[$poId][$itemNumberId][$this->_cm_cost]=$cm_cost_amount;
				}
				
				if(isset($Amount[$poId][$itemNumberId][$this->_freight])){
					$Amount[$poId][$itemNumberId][$this->_freight]+=$freight_amount;
				}
				else{
					$Amount[$poId][$itemNumberId][$this->_freight]=$freight_amount;
				}
				
				if(isset($Amount[$poId][$itemNumberId][$this->_currier_pre_cost])){
					$Amount[$poId][$itemNumberId][$this->_currier_pre_cost]+=$currier_pre_cost_amount;
				}
				else{
					$Amount[$poId][$itemNumberId][$this->_currier_pre_cost]=$currier_pre_cost_amount;
				}
				
				if(isset($Amount[$poId][$itemNumberId][$this->_common_oh])){
					$Amount[$poId][$itemNumberId][$this->_common_oh]+=$common_oh_amount;
				}
				else{
					$Amount[$poId][$itemNumberId][$this->_common_oh]=$common_oh_amount;
				}
				
				if(isset($Amount[$poId][$itemNumberId][$this->_depr_amor_pre_cost])){
					$Amount[$poId][$itemNumberId][$this->_depr_amor_pre_cost]+=$depr_amor_pre_cost_amount;
				}
				else{
					$Amount[$poId][$itemNumberId][$this->_depr_amor_pre_cost]=$depr_amor_pre_cost_amount;
				}
				if(isset($Amount[$poId][$itemNumberId][$this->_certificate_pre_cost])){
					$Amount[$poId][$itemNumberId][$this->_certificate_pre_cost]+=$certificate_pre_cost_amount;
				}
				else{
					$Amount[$poId][$itemNumberId][$this->_certificate_pre_cost]=$certificate_pre_cost_amount;
				}
			}
			
			
			elseif($level==$this->_By_OrderAndGmtscolor){
				if(isset($Amount[$poId][$colorId][$this->_lab_test])){
					$Amount[$poId][$colorId][$this->_lab_test]+=$lab_test_amount;
				}
				else{
					$Amount[$poId][$colorId][$this->_lab_test]=$lab_test_amount;
				}
				
				if(isset($Amount[$poId][$colorId][$this->_inspection])){
					$Amount[$poId][$colorId][$this->_inspection]+=$inspection_amount;
				}
				else{
					$Amount[$poId][$colorId][$this->_inspection]=$inspection_amount;
				}
				
				if(isset($Amount[$poId][$colorId][$this->_cm_cost])){
					$Amount[$poId][$colorId][$this->_cm_cost]+=$cm_cost_amount;
				}
				else{
					$Amount[$poId][$colorId][$this->_cm_cost]=$cm_cost_amount;
				}
				
				if(isset($Amount[$poId][$colorId][$this->_freight])){
					$Amount[$poId][$colorId][$this->_freight]+=$freight_amount;
				}
				else{
					$Amount[$poId][$colorId][$this->_freight]=$freight_amount;
				}
				
				if(isset($Amount[$poId][$colorId][$this->_currier_pre_cost])){
					$Amount[$poId][$colorId][$this->_currier_pre_cost]+=$currier_pre_cost_amount;
				}
				else{
					$Amount[$poId][$colorId][$this->_currier_pre_cost]=$currier_pre_cost_amount;
				}
				
				if(isset($Amount[$poId][$colorId][$this->_common_oh])){
					$Amount[$poId][$colorId][$this->_common_oh]+=$common_oh_amount;
				}
				else{
					$Amount[$poId][$colorId][$this->_common_oh]=$common_oh_amount;
				}
				
				if(isset($Amount[$poId][$colorId][$this->_depr_amor_pre_cost])){
					$Amount[$poId][$colorId][$this->_depr_amor_pre_cost]+=$depr_amor_pre_cost_amount;
				}
				else{
					$Amount[$poId][$colorId][$this->_depr_amor_pre_cost]=$depr_amor_pre_cost_amount;
				}
				if(isset($Amount[$poId][$colorId][$this->_certificate_pre_cost])){
					$Amount[$poId][$colorId][$this->_certificate_pre_cost]+=$certificate_pre_cost_amount;
				}
				else{
					$Amount[$poId][$colorId][$this->_certificate_pre_cost]=$certificate_pre_cost_amount;
				}
			}
			elseif($level==$this->_By_OrderAndGmtssize){
				if(isset($Amount[$poId][$sizeId][$this->_lab_test])){
					$Amount[$poId][$sizeId][$this->_lab_test]+=$lab_test_amount;
				}
				else{
					$Amount[$poId][$sizeId][$this->_lab_test]=$lab_test_amount;
				}
				
				if(isset($Amount[$poId][$sizeId][$this->_inspection])){
					$Amount[$poId][$sizeId][$this->_inspection]+=$inspection_amount;
				}
				else{
					$Amount[$poId][$sizeId][$this->_inspection]=$inspection_amount;
				}
				
				if(isset($Amount[$poId][$sizeId][$this->_cm_cost])){
					$Amount[$poId][$sizeId][$this->_cm_cost]+=$cm_cost_amount;
				}
				else{
					$Amount[$poId][$sizeId][$this->_cm_cost]=$cm_cost_amount;
				}
				
				if(isset($Amount[$poId][$sizeId][$this->_freight])){
					$Amount[$poId][$sizeId][$this->_freight]+=$freight_amount;
				}
				else{
					$Amount[$poId][$sizeId][$this->_freight]=$freight_amount;
				}
				
				if(isset($Amount[$poId][$sizeId][$this->_currier_pre_cost])){
					$Amount[$poId][$sizeId][$this->_currier_pre_cost]+=$currier_pre_cost_amount;
				}
				else{
					$Amount[$poId][$sizeId][$this->_currier_pre_cost]=$currier_pre_cost_amount;
				}
				
				if(isset($Amount[$poId][$sizeId][$this->_common_oh])){
					$Amount[$poId][$sizeId][$this->_common_oh]+=$common_oh_amount;
				}
				else{
					$Amount[$poId][$sizeId][$this->_common_oh]=$common_oh_amount;
				}
				
				if(isset($Amount[$poId][$sizeId][$this->_depr_amor_pre_cost])){
					$Amount[$poId][$sizeId][$this->_depr_amor_pre_cost]+=$depr_amor_pre_cost_amount;
				}
				else{
					$Amount[$poId][$sizeId][$this->_depr_amor_pre_cost]=$depr_amor_pre_cost_amount;
				}
				if(isset($Amount[$poId][$sizeId][$this->_certificate_pre_cost])){
					$Amount[$poId][$sizeId][$this->_certificate_pre_cost]+=$certificate_pre_cost_amount;
				}
				else{
					$Amount[$poId][$sizeId][$this->_certificate_pre_cost]=$certificate_pre_cost_amount;
				}
			}
			elseif($level==$this->_By_orderCountryAndGmtsitem){
				if(isset($Amount[$poId][$countryId][$itemNumberId][$this->_lab_test])){
					$Amount[$poId][$countryId][$itemNumberId][$this->_lab_test]+=$lab_test_amount;
				}
				else{
					$Amount[$poId][$countryId][$itemNumberId][$this->_lab_test]=$lab_test_amount;
				}
				
				if(isset($Amount[$poId][$countryId][$itemNumberId][$this->_inspection])){
					$Amount[$poId][$countryId][$itemNumberId][$this->_inspection]+=$inspection_amount;
				}
				else{
					$Amount[$poId][$countryId][$itemNumberId][$this->_inspection]=$inspection_amount;
				}
				
				if(isset($Amount[$poId][$countryId][$itemNumberId][$this->_cm_cost])){
					$Amount[$poId][$countryId][$itemNumberId][$this->_cm_cost]+=$cm_cost_amount;
				}
				else{
					$Amount[$poId][$countryId][$itemNumberId][$this->_cm_cost]=$cm_cost_amount;
				}
				
				if(isset($Amount[$poId][$countryId][$itemNumberId][$this->_freight])){
					$Amount[$poId][$countryId][$itemNumberId][$this->_freight]+=$freight_amount;
				}
				else{
					$Amount[$poId][$countryId][$itemNumberId][$this->_freight]=$freight_amount;
				}
				
				if(isset($Amount[$poId][$countryId][$itemNumberId][$this->_currier_pre_cost])){
					$Amount[$poId][$countryId][$itemNumberId][$this->_currier_pre_cost]+=$currier_pre_cost_amount;
				}
				else{
					$Amount[$poId][$countryId][$itemNumberId][$this->_currier_pre_cost]=$currier_pre_cost_amount;
				}
				
				if(isset($Amount[$poId][$countryId][$itemNumberId][$this->_common_oh])){
					$Amount[$poId][$countryId][$itemNumberId][$this->_common_oh]+=$common_oh_amount;
				}
				else{
					$Amount[$poId][$countryId][$itemNumberId][$this->_common_oh]=$common_oh_amount;
				}
				
				if(isset($Amount[$poId][$countryId][$itemNumberId][$this->_depr_amor_pre_cost])){
					$Amount[$poId][$countryId][$itemNumberId][$this->_depr_amor_pre_cost]+=$depr_amor_pre_cost_amount;
				}
				else{
					$Amount[$poId][$countryId][$itemNumberId][$this->_depr_amor_pre_cost]=$depr_amor_pre_cost_amount;
				}
				if(isset($Amount[$poId][$countryId][$itemNumberId][$this->_certificate_pre_cost])){
					$Amount[$poId][$countryId][$itemNumberId][$this->_certificate_pre_cost]+=$certificate_pre_cost_amount;
				}
				else{
					$Amount[$poId][$countryId][$itemNumberId][$this->_certificate_pre_cost]=$certificate_pre_cost_amount;
				}
			}
			elseif($level==$this->_By_OrderCountryAndGmtscolor){
				if(isset($Amount[$poId][$countryId][$colorId][$this->_lab_test])){
					$Amount[$poId][$countryId][$colorId][$this->_lab_test]+=$lab_test_amount;
				}
				else{
					$Amount[$poId][$countryId][$colorId][$this->_lab_test]=$lab_test_amount;
				}
				
				if(isset($Amount[$poId][$countryId][$colorId][$this->_inspection])){
					$Amount[$poId][$countryId][$colorId][$this->_inspection]+=$inspection_amount;
				}
				else{
					$Amount[$poId][$countryId][$colorId][$this->_inspection]=$inspection_amount;
				}
				
				if(isset($Amount[$poId][$countryId][$colorId][$this->_cm_cost])){
					$Amount[$poId][$countryId][$colorId][$this->_cm_cost]+=$cm_cost_amount;
				}
				else{
					$Amount[$poId][$countryId][$colorId][$this->_cm_cost]=$cm_cost_amount;
				}
				
				if(isset($Amount[$poId][$countryId][$colorId][$this->_freight])){
					$Amount[$poId][$countryId][$colorId][$this->_freight]+=$freight_amount;
				}
				else{
					$Amount[$poId][$countryId][$colorId][$this->_freight]=$freight_amount;
				}
				
				if(isset($Amount[$poId][$countryId][$colorId][$this->_currier_pre_cost])){
					$Amount[$poId][$countryId][$colorId][$this->_currier_pre_cost]+=$currier_pre_cost_amount;
				}
				else{
					$Amount[$poId][$countryId][$colorId][$this->_currier_pre_cost]=$currier_pre_cost_amount;
				}
				
				if(isset($Amount[$poId][$countryId][$colorId][$this->_common_oh])){
					$Amount[$poId][$countryId][$colorId][$this->_common_oh]+=$common_oh_amount;
				}
				else{
					$Amount[$poId][$countryId][$colorId][$this->_common_oh]=$common_oh_amount;
				}
				
				if(isset($Amount[$poId][$countryId][$colorId][$this->_depr_amor_pre_cost])){
					$Amount[$poId][$countryId][$colorId][$this->_depr_amor_pre_cost]+=$depr_amor_pre_cost_amount;
				}
				else{
					$Amount[$poId][$countryId][$colorId][$this->_depr_amor_pre_cost]=$depr_amor_pre_cost_amount;
				}
				if(isset($Amount[$poId][$countryId][$colorId][$this->_certificate_pre_cost])){
					$Amount[$poId][$countryId][$colorId][$this->_certificate_pre_cost]+=$certificate_pre_cost_amount;
				}
				else{
					$Amount[$poId][$countryId][$colorId][$this->_certificate_pre_cost]=$certificate_pre_cost_amount;
				}
			}
			
			elseif($level==$this->_By_OrderCountryAndGmtssize){
				if(isset($Amount[$poId][$countryId][$sizeId][$this->_lab_test])){
					$Amount[$poId][$countryId][$sizeId][$this->_lab_test]+=$lab_test_amount;
				}
				else{
					$Amount[$poId][$countryId][$sizeId][$this->_lab_test]=$lab_test_amount;
				}
				
				if(isset($Amount[$poId][$countryId][$sizeId][$this->_inspection])){
					$Amount[$poId][$countryId][$sizeId][$this->_inspection]+=$inspection_amount;
				}
				else{
					$Amount[$poId][$countryId][$sizeId][$this->_inspection]=$inspection_amount;
				}
				
				if(isset($Amount[$poId][$countryId][$sizeId][$this->_cm_cost])){
					$Amount[$poId][$countryId][$sizeId][$this->_cm_cost]+=$cm_cost_amount;
				}
				else{
					$Amount[$poId][$countryId][$sizeId][$this->_cm_cost]=$cm_cost_amount;
				}
				
				if(isset($Amount[$poId][$countryId][$sizeId][$this->_freight])){
					$Amount[$poId][$countryId][$sizeId][$this->_freight]+=$freight_amount;
				}
				else{
					$Amount[$poId][$countryId][$sizeId][$this->_freight]=$freight_amount;
				}
				
				if(isset($Amount[$poId][$countryId][$sizeId][$this->_currier_pre_cost])){
					$Amount[$poId][$countryId][$sizeId][$this->_currier_pre_cost]+=$currier_pre_cost_amount;
				}
				else{
					$Amount[$poId][$countryId][$sizeId][$this->_currier_pre_cost]=$currier_pre_cost_amount;
				}
				
				if(isset($Amount[$poId][$countryId][$sizeId][$this->_common_oh])){
					$Amount[$poId][$countryId][$sizeId][$this->_common_oh]+=$common_oh_amount;
				}
				else{
					$Amount[$poId][$countryId][$sizeId][$this->_common_oh]=$common_oh_amount;
				}
				
				if(isset($Amount[$poId][$countryId][$sizeId][$this->_depr_amor_pre_cost])){
					$Amount[$poId][$countryId][$sizeId][$this->_depr_amor_pre_cost]+=$depr_amor_pre_cost_amount;
				}
				else{
					$Amount[$poId][$countryId][$sizeId][$this->_depr_amor_pre_cost]=$depr_amor_pre_cost_amount;
				}
				if(isset($Amount[$poId][$countryId][$sizeId][$this->_certificate_pre_cost])){
					$Amount[$poId][$countryId][$sizeId][$this->_certificate_pre_cost]+=$certificate_pre_cost_amount;
				}
				else{
					$Amount[$poId][$countryId][$sizeId][$this->_certificate_pre_cost]=$certificate_pre_cost_amount;
				}
			}
			
			elseif($level==$this->_By_OrderGmtsitemAndGmtscolor){
				if(isset($Amount[$poId][$itemNumberId][$colorId][$this->_lab_test])){
					$Amount[$poId][$itemNumberId][$colorId][$this->_lab_test]+=$lab_test_amount;
				}
				else{
					$Amount[$poId][$itemNumberId][$colorId][$this->_lab_test]=$lab_test_amount;
				}
				
				if(isset($Amount[$poId][$itemNumberId][$colorId][$this->_inspection])){
					$Amount[$poId][$itemNumberId][$colorId][$this->_inspection]+=$inspection_amount;
				}
				else{
					$Amount[$poId][$itemNumberId][$colorId][$this->_inspection]=$inspection_amount;
				}
				
				if(isset($Amount[$poId][$itemNumberId][$colorId][$this->_cm_cost])){
					$Amount[$poId][$itemNumberId][$colorId][$this->_cm_cost]+=$cm_cost_amount;
				}
				else{
					$Amount[$poId][$itemNumberId][$colorId][$this->_cm_cost]=$cm_cost_amount;
				}
				
				if(isset($Amount[$poId][$itemNumberId][$colorId][$this->_freight])){
					$Amount[$poId][$itemNumberId][$colorId][$this->_freight]+=$freight_amount;
				}
				else{
					$Amount[$poId][$itemNumberId][$colorId][$this->_freight]=$freight_amount;
				}
				
				if(isset($Amount[$poId][$itemNumberId][$colorId][$this->_currier_pre_cost])){
					$Amount[$poId][$itemNumberId][$colorId][$this->_currier_pre_cost]+=$currier_pre_cost_amount;
				}
				else{
					$Amount[$poId][$itemNumberId][$colorId][$this->_currier_pre_cost]=$currier_pre_cost_amount;
				}
				
				if(isset($Amount[$poId][$itemNumberId][$colorId][$this->_common_oh])){
					$Amount[$poId][$itemNumberId][$colorId][$this->_common_oh]+=$common_oh_amount;
				}
				else{
					$Amount[$poId][$itemNumberId][$colorId][$this->_common_oh]=$common_oh_amount;
				}
				
				if(isset($Amount[$poId][$itemNumberId][$colorId][$this->_depr_amor_pre_cost])){
					$Amount[$poId][$itemNumberId][$colorId][$this->_depr_amor_pre_cost]+=$depr_amor_pre_cost_amount;
				}
				else{
					$Amount[$poId][$itemNumberId][$colorId][$this->_depr_amor_pre_cost]=$depr_amor_pre_cost_amount;
				}
				if(isset($Amount[$poId][$itemNumberId][$colorId][$this->_certificate_pre_cost])){
					$Amount[$poId][$itemNumberId][$colorId][$this->_certificate_pre_cost]+=$certificate_pre_cost_amount;
				}
				else{
					$Amount[$poId][$itemNumberId][$colorId][$this->_certificate_pre_cost]=$certificate_pre_cost_amount;
				}
			}
			
			elseif($level==$this->_By_OrderGmtsitemAndGmtssize){
				if(isset($Amount[$poId][$itemNumberId][$sizeId][$this->_lab_test])){
					$Amount[$poId][$itemNumberId][$sizeId][$this->_lab_test]+=$lab_test_amount;
				}
				else{
					$Amount[$poId][$itemNumberId][$sizeId][$this->_lab_test]=$lab_test_amount;
				}
				
				if(isset($Amount[$poId][$itemNumberId][$sizeId][$this->_inspection])){
					$Amount[$poId][$itemNumberId][$sizeId][$this->_inspection]+=$inspection_amount;
				}
				else{
					$Amount[$poId][$itemNumberId][$sizeId][$this->_inspection]=$inspection_amount;
				}
				
				if(isset($Amount[$poId][$itemNumberId][$sizeId][$this->_cm_cost])){
					$Amount[$poId][$itemNumberId][$sizeId][$this->_cm_cost]+=$cm_cost_amount;
				}
				else{
					$Amount[$poId][$itemNumberId][$sizeId][$this->_cm_cost]=$cm_cost_amount;
				}
				
				if(isset($Amount[$poId][$itemNumberId][$sizeId][$this->_freight])){
					$Amount[$poId][$itemNumberId][$sizeId][$this->_freight]+=$freight_amount;
				}
				else{
					$Amount[$poId][$itemNumberId][$sizeId][$this->_freight]=$freight_amount;
				}
				
				if(isset($Amount[$poId][$itemNumberId][$sizeId][$this->_currier_pre_cost])){
					$Amount[$poId][$itemNumberId][$sizeId][$this->_currier_pre_cost]+=$currier_pre_cost_amount;
				}
				else{
					$Amount[$poId][$itemNumberId][$sizeId][$this->_currier_pre_cost]=$currier_pre_cost_amount;
				}
				
				if(isset($Amount[$poId][$itemNumberId][$sizeId][$this->_common_oh])){
					$Amount[$poId][$itemNumberId][$sizeId][$this->_common_oh]+=$common_oh_amount;
				}
				else{
					$Amount[$poId][$itemNumberId][$sizeId][$this->_common_oh]=$common_oh_amount;
				}
				
				if(isset($Amount[$poId][$itemNumberId][$sizeId][$this->_depr_amor_pre_cost])){
					$Amount[$poId][$itemNumberId][$sizeId][$this->_depr_amor_pre_cost]+=$depr_amor_pre_cost_amount;
				}
				else{
					$Amount[$poId][$itemNumberId][$sizeId][$this->_depr_amor_pre_cost]=$depr_amor_pre_cost_amount;
				}
				if(isset($Amount[$poId][$itemNumberId][$sizeId][$this->_certificate_pre_cost])){
					$Amount[$poId][$itemNumberId][$sizeId][$this->_certificate_pre_cost]+=$certificate_pre_cost_amount;
				}
				else{
					$Amount[$poId][$itemNumberId][$sizeId][$this->_certificate_pre_cost]=$certificate_pre_cost_amount;
				}
			}
			
			elseif($level==$this->_By_OrderGmtscolorAndGmtssize){
				if(isset($Amount[$poId][$colorId][$sizeId][$this->_lab_test])){
					$Amount[$poId][$colorId][$sizeId][$this->_lab_test]+=$lab_test_amount;
				}
				else{
					$Amount[$poId][$colorId][$sizeId][$this->_lab_test]=$lab_test_amount;
				}
				
				if(isset($Amount[$poId][$colorId][$sizeId][$this->_inspection])){
					$Amount[$poId][$colorId][$sizeId][$this->_inspection]+=$inspection_amount;
				}
				else{
					$Amount[$poId][$colorId][$sizeId][$this->_inspection]=$inspection_amount;
				}
				
				if(isset($Amount[$poId][$colorId][$sizeId][$this->_cm_cost])){
					$Amount[$poId][$colorId][$sizeId][$this->_cm_cost]+=$cm_cost_amount;
				}
				else{
					$Amount[$poId][$colorId][$sizeId][$this->_cm_cost]=$cm_cost_amount;
				}
				
				if(isset($Amount[$poId][$colorId][$sizeId][$this->_freight])){
					$Amount[$poId][$colorId][$sizeId][$this->_freight]+=$freight_amount;
				}
				else{
					$Amount[$poId][$colorId][$sizeId][$this->_freight]=$freight_amount;
				}
				
				if(isset($Amount[$poId][$colorId][$sizeId][$this->_currier_pre_cost])){
					$Amount[$poId][$colorId][$sizeId][$this->_currier_pre_cost]+=$currier_pre_cost_amount;
				}
				else{
					$Amount[$poId][$colorId][$sizeId][$this->_currier_pre_cost]=$currier_pre_cost_amount;
				}
				
				if(isset($Amount[$poId][$colorId][$sizeId][$this->_common_oh])){
					$Amount[$poId][$colorId][$sizeId][$this->_common_oh]+=$common_oh_amount;
				}
				else{
					$Amount[$poId][$colorId][$sizeId][$this->_common_oh]=$common_oh_amount;
				}
				
				if(isset($Amount[$poId][$colorId][$sizeId][$this->_depr_amor_pre_cost])){
					$Amount[$poId][$colorId][$sizeId][$this->_depr_amor_pre_cost]+=$depr_amor_pre_cost_amount;
				}
				else{
					$Amount[$poId][$colorId][$sizeId][$this->_depr_amor_pre_cost]=$depr_amor_pre_cost_amount;
				}
				if(isset($Amount[$poId][$colorId][$sizeId][$this->_certificate_pre_cost])){
					$Amount[$poId][$colorId][$sizeId][$this->_certificate_pre_cost]+=$certificate_pre_cost_amount;
				}
				else{
					$Amount[$poId][$colorId][$sizeId][$this->_certificate_pre_cost]=$certificate_pre_cost_amount;
				}
			}
			elseif($level==$this->_By_orderCountryGmtsitemAndGmtscolor){
				if(isset($Amount[$poId][$countryId][$itemNumberId][$colorId][$this->_lab_test])){
					$Amount[$poId][$countryId][$itemNumberId][$colorId][$this->_lab_test]+=$lab_test_amount;
				}
				else{
					$Amount[$poId][$countryId][$itemNumberId][$colorId][$this->_lab_test]=$lab_test_amount;
				}
				
				if(isset($Amount[$poId][$countryId][$itemNumberId][$colorId][$this->_inspection])){
					$Amount[$poId][$countryId][$itemNumberId][$colorId][$this->_inspection]+=$inspection_amount;
				}
				else{
					$Amount[$poId][$countryId][$itemNumberId][$colorId][$this->_inspection]=$inspection_amount;
				}
				
				if(isset($Amount[$poId][$countryId][$itemNumberId][$colorId][$this->_cm_cost])){
					$Amount[$poId][$countryId][$itemNumberId][$colorId][$this->_cm_cost]+=$cm_cost_amount;
				}
				else{
					$Amount[$poId][$countryId][$itemNumberId][$colorId][$this->_cm_cost]=$cm_cost_amount;
				}
				
				if(isset($Amount[$poId][$countryId][$itemNumberId][$colorId][$this->_freight])){
					$Amount[$poId][$countryId][$itemNumberId][$colorId][$this->_freight]+=$freight_amount;
				}
				else{
					$Amount[$poId][$countryId][$itemNumberId][$colorId][$this->_freight]=$freight_amount;
				}
				
				if(isset($Amount[$poId][$countryId][$itemNumberId][$colorId][$this->_currier_pre_cost])){
					$Amount[$poId][$countryId][$itemNumberId][$colorId][$this->_currier_pre_cost]+=$currier_pre_cost_amount;
				}
				else{
					$Amount[$poId][$countryId][$itemNumberId][$colorId][$this->_currier_pre_cost]=$currier_pre_cost_amount;
				}
				
				if(isset($Amount[$poId][$countryId][$itemNumberId][$colorId][$this->_common_oh])){
					$Amount[$poId][$countryId][$itemNumberId][$colorId][$this->_common_oh]+=$common_oh_amount;
				}
				else{
					$Amount[$poId][$countryId][$itemNumberId][$colorId][$this->_common_oh]=$common_oh_amount;
				}
				
				if(isset($Amount[$poId][$countryId][$itemNumberId][$colorId][$this->_depr_amor_pre_cost])){
					$Amount[$poId][$countryId][$itemNumberId][$colorId][$this->_depr_amor_pre_cost]+=$depr_amor_pre_cost_amount;
				}
				else{
					$Amount[$poId][$countryId][$itemNumberId][$colorId][$this->_depr_amor_pre_cost]=$depr_amor_pre_cost_amount;
				}
				if(isset($Amount[$poId][$countryId][$itemNumberId][$colorId][$this->_certificate_pre_cost])){
					$Amount[$poId][$countryId][$itemNumberId][$colorId][$this->_certificate_pre_cost]+=$certificate_pre_cost_amount;
				}
				else{
					$Amount[$poId][$countryId][$itemNumberId][$colorId][$this->_certificate_pre_cost]=$certificate_pre_cost_amount;
				}
			}
			elseif($level==$this->_By_orderCountryGmtsitemAndGmtssize){
				if(isset($Amount[$poId][$countryId][$itemNumberId][$sizeId][$this->_lab_test])){
					$Amount[$poId][$countryId][$itemNumberId][$sizeId][$this->_lab_test]+=$lab_test_amount;
				}
				else{
					$Amount[$poId][$countryId][$itemNumberId][$sizeId][$this->_lab_test]=$lab_test_amount;
				}
				
				if(isset($Amount[$poId][$countryId][$itemNumberId][$sizeId][$this->_inspection])){
					$Amount[$poId][$countryId][$itemNumberId][$sizeId][$this->_inspection]+=$inspection_amount;
				}
				else{
					$Amount[$poId][$countryId][$itemNumberId][$sizeId][$this->_inspection]=$inspection_amount;
				}
				
				if(isset($Amount[$poId][$countryId][$itemNumberId][$sizeId][$this->_cm_cost])){
					$Amount[$poId][$countryId][$itemNumberId][$sizeId][$this->_cm_cost]+=$cm_cost_amount;
				}
				else{
					$Amount[$poId][$countryId][$itemNumberId][$sizeId][$this->_cm_cost]=$cm_cost_amount;
				}
				
				if(isset($Amount[$poId][$countryId][$itemNumberId][$sizeId][$this->_freight])){
					$Amount[$poId][$countryId][$itemNumberId][$sizeId][$this->_freight]+=$freight_amount;
				}
				else{
					$Amount[$poId][$countryId][$itemNumberId][$sizeId][$this->_freight]=$freight_amount;
				}
				
				if(isset($Amount[$poId][$countryId][$itemNumberId][$sizeId][$this->_currier_pre_cost])){
					$Amount[$poId][$countryId][$itemNumberId][$sizeId][$this->_currier_pre_cost]+=$currier_pre_cost_amount;
				}
				else{
					$Amount[$poId][$countryId][$itemNumberId][$sizeId][$this->_currier_pre_cost]=$currier_pre_cost_amount;
				}
				
				if(isset($Amount[$poId][$countryId][$itemNumberId][$sizeId][$this->_common_oh])){
					$Amount[$poId][$countryId][$itemNumberId][$sizeId][$this->_common_oh]+=$common_oh_amount;
				}
				else{
					$Amount[$poId][$countryId][$itemNumberId][$sizeId][$this->_common_oh]=$common_oh_amount;
				}
				
				if(isset($Amount[$poId][$countryId][$itemNumberId][$sizeId][$this->_depr_amor_pre_cost])){
					$Amount[$poId][$countryId][$itemNumberId][$sizeId][$this->_depr_amor_pre_cost]+=$depr_amor_pre_cost_amount;
				}
				else{
					$Amount[$poId][$countryId][$itemNumberId][$sizeId][$this->_depr_amor_pre_cost]=$depr_amor_pre_cost_amount;
				}
				if(isset($Amount[$poId][$countryId][$itemNumberId][$sizeId][$this->_certificate_pre_cost])){
					$Amount[$poId][$countryId][$itemNumberId][$sizeId][$this->_certificate_pre_cost]+=$certificate_pre_cost_amount;
				}
				else{
					$Amount[$poId][$countryId][$itemNumberId][$sizeId][$this->_certificate_pre_cost]=$certificate_pre_cost_amount;
				}
			}
			elseif($level==$this->_By_orderCountryGmtscolorAndGmtssize){
				if(isset($Amount[$poId][$countryId][$colorId][$sizeId][$this->_lab_test])){
					$Amount[$poId][$countryId][$colorId][$sizeId][$this->_lab_test]+=$lab_test_amount;
				}
				else{
					$Amount[$poId][$countryId][$colorId][$sizeId][$this->_lab_test]=$lab_test_amount;
				}
				
				if(isset($Amount[$poId][$countryId][$colorId][$sizeId][$this->_inspection])){
					$Amount[$poId][$countryId][$colorId][$sizeId][$this->_inspection]+=$inspection_amount;
				}
				else{
					$Amount[$poId][$countryId][$colorId][$sizeId][$this->_inspection]=$inspection_amount;
				}
				
				if(isset($Amount[$poId][$countryId][$colorId][$sizeId][$this->_cm_cost])){
					$Amount[$poId][$countryId][$colorId][$sizeId][$this->_cm_cost]+=$cm_cost_amount;
				}
				else{
					$Amount[$poId][$countryId][$colorId][$sizeId][$this->_cm_cost]=$cm_cost_amount;
				}
				
				if(isset($Amount[$poId][$countryId][$colorId][$sizeId][$this->_freight])){
					$Amount[$poId][$countryId][$colorId][$sizeId][$this->_freight]+=$freight_amount;
				}
				else{
					$Amount[$poId][$countryId][$colorId][$sizeId][$this->_freight]=$freight_amount;
				}
				
				if(isset($Amount[$poId][$countryId][$colorId][$sizeId][$this->_currier_pre_cost])){
					$Amount[$poId][$countryId][$colorId][$sizeId][$this->_currier_pre_cost]+=$currier_pre_cost_amount;
				}
				else{
					$Amount[$poId][$countryId][$colorId][$sizeId][$this->_currier_pre_cost]=$currier_pre_cost_amount;
				}
				
				if(isset($Amount[$poId][$countryId][$colorId][$sizeId][$this->_common_oh])){
					$Amount[$poId][$countryId][$colorId][$sizeId][$this->_common_oh]+=$common_oh_amount;
				}
				else{
					$Amount[$poId][$countryId][$colorId][$sizeId][$this->_common_oh]=$common_oh_amount;
				}
				
				if(isset($Amount[$poId][$countryId][$colorId][$sizeId][$this->_depr_amor_pre_cost])){
					$Amount[$poId][$countryId][$colorId][$sizeId][$this->_depr_amor_pre_cost]+=$depr_amor_pre_cost_amount;
				}
				else{
					$Amount[$poId][$countryId][$colorId][$sizeId][$this->_depr_amor_pre_cost]=$depr_amor_pre_cost_amount;
				}
				if(isset($Amount[$poId][$countryId][$colorId][$sizeId][$this->_certificate_pre_cost])){
					$Amount[$poId][$countryId][$colorId][$sizeId][$this->_certificate_pre_cost]+=$certificate_pre_cost_amount;
				}
				else{
					$Amount[$poId][$countryId][$colorId][$sizeId][$this->_certificate_pre_cost]=$certificate_pre_cost_amount;
				}
			}
			
			
			elseif($level==$this->_By_orderGmtsitemGmtscolorAndGmtssize){
				if(isset($Amount[$poId][$itemNumberId][$colorId][$sizeId][$this->_lab_test])){
					$Amount[$poId][$itemNumberId][$colorId][$sizeId][$this->_lab_test]+=$lab_test_amount;
				}
				else{
					$Amount[$poId][$itemNumberId][$colorId][$sizeId][$this->_lab_test]=$lab_test_amount;
				}
				
				if(isset($Amount[$poId][$itemNumberId][$colorId][$sizeId][$this->_inspection])){
					$Amount[$poId][$itemNumberId][$colorId][$sizeId][$this->_inspection]+=$inspection_amount;
				}
				else{
					$Amount[$poId][$itemNumberId][$colorId][$sizeId][$this->_inspection]=$inspection_amount;
				}
				
				if(isset($Amount[$poId][$itemNumberId][$colorId][$sizeId][$this->_cm_cost])){
					$Amount[$poId][$itemNumberId][$colorId][$sizeId][$this->_cm_cost]+=$cm_cost_amount;
				}
				else{
					$Amount[$poId][$itemNumberId][$colorId][$sizeId][$this->_cm_cost]=$cm_cost_amount;
				}
				
				if(isset($Amount[$poId][$itemNumberId][$colorId][$sizeId][$this->_freight])){
					$Amount[$poId][$itemNumberId][$colorId][$sizeId][$this->_freight]+=$freight_amount;
				}
				else{
					$Amount[$poId][$itemNumberId][$colorId][$sizeId][$this->_freight]=$freight_amount;
				}
				
				if(isset($Amount[$poId][$itemNumberId][$colorId][$sizeId][$this->_currier_pre_cost])){
					$Amount[$poId][$itemNumberId][$colorId][$sizeId][$this->_currier_pre_cost]+=$currier_pre_cost_amount;
				}
				else{
					$Amount[$poId][$itemNumberId][$colorId][$sizeId][$this->_currier_pre_cost]=$currier_pre_cost_amount;
				}
				
				if(isset($Amount[$poId][$itemNumberId][$colorId][$sizeId][$this->_common_oh])){
					$Amount[$poId][$itemNumberId][$colorId][$sizeId][$this->_common_oh]+=$common_oh_amount;
				}
				else{
					$Amount[$poId][$itemNumberId][$colorId][$sizeId][$this->_common_oh]=$common_oh_amount;
				}
				
				if(isset($Amount[$poId][$itemNumberId][$colorId][$sizeId][$this->_depr_amor_pre_cost])){
					$Amount[$poId][$itemNumberId][$colorId][$sizeId][$this->_depr_amor_pre_cost]+=$depr_amor_pre_cost_amount;
				}
				else{
					$Amount[$poId][$itemNumberId][$colorId][$sizeId][$this->_depr_amor_pre_cost]=$depr_amor_pre_cost_amount;
				}
				if(isset($Amount[$poId][$itemNumberId][$colorId][$sizeId][$this->_certificate_pre_cost])){
					$Amount[$poId][$itemNumberId][$colorId][$sizeId][$this->_certificate_pre_cost]+=$certificate_pre_cost_amount;
				}
				else{
					$Amount[$poId][$itemNumberId][$colorId][$sizeId][$this->_certificate_pre_cost]=$certificate_pre_cost_amount;
				}
			}
			elseif($level==$this->_By_orderCountryGmtsitemGmtscolorAndGmtssize){
				if(isset($Amount[$poId][$countryId][$itemNumberId][$colorId][$sizeId][$this->_lab_test])){
					$Amount[$poId][$countryId][$itemNumberId][$colorId][$sizeId][$this->_lab_test]+=$lab_test_amount;
				}
				else{
					$Amount[$poId][$countryId][$itemNumberId][$colorId][$sizeId][$this->_lab_test]=$lab_test_amount;
				}
				
				if(isset($Amount[$poId][$countryId][$itemNumberId][$colorId][$sizeId][$this->_inspection])){
					$Amount[$poId][$countryId][$itemNumberId][$colorId][$sizeId][$this->_inspection]+=$inspection_amount;
				}
				else{
					$Amount[$poId][$countryId][$itemNumberId][$colorId][$sizeId][$this->_inspection]=$inspection_amount;
				}
				
				if(isset($Amount[$poId][$countryId][$itemNumberId][$colorId][$sizeId][$this->_cm_cost])){
					$Amount[$poId][$countryId][$itemNumberId][$colorId][$sizeId][$this->_cm_cost]+=$cm_cost_amount;
				}
				else{
					$Amount[$poId][$countryId][$itemNumberId][$colorId][$sizeId][$this->_cm_cost]=$cm_cost_amount;
				}
				
				if(isset($Amount[$poId][$countryId][$itemNumberId][$colorId][$sizeId][$this->_freight])){
					$Amount[$poId][$countryId][$itemNumberId][$colorId][$sizeId][$this->_freight]+=$freight_amount;
				}
				else{
					$Amount[$poId][$countryId][$itemNumberId][$colorId][$sizeId][$this->_freight]=$freight_amount;
				}
				
				if(isset($Amount[$poId][$countryId][$itemNumberId][$colorId][$sizeId][$this->_currier_pre_cost])){
					$Amount[$poId][$countryId][$itemNumberId][$colorId][$sizeId][$this->_currier_pre_cost]+=$currier_pre_cost_amount;
				}
				else{
					$Amount[$poId][$countryId][$itemNumberId][$colorId][$sizeId][$this->_currier_pre_cost]=$currier_pre_cost_amount;
				}
				
				if(isset($Amount[$poId][$countryId][$itemNumberId][$colorId][$sizeId][$this->_common_oh])){
					$Amount[$poId][$countryId][$itemNumberId][$colorId][$sizeId][$this->_common_oh]+=$common_oh_amount;
				}
				else{
					$Amount[$poId][$countryId][$itemNumberId][$colorId][$sizeId][$this->_common_oh]=$common_oh_amount;
				}
				
				if(isset($Amount[$poId][$countryId][$itemNumberId][$colorId][$sizeId][$this->_depr_amor_pre_cost])){
					$Amount[$poId][$countryId][$itemNumberId][$colorId][$sizeId][$this->_depr_amor_pre_cost]+=$depr_amor_pre_cost_amount;
				}
				else{
					$Amount[$poId][$countryId][$itemNumberId][$colorId][$sizeId][$this->_depr_amor_pre_cost]=$depr_amor_pre_cost_amount;
				}
				if(isset($Amount[$poId][$countryId][$itemNumberId][$colorId][$sizeId][$this->_certificate_pre_cost])){
					$Amount[$poId][$countryId][$itemNumberId][$colorId][$sizeId][$this->_certificate_pre_cost]+=$certificate_pre_cost_amount;
				}
				else{
					$Amount[$poId][$countryId][$itemNumberId][$colorId][$sizeId][$this->_certificate_pre_cost]=$certificate_pre_cost_amount;
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
	public function getAmountArray_by_job(){
		$Amount=$this->_setAmount($this->_By_Job);
		return $Amount;
	}
	
	// Order wise
	
	//Amount
	public function getAmountArray_by_order(){
		$Amount=$this->_setAmount($this->_By_Order);
		return $Amount;
	}
	
	// Order and Country wise
	//Amount
	public function getAmountArray_by_orderAndCountry(){
		$Amount=$this->_setAmount($this->_By_OrderAndCountry);
		return $Amount;
	}
	
	// Order and Gmts Item wise
	//Amount
	public function getAmountArray_by_orderAndGmtsitem(){
		$Amount=$this->_setAmount($this->_By_OrderAndGmtsitem);
		return $Amount;
	}
	
	// Order and Gmts Color wise
	//Amount
	public function getAmountArray_by_orderAndGmtscolor(){
		$Amount=$this->_setAmount($this->_By_OrderAndGmtscolor);
		return $Amount;
	}
	
	// Order and Gmts Size wise
	//Amount
	public function getAmountArray_by_orderAndGmtssize(){
		$Amount=$this->_setAmount($this->_By_OrderAndGmtssize);
		return $Amount;
	}
	
	
	
	// Order,Country and Gmts Item wise
	//Amount
	public function getAmountArray_by_orderCountryAndGmtsitem(){
		$Amount=$this->_setAmount($this->_By_orderCountryAndGmtsitem);
		return $Amount;
	}
	
	// Order and Country And Color Wise
	//Amount
	public function getAmountArray_by_orderCountryAndGmtscolor(){
		$Amount=$this->_setAmount($this->_By_OrderCountryAndGmtscolor);
		return $Amount;
	}
	
	// Order and Country And Size Wise
	//Amount
	public function getAmountArray_by_orderCountryAndGmtssize(){
		$Amount=$this->_setAmount($this->_By_OrderCountryAndGmtssize);
		return $Amount;
	}
	
	// Order and Gmts Item And Color Wise
	//Amount
	public function getAmountArray_by_orderGmtsitemAndGmtscolor(){
		$Amount=$this->_setAmount($this->_By_OrderGmtsitemAndGmtscolor);
		return $Amount;
	}
	
	// Order and Gmts Item And Size Wise=============================================================================
	//Amount
	public function getAmountArray_by_orderGmtsitemAndGmtssize(){
		$Amount=$this->_setAmount($this->_By_OrderGmtsitemAndGmtssize);
		return $Amount;
	}
	
	// Order and Gmts Color And Size Wise=============================================================================
	//Amount
	public function getAmountArray_by_orderGmtscolorAndGmtssize(){
		$Amount=$this->_setAmount($this->_By_OrderGmtscolorAndGmtssize);
		return $Amount;
	}
	
	// Order,Country,Gmts Item  and Gmts Color wise
	//Amount
	public function getAmountArray_by_orderCountryGmtsitemAndGmtscolor(){
		$Amount=$this->_setAmount($this->_By_orderCountryGmtsitemAndGmtscolor);
		return $Amount;
	}
	
	// Order,Country,Gmts Item  and Gmts Size wise
	//Amount
	public function getAmountArray_by_orderCountryGmtsitemAndGmtssize(){
		$Amount=$this->_setAmount($this->_By_orderCountryGmtsitemAndGmtssize);
		return $Amount;
	}
	
	// Order,Country,Gmts Color  and Gmts Size wise
	//Amount
	public function getAmountArray_by_orderCountryGmtscolorAndGmtssize(){
		$Amount=$this->_setAmount($this->_By_orderCountryGmtscolorAndGmtssize);
		return $Amount;
	}
	
	// Order,Gmts item,Gmts Color  and Gmts Size wise
	//Amount
	public function getAmountArray_by_orderGmtsitemGmtscolorAndGmtssize(){
		$Amount=$this->_setAmount($this->_By_orderGmtsitemGmtscolorAndGmtssize);
		return $Amount;
	}
	
	// Order,Country,Gmts Item, Gmts Color and Gmts size wise
	//Amount
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