<?
header('Content-type:text/html; charset=utf-8');
session_start();
if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");
require_once('../../../includes/common.php');
$user_id=$_SESSION['logic_erp']['user_id'];
$data=$_REQUEST['data'];
$action=$_REQUEST['action'];

if ($action=="load_drop_down_buyer")
{
	echo create_drop_down( "cbo_buyer_name", 120, "select buy.id,buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company='$data' and buy.id in (select  buyer_id from  lib_buyer_party_type where party_type in (1,3,21,90))  $buyer_cond order by buyer_name","id,buyer_name", 1, "- All Buyer -", $selected, "" );     	 
	exit();
}

if ($action=="load_drop_down_location")
{
    extract($_REQUEST);
    $choosenCompany = $data;  
	echo create_drop_down( "cbo_lc_location_name", 140, "SELECT id,location_name from lib_location where status_active =1 and is_deleted=0 and company_id in( $choosenCompany) group by id,location_name  order by location_name","id,location_name", 1, "-- Select location --", $selected, "" );
	exit();
}
//--------------------------------------------------------------------------------------------------------------------
if($action=="job_no_popup")
{
	echo load_html_head_contents("Job Info", "../../../", 1, 1,'','','');
	extract($_REQUEST);
	//echo $cbo_year_id.'aziz';
	?>
	<script>
	
		function js_set_value(str)
		{
			var splitData = str.split("_");
			//alert (splitData[1]);
			$("#hide_job_id").val(splitData[0]); 
			$("#hide_job_no").val(splitData[1]); 
			parent.emailwindow.hide();
		}
    </script>
	</head>
	<body>
	<div align="center">
		<form name="styleRef_form" id="styleRef_form">
			<fieldset style="width:580px;">
	            <table width="570" cellspacing="0" cellpadding="0" border="1" rules="all" align="center" class="rpt_table" id="tbl_list">
	            	<thead>
	                    <th>Buyer</th>
	                    <th>Search By</th>
	                    <th id="search_by_td_up" width="170">Please Enter Job No</th>
	                    <th><input type="reset" name="button" class="formbutton" value="Reset" style="width:100px;" onClick="reset_form('styleRef_form','search_div','','','','');"></th> 					<input type="hidden" name="hide_job_id" id="hide_job_id" value="" />
	                    <input type="hidden" name="hide_job_no" id="hide_job_no" value="" />
	                </thead>
	                <tbody>
	                	<tr>
	                        <td align="center">
	                        	 <? 
									echo create_drop_down( "cbo_buyer_name", 140, "select buy.id, buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company=$companyID $buyer_cond and buy.id in (select buyer_id from lib_buyer_party_type where party_type in (1,3,21,90)) order by buy.buyer_name","id,buyer_name",1, "-- All Buyer--",$buyer_name,"",0 );
								?>
	                        </td>                 
	                        <td align="center">	
	                    	<?
	                       		$search_by_arr=array(1=>"Job No",2=>"Style Ref");
								$dd="change_search_event(this.value, '0*0', '0*0', '../../') ";							
								echo create_drop_down( "cbo_search_by", 130, $search_by_arr,"",0, "--Select--", "",$dd,0 );
							?>
	                        </td>     
	                        <td align="center" id="search_by_td">				
	                            <input type="text" style="width:130px" class="text_boxes" name="txt_search_common" id="txt_search_common" />	
	                        </td> 	
	                        <td align="center">
	                        	<input type="button" name="button" class="formbutton" value="Show" onClick="show_list_view ('<? echo $companyID; ?>'+'**'+document.getElementById('cbo_buyer_name').value+'**'+document.getElementById('cbo_search_by').value+'**'+document.getElementById('txt_search_common').value+'**'+'<? echo $cbo_year_id; ?>', 'create_job_no_search_list_view', 'search_div', 'grey_to_finish_roll_process_loss_report_controller', 'setFilterGrid(\'tbl_list_search\',-1)');" style="width:100px;" />
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
if($action=="create_job_no_search_list_view")
{
	$data=explode('**',$data);
	$company_id=$data[0];
	$year_id=$data[4];
	$month_id=$data[5];
	//echo $month_id;
	$buyer_arr=return_library_array( "select id, buyer_name from lib_buyer",'id','buyer_name');
	$company_arr=return_library_array( "select id, company_name from lib_company",'id','company_name');
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
	$search_by=$data[2];
	$search_string="%".trim($data[3])."%";
	if($search_by==2) $search_field="style_ref_no"; else $search_field="job_no";
	//$year="year(insert_date)";
	if($db_type==0) $year_field="YEAR(insert_date) as year"; 
	else if($db_type==2) $year_field="to_char(insert_date,'YYYY') as year";
	else $year_field="";
	if($db_type==0)
	{
	if($year_id!=0) $year_cond=" and year(insert_date)=$year_id"; else $year_cond="";	
	}
	else if($db_type==2)
	{
	$year_field_con=" and to_char(insert_date,'YYYY')";
	if($year_id!=0) $year_cond="$year_field_con=$year_id"; else $year_cond="";	
	}
	//if($month_id!=0) $month_cond=" and month(insert_date)=$month_id"; else $month_cond="";
	$arr=array (0=>$company_arr,1=>$buyer_arr);
	$sql= "select id, job_no, job_no_prefix_num, company_name, buyer_name, style_ref_no, $year_field from wo_po_details_master where status_active=1 and is_deleted=0 and company_name=$company_id and $search_field like '$search_string' $buyer_id_cond $year_cond  order by job_no";
	echo create_list_view("tbl_list_search", "Company,Buyer Name,Job No,Year,Style Ref. No", "120,130,80,60","600","240",0, $sql , "js_set_value", "id,job_no_prefix_num", "", 1, "company_name,buyer_name,0,0,0", $arr , "company_name,buyer_name,job_no_prefix_num,year,style_ref_no", "",'','0,0,0,0,0','') ;
	exit(); 
} // Job Search end


if($action=="batch_no_search_popup")
{
	echo load_html_head_contents("Batch No Info", "../../../", 1, 1,'','','');
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
			//alert(str);
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
				name += selected_name[i] + '*';
			}
			
			id = id.substr( 0, id.length - 1 );
			name = name.substr( 0, name.length - 1 );
			
			$('#hide_order_id').val( id );
			$('#hide_order_no').val( name );
		}
    </script>
	</head>
	<body>
	<div align="center">
		<form name="styleRef_form" id="styleRef_form">
			<fieldset style="width:760px;">
	            <table width="750" cellspacing="0" cellpadding="0" border="1" rules="all" align="center" class="rpt_table">
	            	<thead>
	                  
	                    <th>Batch No </th>
	                    <th>Batch Date</th>
	                    <th><input type="reset" name="button" class="formbutton" value="Reset"  style="width:100px;"></th> 
	                    <input type="hidden" name="hide_order_no" id="hide_order_no" value="" />
	                    <input type="hidden" name="hide_order_id" id="hide_order_id" value="" />
	                </thead>
	                <tbody>
	                	<tr>
	                        <td align="center" id="search_by_td">				
	                            <input type="text" style="width:130px" class="text_boxes" name="txt_search_common" id="txt_search_common" />	
	                        </td> 
	                        <td align="center">
	                            <input type="text" name="txt_date_from" id="txt_date_from" class="datepicker" style="width:70px" readonly>To
	                            <input type="text" name="txt_date_to" id="txt_date_to" class="datepicker" style="width:70px" readonly>
	                        </td>	
	                        <td align="center">
	                        	<input type="button" name="button" class="formbutton" value="Show" onClick="show_list_view ('<? echo $companyID; ?>'+'**'+document.getElementById('txt_search_common').value+'**'+document.getElementById('txt_date_from').value+'**'+document.getElementById('txt_date_to').value, 'create_batch_no_search_list_view', 'search_div', 'grey_to_finish_roll_process_loss_report_controller', 'setFilterGrid(\'tbl_list\',-1)');" style="width:100px;" />
	                    	</td>
	                    </tr>
	                    <tr>
	                        <td colspan="5" height="20" valign="middle"><? echo load_month_buttons(1); ?></td>
	                    </tr>
	            	</tbody>
	           	</table>
	            <div style="margin-top:15px" id="search_div"></div>
			</fieldset>
		</form>
	</div>
	</body>           
	<script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
	</html>
	<?
	exit(); 
}
if($action=="create_batch_no_search_list_view")
{
	$data=explode('**',$data);
	$company_id=$data[0];
	//echo $data[1];
	
	$batch_no=$data[1];
	$search_string="%".trim($data[3])."%";
	//if($batch_no=='') $search_field="b.po_number";  else  $search_field="b.po_number";
	if ($batch_no=="") $batch_no_cond=""; else $batch_no_cond=" and a.batch_no in ('$batch_no') "; 
		
	
	$start_date =trim($data[2]);
	$end_date =trim($data[3]);	
	if($start_date!="" && $end_date!="")
	{
		if($db_type==0)
		{
			$date_cond="and a.batch_date between '".change_date_format($start_date,"yyyy-mm-dd", "-")."' and '".change_date_format($end_date,"yyyy-mm-dd", "-")."'";
		}
		else
		{
			$date_cond="and a.batch_date between '".change_date_format($start_date,'','',1)."' and '".change_date_format($end_date,'','',1)."'";	
		}
	}
	else
	{
		$date_cond="";
	}

	$sql="select a.id,a.batch_no,a.batch_for,a.booking_no,a.color_id,a.batch_weight from pro_batch_create_mst a where a.company_id=$company_id and a.is_deleted=0 and a.status_active=1 $date_cond $batch_no_cond";	
	$arr=array(1=>$color_library,3=>$batch_for);
	echo  create_list_view("tbl_list", "Batch no,Color,Booking no, Batch for,Batch weight ", "150,100,150,100,70","700","350",0, $sql, "js_set_value", "id,batch_no", "", 1, "0,color_id,0,batch_for,0", $arr , "batch_no,color_id,booking_no,batch_for,batch_weight", "",'','0','',1) ;
	exit();
}//Batch Search End


