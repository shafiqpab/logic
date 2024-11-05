<?
include('../../../includes/common.php'); 
session_start();

$data=$_REQUEST['data'];
$action=$_REQUEST['action'];


if( $_SESSION['logic_erp']['user_id'] == "" ) { header("location:login.php"); die; }


if ($action=="load_drop_down_location")
{
	echo create_drop_down( "cbo_location_name", 150, "select id,location_name from lib_location where company_id='$data' and status_active =1 and is_deleted=0 order by location_name","id,location_name", 1, "-- Select Location --", $selected, "" );	
	exit();	 
}
if ($action=="load_drop_down_party")
{
    $data=explode("_",$data);

    if($data[1]==1 && $data[0]!=0)
    {

        echo create_drop_down( "cbo_party_name", 120, "select comp.id, comp.company_name from lib_company comp where comp.status_active=1 and comp.is_deleted=0 order by comp.company_name","id,company_name", 1, "-- Select Party --", $data[2], "");
    }
    elseif($data[1]==2 && $data[0]!=0)
    {
        echo create_drop_down( "cbo_party_name", 120, "select buy.id,buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company='$data[0]' $buyer_cond  and buy.id in (select  buyer_id from  lib_buyer_party_type where party_type in (2,3)) order by buyer_name","id,buyer_name", 1, "-- Select Party --",0, "" );
    }
    else
    {
    	echo create_drop_down('cbo_party_name', 120, $blank_array, '', 1, '-- Select Party --', $selected, "",1);
    }   
    exit();  
}
if ($action=="job_popup")
{
	echo load_html_head_contents("Popup Info","../../../", 1, 1, $unicode,'','');
	$data=explode("_",$data);
	$cbo_company_name=$data[0];
	$cbo_pro_type=$data[2];
	$cbo_order_type=$data[3];
	$cbo_yd_type=$data[4];
	?>
	<script>
		// function js_set_value(id)
		// { 
		// 	$("#hidden_mst_id").val(id);
		// 	document.getElementById('selected_order').value=id;
		// 	parent.emailwindow.hide();
		// }

		var selected_id = new Array();
		var jobNoArr = new Array(); 
		function toggle( x, origColor ) {
			var newColor = 'yellow';
			if ( x.style ) {
				x.style.backgroundColor = ( newColor == x.style.backgroundColor )? origColor : newColor;
			}
		}
		
		function check_all_data() {
			
			$("#tbl_data_list tbody tr").each(function() 
            {
				var valTP=$(this).attr("id");
				if (typeof valTP != "undefined")
                {
					var val = valTP.split("_");
					var id = val[1];
					var receive_dtls_id =  $('#txt_individual_id'+id).val()*1;
					var job_no =  $('#txt_job_id'+id).val();
					var tbl_row_count = document.getElementById( 'tbl_data_list' ).rows.length-1;
						
						js_set_value( id+'***'+receive_dtls_id+'***'+ job_no);
							
				}
			});

			
		}

		function js_set_value( str) 
		{
			// alert(str);return;
			splitArr = str.split('***'); 
            var serial=splitArr[0]*1;
            var receive_dtls_id=splitArr[1]*1;
            var job_no=splitArr[2];
			// if(jobNoArr.length==0)
			// {
			// 	jobNoArr.push( job_no );
			// }
			// else if( jQuery.inArray( job_no, jobNoArr )==-1 &&  jobNoArr.length>0)
			// {
			// 	alert("Job Mixed Not Allowed");
			// 	return true;
			// }

			toggle( document.getElementById( 'search_' + serial ), '#FFFFCC' );
			
			
			if( jQuery.inArray( $('#txt_individual_id' + serial).val(), selected_id ) == -1 ) {
				selected_id.push( $('#txt_individual_id' + serial).val() );
			}
			else {
				for( var i = 0; i < selected_id.length; i++ ) {
					if( selected_id[i] == $('#txt_individual_id' + serial).val() ) break;
				}
				selected_id.splice( i, 1 );
			}
			var rcv_nos = '';
			for( var i = 0; i < selected_id.length; i++ ) {
				rcv_nos += selected_id[i] + ',';
			}

			if( jQuery.inArray( $('#txt_job_id' + serial).val(), jobNoArr ) == -1 ) {
				jobNoArr.push( $('#txt_job_id' + serial).val() );
			}
			// else {
			// 	for( var i = 0; i < jobNoArr.length; i++ ) {
			// 		if( jobNoArr[i] == $('#txt_job_id' + serial).val() ) break;
			// 	}
			// 	job_no_arr.splice( i, 1 );
			// }
			var job_nos = '';
			for( var i = 0; i < jobNoArr.length; i++ ) {
				job_nos += jobNoArr[i] + ',';
			}

			rcv_nos = rcv_nos.substr( 0, rcv_nos.length - 1 );
			job_nos = job_nos.substr( 0, job_nos.length - 1 );
			$('#hidden_details_id').val( rcv_nos );
			$('#hidden_job_no').val( job_nos );
			
		}
	
		
		function fnc_load_party_order_popup(company,within_group,party)
		{
			load_drop_down( 'yd_material_issue_controller', company+'_'+within_group+'_'+party, 'load_drop_down_buyer', 'buyer_td' );
			$('#cbo_party_name').attr('disabled','disabled');
		}
		
		function search_by(val)
		{
			$('#txt_search_string').val('');
			if(val==1 || val==0) $('#search_by_td').html('YD Job No');
			else if(val==2) $('#search_by_td').html('W/O No');
			else if(val==3) $('#search_by_td').html('Buyer Style');
			else if(val==4) $('#search_by_td').html('Buyer Job');
		}
		
		function search_by_date(val)
		{
			$('#txt_date_from').val('');
			$('#txt_date_to').val('');
			if(val==1 || val==0) $('#date_search_by_td').html('Delivery Date');
			else if(val==2) $('#date_search_by_td').html('WO Rcve Date');
		}
		
	</script>
	</head>
	<body>
		<div align="center" style="width:100%;" >
			<form name="searchorderfrm_<?php echo $tblRow;?>" id="searchorderfrm_<?php echo $tblRow;?>" autocomplete="off">
				<table cellspacing="0" cellpadding="0" border="1" rules="all" class="rpt_table" align="center" style="width: 100%;">
					<thead>
		                <tr>
		                    <th colspan="12"><?php echo create_drop_down('cbo_string_search_type', 163, $string_search_type, '', 1, '-- Searching Type --'); ?></th>
		                </tr>
		                <tr>
		                    <th width="120" class="must_entry_caption" >Company Name</th>
		                    <th width="80" class="must_entry_caption" >Within Group</th>
		                    <th width="120">Party Name</th>
		                    <th width="80">Search By</th>
		                    <th width="80" id="search_by_td">YD Job No</th>
		                    <th width="70">Prod. Type</th>
		                    <th width="70" class="must_entry_caption" >Order Type</th>
		                    <th width="70">Y/D Type</th>
		                    <th width="70">Date Type</th>
		                    <th width="160" id="date_search_by_td">Delivery Date</th>
		                    <th>
		                    	<input type="reset" name="reset" id="reset" class="formbutton" value="Reset" style="width: 100px" />
								<!-- <input type="hidden" name="hidden_details_id" id="hidden_details_id">   -->
		                    </th>
		                </tr>
		            </thead>
		            <tbody>
                		<tr class="general">
                			<td>
		                        <?php echo create_drop_down('cbo_company_name', 120, "select id, company_name from lib_company where status_active=1 and is_deleted=0 order by company_name", 'id,company_name', 1, '-- Select Company --', $cbo_company_name, "load_drop_down( 'yd_material_issue_requisition_controller', this.value+'_'+document.getElementById('cbo_within_group').value, 'load_drop_down_party', 'party_td' );",1); ?>
		                    </td>
		                    <td> 
	                            <?php echo create_drop_down('cbo_within_group', 80, $yes_no, '', 1, '-- Select Within Group --', $cbo_within_group, "load_drop_down( 'yd_material_issue_requisition_controller', document.getElementById('cbo_company_name').value+'_'+this.value, 'load_drop_down_party', 'party_td' );",0); ?>
	                        </td>
	                        <td id="party_td"> 
	                            <?php 

		                            if($cbo_within_group==1 && $cbo_company_name!=0)
								    {

								        echo create_drop_down( "cbo_party_name", 120, "select comp.id, comp.company_name from lib_company comp where comp.status_active=1 and comp.is_deleted=0 order by comp.company_name","id,company_name", 1, "-- Select Party --", $cbo_party_name, "",0);
								    }
								    elseif($cbo_within_group==2 && $cbo_company_name!=0)
								    {
								        echo create_drop_down( "cbo_party_name", 120, "select buy.id,buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company='$cbo_company_name' $buyer_cond  and buy.id in (select  buyer_id from  lib_buyer_party_type where party_type in (2,3)) order by buyer_name","id,buyer_name", 1, "-- Select Party --",$cbo_party_name, "",0 );
								    }
								    else
								    {
								    	echo create_drop_down('cbo_party_name', 120, $blank_array, '', 1, '-- Select Party --', $selected, "",0);
								    }

	                            ?>
	                        </td>
	                        <td>
	                        	<?
									$search_by_arr=array(1=>"YD Job No",2=>"W/O No",3=>"Buyer Style",4=>"Buyer Job");
									echo create_drop_down( "cbo_type",80, $search_by_arr,"",0, "",1,'search_by(this.value)',0 );
								?>
	                        </td>
	                        <td align="center">
                                <input type="text" name="txt_search_string" id="txt_search_string" class="text_boxes" style="width:80px" placeholder="Write" />
                            </td>
                            <td>
	                        	<? echo create_drop_down( "cbo_pro_type",70, $w_pro_type_arr,"",1, "--Select--",$cbo_pro_type,'',0 );?>
	                        </td>
	                        <td>
	                        	<? echo create_drop_down( "cbo_order_type",70, $w_order_type_arr,"",1, "--Select--",$cbo_order_type,'',0 ); ?>
	                        </td>
	                        <td>
	                        	<? echo create_drop_down( "cbo_yd_type",70, $yd_type_arr,"",1, "--Select--",$cbo_yd_type,'',0 ); ?>
	                        </td>
							<td>
							
	                        	<? 
								$yd_date_arr=array(1=>"Delivery Date ",2=>"WO Rcvd Date");
								echo create_drop_down( "cbo_date_type",70, $yd_date_arr,"",1, "--Select--",1,'search_by_date(this.value)',0 ); ?>
	                        </td>
	                        <td>
                                <input name="txt_date_from" id="txt_date_from" class="datepicker" style="width:60px" placeholder="From">
                                <input name="txt_date_to" id="txt_date_to" class="datepicker" style="width:60px" placeholder="To">
                            </td>
                            <td align="center">
                                <input type="button" name="button2" class="formbutton" value="Show" onClick="show_list_view ( document.getElementById('cbo_string_search_type').value+'_'+document.getElementById('cbo_company_name').value+'_'+document.getElementById('cbo_within_group').value+'_'+document.getElementById('cbo_party_name').value+'_'+document.getElementById('cbo_type').value+'_'+document.getElementById('txt_search_string').value+'_'+document.getElementById('cbo_pro_type').value+'_'+document.getElementById('cbo_order_type').value+'_'+document.getElementById('cbo_yd_type').value+'_'+document.getElementById('txt_date_from').value+'_'+document.getElementById('txt_date_to').value+'_'+document.getElementById('cbo_year_selection').value+'_'+document.getElementById('cbo_date_type').value, 'create_job_search_list_view', 'search_div', 'yd_material_issue_requisition_controller', 'setFilterGrid(\'tbl_data_list\',-1)')" style="width:100px;" />
                            </td>
                		</tr>
                		<tr>
                            <td colspan="12" align="center" valign="middle">
                                <? echo load_month_buttons(1);  ?>
                                <input type="hidden" name="hidden_details_id" id="hidden_details_id" class="text_boxes" style="width:70px">
                                <input type="hidden" id="hidden_job_no" name="hidden_job_no" value="" />
                            </td>
                        </tr>
                	</tbody>
		        </table>
			</form>
		</div>
		<div id="search_div" align="center">
			
		</div>
	</body>           
	<script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
	</html>
	<?
	exit();
}
if($action=="create_job_search_list_view")
{	
	$data=explode('_',$data);

	$party_arr=return_library_array( "select id, buyer_name from lib_buyer",'id','buyer_name');
	$comp_arr=return_library_array("select id, company_name from lib_company",'id','company_name');

	$search_type 			=trim(str_replace("'","",$data[0]));
	$cbo_company_name  		=trim(str_replace("'","",$data[1]));
	$cbo_within_group 		=trim(str_replace("'","",$data[2]));
	$cbo_party_name 		=trim(str_replace("'","",$data[3]));
	$search_by 				=trim(str_replace("'","",$data[4]));
	$search_str 			=trim(str_replace("'","",$data[5]));
	$cbo_pro_type 			=trim(str_replace("'","",$data[6]));
	$cbo_order_type 		=trim(str_replace("'","",$data[7]));
	$cbo_yd_type 			=trim(str_replace("'","",$data[8]));
	$txt_date_from 			=trim(str_replace("'","",$data[9]));
	$txt_date_to 			=trim(str_replace("'","",$data[10]));
	$cbo_year_selection 	=trim(str_replace("'","",$data[11]));
	$cbo_date_type 			=trim(str_replace("'","",$data[12]));
	

	if($cbo_company_name==0)
	{
		echo "<p style='margin-top: 10px;color:red'>Please Select Company Name first!!!</p>";
		die;
	}

	if($cbo_within_group==0)
	{
		echo "<p style='margin-top: 10px;color:red'>Please Select Within Group first!!!</p>";
		die;
	}

	if($cbo_order_type==0)
	{
		echo "<p style='margin-top: 10px;color:red'>Please Select Order Type first!!!</p>";
		die;
	}

	$company="";$within_group="";$condition = "";
		
	if($cbo_company_name!=0)
	{
		$company = " and a.company_id=$cbo_company_name";
	}

	// if($txt_receive_no!=0)
	// {
	// 	$condition .= " and a.receive_no_prefix_num=$txt_receive_no";
	// }

	if($cbo_within_group!=0)
	{
		$within_group = " and a.within_group=$cbo_within_group";
	}

	if($cbo_party_name!=0)
	{
		$condition .= " and a.party_id=$cbo_party_name";
	}

	if($cbo_pro_type!=0)
	{
		$condition .= " and a.pro_type=$cbo_pro_type";
	}

	if($cbo_order_type!=0)
	{
		$condition .= " and a.order_type=$cbo_order_type";
	}

	if($cbo_yd_type!=0)
	{
		$condition .= " and a.yd_type=$cbo_yd_type";
	}
	
	$date_con = '';


	if($cbo_date_type==1){
		$date_type = "and a.delivery_date between '";
	}else{
		$date_type = "and a.receive_date between '";
	}
	if($db_type==0)
	{ 
		if ($txt_date_from!="" &&  $txt_date_to!="") $date_con = $date_type.change_date_format($txt_date_from,'yyyy-mm-dd')."' and '".change_date_format($txt_date_to,'yyyy-mm-dd')."'"; else $date_con ="";
	}
	else
	{
		if ($txt_date_from!="" &&  $txt_date_to!="") $date_con = $date_type.change_date_format($txt_date_from, "", "",1)."' and '".change_date_format($txt_date_to, "", "",1)."'"; else $date_con ="";
	}


	if($search_type==1)
	{
		if($search_str!="")
		{
			if($search_by==1) $condition.="and a.job_no_prefix_num='$search_str'";
			else if($search_by==2) $condition.="and a.order_no='$search_str'";
			else if ($search_by==3) $condition.=" and b.style_ref = '$search_str' ";
			else if ($search_by==4) $condition.=" and b.sales_order_no = '$search_str' ";
		}
		
	}
	else if($search_type==2)
	{
		if($search_str!="")
		{
			if($search_by==1) $condition.="and a.job_no_prefix_num like '$search_str%'";
			else if($search_by==2) $condition.="and a.order_no like '$search_str%'";
			else if ($search_by==3) $condition.=" and b.style_ref like  '$search_str%' ";
			else if ($search_by==4) $condition.=" and b.sales_order_no like  '$search_str%' ";
		}
		
	}
	else if($search_type==3)
	{
		if($search_str!="")
		{
			if($search_by==1) $condition.="and a.job_no_prefix_num like '%$search_str'";
			else if($search_by==2) $condition.="and a.order_no like '%$search_str'";
			else if ($search_by==3) $condition.=" and b.style_ref like  '%$search_str' ";
			else if ($search_by==4) $condition.=" and b.sales_order_no like  '%$search_str' ";
		}
		
	}
	else if($search_type==4 || $search_type==0)
	{
		if($search_str!="")
		{
			if($search_by==1) $condition.="and a.job_no_prefix_num like '%$search_str%'";
			else if($search_by==2) $condition.="and a.order_no like '%$search_str%'";
			else if ($search_by==3) $condition.=" and b.style_ref like  '%$search_str%' ";
			else if ($search_by==4) $condition.=" and b.sales_order_no like  '%$search_str%' ";
		}		
	}   
			
	if($db_type==0)
	{ 
		$ins_year_cond="year(a.insert_date)";
	}
	else
	{
		$ins_year_cond="TO_CHAR(a.insert_date,'YYYY')"; 
	}
			    
	$yarn_count=return_library_array("select distinct(b.id) as id,b.yarn_count from lib_yarn_count b where b.status_active=1 and b.is_deleted=0","id","yarn_count"); 
	
	$sql=   "SELECT c.id,d.id as dtls_id, a.yd_job AS job_no, a.job_no_prefix_num, a.within_group, TO_CHAR (a.insert_date, 'YYYY') AS year, a.location_id, a.party_id, c.receive_date, a.order_no, a.delivery_date, a.booking_without_order, a.booking_type, a.order_type, a.yd_process, a.yd_type, a.pro_type,b.id as odr_dtls_id, b.lot, b.buyer_buyer,b.count_id, b.count_type,b.YARN_TYPE_ID, b.YARN_COMPOSITION_ID,b.sales_order_no, c.yd_trans_no, c.chalan_no, SUM (d.receive_qty) AS receive_qty FROM yd_ord_mst a, yd_ord_dtls b, yd_material_mst c, yd_material_dtls d WHERE a.id = b.mst_id AND a.entry_form = 374 AND a.status_active = 1 AND a.is_deleted = 0 AND c.yd_job_id = a.id AND c.id = d.mst_id AND b.id = d.job_dtls_id $company $within_group $condition $date_con GROUP BY c.id,d.id, a.yd_job, a.job_no_prefix_num, a.within_group, a.insert_date, a.location_id, a.party_id, c.receive_date, a.order_no, a.delivery_date, a.booking_without_order, a.booking_type, a.order_type, a.yd_process, a.yd_type, a.pro_type,b.id, b.lot, b.buyer_buyer, b.count_id, b.YARN_TYPE_ID, b.YARN_COMPOSITION_ID, b.count_type, b.sales_order_no, c.yd_trans_no, c.chalan_no ORDER BY a.party_id,b.count_id,b.YARN_COMPOSITION_ID,b.YARN_TYPE_ID ASC";
	// echo $sql;die;	

	$result = sql_select($sql);

	$sql_issued = "select a.yd_job, b.id, c.REQ_QTY
	from yd_ord_mst a, yd_ord_dtls b, yd_requisition_dtls c
	where  a.id=b.mst_id  and b.id = c.job_dtls_id $company $within_group $condition and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 and c.is_deleted=0 and c.status_active=1 group by a.yd_job, b.id, c.REQ_QTY";
	// echo $sql_issued;
	$issued_arr=array();
	$issued_data=sql_select($sql_issued);
	foreach ($issued_data as $key => $data) 
	{
		$issued_arr[$data['YD_JOB']][$data['ID']]+=$data['REQ_QTY'];
	}
	// echo "<pre>";print_r($issued_arr);die;
	?>
	<table class="rpt_table" border="1" cellpadding="0" cellspacing="0" rules="all" width="1200" >
		<thead>
            <th width="30">SL</th>
            <th width="100">Job no.</th>
            <th width="100">Party Name</th>
            <th width="60">Prod. Type</th>
            <th width="60">Within Group</th>
            <th width="80">WO No</th>
            <th width="80">Cust Buyer</th>
            <th width="80">Order Type</th>
            <th width="80">YD Type</th>
            <th width="80">Count</th>
            <th width="120">Composition</th>
			<th width="80">Yarn Type</th>
			<th width="80">Yarn lot</th>
			<th width="80">Rcvd Qty</th>
			<th width="80">Cumu. Issue Qty</th>
			<th width="80">Stock Qty</th>
        </thead>
	</table>
	<div style="width:1205px; max-height:220px;overflow-y:scroll;" >
		<table class="rpt_table" border="1" id="tbl_data_list" cellpadding="0" cellspacing="0" rules="all" width="1200">
			<tbody>
			<?php
			$i=1;
			$count_type_arr = array(1 => "Single",2 => "Double");
			foreach($result as $data)
			{
				if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";

				if($data[csf('within_group')]==1)
				{
					$party_name = $comp_arr[$data[csf('party_id')]];

				}
				else
				{
					$party_name = $party_arr[$data[csf('party_id')]];
				}
				?>
				
				<tr bgcolor="<? echo $bgcolor; ?>" onClick='js_set_value("<? echo $i.'***'.$data[csf('dtls_id')].'***'.$data[csf('job_no')]; ?>")' style="cursor:pointer"  id="search_<? echo $i;?>">
					
					<td align="center" width="30">
						<? echo $i; ?>
						<input type="hidden" name="txt_individual_id" id="txt_individual_id<?php echo $i; ?>" value="<?php echo $data[csf('dtls_id')]; ?>"/>
						<input type="hidden" name="txt_job_id" id="txt_job_id<?php echo $i; ?>" value="<?php echo $data[csf('job_no')]; ?>"/>
					</td>
					<td align="center" width="100"><? echo $data[csf('job_no')]; ?></td>
					<td align="center" width="100"><? echo $party_name; ?></td>
		            <td align="center" width="60"><? echo $w_pro_type_arr[$data[csf('pro_type')]]; ?></td>
		            <td align="center" width="60"><? echo $yes_no[$data[csf('within_group')]]; ?></td>
					<td align="center" width="80"><? echo $data[csf('order_no')]; ?></td>
		            <td align="center" width="80"><? echo $data[csf('buyer_buyer')]; ?></td>
		            <td align="center" width="80"><? echo $w_order_type_arr[$data[csf('order_type')]]; ?></td>
		            <td align="center" width="80"><? echo $yd_type_arr[$data[csf('yd_type')]]; ?></td>
		            <td align="center" width="80"><? echo $yarn_count[$data[csf('count_id')]]; ?></td>
		            <td align="center" width="120"><? echo $composition[$data[csf('YARN_COMPOSITION_ID')]]; ?></td>
		            <td align="center" width="80"><? echo $yarn_type[$data[csf('YARN_TYPE_ID')]]; ?></td> 
		            <td align="center" width="80"><? echo $data[csf('lot')]; ?></td> 
					<td align="center" width="80"><? echo number_format($data[csf('receive_qty')],2,".",""); ?></td>
					<td align="center" width="80"><? echo number_format($issued_arr[$data['JOB_NO']][$data['ODR_DTLS_ID']],2,".",""); ?></td>
					<td align="center" width="80"><? $stock=$data[csf('receive_qty')]-$issued_arr[$data['JOB_NO']][$data['ODR_DTLS_ID']]; echo number_format($stock,2,".",""); ?></td>
				</tr>
				
				<?php
					$i++;
				}
				?>
	        </tbody>
		</table>
	</div>
	<br>
		<table width="1250" cellspacing="0" cellpadding="0" style="border:none" align="center">
			<tr>
				<td align="center" height="30" valign="bottom">
				<div style="width:100%">
				<div style="width:50%; float:left;" align="left">
				<input type="checkbox" name="check_all" id="check_all" onClick='check_all_data()' /> Check / Uncheck All
				</div>
				<div style="width:50%; float:left" align="left">
				<input type="button" name="close" onClick="parent.emailwindow.hide();" class="formbutton" value="Close" style="width:100px" />
				</div>
				</div>
				</td>
			</tr>
		</table>
	<?

	exit();
}
if($action=="load_php_dtls_form")
{ 
	// echo $data;die;
	$data = explode("**", $data);
	$ids=$data[0];
	$job_no=str_replace("'","",$data[1]);

	$job_nos = explode(",", $job_no);
	for ($i=0; $i < count($job_nos); $i++) { 
		$job_no_list.="'".$job_nos[$i]."',";
	} 
	$job_no_list=chop($job_no_list,",");
	
	
	$count_arr = return_library_array("select id,yarn_count from lib_yarn_count where is_deleted=0 and status_active=1", 'id', 'yarn_count');
	//$count_arr = return_library_array("select id,construction from lib_yarn_count_determina_mst where is_deleted=0 and status_active=1", 'id', 'construction');
    $comp_arr = return_library_array("select id,composition_name from lib_composition_array where is_deleted=0 and status_active=1", 'id', 'composition_name');
    // $color_arr = return_library_array("select id,color_name from lib_color where is_deleted=0 and status_active=1", 'id', 'color_name');
	$groupName=return_library_array("select comp.id, comp.company_name from lib_company comp where comp.status_active=1 and comp.is_deleted=0  order by comp.company_name","id","company_name");
    $PartyName = return_library_array("select comp.id, comp.buyer_name from lib_buyer comp where comp.status_active=1 and comp.is_deleted=0  order by comp.buyer_name","id","buyer_name");
	
	//  print_r($PartyName);
	$sql=   "SELECT b.id,c.id as rcv_id, d.id as dtls_id, a.yd_job AS job_no, a.job_no_prefix_num, a.within_group, TO_CHAR (a.insert_date, 'YYYY') AS year, a.location_id, a.party_id, c.receive_date, a.order_no, a.delivery_date, a.booking_without_order, a.booking_type, a.order_type, a.yd_process, a.yd_type, a.pro_type, b.lot, b.buyer_buyer,b.count_id, b.count_type,b.YARN_TYPE_ID, b.YARN_COMPOSITION_ID,b.sales_order_no, c.yd_trans_no, c.chalan_no, SUM (d.receive_qty) AS receive_qty FROM yd_ord_mst a, yd_ord_dtls b, yd_material_mst c, yd_material_dtls d WHERE d.id in ($ids) and a.id = b.mst_id AND a.entry_form = 374 AND a.status_active = 1 AND a.is_deleted = 0 AND c.yd_job_id = a.id AND c.id = d.mst_id AND b.id = d.job_dtls_id GROUP BY b.id,c.id,d.id, a.yd_job, a.job_no_prefix_num, a.within_group, a.insert_date, a.location_id, a.party_id, c.receive_date, a.order_no, a.delivery_date, a.booking_without_order, a.booking_type, a.order_type, a.yd_process, a.yd_type, a.pro_type, b.lot, b.buyer_buyer, b.count_id, b.YARN_TYPE_ID, b.YARN_COMPOSITION_ID, b.count_type, b.sales_order_no, c.yd_trans_no, c.chalan_no ORDER BY a.party_id,b.count_id,b.YARN_COMPOSITION_ID,b.YARN_TYPE_ID ASC";
	// echo $sql;die;	
     
	$data_array = sql_select($sql);
	// print_r($data_array);die;

	$sql_issued = "select a.yd_job, b.id,d.id as issued_id, d.REQ_QTY
	from yd_ord_mst a, yd_ord_dtls b,yd_requisition_mst c, yd_requisition_dtls d
	where  a.id=b.mst_id  and b.id = d.job_dtls_id and c.id=d.mst_id and d.YD_JOB_NO in (".$job_no_list.") and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 and d.is_deleted=0 and d.status_active=1 and d.entry_form=699 group by a.yd_job, b.id,d.id, d.REQ_QTY";
	// echo $sql_issued;
	$issued_arr=array();
	$issued_data=sql_select($sql_issued);
	foreach ($issued_data as $key => $data) 
	{	
		$issued_arr[$data['YD_JOB']][$data['ID']]+=$data['REQ_QTY'];
	}
	// echo "<pre>";print_r($issued_arr);

	// unset($issued_arr);
	
   
    $counter = 1;
    foreach ($data_array as $row) 
	{
		
		
    	?>
		<tr> 

                <td align="right">
					<input type="hidden" name="orderno_<? echo $counter; ?>" id="orderno_<? echo $counter; ?>" value="<? echo $row[csf("order_no")]; ?>">
					<input type="hidden" name="metarialRcvid_<? echo $counter; ?>" id="metarialRcvid_<? echo $counter; ?>" value="<? echo $row[csf("rcv_id")]; ?>">
					<input type="hidden" name="metarialRcvDtlsid_<? echo $counter; ?>" id="metarialRcvDtlsid_<? echo $counter; ?>" value="<? echo $row[csf("dtls_id")]; ?>">
					<input type="hidden" name="orderDtlsid_<? echo $counter; ?>" id="orderDtlsid_<? echo $counter; ?>" value="<? echo $row[csf("id")]; ?>">
                    
                    
                    <input name="Serialid_<?php echo $counter; ?>" id="Serialid_<?php echo $counter; ?>" class="text_boxes" type="text"  style="width:30px;text-align:center" value="<?php echo $counter; ?>" readonly/>
                     
                     
                </td> 
                <td align="center">
                     <input type="text" id="cboWithinGroup_<?php echo $counter; ?>" name="cboWithinGroup_" class="text_boxes" style="width:80px;text-align:center" value="<?php echo $yes_no[$row[csf("within_group")]]; ?>" readonly>
                     <input type="hidden" id="hdnWithinGroup_<?php echo $counter; ?>" name="hdnWithinGroup_" class="text_boxes"  value="<?php echo $row[csf("within_group")]; ?>" readonly>
                </td>
                <td align="center">
                     <input type="text" id="cboPartyName_<?php echo $counter; ?>" name="cboPartyName_<?php echo $counter; ?>" class="text_boxes" style="width:120px;text-align:center" value="<?php echo $row[csf("within_group")]==2?$PartyName[$row[csf("party_id")]]:$groupName[$row[csf("party_id")]]; ?>" readonly>
                     <input type="hidden" id="hdnPartyName_<?php echo $counter; ?>" name="hdnPartyName_<?php echo $counter; ?>" class="text_boxes"  value="<?php echo $row[csf("party_id")]; ?>" readonly>
                </td>

                <td align="center">
                     <input type="text" id="txtJobNo_<?php echo $counter; ?>" name="txtJobNo_<?php echo $counter; ?>" class="text_boxes" style="width:120px;text-align:center" value="<?php echo $row[csf("job_no")]; ?>" readonly>
                </td>
                <td align="center">
                     <input type="text" id="txtOrderNo_<?php echo $counter; ?>" name="txtOrderNo_<?php echo $counter; ?>" class="text_boxes" style="width:120px;text-align:center" value="<?php echo $row[csf("order_no")]; ?>" readonly>
                </td>
                <td align="center">
                    <input name="txtJobDescription_<?php echo $counter; ?>" id="txtJobDescription_<?php echo $counter; ?>" class="text_boxes" type="text"  style="width:230px;text-align:center" value="<?php echo $count_arr[$row[csf("count_id")]]." ". $comp_arr[$row[csf("yarn_composition_id")]]." ".$yarn_type[$row[csf("YARN_TYPE_ID")]]; ?>" readonly/>
                </td>
               <td align="center">
                    <input name="txtLotNo_<?php echo $counter; ?>" id="txtLotNo_<?php echo $counter; ?>" class="text_boxes" type="text"  style="width:100px;text-align:center" value="<?php echo $row[csf("lot")]; ?>" readonly/>
                </td>
                <td align="right">
                    <input name="txtRvcdQty_<?php echo $counter; ?>" id="txtRvcdQty_<?php echo $counter; ?>" class="text_boxes_numeric" type="text"  style="width:80px" value="<?php echo $row[csf("receive_qty")]; ?>" readonly/>
                </td>
                 <td align="right">
                    <input name="txtStock_<?php echo $counter; ?>" id="txtStock_<?php echo $counter; ?>" class="text_boxes_numeric" type="text"  style="width:80px"  value="<? $stock=$row[csf("receive_qty")]-$issued_arr[$row['JOB_NO']][$row['ID']];echo number_format($stock,2,".",""); ?>"  readonly/>
                </td>
                 <td align="right">
                    <input name="txtCumuReqQty_<?php echo $counter; ?>" id="txtCumuReqQty_<?php echo $counter; ?>" class="text_boxes_numeric" type="text"  style="width:80px" value="<? echo number_format($issued_arr[$row['JOB_NO']][$row['ID']],2,".",""); ?>" readonly/>
                </td>
                
                <td align="right">
                    <input name="txtreqquantity_<?php echo $counter; ?>" id="txtreqquantity_<?php echo $counter; ?>" style="width:80px"  class="text_boxes_numeric" type="text" onKeyUp="check_iss_qty_ability(this.value,<? echo $counter; ?>); fnc_total_calculate();"   value=""  placeholder="<? echo number_format($stock,2,".","")  ;?>"   stock_qty="<? echo number_format($stock,2,".","")  ; ?>" rec_qty="<? echo number_format($rec_qty,2,".",""); ?>" />
                </td>
				<td align="right">
                    <input name="availableQty_<?php echo $counter; ?>" id="availableQty_<?php echo $counter; ?>" class="text_boxes_numeric" type="text"  style="width:100px" value="" placeholder="<? echo number_format($stock,2,".","") ;?>" readonly/>
                </td>
                 </tr>
    	
			<?
				$counter++;
	}
    ?>
    
	    </tr>

    <?
  exit();
}

