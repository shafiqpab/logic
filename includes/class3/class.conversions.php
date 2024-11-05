<?
class conversion extends report{
	private $_By_JobAndConversionid='By_JobAndConversionid';
	private $_By_JobAndFabric='By_JobAndFabric';
	private $_By_JobAndProcess='By_JobAndProcess';
	private $_By_JobFabricAndProcess='By_JobFabricAndProcess';
	
	private $_By_OrderAndConversionid='By_OrderAndConversionid';
	private $_By_OrderAndFabric='By_OrderAndFabric';
	private $_By_OrderAndProcess='By_OrderAndProcess';
	private $_By_OrderFabricAndProcess='By_OrderFabricAndProcess';
	private $_By_OrderFabricProcessAndDiaWidth='By_OrderFabricProcessAndDiaWidth';
	
	private $_By_OrderCountryAndConversionid='By_OrderCountryAndConversionid';
	private $_By_OrderCountryAndFabric='By_OrderCountryAndFabric';
	private $_By_OrderCountryAndProcess='By_OrderCountryAndProcess';
	private $_By_OrderCountryFabricAndProcess='By_OrderCountryFabricAndProcess';
	
	private $_query="";
	private $_query2="";
	private $_query3="";
	private $_query4="";
	
	private $_dataArray=array();
	private $_dataArray2=array();
	private $_dataArray3=array();
	private $_dataArray4=array();
	
	private $_rateArray=array();
	private $_cotrastColorArray=array();
	private $_stripeColorArray=array();
	private $_stripeColorReqArray=array();
	// class constructor
	function __construct(condition $condition){
		parent::__construct($condition);
		$this->_setQuery();
		$this->_setData();
		$this->_setRateArray();
		$this->_setCotrastColorArray();
		$this->_setStripeColorArray();
	}// end class constructor
	
	private function _setQuery(){
		//$jobcond=$this->_setJobsString($this->_jobs,'a.job_no');
		///$pocond=$this->_setPoIdsString($this->_poIds, 'b.id');
		$this->_query='select a.job_no AS "job_no",b.id AS "id",c.item_number_id AS "item_number_id",c.country_id AS "country_id",c.color_number_id AS "color_number_id",c.size_number_id AS "size_number_id",c.order_quantity AS "order_quantity",c.plan_cut_qnty AS "plan_cut_qnty",d.id AS "pre_cost_dtls_id",d.fab_nature_id AS "fab_nature_id",d.color_size_sensitive AS "color_size_sensitive",d.color_type_id AS "color_type_id",e.dia_width AS "dia_width", e.cons AS "cons",e.requirment AS "requirment",f.id AS "convertion_id",f.fabric_description AS "fabric_description",f.cons_process AS "cons_process",f.req_qnty AS "req_qnty",f.process_loss AS "process_loss",f.avg_req_qnty AS "avg_req_qnty",f.charge_unit AS "charge_unit",f.amount "amount" ,f.color_break_down AS "color_break_down"   from wo_po_details_master a, wo_po_break_down b,wo_po_color_size_breakdown c,wo_pre_cost_fabric_cost_dtls d,wo_pre_cos_fab_co_avg_con_dtls e,wo_pre_cost_fab_conv_cost_dtls f  where 1=1 '.$this->cond.' and a.id=b.job_id and a.id=c.job_id and a.id=d.job_id and a.id=e.job_id and a.id=f.job_id and b.id=c.po_break_down_id and d.id=e.pre_cost_fabric_cost_dtls_id and c.po_break_down_id=e.po_break_down_id and c.item_number_id= d.item_number_id and c.color_number_id=e.color_number_id and c.size_number_id=e.gmts_sizes and d.id=f.fabric_description  and e.cons !=0   and a.is_deleted=0 and a.status_active=1 and c.is_deleted=0 and c.status_active=1 and f.is_deleted=0 and f.status_active=1  
UNION ALL
select a.job_no AS "job_no",b.id AS "id",c.item_number_id AS "item_number_id",c.country_id AS "country_id",c.color_number_id AS "color_number_id",c.size_number_id AS "size_number_id",c.order_quantity AS "order_quantity",c.plan_cut_qnty AS "plan_cut_qnty",d.id AS "pre_cost_dtls_id",d.fab_nature_id AS "fab_nature_id",d.color_size_sensitive AS "color_size_sensitive",d.color_type_id AS "color_type_id",e.dia_width AS "dia_width",e.cons AS "cons",e.requirment AS "requirment",f.id AS "convertion_id",f.fabric_description AS "fabric_description",f.cons_process AS "cons_process",f.req_qnty AS "req_qnty",f.process_loss AS "process_loss",f.avg_req_qnty AS "avg_req_qnty",f.charge_unit AS "charge_unit",f.amount "amount" ,f.color_break_down AS "color_break_down"   from wo_po_details_master a, wo_po_break_down b,wo_po_color_size_breakdown c,wo_pre_cost_fabric_cost_dtls d,wo_pre_cos_fab_co_avg_con_dtls e,wo_pre_cost_fab_conv_cost_dtls f  where 1=1 '.$this->cond.' and a.id=b.job_id and a.id=c.job_id and a.id=d.job_id and a.id=e.job_id and a.id=f.job_id and b.id=c.po_break_down_id and d.id=e.pre_cost_fabric_cost_dtls_id and c.po_break_down_id=e.po_break_down_id and c.item_number_id= d.item_number_id and c.color_number_id=e.color_number_id and c.size_number_id=e.gmts_sizes and f.fabric_description=0  and e.cons !=0  and a.is_deleted=0 and a.status_active=1 and c.is_deleted=0 and c.status_active=1 and f.is_deleted=0 and f.status_active=1 ';// and b.is_deleted=0 and b.status_active=1

		//$this->_query2='select f.id AS "convertion_id", f.color_break_down AS "color_break_down" from wo_po_details_master a,wo_pre_cost_fab_conv_cost_dtls f where 1=1 '.$this->jobtablecond.' and a.id=f.job_id';
		$this->_query2='select f.id AS "convertion_id", f.color_break_down AS "color_break_down", d.color_size_sensitive as  "color_size_sensitive" from wo_po_details_master a,wo_pre_cost_fab_conv_cost_dtls f,wo_pre_cost_fabric_cost_dtls d where 1=1 '.$this->jobtablecond.' and d.id=f.fabric_description and a.job_no=f.job_no and d.job_no=a.job_no and f.is_deleted=0 and f.status_active=1 and d.is_deleted=0 and d.status_active=1';
		
		$this->_query3='select g.pre_cost_fabric_cost_dtls_id AS "pre_cost_fabric_cost_dtls_id", g.gmts_color_id AS "gmts_color_id", g.contrast_color_id AS "contrast_color_id" from wo_po_details_master a,wo_pre_cos_fab_co_color_dtls g where 1=1 '.$this->jobtablecond.' and a.id=g.job_id and g.is_deleted=0 and g.status_active=1';
		$this->_query4='select h.pre_cost_fabric_cost_dtls_id AS "pre_cost_fabric_cost_dtls_id", h.color_number_id AS "gmts_color_id", h.stripe_color AS "stripe_color", h.fabreq AS "fabreq" from wo_po_details_master a,wo_pre_stripe_color h where yarn_dyed=1 '.$this->jobtablecond.' and a.id=h.job_id and h.is_deleted=0 and h.status_active=1';

	}
	
