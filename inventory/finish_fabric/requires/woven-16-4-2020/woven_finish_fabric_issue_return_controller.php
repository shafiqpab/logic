<?
header('Content-type:text/html; charset=utf-8');
session_start();
include('../../../includes/common.php');

$user_id = $_SESSION['logic_erp']["user_id"];
if( $_SESSION['logic_erp']['user_id'] == "" ) { header("location:login.php"); die; }
$permission=$_SESSION['page_permission'];

$data=$_REQUEST['data'];
$action=$_REQUEST['action'];

if($action=='roll_maintained')
{
	$cbo_company_name=$data;
	$variable_setting_production=return_field_value("fabric_roll_level","variable_settings_production","company_name='$cbo_company_name' and item_category_id=3 and variable_list=3 and status_active=1","fabric_roll_level");
	if($variable_setting_production==1)
	{
		echo "$('#roll_maintained').val($variable_setting_production);\n";
	}
	else
	{
		echo "$('#roll_maintained').val(0);\n";
	}
	exit();
}

if ($action=="load_drop_down_store")
{	  
	echo create_drop_down("cbo_store_name",170, "select a.id, a.store_name from lib_store_location a, lib_store_location_category b where a.id= b.store_location_id and a.company_id='$data' and b.category_type=3 and a.status_active=1 and a.is_deleted=0 group by a.id, a.store_name order by a.store_name","id,store_name", 1, "-- Select store --", 0,"", 0);  	 
	exit();
}

if($action=="load_drop_down_sewing_com")
{
	$data = explode("_",$data);
	$company_id=$data[1];

	if($data[0]==1)
	{
		echo create_drop_down( "cbo_sewing_company", 150, "select comp.id, comp.company_name from lib_company comp where comp.status_active=1 and comp.is_deleted=0 $company_cond order by comp.company_name","id,company_name",1, "--Select Sewing Company--", "$company_id", "","" );
	}
	else if($data[0]==3)
	{
		echo create_drop_down( "cbo_sewing_company", 150, "select a.id,a.supplier_name from lib_supplier a, lib_supplier_party_type b where a.id=b.supplier_id and b.party_type=21 and a.status_active=1 group by a.id,a.supplier_name order by a.supplier_name","id,supplier_name", 1, "--Select Sewing Company--", 1, "" );
	}
	else
	{
		echo create_drop_down( "cbo_sewing_company", 150, $blank_array,"",1, "--Select Sewing Company--", 1, "" );
	}
	exit();
}



if($action=="mrr_popup")
{
	echo load_html_head_contents("Popup Info","../../../", 1, 1, $unicode);
	extract($_REQUEST);  
?>
     
<script>
	function js_set_value(mrr)
	{
 		$("#hidden_recv_number").val(mrr); // mrr number
		parent.emailwindow.hide();
	}
</script>

</head>

<body>
<div align="center" style="width:100%;" >
<form name="searchorderfrm_1"  id="searchorderfrm_1" autocomplete="off">
	<table width="750" cellspacing="0" cellpadding="0" border="1" class="rpt_table" align="center" rules="all">
            <thead>
                <tr>                	 
                    <th>Search By</th>
                    <th align="center" id="search_by_td_up">Enter Issue Number</th>
                    <th>Date Range</th>
                    <th><input type="reset" name="re_button" id="re_button" value="Reset" style="width:100px" class="formbutton"  /></th>           
                </tr>
            </thead>
            <tbody>
                <tr class="general">
                    <td>
                        <?  
                            $search_by = array(1=>'Issue No',2=>'Challan No');
							$dd="change_search_event(this.value, '0*0', '0*0', '../../') ";
							echo create_drop_down( "cbo_search_by", 120, $search_by,"",0, "--Select--", "",$dd,0 );
                        ?>
                    </td>
                    <td width="" align="center" id="search_by_td">				
                        <input type="text" style="width:130px" class="text_boxes"  name="txt_search_common" id="txt_search_common" />	
                    </td>    
                    <td align="center">
                        <input name="txt_date_from" id="txt_date_from" class="datepicker" style="width:80px" placeholder="From Date" />
                        <input name="txt_date_to" id="txt_date_to" class="datepicker" style="width:80px" placeholder="To Date" />
                    </td> 
                    <td align="center">
                        <input type="button" name="btn_show" class="formbutton" value="Show" onClick="show_list_view ( document.getElementById('cbo_search_by').value+'_'+document.getElementById('txt_search_common').value+'_'+document.getElementById('txt_date_from').value+'_'+document.getElementById('txt_date_to').value+'_'+<? echo $cbo_company_id; ?>, 'create_mrr_search_list_view', 'search_div', 'woven_finish_fabric_issue_return_controller', 'setFilterGrid(\'list_view\',-1)')" style="width:100px;" />				
                    </td>
            </tr>
        	<tr>                  
            	<td align="center" height="40" valign="middle" colspan="5">
					<? echo load_month_buttons(1);  ?>
                    <!-- Hidden field here-->
                     <input type="hidden" id="hidden_recv_number" value="" />
                    <!-- -END-->
                </td>
            </tr>    
            </tbody>
         </tr>         
        </table>    
        <div align="center" style="margin-top:10px" valign="top" id="search_div"> </div> 
        </form>
   </div>
</body>           
<script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
</html>
<?
}

if($action=="create_mrr_search_list_view")
{
	$ex_data = explode("_",$data);
	$txt_search_by = $ex_data[0];
	$txt_search_common = $ex_data[1];
	$fromDate = $ex_data[2];
	$toDate = $ex_data[3];
	$company = $ex_data[4];
	
	$sql_cond="";
	if(trim($txt_search_common)!="")
	{
		if(trim($txt_search_by)==1) // for mrr
		{
			$sql_cond .= " and a.issue_number LIKE '%$txt_search_common'";	
		}
		else if(trim($txt_search_by)==2) // for chllan no
		{
			$sql_cond .= " and a.challan_no LIKE '%$txt_search_common%'";				
 		}		 
 	} 
	
	if( $fromDate!="" && $toDate!="" ) 
	{
		if($db_type==0)
		{
			$sql_cond .= " and a.issue_date  between '".change_date_format($fromDate,'yyyy-mm-dd')."' and '".change_date_format($toDate,'yyyy-mm-dd')."'";
		}
		else
		{
			$sql_cond .= " and a.issue_date  between '".change_date_format($fromDate,'','',1)."' and '".change_date_format($toDate,'','',1)."'";
		}
	}
	if(trim($company)!="") $sql_cond .= " and a.company_id='$company'";
	
	if($db_type==0) $year_field="YEAR(a.insert_date) as year"; 
	else if($db_type==2) $year_field="to_char(a.insert_date,'YYYY') as year";
	else $year_field="";//defined Later
	
	$sql = "select a.id,a.issue_number_prefix_num,a.issue_number,a.issue_basis,a.issue_purpose,$year_field, a.challan_no,a.issue_date,sum(b.cons_quantity) as issue_qnty from inv_transaction b, inv_issue_master a where a.id=b.mst_id and a.entry_form in(19) and a.status_active=1 $sql_cond  and b.transaction_type in(2)  group by  a.id,a.issue_number_prefix_num,a.issue_number,a.issue_basis,a.issue_purpose, a.challan_no,a.issue_date,a.insert_date order by a.id";
	
	//$sql="select id, issue_number, challan_no, company_id, issue_date, issue_purpose, buyer_id, sample_type from inv_issue_master where item_category=3 and company_id=$company_id and $search_field like '$search_string' and entry_form=19 and status_active=1 and is_deleted=0";

	//echo $sql;
	?>
    	<table class="rpt_table" border="1" cellpadding="0" cellspacing="0" rules="all" width="720">
        	<thead>
                <tr>
                	<th width="50">SL</th>
                    <th width="100">MRR No</th>
                    <th width="100">Year</th>
                    <th width="100">Challan No</th>
                    <th width="100">Issue Date</th>
                    <th width="150">Issue Purpose</th>
                    <th>Issue Qnty</th>
                </tr>
            </thead>
        </table>
        
        <div style="width:720px; overflow-y:scroll; max-height:280px;font-size:12px; overflow-x:hidden;" id="scroll_body">
    	<table class="rpt_table" border="1" cellpadding="0" cellspacing="0" rules="all" width="700" id="list_view">
            <tbody>
            <?
			$i=1;
			$sql_result=sql_select($sql);
			foreach($sql_result as $row)
			{
				if ($k%2==0)
				$bgcolor="#E9F3FF";
				else
				$bgcolor="#FFFFFF";

				?>
            	<tr bgcolor="<? echo $bgcolor; ?>" style="cursor:pointer;" onClick="js_set_value('<? echo $row[csf("id")]; ?>_<? echo $row[csf("issue_number")]; ?>_<? echo $row[csf("issue_purpose")]; ?>_<? echo $row[csf("challan_no")]; ?>')">
                	<td width="50" align="center"><p><? echo $i; ?>&nbsp;</p></td>
                    <td width="100" align="center"><p><? echo $row[csf("issue_number_prefix_num")]; ?>&nbsp;</p></td>
                    <td width="100" align="center"><p><? echo $row[csf("year")]; ?>&nbsp;</p></td>
                    <td width="100" ><p><? echo $row[csf("challan_no")]; ?>&nbsp;</p></td>
                    <td width="100" align="center"><p><? if($row[csf("issue_date")]!="" && $row[csf("issue_date")]!="0000-00-00") echo change_date_format($row[csf("issue_date")]); ?>&nbsp;</p></td>
                    <td width="150"><p>
					<?
					echo $yarn_issue_purpose[$row[csf("issue_purpose")]]; 
					 
					?>&nbsp;</p></td>
                    <td align="right"><p><? echo number_format($row[csf("issue_qnty")],2,".",""); ?>&nbsp;</p></td>
                </tr>
                <?
				$i++;
			}
			?>
            </tbody>
        </table>
        </div>
    <?	
	exit();
	
}

