<?
header('Content-type:text/html; charset=utf-8');
session_start();
if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");

include('../../includes/common.php');

$data=$_REQUEST['data'];
$action=$_REQUEST['action'];

$color_arr=return_library_array( "select id, color_name from lib_color where status_active=1 and is_deleted=0",'id','color_name');
$buyer_arr=return_library_array( "select id, buyer_name from lib_buyer",'id','buyer_name');
$batch_arr = return_library_array("select id, batch_no from pro_batch_create_mst","id","batch_no");

//====================Location ACTION========
if ($action=="load_drop_down_location")
{
	$data=explode("_",$data);
	echo create_drop_down( "cbo_location", 150, "select id,location_name from lib_location where company_id='$data[0]' and status_active =1 and is_deleted=0 order by location_name","id,location_name", 1, "-- Select Location --", 0, "" );
	exit();
}
//====================DYEING COMPANY ACTION========
if($action=="load_drop_down_dyeing_com")
{
	$data = explode("_",$data);
	$company_id=$data[1];

	if($data[0]==1)
	{
		echo create_drop_down( "cbo_dyeing_company", 160, "select comp.id, comp.company_name from lib_company comp where comp.status_active=1 and comp.is_deleted=0 $company_cond order by comp.company_name","id,company_name",1, "--Select Dyeing Company--", "$company_id", "","" );
	}
	else if($data[0]==3)
	{
		echo create_drop_down( "cbo_dyeing_company", 160, "select id, supplier_name from lib_supplier where find_in_set(21,party_type) and find_in_set($company_id,tag_company) and status_active=1 and is_deleted=0","id,supplier_name", 1, "--Select Dyeing Company--", 1, "" );
	}
	else
	{
		echo create_drop_down( "cbo_dyeing_company", 160, $blank_array,"",1, "--Select Dyeing Company--", 1, "" );
	}
	exit();
}

if ($action=="load_drop_machine")
{
	echo create_drop_down( "cbo_machine_name", 182, "select id,concat(machine_no,'-',brand) as machine_name from lib_machine_name where category_id=2 and company_id=$data and status_active=1 and is_deleted=0 and is_locked=0 order by machine_name","id,machine_name", 1, "-- Select Machine --", 0, "","" );
	exit();
}

//====================SYSTEM ID POPUP========
if ($action=="systemId_popup")
{
	echo load_html_head_contents("System ID Info", "../../", 1, 1,'','','');
	extract($_REQUEST);
?>
	<script>
		function js_set_value(id)
		{
			$('#hidden_sys_id').val(id);
			parent.emailwindow.hide();
		}
    </script>
</head>

<body>
<div align="center" style="width:840px;">
    <form name="searchsystemidfrm"  id="searchsystemidfrm">
        <fieldset style="width:830px;">
        <legend>Enter search words</legend>
            <table cellpadding="0" cellspacing="0" width="800" class="rpt_table">
                <thead>
                    <th>Received Date Range</th>
                    <th>Search By</th>
                    <th id="search_by_td_up">Enter System Id</th>
                    <th>
                        <input type="reset" name="reset" id="reset" value="Reset" style="width:100px;" class="formbutton" />
                        <input type="hidden" name="txt_company_id" id="txt_company_id" class="text_boxes" value="<? echo $cbo_company_id; ?>">
                        <input type="hidden" name="hidden_sys_id" id="hidden_sys_id" class="text_boxes" value="">
                    </th>
                </thead>
                <tr class="general">
                    <td>
                        <input type="text" name="txt_date_from" id="txt_date_from" class="datepicker" style="width:70px;">To<input type="text" name="txt_date_to" id="txt_date_to" class="datepicker" style="width:70px;">
                    </td>
                    <td>
						<?
							$search_by_arr=array(1=>"System ID",2=>"Challan No.");
							$dd="change_search_event(this.value, '0*0', '0*0', '../../') ";
							echo create_drop_down( "cbo_search_by", 150, $search_by_arr,"",0, "--Select--", "",$dd,0 );
                        ?>
                    </td>
                    <td id="search_by_td">
                        <input type="text" style="width:130px;" class="text_boxes"  name="txt_search_common" id="txt_search_common" />
                    </td>
                    <td>
                        <input type="button" name="button2" class="formbutton" value="Show" onClick="show_list_view ( document.getElementById('txt_search_common').value+'_'+document.getElementById('cbo_search_by').value+'_'+document.getElementById('txt_date_from').value+'_'+document.getElementById('txt_date_to').value+'_'+document.getElementById('txt_company_id').value, 'create_dye_search_list_view', 'search_div', 'dye_prod_update_controller', 'setFilterGrid(\'tbl_list_search\',-1);')" style="width:100px;" />
                    </td>
                </tr>
                <tr>
                    <td colspan="5" align="center" height="40" valign="middle"><? echo load_month_buttons(1); ?></td>
                </tr>
            </table>
            <table width="100%" style="margin-top:5px;">
                <tr>
                    <td colspan="5">
                        <div style="width:100%; margin-top:10px; margin-left:3px;" id="search_div" align="left"></div>
                    </td>
                </tr>
            </table>
    	</fieldset>
    </form>
</div>
</body>
<script src="../../includes/functions_bottom.js" type="text/javascript"></script>
</html>
<?
}

