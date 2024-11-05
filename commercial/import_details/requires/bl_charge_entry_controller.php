<?
header('Content-type:text/html; charset=utf-8');
session_start();
include('../../../includes/common.php');

$user_id = $_SESSION['logic_erp']["user_id"];
if( $_SESSION['logic_erp']['user_id'] == "" ) { header("location:login.php"); die; }
$permission=$_SESSION['page_permission'];

$data=$_REQUEST['data'];
$action=$_REQUEST['action'];
 
//--------------------------- Start-------------------------------------//

if ($action=="load_drop_down_buyer")
{
	if($data != 0)
	{
		echo create_drop_down("cbo_buyer_name", 162, "select buy.id, buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company='$data' $buyer_cond and buy.id in (select buyer_id from lib_buyer_party_type where party_type in (1,3,21,90)) order by buy.buyer_name", "id,buyer_name", 1, "-- Select Buyer --", $selected, "");
	}
	else {
		echo create_drop_down( "cbo_buyer_name", 162, "SELECT buy.id as id ,buy.buyer_name as buyer_name  from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id $buyer_cond and buy.id in (select  buyer_id from  lib_buyer_party_type where party_type in (1,3,21,90)) group by buy.id,buy.buyer_name order by buyer_name","id,buyer_name", 1, "-- Select Buyer --", $selected, "" );
	exit();
	}
}

