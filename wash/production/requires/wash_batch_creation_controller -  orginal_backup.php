<?
header('Content-type:text/html; charset=utf-8');
session_start();
if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");

include('../../../includes/common.php');

$data=$_REQUEST['data'];
$action=$_REQUEST['action'];

$color_arr=return_library_array( "select id, color_name from lib_color",'id','color_name');
$buyer_arr=return_library_array( "select id, buyer_name from lib_buyer",'id','buyer_name');
if($action == "load_drop_down_sub_operation")
{
	switch ($data) {
		case "1":
			//echo "Your favorite color is red!";
			$load_data = '1,2';
			break;
		case "2":
			$load_data = '1,2,3,4,5';
			break;
		case "3":
			$load_data = '3,4,5';
			break;
			case "4":
			$load_data = '3,4,5';
			break;
		default:
			$load_data = '';
	}
	echo create_drop_down( "cbo_sub_operation",172, $wash_sub_operation_arr,"","", "", 0, "",'',$load_data,'','','',9);
	exit();
}
if($action=="po_popup")
{
  	echo load_html_head_contents("Order Info","../../../", 1, 1, '','1','');
	extract($_REQUEST);
	//if($tot_row>1) $disabled=1; else $disabled=0;
	if($color_name != "") $color_id = return_field_value("id","lib_color","color_name='$color_name' and is_deleted=0 and status_active=1");			
	else $color_id = 0;
	?>
		<script>
			function js_set_value( po_id,po_no,gmts_item_id,buyer_id,buyer_po_id,buyer_po_no,color_id,job_no,within_group,po_qnty,buyer_style_ref,order_uom)
			{
				
				//alert(buyer_po_id);
				document.getElementById('po_id').value=po_id;
				document.getElementById('po_no').value=po_no;
				document.getElementById('gmts_item_id').value=gmts_item_id;
				document.getElementById('buyer_id').value=buyer_id;
				parent.emailwindow.hide();
			}
		</script>
	</head>
	
	<body>
		<fieldset style="width:720px;margin-left:10px">
			<form name="searchorderfrm_1"  id="searchorderfrm_1" autocomplete="off">
				<table cellpadding="0" cellspacing="0" width="720" class="rpt_table" align="center">
					<thead>
						<th>Buyer</th>
						<th>Search By</th>
						<th>Search</th>
						<th>
							<input type="reset" name="reset" id="reset" value="Reset" style="width:100px" class="formbutton" />
							<input type="hidden" name="po_id" id="po_id" value="">
							<input type="hidden" name="po_no" id="po_no" value="">
							<input type="hidden" name="gmts_item_id" id="gmts_item_id" value="">
							<input type="hidden" name="buyer_id" id="buyer_id" value="">
						</th> 
					</thead>
					<tr class="general">
						<td><? echo create_drop_down( "cbo_buyer_name", 170, "select buy.id, buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company='$cbo_company_id' $buyer_cond and buy.id in (select buyer_id from lib_buyer_party_type where party_type in (2,3)) order by buy.buyer_name","id,buyer_name", 1, "-- Select Buyer --", $buyer_id,'',$disabled); ?></td>
						<td><?
								$search_by_arr=array(1=>"PO No",2=>"Job No",3=>"Buyer Style No");
								echo create_drop_down("cbo_search_by", 170, $search_by_arr,"",0, "--Select--", "",$dd,0 );
							?>
						</td>                 
						<td><input type="text" style="width:130px" class="text_boxes"  name="txt_search_common" id="txt_search_common" /></td> 						
						<td><input type="button" name="button2" class="formbutton" value="Show" onClick="show_list_view ( document.getElementById('txt_search_common').value+'_'+document.getElementById('cbo_search_by').value+'_'+<? echo $cbo_company_id; ?>+'_'+document.getElementById('cbo_buyer_name').value+'_'+<? echo $color_id; ?>, 'create_po_search_list_view', 'search_div', 'wash_batch_creation_controller', 'setFilterGrid(\'tbl_list_search\',-1);')" style="width:100px;" /></td>
					</tr>
				</table>
				<div id="search_div" style="margin-top:10px"></div>   
			</form>
		</fieldset>
	</body>           
	<script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
	</html>
	<?
	exit();
}

if($action=="create_po_search_list_view")
{
	$data=explode('_',$data);
	$color_arr = return_library_array("select id, color_name from lib_color", 'id', 'color_name');
	$search_string=trim($data[0]);
	$search_by=$data[1];
	$company_id =$data[2];
	$buyer_id =$data[3];
	$color_id =$data[4];
	
	if($search_by==1) $search_field='b.order_no'; else $search_field='a.subcon_job';
	if($buyer_id==0) $party_cond=""; else $party_cond="and a.party_id=$buyer_id";
	if($color_id==0) $color_cond =""; else $color_cond ="and b.gmts_color_id='$color_id'";

	if($db_type==0)  
    {
       $gmts_item_id_cond = "group_concat(b.gmts_item_id) as gmts_item_id";
       $color_id_cond = "group_concat(b.gmts_color_id) as color_id";
    }
    else
    {
       $gmts_item_id_cond = "listagg(cast(b.gmts_item_id as varchar2(4000)),',') within group (order by b.gmts_item_id) as gmts_item_id";
       $color_id_cond = "listagg(cast(b.gmts_color_id as varchar2(4000)),',') within group (order by b.gmts_color_id) as color_id";
    }

    $po_ids='';
	if($db_type==0) $id_cond="group_concat(b.id)";
	else if($db_type==2) $id_cond="listagg(b.id,',') within group (order by b.id)";
	
	
	if($search_string !="" && ($search_by==3 || $search_by==2 || $search_by==1 ))
	{
		$style_cond=''; 
		if($search_by==3)
		{
			//$style_cond=" and a.style_ref_no like '%$search_string%'";
			$buyer_style_ref_cond=" and b.buyer_style_ref like '%$search_string%' ";
		}
		else if($search_by==2)
		{
			$job_cond=" and a.subcon_job like '%$search_string%'";
		}
		else
		{
			$po_cond=" and b.order_no like '%$search_string%'";
		}
		$po_ids = return_field_value("$id_cond as id", "wo_po_details_master a, wo_po_break_down b", "a.job_no=b.job_no_mst $style_cond", "id");
	}
	
	
	if ($po_ids!="") $po_idsCond=" and b.buyer_po_id in ($po_ids)"; else $po_idsCond="";
	$po_sql ="Select a.style_ref_no, b.id, b.po_number from wo_po_details_master a, wo_po_break_down b where a.job_no=b.job_no_mst";
	$po_sql_res=sql_select($po_sql); $buyer_po_arr=array();
	foreach ($po_sql_res as $row)
	{
		$buyer_po_arr[$row[csf("id")]]['style']=$row[csf("style_ref_no")];
		//$buyer_po_arr[$row[csf("id")]]['po']=$row[csf("buyer_po_no")];
		//$buyer_po_arr[$row[csf("id")]]['po']=$row[csf("po_number")];
	}
	unset($po_sql_res);

	//$sql = "select a.subcon_job as job_no, b.cust_style_ref as style_ref_no, a.party_id as buyer_name, b.order_uom, $gmts_item_id_cond, $color_id_cond, b.id, b.order_no as po_number, b.order_quantity as po_qnty_in_pcs from subcon_ord_mst a, subcon_ord_dtls b, subcon_ord_breakdown c where a.subcon_job=b.job_no_mst and b.id=c.order_id and a.company_id=$company_id $color_cond and a.party_id=$buyer_id and $search_field like '$search_string' and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 group by a.subcon_job, b.cust_style_ref, a.party_id, b.order_uom, b.id, b.order_no, b.order_quantity";
	$sql = "select a.subcon_job as job_no,a.within_group, a.party_id as buyer_name,b.buyer_po_id,b.buyer_po_no, b.order_uom, b.gmts_item_id as gmts_item_id, b.gmts_color_id as color_id, b.id, b.order_no as po_number, b.order_quantity as po_qnty,b.buyer_style_ref from subcon_ord_mst a, subcon_ord_dtls b where a.subcon_job=b.job_no_mst and a.id=b.mst_id and a.entry_form=295 and a.company_id=$company_id $color_cond $party_cond $po_idsCond $job_cond $po_cond $buyer_style_ref_cond and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 order by b.id DESC";
	?>
    <div>
        <table cellspacing="0" cellpadding="0" border="1" rules="all" width="800" class="rpt_table" >
            <thead>
                <th width="30">SL</th>
                <th width="110">Job No</th>
                <th width="110">PO No</th>
                <th width="100">Buyer PO</th>
                <th width="110">Buyer Style</th>
                <th width="70">PO Qty</th>
                <th width="50">UOM</th>
                <th>Colors</th>
            </thead>
        </table>
        <div style="width:818px; overflow-y:scroll; max-height:240px;" id="buyer_list_view" align="center">
            <table cellspacing="0" cellpadding="0" border="1" rules="all" width="800" class="rpt_table" id="tbl_list_search" >
            <?
				$i=1;
				$nameArray=sql_select( $sql );
				foreach ($nameArray as $selectResult)
				{
					//$items_id=implode(",", array_unique(explode(",", $selectResult[csf('gmts_item_id')])));
					$qty=0;
					$qty=number_format($selectResult[csf('po_qnty')]*12,0,'','');

					if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
				?>
                    <tr bgcolor="<? echo $bgcolor; ?>" style="text-decoration:none; cursor:pointer" id="search<? echo $i;?>" onClick="js_set_value(<? echo $selectResult[csf('id')]; ?>,'<? echo $selectResult[csf('po_number')]; ?>','<? echo $selectResult[csf('gmts_item_id')]; ?>','<? echo $selectResult[csf('buyer_name')]; ?>','<? echo $selectResult[csf('buyer_po_id')]; ?>','<? echo $selectResult[csf('buyer_po_no')]; ?>','<? echo $selectResult[csf('color_id')]; ?>','<? echo $selectResult[csf('job_no')]; ?>','<? echo $selectResult[csf('within_group')]; ?>','<? echo $selectResult[csf('po_qnty')]; ?>','<? echo $selectResult[csf('buyer_style_ref')]; ?>','<? echo $selectResult[csf('order_uom')]; ?>')"> 
                        <td width="30" align="center"><? echo $i; ?></td>	
                        <td width="110" style="word-break:break-all"><? echo $selectResult[csf('job_no')]; ?></td>
                        <td width="110" style="word-break:break-all"><? echo $selectResult[csf('po_number')]; ?></td>
                        <td width="100" style="word-break:break-all" title="<? echo $selectResult[csf('buyer_po_id')]; ?>"><? echo $selectResult[csf('buyer_po_no')]; ?></td>
                        <td width="110" style="word-break:break-all"><? if($selectResult[csf('within_group')]==1){echo $buyer_po_arr[$selectResult[csf('buyer_po_id')]]['style'];}else { echo $selectResult[csf('buyer_style_ref')] ;} ?></td>
                        <td width="70" align="right"><? echo $selectResult[csf('po_qnty')]; ?></td> 
                        <td width="50" align="center"><? echo $unit_of_measurement[$selectResult[csf('order_uom')]]; ?></td>
                        <td style="word-break:break-all"><? echo $color_arr[$selectResult[csf('color_id')]]; ?></td>
                    </tr>
                <?
                $i++;
				}
			?>
            </table>
        </div>
	</div>           
	<?
    exit();	
}

