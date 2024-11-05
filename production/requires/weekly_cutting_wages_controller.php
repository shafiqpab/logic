<?
header('Content-type:text/html; charset=utf-8');
session_start();
if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");

include('../../includes/common.php');
extract($_REQUEST);
//Array List----------------------------------------------------	
	$company_arr = return_library_array("select id, company_name from lib_company where status_active=1 and is_deleted=0 order by company_name","id","company_name");
	$buyer_arr=return_library_array( "select id,short_name from lib_buyer where status_active=1 and is_deleted=0 order by short_name",'id','short_name');
	$supplier_arr=return_library_array( "select id, supplier_name from lib_supplier where status_active=1 and is_deleted=0 order by supplier_name",'id','supplier_name');
	$po_number_arr = return_library_array("select id, po_number from wo_po_break_down where  status_active=1 and is_deleted=0","id","po_number");
	
	$location_details = return_library_array("select id,location_name from lib_location where status_active=1 and is_deleted=0 order by location_name","id","location_name");


//---------------------------------------------------

if($action=="load_drop_down_location")
{
	echo create_drop_down( "cbo_location", 160, "select location_name,id from lib_location where company_id='$data' and is_deleted=0  and status_active=1  order by location_name",'id,location_name', 1, '--- Select Location ---', 0, ""  );
exit();	
}



if ($action=="systemId_popup")
{
	echo load_html_head_contents("System ID Info", "../../", 1, 1,'','','');
	extract($_REQUEST);
?>
	<script>
		function js_set_value(id,sys_id)
		{ 
			$('#hidden_mst_id').val(id+'***'+sys_id);
			parent.emailwindow.hide();
		}
   
        function calculate_date()
		{		
			var thisDate=($('#txt_week_date_from').val()).split('-');
			var in_date=thisDate[2]+'-'+thisDate[1]+'-'+thisDate[0];
			//var days=($('#days_required').val())-1;
			var days=5;
			var date = add_days(in_date,days);	
			var split_date=date.split('-');			
			var res_date=split_date[0]+'-'+split_date[1]+'-'+split_date[2];
			$('#txt_week_date_to').val(res_date);
		}
   
    </script>
    
    
</head>

<body>
<div align="center" style="width:840px;">
    <form name="searchsystemidfrm"  id="searchsystemidfrm">
        <fieldset style="width:830px;">
        <legend>Enter search words</legend>
            <table cellpadding="0" cellspacing="0" width="700" border="1" rules="all" class="rpt_table">
                <thead>
                    <th>SYS Number</th>
                    <th>Location</th>
                    <th>Week From</th>
                    <th>Week To</th>
                    <th>
                        <input type="reset" name="reset" id="reset" value="Reset" style="width:100px;" class="formbutton" />
                        <input type="hidden" name="txt_company_id" id="txt_company_id" value="<? echo $cbo_company_id; ?>">
                        <input type="hidden" id="hidden_mst_id">
					</th>
                </thead>
                <tr>
                    <td>
						<input type="text" style="width:180px;" class="text_boxes"  name="txt_system_no" id="txt_system_no" />
                    </td>
                    <td id="location_td">
						<?
							echo create_drop_down( "cbo_location", 160, $location_details,"", 1, "--Select Location--", 0, "" );
                        ?>
                    </td>
                    <td>
						<input type="text" name="txt_week_date_from" id="txt_week_date_from" class="datepicker" style="text-align:center;width:150px" onChange="calculate_date()" readonly />
                        </td>
                    <td>
						<input type="text" name="txt_week_date_to" id="txt_week_date_to" class="datepicker" style="text-align:center;width:150px" readonly />
                        </td>
                    <td align="center">
                        <input type="button" name="button2" class="formbutton" value="Show" onClick="show_list_view ( document.getElementById('txt_company_id').value+'_'+document.getElementById('txt_system_no').value+'_'+document.getElementById('cbo_location').value+'_'+document.getElementById('txt_week_date_from').value+'_'+document.getElementById('txt_week_date_to').value, 'system_id_list_view', 'search_div', 'weekly_cutting_wages_controller', 'setFilterGrid(\'tbl_list_search\',-1);')" style="width:100px;" />
                    </td>
                </tr>
            </table>
            <table width="100%" style="margin-top:5px;">
                <tr>
                    <td colspan="5">
                        <div style="margin-top:10px; margin-left:3px;" id="search_div" align="left"></div>
                    </td>
                </tr>
            </table>
    	</fieldset>
    </form>
</div>
</body>
<script src="../../includes/functions_bottom.js" type="text/javascript"></script>
<script type="text/javascript">
	load_drop_down('weekly_cutting_wages_controller',<? echo $cbo_company_id;?>, 'load_drop_down_location', 'location_td' );
</script>

</html>
<?
exit();
}