if($action=="invoice_popup_search")
{
	echo load_html_head_contents("Export Information Entry Form", "../../../", 1, 1,'','1','');
	extract($_REQUEST);
	?>

	<script>

		function js_set_value(data)
		{
			//alert(data);
			var data_string=data.split('_');
			$('#hidden_invoice_id').val(data_string[0]);
			$('#company_id').val(data_string[1]);

			parent.emailwindow.hide();
		}

    </script>

	</head>

	<body>
	<div align="center" style="width:900px;">
		<form name="searchexportinformationfrm"  id="searchexportinformationfrm">
			<fieldset style="width:880px;">
			<legend>Enter search words</legend>
				<table cellpadding="0" cellspacing="0" width="860" class="rpt_table" border="1" rules="all">
				<input type="hidden" name="hidden_invoice_id" id="hidden_invoice_id"  />
				<input type="hidden" name="company_id" id="company_id" />
					<thead>
						<tr>
                            <th colspan="4"><? echo create_drop_down( "cbo_string_search_type", 130, $string_search_type,'', 1, "-- Searching Type --",1 ); ?></th>
                            <th colspan="2"></th>
                        </tr>
						<tr>
							<th>Company</th>
							<th>Buyer</th>
							<th>Search By</th>
							<th>Invoice Date Range</th>
							<th>Enter Invoice No</th>
							<th>
								<input type="reset" name="reset" id="reset" value="Reset" style="width:100px" class="formbutton" />
							</th>
						</tr>
					</thead>
					<tr class="general">
						<td>
							<?
								echo create_drop_down( "cbo_company_name", 170, "select comp.id, comp.company_name from lib_company comp where comp.status_active=1 and comp.is_deleted=0 $company_cond order by comp.company_name","id,company_name", 1, "--- Select Company ---", $cbo_company_name, "load_drop_down( 'bl_charge_entry_controller', this.value, 'load_drop_down_buyer', 'buyer_td_id' );",1 );
							?>
						</td>
						<td id="buyer_td_id">
							<?
							echo create_drop_down("cbo_buyer_name", 162, "SELECT buy.id as id ,buy.buyer_name as buyer_name  from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id $buyer_cond and buy.id in (select  buyer_id from  lib_buyer_party_type where party_type in (1,3,21,90)) group by buy.id,buy.buyer_name order by buyer_name", "id,buyer_name", 1, "--- Select Buyer ---", $selected, "");
							?>
						</td>
						<td>
							<?
								$arr=array(1=>'Invoice NO');
								echo create_drop_down( "cbo_search_by", 100, $arr,"", 0, "", 0, "" );
							?>
						</td>
						<td>
							<input type="text" name="invoice_start_date" id="invoice_start_date" class="datepicker" style="width:70px;" />To
                            <input type="text" name="invoice_end_date" id="invoice_end_date" class="datepicker" style="width:70px;" />
						</td>
						<td>
							<input type="text" style="width:130px" class="text_boxes"  name="txt_search_common" id="txt_search_common" />
						</td>
						<td>
							<input type="button" id="search_button" class="formbutton" value="Show" onClick="show_list_view ( document.getElementById('cbo_company_name').value+'**'+document.getElementById('cbo_buyer_name').value+'**'+document.getElementById('cbo_search_by').value+'**'+document.getElementById('txt_search_common').value+'**'+document.getElementById('invoice_start_date').value+'**'+document.getElementById('invoice_end_date').value+'**'+document.getElementById('cbo_string_search_type').value,'invoice_search_list_view', 'search_div', 'bl_charge_entry_controller', 'setFilterGrid(\'list_view\',-1)')" style="width:100px;" />
						</td>
					</tr> 
				</table>
				<div style="width:100%; margin-top:10px" id="search_div" align="left"></div>
			</fieldset>
		</form>
	</div>
	</body>
	<script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
	</html>
	<?
	exit();
}
if($action==='invoice_search_list_view')
{
	list($company_id, $buyer_id, $search_by, $invoice_num, $invoice_start_date, $invoice_end_date, $search_string) = explode('**', $data);

	if($buyer_id==0)
	{
		if ($_SESSION['logic_erp']['data_level_secured']==1)
		{
			if($_SESSION['logic_erp']["buyer_id"]!='') $buyer_id_cond=" and a.buyer_id in (".$_SESSION['logic_erp']["buyer_id"].")"; else $buyer_id_cond="";
		}
		else
		{
			$buyer_id_cond='';
		}
	}
	else
	{
		$buyer_id_cond=" and a.buyer_id=$buyer_id";
	}

	$search_text=''; $company_cond ='';
	if($company_id !=0) $company_cond = "and a.benificiary_id=$company_id";

	if ($invoice_num != '')
	{
		if($search_string==1)
			$search_text="and a.invoice_no like '".trim($invoice_num)."'";
		else if ($search_string==2) 
			$search_text="and a.invoice_no like '".trim($invoice_num)."%'";
		else if ($search_string==3)
			$search_text="and a.invoice_no like '%".trim($invoice_num)."'";
		else if ($search_string==4 || $search_string==0)
			$search_text="and a.invoice_no like '%".trim($invoice_num)."%'";
	}

	if ($invoice_start_date != '' && $invoice_end_date != '') 
	{
        if ($db_type == 0) {
            $date_cond = "and a.invoice_date between '" . change_date_format($invoice_start_date, 'yyyy-mm-dd') . "' and '" . change_date_format($invoice_end_date, 'yyyy-mm-dd') . "'";
        } else if ($db_type == 2) {
            $date_cond = "and a.invoice_date between '" . change_date_format($invoice_start_date, '', '', 1) . "' and '" . change_date_format($invoice_end_date, '', '', 1) . "'";
        }
    } 
    else 
    {
        $date_cond = '';
    }

    $buyer_arr=return_library_array( "select id, short_name from lib_buyer",'id','short_name');
	$comp_arr=return_library_array( "select id, company_short_name from lib_company",'id','company_short_name');

	// $sql = "SELECT id as ID, benificiary_id as BENIFICIARY_ID, buyer_id as BUYER_ID, invoice_no as INVOICE_NO, invoice_date as INVOICE_DATE, is_lc as IS_LC, lc_sc_id as LC_SC_ID, invoice_value as INVOICE_VALUE, net_invo_value as NET_INVO_VALUE, import_btb as IMPORT_BTB 
	// from com_export_invoice_ship_mst 
	// where status_active=1 and is_deleted=0 $company_cond $search_text $buyer_id_cond $date_cond  order by invoice_date desc";

	$sql = "SELECT a.id as ID, a.benificiary_id as BENIFICIARY_ID, a.buyer_id as BUYER_ID, a.invoice_no as INVOICE_NO, a.invoice_date as INVOICE_DATE, a.is_lc as IS_LC, a.lc_sc_id as LC_SC_ID, a.invoice_value as INVOICE_VALUE, a.net_invo_value as NET_INVO_VALUE, a.import_btb as IMPORT_BTB 
	from com_export_invoice_ship_mst a
	left join  bl_charge b on a.id = b.invoice_id and b.status_active=1 and b.is_deleted=0
	where a.status_active=1 and a.is_deleted=0  $company_cond $search_text $buyer_id_cond $date_cond and a.id not in (SELECT invoice_id FROM bl_charge WHERE is_deleted=0 and status_active=1)  order by invoice_date desc";
	//echo $sql;
	$data_array=sql_select($sql);		
	$lc_arr=return_library_array( "select id, export_lc_no from com_export_lc",'id','export_lc_no');
	$sc_arr=return_library_array( "select id, contract_no from com_sales_contract",'id','contract_no');


	if ($invoice_num != '' && count($data_array)==0)
	{
		?>
		<div align="center"><span style="color:red;font-weight:bold; font-size: 20px;">** Already BL done with this Invoice **</span></div>
		<?
	}
	
	?>
	<table width="880" cellpadding="0" cellspacing="0" border="1" class="rpt_table" rules="all">
        <thead>
            <th width="40">SL</th>
            <th width="100">Company</th>
            <th width="100">Buyer</th>
            <th width="150">Invoice No</th>
            <th width="100">Invoice Date</th>
            <th width="150">LC/SC No</th>
            <th width="100">LC/SC</th>
            <th>Net Invoice Value</th>
        </thead>
     </table>
     <div style="width:900px; overflow-y:scroll; max-height:280px">
     	<table width="880" cellpadding="0" cellspacing="0" border="1" class="rpt_table" rules="all" id="list_view">
		<?			
            $i = 1;
            foreach($data_array as $row)
            {
                if ($i%2==0)
                    $bgcolor="#FFFFFF";
                else
                    $bgcolor="#E9F3FF";

				if($row['IS_LC']==1)
				{
					$lc_sc_no=$lc_arr[$row['LC_SC_ID']];
					$is_lc_sc='LC';
				}
				else
				{
					$lc_sc_no=$sc_arr[$row['LC_SC_ID']];
					$is_lc_sc='SC';
				}

				if($row['IMPORT_BTB']==1) $buyer=$comp_arr[$row['BUYER_ID']]; else $buyer=$buyer_arr[$row['BUYER_ID']];
				?>
				<tr bgcolor="<? echo $bgcolor; ?>" style="text-decoration:none; cursor:pointer;" id="search<? echo $i;?>" onClick="js_set_value( '<? echo $row['ID']; ?>_<? echo $row['BENIFICIARY_ID']; ?>');" >                	
					<td width="40"><? echo $i; ?></td>
					<td width="100"><p><? echo $comp_arr[$row['BENIFICIARY_ID']]; ?></p></td>
					<td width="100"><p><? echo $buyer; ?></p></td>
                    <td width="150"><p><? echo $row['INVOICE_NO']; ?></p></td>
					<td width="100" align="center"><p><? echo change_date_format($row['INVOICE_DATE']); ?></td>
                    <td width="150"><p><? echo $lc_sc_no; ?></p></td>
                    <td width="100" align="center"><p><? echo $is_lc_sc; ?></p></td>
					<td align="right"><p><?
					echo number_format($row['NET_INVO_VALUE'],2); ?></p></td>
				</tr>
            <?
			$i++;
            }
			?>
		</table>
    </div>
    <?
	exit();
}

