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
if ($action=="load_supplier_dropdown")
{
	//echo $data;
	$data = explode('_',$data);

	if ($data[1]==0)
	{
		//echo create_drop_down( "cbo_supplier_id", 165, $blank_array,'', 1, '----Select----',0,0,0);
		echo create_drop_down( "cbo_supplier_id",165,"select c.supplier_name,c.id from lib_supplier_tag_company a,lib_supplier_party_type b, lib_supplier c where c.id=b.supplier_id and a.supplier_id = b.supplier_id and a.tag_company='$data[0]' and c.status_active=1 and c.is_deleted=0 group by c.id, c.supplier_name order by c.supplier_name",'id,supplier_name', 1, '----Select----',0,0,0);
	}
	else if($data[1]==1)
	{
		echo create_drop_down( "cbo_supplier_id",165,"select c.supplier_name,c.id from lib_supplier_tag_company a,lib_supplier_party_type b, lib_supplier c where c.id=b.supplier_id and a.supplier_id = b.supplier_id and a.tag_company='$data[0]' and b.party_type =2 and c.status_active=1 and c.is_deleted=0 group by c.id, c.supplier_name order by c.supplier_name",'id,supplier_name', 1, '----Select----',0,0,0);
	}
	else if($data[1]==2 || $data[1]==3 || $data[1]==13 || $data[1]==14)
	{
		echo create_drop_down( "cbo_supplier_id", 165,"select c.supplier_name, c.id from  lib_supplier_tag_company a,lib_supplier_party_type b, lib_supplier c where c.id=b.supplier_id and a.supplier_id = b.supplier_id and a.tag_company='$data[0]' and b.party_type =9 and c.status_active=1 and c.is_deleted=0 group by c.id, c.supplier_name order by c.supplier_name",'id,supplier_name', 1, '----Select----',0,0,0);
	}
	else if($data[1]==4)
	{
		echo create_drop_down( "cbo_supplier_id", 165,"select c.supplier_name,c.id from  lib_supplier_tag_company a,lib_supplier_party_type b, lib_supplier c where c.id=b.supplier_id and a.supplier_id = b.supplier_id and a.tag_company='$data[0]' and b.party_type in(4,5) and c.status_active=1 and c.is_deleted=0 group by c.id, c.supplier_name order by c.supplier_name",'id,supplier_name', 1, '----Select----',0,0,0);

	}
	else if($data[1]==5 || $data[1]==6 || $data[1]==7)
	{
		echo create_drop_down( "cbo_supplier_id", 165,"select c.supplier_name,c.id from  lib_supplier_tag_company a,lib_supplier_party_type b, lib_supplier c where c.id=b.supplier_id and a.supplier_id = b.supplier_id and a.tag_company='$data[0]' and b.party_type=3 group by c.id, c.supplier_name order by c.supplier_name",'id,supplier_name', 1, '----Select----',0,0,0);
	}
	else if($data[1]==8)
	{
		echo create_drop_down( "cbo_supplier_id", 165,"select c.supplier_name,c.id from  lib_supplier_tag_company a,lib_supplier_party_type b, lib_supplier c where c.id=b.supplier_id and a.supplier_id = b.supplier_id and a.tag_company='$data[0]' and b.party_type = 7 and c.status_active=1 and c.is_deleted=0 group by c.id, c.supplier_name order by c.supplier_name",'id,supplier_name', 1, '----Select----',0,0,0);
	}
	else if($data[1]==9 || $data[1]==10)
	{
		echo create_drop_down( "cbo_supplier_id", 165,"select c.supplier_name,c.id from  lib_supplier_tag_company a,lib_supplier_party_type b, lib_supplier c where c.id=b.supplier_id and a.supplier_id = b.supplier_id and a.tag_company='$data[0]' and b.party_type = 6 group by c.id, c.supplier_name order by c.supplier_name",'id,supplier_name', 1, '----Select----',0,0,0);
	}
	else if($data[1]==11)
	{
		echo create_drop_down( "cbo_supplier_id", 165,"select c.supplier_name,c.id from  lib_supplier_tag_company a,lib_supplier_party_type b, lib_supplier c where c.id=b.supplier_id and a.supplier_id = b.supplier_id and a.tag_company='$data[0]' and b.party_type = 8 and c.status_active=1 and c.is_deleted=0 group by c.id, c.supplier_name order by c.supplier_name",'id,supplier_name', 1, '----Select----',0,0,0);
	}
	else if($data[1]==12 || $data[1]==24 || $data[1]==25)
	{
		echo create_drop_down( "cbo_supplier_id", 165,"select c.supplier_name,c.id from  lib_supplier_tag_company a,lib_supplier_party_type b, lib_supplier c where c.id=b.supplier_id and a.supplier_id = b.supplier_id and a.tag_company='$data[0]' and b.party_type in(20,21,22,23,24,30,31,32,35,36,37,38,39) and c.status_active=1 and c.is_deleted=0 group by c.id, c.supplier_name",'id,supplier_name', 1, '----Select----',0,0,0);
	}
	else if($data[1]==32)
	{
		echo create_drop_down( "cbo_supplier_id", 165,"select c.supplier_name,c.id from  lib_supplier_tag_company a,lib_supplier_party_type b, lib_supplier c where c.id=b.supplier_id and a.supplier_id = b.supplier_id and a.tag_company='$data[0]' and b.party_type in(92) and c.status_active=1 and c.is_deleted=0 group by c.id, c.supplier_name order by c.supplier_name",'id,supplier_name', 1, '----Select----',0,0,0);
	}
	else if($data[1]==110)
	{
		echo create_drop_down( "cbo_supplier_id", 165,"select comp.id, comp.company_name from lib_company comp where comp.status_active=1 and comp.is_deleted=0 $company_cond order by comp.company_name",'id,company_name', 1, '----Select----',0,0,0);
	}
	else
	{
		echo create_drop_down( "cbo_supplier_id", 165,"select DISTINCT(c.supplier_name),c.id from  lib_supplier_tag_company a,lib_supplier_party_type b, lib_supplier c where c.id=b.supplier_id and a.supplier_id = b.supplier_id and a.tag_company='$data[0]' and b.party_type = 7 and c.status_active=1 and c.is_deleted=0 order by c.supplier_name",'id,supplier_name', 1, '-- Select Supplier --',0,'',0);
	}

	exit();
}

if ($action=="pi_popup")
{
	echo load_html_head_contents("PI Info", "../../../", 1, 1,'','','');
	extract($_REQUEST);
	?>

    <script>
		var update_id='<? echo $update_id; ?>';

		var selected_id = new Array(); selected_name = new Array();
		var supplier_id_arr_chk = new Array; var entry_form_arr = new Array;

		function check_all_data(is_checked)
		{
			var tbl_row_count = document.getElementById( 'tbl_list_search' ).rows.length;
			tbl_row_count = tbl_row_count - 1;

			for( var i = 1; i <= tbl_row_count; i++ ) {
				js_set_value( i );
			}
		}

		function toggle( x, origColor )
		{
			var newColor = 'yellow';
			if ( x.style )
			{
				x.style.backgroundColor = ( newColor == x.style.backgroundColor )? origColor : newColor;
			}
		}

		function set_all()
		{
			var old=document.getElementById('txt_pi_row_id').value;
			if(old!="")
			{
				old=old.split(",");
				for(var i=0; i<old.length; i++)
				{
					js_set_value( old[i] )
				}
			}
		}

		function js_set_value( str)
		{
			var refClosingStatus=$('#refClosingStatus_' + str).val();
			if(refClosingStatus==1)
			{
				alert("This PI Already Closed");return;
			}
			
			if(update_id!="")
			{
				var data=$('#txt_individual_id' + str).val()+"**"+update_id;
				if(document.getElementById('search' + str).style.backgroundColor=='yellow')
				{
					var pi_no=$('#search' + str).find("td:eq(1)").text();
					var response = return_global_ajax_value( data, 'check_used_or_not', '', 'commercial_office_note_controller');
					response=response.split("**");
					if(response[0]==1)
					{
						alert("Bellow Invoice Found Against PI- "+pi_no+". So You can't Detach it.\n Invoice No: "+response[1]);
						return false;
					}
				}
			}

			//=========Supplier and Entry Form Mixing validation Start==========
			//alert(supplier_id+'='+supplier_id_arr_chk);
			var any_selected = $('#txt_selected_id').val();
			if(any_selected=="")
			{
				supplier_id_arr_chk = [];
				entry_form_arr = [];
				selected_id = []; 
				selected_name = [];
			}

			var supplier_id = $('#supplierChk_' + str).val();
			if(supplier_id_arr_chk.length==0)
			{
				supplier_id_arr_chk.push( supplier_id );
			}
			else if( jQuery.inArray( supplier_id, supplier_id_arr_chk )==-1 &&  supplier_id_arr_chk.length>0)
			{
				alert("Supplier Mixed is Not Allowed");
				return;
			}

			var entry_form = $('#entryForm_' + str).val();
			var item_category_id = $('#itemCategory_' + str).val();
			if(entry_form_arr.length==0)
			{
				entry_form_arr.push( entry_form );
			}
			else if( jQuery.inArray( entry_form, entry_form_arr )==-1 &&  entry_form_arr.length>0)
			{
				alert("Entry Form Mixed is Not Allowed");
				return;
			}
			//===================End ========================

			toggle( document.getElementById( 'search' + str ), '#FFFFCC' );

			if( jQuery.inArray( $('#txt_individual_id' + str).val(), selected_id ) == -1 ) {
				selected_id.push( $('#txt_individual_id' + str).val() );
				selected_name.push( $('#txt_individual' + str).val() );
			}
			else
			{
				for( var i = 0; i < selected_id.length; i++ )
				{
					if( selected_id[i] == $('#txt_individual_id' + str).val() )
					break;
				}
				selected_id.splice( i, 1 );
				selected_name.splice( i, 1 );
			}
			var id =''; var name = '';
			for( var i = 0; i < selected_id.length; i++ ) {
				id += selected_id[i] + ',';
				name += selected_name[i] + ',';
			}
			id = id.substr( 0, id.length - 1 );
			name = name.substr( 0, name.length - 1 );

			$('#txt_selected_id').val( id );
			$('#txt_selected').val( name );
			if(id=="")
			{
				$('#txt_pi_entry_form').val('');
				$('#txt_item_category').val('');
				
			}else{
				$('#txt_pi_entry_form').val(entry_form);
				$('#txt_item_category').val(item_category_id);
			}
		}

		function reset_hide_field(type)
		{
			$('#txt_selected_id').val( '' );
			$('#txt_selected').val( '' );
			if(type==1)
			{
				$('#search_div').html( '' );
			}
		}
    </script>

	</head>

	<body>
		<div align="center" style="width:950px;" >
			<form name="searchorderfrm_1"  id="searchorderfrm_1" autocomplete="off">
				<fieldset style="width:940px;margin-left:10px">
					<table style="margin-top:10px" width="940" cellspacing="0" cellpadding="0" border="1" class="rpt_table" align="center" rules="all">
						<thead>
							<th>Importer</th>
							<th>Category</th>
							<th>Supplier</th>
							<th>PI Number</th>
							<th>System ID</th>
							<th>PI Date</th>
							<th><input type="reset" name="button" class="formbutton" value="Reset" onClick="reset_hide_field(1)" style="width:70px;"></th>
							<input type="hidden" name="txt_selected_id" id="txt_selected_id" value="" />
							<input type="hidden" name="txt_selected"  id="txt_selected" value="" />
							<input type="hidden" name="txt_pi_entry_form"  id="txt_pi_entry_form" value="" />
							<input type="hidden" name="txt_item_category"  id="txt_item_category" value="" />
						</thead>
						<tr class="general">
							<td align="center">
								<?
								echo create_drop_down( "cbo_company_id", 140,"select comp.id, comp.company_name from lib_company comp where comp.status_active=1 and comp.is_deleted=0 $company_cond order by comp.company_name",'id,company_name', 1, '----Select----',$cbo_importer_id,"",1);
								?>
							</td>
							<td align="center">
								<?
								echo create_drop_down( "cbo_item_category", 140, $item_category,'', 1, '----Select----',$item_category_id,"load_drop_down( 'commercial_office_note_controller',document.getElementById('cbo_company_id').value+'_'+this.value, 'load_supplier_dropdown', 'supplier_td' );",0,'','','','74,72,79,73,71,77,78,75,76');
								?>
							</td>
							<td align="center" id="supplier_td">
								<? echo create_drop_down( "cbo_supplier_id", 165,$blank_array,'', 1, '----Select----',0,0,0); ?>
							</td>
							<td align="center">
								<input type="text" name="txt_pi_no" id="txt_pi_no" class="text_boxes" style="width:100px">
							</td>
							<td align="center">
								<input type="text" name="txt_system_no" id="txt_system_no" class="text_boxes" style="width:100px">
							</td>
							<td align="center">
								<input type="text" name="txt_date_from" id="txt_date_from" class="datepicker" style="width:55px">To
								<input type="text" name="txt_date_to" id="txt_date_to" class="datepicker" style="width:55px">
							</td>
							<td align="center">
								<input type="button" name="button2" class="formbutton" value="Show" onClick="show_list_view ( document.getElementById('txt_pi_no').value+'_'+document.getElementById('txt_date_from').value+'_'+document.getElementById('txt_date_to').value+'_'+document.getElementById('cbo_item_category').value+'_'+document.getElementById('cbo_company_id').value+'_'+document.getElementById('cbo_supplier_id').value+'_'+'<? echo $txt_hidden_pi_id; ?>'+'_'+document.getElementById('txt_system_no').value, 'create_pi_search_list_view', 'search_div', 'commercial_office_note_controller', 'setFilterGrid(\'tbl_list_search\',-1)');reset_hide_field(0);set_all();" style="width:70px;" />
							</td>
						</tr>
						<tr>
							<td colspan="7" align="center" height="40" valign="middle"><? echo load_month_buttons(1);  ?></td>
						</tr>
					</table>
					<div style="margin-top:10px" id="search_div"></div>
				</fieldset>
			</form>
		</div>
	</body>

	<script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
	<script>
		load_drop_down( 'commercial_office_note_controller',<?  echo $cbo_importer_id; ?>+'_'+document.getElementById('cbo_item_category').value, 'load_supplier_dropdown', 'supplier_td' );
	</script>
	</html>
	<?
}

if($action=="create_pi_search_list_view")
{
	$data=explode('_',$data);
	$txt_system_no=$data[7];
	
	$sql=sql_select("select b.approval_need, a.setup_date from approval_setup_mst a, approval_setup_dtls b where a.id=b.mst_id and a.company_id=$data[4] and b.page_id in (18) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.setup_date = (select max(setup_date) as id from approval_setup_mst where is_deleted=0 and company_id=$data[4] )");
	foreach($sql as $row){
		$trims_approval_need = $row[csf('approval_need')];
		//$test_data[$row[csf('page_id')]]=$row[csf('approval_need')]."=".$row[csf('setup_date')];
	}
	//var_dump($_SESSION);
	//print_r($trims_approval_need);die;
	if ($data[0]!="") $pi_number="%".$data[0]."%"; else $pi_number = '%%';

	if($db_type==0)
	{
		if ($data[1]!="" && $data[2]!="") $pi_date = "and pi_date between '".change_date_format($data[1], "yyyy-mm-dd", "-")."' and '".change_date_format($data[2], "yyyy-mm-dd", "-")."'"; else $pi_date ="";
	}
	else if($db_type==2)
	{
		if($data[1]!="" && $data[2]!="") $pi_date ="and pi_date between '".change_date_format($data[1],'','',1)."' and '".change_date_format($data[2],'','',1)."'";
		else $pi_date="";
	}

	$item_category_id =$data[3];
	if($data[4]!=0) $importer_id =$data[4]; else $importer_id='%%';
	if($data[5]!=0) $supplier_id =$data[5]; else $supplier_id='%%';


	$all_pi_id=$data[6];
	$hidden_pi_id=explode(",",$all_pi_id);
	$comp=return_library_array( "select id, company_short_name from lib_company",'id','company_short_name');
	$supplier=return_library_array( "select id, supplier_name from lib_supplier",'id','supplier_name');

	$import_pi_cond='';
	if($item_category_id==110)
	{
		$item_category_id=10;
		$import_pi_cond=" and a.import_pi=1 and a.within_group=1";
	}

	$item_category_cond="";
	if($item_category_id)
	{
		$item_category_cond = " and b.item_category_id = $item_category_id ";
	}

	$system_no_cond="";
	if($txt_system_no)
	{
		$system_no_cond = " and b.id = $txt_system_no ";
	}
	if($trims_approval_need ==1)
	{
		$approve_cond = " and a.approved = 1";
		$all_pi_id_cond = "";
	}else{
		$approve_cond = "";
		$all_pi_id_cond = " or a.id in($all_pi_id)";
	}

	if($all_pi_id=="")
	{
		$sql= "SELECT a.id, a.pi_number, a.pi_date,  a.importer_id, a.supplier_id, a.last_shipment_date, a.hs_code, a.pi_basis_id, a.net_total_amount, a.import_pi, a.approved, a.entry_form, a.item_category_id, a.ref_closing_status
		from com_pi_master_details a, com_pi_item_details b
		where a.id=b.pi_id and a.supplier_id like '".$supplier_id."' and a.importer_id like '".$importer_id."' $item_category_cond and a.pi_number like '$pi_number' $pi_date $import_pi_cond and a.status_active = 1 and a.is_deleted =0 and a.id not in(select pi_id from commercial_office_note_dtls where status_active=1 and is_deleted=0) $approve_cond
		group by a.id, a.pi_number, a.pi_date,  a.importer_id, a.supplier_id, a.last_shipment_date, a.hs_code, a.pi_basis_id, a.net_total_amount, a.import_pi, a.approved, a.entry_form, a.item_category_id, a.ref_closing_status order by a.pi_number";
	}
	else
	{
		$sql= "SELECT a.id, a.pi_number, a.pi_date,  a.importer_id, a.supplier_id, a.last_shipment_date, a.hs_code, a.pi_basis_id, a.net_total_amount, a.import_pi, a.approved, a.entry_form, a.item_category_id, a.ref_closing_status
		from com_pi_master_details a, com_pi_item_details b
		where a.id = b.pi_id and a.supplier_id like '".$supplier_id."' and a.importer_id like '".$importer_id."' $item_category_cond and a.pi_number like '$pi_number' $pi_date $import_pi_cond $approve_cond and a.status_active = 1 and a.is_deleted =0 and a.id not in(select pi_id from commercial_office_note_dtls where status_active=1 and is_deleted=0)   $all_pi_id_cond
		group by a.id, a.pi_number, a.pi_date,  a.importer_id, a.supplier_id, a.last_shipment_date, a.hs_code, a.pi_basis_id, a.net_total_amount, a.import_pi, a.approved,a.entry_form, a.item_category_id, a.ref_closing_status order by a.pi_number";
	}
	//echo $sql; // or a.id in($all_pi_id)
	?>
    <div>
        <table cellspacing="0" cellpadding="0" border="1" rules="all" width="840" class="rpt_table">
            <thead>
                <th width="30">SL</th>
                <th width="110">PI No</th>
                <th width="75">PI Date</th>
                <th width="130">Supplier</th>
                <th width="100">Item Category</th>
                <th width="60">HS Code</th>
                <th width="100">PI Basis</th>
                <th width="75">Last Ship Date</th>
                <th>PI Value</th>
            </thead>
		</table>
		<div style="width:840px; max-height:270px; overflow-y:scroll">
        	<table cellspacing="0" cellpadding="0" border="1" rules="all" width="822" class="rpt_table" id="tbl_list_search">
                <?
                $i=1; $pi_row_id="";
                $nameArray=sql_select( $sql );
                foreach ($nameArray as $selectResult)
                {
                    if ($i%2==0)
                        $bgcolor="#E9F3FF";
                    else
                        $bgcolor="#FFFFFF";

					if(in_array($selectResult[csf('id')],$hidden_pi_id))
					{
						if($pi_row_id=="") $pi_row_id=$i; else $pi_row_id.=",".$i;
					}

					if($selectResult[csf('import_pi')]==1)
					{
						$supplier_name=$comp[$selectResult[csf('supplier_id')]];
					}
					else
					{
						$supplier_name=$supplier[$selectResult[csf('supplier_id')]];
					}
            		?>
                    <tr height="20" bgcolor="<? echo $bgcolor; ?>" style="text-decoration:none; cursor:pointer" id="search<? echo $i;?>" onClick="js_set_value(<? echo $i;?>)">
                        <td width="30" align="center"><? echo "$i"; ?>
							<input type="hidden" name="txt_individual_id" id="txt_individual_id<? echo $i; ?>" value="<? echo $selectResult[csf('id')]; ?>"/>
							<input type="hidden" name="txt_individual" id="txt_individual<? echo $i; ?>" value="<? echo $selectResult[csf('pi_number')]; ?>"/>
							<input type="hidden" name="supplierChk[]" id="supplierChk_<? echo $i; ?>" value="<? echo $selectResult[csf('import_pi')].'_'.$selectResult[csf('supplier_id')]; ?>"/>
							<input type="hidden" name="entryForm[]" id="entryForm_<? echo $i; ?>" value="<? echo $selectResult[csf('entry_form')];?>"/>
							<input type="hidden" name="itemCategory[]" id="itemCategory_<? echo $i; ?>" value="<? echo $selectResult[csf('item_category_id')];?>"/>
							<input type="hidden" name="refClosingStatus[]" id="refClosingStatus_<? echo $i; ?>" value="<? echo $selectResult[csf('ref_closing_status')];?>"/>
                        </td>
                        <td width="110"><p><? echo $selectResult[csf('pi_number')];?></p></td>
                        <td width="75"><? echo change_date_format($selectResult[csf('pi_date')]);?></td>
                        <td width="130"><p><? echo $supplier_name;//$supplier[$selectResult[csf('supplier_id')]]; ?></p></td>
                        <td width="100" title="<? echo $selectResult[csf('item_category_id')]; ?>"><p><? echo $item_category[$selectResult[csf('item_category_id')]]; ?></p></td>
                        <td width="60"><p><? echo $selectResult[csf('hs_code')]; ?></p></td>                        
                        <td width="100"><p><? echo $pi_basis[$selectResult[csf('pi_basis_id')]]; ?></p></td>
                        <td width="75"><? echo change_date_format($selectResult[csf('last_shipment_date')]); ?></td>
                        <td align="right"><? echo number_format($selectResult[csf('net_total_amount')],2,'.',''); ?>&nbsp;</td>
                    </tr>
                	<?
                	$i++;
                }
                ?>
               	<input type="hidden" name="txt_pi_row_id" id="txt_pi_row_id" value="<? echo $pi_row_id; ?>"/>
            </table>
        </div>
        <table width="900" cellspacing="0" cellpadding="0" border="1" align="center">
            <tr>
                <td align="center" height="30" valign="bottom">
                    <div style="width:100%">
                        <div style="width:45%; float:left" align="left">
                            <input type="checkbox" name="check_all" id="check_all" onClick="check_all_data(this.checked)" /> Check / Uncheck All
                        </div>
                        <div style="width:53%; float:left" align="left">
                            <input type="button" name="close" onClick="parent.emailwindow.hide();" class="formbutton" value="Close" style="width:100px" />
                        </div>
                    </div>
                </td>
            </tr>
        </table>
	</div>
	<?
	exit();
}