	public function getQuery(){
		return $this->_query;
	}
	
	private function _setData() {
		$this->_dataArray=$this->condition->sql_select($this->_query);
		$this->_dataArray2=$this->condition->sql_select($this->_query2);
		$this->_dataArray3=$this->condition->sql_select($this->_query3);
		$this->_dataArray4=$this->condition->sql_select($this->_query4);
		return $this;
	}
	
	public function getData() {
		return $this->_dataArray;
	}
	
	public function _setCotrastColorArray(){
		//$row=oci_fetch_assoc($this->_dataArray3)
		while($row=oci_fetch_assoc($this->_dataArray3)){
			$pre_cost_fabric_cost_dtls_id=$row['pre_cost_fabric_cost_dtls_id'];
			$gmts_color_id=$row['gmts_color_id'];
			$contrast_color_id=$row['contrast_color_id'];
			$this->_cotrastColorArray[$pre_cost_fabric_cost_dtls_id][$gmts_color_id]=$contrast_color_id;
		}
	}
	
	public function _setStripeColorArray(){
		while($row=oci_fetch_assoc($this->_dataArray4)){
			$pre_cost_fabric_cost_dtls_id=$row['pre_cost_fabric_cost_dtls_id'];
			$gmts_color_id=$row['gmts_color_id'];
			$stripe_color=$row['stripe_color'];
			$fabreq=$row['fabreq'];
			$this->_stripeColorArray[$pre_cost_fabric_cost_dtls_id][$gmts_color_id][$stripe_color]=$stripe_color;
			$this->_stripeColorReqArray[$pre_cost_fabric_cost_dtls_id][$gmts_color_id][$stripe_color]+=$fabreq;
		}
	}
	
	public function _setRateArray(){
		while($row=oci_fetch_assoc($this->_dataArray2)){
			$id=$row['convertion_id'];
			$colorBreakDown=$row['color_break_down'];
			$color_size_sensitive=$row['color_size_sensitive'];
			if($colorBreakDown !="")
			{
				$arr_1=explode("__",$colorBreakDown);
				for($ci=0;$ci<count($arr_1);$ci++)
				{
				$arr_2=explode("_",$arr_1[$ci]);
				if($color_size_sensitive==3){
					//$rateColorId=$this->_cotrastColorArray[$preCostDtlsId][$colorId];
					$this->_rateArray[$id][$arr_2[0]][$arr_2[3]]=$arr_2[1];
					}
					else{
						//echo $arr_2[0].'=T='.$arr_2[1].'<br>';
					 $this->_rateArray[$id][$arr_2[0]][$arr_2[3]]=$arr_2[1];
					}
					
				//$this->_rateArray[$id][$arr_2[0]]=$arr_2[1];
				//$this->_rateArray[$id][$arr_2[3]]=$arr_2[1];
				}
			}
		}
	}
	