if($action=="populate_data_from_invoice")
{
	$sql="SELECT id as ID,buyer_id as BUYER_ID,invoice_no as INVOICE_NO,invoice_date as INVOICE_DATE,invoice_value as INVOICE_VALUE,invoice_quantity as INVOICE_QUANTITY,bl_no as BL_NO,bl_date as BL_DATE,bl_rev_date as BL_REV_DATE,forwarder_name as FORWARDER_NAME,shipping_mode as SHIPPING_MODE,ex_factory_date as EX_FACTORY_DATE
	from com_export_invoice_ship_mst mst WHERE id=$data";
    // echo $sql;
	$data_array=sql_select($sql);

 	foreach ($data_array as $row)
	{
		echo "document.getElementById('cbo_buyer_name').value 		= ".$row["BUYER_ID"].";\n";
		echo "document.getElementById('txt_invoice_no').value 		= '".$row["INVOICE_NO"]."';\n";
		echo "document.getElementById('txt_invoice_date').value 	= '".change_date_format($row["INVOICE_DATE"])."';\n";
		echo "document.getElementById('txt_invoice_value').value 		= '".$row["INVOICE_VALUE"]."';\n";
		echo "document.getElementById('txt_invoice_qnty').value 		= '".$row["INVOICE_QUANTITY"]."';\n";

		echo "document.getElementById('txt_bl_no').value				= '".$row["BL_NO"]."';\n";
		echo "document.getElementById('txt_bl_date').value 				= '".change_date_format($row["BL_DATE"])."';\n";
		echo "document.getElementById('txt_bl_rev_date').value 			= '".change_date_format($row["BL_REV_DATE"])."';\n";
		echo "document.getElementById('cbo_forwarder_name').value 		= '".$row["FORWARDER_NAME"]."';\n";
		echo "document.getElementById('cbo_shipment_id').value 		= '".$row["SHIPPING_MODE"]."';\n";
		echo "document.getElementById('txt_ex_factory').value 		= '".change_date_format($row["EX_FACTORY_DATE"])."';\n";

		echo "document.getElementById('invoice_id').value 			= '".$row["ID"]."';\n";
		exit();
	}
}