if($action=="create_dye_search_list_view")
{
	$data = explode("_",$data);
	$search_string="%".trim($data[0])."%";
	$search_by=$data[1];
	$start_date =$data[2];
	$end_date =$data[3];
	$company_id =$data[4];

	if($start_date!="" && $end_date!="")
	{
		$date_cond="and received_date between '".change_date_format(trim($start_date), "yyyy-mm-dd", "-")."' and '".change_date_format(trim($end_date), "yyyy-mm-dd", "-")."'";
	}
	else
	{
		$date_cond="";
	}
	
	if(trim($data[0])!="")
	{
		if($search_by==1)
			$search_field_cond="and dye_system_id like '$search_string'";
		else
			$search_field_cond="and challan_no like '$search_string'";
	}
	else
	{
		$search_field_cond="";
	}
	
	$company_arr=return_library_array( "select id, company_name from lib_company",'id','company_name');
	$supplier_arr=return_library_array( "select id, supplier_name from lib_supplier",'id','supplier_name');
	
	$sql = "select id, dye_system_id, recieve_basis, received_date, challan_no, dyeing_source, dyeing_company from pro_dyeing_update_mst where company_id='$company_id' and status_active=1 and is_deleted=0 $search_field_cond $date_cond"; 
	//echo $sql;die;
	?>
    <div>
        <table cellspacing="0" cellpadding="0" border="1" rules="all" width="800" class="rpt_table" >
            <thead>
                <th width="50">SL</th>
                <th width="130">System ID</th>
                <th width="110">Production Basis</th>
                <th width="90">Production Date</th>
                <th width="110">Challan No.</th>
                <th width="110">Dyeing Source</th>
                <th>Dyeing Company</th>
            </thead>
        </table>
        <div style="width:820px; overflow-y:scroll; max-height:210px;" id="buyer_list_view" align="center">
            <table cellspacing="0" cellpadding="0" border="1" rules="all" width="800" class="rpt_table" id="tbl_list_search" >
            <?
				$i=1;
				$nameArray=sql_select( $sql );
				foreach ($nameArray as $selectResult)
				{
					if ($i%2==0)  
						$bgcolor="#E9F3FF";
					else
						$bgcolor="#FFFFFF";
						
					$dye_company='';
					
					if($selectResult[csf('dyeing_source')]==1)
					{
						$dye_company=$company_arr[$selectResult[csf('dyeing_company')]];
					}
					else
					{
						$dye_company=$supplier_arr[$selectResult[csf('dyeing_company')]];
					}
					
					?>
                    <tr bgcolor="<? echo $bgcolor; ?>" style="text-decoration:none; cursor:pointer" onClick="js_set_value(<? echo $selectResult[csf('id')]; ?>)"> 
                        <td width="50" align="center"><? echo $i; ?></td>	
                        <td width="130"><p><? echo $selectResult[csf('dye_system_id')]; ?></p></td>
                        <td width="110"><? echo $receive_basis_arr[$selectResult[csf('recieve_basis')]]; ?></td>
                        <td width="90"><? echo change_date_format($selectResult[csf('received_date')]); ?></td> 
                        <td width="110"><p><? echo $selectResult[csf('challan_no')]; ?></p></td>
                        <td width="110"><p><? echo $knitting_source[$selectResult[csf('dyeing_source')]]; ?></p></td>
                        <td><p><? echo $dye_company; ?></p></td>	
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

if($action=='populate_data_from_dye_update')
{
	
	$data_array=sql_select("select id, dye_system_id, company_id, recieve_basis, received_date, challan_no, dyeing_source, dyeing_company, location_id, remarks from pro_dyeing_update_mst where id='$data'");
	foreach ($data_array as $row)
	{ 

		echo "document.getElementById('txt_system_id').value 				= '".$row[csf("dye_system_id")]."';\n";
		echo "document.getElementById('cbo_production_basis').value 		= '".$row[csf("recieve_basis")]."';\n";
		echo "document.getElementById('cbo_company_id').value 				= '".$row[csf("company_id")]."';\n";
		echo "$('#cbo_company_id').attr('disabled','true')".";\n";
		echo "set_production_besis();\n";
		echo "load_drop_down('requires/dye_prod_update_controller', $row[company_id], 'load_drop_down_location', 'location_td' );\n";
		
		echo "document.getElementById('txt_production_date').value 			= '".change_date_format($row[csf("received_date")])."';\n";
		echo "document.getElementById('cbo_dyeing_source').value 			= '".$row[csf("dyeing_source")]."';\n";
		
		echo "load_drop_down('requires/dye_prod_update_controller', $row[dyeing_source]+'_'+$row[company_id], 'load_drop_down_dyeing_com', 'dyeingcom_td' );\n";
		
		echo "document.getElementById('cbo_dyeing_company').value 			= '".$row[csf("dyeing_company")]."';\n";
		echo "document.getElementById('txt_challan_no').value 				= '".$row[csf("challan_no")]."';\n";
		echo "document.getElementById('cbo_location').value 				= '".$row[csf("location_id")]."';\n";
		echo "document.getElementById('txt_remarks').value 					= '".$row[csf("remarks")]."';\n";
		echo "document.getElementById('update_id').value 					= '".$row[csf("id")]."';\n";
		
		echo "set_button_status(0, '".$_SESSION['page_permission']."', 'fnc_dye_production',1);\n";  
		exit();
	}
}

if ($action=="batch_number_popup")
{
	echo load_html_head_contents("Batch Number Info", "../../", 1, 1,'','','');
	extract($_REQUEST);
?>
	<script>
		function js_set_value(id)
		{
			$('#hidden_batch_id').val(id);
			parent.emailwindow.hide();
		}
    </script>
</head>

<body>
<div align="center" style="width:800px;">
    <form name="searchbatchnofrm"  id="searchbatchnofrm">
        <fieldset style="width:790px;">
        <legend>Enter search words</legend>
            <table cellpadding="0" cellspacing="0" width="770" border="1" rules="all" class="rpt_table">
                <thead>
                    <th width="200px">Batch Date Range</th>
                    <th width="160px">Search By</th>
                    <th id="search_by_td_up" width="180">Enter Batch No</th>
                    <th>
                        <input type="reset" name="reset" id="reset" value="Reset" style="width:100px;" class="formbutton" />
                        <input type="hidden" name="txt_company_id" id="txt_company_id" class="text_boxes" value="<? echo $cbo_company_id; ?>">
                        <input type="hidden" name="hidden_batch_id" id="hidden_batch_id" class="text_boxes" value="">
                    </th>
                </thead>
                <tr>
                    <td align="center">
                        <input type="text" name="txt_date_from" id="txt_date_from" class="datepicker" style="width:70px;">To<input type="text" name="txt_date_to" id="txt_date_to" class="datepicker" style="width:70px;">
                    </td>
                    <td align="center" width="160px">
						<?
							$search_by_arr=array(0=>"Batch No",1=>"Fabric Booking no.",2=>"Color");
							$dd="change_search_event(this.value, '0*0*0', '0*0*0', '../../') ";
							echo create_drop_down( "cbo_search_by", 150, $search_by_arr,"",0, "--Select--", "",$dd,0 );
                        ?>
                    </td>
                    <td align="center" id="search_by_td" width="140px">
                        <input type="text" style="width:130px;" class="text_boxes"  name="txt_search_common" id="txt_search_common" />
                    </td>
                    <td align="center">
                        <input type="button" name="button2" class="formbutton" value="Show" onClick="show_list_view ( document.getElementById('txt_search_common').value+'_'+document.getElementById('cbo_search_by').value+'_'+document.getElementById('txt_date_from').value+'_'+document.getElementById('txt_date_to').value+'_'+document.getElementById('txt_company_id').value, 'create_batch_search_list_view', 'search_div', 'dye_prod_update_controller', 'setFilterGrid(\'tbl_list_search\',-1);')" style="width:100px;" />
                    </td>
                </tr>
                <tr>
                    <td colspan="5" align="center" height="40" valign="middle"><? echo load_month_buttons(1); ?></td>
                </tr>
            </table>
            <table width="100%" style="margin-top:5px;">
                <tr>
                    <td colspan="5">
                        <div style="width:100%; margin-left:3px;" id="search_div" align="left"></div>
                    </td>
                </tr>
            </table>
        </fieldset>
    </form>
</div>
</body>
<script src="../../includes/functions_bottom.js" type="text/javascript"></script>
</html>
<?
}

if($action=="create_batch_search_list_view")
{
	$data = explode("_",$data);
	$search_string="%".trim($data[0])."%";
	$search_by=$data[1];
	$start_date =$data[2];
	$end_date =$data[3];
	$company_id =$data[4];

	if($start_date!="" && $end_date!="")
	{
		$date_cond="and batch_date between '".change_date_format(trim($start_date), "yyyy-mm-dd", "-")."' and '".change_date_format(trim($end_date), "yyyy-mm-dd", "-")."'";
	}
	else
	{
		$date_cond="";
	}
	
	if(trim($data[0])!="")
	{
		if($search_by==0)
			$search_field_cond="and batch_no like '$search_string'";
		else if($search_by==1)
			$search_field_cond="and booking_no like '$search_string'";
		else
			$search_field_cond="and color_id in(select id from lib_color where color_name like '$search_string')";
	}
	else
	{
		$search_field_cond="";
	}
	
	$sql = "select id, batch_no, batch_date, batch_weight, booking_no, extention_no, color_id, batch_against, re_dyeing_from from pro_batch_create_mst where batch_for in(0,1) and entry_form=0 and batch_against<>4 and company_id=$company_id and status_active=1 and is_deleted=0 $search_field_cond $date_cond"; 
	//echo $sql;die;
	?>
    <div>
        <table cellspacing="0" cellpadding="0" border="1" rules="all" width="750" class="rpt_table" >
            <thead>
                <th width="40">SL</th>
                <th width="100">Batch No</th>
                <th width="80">Extention No</th>
                <th width="80">Batch Date</th>
                <th width="90">Batch Qnty</th>
                <th width="115">Booking No</th>
                <th width="80">Color</th>
                <th>Po No</th>
            </thead>
        </table>
        <div style="width:770px; overflow-y:scroll; max-height:230px;" id="buyer_list_view" align="center">
            <table cellspacing="0" cellpadding="0" border="1" rules="all" width="750" class="rpt_table" id="tbl_list_search" >
            <?
				$i=1;
				$nameArray=sql_select( $sql );
				foreach ($nameArray as $selectResult)
				{
					if ($i%2==0)  
						$bgcolor="#E9F3FF";
					else
						$bgcolor="#FFFFFF";
					
					$po_no='';
					
					if($selectResult['re_dyeing_from']==0)
					{	
						$sql_po="select a.po_number as po_no from wo_po_break_down a, pro_batch_create_dtls b where a.id=b.po_id and b.mst_id=$selectResult[id] and b.status_active=1 and b.is_deleted=0 group by a.id";
						$poArray=sql_select( $sql_po );
						foreach ($poArray as $row1)
						{
							if($po_no=='') $po_no=$row1[csf('po_no')]; else $po_no.=",".$row1[csf('po_no')];
						}
						?>
						<tr bgcolor="<? echo $bgcolor; ?>" style="text-decoration:none; cursor:pointer" onClick="js_set_value(<? echo $selectResult[csf('id')]; ?>)"> 
							<td width="40" align="center"><? echo $i; ?></td>	
							<td width="100"><p><? echo $selectResult[csf('batch_no')]; ?></p></td>
                            <td width="80"><p><? if($selectResult[csf('extention_no')]!=0) echo $selectResult[csf('extention_no')]; ?></p></td>
							<td width="80"><? echo change_date_format($selectResult[csf('batch_date')]); ?></td>
							<td width="90" align="right"><? echo $selectResult[csf('batch_weight')]; ?></td> 
							<td width="115"><p><? echo $selectResult[csf('booking_no')]; ?></p></td>
							<td width="80"><p><? echo $color_arr[$selectResult[csf('color_id')]]; ?></p></td>
							<td><? echo $po_no; ?></td>	
						</tr>
						<?
						$i++;
					}
					else
					{
						$sql_re= "select id, batch_no, batch_date, batch_weight, booking_no, extention_no, color_id, batch_against, re_dyeing_from from pro_batch_create_mst where  batch_for in(0,1) and entry_form=0 and batch_against<>4 and status_active=1 and is_deleted=0 and id=$selectResult[re_dyeing_from]";
						$dataArray=sql_select( $sql_re );
						foreach($dataArray as $row)
						{
							if($row['re_dyeing_from']==0)
							{
								$sql_po="select a.po_number as po_no from wo_po_break_down a, pro_batch_create_dtls b where a.id=b.po_id and b.mst_id=$row[id] and b.status_active=1 and b.is_deleted=0 group by a.id";
								$poArray=sql_select( $sql_po );
								foreach ($poArray as $row2)
								{
									if($po_no=='') $po_no=$row2[csf('po_no')]; else $po_no.=",".$row2[csf('po_no')];
								}
								?>
								<tr bgcolor="<? echo $bgcolor; ?>" style="text-decoration:none; cursor:pointer" onClick="js_set_value(<? echo $selectResult[csf('id')]; ?>)"> 
									<td width="40" align="center"><? echo $i; ?></td>	
									<td width="100"><p><? echo $selectResult[csf('batch_no')]; ?></p></td>
									<td width="80"><p><? if($selectResult[csf('extention_no')]!=0) echo $selectResult[csf('extention_no')]; ?></p></td>
									<td width="80"><? echo change_date_format($selectResult[csf('batch_date')]); ?></td>
									<td width="90" align="right"><? echo $selectResult[csf('batch_weight')]; ?></td> 
									<td width="115"><p><? echo $selectResult[csf('booking_no')]; ?></p></td>
									<td width="80"><p><? echo $color_arr[$selectResult[csf('color_id')]]; ?></p></td>
									<td><? echo $po_no; ?></td>	
								</tr>
								<?
								$i++;
							}
						}
					}
				}
			?>
            </table>
        </div>
	</div>           
<?

exit();
}

if($action=='populate_data_from_batch')
{
	$data_array=sql_select("select batch_no, batch_weight, color_id, booking_without_order from pro_batch_create_mst where id='$data'");
	foreach ($data_array as $row)
	{ 
		$prod_qnty=return_field_value("sum(receive_qnty)","pro_dyeing_update_dtls","batch_id=$data and status_active=1 and is_deleted=0");
		$yet_to_prod_qnty=$row[csf("batch_weight")]-$prod_qnty;
		
		echo "document.getElementById('txt_batch_no').value 				= '".$row[csf("batch_no")]."';\n";
		echo "document.getElementById('txt_batch_id').value 				= '".$data."';\n";
		echo "document.getElementById('txt_color').value 					= '".$color_arr[$row[csf("color_id")]]."';\n";
		echo "document.getElementById('txt_batch_qnty').value 				= '".$row[csf("batch_weight")]."';\n";
		echo "document.getElementById('txt_total_production').value 		= '".$prod_qnty."';\n";
		echo "document.getElementById('txt_yet_production').value 			= '".$yet_to_prod_qnty."';\n";
		echo "document.getElementById('batch_booking_without_order').value 	= '".$row[csf("booking_without_order")]."';\n";
		
		if($row[csf("booking_without_order")]==1)
		{
			echo "$('#txt_production_qty').removeAttr('readonly','readonly');\n";
			echo "$('#txt_production_qty').removeAttr('onClick','onClick');\n";	
			echo "$('#txt_production_qty').removeAttr('placeholder','placeholder');\n";		
		}
		else
		{
			echo "$('#txt_production_qty').attr('readonly','readonly');\n";
			echo "$('#txt_production_qty').attr('onClick','openmypage_po();');\n";	
			echo "$('#txt_production_qty').attr('placeholder','Single Click to Search');\n";	
		}
		
		exit();
	}
}

if($action=='show_fabric_desc_listview')
{
	$data_array=sql_select("select item_description, sum(batch_qnty) as qnty from pro_batch_create_dtls where mst_id='$data' and status_active=1 and is_deleted=0 group by item_description");
	?>
    <table class="rpt_table" border="1" cellpadding="0" cellspacing="0" rules="all" width="300">
        <thead>
            <th>SL</th>
            <th>Fabric Description</th>
            <th>Qnty</th>
        </thead>
        <tbody>
            <? 
            $i=1;
            foreach($data_array as $row)
            {  
                if ($i%2==0)  
                    $bgcolor="#E9F3FF";
                else
                    $bgcolor="#FFFFFF";
				
				$item_desc=explode(",",$row[csf('item_description')]);
				$body_part_id=array_search($item_desc[0], $body_part);
				$cons_comp=$item_desc[2];
				$gsm=$item_desc[3];
				$dia_width=$item_desc[4];
             ?>
                <tr bgcolor="<? echo $bgcolor; ?>" onClick='set_form_data("<? echo $body_part_id."**".$cons_comp."**".$gsm."**".$dia_width; ?>")' style="cursor:pointer" >
                    <td><? echo $i; ?></td>
                    <td><? echo $row[csf('item_description')]; ?></td>
                    <td align="right"><? echo $row[csf('qnty')]; ?></td>
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

if ($action=="fabricDescription_popup")
{
	echo load_html_head_contents("Fabric Description Info", "../../", 1, 1,'','','');
	extract($_REQUEST);
?> 

	<script>
		
		$(document).ready(function(e) {
            setFilterGrid('tbl_list_search',-1);
        });
		
		function js_set_value(comp,gsm,dia_width)
		{
			$('#hidden_desc_no').val(comp);
			$('#hidden_gsm').val(gsm);
			$('#hidden_dia_width').val(dia_width);
			parent.emailwindow.hide();
		}
	
    </script>

</head>

<body>
	<form name="searchdescfrm"  id="searchdescfrm">
		<fieldset style="width:520px;margin-left:10px">
			<?
                $data_array=sql_select("select detarmination_id, gsm, dia_width from product_details_master where item_category_id=13 and status_active=1 and is_deleted=0");
            ?>
            <input type="hidden" name="hidden_desc_no" id="hidden_desc_no" class="text_boxes" value="">  
            <input type="hidden" name="hidden_gsm" id="hidden_gsm" class="text_boxes" value=""> 
            <input type="hidden" name="hidden_dia_width" id="hidden_dia_width" class="text_boxes" value="">  
            
            <div style="margin-left:10px; margin-top:10px">
                <table class="rpt_table" border="1" cellpadding="0" cellspacing="0" rules="all" width="480">
                    <thead>
                        <th width="40">SL</th>
                        <th width="250">Construction</th>
                        <th width="100">GSM/Weight</th>
                        <th>Dia/Width</th>
                    </thead>
                </table>
                <div style="width:500px; max-height:280px; overflow-y:scroll" id="list_container" align="left"> 
                    <table class="rpt_table" border="1" cellpadding="0" cellspacing="0" rules="all" width="480" id="tbl_list_search">  
                        <? 
                        $i=1;
                        foreach($data_array as $row)
                        {  
                            if ($i%2==0)  
                                $bgcolor="#E9F3FF";
                            else
                                $bgcolor="#FFFFFF";

							$determination_sql=sql_select("select a.construction, b.copmposition_id, b.percent from lib_yarn_count_determina_mst a, lib_yarn_count_determina_dtls b where a.id=b.mst_id and a.id=$row[detarmination_id]");
				
							if($determination_sql[0][csf('construction')]!="")
							{
								$cons_comp=$determination_sql[0][csf('construction')].", ";
							}
							
							foreach( $determination_sql as $d_row )
							{
								$cons_comp.=$composition[$d_row[csf('copmposition_id')]]." ".$d_row[csf('percent')]."% ";
							}
							
                         ?>
                            <tr bgcolor="<? echo $bgcolor; ?>" onClick="js_set_value('<? echo $cons_comp; ?>','<? echo $row[csf('gsm')]; ?>','<? echo $row[csf('dia_width')]; ?>')" style="cursor:pointer" >
                                <td width="40"><? echo $i; ?></td>
                                <td width="250"><p><? echo $cons_comp; ?></p></td>
                                <td width="100"><? echo $row[csf('gsm')]; ?></td>
                                <td><p><? echo $row[csf('dia_width')]; ?></p></td>
                            </tr>
                        <? 
                        $i++; 
                        } 
                        ?>
                    </table>
                </div> 
            </div>
		</fieldset>
	</form>
</body>           
<script src="../../includes/functions_bottom.js" type="text/javascript"></script>
</html>
<?
exit();
}

if ($action=="po_popup")
{
	echo load_html_head_contents("PO Info", "../../", 1, 1,'','','');
	
	extract($_REQUEST);
	$data=explode("_",$data);
	$po_id=$data[0]; $type=$data[1];
	if($prev_distribution_method=="")
	{
		if($roll_maintained==1) $prev_distribution_method=2; else $prev_distribution_method=1;
	}
	
	if($type==1) 
	{
		$dtls_id=$data[2]; 
		$roll_maintained=$data[3]; 
		$save_data=$data[4]; 
		$prev_distribution_method=$data[5]; 
		$production_basis=$data[6]; 
	}
?>
	<script>
		var production_basis=<? echo $production_basis; ?>;
		var roll_maintained=<? echo $roll_maintained; ?>;
		
		function fn_show_check()
		{
			show_list_view ( document.getElementById('txt_search_common').value+'_'+document.getElementById('cbo_search_by').value+'_'+<? echo $cbo_company_id; ?>+'_'+document.getElementById('cbo_buyer_name').value+'_'+'<? echo $all_po_id; ?>', 'create_po_search_list_view', 'search_div', 'dye_prod_update_controller', 'setFilterGrid(\'tbl_list_search\',-1);hidden_field_reset();');
			set_all();
		}
		
		function distribute_qnty(str)
		{
			if(str==1 && roll_maintained==0)
			{
				var tot_po_qnty=$('#tot_po_qnty').val()*1;
				var txt_prop_dey_qnty=$('#txt_prop_dey_qnty').val()*1;
				var tblRow = $("#tbl_list_search tr").length;
				var len=totalDye=0;
				$("#tbl_list_search").find('tr').each(function()
				{
					len=len+1;
					
					var po_qnty=$(this).find('input[name="txtPoQnty[]"]').val()*1;
					var perc=(po_qnty/tot_po_qnty)*100;
					
					var dye_qnty=(perc*txt_prop_dey_qnty)/100;
					
					totalDye = totalDye*1+dye_qnty*1;
					totalDye = totalDye.toFixed(2);
											
					if(tblRow==len)
					{
						var balance = txt_prop_dey_qnty-totalDye;
						if(balance!=0) dye_qnty=dye_qnty+(balance);							
					}
						
					$(this).find('input[name="txtDyeQnty[]"]').val(dye_qnty.toFixed(2));
				});
			}
			/*else
			{
				$('#txt_prop_dey_qnty').val('');
				$("#tbl_list_search").find('tr').each(function()
				{
					$(this).find('input[name="txtDyeQnty[]"]').val('');
				});
			}*/
		}

		var selected_id = new Array();

		 function check_all_data() 
		 {
			var tbl_row_count = document.getElementById( 'tbl_list_search' ).rows.length;

			tbl_row_count = tbl_row_count-1;
			for( var i = 1; i <= tbl_row_count; i++ ) {
				js_set_value( i,1 );
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
			var old=document.getElementById('txt_po_row_id').value;
			if(old!="")
			{
				old=old.split(",");
				for(var i=0; i<old.length; i++)
				{
					js_set_value( old[i],0 )
				}
			}
		}

		function js_set_value( str )
		{

			toggle( document.getElementById( 'search' + str ), '#FFFFCC' );

			if( jQuery.inArray( $('#txt_individual_id' + str).val(), selected_id ) == -1 ) {
				selected_id.push( $('#txt_individual_id' + str).val() );
				
			}
			else {
				for( var i = 0; i < selected_id.length; i++ ) {
					if( selected_id[i] == $('#txt_individual_id' + str).val() ) break;
				}
				selected_id.splice( i, 1 );
			}
			var id = '';
			for( var i = 0; i < selected_id.length; i++ ) {
				id += selected_id[i] + ',';
			}
			id = id.substr( 0, id.length - 1 );

			$('#po_id').val( id );
		}

		function show_dye_prod_recv()
		{
			var po_id=$('#po_id').val();
			show_list_view ( po_id+'_'+'1'+'_'+'<? echo $dtls_id; ?>'+'_'+'<? echo $roll_maintained; ?>'+'_'+'<? echo $save_data; ?>'+'_'+'<? echo $prev_distribution_method; ?>'+'_'+'<? echo $production_basis; ?>', 'po_popup', 'search_div', 'dye_prod_update_controller', '');
		}

		function hidden_field_reset()
		{
			$('#po_id').val('');
			$('#save_string').val( '' );
			$('#tot_dye_qnty').val( '' );
			selected_id = new Array();
		}

		function fnc_close()
		{
			var save_string='';	 var tot_dye_qnty='';
			var po_id_array = new Array(); var buyer_id_array = new Array(); var buyer_name_array = new Array();

			$("#tbl_list_search").find('tr').each(function()
			{
				var txtPoId=$(this).find('input[name="txtPoId[]"]').val();
				var txtDyeQnty=$(this).find('input[name="txtDyeQnty[]"]').val();
				var txtRoll=$(this).find('input[name="txtRoll[]"]').val();
				var buyerId=$(this).find('input[name="buyerId[]"]').val();
				var buyerName=$(this).find('input[name="buyerName[]"]').val();

				tot_dye_qnty=tot_dye_qnty*1+txtDyeQnty*1;
				
				if(roll_maintained==0)
				{
					txtRoll=0;
				}
				
				if(txtDyeQnty*1>0)
				{
					if(save_string=="")
					{
						save_string=txtPoId+"**"+txtDyeQnty+"**"+txtRoll;
					}
					else
					{
						save_string+=","+txtPoId+"**"+txtDyeQnty+"**"+txtRoll;
					}
					
					if( jQuery.inArray( txtPoId, po_id_array) == -1 )
					{
						po_id_array.push(txtPoId);
					}
					
					if( jQuery.inArray( buyerId, buyer_id_array) == -1 )
					{
						buyer_id_array.push(buyerId);
						buyer_name_array.push(buyerName);
					}
				}
			});

			$('#save_string').val( save_string );
			$('#tot_dye_qnty').val( tot_dye_qnty );
			$('#all_po_id').val( po_id_array );
			$('#buyer_id').val( buyer_id_array );
			$('#buyer_name').val( buyer_name_array );
			$('#distribution_method').val( $('#cbo_distribiution_method').val() );
			
			parent.emailwindow.hide();
		}
    </script>
</head>

<body>
	<?
	if($type!=1)
	{
	?>
	<form name="searchdescfrm"  id="searchdescfrm">
		<fieldset style="width:620px;margin-left:10px">
        	<input type="hidden" name="save_string" id="save_string" class="text_boxes" value="">
            <input type="hidden" name="tot_dye_qnty" id="tot_dye_qnty" class="text_boxes" value="">
            <input type="hidden" name="all_po_id" id="all_po_id" class="text_boxes" value="">
            <input type="hidden" name="buyer_id" id="buyer_id" class="text_boxes" value="">
            <input type="hidden" name="buyer_name" id="buyer_name" class="text_boxes" value="">
            <input type="hidden" name="distribution_method" id="distribution_method" class="text_boxes" value="">
	<?
	}
	
	if($production_basis==4 && $type!=1)
	{
	?>
		<table cellpadding="0" cellspacing="0" width="620" class="rpt_table">
			<thead>
				<th>Buyer</th>
				<th>Search By</th>
				<th>Search</th>
				<th>
					<input type="reset" name="reset" id="reset" value="Reset" style="width:100px" class="formbutton" />
					<input type="hidden" name="po_id" id="po_id" value="">
				</th>
			</thead>
			<tr class="general">
				<td align="center">
					<?
						echo create_drop_down( "cbo_buyer_name", 170, "select buy.id, buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company='$cbo_company_id' $buyer_cond and buy.id in (select buyer_id from lib_buyer_party_type where party_type in (1,3,21,90)) order by buy.buyer_name","id,buyer_name", 1, "-- All Buyer --", $selected, "",$data[0] );
					?>
				</td>
				<td align="center">
					<?
						$search_by_arr=array(1=>"PO No",2=>"Job No");
						echo create_drop_down( "cbo_search_by", 170, $search_by_arr,"",0, "--Select--", "",$dd,0 );
					?>
				</td>
				<td align="center">
					<input type="text" style="width:130px" class="text_boxes"  name="txt_search_common" id="txt_search_common" />
				</td>
				<td align="center">
					<input type="button" name="button2" class="formbutton" value="Show" onClick="fn_show_check();" style="width:100px;" />
				</td>
			</tr>
		</table>
		<div id="search_div" style="margin-top:10px">
			<?
			if($save_data!="")
			{
				
			?>
				<div style="width:600px; margin-top:10px" align="center">
                    <table class="rpt_table" border="1" cellpadding="0" cellspacing="0" rules="all" width="300" align="center">
                        <thead>
                            <th>Total Dye Qnty</th>
                            <th>Distribution Method</th>
                        </thead>
                        <tr class="general">
                            <td><input type="text" name="txt_prop_dey_qnty" id="txt_prop_dey_qnty" class="text_boxes_numeric" value="<? echo $txt_production_qty; ?>" style="width:120px" onBlur="distribute_qnty(document.getElementById('cbo_distribiution_method').value)"></td>
                            <td>
                                <?
                                    $distribiution_method=array(1=>"Proportionately",2=>"Manually");
                                    echo create_drop_down( "cbo_distribiution_method", 160, $distribiution_method,"",0, "",$prev_distribution_method, "distribute_qnty(this.value);",0 );
                                ?>
                            </td>
                        </tr>
                    </table>
				</div>
				<div style="margin-left:10px; margin-top:10px">
					<table class="rpt_table" border="1" cellpadding="0" cellspacing="0" rules="all" width="580">
						<thead>
							<th width="150">PO No</th>
							<th width="140">PO Qnty</th>
							<th width="140">Grey Qnty</th>
							<?
							if($roll_maintained==1)
							{
							?>
								<th>Roll</th>
							<?
							}
							?>
						</thead>
					</table>
					<div style="width:600px; max-height:220px; overflow-y:scroll" id="list_container" align="left"> 
						<table class="rpt_table" border="1" cellpadding="0" cellspacing="0" rules="all" width="580" id="tbl_list_search">  
							<? 
							$i=1; $tot_po_qnty=0; $po_array=array();  

							$explSaveData = explode(",",$save_data); 	
							for($z=0;$z<count($explSaveData);$z++)
							{
								if ($i%2==0)  
									$bgcolor="#E9F3FF";
								else
									$bgcolor="#FFFFFF";
									
								$po_wise_data = explode("**",$explSaveData[$z]);
								$order_id=$po_wise_data[0];
								$dye_qnty=$po_wise_data[1];
								$roll_no=$po_wise_data[2];
								
								$po_data=sql_select("select a.buyer_name, b.po_number, (a.total_set_qnty*b.po_quantity) as po_qnty_in_pcs from wo_po_details_master a, wo_po_break_down b where a.job_no=b.job_no_mst and b.id=$order_id");
								
								if(!(in_array($order_id,$po_array)))
								{
									$tot_po_qnty+=$po_data[0][csf('po_qnty_in_pcs')];
									$po_array[]=$order_id;
								}

								?>
								<tr bgcolor="<? echo $bgcolor; ?>" id="tr_<? echo $i; ?>">
                                    <td width="150">
                                        <p><? echo $po_data[0][csf('po_number')]; ?></p>
                                        <input type="hidden" name="txtPoId[]" id="txtPoId_<? echo $i; ?>" value="<? echo $order_id; ?>">
                                        <input type="hidden" name="buyerId[]" id="buyerId_<? echo $i; ?>" class="text_boxes" value="<? echo $po_data[0][csf('buyer_name')]; ?>">
                                        <input type="hidden" name="buyerName[]" id="buyerName_<? echo $i; ?>" class="text_boxes" value="<? echo $buyer_arr[$po_data[0][csf('buyer_name')]]; ?>">
                                    </td>
                                    <td width="140" align="right">
                                        <? echo $po_data[0][csf('po_qnty_in_pcs')]; ?>
                                        <input type="hidden" name="txtPoQnty[]" id="txtPoQnty_<? echo $i; ?>" value="<? echo $po_data[0][csf('po_qnty_in_pcs')]; ?>">
                                    </td>
                                    <td width="140" align="center">
                                        <input type="text" name="txtDyeQnty[]" id="txtDyeQnty_<? echo $i; ?>" class="text_boxes_numeric" style="width:100px" value="<? echo $dye_qnty; ?>">
                                    </td>
                                    <?
                                    if($roll_maintained==1)
                                    {
                                    ?>
                                        <td align="center">
                                            <input type="text" name="txtRoll[]" id="txtRoll_<? echo $i; ?>" class="text_boxes_numeric" style="width:80px" value="<? if($roll_no!=0) echo $roll_no; ?>" disabled="disabled"/>
                                        </td>
                                    <?
                                    }
                                    ?>
                                </tr>
							<? 
							$i++;
							}
							?>
							<input type="hidden" name="tot_po_qnty" id="tot_po_qnty" class="text_boxes" value="<? echo $tot_po_qnty; ?>">
						</table>
					</div>
					<table width="620">
						 <tr>
							<td align="center" >
								<input type="button" name="close" class="formbutton" value="Close" id="main_close" onClick="fnc_close();" style="width:100px" />
							</td>
						</tr>
					</table>
				</div>
			<?
			}
			?>        
        </div>
	<?
	}
	else
	{
	?>
		<div style="width:600px; margin-top:10px" align="center">
			<table class="rpt_table" border="1" cellpadding="0" cellspacing="0" rules="all" width="300" align="center">
				<thead>
					<th>Total Dye Qnty</th>
					<th>Distribution Method</th>
				</thead>
				<tr class="general">
					<td><input type="text" name="txt_prop_dey_qnty" id="txt_prop_dey_qnty" class="text_boxes_numeric" value="<? echo $txt_production_qty; ?>" style="width:120px" onBlur="distribute_qnty(document.getElementById('cbo_distribiution_method').value)"></td>
					<td>
						<?
							$distribiution_method=array(1=>"Proportionately",2=>"Manually");
							echo create_drop_down( "cbo_distribiution_method", 160, $distribiution_method,"",0, "",$prev_distribution_method, "distribute_qnty(this.value);",0 );
						?>
					</td>
				</tr>
			</table>
		</div>
		<div style="margin-left:10px; margin-top:10px">
			<table class="rpt_table" border="1" cellpadding="0" cellspacing="0" rules="all" width="580">
				<thead>
					<th width="150">PO No</th>
					<th width="140">PO Qnty</th>
					<th width="140">Dye Qnty</th>
                    <?
					if($roll_maintained==1)
					{
					?>
						<th>Roll</th>
                    <?
					}
                    ?>
				</thead>
			</table>
			<div style="width:600px; max-height:220px; overflow-y:scroll" id="list_container" align="left">
				<table class="rpt_table" border="1" cellpadding="0" cellspacing="0" rules="all" width="580" id="tbl_list_search">
					<?
					$i=1; $tot_po_qnty=0;
					
					if($save_data!="" && $production_basis==4)
					{
						$po_id = explode(",",$po_id);
						$explSaveData = explode(",",$save_data); 	
						for($z=0;$z<count($explSaveData);$z++)
						{
							if ($i%2==0)  
								$bgcolor="#E9F3FF";
							else
								$bgcolor="#FFFFFF";
								
							$po_wise_data = explode("**",$explSaveData[$z]);
							$order_id=$po_wise_data[0];
							$dye_qnty=$po_wise_data[1];
							$roll_no=$po_wise_data[2];
							
							if(in_array($order_id,$po_id))
							{
								$po_data=sql_select("select a.buyer_name, b.po_number, (a.total_set_qnty*b.po_quantity) as po_qnty_in_pcs from wo_po_details_master a, wo_po_break_down b where a.job_no=b.job_no_mst and b.id=$order_id");
									
								if(!(in_array($order_id,$po_array)))
								{
									$tot_po_qnty+=$po_data[0][csf('po_qnty_in_pcs')];
									$po_array[]=$order_id;
								}
								?>
								<tr bgcolor="<? echo $bgcolor; ?>" id="tr_<? echo $i; ?>">
									<td width="150">
										<p><? echo $po_data[0][csf('po_number')]; ?></p>
										<input type="hidden" name="txtPoId[]" id="txtPoId_<? echo $i; ?>" value="<? echo $order_id; ?>">
										<input type="hidden" name="buyerId[]" id="buyerId_<? echo $i; ?>" class="text_boxes" value="<? echo $po_data[0][csf('buyer_name')]; ?>">
										<input type="hidden" name="buyerName[]" id="buyerName_<? echo $i; ?>" class="text_boxes" value="<? echo $buyer_arr[$po_data[0][csf('buyer_name')]]; ?>">
									</td>
									<td width="140" align="right">
										<? echo $po_data[0][csf('po_qnty_in_pcs')]; ?>
										<input type="hidden" name="txtPoQnty[]" id="txtPoQnty_<? echo $i; ?>" value="<? echo $po_data[0][csf('po_qnty_in_pcs')]; ?>">
									</td>
									<td width="140" align="center">
										<input type="text" name="txtDyeQnty[]" id="txtDyeQnty_<? echo $i; ?>" class="text_boxes_numeric" style="width:100px" value="<? echo $dye_qnty; ?>">
									</td>
									<?
									if($roll_maintained==1)
									{
									?>
										<td align="center">
											<input type="text" name="txtRoll[]" id="txtRoll_<? echo $i; ?>" class="text_boxes_numeric" style="width:80px" value="<? if($roll_no!=0) echo $roll_no; ?>" disabled="disabled"/>
										</td>
									<?
									}
									?>
								</tr>
							<? 
							$i++;
							}
						}
						
						$result=implode(",",array_diff($po_id, $po_array));
						if($result!="")
						{
							if($roll_maintained==1)
							{
								$po_sql="select b.id, a.buyer_name, b.po_number, (a.total_set_qnty*b.po_quantity) as po_qnty_in_pcs, d.roll_no from wo_po_details_master a, wo_po_break_down b left join pro_roll_details d on b.id=d.po_breakdown_id and d.entry_form=1 and d.status_active=1 and d.is_deleted=0 where a.job_no=b.job_no_mst and b.id in ($result)";
							}
							else
							{
								$po_sql="select b.id, a.buyer_name, b.po_number, (a.total_set_qnty*b.po_quantity) as po_qnty_in_pcs from wo_po_details_master a, wo_po_break_down b where a.job_no=b.job_no_mst and b.id in ($result)";
							}
							
							$nameArray=sql_select($po_sql);
							foreach($nameArray as $row)
							{  
								if ($i%2==0)  
									$bgcolor="#E9F3FF";
								else
									$bgcolor="#FFFFFF";
								
								if(!(in_array($row[csf('id')],$po_array)))
								{
									$tot_po_qnty+=$row[csf('po_qnty_in_pcs')];
									$po_array[]=$row[csf('id')];
								}
								
								/*if($roll_maintained==1)
								{
									if($row[csf('roll_no')]!=0)
									{
										$dye_recv_qnty=return_field_value("sum(qnty)","pro_roll_details","po_breakdown_id='$row[id]' and roll_no='$row[roll_no]' and entry_form=3 and status_active=1 and is_deleted=0");
									}
									else $dye_recv_qnty=0;
									
									$dye_qnty=$row[csf('qnty')]-$dye_recv_qnty;
								}
								else
								{
									$dye_recv_qnty=return_field_value("sum(quantity)"," order_wise_pro_details","po_breakdown_id='$row[id]' and entry_form=6 and status_active=1 and is_deleted=0");
								}*/

							 ?>
								<tr bgcolor="<? echo $bgcolor; ?>" id="tr_<? echo $i; ?>">
									<td width="150">
										<p><? echo $row[csf('po_number')]; ?></p>
										<input type="hidden" name="txtPoId[]" id="txtPoId_<? echo $i; ?>" value="<? echo $row[csf('id')]; ?>">
										<input type="hidden" name="buyerId[]" id="buyerId_<? echo $i; ?>" class="text_boxes" value="<? echo $row[csf('buyer_name')]; ?>">
										<input type="hidden" name="buyerName[]" id="buyerName_<? echo $i; ?>" class="text_boxes" value="<? echo $buyer_arr[$row[csf('buyer_name')]]; ?>">
									</td>
									<td width="140" align="right">
										<? echo $row[csf('po_qnty_in_pcs')]; ?>
										<input type="hidden" name="txtPoQnty[]" id="txtPoQnty_<? echo $i; ?>" value="<? echo $row[csf('po_qnty_in_pcs')]; ?>">
									</td>
									<td width="140" align="center">
										<input type="text" name="txtDyeQnty[]" id="txtDyeQnty_<? echo $i; ?>" class="text_boxes_numeric" style="width:100px" value="">
									</td>
									<?
									if($roll_maintained==1)
									{
									?>
										<td align="center">
											<input type="text" name="txtRoll[]" id="txtRoll_<? echo $i; ?>" class="text_boxes_numeric" style="width:80px" value="<? if($row[csf('roll_no')]!=0) echo $row[csf('roll_no')]; ?>" disabled="disabled"/>
									</td>
									<?
									}
									?>
								</tr>
							<? 
							$i++; 
							} 
						}
					}
					else if($save_data!="" && $production_basis==5)
					{
						$dye_qnty_array=array();
						$explSaveData = explode(",",$save_data); 
						
						if($roll_maintained==1)
						{
							for($i=0;$i<count($explSaveData);$i++)
							{
								$po_wise_data = explode("**",$explSaveData[$i]);
								$order_id=$po_wise_data[0];
								$dye_qnty=$po_wise_data[1];
								$roll_no=$po_wise_data[2];
								
								$dye_qnty_array[$order_id][$roll_no]=$dye_qnty;
							} 	
						}
						else
						{
							for($i=0;$i<count($explSaveData);$i++)
							{
								$po_wise_data = explode("**",$explSaveData[$i]);
								$order_id=$po_wise_data[0];
								$dye_qnty=$po_wise_data[1];
								
								$dye_qnty_array[$order_id]=$dye_qnty;
							} 	
						}
					
						$tot_po_qnty=0; $po_array=array();
						
						if($roll_maintained==1)
						{
							$po_sql="select b.id, a.buyer_name, b.po_number, (a.total_set_qnty*b.po_quantity) as po_qnty_in_pcs, c.roll_no, c.batch_qnty as qnty from wo_po_details_master a, wo_po_break_down b, pro_batch_create_dtls c where a.job_no=b.job_no_mst and b.id=c.po_id and c.mst_id='$txt_batch_id' and c.status_active=1 and c.is_deleted=0 group by c.id";
						}
						else
						{
							$po_sql="select b.id, a.buyer_name, b.po_number, (a.total_set_qnty*b.po_quantity) as po_qnty_in_pcs, sum(c.batch_qnty) as qnty from wo_po_details_master a, wo_po_break_down b, pro_batch_create_dtls c where a.job_no=b.job_no_mst and b.id=c.po_id and c.mst_id='$txt_batch_id' and c.status_active=1 and c.is_deleted=0 group by b.id";	
						}
						
						$nameArray=sql_select($po_sql);
						foreach($nameArray as $row)
						{
							if ($i%2==0)  
								$bgcolor="#E9F3FF";
							else
								$bgcolor="#FFFFFF";
								
							if(!(in_array($row[csf('id')],$po_array)))
							{
								$tot_po_qnty+=$row[csf('po_qnty_in_pcs')];
								$po_array[]=$row[csf('id')];
							}
							
							if($roll_maintained==1)
							{
								$dye_qnty=$dye_qnty_array[$row[csf('id')]][$row[csf('roll_no')]];
								
								if($dye_qnty=="")
								{
									if($row[csf('roll_no')]!=0)
									{
										$dye_recv_qnty=return_field_value("sum(qnty)","pro_roll_details","po_breakdown_id='$row[id]' and roll_no='$row[roll_no]' and entry_form=3 and status_active=1 and is_deleted=0");
									}
									else $dye_recv_qnty=0;
									$dye_qnty=$row[csf('qnty')]-$dye_recv_qnty;
								}
							}
							else 
							{
								$dye_qnty=$dye_qnty_array[$row[csf('id')]];
								
								if($dye_qnty=="")
								{
									$dye_recv_qnty=return_field_value("sum(quantity)"," order_wise_pro_details","po_breakdown_id='$row[id]' and entry_form=6 and status_active=1 and is_deleted=0");
									$dye_qnty=$row[csf('qnty')]-$dye_recv_qnty;
								}
							}
							
							?>
							<tr bgcolor="<? echo $bgcolor; ?>" id="tr_<? echo $i; ?>">
								<td width="150">
									<p><? echo $row[csf('po_number')]; ?></p>
									<input type="hidden" name="txtPoId[]" id="txtPoId_<? echo $i; ?>" value="<? echo $row[csf('id')]; ?>">
									<input type="hidden" name="buyerId[]" id="buyerId_<? echo $i; ?>" class="text_boxes" value="<? echo $row[csf('buyer_name')]; ?>">
									<input type="hidden" name="buyerName[]" id="buyerName_<? echo $i; ?>" class="text_boxes" value="<? echo $buyer_arr[$row[csf('buyer_name')]];?>">
								</td>
								<td width="140" align="right">
									<? echo $row[csf('po_qnty_in_pcs')]; ?>
									<input type="hidden" name="txtPoQnty[]" id="txtPoQnty_<? echo $i; ?>" value="<? echo $row[csf('po_qnty_in_pcs')]; ?>">
								</td>
								<td width="140" align="center">
									<input type="text" name="txtDyeQnty[]" id="txtDyeQnty_<? echo $i; ?>" class="text_boxes_numeric" style="width:100px" value="<? echo $dye_qnty; ?>">
								</td>
								<?
								if($roll_maintained==1)
								{
								?>
									<td align="center">
										<input type="text" name="txtRoll[]" id="txtRoll_<? echo $i; ?>" class="text_boxes_numeric" style="width:80px" value="<? if($row[csf('roll_no')]!=0) echo $row[csf('roll_no')]; ?>" disabled="disabled"/>
									</td>
								<?
								}
								?>
							</tr>
						<? 
						$i++;
						}
					}
					else
					{
						if($type==1)
						{
							if($po_id!="")
							{
								if($roll_maintained==1)
								{
									$po_sql="select b.id, a.buyer_name, b.po_number, (a.total_set_qnty*b.po_quantity) as po_qnty_in_pcs, d.roll_no, d.qnty from wo_po_details_master a, wo_po_break_down b left join pro_roll_details d on b.id=d.po_breakdown_id and d.entry_form=1 and d.roll_no<>0 and d.status_active=1 and d.is_deleted=0 where a.job_no=b.job_no_mst and b.id in ($po_id)";
								}
								else
								{
									$po_sql="select b.id, a.buyer_name, b.po_number, (a.total_set_qnty*b.po_quantity) as po_qnty_in_pcs from wo_po_details_master a, wo_po_break_down b where a.job_no=b.job_no_mst and b.id in ($po_id)";
								}
							}
						}
						else
						{
							if($roll_maintained==1)
							{
								$po_sql="select b.id, a.buyer_name, b.po_number, (a.total_set_qnty*b.po_quantity) as po_qnty_in_pcs, c.roll_no, c.batch_qnty as qnty from wo_po_details_master a, wo_po_break_down b, pro_batch_create_dtls c where a.job_no=b.job_no_mst and b.id=c.po_id and c.mst_id='$txt_batch_id' and c.status_active=1 and c.is_deleted=0 group by c.id";
							}
							else
							{
								$po_sql="select b.id, a.buyer_name, b.po_number, (a.total_set_qnty*b.po_quantity) as po_qnty_in_pcs, sum(c.batch_qnty) as qnty from wo_po_details_master a, wo_po_break_down b, pro_batch_create_dtls c where a.job_no=b.job_no_mst and b.id=c.po_id and c.mst_id='$txt_batch_id' and c.status_active=1 and c.is_deleted=0 group by b.id";
							}
						}
					
						$po_array=array(); $tot_po_qnty=0;
						$nameArray=sql_select($po_sql);
						foreach($nameArray as $row)
						{
							if ($i%2==0)
								$bgcolor="#E9F3FF";
							else
								$bgcolor="#FFFFFF";
							
							if(!(in_array($row[csf('id')],$po_array)))
							{
								$tot_po_qnty+=$row[csf('po_qnty_in_pcs')];
								$po_array[]=$row[csf('id')];
							}
							
							$dye_qnty='';
							
							if($type!=1)
							{
								if($roll_maintained==1)
								{
									if($row[csf('roll_no')]!=0)
									{
										$dye_recv_qnty=return_field_value("sum(qnty)","pro_roll_details","po_breakdown_id='$row[id]' and roll_no='$row[roll_no]' and entry_form=3 and status_active=1 and is_deleted=0");
									}
									else $dye_recv_qnty=0;
								}
								else
								{
									$dye_recv_qnty=return_field_value("sum(quantity)"," order_wise_pro_details","po_breakdown_id='$row[id]' and entry_form=6 and status_active=1 and is_deleted=0");
								}
								
								$dye_qnty=$row[csf('qnty')]-$dye_recv_qnty;
							}
						 ?>
							<tr bgcolor="<? echo $bgcolor; ?>" id="tr_<? echo $i; ?>">
								<td width="150">
									<p><? echo $row[csf('po_number')]; ?></p>
									<input type="hidden" name="txtPoId[]" id="txtPoId_<? echo $i; ?>" value="<? echo $row[csf('id')]; ?>">
									<input type="hidden" name="buyerId[]" id="buyerId_<? echo $i; ?>" class="text_boxes" value="<? echo $row[csf('buyer_name')]; ?>">
									<input type="hidden" name="buyerName[]" id="buyerName_<? echo $i; ?>" class="text_boxes" value="<? echo $buyer_arr[$row[csf('buyer_name')]];?>">
								</td>
								<td width="140" align="right">
									<? echo $row[csf('po_qnty_in_pcs')]; ?>
									<input type="hidden" name="txtPoQnty[]" id="txtPoQnty_<? echo $i; ?>" value="<? echo $row[csf('po_qnty_in_pcs')]; ?>">
								</td>
								<td width="140" align="center">
									<input type="text" name="txtDyeQnty[]" id="txtDyeQnty_<? echo $i; ?>" class="text_boxes_numeric" style="width:100px" value="<? echo $dye_qnty; ?>">
								</td>
								<?
								if($roll_maintained==1)
								{
								?>
									<td align="center">
										<input type="text" name="txtRoll[]" id="txtRoll_<? echo $i; ?>" class="text_boxes_numeric" style="width:80px" value="<? if($row[csf('roll_no')]!=0) echo $row[csf('roll_no')]; ?>" disabled="disabled"/>
									</td>
								<?
								}
								?>
							</tr>
						<?
						$i++;
						}
					}
					?>
					<input type="hidden" name="tot_po_qnty" id="tot_po_qnty" class="text_boxes" value="<? echo $tot_po_qnty; ?>">
				</table>
			</div>
			<table width="620">
				 <tr>
					<td align="center" >
						<input type="button" name="close" class="formbutton" value="Close" id="main_close" onClick="fnc_close();" style="width:100px" />
					</td>
				</tr>
			</table>
		</div>
	<?
	}
	if($type!=1)
	{
	?>
		</fieldset>
	</form>
    <?
	}
	?>
</body>
<script src="../../includes/functions_bottom.js" type="text/javascript"></script>
</html>
<?
exit();
}
if($action=="create_po_search_list_view")
{
	$data = explode("_",$data);

	$search_string="%".trim($data[0])."%";
	$search_by=$data[1];

	if($search_by==1)
		$search_field='b.po_number';
	else
		$search_field='a.job_no';
		
	$company_id =$data[2];
	$buyer_id =$data[3];
	
	$all_po_id=$data[4];
	
	if($all_po_id!="")
		$po_id_cond=" or b.id in($all_po_id)";
	else 
		$po_id_cond="";
	
	$hidden_po_id=explode(",",$all_po_id);

	if($buyer_id==0) $buyer="%%"; else $buyer=$buyer_id; 
	
	$sql = "select a.job_no, a.buyer_name, a.style_ref_no, a.order_uom, b.id, b.po_number, (a.total_set_qnty*b.po_quantity) as po_qnty_in_pcs, b.pub_shipment_date from wo_po_details_master a, wo_po_break_down b where a.job_no=b.job_no_mst and a.company_name=$company_id and a.buyer_name like '$buyer' and $search_field like '$search_string' and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $po_id_cond group by b.id"; 
	//echo $sql;die;
	?>
    <div>
        <table cellspacing="0" cellpadding="0" border="1" rules="all" width="600" class="rpt_table" >
            <thead>
                <th width="30">SL</th>
                <th width="90">Job No</th>
                <th width="100">Style No</th>
                <th width="100">PO No</th>
                <th width="80">PO Quantity</th>
                <th width="100">Buyer</th>
                <th>Shipment Date</th>
            </thead>
        </table>
        <div style="width:618px; overflow-y:scroll; max-height:240px;" id="buyer_list_view" align="center">
            <table cellspacing="0" cellpadding="0" border="1" rules="all" width="600" class="rpt_table" id="tbl_list_search" >
            <?
				$i=1; $po_row_id='';
				$nameArray=sql_select( $sql );
				foreach ($nameArray as $selectResult)
				{
					if ($i%2==0)  
						$bgcolor="#E9F3FF";
					else
						$bgcolor="#FFFFFF";
						
					$roll_used=0;
					
					if(in_array($selectResult[csf('id')],$hidden_po_id)) 
					{
						if($po_row_id=="") $po_row_id=$i; else $po_row_id.=",".$i;
						
						$roll_data_array=sql_select("select roll_no from pro_roll_details where po_breakdown_id=$selectResult[id] and roll_used=1 and entry_form=1 and status_active=1 and is_deleted=0");
						if(count($roll_data_array)>0)
						{
							$roll_used=1;
						}
						else
							$roll_used=0;
					}
							
					?>
                        <tr bgcolor="<? echo $bgcolor; ?>" style="text-decoration:none; cursor:pointer" id="search<? echo $i;?>" onClick="js_set_value(<? echo $i; ?>)"> 
                            <td width="30" align="center">
								<? echo $i; ?>
                            	<input type="hidden" name="txt_individual_id" id="txt_individual_id<? echo $i ?>" value="<? echo $selectResult[csf('id')]; ?>"/>
                            </td>	
                            <td width="90"><p><? echo $selectResult[csf('job_no')]; ?></p></td>
                            <td width="100"><p><? echo $selectResult[csf('style_ref_no')]; ?></p></td>
                            <td width="100"><p><? echo $selectResult[csf('po_number')]; ?></p></td>
                            <td width="80" align="right"><? echo $selectResult[csf('po_qnty_in_pcs')]; ?></td> 
                            <td width="100"><p><? echo $buyer_arr[$selectResult[csf('buyer_name')]]; ?></p></td>
                            <td align="center"><? echo change_date_format($selectResult[csf('pub_shipment_date')]); ?></td>	
                        </tr>
                    <?
                    $i++;
				}
			?>
				<input type="hidden" name="txt_po_row_id" id="txt_po_row_id" value="<? echo $po_row_id; ?>"/>
            </table>
        </div>
         <table width="620" cellspacing="0" cellpadding="0" style="border:none" align="center">
            <tr>
                <td align="center" height="30" valign="bottom">
                    <div style="width:100%"> 
                        <div style="width:50%; float:left" align="left">
                            <input type="checkbox" name="check_all" id="check_all" onClick="check_all_data()" /> Check / Uncheck All
                        </div>
                        <div style="width:50%; float:left" align="left">
                            <input type="button" name="close" onClick="show_dye_prod_recv();" class="formbutton" value="Close" style="width:100px" />
                        </div>
                    </div>
                </td>
            </tr>
        </table>
	</div>           
<?
exit();
}
if ($action=="save_update_delete")
{
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process )); 
	
	if ($operation==0)  // Insert Here
	{ 
		$con = connect();
		if($db_type==0)
		{
			mysql_query("BEGIN");
		}		
		//if( check_table_status( $_SESSION['menu_id'], 1 )==0 ) { echo "15**0"; die;}
		
		$dye_recv_num=''; $dye_update_id=''; $flag=1;
		
		if(str_replace("'","",$update_id)=="")
		{
			//$new_dye_recv_system_id=explode("*",return_mrr_number( str_replace("'","",$cbo_company_id), '', 'DPU', date("Y",time()), 5, "select dye_recv_prefix, dye_recv_prefix_number from pro_dyeing_update_mst where company_id=$cbo_company_id order by dye_recv_prefix_number desc ", "dye_recv_prefix", "dye_recv_prefix_number" ));
			$id= return_next_id_by_sequence("PRO_DYEING_UPDATE_MST_PK_SEQ", "pro_dyeing_update_mst", $con);
			$new_dye_recv_system_id = explode("*", return_next_id_by_sequence("PRO_DYEING_UPDATE_MST_PK_SEQ", "pro_dyeing_update_mst",$con,1,$cbo_company_id,'DPU',6,date("Y",time()) ));
		 
			//$id=return_next_id( "id", "pro_dyeing_update_mst", 1 ) ;
			
					 
			$field_array="id, dye_recv_prefix, dye_recv_prefix_number, dye_system_id, recieve_basis, company_id,entry_form, received_date, challan_no, dyeing_source, dyeing_company, location_id, remarks, inserted_by, insert_date";
			
			$data_array="(".$id.",'".$new_dye_recv_system_id[1]."',".$new_dye_recv_system_id[2].",'".$new_dye_recv_system_id[0]."',".$cbo_production_basis.",".$cbo_company_id.",6,".$txt_production_date.",".$txt_challan_no.",".$cbo_dyeing_source.",".$cbo_dyeing_company.",".$cbo_location.",".$txt_remarks.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."')";
			
			//echo "insert into pro_dyeing_update_mst (".$field_array.") values ".$data_array;die;
			$rID=sql_insert("pro_dyeing_update_mst",$field_array,$data_array,0);
			
			if($rID) $flag=1; else $flag=0;
			
			$dye_recv_num=$new_dye_recv_system_id[0];
			$dye_update_id=$id;
		}
		else
		{
			$field_array_update="recieve_basis*received_date*challan_no*dyeing_source*dyeing_company*location_id*remarks*updated_by*update_date";
			
			$data_array_update=$cbo_production_basis."*".$txt_production_date."*".$txt_challan_no."*".$cbo_dyeing_source."*".$cbo_dyeing_company."*".$cbo_location."*".$txt_remarks."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'";
			
			$rID=sql_update("pro_dyeing_update_mst",$field_array_update,$data_array_update,"id",$update_id,1);
			if($flag==1) 
			{
				if($rID) $flag=1; else $flag=0; 
			} 
			
			$dye_recv_num=str_replace("'","",$txt_system_id);
			$dye_update_id=str_replace("'","",$update_id);
		}
		
		//$color_id=return_id( $txt_color, $color_arr, "lib_color", "id,color_name");
		if(str_replace("'","",$txt_color)!="")
		{
			if (!in_array(str_replace("'","",$txt_color),$new_array_color))
			{
				$color_id = return_id( str_replace("'","",$txt_color), $color_arr, "lib_color", "id,color_name","6");
				//echo $$txtColorName.'='.$color_id.'<br>';
				$new_array_color[$color_id]=str_replace("'","",$txt_color);

			}
			else $color_id =  array_search(str_replace("'","",$txt_color), $new_array_color);
		}
		else
		{
			$color_id=0;
		}

		$ItemDesc=$body_part[str_replace("'","",$cbo_body_part)].", ".str_replace("'","",$txt_cons_comp).", ".str_replace("'","",$txt_gsm).", ".str_replace("'","",$txt_dia_width);
		
		if(str_replace("'","",$cbo_production_basis)==4)
		{
			if(is_duplicate_field( "batch_no", "pro_batch_create_mst", "batch_no=$txt_batch_no" )==1)
			{
				echo "11**0"; 
				disconnect($con);
				die;			
			}
			
			//$batch_id=return_next_id( "id", "pro_batch_create_mst", 1 ) ;
			$batch_id = return_next_id_by_sequence("PRO_BATCH_CREATE_MST_PK_SEQ", "pro_batch_create_mst", $con);
					 
			$field_array_batch="id, batch_no, entry_form, batch_date, company_id, color_id, batch_weight, inserted_by, insert_date";
			
			$data_array_batch="(".$batch_id.",".$txt_batch_no.",6,".$txt_production_date.",".$cbo_company_id.",'".$color_id."',".$txt_production_qty.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."')";
			
			//echo "insert into pro_batch_create_mst (".$field_array_batch.") values ".$data_array_batch;die;
			$rID6=sql_insert("pro_batch_create_mst",$field_array_batch,$data_array_batch,0);
			
			if($rID6) $flag=1; else $flag=0;
		}
		else
		{
			$batch_id=str_replace("'","",$txt_batch_id);
		}
		
		//$id_dtls=return_next_id( "id", " pro_dyeing_update_dtls", 1 ) ;
		$id_dtls = return_next_id_by_sequence("PRO_DYEING_UPDATE_DTLS_PK_SEQ", "pro_dyeing_update_dtls", $con);
		
		$field_array_dtls="id, mst_id, batch_id, body_part_id, febric_description, color_id, gsm, dia_width, receive_qnty, order_id, buyer_id, machine_name, start_hours, end_hours, start_minutes, end_minutes, start_date, end_date, inserted_by, insert_date";
		
		$data_array_dtls="(".$id_dtls.",".$dye_update_id.",'".$batch_id."',".$cbo_body_part.",".$txt_cons_comp.",'".$color_id."',".$txt_gsm.",".$txt_dia_width.",".$txt_production_qty.",".$all_po_id.",".$buyer_id.",".$cbo_machine_name.",".$txt_start_hours.",".$txt_end_hours.",".$txt_start_minuties.",".$txt_end_minutes.",".$txt_start_date.",".$txt_end_date.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."')";
		
		//echo "insert into pro_dyeing_update_dtls (".$field_array_dtls.") values ".$data_array_dtls;die;
		$rID2=sql_insert("pro_dyeing_update_dtls",$field_array_dtls,$data_array_dtls,0);
		if($flag==1) 
		{
			if($rID2) $flag=1; else $flag=0; 
		} 
		
		$field_array_roll="id, mst_id, dtls_id, po_breakdown_id, entry_form, qnty, roll_no, inserted_by, insert_date";
		
		$save_string=explode(",",str_replace("'","",$save_data));
		
		$po_array=array();
		
		for($i=0;$i<count($save_string);$i++)
		{
			$order_dtls=explode("**",$save_string[$i]);
			
			$order_id=$order_dtls[0];
			$order_qnty_roll_wise=$order_dtls[1];
			$roll_no=$order_dtls[2];
			
			if($i==0) $add_comma=""; else $add_comma=",";
			
			//if( $id_roll=="" ) $id_roll = return_next_id( "id", "pro_roll_details", 1 ); else $id_roll = $id_roll+1;
			$id_roll = return_next_id_by_sequence("PRO_ROLL_DTLS_PK_SEQ", "pro_roll_details", $con);

			
			$data_array_roll.="$add_comma(".$id_roll.",".$dye_update_id.",".$id_dtls.",'".$order_id."',3,'".$order_qnty_roll_wise."','".$roll_no."',".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."')";
			
			if(array_key_exists($order_id,$po_array))
			{
				$po_array[$order_id]+=$order_qnty_roll_wise;
			}
			else
			{
				$po_array[$order_id]=$order_qnty_roll_wise;
			}
			
			if(str_replace("'","",$cbo_production_basis)==4)
			{
				$field_array_batch_dtls="id, mst_id, po_id, item_description, roll_no, batch_qnty, inserted_by, insert_date";
				
				//if($id_dtls_batch=="" ) $id_dtls_batch = return_next_id( "id", "pro_batch_create_dtls", 1 ); else $id_dtls_batch = $id_dtls_batch+1;
				$id_dtls_batch = return_next_id_by_sequence("PRO_GREY_BATCH_DTLS_PK_SEQ", "pro_batch_create_dtls", $con);
				
				$data_array_batch_dtls .="$add_comma(".$id_dtls_batch.",'".$batch_id."',".$order_id.",'".$ItemDesc."','".$roll_no."',".$order_qnty_roll_wise.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."')"; 
				
				//$id_roll = $id_roll+1;
				
				$data_array_roll_for_batch.="$add_comma(".$id_roll.",'".$batch_id."',".$id_dtls_batch.",".$order_id.",2,".$order_qnty_roll_wise.",'".$roll_no."',".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."')";
			}

		}
		
		//echo "insert into pro_roll_details (".$field_array_roll.") values ".$data_array_roll;die;	
		if($data_array_roll!="" && str_replace("'","",$roll_maintained)==1 && str_replace("'","",$batch_booking_without_order)!=1)
		{
			$rID4=sql_insert("pro_roll_details",$field_array_roll,$data_array_roll,0);
			if($flag==1) 
			{
				if($rID4) $flag=1; else $flag=0; 
			} 
		}
		
		$field_array_proportionate="id, trans_id, trans_type, entry_form, dtls_id, po_breakdown_id, prod_id, color_id, quantity, inserted_by, insert_date";

		$i=0;
		foreach($po_array as $key=>$val)
		{
			if($i==0) $add_comma=""; else $add_comma=",";
			
			
			//if( $id_prop=="" ) $id_prop = return_next_id( "id", "order_wise_pro_details", 1 ); else $id_prop = $id_prop+1;
			$id_prop = return_next_id_by_sequence("ORDER_WISE_PROP_PK_SEQ", "order_wise_pro_details", $con);
			
			$order_id=$key;
			$order_qnty=$val;
			
			$data_array_prop.="$add_comma(".$id_prop.",0,1,6,".$id_dtls.",'".$order_id."','".$id_dtls."','".$color_id."','".$order_qnty."',".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."')";
			$i++;
		}
		
		//echo "insert into order_wise_pro_details (".$field_array_proportionate.") values ".$data_array_prop;die;	
		if($data_array_prop!="" && str_replace("'","",$batch_booking_without_order)!=1)
		{
			$rID5=sql_insert("order_wise_pro_details",$field_array_proportionate,$data_array_prop,0);
			if($flag==1) 
			{
				if($rID5) $flag=1; else $flag=0; 
			} 
		}
		
		if($data_array_batch_dtls!="")
		{
			//echo "insert into pro_batch_create_dtls (".$field_array_batch_dtls.") values ".$data_array_batch_dtls;die;	
			$rID7=sql_insert("pro_batch_create_dtls",$field_array_batch_dtls,$data_array_batch_dtls,1);
			if($flag==1) 
			{
				if($rID7) $flag=1; else $flag=0; 
			} 
		}
		
		if($data_array_roll_for_batch!="" && str_replace("'","",$roll_maintained)==1 && str_replace("'","",$batch_booking_without_order)!=1)
		{
			//echo "insert into pro_roll_details (".$field_array_roll.") values ".$data_array_roll_for_batch;die;	
			$rID8=sql_insert("pro_roll_details",$field_array_roll,$data_array_roll_for_batch,1);
			if($flag==1) 
			{
				if($rID8) $flag=1; else $flag=0; 
			} 
		}
		
		if($db_type==0)
		{
			if($flag==1)
			{
				mysql_query("COMMIT");  
				echo "0**".$dye_update_id."**".$dye_recv_num."**0";
			}
			else
			{
				mysql_query("ROLLBACK"); 
				echo "5**0**"."&nbsp;"."**0";
			}
		}
		
		if($db_type==2 || $db_type==1 )
		{
			if($flag==1)
			{
				echo "0**".$dye_update_id."**".$dye_recv_num."**0";
			}
			else
			{
				echo "5**0**"."&nbsp;"."**0";
			}
		}
		
		check_table_status( $_SESSION['menu_id'],0);
				
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
		
		$flag=1;
		
		$field_array_update="recieve_basis*received_date*challan_no*dyeing_source*dyeing_company*location_id*remarks*updated_by*update_date";
			
		$data_array_update=$cbo_production_basis."*".$txt_production_date."*".$txt_challan_no."*".$cbo_dyeing_source."*".$cbo_dyeing_company."*".$cbo_location."*".$txt_remarks."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'";
		
		$rID=sql_update("pro_dyeing_update_mst",$field_array_update,$data_array_update,"id",$update_id,1);
		if($flag==1) 
		{
			if($rID) $flag=1; else $flag=0; 
		} 
		
		//$color_id=return_id( $txt_color, $color_arr, "lib_color", "id,color_name");
		if(str_replace("'","",$txt_color)!="")
		{
			if (!in_array(str_replace("'","",$txt_color),$new_array_color))
			{
				$color_id = return_id( str_replace("'","",$txt_color), $color_arr, "lib_color", "id,color_name","6");
				//echo $$txtColorName.'='.$color_id.'<br>';
				$new_array_color[$color_id]=str_replace("'","",$txt_color);

			}
			else $color_id =  array_search(str_replace("'","",$txt_color), $new_array_color);
		}
		else
		{
			$color_id=0;
		}

		$ItemDesc=$body_part[str_replace("'","",$cbo_body_part)].", ".str_replace("'","",$txt_cons_comp).", ".str_replace("'","",$txt_gsm).", ".str_replace("'","",$txt_dia_width);
		
		if(str_replace("'","",$cbo_production_basis)==4)
		{
			if(is_duplicate_field( "batch_no", "pro_batch_create_mst", "batch_no=$txt_batch_no and id<>$txt_batch_id" )==1)
			{
				echo "11**0"; 
				disconnect($con);
				die;			
			}
			
			$field_array_batch_update="batch_no*batch_date*color_id*batch_weight*updated_by*update_date";
			
			$data_array_batch_update=$txt_batch_no."*".$txt_production_date."*".$color_id."*".$txt_production_qty."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'";
			
			$rID6=sql_update("pro_batch_create_mst",$field_array_batch_update,$data_array_batch_update,"id",$txt_batch_id,0);
			if($flag==1) 
			{
				if($rID6) $flag=1; else $flag=0; 
			} 
			
			$delete_batch_dtls=execute_query( "delete from pro_batch_create_dtls where mst_id=$txt_batch_id",0);
			if($flag==1) 
			{
				if($delete_batch_dtls) $flag=1; else $flag=0; 
			} 
			
			if(str_replace("'","",$roll_maintained)==1)
			{
				$delete_batch_roll=execute_query( "delete from pro_roll_details where mst_id=$txt_batch_id and entry_form=2",0);
				if($flag==1) 
				{
					if($delete_batch_roll) $flag=1; else $flag=0; 
				} 
			}
		}
		
		$field_array_dtls="body_part_id*febric_description*color_id*gsm*dia_width*receive_qnty*order_id*buyer_id*machine_name*start_hours*end_hours*start_minutes*end_minutes*start_date*end_date*updated_by*update_date";
		
		$data_array_dtls=$cbo_body_part."*".$txt_cons_comp."*'".$color_id."'*".$txt_gsm."*".$txt_dia_width."*".$txt_production_qty."*".$all_po_id."*".$buyer_id."*".$cbo_machine_name."*".$txt_start_hours."*".$txt_end_hours."*".$txt_start_minuties."*".$txt_end_minutes."*".$txt_start_date."*".$txt_end_date."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'";
		
		$rID2=sql_update("pro_dyeing_update_dtls",$field_array_dtls,$data_array_dtls,"id",$update_dtls_id,0);
		if($flag==1) 
		{
			if($rID2) $flag=1; else $flag=0; 
		} 
		
		if(str_replace("'","",$roll_maintained)==1)
		{
			$delete_roll=execute_query( "delete from pro_roll_details where dtls_id=$update_dtls_id and entry_form=3",0);
			if($flag==1) 
			{
				if($delete_roll) $flag=1; else $flag=0; 
			} 
		}
		
		$delete_prop=execute_query( "delete from order_wise_pro_details where dtls_id=$update_dtls_id and entry_form=6",0);
		if($flag==1) 
		{
			if($delete_prop) $flag=1; else $flag=0; 
		}
		
		$field_array_roll="id, mst_id, dtls_id, po_breakdown_id, entry_form, qnty, roll_no, inserted_by, insert_date";
		
		$save_string=explode(",",str_replace("'","",$save_data));
		
		$po_array=array();
		
		for($i=0;$i<count($save_string);$i++)
		{
			$order_dtls=explode("**",$save_string[$i]);
			
			$order_id=$order_dtls[0];
			$order_qnty_roll_wise=$order_dtls[1];
			$roll_no=$order_dtls[2];
			
			if($i==0) $add_comma=""; else $add_comma=",";
			
			//if( $id_roll=="" ) $id_roll = return_next_id( "id", "pro_roll_details", 1 ); else $id_roll = $id_roll+1;
			$id_roll = return_next_id_by_sequence("PRO_ROLL_DTLS_PK_SEQ", "pro_roll_details", $con);
			$data_array_roll.="$add_comma(".$id_roll.",".$update_id.",".$update_dtls_id.",'".$order_id."',3,'".$order_qnty_roll_wise."','".$roll_no."',".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."')";
			
			if(array_key_exists($order_id,$po_array))
			{
				$po_array[$order_id]+=$order_qnty_roll_wise;
			}
			else
			{
				$po_array[$order_id]=$order_qnty_roll_wise;
			}
			
			if(str_replace("'","",$cbo_production_basis)==4)
			{
				$field_array_batch_dtls="id, mst_id, po_id, item_description, roll_no, batch_qnty, inserted_by, insert_date";
				
				//if($id_dtls_batch=="" ) $id_dtls_batch = return_next_id( "id", "pro_batch_create_dtls", 1 ); else $id_dtls_batch = $id_dtls_batch+1;
				$id_dtls_batch = return_next_id_by_sequence("PRO_GREY_BATCH_DTLS_PK_SEQ", "pro_batch_create_dtls", $con);
				
				$data_array_batch_dtls .="$add_comma(".$id_dtls_batch.",".$txt_batch_id.",".$order_id.",'".$ItemDesc."','".$roll_no."',".$order_qnty_roll_wise.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."')"; 
				
				//$id_roll = $id_roll+1;
				
				$data_array_roll_for_batch.="$add_comma(".$id_roll.",".$txt_batch_id.",".$id_dtls_batch.",".$order_id.",2,".$order_qnty_roll_wise.",'".$roll_no."',".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."')";
			}

		}
		
		//echo "insert into pro_roll_details (".$field_array_roll.") values ".$data_array_roll;die;	
		if($data_array_roll!="" && str_replace("'","",$roll_maintained)==1 && str_replace("'","",$batch_booking_without_order)!=1)
		{
			$rID4=sql_insert("pro_roll_details",$field_array_roll,$data_array_roll,0);
			if($flag==1) 
			{
				if($rID4) $flag=1; else $flag=0; 
			} 
		}
		
		$field_array_proportionate="id, trans_id, trans_type, entry_form, dtls_id, po_breakdown_id, prod_id, color_id, quantity, inserted_by, insert_date";

		$i=0;
		foreach($po_array as $key=>$val)
		{
			if($i==0) $add_comma=""; else $add_comma=",";
			
			//if( $id_prop=="" ) $id_prop = return_next_id( "id", "order_wise_pro_details", 1 ); else $id_prop = $id_prop+1;
			$id_prop = return_next_id_by_sequence("ORDER_WISE_PROP_PK_SEQ", "order_wise_pro_details", $con);
			
			$order_id=$key;
			$order_qnty=$val;
			
			$data_array_prop.="$add_comma(".$id_prop.",0,1,6,".$update_dtls_id.",'".$order_id."',".$update_dtls_id.",'".$color_id."','".$order_qnty."',".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."')";
			$i++;
		}
		
		//echo "insert into order_wise_pro_details (".$field_array_proportionate.") values ".$data_array_prop;die;	
		if($data_array_prop!="" && str_replace("'","",$batch_booking_without_order)!=1)
		{
			$rID5=sql_insert("order_wise_pro_details",$field_array_proportionate,$data_array_prop,0);
			if($flag==1) 
			{
				if($rID5) $flag=1; else $flag=0; 
			} 
		}
		
		if($data_array_batch_dtls!="")
		{
			//echo "insert into pro_batch_create_dtls (".$field_array_batch_dtls.") values ".$data_array_batch_dtls;die;	
			$rID7=sql_insert("pro_batch_create_dtls",$field_array_batch_dtls,$data_array_batch_dtls,1);
			if($flag==1) 
			{
				if($rID7) $flag=1; else $flag=0; 
			} 
		}
		
		if($data_array_roll_for_batch!="" && str_replace("'","",$roll_maintained)==1 && str_replace("'","",$batch_booking_without_order)!=1)
		{
			//echo "insert into pro_roll_details (".$field_array_roll.") values ".$data_array_roll_for_batch;die;	
			$rID8=sql_insert("pro_roll_details",$field_array_roll,$data_array_roll_for_batch,1);
			if($flag==1) 
			{
				if($rID8) $flag=1; else $flag=0; 
			} 
		}
		
		if($db_type==0)
		{
			if($flag==1)
			{
				mysql_query("COMMIT");  
				echo "1**".str_replace("'", '', $update_id)."**".str_replace("'", '', $txt_system_id)."**0";
			}
			else
			{
				mysql_query("ROLLBACK"); 
				echo "6**0**0**1";
			}
		}

		if($db_type==2 || $db_type==1 )
		{
			if($flag==1)
			{
				echo "1**".str_replace("'", '', $update_id)."**".str_replace("'", '', $txt_system_id)."**0";
			}
			else
			{
				echo "6**0**0**1";
			}
		}
		disconnect($con);
		die;
	}
	
}

if($action=="show_dye_prod_listview")
{
	$machine_arr = return_library_array("select id, concat(machine_no,'-',brand) as machine_name from lib_machine_name","id","machine_name");
	
	$sql="select id, batch_id, body_part_id, febric_description, gsm, dia_width, color_id, receive_qnty, machine_name from pro_dyeing_update_dtls where mst_id='$data' and status_active = '1' and is_deleted = '0'";
	
	$arr=array(0=>$batch_arr,1=>$body_part,5=>$color_arr,7=>$machine_arr);
	 
	echo  create_list_view("list_view", "Batch,Body Part,Fabric Description,GSM,Dia / Width,Color, Production Qnty, Machine No", "110,100,150,80,80,80,90,110","820","200",0, $sql, "put_data_dtls_part", "id", "'populate_dye_details_form_data'", 0, "batch_id,body_part_id,0,0,0,color_id,0,machine_name", $arr, "batch_id,body_part_id,febric_description,gsm,dia_width,color_id,receive_qnty,machine_name", "requires/dye_prod_update_controller",'','0,0,0,0,0,0,1,0');
	
	exit();
}
if($action=='populate_dye_details_form_data')
{
	$data=explode("**",$data);
	$id=$data[0];
	$roll_maintained=$data[1];
	
	$data_array=sql_select("select a.recieve_basis, b.id, b.batch_id, b.body_part_id, b.febric_description, b.gsm, b.dia_width, b.color_id, b.receive_qnty, b.machine_name, b.order_id, b.buyer_id, b.start_hours, b.end_hours, b.start_minutes, b.end_minutes, b.start_date, b.end_date from pro_dyeing_update_mst a, pro_dyeing_update_dtls b where a.id=b.mst_id and b.id='$id'");
	foreach ($data_array as $row)
	{ 
		$buyer_name='';
		$buyer=explode(",",$row[csf('buyer_id')]);
		foreach($buyer as $val )
		{
			if($buyer_name=='') $buyer_name=$buyer_arr[$val]; else $buyer_name.=",".$buyer_arr[$val];
		}
		
		echo "document.getElementById('txt_batch_id').value 				= '".$row[csf("batch_id")]."';\n";
		echo "document.getElementById('txt_batch_no').value 				= '".$batch_arr[$row[csf("batch_id")]]."';\n";
		
		if($row[csf('recieve_basis')]==5)
		{
			echo "get_php_form_data($row[batch_id], 'populate_data_from_batch', 'requires/dye_prod_update_controller' );\n";
			echo "show_list_view('$row[batch_id]','show_fabric_desc_listview','list_fabric_desc_container','requires/dye_prod_update_controller','');\n";
		}
	
		if($row[csf("start_hours")]=="0") $start_hours=""; else $start_hours=$row[csf("start_hours")];
		if($row[csf("start_minutes")]=="0") $start_minutes=""; else $start_minutes=$row[csf("start_minutes")];
		if($row[csf("end_hours")]=="0") $end_hours=""; else $end_hours=$row[csf("end_hours")];
		if($row[csf("end_minutes")]=="0") $end_minutes=""; else $end_minutes=$row[csf("end_minutes")];

		echo "document.getElementById('cbo_body_part').value 				= '".$row[csf("body_part_id")]."';\n";
		echo "document.getElementById('txt_cons_comp').value 				= '".$row[csf("febric_description")]."';\n";
		echo "document.getElementById('txt_color').value 					= '".$color_arr[$row[csf("color_id")]]."';\n";
		echo "document.getElementById('txt_gsm').value 						= '".$row[csf("gsm")]."';\n";
		echo "document.getElementById('txt_dia_width').value 				= '".$row[csf("dia_width")]."';\n";
		echo "document.getElementById('txt_production_qty').value 			= '".$row[csf("receive_qnty")]."';\n";
		echo "document.getElementById('buyer_name').value 					= '".$buyer_name."';\n";
		echo "document.getElementById('buyer_id').value 					= '".$row[csf("buyer_id")]."';\n";
		echo "document.getElementById('cbo_machine_name').value 			= '".$row[csf("machine_name")]."';\n";
		echo "document.getElementById('txt_start_hours').value 				= '".$start_hours."';\n";
		echo "document.getElementById('txt_start_minuties').value 			= '".$start_minutes."';\n";
		echo "document.getElementById('txt_start_date').value 				= '".change_date_format($row[csf("start_date")])."';\n";
		echo "document.getElementById('txt_end_hours').value 				= '".$end_hours."';\n";
		echo "document.getElementById('txt_end_minutes').value 				= '".$end_minutes."';\n";
		echo "document.getElementById('txt_end_date').value 				= '".change_date_format($row[csf("end_date")])."';\n";
		echo "document.getElementById('all_po_id').value 					= '".$row[csf("order_id")]."';\n";
		echo "document.getElementById('update_dtls_id').value 				= '".$row[csf("id")]."';\n";
		
		$save_string='';
		if($roll_maintained==1)
		{
			$data_roll_array=sql_select("select id, po_breakdown_id, qnty,roll_no from pro_roll_details where dtls_id='$id' and entry_form=3 and status_active=1 and is_deleted=0");
			foreach($data_roll_array as $row_roll)
			{ 
				if($save_string=="")
				{
					$save_string=$row_roll[csf("po_breakdown_id")]."**".$row_roll[csf("qnty")]."**".$row_roll[csf("roll_no")];
				}
				else
				{
					$save_string.=",".$row_roll[csf("po_breakdown_id")]."**".$row_roll[csf("qnty")]."**".$row_roll[csf("roll_no")];
				}
			}
		}
		else
		{
			$data_po_array=sql_select("select po_breakdown_id, quantity from order_wise_pro_details where dtls_id='$id' and entry_form=6 and status_active=1 and is_deleted=0");
			foreach($data_po_array as $row_po)
			{ 
				if($save_string=="")
				{
					$save_string=$row_po[csf("po_breakdown_id")]."**".$row_po[csf("quantity")];
				}
				else
				{
					$save_string.=",".$row_po[csf("po_breakdown_id")]."**".$row_po[csf("quantity")];
				}
			}
		}
		echo "document.getElementById('save_data').value 				= '".$save_string."';\n";
		
		echo "set_button_status(1, '".$_SESSION['page_permission']."', 'fnc_dye_production',1);\n";  
		exit();
	}
}

if($action=="roll_maintained")
{
	
	$roll_maintained=return_field_value("fabric_roll_level","variable_settings_production","company_name ='$data' and variable_list=3 and is_deleted=0 and status_active=1");

	if($roll_maintained=="" || $roll_maintained==2) $roll_maintained=0; else $roll_maintained=$roll_maintained;
	
	echo "document.getElementById('roll_maintained').value 				= '".$roll_maintained."';\n";
	
	echo "reset_form('dyingproductionentry_1','list_fabric_desc_container','','','set_production_besis();','update_id*txt_system_id*cbo_production_basis*cbo_company_id*txt_production_date*cbo_dyeing_source*cbo_dyeing_company*txt_challan_no*cbo_location*txt_remarks*roll_maintained');\n";
	
	exit();	
}

?>