	public function getRateArray(){
			return $this->_stripeColorArray;
	}
	private function _calculateQty($plan_cut_qnty,$costingPerQty,$set_item_ratio,$cons_qnty){
	  //return $reqyarnqnty =($plan_cut_qnty/($costingPerQty*$set_item_ratio))*$cons_qnty;
	  return $reqyarnqnty =($plan_cut_qnty/$set_item_ratio)*($cons_qnty/$costingPerQty);
	 // echo $plan_cut_qnty.'='.$set_item_ratio.'='.$cons_qnty.'='.$costingPerQty;die;
	}
	private function _calculateAmount($reqyarnqnty,$rate){
	 return $amount=$reqyarnqnty*$rate;
	}
	
	public function _setQty($level){
		$jobNo='';
		$poId='';
		$itemNumberId='';
		$countryId='';
		$colorId='';
		$sizeId='';
		$planPutQnty='';
		$fabNatureId='';
		$diaWidth='';
		$cons='';
		$requirment='';
		$convertionId='';
		$fabricId='';
		$consProcessId='';
		$req_qnty='';
		$process_loss='';
		$avg_req_qnty='';
		$charge_unit='';
		//$gmtsitemRatioArray=$this->_gmtsitemRatioArray;
		//$costingPerArray=$this->_costingPerQtyArr;
		$Qty=array();
		//foreach($this->_dataArray as $row)
		while($row=oci_fetch_assoc($this->_dataArray))
		{
			/*$jobNo=$row[csf('job_no')];
			$poId=$row[csf('id')];
			$itemNumberId=$row[csf('item_number_id')];
			$countryId=$row[csf('country_id')];
			$colorId=$row[csf('color_number_id')];
			$sizeId=$row[csf('size_number_id')];
			$planPutQnty=$row[csf('plan_cut_qnty')];
			$fabNatureId=$row[csf('fab_nature_id')];
			$cons=$row[csf('cons')];
			$requirment=$row[csf('requirment')];
			$convertionId=$row[csf('convertion_id')];
			$fabricId=$row[csf('fabric_description')];
			$consProcessId=$row[csf('cons_process')];
			$req_qnty=$row[csf('req_qnty')];
			$avg_req_qnty=$row[csf('avg_req_qnty')];
			$charge_unit=$row[csf('charge_unit')];*/
			
			/*$jobNo=$row['job_no'];
			$poId=$row['id'];
			$itemNumberId=$row['item_number_id'];
			$countryId=$row['country_id'];
			$colorId=$row['color_number_id'];
			$sizeId=$row['size_number_id'];
			$planPutQnty=$row['plan_cut_qnty'];
			$fabNatureId=$row['fab_nature_id'];
			$cons=$row['cons'];
			$requirment=$row['requirment'];
			$convertionId=$row['convertion_id'];
			$fabricId=$row['fabric_description'];
			$consProcessId=$row['cons_process'];
			$req_qnty=$row['req_qnty'];
			$process_loss=$row['process_loss'];
			$avg_req_qnty=$row['avg_req_qnty'];
			$charge_unit=$row['charge_unit'];*/
			
			
			$jobNo=$row['job_no'];
			$poId=$row['id'];
			$itemNumberId=$row['item_number_id'];
			$countryId=$row['country_id'];
			$colorId=$row['color_number_id'];
			$sizeId=$row['size_number_id'];
			$planPutQnty=$row['plan_cut_qnty'];
			$preCostDtlsId=$row['pre_cost_dtls_id'];
			$fabNatureId=$row['fab_nature_id'];
			$colorSizeSensitive=$row['color_size_sensitive'];
			$colorTypeId=$row['color_type_id'];
			$diaWidth=$row['dia_width'];
			$cons=$row['cons'];
			$requirment=$row['requirment'];
			$convertionId=$row['convertion_id'];
			$fabricId=$row['fabric_description'];
			$consProcessId=$row['cons_process'];
			$req_qnty=$row['req_qnty'];
			$process_loss=$row['process_loss'];
			$avg_req_qnty=$row['avg_req_qnty'];
			$charge_unit=$row['charge_unit'];
			$colorBreakDown=$row['color_break_down'];
			
			//$costingPerQty=$this->_costingPer($costingPerArray[$jobNo]);
			//$set_item_ratio=$gmtsitemRatioArray[$jobNo][$itemNumberId];
			//$costingPerQty=$this->_costingPer($this->_costingPerQtyArr[$jobNo]);
			
			$costingPerQty=$this->_costingPerQtyArr[$jobNo];
			$set_item_ratio=$this->_gmtsitemRatioArray[$jobNo][$itemNumberId];
			
			//$requirment=$requirment-($requirment*$process_loss)/100;
			
			//$reqqnty=$this->_calculateQty($planPutQnty,$costingPerQty,$set_item_ratio,$requirment);
			
			$reqqnty=0;
			//$convrate=0;
            //$amount=0;
			
			$stripe_color_array=$this->_stripeColorArray[$preCostDtlsId][$colorId]; //[$stripe_color]=$stripe_color;
			//print_r($stripe_color_array);
			if(($colorTypeId==2 || $colorTypeId==3 || $colorTypeId==4 || $colorTypeId==6 ) && $consProcessId==30 && count($stripe_color_array)>0)
			{
				$qnty=0;
				$avgqnty=0;
				//$convrate=0;
				//$amt=0;
				foreach($stripe_color_array as $stripe_color_id){
					$stripe_color_cons_dzn=$this->_stripeColorReqArray[$preCostDtlsId][$colorId][$stripe_color_id];
					//echo $stripe_color_cons_dzn."==";
					$requirment=$stripe_color_cons_dzn-($stripe_color_cons_dzn*$process_loss)/100;
					$qnty=$this->_calculateQty($planPutQnty,$costingPerQty,$set_item_ratio,$requirment);
					
					$convrate=$this->_rateArray[$convertionId][$colorId][$stripe_color_id];
					//echo "A=".$qnty.'='.$convrate.'<br>';
					if($convrate>0){
					//$amt=$this->_calculateAmount($qnty,$convrate);
					$reqqnty+=$qnty;
					//$amount+=$amt;
					}
				}
				
			}
			else if($consProcessId !=30)
			{
				$convrate=0;
				$requirment=$requirment-($requirment*$process_loss)/100;
			    $reqqnty=$this->_calculateQty($planPutQnty,$costingPerQty,$set_item_ratio,$requirment);
				//echo "B=".$qnty;
				/*$rateColorId=$colorId;
				if($colorSizeSensitive==3){
					$rateColorId=$this->_cotrastColorArray[$preCostDtlsId][$colorId];
				}else{
					$rateColorId=$colorId;
				}
				
				
				if($colorBreakDown !="")
				{
					$convrate=$this->_rateArray[$convertionId][$rateColorId];
				}
				else
				{
					$convrate=$charge_unit;
				}
				$amount=$this->_calculateAmount($reqqnty,$convrate);*/
			}
			
			if($level==$this->_By_Job){
				if(isset($Qty[$jobNo])){
					$Qty[$jobNo]+=$reqqnty;
				}
				else{
					$Qty[$jobNo]=$reqqnty;
				}
			}
			elseif($level==$this->_By_JobAndConversionid){
				if(isset($Qty[$jobNo][$convertionId])){
					$Qty[$jobNo][$convertionId]+=$reqqnty;
				}
				else{
					$Qty[$jobNo][$convertionId]=$reqqnty;
				}
			}
			elseif($level==$this->_By_JobAndFabric){
				if(isset($Qty[$jobNo][$fabricId])){
					$Qty[$jobNo][$fabricId]+=$reqqnty;
				}
				else{
					$Qty[$jobNo][$fabricId]=$reqqnty;
				}
			}
			elseif($level==$this->_By_JobAndProcess){
				if(isset($Qty[$jobNo][$consProcessId])){
					$Qty[$jobNo][$consProcessId]+=$reqqnty;
				}
				else{
					$Qty[$jobNo][$consProcessId]=$reqqnty;
				}
			}
			elseif($level==$this->_By_JobFabricAndProcess){
				if(isset($Qty[$jobNo][$fabricId][$consProcessId])){
					$Qty[$jobNo][$fabricId][$consProcessId]+=$reqqnty;
				}
				else{
					$Qty[$jobNo][$fabricId][$consProcessId]=$reqqnty;
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
			elseif($level==$this->_By_OrderAndConversionid){
				if(isset($Qty[$poId][$convertionId])){
					$Qty[$poId][$convertionId]+=$reqqnty;
				}
				else{
					$Qty[$poId][$convertionId]=$reqqnty;
				}
			}
			elseif($level==$this->_By_OrderAndFabric){
				if(isset($Qty[$poId][$fabricId])){
					$Qty[$poId][$fabricId]+=$reqqnty;
				}
				else{
					$Qty[$poId][$fabricId]=$reqqnty;
				}
			}
			elseif($level==$this->_By_OrderAndProcess){
				if(isset($Qty[$poId][$consProcessId])){
					$Qty[$poId][$consProcessId]+=$reqqnty;
				}
				else{
					$Qty[$poId][$consProcessId]=$reqqnty;
				}
			}
			elseif($level==$this->_By_OrderFabricAndProcess){
				if(isset($Qty[$poId][$fabricId][$consProcessId])){
					$Qty[$poId][$fabricId][$consProcessId]+=$reqqnty;
				}
				else{
					$Qty[$poId][$fabricId][$consProcessId]=$reqqnty;
				}
			}
			elseif($level==$this->_By_OrderFabricProcessAndDiaWidth){
				if(isset($Qty[$poId][$fabricId][$consProcessId][$diaWidth])){
					$Qty[$poId][$fabricId][$consProcessId][$diaWidth]+=$reqqnty;
				}
				else{
					$Qty[$poId][$fabricId][$consProcessId][$diaWidth]=$reqqnty;
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
			elseif($level==$this->_By_OrderCountryAndConversionid){
				if(isset($Qty[$poId][$countryId][$convertionId])){
					$Qty[$poId][$countryId][$convertionId]+=$reqqnty;
				}
				else{
					$Qty[$poId][$countryId][$convertionId]=$reqqnty;
				}
			}
			elseif($level==$this->_By_OrderCountryAndFabric){
				if(isset($Qty[$poId][$countryId][$fabricId])){
					$Qty[$poId][$countryId][$fabricId]+=$reqqnty;
				}
				else{
					$Qty[$poId][$countryId][$fabricId]=$reqqnty;
				}
			}
			elseif($level==$this->_By_OrderCountryAndProcess){
				if(isset($Qty[$poId][$countryId][$consProcessId])){
					$Qty[$poId][$countryId][$consProcessId]+=$reqqnty;
				}
				else{
					$Qty[$poId][$countryId][$consProcessId]=$reqqnty;
				}
			}
			elseif($level==$this->_By_OrderCountryFabricAndProcess){
				if(isset($Qty[$poId][$countryId][$fabricId][$consProcessId])){
					$Qty[$poId][$countryId][$fabricId][$consProcessId]+=$reqqnty;
				}
				else{
					$Qty[$poId][$countryId][$fabricId][$consProcessId]=$reqqnty;
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
		$preCostDtlsId='';
		$fabNatureId='';
		$colorSizeSensitive='';
		$colorTypeId='';
		$cons='';
		$requirment='';
		$convertionId='';
		$fabricId='';
		$consProcessId='';
		$req_qnty='';
		$process_loss='';
		$avg_req_qnty='';
		$charge_unit='';
		$colorBreakDown='';
		//$gmtsitemRatioArray=$this->_gmtsitemRatioArray;
		//$costingPerArray=$this->_costingPerQtyArr;
		$Amount=array();
		//foreach($this->_dataArray as $row)
		while($row=oci_fetch_assoc($this->_dataArray))
		{
			/*$jobNo=$row[csf('job_no')];
			$poId=$row[csf('id')];
			$itemNumberId=$row[csf('item_number_id')];
			$countryId=$row[csf('country_id')];
			$colorId=$row[csf('color_number_id')];
			$sizeId=$row[csf('size_number_id')];
			$planPutQnty=$row[csf('plan_cut_qnty')];
			$fabNatureId=$row[csf('fab_nature_id')];
			$cons=$row[csf('cons')];
			$requirment=$row[csf('requirment')];
			$convertionId=$row[csf('convertion_id')];
			$fabricId=$row[csf('fabric_description')];
			$consProcessId=$row[csf('cons_process')];
			$req_qnty=$row[csf('req_qnty')];
			$avg_req_qnty=$row[csf('avg_req_qnty')];
			$charge_unit=$row[csf('charge_unit')];
			$colorBreakDown=$row[csf('color_break_down')];*/
			
			$jobNo=$row['job_no'];
			$poId=$row['id'];
			$itemNumberId=$row['item_number_id'];
			$countryId=$row['country_id'];
			$colorId=$row['color_number_id'];
			$sizeId=$row['size_number_id'];
			$planPutQnty=$row['plan_cut_qnty'];
			$preCostDtlsId=$row['pre_cost_dtls_id'];
			$fabNatureId=$row['fab_nature_id'];
			$colorSizeSensitive=$row['color_size_sensitive'];
			$colorTypeId=$row['color_type_id'];
			$cons=$row['cons'];
			$requirment=$row['requirment'];
			$convertionId=$row['convertion_id'];
			$fabricId=$row['fabric_description'];
			$consProcessId=$row['cons_process'];
			$req_qnty=$row['req_qnty'];
			$process_loss=$row['process_loss'];
			$avg_req_qnty=$row['avg_req_qnty'];
			$charge_unit=$row['charge_unit'];
			$colorBreakDown=$row['color_break_down'];
			
			//$costingPerQty=$this->_costingPer($costingPerArray[$jobNo]);
			//$set_item_ratio=$gmtsitemRatioArray[$jobNo][$itemNumberId];
			//$costingPerQty=$this->_costingPer($this->_costingPerQtyArr[$jobNo]);
			$costingPerQty=$this->_costingPerQtyArr[$jobNo];
			$set_item_ratio=$this->_gmtsitemRatioArray[$jobNo][$itemNumberId];
			
			/*$convcolorrate=array();
			if($colorBreakDown !="")
			{
				$arr_1=explode("__",$colorBreakDown);
				for($ci=0;$ci<count($arr_1);$ci++)
				{
				$arr_2=explode("_",$arr_1[$ci]);
				$convcolorrate[$arr_2[0]]=$arr_2[1];
				}
			}*/
			$reqqnty=0;
			$convrate=0;
            $amount=0;
			
			$stripe_color_array=$this->_stripeColorArray[$preCostDtlsId][$colorId]; //[$stripe_color]=$stripe_color;
			if(($colorTypeId==2 || $colorTypeId==3 || $colorTypeId==4 || $colorTypeId==6) && $consProcessId==30 && count($stripe_color_array)>0)
			{
				$qnty=0;
				$avgqnty=0;
				$convrate=0;$reqqnty=0;
				$amt=0;$amount=0;
				foreach($stripe_color_array as $stripe_color_id){
					$stripe_color_cons_dzn=$this->_stripeColorReqArray[$preCostDtlsId][$colorId][$stripe_color_id];
					$requirment=$stripe_color_cons_dzn-($stripe_color_cons_dzn*$process_loss)/100;
					$qnty=$this->_calculateQty($planPutQnty,$costingPerQty,$set_item_ratio,$requirment);
					$convrate=$this->_rateArray[$convertionId][$colorId][$stripe_color_id];
					//echo $qnty.'='.$convrate.',';
					if($convrate>0){
					$amt=$this->_calculateAmount($qnty,$convrate);
					$reqqnty+=$qnty;
					$amount+=$amt;
					}
				}
				
			}
			else if($consProcessId !=30)
			{
				$convrate=0;
				$requirment=$requirment-($requirment*$process_loss)/100;
			    $reqqnty=$this->_calculateQty($planPutQnty,$costingPerQty,$set_item_ratio,$requirment);
				$rateColorId=$colorId;
				if($colorSizeSensitive==3){
					$rateColorId=$this->_cotrastColorArray[$preCostDtlsId][$colorId];
				}else{
					$rateColorId=$colorId;
				}
				
				
				if($colorBreakDown !="")
				{
					$convrate=$this->_rateArray[$convertionId][$colorId][$rateColorId];
				}
				else
				{
					$convrate=$charge_unit;
				}
				$amount=$this->_calculateAmount($reqqnty,$convrate);
			}
			
			if($level==$this->_By_Job){
				if(isset($Amount[$jobNo])){
					$Amount[$jobNo]+=$amount;
				}
				else{
					$Amount[$jobNo]=$amount;
				}
			}
			elseif($level==$this->_By_JobAndConversionid){
				if(isset($Amount[$jobNo][$convertionId])){
					$Amount[$jobNo][$convertionId]+=$amount;
				}
				else{
					$Amount[$jobNo][$convertionId]=$amount;
				}
			}
			elseif($level==$this->_By_JobAndFabric){
				if(isset($Amount[$jobNo][$fabricId])){
					$Amount[$jobNo][$fabricId]+=$amount;
				}
				else{
					$Amount[$jobNo][$fabricId]=$amount;
				}
			}
			elseif($level==$this->_By_JobAndProcess){
				if(isset($Amount[$jobNo][$consProcessId])){
					$Amount[$jobNo][$consProcessId]+=$amount;
				}
				else{
					$Amount[$jobNo][$consProcessId]=$amount;
				}
			}
			elseif($level==$this->_By_JobFabricAndProcess){
				if(isset($Amount[$jobNo][$fabricId][$consProcessId])){
					$Amount[$jobNo][$fabricId][$consProcessId]+=$amount;
				}
				else{
					$Amount[$jobNo][$fabricId][$consProcessId]=$amount;
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
			elseif($level==$this->_By_OrderAndConversionid){
				if(isset($Amount[$poId][$convertionId])){
					$Amount[$poId][$convertionId]+=$amount;
				}
				else{
					$Amount[$poId][$convertionId]=$amount;
				}
			}
			elseif($level==$this->_By_OrderAndFabric){
				if(isset($Amount[$poId][$fabricId])){
					$Amount[$poId][$fabricId]+=$amount;
				}
				else{
					$Amount[$poId][$fabricId]=$amount;
				}
			}
			elseif($level==$this->_By_OrderAndProcess){
				//echo $poId."_".$consProcessId;
				//die;
				if(isset($Amount[$poId][$consProcessId])){
					$Amount[$poId][$consProcessId]+=$amount;
				}
				else{
					$Amount[$poId][$consProcessId]=$amount;
				}
			}
			elseif($level==$this->_By_OrderFabricAndProcess){
				if(isset($Amount[$poId][$fabricId][$consProcessId])){
					$Amount[$poId][$fabricId][$consProcessId]+=$amount;
				}
				else{
					$Amount[$poId][$fabricId][$consProcessId]=$amount;
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
			elseif($level==$this->_By_OrderCountryAndConversionid){
				if(isset($Amount[$poId][$countryId][$convertionId])){
					$Amount[$poId][$countryId][$convertionId]+=$amount;
				}
				else{
					$Amount[$poId][$countryId][$convertionId]=$amount;
				}
			}
			elseif($level==$this->_By_OrderCountryAndFabric){
				if(isset($Amount[$poId][$countryId][$fabricId])){
					$Amount[$poId][$countryId][$fabricId]+=$amount;
				}
				else{
					$Amount[$poId][$countryId][$fabricId]=$amount;
				}
			}
			elseif($level==$this->_By_OrderCountryAndProcess){
				if(isset($Amount[$poId][$countryId][$consProcessId])){
					$Amount[$poId][$countryId][$consProcessId]+=$amount;
				}
				else{
					$Amount[$poId][$countryId][$consProcessId]=$amount;
				}
			}
			elseif($level==$this->_By_OrderCountryFabricAndProcess){
				if(isset($Amount[$poId][$countryId][$fabricId][$consProcessId])){
					$Amount[$poId][$countryId][$fabricId][$consProcessId]+=$amount;
				}
				else{
					$Amount[$poId][$countryId][$fabricId][$consProcessId]=$amount;
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
	public function getQty_by_jobAndConversionid($jobNo,$conversionId){
		$Qty=$this->_setQty($this->_By_JobAndConversionid);
		return $Qty[$jobNo][$conversionId];
	}
	
	public function getQtyArray_by_jobAndConversionid(){
		$Qty=$this->_setQty($this->_By_JobAndConversionid);
		return $Qty;
		
	}
	//Amount
	public function getAmount_by_jobAndConversionid($jobNo,$conversionId){
		$Amount=$this->_setAmount($this->_By_JobAndConversionid);
		return $Amount[$jobNo][$conversionId];
	}
	
	public function getAmountArray_by_jobAndConversionid(){
		$Amount=$this->_setAmount($this->_By_JobAndConversionid);
		return $Amount;
	}
	
	
	//Job and Fabric wise
	//Qty
	public function getQty_by_jobAndFabric($jobNo,$fabric){
		$Qty=$this->_setQty($this->_By_JobAndFabric);
		return $Qty[$jobNo][$fabric];
	}
	
	public function getQtyArray_by_jobAndFabric(){
		$Qty=$this->_setQty($this->_By_JobAndFabric);
		return $Qty;
		
	}
	//Amount
	public function getAmount_by_jobAndFabric($jobNo,$fabric){
		$Amount=$this->_setAmount($this->_By_JobAndFabric);
		return $Amount[$jobNo][$fabric];
	}
	
	public function getAmountArray_by_jobAndFabric(){
		$Amount=$this->_setAmount($this->_By_JobAndFabric);
		return $Amount;
	}
	
	//Job and Process wise
	//Qty
	public function getQty_by_jobAndProcess($jobNo,$process){
		$Qty=$this->_setQty($this->_By_JobAndProcess);
		return $Qty[$jobNo][$process];
	}
	
	public function getQtyArray_by_jobAndProcess(){
		$Qty=$this->_setQty($this->_By_JobAndProcess);
		return $Qty;
		
	}
	//Amount
	public function getAmount_by_jobAndProcess($jobNo,$process){
		$Amount=$this->_setAmount($this->_By_JobAndProcess);
		return $Amount[$jobNo][$process];
	}
	
	public function getAmountArray_by_jobAndProcess(){
		$Amount=$this->_setAmount($this->_By_JobAndProcess);
		return $Amount;
	}
	
	//Job Fabric and Process wise
	//Qty
	public function getQty_by_jobFabricAndProcess($jobNo,$fabric,$process){
		$Qty=$this->_setQty($this->_By_JobFabricAndProcess);
		return $Qty[$jobNo][$fabric][$process];
	}
	
	public function getQtyArray_by_jobFabricAndProcess(){
		$Qty=$this->_setQty($this->_By_JobFabricAndProcess);
		return $Qty;
		
	}
	//Amount
	public function getAmount_by_jobFabricAndProcess($jobNo,$fabric,$process){
		$Amount=$this->_setAmount($this->_By_JobFabricAndProcess);
		return $Amount[$jobNo][$fabric][$process];
	}
	
	public function getAmountArray_by_jobFabricAndProcess(){
		$Amount=$this->_setAmount($this->_By_JobFabricAndProcess);
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
	public function getQty_by_orderAndConversionid($poId,$conversionId){
		$Qty=$this->_setQty($this->_By_OrderAndConversionid);
		return $Qty[$poId][$conversionId];
	}
	
	public function getQtyArray_by_orderAndConversionid(){
		$Qty=$this->_setQty($this->_By_OrderAndConversionid);
		return $Qty;
		
	}
	//Amount
	public function getAmount_by_orderAndConversionid($poId,$conversionId){
		$Amount=$this->_setAmount($this->_By_OrderAndConversionid);
		return $Amount[$poId][$conversionId];
	}
	
	public function getAmountArray_by_orderAndConversionid(){
		$Amount=$this->_setAmount($this->_By_OrderAndConversionid);
		return $Amount;
	}
	
	
	//Order and Fabric wise
	//Qty
	public function getQty_by_orderAndFabric($poId,$fabric){
		$Qty=$this->_setQty($this->_By_OrderAndFabric);
		return $Qty[$poId][$fabric];
	}
	
	public function getQtyArray_by_orderAndFabric(){
		$Qty=$this->_setQty($this->_By_OrderAndFabric);
		return $Qty;
		
	}
	//Amount
	public function getAmount_by_orderAndFabric($poId,$fabric){
		$Amount=$this->_setAmount($this->_By_OrderAndFabric);
		return $Amount[$poId][$fabric];
	}
	
	public function getAmountArray_by_orderAndFabric(){
		$Amount=$this->_setAmount($this->_By_OrderAndFabric);
		return $Amount;
	}
	
	//Order and Process wise
	//Qty
	public function getQty_by_orderAndProcess($poId,$process){
		$Qty=$this->_setQty($this->_By_OrderAndProcess);
		return $Qty[$poId][$process];
	}
	
	public function getQtyArray_by_orderAndProcess(){
		$Qty=$this->_setQty($this->_By_OrderAndProcess);
		return $Qty;
	}
	//Amount
	public function getAmount_by_orderAndProcess($poId,$process){
		$Amount=$this->_setAmount($this->_By_OrderAndProcess);
		return $Amount[$poId][$process];
	}
	
	public function getAmountArray_by_orderAndProcess(){
		$Amount=$this->_setAmount($this->_By_OrderAndProcess);
		return $Amount;
	}
	
	//Order Fabric and Process wise
	//Qty
	public function getQty_by_orderFabricAndProcess($poId,$fabric,$process){
		$Qty=$this->_setQty($this->_By_OrderFabricAndProcess);
		return $Qty[$poId][$fabric][$process];
	}
	
	public function getQtyArray_by_orderFabricAndProcess(){
		$Qty=$this->_setQty($this->_By_OrderFabricAndProcess);
		return $Qty;
		
	}
	
	public function getQtyArray_by_orderFabricProcessAndDiaWidth(){
		$Qty=$this->_setQty($this->_By_OrderFabricProcessAndDiaWidth);
		return $Qty;
		
	}
	//Amount
	public function getAmount_by_orderFabricAndProcess($poId,$fabric,$process){
		$Amount=$this->_setAmount($this->_By_OrderFabricAndProcess);
		return $Amount[$poId][$fabric][$process];
	}
	
	public function getAmountArray_by_orderFabricAndProcess(){
		$Amount=$this->_setAmount($this->_By_OrderFabricAndProcess);
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
	public function getQty_by_orderCountryAndConversionid($poId,$countryId,$conversionId){
		$Qty=$this->_setQty($this->_By_OrderCountryAndConversionid);
		return $Qty[$poId][$countryId][$conversionId];
	}
	
	public function getQtyArray_by_orderCountryAndConversionid(){
		$Qty=$this->_setQty($this->_By_OrderCountryAndConversionid);
		return $Qty;
		
	}
	//Amount
	public function getAmount_by_orderCountryAndConversionid($poId,$countryId,$conversionId){
		$Amount=$this->_setAmount($this->_By_OrderCountryAndConversionid);
		return $Amount[$poId][$countryId][$conversionId];
	}
	
	public function getAmountArray_by_orderCountryAndConversionid(){
		$Amount=$this->_setAmount($this->_By_OrderCountryAndConversionid);
		return $Amount;
	}
	
	
	//Order,Country and Fabric wise
	//Qty
	public function getQty_by_orderCountryAndFabric($poId,$countryId,$fabric){
		$Qty=$this->_setQty($this->_By_OrderCountryAndFabric);
		return $Qty[$poId][$countryId][$fabric];
	}
	
	public function getQtyArray_by_orderCountryAndFabric(){
		$Qty=$this->_setQty($this->_By_OrderCountryAndFabric);
		return $Qty;
		
	}
	//Amount
	public function getAmount_by_orderCountryAndFabric($poId,$countryId,$fabric){
		$Amount=$this->_setAmount($this->_By_OrderCountryAndFabric);
		return $Amount[$poId][$countryId][$fabric];
	}
	
	public function getAmountArray_by_orderCountryAndFabric(){
		$Amount=$this->_setAmount($this->_By_OrderCountryAndFabric);
		return $Amount;
	}
	
	//Order,Country and Process wise
	//Qty
	public function getQty_by_orderCountryAndProcess($poId,$countryId,$process){
		$Qty=$this->_setQty($this->_By_OrderCountryAndProcess);
		return $Qty[$poId][$countryId][$process];
	}
	
	public function getQtyArray_by_orderCountryAndProcess(){
		$Qty=$this->_setQty($this->_By_OrderCountryAndProcess);
		return $Qty;
		
	}
	//Amount
	public function getAmount_by_orderCountryAndProcess($poId,$countryId,$process){
		$Amount=$this->_setAmount($this->_By_OrderCountryAndProcess);
		return $Amount[$poId][$countryId][$process];
	}
	
	public function getAmountArray_by_orderCountryAndProcess(){
		$Amount=$this->_setAmount($this->_By_OrderCountryAndProcess);
		return $Amount;
	}
	
	//Order,Country Fabric and Process wise
	//Qty
	public function getQty_by_orderCountryFabricAndProcess($poId,$countryId,$fabric,$process){
		$Qty=$this->_setQty($this->_By_OrderCountryFabricAndProcess);
		return $Qty[$poId][$countryId][$fabric][$process];
	}
	
	public function getQtyArray_by_orderCountryFabricAndProcess(){
		$Qty=$this->_setQty($this->_By_OrderCountryFabricAndProcess);
		return $Qty;
		
	}
	//Amount
	public function getAmount_by_orderCountryFabricAndProcess($poId,$countryId,$fabric,$process){
		$Amount=$this->_setAmount($this->_By_OrderCountryFabricAndProcess);
		return $Amount[$poId][$countryId][$fabric][$process];
	}
	
	public function getAmountArray_by_orderCountryFabricAndProcess(){
		$Amount=$this->_setAmount($this->_By_OrderCountryFabricAndProcess);
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
		unset($this->_dataArray);
		unset($this->_dataArray2);
		unset($this->_dataArray3);
		unset($this->_rateArray);
		unset($this->_cotrastColorArray);
	}
}
?>