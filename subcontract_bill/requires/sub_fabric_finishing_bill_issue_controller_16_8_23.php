<?
header('Content-type:text/html; charset=utf-8');
session_start();
if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");

include('../../includes/common.php');
require_once('../../includes/class4/class.conditions.php');
require_once('../../includes/class4/class.reports.php');
require_once('../../includes/class4/class.conversions.php');

$permission=$_SESSION['page_permission'];
$data=$_REQUEST['data'];
$action=$_REQUEST['action'];
$bill_on_batchorfab=2;

if ($action=="load_drop_down_location")
{
	echo create_drop_down( "cbo_location_name", 150, "select id,location_name from lib_location where company_id='$data' and status_active =1 and is_deleted=0 order by location_name","id,location_name", 1, "--Select Location--", $selected, "","","","","","",3 );
	exit();	 
}
if ($action=="load_drop_down_location_popup")
{
	echo create_drop_down( "cbo_location_name", 100, "select id,location_name from lib_location where company_id='$data' and status_active =1 and is_deleted=0 order by location_name","id,location_name", 1, "--Select Location--", $selected, "","","","","","",3 );
	exit();	 
}

if ($action=="load_drop_down_party_location")
{
	echo create_drop_down( "cbo_party_location", 150, "select id,location_name from lib_location where company_id='$data' and status_active =1 and is_deleted=0 order by location_name","id,location_name", 1, "--Party Location--", $selected, "","","","","","",3 );
	exit();	 
}

if ($action=="load_drop_down_party_name")
{ 
	$data=explode('_',$data);
	if($data[1]==2)
	{
		echo create_drop_down( "cbo_party_name", 150, "select buy.id,buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id  and b.tag_company='$data[0]' $buyer_cond  and buy.id in (select  buyer_id from  lib_buyer_party_type where party_type in (2,3)) order by buyer_name","id,buyer_name", 1, "-- Select Party --", $selected, "","","","","","",5 );
	}
	else if($data[1]==1)
	{	
		echo create_drop_down( "cbo_party_name", 150, "select comp.id, comp.company_name from lib_company comp where comp.status_active=1 and comp.is_deleted=0 $company_cond order by comp.company_name","id,company_name",1, "-- Select Party --", $selected, "load_drop_down( 'requires/sub_fabric_finishing_bill_issue_controller', this.value, 'load_drop_down_party_location', 'partylocation_td');","","","","","",5 ); 
	}
	else
	{
		echo create_drop_down( "cbo_party_name", 150, $blank_array,"", 1, "-- Select Party --", $selected, "",0,"","","","",5);
	}
	exit();
}

if ($action=="load_drop_down_party_name_popup")
{ 
	$data=explode('_',$data);
	if($data[1]==2)
	{
		echo create_drop_down( "cbo_party_name", 130, "select buy.id,buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id  and b.tag_company='$data[0]' $buyer_cond  and buy.id in (select  buyer_id from  lib_buyer_party_type where party_type in (2,3)) order by buyer_name","id,buyer_name", 1, "-- Select Party --", $selected, "","","","","","",5 );
	}
	else if($data[1]==1)
	{	
		echo create_drop_down( "cbo_party_name", 130, "select comp.id, comp.company_name from lib_company comp where comp.status_active=1 and comp.is_deleted=0 $company_cond order by comp.company_name","id,company_name",1, "-- Select Party --", $selected, "","","","","","",5 ); 
	}
	else
	{
		echo create_drop_down( "cbo_party_name", 130, $blank_array,"", 1, "-- Select Party --", $selected, "",0,"","","","",5);
	}
	exit();
}

if ($action=="load_variable_settings")
{
	echo "$('#variable_check').val(0);\n";
	echo "$('#bill_on').text('');\n"; 
	$sql_result = sql_select("select variable_list, dyeing_fin_bill from  variable_settings_subcon where company_id='$data' and variable_list in (1,3,8,11) order by id");
 	foreach($sql_result as $result)
	{
		if($result[csf("variable_list")]==1)// bill on qty
		{
			echo "$('#variable_check').val(".$result[csf("dyeing_fin_bill")].");\n";
			if ($result[csf("dyeing_fin_bill")]==1)
			{
				echo "$('#bill_on').text('Bill On Grey Qty');\n"; 
			}
			else if ($result[csf("dyeing_fin_bill")]==2)
			{
				echo "$('#bill_on').text('Bill On Delivery Qty');\n"; 
			}
			else
			{
				echo "$('#bill_on').text('');\n"; 
			}
		}
		else if($result[csf("variable_list")]==3)//rate from
		{
			$rate_from=$result[csf("dyeing_fin_bill")];
			if($rate_from=="") $rate_from=3; else if ($rate_from==0) $rate_from=3;
			else $rate_from=$rate_from;
			
			echo "$('#hidd_rate_from').val(".$rate_from.");\n";
		}
		else if($result[csf("variable_list")]==8)// inhouse bill from
		{
			$finishdata_source=$result[csf("dyeing_fin_bill")];
			if($finishdata_source=="") $finishdata_source=1; else if ($finishdata_source==0) $finishdata_source=1;
			else $finishdata_source=$finishdata_source;
			
			echo "$('#hidd_inhouse_bill_from').val(".$finishdata_source.");\n";
		}
		else if($result[csf("variable_list")]==11)// Control With
		{
			$control_with=$result[csf("dyeing_fin_bill")];
			if($control_with=="") $control_with=0;
			
			echo "$('#hddn_control_with').val(".$control_with.");\n";
		}
	}
 	exit();
}

// ================================Print button ==============================

if($action=="print_button_variable_setting")
{

    $print_report_format=0;
    $print_report_format=return_field_value("format_id","lib_report_template","template_name ='".$data."' and module_id=8 and report_id=266 and is_deleted=0 and status_active=1");
	 $printButton=explode(',',$print_report_format);
	   
	   echo "$('#print1').hide();";
	   echo "$('#short_bill').hide();";
	   echo "$('#print2').hide();";
	   echo "$('#print3').hide();";

	foreach($printButton as $id){				
		if($id==143){echo "$('#print1').show();";}
		else if($id==832){echo "$('#short_bill').show();";}
		else if($id==66){echo "$('#print2').show();";}		
		else if($id==85){echo "$('#print3').show();";}	
	}

    exit();
}
// ======================= End Print button =================================================