if ($action=="save_update_delete")
{
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process )); 
	
	if(str_replace("'","",$txt_ext_no)!="" || $db_type==0)
	{
		$extention_no_cond="extention_no=$txt_ext_no";
	}
	else 
	{
		$extention_no_cond="extention_no is null";
	}
	
	if($operation==0)  // Insert Here
	{ 
		$con = connect();
		if($db_type==0)
		{
			mysql_query("BEGIN");
		}
		
		//if( check_table_status( $_SESSION['menu_id'], 1 )==0 ) { echo "15**0"; die;}
		
		$batch_update_id=''; $batch_no_creation=str_replace("'","",$batch_no_creation);
		
		if(str_replace("'","",$txt_batch_color)!="")
		{
			if (!in_array(str_replace("'","",$txt_batch_color),$new_array_color))
			{
				$color_id = return_id( str_replace("'","",$txt_batch_color), $color_arr, "lib_color", "id,color_name","316");
				$new_array_color[$color_id]=str_replace("'","",$txt_batch_color);
			}
			else $color_id =  array_search(str_replace("'","",$txt_batch_color), $new_array_color);
		}
		else $color_id=0;
		
		if(str_replace("'","",$update_id)=="")
		{
			//$id=return_next_id( "id", "pro_batch_create_mst", 1 ) ;
			$id = return_next_id_by_sequence("PRO_BATCH_CREATE_MST_PK_SEQ", "pro_batch_create_mst", $con);
			$batch_update_id=$id;
			$serial_no=date("y",strtotime($pc_date_time))."-".$id;
			
		 	if($batch_no_creation==1)
			{
				$txt_batch_number="'".$id."'";
			}
			else
			{
				if(is_duplicate_field( "batch_no", "pro_batch_create_mst", "batch_no=$txt_batch_number and $extention_no_cond and entry_form=316" )==1)
				{
					//check_table_status( $_SESSION['menu_id'],0);
					echo "11**0"; 
					die;			
				}
				$txt_batch_number=$txt_batch_number;
			}

			$field_array="id, entry_form, batch_no, batch_date, batch_against, company_id, extention_no, color_id, batch_weight, color_range_id, process_id, organic, dur_req_hr, dur_req_min,dyeing_machine,remarks, inserted_by, insert_date, shift_id, operator_name, supervisor_name, within_group, party_id,operation_type,gmts_type,sub_operation";
			$data_array="(".$id.",316,".$txt_batch_number.",".$txt_batch_date.",".$cbo_batch_against.",".$cbo_company_id.",".$txt_ext_no.",".$color_id.",".$txt_batch_weight.",".$cbo_color_range.",".$txt_process_id.",".$txt_organic.",".$txt_du_req_hr.",".$txt_du_req_min.",".$machine_id.",".$txt_remarks.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."',".$cbo_shift.",".$txt_operator.",".$txt_supervisor.",".$cbo_within_group.",".$buyer_id.",".$cbo_operation.",".$cbo_gmts_type.",".$cbo_sub_operation.")";
		}
		else
		{
			$batch_update_id=str_replace("'","",$update_id);
			$serial_no=str_replace("'","",$txt_batch_sl_no);
			if($batch_no_creation!=1)
			{
				if(is_duplicate_field( "batch_no", "pro_batch_create_mst", "batch_no=$txt_batch_number and $extention_no_cond and entry_form=316 and id<>$update_id" )==1)
				{
					//check_table_status( $_SESSION['menu_id'],0);
					echo "11**0"; 
					die;			
				}
			}
			
			$field_array_update="batch_no*batch_date*extention_no*color_id*batch_weight*color_range_id*process_id*dyeing_machine*organic*dur_req_hr*dur_req_min*remarks*updated_by*update_date*shift_id*operator_name*supervisor_name*within_group*party_id*operation_type*gmts_type*sub_operation";
			$data_array_update=$txt_batch_number."*".$txt_batch_date."*".$txt_ext_no."*".$color_id."*".$txt_batch_weight."*".$cbo_color_range."*".$txt_process_id."*".$machine_id."*".$txt_organic."*".$txt_du_req_hr."*".$txt_du_req_min."*".$txt_remarks."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'*".$cbo_shift."*".$txt_operator."*".$txt_supervisor."*".$cbo_within_group."*".$buyer_id."*".$cbo_operation."*".$cbo_gmts_type."*".$cbo_sub_operation."";
		}
		
		//$id_dtls=return_next_id( "id", "pro_batch_create_dtls", 1 ) ;
		$field_array_dtls="id, mst_id, po_id, prod_id, roll_no, batch_qnty, buyer_po_id, inserted_by, insert_date";
		$batch_balance='';
		for($i=1;$i<=$total_row;$i++)
		{
			$id_dtls = return_next_id_by_sequence("PRO_BATCH_CREATE_DTLS_PK_SEQ", "pro_batch_create_dtls", $con);
			$po_id="poId_".$i;  
			$cboItem="cboItem_".$i;
			$txtGmtsQty="txtGmtsQty_".$i;
			$txtBatchQnty="txtBatchQnty_".$i;
			$buyerPoId="buyerPoId_".$i;

			if($data_array_dtls!="") $data_array_dtls.=","; 	
			$data_array_dtls.="(".$id_dtls.",".$batch_update_id.",".$$po_id.",".$$cboItem.",".$$txtGmtsQty.",".$$txtBatchQnty.",".$$buyerPoId.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."')"; 
			//$id_dtls=$id_dtls+1;
		}
		//echo "10**0**0"; die;
		$flag=1;
		
		if(str_replace("'","",$update_id)=="")
		{
			$rID=sql_insert("pro_batch_create_mst",$field_array,$data_array,0);
			if($rID==1 && $flag==1) $flag=1; else $flag=0;
		}
		else
		{
			$rID=sql_update("pro_batch_create_mst",$field_array_update,$data_array_update,"id",$update_id,1);
			if($rID==1 && $flag==1) $flag=1; else $flag=0; 
		}
		
		//echo "insert into pro_batch_create_dtls (".$field_array_dtls.") values ".$data_array_dtls;die;
		$rID2=sql_insert("pro_batch_create_dtls",$field_array_dtls,$data_array_dtls,1);
		if($rID2==1 && $flag==1) $flag=1; else $flag=0; 
		
		//check_table_status( $_SESSION['menu_id'],0);
		//echo "10**".$flag;die;
		if($db_type==0)
		{
			if($flag==1)
			{
				mysql_query("COMMIT");  
				echo "0**".$batch_update_id."**".$serial_no;
			}
			else
			{
				mysql_query("ROLLBACK"); 
				echo "5**0**0";
			}
		}
		else if($db_type==2 || $db_type==1 )
		{
			if($flag==1)
			{
				oci_commit($con);   
				echo "0**".$batch_update_id."**".$serial_no;
			}
			else
			{
				oci_rollback($con);
				echo "5**0**0";
			}
		}
				
		disconnect($con);
		die;
	}
	else if ($operation==1)   // Update Here
	{ 
		$con = connect();
		if($db_type==0)
		{
			mysql_query("BEGIN");
		}
		
		if(str_replace("'","",$txt_batch_color)!="")
		{
			if (!in_array(str_replace("'","",$txt_batch_color),$new_array_color))
			{
				$color_id = return_id( str_replace("'","",$txt_batch_color), $color_arr, "lib_color", "id,color_name","316");
				$new_array_color[$color_id]=str_replace("'","",$txt_batch_color);
			}
			else $color_id =  array_search(str_replace("'","",$txt_batch_color), $new_array_color);
		}
		else $color_id=0;
		
		$batch_no_creation=str_replace("'","",$batch_no_creation);
		
		$batch_update_id=str_replace("'","",$update_id);
		$serial_no=str_replace("'","",$txt_batch_sl_no);
		
		if($batch_no_creation!=1)
		{
			if(is_duplicate_field( "batch_no", "pro_batch_create_mst", "batch_no=$txt_batch_number and $extention_no_cond and entry_form=316 and id<>$update_id" )==1)
			{
				//check_table_status( $_SESSION['menu_id'],0);
				echo "11**0"; 
				die;			
			}
		}
		
		$field_array_update="batch_no*batch_date*extention_no*color_id*batch_weight*color_range_id*process_id*dyeing_machine*organic*dur_req_hr*dur_req_min*remarks*updated_by*update_date*shift_id*operator_name*supervisor_name*within_group*party_id*operation_type*gmts_type*sub_operation";
		$data_array_update=$txt_batch_number."*".$txt_batch_date."*".$txt_ext_no."*".$color_id."*".$txt_batch_weight."*".$cbo_color_range."*".$txt_process_id."*".$machine_id."*".$txt_organic."*".$txt_du_req_hr."*".$txt_du_req_min."*".$txt_remarks."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'*".$cbo_shift."*".$txt_operator."*".$txt_supervisor."*".$cbo_within_group."*".$buyer_id."*".$cbo_operation."*".$cbo_gmts_type."*".$cbo_sub_operation."";
		
		//$id_dtls_batch=return_next_id( "id", "pro_batch_create_dtls", 1 ) ;
		$field_array_dtls="id, mst_id, po_id, prod_id, roll_no, batch_qnty, buyer_po_id, inserted_by, insert_date";
		$field_array_dtls_update="po_id*prod_id*roll_no*batch_qnty*buyer_po_id*updated_by*update_date";
		for($i=1;$i<=$total_row;$i++)
		{
			$id_dtls_batch = return_next_id_by_sequence("PRO_BATCH_CREATE_DTLS_PK_SEQ", "pro_batch_create_dtls", $con);
			$po_id="poId_".$i;
			$cboItem="cboItem_".$i;
			$txtGmtsQty="txtGmtsQty_".$i;
			$txtBatchQnty="txtBatchQnty_".$i;
			$updateIdDtls="updateIdDtls_".$i;
			$buyerPoId="buyerPoId_".$i;
			if(str_replace("'","",$$updateIdDtls)!="")
			{
				$id_arr[]=str_replace("'",'',$$updateIdDtls);
				$data_array_dtls_update[str_replace("'",'',$$updateIdDtls)] = explode("*",($$po_id."*".$$cboItem."*".$$txtGmtsQty."*".$$txtBatchQnty."*".$$buyerPoId."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'"));
			}
			else
			{
				if($data_array_dtls!="") $data_array_dtls.=","; 	
				$data_array_dtls.="(".$id_dtls_batch.",".$batch_update_id.",".$$po_id.",".$$cboItem.",".$$txtGmtsQty.",".$$txtBatchQnty.",".$$buyerPoId.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."')"; 
				
				//$id_dtls_batch=$id_dtls_batch+1;
			}
		}
		$flag=1;
		$rID=sql_update("pro_batch_create_mst",$field_array_update,$data_array_update,"id",$update_id,1);
		if($rID==1 && $flag==1) $flag=1; else $flag=0;
		
		//echo bulk_update_sql_statement( "pro_batch_create_dtls", "id", $field_array_dtls_update, $data_array_dtls_update, $id_arr );die;
		if($data_array_dtls_update!="")
		{
			$rID2=execute_query(bulk_update_sql_statement( "pro_batch_create_dtls", "id", $field_array_dtls_update, $data_array_dtls_update, $id_arr ));
			if($rID2==1 && $flag==1) $flag=1; else $flag=0; 
		}
		//echo $flag;die;
		//echo "6**0**insert into pro_batch_create_dtls (".$field_array_dtls.") values ".$data_array_dtls;die;
		if($data_array_dtls!="")
		{
			$rID3=sql_insert("pro_batch_create_dtls",$field_array_dtls,$data_array_dtls,1);
			if($rID3==1 && $flag==1) $flag=1; else $flag=0; 
		}
	
		if($txt_deleted_id!="")
		{
			$field_array_status="updated_by*update_date*status_active*is_deleted";
			$data_array_status=$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'*0*1";
	
			$rID4=sql_multirow_update("pro_batch_create_dtls",$field_array_status,$data_array_status,"id",$txt_deleted_id,1);
			if($rID4==1 && $flag==1) $flag=1; else $flag=0; 
		}
		//echo "10**".$rID.'='.$rID1.'='.$rID2.'='.$rID3.'='.$rID4.'='.$flag; die;
		if($db_type==0)
		{
			if($flag==1)
			{
				mysql_query("COMMIT");  
				echo "1**".$batch_update_id."**".$serial_no;
			}
			else
			{
				mysql_query("ROLLBACK"); 
				echo "6**0**1";
			}
		}
		else if($db_type==2 || $db_type==1 )
		{
			if($flag==1)
			{
				oci_commit($con);
				echo "1**".$batch_update_id."**".$serial_no;
			}
			else
			{
				oci_rollback($con);
				echo "6**0**1";
			}
		}
		disconnect($con);
		die;
	}
}