if ($action=="save_update_delete")
{
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process )); 

	if ($operation==0) // Insert Here----------------------------------------------------------
	{
		$con = connect();
		if($db_type==0)
		{
			mysql_query("BEGIN");
		}
	
		$mst_id=return_next_id("id", "BL_CHARGE", 1);
		
		if($db_type==0) $insert_date_con="and YEAR(insert_date)=".date('Y',time())."";
		else if($db_type==2) $insert_date_con="and TO_CHAR(insert_date,'YYYY')=".date('Y',time())."";
		
		$new_sys_no=explode("*",return_mrr_number( str_replace("'","",$cbo_company_name), '', 'BLC', date("Y",time()), 5, "select sys_number_prefix,sys_number_prefix_num from bl_charge where company_id=$cbo_company_name $insert_date_con order by id desc ", "sys_number_prefix", "sys_number_prefix_num" ));

		$field_array_mst="id, sys_number, sys_number_prefix, sys_number_prefix_num, company_id, invoice_id, bl_charge_date,bl_no,bl_date,forwarder_name, remarks, bl_charge, stamp_charge, air_company_charge, air_buyer_charge,adjustment_charge,surrendered_charge,special_charge, others_charge, ready_to_approve, inserted_by, insert_date, status_active, is_deleted";
		
		$data_array_mst="(".$mst_id.",'".$new_sys_no[0]."','".$new_sys_no[1]."','".$new_sys_no[2]."',".$cbo_company_name.",".$invoice_id.",".$txt_bl_change_date.",".$txt_bl_no.",".$txt_bl_date.",".$cbo_forwarder_name.",".$txt_remarks.",".$txt_bl_charge.",".$txt_stamp_charge.",".$txt_air_company_charge.",".$txt_air_buyer_charge.",".$txt_adjustment_local_charge.",".$txt_mbl_surrendered_charge.",".$txt_special_permission_charge.",".$txt_others_charge.",".$ready_to_approve.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."',1,0)";
		// echo "10**INSERT INTO BL_CHARGE (".$field_array_mst.") VALUES ".$data_array_mst."</br>"; 
		// die;
		
		$rID=sql_insert("BL_CHARGE",$field_array_mst,$data_array_mst,0);
		// echo '100**'.$rID;oci_rollback($con);die;

		$field_array="bl_no*bl_date*forwarder_name*updated_by*update_date";
		$data_array=$txt_bl_no."*".$txt_bl_date."*".$cbo_forwarder_name."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'";
		$rID2=sql_update("com_export_invoice_ship_mst",$field_array,$data_array,"id",$invoice_id,1);
		
		if($db_type==0)
		{
			if($rID==1 && $rID2==1)
			{
				mysql_query("COMMIT");  
				echo "0**".$new_sys_no[0]."**".$mst_id;
			}
			else
			{
				mysql_query("ROLLBACK"); 
				echo "10**";
			}
		}
		else if($db_type==2 || $db_type==1 )
		{
			if($rID==1 && $rID2==1)
			{
				oci_commit($con);  
				echo "0**".$new_sys_no[0]."**".$mst_id;
			}
			else
			{
				oci_rollback($con);
				echo "10**";
			}
		}
		disconnect($con);
		die;
	}
	else if ($operation==1) // Update Here----------------------------------------------------------
	{
		$con = connect();
		if($db_type==0)
		{
			mysql_query("BEGIN");
		}

		$is_posted=sql_select("select is_posted_account from BL_CHARGE where id=$update_id and status_active=1");

		if($is_posted[0][csf("is_posted_account")] == 1)
		{
			echo "20**Already Posted in Accounts, so update and delete is not allowed";
			disconnect($con);die;
		}

		$field_array_mst="company_id*invoice_id*bl_charge_date*remarks*bl_charge*stamp_charge*air_company_charge*air_buyer_charge*adjustment_charge*surrendered_charge*special_charge*others_charge*ready_to_approve*bl_no*bl_date*forwarder_name*updated_by*update_date";
		$data_array_mst="".$cbo_company_name."*".$invoice_id."*".$txt_bl_change_date."*".$txt_remarks."*".$txt_bl_charge."*".$txt_stamp_charge."*".$txt_air_company_charge."*".$txt_air_buyer_charge."*".$txt_adjustment_local_charge."*".$txt_mbl_surrendered_charge."*".$txt_special_permission_charge."*".$txt_others_charge."*".$ready_to_approve."*".$txt_bl_no."*".$txt_bl_date."*".$cbo_forwarder_name."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'";

		$rID=sql_update("BL_CHARGE",$field_array_mst,$data_array_mst,"id","".$update_id."",0);
		// echo "10**".$rID."</br>"; die;

		$field_array="bl_no*bl_date*forwarder_name*updated_by*update_date";
		$data_array=$txt_bl_no."*".$txt_bl_date."*".$cbo_forwarder_name."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'";
		$rID2=sql_update("com_export_invoice_ship_mst",$field_array,$data_array,"id",$invoice_id,1);

		if($db_type==0)
		{
			if($rID==1 && $rID2==1)
			{
				mysql_query("COMMIT");  
				echo "1**".str_replace("'",'',$txt_system_id)."**".str_replace("'",'',$update_id);
			}
			else
			{
				mysql_query("ROLLBACK"); 
				echo "10**";
			}
		}
		else if($db_type==2 || $db_type==1 )
		{
			if($rID==1 && $rID2==1)
			{
				oci_commit($con);  
				echo "1**".str_replace("'",'',$txt_system_id)."**".str_replace("'",'',$update_id);
			}
			else
			{
				oci_rollback($con);
				echo "10**";
			}
		}
	
		disconnect($con);
		die;
	}
	else if ($operation==2) // Delete Here----------------------------------------------------------  
	{
		$con = connect();
		if($db_type==0)
		{
			mysql_query("BEGIN");
		}

		$is_posted=sql_select("select is_posted_account from BL_CHARGE where id=$update_id and status_active=1");

		if($is_posted[0][csf("is_posted_account")] == 1)
		{
			 echo "10**Already Posted in Accounts, so update and delete is not allowed";
			disconnect($con);die;
		}


		$field_array="status_active*is_deleted*updated_by*update_date";
		$data_array="0*1*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'";
		$rID=sql_delete("BL_CHARGE",$field_array,$data_array,"id","".$update_id."",0);

		$field_array1="bl_no*bl_date*forwarder_name*updated_by*update_date";
		$txt_bl_no1="";
		$txt_bl_date1="";
		$cbo_forwarder_name1="";
		$data_array1="'".$txt_bl_no1."'*'".$txt_bl_date1."'*'".$cbo_forwarder_name1."'*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'";
		$rID2=sql_update("com_export_invoice_ship_mst",$field_array1,$data_array1,"id",$invoice_id,1);

		
		// echo "10**".$rID."</br>"; die;
		if($db_type==0)
		{
			if($rID==1 && $rID2==1)
			{
				mysql_query("COMMIT");  
				echo "2**".str_replace("'",'',$txt_system_id)."**".str_replace("'",'',$update_id);;
			}
			else
			{
				mysql_query("ROLLBACK"); 
				echo "10**";
			}
		}
		else if($db_type==2 || $db_type==1 )
		{
			if($rID==1 && $rID2==1)
			{
				oci_commit($con);  
				echo "2**".str_replace("'",'',$txt_system_id)."**".str_replace("'",'',$update_id);;
			}
			else
			{
				oci_rollback($con);
				echo "10**";
			}
		}
		
		disconnect($con);
	}// Delete Here End ----------------------------------------------------------
	
}