if($action=="booking_no_popup")
{
	echo load_html_head_contents("booking Info", "../../", 1, 1,'','','');
	extract($_REQUEST);
	?>
	<script>
		var selected_id = new Array; var selected_name = new Array;
		function check_all_data()
		{
			var tbl_row_count = document.getElementById( 'tbl_list_search' ).rows.length;
			tbl_row_count = tbl_row_count - 1;

			for( var i = 1; i <= tbl_row_count; i++ )
			{
				$('#tr_'+i).trigger('click'); 
			}
		}
		function toggle( x, origColor ) {
			var newColor = 'yellow';
			if ( x.style ) {
				x.style.backgroundColor = ( newColor == x.style.backgroundColor )? origColor : newColor;
			}
		}
		function js_set_value( str ) {
			if (str!="") str=str.split("_");
			toggle( document.getElementById( 'tr_' + str[0] ), '#FFFFCC' );
			if( jQuery.inArray( str[1], selected_id ) == -1 ) {
				selected_id.push( str[1] );
				selected_name.push( str[2] );
			}
			else {
				for( var i = 0; i < selected_id.length; i++ ) {
					if( selected_id[i] == str[1] ) break;
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
			$('#hide_booking_id').val( id );
			$('#hide_booking_no').val( name );
		}
    </script>
    </head>
    <body>
    <div align="center">
	<form name="styleRef_form" id="styleRef_form">
		<fieldset style="width:580px;">
            <table width="570" cellspacing="0" cellpadding="0" border="1" rules="all" align="center" class="rpt_table" id="tbl_list">
            	<thead>
                    <th>Company</th>
                    <th>Location</th>
					<th>Year</th>
                    <th>Booking No</th>
                    <th>Receive date Range</th>
                    <th>
						<input type="reset" name="button" class="formbutton" value="Reset" style="width:70px;" onClick="reset_form('styleRef_form','search_div','','','','');">						
						<input type="hidden" name="hide_booking_id" id="hide_booking_id" value="" />
                   		<input type="hidden" name="hide_booking_no" id="hide_booking_no" value="" /> 
                    </th> 
                </thead>
                <tbody>
                	<tr>
                        <td align="center">
                        <? 
							echo create_drop_down( "cbo_company_id", 100, "select id,company_name from lib_company comp where status_active=1 and is_deleted=0 and core_business not in(3) $company_cond order by company_name","id,company_name",1, "-- Select Company --", '', "load_drop_down('sub_fabric_finishing_bill_issue_controller', this.value, 'load_drop_down_location_popup', 'location_td' );",2);
						?>
                        </td>                 
                        <td align="center" id="location_td">	
                    	<?
                       		echo create_drop_down( "cbo_location_name", 100, $blank_array,"", 1, "--Select Location--", $selected,"","","","","","",3);
						
						?>
                        </td>     
                        <td align="center" >				
                        <?
							 echo create_drop_down( "cbo_job_year", 60, $year,"", 1, "-- Select --", date('Y'), "",0 );
						?>	
                        </td> 	
						<td><input name="txt_book_no" id="txt_book_no" class="text_boxes" style="width:60px"></td>

						<td><input class="datepicker" type="text" style="width:55px" name="txt_date_from" id="txt_date_from" placeholder="Form Date" />&nbsp;<input class="datepicker" type="text" style="width:55px" name="txt_date_to" id="txt_date_to" placeholder="To Date" />
                            </td>
                        <td align="center">
						<input type="button" name="button2" class="formbutton" value="Show" onClick="show_list_view ( document.getElementById('cbo_company_id').value+'_'+document.getElementById('cbo_location_name').value+'_'+document.getElementById('cbo_job_year').value+'_'+document.getElementById('txt_book_no').value+'_'+document.getElementById('txt_date_from').value+'_'+document.getElementById('txt_date_to').value, 'create_booking_search_list_view','search_div','sub_fabric_finishing_bill_issue_controller','setFilterGrid(\'list_view\',-1); set_all();');" style="width:70px;" />
                    </td>
                    </tr>
            	</tbody>
           	</table>
            <div style="margin-top:15px" id="search_div"></div>
		</fieldset>
	</form>
    </div>
    </body>           
    <script src="../../includes/functions_bottom.js" type="text/javascript"></script>
    </html>
    <?
	exit(); 
}
if($action=="create_booking_search_list_view")
{
	$data=explode('_',$data);
	if ($data[0]!=0) $company=" and a.company_id='$data[0]'"; else { echo "Please Select Company First."; die; }
	if ($data[1]!=0) $location=" and a.location_id='$data[1]'"; else $location="";
	$year_cond=" and to_char(b.insert_date,'YYYY')=$data[2]";
	if (str_replace("'","",$data[3])!="") $booking_cond=" and b.booking_no_prefix_num like '%$data[3]%'  $booking_year_cond  "; else  $booking_cond="";
	if ($data[4]!="" &&  $data[5]!="") $booking_date  = "and a.receive_date  between '".change_date_format($data[4], "yyyy-mm-dd", "-",1)."' and '".change_date_format($data[5], "yyyy-mm-dd", "-",1)."'"; else $booking_date ="";
	$company_arr=return_library_array( "select id, company_name from lib_company",'id','company_name');
	$arr=array (0=>$company_arr);
	$sql= "select a.booking_no,a.challan_no,b.job_no from inv_receive_master a,wo_booking_mst b where a.BOOKING_ID=b.id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.RECEIVE_BASIS=2 $company $location $booking_cond $booking_date $year_cond order by  a.booking_no";
	echo create_list_view("tbl_list_search", "Booking_no,Job No,Sys. Challan", "120,130,140","600","240",0, $sql , "js_set_value", "id,job_no_prefix_num", "", 1, "0,0,0", $arr , "booking_no,job_no,challan_no", "",'','0,0,0','',1) ;
   exit(); 
} // Job Search end

if ($action=="bill_no_popup")
{
	echo load_html_head_contents("Popup Info","../../", 1, 1, $unicode,'','');
	$exdata=explode('_',$data);
	?>
	<script>
		function js_set_value(id)
		{ 
			document.getElementById('issue_id').value=id;
			document.getElementById('company_id').value=$('#cbo_company_id').val();
			parent.emailwindow.hide();
		}
		
		function fnc_chanllan(val)
		{
			if(val==1) 
			{
				 $('#txt_search_challan').removeAttr('disabled','disabled');
				  document.getElementById('th_challan').innerHTML='Rec. Challan No';
			}
			else if(val==2)  //Delivery Challan.
			{
				 $('#txt_search_challan').removeAttr('disabled','disabled');
				  
				  document.getElementById('th_challan').innerHTML='Delivey Challan';
			}
			else 
			{
				 $('#txt_search_challan').attr('disabled','disabled');
				  document.getElementById('th_challan').innerHTML='Rec. Challan No';
			}
		}
	</script>
	</head>
	<body>
        <div align="center">
            <form name="dyingfinishingbill_1"  id="dyingfinishingbill_1" autocomplete="off">
                <table width="830" cellspacing="0" cellpadding="0" border="1" rules="all" class="rpt_table" align="center">
                    <thead>                	 
                        <th width="130">Company Name</th>
                        <th width="100">Source</th>
                        <th width="130">Party Name</th>
                        <th width="80">Issue ID</th>
                        <th width="80" id="th_challan">Rec. Challan No</th>
                        <th width="130" colspan="2">Bill Date Range</th>
                        <th width="80">Batch</th>
                        <th><input type="reset" name="reset" id="reset" class="formbutton" value="Reset" style="width:70px;" /></th>           
                    </thead>
                    <tbody>
                        <tr class="general">
                            <td> 
                                <input type="hidden" id="issue_id">
                                <input type="hidden" id="company_id">
                                <?   
                                    echo create_drop_down( "cbo_company_id", 130, "select id,company_name from lib_company comp where status_active =1 and is_deleted=0 $company_cond order by company_name","id,company_name",1, "-- Select Company --", $exdata[0],"",0 );
                                ?>
                            </td>
                            <td><? echo create_drop_down( "cbo_party_source", 100, $knitting_source,"", 1, "-- Select Party --", $selected, "load_drop_down( 'sub_fabric_finishing_bill_issue_controller',document.getElementById('cbo_company_id').value+'_'+this.value, 'load_drop_down_party_name_popup', 'party_td' ); fnc_chanllan(this.value); ",0,"1,2","","","",4); ?></td>
                            <td id="party_td"><? echo create_drop_down( "cbo_party_name", 130, $blank_array,"", 1, "-- Select Party --", $selected, "",0,"","","","",5); ?></td>
                            <td><input type="text" name="txt_search_common" id="txt_search_common" class="text_boxes_numeric" style="width:70px" /></td>
                            <td><input type="text" name="txt_search_challan" id="txt_search_challan" class="text_boxes" style="width:70px" placeholder="Write" disabled /></td>
                            <td><input name="txt_date_from" id="txt_date_from" class="datepicker" style="width:55px"></td>
                            <td><input name="txt_date_to" id="txt_date_to" class="datepicker" style="width:55px"></td>
                            <td><input type="text" name="txt_search_batch" id="txt_search_batch" class="text_boxes" style="width:70px" /></td>
                            <td>
                                <input type="button" name="button2" class="formbutton" value="Show" onClick="show_list_view ( document.getElementById('cbo_company_id').value+'_'+document.getElementById('cbo_party_name').value+'_'+document.getElementById('txt_date_from').value+'_'+document.getElementById('txt_date_to').value+'_'+document.getElementById('txt_search_common').value+'_'+document.getElementById('txt_search_challan').value+'_'+document.getElementById('txt_search_batch').value+'_'+document.getElementById('cbo_party_source').value+'_'+document.getElementById('cbo_year_selection').value, 'dyeingfinishing_bill_list_view', 'search_div', 'sub_fabric_finishing_bill_issue_controller', 'setFilterGrid(\'list_view\',-1)')" style="width:70px;" />
                            </td>
                        </tr>
                        <tr><td colspan="9" align="center" valign="middle"><? echo load_month_buttons(1); ?></td></tr>
                    </tbody>
                </table>
                <div id="search_div"></div>    
            </form>
        </div>
	</body>           
	<script src="../../includes/functions_bottom.js" type="text/javascript"></script>
	</html>
	<?
	exit();
}

if ($action=="dyeingfinishing_bill_list_view")
{
	$data=explode('_',$data);
	if ($data[0]!=0) $company_name=" and a.company_id='$data[0]'"; else { echo "Please Select Company First."; die; }
	if ($data[1]!=0) $party_name=" and a.party_id='$data[1]'"; else $party_name="";

	if ($data[0]!=0 && $data[2]=="" &&  $data[3]=="" && $data[4]=='')
	{

		echo "Please Select Issue Id/Date Range."; die; 
	}
	
	if($db_type==0)
	{ 
		if ($data[2]!="" &&  $data[3]!="") $return_date = "and a.bill_date between '".change_date_format($data[2],'yyyy-mm-dd')."' and '".change_date_format($data[3],'yyyy-mm-dd')."'"; else $return_date ="";
	}
	else
	{
		if ($data[2]!="" &&  $data[3]!="") $return_date = "and a.bill_date between '".change_date_format($data[2], "", "",1)."' and '".change_date_format($data[3], "", "",1)."'"; else $return_date ="";
	}
	
	if ($data[4]!='') $bill_id_cond=" and a.prefix_no_num='$data[4]'"; else $bill_id_cond="";
	//if ($data[5]!='') $recChallan_cond=" and challan_no='$data[5]'"; else $recChallan_cond="";
	$party_source=$data[7];
	$year_id=$data[8];
	$bill_year_cond="";
	if($year_id!=0)
	{
		if($db_type==0)
		{
			$bill_year_cond=" and year(a.insert_date)='$year_id'";
		}
		else
		{
			$bill_year_cond=" and to_char(a.insert_date,'YYYY')='$year_id'";	
		}
	}
	
	if($party_source==1)
	{
	if ($data[5]!='') $recChallan_cond=" and challan_no='$data[5]'"; else $recChallan_cond="";
	}
	else
	{
	if ($data[5]!='') $DeliveryChallan_cond=" and a.delivery_prefix_num='$data[5]'"; else $DeliveryChallan_cond="";
	}
	
	//echo $party_source.'='.$data[5];
if($party_source==1)
{
	$ttl_msg="rec";
	$rec_man_challan_arr=array();
	$sql_rec="select id, challan_no from inv_receive_master where status_active=1 and is_deleted=0 $recChallan_cond";
	$sql_rec_result = sql_select($sql_rec); $recId=""; $tot_rows=0;
	foreach($sql_rec_result as $row)
	{
		$tot_rows++;
		$rec_man_challan_arr[$row[csf("id")]]=$row[csf("challan_no")];
		$recId.="'".$row[csf("id")]."',";
	}
	unset($sql_rec_result);
	$rec_id_cond="";
	if ($data[5]!='')
	{
		$recIds=chop($recId,','); 
		if($db_type==2 && $tot_rows>1000)
		{
			$rec_id_cond=" and (";
			$recIdsArr=array_chunk(explode(",",$recIds),999);
			foreach($recIdsArr as $ids)
			{
				$ids=implode(",",$ids);
				$rec_id_cond.=" b.delivery_id in($ids) or ";
			}
			$rec_id_cond=chop($rec_id_cond,'or ');
			$rec_id_cond.=")";
		}
		else $rec_id_cond=" and b.delivery_id in ($recIds)";
	}
}
	
	if($db_type==0) $bid_cond="group_concat(id)";
	else if($db_type==2) $bid_cond="listagg(id,',') within group (order by id)";
	
	$batchidCond="";
	
	if($data[6]!='')
	{ 
		$batch_ids = return_field_value("$bid_cond as id", "pro_batch_create_mst", "batch_no='$data[6]' and status_active=1 and is_deleted=0", "id");
		if ($batch_ids!="") $batchidCond=" and b.batch_id in ($batch_ids)"; else $batchidCond="Batch Not found.".die;
	}
	if($party_source==2)
    {
		$ttl_msg="Delivery";
	$sub_del_challan_arr=array();
	 $sql_sub_challan="select a.delivery_no,a.challan_no, b.id from subcon_delivery_mst a, subcon_delivery_dtls b where a.id=b.mst_id  and a.status_active=1 and a.is_deleted=0 $DeliveryChallan_cond $bill_year_cond $company_name and a.process_id=4";
	$sql_sub_challan_result = sql_select($sql_sub_challan);
	foreach ($sql_sub_challan_result as $row)
	{
		if($row[csf("delivery_no")]!='')
		{
		$sub_del_challan_arr[$row[csf("id")]]=$row[csf("delivery_no")];
		$sub_deli_id_arr[$row[csf("id")]]=$row[csf("id")];
		}
		
	}
	unset($sql_sub_challan_result);
	$deli_id_cond = where_con_using_array($sub_deli_id_arr,1,"b.delivery_id");
   }
	//echo $deli_id_cond;die;
	
	$company_id=return_library_array( "select id, company_name from lib_company",'id','company_name');
	$location_arr=return_library_array( "select id,location_name from lib_location",'id','location_name');
	$party_arr=return_library_array( "select id,buyer_name from lib_buyer",'id','buyer_name');
	$batch_arr=return_library_array( "select id, batch_no from pro_batch_create_mst where status_active=1 and is_deleted=0",'id','batch_no');
	
	$arr=array (2=>$location,4=>$party_arr,5=>$knitting_source,6=>$bill_for);
	
	if($db_type==0)
	{
		$year_cond= "year(a.insert_date)as year";
		$delivery_id_cond="group_concat(b.delivery_id)";
	}
	else if($db_type==2)
	{
		$year_cond= "TO_CHAR(a.insert_date,'YYYY') as year";
		$delivery_id_cond="LISTAGG(CAST(b.delivery_id AS VARCHAR2(4000)),',') WITHIN GROUP ( ORDER BY b.delivery_id)";
	}
	//group by a.id, a.bill_no, a.prefix_no_num, a.insert_date, a.location_id, a.bill_date, a.party_id, a.party_source, a.bill_for, c.batch_no
	
	 $sql= "select a.id, a.bill_no, a.prefix_no_num, $year_cond, a.location_id, a.bill_date, a.party_id, a.party_source, a.bill_for,b.delivery_id, c.batch_no
	from subcon_inbound_bill_mst a, subcon_inbound_bill_dtls b, pro_batch_create_mst c
	where a.id=b.mst_id and a.process_id=4 and a.status_active=1 $company_name $party_name $return_date $bill_id_cond $rec_id_cond $batchidCond and b.batch_id=c.id $bill_year_cond $deli_id_cond
	
	order by a.id DESC"; 
	
	// $sql= "select a.id, a.bill_no, a.prefix_no_num, $year_cond, a.location_id, a.bill_date, a.party_id, a.party_source, a.bill_for, b.delivery_id as delivery_id from subcon_inbound_bill_mst a, subcon_inbound_bill_dtls b where a.id=b.mst_id and a.process_id=4 and a.status_active=1 $company_name $party_name $return_date $bill_id_cond $rec_id_cond  order by a.id DESC";
	 //group by a.id, a.bill_no, a.prefix_no_num, a.insert_date, a.location_id, a.bill_date, a.party_id, a.party_source, a.bill_for
	 $result = sql_select($sql);
	  foreach( $result as $row )
		{
			$fin_bill_issue_arr[$row[csf("bill_no")]]['id']=$row[csf("id")];
			$fin_bill_issue_arr[$row[csf("bill_no")]]['prefix_no_num']=$row[csf("prefix_no_num")];
			$fin_bill_issue_arr[$row[csf("bill_no")]]['year']=$row[csf("year")];
			$fin_bill_issue_arr[$row[csf("bill_no")]]['delivery_id'].=$row[csf("delivery_id")].',';
			$fin_bill_issue_arr[$row[csf("bill_no")]]['location_id']=$row[csf("location_id")];
			$fin_bill_issue_arr[$row[csf("bill_no")]]['bill_date']=$row[csf("bill_date")];
			$fin_bill_issue_arr[$row[csf("bill_no")]]['party_id']=$row[csf("party_id")];
			$fin_bill_issue_arr[$row[csf("bill_no")]]['batch_no']=$row[csf("batch_no")];
			$fin_bill_issue_arr[$row[csf("bill_no")]]['party_source']=$row[csf("party_source")];
			$fin_bill_issue_arr[$row[csf("bill_no")]]['bill_for']=$row[csf("bill_for")];
		}
		
	?>
	<div>
     	<table cellspacing="0" cellpadding="0" border="1" rules="all" width="810" class="rpt_table">
            <thead>
                <th width="30">SL</th>
                <th width="60">Bill No</th>
                <th width="60">Year</th>
                <th width="110">Location</th>
                <th width="110">Source</th>
                <th width="60">Bill Date</th>
               
                <th width="120">Party</th>
                <th width="80">Bill For</th>
                <th width="100"><?=$ttl_msg; ?><br>Challan No</th>
                <th>Batch</th>
            </thead>
     	</table>
     </div>
     <div style="width:810px; max-height:250px;overflow-y:scroll;" >	 
        <table cellspacing="0" cellpadding="0" border="1" rules="all" width="790" class="rpt_table" id="list_view">
			<?
			$i=1; 
           // foreach( $result as $row )
		     foreach( $fin_bill_issue_arr as $bill_no=>$row )
            {
                if ($i%2==0)  $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
				$challan_no=""; $bill_company="";
				if($row[("party_source")]==1) 
				{
					$bill_company=$company_id[$row[("party_id")]];
					//$challan_no=$rec_man_challan_arr[$row[csf("delivery_id")]];
					$delivery_id=explode(",",$row[("delivery_id")]);
					$ex_del_id=rtrim($delivery_id,',');
					foreach($ex_del_id as $del_id)
					{
						if ($challan_no=="") $challan_no=$rec_man_challan_arr[$del_id]; else $challan_no.=','.$rec_man_challan_arr[$del_id];
					}
				}
				elseif($row[("party_source")]==2) 
				{
					$bill_company=$party_arr[$row[("party_id")]];
					$delivery_id=rtrim($row[("delivery_id")],',');
					$ex_del_id=array_unique(explode("_",$delivery_id));
					foreach($ex_del_id as $del_id)
					{
						//echo $sub_del_challan_arr[$del_id].'='.$del_id.'D';
					 if ($challan_no=="") $challan_no=$sub_del_challan_arr[$del_id]; else $challan_no.=','.$sub_del_challan_arr[$del_id];
					}
				}
				$unique_challan=implode(",",array_unique(explode(',',$challan_no)));
				
				//if($row[csf("party_source")]==1) $bill_company=$company_id[$row[csf("party_id")]]; else $bill_company=$party_arr[$row[csf("party_id")]];
				?>
					<tr bgcolor="<? echo $bgcolor; ?>" style="text-decoration:none;cursor:pointer" onClick="js_set_value(<? echo $row[("id")];?>);" > 
						<td width="30"><? echo $i; ?></td>
						<td width="60"><? echo $row[("prefix_no_num")]; ?></td>
                        <td width="60"><? echo $row[("year")]; ?></td>		
						<td width="110"><? echo $location_arr[$row[("location_id")]];  ?></td>	
                        <td width="110" style="word-break:break-all"><p><? echo $knitting_source[$row[("party_source")]];  ?></p></td>
						<td width="60"><? echo change_date_format($row[("bill_date")]); ?></td>
						<td width="120"><? echo $bill_company;?> </td>	
						<td width="80"><? echo $bill_for[$row[("bill_for")]]; ?></td>
                        <td width="100" style="word-wrap:break-word; word-break: break-all;"><? echo $unique_challan; ?>&nbsp;</td>
                        <td style="word-wrap:break-word; word-break: break-all;">
                        	<?php echo $row[('batch_no')]; ?>
                        </td>
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

if ($action=="load_php_data_to_form_issue")
{
	$sql="SELECT min(delivery_date) as min_date, max(delivery_date) as max_date FROM subcon_inbound_bill_dtls WHERE mst_id='$data' and status_active=1 and is_deleted=0 group by mst_id";
	
	$sql_result_arr =sql_select($sql); 
	$mindate='';  $maxdate='';
	$mindate=$sql_result_arr[0][csf('min_date')];
	$maxdate=$sql_result_arr[0][csf('max_date')];
	unset($sql_result_arr);
	
	$nameArray= sql_select("select id, bill_no, company_id,upcharge,discount, location_id, bill_date, party_id, party_source, party_location_id, inhouse_bill_from, bill_for, is_posted_account,post_integration_unlock,remarks from subcon_inbound_bill_mst where id='$data'");
	foreach ($nameArray as $row)
	{	
		echo "load_drop_down( 'requires/sub_fabric_finishing_bill_issue_controller', '".$row[csf("company_id")]."', 'load_drop_down_location', 'location_td');\n";
		echo "load_drop_down( 'requires/sub_fabric_finishing_bill_issue_controller', '".$row[csf("company_id")]."'+'_'+'".$row[csf("party_source")]."', 'load_drop_down_party_name', 'party_td' );\n";
		echo "load_drop_down( 'requires/sub_fabric_finishing_bill_issue_controller', '".$row[csf("party_id")]."', 'load_drop_down_party_location', 'partylocation_td' );\n";
		
		echo "document.getElementById('txt_bill_no').value 					= '".$row[csf("bill_no")]."';\n";
		echo "document.getElementById('cbo_company_id').value 				= '".$row[csf("company_id")]."';\n";
		echo "document.getElementById('cbo_location_name').value			= '".$row[csf("location_id")]."';\n"; 
		echo "document.getElementById('txt_bill_date').value 				= '".change_date_format($row[csf("bill_date")])."';\n";   
		echo "document.getElementById('cbo_party_source').value				= '".$row[csf("party_source")]."';\n"; 
		echo "document.getElementById('cbo_party_name').value				= '".$row[csf("party_id")]."';\n"; 
		
		echo "document.getElementById('txt_upcharge').value				= '".$row[csf("upcharge")]."';\n"; 
		echo "document.getElementById('txt_discount').value				= '".$row[csf("discount")]."';\n"; 
		echo "document.getElementById('txt_remarks').value				= '".$row[csf("remarks")]."';\n"; 
		
		echo "document.getElementById('cbo_party_location').value			= '".$row[csf("party_location_id")]."';\n";
		echo "document.getElementById('hidden_acc_integ').value				= '".$row[csf("is_posted_account")]."';\n";
		echo "document.getElementById('hidden_integ_unlock').value			= '".$row[csf("post_integration_unlock")]."';\n";
		if($row[csf("is_posted_account")]==1 && $row[csf("post_integration_unlock")]==0)
		{
			echo "$('#accounting_integration_div').text('All Ready Posted in Accounting.');\n"; 
		}
		else if($row[csf("is_posted_account")]==1 && $row[csf("post_integration_unlock")]==1)
		{
			echo "$('#accounting_integration_div').text('Deleting not allowed since posted in Accounts.Only Data changing is allowed.');\n"; 
		}
		else 
		{
			echo "$('#accounting_integration_div').text('');\n"; 
		}
		if($row[csf("inhouse_bill_from")]==0) $row[csf("inhouse_bill_from")]=1;
		
		echo "document.getElementById('hidd_inhouse_bill_from').value		= '".$row[csf("inhouse_bill_from")]."';\n"; 
		echo "document.getElementById('cbo_bill_for').value					= '".$row[csf("bill_for")]."';\n"; 
		echo "document.getElementById('txt_bill_form_date').value 			= '".change_date_format($mindate)."';\n";  
		echo "document.getElementById('txt_bill_to_date').value 			= '".change_date_format($maxdate)."';\n";  
	    echo "document.getElementById('update_id').value            		= '".$row[csf("id")]."';\n";
		
		/*echo "$('#cbo_company_id').attr('disabled','true')".";\n";
		echo "$('#cbo_party_source').attr('disabled','true')".";\n";
		echo "$('#cbo_party_name').attr('disabled','true')".";\n";
		echo "$('#cbo_location_name').attr('disabled','true')".";\n";
		echo "$('#cbo_party_location').attr('disabled','true')".";\n";*/
		
		//echo "show_list_view(document.getElementById('cbo_party_source').value+'_'+document.getElementById('cbo_party_name').value+'_'+document.getElementById('update_id').value+'_'+document.getElementById('issue_id_all').value,'dyingfinishing_delivery_list_view','dyeingfinishing_info_list','requires/sub_fabric_finishing_bill_issue_controller','set_all()');\n";
		echo "set_button_status(1, permission, 'fnc_dyeing_finishing_bill_issue',1);\n";
	}
	exit();
}

if ($action=="dyingfinishing_delivery_list_view") 
{
	echo load_html_head_contents("Popup Info","../", 1, 1, $unicode,1,'');
	//echo $data;
	$data=explode('***',$data);
	$ex_bill_for=$data[4];
	//$ex_bill_for=$data[4];
	$date_from=$data[5];
	$date_to=$data[6];
	$manual_challan_no=$data[7];
	$variable_check=$data[8];
	$inhouse_bill_from=$data[9];
	$sys_challan=$data[10];
	$batch_no=$data[11];
	$update_id=$data[12];
	$str_data=trim($data[13]);
	//print_r($str_data);
//echo $batch_no.'DSDD';
	
 //	echo $data[2].'='.$inhouse_bill_from;
 $composition_arr=array(); $constructtion_arr=array();
 	$sql_deter="select a.id, a.construction, b.copmposition_id, b.percent from lib_yarn_count_determina_mst a, lib_yarn_count_determina_dtls b where a.id=b.mst_id";
	$data_array=sql_select($sql_deter);
	foreach( $data_array as $row )
	{
		if(array_key_exists($row[csf('id')],$composition_arr))
		{
			$composition_arr[$row[csf('id')]]=$composition_arr[$row[csf('id')]]." ".$composition[$row[csf('copmposition_id')]]." ".$row[csf('percent')]."%";
		}
		else
		{
			$composition_arr[$row[csf('id')]]=$row[csf('construction')].", ".$composition[$row[csf('copmposition_id')]]." ".$row[csf('percent')]."%";
		}
	}
	
	if($data[2]==2)
	{
		if($db_type==0)
		{ 
			if ($date_from!="" &&  $date_to!="") $date_cond= "and a.delivery_date between '".change_date_format($date_from,'yyyy-mm-dd')."' and '".change_date_format($date_to,'yyyy-mm-dd')."'"; else $date_cond= "";
			$year_cond= "year(b.insert_date)as year";
		}
		else if ($db_type==2)
		{
			if ($date_from!="" &&  $date_to!="") $date_cond= "and a.delivery_date between '".change_date_format($date_from, "", "",1)."' and '".change_date_format($date_to, "", "",1)."'";  else $date_cond= "";
			$year_cond= "TO_CHAR(b.insert_date,'YYYY') as year";
		}
		$delv_id=implode(',',explode('!!!!',$str_data));
		?>
            <div >
                <table cellspacing="0" cellpadding="0" border="1" rules="all"  align="center" width="1000px" class="rpt_table">
                    <thead>
                    	<th width="30">&nbsp;</th>
						<th width="30">SL</th>
						<th width="90">Process</th>
						<th width="80">Challan No</th>
						<th width="60">Delivery Date</th>
						<th width="100">Order No</th>
                        <th width="100">Batch No</th>
                        <th width="110">Sub-Process</th>                    
						<th width="130">Fabric Description</th>
                        <th width="70">Grey Used Qty</th>
						<th width="70">Delivery Qty</th>
						<th>Currency</th>
                    </thead>
                 </table>
            </div>
            <div style="width:1000px;max-height:180px; overflow-y:scroll" id="sewing_production_list_view" align="center">
                <table cellspacing="0" cellpadding="0" border="1" rules="all" width="980px" class="rpt_table" id="tbl_list_search">
				<? 
				$color_name=return_library_array( "select id, color_name from lib_color where status_active=1 and is_deleted=0",'id','color_name');
				$rec_febricdesc_arr=return_library_array( "select id,material_description from sub_material_dtls",'id','material_description');
				$prod_febricdesc_arr=return_library_array( "select id,fabric_description from subcon_production_dtls",'id','fabric_description');
				$batch_arr=return_library_array( "select id, batch_no from pro_batch_create_mst",'id','batch_no');
				
				$sql="select id, item_category_id,product_name_details from product_details_master where status_active=1 and is_deleted=0"; 
			$nameArray=sql_select($sql);
			foreach($nameArray as $row){
				if($row[csf('item_category_id')]==5 || $row[csf('item_category_id')]==6 || $row[csf('item_category_id')]==7)
				{
				$product_data_arr[$row[csf('id')]]=$row[csf('item_category_id')];
				}
				
				$product_dtls_arr[$row[csf('id')]]=$row[csf('product_name_details')];
			}
			unset($nameArray);
			
				
				$grey_qty_array=array(); $grey_fabric_array=array();
				
				if($batch_no!="") $batch_no_cond=" and a.batch_no='$batch_no'"; else $batch_no_cond="";
				
			 	$grey_sql="Select a.id as batch_id,a.batch_against,a.color_range_id,b.id, b.mst_id, b.fabric_from, b.po_id, b.item_description, b.fin_dia, b.batch_qnty from pro_batch_create_mst a, pro_batch_create_dtls b where a.id=b.mst_id and a.entry_form=36 and b.status_active=1 and b.is_deleted=0 $batch_no_cond";
				$grey_sql_result =sql_select($grey_sql);
				$batchId="";
				foreach($grey_sql_result as $row)
				{
					$batchId.=$row[csf('mst_id')].",";
					$item_name=explode(',',$row[csf('item_description')]);
					$grey_qty_array[$row[csf('po_id')]][$row[csf('id')]]=$row[csf('batch_qnty')];
					$color_range_qty_array[$row[csf('batch_id')]]=$row[csf('color_range_id')];
					$grey_fabric_array[$row[csf('id')]]=$row[csf('item_description')];
					if($row[csf('batch_against')]==2)
					{
					$re_batch_chk_array[$row[csf('batch_id')]]=$row[csf('batch_id')];
					}
				}
				unset($grey_sql_result);
				
				$batchIds_cond="";
				if($batch_no!="")
				{
					$exbatchId=array_unique(array_filter(explode(",",$batchId)));
					$batchIds="";  $tot_rows=0;
					foreach($exbatchId as $btchid)
					{
						$tot_rows++;
						$batchIds.=$btchid.",";
					}
					
					$batchIds=chop($batchIds,',');
					if($db_type==2 && $tot_rows>1000)
					{
						$batchIds_cond=" and (";
						$batchIdsArr=array_chunk(explode(",",$batchIds),999);
						foreach($batchIdsArr as $ids)
						{
							$ids=implode(",",$ids);
							$batchIds_cond.=" b.batch_id in($ids) or ";
						}
						$batchIds_cond=chop($batchIds_cond,'or ');
						$batchIds_cond.=")";
					}
					else $batchIds_cond=" and b.batch_id in ($batchIds)"; 
				}
				
				// var_dump($grey_qty_array);
				$garments_itemArr=return_library_array( "select id,const_comp from lib_subcon_charge",'id','const_comp');
				
				$order_arr=array();
				$sql_ord=sql_select("select a.subcon_job, a.job_no_prefix_num, a.currency_id, b.id, b.order_no, b.cust_style_ref, b.cust_buyer,c.rate,c.qnty,c.item_id   from subcon_ord_mst a, subcon_ord_dtls b,subcon_ord_breakdown c where a.subcon_job=b.job_no_mst  and b.id=c.order_id and c.status_active=1");
				//echo "select a.subcon_job, a.job_no_prefix_num, a.currency_id, b.id, b.order_no, b.cust_style_ref, b.cust_buyer,c.rate,c.qnty,c.item_id   from subcon_ord_mst a, subcon_ord_dtls b,subcon_ord_breakdown c where a.subcon_job=b.job_no_mst  and b.id=c.order_id and c.status_active=1";
				
				foreach($sql_ord as $row)
				{
					$order_arr[$row[csf('id')]]['job']=$row[csf('job_no_prefix_num')];
					$order_arr[$row[csf('id')]]['currency']=$row[csf('currency_id')];
					$order_arr[$row[csf('id')]]['po']=$row[csf('order_no')];
					$order_arr[$row[csf('id')]]['style']=$row[csf('cust_style_ref')];
					$order_arr[$row[csf('id')]]['buyer']=$row[csf('cust_buyer')];
					$order_rate_arr[$row[csf('id')]][$garments_itemArr[$row[csf('item_id')]]]['rate']=$row[csf('rate')];
				}
				unset($sql_ord);
				
				/*return_library_array( "select id, order_no from subcon_ord_dtls",'id','order_no');
				$currency_arr=return_library_array( "select b.id, a.currency_id from  subcon_ord_mst a, subcon_ord_dtls b where a.subcon_job=b.job_no_mst",'id','currency_id');*/
                $i=1;
				//$old_delivery_id=implode(',',explode('_',$data[3]));
				// echo $bill_on_batchorfab.'DDDDDDDD=';
				
				if($sys_challan!="") $sys_challan_cond=" and a.delivery_prefix_num in ($sys_challan)"; else $sys_challan_cond="";
				if($manual_challan_no!="") $manual_challan_no_cond=" and a.challan_no in ($manual_challan_no)"; else $manual_challan_no_cond="";
				if($bill_on_batchorfab==1)
				{
					if($db_type==0)
					{
						if(!$update_id)
						{
							$sql="select a.delivery_prefix_num as challan_no, b.width_dia_type, a.delivery_date, b.sub_process_id, sum(b.carton_roll) as carton_roll,b.color_id, b.batch_id, b.order_id, group_concat(b.id SEPARATOR '_') as id, group_concat(b.process_id SEPARATOR '_') as process_id, group_concat(b.item_id SEPARATOR '_') as item_id, sum(b.gray_qty) as gray_qty, sum(b.delivery_qty) as delivery_qty, 0 as type from  subcon_delivery_mst a, subcon_delivery_dtls b where a.company_id='$data[0]' and b.bill_status=0 and a.id=b.mst_id and a.party_id='$data[3]' and b.process_id in (3,4) and a.status_active=1 and a.is_deleted=0 $sys_challan_cond $manual_challan_no_cond $batchIds_cond $date_cond group by a.delivery_prefix_num, b.width_dia_type, a.delivery_date, b.sub_process_id, b.color_id, b.batch_id, b.order_id"; 
						}
						else
						{
							$sql="(select a.delivery_prefix_num as challan_no, b.width_dia_type, a.delivery_date, b.sub_process_id,sum(b.carton_roll) as carton_roll, b.color_id, b.batch_id, b.order_id, group_concat(b.id SEPARATOR '_') as id, group_concat(b.process_id SEPARATOR '_') as process_id, group_concat(b.item_id SEPARATOR '_') as item_id,sum(b.gray_qty) as gray_qty, sum(b.delivery_qty) as delivery_qty, 0 as type from subcon_delivery_mst a, subcon_delivery_dtls b where a.company_id='$data[0]' and a.id=b.mst_id and a.party_id='$data[3]' and b.process_id in (3,4) and a.status_active=1 and b.bill_status=0 $sys_challan_cond $manual_challan_no_cond $batchIds_cond $date_cond group by a.delivery_prefix_num, b.width_dia_type, a.delivery_date,b.sub_process_id, b.color_id, b.batch_id, b.order_id)
							 union 
							 (select a.delivery_prefix_num as challan_no, b.width_dia_type, a.delivery_date,b.sub_process_id,sum(b.carton_roll) as carton_roll, b.color_id, b.batch_id, b.order_id, group_concat(b.id SEPARATOR '_') as id, group_concat(b.process_id SEPARATOR '_') as process_id, group_concat(b.item_id SEPARATOR '_') as item_id,sum(b.gray_qty) as gray_qty, sum(b.delivery_qty) as delivery_qty, 1 as type from subcon_delivery_mst a, subcon_delivery_dtls b where  a.company_id='$data[0]' and a.id=b.mst_id and a.party_id='$data[3]' and b.process_id in (3,4) and b.id in ($delv_id) and a.status_active=1 group by a.delivery_prefix_num, b.width_dia_type, a.delivery_date,b.sub_process_id, b.color_id, b.batch_id, b.order_id) order by type DESC";
						}
					}
					else if ($db_type==2)
					{
						if(!$update_id)
						{
							$sql="select a.delivery_prefix_num as challan_no, b.width_dia_type, a.delivery_date, b.sub_process_id,sum(b.carton_roll) as carton_roll, b.color_id, b.batch_id, b.order_id,
							listagg(b.id,'_') within group (order by b.id) as id,
							listagg(b.process_id,'_') within group (order by b.process_id) as process_id,
							listagg(b.item_id,'_') within group (order by b.item_id) as item_id,sum(b.gray_qty) as gray_qty,
							sum(b.delivery_qty) as delivery_qty, 0 as type
					from  subcon_delivery_mst a, subcon_delivery_dtls b where a.company_id='$data[0]' and b.bill_status=0 and a.id=b.mst_id and a.party_id='$data[3]' and a.process_id=4 and b.process_id in (3,4) and a.status_active=1 and a.is_deleted=0 $sys_challan_cond $manual_challan_no_cond $batchIds_cond $date_cond group by a.delivery_prefix_num, b.width_dia_type, a.delivery_date, b.sub_process_id, b.color_id, b.batch_id, b.order_id order by a.delivery_prefix_num"; 
						}
						else
						{
							$sql="(select a.delivery_prefix_num as challan_no, b.width_dia_type, a.delivery_date, b.sub_process_id,sum(b.carton_roll) as carton_roll, b.color_id, b.batch_id, b.order_id,
							listagg(b.id,'_') within group (order by b.id) as id,
							listagg(b.process_id,'_') within group (order by b.process_id) as process_id,
							listagg(b.item_id,'_') within group (order by b.item_id) as item_id,sum(b.gray_qty) as gray_qty,
							sum(b.delivery_qty) as delivery_qty, 0 as type 
							from subcon_delivery_mst a, subcon_delivery_dtls b where a.company_id='$data[0]' and a.id=b.mst_id and a.party_id='$data[3]' and a.process_id=4 and b.process_id in (3,4) and a.status_active=1 and b.bill_status=0 $sys_challan_cond $manual_challan_no_cond $batchIds_cond $date_cond group by a.delivery_prefix_num, b.width_dia_type, a.delivery_date,b.sub_process_id, b.color_id, b.batch_id, b.order_id)
							 union 
							 (select a.delivery_prefix_num as challan_no, b.width_dia_type, a.delivery_date,b.sub_process_id,sum(b.carton_roll) as carton_roll, b.color_id, b.batch_id, b.order_id,
							 listagg(b.id,'_') within group (order by b.id) as id,
							listagg(b.process_id,'_') within group (order by b.process_id) as process_id,
							listagg(b.item_id,'_') within group (order by b.item_id) as item_id,sum(b.gray_qty) as gray_qty,
							sum(b.delivery_qty) as delivery_qty, 1 as type 
							  from subcon_delivery_mst a, subcon_delivery_dtls b where a.company_id='$data[0]' and a.id=b.mst_id and a.party_id='$data[3]' and a.process_id=4 and b.process_id in (3,4) and b.id in ($delv_id) and a.status_active=1 and b.bill_status=1 group by a.delivery_prefix_num, b.width_dia_type, a.delivery_date,b.sub_process_id, b.color_id, b.batch_id, b.order_id) order by type DESC";
						}
					}
				}
				else
				{
					if($db_type==0)
					{
						if(!$update_id)
						{
							$sql="select a.delivery_prefix_num as challan_no, b.width_dia_type, a.delivery_date, b.sub_process_id,sum(b.carton_roll) as carton_roll,b.color_id, b.batch_id, b.order_id, b.id as id, b.process_id as process_id, b.item_id as item_id, sum(b.gray_qty) as gray_qty, sum(b.delivery_qty) as delivery_qty, 0 as type from  subcon_delivery_mst a, subcon_delivery_dtls b where a.company_id='$data[0]' and b.bill_status=0 and a.id=b.mst_id and a.party_id='$data[3]' and b.process_id in (3,4) and a.status_active=1 and a.is_deleted=0 $sys_challan_cond $manual_challan_no_cond $batchIds_cond $date_cond group by a.delivery_prefix_num, b.width_dia_type, a.delivery_date, b.sub_process_id, b.color_id, b.batch_id, b.order_id, b.id, b.process_id, b.item_id"; 
						}
						else
						{
							$sql="(select a.delivery_prefix_num as challan_no, b.width_dia_type, a.delivery_date, b.sub_process_id,b.carton_roll, b.color_id, b.batch_id, b.order_id, b.id as id, b.process_id as process_id, b.item_id as item_id, sum(b.gray_qty) as gray_qty, sum(b.delivery_qty) as delivery_qty, 0 as type from subcon_delivery_mst a, subcon_delivery_dtls b where a.company_id='$data[0]' and a.id=b.mst_id and a.party_id='$data[3]' and b.process_id in (3,4) and a.status_active=1 and b.bill_status=0 $sys_challan_cond $manual_challan_no_cond $batchIds_cond $date_cond group by a.delivery_prefix_num, b.width_dia_type, a.delivery_date,b.sub_process_id,b.carton_roll, b.color_id, b.batch_id, b.order_id, b.id, b.process_id, b.item_id)
							 union 
							 (select a.delivery_prefix_num as challan_no, b.width_dia_type, a.delivery_date,b.sub_process_id, b.color_id, b.batch_id, b.order_id, b.id as id, b.process_id as process_id, b.item_id as item_id, sum(b.gray_qty) as gray_qty, sum(b.delivery_qty) as delivery_qty, 1 as type from subcon_delivery_mst a, subcon_delivery_dtls b where a.company_id='$data[0]' and a.id=b.mst_id and a.party_id='$data[3]' and b.process_id in (3,4) and b.id in ($delv_id) and a.status_active=1 group by a.delivery_prefix_num, b.width_dia_type, a.delivery_date,b.sub_process_id, b.color_id, b.batch_id, b.order_id, b.id, b.process_id, b.item_id) order by type DESC";
						}
					}
					else if ($db_type==2)
					{
						if(!$update_id)
						{
							$sql="select a.delivery_prefix_num as challan_no, b.width_dia_type, a.delivery_date, b.sub_process_id,sum(b.carton_roll) as carton_roll, b.color_id, b.batch_id, b.order_id, b.id as id, b.process_id as process_id, b.item_id as item_id, sum(b.gray_qty) as gray_qty, sum(b.delivery_qty) as delivery_qty, 0 as type
			from  subcon_delivery_mst a, subcon_delivery_dtls b where a.company_id='$data[0]' and b.bill_status=0 and a.id=b.mst_id and a.party_id='$data[3]' and a.process_id=4 and b.process_id in (3,4) and a.status_active=1 and a.is_deleted=0 $sys_challan_cond $manual_challan_no_cond $batchIds_cond $date_cond group by a.delivery_prefix_num, b.width_dia_type, a.delivery_date, b.sub_process_id, b.color_id, b.batch_id, b.order_id, b.id, b.process_id, b.item_id order by a.delivery_prefix_num"; 
						}
						else
						{
							$sql="(select a.delivery_prefix_num as challan_no, b.width_dia_type, a.delivery_date, b.sub_process_id,sum(b.carton_roll) as carton_roll, b.color_id, b.batch_id, b.order_id,
							b.id as id, b.process_id as process_id, b.item_id as item_id, sum(b.gray_qty) as gray_qty, sum(b.delivery_qty) as delivery_qty, 0 as type 
							from subcon_delivery_mst a, subcon_delivery_dtls b where a.company_id='$data[0]' and a.id=b.mst_id and a.party_id='$data[3]' and a.process_id=4 and b.process_id in (3,4) and a.status_active=1 and b.bill_status=0 $sys_challan_cond $manual_challan_no_cond $batchIds_cond $date_cond group by a.delivery_prefix_num, b.width_dia_type, a.delivery_date,b.sub_process_id, b.color_id, b.batch_id, b.order_id, b.id, b.process_id, b.item_id)
							 union 
							 (select a.delivery_prefix_num as challan_no, b.width_dia_type, a.delivery_date,b.sub_process_id,sum(b.carton_roll) as carton_roll, b.color_id, b.batch_id, b.order_id,
							 b.id as id, b.process_id as process_id, b.item_id as item_id,sum(b.gray_qty) as gray_qty, sum(b.delivery_qty) as delivery_qty, 1 as type 
							  from subcon_delivery_mst a, subcon_delivery_dtls b where a.company_id='$data[0]' and a.id=b.mst_id and a.party_id='$data[3]' and a.process_id=4 and b.process_id in (3,4) and b.id in ($delv_id) and a.status_active=1 and b.bill_status=1 group by a.delivery_prefix_num, b.width_dia_type, a.delivery_date,b.sub_process_id, b.color_id, b.batch_id, b.order_id, b.id, b.process_id, b.item_id) order by type DESC";
						}
					}
				}
				//echo $sql;
				$sql_result =sql_select($sql);
				foreach($sql_result as $row) //$row[csf('batch_id')]
				{
					$batchId_arr[$row[csf('batch_id')]]=$row[csf('batch_id')];
				}
				
				$batchIds=implode(",",$batchId_arr);
				$batchIds_id=implode(",",array_unique(explode(",",$batchIds)));
			 	$sql_color_precentage="select a.id, a.batch_id, b.id as dtls_id, b.prod_id, b.sub_process_id, b.dose_base as item_cat, b.ratio from pro_recipe_entry_mst a, pro_recipe_entry_dtls b where a.id=b.mst_id  and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and b.ratio is not null and b.seq_no is not null and a.batch_id in($batchIds_id)";
				$result_color_precentage=sql_select($sql_color_precentage);
				$batch_item_ratio_total_arr=array();
				$batchTotal_arr= array();
				foreach($result_color_precentage as $row){
					$batch_item_ratio_total_arr[$row[csf('batch_id')]][$product_data_arr[$row[csf('prod_id')]]]['ratio_total'] += $row[csf('ratio')];
					$batch_item_ratio_total_arr[$row[csf('batch_id')]][$product_data_arr[$row[csf('prod_id')]]]['ratio_count'] += 1;
					$batchTotal_arr[$row[csf('batch_id')]]['batch_ratio_total']  += $row[csf('ratio')];
				}
				unset($result_color_precentage);
					
				foreach($sql_result as $row) //$row[csf('batch_id')]
				{
					if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
					$checked_val=2; $ischeck="";
					if ($row[csf('type')]==0) 
					{
						$row_color=$bgcolor; $checked_val=2; $ischeck="";
					}
					else 
					{
						$bgcolor="yellow"; $checked_val=1; $ischeck="checked";
					}
					//echo $row[csf('batch_id')].', ';
					
					$sub_process_id=array_unique(explode(',',$row[csf('sub_process_id')]));
					$subprocess_val='';
					foreach ($sub_process_id as $val)
					{
						if($subprocess_val=='') $subprocess_val=$conversion_cost_head_array[$val]; else $subprocess_val.=" + ".$conversion_cost_head_array[$val];
					}
					
					$process_id=array_unique(explode('_',$row[csf('process_id')]));
					$process_val=''; $process_ids='';
					foreach ($process_id as $val)
					{
						if($process_val=='') $process_val=$production_process[$val]; else $process_val.=", ".$production_process[$val];
						if($process_ids=='') $process_ids=$val; else $process_ids.=", ".$val;
					}
					
					$delivery_id=array_unique(explode(',',$row[csf('id')]));
					$delivery_id_val='';
					foreach ($delivery_id as $val)
					{
						if($delivery_id_val=='') $delivery_id_val=$val; else $delivery_id_val.="_".$val;
					}
					
					$item_id=array_unique(explode('_',$row[csf('item_id')]));
					$item_name=''; $grey_qty=0;
					foreach ($item_id as $val)
					{
						if($item_name=='') $item_name=$grey_fabric_array[$val]; else $item_name.=",<br>".$grey_fabric_array[$val];
						$grey_qty+=$grey_qty_array[$row[csf('order_id')]][$val];
					}
					
					if($variable_check==1)
					{
						$on_bill_qty=$row[csf('gray_qty')];
					}
					else
					{
						$on_bill_qty=$row[csf('delivery_qty')];
					}
					$order_rate=$order_rate_arr[$row[csf('order_id')]][$item_name]['rate'];
				//	echo $order_rate.'<br>';
					
					//$ex_dia_type=implode(",",array_unique(explode(",",$row[csf('width_dia_type')])));
					$width_dia_type=array_unique(explode(",",$row[csf('width_dia_type')]));
					$ex_dia_type=$width_dia_type[0];
					$carton_roll=$row[csf('carton_roll')];
					$color_range_id=$color_range_qty_array[$row[csf('batch_id')]];
					//echo $color_range_id.'DD';
					$BatchTotal 				= $batchTotal_arr[$row[csf('batch_id')]]['batch_ratio_total'];
					$chemicalsTotal 			= $batch_item_ratio_total_arr[$row[csf('batch_id')]][5]['ratio_total'];
					$dyesTotal 					= $batch_item_ratio_total_arr[$row[csf('batch_id')]][6]['ratio_total'];
					$auxiChemicalsTotal			= $batch_item_ratio_total_arr[$row[csf('batch_id')]][7]['ratio_total'];
					
					$percentageOfColor = 0;
					if($dyesTotal>0 && $BatchTotal>0 )
					{
					//	echo $row[csf('batch_id')].'=='.$dyesTotal.'TTTTTTTT'.$BatchTotal;
					 $percentageOfColor=($dyesTotal/$BatchTotal)*100;
					}
					if($re_batch_chk_array[$row[csf('batch_id')]]=="") //Re dying batch checked
					{
					//echo number_format($percentageOfColor, 2, '.', '').'D=D';
					$str_val=$row[csf('id')].'**'.change_date_format($row[csf('delivery_date')]).'**'.$row[csf('challan_no')].'**'.$row[csf('order_id')].'**'.$order_arr[$row[csf('order_id')]]['po'].'**'.$order_arr[$row[csf('order_id')]]['style'].'**'.$order_arr[$row[csf('order_id')]]['buyer'].'**'.$order_arr[$row[csf('order_id')]]['job'].'**'.$carton_roll.'********'.$row[csf('item_id')].'**'.$item_name.'**'.$row[csf('batch_id')].'**'.$row[csf('color_id')].'**'.$color_name[$row[csf('color_id')]].'**'.$row[csf('sub_process_id')].'**'.$subprocess_val.'**'.$ex_dia_type.'**'.$fabric_typee[$ex_dia_type].'**'.number_format($on_bill_qty, 2, '.', '').'**'.$color_range_id.'**'.number_format($percentageOfColor, 2, '.', '').'**'.$order_rate.'**************'.$process_ids;
					
					?>
					<tr id="tr_<? echo $row[csf('id')]; ?>" bgcolor="<? echo $bgcolor; ?>" style="text-decoration:none; cursor:pointer" onClick="js_set_value('<? echo $row[csf('id')]."***".$order_arr[$row[csf('order_id')]]['currency']; ?>');" >
                    	<td width="30" align="center"><input type="checkbox" name="checkid<? echo $i; ?>" id="checkid<? echo $i; ?>" onClick="fnc_check(<? echo $i; ?>)" value="<? echo $checked_val; ?>" <? echo $ischeck; ?> ></td>
						<td width="30"><? echo $i; ?></td>
                        <td width="90"><? echo $process_val; ?></td>
						<td width="80"><p><? echo $row[csf('challan_no')]; ?></p></td>
						<td width="60"><? echo change_date_format($row[csf('delivery_date')]); ?></td>
						<td width="100"><? echo $order_arr[$row[csf('order_id')]]['po']; ?></td>
                        <td width="100"><? echo $batch_arr[$row[csf('batch_id')]]; ?></td>
                        <td width="110"><? echo $subprocess_val; ?></td>
						<td width="130"><p><? echo $item_name; ?></p></td>
                        <td width="70" align="right"><? echo $row[csf('gray_qty')]; ?>&nbsp;</td>
						<td width="70" align="right"><? echo $row[csf('delivery_qty')]; ?>&nbsp;</td>
						<td><? echo $currency[$order_arr[$row[csf('order_id')]]['currency']]; ?>                        
							<input type="hidden" id="strid<? echo $i; ?>" value="<? echo $str_val; ?>">
							<input type="hidden" id="currid<? echo $row[csf('id')]; ?>" style="width:50px" value="<? echo $order_arr[$row[csf('order_id')]]['currency']; ?>">
					    </td>
					</tr>
					<?
					$i++;
					}
				}
				?>
		   </table>
		</div>
		   <table width="933px">
				<tr align="center">
					<td align="center">
						<input type="button" id="show_button" align="middle" class="formbutton" style="width:100px" value="Close" onClick="window_close(0)" />
					</td>
				</tr>
		   </table>
	</body>           
	<script src="../includes/functions_bottom.js" type="text/javascript"></script>
	</html>
    <?
	}
	else if($data[2]==1)
	{
		if($db_type==0)
		{ 
			if ($date_from!="" &&  $date_to!="") $date_cond= "and a.receive_date between '".change_date_format($date_from,'yyyy-mm-dd')."' and '".change_date_format($date_to,'yyyy-mm-dd')."'"; else $date_cond= "";
			$year_cond= "year(b.insert_date)as year";
		}
		else if ($db_type==2)
		{
			if ($date_from!="" &&  $date_to!="") $date_cond= "and a.receive_date between '".change_date_format($date_from, "", "",1)."' and '".change_date_format($date_to, "", "",1)."'";  else $date_cond= "";
			$year_cond= "TO_CHAR(b.insert_date,'YYYY') as year";
		}
		?>
        	<div id="list_view_body">
            <div>
                <table cellspacing="0" cellpadding="0" border="1" rules="all"  align="center" width="1090px" class="rpt_table">
                    <thead>
                    	<th width="30">&nbsp;</th>
                        <th width="30">SL</th>
                        <th width="100">Sys. Challan</th>
                        <th width="50">Rec. Challan</th>
                        <th width="60">Rec. Date</th>
                        <th width="70">Color</th>
                        <th width="50">Job No</th>
                        <th width="90">Style Ref.</th>
                        <th width="80">Order No</th>
                        <th width="70">Batch No</th>
                        <th width="30">Ext.</th>
                        <th width="100">Sub-Process</th>                    
                        <th width="120">Fabric Description</th>
                        <th width="70">Dia/Width Type</th>
                        <th width="60">Grey Qty</th>
                        <th>Rec. Qty</th>
                    </thead>
                 </table>
            </div>
            <div style="width:1090px;max-height:180px; overflow-y:scroll" id="sewing_production_list_view" align="center">
                <table cellspacing="0" cellpadding="0" border="1" rules="all" width="1070px" class="rpt_table" id="tbl_list_search">
				<?
				//$inhouse_bill_from=2;
				 //echo $inhouse_bill_from;
				if($inhouse_bill_from==2)
				{
					if($db_type==0)
					{ 
						if ($date_from!="" &&  $date_to!="") $date_cond= "and a.delevery_date between '".change_date_format($date_from,'yyyy-mm-dd')."' and '".change_date_format($date_to,'yyyy-mm-dd')."'"; else $date_cond= "";
				
					}
					else if ($db_type==2)
					{
						if ($date_from!="" &&  $date_to!="") $date_cond= "and a.delevery_date between '".change_date_format($date_from, "", "",1)."' and '".change_date_format($date_to, "", "",1)."'";  else $date_cond= "";
					}
				}
				//$product_dtls_arr=return_library_array( "select id, product_name_details from product_details_master",'id','product_name_details');
			
				$color_name=return_library_array( "select id, color_name from lib_color where status_active=1 and is_deleted=0",'id','color_name');
				$recive_basis_arr=return_library_array( "select id, receive_basis from inv_receive_master",'id','receive_basis');
				$buyer_arr=return_library_array( "select id, buyer_name from lib_buyer",'id','buyer_name');
				$bill_qty_array=array();
				$sql_bill="select mst_id, challan_no, order_id, febric_description_id, body_part_id, item_id, batch_id, (packing_qnty) as roll_qty, (delivery_qty) as bill_qty, dia_width_type from subcon_inbound_bill_dtls where status_active=1 and is_deleted=0 ";
				 
				$sql_bill_result =sql_select($sql_bill); $str_data="";
				foreach ($sql_bill_result as $row)
				{
					$bill_qty_array[$row[csf('challan_no')]][$row[csf('order_id')]][$row[csf('item_id')]][$row[csf('body_part_id')]][$row[csf('febric_description_id')]][$row[csf('dia_width_type')]][$row[csf('batch_id')]]['qty']+=$row[csf('bill_qty')];
					
					if($row[csf('mst_id')]==$update_id)
					{
						 if($str_data=="") $str_data=$row[csf('challan_no')].'_'.$row[csf('order_id')].'_'.$row[csf('item_id')].'_'.$row[csf('body_part_id')].'_'.$row[csf('febric_description_id')].'_'.$row[csf('dia_width_type')].'_'.$row[csf('batch_id')]; 
						 
						 else $str_data.='!!!!'.$row[csf('challan_no')].'_'.$row[csf('order_id')].'_'.$row[csf('item_id')].'_'.$row[csf('body_part_id')].'_'.$row[csf('febric_description_id')].'_'.$row[csf('dia_width_type')].'_'.$row[csf('batch_id')];
					}
				}
				unset($sql_bill_result);
				//print_r($bill_qty_array);
				
				$ex_str_data=explode("!!!!",$str_data);
				$str_arr=array();
				foreach($ex_str_data as $str)
				{
					$str_arr[]=$str;
				}
				
				$batch_array=array(); $batch_qty_arr=array();
				$batchId=""; $batch_no_cond=""; $batchIds_cond="";
				if($batch_no!="")
				{
					$batch_no_cond=" and a.batch_no='$batch_no'";
					$grey_sql="Select a.id, a.batch_no, a.extention_no,a.batch_against,a.color_range_id, a.process_id, b.po_id, b.prod_id, b.body_part_id, b.batch_qnty as batch_qnty from pro_batch_create_mst a, pro_batch_create_dtls b where a.id=b.mst_id and a.entry_form!=36 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $batch_no_cond";
					$grey_sql_result =sql_select($grey_sql);
					
					foreach($grey_sql_result as $row)
					{
						$batchId.=$row[csf('id')].",";
						$batch_array[$row[csf('id')]]['batch_no']=$row[csf('batch_no')];
						$batch_array[$row[csf('id')]]['extention_no']=$row[csf('extention_no')];
						$batch_array[$row[csf('id')]]['process_id']=$row[csf('process_id')];
						$batch_array[$row[csf('id')]]['color_range_id']=$row[csf('color_range_id')];
						$batch_qty_arr[$row[csf('id')]][$row[csf('po_id')]][$row[csf('body_part_id')]]['batch_qnty']+=$row[csf('batch_qnty')];
						if($row[csf('batch_against')]==2)
						{
						$re_batch_chk_array[$row[csf('id')]]=$row[csf('id')];
						}
					}
					unset($grey_sql_result);
	
					if($batch_no!="")
					{
						$exbatchId=array_unique(array_filter(explode(",",$batchId)));
						$batchIds="";  $tot_rows=0;
						foreach($exbatchId as $btchid)
						{
							$tot_rows++;
							$batchIds.=$btchid.",";
						}
						
						$batchIds=chop($batchIds,',');
						if($db_type==2 && $tot_rows>1000)
						{
							$batchIds_cond=" and (";
							$batchIdsArr=array_chunk(explode(",",$batchIds),999);
							foreach($batchIdsArr as $ids)
							{
								$ids=implode(",",$ids);
								$batchIds_cond.=" d.id in($ids) or ";
							}
							$batchIds_cond=chop($batchIds_cond,'or ');
							$batchIds_cond.=")";
						}
						else $batchIds_cond=" and d.id in ($batchIds)"; 
					}
				}
				// var_dump($grey_qty_array);
				
                $i=1;
				if($manual_challan_no!='') $manual_challan_cond=" and a.challan_no='$manual_challan_no'"; else  $manual_challan_cond="";
				if($ex_bill_for==1) $bill_for_id="Fb"; else if($ex_bill_for==2) $bill_for_id="SM";
				if($ex_bill_for==1) $bill_for_cond=" and d.batch_against='1'"; else if($ex_bill_for==2) $bill_for_cond=" and d.batch_against='3'"; else if($ex_bill_for==3) $bill_for_cond=" and d.booking_without_order='1'";
				$sys_challan_cond="";
				if($inhouse_bill_from==2) //Bill from delivery
				{
					if($sys_challan!="") $sys_challan_cond=" and a.sys_number_prefix_num in ($sys_challan)";
				}
				else
				{
					if($sys_challan!="") $sys_challan_cond=" and a.recv_number_prefix_num in ($sys_challan)";
				}
				//if($ex_bill_for!=3)
			 	//echo $inhouse_bill_from.'='.$inhouse_bill_from;die;
				if($ex_bill_for!=3) 
				{
					if($db_type==0)
					{
						$year_cond="year(a.insert_date)";
						$booking_without_order="IFNULL(d.booking_without_order,0)";
					}
					else if($db_type==2) 
					{
						$year_cond="TO_CHAR(a.insert_date,'YYYY')";
						$booking_without_order="nvl(d.booking_without_order,0)";
					}
					//echo $inhouse_bill_from.'DDDDDDDD=';
					if($inhouse_bill_from==2) //Bill from delivery  and a.sys_number='HAL-FDSR-22-00423'
					{		
						$roll_field_con="LISTAGG(e.roll_id, ',') WITHIN GROUP (ORDER BY e.roll_id desc) as roll_id";
						$roll_field_con="";
						//echo $data[2].'=SS';
						if($data[2]==1){
							$localtion_cond="AND a.location_id=$data[1]";
						}else{
							$localtion_cond="AND c.location_id=$data[1]";
							}
							//,e.process_id,
						      $sql="SELECT a.id, a.entry_form,c.recv_number,c.receive_basis,a.sys_number as sys_number, a.sys_number_prefix_num as recv_number_prefix_num, '' as challan_no, a.delevery_date as receive_date, c.booking_id as bookingno, $year_cond as year, b.batch_id, b.product_id as prod_id, b.bodypart_id as body_part_id, b.determination_id as fabric_description_id, b.color_id, b.width_type as dia_width_type,sum(b.current_delivery) as rec_qnty,sum(e.production_qty) as production_qty, sum(f.grey_used_qty) as grey_used_qty, sum(e.roll_no) as carton_roll, f.po_breakdown_id as po_breakdown_id, d.booking_no_id, d.booking_no
							FROM pro_grey_prod_delivery_mst a, pro_grey_prod_delivery_dtls b, inv_receive_master c, pro_batch_create_mst d, pro_finish_fabric_rcv_dtls e, order_wise_pro_details f
							WHERE a.id=b.mst_id and b.grey_sys_id=c.id and d.id=b.batch_id and c.id=e.mst_id and e.id=f.dtls_id and e.id=b.sys_dtls_id and b.order_id=f.po_breakdown_id 
							
							and a.entry_form in (54,67) AND c.knitting_source=1 AND a.company_id=$data[3] $localtion_cond AND c.knitting_company=$data[0] and c.receive_basis in (0,2,4,5,9,11) and c.item_category=2 and c.entry_form in (7,66) and b.current_delivery>0  and f.entry_form in (7,66)
							and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and d.batch_against in (0,1,2,3) and $booking_without_order=0 $date_cond $manual_challan_cond $sys_challan_cond $batchIds_cond
							group by a.id, a.entry_form,c.recv_number, c.receive_basis,a.sys_number, a.sys_number_prefix_num, a.delevery_date, c.booking_id, a.insert_date, b.batch_id, b.product_id, b.bodypart_id, b.determination_id, b.color_id, b.width_type, f.po_breakdown_id, d.booking_no_id, d.booking_no order by a.sys_number_prefix_num DESC";
					}
					else //Bill from receive
					{
						  $sql="SELECT a.id, a.entry_form, a.recv_number,a.receive_basis,a.recv_number as sys_number, a.recv_number_prefix_num, a.challan_no, a.receive_date, a.booking_id as bookingno, $year_cond as year, b.batch_id, b.prod_id, b.body_part_id,b.process_id, b.fabric_description_id, b.color_id, b.dia_width_type, sum(c.quantity) as rec_qnty, sum(b.grey_used_qty) as grey_used_qty, sum(b.no_of_roll) as carton_roll, c.po_breakdown_id, d.booking_no_id, d.booking_no
							FROM inv_receive_master a, pro_finish_fabric_rcv_dtls b, order_wise_pro_details c, pro_batch_create_mst d
							WHERE a.id=b.mst_id and b.id=c.dtls_id and d.id=b.batch_id and c.trans_type=1 and c.entry_form in (7,37,66,68) and c.trans_id!=0 and a.entry_form in (7,37,66,68) AND a.knitting_source=1 AND a.company_id=$data[3] AND a.location_id=$data[1] and c.quantity>0  AND a.knitting_company=$data[0] and a.receive_basis in (2,4,5,9,11) and a.item_category=2 
							and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and d.batch_against in (0,1,3) and $booking_without_order=0 $date_cond $manual_challan_cond $sys_challan_cond $batchIds_cond
							group by a.id, a.entry_form,a.recv_number, a.receive_basis, a.recv_number,a.recv_number_prefix_num, a.challan_no, a.receive_date, a.booking_id, a.insert_date, b.batch_id, b.prod_id, b.body_part_id, b.fabric_description_id, b.color_id, b.dia_width_type, c.po_breakdown_id,b.process_id, d.booking_no_id, d.booking_no order by a.recv_number_prefix_num DESC";
					}
				}
				else
				{
					if($db_type==0)
					{
						$year_cond="year(a.insert_date)";
						$booking_without_order="IFNULL(d.booking_without_order,0)";
					}
					else if($db_type==2) 
					{
						$year_cond="TO_CHAR(a.insert_date,'YYYY')";
						$booking_without_order="nvl(d.booking_without_order,0)";
					}
					if($inhouse_bill_from==2) //Bill from delivery
					{
						 $sql="SELECT a.id, a.entry_form, c.receive_basis,c.recv_number, a.sys_number as sys_number,a.sys_number_prefix_num as recv_number_prefix_num , '' as challan_no, a.delevery_date as receive_date,  $year_cond as year, b.batch_id, b.product_id as prod_id, b.bodypart_id as body_part_id, b.determination_id as fabric_description_id, b.color_id, b.width_type as dia_width_type, sum(b.current_delivery) as rec_qnty, sum(e.grey_used_qty) as grey_used_qty,sum(e.production_qty) as production_qty, sum(b.roll) as carton_roll, null as po_breakdown_id, null as booking_no_id, d.booking_no as booking_no
							FROM pro_grey_prod_delivery_mst a, pro_grey_prod_delivery_dtls b, inv_receive_master c, pro_batch_create_mst d, pro_finish_fabric_rcv_dtls e
							WHERE a.id=b.mst_id and b.grey_sys_id=c.id and d.id=b.batch_id and c.id=e.mst_id and e.id=b.sys_dtls_id 
							
							and a.entry_form in (54,67) AND c.knitting_source=1 AND a.company_id=$data[3] AND a.location_id=$data[1] AND c.knitting_company=$data[0] and c.receive_basis in (0,2,4,5,9,11) and c.item_category=2 and c.entry_form in (7,66) and b.current_delivery>0
							and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and d.batch_against in (0,1,2,3) and $booking_without_order=1 $date_cond $manual_challan_cond $sys_challan_cond $batchIds_cond
							group by a.id, a.entry_form,c.recv_number, c.receive_basis,a.sys_number, a.sys_number_prefix_num, a.delevery_date, d.booking_no, a.insert_date, b.batch_id, b.product_id, b.bodypart_id, b.determination_id, b.color_id, b.width_type, b.order_id order by a.sys_number_prefix_num DESC";
					}
					else
					{
						 $sql="SELECT a.id, a.entry_form, a.recv_number,a.receive_basis, a.recv_number as sys_number, a.recv_number_prefix_num, a.challan_no, a.receive_date, null as bookingno, $year_cond as year, b.batch_id, b.prod_id, b.body_part_id,b.process_id, b.fabric_description_id, b.color_id, b.dia_width_type, sum(b.receive_qnty) as rec_qnty,sum(b.grey_used_qty) as grey_used_qty, sum(b.no_of_roll) as carton_roll, 0 as po_breakdown_id, null as booking_no_id,d.booking_no 
							FROM inv_receive_master a, pro_finish_fabric_rcv_dtls b, pro_batch_create_mst d
							WHERE a.id=b.mst_id and d.id=b.batch_id and a.entry_form in (7,37,66,68) and b.receive_qnty>0 AND a.knitting_source=1 AND a.company_id=$data[3] AND a.location_id=$data[1] AND a.knitting_company=$data[0] and a.receive_basis in(2,4,5,9,11) and d.batch_against in (3,5) and $booking_without_order=1 and a.item_category=2 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $date_cond $manual_challan_cond $sys_challan_cond $batchIds_cond
							group by a.id, a.entry_form,a.recv_number, a.receive_basis,  a.recv_number,a.recv_number_prefix_num, a.challan_no, a.receive_date, a.insert_date, b.batch_id, b.prod_id, b.body_part_id, b.fabric_description_id, b.color_id,b.process_id, b.dia_width_type,d.booking_no order by a.recv_number_prefix_num DESC";
					}
				}
			 	//echo $sql; //die;
				$sql_result =sql_select($sql);
				
				//print_r($sql_result);
				$batchidstr=""; $poid=""; $pordid="";
				foreach($sql_result as $row)
				{
					$batchidstr.=$row[csf('batch_id')].',';
					$poid.=$row[csf('po_breakdown_id')].',';
					$poidArr[$row[csf('po_breakdown_id')]]=$row[csf('po_breakdown_id')];
					$RollIdArr[$row[csf('roll_id')]]=$row[csf('roll_id')];
					$pordid.=$row[csf('prod_id')].',';
				}
				//print_r($RollIdArr);
			/*	$poid_cond=where_con_using_array(array_unique($RollIdArr),0,"d.roll_id");
				     $sql_grey="SELECT d.roll_id, b.prod_id, b.body_part_id, e.detarmination_id as fabric_description_id, sum(c.quantity) as rec_qty, sum(d.qnty) as grey_qty, sum(c.quantity) as order_qnty, sum(b.no_of_roll) as carton_roll,c.po_breakdown_id as po_id FROM inv_issue_master a, inv_grey_fabric_issue_dtls b, order_wise_pro_details c, pro_roll_details d,product_details_master e
					WHERE a.id=b.mst_id and b.id=c.dtls_id and d.dtls_id=b.id and d.dtls_id=c.dtls_id and b.trans_id=c.trans_id and c.po_breakdown_id=d.po_breakdown_id and b.prod_id=c.prod_id  and e.id=c.prod_id  and b.prod_id=e.id and c.trans_type=2 and c.entry_form in (61) and c.trans_id!=0 and a.entry_form in (61) AND a.knit_dye_source=1 AND a.company_id=$data[3] AND a.knit_dye_company=$data[0] and a.item_category=13 
					and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0  $poid_cond 
					group by d.roll_id, b.prod_id, b.body_part_id, e.detarmination_id,c.po_breakdown_id ";
					$sql_result_grey_issueroll =sql_select($sql_grey);
					$barcode_grey_issue_array=array();
					foreach($sql_result_grey_issueroll as $row)
					{
						 
						$grey_issue_array[$row[csf('roll_id')]][$row[csf('po_id')]][$row[csf('fabric_description_id')]][$row[csf('body_part_id')]]['grey_qty']+=$row[csf('grey_qty')];
						 
					}*/
					 //print_r($grey_issue_array);
					
				if($batch_no=="")// Batch Filter
				{
					$exbatchIdstr=array_unique(array_filter(explode(",",$batchidstr)));
					$batchIds=""; $tot_rows=0;
					foreach($exbatchIdstr as $btchid)
					{
						$tot_rows++;
						$batchIds.=$btchid.",";
					}
					$batchidstrCond="";
					$batchIds=chop($batchIds,',');
					if($db_type==2 && $tot_rows>1000)
					{
						$batchidstrCond=" and (";
						$batchIdsArr=array_chunk(explode(",",$batchIds),999);
						foreach($batchIdsArr as $ids)
						{
							$ids=implode(",",$ids);
							$batchidstrCond.=" a.id in($ids) or ";
						}
						$batchidstrCond=chop($batchidstrCond,'or ');
						$batchidstrCond.=")";
					}
					else $batchidstrCond=" and a.id in ($batchIds)"; 
					
					 $grey_sql="Select a.id, a.batch_no, a.extention_no,a.color_range_id, a.process_id, b.po_id, b.prod_id, b.body_part_id, b.batch_qnty as batch_qnty from pro_batch_create_mst a, pro_batch_create_dtls b where a.id=b.mst_id and a.entry_form!=36 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $batchidstrCond";
					$grey_sql_result =sql_select($grey_sql);
					foreach($grey_sql_result as $row)
					{
						$batch_array[$row[csf('id')]]['batch_no']=$row[csf('batch_no')];
						$batch_array[$row[csf('id')]]['extention_no']=$row[csf('extention_no')];
						$batch_array[$row[csf('id')]]['process_id']=$row[csf('process_id')];
						$batch_array[$row[csf('id')]]['color_range_id']=$row[csf('color_range_id')];
						//$batch_qty_arr[$row[csf('id')]][$row[csf('po_id')]][$row[csf('body_part_id')]]['batch_qnty']+=$row[csf('batch_qnty')];
						$batch_qty_arr[$row[csf('id')]][$row[csf('po_id')]][$row[csf('prod_id')]]['batch_qnty']+=$row[csf('batch_qnty')];
						$fin_processId_array[$row[csf('id')]][$row[csf('body_part_id')]]['process_id']=$row[csf('process_id')];
					}
					unset($grey_sql_result);
				}
				  $fin_fab_sql="Select a.id, a.batch_no, a.extention_no,a.color_range_id, b.process_id, b.prod_id, b.body_part_id, b.grey_used_qty as grey_used_qty from pro_batch_create_mst a, pro_finish_fabric_rcv_dtls b,inv_receive_master c where c.id=b.mst_id and c.entry_form in(7) and  a.id=b.batch_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $batchidstrCond";
					$fin_sql_result =sql_select($fin_fab_sql);
					foreach($fin_sql_result as $row)
					{
					 
						$fin_processId_array[$row[csf('id')]][$row[csf('body_part_id')]]['process_id']=$row[csf('process_id')];
					 
					}
					unset($fin_sql_result);
					//print_r($fin_processId_array);
					
				$po_array=array();
				if($ex_bill_for!=3)// Po Filter
				{
					$expoid=array_unique(array_filter(explode(",",$poid)));
					$poids=""; $tot_rows=0;
					foreach($expoid as $poidval)
					{
						$tot_rows++;
						$poids.=$poidval.",";
					}
					$poidCond="";
					$poIds=chop($poids,',');
					if($db_type==2 && $tot_rows>1000)
					{
						$poidCond=" and (";
						$poIdsArr=array_chunk(explode(",",$poIds),999);
						foreach($poIdsArr as $ids)
						{
							$ids=implode(",",$ids);
							$poidCond.=" b.id in($ids) or ";
						}
						$poidCond=chop($poidCond,'or ');
						$poidCond.=")";
					}
					else $poidCond=" and b.id in ($poIds)"; 
					
					$po_sql=sql_select( "select a.style_ref_no, a.job_no_prefix_num, a.buyer_name, b.id, b.po_number from  wo_po_details_master a, wo_po_break_down b where a.job_no=b.job_no_mst and a.status_active!=0 and a.is_deleted=0 and b.status_active!=0 and b.is_deleted=0 $poidCond");
					foreach($po_sql as $row)
					{
						$po_array[$row[csf('id')]]['job']=$row[csf('job_no_prefix_num')];
						$po_array[$row[csf('id')]]['style']=$row[csf('style_ref_no')];
						$po_array[$row[csf('id')]]['order']=$row[csf('po_number')];
						$po_array[$row[csf('id')]]['buyer']=$row[csf('buyer_name')];
					}
					unset($po_sql);
				}
				
				//Product filter
				$expordid=array_unique(array_filter(explode(",",$pordid)));
				$pordids=""; $tot_rows=0;
				foreach($expordid as $prodidval)
				{
					$tot_rows++;
					$pordids.=$prodidval.",";
				}
				$pordidCond="";
				$pordids=chop($pordids,',');
				if($db_type==2 && $tot_rows>1000)
				{
					$pordidCond=" and (";
					$prodIdsArr=array_chunk(explode(",",$pordids),999);
					foreach($prodIdsArr as $ids)
					{
						$ids=implode(",",$ids);
						$pordidCond.=" id in($ids) or ";
					}
					$pordidCond=chop($pordidCond,'or ');
					$pordidCond.=")";
				}
				else $pordidCond=" and id in ($pordids)"; 
				
			$sql="select id, item_category_id,product_name_details from product_details_master where status_active=1 and is_deleted=0 $pordidCond"; 
				$nameArray=sql_select($sql);
				foreach($nameArray as $row)
				{
					if($row[csf('item_category_id')]==5 || $row[csf('item_category_id')]==6 || $row[csf('item_category_id')]==7)
					{
						$product_data_arr[$row[csf('id')]]=$row[csf('item_category_id')];
					}
					$product_dtls_arr[$row[csf('id')]]=$row[csf('product_name_details')];
				}
				unset($nameArray);
				
				foreach($sql_result as $row) // for update row
				{
					if($row[csf('po_breakdown_id')]=="") $row[csf('po_breakdown_id')]=0;
					$all_value=$row[csf('recv_number_prefix_num')].'_'.$row[csf('po_breakdown_id')].'_'.$row[csf('prod_id')].'_'.$row[csf('body_part_id')].'_'.$row[csf('fabric_description_id')].'_'.$row[csf('dia_width_type')].'_'.$row[csf('batch_id')];
					//$color_range_id=$batch_array[$row[csf('batch_id')]]['color_range_id'];
					$batchId_arr[$row[csf('batch_id')]]=$row[csf('batch_id')];
					
					$color_range_id=$batch_array[$row[csf('batch_id')]]['color_range_id'];
					$BatchTotal 				= $batchTotal_arr[$row[csf('batch_id')]]['batch_ratio_total'];
					$chemicalsTotal 			= $batch_item_ratio_total_arr[$row[csf('batch_id')]][5]['ratio_total'];
					$dyesTotal 					= $batch_item_ratio_total_arr[$row[csf('batch_id')]][6]['ratio_total'];
					$auxiChemicalsTotal			= $batch_item_ratio_total_arr[$row[csf('batch_id')]][7]['ratio_total'];
					
					$percentageOfColor = 0;
					if($dyesTotal>0 && $BatchTotal>0 )
					{
					//	echo $row[csf('batch_id')].'=='.$dyesTotal.'TTTTTTTT'.$BatchTotal;
					 $percentageOfColor=($dyesTotal/$BatchTotal)*100;
					}
					
					if(in_array($all_value,$str_arr))
					{
						$booking_no=0; $independent=''; $bill_for_id=0; $bill_for_sb=0;
						if ($row[csf('entry_form')]==7)
						{
							if($ex_bill_for==1) { if($row[csf('receive_basis')]==4) $independent=4; } //else $independent='';
							if ($row[csf('receive_basis')]==5) $booking_no=$row[csf('booking_no')]; else $booking_no=0;
							if($ex_bill_for==1) $bill_for_id="Fb"; else if($ex_bill_for==2) $bill_for_id="SM"; else if($ex_bill_for==3) $bill_for_id="SMN";
						}
						else if ($row[csf('entry_form')]==37)
						{
							if ($row[csf('receive_basis')]==9) 
							{
								if($recive_basis_arr[$row[csf('bookingno')]]==4) $independent=4; //else $row[csf('receive_basis')]=5;
							}
							
							if($ex_bill_for==1) { if($row[csf('receive_basis')]==4) $independent=4; }// else $independent='';
							if($row[csf('receive_basis')]==2 || $row[csf('receive_basis')]==11) 
								$booking_no=$row[csf('booking_no')];
							else if($row[csf('receive_basis')]==9 && $recive_basis_arr[$row[csf('bookingno')]]==5) 
								$booking_no=$row[csf('booking_no')];
							else 
								$booking_no=0;
							if($ex_bill_for==1) { $bill_for_id="Fb"; $bill_for_sb="SB"; } else if($ex_bill_for==2 && $row[csf('receive_basis')]!=4) $bill_for_id="SM";  else if($ex_bill_for==3) $bill_for_id="SMN";
						}
						else if ($row[csf('entry_form')]==66)
						{
							$rec_basis=0; $bookinNo=""; $bookingId=0;// booking id is barcode, and booking no is delv id
							$rec_basis=0;
							$bookinNo=$row[csf('booking_no')];
							$bookingId=$row[csf('booking_id')];
							
							if($ex_bill_for==1) { if($rec_basis==0) $independent=4; } //else $independent='';
							if ($rec_basis==0) $booking_no=$bookinNo; else $booking_no=0;
							if($ex_bill_for==1) $bill_for_id="Fb"; else if($ex_bill_for==2 && $rec_basis!=0) $bill_for_id="SM"; else if($ex_bill_for==3) $bill_for_id="SMN";
						}
						else if ($row[csf('entry_form')]==68)
						{
							$rec_basis=0; $bookinNo=""; $bookingId=0;// booking id is barcode, and booking no is delv id
							$rec_basis=0;
							$bookinNo=$row[csf('booking_no')];
							$bookingId=$row[csf('booking_id')];
							
							if ($ex_bill_for==1) { if($rec_basis==0) $independent=4; } //else $independent='';
							if ($rec_basis==0) $booking_no=$bookinNo; else $booking_no=0;
							if ($ex_bill_for==1) $bill_for_id="Fb"; else if($ex_bill_for==2 && $rec_basis!=0) $bill_for_id="SM"; else if($ex_bill_for==3) $bill_for_id="SMN";
						}
						if ($row[csf('entry_form')]==54 || $row[csf('entry_form')]==67)
						{
							if($ex_bill_for==1) { if($row[csf('receive_basis')]==0) $independent=4; } //else $independent='';
							if ($row[csf('receive_basis')]==5) $booking_no=$row[csf('booking_no')]; else $booking_no=0;
							if($ex_bill_for==1) $bill_for_id="Fb"; else if($ex_bill_for==2) $bill_for_id="SM"; else if($ex_bill_for==3) $bill_for_id="SMN";
						}
						
						$ex_booking="";
						if($booking_no!='') $ex_booking=explode('-',$booking_no); else $ex_booking="_";
						$process_id=array_unique(explode(',',$batch_array[$row[csf('batch_id')]]['process_id']));
						$process_name='';
						foreach ($process_id as $val)
						{
							if($process_name=='') $process_name=$conversion_cost_head_array[$val]; else $process_name.=",".$conversion_cost_head_array[$val];
						}
						$on_bill_qty=0; 
						
						if($inhouse_bill_from==2)
						{
							
							//$grey_issue=$row[csf('production_qty')];
							if($row[csf('grey_used_qty')]>0) $row[csf('grey_used_qty')]=$row[csf('grey_used_qty')];else $row[csf('grey_used_qty')]=$row[csf('production_qty')];
							if($variable_check==1) $on_bill_qty=$row[csf('grey_used_qty')]; else $on_bill_qty=$row[csf('rec_qnty')];
						}
						else
						{
							if ($variable_check==1) $on_bill_qty=$row[csf('grey_used_qty')];//$batch_qty_arr[$row[csf('batch_id')]][$row[csf('po_breakdown_id')]][$row[csf('prod_id')]]['batch_qnty'];
							else $on_bill_qty=$row[csf('rec_qnty')];
						}
						
						$grey_qty=0;
						if($inhouse_bill_from==2) $grey_qty=$row[csf('grey_used_qty')]; else $grey_qty=$row[csf('grey_used_qty')];//$batch_qty_arr[$row[csf('batch_id')]][$row[csf('po_breakdown_id')]][$row[csf('prod_id')]]['batch_qnty'];
						 $des_composition=$composition_arr[$row[csf('fabric_description_id')]];
						
						
						$str_val=$row[csf('id')].'_'.change_date_format($row[csf('receive_date')]).'_'.$row[csf('recv_number_prefix_num')].'_'.$row[csf('po_breakdown_id')].'_'.$po_array[$row[csf('po_breakdown_id')]]['order'].'_'.$po_array[$row[csf('po_breakdown_id')]]['style'].'_'.$buyer_arr[$po_array[$row[csf('po_breakdown_id')]]['buyer']].'_'.$po_array[$row[csf('po_breakdown_id')]]['job'].'_'.$row[csf('carton_roll')].'_'.$row[csf('body_part_id')].'_'.$body_part[$row[csf("body_part_id")]].'_'.$row[csf('fabric_description_id')].'_'.$row[csf('prod_id')].'_'.$des_composition.'_'.$row[csf('batch_id')].'_'.$row[csf('color_id')].'_'.$color_name[$row[csf("color_id")]].'_'.$batch_array[$row[csf('batch_id')]]['process_id'].'_'.$process_name.'_'.$row[csf('dia_width_type')].'_'.$fabric_typee[$row[csf('dia_width_type')]].'_'.number_format($on_bill_qty, 2, '.', '').'_'.$color_range_id.'_'.number_format($percentageOfColor,2, '.', '').'________';
						
						if($independent==4)
						{
							if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
							?>
                            <tr id="tr_<? echo $all_value; ?>" bgcolor="yellow" bgcolor="<? echo $bgcolor; ?>" style="text-decoration:none; cursor:pointer" onClick="js_set_value('<? echo $all_value."***".'1'; ?>');" >
                            	<td width="30" align="center" bgcolor="#CCFFCC"><input type="checkbox" name="checkid<? echo $i; ?>" id="checkid<? echo $i; ?>" onClick="fnc_check(<? echo $i; ?>)" value="1" checked ></td>
								<td width="30" align="center"><? echo $i; ?></td>
								<td width="100" title="<? echo $row[csf('recv_number')]; ?>" align="center"><? echo $row[csf('sys_number')]; ?></td>
								<td width="50" align="center" style="word-break:break-all"><? echo $row[csf('challan_no')]; ?></td>
								<td width="60"><? echo change_date_format($row[csf('receive_date')]); ?></td>
								<td width="70" style="word-break:break-all"><? echo $color_name[$row[csf('color_id')]]; ?></td>
								<td width="50" align="center"><? echo $po_array[$row[csf('po_breakdown_id')]]['job']; ?></td>
								<td width="90" style="word-break:break-all"><? echo $po_array[$row[csf('po_breakdown_id')]]['style']; ?></td>
								<td width="80" style="word-break:break-all"><? echo $po_array[$row[csf('po_breakdown_id')]]['order']; ?></td>
								<td width="70" style="word-break:break-all"><? echo $batch_array[$row[csf('batch_id')]]['batch_no']; ?></td>
								<td width="30"><? echo $batch_array[$row[csf('batch_id')]]['extention_no']; ?></td>
								<td width="100" style="word-break:break-all"><? echo $process_name; ?>&nbsp;</td>
								<td width="120" style="word-break:break-all"><? echo $des_composition; ?></td>
								<td width="70" style="word-break:break-all"><? echo $fabric_typee[$row[csf('dia_width_type')]]; ?></td>
								<td width="60" align="right"><? echo number_format($grey_qty,2,'.',''); ?></td>
								<td align="right"><? echo number_format($row[csf('rec_qnty')],2,'.',''); ?>
                                <input type="hidden" id="strid<? echo $i; ?>" value="<? echo $str_val; ?>">
								<input type="hidden" id="currid<? echo $all_value; ?>" style="width:50px" value="<? echo '1'; ?>"></td>
							</tr>
							<?php
							$i++;
						}
						else
						{
							if( strtolower($ex_booking[1])==strtolower($bill_for_id) || strtolower($ex_booking[1])==strtolower($bill_for_sb)) 
							{
								if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";//production_qty
								?>
								<tr id="tr_<? echo $all_value; ?>"  bgcolor="yellow" bgcolor="<? echo $bgcolor; ?>" style="text-decoration:none; cursor:pointer" onClick="js_set_value('<? echo $all_value."***".'1'; ?>');" >
                                    <td width="30" align="center" bgcolor="#CCFFCC"><input type="checkbox" name="checkid<? echo $i; ?>" id="checkid<? echo $i; ?>" onClick="fnc_check(<? echo $i; ?>)" value="1" checked ></td>
                                    <td width="30" align="center"><? echo $i; ?></td>
                                    <td width="100" title="<? echo $row[csf('recv_number')]; ?>" align="center"><? echo $row[csf('sys_number')]; ?></td>
                                    <td width="50" align="center" style="word-break:break-all"><? echo $row[csf('challan_no')]; ?></td>
                                    <td width="60"><? echo change_date_format($row[csf('receive_date')]); ?></td>
                                    <td width="70" style="word-break:break-all"><? echo $color_name[$row[csf('color_id')]]; ?></td>
                                    <td width="50" align="center"><? echo $po_array[$row[csf('po_breakdown_id')]]['job']; ?></td>
                                    <td width="90" style="word-break:break-all"><? echo $po_array[$row[csf('po_breakdown_id')]]['style']; ?></td>
                                    <td width="80" style="word-break:break-all"><? echo $po_array[$row[csf('po_breakdown_id')]]['order']; ?></td>
                                    <td width="70" style="word-break:break-all"><? echo $batch_array[$row[csf('batch_id')]]['batch_no']; ?></td>
                                    <td width="30"><? echo $batch_array[$row[csf('batch_id')]]['extention_no']; ?></td>
                                    <td width="100" style="word-break:break-all"><? echo $process_name; ?>&nbsp;</td>
                                    <td width="120" style="word-break:break-all"><? echo $des_composition; ?></td>
                                    <td width="70" style="word-break:break-all"><? echo $fabric_typee[$row[csf('dia_width_type')]]; ?></td>
                                    <td width="60" align="right"><? echo number_format($grey_qty,2,'.',''); ?></td>
                                    <td align="right"><? echo number_format($row[csf('rec_qnty')],2,'.',''); ?>
                                    <input type="hidden" id="strid<? echo $i; ?>" value="<? echo $str_val; ?>">
                                    <input type="hidden" id="currid<? echo $all_value; ?>" style="width:50px" value="<? echo '1'; ?>"></td>
                                </tr>
								<?php
								$i++;
							}
						}
					}
				}
				
	
				$batchIds=implode(",",$batchId_arr);
				$batchIds_id=implode(",",array_unique(explode(",",$batchIds)));
			 	$sql_color_precentage="select a.id, a.batch_id, b.id as dtls_id, b.prod_id, b.sub_process_id, b.dose_base as item_cat, b.ratio from pro_recipe_entry_mst a, pro_recipe_entry_dtls b where a.id=b.mst_id  and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and b.ratio is not null and b.seq_no is not null and a.batch_id in($batchIds_id)";
				$result_color_precentage=sql_select($sql_color_precentage);
				$batch_item_ratio_total_arr=array();
				$batchTotal_arr= array();
				foreach($result_color_precentage as $row){
					$batch_item_ratio_total_arr[$row[csf('batch_id')]][$product_data_arr[$row[csf('prod_id')]]]['ratio_total'] += $row[csf('ratio')];
					$batch_item_ratio_total_arr[$row[csf('batch_id')]][$product_data_arr[$row[csf('prod_id')]]]['ratio_count'] += 1;
					$batchTotal_arr[$row[csf('batch_id')]]['batch_ratio_total']  += $row[csf('ratio')];
				}
				unset($result_color_precentage);
			//--------------------------------------------------------------------------------------------------
					$conversion_sql_data=sql_select("SELECT a.job_no,c.color_number_id,d.color_type_id,d.item_number_id,f.contrast_color_id     AS fabric_color,g.charge_unit,b.id po_id,d.lib_yarn_count_deter_id deter_id, g.cons_process
					FROM wo_po_details_master            a,
						 wo_po_break_down                b,
						 wo_po_color_size_breakdown      c,
						 wo_pre_cost_fabric_cost_dtls    d,
						 wo_pre_cos_fab_co_avg_con_dtls  e
						 LEFT JOIN wo_pre_cos_fab_co_color_dtls f
							 ON     e.pre_cost_fabric_cost_dtls_id =
									f.pre_cost_fabric_cost_dtls_id
								AND e.color_number_id = f.gmts_color_id,
								wo_pre_cost_fab_conv_cost_dtls g 
				   WHERE     1 = 1
						 
						 and g.fabric_description=d.id AND a.job_no = b.job_no_mst  AND a.job_no = c.job_no_mst AND a.job_no = d.job_no AND a.job_no = e.job_no
						 AND b.id = c.po_break_down_id AND d.id = e.pre_cost_fabric_cost_dtls_id AND c.po_break_down_id = e.po_break_down_id AND c.item_number_id = d.item_number_id
						 AND c.color_number_id = e.color_number_id  AND c.size_number_id = e.gmts_sizes AND a.is_deleted = 0 AND a.status_active = 1 AND b.is_deleted = 0
						 AND b.status_active = 1  AND c.is_deleted = 0 AND c.status_active = 1 AND d.status_active = 1 AND d.is_deleted = 0
					  group by a.job_no,c.color_number_id,d.color_type_id,d.item_number_id,f.contrast_color_id ,g.charge_unit,b.id,d.lib_yarn_count_deter_id, g.cons_process");

					$conversion_arr=array();
					foreach($conversion_sql_data as $row){
						$conversion_arr[$row[csf('po_id')]][$row[csf('deter_id')]][$row[csf('cons_process')]][$row[csf('color_number_id')]]['charge_unit']=$row[csf('charge_unit')];

					}

			//--------------------------------------------------------------------------------------------------


				foreach($sql_result as $row) // for new row
				{
					$booking_no=0; $independent=''; $bill_for_id=0; $bill_for_sb=0;
					//echo $row[csf('entry_form')].'T';
					if ($row[csf('entry_form')]==7)
					{
						if($ex_bill_for==1) { if($row[csf('receive_basis')]==4) $independent=4; } //else $independent='';
						if ($row[csf('receive_basis')]==5) $booking_no=$row[csf('booking_no')]; else $booking_no=0;
						if($ex_bill_for==1) $bill_for_id="Fb"; else if($ex_bill_for==2) $bill_for_id="SM"; else if($ex_bill_for==3) $bill_for_id="SMN";
					}
					else if ($row[csf('entry_form')]==37)
					{
						if ($row[csf('receive_basis')]==9) 
						{
							if($recive_basis_arr[$row[csf('bookingno')]]==4) $independent=4;
						}
						
						if($ex_bill_for==1) { if($row[csf('receive_basis')]==4) $independent=4; }// else $independent='';
						if($row[csf('receive_basis')]==2 || $row[csf('receive_basis')]==11) 
							$booking_no=$row[csf('booking_no')];
						else if($row[csf('receive_basis')]==9 && $recive_basis_arr[$row[csf('bookingno')]]==5) 
							$booking_no=$row[csf('booking_no')];
						else 
							$booking_no=0;
						if($ex_bill_for==1) { $bill_for_id="Fb"; $bill_for_sb="SB"; } else if($ex_bill_for==2 && $row[csf('receive_basis')]!=4) $bill_for_id="SM"; else if($ex_bill_for==3) $bill_for_id="SMN";
					}
					else if ($row[csf('entry_form')]==66)
					{
						$rec_basis=0; $bookinNo=""; $bookingId=0;// booking id is barcode, and booking no is delv id
						$rec_basis=0;
						$bookinNo=$row[csf('booking_no')];
						$bookingId=$row[csf('booking_id')];
						
						if($ex_bill_for==1) { if($rec_basis==0) $independent=4; } //else $independent='';
						if ($rec_basis==0) $booking_no=$bookinNo; else $booking_no=0;
						if($ex_bill_for==1) $bill_for_id="Fb"; else if($ex_bill_for==2 && $rec_basis!=0) $bill_for_id="SM"; else if($ex_bill_for==3) $bill_for_id="SMN";
					}
					else if ($row[csf('entry_form')]==68)
					{
						$rec_basis=0; $bookinNo=""; $bookingId=0;// booking id is barcode, and booking no is delv id
						$rec_basis=0;
						$bookinNo=$row[csf('booking_no')];
						$bookingId=$row[csf('booking_id')];
						
						
						if ($ex_bill_for==1) { if($rec_basis==0) $independent=4; } //else $independent='';
						if ($rec_basis==0) $booking_no=$bookinNo; else $booking_no=0;
						if ($ex_bill_for==1) $bill_for_id="Fb"; else if($ex_bill_for==2 && $rec_basis!=0) $bill_for_id="SM"; else if($ex_bill_for==3) $bill_for_id="SMN";
					}
					if ($row[csf('entry_form')]==54 || $row[csf('entry_form')]==67)
					{
						if($ex_bill_for==1) { if($row[csf('receive_basis')]==0) $independent=4; } //else $independent='';
						if ($row[csf('receive_basis')]==5 || $row[csf('receive_basis')]==0) $booking_no=$row[csf('booking_no')]; else $booking_no=0;
						if($ex_bill_for==1) $bill_for_id="Fb"; else if($ex_bill_for==2) $bill_for_id="SM"; else if($ex_bill_for==3) $bill_for_id="SMN";
						//echo $row[csf('entry_form')].'-'.$row[csf('receive_basis')].'-'.$booking_no;
						//$booking_no=$row[csf('booking_no')];
						$bookingId=$row[csf('booking_id')];
						//echo $booking_no.'='.$bookingId.'<br>';
					}
					$ex_booking=""; $bill_qty=0;
					if($booking_no!='') $ex_booking=explode('-',$booking_no); else $ex_booking="_";
					//echo strtolower($ex_booking[1]).'=='.strtolower($bill_for_id);
					
					//if($ex_booking[1]!='Fb') echo $ex_booking[1];
					$bill_qty=$bill_qty_array[$row[csf('recv_number_prefix_num')]][$row[csf('po_breakdown_id')]][$row[csf('prod_id')]][$row[csf('body_part_id')]][$row[csf('fabric_description_id')]][$row[csf('dia_width_type')]][$row[csf('batch_id')]]['qty'];
					//$bill_qty_array[$row[csf('challan_no')]][$row[csf('order_id')]][$row[csf('item_id')]][$row[csf('body_part_id')]][$row[csf('febric_description_id')]][$row[csf('dia_width_type')]][$row[csf('batch_id')]]['qty']
					
					//$avilable_qty=$row[csf('rec_qnty')]-$bill_qty;
					$avilable_roll=$roll_no_arr[$row[csf('dtls_id')]]-$roll_qty;
					$all_value=$row[csf('recv_number_prefix_num')].'_'.$row[csf('po_breakdown_id')].'_'.$row[csf('prod_id')].'_'.$row[csf('body_part_id')].'_'.$row[csf('fabric_description_id')].'_'.$row[csf('dia_width_type')].'_'.$row[csf('batch_id')];
					//echo $row[csf('process_id')].', ';
					
					//$process_id=array_unique(explode(',',$batch_array[$row[csf('batch_id')]]['process_id']));
					$process_id=array_unique(explode(',',$fin_processId_array[$row[csf('batch_id')]][$row[csf('body_part_id')]]['process_id']));
					//issue Id-17310
					$process_name='';$unitCharge=0;
					foreach ($process_id as $val)
					{
						if($process_name=='') $process_name=$conversion_cost_head_array[$val]; else $process_name.=",".$conversion_cost_head_array[$val];
						$unitCharge+=$conversion_arr[$row[csf('po_breakdown_id')]][$row[csf('fabric_description_id')]][$val][$row[csf('color_id')]]['charge_unit'];
					}
					$roll_idArr=array_unique(explode(",",$row[csf('roll_id')]));
					$on_bill_qty=0;
					if($inhouse_bill_from==2)
					{
						
						$grey_issue=$row[csf('production_qty')];
						foreach($roll_idArr as $rid)
						{
						//$grey_issue+=$grey_issue_array[$rid][$row[csf('po_breakdown_id')]][$row[csf('fabric_description_id')]][$row[csf('body_part_id')]]['grey_qty'];
						}
						//echo $row[csf('roll_id')].'='.$row[csf('fabric_description_id')].'='.$row[csf('body_part_id')].'='.$grey_issue."<br>";
						$grey_qty=$row[csf('grey_used_qty')];
						if($grey_qty) $grey_qty=$grey_qty;else $grey_qty=$grey_issue;
						
						if ($variable_check==1) $on_bill_qty=$grey_qty; else $on_bill_qty=$row[csf('rec_qnty')];
					}
					else
					{ //grey_used_qty
						//$on_bill_qty=$batch_qty_arr[$row[csf('batch_id')]][$row[csf('po_breakdown_id')]][$row[csf('prod_id')]]['batch_qnty'];
						if ($variable_check==1) $on_bill_qty=$row[csf('grey_used_qty')];
						else $on_bill_qty=$row[csf('rec_qnty')];
						//echo $variable_check.'='.$batch_qty_arr[$row[csf('batch_id')]][$row[csf('po_breakdown_id')]][$row[csf('prod_id')]]['batch_qnty'].'<br>';
					}
					$color_range_id=$batch_array[$row[csf('batch_id')]]['color_range_id'];
					$BatchTotal 				= $batchTotal_arr[$row[csf('batch_id')]]['batch_ratio_total'];
					$chemicalsTotal 			= $batch_item_ratio_total_arr[$row[csf('batch_id')]][5]['ratio_total'];
					$dyesTotal 					= $batch_item_ratio_total_arr[$row[csf('batch_id')]][6]['ratio_total'];
					$auxiChemicalsTotal			= $batch_item_ratio_total_arr[$row[csf('batch_id')]][7]['ratio_total'];
					
					$percentageOfColor = 0;
					if($dyesTotal>0 && $BatchTotal>0 )
					{
					//	echo $row[csf('batch_id')].'=='.$dyesTotal.'TTTTTTTT'.$BatchTotal;
					 $percentageOfColor=($dyesTotal/$BatchTotal)*100;
					}
					 
					$grey_qty=0;
					if($inhouse_bill_from==2) 
					{ 
						$grey_issue=$row[csf('production_qty')];
						foreach($roll_idArr as $rid) 
						{
						//$grey_issue+=$grey_issue_array[$rid][$row[csf('po_breakdown_id')]][$row[csf('fabric_description_id')]][$row[csf('body_part_id')]]['grey_qty'];
						}
						//$grey_issue=$grey_issue_array[$row[csf('po_breakdown_id')]][$row[csf('fabric_description_id')]][$row[csf('body_part_id')]]['grey_qty'];
					//$grey_issue=$grey_issue_array[$row[csf('roll_id')]][$row[csf('po_breakdown_id')]][$row[csf('fabric_description_id')]][$row[csf('body_part_id')]]['grey_qty'];
						//echo $row[csf('po_breakdown_id')].'='.$row[csf('fabric_description_id')].'='.$row[csf('body_part_id')].'='.$grey_issue."<br>";
						$grey_qty=$row[csf('grey_used_qty')];
						if($grey_qty) $grey_qty=$grey_qty;else $grey_qty=$grey_issue;
					}
					 else {
						 $grey_qty=$row[csf('grey_used_qty')];//$batch_qty_arr[$row[csf('batch_id')]][$row[csf('po_breakdown_id')]][$row[csf('prod_id')]]['batch_qnty'];
					 }
					$avilable_qty=$grey_qty-$bill_qty;
					//echo $grey_qty.'='.$bill_qty.'=';
					//echo $row[csf('roll_id')].'='.$row[csf('sys_number')].'='.$grey_issue.'='.$avilable_qty.'<br>';
					 
					 $des_composition=$composition_arr[$row[csf('fabric_description_id')]];
					// echo $des_composition.'D';
				
					$str_val=$row[csf('id')].'_'.change_date_format($row[csf('receive_date')]).'_'.$row[csf('recv_number_prefix_num')].'_'.$row[csf('po_breakdown_id')].'_'.$po_array[$row[csf('po_breakdown_id')]]['order'].'_'.$po_array[$row[csf('po_breakdown_id')]]['style'].'_'.$buyer_arr[$po_array[$row[csf('po_breakdown_id')]]['buyer']].'_'.$po_array[$row[csf('po_breakdown_id')]]['job'].'_'.$row[csf('carton_roll')].'_'.$row[csf('body_part_id')].'_'.$body_part[$row[csf("body_part_id")]].'_'.$row[csf('fabric_description_id')].'_'.$row[csf('prod_id')].'_'.$des_composition.'_'.$row[csf('batch_id')].'_'.$row[csf('color_id')].'_'.$color_name[$row[csf("color_id")]].'_'.$fin_processId_array[$row[csf('batch_id')]][$row[csf('body_part_id')]]['process_id'].'_'.$process_name.'_'.$row[csf('dia_width_type')].'_'.$fabric_typee[$row[csf('dia_width_type')]].'_'.number_format($on_bill_qty, 2, '.', '').'_'.$color_range_id.'_'.number_format($percentageOfColor,2, '.', '').'_'.number_format($unitCharge,2, '.', '').'________';
					if($independent==4)
					{
						if($avilable_qty>0)
						{
							if($re_batch_chk_array[$row[csf('batch_id')]]=="")
							{
							if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
							?>
                            <tr id="tr_<? echo $all_value; ?>" bgcolor="<? echo $bgcolor; ?>" style="text-decoration:none; cursor:pointer" onClick="js_set_value('<? echo $all_value."***".'1'; ?>');" >
                            	<td width="30" align="center" bgcolor="#CCFFCC"><input type="checkbox" name="checkid<? echo $i; ?>" id="checkid<? echo $i; ?>" onClick="fnc_check(<? echo $i; ?>)" value="2" ></td>
								<td width="30" align="center"><? echo $i; ?></td>
								<td width="100" align="center" title="<? echo $grey_qty.'='.$bill_qty; ?>"><? echo $row[csf('sys_number')]; ?></td>
								<td width="50" align="center" style="word-break:break-all"><? echo $row[csf('challan_no')]; ?></td>
								<td width="60"><? echo change_date_format($row[csf('receive_date')]); ?></td>
								<td width="70" style="word-break:break-all"><? echo $color_name[$row[csf('color_id')]]; ?></td>
								<td width="50" align="center"><? echo $po_array[$row[csf('po_breakdown_id')]]['job']; ?></td>
								<td width="90" style="word-break:break-all"><? echo $po_array[$row[csf('po_breakdown_id')]]['style']; ?></td>
								<td width="80" style="word-break:break-all"><? echo $po_array[$row[csf('po_breakdown_id')]]['order']; ?></td>
								<td width="70" style="word-break:break-all"><? echo $batch_array[$row[csf('batch_id')]]['batch_no']; ?></td>
								<td width="30"><? echo $batch_array[$row[csf('batch_id')]]['extention_no']; ?></td>
								<td width="100" style="word-break:break-all"><? echo $process_name; ?>&nbsp;</td>
								<td width="120" style="word-break:break-all"><? echo $des_composition; ?></td>
								<td width="70" style="word-break:break-all"><? echo $fabric_typee[$row[csf('dia_width_type')]]; ?></td>
								<td width="60" align="right"><? echo number_format($grey_qty,2,'.',''); ?></td>
								<td align="right"><? echo number_format($row[csf('rec_qnty')],2,'.',''); ?>
                                <input type="hidden" id="strid<? echo $i; ?>" value="<? echo $str_val; ?>">
								<input type="hidden" id="currid<? echo $all_value; ?>" style="width:50px" value="<? echo '1'; ?>"></td>
							</tr>
							<?php
							$i++;
							}
						}
					}
					else
					{
						//echo strtolower($ex_booking[1]).'='.strtolower($bill_for_id);
						if( strtolower($ex_booking[1])==strtolower($bill_for_id) || strtolower($ex_booking[1])==strtolower($bill_for_sb)) 
						{
							if($avilable_qty>0)
							{
								if($re_batch_chk_array[$row[csf('batch_id')]]=="")
								{
								if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
								?>
								<tr id="tr_<? echo $all_value; ?>" bgcolor="<? echo $bgcolor; ?>" style="text-decoration:none; cursor:pointer" onClick="js_set_value('<? echo $all_value."***".'1'; ?>');" >
                                    <td width="30" align="center"><input type="checkbox" name="checkid<? echo $i; ?>" id="checkid<? echo $i; ?>" onClick="fnc_check(<? echo $i; ?>)" value="2" ></td>
                                    <td width="30" align="center"><? echo $i; ?></td>
                                    <td width="100" align="center" title="<? echo $row[csf('recv_number')]; ?>"><? echo $row[csf('sys_number')]; ?></td>
                                    <td width="50" align="center" style="word-break:break-all"><? echo $row[csf('challan_no')]; ?></td>
                                    <td width="60"><? echo change_date_format($row[csf('receive_date')]); ?></td>
                                    <td width="70" style="word-break:break-all"><? echo $color_name[$row[csf('color_id')]]; ?></td>
                                    <td width="50" align="center"><? echo $po_array[$row[csf('po_breakdown_id')]]['job']; ?></td>
                                    <td width="90" style="word-break:break-all"><? echo $po_array[$row[csf('po_breakdown_id')]]['style']; ?></td>
                                    <td width="80" style="word-break:break-all"><? echo $po_array[$row[csf('po_breakdown_id')]]['order']; ?></td>
                                    <td width="70" style="word-break:break-all"><? echo $batch_array[$row[csf('batch_id')]]['batch_no']; ?></td>
                                    <td width="30"><? echo $batch_array[$row[csf('batch_id')]]['extention_no']; ?></td>
                                    <td width="100" style="word-break:break-all"><? echo $process_name; ?>&nbsp;</td>
                                    <td width="120" style="word-break:break-all"><? echo  $des_composition; ?></td>
                                    <td width="70" style="word-break:break-all"><? echo $fabric_typee[$row[csf('dia_width_type')]]; ?></td>
                                    <td width="60" align="right"><? echo number_format($grey_qty,2,'.',''); ?></td>
                                    <td align="right"><? echo number_format($row[csf('rec_qnty')],2,'.',''); ?>
                                    
                                    <input type="hidden" id="strid<? echo $i; ?>" value="<? echo $str_val; ?>">
                                    <input type="hidden" id="currid<? echo $all_value; ?>" style="width:50px" value="<? echo '1'; ?>"></td>
                                </tr>
								<?php
								$i++;
								}
							}
						}
					}
				}
				?>
		   </table>
		</div>
		   <table width="1020px">
				<tr>
                	<td bgcolor="#7FDF00" align="center"><input type="checkbox" name="checkall" id="checkall" class="formbutton" value="2" onClick="check_all_data();"/> Check all</td>
					<td bgcolor="#FF80FF" align="center"><input type="button" id="show_button" align="middle" class="formbutton" style="width:100px" value="Close" onClick="window_close(0);" /></td>
				</tr>
		   </table>
           </div>
	</body>           
	<script src="../includes/functions_bottom.js" type="text/javascript"></script>
	</html>
    <?
	}
	exit();
}

if ($action=="load_dtls_data") 
{
	$ex_data=explode("!^!",$data);
	$upid=$ex_data[0];
	$source=$ex_data[1];
	$buyer_arr=return_library_array( "select id, short_name from lib_buyer",'id','short_name');
	//$roll_no_arr=return_library_array( "select id, no_of_roll from  pro_grey_prod_entry_dtls",'id','no_of_roll');
    $product_dtls_arr=return_library_array( "select id,product_name_details from  product_details_master",'id','product_name_details');
	$color_name=return_library_array( "select id, color_name from lib_color where status_active=1 and is_deleted=0",'id','color_name');
	if($source==1)
	{
		/*$determ_arr = return_library_array( "select mst_id, copmposition_id from lib_yarn_count_determina_dtls",'mst_id','copmposition_id');
		$composition_arr=array();
		$sql_deter="select a.id, a.construction, b.copmposition_id, b.percent from lib_yarn_count_determina_mst a, lib_yarn_count_determina_dtls b where a.id=b.mst_id";
		$data_array=sql_select($sql_deter);
		if(count($data_array)>0)
		{
			foreach( $data_array as $row )
			{
				if(array_key_exists($row[csf('id')],$composition_arr))
				{
					$composition_arr[$row[csf('id')]]=$composition_arr[$row[csf('id')]]." ".$composition[$row[csf('copmposition_id')]]." ".$row[csf('percent')]."%";
				}
				else
				{
					$composition_arr[$row[csf('id')]]=$row[csf('construction')].", ".$composition[$row[csf('copmposition_id')]]." ".$row[csf('percent')]."%";
				}
			}
		}*/
		//var_dump($order_array);
		 $composition_arr=array(); $constructtion_arr=array();
 	$sql_deter="select a.id, a.construction, b.copmposition_id, b.percent from lib_yarn_count_determina_mst a, lib_yarn_count_determina_dtls b where a.id=b.mst_id";
	$data_array=sql_select($sql_deter);
	foreach( $data_array as $row )
	{
		if(array_key_exists($row[csf('id')],$composition_arr))
		{
			$composition_arr[$row[csf('id')]]=$composition_arr[$row[csf('id')]]." ".$composition[$row[csf('copmposition_id')]]." ".$row[csf('percent')]."%";
		}
		else
		{
			$composition_arr[$row[csf('id')]]=$row[csf('construction')].", ".$composition[$row[csf('copmposition_id')]]." ".$row[csf('percent')]."%";
		}
	}
		
		$sql="select id as upd_id, delivery_id, delivery_date, challan_no, item_id, body_part_id, color_id, batch_id, febric_description_id, dia_width_type, add_process, add_process_name, packing_qnty as carton_roll, delivery_qty, lib_rate_id, rate_data_string, rate, add_rate_id, add_rate, amount, remarks, order_id, color_range_id, shade_percentage, currency_id from subcon_inbound_bill_dtls where mst_id=$upid  and status_active=1 and is_deleted=0 order by id ASC";
		//echo $sql;
		$sql_result_arr =sql_select($sql);
		$poid=""; $batchid="";
		foreach ($sql_result_arr as $row)
		{
			$batchid.=$row[csf('batch_id')].',';
			$poid.=$row[csf('order_id')].',';
		}
		
		//Po id filter
		$expoid=array_unique(array_filter(explode(",",$poid)));
		$poIds=""; $tot_rows=0;
		foreach($expoid as $poidval)
		{
			$tot_rows++;
			$poIds.=$poidval.",";
		}
		$poidCond="";
		$poids=chop($poIds,',');
		if($db_type==2 && $tot_rows>1000)
		{
			$poidCond=" and (";
			$poIdsArr=array_chunk(explode(",",$poids),999);
			foreach($poIdsArr as $ids)
			{
				$ids=implode(",",$ids);
				$poidCond.=" b.id in($ids) or ";
			}
			$poidCond=chop($poidCond,'or ');
			$poidCond.=")";
		}
		else $poidCond=" and b.id in ($poids)"; 
		
		$job_order_arr=array();
		$sql_job="Select a.job_no_prefix_num, a.buyer_name, a.style_ref_no, b.id, b.po_number from wo_po_details_master a, wo_po_break_down b,subcon_inbound_bill_dtls c where a.job_no=b.job_no_mst and b.id=c.order_id and c.mst_id=$upid and a.status_active!=0 and a.is_deleted=0 and b.status_active!=0 and b.is_deleted=0 $poidCond";
		$sql_job_result =sql_select($sql_job);
		foreach($sql_job_result as $row)
		{
			$job_order_arr[$row[csf('id')]]['job']=$row[csf('job_no_prefix_num')];
			$job_order_arr[$row[csf('id')]]['style']=$row[csf('style_ref_no')];
			$job_order_arr[$row[csf('id')]]['buyer']=$row[csf('buyer_name')];
			$job_order_arr[$row[csf('id')]]['po']=$row[csf('po_number')];
		}
		unset($sql_job_result);
		
		// Batch Filter
		$exbatchid=array_unique(array_filter(explode(",",$batchid)));
		$batchIds=""; $tot_rows=0;
		foreach($exbatchid as $btchid)
		{
			$tot_rows++;
			$batchIds.=$btchid.",";
		}
		$batchidCond="";
		$batchIds=chop($batchIds,',');
		if($db_type==2 && $tot_rows>1000)
		{
			$batchidCond=" and (";
			$batchIdsArr=array_chunk(explode(",",$batchIds),999);
			foreach($batchIdsArr as $ids)
			{
				$ids=implode(",",$ids);
				$batchidCond.=" a.id in($ids) or ";
			}
			$batchidCond=chop($batchidCond,'or ');
			$batchidCond.=")";
		}
		else $batchidstrCond=" and a.id in ($batchIds)"; 
		
		$batch_array=array();
		//$grey_sql="Select a.id, a.batch_no, a.extention_no, a.process_id, sum(b.batch_qnty) as batch_qnty from pro_batch_create_mst a, pro_batch_create_dtls b where a.id=b.mst_id and a.entry_form!=36 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $batchidstrCond group by a.id, a.batch_no, a.extention_no, a.process_id";
		$grey_sql="Select a.id, a.batch_no, a.extention_no, a.process_id, sum(b.batch_qnty) as batch_qnty from pro_batch_create_mst a, pro_batch_create_dtls b,subcon_inbound_bill_dtls c where a.id=b.mst_id and c.batch_id=a.id and a.entry_form!=36 and c.mst_id=$upid and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 group by a.id, a.batch_no, a.extention_no, a.process_id";
		$grey_sql_result =sql_select($grey_sql);
		
		foreach($grey_sql_result as $row)
		{
			$batch_array[$row[csf('id')]]['batch_no']=$row[csf('batch_no')];
			$batch_array[$row[csf('id')]]['extention_no']=$row[csf('extention_no')];
			$batch_array[$row[csf('id')]]['process_id']=$row[csf('process_id')];
			$batch_array[$row[csf('id')]]['batch_qnty']=$row[csf('batch_qnty')];
		}
		unset($grey_sql_result);
		
		$str_val="";
		foreach ($sql_result_arr as $row)
		{
			$process_id=array_unique(explode(',',$batch_array[$row[csf('batch_id')]]['process_id']));
			$process_name='';
			foreach ($process_id as $val)
			{
				if($process_name=='') $process_name=$conversion_cost_head_array[$val]; else $process_name.=",".$conversion_cost_head_array[$val];
			}
			$desc_composition=$composition_arr[$row[csf('febric_description_id')]];
			
			if($str_val=="") $str_val=$row[csf('delivery_id')].'**'.change_date_format($row[csf('delivery_date')]).'**'.$row[csf('challan_no')].'**'.$row[csf('order_id')].'**'.$job_order_arr[$row[csf('order_id')]]['po'].'**'.$job_order_arr[$row[csf('order_id')]]['style'].'**'.$buyer_arr[$job_order_arr[$row[csf('order_id')]]['buyer']].'**'.$job_order_arr[$row[csf('order_id')]]['job'].'**'.$row[csf('carton_roll')].'**'.$row[csf('body_part_id')].'**'.$body_part[$row[csf("body_part_id")]].'**'.$row[csf('febric_description_id')].'**'.$row[csf('item_id')].'**'.$desc_composition.'**'.$row[csf('batch_id')].'**'.$row[csf('color_id')].'**'.$color_name[$row[csf('color_id')]].'**'.$batch_array[$row[csf('batch_id')]]['process_id'].'**'.$process_name.'**'.$row[csf('dia_width_type')].'**'.$fabric_typee[$row[csf('dia_width_type')]].'**'.number_format($row[csf('delivery_qty')], 2, '.', '').'**'.$row[csf('lib_rate_id')].'**'.$row[csf('rate')].'**'.$row[csf('add_rate_id')].'**'.$row[csf('add_rate')].'**'.number_format($row[csf('amount')], 4, '.', '').'**'.$row[csf('upd_id')].'**'.$row[csf('remarks')].'**'.$row[csf('currency_id')].'**'.$row[csf('rate_data_string')].'**'.$row[csf('color_range_id')].'**'.$row[csf('shade_percentage')]; 
			else $str_val.="###".$row[csf('delivery_id')].'**'.change_date_format($row[csf('delivery_date')]).'**'.$row[csf('challan_no')].'**'.$row[csf('order_id')].'**'.$job_order_arr[$row[csf('order_id')]]['po'].'**'.$job_order_arr[$row[csf('order_id')]]['style'].'**'.$buyer_arr[$job_order_arr[$row[csf('order_id')]]['buyer']].'**'.$job_order_arr[$row[csf('order_id')]]['job'].'**'.$row[csf('carton_roll')].'**'.$row[csf('body_part_id')].'**'.$body_part[$row[csf("body_part_id")]].'**'.$row[csf('febric_description_id')].'**'.$row[csf('item_id')].'**'.$desc_composition.'**'.$row[csf('batch_id')].'**'.$row[csf('color_id')].'**'.$color_name[$row[csf('color_id')]].'**'.$batch_array[$row[csf('batch_id')]]['process_id'].'**'.$process_name.'**'.$row[csf('dia_width_type')].'**'.$fabric_typee[$row[csf('dia_width_type')]].'**'.number_format($row[csf('delivery_qty')], 2, '.', '').'**'.$row[csf('lib_rate_id')].'**'.$row[csf('rate')].'**'.$row[csf('add_rate_id')].'**'.$row[csf('add_rate')].'**'.number_format($row[csf('amount')], 4, '.', '').'**'.$row[csf('upd_id')].'**'.$row[csf('remarks')].'**'.$row[csf('currency_id')].'**'.$row[csf('rate_data_string')].'**'.$row[csf('color_range_id')].'**'.$row[csf('shade_percentage')];
		}
	}
	else
	{
		$product_dtls_arr=return_library_array( "select b.id,b.item_description from pro_batch_create_dtls b,subcon_inbound_bill_dtls c where b.mst_id=c.batch_id and c.mst_id=$upid ",'id','item_description');
		$job_order_arr=array();
		//$sql_job="Select a.job_no_prefix_num, b.cust_buyer as buyer_name, b.cust_style_ref as style_ref_no, b.id, b.order_no as po_number from subcon_ord_mst a, subcon_ord_dtls b where a.subcon_job=b.job_no_mst and a.status_active=1 and a.is_deleted=0";
			$sql_job="Select a.job_no_prefix_num, b.cust_buyer as buyer_name, b.cust_style_ref as style_ref_no, b.id, b.order_no as po_number from subcon_ord_mst a, subcon_ord_dtls b,subcon_inbound_bill_dtls c where a.subcon_job=b.job_no_mst and b.id=c.order_id  and c.mst_id=$upid and a.status_active=1 and a.is_deleted=0";

		$sql_job_result =sql_select($sql_job);
		foreach($sql_job_result as $row)
		{
			$job_order_arr[$row[csf('id')]]['job']=$row[csf('job_no_prefix_num')];
			$job_order_arr[$row[csf('id')]]['style']=$row[csf('style_ref_no')];
			$job_order_arr[$row[csf('id')]]['buyer']=$row[csf('buyer_name')];
			$job_order_arr[$row[csf('id')]]['po']=$row[csf('po_number')];
		}
		unset($sql_job_result);
		
		$batch_array=array();
		//$grey_sql="Select a.id, a.batch_no, a.extention_no, a.process_id, sum(b.batch_qnty) as batch_qnty from pro_batch_create_mst a, pro_batch_create_dtls b where a.id=b.mst_id and a.entry_form=36 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 group by a.id, a.batch_no, a.extention_no, a.process_id";
		$grey_sql="Select a.id, a.batch_no, a.extention_no, a.process_id, sum(b.batch_qnty) as batch_qnty from pro_batch_create_mst a, pro_batch_create_dtls b,subcon_inbound_bill_dtls c where a.id=b.mst_id and a.id=c.batch_id and a.entry_form=36 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 group by a.id, a.batch_no, a.extention_no, a.process_id";
		$grey_sql_result =sql_select($grey_sql);
		
		foreach($grey_sql_result as $row)
		{
			$batch_array[$row[csf('id')]]['batch_no']=$row[csf('batch_no')];
			$batch_array[$row[csf('id')]]['extention_no']=$row[csf('extention_no')];
			$batch_array[$row[csf('id')]]['process_id']=$row[csf('process_id')];
			$batch_array[$row[csf('id')]]['batch_qnty']=$row[csf('batch_qnty')];
		}
		unset($grey_sql_result);
		
		$sql="select id as upd_id, delivery_id, delivery_date, challan_no, item_id, body_part_id, color_id, batch_id, febric_description_id, dia_width_type, add_process, add_process_name, packing_qnty as carton_roll, delivery_qty, lib_rate_id,rate_data_string, rate, add_rate_id, add_rate, amount, remarks, order_id,color_range_id,shade_percentage,currency_id from subcon_inbound_bill_dtls where mst_id=$upid  and status_active=1 and is_deleted=0 order by id ASC";
		//echo $sql;
		$sql_result_arr =sql_select($sql); $str_val="";
		foreach ($sql_result_arr as $row)
		{
			$process_id=array_unique(explode(',',$batch_array[$row[csf('batch_id')]]['process_id']));
			$process_name='';
			foreach ($process_id as $val)
			{
				if($process_name=='') $process_name=$conversion_cost_head_array[$val]; else $process_name.=",".$conversion_cost_head_array[$val];
			}
			$desc_composition=$composition_arr[$row[csf('febric_description_id')]];

			if($str_val=="") $str_val=$row[csf('delivery_id')].'**'.change_date_format($row[csf('delivery_date')]).'**'.$row[csf('challan_no')].'**'.$row[csf('order_id')].'**'.$job_order_arr[$row[csf('order_id')]]['po'].'**'.$job_order_arr[$row[csf('order_id')]]['style'].'**'.$job_order_arr[$row[csf('order_id')]]['buyer'].'**'.$job_order_arr[$row[csf('order_id')]]['job'].'**'.$row[csf('carton_roll')].'**'.$row[csf('body_part_id')].'**'.$body_part[$row[csf("body_part_id")]].'**'.$row[csf('febric_description_id')].'**'.$row[csf('item_id')].'**'.$desc_composition.'**'.$row[csf('batch_id')].'**'.$row[csf('color_id')].'**'.$color_name[$row[csf('color_id')]].'**'.$row[csf('add_process')].'**'.$row[csf('add_process_name')].'**'.$row[csf('dia_width_type')].'**'.$fabric_typee[$row[csf('dia_width_type')]].'**'.number_format($row[csf('delivery_qty')], 2, '.', '').'**'.$row[csf('lib_rate_id')].'**'.$row[csf('rate')].'**'.$row[csf('add_rate_id')].'**'.$row[csf('add_rate')].'**'.number_format($row[csf('amount')], 4, '.', '').'**'.$row[csf('upd_id')].'**'.$row[csf('remarks')].'**'.$row[csf('currency_id')].'**'.$row[csf('rate_data_string')].'**'.$row[csf('color_range_id')].'**'.$row[csf('shade_percentage')];
			
			else $str_val.="###".$row[csf('delivery_id')].'**'.change_date_format($row[csf('delivery_date')]).'**'.$row[csf('challan_no')].'**'.$row[csf('order_id')].'**'.$job_order_arr[$row[csf('order_id')]]['po'].'**'.$job_order_arr[$row[csf('order_id')]]['style'].'**'.$job_order_arr[$row[csf('order_id')]]['buyer'].'**'.$job_order_arr[$row[csf('order_id')]]['job'].'**'.$row[csf('carton_roll')].'**'.$row[csf('body_part_id')].'**'.$body_part[$row[csf("body_part_id")]].'**'.$row[csf('febric_description_id')].'**'.$row[csf('item_id')].'**'.$desc_composition.'**'.$row[csf('batch_id')].'**'.$row[csf('color_id')].'**'.$color_name[$row[csf('color_id')]].'**'.$row[csf('add_process')].'**'.$row[csf('add_process_name')].'**'.$row[csf('dia_width_type')].'**'.$fabric_typee[$row[csf('dia_width_type')]].'**'.number_format($row[csf('delivery_qty')], 2, '.', '').'**'.$row[csf('lib_rate_id')].'**'.$row[csf('rate')].'**'.$row[csf('add_rate_id')].'**'.$row[csf('add_rate')].'**'.number_format($row[csf('amount')], 4, '.', '').'**'.$row[csf('upd_id')].'**'.$row[csf('remarks')].'**'.$row[csf('currency_id')].'**'.$row[csf('rate_data_string')].'**'.$row[csf('color_range_id')].'**'.$row[csf('shade_percentage')];
		}
	}
	
	echo $str_val;
	exit();
}

if ($action=="save_update_delete")
{
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process )); 
	
	$order_ids=str_replace("'","",$orderIds);
	$order_ids=implode(",",array_unique(explode(",",$order_ids)));
	$updateId=str_replace("'","",$update_id);
	
	$control_with=str_replace("'","",$hddn_control_with);
	//$cbo_bill_sectionId=array(31,55);
	$party_sourceId=str_replace("'","",$cbo_party_source);
	$bill_forId=str_replace("'","",$cbo_bill_for);
	$fabric_dyeingCost_arr=array(25,26,31,32,34,38,39,60,61,62,63,74,75,76,78,79,80,81,83,84,85,86,87,88,89,90,92,129,137,138,139,141,144,145,146,147,149,158,162,163,165,169,170,171,172,173,174,176,203,209,232,234,238,266,267,280,281,282,283,284,285,286,287);
	//$fabric_dyeingCost_arr=array(31);
	if($control_with==1)
	{
		if($bill_forId!=3)
		{
			if($party_sourceId==1)
			{
	
				$sql_po=sql_select("select b.id, b.job_no_mst,a.exchange_rate from wo_po_break_down b,wo_pre_cost_mst a where  b.job_id= a.job_id and a.status_active=1 and b.status_active=1 and  b.id in($order_ids) ");
				foreach($sql_po as $row)
				{
					$job_arr[$row[csf('id')]]=$row[csf('job_no_mst')];
					$job_exchnage_arr[$row[csf('job_no_mst')]]=$row[csf('exchange_rate')];
				}
				$condition = new condition();
				if($order_ids!='' || $order_ids!=0)
				{
					$condition->po_id_in($order_ids); 
				}
				
				$condition->init();
				$conversion= new conversion($condition);
				//echo "10**=".$conversion->getQuery(); die;
				$conversion_costing_arr=$conversion->getAmountArray_by_orderAndProcess();
				//print_r($conversion_costing_arr);
				if($updateId!="") $thisbill_cond=" and a.id!='$updateId'"; else $thisbill_cond="";
				
				$previous_bill_sql=sql_select("select b.order_id,sum(b.amount) as amount from subcon_inbound_bill_mst a, subcon_inbound_bill_dtls b where a.id=b.mst_id and a.party_source=1 and a.process_id=4 and b.order_id in ($order_ids) $thisbill_cond and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 group by b.order_id");
			//	echo "10**=select b.order_id,sum(b.amount) as amount from subcon_inbound_bill_mst a, subcon_inbound_bill_dtls b where a.id=b.mst_id and a.party_source=1 and a.process_id=2 and b.order_id in ($order_ids) $thisbill_cond and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 group by b.order_id";die;
				foreach($previous_bill_sql as $row)
				{
					$previous_bill_amountArr[$row[csf('order_id')]]=$row[csf('amount')];
				}
				for($i=1; $i<=$tot_row; $i++)
				{
					 
					$po_id="ordernoid_".$i;
					$po_ids=str_replace("'","",$$po_id);
					//$quantity="deliveryqnty_".$i;
					//$rate="txtrate_".$i;
					$amount="txtAmount_".$i;
					$curanci="curanci_".$i;
					$current_amount=str_replace("'","",$$amount);
					$curanciId=str_replace("'","",$$curanci);
					$previous_bill_amount=$previous_bill_amountArr[$po_ids];
					if($previous_bill_amount=='') $previous_bill_amount=0;
					$msg="Total bill amount exceeding costing amount not allowed.";
					
	
					
					if($curanciId==1) //TK
					{
						$budget_amount=0;
						$ex_change_rate=$job_exchnage_arr[$job_arr[$po_ids]];	
						foreach($fabric_dyeingCost_arr as $fab_process_id)
						{
						$budget_amount+=array_sum($conversion_costing_arr[$po_ids][$fab_process_id])*$ex_change_rate;
						}
					}
					else
					{
						//$ex_change_rate=1;	
						$budget_amount=0;
						foreach($fabric_dyeingCost_arr as $fab_process_id)
						{
						$budget_amount+=array_sum($conversion_costing_arr[$po_ids][$fab_process_id]);
						//echo  "10**=".$po_ids;die;
						}
						  
					}
					
					
					$total_bill_amount=$previous_bill_amount+$current_amount;
					$avaible_bill_amount=$budget_amount-$previous_bill_amount;
					//echo "10**=".$previous_bill_amount.'='.$current_amount.'='.$budget_amount.'='.$curanciId;die;
					
					if($total_bill_amount>$budget_amount)
					{
						echo "17**".rtrim($previous_bill_amount)."**".rtrim($budget_amount)."**".rtrim($avaible_bill_amount)."**".$msg;
						disconnect($con);die;
						//echo $total_bill_amount."_".$budget_amount."_".$previous_bill_amount."_".$current_amount;
					}
				} //Loop end
				
			}
		}
    }
	//echo "10**="."17**".rtrim($previous_bill_amount)."**".rtrim($budget_amount)."**".rtrim($avaible_bill_amount)."**".$msg;die;
	
	if ($operation==0)   // Insert Here========================================================================================
	{
		$con = connect();
		if($db_type==0)
		{
			mysql_query("BEGIN");
		}
		if (is_duplicate_field( "delivery_id", "subcon_inbound_bill_dtls", "mst_id=$update_id" )==1)
		{
			echo "11**0"; 
			disconnect($con);
			die;			
		}
		if($db_type==0) $year_cond=" and YEAR(insert_date)"; else if($db_type==2) $year_cond=" and TO_CHAR(insert_date,'YYYY')";	
		
		for($i=1; $i<=$tot_row; $i++)
		{
			$color_process="color_process_".$i;  
		}
		
		$new_bill_no=explode("*",return_mrr_number( str_replace("'","",$cbo_company_id), '', 'DFB', date("Y",time()), 5, "select prefix_no, prefix_no_num from  subcon_inbound_bill_mst where company_id=$cbo_company_id and process_id=4 $year_cond=".date('Y',time())." order by id desc ", "prefix_no", "prefix_no_num" ));
		$id=return_next_id( "id", "subcon_inbound_bill_mst", 1 ) ; 	
		$field_array="id, prefix_no, prefix_no_num, bill_no, company_id, location_id, bill_date, party_id, party_source, party_location_id, inhouse_bill_from, bill_for,upcharge,discount,remarks,process_id, inserted_by, insert_date";
		$data_array="(".$id.",'".$new_bill_no[1]."','".$new_bill_no[2]."','".$new_bill_no[0]."',".$cbo_company_id.",".$cbo_location_name.",".$txt_bill_date.",".$cbo_party_name.",".$cbo_party_source.",".$cbo_party_location.",".$hidd_inhouse_bill_from.",".$cbo_bill_for.",".$txt_upcharge.",".$txt_discount.",".$txt_remarks.",4,".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."')"; 
		//echo "INSERT INTO subcon_inbound_bill_mst (".$field_array.") VALUES ".$data_array; //die;
			
		$return_no=$new_bill_no[0];
		
		$id1=return_next_id( "id", "subcon_inbound_bill_dtls",1);
		
		$field_array1 ="id, mst_id, delivery_id, delivery_date, challan_no, order_id, item_id, febric_description_id, dia_width_type, batch_id, body_part_id, add_process, add_process_name, packing_qnty, delivery_qty, lib_rate_id,rate_data_string, rate, add_rate_id, add_rate, amount, remarks,color_range_id,shade_percentage,currency_id, process_id, color_id, inserted_by, insert_date";
		$field_array_up ="delivery_id*delivery_date*challan_no*order_id*item_id*febric_description_id*body_part_id*add_process*add_process_name*color_id*packing_qnty*delivery_qty*lib_rate_id*rate_data_string*rate*add_rate_id*add_rate*amount*remarks*color_range_id*shade_percentage*currency_id*updated_by*update_date";
		$field_array_delivery="bill_status";
		$field_array_order="cust_buyer*cust_style_ref";
		$add_comma=0; $data_array1='';
		for($i=1; $i<=$tot_row; $i++)
		{
			$delivery_id="deliveryid_".$i;
			$delevery_date="txtDeleverydate_".$i;
			$challen_no="txtChallenno_".$i;
			$orderid="ordernoid_".$i;
			$item_id="itemid_".$i;
			$compoid="compoid_".$i;
			$bodypartid="bodypartid_".$i;
			$style_name="txtStylename_".$i;
			$buyer_name="txt_buyername_".$i;
			$number_roll="txtNumberroll_".$i;
			$quantity="txtDeliveryqnty_".$i;
			$libRateId="libRateId_".$i;
			$rateDataString="rateDataString_".$i;
			$rate="txtRate_".$i;
			$libAddRateId="libAddRateId_".$i;
			$addRate="txtAddRate_".$i;
			$amount="txtAmount_".$i;
			$cbocolorrangeid="cbocolorrangeid_".$i;
			$txtshadeper="txtshadeper_".$i;
			$curanci="curanci_".$i;
			$remarks="remarksvalue_".$i;
			$updateid_dtls="updateiddtls_".$i;
			$color_process="colorProcess_".$i;
			$color_id="colorId_".$i;
			$add_process="addProcess_".$i;
			$txt_add_process="txtAddProcess_".$i;
			$diaType="diaType_".$i;
			$batchid="batchid_".$i;
			
			$rate_cal=str_replace("'",'',$$rate)*1;
			$processId=str_replace("'",'',$$color_process);
			$addRate_cal=str_replace("'",'',$$addRate)*1;
			$tot_rate=$rate_cal+$addRate_cal;
			$quantity_cal=str_replace("'",'',$$quantity)*1;
			$tot_amount=number_format($quantity_cal*$tot_rate,2,'.','');
			
			if($processId) $processId=$processId;
			else $processId=4;
			
			  
			if(str_replace("'",'',$$updateid_dtls)=="")  
			{ 
				if ($add_comma!=0) $data_array1.=",";
				$data_array1.="(".$id1.",".$id.",".$$delivery_id.",".$$delevery_date.",".$$challen_no.",".$$orderid.",".$$item_id.",".$$compoid.",".$$diaType.",".$$batchid.",".$$bodypartid.",".$$add_process.",".$$txt_add_process.",".$$number_roll.",".$$quantity.",".$$libRateId.",".$$rateDataString.",".$$rate.",".$$libAddRateId.",".$$addRate.",".$tot_amount.",".$$remarks.",".$$cbocolorrangeid.",".$$txtshadeper.",".$$curanci.",".$processId.",".$$color_id.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."')";
				$id1=$id1+1;
				$add_comma++;
				$id_arr_deli=implode(',',explode('_',str_replace("'",'',$$delivery_id)));
				$delv_id=explode(',',$id_arr_deli);
				//$id_arr_delivery=explode(',',$id_arr_deli);
				//$data_array_delivery[explode(',',str_replace("'",'',$$delivery_id))] =explode("*",("1"));
				foreach($delv_id as $val)
				{
					$id_arr_delivery[]=$val;
					$data_array_delivery[$val] =explode("*",("1"));
				}
			}
			else
			{
				$id_arr[]=str_replace("'",'',$$updateid_dtls);
				$data_array_up[str_replace("'",'',$$updateid_dtls)] =explode("*",("".$$delivery_id."*".$$delevery_date."*".$$challen_no."*".$$orderid."*".$$item_id."*".$$add_process."*".$$txt_add_process."*".$$color_id."*".$$number_roll."*".$$quantity."*".$$libRateId."*".$$rateDataString."*".$$rate."*".$$libAddRateId."*".$$addRate."*".$tot_amount."*".$$remarks."*".$$cbocolorrangeid."*".$$txtshadeper."*".$$curanci."*'".$_SESSION['logic_erp']['user_id']."'*'".$pc_date_time."'"));
				$id_arr_deli=implode(',',explode('_',str_replace("'",'',$$delivery_id)));
				$delv_id=explode(',',$id_arr_deli);
				//$id_arr_delivery=explode(',',$id_arr_deli);
				//$data_array_delivery[explode(',',str_replace("'",'',$$delivery_id))] =explode("*",("1"));
				foreach($delv_id as $val)
				{
					$id_arr_delivery[]=$val;
					$data_array_delivery[$val] =explode("*",("1"));
				}
			}
			// echo $data_array1;die;
			//order table insert====================================================================================================
			if(str_replace("'",'',$$style_name)=="" || str_replace("'",'',$$buyer_name)=="")  
			{
				$order_id_arr[]=str_replace("'",'',$$orderid);
				$data_array_order[str_replace("'",'',$$orderid)] =explode("*",("".$$buyer_name."*".$$style_name.""));
			}
			else
			{
				$order_id_arr[]=str_replace("'",'',$$orderid);
				$data_array_order[str_replace("'",'',$$orderid)] =explode("*",("".$$buyer_name."*".$$style_name.""));	
			}
		}
		//echo "10**";
		//$rID1=execute_query(bulk_update_sql_statement("subcon_inbound_bill_dtls", "id",$field_array_up,$data_array_up,$id_arr ));
			//echo bulk_update_sql_statement( "subcon_inbound_bill_dtls", "id",$field_array_up,$data_array_up,$id_arr );
			
		$flag=1;
		$rID=sql_insert("subcon_inbound_bill_mst",$field_array,$data_array,0);
		if($rID==1 && $flag==1) $flag=1; else $flag=0;
		if (str_replace("'",'',$cbo_party_source)==2)
		{
			$rID2=execute_query(bulk_update_sql_statement( "subcon_delivery_dtls", "id",$field_array_delivery,$data_array_delivery,$id_arr_delivery ));
			if($rID2==1 && $flag==1) $flag=1; else $flag=0;
			//$rID4=execute_query(bulk_update_sql_statement( "subcon_ord_dtls", "id",$field_array_order,$data_array_order,$order_id_arr ));
			//if($rID4==1 && $flag==1) $flag=1; else $flag=0;
		}
		 
		if($data_array1!="")
		{
			//echo "10**insert into subcon_inbound_bill_dtls (".$field_array1.") values ".$data_array1; die;
		/*	echo $rID2.'**'.$rID4.'**'.$rID;
			die;*/
			$rID1=sql_insert("subcon_inbound_bill_dtls",$field_array1,$data_array1,1);
			if($rID1==1 && $flag==1) $flag=1; else $flag=0;
		}
		
		//echo "10**".$rID.'='.$rID1.'='.$rID2.'='.$rID4.'='.$flag; die;
	/*	echo $data_array1;
		die;*/
		
		if($db_type==0)
		{
			if($flag==1)
			{
				mysql_query("COMMIT");  
				echo "0**".str_replace("'",'',$id)."**".str_replace("'",'',$return_no);
			}
			else
			{
				mysql_query("ROLLBACK"); 
				echo "10**".str_replace("'",'',$id)."**".str_replace("'",'',$return_no);
			}
		}
		else if($db_type==2)
		{
			if($flag==1)
			{
				oci_commit($con);
				echo "0**".str_replace("'",'',$id)."**".str_replace("'",'',$return_no);
			}
			else
			{
				oci_rollback($con);
				echo "10**".str_replace("'",'',$id)."**".str_replace("'",'',$return_no);
			}
		}	
		
		disconnect($con);
		die;
	}
	else if ($operation==1)   // Update Here=============================================================================
	{
		$con = connect();
		if($db_type==0)
		{
			mysql_query("BEGIN");
		}
		
		$id=str_replace("'",'',$update_id);
		
		$nameArray= sql_select("select is_posted_account,post_integration_unlock from subcon_inbound_bill_mst where id='$id'");
		$posted_account=$nameArray[0][csf('is_posted_account')];
		$post_integration_unlock=$nameArray[0][csf('post_integration_unlock')];
		
		if($posted_account==1 && $post_integration_unlock==0)
		{
			echo "14**All Ready Posted in Accounting.";
			disconnect($con);
			exit();
		}
		$field_array="bill_date*upcharge*discount*remarks*updated_by*update_date";
		$data_array="".$txt_bill_date."*".$txt_upcharge."*".$txt_discount."*".$txt_remarks."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'"; 
		$return_no=str_replace("'",'',$txt_bill_no);
		
		
		$sql_dtls="Select id from subcon_inbound_bill_dtls where mst_id=$update_id and status_active=1 and is_deleted=0";
		$nameArray=sql_select( $sql_dtls );
		foreach($nameArray as $row)
		{
			$dtls_update_id_array[]=$row[csf('id')];
		}
		
		$id1=return_next_id( "id", "subcon_inbound_bill_dtls",1);
		
		$field_array1 ="id, mst_id, delivery_id, delivery_date, challan_no, order_id, item_id, febric_description_id, dia_width_type, batch_id, body_part_id, add_process, add_process_name, packing_qnty, delivery_qty, lib_rate_id,rate_data_string, rate, add_rate_id, add_rate, amount, remarks,color_range_id,shade_percentage,currency_id, process_id, color_id, inserted_by, insert_date";
			
		$field_array_up ="packing_qnty*add_process_name*delivery_qty*lib_rate_id*rate_data_string*rate*add_rate_id*add_rate*amount*remarks*color_range_id*shade_percentage*currency_id*updated_by*update_date";
		
		$field_array_delivery="bill_status";
		$field_array_order="cust_buyer*cust_style_ref";
		
		$add_comma=0;
		for($i=1; $i<=$tot_row; $i++)
		{
			$delivery_id="deliveryid_".$i;
			$delevery_date="txtDeleverydate_".$i;
			$challen_no="txtChallenno_".$i;
			$orderid="ordernoid_".$i;
			$item_id="itemid_".$i;
			$compoid="compoid_".$i;
			$bodypartid="bodypartid_".$i;
			$style_name="txtStylename_".$i;
			$buyer_name="txtBuyername_".$i;
			$number_roll="txtNumberroll_".$i;
			$quantity="txtDeliveryqnty_".$i;
			$libRateId="libRateId_".$i;
			$rateDataString="rateDataString_".$i;
			$rate="txtRate_".$i;
			$libAddRateId="libAddRateId_".$i;
			$addRate="txtAddRate_".$i;
			$amount="txtAmount_".$i;
			$cbocolorrangeid="cbocolorrangeid_".$i;
			$txtshadeper="txtshadeper_".$i;
			$curanci="curanci_".$i;
			$remarks="remarksvalue_".$i;
			$updateid_dtls="updateiddtls_".$i;
			$color_process="colorProcess_".$i;
			$color_id="colorId_".$i;
			$add_process="addProcess_".$i;
			$txt_add_process="txtAddProcess_".$i;
			$diaType="diaType_".$i;
			$batchid="batchid_".$i;
			
			$rate_cal=str_replace("'",'',$$rate)*1;
			$addRate_cal=str_replace("'",'',$$addRate)*1;
			$processId=str_replace("'",'',$$color_process);
			$tot_rate=$rate_cal+$addRate_cal;
			$quantity_cal=str_replace("'",'',$$quantity)*1;
			$tot_amount=number_format($quantity_cal*$tot_rate,2,'.','');
			
			if($processId) $processId=$processId;
			else $processId=4;
				
			if(str_replace("'",'',$$updateid_dtls)=="")  
			{ 
			  if ($add_comma!=0) $data_array1 .=",";
			  $data_array1 .="(".$id1.",".$id.",".$$delivery_id.",".$$delevery_date.",".$$challen_no.",".$$orderid.",".$$item_id.",".$$compoid.",".$$diaType.",".$$batchid.",".$$bodypartid.",".$$add_process.",".$$txt_add_process.",".$$number_roll.",".$$quantity.",".$$libRateId.",".$$rateDataString.",".$$rate.",".$$libAddRateId.",".$$addRate.",".$tot_amount.",".$$remarks.",'".str_replace("'",'',$$cbocolorrangeid)."','".str_replace("'",'',$$txtshadeper)."','".str_replace("'",'',$$curanci)."',".$processId.",".$$color_id.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."')";
			  $id1=$id1+1;
			  $add_comma++;
				
				$id_arr_deli=implode(',',explode('_',str_replace("'",'',$$delivery_id)));
				$delv_id=explode(',',$id_arr_deli);
				//$id_arr_delivery=explode(',',$id_arr_deli);
				//$data_array_delivery[explode(',',str_replace("'",'',$$delivery_id))] =explode("*",("1"));
				foreach($delv_id as $val)
				{
					$id_arr_delivery[]=$val;
					$data_array_delivery[$val] =explode("*",("1"));
				}
			}
			else
			{
				$id_arr[]=str_replace("'",'',$$updateid_dtls);
				
				$data_array_up[str_replace("'",'',$$updateid_dtls)] =explode("*",("".$$number_roll."*".$$txt_add_process."*".$$quantity."*".$$libRateId."*".$$rateDataString."*".$$rate."*".$$libAddRateId."*".$$addRate."*".$tot_amount."*".$$remarks."*".$$cbocolorrangeid."*".$$txtshadeper."*".$$curanci."*'".$_SESSION['logic_erp']['user_id']."'*'".$pc_date_time."'"));

				$id_arr_deli=implode(',',explode('_',str_replace("'",'',$$delivery_id)));
				$delv_id=explode(',',$id_arr_deli);
				//$id_arr_delivery=explode(',',$id_arr_deli);
				//$data_array_delivery[explode(',',str_replace("'",'',$$delivery_id))] =explode("*",("1"));
				foreach($delv_id as $val)
				{
					$id_arr_delivery[]=$val;
					$data_array_delivery[$val] =explode("*",("1"));
				}
			}
			//print_r ($data_array_delivery);
		    //order table insert====================================================================================================
			if(str_replace("'",'',$$style_name)=="" || str_replace("'",'',$$buyer_name)=="")  
			{
				$order_id_arr[]=str_replace("'",'',$$orderid);
				$data_array_order[str_replace("'",'',$$orderid)] =explode("*",("".$$buyer_name."*".$$style_name.""));
			}
			else
			{
				$order_id_arr[]=str_replace("'",'',$$orderid);
				$data_array_order[str_replace("'",'',$$orderid)] =explode("*",("".$$buyer_name."*".$$style_name.""));	
			}
		//order table insert====================================================================================================
		}
		$flag=1;
		$rID=sql_update("subcon_inbound_bill_mst",$field_array,$data_array,"id",$id,0);
		if($rID==1 && $flag==1) $flag=1; else $flag=0;
		
		$rID1=execute_query(bulk_update_sql_statement("subcon_inbound_bill_dtls", "id",$field_array_up,$data_array_up,$id_arr ));
		if($rID1==1 && $flag==1) $flag=1; else $flag=0;
			//echo $flag;die;
		
			
		if($data_array1!="")
		{
			//echo "insert into subcon_inbound_bill_dtls (".$field_array1.") values ".$data_array1;die;
			$rID1=sql_insert("subcon_inbound_bill_dtls",$field_array1,$data_array1,0);
			if($rID1==1 && $flag==1) $flag=1; else $flag=0;
		}
		
		//echo $delete_id; die;
		/*if (str_replace("'",'',$cbo_party_source)==1)
		{*/
			if(implode(',',$id_arr)!="")
			{
				$distance_delete_id=implode(',',array_diff($dtls_update_id_array,$id_arr));
			}
			else
			{
				$distance_delete_id=implode(',',$dtls_update_id_array);
			}
			if(str_replace("'",'',$distance_delete_id)!="")
			{
				$rID3=execute_query( "delete from subcon_inbound_bill_dtls where id in ($distance_delete_id)",0);
				if($rID3==1 && $flag==1) $flag=1; else $flag=0;
			}
		/*}
		else
		{
			if(implode(',',$id_arr)!="")
			{
				$distance_delete_id=implode(',',array_diff($dtls_update_id_array,$id_arr));
			}
			else
			{
				$distance_delete_id=implode(',',$dtls_update_id_array);
			}
			if(str_replace("'",'',$distance_delete_id)!="")
			{
				$rID3=execute_query( "delete from subcon_inbound_bill_dtls where id in ($distance_delete_id)",0);
				if($rID3) $flag=1; else $flag=0;
			}
		}*/
		if (str_replace("'",'',$cbo_party_source)==2)
		{
			$rID2=execute_query(bulk_update_sql_statement( "subcon_delivery_dtls", "id",$field_array_delivery,$data_array_delivery,$id_arr_delivery ));
			if($rID2==1 && $flag==1) $flag=1; else $flag=0;
			
			//echo bulk_update_sql_statement( "subcon_ord_dtls", "id",$field_array_order,$data_array_order,$order_id_arr );die;
			//$rID4=execute_query(bulk_update_sql_statement( "subcon_ord_dtls", "id",$field_array_order,$data_array_order,$order_id_arr ));
			//if($rID4==1 && $flag==1) $flag=1; else $flag=0;
		}

				
		if($db_type==0)
		{
			if($flag==1)
			{
				mysql_query("COMMIT");  
				echo "1**".str_replace("'",'',$id)."**".str_replace("'",'',$return_no);
			}
			else
			{
				mysql_query("ROLLBACK"); 
				echo "10**".str_replace("'",'',$id)."**".str_replace("'",'',$return_no);
			}
		}
		else if($db_type==2)
		{
			if($flag==1)
			{
				oci_commit($con);
				echo "1**".str_replace("'",'',$id)."**".str_replace("'",'',$return_no);
			}
			else
			{
				oci_rollback($con);
				echo "10**".str_replace("'",'',$id)."**".str_replace("'",'',$return_no);
			}
		}	
		disconnect($con);
		die;
	}
	else if ($operation==2)  //Delete here======================================================================================
	{
		$con = connect();
		if($db_type==0)
		{
			mysql_query("BEGIN");
		}
		$id=str_replace("'",'',$update_id);
		$return_no=str_replace("'",'',$txt_bill_no);
		$field_array_delivery="bill_status";
		if(str_replace("'",'',$delete_id)!="")
		{
			$delete_id=str_replace("'",'',$delete_id);
			$rID3=execute_query( "delete from subcon_inbound_bill_dtls where delivery_id in ($delete_id)",0);
			$delete_id=explode(",",str_replace("'",'',$delete_id));
			for ($i=0;$i<count($delete_id);$i++)
			{
				$id_delivery[]=$delete_id[$i];
				$data_delivery[str_replace("'",'',$delete_id[$i])] =explode(",",("0"));
			}
		}
		echo "10**=";
		echo bulk_update_sql_statement( "subcon_delivery", "id",$field_array_delivery,$data_delivery,$id_delivery );
		die;
		$rID4=execute_query(bulk_update_sql_statement( "subcon_delivery", "id",$field_array_delivery,$data_delivery,$id_delivery ));
		
		if($db_type==0)
		{
			if($rID3 && $rID4)
			{
				mysql_query("COMMIT");  
				echo "2**".str_replace("'",'',$id)."**".str_replace("'",'',$return_no);
			}
			else
			{
				mysql_query("ROLLBACK"); 
				echo "10**".str_replace("'",'',$id)."**".str_replace("'",'',$return_no);
			}
		}
		disconnect($con);
		die;
	}
}
//=======================================Bill Print==========================================================================
if($action=="dyeing_finishin_bill_print4")
{
	extract($_REQUEST);
	$data=explode('*',$data);

	$imge_arr=return_library_array( "select master_tble_id,image_location from common_photo_library where form_name='company_details' and file_type=1",'master_tble_id','image_location');
	$company_library=return_library_array( "select id, company_name from lib_company", "id", "company_name");
	$party_library=return_library_array( "select id,buyer_name from lib_buyer", "id","buyer_name");
	$color_library=return_library_array( "select id,color_name from  lib_color where status_active=1 and is_deleted=0", "id","color_name");
	$color_id_arr=return_library_array( "select id, color_id from subcon_delivery_dtls",'id','color_id');
	$inv_item_arr=return_library_array( "select id,material_description from sub_material_dtls",'id','material_description');
	$prod_item_arr=return_library_array( "select id,fabric_description from subcon_production_dtls",'id','fabric_description');
	$prod_process_arr=return_library_array( "select cons_comp_id, process from subcon_production_dtls",'cons_comp_id','process');
	$prod_dtls_arr=return_library_array( "select id, product_name_details from  product_details_master",'id','product_name_details');
	//$location_arr=return_library_array("select id,location_name from lib_location", "id","location_name");
	
	$sql_mst="Select id, bill_no, bill_date, location_id, party_id, party_source, upcharge,discount,party_location_id, bill_for, terms_and_condition from subcon_inbound_bill_mst where company_id=$data[0] and id='$data[1]' and status_active=1 and is_deleted=0";
	$dataArray=sql_select($sql_mst);
	$upcharge=$dataArray[0][csf('upcharge')];
	$discount=$dataArray[0][csf('discount')];

	$location_id=$dataArray[0][csf('location_id')];
	$sql_com_loc="select b.id,b.location_name,b.address from lib_location b,lib_company a where a.id=b.company_id and b.company_id=$data[0] and b.id=$location_id and a.status_active=1";
	$dataArray_loc=sql_select($sql_com_loc);
	foreach($dataArray_loc as $row)
	{
		$loc_address=$row[csf('address')];
		$location_arr[$row[csf('id')]]=$row[csf('location_name')];
	}
	
	?>
    <div style="width:1130px;" align="center">
    <table width="900" cellpadding="0" cellspacing="0" align="center">
        <tr>
            <td width="100" align="right"> 
            	<img  src='../../<? echo $imge_arr[str_replace("'","",$data[0])]; ?>' height='70' width='100' />
            </td>
            <td>
                <table width="800" cellspacing="0" align="center">
                    <tr>
                    	<td align="center" style="font-size:20px"><strong ><? echo $company_library[$data[0]]; ?></strong></td>
                    </tr>
                    <tr>
                        <td align="center"  style="font-size:16px"><strong>Unit : <? echo $location_arr[$dataArray[0][csf('location_id')]]; ?></strong></td>
                    </tr>
                    <tr class="form_caption">
                        <td  align="center" style="font-size:14px"><? echo $loc_address;//show_company($data[0],'',''); ?></td>  
                    </tr>
                    <tr>
                    	<td align="center" style="font-size:18px"><strong><? echo $data[3]; ?></strong></td>
                    </tr>
                </table>
            </td>
        </tr>
    </table> 
    <table width="930" cellspacing="0" align="center" border="0">   
    	  <tr><td colspan="6" align="center"></hr></td></tr>
             <tr>
			 <?
			 	if($dataArray[0][csf('party_source')]==2)
				{
					$party_add=$dataArray[0][csf('party_id')];
					$nameArray=sql_select( "select address_1, web_site, buyer_email, country_id from  lib_buyer where id=$party_add"); 
					foreach ($nameArray as $result)
					{ 
                    	$address="";
						if($result!="") $address=$result[csf('address_1')];//.'<br>'.$country_arr[$result['country_id']].'<br>'.$result['email'].'<br>'.$result['web_site']
					}
					$party_details=$party_library[$party_add].'<br>'.$address;
				}
				else if($dataArray[0][csf('party_source')]==1)
				{
					$party_details=$company_library[$dataArray[0][csf('party_id')]];
				}
			 ?>
                <td width="300" rowspan="4" valign="top" colspan="2"><strong>Party :<? echo $party_details; ?></strong></td>
                <td width="130"><strong>Party Location: </strong></td><td width="175px"> <? echo $location_arr[$dataArray[0][csf('party_location_id')]]; ?></td>
                <td width="130"><strong>Bill No :</strong></td> <td width="175"><? echo $dataArray[0][csf('bill_no')]; ?></td>
            </tr>
             <tr>
             	<td><strong>Bill Date: </strong></td><td> <? echo change_date_format($dataArray[0][csf('bill_date')]); ?></td>
                <td><strong>Source :</strong></td> <td><? echo $knitting_source[$dataArray[0][csf('party_source')]]; ?></td>
            </tr>
            <tr>
                <td><strong>Bill For :</strong></td> <td><? echo $bill_for[$dataArray[0][csf('bill_for')]]; ?></td>
                <td>&nbsp;</td><td>&nbsp;</td>
                <td>&nbsp;</td><td>&nbsp;</td>
            </tr>
        </table>
        <br>
        <?
		$mst_id=$dataArray[0][csf('id')];
		$process_ids_arr=array();
		$sql_rate="select id, process_id, in_house_rate, uom_id, rate_type_id, customer_rate, buyer_id, status_active from lib_subcon_charge where status_active!=0 and is_deleted=0 and rate_type_id in (3,4,7,8) and process_type_id=1 order by id Desc";
		$sql_rate_res=sql_select($sql_rate);
		foreach($sql_rate_res as $rrow)
		{
			$process_ids_arr[$rrow[csf('id')]]=$rrow[csf('process_id')];
		}
		unset($sql_rate_res);
		
		$batch_array=array(); $order_array=array();
		$grey_color_array=array();
		//$grey_sql="Select a.color_id, a.color_range_id, b.fabric_from, b.po_id, a.id, b.item_description from pro_batch_create_mst a, pro_batch_create_dtls b where a.id=b.mst_id and b.status_active=1 and b.is_deleted=0";
		$grey_sql="Select a.color_id, a.color_range_id, b.fabric_from, b.po_id, b.id, b.item_description from pro_batch_create_mst a, pro_batch_create_dtls b,subcon_inbound_bill_dtls c where a.id=b.mst_id and c.batch_id=a.id and c.mst_id='$mst_id' and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0";
		$grey_sql_result =sql_select($grey_sql);
		foreach($grey_sql_result as $row)
		{
			$batch_array[$row[csf('id')]]['color_range']=$row[csf('color_range_id')];
			$batch_array[$row[csf('id')]]['color']=$row[csf('color_id')];
			$batch_array[$row[csf('id')]]['item_description']=$row[csf('item_description')];
		}	
		
		if($dataArray[0][csf('party_source')]==2)
		{
		//	$order_sql="select id, order_no, order_uom, cust_buyer, cust_style_ref from subcon_ord_dtls where status_active=1 and is_deleted=0";
			$order_sql="select b.id, b.order_no, b.order_uom, b.cust_buyer, b.cust_style_ref from subcon_ord_dtls b,subcon_inbound_bill_dtls c  where b.id=c.order_id and b.status_active=1 and b.is_deleted=0 and c.mst_id='$mst_id'";
			$order_sql_result =sql_select($order_sql);
			foreach($order_sql_result as $row)
			{
				$order_array[$row[csf('id')]]['order_no']=$row[csf('order_no')];
				$order_array[$row[csf('id')]]['order_uom']=$row[csf('order_uom')];
				$order_array[$row[csf('id')]]['cust_buyer']=$row[csf('cust_buyer')];
				$order_array[$row[csf('id')]]['cust_style_ref']=$row[csf('cust_style_ref')];
			}
		}
		else if($dataArray[0][csf('party_source')]==1)
		{
			//$order_sql="select a.buyer_name, a.style_ref_no, a.order_uom, b.id, b.po_number, b.grouping from wo_po_details_master a, wo_po_break_down b where a.job_no=b.job_no_mst and a.status_active!=0 and a.is_deleted=0 and b.status_active!=0 and b.is_deleted=0";
			$order_sql="SELECT a.job_no,a.buyer_name, a.style_ref_no, a.order_uom, b.id, b.po_number, b.grouping from wo_po_details_master a, wo_po_break_down b,subcon_inbound_bill_dtls c  where a.job_no=b.job_no_mst  and b.id=c.order_id and a.status_active!=0 and a.is_deleted=0 and b.status_active!=0 and b.is_deleted=0 and c.mst_id='$mst_id'";
			$order_sql_result =sql_select($order_sql);
			//echo $order_sql;
			foreach($order_sql_result as $row)
			{
				$order_array[$row[csf('id')]]['order_no']=$row[csf('po_number')];
				$order_array[$row[csf('id')]]['order_uom']=$row[csf('order_uom')];
				$order_array[$row[csf('id')]]['cust_buyer']=$party_library[$row[csf('buyer_name')]];
				$order_array[$row[csf('id')]]['cust_style_ref']=$row[csf('style_ref_no')];
				$order_array[$row[csf('id')]]['internal_ref']=$row[csf('grouping')];
				$job_no=$row[csf('job_no')];
			}
			$recChallan_arr=array();
			
			$rec_challa_sql="SELECT a.recv_number_prefix_num, a.challan_no, b.batch_id, b.prod_id, b.body_part_id, b.fabric_description_id, b.dia_width_type, c.po_breakdown_id
							FROM inv_receive_master a, pro_finish_fabric_rcv_dtls b, order_wise_pro_details c, pro_batch_create_mst d
							WHERE a.id=b.mst_id and b.id=c.dtls_id and d.id=b.batch_id and c.trans_type=1 and c.entry_form in (7,37) and c.trans_id!=0 and a.entry_form in (7,37) AND a.knitting_source=1 AND a.company_id='".$dataArray[0][csf('party_id')]."' AND a.location_id='".$dataArray[0][csf('location_id')]."' AND a.knitting_company=$data[0] and a.receive_basis in(4,5,9) and b.trans_id!=0  and a.item_category=2 
							and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 
							group by a.id, a.recv_number_prefix_num, a.challan_no, b.batch_id, b.prod_id, b.body_part_id, b.fabric_description_id, b.dia_width_type, c.po_breakdown_id order by a.recv_number_prefix_num DESC";
			$rec_challa_sql_res=sql_select($rec_challa_sql);
			foreach($rec_challa_sql_res as $row)
			{
				$recChallan_arr[$row[csf('recv_number_prefix_num')]][$row[csf('po_breakdown_id')]][$row[csf('prod_id')]][$row[csf('batch_id')]][$row[csf('body_part_id')]][$row[csf('fabric_description_id')]][$row[csf('dia_width_type')]]=$row[csf('challan_no')];
			}
		}
		//var_dump($recChallan_arr);
		$rate_data_string=$lib_rate_id=""; $mainProcess_arr=array(); $fabric_color_arr=array();
		?>
	<div style="width:100%;">
		<table align="center" cellspacing="0" width="1350"  border="1" rules="all" class="rpt_table" >
            <thead bgcolor="#dddddd" align="center" style="font-size:12px"> 
                <th width="30">SL</th>
                <th width="55">Challan no</th>
                <th width="65"> Delv. Date</th>
                <th width="75">Job no</th>
                <th width="80">Order</th> 
                <th width="70">Buyer  & <br> Style</th>
                <th width="180">Fabric Des.</th>
                <th width="60">D.W Type</th>
                <th width="70">Color Range</th>
                <th width="60">Color</th>
				<th width="60">Shade Percentage</th>
                <th width="100">A.Process</th>
                <th width="30">Roll</th>
                <th width="60">Bill Qty</th>
                <th width="30">UOM</th>
                <th width="30">Rate (Main)</th>
                <th width="30">Rate (Add)</th>
                <th width="60">Amount</th>
                <th width="50">Currency</th>
                <th>Remarks</th>
            </thead>
		 <?
     		
			//here
			$sql_result =sql_select("select id, delivery_id, delivery_date, challan_no, order_id, item_id, batch_id,color_range_id, body_part_id, febric_description_id, dia_width_type, color_id, packing_qnty, delivery_qty, rate, add_rate, amount, remarks, currency_id, process_id,shade_percentage, add_process, add_process_name, rate_data_string, lib_rate_id from subcon_inbound_bill_dtls  where mst_id='$mst_id' and status_active=1 and is_deleted=0 order by challan_no ASC"); 

			$i=1; $j=1; $challan_arr=array();
			foreach($sql_result as $row)
			{
				if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
				
				if (!in_array($row[csf('challan_no')],$challan_arr) )
				{
					if($j!=1)
					{ 
						?>
                        <tr class="tbl_bottom" bgcolor="#C0DCC0" style="font-size:12px"> 
                            <td align="right" colspan="12"><strong>Challan Total:</strong></td>
                            <td align="right"><strong><? echo $sub_packing_qty; ?>&nbsp;</strong></td>
                            <td align="right"><strong><? echo number_format($sub_delivery_qty,2,'.',','); ?>&nbsp;</strong></td>
                            <td>&nbsp;</td>
                            <td>&nbsp;</td>
                            <td>&nbsp;</td>
                            <td align="right"><strong><? echo number_format($sub_amount,2,'.',','); ?>&nbsp;</strong></td>
                            <td>&nbsp;</td>
                            <td>&nbsp;</td>
                        </tr>
						<?
						unset($sub_packing_qty);
						unset($sub_delivery_qty);
						unset($sub_amount);
					}
					$challan_arr[]=$row[csf("challan_no")];
					$j++;
				}
				$process=explode(',',$row[csf("add_process")]);
				$add_process="";
				foreach($process as $inf)
				{
					if($add_process=="") $add_process=$conversion_cost_head_array[$inf]; else $add_process.=", ".$conversion_cost_head_array[$inf];
				}
				
				if($dataArray[0][csf('party_source')]==2) $item_all= explode(',',$batch_array[$row[csf('item_id')]]['item_description']);
				else if($dataArray[0][csf('party_source')]==1) $item_all= explode(',',$row[csf('item_id')]);
				
				$item_name="";
				foreach($item_all as $inf)
				{
					if($dataArray[0][csf('party_source')]==2)
					{
						if($item_name=="") $item_name=$inf; else $item_name.=", ".$inf;
					}
					else if($dataArray[0][csf('party_source')]==1)
					{
						if($item_name=="") $item_name=$prod_dtls_arr[$inf]; else $item_name.=", ".$prod_dtls_arr[$inf];
					}
				}
				$exrate_data_string=explode("#",$row[csf("rate_data_string")]);
				foreach($exrate_data_string as $process_data)
				{
					$exrate_data=explode("__",$process_data);
					$lib_id=$exrate_data[0];
					$librate=$exrate_data[1];
					$fabric_color_arr[$item_name][$color_library[$row[csf('color_id')]]][$process_ids_arr[$lib_id]]+=$librate;
					$mainProcess_arr[$process_ids_arr[$lib_id]]=$process_ids_arr[$lib_id];
				}
				
				$rec_challan="";
				$rec_challan=$recChallan_arr[$row[csf('challan_no')]][$row[csf('order_id')]][$row[csf('item_id')]][$row[csf('batch_id')]][$row[csf('body_part_id')]][$row[csf('febric_description_id')]][$row[csf('dia_width_type')]];
			?>
				<tr bgcolor="<? echo $bgcolor; ?>" style="font-size:12px"> 
					<td><? echo $i; ?></td>
					<td align="center" style="word-break:break-all"><? echo $row[csf('challan_no')]; ?></td>
					<td style="word-break:break-all"><? echo change_date_format($row[csf('delivery_date')]); ?></td>
					<td style="word-break:break-all"><? echo $job_no; ?></td>
					<td style="word-break:break-all"><? echo $order_array[$row[csf('order_id')]]['order_no']; ?></td>
					<td align="center" style="word-break:break-all"><? echo $order_array[$row[csf('order_id')]]['cust_buyer'].'<br>'.$order_array[$row[csf('order_id')]]['cust_style_ref']; ?></td>
					<td style="word-break:break-all"><? echo $item_name; ?></td>
					<td style="word-break:break-all"><? echo $fabric_typee[$row[csf('dia_width_type')]]; ?></td>
					<td style="word-break:break-all"><? echo $color_range[$row[csf('color_range_id')]];?></td>
					<td style="word-break:break-all"><? echo $color_library[$row[csf('color_id')]]; ?></td>
					<td style="word-break:break-all"><? echo $row[csf('shade_percentage')]; ?></td>
					<td style="word-break:break-all"><? echo $row[csf('add_process_name')];//$add_process; ?></td>
					<td align="right"><? echo $row[csf('packing_qnty')]; ?>&nbsp;</td>
					<td align="right"><? echo number_format($row[csf('delivery_qty')],2,'.',','); ?>&nbsp;</td>
					<td style="word-break:break-all"><? echo $unit_of_measurement[12]; ?></td>
					<td align="right"><? echo number_format($row[csf('rate')],4,'.',','); ?>&nbsp;</td>
					<td align="right"><? echo number_format($row[csf('add_rate')],4,'.',','); ?>&nbsp;</td>
					<td align="right"><? echo number_format($row[csf('amount')],4,'.',','); ?>&nbsp;</td>
					<td align="center" style="word-break:break-all"><? echo $currency[$row[csf('currency_id')]]; ?></td>
					<td style="word-break:break-all"><? echo $row[csf('remarks')]; ?></td>
					<? 
					$carrency_id=$row[csf('currency_id')];
					if($carrency_id==1) $paysa_sent="Paisa"; else if($carrency_id==2) $paysa_sent="CENTS";
					?>
				</tr>
				<?php
				$i++;
				
				$sub_packing_qty+=$row[csf('packing_qnty')];
				$sub_delivery_qty+=$row[csf('delivery_qty')];
				$sub_amount += $row[csf('amount')];
				
				$tot_packing_qty+=$row[csf('packing_qnty')];
				$tot_delivery_qty+=$row[csf('delivery_qty')];
				$total_amount += $row[csf('amount')];
			}
			//	discount,upcharge
			?>
            <tr class="tbl_bottom" bgcolor="#C0DCC0" style="font-size:12px"> 
                <td align="right" colspan="12"><strong>Challan Total:</strong></td>
                <td align="right"><strong><? echo $sub_packing_qty; ?>&nbsp;</strong></td>
                <td align="right"><strong><? echo number_format($sub_delivery_qty,2,'.',','); ?>&nbsp;</strong></td>
                <td>&nbsp;</td>
                <td>&nbsp;</td>
                <td>&nbsp;</td>
                <td align="right"><strong><? echo number_format($sub_amount,4,'.',','); ?>&nbsp;</strong></td>
                <td>&nbsp;</td>
                <td>&nbsp;</td>
            </tr>
            
        	<tr bgcolor="#FFFFAA" style="font-size:12px"> 
                <td align="right" colspan="12"><strong>Grand Total</strong></td>
                <td align="right"><strong><? echo $tot_packing_qty; ?>&nbsp;</strong></td>
                <td align="right"><strong><? echo number_format($tot_delivery_qty,2,'.',','); ?>&nbsp;</strong></td>
                <td>&nbsp;</td>
                <td>&nbsp;</td>
                <td>&nbsp;</td>
                <td align="right"><strong><? echo $format_total_amount=number_format($total_amount,4,'.',','); ?>&nbsp;</strong></td>
                <td>&nbsp;</td>
                <td>&nbsp;</td>
			</tr>
            <tr bgcolor="#FFFFAA" style="font-size:12px"> 
                <td align="right" colspan="12"><strong>Upcharge</strong></td>
                <td align="right"><strong><? //echo $tot_packing_qty; ?>&nbsp;</strong></td>
                <td align="right"><strong><? //echo number_format($tot_delivery_qty,2,'.',','); ?>&nbsp;</strong></td>
                <td>&nbsp;</td>
                <td>&nbsp;</td>
                <td>&nbsp;</td>
                <td align="right"><strong><? echo  number_format($upcharge,4,'.',','); ?>&nbsp;</strong></td>
                <td>&nbsp;</td>
                <td>&nbsp;</td>
			</tr>
            <tr bgcolor="#FFFFAA" style="font-size:12px"> 
                <td align="right" colspan="12"><strong>Discount</strong></td>
                <td align="right"><strong><? //echo $tot_packing_qty; ?>&nbsp;</strong></td>
                <td align="right"><strong><? //echo number_format($tot_delivery_qty,2,'.',','); ?>&nbsp;</strong></td>
                <td>&nbsp;</td>
                <td>&nbsp;</td>
                <td>&nbsp;</td>
                <td align="right"><strong><? echo  number_format($discount,4,'.',','); ?>&nbsp;</strong></td>
                <td>&nbsp;</td>
                <td>&nbsp;</td>
			</tr>
             <tr bgcolor="#FFFFAA" style="font-size:12px"> 
                <td align="right" colspan="12"><strong>Net Total</strong></td>
                <td align="right"><strong><? //echo $tot_packing_qty; ?>&nbsp;</strong></td>
                <td align="right"><strong><? //echo number_format($tot_delivery_qty,2,'.',','); ?>&nbsp;</strong></td>
                <td>&nbsp;</td>
                <td>&nbsp;</td>
                <td>&nbsp;</td>
                <td align="right"><strong><?
				$net_total_amount=($total_amount+$upcharge)-$discount;
				 echo  number_format($net_total_amount,4,'.',','); ?>&nbsp;</strong></td>
                <td>&nbsp;</td>
                <td>&nbsp;</td>
			</tr>
           <tr>
               <td colspan="20" align="left"><b>In Word: <? echo number_to_words(number_format($net_total_amount,4),$currency[$carrency_id],$paysa_sent); ?></b></td>
           </tr>
        </table>
        <table width="930" align="center" > 
        	<tr><td colspan="2">&nbsp;</td> </tr>
            <tr><td colspan="2" align="center"><b>TERMS & CONDITION</b></td> </tr>
        <?
			$bill_no=$dataArray[0][csf('bill_no')];
			$sql_terms="Select id,terms from subcon_terms_condition where entry_form=2 and bill_no='$bill_no' ";
			$result_sql_terms =sql_select($sql_terms);
			$i=1;
			if(count($result_sql_terms)>0)
			{
				foreach($result_sql_terms as $rows)
				{
					if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
				?>
					<tr bgcolor="<? echo $bgcolor; ?>"> 
						<td width="30"><? echo $i; ?></td>
						<td style="word-break:break-all"><? echo $rows[csf('terms')]; ?></td>
					</tr>
				<?
				$i++;
				}
			}
			?>
        </table>
        <br>
		 <? echo signature_table(48, $data[0], "930px"); ?>
   </div>
   </div>
	<?
    exit();
}
if($action=="fabric_finishing_print") 
{
	
	
    extract($_REQUEST);
	$data=explode('*',$data);
	
	$inhouse_bill_from=$data[6];
	//  echo $hidd_inhouse_bill_from.'DDDDDDDD';

	$imge_arr=return_library_array( "select master_tble_id,image_location from common_photo_library where form_name='company_details' and file_type=1",'master_tble_id','image_location');
	$company_library=return_library_array( "select id, company_name from lib_company", "id", "company_name");
	$party_library=return_library_array( "select id,buyer_name from lib_buyer", "id","buyer_name");
	$color_library=return_library_array( "select id,color_name from  lib_color where status_active=1 and is_deleted=0", "id","color_name");
	$color_id_arr=return_library_array( "select id, color_id from subcon_delivery_dtls",'id','color_id');
	$inv_item_arr=return_library_array( "select id,material_description from sub_material_dtls",'id','material_description');
	$prod_item_arr=return_library_array( "select id,fabric_description from subcon_production_dtls",'id','fabric_description');
	$prod_process_arr=return_library_array( "select cons_comp_id, process from subcon_production_dtls",'cons_comp_id','process');
	$prod_dtls_arr=return_library_array( "select id, product_name_details from  product_details_master",'id','product_name_details');
	$location_arr=return_library_array("select id,location_name from lib_location", "id","location_name");
	$batch_arr=return_library_array( "select id, batch_no from pro_batch_create_mst",'id','batch_no');
	
	
	$sql_mst="Select id, bill_no, bill_date, location_id, party_id, party_source, party_location_id, bill_for,remarks, terms_and_condition from subcon_inbound_bill_mst where company_id=$data[0] and id='$data[1]' and status_active=1 and is_deleted=0";
	$dataArray=sql_select($sql_mst);
	$location_id=$dataArray[0][csf('location_id')];
	$sql_com_loc="select b.id,b.location_name,b.address from lib_location b,lib_company a where a.id=b.company_id and b.company_id=$data[0] and b.id=$location_id and a.status_active=1";
	$dataArray_loc=sql_select($sql_com_loc);
	foreach($dataArray_loc as $row)
	{
		$loc_address=$row[csf('address')];
	}

	?>
    <div style="width:1130px;" align="center">
	<? if($data[4]==1)
	{
	?>
	<style>
		@media print {
			table tr th,table tr td{ font-size: 20px !important; }
		}
	</style>
	<? } ?>
    <table width="900" cellpadding="0" cellspacing="0" align="center">
        <tr>
            <td width="100" align="right"> 
            	<img src='../../<? echo $imge_arr[str_replace("'","",$data[0])]; ?>' height='70' width='100' />
            </td>
            <td>
                <table width="800" cellspacing="0" align="center">
                    <tr>
                    	<td align="center" style="font-size:20px"><strong ><? echo $company_library[$data[0]]; ?></strong></td>
                    </tr>
                    <tr>
                        <td align="center"  style="font-size:16px"><strong>Unit : <? echo $location_arr[$dataArray[0][csf('location_id')]]; ?></strong></td>
                    </tr>
                    <tr class="form_caption">
                        <td  align="center" style="font-size:14px"><? echo $loc_address;//show_company($data[0],'',''); ?></td>  
                    </tr>
                    <tr>
                    	<td align="center" style="font-size:18px"><strong><? echo $data[3]; ?></strong></td>
                    </tr>
                </table>
            </td>
        </tr>
    </table> 
    <table width="930" cellspacing="0" align="center" border="0">   
    	  <tr><td colspan="6" align="center"><hr></hr></td></tr>
             <tr>
			 <?
			 	if($dataArray[0][csf('party_source')]==2)
				{
					$party_add=$dataArray[0][csf('party_id')];
					$nameArray=sql_select( "select address_1, web_site, buyer_email, country_id from  lib_buyer where id=$party_add"); 
					foreach ($nameArray as $result)
					{ 
                    	$address="";
						if($result!="") $address=$result[csf('address_1')];
					}
					$party_details=$party_library[$party_add].'<br>'.$address;
				}
				else if($dataArray[0][csf('party_source')]==1)
				{
					$party_details=$company_library[$dataArray[0][csf('party_id')]];
				}
			 ?>
                <td width="300" rowspan="4" valign="top" colspan="2"><strong>Party :<? echo $party_details; ?></strong></td>
                <td width="130"><strong>Party Location: </strong></td><td width="175px"> <? echo $location_arr[$dataArray[0][csf('party_location_id')]]; ?></td>
                <td width="130"><strong>Bill No :</strong></td> <td width="175"><strong><? echo $dataArray[0][csf('bill_no')]; ?></strong></td>
            </tr>
             <tr>
             	<td><strong>Bill Date: </strong></td><td> <? echo change_date_format($dataArray[0][csf('bill_date')]); ?></td>
                <td><strong>Source :</strong></td> <td><? echo $knitting_source[$dataArray[0][csf('party_source')]]; ?></td>
            </tr>
            <tr>
                <td><strong>Bill For :</strong></td> <td><? echo $bill_for[$dataArray[0][csf('bill_for')]]; ?></td>
				<td><strong>Remarks :</strong></td> <td><? echo $dataArray[0][csf('remarks')]; ?></td>
            </tr>
        </table>
        <br>
        <?
		$process_ids_arr=array();
		$sql_rate="select id, process_id, in_house_rate, uom_id, rate_type_id, customer_rate, buyer_id, status_active from lib_subcon_charge where status_active!=0 and is_deleted=0 and rate_type_id in (3,4,7,8) and process_type_id=1 order by id Desc";
		$sql_rate_res=sql_select($sql_rate);
		foreach($sql_rate_res as $rrow)
		{
			$process_ids_arr[$rrow[csf('id')]]=$rrow[csf('process_id')];
		}
		unset($sql_rate_res);
		
		$batch_array=array(); $order_array=array();
		$grey_color_array=array();
		$grey_sql="Select a.color_id, b.fabric_from, b.po_id, b.id, b.item_description from pro_batch_create_mst a, pro_batch_create_dtls b where a.id=b.mst_id and b.status_active=1 and b.is_deleted=0";
		$grey_sql_result =sql_select($grey_sql);
		foreach($grey_sql_result as $row)
		{
			//$batch_array[$row[csf('id')]]=$row[csf('fabric_from')];
			$batch_array[$row[csf('id')]]['color']=$row[csf('color_id')];
			$batch_array[$row[csf('id')]]['item_description']=$row[csf('item_description')];
		}	
		$mst_id=$dataArray[0][csf('id')]; //Main Query
		$sql_result =sql_select("select id, delivery_id, delivery_date, challan_no, order_id, item_id, batch_id, body_part_id, febric_description_id, dia_width_type, color_id, color_range_id, packing_qnty, delivery_qty, rate, add_rate, amount, remarks, currency_id, process_id, add_process, add_process_name, rate_data_string, lib_rate_id from subcon_inbound_bill_dtls  where mst_id='$mst_id' and status_active=1 and is_deleted=0 order by challan_no"); 
		foreach($sql_result as $row)
		{
		 $order_idArr[$row[csf('order_id')]]=$row[csf('order_id')];
		}
				
		
		if($dataArray[0][csf('party_source')]==2)
		{
			$order_sql="select id, job_no_mst, order_no, order_uom, cust_buyer, cust_style_ref from subcon_ord_dtls where status_active=1 and is_deleted=0";
			$order_sql_result =sql_select($order_sql);
			foreach($order_sql_result as $row)
			{
				$order_array[$row[csf('id')]]['job_no']=$row[csf('job_no_mst')];
				$order_array[$row[csf('id')]]['order_no']=$row[csf('order_no')];
				$order_array[$row[csf('id')]]['order_uom']=$row[csf('order_uom')];
				$order_array[$row[csf('id')]]['cust_buyer']=$row[csf('cust_buyer')];
				$order_array[$row[csf('id')]]['cust_style_ref']=$row[csf('cust_style_ref')];
			}
		}
		else if($dataArray[0][csf('party_source')]==1)
		{
			$order_sql="select a.job_no, a.buyer_name, a.style_ref_no, a.order_uom, b.id, b.po_number from wo_po_details_master a, wo_po_break_down b where a.job_no=b.job_no_mst and a.status_active!=0 and a.is_deleted=0 and b.status_active!=0 and b.is_deleted=0";
			$order_sql_result =sql_select($order_sql);
			foreach($order_sql_result as $row)
			{
				$order_array[$row[csf('id')]]['job_no']=$row[csf('job_no')];
				$order_array[$row[csf('id')]]['order_no']=$row[csf('po_number')];
				$order_array[$row[csf('id')]]['order_uom']=$row[csf('order_uom')];
				$order_array[$row[csf('id')]]['cust_buyer']=$party_library[$row[csf('buyer_name')]];
				$order_array[$row[csf('id')]]['cust_style_ref']=$row[csf('style_ref_no')];
			}
			$recChallan_arr=array();
			$recQty_arr=array();
			
			/* $rec_challa_sql="SELECT a.recv_number_prefix_num, a.challan_no, b.batch_id, b.prod_id, b.body_part_id, b.fabric_description_id, b.dia_width_type, c.po_breakdown_id,sum(c.quantity) as rec_qnty FROM inv_receive_master a, pro_finish_fabric_rcv_dtls b, order_wise_pro_details c, pro_batch_create_mst d WHERE a.id=b.mst_id and b.id=c.dtls_id and d.id=b.batch_id and c.trans_type=1 and c.entry_form in (7,37,68) and c.trans_id!=0 and a.entry_form in (7,37,68) AND a.knitting_source=1 AND a.company_id='".$dataArray[0][csf('party_id')]."' AND a.location_id='".$dataArray[0][csf('location_id')]."' AND a.knitting_company=$data[0] and a.receive_basis in(2,4,5,9,11) and b.trans_id!=0  and a.item_category=2 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0  and c.po_breakdown_id in(".implode(",",$order_idArr).") group by a.id, a.recv_number_prefix_num, a.challan_no, b.batch_id, b.prod_id, b.body_part_id, b.fabric_description_id, b.dia_width_type, c.po_breakdown_id order by a.recv_number_prefix_num DESC";
			$rec_challa_sql_res=sql_select($rec_challa_sql);
			foreach($rec_challa_sql_res as $row)
			{
				$recChallan_arr[$row[csf('recv_number_prefix_num')]][$row[csf('prod_id')]][$row[csf('batch_id')]][$row[csf('body_part_id')]][$row[csf('fabric_description_id')]][$row[csf('dia_width_type')]]=$row[csf('challan_no')];
				$recQty_arr[$row[csf('recv_number_prefix_num')]][$row[csf('prod_id')]][$row[csf('batch_id')]][$row[csf('body_part_id')]][$row[csf('fabric_description_id')]][$row[csf('dia_width_type')]]=$row[csf('rec_qnty')];
			}*/
				if($inhouse_bill_from==2) //Bill from delivery  and a.sys_number='HAL-FDSR-22-00423'  AND c.location_id='".$dataArray[0][csf('location_id')]."'
				{
						     $sql_rec="SELECT a.id, a.entry_form,c.recv_number,c.receive_basis,a.sys_number as sys_number, a.sys_number_prefix_num as recv_number_prefix_num, '' as challan_no, a.delevery_date as receive_date, c.booking_id as bookingno,b.batch_id, b.product_id as prod_id, b.bodypart_id as body_part_id, b.determination_id as fabric_description_id, b.color_id, b.width_type as dia_width_type,sum(b.current_delivery) as rec_qnty,sum(e.production_qty) as production_qty, sum(f.grey_used_qty) as grey_used_qty, sum(e.roll_no) as carton_roll, f.po_breakdown_id as po_breakdown_id, d.booking_no_id, d.booking_no
							FROM pro_grey_prod_delivery_mst a, pro_grey_prod_delivery_dtls b, inv_receive_master c, pro_batch_create_mst d, pro_finish_fabric_rcv_dtls e, order_wise_pro_details f
							WHERE a.id=b.mst_id and b.grey_sys_id=c.id and d.id=b.batch_id and c.id=e.mst_id and e.id=f.dtls_id and e.id=b.sys_dtls_id and b.order_id=f.po_breakdown_id 
							
							and a.entry_form in (54,67)   AND c.knitting_source=1 AND c.company_id='".$dataArray[0][csf('party_id')]."' AND c.knitting_company=$data[0] and c.receive_basis in (0,2,4,5,9,11) and c.item_category=2 and c.entry_form in (7,66) and b.current_delivery>0  and f.entry_form in (7,66)
							and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and d.batch_against in (0,1,2,3)   and f.po_breakdown_id in(".implode(",",$order_idArr).")
							group by a.id, a.entry_form,c.recv_number, c.receive_basis,a.sys_number, a.sys_number_prefix_num, a.delevery_date, c.booking_id, a.insert_date, b.batch_id, b.product_id, b.bodypart_id, b.determination_id, b.color_id, b.width_type, f.po_breakdown_id, d.booking_no_id, d.booking_no order by a.sys_number_prefix_num DESC";
						$sql_rec_result =sql_select($sql_rec);	
						foreach($sql_rec_result as $row)
						{
							$recQty_arr[$row[csf('recv_number_prefix_num')]][$row[csf('prod_id')]][$row[csf('batch_id')]][$row[csf('body_part_id')]][$row[csf('fabric_description_id')]][$row[csf('dia_width_type')]][$row[csf('po_breakdown_id')]]=$row[csf('rec_qnty')];
						}
					//print_r($recQty_arr);
				}
				else
				{
					$sql_rec="SELECT a.id, a.entry_form, a.recv_number,a.receive_basis,a.recv_number as sys_number, a.recv_number_prefix_num, a.challan_no, a.receive_date, a.booking_id as bookingno, b.batch_id, b.prod_id, b.body_part_id,b.process_id, b.fabric_description_id, b.color_id, b.dia_width_type, sum(c.quantity) as rec_qnty, sum(b.no_of_roll) as carton_roll, c.po_breakdown_id, d.booking_no_id, d.booking_no
							FROM inv_receive_master a, pro_finish_fabric_rcv_dtls b, order_wise_pro_details c, pro_batch_create_mst d
							WHERE a.id=b.mst_id and b.id=c.dtls_id and d.id=b.batch_id and c.trans_type=1 and c.entry_form in (7,37,66,68) and c.trans_id!=0 and a.entry_form in (7,37,66,68) AND a.knitting_source=1   and c.quantity>0  AND a.company_id='".$dataArray[0][csf('party_id')]."' AND a.location_id='".$dataArray[0][csf('location_id')]."' AND a.knitting_company=$data[0]  and a.receive_basis in (2,4,5,9,11) and a.item_category=2 
							and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and d.batch_against in (0,1,3)   
							group by a.id, a.entry_form,a.recv_number, a.receive_basis, a.recv_number,a.recv_number_prefix_num, a.challan_no, a.receive_date, a.booking_id, a.insert_date, b.batch_id, b.prod_id, b.body_part_id, b.fabric_description_id, b.color_id, b.dia_width_type, c.po_breakdown_id,b.process_id, d.booking_no_id, d.booking_no order by a.recv_number_prefix_num DESC";
							$sql_rec_result =sql_select($sql_rec);	
						foreach($sql_rec_result as $row)
						{
							$recQty_arr[$row[csf('recv_number_prefix_num')]][$row[csf('prod_id')]][$row[csf('batch_id')]][$row[csf('body_part_id')]][$row[csf('fabric_description_id')]][$row[csf('dia_width_type')]]=$row[csf('rec_qnty')];
						}
					
				}
		}
		
		 $rate_data_string=$lib_rate_id=""; $mainProcess_arr=array(); $fabric_color_arr=array();
		 if($data[4]==1)
		 {
			?>
			<div style="width:100%;">
			<table align="center" cellspacing="0" width="1530"  border="1" rules="all" class="rpt_table" >
				<thead bgcolor="#dddddd" align="center" style="font-size:14px"> 
					<th width="20">SL</th>
					<th width="75">Challan & <br> Delv. Date</th>
					<th width="50">Rec. Challan</th>
					<th width="90">Batch No</th>
					<th width="90">Job No</th>
					<th width="100">Order</th> 
					<th width="100">Buyer  & <br> Style</th>
					<th width="150">Fabric Des.</th>
					<th width="80">D.W Type</th>
					<th width="60">Color</th>
					<th width="100">Color Range</th>
					<th width="100">A.Process</th>
					<th width="30">Roll</th>
					<th width="60">Bill Qty</th>
					<th width="60">Rec Qty</th>
					<th width="30">UOM</th>
					<th width="30">Rate (Main)</th>
					<th width="30">Rate (Add)</th>
					<th width="60">Amount</th>
					<th width="50">Currency</th>
					<th>Remarks</th>
				</thead>
			<?
				$i=1;
				
				foreach($sql_result as $row)
				{
					if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
					//$rate_data_string.=$row[csf("rate_data_string")].',';
					//$lib_rate_id.=$row[csf("lib_rate_id")].',';
					
					$process=explode(',',$row[csf("add_process")]);
					$add_process="";
					foreach($process as $inf)
					{
						if($add_process=="") $add_process=$conversion_cost_head_array[$inf]; else $add_process.=", ".$conversion_cost_head_array[$inf];
					}
					
					if($dataArray[0][csf('party_source')]==2)
					{
						$item_all= explode(',',$batch_array[$row[csf('item_id')]]['item_description']);
					}
					else if($dataArray[0][csf('party_source')]==1)
					{
						$item_all= explode(',',$row[csf('item_id')]);
					}
					$item_name="";
					foreach($item_all as $inf)
					{
						if($dataArray[0][csf('party_source')]==2)
						{
							if($item_name=="") $item_name=$inf; else $item_name.=", ".$inf;
						}
						else if($dataArray[0][csf('party_source')]==1)
						{
							if($item_name=="") $item_name=$prod_dtls_arr[$inf]; else $item_name.=", ".$prod_dtls_arr[$inf];
						}
					}
					$exrate_data_string=explode("#",$row[csf("rate_data_string")]);
					foreach($exrate_data_string as $process_data)
					{
						$exrate_data=explode("__",$process_data);
						$lib_id=$exrate_data[0];
						$librate=$exrate_data[1];
						$fabric_color_arr[$item_name][$color_library[$row[csf('color_id')]]][$process_ids_arr[$lib_id]]+=$librate;
						$mainProcess_arr[$process_ids_arr[$lib_id]]=$process_ids_arr[$lib_id];
					}
					
					$rec_challan="";
					$rec_challan=$recChallan_arr[$row[csf('challan_no')]][$row[csf('item_id')]][$row[csf('batch_id')]][$row[csf('body_part_id')]][$row[csf('febric_description_id')]][$row[csf('dia_width_type')]];
					$rec_qty="";
					$rec_qty=$recQty_arr[$row[csf('challan_no')]][$row[csf('item_id')]][$row[csf('batch_id')]][$row[csf('body_part_id')]][$row[csf('febric_description_id')]][$row[csf('dia_width_type')]];
				?>
					<tr bgcolor="<? echo $bgcolor; ?>" style="font-size:14px"> 
						<td><? echo $i; ?></td>
						<td align="center" style="word-break:break-all"><? echo $row[csf('challan_no')].'<br>'.change_date_format($row[csf('delivery_date')]); ?></td>
						<td style="word-break:break-all"><? echo $rec_challan; ?></td>
						<td style="word-break:break-all"><? echo $batch_arr[$row[csf('batch_id')]]; ?></td>
						<td style="word-break:break-all"><? echo $order_array[$row[csf('order_id')]]['job_no']; ?></td>
						<td style="word-break:break-all"><? echo $order_array[$row[csf('order_id')]]['order_no']; ?></td>
						<td align="center" style="word-break:break-all"><? echo $order_array[$row[csf('order_id')]]['cust_buyer'].'<br>'.$order_array[$row[csf('order_id')]]['cust_style_ref']; ?></td>
						<td style="word-break:break-all"><? echo $item_name; ?></td>
						<td style="word-break:break-all"><? echo $fabric_typee[$row[csf('dia_width_type')]]; ?></td>
						<td style="word-break:break-all"><? echo $color_library[$row[csf('color_id')]]; ?></td>
						<td style="word-break:break-all"><? echo $color_range[$row[csf('color_range_id')]]; ?></td>
						<td style="word-break:break-all"><? echo $row[csf('add_process_name')];//$add_process; ?></td>
						<td align="right"><? echo $row[csf('packing_qnty')]; $tot_packing_qty+=$row[csf('packing_qnty')]; ?>&nbsp;</td>
						<td align="right"><? echo number_format($row[csf('delivery_qty')],2,'.',','); $tot_delivery_qty+=$row[csf('delivery_qty')]; ?>&nbsp;</td>
						<td align="right"><? echo number_format($rec_qty,2,'.',','); $tot_rec_qty+=$rec_qty?>&nbsp;</td>
						<td><? echo $unit_of_measurement[12]; ?></td>
						<td align="right"><? echo number_format($row[csf('rate')],2,'.',','); ?>&nbsp;</td>
						<td align="right"><? echo number_format($row[csf('add_rate')],2,'.',','); ?>&nbsp;</td>
						<td align="right"><? echo number_format($row[csf('amount')],2,'.',',');  $total_amount += $row[csf('amount')]; ?>&nbsp;</td>

						<td style="word-break:break-all"><? echo $currency[$row[csf('currency_id')]]; ?></td>

						<td style="word-break:break-all"><? echo $row[csf('remarks')]; ?></td>
						<? 
						$carrency_id=$row[csf('currency_id')];
						if($carrency_id==1) $paysa_sent="Paisa"; else if($carrency_id==2) $paysa_sent="CENTS";
						?>
					</tr>
					<?php
					$i++;
				}
				?>
				<tr style="font-size:14px"> 
					<td align="right" colspan="12"><strong>Total</strong></td>
					<td align="right"><strong><? echo $tot_packing_qty; ?>&nbsp;</strong></td>
					<td align="right"><strong><? echo number_format($tot_delivery_qty,2,'.',','); ?>&nbsp;</strong></td>
					<td align="right"><strong><? echo number_format($tot_rec_qty,2,'.',','); ?>&nbsp;</strong></td>
					<td>&nbsp;</td>
					<td>&nbsp;</td>
					<td>&nbsp;</td>
					<td align="right"><strong><? echo $format_total_amount=number_format($total_amount,2,'.',','); ?>&nbsp;</strong></td>
					<td>&nbsp;</td>
					<td>&nbsp;</td>
				</tr>
			<tr>
				<td colspan="21" align="left"><b>In Word: <? echo number_to_words($format_total_amount,$currency[$carrency_id],$paysa_sent); ?></b></td>
			</tr>
			</table>
			<?
		 }
		 elseif($data[4]==2)
		 {
			 ?>
            <div style="width:100%;">
             <style type="text/css" media="all">
		    	.rpt_table tr th,.rpt_table tr td{ font-size: 20px !important; }
		    </style>
            <table align="center" cellspacing="0" width="1380"  border="1" rules="all" class="rpt_table">
                <thead bgcolor="#dddddd" align="center">
                    <th width="30">SL</th>
                    <th width="105" align="center">Sys. Challan & <br> Delv. Date</th>
                    <th width="50">Rec. Challan</th>
                    <th width="120" align="center">Job No</th>
                    <th width="130" align="center">Order</th> 
                    <th width="100" align="center">Buyer & <br> Style</th>
                    <th width="80" align="center">Color</th>
                    <th width="100" align="center">Color Range</th>
                    <th width="130" align="center">A.Process</th>
                    <th width="30" align="center">Roll</th>
                    <th width="70" align="center">Bill Qty</th>
                    <th width="50" align="center">Rate (Main)</th>
                    <th width="30" align="center">Rate (Add)</th>
                    <th width="70" align="center">Amount</th>
                    <th width="50" align="center">Currency</th>
                    <th>Remarks</th>
                </thead>
             <?
                $i=1;
                $mst_id=$dataArray[0][csf('id')];
                $sql_dtls ="select delivery_date, challan_no, order_id, color_id, color_range_id, currency_id, sum(packing_qnty) as packing_qnty, sum(delivery_qty) as delivery_qty, rate, add_rate, sum(amount) as amount, add_process, add_process_name, max(remarks) as remarks, rate_data_string, lib_rate_id from subcon_inbound_bill_dtls  where mst_id='$mst_id' and status_active=1 and is_deleted=0 group by delivery_date, challan_no, order_id, color_id, color_range_id, currency_id, rate, add_rate, add_process, add_process_name, rate_data_string, lib_rate_id order by delivery_date, challan_no, order_id, color_id, rate, add_process"; 
                //echo $sql_dtls; die;

                $sql_result =sql_select($sql_dtls);

                foreach($sql_result as $row)
                {
                    if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
					$process=explode(',',$row[csf("add_process")]);
					$add_process="";
					foreach($process as $inf)
					{
						if($add_process=="") $add_process=$conversion_cost_head_array[$inf]; else $add_process.=", ".$conversion_cost_head_array[$inf];
					}
					$rec_challan="";
					$rec_challan=$recChallan_arr[$row[csf('challan_no')]][$row[csf('order_id')]][$row[csf('item_id')]][$row[csf('batch_id')]][$row[csf('body_part_id')]][$row[csf('febric_description_id')]][$row[csf('dia_width_type')]];
					
                   ?>
                    <tr bgcolor="<? echo $bgcolor; ?>"> 
                        <td><? echo $i; ?></td>
                        <td align="center" style="word-break:break-all"><? echo $row[csf('challan_no')].'<br>'.change_date_format($row[csf('delivery_date')]); ?></td>
                        <td style="word-break:break-all"><? echo $rec_challan; ?></td>
                        <td style="word-break:break-all"><? echo $order_array[$row[csf('order_id')]]['job_no']; ?></td>
                        <td style="word-break:break-all"><? echo $order_array[$row[csf('order_id')]]['order_no']; ?></td>
                        <td align="center" style="word-break:break-all"><? echo $order_array[$row[csf('order_id')]]['cust_buyer'].'<br>'.$order_array[$row[csf('order_id')]]['cust_style_ref']; ?></td>
                        <td style="word-break:break-all"><? echo $color_library[$row[csf('color_id')]]; ?></td>
                        <td style="word-break:break-all"><? echo $color_range[$row[csf('color_range_id')]]; ?></td>
                        <td style="word-break:break-all"><? echo $row[csf('add_process_name')]; ?></td>
                        <td align="right"><? echo $row[csf('packing_qnty')]; $tot_packing_qty+=$row[csf('packing_qnty')]; ?>&nbsp;</td>
                        <td align="right"><? echo number_format($row[csf('delivery_qty')],2,'.',','); $tot_delivery_qty+=$row[csf('delivery_qty')]; ?>&nbsp;</td>
                        <td align="right"><? echo number_format($row[csf('rate')],2,'.',','); ?>&nbsp;</td>
                        <td align="right"><? echo number_format($row[csf('add_rate')],2,'.',','); ?>&nbsp;</td>
                        <td align="right"><? echo number_format($row[csf('amount')],2,'.',',');  $total_amount += $row[csf('amount')]; ?>&nbsp;</td>
                        <td align="center" style="word-break:break-all"><? echo $currency[$row[csf('currency_id')]]; ?></td>
                        <td style="word-break:break-all"><? echo $row[csf('remarks')]; ?></td>
                        <? 
                        $carrency_id=$row['currency_id'];
                        if($carrency_id==1){$paysa_sent="Paisa";} else if($carrency_id==2){$paysa_sent="CENTS";}
                       ?>
                    </tr>
                    <?
                    $i++;
                }
                ?>
                <tr> 
                    <td align="right" colspan="9"><strong>Total</strong></td>
                    <td align="right"><strong><? echo $tot_packing_qty; ?>&nbsp;</strong></td>
                    <td align="right"><strong><? echo number_format($tot_delivery_qty,2,'.',','); ?>&nbsp;</strong></td>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td align="right"><strong><? echo $format_total_amount=number_format($total_amount,2,'.',','); ?>&nbsp;</strong></td>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                </tr>
               <tr>
                   <td colspan="16" align="left"><b>In Word: <? echo number_to_words($format_total_amount,$currency[$carrency_id],$paysa_sent); ?></b></td>
               </tr>
            </table>
        <?			 
		 }
		 elseif($data[4]==3)
		 {
		 ?>
	<div style="width:100%;" align="center">
		<table align="center" cellspacing="0" width="1180"  border="1" rules="all" class="rpt_table" >
            <thead bgcolor="#dddddd" align="center" style="font-size:14px"> 
                <th width="30">SL</th>
                <th width="60">Challan & <br> Delv. Date</th>
                <th width="50">Rec. Challan</th>
                <th width="75">Job No</th> 
                <th width="80">Order</th> 
                <th width="70">Buyer  & <br> Style</th>
                <th width="180">Fabric Des.</th>
                <th width="60">D.W Type</th>
                <th width="60">Color</th>
                <th width="100">Color Range</th>
                <th width="100">A.Process</th>
                <th width="30">Roll</th>
                <th width="60">Bill Qty</th>
                <th width="30">UOM</th>
                <th width="30">Rate (Main)</th>
                <th width="30">Rate (Add)</th>
                <th width="60">Amount</th>
                <th width="50">Currency</th>
                <th>Remarks</th>
            </thead>
		 <?
     		$i=1;
			
			$sql_currency_result_usd=sql_select("SELECT conversion_rate from currency_conversion_rate WHERE con_date = (SELECT MAX(con_date) from currency_conversion_rate WHERE is_deleted=0 and status_active=1 and currency=2)");
			
			$mst_id=$dataArray[0][csf('id')];
			$sql_result =sql_select("select id, delivery_id, delivery_date, challan_no, order_id, item_id, batch_id, body_part_id, febric_description_id, dia_width_type, color_id, color_range_id, packing_qnty, delivery_qty, rate, add_rate, amount, remarks, currency_id, process_id, add_process, add_process_name, rate_data_string, lib_rate_id from subcon_inbound_bill_dtls  where mst_id='$mst_id' and status_active=1 and is_deleted=0 order by challan_no"); 
			foreach($sql_result as $row)
			{
				if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
				
				$process=explode(',',$row[csf("add_process")]);
				$add_process="";
				foreach($process as $inf)
				{
					if($add_process=="") $add_process=$conversion_cost_head_array[$inf]; else $add_process.=", ".$conversion_cost_head_array[$inf];
				}
                
				if($dataArray[0][csf('party_source')]==2)
				{
					$item_all= explode(',',$batch_array[$row[csf('item_id')]]['item_description']);
				}
				else if($dataArray[0][csf('party_source')]==1)
				{
					$item_all= explode(',',$row[csf('item_id')]);
				}
				$item_name="";
				foreach($item_all as $inf)
				{
					if($dataArray[0][csf('party_source')]==2)
					{
						if($item_name=="") $item_name=$inf; else $item_name.=", ".$inf;
					}
					else if($dataArray[0][csf('party_source')]==1)
					{
						if($item_name=="") $item_name=$prod_dtls_arr[$inf]; else $item_name.=", ".$prod_dtls_arr[$inf];
					}
				}
				$rec_challan="";
				$rec_challan=$recChallan_arr[$row[csf('challan_no')]][$row[csf('order_id')]][$row[csf('item_id')]][$row[csf('batch_id')]][$row[csf('body_part_id')]][$row[csf('febric_description_id')]][$row[csf('dia_width_type')]];
			?>
				<tr bgcolor="<? echo $bgcolor; ?>" style="font-size:14px"> 
                    <td><? echo $i; ?></td>
                    <td align="center" style="word-break:break-all"><? echo $row[csf('challan_no')].'<br>'.change_date_format($row[csf('delivery_date')]); ?></td>
                    <td style="word-break:break-all"><? echo $rec_challan; ?></td>
                    <td style="word-break:break-all"><? echo $order_array[$row[csf('order_id')]]['job_no']; ?></td>
                    <td style="word-break:break-all"><? echo $order_array[$row[csf('order_id')]]['order_no']; ?></td>
                    <td align="center" style="word-break:break-all"><? echo $order_array[$row[csf('order_id')]]['cust_buyer'].'<br>'.$order_array[$row[csf('order_id')]]['cust_style_ref']; ?></td>
                    <td style="word-break:break-all"><? echo $item_name; ?></td>
                    <td style="word-break:break-all"><? echo $fabric_typee[$row[csf('dia_width_type')]]; ?></td>
                    <td style="word-break:break-all"><? echo $color_library[$row[csf('color_id')]]; ?></td>
                    <td style="word-break:break-all"><? echo $color_range[$row[csf('color_range_id')]]; ?></td>
                    <td style="word-break:break-all"><? echo $row[csf('add_process_name')];//$add_process; ?></td>
                    <td align="right"><? echo $row[csf('packing_qnty')]; $tot_packing_qty+=$row[csf('packing_qnty')]; ?>&nbsp;</td>
                    <td align="right"><? echo number_format($row[csf('delivery_qty')],2,'.',','); $tot_delivery_qty+=$row[csf('delivery_qty')]; ?>&nbsp;</td>
                    <td style="word-break:break-all"><? echo $unit_of_measurement[12]; ?></td>
                    <td align="right"><? echo number_format($row[csf('rate')],2,'.',','); ?>&nbsp;</td>
                    <td align="right"><? echo number_format($row[csf('add_rate')],2,'.',','); ?>&nbsp;</td>
                    <td align="right" style="word-break:break-all"><? echo number_format($row[csf('amount')]/$sql_currency_result_usd[0][csf('conversion_rate')],2,'.',',');  $total_amount += $row[csf('amount')]/$sql_currency_result_usd[0][csf('conversion_rate')]; ?>&nbsp;</td>

                    <td align="center" style="word-break:break-all"><? echo $currency[2]; ?></td>
                    <td style="word-break:break-all"><? echo $row[csf('remarks')]; ?></td>
                    <? 
					$carrency_id=$row[csf('currency_id')];
					if($carrency_id==1) $paysa_sent="Paisa"; else if($carrency_id==2) $paysa_sent="CENTS";
				    ?>
                </tr>
                <?php
                $i++;
			}
			?>
        	<tr style="font-size:14px"> 
                <td align="right" colspan="11"><strong>Total</strong></td>
                <td align="right"><strong><? echo $tot_packing_qty; ?>&nbsp;</strong></td>
                <td align="right"><strong><? echo number_format($tot_delivery_qty,2,'.',','); ?>&nbsp;</strong></td>
                <td>&nbsp;</td>
                <td>&nbsp;</td>
                <td>&nbsp;</td>
                <td align="right"><strong><? echo $format_total_amount=number_format($total_amount,2,'.',','); ?>&nbsp;</strong></td>
                <td>&nbsp;</td>
                <td>&nbsp;</td>
			</tr>
           <tr>
               <td colspan="19" align="left"><b>In Word: <? echo number_to_words($format_total_amount,$currency[2],$paysa_sent); ?></b></td>
           </tr>
        </table>
        <?
		 }
		 
		 if($data[5]==1)
		 {
			if($data[4]==1)
			{
				$process_ids_count=count($mainProcess_arr);
				
				$tblWidth=$process_ids_count*70;
				if($process_ids_count>0)
				{
					?>
					<br>
					<table width="<? echo $tblWidth+350; ?>" cellspacing="0" border="1" class="rpt_table" rules="all">   
						<thead>
							<th width="150">Fabric Description</th>
							<th width="100">Color</th>
							<?
								foreach($mainProcess_arr as $process_ids)
								{
									?>
										<th width="70"><? echo $conversion_cost_head_array[$process_ids]; ?></th>
									<?
								}
							?>
							<th>Total (TK)</th>
						 </thead>
						 <tbody>
							<? 
							foreach($fabric_color_arr as $cons_comp=>$consData)
							{
								foreach($consData as $colorName=>$processData)
								{
									?>
									<tr>
										<td style="word-break:break-all"><? echo $cons_comp; ?></td>
										<td style="word-break:break-all"><? echo $colorName; ?></td>
										<?
											$rowTotalTk=0;
											foreach($mainProcess_arr as $process_ids)
											{
												?>
													<td align="right"><? if($processData[$process_ids]!=0) echo number_format($processData[$process_ids],3); else echo ""; ?></td>
												<?
												$rowTotalTk+=$processData[$process_ids];
											}
										?>
										<td align="right"><? echo number_format($rowTotalTk,3); ?></td>
									</tr>
							<? } } ?>
						 </tbody>
					</table>
        <? } } }?>
        <table width="930" align="center" > 
        	<tr><td colspan="2">&nbsp;</td> </tr>
            <tr><td colspan="2" align="center"><b>TERMS & CONDITION</b></td> </tr>
        <?
			$bill_no=$dataArray[0][csf('bill_no')];
			$sql_terms="Select id,terms from subcon_terms_condition where entry_form=2 and bill_no='$bill_no' ";
			$result_sql_terms =sql_select($sql_terms);
			$i=1;
			if(count($result_sql_terms)>0)
			{
				foreach($result_sql_terms as $rows)
				{
					if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
				?>
					<tr bgcolor="<? echo $bgcolor; ?>"> 
						<td width="30"><? echo $i; ?></td>
						<td style="word-break:break-all"><? echo $rows[csf('terms')]; ?></td>
					</tr>
				<?
				$i++;
				}
			}
			?>
        </table>
        <br>
		 <?
            echo signature_table(48, $data[0], "930px");
         ?>
   </div>
   </div>
	<?
    exit();
}

if($action=="dyeing_finishin_bill_print")
{
	extract($_REQUEST);
	$data=explode('*',$data);

	$imge_arr=return_library_array( "select master_tble_id,image_location from common_photo_library where form_name='company_details' and file_type=1",'master_tble_id','image_location');
	$company_library=return_library_array( "select id, company_name from lib_company", "id", "company_name");
	$party_library=return_library_array( "select id,buyer_name from lib_buyer", "id","buyer_name");
	$color_library=return_library_array( "select id,color_name from  lib_color where status_active=1 and is_deleted=0", "id","color_name");
	$color_id_arr=return_library_array( "select id, color_id from subcon_delivery_dtls",'id','color_id');
	$inv_item_arr=return_library_array( "select id,material_description from sub_material_dtls",'id','material_description');
	$prod_item_arr=return_library_array( "select id,fabric_description from subcon_production_dtls",'id','fabric_description');
	$prod_process_arr=return_library_array( "select cons_comp_id, process from subcon_production_dtls",'cons_comp_id','process');
	$prod_dtls_arr=return_library_array( "select id, product_name_details from  product_details_master",'id','product_name_details');
	//$location_arr=return_library_array("select id,location_name from lib_location", "id","location_name");
	
	$sql_mst="Select id, bill_no, bill_date, location_id, party_id, party_source, upcharge,discount,party_location_id, bill_for, terms_and_condition from subcon_inbound_bill_mst where company_id=$data[0] and id='$data[1]' and status_active=1 and is_deleted=0";
	$dataArray=sql_select($sql_mst);
	$upcharge=$dataArray[0][csf('upcharge')];
	$discount=$dataArray[0][csf('discount')];

	$location_id=$dataArray[0][csf('location_id')];
	$sql_com_loc="select b.id,b.location_name,b.address from lib_location b,lib_company a where a.id=b.company_id and b.company_id=$data[0] and b.id=$location_id and a.status_active=1";
	$dataArray_loc=sql_select($sql_com_loc);
	foreach($dataArray_loc as $row)
	{
		$loc_address=$row[csf('address')];
		$location_arr[$row[csf('id')]]=$row[csf('location_name')];
	}
	
	?>
    <div style="width:1130px;" align="center">
    <table width="900" cellpadding="0" cellspacing="0" align="center">
        <tr>
            <td width="100" align="right"> 
            	<img  src='../../<? echo $imge_arr[str_replace("'","",$data[0])]; ?>' height='70' width='100' />
            </td>
            <td>
                <table width="800" cellspacing="0" align="center">
                    <tr>
                    	<td align="center" style="font-size:20px"><strong ><? echo $company_library[$data[0]]; ?></strong></td>
                    </tr>
                    <tr>
                        <td align="center"  style="font-size:16px"><strong>Unit : <? echo $location_arr[$dataArray[0][csf('location_id')]]; ?></strong></td>
                    </tr>
                    <tr class="form_caption">
                        <td  align="center" style="font-size:14px"><? echo $loc_address;//show_company($data[0],'',''); ?></td>  
                    </tr>
                    <tr>
                    	<td align="center" style="font-size:18px"><strong><? echo $data[3]; ?></strong></td>
                    </tr>
                </table>
            </td>
        </tr>
    </table> 
    <table width="930" cellspacing="0" align="center" border="0">   
    	  <tr><td colspan="6" align="center"></hr></td></tr>
             <tr>
			 <?
			 	if($dataArray[0][csf('party_source')]==2)
				{
					$party_add=$dataArray[0][csf('party_id')];
					$nameArray=sql_select( "select address_1, web_site, buyer_email, country_id from  lib_buyer where id=$party_add"); 
					foreach ($nameArray as $result)
					{ 
                    	$address="";
						if($result!="") $address=$result[csf('address_1')];//.'<br>'.$country_arr[$result['country_id']].'<br>'.$result['email'].'<br>'.$result['web_site']
					}
					$party_details=$party_library[$party_add].'<br>'.$address;
				}
				else if($dataArray[0][csf('party_source')]==1)
				{
					$party_details=$company_library[$dataArray[0][csf('party_id')]];
				}
			 ?>
                <td width="300" rowspan="4" valign="top" colspan="2"><strong>Party :<? echo $party_details; ?></strong></td>
                <td width="130"><strong>Party Location: </strong></td><td width="175px"> <? echo $location_arr[$dataArray[0][csf('party_location_id')]]; ?></td>
                <td width="130"><strong>Bill No :</strong></td> <td width="175"><? echo $dataArray[0][csf('bill_no')]; ?></td>
            </tr>
             <tr>
             	<td><strong>Bill Date: </strong></td><td> <? echo change_date_format($dataArray[0][csf('bill_date')]); ?></td>
                <td><strong>Source :</strong></td> <td><? echo $knitting_source[$dataArray[0][csf('party_source')]]; ?></td>
            </tr>
            <tr>
                <td><strong>Bill For :</strong></td> <td><? echo $bill_for[$dataArray[0][csf('bill_for')]]; ?></td>
                <td>&nbsp;</td><td>&nbsp;</td>
                <td>&nbsp;</td><td>&nbsp;</td>
            </tr>
        </table>
        <br>
        <?
		$mst_id=$dataArray[0][csf('id')];
		$process_ids_arr=array();
		$sql_rate="select id, process_id, in_house_rate, uom_id, rate_type_id, customer_rate, buyer_id, status_active from lib_subcon_charge where status_active!=0 and is_deleted=0 and rate_type_id in (3,4,7,8) and process_type_id=1 order by id Desc";
		$sql_rate_res=sql_select($sql_rate);
		foreach($sql_rate_res as $rrow)
		{
			$process_ids_arr[$rrow[csf('id')]]=$rrow[csf('process_id')];
		}
		unset($sql_rate_res);
		
		$batch_array=array(); $order_array=array();
		$grey_color_array=array();
		//$grey_sql="Select a.color_id, a.color_range_id, b.fabric_from, b.po_id, a.id, b.item_description from pro_batch_create_mst a, pro_batch_create_dtls b where a.id=b.mst_id and b.status_active=1 and b.is_deleted=0";
		$grey_sql="Select a.color_id, a.color_range_id, b.fabric_from, b.po_id, b.id, b.item_description from pro_batch_create_mst a, pro_batch_create_dtls b,subcon_inbound_bill_dtls c where a.id=b.mst_id and c.batch_id=a.id and c.mst_id='$mst_id' and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0";
		$grey_sql_result =sql_select($grey_sql);
		foreach($grey_sql_result as $row)
		{
			$batch_array[$row[csf('id')]]['color_range']=$row[csf('color_range_id')];
			$batch_array[$row[csf('id')]]['color']=$row[csf('color_id')];
			$batch_array[$row[csf('id')]]['item_description']=$row[csf('item_description')];
		}	
		
		if($dataArray[0][csf('party_source')]==2)
		{
		//	$order_sql="select id, order_no, order_uom, cust_buyer, cust_style_ref from subcon_ord_dtls where status_active=1 and is_deleted=0";
			$order_sql="select b.id, b.order_no, b.order_uom, b.cust_buyer, b.cust_style_ref from subcon_ord_dtls b,subcon_inbound_bill_dtls c  where b.id=c.order_id and b.status_active=1 and b.is_deleted=0 and c.mst_id='$mst_id'";
			$order_sql_result =sql_select($order_sql);
			foreach($order_sql_result as $row)
			{
				$order_array[$row[csf('id')]]['order_no']=$row[csf('order_no')];
				$order_array[$row[csf('id')]]['order_uom']=$row[csf('order_uom')];
				$order_array[$row[csf('id')]]['cust_buyer']=$row[csf('cust_buyer')];
				$order_array[$row[csf('id')]]['cust_style_ref']=$row[csf('cust_style_ref')];
			}
		}
		else if($dataArray[0][csf('party_source')]==1)
		{
			//$order_sql="select a.buyer_name, a.style_ref_no, a.order_uom, b.id, b.po_number, b.grouping from wo_po_details_master a, wo_po_break_down b where a.job_no=b.job_no_mst and a.status_active!=0 and a.is_deleted=0 and b.status_active!=0 and b.is_deleted=0";
			$order_sql="select a.buyer_name, a.style_ref_no, a.order_uom, b.id, b.po_number, b.grouping from wo_po_details_master a, wo_po_break_down b,subcon_inbound_bill_dtls c  where a.job_no=b.job_no_mst  and b.id=c.order_id and a.status_active!=0 and a.is_deleted=0 and b.status_active!=0 and b.is_deleted=0 and c.mst_id='$mst_id'";
			$order_sql_result =sql_select($order_sql);
			foreach($order_sql_result as $row)
			{
				$order_array[$row[csf('id')]]['order_no']=$row[csf('po_number')];
				$order_array[$row[csf('id')]]['order_uom']=$row[csf('order_uom')];
				$order_array[$row[csf('id')]]['cust_buyer']=$party_library[$row[csf('buyer_name')]];
				$order_array[$row[csf('id')]]['cust_style_ref']=$row[csf('style_ref_no')];
				$order_array[$row[csf('id')]]['internal_ref']=$row[csf('grouping')];
			}
			$recChallan_arr=array();
			
			$rec_challa_sql="SELECT a.recv_number_prefix_num, a.challan_no, b.batch_id, b.prod_id, b.body_part_id, b.fabric_description_id, b.dia_width_type, c.po_breakdown_id
							FROM inv_receive_master a, pro_finish_fabric_rcv_dtls b, order_wise_pro_details c, pro_batch_create_mst d
							WHERE a.id=b.mst_id and b.id=c.dtls_id and d.id=b.batch_id and c.trans_type=1 and c.entry_form in (7,37) and c.trans_id!=0 and a.entry_form in (7,37) AND a.knitting_source=1 AND a.company_id='".$dataArray[0][csf('party_id')]."' AND a.location_id='".$dataArray[0][csf('location_id')]."' AND a.knitting_company=$data[0] and a.receive_basis in(4,5,9) and b.trans_id!=0  and a.item_category=2 
							and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 
							group by a.id, a.recv_number_prefix_num, a.challan_no, b.batch_id, b.prod_id, b.body_part_id, b.fabric_description_id, b.dia_width_type, c.po_breakdown_id order by a.recv_number_prefix_num DESC";
			$rec_challa_sql_res=sql_select($rec_challa_sql);
			foreach($rec_challa_sql_res as $row)
			{
				$recChallan_arr[$row[csf('recv_number_prefix_num')]][$row[csf('po_breakdown_id')]][$row[csf('prod_id')]][$row[csf('batch_id')]][$row[csf('body_part_id')]][$row[csf('fabric_description_id')]][$row[csf('dia_width_type')]]=$row[csf('challan_no')];
			}
		}
		//var_dump($recChallan_arr);
		$rate_data_string=$lib_rate_id=""; $mainProcess_arr=array(); $fabric_color_arr=array();
		?>
	<div style="width:100%;">
		<table align="center" cellspacing="0" width="1250"  border="1" rules="all" class="rpt_table" >
            <thead bgcolor="#dddddd" align="center" style="font-size:12px"> 
                <th width="30">SL</th>
                <th width="60">Challan & <br> Delv. Date</th>
                <th width="50">Rec. Challan</th>
                <th width="80">Internal Ref.</th>
                <th width="80">Order</th> 
                <th width="70">Buyer  & <br> Style</th>
                <th width="180">Fabric Des.</th>
                <th width="60">D.W Type</th>
                <th width="70">Color Range</th>
                <th width="60">Color</th>
                <th width="100">A.Process</th>
                <th width="30">Roll</th>
                <th width="60">Bill Qty</th>
                <th width="30">UOM</th>
                <th width="30">Rate (Main)</th>
                <th width="30">Rate (Add)</th>
                <th width="60">Amount</th>
                <th width="50">Currency</th>
                <th>Remarks</th>
            </thead>
		 <?
     		
			
			$sql_result =sql_select("select id, delivery_id, delivery_date, challan_no, order_id, item_id, batch_id,color_range_id, body_part_id, febric_description_id, dia_width_type, color_id, packing_qnty, delivery_qty, rate, add_rate, amount, remarks, currency_id, process_id, add_process, add_process_name, rate_data_string, lib_rate_id from subcon_inbound_bill_dtls  where mst_id='$mst_id' and status_active=1 and is_deleted=0 order by challan_no ASC"); 
			$i=1; $j=1; $challan_arr=array();
			foreach($sql_result as $row)
			{
				if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
				
				if (!in_array($row[csf('challan_no')],$challan_arr) )
				{
					if($j!=1)
					{ 
						?>
                        <tr class="tbl_bottom" bgcolor="#C0DCC0" style="font-size:12px"> 
                            <td align="right" colspan="11"><strong>Challan Total:</strong></td>
                            <td align="right"><strong><? echo $sub_packing_qty; ?>&nbsp;</strong></td>
                            <td align="right"><strong><? echo number_format($sub_delivery_qty,2,'.',','); ?>&nbsp;</strong></td>
                            <td>&nbsp;</td>
                            <td>&nbsp;</td>
                            <td>&nbsp;</td>
                            <td align="right"><strong><? echo number_format($sub_amount,2,'.',','); ?>&nbsp;</strong></td>
                            <td>&nbsp;</td>
                            <td>&nbsp;</td>
                        </tr>
						<?
						unset($sub_packing_qty);
						unset($sub_delivery_qty);
						unset($sub_amount);
					}
					$challan_arr[]=$row[csf("challan_no")];
					$j++;
				}
				$process=explode(',',$row[csf("add_process")]);
				$add_process="";
				foreach($process as $inf)
				{
					if($add_process=="") $add_process=$conversion_cost_head_array[$inf]; else $add_process.=", ".$conversion_cost_head_array[$inf];
				}
				
				if($dataArray[0][csf('party_source')]==2) $item_all= explode(',',$batch_array[$row[csf('item_id')]]['item_description']);
				else if($dataArray[0][csf('party_source')]==1) $item_all= explode(',',$row[csf('item_id')]);
				
				$item_name="";
				foreach($item_all as $inf)
				{
					if($dataArray[0][csf('party_source')]==2)
					{
						if($item_name=="") $item_name=$inf; else $item_name.=", ".$inf;
					}
					else if($dataArray[0][csf('party_source')]==1)
					{
						if($item_name=="") $item_name=$prod_dtls_arr[$inf]; else $item_name.=", ".$prod_dtls_arr[$inf];
					}
				}
				$exrate_data_string=explode("#",$row[csf("rate_data_string")]);
				foreach($exrate_data_string as $process_data)
				{
					$exrate_data=explode("__",$process_data);
					$lib_id=$exrate_data[0];
					$librate=$exrate_data[1];
					$fabric_color_arr[$item_name][$color_library[$row[csf('color_id')]]][$process_ids_arr[$lib_id]]+=$librate;
					$mainProcess_arr[$process_ids_arr[$lib_id]]=$process_ids_arr[$lib_id];
				}
				
				$rec_challan="";
				$rec_challan=$recChallan_arr[$row[csf('challan_no')]][$row[csf('order_id')]][$row[csf('item_id')]][$row[csf('batch_id')]][$row[csf('body_part_id')]][$row[csf('febric_description_id')]][$row[csf('dia_width_type')]];
			?>
				<tr bgcolor="<? echo $bgcolor; ?>" style="font-size:12px"> 
					<td><? echo $i; ?></td>
					<td align="center" style="word-break:break-all"><? echo $row[csf('challan_no')].'<br>'.change_date_format($row[csf('delivery_date')]); ?></td>
					<td style="word-break:break-all"><? echo $rec_challan; ?></td>
					<td style="word-break:break-all"><? echo $order_array[$row[csf('order_id')]]['internal_ref']; ?></td>
					<td style="word-break:break-all"><? echo $order_array[$row[csf('order_id')]]['order_no']; ?></td>
					<td align="center" style="word-break:break-all"><? echo $order_array[$row[csf('order_id')]]['cust_buyer'].'<br>'.$order_array[$row[csf('order_id')]]['cust_style_ref']; ?></td>
					<td style="word-break:break-all"><? echo $item_name; ?></td>
					<td style="word-break:break-all"><? echo $fabric_typee[$row[csf('dia_width_type')]]; ?></td>
					<td style="word-break:break-all"><? echo $color_range[$row[csf('color_range_id')]];//$color_range[$batch_array[$row[csf('batch_id')]]['color_range']]; ?></td>
					<td style="word-break:break-all"><? echo $color_library[$row[csf('color_id')]]; ?></td>
					<td style="word-break:break-all"><? echo $row[csf('add_process_name')];//$add_process; ?></td>
					<td align="right"><? echo $row[csf('packing_qnty')]; ?>&nbsp;</td>
					<td align="right"><? echo number_format($row[csf('delivery_qty')],2,'.',','); ?>&nbsp;</td>
					<td style="word-break:break-all"><? echo $unit_of_measurement[12]; ?></td>
					<td align="right"><? echo number_format($row[csf('rate')],4,'.',','); ?>&nbsp;</td>
					<td align="right"><? echo number_format($row[csf('add_rate')],4,'.',','); ?>&nbsp;</td>
					<td align="right"><? echo number_format($row[csf('amount')],4,'.',','); ?>&nbsp;</td>

					<td align="center" style="word-break:break-all"><? echo $currency[$row[csf('currency_id')]]; ?></td>
					<td style="word-break:break-all"><? echo $row[csf('remarks')]; ?></td>
					<? 
					$carrency_id=$row[csf('currency_id')];
					if($carrency_id==1) $paysa_sent="Paisa"; else if($carrency_id==2) $paysa_sent="CENTS";
					?>
				</tr>
				<?php
				$i++;
				
				$sub_packing_qty+=$row[csf('packing_qnty')];
				$sub_delivery_qty+=$row[csf('delivery_qty')];
				$sub_amount += $row[csf('amount')];
				
				$tot_packing_qty+=$row[csf('packing_qnty')];
				$tot_delivery_qty+=$row[csf('delivery_qty')];
				$total_amount += $row[csf('amount')];
			}
			//	discount,upcharge
			?>
            <tr class="tbl_bottom" bgcolor="#C0DCC0" style="font-size:12px"> 
                <td align="right" colspan="11"><strong>Challan Total:</strong></td>
                <td align="right"><strong><? echo $sub_packing_qty; ?>&nbsp;</strong></td>
                <td align="right"><strong><? echo number_format($sub_delivery_qty,2,'.',','); ?>&nbsp;</strong></td>
                <td>&nbsp;</td>
                <td>&nbsp;</td>
                <td>&nbsp;</td>
                <td align="right"><strong><? echo number_format($sub_amount,4,'.',','); ?>&nbsp;</strong></td>
                <td>&nbsp;</td>
                <td>&nbsp;</td>
            </tr>
            
        	<tr bgcolor="#FFFFAA" style="font-size:12px"> 
                <td align="right" colspan="11"><strong>Grand Total</strong></td>
                <td align="right"><strong><? echo $tot_packing_qty; ?>&nbsp;</strong></td>
                <td align="right"><strong><? echo number_format($tot_delivery_qty,2,'.',','); ?>&nbsp;</strong></td>
                <td>&nbsp;</td>
                <td>&nbsp;</td>
                <td>&nbsp;</td>
                <td align="right"><strong><? echo $format_total_amount=number_format($total_amount,4,'.',','); ?>&nbsp;</strong></td>
                <td>&nbsp;</td>
                <td>&nbsp;</td>
			</tr>
            <tr bgcolor="#FFFFAA" style="font-size:12px"> 
                <td align="right" colspan="11"><strong>Upcharge</strong></td>
                <td align="right"><strong><? //echo $tot_packing_qty; ?>&nbsp;</strong></td>
                <td align="right"><strong><? //echo number_format($tot_delivery_qty,2,'.',','); ?>&nbsp;</strong></td>
                <td>&nbsp;</td>
                <td>&nbsp;</td>
                <td>&nbsp;</td>
                <td align="right"><strong><? echo  number_format($upcharge,4,'.',','); ?>&nbsp;</strong></td>
                <td>&nbsp;</td>
                <td>&nbsp;</td>
			</tr>
            <tr bgcolor="#FFFFAA" style="font-size:12px"> 
                <td align="right" colspan="11"><strong>Discount</strong></td>
                <td align="right"><strong><? //echo $tot_packing_qty; ?>&nbsp;</strong></td>
                <td align="right"><strong><? //echo number_format($tot_delivery_qty,2,'.',','); ?>&nbsp;</strong></td>
                <td>&nbsp;</td>
                <td>&nbsp;</td>
                <td>&nbsp;</td>
                <td align="right"><strong><? echo  number_format($discount,4,'.',','); ?>&nbsp;</strong></td>
                <td>&nbsp;</td>
                <td>&nbsp;</td>
			</tr>
             <tr bgcolor="#FFFFAA" style="font-size:12px"> 
                <td align="right" colspan="11"><strong>Net Total</strong></td>
                <td align="right"><strong><? //echo $tot_packing_qty; ?>&nbsp;</strong></td>
                <td align="right"><strong><? //echo number_format($tot_delivery_qty,2,'.',','); ?>&nbsp;</strong></td>
                <td>&nbsp;</td>
                <td>&nbsp;</td>
                <td>&nbsp;</td>
                <td align="right"><strong><?
				$net_total_amount=($total_amount+$upcharge)-$discount;
				 echo  number_format($net_total_amount,4,'.',','); ?>&nbsp;</strong></td>
                <td>&nbsp;</td>
                <td>&nbsp;</td>
			</tr>
            
           <tr>
               <td colspan="19" align="left"><b>In Word: <? echo number_to_words($net_total_amount,$currency[$carrency_id],$paysa_sent); ?></b></td>
           </tr>
        </table>
        <table width="930" align="center" > 
        	<tr><td colspan="2">&nbsp;</td> </tr>
            <tr><td colspan="2" align="center"><b>TERMS & CONDITION</b></td> </tr>
        <?
			$bill_no=$dataArray[0][csf('bill_no')];
			$sql_terms="Select id,terms from subcon_terms_condition where entry_form=2 and bill_no='$bill_no' ";
			$result_sql_terms =sql_select($sql_terms);
			$i=1;
			if(count($result_sql_terms)>0)
			{
				foreach($result_sql_terms as $rows)
				{
					if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
				?>
					<tr bgcolor="<? echo $bgcolor; ?>"> 
						<td width="30"><? echo $i; ?></td>
						<td style="word-break:break-all"><? echo $rows[csf('terms')]; ?></td>
					</tr>
				<?
				$i++;
				}
			}
			?>
        </table>
        <br>
		 <? echo signature_table(48, $data[0], "930px"); ?>
   </div>
   </div>
	<?
    exit();
}
 //=====================Print Btn 5 START=======================================MD.SAKIBUL ISLAM=====================================================
 if($action=="dyeingFinishinBillPrint5")
 {
	 extract($_REQUEST);
	 $data=explode('*',$data);
	 $company_library=return_library_array( "select id, company_name from lib_company", "id", "company_name");
	 $party_library=return_library_array( "select id,buyer_name from lib_buyer", "id","buyer_name");
	 $color_library=return_library_array( "select id,color_name from  lib_color where status_active=1 and is_deleted=0", "id","color_name");
	 $prod_dtls_arr=return_library_array( "select id, product_name_details from  product_details_master",'id','product_name_details');
	 $pro_batch_arr=return_library_array( "select id, batch_no from pro_batch_create_mst",'id','batch_no');
	 
	 $sql_mst="Select id, bill_no, bill_date, location_id, party_id, party_source, upcharge,discount,party_location_id, bill_for, terms_and_condition from subcon_inbound_bill_mst where company_id=$data[0] and id='$data[1]' and status_active=1 and is_deleted=0";
	 $dataArray=sql_select($sql_mst);
 
	 $location_id=$dataArray[0][csf('location_id')];
	 $sql_com_loc="select b.id,b.location_name,b.address from lib_location b,lib_company a where a.id=b.company_id and b.company_id=$data[0] and b.id=$location_id and a.status_active=1";
	 $dataArray_loc=sql_select($sql_com_loc);
	 foreach($dataArray_loc as $row)
	 {
		 $loc_address=$row[csf('address')];
		 $location_arr[$row[csf('id')]]=$row[csf('location_name')];
	 }
	 
	 ?>
	 <div style="width:870px;" align="center">
	 <table width="860" cellpadding="0" cellspacing="0" align="center">
		 <tr>
			 <td>
				 <table width="860" cellspacing="0" align="center">
					 <tr>
						 <td align="center" style="font-size:20px"><strong ><? echo $company_library[$data[0]]; ?></strong></td>
					 </tr>
					 <tr class="form_caption">
						 <td  align="center" style="font-size:14px"><? echo $location_arr[$dataArray[0][csf('location_id')]].", ".$loc_address; ?></td>  
					 </tr>
					 <tr>
						 <td align="center" style="font-size:18px"><strong><? echo $data[3]; ?></strong></td>
					 </tr>
				 </table>
			 </td>
		 </tr>
	 </table> 
	 <table width="860" cellspacing="0" align="center" border="0">   
		   <tr></tr>
			  <tr>
				<?
					if($dataArray[0][csf('party_source')]==2)
					{
						$party_add=$dataArray[0][csf('party_id')];
						$nameArray=sql_select( "select address_1, web_site, buyer_email, country_id from  lib_buyer where id=$party_add"); 
						foreach ($nameArray as $result)
						{ 
							$address="";
							if($result!="") $address=$result[csf('address_1')];
						}
						$party_details=$party_library[$party_add].'<br>'.$address;
					}
					else if($dataArray[0][csf('party_source')]==1)
					{
						$party_details=$company_library[$dataArray[0][csf('party_id')]];
					}
				?>
			 	 <td><strong>Bill No : </strong><? echo $dataArray[0][csf('bill_no')]; ?></td>
				 <td></td>
				 <td></td>
				 <td align="right"><strong>Date: </strong><? echo change_date_format($dataArray[0][csf('bill_date')]); ?></td>
			 </tr>
			  <tr>
			  		<td><strong>To,<? echo "<br>". $party_details; ?></strong></td>
					<td></td>
				 	<td></td>
					<td align="right">Print Date Time:<?
					$formatted_date = date('d/m/Y h:i:s A', strtotime($pc_date_time));
					echo $formatted_date;?></td>
			 </tr>
			 <tr>
				 	<td><strong>Attention :</strong><? echo "Accounts Department"; ?></td>
			 </tr>
		 </table>
		 <br>
		 <?
		 $mst_id=$dataArray[0][csf('id')];
		 $process_ids_arr=array();
		 $sql_rate="select id, process_id, customer_rate, status_active from lib_subcon_charge where status_active!=0 and is_deleted=0  and process_type_id=1 order by id Desc";
		 $sql_rate_res=sql_select($sql_rate);
		 foreach($sql_rate_res as $rrow)
		 {
			 $process_ids_arr[$rrow[csf('id')]]=$rrow[csf('process_id')];
		 }
		 unset($sql_rate_res);
		 
		 $batch_array=array(); $order_array=array();
		 $grey_color_array=array();
		 $grey_sql="Select a.color_id, a.color_range_id, b.fabric_from, b.po_id, b.id, b.item_description from pro_batch_create_mst a, pro_batch_create_dtls b,subcon_inbound_bill_dtls c where a.id=b.mst_id and c.batch_id=a.id and c.mst_id='$mst_id' and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0";
		 $grey_sql_result =sql_select($grey_sql);
		 foreach($grey_sql_result as $row)
		 {
			 $batch_array[$row[csf('id')]]['color_range']=$row[csf('color_range_id')];
			 $batch_array[$row[csf('id')]]['color']=$row[csf('color_id')];
			 $batch_array[$row[csf('id')]]['item_description']=$row[csf('item_description')];
		 }	
		 
		 if($dataArray[0][csf('party_source')]==2)
		 {
			 $order_sql="SELECT b.id, b.order_no, b.order_uom, b.cust_buyer, b.cust_style_ref,b.batch_id from subcon_ord_dtls b,subcon_inbound_bill_dtls c  where b.id=c.order_id and b.status_active=1 and b.is_deleted=0 and c.mst_id='$mst_id'";
			 $order_sql_result =sql_select($order_sql);
			 foreach($order_sql_result as $row)
			 {
				 $order_array[$row[csf('id')]]['order_no']=$row[csf('order_no')];
				 $order_array[$row[csf('id')]]['order_uom']=$row[csf('order_uom')];
				 $order_array[$row[csf('id')]]['cust_buyer']=$row[csf('cust_buyer')];
				 $order_array[$row[csf('id')]]['cust_style_ref']=$row[csf('cust_style_ref')];
				 $order_array[$row[csf('id')]]['batch_id']=$row[csf('batch_id')];
			 }
		 }
		 else if($dataArray[0][csf('party_source')]==1)
		 {
			 $order_sql="SELECT a.job_no,a.buyer_name, a.style_ref_no, a.order_uom, b.id, b.po_number, b.grouping from wo_po_details_master a, wo_po_break_down b,subcon_inbound_bill_dtls c  where a.job_no=b.job_no_mst  and b.id=c.order_id and a.status_active!=0 and a.is_deleted=0 and b.status_active!=0 and b.is_deleted=0 and c.mst_id='$mst_id'";
			 $order_sql_result =sql_select($order_sql);
			 foreach($order_sql_result as $row)
			 {
				 $order_array[$row[csf('id')]]['order_no']=$row[csf('po_number')];
				 $order_array[$row[csf('id')]]['order_uom']=$row[csf('order_uom')];
				 $order_array[$row[csf('id')]]['cust_buyer']=$party_library[$row[csf('buyer_name')]];
				 $order_array[$row[csf('id')]]['cust_style_ref']=$row[csf('style_ref_no')];
				 $order_array[$row[csf('id')]]['internal_ref']=$row[csf('grouping')];
				 $job_no=$row[csf('job_no')];
			 }
			 $recChallan_arr=array();
			 
			 $rec_challa_sql="SELECT a.recv_number_prefix_num, a.challan_no, b.batch_id, b.prod_id, b.body_part_id, b.fabric_description_id, b.dia_width_type, c.po_breakdown_id
							 FROM inv_receive_master a, pro_finish_fabric_rcv_dtls b, order_wise_pro_details c, pro_batch_create_mst d
							 WHERE a.id=b.mst_id and b.id=c.dtls_id and d.id=b.batch_id and c.trans_type=1 and c.entry_form in (7,37) and c.trans_id!=0 and a.entry_form in (7,37) AND a.knitting_source=1 AND a.company_id='".$dataArray[0][csf('party_id')]."' AND a.location_id='".$dataArray[0][csf('location_id')]."' AND a.knitting_company=$data[0] and a.receive_basis in(4,5,9) and b.trans_id!=0  and a.item_category=2 
							 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 
							 group by a.id, a.recv_number_prefix_num, a.challan_no, b.batch_id, b.prod_id, b.body_part_id, b.fabric_description_id, b.dia_width_type, c.po_breakdown_id order by a.recv_number_prefix_num DESC";
			 $rec_challa_sql_res=sql_select($rec_challa_sql);
			 foreach($rec_challa_sql_res as $row)
			 {
				 $recChallan_arr[$row[csf('recv_number_prefix_num')]][$row[csf('po_breakdown_id')]][$row[csf('prod_id')]][$row[csf('batch_id')]][$row[csf('body_part_id')]][$row[csf('fabric_description_id')]][$row[csf('dia_width_type')]]=$row[csf('challan_no')];
			 }
		 }
		 $rate_data_string=$lib_rate_id=""; $mainProcess_arr=array(); $fabric_color_arr=array();
		 ?>
	 <div style="width:100%;">
		 <table align="center" cellspacing="0" width="860"  border="1" rules="all" class="rpt_table" >
			 <thead bgcolor="#dddddd" align="center" style="font-size:12px"> 
				 <th width="30">SL</th>
				 <th width="100">Challan No</th>
				 <th width="160"> Description</th>
				 <th width="100">Colour</th>
				 <th width="100">Dyeing Type</th> 
				 <th width="150">Fabric Type</th>
				 <th width="80">Gray Weight <br>(K.G) </th>
				 <th width="70">Rate</th>
				 <th width="70">Taka</th>
			 </thead>
		  <?
			 $sql_result =sql_select("select id, delivery_id, delivery_date, challan_no, order_id, item_id, batch_id,color_range_id, body_part_id, febric_description_id, dia_width_type, color_id, packing_qnty, delivery_qty, rate, add_rate, amount, remarks, currency_id, process_id,shade_percentage, add_process, add_process_name, rate_data_string, lib_rate_id from subcon_inbound_bill_dtls  where mst_id='$mst_id' and status_active=1 and is_deleted=0 order by challan_no ASC"); 

			 $i=1; $j=1; $challan_arr=array();
			 foreach($sql_result as $row)
			 {
				 if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
				 
				 if (!in_array($row[csf('challan_no')],$challan_arr) )
				 {
					 if($j!=1)
					 { 
						 ?>
						 <tr class="tbl_bottom" bgcolor="#C0DCC0" style="font-size:12px"> 
							 <td align="right" colspan="5"><strong>Total:</strong></td>
							 <td>&nbsp;</td>
							 <td align="right"><strong><? echo number_format($sub_delivery_qty,2,'.',','); ?>&nbsp;</strong></td>
							 <td>&nbsp;</td>
							 <td align="right"><strong><? echo number_format($sub_amount,2,'.',','); ?>&nbsp;</strong></td>
						 </tr>
						 <?
						 unset($sub_packing_qty);
						 unset($sub_delivery_qty);
						 unset($sub_amount);
					 }
					 $challan_arr[]=$row[csf("challan_no")];
					 $j++;
				 }
				 $process=explode(',',$row[csf("add_process")]);
				 $add_process="";
				 foreach($process as $inf)
				 {
					 if($add_process=="") $add_process=$conversion_cost_head_array[$inf]; else $add_process.=", ".$conversion_cost_head_array[$inf];
				 }
				
				 
				 if($dataArray[0][csf('party_source')]==2) $item_all= explode(',',$batch_array[$row[csf('item_id')]]['item_description']);
				 else if($dataArray[0][csf('party_source')]==1) $item_all= explode(',',$row[csf('item_id')]);

				 
				 $item_name="";
				 foreach($item_all as $inf)
				 {
					 if($dataArray[0][csf('party_source')]==2)
					 {
						 if($item_name=="") $item_name=$inf; else $item_name.=", ".$inf;
					 }
					 else if($dataArray[0][csf('party_source')]==1)
					 {
						 if($item_name=="") $item_name=$prod_dtls_arr[$inf]; else $item_name.=", ".$prod_dtls_arr[$inf];
					 }
				 }
				 $exrate_data_string=explode("#",$row[csf("rate_data_string")]);
				 foreach($exrate_data_string as $process_data)
				 {
					 $exrate_data=explode("__",$process_data);
					 $lib_id=$exrate_data[0];
					 $librate=$exrate_data[1];
					 $fabric_color_arr[$item_name][$color_library[$row[csf('color_id')]]][$process_ids_arr[$lib_id]]+=$librate;
					 $mainProcess_arr[$process_ids_arr[$lib_id]]=$process_ids_arr[$lib_id];
				 }
				 
				 $rec_challan="";
				 $rec_challan=$recChallan_arr[$row[csf('challan_no')]][$row[csf('order_id')]][$row[csf('item_id')]][$row[csf('batch_id')]][$row[csf('body_part_id')]][$row[csf('febric_description_id')]][$row[csf('dia_width_type')]];
				 
			 ?>
				 <tr bgcolor="<? echo $bgcolor; ?>" style="font-size:12px"> 
					 <td><? echo $i; ?></td>
					 <td align="center" style="word-break:break-all"><? echo $row[csf('challan_no')]."<br>".change_date_format($row[csf('delivery_date')]); ?></td>
					 <td style="word-break:break-all"><? echo "Buyer: ".$order_array[$row[csf('order_id')]]['cust_buyer']."<br>"."Style No: ".$order_array[$row[csf('order_id')]]['cust_style_ref']."<br>"."Order No: ".$order_array[$row[csf('order_id')]]['order_no']."<br>"."Batch No: ".$pro_batch_arr[$row[csf('batch_id')]];  ?></td>
					 <td style="word-break:break-all"><? echo $color_library[$row[csf('color_id')]]; ?></td>
					 <td style="word-break:break-all"><? echo $fabric_typee[$row[csf('dia_width_type')]]; ?></td>
					 <td align="center" style="word-break:break-all"><? echo $item_name;; ?></td>
					 <td style="word-break:break-all"><? echo number_format($row[csf('delivery_qty')],2,'.',','); ?></td>
					 <td style="word-break:break-all"><? echo number_format($row[csf('rate')],2,'.',','); ?></td>
					 <td style="word-break:break-all"><? echo number_format($row[csf('amount')],2,'.',',');?></td>

					 <? 
					 $carrency_id=$row[csf('currency_id')];
					 if($carrency_id==1) $paysa_sent="Paisa"; else if($carrency_id==2) $paysa_sent="CENTS";
					 ?>
				 </tr>
				 <?php
				 $i++;
				 
				 $sub_packing_qty+=$row[csf('packing_qnty')];
				 $sub_delivery_qty+=$row[csf('delivery_qty')];
				 $sub_amount += $row[csf('amount')];
				 
				 $tot_packing_qty+=$row[csf('packing_qnty')];
				 $tot_delivery_qty+=$row[csf('delivery_qty')];
				 $total_amount += $row[csf('amount')];
			 }
			 ?>
			 <tr class="tbl_bottom" bgcolor="#C0DCC0" style="font-size:12px"> 
				 <td align="right" colspan="5"><strong>Total:</strong></td>
				 <td>&nbsp;</td>
				 <td align="right"><strong><? echo number_format($sub_delivery_qty,2,'.',','); ?>&nbsp;</strong></td>
				 <td>&nbsp;</td>
				 <td align="right"><strong><? echo number_format($sub_amount,2,'.',','); ?>&nbsp;</strong></td>
			 </tr>
			 
			 <tr bgcolor="#FFFFAA" style="font-size:12px"> 
				 <td align="right" colspan="5"><strong>Grand Total</strong></td>
				 <td>&nbsp;</td>
				 <td align="right"><strong><? echo number_format($tot_delivery_qty,2,'.',','); ?>&nbsp;</strong></td>
				 <td>&nbsp;</td>
				 <td align="right"><strong><? echo $format_total_amount=number_format($total_amount,2,'.',','); ?>&nbsp;</strong></td>
			 </tr>
			<tr>
				<td colspan="20" align="left"><b>In Word: <? echo number_to_words(number_format($total_amount,2),$currency[$carrency_id],$paysa_sent); ?></b></td>
			</tr>
		 </table>
		 <br>
		  <? echo signature_table(48, $data[0], "930px"); ?>
	</div>
	</div>
	 <?
	 exit();
 }
 
  //======================================= Print Btn 5 END==========================================================================

if($action=="dyeing_finishin_bill_print3")
{
	
	
    extract($_REQUEST);
	$data=explode('*',$data);
	
	
	// echo $data[1].'DDDDDDDD'; die;

	$imge_arr=return_library_array( "select master_tble_id,image_location from common_photo_library where form_name='company_details' and file_type=1",'master_tble_id','image_location');
	$company_library=return_library_array( "select id, company_name from lib_company", "id", "company_name");
	$party_library=return_library_array( "select id,buyer_name from lib_buyer", "id","buyer_name");
	$color_library=return_library_array( "select id,color_name from  lib_color where status_active=1 and is_deleted=0", "id","color_name");
	$color_id_arr=return_library_array( "select id, color_id from subcon_delivery_dtls",'id','color_id');
	$inv_item_arr=return_library_array( "select id,material_description from sub_material_dtls",'id','material_description');
	$prod_item_arr=return_library_array( "select id,fabric_description from subcon_production_dtls",'id','fabric_description');
	$prod_process_arr=return_library_array( "select cons_comp_id, process from subcon_production_dtls",'cons_comp_id','process');
	$prod_dtls_arr=return_library_array( "select id, product_name_details from  product_details_master",'id','product_name_details');
	$location_arr=return_library_array("select id,location_name from lib_location", "id","location_name");
	$batch_arr=return_library_array( "select id, batch_no from pro_batch_create_mst",'id','batch_no');
	
	
	$sql_mst="Select id, bill_no, bill_date, location_id, party_id, party_source, party_location_id, bill_for, terms_and_condition from subcon_inbound_bill_mst where company_id=$data[0] and id='$data[1]' and status_active=1 and is_deleted=0";
	$dataArray=sql_select($sql_mst);
	$location_id=$dataArray[0][csf('location_id')];
	$sql_com_loc="select b.id,b.location_name,b.address from lib_location b,lib_company a where a.id=b.company_id and b.company_id=$data[0] and b.id=$location_id and a.status_active=1";
	$dataArray_loc=sql_select($sql_com_loc);
	foreach($dataArray_loc as $row)
	{
		$loc_address=$row[csf('address')];
	}

	?>
    <div style="width:1130px;" align="center">
	<? if($data[4]==1)
	{
	?>
	<style>
		@media print {
			table tr th,table tr td{ font-size: 20px !important; }
		}
	</style>
	<? } ?>
    <table width="900" cellpadding="0" cellspacing="0" align="center">
        <tr>
            <td width="100" align="right"> 
            	<img src='../../<? echo $imge_arr[str_replace("'","",$data[0])]; ?>' height='70' width='100' />
            </td>
            <td>
                <table width="800" cellspacing="0" align="center">
                    <tr>
                    	<td align="center" style="font-size:20px"><strong ><? echo $company_library[$data[0]]; ?></strong></td>
                    </tr>
                    <tr>
                        <td align="center"  style="font-size:16px"><strong>Unit : <? echo $location_arr[$dataArray[0][csf('location_id')]]; ?></strong></td>
                    </tr>
                    <tr class="form_caption">
                        <td  align="center" style="font-size:14px"><? echo $loc_address;//show_company($data[0],'',''); ?></td>  
                    </tr>
                    <tr>
                    	<td align="center" style="font-size:18px"><strong><? echo $data[3]; ?></strong></td>
                    </tr>
                </table>
            </td>
        </tr>
    </table> 
    <table width="930" cellspacing="0" align="center" border="0">   
    	  <tr><td colspan="6" align="center"><hr></hr></td></tr>
             <tr>
			 <?
			 	if($dataArray[0][csf('party_source')]==2)
				{
					$party_add=$dataArray[0][csf('party_id')];
					$nameArray=sql_select( "select address_1, web_site, buyer_email, country_id from  lib_buyer where id=$party_add"); 
					foreach ($nameArray as $result)
					{ 
                    	$address="";
						if($result!="") $address=$result[csf('address_1')];
					}
					$party_details=$party_library[$party_add].'<br>'.$address;
				}
				else if($dataArray[0][csf('party_source')]==1)
				{
					$party_details=$company_library[$dataArray[0][csf('party_id')]];
				}
			 ?>
                <td width="300" rowspan="4" valign="top" colspan="2"><strong>Party :<? echo $party_details; ?></strong></td>
                <td width="130"><strong>Party Location: </strong></td><td width="175px"> <? echo $location_arr[$dataArray[0][csf('party_location_id')]]; ?></td>
                <td width="130"><strong>Bill No :</strong></td> <td width="175"><strong><? echo $dataArray[0][csf('bill_no')]; ?></strong></td>
            </tr>
             <tr>
             	<td><strong>Bill Date: </strong></td><td> <? echo change_date_format($dataArray[0][csf('bill_date')]); ?></td>
                <td><strong>Source :</strong></td> <td><? echo $knitting_source[$dataArray[0][csf('party_source')]]; ?></td>
            </tr>
            <tr>
                <td><strong>Bill For :</strong></td> <td><? echo $bill_for[$dataArray[0][csf('bill_for')]]; ?></td>
                <td>&nbsp;</td><td>&nbsp;</td>
                <td>&nbsp;</td><td>&nbsp;</td>
            </tr>
        </table>
        <br>
        <?
		$process_ids_arr=array();
		$sql_rate="select id, process_id, in_house_rate, uom_id, rate_type_id, customer_rate, buyer_id, status_active from lib_subcon_charge where status_active!=0 and is_deleted=0 and rate_type_id in (3,4,7,8) and process_type_id=1 order by id Desc";
		$sql_rate_res=sql_select($sql_rate);
		foreach($sql_rate_res as $rrow)
		{
			$process_ids_arr[$rrow[csf('id')]]=$rrow[csf('process_id')];
		}
		unset($sql_rate_res);
		
		$batch_array=array(); $order_array=array();
		$grey_color_array=array();
		$grey_sql="Select a.color_id, b.fabric_from, b.po_id, b.id, b.item_description from pro_batch_create_mst a, pro_batch_create_dtls b where a.id=b.mst_id and b.status_active=1 and b.is_deleted=0";
		$grey_sql_result =sql_select($grey_sql);
		foreach($grey_sql_result as $row)
		{
			//$batch_array[$row[csf('id')]]=$row[csf('fabric_from')];
			$batch_array[$row[csf('id')]]['color']=$row[csf('color_id')];
			$batch_array[$row[csf('id')]]['item_description']=$row[csf('item_description')];
		}	
		
		if($dataArray[0][csf('party_source')]==2)
		{
			$order_sql="select id, job_no_mst, order_no, order_uom, cust_buyer, cust_style_ref from subcon_ord_dtls where status_active=1 and is_deleted=0";
			$order_sql_result =sql_select($order_sql);
			foreach($order_sql_result as $row)
			{
				$order_array[$row[csf('id')]]['job_no']=$row[csf('job_no_mst')];
				$order_array[$row[csf('id')]]['order_no']=$row[csf('order_no')];
				$order_array[$row[csf('id')]]['order_uom']=$row[csf('order_uom')];
				$order_array[$row[csf('id')]]['cust_buyer']=$row[csf('cust_buyer')];
				$order_array[$row[csf('id')]]['cust_style_ref']=$row[csf('cust_style_ref')];
			}
		}
		else if($dataArray[0][csf('party_source')]==1)
		{
			$order_sql="select a.job_no, a.buyer_name, a.style_ref_no, a.order_uom, b.id, b.po_number from wo_po_details_master a, wo_po_break_down b where a.job_no=b.job_no_mst and a.status_active!=0 and a.is_deleted=0 and b.status_active!=0 and b.is_deleted=0";
			$order_sql_result =sql_select($order_sql);
			foreach($order_sql_result as $row)
			{
				$order_array[$row[csf('id')]]['job_no']=$row[csf('job_no')];
				$order_array[$row[csf('id')]]['order_no']=$row[csf('po_number')];
				$order_array[$row[csf('id')]]['order_uom']=$row[csf('order_uom')];
				$order_array[$row[csf('id')]]['cust_buyer']=$party_library[$row[csf('buyer_name')]];
				$order_array[$row[csf('id')]]['cust_style_ref']=$row[csf('style_ref_no')];
			}
			$recChallan_arr=array();
			
			$rec_challa_sql="SELECT a.recv_number_prefix_num, a.challan_no, b.batch_id, b.prod_id, b.body_part_id, b.fabric_description_id, b.dia_width_type, c.po_breakdown_id
							FROM inv_receive_master a, pro_finish_fabric_rcv_dtls b, order_wise_pro_details c, pro_batch_create_mst d
							WHERE a.id=b.mst_id and b.id=c.dtls_id and d.id=b.batch_id and c.trans_type=1 and c.entry_form in (7,37) and c.trans_id!=0 and a.entry_form in (7,37) AND a.knitting_source=1 AND a.company_id='".$dataArray[0][csf('party_id')]."' AND a.location_id='".$dataArray[0][csf('location_id')]."' AND a.knitting_company=$data[0] and a.receive_basis in(4,5,9) and b.trans_id!=0  and a.item_category=2 
							and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 
							group by a.id, a.recv_number_prefix_num, a.challan_no, b.batch_id, b.prod_id, b.body_part_id, b.fabric_description_id, b.dia_width_type, c.po_breakdown_id order by a.recv_number_prefix_num DESC";
			$rec_challa_sql_res=sql_select($rec_challa_sql);
			foreach($rec_challa_sql_res as $row)
			{
				$recChallan_arr[$row[csf('recv_number_prefix_num')]][$row[csf('po_breakdown_id')]][$row[csf('prod_id')]][$row[csf('batch_id')]][$row[csf('body_part_id')]][$row[csf('fabric_description_id')]][$row[csf('dia_width_type')]]=$row[csf('challan_no')];
			}
		}
		//var_dump($recChallan_arr);
		 $rate_data_string=$lib_rate_id=""; $mainProcess_arr=array(); $fabric_color_arr=array();
		//  if($data[4]==1)
		//  {
			?>
			<div style="width:100%;">
			<table align="center" cellspacing="0" width="1390"  border="1" rules="all" class="rpt_table" >
				<thead bgcolor="#dddddd" align="center" style="font-size:14px"> 
					<th width="20">SL</th>
					<th width="75">Challan & <br> Delv. Date</th>
					<th width="90">Batch No</th>
					<th width="90">Job No</th>
					<th width="100" style="font-size:16px">Order</th> 
					<th width="100">Buyer  & <br> Style</th>
					<th width="150">Fabric Des.</th>
					<th width="80">D.W Type</th>
					<th width="60">Color</th>
					<th width="100" style="font-size:16px">Color Range</th>
					<th width="100">A.Process</th>
					<th width="30">Roll</th>
					<th width="60" style="font-size:16px">Bill Qty</th>
					<th width="30">UOM</th>
					<th width="30" style="font-size:16px">Rate (Main)</th>
					<th width="60" style="font-size:16px">Amount</th>
					<th width="50">Currency</th>
					
				</thead>
			<?
				$i=1;
				$mst_id=$dataArray[0][csf('id')];
				$sql_result =sql_select("select id, delivery_id, delivery_date, challan_no, order_id, item_id, batch_id, body_part_id, febric_description_id, dia_width_type, color_id, color_range_id, packing_qnty, delivery_qty, rate, add_rate, amount, remarks, currency_id, process_id, add_process, add_process_name, rate_data_string, lib_rate_id from subcon_inbound_bill_dtls  where mst_id='$mst_id' and status_active=1 and is_deleted=0 order by challan_no"); 
				foreach($sql_result as $row)
				{
					if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
					//$rate_data_string.=$row[csf("rate_data_string")].',';
					//$lib_rate_id.=$row[csf("lib_rate_id")].',';
					
					$process=explode(',',$row[csf("add_process")]);
					$add_process="";
					foreach($process as $inf)
					{
						if($add_process=="") $add_process=$conversion_cost_head_array[$inf]; else $add_process.=", ".$conversion_cost_head_array[$inf];
					}
					
					if($dataArray[0][csf('party_source')]==2)
					{
						$item_all= explode(',',$batch_array[$row[csf('item_id')]]['item_description']);
					}
					else if($dataArray[0][csf('party_source')]==1)
					{
						$item_all= explode(',',$row[csf('item_id')]);
					}
					$item_name="";
					foreach($item_all as $inf)
					{
						if($dataArray[0][csf('party_source')]==2)
						{
							if($item_name=="") $item_name=$inf; else $item_name.=", ".$inf;
						}
						else if($dataArray[0][csf('party_source')]==1)
						{
							if($item_name=="") $item_name=$prod_dtls_arr[$inf]; else $item_name.=", ".$prod_dtls_arr[$inf];
						}
					}
					$exrate_data_string=explode("#",$row[csf("rate_data_string")]);
					foreach($exrate_data_string as $process_data)
					{
						$exrate_data=explode("__",$process_data);
						$lib_id=$exrate_data[0];
						$librate=$exrate_data[1];
						$fabric_color_arr[$item_name][$color_library[$row[csf('color_id')]]][$process_ids_arr[$lib_id]]+=$librate;
						$mainProcess_arr[$process_ids_arr[$lib_id]]=$process_ids_arr[$lib_id];
					}
					
					$rec_challan="";
					$rec_challan=$recChallan_arr[$row[csf('challan_no')]][$row[csf('order_id')]][$row[csf('item_id')]][$row[csf('batch_id')]][$row[csf('body_part_id')]][$row[csf('febric_description_id')]][$row[csf('dia_width_type')]];
					?>
					<tr bgcolor="<? echo $bgcolor; ?>" style="font-size:14px"> 
						<td><? echo $i; ?></td>
						<td align="center" style="word-break:break-all"><? echo $row[csf('challan_no')].'<br>'.change_date_format($row[csf('delivery_date')]); ?></td>
						<td align="center" style="word-break:break-all"><? echo $batch_arr[$row[csf('batch_id')]]; ?></td>
						<td align="center" style="word-break:break-all"><? echo $order_array[$row[csf('order_id')]]['job_no']; ?></td>
						<td align="center" style="word-break:break-all;font-size:16px"><? echo $order_array[$row[csf('order_id')]]['order_no']; ?></td>
						<td align="center" style="word-break:break-all"><? echo $order_array[$row[csf('order_id')]]['cust_buyer'].'<br>'.$order_array[$row[csf('order_id')]]['cust_style_ref']; ?></td>
						<td style="word-break:break-all"><? echo $item_name; ?></td>
						<td align="center" style="word-break:break-all"><? echo $fabric_typee[$row[csf('dia_width_type')]]; ?></td>
						<td align="center" style="word-break:break-all"><? echo $color_library[$row[csf('color_id')]]; ?></td>
						<td align="center" style="word-break:break-all;font-size:16px"><? echo $color_range[$row[csf('color_range_id')]]; ?></td>
						<td align="center" style="word-break:break-all"><? echo $row[csf('add_process_name')];//$add_process; ?></td>
						<td align="right"><? echo $row[csf('packing_qnty')]; $tot_packing_qty+=$row[csf('packing_qnty')]; ?>&nbsp;</td>
						<td align="right" style="font-size:16px"><? echo number_format($row[csf('delivery_qty')],2,'.',','); $tot_delivery_qty+=$row[csf('delivery_qty')]; ?>&nbsp;</td>
						<td align="center"><? echo $unit_of_measurement[12]; ?></td>
						<td align="right" style="font-size:16px"><? echo number_format($row[csf('rate')],2,'.',','); ?>&nbsp;</td>
						<td align="right" style="font-size:16px"><? echo number_format($row[csf('amount')],2,'.',',');  $total_amount += $row[csf('amount')]; ?>&nbsp;</td>

						<td style="word-break:break-all" align="center"><? echo $currency[$row[csf('currency_id')]]; ?></td>


					</tr>
					<?php
					$i++;
				}
				?>
				<tr style="font-size:14px"> 
					<td align="right" colspan="11"><strong>Total</strong></td>
					<td align="right"><strong><? echo $tot_packing_qty; ?>&nbsp;</strong></td>
					<td align="right"><strong><? echo number_format($tot_delivery_qty,2,'.',','); ?>&nbsp;</strong></td>
					<td>&nbsp;</td>
					<td>&nbsp;</td>
					<td align="right"><strong><? echo $format_total_amount=number_format($total_amount,2,'.',','); ?>&nbsp;</strong></td>
					<td>&nbsp;</td>
					
				</tr>
			<tr>
				<td colspan="17" align="left"><b>In Word: <? echo number_to_words($format_total_amount,$currency[$carrency_id],$paysa_sent); ?></b></td>
			</tr>
			</table>
			
		
				
				
        
    
        <br>
		 <?
            echo signature_table(48, $data[0], "930px");
         ?>
   </div>
   </div>
	<?
    exit();
}

if($action=="terms_condition_popup")
{
	echo load_html_head_contents("Trems & Condition Search","../../", 1, 1, $unicode);
	extract($_REQUEST);
	
	$_SESSION['page_permission']=$permission;
?>
	<script>
	var permission='<? echo $permission; ?>';
	function add_break_down_tr(i) 
	{
		var row_num=$('#tbl_termcondi_details tr').length-1;
		if (row_num!=i)
		{
			return false;
		}
		else
		{
			i++;
			
			$("#tbl_termcondi_details tr:last").clone().find("input,select").each(function() {
			$(this).attr({
			'id': function(_, id) { var id=id.split("_"); return id[0] +"_"+ i },
			'name': function(_, name) { return name + i },
			'value': function(_, value) { return value }              
			});  
			}).end().appendTo("#tbl_termcondi_details");
			$('#increase_'+i).removeAttr("onClick").attr("onClick","add_break_down_tr("+i+");");
			$('#decrease_'+i).removeAttr("onClick").attr("onClick","fn_deletebreak_down_tr("+i+")");
			$('#termscondition_'+i).val("");
			$('#sltd_'+i).val(i);
			//$('#sl_td').i
			//alert(i)
			//document.getElementById('sltd_'+i).innerHTML=i;
		}
	}

	function fn_deletebreak_down_tr(rowNo) 
	{   
		var numRow = $('table#tbl_termcondi_details tbody tr').length; 
		if(numRow==rowNo && rowNo!=1)
		{
			$('#tbl_termcondi_details tbody tr:last').remove();
		}
	}

	function fnc_fabric_finishing_terms_condition( operation )
	{
		var row_num=$('#tbl_termcondi_details tr').length-1;
		var data_all="";
		for (var i=1; i<=row_num; i++)
		{
			if (form_validation('termscondition_'+i,'Term Condition')==false)
			{
				return;
			}
			data_all=data_all+get_submitted_data_string('txt_bill_no*termscondition_'+i,"../../");
		}
		var data="action=save_update_delete_terms_condition&operation="+operation+'&total_row='+row_num+data_all;
		//freeze_window(operation);
		http.open("POST","sub_fabric_finishing_bill_issue_controller.php",true);
		http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
		http.send(data);
		http.onreadystatechange = fnc_fabric_finishing_terms_condition_reponse;
	}

	function fnc_fabric_finishing_terms_condition_reponse()
	{
		if(http.readyState == 4) 
		{
			var reponse=trim(http.responseText).split('**');
			//if (reponse[0].length>2) reponse[0]=10;
			if(reponse[0]==0 || reponse[0]==1)
			{
				parent.emailwindow.hide();
			}
		}
	}
    </script>
</head>
<body>
<div align="center" style="width:100%;" >
<? echo load_freeze_divs ("../../",$permission);  ?>
    <fieldset>
    <input type="hidden" id="txt_bill_no" name="txt_bill_no" value="<? echo str_replace("'","",$txt_bill_no) ?>"/>
        <form id="termscondi_1" autocomplete="off">
        <table width="650" cellspacing="0" class="rpt_table" border="0" id="tbl_termcondi_details" rules="all">
            <thead>
                <tr>
                    <th width="50">Sl</th><th width="530">Terms</th><th ></th>
                </tr>
            </thead>
            <tbody>
				<?
                $data_array=sql_select("select id, terms from  subcon_terms_condition where bill_no=$txt_bill_no");// quotation_id='$data'
                if(count($data_array)>0)
                {
					$i=0;
					foreach( $data_array as $row )
					{
						if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
						$i++;
						?>
						<tr id="settr_1" align="center" bgcolor="<? echo $bgcolor;  ?>">
                            <td >
                                <input type="text" id="sltd_<? echo $i;?>"   name="sltd_<? echo $i;?>" style="width:100%;background-color:<? echo $bgcolor;  ?>"  value="<? echo $i; ?>"   /> 
                            </td>
                            <td>
                                <input type="text" id="termscondition_<? echo $i;?>"   name="termscondition_<? echo $i;?>" style="width:95%"  class="text_boxes"  value="<? echo $row[csf('terms')]; ?>"  /> 
                            </td>
                            <td> 
                                <input type="button" id="increase_<? echo $i; ?>" style="width:30px" class="formbuttonplasminus" value="+" onClick="add_break_down_tr(<? echo $i; ?> )" />
                                <input type="button" id="decrease_<? echo $i; ?>" style="width:30px" class="formbuttonplasminus" value="-" onClick="javascript:fn_deletebreak_down_tr(<? echo $i; ?>);" />
                            </td>
                        </tr>
                        <?
					}
                }
                else
                {
					$data_array=sql_select("select id, terms from lib_terms_condition where is_default=1 and page_id=336");// quotation_id='$data'
					foreach( $data_array as $row )
					{
						if ($i%2==0) $bgcolor="#E9F3FF";  else  $bgcolor="#FFFFFF";
						$i++;
						?>
						<tr id="settr_1" align="center" bgcolor="<? echo $bgcolor;  ?>">
                            <td >
                                <input type="text" id="sltd_<? echo $i;?>"   name="sltd_<? echo $i;?>" style="width:100%; background-color:<? echo $bgcolor;  ?>"  value="<? echo $i; ?>"   /> 
                            </td>
                            <td>
                                <input type="text" id="termscondition_<? echo $i;?>"   name="termscondition_<? echo $i;?>" style="width:95%"  class="text_boxes"  value="<? echo $row[csf('terms')]; ?>"  /> 
                            </td>
                            <td>
                                <input type="button" id="increase_<? echo $i; ?>" style="width:30px" class="formbuttonplasminus" value="+" onClick="add_break_down_tr(<? echo $i; ?> )" />
                                <input type="button" id="decrease_<? echo $i; ?>" style="width:30px" class="formbuttonplasminus" value="-" onClick="javascript:fn_deletebreak_down_tr(<? echo $i; ?> );" />
                            </td>
						</tr>
						<? 
					}
                } 
                ?>
            </tbody>
        </table>
        <table width="650" cellspacing="0" class="" border="0">
            <tr>
                <td align="center" height="15" width="100%"> </td>
            </tr>
            <tr>
                <td align="center" width="100%" class="button_container">
					<?
						echo load_submit_buttons( $permission, "fnc_fabric_finishing_terms_condition", 0,0 ,"reset_form('termscondi_1','','','','')",1) ; 
                    ?>
                </td> 
            </tr>
        </table>
        </form>
    </fieldset>
	</div>
	</body>           
	<script src="../../includes/functions_bottom.js" type="text/javascript"></script>
	</html>
	<?
	exit();
}

if($action=="save_update_delete_terms_condition")
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
		
		//if  ( check_table_status( $_SESSION['menu_id'], 1 )==0 ) { echo "15**0"; die;}		
		 $id=return_next_id( "id", "subcon_terms_condition", 1 ) ;
		 $field_array="id,bill_no,terms,entry_form";
		 for ($i=1;$i<=$total_row;$i++)
		 {
			$termscondition="termscondition_".$i;
			if ($i!=1) $data_array .=",";
			$data_array .="(".$id.",".$txt_bill_no.",".$$termscondition.",2)";
			$id=$id+1;
		 }
		// echo  $data_array;
		$rID_de3=execute_query( "delete from subcon_terms_condition where  bill_no =".$txt_bill_no."",0);

		$rID=sql_insert("subcon_terms_condition",$field_array,$data_array,1);
		//check_table_status( $_SESSION['menu_id'],0);
		if($db_type==0)
		{
			if($rID ){
				mysql_query("COMMIT");  
				echo "0**".$txt_bill_no;
			}
			else{
				mysql_query("ROLLBACK"); 
				echo "10**".$txt_bill_no;
			}
		}
		
		if($db_type==2 || $db_type==1 )
		{
			if($rID ){
				oci_commit($con);  
				echo "0**".$txt_bill_no;
			}
			else{
				oci_rollback($con);  
				echo "10**".$txt_bill_no;
			}
		}
		disconnect($con);
		die;
	}		
}
if($action=="remarks_popup")
{
	echo load_html_head_contents("Remarks","../../", 1, 1, $unicode);
	extract($_REQUEST);
	?>
    <script>
	function js_set_value(val)
	{
		document.getElementById('text_new_remarks').value=val;
		parent.emailwindow.hide();
	}
	</script>
    </head>
<body>
<div align="center">
	<fieldset style="width:400px;margin-left:4px;">
        <form name="remarksfrm_1"  id="remarksfrm_1" autocomplete="off">
            <table cellpadding="0" cellspacing="0" width="370" >
                <tr>
                    <td align="center"><input type="hidden" name="auto_id" id="auto_id" value="<? echo $data; ?>" />
                      <textarea id="text_new_remarks" name="text_new_remarks" class="text_area" title="Maximum 1000 Character" maxlength="1000" style="width:330px; height:270px" placeholder="Remarks Here. Maximum 1000 Character." ><? echo $data; ?></textarea>
                    </td>
                </tr>
                <tr>
                	<td align="center">
                 <input type="button" id="formbuttonplasminus" align="middle" class="formbutton" style="width:100px" value="Close" onClick="js_set_value(document.getElementById('text_new_remarks').value)" />
                 	</td>
                </tr>
            </table>
        </form>
    </fieldset>
</div>    
</body>           
<script src="../../includes/functions_bottom.js" type="text/javascript"></script>
</html>
<?
exit();
}