if($action == 'load_php_mst_data_to_form') {
	
	
	$data = explode(",", $data);
	for ($i=0; $i < count($data); $i++) { 
		$job_no_list.="'".$data[$i]."',";
	} 
	$job_no_list=chop($job_no_list,",");

	
	$sql = "select a.id, a.company_id, a.location_id, a.order_type, a.yd_process, a.yd_type, a.pro_type, a.yd_job, b.receive_quantity
	from yd_ord_mst a, yd_material_mst b
	where a.yd_job in (".$job_no_list.") and b.yd_job_id=a.id and  a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0";

// echo $sql;die;
$data_array = sql_select($sql);
unset($sql);

// echo 'location id: '.$data_array[0][csf('location_id')];die;

// /*echo "document.getElementById('cbo_company_name').value = '".$data_array[0][csf('company_id')]."';\n";
echo "document.getElementById('cbo_pro_type').value = '".$data_array[0][csf('pro_type')]."';\n";
echo "document.getElementById('cbo_order_type').value = '".$data_array[0][csf('order_type')]."';\n";
echo "document.getElementById('cbo_yd_type').value = '".$data_array[0][csf('yd_type')]."';\n";
echo "document.getElementById('cbo_yd_process').value = '".$data_array[0][csf('yd_process')]."';\n";
// echo "document.getElementById('txt_receive_no').value = '".$data_array[0][csf('yd_trans_no')]."';\n";
// echo "load_drop_down('requires/yd_material_receive_controller', document.getElementById('cbo_company_name').value+'_'+document.getElementById('cbo_within_group').value, 'load_drop_down_buyer', 'buyer_td' );";


exit();
}