function sql_updates($strTable,$arrUpdateFields,$arrUpdateValues,$arrRefFields,$arrRefValues,$commit)
{

	$strQuery = "UPDATE ".$strTable." SET ";
	$arrUpdateFields=explode("*",$arrUpdateFields);
	$arrUpdateValues=explode("*",$arrUpdateValues);

	if(count($arrUpdateFields)!=count($arrUpdateValues)){
		return "0";
	}

	if(is_array($arrUpdateFields))
	{
		$arrayUpdate = array_combine($arrUpdateFields,$arrUpdateValues);
		$Arraysize = count($arrayUpdate);
		$i = 1;
		foreach($arrayUpdate as $key=>$value):
			$strQuery .= ($i != $Arraysize)? $key."=".$value.", ":$key."=".$value;
			$i++;
		endforeach;
	}
	else
	{
		$strQuery .= $arrUpdateFields."=".$arrUpdateValues;
	}
	$strQuery .=" WHERE ";

	$arrRefFields=explode("*",$arrRefFields);
	$arrRefValues=explode("*",$arrRefValues);
	if(is_array($arrRefFields))
	{
		$arrayRef = array_combine($arrRefFields,$arrRefValues);
		$Arraysize = count($arrayRef);
		$i = 1;
		foreach($arrayRef as $key=>$value):
			$strQuery .= ($i != $Arraysize)? $key."=".$value." AND ":$key."=".$value."";
			$i++;
		endforeach;
	}
	else
	{
		$strQuery .= $arrRefFields."=".$arrRefValues."";
	}

	global $con;
	if( strpos($strQuery, "WHERE")==false)  return "0";
	echo "10**".$strQuery; die;
	$stid =  oci_parse($con, $strQuery);
	$exestd=oci_execute($stid,OCI_NO_AUTO_COMMIT);
	if ($exestd)
		return "1";
	else
		return "0";

	die;
	if ( $commit==1 )
	{
		if (!oci_error($stid))
		{
			oci_commit($con);
			return "1";
		}
		else
		{
			oci_rollback($con);
			return "10";
		}
	}
	else
		return 1;
	die;
}


if($action=="batch_popup")
{
  	echo load_html_head_contents("Batch Info","../../../", 1, 1, '','1','');
	extract($_REQUEST);
	?>
	<script>
	
		function js_set_value( batch_id,operation_id,sub_operation_id)
		{
			
			//alert(sub_operation_id);
			document.getElementById('hidden_batch_id').value=batch_id;
			document.getElementById('hidden_operation_id').value=operation_id;
			document.getElementById('hidden_sub_operation_id').value=sub_operation_id;
			parent.emailwindow.hide();
		}
	
    </script>
    </head>
    <body>
    <div align="center">
        <fieldset style="width:830px;margin-left:4px;">
            <form name="searchorderfrm_1"  id="searchorderfrm_1" autocomplete="off">
                <table cellpadding="0" cellspacing="0" width="750" class="rpt_table">
                    <thead>
                        <th>Search By</th>
                        <th>Search</th>
                        <th>Date Range</th>
                        <th>
                            <input type="reset" name="reset" id="reset" value="Reset" style="width:100px" class="formbutton" />
                            <input type="hidden" name="hidden_batch_id" id="hidden_batch_id" value="">
                            <input type="hidden" name="hidden_operation_id" id="hidden_operation_id" value="">
                            <input type="hidden" name="hidden_sub_operation_id" id="hidden_sub_operation_id" value="">
                        </th> 
                    </thead>
                    <tr class="general">
                        <td>	
                            <?
                                $search_by_arr=array(1=>"Batch No",2=>"Style No");
                                echo create_drop_down( "cbo_search_by", 150, $search_by_arr,"",0, "--Select--", "",$dd,0 );
                            ?>
                        </td>                 
                        <td>				
                            <input type="text" style="width:140px" class="text_boxes"  name="txt_search_common" id="txt_search_common" />	
                        </td> 
                        <td>
                            <input name="txt_date_from" id="txt_date_from" class="datepicker" style="width:55px" placeholder="From Date" value="">
                                &nbsp; To
                            <input name="txt_date_to" id="txt_date_to" class="datepicker" style="width:55px"  placeholder="To Date"  value="">
                        </td>						
                        <td>
                            <input type="button" name="button2" class="formbutton" value="Show" onClick="show_list_view ( document.getElementById('txt_search_common').value+'_'+document.getElementById('cbo_search_by').value+'_'+<? echo $cbo_company_id; ?>+'_'+<? echo $batch_against; ?>+'_'+document.getElementById('txt_date_from').value+'_'+document.getElementById('txt_date_to').value, 'create_batch_search_list_view', 'search_div', 'wash_batch_creation_controller', 'setFilterGrid(\'tbl_list_search\',-1);')" style="width:100px;" />
                        </td>
                    </tr>
                    <tfoot>
                        <tr>
                            <td colspan="4" align="center">
                                <? echo load_month_buttons(1); ?>
                            </td>
                        </tr>
                    </tfoot>
                </table>
                <div id="search_div" style="margin-top:10px"></div>   
            </form>
        </fieldset>
    </div>    
    </body>           
    <script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
    </html>
    <?
    exit();
}

if($action=="create_batch_search_list_view")
{
	$data=explode('_',$data);
	
	$search_string=trim($data[0]);
	$search_by =$data[1];
	$company_id =$data[2];
	$batch_against_id=$data[3];
	$date_from=$data[4];
	$date_to=$data[5];
	
	if($search_string!='')
	{
		if($search_by==1)
		{
			$search_field="and a.batch_no like '$search_string'";
		}
		else if($search_by==2)
		{
			//$style_cond=" and a.style_ref_no like '$search_string'";
			$buyer_style_cond=" and b.buyer_style_ref like '%$search_string%'";
		}
		else
		{
			$search_field='booking_no';
		}
	}
	 
	 		$order_buyer_po_array=array();
			$buyer_po_arr=array();
			$order_buyer_po='';
			$order_sql ="select b.id,b.buyer_po_no,b.buyer_style_ref from subcon_ord_mst a, subcon_ord_dtls b where a.id=b.mst_id and a.entry_form='295' $buyer_style_cond"; 
			$order_sql_res=sql_select($order_sql);
			foreach ($order_sql_res as $row)
			{
				$order_buyer_po_array[]=$row[csf("id")];
				$buyer_po_arr[$row[csf("id")]]['style']=$row[csf("buyer_style_ref")];
				$buyer_po_arr[$row[csf("id")]]['po']=$row[csf("buyer_po_no")];
			}
			//unset($order_sql_res);
			$order_buyer_po=implode(",",$order_buyer_po_array);
			//echo $order_buyer_po; 
			if ($order_buyer_po!="") $order_order_buyer_poCond=" and b.po_id in ($order_buyer_po)"; else $order_order_buyer_poCond="";
	 
	 
	 

	/*$po_ids=''; 
	$buyer_po_arr=array();
	if($search_by==2 && $data[0]!='')
	{
		if($db_type==0) $id_cond="group_concat(b.id) as id";
		else if($db_type==2) $id_cond="rtrim(xmlagg(xmlelement(e,b.id,',').extract('//text()') order by b.id).GetClobVal(),',') as id";
		//echo "select $id_cond from wo_po_details_master a, wo_po_break_down b where a.job_no=b.job_no_mst $job_cond $style_cond $po_cond";
		
		$po_ids = return_field_value("$id_cond", "wo_po_details_master a, wo_po_break_down b", "a.job_no=b.job_no_mst $style_cond ", "id");
		
		//echo $po_ids;
		if($db_type==2 && $po_ids!="") $po_ids = $po_ids->load();
		if ($po_ids!="")
		{
			$po_ids=explode(",",$po_ids);
			$po_idsCond=""; $poIdsCond="";
			//echo count($po_ids); die;
			if($db_type==2 && count($po_ids)>=999)
			{
				$chunk_arr=array_chunk($po_ids,999);
				foreach($chunk_arr as $val)
				{
					$ids=implode(",",$val);
					if($po_idsCond=="")
					{
						$po_idsCond.=" and ( b.buyer_po_id in ( $ids) ";
						$poIdsCond.=" and ( b.id in ( $ids) ";
					}
					else
					{
						$po_idsCond.=" or  b.buyer_po_id in ( $ids) ";
						$poIdsCond.=" or  b.id in ( $ids) ";
					}
				}
				$po_idsCond.=")";
				$poIdsCond.=")";
			}
			else
			{
				$ids=implode(",",$po_ids);
				$po_idsCond.=" and b.buyer_po_id in ($ids) ";
				$poIdsCond.=" and b.id in ($ids) ";
			}
		}
		else if($po_ids=="" && ($search_by==2 && $data[0]!=''))
		{
			echo "Not Found"; die;
		}
		//echo $po_idsCond;
	}*/
	
	/*
	$po_sql ="Select a.style_ref_no,a.job_no_prefix_num, b.id, b.po_number from wo_po_details_master a, wo_po_break_down b where a.job_no=b.job_no_mst $poIdsCond";
	$po_sql_res=sql_select($po_sql); 
	$buyer_style_arr=array();
	foreach ($po_sql_res as $row)
	{
		$buyer_style_arr[$row[csf("id")]]=$row[csf("style_ref_no")];
		//$buyer_po_arr[$row[csf("id")]]['po']=$row[csf("po_number")];
		//$buyer_po_arr[$row[csf("id")]]['job']=$row[csf("job_no_prefix_num")];
	}
	unset($po_sql_res);*/

	if($data[0] != "") $date_cond="";
	else
	{
		if($date_from != "" && $date_to != ""){
			if($db_type==0)
			{
				$date_cond=" and a.batch_date between '".change_date_format($date_from,'yyyy-mm-dd')."' and '".change_date_format($date_to,'yyyy-mm-dd')."'";
			}
			else
			{
				$date_cond=" and a.batch_date between '".change_date_format($date_from, "", "",1)."' and '".change_date_format($date_to, "", "",1)."'";
			}
		}
	}

	$batch_cond.=" and a.batch_against=$batch_against_id";
	
	//$arr=array(2=>$po_name_arr,5=>$batch_against,6=>$color_arr,7=>$buyer_style_arr);

	if($db_type==2) 
	{
		$group_concat_id=" ,listagg(b.po_id,',') within group (order by b.po_id) as po_id , listagg(b.buyer_po_id,',') within group (order by b.buyer_po_id) as buyer_po_id" ;
	}
	else if($db_type==0)
	{
		$group_concat_id=" ,group_concat(b.po_id) as po_id, group_concat(b.buyer_po_id) as buyer_po_id" ;
	}
	
	 $sql = "select a.id, a.batch_no, a.extention_no, a.batch_weight, a.total_trims_weight, a.batch_date, a.batch_against, a.batch_for, a.booking_no, a.color_id $group_concat_id,b.po_id,a.gmts_type, a.sub_operation from pro_batch_create_mst a, pro_batch_create_dtls b where a.id=b.mst_id and a.company_id=$company_id  $search_field $date_cond and a.status_active=1 and a.entry_form=316 and a.is_deleted=0 $batch_cond $order_order_buyer_poCond group by a.id, a.batch_no, a.extention_no, a.batch_weight, a.total_trims_weight, a.batch_date, a.batch_against, a.batch_for, a.booking_no, a.color_id ,b.po_id,a.gmts_type,a.sub_operation"; 
	//echo $sql;	 
	/*echo  create_list_view("tbl_list_search", "Batch No,Ext. No,Order No,Batch Weight, Batch Date,Batch Against, Color,Style No", "100,70,150,80,80,80,80,80","810","250",0, $sql, "js_set_value", "id", "", 1, "0,0,id,0,0,batch_against,color_id,", $arr, "batch_no,extention_no,id,batch_weight,batch_date,batch_against,color_id", "",'','0,0,0,2,3,0');*/
	$result = sql_select($sql);
	$po_arr=return_library_array( "select id,order_no from subcon_ord_dtls",'id','order_no');
	?>
    <div>
     	<table cellspacing="0" cellpadding="0" border="1" rules="all" width="830" class="rpt_table">
            <thead>
                <th width="30" >SL</th>
                <th width="100" >Batch No</th>
                <th width="70" >Ext. No</th>
                <th width="150" >Order No</th>
                <th width="80" >Batch Weight</th>
                <th width="80" >Batch Date</th>
                <th width="80">Batch Against</th>
                <th width="80">Color</th>
                <th>Buyer Style</th>
            </thead>
     	</table>
     <div style="width:830px; max-height:270px;overflow-y:scroll;" >	 
        <table cellspacing="0" cellpadding="0" border="1" rules="all" width="810" class="rpt_table" id="tbl_po_list">
			<?
			$i=1;
            foreach( $result as $row )
            {
                if ($i%2==0)  $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
				$order_no='';
				$order_id=array_unique(explode(",",$row[csf("po_id")]));
				foreach($order_id as $val)
				{
					if($order_no=="") $order_no=$po_arr[$val]; else $order_no.=",".$po_arr[$val];
				}
				$order_no=implode(",",array_unique(explode(",",$order_no)));
				
				/*$buyer_po=""; $buyer_style=""; $buyer_job="";
				$buyer_po_id=explode(",",$row[csf('buyer_po_id')]);
				foreach($buyer_po_id as $po_id)
				{
					//if($buyer_po=="") $buyer_po=$buyer_po_arr[$po_id]['po']; else $buyer_po.=','.$buyer_po_arr[$po_id]['po'];
					if($buyer_style=="") $buyer_style=$buyer_style_arr[$po_id]; else $buyer_style.=','.$buyer_style_arr[$po_id];
					//if($buyer_job=="") $buyer_job=$buyer_po_arr[$po_id]['job']; else $buyer_job.=','.$buyer_po_arr[$po_id]['job'];
				}
				//$buyer_po=implode(",",array_unique(explode(",",$buyer_po)));
				$buyer_style=implode(",",array_unique(explode(",",$buyer_style)));*/
				//$buyer_job=implode(",",array_unique(explode(",",$buyer_job)));,a.gmts_type, a.sub_operation
				?>
					<tr bgcolor="<? echo $bgcolor; ?>" style="text-decoration:none;cursor:pointer" onClick="js_set_value('<? echo $row[csf("id")];?>','<? echo $row[csf("gmts_type")];?>','<? echo $row[csf("sub_operation")];?>');" > 
						<td width="30" align="center"><? echo $i; ?></td>
		                <td width="100" ><? echo $row[csf("batch_no")]; ?></td>
		                <td width="70" ><? echo $row[csf("extention_no")]; ?></td>
		                <td width="150" ><? echo $order_no; ?></td>
		                <td width="80" ><? echo $row[csf("batch_weight")]; ?></td>
		                <td width="80" ><? echo change_date_format($row[csf("batch_date")]); ?></td>
		                <td width="80"><? echo $batch_against[$row[csf("batch_against")]]; ?></td>
		                <td width="80"><? echo $color_arr[$row[csf("color_id")]]; ?></td>
		                <td style="word-break:break-all"><? echo $buyer_po_arr[$row[csf("po_id")]]['style'];//$buyer_style; ?></td>
					</tr>
				<? 
				$i++;
            }
   		?>
			</table>
		</div>
     </div>
     <?	
	exit();	
}