if ($action=="system_popup")
{
	echo load_html_head_contents("Popup Info", "../../../", 1, 1,$unicode,1,'');
	extract($_REQUEST);
	?>
	<script>
		function js_set_value( id )
		{
			document.getElementById('selected_id').value=id;
			parent.emailwindow.hide();
		}
    </script>
    </head>
    <body>
    <div align="center" style="width:100%;" >
    <form name="searchorderfrm_1"  id="searchorderfrm_1" autocomplete="off">
        <table width="750" cellspacing="0" cellpadding="0" border="1" rules="all" class="rpt_table" align="center">
            <thead>
                <tr>
                    <th colspan="6"><? echo create_drop_down( "cbo_string_search_type", 130, $string_search_type,'', 1, "-- Searching Type --" ); ?></th>
                </tr>
                <tr>
                    <th class="must_entry_caption">Company Name</th>
					<th >System ID</th>
                    <th>Invoice No</th>
                    <th colspan="2">BL Change Date Range</th>
					<th><input type="reset" name="reset" id="reset" value="Reset" style="width:100px" class="formbutton" />
					</th>
                </tr>        
            </thead>
            <tbody>
                <tr class="general">
                    <td class="must_entry_caption"> 
                        <input type="hidden" id="selected_id">
                        <? echo create_drop_down( "cbo_company_id", 150, "select comp.id, comp.company_name from lib_company comp where comp.status_active=1 and comp.is_deleted=0 $company_cond order by comp.company_name","id,company_name", 1, "-- Select Company --", $cbo_company_name, '',1);?>
                    </td>
                    <td > 
						<input name="txt_sys" id="txt_sys" class="text_boxes" style="width:100px">
                    </td>
					<td >
						<input type="text" style="width:130px;" class="text_boxes"  name="txt_invoice_no" id="txt_invoice_no" />
					</td>
                    <td><input name="txt_date_from" id="txt_date_from" class="datepicker" style="width:60px" placeholder="From Date"></td>
                    <td><input name="txt_date_to" id="txt_date_to" class="datepicker" style="width:60px" placeholder="To Date"></td> 
                    <td align="center">
                        <input type="button" name="button2" class="formbutton" value="Show" onClick="show_list_view ( document.getElementById('cbo_company_id').value+'_'+document.getElementById('txt_sys').value+'_'+document.getElementById('txt_date_from').value+'_'+document.getElementById('txt_date_to').value+'_'+document.getElementById('cbo_year_selection').value+'_'+document.getElementById('cbo_string_search_type').value+'_'+document.getElementById('txt_invoice_no').value, 'create_system_search_list_view', 'search_div', 'bl_charge_entry_controller', 'setFilterGrid(\'search_div\',-1)');" style="width:100px;" /></td>
                </tr>
                <tr>
                    <td align="center" colspan="8"><? echo load_month_buttons(1); ?></td>
                </tr>
            </tbody>
        </table>
		<br>
        <div id="search_div"></div>
    </form>
    </div>
    </body>
      <script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
    </html>
    <?
    exit();
}