if($action=="dyeing_rate_popup")
{
	echo load_html_head_contents("Dyeing Rate Popup","../../", 1, 1, $unicode);
	extract($_REQUEST);
	$data=explode('_',$data);
	$prev_id=explode(",",$data[2]);
	//$add_process=explode(",",$data[3]);

	?>
   <!--  <script>
	function js_set_value(val)
	{
		document.getElementById('hddn_all_data').value=val;
		parent.emailwindow.hide();
	}
	</script> -->
	<script>
	//var prev_id='<? echo $data[2]; ?>';
    var selected_id = new Array, selected_rate = new Array();
   

    function toggle(x, origColor) {
        var newColor = 'yellow';
        if (x.style) {
            x.style.backgroundColor = (newColor == x.style.backgroundColor) ? origColor : newColor;
        }
    }

    function set_all()
	{
		//alert();
		var old=document.getElementById('txt_prev_rate_id').value;
		if(old!="")
		{   
			old=old.split(",");
			for(var i=0; i<old.length; i++)
			{  
				js_set_value( old[i] ) 
			}
		}
	}

    function js_set_value(str) {
    	//alert(str);
        toggle(document.getElementById('search' + str), '#FFFFCC');

        if (jQuery.inArray($('#txt_individual_id' + str).val(), selected_id) == -1) {
            selected_id.push($('#txt_individual_id' + str).val());
            selected_rate.push($('#txt_in_house_rate' + str).val());
           // alert($('#txt_in_house_rate' + str).val());

        } else {
            for (var i = 0; i < selected_id.length; i++) {
                if (selected_id[i] == $('#txt_individual_id' + str).val())
                    break;
            }
            selected_id.splice(i, 1);
            selected_rate.splice(i, 1);
        }
        var id_rate = ''; 
        for (var i = 0; i < selected_id.length; i++) {
            id_rate += selected_id[i] + '__'+ selected_rate[i] + '#';
        }
        id_rate = id_rate.substr(0, id_rate.length - 1);
        $('#hddn_all_data').val(id_rate);
    }
    </script>
    <input type="hidden" id="hddn_all_data" />
	<?
	$buyer_arr=return_library_array("select id, short_name from lib_buyer",'id','short_name');
	$color_arr=return_library_array("select id, color_name from lib_color where status_active=1 and is_deleted=0",'id','color_name');
	
	//print_r($composition_arr);
	$sql="select id, const_comp, process_type_id, process_id, color_id,color_range_id, width_dia_id, in_house_rate, uom_id, rate_type_id, customer_rate, buyer_id, status_active from lib_subcon_charge where status_active!=0 and is_deleted=0 and rate_type_id in (3,4,7,8) and process_type_id=1 and comapny_id=$data[0] and process_id in ($data[3]) order by id Desc";// and buyer_id=$data[1]
	$result = sql_select($sql); $i=1;
	?>
    <table width="910" border="1" cellpadding="0" cellspacing="0" class="rpt_table" rules="all">
    	<thead>
        	<th width="25">SL</th>
            <th width="80">Buyer</th>
            <th width="80">Process Name</th>
            <th width="160">Construction & Composition</th>
            <th width="80">Color Range</th>
            <th width="80">Color</th>
            <th width="80">Rate type</th>
            <th width="110">Width/Dia type</th>
            <th width="80">In House Rate</th>
            <th width="60">UOM</th>
            <th>Customer Rate</th>
        </thead>
    </table>
    <div style="width:930; max-height:350px; overflow-y:scroll">
        <table cellpadding="0" width="910" class="rpt_table" rules="all" border="1" id="table_body">
            <tbody>
                <?
                //echo $data[2];
                foreach($result as $row)
                {
                    if($i%2==0)$bgcolor="#E9F3FF";  else $bgcolor="#FFFFFF";
                    if(in_array($row[csf('id')],$prev_id)) 
					{
						if($prev_rate_id=="") $prev_rate_id=$i; else $prev_rate_id.=",".$i;
					}
                	?>
                    <tr bgcolor="<? echo $bgcolor; ?>" style="text-decoration:none;cursor:pointer" onClick="js_set_value('<? echo $i; ?>')" id="search<? echo $i; ?>">
                        <td width="25"><? echo $i; ?></td>
                        <td width="80"><? echo $buyer_arr[$row[csf("buyer_id")]]; ?></td>
                        <td width="80"><? echo $conversion_cost_head_array[$row[csf('process_id')]]; ?></td>
                        <td width="160"><? echo $row[csf("const_comp")]; ?></td>
                        <td width="80"><p><? echo $color_range[$row[csf("color_range_id")]]; ?></p></td>
                        <td width="80"><p><? echo $color_arr[$row[csf("color_id")]]; ?></p></td>
                        <td width="80"><? echo $production_process[$row[csf("rate_type_id")]]; ?></td>
                        <td width="110"><? echo $fabric_typee[$row[csf("width_dia_id")]]; ?></td>
                        <td width="80" align="right"><? echo number_format($row[csf("in_house_rate")],3); ?></td>
                        <td width="60"><? echo $unit_of_measurement[$row[csf("uom_id")]]; ?></td>
                        <td align="right"><? echo number_format($row[csf("customer_rate")],3); ?>
                        	 <input type="hidden" name="txt_individual_id" id="txt_individual_id<?php echo $i ?>" value="<? echo $row[csf('id')]; ?>"/>	
                        	 <input type="hidden" name="txt_in_house_rate" id="txt_in_house_rate<?php echo $i ?>" value="<? echo $row[csf('in_house_rate')]; ?>"/>
                        </td>

                    </tr>
					<?
                    $i++;
                }
                ?>
            </tbody>
            <input type="hidden" name="txt_prev_rate_id" id="txt_prev_rate_id" value="<?php echo $prev_rate_id; ?>"/>
        </table>
    </div>
    <table width="830" cellspacing="0" cellpadding="0" style="border:none" align="center">
                <tr>
                    <td align="center" height="30" valign="bottom">
                        <div style="width:100%"> 
                            <div style="width:50%;" align="center">
                                <input type="button" name="close" onClick="parent.emailwindow.hide();" class="formbutton" value="Close" style="width:100px" />
                            </div>
                        </div>
                    </td>
                </tr>
            </table>
    <script> setFilterGrid("table_body",-1); set_all(); </script>
    <?
	exit();					
}