if($action=="report_generate")
{
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process )); 
	$company_name= str_replace("'","",$cbo_company_name);
	$location_id= str_replace("'","",$location_id);
	$buyer_name= str_replace("'","",$cbo_buyer_name);
	$job_no=trim(str_replace("'","",$txt_job_no));
	$txt_order_no = trim(str_replace("'","",$txt_order_no));
	$batch_no=str_replace("'","",$txt_batch_no);
	$txt_barcode_no= str_replace("'","",$txt_barcode_no);
	
	
	if(str_replace("'","",$txt_date_from)!="" && str_replace("'","",$txt_date_to)!="")
	{
		if($db_type==0)
		{
			$start_date=change_date_format(str_replace("'","",$txt_date_from),"yyyy-mm-dd","");
			$end_date=change_date_format(str_replace("'","",$txt_date_to),"yyyy-mm-dd","");
		}
		else if($db_type==2)
		{
			$start_date=change_date_format(str_replace("'","",$txt_date_from),"","",1);
			$end_date=change_date_format(str_replace("'","",$txt_date_to),"","",1);
		}
		$date_cond=" and a.receive_date between '$start_date' and '$end_date'";
		
	}


	$job_no=str_replace("'","",$txt_job_no);
	if ($job_no=="") $job_no_cond=""; else $job_no_cond=" and a.job_no_prefix_num in ('$job_no') ";
	if ($batch_no=="") $batch_no_cond=""; else $batch_no_cond=" and a.batch_no in ('$batch_no') ";

	if ($txt_booking_no!='') $booking_no_cond="  and a.booking_no LIKE '%$txt_booking_no%'"; else $booking_no_cond="";
	if ($hide_booking_id!=0) $booking_no_cond.="  and a.booking_no_id in($hide_booking_id) "; else $booking_no_cond.="";
	if ($working_company==0) $workingCompany_cond=""; else $workingCompany_cond="  and a.working_company_id=".$working_company." ";
	if ($company_name==0) $workingCompany_cond.=""; else $workingCompany_cond.="  and a.company_id=".$company_name." ";

	if ($working_company==0) $knit_company_cond=""; else $knit_company_cond="  and a.knitting_company=".$working_company." ";
	if ($company_name==0) $knit_company_cond.=""; else $knit_company_cond.="  and a.company_id=".$company_name." ";
	$com_con='';
	if(!empty($company_name) && $company_name>0)
	{
		$com_con=" and company_id=$company_name ";
	}

	
	ob_start();
	

	//echo  $po_cond_for_in.'D';
	
	$search_cond="";
	if($txt_barcode_no)
	{
		$search_cond .=" and c.barcode_no=$txt_barcode_no";
	}
	if($batch_no)
	{
		$search_cond .=" and d.batch_no='$batch_no'";
	}

	if($txt_order_no)
	{
		$search_cond .=" and e.po_number='$txt_order_no'";
	}

	if($job_no)
	{
		$search_cond .=" and f.job_no='$job_no'";
	}

	if($location_id)
	{
		$search_cond .=" and a.location_id='$location_id'";
	}

	$sql_data.="SELECT a.receive_date, a.location_id, a.knitting_source, a.knitting_company, d.batch_no, d.booking_no, c.roll_no, b.prod_id, b.body_part_id, b.color_id, c.qnty, c.qc_pass_qnty, c.reject_qnty, b.gsm, b.width, b.dia_width_type, b.fabric_description_id, c.barcode_no, f.buyer_name as buyer_id, f.job_no, e.po_number, f.style_ref_no
		from inv_receive_master a, pro_finish_fabric_rcv_dtls b, pro_roll_details c, pro_batch_create_mst d, wo_po_break_down e, wo_po_details_master f
		where a.id=b.mst_id and b.id=c.dtls_id and b.batch_id=d.id and c.po_breakdown_id=e.id and e.job_id=f.id and c.booking_without_order=0 and a.entry_form=66 and c.entry_form=66 and a.status_active=1 and b.status_active=1 and c.status_active=1 and a.company_id=$company_name $search_cond $date_cond";

	if($job_no =="" && $txt_order_no =="")
	{
		$sql_data .="UNION ALL SELECT a.receive_date, a.location_id, a.knitting_source, a.knitting_company, d.batch_no, d.booking_no, c.roll_no, b.prod_id, b.body_part_id, b.color_id, c.qnty, c.qc_pass_qnty, c.reject_qnty, b.gsm, b.width, b.dia_width_type, b.fabric_description_id, c.barcode_no, e.buyer_id, null as job_no, null as po_number, null as style_ref_no
		from inv_receive_master a, pro_finish_fabric_rcv_dtls b, pro_roll_details c, pro_batch_create_mst d, wo_non_ord_samp_booking_mst e
		where a.id=b.mst_id and b.id=c.dtls_id and b.batch_id=d.id and c.po_breakdown_id=e.id and c.booking_without_order=1 and a.entry_form=66 and c.entry_form=66 and a.status_active=1 and b.status_active=1 and c.status_active=1 and a.company_id=$company_name $search_cond $date_cond";
	}
	
	//echo $sql_data;
	
	$nameArray=sql_select($sql_data);

	$company_library=return_library_array( "select id, company_short_name from lib_company", "id", "company_short_name"  );
	$supplier_library=return_library_array( "select id, short_name from lib_supplier", "id", "short_name"  );
	$buyer_library=return_library_array( "select id, buyer_name from lib_buyer", "id", "buyer_name"  );
	$color_library=return_library_array( "select id,color_name from lib_color", "id", "color_name"  );

	$sql_deter="select a.id, a.construction, b.copmposition_id, b.percent from lib_yarn_count_determina_mst a, lib_yarn_count_determina_dtls b where a.id=b.mst_id and a.status_active=1 and b.status_active=1";
	$deter_array=sql_select($sql_deter);
	if(count($deter_array)>0)
	{
		foreach( $deter_array as $row )
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
	}
	unset($deter_array);

	?>
	<style type="text/css">
		.word_wrap{
			word-wrap: break-word;
			word-break: break-all;
		}

	</style>
	<div>	
		<table width="2100" cellspacing="0" cellpadding="0" border="0" rules="all" >
		    <tr class="form_caption">
		        <td colspan="22" align="center" style="border:none;font-size:16px; font-weight:bold"> <? echo $report_title; ?></td>
		    </tr>
		    <tr class="form_caption">
		        <td colspan="22" align="center"><?  if($company_name!=0) echo $company_library[$company_name]; ?><br>
		        </b>
		        <?
				if(str_replace("'","",$txt_date_from)!="" && str_replace("'","",$txt_date_to)!="")
				{
					echo str_replace("'","",$txt_date_from) . ' To '.str_replace("'","",$txt_date_to);
				}
		        ?> </b>
		        </td>
		    </tr>
		</table>
		<table cellspacing="0" cellpadding="0" border="1" rules="all" width="1900" class="rpt_table">
			<thead>
				<th width="30">SL</th>
				<th width="80">Barcode No</th>
				<th width="100">Batch No</th>
				<th width="100">Production date	</th>	
				<th width="100">Booking No</th>			
				<th width="100">Buyer Name</th>
				<th width="100">Order No<br>Style</th>
				<th width="100">Job No</th>
				<th width="100">Prod. Source</th>				
				<th width="100">Dye/Finishing Company</th>
				<th width="100">Product ID</th>
				<th width="150">Body Part</th>
				<th width="100">Fabric Type</th>
				<th width="100">Color</th>
				<th width="50">GSM</th>
				<th width="50">Dia</th>
				<th width="80">Dia width type</th>
				<th width="50">Roll No</th>
				<th width="50">Grey Qnty</th>
				<th width="80">Process Loss qnty</th>
				<th width="80">Qc pass qnty</th>
				<th >Process loss %</th>
			</thead>
		</table>

		<div style="width:1900px; overflow-y:scroll; max-height:350px;" id="scroll_body">
			<table cellspacing="0" cellpadding="0" border="1" rules="all" width="1900" class="rpt_table" id="table_body">
				<?
				$knitting_source_array= array(1=>'In-house',3=>'Outbound');
			    $i=1;
			    $grey_total_booking_qty=$process_loss_total_qty=$finish_total_qty=0;
				$process_loss_sum=0;
				foreach ($nameArray as $row )
				{
					if($row[csf('knitting_source')]==1)
					{
						$knitting_company=$company_library[$row[csf('knitting_company')]];
					}
					else
					{
						$knitting_company=$supplier_library[$row[csf('knitting_company')]];
					}


					$process_loss = ($row[csf('reject_qnty')]/$row[csf('qnty')])*100;
					if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";

					?>
					<tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
						<td width="30" align="right"><? echo $i; ?></td>
						<td width="80" align="right"><? echo $row[csf('barcode_no')]; ?></td>
						<td width="100" align="center" class="word_wrap"><? echo $row[csf('batch_no')]; ?></td>
						<td width="100" align="center"><? echo $row[csf('receive_date')]; ?></td>
						<td width="100" align="center"><? echo $row[csf('booking_no')]; ?></td>
						<td width="100" align="center" class="word_wrap"><? echo $buyer_library[$row[csf('buyer_id')]]; ?></td>
						<td width="100" align="center" class="word_wrap"><? echo $row[csf('po_number')]; ?><div style='border-bottom:1px solid black;'></div><? echo $row[csf('style_ref_no')]; ?></td>
						<td width="100" align="center"><? echo $row[csf('job_no')]; ?></td>
						<td width="100" align="center"><? echo $knitting_source_array[$row[csf('knitting_source')]]; ?></td>
						<td width="100" align="center"><? echo $knitting_company; ?></td>
						<td width="100" align="center"><? echo $row[csf('prod_id')]; ?></td>
						<td width="150" align="center"><? echo $body_part[$row[csf('body_part_id')]]; ?></td>
						<td width="100" align="center" class="word_wrap"><? echo $composition_arr[$row[csf('fabric_description_id')]]; ?></td>
						<td width="100" align="center" class="word_wrap"><? echo $color_library[$row[csf('color_id')]]; ?></td>
						<td width="50" align="right"><? echo $row[csf('gsm')]; ?></td>
						<td width="50" align="right"><? echo $row[csf('width')]; ?></td>
						<td width="80" align="right"><? echo $fabric_typee[$row[csf('dia_width_type')]]; ?></td>
						<td width="50" align="right"><? echo $row[csf('roll_no')]; ?></td>
						<td width="50" align="right"><? echo number_format($row[csf('qnty')],2); ?></td>
						<td width="80" align="right"><? echo number_format($row[csf('reject_qnty')],2); ?></td>
						<td width="80" align="right"><? echo number_format($row[csf('qc_pass_qnty')],2); ?></td>
						<td  align="right"><? echo number_format($process_loss,2); ?></td>
					</tr>
					<?

					$i++;

					$grey_total_booking_qty+=$row[csf('qnty')];
					$process_loss_total_qty+=$row[csf('reject_qnty')];
					$finish_total_qty+=$row[csf('qc_pass_qnty')];
				}
			    ?>
			</table>

			<table cellspacing="0" cellpadding="0" border="1" rules="all" width="1900" class="rpt_table" id="report_table_footer">
				<tfoot>
					<th width="30">&nbsp;</th>
					<th width="80">&nbsp;</th>
					<th width="100">&nbsp;</th>
					<th width="100">&nbsp;</th>	
					<th width="100">&nbsp;</th>			
					<th width="100">&nbsp;</th>
					<th width="100">&nbsp;</th>
					<th width="100">&nbsp;</th>
					<th width="100">&nbsp;</th>				
					<th width="100">&nbsp;</th>
					<th width="100">&nbsp;</th>
					<th width="150">&nbsp;</th>
					<th width="100">&nbsp;</th>
					<th width="100">&nbsp;</th>
					<th width="50">&nbsp;</th>
					<th width="50">&nbsp;</th>
					<th width="80">&nbsp;</th>
					<th width="50">&nbsp;</th>
					<th width="50" id="value_total_grey"><? echo number_format($grey_total_booking_qty,2) ;?></th>
					<th width="80" id="value_total_process"><? echo number_format($process_loss_total_qty,2);?></th>
					<th width="80" id="value_total_finish"><? echo number_format($finish_total_qty,2);?></th>
					<th id="value_total_process_loss"><? echo number_format(($process_loss_total_qty/$grey_total_booking_qty)*100,2); ?></th>
				</tfoot>
			</table>
		</div>	
	</div>
    <?
	foreach (glob("$user_id*.xls") as $filename) 
	{
		if( @filemtime($filename) < (time()-$seconds_old) )
		@unlink($filename);
	}
	//---------end------------//
	$name=time();
	$filename=$user_id."_".$name.".xls";
	$create_new_doc = fopen($filename, 'w');
	$is_created = fwrite($create_new_doc,ob_get_contents());
	$filename=$user_id."_".$name.".xls";
	echo "$total_data####$filename";
	exit();
}

?>	