if ($action=="populate_data_from_search_popup")
{
	$data=explode("**",$data);
	$batch_id=$data[2];
	$batch_against=$data[0];
	$batch_for=$data[1];
	
	if($db_type==0) $year_field="DATE_FORMAT(a.insert_date,'%y')"; 
	else if($db_type==2) $year_field="to_char(a.insert_date,'YY')";
	else $year_cond="";//defined Later
		$machine_arr=return_library_array( "select id, machine_no from lib_machine_name",'id','machine_no');

	/*$result=sql_select("select c.party_id as buyer_name from pro_batch_create_dtls a, subcon_ord_dtls b, subcon_ord_mst c where a.po_id=b.id and b.job_no_mst=c.subcon_job and a.mst_id='$batch_id' and a.status_active=1 and a.is_deleted=0");	

	$buyer_id=$result[0][csf('buyer_name')];*/
	
	//echo "select a.id, a.company_id, a.dyeing_machine, a.batch_no, a.extention_no, a.batch_weight, a.batch_date, a.batch_against, a.color_id, a.color_range_id, a.organic, a.process_id, a.dur_req_hr, a.dur_req_min, a.remarks, a.shift_id, a.operator_name, a.supervisor_name,a.party_id, a.within_group,a.operation_type,a.gmts_type,a.sub_operation,b.po_id, $year_field as year from pro_batch_create_mst a ,pro_batch_create_dtls b where a.id='$batch_id' and a.id=b.mst_id and b.mst_id=$batch_id and a.status_active=1 and a.is_deleted=0 ";

	$data_array=sql_select("select a.id, a.company_id, a.dyeing_machine, a.batch_no, a.extention_no, a.batch_weight, a.batch_date, a.batch_against, a.color_id, a.color_range_id, a.organic, a.process_id, a.dur_req_hr, a.dur_req_min, a.remarks, a.shift_id, a.operator_name, a.supervisor_name,a.party_id, a.within_group,a.operation_type,a.gmts_type,a.sub_operation,b.po_id, $year_field as year from pro_batch_create_mst a ,pro_batch_create_dtls b where a.id='$batch_id' and a.id=b.mst_id and b.mst_id=$batch_id and a.status_active=1 and a.is_deleted=0 ");
	
	
	
	//$sql="select b.order_uom, b.id, b.order_no, b.gmts_item_id as item_id, b.gmts_color_id as color_id, b.order_quantity as po_qnty,within_group, party_id,buyer_po_id,a.gmts_type from subcon_ord_mst a, subcon_ord_dtls b where a.id=b.mst_id and a.subcon_job=b.job_no_mst and a.entry_form=295 and b.id in ($poId) and b.gmts_item_id in ($cboItem) order by id DESC";
	
	$gmts_type_arr=return_library_array( "select b.id, a.gmts_type from subcon_ord_mst a, subcon_ord_dtls b where a.id=b.mst_id and a.subcon_job=b.job_no_mst and a.entry_form=295",'id','gmts_type');
	
	
	//print_r($gmts_type_arr);
	
	foreach ($data_array as $row)
	{
		if($row[csf("extention_no")]==0) $ext_no=''; else $ext_no=$row[csf("extention_no")];
		
		$serial_no=$row[csf("id")]."-".$row[csf("year")];

		/*if($row[csf("batch_against")]==6){ $new_subprocess_array= $emblishment_wash_type;}
    	else if($row[csf("batch_against")]==10){ $new_subprocess_array= $emblishment_print_type;}
    	else if($row[csf("batch_against")]==7){ $new_subprocess_array= $emblishment_gmts_type;}*/

		$process_name='';
		$process_id_array=explode(",",$row[csf("process_id")]);
		foreach($process_id_array as $val)
		{
			if($process_name=="") $process_name=$wash_type[$val]; else $process_name.=",".$wash_type[$val];
		}
		
		
		echo "document.getElementById('txt_batch_sl_no').value = '".$serial_no."';\n"; 
		echo "document.getElementById('cbo_batch_against').value = '".$row[csf("batch_against")]."';\n";  
		echo "document.getElementById('cbo_gmts_type').value = '".$gmts_type_arr[$row[csf("po_id")]]."';\n";  
		//echo "document.getElementById('cbo_sub_operation').value = '".$row[csf("sub_operation")]."';\n";
		echo "load_drop_down( 'requires/wash_batch_creation_controller', '".$row[csf("operation_type")]."', 'load_drop_down_sub_operation', 'sub_operation');\n";
		echo "set_multiselect('cbo_sub_operation','0','0','0','0');\n";
		echo "set_multiselect('cbo_sub_operation','0','1','".($row[csf("sub_operation")])."','0');\n"; 
		  
		//echo "document.getElementById('cbo_batch_for').value = '".$row[csf("batch_for")]."';\n";  
		echo "document.getElementById('txt_batch_date').value = '".change_date_format($row[csf("batch_date")])."';\n";  
		echo "document.getElementById('txt_batch_weight').value = '".$row[csf("batch_weight")]."';\n";  
		echo "document.getElementById('cbo_company_id').value = '".$row[csf("company_id")]."';\n";  
		echo "document.getElementById('txt_batch_number').value = '".$row[csf("batch_no")]."';\n";  
		echo "document.getElementById('txt_ext_no').value = '".$ext_no."';\n";  
		echo "document.getElementById('txt_batch_color').value = '".$color_arr[$row[csf("color_id")]]."';\n";
		echo "document.getElementById('txt_machine_no').value = '".$machine_arr[$row[csf("dyeing_machine")]]."';\n";  
		echo "document.getElementById('machine_id').value = '".$row[csf("dyeing_machine")]."';\n";    
		echo "document.getElementById('cbo_color_range').value = '".$row[csf("color_range_id")]."';\n";
		echo "document.getElementById('txt_organic').value = '".$row[csf("organic")]."';\n";
		echo "document.getElementById('txt_process_id').value = '".$row[csf("process_id")]."';\n";
		echo "document.getElementById('txt_process_name').value = '".$process_name."';\n";
		echo "document.getElementById('txt_du_req_hr').value = '".$row[csf("dur_req_hr")]."';\n";
		echo "document.getElementById('txt_du_req_min').value = '".$row[csf("dur_req_min")]."';\n";
		echo "document.getElementById('txt_remarks').value = '".$row[csf("remarks")]."';\n";
		echo "document.getElementById('buyer_id').value = '".$row[csf("party_id")]."';\n";
		echo "document.getElementById('update_id').value = '".$row[csf("id")]."';\n";
		echo "document.getElementById('cbo_within_group').value = '".$row[csf("within_group")]."';\n";
		echo "document.getElementById('cbo_operation').value = '".$row[csf("operation_type")]."';\n";

		echo "document.getElementById('cbo_shift').value = '".$row[csf("shift_id")]."';\n";
		echo "document.getElementById('txt_operator').value = '".$row[csf("operator_name")]."';\n";
		echo "document.getElementById('txt_supervisor').value = '".$row[csf("supervisor_name")]."';\n";

		echo "set_button_status(1, '".$_SESSION['page_permission']."', 'fnc_batch_creation',1);\n";	 
	}
	
	if($db_type==0)
	{
		$data_array2=sql_select("select group_concat(po_id) as po_id, group_concat(prod_id) as prod_id from pro_batch_create_dtls where mst_id=$batch_id");
	}
	else
	{
		$data_array2=sql_select("select listagg(cast(po_id as varchar2(4000)),',') within group (order by po_id) as po_id, listagg(cast(prod_id as varchar2(4000)),',') within group (order by prod_id) as prod_id from pro_batch_create_dtls where mst_id=$batch_id");
	}

	foreach ($data_array2 as $vals) 
	{
		$a = $vals[csf("po_id")];
		$b = $vals[csf("prod_id")];
		echo "load_color_list_update('".$a."*".$b."*".$batch_id."');\n";
	}
	exit();
}