if($action=="dyeing_addRate_popup")
{
	echo load_html_head_contents("Dyeing Additional Rate Popup","../../", 1, 1, $unicode);
	extract($_REQUEST);
	?>
    <script>
	function js_set_value(val)
	{
		document.getElementById('hddn_all_data').value=val;
		parent.emailwindow.hide();
	}
	</script>
     <input type="hidden" id="hddn_all_data" />
	<?
	$buyer_arr=return_library_array("select id, short_name from lib_buyer",'id','short_name');
	$color_arr=return_library_array("select id, color_name from lib_color where status_active=1 and is_deleted=0",'id','color_name');
	
	//print_r($composition_arr);
	$sql="select id, const_comp, process_type_id, process_id, color_id, width_dia_id, in_house_rate, uom_id, rate_type_id, customer_rate, buyer_id, status_active from lib_subcon_charge where status_active!=0 and is_deleted=0 and rate_type_id in (3,4,7,8) and process_type_id=2 and comapny_id=$data order by id Desc";
	$result = sql_select($sql); $i=1;

	?>
    <table width="750" border="1" cellpadding="0" cellspacing="0" class="rpt_table" rules="all">
    	<thead>
        	<th width="25">SL</th>
            <th width="80">Buyer</th>
            <th width="160">Construction & Composition</th>
            <th width="80">Color</th>
            <th width="80">Rate type</th>
            <th width="110">Width/Dia type</th>
            <th width="80">In House Rate</th>
            <th width="60">UOM</th>
            <th>Customer Rate</th>
        </thead>
    </table>
    <div style="width:750; max-height:350px; overflow-y:scroll">
        <table cellpadding="0" width="750" class="rpt_table" rules="all" border="1" id="table_body">
            <tbody>
                <?
                foreach($result as $row)
                {
                    if($i%2==0)$bgcolor="#E9F3FF";  else $bgcolor="#FFFFFF"; 
                ?>
                    <tr bgcolor="<? echo $bgcolor; ?>" style="text-decoration:none;cursor:pointer" onClick="js_set_value('<? echo $row[csf("id")].'***'.$row[csf("in_house_rate")]; ?>')" id="tr_<? echo $i; ?>">
                        <td width="25"><? echo $i; ?></td>
                        <td width="80"><? echo $buyer_arr[$row[csf("buyer_id")]]; ?></td>
                        <td width="160"><? echo $row[csf("const_comp")]; ?></td>
                        <td width="80"><p><? echo $color_arr[$row[csf("color_id")]]; ?></p></td>
                        <td width="80"><? echo $production_process[$row[csf("rate_type_id")]]; ?></td>
                        <td width="110"><? echo $fabric_typee[$row[csf("width_dia_id")]]; ?></td>
                        <td width="80" align="right"><? echo number_format($row[csf("in_house_rate")],3); ?></td>
                        <td width="60"><? echo $unit_of_measurement[$row[csf("uom_id")]]; ?></td>
                        <td align="right"><? echo number_format($row[csf("customer_rate")],3); ?></td>
                    </tr>
					<?
                    $i++;
                }
                ?>
            </tbody>
        </table>
    </div>
    <script> setFilterGrid("table_body",-1); </script>
    <?
	exit();					
}

