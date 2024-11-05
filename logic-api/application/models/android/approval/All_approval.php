<?php
class All_approval extends CI_Model {

	function __construct() {
		error_reporting(0);
		parent::__construct();
	}


     function writeFile($fileName,$txt){
		$file="note_url_script/objectData/".$fileName.".text";
		$current = file_get_contents($file);
		$current .= $txt."\n..........".date('d-m-Y h:i:s a',time()).".........\n\n";
		file_put_contents($file, $current);
	 }


	function get_max_value($tableName, $fieldName) {
		return $this->db->select_max($fieldName)->get($tableName)->row()->{$fieldName};
	}


	function insertData($tableName,$post) {
		$this->db->trans_start();
		$this->db->insert($tableName, $post);
		$this->db->trans_complete();
		if ($this->db->trans_status() == TRUE) {
			return TRUE;
		} else {
			return FALSE;
		}
	}


	function updateData($tableName, $data, $condition) {
		$this->db->trans_start();
		$this->db->update($tableName, $data, $condition);
		$this->db->trans_complete();
		if ($this->db->trans_status() == TRUE) {
			return TRUE;
		} else {
			return FALSE;
		}
	}

	
	function deleteRowByAttribute($tableName, $attribute) {
		$this->db->trans_start();
		$this->db->delete($tableName, $attribute);
		$this->db->trans_complete();
		if ($this->db->trans_status() == TRUE) {
			return TRUE;
		} else {
			return FALSE;
		}
	}

	
	
	public function get_all_approval($user_id=0,$company_id=0,$page_id=0,$select_no = 0) {
		
		$buyer_arr=return_library_array( "select id, buyer_name from lib_buyer", "id", "buyer_name"  );
		$supplier_arr=return_library_array( "select id, supplier_name from lib_supplier", "id", "supplier_name"  );
		$company_arr=return_library_array( "select id, COMPANY_NAME from LIB_COMPANY where id in(1,3)", "id", "COMPANY_NAME"  );
		
			
		//user_sequence_no.........................
		$user_sequence_no_sql="select SEQUENCE_NO,PAGE_ID,COMPANY_ID from electronic_approval_setup where user_id=$user_id and is_deleted=0";
		$user_sequence_no_sql_data_arr = sql_select($user_sequence_no_sql);
		$user_sequence_no_array=array();
		foreach($user_sequence_no_sql_data_arr as $row)
		{
			$user_sequence_no_array[$row->PAGE_ID][$row->COMPANY_ID]=$row->SEQUENCE_NO;
		}

		//min_sequence_no.........................
		$min_sequence_no_sql="select min(sequence_no) as SEQUENCE_NO,PAGE_ID,COMPANY_ID from electronic_approval_setup where user_id=$user_id and is_deleted=0 group by PAGE_ID,COMPANY_ID";
		$min_sequence_no_sql_data_arr = sql_select($min_sequence_no_sql);
		$min_sequence_no_array=array();
		foreach($min_sequence_no_sql_data_arr as $row)
		{
			$min_sequence_no_array[$row->PAGE_ID][$row->COMPANY_ID]=$row->SEQUENCE_NO;
		}

		//min_sequence_no.........................
		$max_sequence_no_sql="select max(sequence_no) as SEQUENCE_NO,PAGE_ID,COMPANY_ID from electronic_approval_setup where user_id=$user_id and is_deleted=0 group by PAGE_ID,COMPANY_ID";
		$max_sequence_no_sql_data_arr = sql_select($max_sequence_no_sql);
		$max_sequence_no_array=array();
		foreach($max_sequence_no_sql_data_arr as $row)
		{
			$max_sequence_no_array[$row->PAGE_ID][$row->COMPANY_ID]=$row->SEQUENCE_NO;
		}

		//user and seq wise buyer id.........................
		$user_buyer_ids_array = array();
		$buyerData = sql_select("select USER_ID, SEQUENCE_NO, BUYER_ID,PAGE_ID,COMPANY_ID from electronic_approval_setup where is_deleted=0 and bypass=2");
		foreach($buyerData as $row)
		{
			$user_buyer_ids_array[$row->PAGE_ID][$row->COMPANY_ID][$row->USER_ID]['u']=$row->BUYER_ID;
			$user_buyer_ids_array[$row->PAGE_ID][$row->COMPANY_ID][$row->SEQUENCE_NO]['s']=$row->BUYER_ID;
		}




		$dataArr=array();
		$i=0;
		
		//$page_id 867=PI app;  410=FB app; 428=Pre Costing; 427=Price Quotation
		
		if($page_id==0){
			$pi_page_id=867;
			$fb_page_id=410;
			$pc_page_id=428;
			$pq_page_id=427;
			$gp_page_id=670;
			$gsd_page_id=850;
			$pr_page_id=813;
			$sr_page_id=937;
			$iir_page_id=1056;
			$sbk_page_id=1120;
			$woAOP_page_id=1177;
			$qc_page_id=1620;
			$db_page_id=616;
			//$tb_page_id=336;
			$tb_with_order_page_id=336000000;
			$tb_without_order_page_id=336000001;
			$yd_page_id=479;
			$sw_page_id=627;
			$opw_page_id=628;
			$ywn_page_id=412;
			$tr_page_id=1630;
			$ida_page_id=1684;
			$ewo_page_id=1257;
			
		}
		else
		{
			$pi_page_id=$page_id;
			$fb_page_id=$page_id;
			$pc_page_id=$page_id;
			$pq_page_id=$page_id;
			$gp_page_id=$page_id;
			$gsd_page_id=$page_id;
			$pr_page_id=$page_id;
			$sr_page_id=$page_id;
			$iir_page_id=$page_id;
			$sbk_page_id=$page_id;
			$woAOP_page_id=$page_id;
			$qc_page_id=$page_id;
			$db_page_id=$page_id;
			$tb_with_order_page_id=$page_id;
			$tb_without_order_page_id=$page_id;
			$yd_page_id=$page_id;
			$sw_page_id=$page_id;
			$opw_page_id=$page_id;
			$ywn_page_id=$page_id;
			$tr_page_id=$page_id;
			$ida_page_id=$page_id;
			$ewo_page_id=$page_id;
		}
		
		if($company_id){
			$company_arr=array($company_id=>$company_arr[$company_id]);
		}
		
		
		//OK
		//PI Approval.............................................................start;
		if($pi_page_id==867){
			
			$page_id=867;

			foreach($company_arr as $company_id=>$company_name){
			
				$user_sequence_no=$user_sequence_no_array[$page_id][$company_id];
				$min_sequence_no=$min_sequence_no_array[$page_id][$company_id];
				$maxUserNo=$max_sequence_no_array[$page_id][$company_id];
		
			
				if($select_no){$select_no_cond=" and a.id='$select_no'";}
			
				$approval_type=0;
				if($approval_type==0){
			   
				if($user_sequence_no==$min_sequence_no) // First user
				{
					
				   $sql="SELECT a.id as PI_ID,a.importer_id,a.source,a.supplier_id,a.PI_NUMBER,a.PI_DATE,a.LAST_SHIPMENT_DATE,a.internal_file_no,a.approved, sum(b.net_pi_amount) as net_pi_amount,a.net_total_amount,a.is_apply_last_update
					from com_pi_master_details a, com_pi_item_details b where a.id=b.PI_ID  and a.importer_id=$company_id and a.is_deleted=0 and a.status_active=1 and b.status_active=1 and b.is_deleted=0 and a.ready_to_approved=1 and a.approved=$approval_type and b.amount>0 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $date_cond $user_crediatial_item_cat_cond $pi_sys_no_cond $select_no_cond group by  a.id,a.item_category_id,a.importer_id,a.source,a.supplier_id,a.PI_NUMBER,a.PI_DATE,a.LAST_SHIPMENT_DATE,a.internal_file_no,a.approved,a.is_apply_last_update,b.item_category_id,a.net_total_amount order by a.id desc";
				   //return $sql;
				}
				else // Next user
				{
					
				
					if($this->db->dbdriver == 'mysqli')
					{
						$sequence_no=return_field_value("max(a.sequence_no) as seq","electronic_approval_setup a ,user_passwd b "," a.user_id=b.id and b.item_cate_id='' and a.company_id=$company_id and a.page_id=$page_id and a.sequence_no<$user_sequence_no and a.bypass=2 and a.is_deleted = 0","seq");
					}
					else
					{
				
						$sequence_no=return_field_value("max(a.sequence_no) as seq","electronic_approval_setup a, user_passwd b "," a.user_id=b.id and b.item_cate_id  is   null and a.company_id=$company_id and a.page_id=$page_id and a.sequence_no<$user_sequence_no and a.bypass=2  and a.is_deleted = 0","seq");
					}
				   // echo $sequence_no;die;
					if($sequence_no=="") // bypass if previous user Yes
					{
				
						$seqSql="select a.SEQUENCE_NO, a.BYPASS, b.ITEM_CATE_ID from electronic_approval_setup a, user_passwd b where a.user_id=b.id and  a.company_id=$company_id and a.page_id=$page_id and a.sequence_no<$user_sequence_no and a.is_deleted=0 order by a.sequence_no desc";
						//echo $seqSql;die;
						$seqData=sql_select($seqSql);
				
						$buyerIds=''; $sequence_no_by_yes=''; $sequence_no_by_no=''; $approved_string=''; $check_buyerIds_arr=array();
						foreach($seqData as $sRow)
						{
							if($sRow->BYPASS==2)
							{
								$sequence_no_by_no[$sRow->SEQUENCE_NO]=$sRow->SEQUENCE_NO;
								if($sRow->ITEM_CATE_ID!="")
								{
									$buyerIds[$sRow->ITEM_CATE_ID]=$sRow->ITEM_CATE_ID;
									$buyer_id_arr=explode(",",$sRow->ITEM_CATE_ID);
									$result=array_diff($buyer_id_arr,$check_buyerIds_arr);
									if(count($result)>0)
									{
										$query_string.=" (c.sequence_no=".$sRow->SEQUENCE_NO." and b.item_category_id in(".implode(",",$result).")) or ";
									}
									$check_buyerIds_arr=array_unique(array_merge($check_buyerIds_arr,$buyer_id_arr));
								}
							}
							else
							{
								$sequence_no_by_yes[$sRow->SEQUENCE_NO]=$sRow->SEQUENCE_NO;
							}
						}
				
				
						$buyerIds=implode(',',$buyerIds);
						if($buyerIds=="")
						{
							$buyerIds_cond="";
							$seqCond="";
						}
						else
						{
							$buyerIds_cond=" and b.item_category_id not in($buyerIds)";
							$seqCond=" and (".chop($query_string,'or ').")";
						}
						$sequence_no_by_no=implode(',',$sequence_no_by_no);
						$sequence_no_by_yes=implode(',',$sequence_no_by_yes);
				
						if($sequence_no_by_yes=="") $sequence_no_by_yes=0;
						if($sequence_no_by_no=="") $sequence_no_by_no=0;
				
						$pi_mst_id_arr=array();
						$pi_mst_id_sql="select distinct (a.id) as PI_MST_ID from com_pi_master_details a, com_pi_item_details b, approval_history c where a.id=b.PI_ID and a.id=c.mst_id and a.importer_id=$company_id and c.sequence_no in ($sequence_no_by_no) and c.entry_form=27 and c.current_approval_status=1 $select_no_cond $user_crediatial_item_cat_cond $seqCond
						union
						select distinct (a.id) as PI_MST_ID from com_pi_master_details a, com_pi_item_details b, approval_history c where a.id=b.PI_ID and a.id=c.mst_id and a.importer_id=$company_id and c.sequence_no in ($sequence_no_by_yes) and c.entry_form=27 and c.current_approval_status=1 $select_no_cond $user_crediatial_item_cat_cond";
				
						//return $pi_mst_id_sql;
						$bResult=sql_select($pi_mst_id_sql);
						foreach($bResult as $bRow)
						{
							$pi_mst_id_arr[$bRow->PI_MST_ID]=$bRow->PI_MST_ID;
						}
				
				
						$pi_mst_id=implode(',',$pi_mst_id_arr);
				
						$pi_mst_id_app_sql=sql_select("select a.id as PI_MST_ID from com_pi_master_details a, com_pi_item_details b, approval_history c
						where a.id=b.PI_ID and a.id=c.mst_id and a.importer_id=$company_id and c.sequence_no=$user_sequence_no and c.entry_form=27 and a.ready_to_approved=1 and c.current_approval_status=1");
				
						$pi_mst_id_app_byuser_arr=array();
						foreach($pi_mst_id_app_sql as $inf)
						{
							$pi_mst_id_app_byuser_arr[$inf->PI_MST_ID]=$inf->PI_MST_ID;
						}
				
						$pi_mst_id_app_byuser=implode(",",$pi_mst_id_app_byuser_arr);
				
						
						$result=array_diff($pi_mst_id_arr,$pi_mst_id_app_byuser_arr);
						$pi_mst_id=implode(",",$result);
				
						$pi_mst_id_cond="";
				
						if($pi_mst_id_app_byuser!="")
						{
							$pi_mst_id_app_byuser_arr=explode(",",$pi_mst_id_app_byuser);
							if(count($pi_mst_id_app_byuser_arr)>995)
							{
								$pi_mst_id_app_byuser_chunk_arr=array_chunk(explode(",",$pi_mst_id_app_byuser),995) ;
								foreach($pi_mst_id_app_byuser_chunk_arr as $chunk_arr)
								{
									$chunk_arr_value=implode(",",$chunk_arr);
									$pi_mst_id_cond.=" and a.id not  in($chunk_arr_value)";
								}
							}
							else
							{
								$pi_mst_id_cond=" and a.id not in($pi_mst_id_app_byuser)";
							}
						}
						else $pi_mst_id_cond="";
				
				
					   $sql="SELECT a.id as PI_ID,a.importer_id,a.source,a.supplier_id,a.PI_NUMBER,a.PI_DATE,a.LAST_SHIPMENT_DATE, a.internal_file_no,a.approved, sum(b.net_pi_amount) as net_pi_amount,a.net_total_amount,a.is_apply_last_update FROM com_pi_master_details a, com_pi_item_details b WHERE a.id=b.PI_ID and a.importer_id=$company_id and a.is_deleted=0 and a.status_active=1 and b.status_active=1 and b.is_deleted=0 and a.ready_to_approved=1 and b.amount>0 $date_cond $user_crediatial_item_cat_cond $pi_sys_no_cond $pi_no_cond $pi_mst_id_cond $buyerIds_cond and a.approved in(0) $select_no_cond GROUP by a.id,a.importer_id,a.source,a.supplier_id,a.PI_NUMBER,a.PI_DATE,a.LAST_SHIPMENT_DATE,a.internal_file_no,a.approved,a.is_apply_last_update,a.net_total_amount ORDER by a.id desc";
						
						//return $sql;  
				
						if($pi_mst_id!="")
						{
				
				
							$pi_mst_id_cond2="and ";
							$pi_mst_id_arr=explode(",",$pi_mst_id);
							if(count($pi_mst_id_arr)>995)
							{
								$pi_mst_id_cond2.=" ( ";
								$pi_mst_id_arr_chunk_arr=array_chunk(explode(",",$pi_mst_id),995) ;
								$slcunk=0;
								foreach($pi_mst_id_arr_chunk_arr as $chunk_arr)
								{
									if($slcunk>0) $pi_mst_id_cond2.=" or";
									$chunk_arr_value=implode(",",$chunk_arr);	
									$pi_mst_id_cond2.="  a.id  in($chunk_arr_value)";
									$slcunk++;	
								}
								$pi_mst_id_cond2.=" )";
							}
							else
							{
								$pi_mst_id_cond2.="  a.id  in($pi_mst_id)";	 
							}
							
							 $sql=" select x.* from (SELECT a.id as PI_ID,a.importer_id,a.source,a.supplier_id,a.PI_NUMBER,a.PI_DATE,a.LAST_SHIPMENT_DATE,
						a.internal_file_no,a.approved, sum(b.net_pi_amount) as net_pi_amount,a.net_total_amount,a.is_apply_last_update
						FROM com_pi_master_details a, com_pi_item_details b 
						WHERE a.id=b.PI_ID  and a.importer_id=$company_id and a.is_deleted=0 and a.status_active=1 and b.status_active=1 and b.is_deleted=0 and a.ready_to_approved=1 and b.amount>0 $date_cond $user_crediatial_item_cat_cond $pi_sys_no_cond $select_no_cond $pi_mst_id_cond $buyerIds_cond and a.approved in(0) 
						GROUP by a.id,a.importer_id,a.source,a.supplier_id,a.PI_NUMBER,a.PI_DATE,a.LAST_SHIPMENT_DATE,a.internal_file_no,a.approved,a.is_apply_last_update,a.net_total_amount
					   ";
							 
							
							$sql.=" union all
							SELECT a.id as PI_ID,a.importer_id,a.source,a.supplier_id,a.PI_NUMBER,a.PI_DATE,a.LAST_SHIPMENT_DATE,
								a.internal_file_no,a.approved, sum(b.net_pi_amount) as net_pi_amount,a.net_total_amount,a.is_apply_last_update
								FROM com_pi_master_details a, com_pi_item_details b 
								WHERE a.id=b.PI_ID  and a.importer_id=$company_id and a.is_deleted=0 and a.status_active=1 and b.status_active=1 and b.is_deleted=0 and a.ready_to_approved=1 and b.amount>0 $date_cond $user_crediatial_item_cat_cond $pi_sys_no_cond $select_no_cond $pi_mst_id_cond2 and a.approved in(1,3) 
								GROUP by a.id,a.importer_id,a.source,a.supplier_id,a.PI_NUMBER,a.PI_DATE,a.LAST_SHIPMENT_DATE,a.internal_file_no,a.approved,a.is_apply_last_update,a.net_total_amount ) x
								order by x.id";
				
						}
						// echo "**".$sql;die;
					}
					else // bypass No
					{
						$user_sequence_no=$user_sequence_no-1;
				
						if($this->db->dbdriver == 'mysqli')
						{
							$sequence_no_by_pass=return_field_value("group_concat(sequence_no)","electronic_approval_setup","page_id=$page_id and
							sequence_no between $sequence_no and $user_sequence_no  and is_deleted=0");
						}
						else
						{
							$sequence_no_by_pass=return_field_value("LISTAGG(sequence_no, ',') WITHIN GROUP (ORDER BY sequence_no) as sequence_no",
							"electronic_approval_setup","page_id=$page_id and sequence_no between $sequence_no and $user_sequence_no  and
							is_deleted=0","sequence_no");
						}
				
						if($sequence_no_by_pass=="") $sequence_no_cond=" and c.sequence_no='$user_sequence_no'";
						else $sequence_no_cond=" and (c.sequence_no='$sequence_no' or c.sequence_no in ($sequence_no_by_pass))";
					   
						$sql="SELECT a.id as PI_ID, a.importer_id, a.source, a.supplier_id, a.PI_NUMBER, a.PI_DATE, a.LAST_SHIPMENT_DATE, a.internal_file_no,a.approved, a.is_apply_last_update, sum(b.net_pi_amount) as net_pi_amount,a.net_total_amount
						FROM com_pi_master_details a, com_pi_item_details b, approval_history c
						WHERE a.id=b.PI_ID and a.id=c.mst_id  and a.importer_id=$company_id and a.is_deleted=0 and a.status_active=1 and a.ready_to_approved=1 and a.approved in(1,3) and c.current_approval_status=1 and b.status_active=1 and b.is_deleted=0 and b.amount>0 $date_cond $sequence_no_cond $pi_sys_no_cond  $select_no_cond $user_crediatial_item_cat_cond
						GROUP by a.id,a.importer_id,a.source,a.supplier_id,a.PI_NUMBER,a.PI_DATE,a.LAST_SHIPMENT_DATE,a.internal_file_no,a.approved,a.is_apply_last_update,a.net_total_amount
						ORDER by a.id desc"; 
								 
					}
				}
			}
			//return $sql;
			
				$nameArray=sql_select( $sql );
				foreach ($nameArray as $row)
				{
					if($select_no){
						$dataArr[PAGE_ID]=$page_id;
						$dataArr[APP_TITLE]='PI Approval';
						$dataArr[COMPANY_ID]=$company_id;
						$dataArr[APP_ID]=$row->PI_ID;
						$dataArr[MESSAGE1]=$row->PI_NUMBER;
						$dataArr[MESSAGE2]=$row->PI_DATE;
						$dataArr[MESSAGE3]='';
						$dataArr[MESSAGE4]='';
					}
					else
					{
						$dataArr[$i][PAGE_ID]=$page_id;
						$dataArr[$i][APP_TITLE]='PI Approval';
						$dataArr[$i][COMPANY_ID]=$company_id;
						$dataArr[$i][APP_ID]=$row->PI_ID;
						$dataArr[$i][MESSAGE1]=$row->PI_NUMBER;
						$dataArr[$i][MESSAGE2]=$row->PI_DATE;
						$dataArr[$i][MESSAGE3]='';
						$dataArr[$i][MESSAGE4]='';
					}
					
					$i++; 
				}
				
			}//end company loof;
			
			
			
		}//End PI App if con;
		
		//OK
		//Fabric Bookin Approval.............................................................start;
		if($fb_page_id==410){
			
			$page_id=410;
			
			
			foreach($company_arr as $company_id=>$company_name){
			
				$user_sequence_no=$user_sequence_no_array[$page_id][$company_id];
				$min_sequence_no=$min_sequence_no_array[$page_id][$company_id];
				$maxUserNo=$max_sequence_no_array[$page_id][$company_id];
				$buyer_ids_array=$user_buyer_ids_array[$page_id][$company_id];
		
				$approval_type=0;
				if($approval_type==0)
				{
					if($this->db->dbdriver == 'mysqli')
					{
						$sequence_no=return_field_value("max(sequence_no) as seq","electronic_approval_setup","company_id=$company_id and page_id=$page_id and sequence_no<$user_sequence_no and bypass=2 and buyer_id='' and is_deleted=0","seq");
					}
					else
					{
						$sequence_no=return_field_value("max(sequence_no) as seq","electronic_approval_setup","company_id=$company_id and page_id=$page_id and sequence_no<$user_sequence_no and bypass=2 and buyer_id is null and is_deleted=0","seq");
					}
			
				
					
					if($user_sequence_no==$min_sequence_no)
					{
						$buyer_ids=$buyer_ids_array[$user_id]['u'];
						if($buyer_ids!=""){$buyer_id_cond=" and a.BUYER_ID in($buyer_ids)";}
						if($select_no){$booking_cond=" and a.BOOKING_NO='$select_no'";}
			
						$sql="select a.id,a.entry_form, a.booking_no_prefix_num, a.BOOKING_NO, a.ITEM_CATEGORY, a.fabric_source, a.company_id, a.booking_type, a.is_short, a.BUYER_ID, a.SUPPLIER_ID, a.DELIVERY_DATE, a.BOOKING_DATE, '0' as approval_id, a.is_approved, a.is_apply_last_update from wo_booking_mst a, wo_booking_dtls b where a.booking_no=b.booking_no  and a.company_id=$company_id and a.is_short in(2,3) and a.booking_type=1 and a.ITEM_CATEGORY in(2,3,13) and a.is_deleted=0 and a.status_active=1 and b.status_active=1 and b.is_deleted=0 and a.ready_to_approved=1 and a.is_approved=$approval_type and b.fin_fab_qnty>0 $buyer_id_cond $booking_cond group by a.id, a.booking_no, a.ITEM_CATEGORY, a.fabric_source, a.company_id, a.booking_type, a.is_short, a.BUYER_ID, a.SUPPLIER_ID, a.DELIVERY_DATE, a.BOOKING_DATE, a.is_approved, a.is_apply_last_update, a.insert_date, a.update_date, a.booking_no_prefix_num,a.entry_form";
						
					}
					else if($sequence_no=="")
					{
						$buyer_ids=$buyer_ids_array[$user_id]['u'];
						if($buyer_ids!=""){$buyer_id_cond=" and a.BUYER_ID in($buyer_ids)";}
						if($select_no){$booking_cond=" and a.BOOKING_NO='$select_no'";}
			
						if($this->db->dbdriver == 'mysqli')
						{
							$booking_id_app_byuser=return_field_value("group_concat(distinct(mst_id)) as booking_id","wo_booking_mst a, approval_history b","a.id=b.mst_id and a.company_id=$company_id and a.is_short=2 and a.booking_type=1 and a.ITEM_CATEGORY in(2,3,13) and b.sequence_no=$user_sequence_no and b.entry_form=7 and b.current_approval_status=1 $booking_cond","booking_id");
						}
						else
						{
							$booking_id_app_byuser=return_field_value("LISTAGG(mst_id, ',') WITHIN GROUP (ORDER BY mst_id) as booking_id","wo_booking_mst a, approval_history b","a.id=b.mst_id and a.company_id=$company_id and a.is_short=2 and a.booking_type=1 and a.ITEM_CATEGORY in(2,3,13) and b.sequence_no=$user_sequence_no and b.entry_form=7 and b.current_approval_status=1 $booking_cond","booking_id");
						}
			
						
							
							
						$seqSql="select SEQUENCE_NO, BYPASS, BUYER_ID from electronic_approval_setup where company_id=$company_id and page_id=$page_id and sequence_no<$user_sequence_no and is_deleted=0 order by sequence_no desc";
						$seqData=sql_select($seqSql);
						
						$buyerIds=''; $sequence_no_by_yes=''; $sequence_no_by_no=''; $approved_string=''; $check_buyerIds_arr=array();
						foreach($seqData as $sRow)
						{
							if($sRow->BYPASS==2)
							{
								$sequence_no_by_no.=$sRow->SEQUENCE_NO.",";
								if($sRow->BUYER_ID!="")
								{
									$buyerIds.=$sRow->BUYER_ID.",";
		
									$buyer_id_arr=explode(",",$sRow->BUYER_ID);
									$result=array_diff($buyer_id_arr,$check_buyerIds_arr);
									if(count($result)>0)
									{
										$query_string.=" (b.sequence_no=".$sRow->SEQUENCE_NO." and a.BUYER_ID in(".implode(",",$result).")) or ";
									}
									$check_buyerIds_arr=array_unique(array_merge($check_buyerIds_arr,$buyer_id_arr));
								}
							}
							else
							{
								$sequence_no_by_yes.=$sRow->SEQUENCE_NO.",";
							}
						}
		
						$buyerIds=chop($buyerIds,',');
						if($buyerIds=="")
						{
							$buyerIds_cond="";
							$seqCond="";
						}
						else
						{
							$buyerIds_cond=" and a.BUYER_ID not in($buyerIds)";
							$seqCond=" and (".chop($query_string,'or ').")";
						}
		
							
							
							
						$sequence_no_by_no=chop($sequence_no_by_no,',');
						$sequence_no_by_yes=chop($sequence_no_by_yes,',');
		
						if($sequence_no_by_yes=="") $sequence_no_by_yes=0;
						if($sequence_no_by_no=="") $sequence_no_by_no=0;
		
						$booking_id='';
						if($select_no){$booking_cond=" and a.BOOKING_NO='$select_no'";}
						
						$booking_id_sql="select distinct (mst_id) as BOOKING_ID from wo_booking_mst a, approval_history b where a.id=b.mst_id and a.company_id=$company_id and a.is_short=2 and a.booking_type=1 and a.ITEM_CATEGORY in(2,3,13) and b.sequence_no in ($sequence_no_by_no) and b.entry_form=7 and b.current_approval_status=1 $buyer_id_cond $date_cond $seqCond $booking_cond
						union
						select distinct (mst_id) as BOOKING_ID from wo_booking_mst a, approval_history b where a.id=b.mst_id and a.company_id=$company_id and a.is_short=2 and a.booking_type=1 and a.ITEM_CATEGORY in(2,3,13) and b.sequence_no in ($sequence_no_by_yes) and b.entry_form=7 and b.current_approval_status=1 $buyer_id_cond $date_cond $booking_cond";
						$bResult=sql_select($booking_id_sql);
						
						//return $booking_id_sql;
						foreach($bResult as $bRow)
						{
							$booking_id.=$bRow->BOOKING_ID.",";
						}
		
						$booking_id=chop($booking_id,',');
			
			
						$booking_id_app_byuser=implode(",",array_unique(explode(",",$booking_id_app_byuser)));
				
				
						$result=array_diff(explode(',',$booking_id),explode(',',$booking_id_app_byuser));
						$booking_id=implode(",",$result);
			
						$booking_id_cond="";
						if($booking_id!="")
						{
							if($this->db->dbdriver != 'mysqli' && count($result)>999)
							{
								$booking_id_chunk_arr=array_chunk($result,999) ;
								foreach($booking_id_chunk_arr as $chunk_arr)
								{
									$chunk_arr_value=implode(",",$chunk_arr);
									$bokIds_cond.=" a.id in($chunk_arr_value) or ";
								}
			
								$booking_id_cond.=" and (".chop($bokIds_cond,'or ').")";
							}
							else
							{
								$booking_id_cond=" and a.id in($booking_id)";
							}
						}
						else $booking_id_cond="";
			
						
						if($select_no){$booking_cond=" and a.BOOKING_NO='$select_no'";}
						
						//return $booking_cond;
						
						
						if($this->db->dbdriver == 'mysqli')
						{
							if($booking_id)
							{
								$sql="select a.entry_form,a.update_date as ob_update, a.insert_date as ob_insertdate, a.id, a.booking_no_prefix_num, a.BOOKING_NO, a.ITEM_CATEGORY, a.fabric_source, a.company_id, a.booking_type, a.is_short, a.BUYER_ID, a.SUPPLIER_ID, a.DELIVERY_DATE, a.BOOKING_DATE, '0' as approval_id,  a.is_approved, a.is_apply_last_update from wo_booking_mst a, wo_booking_dtls b where a.booking_no=b.booking_no and a.company_id=$company_id and a.is_short in(2,3) and a.booking_type=1 and a.ITEM_CATEGORY in(2,3,13) and a.is_deleted=0 and a.status_active=1 and b.status_active=1 and b.is_deleted=0 and a.ready_to_approved=1 and a.is_approved=$approval_type and b.fin_fab_qnty>0 $buyer_id_cond $buyerIds_cond $buyer_id_cond2 $booking_no_cond $booking_cond group by a.id, a.booking_no, a.ITEM_CATEGORY, a.fabric_source, a.company_id, a.booking_type, a.is_short, a.BUYER_ID, a.SUPPLIER_ID, a.DELIVERY_DATE, a.BOOKING_DATE, a.is_approved, a.is_apply_last_update, a.insert_date, a.update_date, a.booking_no_prefix_num,a.entry_form
									union all
									select a.entry_form,a.update_date as ob_update, a.insert_date as ob_insertdate, a.id, a.booking_no_prefix_num, a.BOOKING_NO, a.ITEM_CATEGORY, a.fabric_source, a.company_id, a.booking_type, a.is_short, a.BUYER_ID, a.SUPPLIER_ID, a.DELIVERY_DATE, a.BOOKING_DATE, '0' as approval_id, a.is_approved, a.is_apply_last_update from wo_booking_mst a, wo_booking_dtls b where a.booking_no=b.booking_no and a.company_id=$company_id and a.is_short in(2,3) and a.booking_type=1 and a.ITEM_CATEGORY in(2,3,13) and a.is_deleted=0 and a.status_active=1 and b.status_active=1 and b.is_deleted=0 and a.ready_to_approved=1 and a.is_approved in(3) and b.fin_fab_qnty>0 and a.id in($booking_id) $buyer_id_cond $buyer_id_cond2 $date_cond $booking_no_cond $booking_cond group by a.id, a.booking_no, a.ITEM_CATEGORY, a.fabric_source, a.company_id, a.booking_type, a.is_short, a.BUYER_ID, a.SUPPLIER_ID, a.DELIVERY_DATE, a.BOOKING_DATE,a.is_approved, a.is_apply_last_update, a.insert_date, a.update_date, a.booking_no_prefix_num,a.entry_form ";
							}
							else
							{
								$sql="select a.entry_form,a.id, a.booking_no_prefix_num, a.BOOKING_NO, a.ITEM_CATEGORY, a.fabric_source, a.company_id, a.booking_type, a.is_short, a.BUYER_ID, a.SUPPLIER_ID, a.DELIVERY_DATE, a.BOOKING_DATE, '0' as approval_id, a.is_approved, a.is_apply_last_update from wo_booking_mst a, wo_booking_dtls b where a.booking_no=b.booking_no and a.company_id=$company_id and a.is_short in(2,3) and a.booking_type=1 and a.ITEM_CATEGORY in(2,3,13) and a.is_deleted=0 and a.status_active=1 and b.status_active=1 and b.is_deleted=0 and a.ready_to_approved=1 and a.is_approved=$approval_type and b.fin_fab_qnty>0 $buyer_id_cond $buyer_id_cond2 $date_cond $buyerIds_cond $booking_no_cond $booking_year_cond $booking_cond group by a.id, a.booking_no, a.ITEM_CATEGORY, a.fabric_source, a.company_id, a.booking_type, a.is_short, a.BUYER_ID, a.SUPPLIER_ID, a.DELIVERY_DATE, a.BOOKING_DATE, a.is_approved, a.is_apply_last_update, a.insert_date, a.update_date, a.booking_no_prefix_num,a.entry_form ";
							}
							//echo $sql;
						}
						else
						{
							if($booking_id!=0)
							{   // and a.id in($booking_id)
								$sql="select * from(select a.entry_form,a.update_date, a.insert_date, a.id, a.booking_no_prefix_num, a.BOOKING_NO, a.ITEM_CATEGORY, a.fabric_source, a.company_id, a.booking_type, a.is_short, a.BUYER_ID, a.SUPPLIER_ID, a.DELIVERY_DATE, a.BOOKING_DATE, '0' as approval_id, a.is_approved, a.is_apply_last_update from wo_booking_mst a, wo_booking_dtls b where a.booking_no=b.booking_no and a.company_id=$company_id and a.is_short in(2,3) and a.booking_type=1 and a.ITEM_CATEGORY in(2,3,13) and a.is_deleted=0 and a.status_active=1 and b.status_active=1 and b.is_deleted=0 and a.ready_to_approved=1 and a.is_approved=$approval_type and b.fin_fab_qnty>0 $buyer_id_cond  $date_cond $buyerIds_cond $buyer_id_cond2 $booking_no_cond $booking_cond group by a.id, a.booking_no,a.entry_form, a.ITEM_CATEGORY, a.fabric_source, a.company_id, a.booking_type, a.is_short, a.BUYER_ID, a.SUPPLIER_ID, a.DELIVERY_DATE, a.BOOKING_DATE, a.is_approved, a.is_apply_last_update, a.insert_date, a.update_date, a.booking_no_prefix_num 
								union all 
								select a.entry_form,a.update_date, a.insert_date, a.id, a.booking_no_prefix_num, a.BOOKING_NO, a.ITEM_CATEGORY, a.fabric_source, a.company_id, a.booking_type, a.is_short, a.BUYER_ID, a.SUPPLIER_ID, a.DELIVERY_DATE, a.BOOKING_DATE, '0' as approval_id, a.is_approved, a.is_apply_last_update from wo_booking_mst a, wo_booking_dtls b  where a.booking_no=b.booking_no and a.company_id=$company_id and a.is_short in(2,3) and a.booking_type=1 and a.ITEM_CATEGORY in(2,3,13) and a.is_deleted=0 and a.status_active=1 and b.status_active=1 and b.is_deleted=0 and a.ready_to_approved=1 and a.is_approved in (3) and b.fin_fab_qnty>0 $booking_id_cond $booking_cond $buyer_id_cond $buyer_id_cond2 $date_cond $booking_no_cond group by a.id, a.booking_no,a.entry_form, a.ITEM_CATEGORY, a.fabric_source, a.company_id, a.booking_type, a.is_short, a.BUYER_ID, a.SUPPLIER_ID, a.DELIVERY_DATE, a.BOOKING_DATE, a.is_approved, a.is_apply_last_update, a.insert_date, a.update_date, a.booking_no_prefix_num)";
									
									///return $sql;
							}
							else
							{
								$sql="select a.entry_form,a.id, a.booking_no_prefix_num, a.BOOKING_NO, a.ITEM_CATEGORY, a.fabric_source, a.company_id, a.booking_type, a.is_short, a.BUYER_ID, a.SUPPLIER_ID, a.DELIVERY_DATE, a.BOOKING_DATE, '0' as approval_id, a.is_approved, a.is_apply_last_update from wo_booking_mst a, wo_booking_dtls b where a.booking_no=b.booking_no and a.company_id=$company_id and a.is_short in(2,3) and a.booking_type=1 and a.ITEM_CATEGORY in(2,3,13) and a.is_deleted=0 and a.status_active=1 and b.status_active=1 and b.is_deleted=0 and a.ready_to_approved=1 and a.is_approved=$approval_type and b.fin_fab_qnty>0 $buyer_id_cond $booking_cond $buyer_id_cond2   $date_cond $buyerIds_cond $booking_no_cond $booking_year_cond group by a.id, a.booking_no,a.entry_form, a.ITEM_CATEGORY, a.fabric_source, a.company_id, a.booking_type, a.is_short, a.BUYER_ID, a.SUPPLIER_ID, a.DELIVERY_DATE, a.BOOKING_DATE, a.is_approved, a.is_apply_last_update, a.insert_date, a.update_date, a.booking_no_prefix_num";
								// echo $sql;die;
							}
			
			
						}
					}
					else
					{
						$buyer_ids=$buyer_ids_array[$user_id]['u'];
						if($buyer_ids=="") $buyer_id_cond=""; else $buyer_id_cond=" and a.BUYER_ID in($buyer_ids)";
						if($select_no){$booking_cond=" and a.BOOKING_NO='$select_no'";}
			
						$user_sequence_no = $user_sequence_no-1;
						
							if($this->db->dbdriver == 'mysqli')
							{
								$sequence_no_by_pass=return_field_value("group_concat(sequence_no)","electronic_approval_setup","company_id=$company_id and page_id=$page_id and sequence_no between $sequence_no and $user_sequence_no and is_deleted=0");
							}
							else
							{
								$sequence_no_by_pass=return_field_value("LISTAGG(sequence_no, ',') WITHIN GROUP (ORDER BY sequence_no) as sequence_no","electronic_approval_setup","company_id=$company_id and page_id=$page_id and sequence_no between $sequence_no and $user_sequence_no and is_deleted=0","sequence_no");
							}
			
							if($sequence_no_by_pass=="") $sequence_no_cond=" and b.sequence_no='$sequence_no'";
							else $sequence_no_cond=" and b.sequence_no in ($sequence_no_by_pass)";
			
							$sql="select a.entry_form,a.id, a.booking_no_prefix_num, a.BOOKING_NO, a.ITEM_CATEGORY, a.fabric_source, a.company_id, a.booking_type, a.is_short, a.BUYER_ID, a.SUPPLIER_ID, a.DELIVERY_DATE, a.BOOKING_DATE,a.is_approved, b.id as approval_id, b.sequence_no, b.approved_by, a.is_apply_last_update from wo_booking_mst a, approval_history b where a.id=b.mst_id  and b.entry_form=7 and a.company_id=$company_id and a.is_short in(2,3) and a.booking_type=1 and a.ITEM_CATEGORY in(2,3,13) $booking_cond and a.status_active=1 and a.is_deleted=0 and b.current_approval_status=1 and a.ready_to_approved=1 and a.is_approved in (1,3) $buyer_id_cond $buyer_id_cond2 $sequence_no_cond   $date_cond $booking_no_cond $booking_year_cond";
						
					}
				}
			 	//return $booking_cond;
		

				$nameArray=sql_select( $sql );
				foreach ($nameArray as $row)
				{
					
					if($select_no){
						$dataArr[PAGE_ID]=$page_id;
						$dataArr[APP_TITLE]='Fabric Bookin Approval';
						$dataArr[COMPANY_ID]=$company_id;
						$dataArr[APP_ID]=$row->BOOKING_NO;
						$dataArr[MESSAGE1]=($row->BOOKING_DATE)?$row->BOOKING_DATE:'00-00-0000';
						$dataArr[MESSAGE2]=($row->DELIVERY_DATE)?$row->DELIVERY_DATE:'00-00-0000';
						$dataArr[MESSAGE3]=($buyer_arr[$row->BUYER_ID])?$buyer_arr[$row->BUYER_ID]:'';
						$dataArr[MESSAGE4]=$supplier_arr[$row->SUPPLIER_ID];
					}
					else
					{
						$dataArr[$i][PAGE_ID]=$page_id;
						$dataArr[$i][APP_TITLE]='Fabric Bookin Approval';
						$dataArr[$i][COMPANY_ID]=$company_id;
						$dataArr[$i][APP_ID]=$row->BOOKING_NO;
						
						$dataArr[$i][MESSAGE1]=($row->BOOKING_DATE)?$row->BOOKING_DATE:'00-00-0000';
						$dataArr[$i][MESSAGE2]=($row->DELIVERY_DATE)?$row->DELIVERY_DATE:'00-00-0000';
						$dataArr[$i][MESSAGE3]=($buyer_arr[$row->BUYER_ID])?$buyer_arr[$row->BUYER_ID]:'';
						$dataArr[$i][MESSAGE4]=$supplier_arr[$row->SUPPLIER_ID];
					}
					
					$i++;
				}
				
			}//end company loof;
			


		
		}//End Fabric Booking if con;
		
		//OK
		//Pre Costing Approval.............................................................start;
		if($pc_page_id==428){
			
			$page_id=428;//869
			
			foreach($company_arr as $company_id=>$company_name){
			
				$user_sequence_no=$user_sequence_no_array[$page_id][$company_id];
				$min_sequence_no=$min_sequence_no_array[$page_id][$company_id];
				$maxUserNo=$max_sequence_no_array[$page_id][$company_id];
		
				$buyer_ids_array=$user_buyer_ids_array[$page_id][$company_id];
			
			
			  if($user_sequence_no !="" )
			  {
					
				if($this->db->dbdriver == 'mysqli'){$year_cond="SUBSTRING_INDEX(a.insert_date, '-', 1) as year";}
				else{$year_cond="to_char(a.insert_date,'YYYY') as year";}
			
			
		   
			   if($select_no){$job_no_cond=" and a.job_no='$select_no'";}
			   
			   $type=0;
			   if($type==0)
			   {
					if ($this->db->dbdriver == 'mysqli')
					{
						$sequence_no = return_field_value("max(sequence_no) as seq","electronic_approval_setup","company_id=$company_id and page_id=$page_id and sequence_no<$user_sequence_no and bypass = 2 and buyer_id='' and is_deleted=0","seq");
					}
					else
					{
						$sequence_no = return_field_value("max(sequence_no) as seq","electronic_approval_setup","company_id=$company_id and page_id=$page_id and sequence_no<$user_sequence_no and bypass=2 and buyer_id is null and is_deleted=0","seq");
					}
			
					if($user_sequence_no==$min_sequence_no)
					{
						$buyer_ids = $buyer_ids_array[$user_id]['u'];
						if($buyer_ids=="") $buyer_id_cond2=""; else $buyer_id_cond2=" and a.buyer_name in($buyer_ids)";
			
			
						$sql="select b.id,a.quotation_id,a.job_no_prefix_num,a.job_no, a.buyer_name, a.STYLE_REF_NO,b.costing_date,'0' as approval_id, b.approved,b.inserted_by,b.entry_from from wo_pre_cost_mst b,  wo_po_details_master a ,wo_po_break_down d  where a.job_no=b.job_no and a.id=d.job_id and a.company_name=$company_id and a.is_deleted=0 and a.status_active=1 and b.status_active=1 and b.is_deleted=0 and b.ready_to_approved=1 and b.approved=2 $buyer_id_cond2 $date_cond $job_no_cond $job_year_cond $internal_ref_cond $file_no_cond group by b.id,a.quotation_id,a.job_no_prefix_num,a.job_no, a.buyer_name, a.STYLE_REF_NO,b.costing_date,'0', b.approved,b.inserted_by,b.entry_from";
						 //echo $sql;die;
					}
					else if($sequence_no == "")
					{
						$buyer_ids=$buyer_ids_array[$user_id]['u'];
						if($buyer_ids=="") $buyer_id_cond2=""; else $buyer_id_cond2=" and a.buyer_name in($buyer_ids)";
			
						if($buyer_ids=="") $buyer_id_cond3=""; else $buyer_id_cond3=" and c.buyer_name in($buyer_ids)";
			
							$seqSql="select SEQUENCE_NO, BYPASS, BUYER_ID from electronic_approval_setup where company_id=$company_id and page_id=$page_id and sequence_no<$user_sequence_no and is_deleted=0 order by sequence_no desc";
							$seqData=sql_select($seqSql);
			
			
			
							$buyerIds=''; $sequence_no_by_yes=''; $sequence_no_by_no=''; $approved_string=''; $check_buyerIds_arr=array();
							foreach($seqData as $sRow)
							{
								if($sRow->BYPASS==2)
								{
									$sequence_no_by_no.=$sRow->SEQUENCE_NO.",";
									if($sRow->BUYER_ID!="")
									{
										$buyerIds.=$sRow->BUYER_ID.",";
										$buyer_id_arr=explode(",",$sRow->BUYER_ID);
										$result=array_diff($buyer_id_arr,$check_buyerIds_arr);
										if(count($result)>0)
										{
											$query_string.=" (b.sequence_no=".$sRow->SEQUENCE_NO." and c.buyer_name in(".implode(",",$result).")) or ";
										}
										$check_buyerIds_arr=array_unique(array_merge($check_buyerIds_arr,$buyer_id_arr));
									}
								}
								else
								{
									$sequence_no_by_yes.=$sRow->SEQUENCE_NO.",";
								}
							}
			
							$buyerIds=chop($buyerIds,',');
							if($buyerIds=="")
							{
								$buyerIds_cond="";
								$seqCond="";
							}
							else
							{
								$buyerIds_cond=" and a.buyer_name not in($buyerIds)";
								$seqCond=" and (".chop($query_string,'or ').")";
							}
							$sequence_no_by_no=chop($sequence_no_by_no,',');
							$sequence_no_by_yes=chop($sequence_no_by_yes,',');
			
							if($sequence_no_by_yes=="") $sequence_no_by_yes=0;
							if($sequence_no_by_no=="") $sequence_no_by_no=0;
			
							$pre_cost_id='';
							$pre_cost_id_sql="select distinct (mst_id) as PRE_COST_ID from wo_pre_cost_mst a, approval_history b, wo_po_details_master c where a.id=b.mst_id and a.job_no=c.job_no and c.company_name=$company_id and b.sequence_no in ($sequence_no_by_no) and b.entry_form=15 and b.current_approval_status=1 $buyer_id_cond3 $seqCond
							union
							select distinct (mst_id) as pre_cost_id from wo_pre_cost_mst a, approval_history b, wo_po_details_master c where a.id=b.mst_id and a.job_no=c.job_no and c.company_name=$company_id and b.sequence_no in ($sequence_no_by_yes) and b.entry_form=15 and b.current_approval_status=1 $buyer_id_cond3";
							$bResult=sql_select($pre_cost_id_sql);
							foreach($bResult as $bRow)
							{
								$pre_cost_id.=$bRow->PRE_COST_ID.",";
							}
			
							$pre_cost_id=chop($pre_cost_id,',');
			
							$pre_cost_id_app_sql=sql_select("select b.mst_id as PRE_COST_ID from wo_pre_cost_mst a, approval_history b, wo_po_details_master c
							where a.id=b.mst_id and a.job_no=c.job_no and c.company_name=$company_id and b.sequence_no=$user_sequence_no and b.entry_form=15 and a.ready_to_approved=1 and b.current_approval_status=1");
			
							foreach($pre_cost_id_app_sql as $inf)
							{
								if($pre_cost_id_app_byuser!="") $pre_cost_id_app_byuser.=",".$inf->PRE_COST_ID;
								else $pre_cost_id_app_byuser.=$inf->PRE_COST_ID;
							}
			
							$pre_cost_id_app_byuser=implode(",",array_unique(explode(",",$pre_cost_id_app_byuser)));
						
			
						$pre_cost_id_app_byuser=chop($pre_cost_id_app_byuser,',');
						$result=array_diff(explode(',',$pre_cost_id),explode(',',$pre_cost_id_app_byuser));
						$pre_cost_id=implode(",",$result);
						//echo $pre_cost_id;die;
						$pre_cost_id_cond="";
			
						if($pre_cost_id_app_byuser!="")
						{
							$pre_cost_id_app_byuser_arr=explode(",",$pre_cost_id_app_byuser);
							if(count($pre_cost_id_app_byuser_arr)>995)
							{
								$pre_cost_id_app_byuser_chunk_arr=array_chunk(explode(",",$pre_cost_id_app_byuser),995) ;
								foreach($pre_cost_id_app_byuser_chunk_arr as $chunk_arr)
								{
									$chunk_arr_value=implode(",",$chunk_arr);
									$pre_cost_id_cond.=" and b.id not in($chunk_arr_value)";
								}
							}
							else
							{
								$pre_cost_id_cond=" and b.id not in($pre_cost_id_app_byuser)";
							}
						}
						else $pre_cost_id_cond="";
			
						$sql="select b.id,a.quotation_id,a.job_no_prefix_num,a.job_no, a.buyer_name, a.STYLE_REF_NO,b.costing_date,0 as approval_id,
						b.approved,b.inserted_by ,b.entry_from
						from wo_pre_cost_mst b,wo_po_details_master a,wo_po_break_down d
						where a.job_no=b.job_no and a.job_no=d.job_no_mst and a.company_name=$company_id and a.is_deleted=0 and a.status_active=1 and b.status_active=1
						and b.is_deleted=0 and b.ready_to_approved=1 and b.approved in (0,2) $buyer_id_cond $date_cond $pre_cost_id_cond $buyerIds_cond $buyer_id_cond2 $job_no_cond $file_no_cond $internal_ref_cond group by b.id,a.quotation_id,a.job_no_prefix_num,a.job_no, a.buyer_name, a.STYLE_REF_NO,b.costing_date,
						b.approved,b.inserted_by,b.entry_from ";
						//echo $sql;die;
						if($pre_cost_id!="")
						{
							$pre_cost_id_cond2="and ";
							$pre_cost_id_arr=explode(",",$pre_cost_id);
							if(count($pre_cost_id_arr)>995)
							{
								$pre_cost_id_cond2.=" ( ";
								$pre_cost_id_arr_chunk_arr=array_chunk(explode(",",$pre_cost_id),995) ;
								$slcunk=0;
								foreach($pre_cost_id_arr_chunk_arr as $chunk_arr)
								{
									if($slcunk>0) $pre_cost_id_cond2.=" or";
									$chunk_arr_value=implode(",",$chunk_arr);	
									$pre_cost_id_cond2.="  b.id  in($chunk_arr_value)";
									$slcunk++;	
								}
								$pre_cost_id_cond2.=" )";
							}
							else
							{
								$pre_cost_id_cond2.="  b.id  in($pre_cost_id)";	 
							}
							
							$sql.=" union all
							select b.id,a.quotation_id,a.job_no_prefix_num,a.job_no, a.buyer_name, a.STYLE_REF_NO,b.costing_date,0 as approval_id,
							b.approved,b.inserted_by,b.entry_from
							from wo_pre_cost_mst b,wo_po_details_master a,wo_po_break_down d
							where  a.job_no=b.job_no and a.job_no=d.job_no_mst and a.company_name=$company_id  and a.is_deleted=0 and a.status_active=1 and b.status_active=1
							and b.is_deleted=0 and b.ready_to_approved=1 and b.approved in(1,3) $pre_cost_id_cond2 $buyer_id_cond $buyer_id_cond2  $date_cond $job_no_cond $file_no_cond $internal_ref_cond group by b.id,a.quotation_id,a.job_no_prefix_num,a.job_no, a.buyer_name, a.STYLE_REF_NO,b.costing_date,
							b.approved,b.inserted_by,b.entry_from";
			
						}
					}
					else
					{
						$buyer_ids=$buyer_ids_array[$user_id]['u'];
						if($buyer_ids=="") $buyer_id_cond2=""; else $buyer_id_cond2=" and a.buyer_name in($buyer_ids)";
			
						$user_sequence_no=$user_sequence_no-1;
						if($sequence_no==$user_sequence_no) $sequence_no_by_pass='';
						else
						{
							if ($this->db->dbdriver == 'mysqli')
							{
								$sequence_no_by_pass=return_field_value("group_concat(sequence_no)","electronic_approval_setup","company_id=$company_id and page_id=$page_id and
								 sequence_no between $sequence_no and $user_sequence_no and is_deleted=0");// and bypass=1
							}
							else
							{
								$sequence_no_by_pass=return_field_value("LISTAGG(sequence_no, ',') WITHIN GROUP (ORDER BY sequence_no)
								 as sequence_no","electronic_approval_setup","company_id=$company_id and page_id=$page_id and sequence_no between $sequence_no and $user_sequence_no and is_deleted=0","sequence_no");//bypass=1 and
							}
						}
			
						if($sequence_no_by_pass==""){$sequence_no_cond=" and c.sequence_no='$sequence_no'";}
						else{$sequence_no_cond=" and c.sequence_no in ($sequence_no_by_pass)";}
			
						  $sql="select b.id,a.quotation_id,a.job_no_prefix_num,a.job_no, a.buyer_name, a.STYLE_REF_NO,b.costing_date, b.approved,b.inserted_by,
							  c.id as approval_id, c.sequence_no, c.approved_by,c.id as approval_id ,b.entry_from
							  from wo_pre_cost_mst b,  wo_po_details_master a,approval_history c,wo_po_break_down d
							  where b.id=c.mst_id and c.entry_form=15 and a.job_no=b.job_no and a.company_name=$company_id and a.job_no=d.job_no_mst  and a.is_deleted=0 and
							  a.status_active=1 and b.status_active=1 and c.current_approval_status=1  and
							  b.is_deleted=0 and b.approved in(1,3) $buyer_id_cond $buyer_id_cond2 $sequence_no_cond $date_cond $job_no_cond $file_no_cond $internal_ref_cond group by  b.id,a.quotation_id,a.job_no_prefix_num,a.job_no, a.buyer_name, a.STYLE_REF_NO,b.costing_date, b.approved,b.inserted_by,c.id, c.sequence_no, c.approved_by,b.entry_from "; //and b.ready_to_approved=1
					}
					//return $sql;
				
				}
			
				//return $sql;
				
				$nameArray=sql_select( $sql );
				foreach ($nameArray as $row)
				{
					
					if($select_no){	
						$dataArr[PAGE_ID]=$page_id;
						$dataArr[APP_TITLE]='Pre Cost Approval';
						$dataArr[COMPANY_ID]=$company_id;
						$dataArr[APP_ID]=$row->JOB_NO;
						$dataArr[MESSAGE1]=($row->STYLE_REF_NO)?$row->STYLE_REF_NO:'';
						$dataArr[MESSAGE2]=($row->COSTING_DATE)?$row->COSTING_DATE:'00-00-0000';
						$dataArr[MESSAGE3]=($buyer_arr[$row->BUYER_NAME])?$buyer_arr[$row->BUYER_NAME]:'';
						$dataArr[MESSAGE4]=($supplier_arr[$row->SUPPLIER_ID])?$supplier_arr[$row->SUPPLIER_ID]:'';
					}
					else
					{
						$dataArr[$i][PAGE_ID]=$page_id;
						$dataArr[$i][APP_TITLE]='Pre Cost Approval';
						$dataArr[$i][COMPANY_ID]=$company_id;
						$dataArr[$i][APP_ID]=$row->JOB_NO;
						
						
						//$dataArr[DTLS][$i][2][CAPTION]="STYLE REF NO";
						$dataArr[$i][MESSAGE1]=($row->STYLE_REF_NO)?$row->STYLE_REF_NO:'';
			
						//$dataArr[$i][3][CAPTION]="COSTING DATE";
						$dataArr[$i][MESSAGE2]=($row->COSTING_DATE)?$row->COSTING_DATE:'00-00-0000';
						
						//$dataArr[$i][4][CAPTION]="BUYER NAME";
						$dataArr[$i][MESSAGE3]=($buyer_arr[$row->BUYER_NAME])?$buyer_arr[$row->BUYER_NAME]:'';
						
						//$dataArr[$i][5][CAPTION]="SUPPLIER";
						$dataArr[$i][MESSAGE4]=($supplier_arr[$row->SUPPLIER_ID])?$supplier_arr[$row->SUPPLIER_ID]:'';
					}
					$i++; 
				}
				
			  }//user sequence empty check;
			}//end company loof;
			
				
				
	
		}//Pre Costing Approval if con;
		
		//OK
		//Price Quotation Approval.............................................................start;
		if($pq_page_id==427){
		
			$page_id=427;
			
			foreach($company_arr as $company_id=>$company_name){
			
				$user_sequence_no=$user_sequence_no_array[$page_id][$company_id];
				$min_sequence_no=$min_sequence_no_array[$page_id][$company_id];
				$maxUserNo=$max_sequence_no_array[$page_id][$company_id];
		
				$buyer_ids_array=$user_buyer_ids_array[$page_id][$company_id];
			
			
			
			
			  if($user_sequence_no !="")
			  {
			
				if($this->db->dbdriver == 'mysqli'){$year_cond="SUBSTRING_INDEX(a.insert_date, '-', 1) as year";}
				else{$year_cond="to_char(a.insert_date,'YYYY') as year";}
			
				if($select_no){$quotation_cond=" and a.id=$select_no";}
	
				$approval_type=0;
				if($approval_type==0)
				{
			
					if($this->db->dbdriver == 'mysqli')
					{
						$sequence_no = return_field_value("max(sequence_no) as seq","electronic_approval_setup","company_id=$company_id and page_id=$page_id and sequence_no<$user_sequence_no and bypass = 2 and buyer_id='' and is_deleted=0","seq");
					}
					else
					{
						$sequence_no = return_field_value("max(sequence_no) as seq","electronic_approval_setup","company_id=$company_id and page_id=$page_id and sequence_no<$user_sequence_no and bypass=2 and buyer_id is null and is_deleted=0","seq");
					}
			
					if($user_sequence_no==$min_sequence_no)
					{
			
						$buyer_ids = $buyer_ids_array[$user_id]['u'];
						if($buyer_ids=="") $buyer_id_cond2=""; else $buyer_id_cond2=" and a.BUYER_ID in($buyer_ids)";
			 
						$sql="SELECT a.ID,  a.company_id,  a.BUYER_ID, a.style_ref, a.STYLE_REF, a.QUOT_DATE,a.est_ship_date, '0' as approval_id,  a.approved, a.inserted_by, a.garments_nature, a.mkt_no  from wo_price_quotation a, wo_price_quotation_costing_mst b where a.id=b.quotation_id and a.company_id=$company_id and a.is_deleted=0 and a.status_active=1 and b.status_active=1 and b.is_deleted=0 and a.ready_to_approved=1 and a.approved=$approval_type $buyer_id_cond $buyer_id_cond2 $date_cond $quotation_cond $mkt_no_cond group by a.ID,  a.company_id,  a.BUYER_ID , a.garments_nature, a.style_ref, a.STYLE_REF, a.QUOT_DATE, a.est_ship_date, a.approved, a.inserted_by, a.mkt_no order by a.id ASC";
					}
					else if($sequence_no=="")
					{
			
						$buyer_ids=$buyer_ids_array[$user_id]['u'];
						if($buyer_ids=="") $buyer_id_cond2=""; else $buyer_id_cond2=" and a.BUYER_ID in($buyer_ids)";
			
						$seqSql="select SEQUENCE_NO, BYPASS, BUYER_ID from electronic_approval_setup where company_id=$company_id and page_id=$page_id and sequence_no<$user_sequence_no and is_deleted=0 order by sequence_no desc";
						$seqData=sql_select($seqSql);
			
						$buyerIds=''; $sequence_no_by_yes=''; $sequence_no_by_no=''; $approved_string=''; $check_buyerIds_arr=array();
						foreach($seqData as $sRow)
						{
							if($sRow->BYPASS==2)
							{
								$sequence_no_by_no.=$sRow->SEQUENCE_NO.",";
								if($sRow->BUYER_ID!="")
								{
									$buyerIds.=$sRow->BUYER_ID.",";
			
									$buyer_id_arr=explode(",",$sRow->BUYER_ID);
									$result=array_diff($buyer_id_arr,$check_buyerIds_arr);
									if(count($result)>0)
									{
										$query_string.=" (b.sequence_no=".$sRow->SEQUENCE_NO." and a.BUYER_ID in(".implode(",",$result).")) or ";
									}
									$check_buyerIds_arr=array_unique(array_merge($check_buyerIds_arr,$buyer_id_arr));
								}
							}
							else
							{
								$sequence_no_by_yes.=$sRow->SEQUENCE_NO.",";
							}
						}
						$buyerIds=chop($buyerIds,',');
						if($buyerIds=="")
						{
							$buyerIds_cond="";
							$seqCond="";
						}
						else
						{
							$buyerIds_cond=" and a.BUYER_ID not in($buyerIds)";
							$seqCond=" and (".chop($query_string,'or ').")";
						}
						//echo $seqCond;die;
						$sequence_no_by_no=chop($sequence_no_by_no,',');
						$sequence_no_by_yes=chop($sequence_no_by_yes,',');
			
						if($sequence_no_by_yes=="") $sequence_no_by_yes=0;
						if($sequence_no_by_no=="") $sequence_no_by_no=0;
						if($select_no){$quotation_cond=" and mst_id=$select_no";}
						 
						$quotation_id_sql="select distinct (mst_id) as QUOTATION_ID from wo_price_quotation a, approval_history b where a.id=b.mst_id and a.company_id=$company_id  and b.sequence_no in ($sequence_no_by_no) and b.entry_form=10 and b.current_approval_status=1 $quotation_cond $buyer_id_cond $date_cond $seqCond
						union
						select distinct (mst_id) as quotation_id from wo_price_quotation a, approval_history b where a.id=b.mst_id and a.company_id=$company_id and b.sequence_no in ($sequence_no_by_yes) and b.entry_form=10 and b.current_approval_status=1 $quotation_cond $buyer_id_cond $date_cond";
						
						
						
						$bResult=sql_select($quotation_id_sql);
						foreach($bResult as $bRow)
						{
							$quotation_id.=$bRow->QUOTATION_ID.",";
						}
			
						$quotation_id=chop($quotation_id,',');
			
			
						$quotation_id_app_sql=sql_select(" select mst_id as QUOTATION_ID from wo_price_quotation a, approval_history b where a.id=b.mst_id and a.company_id=$company_id  and b.sequence_no=$user_sequence_no and b.entry_form=10 and b.current_approval_status=1 $quotation_cond ");
			
						foreach($quotation_id_app_sql as $inf)
						{
							if($quotation_id_app_byuser!="") $quotation_id_app_byuser.=",".$inf->QUOTATION_ID;
							else $quotation_id_app_byuser.=$inf->QUOTATION_ID;
						}
			
						$quotation_id_app_byuser=implode(",",array_unique(explode(",",$booking_id_app_byuser)));
						$quotation_id_app_byuser=chop($quotation_id_app_byuser,',');
						$result=array_diff(explode(',',$quotation_id),explode(',',$quotation_id_app_byuser));
						$quotation_id=implode(",",$result);
			
						$quotation_id_cond="";
						if($quotation_id_app_byuser!="")
						{
							$quotation_id_app_byuser_arr=explode(",",$quotation_id_app_byuser);
							if( count($quotation_id_app_byuser_arr)>999)
							{
								$quotation_id_chunk_arr=array_chunk($quotation_id_app_byuser_arr,999) ;
								foreach($quotation_id_chunk_arr as $chunk_arr)
								{
									$chunk_arr_value=implode(",",$chunk_arr);
									$quotation_id_cond.=" and a.id not in($chunk_arr_value)";
								}
			
							}
							else
							{
								$quotation_id_cond=" and a.id not in($quotation_id_app_byuser)";
							}
						}
						else $quotation_id_cond="";
						
						if($select_no){$quotation_cond=" and a.id=$select_no";}
			
						if($quotation_id!="")
						{
							$sql="select a.ID,  a.company_id,  a.BUYER_ID, a.style_ref, a.STYLE_REF, a.QUOT_DATE,a.est_ship_date, '0' as approval_id, a.approved, a.inserted_by, a.garments_nature, a.mkt_no from wo_price_quotation a, wo_price_quotation_costing_mst b where a.id=b.quotation_id and a.company_id=$company_id  and a.is_deleted=0 and a.status_active=1 and b.status_active=1 and b.is_deleted=0 and a.ready_to_approved=1 and a.approved=$approval_type $quotation_id_cond $buyerIds_cond $buyer_id_cond $buyer_id_cond2 $date_cond $quotation_cond $mkt_no_cond group by a.ID, a.company_id, a.BUYER_ID, a.garments_nature, a.style_ref, a.STYLE_REF, a.QUOT_DATE,a.est_ship_date, a.approved, a.inserted_by, a.mkt_no
							UNION ALL
							SELECT a.ID, a.company_id, a.BUYER_ID, a.style_ref, a.STYLE_REF, a.QUOT_DATE,a.est_ship_date, '0' as approval_id, a.approved, a.inserted_by, a.garments_nature, a.mkt_no from wo_price_quotation a, wo_price_quotation_costing_mst b where a.id=b.quotation_id and a.company_id=$company_id  and a.is_deleted=0 and a.status_active=1 and b.status_active=1 and b.is_deleted=0 and a.ready_to_approved=1 and a.approved in (1,3) and (a.id in($quotation_id))  $buyer_id_cond $buyer_id_cond2 $date_cond $quotation_cond $mkt_no_cond group by a.ID, a.company_id, a.BUYER_ID,a.garments_nature, a.style_ref, a.STYLE_REF, a.QUOT_DATE,a.est_ship_date, a.approved , a.inserted_by, a.mkt_no ";
							
						}
						else
						{
							$sql="SELECT a.ID, a.company_id, a.BUYER_ID, a.style_ref, a.STYLE_REF, a.QUOT_DATE,a.est_ship_date, '0' as approval_id, a.approved, a.inserted_by, a.garments_nature, a.mkt_no from wo_price_quotation a, wo_price_quotation_costing_mst b where a.id=b.quotation_id and a.company_id=$company_id  and a.is_deleted=0 and a.status_active=1 and b.status_active=1 and b.is_deleted=0 and a.ready_to_approved=1 and a.approved=$approval_type  $quotation_id_cond $buyerIds_cond $buyer_id_cond $buyer_id_cond2  $date_cond $quotation_cond $mkt_no_cond group by a.ID, a.company_id, a.BUYER_ID, a.garments_nature, a.style_ref, a.STYLE_REF, a.QUOT_DATE,a.est_ship_date, a.approved , a.inserted_by, a.mkt_no order by a.id ASC";
						}
						//return $sql;
					}
					else
					{
			
						$buyer_ids=$buyer_ids_array[$user_id]['u'];
						if($buyer_ids=="") $buyer_id_cond2=""; else $buyer_id_cond2=" and a.BUYER_ID in($buyer_ids)";
						$user_sequence_no=$user_sequence_no-1;
			
						if($sequence_no==$user_sequence_no) $sequence_no_by_pass='';
						else
						{
							if($this->db->dbdriver == 'mysqli')
							{
								$sequence_no_by_pass=return_field_value("group_concat(sequence_no)","electronic_approval_setup","company_id=$company_id and page_id=$page_id and sequence_no between $sequence_no and $user_sequence_no and is_deleted=0");
							}
							else
							{
								$sequence_no_by_pass=return_field_value("LISTAGG(sequence_no, ',') WITHIN GROUP (ORDER BY sequence_no) as sequence_no","electronic_approval_setup","company_id=$company_id and page_id=$page_id and sequence_no between $sequence_no and $user_sequence_no and is_deleted=0","sequence_no");
							}
						}
			
						if($sequence_no_by_pass=="") $sequence_no_cond=" and b.sequence_no='$sequence_no'";
						else $sequence_no_cond=" and b.sequence_no in ($sequence_no_by_pass)";
			
						$sql="SELECT a.ID,  a.company_id,  a.BUYER_ID,  a.style_ref, a.STYLE_REF, a.QUOT_DATE,a.est_ship_date, a.approved,a.inserted_by,b.approved_date, b.id as approval_id, a.garments_nature, a.mkt_no from wo_price_quotation a, approval_history b where a.id=b.mst_id and b.entry_form=10 and a.company_id=$company_id  and a.status_active=1 and a.is_deleted=0 and b.current_approval_status=1 and a.ready_to_approved=1 and  a.approved in(1,3) $buyer_id_cond $buyer_id_cond2 $sequence_no_cond $date_cond $quotation_cond $mkt_no_cond group by a.ID,  a.company_id,  a.BUYER_ID,  a.style_ref, a.STYLE_REF, a.QUOT_DATE,a.est_ship_date, a.approved,a.inserted_by,b.approved_date, b.ID, a.garments_nature, a.mkt_no order by a.id ASC";
					}
					
				
				}

			
			
				$nameArray=sql_select( $sql );
				foreach ($nameArray as $row)
				{
					
					if($select_no){	
						$dataArr[PAGE_ID]=$page_id;
						$dataArr[APP_TITLE]='Fabric Bookin Approval';
						$dataArr[COMPANY_ID]=$company_id;
						$dataArr[APP_ID]=$row->ID;
						$dataArr[MESSAGE1]=($row->STYLE_REF)?$row->STYLE_REF:'';
						$dataArr[MESSAGE2]=($row->QUOT_DATE)?$row->QUOT_DATE:'00-00-0000';
						$dataArr[MESSAGE3]=($buyer_arr[$row->BUYER_ID])?$buyer_arr[$row->BUYER_ID]:'';
						$dataArr[MESSAGE4]='';
					}
					else
					{
						$dataArr[$i][PAGE_ID]=$page_id;
						$dataArr[$i][APP_TITLE]='Fabric Bookin Approval';
						$dataArr[$i][COMPANY_ID]=$company_id;
						$dataArr[$i][APP_ID]=$row->ID;
						
						//$dataArr[DTLS][$i][2][CAPTION]="STYLE REF NO";
						$dataArr[$i][MESSAGE1]=($row->STYLE_REF)?$row->STYLE_REF:'';
			
						//$dataArr[DTLS][$i][3][CAPTION]="QUOTATION DATE";
						$dataArr[$i][MESSAGE2]=($row->QUOT_DATE)?$row->QUOT_DATE:'00-00-0000';
						
						//$dataArr[DTLS][$i][4][CAPTION]="BUYER NAME";
						$dataArr[$i][MESSAGE3]=($buyer_arr[$row->BUYER_ID])?$buyer_arr[$row->BUYER_ID]:'';
						
						//$dataArr[DTLS][$i][5][CAPTION]="";
						$dataArr[$i][MESSAGE4]='';
					}
					$i++; 
					
				}
		
			  }//end user seq empty check;
			}//end company loof;
			
			
		
		
	
			
		}//Price Quotation Approval if con;
		
		//Gate Pass Activation Approval.............................................................start;
		if($gp_page_id==670){
			$page_id=670;
			$approval_type=0;
			foreach($company_arr as $company_id=>$company_name){
			
				$user_sequence_no=$user_sequence_no_array[$page_id][$company_id];
				$min_sequence_no=$min_sequence_no_array[$page_id][$company_id];
				//$maxUserNo=$max_sequence_no_array[$page_id][$company_id];
		
				//$buyer_ids_array=$user_buyer_ids_array[$page_id][$company_id];
	
	
				
				
				if($select_no) $gate_pass_cond="and a.id=$select_no";
				
				//return $gate_pass_cond;
				
				//if($get_pass_basis!=0) $basis_cond="and a.basis='$get_pass_basis' ";else $basis_cond="";
				
				$sql_gate=sql_select("select GATE_PASS_ID from inv_gate_out_scan where  gate_pass_id is not null and status_active=1 and is_deleted=0");
				$gate_out_id_arr=array();
				foreach($sql_gate as $row)
				{
					$gate_out_id_arr[$row->GATE_PASS_ID]=$row->GATE_PASS_ID;
				}
				$gate_out_id=implode(',',$gate_out_id_arr);
				
				
				$gate_out_ids=count($gate_out_id_arr);
	
					//print_r($gate_outIds);
					if($gate_out_id!='' || $gate_out_id!=0)
					{
						if($this->db->dbdriver != 'mysqli' && $gate_out_ids>999)
						{
							$outIds_cond=" and (";
							$outIdsArr=array_chunk($gate_out_id_arr,999);
							foreach($outIdsArr as $ids)
							{
								$ids=implode(",",$ids);
								$outIds_cond.=" a.sys_number not in($ids) or ";
							}
							$outIds_cond=chop($outIds_cond,'or ');
							$outIds_cond.=")";
						}
						else
						{
							$outIds_cond=" and  a.sys_number not in($gate_out_id)";
						}
					}
				
			//return $outIds_cond;
			
				$sql_job=sql_select("select b.ID,a.buyer_name,a.job_no_prefix_num, a.job_no from wo_po_details_master a,  wo_po_break_down b where a.job_no=b.job_no_mst");
			foreach($sql_job as $row)
			{
				$buyer_po_arr[$row->ID]['buyer_name']=$row->BUYER_NAME;
				$buyer_po_arr[$row->ID]['job_no_prefix_num']=$row->JOB_NO;
			}
			
			$po_array=array();
			if($this->db->dbdriver == 'mysqli')
			{
				$po_array=return_library_array("select a.mst_id, group_concat(b.po_breakdown_id) as po_breakdown_id from inv_transaction a, order_wise_pro_details b where a.id=b.trans_id and a.item_category=1 and a.transaction_type=2 and b.entry_form=3 and a.is_deleted=0 and a.status_active=1 and b.status_active=1 and b.is_deleted=0 group by a.mst_id", "mst_id", "po_breakdown_id" );
			}
			else
			{
				$po_array=return_library_array("select a.mst_id, LISTAGG(CAST( b.po_breakdown_id  AS VARCHAR(4000)), ',') WITHIN GROUP (ORDER BY b.po_breakdown_id) as po_breakdown_id from inv_transaction a, order_wise_pro_details b where a.id=b.trans_id and a.item_category=1 and a.transaction_type=2 and b.entry_form=3 and a.is_deleted=0 and a.status_active=1 and b.status_active=1 and b.is_deleted=0 group by a.mst_id", "mst_id", "po_breakdown_id" );
			}
			
			 //$user_sequence_no=return_field_value("sequence_no","electronic_approval_setup","company_id=$company_id and page_id=$page_id and user_id=$user_id and is_deleted=0");
			 //$min_sequence_no=return_field_value("min(sequence_no) as sequence_no","electronic_approval_setup","company_id=$company_id and page_id=$page_id and is_deleted=0","sequence_no");
			
			
				if($apprsoval_type==0 && $user_sequence_no !="")
				{
					$sequence_no=return_field_value("max(sequence_no) as sequence_no","electronic_approval_setup","company_id=$company_id and page_id=$page_id and sequence_no<$user_sequence_no and bypass=2 and is_deleted=0","sequence_no");
					if($user_sequence_no==$min_sequence_no)
					{
						$sql="select a.ID,a.time_hour,a.time_minute,  a.within_group,a.SYS_NUMBER,a.CHALLAN_NO, a.sent_to,a.challan_no, a.company_id,a.OUT_DATE,'0' as approval_id from  inv_gate_pass_mst a, inv_gate_pass_dtls b where a.id=b.mst_id  and a.company_id=$company_id and a.is_approved=$approval_type  and a.is_deleted=0 and a.status_active=1 and b.status_active=1 and b.is_deleted=0  and  a.sys_number not in(select gate_pass_id from inv_gate_out_scan where  gate_pass_id is not null and status_active=1 and is_deleted=0) $basis_cond $gate_pass_cond $date_cond $gatepass_year_cond  group by a.id,a.insert_date,a.time_hour,a.time_minute, a.within_group,a.challan_no, a.sent_to,a.SYS_NUMBER,a.challan_no, a.company_id,a.out_date order by a.id desc";
					}
					else if($sequence_no=="")
					{
						
						if($this->db->dbdriver == 'mysqli')
						{
							$group_concat="group_concat(sequence_no) ";
							$group_concat2="group_concat(mst_id) ";
						}
						else
						{
							$group_concat="LISTAGG(CAST( sequence_no  AS VARCHAR(4000)), ',') WITHIN GROUP (ORDER BY sequence_no) as sequence_no ";
							$group_concat2="LISTAGG(CAST( b.mst_id  AS VARCHAR(4000)), ',') WITHIN GROUP (ORDER BY b.mst_id) as quotation_id";
						}
						$quotation_id_app_byuser=return_field_value("$group_concat2","inv_gate_pass_mst a, approval_history b","a.id=b.mst_id and a.company_id=$company_id  and b.sequence_no=$user_sequence_no and b.entry_form=19 and b.current_approval_status=1","quotation_id");
						
						if($quotation_id_app_byuser!="") $quotation_id_cond=" and a.id not in($quotation_id_app_byuser)";
						else if($quotation_id!="") $quotation_id_cond.=" or (a.id in($quotation_id))";
						else $quotation_id_cond="";
						
							$sequence_no_by=return_field_value("$group_concat ","electronic_approval_setup","company_id=$company_id and page_id=$page_id and sequence_no<$user_sequence_no and bypass=1 and is_deleted=0","sequence_no");
							if($sequence_no_by)
							{
								$quotation_id=return_field_value("$group_concat2","inv_gate_pass_mst a, approval_history b","a.id=b.mst_id and a.company_id=$company_id  and b.sequence_no in ($sequence_no_by) and b.entry_form=19 and b.current_approval_status=1","quotation_id");
							
							}
						
							$quotation_id_app_byuser=implode(",",array_unique(explode(",",$quotation_id_app_byuser)));
							
							   $sql="select a.ID,a.time_hour,a.time_minute,  a.SYS_NUMBER,a.CHALLAN_NO, a.sent_to,a.challan_no, a.company_id,a.OUT_DATE,'0' as approval_id from  inv_gate_pass_mst a, inv_gate_pass_dtls b where a.id=b.mst_id and a.company_id=$company_id  and a.is_deleted=0 and a.status_active=1 and b.status_active=1 and b.is_deleted=0  and a.is_approved=$approval_type   and  a.sys_number not in(select gate_pass_id from inv_gate_out_scan where  gate_pass_id is not null and status_active=1 and is_deleted=0) $basis_cond $gate_pass_cond $date_cond  $quotation_id_cond $gatepass_year_cond group by a.id,a.insert_date,a.time_hour,a.time_minute, a.within_group,a.SYS_NUMBER,a.challan_no, a.sent_to,a.challan_no, a.company_id,a.out_date order by a.id desc";
						
					}
					else
					{
						$user_sequence_no=$user_sequence_no-1;
						
						if($sequence_no==$user_sequence_no) $sequence_no_by_pass='';
						else
						{
							if($this->db->dbdriver == 'mysqli')
							{
								$sequence_no_by_pass=return_field_value("group_concat(sequence_no)","electronic_approval_setup","company_id=$company_id and page_id=$page_id and sequence_no between $sequence_no and $user_sequence_no and bypass=1 and is_deleted=0");
							}
							else
							{
								$sequence_no_by_pass=return_field_value("LISTAGG(CAST( sequence_no  AS VARCHAR(4000)), ',') WITHIN GROUP (ORDER BY sequence_no) as sequence_no","electronic_approval_setup","company_id=$company_id and page_id=$page_id and sequence_no between $sequence_no and $user_sequence_no and bypass=1 and is_deleted=0","sequence_no");	
							}
						}
						
						if($sequence_no_by_pass=="") $sequence_no_cond=" and c.sequence_no='$sequence_no'";
						else $sequence_no_cond=" and (c.sequence_no='$sequence_no' or c.sequence_no in ($sequence_no_by_pass))";
					
						$sql="select a.ID,a.time_hour,a.time_minute, a.SYS_NUMBER, a.CHALLAN_NO, a.sent_to, a.company_id,a.OUT_DATE, a.is_approved   from  inv_gate_pass_mst a,  approval_history c, inv_gate_pass_dtls b where a.id=b.mst_id and a.id=c.mst_id  and a.company_id=$company_id  and a.is_deleted=0 and a.status_active=1 and b.status_active=1 and b.is_deleted=0 and c.entry_form=19 and a.is_approved=1  and  a.sys_number not in(select gate_pass_id from inv_gate_out_scan where  gate_pass_id is not null and status_active=1 and is_deleted=0) $basis_cond $gate_pass_cond $date_cond $gatepass_year_cond group by a.id,a.insert_date,a.time_hour,a.time_minute,  a.challan_no, a.sent_to,a.SYS_NUMBER, a.company_id,a.out_date, a.is_approved order by a.id desc";
						
							
					}
				}
			
				$nameArray=sql_select( $sql );
				foreach ($nameArray as $row)
				{
					if($select_no){	
						$dataArr[PAGE_ID]=$page_id;
						$dataArr[APP_TITLE]='Gate Pass Activation Approval';
						$dataArr[COMPANY_ID]=$company_id;
						$dataArr[APP_ID]=$row->ID;
						$dataArr[MESSAGE1]=$row->CHALLAN_NO;
						$dataArr[MESSAGE2]=$row->SYS_NUMBER;
						$dataArr[MESSAGE3]=$row->OUT_DATE;
						$dataArr[MESSAGE4]='';
					}
					else
					{
						$dataArr[$i][PAGE_ID]=$page_id;
						$dataArr[$i][APP_TITLE]='Gate Pass Activation Approval';
						$dataArr[$i][COMPANY_ID]=$company_id;
						$dataArr[$i][APP_ID]=$row->ID;
						$dataArr[$i][MESSAGE1]=$row->CHALLAN_NO;
						$dataArr[$i][MESSAGE2]=$row->SYS_NUMBER;
						$dataArr[$i][MESSAGE3]=$row->OUT_DATE;
						$dataArr[$i][MESSAGE4]='';
						$i++;
					}
				}
			}//end company
			
			
		}//Gate Pass Activation Approval if con;
		
		//GSD Approval...........................................................................start;
		if($gsd_page_id==850){
				
			foreach($company_arr as $company_id=>$company_name){
			
				$page_id=850;
				$approval_type=0;
				
				$user_sequence_no=$user_sequence_no_array[$page_id][$company_id];
				$min_sequence_no=$min_sequence_no_array[$page_id][$company_id];
				$maxUserNo=$max_sequence_no_array[$page_id][$company_id];
				$buyer_ids_array=$user_buyer_ids_array[$page_id][$company_id];
				
				
		
				
				if($select_no){$select_no_cond=" and a.id='$select_no'";}
				
				if($approval_type==0 && $user_sequence_no !="") // Un-Approve
				{        
			
					if($this->db->dbdriver == 'mysqli')
					{
						$sequence_no=return_field_value("max(sequence_no) as seq","electronic_approval_setup","company_id=$company_id and page_id=$page_id and sequence_no<$user_sequence_no and bypass=2 and buyer_id='' and is_deleted=0","seq");
					}
					else
					{
						$sequence_no=return_field_value("max(sequence_no) as seq","electronic_approval_setup","company_id=$company_id and page_id=$page_id and sequence_no<$user_sequence_no and bypass=2 and buyer_id is null and is_deleted=0","seq");                      
					}
			
					
					if($user_sequence_no==$min_sequence_no)
					{				
						$buyer_ids = $buyer_ids_array[$user_id]['u'];
						if($buyer_ids == "") $buyer_id_cond = ""; else $buyer_id_cond = " and a.buyer_id in($buyer_ids)";			
						$sql = "select a.id,a.company_id,a.BUYER_ID,b.BUYER_NAME,a.SYSTEM_NO,a.PO_JOB_NO,a.STYLE_REF,a.gmts_item_id,a.is_approved,'0' as approval_id from ppl_gsd_entry_mst a, wo_po_details_master b where a.PO_JOB_NO = b.job_no and a.company_id = $company_id and a.is_deleted=0 and a.status_active=1 and a.ready_to_approved = 1 and a.is_approved=$approval_type $buyer_id_cond $buyer_id_cond2 $buyerIds_cond $select_no_cond group by a.id,a.company_id,a.buyer_id,a.SYSTEM_NO,a.PO_JOB_NO,a.STYLE_REF,a.gmts_item_id,a.is_approved,a.update_date, a.insert_date,b.BUYER_NAME order by  a.insert_date desc";	              
					 
					}
		   
					else if($sequence_no == "")
					{              
						$buyer_ids=$buyer_ids_array[$user_id]['u'];
						if($buyer_ids == "") $buyer_id_cond = ""; else $buyer_id_cond = " and a.buyer_id in($buyer_ids)";
						
						if($this->db->dbdriver == 'mysqli')
						{
							$seqSql="select group_concat(sequence_no) as sequence_no_by,
			 group_concat(case when bypass=2 and buyer_id<>'' then buyer_id end) as buyer_ids from electronic_approval_setup where company_id=$company_id and page_id=$page_id and sequence_no<$user_sequence_no and is_deleted=0";
							$seqData=sql_select($seqSql);
							
							$sequence_no_by = $seqData[0]->sequence_no_by;
							$buyerIds = $seqData[0]->buyer_ids;
							
							if($buyerIds == "") $buyerIds_cond = ""; else $buyerIds_cond = " and a.buyer_id not in($buyerIds)";
						}
						else
						{   
							$seqSql="select LISTAGG(sequence_no, ',') WITHIN GROUP (ORDER BY sequence_no) as sequence_no_by, LISTAGG(case when bypass=2 and buyer_id is not null then buyer_id end, ',') WITHIN GROUP (ORDER BY null) as buyer_ids from electronic_approval_setup where company_id=$company_id and page_id=$page_id and sequence_no<$user_sequence_no and is_deleted=0";
							$seqData=sql_select($seqSql);
							
							$sequence_no_by=$seqData[0]->sequence_no_by;
							$buyerIds=$seqData[0]->buyer_ids;
							
							if($buyerIds == "") $buyerIds_cond = ""; else $buyerIds_cond = " and a.buyer_id not in($buyerIds)";				
						}
			
						if($this->db->dbdriver == 'mysqli')
						{	                
							$sql = "select a.id,a.company_id,a.BUYER_ID,b.BUYER_NAME,a.SYSTEM_NO,a.PO_JOB_NO,a.STYLE_REF,a.gmts_item_id,a.is_approved,'0' as approval_id from ppl_gsd_entry_mst a, wo_po_details_master b where a.PO_JOB_NO = b.job_no and a.company_id = $company_id and a.is_deleted=0 and a.status_active=1 and a.ready_to_approved = 1 and a.is_approved=$approval_type $buyer_id_cond $buyer_id_cond2 $buyerIds_cond $select_no_cond group by a.id,a.company_id,a.buyer_id,a.SYSTEM_NO,a.PO_JOB_NO,a.STYLE_REF,a.gmts_item_id,a.is_approved,a.update_date, a.insert_date,b.BUYER_NAME order by a.insert_date desc";	              
						}
						else
						{                   
							$sql = "select a.id,a.company_id,a.BUYER_ID,b.BUYER_NAME,a.SYSTEM_NO,a.PO_JOB_NO,a.STYLE_REF,a.gmts_item_id from ppl_gsd_entry_mst a, wo_po_details_master b where a.PO_JOB_NO = b.job_no and a.company_id = $company_id and a.is_deleted=0 and a.status_active=1 and a.ready_to_approved = 1  $buyer_id_cond $buyer_id_cond2 $buyerIds_cond $select_no_cond group by a.id,a.company_id,a.buyer_id,a.SYSTEM_NO,a.PO_JOB_NO,a.STYLE_REF,a.gmts_item_id,a.is_approved,a.update_date, a.insert_date,b.BUYER_NAME order by a.insert_date desc";	                             
						}
			
					}
					else
					{   
						$buyer_ids = $buyer_ids_array[$user_id]['u'];
						if($buyer_ids == "") { 
							$buyer_id_cond="";                 
						} else {
							$buyer_id_cond=" and a.buyer_id in($buyer_ids)";
						}
								  
						$user_sequence_no=$user_sequence_no-1;
						
						$sequence_no_by_pass=''; // understand 
						if($sequence_no == $user_sequence_no) 
						{                  
						   
							if($this->db->dbdriver == 'mysqli')
							{
								$sequence_no_by_pass=return_field_value("group_concat(sequence_no)","electronic_approval_setup","company_id=$company_id and page_id=$page_id and sequence_no between $sequence_no and $user_sequence_no and is_deleted=0");
							}
							else
							{
								$sequence_no_by_pass=return_field_value("LISTAGG(sequence_no, ',') WITHIN GROUP (ORDER BY sequence_no) as sequence_no","electronic_approval_setup","company_id=$company_id and page_id=$page_id and sequence_no between $sequence_no and $user_sequence_no and is_deleted=0","sequence_no");	
							}
							
							if($sequence_no_by_pass=="") {
								$sequence_no_cond=" and c.sequence_no='$sequence_no'";    
							}
							else {
								$sequence_no_cond=" and c.sequence_no in ($sequence_no_by_pass)";
							}
							$sql = "select a.id,a.company_id,a.buyer_id,b.BUYER_NAME,a.SYSTEM_NO,a.PO_JOB_NO,a.STYLE_REF,a.gmts_item_id,a.is_approved, c.id as approval_id, c.sequence_no, c.approved_by from ppl_gsd_entry_mst a, wo_po_details_master b, approval_history c where a.id=c.mst_id and c.entry_form=23 and c.current_approval_status=1 and a.PO_JOB_NO = b.job_no and a.company_id = $company_id and a.is_deleted=0 and a.status_active=1 and a.ready_to_approved = 1 and a.is_approved=1 $buyer_id_cond $buyer_id_cond2 $sequence_no_cond $select_no_cond group by a.id,a.company_id,a.buyer_id,a.SYSTEM_NO,a.PO_JOB_NO,a.STYLE_REF,a.gmts_item_id,a.is_approved,a.update_date, a.insert_date,b.BUYER_NAME,c.id, c.sequence_no, c.approved_by order by a.insert_date desc";	                
			
						} else {
							$sql = "select a.id,a.company_id,a.buyer_id,b.BUYER_NAME,a.SYSTEM_NO,a.PO_JOB_NO,a.STYLE_REF,a.gmts_item_id,a.is_approved, c.id as approval_id, c.sequence_no, c.approved_by from ppl_gsd_entry_mst a, wo_po_details_master b, approval_history c where a.id=c.mst_id and c.entry_form=23 and c.current_approval_status=1 and a.PO_JOB_NO = b.job_no and a.company_id = $company_id and a.is_deleted=0 and a.status_active=1 and a.ready_to_approved = 1 and a.is_approved=1 $buyer_id_cond $buyer_id_cond2 $sequence_no_cond $select_no_cond group by a.id,a.company_id,a.buyer_id,a.SYSTEM_NO,a.PO_JOB_NO,a.STYLE_REF,a.gmts_item_id,a.is_approved,a.update_date, a.insert_date,b.BUYER_NAME,c.id, c.sequence_no, c.approved_by order by a.insert_date desc";	                
						}
					}
					
					// echo $sql;die; 
					
				
					$nameArray=sql_select( $sql );
					foreach ($nameArray as $row)
					{
						
						if($select_no){	
							$dataArr[PAGE_ID]=$page_id;
							$dataArr[APP_TITLE]='GSD Approval';
							$dataArr[COMPANY_ID]=$company_id;
							$dataArr[APP_ID]=$row->ID;
							$dataArr[MESSAGE1]=($row->STYLE_REF)?$row->STYLE_REF:'';
							$dataArr[MESSAGE2]=$row->PO_JOB_NO;
							$dataArr[MESSAGE3]=($buyer_arr[$row->BUYER_NAME])?$buyer_arr[$row->BUYER_NAME]:'';
							$dataArr[MESSAGE4]='';
						}
						else
						{
							$dataArr[$i][PAGE_ID]=$page_id;
							$dataArr[$i][APP_TITLE]='GSD Approval';
							$dataArr[$i][COMPANY_ID]=$company_id;
							$dataArr[$i][APP_ID]=$row->ID;
							
							//$dataArr[DTLS][$i][2][CAPTION]="STYLE REF NO";
							$dataArr[$i][MESSAGE1]=($row->STYLE_REF)?$row->STYLE_REF:'';
				
							//$dataArr[DTLS][$i][3][CAPTION]="QUOTATION DATE";
							$dataArr[$i][MESSAGE2]=$row->PO_JOB_NO;
							
							//$dataArr[DTLS][$i][4][CAPTION]="BUYER NAME";
							$dataArr[$i][MESSAGE3]=($buyer_arr[$row->BUYER_NAME])?$buyer_arr[$row->BUYER_NAME]:'';
							
							//$dataArr[DTLS][$i][5][CAPTION]="";
							$dataArr[$i][MESSAGE4]='';
						}
						$i++; 
						
					}
				
				
				}
				
		
				
			}//end company foreach;
			
			
		}//GSD Approval if con;
		
		//Purchase Requisition Approval..........................................................start;
		if($pr_page_id==813){
			
			$page_id=813;
			$approval_type=0;
			
			
			foreach($company_arr as $company_id=>$company_name){
				$user_sequence_no=$user_sequence_no_array[$page_id][$company_id];
				$min_sequence_no=$min_sequence_no_array[$page_id][$company_id];
				//$maxUserNo=$max_sequence_no_array[$page_id][$company_id];
				//$buyer_ids_array=$user_buyer_ids_array[$page_id][$company_id];
			
				
				if($approval_type==0 && $user_sequence_no !=""){ // Un-Approve
					
					
					$permitted_item_category=return_field_value("item_cate_id","user_passwd","id=".$user_id."");
					if($permitted_item_category)
					{
						$item_category_con_b="and b.item_category in ($permitted_item_category)";
						$item_category_con_c="and c.item_category in ($permitted_item_category)";
					}
					
					
					$item_category_id=$permitted_item_category;			
					
					
					if($user_sequence_no==$min_sequence_no)// First user
					{
						
						if($this->db->dbdriver == 'mysqli')
						{
							$select_item_cat = "group_concat(b.item_category) as item_category_id ";
						}else{
							$select_item_cat = "listagg(b.item_category, ',') within group (order by b.item_category) as item_category_id ";
						}
			
							$sql ="SELECT a.ID,a.remarks, a.company_id, a.REQU_NO, a.requ_prefix_num , $select_item_cat, a.REQUISITION_DATE, a.delivery_date, 0 as approval_id, a.is_approved 
							from inv_purchase_requisition_mst a, inv_purchase_requisition_dtls b
							where a.id = b.mst_id and a.company_id=$company_id $item_category_con_b and b.item_category not in(1,2,3,12,13,14) and a.is_approved=$approval_type and a.ready_to_approve=1 and a.status_active=1 and a.is_deleted=0 
							group by a.id,a.remarks, a.company_id, a.REQU_NO, a.requ_prefix_num , a.REQUISITION_DATE, a.delivery_date, a.is_approved
							order by  a.id";
					}
					else // Next user
					{
						$sequence_no=return_field_value("max(sequence_no)","electronic_approval_setup","company_id=$company_id and page_id=$page_id and sequence_no<$user_sequence_no and bypass=2 and is_deleted = 0");
						if($sequence_no=="") // bypass if previous user Yes
						{
							if($this->db->dbdriver == 'mysqli')
							{
								
								$seqSql="select group_concat(sequence_no) as sequence_no_by  from electronic_approval_setup where company_id=$company_id and page_id=$page_id and sequence_no<$user_sequence_no and is_deleted=0";
								$seqData=sql_select($seqSql);
								
								$sequence_no_by=$seqData[0][csf('sequence_no_by')];
								
								$requsition_id=return_field_value("group_concat(distinct(b.mst_id)) as requsition_id","inv_purchase_requisition_mst a, approval_history b, inv_purchase_requisition_dtls c","a.id=b.mst_id and a.id = c.mst_id and a.company_id=$company_name $item_category_con_c and c.item_category not in(1,2,3,12,13,14) and b.sequence_no in ($sequence_no_by) and a.ready_to_approve=1 and b.entry_form=1 and b.current_approval_status=1 $date_cond","requsition_id");
								$requsition_id=implode(",",array_unique(explode(",",$requsition_id)));
								
								$requsition_id_app_byuser=return_field_value("group_concat(distinct(b.mst_id)) as requsition_id","inv_purchase_requisition_mst a, approval_history b, inv_purchase_requisition_dtls c","a.id=b.mst_id and a.id = c.mst_id and a.company_id=$company_name and a.ready_to_approve=1 $item_category_con_c and c.item_category not in(1,2,3,12,13,14) and b.sequence_no=$user_sequence_no and a.ready_to_approve=1 and b.entry_form=1 and b.current_approval_status=1","requsition_id");
							}
							else
							{
								
								$seqSql="select LISTAGG(sequence_no, ',') WITHIN GROUP (ORDER BY sequence_no) as sequence_no_by from electronic_approval_setup where company_id=$company_id and page_id=$page_id and sequence_no<$user_sequence_no and is_deleted=0";
								$seqData=sql_select($seqSql);
								
								$sequence_no_by=$seqData[0][csf('sequence_no_by')];
								
								$requsition_id=return_field_value("LISTAGG(b.mst_id, ',') WITHIN GROUP (ORDER BY b.mst_id) as requsition_id","inv_purchase_requisition_mst a, approval_history b, inv_purchase_requisition_dtls c","a.id=b.mst_id and a.id = c.mst_id and a.company_id=$company_name $item_category_con_c and c.item_category not in(1,2,3,12,13,14) and b.sequence_no in ($sequence_no_by) and a.ready_to_approve=1 and b.entry_form=1 and b.current_approval_status=1  $date_cond","requsition_id");
			
			
								$requsition_id=implode(",",array_unique(explode(",",$requsition_id)));
								
								$requsition_id_app_byuser=return_field_value("LISTAGG(b.mst_id, ',') WITHIN GROUP (ORDER BY b.mst_id) as requsition_id","inv_purchase_requisition_mst a, approval_history b, inv_purchase_requisition_dtls c","a.id=b.mst_id and a.id = c.mst_id and a.company_id=$company_name $item_category_con_c and c.item_category not in(1,2,3,12,13,14) and b.sequence_no=$user_sequence_no and a.ready_to_approve=1 and b.entry_form=1 and b.current_approval_status=1","requsition_id");
								$requsition_id_app_byuser=implode(",",array_unique(explode(",",$requsition_id_app_byuser)));
							}
							$result=array_diff(explode(',',$requsition_id),explode(',',$requsition_id_app_byuser));
							$requsition_id=implode(",",$result);
							// print_r($requsition_id);
							if($this->db->dbdriver == 'mysqli')
							{
								$select_item_cat = "group_concat(b.item_category) as item_category_id ";
							}else{
								
								$select_item_cat = "listagg(b.item_category, ',') within group (order by b.item_category) as item_category_id ";
							}
					
						
							
							if($requsition_id!="")
							{
								
			
								$sql=" SELECT x.* from  (SELECT DISTINCT (a.id),a.remarks, a.company_id, a.REQU_NO, a.requ_prefix_num ,  $select_item_cat, a.REQUISITION_DATE, a.delivery_date, a.is_approved
								from inv_purchase_requisition_mst a, inv_purchase_requisition_dtls b where a.id =b.mst_id and a.company_id=$company_name $item_category_con_b and b.item_category not in(1,2,3,12,13,14) and a.is_approved in(0,3) and a.id in ($requsition_id) and a.ready_to_approve=1 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 
								GROUP by a.id, a.company_id,a.remarks, a.REQU_NO, a.requ_prefix_num , a.REQUISITION_DATE, a.delivery_date, a.is_approved 
			
								UNION ALL
			
								SELECT DISTINCT (a.ID),a.remarks, a.company_id, a.REQU_NO, a.requ_prefix_num ,  $select_item_cat, a.REQUISITION_DATE, a.delivery_date, a.is_approved
								from inv_purchase_requisition_mst a, inv_purchase_requisition_dtls b where a.id =b.mst_id and a.company_id=$company_name and a.id not in ($requsition_id) $item_category_con_b and b.item_category not in(1,2,3,12,13,14) and a.is_approved=$approval_type and a.ready_to_approve=1 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 
								GROUP by a.id,a.remarks, a.company_id,a.remarks, a.REQU_NO, a.requ_prefix_num ,  a.is_approved, a.REQUISITION_DATE, a.delivery_date) x  order by x.id";
								//echo $sql;
							}
							else
							{ 
								$sql="SELECT DISTINCT (a.ID),a.remarks, a.company_id, a.REQU_NO, a.requ_prefix_num ,  $select_item_cat, a.REQUISITION_DATE, a.delivery_date, a.is_approved
								from inv_purchase_requisition_mst a, inv_purchase_requisition_dtls b where a.id =b.mst_id and a.company_id=$company_name $item_category_con_b and b.item_category not in(1,2,3,12,13,14) and a.is_approved=$approval_type and a.ready_to_approve=1 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 
								group by a.id,a.remarks, a.company_id, a.REQU_NO, a.requ_prefix_num , a.REQUISITION_DATE, a.delivery_date, a.is_approved order by a.id";
							}
						}
						
						else // bypass No
						{
							$user_sequence_no=$user_sequence_no-1;
							//echo $sequence_no;
							if($sequence_no==$user_sequence_no) 
							{
								$sequence_no_by_pass=$sequence_no;
								$sequence_no_cond=" and b.sequence_no in ($sequence_no_by_pass)";
								if($this->db->dbdriver == 'mysqli')
								{
									$select_item_cat = "group_concat(c.item_category) as item_category_id ";
								}
								else
								{
									$select_item_cat = "listagg(c.item_category, ',') within group (order by c.item_category) as item_category_id ";
								}
							}
							else
							{
								if($this->db->dbdriver == 'mysqli')
								{
									$sequence_no_by_pass=return_field_value("group_concat(sequence_no) as sequence_no","electronic_approval_setup","company_id=$company_name and page_id=$page_id and sequence_no between $sequence_no and $user_sequence_no and bypass=1","sequence_no");
			
									$select_item_cat = "group_concat(c.item_category) as item_category_id ";
			
								}
								else
								{
									$sequence_no_by_pass=return_field_value("listagg(sequence_no,',') within group (order by sequence_no) as sequence_no","electronic_approval_setup","company_id=$company_name and page_id=$page_id and sequence_no between $sequence_no and $user_sequence_no and bypass=1","sequence_no");
			
									$select_item_cat = "listagg(c.item_category, ',') within group (order by c.item_category) as item_category_id ";
								}
								
								if($sequence_no_by_pass=="") $sequence_no_cond=" and b.sequence_no='$sequence_no'";
								else $sequence_no_cond=" and b.sequence_no in ($sequence_no_by_pass)";
							
							}
								$sql="SELECT a.ID,a.remarks, a.company_id, a.REQU_NO, a.requ_prefix_num,  $select_item_cat, a.REQUISITION_DATE, a.delivery_date, b.id as approval_id, a.is_approved 
								from inv_purchase_requisition_mst a, approval_history b, inv_purchase_requisition_dtls c 
								where a.id=b.mst_id and a.id = c.mst_id and a.ready_to_approve=1 and b.entry_form=1 and a.company_id=$company_name $item_category_con_c and c.item_category not in(1,2,3,12,13,14) and a.status_active=1 and a.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and b.current_approval_status=1 and a.is_approved in (1,3) $sequence_no_cond 
								group by a.id,a.remarks, a.company_id, a.REQU_NO, a.requ_prefix_num, a.REQUISITION_DATE, a.delivery_date, b.id, a.is_approved order by a.id";
							
						}
					}
				
						 //echo $sql;die;
					
					$nameArray=sql_select( $sql );
					foreach ($nameArray as $row)
					{
						
						if($select_no){	
							$dataArr[PAGE_ID]=$page_id;
							$dataArr[APP_TITLE]='Purchase Requisition Approval';
							$dataArr[COMPANY_ID]=$company_id;
							$dataArr[APP_ID]=$row->ID;
							$dataArr[MESSAGE1]=($row->REQU_NO)?$row->REQU_NO:'';
							$dataArr[MESSAGE2]=($row->REQUISITION_DATE)?$row->REQUISITION_DATE:'00-00-0000';
							$dataArr[MESSAGE3]=($buyer_arr[$row->BUYER_NAME])?$buyer_arr[$row->BUYER_NAME]:'';
							$dataArr[MESSAGE4]='';
						}
						else
						{
							$dataArr[$i][PAGE_ID]=$page_id;
							$dataArr[$i][APP_TITLE]='Purchase Requisition Approval';
							$dataArr[$i][COMPANY_ID]=$company_id;
							$dataArr[$i][APP_ID]=$row->ID;
							
							//$dataArr[DTLS][$i][2][CAPTION]="STYLE REF NO";
							$dataArr[$i][MESSAGE1]=($row->REQU_NO)?$row->REQU_NO:'';
				
							//$dataArr[DTLS][$i][3][CAPTION]="QUOTATION DATE";
							$dataArr[$i][MESSAGE2]=($row->REQUISITION_DATE)?$row->REQUISITION_DATE:'00-00-0000';
							
							//$dataArr[DTLS][$i][4][CAPTION]="BUYER NAME";
							$dataArr[$i][MESSAGE3]=($buyer_arr[$row->BUYER_NAME])?$buyer_arr[$row->BUYER_NAME]:'';
							
							//$dataArr[DTLS][$i][5][CAPTION]="";
							$dataArr[$i][MESSAGE4]='';
						}
						$i++; 
						
					}
					
				}
				
			}//end company foreach;
	
		
		}//Purchase Requisition Approval if con;
		
		//Sample Requisition Approval
		if($sr_page_id==937){
			$page_id=937;
			$approval_type=0;
			
			
			foreach($company_arr as $company_id=>$company_name){
				$user_sequence_no=$user_sequence_no_array[$page_id][$company_id];
				$min_sequence_no=$min_sequence_no_array[$page_id][$company_id];
				//$maxUserNo=$max_sequence_no_array[$page_id][$company_id];
				//$buyer_ids_array=$user_buyer_ids_array[$page_id][$company_id];
			
				
				if($approval_type==0 && $user_sequence_no !=""){ // Un-Approve
		
		
					if($this->db->dbdriver == 'mysqli')
					{
						$sequence_no=return_field_value("max(sequence_no) as seq","electronic_approval_setup","company_id=$company_id and page_id=$page_id and sequence_no<$user_sequence_no and bypass=2 and buyer_id='' and is_deleted=0","seq");
					}
					else
					{
						$sequence_no=return_field_value("max(sequence_no) as seq","electronic_approval_setup","company_id=$company_id and page_id=$page_id and sequence_no<$user_sequence_no and bypass=2 and buyer_id is null and is_deleted=0","seq");
					}
					
					if($user_sequence_no==$min_sequence_no)
					{	
						//$buyer_ids=$buyer_ids_array[$user_id]['u'];
						$buyer_ids=$user_buyer_ids_array[$user_id]['u'];
						
						if($buyer_ids=="") $buyer_id_cond=""; else $buyer_id_cond=" and a.buyer_name in($buyer_ids)";
						
						$sql_req="select a.id,a.company_id,a.REQUISITION_DATE,a.REQUISITION_NUMBER_PREFIX_NUM,a.STYLE_REF_NO,a.buyer_name,a.season,a.product_dept,a.dealing_marchant,a.agent_name,a.buyer_ref,a.bh_merchant,estimated_shipdate,a.remarks,a.status_active,a.is_deleted from sample_development_mst a where a.entry_form_id=117  and a.company_id=$company_id  $buyer_id_cond  $season_cond $style_id_cond $date_cond and  a.is_approved=$approval_type and a.req_ready_to_approved=1 and a.status_active=1 and a.is_deleted=0 order by a.id";
						//echo $sql_req;die;
					}
					else if($sequence_no=="")
					{  
						$buyer_ids=$buyer_ids_array[$user_id]['u'];
						if($buyer_ids=="") $buyer_id_cond=""; else $buyer_id_cond=" and a.buyer_name in($buyer_ids)";
						
						if($this->db->dbdriver == 'mysqli')
						{
			
							$seqSql="select group_concat(sequence_no) as SEQUENCE_NO_BY,
					group_concat(case when bypass=2 and buyer_id<>'' then buyer_id end) as BUYER_IDS from electronic_approval_setup where company_id=$company_id and page_id=$page_id and sequence_no<$user_sequence_no and is_deleted=0";
					
							$seqData=sql_select($seqSql);
							
							$sequence_no_by=$seqData[0]->SEQUENCE_NO_BY;
							$buyerIds=$seqData[0]->BUYER_IDS;
							
							if($buyerIds=="") $buyerIds_cond=""; else $buyerIds_cond=" and a.buyer_name not in($buyerIds)";
							
							$sample_id=return_field_value("group_concat(distinct(mst_id)) as sample_id","sample_development_mst a, approval_history b","a.id=b.mst_id and a.company_id=$company_id and a.entry_form_id=117 and b.entry_form=25 and b.sequence_no in ($sequence_no_by) and b.current_approval_status=1 $buyer_id_cond $date_cond","sample_id");
							
							$booking_id_app_byuser=return_field_value("group_concat(distinct(mst_id)) as sample_id","sample_development_mst a, approval_history b","a.id=b.mst_id and a.company_id=$company_id and a.entry_form_id=117 and b.sequence_no=$user_sequence_no and b.entry_form=25 and b.current_approval_status=1","sample_id");
						}
						else
						{
							
							$seqSql="select SEQUENCE_NO, BYPASS, BUYER_ID from electronic_approval_setup where company_id=$company_id and page_id=$page_id and sequence_no<$user_sequence_no and is_deleted=0 order by sequence_no desc";
							$seqData=sql_select($seqSql);
							
							
							$buyerIds=''; $sequence_no_by_yes=''; $sequence_no_by_no=''; $approved_string=''; $check_buyerIds_arr=array();
							foreach($seqData as $sRow)
							{
								if($sRow->BYPASS==2)
								{
									$sequence_no_by_no.=$sRow->SEQUENCE_NO.",";
									if($sRow->BUYER_ID!="") 
									{
										$buyerIds.=$sRow->BUYER_ID.",";
										
										$buyer_id_arr=explode(",",$sRow->BUYER_ID);
										$result=array_diff($buyer_id_arr,$check_buyerIds_arr);
										if(count($result)>0)
										{
											$query_string.=" (b.sequence_no=".$sRow->SEQUENCE_NO." and a.buyer_name in(".implode(",",$result).")) or ";
										}
										$check_buyerIds_arr=array_unique(array_merge($check_buyerIds_arr,$buyer_id_arr));
									}
								}
								else
								{
									$sequence_no_by_yes.=$sRow->SEQUENCE_NO.",";
								}
							}
							$buyerIds=chop($buyerIds,',');
							if($buyerIds=="") 
							{
								$buyerIds_cond=""; 
								$seqCond="";
							}
							else 
							{
								$buyerIds_cond=" and a.buyer_name not in($buyerIds)";
								$seqCond=" and (".chop($query_string,'or ').")";
							}
							$sequence_no_by_no=chop($sequence_no_by_no,',');
							$sequence_no_by_yes=chop($sequence_no_by_yes,',');
							
							if($sequence_no_by_yes=="") $sequence_no_by_yes=0;
							if($sequence_no_by_no=="") $sequence_no_by_no=0;
							
							$booking_id='';
							$booking_id_sql="select distinct (mst_id) as BOOKING_ID from sample_development_mst a, approval_history b where a.id=b.mst_id and a.company_id=$company_id and a.entry_form_id=117  and b.sequence_no in ($sequence_no_by_no) and b.entry_form=25 and b.current_approval_status=1 $buyer_id_cond $date_cond $seqCond
							union
							select distinct (mst_id) as booking_id from sample_development_mst a, approval_history b where a.id=b.mst_id and a.company_id=$company_id and a.entry_form_id=117 and b.sequence_no in ($sequence_no_by_yes) and b.entry_form=25 and b.current_approval_status=1 $buyer_id_cond $date_cond";
							$bResult=sql_select($booking_id_sql);
							foreach($bResult as $bRow)
							{
								$booking_id.=$bRow->BOOKING_ID.",";
							}
							
							$booking_id=chop($booking_id,',');
							
							$booking_id_app_byuser=return_field_value("LISTAGG(mst_id, ',') WITHIN GROUP (ORDER BY mst_id) as booking_id","sample_development_mst a, approval_history b","a.id=b.mst_id and a.company_id=$company_id and a.entry_form_id=117 and b.sequence_no=$user_sequence_no and b.entry_form=25 and b.current_approval_status=1","booking_id");
							$booking_id_app_byuser=implode(",",array_unique(explode(",",$booking_id_app_byuser)));
						}
						$result=array_diff(explode(',',$booking_id),explode(',',$booking_id_app_byuser));
						$booking_id=implode(",",$result);
						
						$booking_id_cond="";
						if($booking_id!="")
						{
							if($this->db->dbdriver != 'mysqli' && count($result)>999)
							{
								$booking_id_chunk_arr=array_chunk($result,999) ;
								foreach($booking_id_chunk_arr as $chunk_arr)
								{
									$chunk_arr_value=implode(",",$chunk_arr);	
									$bokIds_cond.=" a.id in($chunk_arr_value) or ";	
								}
								
								$booking_id_cond.=" and (".chop($bokIds_cond,'or ').")";			
							}
							else
							{
								$booking_id_cond=" and a.id in($booking_id)";	 
							}
						}
						else $booking_id_cond="";
						
						
						
						if($this->db->dbdriver == 'mysqli')
						{
							if($booking_id!="")
							{
							
								$sql_req="select * from ( select a.id,a.company_id,a.REQUISITION_DATE,a.REQUISITION_NUMBER_PREFIX_NUM,a.STYLE_REF_NO,a.buyer_name, a.season,a.product_dept, a.dealing_marchant, a.agent_name,a.buyer_ref, a.bh_merchant, a.estimated_shipdate,a.remarks,a.status_active,a.is_deleted , '0' as approval_id,  a.is_approved from sample_development_mst a where  a.entry_form_id=117  and a.company_id=$company_id  $buyer_id_cond $buyer_id_cond2  $buyerIds_cond $season_cond $style_id_cond $date_cond and  a.is_approved=$approval_type and a.req_ready_to_approved=1 and a.status_active=1 and a.is_deleted=0 
								union all
								select a.id,a.company_id,a.REQUISITION_DATE,a.REQUISITION_NUMBER_PREFIX_NUM, a.STYLE_REF_NO,a.buyer_name,a.season, a.product_dept, a.dealing_marchant, a.agent_name,a.buyer_ref, a.bh_merchant, a.estimated_shipdate,a.remarks,a.status_active,a.is_deleted , '0' as approval_id,  a.is_approved from sample_development_mst a where  a.entry_form_id=117  and a.company_id=$company_id  and a.is_approved in(1,3) and a.id in($booking_id) $buyer_id_cond $buyer_id_cond2 $date_cond $season_cond $style_id_cond $date_cond and a.is_approved in(1,3) and a.req_ready_to_approved=1 and a.status_active=1 and a.is_deleted=0 ) sql order by sql.id
								";	
									
							}
							else
							{
								$sql_req="select a.id,a.company_id,a.REQUISITION_DATE,a.REQUISITION_NUMBER_PREFIX_NUM,a.STYLE_REF_NO,a.buyer_name, a.season,a.product_dept, a.dealing_marchant, a.agent_name,a.buyer_ref, a.bh_merchant, a.estimated_shipdate,a.remarks,a.status_active,a.is_deleted , '0' as approval_id,  a.is_approved from sample_development_mst a where  a.entry_form_id=117  and a.company_id=$company_id  $buyer_id_cond $buyer_id_cond2  $buyerIds_cond $season_cond $style_id_cond $date_cond and  a.is_approved=$approval_type and a.req_ready_to_approved=1 and a.status_active=1 and a.is_deleted=0 order by a.id";
							}
						
						}
						else
						{
							if($booking_id!="")
							{   
								
								$sql_req=" select * from (select a.id,a.company_id,a.REQUISITION_DATE,a.REQUISITION_NUMBER_PREFIX_NUM,a.STYLE_REF_NO,a.buyer_name, a.season,a.product_dept, a.dealing_marchant, a.agent_name,a.buyer_ref, a.bh_merchant, a.estimated_shipdate,a.remarks,a.status_active,a.is_deleted , '0' as approval_id,  a.is_approved from sample_development_mst a where  a.entry_form_id=117  and a.company_id=$company_id  $buyer_id_cond $buyer_id_cond2  $buyerIds_cond $season_cond $style_id_cond $date_cond and  a.is_approved=$approval_type and a.req_ready_to_approved=1 and a.status_active=1 and a.is_deleted=0 
								union all
								select a.id,a.company_id,a.REQUISITION_DATE,a.REQUISITION_NUMBER_PREFIX_NUM, a.STYLE_REF_NO,a.buyer_name,a.season, a.product_dept, a.dealing_marchant, a.agent_name,a.buyer_ref, a.bh_merchant,a.estimated_shipdate,a.remarks,a.status_active,a.is_deleted , '0' as approval_id,  a.is_approved from sample_development_mst a where  a.entry_form_id=117  and a.company_id=$company_id  and a.is_approved in(1,3) $booking_id_cond $buyer_id_cond $buyer_id_cond2 $date_cond $season_cond $style_id_cond $date_cond and a.req_ready_to_approved=1 and a.status_active=1 and a.is_deleted=0 ) sql order by sql.id";	 
							}
							else
							{
							
								$sql_req="select a.id,a.company_id,a.REQUISITION_DATE,a.REQUISITION_NUMBER_PREFIX_NUM,a.STYLE_REF_NO,a.buyer_name, a.season,a.product_dept, a.dealing_marchant, a.agent_name,a.buyer_ref, a.bh_merchant, a.estimated_shipdate,a.remarks,a.status_active,a.is_deleted , '0' as approval_id,  a.is_approved from sample_development_mst a where  a.entry_form_id=117  and a.company_id=$company_id  $buyer_id_cond $buyer_id_cond2  $buyerIds_cond $season_cond $style_id_cond $date_cond and  a.is_approved=$approval_type and a.req_ready_to_approved=1 and a.status_active=1 and a.is_deleted=0 order by a.id";
							}
						}
					}
					else
					{
						$buyer_ids=$buyer_ids_array[$user_id]['u'];
						if($buyer_ids=="") $buyer_id_cond=""; else $buyer_id_cond=" and a.buyer_id in($buyer_ids)";
						
						$user_sequence_no = $user_sequence_no-1;
				
						if($this->db->dbdriver == 'mysqli')
						{
							$sequence_no_by_pass=return_field_value("group_concat(sequence_no)","electronic_approval_setup","company_id=$company_id and page_id=$page_id and sequence_no between $sequence_no and $user_sequence_no and is_deleted=0");
						}
						else
						{
							$sequence_no_by_pass=return_field_value("LISTAGG(sequence_no, ',') WITHIN GROUP (ORDER BY sequence_no) as sequence_no","electronic_approval_setup","company_id=$company_id and page_id=$page_id and sequence_no between $sequence_no and $user_sequence_no and is_deleted=0","sequence_no");	
						}
						
						if($sequence_no_by_pass=="") $sequence_no_cond=" and b.sequence_no='$sequence_no'";
						else $sequence_no_cond=" and b.sequence_no in ($sequence_no_by_pass)";
						$sql_req="select a.id,a.company_id,a.REQUISITION_DATE,a.REQUISITION_NUMBER_PREFIX_NUM,a.STYLE_REF_NO,a.buyer_name, a.season,a.product_dept, a.dealing_marchant, a.agent_name,a.buyer_ref, a.bh_merchant, a.estimated_shipdate,a.remarks,a.status_active,a.is_deleted,  a.is_approved, b.id as approval_id, b.sequence_no, b.approved_by from sample_development_mst a, approval_history b where a.id=b.mst_id  and b.entry_form=25 and a.entry_form_id=117  and a.company_id=$company_id  $buyer_id_cond $buyer_id_cond2 $sequence_no_cond $season_cond $style_id_cond $date_cond and a.is_approved in (1,3) and b.current_approval_status=1 and a.req_ready_to_approved=1 and a.status_active=1 and a.is_deleted=0 order by a.id";
						//echo $sql_req;
					}
					
					
					$nameArray=sql_select( $sql_req ); 
					foreach ($nameArray as $row)
					{
						
						if($select_no){	
							$dataArr[PAGE_ID]=$page_id;
							$dataArr[APP_TITLE]='Sample Requisition Approval';
							$dataArr[COMPANY_ID]=$company_id;
							$dataArr[APP_ID]=$row->ID;
							$dataArr[MESSAGE1]=($row->REQUISITION_NUMBER_PREFIX_NUM)?$row->REQUISITION_NUMBER_PREFIX_NUM:'';
							$dataArr[MESSAGE2]=($row->REQUISITION_DATE)?$row->REQUISITION_DATE:'00-00-0000';
							$dataArr[MESSAGE3]=($buyer_arr[$row->BUYER_NAME])?$buyer_arr[$row->BUYER_NAME]:'';
							$dataArr[MESSAGE4]='';
						}
						else
						{
							$dataArr[$i][PAGE_ID]=$page_id;
							$dataArr[$i][APP_TITLE]='Sample Requisition Approval';
							$dataArr[$i][COMPANY_ID]=$company_id;
							$dataArr[$i][APP_ID]=$row->ID;
							
							//$dataArr[DTLS][$i][2][CAPTION]="STYLE REF NO";
							$dataArr[$i][MESSAGE1]=($row->REQUISITION_NUMBER_PREFIX_NUM)?$row->REQUISITION_NUMBER_PREFIX_NUM:'';
				
							//$dataArr[DTLS][$i][3][CAPTION]="QUOTATION DATE";
							$dataArr[$i][MESSAGE2]=($row->REQUISITION_DATE)?$row->REQUISITION_DATE:'00-00-0000';
							
							//$dataArr[DTLS][$i][4][CAPTION]="BUYER NAME";
							$dataArr[$i][MESSAGE3]=($buyer_arr[$row->BUYER_NAME])?$buyer_arr[$row->BUYER_NAME]:'';
							
							//$dataArr[DTLS][$i][5][CAPTION]="";
							$dataArr[$i][MESSAGE4]='';
						}
						$i++; 
						
					}

		
			}
		}//company
	}//Sample Requisition Approval if con;
		
		//Item Issue Requisiton Approval
		if($iir_page_id==1056){
			$page_id=1056;
			$approval_type=0;
			
			
			foreach($company_arr as $company_id=>$company_name){
				$user_sequence_no=$user_sequence_no_array[$page_id][$company_id];
				$min_sequence_no=$min_sequence_no_array[$page_id][$company_id];
				//$maxUserNo=$max_sequence_no_array[$page_id][$company_id];
				$buyer_ids_array=$user_buyer_ids_array[$page_id][$company_id];
			
				
				if($approval_type==0 && $user_sequence_no !=""){
								
					if($select_no)
					{
						$requsition_cond = " and a.id = $select_no ";
					}
					
					
					
					if($user_sequence_no==$min_sequence_no)	// First user
					{
						$sql="SELECT a.id, a.itemissue_req_prefix_num,a.INDENT_DATE,a.REQUIRED_DATE,a.ITEMISSUE_REQ_SYS_ID ,a.company_id,a.is_approved 
						from inv_item_issue_requisition_mst a, inv_itemissue_requisition_dtls b, product_details_master c
						where a.id=b.mst_id and b.product_id=c.id and a.company_id=$company_id $requsition_cond $cbo_item_category_cond and a.ready_to_approved=1 and a.is_approved=0 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0
						group by a.id, a.itemissue_req_prefix_num,a.INDENT_DATE,a.REQUIRED_DATE,a.ITEMISSUE_REQ_SYS_ID ,a.company_id,a.is_approved";
					}
					else // Next user
					{
						$sequence_no=return_field_value("max(sequence_no)","electronic_approval_setup","company_id=$company_id and page_id=$page_id and sequence_no<$user_sequence_no and bypass=2 and is_deleted = 0");
						if($sequence_no=="") // bypass if previous user Yes
						{
							if($this->db->dbdriver == 'mysqli')
							{
								
								$seqSql="select group_concat(sequence_no) as sequence_no_by  from electronic_approval_setup where company_id=$company_id and page_id=$page_id and sequence_no<$user_sequence_no and is_deleted=0";
								$seqData=sql_select($seqSql);
								
								$sequence_no_by=$seqData[0][csf('sequence_no_by')];
								$requsition_id=return_field_value("group_concat(distinct(app.mst_id)) as requsition_id","inv_item_issue_requisition_mst a, inv_itemissue_requisition_dtls b, product_details_master c, approval_history app"," app.mst_id=a.id and a.id=b.mst_id and b.product_id=c.id and a.id=b.mst_id and a.company_id=$company_id and  app.sequence_no in ($sequence_no_by) and app.entry_form=26 and app.current_approval_status=1 ","requsition_id");
								$requsition_id=implode(",",array_unique(explode(",",$requsition_id)));
								$requsition_id_app_byuser=return_field_value("group_concat(distinct(app.mst_id)) as requsition_id","inv_item_issue_requisition_mst a, inv_itemissue_requisition_dtls b, product_details_master c, approval_history app","app.mst_id=a.id and a.id=b.mst_id and b.product_id=c.id and a.id=b.mst_id and a.company_id=$company_id and app.entry_form=26 and app.current_approval_status=1 and app.sequence_no=$user_sequence_no","requsition_id");
							}
							else
							{
			
								$seqSql="select LISTAGG(sequence_no, ',') WITHIN GROUP (ORDER BY sequence_no) as sequence_no_by from electronic_approval_setup where company_id=$company_id and page_id=$page_id and sequence_no<$user_sequence_no and is_deleted=0";
								$seqData=sql_select($seqSql);
								$sequence_no_by=$seqData[0][csf('sequence_no_by')];
								$requsition_id=return_field_value("LISTAGG(app.mst_id, ',') WITHIN GROUP (ORDER BY app.mst_id) as requsition_id","inv_item_issue_requisition_mst a, inv_itemissue_requisition_dtls b, product_details_master c, approval_history app"," app.mst_id=a.id and a.id=b.mst_id and b.product_id=c.id and a.id=b.mst_id and a.company_id=$company_id and  app.sequence_no in ($sequence_no_by) and app.entry_form=26 and app.current_approval_status=1 ","requsition_id");
			
								$requsition_id=implode(",",array_unique(explode(",",$requsition_id)));
								$requsition_id_app_byuser=return_field_value("LISTAGG(app.mst_id, ',') WITHIN GROUP (ORDER BY app.mst_id) as requsition_id","inv_item_issue_requisition_mst a, inv_itemissue_requisition_dtls b, product_details_master c, approval_history app","app.mst_id=a.id and a.id=b.mst_id and b.product_id=c.id and a.id=b.mst_id and a.company_id=$company_id and app.entry_form=26 and app.current_approval_status=1 and app.sequence_no=$user_sequence_no","requsition_id");
								$requsition_id_app_byuser=implode(",",array_unique(explode(",",$requsition_id_app_byuser)));
							}
				
							$result=array_diff(explode(',',$requsition_id),explode(',',$requsition_id_app_byuser));
							$requsition_id=implode(",",$result);
											
							if($requsition_id!="")
							{
								$sql = "select U.* from (SELECT a.id,a.itemissue_req_prefix_num,a.INDENT_DATE,a.REQUIRED_DATE,a.ITEMISSUE_REQ_SYS_ID ,a.company_id,a.is_approved
								FROM inv_item_issue_requisition_mst a, inv_itemissue_requisition_dtls b, product_details_master c
								WHERE a.id in ($requsition_id) and a.id=b.mst_id and b.product_id=c.id and a.company_id=$company_id $cbo_item_category_cond $requ_cond $requsition_cond and a.is_approved in(1,3) $approved_by and a.ready_to_approved=1 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 
								GROUP by a.id,a.itemissue_req_prefix_num,a.INDENT_DATE,a.REQUIRED_DATE,a.ITEMISSUE_REQ_SYS_ID ,a.company_id,a.is_approved
								
								union all 
								 
								SELECT a.id,a.itemissue_req_prefix_num,a.INDENT_DATE,a.REQUIRED_DATE,a.ITEMISSUE_REQ_SYS_ID ,a.company_id,a.is_approved
								FROM inv_item_issue_requisition_mst a, inv_itemissue_requisition_dtls b, product_details_master c
								WHERE a.id not in ($requsition_id) and a.id=b.mst_id and b.product_id=c.id and a.company_id=$company_id $cbo_item_category_cond $requ_cond $requsition_cond and a.is_approved in(0) $approved_by and a.ready_to_approved=1 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 
								GROUP by a.id,a.itemissue_req_prefix_num,a.INDENT_DATE,a.REQUIRED_DATE,a.ITEMISSUE_REQ_SYS_ID ,a.company_id,a.is_approved) U order by U.id";
							}
							else
							{
								$sql = "SELECT a.id,a.itemissue_req_prefix_num,a.INDENT_DATE,a.REQUIRED_DATE,a.ITEMISSUE_REQ_SYS_ID ,a.company_id,a.is_approved
								FROM inv_item_issue_requisition_mst a, inv_itemissue_requisition_dtls b, product_details_master c
								WHERE a.id=b.mst_id and b.product_id=c.id and a.company_id=$company_id $cbo_item_category_cond $requ_cond $requsition_cond and a.is_approved in(0) $approved_by and a.ready_to_approved=1 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 
								GROUP by a.id,a.itemissue_req_prefix_num,a.INDENT_DATE,a.REQUIRED_DATE,a.ITEMISSUE_REQ_SYS_ID ,a.company_id,a.is_approved";
								
							}
						}
						else // bypass No
						{
							$user_sequence_no=$user_sequence_no-1;
							if($sequence_no==$user_sequence_no) 
							{
								$sequence_no_by_pass=$sequence_no;
								$sequence_no_cond=" and d.sequence_no in ($sequence_no_by_pass)";
							}
							else
							{
								if($this->db->dbdriver == 'mysqli')
								{
									$sequence_no_by_pass=return_field_value("group_concat(sequence_no) as sequence_no","electronic_approval_setup","company_id=$company_name and page_id=$page_id and sequence_no between $sequence_no and $user_sequence_no and bypass=1","sequence_no");
								}
								else
								{
									$sequence_no_by_pass=return_field_value("listagg(sequence_no,',') within group (order by sequence_no) as sequence_no","electronic_approval_setup","company_id=$company_name and page_id=$page_id and sequence_no between $sequence_no and $user_sequence_no and bypass=1","sequence_no");
								}
								
								if($sequence_no_by_pass=="") $sequence_no_cond=" and d.sequence_no='$sequence_no'";
								else $sequence_no_cond=" and d.sequence_no in ($sequence_no_by_pass)";
							}
							
							$sql="SELECT a.id,a.itemissue_req_prefix_num,a.INDENT_DATE,a.REQUIRED_DATE,a.ITEMISSUE_REQ_SYS_ID ,a.company_id,a.is_approved
							FROM inv_item_issue_requisition_mst a, inv_itemissue_requisition_dtls b, product_details_master c, approval_history d
							WHERE a.id=b.mst_id and b.product_id=c.id and a.id=d.mst_id and a.company_id=$company_id $sequence_no_cond and a.is_approved in(1,3) and a.ready_to_approved=1 $cbo_item_category_cond $requsition_cond and d.entry_form=26 and d.current_approval_status=1 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0
							GROUP by a.id,a.itemissue_req_prefix_num,a.INDENT_DATE,a.REQUIRED_DATE,a.ITEMISSUE_REQ_SYS_ID ,a.company_id,a.is_approved
							ORDER by a.id";
						} 
					}
					
					
					$nameArray=sql_select( $sql ); 
					foreach ($nameArray as $row)
					{
						
						if($select_no){	
							$dataArr[PAGE_ID]=$page_id;
							$dataArr[APP_TITLE]='Item Issue Requisiton Approval';
							$dataArr[COMPANY_ID]=$company_id;
							$dataArr[APP_ID]=$row->ID;
							$dataArr[MESSAGE1]=($row->ITEMISSUE_REQ_SYS_ID)?$row->ITEMISSUE_REQ_SYS_ID:'';
							$dataArr[MESSAGE2]=($row->REQUIRED_DATE)?$row->REQUIRED_DATE:'00-00-0000';
							$dataArr[MESSAGE3]=($buyer_arr[$row->BUYER_NAME])?$buyer_arr[$row->BUYER_NAME]:'';
							$dataArr[MESSAGE4]='';
						}
						else
						{
							$dataArr[$i][PAGE_ID]=$page_id;
							$dataArr[$i][APP_TITLE]='Item Issue Requisiton Approval';
							$dataArr[$i][COMPANY_ID]=$company_id;
							$dataArr[$i][APP_ID]=$row->ID;
							
							//$dataArr[DTLS][$i][2][CAPTION]="STYLE REF NO";
							$dataArr[$i][MESSAGE1]=($row->ITEMISSUE_REQ_SYS_ID)?$row->ITEMISSUE_REQ_SYS_ID:'';
				
							//$dataArr[DTLS][$i][3][CAPTION]="QUOTATION DATE";
							$dataArr[$i][MESSAGE2]=($row->REQUIRED_DATE)?$row->REQUIRED_DATE:'00-00-0000';
							
							//$dataArr[DTLS][$i][4][CAPTION]="BUYER NAME";
							$dataArr[$i][MESSAGE3]=($buyer_arr[$row->BUYER_NAME])?$buyer_arr[$row->BUYER_NAME]:'';
							
							//$dataArr[DTLS][$i][5][CAPTION]="";
							$dataArr[$i][MESSAGE4]='';
						}
						$i++; 
						
					}
					
				
				}
				
			}//company
	
		}//Item Issue Requisiton Approval if con;
		
		//Service Booking For Knitting Approval
		if($sbk_page_id==1120){
			$page_id=1120;
			$approval_type=0;
			
			
			foreach($company_arr as $company_id=>$company_name){
				$user_sequence_no=$user_sequence_no_array[$page_id][$company_id];
				$min_sequence_no=$min_sequence_no_array[$page_id][$company_id];
				//$maxUserNo=$max_sequence_no_array[$page_id][$company_id];
				$buyer_ids_array=$user_buyer_ids_array[$page_id][$company_id];
			
				if($approval_type==0 && $user_sequence_no !=""){
					
					if($this->db->dbdriver == 'mysqli')
					{
						$sequence_no=return_field_value("max(sequence_no) as seq","electronic_approval_setup","company_id=$company_id and page_id=$page_id and sequence_no<$user_sequence_no and bypass=2 and buyer_id='' and is_deleted=0","seq");
					}
					else
					{
						$sequence_no=return_field_value("max(sequence_no) as seq","electronic_approval_setup","company_id=$company_id and page_id=$page_id and sequence_no<$user_sequence_no and bypass=2 and is_deleted=0","seq");                      
					}
					
					$approval_necessity_setup=return_field_value("approval_need","approval_setup_mst a,approval_setup_dtls b","a.id=b.mst_id and a.company_id=$company_id and b.page_id=27 and a.status_active=1 and a.is_deleted=0  order by a.setup_date desc","approval_need");
					$approval_necessity_setup=1;	
					
					
					if($user_sequence_no==$min_sequence_no && $approval_necessity_setup==1) // First user
					{
						$buyer_ids = $buyer_ids_array[$user_id]['u'];
						
						if($buyer_ids == "") $buyer_id_cond = ""; else $buyer_id_cond = " and a.buyer_id in($buyer_ids)";			
					 
						$sql= "SELECT a.id,a.booking_no_prefix_num as system_no,a.booking_no,a.buyer_id as buyer_name,a.is_approved,'0' as approval_id,
						a.job_no as po_job_no,b.style_ref_no as style_ref from wo_booking_mst a, wo_po_details_master b  
						where a.job_no = b.job_no and a.booking_type=3 and a.company_id = $company_id and  a.status_active=1 and a.is_deleted=0 and a.process=1  and a.ready_to_approved = 1 and a.is_approved=$approval_type $where_id_cond $buyer_id_cond $buyer_id_cond2 $buyerIds_cond order by a.booking_no_prefix_num";		 
					 
					   //echo $sql;
					}
					else if($sequence_no == "" && $approval_necessity_setup==1) // Next user // bypass if previous user Yes
					{
						// echo "bypass Yes";die;
						$buyer_ids=$buyer_ids_array[$user_id]['u'];
						if($buyer_ids == "") $buyer_id_cond = ""; else $buyer_id_cond = " and a.buyer_id in($buyer_ids)";
						
						if($this->db->dbdriver == 'mysqli')
						{
							$seqSql="select group_concat(sequence_no) as SEQUENCE_NO_BY,
							group_concat(case when bypass=2 and buyer_id<>'' then buyer_id end) as BUYER_IDS from electronic_approval_setup where company_id=$company_id and page_id=$page_id and sequence_no<$user_sequence_no and is_deleted=0";
							$seqData=sql_select($seqSql);
							
							$sequence_no_by = $seqData[0]->SEQUENCE_NO_BY;
							$buyerIds = $seqData[0]->BUYER_IDS;
							
							if($buyerIds == "") $buyerIds_cond = ""; else $buyerIds_cond = " and a.buyer_id not in($buyerIds)";
			
							$booking_id_app_byuser=return_field_value("group_concat(distinct(mst_id)) as master_id","wo_booking_mst a, approval_history b",
							"a.id=b.mst_id and a.company_id=$company_id  and b.sequence_no=$user_sequence_no and b.entry_form=29 and
							b.current_approval_status=1","master_id");
						}
						else
						{
							$seqSql="select LISTAGG(sequence_no, ',') WITHIN GROUP (ORDER BY sequence_no) as SEQUENCE_NO_BY, LISTAGG(case when bypass=2 and buyer_id is not null then buyer_id end, ',') WITHIN GROUP (ORDER BY null) as BUYER_IDS from electronic_approval_setup where company_id=$company_id and page_id=$page_id and sequence_no<$user_sequence_no and is_deleted=0";
							$seqData=sql_select($seqSql);
							
							$sequence_no_by=$seqData[0]->SEQUENCE_NO_BY;
							$buyerIds=$seqData[0]->BUYER_IDS;
							
							if($buyerIds == "") $buyerIds_cond = ""; else $buyerIds_cond = " and a.buyer_id not in($buyerIds)";
			
							$booking_id_app_byuser=return_field_value("LISTAGG(mst_id, ',') WITHIN GROUP (ORDER BY mst_id) as master_id","wo_booking_mst a,
							approval_history b","a.id=b.mst_id and a.company_id=$company_id and b.sequence_no=$user_sequence_no and b.entry_form=29 and
							b.current_approval_status=1","master_id");
							$booking_id_app_byuser=implode(",",array_unique(explode(",",$booking_id_app_byuser)));				
						} 
						$booking_id_cond="";
						if($booking_id_app_byuser!="") $booking_id_cond=" and a.id not in($booking_id_app_byuser)";
						// echo $booking_id_cond;die;
			
						$sql= "SELECT a.id, b.style_ref_no as style_ref,a.booking_no_prefix_num as system_no,a.booking_no,a.buyer_id as buyer_name, a.job_no as po_job_no, a.is_approved
						from wo_booking_mst a, wo_po_details_master b  
						where a.job_no=b.job_no and a.booking_type=3 and a.company_id = $company_id and  a.status_active=1 and a.is_deleted=0 and a.process=1 and a.ready_to_approved=1 and a.is_approved in(0,3) $buyer_id_cond $where_id_cond $buyer_id_cond2 $buyerIds_cond $booking_id_cond
						order by a.booking_no_prefix_num";	
						// echo $sql;die;		 
					}
					else // bypass No
					{
						// echo "bypass No";die;
						$buyer_ids = $buyer_ids_array[$user_id]['u'];
						if($buyer_ids == "") { 
							$buyer_id_cond="";                 
						} else {
							$buyer_id_cond=" and a.buyer_id in($buyer_ids)";
						}
								  
						$user_sequence_no=$user_sequence_no-1;
						
						$sequence_no_by_pass=''; // understand 
						if($sequence_no == $user_sequence_no && $approval_necessity_setup==1) 
						{                  
						   
							if($this->db->dbdriver == 'mysqli')
							{
								$sequence_no_by_pass=return_field_value("group_concat(sequence_no)","electronic_approval_setup","company_id=$company_id and page_id=$page_id and sequence_no between $sequence_no and $user_sequence_no and is_deleted=0");
							}
							else
							{
								$sequence_no_by_pass=return_field_value("LISTAGG(sequence_no, ',') WITHIN GROUP (ORDER BY sequence_no) as sequence_no","electronic_approval_setup","company_id=$company_id and page_id=$page_id and sequence_no between $sequence_no and $user_sequence_no and is_deleted=0","sequence_no");	
							}
							
							if($sequence_no_by_pass=="") {
								$sequence_no_cond=" and c.sequence_no='$sequence_no'";    
							}
							else {
								$sequence_no_cond=" and c.sequence_no in ($sequence_no_by_pass)";
							}
										 
			
							$sql= "SELECT a.id,a.booking_no_prefix_num as system_no,a.booking_no,a.buyer_id as buyer_name,a.is_approved,c.id as approval_id, a.job_no as po_job_no,b.style_ref_no as style_ref 
							from wo_booking_mst a, wo_po_details_master b, approval_history c  
							where a.id=c.mst_id and c.entry_form=29 and c.current_approval_status=1 and a.job_no=b.job_no and a.booking_type=3 and a.company_id=$company_id and a.status_active=1 and a.is_deleted=0 and a.process=1 and a.ready_to_approved=1 and a.is_approved=3 $buyer_id_cond $where_id_cond $buyer_id_cond2 $sequence_no_cond 
							order by a.booking_no_prefix_num";		 
						} 
						else 
						{
											
							if($approval_necessity_setup==1)
							{
								$sql= "SELECT a.id,a.booking_no_prefix_num as system_no,a.booking_no,a.buyer_id as buyer_name,a.is_approved,c.id as approval_id, a.job_no as po_job_no,b.style_ref_no as style_ref 
								from wo_booking_mst a, wo_po_details_master b, approval_history c  
								where a.id=c.mst_id and c.entry_form=29 and c.current_approval_status=1 and a.job_no=b.job_no and a.booking_type=3 and a.company_id=$company_id and a.status_active=1 and a.is_deleted=0 and a.process=1  and a.ready_to_approved = 1 and a.is_approved=3 $buyer_id_cond $where_id_cond $buyer_id_cond2 $sequence_no_cond 
								order by a.booking_no_prefix_num";
							}
						
						}
					} 
					
				   	$nameArray=sql_select( $sql ); 
					foreach ($nameArray as $row)
					{
						
						if($select_no){	
							$dataArr[PAGE_ID]=$page_id;
							$dataArr[APP_TITLE]='Item Issue Requisiton Approval';
							$dataArr[COMPANY_ID]=$company_id;
							$dataArr[APP_ID]=$row->ID;
							$dataArr[MESSAGE1]=($row->ITEMISSUE_REQ_SYS_ID)?$row->ITEMISSUE_REQ_SYS_ID:'';
							$dataArr[MESSAGE2]=($row->REQUIRED_DATE)?$row->REQUIRED_DATE:'00-00-0000';
							$dataArr[MESSAGE3]=($buyer_arr[$row->BUYER_NAME])?$buyer_arr[$row->BUYER_NAME]:'';
							$dataArr[MESSAGE4]='';
						}
						else
						{
							$dataArr[$i][PAGE_ID]=$page_id;
							$dataArr[$i][APP_TITLE]='Item Issue Requisiton Approval';
							$dataArr[$i][COMPANY_ID]=$company_id;
							$dataArr[$i][APP_ID]=$row->ID;
							
							//$dataArr[DTLS][$i][2][CAPTION]="STYLE REF NO";
							$dataArr[$i][MESSAGE1]=($row->ITEMISSUE_REQ_SYS_ID)?$row->ITEMISSUE_REQ_SYS_ID:'';
				
							//$dataArr[DTLS][$i][3][CAPTION]="QUOTATION DATE";
							$dataArr[$i][MESSAGE2]=($row->REQUIRED_DATE)?$row->REQUIRED_DATE:'00-00-0000';
							
							//$dataArr[DTLS][$i][4][CAPTION]="BUYER NAME";
							$dataArr[$i][MESSAGE3]=($buyer_arr[$row->BUYER_NAME])?$buyer_arr[$row->BUYER_NAME]:'';
							
							//$dataArr[DTLS][$i][5][CAPTION]="";
							$dataArr[$i][MESSAGE4]='';
						}
						$i++; 
						
					}
       
				
				}
				
			}//company
	
		}//Service Booking For Knitting if con;

		//Work Order for AOP Approval
		if($woAOP_page_id==1177){
			$page_id=1177;
			$approval_type=0;
			
			foreach($company_arr as $company_id=>$company_name){
				$user_sequence_no=$user_sequence_no_array[$page_id][$company_id];
				$min_sequence_no=$min_sequence_no_array[$page_id][$company_id];
				//$maxUserNo=$max_sequence_no_array[$page_id][$company_id];
				$buyer_ids_array=$user_buyer_ids_array[$page_id][$company_id];
				
				if($approval_type==0 && $user_sequence_no !=""){
					
					if($this->db->dbdriver == 'mysqli')
					{
						$sequence_no=return_field_value("max(sequence_no) as seq","electronic_approval_setup","company_id=$company_id and page_id=$page_id and sequence_no<$user_sequence_no and bypass=2 and buyer_id='' and is_deleted=0","seq");
					}
					else
					{
						$sequence_no=return_field_value("max(sequence_no) as seq","electronic_approval_setup","company_id=$company_id and page_id=$page_id and sequence_no<$user_sequence_no and bypass=2 and buyer_id is null and is_deleted=0","seq");                      
					}
					// echo $user_sequence_no.'=='.$min_sequence_no;
					if($user_sequence_no==$min_sequence_no) //  && $approval_necessity_setup==1
					{ 				
						$buyer_ids = $buyer_ids_array[$user_id]['u'];
						if(trim($buyer_ids) == "") $buyer_id_cond = ""; else $buyer_id_cond = " and a.buyer_id in($buyer_ids)";			
									  
					 
						$sql= "SELECT a.id,a.booking_no_prefix_num as SYSTEM_NO,a.booking_no,a.BUYER_ID, a.supplier_id as supplier_name, a.is_approved,'0' as approval_id,
						a.job_no as po_job_no, a.BOOKING_DATE, a.delivery_date 
						FROM wo_booking_mst a
						WHERE a.booking_type=3 and a.company_id = $company_id and  a.status_active=1 and a.is_deleted=0 and a.process=35  and a.ready_to_approved = 1 and a.is_approved=$approval_type  $buyer_id_cond $where_id_cond
						
						order by a.booking_no_prefix_num";
					 
					   // echo $sql;
					}
					else if($sequence_no == "") //  && $approval_necessity_setup==1
					{               
						$buyer_ids=$buyer_ids_array[$user_id]['u'];
						if(trim($buyer_ids) == "") $buyer_id_cond = ""; else $buyer_id_cond = " and a.buyer_id in($buyer_ids)";
						if($this->db->dbdriver == 'mysqli')
						{
							$seqSql="SELECT group_concat(sequence_no) as SEQUENCE_NO_BY, group_concat(case when bypass=2 and buyer_id<>'' then buyer_id end) as BUYER_IDS 
								FROM electronic_approval_setup 
								WHERE company_id=$company_id and page_id=$page_id and sequence_no<$user_sequence_no and is_deleted=0";
							$seqData=sql_select($seqSql);
							$sequence_no_by = $seqData[0]->SEQUENCE_NO_BY;
							$buyerIds = $seqData[0]->BUYER_IDS;
							if($buyerIds == "") $buyerIds_cond = ""; else $buyerIds_cond = " and a.buyer_id not in($buyerIds)";
						}
						else
						{   
							$seqSql="SELECT LISTAGG(sequence_no, ',') WITHIN GROUP (ORDER BY sequence_no) as SEQUENCE_NO_BY, LISTAGG(case when bypass=2 and buyer_id is not null then buyer_id end, ',') WITHIN GROUP (ORDER BY null) as BUYER_IDS 
							FROM electronic_approval_setup 
							WHERE company_id=$company_id and page_id=$page_id and sequence_no<$user_sequence_no and is_deleted=0";
							$seqData=sql_select($seqSql);
							$sequence_no_by=$seqData[0]->SEQUENCE_NO_BY;
							$buyerIds=$seqData[0]->BUYER_IDS;
							if($buyerIds == "") $buyerIds_cond = ""; else $buyerIds_cond = " and a.buyer_id not in($buyerIds)";				
						}
			
						$sql= "SELECT a.booking_no_prefix_num as SYSTEM_NO,a.booking_no,a.BUYER_ID,  a.supplier_id as supplier_name, a.job_no as po_job_no , a.BOOKING_DATE, a.delivery_date
						FROM wo_booking_mst a 
						WHERE  a.booking_type=3 and a.company_id = $company_id and  a.status_active=1 and a.is_deleted=0 and a.process=35  and a.ready_to_approved = 1 and a.is_approved=$approval_type  $buyer_id_cond $where_id_cond  order by a.booking_no_prefix_num";	 
						
			
					}
					else
					{   
						$buyer_ids = $buyer_ids_array[$user_id]['u'];
						if(trim($buyer_ids) == "") { 
							$buyer_id_cond="";                 
						} else {
							$buyer_id_cond=" and a.buyer_id in($buyer_ids)";
						}
								  
						$user_sequence_no=$user_sequence_no-1;
						
						$sequence_no_by_pass=''; // understand 
						if($sequence_no == $user_sequence_no) //  && $approval_necessity_setup==1
						{                  
						   
							if($this->db->dbdriver == 'mysqli')
							{
								$sequence_no_by_pass=return_field_value("group_concat(sequence_no)","electronic_approval_setup","company_id=$company_id and page_id=$page_id and sequence_no between $sequence_no and $user_sequence_no and is_deleted=0");
							}
							else
							{
								$sequence_no_by_pass=return_field_value("LISTAGG(sequence_no, ',') WITHIN GROUP (ORDER BY sequence_no) as sequence_no","electronic_approval_setup","company_id=$company_id and page_id=$page_id and sequence_no between $sequence_no and $user_sequence_no and is_deleted=0","sequence_no");	
							}
							
							if($sequence_no_by_pass=="") {
								$sequence_no_cond=" and c.sequence_no='$sequence_no'";    
							}
							else {
								$sequence_no_cond=" and c.sequence_no in ($sequence_no_by_pass)";
							}
										 
			
							$sql= "SELECT a.id,a.booking_no_prefix_num as SYSTEM_NO,a.booking_no,a.BUYER_ID,  a.supplier_id as supplier_name, a.is_approved,c.id as approval_id, a.job_no as po_job_no,a.BOOKING_DATE, a.delivery_date
							FROM wo_booking_mst a,  approval_history c  
							WHERE a.id=c.mst_id and c.entry_form=162 and c.current_approval_status=1 and  a.booking_type=3 and a.company_id = $company_id and  a.status_active=1 and a.is_deleted=0 and a.process=35  and a.ready_to_approved = 1 and a.is_approved=1  $where_id_cond  $sequence_no_cond order by a.booking_no_prefix_num";		 
						} 
						else 
						{         
							$sql= "SELECT a.id,a.booking_no_prefix_num as SYSTEM_NO,a.booking_no,a.BUYER_ID,  a.supplier_id as supplier_name, a.is_approved,c.id as approval_id, a.job_no as po_job_no, a.BOOKING_DATE, a.delivery_date
							FROM wo_booking_mst a, approval_history c  
							WHERE a.id=c.mst_id and c.entry_form=162 and c.current_approval_status=1 and a.booking_type=3 and a.company_id = $company_id and  a.status_active=1 and a.is_deleted=0 and a.process=35  and a.ready_to_approved = 1 and a.is_approved=1  $where_id_cond  $sequence_no_cond order by a.booking_no_prefix_num";
						
						}
					} 
			
				   	$nameArray=sql_select( $sql ); 
					foreach ($nameArray as $row)
					{
						
						if($select_no){	
							$dataArr[PAGE_ID]=$page_id;
							$dataArr[APP_TITLE]='Work Order for AOP Approval';
							$dataArr[COMPANY_ID]=$company_id;
							$dataArr[APP_ID]=$row->ID;
							$dataArr[MESSAGE1]=$row->SYSTEM_NO;
							$dataArr[MESSAGE2]=($row->BOOKING_DATE)?$row->BOOKING_DATE:'00-00-0000';
							$dataArr[MESSAGE3]=($buyer_arr[$row->BUYER_ID])?$buyer_arr[$row->BUYER_ID]:'';
							$dataArr[MESSAGE4]='';
						}
						else
						{
							$dataArr[$i][PAGE_ID]=$page_id;
							$dataArr[$i][APP_TITLE]='Work Order for AOP Approval';
							$dataArr[$i][COMPANY_ID]=$company_id;
							$dataArr[$i][APP_ID]=$row->ID;
							
							//$dataArr[DTLS][$i][2][CAPTION]="STYLE REF NO";
							$dataArr[$i][MESSAGE1]=$row->SYSTEM_NO;
				
							//$dataArr[DTLS][$i][3][CAPTION]="QUOTATION DATE";
							$dataArr[$i][MESSAGE2]=($row->BOOKING_DATE)?$row->BOOKING_DATE:'00-00-0000';
							
							//$dataArr[DTLS][$i][4][CAPTION]="BUYER NAME";
							$dataArr[$i][MESSAGE3]=($buyer_arr[$row->BUYER_ID])?$buyer_arr[$row->BUYER_ID]:'';
							
							//$dataArr[DTLS][$i][5][CAPTION]="";
							$dataArr[$i][MESSAGE4]='';
						}
						$i++; 
						
					}
       
				
					
				}
			}//company con
			
			
		}//Work Order for AOP Approval if con;
		
		//Quick Costing Approval
		if($qc_page_id==1620){
			$page_id=1620;
			$approval_type=0;
			 
			
			//foreach($company_arr as $company_id=>$company_name){
				//$user_sequence_no=$user_sequence_no_array[$page_id][$company_id];
				//$min_sequence_no=$min_sequence_no_array[$page_id][$company_id];
				//$maxUserNo=$max_sequence_no_array[$page_id][$company_id];
				//$buyer_ids_array=$user_buyer_ids_array[$page_id][$company_id];
				
				
				$user_sequence_no=return_field_value("sequence_no","electronic_approval_setup"," page_id=$page_id and user_id=$user_id and is_deleted=0");
				$min_sequence_no=return_field_value("min(sequence_no)","electronic_approval_setup"," page_id=$page_id and is_deleted=0");
				$buyer_ids_array = array();
				$buyerData = sql_select("select USER_ID, SEQUENCE_NO, BUYER_ID from electronic_approval_setup where page_id=$page_id and is_deleted=0 ");
				
				
				
				foreach($buyerData as $row)
				{
					$buyer_ids_array[$row->USER_ID]['u']=$row->BUYER_ID;
					$buyer_ids_array[$row->SEQUENCE_NO]['s']=$row->BUYER_ID;
				}
				
				
				if($approval_type==0 && $user_sequence_no !=""){
					
					if($this->db->dbdriver == 'mysqli')
					{
						$sequence_no = return_field_value("max(sequence_no) as seq","electronic_approval_setup"," page_id=$page_id and sequence_no<$user_sequence_no and bypass = 2 and buyer_id='' and is_deleted=0","seq");
					}
					else
					{
						$sequence_no = return_field_value("max(sequence_no) as seq","electronic_approval_setup"," page_id=$page_id and sequence_no<$user_sequence_no and bypass=2 and buyer_id is null and is_deleted=0","seq");
					}
			
					if($user_sequence_no==$min_sequence_no)
					{
						$buyer_ids = $buyer_ids_array[$user_id]['u'];
						if($buyer_ids=="") $buyer_id_cond2=""; else $buyer_id_cond2=" and a.buyer_id in($buyer_ids)";
						//$buyer_id_cond=""; $buyer_id_cond2=""; 
						$sql="select a.id,a.qc_no, b.tot_fob_cost,a.cost_sheet_id, a.cost_sheet_no, a.style_ref, a.BUYER_ID, a.DELIVERY_DATE, a.exchange_rate, a.offer_qty, a.COSTING_DATE,a.revise_no, a.option_id,c.job_id, c.id as confirm_id from qc_mst a, qc_tot_cost_summary b, qc_confirm_mst c where a.qc_no=b.mst_id and b.mst_id=c.cost_sheet_id and a.approved in (0,2) and c.ready_to_approve=1 and a.status_active=1 and a.is_deleted=0 and  b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 $buyer_id_cond $buyer_id_cond2 $date_cond  $job_year_cond $style_cond $txt_costshit_no ";
			//and c.job_id>0
				
					}
					else if($sequence_no == "")
					{
						$buyer_ids=$buyer_ids_array[$user_id]['u'];
						if($buyer_ids=="") $buyer_id_cond2=""; else $buyer_id_cond2=" and a.buyer_id in($buyer_ids)";
			
						if($buyer_ids=="") $buyer_id_cond3=""; else $buyer_id_cond3=" and c.buyer_id in($buyer_ids)";
			
						//$buyer_id_cond=""; $buyer_id_cond2=""; $buyer_id_cond3=""; 
			
						$seqSql="select SEQUENCE_NO, BYPASS, BUYER_ID from electronic_approval_setup where company_id=0 and page_id=$page_id and sequence_no<$user_sequence_no and is_deleted=0 order by sequence_no desc";
						$seqData=sql_select($seqSql);
			
			
			
						$buyerIds=''; $sequence_no_by_yes=''; $sequence_no_by_no=''; $approved_string=''; $check_buyerIds_arr=array();
						foreach($seqData as $sRow)
						{
							if($sRow->BYPASS==2)
							{
								$sequence_no_by_no.=$sRow->SEQUENCE_NO.",";
								if($sRow->BUYER_ID!="")
								{
									$buyerIds.=$sRow->BUYER_ID.",";
									$buyer_id_arr=explode(",",$sRow->BUYER_ID);
									$result=array_diff($buyer_id_arr,$check_buyerIds_arr);
									if(count($result)>0)
									{
										$query_string.=" (b.sequence_no=".$sRow->SEQUENCE_NO." and c.buyer_name in(".implode(",",$result).")) or ";
									}
									$check_buyerIds_arr=array_unique(array_merge($check_buyerIds_arr,$buyer_id_arr));
								}
							}
							else
							{
								$sequence_no_by_yes.=$sRow->SEQUENCE_NO.",";
							}
						}
			
						$buyerIds=chop($buyerIds,',');
						if($buyerIds=="")
						{
							$buyerIds_cond=""; $seqCond="";
						}
						else
						{
							$buyerIds_cond=" and a.buyer_id not in($buyerIds)"; $seqCond=" and (".chop($query_string,'or ').")";
						}
						$sequence_no_by_no=chop($sequence_no_by_no,',');
						$sequence_no_by_yes=chop($sequence_no_by_yes,',');
			
						if($sequence_no_by_yes=="") $sequence_no_by_yes=0;
						if($sequence_no_by_no=="") $sequence_no_by_no=0;
			
						$qc_id='';
						$qc_id_sql="select distinct (b.mst_id) as QC_ID from qc_mst a, approval_history b where a.id=b.mst_id and b.sequence_no in ($sequence_no_by_no) and b.entry_form=36 and b.current_approval_status=1 $buyer_id_cond3 $seqCond
						union
						select distinct (b.mst_id) as QC_ID from qc_mst a, approval_history b where a.id=b.mst_id  and b.sequence_no in ($sequence_no_by_yes) and b.entry_form=36 and b.current_approval_status=1 $buyer_id_cond3";
						$bResult=sql_select($qc_id_sql);
						foreach($bResult as $bRow)
						{
							$qc_id.=$bRow->QC_ID.",";
						}
			
						$qc_id=chop($qc_id,',');
			
						$qc_id_app_sql=sql_select("select b.mst_id as QC_ID from qc_mst a, approval_history b
						where a.id=b.mst_id  and b.sequence_no=$user_sequence_no and b.entry_form=36  and b.current_approval_status=1");
			
						$qc_id_app_byuser_arr=array();
						foreach($qc_id_app_sql as $inf)
						{
							$qc_id_app_byuser_arr[$inf->QC_ID]=$inf->QC_ID;
						}
			
						$qc_id_app_byuser=implode(",",$qc_id_app_byuser_arr);
						
			
						$qc_id_app_byuser=chop($qc_id_app_byuser,',');
						$result=array_diff(explode(',',$qc_id),explode(',',$qc_id_app_byuser));
						$qc_id=implode(",",$result);
						//echo $pre_cost_id;die;
						$qc_id_cond="";
			
						if($qc_id_app_byuser!="")
						{
							$qc_id_app_byuser_arr=explode(",",$qc_id_app_byuser);
							if(count($qc_id_app_byuser_arr)>995)
							{
								$qc_id_app_byuser_arr_chunk_arr=array_chunk(explode(",",$qc_id_app_byuser_arr),995) ;
								foreach($qc_id_app_byuser_arr_chunk_arr as $chunk_arr)
								{
									$chunk_arr_value=implode(",",$chunk_arr);
									$qc_id_cond.=" and b.id not in($chunk_arr_value)";
								}
							}
							else
							{
								$qc_id_cond=" and b.id not in($pre_cost_id_app_byuser)";
							}
						}
						else $qc_id_cond="";
			
						$sql="select a.id, a.qc_no, b.tot_fob_cost,a.cost_sheet_id,a.cost_sheet_no, a.style_ref, a.buyer_id, a.DELIVERY_DATE, a.exchange_rate, a.offer_qty, a.COSTING_DATE,a.revise_no, a.option_id,0 as approval_id,
						a.approved,a.inserted_by,a.revise_no, a.option_id,c.job_id, c.id as confirm_id from qc_mst a, qc_tot_cost_summary b,qc_confirm_mst c where a.qc_no=b.mst_id and b.mst_id=c.cost_sheet_id   and a.status_active=1 and a.is_deleted=0  and a.approved in (0,2) and c.ready_to_approve=1 and b.status_active=1 and b.is_deleted=0 and   c.status_active=1 and c.is_deleted=0 $qc_id_cond $buyer_id_cond $buyer_id_cond2 $date_cond  $job_year_cond $style_cond $txt_costshit_no ";
						//echo $sql;die;//and c.job_id>0
						if($qc_id!="")
						{
							$sql.=" union all
							select a.id,a.qc_no, b.tot_fob_cost,a.cost_sheet_id,a.cost_sheet_no, a.style_ref, a.buyer_id, a.DELIVERY_DATE, a.exchange_rate, a.offer_qty, a.COSTING_DATE,a.revise_no, a.option_id,0 as approval_id,
						a.approved,a.inserted_by,a.revise_no, a.option_id,c.job_id, c.id as confirm_id from qc_mst a, qc_tot_cost_summary b, qc_confirm_mst c
							where  a.qc_no=b.mst_id and b.mst_id=c.cost_sheet_id  and a.status_active=1 and a.is_deleted=0  and a.approved in (1,3) and c.ready_to_approve=1 and  b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0
							  and (a.id in($qc_id)) $buyer_id_cond $buyer_id_cond2 $date_cond  $job_year_cond $style_cond $txt_costshit_no";
							  //and c.job_id>0 
						}
					
				}
				
					$nameArray=sql_select( $sql );
					foreach ($nameArray as $row)
					{
						
						if($select_no){	
							$dataArr[PAGE_ID]=$page_id;
							$dataArr[APP_TITLE]='Quick Costing Approval';
							$dataArr[COMPANY_ID]=$company_id;
							$dataArr[APP_ID]=$row->ID;
							$dataArr[MESSAGE1]=($row->STYLE_REF)?$row->STYLE_REF:'';
							$dataArr[MESSAGE2]=($row->COSTING_DATE)?$row->COSTING_DATE:'00-00-0000';
							$dataArr[MESSAGE3]=($buyer_arr[$row->BUYER_ID])?$buyer_arr[$row->BUYER_ID]:'';
							$dataArr[MESSAGE4]='';
						}
						else
						{
							$dataArr[$i][PAGE_ID]=$page_id;
							$dataArr[$i][APP_TITLE]='Quick Costing Approval';
							$dataArr[$i][COMPANY_ID]=$company_id;
							$dataArr[$i][APP_ID]=$row->ID;
							
							//$dataArr[DTLS][$i][2][CAPTION]="STYLE REF NO";
							$dataArr[$i][MESSAGE1]=($row->STYLE_REF)?$row->STYLE_REF:'';
				
							//$dataArr[DTLS][$i][3][CAPTION]="QUOTATION DATE";
							$dataArr[$i][MESSAGE2]=($row->COSTING_DATE)?$row->COSTING_DATE:'00-00-0000';
							
							//$dataArr[DTLS][$i][4][CAPTION]="BUYER NAME";
							$dataArr[$i][MESSAGE3]=($buyer_arr[$row->BUYER_ID])?$buyer_arr[$row->BUYER_ID]:'';
							
							//$dataArr[DTLS][$i][5][CAPTION]="";
							$dataArr[$i][MESSAGE4]='';
						}
						$i++; 
						
					}
				
				
				
				}
				
			// }//company con [quick cost app a company nai]
			
			
		}//Quick Costing Approval if con;
	
		//Dyeing Batch Approval
		if($db_page_id==616){
			$page_id=616;
			$approval_type=0;
			
			
			foreach($company_arr as $company_id=>$company_name){
				$user_sequence_no=$user_sequence_no_array[$page_id][$company_id];
				$min_sequence_no=$min_sequence_no_array[$page_id][$company_id];
				//$maxUserNo=$max_sequence_no_array[$page_id][$company_id];
				//$buyer_ids_array=$user_buyer_ids_array[$page_id][$company_id];
					
				if($select_no){$batch_cond=" and a.id=$select_no";}
				
				if($approval_type==0 && $user_sequence_no !=""){
					
					$batch_cond.=" and a.entry_form in(0,36)"; 
					$approve_form=" and b.entry_form in (0,36) "; 
								
					$sequence_no=return_field_value("max(sequence_no)","electronic_approval_setup","page_id=$page_id and sequence_no<$user_sequence_no and bypass=2 
					and is_deleted=0");
				
					if($user_sequence_no==$min_sequence_no) // check sequence
					{
						$sql="SELECT a.ID,  a.booking_no_id,a.batch_no,a.BOOKING_NO,a.extention_no, a.color_id, a.company_id, a.batch_weight, a.BATCH_DATE,
						a.batch_against, a.batch_for, a.entry_form, a.batch_no, '0' as approval_id, a.is_approved ,to_char(a.insert_date,'YY') as year
						from pro_batch_create_mst a
						where  a.company_id=$company_id  and a.is_deleted=0 and a.status_active=1 and a.ready_to_approved=1 and 
						a.is_approved=$approval_type  $date_cond $batch_cond";
					}
					else if($sequence_no=="")
					{
						if($this->db->dbdriver == 'mysqli')
						{
							$sequence_no_by=return_field_value("group_concat(sequence_no)","electronic_approval_setup","page_id=$page_id
							and sequence_no<$user_sequence_no and bypass=1 and is_deleted=0");
							$booking_id=return_field_value("group_concat(distinct(mst_id)) as batch_id","pro_batch_create_mst a, approval_history b","a.id=b.mst_id
							and a.company_id=$company_id and b.sequence_no in ($sequence_no_by) and b.entry_form=16 and b.current_approval_status=1","batch_id");
							$booking_id_app_byuser=return_field_value("group_concat(distinct(mst_id)) as batch_id","pro_batch_create_mst a, approval_history b",
							"a.id=b.mst_id and a.company_id=$company_id  and b.sequence_no=$user_sequence_no and b.entry_form=16 and 
							b.current_approval_status=1","batch_id");
						}
						else
						{
							$sequence_no_by=return_field_value("LISTAGG(sequence_no, ',') WITHIN GROUP (ORDER BY sequence_no) as sequence_no",
							"electronic_approval_setup","page_id=$page_id and sequence_no<$user_sequence_no and bypass=1 and is_deleted=0","sequence_no");
							
							$booking_id=return_field_value("LISTAGG(mst_id, ',') WITHIN GROUP (ORDER BY mst_id) as batch_id","pro_batch_create_mst a,
							approval_history b","a.id=b.mst_id and a.company_id=$company_id  and b.sequence_no in ($sequence_no_by) and b.entry_form=16 
							and b.current_approval_status=1","batch_id");
							$booking_id=implode(",",array_unique(explode(",",$booking_id)));
							
							$booking_id_app_byuser=return_field_value("LISTAGG(mst_id, ',') WITHIN GROUP (ORDER BY mst_id) as batch_id","pro_batch_create_mst a,
							approval_history b","a.id=b.mst_id and a.company_id=$company_id and b.sequence_no=$user_sequence_no and b.entry_form=16 and
							b.current_approval_status=1","batch_id");
							$booking_id_app_byuser=implode(",",array_unique(explode(",",$booking_id_app_byuser)));
						}
						
						$result=array_diff(explode(',',$booking_id),explode(',',$booking_id_app_byuser));
						$booking_id=implode(",",$result);
						
						$booking_id_cond="";
						if($booking_id_app_byuser!="") $booking_id_cond=" and a.id not in($booking_id_app_byuser)";
						
						$sql="SELECT a.ID,a.booking_no_id,a.batch_no, a.BOOKING_NO, a.extention_no, a.color_id, a.company_id, a.batch_weight, a.BATCH_DATE,
						a.batch_against, a.batch_for, a.entry_form, a.batch_no, '0' as approval_id, a.is_approved,to_char(a.insert_date,'YY') as year from pro_batch_create_mst a 
						where  a.company_id=$company_id  and a.is_deleted=0 and a.status_active=1 and a.ready_to_approved=1 
						and a.is_approved=$approval_type  $date_cond $batch_cond $booking_id_cond";
						if($booking_id!="")
						{
							$sql.="UNION ALL
							SELECT a.ID,a.booking_no_id,a.batch_no, a.BOOKING_NO, a.extention_no, a.color_id, a.company_id, a.batch_weight, a.BATCH_DATE, 
							a.batch_against,a.batch_for, a.entry_form, a.batch_no, '0' as approval_id, a.is_approved,to_char(a.insert_date,'YY') as year from pro_batch_create_mst a 
							where  a.company_id=$company_id and a.is_deleted=0 and a.status_active=1 and a.ready_to_approved=1 and 
							a.is_approved=1 and a.id in($booking_id) $date_cond $batch_cond";
						}
					}
					else
					{
							
						$user_sequence_no=$user_sequence_no-1;
						
						if($sequence_no==$user_sequence_no) $sequence_no_by_pass=''; 
						else
						{
							if($this->db->dbdriver == 'mysqli')
							{
								$sequence_no_by_pass=return_field_value("group_concat(sequence_no)","electronic_approval_setup","page_id=$page_id and
								sequence_no between $sequence_no and $user_sequence_no and bypass=1 and is_deleted=0");
							}
							else
							{
								$sequence_no_by_pass=return_field_value("LISTAGG(sequence_no, ',') WITHIN GROUP (ORDER BY sequence_no) as sequence_no",
								"electronic_approval_setup","page_id=$page_id and sequence_no between $sequence_no and $user_sequence_no and bypass=1 and 
								is_deleted=0","sequence_no");	
							}
						}
						if($sequence_no_by_pass=="") $sequence_no_cond=" and b.sequence_no='$sequence_no'";
						else $sequence_no_cond=" and (b.sequence_no='$sequence_no' or b.sequence_no in ($sequence_no_by_pass))";
						
						$sql="SELECT a.ID,  a.booking_no_id,a.batch_no, a.BOOKING_NO, a.extention_no, a.color_id, a.company_id, a.batch_weight, a.BATCH_DATE,
						a.batch_against,a.batch_for, a.entry_form, a.batch_no, b.id as approval_id, a.is_approved,b.sequence_no,to_char(a.insert_date,'YY') as year from pro_batch_create_mst a,
						approval_history b 
						where a.id=b.mst_id and  a.company_id=$company_id and b.entry_form=16 and a.is_deleted=0 and a.status_active=1 and
						a.ready_to_approved=1 and a.is_approved=1 and b.current_approval_status=1  $sequence_no_cond $date_cond $batch_cond";
					}
					
					
					$nameArray=sql_select( $sql ); 
					foreach ($nameArray as $row)
					{
						
						if($select_no){	
							$dataArr[PAGE_ID]=$page_id;
							$dataArr[APP_TITLE]='Dyeing Batch Approval';
							$dataArr[COMPANY_ID]=$company_id;
							$dataArr[APP_ID]=$row->ID;
							$dataArr[MESSAGE1]=$row->BOOKING_NO;
							$dataArr[MESSAGE2]=($row->BATCH_DATE)?$row->BATCH_DATE:'00-00-0000';
							$dataArr[MESSAGE3]=($buyer_arr[$row->BUYER_ID])?$buyer_arr[$row->BUYER_ID]:'';
							$dataArr[MESSAGE4]='';
						}
						else
						{
							$dataArr[$i][PAGE_ID]=$page_id;
							$dataArr[$i][APP_TITLE]='Dyeing Batch Approval';
							$dataArr[$i][COMPANY_ID]=$company_id;
							$dataArr[$i][APP_ID]=$row->ID;
							
							//$dataArr[DTLS][$i][2][CAPTION]="STYLE REF NO";
							$dataArr[$i][MESSAGE1]=$row->BOOKING_NO;
				
							//$dataArr[DTLS][$i][3][CAPTION]="QUOTATION DATE";
							$dataArr[$i][MESSAGE2]=($row->BATCH_DATE)?$row->BATCH_DATE:'00-00-0000';
							
							//$dataArr[DTLS][$i][4][CAPTION]="BUYER NAME";
							$dataArr[$i][MESSAGE3]=($buyer_arr[$row->BUYER_ID])?$buyer_arr[$row->BUYER_ID]:'';
							
							//$dataArr[DTLS][$i][5][CAPTION]="";
							$dataArr[$i][MESSAGE4]='';
						}
						$i++; 
						
					}
       

				
				}
				
			}//end company foreach;
		
		}//Dyeing Batch Approval if con;
		
		//Trims Booking Approval [With Order]
		if($tb_with_order_page_id==336000000){
			$page_id=336;
			$approval_type=0;
			$cbo_booking_type=1;
			
			
			foreach($company_arr as $company_id=>$company_name){
				$user_sequence_no=$user_sequence_no_array[$page_id][$company_id];
				$min_sequence_no=$min_sequence_no_array[$page_id][$company_id];
				//$maxUserNo=$max_sequence_no_array[$page_id][$company_id];
				$buyer_ids_array=$user_buyer_ids_array[$page_id][$company_id];
			
				
				if($approval_type==0 && $user_sequence_no !=""){
					if($select_no){$booking_cond=" and a.id=$select_no";}
					
						if($this->db->dbdriver == 'mysqli')
						{
							$sequence_no=return_field_value("max(sequence_no) as seq","electronic_approval_setup","company_id=$company_id and page_id=$page_id and sequence_no<$user_sequence_no and bypass=2 and buyer_id='' and is_deleted=0","seq");
						}
						else
						{
							$sequence_no=return_field_value("max(sequence_no) as seq","electronic_approval_setup","company_id=$company_id and page_id=$page_id and sequence_no<$user_sequence_no and buyer_id is null and bypass=2 and is_deleted=0","seq");
						}
					  
						if($user_sequence_no==$min_sequence_no) // first user
						{
							$buyer_ids=$buyer_ids_array[$user_id]['u'];
							if($buyer_ids=="") { $buyer_id_cond2=""; $buyer_id_condnon2="";}
							else {$buyer_id_cond2=" and c.buyer_name in($buyer_ids)"; $buyer_id_condnon2=" and a.buyer_id in($buyer_ids)";}
							if($cbo_booking_type==1) //With Order
							{
								if($this->db->dbdriver == 'mysqli')
								{
									$sql="select a.id, a.entry_form, a.booking_no_prefix_num as prefix_num, a.booking_no, a.item_category, a.fabric_source, a.entry_form, a.company_id, a.booking_type, a.is_short, a.pay_mode, a.supplier_id, a.delivery_date, a.BOOKING_DATE, '0' as approval_id, a.job_no, a.is_approved, a.po_break_down_id from wo_booking_mst a, wo_booking_dtls b,wo_po_details_master c where  a.booking_no=b.booking_no and  b.job_no=c.job_no and a.company_id=$company_id and a.item_category in(4) and a.is_deleted=0 and a.status_active=1 and b.status_active=1 and b.is_deleted=0 and a.is_approved=$approval_type and a.ready_to_approved=1 $buyer_id_cond2 $buyer_id_cond $buyer_id_cond3 $date_cond $booking_cond $booking_year_cond group by a.id";
								}
								else
								{
								  $sql="select a.id, a.entry_form, a.booking_no_prefix_num as prefix_num, a.booking_no, a.item_category, a.fabric_source, a.company_id, a.entry_form, a.booking_type, a.is_short, a.pay_mode, a.supplier_id, a.delivery_date, a.BOOKING_DATE, '0' as approval_id, a.job_no, a.is_approved, a.po_break_down_id from wo_booking_mst a, wo_booking_dtls b,wo_po_details_master c where a.booking_no=b.booking_no and b.job_no=c.job_no and a.company_id=$company_id and a.item_category in(4) and a.is_deleted=0 and a.status_active=1 and b.status_active=1 and b.is_deleted=0 and a.is_approved=$approval_type and a.ready_to_approved=1 $buyer_id_cond2 $buyer_id_cond $buyer_id_cond3 $date_cond $booking_cond $booking_year_cond group by a.id, a.booking_no_prefix_num, a.booking_no, a.entry_form, a.item_category, a.fabric_source, a.company_id, a.booking_type, a.entry_form, a.is_short, a.buyer_id, a.pay_mode, a.supplier_id, a.delivery_date, a.BOOKING_DATE,a.job_no, a.is_approved, a.po_break_down_id order by a.booking_no_prefix_num";
								}
								//echo $sql;//die("with hot pic");
							}
							else  //WithOut Order
							{
								if($this->db->dbdriver == 'mysqli')
								{
									$sql="select a.id, a.booking_no_prefix_num as prefix_num,a.booking_no, a.item_category, a.fabric_source, a.company_id, a.booking_type, a.is_short, a.BUYER_ID, a.pay_mode, a.supplier_id, a.delivery_date, a.BOOKING_DATE, '0' as approval_id, a.is_approved, a.po_break_down_id from wo_non_ord_samp_booking_mst a,wo_non_ord_samp_booking_dtls b where a.booking_no=b.booking_no and a.company_id=$company_id and a.item_category in(4) and a.booking_type=5 and a.is_deleted=0 and a.status_active=1 and b.status_active=1 and b.is_deleted=0 and a.is_approved=$approval_type and a.ready_to_approved=1 $buyer_id_condnon2 $buyer_id_condnon $date_cond $booking_cond $booking_year_cond group by a.id ";
								}
								else
								{
									$sql="select a.id, a.booking_no_prefix_num as prefix_num, a.booking_no, a.item_category, a.fabric_source, a.company_id, a.booking_type, a.is_short, a.BUYER_ID, a.pay_mode, a.supplier_id, a.delivery_date, a.BOOKING_DATE, '0' as approval_id, a.is_approved, a.po_break_down_id from wo_non_ord_samp_booking_mst a,wo_non_ord_samp_booking_dtls b where a.booking_no=b.booking_no and a.company_id=$company_id and a.item_category in(4) and a.booking_type=5  and a.is_deleted=0 and a.status_active=1 and b.status_active=1 and b.is_deleted=0 and a.is_approved=$approval_type and a.ready_to_approved=1 $buyer_id_condnon2 $buyer_id_condnon $date_cond $booking_cond  $booking_year_cond group by a.id,a.booking_no_prefix_num, a.booking_no, a.item_category, a.fabric_source, a.company_id, a.booking_type, a.is_short, a.buyer_id, a.pay_mode, a.supplier_id, a.delivery_date, a.BOOKING_DATE,a.is_approved, a.po_break_down_id order by a.booking_no_prefix_num";
								}
								//echo $sql;die(" with cool ppic");
							}
						}
						else if($sequence_no=="") // Next user // bypass if previous user Yes 
						{
							// echo "bypass yes";die;
							$buyer_ids=$buyer_ids_array[$user_id]['u'];
							if($buyer_ids=="") {$buyer_id_cond2=""; $buyer_id_condnon2="";} 
							else {$buyer_id_cond2=" and c.buyer_name in($buyer_ids)"; $buyer_id_condnon2=" and a.buyer_id in($buyer_ids)";}
							
						
							if($this->db->dbdriver == 'mysqli')
							{
								$seqSql="select SEQUENCE_NO, BYPASS, BUYER_ID from electronic_approval_setup where company_id=$company_id and page_id=$page_id and sequence_no<$user_sequence_no and is_deleted=0 order by sequence_no desc";
								$seqData=sql_select($seqSql);
								
								
								$buyerIds=''; $sequence_no_by_yes=''; $sequence_no_by_no=''; $approved_string=''; $check_buyerIds_arr=array();
								foreach($seqData as $sRow)
								{
									if($sRow->BYPASS==2)
									{
										$sequence_no_by_no.=$sRow->SEQUENCE_NO.",";
										if($sRow->BUYER_ID!="") 
										{
											$buyerIds.=$sRow->BUYER_ID.",";
											
											$buyer_id_arr=explode(",",$sRow->BUYER_ID);
											$result=array_diff($buyer_id_arr,$check_buyerIds_arr);
											if(count($result)>0)
											{
												$query_string.=" (b.sequence_no=".$sRow->SEQUENCE_NO." and a.buyer_id in(".implode(",",$result).")) or ";
											}
											$check_buyerIds_arr=array_unique(array_merge($check_buyerIds_arr,$buyer_id_arr));
										}
									}
									else
									{
										$sequence_no_by_yes.=$sRow->SEQUENCE_NO.",";
									}
								}
								//var_dump($check_buyerIds_arr);die;
								$buyerIds=chop($buyerIds,',');
								if($buyerIds=="") 
								{
									$buyerIds_cond=""; 
									$seqCond="";
								}
								else 
								{
									$buyerIds_cond=" and a.buyer_id not in($buyerIds)";
									$seqCond=" and (".chop($query_string,'or ').")";
								}
								//echo $seqCond;die;
								$sequence_no_by_no=chop($sequence_no_by_no,',');
								$sequence_no_by_yes=chop($sequence_no_by_yes,',');
								
								if($sequence_no_by_yes=="") $sequence_no_by_yes=0;
								if($sequence_no_by_no=="") $sequence_no_by_no=0;
								
								
								if($cbo_booking_type==1) //With Order
								{
									$booking_id='';
									$booking_id_sql="select distinct (mst_id) as booking_id from wo_booking_mst a, approval_history b where a.id=b.mst_id and a.company_id=$company_id  and a.item_category in(4) and b.sequence_no in ($sequence_no_by_no) and b.entry_form=8 and b.current_approval_status=1 $buyer_id_condnon2  $seqCond
									union
									select distinct (mst_id) as booking_id from wo_booking_mst a, approval_history b where a.id=b.mst_id and a.company_id=$company_id and  a.item_category in(4) and b.sequence_no in ($sequence_no_by_yes) and b.entry_form=8 and b.current_approval_status=1 $buyer_id_condnon2 ";
									//echo $booking_id_sql;die;
									$bResult=sql_select($booking_id_sql);
									foreach($bResult as $bRow)
									{
										$booking_id.=$bRow[csf('booking_id')].",";
									}
									
									$booking_id=chop($booking_id,',');
									
									$booking_id_app_byuser=return_field_value("GROUP_CONCAT(mst_id, ',') as booking_id","wo_booking_mst a, approval_history b","a.id=b.mst_id and a.company_id=$company_id and a.booking_type=2 and a.item_category in(4) and b.sequence_no=$user_sequence_no and b.entry_form=8 and b.current_approval_status=1","booking_id");
									$booking_id_app_byuser=implode(",",array_unique(explode(",",$booking_id_app_byuser)));
									
									$result=array_diff(explode(',',$booking_id),explode(',',$booking_id_app_byuser));
									$booking_id=implode(",",$result);
									
									$booking_id_cond="";
									if($booking_id!="")
									{
										if($this->db->dbdriver != 'mysqli' && count($result)>999)
										{
											$booking_id_chunk_arr=array_chunk($result,999) ;
											foreach($booking_id_chunk_arr as $chunk_arr)
											{
												$chunk_arr_value=implode(",",$chunk_arr);	
												$bokIds_cond.=" a.id in($chunk_arr_value) or ";	
											}
											
											$booking_id_cond.=" and (".chop($bokIds_cond,'or ').")";			
											//echo $booking_id_cond;die;
										}
										else
										{
											$booking_id_cond=" and a.id in($booking_id)";	 
										}
									}
									else $booking_id_cond="";
									if($booking_id!=="")
									{
									$sql="SELECT a.id, a.entry_form,a.booking_no_prefix_num as prefix_num,a.booking_no, a.item_category, a.fabric_source, a.company_id, a.booking_type, a.is_short, a.supplier_id, a.delivery_date, a.BOOKING_DATE, '0' as approval_id, a.job_no, a.is_approved, a.po_break_down_id from wo_booking_mst a, wo_booking_dtls b,wo_po_details_master c where a.booking_no=b.booking_no and  b.job_no=c.job_no and a.company_id=$company_id and a.item_category in(4) and a.is_deleted=0 and a.status_active=1 and b.status_active=1 and b.is_deleted=0 and a.is_approved in(0) and a.ready_to_approved=1 $buyer_id_cond2 $buyer_id_cond $buyerIds_cond $date_cond $booking_cond group by a.id,a.booking_no_prefix_num,a.booking_no,a.entry_form,a.item_category, a.fabric_source, a.company_id, a.booking_type, a.is_short, a.buyer_id, a.supplier_id, a.delivery_date, a.BOOKING_DATE,a.job_no, a.is_approved, a.po_break_down_id 
										union all 
										SELECT a.id, a.entry_form,a.booking_no_prefix_num as prefix_num, a.booking_no, a.item_category, a.fabric_source, a.company_id, a.booking_type, a.is_short, a.supplier_id, a.delivery_date, a.BOOKING_DATE, '0' as approval_id, a.job_no, a.is_approved, a.po_break_down_id from wo_booking_mst a, wo_booking_dtls b,wo_po_details_master c where a.booking_no=b.booking_no and  b.job_no=c.job_no and a.company_id=$company_id and a.item_category in(4) and a.is_deleted=0 and a.status_active=1 and b.status_active=1 and b.is_deleted=0  and a.ready_to_approved=1 and a.is_approved in(0,3) and a.id in($booking_id) $buyer_id_cond2 $buyer_id_cond $date_cond $booking_cond group by a.id,a.booking_no_prefix_num,a.booking_no,a.entry_form,a.item_category, a.fabric_source, a.company_id, a.booking_type, a.is_short, a.buyer_id, a.supplier_id, a.delivery_date, a.BOOKING_DATE,a.job_no, a.is_approved, a.po_break_down_id order by prefix_num";
									}
									else 
									{
										$sql="SELECT a.id,a.entry_form,a.booking_no_prefix_num as prefix_num,a.booking_no, a.item_category, a.fabric_source, a.company_id, a.booking_type, a.is_short, a.supplier_id, a.delivery_date, a.BOOKING_DATE, '0' as approval_id, a.job_no, a.is_approved, a.po_break_down_id from wo_booking_mst a, wo_booking_dtls b,wo_po_details_master c where a.booking_no=b.booking_no and  b.job_no=c.job_no and a.company_id=$company_id and a.item_category in(4) and a.is_deleted=0 and a.status_active=1 and b.status_active=1 and b.is_deleted=0 and a.is_approved in(0,3) and a.ready_to_approved=1 $buyer_id_cond2 $buyer_id_cond  $buyerIds_cond $date_cond $booking_cond group by a.id,a.booking_no_prefix_num,a.booking_no,a.entry_form,a.item_category, a.fabric_source, a.company_id, a.booking_type, a.is_short, a.buyer_id, a.supplier_id, a.delivery_date, a.BOOKING_DATE,a.job_no, a.is_approved, a.po_break_down_id order by a.booking_no_prefix_num";
									}
									//echo $sql;	
								}
								else //Without Order
								{				
									$booking_id='';
									$booking_id_sql="select distinct (mst_id) as booking_id from wo_non_ord_samp_booking_mst a, approval_history b where a.id=b.mst_id and a.company_id=$company_id  and a.item_category in(4) and b.sequence_no in ($sequence_no_by_no) and b.entry_form=8 and b.current_approval_status=1 $buyer_id_condnon2  $seqCond
									union
									select distinct (mst_id) as booking_id from wo_non_ord_samp_booking_mst a, approval_history b where a.id=b.mst_id and a.company_id=$company_id and  a.item_category in(4) and b.sequence_no in ($sequence_no_by_yes) and b.entry_form=8 and b.current_approval_status=1 $buyer_id_condnon2 ";
									//echo $booking_id_sql;die;
									$bResult=sql_select($booking_id_sql);
									foreach($bResult as $bRow)
									{
										$booking_id.=$bRow[csf('booking_id')].",";
									}
									
									$booking_id=chop($booking_id,',');
									
									$booking_id_app_byuser=return_field_value("GROUP_CONCAT(mst_id, ',') as booking_id","wo_non_ord_samp_booking_mst a, approval_history b","a.id=b.mst_id and a.company_id=$company_id and a.booking_type=5 and a.item_category in(4) and b.sequence_no=$user_sequence_no and b.entry_form=8 and b.current_approval_status=1","booking_id");
									$booking_id_app_byuser=implode(",",array_unique(explode(",",$booking_id_app_byuser)));
									
									$result=array_diff(explode(',',$booking_id),explode(',',$booking_id_app_byuser));
									$booking_id=implode(",",$result);
									
									$booking_id_cond="";
									if($booking_id!="")
									{
										if($this->db->dbdriver != 'mysqli' && count($result)>999)
										{
											$booking_id_chunk_arr=array_chunk($result,999) ;
											foreach($booking_id_chunk_arr as $chunk_arr)
											{
												$chunk_arr_value=implode(",",$chunk_arr);	
												$bokIds_cond.=" a.id in($chunk_arr_value) or ";	
											}
											
											$booking_id_cond.=" and (".chop($bokIds_cond,'or ').")";			
											//echo $booking_id_cond;die;
										}
										else
										{
											$booking_id_cond=" and a.id in($booking_id)";	 
										}
									}
									else $booking_id_cond="";
									
									
									
									
									if($booking_id!=="")
									{
										
										$sql="select a.id, a.booking_no_prefix_num as prefix_num,a.booking_no, a.item_category, a.fabric_source, a.company_id, a.booking_type, a.is_short, a.BUYER_ID, a.supplier_id, a.delivery_date, a.BOOKING_DATE, '0' as approval_id, a.is_approved, a.po_break_down_id from wo_non_ord_samp_booking_mst a,wo_non_ord_samp_booking_dtls b where a.booking_no=b.booking_no and a.company_id=$company_id and a.item_category in(4) and a.booking_type=5 and a.is_deleted=0 and a.status_active=1 and b.status_active=1 and b.is_deleted=0 and a.is_approved=$approval_type and a.ready_to_approved=1 $buyer_id_condnon2 $buyer_id_condnon $date_cond $booking_cond group by a.id, a.booking_no_prefix_num,a.booking_no, a.item_category, a.fabric_source, a.company_id, a.booking_type, a.is_short, a.buyer_id, a.supplier_id, a.delivery_date, a.BOOKING_DATE,a.is_approved, a.po_break_down_id 
										union all 
										select a.id, a.booking_no_prefix_num as prefix_num,a.booking_no, a.item_category, a.fabric_source, a.company_id, a.booking_type, a.is_short, a.BUYER_ID, a.supplier_id, a.delivery_date, a.BOOKING_DATE, '0' as approval_id, a.is_approved, a.po_break_down_id from wo_non_ord_samp_booking_mst a,wo_non_ord_samp_booking_dtls b where a.booking_no=b.booking_no and a.company_id=$company_id and a.item_category in(4) and a.booking_type=5 and a.is_deleted=0 and a.status_active=1 and b.status_active=1 and b.is_deleted=0  and a.ready_to_approved=1 and a.is_approved in(1,0) $booking_id_cond $buyer_id_condnon2 $buyer_id_condnon $date_cond $booking_cond group by a.id, a.booking_no_prefix_num,a.booking_no, a.item_category, a.fabric_source, a.company_id, a.booking_type, a.is_short, a.buyer_id, a.supplier_id, a.delivery_date, a.BOOKING_DATE,a.is_approved, a.po_break_down_id order by prefix_num";
										
									}
									else 
									{
										
										  $sql="select a.id, a.booking_no_prefix_num as prefix_num,a.booking_no,a.item_category, a.fabric_source, a.company_id, a.booking_type, a.is_short, a.BUYER_ID, a.supplier_id, a.delivery_date, a.BOOKING_DATE, '0' as approval_id, a.is_approved, a.po_break_down_id from wo_non_ord_samp_booking_mst a,wo_non_ord_samp_booking_dtls b where a.booking_no=b.booking_no and a.company_id=$company_id and a.item_category in(4) and a.booking_type=5  and a.is_deleted=0 and a.status_active=1 and b.status_active=1 and b.is_deleted=0 and a.is_approved=$approval_type and a.ready_to_approved=1 $buyer_id_condnon2 $buyer_id_condnon $date_cond $booking_cond  group by a.id,a.booking_no_prefix_num,a.booking_no, a.item_category, a.fabric_source, a.company_id, a.booking_type, a.is_short, a.buyer_id, a.supplier_id, a.delivery_date, a.BOOKING_DATE,a.is_approved, a.po_break_down_id order by a.booking_no_prefix_num";
									}	
							
								}
								 //echo $sql;	
							}
							else
							{
								$seqSql="select SEQUENCE_NO, BYPASS, BUYER_ID from electronic_approval_setup where company_id=$company_id and page_id=$page_id and sequence_no<$user_sequence_no and is_deleted=0 order by sequence_no desc";
								//echo $seqSql;die;
								$seqData=sql_select($seqSql);
								
								
								$buyerIds=''; $sequence_no_by_yes=''; $sequence_no_by_no=''; $approved_string=''; $check_buyerIds_arr=array();
								foreach($seqData as $sRow)
								{
									if($sRow->BYPASS==2)
									{
										$sequence_no_by_no.=$sRow->SEQUENCE_NO.",";
										if($sRow->BUYER_ID!="") 
										{
											$buyerIds.=$sRow->BUYER_ID.",";
											
											$buyer_id_arr=explode(",",$sRow->BUYER_ID);
											$result=array_diff($buyer_id_arr,$check_buyerIds_arr);
											if(count($result)>0)
											{
												$query_string.=" (b.sequence_no=".$sRow->SEQUENCE_NO." and a.buyer_id in(".implode(",",$result).")) or ";
											}
											$check_buyerIds_arr=array_unique(array_merge($check_buyerIds_arr,$buyer_id_arr));
										}
									}
									else
									{
										$sequence_no_by_yes.=$sRow->SEQUENCE_NO.",";
									}
								}
								//var_dump($check_buyerIds_arr);die;
								$buyerIds=chop($buyerIds,',');
								if($buyerIds=="") 
								{
									$buyerIds_cond=""; 
									$seqCond="";
								}
								else 
								{
									$buyerIds_cond=" and a.buyer_id not in($buyerIds)";
									$seqCond=" and (".chop($query_string,'or ').")";
								}
								//echo $seqCond;die;
								$sequence_no_by_no=chop($sequence_no_by_no,',');
								$sequence_no_by_yes=chop($sequence_no_by_yes,',');
								
								if($sequence_no_by_yes=="") $sequence_no_by_yes=0;
								if($sequence_no_by_no=="") $sequence_no_by_no=0;
								
								
								if($cbo_booking_type==1) //With Order
								{
									$booking_id='';
									$booking_id_sql="select distinct (mst_id) as BOOKING_ID from wo_booking_mst a, approval_history b where a.id=b.mst_id and a.company_id=$company_id  and a.item_category in(4) and b.sequence_no in ($sequence_no_by_no) and b.entry_form=8 and b.current_approval_status=1 $buyer_id_condnon2  $seqCond
									union
									select distinct (mst_id) as booking_id from wo_booking_mst a, approval_history b where a.id=b.mst_id and a.company_id=$company_id and  a.item_category in(4) and b.sequence_no in ($sequence_no_by_yes) and b.entry_form=8 and b.current_approval_status=1 $buyer_id_condnon2 ";
									//echo $booking_id_sql;die;
									$bResult=sql_select($booking_id_sql);
									foreach($bResult as $bRow)
									{
										$booking_id.=$bRow->BOOKING_ID.",";
									}
									
									$booking_id=chop($booking_id,',');
									
									$booking_id_app_byuser=return_field_value("LISTAGG(mst_id, ',') WITHIN GROUP (ORDER BY mst_id) as booking_id","wo_booking_mst a, approval_history b","a.id=b.mst_id and a.company_id=$company_id and a.booking_type=2 and a.item_category in(4) and b.sequence_no=$user_sequence_no and b.entry_form=8 and b.current_approval_status=1","booking_id");
									$booking_id_app_byuser=implode(",",array_unique(explode(",",$booking_id_app_byuser)));
									
									$result=array_diff(explode(',',$booking_id),explode(',',$booking_id_app_byuser));
									$booking_id=implode(",",$result);
									
									$booking_id_cond="";
									if($booking_id!="")
									{
										if($this->db->dbdriver != 'mysqli' && count($result)>999)
										{
											$booking_id_chunk_arr=array_chunk($result,999) ;
											foreach($booking_id_chunk_arr as $chunk_arr)
											{
												$chunk_arr_value=implode(",",$chunk_arr);	
												$bokIds_cond.=" a.id in($chunk_arr_value) or ";	
											}
											
											$booking_id_cond.=" and (".chop($bokIds_cond,'or ').")";			
											//echo $booking_id_cond;die;
										}
										else
										{
											$booking_id_cond=" and a.id in($booking_id)";	 
										}
									}
									else $booking_id_cond="";
				
								
									// echo $booking_cond;
									$sequence_no_cond=" and d.sequence_no in ($user_sequence_no)";
				
									if($booking_id!="")
									{
									$sql="SELECT a.id, a.entry_form, a.booking_no_prefix_num as prefix_num, a.booking_no, a.item_category, a.fabric_source, a.company_id, a.booking_type, a.is_short, a.pay_mode, a.supplier_id, a.delivery_date, a.BOOKING_DATE, '0' as approval_id, a.job_no, a.is_approved, a.po_break_down_id from wo_booking_mst a, wo_booking_dtls b,wo_po_details_master c where a.booking_no=b.booking_no and  b.job_no=c.job_no and a.company_id=$company_id and a.item_category in(4) and a.is_deleted=0 and a.status_active=1 and b.status_active=1 and b.is_deleted=0 and a.is_approved in(0) and a.ready_to_approved=1 $buyer_id_cond2 $buyer_id_cond $buyerIds_cond $date_cond $booking_cond  group by a.id,a.booking_no_prefix_num,a.booking_no,a.entry_form,a.item_category, a.fabric_source, a.company_id, a.booking_type, a.is_short, a.buyer_id, a.pay_mode, a.supplier_id, a.delivery_date, a.BOOKING_DATE,a.job_no, a.is_approved, a.po_break_down_id 
										union all 
										select a.id, a.entry_form, a.booking_no_prefix_num as prefix_num, a.booking_no, a.item_category, a.fabric_source, a.company_id, a.booking_type, a.is_short, a.pay_mode, a.supplier_id, a.delivery_date, a.BOOKING_DATE, '0' as approval_id, a.job_no, a.is_approved, a.po_break_down_id from wo_booking_mst a, wo_booking_dtls b,wo_po_details_master c where a.booking_no=b.booking_no and  b.job_no=c.job_no and a.company_id=$company_id and a.item_category in(4) and a.is_deleted=0 and a.status_active=1 and b.status_active=1 and b.is_deleted=0  and a.ready_to_approved=1 and a.is_approved  in(0,3) $booking_id_cond $buyer_id_cond2 $buyer_id_cond $date_cond $booking_cond group by a.id,a.booking_no_prefix_num,a.booking_no,a.entry_form,a.item_category, a.fabric_source, a.company_id, a.booking_type, a.is_short, a.buyer_id, a.pay_mode, a.supplier_id, a.delivery_date, a.BOOKING_DATE,a.job_no, a.is_approved, a.po_break_down_id  order by prefix_num";
									}
									else 
									{
										$sql="SELECT a.id,a.entry_form,a.booking_no_prefix_num as prefix_num,a.booking_no, a.item_category, a.fabric_source, a.company_id, a.booking_type, a.is_short, a.pay_mode, a.supplier_id, a.delivery_date, a.BOOKING_DATE, '0' as approval_id, a.job_no, a.is_approved, a.po_break_down_id from wo_booking_mst a, wo_booking_dtls b,wo_po_details_master c where a.booking_no=b.booking_no and  b.job_no=c.job_no and a.company_id=$company_id and a.item_category in(4) and a.is_deleted=0 and a.status_active=1 and b.status_active=1 and b.is_deleted=0 and a.is_approved in(3) and a.ready_to_approved=1 $buyer_id_cond2 $buyer_id_cond  $buyerIds_cond $date_cond $booking_cond group by a.id,a.booking_no_prefix_num,a.booking_no,a.entry_form,a.item_category, a.fabric_source, a.company_id, a.booking_type, a.is_short, a.buyer_id, a.pay_mode, a.supplier_id, a.delivery_date, a.BOOKING_DATE,a.job_no, a.is_approved, a.po_break_down_id  order by a.booking_no_prefix_num";
									}
									
									//echo $sql;	
										
									
								}
								else //Without Order
								{
									
									
									$booking_id='';
									$booking_id_sql="select distinct (mst_id) as booking_id from wo_non_ord_samp_booking_mst a, approval_history b where a.id=b.mst_id and a.company_id=$company_id  and a.item_category in(4) and b.sequence_no in ($sequence_no_by_no) and b.entry_form=8 and b.current_approval_status=1 $buyer_id_condnon2  $seqCond
									union
									select distinct (mst_id) as booking_id from wo_non_ord_samp_booking_mst a, approval_history b where a.id=b.mst_id and a.company_id=$company_id and  a.item_category in(4) and b.sequence_no in ($sequence_no_by_yes) and b.entry_form=8 and b.current_approval_status=1 $buyer_id_condnon2 ";
									//echo $booking_id_sql;die;
									$bResult=sql_select($booking_id_sql);
									foreach($bResult as $bRow)
									{
										$booking_id.=$bRow[csf('booking_id')].",";
									}
									
									$booking_id=chop($booking_id,',');
									
									$booking_id_app_byuser=return_field_value("LISTAGG(mst_id, ',') WITHIN GROUP (ORDER BY mst_id) as booking_id","wo_non_ord_samp_booking_mst a, approval_history b","a.id=b.mst_id and a.company_id=$company_id and a.booking_type=5 and a.item_category in(4) and b.sequence_no=$user_sequence_no and b.entry_form=8 and b.current_approval_status=1","booking_id");
									$booking_id_app_byuser=implode(",",array_unique(explode(",",$booking_id_app_byuser)));
									
									$result=array_diff(explode(',',$booking_id),explode(',',$booking_id_app_byuser));
									$booking_id=implode(",",$result);
									
									$booking_id_cond="";
									if($booking_id!="")
									{
										if($this->db->dbdriver != 'mysqli' && count($result)>999)
										{
											$booking_id_chunk_arr=array_chunk($result,999) ;
											foreach($booking_id_chunk_arr as $chunk_arr)
											{
												$chunk_arr_value=implode(",",$chunk_arr);	
												$bokIds_cond.=" a.id in($chunk_arr_value) or ";	
											}
											
											$booking_id_cond.=" and (".chop($bokIds_cond,'or ').")";			
											//echo $booking_id_cond;die;
										}
										else
										{
											$booking_id_cond=" and a.id in($booking_id)";	 
										}
									}
									else $booking_id_cond="";
									
									
									
									
									if($booking_id!=="")
									{
										
										$sql="select a.id, a.booking_no_prefix_num as prefix_num, a.booking_no, a.item_category, a.fabric_source, a.company_id, a.booking_type, a.is_short, a.BUYER_ID, a.pay_mode, a.supplier_id, a.delivery_date, a.BOOKING_DATE, '0' as approval_id, a.is_approved, a.po_break_down_id from wo_non_ord_samp_booking_mst a,wo_non_ord_samp_booking_dtls b where a.booking_no=b.booking_no and a.company_id=$company_id and a.item_category in(4) and a.booking_type=5 and a.is_deleted=0 and a.status_active=1 and b.status_active=1 and b.is_deleted=0 and a.is_approved=$approval_type and a.ready_to_approved=1 $buyer_id_condnon2 $buyer_id_condnon $date_cond $booking_cond group by a.id, a.booking_no_prefix_num, a.booking_no, a.item_category, a.fabric_source, a.company_id, a.booking_type, a.is_short, a.buyer_id, a.pay_mode, a.supplier_id, a.delivery_date, a.BOOKING_DATE,a.is_approved, a.po_break_down_id
										union all 
										select a.id, a.booking_no_prefix_num as prefix_num, a.booking_no, a.item_category, a.fabric_source, a.company_id, a.booking_type, a.is_short, a.BUYER_ID, a.pay_mode, a.supplier_id, a.delivery_date, a.BOOKING_DATE, '0' as approval_id, a.is_approved, a.po_break_down_id from wo_non_ord_samp_booking_mst a,wo_non_ord_samp_booking_dtls b where a.booking_no=b.booking_no and a.company_id=$company_id and a.item_category in(4) and a.booking_type=5 and a.is_deleted=0 and a.status_active=1 and b.status_active=1 and b.is_deleted=0  and a.ready_to_approved=1 and a.is_approved in(1,0) $booking_id_cond $buyer_id_condnon2 $buyer_id_condnon $date_cond $booking_cond group by a.id, a.booking_no_prefix_num,a.booking_no, a.item_category, a.fabric_source, a.company_id, a.booking_type, a.is_short, a.buyer_id, a.pay_mode, a.supplier_id, a.delivery_date, a.BOOKING_DATE,a.is_approved, a.po_break_down_id  order by prefix_num";
										
									}
									else 
									{
										  $sql="select a.id, a.booking_no_prefix_num as prefix_num, a.booking_no, a.item_category, a.fabric_source, a.company_id, a.booking_type, a.is_short, a.BUYER_ID, a.pay_mode, a.supplier_id, a.delivery_date, a.BOOKING_DATE, '0' as approval_id, a.is_approved, a.po_break_down_id from wo_non_ord_samp_booking_mst a,wo_non_ord_samp_booking_dtls b where a.booking_no=b.booking_no and a.company_id=$company_id and a.item_category in(4) and a.booking_type=5  and a.is_deleted=0 and a.status_active=1 and b.status_active=1 and b.is_deleted=0 and a.is_approved=$approval_type and a.ready_to_approved=1 $buyer_id_condnon2 $buyer_id_condnon $date_cond $booking_cond  group by a.id,a.booking_no_prefix_num,a.booking_no, a.item_category, a.fabric_source, a.company_id, a.booking_type, a.is_short, a.buyer_id, a.pay_mode, a.supplier_id, a.delivery_date, a.BOOKING_DATE,a.is_approved, a.po_break_down_id  order by a.booking_no_prefix_num";
									}	
								}
								// echo $sql;	
							}
						
						}
						else 
						{
							
							
							$buyer_ids=$buyer_ids_array[$user_id]['u'];
							if($buyer_ids=="") $buyer_id_cond2=""; else $buyer_id_cond2=" and c.buyer_name in($buyer_ids)";
							if($buyer_ids=="") $buyer_id_condnon2=""; else  $buyer_id_condnon2=" and a.buyer_id in($buyer_ids)";
							$user_sequence_no=$user_sequence_no-1;
							//echo $approval_type.'='.$sequence_no.'='.$user_sequence_no;die;
							if($sequence_no==$user_sequence_no) $sequence_no_by_pass='';
							else
							{
								if($this->db->dbdriver == 'mysqli')
								{
									$sequence_no_by_pass=return_field_value("group_concat(sequence_no) as sequence_id","electronic_approval_setup","company_id=$company_id and page_id=$page_id and sequence_no between $sequence_no and $user_sequence_no and bypass=1 and  is_deleted=0","sequence_id");
								}
								else
								{
									$sequence_no_by_pass=return_field_value("listagg(sequence_no,',') within group (order by sequence_no) as sequence_id","electronic_approval_setup","company_id=$company_id and page_id=$page_id and sequence_no between $sequence_no and $user_sequence_no and bypass=1 and  is_deleted=0","sequence_id");
							   }
							}
							
							if($sequence_no_by_pass=="") $sequence_no_cond=" and d.sequence_no in($sequence_no)";
							//if($sequence_no_by_pass=="") $sequence_no_cond=" and d.sequence_no in ($min_sequence_no)";
							else $sequence_no_cond=" and (d.sequence_no='$sequence_no' or d.sequence_no in ($sequence_no_by_pass))";
							if($cbo_booking_type==1) //With Order
							{
								
								
								$sql="SELECT a.id, a.entry_form, a.booking_no_prefix_num as prefix_num, a.booking_no, a.item_category, a.fabric_source, a.company_id, a.booking_type, a.is_short, a.pay_mode, a.supplier_id, a.delivery_date, a.BOOKING_DATE, a.job_no, a.po_break_down_id, d.id as approval_id,a.is_approved, c.BUYER_NAME as BUYER_ID
								from wo_booking_mst a,wo_booking_dtls b,wo_po_details_master c,approval_history d 
								where a.id=d.mst_id and a.booking_no=b.booking_no and b.job_no=c.job_no and d.entry_form=8 and a.company_id=$company_id and a.item_category in(4) and a.status_active=1 and a.is_deleted=0 and current_approval_status=1 and a.ready_to_approved=1 and a.is_approved in(3) $buyer_id_cond $buyer_id_cond2 $sequence_no_cond $date_cond $booking_cond group by a.id,a.booking_no_prefix_num,a.entry_form,a.booking_no,a.item_category, a.fabric_source, a.company_id, a.booking_type, a.is_short, a.pay_mode, a.supplier_id, a.delivery_date, a.BOOKING_DATE, a.job_no, a.po_break_down_id, d.id,a.is_approved, c.BUYER_NAME  order by a.booking_no_prefix_num";
							}
							else
							{
								$sql="SELECT a.id, a.booking_no_prefix_num as prefix_num, a.booking_no,a.item_category, a.fabric_source, a.company_id, a.booking_type, a.is_short, a.BUYER_ID, a.pay_mode, a.supplier_id, a.delivery_date, a.BOOKING_DATE, a.po_break_down_id, d.id as approval_id,a.is_approved 
								from wo_non_ord_samp_booking_mst  a, approval_history d 
								where a.id=d.mst_id and d.entry_form=8 and a.company_id=$company_id and a.item_category in(4) and a.booking_type=5 and a.status_active=1 and a.is_deleted=0 and current_approval_status=1 and a.is_approved=1 and a.ready_to_approved=1 $buyer_id_condnon $buyer_id_condnon2 $sequence_no_cond $date_cond $booking_cond";
								
							}
						}
					
						
						
						$nameArray=sql_select( $sql ); 
						foreach ($nameArray as $row)
						{
							
							if($select_no){	
								$dataArr[PAGE_ID]=336000000;
								$dataArr[APP_TITLE]='Trims Booking Approval';
								$dataArr[COMPANY_ID]=$company_id;
								$dataArr[APP_ID]=$row->ID;
								$dataArr[MESSAGE1]=$row->BOOKING_NO;
								$dataArr[MESSAGE2]=($row->BOOKING_DATE)?$row->BOOKING_DATE:'00-00-0000';
								$dataArr[MESSAGE3]=($buyer_arr[$row->BUYER_ID])?$buyer_arr[$row->BUYER_ID]:'';
								$dataArr[MESSAGE4]='';
							}
							else
							{
								$dataArr[$i][PAGE_ID]=336000000;
								$dataArr[$i][APP_TITLE]='Trims Booking Approval';
								$dataArr[$i][COMPANY_ID]=$company_id;
								$dataArr[$i][APP_ID]=$row->ID;
								
								//$dataArr[DTLS][$i][2][CAPTION]="STYLE REF NO";
								$dataArr[$i][MESSAGE1]=$row->BOOKING_NO;
					
								//$dataArr[DTLS][$i][3][CAPTION]="QUOTATION DATE";
								$dataArr[$i][MESSAGE2]=($row->BOOKING_DATE)?$row->BOOKING_DATE:'00-00-0000';
								
								//$dataArr[DTLS][$i][4][CAPTION]="BUYER NAME";
								$dataArr[$i][MESSAGE3]=($buyer_arr[$row->BUYER_ID])?$buyer_arr[$row->BUYER_ID]:'';
								
								//$dataArr[DTLS][$i][5][CAPTION]="";
								$dataArr[$i][MESSAGE4]='';
							}
							$i++; 
							
						}
					
					
				}
				
			}//end company foreach;
		
		}//Trims Booking Approval if con;
		
		//Trims Booking Approval [With out Order]
		if($tb_without_order_page_id==336000001){
			$page_id=336;
			$approval_type=0;
			$cbo_booking_type=2;
			
			
			foreach($company_arr as $company_id=>$company_name){
				$user_sequence_no=$user_sequence_no_array[$page_id][$company_id];
				$min_sequence_no=$min_sequence_no_array[$page_id][$company_id];
				//$maxUserNo=$max_sequence_no_array[$page_id][$company_id];
				$buyer_ids_array=$user_buyer_ids_array[$page_id][$company_id];
			
				
				if($approval_type==0 && $user_sequence_no !=""){
					if($select_no){$booking_cond=" and a.id=$select_no";}
					
					
						if($this->db->dbdriver == 'mysqli')
						{
							$sequence_no=return_field_value("max(sequence_no) as seq","electronic_approval_setup","company_id=$company_id and page_id=$page_id and sequence_no<$user_sequence_no and bypass=2 and buyer_id='' and is_deleted=0","seq");
						}
						else
						{
							$sequence_no=return_field_value("max(sequence_no) as seq","electronic_approval_setup","company_id=$company_id and page_id=$page_id and sequence_no<$user_sequence_no and buyer_id is null and bypass=2 and is_deleted=0","seq");
						}
					  
						if($user_sequence_no==$min_sequence_no) // first user
						{
							$buyer_ids=$buyer_ids_array[$user_id]['u'];
							if($buyer_ids=="") { $buyer_id_cond2=""; $buyer_id_condnon2="";}
							else {$buyer_id_cond2=" and c.buyer_name in($buyer_ids)"; $buyer_id_condnon2=" and a.buyer_id in($buyer_ids)";}
							if($cbo_booking_type==1) //With Order
							{
								if($this->db->dbdriver == 'mysqli')
								{
									$sql="select a.id, a.entry_form, a.booking_no_prefix_num as prefix_num, a.booking_no, a.item_category, a.fabric_source, a.entry_form, a.company_id, a.booking_type, a.is_short, a.pay_mode, a.supplier_id, a.delivery_date, a.BOOKING_DATE, '0' as approval_id, a.job_no, a.is_approved, a.po_break_down_id from wo_booking_mst a, wo_booking_dtls b,wo_po_details_master c where  a.booking_no=b.booking_no and  b.job_no=c.job_no and a.company_id=$company_id and a.item_category in(4) and a.is_deleted=0 and a.status_active=1 and b.status_active=1 and b.is_deleted=0 and a.is_approved=$approval_type and a.ready_to_approved=1 $buyer_id_cond2 $buyer_id_cond $buyer_id_cond3 $date_cond $booking_cond $booking_year_cond group by a.id";
								}
								else
								{
								  $sql="select a.id, a.entry_form, a.booking_no_prefix_num as prefix_num, a.booking_no, a.item_category, a.fabric_source, a.company_id, a.entry_form, a.booking_type, a.is_short, a.pay_mode, a.supplier_id, a.delivery_date, a.BOOKING_DATE, '0' as approval_id, a.job_no, a.is_approved, a.po_break_down_id from wo_booking_mst a, wo_booking_dtls b,wo_po_details_master c where a.booking_no=b.booking_no and b.job_no=c.job_no and a.company_id=$company_id and a.item_category in(4) and a.is_deleted=0 and a.status_active=1 and b.status_active=1 and b.is_deleted=0 and a.is_approved=$approval_type and a.ready_to_approved=1 $buyer_id_cond2 $buyer_id_cond $buyer_id_cond3 $date_cond $booking_cond $booking_year_cond group by a.id, a.booking_no_prefix_num, a.booking_no, a.entry_form, a.item_category, a.fabric_source, a.company_id, a.booking_type, a.entry_form, a.is_short, a.buyer_id, a.pay_mode, a.supplier_id, a.delivery_date, a.BOOKING_DATE,a.job_no, a.is_approved, a.po_break_down_id order by a.booking_no_prefix_num";
								}
								//echo $sql;//die("with hot pic");
							}
							else  //WithOut Order
							{
								if($this->db->dbdriver == 'mysqli')
								{
									$sql="select a.id, a.booking_no_prefix_num as prefix_num,a.booking_no, a.item_category, a.fabric_source, a.company_id, a.booking_type, a.is_short, a.BUYER_ID, a.pay_mode, a.supplier_id, a.delivery_date, a.BOOKING_DATE, '0' as approval_id, a.is_approved, a.po_break_down_id from wo_non_ord_samp_booking_mst a,wo_non_ord_samp_booking_dtls b where a.booking_no=b.booking_no and a.company_id=$company_id and a.item_category in(4) and a.booking_type=5 and a.is_deleted=0 and a.status_active=1 and b.status_active=1 and b.is_deleted=0 and a.is_approved=$approval_type and a.ready_to_approved=1 $buyer_id_condnon2 $buyer_id_condnon $date_cond $booking_cond $booking_year_cond group by a.id ";
								}
								else
								{
									$sql="select a.id, a.booking_no_prefix_num as prefix_num, a.booking_no, a.item_category, a.fabric_source, a.company_id, a.booking_type, a.is_short, a.BUYER_ID, a.pay_mode, a.supplier_id, a.delivery_date, a.BOOKING_DATE, '0' as approval_id, a.is_approved, a.po_break_down_id from wo_non_ord_samp_booking_mst a,wo_non_ord_samp_booking_dtls b where a.booking_no=b.booking_no and a.company_id=$company_id and a.item_category in(4) and a.booking_type=5  and a.is_deleted=0 and a.status_active=1 and b.status_active=1 and b.is_deleted=0 and a.is_approved=$approval_type and a.ready_to_approved=1 $buyer_id_condnon2 $buyer_id_condnon $date_cond $booking_cond  $booking_year_cond group by a.id,a.booking_no_prefix_num, a.booking_no, a.item_category, a.fabric_source, a.company_id, a.booking_type, a.is_short, a.buyer_id, a.pay_mode, a.supplier_id, a.delivery_date, a.BOOKING_DATE,a.is_approved, a.po_break_down_id order by a.booking_no_prefix_num";
								}
								//echo $sql;die(" with cool ppic");
							}
						}
						else if($sequence_no=="") // Next user // bypass if previous user Yes 
						{
							// echo "bypass yes";die;
							$buyer_ids=$buyer_ids_array[$user_id]['u'];
							if($buyer_ids=="") {$buyer_id_cond2=""; $buyer_id_condnon2="";} 
							else {$buyer_id_cond2=" and c.buyer_name in($buyer_ids)"; $buyer_id_condnon2=" and a.buyer_id in($buyer_ids)";}
							
						
							if($this->db->dbdriver == 'mysqli')
							{
								$seqSql="select SEQUENCE_NO, BYPASS, BUYER_ID from electronic_approval_setup where company_id=$company_id and page_id=$page_id and sequence_no<$user_sequence_no and is_deleted=0 order by sequence_no desc";
								$seqData=sql_select($seqSql);
								
								
								$buyerIds=''; $sequence_no_by_yes=''; $sequence_no_by_no=''; $approved_string=''; $check_buyerIds_arr=array();
								foreach($seqData as $sRow)
								{
									if($sRow->BYPASS==2)
									{
										$sequence_no_by_no.=$sRow->SEQUENCE_NO.",";
										if($sRow->BUYER_ID!="") 
										{
											$buyerIds.=$sRow->BUYER_ID.",";
											
											$buyer_id_arr=explode(",",$sRow->BUYER_ID);
											$result=array_diff($buyer_id_arr,$check_buyerIds_arr);
											if(count($result)>0)
											{
												$query_string.=" (b.sequence_no=".$sRow->SEQUENCE_NO." and a.buyer_id in(".implode(",",$result).")) or ";
											}
											$check_buyerIds_arr=array_unique(array_merge($check_buyerIds_arr,$buyer_id_arr));
										}
									}
									else
									{
										$sequence_no_by_yes.=$sRow->SEQUENCE_NO.",";
									}
								}
								//var_dump($check_buyerIds_arr);die;
								$buyerIds=chop($buyerIds,',');
								if($buyerIds=="") 
								{
									$buyerIds_cond=""; 
									$seqCond="";
								}
								else 
								{
									$buyerIds_cond=" and a.buyer_id not in($buyerIds)";
									$seqCond=" and (".chop($query_string,'or ').")";
								}
								//echo $seqCond;die;
								$sequence_no_by_no=chop($sequence_no_by_no,',');
								$sequence_no_by_yes=chop($sequence_no_by_yes,',');
								
								if($sequence_no_by_yes=="") $sequence_no_by_yes=0;
								if($sequence_no_by_no=="") $sequence_no_by_no=0;
								
								
								if($cbo_booking_type==1) //With Order
								{
									$booking_id='';
									$booking_id_sql="select distinct (mst_id) as booking_id from wo_booking_mst a, approval_history b where a.id=b.mst_id and a.company_id=$company_id  and a.item_category in(4) and b.sequence_no in ($sequence_no_by_no) and b.entry_form=8 and b.current_approval_status=1 $buyer_id_condnon2  $seqCond
									union
									select distinct (mst_id) as booking_id from wo_booking_mst a, approval_history b where a.id=b.mst_id and a.company_id=$company_id and  a.item_category in(4) and b.sequence_no in ($sequence_no_by_yes) and b.entry_form=8 and b.current_approval_status=1 $buyer_id_condnon2 ";
									//echo $booking_id_sql;die;
									$bResult=sql_select($booking_id_sql);
									foreach($bResult as $bRow)
									{
										$booking_id.=$bRow[csf('booking_id')].",";
									}
									
									$booking_id=chop($booking_id,',');
									
									$booking_id_app_byuser=return_field_value("GROUP_CONCAT(mst_id, ',') as booking_id","wo_booking_mst a, approval_history b","a.id=b.mst_id and a.company_id=$company_id and a.booking_type=2 and a.item_category in(4) and b.sequence_no=$user_sequence_no and b.entry_form=8 and b.current_approval_status=1","booking_id");
									$booking_id_app_byuser=implode(",",array_unique(explode(",",$booking_id_app_byuser)));
									
									$result=array_diff(explode(',',$booking_id),explode(',',$booking_id_app_byuser));
									$booking_id=implode(",",$result);
									
									$booking_id_cond="";
									if($booking_id!="")
									{
										if($this->db->dbdriver != 'mysqli' && count($result)>999)
										{
											$booking_id_chunk_arr=array_chunk($result,999) ;
											foreach($booking_id_chunk_arr as $chunk_arr)
											{
												$chunk_arr_value=implode(",",$chunk_arr);	
												$bokIds_cond.=" a.id in($chunk_arr_value) or ";	
											}
											
											$booking_id_cond.=" and (".chop($bokIds_cond,'or ').")";			
											//echo $booking_id_cond;die;
										}
										else
										{
											$booking_id_cond=" and a.id in($booking_id)";	 
										}
									}
									else $booking_id_cond="";
									if($booking_id!=="")
									{
									$sql="SELECT a.id, a.entry_form,a.booking_no_prefix_num as prefix_num,a.booking_no, a.item_category, a.fabric_source, a.company_id, a.booking_type, a.is_short, a.supplier_id, a.delivery_date, a.BOOKING_DATE, '0' as approval_id, a.job_no, a.is_approved, a.po_break_down_id from wo_booking_mst a, wo_booking_dtls b,wo_po_details_master c where a.booking_no=b.booking_no and  b.job_no=c.job_no and a.company_id=$company_id and a.item_category in(4) and a.is_deleted=0 and a.status_active=1 and b.status_active=1 and b.is_deleted=0 and a.is_approved in(0) and a.ready_to_approved=1 $buyer_id_cond2 $buyer_id_cond $buyerIds_cond $date_cond $booking_cond group by a.id,a.booking_no_prefix_num,a.booking_no,a.entry_form,a.item_category, a.fabric_source, a.company_id, a.booking_type, a.is_short, a.buyer_id, a.supplier_id, a.delivery_date, a.BOOKING_DATE,a.job_no, a.is_approved, a.po_break_down_id 
										union all 
										SELECT a.id, a.entry_form,a.booking_no_prefix_num as prefix_num, a.booking_no, a.item_category, a.fabric_source, a.company_id, a.booking_type, a.is_short, a.supplier_id, a.delivery_date, a.BOOKING_DATE, '0' as approval_id, a.job_no, a.is_approved, a.po_break_down_id from wo_booking_mst a, wo_booking_dtls b,wo_po_details_master c where a.booking_no=b.booking_no and  b.job_no=c.job_no and a.company_id=$company_id and a.item_category in(4) and a.is_deleted=0 and a.status_active=1 and b.status_active=1 and b.is_deleted=0  and a.ready_to_approved=1 and a.is_approved in(0,3) and a.id in($booking_id) $buyer_id_cond2 $buyer_id_cond $date_cond $booking_cond group by a.id,a.booking_no_prefix_num,a.booking_no,a.entry_form,a.item_category, a.fabric_source, a.company_id, a.booking_type, a.is_short, a.buyer_id, a.supplier_id, a.delivery_date, a.BOOKING_DATE,a.job_no, a.is_approved, a.po_break_down_id order by prefix_num";
									}
									else 
									{
										$sql="SELECT a.id,a.entry_form,a.booking_no_prefix_num as prefix_num,a.booking_no, a.item_category, a.fabric_source, a.company_id, a.booking_type, a.is_short, a.supplier_id, a.delivery_date, a.BOOKING_DATE, '0' as approval_id, a.job_no, a.is_approved, a.po_break_down_id from wo_booking_mst a, wo_booking_dtls b,wo_po_details_master c where a.booking_no=b.booking_no and  b.job_no=c.job_no and a.company_id=$company_id and a.item_category in(4) and a.is_deleted=0 and a.status_active=1 and b.status_active=1 and b.is_deleted=0 and a.is_approved in(0,3) and a.ready_to_approved=1 $buyer_id_cond2 $buyer_id_cond  $buyerIds_cond $date_cond $booking_cond group by a.id,a.booking_no_prefix_num,a.booking_no,a.entry_form,a.item_category, a.fabric_source, a.company_id, a.booking_type, a.is_short, a.buyer_id, a.supplier_id, a.delivery_date, a.BOOKING_DATE,a.job_no, a.is_approved, a.po_break_down_id order by a.booking_no_prefix_num";
									}
									//echo $sql;	
								}
								else //Without Order
								{				
									$booking_id='';
									$booking_id_sql="select distinct (mst_id) as booking_id from wo_non_ord_samp_booking_mst a, approval_history b where a.id=b.mst_id and a.company_id=$company_id  and a.item_category in(4) and b.sequence_no in ($sequence_no_by_no) and b.entry_form=8 and b.current_approval_status=1 $buyer_id_condnon2  $seqCond
									union
									select distinct (mst_id) as booking_id from wo_non_ord_samp_booking_mst a, approval_history b where a.id=b.mst_id and a.company_id=$company_id and  a.item_category in(4) and b.sequence_no in ($sequence_no_by_yes) and b.entry_form=8 and b.current_approval_status=1 $buyer_id_condnon2 ";
									//echo $booking_id_sql;die;
									$bResult=sql_select($booking_id_sql);
									foreach($bResult as $bRow)
									{
										$booking_id.=$bRow[csf('booking_id')].",";
									}
									
									$booking_id=chop($booking_id,',');
									
									$booking_id_app_byuser=return_field_value("GROUP_CONCAT(mst_id, ',') as booking_id","wo_non_ord_samp_booking_mst a, approval_history b","a.id=b.mst_id and a.company_id=$company_id and a.booking_type=5 and a.item_category in(4) and b.sequence_no=$user_sequence_no and b.entry_form=8 and b.current_approval_status=1","booking_id");
									$booking_id_app_byuser=implode(",",array_unique(explode(",",$booking_id_app_byuser)));
									
									$result=array_diff(explode(',',$booking_id),explode(',',$booking_id_app_byuser));
									$booking_id=implode(",",$result);
									
									$booking_id_cond="";
									if($booking_id!="")
									{
										if($this->db->dbdriver != 'mysqli' && count($result)>999)
										{
											$booking_id_chunk_arr=array_chunk($result,999) ;
											foreach($booking_id_chunk_arr as $chunk_arr)
											{
												$chunk_arr_value=implode(",",$chunk_arr);	
												$bokIds_cond.=" a.id in($chunk_arr_value) or ";	
											}
											
											$booking_id_cond.=" and (".chop($bokIds_cond,'or ').")";			
											//echo $booking_id_cond;die;
										}
										else
										{
											$booking_id_cond=" and a.id in($booking_id)";	 
										}
									}
									else $booking_id_cond="";
									
									
									
									
									if($booking_id!=="")
									{
										
										$sql="select a.id, a.booking_no_prefix_num as prefix_num,a.booking_no, a.item_category, a.fabric_source, a.company_id, a.booking_type, a.is_short, a.BUYER_ID, a.supplier_id, a.delivery_date, a.BOOKING_DATE, '0' as approval_id, a.is_approved, a.po_break_down_id from wo_non_ord_samp_booking_mst a,wo_non_ord_samp_booking_dtls b where a.booking_no=b.booking_no and a.company_id=$company_id and a.item_category in(4) and a.booking_type=5 and a.is_deleted=0 and a.status_active=1 and b.status_active=1 and b.is_deleted=0 and a.is_approved=$approval_type and a.ready_to_approved=1 $buyer_id_condnon2 $buyer_id_condnon $date_cond $booking_cond group by a.id, a.booking_no_prefix_num,a.booking_no, a.item_category, a.fabric_source, a.company_id, a.booking_type, a.is_short, a.buyer_id, a.supplier_id, a.delivery_date, a.BOOKING_DATE,a.is_approved, a.po_break_down_id 
										union all 
										select a.id, a.booking_no_prefix_num as prefix_num,a.booking_no, a.item_category, a.fabric_source, a.company_id, a.booking_type, a.is_short, a.BUYER_ID, a.supplier_id, a.delivery_date, a.BOOKING_DATE, '0' as approval_id, a.is_approved, a.po_break_down_id from wo_non_ord_samp_booking_mst a,wo_non_ord_samp_booking_dtls b where a.booking_no=b.booking_no and a.company_id=$company_id and a.item_category in(4) and a.booking_type=5 and a.is_deleted=0 and a.status_active=1 and b.status_active=1 and b.is_deleted=0  and a.ready_to_approved=1 and a.is_approved in(1,0) $booking_id_cond $buyer_id_condnon2 $buyer_id_condnon $date_cond $booking_cond group by a.id, a.booking_no_prefix_num,a.booking_no, a.item_category, a.fabric_source, a.company_id, a.booking_type, a.is_short, a.buyer_id, a.supplier_id, a.delivery_date, a.BOOKING_DATE,a.is_approved, a.po_break_down_id order by prefix_num";
										
									}
									else 
									{
										
										  $sql="select a.id, a.booking_no_prefix_num as prefix_num,a.booking_no,a.item_category, a.fabric_source, a.company_id, a.booking_type, a.is_short, a.BUYER_ID, a.supplier_id, a.delivery_date, a.BOOKING_DATE, '0' as approval_id, a.is_approved, a.po_break_down_id from wo_non_ord_samp_booking_mst a,wo_non_ord_samp_booking_dtls b where a.booking_no=b.booking_no and a.company_id=$company_id and a.item_category in(4) and a.booking_type=5  and a.is_deleted=0 and a.status_active=1 and b.status_active=1 and b.is_deleted=0 and a.is_approved=$approval_type and a.ready_to_approved=1 $buyer_id_condnon2 $buyer_id_condnon $date_cond $booking_cond  group by a.id,a.booking_no_prefix_num,a.booking_no, a.item_category, a.fabric_source, a.company_id, a.booking_type, a.is_short, a.buyer_id, a.supplier_id, a.delivery_date, a.BOOKING_DATE,a.is_approved, a.po_break_down_id order by a.booking_no_prefix_num";
									}	
							
								}
								 //echo $sql;	
							}
							else
							{
								$seqSql="select SEQUENCE_NO, BYPASS, BUYER_ID from electronic_approval_setup where company_id=$company_id and page_id=$page_id and sequence_no<$user_sequence_no and is_deleted=0 order by sequence_no desc";
								//echo $seqSql;die;
								$seqData=sql_select($seqSql);
								
								
								$buyerIds=''; $sequence_no_by_yes=''; $sequence_no_by_no=''; $approved_string=''; $check_buyerIds_arr=array();
								foreach($seqData as $sRow)
								{
									if($sRow->BYPASS==2)
									{
										$sequence_no_by_no.=$sRow->SEQUENCE_NO.",";
										if($sRow->BUYER_ID!="") 
										{
											$buyerIds.=$sRow->BUYER_ID.",";
											
											$buyer_id_arr=explode(",",$sRow->BUYER_ID);
											$result=array_diff($buyer_id_arr,$check_buyerIds_arr);
											if(count($result)>0)
											{
												$query_string.=" (b.sequence_no=".$sRow->SEQUENCE_NO." and a.buyer_id in(".implode(",",$result).")) or ";
											}
											$check_buyerIds_arr=array_unique(array_merge($check_buyerIds_arr,$buyer_id_arr));
										}
									}
									else
									{
										$sequence_no_by_yes.=$sRow->SEQUENCE_NO.",";
									}
								}
								//var_dump($check_buyerIds_arr);die;
								$buyerIds=chop($buyerIds,',');
								if($buyerIds=="") 
								{
									$buyerIds_cond=""; 
									$seqCond="";
								}
								else 
								{
									$buyerIds_cond=" and a.buyer_id not in($buyerIds)";
									$seqCond=" and (".chop($query_string,'or ').")";
								}
								//echo $seqCond;die;
								$sequence_no_by_no=chop($sequence_no_by_no,',');
								$sequence_no_by_yes=chop($sequence_no_by_yes,',');
								
								if($sequence_no_by_yes=="") $sequence_no_by_yes=0;
								if($sequence_no_by_no=="") $sequence_no_by_no=0;
								
								
								if($cbo_booking_type==1) //With Order
								{
									$booking_id='';
									$booking_id_sql="select distinct (mst_id) as BOOKING_ID from wo_booking_mst a, approval_history b where a.id=b.mst_id and a.company_id=$company_id  and a.item_category in(4) and b.sequence_no in ($sequence_no_by_no) and b.entry_form=8 and b.current_approval_status=1 $buyer_id_condnon2  $seqCond
									union
									select distinct (mst_id) as booking_id from wo_booking_mst a, approval_history b where a.id=b.mst_id and a.company_id=$company_id and  a.item_category in(4) and b.sequence_no in ($sequence_no_by_yes) and b.entry_form=8 and b.current_approval_status=1 $buyer_id_condnon2 ";
									//echo $booking_id_sql;die;
									$bResult=sql_select($booking_id_sql);
									foreach($bResult as $bRow)
									{
										$booking_id.=$bRow->BOOKING_ID.",";
									}
									
									$booking_id=chop($booking_id,',');
									
									$booking_id_app_byuser=return_field_value("LISTAGG(mst_id, ',') WITHIN GROUP (ORDER BY mst_id) as booking_id","wo_booking_mst a, approval_history b","a.id=b.mst_id and a.company_id=$company_id and a.booking_type=2 and a.item_category in(4) and b.sequence_no=$user_sequence_no and b.entry_form=8 and b.current_approval_status=1","booking_id");
									$booking_id_app_byuser=implode(",",array_unique(explode(",",$booking_id_app_byuser)));
									
									$result=array_diff(explode(',',$booking_id),explode(',',$booking_id_app_byuser));
									$booking_id=implode(",",$result);
									
									$booking_id_cond="";
									if($booking_id!="")
									{
										if($this->db->dbdriver != 'mysqli' && count($result)>999)
										{
											$booking_id_chunk_arr=array_chunk($result,999) ;
											foreach($booking_id_chunk_arr as $chunk_arr)
											{
												$chunk_arr_value=implode(",",$chunk_arr);	
												$bokIds_cond.=" a.id in($chunk_arr_value) or ";	
											}
											
											$booking_id_cond.=" and (".chop($bokIds_cond,'or ').")";			
											//echo $booking_id_cond;die;
										}
										else
										{
											$booking_id_cond=" and a.id in($booking_id)";	 
										}
									}
									else $booking_id_cond="";
				
								
									// echo $booking_cond;
									$sequence_no_cond=" and d.sequence_no in ($user_sequence_no)";
				
									if($booking_id!="")
									{
									$sql="SELECT a.id, a.entry_form, a.booking_no_prefix_num as prefix_num, a.booking_no, a.item_category, a.fabric_source, a.company_id, a.booking_type, a.is_short, a.pay_mode, a.supplier_id, a.delivery_date, a.BOOKING_DATE, '0' as approval_id, a.job_no, a.is_approved, a.po_break_down_id from wo_booking_mst a, wo_booking_dtls b,wo_po_details_master c where a.booking_no=b.booking_no and  b.job_no=c.job_no and a.company_id=$company_id and a.item_category in(4) and a.is_deleted=0 and a.status_active=1 and b.status_active=1 and b.is_deleted=0 and a.is_approved in(0) and a.ready_to_approved=1 $buyer_id_cond2 $buyer_id_cond $buyerIds_cond $date_cond $booking_cond  group by a.id,a.booking_no_prefix_num,a.booking_no,a.entry_form,a.item_category, a.fabric_source, a.company_id, a.booking_type, a.is_short, a.buyer_id, a.pay_mode, a.supplier_id, a.delivery_date, a.BOOKING_DATE,a.job_no, a.is_approved, a.po_break_down_id 
										union all 
										select a.id, a.entry_form, a.booking_no_prefix_num as prefix_num, a.booking_no, a.item_category, a.fabric_source, a.company_id, a.booking_type, a.is_short, a.pay_mode, a.supplier_id, a.delivery_date, a.BOOKING_DATE, '0' as approval_id, a.job_no, a.is_approved, a.po_break_down_id from wo_booking_mst a, wo_booking_dtls b,wo_po_details_master c where a.booking_no=b.booking_no and  b.job_no=c.job_no and a.company_id=$company_id and a.item_category in(4) and a.is_deleted=0 and a.status_active=1 and b.status_active=1 and b.is_deleted=0  and a.ready_to_approved=1 and a.is_approved  in(0,3) $booking_id_cond $buyer_id_cond2 $buyer_id_cond $date_cond $booking_cond group by a.id,a.booking_no_prefix_num,a.booking_no,a.entry_form,a.item_category, a.fabric_source, a.company_id, a.booking_type, a.is_short, a.buyer_id, a.pay_mode, a.supplier_id, a.delivery_date, a.BOOKING_DATE,a.job_no, a.is_approved, a.po_break_down_id  order by prefix_num";
									}
									else 
									{
										$sql="SELECT a.id,a.entry_form,a.booking_no_prefix_num as prefix_num,a.booking_no, a.item_category, a.fabric_source, a.company_id, a.booking_type, a.is_short, a.pay_mode, a.supplier_id, a.delivery_date, a.BOOKING_DATE, '0' as approval_id, a.job_no, a.is_approved, a.po_break_down_id from wo_booking_mst a, wo_booking_dtls b,wo_po_details_master c where a.booking_no=b.booking_no and  b.job_no=c.job_no and a.company_id=$company_id and a.item_category in(4) and a.is_deleted=0 and a.status_active=1 and b.status_active=1 and b.is_deleted=0 and a.is_approved in(3) and a.ready_to_approved=1 $buyer_id_cond2 $buyer_id_cond  $buyerIds_cond $date_cond $booking_cond group by a.id,a.booking_no_prefix_num,a.booking_no,a.entry_form,a.item_category, a.fabric_source, a.company_id, a.booking_type, a.is_short, a.buyer_id, a.pay_mode, a.supplier_id, a.delivery_date, a.BOOKING_DATE,a.job_no, a.is_approved, a.po_break_down_id  order by a.booking_no_prefix_num";
									}
									
									//echo $sql;	
										
									
								}
								else //Without Order
								{
									
									
									$booking_id='';
									$booking_id_sql="select distinct (mst_id) as booking_id from wo_non_ord_samp_booking_mst a, approval_history b where a.id=b.mst_id and a.company_id=$company_id  and a.item_category in(4) and b.sequence_no in ($sequence_no_by_no) and b.entry_form=8 and b.current_approval_status=1 $buyer_id_condnon2  $seqCond
									union
									select distinct (mst_id) as booking_id from wo_non_ord_samp_booking_mst a, approval_history b where a.id=b.mst_id and a.company_id=$company_id and  a.item_category in(4) and b.sequence_no in ($sequence_no_by_yes) and b.entry_form=8 and b.current_approval_status=1 $buyer_id_condnon2 ";
									//echo $booking_id_sql;die;
									$bResult=sql_select($booking_id_sql);
									foreach($bResult as $bRow)
									{
										$booking_id.=$bRow[csf('booking_id')].",";
									}
									
									$booking_id=chop($booking_id,',');
									
									$booking_id_app_byuser=return_field_value("LISTAGG(mst_id, ',') WITHIN GROUP (ORDER BY mst_id) as booking_id","wo_non_ord_samp_booking_mst a, approval_history b","a.id=b.mst_id and a.company_id=$company_id and a.booking_type=5 and a.item_category in(4) and b.sequence_no=$user_sequence_no and b.entry_form=8 and b.current_approval_status=1","booking_id");
									$booking_id_app_byuser=implode(",",array_unique(explode(",",$booking_id_app_byuser)));
									
									$result=array_diff(explode(',',$booking_id),explode(',',$booking_id_app_byuser));
									$booking_id=implode(",",$result);
									
									$booking_id_cond="";
									if($booking_id!="")
									{
										if($this->db->dbdriver != 'mysqli' && count($result)>999)
										{
											$booking_id_chunk_arr=array_chunk($result,999) ;
											foreach($booking_id_chunk_arr as $chunk_arr)
											{
												$chunk_arr_value=implode(",",$chunk_arr);	
												$bokIds_cond.=" a.id in($chunk_arr_value) or ";	
											}
											
											$booking_id_cond.=" and (".chop($bokIds_cond,'or ').")";			
											//echo $booking_id_cond;die;
										}
										else
										{
											$booking_id_cond=" and a.id in($booking_id)";	 
										}
									}
									else $booking_id_cond="";
									
									
									
									
									if($booking_id!=="")
									{
										
										$sql="select a.id, a.booking_no_prefix_num as prefix_num, a.booking_no, a.item_category, a.fabric_source, a.company_id, a.booking_type, a.is_short, a.BUYER_ID, a.pay_mode, a.supplier_id, a.delivery_date, a.BOOKING_DATE, '0' as approval_id, a.is_approved, a.po_break_down_id from wo_non_ord_samp_booking_mst a,wo_non_ord_samp_booking_dtls b where a.booking_no=b.booking_no and a.company_id=$company_id and a.item_category in(4) and a.booking_type=5 and a.is_deleted=0 and a.status_active=1 and b.status_active=1 and b.is_deleted=0 and a.is_approved=$approval_type and a.ready_to_approved=1 $buyer_id_condnon2 $buyer_id_condnon $date_cond $booking_cond group by a.id, a.booking_no_prefix_num, a.booking_no, a.item_category, a.fabric_source, a.company_id, a.booking_type, a.is_short, a.buyer_id, a.pay_mode, a.supplier_id, a.delivery_date, a.BOOKING_DATE,a.is_approved, a.po_break_down_id
										union all 
										select a.id, a.booking_no_prefix_num as prefix_num, a.booking_no, a.item_category, a.fabric_source, a.company_id, a.booking_type, a.is_short, a.BUYER_ID, a.pay_mode, a.supplier_id, a.delivery_date, a.BOOKING_DATE, '0' as approval_id, a.is_approved, a.po_break_down_id from wo_non_ord_samp_booking_mst a,wo_non_ord_samp_booking_dtls b where a.booking_no=b.booking_no and a.company_id=$company_id and a.item_category in(4) and a.booking_type=5 and a.is_deleted=0 and a.status_active=1 and b.status_active=1 and b.is_deleted=0  and a.ready_to_approved=1 and a.is_approved in(1,0) $booking_id_cond $buyer_id_condnon2 $buyer_id_condnon $date_cond $booking_cond group by a.id, a.booking_no_prefix_num,a.booking_no, a.item_category, a.fabric_source, a.company_id, a.booking_type, a.is_short, a.buyer_id, a.pay_mode, a.supplier_id, a.delivery_date, a.BOOKING_DATE,a.is_approved, a.po_break_down_id  order by prefix_num";
										
									}
									else 
									{
										  $sql="select a.id, a.booking_no_prefix_num as prefix_num, a.booking_no, a.item_category, a.fabric_source, a.company_id, a.booking_type, a.is_short, a.BUYER_ID, a.pay_mode, a.supplier_id, a.delivery_date, a.BOOKING_DATE, '0' as approval_id, a.is_approved, a.po_break_down_id from wo_non_ord_samp_booking_mst a,wo_non_ord_samp_booking_dtls b where a.booking_no=b.booking_no and a.company_id=$company_id and a.item_category in(4) and a.booking_type=5  and a.is_deleted=0 and a.status_active=1 and b.status_active=1 and b.is_deleted=0 and a.is_approved=$approval_type and a.ready_to_approved=1 $buyer_id_condnon2 $buyer_id_condnon $date_cond $booking_cond  group by a.id,a.booking_no_prefix_num,a.booking_no, a.item_category, a.fabric_source, a.company_id, a.booking_type, a.is_short, a.buyer_id, a.pay_mode, a.supplier_id, a.delivery_date, a.BOOKING_DATE,a.is_approved, a.po_break_down_id  order by a.booking_no_prefix_num";
									}	
								}
								// echo $sql;	
							}
						
						}
						else 
						{
							
							
							$buyer_ids=$buyer_ids_array[$user_id]['u'];
							if($buyer_ids=="") $buyer_id_cond2=""; else $buyer_id_cond2=" and c.buyer_name in($buyer_ids)";
							if($buyer_ids=="") $buyer_id_condnon2=""; else  $buyer_id_condnon2=" and a.buyer_id in($buyer_ids)";
							$user_sequence_no=$user_sequence_no-1;
							//echo $approval_type.'='.$sequence_no.'='.$user_sequence_no;die;
							if($sequence_no==$user_sequence_no) $sequence_no_by_pass='';
							else
							{
								if($this->db->dbdriver == 'mysqli')
								{
									$sequence_no_by_pass=return_field_value("group_concat(sequence_no) as sequence_id","electronic_approval_setup","company_id=$company_id and page_id=$page_id and sequence_no between $sequence_no and $user_sequence_no and bypass=1 and  is_deleted=0","sequence_id");
								}
								else
								{
									$sequence_no_by_pass=return_field_value("listagg(sequence_no,',') within group (order by sequence_no) as sequence_id","electronic_approval_setup","company_id=$company_id and page_id=$page_id and sequence_no between $sequence_no and $user_sequence_no and bypass=1 and  is_deleted=0","sequence_id");
							   }
							}
							
							if($sequence_no_by_pass=="") $sequence_no_cond=" and d.sequence_no in($sequence_no)";
							//if($sequence_no_by_pass=="") $sequence_no_cond=" and d.sequence_no in ($min_sequence_no)";
							else $sequence_no_cond=" and (d.sequence_no='$sequence_no' or d.sequence_no in ($sequence_no_by_pass))";
							if($cbo_booking_type==1) //With Order
							{
								
								
								$sql="SELECT a.id, a.entry_form, a.booking_no_prefix_num as prefix_num, a.booking_no, a.item_category, a.fabric_source, a.company_id, a.booking_type, a.is_short, a.pay_mode, a.supplier_id, a.delivery_date, a.BOOKING_DATE, a.job_no, a.po_break_down_id, d.id as approval_id,a.is_approved, c.BUYER_NAME as BUYER_ID
								from wo_booking_mst a,wo_booking_dtls b,wo_po_details_master c,approval_history d 
								where a.id=d.mst_id and a.booking_no=b.booking_no and b.job_no=c.job_no and d.entry_form=8 and a.company_id=$company_id and a.item_category in(4) and a.status_active=1 and a.is_deleted=0 and current_approval_status=1 and a.ready_to_approved=1 and a.is_approved in(3) $buyer_id_cond $buyer_id_cond2 $sequence_no_cond $date_cond $booking_cond group by a.id,a.booking_no_prefix_num,a.entry_form,a.booking_no,a.item_category, a.fabric_source, a.company_id, a.booking_type, a.is_short, a.pay_mode, a.supplier_id, a.delivery_date, a.BOOKING_DATE, a.job_no, a.po_break_down_id, d.id,a.is_approved, c.BUYER_NAME  order by a.booking_no_prefix_num";
							}
							else
							{
								$sql="SELECT a.id, a.booking_no_prefix_num as prefix_num, a.booking_no,a.item_category, a.fabric_source, a.company_id, a.booking_type, a.is_short, a.BUYER_ID, a.pay_mode, a.supplier_id, a.delivery_date, a.BOOKING_DATE, a.po_break_down_id, d.id as approval_id,a.is_approved 
								from wo_non_ord_samp_booking_mst  a, approval_history d 
								where a.id=d.mst_id and d.entry_form=8 and a.company_id=$company_id and a.item_category in(4) and a.booking_type=5 and a.status_active=1 and a.is_deleted=0 and current_approval_status=1 and a.is_approved=1 and a.ready_to_approved=1 $buyer_id_condnon $buyer_id_condnon2 $sequence_no_cond $date_cond $booking_cond";
								
							}
						}
					
						
						
						$nameArray=sql_select( $sql ); 
						foreach ($nameArray as $row)
						{
							
							if($select_no){	
								$dataArr[PAGE_ID]=336000001;
								$dataArr[APP_TITLE]='Trims Booking Without Approval';
								$dataArr[COMPANY_ID]=$company_id;
								$dataArr[APP_ID]=$row->ID;
								$dataArr[MESSAGE1]=$row->BOOKING_NO;
								$dataArr[MESSAGE2]=($row->BOOKING_DATE)?$row->BOOKING_DATE:'00-00-0000';
								$dataArr[MESSAGE3]=($buyer_arr[$row->BUYER_ID])?$buyer_arr[$row->BUYER_ID]:'';
								$dataArr[MESSAGE4]='';
							}
							else
							{
								$dataArr[$i][PAGE_ID]=336000001;
								$dataArr[$i][APP_TITLE]='Trims Booking Without Approval';
								$dataArr[$i][COMPANY_ID]=$company_id;
								$dataArr[$i][APP_ID]=$row->ID;
								
								//$dataArr[DTLS][$i][2][CAPTION]="STYLE REF NO";
								$dataArr[$i][MESSAGE1]=$row->BOOKING_NO;
					
								//$dataArr[DTLS][$i][3][CAPTION]="QUOTATION DATE";
								$dataArr[$i][MESSAGE2]=($row->BOOKING_DATE)?$row->BOOKING_DATE:'00-00-0000';
								
								//$dataArr[DTLS][$i][4][CAPTION]="BUYER NAME";
								$dataArr[$i][MESSAGE3]=($buyer_arr[$row->BUYER_ID])?$buyer_arr[$row->BUYER_ID]:'';
								
								//$dataArr[DTLS][$i][5][CAPTION]="";
								$dataArr[$i][MESSAGE4]='';
							}
							$i++; 
							
						}
					
					
				}
				
			}//end company foreach;
		
		}//Trims Booking Approval if con;
		
		//Yarn Delivery Approval
		if($yd_page_id==479){
			$page_id=479;
			$approval_type=0;
			
			foreach($company_arr as $company_id=>$company_name){
				$user_sequence_no=$user_sequence_no_array[$page_id][$company_id];
				$min_sequence_no=$min_sequence_no_array[$page_id][$company_id];
				//$maxUserNo=$max_sequence_no_array[$page_id][$company_id];
				$buyer_ids_array=$user_buyer_ids_array[$page_id][$company_id];
				
				if($approval_type==0 && $user_sequence_no !=""){

					if($select_no){$issue_cond=" and a.id=$select_no";}
					
					
					
					$sequence_no=return_field_value("max(sequence_no)","electronic_approval_setup","page_id=$page_id and sequence_no<$user_sequence_no and bypass=2 and is_deleted=0 and company_id=$company_id");
					
					if($user_sequence_no==$min_sequence_no)
					{
						if($this->db->dbdriver == 'mysqli')
						{
							$sql="SELECT a.ID,  a.issue_number_prefix_num, a.ISSUE_NUMBER,a.challan_no, a.company_id, $select_year(a.insert_date $year_format) as year,  a.issue_purpose, a.issue_basis,a.knit_dye_source, a.ISSUE_DATE, a.knit_dye_company, '0' as approval_id,  group_concat(distinct(case when a.issue_basis=3 then  b.requisition_no end )) as requisition_no, group_concat(distinct(case when a.issue_basis=1 then  a.booking_no end )) as booking_no, a.is_approved  from inv_issue_master a, inv_transaction b where a.id=b.mst_id  and a.company_id=$company_id  and a.is_deleted=0 and a.status_active=1 and b.status_active=1 and b.is_deleted=0 and a.entry_form=3 and a.is_approved=$approval_type and a.ready_to_approve=1  $txt_req_no $date_cond $issue_cond  group by a.id,  a.issue_number_prefix_num, a.issue_number,a.challan_no, a.company_id,a.insert_date, a.issue_purpose, a.issue_basis,a.knit_dye_source, a.ISSUE_DATE, a.knit_dye_company, a.is_approved";
						}
						else
						{
							$sql="SELECT a.ID,  a.issue_number_prefix_num, a.ISSUE_NUMBER,a.challan_no, a.company_id, $select_year(a.insert_date $year_format) as year,  a.issue_purpose, a.issue_basis,a.knit_dye_source, a.ISSUE_DATE, a.knit_dye_company, '0' as approval_id, LISTAGG(CAST( (case when a.issue_basis=3 then  b.requisition_no end )  AS VARCHAR(4000)), ',') WITHIN GROUP (ORDER BY b.requisition_no) as requisition_no, LISTAGG(CAST( (case when a.issue_basis=1 then  a.booking_no end )  AS VARCHAR(4000)), ',') WITHIN GROUP (ORDER BY a.booking_no) as booking_no, a.is_approved  from  inv_issue_master a, inv_transaction b where a.id=b.mst_id and a.company_id=$company_id  and a.is_deleted=0 and a.status_active=1 and b.status_active=1 and b.is_deleted=0 and a.entry_form=3 and a.is_approved=$approval_type and a.ready_to_approve=1  $txt_req_no $date_cond   $issue_cond
							group by a.id,  a.issue_number_prefix_num, a.issue_number,a.challan_no, a.company_id,a.insert_date, a.issue_purpose, a.issue_basis,a.knit_dye_source, a.ISSUE_DATE, a.knit_dye_company, a.is_approved";
						}
						// echo $sql;
					}
					else if($sequence_no=="") //last approval authority having bypass=no previlages // Next User bypass Yes
					{
						if($quotation_id_app_byuser!="") $quotation_id_cond=" and a.id not in($quotation_id_app_byuser)";
						else if($quotation_id!="") $quotation_id_cond.=" or (a.id in($quotation_id))";
						else $quotation_id_cond="";
						
						if($this->db->dbdriver == 'mysqli')
						{
							$sequence_no_by=return_field_value("group_concat(sequence_no)","electronic_approval_setup","page_id=$page_id and sequence_no<$user_sequence_no and bypass=1 and is_deleted=0");
							$quotation_id=return_field_value("group_concat(distinct(mst_id)) as quotation_id","inv_issue_master a, approval_history b","a.id=b.mst_id and a.company_id=$company_id and a.item_category=1  and b.sequence_no in ($sequence_no_by) and b.entry_form=14 and b.current_approval_status=1","quotation_id");
							
							
							$quotation_id_app_byuser=return_field_value("group_concat(distinct(mst_id)) as quotation_id","inv_issue_master a, approval_history b","a.id=b.mst_id and a.company_id=$company_id and a.item_category=1  and b.sequence_no=$user_sequence_no and b.entry_form=14 and b.current_approval_status=1","quotation_id");
							
							$sql="SELECT a.ID,  a.issue_number_prefix_num, a.ISSUE_NUMBER,a.challan_no, a.company_id, $select_year(a.insert_date $year_format) as year,  a.issue_purpose, a.issue_basis, a.ISSUE_DATE, a.knit_dye_company,a.knit_dye_source, '0' as approval_id,  group_concat(distinct(case when a.issue_basis=3 then  b.requisition_no end )) as requisition_no, group_concat(distinct(case when a.issue_basis=1 then  a.booking_no end )) as booking_no, a.is_approved  from  inv_issue_master a, inv_transaction b where a.id=b.mst_id and a.company_id=$company_id  and a.is_deleted=0 and a.status_active=1 and b.status_active=1 and b.is_deleted=0 and a.entry_form=3 and a.is_approved=$approval_type and a.ready_to_approve=1  $txt_req_no $date_cond $issue_cond $quotation_id_cond  group by a.id,a.issue_number_prefix_num, a.issue_number,a.challan_no, a.company_id,a.insert_date,a.issue_purpose, a.issue_basis, a.ISSUE_DATE, a.knit_dye_company,a.knit_dye_source, a.is_approved";
							// echo $sql;
						}
						else
						{
							$sequence_no_by=return_field_value("LISTAGG(CAST( sequence_no  AS VARCHAR(4000)), ',') WITHIN GROUP (ORDER BY sequence_no) as sequence_no ","electronic_approval_setup","page_id=$page_id and sequence_no<$user_sequence_no and bypass=1 and is_deleted=0","sequence_no");
							
							$quotation_id=return_field_value("LISTAGG(CAST( mst_id  AS VARCHAR(4000)), ',') WITHIN GROUP (ORDER BY mst_id) as quotation_id","inv_issue_master a, approval_history b","a.id=b.mst_id and a.company_id=$company_id and a.item_category=1  and b.sequence_no in ($sequence_no_by) and b.entry_form=14 and b.current_approval_status=1","quotation_id");
							
							$quotation_id_app_byuser=return_field_value("LISTAGG(CAST( mst_id  AS VARCHAR(4000)), ',') WITHIN GROUP (ORDER BY mst_id) as quotation_id","inv_issue_master a, approval_history b","a.id=b.mst_id and a.company_id=$company_id and a.item_category=1  and b.sequence_no=$user_sequence_no and b.entry_form=14 and b.current_approval_status=1","quotation_id");
							$quotation_id_app_byuser=implode(",",array_unique(explode(",",$quotation_id_app_byuser)));
							
							$sql="SELECT a.ID,  a.issue_number_prefix_num, a.ISSUE_NUMBER,a.challan_no, a.company_id, $select_year(a.insert_date $year_format) as year,  a.issue_purpose, a.issue_basis, a.ISSUE_DATE, a.knit_dye_company,a.knit_dye_source, '0' as approval_id, LISTAGG(CAST( (case when a.issue_basis=3 then  b.requisition_no end )  AS VARCHAR(4000)), ',') WITHIN GROUP (ORDER BY b.requisition_no) as requisition_no, LISTAGG(CAST( (case when a.issue_basis=1 then  a.booking_no end )  AS VARCHAR(4000)), ',') WITHIN GROUP (ORDER BY a.booking_no) as booking_no, a.is_approved  from  inv_issue_master a, inv_transaction b where a.id=b.mst_id and a.company_id=$company_id  and a.is_deleted=0 and a.status_active=1 and b.status_active=1 and b.is_deleted=0 and a.entry_form=3 and a.is_approved=$approval_type and a.ready_to_approve=1  $txt_req_no $date_cond $issue_cond $quotation_id_cond  group by a.id,a.issue_number_prefix_num, a.issue_number,a.challan_no, a.company_id,a.insert_date,a.issue_purpose, a.issue_basis, a.ISSUE_DATE, a.knit_dye_company,a.knit_dye_source, a.is_approved";
							
						}
						
					}
					else  // if previous User bypass No
					{
						$user_sequence_no=$user_sequence_no-1;
						
						if($sequence_no==$user_sequence_no) $sequence_no_by_pass='';
						else
						{
							if($this->db->dbdriver == 'mysqli')
							{
								$sequence_no_by_pass=return_field_value("group_concat(sequence_no)","electronic_approval_setup","page_id=$page_id and sequence_no between $sequence_no and $user_sequence_no and bypass=1 and is_deleted=0");
							}
							else
							{
								$sequence_no_by_pass=return_field_value("LISTAGG(CAST( sequence_no  AS VARCHAR(4000)), ',') WITHIN GROUP (ORDER BY sequence_no) as sequence_no","electronic_approval_setup","page_id=$page_id and sequence_no between $sequence_no and $user_sequence_no and bypass=1 and is_deleted=0","sequence_no");	
							}
						}
						
						if($sequence_no_by_pass=="") $sequence_no_cond=" and c.sequence_no='$sequence_no'";
						else $sequence_no_cond=" and (c.sequence_no='$sequence_no' or c.sequence_no in ($sequence_no_by_pass))";
						
						if($this->db->dbdriver == 'mysqli')
						{
							$sql="SELECT a.ID,  a.issue_number_prefix_num, a.ISSUE_NUMBER, a.company_id, $select_year(a.insert_date $year_format) as year,  a.issue_purpose, a.issue_basis, a.ISSUE_DATE,a.challan_no,a.knit_dye_company,a.knit_dye_source, group_concat(distinct c.id) as approval_id,  group_concat(distinct(case when a.issue_basis=3 then  b.requisition_no end ) ) as requisition_no, group_concat(distinct(case when a.issue_basis=1 then  a.booking_no end )) as booking_no, a.is_approved   from  inv_issue_master a,  approval_history c, inv_transaction b where a.id=b.mst_id and a.id=c.mst_id  and a.company_id=$company_id  and a.is_deleted=0 and a.status_active=1 and b.status_active=1 and b.is_deleted=0 and c.entry_form=14 and a.is_approved in(3) and a.ready_to_approve=1 $txt_req_no $date_cond $issue_cond $sequence_no_cond 
							group by a.id, a.issue_number_prefix_num, a.issue_number, a.company_id, a.insert_date, a.issue_purpose, a.issue_basis, a.ISSUE_DATE, a.challan_no,a.knit_dye_company,a.knit_dye_source, a.is_approved order by a.insert_date desc";
						}
						else
						{
							$sql="SELECT a.ID,  a.issue_number_prefix_num, a.ISSUE_NUMBER, a.company_id, $select_year(a.insert_date $year_format) as year,  a.issue_purpose, a.issue_basis, a.ISSUE_DATE,a.challan_no,a.knit_dye_company,a.knit_dye_source,   LISTAGG(CAST( c.id  AS VARCHAR(4000)), ',') WITHIN GROUP (ORDER BY c.id) as approval_id, LISTAGG(CAST( (case when a.issue_basis=3 then  b.requisition_no end )  AS VARCHAR(4000)), ',') WITHIN GROUP (ORDER BY b.requisition_no) as requisition_no, LISTAGG(CAST( (case when a.issue_basis=1 then  a.booking_no end )  AS VARCHAR(4000)), ',') WITHIN GROUP (ORDER BY a.booking_no) as booking_no, a.is_approved   from  inv_issue_master a,  approval_history c, inv_transaction b where a.id=b.mst_id and a.id=c.mst_id  and a.company_id=$company_id  and a.is_deleted=0 and a.status_active=1 and b.status_active=1 and b.is_deleted=0 and c.entry_form=14 and a.is_approved in(3) and a.ready_to_approve=1 $txt_req_no $date_cond $issue_cond $sequence_no_cond 
							group by a.id, a.issue_number_prefix_num, a.issue_number, a.company_id, a.insert_date, a.issue_purpose, a.issue_basis, a.ISSUE_DATE ,a.challan_no,a.knit_dye_company,a.knit_dye_source, a.is_approved
							order by a.insert_date desc";//and b.id=d.trans_id
						}
						
					}
					
					
					 //echo $sql;	die;
					$nameArray=sql_select( $sql ); 
					foreach ($nameArray as $row)
					{
						
						if($select_no){	
							$dataArr[PAGE_ID]=$page_id;
							$dataArr[APP_TITLE]='Yarn Delivery Approval';
							$dataArr[COMPANY_ID]=$company_id;
							$dataArr[APP_ID]=$row->ID;
							$dataArr[MESSAGE1]=$row->ISSUE_NUMBER;
							$dataArr[MESSAGE2]=($row->ISSUE_DATE)?$row->ISSUE_DATE:'00-00-0000';
							$dataArr[MESSAGE3]=($buyer_arr[$row->BUYER_ID])?$buyer_arr[$row->BUYER_ID]:'';
							$dataArr[MESSAGE4]='';
						}
						else
						{
							$dataArr[$i][PAGE_ID]=$page_id;
							$dataArr[$i][APP_TITLE]='Yarn Delivery Approval';
							$dataArr[$i][COMPANY_ID]=$company_id;
							$dataArr[$i][APP_ID]=$row->ID;
							
							//$dataArr[DTLS][$i][2][CAPTION]="STYLE REF NO";
							$dataArr[$i][MESSAGE1]=$row->ISSUE_NUMBER;
				
							//$dataArr[DTLS][$i][3][CAPTION]="QUOTATION DATE";
							$dataArr[$i][MESSAGE2]=($row->ISSUE_DATE)?$row->ISSUE_DATE:'00-00-0000';
							
							//$dataArr[DTLS][$i][4][CAPTION]="BUYER NAME";
							$dataArr[$i][MESSAGE3]=($buyer_arr[$row->BUYER_ID])?$buyer_arr[$row->BUYER_ID]:'';
							
							//$dataArr[DTLS][$i][5][CAPTION]="";
							$dataArr[$i][MESSAGE4]='';
						}
						$i++; 
						
					}
					
				
				}
			}//company con
			
			
		}//Yarn Delivery Approval if con;
		
		//Stationary Work Order Approval
		if($sw_page_id==627){
			$page_id=627;
			$approval_type=0;
			
			foreach($company_arr as $company_id=>$company_name){
				$user_sequence_no=$user_sequence_no_array[$page_id][$company_id];
				$min_sequence_no=$min_sequence_no_array[$page_id][$company_id];
				//$maxUserNo=$max_sequence_no_array[$page_id][$company_id];
				$buyer_ids_array=$user_buyer_ids_array[$page_id][$company_id];
				
				if($approval_type==0 && $user_sequence_no !=""){

					if($select_no){$where_cond=" and a.id=$select_no";}
					
									
					if($user_sequence_no == $min_sequence_no) // First user
					{     
						if($this->db->dbdriver == 'mysqli')
						{
							$select_item_cat = "group_concat(b.item_category_id) as item_category_id ";
						}else{
							$select_item_cat = "listagg(b.item_category_id, ',') within group (order by b.item_category_id) as item_category_id ";
						}
			
						$sql ="SELECT DISTINCT (a.ID), a.company_name, a.wo_number_prefix_num, a.supplier_id, a.wo_date, a.DELIVERY_DATE, a.WO_NUMBER, a.item_category, a.currency_id, a.wo_basis_id, a.pay_mode, a.source, a.attention, a.requisition_no,  a.delivery_place, a.location_id
						FROM wo_non_order_info_mst a, wo_non_order_info_dtls b 
						WHERE a.id = b.mst_id and a.company_name=$company_id and a.entry_form = 146 and a.is_approved=$approval_type and a.wo_number_prefix_num LIKE '%$txt_wo_no%' and a.ready_to_approved =1 and a.status_active=1 and a.is_deleted=0  $where_cond
						order by a.id";
					}
			
					else // Next user
					{
						$sequence_no=return_field_value("max(sequence_no)","electronic_approval_setup","company_id=$company_id and page_id=$page_id and sequence_no<$user_sequence_no and bypass=2 and is_deleted = 0");
						if($sequence_no=="") // bypass if previous user Yes
						{
							$sql="SELECT DISTINCT (a.ID), a.company_name, a.wo_number_prefix_num, a.supplier_id, a.wo_date, a.DELIVERY_DATE, a.WO_NUMBER, a.item_category, a.currency_id, a.wo_basis_id, a.pay_mode, a.source, a.attention, a.requisition_no,  a.delivery_place, a.location_id
							from wo_non_order_info_mst a, wo_non_order_info_dtls b 
							where a.id =b.mst_id and a.company_name=$company_id and a.entry_form = 146 $user_crediatial_item_cat_cond2 and b.item_category_id not in(1,2,3,12,13,14) and a.is_approved in(0,3) and a.ready_to_approved=1 and a.wo_number_prefix_num LIKE '%$txt_wo_no%' and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $where_cond
							order by a.id";
						}
			
						else // bypass No
						{
							$user_sequence_no=$user_sequence_no-1;
							if($sequence_no==$user_sequence_no) 
							{
								$sequence_no_by_pass=$sequence_no;
								$sequence_no_cond=" and b.sequence_no in ($sequence_no_by_pass)";
							}
							else
							{
								if($this->db->dbdriver == 'mysqli') 
								{
									$sequence_no_by_pass=return_field_value("group_concat(sequence_no) as sequence_no","electronic_approval_setup","company_id=$company_id and page_id=$page_id and sequence_no between $sequence_no and $user_sequence_no and bypass=1","sequence_no");
			
								}
								else
								{
									$sequence_no_by_pass=return_field_value("listagg(sequence_no,',') within group (order by sequence_no) as sequence_no","electronic_approval_setup","company_id=$company_id and page_id=$page_id and sequence_no between $sequence_no and $user_sequence_no and bypass=1","sequence_no");
								}
								
								if($sequence_no_by_pass=="") $sequence_no_cond=" and b.sequence_no='$sequence_no'";
								else $sequence_no_cond=" and b.sequence_no in ($sequence_no_by_pass)";              
							}
							$sql="SELECT DISTINCT (a.ID), a.company_name, a.wo_number_prefix_num, a.supplier_id, a.wo_date, a.DELIVERY_DATE, a.WO_NUMBER, a.item_category, a.currency_id, a.wo_basis_id, a.pay_mode, a.source, a.attention, a.requisition_no,  a.delivery_place, a.location_id
							from wo_non_order_info_mst a, approval_history b, wo_non_order_info_dtls c 
							where a.id=b.mst_id and a.id = c.mst_id and a.entry_form = 146 and a.ready_to_approved =1 and b.entry_form=5 and a.company_name=$company_id $user_crediatial_item_cat_cond and c.item_category_id not in(1,2,3,12,13,14) and a.status_active=1 and a.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and b.current_approval_status=1 and a.wo_number_prefix_num LIKE '%$txt_wo_no%' and a.is_approved in (1,3) $sequence_no_cond $where_cond
							order by a.id";
							// echo $sql;
						}
					}
				
					
					
					  //echo $sql;	die;
					$nameArray=sql_select( $sql ); 
					foreach ($nameArray as $row)
					{
						
						if($select_no){	
							$dataArr[PAGE_ID]=$page_id;
							$dataArr[APP_TITLE]='Stationary Work Order Approval';
							$dataArr[COMPANY_ID]=$company_id;
							$dataArr[APP_ID]=$row->ID;
							$dataArr[MESSAGE1]=$row->WO_NUMBER;
							$dataArr[MESSAGE2]=($row->DELIVERY_DATE)?$row->DELIVERY_DATE:'00-00-0000';
							$dataArr[MESSAGE3]=($buyer_arr[$row->BUYER_ID])?$buyer_arr[$row->BUYER_ID]:'';
							$dataArr[MESSAGE4]='';
						}
						else
						{
							$dataArr[$i][PAGE_ID]=$page_id;
							$dataArr[$i][APP_TITLE]='Stationary Work Order Approval';
							$dataArr[$i][COMPANY_ID]=$company_id;
							$dataArr[$i][APP_ID]=$row->ID;
							
							//$dataArr[DTLS][$i][2][CAPTION]="STYLE REF NO";
							$dataArr[$i][MESSAGE1]=$row->WO_NUMBER;
				
							//$dataArr[DTLS][$i][3][CAPTION]="QUOTATION DATE";
							$dataArr[$i][MESSAGE2]=($row->DELIVERY_DATE)?$row->DELIVERY_DATE:'00-00-0000';
							
							//$dataArr[DTLS][$i][4][CAPTION]="BUYER NAME";
							$dataArr[$i][MESSAGE3]=($buyer_arr[$row->BUYER_ID])?$buyer_arr[$row->BUYER_ID]:'';
							
							//$dataArr[DTLS][$i][5][CAPTION]="";
							$dataArr[$i][MESSAGE4]='';
						}
						$i++; 
						
					}
					
				
				}
			}//company con
			
			
		}//Stationary Work Order Approval if con;
		
		//Other Purchase WO Approval
		if($opw_page_id==628){
			$page_id=628;
			$approval_type=0;
			
			foreach($company_arr as $company_id=>$company_name){
				$user_sequence_no=$user_sequence_no_array[$page_id][$company_id];
				$min_sequence_no=$min_sequence_no_array[$page_id][$company_id];
				//$maxUserNo=$max_sequence_no_array[$page_id][$company_id];
				$buyer_ids_array=$user_buyer_ids_array[$page_id][$company_id];
				
				if($approval_type==0 && $user_sequence_no !=""){

					//if($select_no){$where_cond=" and a.id=$select_no";}
					
									
								
					if($user_sequence_no == $min_sequence_no) // First user
					{     
						if($this->db->dbdriver == 'mysqli')
						{
							$select_item_cat = "group_concat(b.item_category_id) as item_category_id ";
						}else{
							$select_item_cat = "listagg(b.item_category_id, ',') within group (order by b.item_category_id) as item_category_id ";
						}
			
						$sql ="SELECT DISTINCT (a.ID), a.company_name, a.wo_number_prefix_num, a.supplier_id, a.wo_date, a.DELIVERY_DATE, a.WO_NUMBER, a.item_category, a.currency_id, a.wo_basis_id, a.pay_mode, a.source, a.attention, a.requisition_no,  a.delivery_place, a.location_id
						FROM wo_non_order_info_mst a, wo_non_order_info_dtls b 
						WHERE a.id = b.mst_id and a.company_name=$company_id $user_crediatial_item_cat_cond2 and a.is_approved=$approval_type and a.wo_number_prefix_num LIKE '%$txt_wo_no%' and a.ready_to_approved =1 and a.status_active=1 and a.is_deleted=0 and b.item_category_id not in(1,4,5,6,7,11,23)
						order by a.id";
						// echo $sql; 
					}
			
					else // Next user
					{
						$sequence_no=return_field_value("max(sequence_no)","electronic_approval_setup","company_id=$company_id and page_id=$page_id and sequence_no<$user_sequence_no and bypass=2 and is_deleted = 0");
						if($sequence_no=="") // bypass if previous user Yes
						{
							$sql="SELECT DISTINCT (a.ID), a.company_name, a.wo_number_prefix_num, a.supplier_id, a.wo_date, a.DELIVERY_DATE, a.WO_NUMBER, a.item_category, a.currency_id, a.wo_basis_id, a.pay_mode, a.source, a.attention, a.requisition_no,  a.delivery_place, a.location_id
							from wo_non_order_info_mst a, wo_non_order_info_dtls b 
							where a.id =b.mst_id and a.company_name=$company_id $user_crediatial_item_cat_cond2 and b.item_category_id not in(1,4,5,6,7,11,23) and a.is_approved in(0,3) and a.ready_to_approved=1 and a.wo_number_prefix_num LIKE '%$txt_wo_no%' and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0
							order by a.id";
							//echo $sql;
						}
			
						else // bypass No
						{
							$user_sequence_no=$user_sequence_no-1;
							if($sequence_no==$user_sequence_no) 
							{
								$sequence_no_by_pass=$sequence_no;
								$sequence_no_cond=" and b.sequence_no in ($sequence_no_by_pass)";
							}
							else
							{
								if($this->db->dbdriver == 'mysqli')
								{
									$sequence_no_by_pass=return_field_value("group_concat(sequence_no) as sequence_no","electronic_approval_setup","company_id=$company_id and page_id=$page_id and sequence_no between $sequence_no and $user_sequence_no and bypass=1","sequence_no");
			
								}
								else
								{
									$sequence_no_by_pass=return_field_value("listagg(sequence_no,',') within group (order by sequence_no) as sequence_no","electronic_approval_setup","company_id=$company_id and page_id=$page_id and sequence_no between $sequence_no and $user_sequence_no and bypass=1","sequence_no");
								}
								
								if($sequence_no_by_pass=="") $sequence_no_cond=" and b.sequence_no='$sequence_no'";
								else $sequence_no_cond=" and b.sequence_no in ($sequence_no_by_pass)";              
							}
							$sql="SELECT DISTINCT (a.ID), a.company_name, a.wo_number_prefix_num, a.supplier_id, a.wo_date, a.DELIVERY_DATE, a.WO_NUMBER, a.item_category, a.currency_id, a.wo_basis_id, a.pay_mode, a.source, a.attention, a.requisition_no,  a.delivery_place, a.location_id
							from wo_non_order_info_mst a, approval_history b, wo_non_order_info_dtls c 
							where a.id=b.mst_id and a.id = c.mst_id and a.ready_to_approved =1 and b.entry_form=17 and a.company_name=$company_id $user_crediatial_item_cat_cond and c.item_category_id not in(1,4,5,6,7,11,23) and a.status_active=1 and a.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and b.current_approval_status=1 and a.wo_number_prefix_num LIKE '%$txt_wo_no%' and a.is_approved in (1,3) $sequence_no_cond
							order by a.id";
							// echo $sql;
						}
					}
				
				
					
					
					  //echo $sql;	die;
					$nameArray=sql_select( $sql ); 
					foreach ($nameArray as $row)
					{
						
						if($select_no){	
							$dataArr[PAGE_ID]=$page_id;
							$dataArr[APP_TITLE]='Other Purchase WO Approval';
							$dataArr[COMPANY_ID]=$company_id;
							$dataArr[APP_ID]=$row->ID;
							$dataArr[MESSAGE1]=$row->WO_NUMBER;
							$dataArr[MESSAGE2]=($row->DELIVERY_DATE)?$row->DELIVERY_DATE:'00-00-0000';
							$dataArr[MESSAGE3]=($buyer_arr[$row->BUYER_ID])?$buyer_arr[$row->BUYER_ID]:'';
							$dataArr[MESSAGE4]='';
						}
						else
						{
							$dataArr[$i][PAGE_ID]=$page_id;
							$dataArr[$i][APP_TITLE]='Other Purchase WO Approval';
							$dataArr[$i][COMPANY_ID]=$company_id;
							$dataArr[$i][APP_ID]=$row->ID;
							
							//$dataArr[DTLS][$i][2][CAPTION]="STYLE REF NO";
							$dataArr[$i][MESSAGE1]=$row->WO_NUMBER;
				
							//$dataArr[DTLS][$i][3][CAPTION]="QUOTATION DATE";
							$dataArr[$i][MESSAGE2]=($row->DELIVERY_DATE)?$row->DELIVERY_DATE:'00-00-0000';
							
							//$dataArr[DTLS][$i][4][CAPTION]="BUYER NAME";
							$dataArr[$i][MESSAGE3]=($buyer_arr[$row->BUYER_ID])?$buyer_arr[$row->BUYER_ID]:'';
							
							//$dataArr[DTLS][$i][5][CAPTION]="";
							$dataArr[$i][MESSAGE4]='';
						}
						$i++; 
						
					}
					
				
				}
			}//company con
			
			
		}//Other Purchase WO Approval if con;
		
		//Yarn Work Order Approval New
		if($ywn_page_id==412){
			$page_id=412;
			$approval_type=0;
			
			foreach($company_arr as $company_id=>$company_name){
				$user_sequence_no=$user_sequence_no_array[$page_id][$company_id];
				$min_sequence_no=$min_sequence_no_array[$page_id][$company_id];
				//$maxUserNo=$max_sequence_no_array[$page_id][$company_id];
				$buyer_ids_array=$user_buyer_ids_array[$page_id][$company_id];
				
				if($select_no){$where_cond=" and a.id=$select_no";}
				
				if($approval_type==0 && $user_sequence_no !=""){

					
			
					$sequence_no=return_field_value("max(sequence_no)","electronic_approval_setup","page_id=$page_id and sequence_no<$user_sequence_no and bypass=2 and is_deleted=0 and company_id=$company_id");
					
					if($user_sequence_no==$min_sequence_no) // first approval authority
					{
						$sql="SELECT a.ID, a.company_name, a.wo_number_prefix_num, a.supplier_id, a.wo_date, a.DELIVERY_DATE, a.is_approved, a.source, a.payterm_id, a.inserted_by, a.WO_NUMBER, a.wo_basis_id
						from wo_non_order_info_mst a
						where a.company_name=$company_id $date_cond  and a.wo_number_prefix_num LIKE '%$txt_wo_no%' and a.is_deleted=0 and a.status_active=1 and a.ready_to_approved=1 and a.is_approved in (0) and a.entry_form=144 $where_cond";
						 // echo $sql;//die("with sumon");
					}
					else if($sequence_no=="") //last approval authority having bypass=no previlages // Next User bypass Yes
					{
			
						if($this->db->dbdriver == 'mysqli')
						{
							$sequence_no_by=return_field_value("group_concat(sequence_no)","electronic_approval_setup","page_id=$page_id
							and sequence_no<$user_sequence_no and bypass=1 and is_deleted=0");
							$booking_id=return_field_value("group_concat(distinct(mst_id)) as batch_id","wo_non_order_info_mst a, approval_history b","a.id=b.mst_id
							and a.company_name=$company_id and b.sequence_no in ($sequence_no_by) and b.entry_form=2 and b.current_approval_status=1","batch_id");
							$booking_id_app_byuser=return_field_value("group_concat(distinct(mst_id)) as batch_id","wo_non_order_info_mst a, approval_history b",
							"a.id=b.mst_id and a.company_name=$company_id  and b.sequence_no=$user_sequence_no and b.entry_form=2 and
							b.current_approval_status=1","batch_id");
						}
						else
						{
							$sequence_no_by=return_field_value("LISTAGG(sequence_no, ',') WITHIN GROUP (ORDER BY sequence_no) as sequence_no",
							"electronic_approval_setup","page_id=$page_id and sequence_no<$user_sequence_no and bypass=1 and is_deleted=0","sequence_no");
			
							$booking_id=return_field_value("LISTAGG(mst_id, ',') WITHIN GROUP (ORDER BY mst_id) as batch_id","wo_non_order_info_mst a,
							approval_history b","a.id=b.mst_id and a.company_name=$company_id  and b.sequence_no in ($sequence_no_by) and b.entry_form=2
							and b.current_approval_status=1","batch_id");
							$booking_id=implode(",",array_unique(explode(",",$booking_id)));
			
							$booking_id_app_byuser=return_field_value("LISTAGG(mst_id, ',') WITHIN GROUP (ORDER BY mst_id) as batch_id","wo_non_order_info_mst a,
							approval_history b","a.id=b.mst_id and a.company_name=$company_id and b.sequence_no=$user_sequence_no and b.entry_form=2 and
							b.current_approval_status=1","batch_id");
							$booking_id_app_byuser=implode(",",array_unique(explode(",",$booking_id_app_byuser)));
						}
			
						$result=array_diff(explode(',',$booking_id),explode(',',$booking_id_app_byuser));
						$booking_id=implode(",",$result);
			
						$booking_id_cond="";
						if($booking_id_app_byuser!="") $booking_id_cond=" and a.id not in($booking_id_app_byuser)";
			
			
						$sql="SELECT a.ID, a.company_name, a.wo_number_prefix_num, a.supplier_id, a.wo_date, a.DELIVERY_DATE, a.is_approved, a.source, a.payterm_id, a.inserted_by, a.WO_NUMBER, a.wo_basis_id
						from wo_non_order_info_mst a
						where  a.company_name=$company_id $date_cond   and a.wo_number_prefix_num LIKE '%$txt_wo_no%' and a.is_deleted=0 and a.status_active=1 and a.ready_to_approved=1 and a.is_approved in (0,3) and a.entry_form=144 $where_cond $booking_id_cond";
						   
			
					}
					else // bypass No
					{
			
						$user_sequence_no=$user_sequence_no-1;
			
						if($this->db->dbdriver == 'mysqli')
						{
							$sequence_no_by_pass=return_field_value("group_concat(sequence_no)","electronic_approval_setup","page_id=$page_id and
							sequence_no between $sequence_no and $user_sequence_no and bypass=1 and is_deleted=0");
						}
						else
						{
							$sequence_no_by_pass=return_field_value("LISTAGG(sequence_no, ',') WITHIN GROUP (ORDER BY sequence_no) as sequence_no",
							"electronic_approval_setup","page_id=$page_id and sequence_no between $sequence_no and $user_sequence_no and bypass=1 and
							is_deleted=0","sequence_no");
						}
			
			
						if($sequence_no_by_pass=="") $sequence_no_cond=" and c.sequence_no='$user_sequence_no'";
						else $sequence_no_cond=" and (c.sequence_no='$sequence_no' or c.sequence_no in ($sequence_no_by_pass))";
						//echo $sequence_no;die;
						$sql="SELECT a.ID, a.company_name, a.wo_number_prefix_num, a.supplier_id, a.wo_date, a.DELIVERY_DATE, a.is_approved, a.source, a.payterm_id, a.inserted_by, a.WO_NUMBER, a.wo_basis_id
						from wo_non_order_info_mst a, approval_history c
						where  a.id=c.mst_id and  a.company_name=$company_id $date_cond and a.wo_number_prefix_num LIKE '%$txt_wo_no%' and c.entry_form=2 and a.is_deleted=0 and a.status_active=1 and
						a.ready_to_approved = 1 and a.is_approved in(1,3) and c.current_approval_status=1 and a.entry_form=144 $sequence_no_cond $where_cond";
						//echo $sql;//die("with kakku");
					}
				
				
				
					
					
					 //echo $sql;	die;
					$nameArray=sql_select( $sql ); 
					foreach ($nameArray as $row)
					{
						
						if($select_no){	
							$dataArr[PAGE_ID]=$page_id;
							$dataArr[APP_TITLE]='Yarn Work Order Approval New';
							$dataArr[COMPANY_ID]=$company_id;
							$dataArr[APP_ID]=$row->ID;
							$dataArr[MESSAGE1]=$row->WO_NUMBER;
							$dataArr[MESSAGE2]=($row->DELIVERY_DATE)?$row->DELIVERY_DATE:'00-00-0000';
							$dataArr[MESSAGE3]=($buyer_arr[$row->BUYER_ID])?$buyer_arr[$row->BUYER_ID]:'';
							$dataArr[MESSAGE4]='';
						}
						else
						{
							$dataArr[$i][PAGE_ID]=$page_id;
							$dataArr[$i][APP_TITLE]='Yarn Work Order Approval New';
							$dataArr[$i][COMPANY_ID]=$company_id;
							$dataArr[$i][APP_ID]=$row->ID;
							
							//$dataArr[DTLS][$i][2][CAPTION]="STYLE REF NO";
							$dataArr[$i][MESSAGE1]=$row->WO_NUMBER;
				
							//$dataArr[DTLS][$i][3][CAPTION]="QUOTATION DATE";
							$dataArr[$i][MESSAGE2]=($row->DELIVERY_DATE)?$row->DELIVERY_DATE:'00-00-0000';
							
							//$dataArr[DTLS][$i][4][CAPTION]="BUYER NAME";
							$dataArr[$i][MESSAGE3]=($buyer_arr[$row->BUYER_ID])?$buyer_arr[$row->BUYER_ID]:'';
							
							//$dataArr[DTLS][$i][5][CAPTION]="";
							$dataArr[$i][MESSAGE4]='';
						}
						$i++; 
						
					}
					
				
				}
			}//company con
			
			
		}//Yarn Work Order Approval New if con;
		
		//Transfer Requisition Approval
		if($tr_page_id==1630){
			$page_id=1630;
			$approval_type=0;
			
			foreach($company_arr as $company_id=>$company_name){
				$user_sequence_no=$user_sequence_no_array[$page_id][$company_id];
				$min_sequence_no=$min_sequence_no_array[$page_id][$company_id];
				//$maxUserNo=$max_sequence_no_array[$page_id][$company_id];
				$buyer_ids_array=$user_buyer_ids_array[$page_id][$company_id];
				
				if($select_no){$requ_no_cond=" and a.id=$select_no";}
				
				if($approval_type==0 && $user_sequence_no !=""){
						
					if($user_sequence_no==$min_sequence_no) // First user
					{
						$sql ="SELECT a.ID,  a.transfer_prefix_number, a.transfer_system_id,a.CHALLAN_NO, a.company_id, $select_year(a.insert_date $year_format) as year,  a.transfer_criteria, a.TRANSFER_DATE, '0' as approval_id, a.is_approved, 0 as approval_id, a.entry_form 
						from inv_item_transfer_requ_mst a
						where a.company_id=$company_id  and a.is_deleted=0 and a.status_active=1 and a.entry_form in(14,339,180,183,110,353) and a.is_approved=0 and a.ready_to_approve=1 $transfer_criteria $date_cond $requ_no_cond $barcode_cond 
						group by a.id, a.transfer_prefix_number, a.transfer_system_id,a.CHALLAN_NO, a.company_id,a.insert_date,  a.transfer_criteria, a.TRANSFER_DATE, a.is_approved, a.entry_form
						order by a.id";
						//echo $sql;die;
					}
					else // Next user
					{
						$sequence_no=return_field_value("max(sequence_no)","electronic_approval_setup","company_id=$company_id and page_id=$page_id and sequence_no<$user_sequence_no and bypass=2 and is_deleted = 0");
						if($sequence_no=="") // bypass if previous user Yes
						{
							if($this->db->dbdriver == 'mysqli')
							{
								
								$seqSql="select group_concat(sequence_no) as sequence_no_by  from electronic_approval_setup where company_id=$company_id and page_id=$page_id and sequence_no<$user_sequence_no and is_deleted=0";
								$seqData=sql_select($seqSql);
								
								$sequence_no_by=$seqData[0][csf('sequence_no_by')];
								
								$requsition_id=return_field_value("group_concat(distinct(b.mst_id)) as requsition_id","inv_item_transfer_requ_mst a, approval_history b","a.id=b.mst_id  and a.company_id=$company_id and b.sequence_no in ($sequence_no_by) and a.ready_to_approve=1 and b.entry_form=35 and b.current_approval_status=1 and a.is_approved in (3,1)","requsition_id");
								$requsition_id=implode(",",array_unique(explode(",",$requsition_id)));
								
								$requsition_id_app_byuser=return_field_value("group_concat(distinct(b.mst_id)) as requsition_id","inv_item_transfer_requ_mst a, approval_history b","a.id=b.mst_id and a.company_id=$company_id and a.ready_to_approve=1  and b.sequence_no=$user_sequence_no and a.ready_to_approve=1 and b.entry_form=35 and b.current_approval_status=1 and a.is_approved in (3,1)","requsition_id");
							}
							else
							{
								$seqSql="select LISTAGG(sequence_no, ',') WITHIN GROUP (ORDER BY sequence_no) as sequence_no_by from electronic_approval_setup where company_id=$company_id and page_id=$page_id and sequence_no<$user_sequence_no and is_deleted=0";
								$seqData=sql_select($seqSql);
								
								$sequence_no_by=$seqData[0][csf('sequence_no_by')];
				
								$requsition_id=return_field_value("LISTAGG(b.mst_id, ',') WITHIN GROUP (ORDER BY b.mst_id) as requsition_id","inv_item_transfer_requ_mst a, approval_history b","a.id=b.mst_id  and a.company_id=$company_id and b.sequence_no in ($sequence_no_by) and a.ready_to_approve=1 and b.entry_form=37 and b.current_approval_status=1 and a.is_approved in (3,1)","requsition_id");
			
								$requsition_id=implode(",",array_unique(explode(",",$requsition_id)));
								
								$requsition_id_app_byuser=return_field_value("LISTAGG(b.mst_id, ',') WITHIN GROUP (ORDER BY b.mst_id) as requsition_id","inv_item_transfer_requ_mst a, approval_history b","a.id=b.mst_id and a.company_id=$company_id and a.ready_to_approve=1  and b.sequence_no=$user_sequence_no  and b.entry_form=37 and b.current_approval_status=1 and a.is_approved in (3,1)","requsition_id");
								$requsition_id_app_byuser=implode(",",array_unique(explode(",",$requsition_id_app_byuser)));
							}
							$result=array_diff(explode(',',$requsition_id),explode(',',$requsition_id_app_byuser));
							$requsition_id=implode(",",$result);
			
							if($requsition_id!="")
							{					
								$sql=" SELECT x.* from  (SELECT a.id,  a.transfer_prefix_number, a.transfer_system_id,a.CHALLAN_NO, a.company_id, $select_year(a.insert_date $year_format) as year,  a.transfer_criteria, a.TRANSFER_DATE, '0' as approval_id, a.is_approved, a.entry_form
								from inv_item_transfer_requ_mst a
								where a.company_id=$company_id  and a.is_deleted=0 and a.status_active=1 and a.entry_form in(14,339,180,183,110,353) and a.is_approved in(0,3) and a.id in ($requsition_id) and a.ready_to_approve=1 and a.status_active=1 and a.is_deleted=0 $transfer_criteria $date_cond $requ_no_cond  $barcode_cond
								GROUP by a.id,  a.transfer_prefix_number, a.transfer_system_id,a.CHALLAN_NO, a.company_id,a.insert_date,  a.transfer_criteria, a.TRANSFER_DATE, a.is_approved, a.entry_form
			
								UNION ALL
			
								 SELECT a.id,  a.transfer_prefix_number, a.transfer_system_id,a.CHALLAN_NO, a.company_id, $select_year(a.insert_date $year_format) as year,  a.transfer_criteria, a.TRANSFER_DATE, '0' as approval_id, a.is_approved, a.entry_form
								from inv_item_transfer_requ_mst a
								where  a.id not in ($requsition_id) and a.is_approved=$approval_type and a.company_id=$company_id  and a.is_deleted=0 and a.status_active=1 and a.entry_form in(14,339,180,183,110,353) $transfer_criteria $date_cond $requ_no_cond  $barcode_cond
								GROUP by a.id,  a.transfer_prefix_number, a.transfer_system_id,a.CHALLAN_NO, a.company_id,a.insert_date,  a.transfer_criteria, a.TRANSFER_DATE, a.is_approved, a.entry_form) x  order by x.id";
								//echo $sql;
							}
							else
							{ 
								$sql=" SELECT a.id,  a.transfer_prefix_number, a.transfer_system_id,a.CHALLAN_NO, a.company_id, $select_year(a.insert_date $year_format) as year,  a.transfer_criteria, a.TRANSFER_DATE, '0' as approval_id, a.is_approved, a.entry_form
								from inv_item_transfer_requ_mst a 
								where a.is_approved=$approval_type and a.company_id=$company_id and a.is_deleted=0 and a.status_active=1 and a.entry_form in(14,339,180,183,110,353) $transfer_criteria $date_cond $requ_no_cond  $barcode_cond
								GROUP by a.id,  a.transfer_prefix_number, a.transfer_system_id,a.CHALLAN_NO, a.company_id,a.insert_date,  a.transfer_criteria, a.TRANSFER_DATE, a.is_approved, a.entry_form";
								//echo $sql;
							}
							//echo $sql;
						}
						
						else // bypass No
						{
							$user_sequence_no=$user_sequence_no-1;
							//echo $sequence_no;
							if($sequence_no==$user_sequence_no) 
							{
								$sequence_no_by_pass=$sequence_no;
								$sequence_no_cond=" and c.sequence_no in ($sequence_no_by_pass)";
							}
							else
							{
								if($this->db->dbdriver == 'mysqli') 
								{
									$sequence_no_by_pass=return_field_value("group_concat(sequence_no) as sequence_no","electronic_approval_setup","company_id=$company_id and page_id=$page_id and sequence_no between $sequence_no and $user_sequence_no and bypass=1","sequence_no");
								}
								else
								{
									$sequence_no_by_pass=return_field_value("listagg(sequence_no,',') within group (order by sequence_no) as sequence_no","electronic_approval_setup","company_id=$company_id and page_id=$page_id and sequence_no between $sequence_no and $user_sequence_no and bypass=1","sequence_no");
								}
								
								if($sequence_no_by_pass=="") $sequence_no_cond=" and c.sequence_no='$sequence_no'";
								else $sequence_no_cond=" and c.sequence_no in ($sequence_no_by_pass)";
							}
			
							$sql=" SELECT a.id,  a.transfer_prefix_number, a.transfer_system_id,a.CHALLAN_NO, a.company_id, $select_year(a.insert_date $year_format) as year,  a.transfer_criteria, a.TRANSFER_DATE, '0' as approval_id, a.is_approved, c.id as approval_id, a.entry_form
							from inv_item_transfer_requ_mst a, approval_history c 
							where   a.is_approved in (1,3) and a.id=c.mst_id and c.entry_form in(37)  and a.company_id=$company_id  and a.is_deleted=0 and a.status_active=1 and a.entry_form in(14,339,180,183,110,353) and c.current_approval_status=1 $sequence_no_cond $transfer_criteria $date_cond $requ_no_cond $barcode_cond
							GROUP by a.id,  a.transfer_prefix_number, a.transfer_system_id,a.CHALLAN_NO, a.company_id,a.insert_date,  a.transfer_criteria, a.TRANSFER_DATE, a.is_approved,c.id, a.entry_form
							order by a.id";
							//echo $sql;
						}
					}
				
					
					
					 //echo $sql;	die;
					$nameArray=sql_select( $sql ); 
					foreach ($nameArray as $row)
					{
						
						if($select_no){	
							$dataArr[PAGE_ID]=$page_id;
							$dataArr[APP_TITLE]='Transfer Requisition Approval';
							$dataArr[COMPANY_ID]=$company_id;
							$dataArr[APP_ID]=$row->ID;
							$dataArr[MESSAGE1]=$row->CHALLAN_NO.' ';
							$dataArr[MESSAGE2]=($row->TRANSFER_DATE)?$row->TRANSFER_DATE:'00-00-0000';
							$dataArr[MESSAGE3]=($buyer_arr[$row->BUYER_ID])?$buyer_arr[$row->BUYER_ID]:'';
							$dataArr[MESSAGE4]='';
						}
						else
						{
							$dataArr[$i][PAGE_ID]=$page_id;
							$dataArr[$i][APP_TITLE]='Transfer Requisition Approval';
							$dataArr[$i][COMPANY_ID]=$company_id;
							$dataArr[$i][APP_ID]=$row->ID;
							
							//$dataArr[DTLS][$i][2][CAPTION]="STYLE REF NO";
							$dataArr[$i][MESSAGE1]= $row->CHALLAN_NO.' ';
				
							//$dataArr[DTLS][$i][3][CAPTION]="QUOTATION DATE";
							$dataArr[$i][MESSAGE2]=($row->TRANSFER_DATE)?$row->TRANSFER_DATE:'00-00-0000';
							
							//$dataArr[DTLS][$i][4][CAPTION]="BUYER NAME";
							$dataArr[$i][MESSAGE3]=($buyer_arr[$row->BUYER_ID])?$buyer_arr[$row->BUYER_ID]:'';
							
							//$dataArr[DTLS][$i][5][CAPTION]="";
							$dataArr[$i][MESSAGE4]='';
						}
						$i++; 
						
					}
					
				
				}
			}//company con
			
			
		}//Transfer Requisition Approval if con;
 
 
		//Import Document Acceptance Approval
		if($ida_page_id==1684){
			$page_id=1684;
			$approval_type=0;
			
			foreach($company_arr as $company_id=>$company_name){
				$user_sequence_no=$user_sequence_no_array[$page_id][$company_id];
				$min_sequence_no=$min_sequence_no_array[$page_id][$company_id];
				//$maxUserNo=$max_sequence_no_array[$page_id][$company_id];
				$buyer_ids_array=$user_buyer_ids_array[$page_id][$company_id];
				
				if($select_no){$invoice_no_cond=" and a.id=$select_no";}
				
				
				if($approval_type==0 && $user_sequence_no !=""){
						
					if($user_sequence_no==$min_sequence_no) // First user
					{
					   $sql="select a.ID,a.INVOICE_NO,a.INVOICE_DATE ,a.IS_LC,a.ACCEPTANCE_TIME,c.LC_NUMBER,c.LC_DATE, c.SUPPLIER_ID, c.IMPORTER_ID,c.PI_ID,c.PI_VALUE,b.CURRENT_ACCEPTANCE_VALUE from com_import_invoice_mst a,com_import_invoice_dtls b,com_btb_lc_master_details c where a.id=b.import_invoice_id and c.id=b.BTB_LC_ID and a.STATUS_ACTIVE=1 and a.IS_DELETED=0 and b.STATUS_ACTIVE=1 and b.IS_DELETED=0 $date_cond $invoice_no_cond  and c.IMPORTER_ID=$company_id $supplier_cond 
					   and a.id not in(SELECT d.mst_id FROM com_import_invoice_mst a, com_import_invoice_dtls b,approval_history d,com_btb_lc_master_details c WHERE a.id=b.import_invoice_id and a.id=d.mst_id and c.id=b.BTB_LC_ID and a.STATUS_ACTIVE=1 and a.IS_DELETED=0 and b.STATUS_ACTIVE=1 and b.IS_DELETED=0 and d.current_approval_status=1 and d.entry_form=38 and d.approved_by= $user_id $date_cond $invoice_no_cond  and c.IMPORTER_ID=$company_id $supplier_cond )
					   ";
					}
					else // Next user
					{
						
						$sequence_no=return_field_value("max(sequence_no)","electronic_approval_setup","company_id=$company_id and page_id=$page_id and sequence_no<$user_sequence_no and bypass=2 and is_deleted = 0");
			
						if($sequence_no=="") // bypass if previous user Yes
						{
						   
							$invoice_id_app_byuser_arr = return_library_array("select mst_id, mst_id from com_import_invoice_mst a, approval_history b where a.id=b.mst_id and  b.sequence_no=$user_sequence_no and b.entry_form=38 and b.current_approval_status=1", 'mst_id', 'mst_id');
							$invoice_id_app_byuser=implode(',',$invoice_id_app_byuser_arr);
							
							$invoice_id_cond="";
							if($invoice_id_app_byuser!="") $invoice_id_cond=" and a.id not in($invoice_id_app_byuser)";
			
						   $sql="select a.ID,a.INVOICE_NO,a.INVOICE_DATE ,a.IS_LC,a.ACCEPTANCE_TIME,c.LC_NUMBER,c.LC_DATE, c.SUPPLIER_ID, c.IMPORTER_ID,b.CURRENT_ACCEPTANCE_VALUE from com_import_invoice_mst a,com_import_invoice_dtls b,com_btb_lc_master_details c where a.id=b.import_invoice_id and c.id=b.BTB_LC_ID and a.STATUS_ACTIVE=1 and a.IS_DELETED=0 and b.STATUS_ACTIVE=1 and b.IS_DELETED=0  $date_cond $invoice_no_cond  and c.IMPORTER_ID=$company_id $supplier_cond $supplier_cond
						   and a.id not in(SELECT d.mst_id FROM com_import_invoice_mst a, com_import_invoice_dtls b,approval_history d,com_btb_lc_master_details c WHERE a.id=b.import_invoice_id and a.id=d.mst_id and c.id=b.BTB_LC_ID and a.STATUS_ACTIVE=1 and a.IS_DELETED=0 and b.STATUS_ACTIVE=1 and b.IS_DELETED=0 and d.current_approval_status=1 and d.entry_form=38 and d.approved_by= $user_id $date_cond $invoice_no_cond  and c.IMPORTER_ID=$company_id $supplier_cond )
						   ";
							  
						}
						else // bypass No
						{
						  $user_sequence_no=$user_sequence_no-1;
			
							$sequence_no_by_pass_arr = return_library_array("select sequence_no, sequence_no from electronic_approval_setup where page_id=$page_id and sequence_no between $sequence_no and $user_sequence_no and bypass=1 and is_deleted=0", 'sequence_no', 'sequence_no');
							
							$sequence_no_by_pass=implode(',',$sequence_no_by_pass_arr);
							
							if($sequence_no_by_pass=="") $sequence_no_cond=" and d.sequence_no='$user_sequence_no'";
							else $sequence_no_cond=" and (d.sequence_no='$sequence_no' or d.sequence_no in ($sequence_no_by_pass))";
							
							$sql="select a.ID,a.INVOICE_NO,a.INVOICE_DATE ,a.IS_LC,a.ACCEPTANCE_TIME,c.LC_NUMBER,c.LC_DATE, c.SUPPLIER_ID, c.IMPORTER_ID,c.PI_ID,c.PI_VALUE,b.CURRENT_ACCEPTANCE_VALUE from com_import_invoice_mst a,com_import_invoice_dtls b,com_btb_lc_master_details c,approval_history d where a.id=b.import_invoice_id and c.id=b.BTB_LC_ID and a.id=d.mst_id and a.STATUS_ACTIVE=1 and a.IS_DELETED=0 and b.STATUS_ACTIVE=1 and b.IS_DELETED=0  $date_cond $sequence_no_cond $invoice_no_cond  and c.IMPORTER_ID=$company_id $supplier_cond
							and a.id not in(SELECT d.mst_id FROM com_import_invoice_mst a, com_import_invoice_dtls b,approval_history d,com_btb_lc_master_details c WHERE a.id=b.import_invoice_id and a.id=d.mst_id and c.id=b.BTB_LC_ID and a.STATUS_ACTIVE=1 and a.IS_DELETED=0 and b.STATUS_ACTIVE=1 and b.IS_DELETED=0 and d.current_approval_status=1 and d.entry_form=38 and d.approved_by= $user_id $date_cond $invoice_no_cond  and c.IMPORTER_ID=$company_id $supplier_cond )
							";             
						}
					}
				
				
				
					$nameArray=sql_select( $sql ); 
					foreach ($nameArray as $row)
					{
						
						if($select_no){	
							$dataArr[PAGE_ID]=$page_id;
							$dataArr[APP_TITLE]='Import Document Acceptance Approval';
							$dataArr[COMPANY_ID]=$company_id;
							$dataArr[APP_ID]=$row->ID;
							$dataArr[MESSAGE1]=$row->INVOICE_NO.' ';
							$dataArr[MESSAGE2]=($row->INVOICE_DATE)?$row->INVOICE_DATE:'00-00-0000';
							$dataArr[MESSAGE3]=($buyer_arr[$row->BUYER_ID])?$buyer_arr[$row->BUYER_ID]:'';
							$dataArr[MESSAGE4]='';
						}
						else
						{
							$dataArr[$i][PAGE_ID]=$page_id;
							$dataArr[$i][APP_TITLE]='Import Document Acceptance Approval';
							$dataArr[$i][COMPANY_ID]=$company_id;
							$dataArr[$i][APP_ID]=$row->ID;
							
							//$dataArr[DTLS][$i][2][CAPTION]="STYLE REF NO";
							$dataArr[$i][MESSAGE1]= $row->INVOICE_NO.' ';
				
							//$dataArr[DTLS][$i][3][CAPTION]="QUOTATION DATE";
							$dataArr[$i][MESSAGE2]=($row->INVOICE_DATE)?$row->INVOICE_DATE:'00-00-0000';
							
							//$dataArr[DTLS][$i][4][CAPTION]="BUYER NAME";
							$dataArr[$i][MESSAGE3]=($buyer_arr[$row->BUYER_ID])?$buyer_arr[$row->BUYER_ID]:'';
							
							//$dataArr[DTLS][$i][5][CAPTION]="";
							$dataArr[$i][MESSAGE4]='';
						}
						$i++; 
						
					}
					
				
				}
			}//company con
			
			
		}//Import Document Acceptance Approval if con;
 
 
		//Embellishment Work Order Approval V2
		if($ewo_page_id==1257){
			$page_id=1257;
			$approval_type=0;
			
			foreach($company_arr as $company_id=>$company_name){
				$user_sequence_no=$user_sequence_no_array[$page_id][$company_id];
				$min_sequence_no=$min_sequence_no_array[$page_id][$company_id];
				//$maxUserNo=$max_sequence_no_array[$page_id][$company_id];
				$buyer_ids_array=$user_buyer_ids_array[$page_id][$company_id];
				
				
				$buyer_id_cond=$buyer_id_cond2=$sequence_no_cond=$date_cond=$booking_no_cond=$booking_year_cond='';
								
				if($select_no){$booking_no_cond=" and a.id=$select_no";}
				if($approval_type==0 && $user_sequence_no !=""){
						
					if($this->db->dbdriver == 'mysqli'){$orderBy_cond="IFNULL";}
					else{$orderBy_cond="NVL";}
					
					
					
					
					if($this->db->dbdriver == 'mysqli')
					{
						$sequence_no=return_field_value("max(sequence_no) as seq","electronic_approval_setup","company_id=$company_id and page_id=$page_id and sequence_no<$user_sequence_no and bypass=2 and buyer_id='' and is_deleted=0","seq");
					}
					else
					{
						$sequence_no=return_field_value("max(sequence_no) as seq","electronic_approval_setup","company_id=$company_id and page_id=$page_id and sequence_no<$user_sequence_no and bypass=2 and buyer_id is null and is_deleted=0","seq");
					}
					// echo $user_sequence_no.'=='.$min_sequence_no;
					if($user_sequence_no==$min_sequence_no)
					{	
						$buyer_ids=$buyer_ids_array[$user_id]['u'];
						if($buyer_ids=="") $buyer_id_cond=""; else $buyer_id_cond=" and a.buyer_id in($buyer_ids)";
						
						$sql="SELECT a.id,a.entry_form,  a.booking_no_prefix_num, a.BOOKING_NO, a.item_category, a.fabric_source, a.company_id, a.booking_type, a.is_short, a.BUYER_ID, a.supplier_id, a.delivery_date, a.BOOKING_DATE, '0' as approval_id, a.is_approved, a.is_apply_last_update from wo_booking_mst a, wo_booking_dtls b where a.booking_no=b.booking_no  and a.company_id=$company_id and a.is_short in(2,3) and a.booking_type=6 and a.item_category in(25) and a.is_deleted=0 and a.status_active=1 and b.status_active=1 and b.is_deleted=0 and a.ready_to_approved=1 and a.is_approved=$approval_type  $buyer_id_cond $buyer_id_cond2 $booking_no_cond $date_cond $booking_year_cond group by a.id, a.booking_no, a.item_category, a.fabric_source, a.company_id, a.booking_type, a.is_short, a.BUYER_ID, a.supplier_id, a.delivery_date, a.BOOKING_DATE, a.is_approved, a.is_apply_last_update, a.insert_date, a.update_date, a.booking_no_prefix_num,a.entry_form order by $orderBy_cond(a.update_date, a.insert_date) desc";
						//echo $sql;
					}
					else if($sequence_no=="")
					{  
						$buyer_ids=$buyer_ids_array[$user_id]['u'];
						if($buyer_ids=="") $buyer_id_cond=""; else $buyer_id_cond=" and a.BUYER_ID in($buyer_ids)";
						
						if($this->db->dbdriver == 'mysqli')
						{
			
							$seqSql="select group_concat(sequence_no) as sequence_no_by,
					group_concat(case when bypass=2 and buyer_id<>'' then buyer_id end) as buyer_ids from electronic_approval_setup where company_id=$company_id and page_id=$page_id and sequence_no<$user_sequence_no and is_deleted=0";
							$seqData=sql_select($seqSql);
							
							$sequence_no_by=$seqData[0][csf('sequence_no_by')];
							$buyerIds=$seqData[0][csf('buyer_ids')];
							
							if($buyerIds=="") $buyerIds_cond=""; else $buyerIds_cond=" and a.BUYER_ID not in($buyerIds)";
							
							$booking_id=return_field_value("group_concat(distinct(mst_id)) as booking_id","wo_booking_mst a, approval_history b","a.id=b.mst_id and a.company_id=$company_id and a.is_short=2 and a.booking_type=6 and a.item_category in(25) and b.sequence_no in ($sequence_no_by) and b.entry_form=32 and b.current_approval_status=1 $buyer_id_cond $date_cond","booking_id");
							
							$booking_id_app_byuser=return_field_value("group_concat(distinct(mst_id)) as booking_id","wo_booking_mst a, approval_history b","a.id=b.mst_id and a.company_id=$company_id and a.is_short=2 and a.booking_type=6 and a.item_category in(25) and b.sequence_no=$user_sequence_no and b.entry_form=32 and b.current_approval_status=1","booking_id");
						}
						else
						{
							
							$seqSql="select sequence_no, bypass, buyer_id from electronic_approval_setup where company_id=$company_id and page_id=$page_id and sequence_no<$user_sequence_no and is_deleted=0 order by sequence_no desc";
							$seqData=sql_select($seqSql);
							
							$buyerIds=''; $sequence_no_by_yes=''; $sequence_no_by_no=''; $approved_string=''; $check_buyerIds_arr=array();
							foreach($seqData as $sRow)
							{
								if($sRow[csf('bypass')]==2)
								{
									$sequence_no_by_no.=$sRow[csf('sequence_no')].",";
									if($sRow[csf('buyer_id')]!="") 
									{
										$buyerIds.=$sRow[csf('buyer_id')].",";
										
										$buyer_id_arr=explode(",",$sRow[csf('buyer_id')]);
										$result=array_diff($buyer_id_arr,$check_buyerIds_arr);
										if(count($result)>0)
										{
											$query_string.=" (b.sequence_no=".$sRow[csf('sequence_no')]." and a.BUYER_ID in(".implode(",",$result).")) or ";
										}
										$check_buyerIds_arr=array_unique(array_merge($check_buyerIds_arr,$buyer_id_arr));
									}
								}
								else
								{
									$sequence_no_by_yes.=$sRow[csf('sequence_no')].",";
								}
							}
							//var_dump($check_buyerIds_arr);die;
							$buyerIds=chop($buyerIds,',');
							if($buyerIds=="") 
							{
								$buyerIds_cond=""; 
								$seqCond="";
							}
							else 
							{
								$buyerIds_cond=" and a.BUYER_ID not in($buyerIds)";
								$seqCond=" and (".chop($query_string,'or ').")";
							}
							//echo $seqCond;die;
							$sequence_no_by_no=chop($sequence_no_by_no,',');
							$sequence_no_by_yes=chop($sequence_no_by_yes,',');
							
							if($sequence_no_by_yes=="") $sequence_no_by_yes=0;
							if($sequence_no_by_no=="") $sequence_no_by_no=0;
							
							$booking_id='';
							$booking_id_sql="select distinct (mst_id) as BOOKING_ID from wo_booking_mst a, approval_history b where a.id=b.mst_id and a.company_id=$company_id and a.is_short=2 and a.booking_type=6 and a.item_category in(25) and b.sequence_no in ($sequence_no_by_no) and b.entry_form=32 and b.current_approval_status=1 $buyer_id_cond $date_cond $seqCond
							union
							select distinct (mst_id) as BOOKING_ID from wo_booking_mst a, approval_history b where a.id=b.mst_id and a.company_id=$company_id and a.is_short=2 and a.booking_type=6 and a.item_category in(25) and b.sequence_no in ($sequence_no_by_yes) and b.entry_form=32 and b.current_approval_status=1 $buyer_id_cond $date_cond";
							$bResult=sql_select($booking_id_sql);
							foreach($bResult as $bRow)
							{
								$booking_id.=$bRow->BOOKING_ID.",";
							}
							
							$booking_id=chop($booking_id,',');
							
							$booking_id_app_byuser=return_field_value("LISTAGG(mst_id, ',') WITHIN GROUP (ORDER BY mst_id) as booking_id","wo_booking_mst a, approval_history b","a.id=b.mst_id and a.company_id=$company_id and a.is_short=2 and a.booking_type=6 and a.item_category in(25) and b.sequence_no=$user_sequence_no and b.entry_form=32 and b.current_approval_status=1","booking_id");
							$booking_id_app_byuser=implode(",",array_unique(explode(",",$booking_id_app_byuser)));
						}
						//echo $booking_id;die;
						$result=array_diff(explode(',',$booking_id),explode(',',$booking_id_app_byuser));
						$booking_id=implode(",",$result);
						
						$booking_id_cond="";
						if($booking_id!="")
						{
							if($db_type==2 && count($result)>999)
							{
								$booking_id_chunk_arr=array_chunk($result,999) ;
								foreach($booking_id_chunk_arr as $chunk_arr)
								{
									$chunk_arr_value=implode(",",$chunk_arr);	
									$bokIds_cond.=" a.id in($chunk_arr_value) or ";	
								}
								
								$booking_id_cond.=" and (".chop($bokIds_cond,'or ').")";			
								//echo $booking_id_cond;die;
							}
							else
							{
								$booking_id_cond=" and a.id in($booking_id)";	 
							}
						}
						else $booking_id_cond="";
						
						
						
						if($this->db->dbdriver == 'mysqli')
						{
							if($booking_id!="")
							{
								$sql="select a.entry_form,a.update_date as ob_update, a.insert_date as ob_insertdate, a.id,  a.booking_no_prefix_num, a.BOOKING_NO, a.item_category, a.fabric_source, a.company_id, a.booking_type, a.is_short, a.BUYER_ID, a.supplier_id, a.delivery_date, a.BOOKING_DATE, '0' as approval_id,  a.is_approved, a.is_apply_last_update from wo_booking_mst a, wo_booking_dtls b where a.booking_no=b.booking_no and a.company_id=$company_id and a.is_short in(2,3) and a.booking_type=6 and a.item_category in(25) and a.is_deleted=0 and a.status_active=1 and b.status_active=1 and b.is_deleted=0 and a.ready_to_approved=1 and a.is_approved=$approval_type $buyer_id_cond  $date_cond $buyerIds_cond $buyer_id_cond2 $booking_no_cond group by a.id, a.booking_no, a.item_category, a.fabric_source, a.company_id, a.booking_type, a.is_short, a.BUYER_ID, a.supplier_id, a.delivery_date, a.BOOKING_DATE, a.is_approved, a.is_apply_last_update, a.insert_date, a.update_date, a.booking_no_prefix_num,a.entry_form
									union all
									select a.entry_form,a.update_date as ob_update, a.insert_date as ob_insertdate, a.id,  a.booking_no_prefix_num, a.BOOKING_NO, a.item_category, a.fabric_source, a.company_id, a.booking_type, a.is_short, a.BUYER_ID, a.supplier_id, a.delivery_date, a.BOOKING_DATE, '0' as approval_id, a.is_approved, a.is_apply_last_update from wo_booking_mst a, wo_booking_dtls b where a.booking_no=b.booking_no and a.company_id=$company_id and a.is_short in(2,3) and a.booking_type=6 and a.item_category in(25) and a.is_deleted=0 and a.status_active=1 and b.status_active=1 and b.is_deleted=0 and a.ready_to_approved=1 and a.is_approved in(1,3) and a.id in($booking_id) $buyer_id_cond $buyer_id_cond2 $date_cond $booking_no_cond group by a.id, a.booking_no, a.item_category, a.fabric_source, a.company_id, a.booking_type, a.is_short, a.BUYER_ID, a.supplier_id, a.delivery_date, a.BOOKING_DATE,a.is_approved, a.is_apply_last_update, a.insert_date, a.update_date, a.booking_no_prefix_num,a.entry_form order by $orderBy_cond(ob_update, ob_insertdate) desc";
							}
							else
							{
								$sql="select a.entry_form,a.id,  a.booking_no_prefix_num, a.BOOKING_NO, a.item_category, a.fabric_source, a.company_id, a.booking_type, a.is_short, a.BUYER_ID, a.supplier_id, a.delivery_date, a.BOOKING_DATE, '0' as approval_id, a.is_approved, a.is_apply_last_update from wo_booking_mst a, wo_booking_dtls b where a.booking_no=b.booking_no and a.company_id=$company_id and a.is_short in(2,3) and a.booking_type=6 and a.item_category in(25) and a.is_deleted=0 and a.status_active=1 and b.status_active=1 and b.is_deleted=0 and a.ready_to_approved=1 and a.is_approved=$approval_type  $buyer_id_cond $buyer_id_cond2 $date_cond $buyerIds_cond $booking_no_cond $booking_year_cond group by a.id, a.booking_no, a.item_category, a.fabric_source, a.company_id, a.booking_type, a.is_short, a.BUYER_ID, a.supplier_id, a.delivery_date, a.BOOKING_DATE, a.is_approved, a.is_apply_last_update, a.insert_date, a.update_date, a.booking_no_prefix_num,a.entry_form order by $orderBy_cond(a.update_date, a.insert_date) desc";
							}
							//echo $sql;
						}
						else
						{
							if($booking_id!="")
							{   // and a.id in($booking_id)
								$sql="select * from(select a.entry_form,a.update_date, a.insert_date, a.id,  a.booking_no_prefix_num, a.BOOKING_NO, a.item_category, a.fabric_source, a.company_id, a.booking_type, a.is_short, a.BUYER_ID, a.supplier_id, a.delivery_date, a.BOOKING_DATE, '0' as approval_id, a.is_approved, a.is_apply_last_update from wo_booking_mst a, wo_booking_dtls b where a.booking_no=b.booking_no and a.company_id=$company_id and a.is_short in(2,3) and a.booking_type=6 and a.item_category in(25) and a.is_deleted=0 and a.status_active=1 and b.status_active=1 and b.is_deleted=0 and a.ready_to_approved=1 and a.is_approved=$approval_type  $buyer_id_cond  $date_cond $buyerIds_cond $buyer_id_cond2 $booking_no_cond group by a.id, a.booking_no,a.entry_form, a.item_category, a.fabric_source, a.company_id, a.booking_type, a.is_short, a.BUYER_ID, a.supplier_id, a.delivery_date, a.BOOKING_DATE, a.is_approved, a.is_apply_last_update, a.insert_date, a.update_date, a.booking_no_prefix_num
									union all
									select a.entry_form,a.update_date, a.insert_date, a.id,  a.booking_no_prefix_num, a.booking_no, a.item_category, a.fabric_source, a.company_id, a.booking_type, a.is_short, a.BUYER_ID, a.supplier_id, a.delivery_date, a.BOOKING_DATE, '0' as approval_id, a.is_approved, a.is_apply_last_update from wo_booking_mst a, wo_booking_dtls b  where a.booking_no=b.booking_no and a.company_id=$company_id and a.is_short in(2,3) and a.booking_type=6 and a.item_category in(25) and a.is_deleted=0 and a.status_active=1 and b.status_active=1 and b.is_deleted=0 and a.ready_to_approved=1 and a.is_approved in (1,3)  $booking_id_cond $buyer_id_cond $buyer_id_cond2 $date_cond $booking_no_cond group by a.id, a.booking_no,a.entry_form, a.item_category, a.fabric_source, a.company_id, a.booking_type, a.is_short, a.BUYER_ID, a.supplier_id, a.delivery_date, a.BOOKING_DATE, a.is_approved, a.is_apply_last_update, a.insert_date, a.update_date, a.booking_no_prefix_num) order by $orderBy_cond(update_date, insert_date) desc";  
							}
							else
							{
								$sql="select a.entry_form,a.id,  a.booking_no_prefix_num, a.booking_no, a.item_category, a.fabric_source, a.company_id, a.booking_type, a.is_short, a.BUYER_ID, a.supplier_id, a.delivery_date, a.BOOKING_DATE, '0' as approval_id, a.is_approved, a.is_apply_last_update from wo_booking_mst a, wo_booking_dtls b where a.booking_no=b.booking_no and a.company_id=$company_id and a.is_short in(2,3) and a.booking_type=6 and a.item_category in(25) and a.is_deleted=0 and a.status_active=1 and b.status_active=1 and b.is_deleted=0 and a.ready_to_approved=1 and a.is_approved=$approval_type  $buyer_id_cond $buyer_id_cond2   $date_cond $buyerIds_cond $booking_no_cond $booking_year_cond group by a.id, a.booking_no,a.entry_form, a.item_category, a.fabric_source, a.company_id, a.booking_type, a.is_short, a.BUYER_ID, a.supplier_id, a.delivery_date, a.BOOKING_DATE, a.is_approved, a.is_apply_last_update, a.insert_date, a.update_date, a.booking_no_prefix_num order by $orderBy_cond(a.update_date, a.insert_date) desc";
								// echo $sql;die;
							}
							
						
						}
					}
					else
					{
						$buyer_ids=$buyer_ids_array[$user_id]['u'];
						if($buyer_ids=="") $buyer_id_cond=""; else $buyer_id_cond=" and a.BUYER_ID in($buyer_ids)";
						
						$user_sequence_no = $user_sequence_no-1;
						
							if($this->db->dbdriver == 'mysqli')
							{
								$sequence_no_by_pass=return_field_value("group_concat(sequence_no)","electronic_approval_setup","company_id=$company_id and page_id=$page_id and sequence_no between $sequence_no and $user_sequence_no and is_deleted=0");
							}
							else
							{
								$sequence_no_by_pass=return_field_value("LISTAGG(sequence_no, ',') WITHIN GROUP (ORDER BY sequence_no) as sequence_no","electronic_approval_setup","company_id=$company_id and page_id=$page_id and sequence_no between $sequence_no and $user_sequence_no and is_deleted=0","sequence_no");	
							}
							
							if($sequence_no_by_pass=="") $sequence_no_cond=" and b.sequence_no='$sequence_no'";
							else $sequence_no_cond=" and b.sequence_no in ($sequence_no_by_pass)";
							
							$sql="select a.entry_form,a.id,  a.booking_no_prefix_num, a.BOOKING_NO, a.item_category, a.fabric_source, a.company_id, a.booking_type, a.is_short, a.BUYER_ID, a.supplier_id, a.delivery_date, a.BOOKING_DATE,a.is_approved, b.id as approval_id, b.sequence_no, b.approved_by, a.is_apply_last_update from wo_booking_mst a, approval_history b where a.id=b.mst_id  and b.entry_form=32 and a.company_id=$company_id and a.is_short in(2,3) and a.booking_type=6 and a.item_category in(25) and a.status_active=1 and a.is_deleted=0 and b.current_approval_status=1 and a.ready_to_approved=1 and a.is_approved in (1,3) $buyer_id_cond $buyer_id_cond2 $sequence_no_cond   $date_cond $booking_no_cond $booking_year_cond order by $orderBy_cond(a.update_date, a.insert_date) desc";
						
					}
			



				
				
					$nameArray=sql_select( $sql ); 
					foreach ($nameArray as $row)
					{
						
						if($select_no){	
							$dataArr[PAGE_ID]=$page_id;
							$dataArr[APP_TITLE]='Embellishment Work Order Approval V2';
							$dataArr[COMPANY_ID]=$company_id;
							$dataArr[APP_ID]=$row->ID;
							$dataArr[MESSAGE1]=$row->BOOKING_NO;
							$dataArr[MESSAGE2]=($row->BOOKING_DATE)?$row->BOOKING_DATE:'00-00-0000';
							$dataArr[MESSAGE3]=($buyer_arr[$row->BUYER_ID])?$buyer_arr[$row->BUYER_ID]:'';
							$dataArr[MESSAGE4]='';
						}
						else
						{
							$dataArr[$i][PAGE_ID]=$page_id;
							$dataArr[$i][APP_TITLE]='Embellishment Work Order Approval V2';
							$dataArr[$i][COMPANY_ID]=$company_id;
							$dataArr[$i][APP_ID]=$row->ID;
							
							//$dataArr[DTLS][$i][2][CAPTION]="STYLE REF NO";
							$dataArr[$i][MESSAGE1]= $row->BOOKING_NO;
				
							//$dataArr[DTLS][$i][3][CAPTION]="QUOTATION DATE";
							$dataArr[$i][MESSAGE2]=($row->BOOKING_DATE)?$row->BOOKING_DATE:'00-00-0000';
							
							//$dataArr[DTLS][$i][4][CAPTION]="BUYER NAME";
							$dataArr[$i][MESSAGE3]=($buyer_arr[$row->BUYER_ID])?$buyer_arr[$row->BUYER_ID]:'';
							
							//$dataArr[DTLS][$i][5][CAPTION]="";
							$dataArr[$i][MESSAGE4]='';
						}
						$i++; 
						
					}
					
				
				}
			}//company con
			
			
		}//Embellishment Work Order Approval V2 if con;
 
 

 
		
		//return value...........................
		if(count($dataArr)==0){
			if($select_no){	
				$dataArr[PAGE_ID]=0;
				$dataArr[APP_TITLE]='';
				$dataArr[COMPANY_ID]=0;
				$dataArr[APP_ID]='';
				$dataArr[MESSAGE1]='';
				$dataArr[MESSAGE2]='';
				$dataArr[MESSAGE3]='';
				$dataArr[MESSAGE4]='';
			}
			else
			{
				$dataArr[$i][PAGE_ID]=0;
				$dataArr[$i][APP_TITLE]='';
				$dataArr[$i][COMPANY_ID]=0;
				$dataArr[$i][APP_ID]='';
				$dataArr[$i][MESSAGE1]='';
				$dataArr[$i][MESSAGE2]='';
				$dataArr[$i][MESSAGE3]='';
				$dataArr[$i][MESSAGE4]='';
			}
			
		}
		
		
		
		return $dataArr;
		
		
		
	}
	//.........
	

		
	public function save_update_all_approval($save_obj){
		$response_obj = json_decode($save_obj);
		 //return $response_obj;
		
		  	$user_id_approval = $response_obj->data->index->user_id;
			$app_id = $response_obj->data->index->app_id;
			$company_id = $response_obj->data->index->company_id;
			$page_id = $response_obj->data->index->page_id;
			if($this->db->dbdriver == 'mysqli'){
				$pc_date_time = date("Y-m-d H:i:s",time());
				$app_date = date("Y-m-d",time());
			}
			else{
				$pc_date_time = date("d-M-Y h:i:s A",time());
				$app_date = date("d-M-Y",time());
			}
			

			
			//$page_id 867=PI app;  410=FB app; 428=Pre Costing; 427=Price Quotation

			
			//PI App Save................................
			if($page_id==867){
				$page_id=867;
				$pi_id=$app_id;
				$approval_type=0;
				
				$user_sequence_no=return_field_value("sequence_no","electronic_approval_setup","company_id=$company_id and page_id=$page_id and user_id=$user_id_approval and is_deleted=0")*1;
				$min_sequence_no=return_field_value("min(sequence_no)","electronic_approval_setup","company_id=$company_id and page_id=$page_id and is_deleted=0")*1;
				
			
				if($approval_type==0)
				{
			
					$is_not_last_user=return_field_value("sequence_no","electronic_approval_setup","company_id=$company_id and page_id=$page_id and sequence_no>$user_sequence_no and bypass=2 and is_deleted=0");
					if($is_not_last_user!=""){$partial_approval=3;}
					else{$partial_approval=1;}
					
					$field_array="id, entry_form, mst_id, approved_no, sequence_no, current_approval_status, approved_by, approved_date, user_ip,comments,inserted_by,insert_date"; 
					$id=return_next_id("ID", "APPROVAL_HISTORY", "", "",1);
					
					$max_approved_no_arr = return_library_array("select mst_id, max(approved_no) as approved_no from approval_history where mst_id=$pi_id and entry_form=21 group by mst_id","mst_id","approved_no");
					
					$approved_status_arr = return_library_array("select id, approved from com_pi_master_details where id =$pi_id","id","approved");
			
					//return $approved_status_arr;
				
			
			
					//---------------
					$approved_no=$max_approved_no_arr[$pi_id];
					$approved_status=$approved_status_arr[$pi_id];
					
					if($approved_status==0)
					{
						$approved_no=$approved_no+1;
						$approved_no_array[$pi_id]=$approved_no;
					}
					
					$data_arr_save['ID']= $id;
					$data_arr_save['ENTRY_FORM']= 21;
					$data_arr_save['MST_ID']= $pi_id;
					$data_arr_save['APPROVED_NO']= $approved_no;
					$data_arr_save['SEQUENCE_NO']= $user_sequence_no;
					$data_arr_save['CURRENT_APPROVAL_STATUS']= 1;
					$data_arr_save['APPROVED_BY']= $user_id_approval;
					$data_arr_save['APPROVED_DATE']= $pc_date_time;
					$data_arr_save['USER_IP']= $user_ip;
					$data_arr_save['COMMENTS']= 1;
					$data_arr_save['INSERTED_BY']= $user_id_approval;
					$data_arr_save['INSERT_DATE']= $pc_date_time;
						
					
					
					if(count($approved_no_array)>0)
					{
						$approved_string="";
						
						foreach($approved_no_array as $key=>$value)
						{
							$approved_string.=" WHEN $key THEN $value";
						}
						
						$approved_string_mst="CASE id ".str_replace("'",'',$approved_string)." END";
						$approved_string_dtls="CASE id ".str_replace("'",'',$approved_string)." END";
						
						$sql_insert="insert into com_pi_master_details_history(id,approved_no, mst_id, item_category_id, importer_id, supplier_id, pi_number, pi_date, last_shipment_date, pi_validity_date, currency_id, source, hs_code, internal_file_no, intendor_name, pi_basis_id, remarks, total_amount, upcharge, discount, net_total_amount, approved, approved_by, approved_date, inserted_by, insert_date, updated_by, update_date, status_active, is_deleted) 
							select	
							'', $approved_string_mst, id, item_category_id, importer_id, supplier_id, pi_number, pi_date, last_shipment_date, pi_validity_date, currency_id, source, hs_code, internal_file_no, intendor_name, pi_basis_id, remarks, total_amount, upcharge, discount, net_total_amount, approved, approved_by, approved_date, inserted_by, insert_date, updated_by, update_date, status_active, is_deleted from com_pi_master_details where id=$pi_id";
								
							 
						
						$sql_insert_dtls="insert into com_pi_item_details_history(id, approved_no, dtls_id,pi_id, work_order_no, work_order_id, work_order_dtls_id, determination_id, item_prod_id, item_group, item_description, color_id, item_color, size_id, item_size, count_name, yarn_composition_item1, yarn_composition_percentage1, yarn_composition_item2, yarn_composition_percentage2, fabric_composition, fabric_construction, yarn_type, gsm, dia_width, weight, uom, quantity, rate, amount, net_pi_rate, net_pi_amount, service_type, brand_supplier, inserted_by, insert_date, updated_by, update_date, status_active, is_deleted) 
							select	
							'', $approved_string_dtls, id, pi_id, work_order_no, work_order_id,work_order_dtls_id, determination_id,  item_prod_id,item_group, item_description,  color_id,item_color, size_id, item_size, count_name, yarn_composition_item1, yarn_composition_percentage1,yarn_composition_item2,  yarn_composition_percentage2,  fabric_composition,fabric_construction, yarn_type,gsm,dia_width,weight,uom, quantity, rate, amount, net_pi_rate, net_pi_amount, service_type, brand_supplier, inserted_by, insert_date, updated_by, update_date, status_active, is_deleted from com_pi_item_details where pi_id=$pi_id";
								
							
					}
					
					//return count($approved_no_array);
					
					$data_arr_up['APPROVED']= 1;
					$rID=$this->updateData('COM_PI_MASTER_DETAILS', $data_arr_up, array('ID' => $pi_id));
					if($rID) $flag=1; else $flag=0;
					
					$rID2=$this->insertData("APPROVAL_HISTORY",$data_arr_save);
					
					
					if($flag==1) 
					{
						if($rID2) $flag=1; else $flag=0; 
					} 
					
					if(count($approved_no_array)>0)
					{
						$rID3=$this->db->query($sql_insert);
						if($flag==1) 
						{
							if($rID3) $flag=1; else $flag=0; 
						} 
			
						$rID4=$this->db->query($sql_insert_dtls);
						if($flag==1) 
						{
							if($rID4) $flag=1; else $flag=0; 
						} 
					}
					
	
					if($flag==1) $msg=1; else $msg=0;
				}
				return $msg;
			
			}//end if condition;
			//Price Quotation App Save................................
			else if($page_id==427){
				
				
				$quotation_no =$app_id;
				$page_id=427;
				$approval_type=0;
			
				$user_sequence_no=return_field_value("sequence_no","electronic_approval_setup","company_id=$company_id and page_id=$page_id and user_id=$user_id_approval and is_deleted=0");
				$user_bypass=return_field_value("bypass","electronic_approval_setup","company_id=$company_id and page_id=$page_id and user_id=$user_id_approval and is_deleted=0");
		
		
				if($approval_type==0)
				{
					if($user_bypass ==2)
					{
						$is_not_last_user=return_field_value("sequence_no","electronic_approval_setup","company_id=$company_id and page_id=$page_id and sequence_no>$user_sequence_no and bypass=2 and is_deleted=0");
					}
					else
					{
						$is_not_last_user=return_field_value("sequence_no","electronic_approval_setup","company_id=$company_id and page_id=$page_id and sequence_no>$user_sequence_no and is_deleted=0");
					}
					
	
					if($is_not_last_user != "")
					{
						// getting login in user's buyer id
						$loginUserBuyersArr = array();
						$loginUserBuyersSQL=sql_select("select (b.buyer_id || ',' ||  a.buyer_id) as buyer_id from user_passwd a, electronic_approval_setup b where a.id = b.user_id and b.company_id=$company_id and b.page_id=$page_id and b.user_id=$user_id_approval and b.is_deleted=0 group by b.buyer_id, a.buyer_id");
						foreach ($loginUserBuyersSQL as $key => $buyerID) {
							$loginUserBuyersArr[] = $buyerID[csf('buyer_id')];
						}
			
						$loginUserBuyersArr = implode(',',$loginUserBuyersArr);
						$loginUserBuyersArr = explode(',',$loginUserBuyersArr);
						$loginUserBuyersArr = array_filter($loginUserBuyersArr);
						$loginUserBuyersArr = array_unique($loginUserBuyersArr);
						// print_r($loginUserBuyersArr);die();
			
						// getting next level all user's buyer id
						$credentialUserBuyersArr = array();
						$sql = sql_select("select (b.buyer_id || ',' ||  a.buyer_id) as buyer_id from user_passwd a, electronic_approval_setup b where a.id = b.user_id and b.company_id=$company_id and b.page_id=$page_id and b.sequence_no>$user_sequence_no and b.is_deleted=0 group by b.buyer_id, a.buyer_id");
						foreach ($sql as $key => $buyerID) {
							$credentialUserBuyersArr[] = $buyerID[csf('buyer_id')];
						}
						$credentialUserBuyersArr = implode(',',$credentialUserBuyersArr);
						$credentialUserBuyersArr = explode(',',$credentialUserBuyersArr);
						$credentialUserBuyersArr = array_filter($credentialUserBuyersArr);
						$credentialUserBuyersArr = array_unique($credentialUserBuyersArr);
						// print_r($credentialUserBuyersArr);die();
			
						if(count($credentialUserBuyersArr)>0)
						{
							if(in_array($loginUserBuyersArr,$credentialUserBuyersArr))
							{
								$partial_approval=3;
							}
							else
							{
								$partial_approval=1;
							}
						}
						else
						{
							$partial_approval=3;
						}
						
					}
					else
					{
						$partial_approval=1;
					}
					//return $partial_approval;
					
					$id=return_next_id("ID", "APPROVAL_HISTORY", "", "",1);
			
					$max_approved_no_arr = return_library_array("select mst_id, max(approved_no) as approved_no from approval_history where mst_id=$quotation_no and entry_form=10 group by mst_id","mst_id","approved_no");
			
					$approved_status_arr = return_library_array("select id, approved from wo_price_quotation where id =$quotation_no","id","approved");
					
		
					$approved_no=$max_approved_no_arr[$booking_id];
					$approved_status=$approved_status_arr[$quotation_no];
		
					if($approved_status==0)
					{
						$approved_no=$approved_no+1;
						$approved_no_array[$quotation_no]=$approved_no;
					}
		
				 
					
					$data_arr_save['ID']= $id;
					$data_arr_save['ENTRY_FORM']= 10;
					$data_arr_save['MST_ID']= $quotation_no;
					$data_arr_save['APPROVED_NO']= $approved_no;
					$data_arr_save['SEQUENCE_NO']= $user_sequence_no;
					$data_arr_save['CURRENT_APPROVAL_STATUS']= 1;
					$data_arr_save['APPROVED_BY']= $user_id_approval;
					$data_arr_save['APPROVED_DATE']= $app_date;
					$data_arr_save['USER_IP']= $user_ip;
					$data_arr_save['COMMENTS']= '';
					$data_arr_save['INSERTED_BY']= $user_id_approval;
					$data_arr_save['INSERT_DATE']= $pc_date_time;
					
					
					
			
					if(count($approved_no_array)>0)
					{
						$approved_string="";
						foreach($approved_no_array as $key=>$value)
						{
							$approved_string.=" WHEN $key THEN $value";
						}
			
						$approved_string_mst="CASE id ".$approved_string." END";
						$approved_string_dtls="CASE quotation_id ".$approved_string." END";
			
						$sql_insert="insert into wo_price_quotation_his( id, approved_no, quotation_id, company_id, buyer_id, style_ref, revised_no, pord_dept, product_code, style_desc, currency, agent, offer_qnty, region, color_range, incoterm, incoterm_place, machine_line, prod_line_hr, fabric_source, costing_per, quot_date, est_ship_date, factory, remarks, garments_nature, order_uom, gmts_item_id, set_break_down, total_set_qnty, cm_cost_predefined_method_id, exchange_rate, sew_smv, cut_smv, sew_effi_percent, cut_effi_percent, efficiency_wastage_percent, season, approved, approved_by, approved_date, inserted_by, insert_date, updated_by, update_date, status_active, is_deleted, op_date, inquery_id, m_list_no, bh_marchant, ready_to_approved)
							select
							'', $approved_string_mst, id, company_id, buyer_id, style_ref, revised_no, pord_dept, product_code, style_desc, currency, agent, offer_qnty, region, color_range, incoterm, incoterm_place, machine_line, prod_line_hr, fabric_source, costing_per, quot_date, est_ship_date, factory, remarks, garments_nature, order_uom, gmts_item_id, set_break_down, total_set_qnty, cm_cost_predefined_method_id, exchange_rate, sew_smv, cut_smv, sew_effi_percent, cut_effi_percent, efficiency_wastage_percent, season, approved, approved_by, approved_date, inserted_by, insert_date, updated_by, update_date, status_active, is_deleted, op_date, inquery_id, m_list_no, bh_marchant, ready_to_approved
					from wo_price_quotation where id=$quotation_no";
						//return $sql_insert;die;
			
						$sql_insert2="insert into wo_price_quot_costing_mst_his(id, quot_mst_id, quotation_id, approved_no, costing_per_id, order_uom_id, fabric_cost, fabric_cost_percent, trims_cost, trims_cost_percent, embel_cost, embel_cost_percent, wash_cost, wash_cost_percent, comm_cost, comm_cost_percent, lab_test, lab_test_percent, inspection, inspection_percent, cm_cost, cm_cost_percent, freight, freight_percent, common_oh, common_oh_percent, total_cost, total_cost_percent, commission, commission_percent, final_cost_dzn, final_cost_dzn_percent, final_cost_pcs, final_cost_set_pcs_rate, a1st_quoted_price, a1st_quoted_price_percent, a1st_quoted_price_date, revised_price, revised_price_date, confirm_price, confirm_price_set_pcs_rate, confirm_price_dzn, confirm_price_dzn_percent, margin_dzn, margin_dzn_percent, price_with_commn_dzn, price_with_commn_percent_dzn, price_with_commn_pcs, price_with_commn_percent_pcs, confirm_date, asking_quoted_price, asking_quoted_price_percent, inserted_by, insert_date, updated_by, update_date, status_active, is_deleted, currier_pre_cost, currier_percent, certificate_pre_cost, certificate_percent, terget_qty, depr_amor_pre_cost, depr_amor_po_price, interest_pre_cost, interest_po_price, income_tax_pre_cost, income_tax_po_price)
							select
							'', id, quotation_id, $approved_string_dtls, costing_per_id, order_uom_id, fabric_cost, fabric_cost_percent, trims_cost, trims_cost_percent, embel_cost, embel_cost_percent, wash_cost, wash_cost_percent, comm_cost, comm_cost_percent, lab_test, lab_test_percent, inspection, inspection_percent, cm_cost, cm_cost_percent, freight, freight_percent, common_oh, common_oh_percent, total_cost, total_cost_percent, commission, commission_percent, final_cost_dzn, final_cost_dzn_percent, final_cost_pcs, final_cost_set_pcs_rate, a1st_quoted_price, a1st_quoted_price_percent, a1st_quoted_price_date, revised_price, revised_price_date, confirm_price, confirm_price_set_pcs_rate, confirm_price_dzn, confirm_price_dzn_percent, margin_dzn, margin_dzn_percent, price_with_commn_dzn, price_with_commn_percent_dzn, price_with_commn_pcs, price_with_commn_percent_pcs, confirm_date, asking_quoted_price, asking_quoted_price_percent, inserted_by, insert_date, updated_by, update_date, status_active, is_deleted, currier_pre_cost, currier_percent, certificate_pre_cost, certificate_percent, terget_qty, depr_amor_pre_cost, depr_amor_po_price, interest_pre_cost, interest_po_price, income_tax_pre_cost, income_tax_po_price from wo_price_quotation_costing_mst where quotation_id=$quotation_no";
						//echo $sql_insert2;die;
			
						$sql_insert3="insert into wo_price_quot_set_details_his(id, approved_no, quot_set_dlts_id, quotation_id, gmts_item_id, set_item_ratio)
							select
							'', $approved_string_dtls, id, quotation_id, gmts_item_id, set_item_ratio from wo_price_quotation_set_details where quotation_id=$quotation_no";
						//echo $sql_insert3;die;
			
						$sql_insert4="insert into wo_pri_quo_comm_cost_dtls_his(id, approved_no, quo_comm_dtls_id, quotation_id, item_id, base_id,  rate, amount, inserted_by, insert_date, updated_by, update_date, status_active, is_deleted)
							select
							'', $approved_string_dtls, id, quotation_id, item_id, base_id, rate, amount, inserted_by, insert_date, updated_by, update_date, status_active, is_deleted from wo_pri_quo_comarcial_cost_dtls where quotation_id=$quotation_no";
						//echo $sql_insert4;die;
			
						$sql_insert5="insert into wo_pri_quo_commiss_dtls_his(id, approved_no, quo_commiss_dtls_id, quotation_id, particulars_id, commission_base_id, commision_rate, commission_amount, inserted_by, insert_date, updated_by, update_date,  status_active, is_deleted)
							select
							'', $approved_string_dtls, id, quotation_id, particulars_id, commission_base_id, commision_rate, commission_amount, inserted_by, insert_date, updated_by,  update_date, status_active, is_deleted from wo_pri_quo_commiss_cost_dtls where quotation_id=$quotation_no";
						//echo $sql_insert5;die;
			
						$sql_insert6="insert into wo_pri_quo_embe_cost_dtls_his(id, approved_no, quo_emb_dtls_id, quotation_id, emb_name, emb_type, cons_dzn_gmts, rate, amount, charge_lib_id, inserted_by, insert_date, updated_by, update_date, status_active, is_deleted)
							select
							'', $approved_string_dtls, id, quotation_id, emb_name, emb_type, cons_dzn_gmts, rate, amount, charge_lib_id, inserted_by, insert_date, updated_by, update_date, status_active, is_deleted from wo_pri_quo_embe_cost_dtls where quotation_id=$quotation_no";
						//echo $sql_insert6;die;
			
						$sql_insert7="insert into wo_pri_quo_fab_cost_dtls_his(id, approved_no, quo_fab_dtls_id, quotation_id, item_number_id, body_part_id,  fab_nature_id, color_type_id, lib_yarn_count_deter_id, construction, composition, fabric_description, gsm_weight, avg_cons, fabric_source, rate, amount, avg_finish_cons, avg_process_loss, inserted_by, insert_date, updated_by, update_date, status_active, is_deleted, company_id, costing_per, fab_cons_in_quotat_varia, process_loss_method, yarn_breack_down, width_dia_type, cons_breack_down, msmnt_break_down, marker_break_down)
							select
							'', $approved_string_dtls, id, quotation_id, item_number_id, body_part_id, fab_nature_id, color_type_id, lib_yarn_count_deter_id, construction, composition, fabric_description, gsm_weight, avg_cons, fabric_source, rate, amount, avg_finish_cons, avg_process_loss, inserted_by, insert_date, updated_by, update_date, status_active, is_deleted, company_id, costing_per, fab_cons_in_quotat_varia, process_loss_method, yarn_breack_down, width_dia_type, cons_breack_down, msmnt_break_down, marker_break_down from wo_pri_quo_fabric_cost_dtls where quotation_id =$quotation_no";
						//echo $sql_insert7;die;
			
						$sql_insert8="insert into wo_pri_quo_fab_conv_dtls_his (id, approved_no, quo_fab_conv_dtls_id, quotation_id, cost_head, cons_type, req_qnty, charge_unit, amount, charge_lib_id, inserted_by, insert_date, updated_by, update_date, status_active, is_deleted, process_loss)
							select
							'', $approved_string_dtls, id, quotation_id, cost_head, cons_type, req_qnty, charge_unit, amount, charge_lib_id, inserted_by, insert_date, updated_by, update_date, status_active, is_deleted, process_loss from wo_pri_quo_fab_conv_cost_dtls where quotation_id=$quotation_no";
						//echo $sql_insert8;die;
			
						$sql_insert9="insert into wo_pri_quo_fab_co_avg_con_his (id, approved_no, quo_fab_avg_co_dtls_id, wo_pri_quo_fab_co_dtls_id, quotation_id, gmts_sizes, dia_width, cons, process_loss_percent, requirment, pcs, body_length, body_sewing_margin, body_hem_margin, sleeve_length, sleeve_sewing_margin, sleeve_hem_margin, half_chest_length, half_chest_sewing_margin, front_rise_length, front_rise_sewing_margin, west_band_length, west_band_sewing_margin, in_seam_length, in_seam_sewing_margin, in_seam_hem_margin, half_thai_length, half_thai_sewing_margin, total, marker_dia, marker_yds, marker_inch, gmts_pcs, marker_length, net_fab_cons)
							select
							'', $approved_string_dtls, id, wo_pri_quo_fab_co_dtls_id, quotation_id, gmts_sizes, dia_width, cons, process_loss_percent, requirment, pcs, body_length, body_sewing_margin, body_hem_margin, sleeve_length, sleeve_sewing_margin, sleeve_hem_margin, half_chest_length, half_chest_sewing_margin, front_rise_length, front_rise_sewing_margin, west_band_length, west_band_sewing_margin, in_seam_length, in_seam_sewing_margin, in_seam_hem_margin, half_thai_length, half_thai_sewing_margin, total, marker_dia, marker_yds, marker_inch, gmts_pcs, marker_length, net_fab_cons from wo_pri_quo_fab_co_avg_con_dtls where quotation_id=$quotation_no";
						//echo $sql_insert9;die;
			
						$sql_insert10="insert into wo_pri_quo_fab_yarn_dtls_his(id, approved_no, quo_yarn_dtls_id, quotation_id, count_id, copm_one_id, percent_one, copm_two_id, percent_two, type_id, cons_ratio, cons_qnty, rate, amount, inserted_by, insert_date, updated_by, update_date, status_active, is_deleted, supplier_id)
							select
							'', $approved_string_dtls, id, quotation_id, count_id, copm_one_id, percent_one, copm_two_id, percent_two, type_id, cons_ratio, cons_qnty, rate, amount, inserted_by, insert_date, updated_by, update_date, status_active, is_deleted, supplier_id from wo_pri_quo_fab_yarn_cost_dtls where quotation_id=$quotation_no";
						//echo $sql_insert10;die;
			
						$sql_insert11="insert into wo_pri_quo_sum_dtls_his( id, approved_no, quo_sum_dtls_id, quotation_id, fab_yarn_req_kg, fab_woven_req_yds, fab_knit_req_kg, fab_amount, yarn_cons_qnty, yarn_amount, conv_req_qnty, conv_charge_unit, conv_amount, trim_cons, trim_rate, trim_amount, emb_amount, wash_amount, comar_rate, comar_amount, commis_rate, commis_amount, inserted_by, insert_date, updated_by, update_date, status_active, is_deleted)
							select
							'', $approved_string_dtls, id, quotation_id, fab_yarn_req_kg, fab_woven_req_yds, fab_knit_req_kg, fab_amount, yarn_cons_qnty, yarn_amount, conv_req_qnty, conv_charge_unit, conv_amount, trim_cons, trim_rate, trim_amount, emb_amount, wash_amount, comar_rate, comar_amount, commis_rate, commis_amount, inserted_by, insert_date, updated_by, update_date, status_active, is_deleted from wo_pri_quo_sum_dtls where quotation_id=$quotation_no";
			
						//echo $sql_insert11;die;
			
						$sql_insert12="insert into wo_pri_quo_trim_cost_dtls_his( id, approved_no, quo_trim_dtls_id, quotation_id, trim_group, cons_uom, cons_dzn_gmts, rate, amount, apvl_req, nominated_supp, inserted_by, insert_date, updated_by, update_date, status_active, is_deleted)
							select
							'', $approved_string_dtls, id, quotation_id, trim_group, cons_uom, cons_dzn_gmts, rate, amount, apvl_req, nominated_supp, inserted_by, insert_date, updated_by, update_date, status_active, is_deleted from wo_pri_quo_trim_cost_dtls where quotation_id=$quotation_no";
						//echo $sql_insert12;die;
					}
			
					
					if($partial_approval == 1)
					{
						$data_arr_up=array();
						$data_arr_up[APPROVED_BY]= $user_id_approval;
						$data_arr_up[APPROVED_DATE]= $app_date;
						$rID=$this->updateData('WO_PRICE_QUOTATION', $data_arr_up, array('ID' => $quotation_no));
					}
					else{
						$data_arr_up=array();
						$data_arr_up[APPROVED]= $partial_approval;
						$rID=$this->updateData('WO_PRICE_QUOTATION', $data_arr_up, array('ID' => $quotation_no));
					}
					if($rID) $flag=1; else $flag=0;
					
					
			
					$query="UPDATE approval_history SET current_approval_status=0 WHERE entry_form=10 and mst_id in ($quotation_no)";
					$rIDapp=$this->db->query($query);
					if($flag==1)
					{
						if($rIDapp) $flag=1; else $flag=0;
					}
			
					
					$rID2=$this->insertData("APPROVAL_HISTORY",$data_arr_save);
					if($flag==1)
					{
						if($rID2) $flag=1; else $flag=0;
					}
			
					
			
					if(count($approved_no_array)>0)
					{
						$rID3=$this->db->query($sql_insert);
						if($flag==1)
						{
							if($rID3) $flag=1; else $flag=0;
						}
			
						$rID4=$this->db->query($sql_insert2);
						if($flag==1)
						{
							if($rID4) $flag=1; else $flag=0;
						}
			
						$rID5=$this->db->query($sql_insert3);
						if($flag==1)
						{
							if($rID5) $flag=1; else $flag=0;
						}
			
						$rID6=$this->db->query($sql_insert4);
						if($flag==1)
						{
							if($rID6) $flag=1; else $flag=0;
						}
			
						$rID7=$this->db->query($sql_insert5);
						if($flag==1)
						{
							if($rID7) $flag=1; else $flag=0;
						}
			
						$rID8=$this->db->query($sql_insert6);
						if($flag==1)
						{
							if($rID8) $flag=1; else $flag=0;
						}
			
						$rID9=$this->db->query($sql_insert7);
						if($flag==1)
						{
							if($rID9) $flag=1; else $flag=0;
						}
			
						$rID10=$this->db->query($sql_insert8);
						if($flag==1)
						{
							if($rID10) $flag=1; else $flag=0;
						}
			
						$rID11=$this->db->query($sql_insert9);
						if($flag==1)
						{
							if($rID11) $flag=1; else $flag=0;
						}
			
						$rID12=$this->db->query($sql_insert10);
						if($flag==1)
						{
							if($rID12) $flag=1; else $flag=0;
						}
			
						$rID13=$this->db->query($sql_insert11);
						if($flag==1)
						{
							if($rID13) $flag=1; else $flag=0;
						}
			
						$rID14=$this->db->query($sql_insert12);
						if($flag==1)
						{
							if($rID14) $flag=1; else $flag=0;
						}
					}
					
				}
		
				if($flag==1) $msg=1; else $msg=0;
				return $msg;
	
			
			}//end else if condition;
			//Pre Cost App Save................................
			else if($page_id==428){
							
				 $page_id=428;
				 $booking_nos=$app_id;
				 $approval_type=2;
				 
				 $booking_ids=return_field_value("id","wo_pre_cost_mst","job_no='$app_id' and status_active=1 and is_deleted=0");

				 //return $booking_ids;
				 
			
				$user_sequence_no=return_field_value("sequence_no","electronic_approval_setup","company_id=$company_id and page_id=$page_id and user_id=$user_id_approval and is_deleted=0");
				$min_sequence_no=return_field_value("min(sequence_no)","electronic_approval_setup","company_id=$company_id and page_id=$page_id and is_deleted=0");
			
				$buyer_arr=return_library_array( "select b.id, a.buyer_name   from wo_pre_cost_mst b,  wo_po_details_master a   where  a.job_no=b.job_no and a.is_deleted=0 and  a.status_active=1 and b.status_active=1 and  b.is_deleted=0 and b.id in ($booking_ids)", "id", "buyer_name"  );
				
				
				if($approval_type==2)
				{
					if($this->db->dbdriver == 'mysqli') {$buyer_id_cond=" and a.buyer_id is null "; $buyer_id_cond2=" and b.buyer_id is not null ";}
					else{$buyer_id_cond=" and a.buyer_id='' "; $buyer_id_cond2=" and b.buyer_id!='' ";}
			
			
			
			//echo "22**";
					$is_not_last_user=return_field_value("a.sequence_no","electronic_approval_setup a"," a.company_id=$company_id and a.page_id=$page_id and a.sequence_no>$user_sequence_no and a.bypass=2 and a.is_deleted=0 $buyer_id_cond","sequence_no");
			
		
			
					$partial_approval = "";
					if($is_not_last_user == "")
					{
						//$credentialUserBuyersArr = [];
						$sql = sql_select("select (b.buyer_id) as BUYER_ID from electronic_approval_setup b where b.company_id=$company_id and b.page_id=$page_id and b.sequence_no>$user_sequence_no and b.is_deleted=0 and b.bypass=2  group by b.buyer_id");
						foreach ($sql as $key => $buyerID) {
							$credentialUserBuyersArr[$buyerID->BUYER_ID] = $buyerID->BUYER_ID;
						}
			
						$credentialUserBuyersArr = implode(',',$credentialUserBuyersArr);
					}
					else
					{
			
						$check_user_buyer = sql_select("select b.user_id as BUYER_ID from user_passwd a, electronic_approval_setup b where a.id = b.user_id and b.company_id=$company_id and b.page_id=$page_id and b.sequence_no>$user_sequence_no and b.is_deleted=0 and b.bypass=2 $buyer_id_cond  group by b.user_id");
						//echo "21**".count($check_user_buyer);die;
						if(count($check_user_buyer)==0)
						{
			
							$sql = sql_select("select b.buyer_id as BUYER_ID from user_passwd b, electronic_approval_setup a where b.id = a.user_id and a.company_id=$company_id and a.page_id=$page_id and a.sequence_no>$user_sequence_no and a.is_deleted=0 and a.bypass=2 $buyer_id_cond $buyer_id_cond2 group by b.buyer_id");
							foreach ($sql as $key => $buyerID) {
								$credentialUserBuyersArr[$buyerID->BUYER_ID] = $buyerID->BUYER_ID;
							}
			
							$sql = sql_select("select (b.buyer_id) as BUYER_ID from electronic_approval_setup b where b.company_id=$company_id and b.page_id=$page_id and b.sequence_no>$user_sequence_no and b.is_deleted=0 and b.bypass=2 $buyer_id_cond2  group by b.buyer_id");
							foreach ($sql as $key => $buyerID) {
								$credentialUserBuyersArr[$buyerID->BUYER_ID] = $buyerID->BUYER_ID;
							}
			
							$credentialUserBuyersArr = implode(',',$credentialUserBuyersArr);
						}
						//print_r($credentialUserBuyersArr);die;
					}
					// if($is_not_last_user!="") $partial_approval=3; else $partial_approval=1;
					
			
					$response=$booking_ids;
					//$field_array="id, entry_form, mst_id, approved_no, sequence_no, current_approval_status, approved_by, approved_date,inserted_by,insert_date";
					$id=return_next_id( "id","approval_history", 1 ) ;
					
			
					$max_approved_no_arr = return_library_array("select mst_id, max(approved_no) as approved_no from approval_history where mst_id in($booking_ids) and entry_form=15 group by mst_id","mst_id","approved_no");
			
					$approved_status_arr = return_library_array("select id, approved from wo_pre_cost_mst where id in($booking_ids)","id","approved");
					$approved_no_array=array();
					$booking_ids_all=explode(",",$booking_ids);
					$booking_nos_all=explode(",",$booking_nos);
					$book_nos='';
					//print_r($credentialUserBuyersArr);die;
					for($i=0;$i<count($booking_nos_all);$i++)
					{
						$val=$booking_nos_all[$i];
						$booking_id=$booking_ids_all[$i];
			
						$approved_no=$max_approved_no_arr[$booking_id];
						$approved_status=$approved_status_arr[$booking_id];
						$buyer_id=$buyer_arr[$booking_id];
			
			
						if($approved_status==2)
						{
							$approved_no=$approved_no+1;
							$approved_no_array[$val]=$approved_no;
							if($book_nos=="") $book_nos=$val; else $book_nos.=",".$val;
						}
			
						if($is_not_last_user == "")
						{
							if(in_array($buyer_id,$credentialUserBuyersArr))
							{
								$partial_approval=3;
							}
							else
							{
								$partial_approval=1;
							}
						}
						else
						{
							if(count($credentialUserBuyersArr)>0)
							{
								if(in_array($buyer_id,$credentialUserBuyersArr))
								{
									$partial_approval=3;
								}
								else
								{
									$partial_approval=1;
								}
							}
							else
							{
								$partial_approval=3;
							}
							//$partial_approval=3;
						}
						//echo $partial_approval;die;
						$booking_id_arr[]=$booking_id;
						$data_array_booking_update[$booking_id]=explode("*",($partial_approval));
			
						if($partial_approval==1)
						{
							//$full_approve_booking_id_arr[]=$booking_id;
							//$data_array_full_approve_booking_update[$booking_id]=explode("*",($user_id_approval."*'".$pc_date_time."'"));
						}
			
			
						//if($data_array!="") $data_array.=",";
						//$data_array.="(".$id.",15,".$booking_id.",".$approved_no.",'".$user_sequence_no."',1,".$user_id_approval.",'".$pc_date_time."',".$user_id.",'".$pc_date_time."')";
						//$id=$id+1;
						
						
					
						$data_arr_save['ID']= $id;
						$data_arr_save['ENTRY_FORM']= 15;
						$data_arr_save['MST_ID']= $booking_id;
						$data_arr_save['APPROVED_NO']= $approved_no;
						$data_arr_save['SEQUENCE_NO']= $user_sequence_no;
						$data_arr_save['CURRENT_APPROVAL_STATUS']= 1;
						$data_arr_save['APPROVED_BY']= $user_id_approval;
						$data_arr_save['APPROVED_DATE']= $app_date;
						$data_arr_save['INSERTED_BY']= $user_id_approval;
						$data_arr_save['INSERT_DATE']= $pc_date_time;
						
						
					}
					
					
					
					
			
					$flag=1;
					if(count($approved_no_array)>0)
					{
			
						$approved_string="";
			
						if($this->db->dbdriver == 'mysqli')
						{
							foreach($approved_no_array as $key=>$value)
							{
								$approved_string.=" WHEN '$key' THEN $value";
							}
						}
						else
						{
							foreach($approved_no_array as $key=>$value)
							{
								$approved_string.=" WHEN TO_NCHAR('$key') THEN '".$value."'";
							}
						}
			
						// return $approved_string;
						
						
						$approved_string_mst="CASE job_no ".$approved_string." END";
						$approved_string_dtls="CASE job_no ".$approved_string." END";
						$sql_insert="insert into wo_pre_cost_mst_histry(id, approved_no, pre_cost_mst_id, garments_nature, job_no, costing_date, incoterm, incoterm_place,
						machine_line, prod_line_hr, costing_per, remarks, copy_quatation, cm_cost_predefined_method_id, exchange_rate, sew_smv, cut_smv, sew_effi_percent,
						cut_effi_percent, efficiency_wastage_percent, approved, approved_by, approved_date, inserted_by, insert_date, updated_by, update_date, status_active,
						is_deleted)
								select
								'', $approved_string_mst, id, garments_nature, job_no, costing_date, incoterm, incoterm_place, machine_line, prod_line_hr, costing_per,
						remarks, copy_quatation, cm_cost_predefined_method_id, exchange_rate, sew_smv, cut_smv, sew_effi_percent, cut_effi_percent,
						efficiency_wastage_percent, approved, approved_by, approved_date, inserted_by, insert_date, updated_by, update_date, status_active, is_deleted
						from wo_pre_cost_mst where job_no in ('$book_nos')";
						//return $sql_insert;die;
			
			
						$sql_precost_dtls="insert into wo_pre_cost_dtls_histry(id,approved_no,pre_cost_dtls_id,job_no,costing_per_id,order_uom_id,fabric_cost,  fabric_cost_percent,trims_cost,trims_cost_percent,embel_cost,embel_cost_percent,wash_cost,wash_cost_percent,comm_cost,comm_cost_percent,commission,
					commission_percent,	lab_test,lab_test_percent,inspection, inspection_percent,cm_cost,cm_cost_percent,freight,freight_percent,currier_pre_cost,
					currier_percent, certificate_pre_cost,certificate_percent,common_oh,common_oh_percent,total_cost,total_cost_percent,price_dzn,price_dzn_percent,
					margin_dzn,margin_dzn_percent,cost_pcs_set,cost_pcs_set_percent,price_pcs_or_set,price_pcs_or_set_percent,margin_pcs_set,margin_pcs_set_percent,
					cm_for_sipment_sche,margin_pcs_bom, margin_bom_per,inserted_by,insert_date,updated_by,update_date,status_active,is_deleted)
								select
								'', $approved_string_dtls, id,job_no,costing_per_id,order_uom_id,fabric_cost,  fabric_cost_percent,trims_cost,trims_cost_percent,embel_cost,embel_cost_percent,wash_cost,wash_cost_percent,comm_cost,comm_cost_percent,commission,
					commission_percent,	lab_test,lab_test_percent,inspection, inspection_percent,cm_cost,cm_cost_percent,freight,freight_percent,currier_pre_cost,
					currier_percent, certificate_pre_cost,certificate_percent,common_oh,common_oh_percent,total_cost,total_cost_percent,price_dzn,price_dzn_percent,
					margin_dzn,margin_dzn_percent,cost_pcs_set,cost_pcs_set_percent,price_pcs_or_set,price_pcs_or_set_percent,margin_pcs_set,margin_pcs_set_percent,
					cm_for_sipment_sche,margin_pcs_bom, margin_bom_per,inserted_by,insert_date,updated_by,update_date,status_active,is_deleted from wo_pre_cost_dtls  where  job_no in ('$book_nos')";
						//echo $sql_precost_dtls;die;
			
			
						//--------------------------------------wo_pre_cost_fabric_cost_dtls_h---------------------------------------------------------------------------
						$sql_precost_fabric_cost_dtls="insert into wo_pre_cost_fabric_cost_dtls_h(id,approved_no,pre_cost_fabric_cost_dtls_id, job_no, item_number_id, body_part_id, fab_nature_id, color_type_id,lib_yarn_count_deter_id,construction,composition, fabric_description, gsm_weight,color_size_sensitive,	color, avg_cons, fabric_source, rate, amount,avg_finish_cons,avg_process_loss, inserted_by, insert_date,updated_by,update_date, status_active, is_deleted, company_id, costing_per,consumption_basis,process_loss_method,cons_breack_down,msmnt_break_down,color_break_down,yarn_breack_down,marker_break_down,width_dia_type)
							select
							'', $approved_string_dtls, id,job_no, item_number_id, body_part_id, fab_nature_id, color_type_id,lib_yarn_count_deter_id,construction,composition, fabric_description, gsm_weight,color_size_sensitive,	color, avg_cons, fabric_source, rate,amount,avg_finish_cons,avg_process_loss, inserted_by, insert_date,updated_by,update_date, status_active, is_deleted, company_id, costing_per,consumption_basis,process_loss_method,cons_breack_down,msmnt_break_down,color_break_down,yarn_breack_down,marker_break_down,width_dia_type from wo_pre_cost_fabric_cost_dtls where  job_no in ('$book_nos')";
						//echo $sql_precost_fabric_cost_dtls;die;
			
						//--------------------------------------wo_pre_cost_fab_yarn_cst_dtl_h---------------------------------------------------------------------------
						$sql_precost_fab_yarn_cst="insert into wo_pre_cost_fab_yarn_cst_dtl_h(id,approved_no,pre_cost_fab_yarn_cost_dtls_id,fabric_cost_dtls_id,job_no,count_id,copm_one_id,percent_one,copm_two_id,percent_two,type_id,cons_ratio,cons_qnty,rate,amount,inserted_by,insert_date,updated_by,update_date,status_active,is_deleted)
							select
							'', $approved_string_dtls, id,fabric_cost_dtls_id,job_no,count_id,copm_one_id,percent_one,copm_two_id,percent_two,type_id,cons_ratio,cons_qnty,rate,amount,
					inserted_by,insert_date,updated_by,update_date,status_active,is_deleted from wo_pre_cost_fab_yarn_cost_dtls  where  job_no in ('$book_nos')";
							//echo $sql_precost_fab_yarn_cst;die;
			
						//----------------------------------wo_pre_cost_comarc_cost_dtls_h----------------------------------------
						$sql_precost_fcomarc_cost_dtls="insert into wo_pre_cost_comarc_cost_dtls_h(id,approved_no,pre_cost_comarci_cost_dtls_id,job_no,item_id,rate,amount,inserted_by,insert_date,updated_by,update_date,status_active,is_deleted)
							select
							'', $approved_string_dtls,id,job_no,item_id,rate,amount,inserted_by,insert_date,updated_by,update_date,status_active,
					is_deleted from wo_pre_cost_comarci_cost_dtls where  job_no in ('$book_nos')";
							//echo $sql_precost_fcomarc_cost_dtls;die;
			
			
						//-------------------------------------pre_cost_commis_cost_dtls_h-------------------------------------------
						$sql_precost_commis_cost_dtls="insert into wo_pre_cost_commis_cost_dtls_h(id,approved_no,pre_cost_commiss_cost_dtls_id,job_no,particulars_id,
						commission_base_id,commision_rate,commission_amount,inserted_by,insert_date,updated_by,update_date,status_active,is_deleted)
							select
							'', $approved_string_dtls, id,job_no,particulars_id,commission_base_id,commision_rate,commission_amount,inserted_by,insert_date,updated_by,update_date,status_active,is_deleted from wo_pre_cost_commiss_cost_dtls where  job_no in ('$book_nos')";
						//	echo $sql_precost_commis_cost_dtls;die;
			
						//--------------------------------------   wo_pre_cost_embe_cost_dtls_his---------------------------------------------------------------------------
						$sql_precost_embe_cost_dtls="insert into  wo_pre_cost_embe_cost_dtls_his(id,approved_no,pre_cost_embe_cost_dtls_id,job_no,emb_name,
						emb_type,cons_dzn_gmts,rate,amount,charge_lib_id,inserted_by,insert_date,updated_by,update_date,status_active,is_deleted)
							select
							'', $approved_string_dtls, id,job_no,emb_name,
					emb_type,cons_dzn_gmts,rate,amount,charge_lib_id,inserted_by,insert_date,updated_by,update_date,status_active,is_deleted from wo_pre_cost_embe_cost_dtls  where  job_no in ('$book_nos')";
							//echo $sql_precost_commis_cost_dtls;die;
			
						//---------------------------------wo_pre_cost_fab_yarnbkdown_his------------------------------------------------
			
						$sql_precost_fab_yarnbkdown_his="insert into  wo_pre_cost_fab_yarnbkdown_his(id,approved_no,pre_cost_fab_yarnbreakdown_id,fabric_cost_dtls_id,job_no,count_id,copm_one_id,percent_one,copm_two_id,percent_two,type_id,cons_ratio,cons_qnty,rate,amount,inserted_by,insert_date,updated_by,update_date,status_active,is_deleted)
							select
							'', $approved_string_dtls, id,fabric_cost_dtls_id,job_no,count_id,copm_one_id,percent_one,copm_two_id,percent_two,type_id,cons_ratio,cons_qnty,rate,amount,inserted_by,insert_date,updated_by,update_date,status_active,is_deleted from wo_pre_cost_fab_yarnbreakdown  where  job_no in ('$book_nos')";
							//echo $sql_precost_fab_yarnbkdown_his;die;
			
						//------------------------------wo_pre_cost_sum_dtls_histroy-----------------------------------------------
			
						$sql_precost_fab_sum_dtls="insert into  wo_pre_cost_sum_dtls_histroy(id,approved_no,pre_cost_sum_dtls_id,job_no,fab_yarn_req_kg,fab_woven_req_yds,fab_knit_req_kg,fab_woven_fin_req_yds,fab_knit_fin_req_kg,fab_amount,avg,yarn_cons_qnty,yarn_amount,conv_req_qnty,conv_charge_unit,conv_amount,trim_cons,trim_rate,trim_amount,emb_amount,comar_rate,
						comar_amount,commis_rate,commis_amount,inserted_by,insert_date,updated_by,update_date,status_active,is_deleted)
							select
							'', $approved_string_dtls, id,job_no,fab_yarn_req_kg,fab_woven_req_yds,fab_knit_req_kg,fab_woven_fin_req_yds,fab_knit_fin_req_kg,fab_amount,avg,yarn_cons_qnty,yarn_amount,conv_req_qnty,conv_charge_unit,conv_amount,trim_cons,trim_rate,trim_amount,emb_amount,comar_rate,
						comar_amount,commis_rate,commis_amount,inserted_by,insert_date,updated_by,update_date,status_active,is_deleted from wo_pre_cost_sum_dtls  where  job_no in ('$book_nos')";
							//echo $sql_precost_fab_sum_dtls;die;
							//----------------------------------------------------wo_pre_cost_trim_cost_dtls_his------------------------------	------------------------------------------
			
						$sql_precost_trim_cost_dtls="insert into  wo_pre_cost_trim_cost_dtls_his(id,approved_no,pre_cost_trim_cost_dtls_id,job_no, trim_group,description,brand_sup_ref, cons_uom, cons_dzn_gmts, rate, amount, apvl_req, nominated_supp,cons_breack_down,inserted_by, insert_date,updated_by,update_date, status_active,is_deleted)
							select
							'', $approved_string_dtls, id,job_no, trim_group,description,brand_sup_ref, cons_uom, cons_dzn_gmts, rate, amount, apvl_req, nominated_supp,cons_breack_down,inserted_by, insert_date,updated_by,update_date, status_active,is_deleted from wo_pre_cost_trim_cost_dtls  where  job_no in ('$book_nos')";
							//echo $sql_precost_trim_cost_dtls;die;
			
			
						//---------------------------wo_pre_cost_trim_co_cons_dtl_h--------------------------------------------------
			
						$sql_precost_trim_co_cons_dtl="insert into   wo_pre_cost_trim_co_cons_dtl_h(id,approved_no,pre_cost_trim_co_cons_dtls_id,wo_pre_cost_trim_cost_dtls_id,job_no, po_break_down_id,item_size, cons, place, pcs,country_id)
							select
							'', $approved_string_dtls, id,wo_pre_cost_trim_cost_dtls_id,job_no,po_break_down_id,item_size, cons,place, pcs,country_id from wo_pre_cost_trim_co_cons_dtls  where  job_no in ('$book_nos')";
						//-----------------------------------------wo_pre_cost_fab_con_cst_dtls_h-----------------------------------------
			
						$sql_precost_fab_con_cst_dtls="insert into   wo_pre_cost_fab_con_cst_dtls_h(id,approved_no,pre_cost_fab_conv_cst_dtls_id,job_no, fabric_description,cons_process,req_qnty,charge_unit,amount,color_break_down,charge_lib_id,inserted_by,insert_date,updated_by,update_date, status_active,is_deleted)
							select
							'', $approved_string_dtls, id,job_no, fabric_description,cons_process,req_qnty,charge_unit,amount,color_break_down,charge_lib_id,inserted_by,insert_date,updated_by,update_date, status_active,is_deleted from wo_pre_cost_fab_conv_cost_dtls  where  job_no in ('$book_nos')";
			
			
						if(count($sql_precost_trim_cost_dtls)>0)
						{
							//$rID12=execute_query($sql_precost_trim_cost_dtls,1);
							$rID12=$this->db->query($sql_precost_trim_cost_dtls);
							if($flag==1)
							{
								if($rID12) $flag=1; else $flag=0;
							}
						}
			
			
						if(count($sql_precost_trim_cost_dtls)>0)
						{
							//$rID13=execute_query($sql_precost_trim_co_cons_dtl,1);
							$rID13=$this->db->query($sql_precost_trim_co_cons_dtl);
							if($flag==1)
							{
								if($rID13) $flag=1; else $flag=0;
							}
						}
			
								 
						//$rID13=execute_query($sql_precost_fab_con_cst_dtls,1);
						$rID13=$this->db->query($sql_precost_fab_con_cst_dtls);
						if($flag==1)
						{
							if($rID13) $flag=1; else $flag=0;
						}
			
						if(count($sql_insert)>0)
						{
							//$rID3=execute_query($sql_insert,0);
							$rID3=$this->db->query($sql_insert);
							if($flag==1)
							{
								if($rID3) $flag=1; else $flag=0;
							}
						}
						//echo '895='.$flag; die;
						if(count($sql_precost_dtls)>0)
						{
							//$rID4=execute_query($sql_precost_dtls,1);
							$rID4=$this->db->query($sql_precost_dtls);
							if($flag==1)
							{
								if($rID4) $flag=1; else $flag=0;
							}
						}
			
						if(count($sql_precost_fabric_cost_dtls)>0)
						{
							//$rID5=execute_query($sql_precost_fabric_cost_dtls,1);
							$rID5=$this->db->query($sql_precost_fabric_cost_dtls);
							if($flag==1)
							{
								if($rID5) $flag=1; else $flag=0;
							}
						}
			
						if(count($sql_precost_fab_yarn_cst)>0)
						{
							//$rID6=execute_query($sql_precost_fab_yarn_cst,1);
							$rID6=$this->db->query($sql_precost_fab_yarn_cst);
							if($flag==1)
							{
								if($rID6) $flag=1; else $flag=0;
							}
						}
			
						if(count($sql_precost_fcomarc_cost_dtls)>0)
						{
							//$rID7=execute_query($sql_precost_fcomarc_cost_dtls,1);
							$rID7=$this->db->query($sql_precost_fcomarc_cost_dtls);
							if($flag==1)
							{
								if($rID7) $flag=1; else $flag=0;
							}
						}
						if(count($sql_precost_commis_cost_dtls)>0)
						{
							//$rID8=execute_query($sql_precost_commis_cost_dtls,1);
							$rID8=$this->db->query($sql_precost_commis_cost_dtls);
							if($flag==1)
							{
								if($rID8) $flag=1; else $flag=0;
							}
						}
						if(count($sql_precost_embe_cost_dtls)>0)
						{
							//$rID9=execute_query($sql_precost_embe_cost_dtls,1);
							$rID9=$this->db->query($sql_precost_embe_cost_dtls);
							if($flag==1)
							{
								if($rID9) $flag=1; else $flag=0;
							}
						}
			
						if(count($sql_precost_fab_yarnbkdown_his)>0)
						{
							//$rID10=execute_query($sql_precost_fab_yarnbkdown_his,1);
							$rID10=$this->db->query($sql_precost_fab_yarnbkdown_his);
							if($flag==1)
							{
								if($rID10) $flag=1; else $flag=0;
							}
						}
			
						if(count($sql_precost_fab_sum_dtls)>0)
						{
							//$rID11=execute_query($sql_precost_fab_sum_dtls,1);
							$rID11=$this->db->query($sql_precost_fab_sum_dtls);
							if($flag==1)
							{
								if($rID11) $flag=1; else $flag=0;
							}
						}
					}
			
					$rID9=1;
					if(count($full_approve_booking_id_arr)>0)
					{
			
						//$field_array_full_approved_booking_update = "approved_by*approved_date";
						//$rID9=execute_query(bulk_update_sql_statement( "wo_pre_cost_mst", "id", $field_array_full_approved_booking_update, $data_array_full_approve_booking_update, $full_approve_booking_id_arr));
						
						$data_arr_up=array();
						$data_arr_up[APPROVED_BY]= $user_id_approval;
						$data_arr_up[APPROVED_DATE]= $app_date;
						$rID9=$this->updateData('WO_PRE_COST_MST', $data_arr_up, array('ID' => $app_id));
						
						
						if($flag==1)
						{
							if($rID9) $flag=1; else $flag=0;
						}
						
					}
			
					//$field_array_booking_update = "approved";
					//$rID=execute_query(bulk_update_sql_statement( "wo_pre_cost_mst", "id", $field_array_booking_update, $data_array_booking_update, $booking_id_arr));
					
					$data_arr_up=array();
					$data_arr_up[APPROVED]= $partial_approval;
					$rID=$this->updateData('WO_PRE_COST_MST', $data_arr_up, array('ID' => $booking_ids));
					
					
			
					if($flag==1)
					{
						if($rID) $flag=1; else $flag=0;
					}
					//echo $flag; die;
			
					$query="UPDATE approval_history SET current_approval_status=0 WHERE entry_form=15 and mst_id in ($booking_ids)";
					$rIDapp=$this->db->query($query);
					if($flag==1)
					{
						if($rIDapp) $flag=1; else $flag=0;
					}
			
					//$rID2=sql_insert("approval_history",$field_array,$data_array,0);
					$rID2=$this->insertData("APPROVAL_HISTORY",$data_arr_save);
					if($flag==1)
					{
						if($rID2) $flag=1; else $flag=0;
					}
			
			
					if($flag==1) $msg=1; else $msg=0;
				}

				
				return $msg;
			
			
				
			}//end else if condition;
			//Fabric booking app................
			else if($page_id==410){
			
				$user_id=$user_id_approval;
				$page_id=410;
				$booking_nos=$app_id;
				$approval_type=0;
 				$booking_ids=return_field_value("id","wo_booking_mst","BOOKING_NO='$app_id' and is_deleted=0");

				//echo "0**";
				$user_sequence_no=return_field_value("sequence_no","electronic_approval_setup","company_id=$company_id and page_id=$page_id and user_id=$user_id_approval and is_deleted=0");
				$min_sequence_no=return_field_value("min(sequence_no)","electronic_approval_setup","company_id=$company_id and page_id=$page_id and is_deleted=0");
			
				if($approval_type==0)
				{

					$buyer_arr=return_library_array( "select id, buyer_id  from wo_booking_mst where id in ($booking_ids)", "id", "buyer_id"  );
			
					if($this->db->dbdriver == 'mysqli'){$buyer_id_cond=" and a.buyer_id='' "; $buyer_id_cond2=" and b.buyer_id!='' ";}
					else {$buyer_id_cond=" and a.buyer_id is null "; $buyer_id_cond2=" and b.buyer_id is not null ";}
			
					$is_not_last_user=return_field_value("a.sequence_no as sequence_no","electronic_approval_setup a"," a.company_id=$company_id and a.page_id=$page_id and a.sequence_no>$user_sequence_no and a.bypass=2 and a.is_deleted=0 $buyer_id_cond","sequence_no");
			
					$partial_approval = "";
					if($is_not_last_user == "")
					{
						//$credentialUserBuyersArr = [];
						$sql = sql_select("select (b.buyer_id) as BUYER_ID from electronic_approval_setup b where b.company_id=$company_id and b.page_id=$page_id and b.sequence_no>$user_sequence_no and b.is_deleted=0 and b.bypass=2  group by b.buyer_id");
						foreach ($sql as $key => $buyerID) {
							$credentialUserBuyersArr[$buyerID->BUYER_ID] = $buyerID->BUYER_ID;
						}
						$credentialUserBuyersArr = implode(',',$credentialUserBuyersArr);
;
					}
					else
					{
						$check_user_buyer = sql_select("select b.user_id as BUYER_ID from user_passwd a, electronic_approval_setup b where a.id = b.user_id and b.company_id=$company_id and b.page_id=$page_id and b.sequence_no>$user_sequence_no and b.is_deleted=0 and b.bypass=2 $buyer_id_cond  group by b.user_id");
						//echo "21**".count($check_user_buyer);die;
						if(count($check_user_buyer)==0)
						{
			
							$sql = sql_select("select b.buyer_id as BUYER_ID from user_passwd b, electronic_approval_setup a where b.id = a.user_id and a.company_id=$company_id and a.page_id=$page_id and a.sequence_no>$user_sequence_no and a.is_deleted=0 and a.bypass=2 $buyer_id_cond $buyer_id_cond2 group by b.buyer_id");
							foreach ($sql as $key => $buyerID) {
								$credentialUserBuyersArr[$buyerID->BUYER_ID] = $buyerID->BUYER_ID;
							}
			
							$sql = sql_select("select (b.buyer_id) as BUYER_ID from electronic_approval_setup b where b.company_id=$company_id and b.page_id=$page_id and b.sequence_no>$user_sequence_no and b.is_deleted=0 and b.bypass=2 $buyer_id_cond2  group by b.buyer_id");
							foreach ($sql as $key => $buyerID) {
								$credentialUserBuyersArr[$buyerID->BUYER_ID] = $buyerID->BUYER_ID;
							}
			
							$credentialUserBuyersArr = implode(',',$credentialUserBuyersArr);
						}
						//print_r($credentialUserBuyersArr);die;
					}
					//$field_array="id, entry_form, mst_id, approved_no, sequence_no, current_approval_status, approved_by, approved_date, user_ip,comments,inserted_by,insert_date";
					$id=return_next_id( "id","approval_history", 1 ) ;
			
					$max_approved_no_arr = return_library_array("select mst_id, max(approved_no) as approved_no from approval_history where mst_id in($booking_ids) and entry_form=7 group by mst_id","mst_id","approved_no");
			
					$approved_status_arr = return_library_array("select id, is_approved from wo_booking_mst where id in($booking_ids)","id","is_approved");
			
					$approved_no_array=array();
					$booking_ids_all=explode(",",$booking_ids);
					$booking_nos_all=explode(",",$booking_nos);
					$app_instru_all=explode(",",$appv_instras);
					$book_nos='';
			
					for($i=0;$i<count($booking_nos_all);$i++)
					{
						$val=$booking_nos_all[$i];
						$booking_id=$booking_ids_all[$i];
						$app_instru=$app_instru_all[$i];
			
						$approved_no=$max_approved_no_arr[$booking_id];
						$approved_status=$approved_status_arr[$booking_id];
						$buyer_id=$buyer_arr[$booking_id];
						//echo $buyer_id;die;
						if($approved_status==0)
						{
							$approved_no=$approved_no+1;
							$approved_no_array[$val]=$approved_no;
							if($book_nos=="") $book_nos=$val; else $book_nos.=",".$val;
						}
						//echo "20**";
						if($is_not_last_user == "")
						{
							if(in_array($buyer_id,$credentialUserBuyersArr))
							{
								$partial_approval=3;
							}
							else
							{
								$partial_approval=1;
							}
						}
						else
						{
							if(count($credentialUserBuyersArr)>0)
							{
								if(in_array($buyer_id,$credentialUserBuyersArr))
								{
									$partial_approval=3;
								}
								else
								{
									$partial_approval=1;
								}
							}
							else
							{
								$partial_approval=3;
							}
							//$partial_approval=3;
						}
						//echo "20".$partial_approval;die;
						$booking_id_arr[]=$booking_id;
						$data_array_booking_update[$booking_id]=explode("*",($partial_approval));
			
						//if($data_array!="") $data_array.=",";
						//$data_array.="(".$id.",7,".$booking_id.",".$approved_no.",'".$user_sequence_no."',1,".$user_id_approval.",'".$pc_date_time."','".$user_ip."',".$app_instru.",".$user_id.",'".$pc_date_time."')";
						//$id=$id+1;
						
						$data_arr_save['ID']= $id;
						$data_arr_save['ENTRY_FORM']= 7;
						$data_arr_save['MST_ID']= $booking_id;
						$data_arr_save['APPROVED_NO']= $approved_no;
						$data_arr_save['SEQUENCE_NO']= $user_sequence_no;
						$data_arr_save['CURRENT_APPROVAL_STATUS']= 1;
						$data_arr_save['APPROVED_BY']= $user_id_approval;
						$data_arr_save['APPROVED_DATE']= $app_date;
						$data_arr_save['USER_IP']= $user_ip;
						$data_arr_save['COMMENTS']= '';
						$data_arr_save['INSERTED_BY']= $user_id_approval;
						$data_arr_save['INSERT_DATE']= $pc_date_time;
			
					}
					//echo "10**".$data_array;die;
					if(count($approved_no_array)>0)
					{
						$approved_string="";
			
						if($this->db->dbdriver == 'mysqli')
						{
							foreach($approved_no_array as $key=>$value)
							{
								$approved_string.=" WHEN $key THEN $value";
							}
						}
						else
						{
							foreach($approved_no_array as $key=>$value)
							{
								$approved_string.=" WHEN TO_NCHAR('$key') THEN '".$value."'";
							}
						}
			
						$approved_string_mst="CASE booking_no ".$approved_string." END";
						$approved_string_dtls="CASE booking_no ".$approved_string." END";
			
						$sql_insert="insert into wo_booking_mst_hstry(id, approved_no, booking_id, booking_type, is_short, booking_no_prefix, booking_no_prefix_num, booking_no, company_id, buyer_id, job_no, po_break_down_id, item_category, fabric_source, currency_id, exchange_rate, pay_mode, source, BOOKING_DATE, delivery_date, booking_month, booking_year, supplier_id, attention, booking_percent, colar_excess_percent, cuff_excess_percent, is_approved, is_deleted, status_active, inserted_by, insert_date, updated_by, update_date, ready_to_approved, is_apply_last_update, rmg_process_breakdown)
							select
							'', $approved_string_mst, id, booking_type, is_short, booking_no_prefix, booking_no_prefix_num, booking_no, company_id, buyer_id, job_no, po_break_down_id, item_category, fabric_source, currency_id, exchange_rate, pay_mode, source, BOOKING_DATE, delivery_date, booking_month, booking_year, supplier_id, attention, booking_percent, colar_excess_percent, cuff_excess_percent, is_approved, is_deleted, status_active, inserted_by, insert_date, updated_by, update_date, ready_to_approved, is_apply_last_update, rmg_process_breakdown from wo_booking_mst where booking_no in ('$book_nos')";
			
						
						$sql_insert_dtls="insert into wo_booking_dtls_hstry(id, approved_no, booking_dtls_id, job_no, po_break_down_id, pre_cost_fabric_cost_dtls_id, color_size_table_id, booking_no, booking_type, is_short, fabric_color_id, item_size, fin_fab_qnty, grey_fab_qnty, rate, amount, color_type, construction, copmposition, gsm_weight, dia_width, process_loss_percent, trim_group, description, brand_supplier, uom, process, sensitivity, wo_qnty, delivery_date, cons_break_down, inserted_by, insert_date, updated_by, update_date, status_active, is_deleted, rmg_qty, gmt_item, responsible_dept, responsible_person, reason, gmts_size, gmts_color_id)
							select
							'', $approved_string_dtls, id, job_no, po_break_down_id, pre_cost_fabric_cost_dtls_id, color_size_table_id, booking_no, booking_type, is_short, fabric_color_id, item_size, fin_fab_qnty, grey_fab_qnty, rate, amount, color_type, construction, copmposition, gsm_weight, dia_width, process_loss_percent, trim_group, description, brand_supplier, uom, process, sensitivity, wo_qnty, delivery_date, cons_break_down, inserted_by, insert_date, updated_by, update_date, status_active, is_deleted, rmg_qty, gmt_item, responsible_dept, responsible_person, reason, gmts_size, gmts_color_id from wo_booking_dtls where booking_no in ('$book_nos')";
						 
					}
			
					//$field_array_booking_update = "is_approved";
					//$rID=execute_query(bulk_update_sql_statement( "WO_BOOKING_MST", "id", $field_array_booking_update, $data_array_booking_update, $booking_id_arr));
					
					
					$data_arr_up=array();
					$data_arr_up[IS_APPROVED]= $partial_approval;
					$rID=$this->updateData('WO_BOOKING_MST', $data_arr_up, array('ID' =>$booking_id));
					
					
					if($rID) $flag=1; else $flag=0;
			
					$query="UPDATE approval_history SET current_approval_status=0 WHERE entry_form=7 and mst_id in ($booking_ids)";
					$rIDapp=$this->db->query($query);
					if($flag==1)
					{
						if($rIDapp) $flag=1; else $flag=0;
					}
			
				 
			
					//echo "18**".$sql_insert_dtls;die;
					//echo "18**insert into approval_history (".$field_array.") Values ".$data_array;die;
					//$rID2=sql_insert("approval_history",$field_array,$data_array,0);
					$rID2=$this->insertData("APPROVAL_HISTORY",$data_arr_save);
					//echo "18**".$rID2;die;
					if($flag==1)
					{
						if($rID2) $flag=1; else $flag=0;
					}
			
					if(count($approved_no_array)>0)
					{
						$rID3=$this->db->query($sql_insert);
						if($flag==1)
						{
							if($rID3) $flag=1; else $flag=0;
						}
			
						$rID4=$this->db->query($sql_insert_dtls);
						if($flag==1)
						{
							if($rID4) $flag=1; else $flag=0;
						} //echo $sql_insert_dtls;die;
					}
					
					
					if($flag==1) $msg=1; else $msg=0;
				}
				return $msg;
	


	
				
			}//end else if condition;
			//GSD Approval....................
			else if($page_id==850){
			
				$user_id=$user_id_approval;
				$page_id=850;
				$target_ids=$app_id;
				$approval_type=0;
			   
				$user_sequence_no=return_field_value("sequence_no","electronic_approval_setup","company_id=$company_id and page_id=$page_id and user_id=$user_id and is_deleted=0");
		
				$approved_no_array=array();
				$id=return_next_id( "id","approval_history", 1 ) ;
				$target_app_ids=explode(",",$target_ids);		
				$i=0;  
				
				foreach($target_app_ids as $val)
				{		
					$approved_no=return_field_value("max(approved_no)","approval_history","mst_id='$val'");
					$approved_no=$approved_no+1;
					$approved_no_array[$val]=$approved_no;
					
					if($i!=0) $data_array.=",";
					
					$data_arr_save=array(
						ID=>$id, 
						ENTRY_FORM=>23, 
						MST_ID=>$val, 
						APPROVED_NO=>$approved_no, 
						SEQUENCE_NO=>$user_sequence_no, 
						CURRENT_APPROVAL_STATUS=>1, 
						APPROVED_BY=>$user_id, 
						APPROVED_DATE=>$pc_date_time, 
						USER_IP=>'', 
					);
					$id=$id+1;
					$i++;
				}
				
				$approved_string="";
				
				foreach($approved_no_array as $key=>$value)
				{
					$approved_string.=" WHEN $key THEN $value";
				}
				
				$approved_string_mst="CASE id ".$approved_string." END";
				//$approved_string_dtls="CASE mst_id ".$approved_string." END";
				$sql_insert="insert into ppl_gsd_entry_mst_history(id, hist_mst_id, approved_no,company_id, po_dtls_id, po_job_no, po_break_down_id, working_hour, total_smv, allowance, sam_style, operation_count, pitch_time, man_power_1, man_power_2, per_hour_gmt_target, inserted_by, insert_date, updated_by, update_date, status_active, is_deleted, gmts_item_id, day_target, mc_operation_count, tot_mc_smv, opt_pitch_time, style_ref, buyer_id, tot_manual_smv, tot_finishing_smv, system_no_prefix, extention_no, system_no, extended_from, is_copied, is_approved, ready_to_approved)
				select	
				'', id, $approved_string_mst, company_id, po_dtls_id, po_job_no, po_break_down_id, working_hour, total_smv, allowance, sam_style, operation_count, pitch_time, man_power_1, man_power_2, per_hour_gmt_target, inserted_by, insert_date, updated_by, update_date, status_active, is_deleted, gmts_item_id, day_target, mc_operation_count, tot_mc_smv, opt_pitch_time, style_ref, buyer_id, tot_manual_smv, tot_finishing_smv, system_no_prefix, extention_no, system_no, extended_from, is_copied, is_approved, ready_to_approved from ppl_gsd_entry_mst where id in ($target_ids)";
					
				$rID3=$this->db->query($sql_insert,0);
			   
				if($flag==1) 
				{
					if($rID3) $flag=1; else $flag=0; 
				} 
				
				
				$data_arr_up[IS_APPROVED]= 1;
				$rID=$this->updateData('PPL_GSD_ENTRY_MST', $data_arr_up, array('ID' => $target_ids));
				if($rID) $flag=1; else $flag=0;
				$rID2=$this->insertData("APPROVAL_HISTORY",$data_arr_save);				
			
			
				if($flag==1) 
				{
					if($rID2) $flag=1; else $flag=0; 
				}
		
			   if($flag==1) $msg='1'; else $msg='0';
				
				return $msg;
				
			
			}//end else if condition;
			//Gate Pass Activation Approval.......................
			else if($page_id==670){
				$user_id=$user_id_approval;
				$page_id=670;
				$approval_ids=$app_id;
				$approval_type=0;
				
				$user_sequence_no=return_field_value("sequence_no","electronic_approval_setup","company_id=$company_id and page_id=$page_id and user_id=$user_id and is_deleted=0");
				$min_sequence_no=return_field_value("min(sequence_no)","electronic_approval_setup","company_id=$company_id and page_id=$page_id and is_deleted=0");
	
				
				
				$approval_ids=str_replace("'","",$approval_ids);				
				
				//$field_array="id, entry_form, mst_id, approved_no, sequence_no, current_approval_status, approved_by, approved_date"; 
				$id=return_next_id( "id","approval_history", 1 ) ;
				
				$max_approved_no_arr = return_library_array("select mst_id, max(approved_no) as approved_no from approval_history where mst_id in($approval_ids) and entry_form=19 group by mst_id","mst_id","approved_no");
				
				$approved_status_arr = return_library_array("select id, is_approved from inv_gate_pass_mst where id in($approval_ids)","id","is_approved");
				
		
				$approved_no_array=array();
				$booking_ids_all=explode(",",$approval_ids);
				$book_nos='';
				//print_r($booking_nos_all);die;
				
				for($i=0;$i<count($booking_ids_all);$i++)
				{
					$val=$booking_ids_all[$i];
					$booking_id=$booking_ids_all[$i];
		
					$approved_no=$max_approved_no_arr[$booking_id];
					$approved_status=$approved_status_arr[$booking_id];
					
					if($approved_status==0)
					{
						$approved_no=$approved_no+1;
						$approved_no_array[$val]=$approved_no;
						if($book_nos=="") $book_nos=$val; else $book_nos.=",".$val;
						
					}
					//echo $approved_status;die;
					$data_arr_save=array(
						ID=>$id, 
						ENTRY_FORM=>19, 
						MST_ID=>$booking_id, 
						APPROVED_NO=>$approved_no, 
						SEQUENCE_NO=>$user_sequence_no, 
						CURRENT_APPROVAL_STATUS=>1, 
						APPROVED_BY=>$user_id, 
						APPROVED_DATE=>$pc_date_time, 
					);
					
					$id=$id+1;
				}
				
				if(count($approved_no_array)>0)
				{
					/*
					
					$approved_string="";
					foreach($approved_no_array as $key=>$value)
					{
						$approved_string.=" WHEN ".str_replace("'","",$key)." THEN $value";
					}
					
					$approved_string_mst="CASE id ".$approved_string." END";
					$approved_string_dtls="CASE mst_id ".$approved_string." END";
					
					
					$sql_insert_dtls="insert into  inv_transaction_history (id, approve_no, transaction_id, mst_id, requisition_no, receive_basis,pi_wo_batch_no,company_id,supplier_id,prod_id,product_code,item_category,
		transaction_type,transaction_date,store_id,order_id,brand_id,order_uom,order_qnty,order_rate,order_ile,order_ile_cost,
		order_amount,cons_uom,cons_quantity,return_qnty,cons_reject_qnty,cons_rate,cons_ile,cons_ile_cost,cons_amount,balance_qnty,
		balance_amount,floor_id,machine_id,machine_category,no_of_bags,cone_per_bag,weight_per_bag,weight_per_cone,room,rack,self,bin_box,
		expire_date,dyeing_sub_process,issue_challan_no,remarks,batch_lot,location_id,department_id,section_id,job_no,dyeing_color_id,inserted_by,
		insert_date,updated_by,update_date,status_active,is_deleted) 
						select	
						'', $approved_string_dtls, id, mst_id, requisition_no, receive_basis,pi_wo_batch_no,company_id,supplier_id,prod_id,product_code,item_category,transaction_type,transaction_date,store_id,
		order_id,brand_id,order_uom,order_qnty,order_rate,order_ile,order_ile_cost,order_amount,cons_uom,cons_quantity,return_qnty,cons_reject_qnty,cons_rate,
		cons_ile,cons_ile_cost,cons_amount,balance_qnty,balance_amount,floor_id,machine_id,machine_category,no_of_bags,cone_per_bag,weight_per_bag,weight_per_cone,
		room,rack,self,bin_box,expire_date,dyeing_sub_process,issue_challan_no,remarks,batch_lot,location_id,department_id,section_id,job_no,dyeing_color_id,
		inserted_by,insert_date,updated_by,update_date,status_active,is_deleted   from inv_transaction where mst_id in ($book_nos) and transaction_type=2  and status_active=1";*/
					

					//$rID=sql_multirow_update("inv_gate_pass_mst","is_approved",1,"id",$booking_ids,1);
					//if($rID) $flag=1; else $flag=0;
					$data_arr_up=array();
					$data_arr_up[IS_APPROVED]= 1;
					$rID=$this->updateData('INV_GATE_PASS_MST', $data_arr_up, array('ID' => $approval_ids));
					if($rID) $flag=1; else $flag=0;
					
					 
					if($approval_ids!="")
					{ 
						$data_arr_up=array();
						$data_arr_up[CURRENT_APPROVAL_STATUS]= 0;
						$rIDapp=$this->updateData('APPROVAL_HISTORY', $data_arr_up, array('MST_ID' => $approval_ids,'ENTRY_FORM' => 19));
						if($flag==1) 
						{
							if($rIDapp) $flag=1; else $flag=0; 
						} 
					}					
					
					$rID2=$this->insertData("APPROVAL_HISTORY",$data_arr_save);				
					

					if($flag==1) 
					{
						if($rID2) $flag=1; else $flag=0; 
					}
					
				}
				if($flag==1) $msg='1'; else $msg='0';
				return $msg;
	
			}//end else if condition;
			//Purchase Requisition Approval.......................
			else if($page_id==813){
				$user_id=$user_id_approval;
				$page_id=813;
				$reqs_ids=$app_id;
				$approval_type=0;
				
				$user_sequence_no=return_field_value("sequence_no","electronic_approval_setup","page_id=$page_id and user_id=$user_id_approval and company_id = $company_id and is_deleted = 0");

    			$min_sequence_no=return_field_value("min(sequence_no)","electronic_approval_setup","page_id=$page_id and is_deleted = 0");

		
				$is_not_last_user=return_field_value("sequence_no","electronic_approval_setup","company_id=$company_id and page_id=$page_id and sequence_no>$user_sequence_no and bypass=2 and is_deleted=0");
		
				if($is_not_last_user!="") $partial_approval=3; else $partial_approval=1;
				
				
				//$reqs_ids=explode(",",$reqs_ids);
		
				$field_array="id, entry_form, mst_id, approved_no, sequence_no, current_approval_status, approved_by, approved_date, inserted_by, insert_date";  
				
				$i=0;
				$id=return_next_id( "id","approval_history", 1 ) ;
				
				$approved_no_array=array();
				foreach(explode(",",$reqs_ids) as $val)
				{
					$approved_no=return_field_value("max(approved_no) as approved_no","approval_history","mst_id='$val' and entry_form=1","approved_no");
					$approved_no=$approved_no+1;
				
					if($i!=0) $data_array.=",";
					 
					//$data_array.="(".$id.",1,".$val.",".$approved_no.",'".$user_sequence_no."',1,".$user_id_approval.",'".$pc_date_time."',".$user_id_approval.",'".$pc_date_time."')";
					
					$data_arr_save=array(
						ID=>$id, 
						ENTRY_FORM=>1, 
						MST_ID=>$val, 
						APPROVED_NO=>$approved_no, 
						SEQUENCE_NO=>$user_sequence_no, 
						CURRENT_APPROVAL_STATUS=>1, 
						APPROVED_BY=>$user_id_approval, 
						APPROVED_DATE=>$pc_date_time, 
						INSERTED_BY=>$user_id_approval, 
						INSERT_DATE=>$pc_date_time 
					);
					
					$approved_no_array[$val]=$approved_no;
						
					$id=$id+1;
					$i++;
				}
				
				
		
				$approved_string="";
			
				foreach($approved_no_array as $key=>$value)
				{
					$approved_string.=" WHEN $key THEN $value";
				}
				
				$approved_string_mst="CASE id ".$approved_string." END";
				$approved_string_dtls="CASE mst_id ".$approved_string." END";
				
				$sql_insert="INSERT into inv_pur_requisition_mst_hist(id, hist_mst_id, approved_no, entry_form, requ_no, requ_no_prefix, requ_prefix_num, company_id, item_category_id, supplier_id, location_id, division_id, department_id, section_id, requisition_date, store_name, pay_mode, source, cbo_currency, delivery_date, do_no, attention, remarks, terms_and_condition, manual_req, ready_to_approve, is_approved, status_active, is_deleted, inserted_by,insert_date,updated_by,update_date) 
				select	
				'', id, $approved_string_mst, entry_form, requ_no, requ_no_prefix, requ_prefix_num, company_id, item_category_id, supplier_id, location_id, division_id, department_id, section_id, requisition_date, store_name, pay_mode, source, cbo_currency, delivery_date, do_no, attention, remarks, terms_and_condition, manual_req, ready_to_approve, is_approved, status_active, is_deleted, inserted_by,insert_date,updated_by,update_date from  inv_purchase_requisition_mst where id in ($reqs_ids)";
				
				
				$sql_insert_dtls="INSERT into  inv_pur_requisition_dtls_hist(id, approved_no, mst_id, product_id, user_code_maintain, required_for, job_id, job_no, buyer_id, STYLE_REF_NO, color_id, count_id, composition_id, com_percent, yarn_type_id, yarn_inhouse_date, cons_uom, quantity, rate, amount, stock, remarks, inserted_by, insert_date, updated_by, update_date, status_active, is_deleted) 
				select	
				'', $approved_string_dtls, mst_id, product_id, user_code_maintain, required_for, job_id, job_no, buyer_id, STYLE_REF_NO, color_id, count_id, composition_id, com_percent, yarn_type_id, yarn_inhouse_date, cons_uom, quantity, rate, amount, stock, remarks, inserted_by, insert_date, updated_by, update_date, status_active, is_deleted from  inv_purchase_requisition_dtls where mst_id in ($reqs_ids)";
		
				
				
				//$rID=sql_multirow_update("inv_purchase_requisition_mst","is_approved",$partial_approval,"id",$req_nos,0);
				$data_arr_up[IS_APPROVED]= 1;
				$rID=$this->updateData('INV_PURCHASE_REQUISITION_MST', $data_arr_up, array('ID' => $reqs_ids));    
				if($rID) $flag=1; else $flag=0;
		
				//$query="UPDATE approval_history SET current_approval_status=0 WHERE entry_form=1 and mst_id in ($approval_ids)"; //die;
				//$rIDapp=execute_query($query,1);
				
				if($reqs_ids){
					$data_arr_up=array();
					$data_arr_up[CURRENT_APPROVAL_STATUS]= 0;
					$rIDapp=$this->updateData('APPROVAL_HISTORY', $data_arr_up, array('MST_ID' => $reqs_ids,'ENTRY_FORM' => 1));
				
					if($flag==1) 
					{
						if($rIDapp) $flag=1; else $flag=0; 
					}
				}
				
				
				//$rID2=sql_insert("approval_history",$field_array,$data_array,0);
				$rID2=$this->insertData("APPROVAL_HISTORY",$data_arr_save);	
				if($flag==1) 
				{
					if($rID2) $flag=1; else $flag=0; 
					
				}
				$rID3=$this->db->query($sql_insert,0);
				if($flag==1) 
				{
					if($rID3) $flag=1; else $flag=0; 
					
				}       
				$rID4=$this->db->query($sql_insert_dtls,1);
				if($flag==1) 
				{
					if($rID4) $flag=1; else $flag=0; 
					
				} 
				
				if($flag==1) $msg='1'; else $msg='0'; 
				return $msg;
		}//end else if condition;
			//Sample Requisition Approval.......................
			else if($page_id==937){
				$user_id=$user_id_approval;
				$page_id=937;
				$reqs_ids=$app_id;
				$approval_type=0;
				
				$user_sequence_no=return_field_value("sequence_no","electronic_approval_setup","company_id=$company_id and page_id=$page_id and user_id=$user_id_approval and is_deleted=0");
				$min_sequence_no=return_field_value("min(sequence_no)","electronic_approval_setup","company_id=$company_id and page_id=$page_id and is_deleted=0");
				
				//$reqs_ids=explode(",",trim($req_nos));
				$buyer_arr=return_library_array( "select id, buyer_name  from sample_development_mst where id in ($reqs_ids)", "id", "buyer_id"  );
				
				$is_not_last_user=return_field_value("sequence_no","electronic_approval_setup","company_id=$company_id and page_id=$page_id and sequence_no>$user_sequence_no and bypass=2 and is_deleted=0");
				
				if($this->db->dbdriver != 'mysqli') {$buyer_id_cond=" and a.buyer_id is null "; $buyer_id_cond2=" and b.buyer_id is not null ";}
				else 			{$buyer_id_cond=" and a.buyer_id='' "; $buyer_id_cond2=" and b.buyer_id!='' ";}
				
				$is_not_last_user=return_field_value("a.sequence_no as sequence_no","electronic_approval_setup a"," a.company_id=$company_id and a.page_id=$page_id and a.sequence_no>$user_sequence_no and a.bypass=2 and a.is_deleted=0 $buyer_id_cond","sequence_no");
				
				$partial_approval = "";
				if($is_not_last_user == "")
				{
					$sql = sql_select("select (b.buyer_id) as BUYER_ID from electronic_approval_setup b where b.company_id=$company_id and b.page_id=$page_id and b.sequence_no>$user_sequence_no and b.is_deleted=0 and b.bypass=2  group by b.buyer_id");
					if(count($sql)>0)
					{
						foreach ($sql as $key => $buyerID) {
							$credentialUserBuyersArr[$buyerID->BUYER_ID] = $buyerID->BUYER_ID;
						}
						//$credentialUserBuyersArr = implode(',',$credentialUserBuyersArr);
						//$credentialUserBuyersArr = explode(',',$credentialUserBuyersArr);
						//$credentialUserBuyersArr = array_unique($credentialUserBuyersArr);
					}
				}
				else
				{
					$check_user_buyer = sql_select("select b.user_id as user_id from user_passwd a, electronic_approval_setup b where a.id = b.user_id and b.company_id=$company_id and b.page_id=$page_id and b.sequence_no>$user_sequence_no and b.is_deleted=0 and b.bypass=2 $buyer_id_cond  group by b.user_id");
		
					if(count($check_user_buyer)==0)
					{
						
						$sql = sql_select("select b.buyer_id as BUYER_ID from user_passwd b, electronic_approval_setup a where b.id = a.user_id and a.company_id=$company_id and a.page_id=$page_id and b.sequence_no>$user_sequence_no and a.is_deleted=0 and a.bypass=2 $buyer_id_cond $buyer_id_cond2 group by b.buyer_id");
						if(count($sql)>0)
						{
							foreach ($sql as $key => $buyerID) {
								$credentialUserBuyersArr[$buyerID->BUYER_ID] = $buyerID->BUYER_ID;
							}
						}
						
						$sql = sql_select("select (b.buyer_id) as BUYER_ID from electronic_approval_setup b where b.company_id=$company_id and b.page_id=$page_id and b.sequence_no>$user_sequence_no and b.is_deleted=0 and b.bypass=2 $buyer_id_cond2  group by b.buyer_id");
						if(count($sql)>0)
						{
							foreach ($sql as $key => $buyerID) {
								$credentialUserBuyersArr[$buyerID->BUYER_ID] = $buyerID->BUYER_ID;
							}
						}
						//$credentialUserBuyersArr = implode(',',$credentialUserBuyersArr);
						//$credentialUserBuyersArr = explode(',',$credentialUserBuyersArr);
						//$credentialUserBuyersArr = array_unique($credentialUserBuyersArr);
					}
					
				}
				//$field_array="id, entry_form, mst_id, approved_no,sequence_no, current_approval_status, approved_by, approved_date,inserted_by,insert_date";
				
				$max_approved_no_arr = return_library_array("select mst_id, max(approved_no) as approved_no from approval_history where mst_id in($reqs_ids) and entry_form=25 group by mst_id","mst_id","approved_no");
				
				$approved_status_arr = return_library_array("select id, is_approved from sample_development_mst where id in($reqs_ids)","id","is_approved");
				 
				$i=0;
				$id=return_next_id( "id","approval_history", 1 ) ;
				$approved_no_array=array();
				$book_nos='';
				$booking_ids='';
				foreach(explode(",",$reqs_ids) as $val)
				{	
					
					$approved_no=$max_approved_no_arr[$val];
					$approved_status=$approved_status_arr[$val];
					$buyer_id=$buyer_arr[$val];
					
					if($approved_status==0)
					{
						$approved_no=$approved_no+1;
						$approved_no_array[$val]=$approved_no;
					}
					
					
					if($is_not_last_user == "")
					{
						if(in_array($buyer_id,$credentialUserBuyersArr))
						{
							$partial_approval=3;
						}			
						else
						{
							$partial_approval=1;
						}
					}
					else
					{
						if(count($credentialUserBuyersArr)>0)
						{
							if(in_array($buyer_id,$credentialUserBuyersArr))
							{
								$partial_approval=3;
							}			
							else
							{
								$partial_approval=1;
							}
						}
						else
						{
							$partial_approval=1;
						}
					}
					
					$booking_id_arr[]=$booking_id;
					$data_array_booking_update[$booking_id]=explode("*",($partial_approval));
					
					//if($data_array!="") $data_array.=",";
		
					//$data_array.="(".$id.",25,".$booking_id.",".$approved_no.",'".$user_sequence_no."',1,".$user_id_approval.",'".$pc_date_time."',".$user_id.",'".$pc_date_time."')";
					
					$data_arr_save=array(
						ID=>$id, 
						ENTRY_FORM=>25, 
						MST_ID=>$booking_id, 
						APPROVED_NO=>$approved_no, 
						SEQUENCE_NO=>$user_sequence_no, 
						CURRENT_APPROVAL_STATUS=>1, 
						APPROVED_BY=>$user_id_approval, 
						APPROVED_DATE=>$pc_date_time, 
						INSERTED_BY=>$user_id_approval, 
						INSERT_DATE=>$pc_date_time
					);
					
					
					$id=$id+1;
					$i++;
				}
				
		
		
			if(count($approved_no_array)>0)
			{
				$approved_string="";
				
				foreach($approved_no_array as $key=>$value)
				{
					$approved_string.=" WHEN $key THEN $value";
				}
		
		
				$approved_string_mst="CASE id ".$approved_string." END"; 
				//CASE id  WHEN 538 THEN 2 END
				$approved_string_dtls="CASE sample_mst_id ".$approved_string." END";
				$approved_string_dtls_fab="CASE sample_mst_id ".$approved_string." END";
				
				$sql_insert="insert into sample_development_mst_history(id,hist_mst_id,approved_no,requisition_number_prefix,requisition_number_prefix_num,requisition_number,sample_stage_id,requisition_date,quotation_id,style_ref_no,company_id,location_id,buyer_name,season,product_dept,dealing_marchant,agent_name,buyer_ref,bh_merchant,estimated_shipdate,remarks,inserted_by,insert_date,status_active,is_deleted,entry_form_id,updated_by,update_date) 
					select	
					'',id,$approved_string_mst,requisition_number_prefix,requisition_number_prefix_num,requisition_number,sample_stage_id,requisition_date,quotation_id,style_ref_no,company_id,location_id,buyer_name,season,product_dept,dealing_marchant,agent_name,buyer_ref,bh_merchant,estimated_shipdate,remarks,inserted_by,insert_date,status_active,is_deleted,entry_form_id,updated_by,update_date from  sample_development_mst where id in ($reqs_ids)";
						
				
		
				 $sql_insert_dtls="insert into sample_development_dtls_hist(id,approved_no,sample_mst_id,sample_name,gmts_item_id,smv,article_no,sample_color,sample_prod_qty,submission_qty,delv_start_date,delv_end_date,sample_charge,sample_curency,inserted_by,insert_date,status_active,is_deleted,entry_form_id,size_data,fabric_status,acc_status,embellishment_status,fab_status_id,acc_status_id,embellishment_status_id,updated_by,update_date) 
					select	
					'', $approved_string_dtls,sample_mst_id,sample_name,gmts_item_id,smv,article_no,sample_color,sample_prod_qty,submission_qty,delv_start_date,delv_end_date,sample_charge,sample_curency,inserted_by,insert_date,status_active,is_deleted,entry_form_id,size_data,fabric_status,acc_status,embellishment_status,fab_status_id,acc_status_id,embellishment_status_id,updated_by,update_date from  sample_development_dtls where sample_mst_id in ($reqs_ids) and entry_form_id=117";
		
					$sql_insert_dtls_fab="insert into sample_development_fabric_hist(id,approved_no,sample_mst_id,sample_name,gmts_item_id,body_part_id,fabric_nature_id,fabric_description,gsm,dia,color_data,color_type_id,width_dia_id,uom_id,required_dzn,required_qty,inserted_by,insert_date,status_active,is_deleted,updated_by,update_date,sample_name_ra,gmts_item_id_ra,trims_group_ra,description_ra,brand_ref_ra,uom_id_ra,req_dzn_ra,req_qty_ra,sample_name_re,gmts_item_id_re,name_re,type_re,remarks_re) 
					select	
					'',$approved_string_dtls_fab,sample_mst_id,sample_name,gmts_item_id,body_part_id,fabric_nature_id,fabric_description,gsm,dia,color_data,color_type_id,width_dia_id,uom_id,required_dzn,required_qty,inserted_by,insert_date,status_active,is_deleted,updated_by,update_date,sample_name_ra,gmts_item_id_ra,trims_group_ra,description_ra,brand_ref_ra,uom_id_ra,req_dzn_ra,req_qty_ra,sample_name_re,gmts_item_id_re,name_re,type_re,remarks_re from sample_development_fabric_acc where sample_mst_id in ($reqs_ids)";
				}
				$flag=1;
				
				/*$field_array_booking_update = "is_approved";
				$rID=execute_query(bulk_update_sql_statement( "sample_development_mst", "id", $field_array_booking_update, $data_array_booking_update, $booking_id_arr));
				if($rID) $flag=1; else $flag=0;*/
				
				$data_arr_up=array();
				$data_arr_up[IS_APPROVED]= $partial_approval;
				$rID=$this->updateData('SAMPLE_DEVELOPMENT_MST', $data_arr_up, array('ID' => $reqs_ids));
				if($rID) $flag=1; else $flag=0;

				
				$query="UPDATE approval_history SET current_approval_status=0 WHERE entry_form=25 and mst_id in ($reqs_ids)";
				$rIDapp=$this->db->query($query);
				if($flag==1) 
				{
					if($rIDapp) $flag=1; else $flag=0; 
				}
				
				$rID2=$this->insertData("APPROVAL_HISTORY",$data_arr_save);	
		
				if($flag==1) 
				{
					if($rID2) $flag=1; else $flag=0;
				} 
				
				$rID3=$this->db->query($sql_insert,0);
		
				if($flag==1) 
				{
					if($rID3) $flag=1; else $flag=0; 
				} 
				
				$rID4=$this->db->query($sql_insert_dtls,1);
		
				if($flag==1) 
				{
					if($rID4) $flag=1; else $flag=0; 
				} 
				
				$rID5=$this->db->query($sql_insert_dtls_fab,1);
				if($flag==1) 
				{
					if($rID5) $flag=1; else $flag=0; 
				} 
				//echo "21**".$flag;
				if($flag==1) $msg='1'; else $msg='0';
				return $msg;
			}//end else if condition;
			//Item Issue Requisiton Approval....................
			else if($page_id==1056){
				$user_id=$user_id_approval;
				$page_id=1056;
				$req_nos=$app_id;
				$approval_type=0;
				
				$user_sequence_no=return_field_value("sequence_no","electronic_approval_setup","company_id=$company_id and page_id=$page_id and user_id=$user_id_approval and is_deleted=0");
				$min_sequence_no=return_field_value("min(sequence_no)","electronic_approval_setup","company_id=$company_id and page_id=$page_id and is_deleted=0");

				$is_not_last_user=return_field_value("sequence_no","electronic_approval_setup","company_id=$company_id and page_id=$page_id and sequence_no>$user_sequence_no and bypass=2 and is_deleted=0");
		
				if($is_not_last_user!="") $partial_approval=3; else $partial_approval=1;
				//$field_array="id, entry_form, mst_id, approved_no, sequence_no, current_approval_status, approved_by, approved_date, user_ip,inserted_by,insert_date"; 
		
				$id=return_next_id( "id","approval_history", 1 ) ;
		
				$mst_id_approve_arr=array();
				$max_approved_no_arr = return_library_array("select mst_id, max(approved_no) as approved_no from approval_history where mst_id in($req_nos) and entry_form=26 group by mst_id","mst_id","approved_no");
		
				$approved_status_arr = return_library_array("select id, is_approved from inv_item_issue_requisition_mst where id in($req_nos) ","id","is_approved");
		
				$approved_no_array=array();
				
				$i=0;
				foreach(explode(",",$req_nos) as $val)
				{
					$approved_no=$max_approved_no_arr[$val]['approved_no'];
					$approved_status=$approved_status_arr[$val];
					if($approved_status == 0)
					{
						$approved_no=$approved_no+1;
						$approved_no_array[$val]=$approved_no;
					}
					//if($i!=0) $data_array.=",";
					
					//$data_array.="(".$id.",26,".$val.",".$approved_no.",".$user_sequence_no.",1,".$user_id_approval.",'".$pc_date_time."','".$user_ip."',".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."')"; 
					$data_arr_save=array(
						ID=>$id,
						ENTRY_FORM=>26,
						MST_ID=>$val,
						APPROVED_NO=>$approved_no,
						SEQUENCE_NO=>$user_sequence_no,
						CURRENT_APPROVAL_STATUS=>1,
						APPROVED_BY=>$user_id_approval,
						APPROVED_DATE=>$pc_date_time,
						USER_IP=>$user_ip,
						INSERTED_BY=>$user_id_approval,
						INSERT_DATE=>$pc_date_time,
					);
					
					$id=$id+1;
					$i++;
				}	
		
				//$rID=sql_multirow_update("inv_item_issue_requisition_mst","is_approved",$partial_approval,"id",$req_nos,0);    
				
				
				
				$data_arr_up=array();
				$data_arr_up[IS_APPROVED]= $partial_approval;
				$rID=$this->updateData('INV_ITEM_ISSUE_REQUISITION_MST', $data_arr_up, array('ID' => $req_nos));
				if($rID) $flag=1; else $flag=0;
				
				
				$query="UPDATE approval_history SET current_approval_status=0 WHERE entry_form=26 and mst_id in ($req_nos)";
				$rIDapp=$this->db->query($query);
				if($flag==1) 
				{
					if($rIDapp) $flag=1; else $flag=0; 
				} 
				
				$rID2=$this->insertData("APPROVAL_HISTORY",$data_arr_save);	
				if($flag==1) 
				{
					if($rID2) $flag=1; else $flag=0; 
				} 
				
				if($flag==1) $msg='1'; else $msg='0';
				return $msg;
			
				
			}//end else if condition;
			//Service Booking For Knitting....................
			else if($page_id==1120){
				$user_id=$user_id_approval;
				$page_id=1120;
				$target_ids=$app_id;
				$approval_type=0;

							
				$user_sequence_no=return_field_value("sequence_no","electronic_approval_setup","company_id=$company_id and page_id=$page_id and user_id=$user_id and is_deleted=0");
		
				$is_not_last_user=return_field_value("sequence_no","electronic_approval_setup","company_id=$company_id and page_id=$page_id and sequence_no>$user_sequence_no and bypass=2 and is_deleted=0");
		
				if($is_not_last_user!="") $partial_approval=3; else $partial_approval=1;
		
				$approved_no_array=array();
				
				$id=return_next_id( "id","approval_history", 1 ) ;
				$i=0;  
					
				foreach(explode(",",$target_ids) as $val)
				{		
					$approved_no=return_field_value("max(approved_no)","approval_history","mst_id='$val'");
					$approved_no=$approved_no+1;
					$approved_no_array[$val]=$approved_no;
				
					
					$data_arr_save=array(
						ID=>$id, 
						ENTRY_FORM=>29, 
						MST_ID=>$val, 
						APPROVED_NO=>$approved_no, 
						SEQUENCE_NO=>$user_sequence_no, 
						CURRENT_APPROVAL_STATUS=>1, 
						APPROVED_BY=>$user_id_approval, 
						APPROVED_DATE=>$pc_date_time, 
						USER_IP=>$user_ip
					);
					$id=$id+1;
					$i++;
				}
			
					//$rID=sql_multirow_update("wo_booking_mst","is_approved",$partial_approval,"id",$target_ids,0);    
					
					$data_arr_up=array();
					$data_arr_up[IS_APPROVED]= $partial_approval;
					$rID=$this->updateData('WO_BOOKING_MST', $data_arr_up, array('ID' => $target_ids));
					if($rID) $flag=1; else $flag=0;
			
					
					
					
					
					$query="UPDATE approval_history SET current_approval_status=0 WHERE entry_form=29 and mst_id in ($target_ids)";
					$rIDapp=$this->db->query($query);
					if($flag==1) 
					{
						if($rIDapp) $flag=1; else $flag=0; 
					}
			
					//$rID2=sql_insert("approval_history",$field_array,$data_array,0);
					$rID2=$this->insertData("APPROVAL_HISTORY",$data_arr_save);	
					if($flag==1) 
					{
						if($rID2) $flag=1; else $flag=0; 
					}
				   if($flag==1) $msg='1'; else $msg='0';
				   
				   return $msg;
					
				
			}//end else if condition;
			//Work Order for AOP Approval.....................
			else if($page_id==1177){
								  
				$user_id=$user_id_approval;
				$page_id=1177;
				$target_ids=$app_id;
				$approval_type=0;
					
				$user_sequence_no=return_field_value("sequence_no","electronic_approval_setup","company_id=$company_id and page_id=$page_id and user_id=$user_id and is_deleted=0");
		
				$approved_no_array=array();
				
				$id=return_next_id( "id","approval_history", 1 ) ;
				$i=0;  
				
				foreach(explode(",",$target_ids) as $val)
				{		
					$approved_no=return_field_value("max(approved_no)","approval_history","mst_id='$val'");
					$approved_no=$approved_no+1;
					$approved_no_array[$val]=$approved_no;
	
					
					$data_arr_save=array(
						ID=>$id, 
						ENTRY_FORM=>162, 
						MST_ID=>$val, 
						APPROVED_NO=>$approved_no, 
						SEQUENCE_NO=>$user_sequence_no, 
						CURRENT_APPROVAL_STATUS=>1, 
						APPROVED_BY=>$user_id_approval, 
						APPROVED_DATE=>$pc_date_time, 
						USER_IP=>$user_ip
					);
					
					
					
					$id=$id+1;
					$i++;
				}
				
					
					
				//$rID = sql_multirow_update("wo_booking_mst","is_approved",1,"id",$target_ids,0);
				$data_arr_up=array();
				$data_arr_up[IS_APPROVED]= 1;
				$rID=$this->updateData('WO_BOOKING_MST', $data_arr_up, array('ID' => $target_ids));
				if($rID) $flag=1; else $flag=0;
		
				//$rID2=sql_insert("approval_history",$field_array,$data_array,0);
				$rID2=$this->insertData("APPROVAL_HISTORY",$data_arr_save);	
				if($flag==1) 
				{
					if($rID2) $flag=1; else $flag=0; 
				}
		
			   if($flag==1) $msg='1'; else $msg='0';
			   return $msg;
					
		}//end else if condition;
			//Quick Costing Approval.....................
			else if($page_id==1620){
				$user_id=$user_id_approval;
				$page_id=1620;
				$booking_ids=$app_id;
				$approval_type=0;
				
				$user_sequence_no=return_field_value("sequence_no","electronic_approval_setup"," page_id=$page_id and user_id=$user_id_approval and is_deleted=0");
				$min_sequence_no=return_field_value("min(sequence_no)","electronic_approval_setup"," page_id=$page_id and is_deleted=0");
				$buyer_arr=return_library_array( "select a.id, a.buyer_id   from   qc_mst a   where   a.is_deleted=0 and  a.status_active=1  and a.id in ($booking_ids)", "id", "buyer_id"  );
				
				
				
				if($this->db->dbdriver == 'mysqli'){
					$buyer_id_cond=" and a.buyer_id is null "; $buyer_id_cond2=" and b.buyer_id is not null ";
				}
				else{
					$buyer_id_cond=" and a.buyer_id='' "; $buyer_id_cond2=" and b.buyer_id!='' ";
				}
		
		
		
				//echo "22**";
				$is_not_last_user=return_field_value("a.sequence_no","electronic_approval_setup a"," a.page_id=$page_id and a.sequence_no>$user_sequence_no and a.bypass=2 and a.is_deleted=0 $buyer_id_cond","sequence_no");
		
				//return $user_sequence_no;die;
		
				$partial_approval = "";
				if($is_not_last_user == "")
				{
					//$credentialUserBuyersArr = [];
					$sql = sql_select("select (b.buyer_id) as BUYER_ID from electronic_approval_setup b where b.page_id=$page_id and b.sequence_no>$user_sequence_no and b.is_deleted=0 and b.bypass=2  group by b.buyer_id");
					foreach ($sql as $key => $buyerID) {
						$credentialUserBuyersArr[$buyerID->BUYER_ID] = $buyerID->BUYER_ID;
					}
				}
				else
				{
		
					$check_user_buyer = sql_select("select b.user_id as user_id from user_passwd a, electronic_approval_setup b where a.id = b.user_id and b.page_id=$page_id and b.sequence_no>$user_sequence_no and b.is_deleted=0 and b.bypass=2 $buyer_id_cond  group by b.user_id");
					//echo "21**".count($check_user_buyer);die;
					if(count($check_user_buyer)==0)
					{
		
						$sql = sql_select("select b.buyer_id as BUYER_ID from user_passwd b, electronic_approval_setup a where b.id = a.user_id and  a.page_id=$page_id and a.sequence_no>$user_sequence_no and a.is_deleted=0 and a.bypass=2 $buyer_id_cond $buyer_id_cond2 group by b.buyer_id");
						foreach ($sql as $key => $buyerID) {
							$credentialUserBuyersArr[$buyerID->BUYER_ID] = $buyerID->BUYER_ID;
						}
		
						$sql = sql_select("select (b.buyer_id) as BUYER_ID from electronic_approval_setup b where  b.page_id=$page_id and b.sequence_no>$user_sequence_no and b.is_deleted=0 and b.bypass=2 $buyer_id_cond2  group by b.buyer_id");
						foreach ($sql as $key => $buyerID) {
							$credentialUserBuyersArr[$buyerID->BUYER_ID] = $buyerID->BUYER_ID;
						}
		
					}
					//print_r($credentialUserBuyersArr);die;
				}
				// if($is_not_last_user!="") $partial_approval=3; else $partial_approval=1;
		
				
				//$field_array="id, entry_form, mst_id, approved_no, sequence_no, current_approval_status, approved_by, approved_date,inserted_by,insert_date";
				$id=return_next_id( "id","approval_history", 1 ) ;
		
				$max_approved_no_arr = return_library_array("select mst_id, max(approved_no) as approved_no from approval_history where mst_id in($booking_ids) and entry_form=36 group by mst_id","mst_id","approved_no");
		
				$approved_status_arr = return_library_array("select id, approved from qc_mst where id in($booking_ids)","id","approved");
				//echo "21**";
			//	print_r($approved_status_arr);die;
				$approved_no_array=array();
				$booking_ids_all=explode(",",$booking_ids);
				$book_nos='';
				//print_r($credentialUserBuyersArr);die;
				for($i=0;$i<count($booking_ids_all);$i++)
				{
					$val=$booking_ids_all[$i];
					$booking_id=$booking_ids_all[$i];
					$confirm_id=$booking_ids_all[$i];
		
					$approved_no=$max_approved_no_arr[$booking_id];
					$approved_status=$approved_status_arr[$booking_id];
					$buyer_id=$buyer_arr[$booking_id];
		
		
					if($approved_status==0 || $approved_status==2)
					{
						$approved_no=$approved_no+1;
						$approved_no_array[$val]=$approved_no;
						if($book_nos=="") $book_nos=$val; else $book_nos.=",".$val;
					}
		
					if($is_not_last_user == "")
					{
						if(in_array($buyer_id,$credentialUserBuyersArr))
						{
							$partial_approval=3;
						}
						else
						{
							$partial_approval=1;
						}
					}
					else
					{
						if(count($credentialUserBuyersArr)>0)
						{
							if(in_array($buyer_id,$credentialUserBuyersArr))
							{
								$partial_approval=3;
							}
							else
							{
								$partial_approval=1;
							}
						}
						else
						{
							$partial_approval=3;
						}
						//$partial_approval=3;
					}
					//echo $partial_approval;die;
					$booking_id_arr[]=$booking_id;
					$data_array_booking_update[$booking_id]=explode("*",($partial_approval));
					$confirm_id_arr[]=$confirm_id;
					$data_array_confirm_update[$confirm_id]=explode("*",($partial_approval));
		
					if($partial_approval==1)
					{
						$full_approve_booking_id_arr[]=$booking_id;
						$full_approve_confirm_id_arr[]=$confirm_id;
						$data_array_full_approve_booking_update[$booking_id]=explode("*",($user_id_approval."*'".$pc_date_time."'"));
					}
		
		
					//if($data_array!="") $data_array.=",";
					//$data_array.="(".$id.",36,".$booking_id.",".$approved_no.",'".$user_sequence_no."',1,".$user_id_approval.",'".$pc_date_time."',".$user_id.",'".$pc_date_time."')";
					
					$data_arr_save=array(
						ID=>$id, 
						ENTRY_FORM=>36, 
						MST_ID=>$booking_id, 
						APPROVED_NO=>$approved_no, 
						SEQUENCE_NO=>$user_sequence_no, 
						CURRENT_APPROVAL_STATUS=>1, 
						APPROVED_BY=>$user_id_approval, 
						APPROVED_DATE=>$pc_date_time, 
						USER_IP=>$user_ip
					);
					
					$id=$id+1;
				}
		
				$flag=1;
				if(count($approved_no_array)>0)
				{
		
					$approved_string="";
		
					if($this->db->dbdriver == 'mysqli')
					{
						foreach($approved_no_array as $key=>$value)
						{
							$approved_string.=" WHEN $key THEN $value";
						}
					}
					else
					{
						foreach($approved_no_array as $key=>$value)
						{
							$approved_string.=" WHEN $key THEN '".$value."'";
						}
					}
		
					$approved_string_mst="CASE qc_no ".$approved_string." END";
					$approved_string_dtls="CASE mst_id ".$approved_string." END";
					$approved_string_confirm="CASE cost_sheet_id ".$approved_string." END";
		
					$confirm_mst_sql="insert into qc_confirm_mst_history(id, approved_no, confirm_mst_id,cost_sheet_id, lib_item_id, confirm_style,confirm_order_qty, confirm_fob, deal_merchant,  ship_date, inserted_by, insert_date, updated_by, update_date, status_active, is_deleted, job_id, approved,  approved_by, approved_date) 
		
						select 
						  '',$approved_string_confirm,id, cost_sheet_id, lib_item_id,confirm_style, confirm_order_qty, confirm_fob,  deal_merchant, ship_date, inserted_by, insert_date, updated_by, update_date,  status_active, is_deleted, job_id,  approved, approved_by, approved_date from qc_confirm_mst where cost_sheet_id in ($booking_ids)";
		
					$confirm_dtls_sql="insert into qc_confirm_dtls_history( id, approved_no, confirm_dtls_id, mst_id, cost_sheet_id, item_id, 
					   fab_cons_kg, fab_cons_yds, fab_amount, sp_oparation_amount, acc_amount, fright_amount,  lab_amount, misce_amount, other_amount,  comm_amount, fob_amount, cm_amount,  rmg_ratio, inserted_by, insert_date,  updated_by, update_date, status_active,  is_deleted, fab_cons_mtr, cppm_amount,  smv_amount) 
		
					select 
					  '',$approved_string_confirm,id, mst_id, cost_sheet_id, item_id, fab_cons_kg, fab_cons_yds, fab_amount, sp_oparation_amount, acc_amount,  fright_amount, lab_amount, misce_amount,  other_amount, comm_amount, fob_amount,   cm_amount, rmg_ratio, inserted_by,  insert_date, updated_by, update_date,  status_active, is_deleted, fab_cons_mtr,   cppm_amount, smv_amount
					from qc_confirm_dtls where cost_sheet_id in ($booking_ids)";
		
		
		
					$sql_insert_cons_rate="insert into  qc_cons_rate_dtls_histroy( id,approved_no, cons_rate_dtls_id, mst_id, item_id, type, 	particular_type_id, formula, consumption, unit,  is_calculation, rate, rate_data, value, inserted_by, insert_date, 
						updated_by, update_date, status_active,  is_deleted, ex_percent)
					   select 
					   '',$approved_string_dtls, id, mst_id, item_id, type, particular_type_id, formula,  consumption, unit, is_calculation, rate, rate_data, value, 
					   inserted_by, insert_date, updated_by,update_date, status_active, is_deleted, ex_percent
					from qc_cons_rate_dtls where mst_id in ($booking_ids) ";
					//echo $sql_insert;die;
		
		
					$sql_fabric_dtls="insert into  qc_fabric_dtls_history(id,approved_no, fabric_dtls_id, mst_id,  item_id, body_part, 
					des,value, alw, inserted_by,insert_date, updated_by, update_date,  status_active, is_deleted, uniq_id)
					select 
						'',$approved_string_dtls,id, mst_id, item_id, body_part, des, value, alw, inserted_by, insert_date, updated_by, update_date,
						 status_active,  is_deleted, uniq_id from qc_fabric_dtls where mst_id in ($booking_ids)";
					//echo $sql_precost_dtls;die;
		
		
					//--------------------------------------wo_pre_cost_fabric_cost_dtls_h---------------------------------------------------------------------------
		
					$sql_item_cost_dtls="insert into qc_item_cost_summary_his(id, approved_no ,item_sum_id, mst_id, item_id, fabric_cost, sp_operation_cost,accessories_cost, smv, efficiency, cm_cost, frieght_cost, lab_test_cost, miscellaneous_cost, other_cost, commission_cost,fob_pcs, inserted_by, insert_date, updated_by, update_date, status_active,is_deleted, rmg_ratio, cpm)
						select
						'', $approved_string_dtls,  id, mst_id, item_id, fabric_cost, sp_operation_cost, accessories_cost,  smv, efficiency, cm_cost,frieght_cost, lab_test_cost, miscellaneous_cost, other_cost, commission_cost, fob_pcs, inserted_by, insert_date, updated_by, update_date, status_active, is_deleted, rmg_ratio, cpm from qc_item_cost_summary where  mst_id in ($booking_ids)";
					//echo $sql_precost_fabric_cost_dtls;die;
		
					//--------------------------------------wo_pre_cost_fab_yarn_cst_dtl_h---------------------------------------------------------------------------
					$sql_meeting_mst="insert into qc_meeting_mst_history(id, approved_no, metting_mst_id, mst_id, meeting_no, buyer_agent_id, location_id, meeting_date, meeting_time, remarks,  inserted_by, insert_date, updated_by,  update_date, status_active, is_deleted)
						select
						'', $approved_string_dtls, id, mst_id, meeting_no, buyer_agent_id, location_id, meeting_date, meeting_time, remarks, inserted_by, insert_date, updated_by, update_date, status_active, is_deleted from qc_meeting_mst  where  mst_id in ($booking_ids)";
						//echo $sql_precost_fab_yarn_cst;die;
		
					//----------------------------------wo_pre_cost_comarc_cost_dtls_h----------------------------------------
					$sql_qc_mst="insert into qc_mst_history( id , approved_no, qc_mst_id, cost_sheet_id, cost_sheet_no, temp_id, style_des, buyer_id, cons_basis, season_id, style_ref, department_id, delivery_date, exchange_rate, offer_qty, quoted_price, tgt_price, stage_id, costing_date, inserted_by, insert_date, updated_by, update_date, status_active, is_deleted, lib_item_id, pre_cost_sheet_id, revise_no,  option_id, buyer_remarks, option_remarks,  meeting_no, qc_no, uom, 
						approved, approved_by, approved_date, from_client)
						select
						'', $approved_string_mst, id, cost_sheet_id, cost_sheet_no, temp_id, style_des, buyer_id, cons_basis, season_id, style_ref, department_id, delivery_date, exchange_rate,  offer_qty, quoted_price, tgt_price,  stage_id, costing_date, inserted_by,  insert_date, updated_by, update_date,  status_active, is_deleted, lib_item_id,  pre_cost_sheet_id, revise_no, option_id,  buyer_remarks, option_remarks, meeting_no,  qc_no, uom, approved, approved_by, approved_date, from_client from qc_mst where  qc_no in ($booking_ids)";
						//echo $sql_precost_fcomarc_cost_dtls;die;
		
		
					//-------------------------------------pre_cost_commis_cost_dtls_h-------------------------------------------
					$sql_tot_cost="insert into qc_tot_cost_summary_history( id,approved_no, tot_sum_id, mst_id,  buyer_agent_id, location_id, no_of_pack,  is_confirm, is_cm_calculative, mis_lumsum_cost,  commision_per, tot_fab_cost, tot_sp_operation_cost, tot_accessories_cost, tot_cm_cost, tot_fright_cost,  tot_lab_test_cost, tot_miscellaneous_cost, tot_other_cost,  tot_commission_cost, tot_cost, tot_fob_cost,  inserted_by, insert_date, updated_by,  update_date, status_active, is_deleted,  tot_rmg_ratio)
						select
						'', $approved_string_dtls,  id, mst_id, buyer_agent_id,location_id, no_of_pack, is_confirm, is_cm_calculative, mis_lumsum_cost, commision_per, tot_fab_cost, tot_sp_operation_cost, tot_accessories_cost,  tot_cm_cost, tot_fright_cost, tot_lab_test_cost, tot_miscellaneous_cost, tot_other_cost, tot_commission_cost,  tot_cost, tot_fob_cost, inserted_by,   insert_date, updated_by, update_date,  status_active, is_deleted, tot_rmg_ratio from qc_tot_cost_summary where  mst_id in ($booking_ids)";
		
					if(count($confirm_mst_sql)>0)
					{
						
						$rID12=$this->db->query($confirm_mst_sql);
						if($flag==1)
						{
							if($rID12) $flag=1; else $flag=130;
						}
					}
		
		
					if(count($confirm_dtls_sql)>0)
					{
						
						$rID13=$this->db->query($confirm_dtls_sql);
						if($flag==1)
						{
							if($rID13) $flag=1; else $flag=120;
						}
					}
		
					if(count($sql_insert_cons_rate)>0)
					{
		
						$rID13=$this->db->query($sql_insert_cons_rate);
						if($flag==1)
						{
							if($rID13) $flag=1; else $flag=110;
						}
					}
		
					if(count($sql_fabric_dtls)>0)
					{
						$rID3=$this->db->query($sql_fabric_dtls);
						if($flag==1)
						{
							if($rID3) $flag=1; else $flag=100;
						}
					}
					
					
					if(count($sql_item_cost_dtls)>0)
					{
						$rID4=$this->db->query($sql_item_cost_dtls);
						if($flag==1)
						{
							if($rID4) $flag=1; else $flag=90;
						}
					}
		
					if(count($sql_meeting_mst)>0)
					{
						$rID5=$this->db->query($sql_meeting_mst);
						if($flag==1)
						{
							if($rID5) $flag=1; else $flag=80;
						}
					}
		
					if(count($sql_qc_mst)>0)
					{
						
						$rID6=$this->db->query($sql_qc_mst);
						if($flag==1)
						{
							if($rID6) $flag=1; else $flag=70;
						}
					}
		
					if(count($sql_tot_cost)>0)
					{
						$rID7=$this->db->query($sql_tot_cost);
						if($flag==1)
						{
							if($rID7) $flag=1; else $flag=60;
						}
					}
		
				}
		
				$rID8=$rID9=1;
				if(count($full_approve_booking_id_arr)>0)
				{
		
					
					
					
					//$field_array_full_approved_booking_update = "APPROVED_BY*APPROVED_DATE";
					//$rID8=execute_query(bulk_update_sql_statement( "QC_MST", "id", $field_array_full_approved_booking_update, $data_array_full_approve_booking_update, $full_approve_booking_id_arr));
					
					$data_arr_up=array();
					$data_arr_up[APPROVED_BY]= $user_id_approval;
					$data_arr_up[APPROVED_DATE]= $pc_date_time;
					$rID8=$this->updateData('QC_MST', $data_arr_up, array('ID' => $booking_ids));
					if($flag==1)
					{
						if($rID8) $flag=1; else $flag=50;
					}
					
					
					
		
					//$rID9=execute_query(bulk_update_sql_statement( "qc_confirm_mst", "id", $field_array_full_approved_booking_update, $data_array_full_approve_booking_update, $full_approve_confirm_id_arr));
					
					$data_arr_up=array();
					$data_arr_up[APPROVED_BY]= $user_id_approval;
					$data_arr_up[APPROVED_DATE]= $pc_date_time;
					$rID9=$this->updateData('QC_CONFIRM_MST', $data_arr_up, array('ID' => $booking_ids));
					if($flag==1)
					{
						if($rID9) $flag=1; else $flag=40;
					}
				}
		
				//$field_array_booking_update = "approved";
		
				//$rID=execute_query(bulk_update_sql_statement( "qc_mst", "id", $field_array_booking_update, $data_array_booking_update, $booking_id_arr));
				
				
				$data_arr_up=array();
				$data_arr_up[APPROVED]= $partial_approval;
				$rID=$this->updateData('QC_MST', $data_arr_up, array('ID' => $booking_ids));
				if($flag==1)
				{
					if($rID) $flag=1; else $flag=30;
				}
		
				//$rIDConfirm=execute_query(bulk_update_sql_statement( "qc_confirm_mst", "id", $field_array_booking_update, $data_array_confirm_update, $confirm_id_arr));
				$data_arr_up=array();
				$data_arr_up[APPROVED]= $partial_approval;
				$rIDConfirm=$this->updateData('QC_CONFIRM_MST', $data_arr_up, array('ID' => $booking_ids));
				if($flag==1)
				{
					if($rIDConfirm) $flag=1; else $flag=20;
				}
		
				$query="UPDATE approval_history SET current_approval_status=0 WHERE entry_form=36 and mst_id in ($booking_ids)";
				$rIDapp=$this->db->query($query);
				if($flag==1)
				{
					if($rIDapp) $flag=1; else $flag=10;
				}
		
				//$rID2=sql_insert("approval_history",$field_array,$data_array,0);
				$rID2=$this->insertData("APPROVAL_HISTORY",$data_arr_save);
				if($flag==1)
				{
					if($rID2) $flag=1; else $flag=0;
				}
		
				//echo "21**".$flag;die;
				if($flag==1) $msg='1'; else $msg='0';
				return $msg;
				
			
			
			}//end else if condition;
			//Dyeing Batch Approval.....................
			else if($page_id==616){
								  
				$user_id=$user_id_approval;
				$page_id=616;
				$mst_id=$app_id;
				$approval_type=0;
					
				$user_sequence_no=return_field_value("sequence_no","electronic_approval_setup","page_id=$page_id and user_id=$user_id_approval and is_deleted=0");
				$min_sequence_no=return_field_value("min(sequence_no)","electronic_approval_setup","page_id=$page_id and is_deleted=0");
				//$entry_form_arr = return_library_array("select id, entry_form from pro_batch_create_mst where id = $mst_id","id","entry_form");		
		
				$is_not_last_user=return_field_value("sequence_no","electronic_approval_setup","company_id=$company_id and page_id=$page_id and sequence_no>$user_sequence_no and bypass=2 and is_deleted=0");
				
				if($is_not_last_user!="") $partial_approval=3; else $partial_approval=1;
		
				//$field_array="id,entry_form, mst_id, approved_no, sequence_no, current_approval_status, approved_by, approved_date, user_ip,comments,inserted_by,insert_date"; 
				$id=return_next_id( "id","approval_history", 1 ) ;
				
				$max_approved_no_arr = return_library_array("select mst_id, max(approved_no) as approved_no from approval_history where mst_id in($mst_id)
				and entry_form=16 group by mst_id","mst_id","approved_no");
				
				$approved_status_arr = return_library_array("select id, is_approved from pro_batch_create_mst where id in($mst_id)","id","is_approved");
				
				
				$approved_no_array=array();
			
				foreach(explode(",",$mst_id) as $val)
				{
		
					$approved_no=$max_approved_no_arr[$val];
					$approved_status=$approved_status_arr[$val];
					$entry_form=$entry_form_arr[$val];
					
					if($approved_status==0)
					{
						$approved_no=$approved_no+1;
						$approved_no_array[$val]=$approved_no;
						if($book_nos=="") $book_nos=$val; else $book_nos.=",".$val;
					}
					//if($data_array!="") $data_array.=",";
					//$data_array.="(".$id.",16,".$booking_id.",".$approved_no.",'".$user_sequence_no."',1,".$user_id_approval.",'".$pc_date_time."','".$user_ip."',".$app_instru.",".$user_id.",'".$pc_date_time."')"; 
					
					$data_arr_save=array(
						ID=>$id, 
						ENTRY_FORM=>16, 
						MST_ID=>$val, 
						APPROVED_NO=>$approved_no, 
						SEQUENCE_NO=>$user_sequence_no, 
						CURRENT_APPROVAL_STATUS=>1, 
						APPROVED_BY=>$user_id_approval, 
						APPROVED_DATE=>$pc_date_time, 
						USER_IP=>$user_ip
					);
					
					$id=$id+1;
				}
				
				//var_dump($approved_no_array);die;		
				
				if(count($approved_no_array)>0)
				{
					$approved_string="";
					foreach($approved_no_array as $key=>$value)
					{
						$approved_string.=" WHEN $key THEN $value";
					}
					$approved_string_mst="CASE id ".$approved_string." END";
					$approved_string_dtls="CASE mst_id ".$approved_string." END"; 
					
		
					$sql_insert="INSERT INTO pro_batch_create_mst_histry (id,approved_no,batch_id,batch_no,entry_form, batch_date, batch_against, batch_for,
					company_id, booking_no_id, booking_no, booking_without_order, extention_no, color_id, batch_weight, total_trims_weight, color_range_id,
					process_id, organic, total_liquor, re_dyeing_from, dur_req_hr, dur_req_min, remarks, inserted_by, insert_date, updated_by, update_date,
					ready_to_approved, is_approved, status_active, is_deleted)
					
					select	
					'', $approved_string_mst, id,batch_no,entry_form, batch_date, batch_against, batch_for, company_id, booking_no_id, booking_no,
					booking_without_order, extention_no, color_id, batch_weight, total_trims_weight, color_range_id, process_id, organic, total_liquor,
					re_dyeing_from, dur_req_hr, dur_req_min, remarks, inserted_by, insert_date, updated_by, update_date, ready_to_approved, is_approved,
					status_active, is_deleted
					from pro_batch_create_mst where id in ($mst_id)";
					
					$sql_insert_dtls="INSERT INTO pro_batch_create_dtls_histry (id,approved_no,batch_dtls_id, mst_id, program_no, fabric_from, po_id, po_batch_no,
					prod_id, item_description, fin_dia, width_dia_type, roll_no, roll_id, barcode_no, batch_qnty, rec_challan, dtls_id, inserted_by, insert_date,
					updated_by, update_date, status_active, is_deleted)
					
					select	
					
					'', $approved_string_dtls, id, mst_id, program_no, fabric_from, po_id, po_batch_no, prod_id, item_description, fin_dia, width_dia_type,
					roll_no, roll_id, barcode_no, batch_qnty, rec_challan, dtls_id, inserted_by, insert_date, updated_by, update_date, status_active, is_deleted
					from pro_batch_create_dtls where mst_id in ($mst_id)";
				
				}
				
				//$rID=sql_multirow_update("pro_batch_create_mst","is_approved*ready_to_approved","1*1","id",$booking_ids,0);
				$data_arr_up=array();
				$data_arr_up[IS_APPROVED]= 1;
				$data_arr_up[READY_TO_APPROVED]= 1;
				$rID=$this->updateData('PRO_BATCH_CREATE_MST', $data_arr_up, array('ID' => $mst_id));
				
				//$rID2=sql_insert("approval_history",$field_array,$data_array,0);
				$rID2=$this->insertData("APPROVAL_HISTORY",$data_arr_save);
				if($rID2 && $rID) $flag=1; else $flag=20; 
				
				if(count($approved_no_array)>0)
				{
					$rID3=$this->db->query($sql_insert,0);
					if($flag==1) 
					{
						if($rID3) $flag=1; else $flag=30; 
					} 
		
					$rID4=$this->db->query($sql_insert_dtls,1);
					if($flag==1) 
					{
						if($rID4) $flag=1; else $flag=40; 
					} 
				}
				if($flag==1) $msg='1'; else $msg='0';
			   return $msg;
					
		}//end else if condition;
			//Trims Booking Approval [with order].....................
			else if($page_id==336000000){
								  
				$user_id=$user_id_approval;
				$page_id=336;
				$mst_id=$app_id;
				$approval_type=0;
				$booking_no=return_field_value("booking_no","wo_booking_mst","id=$mst_id and is_deleted=0");

					
				
				
				$user_sequence_no=return_field_value("sequence_no","electronic_approval_setup","page_id=$page_id and user_id=$user_id_approval and is_deleted=0");
				$min_sequence_no=return_field_value("min(sequence_no)","electronic_approval_setup","page_id=$page_id and is_deleted=0");
						
		
				 //With Order.................
				
				$is_not_last_user=return_field_value("sequence_no","electronic_approval_setup","company_id=$company_id and page_id=$page_id and sequence_no>$user_sequence_no and bypass=2 and is_deleted=0");
				if($is_not_last_user!="") $partial_approval=3; else $partial_approval=1;
				// echo $partial_approval;die;
	
				
				//$field_array="id, entry_form, mst_id, approved_no, sequence_no, current_approval_status,comments, approved_by, approved_date, inserted_by, insert_date"; 
				$id=return_next_id( "id","approval_history", 1 ) ;
					  
				$max_approved_no_arr = return_library_array("select mst_id, max(approved_no) as approved_no from approval_history where mst_id in($mst_id) and entry_form=8 group by mst_id","mst_id","approved_no");
				$approved_status_arr = return_library_array("select id, is_approved from wo_booking_mst where id in($mst_id)","id","is_approved");
			
				$approved_no_array=array();
				$book_nos='';
				foreach(explode(",",$mst_id) as $val)
				{
					$approved_no=$max_approved_no_arr[$val];
					$approved_status=$approved_status_arr[$val];
					
					if($approved_status==0)
					{
						$approved_no=$approved_no+1;
						$approved_no_array[$booking_no]=$approved_no;
					}
					
					
					$data_arr_save=array(
						ID=>$id, 
						ENTRY_FORM=>8, 
						MST_ID=>$val, 
						APPROVED_NO=>$approved_no, 
						SEQUENCE_NO=>$user_sequence_no, 
						CURRENT_APPROVAL_STATUS=>1, 
						APPROVED_BY=>$user_id_approval, 
						APPROVED_DATE=>$pc_date_time, 
						USER_IP=>$user_ip
					);
                    
					$id=$id+1;
				}
				
				$flag=1;
				if(count($approved_no_array)>0)
				{
					$approved_string="";
					if($this->db->dbdriver == 'mysqli')
					{
						foreach($approved_no_array as $key=>$value)
						{
							$approved_string.=" WHEN '$key' THEN $value";
							$approved_string1.=" WHEN '$key' THEN $value";
						}
					}
					else
					{
						foreach($approved_no_array as $key=>$value)
						{
							$approved_string.=" WHEN TO_NCHAR('$key') THEN '".$value."'";
							$approved_string1.=" WHEN TO_CHAR('$key') THEN '".$value."'";
						}
					}
					
					$approved_string_mst="CASE booking_no ".$approved_string." END";
					$approved_string_dtls="CASE booking_no ".$approved_string." END";
					$approved_string_dtls1="CASE booking_no ".$approved_string1." END";
					
					$sql_insert="insert into wo_booking_mst_hstry(id, approved_no, booking_id, booking_type, is_short, booking_no_prefix, booking_no_prefix_num, booking_no, company_id, buyer_id, job_no, po_break_down_id, item_category, fabric_source, currency_id, exchange_rate, pay_mode, source, booking_date, delivery_date, booking_month, booking_year, supplier_id, attention, booking_percent, colar_excess_percent, cuff_excess_percent, is_approved, inserted_by, insert_date, updated_by, update_date, status_active, is_deleted) 
						select	
						'', $approved_string_mst, id, booking_type, is_short, booking_no_prefix, booking_no_prefix_num, booking_no, company_id, buyer_id, job_no, po_break_down_id, item_category, fabric_source, currency_id, exchange_rate, pay_mode, source, booking_date, delivery_date, booking_month, booking_year, supplier_id, attention, booking_percent, colar_excess_percent, cuff_excess_percent, is_approved, inserted_by, insert_date, updated_by, update_date, status_active, is_deleted from wo_booking_mst where booking_no in ('$booking_no')";
							
					
					$sql_insert_dtls="insert into wo_booking_dtls_hstry(id, approved_no, booking_dtls_id, job_no, po_break_down_id, pre_cost_fabric_cost_dtls_id, color_size_table_id, booking_no, booking_type, is_short, fabric_color_id, item_size, fin_fab_qnty, grey_fab_qnty, rate, amount, color_type, construction, copmposition, gsm_weight, dia_width, process_loss_percent, trim_group, description, brand_supplier, uom, process, sensitivity, wo_qnty, delivery_date, cons_break_down, inserted_by, insert_date, updated_by, update_date, status_active, is_deleted) 
						select	
						'', $approved_string_dtls, id, job_no, po_break_down_id, pre_cost_fabric_cost_dtls_id, color_size_table_id, booking_no, booking_type, is_short, fabric_color_id, item_size, fin_fab_qnty, grey_fab_qnty, rate, amount, color_type, construction, copmposition, gsm_weight, dia_width, process_loss_percent, trim_group, description, brand_supplier, uom, process, sensitivity, wo_qnty, delivery_date, cons_break_down, inserted_by, insert_date, updated_by, update_date, status_active, is_deleted from wo_booking_dtls where booking_no in ('$booking_no')";
							
				
					
					$sql_insert_cons_dtls="insert into wo_trim_book_con_dtls_hstry(id, approved_no,wo_trim_book_con_dtl_id, wo_trim_booking_dtls_id,booking_no, job_no, po_break_down_id, color_number_id, gmts_sizes, item_color, item_size, cons, process_loss_percent, requirment, pcs, color_size_table_id) 
						select	
						'', $approved_string_dtls1, id,wo_trim_booking_dtls_id,booking_no, job_no, po_break_down_id, color_number_id, gmts_sizes, item_color, item_size, cons, process_loss_percent, requirment, pcs, color_size_table_id from wo_trim_book_con_dtls where booking_no in ('$booking_no')";
					
					 
						
					$rID3=$this->db->query($sql_insert,0);
					if($flag==1) 
					{
						if($rID3) $flag=1; else $flag=0; 
					} 
						
					$rID4=$this->db->query($sql_insert_dtls,1);
					if($flag==1) 
					{
						if($rID4) $flag=1; else $flag=0; 
					} 
					
					$rID5=$this->db->query($sql_insert_cons_dtls,1);
					if($flag==1) 
					{
						if($rID5) $flag=1; else $flag=0; 
					} 
				}
				
				//$rID=sql_multirow_update("wo_booking_mst","is_approved",$partial_approval,"id",$booking_ids,0);
				$data_arr_up=array();
				$data_arr_up[IS_APPROVED]= $partial_approval;
				$rID=$this->updateData('WO_BOOKING_MST', $data_arr_up, array('ID' => $mst_id));
				if($rID) $flag=1; else $flag=0;
				
				
				$query="UPDATE approval_history SET current_approval_status=0 WHERE entry_form=8 and mst_id in ($mst_id)";
				$rIDapp=$this->db->query($query,1);
				
				if($flag==1) 
				{
					if($rIDapp) $flag=1; else $flag=0; 
				}
				
					
				//$rID2=sql_insert("approval_history",$field_array,$data_array,0);
				$rID2=$this->insertData("APPROVAL_HISTORY",$data_arr_save);	
				if($flag==1) 
				{
					if($rID2) $flag=1; else $flag=0; 
				}
				
				if($flag==1) $msg='1'; else $msg='0';
			   return $msg;
					
		}//end else if condition;
			//Trims Booking Approval [without order].....................
			else if($page_id==336000001){
								  
				$user_id=$user_id_approval;
				$page_id=336;
				$mst_id=$app_id;
				$approval_type=0;
				$booking_no=return_field_value("booking_no","WO_NON_ORD_SAMP_BOOKING_MST","id=$mst_id and is_deleted=0");
				
				$user_sequence_no=return_field_value("sequence_no","electronic_approval_setup","page_id=$page_id and user_id=$user_id_approval and is_deleted=0");
				$min_sequence_no=return_field_value("min(sequence_no)","electronic_approval_setup","page_id=$page_id and is_deleted=0");
						
				//WithOut Order...............
		
				$is_not_last_user=return_field_value("sequence_no","electronic_approval_setup","company_id=$company_id and page_id=$page_id and sequence_no>$user_sequence_no and bypass=2 and is_deleted=0");
				
				$partial_approval = "";
				if($is_not_last_user != "")
				{
					// getting login in user's buyer id
					$loginUserBuyersArr = array();
					$loginUserBuyersSQL=sql_select("select (b.buyer_id || ',' ||  a.buyer_id) as BUYER_ID from user_passwd a, electronic_approval_setup b where a.id = b.user_id and b.company_id=$company_id and b.page_id=$page_id and b.user_id=$user_id_approval and b.is_deleted=0 group by b.buyer_id, a.buyer_id");
					foreach ($loginUserBuyersSQL as $key => $buyerID) {
						$loginUserBuyersArr[$buyerID->BUYER_ID] = $buyerID->BUYER_ID;
					}		
	
					

					$credentialUserBuyersArr = array();
					$sql = sql_select("select (b.buyer_id || ',' ||  a.buyer_id) as BUYER_ID from user_passwd a, electronic_approval_setup b where a.id = b.user_id and b.company_id=$company_id and b.page_id=$page_id and b.sequence_no>$user_sequence_no and b.is_deleted=0 group by b.buyer_id, a.buyer_id");
					foreach ($sql as $key => $buyerID) {
						$credentialUserBuyersArr[$buyerID->BUYER_ID] = $buyerID->BUYER_ID;
					}
					
					
					$isBuyerExist = array_intersect($loginUserBuyersArr,$credentialUserBuyersArr);
					//print_r($isBuyerExist);
					if(count($isBuyerExist) > 0)
					{
						$partial_approval=3;
					}			
					else
					{
						$partial_approval=1;
					}
					// echo $partial_approval;
					// die();
				}
				else
				{
					$partial_approval=1;
				}
	
	
				$field_array="id, entry_form, mst_id, approved_no, sequence_no, current_approval_status, approved_by, approved_date, inserted_by, insert_date"; 
				$id=return_next_id( "id","approval_history", 1 ) ;
					  
				$max_approved_no_arr = return_library_array("select mst_id, max(approved_no) as approved_no from approval_history where mst_id in($mst_id) and entry_form=8 group by mst_id","mst_id","approved_no");
					$approved_status_arr = return_library_array("select id, is_approved from wo_non_ord_samp_booking_mst where id in($mst_id)","id","is_approved");
				//print_r($max_approved_no_arr);
				
				
				$approved_no_array=array();
				$book_nos='';
				foreach(explode(",",$mst_id) as $val)
				{
				
					$approved_no=$max_approved_no_arr[$val];
					$approved_status=$approved_status_arr[$val];
					
					if($approved_status==0)
					{
						$approved_no=$approved_no+1;
						$approved_no_array[$booking_no]=$approved_no;
					}
					
					$data_arr_save=array(
						ID=>$id, 
						ENTRY_FORM=>8, 
						MST_ID=>$val, 
						APPROVED_NO=>$approved_no, 
						SEQUENCE_NO=>$user_sequence_no, 
						CURRENT_APPROVAL_STATUS=>1, 
						APPROVED_BY=>$user_id_approval, 
						APPROVED_DATE=>$pc_date_time, 
						USER_IP=>$user_ip
					);
					
					$id=$id+1;
				}
				//echo "insert into approval_history (".$field_array.") values ".$data_array;die;
				
				if(count($approved_no_array)>0)
				{
					$approved_string="";
					if($this->db->dbdriver == 'mysqli')
					{
						foreach($approved_no_array as $key=>$value)
						{
							$approved_string.=" WHEN '$key' THEN $value";
							$approved_string1.=" WHEN '$key' THEN $value";
						}
					}
					else
					{
						foreach($approved_no_array as $key=>$value)
						{
							$approved_string.=" WHEN TO_NCHAR('$key') THEN '".$value."'";
							$approved_string1.=" WHEN TO_CHAR('$key') THEN '".$value."'";
						}
					}
					
					$approved_string_mst="CASE booking_no ".$approved_string." END";
					$approved_string_dtls="CASE booking_no ".$approved_string." END";
					
					$sql_insert="insert into wo_non_ord_samp_bk_mst_his( id, approved_no, non_ord_samp_mst_id,booking_type, is_short, booking_no_prefix,booking_no_prefix_num, booking_no, company_id,buyer_id, job_no, po_break_down_id,item_category, fabric_source, currency_id, exchange_rate, pay_mode, source, booking_date, delivery_date, booking_month, booking_year, supplier_id, attention,is_deleted, status_active, inserted_by,insert_date, updated_by, update_date,is_approved, ready_to_approved, team_leader,dealing_marchant) 
						select	
						'', $approved_string_mst ,   id, booking_type, is_short,booking_no_prefix, booking_no_prefix_num, booking_no, 
				company_id, buyer_id, job_no,po_break_down_id, item_category, fabric_source, currency_id, exchange_rate, pay_mode,source, booking_date, delivery_date, 
				booking_month, booking_year, supplier_id,attention, is_deleted, status_active,inserted_by, insert_date, updated_by,update_date, is_approved, ready_to_approved,team_leader, dealing_marchant from wo_non_ord_samp_booking_mst where booking_no in ('$booking_no')";
						//echo $sql_insert;	
					
					//echo "insert into wo_booking_mst_hstry (".$field_array.") values ".$data_array;die;
					$sql_insert_dtls="insert into wo_non_ord_samp_bk_dtls_his( id, approved_no, non_ord_samp_dtls_id,booking_no, body_part, color_type_id, 
				lib_yarn_count_deter_id, construction, composition, fabric_description, gsm_weight, fabric_color,item_size, dia_width, finish_fabric, 
				process_loss, grey_fabric, rate,amount, yarn_breack_down, process_loss_method,inserted_by, insert_date, updated_by, update_date, status_active, is_deleted,style_id, style_des, sample_type, gmts_color, gmts_size, trim_group, uom, barnd_sup_ref, trim_qty,article_no, remarks, yarn_details,   body_type_id, item_qty, knitting_charge, rf_qty, bh_qty) 
						select	
						'', $approved_string_dtls, id, booking_no, body_part, color_type_id, lib_yarn_count_deter_id, construction,composition, fabric_description, gsm_weight,fabric_color, item_size, dia_width,finish_fabric, process_loss, grey_fabric,  rate, amount, yarn_breack_down,process_loss_method, inserted_by, insert_date,updated_by, update_date, status_active,is_deleted, style_id, style_des, sample_type, gmts_color, gmts_size, trim_group, uom, barnd_sup_ref, trim_qty, article_no, remarks,yarn_details, body_type_id, item_qty, knitting_charge, rf_qty, bh_qty from wo_non_ord_samp_booking_dtls where booking_no in ('$booking_no')";
							
						
					$rID3=$this->db->query($sql_insert,0);
					if($flag==1) 
					{
						if($rID3) $flag=1; else $flag=0; 
					} 
						
					$rID4=$this->db->query($sql_insert_dtls,1);
					if($flag==1) 
					{
						if($rID4) $flag=1; else $flag=0; 
					} 
					
					
				}
				
				//$rID=sql_multirow_update("wo_non_ord_samp_booking_mst","is_approved",1,"id",$booking_ids,0);
				$data_arr_up=array();
				$data_arr_up[IS_APPROVED]= 1;
				$rID=$this->updateData('WO_NON_ORD_SAMP_BOOKING_MST', $data_arr_up, array('ID' => $mst_id));
				if($rID) $flag=1; else $flag=0;
				
				
	
				$query="UPDATE approval_history SET current_approval_status=0 WHERE entry_form=8 and mst_id in ($mst_id)";
				$rIDapp=$this->db->query($query,1);
				if($flag==1) 
				{
					if($rIDapp) $flag=1; else $flag=0; 
				}
					
				//$rID2=sql_insert("approval_history",$field_array,$data_array,0);
				$rID2=$this->insertData("APPROVAL_HISTORY",$data_arr_save);
				if($flag==1) 
				{
					if($rID2) $flag=1; else $flag=0; 
				}
				
				if($flag==1) $msg='1'; else $msg='0';
			
				
				
				
			   return $msg;
					
		}//end else if condition;
			//Yarn Delivery Approval....................................
			else if($page_id==479){
								  
				$user_id=$user_id_approval;
				$page_id=479;
				$mst_id=$app_id;
				$approval_type=0;
					
					
										
				$user_sequence_no=return_field_value("sequence_no","electronic_approval_setup","page_id=$page_id and 
					user_id = $user_id_approval and is_deleted=0");
				$min_sequence_no=return_field_value("min(sequence_no)","electronic_approval_setup","page_id=$page_id and is_deleted=0");

				$is_not_last_user=return_field_value("sequence_no","electronic_approval_setup","company_id=$company_id and page_id=$page_id and sequence_no>$user_sequence_no and bypass=2 and is_deleted=0");
				
				if($is_not_last_user!="") {$partial_approval=3;} else {$partial_approval=1;}
				// echo $partial_approval;die;
		
				//$field_array="id, entry_form, mst_id, approved_no, sequence_no, current_approval_status, approved_by, approved_date,inserted_by,insert_date"; 
				$id=return_next_id( "id","approval_history", 1 ) ;
				
				$max_approved_no_arr = return_library_array("select mst_id, max(approved_no) as approved_no from approval_history where mst_id in($mst_id) and entry_form=14 group by mst_id","mst_id","approved_no");
				
				$approved_status_arr = return_library_array("select id, is_approved from inv_issue_master where id in($mst_id)","id","is_approved");
				
				$approved_no_array=array();
				$book_nos='';
				foreach(explode(",",$mst_id) as $val)
				{
					$approved_no=$max_approved_no_arr[$val];
					$approved_status=$approved_status_arr[$val];
					
					if($approved_status==0)
					{
						$approved_no=$approved_no+1;
						$approved_no_array[$val]=$approved_no;
						if($book_nos=="") $book_nos=$val; else $book_nos.=",".$val;
					}
					
					//if($data_array!="") $data_array.=",";
					
					//$data_array.="(".$id.",14,".$booking_id.",".$approved_no.",'".$user_sequence_no."',1,".$user_id_approval.",'".$pc_date_time."',".$user_id.",'".$pc_date_time."')"; 
					$data_arr_save=array(
						ID=>$id, 
						ENTRY_FORM=>14, 
						MST_ID=>$val, 
						APPROVED_NO=>$approved_no, 
						SEQUENCE_NO=>$user_sequence_no, 
						CURRENT_APPROVAL_STATUS=>1, 
						APPROVED_BY=>$user_id_approval, 
						APPROVED_DATE=>$pc_date_time, 
						USER_IP=>$user_ip
					);
						
					$id=$id+1;
					
				}
				
				if(count($approved_no_array)>0)
				{
					
					$approved_string="";
					
					foreach($approved_no_array as $key=>$value)
					{
						$approved_string.=" WHEN ".str_replace("'","",$key)." THEN $value";
					}
					
					$approved_string_mst="CASE id ".$approved_string." END";
					$approved_string_dtls="CASE mst_id ".$approved_string." END";
					//$approved_string_dtls_ppropor="CASE mst_id ".$approved_string." END";
					
					$sql_insert="insert into  inv_issue_master_history(id,approve_no,issue_id,issue_number_prefix,issue_number_prefix_num,issue_number,issue_basis,issue_purpose,entry_form,item_category,company_id,location_id,supplier_id,store_id,buyer_id,buyer_job_no,style_ref,booking_id,booking_no,req_no,batch_no,issue_date,sample_type,knit_dye_source,knit_dye_company,challan_no,loan_party,lap_dip_no,gate_pass_no,item_color,color_range,remarks,received_id,received_mrr_no,other_party,order_id,is_approved,inserted_by,insert_date,updated_by,update_date,status_active,is_deleted) 
						select	
						'', $approved_string_mst, id,issue_number_prefix,issue_number_prefix_num,issue_number,issue_basis,issue_purpose,entry_form,item_category,company_id,location_id,supplier_id,store_id,buyer_id,buyer_job_no,style_ref,booking_id,booking_no,req_no,batch_no,issue_date,sample_type,knit_dye_source,knit_dye_company,challan_no,loan_party,lap_dip_no,gate_pass_no,item_color,color_range,remarks,received_id,received_mrr_no,other_party,order_id,is_approved,inserted_by,insert_date,updated_by,update_date,status_active,is_deleted from  inv_issue_master where id in ($book_nos)";
					//echo $sql_insert;die;		
				
					$sql_insert_dtls="insert into  inv_transaction_history (id, approve_no, transaction_id, mst_id, requisition_no, receive_basis,pi_wo_batch_no,company_id,supplier_id,prod_id,product_code,item_category,
					transaction_type,transaction_date,store_id,order_id,brand_id,order_uom,order_qnty,order_rate,order_ile,order_ile_cost,
					order_amount,cons_uom,cons_quantity,return_qnty,cons_reject_qnty,cons_rate,cons_ile,cons_ile_cost,cons_amount,balance_qnty,
					balance_amount,floor_id,machine_id,machine_category,no_of_bags,cone_per_bag,weight_per_bag,weight_per_cone,room,rack,self,bin_box,
					expire_date,dyeing_sub_process,issue_challan_no,remarks,batch_lot,location_id,department_id,section_id,job_no,dyeing_color_id,inserted_by,
					insert_date,updated_by,update_date,status_active,is_deleted) 
						select	
						'', $approved_string_dtls, id, mst_id, requisition_no, receive_basis,pi_wo_batch_no,company_id,supplier_id,prod_id,product_code,item_category,transaction_type,transaction_date,store_id,
					order_id,brand_id,order_uom,order_qnty,order_rate,order_ile,order_ile_cost,order_amount,cons_uom,cons_quantity,return_qnty,cons_reject_qnty,cons_rate,
					cons_ile,cons_ile_cost,cons_amount,balance_qnty,balance_amount,floor_id,machine_id,machine_category,no_of_bags,cone_per_bag,weight_per_bag,weight_per_cone,
					room,rack,self,bin_box,expire_date,dyeing_sub_process,issue_challan_no,remarks,batch_lot,location_id,department_id,section_id,job_no,dyeing_color_id,
					inserted_by,insert_date,updated_by,update_date,status_active,is_deleted   from inv_transaction where mst_id in ($book_nos) and transaction_type=2  and status_active=1";
								
					
					if($this->db->dbdriver == 'mysqli')
					{
						$trans_id=return_field_value("group_concat(distinct id) as id"," inv_transaction","mst_id in($book_nos) and transaction_type=2 and status_active=1","id");
					}
					else
					{
						$trans_id=return_field_value("LISTAGG(id, ',') WITHIN GROUP (ORDER BY id) as id","inv_transaction","mst_id in($book_nos) and transaction_type=2 and status_active=1","id");
					}					
					
		
					
					$sql_insert_dtls_propor="insert into  order_wise_pro_detail_history (id,approve_no, proportionate_id,trans_id,trans_type,entry_form,dtls_id,po_breakdown_id,prod_id,color_id,quantity,issue_purpose,returnable_qnty,inserted_by,insert_date,updated_by,update_date,status_active,is_deleted) 
						select	
						'', 1, id,trans_id,trans_type,entry_form,dtls_id,po_breakdown_id,prod_id,color_id,quantity,issue_purpose,returnable_qnty,inserted_by,insert_date,updated_by,update_date,status_active,is_deleted  from order_wise_pro_details where trans_id in ($trans_id)";
					//echo $sql_insert_dtls_propor;die;	
						
				}
				//$data = $partial_approval."*".$user_id."*'".$pc_date_time."'";
				//$rID=sql_multirow_update("inv_issue_master","is_approved*approved_by*approved_date",$data,"id",$booking_ids,1); 
		
				
				 //return 1;
				
				$data_arr_up=array();
				$data_arr_up=array(
					IS_APPROVED=> $partial_approval,
					APPROVED_BY=>$user_id,
					APPROVED_DATE=>$pc_date_time
				);
				$rID=$this->updateData('INV_ISSUE_MASTER', $data_arr_up, array('ID' => $mst_id));
				if($rID) $flag=1; else $flag=0;
		
				if($mst_id!="")
				{
					//$rIDapp=sql_multirow_update("approval_history","current_approval_status",0,"id",$approval_ids,1);
					
					$data_arr_up=array();
					$data_arr_up=array(
						CURRENT_APPROVAL_STATUS=> 0,
					);
					$rIDapp=$this->updateData('APPROVAL_HISTORY', $data_arr_up, array('ID' => $mst_id,'ENTRY_FORM' => 14));
					if($flag==1) 
					{
						if($rIDapp) $flag=1; else $flag=0; 
					} 
				}
				//$rID2=sql_insert("approval_history",$field_array,$data_array,1);
				$rID2=$this->insertData("APPROVAL_HISTORY",$data_arr_save);	
				if($flag==1) 
				{
					if($rID2) $flag=1; else $flag=0; 
				}
				$rID3=$this->db->query($sql_insert,1);
				if($flag==1) 
				{
					if($rID3) $flag=1; else $flag=0; 
				}
				
				$rID4=$this->db->query($sql_insert_dtls,1);
				if($flag==1) 
				{
					if($rID4) $flag=1; else $flag=0; 
				}
					
				$rID5=$this->db->query($sql_insert_dtls_propor,1);
				if($flag==1) 
				{
					if($rID5) $flag=1; else $flag=0; 
				}
				if($flag==1) $msg=1; else $msg=0;
			   return $msg;
					
		  }//end else if condition;
			//Stationary Work Order Approval....................................
			else if($page_id==627){
								  
				$user_id=$user_id_approval;
				$page_id=627;
				$mst_id=$app_id;
				$approval_type=0;
					
					
										
				$user_sequence_no=return_field_value("sequence_no","electronic_approval_setup","page_id=$page_id and 
					user_id = $user_id_approval and is_deleted=0");
				$min_sequence_no=return_field_value("min(sequence_no)","electronic_approval_setup","page_id=$page_id and is_deleted=0");

				$is_not_last_user=return_field_value("sequence_no","electronic_approval_setup","company_id=$company_id and page_id=$page_id and sequence_no>$user_sequence_no and bypass=2 and is_deleted=0");

				if($is_not_last_user!="") $partial_approval=3; else $partial_approval=1;
				
				//$field_array="id, entry_form, mst_id, approved_no, sequence_no, current_approval_status, approved_by, approved_date, inserted_by, insert_date";
				$i=0;
				$id=return_next_id( "id","approval_history", 1 ) ;
				
				$approved_no_array=array();
				
				foreach(explode(",",$mst_id) as $val)
				{
					$approved_no=return_field_value("max(approved_no) as approved_no","approval_history","mst_id='$val' and entry_form=5","approved_no");
					$approved_no=$approved_no+1;
				
					//if($i!=0) $data_array.=",";
					//$data_array.="(".$id.",5,".$val.",".$approved_no.",'".$user_sequence_no."',1,".$user_id_approval.",'".$pc_date_time."',".$user_id.",'".$pc_date_time."')";
					$data_arr_save=array(
						ID=>$id, 
						ENTRY_FORM=>5, 
						MST_ID=>$val, 
						APPROVED_NO=>$approved_no, 
						SEQUENCE_NO=>$user_sequence_no, 
						CURRENT_APPROVAL_STATUS=>1, 
						APPROVED_BY=>$user_id_approval, 
						APPROVED_DATE=>$pc_date_time, 
						USER_IP=>$user_ip
					);
					
					$approved_no_array[$val]=$approved_no;
						
					$id=$id+1;
					$i++;
				}
			 
				
				$approved_string="";
				
				foreach($approved_no_array as $key=>$value)
				{
					$approved_string.=" WHEN $key THEN $value";
				}
				
				$approved_string_mst="CASE id ".$approved_string." END";
				$approved_string_dtls="CASE mst_id ".$approved_string." END";
				
				$sql_insert="INSERT into wo_non_order_info_mst_history(id, mst_id, approved_no, garments_nature, wo_number_prefix, wo_number_prefix_num, wo_number, company_name, buyer_po, requisition_no, delivery_place, wo_date, supplier_id, attention, wo_basis_id, item_category, currency_id, delivery_date, source, pay_mode, terms_and_condition, is_approved, approved_by, status_active, is_deleted, inserted_by,insert_date,updated_by,update_date) 
				SELECT  
				'', id, $approved_string_mst, garments_nature, wo_number_prefix, wo_number_prefix_num, wo_number, company_name, buyer_po, requisition_no, delivery_place, wo_date, supplier_id, attention, wo_basis_id, item_category, currency_id, delivery_date, source, pay_mode, terms_and_condition, is_approved, approved_by, status_active, is_deleted, inserted_by,insert_date,updated_by,update_date from  wo_non_order_info_mst where id in ($mst_id)";
						
			
				
				$sql_insert_dtls="INSERT into wo_non_order_info_dtls_history(id, approved_no, mst_id, requisition_dtls_id, po_breakdown_id, requisition_no, item_id, yarn_count, yarn_comp_type1st, yarn_comp_percent1st, yarn_comp_type2nd, yarn_comp_percent2nd, yarn_type, color_name, req_quantity, supplier_order_quantity, uom,rate,amount,inserted_by,insert_date,updated_by,update_date,status_active,is_deleted) 
				SELECT  
				'', $approved_string_dtls, mst_id, requisition_dtls_id, po_breakdown_id, requisition_no, item_id, yarn_count, yarn_comp_type1st, yarn_comp_percent1st, yarn_comp_type2nd, yarn_comp_percent2nd, yarn_type, color_name, req_quantity, supplier_order_quantity, uom,rate,amount,inserted_by,insert_date,updated_by,update_date,status_active,is_deleted from wo_non_order_info_dtls where mst_id in ($mst_id)";
				
				//$rID=sql_multirow_update("wo_non_order_info_mst","is_approved",$partial_approval,"id",$req_nos,0);    
				$data_arr_up=array();
				$data_arr_up[IS_APPROVED]= $partial_approval;
				$rID=$this->updateData('WO_NON_ORDER_INFO_MST', $data_arr_up, array('ID' => $mst_id));
				if($rID) $flag=1; else $flag=0;
		
				$query="UPDATE approval_history SET current_approval_status=0 WHERE entry_form=5 and mst_id in ($mst_id)";
				$rIDapp=$this->db->query($query,1);
				if($flag==1) 
				{
					if($rIDapp) $flag=1; else $flag=0; 
				} 
		
			
				//$rID2=sql_insert("approval_history",$field_array,$data_array,0);
				$rID2=$this->insertData("APPROVAL_HISTORY",$data_arr_save);	
				if($flag==1) 
				{
					if($rID2) $flag=1; else $flag=0; 
					
				}
				$rID3=$this->db->query($sql_insert,0);
				if($flag==1) 
				{
					if($rID3) $flag=1; else $flag=0; 
					
				}       
				$rID4=$this->db->query($sql_insert_dtls,1);
				if($flag==1) 
				{
					if($rID4) $flag=1; else $flag=0; 
					
				} 
				
				if($flag==1) $msg=1; else $msg=0;
			
			   return $msg;
					
		  }//end else if condition;
			//Other Purchase WO Approval....................................
			else if($page_id==628){
								  
				$user_id=$user_id_approval;
				$page_id=628;
				$mst_id=$app_id;
				$approval_type=0;
					
					
										
				$user_sequence_no=return_field_value("sequence_no","electronic_approval_setup","page_id=$page_id and 
					user_id = $user_id_approval and is_deleted=0");
				$min_sequence_no=return_field_value("min(sequence_no)","electronic_approval_setup","page_id=$page_id and is_deleted=0");

				$is_not_last_user=return_field_value("sequence_no","electronic_approval_setup","company_id=$company_id and page_id=$page_id and sequence_no>$user_sequence_no and bypass=2 and is_deleted=0");
				if($is_not_last_user!="") $partial_approval=3; else $partial_approval=1;
		
 		
				//$field_array="id, entry_form, mst_id, approved_no, sequence_no, current_approval_status, approved_by, approved_date";
				
				$i=0;
				$id=return_next_id( "id","approval_history", 1 ) ;
				
				$approved_no_array=array();
				foreach(explode(",",$mst_id) as $val)
				{
					$approved_no=return_field_value("max(approved_no) as approved_no","approval_history","mst_id='$val' and entry_form=17","approved_no");
					$approved_no=$approved_no+1;
				
					//if($i!=0) $data_array.=",";
					 
					//$data_array.="(".$id.",17,".$val.",".$approved_no.",".$user_sequence_no.",1,".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."')"; 
					$data_arr_save=array(
						ID=>$id, 
						ENTRY_FORM=>17, 
						MST_ID=>$val, 
						APPROVED_NO=>$approved_no, 
						SEQUENCE_NO=>$user_sequence_no, 
						CURRENT_APPROVAL_STATUS=>1, 
						APPROVED_BY=>$user_id_approval, 
						APPROVED_DATE=>$pc_date_time, 
						USER_IP=>$user_ip
					);           
					
					$approved_no_array[$val]=$approved_no;
						
					$id=$id+1;
					$i++;
				}
				
				
		
				$approved_string="";
			
				foreach($approved_no_array as $key=>$value)
				{
					$approved_string.=" WHEN $key THEN $value";
				}
				
				$approved_string_mst="CASE id ".$approved_string." END";
				$approved_string_dtls="CASE mst_id ".$approved_string." END";
				
				$sql_insert="insert into wo_non_order_info_mst_history(id, mst_id, approved_no, garments_nature, wo_number_prefix, wo_number_prefix_num, wo_number, company_name, buyer_po, requisition_no, delivery_place, wo_date, supplier_id, attention, wo_basis_id, item_category, currency_id, delivery_date, source, pay_mode, terms_and_condition, is_approved, approved_by, status_active, is_deleted, inserted_by,insert_date,updated_by,update_date)
					select
					'', id, $approved_string_mst, garments_nature, wo_number_prefix, wo_number_prefix_num, wo_number, company_name, buyer_po, requisition_no, delivery_place, wo_date, supplier_id, attention, wo_basis_id, item_category, currency_id, delivery_date, source, pay_mode, terms_and_condition, is_approved, approved_by, status_active, is_deleted, inserted_by,insert_date,updated_by,update_date from  wo_non_order_info_mst where id in ($mst_id)";
		
		
		
				 $sql_insert_dtls="insert into wo_non_order_info_dtls_history(id, approved_no, mst_id, requisition_dtls_id, po_breakdown_id, requisition_no, item_id, yarn_count, yarn_comp_type1st, yarn_comp_percent1st, yarn_comp_type2nd, yarn_comp_percent2nd, yarn_type, color_name, req_quantity, supplier_order_quantity, uom,rate,amount,inserted_by,insert_date,updated_by,update_date,status_active,is_deleted)
					select
					'', $approved_string_dtls, mst_id, requisition_dtls_id, po_breakdown_id, requisition_no, item_id, yarn_count, yarn_comp_type1st, yarn_comp_percent1st, yarn_comp_type2nd, yarn_comp_percent2nd, yarn_type, color_name, req_quantity, supplier_order_quantity, uom,rate,amount,inserted_by,insert_date,updated_by,update_date,status_active,is_deleted from wo_non_order_info_dtls where mst_id in ($mst_id)";
						
		
				//$rID=sql_multirow_update("wo_non_order_info_mst","is_approved",$partial_approval,"id",$req_nos,0);    
				$data_arr_up=array();
				$data_arr_up[IS_APPROVED]= $partial_approval;
				$rID=$this->updateData('WO_NON_ORDER_INFO_MST', $data_arr_up, array('ID' => $mst_id));
				if($rID) $flag=1; else $flag=0;
		
				$query="UPDATE approval_history SET current_approval_status=0 WHERE entry_form=17 and mst_id in ($mst_id)";
				$rIDapp=$this->db->query($query,1);
				if($flag==1) 
				{
					if($rIDapp) $flag=1; else $flag=0; 
				}
		
				//$rID2=sql_insert("approval_history",$field_array,$data_array,0);
				$rID2=$this->insertData("APPROVAL_HISTORY",$data_arr_save);
				if($flag==1) 
				{
					if($rID2) $flag=1; else $flag=0; 
					
				}
				$rID3=$this->db->query($sql_insert,0);
				if($flag==1) 
				{
					if($rID3) $flag=1; else $flag=0; 
					
				}       
				$rID4=$this->db->query($sql_insert_dtls,1);
				if($flag==1) 
				{
					if($rID4) $flag=1; else $flag=0; 
					
				} 
				
				if($flag==1) $msg=1; else $msg=0; 
			
			
			   return $msg;
					
		  }//end else if condition;
			//Yarn Work Order Approval New....................................
			else if($page_id==412){
								  
				$user_id=$user_id_approval;
				$page_id=412;
				$mst_id=$app_id;
				$approval_type=0;
					
					
										
				$user_sequence_no=return_field_value("sequence_no","electronic_approval_setup","page_id=$page_id and 
					user_id = $user_id_approval and is_deleted=0");
				$min_sequence_no=return_field_value("min(sequence_no)","electronic_approval_setup","page_id=$page_id and is_deleted=0");

						
		
				$is_not_last_user=return_field_value("sequence_no","electronic_approval_setup","company_id=$company_id and page_id=$page_id and sequence_no>$user_sequence_no and bypass=2 and is_deleted=0","sequence_no");
				
				if($is_not_last_user ==""){
					$partial_approval=1;
				}else{
					$partial_approval=3;
				}
		
				//$rID=sql_multirow_update("wo_non_order_info_mst","is_approved","$partial_approval","id",$req_nos,0);
				$data_arr_up=array();
				$data_arr_up[IS_APPROVED]= $partial_approval;
				$rID=$this->updateData('WO_NON_ORDER_INFO_MST', $data_arr_up, array('ID' => $mst_id));

		
				if($rID) $flag=1; else $flag=0;
		
		
				 
				//$field_array="id, entry_form, mst_id, approved_no, approved_by, approved_date, sequence_no, current_approval_status, inserted_by, insert_date";
				$i=0;
				$id=return_next_id( "id","approval_history", 1 ) ;
		
				$approved_no_array=array();
		
				foreach(explode(",",$mst_id) as $val)
				{
					$approved_no=return_field_value("max(approved_no)","approval_history","mst_id='$val'");
					$approved_no=$approved_no+1;
		
					//if($i!=0) $data_array.=",";
		
					//$data_array.="(".$id.",2,".$val.",".$approved_no.",".$user_id_approval.",'".$pc_date_time."',".$user_sequence_no.",1,".$user_id.",'".$pc_date_time."')";
					$data_arr_save=array(
						ID=>$id, 
						ENTRY_FORM=>2, 
						MST_ID=>$val, 
						APPROVED_NO=>$approved_no, 
						SEQUENCE_NO=>$user_sequence_no, 
						CURRENT_APPROVAL_STATUS=>1, 
						APPROVED_BY=>$user_id_approval, 
						APPROVED_DATE=>$pc_date_time, 
						USER_IP=>$user_ip
					);
		
					$approved_no_array[$val]=$approved_no;
		
					$id=$id+1;
					$i++;
				}
		
		
				$approved_string="";
		
				foreach($approved_no_array as $key=>$value)
				{
					$approved_string.=" WHEN $key THEN $value";
				}
		
				$approved_string_mst="CASE id ".$approved_string." END";
				$approved_string_dtls="CASE mst_id ".$approved_string." END";
		
				$sql_insert="insert into wo_non_order_info_mst_history(id, mst_id, approved_no, garments_nature, wo_number_prefix, wo_number_prefix_num, wo_number, company_name, buyer_po, requisition_no, delivery_place, wo_date, supplier_id, attention, wo_basis_id, item_category, currency_id, delivery_date, source, pay_mode, terms_and_condition, is_approved, approved_by, status_active, is_deleted, inserted_by,insert_date,updated_by,update_date)
					select
					'', id, $approved_string_mst, garments_nature, wo_number_prefix, wo_number_prefix_num, wo_number, company_name, buyer_po, requisition_no, delivery_place, wo_date, supplier_id, attention, wo_basis_id, item_category, currency_id, delivery_date, source, pay_mode, terms_and_condition, is_approved, approved_by, status_active, is_deleted, inserted_by,insert_date,updated_by,update_date from  wo_non_order_info_mst where id in ($mst_id)";
		
				$rID3=$this->db->query($sql_insert);
		
				if($flag==1)
				{
					if($rID3) $flag=1; else $flag=0;
				}
		
				$sql_insert_dtls="insert into wo_non_order_info_dtls_history(id, approved_no, mst_id, requisition_dtls_id, po_breakdown_id, requisition_no, item_id, yarn_count, yarn_comp_type1st, yarn_comp_percent1st, yarn_comp_type2nd, yarn_comp_percent2nd, yarn_type, color_name, req_quantity, supplier_order_quantity, uom,rate,amount,inserted_by,insert_date,updated_by,update_date,status_active,is_deleted)
					select
					'', $approved_string_dtls, mst_id, requisition_dtls_id, po_breakdown_id, requisition_no, item_id, yarn_count, yarn_comp_type1st, yarn_comp_percent1st, yarn_comp_type2nd, yarn_comp_percent2nd, yarn_type, color_name, req_quantity, supplier_order_quantity, uom,rate,amount,inserted_by,insert_date,updated_by,update_date,status_active,is_deleted from wo_non_order_info_dtls where mst_id in ($mst_id)";
		
				$rID4=$this->db->query($sql_insert_dtls);
		
				if($flag==1)
				{
					if($rID4) $flag=1; else $flag=0;
				}
		
				$query="UPDATE approval_history SET current_approval_status=0 WHERE entry_form=2 and mst_id=$mst_id";
				$rIDapp=$this->db->query($query);
				if($flag==1)
				{
					if($rIDapp) $flag=1; else $flag=0;
				}
		
				//$rID2=sql_insert("approval_history",$field_array,$data_array,0);
				$rID2=$this->insertData("APPROVAL_HISTORY",$data_arr_save);	
		
				if($flag==1)
				{
					if($rID2) $flag=1; else $flag=0;
				}
				
				if($flag==1) $msg=1; else $msg=0; 
			
			
			   return $msg;
					
		  }//end else if condition;
			//Transfer Requisition Approval....................................
			else if($page_id==1630){
								  
				$user_id=$user_id_approval;
				$page_id=1630;
				$mst_id=$app_id;
				$approval_type=0;
										
				$user_sequence_no=return_field_value("sequence_no","electronic_approval_setup","page_id=$page_id and 
					user_id = $user_id_approval and is_deleted=0");
				$min_sequence_no=return_field_value("min(sequence_no)","electronic_approval_setup","page_id=$page_id and is_deleted=0");

				$is_not_last_user=return_field_value("sequence_no","electronic_approval_setup","company_id=$company_id and page_id=$page_id and sequence_no>$user_sequence_no and bypass=2 and is_deleted=0");
				// echo $is_not_last_user;die;
		
				if($is_not_last_user!="") $partial_approval=3; else $partial_approval=1;
				// echo $partial_approval;die;
		
				$max_approved_no_arr = return_library_array("SELECT mst_id, max(approved_no) as approved_no from approval_history where mst_id in($mst_id) and entry_form=37 group by mst_id","mst_id","approved_no");
				
				$approved_status_arr = return_library_array("SELECT id, is_approved from inv_item_transfer_requ_mst where id in($mst_id)","id","is_approved");
		
				$field_array="id, entry_form, mst_id, approved_no, sequence_no, current_approval_status, approved_by, approved_date, inserted_by, insert_date"; 
				$id=return_next_id( "id","approval_history", 1 ) ;
		
				 
				
				// ======================================================================== New
				foreach(explode(",",$mst_id) as $val)
				{
					 
					$approved_no=$max_approved_no_arr[$val];
					$approved_status=$approved_status_arr[$val];
					
					if($approved_status==0)
					{
						$approved_no=$approved_no+1;
						$approved_no_array[$val]=$approved_no;
						if($book_nos=="") $book_nos=$val; else $book_nos.=",".$val;
					}
					
					//if($data_array!="") $data_array.=",";
					
					//$data_array.="(".$id.",37,".$booking_id.",".$approved_no.",'".$user_sequence_no."',1,".$user_id_approval.",'".$pc_date_time."',".$user_id.",'".$pc_date_time."')"; 
					$data_arr_save=array(
						ID=>$id, 
						ENTRY_FORM=>37, 
						MST_ID=>$val, 
						APPROVED_NO=>$approved_no, 
						SEQUENCE_NO=>$user_sequence_no, 
						CURRENT_APPROVAL_STATUS=>1, 
						APPROVED_BY=>$user_id_approval, 
						APPROVED_DATE=>$pc_date_time, 
						USER_IP=>$user_ip
					);
						
					$id=$id+1;
					
				}
		
				//$data = $partial_approval."*".$user_id."*'".$pc_date_time."'";
				//$rID=sql_multirow_update("inv_item_transfer_requ_mst","is_approved",$partial_approval,"id",$booking_ids,1);
				
				
				$data_arr_up=array();
				$data_arr_up[IS_APPROVED]= $partial_approval;
				$rID=$this->updateData('INV_ITEM_TRANSFER_REQU_MST', $data_arr_up, array('ID' => $mst_id));
				if($rID) $flag=1; else $flag=0;
		
				if($mst_id!="")
				{
					//$rIDapp=sql_multirow_update("approval_history","current_approval_status",0,"id",$approval_ids,1);
					$data_arr_up=array();
					$data_arr_up[CURRENT_APPROVAL_STATUS]= 0;
					$rIDapp=$this->updateData('APPROVAL_HISTORY', $data_arr_up, array('MST_ID' => $mst_id,'ENTRY_FORM'=>37));
					if($flag==1) 
					{
						if($rIDapp) $flag=1; else $flag=0; 
					} 
				}
				//$rID2=sql_insert("approval_history",$field_array,$data_array,1);
				$rID2=$this->insertData("APPROVAL_HISTORY",$data_arr_save);	
				//echo $rID2;return;
				if($flag==1) 
				{
					if($rID2) $flag=1; else $flag=0; 
				}
		
				if($flag==1) $msg=1; else $msg=0; 
			
			
			   return $msg;
					
		  }//end else if condition;
			//Import Document Acceptance Approval....................................
			else if($page_id==1684){
								  
				$user_id=$user_id_approval;
				$page_id=1684;
				$mst_id=$app_id;
				$approval_type=0;
						
				
				$user_sequence_no=return_field_value("sequence_no","electronic_approval_setup","company_id=$company_id and page_id=$page_id and user_id=$user_id_approval and is_deleted=0");
				$max_approved_no_arr = return_library_array("select mst_id, max(approved_no) as approved_no from approval_history where mst_id=$mst_id and entry_form=38 group by mst_id","mst_id","approved_no");	
				$approved_status_arr = return_library_array("select id, approved from com_pi_master_details where id =$mst_id","id","approved");
		
				//$field_array = "id, entry_form, mst_id, approved_no,sequence_no, current_approval_status, approved_by, approved_date, user_ip,comments,inserted_by,insert_date"; 
				$id = return_next_id( "id","APPROVAL_HISTORY", 1 ) ;
				
						
		
				$approved_no_array = array();
				$book_nos = '';
				foreach(explode(",",$mst_id) as $invoice_id)
				{
					$approved_no = $max_approved_no_arr[$invoice_id];
					$approved_status = $approved_status_arr[$invoice_id];
		
					if($approved_status==0)
					{
						$approved_no = $approved_no+1;
					}
					 
					
					$data_arr_save=array(
						ID=>$id, 
						ENTRY_FORM=>38, 
						MST_ID=>$invoice_id, 
						APPROVED_NO=>$approved_no, 
						SEQUENCE_NO=>$user_sequence_no, 
						CURRENT_APPROVAL_STATUS=>1, 
						APPROVED_BY=>$user_id_approval, 
						APPROVED_DATE=>$pc_date_time, 
						USER_IP=>$user_ip
					);
					 
				}
		
				$query="UPDATE APPROVAL_HISTORY SET current_approval_status=0 WHERE entry_form=38 and mst_id=$mst_id";
				$rIDapp=$this->db->query($query);
				if($rIDapp) $flag=1; else $flag=0; 
				
				//$rID2 = sql_insert("approval_history",$field_array,$data_array,0);
				$rID2=$this->insertData("APPROVAL_HISTORY",$data_arr_save);	
				if($flag==1) 
				{
					if($rID2) $flag=1; else $flag=0; 
				} 
				
				if($flag==1) $msg=1; else $msg=0; 
			
			
			   return $msg;
					
		  }//end else if condition;
		
		
		
		
			//Embellishment Work Order Approval V2....................................
			else if($page_id==1257){
								  
				$user_id=$user_id_approval;
				$page_id=1257;
				$booking_ids=$app_id;
				$approval_type=0;
						
				
				$buyer_arr=return_library_array( "select id, buyer_id  from wo_booking_mst where id=$booking_ids", "id", "buyer_id"  );
				$booking_arr=return_library_array( "select id, booking_no  from wo_booking_mst where id=$booking_ids", "id", "booking_no"  );
				
				
				$user_sequence_no=return_field_value("sequence_no","electronic_approval_setup","company_id=$company_id and page_id=$page_id and user_id=$page_id and is_deleted=0");
				$min_sequence_no=return_field_value("min(sequence_no)","electronic_approval_setup","company_id=$company_id and page_id=$page_id and is_deleted=0");
				
				$user_sequence_no=$user_sequence_no*1;
				$min_sequence_no=$min_sequence_no*1;
				
				
				if($this->db->dbdriver != 'mysqli') {$buyer_id_cond=" and a.buyer_id is null "; $buyer_id_cond2=" and b.buyer_id is not null ";}
				else{$buyer_id_cond=" and a.buyer_id='' "; $buyer_id_cond2=" and b.buyer_id!='' ";}
				
				
				$is_not_last_user=return_field_value("a.sequence_no as sequence_no","electronic_approval_setup a"," a.company_id=$company_id and a.page_id=$page_id and a.sequence_no>$user_sequence_no and a.bypass=2 and a.is_deleted=0 $buyer_id_cond","sequence_no");
				
				
				$partial_approval = "";
				if($is_not_last_user == "")
				{
					$sql = sql_select("select (b.buyer_id) as BUYER_ID from electronic_approval_setup b where b.company_id=$company_id and b.page_id=$page_id and b.sequence_no>$user_sequence_no and b.is_deleted=0 and b.bypass=2  group by b.buyer_id");
					foreach ($sql as $key => $buyerID) {
						$credentialUserBuyersArr[$buyerID->BUYER_ID] = $buyerID->BUYER_ID;
					}
					
				}
				else
				{
					$check_user_buyer = sql_select("select b.user_id as user_id from user_passwd a, electronic_approval_setup b where a.id = b.user_id and b.company_id=$company_id and b.page_id=$page_id and b.sequence_no>$user_sequence_no and b.is_deleted=0 and b.bypass=2 $buyer_id_cond  group by b.user_id");
					if(count($check_user_buyer)==0)
					{
						
						$sql = sql_select("select b.buyer_id as BUYER_ID from user_passwd b, electronic_approval_setup a where b.id = a.user_id and a.company_id=$company_id and a.page_id=$page_id and b.sequence_no>$user_sequence_no and a.is_deleted=0 and a.bypass=2 $buyer_id_cond $buyer_id_cond2 group by b.buyer_id");
						foreach ($sql as $key => $buyerID) {
							$credentialUserBuyersArr[$buyerID->BUYER_ID] = $buyerID->BUYER_ID;
						}
						
						$sql = sql_select("select (b.buyer_id) as BUYER_ID from electronic_approval_setup b where b.company_id=$company_id and b.page_id=$page_id and b.sequence_no>$user_sequence_no and b.is_deleted=0 and b.bypass=2 $buyer_id_cond2  group by b.buyer_id");
						foreach ($sql as $key => $buyerID) {
							$credentialUserBuyersArr[$buyerID->BUYER_ID] = $buyerID->BUYER_ID;
						}
						
					}
					//print_r($credentialUserBuyersArr);die;
				}
				
		
				$field_array="id, entry_form, mst_id, approved_no, sequence_no, current_approval_status, approved_by, approved_date, user_ip,comments,inserted_by,insert_date"; 
				$id=return_next_id( "id","approval_history", 1 ) ;
				
				$max_approved_no_arr = return_library_array("select mst_id, max(approved_no) as approved_no from approval_history where mst_id in($booking_ids) and entry_form=32 group by mst_id","mst_id","approved_no");
				
				$approved_status_arr = return_library_array("select id, is_approved from wo_booking_mst where id in($booking_ids)","id","is_approved");
		
				$approved_no_array=array();
				$book_nos='';
				
				foreach(explode(",",$booking_ids) as $booking_id)
				{
					$approved_no=$max_approved_no_arr[$booking_id];
					$approved_status=$approved_status_arr[$booking_id];
					$buyer_id=$buyer_arr[$booking_id];
					if($approved_status==0)
					{
						$approved_no=$approved_no+1;
						$approved_no_array[$booking_arr[$booking_id]]=$approved_no;
						if($book_nos=="") $book_nos=$booking_arr[$booking_id]; else $book_nos.=",".$booking_arr[$booking_id];
					}
					//echo "20**";
					if($is_not_last_user == "")
					{
						if(in_array($buyer_id,$credentialUserBuyersArr))
						{
							$partial_approval=3;
						}			
						else
						{
							$partial_approval=1;
						}
					}
					else
					{
						if(count($credentialUserBuyersArr)>0)
						{
							if(in_array($buyer_id,$credentialUserBuyersArr))
							{
								$partial_approval=3;
							}			
							else
							{
								$partial_approval=1;
							}
						}
						else
						{
							$partial_approval=1;
						}
					}

					//$booking_id_arr[]=$booking_id;
					//$data_array_booking_update[$booking_id]=explode("*",($partial_approval));
					
					//if($data_array!="") $data_array.=",";
					//$data_array.="(".$id.",32,".$booking_id.",".$approved_no.",'".$user_sequence_no."',1,".$user_id_approval.",'".$pc_date_time."','".$user_ip."',".$app_instru.",".$user_id.",'".$pc_date_time."')"; 
					
					
					$data_arr_save=array(
						ID=>$id, 
						ENTRY_FORM=>32, 
						MST_ID=>$booking_id, 
						APPROVED_NO=>$approved_no, 
						SEQUENCE_NO=>$user_sequence_no, 
						CURRENT_APPROVAL_STATUS=>1, 
						APPROVED_BY=>$user_id_approval, 
						APPROVED_DATE=>$pc_date_time, 
						USER_IP=>$user_ip
					);
					
					
					$id=$id+1;
					
				}

				if(count($approved_no_array)>0)
				{
					$approved_string="";
					
					if($this->db->dbdriver == 'mysqli')
					{
						foreach($approved_no_array as $key=>$value)
						{
							$approved_string.=" WHEN '$key' THEN $value";
						}
					}
					else
					{
						foreach($approved_no_array as $key=>$value)
						{
							$approved_string.=" WHEN TO_NCHAR('$key') THEN '".$value."'";
						}
					}
					
					$approved_string_mst="CASE booking_no ".$approved_string." END";
					$approved_string_dtls="CASE booking_no ".$approved_string." END";
					
					$sql_insert="insert into wo_booking_mst_hstry(id, approved_no, booking_id, booking_type, is_short, booking_no_prefix, booking_no_prefix_num, booking_no, company_id, buyer_id, job_no, po_break_down_id, item_category, fabric_source, currency_id, exchange_rate, pay_mode, source, booking_date, delivery_date, booking_month, booking_year, supplier_id, attention, booking_percent, colar_excess_percent, cuff_excess_percent, is_approved, is_deleted, status_active, inserted_by, insert_date, updated_by, update_date, ready_to_approved, is_apply_last_update, rmg_process_breakdown) 
						select	
						'', $approved_string_mst, id, booking_type, is_short, booking_no_prefix, booking_no_prefix_num, booking_no, company_id, buyer_id, job_no, po_break_down_id, item_category, fabric_source, currency_id, exchange_rate, pay_mode, source, booking_date, delivery_date, booking_month, booking_year, supplier_id, attention, booking_percent, colar_excess_percent, cuff_excess_percent, is_approved, is_deleted, status_active, inserted_by, insert_date, updated_by, update_date, ready_to_approved, is_apply_last_update, rmg_process_breakdown from wo_booking_mst where booking_no in ('$book_nos')";
					
					$sql_insert_dtls="insert into wo_booking_dtls_hstry(id, approved_no, booking_dtls_id, job_no, po_break_down_id, pre_cost_fabric_cost_dtls_id, color_size_table_id, booking_no, booking_type, is_short, fabric_color_id, item_size, fin_fab_qnty, grey_fab_qnty, rate, amount, color_type, construction, copmposition, gsm_weight, dia_width, process_loss_percent, trim_group, description, brand_supplier, uom, process, sensitivity, wo_qnty, delivery_date, cons_break_down, inserted_by, insert_date, updated_by, update_date, status_active, is_deleted, rmg_qty, gmt_item, responsible_dept, responsible_person, reason, gmts_size, gmts_color_id) 
						select	
						'', $approved_string_dtls, id, job_no, po_break_down_id, pre_cost_fabric_cost_dtls_id, color_size_table_id, booking_no, booking_type, is_short, fabric_color_id, item_size, fin_fab_qnty, grey_fab_qnty, rate, amount, color_type, construction, copmposition, gsm_weight, dia_width, process_loss_percent, trim_group, description, brand_supplier, uom, process, sensitivity, wo_qnty, delivery_date, cons_break_down, inserted_by, insert_date, updated_by, update_date, status_active, is_deleted, rmg_qty, gmt_item, responsible_dept, responsible_person, reason, gmts_size, gmts_color_id from wo_booking_dtls where booking_no in ('$book_nos')";
					
				}
				
				//$field_array_booking_update = "is_approved";
				//$rID=execute_query(bulk_update_sql_statement( "wo_booking_mst", "id", $field_array_booking_update, $data_array_booking_update, $booking_id_arr));
				
				
				
				
				$data_arr_up=array();
				$data_arr_up[IS_APPROVED]= $partial_approval;
				$rID=$this->updateData('WO_BOOKING_MST', $data_arr_up, array('ID' => $booking_ids));
				if($rID) $flag=1; else $flag=0;
				
				$query="UPDATE approval_history SET current_approval_status=0 WHERE entry_form=32 and mst_id in ($booking_ids)";
				$rIDapp=$this->db->query($query,1);
				if($flag==1) 
				{
					if($rIDapp) $flag=1; else $flag=0; 
				} 
				
				
				//$rID2=sql_insert("approval_history",$field_array,$data_array,0);
				$rID2=$this->insertData("APPROVAL_HISTORY",$data_arr_save);
				if($flag==1) 
				{
					if($rID2) $flag=1; else $flag=0; 
				} 
				
				if(count($approved_no_array)>0)
				{
					$rID3=$this->db->query($sql_insert,0);
					if($flag==1) 
					{
						if($rID3) $flag=1; else $flag=0; 
					} 
		
					$rID4=$this->db->query($sql_insert_dtls,1);
					if($flag==1) 
					{
						if($rID4) $flag=1; else $flag=0; 
					}
				}
				
				if($flag==1) $msg=1; else $msg=0; 
			
			
			   return $msg;
					
		  }//end else if condition;
		
		
		
		
		
		
		
		
		
	}//End Save;






}