if( $action == 'batch_details' ) 
{
	$data=explode('**',$data);
	$batch_against=$data[0];
	$batch_for=$data[1];
	$batch_id=$data[2];
	$tblRow=0;
	
	$po_array=array(); $po_item_array=array(); $po_item_color_qty=array();

	$po_data_array=sql_select( "select b.gmts_item_id, b.id, b.order_no as po_number, b.order_uom, b.gmts_color_id, b.order_quantity as po_qnty from subcon_ord_mst a, subcon_ord_dtls b where a.subcon_job=b.job_no_mst and a.id=b.mst_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.entry_form=295");

	foreach($po_data_array as $poRow)
	{
		$po_array[$poRow[csf('id')]]=$poRow[csf('po_number')];
		$po_item_array[$poRow[csf('id')]]=$poRow[csf('gmts_item_id')];
		
		$po_item_color_qty[$poRow[csf("id")]][$poRow[csf("gmts_item_id")]][$poRow[csf("gmts_color_id")]]["qty"]=$poRow[csf("po_qnty")];
    	$po_item_color_qty[$poRow[csf("id")]][$poRow[csf("gmts_item_id")]][$poRow[csf("gmts_color_id")]]["uom"]=$poRow[csf("order_uom")];
	}

	$batch_qty_arr=array();
	$batch_dtls_sql="select a.color_id, b.po_id, b.prod_id, sum(b.roll_no) as roll_no from pro_batch_create_mst a, pro_batch_create_dtls b where a.id=b.mst_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 group by b.po_id, b.prod_id,a.color_id";
	$batchArray = sql_select($batch_dtls_sql);
	foreach ($batchArray as $value) 
	{
		$batch_qty_arr[$value[csf("po_id")]][$value[csf("prod_id")]][$value[csf("color_id")]]=$value[csf("roll_no")];
	}

	$issue_qty_arr=array();
	$issue_dtls_sql="select b.quantity,b.uom,b.job_dtls_id, b.buyer_po_id from sub_material_mst a, sub_material_dtls b where  a.entry_form=297 and a.id=b.mst_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 ";
	$issueArray = sql_select($issue_dtls_sql);
	foreach ($issueArray as $value) 
	{
		$issue_qty_arr[$value[csf("job_dtls_id")]]+=$value[csf("quantity")];
	}

	/*$batch_qty_arr=array();
    $batch_dtls_sql="select a.color_id, b.po_id, b.prod_id, sum(b.roll_no) as roll_no from pro_batch_create_mst a, pro_batch_create_dtls b where a.id=b.mst_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.entry_form=316 group by b.po_id, b.prod_id, a.color_id";
    $batchArray = sql_select($batch_dtls_sql);
    foreach ($batchArray as $value) 
    {
    	$batch_qty_arr[$value[csf("po_id")]][$value[csf("prod_id")]][$value[csf("color_id")]]=$value[csf("roll_no")];
    }*/
	
	$data_array=sql_select("select a.color_id, b.id, b.po_id, b.prod_id, b.roll_no, b.batch_qnty, b.buyer_po_id from pro_batch_create_mst a, pro_batch_create_dtls b where b.mst_id=$batch_id and a.id=b.mst_id and b.status_active=1 and b.is_deleted=0 order by b.id"); 
	foreach($data_array as $row)
	{
		$tblRow++;
		$issQty=$prevBatchQty=$currBatchQty=$chkgmtsqty=0;
		$gmts_item_array=array();
		$item_array=explode(",",$po_item_array[$row[csf('po_id')]]);
		foreach($item_array as $item)
		{
			$gmts_item_array[$item]=$garments_item[$item];
		}		
		
		/*$need_multiply=($po_item_color_qty[$row[csf("po_id")]][$row[csf("prod_id")]][$row[csf("color_id")]]["uom"]==2)?12:1;

		$chkgmtsqty=($po_item_color_qty[$row[csf("po_id")]][$row[csf("prod_id")]][$row[csf("color_id")]]["qty"]*$need_multiply-$batch_qty_arr[$row[csf("po_id")]][$row[csf("prod_id")]][$row[csf("color_id")]])+$row[csf('roll_no')];*/
		
		//750**451**251**48 
		$issQty=$issue_qty_arr[$row[csf("po_id")]];
		$prevBatchQty=$batch_qty_arr[$row[csf("po_id")]][$row[csf("prod_id")]][$row[csf("color_id")]];
		$currBatchQty=$row[csf('roll_no')];
		$chkgmtsqty=(($issQty-$prevBatchQty)+$currBatchQty);
		//echo $issue_qty_arr[$row[csf("po_id")]]."**".$batch_qty_arr[$row[csf("po_id")]][$row[csf("prod_id")]][$row[csf("color_id")]]."**".$row[csf('roll_no')]."**".$chkgmtsqty;
		?>
		<tr class="general" id="tr_<? echo $tblRow; ?>">
            <td>
                <input type="text" name="txtPoNo_<? echo $tblRow; ?>" id="txtPoNo_<? echo $tblRow; ?>" class="text_boxes" style="width:130px;" placeholder="Double Click to Search" onDblClick="openmypage_po(<? echo $tblRow; ?>)" value="<? echo $po_array[$row[csf('po_id')]]; ?>" readonly />
                <input type="hidden" name="poId_<? echo $tblRow; ?>" id="poId_<? echo $tblRow; ?>" value="<? echo $row[csf('po_id')]; ?>"/>
                <input type="hidden" name="updateIdDtls_<? echo $tblRow; ?>" id="updateIdDtls_<? echo $tblRow; ?>" value="<? echo $row[csf('id')]; ?>" />
                <input type="hidden" name="buyerPoId_<? echo $tblRow; ?>" id="buyerPoId_<? echo $tblRow; ?>" value="<? echo $row[csf('buyer_po_id')]; ?>" />
            </td>
            <td>
                <? echo create_drop_down( "cboItem_".$tblRow, 180, $gmts_item_array,"", 1, "-- Select Item --", $row[csf('prod_id')], "load_color_list($tblRow)",''); ?>
            </td>
			<td>
				<input type="text" name="txtGmtsQty_<? echo $tblRow; ?>" id="txtGmtsQty_<? echo $tblRow; ?>" class="text_boxes_numeric" onKeyUp="calculate_batch_qnty();" style="width:75px" value="<? echo $row[csf('roll_no')]; ?>"/>

				<input type="hidden" name="chkgmts_<? echo $tblRow; ?>" id="chkgmts_<? echo $tblRow; ?>" class="text_boxes" value="<? echo $chkgmtsqty; ?>" readonly />
			</td>
			<td>
				<input type="text" name="txtBatchQnty_<? echo $tblRow; ?>"  id="txtBatchQnty_<? echo $tblRow; ?>" class="text_boxes_numeric" onKeyUp="calculate_batch_qnty();" style="width:75px" value="<? echo $row[csf('batch_qnty')]; ?>"/>
			</td>
			<td width="65">
				<input type="button" id="increase_<? echo $tblRow; ?>" name="increase_<? echo $tblRow; ?>" style="width:30px" class="formbuttonplasminus" value="+" onClick="add_break_down_tr(<? echo $tblRow; ?>)" />
				<input type="button" id="decrease_<? echo $tblRow; ?>" name="decrease_<? echo $tblRow; ?>" style="width:30px" class="formbuttonplasminus" value="-" onClick="fn_deleteRow(<? echo $tblRow; ?>);" />
			</td>
		</tr>
	<?
	}
	exit();
}

if($action=="process_name_popup")
{
  	echo load_html_head_contents("Process Name Info","../../../", 1, 1, '','1','');
	extract($_REQUEST);
	?>
		<script>
		
			$(document).ready(function(e) {
				setFilterGrid('tbl_list_search',-1);
			});
			
			var selected_id = new Array(); var selected_name = new Array();
			
			function check_all_data() 
			{
				var tbl_row_count = document.getElementById( 'tbl_list_search' ).rows.length; 
	
				tbl_row_count = tbl_row_count-1;
				for( var i = 1; i <= tbl_row_count; i++ ) {
					js_set_value( i );
				}
			}
			
			function toggle( x, origColor ) {
				var newColor = 'yellow';
				if ( x.style ) {
					x.style.backgroundColor = ( newColor == x.style.backgroundColor )? origColor : newColor;
				}
			}
			
			function set_all()
			{
				var old=document.getElementById('txt_process_row_id').value; 
				if(old!="")
				{   
					old=old.split(",");
					for(var k=0; k<old.length; k++)
					{   
						js_set_value( old[k] ) 
					} 
				}
			}
			
			function js_set_value( str ) 
			{
				
				toggle( document.getElementById( 'search' + str ), '#FFFFCC' );
				
				if( jQuery.inArray( $('#txt_individual_id' + str).val(), selected_id ) == -1 ) {
					selected_id.push( $('#txt_individual_id' + str).val() );
					selected_name.push( $('#txt_individual' + str).val() );
					
				}
				else {
					for( var i = 0; i < selected_id.length; i++ ) {
						if( selected_id[i] == $('#txt_individual_id' + str).val() ) break;
					}
					selected_id.splice( i, 1 );
					selected_name.splice( i, 1 );
				}
				
				var id = ''; var name = '';
				for( var i = 0; i < selected_id.length; i++ ) {
					id += selected_id[i] + ',';
					name += selected_name[i] + ',';
				}
				
				id = id.substr( 0, id.length - 1 );
				name = name.substr( 0, name.length - 1 );
				
				$('#hidden_process_id').val(id);
				$('#hidden_process_name').val(name);
			}
		</script>
	</head>
	<body>
	<div align="center">
		<fieldset style="width:370px;margin-left:10px">
			<input type="hidden" name="hidden_process_id" id="hidden_process_id" class="text_boxes" value="">
			<input type="hidden" name="hidden_process_name" id="hidden_process_name" class="text_boxes" value="">
			<form name="searchprocessfrm_1"  id="searchprocessfrm_1" autocomplete="off">
				<table cellspacing="0" cellpadding="0" border="1" rules="all" width="350" class="rpt_table" >
					<thead>
						<th width="50">SL</th>
						<th>Process Name</th>
					</thead>
				</table>
				<div style="width:350px; overflow-y:scroll; max-height:280px;" id="buyer_list_view" align="center">
					<table cellspacing="0" cellpadding="0" border="1" rules="all" width="332" class="rpt_table" id="tbl_list_search" >
					<?
						$i=1; $process_row_id=''; 
	
						$hidden_process_id=explode(",",$txt_process_id);
						foreach($wash_type as $id=>$name)
						{
							if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
							 
							if(in_array($id,$hidden_process_id)) 
							{ 
								if($process_row_id=="") $process_row_id=$i; else $process_row_id.=",".$i;
							}
							?>
							<tr bgcolor="<? echo $bgcolor; ?>" style="text-decoration:none; cursor:pointer" id="search<? echo $i;?>" onClick="js_set_value(<? echo $i;?>)"> 
								<td width="50" align="center"><?php echo "$i"; ?>
									<input type="hidden" name="txt_individual_id" id="txt_individual_id<?php echo $i ?>" value="<? echo $id; ?>"/>	
									<input type="hidden" name="txt_individual" id="txt_individual<?php echo $i ?>" value="<? echo $name; ?>"/>
									<input type="hidden" name="txt_mandatory" id="txt_mandatory<?php echo $i ?>" value="<? echo $mandatory; ?>"/>
								</td>	
								<td><p><? echo $name; ?></p></td>
							</tr>
							<?
							$i++;
						}
					?>
						<input type="hidden" name="txt_process_row_id" id="txt_process_row_id" value="<?php echo $process_row_id; ?>"/>
					</table>
				</div>
				 <table width="350" cellspacing="0" cellpadding="0" style="border:none" align="center">
					<tr>
						<td align="center" height="30" valign="bottom">
							<div style="width:100%"> 
								<div style="width:50%; float:left" align="left">
									<input type="checkbox" name="check_all" id="check_all" onClick="check_all_data()" /> Check / Uncheck All
								</div>
								<div style="width:50%; float:left" align="left">
									<input type="button" name="close" onClick="parent.emailwindow.hide();" class="formbutton" value="Close" style="width:100px" />
								</div>
							</div>
						</td>
					</tr>
				</table>
			</form>
		</fieldset>
	</div>    
	</body>           
	<script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
	<script>set_all();</script>
	</html>
	<?
	exit();
}