if($action=="create_system_search_list_view")
{
	// echo $data;die;
	$com_cond="";$date_cond ="";$year_cond="";$search_text=""; $sys_id="";
	list($company_id,$search_sys,$submission_start_date, $submission_end_date,$year,$search_string,$invoice_num ) = explode('_', $data);
	if ($company_id!=0) {$com_cond=" and a.company_id=$company_id";}
    if ($search_sys != ''){
        $sys_id=" and a.sys_number_prefix_num=$search_sys";
    }
	if ($submission_start_date != '' && $submission_end_date != '') 
	{
        if ($db_type == 0) {
            $date_cond = "and a.bl_charge_date '" . change_date_format($submission_start_date, 'yyyy-mm-dd') . "' and '" . change_date_format($submission_end_date, 'yyyy-mm-dd') . "'";
        } else if ($db_type == 2) {
            $date_cond = "and a.bl_charge_date between '" . change_date_format($submission_start_date, '', '', 1) . "' and '" . change_date_format($submission_end_date, '', '', 1) . "'";
		}
    } 
    else 
    {
        $date_cond = '';
		if($year!=""){
			if($db_type==0)
			{
				$year_cond=" and YEAR(a.bl_charge_date) =$year ";
			}
			else
			{	
				$year_cond=" and to_char(a.bl_charge_date,'YYYY') =$year ";
			}
		}
	}

    if ($invoice_num != '')
	{
		if($search_string==1)
			$search_text="and b.invoice_no like '".trim($invoice_num)."'";
		else if ($search_string==2) 
			$search_text="and b.invoice_no like '".trim($invoice_num)."%'";
		else if ($search_string==3)
			$search_text="and b.invoice_no like '%".trim($invoice_num)."'";
		else if ($search_string==4 || $search_string==0)
			$search_text="and b.invoice_no like '%".trim($invoice_num)."%'";
	}

	$company_arr=return_library_array( "select id, company_name from lib_company",'id','company_name');
    $arr=array(0=>$company_arr);
    $sql = "SELECT a.id,a.sys_number_prefix_num,a.company_id,a.bl_charge_date,b.invoice_no, b.invoice_value 
    from bl_charge a, com_export_invoice_ship_mst b 
    where a.invoice_id=b.id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $com_cond $search_text $sys_id $date_cond $year_cond order by a.id desc";

    // echo $sql;die;
	echo  create_list_view("search_div", "Company Name,System ID,Invoice No,Invoice_value,BL Change Date", "150,70,120,100,80","600","300",0, $sql , "js_set_value", "id", "", 1, "company_id,0,0,0,0", $arr , "company_id,sys_number_prefix_num,invoice_no,invoice_value,bl_charge_date", "",'','0,0,0,0,3');
	exit();
}