if($action=="show_fabric_desc_listview")
{ 
 	$mrr_no = $data;
	$stat_variable = 1; //N.B.  Floor,Room,Rack,Shelf,Bin show with this static variable until variable is created.

	$color_arr=return_library_array( "select id, color_name from lib_color",'id','color_name');
	$batch_no_arr=return_library_array( "select id, batch_no from pro_batch_create_mst",'id','batch_no');
	
	if($stat_variable == 1){
		$iss_ret_field = " ,floor_id,room, rack, self,bin_box ";
		$iss_field = " , d.floor_id, d.room, d.rack,d.self, d.bin_box ";
	}

	$issue_rtn_array=array();
	$issData=sql_select("select issue_id, prod_id, batch_id_from_fissuertn, sum(cons_quantity) as qnty $iss_ret_field from inv_transaction where status_active=1 and is_deleted=0 and transaction_type=4 and issue_id=$mrr_no group by issue_id, prod_id, batch_id_from_fissuertn $iss_ret_field");

	foreach($issData as $row)
	{
		if($stat_variable == 1)
		{
			$issue_rtn_array[$row[csf('issue_id')]][$row[csf('prod_id')]][$row[csf('batch_id_from_fissuertn')]][$row[csf('floor_id')]][$row[csf('room')]][$row[csf('rack')]][$row[csf('self')]][$row[csf('bin_box')]]+=$row[csf('qnty')];
		}else{
			$issue_rtn_array[$row[csf('issue_id')]][$row[csf('prod_id')]][$row[csf('batch_id_from_fissuertn')]] +=$row[csf('qnty')];
		}
	}
	
	$data_array=sql_select("select  a.id as issue_id, b.batch_id as batch_id, b.order_id,b.no_of_roll, sum(b.issue_qnty) as qnty, c.id as prod_id, c.product_name_details, c.current_stock, c.color, c.unit_of_measure $iss_field , b.store_id,a.company_id from inv_issue_master a, inv_wvn_finish_fab_iss_dtls b, product_details_master c, inv_transaction d  where a.id=b.mst_id and b.prod_id=c.id and a.id = d.mst_id and d.transaction_type=2 and d.item_category=3 and a.id='$mrr_no' and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.entry_form in(19) and b.trans_id = d.id
	group by a.id,b.batch_id, b.order_id,b.no_of_roll, c.id, c.product_name_details, c.current_stock, c.color, c.unit_of_measure $iss_field, b.store_id,a.company_id");	

		foreach ($data_array as  $val) 
		{
			$store_arr[$val[csf("store_id")]] = $val[csf("store_id")];
			$company_id = $val[csf("company_id")];
		}

	if($stat_variable == 1)
	{
		$lib_room_rack_shelf_sql = "select b.company_id,b.location_id,b.store_id,b.floor_id,b.room_id,b.rack_id,b.shelf_id,b.bin_id,a.floor_room_rack_name floor_name,c.floor_room_rack_name room_name,d.floor_room_rack_name rack_name,e.floor_room_rack_name shelf_name,f.floor_room_rack_name bin_name from lib_floor_room_rack_dtls b 
		left join lib_floor_room_rack_mst a on b.floor_id=a.floor_room_rack_id and a.status_active =1 and a.is_deleted=0
		left join lib_floor_room_rack_mst c on b.room_id=c.floor_room_rack_id and c.status_active =1 and c.is_deleted=0
		left join lib_floor_room_rack_mst d on b.rack_id=d.floor_room_rack_id and d.status_active =1 and d.is_deleted=0
		left join lib_floor_room_rack_mst e on b.shelf_id=e.floor_room_rack_id and e.status_active =1 and e.is_deleted=0
		left join lib_floor_room_rack_mst f on b.bin_id=f.floor_room_rack_id and f.status_active =1 and f.is_deleted=0
		where b.status_active =1 and b.is_deleted=0 and b.company_id =$company_id and b.store_id in(".implode(',',$store_arr).")";
		$lib_floor_arr=sql_select($lib_room_rack_shelf_sql); 
		foreach ($lib_floor_arr as $room_rack_shelf_row) 
		{
			$company  = $room_rack_shelf_row[csf("company_id")];
			$location = $room_rack_shelf_row[csf("location_id")];
			$floor_id = $room_rack_shelf_row[csf("floor_id")];
			$room_id  = $room_rack_shelf_row[csf("room_id")];
			$rack_id  = $room_rack_shelf_row[csf("rack_id")];
			$shelf_id = $room_rack_shelf_row[csf("shelf_id")];
			$bin_id   = $room_rack_shelf_row[csf("bin_id")];

			if($floor_id!="" && $room_id=="" && $rack_id=="" && $shelf_id=="" && $bin_id==""){
				$lib_floor_arr[$company][$floor_id] = $room_rack_shelf_row[csf("floor_name")];
			}

			if($floor_id!="" && $room_id!="" && $rack_id=="" && $shelf_id=="" && $bin_id==""){
				$lib_room_arr[$company][$floor_id][$room_id] = $room_rack_shelf_row[csf("room_name")];
			}

			if($floor_id!="" && $room_id!="" && $rack_id!="" && $shelf_id=="" && $bin_id==""){
				$lib_rack_arr[$company][$floor_id][$room_id][$rack_id] = $room_rack_shelf_row[csf("rack_name")];
			}

			if($floor_id!="" && $room_id!="" && $rack_id!="" && $shelf_id!="" && $bin_id==""){
				$lib_shelf_arr[$company][$floor_id][$room_id][$rack_id][$shelf_id] = $room_rack_shelf_row[csf("shelf_name")];
			}

			if($floor_id!="" && $room_id!="" && $rack_id!="" && $shelf_id!="" && $bin_id!=""){
				$lib_bin_arr[$company][$floor_id][$room_id][$rack_id][$shelf_id][$bin_id] = $room_rack_shelf_row[csf("bin_name")];
			}
		}
	}
	?>
    <table class="rpt_table" border="1" cellpadding="0" cellspacing="0" rules="all" width="590">
        <thead>
            <th width="30">SL</th>
            <th width="50">Prod. ID</th> 
            <th width="60">Batch No</th>
            <th width="120">Fabric Description</th>
            <th width="50">UOM</th>
            <? 
            if($stat_variable == 1)
            { 
	            ?>
	            <th width="50">Floor</th>
	            <th width="50">Room</th>
	            <th width="50">Rack</th>
	            <th width="50">Shelf</th>
	            <th width="50">Bin</th>
	        	<?
        	}
        	?>
            <th width="60">Issue Qty</th>
            <th width="60">Issue Return Qty</th>
            <th>Balance</th>
        </thead>
        <tbody>
            <? 
            $i=1;
            foreach($data_array as $row)
            { 
                if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
				if($stat_variable == 1)
				{
					$iss_rtn_qnty=$issue_rtn_array[$row[csf('issue_id')]][$row[csf('prod_id')]][$row[csf('batch_id')]][$row[csf('floor_id')]][$row[csf('room')]][$row[csf('rack')]][$row[csf('self')]][$row[csf('bin_box')]];

					$floor 		= $lib_floor_arr[$row[csf("company_id")]][$row[csf("floor_id")]];
					$room 		= $lib_room_arr[$row[csf("company_id")]][$row[csf("floor_id")]][$row[csf("room")]];
					$rack_no	= $lib_rack_arr[$row[csf("company_id")]][$row[csf("floor_id")]][$row[csf("room")]][$row[csf("rack")]];
					$shelf_no 	= $lib_shelf_arr[$row[csf("company_id")]][$row[csf("floor_id")]][$row[csf("room")]][$row[csf("rack")]][$row[csf("self")]];
					$bin_no 	= $lib_bin_arr[$row[csf("company_id")]][$row[csf("floor_id")]][$row[csf("room")]][$row[csf("rack")]][$row[csf("self")]][$row[csf("bin_box")]];

					$addi_set_form_string = "**".$row[csf('floor_id')]."**"."$floor"."**".$row[csf('room')]."**".$room."**".$row[csf('rack')]."**".$rack_no."**".$row[csf('self')]."**".$shelf_no."**".$row[csf('bin_box')]."**".$bin_no;
				}
				else
				{
					$iss_rtn_qnty=$issue_rtn_array[$row[csf('issue_id')]][$row[csf('prod_id')]][$row[csf('batch_id')]];
					$addi_set_form_string = "";
				}
				
				$balance=$row[csf('qnty')]-$iss_rtn_qnty;

				
	            ?>
                <tr bgcolor="<? echo $bgcolor; ?>" onClick='set_form_data("<? echo $row[csf('batch_id')]."**".$row[csf('issue_id')]."**".$row[csf('prod_id')]."**".$row[csf('product_name_details')]."**".number_format($row[csf('qnty')],2,".","")."**".number_format($iss_rtn_qnty,2,".","")."**".number_format($row[csf('current_stock')],2,".","")."**".$color_arr[$row[csf('color')]]."**".$row[csf('order_id')]."**".$row[csf('unit_of_measure')]."**".$row[csf('no_of_roll')]."**".$stat_variable.$addi_set_form_string;?>")' style="cursor:pointer" >

                    <td align="center"><? echo $i; ?></td>
                    <td align="center"><p><? echo $row[csf('prod_id')]; ?></p></td>
                    <td><p><? echo $batch_no_arr[$row[csf('batch_id')]]; ?></p></td>
                    <td><p><? echo $row[csf('product_name_details')]; ?></p></td>
                    <td align="center"><p><? echo $unit_of_measurement[$row[csf('unit_of_measure')]]; ?>&nbsp;</p></td>
		            <? 
		            if($stat_variable == 1)
		            { 
			            ?>
	                    <td align="center"><p><? echo $floor; ?>&nbsp;</p></td>
	                    <td align="center"><p><? echo $room; ?>&nbsp;</p></td>
	                    <td align="center"><p><? echo $rack_no; ?>&nbsp;</p></td>
	                    <td align="center"><p><? echo $shelf_no; ?>&nbsp;</p></td>
	                    <td align="center"><p><? echo $bin_no; ?>&nbsp;</p></td>
	                    <?
                	}
                    ?>
                    <td align="right"><? echo number_format($row[csf('qnty')],2,'.',''); ?></td>
                    <td align="right"><? echo number_format($iss_rtn_qnty,2,'.',''); ?></td>
                    <td align="right"><? echo number_format($balance,2,'.',''); ?></td>
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


if($action=="populate_details_from_data")
{
	$data=explode("**",$data);
 	$dtls_data=sql_select("select batch_id, store_id from  inv_wvn_finish_fab_iss_dtls where status_active=1 and is_deleted=0 and mst_id=$data[1] and batch_id=$data[0]");
	echo "$('#cbo_store_name').val(".$dtls_data[0][csf("store_id")].");\n";
	echo "$('#hidden_batch_id').val(".$dtls_data[0][csf("batch_id")].");\n";
	
	$batch_no=return_field_value("batch_no","pro_batch_create_mst","id='".$dtls_data[0][csf("batch_id")]."'");
	echo "$('#txt_batch_no').val('".$batch_no."');\n";
	exit();
}

if($action=="return_po_popup")
{
	echo load_html_head_contents("Issue Return Info", "../../../", 1, 1,'','','');
	extract($_REQUEST);
	$cbo_company_name=str_replace("'","",$cbo_company_name);
	$txt_issue_id=str_replace("'","",$txt_issue_id);
	$txt_prod_id=str_replace("'","",$txt_prod_id);
	$update_id=str_replace("'","",$update_id);
	$txt_return_qnty=str_replace("'","",$txt_return_qnty);
	$roll_maintained=str_replace("'","",$roll_maintained);
	
	if($update_id>0 && $txt_return_qnty>0)
	{
		$order_sql=sql_select("select po_breakdown_id,quantity from order_wise_pro_details where trans_id=$update_id and trans_type=4 and entry_form=209 and status_active=1");
		foreach($order_sql as $row)
		{
			$order_wise_qnty_arr[$row[csf("po_breakdown_id")]]=$row[csf("quantity")];
		}
	}
	
	if($roll_maintained==1)
	{
		$table_width=600;
	}
	else
	{
		$table_width=500;
	}
	?>
<script>
	
	
	function js_set_value()
	{
		var table_legth=$('#pop_table tbody tr').length;
		var break_qnty=break_roll=break_id="";
		var tot_qnty=tot_roll=0;
		for(var i=1; i<=table_legth; i++)
		{
			//if(i!=1) break_qnty+="_";
			tot_qnty +=($("#returnQnty_"+i).val()*1);
			if(break_qnty!="") break_qnty +="_";
			break_qnty+=($("#poId_"+i).val()*1)+'**'+($("#returnQnty_"+i).val()*1);
			if(break_roll!="") break_roll +="_";
			break_roll+=($("#poId_"+i).val()*1)+'**'+($("#roll_"+i).val()*1)+'**'+($("#returnQnty_"+i).val()*1);
			if(break_id!="") break_id +=",";
			break_id+=($("#poId_"+i).val()*1);
			if($("#roll_"+i).val()>0) tot_roll +=($("#roll_"+i).val()*1);
			
		}
		$("#tot_qnty").val(tot_qnty);
		$("#tot_roll").val(tot_roll);
		$("#break_qnty").val(break_qnty);
		$("#break_roll").val(break_roll);
		$("#break_order_id").val(break_id);
		parent.emailwindow.hide();
	}
	
	function fn_calculate(id)
	{
		var recv_qnty=($("#recevqnty_"+id).val()*1);
		var cumu_qnty=($("#cumulativeIssue_"+id).val()*1);
		var return_qnty=($("#returnQnty_"+id).val()*1);

		if( return_qnty > (recv_qnty - cumu_qnty) ) 
		{
			alert("Return Quantity Can not be Greater Than Issue Quantity.");
			$("#returnQnty_"+id).val(0);
		}
	}
	
	
</script>

</head>

<body>
    <div align="center" style="width:100%;" >
    <form name="searchorderfrm_1"  id="searchorderfrm_1" autocomplete="off">
        <table width="<? echo $table_width; ?>" cellspacing="0" cellpadding="0" border="1" rules="all" class="rpt_table" align="center" id="pop_table">
                <thead>
                    <tr>                	 
                        <th width="140">Order No</th>
                        <th width="120">Issue Quantity</th>
                        <th width="120">Cumulative Return</th>
                        <?
						if($roll_maintained==1)
						{
							?>
							<th>Roll</th>
							<?
						}
						?>           
                        <th width="120">Return Quantity</th>
                    </tr>
                </thead>
                <tbody>
                <?
				$po_no_arr = return_library_array("select id,po_number from wo_po_break_down","id","po_number");
				$sql=sql_select("select a.prod_id, a.po_breakdown_id, sum(a.quantity) as receive_qnty, b.mst_id from  order_wise_pro_details a, inv_transaction b where a.trans_id=b.id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and b.company_id='$cbo_company_name' and b.mst_id='$txt_issue_id' and b.prod_id='$txt_prod_id' and a.entry_form in(19) and b.transaction_type in(2) group by b.mst_id, a.prod_id, a.po_breakdown_id");
				$i=1;

				if($update_id)
				{
					$update_cond = " and a.id <> $update_id";
				}
				foreach($sql as $row)
				{
					$cumilitive_issue=return_field_value("sum(c.quantity) as cumu_qnty","inv_transaction a,  order_wise_pro_details c","a.id=c.trans_id and c.status_active=1 and a.issue_id='$txt_issue_id' and a.prod_id=$txt_prod_id and a.transaction_type=4 and c.po_breakdown_id='".$row[csf('po_breakdown_id')]."' $update_cond","cumu_qnty");

					$balance_qnty = $row[csf("receive_qnty")] - $cumilitive_issue;
					?>
                	<tr>
                    	<td align="center"><input type="text" id="poNo_<? echo $i; ?>" name="poNo_<? echo $i; ?>" class="text_boxes" style="width:140px" value="<? echo $po_no_arr[$row[csf("po_breakdown_id")]];  ?>"  readonly disabled >
                        <input type="hidden" id="poId_<? echo $i; ?>" name="poId_<? echo $i; ?>" class="text_boxes" style="width:140px" value="<? echo $row[csf("po_breakdown_id")];  ?>"  readonly disabled >
                        </td>
                        <td align="center"> <input type="text" id="recevqnty_<? echo $i; ?>" name="recevqnty_<? echo $i; ?>" class="text_boxes_numeric" style="width:110px" value="<? echo number_format($row[csf("receive_qnty")],2);  ?>" readonly disabled ></td>
                        <td align="center"><input type="text" id="cumulativeIssue_<? echo $i; ?>" name="cumulativeIssue_<? echo $i; ?>" value="<? echo number_format($cumilitive_issue,2); ?>" class="text_boxes_numeric" style="width:110px" readonly disabled ></td>
                        <?
						if($roll_maintained==1)
						{
							?>
							<td align="center"><input type="text" id="roll_<? echo $i; ?>" name="roll_<? echo $i; ?>" class="text_boxes_numeric" style="width:80px" ></td>
							<?
						}
						else
						{
							?>
							<td align="center" style="display:none;"><input type="text" id="roll_<? echo $i; ?>" name="roll_<? echo $i; ?>" class="text_boxes_numeric" style="width:80px" ></td>
							<?
						}
						?> 
                        <td align="center">
                        <input type="text" id="returnQnty_<? echo $i; ?>" name="returnQnty_<? echo $i; ?>" onKeyUp="fn_calculate(<? echo $i; ?>);" class="text_boxes_numeric" placeholder="<? echo number_format($balance_qnty,2,".","")?>" value="<? echo $order_wise_qnty_arr[$row[csf("po_breakdown_id")]]; ?>" style="width:110px" >

                        <input type="hidden" id="hiddenissueqnty_<? echo $i; ?>" name="hiddenissueqnty_<? echo $i; ?>" class="text_boxes_numeric" value="<? echo $order_wise_qnty_arr[$row[csf("po_breakdown_id")]]; ?>">
                        </td>
                        
                    </tr>
                    <?
					$i++;
				}
				?>
                </tbody>
        </table>
        <table width="<? echo $table_width; ?>" cellspacing="0" cellpadding="0" border="0" rules="all" align="center">
            <tr>
                <td align="center"> 
                <input type="button" id="btn_close" name="" value="Close" onClick="js_set_value();" style="width:150px;" class="formbutton" >
                <input type="hidden" id="tot_qnty" name="tot_qnty" >
                <input type="hidden" id="tot_roll" name="tot_roll" >
                <input type="hidden" id="break_qnty" name="break_qnty" >
                <input type="hidden" id="break_roll" name="break_roll" >
                <input type="hidden" id="break_order_id" name="break_order_id" >
                </td>
            </tr>
        </table>
    </form>
    </div>
</body>    
	<?

}


if($action=="save_update_delete")
{	 
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process )); 
	
	$roll_maintained=str_replace("'","",$roll_maintained);
	$cbo_issue_purpose=str_replace("'","",$cbo_issue_purpose);
	//echo $issue_purpose;die;
	
	if($cbo_issue_purpose==8) $book_without_order=1; else $book_without_order=0;
	
	$is_update_cond =( $operation==1 ) ? " and id <> $update_id" : "";
	$max_transaction_date = return_field_value("max(transaction_date) as max_date", "inv_transaction", "prod_id=$txt_prod_id and store_id= $cbo_store_name and status_active = 1 $is_update_cond", "max_date");      
	if($max_transaction_date != "")
	{
		$max_transaction_date = date("Y-m-d", strtotime($max_transaction_date));
		$receive_date = date("Y-m-d", strtotime(str_replace("'", "", $txt_issue_date)));
		if ($receive_date < $max_transaction_date) 
		{
			echo "20**Issue Return Date Can not Be Less Than Last Transaction Date Of This Lot";
			check_table_status($_SESSION['menu_id'], 0);
			disconnect($con);
			die;
		}
	}
	
	if( $operation==0 ) // Insert Here----------------------------------------------------------
	{
		$con = connect();
		if($db_type==0)	{ mysql_query("BEGIN"); }
 		


		$totalIssuedQnty = return_field_value("sum(b.cons_quantity)","inv_issue_master a, inv_transaction b"," a.id=b.mst_id and a.id=$txt_issue_id and b.prod_id=$txt_prod_id and b.item_category=3 and b.transaction_type=2");
		$totalReturnedQnty = return_field_value("sum(cons_quantity)","inv_transaction","issue_id=$txt_issue_id and prod_id=$txt_prod_id and item_category=3 and transaction_type=4");

		
		$txt_return_qnty=str_replace("'","",$txt_return_qnty);

		if( ($totalReturnedQnty + $txt_return_qnty) > $totalIssuedQnty)
		{
			echo "20**Return Quantity Not Over Issue Quantity.";
			die;
		}


 		if(str_replace("'","",$issue_mst_id)!="")
		{
			$new_return_number[0] = str_replace("'","",$txt_system_id);
			$id=str_replace("'","",$issue_mst_id);
			//issue master table UPDATE here START----------------------//		
 			$field_array_mst="receive_purpose*booking_without_order*receive_date*issue_id*challan_no*updated_by*update_date";
			$data_array_mst=$cbo_issue_purpose."*".$book_without_order."*".$txt_issue_date."*".$txt_issue_id."*".$txt_challan_no."*'".$user_id."'*'".$pc_date_time."'";
			//echo $field_array."<br>".$data_array;die;
		}
		else  	
		{	 
			//issue master table entry here START---------------------------------------//		
			//$id=return_next_id("id", "inv_receive_master", 1);
			
			if($db_type==0) $year_cond="YEAR(insert_date)"; 
			else if($db_type==2) $year_cond="to_char(insert_date,'YYYY')";
			else $year_cond="";//defined Later
					
			//$new_return_number=explode("*",return_mrr_number( str_replace("'","",$cbo_company_id), '', 'KFIR', date("Y",time()), 5, "select recv_number_prefix,recv_number_prefix_num from inv_receive_master where company_id=$cbo_company_id and entry_form=209 and $year_cond=".date('Y',time())." order by id DESC ", "recv_number_prefix", "recv_number_prefix_num" ));
			
			$id = return_next_id_by_sequence("INV_RECEIVE_MASTER_PK_SEQ", "inv_receive_master", $con);
			$new_return_number = explode("*", return_next_id_by_sequence("INV_RECEIVE_MASTER_PK_SEQ", "inv_receive_master",$con,1,str_replace("'","",$cbo_company_id),'WFIR',209,date("Y",time())));
			
 			$field_array_mst="id, recv_number_prefix, recv_number_prefix_num, recv_number, entry_form, item_category, company_id, receive_date, issue_id, challan_no, inserted_by, insert_date";
			$data_array_mst="(".$id.",'".$new_return_number[1]."','".$new_return_number[2]."','".$new_return_number[0]."',209,3,".$cbo_company_id.",".$txt_issue_date.",".$txt_issue_id.",".$txt_challan_no.",'".$user_id."','".$pc_date_time."')";
			//echo "20**".$field_array_mst."<br>".$data_array_mst;die;
		}
		
		
		//transaction table insert here START--------------------------------//cbouom

		$transactionID = return_next_id_by_sequence("INV_TRANSACTION_PK_SEQ", "inv_transaction", $con); 				
		$field_array_trans = "id,mst_id,company_id,prod_id,item_category,transaction_type,transaction_date,order_uom,order_qnty,cons_uom,cons_quantity,remarks,issue_id,issue_challan_no,floor_id,room,rack,self,bin_box,batch_id_from_fissuertn,pi_wo_batch_no,no_of_roll,store_id,inserted_by,insert_date";
 		$data_array_trans = "(".$transactionID.",".$id.",".$cbo_company_id.",".$txt_prod_id.",3,4,".$txt_issue_date.",".$cbouom.",".$txt_return_qnty.",".$cbouom.",".$txt_return_qnty.",".$txt_remarks.",".$txt_issue_id.",".$txt_challan_no.",".$txt_floor.",".$txt_room.",".$txt_rack.",".$txt_shelf.",".$txt_bin.",".$hidden_batch_id.",".$hidden_batch_id.",".$txt_no_of_roll.",".$cbo_store_name.",'".$user_id."','".$pc_date_time."')"; 
		
		//echo "insert into inv_transaction ($field_array_trans) values $data_array_trans"; die;
		 
		//adjust product master table START-------------------------------------//
		
		$sql = sql_select("select product_name_details,last_purchased_qnty,current_stock,color from product_details_master where id=$txt_prod_id");
		$presentStock=$available_qnty=0; $color_id=0;
		$product_name_details="";
		foreach($sql as $result)
		{
			$presentStock			=$result[csf("current_stock")];
			$product_name_details 	=$result[csf("product_name_details")];
			$color_id 				=$result[csf("color")];
		}
		$nowStock 		= $presentStock+str_replace("'","",$txt_return_qnty);
		//echo $nowStock;die;
			
		$field_array_prod="last_purchased_qnty*current_stock*updated_by*update_date";
		$data_array_prod=$txt_return_qnty."*".$nowStock."*'".$user_id."'*'".$pc_date_time."'";
		
		
		//order_wise_pro_detail table insert here
		
		$txt_break_qnty=str_replace("'","",$txt_break_qnty);
		$txt_break_roll=str_replace("'","",$txt_break_roll);
		$txt_order_id_all=str_replace("'","",$txt_order_id_all);
		$ordr_wise_rtn_qnty_arr=explode("_",$txt_break_qnty);
		$ordr_wise_rtn_roll_arr=explode("_",$txt_break_roll);
		$ordr_id_arr=explode(",",$txt_order_id_all);
		//$proportion_id = return_next_id("id", "order_wise_pro_details", 1);
		//$roll_id = return_next_id("id", "pro_roll_details", 1);
		
		$field_array_proportion="id,trans_id,trans_type,entry_form,po_breakdown_id,prod_id,quantity,color_id,inserted_by,insert_date";
		 
		$data_array_proportion=$data_array_roll="";
		if(!empty($txt_break_qnty))
		{
			foreach($ordr_wise_rtn_qnty_arr as $val)
			{
				$order_qnty_arr=explode("**",$val);
				if($order_qnty_arr[1]>0)
				{
					$proportion_id = return_next_id_by_sequence("ORDER_WISE_PROP_PK_SEQ", "order_wise_pro_details", $con);
					if($data_array_proportion!="") $data_array_proportion.=", ";
					$data_array_proportion.="(".$proportion_id.",".$transactionID.",4,209,".$order_qnty_arr[0].",".$txt_prod_id.",".$order_qnty_arr[1].",".$color_id.",'".$user_id."','".$pc_date_time."')";
					//$proportion_id++;
				}
			}
			
			/*	
			if($roll_maintained==1)
			{
				$field_array_roll="id,mst_id,dtls_id,po_breakdown_id,entry_form,roll_no,qnty,inserted_by,insert_date";
				
				foreach($ordr_wise_rtn_roll_arr as $val)
				{
					$order_roll_arr=explode("**",$val);
					
					if($order_roll_arr[1]>0)
					{
						$roll_id = return_next_id_by_sequence("PRO_ROLL_DTLS_PK_SEQ", "pro_roll_details", $con);
						if($data_array_roll!="") $data_array_roll.=", ";
						$data_array_roll.="(".$roll_id.",".$id.",".$transactionID.",".$order_roll_arr[0].",209,".$order_roll_arr[1].",".$order_roll_arr[2].",'".$user_id."','".$pc_date_time."')";
						//$roll_id++;
					}
				}
			}
			*/
		}
		
		
		
		$rID=$transID=$prodUpdate=$propoId=$rollId=true;
		if(str_replace("'","",$txt_system_id)!="")
		{
			$rID=sql_update("inv_receive_master",$field_array_mst,$data_array_mst,"id",$id,1);
		}
		else
		{
			$rID=sql_insert("inv_receive_master",$field_array_mst,$data_array_mst,1);
		}
		$transID = sql_insert("inv_transaction",$field_array_trans,$data_array_trans,1);
		$prodUpdate = sql_update("product_details_master",$field_array_prod,$data_array_prod,"id",$txt_prod_id,1);
		if($data_array_proportion!="")
		{
			$propoId=sql_insert("order_wise_pro_details",$field_array_proportion,$data_array_proportion,1);
			/*if($roll_maintained==1)
			{
				$rollId=sql_insert("pro_roll_details",$field_array_roll,$data_array_roll,1);
			}*/
		}
		
		//echo "10**$rID && $transID && $prodUpdate && $propoId && $rollId";die;
		
		if($db_type==0)
		{
			if( $rID && $transID && $prodUpdate && $propoId && $rollId )
			{
				mysql_query("COMMIT");  
				echo "0**".$new_return_number[0]."**".$id."**".str_replace("'","",$roll_maintained);
			}
			else
			{
				mysql_query("ROLLBACK"); 
				echo "10**".$new_return_number[0];
			}
		}
		else if($db_type==2 || $db_type==1 )
		{
			if( $rID && $transID && $prodUpdate && $propoId && $rollId)
			{
				oci_commit($con);  
				echo "0**".$new_return_number[0]."**".$id."**".str_replace("'","",$roll_maintained);
			}
			else
			{
				oci_rollback($con);
				echo "10**".$new_return_number[0];
			}
		}
		disconnect($con);
		die;
				
	}	
	else if ($operation==1) // Update Here----------------------------------------------------------
	{
		$con = connect();		
		if($db_type==0)	{ mysql_query("BEGIN"); }
		//table lock here 
		$issue_mst_id= str_replace("'","",$issue_mst_id);
		$roll_maintained= str_replace("'","",$roll_maintained);
		$txt_system_id = str_replace("'","",$txt_system_id);
		//check update id
		if( str_replace("'","",$update_id) == "" )
		{
			echo "10";die; 
		}
		
		$totalIssuedQnty = return_field_value("sum(b.cons_quantity)","inv_issue_master a, inv_transaction b"," a.id=b.mst_id and a.id=$txt_issue_id and b.prod_id=$txt_prod_id and b.item_category=3 and b.transaction_type=2");
		$totalReturnedQnty = return_field_value("sum(cons_quantity)","inv_transaction","issue_id=$txt_issue_id and prod_id=$txt_prod_id and item_category=3 and transaction_type=4 and id <> $update_id");

		
		$txt_return_qnty=str_replace("'","",$txt_return_qnty);

		$prev_return_qnty=str_replace("'","",$prev_return_qnty);

		if( ($totalReturnedQnty + $txt_return_qnty) > $totalIssuedQnty)
		{
			echo "20**Return Quantity ($totalReturnedQnty + $txt_return_qnty) Not Over Issue Quantity ($totalIssuedQnty)";
			die;
		}


		
		//****************************************** BEFORE ENTRY ADJUST START *****************************************//
		//product master table information
		//before stock update
		$sql = sql_select( "select a.id,a.current_stock, b.cons_quantity from product_details_master a, inv_transaction b where a.id=b.prod_id and a.id=$before_prod_id and b.id=$update_id and a.item_category_id=3 and b.item_category=3 and b.transaction_type=4" );
		$before_prod_id=$before_issue_qnty=$before_stock_qnty=0;
		foreach($sql as $result)
		{
			$before_prod_id 	= $result[csf("id")];
 			$before_stock_qnty 	= $result[csf("current_stock")];
			//before quantity and stock value
			$before_issue_qnty	= $result[csf("cons_quantity")];
		}
		
		//current product ID
		$txt_prod_id = str_replace("'","",$txt_prod_id);
		$txt_return_qnty = str_replace("'","",$txt_return_qnty);
		$before_prod_id= str_replace("'","",$before_prod_id);
		$curr_stock_qnty=return_field_value("current_stock","product_details_master","id=$txt_prod_id and item_category_id=3");
		
		
 		//echo $receive_purpose;die;
		//weighted and average rate START here------------------------//
		//product master table data UPDATE START----------------------//		
		$update_array_prod= "last_purchased_qnty*current_stock*updated_by*update_date";
		if($before_prod_id==$txt_prod_id)
		{
			$adj_stock_qnty = (($curr_stock_qnty-$before_issue_qnty)+$txt_return_qnty); // CurrentStock + Before Issue Qnty - Current Issue Qnty
			 
			$data_array_prod= $txt_return_qnty."*".$adj_stock_qnty."*'".$user_id."'*'".$pc_date_time."'";
		}
		else
		{
			$updateIdprod_array = $update_dataProd = array();
			//before product adjust
			$adj_before_stock_qnty 	= $before_stock_qnty-$before_issue_qnty; // CurrentStock - Before Issue Qnty
			 
			$updateIdprod_array[]=$before_prod_id;
			$update_dataProd[$before_prod_id]=explode("*",("".$before_issue_qnty."*".$adj_before_stock_qnty."*'".$user_id."'*'".$pc_date_time."'"));
			
			//current product adjust
			$adj_curr_stock_qnty = 	$curr_stock_qnty+$txt_return_qnty; // CurrentStock + Before Issue Qnty
			
			$updateIdprod_array[]=$txt_prod_id;
			$update_dataProd[$txt_prod_id]=explode("*",("".$txt_return_qnty."*".$adj_curr_stock_qnty."*'".$user_id."'*'".$pc_date_time."'"));
		}
		
		
	
		 
  		$id=str_replace("'","",$issue_mst_id);
		//yarn master table UPDATE here START----------------------//cbouom	
		$field_array_mst="receive_date*issue_id*challan_no*updated_by*update_date";
		$data_array_mst=$txt_issue_date."*".$txt_issue_id."*".$txt_challan_no."*'".$user_id."'*'".$pc_date_time."'";
		
		
		//,rack,self
 		$field_array_trans="company_id*prod_id*item_category*transaction_type*transaction_date*order_uom*order_qnty*cons_uom*cons_quantity*remarks*issue_id*issue_challan_no*floor_id*room*rack*self*bin_box*batch_id_from_fissuertn*pi_wo_batch_no*no_of_roll*store_id*updated_by*update_date";
 		$data_array_trans= "".$cbo_company_id."*".$txt_prod_id."*3*4*".$txt_issue_date."*".$cbouom."*".$txt_return_qnty."*".$cbouom."*".$txt_return_qnty."*".$txt_remarks."*".$txt_issue_id."*".$txt_challan_no."*".$txt_floor."*".$txt_room."*".$txt_rack."*".$txt_shelf."*".$txt_bin."*".$hidden_batch_id."*".$hidden_batch_id."*".$txt_no_of_roll."*".$cbo_store_name."*'".$user_id."'*'".$pc_date_time."'"; 

		//echo $field_array."<br>".$data_array;die;
		$update_id = str_replace("'","",$update_id);
		//order_wise_pro_detail table insert here
		
		$txt_break_qnty=str_replace("'","",$txt_break_qnty);
		$txt_break_roll=str_replace("'","",$txt_break_roll);
		$txt_order_id_all=str_replace("'","",$txt_order_id_all);
		$ordr_wise_rtn_qnty_arr=explode("_",$txt_break_qnty);
		$ordr_wise_rtn_roll_arr=explode("_",$txt_break_roll);
		$ordr_id_arr=explode(",",$txt_order_id_all);
		//$proportion_id = return_next_id("id", "order_wise_pro_details", 1);
		
		//$roll_id = return_next_id("id", "pro_roll_details", 1);
		
		$field_array_proportion="id,trans_id,trans_type,entry_form,po_breakdown_id,prod_id,quantity,color_id,inserted_by,insert_date";
		$color_id=return_field_value("color","product_details_master","id=$txt_prod_id and item_category_id=3","color");
		//echo "select color from product_details_master where id=$txt_prod_id and item_category_id=2"; die;
		 
		$data_array_proportion=$data_array_roll="";
		if(!empty($txt_break_qnty))
		{
			foreach($ordr_wise_rtn_qnty_arr as $val)
			{
				$order_qnty_arr=explode("**",$val);
				if($order_qnty_arr[1]>0)
				{
					$proportion_id = return_next_id_by_sequence("ORDER_WISE_PROP_PK_SEQ", "order_wise_pro_details", $con);
					if($data_array_proportion!="") $data_array_proportion.=", ";
					$data_array_proportion.="(".$proportion_id.",".$update_id.",4,209,".$order_qnty_arr[0].",".$txt_prod_id.",".$order_qnty_arr[1].",".$color_id.",'".$user_id."','".$pc_date_time."')";
					//$proportion_id++;
				}
			}
			

		}
		
		
 		$query1=$query4=$query5=$rID=$transID=$propoId=$rollId=true;
		
		if($before_prod_id==$txt_prod_id)
		{
			$query1= sql_update("product_details_master",$update_array_prod,$data_array_prod,"id",$before_prod_id,1);
		}
		else
		{
			$query1=execute_query(bulk_update_sql_statement("product_details_master","id",$update_array_prod,$update_dataProd,$updateIdprod_array));
		}
		$rID=sql_update("inv_receive_master",$field_array_mst,$data_array_mst,"id",$id,1);
		$transID = sql_update("inv_transaction",$field_array_trans,$data_array_trans,"id",$update_id,1);
		if($data_array_proportion!="")
		{
			$query4 = execute_query("DELETE FROM order_wise_pro_details WHERE trans_id=$update_id and entry_form=209");
			$propoId=sql_insert("order_wise_pro_details",$field_array_proportion,$data_array_proportion,1);

		} 
		
		//echo "10**fail";die;
		if($db_type==0)
		{
			if($query1 && $query4 && $query5 && $rID && $transID && $propoId && $rollId)
			{
				mysql_query("COMMIT");  
				echo "1**".str_replace("'","",$txt_system_id)."**".$issue_mst_id."**".str_replace("'","",$roll_maintained);
			}
			else
			{
				mysql_query("ROLLBACK"); 
				echo "10**".str_replace("'","",$txt_system_id);
			}
		}
		else if($db_type==2 || $db_type==1 )
		{
			if($query1 && $query4 && $query5 && $rID && $transID && $propoId && $rollId)
			{
				oci_commit($con);   
				echo "1**".str_replace("'","",$txt_system_id)."**".$issue_mst_id."**".str_replace("'","",$roll_maintained);
			}
			else
			{
				oci_rollback($con);
				echo "10**".str_replace("'","",$txt_system_id);
			}
		}
		disconnect($con);
		die;
 	}
	else if ($operation==2) // Delete Here----------------------------------------------------------
	{
		 //no operation
	}		
}

if($action=="show_dtls_list_view")
{
	
	$ex_data = explode("**",$data);
	
	$sql = "select a.recv_number,a.company_id,a.supplier_id,a.receive_date,a.item_category,a.recv_number,b.id, b.cons_quantity, b.cons_uom, b.cons_rate, b.cons_amount, c.product_name_details, c.id as prod_id   
			from  inv_receive_master a, inv_transaction b left join product_details_master c on b.prod_id=c.id
			where a.id=b.mst_id and b.item_category=3 and b.transaction_type=4  and a.id=$ex_data[0]";
	//echo $sql;
	$result = sql_select($sql);
	$i=1;
	$rettotalQnty=0;
	$rcvtotalQnty=0;
	$rejtotalQnty=0;
	$totalAmount=0;
	?> 
     	<table class="rpt_table" border="1" cellpadding="2" cellspacing="0" style="width:600px" rules="all">
        	<thead>
            	<tr>
                	<th>SL</th>
                    <th>Return No</th>
                    <th>Product ID</th>
                    <th>Item Description</th>
                    <th>Return Qty</th>
                </tr>
            </thead>
            <tbody>
            	<? 
				foreach($result as $row){					
					if($i%2==0)
						$bgcolor="#E9F3FF";
					else 
						$bgcolor="#FFFFFF";
 					
					$rettotalQnty +=$row[csf("cons_quantity")];
 					$totalAmount +=$row[csf("cons_amount")];
 					
 				?>
                	<tr bgcolor="<? echo $bgcolor; ?>" onClick='get_php_form_data("<? echo $row[csf("id")]."**".$ex_data[1];?>","child_form_input_data","requires/woven_finish_fabric_issue_return_controller")' style="cursor:pointer" >
                        <td width="50"><? echo $i; ?></td>
                        <td width="120"><p><? echo $row[csf("recv_number")]; ?></p></td>
                        <td width="100" align="center"><p><? echo $row[csf("prod_id")]; ?></p></td>
                        <td width="250"><p><? echo $row[csf("product_name_details")]; ?></p></td>
                        <td align="right" style="padding-right:3px;"><p><? echo $row[csf("cons_quantity")]; ?></p></td>
                   </tr>
                <? $i++; } ?>
                	<tfoot>
                        <th colspan="4">Total</th>                         
                        <th><? echo $rettotalQnty; ?></th> 
                   </tfoot>
            </tbody>
        </table>
    <?
	exit();
}

if($action=="child_form_input_data")
{
	$stat_variable = 1; //N.B static variable for floor,room,rack,shelf,bin until it's created.
	$ex_data = explode("**",$data);
	$color_arr=return_library_array( "select id, color_name from lib_color",'id','color_name');
	$batch_arr = return_library_array("select id, batch_no from pro_batch_create_mst","id","batch_no");
	$roll_maintained=str_replace("'","",$ex_data[1]);
  	$sql = "select a.company_id, b.id as prod_id, b.product_name_details, b.color, b.current_stock, a.id as tr_id, a.store_id, a.issue_id, a.cons_quantity, a.issue_challan_no,a.remarks,a.floor_id,a.room, a.rack, a.self, a.bin_box, a.batch_id_from_fissuertn, a.no_of_roll, a.cons_uom
			from inv_transaction a, product_details_master b
 			where a.id=$ex_data[0] and a.status_active=1 and a.item_category=3 and transaction_type=4 and a.prod_id=b.id and b.status_active=1";
 	//echo $sql;die;
	$result = sql_select($sql);
	foreach ($result as $val) 
	{
		$store_ids[$val[csf("store_id")]] =$val[csf("store_id")];
		$company_id = $val[csf("company_id")];
	}
	if($stat_variable == 1)
	{
		$lib_room_rack_shelf_sql = "select b.company_id,b.location_id,b.store_id,b.floor_id,b.room_id,b.rack_id,b.shelf_id,b.bin_id,a.floor_room_rack_name floor_name,c.floor_room_rack_name room_name,d.floor_room_rack_name rack_name,e.floor_room_rack_name shelf_name,f.floor_room_rack_name bin_name from lib_floor_room_rack_dtls b 
			left join lib_floor_room_rack_mst a on b.floor_id=a.floor_room_rack_id and a.status_active =1 and a.is_deleted=0
			left join lib_floor_room_rack_mst c on b.room_id=c.floor_room_rack_id and c.status_active =1 and c.is_deleted=0
			left join lib_floor_room_rack_mst d on b.rack_id=d.floor_room_rack_id and d.status_active =1 and d.is_deleted=0
			left join lib_floor_room_rack_mst e on b.shelf_id=e.floor_room_rack_id and e.status_active =1 and e.is_deleted=0
			left join lib_floor_room_rack_mst f on b.bin_id=f.floor_room_rack_id and f.status_active =1 and f.is_deleted=0
			where b.status_active =1 and b.is_deleted=0 and b.company_id =".$company_id." and b.store_id in(".implode(',',$store_ids).")";
		$lib_floor_arr=sql_select($lib_room_rack_shelf_sql); 
		foreach ($lib_floor_arr as $room_rack_shelf_row) 
		{
			$company  = $room_rack_shelf_row[csf("company_id")];
			$location = $room_rack_shelf_row[csf("location_id")];
			$floor_id = $room_rack_shelf_row[csf("floor_id")];
			$room_id  = $room_rack_shelf_row[csf("room_id")];
			$rack_id  = $room_rack_shelf_row[csf("rack_id")];
			$shelf_id = $room_rack_shelf_row[csf("shelf_id")];
			$bin_id   = $room_rack_shelf_row[csf("bin_id")];

			if($floor_id!="" && $room_id=="" && $rack_id=="" && $shelf_id=="" && $bin_id==""){
				$lib_floor_arr[$company][$floor_id] = $room_rack_shelf_row[csf("floor_name")];
			}

			if($floor_id!="" && $room_id!="" && $rack_id=="" && $shelf_id=="" && $bin_id==""){
				$lib_room_arr[$company][$floor_id][$room_id] = $room_rack_shelf_row[csf("room_name")];
			}

			if($floor_id!="" && $room_id!="" && $rack_id!="" && $shelf_id=="" && $bin_id==""){
				$lib_rack_arr[$company][$floor_id][$room_id][$rack_id] = $room_rack_shelf_row[csf("rack_name")];
			}

			if($floor_id!="" && $room_id!="" && $rack_id!="" && $shelf_id!="" && $bin_id==""){
				$lib_shelf_arr[$company][$floor_id][$room_id][$rack_id][$shelf_id] = $room_rack_shelf_row[csf("shelf_name")];
			}

			if($floor_id!="" && $room_id!="" && $rack_id!="" && $shelf_id!="" && $bin_id!=""){
				$lib_bin_arr[$company][$floor_id][$room_id][$rack_id][$shelf_id][$bin_id] = $room_rack_shelf_row[csf("bin_name")];
			}
		}
	}

	foreach($result as $row)
	{
		$issue_purpose=return_field_value("issue_purpose","inv_issue_master","id='".$row[csf("issue_id")]."'");
		
		if($stat_variable == 1)
		{
			$floor 		= $lib_floor_arr[$row[csf("company_id")]][$row[csf("floor_id")]];
			$room 		= $lib_room_arr[$row[csf("company_id")]][$row[csf("floor_id")]][$row[csf("room")]];
			$rack_no	= $lib_rack_arr[$row[csf("company_id")]][$row[csf("floor_id")]][$row[csf("room")]][$row[csf("rack")]];
			$shelf_no 	= $lib_shelf_arr[$row[csf("company_id")]][$row[csf("floor_id")]][$row[csf("room")]][$row[csf("rack")]][$row[csf("self")]];
			$bin_no 	= $lib_bin_arr[$row[csf("company_id")]][$row[csf("floor_id")]][$row[csf("room")]][$row[csf("rack")]][$row[csf("self")]][$row[csf("bin_box")]];


			echo "$('#txt_floor').val('".$row[csf("floor_id")]."');\n";
			echo "$('#txt_floor_name').val('".$floor."');\n";
			echo "$('#txt_room').val('".$row[csf("room")]."');\n";
			echo "$('#txt_room_name').val('".$room."');\n";
			echo "$('#txt_rack').val('".$row[csf("rack")]."');\n";
			echo "$('#txt_rack_name').val('".$rack_no."');\n";
			echo "$('#txt_shelf').val('".$row[csf("self")]."');\n";
			echo "$('#txt_shelf_name').val('".$shelf_no."');\n";
			echo "$('#txt_bin').val('".$row[csf("bin_box")]."');\n";
			echo "$('#txt_bin_name').val('".$bin_no."');\n";
		}






		echo "return_qnty_basis(".$issue_purpose.");\n";
		echo "$('#cbo_store_name').val('".$row[csf("store_id")]."');\n";
		echo "$('#txt_batch_no').val('".$batch_arr[$row[csf("batch_id_from_fissuertn")]]."');\n";
		echo "$('#hidden_batch_id').val('".$row[csf("batch_id_from_fissuertn")]."');\n";
 		echo "$('#txt_fabric_desc').val('".$row[csf("product_name_details")]."');\n";
		echo "$('#txt_prod_id').val('".$row[csf("prod_id")]."');\n";
		echo "$('#before_prod_id').val('".$row[csf("prod_id")]."');\n";
		echo "$('#txt_return_qnty').val('".$row[csf("cons_quantity")]."');\n"; 
		echo "$('#prev_return_qnty').val('".$row[csf("cons_quantity")]."');\n"; 
		echo "$('#txt_no_of_roll').val('".$row[csf("no_of_roll")]."');\n";
		
 		echo "$('#txt_remarks').val('".$row[csf("remarks")]."');\n";
		echo "$('#txt_issue_id').val('".$row[csf("issue_id")]."');\n";
		echo "$('#txt_color').val('".$color_arr[$row[csf("color")]]."');\n";
		echo "$('#cbouom').val('".$row[csf("cons_uom")]."');\n";
		
		$propotion_sql=sql_select("select po_breakdown_id, quantity from order_wise_pro_details where trans_id='".$row[csf("tr_id")]."'");
		$po_wise_qnty="";$po_id_all="";
		if(count($propotion_sql)>0)
		{
			foreach($propotion_sql as $row_order)
			{
				if($po_wise_qnty!="") $po_wise_qnty .="_";
				$po_wise_qnty .=$row_order[csf("po_breakdown_id")]."**".$row_order[csf("quantity")];
				if($po_id_all!="") $po_id_all .=",";
				$po_id_all .=$row_order[csf("po_breakdown_id")];
			}
			if($roll_maintained==1)
			{
				$roll_sql=sql_select("select po_breakdown_id, roll_no, qnty from  pro_roll_details where mst_id='$issue_id' and dtls_id='".$row[csf("tr_id")]."'");
				$roll_ref="";
				foreach($roll_sql as $row_roll)
				{
					if($roll_ref!="") $roll_ref .="_";
					$roll_ref .=$row_roll[csf("po_breakdown_id")]."**".$row_roll[csf("roll_no")]."**".$row_roll[csf("qnty")];
				}
			}
		}
		else
		{
			echo "$('#txt_return_qnty').removeAttr('placeholder').removeAttr('readonly').removeAttr('onDblClick').attr('placeholder','write');\n";
			echo "$('#txt_no_of_roll').removeAttr('readonly');\n";
		}
		
		echo "$('#txt_break_qnty').val('$po_wise_qnty');\n";
		echo "$('#txt_break_roll').val('$roll_ref');\n";
		echo "$('#txt_order_id_all').val('$po_id_all');\n";
		
		
		$totalIssued = return_field_value("sum(b.cons_quantity)","inv_issue_master a, inv_transaction b"," a.id=b.mst_id and a.id='".$row[csf("issue_id")]."' and b.prod_id='".$row[csf("prod_id")]."' and b.item_category=3 and b.transaction_type=2");
		
		if($totalIssued=="") $totalIssued=0;
		echo "$('#txt_tot_issue').val('".$totalIssued."');\n";
		
		
		$totalReturn = return_field_value("sum(cons_quantity)","inv_transaction","issue_id='".$row[csf("issue_id")]."' and prod_id='".$row[csf("prod_id")]."' and item_category=3 and transaction_type=4");
		echo "$('#txt_total_return_display').val('".$totalReturn."');\n";
		$netUsed = $totalIssued-$totalReturn;
		echo "$('#txt_net_used').val('".$netUsed."');\n";
		echo "$('#hide_net_used').val('".$row[csf("cons_quantity")]."');\n";
		echo "$('#txt_global_stock').val('".$row[csf("current_stock")]."');\n";
		echo "$('#update_id').val(".$row[csf("tr_id")].");\n";
		
	}
 	echo "set_button_status(1, permission, 'fnc_fabric_issue_rtn',1,1);\n";		
  	exit();
}


if($action=="return_number_popup")
{
  	echo load_html_head_contents("Popup Info","../../../", 1, 1, $unicode);
	extract($_REQUEST);  
?>
     
<script>
	function js_set_value(mrr)
	{
 		$("#hidden_return_number").val(mrr); // mrr number
		parent.emailwindow.hide();
	}
	
	function change_caption()
	{
		var caption=$("#cbo_search_by :selected").text();
		$("#search_by_td_up").text("Enter "+caption);
	}
</script>

</head>

<body>
<div align="center" style="width:100%;" >
<form name="searchorderfrm_1"  id="searchorderfrm_1" autocomplete="off">
	<table width="800" cellspacing="0" cellpadding="0" border="1" rules="all" class="rpt_table" align="center">
            <thead>
                <tr>                	 
                    <th width="170">Search By</th>
                    <th width="270" align="center" id="search_by_td_up">Enter Return Number</th>
                    <th width="220">Date Range</th>
                    <th><input type="reset" name="re_button" id="re_button" value="Reset" style="width:100px" class="formbutton"  /></th>           
                </tr>
            </thead>
            <tbody>
                <tr class="general">                    
                    <td>
                        <?  
                            $search_by = array(1=>'Return Number',2=>'Batch No');
							//$dd="change_search_event(this.value, '0*0', '0*0', '../../../') ";
							echo create_drop_down( "cbo_search_by", 140, $search_by,"",0, "--Select--", "",'change_caption()','' );
                        ?>
                    </td>
                    <td width="" align="center" id="search_by_td">				
                        <input type="text" style="width:230px" class="text_boxes"  name="txt_search_common" id="txt_search_common" />	
                    </td>    
                    <td align="center">
                        <input name="txt_date_from" id="txt_date_from" class="datepicker" style="width:80px" placeholder="From Date" />
                        <input name="txt_date_to" id="txt_date_to" class="datepicker" style="width:80px" placeholder="To Date" />
                    </td> 
                    <td align="center">
                        <input type="button" name="btn_show" class="formbutton" value="Show" onClick="show_list_view ( document.getElementById('cbo_search_by').value+'_'+document.getElementById('txt_search_common').value+'_'+document.getElementById('txt_date_from').value+'_'+document.getElementById('txt_date_to').value+'_'+<? echo $company; ?>, 'create_return_search_list_view', 'search_div', 'woven_finish_fabric_issue_return_controller', 'setFilterGrid(\'list_view\',-1)')" style="width:100px;" />				
                    </td>
            </tr>
        	<tr>                  
            	<td align="center" height="40" valign="middle" colspan="5">
					<? echo load_month_buttons(1);  ?>
                    <!- Hidden field here-->
                     <input type="hidden" id="hidden_return_number" value="" />
                    <!-- END-->
                </td>
            </tr>    
            </tbody>
         </tr>         
        </table>    
        <div style="margin-top:5px" align="center" valign="top" id="search_div"> </div> 
        </form>
   </div>
</body>           
<script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
</html>
<?
}


if($action=="create_return_search_list_view")
{
	
	$ex_data = explode("_",$data);
	$search_by = $ex_data[0];
	$search_common = $ex_data[1];
	$txt_date_from = $ex_data[2];
	$txt_date_to = $ex_data[3];
	$company = $ex_data[4];
	
	$sql_cond="";
	if($search_common!="") 
	{
		if($search_by==1)
		{
			$sql_cond .= " and a.recv_number like '%$search_common'";
		}
		else
		{
			$sql_cond .= " and d.batch_no like '$search_common%'";
		}
	}
	
	if( $txt_date_from!="" && $txt_date_to!="" ) 
	{
		if($db_type==0)
		{
			$sql_cond .= " and a.receive_date  between '".change_date_format($txt_date_from,'yyyy-mm-dd')."' and '".change_date_format($txt_date_to,'yyyy-mm-dd')."'";
		}
		else
		{
			$sql_cond .= " and a.receive_date  between '".change_date_format($txt_date_from,'','',1)."' and '".change_date_format($txt_date_to,'','',1)."'";
		}
	}
	
	if($company!="") $sql_cond .= " and a.company_id='$company'";
	
	if($db_type==0) $year_field="YEAR(a.insert_date) as year,"; 
	else if($db_type==2) $year_field="to_char(a.insert_date,'YYYY') as year,";
	else $year_field="";//defined Later
	
	$sql = "select a.id as mst_id,a.recv_number_prefix_num, a.recv_number, a.receive_date, a.item_category, a.issue_id, $year_field b.id, b.cons_quantity, c.product_name_details, c.id as prod_id, d.batch_no
			from inv_receive_master a, inv_transaction b, product_details_master c, pro_batch_create_mst d
			where a.id=b.mst_id and b.prod_id=c.id and b.batch_id_from_fissuertn=d.id and b.item_category=3 and b.transaction_type=4 and a.entry_form=209 $sql_cond order by a.id"; 
 	//echo $sql;die;
	$arr=array();
 	echo create_list_view("list_view", "Return No, Year, Batch No, Item Description, Return Date, Return Qnty","70,60,140,280,80","800","260",0, $sql , "js_set_value", "mst_id,issue_id", "", 1, "0,0,0,0,0,0", $arr, "recv_number_prefix_num,year,batch_no,product_name_details,receive_date,cons_quantity","","",'0,0,0,0,3,2') ;	
 	exit();
}

if($action=="populate_master_from_data")
{  
	
 	$sql = "select id,recv_number,company_id,receive_purpose,receive_date,challan_no,issue_id from inv_receive_master  where id='$data'";
	//echo $sql;
	$res = sql_select($sql);
	foreach($res as $row)
	{
 		echo "set_button_status(0, permission, 'fnc_fabric_issue_rtn',1,1);";
		echo "$('#txt_system_id').val('".$row[csf("recv_number")]."');\n";
		echo "$('#issue_mst_id').val('".$row[csf("id")]."');\n";
		echo "$('#cbo_issue_purpose').val('".$row[csf("receive_purpose")]."');\n";
		echo "$('#txt_issue_id').val('".$row[csf("issue_id")]."');\n";
		$issue_num = return_field_value("issue_number"," inv_issue_master","id='".$row[csf("issue_id")]."'");
		echo "$('#txt_issue_no').val('$issue_num');\n";
		echo "return_qnty_basis('".$row[csf("receive_basis")]."');\n";
		echo "$('#txt_issue_date').val('".change_date_format($row[csf("receive_date")])."');\n";
		
		echo "$('#txt_challan_no').val('".$row[csf("challan_no")]."');\n";
		echo "disable_enable_fields( 'cbo_company_name', 1, '', '' );\n"; // disable true
				
   	}	
	exit();	
}

if ($action=="issue_return_print")
{
    extract($_REQUEST);
	$data=explode('*',$data);
	//print_r ($data);

	$sql=" select id, recv_number, issue_id, challan_no, receive_date from  inv_receive_master where id='$data[3]' and entry_form=209 and item_category=3";
	
	$dataArray=sql_select($sql);
	$company_library=return_library_array( "select id, company_name from lib_company", "id", "company_name");
	$supplier_library=return_library_array( "select id,supplier_name from  lib_supplier", "id","supplier_name");
	$color_arr=return_library_array( "select id, color_name from lib_color", "id","color_name");
	$batch_arr=array();
	$sql_batch=sql_select("select id, batch_no, color_id from pro_batch_create_mst where status_active=1 and is_deleted=0");
	foreach($sql_batch as $val)
	{
		$batch_arr[$val[csf('id')]]['batch']=$val[csf('batch_no')];
		$batch_arr[$val[csf('id')]]['color']=$val[csf('color_id')];
	}
	
	//$issueNo_arr=return_library_array( "select id, issue_number from inv_issue_master where status_active=1 and is_deleted=0 and item_category=2 and entry_form=18", "id", "issue_number");

	$issueNo_result = sql_select("select id, issue_number,knit_dye_company,knit_dye_source from inv_issue_master where status_active=1 and is_deleted=0 and item_category=3 and entry_form=19 and id='$data[4]'");
	foreach ($issueNo_result as $value) 
	{
		$issueNo_arr[$value[csf("id")]]["issue_number"] = $value[csf("issue_number")];
		$issueNo_arr[$value[csf("id")]]["knit_dye_source"] = $value[csf("knit_dye_source")];
		$issueNo_arr[$value[csf("id")]]["knit_dye_company"] = $value[csf("knit_dye_company")];
	}

?>
    <div style="width:930px;">
        <table width="900" cellspacing="0" align="right">
            <tr>
                <td colspan="6" align="center" style="font-size:22px"><strong><? echo $company_library[$data[0]]; ?></strong></td>
            </tr>
            <tr class="form_caption">
                <td colspan="6" align="center" style="font-size:14px">  
                    <?
                        $nameArray=sql_select( "select plot_no, level_no, road_no, block_no, country_id, province, city, zip_code, email, website from lib_company where id=$data[0]"); 
                        foreach ($nameArray as $result)
                        { 
                        ?>
                            Plot No: <? echo $result[csf('plot_no')]; ?> 
                            Level No: <? echo $result[csf('level_no')]?>
                            Road No: <? echo $result[csf('road_no')]; ?> 
                            Block No: <? echo $result[csf('block_no')];?> 
                            City No: <? echo $result[csf('city')];?> 
                            Zip Code: <? echo $result[csf('zip_code')]; ?> 
                            Province No: <? echo $result[csf('province')];?> 
                            Country: <? echo $country_arr[$result[csf('country_id')]]; ?><br> 
                            Email Address: <? echo $result[csf('email')];?> 
                            Website No: <? echo $result[csf('website')];
                        }
                    ?> 
                </td>  
            </tr>
            <tr>
                <td colspan="6" align="center" style="font-size:16px"><strong><u><? echo $data[2]; ?> Challan</u></strong></td>
            </tr>
            <tr>
                <td width="120"><strong>Return ID:</strong></td><td width="175px"><? echo $dataArray[0][csf('recv_number')]; ?></td>
                <td width="130"><strong>Return Date:</strong></td><td width="175px"><? echo change_date_format($dataArray[0][csf('receive_date')]); ?></td>
                <td width="125"><strong>Issue No:</strong></td> <td width="175px"><? echo $issueNo_arr[$dataArray[0][csf('issue_id')]]["issue_number"]; ?></td>
            </tr>
            <tr>
                <td><strong>Challan:</strong></td> <td><? echo $dataArray[0][csf('challan_no')]; ?></td>
                <td><strong>Service Company</strong></td>
                <td>
                	<?
                		if($issueNo_arr[$dataArray[0][csf('issue_id')]]["knit_dye_source"] == 1)
                		{
                			echo $company_library[$issueNo_arr[$dataArray[0][csf('issue_id')]]["knit_dye_company"]];
                		}else{
                			echo $supplier_library[$issueNo_arr[$dataArray[0][csf('issue_id')]]["knit_dye_company"]];
                		}
                		
                	?>
                </td>
                <td><strong>&nbsp;</strong></td><td><? //echo $dataArray[0][csf('remarks')]; ?></td>
            </tr>
        </table>
     <br>
        <div style="width:100%;">
        <table align="right" cellspacing="0" cellpadding="0" width="900" border="1" rules="all" class="rpt_table" >
            <thead bgcolor="#dddddd" align="center">
                <th width="30">SL</th>
                <th width="110">Store</th>
                <th width="70">Batch No</th>
                <th width="200">Item Description</th>
                <th width="80">Color</th> 
               <!--  <th width="60">Rack</th>
                <th width="60">Self</th> -->
                <th width="60">Roll</th> 
                <th width="100">Returned Qty.</th>
                <th>Remarks</th>
            </thead>
            <tbody>
        <?
            $store_arr=return_library_array( "select id, store_name from lib_store_location",'id','store_name');
            
            $i=1;
            $mst_id=$dataArray[0][csf('id')];
        
            $sql_dtls="Select a.id as pd_id, a.product_name_details, a.lot, b.id, b.cons_uom, b.batch_id_from_fissuertn, b.rack, b.self, b.cons_quantity, b.store_id, b.no_of_roll, b.remarks from product_details_master a, inv_transaction b where a.id=b.prod_id and b.transaction_type=4 and b.item_category=3 and b.mst_id='$data[3]' and b.status_active=1 and b.is_deleted=0";
            //echo $sql_dtls;
            $sql_result = sql_select($sql_dtls);	
            foreach($sql_result as $row)
            {
                if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
                ?>
                <tr bgcolor="<? echo $bgcolor; ?>">
                    <td align="center"><? echo $i; ?></td>
                    <td><p><? echo $store_arr[$row[csf("store_id")]]; ?></p></td>
                    <td><p><? echo $batch_arr[$row[csf('batch_id_from_fissuertn')]]['batch']; ?></p></td>
                    <td><p><? echo $row[csf("product_name_details")]; ?></p></td>
                    <td><p><? echo $color_arr[$batch_arr[$row[csf('batch_id_from_fissuertn')]]['color']]; ?></p></td>
                    <!-- <td><? //echo $row[csf("rack")]; ?></td>
                    <td><? //echo $row[csf("self")]; ?></td> -->
                    <td align="right"><? echo $row[csf("no_of_roll")]; ?></td>
                    <td align="right"><? echo number_format($row[csf("cons_quantity")],2,'.',''); ?></td>
                    <td><p><? echo $row[csf("remarks")]; ?></p></td>
                </tr>
                <? 
                $cons_quantity_sum+=$row[csf('cons_quantity')];
                $i++; 
            } ?>
            </tbody>
            <tfoot>
                <tr>
                    <td colspan="6" align="right">Total :</td>
                    <td align="right"><? echo number_format($cons_quantity_sum,2,'.',''); ?></td>
                    <td>&nbsp;</td>
                </tr>                           
            </tfoot>
        </table>
        <br>
         <?
            echo signature_table(129, $data[0], "900px");
         ?>
        </div>
	</div>
	<?
    exit();			
}
?>