if($action=="batch_no_creation")
{
	$batch_no_creation=return_field_value("batch_no_creation","variable_settings_production","company_name ='$data' and variable_list=24 and is_deleted=0 and status_active=1");

	if($batch_no_creation!=1) $batch_no_creation=0;
	
	echo "document.getElementById('batch_no_creation').value 				= '".$batch_no_creation."';\n";
	echo "$('#txt_batch_number').val('');\n";
	echo "$('#update_id').val('');\n";
	if($batch_no_creation==1)
	{
		echo "$('#txt_batch_number').attr('readonly','readonly');\n";
	}
	else
	{
		echo "$('#txt_batch_number').removeAttr('readonly','readonly');\n";
	}
	
	exit();	
}

if($action=="batch_card_print") 
{
	extract($_REQUEST);
	$data=explode('*',$data);
	$company=$data[0];
	$batchagainst=$data[5];
	$batch_update_id=$data[1];
	//$batch_mst_update_id=str_pad($batch_update_id,10,'0',STR_PAD_LEFT);
	$batch_mst_update_id=$data[3];
	$batch_sl_no=$data[2];
	//echo $data[0]."**".$data[1]."**".$data[2]."**".$data[3]."**".$data[4]."**".$data[5];die;
	$company_library=return_library_array( "select id, company_name from lib_company", "id", "company_name");
	$color_arr=return_library_array( "select id, color_name from lib_color", "id", "color_name");
	$machine_arr=return_library_array( "select id, machine_no from lib_machine_name",'id','machine_no');
	$party_arr=return_library_array( "select id, buyer_name from lib_buyer",'id','buyer_name');

	$sql_arr=sql_select("select a.party_id, b.id, b.order_no, b.cust_style_ref, b.cust_buyer from subcon_ord_mst a, subcon_ord_dtls b where a.subcon_job=b.job_no_mst");
	foreach ($sql_arr as $row) 
	{
		$order_no_arr[$row[csf('id')]]		=$row[csf('order_no')];
		$cust_buyer[$row[csf('id')]]		=$row[csf('cust_buyer')];
		$cust_style_ref[$row[csf('id')]]	=$row[csf('cust_style_ref')];
		$party_id[$row[csf('id')]]			=$row[csf('party_id')];
	}

	$dataArray=sql_select("select a.color_id, a.batch_date, a.batch_against, a.shift_id, a.batch_weight, a.dyeing_machine, a.process_id, a.remarks, a.operator_name, a.supervisor_name, b.po_id, b.roll_no from pro_batch_create_mst a, pro_batch_create_dtls b where a.id=$data[1] and a.id=b.mst_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0");
	//print_r($dataArray);

	$order_num=array(); $cust_buyer_arr=array(); $cust_style_ref_arr=array(); $party_id_arr=array(); $Gmts_qty=0;
	foreach ($dataArray as $value) 
	{
		$order_num[]			=$order_no_arr[$value[csf('po_id')]];
		$cust_buyer_arr[]		=$cust_buyer[$value[csf('po_id')]];
		$cust_style_ref_arr[]	=$cust_style_ref[$value[csf('po_id')]];
		$party_id_arr[]			=$party_arr[$party_id[$value[csf('po_id')]]];
		$Gmts_qty+=$value[csf('roll_no')];
	}
	?>
	<table width="580" cellspacing="0" align="center" border="0">
		<tr>
			<td colspan="5" align="center" style="font-size:22px"><strong><? echo $company_library[$company]; ?></strong></td>
		</tr>
		<tr>
			<td colspan="4" align="center" style="font-size:16px;">
				<strong >Batch Card - <? echo $batch_against[$dataArray[0][csf("batch_against")]]; ?></strong>
			</td>
		</tr>
	</table>
	<br>
	<table width="580" cellspacing="0" align="center" border="1" rules="all" class="rpt_table" >
		<tr>
			<td style="font-size:14px"><strong>Batch No.</strong></td>
			<td> <? echo $data[3]; ?> </td>	
			<td style="font-size:14px"><strong>Buyer</strong></td>
			<td><? echo implode(",", array_unique($party_id_arr)); ?></td>		
		</tr>

		<tr>
			<td style="font-size:14px"><strong>Batch Date</strong></td>
			<td> <? echo change_date_format($dataArray[0][csf("batch_date")]); ?> </td>	
			<td style="font-size:14px"><strong>Shift</strong></td>	
			<td> <? echo $shift_name[$dataArray[0][csf("shift_id")]]; ?> </td>		
		</tr>

		<tr>
			<td style="font-size:14px"><strong>Party Style</strong></td>
			<td><? echo implode(",", array_filter($cust_style_ref_arr)); ?></td>
			<td style="font-size:14px"><strong>Batch Color</strong></td>	
			<td> <? echo $color_arr[$dataArray[0][csf("color_id")]]; ?> </td>
		</tr>

		<tr>
			<td style="font-size:14px"><strong>Weight</strong></td>
			<td> <? echo $dataArray[0][csf("batch_weight")]; ?> </td>
			<td style="font-size:14px"><strong>Wash Type</strong></td>	
			<td>
				<? 
					$process_id_array=explode(",",$dataArray[0][csf("process_id")]);
					foreach($process_id_array as $val)
					{
						if($process_name=="") $process_name=$wash_type[$val]; else $process_name.=",".$wash_type[$val];
					}
					echo $process_name;
				?>
			</td>	
		</tr>

		<tr>
			<td style="font-size:14px"><strong>M/C No.</strong></td>
			<td> <? echo $machine_arr[$dataArray[0][csf("dyeing_machine")]]; ?> </td>
			<td style="font-size:14px"><strong>Total Gmt Qty(Pcs)</strong></td>	
			<td><? echo $Gmts_qty; ?></td>
		</tr>

		<tr>
			<td style="font-size:14px"><strong>Operator</strong></td>
			<td> <? echo $dataArray[0][csf("operator_name")]; ?> </td>		
			<td style="font-size:14px"><strong>Dryer No.</strong></td>	
			<td></td>		
		</tr>

		<tr>
			<td style="font-size:14px"><strong>Supervisor</strong></td>
			<td> <? echo $dataArray[0][csf("supervisor_name")]; ?> </td>
			<td style="font-size:14px"><strong>Dryer Operator</strong></td>	
			<td></td>		
		</tr>

		<tr>
			<td style="font-size:14px"><strong>Remarks</strong></td>
			<td colspan="3"> <? echo $dataArray[0][csf("remarks")]; ?> </td>		
		</tr>
	</table>

	<script type="text/javascript" src="../../../js/jquery.js"></script>
    <!-- <script type="text/javascript" src="../../../js/jquerybarcode.js"></script> -->
    <!-- <script>
    		function generateBarcode( valuess )
    		{
    			var value = valuess;//$("#barcodeValue").val();
    		  	//alert(value)
    			var btype = 'code39';//$("input[name=btype]:checked").val();
    			var renderer ='bmp';// $("input[name=renderer]:checked").val();
    			 
    			var settings = {
    			  output:renderer,
    			  bgColor: '#FFFFFF',
    			  color: '#000000',
    			  barWidth: 1,
    			  barHeight: 30,
    			  moduleSize:5,
    			  posX: 10,
    			  posY: 20,
    			  addQuietZone: 1
    			};
    			//$("#barcode_img_id").html('11');
    			 value = {code:value, rect: false};
    			
    			$("#barcode_img_id").show().barcode(value, btype, settings);
    		} 
    		generateBarcode('<? echo $batch_mst_update_id; ?>');
    	</script> -->
	
	<?
}