if ($action=="populate_data_from_search_popup")
{
	$data_array="SELECT a.id as ID,a.sys_number as SYS_NUMBER, a.company_id as COMPANY_ID,a.invoice_id as INVOICE_ID,a.bl_charge_date as BL_CHARGE_DATE, a.remarks as REAMRKS, a.bl_charge as BL_CHARGE, a.stamp_charge as STAMP_CHARGE,a.READY_TO_APPROVE, a.air_company_charge as AIR_COMPANY_CHARGE, a.air_buyer_charge as AIR_BUYER_CHARGE,a.adjustment_charge as ADJUSTMENT_CHARGE,a.surrendered_charge as SURRENDERED_CHARGE,a.special_charge as SPECIAL_CHARGE , a.others_charge as OTHERS_CHARGE,b.buyer_id as BUYER_ID,b.invoice_no as INVOICE_NO,b.invoice_date as INVOICE_DATE,invoice_value as INVOICE_VALUE,b.invoice_quantity as INVOICE_QUANTITY,a.bl_no as BL_NO,a.bl_date as BL_DATE,b.bl_rev_date as BL_REV_DATE,b.forwarder_name as FORWARDER_NAME,b.shipping_mode as SHIPPING_MODE,b.ex_factory_date as EX_FACTORY_DATE, a.is_posted_account as IS_POSTED_ACCOUNT
    from bl_charge a, com_export_invoice_ship_mst b 
    where a.id='$data' and a.invoice_id=b.id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 ";
    // echo $data_array;die;


	


    $data_result=sql_select($data_array);
    echo "document.getElementById('txt_system_id').value = '".$data_result[0]["SYS_NUMBER"]."';\n";  
    echo "document.getElementById('cbo_company_name').value = '".$data_result[0]["COMPANY_ID"]."';\n";  
    echo "document.getElementById('txt_bl_change_date').value = '".change_date_format($data_result[0]["BL_CHARGE_DATE"])."';\n";
    echo "document.getElementById('txt_remarks').value = '".$data_result[0]["REAMRKS"]."';\n";

    echo "document.getElementById('cbo_buyer_name').value 		= ".$data_result[0]["BUYER_ID"].";\n";
    echo "document.getElementById('ready_to_approve').value 		= ".$data_result[0]["READY_TO_APPROVE"].";\n";
    echo "document.getElementById('txt_invoice_no').value 		= '".$data_result[0]["INVOICE_NO"]."';\n";
    echo "document.getElementById('invoice_id').value 			= '".$data_result[0]["INVOICE_ID"]."';\n"; 
    echo "document.getElementById('txt_invoice_date').value 	= '".change_date_format($data_result[0]["INVOICE_DATE"])."';\n";
    echo "document.getElementById('txt_invoice_value').value 		= '".$data_result[0]["INVOICE_VALUE"]."';\n";
    echo "document.getElementById('txt_invoice_qnty').value 		= '".$data_result[0]["INVOICE_QUANTITY"]."';\n";
    echo "document.getElementById('txt_bl_no').value				= '".$data_result[0]["BL_NO"]."';\n";
    echo "document.getElementById('txt_bl_date').value 				= '".change_date_format($data_result[0]["BL_DATE"])."';\n";
    echo "document.getElementById('txt_bl_rev_date').value 			= '".change_date_format($data_result[0]["BL_REV_DATE"])."';\n";
    echo "document.getElementById('cbo_forwarder_name').value 		= '".$data_result[0]["FORWARDER_NAME"]."';\n";
    echo "document.getElementById('cbo_shipment_id').value 		= '".$data_result[0]["SHIPPING_MODE"]."';\n";
    echo "document.getElementById('txt_ex_factory').value 		= '".change_date_format($data_result[0]["EX_FACTORY_DATE"])."';\n";

    echo "document.getElementById('txt_bl_charge').value 		= '".$data_result[0]["BL_CHARGE"]."';\n";
    echo "document.getElementById('txt_stamp_charge').value 	= '".$data_result[0]["STAMP_CHARGE"]."';\n";
    echo "document.getElementById('txt_air_company_charge').value = '".$data_result[0]["AIR_COMPANY_CHARGE"]."';\n";
    echo "document.getElementById('txt_air_buyer_charge').value = '".$data_result[0]["AIR_BUYER_CHARGE"]."';\n";
    echo "document.getElementById('txt_adjustment_local_charge').value = '".$data_result[0]["ADJUSTMENT_CHARGE"]."';\n";
    echo "document.getElementById('txt_mbl_surrendered_charge').value = '".$data_result[0]["SURRENDERED_CHARGE"]."';\n";
    echo "document.getElementById('txt_special_permission_charge').value = '".$data_result[0]["SPECIAL_CHARGE"]."';\n";
    echo "document.getElementById('txt_others_charge').value 	= '".$data_result[0]["OTHERS_CHARGE"]."';\n";
    echo "document.getElementById('update_id').value 	= '".$data_result[0]["ID"]."';\n";

	$msg="Already Posted in Accounts";
        if($data_result[0]["IS_POSTED_ACCOUNT"]==1){
			echo "$('#posted_account_td').text('".$msg."');\n";
		}else{
			
		}


	exit();
}