if( $action=='show_pi_details_list') // from btb
{
	//print_r($data);die;
	$data = explode('_',$data);

	$pi_mst_id = $data[0];
	$pi_entry_form = $data[1];
	$txt_item_category_id = $data[2];
	//$pi_mst_id = explode('*',$pi_mst_id);
	$size_library = return_library_array('SELECT id,size_name FROM lib_size','id','size_name');
	$color_library = return_library_array('SELECT id,color_name FROM lib_color','id','color_name');
	$lib_body_part_arr=return_library_array("select id, body_part_full_name from lib_body_part", "id", "body_part_full_name");

	$sql = "SELECT a.item_category_id, a.id as dtls_id, a.color_id, a.count_name, a.yarn_composition_item1, a.yarn_composition_percentage1, a.yarn_composition_item2,a.yarn_composition_percentage2, a.yarn_type, a.body_part_id, a.fab_type, a.fabric_construction, a.fab_design, a.fabric_composition, a.uom,a.quantity,a.net_pi_rate as rate, a.net_pi_amount as amount, a.hs_code, b.pi_date, b.pi_number, b.id as master_id, a.item_description
	FROM com_pi_item_details a, com_pi_master_details b
	WHERE b.id=a.pi_id and a.pi_id in($pi_mst_id) and a.status_active=1 and a.is_deleted=0 order by a.id asc";
	//echo $sql;
	$data_array=sql_select($sql);

	$yarn_count = return_library_array('SELECT id,yarn_count FROM lib_yarn_count','id','yarn_count');
	?>
    <table class="rpt_table" width="1000" cellspacing="1" rules="all">
		<thead>
			<tr>
				<th width="40">SL No</th>
				<th width="100">PI No</th>
				<th width="100">PI Date</th>
				<th width="150">Item Description</th>
				<th width="100">HS Code</th>
				<th width="50">UOM</th>
				<th width="100">Quantity</th>
				<th width="100">Rate</th>
				<th width="100">Value</th>
				<!-- <th width="30"></th> -->
			</tr>
		</thead>
	</table>
	<table class="rpt_table" width="1000" cellspacing="1" rules="all" id="pi_details_list">
		<tbody>
		<?
		$i = 0;
		foreach($data_array as $row)
		{
			$i++;
			if( $i % 2 == 0 ) $bgcolor="#E9F3FF";
			else $bgcolor = "#FFFFFF";
			if ($row[csf('item_category_id')]==1) 
			{
				if($row[csf('yarn_composition_percentage1')]!=0) {$composition_percentage1 = $row[csf('yarn_composition_percentage1')]."%";}
				if($row[csf('yarn_composition_percentage2')]!=0) {$composition_percentage2 = $row[csf('yarn_composition_percentage2')]."%";}
				$item_desc=$composition[$row[csf('yarn_composition_item1')]].' '.$composition[$row[csf('yarn_composition_item2')]].' '.$composition_percentage1.' '.$composition_percentage2;
			}
			else if ($row[csf('item_category_id')]==2)
			{
				$item_desc=$row[csf('fabric_construction')]." ".$row[csf('fabric_composition')];
			}
			else if ($row[csf('item_category_id')]==3)
			{
				$item_desc=$lib_body_part_arr[$row[csf('body_part_id')]]." ".$row[csf('fab_type')]." ".$row[csf('fabric_construction')]." ".$row[csf('fab_design')]." ".$row[csf('fabric_composition')];
			}
			else
			{
				$item_desc=$row[csf('item_description')];
			}
			
			?>
			<tr id="tr_<? echo $i; ?>" bgcolor="<? echo $bgcolor; ?>">
				<td id="slno_<? echo $i; ?>" width="40"><p><? echo $i; ?></p></td>
				<td width="100"><p><? echo $row[csf('pi_number')]; ?></p></td>
				<td width="100"><p><? echo change_date_format($row[csf('pi_date')]); ?></p></td>
				
				<td width="150"><p><? echo $item_desc; ?></p>
					<input type="hidden" name="bodyPartId[]" id="bodyPartId_<? echo $i; ?>" value="<? echo $row[csf('body_part_id')]; ?>"/>
					<input type="hidden" name="fabType[]" id="fabType_<? echo $i; ?>" value="<? echo $row[csf('fab_type')]; ?>"/>
					<input type="hidden" name="fabric_construction[]" id="fabric_construction_<? echo $i; ?>" value="<? echo $row[csf('fabric_construction')]; ?>"/>
					<input type="hidden" name="fab_design[]" id="fab_design_<? echo $i; ?>" value="<? echo $row[csf('fab_design')]; ?>"/>
					<input type="hidden" name="fabric_composition[]" id="fabric_composition_<? echo $i; ?>" value="<? echo $row[csf('fabric_composition')]; ?>"/>
				</td>

				<td width="100"><p><? echo $row[csf('hs_code')]; ?></p></td>
				<td width="50"><p><? if( $row[csf('uom')] != 0 ) echo $unit_of_measurement[$row[csf('uom')]]; ?></p></td>
				<td width="100" align="right"><p><? echo number_format($row[csf('quantity')],2); ?></p></td>
				<td width="100" align="right"><p><? echo $row[csf('rate')]; ?></p></td>
				<td width="100" align="right"><p><? echo number_format($row[csf('amount')],4);  $total_ammount += $row[csf('amount')];?></p>
					<input type="hidden" name="pidtlsId[]" id="pidtlsId_<? echo $i; ?>" value="<? echo $row[csf('dtls_id')]; ?>"/>
                    <input type="hidden" name="piId[]" id="piId_<? echo $i; ?>" value="<? echo $row[csf('master_id')]; ?>"/>
                    <input type="hidden" name="piNumber[]" id="piNumber_<? echo $i; ?>" value="<? echo $row[csf('pi_number')]; ?>"/>
                    <input type="hidden" name="compositionItem1[]" id="compositionItem1_<? echo $i; ?>" value="<? echo $row[csf('yarn_composition_item1')]; ?>"/>
                    <input type="hidden" name="compositionPercentage[]" id="compositionPercentage_<? echo $i; ?>" value="<? echo $row[csf('yarn_composition_percentage1')]; ?>"/>
                    <input type="hidden" name="compositionItem2[]" id="compositionItem2_<? echo $i; ?>" value="<? echo $row[csf('yarn_composition_item2')]; ?>"/>
                    <input type="hidden" name="itemDescription[]" id="itemDescription_<? echo $i; ?>" value="<? echo $row[csf('item_description')]; ?>"/>
                    <input type="hidden" name="itemCategoryId[]" id="itemCategoryId_<? echo $i; ?>" value="<? echo $row[csf('item_category_id')]; ?>"/>
                    <input type="hidden" name="colorId[]" id="colorId_<? echo $i; ?>" value="<? echo $row[csf('color_id')]; ?>"/>
                    <input type="hidden" name="countName[]" id="countName_<? echo $i; ?>" value="<? echo $row[csf('count_name')]; ?>"/>
                    <input type="hidden" name="yarnType[]" id="yarnType_<? echo $i; ?>" value="<? echo $row[csf('yarn_type')]; ?>"/>
                    <input type="hidden" name="hsCode[]" id="hsCode_<? echo $i; ?>" value="<? echo $row[csf('hs_code')]; ?>"/>
                    <input type="hidden" name="uom[]" id="uom_<? echo $i; ?>" value="<? echo $row[csf('uom')]; ?>"/>
                    <input type="hidden" name="quantity[]" id="quantity_<? echo $i; ?>" value="<? echo $row[csf('quantity')]; ?>"/>
                    <input type="hidden" name="rate[]" id="rate_<? echo $i; ?>" value="<? echo $row[csf('rate')]; ?>"/>
                    <input type="hidden" name="amount[]" id="amount_<? echo $i; ?>" value="<? echo $row[csf('amount')]; ?>"/>
                </td>
				<!-- <td id="button_<? //echo $i; ?>" align="center" width="30">
                    <input type="button" id="decrease_<? //echo $i; ?>" name="decrease[]" style="width:30px" class="formbuttonplasminus" value="-" onClick="fn_deleteRow(<? //echo $i; ?>);" />
                </td> -->
			</tr>
			<?
		}
		?>
		</tbody>
		<tfoot>
			<tr class="tbl_bottom">
			<td colspan="8">Total</td>
			<td id="total_amount"><? echo number_format($total_ammount,4); $total_ammount = 0;?></td>
		</tr>
		</tfoot>
	</table>
	<?
	exit();
}

if ($action=="set_value_pi_select")
{
	$pi_value = return_field_value("sum(net_total_amount)","com_pi_master_details","id in($data) and status_active=1 and is_deleted=0");
	$nameArray=sql_select("SELECT id, pi_number, supplier_id, last_shipment_date, currency_id, import_pi, item_category_id from com_pi_master_details where id in($data)" );

	foreach ($nameArray as $inf)
	{
		if($inf[csf('import_pi')] == 1)
		{
			echo "load_drop_down( 'requires/commercial_office_note_controller',".$inf[csf('supplier_id')].", 'load_drop_down_importer', 'supplier_td');\n";
		}
		if($inf[csf("currency_id")]==1)
			$txt_pi_value=number_format($pi_value,$dec_place[4],'.','');
		else
			$txt_pi_value=number_format($pi_value,$dec_place[5],'.','');
		echo "document.getElementById('cbo_supplier_id').value = '".$inf[csf("supplier_id")]."';\n";
		echo "document.getElementById('txt_last_shipment_date').value = '".change_date_format($inf[csf("last_shipment_date")])."';\n";
		echo "document.getElementById('txt_pi_value').value = '".$txt_pi_value."';\n";
		echo "document.getElementById('txt_lc_value').value = '".$txt_pi_value."';\n";
		echo "document.getElementById('hidden_pi_currency_id').value = '".$inf[csf("currency_id")]."';\n";
		echo "document.getElementById('txt_hidden_item_category').value = '".$inf[csf("item_category_id")]."';\n";
	}
	exit();
}