if($action=="bill_amount_check")
{
	$data=explode("_",$data);
	$orderIds=$data[0];
	//echo "0"."___"; die;
	$current_amount=$data[1];
	$update_id=$data[2];
	$job_arr=return_library_array("select id, job_no_mst from wo_po_break_down",'id','job_no_mst');
	$exc_rate_arr=return_library_array("select job_no, exchange_rate from wo_pre_cost_mst",'job_no','exchange_rate');
	
	$condition = new condition();
	if($orderIds!='' || $orderIds!=0)
	{
		$condition->po_id("in($orderIds)"); 
	}
	
	$condition->init();
	$conversion= new conversion($condition);
	//echo $conversion->getQuery(); die;
	$conversion_costing_arr=$conversion->getAmountArray_by_orderAndProcess();
	
	$budget_amount=0; $job_no='';
	$ex_po_id=array_unique(explode(",",$orderIds));
	foreach($ex_po_id as $po_id)
	{
		$budgetAmt=0;
		$budgetAmt=$conversion_costing_arr[$po_id][31]*$exc_rate_arr[$job_arr[$po_id]];
		$budget_amount+=$budgetAmt;
	}
	
	if($update_id!="") $thisbill_cond=" and a.id!='$update_id'"; else $thisbill_cond="";
	
	$previous_bill_sql=sql_select("select sum(b.amount) as amount from subcon_inbound_bill_mst a, subcon_inbound_bill_dtls b where a.id=b.mst_id and a.party_source=1 and a.process_id=4 and b.order_id in ($orderIds) $thisbill_cond and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0");
	
	$previous_bill_amount=$previous_bill_sql[0][csf('amount')];
	
	$total_bill_amount=$previous_bill_amount+$current_amount;
	$avaible_bill_amount=$budget_amount-$previous_bill_amount;
	
	$msg="Total bill amount exceeding costing amount not allowed.";
	
	if($total_bill_amount>$budget_amount)
	{
		echo "1"."_".rtrim($previous_bill_amount)."_".rtrim($budget_amount)."_".rtrim($avaible_bill_amount)."_".$msg;
		//echo $total_bill_amount."_".$budget_amount."_".$previous_bill_amount."_".$current_amount;
	}
	else
	{
		echo "0"."___";
	}
 	exit();
}


