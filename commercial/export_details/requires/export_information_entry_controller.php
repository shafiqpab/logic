<?
header('Content-type:text/html; charset=utf-8');
session_start();
if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");
include('../../../includes/common.php');
$data=$_REQUEST['data'];
$action=$_REQUEST['action'];
$user_id=$_SESSION['logic_erp']["user_id"];
$permission=$_SESSION['page_permission'];

//--------------------------- Start-------------------------------------//
if ($action=="load_drop_down_buyer_for_excate")
{

	$data=explode("_",$data);
	if($data[0] != 0)
	{
		echo create_drop_down("cbo_buyer_name", 162, "select buy.id, buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company=$data[0] and buy.id=$data[1] $buyer_cond and buy.id in (select buyer_id from lib_buyer_party_type where party_type in (1,3,21,90)) group by buy.id,buy.buyer_name order by buy.buyer_name", "id,buyer_name", 1, "-- Select Buyer --", $selected, "");
	}
	else 
	{
		echo create_drop_down( "cbo_buyer_name", 162, "SELECT buy.id as id ,buy.buyer_name as buyer_name  from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id $buyer_cond and buy.id in (select  buyer_id from  lib_buyer_party_type where party_type in (1,3,21,90)) group by buy.id,buy.buyer_name order by buyer_name","id,buyer_name", 1, "-- Select Buyer --", $selected, "" );
	exit();
	}
}

if ($action=="load_drop_down_buyer")
{
	/*echo create_drop_down( "cbo_buyer_name", 162, "select buy.id, buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company='$data' $buyer_cond and buy.id in (select buyer_id from lib_buyer_party_type where party_type in (1,3,21,90)) order by buy.buyer_name","id,buyer_name", 1, "-- Select Buyer --", $selected, "" );
	exit();*/
	if($data != 0)
	{
		echo create_drop_down("cbo_buyer_name", 162, "select buy.id, buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company='$data' $buyer_cond and buy.id in (select buyer_id from lib_buyer_party_type where party_type in (1,3,21,90)) group by buy.id,buy.buyer_name order by buy.buyer_name", "id,buyer_name", 1, "-- Select Buyer --", $selected, "");
	}
	else {
		echo create_drop_down( "cbo_buyer_name", 162, "SELECT buy.id as id ,buy.buyer_name as buyer_name  from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id $buyer_cond and buy.id in (select  buyer_id from  lib_buyer_party_type where party_type in (1,3,21,90)) group by buy.id,buy.buyer_name order by buyer_name","id,buyer_name", 1, "-- Select Buyer --", $selected, "" );
	exit();
	}
}

if ($action == "load_drop_down_location")
{

	$location_res = sql_select("select id,location_name from lib_location where company_id='$data' and status_active =1 and is_deleted=0");
	$selected=0;
	if(count($location_res)==1)
	{
		$selected=$location_res[0][csf('id')];
	}
	$sql="select b.id as id, b.location_name || ' (' || a.company_short_name || ')' as location_name  from lib_company a, lib_location b where a.id=b.company_id and b.status_active =1 and b.is_deleted=0 and a.status_active =1 and a.is_deleted=0 order by b.location_name";
	// echo create_drop_down("cbo_location", 165, "select id,location_name from lib_location where company_id='$data' and status_active =1 and is_deleted=0 order by location_name", "id,location_name", 1, "-- Select Location --", 0, "");
	echo create_drop_down("cbo_location", 165, $sql, "id,location_name", 1, "-- Select Location --", $selected, "");
	exit();
}
if ($action=="load_drop_down_country_code")
{
	echo create_drop_down( "cbo_country_code", 162, "select id, ultimate_country_code from lib_country_loc_mapping where status_active =1 and is_deleted=0 and country_id='$data' ","id,ultimate_country_code", 1, "-- Select Country Code --", $selected, "" );
	exit();
}

if($action=="print_button_variable_setting")
{
    $print_report_format=0;
    $print_report_format=return_field_value("format_id","lib_report_template","template_name ='".$data."' and module_id=5 and report_id=245 and is_deleted=0 and status_active=1");
    echo "print_report_button_setting('".$print_report_format."');\n";
    exit();
}

if($action=="pinumber_popup")
{
  	echo load_html_head_contents("Invoice Popup Info","../../../", 1, 1, $unicode);
	extract($_REQUEST);
    ?>
        <script>
            function js_set_value(str)
            {
            	var splitData = str.split("_");
            	$("#invoice_id").val(splitData[0]);
            	$("#invoice_no").val(splitData[1]);
            	parent.emailwindow.hide();
            }
        </script>
    </head>
    <body>
        <div align="center" style="width:100%; margin-top:5px" >
            <form name="searchlcfrm_1" id="searchlcfrm_1" autocomplete="off">
            	<table width="770" cellspacing="0" cellpadding="0" border="1" rules="all" class="rpt_table" align="center">
                        <thead>
                            <tr>
                                <th>Company</th>
                                <th>Buyer</th>
                                <th>Invoice Year</th>
                                <th id="search_by_td_up">Invoice No</th>
                                <th>
                                	<input type="reset" id="res" value="Reset" style="width:100px" class="formbutton" onClick="reset_form('searchlcfrm_1','search_div','','','','');" />
                                    <input type="hidden" id="invoice_id" value="" />
                                    <input type="hidden" id="invoice_no" value="" />
                                </th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr align="center">
								<td>
								<?
									echo create_drop_down( "cbo_company_name", 170, "select comp.id, comp.company_name from lib_company comp where comp.status_active=1 and  comp.id=$companyID and comp.is_deleted=0  order by comp.company_name","id,company_name", 1, "--- Select Company ---", 0, "load_drop_down( 'export_information_entry_controller', this.value+'_'+$buyer_name, 'load_drop_down_buyer_for_excate', 'buyer_td_id' );" );
								?>
							</td>
							<td id="buyer_td_id">
								<?
									echo create_drop_down("cbo_buyer_name", 162, "", "id,buyer_name", 1, "--- Select Buyer ---", $selected, "");
								?>
							</td>
								<td>
								<? echo create_drop_down( "txt_year",90, $year, "", 0, "--All--", date('Y'), "", "", ""); ?>
								</td>
                                <td align="center" id="search_by_td">
                                    <input type="text" style="width:130px" class="text_boxes"  name="txt_invoice_no" id="txt_invoice_no" />
                                </td>                       
                                 <td align="center">
                                    <input type="button" name="btn_show" class="formbutton" value="Show" onClick="show_list_view ( document.getElementById('cbo_company_name').value+'_'+<?=$buyer_name ?>+'_'+document.getElementById('txt_invoice_no').value+'_'+document.getElementById('txt_year').value, 'create_invoice_search_list_view', 'search_div', 'export_information_entry_controller', 'setFilterGrid(\'list_view\',-1)')" style="width:100px;" />
                                </td>
                       	 	</tr>
                        </tbody>
                    </table>
                    <div align="center" style="margin-top:10px" id="search_div"> </div>
                </form>
           </div>
        </body>
        <script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
    </html>
    <?

}

if($action=="create_invoice_search_list_view")
{
	$ex_data = explode("_",$data);
	// print_r($ex_data);
	$company_arr = return_library_array("select id, company_name from lib_company ","id","company_name");
	$buyer_name=return_library_array("select id, buyer_name from lib_buyer where status_active = 1 and is_deleted = 0","id","buyer_name");
	$user_arr = return_library_array("select id, user_name from user_passwd", "id", "user_name");

	$company_id = $ex_data[0];
	$buyer_id = $ex_data[1];
	$invoice_no = trim($ex_data[2]);
	$year = $ex_data[3];

	 $sql_cond ='';
	 $sql_cond .= ($company_id!=0) ? " and a.company_id=$company_id" : "";
	 $sql_cond .= ($buyer_id!=0) ? " and a.buyer_id=$buyer_id" : "";
	 $sql_cond .= ($year!=0) ? " and a.invoice_year=$year" : "";
	 $sql_cond .= ($invoice_no != "") ? " AND a.INVOICE_NO LIKE '%$invoice_no%'" : "";

	$sql = "SELECT a.id, a.company_id, a.buyer_id, a.invoice_year, a.insert_date, a.invoice_no, a.invoice_status, a.inserted_by
	from lib_invoice_creation a
	left join com_export_invoice_ship_mst b on a.invoice_no = b.invoice_no
	where a.status_active = 1 and a.invoice_status = 1 and a.is_deleted = 0 $sql_cond and b.invoice_no is null order by a.id";

	$arr=array(1=>$company_arr,2=>$buyer_name,4=>$user_arr,5=>$row_status);
	echo create_list_view("list_view", "Invoice No, Company Name, Buyer Name, Invoice Year, Insert By,Status","130,110,130,90,130","780","260",0, $sql , "js_set_value", "id,invoice_no", "", 1, "0,company_id,buyer_id,0,inserted_by,invoice_status", $arr, "invoice_no,company_id,buyer_id,invoice_year,inserted_by,invoice_status", "",'','0,0,0,0,0,0') ;
	exit();
}

if ($action=="openpopup_dem_export") 
{
	extract($_REQUEST);
	list($buttonId,$user_ids) = explode('_',$data);
	echo load_html_head_contents("User Select","../../../", 1, 1, $unicode,'','');
	?>
    <script>

	    var userId ='<?= $user_ids;?>';

		function check_all_data(allUserStr){
			allUserArr = allUserStr.split(',');
			allUserArr.forEach((user_id) => {
				js_set_value(user_id);
			});
		}

		function toggle( x, origColor ) {
			var newColor = 'yellow';
			if ( x.style ) {
				x.style.backgroundColor = ( newColor == x.style.backgroundColor )? origColor : newColor;
			}
		}

		var selected_id = new Array();
		function js_set_value(user_id) {
			user_id = user_id*1;
			toggle( document.getElementById( 'tr_' + user_id ), '#E9F3FF' );

			if( jQuery.inArray(user_id, selected_id ) == -1 ) {
				selected_id.push(user_id);
			}
			else {
				for( var i = 0; i < selected_id.length; i++ ) {
					if( selected_id[i] == user_id) break;
				}
				selected_id.splice( i, 1 );
			}

			var id =''; 
			for( var i = 0; i < selected_id.length; i++ ) {
				id += selected_id[i] + ',';
			}
			id = id.substr( 0, id.length - 1 );
			$('#selected_print_report').val( id );
		}

		var windowClose = () =>{
			var seqArr = new Array();
			for( var i = 0; i < selected_id.length; i++ ) {
				seqArr.push($('#seq_'+selected_id[i]).val());
			}
			$('#selected_seq_id').val('').val( seqArr.join(',') );
			parent.emailwindow.hide();
		}
	</script>
	</head>
	    <body>
		<?
		$sql_users = sql_select( "select USER_NAME,ID from user_passwd where valid=1 order by user_name ASC");
		?>
			<div align="center" style="width:100%;">
				<input type="hidden" id="selected_print_report" name="selected_print_report" value=""/>
				<input type="hidden" id="selected_seq_id" name="selected_seq_id" value=""/>
				<table width="420" cellspacing="0" class="rpt_table" border="0" rules="all">
					<thead>
						<th align="center" width="50" >Sl No</th>
						<th  width="200">Print List</th>
						<th >Copy</th>
					</thead>
				</table>
				<div style="width:420px; max-height:220px; overflow-y:scroll;">
					<table cellspacing="0" width="420" class="rpt_table" border="0" rules="all" id="item_table2" align="left">
						<tbody>
						<?
						$i=1;
						$user_id_arr = array();
						$sql_arr = array(1=>'FRO Top Sheet',2=>'Bill of Exchange',3=>'COMMERCIAL INVOICE',4=>'PACKING LIST',5=>'DELIVERY CHALLAN / TRUCK RECEIPT',6=>'CERTIFICATE OF ORIGIN',7=>'Bill  of Exchange (Pad Print)',8=>'COMMERCIAL INVOICE (Pad Print)',9=>'PACKING LIST ( Pad Print)',10=>'DELIVERY CHALLAN / TRUCK RECEIPT (Pad Print)');
						foreach($sql_arr as $key=>$row)
						{
							$bgcolor=($i%2==0)?"#E9F3FF":"#FFFFFF";
							$user_id_arr[$key] = $key;
							?>
							<tr bgcolor="<?= $bgcolor;?>" onClick="js_set_value(<?= $key;?>)" id="tr_<?= $key;?>">
								<td align="center" width="50"><?=$i ?></td>
								<td width="200"><?= $row; ?></td>
								<td > <input width="40px" type="number" class="text_boxes_numeric" value="1"  id="seq_<?=$key;?>"> </td>
							</tr>
						<?
						$i++;
						}
						?>
						</tbody>
					</table>
				</div><br>
				<table cellspacing="0" cellpadding="0" style="border:none" align="center">
					<tr>
						<td valign="bottom">
							<div style="width:100%">
								<div style="width:55%; float:left" align="left">
									<input type="checkbox" name="check_all" id="check_all" onClick="check_all_data('<?= implode(',',$user_id_arr);?>')"/> Check / Uncheck All
								</div>
								<div style="width:17%; float:left" align="left">
									<input type="button" name="close" onClick="windowClose();" class="formbutton" value="Close" style="width:100px"/>
								</div>
							</div>
						</td>
					</tr>
				</table>
			</div>
        </body>
	<script>
	setFilterGrid('item_table2',-1);
	if(userId !=''){
		check_all_data(userId);
	}
	</script>
	</html>
	<?
}

if($action=="lcSc_popup_search")
{
	echo load_html_head_contents("Export Information Entry Form", "../../../", 1, 1,'','1','');
	extract($_REQUEST);
	?>

		<script>

			function js_set_value(data)
			{
				var data=data.split("_");
				if (data[6]==1 && (data[7]!=1 && data[7]!=3))
				{
					alert("Approval Necessity Setup Yes. Please Approved First.");
					return;
				}

				$('#hidden_lcSc_id').val(data[0]);
				$('#is_lcSc').val(data[1]);
				$('#company_id').val(data[2]);

				if(data[3]=="") { data[3]=0; }
				$('#import_btb').val(data[3]);
				$('#export_item_category').val(data[4]);
				$('#lc_for').val(data[5]);
				parent.emailwindow.hide();
			}

		</script>

	</head>

	<body>
		<div align="center" style="width:1020px;">
			<form name="searchexportinformationfrm"  id="searchexportinformationfrm">
				<fieldset style="width:1020px;">
				<legend>Enter search words</legend>
					<table cellpadding="0" cellspacing="0" width="1020" class="rpt_table" border="1" rules="all">
						<thead>
							<th class="must_entry_caption">Company</th>
							<th>Buyer</th>
							<th>Year</th>
                            <th>LC For</th>
							<th>Search By</th>
							<th>Enter</th>
                            <th>Order No</th>
							<th>
								<input type="reset" name="reset" id="reset" value="Reset" style="width:100px" class="formbutton" />
								<input type="hidden" name="hidden_lcSc_id" id="hidden_lcSc_id" value="" />
								<input type="hidden" name="is_lcSc" id="is_lcSc" value="" />
								<input type="hidden" name="company_id" id="company_id" value="" />
								<input type="hidden" name="import_btb" id="import_btb" value="" />
                                <input type="hidden" name="export_item_category" id="export_item_category" value="" />
                                <input type="hidden" name="lc_for" id="lc_for" value="" />
							</th>
						</thead>
						<tr class="general">
							<td>
								<?
									echo create_drop_down( "cbo_company_name", 170, "select comp.id, comp.company_name from lib_company comp where comp.status_active=1 and comp.is_deleted=0 $company_cond order by comp.company_name","id,company_name", 1, "--- Select Company ---", 0, "load_drop_down( 'export_information_entry_controller', this.value, 'load_drop_down_buyer', 'buyer_td_id' );" );
								?>
							</td>
							<td id="buyer_td_id">
								<?
									echo create_drop_down("cbo_buyer_name", 162, "SELECT buy.id as id ,buy.buyer_name as buyer_name  from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id $buyer_cond and buy.id in (select  buyer_id from  lib_buyer_party_type where party_type in (1,3,21,90)) group by buy.id,buy.buyer_name order by buyer_name", "id,buyer_name", 1, "--- Select Buyer ---", $selected, "");
								?>
							</td>
							<td align="center">
								<?
								    echo create_drop_down( "cbo_year", 80, create_year_array(),"", 1,"-- All --", date("Y",time()), "",0,"" ); 
                                ?> 
                          	</td>
                            <td>
								<? echo create_drop_down( "cbo_lc_for", 100, $lc_for_arr,"", 0, "", 1, "" ); ?>
							</td>
							<td>
								<?
									$arr=array(1=>'LC NO',2=>'SC No',3=>'File No');
									echo create_drop_down( "cbo_search_by", 100, $arr,"", 0, "", 0, "" );
								?>
							</td>
							<td>
								<input type="text" style="width:130px" class="text_boxes"  name="txt_search_common" id="txt_search_common" />
							</td>
                            <td>
								<input type="text" style="width:120px" class="text_boxes"  name="txt_order_no" id="txt_order_no" />
							</td>
							<td>
								<input type="button" id="search_button" class="formbutton" value="Show" onClick="show_list_view ( document.getElementById('cbo_company_name').value+'**'+document.getElementById('cbo_buyer_name').value+'**'+document.getElementById('cbo_search_by').value+'**'+document.getElementById('txt_search_common').value+'**'+document.getElementById('txt_order_no').value+'**'+document.getElementById('cbo_lc_for').value+'**'+document.getElementById('cbo_year').value, 'lcSc_search_list_view', 'search_div', 'export_information_entry_controller', 'setFilterGrid(\'list_view\',-1)')" style="width:100px;" />
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

if($action=="lcSc_search_list_view")
{
	$data=explode('**',$data);

	$company_id=$data[0];
	if ($company_id==0) {echo "Plz Select Company"; die;}
	$txt_order_no=trim(str_replace("'","",$data[4]));
	$cbo_lc_for=trim(str_replace("'","",$data[5]));
	$cbo_year=trim(str_replace("'","",$data[6]));
	$order_ids=array();
	if($txt_order_no)
	{
		$sql_order=sql_select("select id from wo_po_break_down where po_number='$txt_order_no' and is_deleted=0");
		foreach($sql_order as $row)
		{
			$order_ids[$row[csf("id")]]=$row[csf("id")];
		}
	}
	if($data[1]==0)
	{
		if ($_SESSION['logic_erp']["data_level_secured"]==1)
		{
			if($_SESSION['logic_erp']["buyer_id"]!="") $buyer_id_cond=" and buyer_name in (".$_SESSION['logic_erp']["buyer_id"].")"; else $buyer_id_cond="";
		}
		else
		{
			$buyer_id_cond="";
		}
	}
	else
	{
		$buyer_id_cond=" and buyer_name=$data[1]";
	}

	//if($data[1]==0) $buyer_id="%%"; else $buyer_id=$data[1];
	$search_by=$data[2];
	if($search_by==1){
		if($data[3]!='') $search_text="and a.export_lc_no like '%".trim($data[3])."%'"; else $search_text=" ";
	}
	else if($search_by==2)
	{
		if($data[3]!='') $search_text="and a.contract_no like '%".trim($data[3])."%'"; else $search_text=" ";
	}
	else if($search_by==3)
	{
		if($data[3]!='') $search_text="and a.internal_file_no like '%".trim($data[3])."%'"; else $search_text=" ";
	}

	if($cbo_year>0){$year_con = "and to_char(a.insert_date,'YYYY') = $cbo_year"; } else $year_con=" ";

	if($db_type==0) $year_field="YEAR(a.insert_date) as year,";
	else if($db_type==2) $year_field="to_char(a.insert_date,'YYYY') as year,";
	else $year_field="";//defined Later

	if($company_id !=0 ) $company_cond ="and a.beneficiary_name=$company_id"; else $company_cond =" ";
	$buyer_arr=return_library_array( "select id, short_name from lib_buyer",'id','short_name');
	$comp=return_library_array( "select id, company_short_name from lib_company",'id','company_short_name');
	$bank_arr=return_library_array( "select id, bank_name from lib_bank",'id','bank_name');
	$po_id_cond="";

	if ($db_type==2) $app_nes_setup_date=change_date_format(date('d-m-Y'), "", "",1);
	else if ($db_type==0) $app_nes_setup_date=change_date_format(date('d-m-Y'),'yyyy-mm-dd');
	
	if($search_by==1)
	{
		if(count($order_ids)>0)
		{
			$po_id_cond=" and b.wo_po_break_down_id in(".implode(",",$order_ids).")";
			$sql = "select a.id as id, a.beneficiary_name, $year_field a.export_lc_prefix_number as system_num,a. export_lc_system_id as system_id, a.export_lc_no as lc_sc, a.internal_file_no, a.beneficiary_name, a.buyer_name, a.lien_bank, a.export_item_category, 1 as type, a.import_btb,a.approved  
			from com_export_lc a join com_export_lc_order_info b on a.id=b.com_export_lc_id and b.status_active=1 $po_id_cond
			where a.status_active=1 and a.is_deleted=0 $company_cond $search_text $buyer_id_cond $year_con
			group by a.id, a.beneficiary_name, a.insert_date, a.export_lc_prefix_number,a. export_lc_system_id, a.export_lc_no, a.internal_file_no, a.beneficiary_name, a.buyer_name, a.lien_bank, a.export_item_category, a.import_btb, a.approved 
			order by id desc";
		}
		else
		{
			$sql = "select a.id, a.beneficiary_name, $year_field a.export_lc_prefix_number as system_num, a.export_lc_system_id as system_id, a.export_lc_no as lc_sc, a.internal_file_no, a.beneficiary_name, a.buyer_name, a.lien_bank, a.export_item_category, 1 as type, a.import_btb, a.approved  
			from com_export_lc a where a.status_active=1 and a.is_deleted=0 $company_cond $search_text $buyer_id_cond $year_con order by id";
		}
		
		

		$lc_sc="LC No";

		foreach (sql_select($sql) as $value)
		{
			if($value[csf("import_btb")] == 1){
				$import_btb_buyer[$value[csf("id")]]=$comp[$value[csf("buyer_name")]];
			}else{
				$import_btb_buyer[$value[csf("id")]]=$buyer_arr[$value[csf("buyer_name")]];
			}

		}
		$id_buyer = "id";
		//$arr=array (4=>$comp,5=>$import_btb_buyer,6=>$bank_arr);

		// Approval Necessity Setup part		
		$lc_approval_status="select approval_need, allow_partial from approval_setup_dtls where mst_id=(select id from approval_setup_mst where company_id='$company_id' and setup_date=(select max(setup_date) from approval_setup_mst where setup_date <= '$app_nes_setup_date' and company_id='$company_id')) and page_id=46 and status_active=1 and is_deleted=0";
		$lc_app_need_setup=sql_select($lc_approval_status);
		$lc_approval_need=$lc_app_need_setup[0][csf("approval_need")];
	}
	else if($search_by==2)
	{
		$lc_for_cond="";
		if($cbo_lc_for) $lc_for_cond=" and a.lc_for=$cbo_lc_for";
		if(count($order_ids)>0)
		{
			$po_id_cond=" and b.wo_po_break_down_id in(".implode(",",$order_ids).")";
			$sql = "select a.id as id, a.beneficiary_name, $year_field a.contact_prefix_number as system_num, a.contact_system_id as system_id, a.contract_no as lc_sc, a.internal_file_no, a.beneficiary_name, a.buyer_name, a.lien_bank, 0 as export_item_category, 2 as type, 0 as import_btb, a.lc_for, a.approved  
			from com_sales_contract a join com_sales_contract_order_info b on a.id=b.com_sales_contract_id and b.status_active=1 $po_id_cond
			where a.convertible_to_lc<>1 and a.status_active=1 and a.is_deleted=0 $company_cond $search_text $buyer_id_cond $lc_for_cond $year_con
			group by a.id, a.beneficiary_name, a.insert_date, a.contact_prefix_number, a.contact_system_id, a.contract_no, a.internal_file_no, a.beneficiary_name, a.buyer_name, a.lien_bank, a.lc_for, a.approved 
			order by id desc";
		}
		else
		{
			$sql = "select a.id, a.beneficiary_name, $year_field a.contact_prefix_number as system_num, a.contact_system_id as system_id, a.contract_no as lc_sc, a.internal_file_no, a.beneficiary_name, a.buyer_name, a.lien_bank, 0 as export_item_category, 2 as type, 0 as import_btb, a.lc_for, a.approved 
			from com_sales_contract a where a.convertible_to_lc<>1 and a.status_active=1 and a.is_deleted=0 $company_cond $search_text $buyer_id_cond $lc_for_cond $year_con order by id";
		}
		
		//echo $sql;
		$id_buyer = "buyer_name";
		$lc_sc="SC No";
		//$arr=array (4=>$comp,5=>$buyer_arr,6=>$bank_arr,7=>$lc_for_arr);

		$sc_approval_status="select approval_need, allow_partial from approval_setup_dtls where mst_id=(select id from approval_setup_mst where company_id='$company_id' and setup_date=(select max(setup_date) from approval_setup_mst where setup_date <= '$app_nes_setup_date' and company_id='$company_id')) and page_id=47 and status_active=1 and is_deleted=0";
    	$sc_app_need_setup=sql_select($sc_approval_status);
    	$sc_approval_need=$sc_app_need_setup[0][csf("approval_need")];
	}
	else if($search_by==3)
	{
		$lc_for_cond="";
		if($cbo_lc_for) $lc_for_cond=" and a.lc_for=$cbo_lc_for";
		if(count($order_ids)>0)
		{
			$po_id_cond=" and b.wo_po_break_down_id in(".implode(",",$order_ids).")";
			$sql = "SELECT a.id as id, a.beneficiary_name, $year_field a.export_lc_prefix_number as system_num,a. export_lc_system_id as system_id, a.export_lc_no as lc_sc, a.internal_file_no, a.beneficiary_name, a.buyer_name, a.lien_bank, a.export_item_category, 1 as type, a.import_btb, 0 as lc_for,a.approved  
			from com_export_lc a join com_export_lc_order_info b on a.id=b.com_export_lc_id and b.status_active=1 $po_id_cond
			where a.status_active=1 and a.is_deleted=0 $company_cond $search_text $buyer_id_cond $year_con
			group by a.id, a.beneficiary_name, a.insert_date, a.export_lc_prefix_number,a. export_lc_system_id, a.export_lc_no, a.internal_file_no, a.beneficiary_name, a.buyer_name, a.lien_bank, a.export_item_category, a.import_btb, a.approved 
			union all
			SELECT a.id as id, a.beneficiary_name, $year_field a.contact_prefix_number as system_num, a.contact_system_id as system_id, a.contract_no as lc_sc, a.internal_file_no, a.beneficiary_name, a.buyer_name, a.lien_bank, 0 as export_item_category, 2 as type, 0 as import_btb, a.lc_for, a.approved  
			from com_sales_contract a join com_sales_contract_order_info b on a.id=b.com_sales_contract_id and b.status_active=1 $po_id_cond
			where a.convertible_to_lc<>1 and a.status_active=1 and a.is_deleted=0 $company_cond $search_text $buyer_id_cond $year_con $lc_for_cond
			group by a.id, a.beneficiary_name, a.insert_date, a.contact_prefix_number, a.contact_system_id, a.contract_no, a.internal_file_no, a.beneficiary_name, a.buyer_name, a.lien_bank, a.lc_for, a.approved 
			order by id desc";
		}
		else
		{
			$sql = "SELECT a.id, a.beneficiary_name, $year_field a.export_lc_prefix_number as system_num, a.export_lc_system_id as system_id, a.export_lc_no as lc_sc, a.internal_file_no, a.beneficiary_name, a.buyer_name, a.lien_bank, a.export_item_category, 1 as type, a.import_btb, 0 as lc_for, a.approved  
			from com_export_lc a where a.status_active=1 and a.is_deleted=0 $company_cond $search_text $buyer_id_cond $year_con
			union all
			SELECT a.id, a.beneficiary_name, $year_field a.contact_prefix_number as system_num, a.contact_system_id as system_id, a.contract_no as lc_sc, a.internal_file_no, a.beneficiary_name, a.buyer_name, a.lien_bank, 0 as export_item_category, 2 as type, 0 as import_btb, a.lc_for, a.approved 
			from com_sales_contract a where a.convertible_to_lc<>1 and a.status_active=1 and a.is_deleted=0 $company_cond $search_text $buyer_id_cond $year_con $lc_for_cond order by id desc";
		}
		
		//echo $sql;
		foreach (sql_select($sql) as $value)
		{
			if($value["TYPE"]==1)
			{
				if($value[csf("import_btb")] == 1){
					$import_btb_buyer[$value[csf("id")]]=$comp[$value[csf("buyer_name")]];
				}else{
					$import_btb_buyer[$value[csf("id")]]=$buyer_arr[$value[csf("buyer_name")]];
				}
			}
		}
		$lc_sc="LC/SC No";

		$lc_approval_status="select approval_need, allow_partial from approval_setup_dtls where mst_id=(select id from approval_setup_mst where company_id='$company_id' and setup_date=(select max(setup_date) from approval_setup_mst where setup_date <= '$app_nes_setup_date' and company_id='$company_id')) and page_id=46 and status_active=1 and is_deleted=0";
		$lc_app_need_setup=sql_select($lc_approval_status);
		$lc_approval_need=$lc_app_need_setup[0][csf("approval_need")];

		$sc_approval_status="select approval_need, allow_partial from approval_setup_dtls where mst_id=(select id from approval_setup_mst where company_id='$company_id' and setup_date=(select max(setup_date) from approval_setup_mst where setup_date <= '$app_nes_setup_date' and company_id='$company_id')) and page_id=47 and status_active=1 and is_deleted=0";
    	$sc_app_need_setup=sql_select($sc_approval_status);
    	$sc_approval_need=$sc_app_need_setup[0][csf("approval_need")];
	}
	//echo $sql;

	//echo $approval_need.'system';

		

	$table_width=980;
	?>
	<table cellspacing="0" cellpadding="0" border="1" rules="all" width="<?= $table_width; ?>" class="rpt_table" >
        <thead>
            <th width="40">SL</th>
            <th width="60">Year</th>
            <th width="60">System ID</th>
            <th width="130">File No</th>
            <th width="140"><?= $lc_sc; ?></th>
            <th width="120">Benificiary</th>
			<th width="120">Buyer</th>
			<th width="130">Lien Bank</th>
            <th>LC For</th>
        </thead>
    </table>
    <div style="width:<?= $table_width+20; ?>px; overflow-y:scroll; max-height:280px;" id="buyer_list_view" align="center">
        <table cellspacing="0" cellpadding="0" border="1" rules="all" width="<?= $table_width; ?>" class="rpt_table" id="tbl_list_search" >
        <?
            $sql_result=sql_select($sql);
			$i=1; 
            foreach($sql_result as $row)
            {
				if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
				if($row['TYPE']==1){
					$buyer_name=$import_btb_buyer[$row[csf("id")]];
					$approval_need=$lc_approval_need;
				} 
				else{ 
					$buyer_name=$buyer_arr[$row[csf('buyer_name')]];
					$approval_need=$sc_approval_need;
				}
				?>
				<tr bgcolor="<? echo $bgcolor; ?>" style="text-decoration:none; cursor:pointer" id="tr_<? echo $i;?>" onClick="js_set_value('<? echo $row[csf('id')]."_".$row[csf('type')]."_".$row[csf('beneficiary_name')]."_".$row[csf('import_btb')]."_".$row[csf('export_item_category')]."_".$row[csf('lc_for')]."_".$approval_need."_".$row[csf('approved')];?>')">
					<td width="40" align="center"><? echo $i; ?></td>	
					<td width="60"><p><? echo $row[csf('year')]; ?></p></td>
					<td width="60"><p><? echo $row[csf('system_num')]; ?></p></td>
					<td width="130"><p><? echo $row[csf('internal_file_no')]; ?></p></td>
					<td width="140"><p><? echo $row[csf('lc_sc')]; ?></p></td>
					<td width="120"><p><? echo $comp[$row[csf('beneficiary_name')]]; ?></p></td>
					<td width="120"><p><? echo $buyer_name; ?></p></td>
					<td width="130"><p><? echo $bank_arr[$row[csf('lien_bank')]]; ?></p></td>
					<td ><p><? echo $lc_for_arr[$row[csf('lc_for')]]; ?></p></td>
				</tr>
				<?
				$i++;
            }
        ?>
        </table>		
    </div>
	<?
	//echo  create_list_view("list_view", "Year,System ID,File No,$lc_sc,Benificiary,Buyer,Lien Bank,LC For", "60,60,100,140,120,120,120","920","280",0, $sql, "js_set_value", "id,type,beneficiary_name,import_btb,export_item_category,lc_for", "", 1, "0,0,0,0,beneficiary_name,$id_buyer,lien_bank,lc_for", $arr , "year,system_num,internal_file_no,lc_sc,beneficiary_name,$id_buyer,lien_bank,lc_for", "",'','0,0,0,0,0,0,0,0');
	exit();
}

if($action=="populate_data_from_lcSc")
{
	$explode_data = explode("**",$data);
	$lcSc_id=$explode_data[0];
	$is_lcSc=$explode_data[1];
	$invoice_id=$explode_data[2];
	$company_id=str_replace("'","",$explode_data[3]);
	$import_btb=$explode_data[4];
	$export_item_category=$explode_data[5];
	$load_all_po=$explode_data[6];
	$shipment_check=$explode_data[7];
	
	// echo $load_all_po.'**'.$export_item_category; //die;
	
	$variable_setting_ac_po=return_field_value("cm_cost_method","variable_order_tracking","company_name='".$company_id."' and  variable_list=93","cm_cost_method");

	$variable_setting=return_field_value("cost_heads_status","variable_settings_commercial","company_name='".$company_id."' and  variable_list=18","cost_heads_status");
	
	$variable_setting_commission=return_field_value("export_invoice_qty_source","variable_settings_commercial","company_name='".$company_id."' and  variable_list=26","export_invoice_qty_source");
	
	//echo $variable_setting.'system';
	$disable_rate=" ";
	if($variable_setting==1) $disable_rate='disabled';
	$invoiceQtySource=1;
	$invoiceCommissionQtySource=1;
	$disabled="";
	if($invoice_id==0 || $invoice_id=="")
	{
		$invoiceQtySource=return_field_value("export_invoice_qty_source","variable_settings_commercial","company_name='".$company_id."' and  variable_list=22","export_invoice_qty_source");
		$invoiceCommissionQtySource=return_field_value("export_invoice_qty_source","variable_settings_commercial","company_name='".$company_id."' and  variable_list=26","export_invoice_qty_source");
		
	}else{
		$invoiceQtySource=return_field_value("export_invoice_qty_source","com_export_invoice_ship_mst","id='".$invoice_id."'","export_invoice_qty_source");
		$invoiceCommissionQtySource=return_field_value("commission_source_export","com_export_invoice_ship_mst","id='".$invoice_id."'","commission_source_export");
	}

	//echo $company_id; die;

	if($invoiceQtySource==2 || $invoiceQtySource==3){
		$disabled='disabled';
	}else{
		$disabled="";
	}
	
	echo "document.getElementById('export_invoice_qty_source').value			= '".$invoiceQtySource."';\n";
	echo "document.getElementById('commission_source_at_export_invoice').value			= '".$invoiceCommissionQtySource."';\n";
	
	$po_invoice_data_array=array();
	if($load_all_po==1) $invoice_qnty_cond=""; else $invoice_qnty_cond=" and a.current_invoice_qnty>0";
	$invoice_sql="SELECT a.po_breakdown_id as PO_BREAKDOWN_ID, sum(a.current_invoice_qnty) as CURRENT_INVOICE_QNTY, sum(a.current_invoice_value) as CURRENT_INVOICE_VALUE 
	FROM com_export_invoice_ship_dtls a, com_export_invoice_ship_mst b 
	where a.mst_id=b.id and b.is_lc='$is_lcSc' and b.lc_sc_id='$lcSc_id' and a.status_active=1 and a.is_deleted=0 $invoice_qnty_cond 
	group by a.po_breakdown_id";
	
	
	//echo $invoice_sql;die;
	$data_array_invoice=sql_select($invoice_sql);
	foreach($data_array_invoice as $row)
	{
		$po_invoice_data_array[$row['PO_BREAKDOWN_ID']]['qnty']=$row['CURRENT_INVOICE_QNTY'];
		$po_invoice_data_array[$row['PO_BREAKDOWN_ID']]['val']=$row['CURRENT_INVOICE_VALUE'];
	}
	
	/*if($invoiceCommissionQtySource==2)
	{
		echo "$('#txt_commission').attr('disabled','true')".";\n";
		echo "$('#txt_commission_amt').attr('disabled','true')".";\n";
	}
	else
	{
		echo "$('#txt_commission').remove('disabled','disabled')".";\n";
		echo "$('#txt_commission_amt').remove('disabled','disabled')".";\n";
	}*/
	//echo $invoiceCommissionQtySource;die;
	if($is_lcSc== '1')
	{
		$pre_cost_data_array=array();
		$pre_cost_sql="SELECT a.id as PO_BREAKDOWN_ID, a.job_no_mst as JOB_NO_MST, a.po_number as PO_NUMBER, d.costing_per as COSTING_PER, c.commis_amount as COMMIS_AMOUNT, d.job_id as JOB_ID, a.UNIT_PRICE, a.PO_QUANTITY
		FROM com_export_lc_order_info b, wo_po_break_down a, wo_pre_cost_sum_dtls c,wo_pre_cost_mst d 
		where b.wo_po_break_down_id=a.id and a.job_id=c.job_id and a.job_id=d.job_id and b.com_export_lc_id=$lcSc_id and b.status_active=1 and b.is_deleted=0 and a.status_active=1 and a.is_deleted=0 and b.is_sales=0";
		
		$data_array_pre_cost=sql_select($pre_cost_sql);
		$po_info=array();
		foreach($data_array_pre_cost as $row)
		{
			$cbo_costing_per=$row['COSTING_PER'];
			if($cbo_costing_per==1) $costing_per=12;
			else if($cbo_costing_per==2) $costing_per=1;
			else if($cbo_costing_per==3) $costing_per=24;
			else if($cbo_costing_per==4) $costing_per=36;
			else $costing_per=48;
			$pre_cost_data_array[$row['PO_BREAKDOWN_ID']]['commis_amount']=$row['COMMIS_AMOUNT']/$costing_per;
			
			$po_info[$row['PO_BREAKDOWN_ID']]['po_rate']=$row['UNIT_PRICE'];
			$po_info[$row['PO_BREAKDOWN_ID']]['po_quantity']=$row['PO_QUANTITY'];
		}
	}
	else
	{
		$pre_cost_data_array=array();
		$pre_cost_sql="SELECT a.id as PO_BREAKDOWN_ID, a.job_no_mst as JOB_NO_MST, a.po_number as PO_NUMBER, d.costing_per as COSTING_PER, c.commis_amount as COMMIS_AMOUNT, d.job_id as JOB_ID, a.UNIT_PRICE, a.PO_QUANTITY
		FROM com_sales_contract_order_info b, wo_po_break_down a, wo_pre_cost_sum_dtls c,wo_pre_cost_mst d 
		where b.wo_po_break_down_id=a.id and a.job_id=c.job_id and a.job_id=d.job_id and b.com_export_lc_id=$lcSc_id and b.status_active=1 and b.is_deleted=0 and a.status_active=1 and a.is_deleted=0 and b.is_sales=0";
		
		$data_array_pre_cost=sql_select($pre_cost_sql);
		$po_info=array();
		foreach($data_array_pre_cost as $row)
		{
			$cbo_costing_per=$row['COSTING_PER'];
			if($cbo_costing_per==1) $costing_per=12;
			else if($cbo_costing_per==2) $costing_per=1;
			else if($cbo_costing_per==3) $costing_per=24;
			else if($cbo_costing_per==4) $costing_per=36;
			else $costing_per=48;
			$pre_cost_data_array[$row['PO_BREAKDOWN_ID']]['commis_amount']=$row['COMMIS_AMOUNT']/$costing_per;
			
			$po_info[$row['PO_BREAKDOWN_ID']]['po_rate']=$row['UNIT_PRICE'];
			$po_info[$row['PO_BREAKDOWN_ID']]['po_quantity']=$row['PO_QUANTITY'];
		}
	}
	
	
	//echo "<pre>";
	//print_r($pre_cost_data_array);
	//echo "10**".$import_btb; 
	$brand_arr=return_library_array( "select id, brand_name from lib_buyer_brand brand",'id','brand_name');
	if($import_btb==1)
	{
		$color_array = return_library_array("select id, color_name from lib_color","id","color_name");
		$item_group_arr=return_library_array( "select id, item_name from lib_item_group", "id", "item_name"  );
		$size_library=return_library_array( "select id,size_name from lib_size where status_active=1 and is_deleted=0", "id", "size_name"  );

		if($load_all_po==1){
			$leftJoinCond=" left join com_export_invoice_ship_dtls c on c.po_breakdown_id=b.pi_dtls_id and c.mst_id=$invoice_id and c.status_active=1 and c.is_deleted=0 ";
			$extWhereCond=$extJoinCond="";
		}else{
			$leftJoinCond="";
			$extJoinCond=", com_export_invoice_ship_dtls c ";
			$extWhereCond=" and c.po_breakdown_id=b.pi_dtls_id and c.mst_id=$invoice_id and c.status_active=1 and c.is_deleted=0 and c.current_invoice_qnty>0";
		}

		if($is_lcSc== '1')
		{
			$sql= "SELECT id, export_lc_no as lc_sc, lien_bank, tolerance, applicant_name, beneficiary_name, buyer_name, shipping_mode, inco_term, inco_term_place, port_of_entry, port_of_loading, port_of_discharge, internal_file_no FROM com_export_lc where id= $lcSc_id";

			if($invoice_id==0 || $invoice_id=="")
			{
				$order_sql="SELECT work_order_no as po_number, pi_dtls_id as order_id, color_id, construction, composition, gsm, dia_width, attached_qnty, attached_rate, attached_value, uom, is_sales as type, item_group_id, size_id, aop_color, body_part_id, gmts_item_id, embell_name, embell_type, fabric_description, is_service as IS_SERVICE, buyer_style_ref as style_ref_no,booking_no
				FROM com_export_lc_order_info where com_export_lc_id=$lcSc_id and status_active=1 and is_deleted=0";
			}
			else
			{
				$order_sql="SELECT b.work_order_no as po_number, b.pi_dtls_id as order_id, b.color_id, b.construction, b.composition, b.gsm, b.dia_width, b.attached_qnty, b.attached_rate, b.attached_value, b.uom, c.id as dtls_id, c.mst_id, c.current_invoice_rate as rate, c.current_invoice_qnty, c.current_invoice_value,carton_qty, c.production_source, b.is_sales as type, b.item_group_id, b.size_id, b.aop_color, b.body_part_id, b.gmts_item_id, b.embell_name, b.embell_type, b.fabric_description, b.is_service as IS_SERVICE, b.buyer_style_ref as style_ref_no, b.booking_no
				FROM com_export_lc_order_info b $leftJoinCond $extJoinCond where b.com_export_lc_id=$lcSc_id and b.status_active=1 and b.is_deleted=0 $extWhereCond";
			}
		}
		else if($is_lcSc=='2')
		{
			$sql= "SELECT id, contract_no as lc_sc ,tolerance, lien_bank, applicant_name, beneficiary_name, buyer_name, shipping_mode, inco_term, inco_term_place, port_of_entry, port_of_loading, port_of_discharge, internal_file_no FROM com_sales_contract where id=$lcSc_id";

			if($invoice_id==0 || $invoice_id=="")
			{
				$order_sql="SELECT work_order_no as po_number, pi_dtls_id as order_id, color_id, construction, composition, gsm, dia_width, attached_qnty, attached_rate, attached_value, 0 as uom, is_sales as type, 0 as item_group_id, 0 as size_id, 0 as aop_color, 0 as body_part_id, 0 as gmts_item_id, 0 as embell_name, 0 as embell_type, fabric_description, 0 as IS_SERVICE  
				FROM com_sales_contract_order_info where com_export_lc_id=$lcSc_id and status_active=1 and is_deleted=0";
			}
			else
			{
				$order_sql="SELECT b.work_order_no as po_number, b.pi_dtls_id as order_id, b.color_id, b.construction, b.composition, b.gsm, b.dia_width, b.attached_qnty, b.attached_rate, b.attached_value, 0 as uom, c.id as dtls_id, c.mst_id, c.current_invoice_rate as rate, c.current_invoice_qnty, c.current_invoice_value,c.carton_qty, c.production_source, b.is_sales as type, 0 as item_group_id, 0 as size_id, 0 as aop_color, 0 as body_part_id, 0 as gmts_item_id, 0 as embell_name, 0 as embell_type, b.fabric_description, 0 as IS_SERVICE  
				FROM com_sales_contract_order_info b $leftJoinCond $extJoinCond  where b.com_export_lc_id=$lcSc_id and b.status_active=1 and b.is_deleted=0 $extWhereCond";
			}
		}
		//echo $sql;die;
		// echo $order_sql;die;
		$data_array=sql_select($sql);
		$data_array_po_attached=sql_select($order_sql);

		foreach ($data_array as $row)
		{
			$row_number = count($data_array_po_attached);

			echo "document.getElementById('cbo_beneficiary_name').value			= '".$row[csf("beneficiary_name")]."';\n";
			echo "document.getElementById('cbo_buyer_name').value 				= ".$row[csf("buyer_name")].";\n";
			echo "document.getElementById('cbo_lien_bank').value 				= '".$row[csf("lien_bank")]."';\n";
			echo "document.getElementById('cbo_applicant_name').value 			= '".$row[csf("applicant_name")]."';\n";
			echo "document.getElementById('internal_file_no').value 			= '".$row[csf("internal_file_no")]."';\n";
			echo "document.getElementById('tot_row').value 						= '".$row_number."';\n";

			if($invoice_id==0)
			{
				echo "document.getElementById('txt_lc_sc_no').value 				= '".$row[csf("lc_sc")]."';\n";
				echo "document.getElementById('lc_sc_id').value 					= '".$row[csf("id")]."';\n";
				echo "document.getElementById('import_btb').value 					= '".$import_btb."';\n";
				echo "document.getElementById('is_lc_sc').value 					= '".$is_lcSc."';\n";
				echo "document.getElementById('inco_term').value					= '".$row[csf("inco_term")]."';\n";
				echo "document.getElementById('inco_term_place').value 				= '".$row[csf("inco_term_place")]."';\n";
				echo "document.getElementById('shipping_mode').value 				= '".$row[csf("shipping_mode")]."';\n";
				echo "document.getElementById('port_of_entry').value 				= '".$row[csf("port_of_entry")]."';\n";
				echo "document.getElementById('port_of_loading').value 				= '".$row[csf("port_of_loading")]."';\n";
				echo "document.getElementById('port_of_discharge').value 			= '".$row[csf("port_of_discharge")]."';\n";

				echo "reset_form('','','txt_invoice_val*txt_discount*txt_discount_ammount*txt_bonus*txt_bonus_ammount*txt_claim*txt_claim_ammount*txt_invo_qnty*txt_commission*txt_commission_amt*txt_other_discount*txt_other_discount_amt*txt_upcharge*txt_net_invo_val');\n";
			}

			$table=""; $i=1;
			if($row_number==0)
			{
				$table=$table.'<tr class="general"><td colspan="13" align="center">Fabric Details is Not Available For This LC/SC </td></tr>';
				echo "$('#order_rate').removeAttr('disabled','disabled')".";\n";
				echo "$('#txt_invoice_val').removeAttr('disabled','disabled')".";\n";
				echo "$('#txt_invo_qnty').removeAttr('disabled','disabled')".";\n";
			}
			else
			{
				$total_attached_order_qnty='';
				foreach ($data_array_po_attached as $slectResult)
				{
					$tolerance_order_qty = $slectResult[csf('attached_qnty')]+($row[csf("tolerance")]/100*$slectResult[csf('attached_qnty')]);
					$total_tolerance_order_qty+=$tolerance_order_qty ;

					if($invoice_id==0)
					{
						$unit_price=$slectResult[csf('attached_rate')];
					}
					else
					{
						if($slectResult[csf('current_invoice_qnty')] > 0)
						{
							$unit_price=$slectResult[csf('rate')];
						}
						else
						{
							$unit_price=$slectResult[csf('attached_rate')];
						}
					}
					//echo $export_item_category.test;die;
					if($export_item_category==10)
					{
						$fabrication = $slectResult[csf('construction')]." ".$slectResult[csf('composition')];
						$gsm_dia = $slectResult[csf('gsm')]." & ".$slectResult[csf('dia_width')];
						$color=$color_array[$slectResult[csf('color_id')]];
					}
					else if($export_item_category==45)
					{
						$fabrication = $slectResult[csf('fabric_description')];
						$gsm_dia = $item_group_arr[$slectResult[csf('item_group_id')]];
						$color=$color_array[$slectResult[csf('color_id')]]." & ".$size_library[$slectResult[csf('size_id')]];
					}
					else if($export_item_category==23)
					{
						$fabrication = $body_part[$slectResult[csf('body_part_id')]];
						$gsm_dia = $slectResult[csf('gsm')]." & ".$color_array[$slectResult[csf('aop_color')]];
						$color=$color_array[$slectResult[csf('color_id')]];
					}
					else if($export_item_category==35 || $export_item_category==36)
					{
						if($export_item_category==36) $emb_type_arr=$emblishment_embroy_type; else $emb_type_arr=$emblishment_print_type;
						$fabrication = $garments_item[$slectResult[csf('gmts_item_id')]]." & ".$body_part[$slectResult[csf('body_part_id')]];
						$gsm_dia = $emblishment_name_array[$slectResult[csf('embell_name')]]." & ".$emb_type_arr[$slectResult[csf('embell_type')]];
						$color=$color_array[$slectResult[csf('color_id')]]." & ".$size_library[$slectResult[csf('size_id')]];
					}
					else if($export_item_category==37)
					{
						if($slectResult[csf('embell_name')]==1) $wash_process_type=$wash_wet_process;
						else if($slectResult[csf('embell_name')]==2) $wash_process_type=$wash_dry_process;
						else if($slectResult[csf('embell_name')]==3) $wash_process_type=$wash_laser_desing;
						$fabrication = $garments_item[$slectResult[csf('gmts_item_id')]]." & ".$slectResult[csf('fabric_description')];
						$gsm_dia = $wash_type[$slectResult[csf('embell_name')]]." & ".$wash_process_type[$slectResult[csf('embell_type')]];
						$color=$color_array[$slectResult[csf('color_id')]];
					}
					
					
					
					$act_po_infos='';

					$cumu_qty = $po_invoice_data_array[$slectResult[csf('order_id')]]['qnty'];
					$cumu_val = $po_invoice_data_array[$slectResult[csf('order_id')]]['val'];
					$pre_cost_amt=$pre_cost_data_array[$slectResult[csf('order_id')]]['commis_amount'];
					
					
					$prv_commission_amt=$pre_cost_amt*$slectResult[csf('current_invoice_qnty')];

					$po_balance_qnty=$slectResult[csf('attached_qnty')]-$cumu_qty;

					$total_cumu_qty+=$cumu_qty;
					$total_cumu_val+=$cumu_val;

					$total_value+=$slectResult[csf('current_invoice_value')];
					$total_qty+=$slectResult[csf('current_invoice_qnty')];

					if($slectResult[csf('current_invoice_qnty')]>0) $invc_qnty=$slectResult[csf('current_invoice_qnty')]; else $invc_qnty='';

					//$ex_factory_qnty=$exFactoryArr[$slectResult[csf('order_id')]]; ondblclick="pop_entry_actual_po('.$i.')" 

					/*$table=$table.'<tr align="center" id="tr_'.$i.'"><td width="115"><font style="display:none">'.$slectResult[csf('work_order_no')].'</font>\n<input type="hidden" id="order_id_'.$i.'" value="'.$slectResult[csf('order_id')].'"  /><input type="hidden" id="actual_po_infos_'.$i.'" value="'.$act_po_infos.'" /><input type="text" id="order_no_'.$i.'"  value="'.$slectResult[csf('work_order_no')].'" class="text_boxes" style="width:100px" readonly id="order_no_'.$i.'"  /></td><td width="100"><font style="display:none">'.$slectResult[csf('style_ref_no')].'</font>\n<input type="text" id="style_ref_no_'.$i.'"  value="'.$slectResult[csf('style_ref_no')].'" class="text_boxes" style="width:95px" disabled/></td><td width="80"><font style="display:none">'.$article_no.'</font>\n<input type="text" id="article_no_'.$i.'"  value="'.$fabrication.'" class="text_boxes" style="width:65px" disabled/></td><td width="70"><input type="text" id="shipment_date_'.$i.'" value="'.$gsm_dia.'" class="text_boxes" style="width:55px" disabled /></td><td width="80"><input type="hidden" disabled  id="tollerence_order_qty_'.$i.'" value="'.$tolerance_order_qty.'" /><input type="text" disabled id="order_qty_'.$i.'" value="'.$slectResult[csf('attached_qnty')].'" class="text_boxes_numeric" style="width:65px;"/></td><td width="70"><input type="text"  id="order_uom_'.$i.'" value="'.$unit_of_measurement[$slectResult[csf('uom')]].'" class="text_boxes" style="width:55px;" '.$readonly_status.' /></td><td width="70"><input type="text"  id="order_rate_'.$i.'" value="'.$unit_price.'" class="text_boxes_numeric" style="width:57px;" onKeyUp="calculate_value_rate('.$i.')" '.$readonly_status.' /></td><td width="80"><input type="text" id="curr_invo_qty_'.$i.'" class="text_boxes_numeric" style="width:65px" onKeyUp="calculate_value_rate('.$i.')" value="'.$invc_qnty.'" /><input type="hidden"  id="curr_hide_invo_qty_'.$i.'" value="'.$slectResult[csf('current_invoice_qnty')].'" /><input type="hidden" id="colorSize_infos_'.$i.'" value="'.$colorSize_infos.'" /></td><td width="95"><input name="text" type="text" id="curr_invo_val_'.$i.'" class="text_boxes_numeric" value="'.$slectResult[csf('current_invoice_value')].'" style="width:80px;" disabled /><input type="hidden" id="curr_hide_invo_val_'.$i.'" value="'.$slectResult[csf('current_invoice_value')].'" /></td><td width="80"><input type="text" id="cum_invo_qty_'.$i.'" value="'.$cumu_qty.'" disabled class="text_boxes_numeric" style="width:65px;background: #D0EFC2;"/><input type="hidden" id="hide_cum_invo_qty_'.$i.'" value="'.$cumu_qty.'" /></td><td width="80"><input type="text" id="po_bl_qty_'.$i.'" value="'.$po_balance_qnty.'" disabled class="text_boxes_numeric" style="width:65px;"/></td><td width="95"><input type="text" id="cum_invo_val_'.$i.'"  value="'.$cumu_val.'" disabled  class="text_boxes_numeric" style="width:80px;" /><input type="hidden" id="hide_cum_invo_val_'.$i.'" value="'.$cumu_val.'" /></td><td width="80"><input type="text" id="ex_factory_qty_'.$i.'" value="'.$ex_factory_qnty.'" disabled class="text_boxes_numeric" style="width:65px;"/></td><td width="105"><input type="text" title="'.$color.'" value="'.$color.'" disabled class="text_boxes" style="width:90px;"/></td><td>'.create_drop_down( "cbo_production_source_$i", 90, $knitting_source,'', 1, '', 0, '','1','1,3' ).'</td></tr>';
					<input type="text" id="order_no_'.$i.'"  value="'.$slectResult[csf('po_number')].'" class="text_boxes" style="width:100px" readonly  id="order_no_'.$i.'" />
					<input type="hidden" id="pre_cost_amt_'.$i.'"  value="'.$pre_cost_amt.'" class="text_boxes" style="width:100px" readonly  id="pre_cost_amt_'.$i.'" />
					<input type="hidden" id="hidden_commission_amt_'.$i.'"  value="'.$prv_commission_amt.'" class="text_boxes" style="width:100px" readonly  id="pre_cost_amt_'.$i.'" />
					//### ondblclick="pop_entry_actual_po('.$i.')" address in openpage_colorSize() function
					*/
					if($slectResult[csf('order_uom')]==58) $uom_color='style="color:#F60;"'; else $uom_color="";
					if($export_item_category==37)
					{
						$wash_job_no=$slectResult[csf('po_number')];
						$wo_po_no=$slectResult[csf('booking_no')];
					}
					else
					{
						$wash_job_no=$slectResult[csf('job_no_mst')];
						$wo_po_no=$slectResult[csf('po_number')];
					}
					$table=$table.'<tr align="center" id="tr_'.$i.'"><td width="80" id="td_job_no_'.$i.'" style="word-break:break-all">'.$wash_job_no.'</td><td width="90" id="td_order_no_'.$i.'" ondblclick="pop_entry_actual_po('.$i.')" title="'.$act_po_infos.'" orderid="'.$slectResult[csf('order_id')].'" ordertype="'.$slectResult[csf('type')].'" isservice="'.$slectResult['IS_SERVICE'].'" style="cursor:pointer; word-break:break-all"><span id="order_no_'.$i.'">'.$wo_po_no.'</span> <span id="pre_cost_amt_'.$i.'" style="display:none">'.$pre_cost_amt.'</span><input type="hidden" id="hidden_commission_amt_'.$i.'"  value="'.$prv_commission_amt.'" />\n</td><td width="105" id="td_style_ref_no_'.$i.'" style="word-break:break-all">'.$slectResult[csf('style_ref_no')].'</td><td width="80" id="td_article_no_'.$i.'" style="word-break:break-all">'.$fabrication.'</td><td width="60" id="td_shipment_date_'.$i.'"  align="center">'.$gsm_dia.'</td><td width="80" id="td_order_qty_'.$i.'"  title="'.$tolerance_order_qty.'" align="right">'.$slectResult[csf('attached_qnty')].'</td><td width="60" id="td_order_uom_'.$i.'" '.$uom_color.' title="'.$slectResult[csf('order_uom')].'">'.$unit_of_measurement[$slectResult[csf('uom')]].'</td><td width="70" id="td_order_rate_'.$i.'"><input type="text"  id="order_rate_'.$i.'" value="'.$unit_price.'" class="text_boxes_numeric" style="width:60px;" onKeyUp="calculate_value_rate('.$i.')" '.$disable_rate.' /></td><td width="80" id="td_curr_invo_qty_'.$i.'" title="'.$slectResult[csf('current_invoice_qnty')].'"><input type="text" id="curr_invo_qty_'.$i.'" class="text_boxes_numeric" style="width:70px" onKeyUp="calculate_value_rate('.$i.')" value="'.$invc_qnty.'" ondblclick="openpage_colorSize('.$i.')" '.$disabled.' /><input type="hidden" id="actual_po_infos_'.$i.'" value="'.$act_po_infos.'" /></td><td width="95" id="td_curr_invo_val_'.$i.'" title="'.$slectResult[csf('current_invoice_value')].'"><input name="text" type="text" id="curr_invo_val_'.$i.'" class="text_boxes_numeric" value="'.$slectResult[csf('current_invoice_value')].'" style="width:85px;" disabled /></td><td width="65" id="td_cum_invo_qty_'.$i.'" title="'.$cumu_qty.'" align="right">'.$cumu_qty.'</td><td width="65" id="td_po_bl_qty_'.$i.'" align="right">'.$po_balance_qnty.'</td><td width="75" id="td_cum_invo_val_'.$i.'" title="'.$cumu_val.'" align="right">'.$cumu_val.'</td><td width="65" id="td_ex_factory_qty_'.$i.'" align="right">'.$ex_factory_qnty.'</td><td width="90" id="td_dealing_merchant_'.$i.'" title="'.$colorSize_infos.'" style="word-break:break-all">'.$dealing_merchant.'</td><td id="td_cbo_production_source_'.$i.'" title="'.$slectResult[csf('production_source')].'">'.$knitting_source[$slectResult[csf('production_source')]].'</td><td  width="50" ></td><td width="50" id="td_carton_qty_'.$i.'" ><input type="text"  id="carton_qty_'.$i.'" value="'.$slectResult[csf('carton_qty')].'" class="text_boxes_numeric" style="width:50px;" /></td></tr>';
					$i++;
					$total_attached_order_qnty+=$slectResult[csf('attached_qnty')];
				}

				$table=$table.'<tr class="tbl_bottom"><td colspan="5">Total</td><td><input type="text" disabled  id="total_attached_order_qnty" value="'.$total_attached_order_qnty.'" class="text_boxes_numeric" style="width:65px;"/></td>><td></td><td><input type="hidden" id="total_tolerence_order_qty" value="'.$total_tolerance_order_qty.'" /><input type="hidden" id="hiddien_total_commission_amt" value="" /></td><td><input type="text" disabled id="total_current_invoice_qty" value="'.$total_qty.'" disabled class="text_boxes_numeric" style="width:65px;" /></td><td><input type="text" disabled  id="total_current_invoice_val" value="'.$total_value.'" disabled class="text_boxes_numeric" style="width:80px;" /></td><td colspan="7"></td></tr>';	
				if ($variable_setting==1) echo "$('#order_rate').attr('disabled','disabled')".";\n";
				else echo "$('#order_rate').removeAttr('disabled','disabled')".";\n";
				echo "$('#txt_invoice_val').attr('disabled','disabled')".";\n";
				echo "$('#txt_invo_qnty').attr('disabled','disabled')".";\n";				
			}

			echo "$('#tbl_order_list tbody tr').remove();\n";
			echo "$('#order_details').html('".$table."')".";\n";
			echo "active_inactive();\n";
			//echo "var tableFilters = {col_1:'none',col_2:'none',col_3:'none',col_4:'none',col_5:'none',col_6:'none',col_7:'none',col_8:'none',col_9:'none',col_10:'none'};\n";
			//if($row_number>0) echo "setFilterGrid('tbl_order_list',-1,tableFilters);\n";
			if($row_number>0) echo "setFilterGrid('tbl_order_list',-1);\n";
		}
	}
	else
	{
		$dealing_merchant_array = return_library_array("select id, team_member_name from lib_mkt_team_member_info","id","team_member_name");

		if($load_all_po==1){
			$leftJoinCond=" left join com_export_invoice_ship_dtls c on c.po_breakdown_id=b.wo_po_break_down_id and c.mst_id=$invoice_id and c.status_active=1 and c.is_deleted=0";
			$extWhereCond=$extJoinCond="";
		}
		else{
			$leftJoinCond="";
			$extJoinCond=", com_export_invoice_ship_dtls c ";
			$extWhereCond=" and c.po_breakdown_id=b.wo_po_break_down_id and c.mst_id=$invoice_id and c.status_active=1 and c.is_deleted=0 and c.current_invoice_qnty>0";
		}
		// echo "10**".$is_lcSc;die;
		$full_ship_check="";
		if($shipment_check==1) $full_ship_check=" and a.shiping_status<>3";
		if($is_lcSc== '1')
		{
			$exFactoryArr=return_library_array("select sum((case when a.entry_form<>85 then a.ex_factory_qnty else 0 end)-(case when a.entry_form=85 then a.ex_factory_qnty else 0 end)) as qnty, a.po_break_down_id from pro_ex_factory_mst a, com_export_lc_order_info b where a.po_break_down_id=b.wo_po_break_down_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0  and b.is_service<>1 and b.com_export_lc_id=$lcSc_id group by po_break_down_id","po_break_down_id","qnty");
			$article_res = sql_select("select a.article_number, a.po_break_down_id from wo_po_color_size_breakdown a, com_export_lc_order_info b where a.po_break_down_id=b.wo_po_break_down_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and b.com_export_lc_id=$lcSc_id ");
			foreach ($article_res as $artiVal)
			{
				if($artiVal[csf("article_number")] != "")
				{
					if($articleNoArr[$artiVal[csf("po_break_down_id")]] == "")
					{
						$articleNoArr[$artiVal[csf("po_break_down_id")]] = $artiVal[csf("article_number")];
					}else{
						$articleNoArr[$artiVal[csf("po_break_down_id")]] .= ",".$artiVal[csf("article_number")];
					}
	
				}
			}

			
			$sql= "SELECT id, export_lc_no as lc_sc, lien_bank, tolerance, applicant_name, beneficiary_name, buyer_name, shipping_mode, inco_term, inco_term_place, port_of_entry, port_of_loading, port_of_discharge, internal_file_no,consignee,notifying_party FROM com_export_lc where id= $lcSc_id";
			$cat_wise_entry_form=array(10=>'472', 23=>'278', 36=>'311', 35=>'204', 37=>'295', 45=>'255', 67=>'238');
			if($export_item_category==35 || $export_item_category==36)
			{
				$tbl_relation= "m.embellishment_job = a.job_no_mst";
			}
			else
			{
				$tbl_relation= "m.subcon_job = a.job_no_mst";
			} 
			// if($export_item_category!=67){$within_group=' and m.within_group=2 ';}else{$within_group='';}

			if($invoice_id==0 || $invoice_id=="")
			{ 
				$order_sql="SELECT a.id as ORDER_ID, a.job_no_mst as JOB_NO_MST, a.po_number as PO_NUMBER, a.po_quantity as PO_QUANTITY, a.pub_shipment_date as PUB_SHIPMENT_DATE, b.attached_qnty as ATTACHED_QNTY, b.attached_rate as ATTACHED_RATE, b.attached_value as ATTACHED_VALUE, b.is_service as IS_SERVICE, m.style_ref_no as STYLE_REF_NO, m.dealing_marchant as DEALING_MARCHANT, m.order_uom as ORDER_UOM, 0 as TYPE, m.brand_id as BRAND_ID
				FROM com_export_lc_order_info b, wo_po_break_down a, wo_po_details_master m
				where a.job_no_mst=m.job_no and b.com_export_lc_id=$lcSc_id and b.wo_po_break_down_id=a.id and b.status_active=1 and b.is_deleted=0 and a.status_active=1 and a.is_deleted=0 and b.is_sales=0 and b.is_service=0 $full_ship_check
				union all
				SELECT m.id as ORDER_ID, a.job_no_mst as JOB_NO_MST, a.job_no_mst as PO_NUMBER, sum(a.finish_qty) as PO_QUANTITY, m.delivery_date as PUB_SHIPMENT_DATE, b.attached_qnty as ATTACHED_QNTY, b.attached_rate as ATTACHED_RATE, b.attached_value as ATTACHED_VALUE, b.is_service as IS_SERVICE, m.style_ref_no as STYLE_REF_NO, m.dealing_marchant as DEALING_MARCHANT, a.order_uom as ORDER_UOM, 1 as TYPE, 0 as BRAND_ID
				FROM com_export_lc_order_info b, fabric_sales_order_dtls a, fabric_sales_order_mst m
				where b.com_export_lc_id=$lcSc_id and b.wo_po_break_down_id=m.id and m.id=a.mst_id and b.status_active=1 and b.is_deleted=0 and a.status_active=1 and a.is_deleted=0 and b.is_sales=1 and b.is_service=0 
				group by m.id, a.job_no_mst, m.delivery_date ,b.attached_qnty , b.attached_rate, b.attached_value, b.is_service , m.style_ref_no , m.dealing_marchant , a.order_uom
				union all
				SELECT a.id as ORDER_ID, a.job_no_mst as JOB_NO_MST, a.order_no as PO_NUMBER, a.order_quantity as PO_QUANTITY, m.delivery_date as PUB_SHIPMENT_DATE, b.attached_qnty as ATTACHED_QNTY, b.attached_rate as ATTACHED_RATE, b.attached_value as ATTACHED_VALUE, b.is_service as IS_SERVICE, a.buyer_style_ref as STYLE_REF_NO, 0 as DEALING_MARCHANT, a.order_uom as ORDER_UOM, 2 as TYPE, 0 as BRAND_ID
                FROM com_export_lc_order_info b, subcon_ord_dtls a, subcon_ord_mst m
                WHERE $tbl_relation and m.entry_form = '$cat_wise_entry_form[$export_item_category]' and b.com_export_lc_id=$lcSc_id and b.wo_po_break_down_id=a.id and b.status_active=1 and b.is_deleted=0  and a.is_deleted = 0 AND a.status_active = 1 and m.is_deleted = 0 AND m.status_active = 1 and b.is_sales=0 and b.is_service=1";
			}
			else
			{
				$order_sql="SELECT a.id as ORDER_ID, a.job_no_mst as JOB_NO_MST, a.po_number as PO_NUMBER, a.po_quantity as PO_QUANTITY, a.pub_shipment_date as PUB_SHIPMENT_DATE, b.attached_qnty as ATTACHED_QNTY, b.attached_rate as ATTACHED_RATE, b.attached_value as ATTACHED_VALUE, b.is_service as IS_SERVICE, c.id as DTLS_ID, c.mst_id as MST_ID, c.current_invoice_rate as RATE, c.current_invoice_qnty as CURRENT_INVOICE_QNTY, c.current_invoice_value as CURRENT_INVOICE_VALUE,c.carton_qty as CARTON_QTY, c.production_source as PRODUCTION_SOURCE, c.color_size_rate_data as COLOR_SIZE_RATE_DATA, c.actual_po_infos as ACTUAL_PO_INFOS, m.style_ref_no as STYLE_REF_NO, m.dealing_marchant as DEALING_MARCHANT, m.order_uom as ORDER_UOM, 0 as TYPE, m.brand_id as BRAND_ID
				FROM wo_po_details_master m, wo_po_break_down a, com_export_lc_order_info b $leftJoinCond $extJoinCond
				where a.job_no_mst=m.job_no and b.com_export_lc_id=$lcSc_id and b.wo_po_break_down_id=a.id and b.status_active=1 and b.is_deleted=0 and a.status_active=1 and a.is_deleted=0 and b.is_sales=0 and b.is_service=0 $extWhereCond $full_ship_check
				group by a.id, a.job_no_mst, a.po_number, a.po_quantity, a.pub_shipment_date, b.attached_qnty, b.attached_rate, b.attached_value, b.is_service, c.id, c.mst_id, c.current_invoice_rate, c.current_invoice_qnty, c.current_invoice_value,c.carton_qty, c.production_source, c.color_size_rate_data, c.actual_po_infos, m.style_ref_no, m.dealing_marchant,  m.order_uom, m.brand_id
				union all
				SELECT m.id as ORDER_ID, a.job_no_mst as JOB_NO_MST, a.job_no_mst as PO_NUMBER, sum(a.finish_qty) as PO_QUANTITY, m.delivery_date as PUB_SHIPMENT_DATE, b.attached_qnty as ATTACHED_QNTY, b.attached_rate as ATTACHED_RATE, b.attached_value as ATTACHED_VALUE, b.is_service as IS_SERVICE, c.id as DTLS_ID, c.mst_id as MST_ID, c.current_invoice_rate as RATE, c.current_invoice_qnty as CURRENT_INVOICE_QNTY, c.current_invoice_value as CURRENT_INVOICE_VALUE,c.carton_qty as CARTON_QTY, c.production_source as PRODUCTION_SOURCE, c.color_size_rate_data as COLOR_SIZE_RATE_DATA, c.actual_po_infos as ACTUAL_PO_INFOS, m.style_ref_no as STYLE_REF_NO, m.dealing_marchant as DEALING_MARCHANT, a.order_uom as ORDER_UOM, 1 as TYPE, 0 as BRAND_ID
				FROM fabric_sales_order_mst m, fabric_sales_order_dtls a, com_export_lc_order_info b $leftJoinCond $extJoinCond
				where b.com_export_lc_id=$lcSc_id and b.wo_po_break_down_id=m.id and m.id=a.mst_id and b.status_active=1 and b.is_deleted=0 and a.status_active=1 and a.is_deleted=0 and b.is_sales=1 and b.is_service=0 $extWhereCond
				group by m.id, a.job_no_mst, m.delivery_date, b.attached_qnty, b.attached_rate, b.attached_value, b.is_service, c.id, c.mst_id, c.current_invoice_rate, c.current_invoice_qnty, c.current_invoice_value,c.carton_qty, c.production_source, c.color_size_rate_data, c.actual_po_infos, m.style_ref_no, m.dealing_marchant, a.order_uom 
				union all
				SELECT a.id as ORDER_ID, a.job_no_mst as JOB_NO_MST, a.order_no as PO_NUMBER, a.order_quantity as PO_QUANTITY, m.delivery_date as PUB_SHIPMENT_DATE, b.attached_qnty as ATTACHED_QNTY, b.attached_rate as ATTACHED_RATE, b.attached_value as ATTACHED_VALUE, b.is_service as IS_SERVICE, c.id as DTLS_ID, c.mst_id as MST_ID, c.current_invoice_rate as RATE, c.current_invoice_qnty as CURRENT_INVOICE_QNTY, c.current_invoice_value as CURRENT_INVOICE_VALUE,c.carton_qty as CARTON_QTY, c.production_source as PRODUCTION_SOURCE, c.color_size_rate_data as COLOR_SIZE_RATE_DATA, c.actual_po_infos as ACTUAL_PO_INFOS, a.buyer_style_ref as STYLE_REF_NO, 0 as DEALING_MARCHANT, a.order_uom as ORDER_UOM, 2 as TYPE, 0 as BRAND_ID
                FROM subcon_ord_mst m, subcon_ord_dtls a, com_export_lc_order_info b $leftJoinCond $extJoinCond
                WHERE $tbl_relation and m.entry_form = '$cat_wise_entry_form[$export_item_category]' and b.com_export_lc_id=$lcSc_id and b.wo_po_break_down_id=a.id and b.status_active=1 and b.is_deleted=0  and a.is_deleted = 0 AND a.status_active = 1 and m.is_deleted = 0 AND m.status_active = 1 and b.is_sales=0 and b.is_service=1 $within_group $extWhereCond
				group by a.id, a.job_no_mst, a.order_no, a.order_quantity, m.delivery_date, b.attached_qnty, b.attached_rate, b.attached_value, b.is_service, c.id, c.mst_id, c.current_invoice_rate, c.current_invoice_qnty, c.current_invoice_value,c.carton_qty, c.production_source, c.color_size_rate_data, c.actual_po_infos, a.buyer_style_ref, a.order_uom
				order by current_invoice_qnty";				
			}
		}
		else if($is_lcSc=='2')
		{
			$exFactoryArr=return_library_array("select sum((case when a.entry_form<>85 then a.ex_factory_qnty else 0 end)-(case when a.entry_form=85 then a.ex_factory_qnty else 0 end)) as qnty, a.po_break_down_id from pro_ex_factory_mst a, com_sales_contract_order_info b where a.po_break_down_id=b.wo_po_break_down_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and b.com_sales_contract_id=$lcSc_id group by po_break_down_id","po_break_down_id","qnty");
			
			$article_res = sql_select("select a.article_number as ARTICLE_NUMBER, a.po_break_down_id as PO_BREAK_DOWN_ID from wo_po_color_size_breakdown a, com_sales_contract_order_info b where a.po_break_down_id= b.wo_po_break_down_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and b.com_sales_contract_id=$lcSc_id");
			foreach ($article_res as $artiVal)
			{
				if($artiVal[csf("article_number")] != "")
				{
					if($articleNoArr[$artiVal['PO_BREAK_DOWN_ID']] == "")
					{
						$articleNoArr[$artiVal['PO_BREAK_DOWN_ID']] = $artiVal['ARTICLE_NUMBER'];
					}else{
						$articleNoArr[$artiVal['PO_BREAK_DOWN_ID']] .= ",".$artiVal['ARTICLE_NUMBER'];
					}
	
				}
			}

			$sql= "SELECT id, contract_no as lc_sc ,tolerance, lien_bank, applicant_name, beneficiary_name, buyer_name, shipping_mode, inco_term, inco_term_place, port_of_entry, port_of_loading, port_of_discharge, internal_file_no,consignee,notifying_party FROM com_sales_contract where id=$lcSc_id";

			if($invoice_id==0 || $invoice_id=="")
			{
				$order_sql="SELECT a.id as ORDER_ID, a.job_no_mst as JOB_NO_MST, a.po_number as PO_NUMBER, a.po_quantity as PO_QUANTITY, a.pub_shipment_date as PUB_SHIPMENT_DATE, b.attached_qnty as ATTACHED_QNTY, b.attached_rate as ATTACHED_RATE, b.attached_value as ATTACHED_VALUE, m.style_ref_no as STYLE_REF_NO, m.dealing_marchant as DEALING_MARCHANT, m.order_uom as ORDER_UOM, 0 as TYPE, m.brand_id as BRAND_ID, 0 as IS_SERVICE
				FROM com_sales_contract_order_info b, wo_po_details_master m, wo_po_break_down a
				where a.job_no_mst=m.job_no and b.com_sales_contract_id=$lcSc_id and b.wo_po_break_down_id=a.id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and b.is_sales=0 $full_ship_check
				union all
				SELECT a.id as ORDER_ID, a.job_no as JOB_NO_MST, a.job_no as PO_NUMBER, sum(m.finish_qty) as PO_QUANTITY, a.delivery_date as PUB_SHIPMENT_DATE, b.attached_qnty as ATTACHED_QNTY, b.attached_rate as ATTACHED_RATE, b.attached_value as ATTACHED_VALUE, a.style_ref_no as STYLE_REF_NO, a.dealing_marchant as DEALING_MARCHANT, m.order_uom as ORDER_UOM, 1 as TYPE, 0 as BRAND_ID, 0 as IS_SERVICE
				FROM com_sales_contract_order_info b, fabric_sales_order_mst a, fabric_sales_order_dtls m
				where b.com_sales_contract_id=$lcSc_id and b.wo_po_break_down_id=a.id and a.id=m.mst_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and b.is_sales=1
				group by a.id, a.job_no, a.job_no, a.delivery_date, b.attached_qnty, b.attached_rate, b.attached_value, a.style_ref_no, a.dealing_marchant, m.order_uom
				union all
				SELECT a.id as ORDER_ID, a.style_ref_no as JOB_NO_MST, a.requisition_number as PO_NUMBER, sum(m.sample_prod_qty) as PO_QUANTITY, a.estimated_shipdate as PUB_SHIPMENT_DATE, b.attached_qnty as ATTACHED_QNTY, b.attached_rate as ATTACHED_RATE, b.attached_value as ATTACHED_VALUE, a.style_ref_no as STYLE_REF_NO, a.dealing_marchant as DEALING_MARCHANT, 1 as ORDER_UOM, 2 as TYPE, 0 as BRAND_ID, 0 as IS_SERVICE
				FROM com_sales_contract_order_info b, sample_development_mst a, sample_development_dtls m
				where b.com_sales_contract_id=$lcSc_id and b.wo_po_break_down_id=a.id and a.id=m.sample_mst_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and b.is_sales=3
				group by a.id, a.style_ref_no, a.requisition_number, a.estimated_shipdate, b.attached_qnty, b.attached_rate, b.attached_value, a.style_ref_no, a.dealing_marchant";
			}
			else
			{
				$order_sql="SELECT a.id as ORDER_ID, a.job_no_mst as JOB_NO_MST, a.po_number as PO_NUMBER, a.po_quantity as PO_QUANTITY, a.pub_shipment_date as PUB_SHIPMENT_DATE, b.attached_qnty as ATTACHED_QNTY, b.attached_rate as ATTACHED_RATE, b.attached_value as ATTACHED_VALUE, c.id as DTLS_ID, c.mst_id as MST_ID, c.current_invoice_rate as RATE, c.current_invoice_qnty as CURRENT_INVOICE_QNTY, c.current_invoice_value as CURRENT_INVOICE_VALUE,c.carton_qty as CARTON_QTY, c.production_source as PRODUCTION_SOURCE, c.color_size_rate_data as COLOR_SIZE_RATE_DATA, c.actual_po_infos as ACTUAL_PO_INFOS, m.style_ref_no as STYLE_REF_NO, m.dealing_marchant as DEALING_MARCHANT, m.order_uom as ORDER_UOM, 0 as TYPE, m.brand_id as BRAND_ID, 0 as IS_SERVICE
				FROM wo_po_details_master m, wo_po_break_down a, com_sales_contract_order_info b  $leftJoinCond $extJoinCond
				where a.job_no_mst=m.job_no and b.com_sales_contract_id=$lcSc_id and b.wo_po_break_down_id=a.id and b.status_active=1 and b.is_deleted=0 and a.status_active=1 and a.is_deleted=0 and b.is_sales=0 $extWhereCond $full_ship_check
				group by a.id, a.job_no_mst, a.po_number, a.po_quantity, a.pub_shipment_date, b.attached_qnty, b.attached_rate, b.attached_value, c.id, c.mst_id, c.current_invoice_rate, c.current_invoice_qnty, c.current_invoice_value,c.carton_qty, c.production_source, c.color_size_rate_data, c.actual_po_infos, m.style_ref_no, m.dealing_marchant,  m.order_uom, m.brand_id 
				union all
				SELECT a.id as ORDER_ID, a.job_no as JOB_NO_MST, a.job_no as PO_NUMBER, sum(m.finish_qty) as PO_QUANTITY, a.delivery_date as PUB_SHIPMENT_DATE, b.attached_qnty as ATTACHED_QNTY, b.attached_rate as ATTACHED_RATE, b.attached_value as ATTACHED_VALUE, c.id as DTLS_ID, c.mst_id as MST_ID, c.current_invoice_rate as RATE, c.current_invoice_qnty as CURRENT_INVOICE_QNTY, c.current_invoice_value as CURRENT_INVOICE_VALUE,c.carton_qty as CARTON_QTY, c.production_source as PRODUCTION_SOURCE, c.color_size_rate_data as COLOR_SIZE_RATE_DATA, c.actual_po_infos as ACTUAL_PO_INFOS, a.style_ref_no as STYLE_REF_NO, a.dealing_marchant as DEALING_MARCHANT, m.order_uom as ORDER_UOM, 1 as TYPE, 0 as BRAND_ID, 0 as IS_SERVICE
				FROM fabric_sales_order_dtls m, fabric_sales_order_mst a, com_sales_contract_order_info b  $leftJoinCond $extJoinCond
				where a.id=m.mst_id and b.com_sales_contract_id=$lcSc_id and b.wo_po_break_down_id=a.id and b.status_active=1 and b.is_deleted=0 and a.status_active=1 and a.is_deleted=0 and b.is_sales=1 $extWhereCond
				group by a.id, a.job_no, a.delivery_date, b.attached_qnty, b.attached_rate, b.attached_value, c.id, c.mst_id, c.current_invoice_rate, c.current_invoice_qnty, c.current_invoice_value,c.carton_qty, c.production_source, c.color_size_rate_data, c.actual_po_infos, a.style_ref_no, a.dealing_marchant, m.order_uom 
				union all
				SELECT a.id as ORDER_ID, a.style_ref_no as JOB_NO_MST, a.requisition_number as PO_NUMBER, sum(m.sample_prod_qty) as PO_QUANTITY, a.estimated_shipdate as PUB_SHIPMENT_DATE, b.attached_qnty as ATTACHED_QNTY, b.attached_rate as ATTACHED_RATE, b.attached_value as ATTACHED_VALUE, c.id as DTLS_ID, c.mst_id as MST_ID, c.current_invoice_rate as RATE, c.current_invoice_qnty as CURRENT_INVOICE_QNTY, c.current_invoice_value as CURRENT_INVOICE_VALUE,c.carton_qty as CARTON_QTY, c.production_source as PRODUCTION_SOURCE, c.color_size_rate_data as COLOR_SIZE_RATE_DATA, c.actual_po_infos as ACTUAL_PO_INFOS, a.style_ref_no as STYLE_REF_NO, a.dealing_marchant as DEALING_MARCHANT, 1 as ORDER_UOM, 2 as TYPE, 0 as BRAND_ID, 0 as IS_SERVICE
				FROM sample_development_dtls m, sample_development_mst a, com_sales_contract_order_info b  $leftJoinCond $extJoinCond
				where a.id=m.sample_mst_id and b.com_sales_contract_id=$lcSc_id and b.wo_po_break_down_id=a.id and b.status_active=1 and b.is_deleted=0 and a.status_active=1 and a.is_deleted=0 and b.is_sales=3 $extWhereCond
				group by a.id, a.style_ref_no, a.requisition_number, a.estimated_shipdate, b.attached_qnty, b.attached_rate, b.attached_value, c.id, c.mst_id, c.current_invoice_rate, c.current_invoice_qnty, c.current_invoice_value,c.carton_qty, c.production_source, c.color_size_rate_data, c.actual_po_infos, a.style_ref_no, a.dealing_marchant 
				order by current_invoice_qnty";
			}
		}
		
		// echo $order_sql;//die;
		$data_array=sql_select($sql);
		$data_array_po_attached=sql_select($order_sql);
		$buyer_order_ids=array();
		foreach($data_array_po_attached as $val)
		{
			if($val["TYPE"]==0)
			{
				$buyer_order_ids[$val["ORDER_ID"]]=$val["ORDER_ID"];
			}
		}
		if(count($buyer_order_ids)>0)
		{
			$ac_po_sql="select PO_BREAK_DOWN_ID, ACC_PO_NO from WO_PO_ACC_PO_INFO where STATUS_ACTIVE=1 and PO_BREAK_DOWN_ID in(".implode(",",$buyer_order_ids).")";
			$ac_po_sql_result=sql_select($ac_po_sql);
			$ac_po_no_arr=array();
			foreach($ac_po_sql_result as $val)
			{
				$ac_po_no_arr[$val["PO_BREAK_DOWN_ID"]]=$val["ACC_PO_NO"];
			}
		}
		//echo "test";die;
		
		foreach ($data_array as $row)
		{
			$row_number = count($data_array_po_attached);

			echo "document.getElementById('cbo_beneficiary_name').value			= '".$row[csf("beneficiary_name")]."';\n";
			echo "document.getElementById('cbo_buyer_name').value 				= ".$row[csf("buyer_name")].";\n";
			echo "document.getElementById('cbo_lien_bank').value 				= '".$row[csf("lien_bank")]."';\n";
			echo "document.getElementById('cbo_applicant_name').value 			= '".$row[csf("applicant_name")]."';\n";
			echo "document.getElementById('internal_file_no').value 			= '".$row[csf("internal_file_no")]."';\n";
			echo "document.getElementById('tot_row').value 						= '".$row_number."';\n";
			echo "document.getElementById('consignee').value					= '".$row[csf("consignee")]."';\n";
			echo "document.getElementById('notifying_party').value				= '".$row[csf("notifying_party")]."';\n";


			if($invoice_id==0)
			{
				echo "document.getElementById('txt_lc_sc_no').value 				= '".$row[csf("lc_sc")]."';\n";
				echo "document.getElementById('lc_sc_id').value 					= '".$row[csf("id")]."';\n";
				echo "document.getElementById('import_btb').value 					= '".$import_btb."';\n";
				echo "document.getElementById('is_lc_sc').value 					= '".$is_lcSc."';\n";
				echo "document.getElementById('inco_term').value					= '".$row[csf("inco_term")]."';\n";
				echo "document.getElementById('inco_term_place').value 				= '".$row[csf("inco_term_place")]."';\n";
				echo "document.getElementById('shipping_mode').value 				= '".$row[csf("shipping_mode")]."';\n";
				echo "document.getElementById('port_of_entry').value 				= '".$row[csf("port_of_entry")]."';\n";
				echo "document.getElementById('port_of_loading').value 				= '".$row[csf("port_of_loading")]."';\n";
				echo "document.getElementById('port_of_discharge').value 			= '".$row[csf("port_of_discharge")]."';\n";

				echo "reset_form('','','txt_invoice_val*txt_discount*txt_discount_ammount*txt_bonus*txt_bonus_ammount*txt_claim*txt_claim_ammount*txt_invo_qnty*txt_commission*txt_commission_amt*txt_other_discount*txt_other_discount_amt*txt_upcharge*txt_net_invo_val');\n";
			}

			$table=""; $i=1;
			if($row_number==0)
			{
				$table=$table.'<tr class="general"><td colspan="13" align="center">Order Details is Not Available For This LC/SC </td></tr>';
				echo "$('#order_rate').removeAttr('disabled','disabled')".";\n";
				//echo "$('#txt_invoice_val').removeAttr('disabled','disabled')".";\n";
				//echo "$('#txt_invo_qnty').removeAttr('disabled','disabled')".";\n";
				echo "$('#txt_invoice_val').attr('disabled','disabled')".";\n";
				echo "$('#txt_invo_qnty').attr('disabled','disabled')".";\n";
			}
			else
			{
				$total_attached_order_qnty=$total_ex_factory_qnty="";
				foreach ($data_array_po_attached as $slectResult)
				{
					$tolerance_order_qty = $slectResult['ATTACHED_QNTY']+($row[csf("tolerance")]/100*$slectResult['ATTACHED_QNTY']);
					$total_tolerance_order_qty+=$tolerance_order_qty;

					if($invoice_id==0)
					{
						$unit_price=$slectResult['ATTACHED_RATE'];
					}
					else
					{
						if($slectResult['CURRENT_INVOICE_QNTY'] > 0)
						{
							$unit_price=$slectResult['RATE'];
						}
						else
						{
							$unit_price=$slectResult['ATTACHED_RATE'];
						}
					}

					$shipment_date = change_date_format($slectResult['PUB_SHIPMENT_DATE']);

					$dealing_merchant_id=$slectResult['DEALING_MARCHANT'];
					$dealing_merchant=$dealing_merchant_array[$dealing_merchant_id];

					$article_no=$articleNoArr[$slectResult['ORDER_ID']];
					$article_no=implode(",",array_unique(explode(",",$article_no)));

					$cumu_qty = $po_invoice_data_array[$slectResult['ORDER_ID']]['qnty'];
					$cumu_val = $po_invoice_data_array[$slectResult['ORDER_ID']]['val'];
					
					$pre_cost_amt=$pre_cost_data_array[$slectResult['ORDER_ID']]['commis_amount'];
					
					$prv_commission_amt=$pre_cost_amt*$slectResult['CURRENT_INVOICE_QNTY'];
					//$total_prv_commission_amt+=$pre_cost_amt*$slectResult['CURRENT_INVOICE_QNTY'];

					$po_balance_qnty=$slectResult['ATTACHED_QNTY']-$cumu_qty;

					$total_cumu_qty+=$cumu_qty;
					$total_cumu_val+=$cumu_val;

					$total_value+=$slectResult['CURRENT_INVOICE_VALUE'];
					$total_qty+=$slectResult['CURRENT_INVOICE_QNTY'];

					$colorSize_infos=$slectResult['COLOR_SIZE_RATE_DATA'];
					$act_po_infos=$slectResult['ACTUAL_PO_INFOS'];

					if($slectResult['CURRENT_INVOICE_QNTY']>0) $invc_qnty=$slectResult['CURRENT_INVOICE_QNTY']; else $invc_qnty='';

					$ex_factory_qnty=$exFactoryArr[$slectResult['ORDER_ID']];
					/*$table=$table.'<tr align="center" id="tr_'.$i.'"><td width="115"><font style="display:none">'.$slectResult[csf('po_number')].'</font>\n<input type="hidden" id="order_id_'.$i.'" value="'.$slectResult['ORDER_ID'].'"  /><input type="hidden" id="actual_po_infos_'.$i.'" value="'.$act_po_infos.'" /><input type="text" id="order_no_'.$i.'"  value="'.$slectResult[csf('po_number')].'" class="text_boxes" style="width:100px" readonly ondblclick="pop_entry_actual_po('.$i.')" id="order_no_'.$i.'"  /></td><td width="105"><font style="display:none">'.$slectResult[csf('style_ref_no')].'</font>\n<input type="text" id="style_ref_no_'.$i.'"  value="'.$slectResult[csf('style_ref_no')].'" class="text_boxes" style="width:95px" disabled/></td><td width="80"><font style="display:none">'.$article_no.'</font>\n<input type="text" id="article_no_'.$i.'"  value="'.$article_no.'" class="text_boxes" style="width:65px" disabled/></td><td width="70"><input type="text" id="shipment_date_'.$i.'" value="'.$shipment_date.'" class="datepicker" style="width:55px" disabled /></td><td width="80"><input type="hidden" disabled  id="tollerence_order_qty_'.$i.'" value="'.$tolerance_order_qty.'" /><input type="text" disabled id="order_qty_'.$i.'" value="'.$slectResult['ATTACHED_QNTY'].'" class="text_boxes_numeric" style="width:65px;"/></td><td width="70"><input type="text"  id="order_uom_'.$i.'" value="'.$unit_of_measurement[$slectResult[csf('order_uom')]].'" class="text_boxes" style="width:55px;" '.$readonly_status.' /></td><td width="70"><input type="text"  id="order_rate_'.$i.'" value="'.$unit_price.'" class="text_boxes_numeric" style="width:57px;" onKeyUp="calculate_value_rate('.$i.')" '.$readonly_status.' /></td><td width="80"><input type="text" id="curr_invo_qty_'.$i.'" class="text_boxes_numeric" style="width:65px" onKeyUp="calculate_value_rate('.$i.')" value="'.$invc_qnty.'" ondblclick="openpage_colorSize('.$i.')" '.$disabled.' /><input type="hidden"  id="curr_hide_invo_qty_'.$i.'" value="'.$slectResult['CURRENT_INVOICE_QNTY'].'" /><input type="hidden" id="colorSize_infos_'.$i.'" value="'.$colorSize_infos.'" /></td><td width="95"><input name="text" type="text" id="curr_invo_val_'.$i.'" class="text_boxes_numeric" value="'.$slectResult[csf('current_invoice_value')].'" style="width:80px;" disabled /><input type="hidden" id="curr_hide_invo_val_'.$i.'" value="'.$slectResult[csf('current_invoice_value')].'" /></td><td width="80"><input type="text" id="cum_invo_qty_'.$i.'" value="'.$cumu_qty.'" disabled class="text_boxes_numeric" style="width:65px;background: #D0EFC2;"/><input type="hidden" id="hide_cum_invo_qty_'.$i.'" value="'.$cumu_qty.'" /></td><td width="80"><input type="text" id="po_bl_qty_'.$i.'" value="'.$po_balance_qnty.'" disabled class="text_boxes_numeric" style="width:65px;"/></td><td width="95"><input type="text" id="cum_invo_val_'.$i.'"  value="'.$cumu_val.'" disabled  class="text_boxes_numeric" style="width:80px;" /><input type="hidden" id="hide_cum_invo_val_'.$i.'" value="'.$cumu_val.'" /></td><td width="80"><input type="text" id="ex_factory_qty_'.$i.'" value="'.$ex_factory_qnty.'" disabled class="text_boxes_numeric" style="width:65px;"/></td><td width="105"><input type="text" title="'.$dealing_merchant.'" value="'.$dealing_merchant.'" disabled class="text_boxes" style="width:90px;"/></td><td>'.create_drop_down( "cbo_production_source_$i", 90, $knitting_source,'', 0, '', $slectResult[csf('production_source')], '','','1,3' ).'</td></tr>';
					<input type="text" id="order_no_'.$i.'"  value="'.$slectResult[csf('po_number')].'" class="text_boxes" style="width:100px" readonly ondblclick="pop_entry_actual_po('.$i.')" id="order_no_'.$i.'" />
					<input type="hidden" id="pre_cost_amt_'.$i.'"  value="'.$pre_cost_amt.'" class="text_boxes" style="width:100px" readonly  id="hidden_commission_amt_'.$i.'" />
					<input type="hidden" id="hidden_commission_amt_'.$i.'"  value="'.$prv_commission_amt.'" class="text_boxes" style="width:100px" readonly  id="pre_cost_amt_'.$i.'" />
					//#### ondblclick="pop_entry_actual_po('.$i.')"  address in openpage_colorSize() function
					*/
					
					$readonly_field='';
					if($slectResult['TYPE']==0)
					{
						if($slectResult['ATTACHED_QNTY']!=$po_info[$slectResult['ORDER_ID']]['po_quantity'] || $unit_price!=$po_info[$slectResult['ORDER_ID']]['po_rate'])
						{$row_color='style="background-color:#F60;"';}else{$row_color="";}
						
						if($variable_setting_ac_po==1 && $ac_po_no_arr[$slectResult['ORDER_ID']]!="") $readonly_field=' readonly';
						
					}
					if($slectResult['ORDER_UOM']==58) $uom_color='style="color:7112DR;"'; else $uom_color="";
					
					$table=$table.'<tr align="center" id="tr_'.$i.'" onclick="change_color(this);" '.$row_color.'><td width="80" id="td_job_no_'.$i.'" style="word-break:break-all">'.$slectResult['JOB_NO_MST'].'</td><td width="90" id="td_order_no_'.$i.'" ondblclick="pop_entry_actual_po('.$i.')" title="'.$act_po_infos.'" orderid="'.$slectResult['ORDER_ID'].'" ordertype="'.$slectResult['TYPE'].'" isservice="'.$slectResult['IS_SERVICE'].'" style="cursor:pointer; word-break:break-all"><span id="order_no_'.$i.'">'.$slectResult['PO_NUMBER'].'</span> <span id="pre_cost_amt_'.$i.'" style="display:none">'.$pre_cost_amt.'</span><input type="hidden" id="hidden_commission_amt_'.$i.'"  value="'.$prv_commission_amt.'"/>\n</td><td width="105" id="td_style_ref_no_'.$i.'" style="word-break:break-all">'.$slectResult['STYLE_REF_NO'].'</td><td width="80" id="td_article_no_'.$i.'" style="word-break:break-all">'.$article_no.'</td><td width="60" id="td_shipment_date_'.$i.'"  align="center">'.$shipment_date.'</td><td width="80" id="td_order_qty_'.$i.'"  title="'.$tolerance_order_qty.'" align="right">'.$slectResult['ATTACHED_QNTY'].'</td><td width="60" id="td_order_uom_'.$i.'" '.$uom_color.'  title="'.$slectResult['ORDER_UOM'].'">'.$unit_of_measurement[$slectResult['ORDER_UOM']].'</td><td width="70" id="td_order_rate_'.$i.'"><input type="text"  id="order_rate_'.$i.'" value="'.$unit_price.'" class="text_boxes_numeric" style="width:57px;" onKeyUp="calculate_value_rate('.$i.')" '.$disable_rate.' /></td><td width="80" id="td_curr_invo_qty_'.$i.'"  readonly_field="'.$readonly_field.'" title="'.$slectResult['CURRENT_INVOICE_QNTY'].'"><input type="text" id="curr_invo_qty_'.$i.'" class="text_boxes_numeric" style="width:65px" onKeyUp="calculate_value_rate('.$i.')" value="'.$invc_qnty.'" '.$readonly_field.' ondblclick="openpage_colorSize('.$i.')" '.$disabled.' /><input type="hidden" id="actual_po_infos_'.$i.'" value="'.$act_po_infos.'" /></td><td width="95" id="td_curr_invo_val_'.$i.'" title="'.$slectResult['CURRENT_INVOICE_VALUE'].'"><input name="text" type="text" id="curr_invo_val_'.$i.'" class="text_boxes_numeric" value="'.$slectResult['CURRENT_INVOICE_VALUE'].'" style="width:80px;" disabled /></td><td width="65" id="td_cum_invo_qty_'.$i.'" title="'.$cumu_qty.'" align="right">'.$cumu_qty.'</td><td width="65" id="td_po_bl_qty_'.$i.'" align="right">'.$po_balance_qnty.'</td><td width="75" id="td_cum_invo_val_'.$i.'" title="'.$cumu_val.'" align="right">'.$cumu_val.'</td><td width="65" id="td_ex_factory_qty_'.$i.'" align="right">'.$ex_factory_qnty.'</td><td width="90" id="td_dealing_merchant_'.$i.'" title="'.$colorSize_infos.'" style="word-break:break-all">'.$dealing_merchant.'</td><td id="td_cbo_production_source_'.$i.'" title="'.$slectResult['PRODUCTION_SOURCE'].'">'.$knitting_source[$slectResult['PRODUCTION_SOURCE']].'</td><td  width="50" id="td_brand_'.$i.'" title="'.$slectResult['BRAND_ID'].'">'.$brand_arr[$slectResult['BRAND_ID']].'</td><td width="50" id="td_carton_qty_'.$i.'"><input type="text"  id="carton_qty_'.$i.'" value="'.$slectResult[csf('carton_qty')].'" class="text_boxes_numeric" style="width:50px;" /></td></tr>';

					$i++;
					$total_ex_factory_qnty+=$ex_factory_qnty;
					$total_attached_order_qnty+=$slectResult['ATTACHED_QNTY'];
				}

				$table=$table.'<tr class="tbl_bottom"><td colspan="5">Total</td><td><input type="text" disabled  id="total_attached_order_qnty" value="'.$total_attached_order_qnty.'" class="text_boxes_numeric" style="width:65px;"/></td><td></td><td><input type="hidden" id="total_tolerence_order_qty" value="'.$total_tolerance_order_qty.'" /><input type="hidden" id="hiddien_total_commission_amt" value="" /></td><td><input type="text" disabled id="total_current_invoice_qty" value="'.$total_qty.'" disabled class="text_boxes_numeric" style="width:65px;" /></td><td><input type="text" disabled  id="total_current_invoice_val" value="'.$total_value.'" disabled class="text_boxes_numeric" style="width:80px;" /></td><td></td><td></td><td></td><td><input type="text" disabled  id="total_ex_factory_qnty" value="'.$total_ex_factory_qnty.'" class="text_boxes_numeric" style="width:55px;" /></td><td colspan="3"></td></tr>';

				if ($variable_setting==1) echo "$('#order_rate').attr('disabled','disabled')".";\n";
				else echo "$('#order_rate').removeAttr('disabled','disabled')".";\n";
				echo "$('#txt_invoice_val').attr('disabled','disabled')".";\n";
				echo "$('#txt_invo_qnty').attr('disabled','disabled')".";\n";
			}

			echo "$('#tbl_order_list tbody tr').remove();\n";
			echo "$('#order_details').html('".$table."')".";\n";
			echo "active_inactive();\n";
			//echo "var tableFilters = {col_1:'none',col_2:'none',col_3:'none',col_4:'none',col_5:'none',col_6:'none',col_7:'none',col_8:'none',col_9:'none',col_10:'none'};\n";
			//if($row_number>0) echo "setFilterGrid('tbl_order_list',-1,tableFilters);\n";
			if($row_number>0) echo "setFilterGrid('tbl_order_list',-1);\n";
		}
	}
	exit();
}

if($action=='invoice_qty_popup'){
	echo load_html_head_contents("Export Invoice Qty Form", "../../../", 1, 1,'','1','');
	extract($_REQUEST);
	$invoiceArr=array();
	$sqlinvoice=sql_select("select po_breakdown_id,current_invoice_qnty from com_export_invoice_ship_dtls where mst_id=$invoice_id and po_breakdown_id in(".implode(",",json_decode($order_id,true)).") and current_invoice_qnty >0 and status_active=1 and is_deleted=0");
	foreach($sqlinvoice as $rowinvoice){
		$invoiceArr[$rowinvoice[csf('po_breakdown_id')]]=$rowinvoice[csf('current_invoice_qnty')];
	}
	$gateOutIdArr=array();
	$sqlgateout=sql_select("select id from inv_gate_out_scan where invoice_id=$invoice_id");
	foreach($sqlgateout as $rowgateout){
		$gateOutIdArr[$rowgateout[csf('id')]]=$rowgateout[csf('id')];
	}

	?>
    <style>
		.highlight { background-color: red; }
	</style>
    <script>
		var poIdAndQty={};
		var poId={};
		var gateOutIdArr={};
		var gmtsDelvIdArr={};

		var invoiceData='<? echo json_encode($invoiceArr); ?>'
		var finalData=JSON.parse(invoiceData);
		for(f in finalData){
			poId[f]=f;
			poIdAndQty[f]=finalData[f];
		}
		<? if($invoice_id){?>
		var gateoutData='<? echo json_encode($gateOutIdArr); ?>'
		var gateOutIdArr=JSON.parse(gateoutData);
		<?
		}
		?>
		var lcb='<? echo $order_id;?>'
		var lcOrderArr={};
		var lcOrderJson=JSON.parse(lcb);
		for(a in lcOrderJson){
			lcOrderArr[lcOrderJson[a]]=lcOrderJson[a];
		}
		function check (data) {
			for(bb in data){
			if(lcOrderArr[bb]==bb){
				continue;
			}else{
				return false;
			}
			return true;;
			}
		}
		function js_set_value(tr,gateOutId,gmtsDelvId,data)
		{
			var valid=check (data);
			if(valid==false){
				alert("Gate out PO not available in L/C.\nPlease attach the PO in L/C first to create invoice");
				$(tr).removeClass("highlight");
				return;
			}
			var selected = $(tr).hasClass("highlight");
			$(tr).removeClass("highlight");
			if(!selected){
			$(tr).addClass("highlight");
				gateOutIdArr[gateOutId]=gateOutId;
				gmtsDelvIdArr[gmtsDelvId]=gmtsDelvId;
				for(b in data){
					poId[b]=b;
					if(poIdAndQty[b]){
						poIdAndQty[b]+=data[b]*1;
					}else{
						poIdAndQty[b]=data[b]*1;
					}
				}
			}else{
				delete gateOutIdArr[gateOutId];
				delete gmtsDelvIdArr[gmtsDelvId]
				for(b in data){
					if(poIdAndQty[b]){
						poIdAndQty[b]-=data[b]*1;
						finalData[b]-=data[b]*1;
					}
					if(poIdAndQty[b]<=0){
						delete poId[b];
						delete finalData[b];
					}
				}
			}
		}
		function setData(){
			var poIdlength = Object.keys(poId).length;
			var lcOrderArrlength = Object.keys(lcOrderArr).length;
			if(poIdlength>lcOrderArrlength){
				alert('Po selected here is more than the PO of L/C');
				return;
			}
			for(p in poId){
				if(lcOrderArr[p]==poId[p]){
					finalData[lcOrderArr[p]]=poIdAndQty[lcOrderArr[p]];
				}else{
					alert("Gate out PO not available in L/C.\nPlease attach the PO in L/C first to create invoice");
					return;
				}
			}
			var finalDatalength = Object.keys(finalData).length;
			if(finalDatalength){
				document.getElementById('final_data').value=JSON.stringify(finalData);
				document.getElementById('gate_out_id').value=JSON.stringify(gateOutIdArr);
				document.getElementById('gmts_delv_id').value=JSON.stringify(gmtsDelvIdArr);
				parent.emailwindow.hide();
			}else{
				alert("select at least one");
			}
		}

    </script>

	</head>
	<body>
	<div align="center" style="width:740px;">
	<? //echo $order_id; echo implode(",",json_decode($order_id,true)); ?>
	<form name="searchexportinformationfrm"  id="searchexportinformationfrm">
			<fieldset style="width:720px;">
			<legend>Enter search words</legend>
				<table cellpadding="0" cellspacing="0" width="700" class="rpt_table" border="1" rules="all">

				<input type="hidden" name="lc_sc_id" id="lc_sc_id" value="<? echo $lc_sc_id;  ?>" />
				<input type="hidden" name="is_lc_sc" id="is_lc_sc" value="<? echo $is_lc_sc;  ?>" />
				<input type="hidden" name="export_invoice_qty_source" id="export_invoice_qty_source" value="<? echo $export_invoice_qty_source;  ?>" />
				<input type="hidden" name="order_id" id="order_id" value="<? echo implode(",",json_decode($order_id,true)); ?>" />
				<input type="hidden" name="invoiceId" id="invoiceId" value="<? echo $invoice_id;  ?>" />
				<input type="hidden" name="final_data" id="final_data" value="" />
				<input type="hidden" name="gate_out_id" id="gate_out_id" value="" />
				<input type="hidden" name="gmts_delv_id" id="gmts_delv_id" value="" />
				<input type="hidden" style="width:40px" class="text_boxes"  name="is_attach" id="is_attach" value="<? echo $is_attach; ?>" />

					<thead>
					<tr>
						<th>Gate Out ID</th>
						<th><input type="text" style="width:100px" class="text_boxes"  name="gateout_id" id="gateout_id" /></th>
						<th>Gmts. Delv. ID</th>
						<th><input type="text" style="width:100px" class="text_boxes"  name="gmtsdelv_id" id="gmtsdelv_id" />
						</th>
						<th>
						<input type="button" id="search_button" class="formbutton" value="Show" onClick="show_list_view ( document.getElementById('lc_sc_id').value+'**'+document.getElementById('is_lc_sc').value+'**'+document.getElementById('export_invoice_qty_source').value+'**'+document.getElementById('order_id').value+'**'+document.getElementById('gateout_id').value+'**'+document.getElementById('gmtsdelv_id').value+'**'+document.getElementById('is_attach').value+'**'+document.getElementById('invoiceId').value, 'invoice_qty_list_view', 'search_div', 'export_information_entry_controller', 'setFilterGrid(\'list_view\',-1)')" style="width:100px;" />
						</th>
						</tr>
						<tr>

						<th colspan="5" align="center">
						<input type="button" id="search_button" class="formbutton" value="<? if($is_attach==1){echo "Attach";}else{ echo "detach ";} ?> " onClick="setData()" style="width:100px;" />

						</th>
						</tr>
					</thead>
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

if($action=='invoice_qty_list_view'){
	//extract($_REQUEST);
	$data=explode("**",$data);
	$lc_sc_id=$data[0];
	$is_lc_sc=$data[1];
	$export_invoice_qty_source=$data[2];
	$order_id=$data[3];
	$gate_out_id=$data[4];
	$gmts_delv_id=$data[5];
	$is_attach=$data[6];
	$invoiceId=$data[7];
	$gateOutcond="";
	if($gate_out_id !=""){
		$gateOutcond=" and a.id=$gate_out_id";
	}else{
		$gateOutcond=" ";
	}
	 //$sql= "select a.id, a.gate_pass_id,b.challan_no,c.buyer_order,c.buyer_order_id,c.quantity,d.id as gmts_delv_id from inv_gate_out_scan a, inv_gate_pass_mst b, inv_gate_pass_dtls c, pro_ex_factory_delivery_mst d where a.gate_pass_id=b.sys_number and b.id=c.mst_id  and b.challan_no=d.sys_number  and b.basis=12 and c.item_category_id=30 $gateOutcond and c.buyer_order_id in ($order_id) and a.invoice_id=0  and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 order by a.id";//
	 if($is_attach==1){
	 $sql= "select m.id, m.gate_pass_id,m.challan_no,m.buyer_order,m.buyer_order_id,m.quantity,delv.id as gmts_delv_id,delv.sys_number,ex.po_break_down_id,ex.ex_factory_qnty,po.po_number from  pro_ex_factory_delivery_mst delv join (select a.id, a.gate_pass_id,b.challan_no,c.buyer_order,c.buyer_order_id,c.quantity from inv_gate_out_scan a, inv_gate_pass_mst b, inv_gate_pass_dtls c  where a.gate_pass_id=b.sys_number and b.id=c.mst_id    and b.basis=12 and c.item_category_id=30 $gateOutcond and c.buyer_order_id in ($order_id) and a.invoice_id=0  and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 order by a.id) m on delv.sys_number=m.challan_no join pro_ex_factory_mst ex on delv.id=ex.delivery_mst_id join  wo_po_break_down po on ex.po_break_down_id=po.id  order by m.id,ex.po_break_down_id";//
	 }else{
		 	 $sql= "select m.id, m.gate_pass_id,m.challan_no,m.buyer_order,m.buyer_order_id,m.quantity,delv.id as gmts_delv_id,delv.sys_number,ex.po_break_down_id,ex.ex_factory_qnty,po.po_number from  pro_ex_factory_delivery_mst delv join (select a.id, a.gate_pass_id,b.challan_no,c.buyer_order,c.buyer_order_id,c.quantity from inv_gate_out_scan a, inv_gate_pass_mst b, inv_gate_pass_dtls c  where a.gate_pass_id=b.sys_number and b.id=c.mst_id    and b.basis=12 and c.item_category_id=30 $gateOutcond and c.buyer_order_id in ($order_id) and a.invoice_id =$invoiceId  and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 order by a.id) m on delv.sys_number=m.challan_no join pro_ex_factory_mst ex on delv.id=ex.delivery_mst_id join  wo_po_break_down po on ex.po_break_down_id=po.id  order by m.id,ex.po_break_down_id";//

	 }


	$data_array=sql_select($sql);
	$gridData=array();
	foreach($data_array as $row){
		$gridData[$row[csf('id')]][$row[csf('gmts_delv_id')]]['challan_no']=$row[csf('challan_no')];
		$gridData[$row[csf('id')]][$row[csf('gmts_delv_id')]]['gate_pass_id']=$row[csf('gate_pass_id')];
		$gridData[$row[csf('id')]][$row[csf('gmts_delv_id')]]['gmtsQty'][$row[csf('po_break_down_id')]]=$row[csf('ex_factory_qnty')];
		$gridData[$row[csf('id')]][$row[csf('gmts_delv_id')]]['po_number'][$row[csf('po_break_down_id')]]=$row[csf('po_number')];
	}
	?>
    <table width="720" cellpadding="0" cellspacing="0" border="1" class="rpt_table" rules="all">
        <thead>
            <th width="40">SL</th>
             <th width="100">Gate Pass</th>
            <th width="70">Gate Out ID</th>
            <th width="70">Gmts. Delv. ID</th>
             <th width="130">Challan</th>
            <th width="130">PO</th>
            <th width="80">Buyer</th>

            <th width="">Gmts. Qty</th>
        </thead>
     </table>
     <div style="width:720px; overflow-y:scroll; max-height:280px">
     	<table width="702" cellpadding="0" cellspacing="0" border="1" class="rpt_table" rules="all" id="list_view">
		<?
			$data_array=sql_select($sql);
            $i = 1;
            foreach($gridData as $gateOutId => $gateOutArr)
            {
			foreach($gateOutArr as $gmtsDelvId => $gmtsDelvArr)
            {
                if ($i%2==0)
                    $bgcolor="#FFFFFF";
                else
                    $bgcolor="#E9F3FF";
				?>
				<tr bgcolor="<? echo $bgcolor; ?>" style="text-decoration:none; cursor:pointer;" id="search<? echo $i;?>" onClick='js_set_value(this,<? echo $gateOutId; ?> , <? echo $gmtsDelvId; ?>, <? echo json_encode($gmtsDelvArr['gmtsQty']); ?>);' class="<? if($is_attach==2){echo "highlight" ;}else{echo "";}?>" >
                    <td width="40"><? echo $i; ?></td>
					<td width="100"><? echo $gmtsDelvArr['gate_pass_id']; ?></td>
                    <td width="70"><? echo $gateOutId; ?></td>
					<td width="70"><? echo $gmtsDelvId;  ?></td>
                    <td width="130"><? echo $gmtsDelvArr['challan_no']; ?></td>
                    <td width="130"><? echo implode(",",$gmtsDelvArr['po_number']); ?></td>
					<td width="80"><? //echo change_date_format($row[csf('invoice_date')]); ?></td>

                    <td align="right"><? echo array_sum($gmtsDelvArr['gmtsQty']); ?></td>
				</tr>
            <?
			$i++;
            }
			}
			?>
		</table>
    </div>
    <?
}


/*if($action=="populate_ac_order_data")
{
	$ac_po_sql=sql_select("select a.po_number, a.id from wo_po_break_down a, wo_po_acc_po_info b where a.id=b.po_break_down_id and b.status_active=1 and b.is_deleted=0 and b.acc_po_no='$data'");
	echo $ac_po_sql[0][csf("po_number")].'_'.$ac_po_sql[0][csf("id")];die;
}*/

if($action=="populate_buyer_lcsc_level_data")
{
	$buyer_lcsc_sql=sql_select("select lc_sc_tol_level from lib_buyer where id='$data'");
	echo $buyer_lcsc_sql[0][csf("lc_sc_tol_level")];die;
}

if($action=="populate_ac_order_data")
{
	$ex_data=explode("**", $data);
	$ac_po_sql=sql_select("select a.id from wo_po_break_down a, wo_po_acc_po_info b, wo_po_details_master c where a.id=b.po_break_down_id and a.job_id=c.id and c.company_name=$ex_data[0] and b.status_active=1 and b.is_deleted=0 and b.acc_po_no='$ex_data[1]'");
	$po_no_arr=array();
	foreach($ac_po_sql as $row)
	{
		$po_no_arr[$row[csf("id")]]=$row[csf("id")];
	}
	echo implode("__",$po_no_arr);
	//echo $ac_po_sql[0][csf("po_number")];die;
}

if($action=="populate_internal_ref_data")
{
	$ac_po_sql=sql_select("select a.id as po_id from wo_po_break_down a where a.status_active=1 and a.is_deleted=0 and a.grouping='$data'");
	$po_no_arr=array();
	foreach($ac_po_sql as $row)
	{
		$po_no_arr[$row[csf("po_id")]]=$row[csf("po_id")];
	}
	echo implode("__",$po_no_arr);
	//echo $ac_po_sql[0][csf("po_number")];die;
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
			$('#posted_account').val(data_string[2]);
			if ($('#with_value').attr('checked')){
				$('#is_load_all_po').val(0);
			}
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
				<input type="hidden" name="hidden_invoice_id" id="hidden_invoice_id" value="" />
				<input type="hidden" name="company_id" id="company_id" value="" />
				<input type="hidden" name="posted_account" id="posted_account" value="" />
				<input type="hidden" name="is_load_all_po" id="is_load_all_po" value="1" />
					<thead>
						<tr>
                            <th colspan="4"><? echo create_drop_down( "cbo_string_search_type", 130, $string_search_type,'', 1, "-- Searching Type --",1 ); ?></th>
                            <th colspan="2"><input type="checkbox" name="with_value" id="with_value" /> Load PO with only value</th>
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
								echo create_drop_down( "cbo_company_name", 170, "select comp.id, comp.company_name from lib_company comp where comp.status_active=1 and comp.is_deleted=0 and comp.core_business not in(3) $company_cond order by comp.company_name","id,company_name", 1, "--- Select Company ---", 0, "load_drop_down( 'export_information_entry_controller', this.value, 'load_drop_down_buyer', 'buyer_td_id' );" );
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
							<input type="button" id="search_button" class="formbutton" value="Show" onClick="show_list_view ( document.getElementById('cbo_company_name').value+'**'+document.getElementById('cbo_buyer_name').value+'**'+document.getElementById('cbo_search_by').value+'**'+document.getElementById('txt_search_common').value+'**'+document.getElementById('invoice_start_date').value+'**'+document.getElementById('invoice_end_date').value+'**'+document.getElementById('cbo_string_search_type').value,'invoice_search_list_view', 'search_div', 'export_information_entry_controller', 'setFilterGrid(\'list_view\',-1)')" style="width:100px;" />
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
			if($_SESSION['logic_erp']["buyer_id"]!='') $buyer_id_cond=" and buyer_id in (".$_SESSION['logic_erp']["buyer_id"].")"; else $buyer_id_cond="";
		}
		else
		{
			$buyer_id_cond='';
		}
	}
	else
	{
		$buyer_id_cond=" and buyer_id=$buyer_id";
	}

	$search_text=''; $company_cond ='';
	if($company_id !=0) $company_cond = "and benificiary_id=$company_id";

	if ($invoice_num != '')
	{
		if($search_string==1)
			$search_text="and invoice_no like '".trim($invoice_num)."'";
		else if ($search_string==2) 
			$search_text="and invoice_no like '".trim($invoice_num)."%'";
		else if ($search_string==3)
			$search_text="and invoice_no like '%".trim($invoice_num)."'";
		else if ($search_string==4 || $search_string==0)
			$search_text="and invoice_no like '%".trim($invoice_num)."%'";
	}

	if ($invoice_start_date != '' && $invoice_end_date != '') 
	{
        if ($db_type == 0) {
            $date_cond = "and invoice_date between '" . change_date_format($invoice_start_date, 'yyyy-mm-dd') . "' and '" . change_date_format($invoice_end_date, 'yyyy-mm-dd') . "'";
        } else if ($db_type == 2) {
            $date_cond = "and invoice_date between '" . change_date_format($invoice_start_date, '', '', 1) . "' and '" . change_date_format($invoice_end_date, '', '', 1) . "'";
        }
    } 
    else 
    {
        $date_cond = '';
    }

    $buyer_arr=return_library_array( "select id, short_name from lib_buyer",'id','short_name');
	$comp_arr=return_library_array( "select id, company_short_name from lib_company",'id','company_short_name');

	$sql = "select id, benificiary_id, buyer_id, invoice_no, invoice_date, is_lc, lc_sc_id, invoice_value, net_invo_value, import_btb, is_posted_account from com_export_invoice_ship_mst where status_active=1 and is_deleted=0 $company_cond $search_text $buyer_id_cond $date_cond order by invoice_date desc";
	$data_array=sql_select($sql);		

	$lc_arr=return_library_array( "select id, export_lc_no from com_export_lc",'id','export_lc_no');
	$sc_arr=return_library_array( "select id, contract_no from com_sales_contract",'id','contract_no');

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

				if($row[csf('is_lc')]==1)
				{
					$lc_sc_no=$lc_arr[$row[csf('lc_sc_id')]];
					$is_lc_sc='LC';
				}
				else
				{
					$lc_sc_no=$sc_arr[$row[csf('lc_sc_id')]];
					$is_lc_sc='SC';
				}

				if($row[csf('import_btb')]==1) $buyer=$comp_arr[$row[csf('buyer_id')]]; else $buyer=$buyer_arr[$row[csf('buyer_id')]];
				?>
				<tr bgcolor="<? echo $bgcolor; ?>" style="text-decoration:none; cursor:pointer;" id="search<? echo $i;?>" onClick="js_set_value( '<? echo $row[csf('id')]; ?>_<? echo $row[csf('benificiary_id')]; ?>_<? echo $row[csf('is_posted_account')]; ?>');" >                	
					<td width="40"><? echo $i; ?></td>
					<td width="100"><p><? echo $comp_arr[$row[csf('benificiary_id')]]; ?></p></td>
					<td width="100"><p><? echo $buyer; ?></p></td>
                    <td width="150"><p><? echo $row[csf('invoice_no')]; ?></p></td>
					<td width="100" align="center"><p><? echo change_date_format($row[csf('invoice_date')]); ?></td>
                    <td width="150"><p><? echo $lc_sc_no; ?></p></td>
                    <td width="100" align="center"><p><? echo $is_lc_sc; ?></p></td>
					<td align="right"><p><?
					echo number_format($row[csf('net_invo_value')],2);
					//echo number_format($row[csf('invoice_value')],2); ?></p></td>
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
	$sql="SELECT id, invoice_no, invoice_date, buyer_id, location_id, is_lc, lc_sc_id, import_btb, exp_form_no, exp_form_date, discount_in_percent, discount_ammount, bonus_in_percent, bonus_ammount, claim_in_percent, claim_ammount, invoice_quantity, invoice_value, commission, commission_percent, other_discount_percent, other_discount_amt, upcharge, net_invo_value, country_id, country_code_id, remarks, bl_no, bl_date, bl_rev_date, doc_handover, forwarder_name, etd, feeder_vessel, mother_vessel, etd_destination, eta_destination_place, ic_recieved_date, inco_term, inco_term_place, shipping_bill_n, shipping_mode, total_carton_qnty, port_of_entry, port_of_loading, port_of_discharge, actual_shipment_date, ex_factory_date, freight_amnt_by_supllier, freight_amm_by_buyer, category_no, hs_code, ship_bl_date, advice_date, advice_amount, paid_amount, color_size_rate, gsp_co_no, gsp_co_no_date, cons_per_pcs, cargo_delivery_to, place_of_delivery, insentive_applicable, main_mark, side_mark, net_weight, gross_weight, cbm_qnty, delv_no, consignee, notifying_party, item_description, co_no, co_date, container_no, export_invoice_qty_source, carton_net_weight, carton_gross_weight, stamp_value, lc_for, adhesive_stamp_value, payment_method, cform_qnty, cform_rate, cform_amt, atsite_discount_persent, atsite_discount_amt, is_posted_account ,total_measurment,ngc_id,booking_no,shipment_to,shipment_terms,ud_no,composition,supplied_goods,supplied_goods_amt,ad_payment_adjustment,ad_payment_adjustment_amt
	from com_export_invoice_ship_mst mst WHERE id=$data";
	//echo $sql;
	$data_array=sql_select($sql);
	$gmst_delivery_check=return_field_value("is_posted_account","pro_ex_factory_mst","invoice_no=$data and status_active=1","is_posted_account");

	
	$bl_invoice = "SELECT ID,BL_NO,BL_DATE, INVOICE_ID,FORWARDER_NAME FROM bl_charge WHERE  invoice_id = $data and BL_NO  is not null and status_active=1 and is_deleted = 0 order by id desc";
	//echo $bl_invoice; die();
	$bl_invoice_array=sql_select($bl_invoice);
	$blArr  = array();
	foreach ($bl_invoice_array as $row)
	{
		$blArr[$row['INVOICE_ID']]['BL_NO'] = $row['BL_NO'];
		$blArr[$row['INVOICE_ID']]['BL_DATE'] = $row['BL_DATE'];
		$blArr[$row['INVOICE_ID']]['FORWARDER_NAME'] = $row['FORWARDER_NAME'];
	}

 	foreach ($data_array as $row)
	{
		$export_item_category=0;
		if($row[csf('is_lc')]==1)
		{
			//$lc_sc_no=return_field_value("export_lc_no","com_export_lc","id=".$row[csf('lc_sc_id')]);
			$sql_lc=sql_select("select export_lc_no, export_item_category from com_export_lc where id=".$row[csf('lc_sc_id')]);
			$lc_sc_no=$sql_lc[0][csf("export_lc_no")];
			$export_item_category=$sql_lc[0][csf("export_item_category")];
		}
		else
		{
			$lc_sc_no=return_field_value("contract_no","com_sales_contract","id=".$row[csf('lc_sc_id')]);
		}
		$additional_info=$row[csf("cargo_delivery_to")].'_'.$row[csf("place_of_delivery")].'_'.$row[csf("main_mark")].'_'.$row[csf("side_mark")].'_'.$row[csf("net_weight")].'_'.$row[csf("gross_weight")].'_'.$row[csf("cbm_qnty")].'_'.$row[csf("delv_no")].'_'.$row[csf("consignee")].'_'.$row[csf("notifying_party")].'_'.$row[csf("item_description")].'_'.$row[csf("total_measurment")].'_'.$row[csf("ngc_id")].'_'.$row[csf("booking_no")].'_'.$row[csf("shipment_to")].'_'.$row[csf("shipment_terms")].'_'.$row[csf("ud_no")].'_'.$row[csf("composition")];


		/*if($row[csf("place_of_delivery")]!="")
		{
			$additional_info.='_'.$row[csf("place_of_delivery")];
		}*/

		if($row[csf('exp_form_date')]=='0000-00-00' || $row[csf('exp_form_date')]=='') $exp_form_date=""; else $exp_form_date=change_date_format($row[csf("exp_form_date")]);
		if($row[csf('bl_date')]=='0000-00-00' || $row[csf('bl_date')]=='') $bl_date=""; else $bl_date=change_date_format($row[csf("bl_date")]);
		
		if($row[csf('bl_rev_date')]=='0000-00-00' || $row[csf('bl_rev_date')]=='') $bl_rev_date=""; else $bl_rev_date=change_date_format($row[csf("bl_rev_date")]);
		if($row[csf('doc_handover')]=='0000-00-00' || $row[csf('doc_handover')]=='') $doc_handover=""; else $doc_handover=change_date_format($row[csf("doc_handover")]);
		if($row[csf('etd')]=='0000-00-00' || $row[csf('etd')]=='') $etd=""; else $etd=change_date_format($row[csf("etd")]);
		if($row[csf('ic_recieved_date')]=='0000-00-00' || $row[csf('ic_recieved_date')]=='') $ic_recieved_date=""; else $ic_recieved_date=change_date_format($row[csf("ic_recieved_date")]);
		if($row[csf('etd_destination')]=='0000-00-00' || $row[csf('etd_destination')]=='') $etd_destination=""; else $etd_destination=change_date_format($row[csf("etd_destination")]);
		if($row[csf('ship_bl_date')]=='0000-00-00' || $row[csf('ship_bl_date')]=='') $ship_bl_date=""; else $ship_bl_date=change_date_format($row[csf("ship_bl_date")]);
		if($row[csf('actual_shipment_date')]=='0000-00-00' || $row[csf('actual_shipment_date')]=='') $actual_shipment_date=""; else $actual_shipment_date=change_date_format($row[csf("actual_shipment_date")]);
		if($row[csf('ex_factory_date')]=='0000-00-00' || $row[csf('ex_factory_date')]=='') $ex_factory_date=""; else $ex_factory_date=change_date_format($row[csf("ex_factory_date")]);
		if($row[csf('total_carton_qnty')]=='0') $total_carton_qnty=""; else $total_carton_qnty=$row[csf("total_carton_qnty")];
		if($row[csf('freight_amnt_by_supllier')]=='0') $freight_amnt_by_supllier=""; else $freight_amnt_by_supllier=$row[csf("freight_amnt_by_supllier")];
		if($row[csf('freight_amm_by_buyer')]=='0') $freight_amm_by_buyer=""; else $freight_amm_by_buyer=$row[csf("freight_amm_by_buyer")];

		if($row[csf('advice_date')]=='0000-00-00' || $row[csf('advice_date')]=='') $advice_date=""; else $advice_date=change_date_format($row[csf("advice_date")]);
		if($row[csf('advice_amount')]=='0') $advice_amount=""; else $advice_amount=$row[csf("advice_amount")];
		if($row[csf('paid_amount')]=='0') $paid_amount=""; else $paid_amount=$row[csf("paid_amount")];
		if($row[csf('gsp_co_no_date')]=='0000-00-00' || $row[csf('gsp_co_no_date')]=='') $gsp_co_no_date=""; else $gsp_co_no_date=change_date_format($row[csf("gsp_co_no_date")]);
		if($row[csf('co_date')]=='0000-00-00' || $row[csf('co_date')]=='') $co_date=""; else $co_date=change_date_format($row[csf("co_date")]);

		echo "document.getElementById('txt_lc_sc_no').value 		= '".$lc_sc_no."';\n";
		//echo "document.getElementById('export_invoice_qty_source').value 		= '".$row[csf("lc_sc_id")]."';\n";
		echo "document.getElementById('lc_sc_id').value 			= '".$row[csf("lc_sc_id")]."';\n";
		echo "document.getElementById('is_lc_sc').value 			= '".$row[csf("is_lc")]."';\n";
		echo "document.getElementById('import_btb').value 			= '".$row[csf("import_btb")]."';\n";
		echo "document.getElementById('export_item_category').value	= '".$export_item_category."';\n";
		echo "document.getElementById('cbo_buyer_name').value 		= ".$row[csf("buyer_id")].";\n";
		echo "document.getElementById('txt_invoice_no').value 		= '".$row[csf("invoice_no")]."';\n";
		
		if($gmst_delivery_check)
		{
			echo "$('#txt_invoice_no').attr('disabled',true);\n";
		}
		else
		{
			echo "$('#txt_invoice_no').attr('disabled',false);\n";
		}

		if($blArr[$row[csf("id")]]['BL_NO']!='' && $blArr[$row[csf("id")]]['BL_DATE']!=''){
			echo "$('#bl_date').attr('disabled',true);\n";
			echo "$('#bl_no').attr('disabled',true);\n";
			echo "$('#forwarder_name').attr('disabled',true);\n";
		}
		else{
			echo "$('#bl_date').attr('disabled',false);\n";
			echo "$('#bl_no').attr('disabled',false);\n";
			echo "$('#forwarder_name').attr('disabled',false);\n";
		}
		
		echo "document.getElementById('txt_invoice_date').value 	= '".change_date_format($row[csf("invoice_date")])."';\n";
		echo "document.getElementById('txt_exp_form_no').value 		= '".$row[csf("exp_form_no")]."';\n";
		echo "document.getElementById('txt_exp_form_date').value 	= '".$exp_form_date."';\n";
		echo "document.getElementById('cbo_location').value 		= '".$row[csf("location_id")]."';\n";
		echo "document.getElementById('cbo_country').value 			= '".$row[csf("country_id")]."';\n";
		echo "load_drop_down( 'requires/export_information_entry_controller', '".$row[csf("country_id")]."', 'load_drop_down_country_code', 'country_code_td' );\n";
		echo "document.getElementById('cbo_country_code').value 	= '".$row[csf("country_code_id")]."';\n";
		echo "document.getElementById('txt_remarks').value 			= '".$row[csf("remarks")]."';\n";
		echo "document.getElementById('cbo_lc_for').value 			= '".$row[csf("lc_for")]."';\n";
		echo "document.getElementById('cbo_payment_method').value 			= '".$row[csf("payment_method")]."';\n";


		echo "document.getElementById('txt_invoice_val').value 		= '".$row[csf("invoice_value")]."';\n";
		echo "document.getElementById('txt_discount').value 		= '".$row[csf("discount_in_percent")]."';\n";
		echo "document.getElementById('txt_discount_ammount').value = '".$row[csf("discount_ammount")]."';\n";
		echo "document.getElementById('txt_bonus').value 			= '".$row[csf("bonus_in_percent")]."';\n";
		echo "document.getElementById('txt_bonus_ammount').value 	= '".$row[csf("bonus_ammount")]."';\n";
		echo "document.getElementById('txt_claim').value 			= '".$row[csf("claim_in_percent")]."';\n";
		echo "document.getElementById('txt_claim_ammount').value 	= '".$row[csf("claim_ammount")]."';\n";
		echo "document.getElementById('txt_invo_qnty').value 		= '".$row[csf("invoice_quantity")]."';\n";


		echo "document.getElementById('txt_commission').value 		= '".$row[csf("commission_percent")]."';\n";
		echo "document.getElementById('txt_commission_amt').value 	= '".$row[csf("commission")]."';\n";
		echo "document.getElementById('txt_other_discount').value 	= '".$row[csf("other_discount_percent")]."';\n";
		echo "document.getElementById('txt_other_discount_amt').value 	= '".$row[csf("other_discount_amt")]."';\n";
		echo "document.getElementById('txt_upcharge').value 		= '".$row[csf("upcharge")]."';\n";
		echo "document.getElementById('txt_net_invo_val').value 	= '".$row[csf("net_invo_value")]."';\n";
		
		echo "document.getElementById('txt_cForm_qnty').value 		= '".$row[csf("cform_qnty")]."';\n";
		echo "document.getElementById('txt_cForm_rate').value 		= '".$row[csf("cform_rate")]."';\n";
		echo "document.getElementById('txt_cForm_amt').value 		= '".$row[csf("cform_amt")]."';\n";
		echo "document.getElementById('txt_atsite_discount_percent').value = '".$row[csf("atsite_discount_persent")]."';\n";
		echo "document.getElementById('txt_atsite_discount_amt').value 		= '".$row[csf("atsite_discount_amt")]."';\n";

		if($blArr[$row[csf("id")]]['BL_NO']!='' && $blArr[$row[csf("id")]]['BL_DATE']!=''){
			echo "document.getElementById('bl_no').value				= '".$blArr[$row[csf("id")]]['BL_NO']."';\n";
			echo "document.getElementById('bl_date').value 				= '".change_date_format($blArr[$row[csf("id")]]['BL_DATE'])."';\n";
			echo "document.getElementById('forwarder_name').value		= '".$blArr[$row[csf("id")]]['FORWARDER_NAME']."';\n";
		}
		else{
			echo "document.getElementById('bl_no').value				= '".$row[csf("bl_no")]."';\n";
			echo "document.getElementById('bl_date').value 				= '".change_date_format($row[csf("bl_date")])."';\n";
			echo "document.getElementById('forwarder_name').value 		= '".$row[csf("forwarder_name")]."';\n";
		}

		echo "document.getElementById('bl_rev_date').value 			= '".$bl_rev_date."';\n";
		echo "document.getElementById('doc_handover').value 		= '".$doc_handover."';\n";
		echo "document.getElementById('etd').value 					= '".$etd."';\n";
		echo "document.getElementById('feeder_vessel').value 		= '".$row[csf("feeder_vessel")]."';\n";
		echo "document.getElementById('mother_vessel').value 		= '".$row[csf("mother_vessel")]."';\n";
		echo "document.getElementById('etd_destination').value		= '".$etd_destination."';\n";
		echo "document.getElementById('txt_eta_destination').value	= '".$row[csf("eta_destination_place")]."';\n";
		echo "document.getElementById('ic_recieved_date').value 	= '".$ic_recieved_date."';\n";
		echo "document.getElementById('inco_term').value			= '".$row[csf("inco_term")]."';\n";
		echo "document.getElementById('inco_term_place').value 		= '".$row[csf("inco_term_place")]."';\n";
		echo "document.getElementById('shipping_bill_no').value 	= '".$row[csf("shipping_bill_n")]."';\n";
		echo "document.getElementById('ship_bl_date').value 		= '".$ship_bl_date."';\n";
		echo "document.getElementById('port_of_entry').value 		= '".$row[csf("port_of_entry")]."';\n";
		echo "document.getElementById('port_of_loading').value 		= '".$row[csf("port_of_loading")]."';\n";
		echo "document.getElementById('port_of_discharge').value 	= '".$row[csf("port_of_discharge")]."';\n";
		echo "document.getElementById('shipping_mode').value 		= '".$row[csf("shipping_mode")]."';\n";
		echo "document.getElementById('freight_amnt_supplier').value= '".$freight_amnt_by_supllier."';\n";
		echo "document.getElementById('ex_factory_date').value 		= '".$ex_factory_date."';\n";
		echo "document.getElementById('actual_shipment_date').value	= '".$actual_shipment_date."';\n";
		echo "document.getElementById('freight_amnt_buyer').value 	= '".$freight_amm_by_buyer."';\n";
		echo "document.getElementById('total_carton_qnty').value 	= '".$total_carton_qnty."';\n";
		echo "document.getElementById('txt_category_no').value 		= '".$row[csf("category_no")]."';\n";
		echo "document.getElementById('txt_hs_code').value 			= '".$row[csf("hs_code")]."';\n";

		echo "document.getElementById('txt_advice_date').value 		= '".$advice_date."';\n";
		echo "document.getElementById('txt_advice_amnt').value 		= '".$advice_amount."';\n";
		echo "document.getElementById('txt_paid_amnt').value 		= '".$paid_amount."';\n";
		echo "document.getElementById('txt_gsp_co').value 			= '".$row[csf("gsp_co_no")]."';\n";
		echo "document.getElementById('txt_gsp_co_date').value 		= '".$gsp_co_no_date."';\n";
		echo "document.getElementById('txt_co_no').value 			= '".$row[csf("co_no")]."';\n";
		echo "document.getElementById('txt_co_date').value 			= '".$co_date."';\n";
		echo "document.getElementById('txt_container_no').value 	= '".$row[csf("container_no")]."';\n";
		echo "document.getElementById('txt_stamp_value').value   	= '".$row[csf("stamp_value")]."';\n";
		echo "document.getElementById('cbo_incentive').value 		= '".$row[csf("insentive_applicable")]."';\n";
		echo "document.getElementById('txt_cons').value 			= '".$row[csf("cons_per_pcs")]."';\n"; 
		echo "document.getElementById('txt_net_weight').value 		= '".$row[csf("carton_net_weight")]."';\n";
		echo "document.getElementById('txt_gross_weight').value 	= '".$row[csf("carton_gross_weight")]."';\n";
		echo "document.getElementById('txt_adhesive_stamp_value').value = '".$row[csf("adhesive_stamp_value")]."';\n";
		echo "document.getElementById('txt_buyer_supplied_goods').value = '".$row[csf("supplied_goods")]."';\n";
		echo "document.getElementById('txt_buyer_supplied_goods_amount').value = '".$row[csf("supplied_goods_amt")]."';\n";
		echo "document.getElementById('txt_advance_payment_adjustment').value = '".$row[csf("ad_payment_adjustment")]."';\n";
		echo "document.getElementById('txt_advance_payment_adjustment_amount').value = '".$row[csf("ad_payment_adjustment_amt")]."';\n";
	

		echo "document.getElementById('additional_info').value 		= '".$additional_info."';\n";

		if($row[csf('import_btb')]==1)
		{
			echo "$('#chk_color_size_rate').removeAttr('checked','checked');\n";
			echo "$('#chk_color_size_rate').attr('disabled','disabled');\n";
		}
		else
		{
			if($row[csf("color_size_rate")]==1)
			{
				echo "$('#chk_color_size_rate').attr('checked','checked');\n";
			}
			else
			{
				echo "$('#chk_color_size_rate').removeAttr('checked','checked');\n";
			}
		}

		echo "document.getElementById('update_id').value 			= '".$row[csf("id")]."';\n";
		echo "set_button_status(1, '".$_SESSION['page_permission']."', 'fnc_export_information_entry',1);\n";
		echo "set_button_status(1, '".$_SESSION['page_permission']."', 'fnc_export_information_entry_shipping_info',2);\n";

		exit();
	}
}

if ($action=="save_update_delete_mst")
{
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process ));

	$additional_info=explode("_",str_replace("'", '',$additional_info));

	if ($operation==0)  // Insert Here
	{
		$con = connect();
		if($db_type==0)
		{
			mysql_query("BEGIN");
		}

		if ($db_type==2) $app_nes_setup_date=change_date_format(date('d-m-Y'), "", "",1);
		else if ($db_type==0) $app_nes_setup_date=change_date_format(date('d-m-Y'),'yyyy-mm-dd');

		if(str_replace("'", '',$is_lc_sc)==1)
		{
			if (is_duplicate_field( "invoice_no", "com_export_invoice_ship_mst a, com_export_lc b", "a.lc_sc_id=b.id and a.is_lc=1 and a.invoice_no=$txt_invoice_no and a.benificiary_id=$cbo_beneficiary_name and b.buyer_name=$cbo_buyer_name and b.lien_bank=$cbo_lien_bank and b.id=$lc_sc_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0" )==1)
			{
				echo "11**0";disconnect($con);
				die;
			}

			$lc_sc_approved=return_field_value("approved","com_export_lc","id=$lc_sc_id","approved");
			$approval_status="select approval_need, allow_partial from approval_setup_dtls where mst_id=(select id from approval_setup_mst where company_id=$cbo_beneficiary_name and setup_date=(select max(setup_date) from approval_setup_mst where setup_date <= '$app_nes_setup_date' and company_id=$cbo_beneficiary_name)) and page_id=46 and status_active=1 and is_deleted=0";
			$app_need_setup=sql_select($approval_status);
			$approval_need=$app_need_setup[0][csf("approval_need")];
		}
		else if(str_replace("'", '',$is_lc_sc)==2)
		{
			if (is_duplicate_field( "invoice_no", "com_export_invoice_ship_mst a, com_sales_contract b", "a.lc_sc_id=b.id and a.is_lc=2 and a.invoice_no=$txt_invoice_no and a.benificiary_id=$cbo_beneficiary_name and b.buyer_name=$cbo_buyer_name and b.lien_bank=$cbo_lien_bank and b.id=$lc_sc_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0" )==1)
			{
				echo "11**0";disconnect($con);
				die;
			}

			$lc_sc_approved=return_field_value("approved","com_sales_contract","id=$lc_sc_id","approved");
			$approval_status="select approval_need, allow_partial from approval_setup_dtls where mst_id=(select id from approval_setup_mst where company_id=$cbo_beneficiary_name and setup_date=(select max(setup_date) from approval_setup_mst where setup_date <= '$app_nes_setup_date' and company_id=$cbo_beneficiary_name)) and page_id=47 and status_active=1 and is_deleted=0";
			$app_need_setup=sql_select($approval_status);
			$approval_need=$app_need_setup[0][csf("approval_need")];
		}

		if ($approval_need==1 && ($lc_sc_approved!=1 && $lc_sc_approved!=3)){
			echo "50**0";disconnect($con);
			die;
		}

		$flag=1;
		$id=return_next_id( "id", "com_export_invoice_ship_mst", 1 ) ;

		$field_array="id, invoice_no, invoice_date, buyer_id, location_id, benificiary_id, is_lc, lc_sc_id, exp_form_no, exp_form_date, discount_in_percent, discount_ammount, bonus_in_percent, bonus_ammount, claim_in_percent, claim_ammount, invoice_quantity, invoice_value, commission_percent, commission, other_discount_percent, other_discount_amt, upcharge, net_invo_value, country_id, country_code_id, remarks, color_size_rate, import_btb, cargo_delivery_to ,place_of_delivery, main_mark, side_mark, net_weight, gross_weight, cbm_qnty,delv_no,consignee,notifying_party,item_description,total_measurment,ngc_id,booking_no,shipment_to,shipment_terms,ud_no, composition,supplied_goods,supplied_goods_amt,ad_payment_adjustment,ad_payment_adjustment_amt,export_invoice_qty_source,commission_source_export, lc_for,payment_method,inserted_by,insert_date,cform_qnty,cform_rate, cform_amt, atsite_discount_persent, atsite_discount_amt";

		$data_array="(".$id.",".$txt_invoice_no.",".$txt_invoice_date.",".$cbo_buyer_name.",".$cbo_location.",".$cbo_beneficiary_name.",".$is_lc_sc.",".$lc_sc_id.",".$txt_exp_form_no.",".$txt_exp_form_date.",".$txt_discount.",".$txt_discount_ammount.",".$txt_bonus.",".$txt_bonus_ammount.",".$txt_claim.",".$txt_claim_ammount.",".$txt_invo_qnty.",".$txt_invoice_val.",".$txt_commission.",".$txt_commission_amt.",".$txt_other_discount.",".$txt_other_discount_amt.",".$txt_upcharge.",".$txt_net_invo_val.",".$cbo_country.",".$cbo_country_code.",".$txt_remarks.",".$color_size_rate.",".$import_btb.",'".$additional_info[0]."','".$additional_info[1]."','".$additional_info[2]."','".$additional_info[3]."','".$additional_info[4]."','".$additional_info[5]."','".$additional_info[6]."','".$additional_info[7]."','".$additional_info[8]."','".$additional_info[9]."','".$additional_info[10]."','".$additional_info[11]."','".$additional_info[12]."','".$additional_info[13]."','".$additional_info[14]."','".$additional_info[15]."','".$additional_info[16]."','".$additional_info[17]."',".$txt_buyer_supplied_goods.",".$txt_buyer_supplied_goods_amount.",".$txt_advance_payment_adjustment.",".$txt_advance_payment_adjustment_amount.",".$export_invoice_qty_source.",".$commission_source_at_export_invoice.",".$cbo_lc_for.",".$cbo_payment_method.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."',".$txt_cForm_qnty.",".$txt_cForm_rate.",".$txt_cForm_amt.",".$txt_atsite_discount_percent.",".$txt_atsite_discount_amt.")";

		//echo "insert into com_export_invoice_ship_mst (".$field_array.") values ".$data_array;die;
		/*$rID=sql_insert("com_export_invoice_ship_mst",$field_array,$data_array,0);
		if($flag==1)
		{
			if($rID) $flag=1; else $flag=0;
		} */

		$field_array_dtls="id, mst_id, po_breakdown_id, current_invoice_rate, current_invoice_qnty, current_invoice_value, carton_qty, import_btb, production_source, color_size_rate_data, actual_po_infos, inserted_by, insert_date, is_sales, is_service";
		$field_array_actual_po="id, invoice_id, invoice_details_id, wo_po_breakdown_id, wo_po_act_id, country_id, gmts_item_id, po_no, po_qty, inserted_by, insert_date";
		$field_array_color_size_rate="id, invoice_id, invoice_details_id, po_breakdown_id, acc_po_dtls_id, country_id, qnty, rate, amount, inserted_by, insert_date";

		$id_dtls = return_next_id( "id", "com_export_invoice_ship_dtls", 1 );
		$act_id = return_next_id( "id", "export_invoice_act_po" );
		if($tot_row==0)
		{
			
			$data_array_dtls="(".$id_dtls.",".$id.",0,0,".$txt_invo_qnty.",".$txt_invoice_val.",".$import_btb.",0,'','',".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."',0,0)";
		}
		else
		{
			for($j=1;$j<=$tot_row;$j++)
			{
				$po_breakdown_id="order_id_".$j;
				$order_type="order_type_".$j;
				$order_rate="order_rate_".$j;
				$is_service="is_service_".$j;
				$curr_invo_qty="curr_invo_qty_".$j;
				$curr_invo_val="curr_invo_val_".$j;
				$carton_qty="carton_qty_".$j;
				$cbo_production_source="cbo_production_source_".$j;
				$actual_po_infos="actual_po_infos_".$j;
				$colorSize_infos="colorSize_infos_".$j;

				if(str_replace("'",'',$$curr_invo_qty)>0)
				{
					if($data_array_dtls!="") $data_array_dtls.=",";
					$data_array_dtls.="(".$id_dtls.",".$id.",'".$$po_breakdown_id."','".$$order_rate."','".$$curr_invo_qty."','".$$curr_invo_val."','".$$carton_qty."',".$import_btb.",'".$$cbo_production_source."','".$$colorSize_infos."','".$$actual_po_infos."',".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."','".$$order_type."','".$$is_service."')";
					$id_dtls = $id_dtls+1;

					if(str_replace("'", '',$$actual_po_infos)!="")
					{
						$actual_po=explode("**",str_replace("'", '',$$actual_po_infos));

						foreach($actual_po as $value)
						{
							$actual_po_val=explode('=',$value);
							$po_id = $actual_po_val[0];
							$po_qty = $actual_po_val[1];
							$po_num = $actual_po_val[2];
							$country_id = $actual_po_val[3];
							$gmst_item = $actual_po_val[4];

							if($data_array_actual_po!="") $data_array_actual_po.=",";

							$data_array_actual_po.="(".$act_id.",".$id.",".$id_dtls.",".$$po_breakdown_id.",'".$po_id."','".$country_id."','".$gmst_item."','".$po_num."','".$po_qty."',".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."')";
							$act_id=$act_id+1;
						}
					}

					if($color_size_rate==1)
					{
						$colorSize_data=explode("**",str_replace("'", '',$$colorSize_infos));

						foreach($colorSize_data as $value)
						{
							$colorSize_val=explode('=',$value);
							$colorSize_id = $colorSize_val[0];
							$colorSize_qnty = $colorSize_val[1];
							$colorSize_rate = $colorSize_val[2];
							$colorSize_amnt = $colorSize_val[3];
							$accpo_dtls_id = $colorSize_val[4];

							if($color_size_rate_id=="") $color_size_rate_id = return_next_id( "id", "export_invoice_clr_sz_rt" ); else $color_size_rate_id=$color_size_rate_id+1;

							if($data_array_color_size_rate!="") $data_array_color_size_rate.=",";

							$data_array_color_size_rate.="(".$color_size_rate_id.",".$id.",".$id_dtls.",'".$colorSize_id."','".$accpo_dtls_id."',".$cbo_country.",'".$colorSize_qnty."','".$colorSize_rate."','".$colorSize_amnt."',".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."')";

						}
					}
				}
			}
		}
		$rID=$rID2=$rID3=$rID4=$rID7=true;
		//echo "5**insert into com_export_invoice_ship_mst (".$field_array.") values ".$data_array."***".$flag;die;
		$rID=sql_insert("com_export_invoice_ship_mst",$field_array,$data_array,0);
		if($flag==1)
		{
			if($rID) $flag=1; else $flag=0;
		}
		//echo "5**".$flag;die;
		//echo "5**insert into export_invoice_act_po (".$field_array_actual_po.") values ".$data_array_actual_po."***".$flag;die;
		if($data_array_actual_po!="")
		{
			$rID3=sql_insert("export_invoice_act_po",$field_array_actual_po,$data_array_actual_po,0);
			if($flag==1)
			{
				if($rID3) $flag=1; else $flag=0;
			}
		}
		//echo "5**insert into export_invoice_clr_sz_rt (".$field_array_color_size_rate.") values ".$data_array_color_size_rate;oci_rollback($con);disconnect($con);die;
		if($data_array_color_size_rate!="")
		{
			$rID4=sql_insert("export_invoice_clr_sz_rt",$field_array_color_size_rate,$data_array_color_size_rate,0);
			if($flag==1)
			{
				if($rID4) $flag=1; else $flag=0;
			}
		}
		//echo "5**insert into com_export_invoice_ship_dtls (".$field_array_dtls.") values ".$data_array_dtls."***".$flag;die;
		$rID2=sql_insert("com_export_invoice_ship_dtls",$field_array_dtls,$data_array_dtls,1);
		if($flag==1)
		{
			if($rID2) $flag=1; else $flag=0;
		}

		$gateOutflag=true;
		$gateOutIdArr = json_decode(str_replace("'","",$gate_out_id),true);
		$gmtsDelvIdArr = json_decode(str_replace("'","",$gmts_delv_id),true);
		if(str_replace("'","",$export_invoice_qty_source)==2 && count($gateOutIdArr)>0){
			foreach($gateOutIdArr as $gateOutIdrow){
				$rID7=execute_query( "update  inv_gate_out_scan set invoice_id=$id where id=$gateOutIdrow",0);
				if($flag==1)
		        {
					if($rID7) $flag=1; else $flag=0;
		        }
			}
		}

		//echo "10**$rID##$rID2##$rID3##$rID4##$rID7";oci_rollback($con);disconnect($con);die;
		/*oci_rollback($con);*/
		//echo "5**0**0**".$flag."222";die;
		if($db_type==0)
		{
			if($flag==1)
			{
				mysql_query("COMMIT");
				echo "0**".$id."**1";
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
				echo "0**".$id."**1";
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

		/*if (is_duplicate_field( "invoice_no", "pro_ex_factory_mst", "invoice_no=$update_id and status_active=1 and is_deleted=0" )==1)
		{
			echo "101**".$txt_invoice_no;
			die;
		}*/

		if($db_type==0)
		{
			mysql_query("BEGIN");

			$sql="select a.id as id, a.bank_ref_no as bill_no, 'Submission' as type from com_export_doc_submission_mst a, com_export_doc_submission_invo b where a.id=b.doc_submission_mst_id and b.invoice_id=$update_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 group by a.id, a.bank_ref_no
				union all
				select id, '' as bill_no, 'Proceed Realization' as type from com_export_proceed_realization where invoice_bill_id=$update_id and is_invoice_bill=2 and status_active=1 and is_deleted=0";
		}
		else
		{
			$sql="select a.id as id, a.bank_ref_no as bill_no, 'Submission' as type from com_export_doc_submission_mst a, com_export_doc_submission_invo b where a.id=b.doc_submission_mst_id and b.invoice_id=$update_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 group by a.id, a.bank_ref_no
				union all
				select id, CAST (NULL AS NVARCHAR2(2)) as bill_no, 'Proceed Realization' as type from com_export_proceed_realization where invoice_bill_id=$update_id and is_invoice_bill=2 and status_active=1 and is_deleted=0";
		}
		$data=sql_select($sql);
		if(count($data)>0)
		{
			if($data[0][csf('bill_no')]!='') $invoice_realization=$data[0][csf('type')]."(System Id:".$data[0][csf('id')].", Bill No: ".$data[0][csf('bill_no')].")";
			else $invoice_realization=$data[0][csf('type')]."(System Id: ".$data[0][csf('id')].")";

			echo "14**".$invoice_realization."**1";disconnect($con);
			die;
		}

		if ($db_type==2) $app_nes_setup_date=change_date_format(date('d-m-Y'), "", "",1);
		else if ($db_type==0) $app_nes_setup_date=change_date_format(date('d-m-Y'),'yyyy-mm-dd');

		if(str_replace("'", '',$is_lc_sc)==1)
		{
			if (is_duplicate_field( "invoice_no", "com_export_invoice_ship_mst a, com_export_lc b", "a.lc_sc_id=b.id and a.is_lc=1 and a.invoice_no=$txt_invoice_no and a.benificiary_id=$cbo_beneficiary_name and b.buyer_name=$cbo_buyer_name and b.lien_bank=$cbo_lien_bank and b.id=$lc_sc_id and a.id<>$update_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0" )==1)
			{
				echo "11**0";disconnect($con);
				die;
			}
			
			$lc_sc_approved=return_field_value("approved","com_export_lc","id=$lc_sc_id","approved");
			$approval_status="select approval_need, allow_partial from approval_setup_dtls where mst_id=(select id from approval_setup_mst where company_id=$cbo_beneficiary_name and setup_date=(select max(setup_date) from approval_setup_mst where setup_date <= '$app_nes_setup_date' and company_id=$cbo_beneficiary_name)) and page_id=46 and status_active=1 and is_deleted=0";
			$app_need_setup=sql_select($approval_status);
			$approval_need=$app_need_setup[0][csf("approval_need")];

		
		}
		else if(str_replace("'", '',$is_lc_sc)==2)
		{
			if (is_duplicate_field( "invoice_no", "com_export_invoice_ship_mst a, com_sales_contract b", "a.lc_sc_id=b.id and a.is_lc=2 and a.invoice_no=$txt_invoice_no and a.benificiary_id=$cbo_beneficiary_name and b.buyer_name=$cbo_buyer_name and b.lien_bank=$cbo_lien_bank and b.id=$lc_sc_id and a.id<>$update_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0" )==1)
			{
				echo "11**0";disconnect($con);
				die;
			}

			$lc_sc_approved=return_field_value("approved","com_sales_contract","id=$lc_sc_id","approved");
			$approval_status="select approval_need, allow_partial from approval_setup_dtls where mst_id=(select id from approval_setup_mst where company_id=$cbo_beneficiary_name and setup_date=(select max(setup_date) from approval_setup_mst where setup_date <= '$app_nes_setup_date' and company_id=$cbo_beneficiary_name)) and page_id=47 and status_active=1 and is_deleted=0";
			$app_need_setup=sql_select($approval_status);
			$approval_need=$app_need_setup[0][csf("approval_need")];
		}

		if ($approval_need==1 && ($lc_sc_approved!=1  && $lc_sc_approved!=3)){
			echo "50**0";disconnect($con);
			die;
		}

		$flag=1;
		$field_array="invoice_no*invoice_date*buyer_id*location_id*benificiary_id*is_lc*lc_sc_id*exp_form_no*exp_form_date*discount_in_percent*discount_ammount*bonus_in_percent*bonus_ammount*claim_in_percent*claim_ammount*invoice_quantity*invoice_value*commission_percent*commission*other_discount_percent*other_discount_amt*upcharge*net_invo_value*country_id*country_code_id*remarks*color_size_rate*import_btb*updated_by*update_date*cargo_delivery_to*place_of_delivery*main_mark*side_mark*net_weight*gross_weight*cbm_qnty*delv_no*consignee*notifying_party*item_description*total_measurment*ngc_id*booking_no*shipment_to*shipment_terms*ud_no*composition*supplied_goods*supplied_goods_amt*ad_payment_adjustment*ad_payment_adjustment_amt*export_invoice_qty_source*commission_source_export*lc_for*payment_method*cform_qnty*cform_rate*cform_amt*atsite_discount_persent*atsite_discount_amt";

		$data_array=$txt_invoice_no."*".$txt_invoice_date."*".$cbo_buyer_name."*".$cbo_location."*".$cbo_beneficiary_name."*".$is_lc_sc."*".$lc_sc_id."*".$txt_exp_form_no."*".$txt_exp_form_date."*".$txt_discount."*".$txt_discount_ammount."*".$txt_bonus."*".$txt_bonus_ammount."*".$txt_claim."*".$txt_claim_ammount."*".$txt_invo_qnty."*".$txt_invoice_val."*".$txt_commission."*".$txt_commission_amt."*".$txt_other_discount."*".$txt_other_discount_amt."*".$txt_upcharge."*".$txt_net_invo_val."*".$cbo_country."*".$cbo_country_code."*".$txt_remarks."*".$color_size_rate."*".$import_btb."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'*'".$additional_info[0]."'*'".$additional_info[1]."'*'".$additional_info[2]."'*'".$additional_info[3]."'*'".$additional_info[4]."'*'".$additional_info[5]."'*'".$additional_info[6]."'*'".$additional_info[7]."'*'".$additional_info[8]."'*'".$additional_info[9]."'*'".$additional_info[10]."'*'".$additional_info[11]."'*'".$additional_info[12]."'*'".$additional_info[13]."'*'".$additional_info[14]."'*'".$additional_info[15]."'*'".$additional_info[16]."'*'".$additional_info[17]."'*".$txt_buyer_supplied_goods."*".$txt_buyer_supplied_goods_amount."*".$txt_advance_payment_adjustment."*".$txt_advance_payment_adjustment_amount."*".$export_invoice_qty_source."*".$commission_source_at_export_invoice."*".$cbo_lc_for."*".$cbo_payment_method."*".$txt_cForm_qnty."*".$txt_cForm_rate."*".$txt_cForm_amt."*".$txt_atsite_discount_percent."*".$txt_atsite_discount_amt."";

		//echo "insert into com_export_invoice_ship_mst (".$field_array.") values ".$data_array;die;
		

		//$field_array_dtls="id, mst_id, po_breakdown_id, current_invoice_rate, current_invoice_qnty, current_invoice_value, production_source, color_size_rate_data, import_btb, actual_po_infos, inserted_by, insert_date";
		$field_array_dtls="id, mst_id, po_breakdown_id, current_invoice_rate, current_invoice_qnty, current_invoice_value,carton_qty, import_btb, production_source, color_size_rate_data, actual_po_infos, inserted_by, insert_date, is_sales, is_service";
		$field_array_actual_po="id, invoice_id, invoice_details_id, wo_po_breakdown_id, wo_po_act_id, country_id, gmts_item_id, po_no, po_qty, inserted_by, insert_date";
		$field_array_color_size_rate="id, invoice_id, invoice_details_id, po_breakdown_id, acc_po_dtls_id, country_id, qnty, rate, amount, inserted_by, insert_date";
		$id_dtls=return_next_id( "id", "com_export_invoice_ship_dtls", 1 ) ;
		$act_id = return_next_id( "id", "export_invoice_act_po" );
		if($tot_row==0)
		{
			$data_array_dtls="(".$id_dtls.",".$update_id.",0,0,".$txt_invo_qnty.",".$txt_invoice_val.",".$import_btb.",0,'','',".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."',0,0)";
		}
		else
		{
			for($j=1;$j<=$tot_row;$j++)
			{
				$po_breakdown_id="order_id_".$j;
				$order_type="order_type_".$j;
				$order_rate="order_rate_".$j;
				$is_service="is_service_".$j;
				$curr_invo_qty="curr_invo_qty_".$j;
				$curr_invo_val="curr_invo_val_".$j;
				$carton_qty_="carton_qty_".$j;
				$cbo_production_source="cbo_production_source_".$j;
				$actual_po_infos="actual_po_infos_".$j;
				$colorSize_infos="colorSize_infos_".$j;

				if(str_replace("'",'',$$curr_invo_qty)>0)
				{
					if($data_array_dtls!="") $data_array_dtls.=",";

					$data_array_dtls.="(".$id_dtls.",".$update_id.",'".$$po_breakdown_id."','".$$order_rate."','".$$curr_invo_qty."','".$$curr_invo_val."','".$$carton_qty_."',".$import_btb.",'".$$cbo_production_source."','".$$colorSize_infos."','".$$actual_po_infos."',".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."','".$$order_type."','".$$is_service."')";
					


					if(str_replace("'", '',$$actual_po_infos)!="")
					{
						$actual_po=explode("**",str_replace("'", '',$$actual_po_infos));

						foreach($actual_po as $value)
						{
							$actual_po_val=explode('=',$value);
							$po_id = $actual_po_val[0];
							$po_qty = $actual_po_val[1];
							$po_num = $actual_po_val[2];
							$country_id = $actual_po_val[3];
							$gmst_item = $actual_po_val[4];

							if($data_array_actual_po!="") $data_array_actual_po.=",";

							$data_array_actual_po.="(".$act_id.",".$update_id.",".$id_dtls.",".$$po_breakdown_id.",'".$po_id."','".$country_id."','".$gmst_item."','".$po_num."','".$po_qty."',".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."')";
							$act_id=$act_id+1;
						}
					}

					if($color_size_rate==1)
					{
						$colorSize_data=explode("**",str_replace("'", '',$$colorSize_infos));

						foreach($colorSize_data as $value)
						{
							$colorSize_val=explode('=',$value);
							$colorSize_id = $colorSize_val[0];
							$colorSize_qnty = $colorSize_val[1];
							$colorSize_rate = $colorSize_val[2];
							$colorSize_amnt = $colorSize_val[3];
							$accpo_dtls_id = $colorSize_val[4];

							if($color_size_rate_id=="") $color_size_rate_id = return_next_id( "id", "export_invoice_clr_sz_rt" ); else $color_size_rate_id=$color_size_rate_id+1;

							if($data_array_color_size_rate!="") $data_array_color_size_rate.=",";

							$data_array_color_size_rate.="(".$color_size_rate_id.",".$update_id.",".$id_dtls.",'".$colorSize_id."','".$accpo_dtls_id."',".$cbo_country.",'".$colorSize_qnty."','".$colorSize_rate."','".$colorSize_amnt."',".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."')";
						}
					}
					$id_dtls = $id_dtls+1;
				}
			}
		}

		//echo "6**".$data_array_dtls;die;
		$rID=$rID2=$rID3=$rID4=$delete_dtls=$delete_actual=$delete_color_size_rate=$gateOutflag=$rID7=true;

		$rID=sql_update("com_export_invoice_ship_mst",$field_array,$data_array,"id",$update_id,0);
		if($flag==1)
		{
			if($rID) $flag=1; else $flag=0;
		}

		$delete_dtls=execute_query( "delete from com_export_invoice_ship_dtls where mst_id=$update_id",0);
		if($flag==1)
		{
			if($delete_dtls) $flag=1; else $flag=0;
		}

		$delete_actual=execute_query( "delete from export_invoice_act_po where invoice_id=$update_id",0);
		if($flag==1)
		{
			if($delete_actual) $flag=1; else $flag=0;
		}

		$delete_color_size_rate=execute_query( "delete from export_invoice_clr_sz_rt where invoice_id=$update_id",0);
		if($flag==1)
		{
			if($delete_color_size_rate) $flag=1; else $flag=0;
		}
		//echo "6**insert into export_invoice_act_po (".$field_array_actual_po.") values ".$data_array_actual_po."***".$flag;die;
		if($data_array_actual_po!="")
		{
			$rID3=sql_insert("export_invoice_act_po",$field_array_actual_po,$data_array_actual_po,0);
			if($flag==1)
			{
				if($rID3) $flag=1; else $flag=0;
			}
		}

		//echo "6**insert into com_export_invoice_ship_dtls (".$field_array_dtls.") values ".$data_array_dtls;die;
		if($data_array_color_size_rate!="")
		{
			$rID4=sql_insert("export_invoice_clr_sz_rt",$field_array_color_size_rate,$data_array_color_size_rate,0);
			if($flag==1)
			{
				if($rID4) $flag=1; else $flag=0;
			}
		}

		$rID2=sql_insert("com_export_invoice_ship_dtls",$field_array_dtls,$data_array_dtls,1);
		if($flag==1)
		{
			if($rID2) $flag=1; else $flag=0;
		}

		$gateOutflag=true;
		$gateOutIdArr = json_decode(str_replace("'","",$gate_out_id),true);
		$gmtsDelvIdArr = json_decode(str_replace("'","",$gmts_delv_id),true);
		if(str_replace("'","",$export_invoice_qty_source)==2 && count($gateOutIdArr)>0){
			$rID6=execute_query( "update  inv_gate_out_scan set invoice_id=0 where invoice_id=$update_id",0);
			foreach($gateOutIdArr as $gateOutIdrow){
				$rID7=execute_query( "update  inv_gate_out_scan set invoice_id=$update_id where id=$gateOutIdrow",0);
				if($flag==1)
		        {
					if($rID7) $flag=1; else $flag=0;
		        }
			}
		}


		//echo "6** $rID ## $delete_dtls ## $delete_actual ## $delete_color_size_rate ## $rID3 ## $rID4 ## $rID2" ;die;
		//echo "10** insert into export_invoice_act_po ($field_array_actual_po) values $data_array_actual_po";die;
		//echo "10**$rID##$rID2##$rID3##$rID4##$delete_dtls##$delete_actual##$delete_color_size_rate";die;

		if($db_type==0)
		{
			if($flag==1)
			{
				mysql_query("COMMIT");
				echo "1**".str_replace("'", '', $update_id)."**1";
			}
			else
			{
				mysql_query("ROLLBACK");
				echo "6**".str_replace("'", '', $update_id)."**1";
			}
		}
		else if($db_type==2 || $db_type==1 )
		{
			if($flag==1)
			{
				oci_commit($con);
				echo "1**".str_replace("'", '', $update_id)."**1";
			}
			else
			{
				oci_rollback($con);
				echo "6**".str_replace("'", '', $update_id)."**1";
			}
		}
		disconnect($con);
		die;
	}
	else if ($operation==2)   // Delete Here
	{
		$con = connect();
		if($db_type==0)
		{
			mysql_query("BEGIN");
		}
		
		if(str_replace("'","",$update_id)=="") { echo "10**";disconnect($con);die; }
 		$id=str_replace("'","",$update_id);

		if ($db_type==2) $app_nes_setup_date=change_date_format(date('d-m-Y'), "", "",1);
		else if ($db_type==0) $app_nes_setup_date=change_date_format(date('d-m-Y'),'yyyy-mm-dd');

		if(str_replace("'", '',$is_lc_sc)==1)
		{			
			$lc_sc_approved=return_field_value("approved","com_export_lc","id=$lc_sc_id","approved");
			$approval_status="select approval_need, allow_partial from approval_setup_dtls where mst_id=(select id from approval_setup_mst where company_id=$cbo_beneficiary_name and setup_date=(select max(setup_date) from approval_setup_mst where setup_date <= '$app_nes_setup_date' and company_id=$cbo_beneficiary_name)) and page_id=46 and status_active=1 and is_deleted=0";
			$app_need_setup=sql_select($approval_status);
			$approval_need=$app_need_setup[0][csf("approval_need")];

		
		}
		else if(str_replace("'", '',$is_lc_sc)==2)
		{
			$lc_sc_approved=return_field_value("approved","com_sales_contract","id=$lc_sc_id","approved");
			$approval_status="select approval_need, allow_partial from approval_setup_dtls where mst_id=(select id from approval_setup_mst where company_id=$cbo_beneficiary_name and setup_date=(select max(setup_date) from approval_setup_mst where setup_date <= '$app_nes_setup_date' and company_id=$cbo_beneficiary_name)) and page_id=47 and status_active=1 and is_deleted=0";
			$app_need_setup=sql_select($approval_status);
			$approval_need=$app_need_setup[0][csf("approval_need")];
		}

		if ($approval_need==1 && $lc_sc_approved!=1){
			echo "50**0";disconnect($con);
			die;
		}
		
		$update_field_arr="updated_by*update_date*status_active*is_deleted";
		$update_data_arr="".$user_id."*'".$pc_date_time."'*0*1";
		$invMst=$invDtls=$invPo=$invClr=true;
		//echo "10** $invMst && $invDtls && $invPo && $invClr = $id";oci_rollback($con);die;
		//echo "10**"."Update com_export_invoice_ship_mst set status_active=0,is_deleted=1,updated_by=$user_id,update_date='$pc_date_time'  where id =$id";oci_rollback($con);die;
		$invoice_sub_id = return_field_value("invoice_id","com_export_doc_submission_invo","invoice_id=".$id." and status_active=1 and is_deleted=0","invoice_id");
		if($invoice_sub_id>0)
		{
			echo "35**Delete Not Allow. This Invoice No Found in Bill"; disconnect($con);die;
		}
		else
		{
			if($id>0)
			{
				$invMst=sql_update("com_export_invoice_ship_mst",$update_field_arr,$update_data_arr,"id",$id,1);
				$invDtls=sql_update("com_export_invoice_ship_dtls",$update_field_arr,$update_data_arr,"mst_id",$id,1);
				$invPo=sql_update("export_invoice_act_po",$update_field_arr,$update_data_arr,"invoice_id",$id,1);
				$invClr=sql_update("export_invoice_clr_sz_rt",$update_field_arr,$update_data_arr,"invoice_id",$id,1);
			}
			//echo "10** $invMst && $invDtls && $invPo && $invClr = $update_id";oci_rollback($con);die;
			if($db_type==0)
			{
				if($invMst && $invDtls && $invPo && $invClr)
				{
					mysql_query("COMMIT");  
					echo "2**".$id;
				}
				else
				{
					mysql_query("ROLLBACK"); 
					echo "10**".$id;
				}
			}
			else if($db_type==2 || $db_type==1 )
			{
				if($invMst && $invDtls && $invPo && $invClr)
				{
					oci_commit($con);  
					echo "2**".$id;
				}
				else
				{
					oci_rollback($con);
					echo "10**".$id;
				}
			}
			disconnect($con);
			die;
		}
	}
}

if ($action=="save_update_delete_dtls")
{
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process ));

	$con = connect();
	if($db_type==0)
	{
		mysql_query("BEGIN");
	}
	//txt_co_no*txt_co_date*txt_eta_destination
	$field_array="bl_no*bl_date*bl_rev_date*doc_handover*forwarder_name*etd*feeder_vessel*mother_vessel*etd_destination*eta_destination_place*ic_recieved_date*inco_term*inco_term_place*shipping_bill_n*shipping_mode*total_carton_qnty*carton_net_weight*carton_gross_weight*port_of_entry*port_of_loading*port_of_discharge*actual_shipment_date*ex_factory_date*freight_amnt_by_supllier*freight_amm_by_buyer*category_no*hs_code*ship_bl_date*advice_date*advice_amount*paid_amount*gsp_co_no*gsp_co_no_date*co_no*co_date*container_no*stamp_value*adhesive_stamp_value*seal_no*cons_per_pcs*insentive_applicable*updated_by*update_date";

	$data_array=$bl_no."*".$bl_date."*".$bl_rev_date."*".$doc_handover."*".$forwarder_name."*".$etd."*".$feeder_vessel."*".$mother_vessel."*".$etd_destination."*".$txt_eta_destination."*".$ic_recieved_date."*".$inco_term."*".$inco_term_place."*".$shipping_bill_no."*".$shipping_mode."*".$total_carton_qnty."*".$txt_net_weight."*".$txt_gross_weight."*".$port_of_entry."*".$port_of_loading."*".$port_of_discharge."*".$actual_shipment_date."*".$ex_factory_date."*".$freight_amnt_supplier."*".$freight_amnt_buyer."*".$txt_category_no."*".$txt_hs_code."*".$ship_bl_date."*".$txt_advice_date."*".$txt_advice_amnt."*".$txt_paid_amnt."*".$txt_gsp_co."*".$txt_gsp_co_date."*".$txt_co_no."*".$txt_co_date."*".$txt_container_no."*".$txt_stamp_value."*".$txt_adhesive_stamp_value."*".$txt_seal_no."*".$txt_cons."*".$cbo_incentive."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'";

	$rID=sql_update("com_export_invoice_ship_mst",$field_array,$data_array,"id",$update_id,1);

	if($operation==0)
	{
		$msg="5"; $button_staus="0";
	}
	else if ($operation==1)
	{
		$msg="6"; $button_staus="1";
	}

	if($db_type==0)
	{
		if($rID)
		{
			mysql_query("COMMIT");
			echo "1**1";
		}
		else
		{
			mysql_query("ROLLBACK");
			echo $msg."**".$button_staus;
		}
	}
	else if($db_type==2 || $db_type==1 )
	{
		if($rID)
		{
			oci_commit($con);
			echo "1**1";
		}
		else
		{
			oci_rollback($con);
			echo $msg."**".$button_staus;
		}
	}

	disconnect($con);
	die;

}

if ($action=="actual_po_info_popup")
{
	echo load_html_head_contents("Actual PO Info", "../../../", 1, 1,'','','');
	extract($_REQUEST);

	?>
	<script>

		function fnc_close()
		{
			var save_string=''; var tot_inv_qnty=0;
			//.not(':first')
			$("#tbl_search tbody").find('tr').not(':first').each(function()
			{
				var poActId=$(this).find('input[name="poActId[]"]').val();
				var invQnty=$(this).find('input[name="invQnty[]"]').val();
				var poNo=$(this).find('input[name="poNo[]"]').val();
				var poCountry=$(this).find('input[name="poCountryId[]"]').val();
				var poGmtItem=$(this).find('input[name="poGmtItemId[]"]').val();
				//alert(invQnty);
				if(invQnty*1>0)
				{
					if(save_string=="")
					{
						save_string=poActId+"="+invQnty+"="+poNo+"="+poCountry+"="+poGmtItem;
					}
					else
					{
						save_string+="**"+poActId+"="+invQnty+"="+poNo+"="+poCountry+"="+poGmtItem;
					}
					tot_inv_qnty+=invQnty*1;
				}
			});

			$('#actual_po_infos').val( save_string );
			$('#tot_actual_po_qnty').val( tot_inv_qnty );

			parent.emailwindow.hide();
		}
    </script>

	</head>

	<body> <!--onLoad="set_values();"-->
	<div align="center">
		<fieldset style="width:1020px">
			<table width="1020" cellspacing="0" cellpadding="0" border="1" rules="all" class="rpt_table" id="tbl_search">
				<thead>
					<th width="30">SL</th>
                    <th width="110">Actual Po No.</th>
                    <th width="100">Country</th>
                    <th width="70">Ship Date</th>
                    <th width="110">Gmts Item</th>
					<th width="70">Po Qty</th>
                    <th width="70">Unit Price</th>
                    <th width="80">Curr. Invoice Qty</th>
                    <th width="80">Curr. Invoice Value</th>
                    <th width="70">Cumu Invoice Qty</th>
                    <th width="70">Inv. Balance Qty</th>
                    <th width="80">Cumu Invoice Value</th>
                    <th>Ex-Factory Qty</th>
				</thead>
                <tbody id="list_view">
                <?
                    $save_string=explode("**",$actual_po_infos); $actual_po_data_array=array(); $i=0;
                    //echo $actual_po_infos;
                    //print_r($save_string);
                    foreach($save_string as $value)
                    {
                        $val=explode("=",$value);
                        $actual_po_data_array[$val[0]]=$val[1];
                    }
                    $country_arr = return_library_array("select id, country_name from lib_country","id","country_name");
					
					$ex_fact_sql="select b.MST_ID, b.COUNTRY_ID, b.GMTS_ITEM, a.EX_FACT_QTY from PRO_EX_FACTORY_ACTUAL_PO_DETAILS a, WO_PO_ACC_PO_INFO_DTLS b where a.ACTUAL_PO_DTLS_ID=b.ID and a.STATUS_ACTIVE=1 and a.IS_DELETED=0 and b.PO_BREAK_DOWN_ID='$order_id'";
					//echo $ex_fact_sql;die;
					$ex_fact_sql_result=sql_select($ex_fact_sql);
					$ex_fact_data=array();
					foreach($ex_fact_sql_result as $value)
					{
						$ex_fact_data[$value["MST_ID"]][$value["COUNTRY_ID"]][$value["GMTS_ITEM"]]+=$value["EX_FACT_QTY"];
					}
					
					$prev_inv_sql="select a.WO_PO_ACT_ID, a.COUNTRY_ID, a.GMTS_ITEM_ID, a.PO_QTY as QNTY from EXPORT_INVOICE_ACT_PO a where a.STATUS_ACTIVE=1 and a.IS_DELETED=0 and a.WO_PO_BREAKDOWN_ID='$order_id'";
					//echo $prev_inv_sql;//die;
					$prev_inv_sql_result=sql_select($prev_inv_sql);
					$prev_inv_data=array();
					foreach($prev_inv_sql_result as $value)
					{
						$prev_inv_data[$value["WO_PO_ACT_ID"]][$value["COUNTRY_ID"]][$value["GMTS_ITEM_ID"]]+=$value["QNTY"];
					}
					
					$sql="select a.ID as ACC_PO_ID, a.PO_BREAK_DOWN_ID, a.ACC_PO_NO, a.ACC_SHIP_DATE, b.COUNTRY_ID, b.GMTS_ITEM, sum(b.PO_QTY) as PO_QTY, sum(b.UNIT_VALUE) as UNIT_VALUE 
					from WO_PO_ACC_PO_INFO a, WO_PO_ACC_PO_INFO_DTLS b 
					where a.id=b.mst_id and a.PO_BREAK_DOWN_ID='$order_id' and a.is_deleted=0 and a.status_active=1 and a.is_deleted=0 and a.status_active=1
					group by a.ID, a.PO_BREAK_DOWN_ID, a.ACC_PO_NO, a.ACC_SHIP_DATE, b.COUNTRY_ID, b.GMTS_ITEM";
					
					//echo $sql;
                    $result=sql_select($sql);
					foreach($result as $row)
					{
						$i++;

						if ($i%2==0)
							$bgcolor="#FFFFFF";
						else
							$bgcolor="#E9F3FF";
						$row['UNIT_PRICE']=$row['UNIT_VALUE']/$row['PO_QTY'];
						$invcQnty=$actual_po_data_array[$row['ACC_PO_ID']];
						
						$inv_balance_qnty=$row['PO_QTY']-$prev_inv_data[$row["ACC_PO_ID"]][$row["COUNTRY_ID"]][$row["GMTS_ITEM"]];
						$cu_qnty=$prev_inv_data[$row["ACC_PO_ID"]][$row["COUNTRY_ID"]][$row["GMTS_ITEM"]];
						$cu_amount=$prev_inv_data[$row["ACC_PO_ID"]][$row["COUNTRY_ID"]][$row["GMTS_ITEM"]]*$row['UNIT_PRICE'];
						$exfact_qnty=$ex_fact_data[$row["ACC_PO_ID"]][$row["COUNTRY_ID"]][$row["GMTS_ITEM"]];
						?>
						<tr id="tr_<? echo $i; ?>" bgcolor="<? echo $bgcolor; ?>">
                        	<td align="center"><? echo $i; ?></td>
                            <td><? echo $row['ACC_PO_NO']; ?></td>
							<td><? echo  $country_arr[$row['COUNTRY_ID']]; ?></td>
                            <td align="center"><? echo change_date_format($row['ACC_SHIP_DATE']); ?></td>
							<td><? echo $garments_item[$row['GMTS_ITEM']];?></td>
							<td><input type="text" id="txtOrderQnty_<? echo $i; ?>" name="txtOrderQnty_<? echo $i; ?>" value="<? echo $row['PO_QTY']; ?>" class="text_boxes_numeric" style="width:60px" disabled></td>
							<td><input type="text" id="txtOrderRate_<? echo $i; ?>" value="<? echo $row['UNIT_PRICE']; ?>" name="txtOrderRate_<? echo $i; ?>" class="text_boxes_numeric" style="width:60px" disabled>
							<td><input type="text" id="invQnty_<? echo $i; ?>" name="invQnty[]" value="<? echo $invcQnty; ?>" class="text_boxes_numeric" style="width:60px" onBlur="calculate(<? echo $i; ?>);"></td>
                            <td><input type="text" id="txtInvcAmount_<? echo $i; ?>" name="txtInvcAmount_<? echo $i; ?>"  value="<? echo $invcAmnt; ?>" class="text_boxes_numeric" style="width:60px" disabled></td>
                            <td><input type="text" id="txtCuInvcQnty_<? echo $i; ?>" name="txtCuInvcQnty_<? echo $i; ?>" value="<? echo $cu_qnty; ?>" class="text_boxes_numeric" style="width:60px" readonly /></td>
                            <td><input type="text" id="txtInvcBalQnty_<? echo $i; ?>" name="txtInvcBalQnty_<? echo $i; ?>" value="<? echo $inv_balance_qnty; ?>" class="text_boxes_numeric" style="width:60px" readonly /></td>
                            <td><input type="text" id="txtCuInvcAmount_<? echo $i; ?>" name="txtCuInvcAmount_<? echo $i; ?>"  value="<? echo $cu_amount; ?>" class="text_boxes_numeric" style="width:60px" readonly /></td>
							<td>
								<input type="text" id="txtExfactQnty_<? echo $i; ?>" name="txtExfactQnty_<? echo $i; ?>"  value="<? echo $exfact_qnty; ?>" class="text_boxes_numeric" style="width:60px" disabled>
								<input type="hidden" id="poNo_<? echo $i; ?>" name="poNo[]" value="<? echo $row['ACC_PO_NO']; ?>" />
                                <input type="hidden" id="poActId_<? echo $i; ?>" name="poActId[]" value="<? echo $row['ACC_PO_ID']; ?>" />
                                <input type="hidden" id="poQnty_<? echo $i; ?>" name="poQnty[]" value="<? echo $row['PO_QTY']; ?>" />
                                <input type="hidden" id="poCountryId_<? echo $i; ?>" name="poCountryId[]" value="<? echo $row['COUNTRY_ID']; ?>" />
                                <input type="hidden" id="poGmtItemId_<? echo $i; ?>" name="poGmtItemId[]" value="<? echo $row['GMTS_ITEM']; ?>" />
							</td>
						</tr>
						<?
						$totOrderQnty+=$row['PO_QTY'];
						$totInvcQnty+=$invcQnty;
						$totInvcAmount+=$invcAmnt;
					}
					
					
                   /* $sql="select id, acc_po_no, acc_po_qty from wo_po_acc_po_info where po_break_down_id='$order_id' and is_deleted=0 and status_active=1";
                    //echo $sql;
                    $result=sql_select($sql);
                    foreach($result as $row)
                    {
                        $i++;
                        if($i%2==0) $bgcolor="#FFFFFF"; else $bgcolor="#E9F3FF";
                        $invcQnty=$actual_po_data_array[$row[csf('id')]];
                    	?>
                        <tr id="tr_<? echo $i; ?>" bgcolor="<? echo $bgcolor; ?>">
                        
                            <td  width="240">
                            <? echo $row[csf('acc_po_no')]; ?>
                            </td>
                            <td  width="130">
                            <? echo $row[csf('acc_po_qty')]; ?>
                            </td>
                            <td>
                                <input type="text" id="invQnty_<? echo $i; ?>" name="invQnty[]" class="text_boxes_numeric" value="<? echo $invcQnty; ?>" style="width:100px"/>
                            </td>
                            <td style="display: none;">
                            <input type="hidden" id="poNo_<? echo $i; ?>" name="poNo[]" class="text_boxes" style="width:220px" value="<? echo $row[csf('acc_po_no')]; ?>" disabled />
                            <input type="hidden" id="poActId_<? echo $i; ?>" name="poActId[]" value="<? echo $row[csf('id')]; ?>">
                            <input type="hidden" id="poQnty_<? echo $i; ?>" name="poQnty[]" class="text_boxes_numeric" value="<? echo $row[csf('acc_po_qty')]; ?>" style="width:100px" disabled/>

                            </td>
                        </tr>
                    	<?
                    }*/
                    ?>
                </tbody>
              </table>
				<div align="center" style="margin-top:10px">
					<input type="button" class="formbutton" onClick="fnc_close()" value="Close" style="width:100px"/>
					<input type="hidden" id="actual_po_infos" />
					<input type="hidden" id="tot_actual_po_qnty" />
				</div>
		</fieldset>
	</div>
	</body>
	<script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
	</html>
	<script>
		setFilterGrid('list_view',-1);
	</script>
	<?
	exit();
}

if ($action=="colorSize_infos_popup")
{
	echo load_html_head_contents("Color & Size Rate Info", "../../../", 1, 1,'','','');
	extract($_REQUEST);
	

	$variable_setting_rate=return_field_value("cost_heads_status","variable_settings_commercial","company_name='".$company_id."' and  variable_list=18","cost_heads_status");
	
	$variable_setting_ac_po=return_field_value("cm_cost_method","variable_order_tracking","company_name='".$company_id."' and  variable_list=93","cm_cost_method");
	
	$sql_job_order="select a.gmts_item_id, a.set_break_down, a.order_uom, a.total_set_qnty from wo_po_details_master a, wo_po_break_down b where a.job_no=b.job_no_mst and b.id='$order_id'";
	$result_job_order=sql_select($sql_job_order);
	$total_set_qnty=$result_job_order[0][csf("total_set_qnty")]*1;
	
	
	
	$disable_rate='';
	if ($variable_setting_rate==1) $disable_rate='disabled';

	?>
	<script>

		function fnc_close()
		{
			var order_uom=$("#td_order_uom").attr('title');
			var set_qnty=$("#td_set_qnty").attr('title');
			var set_breakdown=$("#td_set_breakdown").attr('title');
			var set_breakdown_ref=set_breakdown.split("__");
			
			var save_string='';
			var tot_row=$("#list_view tbody tr").length;
			var item_wise_qnty_arr={};
			for(var i=1;i<tot_row;i++)
			{
				
				var txtInvcQnty=parseInt($('#txtInvcQnty_'+i).val());
				
				if(txtInvcQnty>0)
				{
					
					var cboGmts=parseInt($('#cboGmts_'+i).val());
					//alert(cboGmts+"="+$('#cboGmts_'+i).val()+"="+i);
					if(!item_wise_qnty_arr[cboGmts])
					{
						item_wise_qnty_arr[cboGmts]=txtInvcQnty*1;
					}
					else
					{
						item_wise_qnty_arr[cboGmts] += txtInvcQnty*1;
					}
					//alert(txtInvcQnty+"="+item_wise_qnty_arr);
					
					if(save_string=="")
					{
						save_string=$('#colorSizeId_'+i).val()+"="+$('#txtInvcQnty_'+i).val()+"="+$('#txtOrderRate_'+i).val()+"="+$('#txtInvcAmount_'+i).val()+"="+$('#accPoDtlsId_'+i).val();
					}
					else
					{
						save_string+="**"+$('#colorSizeId_'+i).val()+"="+$('#txtInvcQnty_'+i).val()+"="+$('#txtOrderRate_'+i).val()+"="+$('#txtInvcAmount_'+i).val()+"="+$('#accPoDtlsId_'+i).val();
					}
				}
			}
			//alert(item_wise_qnty_arr+"="+min_qnty);
			if(set_qnty>1 && set_breakdown_ref.length>1)
			{
				var min_qnty=0; var p=1;
				for(var val in item_wise_qnty_arr)
				{
					if(p==1)min_qnty=item_wise_qnty_arr[val]*1;
					if(item_wise_qnty_arr[val]*1<min_qnty) min_qnty=item_wise_qnty_arr[val]*1;
					p++;
				}
				
				for(var key in set_breakdown_ref)
				{
					var dta_ref=set_breakdown_ref[key].split("_");
					
					var qnty_ratio=Math.floor((item_wise_qnty_arr[dta_ref[0]]/min_qnty)*1);
					//alert(dta_ref[0]+"="+dta_ref[1]+"="+item_wise_qnty_arr[dta_ref[0]]+"="+qnty_ratio);
					if(dta_ref[1]*1 !== qnty_ratio*1 && qnty_ratio >0)
					{
						alert("Invoice Quantity Not Match With Order Ratio."+dta_ref[1]+"="+qnty_ratio); return;
					}
				}
			}
			
			$('#colorSize_infos').val(save_string );
			parent.emailwindow.hide();
		}
		
		function put_po_qnty(row_id)
		{
			var invc_qnty=$('#txtInvcQnty_'+row_id).val()*1;
			var po_qnty=$('#txtInvcBalQnty_'+row_id).val()*1;
			if(invc_qnty==0)
			{
				$('#txtInvcQnty_'+row_id).val(po_qnty);
			}
		}

		function calculate(row_id,type)
		{
			var invc_qnty=$('#txtInvcQnty_'+row_id).val()*1;
			var invc_rate=$('#txtOrderRate_'+row_id).val()*1;
			var invc_amnt=invc_qnty*invc_rate;
			$('#txtInvcAmount_'+row_id).val(invc_amnt);//.toFixed(2)
			if($('#accPoDtlsId_'+row_id).val()*1 >0)
			{
				/*if(type==1)
				{
					var cu_inv_qnty=$('#hdnCuInvcQnty_'+row_id).val()*1;
					var cu_inv_value=((cu_inv_qnty+invc_qnty)*invc_rate)*1;
					$('#txtCuInvcQnty_'+row_id).val(cu_inv_qnty+invc_qnty);
					$('#txtCuInvcAmount_'+row_id).val(cu_inv_value);
				}
				else
				{
					var cu_inv_qnty=$('#txtInvcQnty_'+row_id).val()*1;
					$('#txtCuInvcAmount_'+row_id).val(cu_inv_value);
				}*/
				
				var cu_inv_qnty=$('#txtInvcQnty_'+row_id).val()*1;
				//$('#txtCuInvcAmount_'+row_id).val(cu_inv_value);
				
				calculate_total(1);
			}
			else
			{
				calculate_total(0);
			}
			
			
		}

		function calculate_total(ac_po_level)
		{
			var tot_row=$("#list_view tbody tr").length-1;
			var ddd={ dec_type:2, comma:0, currency:''}
			math_operation( "totInvcQnty", "txtInvcQnty_", "+", tot_row );
			math_operation( "totInvcAmount", "txtInvcAmount_", "+", tot_row, ddd );
			if(ac_po_level==1)
			{
				math_operation( "totCuInvcQnty", "txtCuInvcQnty_", "+", tot_row, ddd );
				math_operation( "totCuInvcAmount", "txtCuInvcAmount_", "+", tot_row, ddd );
			}
			

			var tot_invc_qnty=$('#totInvcQnty').val()*1;
			var tot_invc_amnt=$('#totInvcAmount').val()*1;
			var avg_invc_rate=tot_invc_amnt/tot_invc_qnty;
			
			if(tot_invc_qnty!='')
			{
				$('#InvcAvgRate').val(avg_invc_rate);//.toFixed(2)
			}
			else
			{
				$('#InvcAvgRate').val(0);//.toFixed(2)
			}
		}
    </script>

	</head>

	<body>
	<div align="center">
		<fieldset style="width:890px">
			<table width="450" cellspacing="0" cellpadding="0" border="1" rules="all" class="rpt_table">
				<thead>
					<th>PO Number</th>
					<th>Country</th>
				</thead>
				<tr bgcolor="#EFEFEF">
					<td align="center"><? echo $order_no=return_field_value( "po_number","wo_po_break_down","id='$order_id'"); ?></td>
					<td align="center"><? echo $country_name=return_field_value( "country_name","lib_country","id='$country_id'"); ?></td>
				</tr>
			</table>
			<br />
			<table width="870" cellspacing="0" cellpadding="0" border="0">
				<tr style="font-size:14px; font-weight:bold;">
					<td width="220" id="td_order_uom" title="<? echo $result_job_order[0][csf("order_uom")]; ?>">Oorder UOM : <? echo $unit_of_measurement[$result_job_order[0][csf("order_uom")]];?></td>
					<td width="220" id="td_set_qnty" title="<? echo $result_job_order[0][csf("total_set_qnty")];?>">Total Set Qnty : <? echo $result_job_order[0][csf("total_set_qnty")];?></td>
					<?
					
					$set_breakdown_arr=explode("__",$result_job_order[0][csf("set_break_down")]);
					//print_r($set_breakdown_arr);
					$gmt_item_qnty="";
					$min_ratio=0; $p=1;
					foreach($set_breakdown_arr as $val)
					{
						$data_ref=explode("_",$val);
						if($gmt_item_qnty !="" ) $gmt_item_qnty.=",&nbsp;&nbsp;";
						$gmt_item_qnty.=$garments_item[$data_ref[0]].".&nbsp; Ratio Qnty: ".$data_ref[1];
						$item_qnty_ratios[$data_ref[0]]=$data_ref[1];
						if($p==1) $min_ratio=$data_ref[1]*1;
						if($data_ref[1]*1<$min_ratio) $min_ratio=$data_ref[1]*1;
						$p++;
					}
					//echo $min_ratio."test";
					//print_r($item_qnty_ratios);
					$qnty_ratios="";
					foreach($item_qnty_ratios as $item_id=>$ratio)
					{
						if($item_id>0 && $ratio>0) $qnty_ratios.= $item_id."_".$ratio/$min_ratio."__";
					}
					$qnty_ratios=chop($qnty_ratios,"__");
					?>
					<td id="td_set_breakdown" title="<? echo $qnty_ratios;//$result_job_order[0][csf("set_break_down")];?>">
					<? echo $gmt_item_qnty;?>
					</td>
				</tr>
			</table>
			<br />
            <?
			
			$color_arr=return_library_array( "select id, color_name from lib_color where status_active=1",'id','color_name');
			$size_arr=return_library_array( "select id, size_name from lib_size where status_active=1",'id','size_name');
			$country_arr = return_library_array("select id, country_name from lib_country","id","country_name");
			

			//echo $variable_setting_ac_po.test5;die;$ex_fact_sql="select ACTUAL_PO_DTLS_ID from PRO_EX_FACTORY_ACTUAL_PO_DETAILS where STATUS_ACTIVE=1 and IS_DELETED=0 and ";
			$invoice_id=str_replace("'","",$invoice_id);
			if($variable_setting_ac_po==1)
			{
				//echo $invoice_id.test;
				$save_string=explode("**",$colorSize_infos); $color_size_data_array=array(); $i=0;
				foreach($save_string as $value)
				{
					$value=explode("=",$value);
					$color_size_data_array[$value[4]]['qnty']=$value[1];
					$color_size_data_array[$value[4]]['rate']=$value[2];
					$color_size_data_array[$value[4]]['amnt']=$value[3];
				}
				
				$ex_fact_sql="select a.ACTUAL_PO_DTLS_ID, a.EX_FACT_QTY from PRO_EX_FACTORY_ACTUAL_PO_DETAILS a, WO_PO_ACC_PO_INFO_DTLS b where a.ACTUAL_PO_DTLS_ID=b.ID and a.STATUS_ACTIVE=1 and a.IS_DELETED=0 and b.PO_BREAK_DOWN_ID='$order_id'";
				//echo $ex_fact_sql;die;
				$ex_fact_sql_result=sql_select($ex_fact_sql);
				$ex_fact_data=array();
				foreach($ex_fact_sql_result as $value)
				{
					$ex_fact_data[$value["ACTUAL_PO_DTLS_ID"]]+=$value["EX_FACT_QTY"];
				}
				$inv_conds="";
				if($invoice_id!="") $inv_conds=" and INVOICE_ID<>$invoice_id";
				$prev_inv_sql="select a.ACC_PO_DTLS_ID, a.QNTY from EXPORT_INVOICE_CLR_SZ_RT a, WO_PO_ACC_PO_INFO_DTLS b where a.ACC_PO_DTLS_ID=b.ID and a.STATUS_ACTIVE=1 and a.IS_DELETED=0 and b.PO_BREAK_DOWN_ID='$order_id' $inv_conds";
				//echo $ex_fact_sql;die;
				$prev_inv_sql_result=sql_select($prev_inv_sql);
				$prev_inv_data=array();
				foreach($prev_inv_sql_result as $value)
				{
					$prev_inv_data[$value["ACC_PO_DTLS_ID"]]+=$value["QNTY"];
				}
				//echo $country_id.test;die;
				$country_cond="";
				if(str_replace("'","",$country_id)>0) $country_cond=" and b.COUNTRY_ID=$country_id";
				$sql="select a.ID as ACC_PO_ID, a.PO_BREAK_DOWN_ID, a.ACC_PO_NO, a.ACC_SHIP_DATE, b.ID as ACTUAL_PO_DTLS_ID, b.COUNTRY_ID, b.GMTS_ITEM, b.GMTS_COLOR_ID, b.GMTS_SIZE_ID, b.PO_QTY, b.UNIT_PRICE, b.UNIT_VALUE 
				from WO_PO_ACC_PO_INFO a, WO_PO_ACC_PO_INFO_DTLS b 
				where a.id=b.mst_id and a.PO_BREAK_DOWN_ID='$order_id' and a.is_deleted=0 and a.status_active=1 and a.is_deleted=0 and a.status_active=1 $country_cond";
				//echo $sql;//die;
				$result=sql_select($sql);
				?>
                <table width="1230" cellspacing="0" cellpadding="0" border="1" rules="all" class="rpt_table" id="list_view">
                    <thead>
                        <th width="30">SL.</th>
                        <th width="110">Actual Po No.</th>
                        <th width="100">Country</th>
                        <th width="70">Ship Date</th>
                        <th width="110">Gmts Item</th>
                        <th width="100">Gmts Color</th>
                        <th width="80">Gmts Size</th>
                        <th width="70">Po Qty</th>
    
                        <th width="70">Unit Price</th>
                        <th width="80">Curr. Invoice Qty</th>
                        <th width="80">Curr. Invoice Value</th>
                        <th width="70">Cumu Invoice Qty</th>
                        <th width="70">Inv. Balance Qty</th>
                        <th width="80">Cumu Invoice Value</th>
                        <th>Ex-Factory Qty</th>
                    </thead>
                    <tbody id="dtls_list_view">
                    <?
					//print_r($color_size_data_array);
					foreach($result as $row)
					{
						$i++;

						if ($i%2==0)
							$bgcolor="#FFFFFF";
						else
							$bgcolor="#E9F3FF";

						$invcQnty=$color_size_data_array[$row['ACTUAL_PO_DTLS_ID']]['qnty'];
						$invcRate=$color_size_data_array[$row['ACTUAL_PO_DTLS_ID']]['rate'];
						$invcAmnt=$color_size_data_array[$row['ACTUAL_PO_DTLS_ID']]['amnt'];
						//echo $row['ACTUAL_PO_DTLS_ID']."<br>";
						//if($invcRate=="") $invcRate=$row[csf('order_rate')];
						$inv_balance_qnty=$row['PO_QTY']-($prev_inv_data[$row["ACTUAL_PO_DTLS_ID"]]);
						$cu_amount=$inv_balance_qnty*$row['UNIT_PRICE'];
						?>
						<tr id="tr_<? echo $i; ?>" bgcolor="<? echo $bgcolor; ?>">
                        	<td align="center"><? echo $i; ?></td>
                            <td><? echo $row['ACC_PO_NO']; ?></td>
							<td><? echo $country_arr[$row['COUNTRY_ID']]; ?></td>
                            <td align="center"><? echo change_date_format($row['ACC_SHIP_DATE']); ?></td>
							<td><? echo $garments_item[$row['GMTS_ITEM']];?>
                            <input type="hidden" id="cboGmts_<? echo $i; ?>" name="cboGmts_<? echo $i; ?>" value="<? echo $row['GMTS_ITEM']; ?>" />
                            </td>
							<td><? echo $color_arr[$row['GMTS_COLOR_ID']];?></td>
                            <td><? echo $size_arr[$row['GMTS_SIZE_ID']];?></td>
							<td><input type="text" id="txtOrderQnty_<? echo $i; ?>" name="txtOrderQnty_<? echo $i; ?>" value="<? echo $row['PO_QTY']; ?>" class="text_boxes_numeric" style="width:70px" disabled></td>
							<td><input type="text" id="txtOrderRate_<? echo $i; ?>" value="<? echo $row['UNIT_PRICE']; ?>" name="txtOrderRate_<? echo $i; ?>" class="text_boxes_numeric" style="width:60px" disabled>
							</td>
							<td><input type="text" id="txtInvcQnty_<? echo $i; ?>" name="txtInvcQnty_<? echo $i; ?>" value="<? echo $invcQnty; ?>" class="text_boxes_numeric" style="width:70px" onBlur="calculate(<? echo $i; ?>,1);" onClick="put_po_qnty(<? echo $i; ?>,1);" /></td>
                            <td><input type="text" id="txtInvcAmount_<? echo $i; ?>" name="txtInvcAmount_<? echo $i; ?>"  value="<? echo $invcAmnt; ?>" class="text_boxes_numeric" style="width:70px" disabled></td>
                            <td><input type="text" id="txtCuInvcQnty_<? echo $i; ?>" name="txtCuInvcQnty_<? echo $i; ?>" value="<? echo $prev_inv_data[$row["ACTUAL_PO_DTLS_ID"]]; ?>" class="text_boxes_numeric" style="width:70px" readonly />
                            <input type="hidden" id="hdnCuInvcQnty_<? echo $i; ?>" name="hdnCuInvcQnty_<? echo $i; ?>" value="<? echo $prev_inv_data[$row["ACTUAL_PO_DTLS_ID"]]; ?>" class="text_boxes_numeric" style="width:70px" readonly />
                            </td>
                            <td><input type="text" id="txtInvcBalQnty_<? echo $i; ?>" name="txtInvcBalQnty_<? echo $i; ?>" value="<? echo $inv_balance_qnty; ?>" class="text_boxes_numeric" style="width:70px" readonly /></td>
                            <td><input type="text" id="txtCuInvcAmount_<? echo $i; ?>" name="txtCuInvcAmount_<? echo $i; ?>"  value="<? echo $cu_amount; ?>" class="text_boxes_numeric" style="width:70px" readonly /></td>
							<td>
								<input type="text" id="txtExfactQnty_<? echo $i; ?>" name="txtExfactQnty_<? echo $i; ?>"  value="<? echo $ex_fact_data[$row["ACTUAL_PO_DTLS_ID"]]; ?>" class="text_boxes_numeric" style="width:70px" disabled>
								<input type="hidden" id="colorSizeId_<? echo $i; ?>" value="<? echo $row['ACC_PO_ID']; ?>">
                                <input type="hidden" id="accPoDtlsId_<? echo $i; ?>" value="<? echo $row['ACTUAL_PO_DTLS_ID']; ?>">
							</td>
						</tr>
						<?
						$totOrderQnty+=$row['PO_QTY'];
						$totInvcQnty+=$invcQnty;
						$totInvcAmount+=$invcAmnt;
						$totCuInvcQnty+=$prev_inv_data[$row["ACTUAL_PO_DTLS_ID"]];;
						$totBalInvcAmount+=$inv_balance_qnty;
						$totCuInvcAmount+=$cu_amount;
						$totExfQnty+=$ex_fact_data[$row["ACTUAL_PO_DTLS_ID"]];;
					}
					$avgRate=number_format($totOrderAmount/$totOrderQnty,4,'.','');
					//$avgRate=$totOrderAmount/$totOrderQnty;
					$avgInvcRate=$totInvcAmount/$totInvcQnty;
                    ?>
                    </tbody>
                    <tfoot>
                        <th colspan="7">Total</th>
                        <th><input type="text" id="totOrderQnty" name="totOrderQnty" value="<? echo $totOrderQnty; ?>" class="text_boxes_numeric" style="width:70px" disabled></th>
                        <th>&nbsp;</th>
                        <th style="display:none"><input type="text" id="txtAvgRate" name="txtAvgRate" value="<? echo $avgRate; ?>" class="text_boxes_numeric" style="width:60px" disabled></th>
                        <th style="display:none"><input type="text" id="totOrderAmount" name="totOrderAmount" value="<? echo $totOrderAmount; ?>" class="text_boxes_numeric" style="width:70px" disabled></th>
                        <th><input type="text" id="totInvcQnty" name="totInvcQnty" value="<? echo $totInvcQnty; ?>" class="text_boxes_numeric" style="width:70px" disabled></th>
                        <th style="display:none"><input type="text" id="InvcAvgRate" name="InvcAvgRate" value="<? echo $avgInvcRate; ?>" class="text_boxes_numeric" style="width:60px" disabled></th>
                        <th><input type="text" id="totInvcAmount" name="totInvcAmount"  value="<? echo $totInvcAmount; ?>" class="text_boxes_numeric" style="width:70px" disabled></th>
                        <th><input type="text" id="totCuInvcQnty" name="totCuInvcQnty" value="<? echo $totCuInvcQnty; ?>" class="text_boxes_numeric" style="width:70px" disabled></th>
                        <th><input type="text" id="totInvcBalQnty" name="totInvcBalQnty" value="<? echo $totBalInvcAmount; ?>" class="text_boxes_numeric" style="width:70px" disabled></th>
                        <th><input type="text" id="totCuInvcAmount" name="totCuInvcAmount"  value="<? echo $totCuInvcAmount; ?>" class="text_boxes_numeric" style="width:70px" disabled></th>
                        <th><input type="text" id="totExFactQnty" name="totExFactQnty"  value="<? echo $totExfQnty; ?>" class="text_boxes_numeric" style="width:70px" disabled></th>
                    </tfoot>
                </table>
                <?
			}
			else
			{
				$save_string=explode("**",$colorSize_infos); $color_size_data_array=array(); $i=0;
				foreach($save_string as $value)
				{
					$value=explode("=",$value);
					$color_size_data_array[$value[0]]['qnty']=$value[1];
					$color_size_data_array[$value[0]]['rate']=$value[2];
					$color_size_data_array[$value[0]]['amnt']=$value[3];
				}
				$sql="select id, item_number_id, article_number, size_number_id, color_number_id, order_quantity, order_rate, order_total from wo_po_color_size_breakdown where po_break_down_id='$order_id' and country_id='$country_id' and is_deleted=0 and status_active=1";
				//echo $sql;
				$result=sql_select($sql);
				?>
                <table width="870" cellspacing="0" cellpadding="0" border="1" rules="all" class="rpt_table" id="list_view">
                    <thead>
                        <th width="140">Gmts Item</th>
                        <th width="80">Article No.</th>
                        <th width="100">Color</th>
                        <th width="70">Size</th>
                        <th width="80">Order Qnty</th>
    
                        <th width="70">Rate</th>
                        <th width="80">Amount</th>
                        <th width="80">Invoice Qnty</th>
                        <th width="70">Invoice Rate</th>
                        <th>Invoice Amount</th>
                    </thead>
                    <tbody id="dtls_list_view">
                    <?
                        
                        foreach($result as $row)
                        {
                            $i++;
    
                            if ($i%2==0)
                                $bgcolor="#FFFFFF";
                            else
                                $bgcolor="#E9F3FF";
    
                            $invcQnty=$color_size_data_array[$row[csf('id')]]['qnty'];
                            $invcRate=$color_size_data_array[$row[csf('id')]]['rate'];
                            $invcAmnt=$color_size_data_array[$row[csf('id')]]['amnt'];
    
                            if($invcRate=="") $invcRate=$row[csf('order_rate')];
                        ?>
                            <tr id="tr_<? echo $i; ?>" bgcolor="<? echo $bgcolor; ?>">
                                <td width="140"><font style="display:none"><? echo $garments_item[$row[csf('item_number_id')]];?></font>
                                    <?  echo create_drop_down( "cboGmts_".$i, 132, $garments_item,"", 0, '','', '','1',$row[csf('item_number_id')]); ?>
                                </td>
                                <td width="80"><font style="display:none"><? echo $row[csf('article_number')];?></font>
                                    <input type="text" id="txtArticleno_<? echo $i; ?>" name="txtArticleno_<? echo $i; ?>" value="<? echo $row[csf('article_number')]; ?>" class="text_boxes" style="width:70px" disabled>
                                </td>
                                <td width="100"><font style="display:none"><? echo $color_arr[$row[csf('color_number_id')]];?></font>
                                    <input type="text" id="txtColor_<? echo $i;?>" name="txtColor_<? echo $i;?>" value="<? echo $color_arr[$row[csf('color_number_id')]]; ?>" class="text_boxes" style="width:90px" disabled>
                                </td>
                                <td width="70"><font style="display:none"><? echo $size_arr[$row[csf('size_number_id')]];?></font>
                                    <input type="text" id="txtSize_<? echo $i; ?>" name="txtSize_<? echo $i; ?>" value="<? echo $size_arr[$row[csf('size_number_id')]]; ?>" class="text_boxes" style="width:55px" disabled>
                                </td>
                                <td width="80">
                                    <input type="text" id="txtOrderQnty_<? echo $i; ?>" name="txtOrderQnty_<? echo $i; ?>" value="<? echo $row[csf('order_quantity')]; ?>" class="text_boxes_numeric" style="width:70px" disabled>
                                </td>
                                <td width="70">
                                    <input type="text" id="txtOrderRate_<? echo $i; ?>" value="<? echo $row[csf('order_rate')]; ?>" name="txtOrderRate_<? echo $i; ?>" class="text_boxes_numeric" style="width:60px" disabled>
                                </td>
                                <td width="80">
                                    <input type="text" id="txtOrderAmount_<? echo $i; ?>" name="txtOrderAmount_<? echo $i; ?>"  value="<? echo $row[csf('order_total')]; ?>" class="text_boxes_numeric" style="width:70px" disabled>
                                </td>
                                <td width="80">
                                    <input type="text" id="txtInvcQnty_<? echo $i; ?>" name="txtInvcQnty_<? echo $i; ?>" value="<? echo $invcQnty; ?>" class="text_boxes_numeric" style="width:70px" onBlur="calculate(<? echo $i; ?>,2);">
                                </td>
                                <td width="70">
                                    <input type="text" id="txtInvcRate_<? echo $i; ?>" name="txtInvcRate_<? echo $i; ?>" value="<? echo $invcRate; ?>" class="text_boxes_numeric" <? echo $disable_rate; ?> style="width:60px" readonly>
                                </td>
                                <td>
                                    <input type="text" id="txtInvcAmount_<? echo $i; ?>" name="txtInvcAmount_<? echo $i; ?>"  value="<? echo $invcAmnt; ?>" class="text_boxes_numeric" style="width:70px" disabled>
                                    <input type="hidden" id="colorSizeId_<? echo $i; ?>" value="<? echo $row[csf('id')]; ?>">
                                    <input type="hidden" id="accPoDtlsId_<? echo $i; ?>" value="0">
                                </td>
                            </tr>
                        <?
                            $totOrderQnty+=$row[csf('order_quantity')];
                            $totOrderAmount+=$row[csf('order_total')];
                            $totInvcQnty+=$invcQnty;
                            $totInvcAmount+=$invcAmnt;
                        }
                        $avgRate=number_format($totOrderAmount/$totOrderQnty,4,'.','');
                        //$avgRate=$totOrderAmount/$totOrderQnty;
                        $avgInvcRate=$totInvcAmount/$totInvcQnty;
                    ?>
                    </tbody>
                    <tfoot>
                        <th colspan="4">Total</th>
                        <th>
                            <input type="text" id="totOrderQnty" name="totOrderQnty" value="<? echo $totOrderQnty; ?>" class="text_boxes_numeric" style="width:70px" disabled>
                        </th>
                        <th>
                            <input type="text" id="txtAvgRate" name="txtAvgRate" value="<? echo $avgRate; ?>" class="text_boxes_numeric" style="width:60px" disabled>
                        </th>
                        <th>
                            <input type="text" id="totOrderAmount" name="totOrderAmount" value="<? echo $totOrderAmount; ?>" class="text_boxes_numeric" style="width:70px" disabled>
                        </th>
                        <th>
                            <input type="text" id="totInvcQnty" name="totInvcQnty" value="<? echo $totInvcQnty; ?>" class="text_boxes_numeric" style="width:70px" disabled>
                        </th>
                        <th>
                            <input type="text" id="InvcAvgRate" name="InvcAvgRate" value="<? echo $avgInvcRate; ?>" class="text_boxes_numeric" style="width:60px" disabled>
                        </th>
                        <th>
                            <input type="text" id="totInvcAmount" name="totInvcAmount"  value="<? echo $totInvcAmount; ?>" class="text_boxes_numeric" style="width:70px" disabled>
                        </th>
                    </tfoot>
                </table>
                <?
			}
			?>
			
			<div style="width:880px;margin-top:10px" align="center">
				<input type="button" class="formbutton" onClick="fnc_close()" value="Close" style="width:100px"/>
				<input type="hidden" id="colorSize_infos" />
				<input type="hidden" id="total_set_qnty" value="<? echo $total_set_qnty;?>" />
			</div>
		</fieldset>
	</div>
	</body>
	<script>
		setFilterGrid('dtls_list_view',-1);
	</script>
	<script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
	</html>
	<?
	exit();
}

if($action=="additional_info_popup")
{
	echo load_html_head_contents("Invoice Additional Info", "../../../", 1, 1,'','','');
	extract($_REQUEST);
	$buyer_library=return_library_array( "select id,buyer_name from lib_buyer", "id", "buyer_name");
	//$data=explode('*',$data);
	?>
	<script>

		var additional_info='<?  echo $data; ?>';

		if(additional_info != "")
		{
			additional_info=additional_info.split('_');

			$(document).ready(function(e) {
				$('#cargo_delivery_to').val( additional_info[0]);
				$('#place_of_delivery').val( additional_info[1]);
				$('#txt_main_mark').val( additional_info[2]);
				$('#txt_side_mark').val( additional_info[3]);
				$('#txt_net_weight').val( additional_info[4]);
				$('#txt_gross_weight').val( additional_info[5]);
				$('#txt_cbm').val( additional_info[6]);
				$('#txt_delv_no').val( additional_info[7]);
				$('#cbo_consignee').val( additional_info[8]);
				$('#cbo_notifying_party').val( additional_info[9]);
				$('#txt_item_description').val( additional_info[10]);
				$('#txt_total_measurment').val( additional_info[11]);
				$('#txt_ngc_id').val( additional_info[12]);
				$('#txt_booking_no').val( additional_info[13]);
				$('#txt_shipment_to').val( additional_info[14]);
				$('#cbo_shipment_terms').val( additional_info[15]);
				$('#txt_ud_no').val( additional_info[16]);
				$('#txt_composition').val( additional_info[17]);

			});
		}


		function submit_additional_info()
		{
			var additional_infos =   $('#cargo_delivery_to').val()+ '_'+$('#place_of_delivery').val()+ '_'+$('#txt_main_mark').val()+ '_'+$('#txt_side_mark').val()+ '_'+$('#txt_net_weight').val()+ '_'+$('#txt_gross_weight').val()+ '_'+$('#txt_cbm').val()+ '_'+$('#txt_delv_no').val()+ '_'+$('#cbo_consignee').val()+ '_'+$('#cbo_notifying_party').val()+ '_'+$('#txt_item_description').val()+ '_'+$('#txt_total_measurment').val()+ '_'+$('#txt_ngc_id').val()+ '_'+$('#txt_booking_no').val()+ '_'+$('#txt_shipment_to').val()+ '_'+$('#cbo_shipment_terms').val()+ '_'+$('#txt_ud_no').val()+ '_'+$('#txt_composition').val();
		
			var additional_infos_arr=additional_infos_data="";
			additional_infos_arr=additional_infos.split("_");
			for(var i=0;i<additional_infos_arr.length;i++)
			{
				additional_infos_data+=additional_infos_arr[i];
			}
			if(additional_infos_data!="") additional_infos=additional_infos; else additional_infos=additional_infos_data;
			$('#additional_infos').val( additional_infos );
			parent.emailwindow.hide();
		}

		
    </script>
	</head>
	<body>
		<div align="center" style="width:100%;" >
		<form name="invoiceadditionalinfo_1"  id="invoiceadditionalinfo_1" autocomplete="off">
			<table width="690" cellspacing="0" cellpadding="0" class="rpt_table" align="center" border="1" rules="all">
				<input type="hidden" name="additional_infos" id="additional_infos" value="">

				<tr>
					<td width="150" align="right">Cargo Delivery To: &nbsp;</td>
					<td width="200">
						<input type="text" name="cargo_delivery_to" id="cargo_delivery_to" value="" class="text_boxes" style="width:190px;"/>
					</td>
					<td width="150" align="right">Place of Delivery: &nbsp;</td>
					<td>
						<input type="text" name="place_of_delivery" id="place_of_delivery" class="text_boxes" style="width:190px;"/>
					</td>
				</tr>
				<tr>
					<td align="right">Main Mark: &nbsp;</td>
					<td><input type="text" name="txt_main_mark" id="txt_main_mark" value="" class="text_boxes" style="width:190px;"/></td>
					<td align="right">Side Mark: &nbsp;</td>
					<td><input type="text" name="txt_side_mark" id="txt_side_mark" value="" class="text_boxes" style="width:190px;"/></td>
				</tr>
				<tr>
					<td align="right">Net Weight: &nbsp;</td>
					<td><input type="text" name="txt_net_weight" id="txt_net_weight" value="" class="text_boxes_numeric" style="width:190px;"/></td>
					<td align="right">Gross Weight: &nbsp;</td>
					<td><input type="text" name="txt_gross_weight" id="txt_gross_weight" value="" class="text_boxes_numeric" style="width:190px;"/></td>
				</tr>
				<tr>
					<td align="right">CBM: &nbsp;</td>
					<td><input type="text" name="txt_cbm" id="txt_cbm" value="" class="text_boxes_numeric" style="width:190px;"/></td>
					<td align="right">Delv. No: &nbsp;</td>
					<td><input type="text" name="txt_delv_no" id="txt_delv_no" value="" class="text_boxes" style="width:190px;"/></td>
				</tr>
				<tr>
					<td align="right">Consignee</td>
					<td><? echo create_drop_down( "cbo_consignee", 165, $buyer_library,"", 1, " select ", 0, "","",$consignee);?></td>
					<td align="right">Notifying Party</td>
					<td><? echo create_drop_down( "cbo_notifying_party", 165, $buyer_library,"", 1, " select ", 0, "","",$notifying_party);?></td>
				</tr>
				<tr>
					<td align="right">Item Description</td>
					<td colspan="3"><input type="text" name="txt_item_description" id="txt_item_description" value="" class="text_boxes" style="width:500px;"/></td>

				</tr>
				<tr>
					<td align="right">Total Measurment</td>
					<td colspan="3"><input type="text" name="txt_total_measurment" id="txt_total_measurment" value="" class="text_boxes" style="width:500px;"/></td>
				</tr>

				<tr>
					<td align="right">NGC ID</td>
					<td><input type="text" name="txt_ngc_id" id="txt_ngc_id" value="" class="text_boxes_numeric" style="width:190px;"/></td>
					<td align="right">Booking No. </td>
					<td><input type="text" name="txt_booking_no" id="txt_booking_no" value="" class="text_boxes" style="width:190px;"/></td>
				</tr>

				<tr>
					<td align="right">Ship To</td>
					<td colspan="3"><input type="text" name="txt_shipment_to" id="txt_shipment_to" value="" class="text_boxes" style="width:500px;"/></td>
					
				</tr>

				<tr>
					<td align="right">Shipment Terms</td>
					<td><?
					$shipment = array("1"=>"Collect", "2"=>"Prepaid");
					echo create_drop_down( "cbo_shipment_terms", 165, $shipment,"", 1, " --select-- ", 0, "","",0);?></td>
					<td align="right">U/D NO.</td>
					<td><input type="text" name="txt_ud_no" id="txt_ud_no" value="" class="text_boxes" style="width:190px;"/></td>
				</tr>
				<tr>
				<td align="right">Composition</td>
					<td colspan="3"><input type="text" name="txt_composition" id="txt_composition" value="" class="text_boxes" style="width:500px;"/></td>
				</tr>

				<tr>
					<td align="center" colspan="4" class="button_container">
						<input type="button" name="close" class="formbutton" value="Close" id="main_close" onClick="submit_additional_info();" style="width:100px" />
					</td>
				</tr>
			</table>
		</form>
		</div>
	</body>
	<script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
	</html>
	<?
	exit();
}


if($action=="freight_info_popup")
{
	echo load_html_head_contents("Freight Info", "../../../", 1, 1,'','','');
	extract($_REQUEST);
	//$buyer_library=return_library_array( "select id,buyer_name from lib_buyer", "id", "buyer_name");
	//$data=explode('*',$data);
	//echo $invoice_id."=".$invoice_no."=".$permission;die;

	?>
	<script>
		var permission='<? echo $permission; ?>';
		var invoice_id='<? echo $invoice_id; ?>';
		
		function fn_amt(row_num)
		{
			var title = 'Freight Details Info';
			var amt_data=$('#freightData_'+row_num).val();
			var page_link = 'export_information_entry_controller.php?action=freight_info_dtls_popup&amt_data='+amt_data;
			emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=720px,height=260px,center=1,resize=1,scrolling=0','../../');
			emailwindow.onclose=function()
			{
				var theform=this.contentDoc.forms[0];
		
				var freight_dtls_data=this.contentDoc.getElementById("freight_dtls_data").value;
				var hidden_tot_amt=this.contentDoc.getElementById("hidden_tot_amt").value;
				
				$('#freightData_'+row_num).val(freight_dtls_data);
				$('#freightCharge_'+row_num).val(hidden_tot_amt);
			}
		}
		
		
		function fnc_freight_info( operation )
		{
			var row_num = $('table#freight_dtls tbody tr').length;
			//alert(row_num);return;
			var data_all="";var j=0;
			for (var i=1; i<=row_num; i++)
			{
				if($('#freightCharge_'+i).val())
				{
					j++;
					var hiddenPoId=$('#hiddenPoId_'+i).val();
					var freightCharge=$('#freightCharge_'+i).val();
					var freightData=$('#freightData_'+i).val();
					data_all+='&hiddenPoId_'+j+"="+hiddenPoId+'&freightCharge_'+j+"="+freightCharge+'&freightData_'+j+"="+freightData;
				}
			}
			//alert(data_all);return;
			var data="action=save_update_delete_freight&operation="+operation+'&total_row='+j+'&invoice_id='+invoice_id+data_all;
			//alert(data);return;
			//freeze_window(operation);
			http.open("POST","export_information_entry_controller.php",true);
			http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
			http.send(data);
			http.onreadystatechange = fnc_freight_info_response;
		}
	
		function fnc_freight_info_response()
		{
			if(http.readyState == 4)
			{
				var reponse=trim(http.responseText).split('**');
				if (reponse[0].length>2) reponse[0]=10;
				//release_freezing();
				if(reponse[0]==0 || reponse[0]==1 || reponse[0]==2)
				{
					parent.emailwindow.hide();
				}
			}
		}

    </script>
    </head>
    <body>
	<div align="center" style="width:100%;" >
	<form name="invoiceFreightInfo_1"  id="invoiceFreightInfo_1" autocomplete="off">
    <?
	$prev_sql="select id, invoice_id, po_breakdown_id, amount, qnty_dtls from export_invoice_freight_dtls where status_active=1 and is_deleted=0 and invoice_id=$invoice_id";
	$prev_sql_result=sql_select($prev_sql);
	$prev_data=array();
	foreach($prev_sql_result as $row)
	{
		$prev_data[$row[csf("po_breakdown_id")]]["amount"]=$row[csf("amount")];
		$prev_data[$row[csf("po_breakdown_id")]]["qnty_dtls"]=$row[csf("qnty_dtls")];
	}
	$inv_sql="select a.po_breakdown_id, a.current_invoice_qnty, a.current_invoice_rate, a.current_invoice_value, b.po_number, c.job_no, c.style_ref_no  
	from com_export_invoice_ship_dtls a, wo_po_break_down b, wo_po_details_master c
	where a.po_breakdown_id=b.id and b.job_no_mst=c.job_no and a.status_active=1 and a.is_deleted=0 and a.current_invoice_qnty>0 and a.mst_id=$invoice_id";
	//echo $inv_sql;
	$inv_sql_result=sql_select($inv_sql);
	?>
		<table width="890" cellspacing="0" cellpadding="0" class="rpt_table" align="center" border="1" rules="all" id="freight_dtls_table">
        	<thead>
            	<tr>
                	<th width="130">Invoice No</th>
                    <th width="100">Job No</th>
                    <th width="110">Style Ref.</th>
                    <th width="110">Order No</th>
                    <th width="90">Invoice Oty</th>
                    <th width="80">Rate</th>
                    <th width="100">Amount</th>
                    <th>Air Freight</th>
                </tr>
            </thead>
        </table>
        <table width="890" cellspacing="0" cellpadding="0" class="rpt_table" align="center" border="1" rules="all" id="freight_dtls">
            <tbody>
            <?
			$i=1;
			foreach($inv_sql_result as $row)
			{
				if ($i%2==0)
                    $bgcolor="#FFFFFF";
                else
                    $bgcolor="#E9F3FF";
				?>
				<tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
                    <td align="center" valign="middle" width="130"><? echo $invoice_no; ?></td>
                    <td width="100"><? echo $row[csf("job_no")];?></td>
                    <td width="110"><? echo $row[csf("style_ref_no")];?></td>
                    <td title="<?=$row[csf("po_breakdown_id")];?>" width="110"><? echo $row[csf("po_number")];?>
                    <input type="hidden" id="hiddenPoId_<? echo $i; ?>" name="hiddenPoId[]" value="<?=$row[csf("po_breakdown_id")];?>" />
                    </td>
                    <td align="right" width="90"><? echo number_format($row[csf("current_invoice_qnty")],2);?></td>
                    <td align="right" width="80"><? echo number_format($row[csf("current_invoice_rate")],2);?></td>
                    <td align="right" width="100"><? echo number_format($row[csf("current_invoice_value")],2);?></td>
                    <td align="center">
                    <input type="text" class="text_boxes_numeric" id="freightCharge_<? echo $i; ?>" name="freightCharge[]" style="width:100px;" onClick="fn_amt(<? echo $i;?>);" readonly placeholder="Browse" value="<?= $prev_data[$row[csf("po_breakdown_id")]]["amount"]?>" />
                    <input type="hidden" id="freightData_<? echo $i; ?>" name="freightData[]" value="<?= $prev_data[$row[csf("po_breakdown_id")]]["qnty_dtls"]?>" />
                    </td>
                </tr>
                <?
				$i++;
			}
			?>	
            </tbody>    
    	</table>
        <div align="center" style="margin-top:10px" class="button_container">
		<? 
		if(count($prev_sql_result)>0) $is_update=1; else $is_update=0;
		echo load_submit_buttons( $permission, "fnc_freight_info", $is_update,0 ,"reset_form('invoiceFreightInfo_1','','','','')",1) ; 
		?>
        </div>
    </form>
	</div>
    </body>
    <script>setFilterGrid('freight_dtls',-1);</script>
    <script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
    </html>
    <?
    exit();
}

if($action=="freight_info_dtls_popup")
{
	echo load_html_head_contents("Air Ship Causes Info","../../../", 1, 1,'','','');
	extract($_REQUEST);
	//echo $amt_data;die;
	if ($db_type == 0) {
		$responsible_unit="select concat(b.id,'*',a.id) id,a.company_name,':',concat(b.location_name) location_name from lib_company a, lib_location b where a.id=b.company_id and b.status_active =1 and b.is_deleted=0 and a.status_active =1 and a.is_deleted=0 order by a.company_name";
	} else if ($db_type == 2 || $db_type == 1) {
		$responsible_unit="select b.id||'*'||a.id as id, a.company_name||' : '||b.location_name as location_name from lib_company a, lib_location b where a.id=b.company_id and b.status_active =1 and b.is_deleted=0 and a.status_active =1 and a.is_deleted=0 order by a.company_name";
	}
	?>
	<script>
	

	function add_break_down_tr(i)
	{
		var row_num=$('#tbl_list_search tbody tr').length;
		if (row_num!=i)
		{
			return false;
		}
		else
		{
			i++;
			$("#tbl_list_search tr:last").clone().find("input,select").each(function() {
				$(this).attr({
					'id': function(_, id) { var id=id.split("_"); return id[0] +"_"+ i },
					'name': function(_, name) { return name + i },
					'value': function(_, value) { return value }
					});
				}).end().appendTo("#tbl_list_search");
			//
			$("#tbl_list_search tr:eq("+i+")").removeAttr('id').attr('id','tr_'+i);
			$("#tbl_list_search tr:eq("+i+")").find('td:first').html(i);
			$('#tbl_list_search tr:eq('+i+') td:eq(3)').attr('id','tdcause_'+i);
			//$('#tdsl_'+i).html(i);
			
			//$('#txtqty_'+i).removeAttr("onBlur").attr("onBlur","fnc_percent_calculate("+i+");");
			$('#cbocausetype_'+i).val(0);
			$('#cbocauseid_'+i).val(0);
			$('#txtAmt_'+i).val("");
			$('#txtPer_'+i).val("");
			$('#txtRemarks_'+i).val("");
			
			$('#increase_'+i).removeAttr("onClick").attr("onClick","add_break_down_tr("+i+");");
			$('#decrease_'+i).removeAttr("onClick").attr("onClick","fn_deletebreak_down_tr("+i+")");
			$('#cbocausetype_'+i).removeAttr("onChange").attr("onChange","load_drop_down( 'export_information_entry_controller',this.value+'__'+'cbocauseid_"+i+"', 'load_drop_down_cause', 'tdcause_"+i+"' )");
			//$('#embellname_'+i).removeAttr("onchange").attr("onchange","load_drop_down('requires/pi_controller_urmi',this.value+'**'+'embelltype_"+i+"', 'load_drop_down_embelltype','embelltypeTd_"+i+"')");
			//cbocausetype_2
			//load_drop_down( 'export_information_entry_controller',this.value, 'load_drop_down_cause', 'causeTd_'".$i." );
			set_all_onclick();
		}
	}

	function fn_deletebreak_down_tr(rowNo)
	{
		var numRow = $('table#tbl_list_search tbody tr').length;
		if(rowNo!=1)
		{
			//var permission_array=permission.split("_");
			var rowid=$('#tr_'+rowNo).val();
			var index=rowNo-1
			$('#tbl_list_search tbody tr:eq('+index+')').remove();
			calculate_amount(1);
		}
	}
	
	function fn_close()
	{
		var numRow = $('table#tbl_list_search tbody tr').length;
		var data_all=""; var tot_amt=0;
		for(i = 1; i <= numRow; i++)
		{
			var resunit=$('#cboResUnit_'+i).val();
			var resdept=$('#cbocausetype_'+i).val();
			var rescause=$('#cbocauseid_'+i).val();
			var resamt=$('#txtAmt_'+i).val();
			//alert($('#txtRemarks_'+i).val());
			if (form_validation('cboResUnit_'+i+'*cbocausetype_'+i+'*cbocauseid_'+i+'*txtAmt_'+i,'Unit*Department*Cause*Amount')==false)
			{
				return;
			}
			//
			data_all+=$('#cboResUnit_'+i).val()+"__"+$('#cbocausetype_'+i).val()+"__"+$('#cbocauseid_'+i).val()+"__"+$('#txtAmt_'+i).val()+"__"+$('#txtPer_'+i).val()+"__"+$('#txtRemarks_'+i).val()+"**";
			tot_amt +=$('#txtAmt_'+i).val()*1;
		}
		data_all = data_all.substr( 0, data_all.length - 2 );
		$('#freight_dtls_data').val(data_all);
		$('#hidden_tot_amt').val(tot_amt);
		parent.emailwindow.hide();
	}
	
	function calculate_amount(i)
    {
        /*var ddd = {dec_type: 5, comma: 0, currency: ''}
        math_operation('amount_' + i, 'quantity_' + i + '*rate_' + i, '*', '', ddd);
        calculate_total_amount(1);*/
		
		var ddd = {dec_type: 2, comma: 0, currency: ''}
		var numRow = $('table#tbl_list_search tbody tr').length;
		//alert(numRow+"="+i);
		math_operation("tot_amt", "txtAmt_", "+", numRow, ddd);
    }
	
	/*function fnc_percent_calculate(i)
	{
		var finish_qnty=$('#hid_finish_qnty').val()*1;
		var qty=$('#txtqty_'+i).val()*1;
		
		var per=(qty/finish_qnty)*100;
		$('#txtper_'+i).val( number_format(per,4,'.','' ) );
		var q=i;
		var row_num = $('table#tbl_list_search tbody tr').length;
		var tot_qty=0;
		for (var i=1; i<=row_num; i++)
		{
			tot_qty+=$('#txtqty_'+i).val()*1;
		}
		
		if(tot_qty>finish_qnty)
		{
			alert("Cause Finish Qty (Kg) is over Then Actual Finish Fabric Qty.\n Total Cause Finish Qty (Kg)"+tot_qty+".\n Actual Finish Fabric Qty"+finish_qnty);
			$('#txtqty_'+q).val('');
			return;
		}
	}*/

	
	</script>
	</head>
	<body>
	<div align="center">
		<div style="display:none"><? echo load_freeze_divs ("../../../",$permission); ?></div>
        <fieldset style="width:710px">
        <form id="causesinfo_1" autocomplete="off">
            <table width="710" cellspacing="0" cellpadding="0" border="1" rules="all" class="rpt_table" id="tbl_list_search">
                <thead>
                	<th width="30">SL
                    <input type="hidden" id="freight_dtls_data" name="freight_dtls_data" />
                    <input type="hidden" id="hidden_tot_amt" name="hidden_tot_amt" />
                    </th>
                    <th width="110" class="must_entry_caption">Responsible Unit</th>
                    <th width="110" class="must_entry_caption">Depertment</th>
                    <th width="150" class="must_entry_caption">Causes</th>
                    <th width="100" class="must_entry_caption">Air Freight Amount</th>
                    <th width="90" style="display:none">Air Fright %</th>
                    <th width="110">Remarks</th>
                    <th>&nbsp;</th>
                </thead>
                <tbody>
					<?
                    $amt_data_ref=explode("**",$amt_data);
					$i=1;
					//$cause_arr=array(1=>"couse 1",2=>"cause 2", 3=>"caouse 3");
					$cause_arr=return_library_array( "select id, cause from booking_cause where entry_form=270 and status_active=1 and is_deleted=0",'id','cause');
					
					foreach($amt_data_ref as $data)
					{
						$data_ref=explode("__",$data);
						$res_unit=0;
						$res_unit=$row[csf("res_location")].'*'.$row[csf("res_company")];
						?>
						<tr id="tr_<? echo $i;?>">
							<td align="center"><? echo $i;?></td>
							<td align="center"><? echo create_drop_down( "cboResUnit_".$i, 100, $responsible_unit,"id,location_name", 1, "-Responsible Unit-", $data_ref[0], "" ); ?></td>
                            <td align="center"><? 
							echo create_drop_down( "cbocausetype_".$i, 100, $short_booking_cause_arr,"", 1, "-- Select --", $data_ref[1], "load_drop_down( 'export_information_entry_controller',this.value+'__'+'cbocauseid_".$i."', 'load_drop_down_cause', 'tdcause_".$i."' );",0 ); //this.value+'**'+'embelltype_1'
							?></td>
							<td align="center" id="tdcause_<? echo $i;?>"><? echo create_drop_down( "cbocauseid_".$i, 130, $cause_arr,"", 1, "-- Select --", $data_ref[2], "" ); ?></td>
							<td align="center"><input type="text" id="txtAmt_<? echo $i;?>" name="txtAmt[]" class="text_boxes_numeric" style="width:70px;" value="<? echo $data_ref[3]; ?>" onKeyUp="calculate_amount(<? echo $i;?>)" /></td>
							<td align="center" style="display:none;"><input type="text" id="txtPer_<? echo $i;?>" name="txtPer[]" class="text_boxes_numeric" style="width:70px;" value="<? echo $data_ref[4]; ?>"/></td>
                            <td align="center"><input type="text" id="txtRemarks_<? echo $i;?>" name="txtRemarks[]" class="text_boxes" style="width:90px;" value="<? echo $data_ref[5]; ?>"/></td>
							<td align="center">
								<input type="button" id="increase_<? echo $i;?>" name="increase_<? echo $i;?>" style="width:30px" class="formbutton" value="+" onClick="add_break_down_tr(<? echo $i;?>)" />
								<input type="button" id="decrease_<? echo $i;?>" name="decrease_<? echo $i;?>" style="width:30px" class="formbutton" value="-" onClick="fn_deletebreak_down_tr(<? echo $i;?>);" />
							</td>
						</tr>
						<?
						$i++;
						$total_amt+=$data_ref[3];
					}
                    ?>
                </tbody>
            </table>
            <table width="710" cellspacing="0" cellpadding="0" border="1" rules="all" class="rpt_table" id="tbl_list_dtls">
            	<tfoot>
                	<th width="30">&nbsp;</th>
                    <th width="110">&nbsp;</th>
                    <th width="110">&nbsp;</th>
                    <th width="150">Total:</th>
                    <th width="100" align="center" style="text-align:center"><input type="text" id="tot_amt" name="tot_amt" class="text_boxes_numeric" style="width:70px;" value="<? echo $total_amt; ?>" /></th>
                    <th style="display:none"></th>
                    <th width="110">&nbsp;</th>
                    <th>&nbsp;</th>
                </tfoot>
            </table>
            <p style="text-align:center"><input type="button" value="Close" style="width:100px" id="btb_close" name="btn_close" onClick="fn_close()" class="formbuttonplasminus" /></p>
        </form>
        </fieldset>
	</div>
	<script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
	</body>
	</html>
	<?
	exit();
}

if($action=="load_drop_down_cause")
{
	//echo $data;die;
	$data_ref=explode("__",$data);
	$sql="select id, cause from booking_cause where entry_form=270 and status_active=1 and is_deleted=0 and cause_id=$data_ref[0]";
	//echo $sql;die;
	echo create_drop_down( $data_ref[1], 130, $sql,"id,cause", 1, "-- Select --", 0, "" );
	die;
}

if($action=="save_update_delete_freight")
{
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process ));
	$con = connect();
	//action=save_update_delete_freight&operation=0&total_row=1&invoice_id=4473&hiddenPoId_1=38982&freightCharge_1=20&freightData_1=34*3__7__1__20__2
	if ($operation==0)  // Insert Here
	{
		if($db_type==0)
		{
			mysql_query("BEGIN");
		}

		//if( check_table_status( $_SESSION['menu_id'], 1 )==0 ) { echo "15**0"; die;}
		$id=return_next_id( "id", "export_invoice_freight_dtls", 1 ) ;
		$field_array="id, invoice_id, po_breakdown_id, amount, qnty_dtls, inserted_by, insert_date, status_active, is_deleted";
		for ($i=1;$i<=$total_row;$i++)
		{
			$hiddenPoId="hiddenPoId_".$i;
			$freightCharge="freightCharge_".$i;
			$freightData="freightData_".$i;
			if ($i!=1) $data_array .=",";
			$data_array .="(".$id.",'".$invoice_id."','".$$hiddenPoId."','".$$freightCharge."','".$$freightData."',".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."',1,0)";
			$id=$id+1;
		}
		//echo "10** $total_row = insert into export_invoice_freight_dtls ($field_array) values $data_array";die;
		$rID=sql_insert("export_invoice_freight_dtls",$field_array,$data_array,0);
		//check_table_status( $_SESSION['menu_id'],0);
		if($db_type==0)
		{
			if($rID==1){
				mysql_query("COMMIT");
				echo "0";
			}
			else{
				mysql_query("ROLLBACK");
				echo "10";
			}
		}
		else if($db_type==2 || $db_type==1 )
		{
			if($rID==1){
				oci_commit($con);
				echo "0";
			}
			else{
				oci_rollback($con);
				echo "10";
			}
		}
		disconnect($con);
		die;
	}
	else if ($operation==1)  // Update Here
	{
		if($db_type==0)
		{
			mysql_query("BEGIN");
		}
		
		$id=return_next_id( "id", "export_invoice_freight_dtls", 1 ) ;
		$field_array="id, invoice_id, po_breakdown_id, amount, qnty_dtls, inserted_by, insert_date, status_active, is_deleted";
		for ($i=1;$i<=$total_row;$i++)
		{
			$hiddenPoId="hiddenPoId_".$i;
			$freightCharge="freightCharge_".$i;
			$freightData="freightData_".$i;
			if ($i!=1) $data_array .=",";
			$data_array .="(".$id.",'".$invoice_id."','".$$hiddenPoId."','".$$freightCharge."','".$$freightData."',".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."',1,0)";
			$id=$id+1;
		}
		$rID2=execute_query("delete from export_invoice_freight_dtls where invoice_id=$invoice_id");
		$rID=sql_insert("export_invoice_freight_dtls",$field_array,$data_array,0);
		
		//echo $rID.'='.$rID1.'='.$rID2.'='.$flag; die;
		//check_table_status( $_SESSION['menu_id'],0);
		if($db_type==0)
		{
			if($rID && $rID2){
				mysql_query("COMMIT");
				echo "1";
			}
			else{
				mysql_query("ROLLBACK");
				echo "10";
			}
		}
		else if($db_type==2 || $db_type==1 )
		{
			if($rID && $rID2){
				oci_commit($con);
				echo "1";
			}
			else{
				oci_rollback($con);
				echo "10";
			}
		}
		disconnect($con);
		die;
	}
	else if ($operation==2)  // Insert Here
	{
		/*$con = connect();
		if($db_type==0)
		{
			mysql_query("BEGIN");
		}

		//if  ( check_table_status( $_SESSION['menu_id'], 1 )==0 ) { echo "15**0"; die;}

		$field_array_up="status_active*is_deleted*updated_by*update_date";
		for ($i=1;$i<=$total_row;$i++)
		{
			$cbocausetype="cbocausetype_".$i;
			$cbocauseid="cbocauseid_".$i;
			$txtqty="txtqty_".$i;
			$txtper="txtper_".$i;
			$hidupid="hidupid_".$i;
			if(str_replace("'",'',$$hidupid)!="")
			{
				$id_arr[]=str_replace("'",'',$$hidupid);
				$data_array_up[str_replace("'",'',$$hidupid)] =explode("*",("0*1*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'"));
			}
		}
		// echo bulk_update_sql_statement( "wo_po_acc_po_info", "id", $field_array_up, $data_array_up, $id_arr );
		$rID=execute_query(bulk_update_sql_statement( "wo_booking_short_cause", "id", $field_array_up, $data_array_up, $id_arr ));
		//check_table_status( $_SESSION['menu_id'],0);
		if($db_type==0)
		{
			if($rID==1){
				mysql_query("COMMIT");
				echo "2";
			}
			else{
				mysql_query("ROLLBACK");
				echo "10";
			}
		}
		else if($db_type==2 || $db_type==1 )
		{
			if($rID==1){
				oci_commit($con);
				echo "2";
			}
			else{
				oci_rollback($con);
				echo "10";
			}
		}
		disconnect($con);
		die;*/
	}
}


if ($action=="print_invoice")
{
    extract($_REQUEST);
	$company_name_sql=sql_select( "select id, company_name, company_short_name, contract_person, plot_no, level_no, road_no, block_no, city, country_id from lib_company");
	foreach($company_name_sql as $row)
	{
		$company_name_arr[$row[csf("id")]]["company_name"]=$row[csf("company_name")];
		$company_name_arr[$row[csf("id")]]["company_short_name"]=$row[csf("company_short_name")];
		$company_name_arr[$row[csf("id")]]["contract_person"]=$row[csf("contract_person")];
		$company_name_arr[$row[csf("id")]]["plot_no"]=$row[csf("plot_no")];
		$company_name_arr[$row[csf("id")]]["level_no"]=$row[csf("level_no")];
		$company_name_arr[$row[csf("id")]]["road_no"]=$row[csf("road_no")];
		$company_name_arr[$row[csf("id")]]["block_no"]=$row[csf("block_no")];
		$company_name_arr[$row[csf("id")]]["city"]=$row[csf("city")];
		$company_name_arr[$row[csf("id")]]["country_id"]=$row[csf("country_id")];

	}

	//var_dump($company_name_arr[1]);

	$applicant_sql=sql_select( "select a.id, a.buyer_name, a.short_name, a.address_1, b.party_type from lib_buyer a,  lib_buyer_party_type b where a.id=b.buyer_id and b.party_type in(22,23,4,5,6,100)");
	foreach($applicant_sql as $row)
	{
		$buyer_name_arr[$row[csf("id")]]["buyer_name"]=$row[csf("buyer_name")];
		$buyer_name_arr[$row[csf("id")]]["address_1"]=$row[csf("address_1")];
	}


	$inv_master_data=sql_select("select id, benificiary_id, buyer_id, location_id, invoice_no, invoice_date, exp_form_no, exp_form_date, is_lc, lc_sc_id,  bl_no, feeder_vessel, inco_term, inco_term_place, shipping_mode, port_of_entry, port_of_loading, port_of_discharge, main_mark, side_mark, net_weight, gross_weight, cbm_qnty, discount_ammount, bonus_ammount, commission, total_carton_qnty, bl_date, carton_net_weight, carton_gross_weight, remarks from com_export_invoice_ship_mst where id=$data");
	if($inv_master_data[0][csf("is_lc")]==1)
	{
		$lc_sc_data=sql_select("select id, export_lc_no, lc_date, notifying_party, consignee, issuing_bank_name, negotiating_bank, lien_bank, pay_term, applicant_name from com_export_lc where id='".$inv_master_data[0][csf("lc_sc_id")]."' ");
		foreach($lc_sc_data as $row)
		{
			$lc_sc_id=$row[csf("id")];
			$lc_sc_no=$row[csf("export_lc_no")];
			$lc_sc_date=change_date_format($row[csf("lc_date")]);
			$notifying_party=$row[csf("notifying_party")];
			$consignee=$row[csf("consignee")];//also notify party
			$issuing_bank_name=$row[csf("issuing_bank_name")];
			$negotiating_bank=$row[csf("lien_bank")];
			$pay_term_id=$row[csf("pay_term")];
			$applicant_name=$row[csf("applicant_name")];
		}

			$cate_hs_sql=sql_select("select wo_po_break_down_id, fabric_description, category_no, hs_code from com_export_lc_order_info where com_export_lc_id='".$inv_master_data[0][csf("lc_sc_id")]."'");
			foreach($cate_hs_sql as $row)
			{
				$order_la_data[$row[csf("wo_po_break_down_id")]]["category_no"]=$row[csf("category_no")];
				$order_la_data[$row[csf("wo_po_break_down_id")]]["hs_code"]=$row[csf("hs_code")];
				$order_la_data[$row[csf("wo_po_break_down_id")]]["fabric_description"]=$row[csf("fabric_description")];
				$all_order_id.=$row[csf("wo_po_break_down_id")].", ";
			}
	}
	else
	{
		$lc_sc_data=sql_select("select id, contract_no, contract_date, notifying_party, consignee, lien_bank, pay_term, applicant_name from com_sales_contract where id='".$inv_master_data[0][csf("lc_sc_id")]."'  and status_active=1");
		foreach($lc_sc_data as $row)
		{
			$lc_sc_id=$row[csf("id")];
			$lc_sc_no=$row[csf("contract_no")];
			$lc_sc_date=change_date_format($row[csf("contract_date")]);
			$notifying_party=$row[csf("notifying_party")];
			$consignee=$row[csf("consignee")];//also notify party
			$issuing_bank_name="";
			$negotiating_bank=$row[csf("lien_bank")];
			$pay_term_id=$row[csf("pay_term")];
			$applicant_name=$row[csf("applicant_name")];
		}

		$cate_hs_sql=sql_select("select wo_po_break_down_id, fabric_description, category_no, hs_code from com_sales_contract_order_info where com_sales_contract_id='".$inv_master_data[0][csf("lc_sc_id")]."' and status_active=1");
		foreach($cate_hs_sql as $row)
		{
			$order_la_data[$row[csf("wo_po_break_down_id")]]["category_no"]=$row[csf("category_no")];
			$order_la_data[$row[csf("wo_po_break_down_id")]]["hs_code"]=$row[csf("hs_code")];
			$order_la_data[$row[csf("wo_po_break_down_id")]]["fabric_description"]=$row[csf("fabric_description")];
			$all_order_id.=$row[csf("wo_po_break_down_id")].", ";
		}
	}

	$all_order_id=chop($all_order_id, " , ");

	$ex_ctn_arr=return_library_array( "select po_break_down_id, sum(total_carton_qnty) as total_carton_qnty from pro_ex_factory_mst where po_break_down_id in($all_order_id) and status_active=1 group by po_break_down_id",'po_break_down_id','total_carton_qnty');

	if($all_order_id!="")
	{
		if($db_type==0)
		{
			$art_num_arr=return_library_array( "select po_break_down_id, min(article_number) as article_number from wo_po_color_size_breakdown where po_break_down_id in($all_order_id) and article_number!='' group by po_break_down_id",'po_break_down_id','article_number');
		}
		else
		{
			$art_num_arr=return_library_array( "select po_break_down_id, min(article_number) as article_number from wo_po_color_size_breakdown where po_break_down_id in($all_order_id) and article_number is not null group by po_break_down_id",'po_break_down_id','article_number');
		}
	}

	//$bank_arr=return_library_array( "select id, bank_name from lib_bank",'id','bank_name');

	?>
    <table id="" cellspacing="0" cellpadding="0" width="690" border="0">
        <tr>
            <td colspan="5" align="center" style="font-size:18px; font-weight:bold"><? echo $company_name_arr[$inv_master_data[0][csf("benificiary_id")]]["company_name"]; ?></td>
        </tr>
        <tr>
            <td colspan="5" align="center" style="font-size:14px; font-weight:bold"><? echo "Commercial Invoice"; ?></td>
        </tr>
        <tr>
            <td colspan="5" align="center">&nbsp;</td>
        </tr>
    </table>
    <table id="" cellspacing="0" cellpadding="0" width="690" border="0"  style="font-size:11px;">
        <tr>
            <td width="90" align="right" valign="top">Shipper :&nbsp;</td>
            <td width="250" align="left" valign="top">
			<?
            $comany_details="";
            echo $company_name_arr[$inv_master_data[0][csf("benificiary_id")]]["company_name"]."<br>";
            $plot_no=$company_name_arr[$inv_master_data[0][csf("benificiary_id")]]["plot_no"];
            $level_no=$company_name_arr[$inv_master_data[0][csf("benificiary_id")]]["level_no"];
            $road_no=$company_name_arr[$inv_master_data[0][csf("benificiary_id")]]["road_no"];
            $block_no=$company_name_arr[$inv_master_data[0][csf("benificiary_id")]]["block_no"];
            $city=$company_name_arr[$inv_master_data[0][csf("benificiary_id")]]["city"];
            $country_id=$company_name_arr[$inv_master_data[0][csf("benificiary_id")]]["country_id"];
            if($plot_no!="")  $comany_details= $plot_no.", ";
			if($level_no!="")  $comany_details.= $level_no.", ";
			if($road_no!="")  $comany_details.= $road_no.", ";
			if($block_no!="")  $comany_details.= $block_no.", ";
			if($city!="")  $comany_details.= "<br>".$city.", ";
			if($country_id!="")  $comany_details.=return_field_value( "country_name","lib_country","id='$country_id'")." . ";
			echo $comany_details;
            ?>
            </td>
            <td width="120" valign="top" align="right">
            Invoice No: &nbsp;<br>
            Lc/Sc No: &nbsp;<br>
            EXP NO: &nbsp;
            </td>
            <td width="140" valign="top" align="left">
			<?
				echo $inv_master_data[0][csf("invoice_no")]."<br>";
				echo $lc_sc_no."<br>";
				echo $inv_master_data[0][csf("exp_form_no")];
			?>
            </td>
            <td valign="top" align="left">
            Date: <? echo change_date_format($inv_master_data[0][csf("invoice_date")]);?><br>
            Date: <? echo $lc_sc_date;?><br>
            Date: <? if($inv_master_data[0][csf("exp_form_date")]!="" && $inv_master_data[0][csf("exp_form_date")]!="0000-00-00" ) echo change_date_format($inv_master_data[0][csf("exp_form_date")]);?><br>
           </td>
        </tr>
        <tr>
        	<td valign="top" align="right">Applicant :&nbsp;</td>
            <td valign="top"  align="left">
			<?
			echo $buyer_name_arr[$applicant_name]["buyer_name"]."<br>";
			echo $buyer_name_arr[$applicant_name]["address_1"]."<br>";
			?>
            </td>
            <td valign="top" align="right">LC Issue Bank: &nbsp;</td>
            <td colspan="2" valign="top"  align="left">
			<?
				echo $issuing_bank_name;
			?></td>
        </tr>
        <tr>
        	<td valign="top" align="right">Notify :&nbsp;</td>
            <td valign="top"  align="left" colspan="4">
			<?
			/*$notifying_party_all="";
			$notifying_party_arr=explode(",",$notifying_party);
			foreach($notifying_party_arr as $bank_id)
			{
				$notifying_party_all.=$buyer_name_arr[$bank_id][2]["buyer_name"]." ";
				if($buyer_name_arr[$bank_id][2]["address_1"]!="")
				{
					$notifying_party_all.=$buyer_name_arr[$bank_id][2]["address_1"].".";
				}
				else
				{
					$notifying_party_all.="<br>";
				}
			}*/
			$notifying_party_all="";
			$notifying_party_arr=explode(",",$notifying_party);
			foreach($notifying_party_arr as $buyer_id)
			{
				if($buyer_name_arr[$buyer_id]["buyer_name"]!="")
				{
					$notifying_party_all.=$buyer_name_arr[$buyer_id]["buyer_name"]."<br>";
					if($buyer_name_arr[$buyer_id]["address_1"]!="")
					{
						$notifying_party_all.=$buyer_name_arr[$buyer_id]["address_1"]." ";
					}
					$notifying_party_all.="<br>";
				}
			}
			$notifying_party_all=chop($notifying_party_all, " <br> ");
			echo $notifying_party_all;
			?>
            </td>

        </tr>
        <tr>
        	<td valign="top" align="right">Also Notify :&nbsp;</td>
            <td valign="top"  align="left" colspan="4">
			<?
			$notifying_also_party_all="";
			$consignee_arr=explode(",",$consignee);
			foreach($consignee_arr as $buyer_con_id)
			{
				if($buyer_name_arr[$buyer_con_id]["buyer_name"]!="")
				{
					$notifying_also_party_all.=$buyer_name_arr[$buyer_con_id]["buyer_name"]."<br>";
					if($buyer_name_arr[$buyer_con_id]["address_1"]!="")
					{
						$notifying_also_party_all.=$buyer_name_arr[$buyer_con_id]["address_1"]." ";
					}
					$notifying_also_party_all.="<br>";
				}
			}
			$notifying_also_party_all=chop($notifying_also_party_all, " <br> ");
			echo $notifying_also_party_all;
			?>
            </td>
        </tr>
        <tr>
        	<td valign="top" align="right">Country Of Orgin :&nbsp; </td>
            <td valign="top"  align="left">
			<?  echo "Bangladesh";
			?>
            </td>
            <td valign="top" align="right">Negotiating Bank: &nbsp;</td>
            <td colspan="2" valign="top"  align="left">
			<?
			$bank_details=sql_select("select id,bank_name,address from lib_bank where id in($negotiating_bank)");
			echo $bank_details[0][csf("bank_name")]." ".$bank_details[0][csf("address")];
			?></td>
        </tr>
        <tr>
        	<td valign="top" align="right">HAWB/BL No :&nbsp;</td>
            <td valign="top"  align="left">
			<?
				echo $inv_master_data[0][csf("bl_no")];
			?>
            </td>
            <td valign="top" align="right">Incoterm: &nbsp;</td>
            <td colspan="2" valign="top"  align="left">
			<?
				echo $incoterm[$inv_master_data[0][csf("inco_term")]];
			?></td>

        </tr>
        <tr>
        	<td valign="top" align="right">Port Of Loading: &nbsp;</td>
            <td valign="top"  align="left">
			<?
				echo $inv_master_data[0][csf("port_of_loading")];
			?></td>
            <td valign="top" align="right">HAWB/BL Date :&nbsp;</td>
            <td valign="top" colspan="2"  align="left">
			<?
				if($inv_master_data[0][csf("bl_date")]!="" && $inv_master_data[0][csf("bl_date")]!="0000-00-00") echo change_date_format($inv_master_data[0][csf("bl_date")]);
			?>
            </td>

        </tr>
        <tr>
        	<td valign="top" align="right">
            Port Of Discharg: &nbsp;
            </td>
            <td valign="top"  align="left">
			<?
				echo $inv_master_data[0][csf("port_of_discharge")]."<br>";
			?>
            </td>
        	<td valign="top" align="right">Fedder Vessel :&nbsp;</td>
            <td valign="top" colspan="2" align="left">
			<?
				echo $inv_master_data[0][csf("feeder_vessel")];
			?>
            </td>
        </tr>
        <tr>
        	<td valign="top" align="right">Payment Terms :&nbsp;</td>
            <td valign="top"  align="left"><? echo $pay_term[$pay_term_id];?></td>
            <td valign="top" align="right">Mode Of Shipment: &nbsp;</td>
            <td valign="top"  align="left"><? echo $shipment_mode[$inv_master_data[0][csf("shipping_mode")]];?></td>
        </tr>
		<tr>
        	<td valign="top" align="right">Remarks :&nbsp;</td>
            <td valign="top"  align="left"><? echo $inv_master_data[0][csf("remarks")];?></td>
            
        </tr>
    </table>
    <br>
    <table id="" cellspacing="0" cellpadding="0" border="1" rules="all" width="690" class="rpt_table"  style="font-size:9px;" >
        <thead>
            <tr>
                <th width="90" rowspan="2">Shipping Mark</th>
                <th colspan="3">Description</th>
                <th width="65" rowspan="2">Style No.</th>
                <th width="45" rowspan="2">Art No.</th>
                <th width="30" rowspan="2" >Category</th>
                <th width="30" rowspan="2">Hs Code</th>
                <th colspan="2">Qnty</th>
                <th width="40" rowspan="2">Ctns Qnty</th>
                <th width="30" rowspan="2">Unit Price</th>
                <th rowspan="2">Amount</th>
            </tr>
            <tr>
            	<th width="65">Po No</th>
                <th width="75">Description</th>
                <th width="75">Description</th>
                <th width="40">Qnty</th>
                <th width="20">UOM</th>
            </tr>
        </thead>
        <tbody>
        <?

		$dtls_sql="select a.id as dtls_id, a.po_breakdown_id, a.current_invoice_rate, a.current_invoice_qnty, a.current_invoice_value, b.po_number, c.style_ref_no, c.gmts_item_id, c.order_uom from  com_export_invoice_ship_dtls a,  wo_po_break_down b, wo_po_details_master c where a.po_breakdown_id=b.id and b.job_no_mst=c.job_no and a.current_invoice_qnty>0 and a.status_active=1 and a.is_deleted=0 and a.mst_id=$data";


		//echo $dtls_sql; die;
		$result=sql_select($dtls_sql);
		$row_span=count($result)+2;
		if($inv_master_data[0][csf("discount_ammount")]>0) $row_span=$row_span+1;
		if($inv_master_data[0][csf("bonus_ammount")]>0)  $row_span=$row_span+1;
		if($inv_master_data[0][csf("commission")]>0)  $row_span=$row_span+1;
		$i=1;
		$main_mark_arr=explode(",",$inv_master_data[0][csf("main_mark")]);
		$side_mark_arr=explode(",",$inv_master_data[0][csf("side_mark")]);
		foreach($result as $row)
		{
			?>
            <tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
            	<?
				if($i==1)
				{
					?>
                	<td width="90" rowspan="<? echo $row_span; ?>" valign="top"><p><span style="text-decoration:underline;">Main Mark</span><br>
					<?
					$all_main_mark="";
					foreach($main_mark_arr as $val)
					{
						$all_main_mark.=$val."<br>";
					}
					$all_main_mark=chop($all_main_mark, " <br> ");
					echo  $all_main_mark;
					?><br><span style="text-decoration:underline;">Side Mark</span><br>
					<?
					$all_side_mark="";
					foreach($side_mark_arr as $val)
					{
						$all_side_mark.=$val."<br>";
					}
					$all_side_mark=chop($all_side_mark, " <br> ");
					echo  $all_side_mark;
					?></p></td>
                	<?
                }
				?>
                <td width="65"><p><? echo $row[csf("po_number")]; ?>&nbsp;</p></td>
                <td width="75"><p><? echo $garments_item[$row[csf("gmts_item_id")]]; ?>&nbsp;</p></td>
                <td width="75"><p><? echo $order_la_data[$row[csf("po_breakdown_id")]]["fabric_description"]; ?>&nbsp;</p></td>
                <td width="65"><p><? echo $row[csf('style_ref_no')]; ?>&nbsp;</p></td>
                <td width="45"><p><? echo $art_num_arr[$row[csf('po_breakdown_id')]]; ?>&nbsp;</p></td>
                <td width="30" align="center"><p><? echo $order_la_data[$row[csf('po_breakdown_id')]]["category_no"]; ?>&nbsp;</p></td>
                <td width="30" align="center"><p><? echo  $order_la_data[$row[csf('po_breakdown_id')]]["hs_code"]; ?>&nbsp;</p></td>
                <td width="40" align="right"><? echo number_format($row[csf('current_invoice_qnty')],2); ?></td>
                <td  width="27" style="padding-left:3px;"><p><? echo $unit_of_measurement[$row[csf('order_uom')]]; ?></p></td>
                <td align="right" width="40"><? echo number_format($ex_ctn_arr[$row[csf('po_breakdown_id')]],2); ?></td>
                <td align="right" width="30"><? echo number_format($row[csf("current_invoice_rate")],2); ?></td>
                <td align="right"><? echo number_format($row[csf("current_invoice_value")],2); ?></td>
            </tr>
            <?
			$total_value+=$row[csf("current_invoice_value")];
			$total_qnty+=$row[csf("current_invoice_qnty")];
			$last_uom=$unit_of_measurement[$row[csf('order_uom')]];
			$total_carton_qnty+=$ex_ctn_arr[$row[csf('po_breakdown_id')]];
			$i++;
		}
		?>
        	<tr bgcolor="#FFFFCC">
                <td width="65"><p>&nbsp;</p></td>
                <td width="75"><p>&nbsp;</p></td>
                <td width="75"><p>&nbsp;</p></td>
                <td width="65"><p>&nbsp;</p></td>
                <td width="45"><p>&nbsp;</p></td>
                <td width="30"><p>&nbsp;</p></td>
                <td width="30"><p>&nbsp;</p></td>
                <td width="40"><p>&nbsp;</p></td>
                <td align="right" width="30"><p>&nbsp;</p></td>
                <td align="right" width="30" colspan="2">Total Value</td>
                <td align="right"><? echo number_format($total_value,2); ?></td>
            </tr>
            <?
			if($inv_master_data[0][csf("discount_ammount")]>0)
			{
				?>
                <tr>
                    <td width="65"><p>&nbsp;</p></td>
                    <td width="75"><p>&nbsp;</p></td>
                    <td width="75"><p>&nbsp;</p></td>
                    <td width="65"><p>&nbsp;</p></td>
                    <td width="45"><p>&nbsp;</p></td>
                    <td width="30"><p>&nbsp;</p></td>
                    <td width="30"><p>&nbsp;</p></td>
                    <td width="40"><p>&nbsp;</p></td>
                    <td align="right" width="30"><p>&nbsp;</p></td>
                    <td align="right" width="30" colspan="2">Total Discount</td>
                    <td align="right"><? echo number_format($inv_master_data[0][csf("discount_ammount")],2); ?></td>
                </tr>
                <?
				$total_value=$total_value-$inv_master_data[0][csf("discount_ammount")];
			}

			if($inv_master_data[0][csf("bonus_ammount")]>0)
			{
				?>
                <tr>
                    <td width="65"><p>&nbsp;</p></td>
                    <td width="75"><p>&nbsp;</p></td>
                    <td width="75"><p>&nbsp;</p></td>
                    <td width="65"><p>&nbsp;</p></td>
                    <td width="45"><p>&nbsp;</p></td>
                    <td width="30"><p>&nbsp;</p></td>
                    <td width="30"><p>&nbsp;</p></td>
                    <td width="40"><p>&nbsp;</p></td>
                    <td align="right" width="30"><p>&nbsp;</p></td>
                    <td align="right" width="30" colspan="2">Total Bonus</td>
                    <td align="right"><? echo number_format($inv_master_data[0][csf("bonus_ammount")],2); ?></td>
                </tr>
                <?
				$total_value=$total_value-$inv_master_data[0][csf("bonus_ammount")];
			}
			if($inv_master_data[0][csf("commission")]>0)
			{
				?>
                <tr>
                    <td width="65"><p>&nbsp;</p></td>
                    <td width="75"><p>&nbsp;</p></td>
                    <td width="75"><p>&nbsp;</p></td>
                    <td width="65"><p>&nbsp;</p></td>
                    <td width="45"><p>&nbsp;</p></td>
                    <td width="30"><p>&nbsp;</p></td>
                    <td width="30"><p>&nbsp;</p></td>
                    <td width="40"><p>&nbsp;</p></td>
                    <td align="right" width="30"><p>&nbsp;</p></td>
                    <td align="right" width="30" colspan="2">Total Commission</td>
                    <td align="right"><? echo number_format($inv_master_data[0][csf("commission")],2); ?></td>
                </tr>
                <?
				$total_value=$total_value-$inv_master_data[0][csf("commission")];
			}
			?>
            <tr bgcolor="#CCCCCC">
                <td width="65"><p>&nbsp;</p></td>
                <td width="75"><p>&nbsp;</p></td>
                <td width="75"><p>&nbsp;</p></td>
                <td width="65"><p>&nbsp;</p></td>
                <td width="45"><p>&nbsp;</p></td>
                <td width="30"><p>&nbsp;</p></td>
                <td width="30"><p>&nbsp;</p></td>
                <td width="40"><p>&nbsp;</p></td>
                <td align="right" width="30"><p>&nbsp;</p></td>
                <td align="right" width="30" colspan="2">Net Total</td>
                <td align="right"><? echo number_format($total_value,2); ?></td>
            </tr>
        </tbody>
    </table>
    <br>
    <table id="" cellspacing="0" cellpadding="0" border="1" rules="all" width="400" class="rpt_table"  style="font-size:10px;" >
    	<tr>
        	<td width="130" align="right">Total Ctns:&nbsp;</td>
            <td width="200" align="right"><? echo number_format($total_carton_qnty,2); ?></td>
            <td>Ctns</td>
        </tr>
        <tr>
        	<td  align="right">Total Quantity:&nbsp;</td>
            <td align="right"><? echo number_format($total_qnty,2); ?></td>
            <td><? echo $last_uom; ?></td>
        </tr>
        <tr>
        	<td align="right">Total Net Wt:&nbsp;</td>
            <td align="right"><? echo number_format($inv_master_data[0][csf("carton_net_weight")],2); ?></td>
            <td>KG</td>
        </tr>
        <tr>
        	<td align="right">Total Gross Wt:&nbsp;</td>
            <td align="right"><? echo number_format($inv_master_data[0][csf("carton_gross_weight")],2); ?></td>
            <td>KG</td>
        </tr>
        <tr>
        	<td align="right">Total CBM:&nbsp;</td>
            <td align="right"><? echo number_format($inv_master_data[0][csf("cbm_qnty")],2); ?></td>
            <td>CBM</td>
        </tr>
    </table>

	<?
	exit();
}

if ($action=="invoice_report_print")
{
	extract($_REQUEST);
	$ajax_data = explode("|",$data);

	$data = $ajax_data[0];
	$additional_info = $ajax_data[1];

	$company_name_sql=sql_select( "select id, company_name, company_short_name, contract_person, plot_no, level_no, road_no, block_no, city, country_id from lib_company");

	foreach($company_name_sql as $row)
	{
		$company_name_arr[$row[csf("id")]]["company_name"]=$row[csf("company_name")];
		$company_name_arr[$row[csf("id")]]["company_short_name"]=$row[csf("company_short_name")];
		$company_name_arr[$row[csf("id")]]["contract_person"]=$row[csf("contract_person")];
		$company_name_arr[$row[csf("id")]]["plot_no"]=$row[csf("plot_no")];
		$company_name_arr[$row[csf("id")]]["level_no"]=$row[csf("level_no")];
		$company_name_arr[$row[csf("id")]]["road_no"]=$row[csf("road_no")];
		$company_name_arr[$row[csf("id")]]["block_no"]=$row[csf("block_no")];
		$company_name_arr[$row[csf("id")]]["city"]=$row[csf("city")];
		$company_name_arr[$row[csf("id")]]["country_id"]=$row[csf("country_id")];

		$company_name_arr["company_name"]=$row[csf("company_name")];

	}
	$location_sql=sql_select( "select id, location_name, company_id,contact_no,address,email, contact_person, country_id from lib_location ");

	foreach ($location_sql as $value) {
		//$location_arr[$value[csf("company_id")]]["location_id"] = $value[csf("id")];
		$location_arr[$value[csf("company_id")]][$value[csf("id")]]["location_name"] = $value[csf("location_name")];
		$location_arr[$value[csf("company_id")]]["contact_no"] = $value[csf("contact_no")];
		$location_arr[$value[csf("company_id")]]["address"] = $value[csf("address")];
		$location_arr[$value[csf("company_id")]]["email"] = $value[csf("email")];
		$location_arr[$value[csf("company_id")]]["contract_person"] = $value[csf("contract_person")];
		$location_arr[$value[csf("company_id")]]["country_id"] = $value[csf("country_id")];
	}

	$lien_bank_info_sql=sql_select( "select id, bank_name, branch_name,swift_code,contact_no,address,email, contact_person from lib_bank where lien_bank=1 ");
	foreach ($lien_bank_info_sql as $value) {
		$lien_bank_arra[$value[csf("id")]]["bank_name"] = $value[csf("bank_name")];
		$lien_bank_arra[$value[csf("id")]]["address"] = $value[csf("address")];
		$lien_bank_arra[$value[csf("id")]]["branch_name"] = $value[csf("branch_name")];
		$lien_bank_arra[$value[csf("id")]]["swift_code"] = $value[csf("swift_code")];
		$lien_bank_arra[$value[csf("id")]]["email"] = $value[csf("email")];
		$lien_bank_arra[$value[csf("id")]]["contact_person"] = $value[csf("contact_person")];
		$lien_bank_arra[$value[csf("id")]]["contact_no"] = $value[csf("contact_no")];
	}

	$issueing_bank_info_sql = sql_select( "select id, bank_name, branch_name,swift_code,contact_no,address,email, contact_person from lib_bank where issusing_bank=1 ");
	foreach($issueing_bank_info_sql as $row ){
		$issuing_bank_arr[$row[csf("id")]]["bank_name"] = $row[csf("bank_name")];
		$issuing_bank_arr[$row[csf("id")]]["branch_name"] = $row[csf("branch_name")];
		$issuing_bank_arr[$row[csf("id")]]["swift_code"] = $row[csf("swift_code")];
		$issuing_bank_arr[$row[csf("id")]]["address"] = $row[csf("address")];
		$issuing_bank_arr[$row[csf("id")]]["email"] = $row[csf("email")];
		$issuing_bank_arr[$row[csf("id")]]["contact_person"] = $row[csf("contact_person")];
		$issuing_bank_arr[$row[csf("id")]]["contact_no"] = $row[csf("contact_no")];
	}

	$sesson_arr_res = sql_select("select id, buyer_id, season_name from lib_buyer_season where status_active =1 and is_deleted=0 order by season_name ASC");
	foreach($sesson_arr_res as $row ){
		$season_array[$row[csf("buyer_id")]][$row[csf("id")]] = $row[csf("season_name")];
	}
	//var_dump($issuing_bank_arr);

	$applicant_sql = sql_select( "select a.id, a.buyer_name, a.short_name, a.address_1, a.exporters_reference, b.party_type from lib_buyer a,  lib_buyer_party_type b where a.id=b.buyer_id and b.party_type in(4,5,6,22,23,100)");
	foreach($applicant_sql as $row)
	{
		$buyer_name_arr[$row[csf("id")]]["buyer_name"]=$row[csf("buyer_name")];
		$buyer_name_arr[$row[csf("id")]]["exporters_reference"]=$row[csf("exporters_reference")];
		$buyer_name_arr[$row[csf("id")]]["address_1"]=$row[csf("address_1")];
	}

	$inv_master_data=sql_select("select a.id, a.benificiary_id, a.buyer_id, a.location_id, a.invoice_no, a.invoice_date, a.exp_form_no, a.exp_form_date, a.is_lc, a.lc_sc_id,  a.bl_no, a.feeder_vessel, a.inco_term, a.inco_term_place, a.shipping_mode, a.port_of_entry, a.port_of_loading, a.port_of_discharge, a.gross_weight, a.cbm_qnty, a.discount_ammount, a.bonus_ammount, a.commission, a.total_carton_qnty, a.bl_date, a.category_no, a.hs_code, a.place_of_delivery, a.net_weight, a.consignee, a.notifying_party, a.item_description, a.bonus_in_percent, a.claim_in_percent, a.forwarder_name, a.container_no, b.current_invoice_qnty, b.current_invoice_rate,b.current_invoice_value, c.id as po_id, c.po_number, d.job_no, d.agent_name,d.season_buyer_wise, d.style_ref_no, d.order_uom, a.carton_net_weight, a.carton_gross_weight
	from com_export_invoice_ship_mst a, com_export_invoice_ship_dtls b, wo_po_break_down c, wo_po_details_master d
	where a.id=b.mst_id and b.po_breakdown_id=c.id and c.job_no_mst=d.job_no and a.id=$data");

	foreach($inv_master_data as $row){
		$po_details_array[$row[csf("po_id")]]["current_invoice_qnty"] = $row[csf("current_invoice_qnty")];
		$po_details_array[$row[csf("po_id")]]["current_invoice_rate"] = $row[csf("current_invoice_rate")];
		$po_details_array[$row[csf("po_id")]]["current_invoice_value"]= $row[csf("current_invoice_value")];
		$po_numbers.=$row[csf("po_number")].",";
		$item_description=$row[csf("item_description")];
	}
	$po_numbers = chop($po_numbers,",");

	if($inv_master_data[0][csf("is_lc")]==1)
	{
		$lc_sc_data=sql_select("select id, export_lc_no, lc_date, notifying_party, consignee, issuing_bank_name, negotiating_bank, lien_bank, pay_term, applicant_name from com_export_lc where id='".$inv_master_data[0][csf("lc_sc_id")]."' ");
		foreach($lc_sc_data as $row)
		{
			$lc_sc_id=$row[csf("id")];
			$lc_sc_no=$row[csf("export_lc_no")];
			$lc_sc_date=change_date_format($row[csf("lc_date")]);
			$notifying_party=$row[csf("notifying_party")];
			$consignee=$row[csf("consignee")];//also notify party
			$issuing_bank_name=$row[csf("issuing_bank_name")];
			$negotiating_bank=$row[csf("lien_bank")];
			$pay_term_id=$row[csf("pay_term")];
			$applicant_name=$row[csf("applicant_name")];
			
			
		}

			$cate_hs_sql=sql_select("select wo_po_break_down_id, fabric_description, category_no, hs_code from com_export_lc_order_info where com_export_lc_id='".$inv_master_data[0][csf("lc_sc_id")]."'");
			foreach($cate_hs_sql as $row)
			{
				$order_la_data[$row[csf("wo_po_break_down_id")]]["category_no"]=$row[csf("category_no")];
				$order_la_data[$row[csf("wo_po_break_down_id")]]["hs_code"]=$row[csf("hs_code")];
				$order_la_data[$row[csf("wo_po_break_down_id")]]["fabric_description"]=$row[csf("fabric_description")];
				$all_order_id.=$row[csf("wo_po_break_down_id")].", ";
			}
	}
	else
	{
		$lc_sc_data=sql_select("select id, contract_no, contract_date, notifying_party, consignee, lien_bank, pay_term, applicant_name from com_sales_contract where id='".$inv_master_data[0][csf("lc_sc_id")]."'  and status_active=1");
		foreach($lc_sc_data as $row)
		{
			$lc_sc_id=$row[csf("id")];
			$lc_sc_no=$row[csf("contract_no")];
			$lc_sc_date=change_date_format($row[csf("contract_date")]);
			$notifying_party=$row[csf("notifying_party")];
			$consignee=$row[csf("consignee")];//also notify party
			$item_description=$row[csf("item_description")];
			$negotiating_bank=$row[csf("lien_bank")];
			$pay_term_id=$row[csf("pay_term")];
			$applicant_name=$row[csf("applicant_name")];
		}

		$cate_hs_sql=sql_select("select wo_po_break_down_id, fabric_description, category_no, hs_code from com_sales_contract_order_info where com_sales_contract_id='".$inv_master_data[0][csf("lc_sc_id")]."' and status_active=1");
		foreach($cate_hs_sql as $row)
		{
			$order_la_data[$row[csf("wo_po_break_down_id")]]["category_no"]=$row[csf("category_no")];
			$order_la_data[$row[csf("wo_po_break_down_id")]]["hs_code"]=$row[csf("hs_code")];
			$order_la_data[$row[csf("wo_po_break_down_id")]]["fabric_description"]=$row[csf("fabric_description")];
			$all_order_id.=$row[csf("wo_po_break_down_id")].", ";
		}
	}
	//var_dump($lc_sc_data);
	$all_order_id=chop($all_order_id, " , ");

	

	//var_dump($art_num_arr);
	$beneficiary_company=$inv_master_data[0][csf("benificiary_id")]["company_name"];
	$payment_mode = ($inv_master_data[0][csf("is_lc")] == 1 ) ? "LC":"SC";
	$job_no = $inv_master_data[0][csf("job_no")];

	$agent_arr_res = sql_select("select a.id,a.buyer_name, a.address_1, a.address_2, a.address_3 from lib_buyer a, lib_buyer_tag_company b where a.status_active =1 and a.is_deleted=0 and b.buyer_id=a.id and b.tag_company='$beneficiary_company' and a.id in (select  buyer_id from  lib_buyer_party_type where party_type in (1,4,6,20,21))  order by buyer_name");

	
	foreach ($agent_arr_res as  $value) {
		
		$agent_array[$value[csf("id")]]["buyer_name"]= $value[csf("buyer_name")];
		if($value[csf("address_1")] != ""){
			$agent_array[$value[csf("id")]]["address"]= $value[csf("address_1")];
		}
		elseif($value[csf("address_2")] != ""){
			$agent_array[$value[csf("id")]]["address"]= $value[csf("address_2")];
		}
		elseif($value[csf("address_3")] != ""){
			$agent_array[$value[csf("id")]]["address"]= $value[csf("address_3")];
		}
		
	}


	$export_lc_details_res = sql_select("select id, export_lc_no, lc_date, notifying_party, issuing_bank_name, pay_term, tenor, lien_date from com_export_lc where id=$lc_sc_id and is_deleted = 0 and status_active=1 ");

	foreach ($export_lc_details_res as $row){
		$export_lc_array[$row[csf("id")]]["export_lc_no"] = $row[csf("export_lc_no")];
		$export_lc_array[$row[csf("id")]]["lc_date"] = $row[csf("lc_date")];
		$export_lc_array[$row[csf("id")]]["notifying_party"] = $row[csf("notifying_party")];
		$export_lc_array[$row[csf("id")]]["pay_term"] = $row[csf("pay_term")];
		$export_lc_array[$row[csf("id")]]["issuing_bank_name"] = $row[csf("issuing_bank_name")];
		$export_lc_array[$row[csf("id")]]["tenor"] = $row[csf("tenor")];
		$export_lc_array[$row[csf("id")]]["lien_date"] = $row[csf("lien_date")];
	}
	//var_dump($export_lc_details_res);
	//$bank_arr=return_library_array( "select id, bank_name from lib_bank",'id','bank_name');

	?>
	<table id="" cellspacing="0" cellpadding="0" width="690">
		<tr>
			<td colspan="2" align="center" style="font-size:18px; font-weight:bold">
				<?
					echo $company_name_arr[$inv_master_data[0][csf("benificiary_id")]]["company_name"]."<br/>";

				?>

			</td>
		</tr>
		<tr>
			<td colspan="2">
			<?
				$comany_details ="  ";
				$plot_no=$company_name_arr[$inv_master_data[0][csf("benificiary_id")]]["plot_no"];
				$level_no=$company_name_arr[$inv_master_data[0][csf("benificiary_id")]]["level_no"];
				$road_no=$company_name_arr[$inv_master_data[0][csf("benificiary_id")]]["road_no"];
				$block_no=$company_name_arr[$inv_master_data[0][csf("benificiary_id")]]["block_no"];
				$country_id=$company_name_arr[$inv_master_data[0][csf("benificiary_id")]]["country_id"];

				if($plot_no!="")  $comany_details= $plot_no.", ";
				if($level_no!="")  $comany_details.= $level_no.", ";
				if($road_no!="")  $comany_details.= $road_no.", ";
				if($block_no!="")  $comany_details.= $block_no.", ";
				$comany_details.= $location_arr[$inv_master_data[0][csf("benificiary_id")]][$inv_master_data[0][csf("location_id")]]["location_name"].", ";
				if($country_id!="")  $comany_details.=return_field_value( "country_name","lib_country","id='$country_id'")." . ";

					echo $location_arr[$inv_master_data[0][csf("benificiary_id")]]["address"]
					. $comany_details
					. $location_arr[$inv_master_data[0][csf("benificiary_id")]]["email"];

			?>

			</td>
		</tr>

		

	</table>
	<? 
		$issuing_bank_details = explode(",", $export_lc_array[$inv_master_data[0][csf("lc_sc_id")]]["issuing_bank_name"]);
		
		for( $i=0; $i<count($issuing_bank_details); $i++){
			$issuing_bank_details["bank_name"] = $issuing_bank_details[0];
			$issuing_bank_details["branch_name"] = $issuing_bank_details[1];
			$issuing_bank_details["level"] = $issuing_bank_details[2];
			$issuing_bank_details["road"] = $issuing_bank_details[3];
			$issuing_bank_details["country"] = $issuing_bank_details[4];
			$issuing_bank_details["swift"] = $issuing_bank_details[5];
			$issuing_bank_details["att"] = $issuing_bank_details[6];
		}
		 //var_dump($issuing_bank_details);
			
	?>
	<table  cellspacing="0" cellpadding="0" width="690" rules="all" border="1" style="font-size:11px;">
		<tr>
			<td colspan="2" align="center" style="font-size:16px; font-weight:bold"><? echo "Commercial Invoice"; ?></td>
		</tr>
		<tr>
			<td width="430">
			<strong>Invoice To: </strong><? echo $agent_array[$inv_master_data[0][csf("agent_name")]]["buyer_name"]; ?><br/>
				<? echo $agent_array[$inv_master_data[0][csf("agent_name")]]["address"]; ?>
			</td>
			<td width="260">
				<table rules="all" border="1" style="font-size:11px;" width="100%">
					<tr>
						<td align="center"><strong>Invoice Number</strong></td>
					</tr>
					<tr>
						<td ><? echo $inv_master_data[0][csf("invoice_no")]; ?></td>
					</tr>
					<tr>
						<td>Date: <? echo $inv_master_data[0][csf("invoice_date")]; ?></td>
					</tr>
				</table>				
			</td>

		</tr>
	</table> <br/>
	<table id="" cellspacing="0" cellpadding="0" border="0" rules="all" width="690" class="rpt_table" style="font-size:11px;">
		<tr>
			<td width="330" valign="top" style="border:0;">
			<h3>Bank Information :</h3>
				<strong>Bank Name :</strong> <? echo $lien_bank_arra[$negotiating_bank]["bank_name"]; ?><br/>
				<strong>Bank Address :</strong><? echo $lien_bank_arra[$negotiating_bank]["address"]; ?><br/>
				
				<strong>Swift Code :</strong><? echo $lien_bank_arra[$negotiating_bank]["swift_code"]; ?><br/>
				<strong>Vendor Code : </strong><? echo $buyer_name_arr[$inv_master_data[0][csf("buyer_id")]]["exporters_reference"] ?>,<br/>
				<strong>Season :</strong> <? echo $season_array[$inv_master_data[0][csf("buyer_id")]][$inv_master_data[0][csf("season_buyer_wise")]]; ?>,<br/>
				<strong>LC Ref.no.</strong> sg40006  <br/>

			</td>
			<td width="360" style="border:0;">			
				<h3>LC Issuing Bank :</h3>
				<strong>Bank Name :</strong> <? echo $issuing_bank_details["bank_name"]; ?><br/>
				<strong>Bank Address :</strong><? echo $issuing_bank_details["branch_name"] .",". $issuing_bank_details["level"].",".$issuing_bank_details["road"].",".$issuing_bank_details["country"]; ?> <br/>
				<strong>SWIFT :</strong><? echo $issuing_bank_details["swift"]; ?> <br/>
				<strong>ATTN :</strong><? echo $issuing_bank_details["att"]; ?> <br/>
				<strong>Payment Mode : </strong><? echo $payment_mode; ?> <br/>
				<strong>Maturity :</strong> <? echo $export_lc_array[$inv_master_data[0][csf("lc_sc_id")]]["lien_date"];?> <br/>
				<strong>Payment Term :</strong><? echo $export_lc_array[$inv_master_data[0][csf("lc_sc_id")]]["tenor"];?> <br/>
				<strong>LC no </strong><? echo $export_lc_array[$inv_master_data[0][csf("lc_sc_id")]]["export_lc_no"];?> <br/>
				<strong>Date:</strong><? echo $export_lc_array[$inv_master_data[0][csf("lc_sc_id")]]["lc_date"];?> 
			</td>
		</tr>

	</table>
	<br>
	<?
		$item_description_details = "select id, job_no, construction, composition, fabric_description, nominated_supp, uom from wo_pre_cost_fabric_cost_dtls where job_no like '%$job_no%' ";
		//echo $item_description_details;
		$result_item_des=sql_select($item_description_details);
		foreach($result_item_des as $row)
		{			
			$item_details_array["job_no"] = $row[csf("job_no")];
			$item_details_array["composition"] = $row[csf("composition")];
			$item_details_array["fabric_description"] = $row[csf("fabric_description")];
			$item_details_array["nominated_supp"] = $row[csf("nominated_supp")];
			$item_details_array["uom"] = $row[csf("uom")];
		}

		$company = $inv_master_data[0][csf("benificiary_id")];

		$supplier_details = sql_select("select a.id,a.supplier_name,a.country_id from lib_supplier a, lib_supplier_tag_company c where a.id=c.supplier_id and a.status_active =1 and a.id in(select supplier_id from lib_supplier_party_type where party_type  in(26,30,31,32)) group by a.id,a.supplier_name,a.country_id order by supplier_name");

		foreach($supplier_details as $row){
			$supplier_details_arr[$row[csf("id")]]["supplier_name"] = $row[csf("supplier_name")];
			$supplier_details_arr[$row[csf("id")]]["country_id"] = $row[csf("country_id")];
		}

		$country_arr = return_library_array("select id, country_name from lib_country","id","country_name");
		
	?>
	<table id="" cellspacing="0" cellpadding="0" border="1" rules="all" width="690" class="rpt_table"  style="font-size:9px;" >
		<thead>
			<tr>
				<th width="330">Details of Item</th>
				<th width="100">PO Numbers</th>
				<th width="80">Quantity</th>
				<th width="80" >Unit Price</th>
				<th width="100">Amount (USD)</th>
			</tr>
		</thead>
		<tbody>
			<tr>
				<td style="padding:10px;">
					<strog>Item Description: </strong> <? echo $item_description; ?><br/>
					<strog>Composition: </strong> <? echo $item_details_array["composition"]; ?><br/>
					<table style="font-size:9px;">
						<tr>
							<td width="80%"><strog>PO No : </strong> <? echo $po_numbers; ?></td>
							<td><strog>Style : </strong> <? echo $inv_master_data[0][csf("style_ref_no")]; ?></td>
						</tr>
						<tr>
							<td><strog>Cat: </strong> <? echo $inv_master_data[0][csf("category_no")]; ?></td>
							<td><strog>HS Code : </strong> <? echo $inv_master_data[0][csf("hs_code")]; ?></td>
						</tr>
						<tr>
							<td><strog>Origin of Goods : </strong> Bangladesh <br/>
								<strog>Origin of Fabrics : </strong> <? echo $country_arr[$supplier_details_arr[$item_details_array["nominated_supp"]]["country_id"]];?> 
							</td>
						</tr>
					</table>
				</td>
				<td colspan="4">
					<table style="font-size:9px;" cellspacing="0" cellpadding="0" border="1" rules="all" class="rpt_table">
					<? 
						
						foreach ($inv_master_data as $row){
					?>
						<tr>
						
							<td width="100"><? echo $row[csf("po_number")]; ?></td>
							
							<td width="100" align="right"><? echo number_format($row[csf("current_invoice_qnty")],2); ?></td>
							<td width="100" align="right"><? echo number_format($row[csf("current_invoice_rate")],5); ?></td>
							<td width="100" align="right"><? echo number_format($row[csf("current_invoice_value")],2); ?></td>
							
						</tr>
						<? 
							$total_qnty += $row[csf("current_invoice_qnty")];
							$total_value += $row[csf("current_invoice_value")];

							$bonus_percent = $row[csf("bonus_in_percent")];
							$claim_percent = $row[csf("claim_in_percent")];
							$uom = $row[csf("order_uom")];
							$consignee = $row[csf("consignee")];
							$notifying_party = $row[csf("notifying_party")];
							$place_of_delivery = $row[csf("place_of_delivery")];
							$forworder = $row[csf("forwarder_name")];
							$shipping_mode = $row[csf("shipping_mode")];
							$inco_term = $row[csf("inco_term")];
							$port_of_loading = $row[csf("port_of_loading")];
							$port_of_discharge = $row[csf("port_of_discharge")];
							$feeder_vessel = $row[csf("feeder_vessel")];
							$bl_no = $row[csf("bl_no")];
							$bl_date = $row[csf("bl_date")];
							$net_weight = $row[csf("carton_net_weight")];
							$gross_weight = $row[csf("carton_gross_weight")];
							$cbm_qnty = $row[csf("cbm_qnty")];
							$total_carton_qnty = $row[csf("total_carton_qnty")];
							$container_no = $row[csf("container_no")];

							$sub_total = $total_value - (($total_value*$claim_percent)/100);
							$net_total = $sub_total - (($sub_total*$bonus_percent)/100);
						}
						?>
					</table>
				</td>
			</tr>
			<tr>
				
				<td  width="330" align="right">Total :</td>			
				<td width="100"></td>
				<td width="80"  align="right"><? echo number_format($total_qnty,2); ?></td>
				<td width="80" align="right" >&nbsp;</td>
				<td width="100" align="right" ><? echo number_format($total_value,2); ?></td>
				
			</tr>
			<tr>
				
				<td  width="330" style="border:0;"></td>	
				<td  width="360" colspan="4">
					<table style="font-size:10px;">
						<tr>
							<td width="100" align="right">Total :</td>
							<td width="80"  align="right"><? echo $total_qnty." ".$unit_of_measurement[$uom];?> </td>
							<td width="80" align="right" >&nbsp;</td>
							<td width="100" align="right" ><? echo number_format($total_value,2); ?></td>
						</tr>
						<tr>
							<td width="100" align="right"></td>
							<td width="80"  align="right">Late Penalty :</td>
							<td width="80" align="right" ><? echo $claim_percent;?></td>
							<td width="100" align="right" ><? echo number_format((($total_value*$claim_percent)/100),2); ?></td>
						</tr>
						<tr>
							<td width="100" align="right">Sub Total :</td>
							<td width="80"  align="right"></td>
							<td width="80" align="right" ></td>
							<td width="100" align="right" ><? echo number_format($sub_total,2); ?></td>
						</tr>
						<tr>
							<td width="100" align="right"></td>
							<td width="80"  align="right">Annual Bonus :</td>
							<td width="80" align="right" ><? echo $bonus_percent;?></td>
							<td width="100" align="right" ><? echo number_format((($sub_total*$bonus_percent)/100),2); ?></td>
						</tr>
						<tr>
							<td width="100" align="right">Net Total :</td>
							<td width="80"  align="right"></td>
							<td width="80" align="right" ></td>
							<td width="100" align="right" ><? echo number_format($net_total,2); ?></td>
						</tr>
					</table>
				</td>
				
			</tr>		
		</tbody>
	</table><br/>
	
	<table cellspacing="0" cellpadding="0" rules="all" width="690" border = "0" style="font-size:10px;" >
		<tr>
			<td width="330" style="padding:10px; border:0;">
				<strong>1st Notify :</strong><br/>
				<? echo $agent_array[$inv_master_data[0][csf("consignee")]]["buyer_name"];?><br/>
				<? $address = explode(",", $agent_array[$inv_master_data[0][csf("consignee")]]["address"]);
					foreach($address as $row){
						echo $row."<br/>";
					}
					
				?>
				
			</td>
			<td width="360" valign="top" style="padding:10px; border:0;">
			<strong>2nd Notify :</strong><br/>
				<? echo $agent_array[$inv_master_data[0][csf("notifying_party")]]["buyer_name"]; ?><br/>
				<? echo $agent_array[$inv_master_data[0][csf("notifying_party")]]["address"]?><br/>
			</td>
		</tr>
		<tr>
			<td width="330" style="padding:10px; border:0;">
				<strong>Delivery Address :</strong><br/>
				<?
					$place_of_delivery = explode(",", $place_of_delivery);
					foreach($place_of_delivery as $row){
						echo $row."<br/>";
					}
				?>
				
				
			</td>
			<td width="360" style="padding:10px; border:0;">
			<strong>Forworder :</strong><? echo $supplier_details_arr[$forworder]["supplier_name"]?><br/>
			<strong>Shipment Mode :</strong><? echo $shipment_mode[$shipping_mode]; ?><br/>
			<strong>Incoterm :</strong><? echo $incoterm[$inco_term]; ?><br/>
			<strong>From :</strong><? echo $port_of_loading; ?><br/>
			<strong>To :</strong><? echo $port_of_discharge; ?>
			</td>
		</tr>
		<tr>
			<td width="330" style="padding:10px; border:0;">
				<strong>FRC No :</strong><? echo $bl_no;?><br/>
				<strong>Date :</strong><? echo $bl_date;?><br/>
				<strong>Total Quantity :</strong><? echo $total_qnty." ".$unit_of_measurement[$uom];?><br/>
				<strong>Total Cartons :</strong><?echo $total_carton_qnty; ?><br/>
				
				
				
			</td>
			<td width="360" style="padding:10px; border:0;">
			<strong>Vessel Name :</strong><? echo $feeder_vessel;?><br/>
			<strong>Container No.:</strong><? echo $container_no; ?><br/>
			<strong>Total Net Weight (kg):</strong><? echo $net_weight; ?><br/>
			<strong>Total grs. Weight (kg):</strong><? echo $gross_weight; ?><br/>
			<strong>Total Volume (CBM):</strong><? echo $cbm_qnty; ?>
			</td>
		</tr>
	</table>
	<?
	exit();
}

if ($action=="print_invoice_CIHnM")
{
	extract($_REQUEST);
	$sql="select a.id as ID, a.invoice_no as INVOICE_NO, a.invoice_date as INVOICE_DATE, a.buyer_id as BUYER_ID, a.location_id as LOCATION_ID, a.benificiary_id as BENIFICIARY_ID, a.is_lc as IS_LC, a.lc_sc_id as LC_SC_ID, a.exp_form_no as EXP_FORM_NO, a.exp_form_date as EXP_FORM_DATE, a.country_id as COUNTRY_ID, a.cargo_delivery_to as CARGO_DELIVERY_TO, a.notifying_party as NOTIFYING_PARTY, a.consignee as CONSIGNEE, b.export_lc_no as CONTRACT_NO, b.lc_date as CONTRACT_DATE, b.lien_bank as LIEN_BANK from com_export_invoice_ship_mst a, com_export_lc b where a.lc_sc_id=b.id and a.is_lc=1 and a.id=$data and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0
	union all
	select a.id as ID, a.invoice_no as INVOICE_NO, a.invoice_date as INVOICE_DATE, a.buyer_id as BUYER_ID, a.location_id as LOCATION_ID, a.benificiary_id as BENIFICIARY_ID, a.is_lc as IS_LC, a.lc_sc_id as LC_SC_ID, a.exp_form_no as EXP_FORM_NO, a.exp_form_date as EXP_FORM_DATE, a.country_id as COUNTRY_ID, a.cargo_delivery_to as CARGO_DELIVERY_TO, a.notifying_party as NOTIFYING_PARTY, a.consignee as CONSIGNEE, b.contract_no as CONTRACT_NO, b.contract_date as CONTRACT_DATE, b.lien_bank as LIEN_BANK from com_export_invoice_ship_mst a, com_sales_contract b where a.lc_sc_id=b.id and a.is_lc=2 and a.id=$data and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0";
	$sql_res=sql_select($sql);

	$sql_order_res=sql_select("select b.id as PO_ID, b.po_number as PO_NUMBER from com_export_invoice_ship_dtls a, wo_po_break_down b where a.po_breakdown_id=b.id and a.mst_id=$data and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0");
	$order_arr=array();
	foreach($sql_order_res as $row){
		$order_arr[$row['PO_ID']]=$row['PO_NUMBER'];
	}
	$order_nos=implode(', ',$order_arr);

	$company_arr = return_library_array("select id, company_name from lib_company ","id","company_name");
	$sql_location=sql_select( "select a.id as ID, a.location_name as LOCATION_NAME, a.address as LOC_ADDRESS from lib_location a where  a.status_active=1 and a.is_deleted=0");
	$location_arr=array();
	foreach($sql_location as $row)
	{
		$location_arr[$row["ID"]]["LOCATION_NAME"]=$row["LOCATION_NAME"];
		$location_arr[$row["ID"]]["LOC_ADDRESS"]=$row["LOC_ADDRESS"];
	}

	$sql_buyer = sql_select( "select a.id as ID, a.buyer_name as BUYER_NAME, a.address_1 as BUYER_ADDRESS from lib_buyer a");
	$buyer_arr=array();
	foreach($sql_buyer as $row)
	{
		$buyer_arr[$row["ID"]]["BUYER_NAME"]=$row["BUYER_NAME"];
		$buyer_arr[$row["ID"]]["BUYER_ADDRESS"]=$row["BUYER_ADDRESS"];
	}

	$sql_lien_bank_info=sql_select( "select id as ID, bank_name as BANK_NAME, branch_name as BRANCH_NAME, swift_code as SWIFT_CODE, contact_no as CONTACT_NO, address as ADDRESS from lib_bank ");
	foreach ($sql_lien_bank_info as $row) {
		$lien_bank_arra[$row["ID"]]["BANK_NAME"] = $row["BANK_NAME"];
		$lien_bank_arra[$row["ID"]]["ADDRESS"] = $row["ADDRESS"];
	}	
	ob_start();
	?>
	<style>
		p{margin: 2px;}
	</style>
	<table align="center" border="1" cellpadding="1" cellspacing="1" style="width:1200px;" rules="all">
		<tr><td colspan="6"><h1 style="margin: 0; text-align: center;">INVOICE</h1></td></tr>
		<tr>
			<td width="600" style="border-bottom:none;" colspan="3">
				<p><strong><? echo $company_arr[$sql_res[0]['BENIFICIARY_ID']]; ?></strong><p>
				<p><? echo $location_arr[$sql_res[0]['LOCATION_ID']]["LOCATION_NAME"]; ?>&nbsp;</p>
				<p><? echo $location_arr[$sql_res[0]['LOCATION_ID']]["LOC_ADDRESS"]; ?>&nbsp;</p>
				<p>&nbsp;</p>
				<p>&nbsp;</p>
				<p>&nbsp;</p>
				<p>&nbsp;</p>
				<p>&nbsp;</p>
			</td>
			<td width="400" colspan="2">
				<p style="text-decoration: underline;">Invoice no and date:<p>
				<p>Number : <? echo $sql_res[0]['INVOICE_NO']; ?>&nbsp;</p>
				<p>Date : <? echo change_date_format($sql_res[0]['INVOICE_DATE']); ?>&nbsp;</p>
				<p>&nbsp;</p>
				<p>&nbsp;</p>
				<p>&nbsp;</p>
				<p>&nbsp;</p>
				<p>&nbsp;</p>
			</td>
			<td width="200">
				<p style="text-decoration: underline;">Exporters ref: </p>
				<p>Contact no: <? echo $sql_res[0]['CONTRACT_NO']; ?>&nbsp;</p>
				<p>Date: <? echo change_date_format($sql_res[0]['CONTRACT_DATE']); ?>&nbsp;</p>
				<p>Exp No: <? echo $sql_res[0]['EXP_FORM_NO']; ?>&nbsp;</p>
				<p>Date: <? echo change_date_format($sql_res[0]['EXP_FORM_DATE']); ?>&nbsp;</p>
				<p>&nbsp;</p>
				<p>&nbsp;</p>
				<p>&nbsp;</p>
			</td>
		</tr>			
		<tr>
			<td colspan="3" width="600" style="border-top:none;">&nbsp;</td>
			<td colspan="3" width="600"><strong>H&M order no: <? echo $order_nos; ?></strong></td>
		</tr>
		<tr>
			<td colspan="3" width="600">
				<p><strong>Buyer :</strong></p>
				<p><? echo $buyer_arr[$sql_res[0]['BUYER_ID']]["BUYER_NAME"]; ?>&nbsp;</p>
				<p><? echo $buyer_arr[$sql_res[0]['BUYER_ID']]["BUYER_ADDRESS"]; ?>&nbsp;</p>
				<p>&nbsp;</p>
				<p>&nbsp;</p>
				<p>&nbsp;</p>
				<p>&nbsp;</p>				
				<p>&nbsp;</p>
			</td>
			<td colspan="3" width="600">
				<p><strong>Cargo Delivery to :</strong></p>
				<p><? echo $buyer_arr[$sql_res[0]['NOTIFYING_PARTY']]["BUYER_NAME"]; ?>&nbsp;</p>
				<p><? echo $buyer_arr[$sql_res[0]['NOTIFYING_PARTY']]["BUYER_ADDRESS"]; ?>&nbsp;</p>
				<p>&nbsp;</p>
				<p>&nbsp;</p>
				<p>&nbsp;</p>
				<p>&nbsp;</p>				
				<p>&nbsp;</p>
			</td>
		</tr>
		<tr>
			<td colspan="3" width="600">
				<p><strong>Consignee :</strong></p>
				<p><? echo $buyer_arr[$sql_res[0]['CONSIGNEE']]["BUYER_NAME"]; ?>&nbsp;</p>
				<p><? echo $buyer_arr[$sql_res[0]['CONSIGNEE']]["BUYER_ADDRESS"]; ?>&nbsp;</p>
				<p>&nbsp;</p>
				<p>&nbsp;</p>
				<p>&nbsp;</p>
				<p>&nbsp;</p>				
				<p>&nbsp;</p>
			</td>
			<td colspan="2" width="400">
				<p style="vertical-align:top;">Country of origin of  goods :</p>
				<p>&nbsp;</p>
				<p>&nbsp;</p>
				<p>&nbsp;</p>
				<p style="text-align: center;"><strong>Bangladesh</strong></p>				
				<p>&nbsp;</p>
				<p>&nbsp;</p>				
				<p>&nbsp;</p>
			</td>
			<td width="200">
				<p style="vertical-align:top;">Country of final destination:</p>
				<p>&nbsp;</p>
				<p>&nbsp;</p>
				<p>&nbsp;</p>
				<p>&nbsp;</p>
				<p>&nbsp;</p>
				<p>&nbsp;</p>
				<p>&nbsp;</p>
			</td>
		</tr>
		<tr>
			<td style="width: 200px;">
				<p style="vertical-align:top;">Vessel / flight no</p>
				<p>&nbsp;</p>
			</td>
			<td colspan="2" style="width: 400px;">
				<p style="vertical-align:top;">Port of loading:<strong>  Chittagong</strong></p>
				<p>&nbsp;</p>
			</td>
			<td colspan="3" width="600">
				<p style="vertical-align:top;">Terms of delivery: <strong> FCA Chittagong</strong></p>
				<p><strong>Sailing Date:</strong></p>
			</td>
		</tr>
		<tr>
			<td style="width: 200px;">
				<p style="vertical-align:top;">Port of discharge:</p>
				<p>&nbsp;</p>
			</td>
			<td colspan="2" style="width: 400px;">
				<p style="vertical-align:top;">Place of delivery:</p>
				<p>&nbsp;</p>
			</td>
			<td colspan="3" width="600">
				<p style="vertical-align:top;">Terms of payment:</p>
				<p>&nbsp;</p>
			</td>
		</tr>
		<tr>
			<td width="200" style="border-top:none; vertical-align:middle;">Marks & nos.</td>
			<td width="200" style="border-top:none; vertical-align:middle;">No & kind of pkg</td>
			<td width="200" style="border-top:none; vertical-align:middle;">Description of goods</td>
			<td width="200" style="border-top:none;" align="center"><p>Quantity</p><p>PACK</p></td>
			<td width="200" style="border-top:none;" align="center"><p>Rate</p><p>USD</p></td>
			<td width="200" style="border-top:none;" align="center"><p>Amount</p><p>USD</p></td>
		</tr>		
		<tr>
			<td colspan="3" width="600">
				<p style="vertical-align: top;">To :</p>
				<p>&nbsp;</p>
				<p style="vertical-align: top;">Order No :</p>
				<p>&nbsp;</p>
				<p>&nbsp;</p>
				<p>&nbsp;</p>
				<p>&nbsp;</p>
				<p>&nbsp;</p>
				<p>&nbsp;</p>
				<p>&nbsp;</p>
				<p>&nbsp;</p>
				<table align="left" border="0" cellpadding="3" cellspacing="1">
					<tr>
						<td colspan="3" width="600">No. of  Pcs in Ctn :</td>
					</tr>
					<tr>
						<td align="left" width="200">Crtn  No :</td>
						<td align="left" width="200"><strong>Total :</strong></td>
						<td align="left" width="200"><strong>Ctns :</strong></td>
					</tr>
					<tr>
						<td width="200">Crtn Msrmnt :</td>
						<td width="200">&nbsp;</td>
						<td width="200">Cat:</td>
					</tr>
					<tr>
						<td width="200">Container No :</td>
						<td width="200">&nbsp;</td>
						<td width="200">Teriff Code :</td>
					</tr>
					<tr>
						<td width="200">Country of manufacture :</td>
						<td width="200">&nbsp;</td>
						<td width="200"><strong>Bangladesh</strong></td>
					</tr>
				</table>
				<table align="left" border="1" cellpadding="1" cellspacing="1" rules="all">
					<tr>
						<td width="100">Country</td>
						<td width="100" align="center">Net Weight</td>
						<td width="100" align="center">Gross Weight</td>
					</tr>
					<tr>
						<td width="100"><strong>&nbsp;</strong></td>
						<td width="100" align="center">&nbsp;</td>
						<td width="100" align="center">&nbsp;</td>
					</tr>
					<tr>
						<td width="100"><strong>Total</strong></td>
						<td width="100" align="center">&nbsp;</td>
						<td width="100" align="center">&nbsp;</td>
					</tr>
				</table>
			</td>
			<td width="200">&nbsp;</td>
			<td width="200">&nbsp;</td>
			<td width="200">&nbsp;</td>
		</tr>
		<tr>
			<td colspan="2" width="400">Dimention:</td>
			<td width="200" align="right"><strong>Total</strong></td>
			<td width="200">&nbsp;</td>
			<td width="200">&nbsp;</td>
			<td width="200">&nbsp;</td>
		</tr>
		<tr>			
			<td colspan="6">
				<p style="vertical-align: top;"><strong>Amount Chargable:</strong><p>
				<p>&nbsp;</p>
			</td>			
		</tr>
		<tr>
			<td colspan="3" width="600" style="vertical-align: top;">
				<p>Declaration:  We declare that this invoice shows the actual</p>
				<p>price of the goods described and that all particulars are</p>
				<p>true and correct.</p>
			</td>
			<td colspan="3" rowspan="2" width="600" rowspan="2" style="vertical-align:middle; text-align:center; text-decoration:overline;">Signature & Date</td>
		</tr>
		<tr>
			<td colspan="3" width="600">
				<p style="text-decoration: underline; vertical-align: top;"><strong>Remarks for Bank Details:-</strong></p>
				<p><strong><? echo $lien_bank_arra[$sql_res[0]['LIEN_BANK']]["BANK_NAME"]; ?></strong>&nbsp;</p>
				<p><strong><? echo $lien_bank_arra[$sql_res[0]['LIEN_BANK']]["ADDRESS"]; ?></strong>&nbsp;</p>
				<p><strong>SWIFT NO: </strong></p>
				<p><strong>ACCOUNT NO: </strong></p>
				<p>&nbsp;</p>
			</td>			
		</tr>
	</table>
	<?
	$html = ob_get_contents();
	ob_clean();
	foreach (glob("tb*.xls") as $filename) {
	@unlink($filename);
	}
	//---------end------------//
	$name=time();
	$filename="tb".$user_id."_".$name.".xls";
	$create_new_doc = fopen($filename, 'w');
	$is_created = fwrite($create_new_doc, $html);
	echo "$filename####$html";
	exit();
}


if ($action=="pdf")
{
	extract($_REQUEST);
	$commercial_invoice=return_field_value( "commercial_invoice","lib_buyer","id='$cbo_buyer_name'");
	require('pdformat/'.$action."_".$commercial_invoice.".php");
	$invoice->show();
    exit();
}
/*if ($action=="pdf_2")
{
	require('pdformat/'.$action.".php");
	$invoice->show();
    exit();
}*/

if ($action=="print_generate")  //develop for wash business
{
	extract($_REQUEST);
	$data = explode("**",$data);

	if($data[1]==1)
	{
		$company_name_sql=sql_select( "select id, company_name, company_short_name, contract_person, plot_no, level_no, road_no, block_no, city, country_id,bin_no from lib_company");
		$color_arr=return_library_array( "select id, color_name from lib_color",'id','color_name');
		foreach($company_name_sql as $row)
		{
			$company_name_arr[$row[csf("id")]]["company_name"]=$row[csf("company_name")];
			$company_name_arr[$row[csf("id")]]["bin_no"]=$row[csf("bin_no")];
			$company_details =" ";
			$plot_no=$row[csf("plot_no")];
			$level_no=$row[csf("level_no")];
			$road_no=$row[csf("road_no")];
			$block_no=$row[csf("block_no")];
			$city_no=$row[csf("city")];
			$country_id=$row[csf("country_id")];

			if($plot_no!="")  $company_details= $plot_no.", ";
			if($level_no!="")  $company_details.= $level_no.", ";
			if($road_no!="")  $company_details.= $road_no.", ";
			if($block_no!="")  $company_details.= $block_no.", ";
			if($country_id!="")  $company_details.=return_field_value( "country_name","lib_country","id='$country_id'")." . ";
			$company_name_arr[$row[csf("id")]]["company_details"]= $company_details;

		}

		$inv_master_data=sql_select("select a.id, a.benificiary_id, a.buyer_id, a.invoice_no, a.invoice_date, a.is_lc as IS_LC, a.lc_sc_id, a.port_of_entry, a.port_of_loading, a.port_of_discharge, b.id as LC_SC_ID, b.export_lc_no as LC_SC_NO, b.lc_date as LC_SC_DATE,b.issuing_bank_name as ISSUING_BANK_NAME
		from com_export_invoice_ship_mst a, com_export_lc b
		where a.id=$data[0] and a.is_lc=1 and a.lc_sc_id=b.id and a.is_deleted=0 and b.is_deleted=0
		union all
		select a.id, a.benificiary_id, a.buyer_id, a.invoice_no, a.invoice_date, a.is_lc as IS_LC, a.lc_sc_id, a.port_of_entry, a.port_of_loading, a.port_of_discharge, b.id as LC_SC_ID, b.contract_no as LC_SC_NO, b.contract_date as LC_SC_DATE,b.issuing_bank as ISSUING_BANK_NAME
		from com_export_invoice_ship_mst a, com_sales_contract b
		where a.id=$data[0] and a.is_lc=2 and a.lc_sc_id=b.id and a.is_deleted=0 and b.is_deleted=0");
		$lc_sc_id=$inv_master_data[0]['LC_SC_ID'];
		$lc_sc_no=$inv_master_data[0]['LC_SC_NO'];
		$is_lc=$inv_master_data[0]['IS_LC'];
		$lc_sc_date=change_date_format($inv_master_data[0]['LC_SC_DATE']);
		$issuing_bank_name=$inv_master_data[0]['ISSUING_BANK_NAME'];
		$issuing_bank_details = explode(",", $issuing_bank_name);
		$issue_bank_details='';
		for( $i=0; $i<count($issuing_bank_details); $i++)
		{
			$issue_bank_name = $issuing_bank_details[0];
			$issue_branch_name = $issuing_bank_details[1];
			$issue_level = $issuing_bank_details[2];
			$issue_road = $issuing_bank_details[3];
			$issue_country = $issuing_bank_details[4];
			// $issue_swift = $issuing_bank_details[5];
			// $issue_att = $issuing_bank_details[6];
			if($issue_branch_name!="")  $issue_bank_details.= $issue_branch_name.", ";
			if($issue_level!="")  $issue_bank_details.= $issue_level.", ";
			if($issue_road!="")  $issue_bank_details= $issue_road.", ";
			if($issue_country!="")  $issue_bank_details.= $issue_country;
		}
		$pi_sql=sql_select("select id,pi_date, pi_number,export_pi_id from com_pi_master_details where id in (select pi_id from com_btb_lc_master_details where lc_number='$lc_sc_no' and is_deleted = 0 and status_active=1 ) and is_deleted = 0 and status_active=1 ");

		$pi_num=$export_pi_id=$pi_date='';
		foreach($pi_sql as $row)
		{
			$pi_num.=$row[csf('pi_number')].', ';
			$pi_date.=change_date_format($row[csf('pi_date')]).', ';
			$export_pi_id.=$row[csf('export_pi_id')].',';
		}

		$btb_lc_attachment_sql=sql_select("select a.id, a.is_lc_sc, a.lc_sc_id ,b.export_lc_no as LC_SC_NO, b.lc_date as LC_SC_DATE
		from com_btb_export_lc_attachment a, com_export_lc b
		where import_mst_id in (select id from com_btb_lc_master_details where lc_number='$lc_sc_no' and is_deleted = 0 and status_active=1 ) and a.is_lc_sc=0 and a.lc_sc_id=b.id and a.is_deleted = 0 and a.status_active=1 and b.is_deleted = 0 and b.status_active=1 
		union all
		select a.id, a.is_lc_sc, a.lc_sc_id ,b.contract_no as LC_SC_NO, b.contract_date as LC_SC_DATE
		from com_btb_export_lc_attachment a, com_sales_contract b
		where import_mst_id in (select id from com_btb_lc_master_details where lc_number='$lc_sc_no' and is_deleted = 0 and status_active=1 ) and a.is_lc_sc=1 and a.lc_sc_id=b.id and a.is_deleted = 0 and a.status_active=1 and b.is_deleted = 0 and b.status_active=1 ");

		$buyer_lc_sc_no='';
		$buyer_lc_sc_date='';
		foreach($btb_lc_attachment_sql as $row)
		{
			$buyer_lc_sc_no.=$row['LC_SC_NO'].', ';
			$buyer_lc_sc_date.=change_date_format($row['LC_SC_DATE']).', ';
		}
		// $export_pi_dtls_sql = "select a.id, a.item_category_id, a.currency_id,a.within_group, b.work_order_no as job_no, b.booking, b.color_id, b.uom, b.quantity, b.rate, b.amount, b.gmts_item_id,c.party_buyer_name, c.id as dtls_id, c.buyer_style_ref, c.buyer_po_no 
		// from com_export_pi_mst a, com_export_pi_dtls b, subcon_ord_dtls c  
		// where a.id=b.pi_id and b.work_order_dtls_id=c.id and a.id in (".chop($export_pi_id,',').") and a.is_deleted=0 and b.is_deleted=0";
		
		$export_pi_dtls_sql = "SELECT  b.current_invoice_qnty as CURRENT_INVOICE_QNTY, b.current_invoice_rate as CURRENT_INVOICE_RATE, b.current_invoice_value as CURRENT_INVOICE_VALUE, c.color_id as COLOR_ID, c.uom as UOM, c.gmts_item_id as GMTS_ITEM_ID, c.embell_name as EMBELL_NAME, c.embell_type as EMBELL_TYPE, d.id as dtls_id, d.buyer_style_ref as BUYER_STYLE_REF
		from  com_export_invoice_ship_dtls b, com_pi_item_details c, subcon_ord_dtls d
		where b.mst_id=$data[0] and b.po_breakdown_id=c.id and c.work_order_dtls_id=d.id and b.is_deleted=0 and c.is_deleted=0 and d.is_deleted=0";
		
		$export_pi_dtls_result = sql_select($export_pi_dtls_sql);
		$all_dtls_id=array();
		foreach($export_pi_dtls_result as $row)
		{
			$all_dtls_id[$row[csf("dtls_id")]]=$row[csf("dtls_id")];
		}
		if(count($all_dtls_id)>0)
		{
			$con_dtls_sql="select mst_id, process, listagg(cast(embellishment_type as varchar(4000)),',') within group (order by embellishment_type) as embellishment_type
			from subcon_ord_breakdown where status_active=1 and is_deleted=0 and mst_id in(".implode(",",$all_dtls_id).")  and embellishment_type>0
			group by mst_id, process";
			//echo $con_dtls_sql;
			$con_dtls_sql_result = sql_select($con_dtls_sql);
			foreach($con_dtls_sql_result as $row)
			{
				$embtype_data[$row[csf("mst_id")]][$row[csf("process")]]=$row[csf("embellishment_type")];
				$embprocess_data[$row[csf("mst_id")]][$row[csf("process")]]=$row[csf("process")];
				
			}
		}
		?>
		<table id="" cellspacing="0" cellpadding="0" width="690">
			<tr>
				<td colspan="2" align="center" style="font-size:18px; font-weight:bold">
					<?
						echo $company_name_arr[$inv_master_data[0][csf("benificiary_id")]]["company_name"]."<br/>";
					?>
				</td>
			</tr>
			<tr>
				<td colspan="2" align="center" >
				<?
					echo "Factory: ".$company_name_arr[$inv_master_data[0][csf("benificiary_id")]]["company_details"];
				?>
				</td>
			</tr>
			<tr>
				<td colspan="2" align="center" >
				<strong>COMMERCIAL INVOICE</strong> 
				</td>
			</tr>
		</table>
		<br>
		<table  cellspacing="0" cellpadding="0" width="690" rules="all" border="1" style="font-size:12px;">
			<tr>
				<td valign="top">
					<strong>Shipper/Exporter:</strong><br>
					<? echo $company_name_arr[$inv_master_data[0][csf("benificiary_id")]]["company_name"]."<br>Factory: ".$company_name_arr[$inv_master_data[0][csf("benificiary_id")]]["company_details"];?>
					<br><strong>Country of Origin:</strong><br>
					<span>We do hereby certify  that Accessories</span><br>
					<span>are Bangladesh Origin.</span>
				</td>
				<td valign="top">
					<strong>Applicant:</strong><br>
					<? echo $company_name_arr[$inv_master_data[0][csf("buyer_id")]]["company_name"]."<br>".$company_name_arr[$inv_master_data[0][csf("buyer_id")]]["company_details"];?><br>
					Final destination: <?echo $inv_master_data[0][csf("port_of_discharge")];?><br>
					Port of loading: <?echo $inv_master_data[0][csf("port_of_loading")];?><br>Sailing on or about
				</td>
				<td valign="top">
					Invoice No: <? echo $inv_master_data[0][csf("invoice_no")]; ?> Date: <? echo $inv_master_data[0][csf("invoice_date")];?><br>
					<strong>DC No. <?echo $lc_sc_no;?></strong><br>
					<strong>Date:  <?echo $lc_sc_date;?></strong><br>
					L/C Issuing Bank: <br><? echo $issue_bank_name; ?><br>
					<? echo $issue_bank_details; ?>
				</td>
			</tr>
		</table> <br/>
		<table id="" cellspacing="0" cellpadding="0" border="0" rules="all" width="690" class="rpt_table" style="font-size:12px;">
			<tr>
				<td width="680" valign="top" style="border:0;">
				Accessories for 100% Export Oriented Readymade Garments Industry as per Proforma Invoice No. <?echo chop($pi_num,', ');?> DATED. <?echo chop($pi_date,', ');?> Export LC/Cont.No. <?echo chop($buyer_lc_sc_no,', ');?> DT: <?echo chop($buyer_lc_sc_date,', ');?> BIN- <?echo $company_name_arr[$inv_master_data[0][csf("buyer_id")]]["bin_no"];?> H.S Code: 6217.10.00 Bin of <?echo $company_name_arr[$inv_master_data[0][csf("benificiary_id")]]["company_name"];?>-<?echo $company_name_arr[$inv_master_data[0][csf("benificiary_id")]]["bin_no"];?>
				</td>
			</tr>

		</table>
		<br>
		<table style="font-size:12px;" cellspacing="0" cellpadding="0" border="1" rules="all" class="rpt_table">
			<thead>
				<tr>
					<th width="30">Sl</th>
					<th width="100">Style</th>
					<th width="80">Color</th>
					<th width="120" >Description of Goods</th>
					<th width="100">Wash Type</th>
					<th width="60">UOM</th>
					<th width="80">QTY</th>
					<th width="80">Unit Price<br>USD ($)</th>
					<th width="100">Total amount<br>IN USD ($)</th>
				</tr>
			</thead>
			<tbody>
				<? 
				$i=1;
				foreach ($export_pi_dtls_result as $row)
				{
				?>
					<tr>
					
						<td ><? echo $i; ?></td>
						<td ><? echo $row[csf("buyer_style_ref")]; ?></td>
						<td ><? echo $color_arr[$row["COLOR_ID"]]; ?></td>
						<td ><? echo $garments_item[$row["GMTS_ITEM_ID"]]; ?></td>
						<td ><?
								$emb_name="";
								$emb_process_arr=$embprocess_data[$row[csf("dtls_id")]];
								foreach($emb_process_arr as $process_id)
								{
									if($embprocess_data[$row[csf("dtls_id")]][$process_id]==1) $process_type=$wash_wet_process;
									else if($embprocess_data[$row[csf("dtls_id")]][$process_id]==2) $process_type=$wash_dry_process;
									else if($embprocess_data[$row[csf("dtls_id")]][$process_id]==3) $process_type=$wash_laser_desing;
									$emb_id_arr=array_unique(explode(",",$embtype_data[$row[csf("dtls_id")]][$process_id]));
									foreach($emb_id_arr as $emb_id)
									{
										$emb_name.=$process_type[$emb_id].",";
									}
								}
								echo chop($emb_name,","); 
							?>
						</td>
						<td ><? echo $unit_of_measurement[$row["UOM"]]; ?></td>
						<td align="right"><? echo number_format($row["CURRENT_INVOICE_QNTY"],2); ?></td>
						<td align="right"><? echo number_format($row["CURRENT_INVOICE_RATE"],2); ?></td>
						<td align="right"><? echo number_format($row["CURRENT_INVOICE_VALUE"],2); ?></td>
					</tr>					
					<? 
						$total_qnty += $row["CURRENT_INVOICE_QNTY"];
						$total_value += $row["CURRENT_INVOICE_VALUE"];
						$i++;
				}
				?>
				</tr>
			</tbody>
			<tfoot>
				<tr>
					<td colspan='6' align="right">Total :</td>			
					<td  align="right"><? echo number_format($total_qnty,2); ?></td>
					<td align="right" >&nbsp;</td>
					<td  align="right" ><? echo number_format($total_value,2); ?></td>
				</tr>
				<tr><td colspan='9'><strong><?= "TOTAL U.S. DOLLARS: ".number_to_words(number_format($total_value,2, '.', ''), "USD", "Cent");?></strong></td></tr>
			</tfoot>
		</table><br/>
		<table>
			<tr>
				<td>CPT Applicant’s factory (Incoterms-2010)	</td>
			</tr>
			<tr><td style="height:25"></td></tr>
			<tr>
				<td>We do hereby certify that the Accessories are provide strictly as per above Proforma Invoice No. <br>
				and that all other terms & conditions there of have been fully complied with.</td>
			</tr>
			<tr>
				<td style="height:50"></td>
			</tr>
			<tr>
				<td style="font-size:18px; font-weight:bold">
					<?
						echo $company_name_arr[$inv_master_data[0][csf("benificiary_id")]]["company_name"]."<br/>";
					?>
				</td>
			</tr>
			<tr>
				<td style="height:100"></td>
			</tr>
			<tr>
				<td >
				Authorized Signature & Seal
				</td>
			</tr>
		</table>
		<?
		exit();
	}

	if($data[1]==2)
	{
		$company_name_sql=sql_select( "select id, company_name, company_short_name, contract_person, plot_no, level_no, road_no, block_no, city, country_id,bin_no from lib_company");
		$color_arr=return_library_array( "select id, color_name from lib_color",'id','color_name');
		foreach($company_name_sql as $row)
		{
			$company_name_arr[$row[csf("id")]]["company_name"]=$row[csf("company_name")];
			$company_name_arr[$row[csf("id")]]["bin_no"]=$row[csf("bin_no")];
			$company_details =" ";
			$plot_no=$row[csf("plot_no")];
			$level_no=$row[csf("level_no")];
			$road_no=$row[csf("road_no")];
			$block_no=$row[csf("block_no")];
			$city_no=$row[csf("city")];
			$country_id=$row[csf("country_id")];

			if($plot_no!="")  $company_details= $plot_no.", ";
			if($level_no!="")  $company_details.= $level_no.", ";
			if($road_no!="")  $company_details.= $road_no.", ";
			if($block_no!="")  $company_details.= $block_no.", ";
			if($country_id!="")  $company_details.=return_field_value( "country_name","lib_country","id='$country_id'")." . ";
			$company_name_arr[$row[csf("id")]]["company_details"]= $company_details;

		}

		$inv_master_data=sql_select("select a.id, a.benificiary_id, a.buyer_id, a.invoice_no, a.invoice_date, a.is_lc, a.lc_sc_id, a.port_of_entry, a.port_of_loading, a.port_of_discharge, b.id as LC_SC_ID, b.export_lc_no as LC_SC_NO, b.lc_date as LC_SC_DATE,b.issuing_bank_name as ISSUING_BANK_NAME
		from com_export_invoice_ship_mst a, com_export_lc b
		where a.id=$data[0] and a.is_lc=1 and a.lc_sc_id=b.id and a.is_deleted=0 and b.is_deleted=0
		union all
		select a.id, a.benificiary_id, a.buyer_id, a.invoice_no, a.invoice_date, a.is_lc, a.lc_sc_id, a.port_of_entry, a.port_of_loading, a.port_of_discharge, b.id as LC_SC_ID, b.contract_no as LC_SC_NO, b.contract_date as LC_SC_DATE,b.issuing_bank as ISSUING_BANK_NAME
		from com_export_invoice_ship_mst a, com_sales_contract b
		where a.id=$data[0] and a.is_lc=2 and a.lc_sc_id=b.id and a.is_deleted=0 and b.is_deleted=0");
		$lc_sc_id=$inv_master_data[0]['LC_SC_ID'];
		$lc_sc_no=$inv_master_data[0]['LC_SC_NO'];
		$lc_sc_date=change_date_format($inv_master_data[0]['LC_SC_DATE']);
		$issuing_bank_name=$inv_master_data[0]['ISSUING_BANK_NAME'];
		$issuing_bank_details = explode(",", $issuing_bank_name);
		$issue_bank_details='';
		for( $i=0; $i<count($issuing_bank_details); $i++)
		{
			$issue_bank_name = $issuing_bank_details[0];
			$issue_branch_name = $issuing_bank_details[1];
			$issue_level = $issuing_bank_details[2];
			$issue_road = $issuing_bank_details[3];
			$issue_country = $issuing_bank_details[4];
			// $issue_swift = $issuing_bank_details[5];
			// $issue_att = $issuing_bank_details[6];
			if($issue_branch_name!="")  $issue_bank_details.= $issue_branch_name.", ";
			if($issue_level!="")  $issue_bank_details.= $issue_level.", ";
			if($issue_road!="")  $issue_bank_details= $issue_road.", ";
			if($issue_country!="")  $issue_bank_details.= $issue_country;
		}
		$pi_sql=sql_select("select id,pi_date, pi_number,export_pi_id from com_pi_master_details where id in (select pi_id from com_btb_lc_master_details where lc_number='$lc_sc_no' and is_deleted = 0 and status_active=1 ) and is_deleted = 0 and status_active=1 ");

		$pi_num=$export_pi_id=$pi_date='';
		foreach($pi_sql as $row)
		{
			$pi_num.=$row[csf('pi_number')].', ';
			$pi_date.=change_date_format($row[csf('pi_date')]).', ';
			$export_pi_id.=$row[csf('export_pi_id')].',';
		}

		$btb_lc_attachment_sql=sql_select("select a.id, a.is_lc_sc, a.lc_sc_id ,b.export_lc_no as LC_SC_NO, b.lc_date as LC_SC_DATE
		from com_btb_export_lc_attachment a, com_export_lc b
		where import_mst_id in (select id from com_btb_lc_master_details where lc_number='$lc_sc_no' and is_deleted = 0 and status_active=1 ) and a.is_lc_sc=0 and a.lc_sc_id=b.id and a.is_deleted = 0 and a.status_active=1 and b.is_deleted = 0 and b.status_active=1 
		union all
		select a.id, a.is_lc_sc, a.lc_sc_id ,b.contract_no as LC_SC_NO, b.contract_date as LC_SC_DATE
		from com_btb_export_lc_attachment a, com_sales_contract b
		where import_mst_id in (select id from com_btb_lc_master_details where lc_number='$lc_sc_no' and is_deleted = 0 and status_active=1 ) and a.is_lc_sc=1 and a.lc_sc_id=b.id and a.is_deleted = 0 and a.status_active=1 and b.is_deleted = 0 and b.status_active=1 ");

		$buyer_lc_sc_no='';
		$buyer_lc_sc_date='';
		foreach($btb_lc_attachment_sql as $row)
		{
			$buyer_lc_sc_no.=$row['LC_SC_NO'].', ';
			$buyer_lc_sc_date.=change_date_format($row['LC_SC_DATE']).', ';
		}

		$export_pi_dtls_sql = "SELECT  b.current_invoice_qnty as CURRENT_INVOICE_QNTY, b.current_invoice_rate as CURRENT_INVOICE_RATE, b.current_invoice_value as CURRENT_INVOICE_VALUE, c.color_id as COLOR_ID, c.uom as UOM, c.gmts_item_id as GMTS_ITEM_ID, c.embell_name as EMBELL_NAME, c.embell_type as EMBELL_TYPE, d.id as dtls_id, d.buyer_style_ref as BUYER_STYLE_REF
		from  com_export_invoice_ship_dtls b, com_pi_item_details c, subcon_ord_dtls d
		where b.mst_id=$data[0] and b.po_breakdown_id=c.id and c.work_order_dtls_id=d.id and b.is_deleted=0 and c.is_deleted=0 and d.is_deleted=0";
		$export_pi_dtls_result = sql_select($export_pi_dtls_sql);
		$all_dtls_id=array();
		foreach($export_pi_dtls_result as $row)
		{
			$all_dtls_id[$row[csf("dtls_id")]]=$row[csf("dtls_id")];
		}
		if(count($all_dtls_id)>0)
		{
			$con_dtls_sql="select mst_id, process, listagg(cast(embellishment_type as varchar(4000)),',') within group (order by embellishment_type) as embellishment_type
			from subcon_ord_breakdown where status_active=1 and is_deleted=0 and mst_id in(".implode(",",$all_dtls_id).")  and embellishment_type>0
			group by mst_id, process";
			//echo $con_dtls_sql;
			$con_dtls_sql_result = sql_select($con_dtls_sql);
			foreach($con_dtls_sql_result as $row)
			{
				$embtype_data[$row[csf("mst_id")]][$row[csf("process")]]=$row[csf("embellishment_type")];
				$embprocess_data[$row[csf("mst_id")]][$row[csf("process")]]=$row[csf("process")];
				
			}
		}
		?>
		<table id="" cellspacing="0" cellpadding="0" width="690">
			<tr>
				<td colspan="2" align="center" style="font-size:18px; font-weight:bold">
					<?
						echo $company_name_arr[$inv_master_data[0][csf("benificiary_id")]]["company_name"]."<br/>";
					?>
				</td>
			</tr>
			<tr>
				<td colspan="2" align="center" >
				<?
					echo "Factory: ".$company_name_arr[$inv_master_data[0][csf("benificiary_id")]]["company_details"];
				?>
				</td>
			</tr>
			<tr>
				<td colspan="2" align="center" >
				<strong>PACKING LIST</strong> 
				</td>
			</tr>
		</table>
		<br>
		<table  cellspacing="0" cellpadding="0" width="690" rules="all" border="1" style="font-size:12px;">
			<tr>
				<td valign="top">
					<strong>Shipper/Exporter:</strong><br>
					<? echo $company_name_arr[$inv_master_data[0][csf("benificiary_id")]]["company_name"]."<br>Factory: ".$company_name_arr[$inv_master_data[0][csf("benificiary_id")]]["company_details"];?>
					<br><strong>Country of Origin:</strong><br>
					<span>We do hereby certify  that Accessories</span><br>
					<span>are Bangladesh Origin.</span>
				</td>
				<td valign="top">
					<strong>Applicant:</strong><br>
					<? echo $company_name_arr[$inv_master_data[0][csf("buyer_id")]]["company_name"]."<br>".$company_name_arr[$inv_master_data[0][csf("buyer_id")]]["company_details"];?><br>
					Final destination: <?echo $inv_master_data[0][csf("port_of_discharge")];?><br>
					Port of loading: <?echo $inv_master_data[0][csf("port_of_loading")];?>
				</td>
				<td valign="top">
					Invoice No: <? echo $inv_master_data[0][csf("invoice_no")]; ?> Date: <? echo $inv_master_data[0][csf("invoice_date")];?><br>
					<strong>DC No. <?echo $lc_sc_no;?></strong><br>
					<strong>Date:  <?echo $lc_sc_date;?></strong><br>
					L/C Issuing Bank: <br><? echo $issue_bank_name; ?><br>
					<? echo $issue_bank_details; ?>
				</td>
			</tr>
		</table> <br/>
		<table id="" cellspacing="0" cellpadding="0" border="0" rules="all" width="690" class="rpt_table" style="font-size:12px;">
			<tr>
				<td width="680" valign="top" style="border:0;">
				Accessories for 100% Export Oriented Readymade Garments Industry as per Proforma Invoice No. <?echo chop($pi_num,', ');?> DATED. <?echo chop($pi_date,', ');?> Export LC/Cont.No. <?echo chop($buyer_lc_sc_no,', ');?> DT: <?echo chop($buyer_lc_sc_date,', ');?> BIN- <?echo $company_name_arr[$inv_master_data[0][csf("buyer_id")]]["bin_no"];?> H.S Code: 6217.10.00 Bin of <?echo $company_name_arr[$inv_master_data[0][csf("benificiary_id")]]["company_name"];?>-<?echo $company_name_arr[$inv_master_data[0][csf("benificiary_id")]]["bin_no"];?>
				</td>
			</tr>

		</table>
		<br>
		<table style="font-size:12px;" cellspacing="0" cellpadding="0" border="1" rules="all" class="rpt_table">
			<thead>
				<tr>
					<th width="30">Sl</th>
					<th width="100">Style</th>
					<th width="80">Color</th>
					<th width="120" >Description of Goods</th>
					<th width="100">Wash Type</th>
					<th width="60">UOM</th>
					<th width="80">QTY</th>
					<!-- <th width="80">Unit Price<br>USD ($)</th>
					<th width="100">Total amount<br>IN USD ($)</th> -->
				</tr>
			</thead>
			<tbody>
				<? 
				$i=1;
				foreach ($export_pi_dtls_result as $row)
				{
				?>
					<tr>
					
						<td ><? echo $i; ?></td>
						<td ><? echo $row[csf("buyer_style_ref")]; ?></td>
						<td ><? echo $color_arr[$row[csf("color_id")]]; ?></td>
						<td ><? echo $garments_item[$row[csf("gmts_item_id")]]; ?></td>
						<td ><?
								$emb_name="";
								$emb_process_arr=$embprocess_data[$row[csf("dtls_id")]];
								foreach($emb_process_arr as $process_id)
								{
									if($embprocess_data[$row[csf("dtls_id")]][$process_id]==1) $process_type=$wash_wet_process;
									else if($embprocess_data[$row[csf("dtls_id")]][$process_id]==2) $process_type=$wash_dry_process;
									else if($embprocess_data[$row[csf("dtls_id")]][$process_id]==3) $process_type=$wash_laser_desing;
									$emb_id_arr=array_unique(explode(",",$embtype_data[$row[csf("dtls_id")]][$process_id]));
									foreach($emb_id_arr as $emb_id)
									{
										$emb_name.=$process_type[$emb_id].",";
									}
								}
								echo chop($emb_name,","); 
							?>
						</td>
						<td ><? echo $unit_of_measurement[$row[csf("uom")]]; ?></td>
						<td align="right"><? echo number_format($row["CURRENT_INVOICE_QNTY"],2); ?></td>
						<!-- <td align="right"><? echo number_format($row[csf("rate")],2); ?></td>
						<td align="right"><? echo number_format($row[csf("amount")],2); ?></td> -->
					</tr>					
					<? 
						$total_qnty += $row["CURRENT_INVOICE_QNTY"];
						$total_value += $row[csf("amount")];
						$i++;
				}
				?>
				</tr>
			</tbody>
			<tfoot>
				<tr>
					<td colspan='6' align="right">Total :</td>			
					<td  align="right"><? echo number_format($total_qnty,2); ?></td>
					<!-- <td align="right" >&nbsp;</td>
					<td  align="right" ><? echo number_format($total_value,2); ?></td> -->
				</tr>
				<!-- <tr><td colspan='7'><strong><?= "TOTAL U.S. DOLLARS: ".number_to_words(number_format($total_value,2, '.', ''), "USD", "Cent");?></strong></td></tr> -->
			</tfoot>
		</table><br/>
		<table>
			<tr><td style="height:25"></td></tr>
			<tr>
				<td>We do hereby certify that the Accessories are provide strictly as per above Proforma Invoice No. <br>
				and that all other terms & conditions there of have been fully complied with.</td>
			</tr>
			<tr>
				<td style="height:50"></td>
			</tr>
			<tr>
				<td style="font-size:18px; font-weight:bold">
					<?
						echo $company_name_arr[$inv_master_data[0][csf("benificiary_id")]]["company_name"]."<br/>";
					?>
				</td>
			</tr>
			<tr>
				<td style="height:100"></td>
			</tr>
			<tr>
				<td >
				Authorized Signature & Seal
				</td>
			</tr>
		</table>
						
		<?
		exit();
	}

	if($data[1]==3)
	{
		$company_name_sql=sql_select( "select id, company_name, company_short_name, contract_person, plot_no, level_no, road_no, block_no, city, country_id,bin_no from lib_company");
		$color_arr=return_library_array( "select id, color_name from lib_color",'id','color_name');
		foreach($company_name_sql as $row)
		{
			$company_name_arr[$row[csf("id")]]["company_name"]=$row[csf("company_name")];
			$company_name_arr[$row[csf("id")]]["bin_no"]=$row[csf("bin_no")];
			$company_details =" ";
			$plot_no=$row[csf("plot_no")];
			$level_no=$row[csf("level_no")];
			$road_no=$row[csf("road_no")];
			$block_no=$row[csf("block_no")];
			$city_no=$row[csf("city")];
			$country_id=$row[csf("country_id")];

			if($plot_no!="")  $company_details= $plot_no.", ";
			if($level_no!="")  $company_details.= $level_no.", ";
			if($road_no!="")  $company_details.= $road_no.", ";
			if($block_no!="")  $company_details.= $block_no.", ";
			if($country_id!="")  $company_details.=return_field_value( "country_name","lib_country","id='$country_id'")." . ";
			$company_name_arr[$row[csf("id")]]["company_details"]= $company_details;

		}

		$inv_master_data=sql_select("select a.id, a.benificiary_id, a.buyer_id, a.invoice_no, a.invoice_date, a.is_lc, a.lc_sc_id, a.port_of_entry, a.port_of_loading, a.port_of_discharge, b.id as LC_SC_ID, b.export_lc_no as LC_SC_NO, b.lc_date as LC_SC_DATE,b.issuing_bank_name as ISSUING_BANK_NAME
		from com_export_invoice_ship_mst a, com_export_lc b
		where a.id=$data[0] and a.is_lc=1 and a.lc_sc_id=b.id and a.is_deleted=0 and b.is_deleted=0
		union all
		select a.id, a.benificiary_id, a.buyer_id, a.invoice_no, a.invoice_date, a.is_lc, a.lc_sc_id, a.port_of_entry, a.port_of_loading, a.port_of_discharge, b.id as LC_SC_ID, b.contract_no as LC_SC_NO, b.contract_date as LC_SC_DATE,b.issuing_bank as ISSUING_BANK_NAME
		from com_export_invoice_ship_mst a, com_sales_contract b
		where a.id=$data[0] and a.is_lc=2 and a.lc_sc_id=b.id and a.is_deleted=0 and b.is_deleted=0");
		$lc_sc_id=$inv_master_data[0]['LC_SC_ID'];
		$lc_sc_no=$inv_master_data[0]['LC_SC_NO'];
		$lc_sc_date=change_date_format($inv_master_data[0]['LC_SC_DATE']);
		$issuing_bank_name=$inv_master_data[0]['ISSUING_BANK_NAME'];
		$issuing_bank_details = explode(",", $issuing_bank_name);
		$issue_bank_details='';
		for( $i=0; $i<count($issuing_bank_details); $i++)
		{
			$issue_bank_name = $issuing_bank_details[0];
			$issue_branch_name = $issuing_bank_details[1];
			$issue_level = $issuing_bank_details[2];
			$issue_road = $issuing_bank_details[3];
			$issue_country = $issuing_bank_details[4];
			// $issue_swift = $issuing_bank_details[5];
			// $issue_att = $issuing_bank_details[6];
			if($issue_branch_name!="")  $issue_bank_details.= $issue_branch_name.", ";
			if($issue_level!="")  $issue_bank_details.= $issue_level.", ";
			if($issue_road!="")  $issue_bank_details= $issue_road.", ";
			if($issue_country!="")  $issue_bank_details.= $issue_country;
		}
		$pi_sql=sql_select("select id,pi_date, pi_number,export_pi_id from com_pi_master_details where id in (select pi_id from com_btb_lc_master_details where lc_number='$lc_sc_no' and is_deleted = 0 and status_active=1 ) and is_deleted = 0 and status_active=1 ");

		$pi_num=$export_pi_id=$pi_date='';
		foreach($pi_sql as $row)
		{
			$pi_num.=$row[csf('pi_number')].', ';
			$pi_date.=change_date_format($row[csf('pi_date')]).', ';
			$export_pi_id.=$row[csf('export_pi_id')].',';
		}

		$btb_lc_attachment_sql=sql_select("select a.id, a.is_lc_sc, a.lc_sc_id ,b.export_lc_no as LC_SC_NO, b.lc_date as LC_SC_DATE
		from com_btb_export_lc_attachment a, com_export_lc b
		where import_mst_id in (select id from com_btb_lc_master_details where lc_number='$lc_sc_no' and is_deleted = 0 and status_active=1 ) and a.is_lc_sc=0 and a.lc_sc_id=b.id and a.is_deleted = 0 and a.status_active=1 and b.is_deleted = 0 and b.status_active=1 
		union all
		select a.id, a.is_lc_sc, a.lc_sc_id ,b.contract_no as LC_SC_NO, b.contract_date as LC_SC_DATE
		from com_btb_export_lc_attachment a, com_sales_contract b
		where import_mst_id in (select id from com_btb_lc_master_details where lc_number='$lc_sc_no' and is_deleted = 0 and status_active=1 ) and a.is_lc_sc=1 and a.lc_sc_id=b.id and a.is_deleted = 0 and a.status_active=1 and b.is_deleted = 0 and b.status_active=1 ");

		$buyer_lc_sc_no='';
		$buyer_lc_sc_date='';
		foreach($btb_lc_attachment_sql as $row)
		{
			$buyer_lc_sc_no.=$row['LC_SC_NO'].', ';
			$buyer_lc_sc_date.=change_date_format($row['LC_SC_DATE']).', ';
		}
		$export_pi_dtls_sql = "SELECT  b.current_invoice_qnty as CURRENT_INVOICE_QNTY, b.current_invoice_rate as CURRENT_INVOICE_RATE, b.current_invoice_value as CURRENT_INVOICE_VALUE, c.color_id as COLOR_ID, c.uom as UOM, c.gmts_item_id as GMTS_ITEM_ID, c.embell_name as EMBELL_NAME, c.embell_type as EMBELL_TYPE, d.id as dtls_id, d.buyer_style_ref as BUYER_STYLE_REF
		from  com_export_invoice_ship_dtls b, com_pi_item_details c, subcon_ord_dtls d
		where b.mst_id=$data[0] and b.po_breakdown_id=c.id and c.work_order_dtls_id=d.id and b.is_deleted=0 and c.is_deleted=0 and d.is_deleted=0";
		$export_pi_dtls_result = sql_select($export_pi_dtls_sql);
		$all_dtls_id=array();
		foreach($export_pi_dtls_result as $row)
		{
			$all_dtls_id[$row[csf("dtls_id")]]=$row[csf("dtls_id")];
		}
		if(count($all_dtls_id)>0)
		{
			$con_dtls_sql="select mst_id, process, listagg(cast(embellishment_type as varchar(4000)),',') within group (order by embellishment_type) as embellishment_type
			from subcon_ord_breakdown where status_active=1 and is_deleted=0 and mst_id in(".implode(",",$all_dtls_id).")  and embellishment_type>0
			group by mst_id, process";
			//echo $con_dtls_sql;
			$con_dtls_sql_result = sql_select($con_dtls_sql);
			foreach($con_dtls_sql_result as $row)
			{
				$embtype_data[$row[csf("mst_id")]][$row[csf("process")]]=$row[csf("embellishment_type")];
				$embprocess_data[$row[csf("mst_id")]][$row[csf("process")]]=$row[csf("process")];
				
			}
		}
		?>
		<table id="" cellspacing="0" cellpadding="0" width="690">
			<tr>
				<td colspan="2" align="center" style="font-size:18px; font-weight:bold">
					<?
						echo $company_name_arr[$inv_master_data[0][csf("benificiary_id")]]["company_name"]."<br/>";
					?>
				</td>
			</tr>
			<tr>
				<td colspan="2" align="center" >
				<?
					echo "Factory: ".$company_name_arr[$inv_master_data[0][csf("benificiary_id")]]["company_details"];
				?>
				</td>
			</tr>
			<tr>
				<td colspan="2" align="center" >
				<strong>DELIVERY CHALLAN</strong> 
				</td>
			</tr>
			<tr>
				<td colspan="2" >
				Challan No: <? echo $inv_master_data[0][csf("invoice_no")]; ?> Date: <? echo $inv_master_data[0][csf("invoice_date")];?>
				</td>
			</tr>
		</table>
		<br>
		<table  cellspacing="0" cellpadding="0" width="690" rules="all" border="1" style="font-size:12px;">
			<tr>
				<td valign="top">
					<strong>Shipper/Exporter:</strong><br>
					<? echo $company_name_arr[$inv_master_data[0][csf("benificiary_id")]]["company_name"]."<br>Factory: ".$company_name_arr[$inv_master_data[0][csf("benificiary_id")]]["company_details"];?>
					<br><strong>Notify parties:</strong><br>
					<span>We do hereby certify  that Accessories</span><br>
					<span>are Bangladesh Origin.</span>
				</td>
				<td valign="top">
					<strong>Applicant:</strong><br>
					<? echo $company_name_arr[$inv_master_data[0][csf("buyer_id")]]["company_name"]."<br>".$company_name_arr[$inv_master_data[0][csf("buyer_id")]]["company_details"];?><br>
					Final destination: <?echo $inv_master_data[0][csf("port_of_discharge")];?><br>
					Port of loading: <?echo $inv_master_data[0][csf("port_of_loading")];?><br>FREIGHT PREPAID
				</td>
				<td valign="top">
					Invoice No: <? echo $inv_master_data[0][csf("invoice_no")]; ?> Date: <? echo $inv_master_data[0][csf("invoice_date")];?><br>
					<strong>DC No. <?echo $lc_sc_no;?></strong><br>
					<strong>Date:  <?echo $lc_sc_date;?></strong><br>
					L/C Issuing Bank: <br><? echo $issue_bank_name; ?><br>
					<? echo $issue_bank_details; ?>
				</td>
			</tr>
		</table> <br/>
		<table id="" cellspacing="0" cellpadding="0" border="0" rules="all" width="690" class="rpt_table" style="font-size:12px;">
			<tr>
				<td width="680" valign="top" style="border:0;">
				Accessories for 100% Export Oriented Readymade Garments Industry as per Proforma Invoice No. <?echo chop($pi_num,', ');?> DATED. <?echo chop($pi_date,', ');?> Export LC/Cont.No. <?echo chop($buyer_lc_sc_no,', ');?> DT: <?echo chop($buyer_lc_sc_date,', ');?> BIN- <?echo $company_name_arr[$inv_master_data[0][csf("buyer_id")]]["bin_no"];?> H.S Code: 6217.10.00 Bin of <?echo $company_name_arr[$inv_master_data[0][csf("benificiary_id")]]["company_name"];?>-<?echo $company_name_arr[$inv_master_data[0][csf("benificiary_id")]]["bin_no"];?>
				</td>
			</tr>

		</table>
		<br>
		<table style="font-size:12px;" cellspacing="0" cellpadding="0" border="1" rules="all" class="rpt_table">
			<thead>
				<tr>
					<th width="30">Sl</th>
					<th width="100">Style</th>
					<th width="80">Color</th>
					<th width="120" >Description of Goods</th>
					<th width="100">Wash Type</th>
					<th width="60">UOM</th>
					<th width="80">QTY</th>
					<!-- <th width="80">Unit Price<br>USD ($)</th>
					<th width="100">Total amount<br>IN USD ($)</th> -->
				</tr>
			</thead>
			<tbody>
				<? 
				$i=1;
				foreach ($export_pi_dtls_result as $row)
				{
				?>
					<tr>
					
						<td ><? echo $i; ?></td>
						<td ><? echo $row[csf("buyer_style_ref")]; ?></td>
						<td ><? echo $color_arr[$row[csf("color_id")]]; ?></td>
						<td ><? echo $garments_item[$row[csf("gmts_item_id")]]; ?></td>
						<td ><?
								$emb_name="";
								$emb_process_arr=$embprocess_data[$row[csf("dtls_id")]];
								foreach($emb_process_arr as $process_id)
								{
									if($embprocess_data[$row[csf("dtls_id")]][$process_id]==1) $process_type=$wash_wet_process;
									else if($embprocess_data[$row[csf("dtls_id")]][$process_id]==2) $process_type=$wash_dry_process;
									else if($embprocess_data[$row[csf("dtls_id")]][$process_id]==3) $process_type=$wash_laser_desing;
									$emb_id_arr=array_unique(explode(",",$embtype_data[$row[csf("dtls_id")]][$process_id]));
									foreach($emb_id_arr as $emb_id)
									{
										$emb_name.=$process_type[$emb_id].",";
									}
								}
								echo chop($emb_name,","); 
							?>
						</td>
						<td ><? echo $unit_of_measurement[$row[csf("uom")]]; ?></td>
						<td align="right"><? echo number_format($row["CURRENT_INVOICE_QNTY"],2); ?></td>
						<!-- <td align="right"><? echo number_format($row[csf("rate")],2); ?></td>
						<td align="right"><? echo number_format($row[csf("amount")],2); ?></td> -->
					</tr>					
					<? 
						$total_qnty += $row["CURRENT_INVOICE_QNTY"];
						$total_value += $row[csf("amount")];
						$i++;
				}
				?>
				</tr>
			</tbody>
			<tfoot>
				<tr>
					<td colspan='6' align="right">Total :</td>			
					<td  align="right"><? echo number_format($total_qnty,2); ?></td>
					<!-- <td align="right" >&nbsp;</td>
					<td  align="right" ><? echo number_format($total_value,2); ?></td> -->
				</tr>
				<!-- <tr><td colspan='7'><strong><?= "TOTAL U.S. DOLLARS: ".number_to_words(number_format($total_value,2, '.', ''), "USD", "Cent");?></strong></td></tr> -->
			</tfoot>
		</table><br/>
		<table width="700">
			<tr>
				<td colspan='2' style="height:25"></td>
			</tr>
			<tr>
				<td colspan='2'>We do hereby certify that the Accessories are provide strictly as per above Proforma Invoice No. <br>
				and that all other terms & conditions there of have been fully complied with.</td>
			</tr>
			<tr>
				<td  colspan='2' style="height:50" ></td>
			</tr>
			<tr>
				<td width='350'></td>
				<td  width='350'>(Goods received in good condition as per above)</td>
			</tr>
			<tr>
				<td style="font-size:18px; font-weight:bold" width='350'>
					<?
						echo $company_name_arr[$inv_master_data[0][csf("benificiary_id")]]["company_name"];
					?>
				</td>
				<td style="font-size:18px; font-weight:bold" width='350'>
					<?
						echo $company_name_arr[$inv_master_data[0][csf("buyer_id")]]["company_name"];
					?>
				</td>
			</tr>
			<tr>
				<td style="height:100" colspan='2'></td>
			</tr>
			<tr>
				<td  colspan='2'>
				Authorized Signature & Seal
				</td>
			</tr>
		</table>
						
		<?
		exit();
	}

	if($data[1]==4)
	{
		$company_name_sql=sql_select( "select id, company_name, company_short_name, contract_person, plot_no, level_no, road_no, block_no, city, country_id,bin_no from lib_company");
		$color_arr=return_library_array( "select id, color_name from lib_color",'id','color_name');
		foreach($company_name_sql as $row)
		{
			$company_name_arr[$row[csf("id")]]["company_name"]=$row[csf("company_name")];
			$company_name_arr[$row[csf("id")]]["bin_no"]=$row[csf("bin_no")];
			$company_details =" ";
			$plot_no=$row[csf("plot_no")];
			$level_no=$row[csf("level_no")];
			$road_no=$row[csf("road_no")];
			$block_no=$row[csf("block_no")];
			$city_no=$row[csf("city")];
			$country_id=$row[csf("country_id")];

			if($plot_no!="")  $company_details= $plot_no.", ";
			if($level_no!="")  $company_details.= $level_no.", ";
			if($road_no!="")  $company_details.= $road_no.", ";
			if($block_no!="")  $company_details.= $block_no.", ";
			if($country_id!="")  $company_details.=return_field_value( "country_name","lib_country","id='$country_id'")." . ";
			$company_name_arr[$row[csf("id")]]["company_details"]= $company_details;

		}

		$inv_master_data=sql_select("select a.id, a.benificiary_id, a.buyer_id, a.invoice_no, a.invoice_date, a.is_lc, a.lc_sc_id, a.port_of_entry, a.port_of_loading, a.port_of_discharge, b.id as LC_SC_ID, b.export_lc_no as LC_SC_NO, b.lc_date as LC_SC_DATE,b.issuing_bank_name as ISSUING_BANK_NAME
		from com_export_invoice_ship_mst a, com_export_lc b
		where a.id=$data[0] and a.is_lc=1 and a.lc_sc_id=b.id and a.is_deleted=0 and b.is_deleted=0
		union all
		select a.id, a.benificiary_id, a.buyer_id, a.invoice_no, a.invoice_date, a.is_lc, a.lc_sc_id, a.port_of_entry, a.port_of_loading, a.port_of_discharge, b.id as LC_SC_ID, b.contract_no as LC_SC_NO, b.contract_date as LC_SC_DATE,b.issuing_bank as ISSUING_BANK_NAME
		from com_export_invoice_ship_mst a, com_sales_contract b
		where a.id=$data[0] and a.is_lc=2 and a.lc_sc_id=b.id and a.is_deleted=0 and b.is_deleted=0");
		$lc_sc_id=$inv_master_data[0]['LC_SC_ID'];
		$lc_sc_no=$inv_master_data[0]['LC_SC_NO'];
		$lc_sc_date=change_date_format($inv_master_data[0]['LC_SC_DATE']);
		$issuing_bank_name=$inv_master_data[0]['ISSUING_BANK_NAME'];
		$issuing_bank_details = explode(",", $issuing_bank_name);
		$issue_bank_details='';
		for( $i=0; $i<count($issuing_bank_details); $i++)
		{
			$issue_bank_name = $issuing_bank_details[0];
			$issue_branch_name = $issuing_bank_details[1];
			$issue_level = $issuing_bank_details[2];
			$issue_road = $issuing_bank_details[3];
			$issue_country = $issuing_bank_details[4];
			// $issue_swift = $issuing_bank_details[5];
			// $issue_att = $issuing_bank_details[6];
			if($issue_branch_name!="")  $issue_bank_details.= $issue_branch_name.", ";
			if($issue_level!="")  $issue_bank_details.= $issue_level.", ";
			if($issue_road!="")  $issue_bank_details= $issue_road.", ";
			if($issue_country!="")  $issue_bank_details.= $issue_country;
		}
		$pi_sql=sql_select("select id,pi_date, pi_number,export_pi_id from com_pi_master_details where id in (select pi_id from com_btb_lc_master_details where lc_number='$lc_sc_no' and is_deleted = 0 and status_active=1 ) and is_deleted = 0 and status_active=1 ");

		$pi_num=$export_pi_id=$pi_date='';
		foreach($pi_sql as $row)
		{
			$pi_num.=$row[csf('pi_number')].', ';
			$pi_date.=change_date_format($row[csf('pi_date')]).', ';
			$export_pi_id.=$row[csf('export_pi_id')].',';
		}

		$btb_lc_attachment_sql=sql_select("select a.id, a.is_lc_sc, a.lc_sc_id ,b.export_lc_no as LC_SC_NO, b.lc_date as LC_SC_DATE
		from com_btb_export_lc_attachment a, com_export_lc b
		where import_mst_id in (select id from com_btb_lc_master_details where lc_number='$lc_sc_no' and is_deleted = 0 and status_active=1 ) and a.is_lc_sc=0 and a.lc_sc_id=b.id and a.is_deleted = 0 and a.status_active=1 and b.is_deleted = 0 and b.status_active=1 
		union all
		select a.id, a.is_lc_sc, a.lc_sc_id ,b.contract_no as LC_SC_NO, b.contract_date as LC_SC_DATE
		from com_btb_export_lc_attachment a, com_sales_contract b
		where import_mst_id in (select id from com_btb_lc_master_details where lc_number='$lc_sc_no' and is_deleted = 0 and status_active=1 ) and a.is_lc_sc=1 and a.lc_sc_id=b.id and a.is_deleted = 0 and a.status_active=1 and b.is_deleted = 0 and b.status_active=1 ");

		$buyer_lc_sc_no='';
		$buyer_lc_sc_date='';
		foreach($btb_lc_attachment_sql as $row)
		{
			$buyer_lc_sc_no.=$row['LC_SC_NO'].', ';
			$buyer_lc_sc_date.=change_date_format($row['LC_SC_DATE']).', ';
		}
		$export_pi_dtls_sql = "SELECT  b.current_invoice_qnty as CURRENT_INVOICE_QNTY, b.current_invoice_rate as CURRENT_INVOICE_RATE, b.current_invoice_value as CURRENT_INVOICE_VALUE, c.color_id as COLOR_ID, c.uom as UOM, c.gmts_item_id as GMTS_ITEM_ID, c.embell_name as EMBELL_NAME, c.embell_type as EMBELL_TYPE, d.id as dtls_id, d.buyer_style_ref as BUYER_STYLE_REF
		from  com_export_invoice_ship_dtls b, com_pi_item_details c, subcon_ord_dtls d
		where b.mst_id=$data[0] and b.po_breakdown_id=c.id and c.work_order_dtls_id=d.id and b.is_deleted=0 and c.is_deleted=0 and d.is_deleted=0";
		$export_pi_dtls_result = sql_select($export_pi_dtls_sql);
		$all_dtls_id=array();
		foreach($export_pi_dtls_result as $row)
		{
			$all_dtls_id[$row[csf("dtls_id")]]=$row[csf("dtls_id")];
		}
		if(count($all_dtls_id)>0)
		{
			$con_dtls_sql="select mst_id, process, listagg(cast(embellishment_type as varchar(4000)),',') within group (order by embellishment_type) as embellishment_type
			from subcon_ord_breakdown where status_active=1 and is_deleted=0 and mst_id in(".implode(",",$all_dtls_id).")  and embellishment_type>0
			group by mst_id, process";
			//echo $con_dtls_sql;
			$con_dtls_sql_result = sql_select($con_dtls_sql);
			foreach($con_dtls_sql_result as $row)
			{
				$embtype_data[$row[csf("mst_id")]][$row[csf("process")]]=$row[csf("embellishment_type")];
				$embprocess_data[$row[csf("mst_id")]][$row[csf("process")]]=$row[csf("process")];
				
			}
		}
		?>
		<table id="" cellspacing="0" cellpadding="0" width="690">
			<tr>
				<td colspan="2" align="center" style="font-size:18px; font-weight:bold">
					<?
						echo $company_name_arr[$inv_master_data[0][csf("benificiary_id")]]["company_name"]."<br/>";
					?>
				</td>
			</tr>
			<tr>
				<td colspan="2" align="center" >
				<?
					echo "Factory: ".$company_name_arr[$inv_master_data[0][csf("benificiary_id")]]["company_details"];
				?>
				</td>
			</tr>
			<tr>
				<td colspan="2" align="center" >
				<strong>TRUCK(Own Transport) RECEIPT</strong> 
				</td>
			</tr>
			<tr>
				<td colspan="2" >
				T.R No: <? echo $inv_master_data[0][csf("invoice_no")]; ?> Date: <? echo $inv_master_data[0][csf("invoice_date")];?>
				</td>
			</tr>
		</table>
		<br>
		<table  cellspacing="0" cellpadding="0" width="690" rules="all" border="1" style="font-size:12px;">
			<tr>
				<td valign="top">
					<strong>Shipper/Exporter:</strong><br>
					<? echo $company_name_arr[$inv_master_data[0][csf("benificiary_id")]]["company_name"]."<br>Factory: ".$company_name_arr[$inv_master_data[0][csf("benificiary_id")]]["company_details"];?>
					<br><strong>Notify parties:</strong><br>
					<span>We do hereby certify  that Accessories</span><br>
					<span>are Bangladesh Origin.</span>
				</td>
				<td valign="top">
					<strong>Applicant:</strong><br>
					<? echo $company_name_arr[$inv_master_data[0][csf("buyer_id")]]["company_name"]."<br>".$company_name_arr[$inv_master_data[0][csf("buyer_id")]]["company_details"];?><br>
					Final destination: <?echo $inv_master_data[0][csf("port_of_discharge")];?><br>
					Port of loading: <?echo $inv_master_data[0][csf("port_of_loading")];?><br>FREIGHT PREPAID
				</td>
				<td valign="top">
					Invoice No: <? echo $inv_master_data[0][csf("invoice_no")]; ?> Date: <? echo $inv_master_data[0][csf("invoice_date")];?><br>
					<strong>DC No. <?echo $lc_sc_no;?></strong><br>
					<strong>Date:  <?echo $lc_sc_date;?></strong><br>
					L/C Issuing Bank: <br><? echo $issue_bank_name; ?><br>
					<? echo $issue_bank_details; ?><br>
					<strong>EXPORT STANDARD PACKING</strong>
				</td>
			</tr>
		</table> <br/>
		<table id="" cellspacing="0" cellpadding="0" border="0" rules="all" width="690" class="rpt_table" style="font-size:12px;">
			<tr>
				<td width="680" valign="top" style="border:0;">
				Accessories for 100% Export Oriented Readymade Garments Industry as per Proforma Invoice No. <?echo chop($pi_num,', ');?> DATED. <?echo chop($pi_date,', ');?> Export LC/Cont.No. <?echo chop($buyer_lc_sc_no,', ');?> DT: <?echo chop($buyer_lc_sc_date,', ');?> BIN- <?echo $company_name_arr[$inv_master_data[0][csf("buyer_id")]]["bin_no"];?> H.S Code: 6217.10.00 Bin of <?echo $company_name_arr[$inv_master_data[0][csf("benificiary_id")]]["company_name"];?>-<?echo $company_name_arr[$inv_master_data[0][csf("benificiary_id")]]["bin_no"];?>
				</td>
			</tr>

		</table>
		<br>
		<table style="font-size:12px;" cellspacing="0" cellpadding="0" border="1" rules="all" class="rpt_table">
			<thead>
				<tr>
					<th width="30">Sl</th>
					<th width="100">Style</th>
					<th width="80">Color</th>
					<th width="120" >Description of Goods</th>
					<th width="100">Wash Type</th>
					<th width="60">UOM</th>
					<th width="80">QTY</th>
					<!-- <th width="80">Unit Price<br>USD ($)</th>
					<th width="100">Total amount<br>IN USD ($)</th> -->
				</tr>
			</thead>
			<tbody>
				<? 
				$i=1;
				foreach ($export_pi_dtls_result as $row)
				{
				?>
					<tr>
					
						<td ><? echo $i; ?></td>
						<td ><? echo $row[csf("buyer_style_ref")]; ?></td>
						<td ><? echo $color_arr[$row[csf("color_id")]]; ?></td>
						<td ><? echo $garments_item[$row[csf("gmts_item_id")]]; ?></td>
						<td ><?
								$emb_name="";
								$emb_process_arr=$embprocess_data[$row[csf("dtls_id")]];
								foreach($emb_process_arr as $process_id)
								{
									if($embprocess_data[$row[csf("dtls_id")]][$process_id]==1) $process_type=$wash_wet_process;
									else if($embprocess_data[$row[csf("dtls_id")]][$process_id]==2) $process_type=$wash_dry_process;
									else if($embprocess_data[$row[csf("dtls_id")]][$process_id]==3) $process_type=$wash_laser_desing;
									$emb_id_arr=array_unique(explode(",",$embtype_data[$row[csf("dtls_id")]][$process_id]));
									foreach($emb_id_arr as $emb_id)
									{
										$emb_name.=$process_type[$emb_id].",";
									}
								}
								echo chop($emb_name,","); 
							?>
						</td>
						<td ><? echo $unit_of_measurement[$row[csf("uom")]]; ?></td>
						<td align="right"><? echo number_format($row["CURRENT_INVOICE_QNTY"],2); ?></td>
						<!-- <td align="right"><? echo number_format($row[csf("rate")],2); ?></td>
						<td align="right"><? echo number_format($row[csf("amount")],2); ?></td> -->
					</tr>					
					<? 
						$total_qnty += $row["CURRENT_INVOICE_QNTY"];
						$total_value += $row[csf("amount")];
						$i++;
				}
				?>
				</tr>
			</tbody>
			<tfoot>
				<tr>
					<td colspan='6' align="right">Total :</td>			
					<td  align="right"><? echo number_format($total_qnty,2); ?></td>
					<!-- <td align="right" >&nbsp;</td>
					<td  align="right" ><? echo number_format($total_value,2); ?></td> -->
				</tr>
				<!-- <tr><td colspan='7'><strong><?= "TOTAL U.S. DOLLARS: ".number_to_words(number_format($total_value,2, '.', ''), "USD", "Cent");?></strong></td></tr> -->
			</tfoot>
		</table><br/>
		<table>
			<tr><td style="height:25"></td></tr>
			<tr>
				<td>We do hereby certify that the Accessories are provide strictly as per above Proforma Invoice No. <br>
				and that all other terms & conditions there of have been fully complied with.</td>
			</tr>
			<tr>
				<td style="height:50"></td>
			</tr>
			<tr>
				<td style="font-size:18px; font-weight:bold">
					<?
						echo $company_name_arr[$inv_master_data[0][csf("benificiary_id")]]["company_name"]."<br/>";
					?>
				</td>
			</tr>
			<tr>
				<td style="height:100"></td>
			</tr>
			<tr>
				<td >
				Authorized Signature & Seal
				</td>
			</tr>
		</table>
						
		<?
		exit();
	}

	if($data[1]==5)
	{
		$company_name_sql=sql_select( "select id, company_name, company_short_name, contract_person, plot_no, level_no, road_no, block_no, city, country_id,bin_no from lib_company");
		$color_arr=return_library_array( "select id, color_name from lib_color",'id','color_name');
		foreach($company_name_sql as $row)
		{
			$company_name_arr[$row[csf("id")]]["company_name"]=$row[csf("company_name")];
			$company_name_arr[$row[csf("id")]]["bin_no"]=$row[csf("bin_no")];
			$company_details =" ";
			$plot_no=$row[csf("plot_no")];
			$level_no=$row[csf("level_no")];
			$road_no=$row[csf("road_no")];
			$block_no=$row[csf("block_no")];
			$city_no=$row[csf("city")];
			$country_id=$row[csf("country_id")];

			if($plot_no!="")  $company_details= $plot_no.", ";
			if($level_no!="")  $company_details.= $level_no.", ";
			if($road_no!="")  $company_details.= $road_no.", ";
			if($block_no!="")  $company_details.= $block_no.", ";
			if($country_id!="")  $company_details.=return_field_value( "country_name","lib_country","id='$country_id'")." . ";
			$company_name_arr[$row[csf("id")]]["company_details"]= $company_details;

		}

		$inv_master_data=sql_select("select a.id, a.benificiary_id, a.buyer_id, a.invoice_no, a.invoice_date, a.is_lc, a.lc_sc_id, a.port_of_entry, a.port_of_loading, a.port_of_discharge, b.id as LC_SC_ID, b.export_lc_no as LC_SC_NO, b.lc_date as LC_SC_DATE,b.issuing_bank_name as ISSUING_BANK_NAME
		from com_export_invoice_ship_mst a, com_export_lc b
		where a.id=$data[0] and a.is_lc=1 and a.lc_sc_id=b.id and a.is_deleted=0 and b.is_deleted=0
		union all
		select a.id, a.benificiary_id, a.buyer_id, a.invoice_no, a.invoice_date, a.is_lc, a.lc_sc_id, a.port_of_entry, a.port_of_loading, a.port_of_discharge, b.id as LC_SC_ID, b.contract_no as LC_SC_NO, b.contract_date as LC_SC_DATE,b.issuing_bank as ISSUING_BANK_NAME
		from com_export_invoice_ship_mst a, com_sales_contract b
		where a.id=$data[0] and a.is_lc=2 and a.lc_sc_id=b.id and a.is_deleted=0 and b.is_deleted=0");
		$lc_sc_id=$inv_master_data[0]['LC_SC_ID'];
		$lc_sc_no=$inv_master_data[0]['LC_SC_NO'];
		$lc_sc_date=change_date_format($inv_master_data[0]['LC_SC_DATE']);
		$issuing_bank_name=$inv_master_data[0]['ISSUING_BANK_NAME'];
		$issuing_bank_details = explode(",", $issuing_bank_name);
		$issue_bank_details='';
		for( $i=0; $i<count($issuing_bank_details); $i++)
		{
			$issue_bank_name = $issuing_bank_details[0];
			$issue_branch_name = $issuing_bank_details[1];
			$issue_level = $issuing_bank_details[2];
			$issue_road = $issuing_bank_details[3];
			$issue_country = $issuing_bank_details[4];
			// $issue_swift = $issuing_bank_details[5];
			// $issue_att = $issuing_bank_details[6];
			if($issue_branch_name!="")  $issue_bank_details.= $issue_branch_name.", ";
			if($issue_level!="")  $issue_bank_details.= $issue_level.", ";
			if($issue_road!="")  $issue_bank_details= $issue_road.", ";
			if($issue_country!="")  $issue_bank_details.= $issue_country;
		}
		$pi_sql=sql_select("select id,pi_date, pi_number,export_pi_id from com_pi_master_details where id in (select pi_id from com_btb_lc_master_details where lc_number='$lc_sc_no' and is_deleted = 0 and status_active=1 ) and is_deleted = 0 and status_active=1 ");

		$pi_num=$export_pi_id=$pi_date='';
		foreach($pi_sql as $row)
		{
			$pi_num.=$row[csf('pi_number')].', ';
			$pi_date.=change_date_format($row[csf('pi_date')]).', ';
			$export_pi_id.=$row[csf('export_pi_id')].',';
		}

		$btb_lc_attachment_sql=sql_select("select a.id, a.is_lc_sc, a.lc_sc_id ,b.export_lc_no as LC_SC_NO, b.lc_date as LC_SC_DATE
		from com_btb_export_lc_attachment a, com_export_lc b
		where import_mst_id in (select id from com_btb_lc_master_details where lc_number='$lc_sc_no' and is_deleted = 0 and status_active=1 ) and a.is_lc_sc=0 and a.lc_sc_id=b.id and a.is_deleted = 0 and a.status_active=1 and b.is_deleted = 0 and b.status_active=1 
		union all
		select a.id, a.is_lc_sc, a.lc_sc_id ,b.contract_no as LC_SC_NO, b.contract_date as LC_SC_DATE
		from com_btb_export_lc_attachment a, com_sales_contract b
		where import_mst_id in (select id from com_btb_lc_master_details where lc_number='$lc_sc_no' and is_deleted = 0 and status_active=1 ) and a.is_lc_sc=1 and a.lc_sc_id=b.id and a.is_deleted = 0 and a.status_active=1 and b.is_deleted = 0 and b.status_active=1 ");

		$buyer_lc_sc_no='';
		$buyer_lc_sc_date='';
		foreach($btb_lc_attachment_sql as $row)
		{
			$buyer_lc_sc_no.=$row['LC_SC_NO'].', ';
			$buyer_lc_sc_date.=change_date_format($row['LC_SC_DATE']).', ';
		}
		$export_pi_dtls_sql = "SELECT  b.current_invoice_qnty as CURRENT_INVOICE_QNTY, b.current_invoice_rate as CURRENT_INVOICE_RATE, b.current_invoice_value as CURRENT_INVOICE_VALUE, c.color_id as COLOR_ID, c.uom as UOM, c.gmts_item_id as GMTS_ITEM_ID, c.embell_name as EMBELL_NAME, c.embell_type as EMBELL_TYPE, d.id as dtls_id, d.buyer_style_ref as BUYER_STYLE_REF
		from  com_export_invoice_ship_dtls b, com_pi_item_details c, subcon_ord_dtls d
		where b.mst_id=$data[0] and b.po_breakdown_id=c.id and c.work_order_dtls_id=d.id and b.is_deleted=0 and c.is_deleted=0 and d.is_deleted=0";
		$export_pi_dtls_result = sql_select($export_pi_dtls_sql);
		$all_dtls_id=array();
		foreach($export_pi_dtls_result as $row)
		{
			$all_dtls_id[$row[csf("dtls_id")]]=$row[csf("dtls_id")];
		}
		if(count($all_dtls_id)>0)
		{
			$con_dtls_sql="select mst_id, process, listagg(cast(embellishment_type as varchar(4000)),',') within group (order by embellishment_type) as embellishment_type
			from subcon_ord_breakdown where status_active=1 and is_deleted=0 and mst_id in(".implode(",",$all_dtls_id).")  and embellishment_type>0
			group by mst_id, process";
			//echo $con_dtls_sql;
			$con_dtls_sql_result = sql_select($con_dtls_sql);
			foreach($con_dtls_sql_result as $row)
			{
				$embtype_data[$row[csf("mst_id")]][$row[csf("process")]]=$row[csf("embellishment_type")];
				$embprocess_data[$row[csf("mst_id")]][$row[csf("process")]]=$row[csf("process")];
				
			}
		}
		?>
		<table id="" cellspacing="0" cellpadding="0" width="690">
			<tr>
				<td colspan="2" align="center" style="font-size:18px; font-weight:bold">
					<?
						echo $company_name_arr[$inv_master_data[0][csf("benificiary_id")]]["company_name"]."<br/>";
					?>
				</td>
			</tr>
			<tr>
				<td colspan="2" align="center" >
				<?
					echo "Factory: ".$company_name_arr[$inv_master_data[0][csf("benificiary_id")]]["company_details"];
				?>
				</td>
			</tr>
			<tr>
				<td colspan="2" align="center" >
				<strong>CERTIFICATE OF ORIGIN</strong> 
				</td>
			</tr>
			<tr>
				<td colspan="2">
				C.O No.: <? echo $inv_master_data[0][csf("invoice_no")]; ?> Date: <? echo $inv_master_data[0][csf("invoice_date")];?>
				</td>
			</tr>
		</table>
		<br>
		<table  cellspacing="0" cellpadding="0" width="690" rules="all" border="1" style="font-size:12px;">
			<tr>
				<td valign="top">
					<strong>Shipper/Exporter:</strong><br>
					<? echo $company_name_arr[$inv_master_data[0][csf("benificiary_id")]]["company_name"]."<br>Factory: ".$company_name_arr[$inv_master_data[0][csf("benificiary_id")]]["company_details"];?>
					<br><strong>Country of Origin:</strong><br>
					<span>We do hereby certify  that Accessories</span><br>
					<span>are Bangladesh Origin.</span>
				</td>
				<td valign="top">
					<strong>Applicant:</strong><br>
					<? echo $company_name_arr[$inv_master_data[0][csf("buyer_id")]]["company_name"]."<br>".$company_name_arr[$inv_master_data[0][csf("buyer_id")]]["company_details"];?><br>
					Final destination: <?echo $inv_master_data[0][csf("port_of_discharge")];?><br>
					Port of loading: <?echo $inv_master_data[0][csf("port_of_loading")];?>
				</td>
				<td valign="top">
					Invoice No: <? echo $inv_master_data[0][csf("invoice_no")]; ?> Date: <? echo $inv_master_data[0][csf("invoice_date")];?><br>
					<strong>DC No. <?echo $lc_sc_no;?></strong><br>
					<strong>Date:  <?echo $lc_sc_date;?></strong><br>
					L/C Issuing Bank: <br><? echo $issue_bank_name; ?><br>
					<? echo $issue_bank_details; ?>
				</td>
			</tr>
		</table> <br/>
		<table id="" cellspacing="0" cellpadding="0" border="0" rules="all" width="690" class="rpt_table" style="font-size:12px;">
			<tr>
				<td width="680" valign="top" style="border:0;">
				Accessories for 100% Export Oriented Readymade Garments Industry as per Proforma Invoice No. <?echo chop($pi_num,', ');?> DATED. <?echo chop($pi_date,', ');?> Export LC/Cont.No. <?echo chop($buyer_lc_sc_no,', ');?> DT: <?echo chop($buyer_lc_sc_date,', ');?> BIN- <?echo $company_name_arr[$inv_master_data[0][csf("buyer_id")]]["bin_no"];?> H.S Code: 6217.10.00 Bin of <?echo $company_name_arr[$inv_master_data[0][csf("benificiary_id")]]["company_name"];?>-<?echo $company_name_arr[$inv_master_data[0][csf("benificiary_id")]]["bin_no"];?>
				</td>
			</tr>

		</table>
		<br>
		<table style="font-size:12px;" cellspacing="0" cellpadding="0" border="1" rules="all" class="rpt_table">
			<thead>
				<tr>
					<th width="30">Sl</th>
					<th width="100">Style</th>
					<th width="80">Color</th>
					<th width="120" >Description of Goods</th>
					<th width="100">Wash Type</th>
					<th width="60">UOM</th>
					<th width="80">QTY</th>
					<!-- <th width="80">Unit Price<br>USD ($)</th>
					<th width="100">Total amount<br>IN USD ($)</th> -->
				</tr>
			</thead>
			<tbody>
				<? 
				$i=1;
				foreach ($export_pi_dtls_result as $row)
				{
				?>
					<tr>
					
						<td ><? echo $i; ?></td>
						<td ><? echo $row[csf("buyer_style_ref")]; ?></td>
						<td ><? echo $color_arr[$row[csf("color_id")]]; ?></td>
						<td ><? echo $garments_item[$row[csf("gmts_item_id")]]; ?></td>
						<td ><?
								$emb_name="";
								$emb_process_arr=$embprocess_data[$row[csf("dtls_id")]];
								foreach($emb_process_arr as $process_id)
								{
									if($embprocess_data[$row[csf("dtls_id")]][$process_id]==1) $process_type=$wash_wet_process;
									else if($embprocess_data[$row[csf("dtls_id")]][$process_id]==2) $process_type=$wash_dry_process;
									else if($embprocess_data[$row[csf("dtls_id")]][$process_id]==3) $process_type=$wash_laser_desing;
									$emb_id_arr=array_unique(explode(",",$embtype_data[$row[csf("dtls_id")]][$process_id]));
									foreach($emb_id_arr as $emb_id)
									{
										$emb_name.=$process_type[$emb_id].",";
									}
								}
								echo chop($emb_name,","); 
							?>
						</td>
						<td ><? echo $unit_of_measurement[$row[csf("uom")]]; ?></td>
						<td align="right"><? echo number_format($row["CURRENT_INVOICE_QNTY"],2); ?></td>
						<!-- <td align="right"><? echo number_format($row[csf("rate")],2); ?></td>
						<td align="right"><? echo number_format($row[csf("amount")],2); ?></td> -->
					</tr>					
					<? 
						$total_qnty += $row["CURRENT_INVOICE_QNTY"];
						$total_value += $row[csf("amount")];
						$i++;
				}
				?>
				</tr>
			</tbody>
			<tfoot>
				<tr>
					<td colspan='6' align="right">Total :</td>			
					<td  align="right"><? echo number_format($total_qnty,2); ?></td>
					<!-- <td align="right" >&nbsp;</td>
					<td  align="right" ><? echo number_format($total_value,2); ?></td> -->
				</tr>
				<!-- <tr><td colspan='7'><strong><?= "TOTAL U.S. DOLLARS: ".number_to_words(number_format($total_value,2, '.', ''), "USD", "Cent");?></strong></td></tr> -->
			</tfoot>
		</table><br/>
		<table>
			<tr><td style="height:25"></td></tr>
			<tr>
				<td>We do hereby certify that the Accessories are provide strictly as per above Proforma Invoice No. <br>
				and that all other terms & conditions there of have been fully complied with.</td>
			</tr>
			<tr>
				<td style="height:50"></td>
			</tr>
			<tr>
				<td style="font-size:18px; font-weight:bold">
					<?
						echo $company_name_arr[$inv_master_data[0][csf("benificiary_id")]]["company_name"]."<br/>";
					?>
				</td>
			</tr>
			<tr>
				<td style="height:100"></td>
			</tr>
			<tr>
				<td >
				Authorized Signature & Seal
				</td>
			</tr>
		</table>
						
		<?
		exit();
	}

	if($data[1]==6)
	{
		$company_name_sql=sql_select( "select id, company_name, company_short_name, contract_person, plot_no, level_no, road_no, block_no, city, country_id,bin_no from lib_company");
		// $color_arr=return_library_array( "select id, color_name from lib_color",'id','color_name');
		$lien_bank_info_sql=sql_select( "select id, bank_name, branch_name,address from lib_bank where lien_bank=1 ");
		foreach ($lien_bank_info_sql as $value) {
			$lien_bank_arra[$value[csf("id")]]["bank_name"] = $value[csf("bank_name")];
			$lien_bank_arra[$value[csf("id")]]["branch_name"] = $value[csf("branch_name")];
			$lien_bank_arra[$value[csf("id")]]["address"] = $value[csf("address")];
		}
		foreach($company_name_sql as $row)
		{
			$company_name_arr[$row[csf("id")]]["company_name"]=$row[csf("company_name")];
			$company_name_arr[$row[csf("id")]]["bin_no"]=$row[csf("bin_no")];
			$company_details =" ";
			$plot_no=$row[csf("plot_no")];
			$level_no=$row[csf("level_no")];
			$road_no=$row[csf("road_no")];
			$block_no=$row[csf("block_no")];
			$city_no=$row[csf("city")];
			$country_id=$row[csf("country_id")];

			if($plot_no!="")  $company_details= $plot_no.", ";
			if($level_no!="")  $company_details.= $level_no.", ";
			if($road_no!="")  $company_details.= $road_no.", ";
			if($block_no!="")  $company_details.= $block_no.", ";
			if($country_id!="")  $company_details.=return_field_value( "country_name","lib_country","id='$country_id'")." . ";
			$company_name_arr[$row[csf("id")]]["company_details"]= $company_details;

		}

		$inv_master_data=sql_select("select a.id, a.benificiary_id, a.buyer_id, a.invoice_no, a.invoice_date, a.is_lc, a.lc_sc_id, a.port_of_entry, a.port_of_loading, a.port_of_discharge, a.invoice_value as INVOICE_VALUE, b.id as LC_SC_ID, b.export_lc_no as LC_SC_NO, b.lc_date as LC_SC_DATE,b.issuing_bank_name as ISSUING_BANK_NAME,b.pay_term as PAY_TERM,b.lien_bank as LIEN_BANK
		from com_export_invoice_ship_mst a, com_export_lc b
		where a.id=$data[0] and a.is_lc=1 and a.lc_sc_id=b.id and a.is_deleted=0 and b.is_deleted=0
		union all
		select a.id, a.benificiary_id, a.buyer_id, a.invoice_no, a.invoice_date, a.is_lc, a.lc_sc_id, a.port_of_entry, a.port_of_loading, a.port_of_discharge, a.invoice_value as INVOICE_VALUE, b.id as LC_SC_ID, b.contract_no as LC_SC_NO, b.contract_date as LC_SC_DATE,b.issuing_bank as ISSUING_BANK_NAME,b.pay_term as PAY_TERM,b.lien_bank as LIEN_BANK
		from com_export_invoice_ship_mst a, com_sales_contract b
		where a.id=$data[0] and a.is_lc=2 and a.lc_sc_id=b.id and a.is_deleted=0 and b.is_deleted=0");
		$lc_sc_id=$inv_master_data[0]['LC_SC_ID'];
		$lc_sc_no=$inv_master_data[0]['LC_SC_NO'];
		$lc_sc_date=change_date_format($inv_master_data[0]['LC_SC_DATE']);
		$issuing_bank_name=$inv_master_data[0]['ISSUING_BANK_NAME'];
		$issuing_bank_details = explode(",", $issuing_bank_name);
		$issue_bank_details='';
		for( $i=0; $i<count($issuing_bank_details); $i++)
		{
			$issue_bank_name = $issuing_bank_details[0];
			$issue_branch_name = $issuing_bank_details[1];
			$issue_level = $issuing_bank_details[2];
			$issue_road = $issuing_bank_details[3];
			$issue_country = $issuing_bank_details[4];
			// $issue_swift = $issuing_bank_details[5];
			// $issue_att = $issuing_bank_details[6];
			if($issue_branch_name!="")  $issue_bank_details.= $issue_branch_name.", ";
			if($issue_level!="")  $issue_bank_details.= $issue_level.", ";
			if($issue_road!="")  $issue_bank_details= $issue_road.", ";
			if($issue_country!="")  $issue_bank_details.= $issue_country;
		}
		$pi_sql=sql_select("select id,pi_date, pi_number,export_pi_id from com_pi_master_details where id in (select pi_id from com_btb_lc_master_details where lc_number='$lc_sc_no' and is_deleted = 0 and status_active=1 ) and is_deleted = 0 and status_active=1 ");

		$pi_num=$export_pi_id=$pi_date='';
		foreach($pi_sql as $row)
		{
			$pi_num.=$row[csf('pi_number')].', ';
			$pi_date.=change_date_format($row[csf('pi_date')]).', ';
			$export_pi_id.=$row[csf('export_pi_id')].',';
		}

		$btb_lc_attachment_sql=sql_select("select a.id, a.is_lc_sc, a.lc_sc_id ,b.export_lc_no as LC_SC_NO, b.lc_date as LC_SC_DATE
		from com_btb_export_lc_attachment a, com_export_lc b
		where import_mst_id in (select id from com_btb_lc_master_details where lc_number='$lc_sc_no' and is_deleted = 0 and status_active=1 ) and a.is_lc_sc=0 and a.lc_sc_id=b.id and a.is_deleted = 0 and a.status_active=1 and b.is_deleted = 0 and b.status_active=1 
		union all
		select a.id, a.is_lc_sc, a.lc_sc_id ,b.contract_no as LC_SC_NO, b.contract_date as LC_SC_DATE
		from com_btb_export_lc_attachment a, com_sales_contract b
		where import_mst_id in (select id from com_btb_lc_master_details where lc_number='$lc_sc_no' and is_deleted = 0 and status_active=1 ) and a.is_lc_sc=1 and a.lc_sc_id=b.id and a.is_deleted = 0 and a.status_active=1 and b.is_deleted = 0 and b.status_active=1 ");

		$buyer_lc_sc_no='';
		$buyer_lc_sc_date='';
		foreach($btb_lc_attachment_sql as $row)
		{
			$buyer_lc_sc_no.=$row['LC_SC_NO'].', ';
			$buyer_lc_sc_date.=change_date_format($row['LC_SC_DATE']).', ';
		}
		
		?>
		<table id="" cellspacing="0" cellpadding="0" width="690">
			<tr>
				<td colspan="2" align="center" style="font-size:25px; font-weight:bold">
					<?
						echo $company_name_arr[$inv_master_data[0][csf("benificiary_id")]]["company_name"]."<br/>";
					?>
				</td>
			</tr>
			<tr>
				<td colspan="2" align="center" >
				<?
					echo "Factory: ".$company_name_arr[$inv_master_data[0][csf("benificiary_id")]]["company_details"];
				?>
				</td>
			</tr>
			<tr>
				<td colspan="2" >
				<span style="width:330;display: inline-block;margin-left:36%;"><strong>BILL OF EXCHANGE</strong> </span>
				<span ><strong>1</strong> </span>
				</td>
			</tr>
			<tr>
				<td>
				BE ref No.: <? echo $inv_master_data[0][csf("invoice_no")]; ?>
				</td>
				<td align="right" >
				Date: <? echo $inv_master_data[0][csf("invoice_date")];?>
				</td>
			</tr>
		</table>

		<table cellspacing="0" cellpadding="0" border="0" width="690" >
			<tr>
				<td width="340" style="font-size:22px;">
				Exchange for US$ 
				</td>
				<td width="340" style="font-size:22px;">
				<?echo number_format($inv_master_data[0]["INVOICE_VALUE"],2);?>
				</td>
			</tr>
			<tr>
				<td colspan="2">
				<?= "TOTAL U.S. DOLLARS: ".number_to_words(number_format($inv_master_data[0]["INVOICE_VALUE"],2, '.', ''), "USD", "Cent");?>
				</td>
			</tr>
			<tr>
				<td colspan="2" height="15"></td>
			</tr>
			<tr>
				<td >
				<? echo $pay_term[$inv_master_data[0]["PAY_TERM"]];?>
				</td>
				<td >
				of this FIRST Bill of Exchange
				</td>
			</tr>
			<tr>
				<td colspan="2">
					(SECOND of the same tenor and date being unpaid)
				</td>
			</tr>
			<tr>
				<td colspan="2" height="15"></td>
			</tr>
			<tr valign="top">
				<td>Pay to the order of</td>
				<td>
					<? echo $lien_bank_arra[$inv_master_data[0]["LIEN_BANK"]]["bank_name"]?><br>
					<? echo $lien_bank_arra[$inv_master_data[0]["LIEN_BANK"]]["branch_name"]?><br>
					<? echo $lien_bank_arra[$inv_master_data[0]["LIEN_BANK"]]["address"]?><br>
				</td>
			</tr>
			<tr>
				<td colspan="2" height="15"></td>
			</tr>
			<tr>
				<td colspan="2">
					DC No. <?echo $lc_sc_no;?><br>
					Date:  <?echo $lc_sc_date;?><br>
				</td>
			</tr>
			<tr>
				<td colspan="2">
				Export LC/Cont.No. <?echo chop($buyer_lc_sc_no,', ');?> DT: <?echo chop($buyer_lc_sc_date,', ');?>
				</td>
			</tr>
			<tr>
				<td colspan="2" height="15"></td>
			</tr>
			<tr valign="top">
				<td >Drawn Under: </td>
				<td>
					<? echo $issue_bank_name; ?><br>
					<? echo $issue_bank_details; ?>
				</td>
			</tr>
		</table>

		<table>
			<tr>
				<td style="height:20"></td>
			</tr>
			<tr>
				<td style="font-size:18px; font-weight:bold">
					<?
						echo $company_name_arr[$inv_master_data[0][csf("benificiary_id")]]["company_name"]."<br/>";
					?>
				</td>
			</tr>
			<tr>
				<td style="height:20"></td>
			</tr>
			<?
			if($data[2]==1){echo "<tr><td style='height:40'></td></tr>";}
			?>
			<tr>
				<td >
				Authorized Signature & Seal
				</td>
			</tr>
		</table>
		<?
		if($data[2]==1){echo "<span style='page-break-after: always;'></span>";}
		?>
		<table id="" cellspacing="0" cellpadding="0" width="690">
			<tr>
				<td colspan="2" align="center" style="font-size:25px; font-weight:bold">
					<?
						echo $company_name_arr[$inv_master_data[0][csf("benificiary_id")]]["company_name"]."<br/>";
					?>
				</td>
			</tr>
			<tr>
				<td colspan="2" align="center" >
				<?
					echo "Factory: ".$company_name_arr[$inv_master_data[0][csf("benificiary_id")]]["company_details"];
				?>
				</td>
			</tr>
			<tr>
				<td colspan="2" >
					<span style="width:330;display: inline-block;margin-left:36%;"><strong>BILL OF EXCHANGE</strong> </span>
					<span ><strong>2</strong> </span>
				</td>
			</tr>
			<tr>
				<td>
				BE ref No.: <? echo $inv_master_data[0][csf("invoice_no")]; ?>
				</td>
				<td align="right" >
				Date: <? echo $inv_master_data[0][csf("invoice_date")];?>
				</td>
			</tr>
		</table>

		<table cellspacing="0" cellpadding="0" border="0" width="690"  style="font-size:12px;">
			<tr>
				<td width="340" style="font-size:22px;">
				Exchange for US$ 
				</td>
				<td width="340" style="font-size:22px;">
				<?echo number_format($inv_master_data[0]["INVOICE_VALUE"],2);?>
				</td>
			</tr>
			<tr>
				<td colspan="2">
				<?= "TOTAL U.S. DOLLARS: ".number_to_words(number_format($inv_master_data[0]["INVOICE_VALUE"],2, '.', ''), "USD", "Cent");?>
				</td>
			</tr>
			<tr>
				<td colspan="2" height="15"></td>
			</tr>
			<tr>
				<td >
				<? echo $pay_term[$inv_master_data[0]["PAY_TERM"]];?>
				</td>
				<td >
				of this SECOND Bill of Exchange
				</td>
			</tr>
			<tr>
				<td colspan="2">
					(FIRST of the same tenor and date being unpaid)
				</td>
			</tr>
			<tr>
				<td colspan="2" height="15"></td>
			</tr>
			<tr valign="top">
				<td>Pay to the order of</td>
				<td>
					<? echo $lien_bank_arra[$inv_master_data[0]["LIEN_BANK"]]["bank_name"]?><br>
					<? echo $lien_bank_arra[$inv_master_data[0]["LIEN_BANK"]]["branch_name"]?><br>
					<? echo $lien_bank_arra[$inv_master_data[0]["LIEN_BANK"]]["address"]?><br>
				</td>
			</tr>
			<tr>
				<td colspan="2" height="15"></td>
			</tr>
			<tr>
				<td colspan="2">
					DC No. <?echo $lc_sc_no;?><br>
					Date:  <?echo $lc_sc_date;?><br>
				</td>
			</tr>
			<tr>
				<td colspan="2">
				Export LC/Cont.No. <?echo chop($buyer_lc_sc_no,', ');?> DT: <?echo chop($buyer_lc_sc_date,', ');?>
				</td>
			</tr>
			<tr>
				<td colspan="2" height="15"></td>
			</tr>
			<tr valign="top">
				<td >Drawn Under: </td>
				<td>
					<? echo $issue_bank_name; ?><br>
					<? echo $issue_bank_details; ?>
				</td>
			</tr>
		</table>

		<table>
			<tr>
				<td style="height:20"></td>
			</tr>
			<tr>
				<td style="font-size:18px; font-weight:bold">
					<?
						echo $company_name_arr[$inv_master_data[0][csf("benificiary_id")]]["company_name"]."<br/>";
					?>
				</td>
			</tr>
			<tr>
				<td style="height:20"></td>
			</tr>
			<?
			if($data[2]==1){echo "<tr><td style='height:40'></td></tr>";}
			?>
			<tr>
				<td >
				Authorized Signature & Seal
				</td>
			</tr>
		</table>
					
		<?
		exit();
	}


}

if($action=="invoice_report_print_ci3")
{
	extract($_REQUEST);
	$update_id=$data;
	/*$SupplierArr=array();
	$sqlSupplier=sql_select("select s.id, s.supplier_name from lib_supplier s, lib_supplier_tag_company b where s.status_active =1 and s.is_deleted=0 and b.supplier_id=s.id and s.id in (select supplier_id from lib_supplier_party_type where party_type in (30,31,32))");
	foreach($sqlSupplier as $row){
		$SupplierArr[$row[csf('id')]]=$row[csf('supplier_name')];
	}*/
	
	$applicant_sql=sql_select( "select a.id, a.buyer_name, a.short_name, a.address_1 from lib_buyer a");
	foreach($applicant_sql as $row)
	{
		$buyer_name_arr[$row[csf("id")]]["buyer_name"]=$row[csf("buyer_name")];
		$buyer_name_arr[$row[csf("id")]]["address_1"]=$row[csf("address_1")];
	}
	$bank_sql=sql_select( "select id, bank_name, address, swift_code from lib_bank");
	foreach($bank_sql as $row)
	{
		$bank_name_arr[$row[csf("id")]]["bank_name"]=$row[csf("bank_name")];
		$bank_name_arr[$row[csf("id")]]["address"]=$row[csf("address")];
		$bank_name_arr[$row[csf("id")]]["swift_code"]=$row[csf("swift_code")];
	}
	$bank_account_sql=sql_select( "select id, account_id, account_type, account_no from lib_bank_account where is_deleted=0 ");
	foreach($bank_account_sql as $row)
	{
		$bank_acc_arr[$row[csf("account_id")]][$row[csf("account_type")]]["account_no"]=$row[csf("account_no")];
	}
	$inv_master_data=sql_select("select id, benificiary_id, buyer_id, location_id, invoice_no, invoice_date, exp_form_no, exp_form_date, is_lc, lc_sc_id,  bl_no, feeder_vessel, inco_term, inco_term_place, shipping_mode, port_of_entry, port_of_loading, port_of_discharge, main_mark, side_mark, net_weight, gross_weight, cbm_qnty, place_of_delivery, delv_no, consignee, notifying_party, item_description, discount_ammount, bonus_ammount, commission, total_carton_qnty, bl_date, hs_code, mother_vessel, category_no, forwarder_name, etd,co_no, total_measurment, invoice_value, net_invo_value from com_export_invoice_ship_mst where id=$update_id");
	$id=$inv_master_data[0][csf("id")];
	$benificiary_id=$inv_master_data[0][csf("benificiary_id")];
	$buyer_id=$inv_master_data[0][csf("buyer_id")];
	$location_id=$inv_master_data[0][csf("location_id")];
	$invoice_no=$inv_master_data[0][csf("invoice_no")];
	$invoice_date=$inv_master_data[0][csf("invoice_date")];
	$exp_form_no=$inv_master_data[0][csf("exp_form_no")];
	$exp_form_date=$inv_master_data[0][csf("exp_form_date")];
	$is_lc=$inv_master_data[0][csf("is_lc")];
	$lc_sc_id=$inv_master_data[0][csf("lc_sc_id")];
	$bl_no=$inv_master_data[0][csf("bl_no")];
	$feeder_vessel=$inv_master_data[0][csf("feeder_vessel")];
	$inco_term=$inv_master_data[0][csf("inco_term")];
	$inco_term_place=$inv_master_data[0][csf("inco_term_place")];
	$shipping_mode=$inv_master_data[0][csf("shipping_mode")];
	$port_of_entry=$inv_master_data[0][csf("port_of_entry")];
	$port_of_loading=$inv_master_data[0][csf("port_of_loading")];
	$port_of_discharge=$inv_master_data[0][csf("port_of_discharge")];
	$main_mark=$inv_master_data[0][csf("main_mark")];
	$side_mark=$inv_master_data[0][csf("side_mark")];
	$net_weight=$inv_master_data[0][csf("net_weight")];
	$gross_weight=$inv_master_data[0][csf("gross_weight")];
	$cbm_qnty=$inv_master_data[0][csf("cbm_qnty")];
	$place_of_delivery=$inv_master_data[0][csf("place_of_delivery")];
	$delv_no=$inv_master_data[0][csf("delv_no")];
	$consignee=$inv_master_data[0][csf("consignee")];
	$notifying_party=$inv_master_data[0][csf("notifying_party")];
	$item_description=$inv_master_data[0][csf("item_description")];
	$discount_ammount=$inv_master_data[0][csf("discount_ammount")];
	$bonus_ammount=$inv_master_data[0][csf("bonus_ammount")];
	$commission=$inv_master_data[0][csf("commission")];
	$total_carton_qnty=$inv_master_data[0][csf("total_carton_qnty")];
	$bl_date=$inv_master_data[0][csf("bl_date")];
	$hs_code=$inv_master_data[0][csf("hs_code")];
	$mother_vessel=$inv_master_data[0][csf("mother_vessel")];
	$category_no=$inv_master_data[0][csf("category_no")];
	$forwarder_name=$inv_master_data[0][csf("forwarder_name")];
	$etd=$inv_master_data[0][csf("etd")];
	$co_no=$inv_master_data[0][csf("co_no")];
	$total_measurment=$inv_master_data[0][csf("total_measurment")];
	$net_invo_value=$inv_master_data[0][csf("net_invo_value")];
	$total_discount=$inv_master_data[0][csf("invoice_value")]-$inv_master_data[0][csf("net_invo_value")];
	
	$itemIdArr=array();
	$setQtyArr=array();
	$poIdArr=array();
	$dtls_sql="select a.id as dtls_id, a.po_breakdown_id,c.total_set_qnty from  com_export_invoice_ship_dtls a,  wo_po_break_down b, wo_po_details_master c where a.po_breakdown_id=b.id and b.job_no_mst=c.job_no and a.current_invoice_qnty>0 and a.status_active=1 and a.is_deleted=0 and a.mst_id=$update_id";
	$PO_agent=sql_select($dtls_sql);
	foreach($PO_agent as $row){
		$poIdArr[]=$row[csf('po_breakdown_id')];
		$setQtyArr[$row[csf('po_breakdown_id')]]=$row[csf('total_set_qnty')];
	}
	
	$carton_arr=array();
	$sqlCarton=sql_select("select a.id,a.sys_number, a.dl_no, b.delivery_mst_id,b.po_break_down_id,b.total_carton_qnty,b.carton_qnty from pro_ex_factory_delivery_mst a,pro_ex_factory_mst b where a.id=b.delivery_mst_id and b.po_break_down_id in(".implode(",",$poIdArr).")");
	foreach($sqlCarton as $rowCarton)
	{
		$carton_arr[$rowCarton[csf('po_break_down_id')]]+=$rowCarton[csf('total_carton_qnty')];
	}
	$agent_id="";
	$fristPo=array_shift($poIdArr);
	$sql_agent=sql_select("select agent_name from wo_po_details_master a, wo_po_break_down b where a.job_no=b.job_no_mst and b.id='$fristPo'");
	foreach($sql_agent as $row_agent){
		$agent_id=$row_agent[csf('agent_name')];
	}
	
	
	if($is_lc==1)
	{
		$lc_sc_data=sql_select("select id, export_lc_no, lc_date, notifying_party, consignee, issuing_bank_name, negotiating_bank, lien_bank, pay_term, applicant_name,inco_term,lien_bank,nominated_shipp_line, buyer_name from com_export_lc where id='".$lc_sc_id."' ");
		foreach($lc_sc_data as $row)
		{
			$lc_sc_id=$row[csf("id")];
			$lc_sc_no=$row[csf("export_lc_no")];
			$lc_sc_date=change_date_format($row[csf("lc_date")]);
			//$notifying_party=$row[csf("notifying_party")];
			//$consignee=$row[csf("consignee")];//also notify party
			$issuing_bank_name=$row[csf("issuing_bank_name")];
			$negotiating_bank=$row[csf("lien_bank")];
			$pay_term_id=$row[csf("pay_term")];
			$applicant_name=$row[csf("applicant_name")];
			$buyer_name=$row[csf("buyer_name")];
			$inco_term=$row[csf("inco_term")];
			$lien_bank=$row[csf("lien_bank")];
			$shipping_line=$row[csf("nominated_shipp_line")];
			$negotiating_bank_text=$row[csf("negotiating_bank")];
		}
		
			$cate_hs_sql=sql_select("select wo_po_break_down_id, fabric_description, category_no, hs_code from com_export_lc_order_info where com_export_lc_id='".$lc_sc_id."'");
			foreach($cate_hs_sql as $row)
			{
				$order_la_data[$row[csf("wo_po_break_down_id")]]["category_no"]=$row[csf("category_no")];
				$order_la_data[$row[csf("wo_po_break_down_id")]]["hs_code"]=$row[csf("hs_code")];
				$order_la_data[$row[csf("wo_po_break_down_id")]]["fabric_description"]=$row[csf("fabric_description")];
			    $all_order_id[$row[csf("wo_po_break_down_id")]]=$row[csf("wo_po_break_down_id")];
			}
	}
	else
	{
		$lc_sc_data=sql_select("select id, contract_no, contract_date, notifying_party, consignee, lien_bank, pay_term, applicant_name,inco_term,lien_bank,shipping_line,buyer_name from com_sales_contract where id='".$lc_sc_id."'  and status_active=1");
		foreach($lc_sc_data as $row)
		{
			$lc_sc_id=$row[csf("id")];
			$lc_sc_no=$row[csf("contract_no")];
			$lc_sc_date=change_date_format($row[csf("contract_date")]);
			//$notifying_party=$row[csf("notifying_party")];
			//$consignee=$row[csf("consignee")];//also notify party
			$issuing_bank_name="";
			$negotiating_bank=$row[csf("lien_bank")];
			$pay_term_id=$row[csf("pay_term")];
			$applicant_name=$row[csf("applicant_name")];
			$buyer_name=$row[csf("buyer_name")];
			$inco_term=$row[csf("inco_term")];
			$lien_bank=$row[csf("lien_bank")];
			$shipping_line=$row[csf("shipping_line")];
			$negotiating_bank_text="";
		}
		
		$cate_hs_sql=sql_select("select wo_po_break_down_id, fabric_description, category_no, hs_code from com_sales_contract_order_info where com_sales_contract_id='".$lc_sc_id."' and status_active=1");
		foreach($cate_hs_sql as $row)
		{
			$order_la_data[$row[csf("wo_po_break_down_id")]]["category_no"]=$row[csf("category_no")];
			$order_la_data[$row[csf("wo_po_break_down_id")]]["hs_code"]=$row[csf("hs_code")];
			$order_la_data[$row[csf("wo_po_break_down_id")]]["fabric_description"]=$row[csf("fabric_description")];
			$all_order_id[$row[csf("wo_po_break_down_id")]]=$row[csf("wo_po_break_down_id")];
		}
	}
	
	$company_name_sql=sql_select( "select id, company_name, company_short_name, contract_person, plot_no, level_no, road_no, block_no, city, country_id,erc_no from lib_company where id ='$benificiary_id'");
	foreach($company_name_sql as $row)
	{
		$company_name=$row[csf("company_name")];
		$company_short_name=$row[csf("company_short_name")];
		$contract_person=$row[csf("contract_person")];
		$plot_no=$row[csf("plot_no")];
		$level_no=$row[csf("level_no")];
		$road_no=$row[csf("road_no")];
		$block_no=$row[csf("block_no")];
		$city=$row[csf("city")];
		$country_id=$row[csf("country_id")];
		$erc_no=$row[csf("erc_no")];
		
	}
	
	$country_name=return_field_value( "country_name","lib_country","id='$country_id'");
	$carrier=$SupplierArr[$forwarder_name];
	$applicant=$buyer_name_arr[$applicant_name]["buyer_name"];
	$applicantAddress=$buyer_name_arr[$applicant_name]["address_1"];
	$agent=$buyer_name_arr[$agent_id]["buyer_name"];
	$agentAddress=$buyer_name_arr[$agent_id]["address_1"];
		
	$dtls_sql="select a.id as dtls_id, a.po_breakdown_id, a.current_invoice_rate, a.current_invoice_qnty, a.current_invoice_value, b.po_number, c.style_ref_no, c.gmts_item_id, c.order_uom, c.gmts_item_id from  com_export_invoice_ship_dtls a,  wo_po_break_down b, wo_po_details_master c where a.po_breakdown_id=b.id and b.job_no_mst=c.job_no and a.current_invoice_qnty>0 and a.status_active=1 and a.is_deleted=0 and a.mst_id=$update_id";
	$result=sql_select($dtls_sql);
	$company_logo=return_field_value("image_location","common_photo_library","file_type=1 and form_name='company_details' and master_tble_id='$benificiary_id'","image_location");

	?>
	<table width='1000' cellspacing="0" cellpadding="0" border="0" >
		<tr>
			<td width="1000" align="center"><img src="../../<? echo $company_logo; ?>" height="70" width="350"></td>
		</tr>
		<tr>
			<td width="1000" align="center"><?echo $city.",".return_field_value( "country_name","lib_country","id=".$country_id);?></td>
		</tr>
		<tr>
			<td width="1000" align="center" style="font-size:16px;"><b>Commercial Invoice</b></td>
		</tr>
	</table>
	<br>
    <table width='1000' cellspacing="0" cellpadding="0" border="1" >
        <tr>
            <td colspan="4" valign="top"> 
                <strong><u>SHIPPER/EXPORTER:</u></strong>
                <br/>
                <b><?php echo $company_name; ?></b>
                <?php
                if($city!="")  $comany_details.= "<br>".$city.", ";
                if($country_id!="")  $comany_details.="<br>".$country_name.".";
                echo  $comany_details;
                ?>
            </td>
            <td colspan="4" valign="top">
                Invoice No.: <?php echo $invoice_no;  ?><br/>
                EXP No. <?php echo $exp_form_no; ?><br/>
                L/C No. <?php echo $lc_sc_no; ?><br/>
                B/L No. <?php echo $bl_no; ?>
            </td>
            <td style="border:none"  colspan="3" valign="top">
                Date: <? echo change_date_format($invoice_date);?><br/>
                Date:<? if($exp_form_date!="" && $exp_form_date!="0000-00-00" ) echo change_date_format($exp_form_date);?><br/>
                Date:<? echo $lc_sc_date;?><br/>
                Date:<?  if($bl_date!="" && $bl_date!="0000-00-00") echo change_date_format($bl_date); ?><br/>
            </td>
        </tr>
        <tr>
			<td colspan="4" valign="top">
				<strong><u>Buyer Name & Address:</u></strong><br/>
                <?
                    echo $buyer_name_arr[$buyer_name]["buyer_name"]."<br/>";
                    echo $buyer_name_arr[$buyer_name]["address_1"];
                ?>
			</td>
            <td colspan="7" valign="top">
                <strong><u>Issueing Bank:</u></strong><br/>
                <? $issue_bank_info=explode(',',$issuing_bank_name);
                    foreach($issue_bank_info as $row)
                    {
                        echo $row.'<br>';
                    }
                ?>
            </td>
        </tr>
        <tr>
            <td colspan="4" valign="top"> 
                <strong><u>Buying Agent:</u></strong><br/>
                <?
                echo $agent."<br/>";
                echo $agentAddress;
                ?> 
            </td>
            <td colspan="7" valign="top">
                <strong><u>Beneficiary's Bank Name & Address:</u></strong><br/>
                <?
                echo $bank_name_arr[$lien_bank]["bank_name"]."<br/>";
                echo $bank_name_arr[$lien_bank]["address"]."<br/>";
                echo 'A/C NO: '.$bank_acc_arr[$lien_bank][10]["account_no"].', SWIFT'.$bank_name_arr[$lien_bank]["swift_code"];
                ?>
            </td>
        </tr>
        <tr>
            <td colspan="4" valign="top"> 
                <strong><u>Consignee/Notify Party:</u></strong><br/>
                <?
                if($buyer_name_arr[$consignee]["buyer_name"]!=''){echo "&nbsp;&nbsp;".$buyer_name_arr[$consignee]["buyer_name"]."<br/>";}
                if($buyer_name_arr[$consignee]["address_1"]!=''){echo "&nbsp;&nbsp;".$buyer_name_arr[$consignee]["address_1"]."<br/>";}
                if($buyer_name_arr[$notifying_party]["buyer_name"]!=''){echo "&nbsp;&nbsp;".$buyer_name_arr[$notifying_party]["buyer_name"]."<br/>";}
                if($buyer_name_arr[$notifying_party]["address_1"]!=''){echo "&nbsp;&nbsp;".$buyer_name_arr[$notifying_party]["address_1"];}
                ?>
            </td>
            <td colspan="4" valign="top">
                SAILING ON / ABOUT:<br/>
                &nbsp;&nbsp;
            </td>
            <td colspan="3" valign="top">
                PAYMENT TERMS :<br/>
                <? echo "&nbsp;&nbsp;".$pay_term[$pay_term_id];?>
            </td>
        </tr>
        <tr>
            <td colspan="3" valign="top">
                CARRIER : <? echo $carrier;?>
            </td>
            <td colspan="3" valign="top">
                SHIP MODE : <? echo $shipment_mode[$shipping_mode];?>
            </td>
            <td colspan="5" valign="top"> 
                F/Vss: <? echo $feeder_vessel;?>, 
                M/Vssl: <? echo $mother_vessel;?>
            </td>
        </tr>
        
        <tr style="font-size:small; font-weight:bold" align="center">
            <td valign="top">
				<u>Port OF Loading:-</u>
				<? echo $port_of_loading; ?>
            </td valign="top">
            <td colspan="2">
				<u>Port OF Dischage:-</u>
				<? echo $port_of_discharge;?> 
            </td>
            <td colspan="2" valign="top"> 
				<u>Final Destination:-</u><br/>
				<? echo "&nbsp;&nbsp;".$place_of_delivery; ?>
            </td>
            <td  colspan="4" valign="top">
				SHIPPING TERMS:<br/>
				<? echo "&nbsp;&nbsp;".$incoterm[$inco_term].",".$inco_term_place; ?>
            </td>
            <td colspan="2" valign="top">
             	<u>Country of Origin:-</u>
            	<?  echo "&nbsp;&nbsp;Bangladesh";?>
            </td>
        </tr>
        
        <tr style="font-size:small; font-weight:bold" align="center">
            <td >STYLE / ARTICLE NO </td>
            <td >P.O. NO</td>
            <td >ITEM DESCRIPTION</td>
            <td >Fabrication</td>
            <td >H.S code</td>
            <td colspan="2">QNTY PCS/PACK</td>
            <td >QNTY CTNS</td>
            <td >UNIT PRICE US$</td>
            <td colspan="2">TOTAL AMOUNT</td>
        </tr>
        
        <?
		foreach($result as $row)
		{
            $gmts_item = '';
            $gmts_item_id = explode(",", $row[csf('gmts_item_id')]);
            foreach ($gmts_item_id as $item_id) {
                if ($gmts_item == "")
                    $gmts_item = $garments_item[$item_id];
                else
                    $gmts_item .= "," . $garments_item[$item_id];
            }
			?>
            <tr style="font-size:small">
                <td width="120"><? echo $row[csf('style_ref_no')]; ?></td>
                <td width="80"><? echo $row[csf("po_number")]; ?></td>
                <td width="120"> <? echo $gmts_item; ?> </td>
                <td width="200"> <? echo $order_la_data[$row[csf('po_breakdown_id')]]["fabric_description"]; ?> </td>
                <td width="80" align="center"><?  echo $order_la_data[$row[csf('po_breakdown_id')]]["hs_code"];; ?></td>
                <td width="60" align="right"><? echo number_format($row[csf('current_invoice_qnty')],0,".",",");  ?></td>
                <td width="50" align="right"><? echo $unit_of_measurement[$row[csf('order_uom')]]; ?></td>
                <td width="50"  align="right"><? echo number_format($carton_arr[$row[csf('po_breakdown_id')]],0,".",","); ?></td>
                <td width="50" align="right"><?  echo number_format($row[csf("current_invoice_rate")],2); ?></td>
                <td width="50" align="right">US$</td>
                <td align="right"><? echo number_format($row[csf("current_invoice_value")],2,".",","); ?></td>
            </tr>
            <?
			$total_value+=$row[csf("current_invoice_value")];
			$total_qnty+=$row[csf("current_invoice_qnty")]*$setQtyArr[$row[csf('po_breakdown_id')]];
			$last_uom=$unit_of_measurement[$row[csf('order_uom')]];
			$total_po_carton_qnty+=$carton_arr[$row[csf('po_breakdown_id')]];
			$i++;
		}
		?>
        <tr>
            <td colspan="5" align="right">
            Total
            </td>
            <td  align="right" colspan="2">
            <? echo number_format($total_qnty,0,".",",")." Pcs" ?>
            </td>
            <td  align="right">
            <? echo $total_po_carton_qnty; ?>
            </td>
            <td >
            </td>
            <td align="right" colspan="2">
            <? echo "US$&nbsp;&nbsp;".number_format($total_value,2,".",","); ?>
            </td>
        </tr>
        <tr>
            <td colspan="11">
                SAY: <? echo number_to_words(def_number_format($net_invo_value,2,""),"USD", "CENTS");?>
            </td>
        </tr>
        <tr>
            <td colspan="3">
                FOR <? echo strtoupper($company_name);?>
                <br/>
                <br/>
                <br/>
                <br/>
                <br/>
                <br/>
                <br/>
                <br/>
                Authorized Signature
                <br/>
                <br/>
                CAT. # <? echo  $category_no ;?><br/>
                <!-- HS. CODE : <? echo  $hs_code ;?> -->
            </td>
            <td colspan="2" valign="top">
                TOTAL QTY&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;:&nbsp;<? echo number_format($total_qnty,0,".",","); ?> PCS<br/>
                TOTAL CTN&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;:&nbsp;<? echo number_format($total_carton_qnty,0,".",","); ?> CTNS<br/>
                TOTAL N. WT&nbsp;&nbsp;:&nbsp;<? echo number_format($net_weight,2); ?> KG<br/>
                TOTAL G. WT&nbsp;&nbsp;:&nbsp;<? echo number_format($gross_weight,2); ?> KG<br/>
                TOTAL VOL&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;:&nbsp;<? echo number_format($cbm_qnty,2); ?> CBM<br/>
                CARTON MEASURMENT:&nbsp;<?echo $total_measurment; ?> <br/>
            </td>
            <td colspan="6" valign="top">
                <strong><u>SHIPPING MARK:</u></strong><br/>
                <?echo $buyer_name_arr[$buyer_name]["buyer_name"]?><br/>
                ITEM NO:<br/>
                CONTARCT NO:<br/>
                SIZE:<br/>
                QTY:<br/>
                CARTON NO:<br/>
                GROSS WEIGHT:<br/>
                NET WEIGHT:<br/>
            </td>
        </tr>
    </table>
	<?
	
}

if($action=="invoice_report_print_2")
{
	extract($_REQUEST);
	// $update_id=$data;

	$brand_arr=return_library_array( "select id, brand_name from lib_buyer_brand brand",'id','brand_name');
	$applicant_sql=sql_select( "select a.id, a.buyer_name, a.short_name, a.address_1 from lib_buyer a");
	foreach($applicant_sql as $row)
	{
		$buyer_name_arr[$row[csf("id")]]["buyer_name"]=$row[csf("buyer_name")];
		$buyer_name_arr[$row[csf("id")]]["address_1"]=$row[csf("address_1")];
	}
	$bank_sql=sql_select( "SELECT id, bank_name, branch_name, address, swift_code from lib_bank");
	foreach($bank_sql as $row)
	{
		$bank_name_arr[$row[csf("id")]]["bank_name"]=$row[csf("bank_name")];
		$bank_name_arr[$row[csf("id")]]["branch_name"]=$row[csf("branch_name")];
		$bank_name_arr[$row[csf("id")]]["address"]=$row[csf("address")];
		$bank_name_arr[$row[csf("id")]]["swift_code"]=$row[csf("swift_code")];
	}
	$bank_account_sql=sql_select( "select id, account_id, account_type, account_no from lib_bank_account where is_deleted=0 ");
	foreach($bank_account_sql as $row)
	{
		$bank_acc_arr[$row[csf("account_id")]][$row[csf("account_type")]]["account_no"]=$row[csf("account_no")];
	}
	$inv_master_data=sql_select("SELECT id, benificiary_id, buyer_id, location_id, invoice_no, invoice_date, exp_form_no, exp_form_date, is_lc, lc_sc_id, bl_no, feeder_vessel, inco_term, inco_term_place, shipping_mode, port_of_entry, port_of_loading, port_of_discharge, main_mark, side_mark, carton_net_weight, carton_gross_weight, cbm_qnty, place_of_delivery, delv_no, consignee, notifying_party, item_description, discount_ammount, bonus_ammount, commission, total_carton_qnty, bl_date, hs_code, mother_vessel, category_no, forwarder_name, etd,co_no, total_measurment, invoice_value, net_invo_value, container_no, seal_no, etd, country_id,commission_percent from com_export_invoice_ship_mst where id=$update_id");
	$id=$inv_master_data[0][csf("id")];
	$benificiary_id=$inv_master_data[0][csf("benificiary_id")];
	$buyer_id=$inv_master_data[0][csf("buyer_id")];
	$location_id=$inv_master_data[0][csf("location_id")];
	$invoice_no=$inv_master_data[0][csf("invoice_no")];
	$invoice_date=$inv_master_data[0][csf("invoice_date")];
	$exp_form_no=$inv_master_data[0][csf("exp_form_no")];
	$exp_form_date=$inv_master_data[0][csf("exp_form_date")];
	$is_lc=$inv_master_data[0][csf("is_lc")];
	$lc_sc_id=$inv_master_data[0][csf("lc_sc_id")];
	$bl_no=$inv_master_data[0][csf("bl_no")];
	$feeder_vessel=$inv_master_data[0][csf("feeder_vessel")];
	$inco_term=$inv_master_data[0][csf("inco_term")];
	$inco_term_place=$inv_master_data[0][csf("inco_term_place")];
	$shipping_mode=$inv_master_data[0][csf("shipping_mode")];
	$port_of_entry=$inv_master_data[0][csf("port_of_entry")];
	$port_of_loading=$inv_master_data[0][csf("port_of_loading")];
	$port_of_discharge=$inv_master_data[0][csf("port_of_discharge")];
	$main_mark=$inv_master_data[0][csf("main_mark")];
	$side_mark=$inv_master_data[0][csf("side_mark")];
	$net_weight=$inv_master_data[0][csf("carton_net_weight")];
	$gross_weight=$inv_master_data[0][csf("carton_gross_weight")];
	$cbm_qnty=$inv_master_data[0][csf("cbm_qnty")];
	$place_of_delivery=$inv_master_data[0][csf("place_of_delivery")];
	$delv_no=$inv_master_data[0][csf("delv_no")];
	$consignee=$inv_master_data[0][csf("consignee")];
	$notifying_party=$inv_master_data[0][csf("notifying_party")];
	$item_description=$inv_master_data[0][csf("item_description")];
	$discount_ammount=$inv_master_data[0][csf("discount_ammount")];
	$bonus_ammount=$inv_master_data[0][csf("bonus_ammount")];
	$commission=$inv_master_data[0][csf("commission")];
	$commission_percent=$inv_master_data[0][csf("commission_percent")];
	$total_carton_qnty=$inv_master_data[0][csf("total_carton_qnty")];
	$bl_date=$inv_master_data[0][csf("bl_date")];
	$hs_code=$inv_master_data[0][csf("hs_code")];
	$mother_vessel=$inv_master_data[0][csf("mother_vessel")];
	$category_no=$inv_master_data[0][csf("category_no")];
	$forwarder_name=$inv_master_data[0][csf("forwarder_name")];
	$etd=$inv_master_data[0][csf("etd")];
	$co_no=$inv_master_data[0][csf("co_no")];
	$total_measurment=$inv_master_data[0][csf("total_measurment")];
	$net_invo_value=$inv_master_data[0][csf("net_invo_value")];
	$container_no=$inv_master_data[0][csf("container_no")];
	$seal_no=$inv_master_data[0][csf("seal_no")];
	$etd=$inv_master_data[0][csf("etd")];
	$inv_country_id=$inv_master_data[0][csf("country_id")];
	$total_discount=$inv_master_data[0][csf("invoice_value")]-$inv_master_data[0][csf("net_invo_value")];
	
	$itemIdArr=array();
	$setQtyArr=array();
	$poIdArr=array();
	$dtls_sql="select a.id as dtls_id, a.po_breakdown_id,c.total_set_qnty from  com_export_invoice_ship_dtls a,  wo_po_break_down b, wo_po_details_master c where a.po_breakdown_id=b.id and b.job_no_mst=c.job_no and a.current_invoice_qnty>0 and a.status_active=1 and a.is_deleted=0 and a.mst_id=$update_id";
	$PO_agent=sql_select($dtls_sql);
	foreach($PO_agent as $row){
		$poIdArr[]=$row[csf('po_breakdown_id')];
		$setQtyArr[$row[csf('po_breakdown_id')]]=$row[csf('total_set_qnty')];
	}
	
	$carton_arr=array();
	$sqlCarton=sql_select("SELECT a.id,a.sys_number, a.dl_no, b.delivery_mst_id,b.po_break_down_id,b.total_carton_qnty,b.carton_qnty from pro_ex_factory_delivery_mst a,pro_ex_factory_mst b where a.id=b.delivery_mst_id and b.po_break_down_id in(".implode(",",$poIdArr).")");
	foreach($sqlCarton as $rowCarton)
	{
		$carton_arr[$rowCarton[csf('po_break_down_id')]]=$rowCarton[csf('total_carton_qnty')];
	}
	$agent_id="";
	// $fristPo=array_shift($poIdArr);
	$sql_fabric=sql_select("select b.ID, c.CONSTRUCTION, c.COMPOSITION from wo_po_break_down b, wo_pre_cost_fabric_cost_dtls c where b.job_no_mst=c.job_no and b.id in(".implode(",",$poIdArr).")");

	foreach($sql_fabric as $row_fabric){
		$fabric_info[$row_fabric["ID"]]=$row_fabric["CONSTRUCTION"]." ".$row_fabric["COMPOSITION"];
	}
	
	
	if($is_lc==1)
	{
		$lc_sc_data=sql_select("SELECT id, export_lc_no, lc_date, notifying_party, consignee, issuing_bank_name, negotiating_bank, lien_bank, pay_term, applicant_name,inco_term,lien_bank,nominated_shipp_line, buyer_name, tenor,shipping_mode from com_export_lc where id='".$lc_sc_id."' ");
		foreach($lc_sc_data as $row)
		{
			$lc_sc_id=$row[csf("id")];
			$lc_sc_no=$row[csf("export_lc_no")];
			$lc_sc_date=change_date_format($row[csf("lc_date")]);
			$notifying_party=$row[csf("notifying_party")];
			$consignee=$row[csf("consignee")];
			$issuing_bank_name=$row[csf("issuing_bank_name")];
			$negotiating_bank=$row[csf("lien_bank")];
			$pay_term_id=$row[csf("pay_term")];
			$applicant_name=$row[csf("applicant_name")];
			$buyer_name=$row[csf("buyer_name")];
			$inco_term=$row[csf("inco_term")];
			$lien_bank=$row[csf("lien_bank")];
			$shipping_line=$row[csf("nominated_shipp_line")];
			$negotiating_bank_text=$row[csf("negotiating_bank")];
			$tenor=$row[csf("tenor")];
			$shipping_mode=$row[csf("shipping_mode")];
		}
		
			$cate_hs_sql=sql_select("SELECT wo_po_break_down_id, fabric_description, category_no, hs_code from com_export_lc_order_info where com_export_lc_id='".$lc_sc_id."'");
			foreach($cate_hs_sql as $row)
			{
				$order_la_data[$row[csf("wo_po_break_down_id")]]["category_no"]=$row[csf("category_no")];
				$order_la_data[$row[csf("wo_po_break_down_id")]]["hs_code"]=$row[csf("hs_code")];
				// $order_la_data[$row[csf("wo_po_break_down_id")]]["fabric_description"]=$row[csf("fabric_description")];
			    $all_order_id[$row[csf("wo_po_break_down_id")]]=$row[csf("wo_po_break_down_id")];
			}
	}
	else
	{
		$lc_sc_data=sql_select("SELECT id, contract_no, contract_date, notifying_party, consignee, lien_bank, pay_term, applicant_name,inco_term,lien_bank,shipping_line,buyer_name, tenor,shipping_mode from com_sales_contract where id='".$lc_sc_id."'  and status_active=1");
		foreach($lc_sc_data as $row)
		{
			$lc_sc_id=$row[csf("id")];
			$lc_sc_no=$row[csf("contract_no")];
			$lc_sc_date=change_date_format($row[csf("contract_date")]);
			$notifying_party=$row[csf("notifying_party")];
			$consignee=$row[csf("consignee")];
			$issuing_bank_name="";
			$negotiating_bank=$row[csf("lien_bank")];
			$pay_term_id=$row[csf("pay_term")];
			$applicant_name=$row[csf("applicant_name")];
			$buyer_name=$row[csf("buyer_name")];
			$inco_term=$row[csf("inco_term")];
			$lien_bank=$row[csf("lien_bank")];
			$shipping_line=$row[csf("shipping_line")];
			$tenor=$row[csf("tenor")];
			$shipping_mode=$row[csf("shipping_mode")];
			$negotiating_bank_text="";
		}
		
		$cate_hs_sql=sql_select("SELECT wo_po_break_down_id, fabric_description, category_no, hs_code from com_sales_contract_order_info where com_sales_contract_id='".$lc_sc_id."' and status_active=1");
		foreach($cate_hs_sql as $row)
		{
			$order_la_data[$row[csf("wo_po_break_down_id")]]["category_no"]=$row[csf("category_no")];
			$order_la_data[$row[csf("wo_po_break_down_id")]]["hs_code"]=$row[csf("hs_code")];
			$order_la_data[$row[csf("wo_po_break_down_id")]]["fabric_description"]=$row[csf("fabric_description")];
			$all_order_id[$row[csf("wo_po_break_down_id")]]=$row[csf("wo_po_break_down_id")];
		}
	}
	
	$company_name_sql=sql_select( "SELECT id, company_name, plot_no, level_no, road_no, block_no, city, country_id,erc_no,email,contact_no,rex_no,rex_reg_date,irc_no,vat_number from lib_company where id ='$benificiary_id'");
	foreach($company_name_sql as $row)
	{
		$company_name=$row[csf("company_name")];
		$plot_no=$row[csf("plot_no")];
		$level_no=$row[csf("level_no")];
		$road_no=$row[csf("road_no")];
		$block_no=$row[csf("block_no")];
		$city=$row[csf("city")];
		$country_id=$row[csf("country_id")];
		$erc_no=$row[csf("erc_no")];
		$contact_no=$row[csf("contact_no")];
		$email=$row[csf("email")];
		$rex_no=$row[csf("rex_no")];
		$rex_reg_date=$row[csf("rex_reg_date")];
		$irc_no=$row[csf("irc_no")];
		$vat_number=$row[csf("vat_number")];
	}
	
	$country_name_arr=return_library_array( "SELECT id, country_name from lib_country",'id','country_name');
	// $carrier=$SupplierArr[$forwarder_name];
	$applicant=$buyer_name_arr[$applicant_name]["buyer_name"];
	$applicantAddress=$buyer_name_arr[$applicant_name]["address_1"];
	$agent=$buyer_name_arr[$agent_id]["buyer_name"];
	$agentAddress=$buyer_name_arr[$agent_id]["address_1"];
		
	$dtls_sql="SELECT a.id as dtls_id, a.po_breakdown_id, a.current_invoice_rate, a.current_invoice_qnty, a.current_invoice_value, b.po_number, c.style_ref_no, c.gmts_item_id, c.order_uom, c.gmts_item_id,c.STYLE_DESCRIPTION, c.BRAND_ID from  com_export_invoice_ship_dtls a,  wo_po_break_down b, wo_po_details_master c where a.po_breakdown_id=b.id and b.job_no_mst=c.job_no and a.current_invoice_qnty>0 and a.status_active=1 and a.is_deleted=0 and a.mst_id=$update_id";
	$result=sql_select($dtls_sql);
	$company_logo=return_field_value("image_location","common_photo_library","file_type=1 and form_name='company_details' and master_tble_id='$benificiary_id'","image_location");

	$data_terms=sql_select("SELECT id,terms,terms_prefix from wo_booking_terms_condition where booking_no=$update_id and entry_form=270 order by id");
	ob_start();
	?>
    <table width='1000' cellspacing="0" cellpadding="0" border="1" >
        <tr>
            <td colspan="12" valign="top" align="center"><b><u>COMMERCIAL INVOICE</u></b></td>
        </tr>
        <tr>
            <td colspan="6" valign="top"> 
                <strong><u>SUPPLIER:</u></strong>
                <br/>
                <?php echo $company_name;?>
                <?php
                if($city!="")  $comany_details.= "<br>".$city.", ";
                if($country_id!="")  $comany_details.="<br>".$country_name_arr[$country_id].".";
                if($contact_no!="")  $comany_details.="<br>Telephone: ".$contact_no.".";
                if($email!="")  $comany_details.="<br>E-MAIL: ".$email.".";
                echo  $comany_details;
                ?>
            </td>
            <td colspan="6" valign="top">
				Invoice No.: <?php echo $invoice_no;  ?><br/>
				Date: <? echo change_date_format($invoice_date);?><br/>
                EXP No. <?php echo $exp_form_no; ?><br/>
				DATE (exp issue): <? if($exp_form_date!="" && $exp_form_date!="0000-00-00" ) echo change_date_format($exp_form_date);?><br/>
                B/L No. <?php echo $bl_no; ?><br/>
				Date:<?  if($bl_date!="" && $bl_date!="0000-00-00") echo change_date_format($bl_date); ?>
            </td>
        </tr>
        <tr>
            <td colspan="6" valign="top"> 
                <strong><u>APPLICANT: </u></strong>
                <br/>
                <?
                    echo $applicant."<br/>";
                    echo $applicantAddress;
                ?>
            </td>
            <td colspan="6" valign="top">
				EXPORT Contract  NO: <?php echo $lc_sc_no;  ?><br/>
				Date: <? echo change_date_format($lc_sc_date);?><br/>
                REX No. <?php echo $rex_no; ?><br/>
				REX Registration date: <? if($rex_reg_date!=""){echo change_date_format($rex_reg_date);}?>
            </td>
        </tr>
        <tr>
			<td colspan="6" valign="top">
				<strong><u>NOTIFY PARTY:</u></strong><br/>
				<? if($buyer_name_arr[$notifying_party]["buyer_name"]!=''){echo $buyer_name_arr[$notifying_party]["buyer_name"]."<br/>";}
                if($buyer_name_arr[$notifying_party]["address_1"]!=''){echo $buyer_name_arr[$notifying_party]["address_1"];}
                ?>
			</td>
            <td colspan="6" valign="top">
                <strong><u>NEGOTIATING BANK: </u></strong><br/>
                <? echo $bank_name_arr[$lien_bank]["bank_name"]."<br>".$bank_name_arr[$lien_bank]["address"]; ?>
            </td>
        </tr>
        <tr>
            <td colspan="6" valign="top"> 
                <strong><u>CONSIGNEE: </u></strong><br/>
				<?
                if($buyer_name_arr[$consignee]["buyer_name"]!=''){echo $buyer_name_arr[$consignee]["buyer_name"]."<br/>";}
                if($buyer_name_arr[$consignee]["address_1"]!=''){echo $buyer_name_arr[$consignee]["address_1"]."<br/>";}
                ?>
            </td>
            <td colspan="6" valign="top">
                <?
                echo 'ACCOUNT NUMBER: '.$bank_acc_arr[$lien_bank][10]["account_no"].'<br> SWIFT: '.$bank_name_arr[$lien_bank]["swift_code"].'<br> ERC NO.: '.$erc_no.'<br> IRC NO.: '.$irc_no.'<br> VAT REG.NO.: '.$vat_number;
                ?>
            </td>
        </tr>
        <tr>
            <td align="center" colspan="2" >FDR VESSEL</td>
            <td align="center" colspan="2" >CONTAINER</td>
            <td align="center" >SEAL</td>
            <td align="center" >ETD</td>
            <td colspan="6">TERMS OF  PAYMENT : <?=$tenor;?></td>
        </tr>
        <tr>
            <td align="center" colspan="2" ><? echo $feeder_vessel;?></td>
            <td align="center" colspan="2" ><? echo $container_no;?></td>
            <td align="center" ><? echo $seal_no;?></td>
            <td align="center" ><?if($etd!=""){echo change_date_format($etd);}?></td>
            <td colspan="6">TERMS OF DELIVERY: <?=$incoterm[$inco_term];?></td>
        </tr>
            <td colspan="3" align="center">COUNTRY OF ORIGIN OF GOODS: BANGLADESH</td>
            <td colspan="3" align="center">COUNTRY OF FINAL DESTINATION: <?=$country_name_arr[$inv_country_id];?></td>
            <td colspan="3" align="center">PORT OF LOADING : <?=$port_of_loading;?></td>
            <td colspan="3" align="center">PORT OF DISCHARGE: <?=$port_of_discharge;?></td>
        </tr>        
        <tr style="font-size:small; font-weight:bold" align="center">
            <td rowspan="2">SHIPPED PER  BY: <?=$shipment_mode[$shipping_mode];?></td>
            <td colspan="8">DESCRIPTION OF GOODS</td>
            <td colspan="3"></td>
        </tr>
        <tr style="font-size:small; font-weight:bold" align="center">
			<td >STYLE NO</td>
            <td >ORDER NO</td>
            <td >MATERIAL</td>
            <td >ITEM DESCRIPTION</td>
            <td >H.S.CODE</td>
            <td >BRAND NAME</td>
            <td ></td>
            <td >CARTON QTY</td>
            <td >QUANTITY IN PCS</td>
            <td >UNIT PRICE IN US$</td>
            <td >TOTAL AMOUNT IN US$</td>
        </tr>
        
        <?
		$i=1;
		foreach($result as $row)
		{
			?>
            <tr style="font-size:small">
				<?if($i==1){?><td width="100" rowspan="<?=count($result);?>">CARTON SIZE:</td><?}?>
                <td width="100"><? echo $row[csf('style_ref_no')]; ?></td>
                <td width="100"><? echo $row[csf("po_number")]; ?></td>
                <td width="150"> <? echo $fabric_info[$row[csf('po_breakdown_id')]]; ?> </td>
                <td width="150"> <? echo $row[csf('STYLE_DESCRIPTION')]; ?> </td>
                <td width="80" align="center"><? echo $hs_code; //echo $order_la_data[$row[csf('po_breakdown_id')]]["hs_code"];?></td>
                <td width="80" align="center"><? echo $brand_arr[$row[csf('brand_id')]];?></td>
                <td width="50" ></td>
                <td width="50" align="right"><? echo number_format($carton_arr[$row[csf('po_breakdown_id')]],0,".",","); ?></td>
                <td width="60" align="right"><? echo number_format($row[csf('current_invoice_qnty')]*$setQtyArr[$row[csf('po_breakdown_id')]],0,".",",");  ?></td>
                <td width="50" align="right"><?  echo "$".number_format($row[csf("current_invoice_rate")],2); ?></td>
                <td align="right"><? echo "$".number_format($row[csf("current_invoice_value")],2,".",","); ?></td>
            </tr>
            <?
			$total_value+=$row[csf("current_invoice_value")];
			$total_qnty+=$row[csf("current_invoice_qnty")]*$setQtyArr[$row[csf('po_breakdown_id')]];
			$last_uom=$unit_of_measurement[$row[csf('order_uom')]];
			$total_po_carton_qnty+=$carton_arr[$row[csf('po_breakdown_id')]];
			$i++;
		}
		?>
        <tr>
            <td colspan="8" align="right">Total</td>
			<td  align="right"><? echo $total_po_carton_qnty; ?></td>
            <td  align="right"><? echo number_format($total_qnty,0,".",",")." Pcs" ?></td>
            <td >US$</td>
            <td align="right"> <? echo "$&nbsp;".number_format($total_value,2,".",","); ?></td>
        </tr>
        <tr>
            <td colspan="11">LESS BUYING COMMISSION IN FAVOUR OF BESTSELLER A/S @<?=$commission_percent;?>% </td>
			<td align="right"> <? if($commission){echo "$&nbsp;".number_format($commission,2,".",",");}?></td>
        </tr>
        <tr>
            <td colspan="12">CLAIM NO.</td>
        </tr>
        <tr>
            <td colspan="11">TOTAL INVOICE VALUE IN WORD <? echo number_to_words(def_number_format($net_invo_value,2,""),"USD", "CENTS");?></td>
			<td align="right"> <? echo "$&nbsp;".number_format($net_invo_value,2,".",","); ?></td>
        </tr>
		<?
		foreach($data_terms as $row)
		{
			?>
			    <tr>
					<td valign="top" colspan="3"><? echo $row[csf('terms_prefix')]; ?></td>
					<td colspan="9"><? echo $row[csf('terms')]; ?></td>
				</tr>
			<?
		}
		?>
        <tr>
            <td colspan="12" valign="top">
				TOTAL CARTON QTY&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<? echo number_format($total_carton_qnty,0,".",","); ?> CTNS<br/>
				TOTAL NET WEIGHT&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<? echo number_format($net_weight,2,".",","); ?> KG<br/>
				TOTAL GROSS WEIGHT&nbsp;<? echo number_format($gross_weight,2,".",","); ?> KG<br/>
				CBM&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<? echo number_format($cbm_qnty,2); ?> CBM
            </td>
        </tr>
    </table>
	<?
		$html = ob_get_contents();
		ob_clean();
		foreach (glob("tb*.xls") as $filename) {
		@unlink($filename);
		}
		//---------end------------//
		$name=time();
		$filename="tb".$user_id."_".$name.".xls";
		$create_new_doc = fopen($filename, 'w');
		$is_created = fwrite($create_new_doc, $html);
		echo "$filename####$html";
	exit();	
}

if ($action=="print_invoice3") // Print 3
{
    extract($_REQUEST);
	echo load_html_head_contents("Export Invoice - Print 3", "../../../", 1, 1);
	// $bank_arr=return_library_array("select id,bank_name from lib_bank ","id","bank_name");
	$company_name_sql=sql_select( "select id, company_name, company_short_name, contract_person, plot_no, level_no, road_no, block_no, city, country_id from lib_company");
	foreach($company_name_sql as $row)
	{
		$company_name_arr[$row["ID"]]["company_name"]=$row["COMPANY_NAME"];
		$company_name_arr[$row["ID"]]["company_short_name"]=$row["COMPANY_SHORT_NAME"];
		$company_name_arr[$row["ID"]]["contract_person"]=$row["CONTRACT_PERSON"];
		$company_name_arr[$row["ID"]]["plot_no"]=$row["PLOT_NO"];
		$company_name_arr[$row["ID"]]["level_no"]=$row["LEVEL_NO"];
		$company_name_arr[$row["ID"]]["road_no"]=$row["ROAD_NO"];
		$company_name_arr[$row["ID"]]["block_no"]=$row["BLOCK_NO"];
		$company_name_arr[$row["ID"]]["city"]=$row["CITY"];
		$company_name_arr[$row["ID"]]["country_id"]=$row["COUNTRY_ID"];
	}

	//var_dump($company_name_arr[1]);

	$applicant_sql=sql_select( "select a.id, a.buyer_name, a.short_name, a.address_1, b.party_type from lib_buyer a,  lib_buyer_party_type b where a.id=b.buyer_id and b.party_type in(22,23,4,5,6,100)");
	foreach($applicant_sql as $row)
	{
		$buyer_name_arr[$row["ID"]]["buyer_name"]=$row["BUYER_NAME"];
		$buyer_name_arr[$row["ID"]]["address_1"]=$row["ADDRESS_1"];
	}


	$inv_master_data=sql_select("SELECT id, benificiary_id, buyer_id, location_id, invoice_no, invoice_date, exp_form_no, exp_form_date, is_lc, lc_sc_id,  bl_no, feeder_vessel, inco_term, inco_term_place, shipping_mode, port_of_entry, port_of_loading, port_of_discharge, main_mark, side_mark, net_weight, gross_weight, cbm_qnty, discount_ammount, bonus_ammount, commission, total_carton_qnty, bl_date, carton_net_weight, carton_gross_weight, remarks, consignee, notifying_party, hs_code,SUPPLIED_GOODS_AMT,AD_PAYMENT_ADJUSTMENT_AMT from com_export_invoice_ship_mst where id=$data");
	if($inv_master_data[0]["IS_LC"]==1)
	{
		$lc_sc_data=sql_select("select id, export_lc_no, lc_date, notifying_party, consignee, issuing_bank_name, negotiating_bank, lien_bank, pay_term, applicant_name from com_export_lc where id='".$inv_master_data[0]["LC_SC_ID"]."' ");
		foreach($lc_sc_data as $row)
		{
			$lc_sc_id=$row["ID"];
			$lc_sc_no=$row["EXPORT_LC_NO"];
			$lc_sc_date=change_date_format($row["LC_DATE"]);
			$notifying_party=$row["NOTIFYING_PARTY"];
			$consignee=$row["CONSIGNEE"];//also notify party
			$issuing_bank_name=$row["ISSUING_BANK_NAME"];
			$negotiating_bank=$row["LIEN_BANK"];
			$pay_term_id=$row["PAY_TERM"];
			$applicant_name=$row["APPLICANT_NAME"];
		}

			$cate_hs_sql=sql_select("select wo_po_break_down_id, hs_code from com_export_lc_order_info where com_export_lc_id='".$inv_master_data[0]["LC_SC_ID"]."'");
			foreach($cate_hs_sql as $row)
			{
				// $order_la_data[$row["WO_PO_BREAK_DOWN_ID"]]["hs_code"]=$row["HS_CODE"];
				$all_order_id.=$row["WO_PO_BREAK_DOWN_ID"].", ";
			}
	}
	else
	{
		$lc_sc_data=sql_select("select id, contract_no, contract_date,issuing_bank, notifying_party, consignee, lien_bank, pay_term, applicant_name from com_sales_contract where id='".$inv_master_data[0]["LC_SC_ID"]."'  and status_active=1");
		foreach($lc_sc_data as $row)
		{
			$lc_sc_id=$row["ID"];
			$lc_sc_no=$row["CONTRACT_NO"];
			$lc_sc_date=change_date_format($row["CONTRACT_DATE"]);
			$notifying_party=$row["NOTIFYING_PARTY"];
			$consignee=$row["CONSIGNEE"];//also notify party
			$issuing_bank_name=$row["ISSUING_BANK"];
			$negotiating_bank=$row["LIEN_BANK"];
			$pay_term_id=$row["PAY_TERM"];
			$applicant_name=$row["APPLICANT_NAME"];
		}

		$cate_hs_sql=sql_select("select wo_po_break_down_id, hs_code from com_sales_contract_order_info where com_sales_contract_id='".$inv_master_data[0]["LC_SC_ID"]."' and status_active=1");
		$hs_code_all="";
		foreach($cate_hs_sql as $row)
		{
			// $order_la_data[$row["WO_PO_BREAK_DOWN_ID"]]["hs_code"]=$row["HS_CODE"];
			$all_order_id.=$row["WO_PO_BREAK_DOWN_ID"].", ";
		}
	}
	$all_order_id=chop($all_order_id, " , ");
	$iss_bank_nam=$iss_bank_address=$iss_bank_account=$iss_bank_swift_code="";
	if($issuing_bank_name)
	{
		$bank_data=sql_select("SELECT a.id, a.bank_name, a.address, a.swift_code,b.account_no from lib_bank a, lib_bank_account b where a.id=b.account_id and b.status_active=1 order by id desc");
		$iss_bank_nam=$bank_data[0]["BANK_NAME"];
		$iss_bank_address=$bank_data[0]["ADDRESS"];
		$iss_bank_account=$bank_data[0]["ACCOUNT_NO"];
		$iss_bank_swift_code=$bank_data[0]["SWIFT_CODE"];
	}
	
	$ex_ctn_arr=return_library_array( "select po_break_down_id, sum(total_carton_qnty) as total_carton_qnty from pro_ex_factory_mst where po_break_down_id in($all_order_id) and status_active=1 group by po_break_down_id",'po_break_down_id','total_carton_qnty');
	
	$bank_details=sql_select("select id,bank_name,address from lib_bank where id in($negotiating_bank)");
	
	?>
    <table id="" cellspacing="0" cellpadding="0" width="690" border="0">
        <tr>
            <td colspan="5" align="center" style="font-size:18px; font-weight:bold"><?=$company_name_arr[$inv_master_data[0]["BENIFICIARY_ID"]]["company_name"]; ?></td>
        </tr>
        <tr>
            <td colspan="5" align="center" style="font-size:14px; font-weight:bold">Commercial Invoice</td>
        </tr>
        <tr>
            <td colspan="5" align="center">&nbsp;</td>
        </tr>
    </table>
    <table id="" cellspacing="0" cellpadding="0" width="690" border="0"  style="font-size:11px;">
        <tr>
            <td width="90" align="right" valign="top">Shipper:&nbsp;</td>
            <td width="250" align="left" valign="top">
			<?
            $comany_details="";
            echo $company_name_arr[$inv_master_data[0]["BENIFICIARY_ID"]]["company_name"]."<br>";
            $plot_no=$company_name_arr[$inv_master_data[0]["BENIFICIARY_ID"]]["plot_no"];
            $level_no=$company_name_arr[$inv_master_data[0]["BENIFICIARY_ID"]]["level_no"];
            $road_no=$company_name_arr[$inv_master_data[0]["BENIFICIARY_ID"]]["road_no"];
            $block_no=$company_name_arr[$inv_master_data[0]["BENIFICIARY_ID"]]["block_no"];
            $city=$company_name_arr[$inv_master_data[0]["BENIFICIARY_ID"]]["city"];
            $country_id=$company_name_arr[$inv_master_data[0]["BENIFICIARY_ID"]]["country_id"];
            if($plot_no!="")  $comany_details= $plot_no.", ";
			if($level_no!="")  $comany_details.= $level_no.", ";
			if($road_no!="")  $comany_details.= $road_no.", ";
			if($block_no!="")  $comany_details.= $block_no.", ";
			if($city!="")  $comany_details.= "<br>".$city.", ";
			if($country_id!="")  $comany_details.=return_field_value( "country_name","lib_country","id='$country_id'")." . ";
			echo $comany_details;
            ?>
            </td>
            <td width="120" valign="top" align="right">
            Invoice No: &nbsp;<br>
            Lc/Sc No: &nbsp;<br>
            EXP NO: &nbsp;
            </td>
            <td width="140" valign="top" align="left">
			<?
				echo $inv_master_data[0]["INVOICE_NO"]."<br>";
				echo $lc_sc_no."<br>";
				echo $inv_master_data[0]["EXP_FORM_NO"];
			?>
            </td>
            <td valign="top" align="left">
            Date: <?=change_date_format($inv_master_data[0]["INVOICE_DATE"]);?><br>
            Date: <?=$lc_sc_date;?><br>
            Date: <? if($inv_master_data[0]["EXP_FORM_DATE"]!="" && $inv_master_data[0]["EXP_FORM_DATE"]!="0000-00-00" ) echo change_date_format($inv_master_data[0]["EXP_FORM_DATE"]);?><br>
           </td>
        </tr>
        <tr>
        	<td valign="top" align="right">Applicant:&nbsp;</td>
            <td valign="top"  align="left">
			<?=$buyer_name_arr[$applicant_name]["buyer_name"]."<br>".$buyer_name_arr[$applicant_name]["address_1"]."<br>"; ?>
            </td>
            <td valign="top" align="right">LC Issue Bank:&nbsp;</td>
            <td colspan="2" valign="top"  align="left"><?=$iss_bank_nam;?><br><?=$iss_bank_address;?><br><?=$iss_bank_account;?><br><?=$iss_bank_swift_code;?></td>
        </tr>
        <tr>
        	<td valign="top" align="right">Consignee:&nbsp;</td>
            <td valign="top" align="left" >
			<?
				$consignee_party_all="";
				if($inv_master_data[0]["CONSIGNEE"])
				{
					$consignee_party_all.=$buyer_name_arr[$inv_master_data[0]["CONSIGNEE"]]["buyer_name"]." <br>";
					if($buyer_name_arr[$inv_master_data[0]["CONSIGNEE"]]["address_1"]!="")
					{
						$consignee_party_all.=$buyer_name_arr[$inv_master_data[0]["CONSIGNEE"]]["address_1"]." <br>";
					}
				}
				else
				{
					$consignee_arr=explode(",",$consignee);
					foreach($consignee_arr as $buyer_con_id)
					{
						if($buyer_name_arr[$buyer_con_id]["buyer_name"]!="")
						{
							$consignee_party_all.=$buyer_name_arr[$buyer_con_id]["buyer_name"]." <br>";
							if($buyer_name_arr[$buyer_con_id]["address_1"]!="")
							{
								$consignee_party_all.=$buyer_name_arr[$buyer_con_id]["address_1"]." <br>";
							}
						}
					}
					$consignee_party_all=chop($consignee_party_all, " <br>");
				}
				echo $consignee_party_all;			
			?>
            </td>
			<td valign="top" align="right">Negotiating Bank:&nbsp;</td>
			<td valign="top" align="left" colspan="2"><?=$bank_details[0]["BANK_NAME"]." ".$bank_details[0]["ADDRESS"];?></td>

        </tr>
        <tr>
        	<td valign="top" align="right">Notify:&nbsp;</td>
            <td valign="top"  align="left" colspan="4">
			<?
				$notifying_party_all="";
				if($inv_master_data[0]["NOTIFYING_PARTY"])
				{
					$notifying_party_all.=$buyer_name_arr[$inv_master_data[0]["NOTIFYING_PARTY"]]["buyer_name"]." <br>";
					if($buyer_name_arr[$inv_master_data[0]["NOTIFYING_PARTY"]]["address_1"]!="")
					{
						$notifying_party_all.=$buyer_name_arr[$inv_master_data[0]["NOTIFYING_PARTY"]]["address_1"]." <br>";
					}
				}
				else
				{					
					$notifying_party_arr=explode(",",$notifying_party);
					foreach($notifying_party_arr as $buyer_id)
					{
						if($buyer_name_arr[$buyer_id]["buyer_name"]!="")
						{
							$notifying_party_all.=$buyer_name_arr[$buyer_id]["buyer_name"]." <br>";
							if($buyer_name_arr[$buyer_id]["address_1"]!="")
							{
								$notifying_party_all.=$buyer_name_arr[$buyer_id]["address_1"]." <br>";
							}
						}
					}
					$notifying_party_all=chop($notifying_party_all, " <br>");
				}
				echo $notifying_party_all;
			?>
            </td>
        </tr>
        <tr>
        	<td valign="top" align="right">Country Of Orgin:&nbsp; </td>
            <td valign="top"  align="left">Bangladesh</td>
            <td valign="top" align="right">Incoterm: &nbsp;</td>
            <td colspan="2" valign="top" align="left"><?=$incoterm[$inv_master_data[0]["INCO_TERM"]];?></td>
        </tr>
        <tr>
        	<td valign="top" align="right">HAWB/BL No:&nbsp;</td>
            <td valign="top"  align="left"><?=$inv_master_data[0]["BL_NO"];?></td>
            <td valign="top" align="right">HAWB/BL Date: &nbsp;</td>
            <td colspan="2" valign="top" align="left"><?if($inv_master_data[0]["BL_DATE"]!="" && $inv_master_data[0]["BL_DATE"]!="0000-00-00") echo change_date_format($inv_master_data[0]["BL_DATE"]);?></td>

        </tr>
        <tr>
        	<td valign="top" align="right">Port Of Loading:&nbsp;</td>
            <td valign="top"  align="left"><?=$inv_master_data[0]["PORT_OF_LOADING"];?></td>
            <td valign="top" align="right">Fedder Vessel:&nbsp;</td>
            <td valign="top" colspan="2"  align="left"><?=$inv_master_data[0]["FEEDER_VESSEL"];?></td>

        </tr>
        <tr>
        	<td valign="top" align="right">Port Of Discharg:&nbsp;</td>
            <td valign="top"  align="left"><?=$inv_master_data[0]["PORT_OF_DISCHARGE"];?></td>
        	<td valign="top" align="right">Mode Of Shipment:&nbsp;</td>
            <td valign="top" colspan="2" align="left"><?=$shipment_mode[$inv_master_data[0]["SHIPPING_MODE"]];?></td>
        </tr>
        <tr>
        	<td valign="top" align="right">Payment Terms:&nbsp;</td>
            <td valign="top"  align="left"><?=$pay_term[$pay_term_id];?></td>
            <td valign="top" align="right">Hs Code:&nbsp;</td>
            <td valign="top"  align="left"><?=$inv_master_data[0]["HS_CODE"];?></td>
        </tr>
        <tr>
            <td valign="top" align="right">Remarks: &nbsp;</td>
            <td valign="top" colspan="3" align="left"><?=$inv_master_data[0]["REMARKS"];?></td>
        </tr>
    </table>
    <br>
    <table id="" cellspacing="0" cellpadding="0" border="1" rules="all" width="690" class="rpt_table"  style="font-size:9px;" >
        <thead>
            <tr>
                <th width="100" rowspan="2">Shipping Mark</th>
                <th colspan="3">Description</th>
                <!-- <th width="70" rowspan="2">Hs Code</th> -->
                <th colspan="2">Qnty</th>
                <th width="50" rowspan="2">Ctns Qnty</th>
                <th width="40" rowspan="2">Unit Price</th>
                <th rowspan="2">Amount</th>
            </tr>
            <tr>
            	<th width="95">Po No</th>
                <th width="100">Style No.</th>
                <th width="120">Description</th>
                <th width="60">Qnty</th>
                <th width="50">UOM</th>
            </tr>
        </thead>
        <tbody>
        <?

		$dtls_sql="SELECT a.id as dtls_id, a.po_breakdown_id, a.current_invoice_rate, a.current_invoice_qnty, a.current_invoice_value, b.po_number, c.style_ref_no, c.gmts_item_id, c.order_uom from  com_export_invoice_ship_dtls a,  wo_po_break_down b, wo_po_details_master c where a.po_breakdown_id=b.id and b.job_no_mst=c.job_no and a.current_invoice_qnty>0 and a.status_active=1 and a.is_deleted=0 and a.mst_id=$data";


		//echo $dtls_sql; die;
		$result=sql_select($dtls_sql);
		$row_span=count($result)+2;
		if($inv_master_data[0]["DISCOUNT_AMMOUNT"]>0) $row_span=$row_span+1;
		if($inv_master_data[0]["BONUS_AMMOUNT"]>0)  $row_span=$row_span+1;
		if($inv_master_data[0]["COMMISSION"]>0)  $row_span=$row_span+1;
		if($inv_master_data[0]["SUPPLIED_GOODS_AMT"]>0)  $row_span=$row_span+1;
		if($inv_master_data[0]["AD_PAYMENT_ADJUSTMENT_AMT"]>0)  $row_span=$row_span+1;
		$i=1;
		$main_mark_arr=explode(",",$inv_master_data[0]["MAIN_MARK"]);
		$side_mark_arr=explode(",",$inv_master_data[0]["SIDE_MARK"]);
		foreach($result as $row)
		{
			?>
            <tr bgcolor="<?=$bgcolor; ?>" onClick="change_color('tr_<?=$i; ?>','<?=$bgcolor; ?>')" id="tr_<?=$i; ?>">
            	<?
				if($i==1)
				{
					?>
                	<td width="90" rowspan="<?=$row_span; ?>" valign="top"><p><span style="text-decoration:underline;">Main Mark</span><br>
					<?
					$all_main_mark="";
					foreach($main_mark_arr as $val)
					{
						$all_main_mark.=$val."<br>";
					}
					$all_main_mark=chop($all_main_mark, " <br> ");
					echo  $all_main_mark;
					?><br><span style="text-decoration:underline;">Side Mark</span><br>
					<?
					$all_side_mark="";
					foreach($side_mark_arr as $val)
					{
						$all_side_mark.=$val."<br>";
					}
					$all_side_mark=chop($all_side_mark, " <br> ");
					echo  $all_side_mark;
					?></p></td>
                	<?
                }
				?>
                <td ><p><?=$row["PO_NUMBER"]; ?>&nbsp;</p></td>
                <td ><p><?=$row['STYLE_REF_NO']; ?>&nbsp;</p></td>
                <td ><p><?=$garments_item[$row["GMTS_ITEM_ID"]]; ?>&nbsp;</p></td>
                <!-- <td align="center"><p><?=$order_la_data[$row['PO_BREAKDOWN_ID']]["hs_code"]; ?>&nbsp;</p></td> -->
                <td align="right"><?=number_format($row['CURRENT_INVOICE_QNTY'],2); ?></td>
                <td style="padding-left:3px;"><p><?=$unit_of_measurement[$row['ORDER_UOM']]; ?></p></td>
                <td align="right" ><?=$ex_ctn_arr[$row['PO_BREAKDOWN_ID']]; ?></td>
                <td align="right" ><?=number_format($row["CURRENT_INVOICE_RATE"],2); ?></td>
                <td align="right"><?=number_format($row["CURRENT_INVOICE_VALUE"],2); ?></td>
            </tr>
            <?
			$total_value+=$row["CURRENT_INVOICE_VALUE"];
			$total_qnty+=$row["CURRENT_INVOICE_QNTY"];
			$last_uom=$unit_of_measurement[$row['ORDER_UOM']];
			$total_carton_qnty+=$ex_ctn_arr[$row['PO_BREAKDOWN_ID']];
			$i++;
		}
		?>
        	<tr bgcolor="#FFFFCC">
                <td ><p>&nbsp;</p></td>
                <td ><p>&nbsp;</p></td>
                <td ><p>&nbsp;</p></td>
                <!-- <td><p>&nbsp;</p></td> -->
                <td><p>&nbsp;</p></td>
                <td align="right"><p>&nbsp;</p></td>
                <td align="right" colspan="2"><b>Total Value</b></td>
                <td align="right"><b><?=number_format($total_value,2); ?></b></td>
            </tr>
            <?
			if($inv_master_data[0]["DISCOUNT_AMMOUNT"]>0)
			{
				?>
                <tr>
                    <td ><p>&nbsp;</p></td>
                    <td ><p>&nbsp;</p></td>
                    <td ><p>&nbsp;</p></td>
                    <!-- <td><p>&nbsp;</p></td> -->
                    <td ><p>&nbsp;</p></td>
                    <td align="right" ><p>&nbsp;</p></td>
                    <td align="right" colspan="2"><b>Total Discount</b></td>
                    <td align="right"><b><?=number_format($inv_master_data[0]["DISCOUNT_AMMOUNT"],2); ?></b></td>
                </tr>
                <?
				$total_value=$total_value-$inv_master_data[0]["DISCOUNT_AMMOUNT"];
			}

			if($inv_master_data[0]["BONUS_AMMOUNT"]>0||$inv_master_data[0]["SUPPLIED_GOODS_AMT"]>0||$inv_master_data[0]["AD_PAYMENT_ADJUSTMENT_AMT"]>0)
			{
				?>
                <tr>
                    <td ><p>&nbsp;</p></td>
                    <td ><p>&nbsp;</p></td>
                    <td ><p>&nbsp;</p></td>
                    <!-- <td ><p>&nbsp;</p></td> -->
                    <td ><p>&nbsp;</p></td>
                    <td align="right" ><p>&nbsp;</p></td>
                    <td align="right"  colspan="2"><b>Total Bonus</b></td>
                    <td align="right"><b><?=number_format($inv_master_data[0]["BONUS_AMMOUNT"]+$inv_master_data[0]["AD_PAYMENT_ADJUSTMENT_AMT"]+$inv_master_data[0]["SUPPLIED_GOODS_AMT"],2); ?></b></td>
                </tr>
                <?
				$total_value=$total_value-($inv_master_data[0]["BONUS_AMMOUNT"]+$inv_master_data[0]["AD_PAYMENT_ADJUSTMENT_AMT"]+$inv_master_data[0]["SUPPLIED_GOODS_AMT"]);
			}
			if($inv_master_data[0]["COMMISSION"]>0)
			{
				?>
                <tr>
                    <td ><p>&nbsp;</p></td>
                    <td ><p>&nbsp;</p></td>
                    <td ><p>&nbsp;</p></td>
                    <!-- <td ><p>&nbsp;</p></td> -->
                    <td ><p>&nbsp;</p></td>
                    <td align="right" ><p>&nbsp;</p></td>
                    <td align="right"  colspan="2"><b>Total Commission</b></td>
                    <td align="right"><b><?=number_format($inv_master_data[0]["COMMISSION"],2); ?></b></td>
                </tr>
                <?
				$total_value=$total_value-$inv_master_data[0]["COMMISSION"];
			}
			?>
            <tr bgcolor="#CCCCCC">
                <td ><p>&nbsp;</p></td>
                <td ><p>&nbsp;</p></td>
                <td ><p>&nbsp;</p></td>
                <!-- <td ><p>&nbsp;</p></td> -->
                <td ><p>&nbsp;</p></td>
                <td align="right" ><p>&nbsp;</p></td>
                <td align="right"  colspan="2"><b>Net Total</b></td>
                <td align="right"><b><?=number_format($total_value,2); ?></b></td>
            </tr>
        </tbody>
    </table>
    <br>
    <table id="" cellspacing="0" cellpadding="0" border="1" rules="all" width="400" class="rpt_table"  style="font-size:10px;" >
    	<tr>
        	<td width="130" align="right">Total Ctns:&nbsp;</td>
            <td width="200" align="right"><?=number_format($total_carton_qnty,0); ?></td>
            <td>Ctns</td>
        </tr>
        <tr>
        	<td  align="right">Total Quantity:&nbsp;</td>
            <td align="right"><?=$total_qnty; ?></td>
            <td><?=$last_uom; ?></td>
        </tr>
        <tr>
        	<td align="right">Total Net Wt:&nbsp;</td>
            <td align="right"><?=number_format($inv_master_data[0]["CARTON_NET_WEIGHT"],2); ?></td>
            <td>KG</td>
        </tr>
        <tr>
        	<td align="right">Total Gross Wt:&nbsp;</td>
            <td align="right"><?=number_format($inv_master_data[0]["CARTON_GROSS_WEIGHT"],2); ?></td>
            <td>KG</td>
        </tr>
        <tr>
        	<td align="right">Total CBM:&nbsp;</td>
            <td align="right"><?=number_format($inv_master_data[0]["CBM_QNTY"],2); ?></td>
            <td>CBM</td>
        </tr>
    </table>

	<?
	exit();
}

if($action=="invoice_report_print_4")   //print 4
{
	extract($_REQUEST);
	// $update_id=$data;

	$brand_arr=return_library_array( "SELECT ID, BRAND_NAME FROM LIB_BUYER_BRAND BRAND",'ID','BRAND_NAME');
	$applicant_sql=sql_select( "SELECT A.ID, A.BUYER_NAME, A.SHORT_NAME, A.ADDRESS_1 FROM LIB_BUYER A");
	foreach($applicant_sql as $row)
	{
		$buyer_name_arr[$row["ID"]]["BUYER_NAME"]=$row["BUYER_NAME"];
		$buyer_name_arr[$row["ID"]]["ADDRESS_1"]=$row["ADDRESS_1"];
	}
	$bank_sql=sql_select( "SELECT A.ID, A.BANK_NAME, A.BRANCH_NAME, A.ADDRESS, A.SWIFT_CODE FROM LIB_BANK A");
	foreach($bank_sql as $row)
	{
		$bank_name_arr[$row["ID"]]["BANK_NAME"]=$row["BANK_NAME"];
		$bank_name_arr[$row["ID"]]["BRANCH_NAME"]=$row["BRANCH_NAME"];
		$bank_name_arr[$row["ID"]]["ADDRESS"]=$row["ADDRESS"];
		$bank_name_arr[$row["ID"]]["SWIFT_CODE"]=$row["SWIFT_CODE"];
	}
	$bank_account_sql=sql_select( "SELECT ID, ACCOUNT_ID, ACCOUNT_TYPE, ACCOUNT_NO FROM LIB_BANK_ACCOUNT WHERE IS_DELETED=0 ");
	foreach($bank_account_sql as $row)
	{
		$bank_acc_arr[$row["ACCOUNT_ID"]][$row["ACCOUNT_TYPE"]]["ACCOUNT_NO"]=$row["account_no"];
	}
	$inv_master_data=sql_select("SELECT ID, BENIFICIARY_ID, BUYER_ID, LOCATION_ID, INVOICE_NO, INVOICE_DATE, EXP_FORM_NO, EXP_FORM_DATE, IS_LC, LC_SC_ID, BL_NO, FEEDER_VESSEL, INCO_TERM, INCO_TERM_PLACE, SHIPPING_MODE, PORT_OF_ENTRY, PORT_OF_LOADING, PORT_OF_DISCHARGE, MAIN_MARK, SIDE_MARK, CARTON_NET_WEIGHT, CARTON_GROSS_WEIGHT, CBM_QNTY, PLACE_OF_DELIVERY, DELV_NO, CONSIGNEE, NOTIFYING_PARTY, ITEM_DESCRIPTION, DISCOUNT_AMMOUNT, BONUS_AMMOUNT, COMMISSION, TOTAL_CARTON_QNTY, BL_DATE, HS_CODE, MOTHER_VESSEL, CATEGORY_NO, FORWARDER_NAME, ETD,CO_NO, TOTAL_MEASURMENT, INVOICE_VALUE, NET_INVO_VALUE, CONTAINER_NO, SEAL_NO, ETD, COUNTRY_ID,COMMISSION_PERCENT,BL_REV_DATE,NGC_ID,BOOKING_NO,SHIPMENT_TO,SHIPMENT_TERMS,UD_NO FROM COM_EXPORT_INVOICE_SHIP_MST WHERE ID=$update_id");
	$id=$inv_master_data[0]["ID"];
	$benificiary_id=$inv_master_data[0]["BENIFICIARY_ID"];
	$ngc_id=$inv_master_data[0]["NGC_ID"];
	$booking_no=$inv_master_data[0]["BOOKING_NO"];
	$shipment_to_all=$inv_master_data[0]["SHIPMENT_TO"];
	$shipment_terms=$inv_master_data[0]["SHIPMENT_TERMS"];
	$ud_no=$inv_master_data[0]["UD_NO"];
	$buyer_id=$inv_master_data[0]["BUYER_ID"];
	$location_id=$inv_master_data[0]["LOCATION_ID"];
	$invoice_no=$inv_master_data[0]["INVOICE_NO"];
	$invoice_date=$inv_master_data[0]["INVOICE_DATE"];
	$bl_rev_date=$inv_master_data[0]["BL_REV_DATE"];
	$exp_form_no=$inv_master_data[0]["EXP_FORM_NO"];
	$exp_form_date=$inv_master_data[0]["EXP_FORM_DATE"];
	$is_lc=$inv_master_data[0]["IS_LC"];
	$lc_sc_id=$inv_master_data[0]["LC_SC_ID"];
	$bl_no=$inv_master_data[0]["BL_NO"];
	$feeder_vessel=$inv_master_data[0]["FEEDER_VESSEL"];
	$inco_term=$inv_master_data[0]["INCO_TERM"];
	$inco_term_place=$inv_master_data[0]["INCO_TERM_PLACE"];
	$shipping_mode=$inv_master_data[0]["SHIPPING_MODE"];
	$port_of_entry=$inv_master_data[0]["PORT_OF_ENTRY"];
	$port_of_loading=$inv_master_data[0]["PORT_OF_LOADING"];
	$port_of_discharge=$inv_master_data[0]["PORT_OF_DISCHARGE"];
	$main_mark=$inv_master_data[0]["MAIN_MARK"];
	$side_mark=$inv_master_data[0]["SIDE_MARK"];
	$net_weight=$inv_master_data[0]["CARTON_NET_WEIGHT"];
	$gross_weight=$inv_master_data[0]["CARTON_GROSS_WEIGHT"];
	$cbm_qnty=$inv_master_data[0]["CBM_QNTY"];
	$place_of_delivery=$inv_master_data[0]["PLACE_OF_DELIVERY"];
	$delv_no=$inv_master_data[0]["DELV_NO"];
	$consignee=$inv_master_data[0]["CONSIGNEE"];
	$notifying_party=$inv_master_data[0]["NOTIFYING_PARTY"];
	$item_description=$inv_master_data[0]["ITEM_DESCRIPTION"];
	$discount_ammount=$inv_master_data[0]["DISCOUNT_AMMOUNT"];
	$bonus_ammount=$inv_master_data[0]["BONUS_AMMOUNT"];
	$commission=$inv_master_data[0]["COMMISSION"];
	$commission_percent=$inv_master_data[0]["COMMISSION_PERCENT"];
	$total_carton_qnty=$inv_master_data[0]["TOTAL_CARTON_QNTY"];
	$bl_date=$inv_master_data[0]["BL_DATE"];
	$hs_code=$inv_master_data[0]["HS_CODE"];
	$mother_vessel=$inv_master_data[0]["MOTHER_VESSEL"];
	$category_no=$inv_master_data[0]["CATEGORY_NO"];
	$forwarder_name=$inv_master_data[0]["FORWARDER_NAME"];
	$etd=$inv_master_data[0]["ETD"];
	$co_no=$inv_master_data[0]["CO_NO"];
	$total_measurment=$inv_master_data[0]["TOTAL_MEASURMENT"];
	$net_invo_value=$inv_master_data[0]["NET_INVO_VALUE"];
	$container_no=$inv_master_data[0]["CONTAINER_NO"];
	$seal_no=$inv_master_data[0]["SEAL_NO"];
	$etd=$inv_master_data[0]["ETD"];
	$inv_country_id=$inv_master_data[0]["COUNTRY_ID"];
	$total_discount=$inv_master_data[0]["INVOICE_VALUE"]-$inv_master_data[0]["NET_INVO_VALUE"];
	//echo $shipment_to_all;
	//$ship = explode(',',$shipment_to_all);
	//print_r($ship);
	$itemIdArr=array();
	$setQtyArr=array();
	$poIdArr=array();
	$dtls_sql="SELECT A.ID AS DTLS_ID, A.PO_BREAKDOWN_ID,C.TOTAL_SET_QNTY FROM  COM_EXPORT_INVOICE_SHIP_DTLS A,  WO_PO_BREAK_DOWN B, WO_PO_DETAILS_MASTER C WHERE A.PO_BREAKDOWN_ID=B.ID AND B.JOB_NO_MST=C.JOB_NO AND A.CURRENT_INVOICE_QNTY>0 AND A.STATUS_ACTIVE=1 AND A.IS_DELETED=0 AND A.MST_ID=$update_id";
	$PO_agent=sql_select($dtls_sql);
	foreach($PO_agent as $row){
		$poIdArr[]=$row['PO_BREAKDOWN_ID'];
		$setQtyArr[$row['PO_BREAKDOWN_ID']]=$row['TOTAL_SET_QNTY'];
	}
	
	$carton_arr=array();
	$sqlCarton=sql_select("SELECT A.ID,A.SYS_NUMBER, A.DL_NO, B.DELIVERY_MST_ID,B.PO_BREAK_DOWN_ID,B.TOTAL_CARTON_QNTY,B.CARTON_QNTY FROM PRO_EX_FACTORY_DELIVERY_MST A,PRO_EX_FACTORY_MST B WHERE A.ID=B.DELIVERY_MST_ID AND B.PO_BREAK_DOWN_ID IN(".implode(",",$poIdArr).")");


	foreach($sqlCarton as $rowCarton)
	{
		$carton_arr[$rowCarton['PO_BREAK_DOWN_ID']]=$rowCarton['TOTAL_CARTON_QNTY'];
	}
	$agent_id="";
	// $fristPo=array_shift($poIdArr);
	$sql_fabric=sql_select("SELECT B.ID, C.CONSTRUCTION, C.COMPOSITION FROM WO_PO_BREAK_DOWN B, WO_PRE_COST_FABRIC_COST_DTLS C WHERE B.JOB_NO_MST=C.JOB_NO AND B.ID IN(".implode(",",$poIdArr).")");

	foreach($sql_fabric as $row_fabric){
		$fabric_info[$row_fabric["ID"]]=$row_fabric["CONSTRUCTION"]." ".$row_fabric["COMPOSITION"];
	}
	
	
	if($is_lc==1)
	{
		$lc_sc_data=sql_select("SELECT ID, EXPORT_LC_NO, LC_DATE, NOTIFYING_PARTY, CONSIGNEE, ISSUING_BANK_NAME, NEGOTIATING_BANK, LIEN_BANK, PAY_TERM, APPLICANT_NAME,INCO_TERM,LIEN_BANK,NOMINATED_SHIPP_LINE, BUYER_NAME, TENOR,SHIPPING_MODE FROM COM_EXPORT_LC WHERE ID='".$lc_sc_id."' ");
		foreach($lc_sc_data as $row)
		{
			$lc_sc_id=$row["ID"];
			$lc_sc_no=$row["EXPORT_LC_NO"];
			$lc_sc_date=change_date_format($row["LC_DATE"]);
			$notifying_party=$row["NOTIFYING_PARTY"];
			$consignee=$row["CONSIGNEE"];
			$issuing_bank_name=$row["ISSUING_BANK_NAME"];
			$negotiating_bank=$row["LIEN_BANK"];
			$pay_term_id=$row["PAY_TERM"];
			$applicant_name=$row["APPLICANT_NAME"];
			$buyer_name=$row["BUYER_NAME"];
			$inco_term=$row["INCO_TERM"];
			$lien_bank=$row["LIEN_BANK"];
			$shipping_line=$row["NOMINATED_SHIPP_LINE"];
			$negotiating_bank_text=$row["NEGOTIATING_BANK"];
			$tenor=$row["TENOR"];
			$shipping_mode=$row["SHIPPING_MODE"];
		}
		
			$cate_hs_sql=sql_select("SELECT WO_PO_BREAK_DOWN_ID, FABRIC_DESCRIPTION, CATEGORY_NO, HS_CODE FROM COM_EXPORT_LC_ORDER_INFO WHERE COM_EXPORT_LC_ID='".$lc_sc_id."'");
			foreach($cate_hs_sql as $row)
			{
				$order_la_data[$row["WO_PO_BREAK_DOWN_ID"]]["CATEGORY_NO"]=$row["CATEGORY_NO"];
				$order_la_data[$row["WO_PO_BREAK_DOWN_ID"]]["HS_CODE"]=$row["HS_CODE"];
			    $all_order_id[$row["WO_PO_BREAK_DOWN_ID"]]=$row["WO_PO_BREAK_DOWN_ID"];
			}
	}
	else
	{
		$lc_sc_data=sql_select("SELECT ID, CONTRACT_NO, CONTRACT_DATE, NOTIFYING_PARTY, CONSIGNEE, LIEN_BANK, PAY_TERM, APPLICANT_NAME,INCO_TERM,LIEN_BANK,SHIPPING_LINE,BUYER_NAME, TENOR,SHIPPING_MODE FROM COM_SALES_CONTRACT WHERE ID='".$lc_sc_id."'  AND STATUS_ACTIVE=1");
		foreach($lc_sc_data as $row)
		{
			$lc_sc_id=$row["ID"];
			$lc_sc_no=$row["CONTRACT_NO"];
			$lc_sc_date=change_date_format($row["CONTRACT_DATE"]);
			$notifying_party=$row["NOTIFYING_PARTY"];
			$consignee=$row["CONSIGNEE"];
			$issuing_bank_name="";
			$negotiating_bank=$row["LIEN_BANK"];
			$pay_term_id=$row["PAY_TERM"];
			$applicant_name=$row["APPLICANT_NAME"];
			$buyer_name=$row["BUYER_NAME"];
			$inco_term=$row["INCO_TERM"];
			$lien_bank=$row["LIEN_BANK"];
			$shipping_line=$row["SHIPPING_LINE"];
			$tenor=$row["TENOR"];
			$shipping_mode=$row["SHIPPING_MODE"];
			$negotiating_bank_text="";
		}
		
		$cate_hs_sql=sql_select("SELECT WO_PO_BREAK_DOWN_ID, FABRIC_DESCRIPTION, CATEGORY_NO, HS_CODE FROM COM_SALES_CONTRACT_ORDER_INFO WHERE COM_SALES_CONTRACT_ID='".$lc_sc_id."' AND STATUS_ACTIVE=1");
		foreach($cate_hs_sql as $row)
		{
			$order_la_data[$row["WO_PO_BREAK_DOWN_ID"]]["CATEGORY_NO"]=$row["CATEGORY_NO"];
			$order_la_data[$row["WO_PO_BREAK_DOWN_ID"]]["HS_CODE"]=$row["HS_CODE"];
			$order_la_data[$row["WO_PO_BREAK_DOWN_ID"]]["FABRIC_DESCRIPTION"]=$row["FABRIC_DESCRIPTION"];
			$all_order_id[$row["WO_PO_BREAK_DOWN_ID"]]=$row["WO_PO_BREAK_DOWN_ID"];
		}
	}
	
	$company_name_sql=sql_select( "SELECT ID, COMPANY_NAME, PLOT_NO, LEVEL_NO, ROAD_NO, BLOCK_NO, CITY, COUNTRY_ID,ERC_NO,EMAIL,CONTACT_NO,REX_NO,REX_REG_DATE,IRC_NO,VAT_NUMBER FROM LIB_COMPANY WHERE ID ='$benificiary_id'");
	foreach($company_name_sql as $row)
	{
		$company_name=$row["COMPANY_NAME"];
		$plot_no=$row["PLOT_NO"];
		$level_no=$row["LEVEL_NO"];
		$road_no=$row["ROAD_NO"];
		$block_no=$row["BLOCK_NO"];
		$city=$row["CITY"];
		$country_id=$row["COUNTRY_ID"];
		$erc_no=$row["ERC_NO"];
		$contact_no=$row["CONTACT_NO"];
		$email=$row["EMAIL"];
		$rex_no=$row["REX_NO"];
		$rex_reg_date=$row["REX_REG_DATE"];
		$irc_no=$row["IRC_NO"];
		$vat_number=$row["VAT_NUMBER"];
	}
	
	$country_name_arr=return_library_array( "SELECT ID, COUNTRY_NAME FROM LIB_COUNTRY",'ID','COUNTRY_NAME');
	// $carrier=$SupplierArr[$forwarder_name];
	$applicant=$buyer_name_arr[$applicant_name]["BUYER_NAME"];
	$buyer=$buyer_name_arr[$buyer_name]["BUYER_NAME"];
	$applicantAddress=$buyer_name_arr[$applicant_name]["ADDRESS_1"];
	$agent=$buyer_name_arr[$agent_id]["BUYER_NAME"];
	$agentAddress=$buyer_name_arr[$agent_id]["ADDRESS_1"];
		
	$dtls_sql="SELECT A.ID AS DTLS_ID, A.PO_BREAKDOWN_ID, A.CURRENT_INVOICE_RATE, A.CURRENT_INVOICE_QNTY, A.CURRENT_INVOICE_VALUE,A.CARTON_QTY, B.PO_NUMBER, C.STYLE_REF_NO, C.GMTS_ITEM_ID, C.ORDER_UOM, C.GMTS_ITEM_ID,C.STYLE_DESCRIPTION, C.BRAND_ID FROM  COM_EXPORT_INVOICE_SHIP_DTLS A,  WO_PO_BREAK_DOWN B, WO_PO_DETAILS_MASTER C WHERE A.PO_BREAKDOWN_ID=B.ID AND B.JOB_NO_MST=C.JOB_NO AND A.CURRENT_INVOICE_QNTY>0 AND A.STATUS_ACTIVE=1 AND A.IS_DELETED=0 AND A.MST_ID=$update_id";
	$result=sql_select($dtls_sql);

	$sql="SELECT B.ID AS ID, B.LOCATION_NAME || ' (' || A.COMPANY_SHORT_NAME || ')' AS LOCATION_NAME  FROM LIB_COMPANY A, LIB_LOCATION B WHERE A.ID=B.COMPANY_ID AND B.STATUS_ACTIVE =1 AND B.IS_DELETED=0 AND A.STATUS_ACTIVE =1 AND A.IS_DELETED=0 ORDER BY B.LOCATION_NAME";

	$location_dtls_sql = "SELECT A.COMPANY_NAME , B.LOCATION_NAME , B.ADDRESS , B.EMAIL,B.COUNTRY_ID FROM  LIB_COMPANY A ,LIB_LOCATION B WHERE A.ID=B.COMPANY_ID AND B.STATUS_ACTIVE =1 AND B.IS_DELETED=0 AND A.STATUS_ACTIVE =1 AND A.IS_DELETED=0  AND B.ID = $location_id";
	//echo $location_dtls_sql;
	$location_dtls = sql_select($location_dtls_sql);
	foreach($location_dtls as $row)
	{
		$ex_company_name=$row['COMPANY_NAME'];
		$ex_location_name=$row['LOCATION_NAME'];
		$ex_address=$row['ADDRESS'];
		$ex_country_id=$row[csf("COUNTRY_ID")];
		$ex_email=$row["EMAIL"];
	}

	$company_logo=return_field_value("image_location","common_photo_library","file_type=1 and form_name='company_details' and master_tble_id='$benificiary_id'","image_location");

	$data_terms=sql_select("SELECT ID,TERMS,TERMS_PREFIX FROM WO_BOOKING_TERMS_CONDITION WHERE BOOKING_NO=$update_id AND ENTRY_FORM=270 ORDER BY ID");
	ob_start();
	?>
    <table width='1040' cellspacing="0" cellpadding="0" border="1" >
        <tr>
            <td colspan="12" valign="top" align="center" style="font-size:Large; font-weight:bold"><b>COMMERCIAL INVOICE</b></td>
        </tr>
		<tr>
            <td colspan="12" valign="top" align="center" style="font-size:small; font-weight:bold" align="center"><b><? echo $buyer ;?></b></td>
        </tr>
		<tr>
            <td colspan="6" valign="top" width='500'  align="center" style="font-size:15px; font-weight:bold" align="center"><b>NGC ID # <?echo $ngc_id;?></b></td>  
			<td colspan="6" width="500"  valign="top" align="center" style="font-size:15px; font-weight:bold" align="center"><b>Booking No: <? echo $booking_no;?> </b></td>
        </tr>
		
        <tr>
            <td colspan="6" valign="top"> 
				<table style="border-collapse: collapse; border:none;" border="1">
					<tr>
						<td valign="top" width='20' style="border-left:none;">1</td>
						<td style="border-bottom:none; border-right:none;" width='500'>
							<strong>Seller:</strong>
							<br/>
							<?php echo $company_name;?>
							<?php
							if($city!="")  $comany_details.= "<br>".$city.", ";
							if($country_id!="")  $comany_details.="<br>".$country_name_arr[$country_id].".";
							if($contact_no!="")  $comany_details.="<br>Telephone: ".$contact_no.".";
							if($email!="")  $comany_details.="<br>E-MAIL: ".$email.".";
							echo  $comany_details;
							?>
						</td>
					</tr>
				</table>
            </td>

            <td colspan="6" valign="top">
				<table style="border-collapse: collapse; border:none;" border="1">
					<tr>
						<td valign="top" width='20'>4</td>
						<td width='250'><b>Invoice No.:</b> <br><?php echo $invoice_no;  ?></td>
						<td width='250' style="border-right: none;"><b>Date:</b> <br> <? echo change_date_format($invoice_date);?><br/></td>
					</tr>
					<tr>
						<td valign="top" width='20'>5</td>
						<td width='250'><b>Export Contract No :</b> <br> <? echo $lc_sc_no;//echo $exp_form_no; ?></td>
						<td width='250' style="border-right: none;"><b>Date:</b><br><?
						 if($lc_sc_date!="" && $lc_sc_date!="0000-00-00" ) echo change_date_format($lc_sc_date);
						 ?><br/></td>
					</tr>
					<tr>
						<td style="border-bottom:none;" valign="top" width='20'>6</td>
						<td style="border-bottom:none;" width='250'><b>Exp From No.:</b>  <br><?php echo $exp_form_no;  ?></td>
						<td style="border-bottom:none; border-right: none;" width='250'><b>Date:</b> <br> <? if($exp_form_date!="" && $exp_form_date!="0000-00-00" ) echo change_date_format($exp_form_date);?><br/></td>
					</tr>
				</table>	
            </td>
        </tr>
		
        <tr>
			<td colspan="6" valign="top"> 
				<table style="border-collapse: collapse; border:none;" border="1">
					<tr>
						<td valign="top" width='20' style="border-left:none;">2</td>
						<td style="border-bottom:none; border-right:none;" width='500'>
							<strong>Factory & Address:</strong>
							<?
							if($ex_company_name!="")  $ex_fac_details.= "<br>".$ex_company_name;
							if($ex_address!="")  $ex_fac_details.= "<br>".$ex_address.", ";
							if($ex_country_id!="")  $ex_fac_details.="<br>".$country_name_arr[$ex_country_id].".";
							if($ex_email!="")  $ex_fac_details.="<br>E-MAIL: ".$ex_email.".";
							echo  $ex_fac_details;
							?>
						</td>
					</tr>
				</table>
            </td>

			<td colspan="6" valign="top">
				<table style="border-collapse: collapse; border:none;" border="1">
					<tr>
						<td valign="top" width='20'>7</td>
						<td width='500'  style="border-right: none;"><b>Payment Terms: <?php echo $pay_term[$pay_term_id];  ?> &nbsp;&nbsp;&nbsp;
						Payment <?echo $tenor; ?> Days</b>
					</td>
					</tr>
					<tr>
						<td valign="top" width='20'></td>
						<td style="border-bottom:none; border-right: none;" width='500'><b>Bank Details for TT Payment :</b> <br> 
						<? 
							echo $bank_name_arr[$lien_bank]["BANK_NAME"]."<br>".$bank_name_arr[$lien_bank]["ADDRESS"];
						?>
						</td>
					</tr>
				</table>	
            </td>

        </tr>

	
		<tr>
			<td colspan="6" valign="top"> 
				<table style="border-collapse: collapse;  border:none;" border="1">
					<tr>
						<td valign="top" width='20' style="border-left:none;">3</td>
						<td style="border-bottom:none; border-right:none;" width='500'>
							<strong>Ship To:</strong> <br>
							<?// echo $shipment_to_all; 
							$explode_shp = explode(',',$shipment_to_all);
							foreach($explode_shp as $row)
							{
								echo $row."<br>";
							}
							?> 
						</td>
					</tr>
				</table>
            </td>
			
			<td colspan="6" valign="top">
				<table style="border-collapse: collapse; border:none;" border="1">
				<tr>
						<td valign="top" width='20'>8</td>
						<td style="border-bottom:none; border-right: none;" width='500'>
							<strong>Sold To:</strong> <br>
							<?
							echo $buyer_name_arr[$buyer_name]["BUYER_NAME"]."<br>".
							$buyer_name_arr[$buyer_name]["ADDRESS_1"];
							?>
						</td>
					</tr>
				</table>	
            </td>
        </tr>

		<tr>
			<td colspan="6" valign="top"> 
				<table style="border-collapse: collapse;  border:none;" border="1">
					<tr>
						<td valign="top" width='20' style="border-left:none;">9</td>
						<td width='250'><b>VAT:</b></td>
						<td width='250' style="border-right:none;"> <? echo $vat_number;?><br/></td>
					</tr>
					<tr>
						<td valign="top" width='20' style="border-left:none;"></td>
						<td width='250'><b>Forwarder:</b></td>
						<td width='250' style="border-right:none;"> <? echo "NF";?><br/></td>
					</tr>
					<tr>
						<td valign="top" width='20' style="border-left:none;"></td>
						<td width='250'><b>Port of Loading : </b></td>
						<td width='250' style="border-right:none;"> <? echo $port_of_loading;?><br/></td>
					</tr>
					<tr>
						<td valign="top" width='20' style="border-bottom:none; border-left:none;"></td>
						<td width='250' style="border-bottom:none;"><b>Port of Destination : </b></td>
						<td width='250' style="border-right:none; border-bottom:none;"> <? echo $port_of_discharge;?><br/></td>
					</tr>
				</table>
            </td>
			
			<td colspan="6" valign="top"> 
				<table style="border-collapse: collapse; border:none;" border="1">
					<tr>
						<td valign="top" width='20'>10</td>
						<td colspan = "2" width='250' style="border-right: none;"><b>F/VSL : <? echo $feeder_vessel;?></b></td>
					</tr>
					<tr>
						<td valign="top" width='20'></td>
						<td width='250'><b>HBL/FCR: <?echo $bl_no;?></b></td>
						<td width='250' style="border-right: none;"><b>DATED: <?echo change_date_format($bl_date);?></b></td>
					</tr>

					<tr>
						<td valign="top" width='20'>9</td>
						<td width='250'><b>OBL: <?echo "NF";?> </b></td>
						<td width='250' style="border-right: none;"><b>DATED: <?echo change_date_format($bl_rev_date) ;?></b></td>
					</tr>
					<tr>
						<td valign="top" width='20'  style="border-bottom:none;">11</td>
						<td colspan="2" style="border-bottom:none; border-right: none;"><b>Shimpemnt Term: <?
						$shipment = array("1"=>"Collect", "2"=>"Prepaid");
						echo $shipment[$shipment_terms];?></b></td>
					</tr>
				</table>
            </td>
        </tr>

		<tr>
			<td colspan="6" valign="top"> 
				<table style="border-collapse: collapse; border:none;" border="1">
					<tr>
						<td valign="top" style="border-top:none; border-bottom:none; border-left:none;" width='20'>12</td>
						<td width='500' style="border-right:none; border-top:none; border-bottom:none;"><b>COUNTRY OF ORIGIN:  BANGLADESH</b></td>
					</tr>
				</table>
            </td>
			
			<td colspan="6" valign="top">
				<table style="border-collapse: collapse; border:none;"  border="1">
					<tr>
						<td valign="top" style="border-top:none; border-bottom:none; border-left:none;" width='20'>14</td>
						<td width='500' style="border-top:none; border-bottom:none; border-right: none;"><b>SHIPMENT MODE : BY SEA</b></td>
					</tr>
					
				</table>		
            </td>
        </tr>

		<tr>
			<td colspan="6" valign="top"> 
				<table style="border-collapse: collapse; border:none;" border="1">
					<tr>
						<td valign="top" style="border-bottom:none; border-top:none; border-left:none;" width='20'>13</td>
						<td width='250' style="border-bottom:none; border-top:none;" ><b>AMOUNT FOB BANGLADESH :</b></td>
						<td width='250' style="border-right:none; border-bottom:none; border-top:none;"><b><?echo "VENDOR 1000003953";?></b></td>
					</tr>
					
				</table>
            </td>
			
			<td colspan="6" valign="top">
				<table style="border-collapse: collapse" border="1">
					<tr>
					</tr>
					
				</table>		
            </td>
        </tr>
	</table>
    <br>
	<table width='1040' cellspacing="0" cellpadding="0" border="1">  
		<tr style="font-size:small; font-weight:bold" align="center">
			<td ><P>MAIN MARK</P> </td>
			<td ><P>PO NOS</P> </td>
			<td ><P>STYLE NOS</P> </td>
			<td ><P>DESCRIPTION OF GOODS</P>  </td>

			<td ><P>CARTON</P> </td>
			<td ><P>PCS</P> </td>
			<td ><P>UNIT PRICE IN US$</P></td>
			<td ><P>TOTAL AMOUNT IN US$</P></td>
		</tr>

		<?
		$i=1;
		foreach($result as $row)
		{
			?>
			<tr style="font-size:small">
				<?if($i==1){?>
				<td width="120" valign="top" rowspan="<?=count($result);?>"><?echo  $main_mark ."<br>".$side_mark?></td><?}?>
				<td width="150" ><p><? echo $row["PO_NUMBER"]; ?></p></td>
				<td width="120"><p><? echo $row['STYLE_REF_NO']; ?></p></td>
				<td width="190"> <p><? echo $item_description; //$row['STYLE_DESCRIPTION']; ?></p></td>
				<td width="120" align="right"><p> </p> <?
				echo number_format($row['CARTON_QTY'],2);
				// echo number_format($carton_arr[$row['PO_BREAKDOWN_ID']],0,".",","); ?></td>
				<td width="100" align="right"> <p><? echo number_format($row['CURRENT_INVOICE_QNTY']*$setQtyArr[$row['PO_BREAKDOWN_ID']],0,".",",");  ?> </p></td>
				<td width="120" align="right"><p><?  echo "$".number_format($row["CURRENT_INVOICE_RATE"],2); ?></p></td>
				<td width="120" align="right"><p><? echo "$".number_format($row["CURRENT_INVOICE_VALUE"],2,".",","); ?></p></td>
			</tr>
			<?
			$total_rate+=$row["CURRENT_INVOICE_RATE"];
			$total_value+=$row["CURRENT_INVOICE_VALUE"];
			$total_qnty+=$row["CURRENT_INVOICE_QNTY"]*$setQtyArr[$row['PO_BREAKDOWN_ID']];
			$last_uom=$unit_of_measurement[$row['ORDER_UOM']];
			//$total_po_carton_qnty+=$carton_arr[$row['PO_BREAKDOWN_ID']];
			$total_po_carton_qnty+=$row['CARTON_QTY'];
			$i++;
		}
		?>
		<tr>
			<td colspan="12" align="left"><b>H.S Code : <? echo $hs_code;?></b></td>
		</tr>
		<tr>
			<td colspan="4" align="right"><b>Total</b></td>
			<td align="right"><b><? echo $total_po_carton_qnty; ?></b></td>
			<td align="right"><b><? echo number_format($total_qnty,0,".",",")." Pcs" ?></b></td>
			<td align="right"> <b><? echo "$&nbsp;".number_format($total_rate,2,".",","); ?></b></td>
			<td align="right"> <b><? echo "$&nbsp;".number_format($total_value,2,".",","); ?></b></td>
		</tr>

		<tr>
			<td colspan="12"><b>TOTAL US DOLLAR <? echo number_to_words(def_number_format($total_value,2,""),"USD", "CENTS")." ONLY";?></b></td>
		</tr>
		<?
		foreach($data_terms as $row)
		{
			?>
				<tr>
					<td valign="top" colspan="3"><? echo $row[csf('terms_prefix')]; ?></td>
					<td colspan="9"><? echo $row[csf('terms')]; ?></td>
				</tr>
			<?
		}
		?>
		
		</table>
		<br>
		<table width='400' cellspacing="0" cellpadding="0" border="1">  
			<tr style="font-weight:bold">
				<td width ="180" align="left">Total Qty</td>
				<td width ="220" align="right"><? echo number_format($total_rate,0,".",","); ?> PCS</td>
			</tr>
			<tr style="font-weight:bold">
				<td width ="180" align="left">Total Cartons</td>
				<td width ="220" align="right"><? echo number_format($total_po_carton_qnty,0,".",","); ?> CTNS</td>
			</tr>
			<tr style="font-weight:bold">
				<td width ="180" align="left">Total Gross Wt.</td>
				<td width ="220" align="right"><? echo number_format($gross_weight,2,".",","); ?> KGS</td>
			</tr>
			<tr style="font-weight:bold" >
				<td width ="180" align="left">Total Net Wt</td>
				<td width ="220" align="right"><? echo number_format($net_weight,2,".",","); ?> KGS</td>
			</tr>
			<tr style="font-weight:bold">
				<td width ="180" align="left">Total Volume</td>
				<td width ="220" align="right"><? echo number_format(2332,2); ?></td>
			</tr>
			<tr style="font-weight:bold">
				<td colspan="2" width ="400" align="left">U/D No : <? echo $ud_no; ?></td>
				
			</tr>
		</table>
 
	<?
		$html = ob_get_contents();
		ob_clean();
		foreach (glob("tb*.xls") as $filename) {
		@unlink($filename);
		}
		//---------end------------//
		$name=time();
		$filename="tb".$user_id."_".$name.".xls";
		$create_new_doc = fopen($filename, 'w');
		$is_created = fwrite($create_new_doc, $html);
		echo "$filename####$html";
	exit();	
}
if($action=="invoice_report_print_6")  //print 6
{
	extract($_REQUEST);
	// $update_id=$data;
	$company_arr = return_library_array("select id, company_name from lib_company ","id","company_name");
	$buyer_name=return_library_array("select id, buyer_name from lib_buyer where status_active = 1 and is_deleted = 0","id","buyer_name");
	$brand_arr=return_library_array( "SELECT ID, BRAND_NAME FROM LIB_BUYER_BRAND BRAND",'ID','BRAND_NAME');
	$applicant_sql=sql_select( "SELECT A.ID, A.BUYER_NAME, A.SHORT_NAME, A.ADDRESS_1 FROM LIB_BUYER A");
	foreach($applicant_sql as $row)
	{
		$buyer_name_arr[$row["ID"]]["BUYER_NAME"]=$row["BUYER_NAME"];
		$buyer_name_arr[$row["ID"]]["ADDRESS_1"]=$row["ADDRESS_1"];
	}
	$bank_sql=sql_select( "SELECT A.ID, A.BANK_NAME, A.BRANCH_NAME, A.ADDRESS, A.SWIFT_CODE FROM LIB_BANK A");
	foreach($bank_sql as $row)
	{
		$bank_name_arr[$row["ID"]]["BANK_NAME"]=$row["BANK_NAME"];
		$bank_name_arr[$row["ID"]]["BRANCH_NAME"]=$row["BRANCH_NAME"];
		$bank_name_arr[$row["ID"]]["ADDRESS"]=$row["ADDRESS"];
		$bank_name_arr[$row["ID"]]["SWIFT_CODE"]=$row["SWIFT_CODE"];
	}
	$bank_account_sql=sql_select( "SELECT ID, ACCOUNT_ID, ACCOUNT_TYPE, ACCOUNT_NO FROM LIB_BANK_ACCOUNT WHERE IS_DELETED=0 ");
	foreach($bank_account_sql as $row)
	{
		$bank_acc_arr[$row["ACCOUNT_ID"]][$row["ACCOUNT_TYPE"]]["ACCOUNT_NO"]=$row["ACCOUNT_NO"];
	}
	$inv_master_data=sql_select("SELECT ID, BENIFICIARY_ID, BUYER_ID, LOCATION_ID, INVOICE_NO, INVOICE_DATE, EXP_FORM_NO, EXP_FORM_DATE, IS_LC, LC_SC_ID, BL_NO, FEEDER_VESSEL, INCO_TERM, INCO_TERM_PLACE, SHIPPING_MODE, PORT_OF_ENTRY, PORT_OF_LOADING, PORT_OF_DISCHARGE, MAIN_MARK, SIDE_MARK, CARTON_NET_WEIGHT, CARTON_GROSS_WEIGHT, CBM_QNTY, PLACE_OF_DELIVERY, DELV_NO, CONSIGNEE, NOTIFYING_PARTY, ITEM_DESCRIPTION, DISCOUNT_AMMOUNT, BONUS_AMMOUNT, COMMISSION, TOTAL_CARTON_QNTY, BL_DATE, HS_CODE, MOTHER_VESSEL, CATEGORY_NO, FORWARDER_NAME, ETD,CO_NO, TOTAL_MEASURMENT, INVOICE_VALUE, NET_INVO_VALUE, CONTAINER_NO, SEAL_NO, ETD, COUNTRY_ID,COMMISSION_PERCENT,BL_REV_DATE,UD_NO FROM COM_EXPORT_INVOICE_SHIP_MST WHERE ID=$update_id");
	$id=$inv_master_data[0]["ID"];
	$benificiary_id=$inv_master_data[0]["BENIFICIARY_ID"];
	$buyer_id=$inv_master_data[0]["BUYER_ID"];
	$location_id=$inv_master_data[0]["LOCATION_ID"];
	$invoice_no=$inv_master_data[0]["INVOICE_NO"];
	$invoice_date=$inv_master_data[0]["INVOICE_DATE"];
	$bl_rev_date=$inv_master_data[0]["BL_REV_DATE"];
	$exp_form_no=$inv_master_data[0]["EXP_FORM_NO"];
	$exp_form_date=$inv_master_data[0]["EXP_FORM_DATE"];
	$is_lc=$inv_master_data[0]["IS_LC"];
	$lc_sc_id=$inv_master_data[0]["LC_SC_ID"];
	$bl_no=$inv_master_data[0]["BL_NO"];
	$feeder_vessel=$inv_master_data[0]["FEEDER_VESSEL"];
	$inco_term=$inv_master_data[0]["INCO_TERM"];
	$inco_term_place=$inv_master_data[0]["INCO_TERM_PLACE"];
	$shipping_mode=$inv_master_data[0]["SHIPPING_MODE"];
	$port_of_entry=$inv_master_data[0]["PORT_OF_ENTRY"];
	$port_of_loading=$inv_master_data[0]["PORT_OF_LOADING"];
	$port_of_discharge=$inv_master_data[0]["PORT_OF_DISCHARGE"];
	$main_mark=$inv_master_data[0]["MAIN_MARK"];
	$side_mark=$inv_master_data[0]["SIDE_MARK"];
	$net_weight=$inv_master_data[0]["CARTON_NET_WEIGHT"];
	$gross_weight=$inv_master_data[0]["CARTON_GROSS_WEIGHT"];
	$cbm_qnty=$inv_master_data[0]["CBM_QNTY"];
	$place_of_delivery=$inv_master_data[0]["PLACE_OF_DELIVERY"];
	$delv_no=$inv_master_data[0]["DELV_NO"];
	$consignee=$inv_master_data[0]["CONSIGNEE"];
	$notifying_party=$inv_master_data[0]["NOTIFYING_PARTY"];
	$item_description=$inv_master_data[0]["ITEM_DESCRIPTION"];
	$discount_ammount=$inv_master_data[0]["DISCOUNT_AMMOUNT"];
	$bonus_ammount=$inv_master_data[0]["BONUS_AMMOUNT"];
	$commission=$inv_master_data[0]["COMMISSION"];
	$commission_percent=$inv_master_data[0]["COMMISSION_PERCENT"];
	$total_carton_qnty=$inv_master_data[0]["TOTAL_CARTON_QNTY"];
	$bl_date=$inv_master_data[0]["BL_DATE"];
	$hs_code=$inv_master_data[0]["HS_CODE"];
	$mother_vessel=$inv_master_data[0]["MOTHER_VESSEL"];
	$category_no=$inv_master_data[0]["CATEGORY_NO"];
	$forwarder_name=$inv_master_data[0]["FORWARDER_NAME"];
	$etd=$inv_master_data[0]["ETD"];
	$co_no=$inv_master_data[0]["CO_NO"];
	$total_measurment=$inv_master_data[0]["TOTAL_MEASURMENT"];
	$net_invo_value=$inv_master_data[0]["NET_INVO_VALUE"];
	$container_no=$inv_master_data[0]["CONTAINER_NO"];
	$seal_no=$inv_master_data[0]["SEAL_NO"];
	$etd=$inv_master_data[0]["ETD"];
	$inv_country_id=$inv_master_data[0]["COUNTRY_ID"];
	$total_discount=$inv_master_data[0]["INVOICE_VALUE"]-$inv_master_data[0]["NET_INVO_VALUE"];
	$ud_no= $inv_master_data[0]["UD_NO"];
	
	$itemIdArr=array();
	$setQtyArr=array();
	$poIdArr=array();
	$dtls_sql="SELECT A.ID AS DTLS_ID, A.PO_BREAKDOWN_ID,C.TOTAL_SET_QNTY FROM  COM_EXPORT_INVOICE_SHIP_DTLS A,  WO_PO_BREAK_DOWN B, WO_PO_DETAILS_MASTER C WHERE A.PO_BREAKDOWN_ID=B.ID AND B.JOB_NO_MST=C.JOB_NO AND A.CURRENT_INVOICE_QNTY>0 AND A.STATUS_ACTIVE=1 AND A.IS_DELETED=0 AND A.MST_ID=$update_id";
	$PO_agent=sql_select($dtls_sql);
	foreach($PO_agent as $row){
		$poIdArr[]=$row['PO_BREAKDOWN_ID'];
		$setQtyArr[$row['PO_BREAKDOWN_ID']]=$row['TOTAL_SET_QNTY'];
	}
	

	$ex_fac_arr=array();
	$sqlExfac=sql_select("SELECT A.ID,A.SYS_NUMBER, B.EX_FACTORY_QNTY,B.PO_BREAK_DOWN_ID,B.TOTAL_CARTON_QNTY FROM PRO_EX_FACTORY_DELIVERY_MST A,PRO_EX_FACTORY_MST B WHERE A.ID=B.DELIVERY_MST_ID AND B.PO_BREAK_DOWN_ID IN(".implode(",",$poIdArr).")");
	foreach($sqlExfac as $row)
	{
		$exfac_qty+=$row['EX_FACTORY_QNTY'];
		$carton_qty+=$row['TOTAL_CARTON_QNTY'];
		$ex_fac_arr[$row['PO_BREAK_DOWN_ID']]['TOTAL_CARTON_QNTY']=$row['TOTAL_CARTON_QNTY'];
	}

	$agent_id="";
	// $fristPo=array_shift($poIdArr);
	$sql_fabric=sql_select("SELECT B.ID, C.CONSTRUCTION, C.COMPOSITION FROM WO_PO_BREAK_DOWN B, WO_PRE_COST_FABRIC_COST_DTLS C WHERE B.JOB_NO_MST=C.JOB_NO AND B.ID IN(".implode(",",$poIdArr).")");

	foreach($sql_fabric as $row_fabric){
		$fabric_info[$row_fabric["ID"]]=$row_fabric["CONSTRUCTION"]." ".$row_fabric["COMPOSITION"];
	}
	
	if($is_lc==1)
	{
		$lc_sc_data=sql_select("SELECT ID, EXPORT_LC_NO, LC_DATE, NOTIFYING_PARTY, CONSIGNEE, ISSUING_BANK_NAME, NEGOTIATING_BANK, LIEN_BANK, PAY_TERM, APPLICANT_NAME,INCO_TERM,LIEN_BANK,NOMINATED_SHIPP_LINE, BUYER_NAME, TENOR,SHIPPING_MODE FROM COM_EXPORT_LC WHERE ID='".$lc_sc_id."' ");
		foreach($lc_sc_data as $row)
		{
			$lc_sc_id=$row["ID"];
			$lc_sc_no=$row["EXPORT_LC_NO"];
			$lc_sc_date=change_date_format($row["LC_DATE"]);
			$notifying_party=$row["NOTIFYING_PARTY"];
			$consignee=$row["CONSIGNEE"];
			$issuing_bank_name=$row["ISSUING_BANK_NAME"];
			$negotiating_bank=$row["LIEN_BANK"];
			$pay_term_id=$row["PAY_TERM"];
			$applicant_name=$row["APPLICANT_NAME"];
			$buyer_name=$row["BUYER_NAME"];
			$inco_term=$row["INCO_TERM"];
			$lien_bank=$row["LIEN_BANK"];
			$shipping_line=$row["NOMINATED_SHIPP_LINE"];
			$negotiating_bank_text=$row["NEGOTIATING_BANK"];
			$tenor=$row["TENOR"];
			$shipping_mode=$row["SHIPPING_MODE"];
		}
		
			$cate_hs_sql=sql_select("SELECT WO_PO_BREAK_DOWN_ID, FABRIC_DESCRIPTION, CATEGORY_NO, HS_CODE FROM COM_EXPORT_LC_ORDER_INFO WHERE COM_EXPORT_LC_ID='".$lc_sc_id."'");
			foreach($cate_hs_sql as $row)
			{
				$order_la_data[$row["WO_PO_BREAK_DOWN_ID"]]["CATEGORY_NO"]=$row["CATEGORY_NO"];
				$order_la_data[$row["WO_PO_BREAK_DOWN_ID"]]["HS_CODE"]=$row["HS_CODE"];
			    $all_order_id[$row["WO_PO_BREAK_DOWN_ID"]]=$row["WO_PO_BREAK_DOWN_ID"];
			}
	}
	else
	{
		$lc_sc_data=sql_select("SELECT ID, CONTRACT_NO, CONTRACT_DATE, NOTIFYING_PARTY, CONSIGNEE, LIEN_BANK, PAY_TERM, APPLICANT_NAME,INCO_TERM,LIEN_BANK,SHIPPING_LINE,BUYER_NAME, TENOR,SHIPPING_MODE FROM COM_SALES_CONTRACT WHERE ID='".$lc_sc_id."'  AND STATUS_ACTIVE=1");
		foreach($lc_sc_data as $row)
		{
			$lc_sc_id=$row["ID"];
			$Sc_no=$row["CONTRACT_NO"];
			$Sc_date=change_date_format($row["CONTRACT_DATE"]);
			$notifying_party=$row["NOTIFYING_PARTY"];
			$consignee=$row["CONSIGNEE"];
			$issuing_bank_name="";
			$negotiating_bank=$row["LIEN_BANK"];
			$pay_term_id=$row["PAY_TERM"];
			$applicant_name=$row["APPLICANT_NAME"];
			$buyer_name=$row["BUYER_NAME"];
			$inco_term=$row["INCO_TERM"];
			$lien_bank=$row["LIEN_BANK"];
			$shipping_line=$row["SHIPPING_LINE"];
			$tenor=$row["TENOR"];
			$shipping_mode=$row["SHIPPING_MODE"];
			$negotiating_bank_text="";
		}
		
		$cate_hs_sql=sql_select("SELECT WO_PO_BREAK_DOWN_ID, FABRIC_DESCRIPTION, CATEGORY_NO, HS_CODE FROM COM_SALES_CONTRACT_ORDER_INFO WHERE COM_SALES_CONTRACT_ID='".$lc_sc_id."' AND STATUS_ACTIVE=1");
		foreach($cate_hs_sql as $row)
		{
			$order_la_data[$row["WO_PO_BREAK_DOWN_ID"]]["CATEGORY_NO"]=$row["CATEGORY_NO"];
			$order_la_data[$row["WO_PO_BREAK_DOWN_ID"]]["HS_CODE"]=$row["HS_CODE"];
			$order_la_data[$row["WO_PO_BREAK_DOWN_ID"]]["FABRIC_DESCRIPTION"]=$row["FABRIC_DESCRIPTION"];
			$all_order_id[$row["WO_PO_BREAK_DOWN_ID"]]=$row["WO_PO_BREAK_DOWN_ID"];
		}
	}
	
	$company_name_sql=sql_select( "SELECT ID, COMPANY_NAME, PLOT_NO, LEVEL_NO, ROAD_NO, BLOCK_NO, CITY, COUNTRY_ID,ERC_NO,EMAIL,CONTACT_NO,REX_NO,REX_REG_DATE,IRC_NO,VAT_NUMBER FROM LIB_COMPANY WHERE ID ='$benificiary_id'");
	foreach($company_name_sql as $row)
	{
		$company_name=$row["COMPANY_NAME"];
		$plot_no=$row["PLOT_NO"];
		$level_no=$row["LEVEL_NO"];
		$road_no=$row["ROAD_NO"];
		$block_no=$row["BLOCK_NO"];
		$city=$row["CITY"];
		$country_id=$row["COUNTRY_ID"];
		$erc_no=$row["ERC_NO"];
		$contact_no=$row["CONTACT_NO"];
		$email=$row["EMAIL"];
		$rex_no=$row["REX_NO"];
		$rex_reg_date=$row["REX_REG_DATE"];
		$irc_no=$row["IRC_NO"];
		$vat_number=$row["VAT_NUMBER"];
	}
	
	$country_name_arr=return_library_array( "SELECT ID, COUNTRY_NAME FROM LIB_COUNTRY",'ID','COUNTRY_NAME');
	// $carrier=$SupplierArr[$forwarder_name];
	$applicant=$buyer_name_arr[$applicant_name]["BUYER_NAME"];
	$buyer=$buyer_name_arr[$buyer_name]["BUYER_NAME"];
	$applicantAddress=$buyer_name_arr[$applicant_name]["ADDRESS_1"];
	$agent=$buyer_name_arr[$agent_id]["BUYER_NAME"];
	$agentAddress=$buyer_name_arr[$agent_id]["ADDRESS_1"];
		
	$dtls_sql="SELECT A.ID AS DTLS_ID, A.PO_BREAKDOWN_ID, A.CURRENT_INVOICE_RATE, A.CURRENT_INVOICE_QNTY, A.CURRENT_INVOICE_VALUE, B.PO_NUMBER, C.STYLE_REF_NO, C.GMTS_ITEM_ID, C.ORDER_UOM, C.GMTS_ITEM_ID,C.STYLE_DESCRIPTION, C.BRAND_ID  FROM  COM_EXPORT_INVOICE_SHIP_DTLS A,  WO_PO_BREAK_DOWN B, WO_PO_DETAILS_MASTER C WHERE A.PO_BREAKDOWN_ID=B.ID AND B.JOB_NO_MST=C.JOB_NO AND A.CURRENT_INVOICE_QNTY>0 AND A.STATUS_ACTIVE=1 AND A.IS_DELETED=0 AND A.MST_ID=$update_id";
	//echo $dtls_sql;//die();
	 $proforma_sql="select distinct a.pi_number,a.pi_date from com_export_pi_mst a, com_export_pi_dtls b,WO_PO_BREAK_DOWN c,com_export_invoice_ship_dtls d where a.id=b.pi_id and  c.JOB_ID=b. work_order_id and d.PO_BREAKDOWN_ID=c.ID and d.MST_ID=$update_id and a.STATUS_ACTIVE=1 AND b.STATUS_ACTIVE=1";
	//echo $proforma_sql;
	$pi_numbers = array();
    $pi_dates = array();
	$result_2=sql_select($proforma_sql);
	foreach ($result_2 as $row)
	{
		$pi_numbers[]=$row[csf("pi_number")];
		
		$pi_dates []=change_date_format($row[csf("pi_date")]);

	}
	$pi_numbers_str = implode(", ", $pi_numbers);
    $pi_dates_str = implode(", ", $pi_dates);
	
	
	$result=sql_select($dtls_sql);

	$sql="SELECT B.ID AS ID, B.LOCATION_NAME || ' (' || A.COMPANY_SHORT_NAME || ')' AS LOCATION_NAME  FROM LIB_COMPANY A, LIB_LOCATION B WHERE A.ID=B.COMPANY_ID AND B.STATUS_ACTIVE =1 AND B.IS_DELETED=0 AND A.STATUS_ACTIVE =1 AND A.IS_DELETED=0 ORDER BY B.LOCATION_NAME";

	$location_dtls_sql = "SELECT A.COMPANY_NAME , B.LOCATION_NAME , B.ADDRESS , B.EMAIL,B.COUNTRY_ID FROM  LIB_COMPANY A ,LIB_LOCATION B WHERE A.ID=B.COMPANY_ID AND B.STATUS_ACTIVE =1 AND B.IS_DELETED=0 AND A.STATUS_ACTIVE =1 AND A.IS_DELETED=0  AND B.ID = $location_id";
	//echo $location_dtls_sql;
	$location_dtls = sql_select($location_dtls_sql);
	foreach($location_dtls as $row)
	{
		$ex_company_name=$row['COMPANY_NAME'];
		$ex_location_name=$row['LOCATION_NAME'];
		$ex_address=$row['ADDRESS'];
		$ex_country_id=$row[csf("COUNTRY_ID")];
		$ex_email=$row["EMAIL"];
	}

	$company_logo=return_field_value("image_location","common_photo_library","file_type=1 and form_name='company_details' and master_tble_id='$benificiary_id'","image_location");

	$data_terms=sql_select("SELECT ID,TERMS,TERMS_PREFIX FROM WO_BOOKING_TERMS_CONDITION WHERE BOOKING_NO=$update_id AND ENTRY_FORM=270 ORDER BY ID");
	ob_start();
	?>


	<style type="text/css" media="print">
			@page { 
				size: auto;
			} 
			
			thead {
		display: table-row-group;
	}
	</style>

 
       <table width='1040' cellspacing="0" cellpadding="0" border="1">

        <!-- <table width='1040' style="border-collapse: collapse;" border="1" > -->
	       <tr>
				<td colspan="8" valign="top" align="center" style="font-size:Large; font-weight:bold;border:none""><b><?
				$text = $company_arr[$benificiary_id];
				// Convert the string to uppercase
				$uppercaseText = strtoupper($text);
				echo $uppercaseText?></b></td>
				
            </tr>
			<tr>
				<td colspan="8" valign="top" align="center" style="font-size:Large; font-weight:bold;border:none">
					<b> <?php
					if($city!="")  $company_details.= "".$city.", ";
					if($country_id!="")  $company_details.="".$country_name_arr[$country_id]."";  
				
					echo  strtoupper($company_details);
					?>
				</b>
				</td>
			</tr>
			 <tr>
				<td colspan="8" valign="top" align="center" style="font-size:Large;text-decoration:underline; font-weight:bold"><b>COMMERCIAL INVOICE</b></td>
			</tr>
			<tr>
				<td colspan="3" valign="top"style="font-size:medium; font-weight:bold;border:none;background-color" ><strong>INVOICE NO. :&nbsp;<?echo $invoice_no?></strong></td>
				<td colspan="5" valign="top" align="right" style="font-size:medium;font-weight:bold; border:none;">DATE :&nbsp;<?echo change_date_format($invoice_date);?></td>
			</tr>
		</table>
	     


		<table border="1" style="border-collapse:collapse;" width='1040' cellspacing="0" cellpadding="0">

				<tr>
					<td  valign="top" colspan="2" style="border:none;" ><strong>SHIPPER/EXPORTER:</strong></td>
				
					<td valign="bottom" style="border:none ;" >&nbsp;<strong><?php $text1 = $company_name;
							// Convert the string to uppercase
							$uppercaseText = strtoupper($text1);
							echo $uppercaseText?></strong></td>
					<td  style="border: none;border-left:1px solid black" ><strong>BUYER'S BANK </strong></td>
					<td style="width: 10px;border: none"align="right">:&nbsp;</td>
					<td  style="border: none" ><strong><?echo strtoupper($buyer_name_arr[$buyer_name]["BUYER_NAME"])?></strong></td>
					
				</tr>
				<tr>
					<td style="border: none;"  colspan="2">&nbsp;</td>
					<td   valign="top"  style="border:none;border-right: 1px solid black;"> <?
									if($city!="")  $comany_details.= $city;
									if($country_id!="")  $comany_details.=strtoupper($country_name_arr[$country_id]).".";
									if($contact_no!="")  $comany_details.="<br>TELEPHONE: ".strtoupper($contact_no).".";
									if($email!="")  $comany_details.="<br>E-MAIL: ".$email.".";
									echo  $comany_details;
					?></td>
						<td style="border:none;"  >&nbsp;</td>
						<td style="width: 10px;border: none">&nbsp;&nbsp;</td>
					<td  valign="top" style="border:none;word-break:break-all;" ><? echo strtoupper($buyer_name_arr[$buyer_name]["ADDRESS_1"]);?></td>
				</tr>
				<tr>
					<td valign="top" style="border: none;"  colspan="2"><strong>CONSIGNEE :</strong></td>
				
					<td  style="border:none;border-right: 1px solid black;" ><strong><? echo strtoupper($buyer_name_arr[$buyer_name]["BUYER_NAME"])?></strong></td>
					<td  style="border: none;"  valign="top" rowspan="2"width="200"><strong>NEGOTIATING  BANK</strong></td>
					<td style="width: 10px;border: none" align="right">:&nbsp;</td>
					<td  style="border: none;"  ><strong><?echo strtoupper($bank_name_arr[$lien_bank]["BANK_NAME"])."<br>";?></strong></td>
				</tr>

				<tr>
				<td style="border:none;" colspan="2" >&nbsp;</td>
					<td valign="top" style="border:none;border-right: 1px solid black;"><? echo strtoupper($buyer_name_arr[$buyer_name]["ADDRESS_1"]);?></td>
					<td style="border: none">&nbsp;</td>
					<td style="border:none;"colspan="3" ><?
						echo strtoupper($bank_name_arr[$lien_bank]["BRANCH_NAME"])."<br>";
						echo strtoupper($bank_name_arr[$lien_bank]["ADDRESS"])."&nbsp;";
						?>
						<br>
						BANK ACCOUNT NO. <? echo $bank_acc_arr[$lien_bank][10]["account_no"]?>
						<br>
						SWIFT CODE- <strong><?	echo  $bank_name_arr[$lien_bank]["SWIFT_CODE"];?></strong>
					</td>
				</tr>
				<tr style="height: 15px;">
				<td  colspan="3" style="border:none;border-right:1px solid">&nbsp;</td>
			    </tr>
				<tr> 
					<td style="border:none;"  colspan="2"><strong>APPLICANT :</strong></td>
					
					<td  style="border:none;border-right: 1px solid black;"><strong><?echo $buyer_name_arr[$applicant_name]["BUYER_NAME"]; ?></strong></td>
					<td  valign="top"style="border:none;" ><strong>COUNTRY OF ORIGIN</strong></td>
					<td style="width: 10px;border: none"align="right">:&nbsp;</td>
					<td style="border:none;" >BANGLADESH</td>
				</tr>
				
				<tr>
					<td style="border:none;"colspan="2" >&nbsp;</td>
					<td  style="border:none;border-right: 1px solid black;"><?echo $buyer_name_arr[$applicant_name]["ADDRESS_1"];?></td>
					<td style="border:none;" >UD NO. </td>
					<td style="width: 10px;border: none" align="right">:&nbsp;</td>
					<td style="border:none;" align="left"><? echo $ud_no;?></td>
					<td style="border:none;" >DATE:</td>
				</tr>
				<tr>
					<td style="border:none;"  colspan=""><strong>NOTIFY PARTY</strong></td>
					<td valign="top"  style="border:none;" ><b>:</b></td>
					<td  style="border:none;border-right: 1px solid black;" colspan=""><strong><? if($buyer_name_arr[$notifying_party]["BUYER_NAME"]!=''){echo $buyer_name_arr[$notifying_party]["BUYER_NAME"]."<br/>";}?></strong></td>
					<td style="border:none;" >TERMS OF PAYMENT</td>
					<td style="width: 10px;border: none"align="right">:&nbsp;</td>
					<td style="border:none;" ><? echo $pay_term[$pay_term_id];?></td>
				</tr>
				<tr>
					<td style="border:none;" colspan="2" >&nbsp;</td>
					<td  style="border:none;border-right: 1px solid black;"><?if($buyer_name_arr[$notifying_party]["ADDRESS_1"]!=''){echo $buyer_name_arr[$notifying_party]["ADDRESS_1"];} ?></td>
					<td style="border:none;" >TERMS OF DELIVERY</td>
					<td style="width: 10px;border: none"align="right">:&nbsp;</td>
					<td style="border:none;" ><? echo $incoterm[$inv_master_data[0][csf("inco_term")]].",".$inco_term_place;?></td>
				</tr>
				<tr>
					<td colspan="3"  style="border:none;border-right: 1px solid black;">&nbsp;</td>
					<td style="border:none;" > FINAL DELIVERY</td>
					<td style="width: 10px;border: none"align="right">:&nbsp;</td>
					<td style="border:none;"><?echo $place_of_delivery;?></td>
				</tr>
				<tr>
					<td colspan="3" style="border:none;border-right: 1px solid black;">&nbsp;</td>
					<td style="border:none;" > PLACE OF LOADING</td>
					<td style="width: 10px;border: none"align="right">:&nbsp;</td>
					<td style="border:none;" ><? echo $port_of_loading;?></td>
				</tr>
				<tr>
					<td colspan="3" style="border:none;border-right: 1px solid black;">&nbsp;</td>
					<td style="border:none;" > FINAL DESTINATION</td>
					<td style="width: 10px;border: none"align="right">:&nbsp;</td>
					<td colspan="2" style="border:none;" ><?echo $place_of_delivery;?></td>
				</tr>
				<tr>
					<td colspan="3" style="border:none;border-right: 1px solid black;">&nbsp;</td>
					<td style="border:none;" > PORT OF DISCHARGE</td>
					<td style="width: 10px;border: none"align="right">:&nbsp;</td>
					<td style="border:none;" ><?echo $inv_master_data[0][csf("port_of_discharge")];?></td>
				</tr>
				<tr>
					<td colspan="3" style="border:none;border-right: 1px solid black;">&nbsp;</td>
					<td style="border:none;" > L/C NO.</td>
					<td style="width: 10px;border: none"align="right">:&nbsp;</td>
					<td style="border:none;" colspan=""> <? echo $lc_sc_no;?></td>
					<td style="border:none;" >DATE:<? echo $lc_sc_date;?></td>
				</tr>
				<tr>
					<td colspan="3" style="border:none;border-right: 1px solid black;">&nbsp;</td>
					<td style="border:none;" > S/C NO.</td>
					<td style="width: 10px;border: none"align="right">:&nbsp;</td>
					<td style="border:none;" colspan=""><? echo $Sc_no;?></td>
					<td style="border:none;" >DATE:<?echo $Sc_date;?></td>
				</tr>
				<tr>
					<td colspan="3" style="border:none;border-right: 1px solid black;">&nbsp;</td>
					<td style="border:none;" >EXP NO.</td>
					<td style="width: 10px;border: none"align="right">:&nbsp;</td>
					<td style="border:none;" ><?  echo $exp_form_no;?> </td>
					<td style="border:none;" >DATE:<? echo $exp_form_date;?></td>
				</tr>
				<tr>
					<td colspan="3" style="border:none;border-right: 1px solid black;">&nbsp;</td>
					<td style="border:none;"  >HS CODE NO</td>
					<td style="width: 10px;border: none"align="right">:&nbsp;</td>
					<td style="border:none;" > <? echo $hs_code;?></td>
				</tr>
				<tr>
					<td colspan="3" style="border:none;border-right: 1px solid black;">&nbsp;</td>
					<td style="border:none;" >BL No.</td>
					<td style="width: 10px;border: none"align="right">:&nbsp;</td>
					<td style="border:none;" ><? echo $bl_no;?></td>
				</tr>
				<tr>
					<td colspan="3" style="border:none;border-right: 1px solid black;">&nbsp;</td>
					<td style="border:none;" > MOTHER VESSEL</td>
					<td style="width: 10px;border: none"align="right">:&nbsp;</td>
					<td style="border:none;"  ><? echo $mother_vessel;?></td>
				</tr>
				<tr>
					<td colspan="3" style="border:none;border-right: 1px solid black;">&nbsp;</td>
					<td style="border:none;" > CONTAINER NUMBER</td>
					<td style="width: 10px;border: none"align="right">:&nbsp;</td>
					<td style="border:none;" align="left"><?echo $container_no;?></td>
				</tr>
		</table>
	 

				<table width='1040' cellspacing="0" cellpadding="0" border="1">
					<thead style="">
							<tr>
								<th >ORDER NUMBER</th>
								<th >GOODS DESCRIPTION		</th>
								<th>STYLE</th>
								<th>COMPOSITION			</th>
								<th>Total qty (pcs)</th>
								<th> Unit price </th>
								<th colspan="2">Total value (FOB CHITTAGONG)</th>
							</tr>
					</thead>
		
				<tbody>
				<?
						
				foreach($result as $row)
				{
					?>
					<tr >
						
						<td ><p><? echo $row["PO_NUMBER"]; ?></p></td>
						<td > <p><? echo $row['STYLE_DESCRIPTION']; ; //$row['STYLE_DESCRIPTION']; ?></p></td>
						<td ><p><? echo $row['STYLE_REF_NO']; ?></p></td>
						<td>&nbsp;</td>
						<td width="100" align="right"> <p><? echo number_format($row['CURRENT_INVOICE_QNTY']*$setQtyArr[$row['PO_BREAKDOWN_ID']],0,".",",");  ?> </p></td>
						<td width="120" align="right"><p><?  echo "$".number_format($row["CURRENT_INVOICE_RATE"],2); ?></p></td>
						<td width="120" align="right" colspan="2"><p><? echo "$".number_format($row["CURRENT_INVOICE_VALUE"],2,".",","); ?></p></td>
					</tr>
					<?
					$total_rate+=$row["CURRENT_INVOICE_RATE"];
					$total_value+=$row["CURRENT_INVOICE_VALUE"];
					$total_qnty+=$row["CURRENT_INVOICE_QNTY"]*$setQtyArr[$row['PO_BREAKDOWN_ID']];
					$last_uom=$unit_of_measurement[$row['ORDER_UOM']];
					$total_ex_factory_qnty+=$rowCarton['EX_FACTORY_QNTY'];
					
					$i++;
				}
				?>

		</tbody>

		<tfoot>
			<tr>
				<td>&nbsp;</td>
				<td>&nbsp;</td>
				<td>&nbsp;</td>
				<td>&nbsp;</td>
				<td>&nbsp;</td>
				<td>&nbsp;</td>
				<td colspan="2">&nbsp; </td>
				
			<tr>
			<tr>
			<td>&nbsp;</td>
			<td>&nbsp;</td>
			<td>&nbsp;</td>
			<td>&nbsp;</td>
			<td>&nbsp;</td>
			<td>&nbsp;</td>
			<td colspan="2">&nbsp;</td>
			
			</tr>
			<tr>
				<td>&nbsp;</td>
			<td >PROFORMA INVOICE NO. 	</td>
			<td colspan="6"><? echo $pi_numbers_str?></td>
			</tr>
			<tr>
			<td>&nbsp;</td>
			<td>	DATE:&nbsp; </td>
			
			<td colspan="6"><? echo    $pi_dates_str;
			?></td>
			</tr>

				<td colspan="4" align="right"><strong>Total=</strong></td>
				<td align="right"><strong><? echo number_format($total_qnty,0,'.',',');?></strong></td>
				<td>&nbsp;</td>
				<td align="right" colspan="2"><strong><? echo "$".number_format($total_value,2,'.',',');?></strong></td>
			</tr>
			<tr>
				<td colspan="8">&nbsp;</td>
			</tr>
		</tfoot>
		</table>

	<table width='1040' cellspacing="0" cellpadding="0" border="1">
		<tr>
			<td align="center" colspan="8"><strong>STATEMENT ON ORIGIN		</strong></td>
		</tr>
	    <tr>
		<td colspan="8" style="text-align: justify;">
		<strong> <?
			foreach($data_terms as $row)
			{
				
				echo $row[csf('terms_prefix')].$row[csf('terms')]; 
					
				
			}
			?>
			</strong>
		</td>
	   </tr>
    </table>

      <table width='1040' cellspacing="0" cellpadding="0" border="1">
					<tr>
						<th >SHIPMENT - TOTAL</th>
						<th colspan="">&nbsp;</th>
						<th colspan="3"style="border: none;">&nbsp;</th>
						<th align="left" style="border: none; text-decoration:underline" colspan="3"><strong>SHIPPING MARK :</strong></th>
						
					</tr>
					<tr>
						<td>TOTAL QTY</td>
						<td><? echo $exfac_qty;?>&nbsp;&nbsp;&nbsp;PCS</td>
						<td colspan="3"style="border: none;">&nbsp;</td>
						<td style="border: none;"><strong>ORDER NUMBER:</strong></td>
					</tr>
					<tr>
						<td>CARTONS	</td>
						<td><? echo $carton_qty;?>&nbsp;&nbsp;&nbsp;CTNS</td>
						<td colspan="3"style="border: none;">&nbsp;</td>
						<td   style="border: none;"><strong>STYLE NUMBER:</strong></td>
					</tr>
					<tr>
						<td>CUBAGE</td>
						<td><? echo$cbm_qnty ;?>&nbsp;&nbsp;&nbsp;CBM</td>
						<td colspan="3"style="border: none;">&nbsp;</td>
						<td  style="border: none;"><strong>ITEM CODE:</strong></td>
					</tr>
					<tr>
						<td>NET-  Weight</td>
						<td><? echo $net_weight;?>&nbsp;&nbsp;&nbsp;KGS</td>
						<td colspan="3"style="border: none;">&nbsp;</td>
						<td colspan="" style="border: none;"><strong>ITEM DESCRIPTION:</strong></td>
					</tr>
					<tr>
						<td>GRS- Weight</td>
						<td><? echo $gross_weight;?>&nbsp;&nbsp;&nbsp;KGS</td>
						<td colspan="3"style="border: none;">&nbsp;</td>
						<td  colspan="" style="border: none;"><strong>GARMENTS QUANTITY:<? echo ".........";?>PCS</strong>	</td>
					</tr>
					<tr>
						<td align="center" colspan="4" style="border: none;">&nbsp;</td>
						<td style="border: none;">&nbsp;</td>
						<td  colspan="2" style="border: none;"><strong>GROSS WEIGHT:<? echo ".........";?>KGS	</strong></td>
					</tr>
					<tr>
					    <td align="center" colspan="4"style="border: none;">&nbsp;</td>
						<td style="border: none;">&nbsp;</td>
						
						<td style="border: none;" ><strong>NET WEITHT:<? echo ".........";?>KGS </strong>	</td>
					
					
					</tr>
					<tr>
					    <td colspan="4"style="border: none;">&nbsp;</td>
						<td style="border: none;">&nbsp;</td>
						<td colspan="2"style="border: none;"><strong>CARTON NUMBER:</strong></td>
					</tr>
					<tr>
						<td colspan="4"style="border: none;">&nbsp;</td>
						<td style="border: none;">&nbsp;</td>
						<td   colspan="2" style="border: none;"><strong>CARTON MEASURMENT:</strong></td>
					</tr>
		</table>

	<?
		$html = ob_get_contents();
		ob_clean();
		foreach (glob("tb*.xls") as $filename) {
		@unlink($filename);
		}
		//---------end------------//
		$name=time();
		$filename="tb".$user_id."_".$name.".xls";
		$create_new_doc = fopen($filename, 'w');
		$is_created = fwrite($create_new_doc, $html);
		echo "$filename####$html";
	exit();	

	



} 

if($action=="invoice_report_print_7")  //print 7
{
	extract($_REQUEST);
	// $update_id=$data;
	$company_arr = return_library_array("select id, company_name from lib_company ","id","company_name");
	$buyer_name=return_library_array("select id, buyer_name from lib_buyer where status_active = 1 and is_deleted = 0","id","buyer_name");
	$brand_arr=return_library_array( "SELECT ID, BRAND_NAME FROM LIB_BUYER_BRAND BRAND",'ID','BRAND_NAME');
	$applicant_sql=sql_select( "SELECT A.ID, A.BUYER_NAME, A.SHORT_NAME, A.ADDRESS_1 FROM LIB_BUYER A");
	foreach($applicant_sql as $row)
	{
		$buyer_name_arr[$row["ID"]]["BUYER_NAME"]=$row["BUYER_NAME"];
		$buyer_name_arr[$row["ID"]]["ADDRESS_1"]=$row["ADDRESS_1"];
	}
	$bank_sql=sql_select( "SELECT A.ID, A.BANK_NAME, A.BRANCH_NAME, A.ADDRESS, A.SWIFT_CODE FROM LIB_BANK A");
	foreach($bank_sql as $row)
	{
		$bank_name_arr[$row["ID"]]["BANK_NAME"]=$row["BANK_NAME"];
		$bank_name_arr[$row["ID"]]["BRANCH_NAME"]=$row["BRANCH_NAME"];
		$bank_name_arr[$row["ID"]]["ADDRESS"]=$row["ADDRESS"];
		$bank_name_arr[$row["ID"]]["SWIFT_CODE"]=$row["SWIFT_CODE"];
	}
	$bank_account_sql=sql_select( "SELECT ID, ACCOUNT_ID, ACCOUNT_TYPE, ACCOUNT_NO FROM LIB_BANK_ACCOUNT WHERE IS_DELETED=0 ");
	foreach($bank_account_sql as $row)
	{
		$bank_acc_arr[$row["ACCOUNT_ID"]][$row["ACCOUNT_TYPE"]]["ACCOUNT_NO"]=$row["ACCOUNT_NO"];
	}
	$inv_master_data=sql_select("SELECT ID, BENIFICIARY_ID, BUYER_ID, LOCATION_ID, INVOICE_NO, INVOICE_DATE, EXP_FORM_NO, EXP_FORM_DATE, IS_LC, LC_SC_ID, BL_NO, FEEDER_VESSEL, INCO_TERM, INCO_TERM_PLACE, SHIPPING_MODE, PORT_OF_ENTRY, PORT_OF_LOADING, PORT_OF_DISCHARGE, MAIN_MARK, SIDE_MARK, CARTON_NET_WEIGHT, CARTON_GROSS_WEIGHT, CBM_QNTY, PLACE_OF_DELIVERY, DELV_NO, CONSIGNEE, NOTIFYING_PARTY, ITEM_DESCRIPTION, DISCOUNT_AMMOUNT, BONUS_AMMOUNT, COMMISSION, TOTAL_CARTON_QNTY, BL_DATE, HS_CODE, MOTHER_VESSEL, CATEGORY_NO, FORWARDER_NAME, ETD,CO_NO, TOTAL_MEASURMENT, INVOICE_VALUE, NET_INVO_VALUE, CONTAINER_NO, SEAL_NO, ETD, COUNTRY_ID,COMMISSION_PERCENT,BL_REV_DATE,UD_NO,COMPOSITION,DISCOUNT_IN_PERCENT FROM COM_EXPORT_INVOICE_SHIP_MST WHERE ID=$update_id");
	echo" ";
	$id=$inv_master_data[0]["ID"];
	$benificiary_id=$inv_master_data[0]["BENIFICIARY_ID"];
	$buyer_id=$inv_master_data[0]["BUYER_ID"];
	$location_id=$inv_master_data[0]["LOCATION_ID"];
	$invoice_no=$inv_master_data[0]["INVOICE_NO"];
	$invoice_date=change_date_format($inv_master_data[0]["INVOICE_DATE"]);
	$bl_rev_date=$inv_master_data[0]["BL_REV_DATE"];
	$exp_form_no=$inv_master_data[0]["EXP_FORM_NO"];
	$exp_form_date=change_date_format($inv_master_data[0]["EXP_FORM_DATE"]);
	$is_lc=$inv_master_data[0]["IS_LC"];
	$lc_sc_id=$inv_master_data[0]["LC_SC_ID"];
	$bl_no=$inv_master_data[0]["BL_NO"];
	$feeder_vessel=$inv_master_data[0]["FEEDER_VESSEL"];
	$inco_term=$inv_master_data[0]["INCO_TERM"];
	$inco_term_place=$inv_master_data[0]["INCO_TERM_PLACE"];
	$shipping_mode=$inv_master_data[0]["SHIPPING_MODE"];
	$port_of_entry=$inv_master_data[0]["PORT_OF_ENTRY"];
	$port_of_loading=$inv_master_data[0]["PORT_OF_LOADING"];
	$port_of_discharge=$inv_master_data[0]["PORT_OF_DISCHARGE"];
	$main_mark=$inv_master_data[0]["MAIN_MARK"];
	$side_mark=$inv_master_data[0]["SIDE_MARK"];
	$net_weight=$inv_master_data[0]["CARTON_NET_WEIGHT"];
	$gross_weight=$inv_master_data[0]["CARTON_GROSS_WEIGHT"];
	$cbm_qnty=$inv_master_data[0]["CBM_QNTY"];
	$place_of_delivery=$inv_master_data[0]["PLACE_OF_DELIVERY"];
	$delv_no=$inv_master_data[0]["DELV_NO"];
	$consignee=$inv_master_data[0]["CONSIGNEE"];
	$notifying_party=$inv_master_data[0]["NOTIFYING_PARTY"];
	$item_description=$inv_master_data[0]["ITEM_DESCRIPTION"];
	$discount_ammount=$inv_master_data[0]["DISCOUNT_AMMOUNT"];
	$bonus_ammount=$inv_master_data[0]["BONUS_AMMOUNT"];
	$commission=$inv_master_data[0]["COMMISSION"];
	$commission_percent=$inv_master_data[0]["COMMISSION_PERCENT"];
	$total_carton_qnty=$inv_master_data[0]["TOTAL_CARTON_QNTY"];
	$bl_date=$inv_master_data[0]["BL_DATE"];
	$hs_code=$inv_master_data[0]["HS_CODE"];
	$mother_vessel=$inv_master_data[0]["MOTHER_VESSEL"];
	$category_no=$inv_master_data[0]["CATEGORY_NO"];
	$forwarder_name=$inv_master_data[0]["FORWARDER_NAME"];
	$co_no=$inv_master_data[0]["CO_NO"];
	$total_measurment=$inv_master_data[0]["TOTAL_MEASURMENT"];
	$net_invo_value=$inv_master_data[0]["NET_INVO_VALUE"];
	$container_no=$inv_master_data[0]["CONTAINER_NO"];
	$seal_no=$inv_master_data[0]["SEAL_NO"];
	$etd=change_date_format($inv_master_data[0]["ETD"]);
	$inv_country_id=$inv_master_data[0]["COUNTRY_ID"];
	$total_discount=$inv_master_data[0]["INVOICE_VALUE"]-$inv_master_data[0]["NET_INVO_VALUE"];
	$ud_no= $inv_master_data[0]["UD_NO"];
	$composition=$inv_master_data[0]["COMPOSITION"];
	$discount_in_percent=$inv_master_data[0]["DISCOUNT_IN_PERCENT"];
	
	$itemIdArr=array();
	$setQtyArr=array();
	$poIdArr=array();
	$dtls_sql="SELECT A.ID AS DTLS_ID, A.PO_BREAKDOWN_ID,C.TOTAL_SET_QNTY FROM  COM_EXPORT_INVOICE_SHIP_DTLS A,  WO_PO_BREAK_DOWN B, WO_PO_DETAILS_MASTER C WHERE A.PO_BREAKDOWN_ID=B.ID AND B.JOB_NO_MST=C.JOB_NO AND A.CURRENT_INVOICE_QNTY>0 AND A.STATUS_ACTIVE=1 AND A.IS_DELETED=0 AND A.MST_ID=$update_id";
	$PO_agent=sql_select($dtls_sql);
	foreach($PO_agent as $row){
		$poIdArr[]=$row['PO_BREAKDOWN_ID'];
		$setQtyArr[$row['PO_BREAKDOWN_ID']]=$row['TOTAL_SET_QNTY'];
	}
	

	$ex_fac_arr=array();
	$sqlExfac=sql_select("SELECT A.ID,A.SYS_NUMBER, B.EX_FACTORY_QNTY,B.PO_BREAK_DOWN_ID,B.TOTAL_CARTON_QNTY FROM PRO_EX_FACTORY_DELIVERY_MST A,PRO_EX_FACTORY_MST B WHERE A.ID=B.DELIVERY_MST_ID AND B.PO_BREAK_DOWN_ID IN(".implode(",",$poIdArr).")");
	foreach($sqlExfac as $row)
	{
		$exfac_qty+=$row['EX_FACTORY_QNTY'];
		$carton_qty+=$row['TOTAL_CARTON_QNTY'];
		$ex_fac_arr[$row['PO_BREAK_DOWN_ID']]['TOTAL_CARTON_QNTY']=$row['TOTAL_CARTON_QNTY'];
	}

	$agent_id="";
	// $fristPo=array_shift($poIdArr);
	$sql_fabric=sql_select("SELECT B.ID, C.CONSTRUCTION, C.COMPOSITION FROM WO_PO_BREAK_DOWN B, WO_PRE_COST_FABRIC_COST_DTLS C WHERE B.JOB_NO_MST=C.JOB_NO AND B.ID IN(".implode(",",$poIdArr).")");

	foreach($sql_fabric as $row_fabric){
		$fabric_info[$row_fabric["ID"]]=$row_fabric["CONSTRUCTION"]." ".$row_fabric["COMPOSITION"];
	}
	
	if($is_lc==1)
	{
		$lc_sc_data=sql_select("SELECT ID, EXPORT_LC_NO, LC_DATE, NOTIFYING_PARTY, CONSIGNEE, ISSUING_BANK_NAME, NEGOTIATING_BANK, LIEN_BANK, PAY_TERM, APPLICANT_NAME,INCO_TERM,LIEN_BANK,NOMINATED_SHIPP_LINE, BUYER_NAME, TENOR,SHIPPING_MODE FROM COM_EXPORT_LC WHERE ID='".$lc_sc_id."' ");
		foreach($lc_sc_data as $row)
		{
			$lc_sc_id=$row["ID"];
			$lc_sc_no=$row["EXPORT_LC_NO"];
			$lc_sc_date=change_date_format($row["LC_DATE"]);
			$notifying_party=$row["NOTIFYING_PARTY"];
			$consignee=$row["CONSIGNEE"];
			$issuing_bank_name=$row["ISSUING_BANK_NAME"];
			$negotiating_bank=$row["LIEN_BANK"];
			$pay_term_id=$row["PAY_TERM"];
			$applicant_name=$row["APPLICANT_NAME"];
			$buyer_name=$row["BUYER_NAME"];
			$inco_term=$row["INCO_TERM"];
			$lien_bank=$row["LIEN_BANK"];
			$shipping_line=$row["NOMINATED_SHIPP_LINE"];
			$negotiating_bank_text=$row["NEGOTIATING_BANK"];
			$tenor=$row["TENOR"];
			$shipping_mode=$row["SHIPPING_MODE"];
		}
		
			$cate_hs_sql=sql_select("SELECT WO_PO_BREAK_DOWN_ID, FABRIC_DESCRIPTION, CATEGORY_NO, HS_CODE FROM COM_EXPORT_LC_ORDER_INFO WHERE COM_EXPORT_LC_ID='".$lc_sc_id."'");
			foreach($cate_hs_sql as $row)
			{
				$order_la_data[$row["WO_PO_BREAK_DOWN_ID"]]["CATEGORY_NO"]=$row["CATEGORY_NO"];
				$order_la_data[$row["WO_PO_BREAK_DOWN_ID"]]["HS_CODE"]=$row["HS_CODE"];
			    $all_order_id[$row["WO_PO_BREAK_DOWN_ID"]]=$row["WO_PO_BREAK_DOWN_ID"];
			}
	}
	else
	{
		$lc_sc_data=sql_select("SELECT ID, CONTRACT_NO, CONTRACT_DATE, NOTIFYING_PARTY, CONSIGNEE, LIEN_BANK, PAY_TERM, APPLICANT_NAME,INCO_TERM,LIEN_BANK,SHIPPING_LINE,BUYER_NAME, TENOR,SHIPPING_MODE FROM COM_SALES_CONTRACT WHERE ID='".$lc_sc_id."'  AND STATUS_ACTIVE=1");
		foreach($lc_sc_data as $row)
		{
			$lc_sc_id=$row["ID"];
			$Sc_no=$row["CONTRACT_NO"];
			$Sc_date=change_date_format($row["CONTRACT_DATE"]);
			$notifying_party=$row["NOTIFYING_PARTY"];
			$consignee=$row["CONSIGNEE"];
			$issuing_bank_name="";
			$negotiating_bank=$row["LIEN_BANK"];
			$pay_term_id=$row["PAY_TERM"];
			$applicant_name=$row["APPLICANT_NAME"];
			$buyer_name=$row["BUYER_NAME"];
			$inco_term=$row["INCO_TERM"];
			$lien_bank=$row["LIEN_BANK"];
			$shipping_line=$row["SHIPPING_LINE"];
			$tenor=$row["TENOR"];
			$shipping_mode=$row["SHIPPING_MODE"];
			$negotiating_bank_text="";
		}
		
		$cate_hs_sql=sql_select("SELECT WO_PO_BREAK_DOWN_ID, FABRIC_DESCRIPTION, CATEGORY_NO, HS_CODE FROM COM_SALES_CONTRACT_ORDER_INFO WHERE COM_SALES_CONTRACT_ID='".$lc_sc_id."' AND STATUS_ACTIVE=1");
		foreach($cate_hs_sql as $row)
		{
			$order_la_data[$row["WO_PO_BREAK_DOWN_ID"]]["CATEGORY_NO"]=$row["CATEGORY_NO"];
			$order_la_data[$row["WO_PO_BREAK_DOWN_ID"]]["HS_CODE"]=$row["HS_CODE"];
			$order_la_data[$row["WO_PO_BREAK_DOWN_ID"]]["FABRIC_DESCRIPTION"]=$row["FABRIC_DESCRIPTION"];
			$all_order_id[$row["WO_PO_BREAK_DOWN_ID"]]=$row["WO_PO_BREAK_DOWN_ID"];
		}
	}
	
	$company_name_sql=sql_select( "SELECT ID, COMPANY_NAME, PLOT_NO, LEVEL_NO, ROAD_NO, BLOCK_NO, CITY, COUNTRY_ID,ERC_NO,EMAIL,CONTACT_NO,REX_NO,REX_REG_DATE,IRC_NO,VAT_NUMBER FROM LIB_COMPANY WHERE ID ='$benificiary_id'");
	foreach($company_name_sql as $row)
	{
		$company_name=$row["COMPANY_NAME"];
		$plot_no=$row["PLOT_NO"];
		$level_no=$row["LEVEL_NO"];
		$road_no=$row["ROAD_NO"];
		$block_no=$row["BLOCK_NO"];
		$city=$row["CITY"];
		$country_id=$row["COUNTRY_ID"];
		$erc_no=$row["ERC_NO"];
		$contact_no=$row["CONTACT_NO"];
		$email=$row["EMAIL"];
		$rex_no=$row["REX_NO"];
		$rex_reg_date=$row["REX_REG_DATE"];
		$irc_no=$row["IRC_NO"];
		$vat_number=$row["VAT_NUMBER"];
	}
	
	$country_name_arr=return_library_array( "SELECT ID, COUNTRY_NAME FROM LIB_COUNTRY",'ID','COUNTRY_NAME');
	// $carrier=$SupplierArr[$forwarder_name];
	$applicant=$buyer_name_arr[$applicant_name]["BUYER_NAME"];
	$buyer=$buyer_name_arr[$buyer_name]["BUYER_NAME"];
	$applicantAddress=$buyer_name_arr[$applicant_name]["ADDRESS_1"];
	$agent=$buyer_name_arr[$agent_id]["BUYER_NAME"];
	$agentAddress=$buyer_name_arr[$agent_id]["ADDRESS_1"];
		
	$dtls_sql="SELECT A.ID AS DTLS_ID, A.PO_BREAKDOWN_ID, A.CURRENT_INVOICE_RATE, A.CURRENT_INVOICE_QNTY, A.CURRENT_INVOICE_VALUE, B.PO_NUMBER, C.STYLE_REF_NO, C.GMTS_ITEM_ID, C.ORDER_UOM, C.GMTS_ITEM_ID,C.STYLE_DESCRIPTION, C.BRAND_ID FROM  COM_EXPORT_INVOICE_SHIP_DTLS A,  WO_PO_BREAK_DOWN B, WO_PO_DETAILS_MASTER C WHERE A.PO_BREAKDOWN_ID=B.ID AND B.JOB_NO_MST=C.JOB_NO AND A.CURRENT_INVOICE_QNTY>0 AND A.STATUS_ACTIVE=1 AND A.IS_DELETED=0 AND A.MST_ID=$update_id";
	//echo $dtls_sql; die();
	$result=sql_select($dtls_sql);

	$sql="SELECT B.ID AS ID, B.LOCATION_NAME || ' (' || A.COMPANY_SHORT_NAME || ')' AS LOCATION_NAME  FROM LIB_COMPANY A, LIB_LOCATION B WHERE A.ID=B.COMPANY_ID AND B.STATUS_ACTIVE =1 AND B.IS_DELETED=0 AND A.STATUS_ACTIVE =1 AND A.IS_DELETED=0 ORDER BY B.LOCATION_NAME";

	$location_dtls_sql = "SELECT A.COMPANY_NAME , B.LOCATION_NAME , B.ADDRESS , B.EMAIL,B.COUNTRY_ID FROM  LIB_COMPANY A ,LIB_LOCATION B WHERE A.ID=B.COMPANY_ID AND B.STATUS_ACTIVE =1 AND B.IS_DELETED=0 AND A.STATUS_ACTIVE =1 AND A.IS_DELETED=0  AND B.ID = $location_id";
	//echo $location_dtls_sql;
	$location_dtls = sql_select($location_dtls_sql);
	foreach($location_dtls as $row)
	{
		$ex_company_name=$row['COMPANY_NAME'];
		$ex_location_name=$row['LOCATION_NAME'];
		$ex_address=$row['ADDRESS'];
		$ex_country_id=$row[csf("COUNTRY_ID")];
		$ex_email=$row["EMAIL"];
	}

	ob_start();
	?>
	<style type="text/css" media="print">
			@page { 
				size: auto;
			} 
			
			thead {
		display: table-row-group;
	}
	</style>
       <table width='1040' cellspacing="0" cellpadding="0" border="" >
	       <tr>
				<td colspan="7" valign="top" align="center" style="font-size:26px; font-weight:bold;border:none"><b><?
				$text = $company_arr[$benificiary_id];
				// Convert the string to uppercase
				$uppercaseText = strtoupper($text);
				echo $uppercaseText?></b></td>	
            </tr>
			<tr>
				<td colspan="7" valign="top" align="center" style="font-size:18px; font-weight:bold;border:none">
					<b> <?php
					if($city!="")  $company_details.= "".$city.", ";
					if($country_id!="")  $company_details.="".$country_name_arr[$country_id]."";  
				
					echo  $company_details;
					?>
				</b>
				</td>
			</tr>
			 <tr>
				<td colspan="7" valign="top" align="center" style="border:none;font-size:20px;text-decoration:underline; font-weight:bold"><b>COMMERCIAL INVOICE</b></td>
			</tr>
		</table>
		<br>
 
		<table border="1" width='1040' cellspacing="0" cellpadding="0" style="border-left: 2px solid black;border-right:1px solid black">

				<tr>
					<td style="border-bottom:none;border-left:1px solid black"><strong>For Account and Risk of Messrs:</strong></td>
					<td colspan="2"style="border-bottom:none;border-top:1px solid black;border-right:none;border-left:1px solid black"><strong>INVOICE NO:</strong>&nbsp;<?echo $invoice_no?> </td>
					<td colspan="4"style="border:none;border-top:1px solid black;border-right:1px solid black">Date :&nbsp;<?echo $invoice_date?></td>
					
					
				</tr>
				<tr>
					<td rowspan="4" valign="top"style="border-top:none; word-wrap: break-word;">C & A BUYING GmbH & Co. KG. <br>
					WANHEIMERSTR 70,<br>
					40468 DUSSELDORF,
					<br>GERMANY.</td>
					<td colspan="2"style="border: none;border-left:1px solid black"><strong>EXP NO :</strong>&nbsp;<?echo $exp_form_no?></td>
					<td colspan="4"style="border:none;border-right:1px solid black">Date :&nbsp;<?echo $exp_form_date?></td>
				
					
				</tr>
				<tr>
					
					<td colspan="2"style="border: none;border-left:1px solid black">CONTRACT NO : &nbsp;<?echo $lc_sc_no?></td>
					<td colspan="4"style="border: none; border-right:1px solid black">Date :&nbsp;<?echo $lc_sc_date?></td>
				</tr>
				<tr><td colspan="6"style="border: none;border-left:1px solid black;border-right:1px solid black;"><strong>BUYER'S BANK : &nbsp;</strong>DEUTSCHE BANK AG,</td></tr>
				<tr>
					<td colspan="6"style="border: none;border-left:1px solid black;border-right:1px solid black">			
                     SINGAPORE BRANCH, SINGAPORE.			

					</td>
				</tr>
				<tr>
					<td style="border-bottom:none;border-left:1px solid black;"><strong>Notify Party:</strong></td>
					<td colspan="6"style="border: none;border-left:1px solid black;border-right:1px solid black"><strong>NEGOTIATING BANK :</strong> <?
					echo $bank_name_arr[$lien_bank]["BANK_NAME"]."(".$bank_name_arr[$lien_bank]["BRANCH_NAME"].")"."<br>";
					echo $bank_name_arr[$lien_bank]["ADDRESS"];
					?></td>
				</tr>

				<tr>
				<td rowspan="6" valign="top" style="border:none;border-left:1px solid black;border-right:1px solid black">C & A BUYING GmbH & Co. KG. <br>		
									WANHEIMERSTR 70,<br>			
									40468 DUSSELDORF,<br> 			
									GERMANY.			</td>
				<td colspan="6"style="border: none;border-bottom:1px solid black;border-left:1px solid black"></td>
				</tr>
				<tr>
					<td style="border: none;border-left:1px solid black;" colspan="2"><strong>TEREMS OF DELIVERY </strong></td>
					<td colspan="4" style="border: none;border-right:1px solid black">: <? echo $incoterm[$inv_master_data[0][csf("inco_term")]]?></td>
				</tr>
				<tr>
					<td style="border: none;border-left:1px solid black"colspan="2"><strong>COUNTRY OF ORIGIN </strong></td>
					<td colspan="4"style="border: none;border-right:1px solid black">:   BANGLADESH</td>
				</tr>
				<tr>
					<td style="border: none;border-left:1px solid black"colspan="2"><strong>SHIPMENT MODE</strong></td>
					<td colspan="4"style="border: none;border-right:1px solid black">:   <? echo strtoupper($shipment_mode[$shipping_mode]);?></td>
				</tr>
				<tr>
					<td colspan="6"style="border: none;border-left:1px solid black;border-right:1px solid black"><strong>CONTAINER NO :&nbsp;</strong>
					<!-- <? echo $container_no?>  -->
				</td>
					
				</tr>
		</table>
		<table width='1040' cellspacing="0" cellpadding="0" border="1" style="border-left: 2px solid black;border-right:1px solid black">
			<tr>
				<th>Vessel/Flight:</th>
				<th>Port of Loading	</th>
				<th>Final Destination</th>
				<th colspan="4">Departure Date:</th>
			</tr>
			<tr>
				<td align="center"><?echo$feeder_vessel?></td>
				<td align="center"><?echo $port_of_loading?></td>
				<td align="center"><? echo $port_of_discharge?></td>
				<td colspan="4" align="center"><?echo $etd?></td>
			</tr>
				
		</table>
	<table width='1040' cellspacing="0" cellpadding="0" border="1" style="border-left: 2px solid black;border-right:1px solid black">  
			<tr style="font-size:small; font-weight:bold" align="center">
				<td ><P>Marks & Number </P> </td>
				<td ><P>Description of Goods</P> </td>
				<td colspan="2"><P>Quantity</P> </td>
				<td colspan="2"><P>	Unit Price(USD)	</P>  </td>
				<td><P>Amount(USD)</P> </td>
			</tr>

		<?
		$po_arr=array();
		$data_arr=array();
		foreach($result as $row)
		{
			$po_no .= $row["PO_NUMBER"].',';
			$style_ref .= $row["STYLE_REF_NO"].',';
			$po_arr[$row["STYLE_REF_NO"]][$row["PO_BREAKDOWN_ID"]] .= $row["PO_NUMBER"]. "/";
			$data_arr[$row["STYLE_REF_NO"]][$row["PO_BREAKDOWN_ID"]]["PO_NUMBER"]=$row["PO_NUMBER"];
			$data_arr[$row["STYLE_REF_NO"]][$row["PO_BREAKDOWN_ID"]]["CURRENT_INVOICE_QNTY"]+=$row["CURRENT_INVOICE_QNTY"];
			$data_arr[$row["STYLE_REF_NO"]][$row["PO_BREAKDOWN_ID"]]["PO_BREAKDOWN_ID"]=$row["PO_BREAKDOWN_ID"];
			$data_arr[$row["STYLE_REF_NO"]][$row["PO_BREAKDOWN_ID"]]["ORDER_UOM"][$unit_of_measurement[$row["ORDER_UOM"]]]=$unit_of_measurement[$row["ORDER_UOM"]];
			$data_arr[$row["STYLE_REF_NO"]][$row["PO_BREAKDOWN_ID"]]["CURRENT_INVOICE_VALUE"]+=$row["CURRENT_INVOICE_VALUE"];
		}
		

		// $all_po = ltrim(implode(",", array_unique(explode(",", chop($po_no, ",")))), ',');
		//  $all_style_ref = ltrim(implode(",", array_unique(explode(",", chop($style_ref, ",")))), ',');
		?>

		<?
		
		$row_span = 0;
		$style_span = array();
		foreach($data_arr as $STYLE_REF_NO => $style_data)
		{
			foreach($style_data as $PO_BREAKDOWN_ID => $row)
			{
				$row_span++;
				$style_span[$STYLE_REF_NO]++;
			}
			$row_span++;
			//$row_span++;
		}
		$i=1;
		foreach($data_arr as $STYLE_REF_NO => $style_data)
		{
			$st_span = 0;
			?>
				<tr style="font-size:small;">
					<?
					if($i==1)
					{
						?>
						<td rowspan="<?=$row_span;?>" width="300" valign="top" style="border-bottom:none" >
						<p style="text-align:left; display:inline-block; position: relative;">
							C & A <?echo "........."?>
							<br>		
							TYPE OF PACK:	
							<br>	
							QUANTITY IN CARTON:	
							<br>	
							CARTON NUMBER :	
							<br>	
							SIZE :	
							<br>	
							COLOUR:	</p>			
						</td>
						<?
					} 
					?>
					
					<td width="830" colspan="6" valign="top" style=""><strong>STYLE NO :&nbsp;</strong><?echo  $STYLE_REF_NO?>
				    <br>
				     <strong>ORDER NO :</strong>
				</td>
					
				</tr>
				<?
			foreach($style_data as $PO_BREAKDOWN_ID => $row)
			{
				?>
				<tr style="font-size:small">
					<td width="300" valign="top"  > 
						<?echo $row['PO_NUMBER'];?>
					</td>
					<td width="140" align="right"> <p><? echo number_format($row['CURRENT_INVOICE_QNTY']*$setQtyArr[$PO_BREAKDOWN_ID],0,".",",");  ?> </p></td>
					<td width="45" align="right"><?=implode(",",$row["ORDER_UOM"]); ?></td>
					<td width="140" align="right"><p><?  echo "$".number_format(($row["CURRENT_INVOICE_VALUE"]/($row['CURRENT_INVOICE_QNTY']*$setQtyArr[$PO_BREAKDOWN_ID])),2); ?></p></td>
					<td width="45" align="right">/PCS</td>
					<td width="160" align="right"><p><? echo "$".number_format($row["CURRENT_INVOICE_VALUE"],2,".",","); ?></p></td>
				</tr>
				
				<?
				$total_rate+=$row["CURRENT_INVOICE_VALUE"]/$row['CURRENT_INVOICE_QNTY']*$setQtyArr[$PO_BREAKDOWN_ID];
				$total_value+=$row["CURRENT_INVOICE_VALUE"];
				$total_qnty+=$row["CURRENT_INVOICE_QNTY"]*$setQtyArr[$PO_BREAKDOWN_ID];
				$last_uom=$unit_of_measurement[$row['ORDER_UOM']];
				$total_po_carton_qnty+=$carton_arr[$PO_BREAKDOWN_ID];
				$i++;
			}
		}
		
		?>
			<tr>
				<td style="border-top:none;">&nbsp; </td>
				<td colspan="6" >
					<? echo $item_description;?>
					<br>
					<? echo $composition;?><br>
					HS CODE :  <? echo $hs_code;?>  <br>
					CAT :     <?echo $category_no;?> <br>
					CTNS : <? echo $total_carton_qnty;?>
				</td>
		</tr>
	
		<tr>
			<td colspan="2" align="right"><b>Total</b></td>
			<td align="right"><b><? echo number_format($total_qnty,0,".",",") ?></b></td>
			<td align="right"><b>PCS</b></td>
			<!-- <td align="right" > <b><? echo "$&nbsp;".number_format($total_rate,2,".",","); ?></b></td> -->
			<td>&nbsp;</td>
			<td>&nbsp;</td>
			<td align="right"> <b><? echo "$&nbsp;".number_format($total_value,2,".",","); ?></b></td>
		</tr>
		
		
	</table>
	
	<table width='1040' cellspacing="0" cellpadding="0" border="1">
	<tr>
			<td colspan="6">LESS 3%</td>
			<td align="right"><?$less=($total_value*3)/100; echo "$".$less?></td>

		</tr>
		<tr>
		<td colspan="6">DN Adjust		<? echo $discount_in_percent."%";?></td>
		<td align="right"><? $dn_adjust=($total_value*$discount_in_percent)/100;echo "$".$dn_adjust?></td>
	   </tr>
		<tr>
			<td colspan="6"><b>Net Payable</b></td>
			<td align="right"><?$net_payable=$total_value-($less+$dn_adjust);echo "$".number_format($net_payable,2)?></td>
	   </tr>

		<tr>
			<td colspan="7"><b>SAY US DOLLARS : <? echo number_to_words(def_number_format($net_payable,2,""),"USD", "CENTS")." ONLY";?></b></td>
		</tr>
	</table>
      <table width='300' cellspacing="0" cellpadding="0" border="1" style="margin-left:40px;margin-top: 20px;">
					<tr>
						<td>TTL PCS</td>
						<td align="right"><? echo  number_format($total_qnty,0,".",",")?> </td>
						<td>PCS</td>
				   </tr>
					<tr>
						<td>CTN.</td>
						<td align="right"><? echo $total_carton_qnty;?></td>
						<td>CTNS</td>
					</tr>
					<tr>
						<td>TTL NET WT</td>
						<td align="right"><?echo number_format($net_weight,2,".",",");?></td>
						<td>KGS</td>
					</tr>
					<tr>
						<td>TTL GR  WT </td>
						<td align="right"><? echo number_format($gross_weight,2,".",",");?></td>
						<td>KGS</td>
					</tr>
					<tr>
						<td>CBM</td>
						<td align="right"><?echo $cbm_qnty; ?></td>
						<td>CBM</td>
					</tr>
		</table>

	<?
		$html = ob_get_contents();
		ob_clean();
		foreach (glob("tb*.xls") as $filename) {
		@unlink($filename);
		}
		//---------end------------//
		$name=time();
		$filename="tb".$user_id."_".$name.".xls";
		$create_new_doc = fopen($filename, 'w');
		$is_created = fwrite($create_new_doc, $html);
		echo "$filename####$html";
	exit();	
} 

if($action=="invoice_report_print_8")  //print 8
{
	extract($_REQUEST);
	// $update_id=$data;
 
	$brand_arr=return_library_array( "SELECT ID, BRAND_NAME FROM LIB_BUYER_BRAND BRAND",'ID','BRAND_NAME');
	$issue_bank_arr=return_library_array( "SELECT ID, BANK_NAME FROM LIB_BANK BRAND",'ID','BANK_NAME');
	$brand_arr=return_library_array( "SELECT ID, BRAND_NAME FROM LIB_BUYER_BRAND BRAND",'ID','BRAND_NAME');
	$applicant_sql=sql_select( "SELECT A.ID, A.BUYER_NAME, A.SHORT_NAME, A.ADDRESS_1 FROM LIB_BUYER A");

	foreach($applicant_sql as $row)
	{
		$buyer_name_arr[$row["ID"]]["BUYER_NAME"]=$row["BUYER_NAME"];
		$buyer_name_arr[$row["ID"]]["ADDRESS_1"]=$row["ADDRESS_1"];
	}
	$bank_sql=sql_select( "SELECT A.ID, A.BANK_NAME, A.BRANCH_NAME, A.ADDRESS, A.SWIFT_CODE FROM LIB_BANK A");
	foreach($bank_sql as $row)
	{
		$bank_name_arr[$row["ID"]]["BANK_NAME"]=$row["BANK_NAME"];
		$bank_name_arr[$row["ID"]]["BRANCH_NAME"]=$row["BRANCH_NAME"];
		$bank_name_arr[$row["ID"]]["ADDRESS"]=$row["ADDRESS"];
		$bank_name_arr[$row["ID"]]["SWIFT_CODE"]=$row["SWIFT_CODE"];
	}
	$bank_account_sql=sql_select( "SELECT ID, ACCOUNT_ID, ACCOUNT_TYPE, ACCOUNT_NO FROM LIB_BANK_ACCOUNT WHERE IS_DELETED=0 ");
	foreach($bank_account_sql as $row)
	{
		$bank_acc_arr[$row["ACCOUNT_ID"]][$row["ACCOUNT_TYPE"]]["ACCOUNT_NO"]=$row["ACCOUNT_NO"];
	}
	$inv_master_data=sql_select("SELECT id, benificiary_id, buyer_id, location_id, invoice_no, invoice_date, exp_form_no, exp_form_date, is_lc, lc_sc_id, bl_no, feeder_vessel, inco_term, inco_term_place, shipping_mode, port_of_entry, port_of_loading, port_of_discharge, main_mark, side_mark, carton_net_weight, carton_gross_weight, cbm_qnty, place_of_delivery, delv_no, consignee, notifying_party, item_description, discount_ammount, bonus_ammount, commission, total_carton_qnty, bl_date, hs_code, mother_vessel, category_no, forwarder_name, etd,co_no, total_measurment, invoice_value, net_invo_value, container_no, seal_no, etd, country_id,commission_percent,bl_rev_date, eta_destination_place, etd_destination from com_export_invoice_ship_mst where id=$update_id");
	
	$id=$inv_master_data[0]["ID"];
	$benificiary_id=$inv_master_data[0]["BENIFICIARY_ID"];
	$buyer_id=$inv_master_data[0]["BUYER_ID"];
	$location_id=$inv_master_data[0]["LOCATION_ID"];
	$invoice_no=$inv_master_data[0]["INVOICE_NO"];
	$invoice_date=$inv_master_data[0]["INVOICE_DATE"];
	$bl_rev_date=$inv_master_data[0]["BL_REV_DATE"];
	$eta_destination_place=$inv_master_data[0]["ETA_DESTINATION_PLACE"];
	$etd_destination=$inv_master_data[0]["ETD_DESTINATION"];
	$is_lc=$inv_master_data[0]["IS_LC"];
	$lc_sc_id=$inv_master_data[0]["LC_SC_ID"];
	$bl_no=$inv_master_data[0]["BL_NO"];
	$feeder_vessel=$inv_master_data[0]["FEEDER_VESSEL"];
	$inco_term=$inv_master_data[0]["INCO_TERM"];
	$inco_term_place=$inv_master_data[0]["INCO_TERM_PLACE"];
	$shipping_mode=$inv_master_data[0]["SHIPPING_MODE"];
	$port_of_entry=$inv_master_data[0]["PORT_OF_ENTRY"];
	$port_of_loading=$inv_master_data[0]["PORT_OF_LOADING"];
	$port_of_discharge=$inv_master_data[0]["PORT_OF_DISCHARGE"];
	$main_mark=$inv_master_data[0]["MAIN_MARK"];
	$side_mark=$inv_master_data[0]["SIDE_MARK"];
	$net_weight=$inv_master_data[0]["CARTON_NET_WEIGHT"];
	$gross_weight=$inv_master_data[0]["CARTON_GROSS_WEIGHT"];
	$cbm_qnty=$inv_master_data[0]["CBM_QNTY"];
	$place_of_delivery=$inv_master_data[0]["PLACE_OF_DELIVERY"];
	$delv_no=$inv_master_data[0]["DELV_NO"];
	$consignee=$inv_master_data[0]["CONSIGNEE"];
	$notifying_party=$inv_master_data[0]["NOTIFYING_PARTY"];
	$item_description=$inv_master_data[0]["ITEM_DESCRIPTION"];
	$discount_ammount=$inv_master_data[0]["DISCOUNT_AMMOUNT"];
	$bonus_ammount=$inv_master_data[0]["BONUS_AMMOUNT"];
	$commission=$inv_master_data[0]["COMMISSION"];
	$commission_percent=$inv_master_data[0]["COMMISSION_PERCENT"];
	$total_carton_qnty=$inv_master_data[0]["TOTAL_CARTON_QNTY"];
	$bl_date=$inv_master_data[0]["BL_DATE"];
	$hs_code=$inv_master_data[0]["HS_CODE"];
	$mother_vessel=$inv_master_data[0]["MOTHER_VESSEL"];
	$category_no=$inv_master_data[0]["CATEGORY_NO"];
	$forwarder_name=$inv_master_data[0]["FORWARDER_NAME"];
	$etd=$inv_master_data[0]["ETD"];
	$co_no=$inv_master_data[0]["CO_NO"];
	$total_measurment=$inv_master_data[0]["TOTAL_MEASURMENT"];
	$net_invo_value=$inv_master_data[0]["NET_INVO_VALUE"];
	$container_no=$inv_master_data[0]["CONTAINER_NO"];
	$seal_no=$inv_master_data[0]["SEAL_NO"];
	$etd=$inv_master_data[0]["ETD"];
	$inv_country_id=$inv_master_data[0]["COUNTRY_ID"];

	$company_name_sql=sql_select("SELECT ID, COMPANY_NAME, PLOT_NO, LEVEL_NO, ROAD_NO, BLOCK_NO, CITY, COUNTRY_ID,ERC_NO,EMAIL,CONTACT_NO,REX_NO,REX_REG_DATE,IRC_NO,VAT_NUMBER FROM LIB_COMPANY WHERE ID ='$benificiary_id'");
	foreach($company_name_sql as $row)
	{
		$company_name=$row["COMPANY_NAME"];
		$plot_no=$row["PLOT_NO"];
		$level_no=$row["LEVEL_NO"];
		$road_no=$row["ROAD_NO"];
		$block_no=$row["BLOCK_NO"];
		$city=$row["CITY"];
		$country_id=$row["COUNTRY_ID"];
		$erc_no=$row["ERC_NO"];
		$contact_no=$row["CONTACT_NO"];
		$email=$row["EMAIL"];
		$rex_no=$row["REX_NO"];
		$rex_reg_date=$row["REX_REG_DATE"];
		$irc_no=$row["IRC_NO"];
		$vat_number=$row["VAT_NUMBER"];
		$address=$plot_no.",".$level_no.",".$road_no.",".$block_no;
	}
	
	$country_name_arr=return_library_array( "SELECT ID, COUNTRY_NAME FROM LIB_COUNTRY",'ID','COUNTRY_NAME');
	// $carrier=$SupplierArr[$forwarder_name];
	$applicant=$buyer_name_arr[$applicant_name]["BUYER_NAME"];
	$buyer=$buyer_name_arr[$buyer_name]["BUYER_NAME"];
	$applicantAddress=$buyer_name_arr[$applicant_name]["ADDRESS_1"];
	$agent=$buyer_name_arr[$agent_id]["BUYER_NAME"];
	$agentAddress=$buyer_name_arr[$agent_id]["ADDRESS_1"];
		

	$company_logo=return_field_value("image_location","common_photo_library","file_type=1 and form_name='company_details' and master_tble_id='$benificiary_id'","image_location");

	$data_terms=sql_select("SELECT ID,TERMS,TERMS_PREFIX FROM WO_BOOKING_TERMS_CONDITION WHERE BOOKING_NO=$update_id AND ENTRY_FORM=270 ORDER BY ID");

	$con_lc_arr=array();
	if($is_lc==1)
	{
		$lc_sc_data=sql_select("SELECT ID, EXPORT_LC_NO, LC_DATE, NOTIFYING_PARTY, CONSIGNEE, ISSUING_BANK_NAME, NEGOTIATING_BANK, LIEN_BANK, PAY_TERM, APPLICANT_NAME,INCO_TERM,NOMINATED_SHIPP_LINE, BUYER_NAME, TENOR,SHIPPING_MODE FROM COM_EXPORT_LC WHERE ID='".$lc_sc_id."' ");
		foreach($lc_sc_data as $row)
		{
			$lc_sc_date=change_date_format($row["LC_DATE"]);
			$sc_lc=$row["EXPORT_LC_NO"];
			$issuing_bank=$issue_bank_arr[$row["LIEN_BANK"]];
			$line_bank_id=$row["LIEN_BANK"];
			
		}
	}
	else
	{
		$lc_sc_data=sql_select("SELECT ID, CONTRACT_NO, CONTRACT_DATE, NOTIFYING_PARTY, CONSIGNEE, LIEN_BANK, PAY_TERM, APPLICANT_NAME,INCO_TERM,SHIPPING_LINE,BUYER_NAME, TENOR,SHIPPING_MODE, ISSUING_BANK FROM COM_SALES_CONTRACT WHERE ID='".$lc_sc_id."'  AND STATUS_ACTIVE=1");
		foreach($lc_sc_data as $row)
		{
			$lc_sc_date=change_date_format($row["CONTRACT_DATE"]);
			$sc_lc=$row["CONTRACT_NO"];
			$issuing_bank=$issue_bank_arr[$row["LIEN_BANK"]];
			$line_bank_id=$row["LIEN_BANK"];
		}
	}

	$wo_sql=sql_select("SELECT  c.MST_ID, e.PI_NUMBER, e.PI_DATE, e.HS_CODE FROM fabric_sales_order_mst m left join com_export_pi_dtls d  on d.WORK_ORDER_ID=m.id left join  com_export_pi_mst e on e.id=d.pi_id, fabric_sales_order_dtls a, com_export_lc_order_info b left join com_export_invoice_ship_dtls c on c.po_breakdown_id=b.wo_po_break_down_id and c.status_active=1 and c.is_deleted=0 where b.com_export_lc_id=$lc_sc_id and b.wo_po_break_down_id=m.id and m.id=a.mst_id and b.status_active=1 and b.is_deleted=0 and a.status_active=1 and a.is_deleted=0 and b.is_sales=1 and b.is_service=0 group by c.MST_ID,e.PI_NUMBER, e.PI_DATE, e.HS_CODE");

	$wo_pi_arrr=array();
	foreach($wo_sql as $row){
		$wo_pi_arrr[$row["MST_ID"]]["HS_CODE"]=$row["HS_CODE"];
		$pi_num.=$row[csf('pi_number')].', ';
		$pi_date.=change_date_format($row[csf('pi_date')]).', ';
	}
	$color_library = return_library_array("select id,color_name from lib_color  where status_active=1 and is_deleted=0", "id", "color_name");
	if($is_lc== '1')
	{		
		$sql= "SELECT id, export_lc_no as lc_sc, lien_bank, tolerance, applicant_name, beneficiary_name, buyer_name, shipping_mode, inco_term, inco_term_place, port_of_entry, port_of_loading, port_of_discharge, internal_file_no,consignee,notifying_party FROM com_export_lc where id= $lc_sc_id";

		$order_sql="SELECT a.FABRIC_DESC, a.GSM_WEIGHT, a.DIA, a.COLOR_ID,a.AVG_RATE, sum(a.FINISH_QTY) as FINISH_QTY
		FROM fabric_sales_order_mst m, fabric_sales_order_dtls a, com_export_invoice_ship_mst b, COM_EXPORT_INVOICE_SHIP_DTLS c 
		where b.LC_SC_ID=$lc_sc_id  and m.id=a.mst_id and c.PO_BREAKDOWN_ID=m.id and b.status_active=1 and b.is_deleted=0 and a.status_active=1 and a.is_deleted=0  and c.status_active=1 and c.is_deleted=0 and b.id=c.MST_ID
		group by  a.FABRIC_DESC, a.GSM_WEIGHT, a.DIA, a.COLOR_ID,a.AVG_RATE";				
	}
	else if($is_lc=='2')
	{
		$sql= "SELECT id, contract_no as lc_sc ,tolerance, lien_bank, applicant_name, beneficiary_name, buyer_name, shipping_mode, inco_term, inco_term_place, port_of_entry, port_of_loading, port_of_discharge, internal_file_no,consignee,notifying_party FROM com_sales_contract where id=$lc_sc_id";

		$order_sql="SELECT a.FABRIC_DESC, a.GSM_WEIGHT, a.DIA, a.COLOR_ID,a.AVG_RATE, sum(a.FINISH_QTY) as FINISH_QTY
		FROM fabric_sales_order_mst m, fabric_sales_order_dtls a, com_export_invoice_ship_mst b, COM_EXPORT_INVOICE_SHIP_DTLS c 
		where b.LC_SC_ID=$lc_sc_id  and m.id=a.mst_id and b.status_active=1 and b.is_deleted=0 and a.status_active=1 and a.is_deleted=0   and c.PO_BREAKDOWN_ID=m.id and c.status_active=1 and c.is_deleted=0 and b.id=c.MST_ID
		group by  a.FABRIC_DESC, a.GSM_WEIGHT, a.DIA, a.COLOR_ID,a.AVG_RATE";				
	}

	// echo $order_sql;

	$sql_result=sql_select($order_sql);




	ob_start();
	?>
	<style type="text/css" media="print">
		@page { 
			size: auto;
		} 
		thead {
		display: table-row-group;
		}
	</style>
    <table width='800' cellspacing="0" cellpadding="0">
		<tr>
			<td  align="left" width="70"><img src="../../<? echo $company_logo; ?>" height="70" width="70"></td>
            <td colspan="8" valign="top" align="center" >
				<b style="font-size:Large; font-weight:bold"> <?php echo $company_name."<br>";?>	</b>
					<?php
					if($plot_no!="")  $comany_details= $plot_no.", ";
					if($level_no!="")  $comany_details.= $level_no.", ";
					if($road_no!="")  $comany_details.= $road_no.", ";
					if($block_no!="")  $comany_details.= $block_no.", ";
					if($city!="")  $comany_details.= "<br>".$city.", ";
					if($contact_no!="")  $comany_details.="Telephone: ".$contact_no.",";
					if($email!="")  $comany_details.="E-MAIL: ".$email.",";
					if($ex_address!="")  $comany_details.="Address: ".$ex_address.",";
					echo  $comany_details;
					?><br>   
			
			</td>
        </tr>
        <tr>
            <td colspan="3" valign="top" > 
				<b>REF:  <?=$invoice_no?></b>
            </td>

            <td colspan="3" valign="top" >
				<b>COMMERCIAL INVOICE</b>
            </td>
			<td colspan="3" valign="top" >
				<b>DATE:<? echo $inv_master_data[0]["INVOICE_DATE"];?></b>
            </td>
        </tr>
		<br>
		<tr>
			<td colspan="9" valign="top" ></td>
		</tr>
		<tr>
			<td colspan="9" valign="top" >To</td>
		</tr>
		<tr>
			<td colspan="9" valign="top" ><?=$buyer_name_arr[$buyer_id]["BUYER_NAME"]?></td>
		</tr>
		<tr>
			<td colspan="9" valign="top" ><?= $buyer_name_arr[$buyer_id]["ADDRESS_1"];?></td>
		</tr>
		<tr>
			<td colspan="9" valign="top" ><?=$country_name_arr[$country_id]?></td>
		</tr>
		<tr>
			<td colspan="3" valign="top" >DRAWN UNDER OF L/C NO :</td>
			<td colspan="3" valign="top" > <?=$sc_lc?></td>
			<td colspan="3" valign="top" >Date: <?=$lc_sc_date?></td>
		</tr>
		<tr>
			<td colspan="3" valign="top" >P/INV NOS :</td>
			<td colspan="3" valign="top" ><?=rtrim($pi_num,", ")?></td>
			<td colspan="3" valign="top" >Date: <?=rtrim($pi_date,", ")?></td>
		</tr>
		<tr>
			<td colspan="3" valign="top" >H.S.Code No : <?=$wo_pi_arrr[$update_id]["HS_CODE"]?></td>
			<td colspan="3">IRC No. : <?=$irc_no?></td>
			<td colspan="3"></td> 
		</tr>
		<tr>
			<td colspan="3" valign="top" >ETA Date & Destination:</td>
			<td colspan="3"><?=$etd_destination." ".$eta_destination_place;?></td>
			<td colspan="3"></td>
		</tr>
		<tr>
			<td colspan="9"><b> Fabric Price for 100 % export oriented readymade garments industries</b></td>
		</tr>
	</table>

    <br>
	<table width='800' cellspacing="0" cellpadding="0" border="1">  
		<tr style="font-size:small; font-weight:bold" align="center">
			<td width = "300" colspan="2"><P>Description Of Goods</P> </td>
			<td width = "80"><P>Finish Quantity</P> </td>
			<td width="80"><P>Unit Price in USD</P> </td>
			<td width="80">Amount (US$) </td>
		</tr>

		<?
		$i=1;
		foreach($sql_result as $row)
		{    ?>
			<tr style="font-size:small">	
				<td width="300" align="left"> <p><? echo $row['FABRIC_DESC']." ". $row['GSM_WEIGHT']." ".$row['DIA']; ?> </p></td>
				<td width="120" align="center"><p><?  echo $color_library[$row["COLOR_ID"]]; ?></p></td>
				<td width="160" align="right"><p><? echo number_format($row["FINISH_QTY"],2,".",","); ?></p></td>
				<td width="160" align="right"><p><? echo number_format($row["AVG_RATE"],2,".",","); ?></p></td>
				<td width="160" align="right"><p><? echo number_format($row["FINISH_QTY"]*$row["AVG_RATE"],2,".",","); ?></p></td>
			</tr>
			<?
			$total_qnty+=$row["FINISH_QTY"];
			$total_ammount+=$row["FINISH_QTY"]*$row["AVG_RATE"];
			$i++;
		}
		?>
		<tr>
			<td colspan="2" align="right"><b>Total</b></td>
			<td align="right"><b><? echo number_format($total_qnty,0,".",",") ?></b></td>
			<td align="right"> <b></b></td>
			<td align="right"> <b><? echo number_format($total_ammount,2,".",","); ?></b></td>
		</tr>

		<tr>
			<td colspan="9"><b>In Word : <? echo number_to_words(def_number_format($total_ammount,2,""),"USD", "CENTS")." ONLY";?></b></td>
		</tr>
	</table>
	<br>
	<table width='800'>
		<tr>
			<td colspan="9"><h3 style="text-align: left;"><b>WE CERTIFIED AND DECLARED THAT AFORESAID MERCHANDISE GOODS SUPPLIED AGST. ABOVE NOTED LETTER OF CREDIT IS ORIGIN OF BANGLADESH</b></h3></td>
		</tr>
		<tr style="width: 40px;"></tr>
		<tr>
			<td colspan="9"><h3 style="text-align: left;"><b>Thank You</b></h3></td>
		</tr>
	</table>
	<br><br><br>
	<span style='page-break-after: always;'></span>
	<!-------------------------- PACKING LIST------------------------------>

	<table width='800' cellspacing="0" cellpadding="0">
		<tr>
			<td  align="left" width="70"><img src="../../<? echo $company_logo; ?>" height="70" width="70"></td>
            <td colspan="8" valign="top" align="center" >
				<b style="font-size:Large; font-weight:bold"> <?php echo $company_name."<br>";?>	</b>
					<?php
					echo  $comany_details;
					?><br>   
			
			</td>
        </tr>
        <tr>
            <td colspan="3" valign="top" > 
				<b>REF:  <?=$invoice_no?></b>
            </td>

            <td colspan="3" valign="top" >
				<b>PACKING LIST</b>
            </td>
			<td colspan="3" valign="top" >
				<b>DATE:<? echo $inv_master_data[0]["INVOICE_DATE"];?></b>
            </td>
        </tr>
		<br>
		<tr>
			<td colspan="9" valign="top" ></td>
		</tr>
		<tr>
			<td colspan="9" valign="top" >To</td>
		</tr>
		<tr>
			<td colspan="9" valign="top" ><?=$buyer_name_arr[$buyer_id]["BUYER_NAME"]?></td>
		</tr>
		<tr>
			<td colspan="9" valign="top" ><?= $buyer_name_arr[$buyer_id]["ADDRESS_1"];?></td>
		</tr>
		<tr>
			<td colspan="9" valign="top" ><?=$country_name_arr[$country_id]?></td>
		</tr>
		<tr>
			<td colspan="3" valign="top" >DRAWN UNDER OF L/C NO :</td>
			<td colspan="3" valign="top" > <?=$sc_lc?></td>
			<td colspan="3" valign="top" >Date: <?=$lc_sc_date?></td>
		</tr>
		<tr>
			<td colspan="3" valign="top" >P/INV NOS :</td>
			<td colspan="3" valign="top" ><?=rtrim($pi_num,", ")?></td>
			<td colspan="3" valign="top" >Date: <?=rtrim($pi_date,", ")?></td>
		</tr>
		<tr>
			<td colspan="3" valign="top" >H.S.Code No : <?=$wo_pi_arrr[$update_id]["HS_CODE"]?></td>
			<td colspan="3">IRC No. : <?=$irc_no?></td>
			<td colspan="3"></td> 
		</tr>
		<tr>
			<td colspan="3" valign="top" >ETA Date & Destination:</td>
			<td colspan="3"><?=$etd_destination." ".$eta_destination_place;?></td>
			<td colspan="3"></td>
		</tr>
		<tr>
			<td colspan="9"><b> Fabric Price for 100 % export oriented readymade garments industries</b></td>
		</tr>
	</table>
    <br>
	<table width='800' cellspacing="0" cellpadding="0" border="1">  
		<tr style="font-size:small; font-weight:bold" align="center">
			<td width = "300" colspan="2"><P>Description Of Goods</P> </td>
			<td width = "80"><P>Finish Quantity</P> </td>
			<td width="80">Roll Quantity</td>
		</tr>

		<?
		$i=1;
		foreach($sql_result as $row)
		{    ?>
			<tr style="font-size:small">	
				<td width="300" align="left"> <p><? echo $row['FABRIC_DESC']." ". $row['GSM_WEIGHT']." ".$row['DIA']; ?> </p></td>
				<td width="120" align="center"><p><?  echo $color_library[$row["COLOR_ID"]]; ?></p></td>
				<td width="160" align="right"><p><? echo number_format($row["FINISH_QTY"],2,".",","); ?></p></td>
				<td width="160" align="right"><p><? echo ceil($row["FINISH_QTY"]/20); ?></p></td>
			</tr>
			<?
			$pac_total_qnty+=$row["FINISH_QTY"];
			$finish_qty+=$row["FINISH_QTY"]/20;
			$i++;
		}
		?>
		<tr>
			<td colspan="2" align="right"><b>Total</b></td>
			<td align="right"><b><? echo number_format($pac_total_qnty,0,".",",") ?></b></td>
			<td align="right"> <b><? echo number_format($finish_qty); ?></b></td>
		</tr>
	</table>
	<br>
	<table width='800'>
		<tr>
			<td colspan="9"><h3 style="text-align: left;"><b>WE DO HEREBY CERTIFY THAT THE GOODS ARE DELIVERED STRICTLY ACCORDANCE WITH THE ABOVE MENTIONED BENEFICIARY'S P/I NOS</b></h3></td>
		</tr>
		<tr style="width: 40px;"></tr>
		<tr>
			<td colspan="9"><h3 style="text-align: left;"><b>Thank You</b></h3></td>
		</tr>
	</table>
	<br><br><br>
	<span style='page-break-after: always;'></span>
		<!-------------------------- TRUCK CHALLAN------------------------------>

	<table width='800' cellspacing="0" cellpadding="0" border="1">
		<tr>
			<td  align="left" width="70"><img src="../../<? echo $company_logo; ?>" height="70" width="70"></td>
            <td colspan="8" valign="top" align="center" >
				<b style="font-size:Large; font-weight:bold"> <?php echo $company_name."<br>";?>	</b>
					<?php
					echo  $comany_details;
					?><br>   
			
			</td>
        </tr>
        <tr>
            <td colspan="3" valign="top"> 
				<b>REF:  <?=$invoice_no?></b>
            </td>
            <td colspan="3" valign="top">
				<b>TRUCK CHALLAN</b>
            </td>
			<td colspan="3" valign="top">
				<b>DATE:<? echo $inv_master_data[0]["INVOICE_DATE"];?></b>
            </td>
        </tr>
	</table>

	<br><br>
	<table width='800' cellspacing="0" cellpadding="0" border="1">
		<tr>
			<table border="1" width='800'>
				<tr>
					<td colspan="4" valign="top" ><b> NAME OF EXPORTER:</b> <?="<br>".$company_name."<br>".$comany_details?></td>
					<td 4olspan="4" valign="top" ><b>NAME OF IMPORTER</b><?="<br>".$buyer_name_arr[$buyer_id]["BUYER_NAME"]."<br>".$buyer_name_arr[$buyer_id]["ADDRESS_1"]."<br>".$country_name_arr[$country_id];?></td>
				</tr>
				<tr>
					<td colspan="4" valign="top" ><b> FACTORY:</b> <?="<br>".$comany_details?></td>
					<td 4olspan="4" valign="top" ><b>LC ISSUING BANK:</b><?="<br>".$issuing_bank."<br>".$bank_name_arr[$line_bank_id]["ADDRESS"]."<br> A/C No: ".$bank_acc_arr[$line_bank_id][20]["ACCOUNT_NO"]."<br>Swift Code:".$bank_name_arr[$line_bank_id]["SWIFT_CODE"];?></td>
				</tr>
			</table>
		</tr>
	</table>
    <br>
	<table width='800' cellspacing="0" cellpadding="0" border="1">  
		<tr style="font-size:small; font-weight:bold" align="center">
			<td width = "300" colspan="2"><P>Description Of Goods</P> </td>
			<td width = "80"><P>Finish Quantity</P> </td>
			<td width="80">Roll Quantity</td>
		</tr>

		<?
		$i=1;
		foreach($sql_result as $row)
		{    ?>
			<tr style="font-size:small">	
				<td width="300" align="left"> <p><? echo $row['FABRIC_DESC']." ". $row['GSM_WEIGHT']." ".$row['DIA']; ?> </p></td>
				<td width="120" align="center"><p><?  echo $color_library[$row["COLOR_ID"]]; ?></p></td>
				<td width="160" align="right"><p><? echo number_format($row["FINISH_QTY"],2,".",","); ?></p></td>
				<td width="160" align="right"><p><? echo ceil($row["FINISH_QTY"]/20); ?></p></td>
			</tr>
			<?
			$truck_total_qnty+=$row["FINISH_QTY"];
			$total_fin_qty+=$row["FINISH_QTY"]/20;
			$i++;
		}
		?>
		<tr>
			<td colspan="2" align="right"><b>Total</b></td>
			<td align="right"><b><? echo number_format($truck_total_qnty,0,".",",") ?></b></td>
			<td align="right"> <b><? echo number_format($total_fin_qty); ?></b></td>
		</tr>
	</table>
	<br>
	<table width='800'>
		<tr>
			<td colspan="9" align="left"> "FREIGHT PREPAID"</td>
		</tr>
		<tr style="height: 30;"></tr>
		<tr>
			<td colspan="9"><h3 style="text-align: left;"><b>THANKING YOU</b></h3></td>
		</tr>
		<tr style="width: 40px;"></tr>
		<tr>
			<td colspan="9"><h5 style="text-align: left;"><b>AUTHORIZED SIGNATURE</b></h5></td>
		</tr>
	</table>
     <br><br><br>
	 <span style='page-break-after: always;'></span>
		<!-------------------------- DELIVERY CHALLAN------------------------------>
	<table width='800' cellspacing="0" cellpadding="0">
		<tr>
			<td  align="left" width="70"><img src="../../<? echo $company_logo; ?>" height="70" width="70"></td>
            <td colspan="8" valign="top" align="center" >
				<b style="font-size:Large; font-weight:bold"> <?php echo $company_name."<br>";?>	</b>
					<?php
					echo  $comany_details;
					?><br>   
			
			</td>
        </tr>
        <tr>
            <td colspan="3" valign="top" > 
				<b>REF:  <?=$invoice_no?></b>
            </td>

            <td colspan="3" valign="top" >
				<b>DELIVERY CHALLAN</b>
            </td>
			<td colspan="3" valign="top" >
				<b>DATE:<? echo $inv_master_data[0]["INVOICE_DATE"];?></b>
            </td>
        </tr>
		<br>
		<tr>
			<td colspan="9" valign="top" ></td>
		</tr>
		<tr>
			<td colspan="9" valign="top" >To</td>
		</tr>
		<tr>
			<td colspan="9" valign="top" ><?=$buyer_name_arr[$buyer_id]["BUYER_NAME"]?></td>
		</tr>
		<tr>
			<td colspan="9" valign="top" ><?= $buyer_name_arr[$buyer_id]["ADDRESS_1"];?></td>
		</tr>
		<tr>
			<td colspan="9" valign="top" ><?=$country_name_arr[$country_id]?></td>
		</tr>
		<tr>
			<td colspan="3" valign="top" >DRAWN UNDER OF L/C NO :</td>
			<td colspan="3" valign="top" > <?=$sc_lc?></td>
			<td colspan="3" valign="top" >Date: <?=$lc_sc_date?></td>
		</tr>
		<tr>
			<td colspan="3" valign="top" >P/INV NOS :</td>
			<td colspan="3" valign="top" ><?=rtrim($pi_num,", ")?></td>
			<td colspan="3" valign="top" >Date: <?=rtrim($pi_date,", ")?></td>
		</tr>
		<tr>
			<td colspan="3" valign="top" >H.S.Code No : <?=$wo_pi_arrr[$update_id]["HS_CODE"]?></td>
			<td colspan="3">IRC No. : <?=$irc_no?></td>
			<td colspan="3"></td> 
		</tr>
		<tr>
			<td colspan="3" valign="top" >ETA Date & Destination:</td>
			<td colspan="3"><?=$etd_destination." ".$eta_destination_place;?></td>
			<td colspan="3"></td>
		</tr>
		<tr>
			<td colspan="9"><b> Fabric Price for 100 % export oriented readymade garments industries</b></td>
		</tr>
	</table>
    <br>
	<table width='800' cellspacing="0" cellpadding="0" border="1">  
		<tr style="font-size:small; font-weight:bold" align="center">
			<td width = "300" colspan="2"><P>Description Of Goods</P> </td>
			<td width = "80"><P>Finish Quantity</P> </td>
			<td width="80">Roll Quantity</td>
		</tr>

		<?
		$i=1;
		foreach($sql_result as $row)
		{    ?>
			<tr style="font-size:small">	
				<td width="300" align="left"> <p><? echo $row['FABRIC_DESC']." ". $row['GSM_WEIGHT']." ".$row['DIA']; ?> </p></td>
				<td width="120" align="center"><p><?  echo $color_library[$row["COLOR_ID"]]; ?></p></td>
				<td width="160" align="right"><p><? echo number_format($row["FINISH_QTY"],2,".",","); ?></p></td>
				<td width="160" align="right"><p><? echo ceil($row["FINISH_QTY"]/20); ?></p></td>
			</tr>
			<?
			$del_total_qnty+=$row["FINISH_QTY"];
			$total_fin+=$row["FINISH_QTY"]/20;
			$i++;
		}
		?>
		<tr>
			<td colspan="2" align="right"><b>Total</b></td>
			<td align="right"><b><? echo number_format($del_total_qnty,0,".",",") ?></b></td>
			<td align="right"> <b><? echo number_format($total_fin); ?></b></td>
		</tr>
	</table>
	<br>
	<table width='800'>
		<tr>
			<td colspan="9"><h3 style="text-align: left;"><b>WE DO HEREBY CERTIFY THAT THE GOODS ARE DELIVERED STRICTLY ACCORDANCE WITH THE ABOVE MENTIONED BENEFICIARY'S P/I NOS</b></h3></td>
		</tr>
		<tr>
			<td colspan="9">I UNDERSIGNED, HEREBY ACKNOWLEDGE THAT WE HAVE RECEIVED THE ABOVE MENTIONED GOODS AS MENTIONED P/I NOS IN GOOD CONDITION AND FULL QTY AGAINST <b>BTB L/C NO: <?=$sc_lc?> Date: <?=$lc_sc_date?></b></td>
		</tr>
		<tr style="width: 40px;"></tr>
		<tr>
			<td colspan="2"><h4 style="text-align: left;"><b>RECEIVER'S SIGNATURE</b></h4></td>
			<td colspan="5"><h4 style="text-align: left;"></h4></td>
			<td colspan="2"><h4 style="text-align: left;"><b>AUTHORIZED SIGNATURE</b></h4></td>
		</tr>
	</table>

	<?
		$html = ob_get_contents();
		ob_clean();
		foreach (glob("tb*.xls") as $filename) {
		@unlink($filename);
		}
		//---------end------------//
		$name=time();
		$filename="tb".$user_id."_".$name.".xls";
		$create_new_doc = fopen($filename, 'w');
		$is_created = fwrite($create_new_doc, $html);
		echo "$filename####$html";
	exit();	
}

if($action=="invoice_report_print_9")  //print 9
{
	extract($_REQUEST);
	// $update_id=$data;
 
	$brand_arr=return_library_array( "SELECT ID, BRAND_NAME FROM LIB_BUYER_BRAND BRAND",'ID','BRAND_NAME');
	$issue_bank_arr=return_library_array( "SELECT ID, BANK_NAME FROM LIB_BANK BRAND",'ID','BANK_NAME');
	$brand_arr=return_library_array( "SELECT ID, BRAND_NAME FROM LIB_BUYER_BRAND BRAND",'ID','BRAND_NAME');
	$applicant_sql=sql_select( "SELECT A.ID, A.BUYER_NAME, A.SHORT_NAME, A.ADDRESS_1 FROM LIB_BUYER A");
	$custom_designation=return_library_array( "SELECT ID, CUSTOM_DESIGNATION FROM LIB_DESIGNATION BRAND",'ID','CUSTOM_DESIGNATION');
	$item_group_arr=return_library_array( "select id, item_name from lib_item_group where item_category=4 and status_active=1",'id','item_name');

	foreach($applicant_sql as $row)
	{
		$buyer_name_arr[$row["ID"]]["BUYER_NAME"]=$row["BUYER_NAME"];
		$buyer_name_arr[$row["ID"]]["ADDRESS_1"]=$row["ADDRESS_1"];
	}
	$bank_sql=sql_select( "SELECT A.ID, A.BANK_NAME, A.BRANCH_NAME, A.ADDRESS, A.SWIFT_CODE, A.DESIGNATION FROM LIB_BANK A");
	foreach($bank_sql as $row)
	{
		$bank_name_arr[$row["ID"]]["BANK_NAME"]=$row["BANK_NAME"];
		$bank_name_arr[$row["ID"]]["BRANCH_NAME"]=$row["BRANCH_NAME"];
		$bank_name_arr[$row["ID"]]["ADDRESS"]=$row["ADDRESS"];
		$bank_name_arr[$row["ID"]]["SWIFT_CODE"]=$row["SWIFT_CODE"];
		$bank_name_arr[$row["ID"]]["DESIGNATION"]=$row["DESIGNATION"];
	}
	$bank_account_sql=sql_select( "SELECT ID, ACCOUNT_ID, ACCOUNT_TYPE, ACCOUNT_NO FROM LIB_BANK_ACCOUNT WHERE IS_DELETED=0 ");
	foreach($bank_account_sql as $row)
	{
		$bank_acc_arr[$row["ACCOUNT_ID"]][$row["ACCOUNT_TYPE"]]["ACCOUNT_NO"]=$row["ACCOUNT_NO"];
	}
	$inv_master_data=sql_select("SELECT id, benificiary_id, buyer_id, location_id, invoice_no, invoice_date, exp_form_no, exp_form_date, is_lc, lc_sc_id, bl_no, feeder_vessel, inco_term, inco_term_place, shipping_mode, port_of_entry, port_of_loading, port_of_discharge, main_mark, side_mark, carton_net_weight, carton_gross_weight, cbm_qnty, place_of_delivery, delv_no, consignee, notifying_party, item_description, discount_ammount, bonus_ammount, commission, total_carton_qnty, bl_date, hs_code, mother_vessel, category_no, forwarder_name, etd,co_no, total_measurment, invoice_value, net_invo_value, container_no, seal_no, etd, country_id,commission_percent,bl_rev_date, eta_destination_place, etd_destination from com_export_invoice_ship_mst where id=$update_id");
	
	$id=$inv_master_data[0]["ID"];
	$benificiary_id=$inv_master_data[0]["BENIFICIARY_ID"];
	$buyer_id=$inv_master_data[0]["BUYER_ID"];
	$location_id=$inv_master_data[0]["LOCATION_ID"];
	$invoice_no=$inv_master_data[0]["INVOICE_NO"];
	$invoice_date=$inv_master_data[0]["INVOICE_DATE"];
	$invoice_value=$inv_master_data[0]["INVOICE_VALUE"];
	$eta_destination_place=$inv_master_data[0]["ETA_DESTINATION_PLACE"];
	$etd_destination=$inv_master_data[0]["ETD_DESTINATION"];


	$bl_rev_date=$inv_master_data[0]["BL_REV_DATE"];
	$is_lc=$inv_master_data[0]["IS_LC"];
	$lc_sc_id=$inv_master_data[0]["LC_SC_ID"];
	$bl_no=$inv_master_data[0]["BL_NO"];
	$feeder_vessel=$inv_master_data[0]["FEEDER_VESSEL"];
	$inco_term=$inv_master_data[0]["INCO_TERM"];
	$inco_term_place=$inv_master_data[0]["INCO_TERM_PLACE"];
	$shipping_mode=$inv_master_data[0]["SHIPPING_MODE"];
	$port_of_entry=$inv_master_data[0]["PORT_OF_ENTRY"];
	$port_of_loading=$inv_master_data[0]["PORT_OF_LOADING"];
	$port_of_discharge=$inv_master_data[0]["PORT_OF_DISCHARGE"];
	$main_mark=$inv_master_data[0]["MAIN_MARK"];
	$side_mark=$inv_master_data[0]["SIDE_MARK"];
	$net_weight=$inv_master_data[0]["CARTON_NET_WEIGHT"];
	$gross_weight=$inv_master_data[0]["CARTON_GROSS_WEIGHT"];
	$cbm_qnty=$inv_master_data[0]["CBM_QNTY"];
	$place_of_delivery=$inv_master_data[0]["PLACE_OF_DELIVERY"];
	$delv_no=$inv_master_data[0]["DELV_NO"];
	$consignee=$inv_master_data[0]["CONSIGNEE"];
	$notifying_party=$inv_master_data[0]["NOTIFYING_PARTY"];
	$item_description=$inv_master_data[0]["ITEM_DESCRIPTION"];
	$discount_ammount=$inv_master_data[0]["DISCOUNT_AMMOUNT"];
	$bonus_ammount=$inv_master_data[0]["BONUS_AMMOUNT"];
	$commission=$inv_master_data[0]["COMMISSION"];
	$commission_percent=$inv_master_data[0]["COMMISSION_PERCENT"];
	$total_carton_qnty=$inv_master_data[0]["TOTAL_CARTON_QNTY"];
	$bl_date=$inv_master_data[0]["BL_DATE"];
	$hs_code=$inv_master_data[0]["HS_CODE"];
	$mother_vessel=$inv_master_data[0]["MOTHER_VESSEL"];
	$category_no=$inv_master_data[0]["CATEGORY_NO"];
	$forwarder_name=$inv_master_data[0]["FORWARDER_NAME"];
	$etd=$inv_master_data[0]["ETD"];
	$co_no=$inv_master_data[0]["CO_NO"];
	$total_measurment=$inv_master_data[0]["TOTAL_MEASURMENT"];
	$net_invo_value=$inv_master_data[0]["NET_INVO_VALUE"];
	$container_no=$inv_master_data[0]["CONTAINER_NO"];
	$seal_no=$inv_master_data[0]["SEAL_NO"];
	$etd=$inv_master_data[0]["ETD"];
	$inv_country_id=$inv_master_data[0]["COUNTRY_ID"];

	$company_name_sql=sql_select("SELECT ID, COMPANY_NAME, PLOT_NO, LEVEL_NO, ROAD_NO, BLOCK_NO, CITY, COUNTRY_ID,ERC_NO,EMAIL,CONTACT_NO,REX_NO,REX_REG_DATE,IRC_NO,VAT_NUMBER FROM LIB_COMPANY WHERE ID ='$benificiary_id'");
	foreach($company_name_sql as $row)
	{
		$company_name=$row["COMPANY_NAME"];
		$plot_no=$row["PLOT_NO"];
		$level_no=$row["LEVEL_NO"];
		$road_no=$row["ROAD_NO"];
		$block_no=$row["BLOCK_NO"];
		$city=$row["CITY"];
		$country_id=$row["COUNTRY_ID"];
		$erc_no=$row["ERC_NO"];
		$contact_no=$row["CONTACT_NO"];
		$email=$row["EMAIL"];
		$rex_no=$row["REX_NO"];
		$rex_reg_date=$row["REX_REG_DATE"];
		$irc_no=$row["IRC_NO"];
		$vat_number=$row["VAT_NUMBER"];
		$address=$plot_no.",".$level_no.",".$road_no.",".$block_no;
	}
	if($plot_no!="")  $com_dtls=$comany_details= $plot_no.", ";
	if($level_no!="")  $com_dtls=$comany_details.= $level_no.", ";
	if($road_no!="")  $com_dtls=$comany_details.= $road_no.", ";
	if($block_no!="")  $com_dtls=$comany_details.= $block_no.", ";
	if($city!="")  $comany_details.= $city.", ";
	if($city!="")  $com_dtls.= $city."";
	if($contact_no!="")  $comany_details.="Telephone: ".$contact_no.",";
	if($email!="")  $comany_details.="E-MAIL: ".$email.",";
	if($ex_address!="")  $com_dtls=$comany_details.="Address: ".$ex_address.",";
	
	$country_name_arr=return_library_array( "SELECT ID, COUNTRY_NAME FROM LIB_COUNTRY",'ID','COUNTRY_NAME');
	// $carrier=$SupplierArr[$forwarder_name];
	$applicant=$buyer_name_arr[$applicant_name]["BUYER_NAME"];
	$buyer=$buyer_name_arr[$buyer_name]["BUYER_NAME"];
	$applicantAddress=$buyer_name_arr[$applicant_name]["ADDRESS_1"];
	$agent=$buyer_name_arr[$agent_id]["BUYER_NAME"];
	$agentAddress=$buyer_name_arr[$agent_id]["ADDRESS_1"];
		

	$company_logo=return_field_value("image_location","common_photo_library","file_type=1 and form_name='company_details' and master_tble_id='$benificiary_id'","image_location");

	$data_terms=sql_select("SELECT ID,TERMS,TERMS_PREFIX FROM WO_BOOKING_TERMS_CONDITION WHERE BOOKING_NO=$update_id AND ENTRY_FORM=270 ORDER BY ID");

	$con_lc_arr=array();
	if($is_lc==1)
	{
		$lc_sc_data=sql_select("SELECT ID, EXPORT_LC_NO, LC_DATE, NOTIFYING_PARTY, CONSIGNEE, ISSUING_BANK_NAME, NEGOTIATING_BANK, LIEN_BANK, PAY_TERM, APPLICANT_NAME,INCO_TERM,NOMINATED_SHIPP_LINE, BUYER_NAME, TENOR,SHIPPING_MODE FROM COM_EXPORT_LC WHERE ID='".$lc_sc_id."' ");
		foreach($lc_sc_data as $row)
		{
			$lc_sc_date=change_date_format($row["LC_DATE"]);
			$sc_lc=$row["EXPORT_LC_NO"];
			$issuing_bank=$issue_bank_arr[$row["LIEN_BANK"]];
			$line_bank_id=$row["LIEN_BANK"];
			
		}
	}
	else
	{
		$lc_sc_data=sql_select("SELECT ID, CONTRACT_NO, CONTRACT_DATE, NOTIFYING_PARTY, CONSIGNEE, LIEN_BANK, PAY_TERM, APPLICANT_NAME,INCO_TERM,SHIPPING_LINE,BUYER_NAME, TENOR,SHIPPING_MODE, ISSUING_BANK FROM COM_SALES_CONTRACT WHERE ID='".$lc_sc_id."'  AND STATUS_ACTIVE=1");
		foreach($lc_sc_data as $row)
		{
			$lc_sc_date=change_date_format($row["CONTRACT_DATE"]);
			$sc_lc=$row["CONTRACT_NO"];
			$issuing_bank=$issue_bank_arr[$row["LIEN_BANK"]];
			$line_bank_id=$row["LIEN_BANK"];
		}
	}

	$bank_sql=sql_select( "SELECT A.ID, A.BANK_NAME, A.BRANCH_NAME, A.ADDRESS, A.SWIFT_CODE, A.DESIGNATION FROM LIB_BANK A");
	foreach($bank_sql as $row)
	{
		$bank_name_arr[$row["ID"]]["BANK_NAME"]=$row["BANK_NAME"];
		$bank_name_arr[$row["ID"]]["BRANCH_NAME"]=$row["BRANCH_NAME"];
		$bank_name_arr[$row["ID"]]["ADDRESS"]=$row["ADDRESS"];
		$bank_name_arr[$row["ID"]]["DESIGNATION"]=$row["DESIGNATION"];
		$bank_name_arr[$row["ID"]]["SWIFT_CODE"]=$row["SWIFT_CODE"];  
	}
	$bank_account_sql=sql_select( "SELECT ID, ACCOUNT_ID, ACCOUNT_TYPE, ACCOUNT_NO FROM LIB_BANK_ACCOUNT WHERE IS_DELETED=0 ");
	foreach($bank_account_sql as $row)
	{
		$bank_acc_arr[$row["ACCOUNT_ID"]][$row["ACCOUNT_TYPE"]]["ACCOUNT_NO"]=$row["ACCOUNT_NO"];
	}

	$applicant_sql=sql_select( "SELECT A.ID, A.BUYER_NAME, A.SHORT_NAME, A.ADDRESS_1 FROM LIB_BUYER A");

	foreach($applicant_sql as $row)
	{
		$buyer_name_arr[$row["ID"]]["BUYER_NAME"]=$row["BUYER_NAME"];
		$buyer_name_arr[$row["ID"]]["ADDRESS_1"]=$row["ADDRESS_1"];
	}

	$wo_sql=sql_select("SELECT  c.MST_ID, e.PI_NUMBER, e.PI_DATE, e.HS_CODE FROM fabric_sales_order_mst m left join com_export_pi_dtls d  on d.WORK_ORDER_ID=m.id left join  com_export_pi_mst e on e.id=d.pi_id, fabric_sales_order_dtls a, com_export_lc_order_info b left join com_export_invoice_ship_dtls c on c.po_breakdown_id=b.wo_po_break_down_id and c.status_active=1 and c.is_deleted=0 where b.com_export_lc_id=$lc_sc_id and b.wo_po_break_down_id=m.id and m.id=a.mst_id and b.status_active=1 and b.is_deleted=0 and a.status_active=1 and a.is_deleted=0 and b.is_sales=1 and b.is_service=0 group by c.MST_ID,e.PI_NUMBER, e.PI_DATE, e.HS_CODE
	union all 
	SELECT   g.MST_ID, f.PI_NUMBER, f.PI_DATE, f.HS_CODE FROM subcon_ord_mst a, subcon_ord_dtls b, com_export_lc_order_info c,  COM_EXPORT_INVOICE_SHIP_DTLS  g, SUBCON_ORD_BREAKDOWN d, COM_EXPORT_PI_DTLS e, COM_EXPORT_PI_MST f  WHERE a.id = b.mst_id and a.entry_form = 255 and c.com_export_lc_id=$lc_sc_id AND c.wo_po_break_down_id = b.id  AND c.wo_po_break_down_id = g.PO_BREAKDOWN_ID AND g.PO_BREAKDOWN_ID = d.mst_id AND e.WORK_ORDER_DTLS_ID = d.id  AND f.id = e.pi_id and b.status_active=1 and b.is_deleted=0 and a.is_deleted = 0 AND a.status_active = 1 and c.is_deleted = 0 AND c.status_active = 1 AND d.status_active = 1 AND e.status_active = 1 and f.ITEM_CATEGORY_ID=45 AND e.status_active = 1 AND g.status_active = 1 group by g.MST_ID, f.PI_NUMBER, f.PI_DATE, f.HS_CODE");

	$wo_pi_arrr=array();
	foreach($wo_sql as $row){
		$wo_pi_arrr[$row["MST_ID"]]["HS_CODE"]=$row["HS_CODE"];
		$hs_code=$row[csf('HS_CODE')];
		$pi_number.=$row[csf('pi_number')].', ';
		$pi_date.=change_date_format($row[csf('pi_date')]).', ';
	}
	$color_library = return_library_array("select id,color_name from lib_color  where status_active=1 and is_deleted=0", "id", "color_name");
	if($is_lc==1) 
	{
		$lc_sc_data=sql_select("SELECT ID, EXPORT_LC_NO, LC_DATE, NOTIFYING_PARTY, CONSIGNEE, ISSUING_BANK_NAME, NEGOTIATING_BANK, LIEN_BANK, PAY_TERM, APPLICANT_NAME,INCO_TERM,LIEN_BANK,NOMINATED_SHIPP_LINE, BUYER_NAME, TENOR,SHIPPING_MODE,LC_VALUE, INTERNAL_FILE_NO, MAX_BTB_LIMIT, CURRENCY_NAME FROM COM_EXPORT_LC WHERE ID='".$lc_sc_id."' ");
		
		foreach($lc_sc_data as $row)
		{
			$lc_sc_id=$row["ID"];
			$lc_sc_no=$row["EXPORT_LC_NO"];
			$lc_sc_value = $row['LC_VALUE'];
			$currency_name=$row["CURRENCY_NAME"];
			$lc_sc_date=change_date_format($row["LC_DATE"]);
			$lien_bank=$row["LIEN_BANK"];	
			$issuing_bank_name=$row["ISSUING_BANK_NAME"];	
			$notifying_party=$row["NOTIFYING_PARTY"];	
			$internal_file_no=$row["INTERNAL_FILE_NO"];	
			$max_btb_limit=$row["MAX_BTB_LIMIT"];	
			$pay_term_id=$row["PAY_TERM"];
			$inco_term_id=$row["INCO_TERM"];	


		}
		$sql_del=sql_select("SELECT d.TRIMS_DEL, c.ORDER_NO, c.ID, d.DELIVERY_DATE, d.REMARKS  FROM com_export_lc_order_info a, SUBCON_ORD_DTLS b, SUBCON_ORD_MST c left join  TRIMS_DELIVERY_MST d on c.id=d.RECEIVED_ID AND d.STATUS_ACTIVE=1 where a.WO_PO_BREAK_DOWN_ID=b.id and b.mst_id=c.id  AND a.STATUS_ACTIVE=1 AND b.STATUS_ACTIVE=1 AND c.STATUS_ACTIVE=1  and a.COM_EXPORT_LC_ID='".$lc_sc_id."'");
	}
	else
	{
		$lc_sc_data=sql_select("SELECT ID, CONTRACT_NO, CONTRACT_DATE, NOTIFYING_PARTY, CONSIGNEE, LIEN_BANK, PAY_TERM, APPLICANT_NAME,INCO_TERM,SHIPPING_LINE,BUYER_NAME, TENOR,SHIPPING_MODE,CONTRACT_VALUE, ISSUING_BANK, INTERNAL_FILE_NO, MAX_BTB_LIMIT, CURRENCY_NAME FROM COM_SALES_CONTRACT WHERE ID='".$lc_sc_id."'  AND STATUS_ACTIVE=1");
		foreach($lc_sc_data as $row)
		{
			$lc_sc_id=$row["ID"];
			$lc_sc_no=$row["CONTRACT_NO"];
			$lc_sc_value=$row["CONTRACT_VALUE"];
			$currency_name=$row["CURRENCY_NAME"];
			$lc_sc_date=change_date_format($row["CONTRACT_DATE"]);
			$lien_bank=$row["LIEN_BANK"];
			$issuing_bank_name=$row["ISSUING_BANK"];
			$notifying_party=$row["NOTIFYING_PARTY"];	
			$internal_file_no=$row["INTERNAL_FILE_NO"];	
			$max_btb_limit=$row["MAX_BTB_LIMIT"];	
			$pay_term_id=$row["PAY_TERM"];	
			$inco_term_id=$row["INCO_TERM"];	

		}

		$sql_del=sql_select("SELECT d.TRIMS_DEL, c.ORDER_NO, c.ID, d.DELIVERY_DATE, d.REMARKS FROM com_export_lc_order_info a, SUBCON_ORD_DTLS b, SUBCON_ORD_MST c left join  TRIMS_DELIVERY_MST d on c.id=d.RECEIVED_ID AND d.STATUS_ACTIVE=1 where a.WO_PO_BREAK_DOWN_ID=b.id and b.mst_id=c.id   AND a.STATUS_ACTIVE=1 AND b.STATUS_ACTIVE=1 AND c.STATUS_ACTIVE=1  and a.COM_EXPORT_LC_ID='".$lc_sc_id."'");
	
	}

	//dtls_query
	$order_sql="SELECT c.ITEM_CATEGORY_ID, c.PI_NUMBER, c.PI_DATE, d.UOM,  d.COMPOSITION  ||' '|| d.CONSTRUCTION ||' '|| d.GSM  as fab_desc, sum(d.QUANTITY) as qty, b.CARTON_QTY, 0 as ITEM_GROUP, 0 as SECTION, sum(d.AMOUNT) as AMOUNT, d.rate FROM com_export_invoice_ship_mst a, COM_EXPORT_INVOICE_SHIP_DTLS b, COM_EXPORT_PI_MST c, COM_EXPORT_PI_DTLS d where a.id=b.mst_id and d.WORK_ORDER_ID=b.PO_BREAKDOWN_ID and c.id=d.pi_id and a.LC_SC_ID = $lc_sc_id and c.ITEM_CATEGORY_ID=10 group by c.ITEM_CATEGORY_ID, c.PI_NUMBER, d.UOM, c.PI_DATE, d.CONSTRUCTION, d.COMPOSITION, d.GSM, d.DIA_WIDTH, b.CARTON_QTY, d.rate
	union all 
	SELECT f.ITEM_CATEGORY_ID, f.PI_NUMBER, f.PI_DATE, e.UOM, e.CONSTRUCTION || ' ' || e.COMPOSITION || ' ' || e.GSM as fab_desc, SUM(g.CURRENT_INVOICE_QNTY) as qty, g.CARTON_QTY, b.ITEM_GROUP, b.SECTION, sum(g.CURRENT_INVOICE_VALUE) as AMOUNT, g.CURRENT_INVOICE_RATE as rate  FROM subcon_ord_mst a, subcon_ord_dtls b, com_export_lc_order_info c,  COM_EXPORT_INVOICE_SHIP_DTLS  g, SUBCON_ORD_BREAKDOWN d, COM_EXPORT_PI_DTLS e, COM_EXPORT_PI_MST f  WHERE a.id = b.mst_id and a.entry_form = 255 and c.com_export_lc_id=$lc_sc_id AND c.wo_po_break_down_id = b.id  AND c.wo_po_break_down_id = g.PO_BREAKDOWN_ID AND g.PO_BREAKDOWN_ID = d.mst_id AND e.WORK_ORDER_DTLS_ID = d.id  AND f.id = e.pi_id and b.status_active=1 and b.is_deleted=0 and a.is_deleted = 0 AND a.status_active = 1 and c.is_deleted = 0 AND c.status_active = 1 AND d.status_active = 1 AND e.status_active = 1 and f.ITEM_CATEGORY_ID=45 AND e.status_active = 1 AND g.status_active = 1 group by f.ITEM_CATEGORY_ID, f.PI_NUMBER, f.PI_DATE, e.UOM, e.CONSTRUCTION, e.COMPOSITION, e.GSM, g.CARTON_QTY, b.ITEM_GROUP, b.SECTION, g.CURRENT_INVOICE_RATE";
	
	$SqlDtlsResult=sql_select($order_sql);
	
	ob_start();
	?>
	<style type="text/css" media="print">
		@page { 
			size: auto;
		} 
		thead {
		display: table-row-group;
		}
	</style>
    <table width='800' cellspacing="0" cellpadding="0">
		<tr>
			<td  align="left" width="70"><img src="../../<? echo $company_logo; ?>" height="70" width="70"></td>
            <td colspan="8" valign="top" align="center" >
				<b style="font-size:Large; font-weight:bold"> <?php echo $company_name."<br>";?>	</b>
					<?php
					echo  $comany_details;
					?><br>   
			
			</td>
        </tr>
        <tr>
            <td colspan="9" align="center" valign="top" >
				<b><u> Benificiary Certificate</u></b>
            </td>
        </tr>
		<br>
		<tr style="height: 30px;"></tr>
	
	</table>

    <table width='500' border="1" cellspacing="0" cellpadding="0">
		<tr>
			<td colspan="9" valign="top"  align="left" > Shipper / Exporter : <br><?php echo $company_name."<br>".$comany_details;?></td>
		</tr>
	</table>
	<br><br><br>
	<table width='500' border="1"  cellspacing="0" cellpadding="0">
		<tr>
			<td colspan="9" valign="top"  align="left" > Applicant : <br> <?php echo $buyer_name_arr[$buyer_id]["BUYER_NAME"]."<br>".$buyer_name_arr[$buyer_id]["ADDRESS_1"];?></td>
		</tr>
	</table>

	<table width='800' cellspacing="0" cellpadding="0">
		<tr style="height: 50px;"></tr>
		<tr>
			<td colspan="9" valign="top"  align="left" >WE HEREBY CERTIFY THAT THE FULL QUANTITY AND QUALITY GOODS RECEIVED BY APPLICANT WHICH SHOWN AT DELIVERY CHALLAN AS PER COMMERCIAL INVOICE NO: <b><?=$invoice_no?></b>  DATE :<b>  <?=$invoice_date?></b>  UNDER BELOW  BTB L/C and EXPORT L/C. </td>
		</tr>
		<tr style="height: 50px;"></tr>
		<tr>
			<td colspan="3" valign="top" align="left">DELIVERY AGAINST BTB L/C NO :</td>
			<td colspan="3" valign="top" align="left"><?=$lc_sc_no?></td>
			<td colspan="3" valign="top" align="left">DATE :<?=$lc_sc_date?></td>
		</tr>
		<tr>
			<td colspan="3" valign="top" align="left">PROFORMA INVOICE NO :</td>
			<td colspan="3" valign="top" align="left"><?=rtrim($pi_number,", ")?></td>
			<td colspan="3" valign="top" align="left">DATE :<?=rtrim($pi_date,", ")?></td>
		</tr>
		<tr>
			<td colspan="3" valign="top" align="left">EXPORT CONTRACT NO :</td>
			<td colspan="3" valign="top" align="left"><?=$eta_destination_place?></td>
			<td colspan="3" valign="top" align="left">DATE :<?=$etd_destination?></td>
		</tr>


		<tr>
			<td colspan="3" valign="top" align="left">H.S CODE NO :</td>
			<td colspan="3" valign="top" align="left"><?=$hs_code?></td>
			<td colspan="3" valign="top" align="left">Vat No :<?=$vat_number?></td>
		</tr>
		<tr style="height: 70px;"></tr>
		<tr>
			<td colspan="9" valign="top" align="left">FOR & BEHALF OF :</td>
		</tr>
	</table>

	<br><br><br>
	<span style='page-break-after: always;'></span>
	<!-------------------------- 1st Button End------------------------------>

	<table width='800' cellspacing="0" cellpadding="0">
		<tr>
			<td  align="left" width="70"><img src="../../<? echo $company_logo; ?>" height="70" width="70"></td>
            <td colspan="8" valign="top" align="center" style="font-size:Large;" >
				<b style="font-size:Large; font-weight:bold"> <?php echo $company_name."<br>";?>	</b>
					<?php
					echo  $comany_details;
					?><br> 		
			</td>
        </tr>
		<tr style="height: 30px;"></tr>
		<tr>
			<td colspan="6" valign="top" align="left">Ref: <?=$internal_file_no?> </td>
			<td colspan="3" valign="top" align="right">DATE: <?=change_date_format(date("Y/m/d"));?></td>
		</tr>
        <tr>
            <td colspan="12" valign="top" align="left" >
			<b><u></u></b> <br>
			To <br>
			<? echo $custom_designation[$bank_name_arr[$lien_bank]["DESIGNATION"]]."<br>".$bank_name_arr[$lien_bank]["BANK_NAME"]."<br>".$bank_name_arr[$lien_bank]["ADDRESS"];?>
			</td>
        </tr>
		<tr style="height: 50px;"></tr>
		<tr>
			<td colspan="12" valign="top" align="center"><u><b>Sub: Request for allowing bill discounting facilities </b></u></td>
		</tr>
		<tr style="height: 50px;"></tr>
		<tr>
			<td colspan="12" valign="top"  align="left" >Dear Sir,<br>With reference to the captioned subject, we would like to draw your kind attention that we have transaction with your bank since long. We are engaged in <b> <?=$export_item_category[$SqlDtlsResult[0]["ITEM_CATEGORY_ID"]];?> </b> supplying business with our valued client <b> <?=$buyer_name_arr[$buyer_id]["BUYER_NAME"]?></b> We are getting local l/c from them regularly. Our L/C No:<b> <?=$lc_sc_no?></b> Date: <b> <?=$lc_sc_date?></b> In this regard we need to discount facility that bill against this L/C which ultimately issued by your bank. </td>
		</tr>
		<tr style="height: 20px;"></tr>
		<tr>
			<td colspan="12" valign="top"  align="left" >As such you are requested to please adjust our import commitment at <b> <?=$max_btb_limit?>% </b> margin from this bill, so that we may run our business smoothly</td>
		</tr>
		<tr style="height: 30px;"></tr>
		<tr>
			<td colspan="12" valign="top"  align="left" >Your kind co-operation in this regard will be highly appreciated.</td>
		</tr>
		<tr style="height: 50px;"></tr>
		<tr>
			<td colspan="9" valign="top" align="left">Thanking you</td>
		</tr>
	</table>

	<br><br><br>
	<span style='page-break-after: always;'></span>
		<!-------------------------- 2st Button End------------------------------>
	<? 
	$limit=2;
	for($i=1; $limit>=$i; $i++){
		if($i==1){
			$list="1st";
			$lists="2nd";
		}else{
			$list="2nd";
			$lists="1st";
		}
		?>
		<div style="width: 805px;border: 1px solid black;padding: 10px">
			<table width='800' cellspacing="0" cellpadding="0" >
				<tr style="height: 30px;"></tr>
				<tr>
					<td colspan="12" valign="top" align="center" style="font-size:Large;"><b>Bill of Exchange</b> </td>
				</tr>
				<tr style="height: 30px;"></tr>
				<tr>
					<td colspan="6" valign="top" align="left">Exchange for <b> <?echo $currency[$currency_name]." ".$currency_symbolArr[$currency_name]?> <?=$invoice_value?></b></td>
					<td colspan="6" valign="top" align="right">DATE:<b> <?=change_date_format(date("Y/m/d"));?></b> </td>
				</tr>
				<tr style="height: 40px;"></tr>
				<tr>
					<td colspan="12" valign="top" align="left"><b><?=$pay_term[$pay_term_id]?></b>  from the date of Acceptance of this <?=$list?> Bill of Exchange ( <?=$lists?> of the same tenor and date being unpaid ) please pay to the order of <b> <?=$bank_name_arr[$lien_bank]["BANK_NAME"].", ".$bank_name_arr[$lien_bank]["ADDRESS"]?></b> The sum of <b> <? echo number_to_words(def_number_format($invoice_value,2,""),"USD", "CENTS")." ONLY";?></b>  Value Received and Change the same to the Account of <b><?=$company_name?></b>. Drawn under <b> <?=$bank_name_arr[$issuing_bank_name]["BANK_NAME"].", ".$bank_name_arr[$issuing_bank_name]["ADDRESS"]?></b>. Letter of Credit on <b><?=$lc_sc_no?></b> Date:<b> <?=$lc_sc_date?></b> To <b><?=$company_name?> </b> ADDRESS : <b><?=$com_dtls?> </b></td>
				</tr>

				<tr style="height: 50px;"></tr>
				<tr>
					<td colspan="6" valign="top" align="left"></td>
					<td colspan="3" valign="top" align="center"> <b><?=$company_name?></b></td>
					<td colspan="3" valign="top" align="left"></td>
				</tr>
				<tr style="height: 50px;"></tr>
				<tr>
					<td colspan="6" valign="top" align="left"></td>
					<td colspan="3" valign="top" style="border-top: 1px solid black;" align="center">Authorised Signature</td>
					<td colspan="3" valign="top" align="left"></td>
				</tr>
				
			</table>
			<br><br><br>
		</div>	
		<?
	}?>
	 <span style='page-break-after: always;'></span>
		<!-------------------------- 3rd Button End------------------------------>

	<table width='800' cellspacing="0" cellpadding="0">
		<tr>
			<td  align="left" width="70"><img src="../../<? echo $company_logo; ?>" height="70" width="70"></td>
            <td colspan="8" valign="top" align="center" >
				<b style="font-size:Large; font-weight:bold"> <?php echo $company_name."<br>";?>	</b>
					<?php
					echo  $comany_details;
					?><br>   
			</td>
        </tr>
		<tr style="height: 10px;"></tr>
		<tr style="height: 10px;">
			<td colspan="9" style="border-top: 2px solid black"></td>
	    </tr>
		<tr>
			<td colspan="3" valign="top" align="left">Ref: <b><?=$internal_file_no?> </b></td>
			<td colspan="3" valign="top" align="left"><b><u>CERTIFICATE OF ORIGIN</u></b> </td>
			<td colspan="3" valign="top" align="right">DATE:<b> <?=change_date_format(date("Y/m/d"));?></b></td>
		</tr>
		<tr style="height: 30px;"></tr>
		<br>
		<tr>
            <td colspan="9" align="left" valign="top" >To </td>
        </tr>
		<br>
		<tr style="height: 30px;"></tr>
		<tr>
			<td colspan="9" valign="top"  align="left" >  <?php echo $buyer_name_arr[$buyer_id]["BUYER_NAME"]."<br>".$buyer_name_arr[$buyer_id]["ADDRESS_1"];?></td>
		</tr>
		<tr style="height: 50px;"></tr>
		<tr>
			<td colspan="3" valign="top" align="left">DELIVERY AGAINST BTB L/C NO:</td>
			<td colspan="3" valign="top" align="left"><?=$lc_sc_no?></td>
			<td colspan="3" valign="top" align="left">DATE :<?=$lc_sc_date?></td>
		</tr>
		<tr>
			<td colspan="3" valign="top" align="left">PROFORMA INVOICE NO :</td>
			<td colspan="3" valign="top" align="left"><?=rtrim($pi_number,", ")?></td>
			<td colspan="3" valign="top" align="left">DATE :<?=rtrim($pi_date,", ")?></td>
		</tr>
		<tr>
			<td colspan="3" valign="top" align="left">EXPORT CONTRACT NO :</td>
			<td colspan="3" valign="top" align="left"><?=$eta_destination_place?></td>
			<td colspan="3" valign="top" align="left">DATE :<?=$etd_destination?></td>
		</tr>
		<tr>
			<td colspan="3" valign="top" align="left">H.S CODE NO :</td>
			<td colspan="3" valign="top" align="left"><?=$hs_code?></td>
			<td colspan="3" valign="top" align="left">Vat No :<?=$vat_number?></td>
		</tr>
		<tr style="height: 30px;"></tr>
		<tr>
			<td colspan="9" align="center">
				<table width='760' border='1' cellspacing="0" cellpadding="0">
					<thead>
						<tr>
							<th width="30" rowspan="2">ID</th>
							<th  width="200">Description of Goods</th>
							<th width="150" rowspan="2">PI NO PI DATE</th>
							<th width="80" rowspan="2">Size</th>
							<th width="80" rowspan="2">Quantity</th>
							<th width="80" rowspan="2">Quantity (Roll/Ctn)</th>
						</tr>
						<tr>
							<th><?=$export_item_category[$SqlDtlsResult[0]["ITEM_CATEGORY_ID"]];?> For 100% Export Oriented Readymade Garments Industry</th>
						</tr>
					</thead>
					<tbody>
						<?					
						$i=1; 
						foreach($SqlDtlsResult as $row){
							if($row["ITEM_CATEGORY_ID"]==45){
								$desc=$item_group_arr[$row["ITEM_GROUP"]]." ".$trims_section[$row["SECTION"]];
							}else{
								$desc=$row["FAB_DESC"];
							}
							?>
							<tr>
								<td  align="center"><?=$i?></td>
								<td  align="left"><?=$desc?></td>
								<td align="center"><?=$row["PI_NUMBER"]."<br>".$row["PI_DATE"]?></td>
								<td><?="";?></td>
								<td  align="center"><?=$row["QTY"]." ".$unit_of_measurement[$row["UOM"]]?></td>
								<td  align="center"><? if($row["ITEM_CATEGORY_ID"]==10){ echo number_format($row["QTY"]/29);}else{echo $row["CARTON_QTY"]; }?></td>
							</tr>
							<?
							$i++;
							$Total_Qty+=$row["QTY"];
						}?>
					</tbody>
					<tfoot>
						<tr>
							<th colspan="4" align="right"> Total</th>
							<th  align="center"><?=$Total_Qty?></th>
							<th></th>
						</tr>
					</tfoot>
				</table>
			</td>
		</tr>
		<tr style="height: 70px;"></tr>
		<tr>
			<td colspan="9" valign="top" align="left">WE CERTIFIED AND DECLARED THAT AFORESAID MERCHANDISE GOODS SUPPLIED AGST. ABOVE NOTED LETTER OF CREDIT ARE ORIGIN OF BANGLADESH. </td>
		</tr>
		<tr style="height: 50px;"></tr>
		<tr>
			<td colspan="9" valign="top" align="left">THANKING YOU,</td>
		</tr>
	</table>

	<br><br><br>
	<span style='page-break-after: always;'></span>
	<!-------------------------- 4th Button End------------------------------>

	<table width='800' cellspacing="0" cellpadding="0">
		<tr>
			<td  align="left" width="70"><img src="../../<? echo $company_logo; ?>" height="70" width="70"></td>
            <td colspan="8" valign="top" align="center" >
				<b style="font-size:Large; font-weight:bold"> <?php echo $company_name."<br>";?>	</b>
					<?php
					echo  $comany_details;
					?><br>   
			</td>
        </tr>
		<tr style="height: 10px;"></tr>
		<tr style="height: 10px;">
			<td colspan="9" style="border-top: 2px solid black"></td>
	    </tr>
		<tr>
			<td colspan="3" valign="top" align="left">Ref: <b><?=$internal_file_no?> </b></td>
			<td colspan="3" valign="top" align="left"><b><u>COMMERCIAL INVOICE</u></b> </td>
			<td colspan="3" valign="top" align="left">DATE:<b> <?=change_date_format(date("Y/m/d"));?></b></td>
		</tr>
		<tr style="height: 30px;"></tr>
		<br>
		<tr>
            <td colspan="9" align="left" valign="top" >To </td>
        </tr>
		<br>
		<tr style="height: 30px;"></tr>
		<tr>
		<td colspan="9" valign="top"  align="left" >  <?php echo $buyer_name_arr[$buyer_id]["BUYER_NAME"]."<br>".$buyer_name_arr[$buyer_id]["ADDRESS_1"];?></td>
		</tr>
		<tr style="height: 50px;"></tr>
		<tr>
			<td colspan="3" valign="top" align="left">DELIVERY AGAINST BTB L/C NO:</td>
			<td colspan="3" valign="top" align="left"><?=$lc_sc_no?></td>
			<td colspan="3" valign="top" align="left">DATE :<?=$lc_sc_date?></td>
		</tr>
		<tr>
			<td colspan="3" valign="top" align="left">PROFORMA INVOICE NO :</td>
			<td colspan="3" valign="top" align="left"><?=rtrim($pi_number,", ")?></td>
			<td colspan="3" valign="top" align="left">DATE :<?=rtrim($pi_date,", ")?></td>
		</tr>
		<tr>
			<td colspan="3" valign="top" align="left">EXPORT CONTRACT NO :</td>
			<td colspan="3" valign="top" align="left"><?=$eta_destination_place?></td>
			<td colspan="3" valign="top" align="left">DATE :<?=$etd_destination?></td>
		</tr>
		<tr>
			<td colspan="3" valign="top" align="left">FRIGHT STATUS: </td>
			<td colspan="3" valign="top" align="left"> FREIGHT PREPAID</td>
			<td colspan="3" valign="top" align="left">VAT NO:<?=$vat_number?></td>
		</tr>
		<tr>
			<td colspan="3" valign="top" align="left">TRADE TERM: </td>
			<td colspan="3" valign="top" align="left"> <?=$incoterm[$inco_term_id]?></td>
			<td colspan="3" valign="top" align="left"></td>
		</tr>
		<tr style="height: 30px;"></tr>
		<tr>
			<td colspan="9" align="left">
				<table width='760' border='1' cellspacing="0" cellpadding="0">
					<thead>
						<tr>
							<th width="30" rowspan="2">ID</th>
							<th  width="200">Description of Goods</th>
							<th width="150" rowspan="2">PI NO PI DATE</th>
							<th width="80" rowspan="2">HS Code</th>
							<th width="80" rowspan="2">Quantity</th>
							<th width="80" rowspan="2">Unit Price</th>
							<th width="80" rowspan="2">Amount</th>
						</tr>
						<tr>
							<th><?=$export_item_category[$SqlDtlsResult[0]["ITEM_CATEGORY_ID"]];?> For 100% Export Oriented Readymade Garments Industry</th>
						</tr>
					</thead>
					<tbody>
						<?
						$SqlDtlResult=sql_select($order_sql);
						$i=1;
						foreach($SqlDtlResult as $row){
							if($row["ITEM_CATEGORY_ID"]==45){
								$desc=$item_group_arr[$row["ITEM_GROUP"]]." ".$trims_section[$row["SECTION"]];
							}else{
								$desc=$row["FAB_DESC"];
							}
							?>
							<tr>
								<td  align="center"><?=$i?></td>
								<td  align="center"><?=$desc?></td>
								<td align="center"><?=$row["PI_NUMBER"]."<br>".$row["PI_DATE"]?></td>
								<td><?="";?></td>
								<td  align="center"><?=$row["QTY"]." ".$unit_of_measurement[$row["UOM"]]?></td>
								<td  align="center"><?=number_format($row["RATE"],2)?></td>
								<td  align="center"><?="$ ".number_format($row["AMOUNT"],2)?></td>
							</tr>
							<?
							$i++;
							$TotalQty+=$row["QTY"];
							$totalamount+=$row["AMOUNT"];
						}?>
					</tbody>
					<tfoot>
						<tr>
							<th colspan="4" align="right"> Total</th>
							<th  align="center"><?=$TotalQty?></th>
							<th  align="center"></th>
							<th  align="center"><?="$ ".number_format($totalamount,2)?></th>
						</tr>
					</tfoot>
				</table>
			</td>
		</tr>
		<tr style="height: 50px;"></tr>
		<tr>
			<td colspan="9" valign="top" align="left"><b>Inward (US$): <? echo number_to_words(def_number_format($totalamount,2,""),"USD", "CENTS")." ONLY";?></b>  </td>
		</tr>
		<tr style="height: 50px;"></tr>
		<tr>
			<td colspan="9" valign="top" align="left">WE CERTIFIED AND DECLARED THAT AFORESAID MERCHANDISE GOODS SUPPLIED AGST. ABOVE NOTED LETTER OF CRADIT ARE ORIGIN OF BANGLADSH</td>
		</tr>
		<tr style="height: 30px;"></tr>
		<tr>
			<td colspan="9" valign="top" align="left">THANKING YOU,</td>
		</tr>
	</table>

	<br><br><br>
	<span style='page-break-after: always;'></span>
	<!-------------------------- 5th Button End------------------------------>

	<table width='800' cellspacing="0" cellpadding="0">
		 <? $SqlPackingDtlsResult=sql_select($order_sql);?>
		<tr>
			<td  align="left" width="70"><img src="../../<? echo $company_logo; ?>" height="70" width="70"></td>
            <td colspan="8" valign="top" align="center" >
				<b style="font-size:Large; font-weight:bold"> <?php echo $company_name."<br>";?>	</b>
					<?php
					echo  $comany_details;
					?><br>   
			</td>
        </tr>
		<tr style="height: 10px;"></tr>
		<tr style="height: 10px;">
			<td colspan="9" style="border-top: 2px solid black"></td>
	    </tr>
		<tr>
			<td colspan="3" valign="top" align="left">Ref: <b><?=$internal_file_no?> </b></td>
			<td colspan="3" valign="top" align="left"><b><u>PACKING LIST</u></b> </td>
			<td colspan="3" valign="top" align="right">DATE:<b> <?=change_date_format(date("Y/m/d"));?></b></td>
		</tr>
		<tr style="height: 30px;"></tr>
		<br>
		<tr>
            <td colspan="9" align="left" valign="top" >To </td>
        </tr>
		<br>
		<tr style="height: 30px;"></tr>
		<tr>
		<td colspan="9" valign="top"  align="left" >  <?php echo $buyer_name_arr[$buyer_id]["BUYER_NAME"]."<br>".$buyer_name_arr[$buyer_id]["ADDRESS_1"];?></td>
		</tr>
		<tr style="height: 50px;"></tr>
		<tr>
			<td colspan="3" valign="top" align="left">DELIVERY AGAINST BTB L/C NO:</td>
			<td colspan="3" valign="top" align="left"><?=$lc_sc_no?></td>
			<td colspan="3" valign="top" align="left">DATE :<?=$lc_sc_date?></td>
		</tr>
		<tr>
			<td colspan="3" valign="top" align="left">PROFORMA INVOICE NO :</td>
			<td colspan="3" valign="top" align="left"><?=rtrim($pi_number,", ")?></td>
			<td colspan="3" valign="top" align="left">DATE :<?=rtrim($pi_date,", ")?></td>
		</tr>
		<tr>
			<td colspan="3" valign="top" align="left">EXPORT CONTRACT NO :</td>
			<td colspan="3" valign="top" align="left"><?=$eta_destination_place?></td>
			<td colspan="3" valign="top" align="left">DATE :<?=$etd_destination?></td>
		</tr>
		<tr>
			<td colspan="3" valign="top" align="left">H.S CODE NO :</td>
			<td colspan="3" valign="top" align="left"><?=$hs_code?></td>
			<td colspan="3" valign="top" align="left">Vat No :<?=$vat_number?></td>
		</tr>
		<tr style="height: 30px;"></tr>
		<tr>
			<td colspan="9" align="center">
				<table width='760' border='1' cellspacing="0" cellpadding="0">
					<thead>
						<tr>
							<th width="30" rowspan="2">ID</th>
							<th  width="200">Description of Goods</th>
							<th width="150" rowspan="2">PI NO PI DATE</th>
							<th width="80" rowspan="2">Size</th>
							<th width="80" rowspan="2">Quantity</th>
							<th width="80" rowspan="2">Quantity (Roll/Ctn)</th>
						</tr>
						<tr>
							<th><?=$export_item_category[$SqlPackingDtlsResult[0]["ITEM_CATEGORY_ID"]];?> For 100% Export Oriented Readymade Garments Industry</th>
						</tr>
					</thead>
					<tbody>
						<?						
						$i=1;$Total_Qty=0;
						foreach($SqlPackingDtlsResult as $row){
							if($row["ITEM_CATEGORY_ID"]==45){
								$desc=$item_group_arr[$row["ITEM_GROUP"]]." ".$trims_section[$row["SECTION"]];
							}else{
								$desc=$row["FAB_DESC"];
							}
							?>
							<tr>
								<td  align="center"><?=$i?></td>
								<td  align="center"><?=$desc?></td>
								<td align="center"><?=$row["PI_NUMBER"]."<br>".$row["PI_DATE"]?></td>
								<td><?="";?></td>
								<td  align="center"><?=$row["QTY"]." ".$unit_of_measurement[$row["UOM"]]?></td>
								<td  align="center"><? if($row["ITEM_CATEGORY_ID"]==10){ echo number_format($row["QTY"]/29);}else{echo $row["CARTON_QTY"]; }?></td>
							</tr>
							<?
							$i++;
							$Total_Qty+=$row["QTY"];
						}?>
					</tbody>
					<tfoot>
						<tr>
							<th colspan="4" align="right"> Total</th>
							<th  align="center"><?=$Total_Qty?></th>
							<th></th>
						</tr>
					</tfoot>
				</table>
			</td>
		</tr>
		<tr style="height: 70px;"></tr>
		<tr>
			<td colspan="9" valign="top" align="left">WE DO HEREBY CERTIFY THAT THE GOODS ARE DELEVERED STRICTLY ACCORDANCE WITH THE ABOVE MENTIONED BENEFICIARY'S P/I NO.</td>
		</tr>
		<tr style="height: 50px;"></tr>
		<tr>
			<td colspan="9" valign="top" align="left">THANKING YOU,</td>
		</tr>
	</table>

	<br><br><br>
	<span style='page-break-after: always;'></span>
	<!-------------------------- 6th Button End------------------------------>
	   
	<table width='800' cellspacing="0" cellpadding="0">
		<?$SqlDelveryDtlsResult=sql_select($order_sql);?>
		<tr>
			<td  align="left" width="70"><img src="../../<? echo $company_logo; ?>" height="70" width="70"></td>
            <td colspan="8" valign="top" align="center" >
				<b style="font-size:Large; font-weight:bold"> <?php echo $company_name."<br>";?></b>
					<?php echo  $comany_details;?>
				<br>   
			</td>
        </tr>
		<tr style="height: 10px;"></tr>
		<tr style="height: 10px;">
			<td colspan="9" style="border-top: 2px solid black"></td>
	    </tr>
		<tr>
			<td colspan="3" valign="top" align="left">Ref: <b><?=$internal_file_no?> </b></td>
			<td colspan="3" valign="top" align="left"><b><u>DELIVERY CHALAN</u></b> </td>
			<td colspan="3" valign="top" align="right">DATE:<b> <?= change_date_format(date("Y/m/d"));?></b></td>
		</tr>
		<tr style="height: 30px;"></tr>
		<br>
		<tr>
            <td colspan="9" align="left" valign="top" >To </td>
        </tr>
		<br>
		<tr style="height: 30px;"></tr>
		<tr>
		<td colspan="9" valign="top"  align="left" >  <?php echo $buyer_name_arr[$buyer_id]["BUYER_NAME"]."<br>".$buyer_name_arr[$buyer_id]["ADDRESS_1"];?></td>
		</tr>
		<tr style="height: 50px;"></tr>
		<tr>
			<td colspan="3" valign="top" align="left">DELIVERY AGAINST BTB L/C NO:</td>
			<td colspan="3" valign="top" align="left"><?=$lc_sc_no?></td>
			<td colspan="3" valign="top" align="left">DATE :<?=$lc_sc_date?></td>
		</tr>
		<tr>
			<td colspan="3" valign="top" align="left">PROFORMA INVOICE NO :</td>
			<td colspan="3" valign="top" align="left"><?=rtrim($pi_number,", ")?></td>
			<td colspan="3" valign="top" align="left">DATE :<?=rtrim($pi_date,", ")?></td>
		</tr>
		<tr>
			<td colspan="3" valign="top" align="left">EXPORT CONTRACT NO :</td>
			<td colspan="3" valign="top" align="left"><?=$eta_destination_place?></td>
			<td colspan="3" valign="top" align="left">DATE :<?=$etd_destination?></td>
		</tr>
		<tr>
			<td colspan="3" valign="top" align="left">H.S CODE NO :</td>
			<td colspan="3" valign="top" align="left"><?=$hs_code?></td>
			<td colspan="3" valign="top" align="left">Vat No :<?=$vat_number?></td>
		</tr>
		<tr style="height: 30px;"></tr>
		<tr>
			<td colspan="9">Consignee Bank Name And Address: <?=$issuing_bank." ".$bank_name_arr[$issuing_bank_name]["ADDRESS"]?></td>
		</tr>
		<tr style="height: 30px;"></tr>
		<tr>
			<td colspan="9" align="center">
				<table width='760' border='1' cellspacing="0" cellpadding="0">
					<thead>
						<tr>
							<th width="30" rowspan="2">ID</th>
							<th  width="200">Description of Goods</th>
							<th width="150" rowspan="2">PI NO PI DATE</th>
							<th width="80" rowspan="2">Size</th>
							<th width="80" rowspan="2">Quantity</th>
							<th width="80" rowspan="2">Quantity (Roll/Ctn)</th>
						</tr>
						<tr>
							<th><?=$export_item_category[$SqlDelveryDtlsResult[0]["ITEM_CATEGORY_ID"]];?> For 100% Export Oriented Readymade Garments Industry</th>
						</tr>
					</thead>
					<tbody>
						<?
						$i=1;$Total_Qty=0;
						foreach($SqlDelveryDtlsResult as $row){
							if($row["ITEM_CATEGORY_ID"]==45){
								$desc=$item_group_arr[$row["ITEM_GROUP"]]." ".$trims_section[$row["SECTION"]];
							}else{
								$desc=$row["FAB_DESC"];
							}
							?>
							<tr>
								<td  align="center"><?=$i?></td>
								<td  align="center"><?=$desc?></td>
								<td align="center"><?=$row["PI_NUMBER"]."<br>".$row["PI_DATE"]?></td>
								<td><?="";?></td>
								<td  align="center"><?=$row["QTY"]." ".$unit_of_measurement[$row["UOM"]]?></td>
								<td  align="center"><? if($row["ITEM_CATEGORY_ID"]==10){ echo number_format($row["QTY"]/29);}else{echo $row["CARTON_QTY"]; }?></td>
							</tr>
							<?
							$i++;
							$Total_Qty+=$row["QTY"];
						}?>
					</tbody>
					<tfoot>
						<tr>
							<th colspan="4" align="right"> Total</th>
							<th  align="center"><?=$Total_Qty?></th>
							<th></th>
						</tr>
					</tfoot>
				</table>
			</td>
		</tr>
		<tr style="height: 70px;"></tr>
		<tr>
		<td colspan="9" valign="top" align="left">WE DO HEREBY CERTIFY THAT THE GOODS ARE DELEVERED STRICTLY ACCORDANCE WITH THE ABOVE MENTIONED BENEFICIARY'S P/I NO.</td>
		</tr>
		<tr style="height: 50px;"></tr>
		<tr>
			<td colspan="9" valign="top"  align="left">
				<table border='1' width='760' cellspacing="0" cellpadding="0" reral="all">
					<tr style="height: 120px;" >
						<td  style="vertical-align: top;"> Carried by: Truck <br>Freight Status: Freight Prepaid</td>
						<td align="center" style="vertical-align: top;">For and On Behalf of</td>
					</tr>
				</table>
		   </td>
		</tr>
		<tr style="height: 50px;"></tr>
		<tr>
			<td colspan="9" valign="top" align="left">WE UNDERSTAND, HEARBY ACKNOWLEDGE THAT WE HAVE RECEOVED THE ABOVE MENTION PI NO'S GOODS CONDITION AND FULL QTY AGAINST BBL/C NO:<b><?=$lc_sc_no?></b> Dt:<b> <?=$lc_sc_date?></b></td>
		</tr>
		<tr style="height: 120px;"></tr>
		<tr>
			<td colspan="7" valign="top"  align="right"> </td>
			<td colspan="2" valign="top" style="border-top: 1px solid black;" align="center"> RECEIVER SIGNATURE AND SEAL </td>
		</tr>
	</table>

	<br><br><br>
	<span style='page-break-after: always;'></span>
	<!-------------------------- 7th Button End------------------------------>

	<table width='800' cellspacing="0" cellpadding="0">
		<?$SqlTruckDtlsResult=sql_select($order_sql);?>
		<tr>
			<td  align="left" width="70"><img src="../../<? echo $company_logo; ?>" height="70" width="70"></td>
            <td colspan="8" valign="top" align="center" >
				<b style="font-size:Large; font-weight:bold"> <?php echo $company_name."<br>";?></b>
					<?php echo  $comany_details;?>
				<br>   
			</td>
        </tr>
		<tr style="height: 10px;"></tr>
		<tr style="height: 10px;">
			<td colspan="9" style="border-top: 2px solid black"></td>
	    </tr>
		<tr>
			<td colspan="3" valign="top" align="left">Ref: <b><?=$internal_file_no?> </b></td>
			<td colspan="3" valign="top" align="left"><b><u>Truck Chalan</u></b> </td>
			<td colspan="3" valign="top" align="right">DATE:<b> <?=change_date_format(date("Y/m/d"));?></b></td>
		</tr>
		<tr style="height: 30px;"></tr>
		<tr>
			<td colspan="9">
				<table border="1" width='800'  cellspacing="0" cellpadding="0">
					<tr>
						<td colspan="4" valign="top" ><b> NAME OF EXPORTER:</b> <?="<br>".$company_name."<br>".$comany_details?></td>
						<td 4olspan="4" valign="top" ><b>NAME OF IMPORTER</b><?="<br>".$buyer_name_arr[$buyer_id]["BUYER_NAME"]."<br>".$buyer_name_arr[$buyer_id]["ADDRESS_1"]."<br>".$country_name_arr[$country_id];?></td>
					</tr>
					<tr>
						<td colspan="4" valign="top" ><b> FACTORY:</b> <?="<br>".$company_name."<br>".$comany_details?></td>
						<td 4olspan="4" valign="top" ><b>LC ISSUING BANK:</b><?="<br>".$issuing_bank."<br>".$bank_name_arr[$issuing_bank_name]["ADDRESS"];?></td>
					</tr>
				</table>
			</td>
		</tr>
		<tr style="height: 50px;"></tr>
		<tr>
			<td colspan="3" valign="top" align="left">DELIVERY AGAINST BTB L/C NO:</td>
			<td colspan="3" valign="top" align="left"><?=$lc_sc_no?></td>
			<td colspan="3" valign="top" align="lift">DATE :<?=$lc_sc_date?></td>
		</tr>
		<tr>
			<td colspan="3" valign="top" align="left">PROFORMA INVOICE NO :</td>
			<td colspan="3" valign="top" align="left"><?=rtrim($pi_number,", ")?></td>
			<td colspan="3" valign="top" align="lift">DATE :<?=rtrim($pi_date,", ")?></td>
		</tr>
		<tr>
			<td colspan="3" valign="top" align="left">EXPORT CONTRACT NO :</td>
			<td colspan="3" valign="top" align="left"><?=$eta_destination_place?></td>
			<td colspan="3" valign="top" align="left">DATE :<?=$etd_destination?></td>
		</tr>
		<tr>
			<td colspan="3" valign="top" align="left">H.S CODE NO :</td>
			<td colspan="3" valign="top" align="left"><?=$hs_code?></td>
			<td colspan="3" valign="top" align="lift">Vat No :<?=$vat_number?></td>
		</tr>
		<tr style="height: 30px;"></tr>
		<tr>
			<td colspan="9">Consignee Bank Name And Address: <?=$issuing_bank."<br>".$bank_name_arr[$issuing_bank_name]["ADDRESS"]?></td>
		</tr>
		<tr style="height: 30px;"></tr>
		<tr>
			<td colspan="9" align="center">
				<table width='760' border='1' cellspacing="0" cellpadding="0">
					<thead>
						<tr>
							<th width="30" rowspan="2">ID</th>
							<th  width="200">Description of Goods</th>
							<th width="150" rowspan="2">PI NO PI DATE</th>
							<th width="80" rowspan="2">Size</th>
							<th width="80" rowspan="2">Quantity</th>
							<th width="80" rowspan="2">Quantity (Roll/Ctn)</th>
						</tr>
						<tr>
							<th><?=$export_item_category[$SqlTruckDtlsResult[0]["ITEM_CATEGORY_ID"]];?> For 100% Export Oriented Readymade Garments Industry</th>
						</tr>
					</thead>
					<tbody>
						<?
						$i=1;$Total_Qty=0;
						foreach($SqlTruckDtlsResult as $row){
							if($row["ITEM_CATEGORY_ID"]==45){
								$desc=$item_group_arr[$row["ITEM_GROUP"]]." ".$trims_section[$row["SECTION"]];
							}else{
								$desc=$row["FAB_DESC"];
							}
							?>
							<tr>
								<td  align="center"><?=$i?></td>
								<td  align="center"><?=$desc?></td>
								<td align="center"><?=$row["PI_NUMBER"]."<br>".$row["PI_DATE"]?></td>
								<td><?="";?></td>
								<td  align="center"><?=$row["QTY"]." ".$unit_of_measurement[$row["UOM"]]?></td>
								<td  align="center"><? if($row["ITEM_CATEGORY_ID"]==10){ echo number_format($row["QTY"]/29);}else{echo $row["CARTON_QTY"]; }?></td>
							</tr>
							<?
							$i++;
							$Total_Qty+=$row["QTY"];
						}?>
					</tbody>
					<tfoot>
						<tr>
							<th colspan="4" align="right"> Total</th>
							<th  align="center"><?=$Total_Qty?></th>
							<th></th>
						</tr>
					</tfoot>
				</table>
			</td>
		</tr>
		<tr style="height: 40px;"></tr>
		<tr>
			<td colspan="9" valign="top" align="left">Freight Prepaid</td>
		</tr>
		<tr style="height: 10px;">
		 
	    </tr>
		<tr>
			<td colspan="9" valign="top"  align="left">
				<table border='1' width='760' cellspacing="0" cellpadding="0" reral="all">
					<tr style="height: 30px;" >
					<td  align="left" colspan="3">Truck No:</td>
					<td  align="left" colspan="3"> Driver Name:</td>
					<td  align="left" colspan="3"> Driver Mobile No:</td>
					</tr>
				</table>
		   </td>
		</tr>
		<tr style="height: 30px;"></tr>
		<tr>
			<td colspan="9" valign="top" align="left">Thank You</td>
		</tr>
		<tr style="height: 20px;"></tr>
		<tr>
			<td colspan="9" valign="top" align="left">For Four H Transport and Agency</td>
		</tr>
		<tr style="height: 120px;"></tr>
		<tr>
			<td colspan="2" valign="top" style="border-top: 1px solid black;"  align="center">AUTHORISED SIGNATURE </td>
			<td colspan="7" valign="top">  </td>
		</tr>
	</table>

	<br><br><br>
	<span style='page-break-after: always;'></span>
	<!-------------------------- 8th Button End------------------------------>

	<table width='800' cellspacing="0" cellpadding="0">
		<tr>
			<td  align="left" width="70"><img src="../../<? echo $company_logo; ?>" height="70" width="70"></td>
            <td colspan="8" valign="top" align="center" >
				<b style="font-size:Large; font-weight:bold"> <?php echo $company_name."<br>";?></b>
					<?php echo  $comany_details;?>
				<br>   
			</td>
        </tr>
		<tr style="height: 10px;"></tr>
		<tr style="height: 10px;">
			<td colspan="9" style="border-top: 2px solid black"></td>
	    </tr>
		<tr>
			<td colspan="3" valign="top" align="left">Ref: <b><?=$internal_file_no?> </b></td>
			<td colspan="3" valign="top" align="left"><b><u></u></b> </td>
			<td colspan="3" valign="top" align="right">DATE:<b> <?=change_date_format(date("Y/m/d"));?></b></td>
		</tr>
		<tr style="height: 10px;"></tr>
		<br>
		<tr>
            <td colspan="9" align="left" valign="top" >To </td>
        </tr>
		<br>
		<tr style="height: 10px;"></tr>
		<tr>
			<td colspan="9" valign="top"  align="left" ><?php echo $custom_designation[$bank_name_arr[$lien_bank]["DESIGNATION"]]."<br>".$bank_name_arr[$lien_bank]["BANK_NAME"]."<br>".$bank_name_arr[$line_bank_id]["ADDRESS"]?></td>
		</tr>
		<tr style="height: 20px;"></tr>
		
		<tr>
			<td colspan="9" valign="top"  align="left" > <u><b>Sub : Collection of Proceeds</b></u></td>
		</tr>
		<tr style="height: 30px;"></tr>
		<tr>
			<td colspan="9" valign="top"  align="left" >Ref : L/C No.<b> <?=$lc_sc_no?> </b> Date: <b><?=$lc_sc_date?></b> For <?echo $currency[$currency_name]." ".$currency_symbolArr[$currency_name]?> <?=$lc_sc_value?></b> Issued by <b> <?=$bank_name_arr[$issuing_bank_name]["BANK_NAME"].". ".$bank_name_arr[$issuing_bank_name]["ADDRESS"]?>. </b></td>
		</tr>
		<tr style="height: 20px;"></tr>
		<tr>
			<td colspan="9" valign="top"  align="left" >Dear Sir,</td>
		</tr>
		<tr style="height: 30px;"></tr>
		<tr>
			<td colspan="9" valign="top"  align="left" >In Terms of above please enclosed find herewith below mention export shipping documents under aforesaid letter credit <? echo $currency[$currency_name]." ".$currency_symbolArr[$currency_name]?> <?=$lc_sc_value?></b> for negotiation purpose.</td>
		</tr>
		<tr style="height: 30px;"></tr>
		<tr>
			<td colspan="9" style="padding-left: 30px;" >
				<table width='500' border='1' cellspacing="0" cellpadding="0">
					<thead>
						<tr>
							<th width="30" >ID</th>
							<th  width="200">Particulars</th>
							<th width="150" >Nos. of Copy</th>
						</tr>
					</thead>
					<tbody>
							<tr>
								<td  align="center">1</td>
								<td  align="center">Bill Of Exchange</td>
								<td align="center">02</td>
							</tr>
							<tr>
								<td  align="center">2</td>
								<td  align="center">Commercial Invoice</td>
								<td align="center">04</td>
							</tr>
							<tr>
								<td  align="center">1</td>
								<td  align="center">Packing List</td>
								<td align="center">04</td>
							</tr>
							<tr>
								<td  align="center">1</td>
								<td  align="center">L/C (Original and Photocopy)</td>
								<td align="center">02</td>
							</tr>
							<tr>
								<td  align="center">1</td>
								<td  align="center">Delivery Challan</td>
								<td align="center">03</td>
							</tr>
							<tr>
								<td  align="center">1</td>
								<td  align="center">Truck Challan</td>
								<td align="center">02</td>
							</tr>
							<tr>
								<td  align="center">1</td>
								<td  align="center">Certificate of Origin</td>
								<td align="center">01</td>
							</tr>
					</tbody>
				</table>
			</td>
		</tr>
		<tr style="height: 30px;"></tr>
		<tr>
			<td colspan="9" valign="top" align="left">Thanking you</td>
		</tr>
	</table>

	<br><br><br>
	<span style='page-break-after: always;'></span>
	<!-------------------------- 9th Button End------------------------------>

	<table width='800' cellspacing="0" cellpadding="0">
		<tr>
			<td  align="left" width="70"><img src="../../<? echo $company_logo; ?>" height="70" width="70"></td>
            <td colspan="8" valign="top" align="center" >
				<b style="font-size:Large; font-weight:bold"> <?php echo $company_name."<br>";?>	</b>
					<?php
					echo  $comany_details;
					?><br>   
			
			</td>
        </tr>
		<tr style="height: 10px;"></tr>
		<tr style="height: 10px;">
			<td colspan="9" style="border-top: 2px solid black"></td>
	    </tr>
        <tr>
            <td colspan="9" align="center" valign="top" >
				<b><u> Pre Shipment Inspection Certificate</u></b>
            </td>
        </tr>
		<br>
		<tr style="height: 30px;"></tr>
	</table>

	<table width='500' border="1" cellspacing="0" cellpadding="0">
		<tr>
			<td colspan="9" valign="top"  align="left" > Shipper / Exporter : <br><?php echo $company_name."<br>".$com_dtls;?></td>
		</tr>
	</table>
	<br><br><br>
	<table width='500' border="1"  cellspacing="0" cellpadding="0">
		<tr>
			<td colspan="9" valign="top"  align="left" > Applicant : <br> <?php echo $buyer_name_arr[$buyer_id]["BUYER_NAME"]."<br>".$buyer_name_arr[$buyer_id]["ADDRESS_1"];?></td>
		</tr>
	</table>

	<table width='800' cellspacing="0" cellpadding="0">
		<tr style="height: 50px;"></tr>
		<tr>
			<td colspan="9" valign="top"  align="left" >WE HEREBY CERTIFY THAT THE FULL QUANTITY AND QUALITY GOODS RECEIVED BY APPLICANT WHICH SHOWN AT DELIVERY CHALLAN AS PER COMMERCIAL INVOICE NO: <?=$invoice_no?> DATE : <?=$invoice_date?> UNDER BELOW  EXPORT L/C. </td>
		</tr>
		<tr style="height: 50px;"></tr>
		<tr>
			<td colspan="3" valign="top" align="left">DELIVERY AGAINST EXPORT L/C NO :</td>
			<td colspan="3" valign="top" align="left"><?=$lc_sc_no?></td>
			<td colspan="3" valign="top" align="left">DATE :<?=$lc_sc_date?></td>
		</tr>
		<tr>
			<td colspan="3" valign="top" align="left">PROFORMA INVOICE NO :</td>
			<td colspan="3" valign="top" align="left"><?=rtrim($pi_number,", ")?></td>
			<td colspan="3" valign="top" align="left">DATE :<?=rtrim($pi_date,", ")?></td>
		</tr>
		<tr>
			<td colspan="3" valign="top" align="left">EXPORT CONTRACT NO :</td>
			<td colspan="3" valign="top" align="left"><?=$lc_sc_no?></td>
			<td colspan="3" valign="top" align="left">DATE :<?=$lc_sc_date?></td>
		</tr>
		<tr>
			<td colspan="3" valign="top" align="left">H.S CODE NO :</td>
			<td colspan="3" valign="top" align="left"><?=$hs_code?></td>
			<td colspan="3" valign="top" align="left">Vat No :<?=$vat_number?></td>
		</tr>
		<tr style="height: 70px;"></tr>
		<tr>
			<td colspan="9" valign="top" align="left">FOR & ON BEHALF OF :</td>
		</tr>
	</table>

	<br><br><br>
	<span style='page-break-after: always;'></span>
	<!-------------------------- 10st Button End------------------------------>

	

	<?
		$html = ob_get_contents();
		ob_clean();
		foreach (glob("tb*.xls") as $filename) {
		@unlink($filename);
		}
		//---------end------------//
		$name=time();
		$filename="tb".$user_id."_".$name.".xls";
		$create_new_doc = fopen($filename, 'w');
		$is_created = fwrite($create_new_doc, $html);
		echo "$filename####$html";
	exit();	
}

if($action=="invoice_report_print_10")  
{
	extract($_REQUEST);
	// $update_id=$data;
 
	$brand_arr=return_library_array( "SELECT ID, BRAND_NAME FROM LIB_BUYER_BRAND BRAND",'ID','BRAND_NAME');
	$issue_bank_arr=return_library_array( "SELECT ID, BANK_NAME FROM LIB_BANK BRAND",'ID','BANK_NAME');
	$brand_arr=return_library_array( "SELECT ID, BRAND_NAME FROM LIB_BUYER_BRAND BRAND",'ID','BRAND_NAME');
	$applicant_sql=sql_select( "SELECT A.ID, A.BUYER_NAME, A.SHORT_NAME, A.ADDRESS_1 FROM LIB_BUYER A");
	$custom_designation=return_library_array( "SELECT ID, CUSTOM_DESIGNATION FROM LIB_DESIGNATION BRAND",'ID','CUSTOM_DESIGNATION');
	$company_arr = return_library_array("select id, company_name from lib_company ","id","company_name");
	$color_arr=return_library_array( "select id, color_name from lib_color where status_active=1",'id','color_name');
	$payment_method_arr=array(1=>"Bank Submission",2=>"TT");

	foreach($applicant_sql as $row)
	{
		$buyer_name_arr[$row["ID"]]["BUYER_NAME"]=$row["BUYER_NAME"];
		$buyer_name_arr[$row["ID"]]["ADDRESS_1"]=$row["ADDRESS_1"];
	}
	$bank_sql=sql_select( "SELECT A.ID, A.BANK_NAME, A.BRANCH_NAME, A.ADDRESS, A.SWIFT_CODE, A.DESIGNATION FROM LIB_BANK A");
	foreach($bank_sql as $row)
	{
		$bank_name_arr[$row["ID"]]["BANK_NAME"]=$row["BANK_NAME"];
		$bank_name_arr[$row["ID"]]["BRANCH_NAME"]=$row["BRANCH_NAME"];
		$bank_name_arr[$row["ID"]]["ADDRESS"]=$row["ADDRESS"];
		$bank_name_arr[$row["ID"]]["SWIFT_CODE"]=$row["SWIFT_CODE"];
		$bank_name_arr[$row["ID"]]["DESIGNATION"]=$row["DESIGNATION"];
	}
	$bank_account_sql=sql_select( "SELECT ID, ACCOUNT_ID, ACCOUNT_TYPE, ACCOUNT_NO FROM LIB_BANK_ACCOUNT WHERE IS_DELETED=0 ");
	foreach($bank_account_sql as $row)
	{
		$bank_acc_arr[$row["ACCOUNT_ID"]][$row["ACCOUNT_TYPE"]]["ACCOUNT_NO"]=$row["ACCOUNT_NO"];
	}
	$inv_master_data=sql_select("SELECT id, benificiary_id, buyer_id, location_id, invoice_no, invoice_date, exp_form_no, exp_form_date, is_lc, lc_sc_id, bl_no, feeder_vessel, inco_term, inco_term_place, shipping_mode, port_of_entry, port_of_loading, port_of_discharge, main_mark, side_mark, carton_net_weight, carton_gross_weight, cbm_qnty, place_of_delivery, delv_no, consignee, notifying_party, item_description, discount_ammount, bonus_ammount, commission, total_carton_qnty, bl_date, hs_code, mother_vessel, category_no, forwarder_name, etd,co_no, total_measurment, invoice_value, net_invo_value, container_no, seal_no, etd, country_id,commission_percent,bl_rev_date, eta_destination_place, etd_destination, shipping_bill_n, payment_method, composition from com_export_invoice_ship_mst where id=$update_id");
	
	$id=$inv_master_data[0]["ID"];
	$benificiary_id=$inv_master_data[0]["BENIFICIARY_ID"];
	$buyer_id=$inv_master_data[0]["BUYER_ID"];
	$location_id=$inv_master_data[0]["LOCATION_ID"];
	$invoice_no=$inv_master_data[0]["INVOICE_NO"];
	$invoice_date=$inv_master_data[0]["INVOICE_DATE"];
	$invoice_value=$inv_master_data[0]["INVOICE_VALUE"];
	$eta_destination_place=$inv_master_data[0]["ETA_DESTINATION_PLACE"];
	$etd_destination=$inv_master_data[0]["ETD_DESTINATION"];
	$consignee=$inv_master_data[0]["CONSIGNEE"];
	$shipping_bill_n=$inv_master_data[0]["SHIPPING_BILL_N"];
	$payment_method=$inv_master_data[0]["PAYMENT_METHOD"];
	$inco_term=$inv_master_data[0]["INCO_TERM"];
	$inco_term_place=$inv_master_data[0]["INCO_TERM_PLACE"];
	$exp_form_no=$inv_master_data[0]["EXP_FORM_NO"];
	$exp_form_date=$inv_master_data[0]["EXP_FORM_DATE"];
	$place_of_delivery=$inv_master_data[0]["PLACE_OF_DELIVERY"];
	$port_of_discharge=$inv_master_data[0]["PORT_OF_DISCHARGE"];
	$port_of_loading=$inv_master_data[0]["PORT_OF_LOADING"];
	$is_lc=$inv_master_data[0]["IS_LC"];
	$lc_sc_id=$inv_master_data[0]["LC_SC_ID"];
	$composition=$inv_master_data[0]["COMPOSITION"];
	$net_weight=$inv_master_data[0]["CARTON_NET_WEIGHT"];
	$gross_weight=$inv_master_data[0]["CARTON_GROSS_WEIGHT"];
	$cbm_qnty=$inv_master_data[0]["CBM_QNTY"];
	$total_measurment=$inv_master_data[0]["TOTAL_MEASURMENT"];
	$hs_code=$inv_master_data[0]["HS_CODE"];

	
	$company_name_sql=sql_select("SELECT ID, COMPANY_NAME, PLOT_NO, LEVEL_NO, ROAD_NO, BLOCK_NO, CITY, COUNTRY_ID,ERC_NO,EMAIL,CONTACT_NO,REX_NO,REX_REG_DATE,IRC_NO,VAT_NUMBER, WEBSITE FROM LIB_COMPANY WHERE ID ='$benificiary_id'");
	foreach($company_name_sql as $row)
	{
		$company_name=$row["COMPANY_NAME"];
		$plot_no=$row["PLOT_NO"];
		$level_no=$row["LEVEL_NO"];
		$road_no=$row["ROAD_NO"];
		$block_no=$row["BLOCK_NO"];
		$city=$row["CITY"];
		$country_id=$row["COUNTRY_ID"];
		$erc_no=$row["ERC_NO"];
		$contact_no=$row["CONTACT_NO"];
		$email=$row["EMAIL"];
		$website=$row["WEBSITE"];
		$rex_reg_date=$row["REX_REG_DATE"];
		$irc_no=$row["IRC_NO"];
		$vat_number=$row["VAT_NUMBER"];
		$address=$plot_no.",".$level_no.",".$road_no.",".$block_no;
	}
	if($plot_no!="")  $shipperDtls=$comany_details= $plot_no.", ";
	if($level_no!="")   $shipperDtls=$comany_details.= $level_no.", ";
	if($road_no!="")   $shipperDtls=$comany_details.= $road_no.", ";
	if($block_no!="")   $shipperDtls=$comany_details.= $block_no.", ";
	if($city!="")   $shipperDtls=$comany_details.= $city.", ";
	if($contact_no!="")  $comany_details.="<br>"."Phone: ".$contact_no.",";
	if($email!="")  $comany_details.="E-MAIL: ".$email.",";
	if($website!="")  $comany_details.="Web: ".$website.",";
	
	$country_name_arr=return_library_array( "SELECT ID, COUNTRY_NAME FROM LIB_COUNTRY",'ID','COUNTRY_NAME');
	// $carrier=$SupplierArr[$forwarder_name];
	$applicant=$buyer_name_arr[$applicant_name]["BUYER_NAME"];
	$buyer=$buyer_name_arr[$buyer_name]["BUYER_NAME"];
	$applicantAddress=$buyer_name_arr[$applicant_name]["ADDRESS_1"];
	$agent=$buyer_name_arr[$agent_id]["BUYER_NAME"];
	$agentAddress=$buyer_name_arr[$agent_id]["ADDRESS_1"];
		

	$company_logo=return_field_value("image_location","common_photo_library","file_type=1 and form_name='company_details' and master_tble_id='$benificiary_id'","image_location");

	$data_terms=sql_select("SELECT ID,TERMS,TERMS_PREFIX FROM WO_BOOKING_TERMS_CONDITION WHERE BOOKING_NO=$update_id AND ENTRY_FORM=270 ORDER BY ID");

	$con_lc_arr=array();
	if($is_lc==1)
	{
		$lc_sc_data=sql_select("SELECT ID, EXPORT_LC_NO, LC_DATE, NOTIFYING_PARTY, CONSIGNEE, ISSUING_BANK_NAME, NEGOTIATING_BANK, LIEN_BANK, PAY_TERM, APPLICANT_NAME,INCO_TERM,NOMINATED_SHIPP_LINE, BUYER_NAME, TENOR,SHIPPING_MODE FROM COM_EXPORT_LC WHERE ID='".$lc_sc_id."' ");
		foreach($lc_sc_data as $row)
		{
			$lc_sc_date=change_date_format($row["LC_DATE"]);
			$sc_lc=$row["EXPORT_LC_NO"];
			$issuing_bank=$issue_bank_arr[$row["LIEN_BANK"]];
			$line_bank_id=$row["LIEN_BANK"];	
		}
	}
	else
	{
		$lc_sc_data=sql_select("SELECT ID, CONTRACT_NO, CONTRACT_DATE, NOTIFYING_PARTY, CONSIGNEE, LIEN_BANK, PAY_TERM, APPLICANT_NAME,INCO_TERM,SHIPPING_LINE,BUYER_NAME, TENOR,SHIPPING_MODE, ISSUING_BANK FROM COM_SALES_CONTRACT WHERE ID='".$lc_sc_id."'  AND STATUS_ACTIVE=1");
		foreach($lc_sc_data as $row)
		{
			$lc_sc_date=change_date_format($row["CONTRACT_DATE"]);
			$sc_lc=$row["CONTRACT_NO"];
			$issuing_bank=$issue_bank_arr[$row["LIEN_BANK"]];
			$line_bank_id=$row["LIEN_BANK"];
		}
	}

	$bank_sql=sql_select( "SELECT A.ID, A.BANK_NAME, A.BRANCH_NAME, A.ADDRESS, A.SWIFT_CODE, A.DESIGNATION FROM LIB_BANK A");
	foreach($bank_sql as $row)
	{
		$bank_name_arr[$row["ID"]]["BANK_NAME"]=$row["BANK_NAME"];
		$bank_name_arr[$row["ID"]]["BRANCH_NAME"]=$row["BRANCH_NAME"];
		$bank_name_arr[$row["ID"]]["ADDRESS"]=$row["ADDRESS"];
		$bank_name_arr[$row["ID"]]["DESIGNATION"]=$row["DESIGNATION"];
		$bank_name_arr[$row["ID"]]["SWIFT_CODE"]=$row["SWIFT_CODE"];  
	}
	$bank_account_sql=sql_select( "SELECT ID, ACCOUNT_ID, ACCOUNT_TYPE, ACCOUNT_NO FROM LIB_BANK_ACCOUNT WHERE IS_DELETED=0 ");
	foreach($bank_account_sql as $row)
	{
		$bank_acc_arr[$row["ACCOUNT_ID"]][$row["ACCOUNT_TYPE"]]["ACCOUNT_NO"]=$row["ACCOUNT_NO"];
	}

	$applicant_sql=sql_select( "SELECT A.ID, A.BUYER_NAME, A.SHORT_NAME, A.ADDRESS_1, A.ADDRESS_2, A.ADDRESS_3, A.ADDRESS_4 FROM LIB_BUYER A");

	foreach($applicant_sql as $row)
	{
		$buyer_name_arr[$row["ID"]]["BUYER_NAME"]=$row["BUYER_NAME"];
		$buyer_name_arr[$row["ID"]]["ADDRESS_1"]=$row["ADDRESS_1"];
		$buyer_name_arr[$row["ID"]]["ADDRESS_2"]=$row["ADDRESS_2"];
		$buyer_name_arr[$row["ID"]]["ADDRESS_3"]=$row["ADDRESS_3"];
		$buyer_name_arr[$row["ID"]]["ADDRESS_4"]=$row["ADDRESS_4"];
	}

	$LocInfoArr=array();
	$loc_sql=sql_select( "SELECT A.ID, A.ADDRESS, A.COMPANY_ID FROM lib_location A");
	foreach($loc_sql as $row)
	{
		$LocInfoArr[$row["ID"]]["ADDRESS"]=$row["ADDRESS"];
		$LocInfoArr[$row["ID"]]["COMPANY_ID"]=$row["COMPANY_ID"];
	}

	$set_item_ratio=return_library_array( "SELECT JOB_ID, SET_ITEM_RATIO FROM wo_po_details_mas_set_details",'JOB_ID','SET_ITEM_RATIO');

	$dtlsSql="SELECT a.id as ORDER_ID, a.job_no_mst as JOB_NO_MST, a.po_number as PO_NUMBER, a.po_quantity as PO_QUANTITY, a.pub_shipment_date as PUB_SHIPMENT_DATE, b.attached_qnty as ATTACHED_QNTY, b.attached_rate as ATTACHED_RATE, b.attached_value as ATTACHED_VALUE, e.ITEM_NUMBER_ID, e.COLOR_NUMBER_ID, m.PRODUCT_DEPT, m.ID as JOB_ID, c.id as DTLS_ID, c.mst_id as MST_ID, c.current_invoice_rate as RATE, c.current_invoice_qnty as CURRENT_INVOICE_QNTY, c.current_invoice_value as CURRENT_INVOICE_VALUE, c.carton_qty as CARTON_QTY, m.style_ref_no as STYLE_REF_NO, m.order_uom as ORDER_UOM 
	FROM wo_po_details_master m, wo_po_break_down a, com_sales_contract_order_info b left join com_export_invoice_ship_dtls c on c.po_breakdown_id=b.wo_po_break_down_id  and c.status_active=1 and c.is_deleted=0 left join export_invoice_clr_sz_rt d on c.id=d.INVOICE_DETAILS_ID and d.status_active=1 left join  wo_po_color_size_breakdown e on  e.id=d.PO_BREAKDOWN_ID AND e.status_active = 1 where a.job_no_mst=m.job_no and b.com_sales_contract_id=$lc_sc_id and b.wo_po_break_down_id=a.id and b.status_active=1 and c.CURRENT_INVOICE_QNTY<>0 and b.is_deleted=0 and a.status_active=1 and a.is_deleted=0 and b.is_sales=0 group by a.id, a.job_no_mst, a.po_number, a.po_quantity, a.pub_shipment_date, b.attached_qnty, b.attached_rate, b.attached_value, e.ITEM_NUMBER_ID, e.COLOR_NUMBER_ID, m.PRODUCT_DEPT, m.ID, c.id, c.mst_id, c.current_invoice_rate, c.current_invoice_qnty, c.current_invoice_value, c.carton_qty, m.style_ref_no, m.order_uom";

	// COM_EXPORT_LC_ORDER_INFO

	$orderNoArr=sql_select($dtlsSql);
	foreach($orderNoArr as $row){
		$orderNO.=$row["PO_NUMBER"].",";
		$pub_shipment_date.=change_date_format($row["PUB_SHIPMENT_DATE"]).",";
	}

	
	ob_start();
	?>
	<style type="text/css" media="print">
		@page { 
			size: auto;
		} 
		thead {
		display: table-row-group;
		}
	</style>
	
    <table width='1200' border="1" cellspacing="0" cellpadding="0">
		<tr>
			<td   align="left" width="70"><img src="../../<? echo $company_logo; ?>" height="99" width="110"></td>
            <td colspan="11" valign="top" align="center" >
				<b style="font-size: 30px;"> <?php echo $company_name."<br>";?></b>
					<?php echo  $comany_details;?><br>   
			</td>
        </tr>
        <tr>
            <td colspan="12"  align="center" valign="top" >
				<b style="font-size: 30px;"> COMMERICAL INVOICE</b>
            </td>
        </tr>
		<tr>
			<td colspan="2" valign="top" width="200" align="left" style="word-break: break-all;"><b> Shipper / Exporter :</b> </td>
			<td colspan="2" valign="top"  width="200" align="left" style="word-break: break-all;"><b>MANUFACTURE :</b> </td>
			<td colspan="2" valign="top"  width="200" align="left" style="word-break: break-all;"><b>BUYER :</b></td>
			<td colspan="6" valign="top"  width="600" align="left" style="word-break: break-all;"><b>INVOICE NO:  <?=$invoice_no?></b></td>

		</tr>
		<tr>
			<td colspan="2" valign="top" width="200" align="left" style="word-break: break-all;"> <?php echo $company_name."<br>".$shipperDtls." ".$country_name_arr[$country_id];?></td>
			<td colspan="2" valign="top"  width="200" align="left" style="word-break: break-all;"> <?php echo $company_arr[$LocInfoArr[$location_id]["COMPANY_ID"]]."<br>".$LocInfoArr[$location_id]["ADDRESS"];?></td>
			<td colspan="2" valign="top"  width="200" align="left" style="word-break: break-all;"> <?php echo $buyer_name_arr[$consignee]["BUYER_NAME"]."<br>".$buyer_name_arr[$consignee]["ADDRESS_1"];?></td>
			<td colspan="6" valign="top"  width="600" align="left" style="word-break: break-all;"> <?php echo "INVOICE DATE: ".$invoice_date."<br>SHIPMENT AUTHORIZATION: ".$shipping_bill_n."<br>"."PAYMENT TERMS: ".$payment_method_arr[$payment_method];?></td>
		</tr>
		<tr>
			<td colspan="3" valign="top" width="300" align="ceneter"><b> LOCAL BANK : </b></td>
			<td colspan="3" valign="top" width="300" align="ceneter"><b>SHIP TO ADDRESS :</b></td>
			<td colspan="6" valign="top"  width="600" align="ceneter"><b>REMARKS :</b></td>		
		</tr>
		<tr>
			<td colspan="3" valign="top" width="300" style="word-break: break-all;" align="ceneter">TO THE ORDER OF <br> </b><?php echo $bank_name_arr[$line_bank_id]["BANK_NAME"]."<br>".$bank_name_arr[$line_bank_id]["ADDRESS"]."<br>SWIFT CODE: ".$bank_name_arr[$line_bank_id]["SWIFT_CODE"]."<br>CD Account: ".$bank_acc_arr[$line_bank_id][10]["ACCOUNT_NO"]?></td>
			<td colspan="3" valign="top" width="300" style="word-break: break-all;" align="ceneter"> <?php echo $buyer_name_arr[$consignee]["ADDRESS_2"];?></td>
			<td colspan="6" valign="top"  width="600"  style="word-break: break-all;" align="ceneter"><?php echo "COUNTRY OF ORIGIN: ".$country_name_arr[$country_id]."<br>TERMS:<b> ".$incoterm[$inco_term]." ".$inco_term_place."</b><br>CONTRACT NO:".$sc_lc."<br><b>DATE:</b>".$lc_sc_date."<br> PO# :<b>".rtrim($orderNO,",")."</b> &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;PO DATE:<b>".rtrim($pub_shipment_date,",")."</b><br> EXP NO:".$exp_form_no."<br> EXP DATE: ".$exp_form_date?></td>		
		</tr>
		<tr>
			<td colspan="3" width="300"  style="word-break: break-all;" valign="top" align="left"><b> 1ST NOTIFY PARTY:</b> </td>
			<td colspan="3"  width="300"  style="word-break: break-all;" valign="top" align="left"> <?php if($buyer_name_arr[$consignee]["ADDRESS_3"]){ echo "<b>2ND NOTIFY : </b>"; } ?></td>
			<td colspan="6"  width="600" style="word-break: break-all;" valign="top" align="left"><b>MODE OF SHIP: </b></td>		

		</tr>
		<tr>
			<td colspan="3" width="300"  style="word-break: break-all;" valign="top" align="left"> <?php echo $buyer_name_arr[$consignee]["ADDRESS_3"];?></td>
			<td colspan="3"  width="300"  style="word-break: break-all;" valign="top" align="left"> <?php if($buyer_name_arr[$consignee]["ADDRESS_3"]){ echo $buyer_name_arr[$consignee]["ADDRESS_4"];} ?></td>
			<td colspan="6"  width="600" style="word-break: break-all;" valign="top" align="left"> <?php echo "PORT OF LOADING: <b>".$port_of_loading."</b><br> PORT OF DISCHARGE: <b>".$port_of_discharge."</b><br> PLACE OF DELIVERY: <b>".$place_of_delivery."<b>";?></td>		
		</tr>

		<?

		
		
		
		
		?>
		<tr>
			<td colspan="12">
				<table border="1" width="1200" cellspacing="0" cellpadding="0">
					<thead>
						<tr>
							<th width="100" rowspan="4">SHIPPING MARKS</th>
							<th  colspan="4">DESCRIPTION OF GOODS</th>
							<th width="60" rowspan="4">QUANTITY IN CARTONS</th>
							<th width="80" rowspan="4">QUANTITY IN PKS</th>
							<th width="80" rowspan="4">QUANTITY IN PCS</th>
							<th width="80" rowspan="4">UNIT PRICE IN US$</th>
							<th width="80" rowspan="4">TTL-AMOUNT US DOLLAR</th>
						</tr>
						<tr>
							<th colspan="4">READYMADE GARMENTS</th>
						</tr>
						<tr>
							<th colspan="4">FABRICATION: <?=$composition?></th>
						</tr>
						<tr>
							<th width="100">PURCHASE ORDER</th>
							<th width="80">STYLE NO</th>
							<th width="100">ITEM</th>
							<th width="60">COLOR</th>
						</tr>
					</thead>
					<tbody>
						<?php 

						//  $DtlasCountArr=sql_select($dtlsSql);
						 $DtlasDataArr=sql_select($dtlsSql);
						 foreach ($DtlasDataArr as $row) { ?>
							<tr>
								<td><??></td>
								<td><?=$row["PO_NUMBER"]?></td>
								<td><?=$row["STYLE_REF_NO"]?></td>
								<td><?=$product_dept[$row["PRODUCT_DEPT"]]." ".$garments_item[$row["ITEM_NUMBER_ID"]]." ".$set_item_ratio[$row["JOB_ID"]]." ".$unit_of_measurement[$row['ORDER_UOM']]?></td>
								<td><?=$color_arr[$row["COLOR_NUMBER_ID"]]?></td>
								<td align="right"><?=$row["CARTON_QTY"]."   CTNS"?></td>
								<td align="right"><?=$row["CURRENT_INVOICE_QNTY"]."  ".$unit_of_measurement[$row['ORDER_UOM']]?></td>
								<td align="right"><?=$row["CURRENT_INVOICE_QNTY"]*$set_item_ratio[$row["JOB_ID"]]."     PCS"?></td>
								<td align="right"><?="$ ".$row["RATE"]."  /  PACK"?></td>
								<td align="right"><?=$row["CURRENT_INVOICE_QNTY"]*$row["RATE"]?></td>
							</tr>
							<?php 
							$totalCartonQty+=$row["CARTON_QTY"];
							$totalcurrent_invoice_qnty+=$row["CURRENT_INVOICE_QNTY"];
							$totalcurrent_invoice_qnty_pcs+=$row["CURRENT_INVOICE_QNTY"]*$set_item_ratio[$row["JOB_ID"]];
							$ttAmmountUsd+=$row["CURRENT_INVOICE_QNTY"]*$row["RATE"];
						} ?>
					</tbody>
					<tfoot>
						<tr>
							<th colspan="5" align="right"> Total</th>
							<th align="right"><?=$totalCartonQty?></th>
							<th align="right"><?=$totalcurrent_invoice_qnty?></th>
							<th align="right"><?=$totalcurrent_invoice_qnty_pcs?></th>
							<th></th>
							<th align="right"><?=$ttAmmountUsd?></th>
						</tr>
					</tfoot>
				</table>
			</td>
		</tr>
		<tr>
			<td colspan="12" align="center"> <b>TOTAL US DOLLAR: <? echo number_to_words(def_number_format($ttAmmountUsd,2,""),"USD", "CENTS")." ONLY";?> </b></td>
		</tr>
		<tr>
			<td colspan="12">
				<table  width="1200" cellspacing="0" cellpadding="0">
					<tr>
						<td> <b>REMARKS: NO WOOD PACKING MATERIAL</b></td>
					</tr>
					<tr>
						<td align="left"> <b>HTS/QUOTA CATG:</b> <?=$hs_code?></td>
					</tr>
					<tr>
						<td width="300" colspan="5"></td>
						<td>TOTAL CARTONS:</td>
						<td><?=$totalCartonQty?></td>
						<td>CARTONS	</td>
						<td width="300" colspan="4"></td>
					</tr>
					<tr>
						<td colspan="5"></td>
						<td>TOTAL QUANTITY:</td>
						<td><?=$totalcurrent_invoice_qnty?></td>
						<td>PACKS	</td>
						<td colspan="4"></td>
					</tr>
					<tr>
						<td colspan="5"></td>
						<td>TOTAL PCS QUANTITY:</td>
						<td><?=$totalcurrent_invoice_qnty_pcs?></td>
						<td>PCS	</td>
						<td colspan="4"></td>
					</tr>
					<tr>
						<td colspan="5"></td>
						<td>TOTAL DZN QUANTITY:</td>
						<td><?=$totalcurrent_invoice_qnty_pcs%12?></td>
						<td>DZN	</td>
						<td colspan="4"></td>
					</tr>
					<tr>
						<td colspan="5"></td>
						<td>TOTAL GROSS WEIGHT:</td>
						<td><?=$gross_weight?></td>
						<td>KGS	</td>
						<td colspan="4"></td>
					</tr>
					<tr>
						<td colspan="5"></td>
						<td>TOTAL NET WEIGHT:</td>
						<td><?=$net_weight?></td>
						<td>KGS	</td>
						<td colspan="4"></td>
					</tr>
					<tr>
						<td colspan="5"></td>
						<td>TOTAL CBM:</td>
						<td><?=$cbm_qnty?></td>
						<td>CBM	</td>
						<td colspan="4"></td>
					</tr>
					<tr>
						<td colspan="5"></td>
						<td>CTNS MEASUREMENT:</td>
						<td><?=$total_measurment?></td>
						<td>INCH	</td>
						<td colspan="4"></td>
					</tr>
				</table>
			</td>
		</tr>
	</table>

	<?
		$html = ob_get_contents();
		ob_clean();
		foreach (glob("tb*.xls") as $filename) {
		@unlink($filename);
		}
		//---------end------------//
		$name=time();
		$filename="tb".$user_id."_".$name.".xls";
		$create_new_doc = fopen($filename, 'w');
		$is_created = fwrite($create_new_doc, $html);
		echo "$filename####$html";
	exit();	
}

if($action=="invoice_report_print_5")  //print 5
{
	extract($_REQUEST);
	// $update_id=$data;

	$brand_arr=return_library_array( "SELECT ID, BRAND_NAME FROM LIB_BUYER_BRAND BRAND",'ID','BRAND_NAME');
	$applicant_sql=sql_select( "SELECT A.ID, A.BUYER_NAME, A.SHORT_NAME, A.ADDRESS_1 FROM LIB_BUYER A");
	foreach($applicant_sql as $row)
	{
		$buyer_name_arr[$row["ID"]]["BUYER_NAME"]=$row["BUYER_NAME"];
		$buyer_name_arr[$row["ID"]]["ADDRESS_1"]=$row["ADDRESS_1"];
	}
	$bank_sql=sql_select( "SELECT A.ID, A.BANK_NAME, A.BRANCH_NAME, A.ADDRESS, A.SWIFT_CODE FROM LIB_BANK A");
	foreach($bank_sql as $row)
	{
		$bank_name_arr[$row["ID"]]["BANK_NAME"]=$row["BANK_NAME"];
		$bank_name_arr[$row["ID"]]["BRANCH_NAME"]=$row["BRANCH_NAME"];
		$bank_name_arr[$row["ID"]]["ADDRESS"]=$row["ADDRESS"];
		$bank_name_arr[$row["ID"]]["SWIFT_CODE"]=$row["SWIFT_CODE"];
	}
	$bank_account_sql=sql_select( "SELECT ID, ACCOUNT_ID, ACCOUNT_TYPE, ACCOUNT_NO FROM LIB_BANK_ACCOUNT WHERE IS_DELETED=0 ");
	foreach($bank_account_sql as $row)
	{
		$bank_acc_arr[$row["ACCOUNT_ID"]][$row["ACCOUNT_TYPE"]]["ACCOUNT_NO"]=$row["ACCOUNT_NO"];
	}
	$inv_master_data=sql_select("SELECT ID, BENIFICIARY_ID, BUYER_ID, LOCATION_ID, INVOICE_NO, INVOICE_DATE, EXP_FORM_NO, EXP_FORM_DATE, IS_LC, LC_SC_ID, BL_NO, FEEDER_VESSEL, INCO_TERM, INCO_TERM_PLACE, SHIPPING_MODE, PORT_OF_ENTRY, PORT_OF_LOADING, PORT_OF_DISCHARGE, MAIN_MARK, SIDE_MARK, CARTON_NET_WEIGHT, CARTON_GROSS_WEIGHT, CBM_QNTY, PLACE_OF_DELIVERY, DELV_NO, CONSIGNEE, NOTIFYING_PARTY, ITEM_DESCRIPTION, DISCOUNT_AMMOUNT, BONUS_AMMOUNT, COMMISSION, TOTAL_CARTON_QNTY, BL_DATE, HS_CODE, MOTHER_VESSEL, CATEGORY_NO, FORWARDER_NAME, ETD,CO_NO, TOTAL_MEASURMENT, INVOICE_VALUE, NET_INVO_VALUE, CONTAINER_NO, SEAL_NO, ETD, COUNTRY_ID,COMMISSION_PERCENT,BL_REV_DATE FROM COM_EXPORT_INVOICE_SHIP_MST WHERE ID=$update_id");
	$id=$inv_master_data[0]["ID"];
	$benificiary_id=$inv_master_data[0]["BENIFICIARY_ID"];
	$buyer_id=$inv_master_data[0]["BUYER_ID"];
	$location_id=$inv_master_data[0]["LOCATION_ID"];
	$invoice_no=$inv_master_data[0]["INVOICE_NO"];
	$invoice_date=$inv_master_data[0]["INVOICE_DATE"];
	$bl_rev_date=$inv_master_data[0]["BL_REV_DATE"];
	$exp_form_no=$inv_master_data[0]["EXP_FORM_NO"];
	$exp_form_date=$inv_master_data[0]["EXP_FORM_DATE"];
	$is_lc=$inv_master_data[0]["IS_LC"];
	$lc_sc_id=$inv_master_data[0]["LC_SC_ID"];
	$bl_no=$inv_master_data[0]["BL_NO"];
	$feeder_vessel=$inv_master_data[0]["FEEDER_VESSEL"];
	$inco_term=$inv_master_data[0]["INCO_TERM"];
	$inco_term_place=$inv_master_data[0]["INCO_TERM_PLACE"];
	$shipping_mode=$inv_master_data[0]["SHIPPING_MODE"];
	$port_of_entry=$inv_master_data[0]["PORT_OF_ENTRY"];
	$port_of_loading=$inv_master_data[0]["PORT_OF_LOADING"];
	$port_of_discharge=$inv_master_data[0]["PORT_OF_DISCHARGE"];
	$main_mark=$inv_master_data[0]["MAIN_MARK"];
	$side_mark=$inv_master_data[0]["SIDE_MARK"];
	$net_weight=$inv_master_data[0]["CARTON_NET_WEIGHT"];
	$gross_weight=$inv_master_data[0]["CARTON_GROSS_WEIGHT"];
	$cbm_qnty=$inv_master_data[0]["CBM_QNTY"];
	$place_of_delivery=$inv_master_data[0]["PLACE_OF_DELIVERY"];
	$delv_no=$inv_master_data[0]["DELV_NO"];
	$consignee=$inv_master_data[0]["CONSIGNEE"];
	$notifying_party=$inv_master_data[0]["NOTIFYING_PARTY"];
	$item_description=$inv_master_data[0]["ITEM_DESCRIPTION"];
	$discount_ammount=$inv_master_data[0]["DISCOUNT_AMMOUNT"];
	$bonus_ammount=$inv_master_data[0]["BONUS_AMMOUNT"];
	$commission=$inv_master_data[0]["COMMISSION"];
	$commission_percent=$inv_master_data[0]["COMMISSION_PERCENT"];
	$total_carton_qnty=$inv_master_data[0]["TOTAL_CARTON_QNTY"];
	$bl_date=$inv_master_data[0]["BL_DATE"];
	$hs_code=$inv_master_data[0]["HS_CODE"];
	$mother_vessel=$inv_master_data[0]["MOTHER_VESSEL"];
	$category_no=$inv_master_data[0]["CATEGORY_NO"];
	$forwarder_name=$inv_master_data[0]["FORWARDER_NAME"];
	$etd=$inv_master_data[0]["ETD"];
	$co_no=$inv_master_data[0]["CO_NO"];
	$total_measurment=$inv_master_data[0]["TOTAL_MEASURMENT"];
	$net_invo_value=$inv_master_data[0]["NET_INVO_VALUE"];
	$container_no=$inv_master_data[0]["CONTAINER_NO"];
	$seal_no=$inv_master_data[0]["SEAL_NO"];
	$etd=$inv_master_data[0]["ETD"];
	$inv_country_id=$inv_master_data[0]["COUNTRY_ID"];
	$total_discount=$inv_master_data[0]["INVOICE_VALUE"]-$inv_master_data[0]["NET_INVO_VALUE"];
	
	$itemIdArr=array();
	$setQtyArr=array();
	$poIdArr=array();
	$dtls_sql="SELECT A.ID AS DTLS_ID, A.PO_BREAKDOWN_ID,C.TOTAL_SET_QNTY FROM  COM_EXPORT_INVOICE_SHIP_DTLS A,  WO_PO_BREAK_DOWN B, WO_PO_DETAILS_MASTER C WHERE A.PO_BREAKDOWN_ID=B.ID AND B.JOB_NO_MST=C.JOB_NO AND A.CURRENT_INVOICE_QNTY>0 AND A.STATUS_ACTIVE=1 AND A.IS_DELETED=0 AND A.MST_ID=$update_id";
	$PO_agent=sql_select($dtls_sql);
	foreach($PO_agent as $row){
		$poIdArr[]=$row['PO_BREAKDOWN_ID'];
		$setQtyArr[$row['PO_BREAKDOWN_ID']]=$row['TOTAL_SET_QNTY'];
	}
	
	$carton_arr=array();
	$sqlCarton=sql_select("SELECT A.ID,A.SYS_NUMBER, A.DL_NO, B.DELIVERY_MST_ID,B.PO_BREAK_DOWN_ID,B.TOTAL_CARTON_QNTY,B.CARTON_QNTY FROM PRO_EX_FACTORY_DELIVERY_MST A,PRO_EX_FACTORY_MST B WHERE A.ID=B.DELIVERY_MST_ID AND B.PO_BREAK_DOWN_ID IN(".implode(",",$poIdArr).")");


	foreach($sqlCarton as $rowCarton)
	{
		$carton_arr[$rowCarton['PO_BREAK_DOWN_ID']]=$rowCarton['TOTAL_CARTON_QNTY'];
	}
	$agent_id="";
	// $fristPo=array_shift($poIdArr);
	$sql_fabric=sql_select("SELECT B.ID, C.CONSTRUCTION, C.COMPOSITION FROM WO_PO_BREAK_DOWN B, WO_PRE_COST_FABRIC_COST_DTLS C WHERE B.JOB_NO_MST=C.JOB_NO AND B.ID IN(".implode(",",$poIdArr).")");

	foreach($sql_fabric as $row_fabric){
		$fabric_info[$row_fabric["ID"]]=$row_fabric["CONSTRUCTION"]." ".$row_fabric["COMPOSITION"];
	}
	
	
	if($is_lc==1)
	{
		$lc_sc_data=sql_select("SELECT ID, EXPORT_LC_NO, LC_DATE, NOTIFYING_PARTY, CONSIGNEE, ISSUING_BANK_NAME, NEGOTIATING_BANK, LIEN_BANK, PAY_TERM, APPLICANT_NAME,INCO_TERM,LIEN_BANK,NOMINATED_SHIPP_LINE, BUYER_NAME, TENOR,SHIPPING_MODE FROM COM_EXPORT_LC WHERE ID='".$lc_sc_id."' ");
		foreach($lc_sc_data as $row)
		{
			$lc_sc_id=$row["ID"];
			$lc_sc_no=$row["EXPORT_LC_NO"];
			$lc_sc_date=change_date_format($row["LC_DATE"]);
			$notifying_party=$row["NOTIFYING_PARTY"];
			$consignee=$row["CONSIGNEE"];
			$issuing_bank_name=$row["ISSUING_BANK_NAME"];
			$negotiating_bank=$row["LIEN_BANK"];
			$pay_term_id=$row["PAY_TERM"];
			$applicant_name=$row["APPLICANT_NAME"];
			$buyer_name=$row["BUYER_NAME"];
			$inco_term=$row["INCO_TERM"];
			$lien_bank=$row["LIEN_BANK"];
			$shipping_line=$row["NOMINATED_SHIPP_LINE"];
			$negotiating_bank_text=$row["NEGOTIATING_BANK"];
			$tenor=$row["TENOR"];
			$shipping_mode=$row["SHIPPING_MODE"];
		}
		
			$cate_hs_sql=sql_select("SELECT WO_PO_BREAK_DOWN_ID, FABRIC_DESCRIPTION, CATEGORY_NO, HS_CODE FROM COM_EXPORT_LC_ORDER_INFO WHERE COM_EXPORT_LC_ID='".$lc_sc_id."'");
			foreach($cate_hs_sql as $row)
			{
				$order_la_data[$row["WO_PO_BREAK_DOWN_ID"]]["CATEGORY_NO"]=$row["CATEGORY_NO"];
				$order_la_data[$row["WO_PO_BREAK_DOWN_ID"]]["HS_CODE"]=$row["HS_CODE"];
			    $all_order_id[$row["WO_PO_BREAK_DOWN_ID"]]=$row["WO_PO_BREAK_DOWN_ID"];
			}
	}
	else
	{
		$lc_sc_data=sql_select("SELECT ID, CONTRACT_NO, CONTRACT_DATE, NOTIFYING_PARTY, CONSIGNEE, LIEN_BANK, PAY_TERM, APPLICANT_NAME,INCO_TERM,LIEN_BANK,SHIPPING_LINE,BUYER_NAME, TENOR,SHIPPING_MODE FROM COM_SALES_CONTRACT WHERE ID='".$lc_sc_id."'  AND STATUS_ACTIVE=1");
		foreach($lc_sc_data as $row)
		{
			$lc_sc_id=$row["ID"];
			$lc_sc_no=$row["CONTRACT_NO"];
			$lc_sc_date=change_date_format($row["CONTRACT_DATE"]);
			$notifying_party=$row["NOTIFYING_PARTY"];
			$consignee=$row["CONSIGNEE"];
			$issuing_bank_name="";
			$negotiating_bank=$row["LIEN_BANK"];
			$pay_term_id=$row["PAY_TERM"];
			$applicant_name=$row["APPLICANT_NAME"];
			$buyer_name=$row["BUYER_NAME"];
			$inco_term=$row["INCO_TERM"];
			$lien_bank=$row["LIEN_BANK"];
			$shipping_line=$row["SHIPPING_LINE"];
			$tenor=$row["TENOR"];
			$shipping_mode=$row["SHIPPING_MODE"];
			$negotiating_bank_text="";
		}
		
		$cate_hs_sql=sql_select("SELECT WO_PO_BREAK_DOWN_ID, FABRIC_DESCRIPTION, CATEGORY_NO, HS_CODE FROM COM_SALES_CONTRACT_ORDER_INFO WHERE COM_SALES_CONTRACT_ID='".$lc_sc_id."' AND STATUS_ACTIVE=1");
		foreach($cate_hs_sql as $row)
		{
			$order_la_data[$row["WO_PO_BREAK_DOWN_ID"]]["CATEGORY_NO"]=$row["CATEGORY_NO"];
			$order_la_data[$row["WO_PO_BREAK_DOWN_ID"]]["HS_CODE"]=$row["HS_CODE"];
			$order_la_data[$row["WO_PO_BREAK_DOWN_ID"]]["FABRIC_DESCRIPTION"]=$row["FABRIC_DESCRIPTION"];
			$all_order_id[$row["WO_PO_BREAK_DOWN_ID"]]=$row["WO_PO_BREAK_DOWN_ID"];
		}
	}
	
	$company_name_sql=sql_select( "SELECT ID, COMPANY_NAME, PLOT_NO, LEVEL_NO, ROAD_NO, BLOCK_NO, CITY, COUNTRY_ID,ERC_NO,EMAIL,CONTACT_NO,REX_NO,REX_REG_DATE,IRC_NO,VAT_NUMBER FROM LIB_COMPANY WHERE ID ='$benificiary_id'");
	foreach($company_name_sql as $row)
	{
		$company_name=$row["COMPANY_NAME"];
		$plot_no=$row["PLOT_NO"];
		$level_no=$row["LEVEL_NO"];
		$road_no=$row["ROAD_NO"];
		$block_no=$row["BLOCK_NO"];
		$city=$row["CITY"];
		$country_id=$row["COUNTRY_ID"];
		$erc_no=$row["ERC_NO"];
		$contact_no=$row["CONTACT_NO"];
		$email=$row["EMAIL"];
		$rex_no=$row["REX_NO"];
		$rex_reg_date=$row["REX_REG_DATE"];
		$irc_no=$row["IRC_NO"];
		$vat_number=$row["VAT_NUMBER"];
	}
	
	$country_name_arr=return_library_array( "SELECT ID, COUNTRY_NAME FROM LIB_COUNTRY",'ID','COUNTRY_NAME');
	// $carrier=$SupplierArr[$forwarder_name];
	$applicant=$buyer_name_arr[$applicant_name]["BUYER_NAME"];
	$buyer=$buyer_name_arr[$buyer_name]["BUYER_NAME"];
	$applicantAddress=$buyer_name_arr[$applicant_name]["ADDRESS_1"];
	$agent=$buyer_name_arr[$agent_id]["BUYER_NAME"];
	$agentAddress=$buyer_name_arr[$agent_id]["ADDRESS_1"];
		
	$dtls_sql="SELECT A.ID AS DTLS_ID, A.PO_BREAKDOWN_ID, A.CURRENT_INVOICE_RATE, A.CURRENT_INVOICE_QNTY, A.CURRENT_INVOICE_VALUE, B.PO_NUMBER, C.STYLE_REF_NO, C.GMTS_ITEM_ID, C.ORDER_UOM, C.GMTS_ITEM_ID,C.STYLE_DESCRIPTION, C.BRAND_ID FROM  COM_EXPORT_INVOICE_SHIP_DTLS A,  WO_PO_BREAK_DOWN B, WO_PO_DETAILS_MASTER C WHERE A.PO_BREAKDOWN_ID=B.ID AND B.JOB_NO_MST=C.JOB_NO AND A.CURRENT_INVOICE_QNTY>0 AND A.STATUS_ACTIVE=1 AND A.IS_DELETED=0 AND A.MST_ID=$update_id";
	$result=sql_select($dtls_sql);

	$sql="SELECT B.ID AS ID, B.LOCATION_NAME || ' (' || A.COMPANY_SHORT_NAME || ')' AS LOCATION_NAME  FROM LIB_COMPANY A, LIB_LOCATION B WHERE A.ID=B.COMPANY_ID AND B.STATUS_ACTIVE =1 AND B.IS_DELETED=0 AND A.STATUS_ACTIVE =1 AND A.IS_DELETED=0 ORDER BY B.LOCATION_NAME";

	$location_dtls_sql = "SELECT A.COMPANY_NAME , B.LOCATION_NAME , B.ADDRESS , B.EMAIL,B.COUNTRY_ID FROM  LIB_COMPANY A ,LIB_LOCATION B WHERE A.ID=B.COMPANY_ID AND B.STATUS_ACTIVE =1 AND B.IS_DELETED=0 AND A.STATUS_ACTIVE =1 AND A.IS_DELETED=0  AND B.ID = $location_id";
	//echo $location_dtls_sql;
	$location_dtls = sql_select($location_dtls_sql);
	foreach($location_dtls as $row)
	{
		$ex_company_name=$row['COMPANY_NAME'];
		$ex_location_name=$row['LOCATION_NAME'];
		$ex_address=$row['ADDRESS'];
		$ex_country_id=$row[csf("COUNTRY_ID")];
		$ex_email=$row["EMAIL"];
	}

	$company_logo=return_field_value("image_location","common_photo_library","file_type=1 and form_name='company_details' and master_tble_id='$benificiary_id'","image_location");

	$data_terms=sql_select("SELECT ID,TERMS,TERMS_PREFIX FROM WO_BOOKING_TERMS_CONDITION WHERE BOOKING_NO=$update_id AND ENTRY_FORM=270 ORDER BY ID");
	ob_start();
	?>
    <table width='1040' cellspacing="0" cellpadding="0" border="1" >
        <tr>
            <td colspan="12" valign="top" align="center" style="font-size:Large; font-weight:bold"><b>COMMERCIAL INVOICE</b></td>
        </tr>
		
		
        <tr>
            <td colspan="6" valign="top" width ="520"> 
			<b>SUPPLIER/EXPORTER:</b>
            </td>

            <td colspan="6" valign="top" width ="520">
            </td>
        </tr>
		
        <tr>
			<td colspan="6" valign="top" width ="520"> 
			<?php echo $company_name;?>
				<?php
				if($city!="")  $comany_details.= "<br>".$city.", ";
				if($country_id!="")  $comany_details.="<br>".$country_name_arr[$country_id].".";
				if($contact_no!="")  $comany_details.="<br>Telephone: ".$contact_no.".";
				if($email!="")  $comany_details.="<br>E-MAIL: ".$email.".";
				echo  $comany_details;
				?><br>  
				EORI No : NA
            </td>

			<td colspan="6" valign="top" width ="520">
				<table style="border-collapse: collapse;">
					<tr>
						<td width='100'>INVOICE NO </td>
						<td width='200'>:&nbsp; <? echo $invoice_no;;?></td>
						<td width='80'>DATE: </td>
						<td width='140'><? echo change_date_format($invoice_date);?></td>
					</tr>
					<tr>
						<td width='100'>EXP. NO </td>
						<td width='200'>:&nbsp; <? echo $exp_form_no;?></td>
						<td width='80'>DATE: </td>
						<td width='140'><? if($exp_form_date!="" && $exp_form_date!="0000-00-00" ) echo change_date_format($exp_form_date);?></td>
					</tr>
					<tr>
						<td width='100'>CONT. NO </td>
						<td width='200'>:&nbsp; <? echo $lc_sc_no;?></td>
						<td width='80'>DATE: </td>
						<td width='140'><? if($lc_sc_date!="" && $lc_sc_date!="0000-00-00" ) echo change_date_format($lc_sc_date);?></td>
					</tr>
				</table>
            </td>
        </tr>

	
		<tr>
			<td colspan="6" valign="top"> 
			<b>FOR ACCOUNT & RISK OF MESSRS: </b>
            </td>
			
			<td colspan="6" valign="top">
			 	<table style="border-collapse: collapse;">
					<tr>
						<td width='240'><b>EXPORT REGISTRATION NO.</b> </td>
						<td width='140'>:&nbsp; <? echo "NA";?></td>
						<td width='60'>DATE: </td>
						<td width='80'><? echo "NA";?></td>
					</tr>
				</table>
            </td>
        </tr>

		<tr>
			<td colspan="6" valign="top"> 
			<b><?echo $applicant;?> </b> <br>
			<? echo $applicantAddress; ?> <br>
			VAT ID NO. <?echo $vat_number;?>
            </td>
			
			<td colspan="6" valign="top">
			<u><b>BUYER BANK</b></u> <br>
			<? echo $bank_name_arr[$lien_bank]["BANK_NAME"]."<br>".$bank_name_arr[$lien_bank]["ADDRESS"];?>
            </td>
        </tr>

		<tr>
			<td colspan="6" valign="top"> 
				<b>CONSIGNEE:  </b>
            </td>
			
			<td colspan="6" valign="top">
				<b>TERMS : </b><? echo "FCA BANGLADESH";?>
            </td>
        </tr>

		<tr>
			<td colspan="6" valign="top"> 
				<?
                if($buyer_name_arr[$consignee]["BUYER_NAME"]!=''){echo $buyer_name_arr[$consignee]["BUYER_NAME"]."<br/>";}
                if($buyer_name_arr[$consignee]["ADDRESS_1"]!=''){echo $buyer_name_arr[$consignee]["ADDRESS_1"]."<br/>";}
            	?>
            </td>
			
			<td colspan="6" valign="top">
				<b>COUNTRY OF ORIGIN : </b><? echo "BANGLADESH";?> <br>
				<b>PAYMENT TERMS BY <? echo $pay_term[$pay_term_id];?>  <?echo $tenor; ?> DAYS</b>
            </td>
        </tr>

		<tr>
			<td colspan="6" valign="top"> 
			   <b>NOTIFY PARTY :</b> 
            </td>
			
			<td colspan="6" valign="top">
				<b>TO THE ORDER OF : </b>
            </td>
        </tr>

		
		<tr>
			<td colspan="6" valign="top"> 
				<? if($buyer_name_arr[$notifying_party]["BUYER_NAME"]!=''){echo $buyer_name_arr[$notifying_party]["BUYER_NAME"]."<br/>";}
                if($buyer_name_arr[$notifying_party]["ADDRESS_1"]!=''){echo $buyer_name_arr[$notifying_party]["ADDRESS_1"];} ?>
			   <br>
			   <table style="border-collapse: collapse;  border:none;" border="1">
					<tr>
						<td width='260'><b>PORT OF LOADING</b></td>
						<td width='260' style="border-right:none;"><b>PORT OF DISCHARGE</b></td>
					</tr>
					<tr>
						<td width='260'><?echo $port_of_loading;?></td>
						<td width='260' style="border-right:none;"> <? echo $port_of_discharge;?><br/></td>
					</tr>
					<tr>
						<td width='260'><b>CARRIER </b></td>
						<td width='260' style="border-right:none;"> <b>COUNTRY OF DESTINATION</b><br/></td>
					</tr>
					<tr>
						<td width='260' style="border-bottom:none;"><?echo $shipment_mode[$shipping_mode];?></td>
						<td width='260' style="border-right:none; border-bottom:none;"> <? echo $country_name_arr[$inv_country_id];?><br/></td>
					</tr>
				</table>
            </td>
			
			<td colspan="6" valign="top">
				<? echo $bank_name_arr[$lien_bank]["BANK_NAME"]."<br>".$bank_name_arr[$lien_bank]["ADDRESS"];?> <br>
				<?
				echo 'SWIFT: '.$bank_name_arr[$lien_bank]["SWIFT_CODE"].'<br> ACCOUNT NUMBER: '.$bank_acc_arr[$lien_bank][10]["ACCOUNT_NO"];
				?>
            </td>

        </tr>

	</table>
    <br>

	<table width='1040' cellspacing="0" cellpadding="0" border="1">  
		<tr style="font-size:small; font-weight:bold" align="center">
			<td width = "300"><P>MARKS & NOS. PKGS</P> </td>
			<td width = "300"><P>DESCRIPTION OF GOODS</P> </td>
			<td><P>QTY. IN PCS</P> </td>
			<td><P>UNIT PRICE</P>  </td>
			<td><P>AMOUNT</P> </td>
		</tr>

		<?
		foreach($result as $row)
		{
			$po_no .= $row["PO_NUMBER"].',';
			$style_ref .= $row["STYLE_REF_NO"].',';
		}

		$all_po = ltrim(implode(",", array_unique(explode(",", chop($po_no, ",")))), ',');
		$all_style_ref = ltrim(implode(",", array_unique(explode(",", chop($style_ref, ",")))), ',');
		?>

		<?
		$i=1;
		foreach($result as $row)
		{
			?>
			<tr style="font-size:small">
			<?if($i==1){?>
				<td width="300" valign="top" rowspan="<?=count($result);?>"><p style="text-align:center; display:inline-block; position: relative;"><u><b>MAIN MARK/SIDE MARK </b></u></p><br>
					<?echo $applicant;?> <br>
					ORDERNUBMER: <?echo "NA";?> <br>
					LOTNUBMER:<? echo "NA";?>	<br>			
					ARTIKELBEZEICHUNG:<? echo "NA";?>	<br>		
					LOTSORTIERUNG:<? echo "NA";?>		<br>	
					ROSE/ROSA		<br>	
					ANZAAHL LOTS:<? echo "NA";?>	<br>		
					KARTONNUBMER:<? echo "NA";?>	<br>		
					BRUTTOGEWICHT:<? echo "NA";?>  <br>
					NETTOGEWICHT: <? echo "NA";?>	<br>		
					KARTONNUBMER: <? echo "NA";?>                    			
				</td>
				<?}
				if($i==1){?>
				<td width="300" valign="top" rowspan="<?=count($result);?>"> <p>
					READY-MADE GARMENTS <br>
					STYLE: <? echo  $all_style_ref; ?>  <br>
					ORDER NO:  <? echo $all_po ; ?>  <br>
					HS CODE :  <? echo $hs_code;?>  <br>
					CAT :     <?echo $category_no;?> <br>
					CTNS : <? echo $total_carton_qnty;?>

				</p></td>
				<?}?>
				
				<td width="140" align="right"> <p><? echo number_format($row['CURRENT_INVOICE_QNTY']*$setQtyArr[$row['PO_BREAKDOWN_ID']],0,".",",");  ?> </p></td>
				<td width="140" align="right"><p><?  echo "$".number_format($row["CURRENT_INVOICE_RATE"],2); ?></p></td>
				<td width="160" align="right"><p><? echo "$".number_format($row["CURRENT_INVOICE_VALUE"],2,".",","); ?></p></td>
			</tr>
			<?
			$total_rate+=$row["CURRENT_INVOICE_RATE"];
			$total_value+=$row["CURRENT_INVOICE_VALUE"];
			$total_qnty+=$row["CURRENT_INVOICE_QNTY"]*$setQtyArr[$row['PO_BREAKDOWN_ID']];
			$last_uom=$unit_of_measurement[$row['ORDER_UOM']];
			$total_po_carton_qnty+=$carton_arr[$row['PO_BREAKDOWN_ID']];
			$i++;
		}
		?>
		<tr>
			<td colspan="2" align="right"><b>Total</b></td>
			<td align="right"><b><? echo number_format($total_qnty,0,".",",")." Pcs" ?></b></td>
			<td align="right"> <b><? echo "$&nbsp;".number_format($total_rate,2,".",","); ?></b></td>
			<td align="right"> <b><? echo "$&nbsp;".number_format($total_value,2,".",","); ?></b></td>
		</tr>

		<tr>
			<td colspan="12"><b>TOTAL UNITED STATES DOLLARS <? echo number_to_words(def_number_format($total_value,2,""),"USD", "CENTS")." ONLY";?></b></td>
		</tr>
		
		<tr>
			<td colspan="12"><h3 style="text-align: center;"><u>STATEMENT ON ORIGIN</u></h3>  				
			The Exporter EXPERIENCE CLOTHING COMPANY LTD.  REX Registration No.  BDREX01053 of the products covered by this document											
			declares that, except where otherwise clearly indicated, these products are of BANGLADESH preferential origin according to rules											
			of Origin of the Generalized System of Preferences of the European Union and that the origin criterion met is "W" 6206.											

			</td>
		</tr>

		<tr>
			<td colspan="5">
				<table width='400'  cellspacing="0" cellpadding="0" border="0" style="border: none;">  
					<tr style="font-weight:bold">
						<td width ="150" align="left">TOTAL G. WT:</td>
						<td width ="250" align="left">: <? echo number_format($gross_weight,2,".",","); ?> KG</td>
					</tr>
					<tr style="font-weight:bold">
						<td width ="150" align="left">TOTAL N. WT:</td>
						<td width ="250" align="left">: <? echo number_format($net_weight,2,".",","); ?> KG</td>
					</tr>
					<tr style="font-weight:bold">
						<td width ="150" align="left">CTN MEAS</td>
						<td width ="250" align="left">: <? echo $total_measurment; ?> CM</td>
					</tr>
					<tr style="font-weight:bold">
						<td width ="150" align="left">CBM</td>
						<td width ="250" align="left">: <? echo $cbm_qnty; ?> CBM</td>
					</tr>
				</table>
			</td>
		</tr>

	</table>
	<br>

	<?
		$html = ob_get_contents();
		ob_clean();
		foreach (glob("tb*.xls") as $filename) {
		@unlink($filename);
		}
		//---------end------------//
		$name=time();
		$filename="tb".$user_id."_".$name.".xls";
		$create_new_doc = fopen($filename, 'w');
		$is_created = fwrite($create_new_doc, $html);
		echo "$filename####$html";
	exit();	
}

if($action=="invoice_report_print_ci_ny")  // CI-NY
{
	extract($_REQUEST);
	// $update_id=$data;
	echo load_html_head_contents("Export Invoice - CI-NY", "../../../", 1, 1);
	$brand_arr=return_library_array( "select id, brand_name from lib_buyer_brand brand",'id','brand_name');
	$applicant_sql=sql_select( "select a.id, a.buyer_name, a.short_name, a.address_1 from lib_buyer a");
	foreach($applicant_sql as $row)
	{
		$buyer_name_arr[$row[csf("id")]]["buyer_name"]=$row[csf("buyer_name")];
		$buyer_name_arr[$row[csf("id")]]["address_1"]=$row[csf("address_1")];
	}
	$bank_sql=sql_select( "SELECT id, bank_name, branch_name, address, swift_code from lib_bank");
	foreach($bank_sql as $row)
	{
		$bank_name_arr[$row[csf("id")]]["bank_name"]=$row[csf("bank_name")];
		$bank_name_arr[$row[csf("id")]]["branch_name"]=$row[csf("branch_name")];
		$bank_name_arr[$row[csf("id")]]["address"]=$row[csf("address")];
		$bank_name_arr[$row[csf("id")]]["swift_code"]=$row[csf("swift_code")];
	}
	$bank_account_sql=sql_select( "select id, account_id, account_type, account_no from lib_bank_account where is_deleted=0 ");
	foreach($bank_account_sql as $row)
	{
		$bank_acc_arr[$row[csf("account_id")]][$row[csf("account_type")]]["account_no"]=$row[csf("account_no")];
	}
	$inv_master_data=sql_select("SELECT id, benificiary_id, buyer_id, location_id, invoice_no, invoice_date, exp_form_no, exp_form_date, is_lc, lc_sc_id, bl_no, feeder_vessel, inco_term, inco_term_place, shipping_mode, port_of_entry, port_of_loading, port_of_discharge, main_mark, side_mark, carton_net_weight, carton_gross_weight, cbm_qnty, place_of_delivery, delv_no, consignee, notifying_party, item_description, discount_ammount, bonus_ammount, commission, total_carton_qnty, bl_date, hs_code, mother_vessel, category_no, forwarder_name, etd,co_no, total_measurment, invoice_value, net_invo_value, container_no, seal_no, etd, country_id,commission_percent from com_export_invoice_ship_mst where id=$update_id");
	$id=$inv_master_data[0][csf("id")];
	$benificiary_id=$inv_master_data[0][csf("benificiary_id")];
	$buyer_id=$inv_master_data[0][csf("buyer_id")];
	$location_id=$inv_master_data[0][csf("location_id")];
	$invoice_no=$inv_master_data[0][csf("invoice_no")];
	$invoice_date=$inv_master_data[0][csf("invoice_date")];
	$exp_form_no=$inv_master_data[0][csf("exp_form_no")];
	$exp_form_date=$inv_master_data[0][csf("exp_form_date")];
	$is_lc=$inv_master_data[0][csf("is_lc")];
	$lc_sc_id=$inv_master_data[0][csf("lc_sc_id")];
	$bl_no=$inv_master_data[0][csf("bl_no")];
	$feeder_vessel=$inv_master_data[0][csf("feeder_vessel")];
	$inco_term=$inv_master_data[0][csf("inco_term")];
	$inco_term_place=$inv_master_data[0][csf("inco_term_place")];
	$shipping_mode=$inv_master_data[0][csf("shipping_mode")];
	$port_of_entry=$inv_master_data[0][csf("port_of_entry")];
	$port_of_loading=$inv_master_data[0][csf("port_of_loading")];
	$port_of_discharge=$inv_master_data[0][csf("port_of_discharge")];
	$main_mark=$inv_master_data[0][csf("main_mark")];
	$side_mark=$inv_master_data[0][csf("side_mark")];
	$net_weight=$inv_master_data[0][csf("carton_net_weight")];
	$gross_weight=$inv_master_data[0][csf("carton_gross_weight")];
	$cbm_qnty=$inv_master_data[0][csf("cbm_qnty")];
	$place_of_delivery=$inv_master_data[0][csf("place_of_delivery")];
	$delv_no=$inv_master_data[0][csf("delv_no")];
	$consignee=$inv_master_data[0][csf("consignee")];
	$notifying_party=$inv_master_data[0][csf("notifying_party")];
	$item_description=$inv_master_data[0][csf("item_description")];
	$discount_ammount=$inv_master_data[0][csf("discount_ammount")];
	$bonus_ammount=$inv_master_data[0][csf("bonus_ammount")];
	$commission=$inv_master_data[0][csf("commission")];
	$commission_percent=$inv_master_data[0][csf("commission_percent")];
	$total_carton_qnty=$inv_master_data[0][csf("total_carton_qnty")];
	$bl_date=$inv_master_data[0][csf("bl_date")];
	$hs_code=$inv_master_data[0][csf("hs_code")];
	$mother_vessel=$inv_master_data[0][csf("mother_vessel")];
	$category_no=$inv_master_data[0][csf("category_no")];
	$forwarder_name=$inv_master_data[0][csf("forwarder_name")];
	$etd=$inv_master_data[0][csf("etd")];
	$co_no=$inv_master_data[0][csf("co_no")];
	$total_measurment=$inv_master_data[0][csf("total_measurment")];
	$net_invo_value=$inv_master_data[0][csf("net_invo_value")];
	$container_no=$inv_master_data[0][csf("container_no")];
	$seal_no=$inv_master_data[0][csf("seal_no")];
	$etd=$inv_master_data[0][csf("etd")];
	$inv_country_id=$inv_master_data[0][csf("country_id")];
	$total_discount=$inv_master_data[0][csf("invoice_value")]-$inv_master_data[0][csf("net_invo_value")];
	
	$itemIdArr=array();
	$setQtyArr=array();
	$poIdArr=array();
	$dtls_sql="select a.id as dtls_id, a.po_breakdown_id,c.total_set_qnty from  com_export_invoice_ship_dtls a,  wo_po_break_down b, wo_po_details_master c where a.po_breakdown_id=b.id and b.job_no_mst=c.job_no and a.current_invoice_qnty>0 and a.status_active=1 and a.is_deleted=0 and a.mst_id=$update_id";
	$PO_agent=sql_select($dtls_sql);
	foreach($PO_agent as $row){
		$poIdArr[]=$row[csf('po_breakdown_id')];
		$setQtyArr[$row[csf('po_breakdown_id')]]=$row[csf('total_set_qnty')];
	}
	
	$carton_arr=array();
	$sqlCarton=sql_select("SELECT a.id,a.sys_number, a.dl_no, b.delivery_mst_id,b.po_break_down_id,b.total_carton_qnty,b.carton_qnty from pro_ex_factory_delivery_mst a,pro_ex_factory_mst b where a.id=b.delivery_mst_id and b.po_break_down_id in(".implode(",",$poIdArr).")");
	foreach($sqlCarton as $rowCarton)
	{
		$carton_arr[$rowCarton[csf('po_break_down_id')]]=$rowCarton[csf('total_carton_qnty')];
	}
	$agent_id="";
	// $fristPo=array_shift($poIdArr);
	$sql_fabric=sql_select("select b.ID, c.CONSTRUCTION, c.COMPOSITION from wo_po_break_down b, wo_pre_cost_fabric_cost_dtls c where b.job_no_mst=c.job_no and b.id in(".implode(",",$poIdArr).")");

	foreach($sql_fabric as $row_fabric){
		$fabric_info[$row_fabric["ID"]]=$row_fabric["CONSTRUCTION"]." ".$row_fabric["COMPOSITION"];
	}
	
	
	if($is_lc==1)
	{
		$lc_sc_data=sql_select("SELECT id, export_lc_no, lc_date, notifying_party, consignee, issuing_bank_name, negotiating_bank, lien_bank, pay_term, applicant_name,inco_term,lien_bank,nominated_shipp_line, buyer_name, tenor,shipping_mode,INTERNAL_FILE_NO from com_export_lc where id='".$lc_sc_id."' ");
		foreach($lc_sc_data as $row)
		{
			$lc_sc_id=$row[csf("id")];
			$lc_sc_no=$row[csf("export_lc_no")];
			$lc_sc_date=change_date_format($row[csf("lc_date")]);
			$notifying_party=$row[csf("notifying_party")];
			$consignee=$row[csf("consignee")];
			$issuing_bank_name=$row[csf("issuing_bank_name")];
			$negotiating_bank=$row[csf("lien_bank")];
			$pay_term_id=$row[csf("pay_term")];
			$applicant_name=$row[csf("applicant_name")];
			$buyer_name=$row[csf("buyer_name")];
			$inco_term=$row[csf("inco_term")];
			$lien_bank=$row[csf("lien_bank")];
			$shipping_line=$row[csf("nominated_shipp_line")];
			$negotiating_bank_text=$row[csf("negotiating_bank")];
			$tenor=$row[csf("tenor")];
			$shipping_mode=$row[csf("shipping_mode")];
			$file_no=$row["INTERNAL_FILE_NO"];
		}
		
			$cate_hs_sql=sql_select("SELECT wo_po_break_down_id, fabric_description, category_no, hs_code from com_export_lc_order_info where com_export_lc_id='".$lc_sc_id."'");
			foreach($cate_hs_sql as $row)
			{
				$order_la_data[$row[csf("wo_po_break_down_id")]]["category_no"]=$row[csf("category_no")];
				$order_la_data[$row[csf("wo_po_break_down_id")]]["hs_code"]=$row[csf("hs_code")];
				// $order_la_data[$row[csf("wo_po_break_down_id")]]["fabric_description"]=$row[csf("fabric_description")];
			    $all_order_id[$row[csf("wo_po_break_down_id")]]=$row[csf("wo_po_break_down_id")];
			}
	}
	else
	{
		$lc_sc_data=sql_select("SELECT id, contract_no, contract_date, notifying_party, consignee, lien_bank, pay_term, applicant_name,inco_term,lien_bank,shipping_line,buyer_name, tenor,shipping_mode,INTERNAL_FILE_NO from com_sales_contract where id='".$lc_sc_id."'  and status_active=1");
		foreach($lc_sc_data as $row)
		{
			$lc_sc_id=$row[csf("id")];
			$lc_sc_no=$row[csf("contract_no")];
			$lc_sc_date=change_date_format($row[csf("contract_date")]);
			$notifying_party=$row[csf("notifying_party")];
			$consignee=$row[csf("consignee")];
			$issuing_bank_name="";
			$negotiating_bank=$row[csf("lien_bank")];
			$pay_term_id=$row[csf("pay_term")];
			$applicant_name=$row[csf("applicant_name")];
			$buyer_name=$row[csf("buyer_name")];
			$inco_term=$row[csf("inco_term")];
			$lien_bank=$row[csf("lien_bank")];
			$shipping_line=$row[csf("shipping_line")];
			$tenor=$row[csf("tenor")];
			$shipping_mode=$row[csf("shipping_mode")];
			$file_no=$row["INTERNAL_FILE_NO"];
			$negotiating_bank_text="";
		}
		
		$cate_hs_sql=sql_select("SELECT wo_po_break_down_id, fabric_description, category_no, hs_code from com_sales_contract_order_info where com_sales_contract_id='".$lc_sc_id."' and status_active=1");
		foreach($cate_hs_sql as $row)
		{
			$order_la_data[$row[csf("wo_po_break_down_id")]]["category_no"]=$row[csf("category_no")];
			$order_la_data[$row[csf("wo_po_break_down_id")]]["hs_code"]=$row[csf("hs_code")];
			$order_la_data[$row[csf("wo_po_break_down_id")]]["fabric_description"]=$row[csf("fabric_description")];
			$all_order_id[$row[csf("wo_po_break_down_id")]]=$row[csf("wo_po_break_down_id")];
		}
	}
	
	$company_name_sql=sql_select( "SELECT id, company_name, plot_no, level_no, road_no, block_no, city, country_id,erc_no,email,contact_no,rex_no,rex_reg_date,irc_no,vat_number,bin_no from lib_company where id ='$benificiary_id'");
	foreach($company_name_sql as $row)
	{
		$company_name=$row[csf("company_name")];
		$plot_no=$row[csf("plot_no")];
		$level_no=$row[csf("level_no")];
		$road_no=$row[csf("road_no")];
		$block_no=$row[csf("block_no")];
		$city=$row[csf("city")];
		$country_id=$row[csf("country_id")];
		$erc_no=$row[csf("erc_no")];
		$contact_no=$row[csf("contact_no")];
		$email=$row[csf("email")];
		$rex_no=$row[csf("rex_no")];
		$rex_reg_date=$row[csf("rex_reg_date")];
		$irc_no=$row[csf("irc_no")];
		$vat_number=$row[csf("vat_number")];
		$bin_no=$row[csf("bin_no")];
	}
	
	$country_name_arr=return_library_array( "SELECT id, country_name from lib_country",'id','country_name');
	// $carrier=$SupplierArr[$forwarder_name];
	$applicant=$buyer_name_arr[$applicant_name]["buyer_name"];
	$applicantAddress=$buyer_name_arr[$applicant_name]["address_1"];
	$agent=$buyer_name_arr[$agent_id]["buyer_name"];
	$agentAddress=$buyer_name_arr[$agent_id]["address_1"];
		
	$dtls_sql="SELECT a.id as dtls_id, a.po_breakdown_id, a.current_invoice_rate, a.current_invoice_qnty, a.current_invoice_value, b.po_number, c.style_ref_no, c.gmts_item_id, c.order_uom, c.gmts_item_id,c.STYLE_DESCRIPTION, c.BRAND_ID from  com_export_invoice_ship_dtls a,  wo_po_break_down b, wo_po_details_master c where a.po_breakdown_id=b.id and b.job_no_mst=c.job_no and a.current_invoice_qnty>0 and a.status_active=1 and a.is_deleted=0 and a.mst_id=$update_id";
	// echo $dtls_sql;
	$result=sql_select($dtls_sql);
	$company_logo=return_field_value("image_location","common_photo_library","file_type=1 and form_name='company_details' and master_tble_id='$benificiary_id'","image_location");

	ob_start();
	?>
    <table width='1000' cellspacing="0" cellpadding="0" border="1" >
        <tr>
            <td colspan="10" valign="top" align="center"><b><u>INVOICE</u></b></td>
        </tr>
        <tr>
            <td colspan="3" rowspan="3" valign="top"> 
                <strong><u>Exporter:</u></strong>
                <br/>
                <strong>FACTORY ADDRESS</strong>
                <br/>
                <?php echo $company_name;?>
                <?php
                if($city!="")  $comany_details.= "<br>".$city.", ";
                if($country_id!="")  $comany_details.="<br>".$country_name_arr[$country_id].".";
                if($contact_no!="")  $comany_details.="<br>Telephone: ".$contact_no.".";
                if($email!="")  $comany_details.="<br>E-MAIL: ".$email.".";
                echo  $comany_details;
                ?>
            </td>
            <td colspan="4" valign="top">
				<strong><u>Invoice No. & Date:</u></strong>
                <br/>
				<?php echo $invoice_no;  ?><br/>
				DT. <? echo change_date_format($invoice_date);?><br/>
            </td>
            <td colspan="3" valign="top">
				<strong><u>Export's Ref:</u></strong>
				<br/>
                <?php echo $exp_form_no; ?><br/>
            </td>
        </tr>
		<tr>
			<td colspan="4">Buyer's Order No. Date</td>
			<td colspan="3">L/C No. & Date<br><?php echo $lc_sc_no; ?> Date: <? echo change_date_format($lc_sc_date);?></td>
		</tr>
		<tr>
			<td colspan="7">Other Reference(s)</td>
		</tr>
		<tr>
            <td colspan="3" valign="top"> 
                <strong><u>CONSIGNEE: </u></strong><br/>
				<?
                if($buyer_name_arr[$consignee]["buyer_name"]!=''){echo $buyer_name_arr[$consignee]["buyer_name"]."<br/>";}
                if($buyer_name_arr[$consignee]["address_1"]!=''){echo $buyer_name_arr[$consignee]["address_1"]."<br/>";}
                ?>
            </td>
			<td colspan="2" valign="top">
				<strong>Buyer</strong><br/>
				<strong><u>NOTIFY PARTY:</u></strong><br/>
				<? if($buyer_name_arr[$notifying_party]["buyer_name"]!=''){echo $buyer_name_arr[$notifying_party]["buyer_name"]."<br/>";}
                if($buyer_name_arr[$notifying_party]["address_1"]!=''){echo $buyer_name_arr[$notifying_party]["address_1"];}
                ?>
			</td>
			<td colspan="2" valign="top">
				<strong>Issuer name and address</strong><br/>
				<?=$agent;?><br/><?=$agentAddress;?>
			</td>
			<td colspan="3" valign="top">
				<strong>Exp No. & Date</strong><br/>
				<?php echo $exp_form_no; ?>&nbsp;DATE:&nbsp;<? if($exp_form_date!="" && $exp_form_date!="0000-00-00" ) echo change_date_format($exp_form_date);?>
			</td>
        </tr>
		<tr>
			<td valign="top">Pre-Carriage by</td>
			<td valign="top" colspan="2">Place of Receipt by Pre-carrier</td>
			<td valign="top" colspan="3">Country of Origin of Goods<br>BANGLADESH</td>
			<td valign="top"><?=$bin_no;?></td>
			<td valign="top" colspan="3">Country of Final Destination<br><?=$country_name_arr[$inv_country_id];?></td>
		</tr>
		<tr>
			<td valign="top">Vessel / Flight No<br><? echo $feeder_vessel;?></td>
			<td valign="top" colspan="2">Port of Loading<br><? echo $port_of_loading;?></td>
			<td valign="top" colspan="7" rowspan="2">Terms of Delivery and Payment<br><?=$incoterm[$inco_term].", ".$port_of_discharge;?></td>
		</tr>
		<tr>
			<td valign="top">Port of Discharge<br><? echo $port_of_discharge;?></td>
			<td valign="top" colspan="2">Final Distination<br><? echo $port_of_loading;?></td>
		</tr>
		<tr>
			<td valign="top">Marks & Nos./ Container No.</td>
			<td valign="top" colspan="5">Description of Goods</td>
			<td align="center">Quantity <br>PCS / SETS</td>
			<td align="center">Quantity <br>CTNS</td>
			<td align="center">Rate <br>USD</td>
			<td align="center">Amount <br>FOB/USD</td>
		</tr>
		<tr>
			<td align="center" valign="top" colspan="6" style='border-bottom:none;'>CARTON NO <?php echo $bl_no; ?></td>
			<td style='border-top:none;border-bottom:none;'></td>
			<td style='border-top:none;border-bottom:none;'></td>
			<td style='border-top:none;border-bottom:none;'></td>
			<td style='border-top:none;border-bottom:none;'></td>
		</tr>
		<tr>
			<td align="center" colspan="2"  style='border-top:none;border-right:none;border-bottom:none;'>COMPOSITION / DESCRIPTION</td>
			<td style='border:none;'></td>
			<td style='border:none;' align="center">STYLE NO</td>
			<td style='border:none;' align="center">ORDER NO.</td>
			<td style='border:none;' align="center">ARTICLE NO</td>
			<td style='border-top:none;border-bottom:none;'></td>
			<td style='border-top:none;border-bottom:none;'></td>
			<td style='border-top:none;border-bottom:none;'></td>
			<td style='border-top:none;border-bottom:none;'></td>
		</tr>        
        <?
		$i=1;
		foreach($result as $row)
		{
			?>
            <tr style="font-size:small">
                <td style='border:none;' width="150" colspan="2"> <? echo $fabric_info[$row[csf('po_breakdown_id')]]; ?> </td>
                <td style='border:none;' width="100"> <? echo $fabric_info[$row[csf('po_breakdown_id')]]; ?> </td>
                <td style='border:none;' width="100"><? echo $row[csf('style_ref_no')]; ?></td>
                <td style='border:none;' width="100"><? echo $row[csf("po_number")]; ?></td>
                <td style='border:none;' width="100"> <? echo $row[csf('STYLE_DESCRIPTION')]; ?> </td>
                <td style='border-top:none;border-bottom:none;' width="100" align="right"><? echo number_format($row[csf('current_invoice_qnty')]*$setQtyArr[$row[csf('po_breakdown_id')]],0,".",",");  ?></td>
                <td style='border-top:none;border-bottom:none;' width="100" align="right"><? echo number_format($carton_arr[$row[csf('po_breakdown_id')]],0,".",","); ?></td>
                <td style='border-top:none;border-bottom:none;' width="100" align="right"><?  echo "$".number_format($row[csf("current_invoice_rate")],2); ?></td>
                <td style='border-top:none;border-bottom:none;' align="right"><? echo "$".number_format($row[csf("current_invoice_value")],2,".",","); ?></td>
            </tr>
            <?
			$total_value+=$row[csf("current_invoice_value")];
			$total_qnty+=$row[csf("current_invoice_qnty")]*$setQtyArr[$row[csf('po_breakdown_id')]];
			$last_uom=$unit_of_measurement[$row[csf('order_uom')]];
			$total_po_carton_qnty+=$carton_arr[$row[csf('po_breakdown_id')]];
			$i++;
		}
		?>
		<tr>
			<td colspan="6" style='border-top:none;'>
				HS-Code is <?=$hs_code;?>
				<br>Category is
				<br>SHIPPING MARK:
				<br>UND W:
				<br>PCS/CTN:
				<br>w:
				<br>L:
				<br>N.W.PER CTN(KGS): TOTAL PCS <? echo number_format($net_weight,2,".",","); ?>
				<br>G.W.PER CTN(KGS): TOTAL CTNS <? echo number_format($gross_weight,2,".",","); ?>
				<br>MEAS(L8B8D): &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;TOTAL NET WT.
				<br>CONT.SIZE &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;TOTAL G.WT.
				<br>TTL N.W.CTN(KGS): TOTAL CBM. <? echo number_format($cbm_qnty,2); ?>
				<br>TTL G.W.CTN(KGS):
				<br>EMPTY CTN
				<br>DESORTED CTN:
				<br>TTL DESORTED CTN:
				<br>TTL PIRCES:
			</td>
			<td style='border-top:none;'></td>
			<td style='border-top:none;'></td>
			<td style='border-top:none;'></td>
			<td style='border-top:none;'></td>
		</tr>
        <tr>
            <td colspan="6" align="right">Total</td>
            <td align="right"><? echo number_format($total_qnty,0,".",",")." Pcs" ?></td>
			<td align="right"><? echo $total_po_carton_qnty; ?></td>
            <td >US$</td>
            <td align="right"> <? echo "$&nbsp;".number_format($total_value,2,".",","); ?></td>
        </tr>
		<tr>
            <td colspan="10">Amount Chargeable <strong><? echo number_to_words(def_number_format($net_invo_value,2,""),"USD", "CENTS");?></strong></td>
        </tr>
		<tr>
			<td colspan="10" align="center">
			<strong> <u> STATMENT ON ORIGIN </u><br>
			The exporter Fabric Knit Composite Ltd. <?=$rex_no;?> of the products covered by this document declares that except where Otherwise clearly indicated, these products are of Bangladesh prferential origin according to rules of origin of the Generalized System of Preferences of the European Union and that the origin criterion met is W <?=substr($hs_code,0,4) ;?> </strong>
			</td>
		</tr>
		<tr>
            <td colspan="10" valign="top" style='border-bottom:none;'>
				VAT NUMBER: <?=$vat_number;?> 
				<br>REX REGISTRATION: <?=$rex_no;?> DATE: <?=change_date_format($rex_reg_date);?>
				<br>FILE NO: <?=$file_no;?> 
				<br>UD:  DATE: 
				<br>BOOKING NO: <?php echo $bl_no; ?>
            </td>
		</tr>
		<tr >
            <td colspan="7" valign="top" style='border-top:none'>
				Declaration
				<br>We declare that this Invoice shows the actual Price of the goods
				<br>described and that all praticulars are true and correct.
            </td>
			<td colspan="3" align="center" valign="bottom">
				Authorised Singnatory
			</td>
        </tr>
    </table>
	<?
	$html = ob_get_contents();
	ob_clean();
	foreach (glob("tb*.xls") as $filename) {
	@unlink($filename);
	}
	//---------end------------//
	$name=time();
	$filename="tb".$user_id."_".$name.".xls";
	$create_new_doc = fopen($filename, 'w');
	$is_created = fwrite($create_new_doc, $html);
	echo "$filename####$html";
	exit();	
}


if($action=='fro_top_sheet_print') // Develop By WAYASEL
{
	extract($_REQUEST);
	$print_type=explode(",",$selected_print_report);
	$selected_seq_id_arr = explode(",",$selected_seq_id);

	$print_type_arr = array_flip($print_type);

	//echo $selected_seq_id_arr[$print_type_arr[2]];die;
	// for($ti=0;$selected_seq_id_arr[$print_type_arr[2]]>$ti; $ti++){	  
	// }

	$company_arr = return_library_array("select id, company_name from lib_company ","id","company_name");
	$size_arr=return_library_array( "select id,size_name from lib_size where status_active=1 and is_deleted=0",'id','size_name');
	$tblRow=0;
	$bank_sql=sql_select( "SELECT A.ID, A.BANK_NAME, A.BRANCH_NAME, A.ADDRESS, A.SWIFT_CODE FROM LIB_BANK A");
	foreach($bank_sql as $row)
	{
		$bank_name_arr[$row["ID"]]["BANK_NAME"]=$row["BANK_NAME"];
		$bank_name_arr[$row["ID"]]["BRANCH_NAME"]=$row["BRANCH_NAME"];
		$bank_name_arr[$row["ID"]]["ADDRESS"]=$row["ADDRESS"];
		$bank_name_arr[$row["ID"]]["SWIFT_CODE"]=$row["SWIFT_CODE"];
	}
	$bank_account_sql=sql_select( "SELECT ID, ACCOUNT_ID, ACCOUNT_TYPE, ACCOUNT_NO FROM LIB_BANK_ACCOUNT WHERE IS_DELETED=0 ");
	foreach($bank_account_sql as $row)
	{
		$bank_acc_arr[$row["ACCOUNT_ID"]][$row["ACCOUNT_TYPE"]]["ACCOUNT_NO"]=$row["ACCOUNT_NO"];
	}

	$applicant_sql=sql_select( "SELECT A.ID, A.BUYER_NAME, A.SHORT_NAME, A.ADDRESS_1 FROM LIB_BUYER A");

	foreach($applicant_sql as $row)
	{
		$buyer_name_arr[$row["ID"]]["BUYER_NAME"]=$row["BUYER_NAME"];
		$buyer_name_arr[$row["ID"]]["ADDRESS_1"]=$row["ADDRESS_1"];
	}

	$inv_master_data=sql_select("SELECT ID, BENIFICIARY_ID, BUYER_ID, LOCATION_ID, INVOICE_NO, INVOICE_DATE, EXP_FORM_NO, EXP_FORM_DATE, IS_LC, LC_SC_ID, BL_NO, FEEDER_VESSEL, INCO_TERM, INCO_TERM_PLACE, SHIPPING_MODE, PORT_OF_ENTRY, PORT_OF_LOADING, PORT_OF_DISCHARGE, MAIN_MARK, SIDE_MARK, CARTON_NET_WEIGHT, CARTON_GROSS_WEIGHT, CBM_QNTY, PLACE_OF_DELIVERY, DELV_NO, CONSIGNEE, NOTIFYING_PARTY, ITEM_DESCRIPTION, DISCOUNT_AMMOUNT, BONUS_AMMOUNT, COMMISSION, TOTAL_CARTON_QNTY, BL_DATE, HS_CODE, MOTHER_VESSEL, CATEGORY_NO, FORWARDER_NAME, ETD,CO_NO, TOTAL_MEASURMENT, INVOICE_VALUE, NET_INVO_VALUE, CONTAINER_NO, SEAL_NO, ETD, COUNTRY_ID,COMMISSION_PERCENT,BL_REV_DATE FROM COM_EXPORT_INVOICE_SHIP_MST WHERE ID=$update_id");
	$id=$inv_master_data[0]["ID"];
	$invoice_no=$inv_master_data[0]["INVOICE_NO"];
	$invoice_date=$inv_master_data[0]["INVOICE_DATE"];
	$is_lc=$inv_master_data[0]["IS_LC"];
	$lc_sc_id=$inv_master_data[0]["LC_SC_ID"];
	$net_invo_value=$inv_master_data[0]["NET_INVO_VALUE"];
	$exp_form_no=$inv_master_data[0]["EXP_FORM_NO"];
	$exp_form_date=$inv_master_data[0]["EXP_FORM_DATE"];
	$buyer_id=$inv_master_data[0]["BUYER_ID"];
	$benificiary_id=$inv_master_data[0]["BENIFICIARY_ID"];

	$company_name_sql=sql_select("SELECT ID, COMPANY_NAME, PLOT_NO, LEVEL_NO, ROAD_NO, BLOCK_NO, CITY, COUNTRY_ID,ERC_NO,EMAIL,CONTACT_NO,REX_NO,REX_REG_DATE,IRC_NO,VAT_NUMBER, BIN_NO, TIN_NUMBER FROM LIB_COMPANY WHERE ID ='$benificiary_id'");
	foreach($company_name_sql as $row)
	{
		$company_name=$row["COMPANY_NAME"];
		$plot_no=$row["PLOT_NO"];
		$level_no=$row["LEVEL_NO"];
		$road_no=$row["ROAD_NO"];
		$block_no=$row["BLOCK_NO"];
		$city=$row["CITY"];
		$country_id=$row["COUNTRY_ID"];
		$erc_no=$row["ERC_NO"];
		$contact_no=$row["CONTACT_NO"];
		$email=$row["EMAIL"];
		$rex_reg_date=$row["REX_REG_DATE"];
		$irc_no=$row["IRC_NO"];
		$vat_number=$row["VAT_NUMBER"];
		$bin_no=$row["BIN_NO"];
		$rex_no=$row["REX_NO"];
		$tin_number=$row["TIN_NUMBER"];
		$address=$plot_no.",".$level_no.",".$road_no.",".$block_no;
	}

	$vat_bin_reg_no="";
	if($vat_number!="" && $rex_no!=""){
		$vat_bin_reg_no=$vat_number." / ".$rex_no;
	}else if($rex_no=="" && $vat_number!=""){
		$vat_bin_reg_no=$vat_number;
	}else if($vat_number=="" && $rex_no!=""){
		$vat_bin_reg_no=$rex_no;
	}



	$invoice_sql="SELECT a.po_breakdown_id as PO_BREAKDOWN_ID, b.CARTON_NET_WEIGHT, b.CARTON_GROSS_WEIGHT
	FROM com_export_invoice_ship_dtls a, com_export_invoice_ship_mst b 
	where a.mst_id=b.id and  a.status_active=1 and a.is_deleted=0 and b.ID =$update_id";
	
	$data_array_invoice=sql_select($invoice_sql);
	$po_invoice_data_array=array();
	foreach($data_array_invoice as $row)
	{
		$po_invoice_data_array[$row['PO_BREAKDOWN_ID']]['CARTON_NET_WEIGHT']=$row['CARTON_NET_WEIGHT'];
		$po_invoice_data_array[$row['PO_BREAKDOWN_ID']]['CARTON_GROSS_WEIGHT']=$row['CARTON_GROSS_WEIGHT'];
	}

	
	if($is_lc==1) 
	{
		$lc_sc_data=sql_select("SELECT ID, EXPORT_LC_NO, LC_DATE, NOTIFYING_PARTY, CONSIGNEE, ISSUING_BANK_NAME, NEGOTIATING_BANK, LIEN_BANK, PAY_TERM, APPLICANT_NAME,INCO_TERM,LIEN_BANK,NOMINATED_SHIPP_LINE, BUYER_NAME, SHIPPING_MODE,LC_VALUE, TENOR, PAY_TERM, PORT_OF_LOADING FROM COM_EXPORT_LC WHERE ID='".$lc_sc_id."' ");
		
		foreach($lc_sc_data as $row)
		{
			$lc_sc_no=$row["EXPORT_LC_NO"];
			$lc_sc_value = $row['LC_VALUE'];
			$lc_sc_date=change_date_format($row["LC_DATE"]);
			$lien_bank=$row["LIEN_BANK"];	
			$issuing_bank_name=$row["ISSUING_BANK_NAME"];	
			$notifying_party=$row["NOTIFYING_PARTY"];	
			$tenor=$row["TENOR"];	
			$pay_term=$row["PAY_TERM"];	
			$port_of_loading=$row["PORT_OF_LOADING"];	
			$shipping_mode=$row["SHIPPING_MODE"];	

		}
		$sql_de="SELECT d.TRIMS_DEL, c.ORDER_NO, c.ID, d.DELIVERY_DATE  FROM com_export_lc_order_info a, SUBCON_ORD_DTLS b, SUBCON_ORD_MST c left join  TRIMS_DELIVERY_MST d on c.id=d.RECEIVED_ID AND d.STATUS_ACTIVE=1 where a.WO_PO_BREAK_DOWN_ID=b.id and b.mst_id=c.id  AND a.STATUS_ACTIVE=1 AND b.STATUS_ACTIVE=1 AND c.STATUS_ACTIVE=1  and a.COM_EXPORT_LC_ID=$lc_sc_id group by d.TRIMS_DEL, c.ORDER_NO, c.ID, d.DELIVERY_DATE";
	}
	else
	{
		$lc_sc_data=sql_select("SELECT ID, CONTRACT_NO, CONTRACT_DATE, NOTIFYING_PARTY, CONSIGNEE, LIEN_BANK, PAY_TERM, APPLICANT_NAME,INCO_TERM,SHIPPING_LINE,BUYER_NAME, SHIPPING_MODE, CONTRACT_VALUE, ISSUING_BANK, TENOR, PAY_TERM, PORT_OF_LOADING FROM COM_SALES_CONTRACT WHERE ID='".$lc_sc_id."'  AND STATUS_ACTIVE=1");
		foreach($lc_sc_data as $row)
		{
			$lc_sc_no=$row["CONTRACT_NO"];
			$lc_sc_value=$row["CONTRACT_VALUE"];
			$lc_sc_date=change_date_format($row["CONTRACT_DATE"]);
			$lien_bank=$row["LIEN_BANK"];
			$issuing_bank_name=$row["ISSUING_BANK"];
			$notifying_party=$row["NOTIFYING_PARTY"];	
			$tenor=$row["TENOR"];	
			$pay_term=$row["PAY_TERM"];	
			$port_of_loading=$row["PORT_OF_LOADING"];	
			$shipping_mode=$row["SHIPPING_MODE"];	
		}

		$sql_de="SELECT d.TRIMS_DEL, c.ORDER_NO, c.ID, d.DELIVERY_DATE FROM com_export_lc_order_info a, SUBCON_ORD_DTLS b, SUBCON_ORD_MST c left join  TRIMS_DELIVERY_MST d on c.id=d.RECEIVED_ID AND d.STATUS_ACTIVE=1 where a.WO_PO_BREAK_DOWN_ID=b.id and b.mst_id=c.id   AND a.STATUS_ACTIVE=1 AND b.STATUS_ACTIVE=1 AND c.STATUS_ACTIVE=1  and a.COM_EXPORT_LC_ID=$lc_sc_id group by d.TRIMS_DEL, c.ORDER_NO, c.ID, d.DELIVERY_DATE";
	
	}

	$sql_del=sql_select($sql_de);

	$btb_lc_attachment_sql=sql_select("SELECT a.id, a.is_lc_sc, a.lc_sc_id ,b.export_lc_no as LC_SC_NO, b.lc_date as LC_SC_DATE, c.LC_NUMBER, c.LC_DATE, c.LCAF_NO,c.TENOR, d.HS_CODE, d.PI_NUMBER, d.PI_DATE
	from com_btb_export_lc_attachment a, com_export_lc b, com_btb_lc_master_details c, com_pi_master_details d
	where a.LC_SC_ID=$lc_sc_id and a.is_lc_sc=0 and a.lc_sc_id=b.id and c.id=a.IMPORT_MST_ID and c.PI_ID=d.id and a.is_deleted = 0 and a.status_active=1 and b.is_deleted = 0 and b.status_active=1  and c.status_active=1  and d.status_active=1 
	union all
	select a.id, a.is_lc_sc, a.lc_sc_id ,b.contract_no as LC_SC_NO, b.contract_date as LC_SC_DATE, c.LC_NUMBER, c.LC_DATE, c.LCAF_NO,c.TENOR, d.HS_CODE, d.PI_NUMBER, d.PI_DATE
	from com_btb_export_lc_attachment a, com_sales_contract b, com_btb_lc_master_details c, com_pi_master_details d
	where  a.LC_SC_ID=$lc_sc_id and a.is_lc_sc=1 and a.lc_sc_id=b.id and c.PI_ID=d.id and c.id=a.IMPORT_MST_ID and a.is_deleted = 0 and a.status_active=1 and b.is_deleted = 0 and b.status_active=1 and c.status_active=1  and d.status_active=1");
	
	$buyer_lc_sc_no='';
	$buyer_lc_sc_date='';
	foreach($btb_lc_attachment_sql as $row)
	{
		$btb_system_id =$row['LC_NUMBER'];
		$lc_date =$row['LC_DATE'];
		$lcaf_no =$row['LCAF_NO'];
		$hs_code =$row['HS_CODE'];
		// $tenor =$row['TENOR'];

		$pi_date.=change_date_format($row['PI_DATE']).', ';
		$pi_number.=$row['PI_NUMBER']."<b> DATE:</b>".change_date_format($row['PI_DATE']).', ';
	}

	$sql_export_pi=sql_select("SELECT b.WORK_ORDER_ID, a.PI_NUMBER FROM com_export_pi_mst a, com_export_pi_dtls b WHERE a.id=b.pi_id and a.exporter_id=$benificiary_id");
	$exportPiArr=array();
	foreach($sql_export_pi as $val){
		$exportPiArr[$val["WORK_ORDER_ID"]]["PI_NUMBER"]=$val["PI_NUMBER"];
	}

	$exportLcAmidment = sql_select("SELECT id, amendment_no, amendment_date, export_lc_no, lc_value FROM com_export_lc_amendment WHERE export_lc_id='$lc_sc_id' and amendment_no<>0 and status_active=1 and is_deleted=0 and is_original=0 order by amendment_no asc");
	foreach($exportLcAmidment as $row){
		$amendment_no=$row["AMENDMENT_NO"].",";
		$amendment_date=$row["AMENDMENT_DATE"].",";
	}
	
	$sqlDelCln=sql_select($sql_de);
	if(!empty($sqlDelCln)){
		foreach($sqlDelCln as $row){
			$challanNo.=$row["TRIMS_DEL"].",";
		}
	}

	$br="<br> <br>";
	$break="<span style='page-break-after: always;'></span>";

	ob_start();

	$print_qty=1;				
	if (in_array(1, $print_type)) {
		$print_qty=$selected_seq_id_arr[$print_type_arr[1]];
		for($ti=0;$print_qty>$ti; $ti++){	  
			?>
			<table width='1000' cellspacing="0" cellpadding="0"  >
				<tr>
					<td colspan="12" valign="top" align="left" style="font-size:Large;">
					<b><u>Date:</u></b> <br>
					To <br>
					The Manager <br>
					<? echo $bank_name_arr[$lien_bank]["BANK_NAME"]."<br>".$bank_name_arr[$lien_bank]["ADDRESS"];?>
					<br><br>
					<b>Sub</b>Submission of export documents for negotiation / collection against <br>
					<u>L/C No. : <? echo $lc_sc_no; ?> &nbsp;&nbsp;  Date : <? echo change_date_format($lc_sc_date); ?> &nbsp;&nbsp; Value  :  <? echo number_format($lc_sc_value,2)?></u>

					<br><br>
		
					Dear Sir, <br>
					With reference to the above, we are enclosed herewith the following documents for your kind Negotiation / Collection <br> <br>
					</td>
				</tr>
			</table>
			<table width='1000' cellspacing="0" cellpadding="0">
				<tr>
				<td width="40">01.</td>
				<td width="230">Bill of Exchange for U.S. </td>
				<td width="230"><b> <? echo number_format($lc_sc_value,2)?></b></td>
				</tr>
				<tr>
					<td width="40">02.</td>
					<td width="230">Commercial Invoice No. </td>
					<td width="230"><b> <? echo $invoice_no;?> </b></td>
				</tr>
				<tr>
					<td width="40">03.</td>
					<td width="230">Delivery Challan </td>
					<td width="230"><b><?=rtrim($challanNo,",");?> </b></td>
				</tr>

				<tr>
				<td width="40">04.</td>
				<td width="230">Packing List </td>
				<td width="230"><b> DO</b></td>
				</tr>
				<tr>
					<td width="40">05.</td>
					<td width="230">Certificate of Origin </td>
					<td width="230"><b> DO </b></td>
				</tr>
				<tr>
					<td width="40">06.</td>
					<td width="230">Beneficiary Certificate </td>
					<td width="230"><b> DO</b></td>
				</tr>
				<tr>
					<td width="40">07.</td>
					<td width="230">L/C No.</td>
					<td width="230"><b> <? echo $lc_sc_no; ?>  </b></td>
				</tr>
				<tr>
					<td width="40">08.</td>
					<td width="230">EXP No. ( Second Original) </td>
					<td width="230"><b><? echo $exp_form_no;?> </b></td>
				</tr>
			</table>
			<table width='1000' cellspacing="0" cellpadding="0">
				<tr>
				<td width="1000">
				<br> <br>
					Your kind consideration to negotiate / collect our said export bill that will be highly appreciated.
					<br><br>
					Thanking you, <br>
					<u>Yours faithfully,</u>
				</td>
				</tr>
			</table>
			<?
			echo $break;
		}
	}                       
					
	if (in_array(2, $print_type)) {
		$print_qty=$selected_seq_id_arr[$print_type_arr[2]];
		$count=1;
		for($ti=0;$print_qty>$ti; $ti++){	  
			?>
			<table width='1000' cellspacing="0" cellpadding="0"  >
				<tr>
					<td colspan="11" valign="top" align="center" style="font-size: 30px;" > <b> <u><i> Bill  of Exchange</i></u></b></td>
					<td  align="center"  style="font-size: 30px;"><?=$count?></td>
				</tr>
				<tr>
					<td colspan="4" valign="top" align="left"><b>INVOICE NO:</b> <?=$invoice_no?></td>
					<td colspan="4" valign="top" align="center"></td>
					<td colspan="4" valign="top" align="left"><b>DATE:</b><?=$invoice_date?></td>
				</tr>
				<tr>
					<td colspan="4" valign="top" align="left"></b>EXCHANGE FOR :<b></td>
					<td colspan="4" valign="top" align="center"><?=$net_invo_value?></td>
					<td colspan="4" valign="top" align="left"><b>ONLY</b></td>
				</tr>
				<tr>
					<td colspan="12" valign="top" align="left"><b>AMOUNT IN WORDS:</b><?=number_to_words(def_number_format($net_invo_value,2,""),"USD", "CENTS")." ONLY";?></td>
				</tr>
				<tr style="height: 10px;"></tr>
				<tr>
					<td colspan="12" valign="top" align="left"><b><?=$tenor?> DAYS FROM THE DATE OF DELIVERY of this FIRST OF EXCHANGE (SECOND of same tenor and date being unpaid)</b></td>
				</tr>	
				<tr>
					<td colspan="4" valign="top" align="left"><b>PAY TO THE ORDER OF:</b></td>
					<td colspan="8" valign="top" align="left"><?=$bank_name_arr[$lien_bank]["ADDRESS"]?></td>
				</tr>
				<tr style="height: 10px;"></tr>
				<tr>
					<td colspan="12" valign="top" align="left"><b>VALUE RECEIVED AND CHARGE AMOUNT TO ACCOUNT OF:</b></td>
				</tr>
				<tr>
					<td colspan="2"></td>
					<td colspan="10" valign="top" align="left"><?=$buyer_name_arr[$buyer_id]["BUYER_NAME"]."<br>".$buyer_name_arr[$buyer_id]["ADDRESS_1"]?></td>
				</tr>
				<tr style="height: 10px;"></tr>
				<tr>
					<td colspan="2"><b>DRAWN ON:</b></td>
					<td colspan="10" valign="top" align="left"><?=$bank_name_arr[$issuing_bank_name]["BANK_NAME"]."<br>".$bank_name_arr[$issuing_bank_name]["ADDRESS"];?></td>
				</tr>
				<tr style="height: 10px;"></tr>
				<tr>
					<td colspan="2" valign="top" align="left"><b>LETTER OF CREDIT NO:</b> </td>
					<td colspan="6" valign="top" align="left"> <?=$lc_sc_no?></td>
					<td colspan="4" valign="top" align="left"><b>DATE:</b><?=$lc_sc_date?></td>
				</tr>
				<tr style="height: 10px;"></tr>
				<tr>
					<td colspan="2" valign="top" align="left"><b>L/C AMENDMENT NO: </b></td>
					<td colspan="6" valign="top" style="word-break: break-all;" align="left"> <?=rtrim($amendment_no,",");?></td>
					<td colspan="4" valign="top" style="word-break: break-all;" align="left"><?=rtrim(change_date_format($amendment_date),",");?></td>
				</tr>
				<tr style="height: 10px;"></tr>
				<tr>
					<td colspan="2" valign="top" align="left"><b>To. </b></td>
					<td colspan="10" valign="top" align="left"><?=$bank_name_arr[$issuing_bank_name]["BANK_NAME"]."<br>".$bank_name_arr[$issuing_bank_name]["ADDRESS"];?> </td>
				</tr>
				<tr style="height: 10px;"></tr>
				<tr>
					<td colspan="2" valign="top" align="left"><b> A/C. </b></td>
					<td colspan="10" valign="top" align="left"> <?=$buyer_name_arr[$buyer_id]["BUYER_NAME"]."<br>".$buyer_name_arr[$buyer_id]["ADDRESS_1"]?> </td>
				</tr>
			</table>
			<br>
			<?$count++;
			echo $break;
		}
	}
	
	if (in_array(3, $print_type)) {
		$print_qty=$selected_seq_id_arr[$print_type_arr[3]];
		for($ti=0;$print_qty>$ti; $ti++){	  
	     	?>		
			<table border="1" width='1000' cellspacing="0" cellpadding="0">
				<tr>
					<td colspan="12" valign="top" align="center" style="font-size: 30px;" ><b>  COMMERCIAL INVOICE </b></td>
				</tr>
				<tr>
					<td colspan="6" valign="top" align="left"><b>NAME & ADDRESS OF SHIPPER / EXPORTE /BENEFICERY:</b><br>
					<b> A.  <?=$company_arr[$benificiary_id]."</b><br>".$address."<br>BIN NO: ".$bin_no?>
					<br> <br>
					<b> B.  <?=$bank_name_arr[$lien_bank]["BANK_NAME"]."</b><br>".$bank_name_arr[$lien_bank]["ADDRESS"]."<br> A/C No: ".$bank_acc_arr[$lien_bank][20]["ACCOUNT_NO"]."<br>Swift Code:".$bank_name_arr[$lien_bank]["SWIFT_CODE"];?>
				
					</td>
					<td  width="500" colspan="6" valign="top" align="left">
						<table > 
								<tr>
									<td width="250"><b> <u>INVOICE NO:</u></b></td>
									<td><b><u>INVOICE DATE:</u></b></td>
								</tr>
								<tr>
									<td><?=$invoice_no?></td>
									<td><?=$invoice_date?></td>
								</tr>
								<tr>
									<td><b></u> EXP NO:<u></b></td>
									<td><b></u>EXP DATE:<u></b></td>
								</tr>
								<tr>
									<td><? echo $exp_form_no;?> </td>
									<td><?=$exp_form_date?></td>
								</tr>

								<tr>
									<td><b> <u> L/C APPLICANT NAME & ADDRESS:</u></b></td>
									<td><b><u>L/C ISSUING BANK & ADDRESS:</u></b></td>
								</tr>
								<tr>
									<td><?=$buyer_name_arr[$buyer_id]["BUYER_NAME"]."<br>".$buyer_name_arr[$buyer_id]["ADDRESS_1"]?></td>
									<td><?=$bank_name_arr[$issuing_bank_name]["BANK_NAME"]."</b><br>".$bank_name_arr[$issuing_bank_name]["ADDRESS"]?></td>
								</tr>					
						</table>
					</td>
				</tr>
				<tr>
					<td colspan="6" valign="top" align="left"><b>NAME & ADDRESS OF NOTIFAY PARTY:</b> <br><b> <?=$buyer_name_arr[$notifying_party]["BUYER_NAME"]."</b><br>".$buyer_name_arr[$notifying_party]["ADDRESS_1"]?> </td>
					<td rowspan="2" style="vertical-align: top;"><b>GOODS SUPPLIED AGINEST FOLLOWING DOCUMENTS:</b> <br><b>BTB LC No:</b> <?=$btb_system_id."  DATE: ".$lc_date."<br>" ?><b>LCAF NO:</b> <?=$lcaf_no?> <br></b>H.S.Code No:</b> <?=$hs_code?> <br> Vat/Bin Reg No: <?=$vat_bin_reg_no?><br><b>TIN No:</b> <?=$tin_number?> <br>  <b>Issueing Bank Bin No:</b> <?=$bin_no?> <br>Bond License No: <?=" ";?> <br> <b> IMPORT AGAINST EXPORT SALES CONTRACT NO:</b> <?=$lc_sc_no;?> <br><b> PROFORMA INVOICE NO:</b><?=rtrim($pi_number,", ") ?> </td>
				</tr>
				<tr>
					<td colspan="6">  
						<table> 
							<tr> 
								<td> <b><u> DRAFT AT / TENOR </u></b></td>
								<td><b><u> TRADE TERMS </u> </b></td>
							</tr>
							<tr> 
								<td><?=$tenor?> DAYS FROM THE DATE OF DELIVERY </td>
								<td> 'EX-WORKS, BENEFICIARY'S FACTORY </td>
							</tr>
							<tr> 
								<td width="250"><b><u>PLACE OF LOADING</u></b></td>
								<td width="250"><b><u>FINAL DESTINATION</u></b></td>
							</tr>
							<tr> 
								<td><?=$port_of_loading?> </td>
								<td> APPLICANT FACTORY </td>
							</tr>
						</table>
					</td>
				</tr>
				<tr>
					<td colspan="6"> 
						<table> 
							<tr> 
								<td  width="250"> <b><u> CARRIER </u></b></td>
								<td  width="250"><b><u> ORIGIN </u> </b></td>
							</tr>
							<tr> 
								<td><?=$shipment_mode[$shipping_mode]?> </td>
								<td> BANGLADESH </td>
							</tr>
						</table>
					</td>
					<td colspan="6"> 
						<table> 
							<tr> 
								<td width="250"> <b><u> FREIGHT </u></b></td>
								<td ><b><u> INCONTERMS </u> </b></td>
							</tr>
							<tr> 
								<td>COLLECT </td>
								<td> 2020 </td>
							</tr>
						</table>
					</td>
				</tr>
			</table>
			<br> <br>
			<?
			$order_id=$sql_del[0]["ID"];
			$sql_rec=sql_select("SELECT a.JOB_NO_PREFIX_NUM,  b.BUYER_STYLE_REF, b.BUYER_BUYER, b.BUYER_PO_NO, c.DESCRIPTION, c.SIZE_ID, c.PLY, c.QNTY, c.id as BREAK_ID, a.ID as MST_ID, b.id as DTLS_ID, e.length as LENGTH, e.width as WIDTH, e.height as HEIGHT, e.flap as FLAP, e.gusset as GUSSET, e.thickness as TICKNESS 
			from SUBCON_ORD_MST a, SUBCON_ORD_DTLS b, SUBCON_ORD_BREAKDOWN c left join subcon_ord_breakdown_size_info e on e.subconordbreakdownid  = c.id where a.id=b.mst_id and b.id=c.mst_id and a.id=$order_id  and a.STATUS_ACTIVE=1 and b.STATUS_ACTIVE=1 and b.STATUS_ACTIVE=1 order by c.id");

			foreach($sql_rec as $row){
				$key=$row['DESCRIPTION'].'**'.$row['BUYER_BUYER'].'**'.$row['BUYER_STYLE_REF'] ;

				$data_order_arr[$row["JOB_NO_PREFIX_NUM"]][$key][$row["BREAK_ID"]]["JOB_NO_PREFIX_NUM"]=$row["JOB_NO_PREFIX_NUM"];
				$data_order_arr[$row["JOB_NO_PREFIX_NUM"]][$key][$row["BREAK_ID"]]["BUYER_STYLE_REF"]=$row["BUYER_STYLE_REF"];
				$data_order_arr[$row["JOB_NO_PREFIX_NUM"]][$key][$row["BREAK_ID"]]["BUYER_BUYER"]=$row["BUYER_BUYER"];
				$data_order_arr[$row["JOB_NO_PREFIX_NUM"]][$key][$row["BREAK_ID"]]["DESCRIPTION"]=$row["DESCRIPTION"];
				$data_order_arr[$row["JOB_NO_PREFIX_NUM"]][$key][$row["BREAK_ID"]]["BUYER_PO_NO"]=$row["BUYER_PO_NO"];
				$data_order_arr[$row["JOB_NO_PREFIX_NUM"]][$key][$row["BREAK_ID"]]["SIZE_ID"]=$row["SIZE_ID"];
				$data_order_arr[$row["JOB_NO_PREFIX_NUM"]][$key][$row["BREAK_ID"]]["PLY"]=$row["PLY"];
				$data_order_arr[$row["JOB_NO_PREFIX_NUM"]][$key][$row["BREAK_ID"]]["DTLS_ID"]=$row["DTLS_ID"];
				$data_order_arr[$row["JOB_NO_PREFIX_NUM"]][$key][$row["BREAK_ID"]]["MST_ID"]=$row["MST_ID"];
				$data_order_arr[$row["JOB_NO_PREFIX_NUM"]][$key][$row["BREAK_ID"]]["LENGTH"]=$row["LENGTH"];
				$data_order_arr[$row["JOB_NO_PREFIX_NUM"]][$key][$row["BREAK_ID"]]["WIDTH"]=$row["WIDTH"];
				$data_order_arr[$row["JOB_NO_PREFIX_NUM"]][$key][$row["BREAK_ID"]]["HEIGHT"]=$row["HEIGHT"];
				$data_order_arr[$row["JOB_NO_PREFIX_NUM"]][$key][$row["BREAK_ID"]]["FLAP"]=$row["FLAP"];
				$data_order_arr[$row["JOB_NO_PREFIX_NUM"]][$key][$row["BREAK_ID"]]["GUSSET"]=$row["GUSSET"];
				$data_order_arr[$row["JOB_NO_PREFIX_NUM"]][$key][$row["BREAK_ID"]]["ORDER_QUANTITY"]+=$row["QNTY"];
			}

			$JobNoRowSpanArr=array();
			$descRowSpanArr=array();
			foreach($data_order_arr as $JonNO=> $JobData)
			{
				$JobNoRowSpan=0;		
				foreach($JobData as $DescData=> $desc_arr)
				{				
					foreach($desc_arr as $BreakIdData=> $row)
					{   
						$JobNoRowSpan++;
						$descRowSpanArr[$JonNO][$DescData]+=1;
					}				
				}
				$JobNoRowSpanArr[$JonNO]=$JobNoRowSpan;
				
			}

			 ?>
			<table border="1" width='1000' cellspacing="0" cellpadding="0">
				<thead>
					<tr>
						<th width="100">ITEM DESCRIPTION</th>
						<th  width="60">PI NO</th>
						<th width="80">BUYER</th>
						<th width="90">Buyer's Style</th>
						<th width="90">PO NO</th>
						<th width="90">JOB NO</th>
						<th width="90">PLY</th>
						<th width="90">L(CM)</th>
						<th width="90">W(CM)</th>
						<th width="90">H(CM)</th>
						<th width="90">F(CM)</th>
						<th width="90">G(CM)</th>
						<th width="90">ORDER QTY</th>
						<th width="90">NET WEIGHT</th>
						<th>GROSS WEIGHT</th>
					</tr>
				</thead>
				<tbody>
					<?$i=1;
					foreach($data_order_arr as $job_no=> $job_arr)
					{
						$rowspan=0;
						foreach($job_arr as $desc=>$desc_arr)
						{ 
							$descrowspan=0;
							foreach($desc_arr as $row)
							{   					
								?>
								<tr bgcolor="<?= $bgcolor; ?>" onClick="change_color('tr_<?= $i; ?>','<?= $bgcolor; ?>')" id="tr_<?= $i; ?>">
									<?php if ($descrowspan == 0) : ?>
										<td align="center" rowspan="<?= $descRowSpanArr[$job_no][$desc] ?>"><?= $row["DESCRIPTION"] ?></td>
									<?php endif; ?>

									<?php if ($rowspan == 0) : ?>
									<td align="center" rowspan="<?= $JobNoRowSpanArr[$job_no] ?>"><?=$exportPiArr[$row["MST_ID"]]["PI_NUMBER"] ?></td>
									<?php endif; ?>

									<?php if ($descrowspan == 0) : ?>
										<td align="center" rowspan="<?= $descRowSpanArr[$job_no][$desc] ?>"><?= $row["BUYER_BUYER"] ?></td>
										<td align="center" rowspan="<?= $descRowSpanArr[$job_no][$desc] ?>"><?= $row["BUYER_STYLE_REF"] ?></td>
										<td align="center" rowspan="<?= $descRowSpanArr[$job_no][$desc] ?>"><?= $row["BUYER_PO_NO"] ?></td>
									<?php endif; ?>

									<?php if ($rowspan == 0) : ?>
										<td align="center" rowspan="<?= $JobNoRowSpanArr[$job_no] ?>"><?= $row["JOB_NO_PREFIX_NUM"] ?></td>
									<?php endif; ?>

									<td align="center"><?= $row["PLY"] ?></td>
									<td align="center"><?= $row["LENGTH"] ?></td>
									<td align="center"><?= $row["WIDTH"] ?></td>
									<td align="center"><?= $row["HEIGHT"] ?></td>
									<td align="center"><?= $row["FLAP"] ?></td>
									<td align="center"><?= $row["GUSSET"] ?></td>
									<td align="center"><?= $row["ORDER_QUANTITY"] ?></td>

									<?php if ($rowspan == 0) : ?>
										<td align="center" rowspan="<?= $JobNoRowSpanArr[$job_no] ?>"><?= $po_invoice_data_array[$row['DTLS_ID']]['CARTON_NET_WEIGHT'] ?></td>
										<td align="center" rowspan="<?= $JobNoRowSpanArr[$job_no] ?>"><?= $po_invoice_data_array[$row['DTLS_ID']]['CARTON_GROSS_WEIGHT'] ?></td>
									<?php endif; ?>
								</tr>							
								<?
								$order_total+=$row["ORDER_QUANTITY"];
								$carton_net_weight=$po_invoice_data_array[$row['DTLS_ID']]['CARTON_NET_WEIGHT'];
								$carton_gross_weight=$po_invoice_data_array[$row['DTLS_ID']]['CARTON_GROSS_WEIGHT'];
								$i++;
								$rowspan++;	
								$descrowspan++;
							}
						}
					}
					?>
				</tbody>
				<tfoot>
					<tr>
						<th colspan="12" align="right" > Total</th>
						<th><?=$order_total?></th>
						<th><?=$carton_net_weight?></th>
						<th><?=$carton_gross_weight?></th>
					</tr>
					<tr>
						<th colspan="15" align="left">AMOUNT IN WORDS: <?=number_to_words($order_total)?></th>
					</tr>
				</tfoot>
			</table>
			<br> <br>
			<table width='1000'>
				<tbody>
					<tr> <td colspan="15">THIS IS TO CERTIFY THAT THE MERCHANDISE ARE DEPZ, BANGLADESH ORIGIN AND THE QUALITY, QUANTITY & UNIT PRICE IS AS PER THIS CREDIT & ARE STRICTLY IN ACCORDANCE WITH THE MENTIONED PROFORMA INVOICE NO: <?=rtrim($pi_number,", ") ?></td></tr>
				</tbody>
			</table>
			<?
			echo $break;	
		}
	}
	
	if (in_array(4, $print_type)) {
		$print_qty=$selected_seq_id_arr[$print_type_arr[4]];
		for($ti=0;$print_qty>$ti; $ti++){	  
			 ?>	
			<table border="1" width='1000' cellspacing="0" cellpadding="0">
				<tr>
					<td colspan="12" valign="top" align="center" style="font-size: 30px;" ><b>  PACKING LIST </b></td>
				</tr>
				<tr>
					<td colspan="6" valign="top" align="left"><b>NAME & ADDRESS OF SHIPPER / EXPORTE /BENEFICERY:</b><br>
					<b> A.  <?=$company_arr[$benificiary_id]."</b><br>".$address."<br>BIN NO: ".$bin_no?>
					<br> <br>
					<b> B.  <?=$bank_name_arr[$lien_bank]["BANK_NAME"]."</b><br>".$bank_name_arr[$lien_bank]["ADDRESS"]."<br> A/C No: ".$bank_acc_arr[$lien_bank][20]["ACCOUNT_NO"]."<br>Swift Code:".$bank_name_arr[$lien_bank]["SWIFT_CODE"];?>
				
					</td>
					<td  width="500" colspan="6" valign="top" align="left">
						<table > 
								<tr>
									<td width="250"><b> <u>INVOICE NO:</u></b></td>
									<td><b><u>INVOICE DATE:</u></b></td>
								</tr>
								<tr>
									<td><?=$invoice_no?></td>
									<td><?=$invoice_date?></td>
								</tr>
								<tr>
									<td><b></u> EXP NO:<u></b></td>
									<td><b></u>EXP DATE:<u></b></td>
								</tr>
								<tr>
									<td><? echo $exp_form_no;?> </td>
									<td><?=$exp_form_date?></td>
								</tr>

								<tr>
									<td><b> <u> L/C APPLICANT NAME & ADDRESS:</u></b></td>
									<td><b><u>L/C ISSUING BANK & ADDRESS:</u></b></td>
								</tr>
								<tr>
									<td><?=$buyer_name_arr[$buyer_id]["BUYER_NAME"]."<br>".$buyer_name_arr[$buyer_id]["ADDRESS_1"]?></td>
									<td><?=$bank_name_arr[$issuing_bank_name]["BANK_NAME"]."</b><br>".$bank_name_arr[$issuing_bank_name]["ADDRESS"]?></td>
								</tr>					
						</table>
					</td>
				</tr>
				<tr>
					<td colspan="6" valign="top" align="left"><b>NAME & ADDRESS OF NOTIFAY PARTY:</b> <br><b> <?=$buyer_name_arr[$notifying_party]["BUYER_NAME"]."</b><br>".$buyer_name_arr[$notifying_party]["ADDRESS_1"]?> </td>
					<td rowspan="2" style="vertical-align: top;"><b>GOODS SUPPLIED AGINEST FOLLOWING DOCUMENTS:</b> <br><b>BTB LC No:</b> <?=$btb_system_id."  DATE: ".$lc_date."<br>" ?><b>LCAF NO:</b> <?=$lcaf_no?> <br></b>H.S.Code No:</b> <?=$hs_code?> <br> Vat/Bin Reg No: <?=$vat_bin_reg_no?><br><b>TIN No:</b> <?=$tin_number?> <br>  <b>Issueing Bank Bin No:</b> <?=$bin_no?> <br>Bond License No: <?=" ";?> <br> <b> IMPORT AGAINST EXPORT SALES CONTRACT NO:</b> <?=$lc_sc_no;?> <br><b> PROFORMA INVOICE NO:</b><?=rtrim($pi_number,", ") ?> </td>
				</tr>
				<tr>
					<td colspan="6">  
						<table> 
							<tr> 
								<td> <b><u> DRAFT AT / TENOR </u></b></td>
								<td><b><u> TRADE TERMS </u> </b></td>
							</tr>
							<tr> 
								<td><?=$tenor?> DAYS FROM THE DATE OF DELIVERY </td>
								<td> 'EX-WORKS, BENEFICIARY'S FACTORY </td>
							</tr>
							<tr> 
								<td width="250"><b><u>PLACE OF LOADING</u></b></td>
								<td width="250"><b><u>FINAL DESTINATION</u></b></td>
							</tr>
							<tr> 
								<td><?=$port_of_loading?> </td>
								<td> APPLICANT FACTORY </td>
							</tr>
						</table>
					</td>
				</tr>
				<tr>
					<td colspan="6"> 
						<table> 
							<tr> 
								<td  width="250"> <b><u> CARRIER </u></b></td>
								<td  width="250"><b><u> ORIGIN </u> </b></td>
							</tr>
							<tr> 
								<td><?=$shipment_mode[$shipping_mode]?> </td>
								<td> BANGLADESH </td>
							</tr>
						</table>
					</td>
					<td colspan="6"> 
						<table> 
							<tr> 
								<td width="250"> <b><u> FREIGHT </u></b></td>
								<td ><b><u> INCONTERMS </u> </b></td>
							</tr>
							<tr> 
								<td>COLLECT </td>
								<td> 2020 </td>
							</tr>
						</table>
					</td>
				</tr>
			</table>
			<br><br>
			<?
			$order_id=$sql_del[0]["ID"];
			$sql_rec=sql_select("SELECT a.JOB_NO_PREFIX_NUM,  b.BUYER_STYLE_REF, b.BUYER_BUYER, b.BUYER_PO_NO, c.DESCRIPTION, c.SIZE_ID, c.PLY, c.QNTY, c.id as BREAK_ID, a.ID as MST_ID, b.id as DTLS_ID, e.length as LENGTH, e.width as WIDTH, e.height as HEIGHT, e.flap as FLAP, e.gusset as GUSSET, e.thickness as TICKNESS 
			from SUBCON_ORD_MST a, SUBCON_ORD_DTLS b, SUBCON_ORD_BREAKDOWN c left join subcon_ord_breakdown_size_info e on e.subconordbreakdownid  = c.id where a.id=b.mst_id and b.id=c.mst_id and a.id=$order_id  and a.STATUS_ACTIVE=1 and b.STATUS_ACTIVE=1 and b.STATUS_ACTIVE=1 order by c.id");

			foreach($sql_rec as $row){
				$key=$row['DESCRIPTION'].'**'.$row['BUYER_BUYER'].'**'.$row['BUYER_STYLE_REF'] ;

				$data_order_arr[$row["JOB_NO_PREFIX_NUM"]][$key][$row["BREAK_ID"]]["JOB_NO_PREFIX_NUM"]=$row["JOB_NO_PREFIX_NUM"];
				$data_order_arr[$row["JOB_NO_PREFIX_NUM"]][$key][$row["BREAK_ID"]]["BUYER_STYLE_REF"]=$row["BUYER_STYLE_REF"];
				$data_order_arr[$row["JOB_NO_PREFIX_NUM"]][$key][$row["BREAK_ID"]]["BUYER_BUYER"]=$row["BUYER_BUYER"];
				$data_order_arr[$row["JOB_NO_PREFIX_NUM"]][$key][$row["BREAK_ID"]]["DESCRIPTION"]=$row["DESCRIPTION"];
				$data_order_arr[$row["JOB_NO_PREFIX_NUM"]][$key][$row["BREAK_ID"]]["BUYER_PO_NO"]=$row["BUYER_PO_NO"];
				$data_order_arr[$row["JOB_NO_PREFIX_NUM"]][$key][$row["BREAK_ID"]]["SIZE_ID"]=$row["SIZE_ID"];
				$data_order_arr[$row["JOB_NO_PREFIX_NUM"]][$key][$row["BREAK_ID"]]["PLY"]=$row["PLY"];
				$data_order_arr[$row["JOB_NO_PREFIX_NUM"]][$key][$row["BREAK_ID"]]["DTLS_ID"]=$row["DTLS_ID"];
				$data_order_arr[$row["JOB_NO_PREFIX_NUM"]][$key][$row["BREAK_ID"]]["MST_ID"]=$row["MST_ID"];
				$data_order_arr[$row["JOB_NO_PREFIX_NUM"]][$key][$row["BREAK_ID"]]["LENGTH"]=$row["LENGTH"];
				$data_order_arr[$row["JOB_NO_PREFIX_NUM"]][$key][$row["BREAK_ID"]]["WIDTH"]=$row["WIDTH"];
				$data_order_arr[$row["JOB_NO_PREFIX_NUM"]][$key][$row["BREAK_ID"]]["HEIGHT"]=$row["HEIGHT"];
				$data_order_arr[$row["JOB_NO_PREFIX_NUM"]][$key][$row["BREAK_ID"]]["FLAP"]=$row["FLAP"];
				$data_order_arr[$row["JOB_NO_PREFIX_NUM"]][$key][$row["BREAK_ID"]]["GUSSET"]=$row["GUSSET"];
				$data_order_arr[$row["JOB_NO_PREFIX_NUM"]][$key][$row["BREAK_ID"]]["ORDER_QUANTITY"]+=$row["QNTY"];
			}

			$JobNoRowSpanArr=array();
			$descRowSpanArr=array();
			foreach($data_order_arr as $JonNO=> $JobData)
			{
				$JobNoRowSpan=0;		
				foreach($JobData as $DescData=> $desc_arr)
				{				
					foreach($desc_arr as $BreakIdData=> $row)
					{   
						$JobNoRowSpan++;
						$descRowSpanArr[$JonNO][$DescData]+=1;
					}				
				}
				$JobNoRowSpanArr[$JonNO]=$JobNoRowSpan;
				
			}

			// echo "<pre>";
			// print_r($descRowSpanArr);

			?>
			<table border="1" width='1000' cellspacing="0" cellpadding="0">
				<thead>
					<tr>
						<th width="100">ITEM DESCRIPTION</th>
						<th  width="60">PI NO</th>
						<th width="80">BUYER</th>
						<th width="90">Buyer's Style</th>
						<th width="90">PO NO</th>
						<th width="90">JOB NO</th>
						<th width="90">PLY</th>
						<th width="90">L(CM)</th>
						<th width="90">W(CM)</th>
						<th width="90">H(CM)</th>
						<th width="90">F(CM)</th>
						<th width="90">G(CM)</th>
						<th width="90">ORDER QTY</th>
						<th width="90">NET WEIGHT</th>
						<th>GROSS WEIGHT</th>
					</tr>
				</thead>
				<tbody>
					<?$i=1;
					foreach($data_order_arr as $job_no=> $job_arr)
					{
						$rowspan=0;
						foreach($job_arr as $desc=>$desc_arr)
						{ 
							$descrowspan=0;
							foreach($desc_arr as $row)
							{   					
								?>
								<tr bgcolor="<?= $bgcolor; ?>" onClick="change_color('tr_<?= $i; ?>','<?= $bgcolor; ?>')" id="tr_<?= $i; ?>">
									<?php if ($descrowspan == 0) : ?>
										<td align="center" rowspan="<?= $descRowSpanArr[$job_no][$desc] ?>"><?= $row["DESCRIPTION"] ?></td>
									<?php endif; ?>

									<?php if ($rowspan == 0) : ?>
									<td align="center" rowspan="<?= $JobNoRowSpanArr[$job_no] ?>"><?=$exportPiArr[$row["MST_ID"]]["PI_NUMBER"] ?></td>
									<?php endif; ?>

									<?php if ($descrowspan == 0) : ?>
										<td align="center" rowspan="<?= $descRowSpanArr[$job_no][$desc] ?>"><?= $row["BUYER_BUYER"] ?></td>
										<td align="center" rowspan="<?= $descRowSpanArr[$job_no][$desc] ?>"><?= $row["BUYER_STYLE_REF"] ?></td>
										<td align="center" rowspan="<?= $descRowSpanArr[$job_no][$desc] ?>"><?= $row["BUYER_PO_NO"] ?></td>
									<?php endif; ?>

									<?php if ($rowspan == 0) : ?>
										<td align="center" rowspan="<?= $JobNoRowSpanArr[$job_no] ?>"><?= $row["JOB_NO_PREFIX_NUM"] ?></td>
									<?php endif; ?>

									<td align="center"><?= $row["PLY"] ?></td>
									<td align="center"><?= $row["LENGTH"] ?></td>
									<td align="center"><?= $row["WIDTH"] ?></td>
									<td align="center"><?= $row["HEIGHT"] ?></td>
									<td align="center"><?= $row["FLAP"] ?></td>
									<td align="center"><?= $row["GUSSET"] ?></td>
									<td align="center"><?= $row["ORDER_QUANTITY"] ?></td>

									<?php if ($rowspan == 0) : ?>
										<td align="center" rowspan="<?= $JobNoRowSpanArr[$job_no] ?>"><?= $po_invoice_data_array[$row['DTLS_ID']]['CARTON_NET_WEIGHT'] ?></td>
										<td align="center" rowspan="<?= $JobNoRowSpanArr[$job_no] ?>"><?= $po_invoice_data_array[$row['DTLS_ID']]['CARTON_GROSS_WEIGHT'] ?></td>
									<?php endif; ?>
								</tr>							
								<?
								$order_total+=$row["ORDER_QUANTITY"];
								$carton_net_weight=$po_invoice_data_array[$row['DTLS_ID']]['CARTON_NET_WEIGHT'];
								$carton_gross_weight=$po_invoice_data_array[$row['DTLS_ID']]['CARTON_GROSS_WEIGHT'];
								$i++;
								$rowspan++;	
								$descrowspan++;
							}
						}
					}
					?>
				</tbody>
				<tfoot>
					<tr>
						<th colspan="12" align="right" > Total</th>
						<th><?=$order_total?></th>
						<th><?=$carton_net_weight?></th>
						<th><?=$carton_gross_weight?></th>
					</tr>
				</tfoot>
			</table>
			<?
			echo $break;
		}
	}

	if (in_array(5, $print_type)) {
		$print_qty=$selected_seq_id_arr[$print_type_arr[5]];
		for($ti=0;$print_qty>$ti; $ti++){	  
			?>	
			<table border="1" width='1000' cellspacing="0" cellpadding="0">
				<tr>
					<td colspan="12" valign="top" align="center" style="font-size: 30px;" ><b>DELIVERY CHALLAN / TRUCK RECEIPT </b></td>
				</tr>
				<tr>
					<td colspan="6" valign="top" align="left"><b>NAME & ADDRESS OF SHIPPER / EXPORTE /BENEFICERY:</b><br>
					<b> A.  <?=$company_arr[$benificiary_id]."</b><br>".$address."<br>BIN NO: ".$bin_no?>
					<br> <br>
					<b> B.  <?=$bank_name_arr[$lien_bank]["BANK_NAME"]."</b><br>".$bank_name_arr[$lien_bank]["ADDRESS"]."<br> A/C No: ".$bank_acc_arr[$lien_bank][20]["ACCOUNT_NO"]."<br>Swift Code:".$bank_name_arr[$lien_bank]["SWIFT_CODE"];?>
				
					</td>
					<td  width="500" colspan="6" valign="top" align="left">
						<table > 
								<tr>
									<td width="250"><b> <u>INVOICE NO:</u></b></td>
									<td><b><u>INVOICE DATE:</u></b></td>
								</tr>
								<tr>
									<td><?=$invoice_no?></td>
									<td><?=$invoice_date?></td>
								</tr>
								<tr>
									<td><b></u> EXP NO:<u></b></td>
									<td><b></u>EXP DATE:<u></b></td>
								</tr>
								<tr>
									<td><? echo $exp_form_no;?> </td>
									<td><?=$exp_form_date?></td>
								</tr>

								<tr>
									<td><b> <u> L/C APPLICANT NAME & ADDRESS:</u></b></td>
									<td><b><u>L/C ISSUING BANK & ADDRESS:</u></b></td>
								</tr>
								<tr>
									<td><?=$buyer_name_arr[$buyer_id]["BUYER_NAME"]."<br>".$buyer_name_arr[$buyer_id]["ADDRESS_1"]?></td>
									<td><?=$bank_name_arr[$issuing_bank_name]["BANK_NAME"]."</b><br>".$bank_name_arr[$issuing_bank_name]["ADDRESS"]?></td>
								</tr>					
						</table>
					</td>
				</tr>
				<tr>
					<td colspan="6" valign="top" align="left"><b>NAME & ADDRESS OF NOTIFAY PARTY:</b> <br><b> <?=$buyer_name_arr[$notifying_party]["BUYER_NAME"]."</b><br>".$buyer_name_arr[$notifying_party]["ADDRESS_1"]?> </td>
					<td rowspan="2" style="vertical-align: top;"><b>GOODS SUPPLIED AGINEST FOLLOWING DOCUMENTS:</b> <br><b>BTB LC No:</b> <?=$btb_system_id."  DATE: ".$lc_date."<br>" ?><b>LCAF NO:</b> <?=$lcaf_no?> <br></b>H.S.Code No:</b> <?=$hs_code?> <br> Vat/Bin Reg No: <?=$vat_bin_reg_no?><br><b>TIN No:</b> <?=$tin_number?> <br>  <b>Issueing Bank Bin No:</b> <?=$bin_no?> <br>Bond License No: <?=" ";?> <br> <b> IMPORT AGAINST EXPORT SALES CONTRACT NO:</b> <?=$lc_sc_no;?><br><b> PROFORMA INVOICE NO:</b><?=rtrim($pi_number,", ") ?> </td>
				</tr>
				<tr>
					<td colspan="6">  
						<table> 
							<tr> 
								<td> <b><u> DRAFT AT / TENOR </u></b></td>
								<td><b><u> TRADE TERMS </u> </b></td>
							</tr>
							<tr> 
								<td><?=$tenor?> DAYS FROM THE DATE OF DELIVERY </td>
								<td> 'EX-WORKS, BENEFICIARY'S FACTORY </td>
							</tr>
							<tr> 
								<td width="250"><b><u>PLACE OF LOADING</u></b></td>
								<td width="250"><b><u>FINAL DESTINATION</u></b></td>
							</tr>
							<tr> 
								<td><?=$port_of_loading?> </td>
								<td> APPLICANT FACTORY </td>
							</tr>
						</table>
					</td>
				</tr>
				<tr>
					<td colspan="6"> 
						<table> 
							<tr> 
								<td  width="250"> <b><u> CARRIER </u></b></td>
								<td  width="250"><b><u> ORIGIN </u> </b></td>
							</tr>
							<tr> 
								<td><?=$shipment_mode[$shipping_mode]?> </td>
								<td> BANGLADESH </td>
							</tr>
						</table>
					</td>
					<td colspan="6"> 
						<table> 
							<tr> 
								<td width="250"> <b><u> FREIGHT </u></b></td>
								<td ><b><u> INCONTERMS </u> </b></td>
							</tr>
							<tr> 
								<td>COLLECT </td>
								<td> 2020 </td>
							</tr>
						</table>
					</td>
				</tr>
			</table>
			<br> <br>
			<?
			$order_id=$sql_del[0]["ID"];
			$sql_rec=sql_select("SELECT a.JOB_NO_PREFIX_NUM,  b.BUYER_STYLE_REF, b.BUYER_BUYER, b.BUYER_PO_NO, c.DESCRIPTION, c.SIZE_ID, c.PLY, c.QNTY, c.id as BREAK_ID, a.ID as MST_ID, b.id as DTLS_ID, e.length as LENGTH, e.width as WIDTH, e.height as HEIGHT, e.flap as FLAP, e.gusset as GUSSET, e.thickness as TICKNESS 
			from SUBCON_ORD_MST a, SUBCON_ORD_DTLS b, SUBCON_ORD_BREAKDOWN c left join subcon_ord_breakdown_size_info e on e.subconordbreakdownid  = c.id where a.id=b.mst_id and b.id=c.mst_id and a.id=$order_id  and a.STATUS_ACTIVE=1 and b.STATUS_ACTIVE=1 and b.STATUS_ACTIVE=1 order by c.id");

			foreach($sql_rec as $row){
				$key=$row['DESCRIPTION'].'**'.$row['BUYER_BUYER'].'**'.$row['BUYER_STYLE_REF'] ;

				$data_order_arr[$row["JOB_NO_PREFIX_NUM"]][$key][$row["BREAK_ID"]]["JOB_NO_PREFIX_NUM"]=$row["JOB_NO_PREFIX_NUM"];
				$data_order_arr[$row["JOB_NO_PREFIX_NUM"]][$key][$row["BREAK_ID"]]["BUYER_STYLE_REF"]=$row["BUYER_STYLE_REF"];
				$data_order_arr[$row["JOB_NO_PREFIX_NUM"]][$key][$row["BREAK_ID"]]["BUYER_BUYER"]=$row["BUYER_BUYER"];
				$data_order_arr[$row["JOB_NO_PREFIX_NUM"]][$key][$row["BREAK_ID"]]["DESCRIPTION"]=$row["DESCRIPTION"];
				$data_order_arr[$row["JOB_NO_PREFIX_NUM"]][$key][$row["BREAK_ID"]]["BUYER_PO_NO"]=$row["BUYER_PO_NO"];
				$data_order_arr[$row["JOB_NO_PREFIX_NUM"]][$key][$row["BREAK_ID"]]["SIZE_ID"]=$row["SIZE_ID"];
				$data_order_arr[$row["JOB_NO_PREFIX_NUM"]][$key][$row["BREAK_ID"]]["PLY"]=$row["PLY"];
				$data_order_arr[$row["JOB_NO_PREFIX_NUM"]][$key][$row["BREAK_ID"]]["DTLS_ID"]=$row["DTLS_ID"];
				$data_order_arr[$row["JOB_NO_PREFIX_NUM"]][$key][$row["BREAK_ID"]]["MST_ID"]=$row["MST_ID"];
				$data_order_arr[$row["JOB_NO_PREFIX_NUM"]][$key][$row["BREAK_ID"]]["LENGTH"]=$row["LENGTH"];
				$data_order_arr[$row["JOB_NO_PREFIX_NUM"]][$key][$row["BREAK_ID"]]["WIDTH"]=$row["WIDTH"];
				$data_order_arr[$row["JOB_NO_PREFIX_NUM"]][$key][$row["BREAK_ID"]]["HEIGHT"]=$row["HEIGHT"];
				$data_order_arr[$row["JOB_NO_PREFIX_NUM"]][$key][$row["BREAK_ID"]]["FLAP"]=$row["FLAP"];
				$data_order_arr[$row["JOB_NO_PREFIX_NUM"]][$key][$row["BREAK_ID"]]["GUSSET"]=$row["GUSSET"];
				$data_order_arr[$row["JOB_NO_PREFIX_NUM"]][$key][$row["BREAK_ID"]]["ORDER_QUANTITY"]+=$row["QNTY"];
			}

			$JobNoRowSpanArr=array();
			$descRowSpanArr=array();
			foreach($data_order_arr as $JonNO=> $JobData)
			{
				$JobNoRowSpan=0;		
				foreach($JobData as $DescData=> $desc_arr)
				{				
					foreach($desc_arr as $BreakIdData=> $row)
					{   
						$JobNoRowSpan++;
						$descRowSpanArr[$JonNO][$DescData]+=1;
					}				
				}
				$JobNoRowSpanArr[$JonNO]=$JobNoRowSpan;
				
			}

			// echo "<pre>";
			// print_r($descRowSpanArr);

			?>
			<table border="1" width='1000' cellspacing="0" cellpadding="0">
				<thead>
					<tr>
						<th width="100">ITEM DESCRIPTION</th>
						<th  width="60">PI NO</th>
						<th width="80">BUYER</th>
						<th width="90">Buyer's Style</th>
						<th width="90">PO NO</th>
						<th width="90">JOB NO</th>
						<th width="90">PLY</th>
						<th width="90">L(CM)</th>
						<th width="90">W(CM)</th>
						<th width="90">H(CM)</th>
						<th width="90">F(CM)</th>
						<th width="90">G(CM)</th>
						<th width="90">ORDER QTY</th>
					</tr>
				</thead>
				<tbody>
					<?$i=1;
					foreach($data_order_arr as $job_no=> $job_arr)
					{
						$rowspan=0;
						foreach($job_arr as $desc=>$desc_arr)
						{ 
							$descrowspan=0;
							foreach($desc_arr as $row)
							{   					
								?>
								<tr bgcolor="<?= $bgcolor; ?>" onClick="change_color('tr_<?= $i; ?>','<?= $bgcolor; ?>')" id="tr_<?= $i; ?>">
									<?php if ($descrowspan == 0) : ?>
										<td align="center" rowspan="<?= $descRowSpanArr[$job_no][$desc] ?>"><?= $row["DESCRIPTION"] ?></td>
									<?php endif; ?>

									<?php if ($rowspan == 0) : ?>
									<td align="center" rowspan="<?= $JobNoRowSpanArr[$job_no] ?>"><?=$exportPiArr[$row["MST_ID"]]["PI_NUMBER"] ?></td>
									<?php endif; ?>

									<?php if ($descrowspan == 0) : ?>
										<td align="center" rowspan="<?= $descRowSpanArr[$job_no][$desc] ?>"><?= $row["BUYER_BUYER"] ?></td>
										<td align="center" rowspan="<?= $descRowSpanArr[$job_no][$desc] ?>"><?= $row["BUYER_STYLE_REF"] ?></td>
										<td align="center" rowspan="<?= $descRowSpanArr[$job_no][$desc] ?>"><?= $row["BUYER_PO_NO"] ?></td>
									<?php endif; ?>

									<?php if ($rowspan == 0) : ?>
										<td align="center" rowspan="<?= $JobNoRowSpanArr[$job_no] ?>"><?= $row["JOB_NO_PREFIX_NUM"] ?></td>
									<?php endif; ?>

									<td align="center"><?= $row["PLY"] ?></td>
									<td align="center"><?= $row["LENGTH"] ?></td>
									<td align="center"><?= $row["WIDTH"] ?></td>
									<td align="center"><?= $row["HEIGHT"] ?></td>
									<td align="center"><?= $row["FLAP"] ?></td>
									<td align="center"><?= $row["GUSSET"] ?></td>
									<td align="center"><?= $row["ORDER_QUANTITY"] ?></td>
								</tr>							
								<?
								$order_total+=$row["ORDER_QUANTITY"];
								$carton_net_weight=$po_invoice_data_array[$row['DTLS_ID']]['CARTON_NET_WEIGHT'];
								$carton_gross_weight=$po_invoice_data_array[$row['DTLS_ID']]['CARTON_GROSS_WEIGHT'];
								$i++;
								$rowspan++;	
								$descrowspan++;
							}
						}
					}
					?>
				</tbody>
				<tfoot>
					<tr>
						<th colspan="12" align="right" > Total</th>
						<th><?=$order_total?></th>
					</tr>
				</tfoot>
			</table>
			
			<br><br><br> <br><br> <br>
			<table width='1000'>
				<tbody>
					<tr> 
						<td colspan="5" width="400" align="center" style="border-top: 1px solid black;" > FOR, PAKIZA KNIT COMPOSITE LIMITED. <br> RECEIPT THE GOODS IN GOOD CONDITIONS AS PER PROFORMA <br> INVOICE & TERMS OF THE L/C</td>
						<td colspan="2"></td>
						<td colspan="5"  align="center" style="border-top: 1px solid black;vertical-align: text-top" > FOR EXPERIENCE ACCESSORIES CO LTD</td>
					</tr>
				</tbody>
			</table>
			<?
			echo $break;
		}
	}
	
	if (in_array(6, $print_type)) {
		$print_qty=$selected_seq_id_arr[$print_type_arr[6]];
		for($ti=0;$print_qty>$ti; $ti++){	  
			?>
			<table width='1000' cellspacing="0" cellpadding="0"  >
				<tr>
					<td colspan="12" valign="top" align="center" style="font-size: 30px;" > <b> <u>CERTIFICATE OF ORIGIN</u></b></td>
				</tr>
				<tr style="height: 40px;"></tr>
				<tr>
					<td colspan="4" valign="top" align="left">GOODS CONSIGNED FROM</td>
					<td valign="top" >:</td>
					<td colspan="4" valign="top" > <b><? echo $company_arr[$benificiary_id]."</b><br>".$address?>"</td>
					<td colspan="4" valign="top" align="left">DATE:05.07.2022</td>
				</tr>
				<tr style="height: 15px;"></tr>
				<tr>
					<td colspan="4" valign="top" align="left">GOODS CONSIGNED TO </td>
					<td valign="top" >:</td>
					<td colspan="4" valign="top" ><?=$buyer_name_arr[$buyer_id]["BUYER_NAME"]."<br>".$buyer_name_arr[$buyer_id]["ADDRESS_1"]?></td>
				</tr>
				<tr style="height: 15px;"></tr>
				<tr>
				<td colspan="4" valign="top" align="left">SHIPMENT FROM</td>
				<td>:</td>
				<td>DEPZ, SAVAR, DHAKA, BANGLADESH</td>
				</tr>
				<tr style="height: 10px;"></tr>
				<tr>
					<td colspan="4" valign="top" align="left">DESTINATION	</td>
					<td>:</td>
					<td colspan="4" valign="top" >APPLICANT FACTORY</td>
				</tr>
				<tr>
					<td colspan="4" valign="top" align="left">INVOICE NO.</td>
					<td>:</td>
					<td colspan="4" valign="top" align="left"><?=$invoice_no?></td>  
					<td colspan="" valign="top" align="left">DATE:<?=change_date_format($invoice_date)?></td>
				</tr>
				<tr>
					<td colspan="4" valign="top" align="left">DELIVERY CHALLAN NO.</td>
					<td>:</td>
					<td colspan="4" valign="top" align="left"><?=$sql_del[0]["TRIMS_DEL"];?></td>  
					<td colspan="" valign="top" align="left">DATE: <?= change_date_format($sql_del[0]["DELIVERY_DATE"]);?></td>
				</tr>
				<tr>
					<td colspan="4" valign="top" align="left">BTB L/C NO.</td>
					<td>:</td>
					<td colspan="4" valign="top" align="left"><?=$lc_sc_no?></td>  
					<td colspan="" valign="top" align="left">  DATE:<?=$lc_sc_date?></td>
				</tr>
				<tr>
					<td colspan="4" valign="top" align="left">AMENDMENT NO</td>
					<td>:</td>
					<td colspan="4" valign="top" align="left"><?=$sql_del[0]["ORDER_NO"]?></td>  
					<td colspan="" valign="top" align="left"> DATE:	NA</td>
				</tr>
				<tr>
					<td colspan="4" valign="top" align="left">EXP NO.</td>
					<td>:</td>
					<td colspan="4" valign="top" align="left"><?echo $exp_form_no?></td>  
					<td colspan="" valign="top" align="left">  DATE:	<?echo change_date_format($exp_form_date)?></td>
				</tr>
				<tr style="height: 15px;"></tr>
				<tr>
					<td colspan="4" valign="top" align="left">VALUE</td>
					<td>:</td>
					<td colspan="" valign="top" align="left"><? echo "$".$net_invo_value?></td>  
				</tr>
				<tr>
					<td colspan="4" valign="top" align="left">&nbsp;</td>
					<td>&nbsp;</td>
					<td colspan="" valign="top" align="left"><?echo number_to_words($net_invo_value)?></td>
				</tr>
				<tr style="height: 10px;"></tr>
				<tr>
					<td  colspan="4" valign="top" align="left">FOR EXPORT OF</td>
					<td>:</td>
					<td  colspan="" valign="top" align="left">CARTON & TOP BOTTOM	</td>
				</tr>
				<tr>
					<td  colspan="4" valign="top" align="left">QUANTITY</td>
					<td>:</td>
					<td  colspan="4" valign="top" align="left">1862</td>
					<td  colspan="4" valign="top" align="left">PCS</td>
				</tr>
				<tr>
				<td  colspan="4" valign="top" align="left">TRADE TERMS	</td>
				<td>:</td>
					<td  colspan="4" valign="top" align="left">EX-WORKS, BENEFICIARY'S FACTORY</td>
				</tr>
				<tr>
				<td colspan="4" valign="top" align="left">FREIGHT	</td>
				<td>:</td>
				<td  colspan="4" valign="top" align="left">COLLECT</td>
				</tr>
				<tr>
				<td colspan="4" valign="top" align="left">EXPORT LC/SC NO	</td>
				<td>:</td>
				<td  colspan="4" valign="top" align="left"><b> <?=$lc_sc_no?> DATE:<?=$lc_sc_date?></b></td>
				</tr>
				<tr style="height: 30px;"></tr>
				<tr>
					<td colspan="12" valign="top"   style="text-decoration: underline;"><b>DECLERATION BY THE EXPORTER</b>	</td>
				</tr>
				<tr style="height: 30px;"></tr>
				<tr>
					<td  colspan="12" valign="top" >THE UNDERSIGNED HEREBY DECLARES THAT THE ABOVE DETAILS AND STATEMENTS ARE CORRECT AND ALL THE GOODS WERE BANGLADESHI ORIGIN .</td>
				</tr>
				<tr style="height: 30px;"></tr>
				<tr>
					<td  colspan="12" valign="top" >For & On Behalf Of </td>
				</tr>
			</table>
			<?
			echo $break;
		}
	}
	
	if (in_array(7, $print_type)) {
		$print_qty=$selected_seq_id_arr[$print_type_arr[7]];
		$count=1;
		for($ti=0;$print_qty>$ti; $ti++){	  
			?>
			<table width='1000' cellspacing="0" cellpadding="0"  >
				<tr>
					<td colspan="11" valign="top" align="center" style="font-size: 30px;" > <b> <u><i> Bill  of Exchange</i></u></b></td>
					<td  align="center"  style="font-size: 30px;"><?=$count?></td>
				</tr>
				<tr>
					<td colspan="4" valign="top" align="left"><b>INVOICE NO:</b> <?=$invoice_no?></td>
					<td colspan="4" valign="top" align="center"></td>
					<td colspan="4" valign="top" align="left"><b>DATE:</b><?=$invoice_date?></td>
				</tr>
				<tr>
					<td colspan="4" valign="top" align="left"></b>EXCHANGE FOR :<b></td>
					<td colspan="4" valign="top" align="center"><?=$net_invo_value?></td>
					<td colspan="4" valign="top" align="left"><b>ONLY</b></td>
				</tr>
				<tr>
					<td colspan="12" valign="top" align="left"><b>AMOUNT IN WORDS:</b><?=number_to_words(def_number_format($net_invo_value,2,""),"USD", "CENTS")." ONLY";?></td>
				</tr>
				<tr style="height: 10px;"></tr>
				<tr>
					<td colspan="12" valign="top" align="left"><b><?=$tenor?> DAYS FROM THE DATE OF DELIVERY of this FIRST OF EXCHANGE (SECOND of same tenor and date being unpaid)</b></td>
				</tr>	
				<tr>
					<td colspan="4" valign="top" align="left"><b>PAY TO THE ORDER OF:</b></td>
					<td colspan="8" valign="top" align="left"><?=$bank_name_arr[$lien_bank]["ADDRESS"]?></td>
				</tr>
				<tr style="height: 10px;"></tr>
				<tr>
					<td colspan="12" valign="top" align="left"><b>VALUE RECEIVED AND CHARGE AMOUNT TO ACCOUNT OF:</b></td>
				</tr>
				<tr>
					<td colspan="2"></td>
					<td colspan="10" valign="top" align="left"><?=$buyer_name_arr[$buyer_id]["BUYER_NAME"]."<br>".$buyer_name_arr[$buyer_id]["ADDRESS_1"]?></td>
				</tr>
				<tr style="height: 10px;"></tr>
				<tr>
					<td colspan="2"><b>DRAWN ON:</b></td>
					<td colspan="10" valign="top" align="left"><?=$bank_name_arr[$issuing_bank_name]["BANK_NAME"]."<br>".$bank_name_arr[$issuing_bank_name]["ADDRESS"];?></td>
				</tr>
				<tr style="height: 10px;"></tr>
				<tr>
					<td colspan="2" valign="top" align="left"><b>LETTER OF CREDIT NO:</b> </td>
					<td colspan="6" valign="top" align="left"> <?=$lc_sc_no?></td>
					<td colspan="4" valign="top" align="left"><b>DATE:</b><?=$lc_sc_date?></td>
				</tr>
				<tr style="height: 10px;"></tr>
				<tr>
					<td colspan="2" valign="top" align="left"><b>L/C AMENDMENT NO: </b></td>
					<td colspan="6" valign="top" style="word-break: break-all;" align="left"> <?=rtrim($amendment_no,",");?></td>
					<td colspan="4" valign="top" style="word-break: break-all;" align="left"><?=rtrim(change_date_format($amendment_date),",");?></td>
				</tr>
				<tr style="height: 10px;"></tr>
				<tr>
					<td colspan="2" valign="top" align="left"><b>To. </b></td>
					<td colspan="10" valign="top" align="left"><?=$bank_name_arr[$issuing_bank_name]["BANK_NAME"]."<br>".$bank_name_arr[$issuing_bank_name]["ADDRESS"];?> </td>
				</tr>
				<tr style="height: 10px;"></tr>
				<tr>
					<td colspan="2" valign="top" align="left"><b> A/C. </b></td>
					<td colspan="10" valign="top" align="left"> <?=$buyer_name_arr[$buyer_id]["BUYER_NAME"]."<br>".$buyer_name_arr[$buyer_id]["ADDRESS_1"]?> </td>
				</tr>
			</table>
			<br>
			<?$count++;
			echo $break;
		}
	}

	if (in_array(8, $print_type)) {
		$print_qty=$selected_seq_id_arr[$print_type_arr[8]];
		for($ti=0;$print_qty>$ti; $ti++){	 
			?>	
			<table border="1" width='1000' cellspacing="0" cellpadding="0">
				<tr>
					<td colspan="12" valign="top" align="center" style="font-size: 30px;" ><b> COMMERCIAL INVOICE </b></td>
				</tr>
				<tr>
					<td colspan="6" valign="top" align="left"><b>NAME & ADDRESS OF SHIPPER / EXPORTE /BENEFICERY:</b><br>
					<b> A.  <?=$company_arr[$benificiary_id]."</b><br>".$address."<br>BIN NO: ".$bin_no?>
					<br> <br>
						<b> B.  <?=$bank_name_arr[$lien_bank]["BANK_NAME"]."</b><br>".$bank_name_arr[$lien_bank]["ADDRESS"]."<br> A/C No: ".$bank_acc_arr[$lien_bank][20]["ACCOUNT_NO"]."<br>Swift Code:".$bank_name_arr[$lien_bank]["SWIFT_CODE"];?>
				
					</td>
					<td  width="500" colspan="6" valign="top" align="left">
							<table > 
								<tr>
									<td width="250"><b> <u>INVOICE NO:</u></b></td>
									<td><b><u>INVOICE DATE:</u></b></td>
								</tr>
								<tr>
									<td><?=$invoice_no?></td>
									<td><?=$invoice_date?></td>
								</tr>
								<tr>
									<td><b></u> EXP NO:<u></b></td>
									<td><b></u>EXP DATE:<u></b></td>
								</tr>
								<tr>
									<td><? echo $exp_form_no;?> </td>
									<td><?=$exp_form_date?></td>
								</tr>

								<tr>
									<td><b> <u> L/C APPLICANT NAME & ADDRESS:</u></b></td>
									<td><b><u>L/C ISSUING BANK & ADDRESS:</u></b></td>
								</tr>
								<tr>
									<td><?=$buyer_name_arr[$buyer_id]["BUYER_NAME"]."<br>".$buyer_name_arr[$buyer_id]["ADDRESS_1"]?></td>
									<td><?=$bank_name_arr[$issuing_bank_name]["BANK_NAME"]."</b><br>".$bank_name_arr[$issuing_bank_name]["ADDRESS"]?></td>
								</tr>					
						</table>
					</td>
				</tr>
				<tr>
					<td colspan="6" valign="top" align="left"><b>NAME & ADDRESS OF NOTIFAY PARTY:</b> <br><b> <?=$buyer_name_arr[$notifying_party]["BUYER_NAME"]."</b><br>".$buyer_name_arr[$notifying_party]["ADDRESS_1"]?> </td>
					<td rowspan="2" style="vertical-align: top;"><b>GOODS SUPPLIED AGINEST FOLLOWING DOCUMENTS:</b> <br><b>BTB LC No:</b> <?=$btb_system_id."  DATE: ".$lc_date."<br>" ?><b>LCAF NO:</b> <?=$lcaf_no?> <br></b>H.S.Code No:</b> <?=$hs_code?> <br> Vat/Bin Reg No: <?=$vat_bin_reg_no?><br><b>TIN No:</b> <?=$tin_number?> <br>  <b>Issueing Bank Bin No:</b> <?=$bin_no?> <br>Bond License No: <?=" ";?> <br> <b> IMPORT AGAINST EXPORT SALES CONTRACT NO:</b> <?=$lc_sc_no;?> <br><b> PROFORMA INVOICE NO:</b><?=rtrim($pi_number,", ") ?> </td>
				</tr>
				<tr>
					<td colspan="6">  
						<table> 
							<tr> 
								<td> <b><u> DRAFT AT / TENOR </u></b></td>
								<td><b><u> TRADE TERMS </u> </b></td>
							</tr>
							<tr> 
								<td><?=$tenor?> DAYS FROM THE DATE OF DELIVERY </td>
								<td> 'EX-WORKS, BENEFICIARY'S FACTORY </td>
							</tr>
							<tr> 
								<td width="250"><b><u>PLACE OF LOADING</u></b></td>
								<td width="250"><b><u>FINAL DESTINATION</u></b></td>
							</tr>
							<tr> 
								<td><?=$port_of_loading?> </td>
								<td> APPLICANT FACTORY </td>
							</tr>
						</table>
					</td>
				</tr>
				<tr>
					<td colspan="6"> 
						<table> 
							<tr> 
								<td  width="250"> <b><u> CARRIER </u></b></td>
								<td  width="250"><b><u> ORIGIN </u> </b></td>
							</tr>
							<tr> 
								<td><?=$shipment_mode[$shipping_mode]?> </td>
								<td> BANGLADESH </td>
							</tr>
						</table>
					</td>
					<td colspan="6"> 
						<table> 
							<tr> 
								<td width="250"> <b><u> FREIGHT </u></b></td>
								<td ><b><u> INCONTERMS </u> </b></td>
							</tr>
							<tr> 
								<td>COLLECT </td>
								<td> 2020 </td>
							</tr>
						</table>
					</td>
				</tr>
			</table>
			<br> <br>
			<?
			$order_id=$sql_del[0]["ID"];
			$sql_rec=sql_select("SELECT a.JOB_NO_PREFIX_NUM,  b.BUYER_STYLE_REF, b.BUYER_BUYER, b.BUYER_PO_NO, c.DESCRIPTION, c.SIZE_ID, c.PLY, c.QNTY, c.id as BREAK_ID, a.ID as MST_ID, b.id as DTLS_ID, e.length as LENGTH, e.width as WIDTH, e.height as HEIGHT, e.flap as FLAP, e.gusset as GUSSET, e.thickness as TICKNESS, c.RATE, c.AMOUNT 
			from SUBCON_ORD_MST a, SUBCON_ORD_DTLS b, SUBCON_ORD_BREAKDOWN c left join subcon_ord_breakdown_size_info e on e.subconordbreakdownid  = c.id where a.id=b.mst_id and b.id=c.mst_id and a.id=$order_id  and a.STATUS_ACTIVE=1 and b.STATUS_ACTIVE=1 and b.STATUS_ACTIVE=1 order by c.id");

			foreach($sql_rec as $row){
				$key=$row['DESCRIPTION'].'**'.$row['BUYER_BUYER'].'**'.$row['BUYER_STYLE_REF'] ;

				$data_order_arr[$row["JOB_NO_PREFIX_NUM"]][$key][$row["BREAK_ID"]]["JOB_NO_PREFIX_NUM"]=$row["JOB_NO_PREFIX_NUM"];
				$data_order_arr[$row["JOB_NO_PREFIX_NUM"]][$key][$row["BREAK_ID"]]["BUYER_STYLE_REF"]=$row["BUYER_STYLE_REF"];
				$data_order_arr[$row["JOB_NO_PREFIX_NUM"]][$key][$row["BREAK_ID"]]["BUYER_BUYER"]=$row["BUYER_BUYER"];
				$data_order_arr[$row["JOB_NO_PREFIX_NUM"]][$key][$row["BREAK_ID"]]["DESCRIPTION"]=$row["DESCRIPTION"];
				$data_order_arr[$row["JOB_NO_PREFIX_NUM"]][$key][$row["BREAK_ID"]]["BUYER_PO_NO"]=$row["BUYER_PO_NO"];
				$data_order_arr[$row["JOB_NO_PREFIX_NUM"]][$key][$row["BREAK_ID"]]["SIZE_ID"]=$row["SIZE_ID"];
				$data_order_arr[$row["JOB_NO_PREFIX_NUM"]][$key][$row["BREAK_ID"]]["PLY"]=$row["PLY"];
				$data_order_arr[$row["JOB_NO_PREFIX_NUM"]][$key][$row["BREAK_ID"]]["DTLS_ID"]=$row["DTLS_ID"];
				$data_order_arr[$row["JOB_NO_PREFIX_NUM"]][$key][$row["BREAK_ID"]]["MST_ID"]=$row["MST_ID"];
				$data_order_arr[$row["JOB_NO_PREFIX_NUM"]][$key][$row["BREAK_ID"]]["LENGTH"]=$row["LENGTH"];
				$data_order_arr[$row["JOB_NO_PREFIX_NUM"]][$key][$row["BREAK_ID"]]["WIDTH"]=$row["WIDTH"];
				$data_order_arr[$row["JOB_NO_PREFIX_NUM"]][$key][$row["BREAK_ID"]]["HEIGHT"]=$row["HEIGHT"];
				$data_order_arr[$row["JOB_NO_PREFIX_NUM"]][$key][$row["BREAK_ID"]]["FLAP"]=$row["FLAP"];
				$data_order_arr[$row["JOB_NO_PREFIX_NUM"]][$key][$row["BREAK_ID"]]["GUSSET"]=$row["GUSSET"];
				$data_order_arr[$row["JOB_NO_PREFIX_NUM"]][$key][$row["BREAK_ID"]]["ORDER_QUANTITY"]+=$row["QNTY"];
				$data_order_arr[$row["JOB_NO_PREFIX_NUM"]][$key][$row["BREAK_ID"]]["RATE"]=$row["RATE"];
				$data_order_arr[$row["JOB_NO_PREFIX_NUM"]][$key][$row["BREAK_ID"]]["AMOUNT"]=$row["AMOUNT"];
			}

			$JobNoRowSpanArr=array();
			$descRowSpanArr=array();
			foreach($data_order_arr as $JonNO=> $JobData)
			{
				$JobNoRowSpan=0;		
				foreach($JobData as $DescData=> $desc_arr)
				{				
					foreach($desc_arr as $BreakIdData=> $row)
					{   
						$JobNoRowSpan++;
						$descRowSpanArr[$JonNO][$DescData]+=1;
					}				
				}
				$JobNoRowSpanArr[$JonNO]=$JobNoRowSpan;
				
			}
			// echo "<pre>";
			// print_r($descRowSpanArr);
			?>
			<table border="1" width='1000' cellspacing="0" cellpadding="0">
				<thead>
					<tr>
						<th width="100">ITEM DESCRIPTION</th>
						<th  width="60">PI NO</th>
						<th width="80">BUYER</th>
						<th width="90">Buyer's Style</th>
						<th width="90">PO NO</th>
						<th width="90">JOB NO</th>
						<th width="90">PLY</th>
						<th width="90">L(CM)</th>
						<th width="90">W(CM)</th>
						<th width="90">H(CM)</th>
						<th width="90">F(CM)</th>
						<th width="90">G(CM)</th>
						<th width="90">ORDER QTY</th>
						<th width="90">PRICE/PCS</th>
						<th width="90"> TOTAL AMOUNT</th>
					</tr>
				</thead>
				<tbody>
					<?$i=1;
					foreach($data_order_arr as $job_no=> $job_arr)
					{
						$rowspan=0;
						foreach($job_arr as $desc=>$desc_arr)
						{ 
							$descrowspan=0;
							foreach($desc_arr as $row)
							{   					
								?>
								<tr bgcolor="<?= $bgcolor; ?>" onClick="change_color('tr_<?= $i; ?>','<?= $bgcolor; ?>')" id="tr_<?= $i; ?>">
									<?php if ($descrowspan == 0) : ?>
										<td align="center" rowspan="<?= $descRowSpanArr[$job_no][$desc] ?>"><?= $row["DESCRIPTION"] ?></td>
									<?php endif; ?>

									<?php if ($rowspan == 0) : ?>
									<td align="center" rowspan="<?= $JobNoRowSpanArr[$job_no] ?>"><?=$exportPiArr[$row["MST_ID"]]["PI_NUMBER"] ?></td>
									<?php endif; ?>

									<?php if ($descrowspan == 0) : ?>
										<td align="center" rowspan="<?= $descRowSpanArr[$job_no][$desc] ?>"><?= $row["BUYER_BUYER"] ?></td>
										<td align="center" rowspan="<?= $descRowSpanArr[$job_no][$desc] ?>"><?= $row["BUYER_STYLE_REF"] ?></td>
										<td align="center" rowspan="<?= $descRowSpanArr[$job_no][$desc] ?>"><?= $row["BUYER_PO_NO"] ?></td>
									<?php endif; ?>

									<?php if ($rowspan == 0) : ?>
										<td align="center" rowspan="<?= $JobNoRowSpanArr[$job_no] ?>"><?= $row["JOB_NO_PREFIX_NUM"] ?></td>
									<?php endif; ?>

									<td align="center"><?= $row["PLY"] ?></td>
									<td align="center"><?= $row["LENGTH"] ?></td>
									<td align="center"><?= $row["WIDTH"] ?></td>
									<td align="center"><?= $row["HEIGHT"] ?></td>
									<td align="center"><?= $row["FLAP"] ?></td>
									<td align="center"><?= $row["GUSSET"] ?></td>
									<td align="center"><?= $row["ORDER_QUANTITY"] ?></td>
									<td align="center"><?= $row["RATE"] ?></td>
									<td align="center"><?= $row["AMOUNT"] ?></td>
								</tr>							
								<?
								$order_total+=$row["ORDER_QUANTITY"];
								$amount_total+=$row["AMOUNT"];
								$carton_net_weight=$po_invoice_data_array[$row['DTLS_ID']]['CARTON_NET_WEIGHT'];
								$carton_gross_weight=$po_invoice_data_array[$row['DTLS_ID']]['CARTON_GROSS_WEIGHT'];
								$i++;
								$rowspan++;	
								$descrowspan++;
							}
						}
					}
					?>
				</tbody>
				<tfoot>
					<tr>
						<th colspan="12" align="right" > Total</th>
						<th><?=$order_total?></th>
						<th></th>
						<th><?=$amount_total?></th>
					</tr>
				</tfoot>
			</table>
			<br> <br>
			<table width='1000'>
				<tbody>
					<tr> <td colspan="15">THIS IS TO CERTIFY THAT THE MERCHANDISE ARE DEPZ, BANGLADESH ORIGIN AND THE QUALITY, QUANTITY & UNIT PRICE IS AS PER THIS CREDIT & ARE STRICTLY IN ACCORDANCE WITH THE MENTIONED PROFORMA INVOICE NO: <?=rtrim($pi_number,", ") ?></td></tr>
				</tbody>
			</table>
			<?
			echo $break;
		}
	}
	
	if (in_array(9, $print_type)) {
		$print_qty=$selected_seq_id_arr[$print_type_arr[9]];
		for($ti=0;$print_qty>$ti; $ti++){	 
			?>
			<table border="1" width='1000' cellspacing="0" cellpadding="0">
				<tr>
					<td colspan="12" valign="top" align="center" style="font-size: 30px;" ><b>PACKING LIST</b></td>
				</tr>
				<tr>
					<td colspan="6" valign="top" align="left"><b>NAME & ADDRESS OF SHIPPER / EXPORTE /BENEFICERY:</b><br>
					<b> A.  <?=$company_arr[$benificiary_id]."</b><br>".$address."<br>BIN NO: ".$bin_no?>
					<br> <br>
					<b> B.  <?=$bank_name_arr[$lien_bank]["BANK_NAME"]."</b><br>".$bank_name_arr[$lien_bank]["ADDRESS"]."<br> A/C No: ".$bank_acc_arr[$lien_bank][20]["ACCOUNT_NO"]."<br>Swift Code:".$bank_name_arr[$lien_bank]["SWIFT_CODE"];?>
				
					</td>
					<td  width="500" colspan="6" valign="top" align="left">
						<table > 
								<tr>
									<td width="250"><b> <u>INVOICE NO:</u></b></td>
									<td><b><u>INVOICE DATE:</u></b></td>
								</tr>
								<tr>
									<td><?=$invoice_no?></td>
									<td><?=$invoice_date?></td>
								</tr>
								<tr>
									<td><b></u> EXP NO:<u></b></td>
									<td><b></u>EXP DATE:<u></b></td>
								</tr>
								<tr>
									<td><? echo $exp_form_no;?> </td>
									<td><?=$exp_form_date?></td>
								</tr>

								<tr>
									<td><b> <u> L/C APPLICANT NAME & ADDRESS:</u></b></td>
									<td><b><u>L/C ISSUING BANK & ADDRESS:</u></b></td>
								</tr>
								<tr>
									<td><?=$buyer_name_arr[$buyer_id]["BUYER_NAME"]."<br>".$buyer_name_arr[$buyer_id]["ADDRESS_1"]?></td>
									<td><?=$bank_name_arr[$issuing_bank_name]["BANK_NAME"]."</b><br>".$bank_name_arr[$issuing_bank_name]["ADDRESS"]?></td>
								</tr>					
						</table>
					</td>
				</tr>
				<tr>
					<td colspan="6" valign="top" align="left"><b>NAME & ADDRESS OF NOTIFAY PARTY:</b> <br><b> <?=$buyer_name_arr[$notifying_party]["BUYER_NAME"]."</b><br>".$buyer_name_arr[$notifying_party]["ADDRESS_1"]?> </td>
					<td rowspan="2" style="vertical-align: top;"><b>GOODS SUPPLIED AGINEST FOLLOWING DOCUMENTS:</b> <br><b>BTB LC No:</b> <?=$btb_system_id."  DATE: ".$lc_date."<br>" ?><b>LCAF NO:</b> <?=$lcaf_no?> <br></b>H.S.Code No:</b> <?=$hs_code?> <br> Vat/Bin Reg No: <?=$vat_bin_reg_no?><br><b>TIN No:</b> <?=$tin_number?> <br>  <b>Issueing Bank Bin No:</b> <?=$bin_no?> <br>Bond License No: <?=" ";?> <br> <b> IMPORT AGAINST EXPORT SALES CONTRACT NO:</b> <?=$lc_sc_no;?> <br><b> PROFORMA INVOICE NO:</b><?=rtrim($pi_number,", ") ?> </td>
				</tr>
				<tr>
					<td colspan="6">  
						<table> 
							<tr> 
								<td> <b><u> DRAFT AT / TENOR </u></b></td>
								<td><b><u> TRADE TERMS </u> </b></td>
							</tr>
							<tr> 
								<td><?=$tenor?> DAYS FROM THE DATE OF DELIVERY </td>
								<td> 'EX-WORKS, BENEFICIARY'S FACTORY </td>
							</tr>
							<tr> 
								<td width="250"><b><u>PLACE OF LOADING</u></b></td>
								<td width="250"><b><u>FINAL DESTINATION</u></b></td>
							</tr>
							<tr> 
								<td><?=$port_of_loading?> </td>
								<td> APPLICANT FACTORY </td>
							</tr>
						</table>
					</td>
				</tr>
				<tr>
					<td colspan="6"> 
						<table> 
							<tr> 
								<td  width="250"> <b><u> CARRIER </u></b></td>
								<td  width="250"><b><u> ORIGIN </u> </b></td>
							</tr>
							<tr> 
								<td><?=$shipment_mode[$shipping_mode]?> </td>
								<td> BANGLADESH </td>
							</tr>
						</table>
					</td>
					<td colspan="6"> 
						<table> 
							<tr> 
								<td width="250"> <b><u> FREIGHT </u></b></td>
								<td ><b><u> INCONTERMS </u> </b></td>
							</tr>
							<tr> 
								<td>COLLECT </td>
								<td> 2020 </td>
							</tr>
						</table>
					</td>
				</tr>
			</table>
			<br> <br>
			<?
			$order_id=$sql_del[0]["ID"];
			$sql_rec=sql_select("SELECT a.JOB_NO_PREFIX_NUM,  b.BUYER_STYLE_REF, b.BUYER_BUYER, b.BUYER_PO_NO, c.DESCRIPTION, c.SIZE_ID, c.PLY, c.QNTY, c.id as BREAK_ID, a.ID as MST_ID, b.id as DTLS_ID, e.length as LENGTH, e.width as WIDTH, e.height as HEIGHT, e.flap as FLAP, e.gusset as GUSSET, e.thickness as TICKNESS 
			from SUBCON_ORD_MST a, SUBCON_ORD_DTLS b, SUBCON_ORD_BREAKDOWN c left join subcon_ord_breakdown_size_info e on e.subconordbreakdownid  = c.id where a.id=b.mst_id and b.id=c.mst_id and a.id=$order_id  and a.STATUS_ACTIVE=1 and b.STATUS_ACTIVE=1 and b.STATUS_ACTIVE=1 order by c.id");

			foreach($sql_rec as $row){
				$key=$row['DESCRIPTION'].'**'.$row['BUYER_BUYER'].'**'.$row['BUYER_STYLE_REF'] ;

				$data_order_arr[$row["JOB_NO_PREFIX_NUM"]][$key][$row["BREAK_ID"]]["JOB_NO_PREFIX_NUM"]=$row["JOB_NO_PREFIX_NUM"];
				$data_order_arr[$row["JOB_NO_PREFIX_NUM"]][$key][$row["BREAK_ID"]]["BUYER_STYLE_REF"]=$row["BUYER_STYLE_REF"];
				$data_order_arr[$row["JOB_NO_PREFIX_NUM"]][$key][$row["BREAK_ID"]]["BUYER_BUYER"]=$row["BUYER_BUYER"];
				$data_order_arr[$row["JOB_NO_PREFIX_NUM"]][$key][$row["BREAK_ID"]]["DESCRIPTION"]=$row["DESCRIPTION"];
				$data_order_arr[$row["JOB_NO_PREFIX_NUM"]][$key][$row["BREAK_ID"]]["BUYER_PO_NO"]=$row["BUYER_PO_NO"];
				$data_order_arr[$row["JOB_NO_PREFIX_NUM"]][$key][$row["BREAK_ID"]]["SIZE_ID"]=$row["SIZE_ID"];
				$data_order_arr[$row["JOB_NO_PREFIX_NUM"]][$key][$row["BREAK_ID"]]["PLY"]=$row["PLY"];
				$data_order_arr[$row["JOB_NO_PREFIX_NUM"]][$key][$row["BREAK_ID"]]["DTLS_ID"]=$row["DTLS_ID"];
				$data_order_arr[$row["JOB_NO_PREFIX_NUM"]][$key][$row["BREAK_ID"]]["MST_ID"]=$row["MST_ID"];
				$data_order_arr[$row["JOB_NO_PREFIX_NUM"]][$key][$row["BREAK_ID"]]["LENGTH"]=$row["LENGTH"];
				$data_order_arr[$row["JOB_NO_PREFIX_NUM"]][$key][$row["BREAK_ID"]]["WIDTH"]=$row["WIDTH"];
				$data_order_arr[$row["JOB_NO_PREFIX_NUM"]][$key][$row["BREAK_ID"]]["HEIGHT"]=$row["HEIGHT"];
				$data_order_arr[$row["JOB_NO_PREFIX_NUM"]][$key][$row["BREAK_ID"]]["FLAP"]=$row["FLAP"];
				$data_order_arr[$row["JOB_NO_PREFIX_NUM"]][$key][$row["BREAK_ID"]]["GUSSET"]=$row["GUSSET"];
				$data_order_arr[$row["JOB_NO_PREFIX_NUM"]][$key][$row["BREAK_ID"]]["ORDER_QUANTITY"]+=$row["QNTY"];
			}

			$JobNoRowSpanArr=array();
			$descRowSpanArr=array();
			foreach($data_order_arr as $JonNO=> $JobData)
			{
				$JobNoRowSpan=0;		
				foreach($JobData as $DescData=> $desc_arr)
				{				
					foreach($desc_arr as $BreakIdData=> $row)
					{   
						$JobNoRowSpan++;
						$descRowSpanArr[$JonNO][$DescData]+=1;
					}				
				}
				$JobNoRowSpanArr[$JonNO]=$JobNoRowSpan;
				
			}

			// echo "<pre>";
			// print_r($descRowSpanArr);
			?>
			<table border="1" width='1000' cellspacing="0" cellpadding="0">
				<thead>
					<tr>
						<th width="100">ITEM DESCRIPTION</th>
						<th  width="60">PI NO</th>
						<th width="80">BUYER</th>
						<th width="90">Buyer's Style</th>
						<th width="90">PO NO</th>
						<th width="90">JOB NO</th>
						<th width="90">PLY</th>
						<th width="90">L(CM)</th>
						<th width="90">W(CM)</th>
						<th width="90">H(CM)</th>
						<th width="90">F(CM)</th>
						<th width="90">G(CM)</th>
						<th width="90">ORDER QTY</th>
						<th width="90">NET WEIGHT</th>
						<th>GROSS WEIGHT</th>
					</tr>
				</thead>
				<tbody>
					<?$i=1;
					foreach($data_order_arr as $job_no=> $job_arr)
					{
						$rowspan=0;
						foreach($job_arr as $desc=>$desc_arr)
						{ 
							$descrowspan=0;
							foreach($desc_arr as $row)
							{   					
								?>
								<tr bgcolor="<?= $bgcolor; ?>" onClick="change_color('tr_<?= $i; ?>','<?= $bgcolor; ?>')" id="tr_<?= $i; ?>">
									<?php if ($descrowspan == 0) : ?>
										<td align="center" rowspan="<?= $descRowSpanArr[$job_no][$desc] ?>"><?= $row["DESCRIPTION"] ?></td>
									<?php endif; ?>

									<?php if ($rowspan == 0) : ?>
									<td align="center" rowspan="<?= $JobNoRowSpanArr[$job_no] ?>"><?=$exportPiArr[$row["MST_ID"]]["PI_NUMBER"] ?></td>
									<?php endif; ?>

									<?php if ($descrowspan == 0) : ?>
										<td align="center" rowspan="<?= $descRowSpanArr[$job_no][$desc] ?>"><?= $row["BUYER_BUYER"] ?></td>
										<td align="center" rowspan="<?= $descRowSpanArr[$job_no][$desc] ?>"><?= $row["BUYER_STYLE_REF"] ?></td>
										<td align="center" rowspan="<?= $descRowSpanArr[$job_no][$desc] ?>"><?= $row["BUYER_PO_NO"] ?></td>
									<?php endif; ?>

									<?php if ($rowspan == 0) : ?>
										<td align="center" rowspan="<?= $JobNoRowSpanArr[$job_no] ?>"><?= $row["JOB_NO_PREFIX_NUM"] ?></td>
									<?php endif; ?>

									<td align="center"><?= $row["PLY"] ?></td>
									<td align="center"><?= $row["LENGTH"] ?></td>
									<td align="center"><?= $row["WIDTH"] ?></td>
									<td align="center"><?= $row["HEIGHT"] ?></td>
									<td align="center"><?= $row["FLAP"] ?></td>
									<td align="center"><?= $row["GUSSET"] ?></td>
									<td align="center"><?= $row["ORDER_QUANTITY"] ?></td>

									<?php if ($rowspan == 0) : ?>
										<td align="center" rowspan="<?= $JobNoRowSpanArr[$job_no] ?>"><?= $po_invoice_data_array[$row['DTLS_ID']]['CARTON_NET_WEIGHT'] ?></td>
										<td align="center" rowspan="<?= $JobNoRowSpanArr[$job_no] ?>"><?= $po_invoice_data_array[$row['DTLS_ID']]['CARTON_GROSS_WEIGHT'] ?></td>
									<?php endif; ?>
								</tr>							
								<?
								$order_total+=$row["ORDER_QUANTITY"];
								$carton_net_weight=$po_invoice_data_array[$row['DTLS_ID']]['CARTON_NET_WEIGHT'];
								$carton_gross_weight=$po_invoice_data_array[$row['DTLS_ID']]['CARTON_GROSS_WEIGHT'];
								$i++;
								$rowspan++;	
								$descrowspan++;
							}
						}
					}
					?>
				</tbody>
				<tfoot>
					<tr>
						<th colspan="12" align="right" > Total</th>
						<th><?=$order_total?></th>
						<th><?=$carton_net_weight?></th>
						<th><?=$carton_gross_weight?></th>
					</tr>
					<tr>
						<th colspan="15" align="left">AMOUNT IN WORDS: <?=number_to_words($order_total)?></th>
					</tr>
				</tfoot>
			</table>
			<br> <br>
			<table width='1000'>
				<tbody>
					<tr> <td colspan="15">THIS IS TO CERTIFY THAT THE MERCHANDISE ARE DEPZ, BANGLADESH ORIGIN AND THE QUALITY, QUANTITY & UNIT PRICE IS AS PER THIS CREDIT & ARE STRICTLY IN ACCORDANCE WITH THE MENTIONED PROFORMA INVOICE NO: <?=rtrim($pi_number,", ") ?></td></tr>
				</tbody>
			</table>
			<?
			echo $break;
		}
	}
	
	if (in_array(10, $print_type)) {
		$print_qty=$selected_seq_id_arr[$print_type_arr[10]];
		for($ti=0;$print_qty>$ti; $ti++){	 
			?>
			
			<table border="1" width='1000' cellspacing="0" cellpadding="0">
				<tr>
					<td colspan="12" valign="top" align="center" style="font-size: 30px;" ><b>  DELIVERY CHALLAN / TRUCK RECEIPT </b></td>
				</tr>
				<tr>
					<td colspan="6" valign="top" align="left"><b>NAME & ADDRESS OF SHIPPER / EXPORTE /BENEFICERY:</b><br>
					<b> A.  <?=$company_arr[$benificiary_id]."</b><br>".$address."<br>BIN NO: ".$bin_no?>
					<br> <br>
						<b> B.  <?=$bank_name_arr[$lien_bank]["BANK_NAME"]."</b><br>".$bank_name_arr[$lien_bank]["ADDRESS"]."<br> A/C No: ".$bank_acc_arr[$lien_bank][20]["ACCOUNT_NO"]."<br>Swift Code:".$bank_name_arr[$lien_bank]["SWIFT_CODE"];?>
				
					</td>
					<td  width="500" colspan="6" valign="top" align="left">
							<table > 
								<tr>
									<td width="250"><b> <u>INVOICE NO:</u></b></td>
									<td><b><u>INVOICE DATE:</u></b></td>
								</tr>
								<tr>
									<td><?=$invoice_no?></td>
									<td><?=$invoice_date?></td>
								</tr>
								<tr>
									<td><b></u> EXP NO:<u></b></td>
									<td><b></u>EXP DATE:<u></b></td>
								</tr>
								<tr>
									<td><? echo $exp_form_no;?> </td>
									<td><?=$exp_form_date?></td>
								</tr>

								<tr>
									<td><b> <u> L/C APPLICANT NAME & ADDRESS:</u></b></td>
									<td><b><u>L/C ISSUING BANK & ADDRESS:</u></b></td>
								</tr>
								<tr>
									<td><?=$buyer_name_arr[$buyer_id]["BUYER_NAME"]."<br>".$buyer_name_arr[$buyer_id]["ADDRESS_1"]?></td>
									<td><?=$bank_name_arr[$issuing_bank_name]["BANK_NAME"]."</b><br>".$bank_name_arr[$issuing_bank_name]["ADDRESS"]?></td>
								</tr>					
						</table>
					</td>
				</tr>
				<tr>
					<td colspan="6" valign="top" align="left"><b>NAME & ADDRESS OF NOTIFAY PARTY:</b> <br><b> <?=$buyer_name_arr[$notifying_party]["BUYER_NAME"]."</b><br>".$buyer_name_arr[$notifying_party]["ADDRESS_1"]?> </td>
					<td rowspan="2" style="vertical-align: top;"><b>GOODS SUPPLIED AGINEST FOLLOWING DOCUMENTS:</b> <br><b>BTB LC No:</b> <?=$btb_system_id."  DATE: ".$lc_date."<br>" ?><b>LCAF NO:</b> <?=$lcaf_no?> <br></b>H.S.Code No:</b> <?=$hs_code?> <br> Vat/Bin Reg No: <?=$vat_bin_reg_no?><br><b>TIN No:</b> <?=$tin_number?> <br>  <b>Issueing Bank Bin No:</b> <?=$bin_no?> <br>Bond License No: <?=" ";?> <br> <b> IMPORT AGAINST EXPORT SALES CONTRACT NO:</b> <?=$lc_sc_no;?> <br><b> PROFORMA INVOICE NO:</b><?=rtrim($pi_number,", ") ?> </td>
				</tr>
				<tr>
					<td colspan="6">  
						<table> 
							<tr> 
								<td> <b><u> DRAFT AT / TENOR </u></b></td>
								<td><b><u> TRADE TERMS </u> </b></td>
							</tr>
							<tr> 
								<td><?=$tenor?> DAYS FROM THE DATE OF DELIVERY </td>
								<td> 'EX-WORKS, BENEFICIARY'S FACTORY </td>
							</tr>
							<tr> 
								<td width="250"><b><u>PLACE OF LOADING</u></b></td>
								<td width="250"><b><u>FINAL DESTINATION</u></b></td>
							</tr>
							<tr> 
								<td><?=$port_of_loading?> </td>
								<td> APPLICANT FACTORY </td>
							</tr>
						</table>
					</td>
				</tr>
				<tr>
					<td colspan="6"> 
						<table> 
							<tr> 
								<td  width="250"> <b><u> CARRIER </u></b></td>
								<td  width="250"><b><u> ORIGIN </u> </b></td>
							</tr>
							<tr> 
								<td><?=$shipment_mode[$shipping_mode]?> </td>
								<td> BANGLADESH </td>
							</tr>
						</table>
					</td>
					<td colspan="6"> 
						<table> 
							<tr> 
								<td width="250"> <b><u> FREIGHT </u></b></td>
								<td ><b><u> INCONTERMS </u> </b></td>
							</tr>
							<tr> 
								<td>COLLECT </td>
								<td> 2020 </td>
							</tr>
						</table>
					</td>
				</tr>
			</table>
			<br> <br>
			<?
			$order_id=$sql_del[0]["ID"];
			$sql_rec=sql_select("SELECT a.JOB_NO_PREFIX_NUM,  b.BUYER_STYLE_REF, b.BUYER_BUYER, b.BUYER_PO_NO, c.DESCRIPTION, c.SIZE_ID, c.PLY, c.QNTY, c.id as BREAK_ID, a.ID as MST_ID, b.id as DTLS_ID, e.length as LENGTH, e.width as WIDTH, e.height as HEIGHT, e.flap as FLAP, e.gusset as GUSSET, e.thickness as TICKNESS 
			from SUBCON_ORD_MST a, SUBCON_ORD_DTLS b, SUBCON_ORD_BREAKDOWN c left join subcon_ord_breakdown_size_info e on e.subconordbreakdownid  = c.id where a.id=b.mst_id and b.id=c.mst_id and a.id=$order_id  and a.STATUS_ACTIVE=1 and b.STATUS_ACTIVE=1 and b.STATUS_ACTIVE=1 order by c.id");

			foreach($sql_rec as $row){
				$key=$row['DESCRIPTION'].'**'.$row['BUYER_BUYER'].'**'.$row['BUYER_STYLE_REF'] ;

				$data_order_arr[$row["JOB_NO_PREFIX_NUM"]][$key][$row["BREAK_ID"]]["JOB_NO_PREFIX_NUM"]=$row["JOB_NO_PREFIX_NUM"];
				$data_order_arr[$row["JOB_NO_PREFIX_NUM"]][$key][$row["BREAK_ID"]]["BUYER_STYLE_REF"]=$row["BUYER_STYLE_REF"];
				$data_order_arr[$row["JOB_NO_PREFIX_NUM"]][$key][$row["BREAK_ID"]]["BUYER_BUYER"]=$row["BUYER_BUYER"];
				$data_order_arr[$row["JOB_NO_PREFIX_NUM"]][$key][$row["BREAK_ID"]]["DESCRIPTION"]=$row["DESCRIPTION"];
				$data_order_arr[$row["JOB_NO_PREFIX_NUM"]][$key][$row["BREAK_ID"]]["BUYER_PO_NO"]=$row["BUYER_PO_NO"];
				$data_order_arr[$row["JOB_NO_PREFIX_NUM"]][$key][$row["BREAK_ID"]]["SIZE_ID"]=$row["SIZE_ID"];
				$data_order_arr[$row["JOB_NO_PREFIX_NUM"]][$key][$row["BREAK_ID"]]["PLY"]=$row["PLY"];
				$data_order_arr[$row["JOB_NO_PREFIX_NUM"]][$key][$row["BREAK_ID"]]["DTLS_ID"]=$row["DTLS_ID"];
				$data_order_arr[$row["JOB_NO_PREFIX_NUM"]][$key][$row["BREAK_ID"]]["MST_ID"]=$row["MST_ID"];
				$data_order_arr[$row["JOB_NO_PREFIX_NUM"]][$key][$row["BREAK_ID"]]["LENGTH"]=$row["LENGTH"];
				$data_order_arr[$row["JOB_NO_PREFIX_NUM"]][$key][$row["BREAK_ID"]]["WIDTH"]=$row["WIDTH"];
				$data_order_arr[$row["JOB_NO_PREFIX_NUM"]][$key][$row["BREAK_ID"]]["HEIGHT"]=$row["HEIGHT"];
				$data_order_arr[$row["JOB_NO_PREFIX_NUM"]][$key][$row["BREAK_ID"]]["FLAP"]=$row["FLAP"];
				$data_order_arr[$row["JOB_NO_PREFIX_NUM"]][$key][$row["BREAK_ID"]]["GUSSET"]=$row["GUSSET"];
				$data_order_arr[$row["JOB_NO_PREFIX_NUM"]][$key][$row["BREAK_ID"]]["ORDER_QUANTITY"]+=$row["QNTY"];
			}

			$JobNoRowSpanArr=array();
			$descRowSpanArr=array();
			foreach($data_order_arr as $JonNO=> $JobData)
			{
				$JobNoRowSpan=0;		
				foreach($JobData as $DescData=> $desc_arr)
				{				
					foreach($desc_arr as $BreakIdData=> $row)
					{   
						$JobNoRowSpan++;
						$descRowSpanArr[$JonNO][$DescData]+=1;
					}				
				}
				$JobNoRowSpanArr[$JonNO]=$JobNoRowSpan;
				
			}

			// echo "<pre>";
			// print_r($descRowSpanArr);
			?>
			<table border="1" width='1000' cellspacing="0" cellpadding="0">
				<thead>
					<tr>
						<th width="100">ITEM DESCRIPTION</th>
						<th  width="60">PI NO</th>
						<th width="80">BUYER</th>
						<th width="90">Buyer's Style</th>
						<th width="90">PO NO</th>
						<th width="90">JOB NO</th>
						<th width="90">PLY</th>
						<th width="90">L(CM)</th>
						<th width="90">W(CM)</th>
						<th width="90">H(CM)</th>
						<th width="90">F(CM)</th>
						<th width="90">G(CM)</th>
						<th width="90">ORDER QTY</th>
					</tr>
				</thead>
				<tbody>
					<?$i=1;
					foreach($data_order_arr as $job_no=> $job_arr)
					{
						$rowspan=0;
						foreach($job_arr as $desc=>$desc_arr)
						{ 
							$descrowspan=0;
							foreach($desc_arr as $row)
							{   					
								?>
								<tr bgcolor="<?= $bgcolor; ?>" onClick="change_color('tr_<?= $i; ?>','<?= $bgcolor; ?>')" id="tr_<?= $i; ?>">
									<?php if ($descrowspan == 0) : ?>
										<td align="center" rowspan="<?= $descRowSpanArr[$job_no][$desc] ?>"><?= $row["DESCRIPTION"] ?></td>
									<?php endif; ?>

									<?php if ($rowspan == 0) : ?>
									<td align="center" rowspan="<?= $JobNoRowSpanArr[$job_no] ?>"><?=$exportPiArr[$row["MST_ID"]]["PI_NUMBER"] ?></td>
									<?php endif; ?>

									<?php if ($descrowspan == 0) : ?>
										<td align="center" rowspan="<?= $descRowSpanArr[$job_no][$desc] ?>"><?= $row["BUYER_BUYER"] ?></td>
										<td align="center" rowspan="<?= $descRowSpanArr[$job_no][$desc] ?>"><?= $row["BUYER_STYLE_REF"] ?></td>
										<td align="center" rowspan="<?= $descRowSpanArr[$job_no][$desc] ?>"><?= $row["BUYER_PO_NO"] ?></td>
									<?php endif; ?>

									<?php if ($rowspan == 0) : ?>
										<td align="center" rowspan="<?= $JobNoRowSpanArr[$job_no] ?>"><?= $row["JOB_NO_PREFIX_NUM"] ?></td>
									<?php endif; ?>

									<td align="center"><?= $row["PLY"] ?></td>
									<td align="center"><?= $row["LENGTH"] ?></td>
									<td align="center"><?= $row["WIDTH"] ?></td>
									<td align="center"><?= $row["HEIGHT"] ?></td>
									<td align="center"><?= $row["FLAP"] ?></td>
									<td align="center"><?= $row["GUSSET"] ?></td>
									<td align="center"><?= $row["ORDER_QUANTITY"] ?></td>
								</tr>							
								<?
								$order_total+=$row["ORDER_QUANTITY"];
								$carton_net_weight=$po_invoice_data_array[$row['DTLS_ID']]['CARTON_NET_WEIGHT'];
								$carton_gross_weight=$po_invoice_data_array[$row['DTLS_ID']]['CARTON_GROSS_WEIGHT'];
								$i++;
								$rowspan++;	
								$descrowspan++;
							}
						}
					}
					?>
				</tbody>
				<tfoot>
					<tr>
						<th colspan="12" align="right" > Total</th>
						<th><?=$order_total?></th>
					</tr>
				</tfoot>
			</table>
			<br> <br>
			<table width='1000'>
				<tbody>
					<tr> <td colspan="15">THIS IS TO CERTIFY THAT THE MERCHANDISE ARE DEPZ, BANGLADESH ORIGIN AND THE QUALITY, QUANTITY & UNIT PRICE IS AS PER THIS CREDIT & ARE STRICTLY IN ACCORDANCE WITH THE MENTIONED PROFORMA INVOICE NO: <?=rtrim($pi_number,", ") ?></td></tr>
				</tbody>
			</table>
			<?
			echo $break;
		}
	}

	$html = ob_get_contents();
	ob_clean();
	foreach (glob("tb*.xls") as $filename) {
	@unlink($filename);
	}
	//---------end------------//
	$name=time();
	$filename="tb".$user_id."_".$name.".xls";
	$create_new_doc = fopen($filename, 'w');
	$is_created = fwrite($create_new_doc, $html);
	echo "$filename####$html";
	exit();	
}

if($action=='bill_of_exchange_report')
{
}










?>