/*if($action=="batch_card_print") //backup
{
	extract($_REQUEST);
	$data=explode('*',$data);
	$company=$data[0];
	$batchagainst=$data[5];
	$batch_update_id=$data[1];
	$batch_mst_update_id=str_pad($batch_update_id,10,'0',STR_PAD_LEFT);
	//echo $batch_mst_update_id;die;
	$batch_sl_no=$data[2];
	//echo $data[3].$data[4];die;
	$company_library=return_library_array( "select id, company_name from lib_company", "id", "company_name");
	
	$job_array=array();
	
	$job_sql="select a.party_id as buyer_name, a.job_no_prefix_num, a.subcon_job as job_no, b.delivery_date as pub_shipment_date, b.id, b.order_no as po_number from subcon_ord_mst a, subcon_ord_dtls b where a.subcon_job=b.job_no_mst and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0";

	$job_sql_result=sql_select($job_sql);
	foreach($job_sql_result as $row)
	{
		$job_array[$row[csf('id')]]['job']=$row[csf('job_no')];
		$job_array[$row[csf('id')]]['po']=$row[csf('po_number')];
		$job_array[$row[csf('id')]]['buyer']=$row[csf('buyer_name')];
		$job_array[$row[csf('id')]]['ship_date']=$row[csf('pub_shipment_date')];
	}
	
	if($db_type==0)
	{
		$sql="select a.id, a.batch_no, a.booking_no_id, a.booking_no,a.booking_without_order,a.color_id, a.batch_against, a.color_range_id, a.organic, a.extention_no, a.total_trims_weight, a.process_id as process_id, a.batch_for, a.batch_weight,a.remarks, group_concat(b.po_id) as po_id, group_concat(b.prod_id) as prod_id from pro_batch_create_mst a, pro_batch_create_dtls b where a.id=b.mst_id and a.id=$batch_update_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 group by a.id, a.batch_no, a.booking_no_id, a.booking_no,a.booking_without_order,a.color_id, a.batch_against, a.color_range_id, a.organic, a.extention_no, a.total_trims_weight, a.process_id, a.batch_for, a.batch_weight,a.remarks";
	}
	else
	{
		$sql="select a.id, a.batch_no, a.booking_no_id,a.booking_no,a.booking_without_order, a.color_id, a.batch_against, a.color_range_id, a.organic, a.extention_no, a.total_trims_weight, a.process_id as process_id, a.batch_for, a.batch_weight,a.remarks, LISTAGG(b.po_id, ',') WITHIN GROUP (ORDER BY b.po_id) AS po_id , LISTAGG(CAST(b.prod_id AS VARCHAR2(4000)),',') WITHIN GROUP (ORDER BY b.prod_id) AS prod_id from pro_batch_create_mst a, pro_batch_create_dtls b where a.id=b.mst_id and a.id=$batch_update_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 group by a.id, a.batch_no, a.color_id, a.batch_against, a.color_range_id, a.organic, a.extention_no, a.booking_no_id, a.booking_no,a.booking_without_order,a.total_trims_weight, a.process_id, a.batch_for, a.batch_weight,a.remarks";	
	}
	//echo $sql;
	$dataArray=sql_select($sql);
	
	$po_number=""; $job_number=""; $buyer_id=""; $ship_date="";
	$po_id=array_unique(explode(",",$dataArray[0][csf('po_id')]));

	$batch_against_id=$dataArray[0][csf('batch_against')];
	$batch_product_id=$dataArray[0][csf('prod_id')];
	foreach($po_id as $val)
	{
		if($po_number=="") $po_number=$job_array[$val]['po']; else $po_number.=', '.$job_array[$val]['po'];
		if($job_number=="") $job_number=$job_array[$val]['job']; else $job_number.=', '.$job_array[$val]['job'];
		if($buyer_id=="") $buyer_id=$buyer_arr[$job_array[$val]['buyer']]; else $buyer_id.=','.$buyer_arr[$job_array[$val]['buyer']];
		if($ship_date=="") $ship_date=change_date_format($job_array[$val]['ship_date']); else $ship_date.=', '.change_date_format($job_array[$val]['ship_date']);
	}
	
	$job_no=implode(",",array_unique(explode(",",$job_number)));
	$buyer_name=implode(",",array_unique(explode(",",$buyer_id)));

?>
    <div style="width:980px;">
     <table width="980" cellspacing="0" align="center" border="0">
        <tr>
            <td colspan="6" align="center" style="font-size:22px"><strong><? echo $company_library[$company]; ?></strong></td>
            <td colspan="2" align="left">Print Time: <? echo $date=date("F j, Y, g:i a"); ?></td>
        </tr>
        <tr>
            <td colspan="6" align="center" style="font-size:18px"><strong><u>Batch Card/<? echo $batch_against[$batch_against_id];?></u></strong></td>
            <td colspan="2" id="barcode_img_id" align="right" style="font-size:24px"></td>
        </tr>
         <tr>
           <td colspan="8">&nbsp; </td> <td>&nbsp; </td>
        </tr>
        <tr>
           <td colspan="6" align="left" style="font-size:18px"><strong><u>Reference Details</u></strong></td>
           <td style="font-size:24px; border: solid 2px;" align="center" colspan="2">&nbsp;<? echo $dataArray[0][csf('organic')];?></td>
        </tr>
        <tr>
            <td width="110"><strong>Batch No</strong></td> <td width="135px">:&nbsp;<? echo $dataArray[0][csf('batch_no')]; ?></td>
            <td width="110"><strong>Batch SL</strong></td><td width="135px">:&nbsp;<? echo $batch_sl_no; ?></td>
            <td width="110"><strong>Batch Color</strong></td><td width="135px">:&nbsp;<? echo $color_arr[$dataArray[0][csf('color_id')]]; ?></td>
            <td width="110"><strong>Color Range</strong></td><td width="135px">:&nbsp;<? echo $color_range[$dataArray[0][csf('color_range_id')]];?></td>
        </tr>
        <tr>
            <td><strong>Batch Against</strong></td><td>:&nbsp;<? echo $batch_against[$dataArray[0][csf('batch_against')]]; ?></td>
            <td><strong>Batch Ext.</strong></td><td>:&nbsp;<? echo $dataArray[0][csf('extention_no')];?></td>
            <td><strong>B. Weight</strong></td><td>:&nbsp;<? echo $dataArray[0][csf('batch_weight')]; ?> Kg</td>
            <td><strong>Buyer</strong></td><td>:&nbsp;<? echo $buyer_name; ?></td>
        </tr>
        <!-- <tr>
            <td><strong>Job</strong></td><td>:&nbsp;<? //echo $job_no; ?></td>
            <td><strong>Order No</strong></td><td>:&nbsp;<? //echo $po_number; ?></td>
            <td><strong>Delivery Date</strong></td><td colspan="2">:&nbsp;<? //echo $ship_date; ?></td>
        </tr> -->
        <tr>
        	<td><strong>Remarks</strong></td><td colspan="7">:&nbsp;<? echo $dataArray[0][csf('remarks')]; ?></td>
        </tr>
    </table>
    <div style="float:left; font-size:17px;"><strong><u>Fabrication Details</u></strong> </div>
    <table align="center" cellspacing="0" width="980"  border="1" rules="all" class="rpt_table" style="border-top:none" >
        <thead bgcolor="#dddddd" align="center">
            <tr>
                <th width="80">SL</th>
                <th width="100">PO No.</th>
                <th width="100">Job No</th>
                <th width="100">Delivery Date</th>
                <th width="250">Gmts. Item</th>
                <th width="200">Gmts. Qty </th>
                <th>Batch Qty. (Kg)</th>
            </tr>
        </thead>
		<?
			$i=1;
			$sql_dtls="select po_id,batch_qnty, roll_no as gmts_qty, prod_id from pro_batch_create_dtls where mst_id=$batch_update_id and status_active=1 and is_deleted=0";
			//echo $sql_dtls;
			$sql_result=sql_select($sql_dtls);
			foreach($sql_result as $row)
			{
				if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
				?>
                <tr bgcolor="<? echo $bgcolor; ?>" >
                    <td><? echo $i; ?></td>
                    <td><? echo $job_array[$row[csf('po_id')]]['po'];?></td>
                    <td><? echo $job_array[$row[csf('po_id')]]['job']; ?></td>
                    <td><? echo change_date_format($job_array[$row[csf('po_id')]]['ship_date']); ?></td>
                    <td><p><? echo $garments_item[$row[csf('prod_id')]]; ?></p></td>
                    <td align="right"><? echo $row[csf('gmts_qty')]; ?></td>
                    <td align="right"><? echo number_format($row[csf('batch_qnty')],2); ?></td>
                </tr>
				<?php
				$total_gmts_qty+= $row[csf('gmts_qty')];
				$total_batch_qty+= $row[csf('batch_qnty')];
				$i++;
			}
			?>
             <tr>
                <td style="border:none;" colspan="5" align="right"><b>Total:</b> </td>
                <td align="right"><b><? echo $total_gmts_qty; ?> </b></td>
                <td align="right"><b><? echo number_format($total_batch_qty,2);  ?> </b></td>
            </tr>
             <tr>
                <td colspan="7" align="right">&nbsp;</td>
            </tr>
         <tr>
            <td colspan="7" align="right">
			<? 

			if($dataArray[0][csf("batch_against")]==6){ $new_subprocess_array= $emblishment_wash_type;}
    		else if($dataArray[0][csf("batch_against")]==10){ $new_subprocess_array= $emblishment_print_type;}
    		else if($dataArray[0][csf("batch_against")]==7){ $new_subprocess_array= $emblishment_gmts_type;}


            $process=$dataArray[0][csf('process_id')];
            $process_id=explode(',',$process);
            //print_r($process_id);
			$process_value='';
			$i=1;
			foreach ($process_id as $val)
			{
				if($process_value=='') 
				{
					$process_value=$i.'. '. $new_subprocess_array[$val];
				} 
				else 
				{
					$process_value.=", ".$i.'. '.$new_subprocess_array[$val];
				}
				$i++;
			}
             ?>
           <table align="left" rules="all" class="rpt_table" width="980">
             <tr>
                 <th  align="left"  style="font-size:20px;"><strong>Process Required</strong></th>
            </tr>
             <tr>
                   <td  style="font-size:20px;" title="<? echo $process_value; ?>"> 
                <p><? echo $process_value; ?></p>
                  </td>
            </tr>
            <tr>
             <td align="left" style="font-size:19px;"> 
          		Heat Setting:&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; &nbsp; &nbsp;&nbsp;&nbsp;&nbsp; &nbsp;&nbsp;&nbsp;&nbsp;&nbsp; &nbsp;&nbsp;&nbsp;&nbsp; &nbsp;&nbsp;&nbsp;&nbsp;&nbsp; &nbsp;   Loading Date: &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; &nbsp; &nbsp;&nbsp;&nbsp;&nbsp; &nbsp;&nbsp;&nbsp;&nbsp; &nbsp;&nbsp;&nbsp;&nbsp;&nbsp; &nbsp;  UnLoading Date:&nbsp;
             </td>
            </tr>
          </table>
             </td>
    	</tr>
    </table>
    <div style="float:left; margin-left:10px;"><strong> Quality Instruction(Hand Written)</strong> </div>
    <table width="980" cellspacing="0" align="center" >
        <tr>
            <td valign="top" align="left" width="480">
                <table cellspacing="0" width="475"  align="left" border="1" rules="all" class="rpt_table">
                    <tr>
                        <th>SL</th><th>Roll No</th><th>Roll Mark</th><th>Actual Dia</th><th>Roll Wgt.</th><th>Remarks</th>
                    </tr>
                    <tr>
                        <td>&nbsp; </td><td>&nbsp; </td><td>&nbsp; </td><td>&nbsp; </td><td>&nbsp; </td><td>&nbsp; </td>
                    </tr>
                    <tr>
                        <td>&nbsp; </td><td>&nbsp; </td><td>&nbsp; </td><td>&nbsp; </td><td>&nbsp; </td><td>&nbsp; </td>
                    </tr>
                    <tr>
                        <td>&nbsp; </td><td>&nbsp; </td><td>&nbsp; </td><td>&nbsp; </td><td>&nbsp; </td><td>&nbsp; </td>
                    </tr>
                    <tr>
                        <td>&nbsp; </td><td>&nbsp; </td><td>&nbsp; </td><td>&nbsp; </td><td>&nbsp; </td><td>&nbsp; </td>
                    </tr>
                     <tr>
                        <td>&nbsp; </td><td>&nbsp; </td><td>&nbsp; </td><td>&nbsp; </td><td>&nbsp; </td><td>&nbsp; </td>
                    </tr>
                     <tr>
                        <td>&nbsp; </td><td>&nbsp; </td><td>&nbsp; </td><td>&nbsp; </td><td>&nbsp; </td><td>&nbsp; </td>
                    </tr>
                </table>
            </td>
            <td width="10" align="justify" valign="top"></td>
            <td width="480" valign="top" align="right">
                <table width="475"  cellspacing="0"  border="1" rules="all" class="rpt_table">
                    <tr>
                        <th>SL</th><th>Roll No</th><th>Actual Dia</th><th>Roll Wgt.</th><th>Remarks</th>
                    </tr>
                    <tr>
                        <td>&nbsp; </td><td>&nbsp; </td><td>&nbsp; </td><td>&nbsp; </td><td>&nbsp; </td>
                    </tr>
                    <tr>
                        <td>&nbsp; </td><td>&nbsp; </td><td>&nbsp; </td><td>&nbsp; </td><td>&nbsp; </td>
                    </tr>
                    <tr>
                        <td>&nbsp; </td><td>&nbsp; </td><td>&nbsp; </td><td>&nbsp; </td><td>&nbsp; </td>
                    </tr>
                    <tr>
                        <td>&nbsp; </td><td>&nbsp; </td><td>&nbsp; </td><td>&nbsp; </td><td>&nbsp; </td>
                    </tr>
                </table>
            </td>
        </tr>
        <tr>
            <td width="480" valign="top">
                <table width="475" cellspacing="0" border="1" rules="all" class="rpt_table">
                    <tr>
                        <th align="left"><strong>Shade Result(<i>Hand Written</i>)</strong></th>
                    </tr>
                    <tr>
                        <td colspan="1" style="width:475px; height:80px" >&nbsp;</td>
                    </tr>
                </table>
        	</td>
            <td width="10" align="justify" valign="top">&nbsp;</td>
            <td width="480" valign="top" align="right">
                <table cellspacing="0" border="1" rules="all" class="rpt_table" width="475" >
                    <tr>
                        <th align="left" colspan="3"><strong>Shrinkage(<i>Hand Written</i>)</strong></th>
                    </tr>
                    <tr>
                        <th><b>Length % </b></th><th><b>Width % </b></th><th><b> Twist % </b></th>
                    </tr>
                    <tr>
                        <td>&nbsp; </td><td>&nbsp; </td><td>&nbsp; </td>
                    </tr>
                    <tr>
                        <td>&nbsp; </td><td>&nbsp; </td><td>&nbsp; </td>
                    </tr>
                    <tr>
                        <td>&nbsp; </td><td>&nbsp; </td><td>&nbsp; </td>
                    </tr>
                    <tr>
                        <td>&nbsp; </td><td>&nbsp; </td><td>&nbsp; </td>
                    </tr>
                </table>
            </td>
        </tr>
        <tr>
            <td width="980" colspan="3">
                <table cellspacing="0" border="1" rules="all" class="rpt_table" width="980" >
                    <tr>
                        <th align="center"><strong>Other Information(<i>Hand Written</i>)</strong></th>
                    </tr>
                    <tr>
                        <td style="width:980px; height:120px" >&nbsp;</td>
                    </tr>
                </table> 
            </td>
        </tr>
    </table>
     <br>
		 <?
            echo signature_table(52, $company, "980px");
         ?>
    </div>
   <script type="text/javascript" src="../../js/jquery.js"></script>
     <script type="text/javascript" src="../../js/jquerybarcode.js"></script>
     <script>
		function generateBarcode( valuess )
		{
			var value = valuess;//$("#barcodeValue").val();
		  //alert(value)
			var btype = 'code39';//$("input[name=btype]:checked").val();
			var renderer ='bmp';// $("input[name=renderer]:checked").val();
			 
			var settings = {
			  output:renderer,
			  bgColor: '#FFFFFF',
			  color: '#000000',
			  barWidth: 1,
			  barHeight: 30,
			  moduleSize:5,
			  posX: 10,
			  posY: 20,
			  addQuietZone: 1
			};
			//$("#barcode_img_id").html('11');
			 value = {code:value, rect: false};
			
			$("#barcode_img_id").show().barcode(value, btype, settings);
		} 
		generateBarcode('<? echo $batch_mst_update_id; ?>');
	</script>
<?
exit();
}*/