if ($action=="save_update_delete")
{
	// echo "<pre>"; print_r($_POST);exit;
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process )); 
	if ($operation==0)   
	{

		$con = connect();
		if($db_type==0) mysql_query("BEGIN");
			
		if($db_type==0) $insert_date_con="and YEAR(insert_date)=".date('Y',time())."";
		else if($db_type==2) $insert_date_con="and TO_CHAR(insert_date,'YYYY')=".date('Y',time())."";
			
		$new_issue_req_no=explode("*",return_mrr_number( str_replace("'","",$cbo_company_name),'', 'YDMIR' , date("Y",time()), 5, "select id,REQ_NO_PREFIX,REQ_NO_PREFIX_NUM from YD_REQUISITION_MST where company_id=$cbo_company_name and entry_form=699 $insert_date_con order by id desc", 'REQ_NO_PREFIX', 'REQ_NO_PREFIX_NUM' ));

		// if(is_duplicate_field( "a.chalan_no", "yd_material_mst a, yd_material_dtls b", "a.yd_job_id='$new_issue_no[0]' and a.trans_Type='$trans_Type' and a.entry_form=388 and a.chalan_no=$txt_issue_challan and b.order_id='$order_no_id' and b.material_description='$txt_material_description' and b.color_id='$color_id'" )==1)
		// {
		// 	//check_table_status( $_SESSION['menu_id'],0);
		// 	echo "11**0";
		// 	die;
		// }

		$id=return_next_id("id","YD_REQUISITION_MST",1) ;
		$field_array="id, entry_form, YD_REQ_NO, REQ_NO_PREFIX, REQ_NO_PREFIX_NUM, company_id, location_id, REQ_DATE, YD_JOB_NO, PRO_TYPE, ORDER_TYPE, YD_TYPE, YD_PROCESS, CHALAN_NO, REMARKS, inserted_by, insert_date, status_active, is_deleted";
		$data_array="(".$id.",'699','".$new_issue_req_no[0]."','".$new_issue_req_no[1]."',".$new_issue_req_no[2].",".$cbo_company_name.",".$cbo_location_name.",".$txt_req_date.",".$txt_job_no.",".$cbo_pro_type.",".$cbo_order_type.",".$cbo_yd_type.",".$cbo_yd_process.
		",'',".$txt_remarks.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."',1,0)";



		
		$txt_issue_req_no=$new_issue_req_no[0];//change_date_format($data[2], "dd-mm-yyyy", "-",1)
		
		$id1=return_next_id("id","YD_REQUISITION_DTLS",1);
		$field_array2="id, mst_id,entry_form, WITHIN_GROUP, PARTY_ID,YD_JOB_NO,ORDER_NO,JOB_DESCRIPTION, LOT, RCVD_QTY, STOCK_QTY, CUMU_REQ_QTY,REQ_QTY,AVAILABLE_QTY,JOB_DTLS_ID,RCV_ID,RCV_DTLS_ID, inserted_by, insert_date, status_active, is_deleted";
		$data_array2="";  $add_commaa=0;
		
		for($i=1; $i<=$total_row; $i++)				
		{
			$cboWithinGroup	= "cboWithinGroup_".$i;
			$cboPartyName	= "cboPartyName_".$i;
			$txtJobNo		= "txtJobNo_".$i;
			$txtOrderNo	    = "txtOrderNo_".$i;
			$txtJobDescription	= "txtJobDescription_".$i;
			$txtLotNo		= "txtLotNo_".$i;
			$txtRvcdQty		= "txtRvcdQty_".$i;
			$txtStock		= "txtStock_".$i;
			$txtCumuReqQty	= "txtCumuReqQty_".$i;
			$txtreqquantity	= "txtreqquantity_".$i;
			$availableQty	= "availableQty_".$i;
			$updatedtlsid	= "updatedtlsid_".$i;
			// $orderno		= "orderno_".$i;
			$orderDtlsid	= "orderDtlsid_".$i;
			$metarialRcvid	= "metarialRcvid_".$i;
			$metarialRcvDtlsid	= "metarialRcvDtlsid_".$i;
	
			$total_prv_req_qty=return_field_value("sum( b.REQ_QTY)  as quantity","yd_requisition_dtls b"," b.JOB_DTLS_ID=".str_replace("'","",$$orderDtlsid)."  and b.status_active=1 and b.is_deleted=0","quantity") ;
			
			$RvcdQty=str_replace("'",'',$$txtRvcdQty);
			$req_quantity=str_replace("'",'',$$txtreqquantity);
			$stock_qnty=$RvcdQty-$total_prv_req_qty;
			
			if($stock_qnty < $req_quantity)
			{
				echo "11**Requisition Qty Larger Than Stock Qty";
				disconnect($con);die;
			}
			


			if ($add_commaa!=0) $data_array2 .=",";

			$data_array2.="(".$id1.",".$id.",699,".$$cboWithinGroup.",".$$cboPartyName.",'".$$txtJobNo."',".$$txtOrderNo.",".$$txtJobDescription.",".$$txtLotNo.",".$$txtRvcdQty.",".$$txtStock.",".$$txtCumuReqQty.",".$$txtreqquantity.",".$$availableQty.",".$$orderDtlsid.",".$$metarialRcvid.",".$$metarialRcvDtlsid.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."',1,0)";
				 
			$id1=$id1+1; $add_commaa++;
			
		}
		$flag=1;
		
		 //echo "INSERT INTO YD_REQUISITION_MST (".$field_array.") VALUES ".$data_array; die;
		$rID=sql_insert("YD_REQUISITION_MST",$field_array,$data_array,0);
		if($flag==1 && $rID==1) $flag=1; else $flag=0;
		//echo $id1;
		// echo "10**INSERT INTO YD_REQUISITION_DTLS (".$field_array2.") VALUES ".$data_array2; die;
		$rID2=sql_insert("YD_REQUISITION_DTLS",$field_array2,$data_array2,1);	
		if($flag==1 && $rID2==1) $flag=1; else $flag=0;
		// echo "10**".$rID."**".$rID2	; die;
		if($db_type==0)
		{
			if($flag==1)
			{
				mysql_query("COMMIT");  
				echo "0**".str_replace("'",'',$txt_issue_req_no)."**".str_replace("'",'',$id)."**".str_replace("'",'',$txt_job_no);
			}
			else
			{
				mysql_query("ROLLBACK"); 
				echo "102**".str_replace("'",'',$txt_issue_req_no)."**".str_replace("'",'',$id)."**".str_replace("'",'',$txt_job_no);
			}
		}
		else if($db_type==2)
		{
			if($flag==1) 
			{
				oci_commit($con);
				echo "0**".str_replace("'",'',$txt_issue_req_no)."**".str_replace("'",'',$id)."**".str_replace("'",'',$txt_job_no);
			}
			else
			{
				oci_rollback($con);
				echo "10**".str_replace("'",'',$txt_issue_req_no)."**".str_replace("'",'',$id)."**".str_replace("'",'',$txt_job_no);
			}
		}
		disconnect($con);
		die;		
	}
	else if ($operation==1)   // Update Here
	{
		$flag = 1;
		$con = connect();

		// echo '10**emnei echo';die;

		if($db_type==0) mysql_query("BEGIN");

		$field_array_mst="location_id*req_date*remarks*updated_by*update_date";
		$data_array_mst="".$cbo_location_name."*".$txt_req_date."*".$txt_remarks."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'";

		$field_array_dtls = "REQ_QTY*AVAILABLE_QTY*updated_by*update_date";

		for($i = 1; $i <= $total_row; $i++) {
			$txtRvcdQty		= "txtRvcdQty_".$i;
			$txtreqquantity	= "txtreqquantity_".$i;
			$availableQty	= "availableQty_".$i;
			$orderDtlsid	= "orderDtlsid_".$i;
			$updatedtlsid	= "updatedtlsid_".$i;

			
			$total_prv_req_qty=return_field_value("sum( b.REQ_QTY)  as quantity","yd_requisition_dtls b"," b.JOB_DTLS_ID=".str_replace("'","",$$orderDtlsid)."  and b.status_active=1 and b.is_deleted=0","quantity") ;

			$prv_req_qty=return_field_value("b.REQ_QTY as qty","yd_requisition_dtls b"," b.JOB_DTLS_ID=".str_replace("'","",$$orderDtlsid)." and b.id=".str_replace("'","",$$updatedtlsid)." and b.status_active=1 and b.is_deleted=0","qty") ;
			
			$RvcdQty=str_replace("'",'',$$txtRvcdQty);
			$req_quantity=str_replace("'",'',$$txtreqquantity);
			$stock_qnty=$RvcdQty-$total_prv_req_qty+$prv_req_qty;
			
			if($stock_qnty < $req_quantity)
			{
				echo "11**Requisition Qty Larger Than Stock Qty";
				disconnect($con);die;
			}

			$data_array_dtls[str_replace("'","",$$updatedtlsid)]=explode("*",("".$$txtreqquantity."*".$$availableQty."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'"));
			$id_arr[]=str_replace("'", "", $$updatedtlsid);
		}

		// sql_update($strTable,$arrUpdateFields,$arrUpdateValues,$arrRefFields,$arrRefValues,$commit)
		$rID = sql_update("YD_REQUISITION_MST", $field_array_mst, $data_array_mst, "id", $update_id, 0);
		// echo $rID;
		// die;
		$flag = ($flag && $rID);	// return true if $flag is true and mst table update is successful

		// echo "10**"."YD_REQUISITION_MST", $field_array_mst, $data_array_mst, "id", $update_id ;die;
		// echo "10**" . bulk_update_sql_statement("YD_REQUISITION_DTLS", "id", $field_array_dtls, $data_array_dtls, $id_arr);die;
		/*var_dump($flag);die;*/

		$rID2 = execute_query(bulk_update_sql_statement("YD_REQUISITION_DTLS", "id", $field_array_dtls, $data_array_dtls, $id_arr), 1);
		// echo $rID."**".$rID2 ;die;
		$flag = ($flag && $rID2);	// return true if $flag is true and dtls table update is successful

		$flag = ($flag && $rID2);	// return true if $flag is true and dtls table insert is successful

		if($db_type==0) {
			if($flag) {
				mysql_query("COMMIT");
				echo "1**".str_replace("'",'',$txt_issue_req_no)."**".str_replace("'",'',$update_id)."**".str_replace("'",'',$txt_job_no);
				// echo "0**".str_replace("'", '', $txt_receive_no)."**".$id_mst."**".str_replace("'",'',$txt_job_no);
			} else {
				mysql_query("ROLLBACK");
				echo "10**".str_replace("'",'',$txt_issue_req_no)."**".str_replace("'",'',$update_id)."**".str_replace("'",'',$txt_job_no);
			}
		}
		else if($db_type==2) {
			if($flag) {
				oci_commit($con);
				echo "1**".str_replace("'",'',$txt_req_no)."**".str_replace("'",'',$update_id)."**".str_replace("'",'',$txt_job_no);
			} else {
				oci_rollback($con);
				echo "10**".str_replace("'",'',$txt_req_no)."**".str_replace("'",'',$update_id)."**".str_replace("'",'',$txt_job_no);
			}
		}

		disconnect($con);
		die;
	}
	else if ($operation==2)   // delete
	{
		$con = connect();
		if($db_type==0) mysql_query("BEGIN");
		 //echo $zero_val;
		 
		
		
		$field_array="status_active*is_deleted*updated_by*update_date";
		$data_array="0*1*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'"; 
		$data_array_dtls="0*1*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'"; 
			
		$flag=1;
		$rID=sql_update("YD_REQUISITION_MST",$field_array,$data_array,"id",$update_id,0);  
		if($rID==1 && $flag==1) $flag=1; else $flag=0;
		//echo "INSERT INTO sub_material_dtls (".$field_array.") VALUES ".$data_array_dtls; die;

		$rID1=sql_update("YD_REQUISITION_DTLS",$field_array,$data_array_dtls,"mst_id",$update_id,1); 
		if($rID1==1 && $flag==1) $flag=1; else $flag=0; 
				
		if($db_type==0)
		{
			if($flag==1)
			{
				mysql_query("COMMIT");  
				echo "2**".str_replace("'",'',$txt_req_no)."**".str_replace("'",'',$update_id)."**".str_replace("'",'',$txt_job_no);
			}
			else
			{
				mysql_query("ROLLBACK"); 
				echo "10**".str_replace("'",'',$txt_req_no)."**".str_replace("'",'',$update_id)."**".str_replace("'",'',$txt_job_no);
			}
		}
		else if($db_type==2)
		{
			if($flag==1)
			{
				oci_commit($con);
				echo "2**".str_replace("'",'',$txt_req_no)."**".str_replace("'",'',$update_id)."**".str_replace("'",'',$txt_job_no);
			}
			else
			{
				oci_rollback($con);
				echo "10**".str_replace("'",'',$txt_req_no)."**".str_replace("'",'',$update_id)."**".str_replace("'",'',$txt_job_no);
			}
		}
		disconnect($con); 
	}
}