if ($action=="save_update_delete")
{
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process )); 
	$cbo_importer_id=str_replace("'","",$cbo_importer_id);
	$cbo_lc_type_id=str_replace("'","",$cbo_lc_type_id);
	 
	if ($operation==0)  // Insert Here
	{
		$con = connect();
		if($db_type==0)
		{
			mysql_query("BEGIN");
		}

		if ($db_type==2) 
		{
			$last_shipment_date = date("d-M-Y", strtotime(str_replace("'", '',$txt_last_shipment_date)));
		}
		else{
			$last_shipment_date = date("Y-m-d", strtotime(str_replace("'", '',$txt_last_shipment_date)));
		}	


		$id=return_next_id("id", "commercial_office_note_mst", 1);

		$prefix="CON"; // Commercial Office Note (CON)
		if($db_type==0) $year_cond="YEAR(insert_date)";
		else if($db_type==2) $year_cond="to_char(insert_date,'YYYY')";
		else $year_cond="";//defined Later

		$new_con_system_id=explode("*",return_mrr_number( str_replace("'","",$cbo_importer_id), '', $prefix, date("Y",time()), 5, "select con_prefix,con_prefix_number from commercial_office_note_mst where importer_id=$cbo_importer_id and lc_type=$cbo_lc_type_id and $year_cond=".date('Y',time())." order by id desc ", "con_prefix", "con_prefix_number" ));

 		$field_array="id,con_prefix,con_prefix_number,con_system_id,item_category_id,importer_id,supplier_id,office_note_date,pi_number,pi_id,currency_id,tenor,remarks,total_amount,ready_to_approved,inserted_by,insert_date,lc_type,pi_entry_form,proposed_bank,section,exchange_rate,status_active,is_deleted";

		$data_array="(".$id.",'".$new_con_system_id[1]."',".$new_con_system_id[2].",'".$new_con_system_id[0]."',".$txt_hidden_item_category.",".$cbo_importer_id.",".$cbo_supplier_id.",".$txt_office_note_date.",".$txt_pi.",".$txt_hidden_pi_id.",".$hidden_pi_currency_id.",".$txt_tenor.",".$txt_remarks.",'".str_replace("'", '', $txt_pi_value)."',".$cbo_ready_to_approved.",".$user_id.",'".$pc_date_time."',".$cbo_lc_type_id.",".$txt_hidden_pi_item.",".$cbo_proposed_bank.",".$cbo_section.",".$txt_exchange_rate.","."1".","."0".")";

 		//echo "insert into commercial_office_note_mst (".$field_array.") values ".$data_array;die;

		$dtlsid=return_next_id("id", "commercial_office_note_dtls", 1);	

		$field_array_dtls="id, mst_id, pi_id, pi_number, pi_dtls_id, yarn_composition_item1, yarn_composition_percentage1, yarn_composition_item2, body_part_id, fab_type, fabric_construction, fab_design, fabric_composition, item_description, item_category_id, color_id, count_name, yarn_type, hs_code, uom, quantity, rate, amount, inserted_by, insert_date,status_active,is_deleted";

		//$field_array_update="pi_number*pi_id*updated_by*update_date";
		
		$data_array_dtls="";
		for($i=1;$i<=$tot_row;$i++)
		{
			if($i>1) $data_array_dtls .= ",";
			$piId 						= 'piId_'.$i;
			$piNumber 					= 'piNumber_'.$i;
			$pidtlsId					= 'pidtlsId_'.$i;
			$compositionItem1			= 'compositionItem1_'.$i;
			$compositionPercentage		= 'compositionPercentage_'.$i;
			$compositionItem2			= 'compositionItem2_'.$i;

			$bodyPartId			        = 'bodyPartId_'.$i;
			$fabType			        = 'fabType_'.$i;
			$fabric_construction		= 'fabric_construction_'.$i;
			$fab_design			        = 'fab_design_'.$i;
			$fabric_composition			= 'fabric_composition_'.$i;

			$itemDescription			= 'itemDescription_'.$i;
			$itemCategoryId				= 'itemCategoryId_'.$i;
			$colorId					= 'colorId_'.$i;
			$countName					= 'countName_'.$i;
			$yarnType					= 'yarnType_'.$i;
			$hsCode						= 'hsCode_'.$i;
			$uom 						= 'uom_'.$i;
			$quantity					= 'quantity_'.$i;
			$rate 						= 'rate_'.$i;
			$amount 					= 'amount_'.$i;

			$data_array_dtls .= "(".$dtlsid.",".$id.",".$$piId.",'".$$piNumber."',".$$pidtlsId.",'".$$compositionItem1."','".$$compositionPercentage."','".$$compositionItem2."','".$$bodyPartId."','".$$fabType."','".$$fabric_construction."','".$$fab_design."','".$$fabric_composition."','".$$itemDescription."','".$$itemCategoryId."','".$$colorId."','".$$countName."','".$$yarnType."','".$$hsCode."','".$$uom."','".$$quantity."','".$$rate."','".$$amount."','".$user_id."','".$pc_date_time."',".'1'.",".'0'.")";	
			$dtlsid=$dtlsid+1;			
		}
		//echo "insert into commercial_office_note_mst (".$field_array.") values ".$data_array;die;
		$rID=sql_insert("commercial_office_note_mst",$field_array,$data_array,1);
		
		//echo "insert into commercial_office_note_dtls (".$field_array_dtls.") values ".$data_array_dtls;die;
		if($data_array_dtls!="")
		{			
			$dtlsrID=sql_insert("commercial_office_note_dtls",$field_array_dtls,$data_array_dtls,1);
		}

		// echo "10**".$rID."##".$dtlsrID;die;

		if($db_type==0)
		{
			if($rID && $dtlsrID)
			{
				mysql_query("COMMIT");
				echo "0**".str_replace("'", '', $id)."**".$new_con_system_id[0];
			}
			else
			{
				mysql_query("ROLLBACK"); 
				//echo "10".$id;
				echo "5**"."0"."**".$new_con_system_id[0];
			}
		}
		
		if($db_type==2 || $db_type==1 )
		{
			if($rID && $dtlsrID)
			{
				oci_commit($con);
				echo "0**".str_replace("'", '', $id)."**".$new_con_system_id[0];
			}
			else
			{
				oci_rollback($con);
				//echo "10**".$id;
				echo "10**"."0"."**".$new_con_system_id[0];
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
		
		if(str_replace("'","",$update_id)=="") { echo "10**";die; }

 		$id=str_replace("'","",$update_id);

		$field_array_update="supplier_id*office_note_date*pi_number*pi_id*currency_id*lc_type*tenor*remarks*total_amount*ready_to_approved*pi_entry_form*proposed_bank*section*exchange_rate*updated_by*update_date";
		$data_array_update="".$cbo_supplier_id."*".$txt_office_note_date."*".$txt_pi."*".$txt_hidden_pi_id."*".$hidden_pi_currency_id."*".$cbo_lc_type_id."*".$txt_tenor."*".$txt_remarks."*".str_replace("'", '', $txt_pi_value)."*".$cbo_ready_to_approved."*".$txt_hidden_pi_item."*".$cbo_proposed_bank."*".$cbo_section."*".$txt_exchange_rate."*'".$user_id."'*'".$pc_date_time."'";

		//echo "20**".$field_array."<br>".$data_array;die;
 		
		$dtlsid=return_next_id("id", "commercial_office_note_dtls", 1);
		$field_array_update_dtls="id, mst_id, pi_id, pi_number, pi_dtls_id, yarn_composition_item1, yarn_composition_percentage1, yarn_composition_item2, body_part_id, fab_type, fabric_construction, fab_design, fabric_composition, item_description, item_category_id, color_id, count_name, yarn_type, hs_code, uom, quantity, rate, amount, updated_by,update_date,status_active,is_deleted";
		
		$data_array_update_dtls="";
		for($i=1;$i<=$tot_row;$i++)
		{
			if($i>1) $data_array_update_dtls .= ",";
			$piId 						= 'piId_'.$i;
			$piNumber 					= 'piNumber_'.$i;
			$pidtlsId					= 'pidtlsId_'.$i;
			$compositionItem1			= 'compositionItem1_'.$i;
			$compositionPercentage		= 'compositionPercentage_'.$i;
			$compositionItem2			= 'compositionItem2_'.$i;

			$bodyPartId			        = 'bodyPartId_'.$i;
			$fabType			        = 'fabType_'.$i;
			$fabric_construction		= 'fabric_construction_'.$i;
			$fab_design			        = 'fab_design_'.$i;
			$fabric_composition			= 'fabric_composition_'.$i;

			$itemDescription			= 'itemDescription_'.$i;
			$itemCategoryId				= 'itemCategoryId_'.$i;
			$colorId					= 'colorId_'.$i;
			$countName					= 'countName_'.$i;
			$yarnType					= 'yarnType_'.$i;
			$hsCode						= 'hsCode_'.$i;
			$uom 						= 'uom_'.$i;
			$quantity					= 'quantity_'.$i;
			$rate 						= 'rate_'.$i;
			$amount 					= 'amount_'.$i;

  			$data_array_update_dtls .= "(".$dtlsid.",".$id.",".$$piId.",'".$$piNumber."',".$$pidtlsId.",'".$$compositionItem1."','".$$compositionPercentage."','".$$compositionItem2."','".$$bodyPartId."','".$$fabType."','".$$fabric_construction."','".$$fab_design."','".$$fabric_composition."','".$$itemDescription."','".$$itemCategoryId."','".$$colorId."','".$$countName."','".$$yarnType."','".$$hsCode."','".$$uom."','".$$quantity."','".$$rate."','".$$amount."','".$user_id."','".$pc_date_time."',".'1'.",".'0'.")";	
			$dtlsid=$dtlsid+1;
		}

		$deleteDtls = execute_query("DELETE FROM commercial_office_note_dtls WHERE mst_id=$update_id");
	
		/*$field_array_status="updated_by*update_date*status_active*is_deleted";
		$data_array_status=$user_id."*'".$pc_date_time."'*0*1";

		$rID2=sql_multirow_update("commercial_office_note_dtls",$field_array_status,$data_array_status,"mst_id",$update_id,0);
		if($flag==1)
		{
			if($rID2) $flag=1; else $flag=0;
		}*/

		$dtlsrID=true;
		if($data_array_update_dtls!="")
		{
			//echo "insert into commercial_office_note_dtls (".$field_array_update_dtls.") values ".$data_array_update_dtls;die;
			$dtlsrID=sql_insert("commercial_office_note_dtls",$field_array_update_dtls,$data_array_update_dtls,1);
		}

		// echo sql_update2("commercial_office_note_mst",$field_array,$data_array,"id","".$update_id."",1);die;
		$rID=sql_update("commercial_office_note_mst",$field_array_update,$data_array_update,"id",$update_id,1);
		
		
		//echo "20**".$rID."&&".$deleteDtls."&&".$dtlsrID;die;

		if($db_type==0)
		{
			if($rID && $deleteDtls && $dtlsrID)
			{
				mysql_query("COMMIT");  
				echo "1**".str_replace("'", '', $id)."**".str_replace("'", '', $txt_system_id);
			}
			else
			{
				mysql_query("ROLLBACK"); 
				echo "10**".$id;
			}
		}
		
		if($db_type==2 || $db_type==1 )
		{
			if($rID && $deleteDtls && $dtlsrID)
			{
				oci_commit($con);
				echo "1**".str_replace("'", '', $id)."**".str_replace("'", '', $txt_system_id);
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
	
	else if ($operation==2) // Delete Here
	{
		echo "20**"."Delete Restricted";die;

		$con = connect();
		if($db_type==0)
		{
			mysql_query("BEGIN");
		}

		$field_array="updated_by*update_date*status_active*is_deleted";
	    $data_array="".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'*'0'*'1'";

		$rID=sql_delete("com_btb_lc_master_details",$field_array,$data_array,"id","".$update_id."",1);
		$delete=execute_query( "delete from com_btb_lc_pi where com_btb_lc_master_details_id = $update_id",0);

		if($db_type==0)
		{
			if($rID && $delete)
			{
				mysql_query("COMMIT");
				echo "2**".$rID;
			}
			else
			{
				mysql_query("ROLLBACK");
				echo "7**".$rID;
			}
		}
		else if($db_type==2 || $db_type==1 )
		{
			if($rID && $delete)
			{
				oci_commit($con);
				echo "2**".$rID;
			}
			else
			{
				oci_rollback($con);
				echo "7**".$rID;
			}
		}

		disconnect($con);
	}
}

if($action=="system_id_pop") // System id popup
{
  	echo load_html_head_contents("Popup Info","../../../", 1, 1, $unicode);
	extract($_REQUEST);  
	?>
	<script>
		function js_set_value(mrr)
		{
			$("#hidden_system_number").val(mrr); // mrr number
			parent.emailwindow.hide();
		}
	</script>
	</head>
	<body>
		<div align="center" style="width:100%;" >
			<form name="searchdocfrm_1"  id="searchdocfrm_1" autocomplete="off">
				<table width="740" cellspacing="0" cellpadding="0" border="1" class="rpt_table" align="center" rules="all">
					<thead>
						<tr>
							<th width="140">System ID</th>
							<th width="180">LC Type</th>
							<th width="200">Date Range</th>
							<th><input type="reset" name="re_button" id="re_button" value="Reset" style="width:100px" class="formbutton"  /></th>
						</tr>
					</thead>
					<tbody>
						<tr>
							<td align="center"> <input type="text" id="txt_sys_no" name="txt_sys_no" class="text_boxes" style="width:100px;" > </td>
							<td align="center">
								<?
								echo create_drop_down( "cbo_lc_type_id",150,$lc_type,'',1,'-Select',1,"",0);
								?>
							</td>
							<td align="center">
								<input name="txt_date_from" id="txt_date_from" class="datepicker" style="width:70px" placeholder="From Date" />
								<input name="txt_date_to" id="txt_date_to" class="datepicker" style="width:70px" placeholder="To Date" />
							</td>
							<td align="center">
								<input type="button" name="btn_show" class="formbutton" value="Show" onClick="show_list_view ( document.getElementById('txt_sys_no').value+'_'+document.getElementById('cbo_lc_type_id').value+'_'+document.getElementById('txt_date_from').value+'_'+document.getElementById('txt_date_to').value+'_'+<? echo $importer_name; ?>+'_'+document.getElementById('cbo_year_selection').value, 'create_system_id_search_list_view', 'search_div', 'commercial_office_note_controller', 'setFilterGrid(\'list_view\',-1)')" style="width:100px;" />
							</td>
						</tr>
						<tr>
							<td align="center" height="40" valign="middle" colspan="5">
								<? echo load_month_buttons(1);  ?>
								<!-- Hidden field here-->
								<input type="hidden" id="hidden_system_number" value="" />
								<!--END-->
							</td>
						</tr>
					</tbody>
				</table>
				<div align="center" valign="top" id="search_div" style="margin-top:5px"> </div>
			</form>
		</div>
	</body>
	<script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
	</html>
	<?
}

if($action=="create_system_id_search_list_view") // System id popup list view
{
	$ex_data = explode("_",$data);
	$sys_id_no = $ex_data[0];
	$cbo_lc_type_id = $ex_data[1];
	$txt_date_from = $ex_data[2];
	$txt_date_to = $ex_data[3];
	$importer_name = $ex_data[4];
	$cbo_year = $ex_data[5];

	//echo $invoice_num."##".$sys_id_no."##".$cbo_lc_type_id."##".$txt_date_from."##".$txt_date_to."##".$importer_name;die;
	$buyer_library=return_library_array( "select id, buyer_name from lib_buyer", "id", "buyer_name"  );

	$sql_cond=""; 
 	if( $txt_date_from!="" || $txt_date_to!="" )
	{
		if($db_type==0)
		{
			$txt_date_from=change_date_format($txt_date_from,'yyyy-mm-dd');
			$txt_date_to=change_date_format($txt_date_to,'yyyy-mm-dd');
		}
		else if($db_type==2)
		{
			$txt_date_from=change_date_format($txt_date_from,'','',-1);
			$txt_date_to=change_date_format($txt_date_to,'','',-1);
		}
		$sql_cond = " and a.office_note_date between '".$txt_date_from."' and '".$txt_date_to."'";
	}
	if(trim($importer_name)>0) $sql_cond .= " and a.importer_id ='$importer_name'";
	if($sys_id_no!="")  $sys_cond=" and a.con_system_id like '%$sys_id_no%'";
	$lc_type_cond='';
	if($cbo_lc_type_id!=0)  $lc_type_cond=" and a.lc_type=$cbo_lc_type_id";

	/*if ($db_type==0) $year_field="YEAR(a.insert_date) as year"; 
	else if ($db_type==2) $year_field="to_char(a.insert_date,'YYYY') as year";*/
	$year_cond='';
	if($db_type==0) $year_cond=" and year(a.insert_date)=".trim($cbo_year); 
	else $year_cond=" and to_char(a.insert_date,'YYYY')=".trim($cbo_year);
	
	$supplier_lib=return_library_array( "select id, supplier_name from lib_supplier",'id','supplier_name');
	
	$sql="SELECT a.id, a.con_prefix_number, a.pi_id, a.pi_number, a.office_note_date, a.supplier_id, a.lc_type, a.ready_to_approved, a.tenor, a.remarks, a.total_amount 
	FROM commercial_office_note_mst a WHERE a.status_active=1 $sql_cond $sys_cond $lc_type_cond $year_cond";
	
	//echo $sql;die;
	$res = sql_select($sql);
    ?>
    <div style="width:718px;">
        <table border="0" width="100%" cellpadding="0" cellspacing="0" class="rpt_table" rules="all">
            <thead>
                <th width="30">SL</th>
                <th width="80">System ID</th>
                <th width="150">PI No</th>
                <th width="170">Supplier</th>
                <th width="80">Office Note Date</th>               
                <th width="100">LC Type</th>
                <th >Value</th>
            </thead>
        </table>
    </div>            
    <div style="width:718px; overflow-y:scroll; max-height:230px">
        <table border="0" width="700" cellpadding="0" cellspacing="0" class="rpt_table" rules="all" id="list_view">             
   		<?                     
        	$i=1;
			foreach($res as $row)
			{  
				
				if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF"; 
          		?>     
			   		<tr bgcolor="<? echo $bgcolor; ?>" style="text-decoration:none; cursor:pointer" onClick="js_set_value(<? echo $row[csf("id")];?>)" > 
                        <td width="30" align="center"><? echo $i;?></td>
                        <td width="80" align="center"><p><? echo $row[csf("con_prefix_number")];?></p></td>               
                        <td width="150" title="<? echo $row[csf("pi_id")]; ?>"><p><? echo $row[csf("pi_number")]; ?></p></td>
                        <td width="170"><p><? echo $supplier_lib[$row[csf("supplier_id")]]; ?></p></td>
                        <td width="80" align="center"><p><? echo change_date_format($row[csf("office_note_date")]); ?></p></td>
                        <td width="100"><p><? echo $lc_type[$row[csf("lc_type")]]; ?></p></td>
                        <td align="right"><p><? echo $row[csf("total_amount")]; ?></p></td>
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

if($action=="populate_master_from_data") // master data populate
{  
	//print_r($_REQUEST);
	$mst_sql="SELECT a.id, a.con_system_id, a.importer_id, a.office_note_date, a.lc_type, a.pi_id, a.pi_number, a.ready_to_approved, a.tenor, a.remarks, a.total_amount, a.is_approved, a.currency_id, a.item_category_id, a.pi_entry_form, a.proposed_bank, a.section, a.exchange_rate FROM commercial_office_note_mst a WHERE a.id=$data"; 
	$mst_data_array=sql_select($mst_sql);
	foreach($mst_data_array as $row)
	{		
  		echo "$('#update_id').val('".$row[csf("id")]."');\n";
  		echo "$('#txt_system_id').val('".$row[csf("con_system_id")]."');\n";
  		echo "$('#cbo_importer_id').val('".$row[csf("importer_id")]."');\n";
  		echo "$('#txt_office_note_date').val('".change_date_format($row[csf("office_note_date")])."');\n";
  		echo "$('#cbo_lc_type_id').val('".$row[csf("lc_type")]."');\n";
  		echo "$('#txt_pi').val('".$row[csf("pi_number")]."');\n";
  		echo "$('#txt_hidden_pi_id').val('".$row[csf("pi_id")]."');\n";
  		echo "$('#cbo_ready_to_approved').val('".$row[csf("ready_to_approved")]."');\n";
  		echo "$('#txt_tenor').val('".$row[csf("tenor")]."');\n";
  		echo "$('#txt_remarks').val('".$row[csf("remarks")]."');\n";
  		echo "$('#txt_pi_value').val('".$row[csf("total_amount")]."');\n";
  		echo "$('#hidden_pi_currency_id').val('".$row[csf("currency_id")]."');\n";
  		echo "$('#txt_hidden_item_category').val('".$row[csf("item_category_id")]."');\n";
  		echo "$('#txt_hidden_pi_item').val('".$row[csf("pi_entry_form")]."');\n";
  		echo "$('#cbo_proposed_bank').val('".$row[csf("proposed_bank")]."');\n";
  		echo "$('#txt_exchange_rate').val('".$row[csf("section")]."');\n";
  		echo "$('#cbo_section').val('".$row[csf("exchange_rate")]."');\n";
  		echo "$('#cbo_importer_id').attr('disabled',true);\n";

  		echo "$('#is_approved').val(".$row[csf("is_approved")].");\n";
		if($row[csf("is_approved")] == 1)	
		{
			echo "$('#approved').text('Approved');\n";
			echo "$('#txt_pi').attr('disabled','true')".";\n";
		}
		elseif($row[csf("is_approved")] == 3)	
		{
			echo "$('#approved').text('Partial Approved');\n";
			echo "$('#txt_pi').attr('disabled','true')".";\n";
		}
		else
		{
			echo "$('#approved').text('');\n";
			echo "$('#txt_pi').prop('disabled', false)".";\n";
	  	}
	}
		
	exit();	
}

if($action=="print") // Print
{

	$data = explode('**',$data);
	//$path = $data[3];  // This parameter used Commercial Office Note Approval Page
	//echo $path.'system';
	echo load_html_head_contents($data[3],"../../", 1, 1, $unicode,'','');
	$cbo_template_id=$data[3];
	if($cbo_template_id==1) $align_cond='center'; else $align_cond='right';
	$user_lib_name=return_library_array("SELECT id,user_full_name from user_passwd", "id", "user_full_name");

	if ($data[3])
	{
		if ($data[2] == 1) echo '<style>body { background-image: url("../img/approved.gif"); } </style>';
		else echo '<style>body { background-image: url("../img/draft.gif"); } </style>';
		echo '<link href="../css/style_common.css" rel="stylesheet" type="text/css" />';
	}
	else
	{
		if ($data[2] == 1) echo '<style>body { background-image: url("../../img/approved.gif"); } </style>';
		else echo '<style>body { background-image: url("../../img/draft.gif"); } </style>';
		echo '<link href="../../css/style_common.css" rel="stylesheet" type="text/css" />';
	}	
	?>
	
	<div style="width:1000px">
		<?
		$supplier_lib=return_library_array( "select id, supplier_name from lib_supplier",'id','supplier_name');
		$item_group_name_arr=return_library_array( "select id,item_name FROM lib_item_group",'id','item_name');
		$lib_body_part_arr=return_library_array("select id, body_part_full_name from lib_body_part", "id", "body_part_full_name");
		$buyer_lib=return_library_array( "SELECT buy.id ,buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company=$data[0] 
		and buy.id in (select buyer_id from lib_buyer_party_type where party_type in (1,3,21,90)) group by buy.id,buy.buyer_name order by buyer_name",'id','buyer_name');

		$btb_limit_lib=return_library_array("SELECT company_name, max_btb_limit from variable_settings_commercial WHERE company_name=$data[0] and variable_list=6 and max_btb_limit_hcode='Max BTB Limit' and status_active=1 and is_deleted=0",'company_name','max_btb_limit');

		$image_location=return_field_value("image_location","common_photo_library","file_type=1 and form_name='company_details' and master_tble_id='$data[0]'","image_location");
		$com_sql=sql_select("SELECT a.id, a.company_name, a.city from lib_company a where a.id=$data[0]");
		$company_name=$com_sql[0][csf("company_name")];
		$location_name=$com_sql[0][csf("city")];

		// =========================================================================
		$sql="SELECT a.id, a.con_system_id, a.is_approved, a.lc_type, a.office_note_date, a.importer_id, a.tenor, a.inserted_by, b.yarn_composition_percentage1, b.yarn_composition_percentage2, b.yarn_composition_item1, b.yarn_composition_item2, b.body_part_id, b.fab_type, b.fabric_construction, b.fab_design, b.fabric_composition, b.item_description, d.internal_file_no, d.supplier_id, d.item_category_id, d.total_amount, d.remarks, d.pi_number, d.id as pi_id, d.pi_date, b.quantity, b.rate as pi_rate, b.amount as pi_amount, c.id as pi_dtls_id, c.work_order_no, c.work_order_dtls_id, c.item_group, d.file_year
		FROM commercial_office_note_mst a, commercial_office_note_dtls b, com_pi_item_details c, com_pi_master_details d  
		WHERE a.id=b.mst_id and b.pi_dtls_id=c.id and c.pi_id=d.id and b.mst_id=$data[1] and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and d.status_active=1 and d.is_deleted=0
		group by a.id, a.con_system_id, a.is_approved, a.lc_type, a.office_note_date, a.importer_id, a.tenor, a.inserted_by, b.yarn_composition_percentage1, b.yarn_composition_percentage2, b.yarn_composition_item1, b.yarn_composition_item2, b.body_part_id, b.fab_type, b.fabric_construction, b.fab_design, b.fabric_composition, b.item_description, d.internal_file_no, d.supplier_id, d.id, d.item_category_id, d.total_amount, d.remarks, d.pi_number, d.pi_date, b.quantity, b.rate, b.amount, c.id, c.work_order_no, c.work_order_dtls_id, c.item_group, d.file_year
		order by d.id";
		$data_arr=sql_select($sql);
		
		/*$data_arr=sql_select("SELECT a.id, a.con_system_id, a.is_approved, a.lc_type, a.office_note_date, a.importer_id, c.internal_file_no, c.supplier_id, c.item_category_id, c.total_amount, c.remarks, a.pi_id, c.pi_number, c.pi_date
		FROM commercial_office_note_mst a, commercial_office_note_dtls b, com_pi_master_details c 
		WHERE a.id=b.mst_id and b.pi_id=c.id and b.mst_id=$data[1] and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 
		group by a.id, a.con_system_id, a.is_approved, a.lc_type, a.office_note_date, a.importer_id, c.internal_file_no, c.supplier_id, c.item_category_id, c.total_amount, c.remarks, a.pi_id, c.pi_number, c.pi_date");*/

		$main_data_arr=array(); $lc_type_arr=array(); $note_date_arr=array(); $$rowspan_arr=array(); 
		$file_no_arr=array(); $work_order_arr=array(); $amount_data_arr=array(); $file_no_year=array();
		foreach($data_arr as $row)
		{
			$main_data_arr[$row[csf('internal_file_no')]][$row[csf('pi_id')]]['lc_type']=$row[csf('lc_type')];
			$main_data_arr[$row[csf('internal_file_no')]][$row[csf('pi_id')]]['office_note_date']=$row[csf('office_note_date')];
			$main_data_arr[$row[csf('internal_file_no')]][$row[csf('pi_id')]]['tenor']=$row[csf('tenor')];
			$main_data_arr[$row[csf('internal_file_no')]][$row[csf('pi_id')]]['supplier_id']=$row[csf('supplier_id')];				
			$main_data_arr[$row[csf('internal_file_no')]][$row[csf('pi_id')]]['pi_number']=$row[csf('pi_number')];				
			$main_data_arr[$row[csf('internal_file_no')]][$row[csf('pi_id')]]['item_category_id']=$row[csf('item_category_id')];
			$main_data_arr[$row[csf('internal_file_no')]][$row[csf('pi_id')]]['total_amount']=$row[csf('total_amount')];
			$main_data_arr[$row[csf('internal_file_no')]][$row[csf('pi_id')]]['pi_date']=$row[csf('pi_date')];
			$main_data_arr[$row[csf('internal_file_no')]][$row[csf('pi_id')]]['remarks']=$row[csf('remarks')];
			$main_data_arr[$row[csf('internal_file_no')]][$row[csf('pi_id')]]['importer_id']=$row[csf('importer_id')];
			$main_data_arr[$row[csf('internal_file_no')]][$row[csf('pi_id')]]['pi_id']=$row[csf('pi_id')];
			
			/*$office_note_data_arr[$row[csf('internal_file_no')]][$row[csf('pi_id')]][$row[csf('pi_dtls_id')]]['quantity']=$row[csf('quantity')];
			$office_note_data_arr[$row[csf('internal_file_no')]][$row[csf('pi_id')]][$row[csf('pi_dtls_id')]]['pi_rate']=$row[csf('pi_rate')];
			$office_note_data_arr[$row[csf('internal_file_no')]][$row[csf('pi_id')]][$row[csf('pi_dtls_id')]]['pi_amount']=$row[csf('pi_amount')];*/
			$inserted_by=$row[csf('inserted_by')];

			if ($row[csf('item_category_id')]==1)
			{
				if ($row[csf('yarn_composition_percentage1')]!=0) {$composition_percentage1 = $row[csf('yarn_composition_percentage1')]."%";}
				if ($row[csf('yarn_composition_percentage2')]!=0) {$composition_percentage2 = $row[csf('yarn_composition_percentage2')]."%";}
				$item_description = $composition[$row[csf('yarn_composition_item1')]].' '.$composition[$row[csf('yarn_composition_item2')]].' '.$composition_percentage1.' '.$composition_percentage2;
				$office_note_data_arr[$row[csf('internal_file_no')]][$row[csf('pi_id')]][$item_description][$row[csf('pi_rate')]]['item_description']=$item_description;
				$office_note_data_arr[$row[csf('internal_file_no')]][$row[csf('pi_id')]][$item_description][$row[csf('pi_rate')]]['pi_rate']=$row[csf('pi_rate')];
				$office_note_data_arr[$row[csf('internal_file_no')]][$row[csf('pi_id')]][$item_description][$row[csf('pi_rate')]]['pi_amount']+=$row[csf('pi_amount')];
				$office_note_data_arr[$row[csf('internal_file_no')]][$row[csf('pi_id')]][$item_description][$row[csf('pi_rate')]]['quantity']+=$row[csf('quantity')];
				$work_order_dtls_id.=$row[csf('work_order_dtls_id')].',';
			}
			else if ($row[csf('item_category_id')]==2)
			{
				$item_description = $row[csf('fabric_construction')]." ".$row[csf('fabric_composition')];
				$office_note_data_arr[$row[csf('internal_file_no')]][$row[csf('pi_id')]][$item_description][$row[csf('pi_rate')]]['item_description']=$item_description;
				$office_note_data_arr[$row[csf('internal_file_no')]][$row[csf('pi_id')]][$item_description][$row[csf('pi_rate')]]['pi_rate']=$row[csf('pi_rate')];
				$office_note_data_arr[$row[csf('internal_file_no')]][$row[csf('pi_id')]][$item_description][$row[csf('pi_rate')]]['pi_amount']+=$row[csf('pi_amount')];
				$office_note_data_arr[$row[csf('internal_file_no')]][$row[csf('pi_id')]][$item_description][$row[csf('pi_rate')]]['quantity']+=$row[csf('quantity')];
				$main_data_arr[$row[csf('internal_file_no')]][$row[csf('pi_id')]]['work_order_no'].=$row[csf('work_order_no')].',';
			}
			else if ($row[csf('item_category_id')]==3)
			{
				$item_description = $lib_body_part_arr[$row[csf('body_part_id')]]." ".$row[csf('fab_type')]." ".$row[csf('fabric_construction')]." ".$row[csf('fab_design')]." ".$row[csf('fabric_composition')];
				$office_note_data_arr[$row[csf('internal_file_no')]][$row[csf('pi_id')]][$item_description][$row[csf('pi_rate')]]['item_description']=$item_description;
				$office_note_data_arr[$row[csf('internal_file_no')]][$row[csf('pi_id')]][$item_description][$row[csf('pi_rate')]]['pi_rate']=$row[csf('pi_rate')];
				$office_note_data_arr[$row[csf('internal_file_no')]][$row[csf('pi_id')]][$item_description][$row[csf('pi_rate')]]['pi_amount']+=$row[csf('pi_amount')];
				$office_note_data_arr[$row[csf('internal_file_no')]][$row[csf('pi_id')]][$item_description][$row[csf('pi_rate')]]['quantity']+=$row[csf('quantity')];
				$main_data_arr[$row[csf('internal_file_no')]][$row[csf('pi_id')]]['work_order_no'].=$row[csf('work_order_no')].',';
			}
			else
			{
				$item_description = $row[csf('item_description')];
				$office_note_data_arr[$row[csf('internal_file_no')]][$row[csf('pi_id')]][$item_description][$row[csf('pi_rate')]]['item_description']=$item_description;
				$office_note_data_arr[$row[csf('internal_file_no')]][$row[csf('pi_id')]][$item_description][$row[csf('pi_rate')]]['pi_rate']=$row[csf('pi_rate')];
				$office_note_data_arr[$row[csf('internal_file_no')]][$row[csf('pi_id')]][$item_description][$row[csf('pi_rate')]]['pi_amount']+=$row[csf('pi_amount')];
				$office_note_data_arr[$row[csf('internal_file_no')]][$row[csf('pi_id')]][$item_description][$row[csf('pi_rate')]]['quantity']+=$row[csf('quantity')];
				$main_data_arr[$row[csf('internal_file_no')]][$row[csf('pi_id')]]['work_order_no'].=$row[csf('work_order_no')].',';
			}

			$lc_type_arr[$row[csf('lc_type')]]=$row[csf('lc_type')];
			$note_date_arr[$row[csf('office_note_date')]]=$row[csf('office_note_date')];

			$internal_file_no="'".$row[csf('internal_file_no')]."'";
			$file_no_arr[$row[csf('internal_file_no')]]=$internal_file_no;
			if ($row[csf('file_year')] != '')
			{
				$file_year="'".$row[csf('file_year')]."'";
				$file_no_year[$row[csf('file_year')]]=$file_year;
			}			
			
			$pi_id_arr[$row[csf('pi_id')]]=$row[csf('pi_id')];			

			$amount_data_arr[$row[csf('internal_file_no')]]+=$row[csf('total_amount')];
		}
		//echo $work_order_dtls_id;
		/*echo "<pre>";
		print_r($rowspan_arr);*/
		$all_pi_id = implode(',',array_unique(explode(',',implode(",",$pi_id_arr))));//chop(implode(",",$pi_id_arr),',');
		$all_file_no = implode(',',array_unique(explode(',',implode(",",$file_no_arr))));
		$all_file_no_year = implode(',',array_unique(explode(',',implode(",",$file_no_year))));

		if ($all_file_no!="") 
		{
			$file_no_cond = " and internal_file_no in($all_file_no)";
			$file_no_lcyear_cond = " and lc_year in($all_file_no_year)";
			$file_no_scyear_cond = " and sc_year in($all_file_no_year)";
		}
		//echo $file_no_lcyear_cond;

		$booking_no_arr=array();
		$sql_booking="select a.internal_file_no, a.id as pi_id, d.job_no as requ_job_no, d.booking_no as requ_booking_no, e.basis, f.booking_no, f.job_no from com_pi_master_details a, com_pi_item_details b, wo_non_order_info_dtls c, inv_purchase_requisition_dtls d, inv_purchase_requisition_mst e, wo_booking_dtls f where a.id=b.pi_id and b.work_order_dtls_id=c.id and c.requisition_dtls_id=d.id and d.mst_id= e.id and d.job_no=f.job_no and a.id in($all_pi_id) and e.basis in(1,5) and a.item_category_id=1 and a.status_active=1 and b.status_active=1 and c.status_active=1 and d.status_active=1 and e.status_active=1 group by a.internal_file_no, a.id, d.job_no, d.booking_no, e.basis, f.booking_no, f.job_no
			union all
			select a.internal_file_no, a.id as pi_id, null as requ_job_no, null as requ_booking_no, null as basis, f.booking_no, f.job_no 
			from com_pi_master_details a, com_pi_item_details b, wo_booking_dtls f 
			where a.id=b.pi_id and b.work_order_no=f.booking_no and a.id in($all_pi_id) and a.status_active=1 and b.status_active=1 and f.status_active=1  group by a.internal_file_no, a.id, f.booking_no, f.job_no";
		$sql_booking_res=sql_select($sql_booking);
		foreach ($sql_booking_res as $val) {
			if ($val[csf('item_category_id')] == 1)
			{
				if ($val[csf('basis')] == 1) {
					$booking_no_arr[$val[csf('internal_file_no')]][$val[csf('pi_id')]]['booking_no'].=$val[csf('requ_booking_no')].',';
					$booking_no_arr[$val[csf('internal_file_no')]][$val[csf('pi_id')]]['job_no'].=$val[csf('job_no')].',';
				}
				else 
				{
					$booking_no_arr[$val[csf('internal_file_no')]][$val[csf('pi_id')]]['booking_no'].=$val[csf('booking_no')].',';
					$booking_no_arr[$val[csf('internal_file_no')]][$val[csf('pi_id')]]['job_no'].=$val[csf('job_no')].',';
				}
			}
			else
			{
				$booking_no_arr[$val[csf('internal_file_no')]][$val[csf('pi_id')]]['booking_no'].=$val[csf('booking_no')].',';
				$booking_no_arr[$val[csf('internal_file_no')]][$val[csf('pi_id')]]['job_no'].=$val[csf('job_no')].',';
			}	
		}
		//echo '<pre>';print_r($booking_no_arr);

		// ==================================BTB/Margin LC===================================

		
		if ($all_pi_id!="") 
		{
			$pi_id_cond = " and pi_id ='$all_pi_id'";
		
			$lc_val_sql="SELECT pi_id, lc_value from com_btb_lc_master_details where importer_id=$data[0] $pi_id_cond and status_active=1 and is_deleted=0";
			//echo $lc_val_sql;
			$lc_val_data_arr=sql_select($lc_val_sql);
			$lc_value_arr=array();
			foreach ($lc_val_data_arr as $key => $value) 
			{
				$lc_value_arr[$value[csf('pi_id')]]=$value[csf('lc_value')];
			}
		}

		// ===============================Export Lc==============================================

		/*$elc_Sql="SELECT a.internal_file_no, b.replaced_amount 
		from com_export_lc a, com_export_lc_atch_sc_info b
		where a.id=b.com_export_lc_id and a.beneficiary_name=$data[0] $file_no_cond and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0";
		//echo $elc_Sql;
		$elc_data_arr=sql_select($elc_Sql);
		$replaced_amount_data_arr=array();
		foreach ($elc_data_arr as $key => $value) 
		{
			$replaced_amount_data_arr[$value[csf('internal_file_no')]]=$value[csf('replaced_amount')];
		}*/
		/*echo "<pre>";
		print_r($replaced_amount_data_arr);die;*/

		// ===============================Export Lc value==============================================

		$lc_val_Sql="SELECT a.internal_file_no, a.lc_value, a.buyer_name, a.replacement_lc from com_export_lc a
        where beneficiary_name=$data[0] $file_no_cond $file_no_lcyear_cond and a.status_active=1 and a.is_deleted=0";
		//echo $lc_val_Sql;
		$lc_val_data_arr=sql_select($lc_val_Sql);
		$lc_direct_arr=array(); $lc_replace_arr=array();
		foreach ($lc_val_data_arr as $key => $value) 
		{			
			if ($value[csf('replacement_lc')]==2) // Replacement LC = No (Direct.lc)
			{
				$lc_direct_arr[$value[csf('internal_file_no')]]+=$value[csf('lc_value')];
			}
			if ($value[csf('replacement_lc')]==1) // Replacement LC Yes
			{
				$lc_replace_arr[$value[csf('internal_file_no')]]+=$value[csf('lc_value')];
			}

			$buyer_data_arr[$value[csf('internal_file_no')]]=$value[csf('buyer_name')];
		}
		/*echo "<pre>";
		print_r($replace_lc_val);die;*/

		// ================================Sales Contract value and buyer=========================

		/* File Details Formula
		1	SC[Finance] = Data will come from-> Sales Contract Entry -> Convertible To: Finance
		2	SC [Replace] = Data will come from-> Sales Contract Entry -> Convertible To: LC/SC
		3	SC Replace Bal. = 1-2
		4	SC [Direct]	= Data will come from-> Sales Contract Entry -> Convertible To: No
		5	LC [Replace] = Data will come from-> Export LC Entry -> Replacement LC : Yes
		6	LC Replace Bal. = 2-5
		7	LC Direct = Data will come from-> Export LC Entry -> Replacement LC : No
		8	File Value  = 1+if minus value of SC Replace Bal then add absolute value of SC Replace Bal+4+7
		*/

		
		$scSql="SELECT internal_file_no, buyer_name, contract_value, convertible_to_lc from com_sales_contract where beneficiary_name=$data[0] $file_no_cond $file_no_scyear_cond and status_active=1 and is_deleted=0";
		
		$sc_data_arr=sql_select($scSql);
		$lcsc_finance_value_arr=array(); $sc_replace_value_arr=array(); $sc_direct_value_arr=array(); 
		foreach ($sc_data_arr as $key => $value) 
		{
			if ($value[csf('convertible_to_lc')]==3) // Convertible to = Finance
			{
				$lcsc_finance_value_arr[$value[csf('internal_file_no')]]+=$value[csf('contract_value')];
			}
			if ($value[csf('convertible_to_lc')]==1) // Convertible to = LC/SC
			{
				$sc_replace_value_arr[$value[csf('internal_file_no')]]+=$value[csf('contract_value')];
			}
			if ($value[csf('convertible_to_lc')]==2) // Convertible to = No (SC [Direct])
			{
				$sc_direct_value_arr[$value[csf('internal_file_no')]]+=$value[csf('contract_value')];
			}

			$buyer_data_arr[$value[csf('internal_file_no')]]=$value[csf('buyer_name')];
		}
		/*echo "<pre>";
		print_r($$approved_date_arr);die;*/

		// ==================================Approval==========================================
		
		$app_sql="SELECT b.mst_id, b.approved_no, b.approved_date from commercial_office_note_mst a, approval_history b where a.id=b.mst_id and a.id=$data[1] and a.is_approved=1 and b.current_approval_status=1";
		$app_data_arr=sql_select($app_sql);
		$approved_no_arr=array(); $approved_date_arr=array();
		foreach ($app_data_arr as $key => $value) 
		{
			$approved_no_arr[$value[csf('mst_id')]]=$value[csf('approved_no')];
			$approved_date_arr[$value[csf('mst_id')]]=$value[csf('approved_date')];
		}
        //echo "<pre>";print_r($approved_date_arr);
		// =============================================================================
		?>
		<style type="text/css">			
			.parent {
			  display: flex;
			  flex-direction:row;
			}

			.column {
			  flex: 1 1 0px;
			}
			.alignment {
			  vertical-align: middle;
			  text-align: center;
			}
			.verticalalign {
			  vertical-align: middle;
			}
		</style>
		<div style="width:1000px; margin-left:10px; height: 80px;" class="parent">
			<div width="250">
				<table width="250" cellspacing="0" align="center">
					<tr>
						<td rowspan="3" width="70">
						<img src="../../<? echo $image_location; ?>" height="70" width="200"></td>
					</tr>
				</table>
			</div>
			<div width="250" class="column">
				<table width="300" cellspacing="0" align="left">
					<tr>
						<td colspan="2" style="font-size:x-large;" align="left"><strong><? echo $company_name; ?></strong></td>
					</tr>
					<tr class="form_caption">
						<td colspan="2" align="left" style="font-size:14px"><? echo $location_name; //.",".$address; ?></td>
					</tr>
				</table>
			</div>
			<div width="250" class="column" align="right">
				<table width="300" cellspacing="1" align="right" class="rpt_table" rules="all">
					<tr>
						<td colspan="2" align="left"><strong>Office Note Date</strong></td>
						<td>&nbsp<? echo change_date_format(implode("",$note_date_arr)); ?></td>
					</tr>
					<tr>
						<td colspan="2" align="left"><strong>Office Note No.</strong></td>
						<td>&nbsp<? echo $data_arr[0][CON_SYSTEM_ID]; ?></td>
					</tr>
					<tr>
						<td colspan="2" align="left"><strong>Approval No.</strong></td>
						<td>&nbsp<? echo $approved_no_arr[$data[1]]; ?></td>
					</tr>
					<tr>
						<td colspan="2" align="left"><strong>Last App. Date & Time</strong></td>
						<td>&nbsp<? $dateFormate=strtotime($approved_date_arr[$data[1]]);
						if ($dateFormate!="") 
						{
							echo date("d-M-Y, h:i:s a", $dateFormate);
						}?></td>
					</tr>
				</table>
			</div>
      	</div>

		<br>
        <div style="width:1250px; margin-left:10px">
          	<div class="parent"><h2>Office Note to open <span style="color: red;"><? echo $lc_type[implode("",$lc_type_arr)]; ?></h2></span>
          		<div class="column" align="right"><h1><span style="color: red;">
          			<? if($data_arr[0][IS_APPROVED]==1) { echo "Approved"; } if($data_arr[0][IS_APPROVED]==3) { echo "Partial Approved"; } ?>
          		</h1></span></div>
          	</div>   

			<table class="rpt_table" width="100%" cellspacing="1" rules="all" border="1">
				<thead>
					<tr>
						<th width="40">Sl. No.</th>
						<th width="60">Our Ref. File No.</th>
						<th width="130">File Details</th>
						<th width="60">BTB Entitled %</th>
						<th width="60">Prev. BTB LC %</th>
						<th width="60">Propossed LC %</th>
						<th width="60">BTB LC Fund Avl. %</th>
						<th width="80">Item Category</th>
						<th width="80">PI No. & Date</th>
						<th width="80">Supplier Name</th>
						<th width="50">Tenor</th>
						<th width="100">Job & Booking</th>						
						<th width="100">Item</th>						
						<th width="70">PI Qty</th>
						<th width="50">Rate</th>						
						<th width="70">PI Value</th>
						<th>Remarks</th>						
					</tr>
				</thead>
				<tbody>
					<?
					$i = 1;
					$total_quantity=$total_ammount = 0;
					foreach($office_note_data_arr as $file_no_key => $file_no)
					{
						foreach ($file_no as $pi_id => $pi_data) 
						{
							
							foreach ($pi_data as $item_des => $item_des_data)
							{
								$j=1;
								$rowspan_val=count($item_des_data);
								foreach ($item_des_data as $rate => $row)
								{	
									?>
									<tr>
									<?
									//echo $rate.'**'.$j.'system';
									$buyer = $buyer_lib[$buyer_data_arr[$file_no_key]];							
									$sc_finance=$lcsc_finance_value_arr[$file_no_key];
									$sc_replace=$sc_replace_value_arr[$file_no_key];
									$sc_replace_bal=($sc_finance-$sc_replace);
									$sc_direct=$sc_direct_value_arr[$file_no_key];
									$lc_replace=$lc_replace_arr[$file_no_key];
									$lc_replace_bal=($sc_replace-$lc_replace);
									$lc_direct=$lc_direct_arr[$file_no_key];									

									$sc_replace_bal_absulate_value=0;
									if ($sc_replace_bal < 0) 
									{
										$sc_replace_bal_absulate_value=abs($sc_replace_bal);
									}

									$file_val = $sc_finance+$sc_replace_bal_absulate_value+$sc_direct+$lc_direct;

									if (0<$file_val) 
									{
										//echo $lc_value_arr[$pi_id]/$file_val*100;
										//$prev_btb_lc=number_format($lc_value_arr[$pi_id]/$file_val*100,2);
										//$propossed_lc=number_format($amount_data_arr[$file_no_key]/$file_val*100,2);
										$prev_btb_lc=$lc_value_arr[$pi_id]/$file_val*100;
										$propossed_lc=$amount_data_arr[$file_no_key]/$file_val*100;
									}
									else
									{
										$prev_btb_lc='0.00';
										$propossed_lc='0.00';
									}

									if ($j==1) 
									{
										?>
										<td width="40" class="alignment" rowspan="<? echo $rowspan_val; ?>"><? echo $i; ?></td>
										<td width="60" class="alignment" rowspan="<? echo $rowspan_val; ?>"><? echo $file_no_key; ?></td>
										<td width="130" title="File Value: SC[Finance]+SC Replace Bal(if minus value of SC Replace Bal then add absolute value of SC Replace Bal)+SC [Direct]+LC Direct" rowspan="<? echo $rowspan_val; ?>">
											<? 
											echo "Buyer : ".$buyer.'<br>'; 
											echo "SC[Finance]: ".$sc_finance.'<br>';
											echo "SC [Replace]: ".$sc_replace.'<br>';
											echo "SC Replace Bal.: ".$sc_replace_bal.'<br>';
											echo "SC [Direct]: ".$sc_direct.'<br>';
											echo "LC [Replace]: ".$lc_replace.'<br>';
											echo "LC Replace Bal.: ".$lc_replace_bal.'<br>';
											echo "LC Direct: ".$lc_direct."<br>";
											echo "File Value: ".$file_val.'<br>';
											?>									
										</td>
										<td width=60" class="alignment" rowspan="<? echo $rowspan_val; ?>">
											<? echo $btb_entitled=$btb_limit_lib[$main_data_arr[$file_no_key][$pi_id]['importer_id']]; if($btb_limit_lib[$main_data_arr[$file_no_key][$pi_id]['importer_id']]!="") echo '%' ?>
										</td>
										<td width=60" class="alignment" rowspan="<? echo $rowspan_val; ?>">
											<? echo $prev_btb_lc.'%';?>	
										</td>
										<td width="60" class="alignment" rowspan="<? echo $rowspan_val; ?>" title="<? echo $amount_data_arr[$file_no_key]; ?>">
											<? echo number_format($propossed_lc,2).'%'; ?>
										</td>
										<td width="60" class="alignment" rowspan="<? echo $rowspan_val; ?>"><? $Fund_Avl=($btb_entitled-($prev_btb_lc+$propossed_lc)); echo number_format($Fund_Avl,2).'%'; ?></td>
										<td width="80" class="alignment" rowspan="<? echo $rowspan_val; ?>"><? echo $item_category[$main_data_arr[$file_no_key][$pi_id]['item_category_id']]; ?></td>
										<td width="80" class="alignment" rowspan="<? echo $rowspan_val; ?>"><? echo 'PI No: '.$main_data_arr[$file_no_key][$pi_id]['pi_number'].'<br>Date: '.change_date_format($main_data_arr[$file_no_key][$pi_id]['pi_date']); ?></td>
										<td width="80" class="alignment" rowspan="<? echo $rowspan_val; ?>"><? echo $supplier_lib[$main_data_arr[$file_no_key][$pi_id]['supplier_id']]; ?></td>
										<td width="50" class="alignment" rowspan="<? echo $rowspan_val; ?>"><? echo $main_data_arr[$file_no_key][$pi_id]['tenor']; ?></td>
										<td width="100" style="word-break: break-all;" class="alignment" rowspan="<? echo $rowspan_val; ?>">
											<? 
											if ($main_data_arr[$file_no_key][$pi_id]['item_category_id'] == 1) {
												echo implode(',',array_unique(explode(',',rtrim($booking_no_arr[$file_no_key][$pi_id]['job_no'],',')))).'</br>&</br>'.implode(',',array_unique(explode(',',rtrim($booking_no_arr[$file_no_key][$pi_id]['booking_no'],','))));
											}										 
											else
											{
												echo implode(',',array_unique(explode(',',rtrim($booking_no_arr[$file_no_key][$pi_id]['job_no'],',')))).'</br>&</br>'.implode(',',array_unique(explode(',',rtrim($main_data_arr[$file_no_key][$pi_id]['work_order_no'],',')))); 
											}
											?>											
										</td>								
										<td width="100" style="word-break: break-all;" class="verticalalign" rowspan="<? echo $rowspan_val; ?>"><? echo $item_des; ?></td>									
				                        <?
				                    }
									?>									
									<td width="70" align="right" class="verticalalign"><? echo number_format($row['quantity'],2); $total_quantity += $row['quantity'];?></td>
									<td width="60" align="right" class="verticalalign"><? echo $row['pi_rate']; ?></td>
									<td width="70" align="right" class="verticalalign"><? echo number_format($row['pi_amount'],4);  $total_ammount += $row['pi_amount'];?></td>
									<?
									if ($j==1)
									{
										?>
										<td class="alignment" rowspan="<? echo $rowspan_val; ?>"><p><? echo $main_data_arr[$file_no_key][$pi_id]['remarks']; ?></p></td> 
										<?
									}
									?>
									</tr>								
								
									<?										
									$j++;									
									
								}
								$i++;
							}						
						}
					}
					?>
					<tr class="tbl_bottom" height="25">
						<td colspan="13">Grand Total: </td>
						<td align="right"><? echo number_format($total_quantity,2); ?></td>
						<td></td>
						<td align="right"><? echo number_format($total_ammount,2); ?></td>
						<td></td>
					</tr>
				</tbody>
			</table>
            <?
            function signature_table_approval($mst_id, $company, $width, $prepared_by, $padding_top = 70) 
            {	
				$sql_user = sql_select("select b.id, b.user_name, b.user_full_name, b.designation from user_passwd b where b.status_active=1");
				$user_arr=array();
				foreach ($sql_user as $val) {
					$user_arr[$val[csf("id")]]['user_full_name']=$val[csf("user_full_name")];
					$user_arr[$val[csf("id")]]['designation']=$val[csf("designation")];
				}
				
				$sql = sql_select("select max(a.approved_no) as approved_no,  a.approved_date, a.approved_by, a.sequence_no, b.inserted_by, b.insert_date from approval_history a, commercial_office_note_mst b where b.id=a.mst_id and a.mst_id=$mst_id and b.importer_id=$company and b.is_approved !=0 and a.entry_form=39 and a.approved_no in(select max(a.approved_no) as approved_no from approval_history a where a.entry_form=39 and a.mst_id=$mst_id) group by a.approved_by,  a.approved_date, a.sequence_no, b.inserted_by, b.insert_date order by a.sequence_no");
				$sql_2=sql_select("select b.inserted_by as user_name, b.insert_date as user_date from commercial_office_note_mst b where b.id=$mst_id and b.importer_id=$company and b.status_active=1 and b.is_deleted=0 and b.is_approved !=0");
					
				$lib_designation_arr=return_library_array("select id, custom_designation from lib_designation","id","custom_designation");
				$sql_2_arr=array();
				if ($sql_2){					
					$sql_2_arr[500] = array ( APPROVED_BY => 'Prepared By', USER_NAME => $user_arr[$sql_2[0][csf("user_name")]]['user_full_name'], USER_DATE =>$sql_2[0][csf("user_date")]);
				}
				
				$sql_arr=array();
				foreach ($sql as $row) {
					$sql_arr[]=array ( APPROVED_BY => $user_arr[$row[csf("approved_by")]]['user_full_name'], USER_NAME => $lib_designation_arr[$user_arr[$row[csf("approved_by")]]['designation']], USER_DATE =>$row[csf('approved_date')]);
				}
				$sql_rs=$sql_2_arr+$sql_arr;
				//echo '<pre>';print_r($sql_rs);

				$count = count($sql_rs);
				$td_width = floor($width / $count);
				$standard_width = $count * 100;
				if ($standard_width > $width) {
					$td_width = 100;
				}
				$no_coloumn_per_tr = floor($width / $td_width);
				//echo $no_coloumn_per_tr;
				$i = 1;
				if ($count == 0) {$message = "<b>Note: This is Software Generated Copy , Signature is not Required.</b>";}
				echo '<table id="signatureTblId" width="' . $width . '" style="padding-top:' . $padding_top . 'px;"><tr><td width="100%" height="' . $padding_top . '" colspan="' . $count . '">' . $message . '</td></tr><tr>';
				foreach ($sql_rs as $row) {
					echo '<td width="' . $td_width . '" align="center" valign="top">
					<strong>' . $row[csf("approved_by")] . "</strong><br>" . $row[csf("user_name")] . "</strong><br>" . $row[csf('user_date')] . '</td>';
					if ($i % $no_coloumn_per_tr == 0) {
						echo '</tr><tr><td width="100%" height="70" colspan="' . $no_coloumn_per_tr . '"></td></tr>';
					}
					$i++;
				}
				echo '</tr></table>';
			}

			$com_office_note_varriable=return_field_value("pi_source_btb_lc","variable_settings_commercial","company_name=$data[0] and variable_list=29 and status_active=1 and is_deleted=0", "pi_source_btb_lc");
			if ($com_office_note_varriable == 2) echo signature_table_approval($data[1], $data[0],"1250px"); // From Approval
			else echo signature_table(177, $data[0], "1250px",$cbo_template_id,70,$user_lib_name[$inserted_by]);  // From Library          
            //echo signature_table(177, $data[0],"900px");
            ?>
        </div>
	</div>
	<?
    exit();
}

if($action=="print2") // Print 2
{
	$data = explode('**',$data);
	//echo $data[1];
	echo load_html_head_contents($data[3],"../../", 1, 1, $unicode,'','');

	$cbo_template_id=$data[3];
    if($cbo_template_id==1) $align_cond='center'; else $align_cond='right';
    $user_lib_name=return_library_array("SELECT id,user_full_name from user_passwd", "id", "user_full_name");

	if ($data[3])
	{
		if ($data[2] == 1) echo '<style>body { background-image: url("../img/approved.gif"); } </style>';
		else echo '<style>body { background-image: url("../img/draft.gif"); } </style>';
		echo '<link href="../css/style_common.css" rel="stylesheet" type="text/css" />';
	}
	else
	{
		if ($data[2] == 1) echo '<style>body { background-image: url("../../img/approved.gif"); } </style>';
		else echo '<style>body { background-image: url("../../img/draft.gif"); } </style>';
		echo '<link href="../../css/style_common.css" rel="stylesheet" type="text/css" />';
	}

	?>
	<div style="width:1000px">
		<?
		$supplier_lib=return_library_array( "select id, supplier_name from lib_supplier",'id','supplier_name');
		$section_lib_arr=return_library_array( "select id, section_name from lib_section where status_active=1 and is_deleted=0",'id','section_name');
		$bank_lib_arr=return_library_array( "select id,bank_name from lib_bank where is_deleted=0 and status_active=1 and issusing_bank = 1 order by bank_name",'id','bank_name');
		$item_group_name_arr=return_library_array( "select id,item_name FROM lib_item_group",'id','item_name');
		$currency_samble = array(1=>'৳',2=>'$',3=>'€',4=>'CHF',5=>'$',6=>'£',7=>'¥');
		$image_location=return_field_value("image_location","common_photo_library","file_type=1 and form_name='company_details' and master_tble_id='$data[0]'","image_location");
		$com_sql=sql_select("SELECT a.id, a.company_name, a.city from lib_company a where a.id=$data[0]");
		$company_name=$com_sql[0][csf("company_name")];
		$location_name=$com_sql[0][csf("city")];

		// =================================Main Query Start==============================
		$main_sql="SELECT a.id, a.con_system_id, a.lc_type, a.office_note_date, a.importer_id, a.section, a.proposed_bank, a.exchange_rate, a.remarks, a.is_approved, a.inserted_by, b.yarn_composition_item1, b.yarn_composition_percentage1 as percent, b.item_description, b.hs_code, sum(b.quantity) as quantity, b.uom, b.rate, b.pi_dtls_id, c.internal_file_no, c.supplier_id, c.item_category_id, b.amount, a.pi_id, c.pi_number, c.pi_date, c.currency_id, d.item_prod_id, d.work_order_id
		from commercial_office_note_mst a, commercial_office_note_dtls b, com_pi_master_details c, com_pi_item_details d 
		where a.id=b.mst_id and b.pi_id=c.id and c.id=b.pi_id and b.pi_id=d.pi_id and b.pi_dtls_id=d.id and b.mst_id=$data[1] and c.item_category_id!=1 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and d.status_active=1 and d.is_deleted=0 
		group by a.id, a.con_system_id, a.lc_type, a.office_note_date, a.importer_id, a.section, a.proposed_bank, a.exchange_rate, a.remarks, a.is_approved, a.inserted_by, b.yarn_composition_item1, b.yarn_composition_percentage1, b.item_description, b.hs_code, b.uom, b.rate, b.pi_dtls_id,c.internal_file_no, c.supplier_id, c.item_category_id, b.amount, a.pi_id, c.pi_number, c.pi_date, c.currency_id, d.item_prod_id, d.work_order_id order by b.pi_dtls_id";
		//echo $main_sql;
		$data_arr=sql_select($main_sql);
		// echo "<pre>"; print_r($data_arr);
		$main_data_arr=array(); $lc_type_arr=array(); $note_date_arr=array();
		foreach($data_arr as $row)
		{
			/*$main_data_arr[$row[csf('pi_number')]][$row[csf('item_description')]]['lc_type']=$row[csf('lc_type')];
			$main_data_arr[$row[csf('pi_number')]][$row[csf('item_description')]]['office_note_date']=$row[csf('office_note_date')];
			$main_data_arr[$row[csf('pi_number')]][$row[csf('item_description')]]['supplier_id']=$row[csf('supplier_id')];
			$main_data_arr[$row[csf('pi_number')]][$row[csf('item_description')]]['pi_id']=$row[csf('pi_id')];
			$main_data_arr[$row[csf('pi_number')]][$row[csf('item_description')]]['quantity']+=$row[csf('quantity')];
			$main_data_arr[$row[csf('pi_number')]][$row[csf('item_description')]]['rate']=$row[csf('rate')];
			$main_data_arr[$row[csf('pi_number')]][$row[csf('item_description')]]['amount']+=$row[csf('amount')];
			$main_data_arr[$row[csf('pi_number')]][$row[csf('item_description')]]['section']=$row[csf('section')];
			$main_data_arr[$row[csf('pi_number')]][$row[csf('item_description')]]['proposed_bank']=$row[csf('proposed_bank')];
			$main_data_arr[$row[csf('pi_number')]][$row[csf('item_description')]]['exchange_rate']=$row[csf('exchange_rate')];
			$main_data_arr[$row[csf('pi_number')]][$row[csf('item_description')]]['percent']=$row[csf('percent')];
			$main_data_arr[$row[csf('pi_number')]][$row[csf('item_description')]]['hs_code']=$row[csf('hs_code')];
			$main_data_arr[$row[csf('pi_number')]][$row[csf('item_description')]]['pi_dtls_id']=$row[csf('pi_dtls_id')];
			$main_data_arr[$row[csf('pi_number')]][$row[csf('item_description')]]['uom']=$row[csf('uom')];
			$main_data_arr[$row[csf('pi_number')]][$row[csf('item_description')]]['remarks']=$row[csf('remarks')];
			$main_data_arr[$row[csf('pi_number')]][$row[csf('item_description')]]['currency_id']=$row[csf('currency_id')];
			$main_data_arr[$row[csf('pi_number')]][$row[csf('item_description')]]['item_category_id']=$row[csf('item_category_id')];
			$main_data_arr[$row[csf('pi_number')]][$row[csf('item_description')]]['item_prod_id']=$row[csf('item_prod_id')];*/
			$inserted_by=$row[csf('inserted_by')];

			$lc_type_arr[$row[csf('lc_type')]]=$row[csf('lc_type')];
			$note_date_arr[$row[csf('office_note_date')]]=$row[csf('office_note_date')];
			$pi_id_arr[$row[csf('pi_id')]]=$row[csf('pi_id')];
			$all_wo_id[$row[csf('work_order_id')]]=$row[csf('work_order_id')];
			$all_item_id[$row[csf('item_prod_id')]]=$row[csf('item_prod_id')];
		}
		$max_wo_id=max($all_wo_id);
		//echo $max_wo_id."test <br>";
		//echo "<pre>"; print_r($all_wo_id);die;
		// =================================Main Query End==============================

		// ==================================proposal for start===================================
		$all_pi_id = chop(implode(",",$pi_id_arr),',');
		if ($all_pi_id!="") 
		{
			$pi_id_cond = " and b.pi_id in($all_pi_id)";
		
			$proposal_sql="SELECT a.tenor, a.payterm_id, b.id as pi_dtls_id, b.item_group from wo_non_order_info_mst a, com_pi_item_details b 
			where a.id=b.work_order_id $pi_id_cond and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0";
			// echo $proposal_sql;
			$proposal_data_arr=sql_select($proposal_sql);
			$tenor_arr=array();$payterm_arr=array();
			foreach ($proposal_data_arr as $key => $value) 
			{
				$tenor_arr[$value[csf('pi_dtls_id')]]['tenor']=$value[csf('tenor')];
				$payterm_arr[$value[csf('pi_dtls_id')]]['payterm_id']=$value[csf('payterm_id')];
				$item_group_arr[$value[csf('pi_dtls_id')]]['item_group']=$value[csf('item_group')];
			}
		}
		// ==================================proposal for end===================================

		// ==================================previous rate start===================================
		if (count($all_wo_id)>0 && count($all_item_id)>0)
		{
			$previous_rate_sql="SELECT b.id, b.item_category_id, b.item_id, b.insert_date, b.rate, a.currency_id, a.wo_number
			from wo_non_order_info_mst a, wo_non_order_info_dtls b
			where a.id=b.mst_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and b.item_id in(".implode(",", $all_item_id).") and a.id not in(".implode(",", $all_wo_id).") and a.id < $max_wo_id 
			order by b.item_id, b.id";// and b.item_id=12970
		}
		//echo $previous_rate_sql;
		$previous_rate_data_arr=sql_select($previous_rate_sql);
		$previous_rate=array();
		foreach ($previous_rate_data_arr as $key => $row) 
		{
			$previous_rate[$row[csf('item_id')]]['rate']=$row[csf('rate')];
			$previous_rate[$row[csf('item_id')]]['currency_id']=$row[csf('currency_id')];
			$previous_rate[$row[csf('item_id')]]['wo_number']=$row[csf('wo_number')];
		}
		//echo "<pre>";print_r($previous_rate);die;
		// ==================================previous rate end===================================

		// ==================================Approval start=======================================		
		$app_sql="SELECT b.mst_id, b.approved_no, b.approved_date from commercial_office_note_mst a, approval_history b where a.id=b.mst_id and a.id=$data[1] and a.is_approved=1 and b.current_approval_status=1";
		$app_data_arr=sql_select($app_sql);
		$approved_no_arr=array(); $approved_date_arr=array();
		foreach ($app_data_arr as $key => $value) 
		{
			$approved_no_arr[$value[csf('mst_id')]]=$value[csf('approved_no')];
			$approved_date_arr[$value[csf('mst_id')]]=$value[csf('approved_date')];
		}
		// ==================================Approval End=======================================	
		$today = date("Y-m-d");
		?>
		<style type="text/css">
			.parent {
			  display: flex;
			  flex-direction:row;
			}

			.column {
			  flex: 1 1 0px;
			}
			.alignment {
			  vertical-align: middle;
			  text-align: center;
			}
			.verticalalign {
			  vertical-align: middle;
			}
			.rpt_table td {
			    border: 1px solid #020202;
			}
			.rpt_table thead th
			{
				border: 1px solid #020202;
			}
		</style>
		<div style="width:1350px; margin-left:10px; height: 80px;" class="parent">
			<div width="250">
				<table width="250" cellspacing="0" align="center">
					<tr>
						<td rowspan="3" width="70">
						<img src="../../<? echo $image_location; ?>" height="70" width="200"></td>
					</tr>
				</table>
			</div>
			<div width="250" class="column">
				<table width="300" cellspacing="0" align="left">
					<tr>
						<td colspan="2" style="font-size:x-large;" align="left"><strong><? echo $company_name; ?></strong></td>
					</tr>
					<tr class="form_caption">
						<td colspan="2" align="left" style="font-size:14px"><? echo $location_name; //.",".$address; ?></td>
					</tr>
				</table>
			</div>
			<div width="250" class="column" align="right">
				<table width="300" cellspacing="1" align="right" class="rpt_table" rules="all" border="1">
					<tr>
						<td colspan="2" align="left"><strong>Office Note Date</strong></td>
						<td>&nbsp<? echo change_date_format(implode("",$note_date_arr)); ?></td>
					</tr>
					<tr>
						<td colspan="2" align="left"><strong>Office Note No.</strong></td>
						<td>&nbsp<? echo $data_arr[0][CON_SYSTEM_ID]; ?></td>
					</tr>
					<tr>
						<td colspan="2" align="left"><strong>Approval No.</strong></td>
						<td>&nbsp<? echo $approved_no_arr[$data[1]]; ?></td>
					</tr>
					<tr>
						<td colspan="2" align="left"><strong>Last App. Date & Time</strong></td>
						<td>&nbsp<? $dateFormate=strtotime($approved_date_arr[$data[1]]);
						if ($dateFormate!="") 
						{
							echo date("d-M-Y, h:i:s a", $dateFormate);
						}?></td>
					</tr>
				</table>
			</div>
      	</div>

        <div style="width:1350px; margin-left:10px;">
          	<div class="parent"><h2>Office Note to open <span style="color: red;"><? echo $lc_type[implode("",$lc_type_arr)]; ?></h2></span>
          		<div class="column" align="right"><h1><span style="color: red;">
          			<? if($data_arr[0][IS_APPROVED]==1) { echo "Approved"; } if($data_arr[0][IS_APPROVED]==3) { echo "Partial Approved"; } ?>
          		</h1></span></div>
          	</div>  

			<table class="rpt_table" width="100%" cellspacing="1" rules="all" border="1">
				<thead>
					<tr>
						<th>Sl. No.</th>
						<th>Tenor</th>
						<th>Section</th>
						<th>Bank</th>
						<th>5% Margin BDT</th>
						<th>Name of Supplyer</th>
						<th>Item Name Group</th>
						<th>Items Description</th>
						<th>HS Code</th>
						<th>Quantity</th>
						<th>Unit</th>
						<th>Rate</th>
						<th>Previous Rate</th>
						<th>Difference Incre/Decre</th>
						<th>Total FC Value </th>
						<th>Remarks</th>
					</tr>
				</thead>
				<tbody>
					<?
					$i = 1;
					$total_quantity=0; $total_ammount=0;$total_margin_bdt=0;
					foreach($data_arr as $row)
					{
						//foreach ($pi_number_val as $item_desc => $row) 
						//{
							if( $i % 2 == 0 ) $bgcolor="#E9F3FF";
							else $bgcolor = "#FFFFFF";
							$tenor=$tenor_arr[$row[csf('pi_dtls_id')]]['tenor'];
							$pay_mode_id=$payterm_arr[$row[csf('pi_dtls_id')]]['payterm_id'];
							$item_group_name=$item_group_name_arr[$item_group_arr[$row[csf('pi_dtls_id')]]['item_group']];
							$proposal_for=($tenor!="") ? $tenor : $pay_term[$pay_mode_id] ;
							$margin_bdt=($row[csf('amount')]*0.05)*$row[csf('exchange_rate')];
							
							//echo $different_rate.'<br>';
							$currency_id=$row[csf('currency_id')];
							if($db_type==0)
							{
								$conversion_date=change_date_format($today, "Y-m-d", "-",1);
							}
							else
							{
								$conversion_date=change_date_format($today, "d-M-y", "-",1);
							}
							//$currency_rate=set_conversion_rate( $previous_rate[$row['item_prod_id']]['currency_id'], $conversion_date );
							if ($currency_id != $previous_rate[$row[csf('item_prod_id')]]['currency_id'])
							{
								if ($currency_id == 1) 
								{
									$currency_rate=set_conversion_rate( $previous_rate[$row[csf('item_prod_id')]]['currency_id'], $conversion_date );
									$last_wo_rate = $previous_rate[$row[csf('item_prod_id')]]['rate']*$currency_rate;									
								}
								else 
								{									
									$currency_rate=set_conversion_rate( $currency_id, $conversion_date );
									$last_wo_rate = $previous_rate[$row[csf('item_prod_id')]]['rate']/$currency_rate;
								}
							}
							else
							{
								$last_wo_rate=$previous_rate[$row[csf('item_prod_id')]]['rate'];				
							}

							$different_rate=$row[csf('rate')]-$last_wo_rate;	

     						if ($currency_id==1) $paysa_sent="Paisa";
     						else if ($currency_id==2) $paysa_sent="CENTS";
							?>
							<tr bgcolor="<? echo $bgcolor; ?>"  height="25" >
								<td width="50" class="alignment"><? echo $i; ?></td>
								<td width="50" class="alignment"><? echo $proposal_for; ?></td>
								<td width="90" class="alignment"><? echo $section_lib_arr[$row[csf('section')]];?></td>
								<td width="100" class="alignment"><? echo $bank_lib_arr[$row[csf('proposed_bank')]];?></td>
								<td width="110" class="alignment"><? echo number_format($margin_bdt,2);
								$total_margin_bdt += $margin_bdt;?></td>
								<td width="90" class="alignment"><? echo $supplier_lib[$row[csf('supplier_id')]]; ?></td>
								<td width="90" class="alignment"><? echo $item_group_name; ?></td>
								<td width="100" class="alignment"><? echo $row[csf('item_description')]; ?></td>
								<td width="50" class="verticalalign"><? echo $row[csf('hs_code')]; ?></td>
								<td width="80" align="right" class="verticalalign"><? echo number_format($row[csf('quantity')],2); 
								$total_quantity += $row[csf('quantity')];?></td>
								<td width="45" class="verticalalign"><? echo $unit_of_measurement[$row[csf('uom')]]; ?></td>
								<td width="60" align="right" align="right" class="verticalalign"><? echo $currency_samble[$currency_id].' '.number_format($row[csf('rate')],3); ?></td>
								<td width="80" align="right" class="verticalalign" title="<? echo 'Item Id: '.$row[csf('item_prod_id')].' and WO Number: '.$previous_rate[$row[csf('item_prod_id')]]['wo_number']; ?>"><? echo $currency_samble[$currency_id].' '.number_format($last_wo_rate,3); ?></td>
								<td width="80" align="right" class="verticalalign"><? if ($last_wo_rate==0) echo $currency_samble[$currency_id].' '.number_format($last_wo_rate,3); else echo $currency_samble[$currency_id].' '.number_format($different_rate,3); ?></td>
								<td width="100" align="right" class="verticalalign"><? echo $currency_samble[$currency_id].' '.number_format($row[csf('amount')],2);  $total_ammount += $row[csf('amount')]; ?></td>								
								<td width="75" class="alignment"><? echo $row[csf('remarks')]; ?></td>
							</tr>
							<?
							$i++;
						//}
					}
					?>
					<tr class="tbl_bottom">
						<td></td>
						<td></td>
						<td></td>
						<td align="right">Total: </td>
						<td align="right"><? echo number_format($total_margin_bdt,2);?></td>
						<td></td>
						<td></td>
						<td></td>
						<td align="right"></td>
						<td align="right"><? //echo number_format($total_quantity,2);?></td>
						<td></td>
						<td></td>
						<td></td>
						<td align="right"></td>
						<td align="right"><? echo $currency_samble[$currency_id].' '.number_format($total_ammount,2);?></td>
						<td></td>
					</tr>
				</tbody>				
			</table>
			<tr>
				<td align="left" colspan="8" ><strong>In Words:<? echo number_to_words($total_ammount,$currency[$currency_id],$paysa_sent); ?></strong></td>
			</tr>
            <?
            function signature_table_approval($mst_id, $company, $width, $prepared_by, $padding_top = 70) 
            {	
				$sql_user = sql_select("select b.id, b.user_name, b.user_full_name, b.designation from user_passwd b where b.status_active=1");
				$user_arr=array();
				foreach ($sql_user as $val) {
					$user_arr[$val[csf("id")]]['user_full_name']=$val[csf("user_full_name")];
					$user_arr[$val[csf("id")]]['designation']=$val[csf("designation")];
				}

				$sql = sql_select("select max(a.approved_no) as approved_no,  a.approved_date, a.approved_by, a.sequence_no, b.inserted_by, b.insert_date from approval_history a, commercial_office_note_mst b where b.id=a.mst_id and a.mst_id=$mst_id and b.importer_id=$company and b.is_approved !=0 and a.entry_form=39 and a.approved_no in(select max(a.approved_no) as approved_no from approval_history a where a.entry_form=39 and a.mst_id=$mst_id) group by a.approved_by,  a.approved_date, a.sequence_no, b.inserted_by, b.insert_date order by a.sequence_no");
				
				$sql_2=sql_select("select b.inserted_by as user_name, b.insert_date as user_date from commercial_office_note_mst b where b.id=$mst_id and b.importer_id=$company and b.status_active=1 and b.is_deleted=0 and b.is_approved !=0");
					
				$lib_designation_arr=return_library_array("select id, custom_designation from lib_designation","id","custom_designation");
				$sql_2_arr=array();
				if ($sql_2){
					$sql_2_arr[500] = array ( APPROVED_BY => 'Prepared By', USER_NAME => $user_arr[$sql_2[0][csf("user_name")]]['user_full_name'], USER_DATE =>$sql_2[0][csf("user_date")]);
				}
				
				$sql_arr=array();
				foreach ($sql as $row) {
					$sql_arr[]=array ( APPROVED_BY => $user_arr[$row[csf("approved_by")]]['user_full_name'], USER_NAME => $lib_designation_arr[$user_arr[$row[csf("approved_by")]]['designation']], USER_DATE =>$row[csf('approved_date')]);
				}
				$sql_rs=$sql_2_arr+$sql_arr;
				//echo '<pre>';print_r($sql_rs);

				$count = count($sql_rs);
				$td_width = floor($width / $count);
				$standard_width = $count * 100;
				if ($standard_width > $width) {
					$td_width = 100;
				}
				$no_coloumn_per_tr = floor($width / $td_width);
				//echo $no_coloumn_per_tr;
				$i = 1;
				if ($count == 0) {$message = "<b>Note: This is Software Generated Copy , Signature is not Required.</b>";}
				echo '<table id="signatureTblId" width="' . $width . '" style="padding-top:' . $padding_top . 'px;"><tr><td width="100%" height="' . $padding_top . '" colspan="' . $count . '">' . $message . '</td></tr><tr>';
				/*if ($user_arr[$sql[0][csf('inserted_by')]]['user_full_name'] != ''){
					echo '<td width="100" align="center" valign="top"><strong>Prepared By</strong><br>' . $user_arr[$sql[0][csf('inserted_by')]]['user_full_name'] .'<br>' .$sql[0][csf('insert_date')]. '</td>';
				}*/
				foreach ($sql_rs as $row) {
					echo '<td width="' . $td_width . '" align="center" valign="top">
					<strong>' . $row[csf("approved_by")] . "</strong><br>" . $row[csf("user_name")] . "</strong><br>" . $row[csf('user_date')] . '</td>';
					if ($i % $no_coloumn_per_tr == 0) {
						echo '</tr><tr><td width="100%" height="70" colspan="' . $no_coloumn_per_tr . '"></td></tr>';
					}
					$i++;
				}
				echo '</tr></table>';
			}

			$com_office_note_varriable=return_field_value("pi_source_btb_lc","variable_settings_commercial","company_name=$data[0] and variable_list=29 and status_active=1 and is_deleted=0", "pi_source_btb_lc");
			if ($com_office_note_varriable == 2)  echo signature_table_approval($data[1], $data[0],"1250px"); // From Approval
			else echo signature_table(177, $data[0], "1250px",$cbo_template_id,70,$user_lib_name[$inserted_by]);  // From Library             
            //echo signature_table(177, $data[0],"900px");
            ?>
        </div>
	</div>
	<?
    exit();
}

if($action=="print3") // Print3
{

	$data = explode('**',$data);
	//$path = $data[3];  // This parameter used Commercial Office Note Approval Page
	//echo $path.'system';
	echo load_html_head_contents($data[3],"../../", 1, 1, $unicode,'','');
	$cbo_template_id=$data[3];
	if($cbo_template_id==1) $align_cond='center'; else $align_cond='right';
	$user_lib_name=return_library_array("SELECT id,user_full_name from user_passwd", "id", "user_full_name");

	if ($data[3])
	{
		if ($data[2] == 1) echo '<style>body { background-image: url("../img/approved.gif"); } </style>';
		else echo '<style>body { background-image: url("../img/draft.gif"); } </style>';
		echo '<link href="../css/style_common.css" rel="stylesheet" type="text/css" />';
	}
	else
	{
		if ($data[2] == 1) echo '<style>body { background-image: url("../../img/approved.gif"); } </style>';
		else echo '<style>body { background-image: url("../../img/draft.gif"); } </style>';
		echo '<link href="../../css/style_common.css" rel="stylesheet" type="text/css" />';
	}	
	?>
	
	<div style="width:1000px">
		<?
		$supplier_lib=return_library_array( "select id, supplier_name from lib_supplier",'id','supplier_name');
		$item_group_name_arr=return_library_array( "select id,item_name FROM lib_item_group",'id','item_name');
		$lib_body_part_arr=return_library_array("select id, body_part_full_name from lib_body_part", "id", "body_part_full_name");
		$buyer_lib=return_library_array( "SELECT buy.id ,buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company=$data[0] 
		and buy.id in (select buyer_id from lib_buyer_party_type where party_type in (1,3,21,90)) group by buy.id,buy.buyer_name order by buyer_name",'id','buyer_name');

		$btb_limit_lib=return_library_array("SELECT company_name, max_btb_limit from variable_settings_commercial WHERE company_name=$data[0] and variable_list=6 and max_btb_limit_hcode='Max BTB Limit' and status_active=1 and is_deleted=0",'company_name','max_btb_limit');

		$image_location=return_field_value("image_location","common_photo_library","file_type=1 and form_name='company_details' and master_tble_id='$data[0]'","image_location");
		$com_sql=sql_select("SELECT a.id, a.company_name, a.city from lib_company a where a.id=$data[0]");
		$company_name=$com_sql[0][csf("company_name")];
		$location_name=$com_sql[0][csf("city")];

		// =========================================================================
		$sql="SELECT a.id, a.con_system_id, a.is_approved, a.lc_type, a.office_note_date, a.importer_id, a.tenor, a.inserted_by, b.yarn_composition_percentage1, b.yarn_composition_percentage2, b.yarn_composition_item1, b.yarn_composition_item2, b.body_part_id, b.fab_type, b.fabric_construction, b.fab_design, b.fabric_composition, b.item_description, d.internal_file_no, d.supplier_id, d.item_category_id, d.total_amount, d.remarks, d.pi_number, d.id as pi_id, d.pi_date, b.quantity, b.rate as pi_rate, b.amount as pi_amount, c.id as pi_dtls_id, c.work_order_no, c.work_order_dtls_id, c.item_group, d.file_year
		FROM commercial_office_note_mst a, commercial_office_note_dtls b, com_pi_item_details c, com_pi_master_details d  
		WHERE a.id=b.mst_id and b.pi_dtls_id=c.id and c.pi_id=d.id and b.mst_id=$data[1] and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and d.status_active=1 and d.is_deleted=0
		group by a.id, a.con_system_id, a.is_approved, a.lc_type, a.office_note_date, a.importer_id, a.tenor, a.inserted_by, b.yarn_composition_percentage1, b.yarn_composition_percentage2, b.yarn_composition_item1, b.yarn_composition_item2, b.body_part_id, b.fab_type, b.fabric_construction, b.fab_design, b.fabric_composition, b.item_description, d.internal_file_no, d.supplier_id, d.id, d.item_category_id, d.total_amount, d.remarks, d.pi_number, d.pi_date, b.quantity, b.rate, b.amount, c.id, c.work_order_no, c.work_order_dtls_id, c.item_group, d.file_year
		order by d.id";
		$data_arr=sql_select($sql);
		
		/*$data_arr=sql_select("SELECT a.id, a.con_system_id, a.is_approved, a.lc_type, a.office_note_date, a.importer_id, c.internal_file_no, c.supplier_id, c.item_category_id, c.total_amount, c.remarks, a.pi_id, c.pi_number, c.pi_date
		FROM commercial_office_note_mst a, commercial_office_note_dtls b, com_pi_master_details c 
		WHERE a.id=b.mst_id and b.pi_id=c.id and b.mst_id=$data[1] and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 
		group by a.id, a.con_system_id, a.is_approved, a.lc_type, a.office_note_date, a.importer_id, c.internal_file_no, c.supplier_id, c.item_category_id, c.total_amount, c.remarks, a.pi_id, c.pi_number, c.pi_date");*/

		$main_data_arr=array(); $lc_type_arr=array(); $note_date_arr=array(); $$rowspan_arr=array(); 
		$file_no_arr=array(); $work_order_arr=array(); $amount_data_arr=array(); $file_no_year=array();
		foreach($data_arr as $row)
		{
			$main_data_arr[$row[csf('internal_file_no')]][$row[csf('pi_id')]]['lc_type']=$row[csf('lc_type')];
			$main_data_arr[$row[csf('internal_file_no')]][$row[csf('pi_id')]]['office_note_date']=$row[csf('office_note_date')];
			$main_data_arr[$row[csf('internal_file_no')]][$row[csf('pi_id')]]['tenor']=$row[csf('tenor')];
			$main_data_arr[$row[csf('internal_file_no')]][$row[csf('pi_id')]]['supplier_id']=$row[csf('supplier_id')];				
			$main_data_arr[$row[csf('internal_file_no')]][$row[csf('pi_id')]]['pi_number']=$row[csf('pi_number')];				
			$main_data_arr[$row[csf('internal_file_no')]][$row[csf('pi_id')]]['item_category_id']=$row[csf('item_category_id')];
			$main_data_arr[$row[csf('internal_file_no')]][$row[csf('pi_id')]]['total_amount']=$row[csf('total_amount')];
			$main_data_arr[$row[csf('internal_file_no')]][$row[csf('pi_id')]]['pi_date']=$row[csf('pi_date')];
			$main_data_arr[$row[csf('internal_file_no')]][$row[csf('pi_id')]]['remarks']=$row[csf('remarks')];
			$main_data_arr[$row[csf('internal_file_no')]][$row[csf('pi_id')]]['importer_id']=$row[csf('importer_id')];
			$main_data_arr[$row[csf('internal_file_no')]][$row[csf('pi_id')]]['pi_id']=$row[csf('pi_id')];

			//$pi_rate_arr[$row[csf('internal_file_no')]][$row[csf('pi_id')]]['pi_amount']+=$row[csf('pi_amount')];
			$pi_rate_arr[$row[csf('internal_file_no')]]['pi_amount']+=$row[csf('pi_amount')];

			/*$office_note_data_arr[$row[csf('internal_file_no')]][$row[csf('pi_id')]][$row[csf('pi_dtls_id')]]['quantity']=$row[csf('quantity')];
			$office_note_data_arr[$row[csf('internal_file_no')]][$row[csf('pi_id')]][$row[csf('pi_dtls_id')]]['pi_rate']=$row[csf('pi_rate')];
			$office_note_data_arr[$row[csf('internal_file_no')]][$row[csf('pi_id')]][$row[csf('pi_dtls_id')]]['pi_amount']=$row[csf('pi_amount')];*/
			$inserted_by=$row[csf('inserted_by')];

			if ($row[csf('item_category_id')]==1)
			{
				if ($row[csf('yarn_composition_percentage1')]!=0) {$composition_percentage1 = $row[csf('yarn_composition_percentage1')]."%";}
				if ($row[csf('yarn_composition_percentage2')]!=0) {$composition_percentage2 = $row[csf('yarn_composition_percentage2')]."%";}
				$item_description = $composition[$row[csf('yarn_composition_item1')]].' '.$composition[$row[csf('yarn_composition_item2')]].' '.$composition_percentage1.' '.$composition_percentage2;
				$office_note_data_arr[$row[csf('internal_file_no')]][$row[csf('pi_id')]][$item_description][$row[csf('pi_rate')]]['item_description']=$item_description;
				$office_note_data_arr[$row[csf('internal_file_no')]][$row[csf('pi_id')]][$item_description][$row[csf('pi_rate')]]['pi_rate']=$row[csf('pi_rate')];
				$office_note_data_arr[$row[csf('internal_file_no')]][$row[csf('pi_id')]][$item_description][$row[csf('pi_rate')]]['pi_amount']+=$row[csf('pi_amount')];
				$office_note_data_arr[$row[csf('internal_file_no')]][$row[csf('pi_id')]][$item_description][$row[csf('pi_rate')]]['quantity']+=$row[csf('quantity')];
				$work_order_dtls_id.=$row[csf('work_order_dtls_id')].',';
			}
			else if ($row[csf('item_category_id')]==2)
			{
				$item_description = $row[csf('fabric_construction')]." ".$row[csf('fabric_composition')];
				$office_note_data_arr[$row[csf('internal_file_no')]][$row[csf('pi_id')]][$item_description][$row[csf('pi_rate')]]['item_description']=$item_description;
				$office_note_data_arr[$row[csf('internal_file_no')]][$row[csf('pi_id')]][$item_description][$row[csf('pi_rate')]]['pi_rate']=$row[csf('pi_rate')];
				$office_note_data_arr[$row[csf('internal_file_no')]][$row[csf('pi_id')]][$item_description][$row[csf('pi_rate')]]['pi_amount']+=$row[csf('pi_amount')];
				$office_note_data_arr[$row[csf('internal_file_no')]][$row[csf('pi_id')]][$item_description][$row[csf('pi_rate')]]['quantity']+=$row[csf('quantity')];
				$main_data_arr[$row[csf('internal_file_no')]][$row[csf('pi_id')]]['work_order_no'].=$row[csf('work_order_no')].',';
			}
			else if ($row[csf('item_category_id')]==3)
			{
				$item_description = $lib_body_part_arr[$row[csf('body_part_id')]]." ".$row[csf('fab_type')]." ".$row[csf('fabric_construction')]." ".$row[csf('fab_design')]." ".$row[csf('fabric_composition')];
				$office_note_data_arr[$row[csf('internal_file_no')]][$row[csf('pi_id')]][$item_description][$row[csf('pi_rate')]]['item_description']=$item_description;
				$office_note_data_arr[$row[csf('internal_file_no')]][$row[csf('pi_id')]][$item_description][$row[csf('pi_rate')]]['pi_rate']=$row[csf('pi_rate')];
				$office_note_data_arr[$row[csf('internal_file_no')]][$row[csf('pi_id')]][$item_description][$row[csf('pi_rate')]]['pi_amount']+=$row[csf('pi_amount')];
				$office_note_data_arr[$row[csf('internal_file_no')]][$row[csf('pi_id')]][$item_description][$row[csf('pi_rate')]]['quantity']+=$row[csf('quantity')];
				$main_data_arr[$row[csf('internal_file_no')]][$row[csf('pi_id')]]['work_order_no'].=$row[csf('work_order_no')].',';
			}
			else
			{
				$item_description = $row[csf('item_description')];
				$office_note_data_arr[$row[csf('internal_file_no')]][$row[csf('pi_id')]][$item_description][$row[csf('pi_rate')]]['item_description']=$item_description;
				$office_note_data_arr[$row[csf('internal_file_no')]][$row[csf('pi_id')]][$item_description][$row[csf('pi_rate')]]['pi_rate']=$row[csf('pi_rate')];
				$office_note_data_arr[$row[csf('internal_file_no')]][$row[csf('pi_id')]][$item_description][$row[csf('pi_rate')]]['pi_amount']+=$row[csf('pi_amount')];
				$office_note_data_arr[$row[csf('internal_file_no')]][$row[csf('pi_id')]][$item_description][$row[csf('pi_rate')]]['quantity']+=$row[csf('quantity')];
				$main_data_arr[$row[csf('internal_file_no')]][$row[csf('pi_id')]]['work_order_no'].=$row[csf('work_order_no')].',';
			}

			$lc_type_arr[$row[csf('lc_type')]]=$row[csf('lc_type')];
			$note_date_arr[$row[csf('office_note_date')]]=$row[csf('office_note_date')];

			$internal_file_no="'".$row[csf('internal_file_no')]."'";
			$file_no_arr[$row[csf('internal_file_no')]]=$internal_file_no;
			if ($row[csf('file_year')] != '')
			{
				$file_year="'".$row[csf('file_year')]."'";
				$file_no_year[$row[csf('file_year')]]=$file_year;
			}			
			
			$pi_id_arr[$row[csf('pi_id')]]=$row[csf('pi_id')];			

			$amount_data_arr[$row[csf('internal_file_no')]]+=$row[csf('total_amount')];
		}
		//echo $work_order_dtls_id;
		/*echo "<pre>";
		print_r($rowspan_arr);*/
		$all_pi_id = implode(',',array_unique(explode(',',implode(",",$pi_id_arr))));//chop(implode(",",$pi_id_arr),',');
		$all_file_no = implode(',',array_unique(explode(',',implode(",",$file_no_arr))));
		$all_file_no_year = implode(',',array_unique(explode(',',implode(",",$file_no_year))));

		if ($all_file_no!="") 
		{
			$file_no_cond = " and a.internal_file_no in($all_file_no)";
			$file_no_lcyear_cond = " and a.lc_year in($all_file_no_year)";
			$file_no_scyear_cond = " and a.sc_year in($all_file_no_year)";
		}
		//echo $file_no_lcyear_cond;

		$booking_no_arr=array();
		$sql_booking="select a.internal_file_no, a.id as pi_id, d.job_no as requ_job_no, d.booking_no as requ_booking_no, e.basis, f.booking_no, f.job_no from com_pi_master_details a, com_pi_item_details b, wo_non_order_info_dtls c, inv_purchase_requisition_dtls d, inv_purchase_requisition_mst e, wo_booking_dtls f where a.id=b.pi_id and b.work_order_dtls_id=c.id and c.requisition_dtls_id=d.id and d.mst_id= e.id and d.job_no=f.job_no and a.id in($all_pi_id) and e.basis in(1,5) and a.item_category_id=1 and a.status_active=1 and b.status_active=1 and c.status_active=1 and d.status_active=1 and e.status_active=1 group by a.internal_file_no, a.id, d.job_no, d.booking_no, e.basis, f.booking_no, f.job_no
			union all
			select a.internal_file_no, a.id as pi_id, null as requ_job_no, null as requ_booking_no, null as basis, f.booking_no, f.job_no 
			from com_pi_master_details a, com_pi_item_details b, wo_booking_dtls f 
			where a.id=b.pi_id and b.work_order_no=f.booking_no and a.id in($all_pi_id) and a.status_active=1 and b.status_active=1 and f.status_active=1  group by a.internal_file_no, a.id, f.booking_no, f.job_no";
		$sql_booking_res=sql_select($sql_booking);
		foreach ($sql_booking_res as $val) {
			if ($val[csf('item_category_id')] == 1)
			{
				if ($val[csf('basis')] == 1) {
					$booking_no_arr[$val[csf('internal_file_no')]][$val[csf('pi_id')]]['booking_no'].=$val[csf('requ_booking_no')].',';
					$booking_no_arr[$val[csf('internal_file_no')]][$val[csf('pi_id')]]['job_no'].=$val[csf('job_no')].',';
				}
				else 
				{
					$booking_no_arr[$val[csf('internal_file_no')]][$val[csf('pi_id')]]['booking_no'].=$val[csf('booking_no')].',';
					$booking_no_arr[$val[csf('internal_file_no')]][$val[csf('pi_id')]]['job_no'].=$val[csf('job_no')].',';
				}
			}
			else
			{
				$booking_no_arr[$val[csf('internal_file_no')]][$val[csf('pi_id')]]['booking_no'].=$val[csf('booking_no')].',';
				$booking_no_arr[$val[csf('internal_file_no')]][$val[csf('pi_id')]]['job_no'].=$val[csf('job_no')].',';
			}	
		}
		//echo '<pre>';print_r($booking_no_arr);

		// ==================================BTB/Margin LC===================================

		
		if ($all_pi_id!="") 
		{
			//$pi_id_cond = " and pi_id ='$all_pi_id'";
			$pi_id_cond = " and b.pi_id in ($all_pi_id)";
		
			//$lc_val_sql="SELECT pi_id, lc_value from com_btb_lc_master_details where importer_id=$data[0] $pi_id_cond and status_active=1 and is_deleted=0";

			$lc_val_sql="SELECT a.pi_id, a.lc_value from com_btb_lc_master_details a ,COM_BTB_LC_PI b 
			where a.id = b.COM_BTB_LC_MASTER_DETAILS_ID and a.importer_id=$data[0] $pi_id_cond and a.status_active=1 and a.is_deleted=0";
			//echo $lc_val_sql;
			$lc_val_data_arr=sql_select($lc_val_sql);
			$lc_value_arr=array();
			foreach ($lc_val_data_arr as $key => $value) 
			{
				$lc_value_arr[$value[csf('pi_id')]]+=$value[csf('lc_value')];
			}
		}

		// ===============================Export Lc==============================================

		/*$elc_Sql="SELECT a.internal_file_no, b.replaced_amount 
		from com_export_lc a, com_export_lc_atch_sc_info b
		where a.id=b.com_export_lc_id and a.beneficiary_name=$data[0] $file_no_cond and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0";
		//echo $elc_Sql;
		$elc_data_arr=sql_select($elc_Sql);
		$replaced_amount_data_arr=array();
		foreach ($elc_data_arr as $key => $value) 
		{
			$replaced_amount_data_arr[$value[csf('internal_file_no')]]=$value[csf('replaced_amount')];
		}*/
		/*echo "<pre>";
		print_r($replaced_amount_data_arr);die;*/

		// ===============================Export Lc value==============================================

		// $lc_val_Sql="SELECT a.internal_file_no, a.lc_value, a.buyer_name, a.replacement_lc , a.last_shipment_date, a.lien_bank from com_export_lc a
        // where beneficiary_name=$data[0] $file_no_cond $file_no_lcyear_cond and a.status_active=1 and a.is_deleted=0";

		// $lc_val_Sql=" SELECT a.id, a.internal_file_no, a.lc_value, a.buyer_name, a.replacement_lc , a.last_shipment_date, a.lien_bank, SUM(b.attached_qnty) as qty from com_export_lc a, COM_EXPORT_LC_ORDER_INFO b
		// where a.id = b.COM_EXPORT_LC_ID and a.beneficiary_name=$data[0] $file_no_cond $file_no_lcyear_cond and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0
		// group by a.id, a.internal_file_no, a.lc_value, a.buyer_name, a.replacement_lc , a.last_shipment_date, a.lien_bank";

		$lc_val_Sql=" SELECT a.id, a.internal_file_no, a.lc_value, a.buyer_name, a.replacement_lc , a.last_shipment_date, a.lien_bank, SUM(b.attached_qnty) as qty ,b.pi_id from com_export_lc a, COM_EXPORT_LC_ORDER_INFO b
		left join  COM_PI_MASTER_DETAILS c on b.COM_EXPORT_LC_ID = c.lc_sc_id and c.is_lc_sc = 1 and  c.status_active=1 and c.is_deleted=0 
		where a.id = b.COM_EXPORT_LC_ID and a.beneficiary_name=$data[0] $file_no_cond $file_no_lcyear_cond and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0
		group by a.id, a.internal_file_no, a.lc_value, a.buyer_name, a.replacement_lc , a.last_shipment_date, a.lien_bank,b.pi_id";
		//echo $lc_val_Sql;
		$lc_val_data_arr=sql_select($lc_val_Sql);
		$lc_direct_arr=array(); $lc_replace_arr=array();
		foreach ($lc_val_data_arr as $key => $value) 
		{			
			
			if ($value[csf('replacement_lc')]==2) // Replacement LC = No (Direct.lc)
			{
				$lc_direct_arr[$value[csf('internal_file_no')]][$value[csf('pi_id')]]+=$value[csf('lc_value')];
			}
			if ($value[csf('replacement_lc')]==1) // Replacement LC Yes
			{
				$lc_replace_arr[$value[csf('internal_file_no')]][$value[csf('pi_id')]]+=$value[csf('lc_value')];
			}
			$lc_po_qty_arr[$value[csf('internal_file_no')]][$value[csf('pi_id')]]+=$value[csf('qty')];
			$buyer_data_arr[$value[csf('internal_file_no')]][$value[csf('pi_id')]]=$value[csf('buyer_name')];
			
			// if ($value[csf('replacement_lc')]==2) // Replacement LC = No (Direct.lc)
			// {
			// 	$lc_direct_arr[$value[csf('internal_file_no')]]+=$value[csf('lc_value')];
			// }
			// if ($value[csf('replacement_lc')]==1) // Replacement LC Yes
			// {
			// 	$lc_replace_arr[$value[csf('internal_file_no')]]+=$value[csf('lc_value')];
			// }
			// $lc_po_qty_arr[$value[csf('internal_file_no')]]+=$value[csf('qty')];
			// $buyer_data_arr[$value[csf('internal_file_no')]]=$value[csf('buyer_name')];
		}

		$lc_val_Sql_ship="SELECT a.internal_file_no,a.buyer_name , max(a.last_shipment_date) as shipment_date, a.lien_bank from com_export_lc a 
        where a.beneficiary_name=$data[0] $file_no_cond $file_no_lcyear_cond and a.status_active=1 and a.is_deleted=0 group by a.internal_file_no,a.buyer_name , a.lien_bank";
		//echo $lc_val_Sql_ship;
		$lc_val_data_arr_shp=sql_select($lc_val_Sql_ship);
		foreach ($lc_val_data_arr_shp as $key => $value) 
		{			
			$lc_shipment_arr[$value[csf('internal_file_no')]]=$value[csf('shipment_date')];
			$lc_bank = $lc_opning_bank[$value[csf('internal_file_no')]]=$value[csf('lien_bank')];
		}

		// echo "<pre>";
		// print_r($lc_shipment_arr);//die;

		// ================================Sales Contract value and buyer=========================

		/* File Details Formula
		1	SC[Finance] = Data will come from-> Sales Contract Entry -> Convertible To: Finance
		2	SC [Replace] = Data will come from-> Sales Contract Entry -> Convertible To: LC/SC
		3	SC Replace Bal. = 1-2
		4	SC [Direct]	= Data will come from-> Sales Contract Entry -> Convertible To: No
		5	LC [Replace] = Data will come from-> Export LC Entry -> Replacement LC : Yes
		6	LC Replace Bal. = 2-5
		7	LC Direct = Data will come from-> Export LC Entry -> Replacement LC : No
		8	File Value  = 1+if minus value of SC Replace Bal then add absolute value of SC Replace Bal+4+7
		*/


		// $scSql="SELECT internal_file_no, contract_no ,contract_date, buyer_name, contract_value, convertible_to_lc, last_shipment_date,lien_bank ,max_btb_limit from com_sales_contract where beneficiary_name=$data[0] $file_no_cond $file_no_scyear_cond and status_active=1 and is_deleted=0";

		$scSql="SELECT a.internal_file_no, a.contract_no ,a.contract_date, a.buyer_name, a.contract_value, a.convertible_to_lc, a.last_shipment_date,a.lien_bank ,a.max_btb_limit,b.id as pi_id 
		from com_sales_contract a 
		left join  COM_PI_MASTER_DETAILS b on a.id = b.lc_sc_id and b.is_lc_sc = 2 and  b.status_active=1 and b.is_deleted=0 
		where a.beneficiary_name=$data[0] $file_no_cond $file_no_scyear_cond and a.status_active=1 and a.is_deleted=0";

		//echo $scSql;
		$sc_data_arr=sql_select($scSql);
		$lcsc_finance_value_arr=array(); $sc_replace_value_arr=array(); $sc_direct_value_arr=array(); 
		foreach ($sc_data_arr as $key => $value) 
		{

			if ($value[csf('convertible_to_lc')]==3) // Convertible to = Finance
			{
				$lcsc_finance_value_arr[$value[csf('internal_file_no')]][$value[csf('pi_id')]]+=$value[csf('contract_value')];
			}
			if ($value[csf('convertible_to_lc')]==1) // Convertible to = LC/SC
			{
				$sc_lc_replace_value_arr[$value[csf('internal_file_no')]][$value[csf('pi_id')]]+=$value[csf('contract_value')];
			}
			if ($value[csf('convertible_to_lc')]==2) // Convertible to = No (SC [Direct])
			{
				$sc_direct_value_arr[$value[csf('internal_file_no')]][$value[csf('pi_id')]]+=$value[csf('contract_value')];
			}
			$sc_replace_value_arr[$value[csf('internal_file_no')]][$value[csf('pi_id')]]+=$value[csf('contract_value')];
			$buyer_data_arr[$value[csf('internal_file_no')]][$value[csf('pi_id')]]=$value[csf('buyer_name')];	
			$sc_cont_no_arr[$value[csf('internal_file_no')]][$value[csf('pi_id')]]=$value[csf('contract_no')];	
			$sc_cont_date_arr[$value[csf('internal_file_no')]][$value[csf('pi_id')]]=$value[csf('contract_date')];
			$convertible_to_lc_arr[$value[csf('internal_file_no')]][$value[csf('pi_id')]]=$value[csf('convertible_to_lc')];
			$sc_max_btb_limit_arr[$value[csf('internal_file_no')]][$value[csf('pi_id')]]=$value[csf('max_btb_limit')];
			// if ($value[csf('convertible_to_lc')]==3) // Convertible to = Finance
			// {
			// 	$lcsc_finance_value_arr[$value[csf('internal_file_no')]]+=$value[csf('contract_value')];
			// }
			// if ($value[csf('convertible_to_lc')]==1) // Convertible to = LC/SC
			// {
			// 	$sc_lc_replace_value_arr[$value[csf('internal_file_no')]]+=$value[csf('contract_value')];
			// }
			// if ($value[csf('convertible_to_lc')]==2) // Convertible to = No (SC [Direct])
			// {
			// 	$sc_direct_value_arr[$value[csf('internal_file_no')]]+=$value[csf('contract_value')];
			// }
			// $sc_replace_value_arr[$value[csf('internal_file_no')]]+=$value[csf('contract_value')];
			// $buyer_data_arr[$value[csf('internal_file_no')]]=$value[csf('buyer_name')];	
			// $sc_cont_no_arr[$value[csf('internal_file_no')]]=$value[csf('contract_no')];	
			// $sc_cont_date_arr[$value[csf('internal_file_no')]]=$value[csf('contract_date')];
			// $convertible_to_lc_arr[$value[csf('internal_file_no')]]=$value[csf('convertible_to_lc')];
			// $sc_max_btb_limit_arr[$value[csf('internal_file_no')]]=$value[csf('max_btb_limit')];


		}

		// $sc_con_sql="SELECT a.id,a.internal_file_no, a.contract_no ,a.contract_date, a.buyer_name, a.contract_value, a.convertible_to_lc, a.last_shipment_date,a.lien_bank , sum(b.attached_qnty) as qty from com_sales_contract a , COM_SALES_CONTRACT_ORDER_INFO b
		// where a.id = b.com_sales_contract_id and a.beneficiary_name=$data[0] $file_no_cond $file_no_scyear_cond and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 
		// group by a.id,a.internal_file_no, a.contract_no ,a.contract_date, a.buyer_name, a.contract_value, a.convertible_to_lc, a.last_shipment_date,a.lien_bank";

		$max_pi_id =  max($pi_id_arr);
		$prev_btb_pi="SELECT a.internal_file_no, a.contract_no,b.id as pi_id ,b.total_amount
		from com_sales_contract a 
		left join  COM_PI_MASTER_DETAILS b on a.id = b.lc_sc_id and b.is_lc_sc = 2 
		and  b.status_active=1 and b.is_deleted=0 
		where a.beneficiary_name=$data[0] $file_no_cond $file_no_scyear_cond and b.id not in ($all_pi_id) and b.id<$max_pi_id and a.status_active=1 and a.is_deleted=0";
		$prev_btb_pi_arr=sql_select($prev_btb_pi);
		$prv_pi_total=array();
		foreach ($prev_btb_pi_arr as $row) 
		{			
			$prv_pi_total[$row[csf('internal_file_no')]]+=$row[csf('total_amount')];
		}

		
		$sc_con_sql="SELECT a.id,a.internal_file_no, a.contract_no ,a.contract_date, a.buyer_name, a.contract_value, a.convertible_to_lc, a.last_shipment_date,a.lien_bank , sum(b.attached_qnty) as qty ,c.id as pi_id
		from com_sales_contract a , COM_SALES_CONTRACT_ORDER_INFO b,COM_PI_MASTER_DETAILS c
		where a.id = b.com_sales_contract_id and  b.com_sales_contract_id = c.lc_sc_id and a.beneficiary_name=$data[0] $file_no_cond $file_no_scyear_cond and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1
		group by a.id,a.internal_file_no, a.contract_no ,a.contract_date, a.buyer_name, a.contract_value, a.convertible_to_lc, a.last_shipment_date,a.lien_bank,c.id";

		//echo $sc_con_sql;
		$sc_con_sql_arr=sql_select($sc_con_sql);
		$sc_po_qty_arr=array(); 
		foreach ($sc_con_sql_arr as $key => $value) 
		{
			if ($value[csf('convertible_to_lc')]==2) // Convertible to = No (SC [Direct])
			{
				$sc_po_qty_arr[$value[csf('internal_file_no')]][$value[csf('pi_id')]]+=$value[csf('qty')];
			}	
		}

		$sc_val_Sql_ship="SELECT a.internal_file_no,a.buyer_name , max(a.last_shipment_date) as shipment_date, a.lien_bank from com_sales_contract a
        where a.beneficiary_name=$data[0] $file_no_cond $file_no_scyear_cond and a.status_active=1 and a.is_deleted=0 group by a.internal_file_no,a.buyer_name , a.lien_bank";
		//echo $sc_val_Sql_ship;
		$sc_val_data_arr_shp=sql_select($sc_val_Sql_ship);
		foreach ($sc_val_data_arr_shp as $key => $value) 
		{			
			$sc_shipment_arr[$value[csf('internal_file_no')]]=$value[csf('shipment_date')];
			$sc_bank = $sc_opning_bank[$value[csf('internal_file_no')]]=$value[csf('lien_bank')];
		}
		/*echo "<pre>";
		print_r($$approved_date_arr);die;*/

		// ==================================Approval==========================================
		
		$app_sql="SELECT b.mst_id, b.approved_no, b.approved_date from commercial_office_note_mst a, approval_history b where a.id=b.mst_id and a.id=$data[1] and a.is_approved=1 and b.current_approval_status=1";
		$app_data_arr=sql_select($app_sql);
		$approved_no_arr=array(); $approved_date_arr=array();
		foreach ($app_data_arr as $key => $value) 
		{
			$approved_no_arr[$value[csf('mst_id')]]=$value[csf('approved_no')];
			$approved_date_arr[$value[csf('mst_id')]]=$value[csf('approved_date')];
		}
        //echo "<pre>";print_r($approved_date_arr);
		// =============================================================================
		?>
		<style type="text/css">			
			.parent {
			  display: flex;
			  flex-direction:row;
			}

			.column {
			  flex: 1 1 0px;
			}
			.alignment {
			  vertical-align: middle;
			  text-align: center;
			}
			.verticalalign {
			  vertical-align: middle;
			}
		</style>
		<div style="width:1000px; margin-left:10px; height: 80px;" class="parent">
			<div width="250">
				<table width="250" cellspacing="0" align="center">
					<tr>
						<td rowspan="3" width="70">
						<img src="../../<? echo $image_location; ?>" height="70" width="200"></td>
					</tr>
				</table>
			</div>
			<div width="250" class="column">
				<table width="300" cellspacing="0" align="left">
					<tr>
						<td colspan="2" style="font-size:x-large;" align="left"><strong><? echo $company_name; ?></strong></td>
					</tr>
					<tr class="form_caption">
						<td colspan="2" align="left" style="font-size:14px"><? echo $location_name; //.",".$address; ?></td>
					</tr>
				</table>
			</div>
			<div width="250" class="column" align="right">
				<table width="300" cellspacing="1" align="right" class="rpt_table" rules="all">
					<tr>
						<td colspan="2" align="left"><strong>Office Note Date</strong></td>
						<td>&nbsp<? echo change_date_format(implode("",$note_date_arr)); ?></td>
					</tr>
					<tr>
						<td colspan="2" align="left"><strong>Office Note No.</strong></td>
						<td>&nbsp<? echo $data_arr[0][CON_SYSTEM_ID]; ?></td>
					</tr>
					<tr>
						<td colspan="2" align="left"><strong>Approval No.</strong></td>
						<td>&nbsp<? echo $approved_no_arr[$data[1]]; ?></td>
					</tr>
					<tr>
						<td colspan="2" align="left"><strong>Last App. Date & Time</strong></td>
						<td>&nbsp<? $dateFormate=strtotime($approved_date_arr[$data[1]]);
						if ($dateFormate!="") 
						{
							echo date("d-M-Y, h:i:s a", $dateFormate);
						}?></td>
					</tr>
				</table>
			</div>
      	</div>

		<br>
        <div style="width:1390px; margin-left:10px">
          	<div class="parent"><h2>Office Note to open <span style="color: red;"><? echo $lc_type[implode("",$lc_type_arr)]; ?></h2></span>
          		<div class="column" align="right"><h1><span style="color: red;">
          			<? if($data_arr[0][IS_APPROVED]==1) { echo "Approved"; } if($data_arr[0][IS_APPROVED]==3) { echo "Partial Approved"; } ?>
          		</h1></span></div>
          	</div>   

			<table class="rpt_table" width="100%" cellspacing="1" rules="all" border="1">
				<thead>
					<tr>
						<th width="40">Sl. No.</th>
						<th width="60">Our Ref. File No.</th>
						<th width="130">File Details</th>
						<th width="60">BTB Entitled %</th>
						<th width="60">Prev. BTB LC %</th>
						<th width="60">Propossed LC %</th>
						<th width="60">BTB LC Fund Avl. %</th>
						<th width="80">Item Category</th>
						<th width="80">PI No. & Date</th>
						<th width="80">Supplier Name</th>
						<th width="50">Tenor</th>
						<th width="100">Job & Booking</th>						
						<th width="100">Item</th>						
						<th width="70">PI Qty</th>
						<th width="50">Rate</th>						
						<th width="70">PI Value</th>
						<th width="70">Last Shipment Date</th>
						<th width="70">LC Opening Bank Name</th>
						<th>Remarks</th>						
					</tr>
				</thead>
				<tbody>
					<?
					$i = 1;
					$total_quantity=$total_ammount = 0;
					foreach($office_note_data_arr as $file_no_key => $file_no)
					{
						foreach ($file_no as $pi_id => $pi_data) 
						{
							
							foreach ($pi_data as $item_des => $item_des_data)
							{
								$j=1;
								$rowspan_val=count($item_des_data);
								$total_pi_amnt=0;
								foreach ($item_des_data as $rate => $row)
								{	
									?>
									<tr>
									<?
									//echo $rate.'**'.$j.'system';
									$buyer = $buyer_lib[$buyer_data_arr[$file_no_key][$pi_id]];							
									$sc_finance=$lcsc_finance_value_arr[$file_no_key][$pi_id];
									$sc_lc_replace=$sc_lc_replace_value_arr[$file_no_key][$pi_id];
									$sc_replace=$sc_replace_value_arr[$file_no_key][$pi_id];
									$sc_replace_bal=($sc_finance-$sc_lc_replace);
									$sc_direct=$sc_direct_value_arr[$file_no_key][$pi_id];
									$lc_replace=$lc_replace_arr[$file_no_key][$pi_id];
									$lc_replace_bal=($sc_replace-$lc_replace);
									$lc_direct=$lc_direct_arr[$file_no_key][$pi_id];									
									$sc_cont_no=$sc_cont_no_arr[$file_no_key][$pi_id];
									$sc_cont_dt=$sc_cont_date_arr[$file_no_key][$pi_id];
									$convertible_to_lc_check=$convertible_to_lc_arr[$file_no_key][$pi_id];
									$sc_po_qty=$sc_po_qty_arr[$file_no_key][$pi_id];
									$lc_po_qty=$lc_po_qty_arr[$file_no_key][$pi_id];
									$btb_entitled=$sc_max_btb_limit_arr[$file_no_key][$pi_id];
								
									// $buyer = $buyer_lib[$buyer_data_arr[$file_no_key]];							
									// $sc_finance=$lcsc_finance_value_arr[$file_no_key];
									// $sc_lc_replace=$sc_lc_replace_value_arr[$file_no_key];
									// $sc_replace=$sc_replace_value_arr[$file_no_key];
									// $sc_replace_bal=($sc_finance-$sc_lc_replace);
									// $sc_direct=$sc_direct_value_arr[$file_no_key];
									// $lc_replace=$lc_replace_arr[$file_no_key];
									// $lc_replace_bal=($sc_replace-$lc_replace);
									// $lc_direct=$lc_direct_arr[$file_no_key];									
									// $sc_cont_no=$sc_cont_no_arr[$file_no_key];
									// $sc_cont_dt=$sc_cont_date_arr[$file_no_key];
									// $convertible_to_lc_check=$convertible_to_lc_arr[$file_no_key];
									// $sc_po_qty=$sc_po_qty_arr[$file_no_key];
									// $lc_po_qty=$lc_po_qty_arr[$file_no_key];
									// $lc_po_qty=$lc_po_qty_arr[$file_no_key];
									// $btb_entitled=$sc_max_btb_limit_arr[$file_no_key];
															
									if($convertible_to_lc_check==2){ // convertible_to_lc_check NO
										 $po_atc_qty = $sc_po_qty;
										 $last_shipment_dt = $sc_shipment_arr[$file_no_key];
										 $opening_bank = return_field_value("bank_name", "lib_bank", "id=$sc_bank");
										 $bank_branch = return_field_value("branch_name", "lib_bank", "id=$sc_bank");
									}
									else{
										 $po_atc_qty = $lc_po_qty;
										 $last_shipment_dt = $lc_shipment_arr[$file_no_key];
										 $opening_bank = return_field_value("bank_name", "lib_bank", "id=$lc_bank");
										 $bank_branch = return_field_value("branch_name", "lib_bank", "id=$lc_bank");
									}
									
									$sc_replace_bal_absulate_value=0;
									if ($sc_replace_bal < 0) 
									{
										$sc_replace_bal_absulate_value=abs($sc_replace_bal);
									}

									$file_val = $sc_finance+$sc_replace_bal_absulate_value+$sc_direct+$lc_direct;

									if ($btb_entitled>0) 
									{
										$btb_reduce_perct_value = ($sc_replace*$btb_entitled)/100;
									    //	$pi_total =  $pi_rate_arr[$file_no_key][$pi_id]['pi_amount']; 
										//$propossed_value = (100*$pi_total)/$btb_reduce_perct_value;
										$pi_total =  $pi_rate_arr[$file_no_key]['pi_amount']; 
										$propossed_value = $pi_total/$file_val*100;

										//$prev_btb_lc=$lc_value_arr[$pi_id]/$file_val*100;
										$prev_btb_lc=$prv_pi_total[$file_no_key]/$file_val*100;

										
	
									}
									else
									{
										$prev_btb_lc='0.00';
										$propossed_value='0.00';
									}

									if ($j==1) 
									{
										?>
										<td width="40" class="alignment" rowspan="<? echo $rowspan_val; ?>"><? echo $i; ?></td>
										<td width="60" class="alignment" rowspan="<? echo $rowspan_val; ?>"><? echo $file_no_key; ?></td>
										<td width="130" title="File Value: SC[Finance]+SC Replace Bal(if minus value of SC Replace Bal then add absolute value of SC Replace Bal)+SC [Direct]+LC Direct" rowspan="<? echo $rowspan_val; ?>">
											<? 
											echo "Buyer Name. ".$buyer.'<br>'; 
											echo "S/C NO. ".$sc_cont_no.'<br>';
											echo "S/C Date. ".change_date_format($sc_cont_dt).'<br>';
											echo "S/C Qty. ".number_format($po_atc_qty,2).'<br>';
											echo "S/C Value. ".number_format($sc_replace,2).'<br>';
											
											// echo "Buyer Name. ".$buyer.'<br>'; 
											// echo "SC[Finance]: ".$sc_finance.'<br>';
											// echo "SC [Replace]: ".$sc_replace.'<br>';
											// echo "SC Replace Bal.: ".$sc_replace_bal.'<br>';
											// echo "SC [Direct]: ".$sc_direct.'<br>';
											// echo "LC [Replace]: ".$lc_replace.'<br>';
											// echo "LC Replace Bal.: ".$lc_replace_bal.'<br>';
											// echo "LC Direct: ".$lc_direct."<br>";
											// echo "File Value: ".$file_val.'<br>';

											?>									
										</td>
										<td width="60" class="alignment" rowspan="<? echo $rowspan_val; ?>">
											<? 
											echo number_format($btb_entitled,2)." %"; 
											 ?>
										</td>
										<td width="60" title="(BTB Value/SC Value)*100" class="alignment" rowspan="<? echo $rowspan_val; ?>">
											<? 
											echo number_format($prev_btb_lc,2).'%';
											?>	
										</td>
										<td width="60" title="(Total PI Value/SC Value)*100" class="alignment" rowspan="<? echo $rowspan_val; ?>" title="<? echo $amount_data_arr[$file_no_key]; ?>">
											<? 
											echo number_format($propossed_value,2). " %";
											?>
										</td>
										<td width="60" title="BTB Entitled - Prev. BTB LC" class="alignment" rowspan="<? echo $rowspan_val; ?>">
											<? 
											$Fund_Avl=($btb_entitled-$prev_btb_lc);
											//$Fund_Avl=($btb_entitled-($prev_btb_lc+$propossed_value));
										    	//$Fund_Avl=(100-($prev_btb_lc+$propossed_value));
											echo number_format($Fund_Avl,2).'%'; 
											?>
										</td>
										<td width="80" class="alignment" rowspan="<? echo $rowspan_val; ?>"><? echo $item_category[$main_data_arr[$file_no_key][$pi_id]['item_category_id']]; ?></td>
										<td width="80" class="alignment" rowspan="<? echo $rowspan_val; ?>"><? echo 'PI No: '.$main_data_arr[$file_no_key][$pi_id]['pi_number'].'<br>Date: '.change_date_format($main_data_arr[$file_no_key][$pi_id]['pi_date']); ?></td>
										<td width="80" class="alignment" rowspan="<? echo $rowspan_val; ?>"><? echo $supplier_lib[$main_data_arr[$file_no_key][$pi_id]['supplier_id']]; ?></td>
										<td width="50" class="alignment" rowspan="<? echo $rowspan_val; ?>"><? echo $main_data_arr[$file_no_key][$pi_id]['tenor']; ?></td>
										<td width="100" class="alignment" rowspan="<? echo $rowspan_val; ?>">
											<? 
											if ($main_data_arr[$file_no_key][$pi_id]['item_category_id'] == 1) {
												echo implode(',',array_unique(explode(',',rtrim($booking_no_arr[$file_no_key][$pi_id]['job_no'],',')))).'</br>&</br>'.implode(',',array_unique(explode(',',rtrim($booking_no_arr[$file_no_key][$pi_id]['booking_no'],','))));
											}										 
											else
											{
												echo implode(',',array_unique(explode(',',rtrim($booking_no_arr[$file_no_key][$pi_id]['job_no'],',')))).'</br>&</br>'.implode(',',array_unique(explode(',',rtrim($main_data_arr[$file_no_key][$pi_id]['work_order_no'],',')))); 
											}
											?>											
										</td>								
										<td width="100" class="verticalalign" rowspan="<? echo $rowspan_val; ?>"><? echo $item_des; ?></td>									
				                        <?
				                    }
									?>									
									<td width="70" align="right" class="verticalalign"><? echo number_format($row['quantity'],2); $total_quantity += $row['quantity'];?></td>
									<td width="60" align="right" class="verticalalign"><? echo $row['pi_rate']; ?></td>
									<td width="70" align="right" class="verticalalign"><? echo number_format($row['pi_amount'],4);  $total_ammount += $row['pi_amount'];?></td>
									
									
									<?
									if ($j==1)
									{
										?>
										<td width="70" align="center" class="alignment" rowspan="<? echo $rowspan_val; ?>"><? echo change_date_format($last_shipment_dt);?></td>

										<td width="70" align="center" class="alignment" rowspan="<? echo $rowspan_val; ?>"><?if($opening_bank) echo $opening_bank. "(".$bank_branch.")";?></td>

										<td class="alignment" rowspan="<? echo $rowspan_val; ?>"><p><? echo $main_data_arr[$file_no_key][$pi_id]['remarks']; ?></p></td> 
										<?
									}
									?>

									</tr>								
								
									<?										
									$j++;									
									
								}
								$i++;
							}						
						}
					}
					?>
					<tr class="tbl_bottom" height="25">
						<td colspan="13">Grand Total: </td>
						<td align="right"><? echo number_format($total_quantity,2); ?></td>
						<td></td>
						<td align="right"><? echo number_format($total_ammount,2); ?></td>
						<td></td>
						<td></td>
						<td></td>
					</tr>
				</tbody>
			</table>
            <?
            function signature_table_approval($mst_id, $company, $width, $prepared_by, $padding_top = 70) 
            {	
				$sql_user = sql_select("select b.id, b.user_name, b.user_full_name, b.designation from user_passwd b where b.status_active=1");
				$user_arr=array();
				foreach ($sql_user as $val) {
					$user_arr[$val[csf("id")]]['user_full_name']=$val[csf("user_full_name")];
					$user_arr[$val[csf("id")]]['designation']=$val[csf("designation")];
				}
				
				$sql = sql_select("select max(a.approved_no) as approved_no,  a.approved_date, a.approved_by, a.sequence_no, b.inserted_by, b.insert_date from approval_history a, commercial_office_note_mst b where b.id=a.mst_id and a.mst_id=$mst_id and b.importer_id=$company and b.is_approved !=0 and a.entry_form=39 and a.approved_no in(select max(a.approved_no) as approved_no from approval_history a where a.entry_form=39 and a.mst_id=$mst_id) group by a.approved_by,  a.approved_date, a.sequence_no, b.inserted_by, b.insert_date order by a.sequence_no");
				$sql_2=sql_select("select b.inserted_by as user_name, b.insert_date as user_date from commercial_office_note_mst b where b.id=$mst_id and b.importer_id=$company and b.status_active=1 and b.is_deleted=0 and b.is_approved !=0");
					
				$lib_designation_arr=return_library_array("select id, custom_designation from lib_designation","id","custom_designation");
				$sql_2_arr=array();
				if ($sql_2){					
					$sql_2_arr[500] = array ( APPROVED_BY => 'Prepared By', USER_NAME => $user_arr[$sql_2[0][csf("user_name")]]['user_full_name'], USER_DATE =>$sql_2[0][csf("user_date")]);
				}
				
				$sql_arr=array();
				foreach ($sql as $row) {
					$sql_arr[]=array ( APPROVED_BY => $user_arr[$row[csf("approved_by")]]['user_full_name'], USER_NAME => $lib_designation_arr[$user_arr[$row[csf("approved_by")]]['designation']], USER_DATE =>$row[csf('approved_date')]);
				}
				$sql_rs=$sql_2_arr+$sql_arr;
				//echo '<pre>';print_r($sql_rs);

				$count = count($sql_rs);
				$td_width = floor($width / $count);
				$standard_width = $count * 100;
				if ($standard_width > $width) {
					$td_width = 100;
				}
				$no_coloumn_per_tr = floor($width / $td_width);
				//echo $no_coloumn_per_tr;
				$i = 1;
				if ($count == 0) {$message = "<b>Note: This is Software Generated Copy , Signature is not Required.</b>";}
				echo '<table id="signatureTblId" width="' . $width . '" style="padding-top:' . $padding_top . 'px;"><tr><td width="100%" height="' . $padding_top . '" colspan="' . $count . '">' . $message . '</td></tr><tr>';
				foreach ($sql_rs as $row) {
					echo '<td width="' . $td_width . '" align="center" valign="top">
					<strong>' . $row[csf("approved_by")] . "</strong><br>" . $row[csf("user_name")] . "</strong><br>" . $row[csf('user_date')] . '</td>';
					if ($i % $no_coloumn_per_tr == 0) {
						echo '</tr><tr><td width="100%" height="70" colspan="' . $no_coloumn_per_tr . '"></td></tr>';
					}
					$i++;
				}
				echo '</tr></table>';
			}

			$com_office_note_varriable=return_field_value("pi_source_btb_lc","variable_settings_commercial","company_name=$data[0] and variable_list=29 and status_active=1 and is_deleted=0", "pi_source_btb_lc");
			if ($com_office_note_varriable == 2) echo signature_table_approval($data[1], $data[0],"1250px"); // From Approval
			else echo signature_table(177, $data[0], "1250px",$cbo_template_id,70,$user_lib_name[$inserted_by]);  // From Library          
            //echo signature_table(177, $data[0],"900px");
            ?>
        </div>
	</div>
	<?
    exit();
}
?>


 