if($action=="machineNo_popup")
{
	echo load_html_head_contents("Popup Info","../../../", 1, 1, $unicode);
	extract($_REQUEST);
	$cbo_company_id=str_replace("'","",$cbo_company_id); 
	$cbo_batch_against=str_replace("'","",$cbo_batch_against);

	if($cbo_batch_against==6){$category=6;}
	//else if($cbo_batch_against==7){$category=2;}
	//else if($cbo_batch_against==10){$category=3;}

	?>
    <script>
    function js_set_value(data)
    {
		var data=data.split("_");
		$("#hidden_machine_id").val(data[0]);
		$("#hidden_machine_name").val(data[1]); 
		parent.emailwindow.hide();
    }
	</script>
    
    <input type="hidden" id="hidden_machine_id" name="hidden_machine_id">
    <input type="hidden" id="hidden_machine_name" name="hidden_machine_name">
    
<? 
	 $location_name=return_library_array( "select location_name,id from  lib_location where is_deleted=0", "id", "location_name"  );
	 $floor=return_library_array( "select floor_name,id from  lib_prod_floor where is_deleted=0", "id", "floor_name"  );
	 $arr=array(0=>$location_name,1=>$floor);  
	 
	 $sql="select location_id,floor_id,machine_no,machine_group,dia_width,gauge,id from lib_machine_name where is_deleted=0 and status_active=1 and company_id='$cbo_company_id' and category_id in ($category)";
     echo create_list_view ( "list_view", "Location Name,Floor Name,Machine No,Machine Group,Dia Width,Gauge", "150,140,100,120,80","740","300",1, $sql, "js_set_value", "id,machine_no","", 1, "location_id,floor_id,0,0,0,0", $arr, "location_id,floor_id,machine_no,machine_group,dia_width,gauge", "", 'setFilterGrid("list_view",-1);','') ;

	exit();	 
}

if($action == "show_color_listview")
{
	$data = explode("*", $data);
	$poId = $data[0];
	$cboItem = $data[1];
	$rowNum = $data[2];
	$batch_no = $data[3];

	$batch_qty_arr=array();
	$batch_dtls_sql="select a.color_id, b.po_id, b.prod_id, sum(b.roll_no) as roll_no from pro_batch_create_mst a, pro_batch_create_dtls b where a.id=b.mst_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 group by b.po_id, b.prod_id,a.color_id";
	$batchArray = sql_select($batch_dtls_sql);
	foreach ($batchArray as $value) 
	{
		$batch_qty_arr[$value[csf("po_id")]][$value[csf("prod_id")]][$value[csf("color_id")]]=$value[csf("roll_no")];
	}

	$issue_qty_arr=array();
	$issue_dtls_sql="select b.quantity,b.uom,b.job_dtls_id, b.buyer_po_id from sub_material_mst a, sub_material_dtls b where  a.entry_form=297 and a.id=b.mst_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 ";
	$issueArray = sql_select($issue_dtls_sql);
	foreach ($issueArray as $value) 
	{
		$issue_qty_arr[$value[csf("job_dtls_id")]]+=$value[csf("quantity")];
	}

	/*$issue_qty_arr=array();
	$issue_dtls_sql="select b.quantity,b.uom,b.job_dtls_id,a.embl_job_no,c.gmts_color_id,c.buyer_po_no,c.mst_id,c.buyer_po_id,c.job_no_mst from sub_material_mst a, sub_material_dtls b,subcon_ord_dtls c where  a.entry_form=297 and a.id=b.mst_id and  c.id=b.job_dtls_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 ";
	
	$issueArray = sql_select($issue_dtls_sql);
	foreach ($issueArray as $value) 
	{
		$issue_qty_arr[$value[csf("mst_id")]][$value[csf("buyer_po_no")]][$value[csf("gmts_color_id")]]+=$value[csf("quantity")];
	}
*/
	//$sql="select b.order_uom,c.order_id, c.item_id, c.color_id, sum(c.qnty) as po_qnty from subcon_ord_mst a, subcon_ord_dtls b, subcon_ord_breakdown c where a.id=c.mst_id and b.id=c.order_id and a.subcon_job=b.job_no_mst and c.order_id in ($poId) and c.item_id in ($cboItem) group by c.order_id, c.color_id, c.item_id,b.order_uom";
	$sql="select b.order_uom, b.id,b.mst_id, b.order_no, b.gmts_item_id as item_id, b.gmts_color_id as color_id, b.order_quantity as po_qnty,a.within_group, a.party_id,b.buyer_po_id,b.buyer_po_no,a.gmts_type from subcon_ord_mst a, subcon_ord_dtls b where a.id=b.mst_id and a.subcon_job=b.job_no_mst and a.entry_form=295 and b.id in ($poId) and b.gmts_item_id in ($cboItem) order by id DESC";
	
	//$sql = "select a.subcon_job as job_no, a.party_id as buyer_name, b.order_uom, b.gmts_item_id as gmts_item_id, b.gmts_color_id as color_id, b.id, b.order_no as po_number, b.order_quantity as po_qnty from subcon_ord_mst a, subcon_ord_dtls b where a.subcon_job=b.job_no_mst and a.id=b.mst_id and a.entry_form=295 and a.company_id=$company_id $color_cond $party_cond and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 order by b.id DESC";

	$i = 1;
	$nameArray = sql_select($sql);
	?>
	<table cellspacing="0" cellpadding="0" border="1" rules="all" width="330" class="rpt_table color_tble" style="float: left;">
		<thead>
			<th width="25">SL</th>
			<th width="60">PO No</th>
			<th width="100">Gmts Item</th>
			<th width="80">Batch Color</th>
			<th width="75">Meterial Issue Qty (Pcs)</th>
			<th width="75">Total Batch Qty (Pcs)</th>     
			<th width="">Balance (Pcs)</th>              
		</thead>
		<tbody>
		<?
		foreach ($nameArray as $selectResult) 
		{
			if ($i % 2 == 0) $bgcolor = "#E9F3FF"; else $bgcolor = "#FFFFFF";
			//echo $selectResult[csf("order_id")]."**".$selectResult[csf("item_id")]."**".$selectResult[csf("color_id")];
			//echo $batch_qty_arr[$selectResult[csf("order_id")]][$selectResult[csf("item_id")]][$selectResult[csf("color_id")]];
			/*if($selectResult[csf('order_uom')]==2) $need_multiply=12; else $need_multiply=1;
			$batch_balance=($selectResult[csf('po_qnty')]*$need_multiply)-$batch_qty_arr[$selectResult[csf("order_id")]][$selectResult[csf("item_id")]][$selectResult[csf("color_id")]];*/
			
			
			//$issue_qty=$issue_qty_arr[$selectResult[csf("mst_id")]][$selectResult[csf("buyer_po_no")]][$selectResult[csf("color_id")]];
			//$issue_qty_arr[$selectResult[csf("id")]];
			
			$batch_balance=($issue_qty_arr[$selectResult[csf("id")]])-$batch_qty_arr[$selectResult[csf("id")]][$selectResult[csf("item_id")]][$selectResult[csf("color_id")]];
			 
				//$batch_balance=($issue_qty)-$batch_qty_arr[$selectResult[csf("id")]][$selectResult[csf("item_id")]][$selectResult[csf("color_id")]];
			?>
			<tr bgcolor="<? echo $bgcolor; ?>" style="text-decoration:none; cursor:pointer" id="search<? echo $i; ?>" onClick="color_set_value('<? echo $color_arr[$selectResult[csf('color_id')]]; ?>','<? echo $batch_balance; ?>','<? echo $rowNum; ?>','<? echo $selectResult[csf('within_group')]; ?>','<? echo $selectResult[csf('party_id')]; ?>','<? echo $selectResult[csf('buyer_po_id')]; ?>','<? echo $selectResult[csf('gmts_type')]; ?>')"> 
				<td align="center"><? echo $i; ?></td>

				<td align="center"><? echo $selectResult[csf('order_no')]; ?></td>
				<td align="center"><? echo $garments_item[$selectResult[csf('item_id')]]; ?></td>

				<td title="<? echo $selectResult[csf('color_id')]; ?>"><p><? echo $color_arr[$selectResult[csf('color_id')]]; ?></p></td>
				<td align="center"><p><? echo $issue_qty_arr[$selectResult[csf("id")]]; ?></p></td>
				<td align="center"><p><? echo $batch_qty_arr[$selectResult[csf("id")]][$selectResult[csf("item_id")]][$selectResult[csf("color_id")]]; ?></p></td>
				<td align="center"><p><? echo $batch_balance; ?></p></td>
			</tr>
			<?
			$i++;
		}
		?>
		</tbody>
	</table>
	<?
	exit();
}