if ($action=="issue_req_popup")
{
	echo load_html_head_contents("Popup Info","../../../", 1, 1, $unicode,'','');
	$data=explode("_",$data);
	$company=$data[0];
	$cbo_pro_type=$data[1];
	$cbo_order_type=$data[2];
	$cbo_yd_type=$data[3];
	?>
	<script>
		function js_set_value(id)
		{ 
			$("#hidden_mst_id").val(id);
			document.getElementById('selected_job').value=id;
			parent.emailwindow.hide();
		}
		
		// function fnc_load_party_order_popup(company,party_name,within_group)
		// {   //alert(company+'_'+party_name+'_'+within_group);	
		// 	load_drop_down( 'yd_material_issue_controller', company+'_'+within_group+'_'+party_name, 'load_drop_down_buyer_pop', 'buyer_td' );
		// }
		function search_by(val)
		{
			$('#txt_search_string').val('');
			if(val==1 || val==0) $('#search_by_td').html('YD Job No');
			else if(val==2) $('#search_by_td').html('Order No');
			else if(val==3) $('#search_by_td').html('Buyer Job');
			else if(val==4) $('#search_by_td').html('Buyer PO');
			else if(val==5) $('#search_by_td').html('Buyer Style');
		}		
	</script>
	</head>
	<body onLoad="fnc_load_party_order_popup(<? echo $company;?>,<? echo $party_name;?>,<? echo $within_group;?>)">
        <div align="center" style="width:100%;" >
            <form name="searchreceivefrm_1"  id="searchreceivefrm_1" autocomplete="off">
                <table width="670" cellspacing="0" cellpadding="0" border="1" rules="all" class="rpt_table" align="center">
                    <thead>
                        <tr>
                            <th colspan="10"><? echo create_drop_down( "cbo_string_search_type", 130, $string_search_type,'', 1, "-- Searching Type --" ); ?></th>
                        </tr>
                    	<tr>                	 
                            <th width="140">Company Name</th>
                            <th width="140">Within Group</th>
							<th width="70">Issue Req ID</th>
                            <th width="80">Prod. Type</th>
                            <th width="80">Order Type </th>
                            <th width="80">Y/D Type </th>
                            <th width="100" style="display:none">Search By</th>
                    		<th width="100" id="search_by_td" style="display:none">YD Job No</th>
                            <th width="100" colspan="2">Req Date Range</th>
                            <th><input type="reset" name="reset" id="reset" class="formbutton" value="Reset" style="width:70px;" /></th>
                        </tr>         
                    </thead>
                    <tbody>
                        <tr class="general">
                            <td> <input type="hidden" id="selected_job">  <!--  echo $data;-->
							<? 
								echo create_drop_down( "cbo_company_name", 140, "select id,company_name from lib_company comp where status_active =1 and is_deleted=0 $company_cond order by company_name","id,company_name", 1, "-- Select Company --", $company, ""); ?>
                            </td>
                            <td>
							<?
								echo create_drop_down( "cbo_within_group", 50, $yes_no,"", 0, "--  --",$within_group, "" ); ?>
							</td>
                            <td>
                                <input type="text" name="txt_search_common" id="txt_search_common" class="text_boxes_numeric" style="width:60px" placeholder="Issue Req ID" />
                            </td>
                            <td id="order_type_td">
								<?
								echo create_drop_down("cbo_pro_type", 80, $w_pro_type_arr,"", 1, "-- Select Type --",$cbo_pro_type,"", "","","","","",7 ); 
								?>
							</td>
							<td id="order_type_td">
								<?
								echo create_drop_down("cbo_order_type", 80, $w_order_type_arr,"", 1, "-- Select Type --",$cbo_order_type,"fnc_load_order_type(this.value);", "","","","","",7 ); 
								?>
							</td>
							<td id="yd_type_td">
								<?
								echo create_drop_down("cbo_yd_type", 80, $yd_type_arr,"", 1, "-- Select Y/D Type --",$cbo_yd_type,"", "","","","","",7 ); 
								?>
							</td>
                            <td style="display:none">
								<?
                                    $search_by_arr=array(1=>"YD Job No",2=>"Order No",3=>"Buyer Job",4=>"Buyer PO",5=>"Buyer Style");
                                    echo create_drop_down( "cbo_type",100, $search_by_arr,"",0, "",1,'search_by(this.value)',0 );
                                ?>
                            </td>
                            <td align="center" style="display:none">
                                <input type="text" name="txt_search_string" id="txt_search_string" class="text_boxes" style="width:100px" placeholder="" />
                            </td>
                            <td>
                                <input name="txt_date_from" id="txt_date_from" class="datepicker" style="width:50px" placeholder="From">
                            </td>
                            <td>
                                <input name="txt_date_to" id="txt_date_to" class="datepicker" style="width:50px" placeholder="To">
                            </td> 
                            <td align="center">
                                <input type="button" name="button2" class="formbutton" value="Show" onClick="show_list_view ( document.getElementById('cbo_company_name').value+'_'+document.getElementById('cbo_within_group').value+'_'+document.getElementById('txt_search_common').value+'_'+document.getElementById('cbo_pro_type').value+'_'+document.getElementById('cbo_yd_type').value+'_'+document.getElementById('cbo_string_search_type').value+'_'+document.getElementById('cbo_order_type').value+'_'+document.getElementById('cbo_type').value+'_'+document.getElementById('txt_search_string').value+'_'+document.getElementById('txt_date_from').value+'_'+document.getElementById('txt_date_to').value, 'create_issue_search_list_view', 'search_div', 'yd_material_issue_requisition_controller', 'setFilterGrid(\'tbl_po_list\',-1)')" style="width:70px;" /></td>
                        </tr>
                        <tr>
                            <td colspan="10" align="center" valign="middle">
								<? echo load_month_buttons(1);  ?>
                                <input type="hidden" name="hidden_mst_id" id="hidden_mst_id" class="text_boxes" style="width:70px">
                            </td>
                        </tr>
                        <tr>
                            <td colspan="10" align="center" valign="top" id=""><div id="search_div"></div></td>
                        </tr>
                    </tbody>
                </table>    
            </form>
        </div>
	</body>           
	<script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
	</html>
	<?
	exit();
}
if($action=="create_issue_search_list_view")
{
	$data=explode('_',$data);
	
	$within_group =$data[1];
	$search_type =$data[6];
	$search_by=str_replace("'","",$data[7]);
	$search_str=trim(str_replace("'","",$data[8]));
	/*echo $search_str;die;*/

	if ($data[0]!=0) $company=" and a.company_id='$data[0]'"; else { echo "Please Select Company First."; die; }
	if ($within_group!=0) $withinGroup=" and b.WITHIN_GROUP='$within_group'"; else $withinGroup="";
	
	if ($data[2]!="" &&  $data[3]!="") $req_date = "and a.REQ_DATE between '".change_date_format($data[2], "", "",1)."' and '".change_date_format($data[3], "", "",1)."'"; else $req_date ="";
	$job_cond=""; $style_cond=""; $po_cond=""; $search_com_cond="";
	if($search_type==1)
	{
		if($search_str!="")
		{
			if($search_by==1) $search_com_cond="and a.REQ_NO_PREFIX_NUM='$search_str'";	
			else if ($search_by==3) $search_com_cond=" and a.REQ_NO_PREFIX_NUM = '$search_str' ";
		}
	}
	else if($search_type==4 || $search_type==0)
	{
		if($search_str!="")
		{
			if($search_by==1) $search_com_cond="and a.REQ_NO_PREFIX_NUM like '$search_str%'";  
			 
			else if ($search_by==3) $search_com_cond=" and a.REQ_NO_PREFIX_NUM like '$search_str%'";  
			
		}
		
	}
	else if($search_type==2)
	{
		if($search_str!="")
		{
			if($search_by==1) $search_com_cond="and a.REQ_NO_PREFIX_NUM like '$search_str%'";  
			
			
			else if ($search_by==3) $search_com_cond=" and a.REQ_NO_PREFIX_NUM like '$search_str%'";  
			
		}
		
	}
	else if($search_type==3)
	{
		if($search_str!="")
		{
			if($search_by==1) $search_com_cond="and a.REQ_NO_PREFIX_NUM like '%$search_str'";  
			
			
			else if ($search_by==3) $search_com_cond=" and a.REQ_NO_PREFIX_NUM like '%$search_str'";  
			
		}
		
	}	
	
	
	$comp=return_library_array("select id, company_name from lib_company",'id','company_name');
	// $po_arr=return_library_array( "select id,order_no from yd_ord_dtls",'id','order_no');

	// $insert_date_cond="TO_CHAR(a.REQ_DATE,'YYYY')";//$insert_date_cond as year,;

	$sql= "select a.id, a.YD_REQ_NO, a.REQ_NO_PREFIX_NUM, a.COMPANY_ID,a.YD_JOB_NO,a.PRO_TYPE,a.ORDER_TYPE,a.YD_PROCESS, a.REQ_DATE, b.within_group
	from YD_REQUISITION_MST a, YD_REQUISITION_DTLS b 
	where a.entry_form=699 and a.id=b.mst_id  and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $req_date $company $withinGroup $search_com_cond
	group by a.id, a.YD_REQ_NO, a.REQ_NO_PREFIX_NUM, a.COMPANY_ID,a.YD_JOB_NO,a.PRO_TYPE,a.ORDER_TYPE,a.YD_PROCESS,a.REQ_DATE,b.within_group order by a.id DESC";
	// echo $sql;
	$result = sql_select($sql);
	?>
    <div>
     	<table cellspacing="0" cellpadding="0" border="1" rules="all" width="620" class="rpt_table">
            <thead>
                <th width="40" >SL</th>
                <th width="70" >Req No</th>
                <th width="70" >Within group</th>
                <th width="120" >Order Type </th>
                <th width="80" >Req Date</th>        
            </thead>
     	</table>
     <div style="width:620px; max-height:270px;overflow-y:scroll;" >	 
        <table cellspacing="0" cellpadding="0" border="1" rules="all" width="620" class="rpt_table" id="tbl_po_list" style="margin-bottom:20px">
			<?
			$i=1;
            foreach( $result as $row )
            {
                
				?>
					<tr bgcolor="<? echo $bgcolor; ?>" style="text-decoration:none;cursor:pointer" onClick="js_set_value('<? echo $row[csf("id")];?>');" > 
						<td width="40" align="center"><? echo $i; ?></td>
						<td width="70" align="center"><? echo $row[csf("YD_REQ_NO")]; ?></td>
                        <td width="70" align="center"><? echo $yes_no[$row[csf("within_group")]]; ?></td>
                        <td width="120"><?php echo $w_order_type_arr[$row[csf('ORDER_TYPE')]]; ?></td>
						<td width="80"><? echo change_date_format($row[csf("req_date")]);  ?></td>	
                        
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
if ($action=="load_php_data_to_form")
{
	
	$nameArray=sql_select( "select id, YD_REQ_NO,company_id, location_id, REQ_DATE,PRO_TYPE,ORDER_TYPE,YD_PROCESS,YD_TYPE,YD_JOB_NO, remarks from YD_REQUISITION_MST where id='$data' and is_deleted=0 and  status_active=1");

	foreach ($nameArray as $row)
	{	
		echo "document.getElementById('txt_req_no').value 		= '".$row[csf("YD_REQ_NO")]."';\n";
		echo "document.getElementById('cbo_company_name').value 	= '".$row[csf("company_id")]."';\n";
		echo "$('#cbo_company_name').attr('disabled','true')".";\n"; 
		echo "load_drop_down( 'requires/yd_material_issue_requisition_controller', document.getElementById('cbo_company_name').value, 'load_drop_down_location', 'location_td' );\n"; 		
		
		echo "document.getElementById('cbo_location_name').value	= '".$row[csf("location_id")]."';\n"; 
		echo "document.getElementById('txt_req_date').value 	= '".change_date_format($row[csf("REQ_DATE")])."';\n";  
		
	    echo "document.getElementById('cbo_pro_type').value            = '".$row[csf("PRO_TYPE")]."';\n";
	    echo "document.getElementById('cbo_order_type').value            = '".$row[csf("ORDER_TYPE")]."';\n";
	    echo "document.getElementById('cbo_yd_type').value            = '".$row[csf("YD_TYPE")]."';\n";
	    echo "document.getElementById('update_id').value            = '".$row[csf("id")]."';\n";
	    echo "document.getElementById('txt_job_no').value 		= '".$row[csf("YD_JOB_NO")]."';\n";
	    echo "document.getElementById('txt_remarks').value 		= '".$row[csf("remarks")]."';\n";

	    
		//echo "set_button_status(0, '".$_SESSION['page_permission']."', 'fnc_material_receive',1);\n";
		 
		echo "$('#cbo_pro_type').attr('disabled','true')".";\n"; 
		echo "$('#cbo_order_type').attr('disabled','true')".";\n"; 
		echo "$('#cbo_yd_type').attr('disabled','true')".";\n"; 
	}
	exit();
}
if($action=="load_php_dtls_form_aftersave")
{  
	// $data=explode("**",$data);
	
	$update_id=$data;
	
	
	$count_arr = return_library_array("select id,yarn_count from lib_yarn_count where is_deleted=0 and status_active=1", 'id', 'yarn_count');
	//$count_arr = return_library_array("select id,construction from lib_yarn_count_determina_mst where is_deleted=0 and status_active=1", 'id', 'construction');
    $comp_arr = return_library_array("select id,composition_name from lib_composition_array where is_deleted=0 and status_active=1", 'id', 'composition_name');
    // $color_arr = return_library_array("select id,color_name from lib_color where is_deleted=0 and status_active=1", 'id', 'color_name');
	$groupName=return_library_array("select comp.id, comp.company_name from lib_company comp where comp.status_active=1 and comp.is_deleted=0  order by comp.company_name","id","company_name");
    $PartyName = return_library_array("select comp.id, comp.buyer_name from lib_buyer comp where comp.status_active=1 and comp.is_deleted=0  order by comp.buyer_name","id","buyer_name");

	$sql = "select  a.YD_JOB_NO as job_id, b.id , b.WITHIN_GROUP, b.PARTY_ID, b.YD_JOB_NO as job_no, b.ORDER_NO, b.JOB_DESCRIPTION, b.LOT,b.RCVD_QTY , b.STOCK_QTY, b.CUMU_REQ_QTY, b.REQ_QTY, b.AVAILABLE_QTY, b.JOB_DTLS_ID,SUM (d.receive_qty) AS receive_qty
	from YD_REQUISITION_MST a, YD_REQUISITION_DTLS b,yd_material_mst c, yd_material_dtls d
	where a.id =$update_id and a.id=b.mst_id AND b.rcv_id=c.id and b.rcv_dtls_id=d.id and a.entry_form=699 and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 group by a.YD_JOB_NO , b.id, b.WITHIN_GROUP, b.PARTY_ID, b.YD_JOB_NO, b.ORDER_NO, b.JOB_DESCRIPTION, b.LOT, b.RCVD_QTY, b.STOCK_QTY, b.CUMU_REQ_QTY, b.REQ_QTY, b.AVAILABLE_QTY, b.JOB_DTLS_ID";//d.id
	
	// echo $sql;die;
	$data_array = sql_select($sql);

	$job_no = $data_array[0]["JOB_ID"]; 
	$job_nos = explode(",", $job_no);
	for ($i=0; $i < count($job_nos); $i++) { 
		$job_no_list.="'".$job_nos[$i]."',";
	} 
	$job_no_list=chop($job_no_list,",");
	// echo $job_no_list;die;
	$sql_issued = "select a.yd_job, b.id, d.REQ_QTY
	from yd_ord_mst a, yd_ord_dtls b,yd_requisition_mst c, yd_requisition_dtls d
	where  a.id=b.mst_id  and b.id = d.job_dtls_id and c.id=d.mst_id and d.YD_JOB_NO in (".$job_no_list.") and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 and d.is_deleted=0 and d.status_active=1 and d.entry_form=699 group by a.yd_job, b.id, d.REQ_QTY";
	// echo $sql_issued;
	$issued_arr=array();
	$issued_data=sql_select($sql_issued);
	foreach ($issued_data as $key => $data) 
	{	
		$issued_arr[$data['YD_JOB']][$data['ID']]+=$data['REQ_QTY'];
	}
	// echo "<pre>";print_r($issued_arr);

    $counter = 1;

    foreach ($data_array as $row)
	{
		
    	?> 
		<tr> 

			<td align="right">
				<input type="hidden" name="orderno_<? echo $counter; ?>" id="orderno_<? echo $counter; ?>" value="<? echo $row[csf("order_no")]; ?>">
				<input type="hidden" name="metarialRcvid_<? echo $counter; ?>" id="metarialRcvid_<? echo $counter; ?>" value="<? echo $row[csf("rcv_id")]; ?>">
				<input type="hidden" name="metarialRcvDtlsid_<? echo $counter; ?>" id="metarialRcvDtlsid_<? echo $counter; ?>" value="<? echo $row[csf("dtls_id")]; ?>">
				<input type="hidden" name="orderDtlsid_<? echo $counter; ?>" id="orderDtlsid_<? echo $counter; ?>" value="<? echo $row[csf("JOB_DTLS_ID")]; ?>">
				<input type="hidden" name="updatedtlsid_<? echo $counter; ?>" id="updatedtlsid_<? echo $counter; ?>" value="<? echo $row[csf("id")]; ?>">
				
				
				<input name="Serialid_<?php echo $counter; ?>" id="Serialid_<?php echo $counter; ?>" class="text_boxes" type="text"  style="width:30px;text-align:center" value="<?php echo $counter; ?>" readonly/>
				
				
			</td> 
			<td align="center">
				<input type="text" id="cboWithinGroup_<?php echo $counter; ?>" name="cboWithinGroup_" class="text_boxes" style="width:80px;text-align:center" value="<?php echo $yes_no[$row[csf("within_group")]]; ?>" readonly>
				<input type="hidden" id="hdnWithinGroup_<?php echo $counter; ?>" name="hdnWithinGroup_" class="text_boxes"  value="<?php echo $row[csf("within_group")]; ?>" readonly>
			</td>
			<td align="center">
				<input type="text" id="cboPartyName_<?php echo $counter; ?>" name="cboPartyName_<?php echo $counter; ?>" class="text_boxes" style="width:120px;text-align:center" value="<?php echo $row[csf("within_group")]==2?$PartyName[$row[csf("party_id")]]:$groupName[$row[csf("party_id")]]; ?>" readonly>
				<input type="hidden" id="hdnPartyName_<?php echo $counter; ?>" name="hdnPartyName_<?php echo $counter; ?>" class="text_boxes"  value="<?php echo $row[csf("party_id")]; ?>" readonly>
			</td>

			<td align="center">
				<input type="text" id="txtJobNo_<?php echo $counter; ?>" name="txtJobNo_<?php echo $counter; ?>" class="text_boxes" style="width:120px;text-align:center" value="<?php echo $row[csf("job_no")]; ?>" readonly>
			</td>
			<td align="center">
				<input type="text" id="txtOrderNo_<?php echo $counter; ?>" name="txtOrderNo_<?php echo $counter; ?>" class="text_boxes" style="width:120px;text-align:center" value="<?php echo $row[csf("order_no")]; ?>" readonly>
			</td>
			<td align="center">
				<input name="txtJobDescription_<?php echo $counter; ?>" id="txtJobDescription_<?php echo $counter; ?>" class="text_boxes" type="text"  style="width:230px;text-align:center" value="<?php echo $count_arr[$row[csf("count_id")]]." ". $row[csf("JOB_DESCRIPTION")]; ?>" readonly/>
			</td>
			<td align="center">
				<input name="txtLotNo_<?php echo $counter; ?>" id="txtLotNo_<?php echo $counter; ?>" class="text_boxes" type="text"  style="width:100px;text-align:center" value="<?php echo $row[csf("lot")]; ?>" readonly/>
			</td>
			<td align="right">
				<input name="txtRvcdQty_<?php echo $counter; ?>" id="txtRvcdQty_<?php echo $counter; ?>" class="text_boxes_numeric" type="text"  style="width:80px" value="<?php echo $row[csf("receive_qty")]; ?>" readonly/>
			</td>
			<td align="right">
				<input name="txtStock_<?php echo $counter; ?>" id="txtStock_<?php echo $counter; ?>" class="text_boxes_numeric" type="text"  style="width:80px"  value="<? $stock=$row[csf("receive_qty")]-$issued_arr[$row['JOB_NO']][$row['JOB_DTLS_ID']]+$row['REQ_QTY'];echo number_format($stock,2,".",""); ?>"  readonly/>
			</td>
			<td align="right">
				<input name="txtCumuReqQty_<?php echo $counter; ?>" id="txtCumuReqQty_<?php echo $counter; ?>" class="text_boxes_numeric" type="text"  style="width:80px" value="<? $cumuQty=$issued_arr[$row['JOB_NO']][$row['JOB_DTLS_ID']]-$row['REQ_QTY'];echo number_format($cumuQty,2,".",""); ?>" readonly/>
			</td>

			<td align="right">
				<input name="txtreqquantity_<?php echo $counter; ?>" id="txtreqquantity_<?php echo $counter; ?>" style="width:80px"  class="text_boxes_numeric" type="text" onKeyUp="check_iss_qty_ability(this.value,<? echo $counter; ?>);  fnc_total_calculate();"   value="<?php echo number_format($row['REQ_QTY'],2,".","") ; ?>"  placeholder="<?php $available=$stock+$row['REQ_QTY']; echo $available=number_format($available,2,".","") ; ?>"   stock_qty="<? echo number_format($stock,2,".","")  ; ?>" rec_qty="<? echo number_format($rec_qty,2,".",""); ?>" />
				
			</td>
			<td align="right">
				<input name="availableQty_<?php echo $counter; ?>" id="availableQty_<?php echo $counter; ?>" class="text_boxes_numeric" type="text"  style="width:100px" value="<? $available_qty=$stock-$row['REQ_QTY']; echo number_format($available_qty,2,".","")  ; ?>" placeholder="<?  echo number_format($stock,2,".","")  ; ?>" readonly/>
			</td>
 		</tr>

	<?
	$counter++;
	}
	?>

</tr>

    <?
  exit();
}

if($action=="yd_material_issue_print")
{
	extract($_REQUEST);
	$data = explode('*', $data);

	$company_library = return_library_array("select id, company_name from lib_company", "id", "company_name");
	$buyer_library = return_library_array("select id, buyer_name from lib_buyer", "id", "buyer_name");
	$size_arr=return_library_array( "select id,size_name from lib_size where status_active=1 and is_deleted=0", "id", "size_name" );
	$color_arr = return_library_array("select id, color_name from lib_color", 'id', 'color_name');
	$imge_arr=return_library_array( "select master_tble_id,image_location from common_photo_library where form_name='company_details' and file_type=1",'master_tble_id','image_location');
	$location_arr = return_library_array("select id, location_name from lib_location", 'id', 'location_name');
	$comp_arr = return_library_array("select id,composition_name from lib_composition_array where is_deleted=0 and status_active=1", 'id', 'composition_name');
	$party_arr=return_library_array( "select id, buyer_name from lib_buyer",'id','buyer_name');
	$count_arr = return_library_array("select id,yarn_count from lib_yarn_count where is_deleted=0 and status_active=1", 'id', 'yarn_count');


	$dataArray=sql_select( "SELECT id, YD_REQ_NO, REQ_DATE, company_id, LOCATION_ID, PRO_TYPE, ORDER_TYPE, YD_PROCESS, YD_TYPE, REMARKS from yd_requisition_mst where id=$data[1] and entry_form=699 and is_deleted=0 and  status_active=1");

	// $groupName=return_library_array("select comp.id, comp.company_name from lib_company comp where comp.status_active=1 and comp.is_deleted=0  order by comp.company_name","id","company_name");
    // $PartyName = return_library_array("select comp.id, comp.buyer_name from lib_buyer comp where comp.status_active=1 and comp.is_deleted=0  order by comp.buyer_name","id","buyer_name");

	$sql = "select  a.YD_JOB_NO as job_id, b.id , b.WITHIN_GROUP, b.PARTY_ID, b.YD_JOB_NO as job_no, b.ORDER_NO, b.JOB_DESCRIPTION, b.LOT,b.RCVD_QTY , b.STOCK_QTY, b.CUMU_REQ_QTY, b.REQ_QTY, b.AVAILABLE_QTY, b.JOB_DTLS_ID,SUM (d.receive_qty) AS receive_qty
	from YD_REQUISITION_MST a, YD_REQUISITION_DTLS b,yd_material_mst c, yd_material_dtls d
	where a.id =$data[1] and a.id=b.mst_id AND b.rcv_id=c.id and b.rcv_dtls_id=d.id and a.entry_form=699 and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 group by a.YD_JOB_NO , b.id, b.WITHIN_GROUP, b.PARTY_ID, b.YD_JOB_NO, b.ORDER_NO, b.JOB_DESCRIPTION, b.LOT, b.RCVD_QTY, b.STOCK_QTY, b.CUMU_REQ_QTY, b.REQ_QTY, b.AVAILABLE_QTY, b.JOB_DTLS_ID";//d.id
	
	// echo $sql;die;
	$data_array_dtls = sql_select($sql);

	$job_no = $data_array_dtls[0]["JOB_ID"]; 
	$job_nos = explode(",", $job_no);
	for ($i=0; $i < count($job_nos); $i++) { 
		$job_no_list.="'".$job_nos[$i]."',";
	} 
	$job_no_list=chop($job_no_list,",");
	// echo $job_no_list;die;
	


	$sql_issued = "select a.yd_job, b.id, d.REQ_QTY
	from yd_ord_mst a, yd_ord_dtls b,yd_requisition_mst c, yd_requisition_dtls d
	where  a.id=b.mst_id  and b.id = d.job_dtls_id and c.id=d.mst_id and d.YD_JOB_NO in (".$job_no_list.") and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 and d.is_deleted=0 and d.status_active=1 and d.entry_form=699 group by a.yd_job, b.id, d.REQ_QTY";
	// echo $sql_issued;
	$issued_arr=array();
	$issued_data=sql_select($sql_issued);
	foreach ($issued_data as $key => $val) 
	{	
		$issued_arr[$val['YD_JOB']][$val['ID']]+=$val['REQ_QTY'];
	}
	// echo "<pre>"; print_r($issued_arr);die;



	$company = $data[0];
	$issue_id = $data[1];
	$system_no = $data[3];

	if($data[3]==1){
		$party=$company_library[$dataArray[0][csf('party_id')]];
	}else{
		$party=$party_arr[$dataArray[0][csf('party_id')]];
	}

	foreach ($data_array_dtls as $row)
	{
		$total_qty+=$row['REQ_QTY'];
	}
	?> 
    
    <div style="width:1220px; font-size:6px">
        <table width="100%" cellpadding="0" cellspacing="0" >
            <tr>
                <td width="70" align="right" style="display: none;"> 
                    <img  src='../../<? echo $imge_arr[str_replace("'","",$data[0])]; ?>' height='100%' width='100%' />
                </td>
                <td>
                    <table width="800" cellspacing="0" align="center">
                        <tr>
                            <td align="center" style="font-size:20px"><strong ><? echo $company_library[$data[0]]; ?></strong></td>
                        </tr>
                        <tr>
                            <td align="center"  style="font-size:16px"><strong> <? echo $location_arr[$dataArray[0][csf('location_id')]]; ?></strong></td>
                        </tr>
						<tr><tr>&nbsp;</tr></tr>
                        <tr>
                            <td align="center" style="font-size:14px"><strong><u><? echo $data[2]; ?></u></strong></td>
                        </tr>
                    </table>
                </td>
            </tr>
        </table>   
        <br>
        <table width="100%" cellpadding="0" cellspacing="0" >  
            <tr>
            	<td width="130"><strong>Requisigtion No:</strong></td>
                <td width="175"><?=$dataArray[0][csf('YD_REQ_NO')];?></td>
				<td width="150"><strong>Requisigtion Date:</strong></td>
                <td width="175"><?=$dataArray[0][csf('REQ_DATE')];?></td>
                <td width="100"><strong>Prod Type:</strong></td>
                <td width="130"><?=$w_pro_type_arr[$dataArray[0][csf('PRO_TYPE')]];?></td>
				<td width="100"><strong>Order Type:</strong></td>
				<td><?=$w_order_type_arr[$dataArray[0][csf('ORDER_TYPE')]];?></td>
            </tr>
            <tr>
            	<td><strong>Y/D Type: </strong></td>
                <td><?=$yd_type_arr[$dataArray[0][csf('YD_TYPE')]];?></td>
                <td><strong>Y/D Process: </strong></td>
                <td><?=$yd_process_arr[$dataArray[0][csf('YD_PROCESS')]];?></td>
            	<td colspan="4"><strong>Total Requistion Qty:  </strong><?=number_format($total_qty,2,".","");?></td>
                
            </tr>
            <tr>
            	<td width="130"> <strong>Remarks:</strong> <?=$dataArray[0][csf('remarks')]; ?></td> 
                <td colspan="7"></td>           
            </tr>
        </table>
        <br>
        
            <table cellspacing="0" width="1200" border="1" rules="all" class="rpt_table">
                <thead bgcolor="#dddddd" align="center">
					<th width="30">SL</th>
					<th width="80">Within Group</th>
					<th width="120">Party</th>
					<th width="120">Job No</th>
					<th width="120">Wo No</th>
					<th width="230">Job Description</th>
					<th width="100">Lot No</th>
					<th width="80">Rcvd Qty.</th>
					<th width="80">Stock Qty.</th>
					<th width="80">Cumu. Req. Qty.</th>
					<th width="80">Requisition Qty</th>
					<th width="80">Abvailable Req.</th>
                </thead>
				<tbody>
				<?
				$counter = 1;
				$total_qty=0;
				foreach ($data_array_dtls as $row)
				{
					
					?> 
					<tr> 
						<td align="right">
							<?php echo $counter; ?>
						</td> 
						<td align="center">
							<?php echo $yes_no[$row[csf("within_group")]]; ?>
							
						</td>
						<td align="center">
							<?php echo $row[csf("within_group")]==2?$party_arr[$row[csf("party_id")]]:$company_library[$row[csf("party_id")]]; ?>
							
						</td>

						<td align="center">
							<?php echo $row[csf("job_no")]; ?>
						</td>
						<td align="center">
							<?php echo $row[csf("order_no")]; ?>
						</td>
						<td align="center">
							<?php echo $count_arr[$row[csf("count_id")]]." ". $row[csf("JOB_DESCRIPTION")]; ?>
						</td>
						<td align="center">
							<?php echo $row[csf("lot")]; ?>
						</td>
						<td align="right">
							<?php echo $row[csf("receive_qty")]; ?>
						</td>
						<td align="right">
							<? $stock=$row[csf("receive_qty")]-$issued_arr[$row['JOB_NO']][$row['JOB_DTLS_ID']];echo number_format($stock,2,".",""); ?>
						</td>
						<td align="right">
							<? echo number_format($issued_arr[$row['JOB_NO']][$row['JOB_DTLS_ID']],2,".",""); ?>
						</td>

						<td align="right">
							<?php echo number_format($row['REQ_QTY'],2,".","") ; ?>
						</td>
						<td align="right">
							<?  echo number_format($stock,2,".","")  ; ?>
						</td>
					</tr>

					<?
					$total_qty+=$row['REQ_QTY'];
					$counter++;
				}
				?>
    			</tbody>
				<tfoot>
					<tr>
						<td colspan="10" align="right"><b> Total</b></td>
						<td align="right" ><?=number_format($total_qty,2,".","")?></td>
						<td></td>
					</tr>
				</tfoot>
            </table>
            <br>
			<?// echo signature_table(154, $com_id, "1200px"); ?>
         </div>
		<script type="text/javascript" src="../../js/jquery.js"></script>
        <script type="text/javascript" src="../../js/jquerybarcode.js"></script>
		<script>
			//for gate pass barcode
			function generateBarcodeGatePass(valuess)
			{
				//var zs = '<?php // echo $x; ?>';
				var value = valuess;//$("#barcodeValue").val();
				var btype = 'code39';//$("input[name=btype]:checked").val();
				var renderer = 'bmp';// $("input[name=renderer]:checked").val();
				var settings = {
					output: renderer,
					bgColor: '#FFFFFF',
					color: '#000000',
					barWidth: 1,
					barHeight: 30,
					moduleSize: 5,
					posX: 10,
					posY: 20,
					addQuietZone: 1
				};
				$("#gate_pass_barcode_img_id_1").html('11');
				value = {code: value, rect: false};
				$("#gate_pass_barcode_img_id_1").show().barcode(value, btype, settings);
			}
			var value = '<? echo $gatePassDataArr[$system_no]['gate_pass_id']; ?>';
			
			if( value != '')
			{
				generateBarcodeGatePass('<? echo strtoupper($gatePassDataArr[$system_no]['gate_pass_id']); ?>');
			}
		</script>
        <div style="page-break-after:always;"></div>
        <?
	exit();
}