if($action=="system_id_list_view")
{
	list($company_id,$sys_number,$location,$from_date,$to_date)=explode("_",$data);	
		
	if($sys_number=='')$sys_number="a.sys_number like('%%')"; else $sys_number="a.sys_number like ('%$sys_number%')";	
	if($location==0)$location="a.location like('%%')"; else $location="a.location =$location";	
	
	
	if($from_date!='' && $to_date!=''){
		if($db_type==0){
			
			$from_date=change_date_format($from_date);
			$to_date=change_date_format($to_date);
		}
		else
		{
			$from_date=change_date_format($from_date,'','',-1);
			$to_date=change_date_format($to_date,'','',-1);
		}
		$date_con_from="and a.week_from_date BETWEEN '$from_date' and '$to_date'";
		$date_con_to="and a.week_to_date BETWEEN '$from_date' and '$to_date'";
	}
	else
	{
		$date_con_from="";	
		$date_con_to="";	
	}


	$sql = "select a.id,a.sys_number,a.company_id,a.location,a.bill_for,a.final_bill,a.week_from_date,a.week_to_date,b.emp_name from pro_weekly_wages_bill_mst a, pro_weekly_wages_bill_dtls b where a.id=b.mst_id and a.company_id=$company_id and $location and $sys_number and a.bill_for=20 $date_con_from $date_con_to and a.status_active=1 and a.is_deleted=0 group by a.id,a.sys_number,a.company_id,a.location,a.bill_for,a.final_bill,b.emp_name,a.week_from_date,a.week_to_date order by a.id"; 

	$result = sql_select($sql);

	?>
    <table cellspacing="0" cellpadding="0" border="1" rules="all" width="820" class="rpt_table">
        <thead>
            <th width="50">SL</th>
            <th width="130">Sys Number</th>
            <th width="150">Service Provider</th>
            <th width="150">Location</th>
            <th width="110">From Date</th>
            <th width="110">To Date</th>
            <th>Final Bill</th>
        </thead>
	</table>
	<div style="width:815px; max-height:220px; overflow-y:scroll" id="list_container_batch" align="left">	 
        <table cellspacing="0" cellpadding="0" border="1" rules="all"  width="797" class="rpt_table" id="tbl_list_search">  
        <?
            $i=1;
            foreach ($result as $row)
            {  
                $bgcolor=($i%2==0)?"#E9F3FF":"#FFFFFF";	 
			?>
                <tr id="tr_<? echo $row[csf('id')]; ?>" bgcolor="<? echo $bgcolor; ?>" style="text-decoration:none; cursor:pointer"  onClick="js_set_value(<? echo $row[csf('id')]; ?>,'<? echo $row[csf('sys_number')]; ?>')" > 
                    <td width="50" align="center"><? echo $i; ?></td>
                    <td width="130" align="center"><p><? echo $row[csf('sys_number')]; ?></p></td>
                    <td width="150"><p><? echo implode(',',array_unique(explode(',',$row[csf('emp_name')]))); ?></p></td>
                    <td width="150"><p><? echo $location_details[$row[csf('location')]]; ?></p></td>
                    <td width="110" align="center"><? echo $row[csf('week_from_date')]; ?></td>
                    <td width="110" align="center"><? echo $row[csf('week_to_date')]; ?></td>
                    <td align="center"><p><? echo $yes_no[$row[csf('final_bill')]]; ?></p></td>
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



if ($action=="employee_info_popup")
{
	echo load_html_head_contents("Employee Info", "../../", 1, 1,'','','');
	extract($_REQUEST);
?>
	<script>
		function js_set_value(id)
		{ 
			$('#hidden_emp_id').val(id);
			parent.emailwindow.hide();
		}
    </script>
    
    
</head>

<body>
<div align="center" style="width:500px;">
    <form name="searchsystemidfrm"  id="searchsystemidfrm">
        <fieldset style="width:500px;">
        <legend>Enter search words</legend>
            <table cellpadding="0" cellspacing="0" width="100%" border="1" rules="all" class="rpt_table">
                <thead>
                    <th>Category</th>
                    <th>Search By</th>
                    <th>Enter Employee Name</th>
                    <th>
                        <input type="reset" name="reset" id="reset" value="Reset" style="width:100px;" class="formbutton" />
                        <input type="hidden" name="txt_company_id" id="txt_company_id" value="<? echo $cbo_company_id; ?>">
                        <input type="hidden" id="hidden_emp_id">
					</th>
                </thead>
                <tr>
                    <td>
						<?
							echo create_drop_down( "cbo_category", 150, $rate_for,"", 1,"-- Select --", $cbo_bill_for,"","","20,30,35,40");
                        ?>
                    </td>
                    <td>
						<?
							echo create_drop_down( "cbo_search_by", 150, $xxxx,"", 1, "-- Select --", 0, "",0 );
                        ?>
                     </td>
                    <td>
						<input type="text" style="width:150px;" class="text_boxes"  name="txt_style_no" id="txt_style_no" />
                        </td>
                    <td align="center">
                        <input type="button" name="button2" class="formbutton" value="Show" onClick="show_list_view ( <? echo $cbo_company_id; ?>+'_'+document.getElementById('cbo_category').value, 'enployee_info_list_view', 'search_div', 'weekly_cutting_wages_controller', 'setFilterGrid(\'tbl_list_search\',-1);')" style="width:100px;" />
                    </td>
                </tr>
            </table>
            <div style="margin-top:5px;" id="search_div"></div>
    	</fieldset>
    </form>
    
    
</div>
</body>
<script src="../../includes/functions_bottom.js" type="text/javascript"></script>
</html>
<?
exit();
}






if($action=="enployee_info_list_view")
{
list($company,$buyer_id)=explode("_",$data);	
	
	
	//$sql = "select * from lib_employee where company_id='$company'";
	
$sql="SELECT a.id,a.supplier_name,a.party_type,a.designation FROM lib_supplier a,lib_supplier_party_type b,lib_supplier_tag_company c WHERE a.id=b.supplier_id and a.id=c.supplier_id and b.party_type =36 and c.tag_company =$company order by a.id";
	
	
	
	$result = sql_select($sql);
	?>
    <table cellspacing="0" cellpadding="0" border="1" rules="all" width="568" class="rpt_table">
        <thead>
            <th width="50">SL</th>
            <th width="100">ID</th>
            <th width="150">Name</th>
            <th width="100">Designation</th>
            <th>Party</th>
        </thead>
	</table>
	<div style="width:568px; max-height:220px; overflow-y:scroll">	 
        <table cellspacing="0" cellpadding="0" border="1" rules="all"  width="550" class="rpt_table" id="tbl_list_search">  
        <?
            $i=1;
            foreach ($result as $row)
            {  
                $bgcolor=($i%2==0)?"#E9F3FF":"#FFFFFF";	 
			?>
                <tr id="tr_<? echo $row[csf('id')]; ?>" bgcolor="<? echo $bgcolor; ?>" style="text-decoration:none; cursor:pointer"  onClick="js_set_value(<? echo $row[csf('id')]; ?>)" > 
                    <td width="50" align="center"><? echo $i; ?></td>
                    <td width="100" align="center"><p><? echo $row[csf('id')]; ?></p></td>
                    <td width="150"><p><? echo $row[csf('supplier_name')]; ?></p></td>
                    <td width="100"><p><? echo $row[csf('designation')]; ?></p></td>
                    <td><p><? echo $row[csf('department_id')]; ?></p></td>
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

if ($action=="order_info_popup")
{
	echo load_html_head_contents("Order Info", "../../", 1, 1,'','','');
	extract($_REQUEST);
	list($company_id)=explode('_',$mst_data); 
	?>
	<script>
	
		function change_color( v_id, origColor ) {
			var x=document.getElementById("tr_"+v_id);
			var newColor = 'yellow';
			if ( x.style ) {
				x.style.backgroundColor = ( newColor == x.style.backgroundColor )? origColor : newColor;
			}
		}	
	
		
		 var selected_id=Array();      
		 var selected_job=Array();      
		 var selected_rate=Array();      
		 var selected_rate_variable=Array();      
		 var selected_style=Array();      
		 var selected_grm_item=Array();      
		 var alls_arr=Array();      
        function js_set_value(selectID,rate,rate_variable,style_ref,grm_item,jobID,bgcolor) 
		{
			
			
			if((jQuery.inArray( jobID, selected_job ) == -1) && (selected_job.length!=0) ) {
				alert('Job Mix Not Allowed'); return;
			}
			else if((jQuery.inArray( rate, selected_rate ) == -1) && (selected_rate.length!=0) ) {
				alert('Rate Mix Not Allowed'); return;
			}
			else if((jQuery.inArray( rate_variable, selected_rate_variable ) == -1) && (selected_rate_variable.length!=0) ) {
				alert('Rate Variable Mix Not Allowed'); return;
			}
			else if(rate==''){
				alert("Check Cutting Entry & Work Order Rate"); return;	
			}
			else
			{
				var tr_id=selectID+style_ref+grm_item;
				change_color(tr_id,bgcolor);
				
				var alls_data=selectID+'_'+jobID+'_'+rate+'_'+rate_variable+'_'+style_ref+'_'+grm_item;			
			
				if( jQuery.inArray( alls_data, alls_arr ) == -1) {
					selected_id.push( selectID );
					selected_job.push( jobID );
					selected_rate.push( rate );
					selected_rate_variable.push( rate_variable );
					selected_style.push( style_ref );
					selected_grm_item.push( grm_item );
					alls_arr.push( alls_data );
				}
				else {
					for( var i = 0; i < alls_arr.length; i++ ) {
						if( alls_arr[i] == alls_data) break;
					}
					selected_id.splice( i, 1 );
					selected_job.splice( i, 1 );
					selected_rate.splice( i, 1 );
					selected_rate_variable.splice( i, 1 );
					selected_style.splice( i, 1 );
					selected_grm_item.splice( i, 1 );
					alls_arr.splice( i, 1 );
				}
				var id =job=rate=rate_var = style=items=alls='';
				for( var i = 0; i < selected_id.length; i++ ) {
					id += selected_id[i] + ',';
					job += selected_job[i] + ',';
					rate += "'"+selected_rate[i] + "',";
					rate_var += selected_rate_variable[i] + ',';
					style += selected_style[i] + ',';
					items += selected_grm_item[i] + ',';
					alls += alls_arr[i] + ',';
				}
				
				
				id = id.substr( 0, id.length - 1 );
				job = job.substr( 0, job.length - 1 );
				rate = rate.substr( 0, rate.length - 1 );
				rate_var = rate_var.substr( 0, rate_var.length - 1 );
				style = style.substr( 0, style.length - 1 );
				items = items.substr( 0, items.length - 1 );
				alls = alls.substr( 0, alls.length - 1 );
				
				$('#hidden_order_id').val( id );
				$('#hidden_job_id').val( job );
				$('#hidden_rate').val( rate );
				$('#hidden_rate_var_id').val( rate_var );
				$('#hidden_style_ref').val( style );
				$('#hidden_item_id').val( items );
				$('#hidden_all').val( alls );
			}
		}

		
		
		function fn_close_window(){
			parent.emailwindow.hide();
		}
    </script>
    
    
</head>

<body>
<div align="center" style="width:940px;">
    <form name="searchsystemidfrm"  id="searchsystemidfrm">
        <fieldset>
        <legend>Enter search words</legend>
            <table cellpadding="0" cellspacing="0" width="700" border="1" rules="all" class="rpt_table">
                <thead>
                    <th>Company</th>
                    <th>Buyer Name</th>
                    <th>Job Number</th>
                    <th>Order No</th>
                    <th>Style</th>
                    <th>
                        <input type="reset" name="reset" id="reset" value="Reset" style="width:100px;" class="formbutton" />
                        <input type="hidden" name="mst_data" id="mst_data" value="<? echo $mst_data; ?>">
                        <input type="hidden" id="hidden_order_id" value="">
                        <input type="hidden" id="hidden_job_id">
                        <input type="hidden" id="hidden_rate">
                        <input type="hidden" id="hidden_rate_var_id">
                        <input type="hidden" id="hidden_style_ref">
                        <input type="hidden" id="hidden_item_id">
                        <input type="hidden" id="hidden_all">
					</th>
                </thead>
                <tr>
                    <td>
						<?
							echo create_drop_down( "cbo_company_id", 150, $company_arr,"", 1, "-- All Select --", $company_id, "",1 );
                        ?>
                    </td>
                    <td>
						<?
							echo create_drop_down( "cbo_buyer_id", 150, $buyer_arr,"", 1, "-- All Select --", 0, "",0 );
                        ?>
                    </td>
                    <td>
						<input type="text" style="width:130px;" class="text_boxes"  name="txt_job_no" id="txt_job_no" />
                    </td>
                    <td>
						<input type="text" style="width:100px;" class="text_boxes"  name="txt_order_no" id="txt_order_no" />
                    </td>
                    <td>
						<input type="text" style="width:130px;" class="text_boxes"  name="txt_style_no" id="txt_style_no" />
                        </td>
                    <td align="center">
                        <input type="button" name="button2" class="formbutton" value="Show" onClick="show_list_view ( document.getElementById('mst_data').value+'_'+document.getElementById('cbo_buyer_id').value+'_'+document.getElementById('txt_job_no').value+'_'+document.getElementById('txt_style_no').value+'_'+document.getElementById('txt_order_no').value+'_'+'<? echo $txt_order_id;?>'+'_'+'<? echo $txt_gmt_item_id;?>', 'order_no_list_view', 'search_div', 'weekly_cutting_wages_controller', 'setFilterGrid(\'tbl_list_search\',-1);')" style="width:100px;" />
                    </td>
                </tr>
            </table>
            <table width="100%" style="margin-top:5px;">
                <tr>
                    <td colspan="5">
                        <div style="margin-top:10px; margin-left:3px;" id="search_div" align="left"></div>
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
exit();
}



if($action=="order_no_list_view")
{

list($company,$rate_for,$from_date,$to_date,$location,$shift,$supplier_id,$buyer,$job_no,$style,$order_no,$order_id,$gmt_item_name)=explode("_",$data);	
if($job_no=='')$job=""; else $job="and b.job_no_mst like ('%$job_no')";	
if($buyer==0)$buyer_id=""; else $buyer_id="and a.buyer_name='$buyer'";	
if($style=="")$style_ref=""; else $style_ref="and a.style_ref_no='$style'";	
if($order_no=="")$order_no=""; else $order_no="and b.po_number like('%".trim($order_no)."')";
if($order_id=="")$order_id_con=""; else $order_id_con="and c.order_id not in('$order_id')";	

$gmt_item_name=str_replace("'","",$gmt_item_name);
if($gmt_item_name=="")$gmt_item_name_con=""; else $gmt_item_name_con="and c.gmt_item_id not in('$gmt_item_name')";	

if($location==""){
	$location_con_a="";
	$location_con_d="";
	$location_con_c="";
}
else{
	$location_con_a="and a.location = '$location'";
	$location_con_d="and d.location = '$location'";
	$location_con_c="and c.location = '$location'";
}
	
if($buyer==0)$buyer_2=""; else $buyer_2=$buyer;
	
	if($db_type==0){
		
		$from_date=change_date_format($from_date);
		$to_date=change_date_format($to_date);
	}
	else
	{
		$from_date=change_date_format($from_date,'','',-1);
		$to_date=change_date_format($to_date,'','',-1);
	}
	
	
	 	//$sql = "select b.order_id,sum(b.bill_qty ) as bill_qty  from pro_weekly_wages_bill_mst a,pro_weekly_wages_order_brk b where a.id=b.weekly_wages_mst_id $location_con_a and a.company_id='$company' and a.bill_for=20 and a.week_from_date BETWEEN '$from_date' and '$to_date' group by b.order_id"; 
	
	$sql = "select c.order_id,a.location,c.bill_qty as bill_qty,c.gmt_item_id from pro_weekly_wages_bill_mst a, pro_weekly_wages_bill_dtls b, pro_weekly_wages_order_brk c 
where a.id=b.mst_id and b.id=c.weekly_wages_dtls_id and a.location='$location' and
  a.company_id='$company' and a.bill_for=20 and a.week_from_date >= '$from_date' and  a.week_to_date <='$to_date' $order_id_con $gmt_item_name_con and a.status_active=1 and a.is_deleted=0  and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0";

	$result = sql_select($sql);
	foreach ($result as $row)
	{ 
	 $key=$row[csf('order_id')].$row[csf('location')].$row[csf('gmt_item_id')];
	 $bill_qty_arr[$key]+=$row[csf('bill_qty')];

	}

	
	 	$sql = "select b.color_type,b.item_id,b.avg_rate,b.style_ref,b.order_id,b.wo_qty,b.uom,b.amount from piece_rate_wo_mst a,piece_rate_wo_dtls b,wo_po_details_master c,pro_garments_production_mst d where a.id=b.mst_id and b.job_id=c.id and d.po_break_down_id=b.order_id  and d.production_type=1 and a.company_id='$company' and a.rate_for='$rate_for' and b.style_ref like '%$style%' and b.buyer_id like '%$buyer_2%' and c.job_no like '%$job_no%' and d.production_date BETWEEN '$from_date' and '$to_date'  and a.status_active=1 and a.is_deleted=0  and b.status_active=1 and b.is_deleted=0  and c.status_active=1 and c.is_deleted=0"; //$location_con_d and a.service_provider_id='$supplier_id'
	
	$result = sql_select($sql);
		foreach ($result as $row)
		{ 
		 $key=$row[csf('order_id')].$row[csf('style_ref')].$row[csf('item_id')];
		 $wo_qty_arr[$key]=$row[csf('wo_qty')];
		 $wo_uom_arr[$key]=$unit_of_measurement[$row[csf('uom')]];
		 $wo_amount_arr[$key]=$row[csf('amount')];
		 $wo_rate_arr[$key]=$row[csf('avg_rate')];
		 $rate_variable_arr[$key]=$row[csf('color_type')];
		}
	
/*	$sql = "select a.id as job_id,b.id,a.company_name,a.buyer_name,a.style_ref_no,c.item_number_id,b.job_no_mst,b.po_number,sum(b.po_quantity) as po_quantity from wo_po_details_master a,wo_po_break_down b,pro_garments_production_mst c where a.job_no=b.job_no_mst $location_con_c and a.company_name='$company' $buyer_id  $style_ref $job and c.po_break_down_id=b.id  and c.production_date BETWEEN '$from_date' and '$to_date' and c.production_type=1  and a.status_active=1 and a.is_deleted=0  and b.status_active=1 and b.is_deleted=0 $order_no group by a.id,a.company_name,a.style_ref_no,c.item_number_id,a.buyer_name,b.job_no_mst,b.id,b.po_number order by a.id";*/
	
$sql = "select a.id as job_id,b.id,a.company_name,a.buyer_name,a.style_ref_no,c.item_number_id,b.job_no_mst,b.po_number,(b.po_quantity) as po_quantity from wo_po_details_master a,wo_po_break_down b,pro_garments_production_mst c where a.job_no=b.job_no_mst $location_con_c and a.company_name='$company' $buyer_id  $style_ref $job and c.po_break_down_id=b.id  and c.production_date BETWEEN '$from_date' and '$to_date' and c.production_type=1  and a.status_active=1 and a.is_deleted=0  and b.status_active=1 and b.is_deleted=0 $order_no group by a.id,a.company_name,a.style_ref_no,c.item_number_id,a.buyer_name,b.job_no_mst,b.id,b.po_number,b.po_quantity  order by a.id";	
	
	$result = sql_select($sql);
	?>
    <table cellspacing="0" cellpadding="0" border="1" rules="all" width="915" class="rpt_table">
        <thead>
            <th width="35">SL</th>
            <th width="80">Job No</th>
            <th width="100">Order No</th>
            <th width="100">Company</th>
            <th width="50">Buyer</th>
            <th width="80">Style Ref</th>
            <th width="100">Item</th>
            <th width="80">PO Qty</th>
            <th width="70">Rate Variables</th>
            <th width="50">Avg. Rate</th>
            <th width="80">Amount</th>
            <th>UOM</th>
        </thead>
	</table>
	<div style="width:915px; max-height:220px; overflow-y:scroll" id="list_container_batch" align="left">	 
        <table cellspacing="0" cellpadding="0" border="1" rules="all"  width="897" class="rpt_table" id="tbl_list_search">  
        <?
            $i=1;
            foreach ($result as $row)
            {  
				$key=$row[csf('id')].$location.$row[csf('item_number_id')];
				if($bill_qty_arr[$key]==''){
				$key2=$row[csf('id')].$row[csf('style_ref_no')].$row[csf('item_number_id')];
				$bgcolor=($i%2==0)?"#E9F3FF":"#FFFFFF";	
				
				if(in_array($row[csf('id')],explode(',',$order_id)) && in_array($row[csf('item_number_id')],explode(',',$gmt_item_name))){$bgcolor="#F9DA42";} 
			?>
                <tr id="tr_<? echo $key2; ?>" bgcolor="<? echo $bgcolor; ?>" style="text-decoration:none; cursor:pointer"  onClick="js_set_value(<? echo $row[csf('id')]; ?>,'<? echo $wo_rate_arr[$key2]; ?>','<? echo $rate_variable_arr[$key2]; ?>','<? echo $row[csf('style_ref_no')]; ?>','<? echo $row[csf('item_number_id')]; ?>','<? echo $row[csf('job_id')]; ?>','<? echo $bgcolor; ?>')" > 
                    <td width="35" align="center"><? echo $i; ?></td>
                    <td width="80" align="center"><p><? echo $row[csf('job_no_mst')]; ?></p></td>
                    <td width="100" align="center"><p><? echo $row[csf('po_number')]; ?></p></td>
                    <td width="100"><p><? echo $company_arr[$row[csf('company_name')]]; ?></p></td>
                    <td width="50" align="center"><p><? echo $buyer_arr[$row[csf('buyer_name')]]; ?></p></td>
                    <td width="80"><p><? echo $row[csf('style_ref_no')]; ?></p></td>
                    <td width="100"><p><? echo $garments_item[$row[csf('item_number_id')]]; ?></p></td>
                    <td width="80" align="right"><p><? echo $row[csf('po_quantity')]; ?></p></td>
                    <td width="70" align="center"><p><? echo $color_type[$rate_variable_arr[$key2]]; ?></p></td>
                    <td width="50" align="right"><p><? echo $wo_rate_arr[$key2]; ?></p></td>
                    <td width="80" align="right"><p><? echo number_format($wo_amount_arr[$key2],2 ,".", ""); ?></p></td>
                    <td align="center"><p><? echo $wo_uom_arr[$key2]; ?></p></td>
                </tr>
        	<?
            $i++;
			   }
            }
        	?>
        </table>
    </div>
    <table width="100%"><tr><td align="center"><input style="width:100px;" class="formbutton" type="button" onClick="fn_close_window()" value="Close"/></td></tr></table>

<?


exit();
}





if($action=="show_weekly_wages_bill_listview")
{
	
	$sql = "select b.id,b.mst_id,b.emp_id,b.emp_name,b.designation,b.order_id,b.buyer_id,b.style_ref,b.gmt_item,b.apv_order_qty,b.cutting_bill_qty,b.previous_bill_qty,b.yet_to_bill_qty from pro_weekly_wages_bill_dtls b where b.mst_id=$data and b.status_active=1 and b.is_deleted=0 order by b.id";
	$result = sql_select($sql);
	?>
    <table cellspacing="0" cellpadding="0" border="1" rules="all" width="850" class="rpt_table">
        <thead>
            <th width="35">SL</th>
            <th width="80">Provider id</th>
            <th width="100">Provider Name</th>
            <th width="100">Order No</th>
            <th width="50">Buyer</th>
            <th width="80">Style</th>
            <th width="100">Item</th>
            <th width="60">WO Qty</th>
            <th width="60">Bill Qty</th>
            <th width="60">Pre. Bill Qty</th>
            <th>Yet to Bill Qty</th>
        </thead>
	</table>
    
	<div style="width:850px; max-height:220px; overflow-y:scroll" id="list_container_batch" align="left">	 
        <table cellspacing="0" cellpadding="0" border="1" rules="all"  width="832" class="rpt_table" id="tbl_list_search">  
        <?
            $i=1;
            foreach ($result as $row)
            {  
                $bgcolor=($i%2==0)?"#E9F3FF":"#FFFFFF";	 
			
				$order_id_arr=array_unique(explode(",",$row[csf('order_id')]));
				$order_id='';
				foreach($order_id_arr as $val)
				{
				$order_id.=$po_number_arr[$val].',';
				}
				$order_id=substr($order_id,0,-1);
				
			
			?>
                <tr id="tr_<? echo $row[csf('id')]; ?>" bgcolor="<? echo $bgcolor; ?>" style="text-decoration:none; cursor:pointer"  onClick="get_php_form_data('<? echo $row[csf('mst_id')].'_'.$row[csf('id')];?>', 'populate_cutting_wages_dtls_form_data','requires/weekly_cutting_wages_controller')" > 
                    <td width="35" align="center"><? echo $i; ?></td>
                    <td width="80" align="center"><p><? echo $row[csf('emp_id')];?></p></td>
                    <td width="100"><p><? echo $row[csf('emp_name')];?></p></td>
                    <td width="100"><p><? echo $order_id;?></p></td>
                    <td width="50"><p><? echo $buyer_arr[$row[csf('buyer_id')]];?></p></td>
                    <td width="80"><p><? echo $row[csf('style_ref')]; ?></p></td>
                    <td width="100"><p><? echo $row[csf('gmt_item')]; ?></p></td>
                    <td width="60" align="right"><p><? echo number_format($row[csf('apv_order_qty')],2);?></p></td>
                    <td width="60" align="right"><p><? echo number_format($row[csf('cutting_bill_qty')],2);?></p></td>
                    <td width="60" align="right"><p><? echo number_format($row[csf('previous_bill_qty')],2);?></p></td>
                    <td align="right"><? echo number_format($row[csf('yet_to_bill_qty')],2);?></td>
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

if($action=='populate_cutting_wages_mst_form_data')
{
	$sql = "select id,company_id, final_bill,location,division_id, department_id, shift, floor_id, week_from_date, week_to_date from pro_weekly_wages_bill_mst where id='$data'";
	$result = sql_select($sql);
	foreach($result as $row){
		echo "document.getElementById('update_id').value	= '".$row[csf("id")]."';\n";
		echo "document.getElementById('cbo_company_id').value	= '".$row[csf("company_id")]."';\n";
		echo "document.getElementById('cbo_final_bill').value	= '".$row[csf("final_bill")]."';\n";
		echo "document.getElementById('cbo_location').value		= '".$row[csf("location")]."';\n";
		echo "document.getElementById('cbo_division').value		= '".$row[csf("division_id")]."';\n";
		echo "document.getElementById('cbo_department').value	= '".$row[csf("department_id")]."';\n";
		echo "document.getElementById('cbo_shift').value		= '".$row[csf("shift")]."';\n";
		echo "document.getElementById('txt_week_date_from').value= '".change_date_format($row[csf("week_from_date")])."';\n";
		echo "document.getElementById('txt_week_date_to').value	= '".change_date_format($row[csf("week_to_date")])."';\n";
	}
	exit();
}


if($action=='populate_cutting_wages_dtls_form_data')
{
	list($mst_id,$dtls_id)=explode("_",$data);
	
	//$sql = "select a.id,b.id as dtls_id,b.emp_id,b.emp_name,b.designation,b.order_id,b.buyer_id,b.apv_order_qty,b.cutting_bill_qty,b.previous_bill_qty,b.yet_to_bill_qty,b.style_ref,b.gmt_item,b.wo_rate,b.amount,b.deducted_emp,b.net_amount,b.rate_variables,b.cutting_bill_uom, b.rate_uom, b.previous_bill_uom, b.yet_to_bill_uom,b.deducted_qty from pro_weekly_wages_bill_mst a,pro_weekly_wages_bill_dtls b where a.id='$mst_id' and a.id=b.mst_id and b.id='$dtls_id'";
	
	$sql = "SELECT a.id,b.id as dtls_id,b.emp_id,b.emp_name,b.designation,b.order_id,b.buyer_id,b.apv_order_qty,b.cutting_bill_qty,b.previous_bill_qty,b.yet_to_bill_qty,b.style_ref,b.gmt_item,b.wo_rate,b.amount,b.deducted_emp,b.net_amount,b.rate_variables,b.cutting_bill_uom, b.rate_uom, b.previous_bill_uom, b.yet_to_bill_uom,b.deducted_qty, 
LISTAGG( CAST(c.gmt_item_id as VARCHAR(4000)),',') WITHIN GROUP(ORDER BY c.gmt_item_id) AS gmt_item_id
from
	 pro_weekly_wages_bill_mst a,
	 pro_weekly_wages_bill_dtls b,
	 pro_weekly_wages_order_brk c
where 
	a.id='$mst_id' and a.id=b.mst_id and b.id='$dtls_id'
	and c.weekly_wages_dtls_id=b.id
	and c.weekly_wages_mst_id =a.id
group by 
	a.id,
	b.id,
	b.emp_id,
	b.emp_name,
	b.designation,
	b.order_id,
	b.buyer_id,
	b.apv_order_qty,
	b.cutting_bill_qty,
	b.previous_bill_qty,
	b.yet_to_bill_qty,
	b.style_ref,
	b.gmt_item,
	b.wo_rate,
	b.amount,
	b.deducted_emp,
	b.net_amount,
	b.rate_variables,
	b.cutting_bill_uom,
	b.rate_uom,
	b.previous_bill_uom,
	b.yet_to_bill_uom,
	b.deducted_qty
	";
	
	//echo $sql;die;
	
	$result = sql_select($sql);
	foreach($result as $row){
		echo "document.getElementById('update_id').value = '".$row[csf("id")]."';\n";
		echo "document.getElementById('dtls_update_id').value = '".$row[csf("dtls_id")]."';\n";
		echo "document.getElementById('txt_emp_code').value	= '".$row[csf("emp_id")]."';\n";
		echo "document.getElementById('txt_emp_card_no').value = '".$row[csf("emp_id")]."';\n";
		echo "document.getElementById('txt_emp_name').value	= '".$row[csf("emp_name")]."';\n";
		
			$order_id_arr=array_unique(explode(",",$row[csf('order_id')]));
			foreach($order_id_arr as $val)
			{
			$order_no.=$po_number_arr[$val].',';	
			}
			$order_no=substr($order_no,0,-1);
		echo "document.getElementById('txt_order_no').value	= '".$order_no."';\n";
		echo "document.getElementById('txt_order_id').value	= '".implode(',',$order_id_arr)."';\n";
		
		echo "document.getElementById('cbo_buyer_name').value	= '".$row[csf("buyer_id")]."';\n";
		echo "document.getElementById('txt_style_ref_no').value	= '".$row[csf("style_ref")]."';\n";
		
		$gmt_item_id_arr=implode(',',array_unique(explode(",",$row[csf('gmt_item_id')])));
		$gmt_item_arr=implode(',',array_unique(explode(",",$row[csf('gmt_item')])));
		
		echo "document.getElementById('txt_gmt_item_name').value	= '".$gmt_item_arr."';\n";
		echo "document.getElementById('txt_gmt_item_id').value	= '".$gmt_item_id_arr."';\n";
		echo "document.getElementById('txt_appv_wo_order_qty').value	= '".$row[csf("apv_order_qty")]."';\n";
		echo "document.getElementById('cbo_wages_rate_variables').value	= '".$row[csf("rate_variables")]."';\n";
		echo "document.getElementById('txt_cutting_bill_qty').value	= '".$row[csf("cutting_bill_qty")]."';\n";
		echo "document.getElementById('txt_wo_rate').value	= '".$row[csf("wo_rate")]."';\n";
		echo "document.getElementById('txt_amount').value	= '".$row[csf("amount")]."';\n";
		echo "document.getElementById('tex_emp_deduct_amount').value	= '".$row[csf("deducted_emp")]."';\n";
		echo "document.getElementById('txt_net_amount').value	= '".$row[csf("net_amount")]."';\n";
		echo "document.getElementById('txt_previous_bill_qty').value	= '".$row[csf("previous_bill_qty")]."';\n";
		echo "document.getElementById('txt_yet_to_bill_qty').value	= '".$row[csf("yet_to_bill_qty")]."';\n";
		
		echo "document.getElementById('cbo_cutting_bill_uom').value	= '".$row[csf("cutting_bill_uom")]."';\n";
		echo "document.getElementById('cbo_wo_rate_uom').value	= '".$row[csf("rate_uom")]."';\n";
		echo "document.getElementById('cbo_previous_bill_qty_uom').value	= '".$row[csf("previous_bill_uom")]."';\n";
		echo "document.getElementById('cbo_yet_to_bill_qty_uom').value	= '".$row[csf("yet_to_bill_uom")]."';\n";
	
	
		echo "document.getElementById('txt_deducted_qty_hidden').value	= '".$row[csf("deducted_qty")]."';\n";

	
	
	}
	

	//Details break down data-----------
	$sql = "select order_id,gmt_item_id, approved_wo_qty, bill_qty, bill_safty_qty, bill_safty_rate, bill_safty_amount, amount, previous_bill_qty, yet_to_bill_qty from pro_weekly_wages_order_brk where weekly_wages_mst_id ='$mst_id' and weekly_wages_dtls_id='$dtls_id'";
	$result = sql_select($sql);
	$order_bread_down_data='';
	foreach($result as $row)
	{
	$order_bread_down_data.=$row[csf("order_id")].'**'.$row[csf("gmt_item_id")].'**'.$row[csf("bill_qty")].'**'.$row[csf("bill_safty_rate")].'**'.$row[csf("bill_safty_qty")].'**'.$row[csf("amount")].'**'.$row[csf("approved_wo_qty")].'**'.$row[csf("previous_bill_qty")].'**'.$row[csf("yet_to_bill_qty")].'__';
	}
 	$order_bread_down_data=substr($order_bread_down_data,0,-2);
	echo "document.getElementById('hidden_order_break_down').value	= '".$order_bread_down_data."';\n";

	echo "set_button_status(1, permission, 'fnc_weekly_wages_bill_entry',1);";

	exit();
}




if($action=='populate_emp_info')
{
	$sql = "select id,supplier_name,designation from lib_supplier where id=$data";
	$data_array=sql_select($sql);
	foreach ($data_array as $row)
	{ 
		echo "document.getElementById('txt_emp_code').value	= '".$row[csf("id")]."';\n";
		echo "document.getElementById('txt_emp_card_no').value	= '".$row[csf("id")]."';\n";
		echo "document.getElementById('txt_emp_name').value	= '".$row[csf("supplier_name")]."';\n";
		exit();
	}
	
}





if($action=='populate_order_data')
{ 
	//list($po_id,$job_id,$rate,$rate_var,$style,$items,$company,$location,$bill_for,$final_bill,$from_date,$to_date,$supplier_id,$dtls_update_id)=explode("_",str_replace("'",'',$data));
	list($po_id,$job_id,$rate,$rate_var,$style,$items,$company,$location,$bill_for,$final_bill,$from_date,$to_date,$supplier_id,$dtls_update_id)=explode("**",str_replace("'",'',$data));
	
if($dtls_update_id)$dtls_update_id_con="and b.id not in($dtls_update_id)"; else $dtls_update_id_con=""; 		

	if($location==""){
		$location_con_a="";
		$location_con_d="";
		$location_con_c="";
	}
	else{
		$location_con_a="and a.location = '$location'";
		$location_con_d="and d.location = '$location'";
		$location_con_c="and c.location = '$location'";
	}
	
	$seftyParcentArray= sql_select("SELECT cut_sefty_parcent from variable_settings_production where company_name='$company' and variable_list=30  and status_active=1 and is_deleted=0");
	
	$total_order=count(explode(',',$po_id));
	$deducted_parcent=$seftyParcentArray[0][csf("cut_sefty_parcent")]; //$deducted_parcent assicen 5 for deduct cut qty;
	
	if($db_type==0){
		
		$from_date=change_date_format($from_date);
		$to_date=change_date_format($to_date);
	}
	else
	{
		$from_date=change_date_format($from_date,'','',-1);
		$to_date=change_date_format($to_date,'','',-1);
	}

//--------------------------------------------------------------------------	
	if($db_type==0){
		$sql = "select a.job_no,a.buyer_name,a.style_ref_no,GROUP_CONCAT(c.item_number_id) as item_number_id,GROUP_CONCAT(b.po_number) AS po_number,c.location  from wo_po_details_master a,wo_po_break_down b,pro_garments_production_mst c where a.job_no=b.job_no_mst and b.id=c.po_break_down_id and b.id in($po_id) and a.id in($job_id) and c.item_number_id in($items) $location_con_c and c.production_type=1 and c.production_date BETWEEN '$from_date' and '$to_date'  and a.status_active=1 and a.is_deleted=0  and b.status_active=1 and b.is_deleted=0  and c.status_active=1 and c.is_deleted=0 group by a.job_no,c.location,a.buyer_name,a.style_ref_no"; 
	}
	else
	{
		$sql = "select a.job_no,a.buyer_name,a.style_ref_no,LISTAGG( CAST(c.item_number_id as VARCHAR(4000)),',') WITHIN GROUP(ORDER BY c.item_number_id) AS item_number_id,LISTAGG( CAST(b.po_number as VARCHAR(4000)),',') WITHIN GROUP(ORDER BY b.po_number) AS po_number,c.location  from wo_po_details_master a,wo_po_break_down b,pro_garments_production_mst c where a.job_no=b.job_no_mst and b.id=c.po_break_down_id and b.id in($po_id) and a.id in($job_id) and c.item_number_id in($items) $location_con_c and c.production_type=1 and c.production_date BETWEEN '$from_date' and '$to_date'  and a.status_active=1 and a.is_deleted=0  and b.status_active=1 and b.is_deleted=0  and c.status_active=1 and c.is_deleted=0 group by c.location,a.job_no,a.buyer_name,a.style_ref_no";
	}

	$data_array=sql_select($sql);
	foreach ($data_array as $row)
	{ 
		
		echo "document.getElementById('txt_order_id').value	= '".$po_id."';\n";
		echo "document.getElementById('txt_order_no').value	= '".implode(',',array_unique(explode(',',$row[csf("po_number")])))."';\n";
		echo "document.getElementById('cbo_buyer_name').value	= '".$row[csf("buyer_name")]."';\n";
		echo "document.getElementById('txt_style_ref_no').value	= '".$row[csf("style_ref_no")]."';\n";
		
		foreach(array_unique(explode(',',$row[csf("item_number_id")])) as $item_um_id){
			$string_item.=$garments_item[$item_um_id].',';	
		}
		echo "document.getElementById('txt_gmt_item_name').value	= '".trim($string_item,',')."';\n";
		$gmt_item_id_arr=implode(',',array_unique(explode(",",$row[csf('item_number_id')])));
		echo "document.getElementById('txt_gmt_item_id').value	= '".$gmt_item_id_arr."';\n";

	}
	
	
//-------------------------------------------------	
	
	
	
 $sql="SELECT c.order_id,c.gmt_item_id,c.bill_qty,c.bill_safty_qty ,c.bill_safty_amount  FROM pro_weekly_wages_bill_mst a, pro_weekly_wages_bill_dtls b,pro_weekly_wages_order_brk c WHERE a.id=b.mst_id and a.id=c.weekly_wages_mst_id and b.id=weekly_wages_dtls_id and c.order_id in($po_id) and a.company_id='$company' and a.bill_for='$bill_for' and c.gmt_item_id in($items) $dtls_update_id_con  and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0  and c.status_active=1 and c.is_deleted=0";// and a.location='$location' and b.emp_id='$supplier_id'


	$total_previous_bill_qty=array();
	$total_previous_deducted_qty=array();
	$total_previous_deducted_amount=array();
	$prv_bill_history='<b><u>Previous Bill History</u></b><br/>';
	$i=1;
	
	$data_array=sql_select($sql);
	foreach ($data_array as $row)
	{  
		$key=$row[csf("order_id")].$row[csf("gmt_item_id")];
		$total_previous_bill_qty[$key]+=round($row[csf("bill_qty")]);
		$total_previous_deducted_qty[$key]+=round($row[csf("bill_safty_qty")]);
		$total_previous_deducted_amount[$key]+=round($row[csf("bill_safty_amount")]);

		$order_by_previous_deducted_qty[$key]+=$row[csf("bill_safty_qty")];
		$prv_bill_history.=$i.'. <b>Order:</b> '.$po_number_arr[$row[csf("order_id")]].' <b>Bill Qty:</b> '.number_format($row[csf("bill_qty")],2).' <b>Deduct Qty:</b> '.number_format($row[csf("bill_safty_qty")],2).'<br/>';		
	$i++;
	}


	$sql_pro = "SELECT b.po_break_down_id,b.item_number_id,sum(b.production_quantity) as production_quantity
	from 
		pro_garments_production_mst b
	where 
		b.production_date BETWEEN '$from_date' and '$to_date' 
		and b.po_break_down_id in($po_id) 
		and b.item_number_id in($items)
		and b.location='$location'
		and b.production_type=1 
		and b.produced_by = 2
		and b.status_active=1
		and b.is_deleted=0
	group by 
		b.po_break_down_id,b.item_number_id";
	$pro_array=sql_select($sql_pro);
	foreach ($pro_array as $rows)
	{
		$pro_arr[$rows[csf("po_break_down_id")].$rows[csf("item_number_id")]] = $rows[csf("production_quantity")];
	}
	//print_r($pro_arr); die;
	
	$sql = "SELECT a.color_type,a.uom,a.order_id,a.item_id,sum(a.wo_qty) as wo_qty,a.avg_rate
	from piece_rate_wo_dtls a,piece_rate_wo_mst c 
	where c.company_id='$company' and a.order_id in($po_id) and a.job_id in($job_id) and a.avg_rate in($rate) and a.color_type in($rate_var) and a.item_id in($items) and a.mst_id=c.id and rate_for='$bill_for' and a.status_active=1 and a.is_deleted=0  and c.status_active=1 and c.is_deleted=0
	group by a.order_id,a.item_id,a.color_type,a.uom,a.avg_rate";


$order_bread_down_data='';	
$tot_cut_qty=$tot_avg_rate=$tot_deducted_qty=$tot_amount=$tot_wo_qty=0;
	
	$data_array=sql_select($sql);
	foreach ($data_array as $row)
	{  
		$key=$row[csf("order_id")].$row[csf("item_id")];
		if($row[csf("uom")]==2)// dzn
		{
			$production_quantity=$pro_arr[$key]/12;
		}
		else
		{
			$production_quantity=$pro_arr[$key]/1;
		}

		//-----------------------
		if($final_bill==2)//Note:2=no;
		{ 
		
			$deducted_qty=(($production_quantity*$deducted_parcent)/100);
			$production_quantity=$production_quantity-$deducted_qty;

			if(($production_quantity+$total_previous_bill_qty[$key]) > $row[csf("wo_qty")]){
				//------------------------------------For message show;
				$excise_qty .='Order no: '.$po_number_arr[$key].' Excess: ';
				$excise_qty .=round(($production_quantity+$total_previous_bill_qty[$key]+$total_previous_deducted_qty[$key])- $row[csf("wo_qty")]).'\n';
				//-----------------------------------------------
				
				$capacity_cutting=$row[csf("wo_qty")]-$total_previous_bill_qty[$key];
				$cut_qty=$capacity_cutting;
				$amount=$cut_qty*$row[csf("avg_rate")];
			
			}
			else
			{
				//$deducted_qty=(($production_quantity*$deducted_parcent)/100);
				$cut_qty=$production_quantity;
				$amount=$cut_qty*$row[csf("avg_rate")];	
			}
			
			
		
		}
		else
		{
			$deducted_qty=(($production_quantity*$deducted_parcent)/100);
			$production_quantity=$production_quantity-$deducted_qty;
		
			if(($production_quantity+$total_previous_bill_qty[$key]+$total_previous_deducted_qty[$key]) > $row[csf("wo_qty")])
			{
				//------------------------------------For message show;
				$excise_qty .='Order no: '.$po_number_arr[$row[csf("order_id")]].' Excess: ';
				$excise_qty .=round(($production_quantity+$total_previous_bill_qty[$key]+$total_previous_deducted_qty[$key])- $row[csf("wo_qty")]).'\n';
				$excise_qty .= "prod qty=(".$production_quantity."+".$total_previous_bill_qty[$key]."+".$total_previous_deducted_qty[$key].")- wo Qty = ".$row[csf("wo_qty")];
				//-----------------------------------------------
				$capacity_cutting=$row[csf("wo_qty")]-($total_previous_bill_qty[$key]);
				$cut_qty=$capacity_cutting;
				$amount=$cut_qty*$row[csf("avg_rate")];
			
			}
			else
			{
				$cut_qty=$production_quantity+$order_by_previous_deducted_qty[$key];
				$amount=$cut_qty*$row[csf("avg_rate")];
			}
		
		
		}
		
	
		
// order level data info------------------------------


$order_by_previous_bill_qty=return_field_value("sum(bill_qty)","pro_weekly_wages_bill_mst a, pro_weekly_wages_bill_dtls b, pro_weekly_wages_order_brk c","a.id=b.mst_id and b.id=c.weekly_wages_dtls_id and a.id=c.weekly_wages_mst_id and c.order_id ='".$row[csf("order_id")]."' and a.company_id='$company' and a.bill_for='$bill_for' and c.gmt_item_id='".$row[csf("item_id")]."' $dtls_update_id_con and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0  and c.status_active=1 and c.is_deleted=0");//$location_con_a

$order_by_yet_to_bill_qty=$row[csf("wo_qty")]-$order_by_previous_bill_qty;

//$order_bread_down_data.=$row[csf("order_id")].'**'.$row[csf("item_id")].'**'.round($cut_qty).'**'.$row[csf("avg_rate")].'**'.round($deducted_qty).'**'.round($amount).'**'.$row[csf("wo_qty")].'**'.$order_by_previous_bill_qty.'**'.$order_by_yet_to_bill_qty.'__';

$order_bread_down_data.=$row[csf("order_id")].'**'.$row[csf("item_id")].'**'.($cut_qty).'**'.$row[csf("avg_rate")].'**'.($deducted_qty).'**'.($amount).'**'.$row[csf("wo_qty")].'**'.$order_by_previous_bill_qty.'**'.$order_by_yet_to_bill_qty.'__';
//---------------------------------
		
	$tot_cut_qty+=$cut_qty;
	$tot_avg_rate+=$row[csf("avg_rate")];
	$tot_deducted_qty+=$deducted_qty;
	$tot_amount+=$amount;
	$tot_wo_qty+=$row[csf("wo_qty")];
	$rate_variables=$row[csf("color_type")];
	$uom=$row[csf("uom")];
	
	$tot_prev_deducted_qty+=$total_previous_deducted_qty[$key];
	$tot_prev_bill_qty+=$total_previous_bill_qty[$key];
	}

	
	
	
	//-----------------------
	if($final_bill==2){// 2=no;1=yes
		$yet_to_bill_qty=$tot_wo_qty-$tot_prev_bill_qty;
		// $tot_cut_qty=round($tot_cut_qty);
		$tot_amount=round($tot_amount);
	}
	else
	{
		if(($production_quantity+$tot_prev_bill_qty+$tot_prev_deducted_qty) >$tot_wo_qty){
			// $tot_cut_qty=round($tot_cut_qty);
			$tot_amount=round($tot_amount);
		}
		else
		{
			// $tot_cut_qty=round($tot_cut_qty);
			$tot_amount=round($tot_amount);
		}
		$yet_to_bill_qty=$tot_wo_qty-$tot_prev_bill_qty;
	}
	
	//echo $tot_cut_qty.'+'.$tot_prev_deducted_qty.' <='. $tot_wo_qty;die;
	//for message---------------------------------------
	if($excise_qty){echo "alert('".$excise_qty."');\n";}
	//-------------------------------------------------------
	if(number_format(($tot_cut_qty+$tot_prev_deducted_qty),0,".", "") <= number_format($tot_wo_qty,0,".", "") && $tot_cut_qty!=0){	
		echo "document.getElementById('txt_cutting_bill_qty').value	= '".number_format($tot_cut_qty,2)."';\n";
		echo "document.getElementById('txt_wo_rate').value	= '".$tot_avg_rate/$total_order."';\n";
		echo "document.getElementById('txt_deducted_qty_hidden').value	= '".$tot_deducted_qty."';\n";
		
		echo "document.getElementById('txt_amount').value	= '".number_format($tot_cut_qty*($tot_avg_rate/$total_order),2)."';\n";
		echo "document.getElementById('txt_net_amount').value='".number_format($tot_cut_qty*($tot_avg_rate/$total_order),2)."';\n";
		echo "document.getElementById('cbo_cutting_bill_uom').value	= '".$uom."';\n";
		echo "document.getElementById('cbo_wo_rate_uom').value	= '".$uom."';\n";
		echo "document.getElementById('cbo_previous_bill_qty_uom').value = '".$uom."';\n";
		echo "document.getElementById('cbo_yet_to_bill_qty_uom').value	= '".$uom."';\n";
		echo "document.getElementById('txt_previous_bill_qty').value	= '".number_format($tot_prev_bill_qty,2)."';\n";
		
		$yet_to_bill_qty=($yet_to_bill_qty-$tot_cut_qty);
		echo "document.getElementById('txt_yet_to_bill_qty').value	= '".number_format($yet_to_bill_qty,2)."';\n";
		echo "document.getElementById('txt_appv_wo_order_qty').value	= '".$tot_wo_qty."';\n";
		echo "document.getElementById('cbo_wages_rate_variables').value	= '".$rate_variables."';\n";
		
		$order_bread_down_data=substr($order_bread_down_data,0,-2)	;
		echo "document.getElementById('hidden_order_break_down').value = '".$order_bread_down_data."';\n";
		echo "document.getElementById('cbo_final_bill').disabled = true;\n";
		echo "document.getElementById('tex_emp_deduct_amount').value = 0;\n";
	}
	else
	{
		echo "alert('Over Bill Not Allowed');\n";	
	}
		echo "document.getElementById('display_prv_bill_history').innerHTML = '".$prv_bill_history."';\n";
	
	
	exit();
}//end if;


//------------------------------------------------------------------------

if($action=="check_unique")
{
	
	$operation_arr=explode("__",$operation);
	$flag=0;
	foreach($operation_arr as $operation_values)
	{ 
	list($id,$job_no,$buyer_id,$buyer_name,$style_ref_no,$gmts_item_id,$gmts_item,$po_id,$po_number)=explode("**",$operation_values);
	
	$is_duplicate = is_duplicate_field( "id", "piece_rate_wo_dtls", "mst_id='$mst_id' and job_id='$id' and item_id='$gmts_item_id'" );
	
	if($is_duplicate==1){
		if($items=='')$items=$gmts_item; else $items.=' and '.$gmts_item;
		$flag=1;
		}
		else
		{
		$flag=0;
		}
	}
	
	if($flag==1){echo $items;}else{echo 0;}

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
		
		$flag=1;
		if(str_replace("'","",$update_id)=="")
		{
			 // echo "YES";die;
			if($db_type==0) $year_cond="YEAR(insert_date)"; 
			else if($db_type==2) $year_cond="to_char(insert_date,'YYYY')";
			else $year_cond="";//defined Later
			
			// master part--------------------------------------------------------------;
			$weekly_wages_bill_system_id=explode("*",return_mrr_number( str_replace("'","",$cbo_company_id), '', 'WCW', date("Y",time()), 5, "select sys_number_prefix, sys_number_prefix_num from pro_weekly_wages_bill_mst where company_id=$cbo_company_id AND bill_for=20 and $year_cond=".date('Y',time())." order by id desc", "sys_number_prefix", "sys_number_prefix_num" ));
		 	
			//$mst_id=return_next_id( "id", "pro_weekly_wages_bill_mst", 1 ) ;
			$mst_id= return_next_id_by_sequence("weekly_wages_bill_mst_seq","pro_weekly_wages_bill_mst", $con );
			
			$field_array_mst="id,sys_number_prefix,sys_number_prefix_num,sys_number,company_id,bill_for,final_bill,location, division_id,department_id,shift,week_from_date,week_to_date,inserted_by,insert_date,status_active,is_deleted";
			
			$data_array_mst="(".$mst_id.",'".$weekly_wages_bill_system_id[1]."',".$weekly_wages_bill_system_id[2].",'".$weekly_wages_bill_system_id[0]."',".$cbo_company_id.",".$cbo_bill_for.",".$cbo_final_bill.",".$cbo_location.",".$cbo_division.",".$cbo_department.",".$cbo_shift.",".$txt_week_date_from.",".$txt_week_date_to.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."','1','0')";

			// details part--------------------------------------------------------------;
			//$dtls_id=return_next_id( "id", "pro_weekly_wages_bill_dtls", 1 ) ;
			$dtls_id= return_next_id_by_sequence("weekly_wages_bill_dtls_seq","pro_weekly_wages_bill_dtls", $con );
			
			$field_array_dtls="id,mst_id,emp_id,emp_name,order_id,buyer_id,style_ref,gmt_item,apv_order_qty,rate_variables,cutting_bill_qty,wo_rate,amount,deducted_emp,net_amount,previous_bill_qty,yet_to_bill_qty,cutting_bill_uom,rate_uom,previous_bill_uom,yet_to_bill_uom,deducted_qty,deducted_rate,deducted_amount,inserted_by,insert_date,status_active,is_deleted";
 
			$txt_net_amount = str_replace(array(',',"'"),"",$txt_net_amount);
			$deducted_amount = str_replace(array(',',"'"),"",$deducted_amount);
			$txt_amount = str_replace(array(',',"'"),"",$txt_amount);
			$txt_cutting_bill_qty = str_replace(array(',',"'"),"",$txt_cutting_bill_qty);
			$txt_previous_bill_qty=str_replace(array(',',"'"),"",$txt_previous_bill_qty);
			$txt_yet_to_bill_qty=str_replace(array(',',"'"),"",$txt_yet_to_bill_qty);
			
			$deducted_qty=str_replace("'","",$txt_deducted_qty_hidden);
			$deducted_rate=str_replace("'","",$txt_wo_rate);
			$deducted_amount=$deducted_qty*$deducted_rate;
			$txt_order_id=str_replace("'","",$txt_order_id);
			

			// echo $txt_previous_bill_qty;die;

			$data_array_dtls="(".$dtls_id.",".$mst_id.",".$txt_emp_code.",".$txt_emp_name.",'".$txt_order_id."',".$cbo_buyer_name.",".$txt_style_ref_no.",".$txt_gmt_item_name.",".$txt_appv_wo_order_qty.",".$cbo_wages_rate_variables.",".$txt_cutting_bill_qty.",".$txt_wo_rate.",".$txt_amount.",".$tex_emp_deduct_amount.",".$txt_net_amount.",".$txt_previous_bill_qty.",".$txt_yet_to_bill_qty.",".$cbo_cutting_bill_uom.",".$cbo_wo_rate_uom.",".$cbo_previous_bill_qty_uom.",".$cbo_yet_to_bill_qty_uom.",".$deducted_qty.",".$deducted_rate.",".$deducted_amount.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."','1','0')";

			//echo $data_array_dtls;die; 
  
			//Order Break down part-----------------------------------------------------------------------
			$field_array_wo_dtls_brk="id, weekly_wages_mst_id, weekly_wages_dtls_id, order_id,gmt_item_id, approved_wo_qty, bill_qty, bill_safty_qty, bill_safty_rate, bill_safty_amount, amount, previous_bill_qty, yet_to_bill_qty, inserted_by,insert_date,status_active,is_deleted";
			
			//$dtls_brk_down_id=return_next_id( "id", "pro_weekly_wages_order_brk", 1 ) ;
	
			$data_array_wo_dtls_brk='';
			$history_row=explode('__',$hidden_order_break_down);
			foreach($history_row as $dataVal)
			{
				$dtls_brk_down_id= return_next_id_by_sequence("weekly_wages_order_brk_seq","pro_weekly_wages_order_brk", $con );
				$dataVal=str_replace("'","",$dataVal);
				list($order_id,$gmt_item_id,$cutqty,$avrate,$deductqty,$amount,$owqty,$prv_bill,$yet_bill)=explode('**',$dataVal);
								
				if($data_array_wo_dtls_brk=='')
				{
				$data_array_wo_dtls_brk="(".$dtls_brk_down_id.",".$mst_id.",".$dtls_id.",".$order_id.",".$gmt_item_id.",".$owqty.",".$cutqty.",'".$deductqty."',".$avrate.",".$deductqty*$avrate.",".$amount.",'".$prv_bill."','".$yet_bill."',".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."','1','0')";
				}
				else
				{
				$data_array_wo_dtls_brk.=",(".$dtls_brk_down_id.",".$mst_id.",".$dtls_id.",".$order_id.",".$gmt_item_id.",".$owqty.",".$cutqty.",'".$deductqty."',".$avrate.",".$deductqty*$avrate.",".$amount.",'".$prv_bill."','".$yet_bill."',".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."','1','0')";
				}
				
				//$dtls_brk_down_id++;
			}
			$rID1=sql_insert("pro_weekly_wages_bill_mst",$field_array_mst,$data_array_mst,0);
			// echo "10**".$rID1;oci_rollback($con);die;
			if($flag==1) 
			{
				if($rID1) $flag=1; else $flag=0; 
			} 

			$rID2=sql_insert("pro_weekly_wages_bill_dtls",$field_array_dtls,$data_array_dtls,0);
			// echo "10**".$rID2;oci_rollback($con);die;
			//echo "10**insert into pro_weekly_wages_bill_dtls $field_array_dtls values  $data_array_dtls"; oci_rollback($con); die;

			if($flag==1) 
			{
				if($rID2) $flag=1; else $flag=0; 
			} 
			
			$rID3=sql_insert("pro_weekly_wages_order_brk",$field_array_wo_dtls_brk,$data_array_wo_dtls_brk,0);
			//echo "10**".$rID3;oci_rollback($con);die;
			if($flag==1) 
			{
				if($rID3) $flag=1; else $flag=0; 
			} 
			//echo "10**insert into pro_weekly_wages_bill_dtls $field_array_dtls values($data_array_dtls)";die;
			//echo $data_array_dtls;die;
			//echo $data_array_wo_dtls_brk;die;
			//echo "10**0**".$rID1.'='.$rID2.'='.$rID3; die;	

			 
			 
			if($flag==1)
			{
				oci_commit($con);  
				echo "0**".$mst_id."**".$weekly_wages_bill_system_id[0]."**0";
			}
			else
			{
				oci_rollback($con);
				echo "10**0**"."&nbsp;"."**0";
			}
		 
					
			disconnect($con);
			die;
		}
		else
		{
 
			if($db_type==0) $year_cond="YEAR(insert_date)"; 
			else if($db_type==2) $year_cond="to_char(insert_date,'YYYY')";
			else $year_cond="";//defined Later
			
			

			// details part--------------------------------------------------------------;
			//$dtls_id=return_next_id( "id", "pro_weekly_wages_bill_dtls", 1 ) ;
			$dtls_id= return_next_id_by_sequence("weekly_wages_bill_dtls_seq","pro_weekly_wages_bill_dtls", $con );
			$field_array_dtls="id,mst_id,emp_id,emp_name,order_id,buyer_id,style_ref,gmt_item,apv_order_qty,rate_variables,cutting_bill_qty,wo_rate,amount,deducted_emp,net_amount,previous_bill_qty,yet_to_bill_qty,cutting_bill_uom,rate_uom,previous_bill_uom,yet_to_bill_uom,deducted_qty,deducted_rate,deducted_amount,inserted_by,insert_date,status_active,is_deleted";

			$txt_net_amount = str_replace(array(',',"'"),"",$txt_net_amount);
			$deducted_amount = str_replace(array(',',"'"),"",$deducted_amount);
			$txt_amount = str_replace(array(',',"'"),"",$txt_amount);
			$txt_yet_to_bill_qty = str_replace(array(',',"'"),"",$txt_yet_to_bill_qty);
			
			$deducted_qty=str_replace("'","",$txt_deducted_qty_hidden);
			$deducted_rate=str_replace("'","",$txt_wo_rate);
			$deducted_amount=$deducted_qty*$deducted_rate;
			$txt_order_id=str_replace("'","",$txt_order_id);
			$update_id=str_replace("'","",$update_id);
			
			$data_array_dtls="(".$dtls_id.",".$update_id.",".$txt_emp_code.",".$txt_emp_name.",'".$txt_order_id."',".$cbo_buyer_name.",".$txt_style_ref_no.",".$txt_gmt_item_name.",".$txt_appv_wo_order_qty.",".$cbo_wages_rate_variables.",".$txt_cutting_bill_qty.",".$txt_wo_rate.",".$txt_amount.",".$tex_emp_deduct_amount.",".$txt_net_amount.",".$txt_previous_bill_qty.",".$txt_yet_to_bill_qty.",".$cbo_cutting_bill_uom.",".$cbo_wo_rate_uom.",".$cbo_previous_bill_qty_uom.",".$cbo_yet_to_bill_qty_uom.",".$deducted_qty.",".$deducted_rate.",".$deducted_amount.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."','1','0')";

		//Order Break down part-----------------------------------------------------------------------
		$field_array_wo_dtls_brk="id, weekly_wages_mst_id, weekly_wages_dtls_id, order_id,gmt_item_id, approved_wo_qty, bill_qty, bill_safty_qty, bill_safty_rate, bill_safty_amount, amount, previous_bill_qty, yet_to_bill_qty, inserted_by,insert_date,status_active,is_deleted";
		//$dtls_brk_down_id=return_next_id( "id", "pro_weekly_wages_order_brk", 1 ) ;
		$data_array_wo_dtls_brk='';
		$history_row=explode('__',$hidden_order_break_down);
		foreach($history_row as $dataVal)
		{
			$dtls_brk_down_id= return_next_id_by_sequence("weekly_wages_order_brk_seq","pro_weekly_wages_order_brk", $con );
			$dataVal=str_replace("'","",$dataVal);
			list($order_id,$gmt_item_id,$cutqty,$avrate,$deductqty,$amount,$owqty,$prv_bill,$yet_bill)=explode('**',$dataVal);
							
			if($data_array_wo_dtls_brk=='')
			{
			$data_array_wo_dtls_brk="(".$dtls_brk_down_id.",".$update_id.",".$dtls_id.",".$order_id.",".$gmt_item_id.",".$owqty.",".$cutqty.",'".$deductqty."',".$avrate.",".$deductqty*$avrate.",".$amount.",'".$prv_bill."','".$yet_bill."',".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."','1','0')";
			}
			else
			{
			$data_array_wo_dtls_brk.=",(".$dtls_brk_down_id.",".$update_id.",".$dtls_id.",".$order_id.",".$gmt_item_id.",".$owqty.",".$cutqty.",'".$deductqty."',".$avrate.",".$deductqty*$avrate.",".$amount.",'".$prv_bill."','".$yet_bill."',".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."','1','0')";
			}
			
			//$dtls_brk_down_id++;
		}
					

				$rID2=sql_insert("pro_weekly_wages_bill_dtls",$field_array_dtls,$data_array_dtls,0);
				if($flag==1) 
				{
					if($rID2) $flag=1; else $flag=0; 
				} 
				
				$rID3=sql_insert("pro_weekly_wages_order_brk",$field_array_wo_dtls_brk,$data_array_wo_dtls_brk,0);
				if($flag==1) 
				{
					if($rID3) $flag=1; else $flag=0; 
				} 
				
				
				if($db_type==0)
				{
					if($flag==1)
					{
						mysql_query("COMMIT");  
						echo "0**".$update_id."**".str_replace("'","",$txt_system_id);
					}
					else
					{
						mysql_query("ROLLBACK"); 
						echo "10**0**"."&nbsp;"."**0";
					}
				}
				else if($db_type==2 || $db_type==1 )
				{
					if($flag==1)
					{
						oci_commit($con);  
						echo "0**".$update_id."**".str_replace("'","",$txt_system_id);
					}
					else
					{
						oci_rollback($con);
						echo "10**0**"."&nbsp;"."**0";
					}
				}
				disconnect($con);
				die;
		}
	}
	else if ($operation==1)   // Update Here
	{
		$con = connect();
		if($db_type==0)
		{
			mysql_query("BEGIN");
		}
		
		// delete here--------------------------------------------------------------- 
		 $update_id=str_replace("'","",$update_id);
		 $txt_system_id=str_replace("'","",$txt_system_id);
		 $dtls_update_id=str_replace("'","",$dtls_update_id);
		
		if($update_id && $dtls_update_id){
			//$rID1=execute_query("delete from pro_weekly_wages_bill_mst where id =".$update_id."",0);
			$rID2=execute_query("delete from pro_weekly_wages_bill_dtls where id=$dtls_update_id and mst_id =".$update_id."",0);
			$rID3=execute_query("delete from pro_weekly_wages_order_brk where weekly_wages_dtls_id=$dtls_update_id and weekly_wages_mst_id =".$update_id."",0);
		}
		 

		
		if($db_type==0)
		{
			if($rID2==1 && $rID3==1){
				mysql_query("COMMIT");  
				//echo "0**".$update_id;
			}
			else{
				mysql_query("ROLLBACK"); 
				//echo "10**".$update_id;
			}
		}
		
		else if($db_type==2 || $db_type==1 )
		{
			if($rID2==1 && $rID3==1){
				oci_commit($con);  
				//echo "0**".$update_id;
			}
			else{
				oci_rollback($con);
				//echo "10**".$update_id;
			}
		}
			//Delete end----------------------------------------------


			//Delete then insert start-------------------

				if($db_type==0)
				{
					mysql_query("BEGIN");
				}
				
				
				$flag=1;
				if($update_id)
				{
					if($db_type==0) $year_cond="YEAR(insert_date)"; 
					else if($db_type==2) $year_cond="to_char(insert_date,'YYYY')";
					else $year_cond="";//defined Later
					
					
					

		// details part--------------------------------------------------------------;
					//$dtls_id=return_next_id( "id", "pro_weekly_wages_bill_dtls", 1 ) ;
					$dtls_id= return_next_id_by_sequence( "weekly_wages_bill_dtls_seq","pro_weekly_wages_bill_dtls", $con );
					$field_array_dtls="id,mst_id,emp_id,emp_name,order_id,buyer_id,style_ref,gmt_item,apv_order_qty,rate_variables,cutting_bill_qty,wo_rate,amount,deducted_emp,net_amount,previous_bill_qty,yet_to_bill_qty,cutting_bill_uom,rate_uom,previous_bill_uom,yet_to_bill_uom,deducted_qty,deducted_rate,deducted_amount,inserted_by,insert_date,status_active,is_deleted";
					
					$deducted_qty=str_replace("'","",$txt_deducted_qty_hidden);
					$deducted_rate=str_replace("'","",$txt_wo_rate);
					$txt_cutting_bill_qty = str_replace(array(',',"'"),"",$txt_cutting_bill_qty);
					$deducted_amount=$deducted_qty*$deducted_rate;
					$txt_order_id=str_replace("'","",$txt_order_id);
					$txt_yet_to_bill_qty = str_replace(array(',',"'"),"",$txt_yet_to_bill_qty);
					
					$data_array_dtls="(".$dtls_id.",".$update_id.",".$txt_emp_code.",".$txt_emp_name.",'".$txt_order_id."',".$cbo_buyer_name.",".$txt_style_ref_no.",".$txt_gmt_item_name.",".$txt_appv_wo_order_qty.",".$cbo_wages_rate_variables.",".$txt_cutting_bill_qty.",".$txt_wo_rate.",".$txt_amount.",".$tex_emp_deduct_amount.",".$txt_net_amount.",".$txt_previous_bill_qty.",".$txt_yet_to_bill_qty.",".$cbo_cutting_bill_uom.",".$cbo_wo_rate_uom.",".$cbo_previous_bill_qty_uom.",".$cbo_yet_to_bill_qty_uom.",".$deducted_qty.",".$deducted_rate.",".$deducted_amount.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."','1','0')";

		//Order Break down part-----------------------------------------------------------------------
			$field_array_wo_dtls_brk="id, weekly_wages_mst_id, weekly_wages_dtls_id, order_id,gmt_item_id, approved_wo_qty, bill_qty, bill_safty_qty, bill_safty_rate, bill_safty_amount, amount, previous_bill_qty, yet_to_bill_qty, inserted_by,insert_date,status_active,is_deleted";
			//$dtls_brk_down_id=return_next_id( "id", "pro_weekly_wages_order_brk", 1 ) ;
			$data_array_wo_dtls_brk='';
			
			$history_row=explode('__',$hidden_order_break_down);
			foreach($history_row as $dataVal)
			{
				$dtls_brk_down_id= return_next_id_by_sequence("weekly_wages_order_brk_seq","pro_weekly_wages_order_brk", $con );
				$dataVal=str_replace("'","",$dataVal);
				list($order_id,$gmt_item_id,$cutqty,$avrate,$deductqty,$amount,$owqty,$prv_bill,$yet_bill)=explode('**',$dataVal);
				// echo "10**".$owqty;die;				
				if($data_array_wo_dtls_brk=='')
				{
				$data_array_wo_dtls_brk="(".$dtls_brk_down_id.",".$update_id.",".$dtls_id.",".$order_id.",".$gmt_item_id.",".$owqty.",".$cutqty.",'".$deductqty."',".$avrate.",".$deductqty*$avrate.",".$amount.",'".$prv_bill."','".$yet_bill."',".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."','1','0')";
				}
				else
				{
				$data_array_wo_dtls_brk.=",(".$dtls_brk_down_id.",".$update_id.",".$dtls_id.",".$order_id.",".$gmt_item_id.",".$owqty.",".$cutqty.",'".$deductqty."',".$avrate.",".$deductqty*$avrate.",".$amount.",'".$prv_bill."','".$yet_bill."',".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."','1','0')";
				}
				
				//$dtls_brk_down_id++;
			}
						

					$rID2=sql_insert("pro_weekly_wages_bill_dtls",$field_array_dtls,$data_array_dtls,0);
					if($flag==1) 
					{
						if($rID2) $flag=1; else $flag=0; 
					} 
					
					$rID3=sql_insert("pro_weekly_wages_order_brk",$field_array_wo_dtls_brk,$data_array_wo_dtls_brk,0);
					// echo "10**insert into pro_weekly_wages_order_brk ($field_array_wo_dtls_brk) values $data_array_wo_dtls_brk";die;
					if($flag==1) 
					{
						if($rID3) $flag=1; else $flag=0; 
					} 
					
					if($db_type==0)
					{
						if($flag==1)
						{
							mysql_query("COMMIT");  
							echo "1**".$update_id."**".str_replace("'","",$txt_system_id);
						}
						else
						{
							mysql_query("ROLLBACK"); 
							echo "10**0**"."&nbsp;"."**0";
						}
					}
					else if($db_type==2 || $db_type==1 )
					{
						if($flag==1)
						{
							oci_commit($con);  
							echo "1**".$update_id."**".str_replace("'","",$txt_system_id);
						}
						else
						{
							oci_rollback($con);
							echo "10**0**"."&nbsp;"."**0";
						}
					}
					
							
					disconnect($con);
					die;
		}
	
	}
	else if ($operation==2)  // Delete Here
	{ 
		$con = connect();
		if($db_type==0)
		{
			mysql_query("BEGIN");
		}
		
		
		
		// delete here--------------------------------------------------------------- 
		 $update_id=str_replace("'","",$update_id);
		 $txt_system_id=str_replace("'","",$txt_system_id);
		 $dtls_update_id=str_replace("'","",$dtls_update_id);
		
		$flag=1;
		if($update_id && $dtls_update_id){
			$rID1=execute_query("UPDATE pro_weekly_wages_bill_dtls SET status_active=0, is_deleted=1 WHERE id=$dtls_update_id and mst_id =".$update_id."",0);
			$rID2=execute_query("UPDATE pro_weekly_wages_order_brk SET status_active=0, is_deleted=1 WHERE weekly_wages_dtls_id=$dtls_update_id and weekly_wages_mst_id =".$update_id."",0);
			$rID3=execute_query("delete from piece_rate_terms_condition where mst_id =".$update_id."",0);
		
		
			if($flag==1) 
			{
				if($rID1) $flag=1; else $flag=0; 
			} 
			
			if($flag==1) 
			{
				if($rID2) $flag=1; else $flag=0; 
			} 
			
			if($flag==1) 
			{
				if($rID3) $flag=1; else $flag=0; 
			} 
			
				
				
				if($db_type==0)
				{
					if($flag==1)
					{
						mysql_query("COMMIT");  
						echo "1**".$update_id."**".str_replace("'","",$txt_system_id);
					}
					else
					{
						mysql_query("ROLLBACK"); 
						echo "10**0**"."&nbsp;"."**0";
					}
				}
				else if($db_type==2 || $db_type==1 )
				{
					if($flag==1)
					{
						oci_commit($con);  
						echo "1**".$update_id."**".str_replace("'","",$txt_system_id);
					}
					else
					{
						oci_rollback($con);
						echo "10**0**"."&nbsp;"."**0";
					}
				}
			
				disconnect($con);
				die;
			}

	}//end operation==2;
	
}

?>