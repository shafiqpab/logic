<?
session_start();
if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");
require_once('../../../includes/common.php');

$_SESSION['page_permission']=$permission;
$user_id = $_SESSION['logic_erp']["user_id"];
if( $_SESSION['logic_erp']['user_id'] == "" ) { header("location:login.php"); die; }
//--------------------------------------------------------------------------------------------------------------------
$data=$_REQUEST['data'];
$action=$_REQUEST['action'];

if ($action=="load_drop_down_buyer")
{
	echo create_drop_down( "cbo_buyer_name", 120, "select buy.id,buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company='$data' and buy.id in (select  buyer_id from  lib_buyer_party_type where party_type in (1,3,21,90))  $buyer_cond order by buyer_name","id,buyer_name", 1, "- All Buyer -", $selected, "" );
	exit();
}

if($action=="load_drop_down_knitting_com")
{
	$data = explode("**",$data);
	$company_id=$data[1];

	if($data[0]==1)
	{
		echo create_drop_down( "cbo_supplier_name", 120, "select comp.id, comp.company_name from lib_company comp where comp.status_active=1 and comp.is_deleted=0 $company_cond order by comp.company_name","id,company_name",1, "-- Select --", $company_id, "","" );
	}
	else if($data[0]==3)
	{
		echo create_drop_down( "cbo_supplier_name", 120, "select a.id,a.supplier_name from lib_supplier a, lib_supplier_party_type b where a.id=b.supplier_id and a.status_active=1 group by a.id,a.supplier_name order by a.supplier_name","id,supplier_name", 1, "-- Select --", 0, "" );
	}
	else
	{
		echo create_drop_down( "cbo_supplier_name", 120, $blank_array,"",1, "-- Select --", 0, "fnc_reset_form(2)" );
	}
	exit();
}

if ($action=="load_drop_down_supplier")
{
	echo create_drop_down( "cbo_supplier_name", 120, "select a.id,a.supplier_name from lib_supplier a, lib_supplier_party_type b,lib_supplier_tag_company c where  a.id=b.supplier_id and a.id=c.supplier_id and  c.tag_company=$data  and a.status_active=1 and a.is_deleted=0 group by a.id,a.supplier_name order by a.supplier_name","id,supplier_name", 1, "-- Select --", "", "" );
	exit();

}

if($action=="booking_no_popup")
{
	echo load_html_head_contents("Booking Info", "../../../", 1, 1,'','','');
	extract($_REQUEST);
	?>
	<script>
			var selected_id = new Array; var selected_name = new Array;

		function check_all_data()
		{
			var tbl_row_count = document.getElementById( 'tbl_list_div' ).rows.length;
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

		function js_set_value2( str ) {

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


			$('#hide_job_id').val( id );
			$('#hide_job_no').val( name );
			$("#hide_booing_type").val(str[3]);
		}
    </script>

</head>

<body>
<div align="center">
	<form name="styleRef_form" id="styleRef_form">
		<fieldset style="width:680px;">
            <table width="670" cellspacing="0" cellpadding="0" border="1" rules="all" align="center" class="rpt_table" id="tbl_list">
            	<thead>
                    <th>Buyer</th>
                    <th>Booking Type</th>

                    <th>Search By</th>
                    <th id="search_by_td_up" width="170">Please Enter Booking No</th>
                    <th><input type="reset" name="button" class="formbutton" value="Reset"  style="width:100px;"></th>
                    <input type="hidden" name="hide_job_no" id="hide_job_no" value="" />
                    <input type="hidden" name="hide_job_id" id="hide_job_id" value="" />
                    <input type="hidden" name="hide_booing_type" id="hide_booing_type" value="" />

                </thead>
                <tbody>
                	<tr>
                        <td align="center">
                        	 <?
								echo create_drop_down( "cbo_buyer_name", 140, "select buy.id, buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company=$companyID $buyer_cond and buy.id in (select buyer_id from lib_buyer_party_type where party_type in (1,3,21,90)) order by buy.buyer_name","id,buyer_name",1, "-- All Buyer--",$cbo_buyer_name,"",0 );
							?>
                        </td>
                         <td align="center">
                    	<?
                       		$search_by_arr=array(1=>"With Order",2=>"Without Order",3=>"AOP-Without Order");
							$dd="change_search_event(this.value, '0*0', '0*0', '../../') ";
							echo create_drop_down( "cbo_booking_type", 100, $search_by_arr,"",0, "--Select--", 1,$dd,0 );
						?>
                        </td>
                        <td align="center">
                    	<?
                       		$search_by_arr=array(1=>"Job No",2=>"Style Ref",3=>"Booking No");
							$dd="change_search_event(this.value, '0*0*0', '0*0*0', '../../') ";
							echo create_drop_down( "cbo_search_by", 130, $search_by_arr,"",0, "--Select--", "3",$dd,0 );
						?>
                        </td>
                        <td align="center" id="search_by_td">
                            <input type="text" style="width:130px" class="text_boxes" name="txt_search_common" id="txt_search_common" />
                        </td>
                        <td align="center">
                        	<input type="button" name="button" class="formbutton" value="Show" onClick="show_list_view ('<? echo $companyID; ?>'+'**'+document.getElementById('cbo_buyer_name').value+'**'+document.getElementById('cbo_search_by').value+'**'+document.getElementById('txt_search_common').value+'**'+'<? echo $cbo_year; ?>'+'**'+'<? echo $cbo_month_id; ?>'+'**'+document.getElementById('cbo_booking_type').value, 'create_booking_no_search_list_view', 'search_div', 'fabric_issue_to_finish_process_and_service_rcv_report_controller', 'setFilterGrid(\'tbl_list_search\',-1)');" style="width:100px;" />
                    </td>
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

if($action=="create_booking_no_search_list_view")
{
	$data=explode('**',$data);
	$company_id=$data[0];
	$year_id=$data[4];
	$month_id=$data[5];
	$booking_type=$data[6];


	$buyer_arr=return_library_array( "select id, buyer_name from lib_buyer",'id','buyer_name');
	$company_arr=return_library_array( "select id, company_name from lib_company",'id','company_name');

	if($data[1]==0)
	{
		if ($_SESSION['logic_erp']["data_level_secured"]==1)
		{
			if($_SESSION['logic_erp']["buyer_id"]!="")
			{
				$buyer_id_cond=" and a.buyer_name in (".$_SESSION['logic_erp']["buyer_id"].")";
				$buyer_id_cond2=" and a.buyer_id in (".$_SESSION['logic_erp']["buyer_id"].")";
			}
			else
			{
				$buyer_id_cond="";$buyer_id_cond2="";
			}
		}
		else
		{
			$buyer_id_cond="";$buyer_id_cond2="";
		}
	}
	else
	{
		$buyer_id_cond=" and a.buyer_name=$data[1]";
		$buyer_id_cond2=" and a.buyer_id=$data[1]";
	}

	$search_by=$data[2];
	$search_string="%".trim($data[3])."";

	if($search_by==2)
	{
		 $search_field="a.style_ref_no";
	}
	else if($search_by==1)
	{
		 $search_field="a.job_no_prefix_num";
	}
	else
	{
		$search_field="b.booking_no";
		$wo_search_field="a.wo_no";
	}
	//$year="year(insert_date)";
	if($db_type==0) $year_field_by="and YEAR(a.insert_date)";
	else if($db_type==2) $year_field_by=" and to_char(a.insert_date,'YYYY')";
	else $year_field_by="";
	if($db_type==0) $month_field_by="and month(a.insert_date)";
	else if($db_type==2) $month_field_by=" and to_char(a.insert_date,'MM')";
	else $month_field_by="";
	if($db_type==0) $year_field=" YEAR(a.insert_date) as year";
	else if($db_type==2) $year_field="  to_char(a.insert_date,'YYYY') as year";
	else $year_field="";

	if($year_id!=0) $year_cond=" $year_field_by=$year_id"; else $year_cond="";
	if($month_id!=0) $month_cond=" $month_field_by=$month_id"; else $month_cond="";
	if($booking_type==1)
	{
	$sql= "select a.job_no,a.job_no_prefix_num, a.company_name, a.buyer_name, a.style_ref_no,b.booking_no,c.id as booking_id  from wo_po_details_master a,wo_booking_dtls b ,wo_booking_mst c where a.job_no=b.job_no and b.booking_no=c.booking_no and a.status_active=1 and a.is_deleted=0 and b.booking_type in(3) and a.company_name=$company_id and $search_field like '$search_string' $buyer_id_cond $year_cond $month_cond group by a.job_no,b.booking_no,a.job_no_prefix_num, a.company_name, a.buyer_name, a.style_ref_no,c.id  order by a.job_no desc";
	}
	else if($booking_type==2)
	{
		$sql= "(select a.company_id as company_name, a.buyer_id as buyer_name, b.style_des as style_ref_no,a.booking_no,a.id as booking_id  from wo_non_ord_samp_booking_mst a,wo_non_ord_samp_booking_dtls b where  a.booking_no=b.booking_no and a.status_active=1 and a.is_deleted=0 and a.booking_type=3 and a.company_id=$company_id and $search_field like '$search_string' $buyer_id_cond2 $year_cond $month_cond group by a.booking_no,a.company_id, a.buyer_id, b.style_des,a.id
		union all
		select a.company_id as company_name, a.buyer_id as buyer_name, null as style_ref_no,a.booking_no,a.id as booking_id  from wo_non_ord_knitdye_booking_mst a,wo_non_ord_knitdye_booking_dtl b where  a.id = b.mst_id  and a.status_active=1 and a.is_deleted=0  and a.company_id=$company_id and $search_field like '$search_string' $buyer_id_cond2 $year_cond $month_cond group by a.booking_no,a.company_id, a.buyer_id,a.id )
		order by booking_no desc ";
	}
	else
	{
		$sql= "SELECT a.company_id as company_name, a.buyer_id as buyer_name, null as style_ref_no, a.wo_no as booking_no, a.id as booking_id from wo_non_ord_aop_booking_mst a, wo_non_ord_aop_booking_dtls b where a.id=b.wo_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.company_id=$company_id and $wo_search_field like '$search_string' $buyer_id_cond2 group by a.company_id, a.buyer_id, a.wo_no, a.id order by a.wo_no desc";
	}
	//echo  $sql;
	$sqlResult=sql_select($sql);
	?>

     <form name="searchprocessfrm_1" id="searchprocessfrm_1" autocomplete="off">
                <table cellspacing="0" cellpadding="0" border="1" rules="all" width="650" class="rpt_table">
                    <thead>
                    <th width="30">SL</th>
                    <th width="130">Company</th>
                    <th width="110">Buyer</th>
                    <th width="110">Job No</th>
                     <th width="120">Style Ref.</th>
                    <th width="">Booking No</th>

                    </thead>
                </table>
                    <table cellspacing="0" cellpadding="0" border="1" rules="all" width="650" class="rpt_table" id="tbl_list_search">
                    <?
					$i=1;
                    foreach($sqlResult as $row )
					{
						if ($i % 2 == 0) $bgcolor = "#E9F3FF"; else $bgcolor = "#FFFFFF";

						$data=$i.'_'.$row[csf('booking_id')].'_'.$row[csf('booking_no')].'_'.$booking_type;
						//echo $data;
					?>
                    	<tr id="tr_<?php echo $i; ?>" bgcolor="<? echo $bgcolor; ?>" style="text-decoration:none; cursor:pointer"  onClick="js_set_value2('<? echo $data;?>')">
                          <td width="30" align="center"><?php echo $i; ?>
                          <td width="130"><p><? echo $company_arr[$row[csf('company_name')]]; ?></p></td>
                          <td width="110"><p><? echo $buyer_arr[$row[csf('buyer_name')]]; ?></p></td>
                          <td width="110"><p><? echo $row[csf('job_no')]; ?></p></td>
                           <td width="120"><p><? echo $row[csf('style_ref_no')]; ?></p></td>
                          <td width=""><p><? echo $row[csf('booking_no')]; ?></p></td>
                       </tr>
                       <?
					   $i++;
					}
					   ?>
                    </table>
                     <table width="600" cellspacing="0" cellpadding="0" style="border:none" align="center">
                    <tr>
                        <td align="center" height="30" valign="bottom">
                            <div style="width:100%">
                                <div style="width:50%; float:left" align="left">
                                    <input type="checkbox" name="check_all" id="check_all" onClick="check_all_data()"/>
                                    Check / Uncheck All
                                </div>
                                <div style="width:50%; float:left" align="left">
                                    <input type="button" name="close" onClick="parent.emailwindow.hide();" class="formbutton" value="Close" style="width:100px"/>
                                </div>
                            </div>
                        </td>
                    </tr>
                </table>
                    </form>

    <?

   exit();
}

if($action=="report_generate")
{
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process ));
	$company_name= str_replace("'","",$cbo_company_name);
	$txt_job_no= str_replace("'","",$txt_job_no);
	$buyer_name= str_replace("'","",$cbo_buyer_name);
	$presentation= str_replace("'","",$cbo_presentation);
	$order_type= str_replace("'","",$cbo_order_type);
	$cbo_supplier_id= str_replace("'","",$cbo_supplier_name);
	$cbo_service_source= str_replace("'","",$cbo_service_source);
	$txt_ref_no= str_replace("'","",$txt_ref_no);
	$txt_file_no= str_replace("'","",$txt_file_no);
	$txt_wo_no= str_replace("'","",$txt_wo_no);

	$buyer_array=return_library_array( "select id, short_name from lib_buyer", "id", "short_name");
	$company_array=return_library_array( "select id, company_name from lib_company", "id", "company_name");
	$supllier_arr=return_library_array( "select id, supplier_name from lib_supplier",'id','supplier_name');

	$color_arr=return_library_array( "select id, color_name from lib_color",'id','color_name');
	$user_arr=return_library_array( "select id,user_name from user_passwd", "id", "user_name"  );

	if($txt_file_no!='') $file_cond=" and b.file_no=$txt_file_no";else  $file_cond="";
	if($txt_ref_no!='') $ref_cond=" and b.grouping='$txt_ref_no'";else  $ref_cond="";
	if($txt_job_no != "") $job_no_cond=" and b.job_no like '%$txt_job_no%'"; else $job_no_cond="";
	if($txt_job_no != "") $job_no_cond2=" and a.job_no like '%$txt_job_no%'"; else $job_no_cond2="";
	$wo_no=array_unique(explode(",",$txt_wo_no));
	//var_dump($wo_no);
	$all_wo_no_cond='';

	foreach($wo_no as $wno)
	{
		if($all_wo_no_cond=='') $all_wo_no_cond="'".$wno."'"; else $all_wo_no_cond.=","."'".$wno."'"; //echo $all_po_id;

	}
	//echo $all_wo_no_cond;

		if($txt_wo_no != "") $wo_no_cond=" and b.booking_no in($all_wo_no_cond)"; else $wo_no_cond="";
	//echo $wo_no_cond;
	if($cbo_supplier_id!=0)
	{
		$supplier_comp_cond="and a.dyeing_company in($cbo_supplier_id)";
	}
	else
	{
		$supplier_comp_cond="";
	}

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


	if($buyer_name != 0) $buyer_name_cond=" and b.buyer_id=$buyer_name"; else $buyer_name_cond="";
	if($presentation != 1){
		if($presentation == 2){
			$process_cond = " and a.entry_form=91";
		}else{
			$process_cond = " and a.entry_form=92";
		}
	}

	if($order_type != 1){
		if($order_type == 2){
			$order_cond = " and b.booking_without_order=0";
		}else{
			$order_cond = " and b.booking_without_order=1";
		}
	}


	$con = connect();
	$r_id1=execute_query("DELETE FROM GBL_TEMP_ENGINE WHERE USER_ID = ".$user_id." and ENTRY_FORM = 155");
	if($r_id1)
	{
		oci_commit($con);
		disconnect($con);
	}

	if($file_cond !="" || $ref_cond !="" || $job_no_cond2 !="" )
	{
		$all_po_id_arr=array();
		$job_sql="select a.buyer_name, a.job_no,a.insert_date, a.style_ref_no, b.id, b.grouping as ref_no, b.po_number,b.file_no from wo_po_details_master a, wo_po_break_down b where a.job_no=b.job_no_mst and b.status_active=1 and b.is_deleted=0  $file_cond $ref_cond  $job_no_cond2 ";
	   //echo $job_sql;
	   $job_sql_result=sql_select($job_sql);
	   foreach($job_sql_result as $row)
	   {
		   array_push($all_po_id_arr,$row[csf('id')]);
	   }

	   $po_cond_for_in="";
		if(!empty($all_po_id_arr))
		{
			$po_cond_for_in = "".where_con_using_array($all_po_id_arr,0,'b.order_id')."";
		}

	}


	/* $all_po=array_unique(explode(",",$all_po_id));
	$po_arr_cond=array_chunk($all_po,1000, true);
	$po_cond_for_in="";
	$poIds=chop($all_po_id,',');
	$po_ids=count(array_unique(explode(",",$all_po_id)));
	if($db_type==2 && $po_ids>1000)
	{
		$po_cond_for_in=" and (";
		$po_cond_for_in2=" and (";
		$poIdsArr=array_chunk(explode(",",$poIds),999);
		foreach($poIdsArr as $ids)
		{
			$ids=implode(",",$ids);
			//$poIds_cond.=" po_break_down_id in($ids) or ";
			$po_cond_for_in.=" b.order_id in($ids) or";
		}
		$po_cond_for_in=chop($po_cond_for_in,'or ');
		$po_cond_for_in.=")";
	}
	else
	{
		$po_cond_for_in=" and b.order_id in($poIds)";
	}
 	*/
	// if($txt_ref_no!='')
	// {
	// 	//$po_cond_for_in=" and b.order_id in($poIds)";
	// 	$po_cond_for_in=$po_cond_for_ins;
	// }
	// else if($txt_file_no!='')
	// {
	// 	//$po_cond_for_in=" and b.order_id in($poIds)";
	// 	$po_cond_for_in=$po_cond_for_ins;
	// }
	// else $po_cond_for_in="";

	$composition_arr=array(); $constructtion_arr=array();$gsm_arr=array();
 	$sql_deter="select a.id, a.construction, b.copmposition_id, b.percent, a.gsm_weight from lib_yarn_count_determina_mst a, lib_yarn_count_determina_dtls b where a.id=b.mst_id";
	$data_array=sql_select($sql_deter);
	foreach( $data_array as $row )
	{
		$composition_arr[$row[csf('id')]]=$composition_arr[$row[csf('id')]]." ".$composition[$row[csf('copmposition_id')]]." ".$row[csf('percent')]."%";
		$constructtion_arr[$row[csf('id')]]=$row[csf('construction')];
		$gsm_arr[$row[csf('id')]]=$row[csf('gsm_weight')];
	}

	$sql= "SELECT a.id, b.id as dtls_id, a.company_id, a.dyeing_company, a.receive_date, a.recv_number, a.challan_no, a.batch_no, a.insert_date, a.inserted_by, a.entry_form,b.booking_no, b.buyer_id, b.process_id, b.color_id, b.batch_issue_qty, b.grey_used, b.batch_id, b.fin_gsm, b.gsm, b.order_id, b.job_no, b.prod_id, b.febric_description_id, b.booking_without_order, a.dyeing_source, b.outbound_batchname, b.width as dia, b.roll_wgt, b.roll_id,b.amount, b.remarks
	from inv_receive_mas_batchroll a, pro_grey_batch_dtls b
	where a.id=b.mst_id and a.entry_form in (62,63,65,91,92) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.company_id=$company_name $date_cond $job_no_cond $buyer_name_cond $process_cond $order_cond $supplier_comp_cond $po_cond_for_in $wo_no_cond";
	//echo $sql;//die;
	$result_sql=sql_select( $sql );
	$isRollIssue = 0;
	$idArr = array();
	$all_poId_arr = array();
	$all_batchId_arr = array();
	foreach($result_sql as $row)
	{
		if($row[csf('entry_form')] == 63)
		{
			$isRollIssue = 1;
			$idArr['id'][$row[csf('id')]] = $row[csf('id')];
			$idArr['dtls_id'][$row[csf('dtls_id')]] = $row[csf('dtls_id')];
			if($row[csf('roll_id')] > 0)
			{
				$idArr['roll_id'][$row[csf('roll_id')]] = $row[csf('roll_id')];
			}
		}

		//for buyer
		$buyerIdArr[$row[csf('buyer_id')]] = $row[csf('buyer_id')];
		if($chkPoIdArr[$row[csf('order_id')]] =='')
		{
			$chkPoIdArr[$row[csf('order_id')]]=$row[csf('order_id')];
			$all_poId_arr[$row[csf('order_id')]] = $row[csf('order_id')];
		}

		if($chkBatchIdArr[$row[csf('batch_id')]] =='')
		{
			$chkBatchIdArr[$row[csf('batch_id')]]=$row[csf('batch_id')];
			$all_batchId_arr[$row[csf('batch_id')]] = $row[csf('batch_id')];
		}

	}

	$all_poId_arr = array_filter($all_poId_arr);
	if(!empty($all_poId_arr))
	{
		fnc_tempengine("GBL_TEMP_ENGINE", $user_id, 155, 1,$all_poId_arr, $empty_arr); //Order id
		//die;
		$job_array=array();
		$jobSql="SELECT a.buyer_name, a.job_no,a.insert_date, a.style_ref_no, b.id, b.grouping as ref_no, b.po_number,b.file_no from wo_po_details_master a, wo_po_break_down b, gbl_temp_engine c where a.job_no=b.job_no_mst and b.status_active=1 and b.is_deleted=0 and b.id=c.ref_val and c.user_id=$user_id and c.ref_from=1 and c.entry_form=155";
		//echo $job_sql;die;
		$jobSql_result=sql_select($jobSql);
		foreach($jobSql_result as $row)
		{
			$job_array[$row[csf('id')]]['job']=$row[csf('job_no')];
			$job_array[$row[csf('id')]]['style_ref_no']=$row[csf('style_ref_no')];
			$job_array[$row[csf('id')]]['ref_no']=$row[csf('ref_no')];
			$job_array[$row[csf('id')]]['file_no']=$row[csf('file_no')];
			$job_array[$row[csf('id')]]['buyer_id']=$row[csf('buyer_name')];
			$job_array[$row[csf('id')]]['po']=$row[csf('po_number')];
			$job_array[$row[csf('id')]]['year']=date("Y", strtotime($row[csf('insert_date')]));
		}
		unset($jobSql_result);

		$bookingSql="SELECT a.booking_no, b.id from wo_booking_dtls a, wo_po_break_down b, gbl_temp_engine c where a.po_break_down_id=b.id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.booking_type=1 and a.is_short in(1,2) and b.id=c.ref_val and c.user_id=$user_id and c.ref_from=1 and c.entry_form=155 group by b.id, a.booking_no";
		//echo $bookingSql;die;
		$bookingSql_result=sql_select($bookingSql);
		$booking_array = array();
		foreach($bookingSql_result as $row)
		{
			$booking_array[$row[csf('id')]]['booking_no']=$row[csf('booking_no')];
		}
		unset($bookingSql_result);
	}

	$all_batchId_arr = array_filter($all_batchId_arr);
	if(!empty($all_batchId_arr))
	{
		fnc_tempengine("GBL_TEMP_ENGINE", $user_id, 155, 2,$all_batchId_arr, $empty_arr); //Order id
		//die;

		$batchSql="SELECT a.id, a.batch_no from pro_batch_create_mst a, gbl_temp_engine b where a.status_active=1 and a.is_deleted=0 and a.id=b.ref_val and b.user_id=$user_id and b.ref_from=2 and b.entry_form=155 ";
		//echo $batchSql;die;
		$batchSql_result=sql_select($batchSql);
		$batch_name_array = array();
		foreach($batchSql_result as $row)
		{
			$batch_name_array[$row[csf('id')]]['batch_no']=$row[csf('batch_no')];
		}
		unset($batchSql_result);
	}

	//for roll level
	if($isRollIssue = 1)
	{
		if(!empty($idArr))
		{
			$sqlBatchBarcode="SELECT r.barcode_no FROM pro_roll_details r WHERE r.entry_form = 63 AND r.roll_no>0 AND r.status_active = 1 AND r.is_deleted = 0".where_con_using_array($idArr['id'],0,'r.mst_id')." ".where_con_using_array($idArr['dtls_id'],0,'r.dtls_id')." ".where_con_using_array($idArr['roll_id'],0,'r.roll_id')."";
			$sqlRoll = "SELECT b.id, b.febric_description_id, b.gsm, b.width, b.color_id, c.mst_id, c.barcode_no, c.entry_form FROM pro_grey_prod_entry_dtls b INNER JOIN pro_roll_details c ON b.id = c.dtls_id WHERE c.entry_form IN(2,22,63) AND c.roll_no>0 AND c.status_active = 1 AND c.is_deleted = 0 AND c.barcode_no IN(".$sqlBatchBarcode.") ORDER BY c.barcode_no, c.entry_form";
			//echo $sqlRoll;
			$sqlRollRslt = sql_select($sqlRoll);
			$dataRoll = array();
			$dataRoll2 = array();
			foreach($sqlRollRslt as $row)
			{
				if($row[csf('entry_form')] == 2 || $row[csf('entry_form')] == 22)
				{
					$dataRoll2[$row[csf('barcode_no')]]['febric_description_id'] = $row[csf('febric_description_id')];
					$dataRoll2[$row[csf('barcode_no')]]['gsm'] = $row[csf('gsm')];
					$dataRoll2[$row[csf('barcode_no')]]['dia'] = $row[csf('width')];
					$dataRoll2[$row[csf('barcode_no')]]['color_id'] = $row[csf('color_id')];
				}
				else
				{
					$dataRoll[$row[csf('mst_id')]]['febric_description_id'] = $dataRoll2[$row[csf('barcode_no')]]['febric_description_id'];
					$dataRoll[$row[csf('mst_id')]]['gsm'] = $dataRoll2[$row[csf('barcode_no')]]['gsm'];
					$dataRoll[$row[csf('mst_id')]]['dia'] = $dataRoll2[$row[csf('barcode_no')]]['dia'];
					$dataRoll[$row[csf('mst_id')]]['color_id'] = $dataRoll2[$row[csf('barcode_no')]]['color_id'];
				}
			}
		}
	}
	//echo "<pre>";
	//print_r($dataRoll);

	$con = connect();
	execute_query("DELETE FROM GBL_TEMP_ENGINE WHERE USER_ID = ".$user_id." and ENTRY_FORM=155");
	oci_commit($con);
	disconnect($con);

	ob_start();
	?>

    <fieldset style="width:2590px;"> <!-- 1960 -->
	 	<table width="2510" cellspacing="0" cellpadding="0" border="0" rules="all" >
	            <tr class="form_caption">
	                <td colspan="26" align="center" style="border:none;font-size:16px; font-weight:bold"> <? echo $report_title; ?></td>
	            </tr>
	            <tr class="form_caption">
	                <td colspan="26" align="center" style="border:none;font-size:16px; font-weight:bold"><? echo $company_array[$company_name]; ?></td>
	            </tr>
	            <tr class="form_caption">
	                <td colspan="26" align="center">
	                <strong>
					<?
					if(str_replace("'","",$txt_date_from)!="" && str_replace("'","",$txt_date_to)!="")
					{
					echo "From ".$start_date." To ".$end_date;
					}
					?>
	                </strong>
	                </td>
	            </tr>
	    </table>
	    <table cellspacing="0" cellpadding="0" border="1" rules="all" width="2590" class="rpt_table" >
	        <thead>
	            <th width="40">SL</th>
	            <th width="110">Trans. Date</th>
	            <th width="130">Trans. Ref.</th>

	            <th width="80">Challan No</th>
	            <th width="90">Service WO</th>
	            <th width="80">Year</th>
	            <th width="100">Job No</th>
	            <th width="100">Booking No</th>
	            <th width="100">Ref No</th>
	            <th width="100">File No</th>

	            <th width="100">Style No</th>
	            <th width="100">Order No</th>
	            <th width="90">Buyer</th>
	            <th width="150">Party Name</th>
	            <th width="125">Construction</th>
	            <th width="125">Composition</th>
	            <th width="50">GSM</th>
	            <th width="50">DIA</th>
	            <th width="80">Batch No</th>
	            <th width="80">Process Name</th>
	            <th width="80">Color</th>
	            <th width="80">Service Issue Qty</th>
	            <th width="80">Service Receive Grey</th>
	            <th width="80">Service Receive Finish</th>
	            <th width="80">Amount</th>
	            <th width="80">Insert By</th>
	            <th width="100">Insert Date and Time</th>
	            <th>Remarks</th>
	        </thead>
	    </table>
	    <div style="width:2610px; overflow-y:scroll; max-height:450px;" id="scroll_body">
		<table cellspacing="0" cellpadding="0" border="1" rules="all" width="2590" class="rpt_table" id="tbl_list_search">
	        <?


	        $i=1;
	        foreach($result_sql as $row)
	        {
	         	$order_id='';
				$order_no=explode(",",$row[csf('order_id')]);
				foreach($order_no as $val)
				{
					if($val>0) $order_id.=$val.",";
				}
				$order_id=chop($order_id,',');

	         	$job_no=$job_array[$order_id]['job'];
				$style_ref_no=$job_array[$order_id]['style_ref_no'];

				$ref_no=$job_array[$order_id]['ref_no'];
				$file_no=$job_array[$order_id]['file_no'];

				$buyer_name=$job_array[$order_id]['buyer_id'];
				$po_number=$job_array[$order_id]['po'];
				$year=$job_array[$order_id]['year'];
				$booking_no=$booking_array[$order_id]['booking_no'];

				//for weight level
				if($row[csf('entry_form')]==91 || $row[csf('entry_form')]==92)
				{
					// entry form 91= issue
					// entry form 92= receive
					if($row[csf('entry_form')]==91) $issue_qty=$row[csf('batch_issue_qty')]; else $issue_qty="";
					if($row[csf('entry_form')]==92) $receive_qty=$row[csf('batch_issue_qty')]; else $receive_qty="";
					if($row[csf('entry_form')]==92) $receive_amount=$row[csf('amount')]; else $receive_amount="";
					if($row[csf('entry_form')]==92) $recv_grey_qty=$row[csf('grey_used')]; else $recv_grey_qty="";
					$row[csf('gsm')] = ($row[csf('entry_form')]==91) ? $row[csf('gsm')] : $row[csf('fin_gsm')];

					/*if($row[csf('entry_form')]==92){
						$batch_name=$row[csf('outbound_batchname')];
					}elseif ($row[csf('entry_form')]==91) {
						$batch_name=$batch_name_array[$row[csf("batch_id")]];
					}*/
				}
				//for roll level
				else if($row[csf('entry_form')] == 63 || $row[csf('entry_form')] == 65)
				{
					// entry form 63 = Grey Roll Issue to Sub Contact
					// entry form 65 = AOP Roll Receive
					//for issue
					if($row[csf('entry_form')] == 63)
					{
						$issue_qty = $row[csf('roll_wgt')];
						$row[csf("febric_description_id")] = $dataRoll[$row[csf('id')]]['febric_description_id'];
						$row[csf("gsm")] = $dataRoll[$row[csf('id')]]['gsm'];
						$row[csf("dia")] = $dataRoll[$row[csf('id')]]['dia'];
						$row[csf("color_id")] = $dataRoll[$row[csf('id')]]['color_id'];
					}
					else
						$issue_qty="";

					//for receive
					if($row[csf('entry_form')] == 65)
						$receive_qty = $row[csf('roll_wgt')];
					else
						$receive_qty="";

					//if($row[csf('entry_form')]==92) $recv_grey_qty=$row[csf('grey_used')]; else $recv_grey_qty="";
				}

				//for color
				$color='';
				$color_id=explode(",",$row[csf('color_id')]);
				foreach($color_id as $val)
				{
					if($val>0)
						$color.=$color_arr[$val].",";
				}
				$color=chop($color,',');

				//for batch
				$batch_name = "";
				$batch_name.=$row[csf('outbound_batchname')];
				if($row[csf('outbound_batchname')]=='')
				{
				$batch_name.=$batch_name_array[$row[csf("batch_id")]];
				}

		        if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
		        ?>
		        <tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
		            <td width="40" align="center"><? echo $i; ?></td>

		            <td width="110" align="center"><p><? echo $row[csf('receive_date')]; ?></p></td>
		            <td width="130" align="center"><p><? echo $row[csf('recv_number')]; ?></p></td>

		            <td width="80" align="center"><p><? echo $row[csf('challan_no')]; ?></p></td>
		            <td width="90" align="center"><p><? echo $row[csf('booking_no')]; ?></p></td>
		            <td width="80" align="center"><p><? echo $year; ?></p></td>
		            <td width="100" align="center"><p><? echo $job_no; ?></p></td>
		            <td width="100" align="center"><p><? echo $booking_no; ?></p></td>
		            <td width="100" align="center"><p><? echo $ref_no; ?></p></td>
		            <td width="100" align="center"><p><? echo $file_no; ?></p></td>

		            <td width="100" align="center"><p><? echo $style_ref_no; ?></p></td>
		            <td width="100" align="center"><p><? echo $po_number; ?></p></td>
		             <td width="90" align="center"><p><? echo $buyer_array[$row[csf('buyer_id')]]; ?></p></td>
		            <td width="150" align="center"><p><? echo ($row[csf('dyeing_source')]==1)?$company_array[$row[csf('dyeing_company')]]:$supllier_arr[$row[csf('dyeing_company')]]; ?></p></td>

		            <td width="125" align="center"><p><? echo $constructtion_arr[$row[csf("febric_description_id")]]; ?></p></td>
		            <td width="125" align="center"><p><? echo $composition_arr[$row[csf('febric_description_id')]]; ?></p></td>

		            <td width="50" align="center"><p><? echo $row[csf('gsm')]; ?></p></td>
		            <td width="50" align="center"><p><? echo $row[csf('dia')]; ?></p></td>
		            <td width="80" align="center"><p><? echo $batch_name; ?></p></td>
		            <td width="80" align="center"><p><? echo $conversion_cost_head_array[$row[csf('process_id')]]; ?></p></td>
		            <td width="80" align="center"><p><? echo $color; ?></p></td>
		            <td width="80" align="right"><p><? echo number_format($issue_qty,2,'.',''); ?></p></td>
		            <td width="80" align="right"><p><? echo number_format($recv_grey_qty,2,'.',''); ?></p></td>
		            <td width="80" align="right"><p><? echo number_format($receive_qty,2,'.',''); ?></p></td>
		            <td width="80" align="right"><p><? echo number_format($receive_amount,2,'.',''); ?></p></td>
		            <td width="80" align="center"><p><? echo $user_arr[$row[csf('inserted_by')]]; ?></p></td>
		            <td align="center" width="100"><? echo $row[csf('insert_date')]; ?></td>
		            <td align="center"><p><? echo $row[csf('remarks')]; ?></p></td>
		        </tr>
				<?
                $total_issue += $issue_qty;//outbound_batchname
                $total_rcv += $receive_qty;
                $total_rec_grey += $recv_grey_qty;
                $i++;
		   }
		  ?>
	     </table>
	    </div>
	    <table cellspacing="0" cellpadding="0" border="1" rules="all" width="2590" class="rpt_table">
	       	<tfoot>
		       	<tr>
		            <th width="40"></th>
		            <th width="110"></th>
		            <th width="130"></th>
		            <th width="80"></th>
		            <th width="90"></th>
		            <th width="80"></th>
		            <th width="100"></th>
		            <th width="100"></th>
		            <th width="100"></th>
		            <th width="100"></th>
		            <th width="100"></th>
		            <th width="100"></th>
		            <th width="90"></th>
		            <th width="150"></th>
		            <th width="125"></th>
		            <th width="125"></th>
		            <th width="50"></th>
		            <th width="50"></th>
		            <th width="80"></th>
		            <th width="80"></th>
		            <th width="80">Total</th>
		            <th width="80" id="issue_qty"><? echo number_format($total_issue,2,'.',''); ?></th>
		            <th width="80" id="rcv_grey_qty"><? echo number_format($total_rec_grey,2,'.',''); ?></th>
		            <th width="80" id="rcv_finish_qty"><? echo number_format($total_rcv,2,'.',''); ?></th>
		            <th width="80"></th>
		            <th width="80"></th>
		            <th width="100"></th>
		            <th></th>
	            </tr>
	        </tfoot>
	    </table>
	</fieldset>

	<?
	foreach (glob("$user_id*.xls") as $filename) {
			if (@filemtime($filename) < (time() - $seconds_old))
				@unlink($filename);
	}
	//---------end------------//
	$name = time();
	$filename = $user_id . "_" . $name . ".xls";
	$create_new_doc = fopen($filename, 'w');
	$is_created = fwrite($create_new_doc, ob_get_contents());
	$filename = $user_id . "_" . $name . ".xls";
	echo "$total_data####$filename####$garph_caption####$garph_data";

	disconnect($con);
	exit();
}

if($action=="report_generate2")
{
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process ));
	$company_name= str_replace("'","",$cbo_company_name);
	$txt_job_no= str_replace("'","",$txt_job_no);
	$buyer_name= str_replace("'","",$cbo_buyer_name);
	$presentation= str_replace("'","",$cbo_presentation);
	$order_type= str_replace("'","",$cbo_order_type);
	$cbo_supplier_id= str_replace("'","",$cbo_supplier_name);
	$cbo_service_source= str_replace("'","",$cbo_service_source);
	$txt_ref_no= str_replace("'","",$txt_ref_no);
	$txt_file_no= str_replace("'","",$txt_file_no);
	$txt_wo_no= str_replace("'","",$txt_wo_no);

	$buyer_array=return_library_array( "select id, short_name from lib_buyer", "id", "short_name");
	$company_array=return_library_array( "select id, company_name from lib_company", "id", "company_name");
	$supllier_arr=return_library_array( "select id, supplier_name from lib_supplier",'id','supplier_name');

	$color_arr=return_library_array( "select id, color_name from lib_color",'id','color_name');
	$user_arr=return_library_array( "select id,user_name from user_passwd", "id", "user_name"  );

	if($txt_file_no!='') $file_cond=" and b.file_no=$txt_file_no";else  $file_cond="";
	if($txt_ref_no!='') $ref_cond=" and b.grouping='$txt_ref_no'";else  $ref_cond="";
	if($txt_job_no != "") $job_no_cond=" and b.job_no like '%$txt_job_no%'"; else $job_no_cond="";
	if($txt_job_no != "") $job_no_cond2=" and a.job_no like '%$txt_job_no%'"; else $job_no_cond2="";
	$wo_no=array_unique(explode(",",$txt_wo_no));
	//var_dump($wo_no);
	$all_wo_no_cond='';

	foreach($wo_no as $wno)
	{
		if($all_wo_no_cond=='') $all_wo_no_cond="'".$wno."'"; else $all_wo_no_cond.=","."'".$wno."'"; //echo $all_po_id;

	}
	//echo $all_wo_no_cond;

		if($txt_wo_no != "") $wo_no_cond=" and b.booking_no in($all_wo_no_cond)"; else $wo_no_cond="";
	//echo $wo_no_cond;
	if($cbo_supplier_id!=0)
	{
		$supplier_comp_cond="and a.dyeing_company in($cbo_supplier_id)";
	}
	else
	{
		$supplier_comp_cond="";
	}

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


	if($buyer_name != 0) $buyer_name_cond=" and b.buyer_id=$buyer_name"; else $buyer_name_cond="";
	if($presentation != 1){
		if($presentation == 2){
			$process_cond = " and a.entry_form=91";
		}else{
			$process_cond = " and a.entry_form=92";
		}
	}

	if($order_type != 1){
		if($order_type == 2){
			$order_cond = " and b.booking_without_order=0";
		}else{
			$order_cond = " and b.booking_without_order=1";
		}
	}


	$con = connect();
	$r_id1=execute_query("DELETE FROM GBL_TEMP_ENGINE WHERE USER_ID = ".$user_id." and ENTRY_FORM = 155");
	if($r_id1)
	{
		oci_commit($con);
		disconnect($con);
	}

	if($file_cond !="" || $ref_cond !="" || $job_no_cond2 !="" )
	{
		$all_po_id_arr=array();
		$job_sql="select a.buyer_name, a.job_no,a.insert_date, a.style_ref_no, b.id, b.grouping as ref_no, b.po_number,b.file_no from wo_po_details_master a, wo_po_break_down b where a.job_no=b.job_no_mst and b.status_active=1 and b.is_deleted=0  $file_cond $ref_cond  $job_no_cond2 ";
	   //echo $job_sql;
	   $job_sql_result=sql_select($job_sql);
	   foreach($job_sql_result as $row)
	   {
		   array_push($all_po_id_arr,$row[csf('id')]);
	   }

	   $po_cond_for_in="";
		if(!empty($all_po_id_arr))
		{
			$po_cond_for_in = "".where_con_using_array($all_po_id_arr,0,'b.order_id')."";
		}

	}


	/* $all_po=array_unique(explode(",",$all_po_id));
	$po_arr_cond=array_chunk($all_po,1000, true);
	$po_cond_for_in="";
	$poIds=chop($all_po_id,',');
	$po_ids=count(array_unique(explode(",",$all_po_id)));
	if($db_type==2 && $po_ids>1000)
	{
		$po_cond_for_in=" and (";
		$po_cond_for_in2=" and (";
		$poIdsArr=array_chunk(explode(",",$poIds),999);
		foreach($poIdsArr as $ids)
		{
			$ids=implode(",",$ids);
			//$poIds_cond.=" po_break_down_id in($ids) or ";
			$po_cond_for_in.=" b.order_id in($ids) or";
		}
		$po_cond_for_in=chop($po_cond_for_in,'or ');
		$po_cond_for_in.=")";
	}
	else
	{
		$po_cond_for_in=" and b.order_id in($poIds)";
	}
 	*/
	// if($txt_ref_no!='')
	// {
	// 	//$po_cond_for_in=" and b.order_id in($poIds)";
	// 	$po_cond_for_in=$po_cond_for_ins;
	// }
	// else if($txt_file_no!='')
	// {
	// 	//$po_cond_for_in=" and b.order_id in($poIds)";
	// 	$po_cond_for_in=$po_cond_for_ins;
	// }
	// else $po_cond_for_in="";

	$composition_arr=array(); $constructtion_arr=array();$gsm_arr=array();
 	$sql_deter="select a.id, a.construction, b.copmposition_id, b.percent, a.gsm_weight from lib_yarn_count_determina_mst a, lib_yarn_count_determina_dtls b where a.id=b.mst_id";
	$data_array=sql_select($sql_deter);
	foreach( $data_array as $row )
	{
		$composition_arr[$row[csf('id')]]=$composition_arr[$row[csf('id')]]." ".$composition[$row[csf('copmposition_id')]]." ".$row[csf('percent')]."%";
		$constructtion_arr[$row[csf('id')]]=$row[csf('construction')];
		$gsm_arr[$row[csf('id')]]=$row[csf('gsm_weight')];
	}

	$sql= "SELECT a.id, b.id as dtls_id, a.company_id, a.dyeing_company, a.receive_date, a.recv_number, a.challan_no, a.batch_no, a.insert_date, a.inserted_by, a.entry_form,b.booking_no, b.buyer_id, b.process_id, b.color_id, b.batch_issue_qty, b.grey_used, b.batch_id, b.fin_gsm, b.gsm, b.order_id, b.job_no, b.prod_id, b.febric_description_id, b.booking_without_order, a.dyeing_source, b.outbound_batchname, b.width as dia, b.roll_wgt, b.roll_id,b.amount, b.remarks
	from inv_receive_mas_batchroll a, pro_grey_batch_dtls b
	where a.id=b.mst_id and a.entry_form in (91,92) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.company_id=$company_name $date_cond $job_no_cond $buyer_name_cond $process_cond $order_cond $supplier_comp_cond $po_cond_for_in $wo_no_cond";
	//echo $sql;//die;
	$result_sql=sql_select( $sql );
	$isRollIssue = 0;
	$idArr = array();
	$all_poId_arr = array();
	$all_batchId_arr = array();
	foreach($result_sql as $row)
	{
		if($row[csf('entry_form')] == 63)
		{
			$isRollIssue = 1;
			$idArr['id'][$row[csf('id')]] = $row[csf('id')];
			$idArr['dtls_id'][$row[csf('dtls_id')]] = $row[csf('dtls_id')];
			if($row[csf('roll_id')] > 0)
			{
				$idArr['roll_id'][$row[csf('roll_id')]] = $row[csf('roll_id')];
			}
		}

		//for buyer
		$buyerIdArr[$row[csf('buyer_id')]] = $row[csf('buyer_id')];
		if($chkPoIdArr[$row[csf('order_id')]] =='')
		{
			$chkPoIdArr[$row[csf('order_id')]]=$row[csf('order_id')];
			$all_poId_arr[$row[csf('order_id')]] = $row[csf('order_id')];
		}

		if($chkBatchIdArr[$row[csf('batch_id')]] =='')
		{
			$chkBatchIdArr[$row[csf('batch_id')]]=$row[csf('batch_id')];
			$all_batchId_arr[$row[csf('batch_id')]] = $row[csf('batch_id')];
		}

	}

	$all_poId_arr = array_filter($all_poId_arr);
	if(!empty($all_poId_arr))
	{
		fnc_tempengine("GBL_TEMP_ENGINE", $user_id, 155, 1,$all_poId_arr, $empty_arr); //Order id
		//die;
		$job_array=array();
		$jobSql="SELECT a.buyer_name, a.job_no,a.insert_date, a.style_ref_no, b.id, b.grouping as ref_no, b.po_number,b.file_no from wo_po_details_master a, wo_po_break_down b, gbl_temp_engine c where a.job_no=b.job_no_mst and b.status_active=1 and b.is_deleted=0 and b.id=c.ref_val and c.user_id=$user_id and c.ref_from=1 and c.entry_form=155";
		//echo $job_sql;die;
		$jobSql_result=sql_select($jobSql);
		foreach($jobSql_result as $row)
		{
			$job_array[$row[csf('id')]]['job']=$row[csf('job_no')];
			$job_array[$row[csf('id')]]['style_ref_no']=$row[csf('style_ref_no')];
			$job_array[$row[csf('id')]]['ref_no']=$row[csf('ref_no')];
			$job_array[$row[csf('id')]]['file_no']=$row[csf('file_no')];
			$job_array[$row[csf('id')]]['buyer_id']=$row[csf('buyer_name')];
			$job_array[$row[csf('id')]]['po']=$row[csf('po_number')];
			$job_array[$row[csf('id')]]['year']=date("Y", strtotime($row[csf('insert_date')]));
		}
		unset($jobSql_result);

		$bookingSql="SELECT a.booking_no, b.id from wo_booking_dtls a, wo_po_break_down b, gbl_temp_engine c where a.po_break_down_id=b.id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.booking_type=1 and a.is_short in(1,2) and b.id=c.ref_val and c.user_id=$user_id and c.ref_from=1 and c.entry_form=155 group by b.id, a.booking_no";
		//echo $bookingSql;die;
		$bookingSql_result=sql_select($bookingSql);
		$booking_array = array();
		foreach($bookingSql_result as $row)
		{
			$booking_array[$row[csf('id')]]['booking_no']=$row[csf('booking_no')];
		}
		unset($bookingSql_result);
	}

	$all_batchId_arr = array_filter($all_batchId_arr);
	if(!empty($all_batchId_arr))
	{
		fnc_tempengine("GBL_TEMP_ENGINE", $user_id, 155, 2,$all_batchId_arr, $empty_arr); //Order id
		//die;

		$batchSql="SELECT a.id, a.batch_no from pro_batch_create_mst a, gbl_temp_engine b where a.status_active=1 and a.is_deleted=0 and a.id=b.ref_val and b.user_id=$user_id and b.ref_from=2 and b.entry_form=155 ";
		//echo $batchSql;die;
		$batchSql_result=sql_select($batchSql);
		$batch_name_array = array();
		foreach($batchSql_result as $row)
		{
			$batch_name_array[$row[csf('id')]]['batch_no']=$row[csf('batch_no')];
		}
		unset($batchSql_result);
	}

	//for roll level
	if($isRollIssue = 1)
	{
		if(!empty($idArr))
		{
			$sqlBatchBarcode="SELECT r.barcode_no FROM pro_roll_details r WHERE r.entry_form = 63 AND r.roll_no>0 AND r.status_active = 1 AND r.is_deleted = 0".where_con_using_array($idArr['id'],0,'r.mst_id')." ".where_con_using_array($idArr['dtls_id'],0,'r.dtls_id')." ".where_con_using_array($idArr['roll_id'],0,'r.roll_id')."";
			$sqlRoll = "SELECT b.id, b.febric_description_id, b.gsm, b.width, b.color_id, c.mst_id, c.barcode_no, c.entry_form FROM pro_grey_prod_entry_dtls b INNER JOIN pro_roll_details c ON b.id = c.dtls_id WHERE c.entry_form IN(2,22,63) AND c.roll_no>0 AND c.status_active = 1 AND c.is_deleted = 0 AND c.barcode_no IN(".$sqlBatchBarcode.") ORDER BY c.barcode_no, c.entry_form";
			//echo $sqlRoll;
			$sqlRollRslt = sql_select($sqlRoll);
			$dataRoll = array();
			$dataRoll2 = array();
			foreach($sqlRollRslt as $row)
			{
				if($row[csf('entry_form')] == 2 || $row[csf('entry_form')] == 22)
				{
					$dataRoll2[$row[csf('barcode_no')]]['febric_description_id'] = $row[csf('febric_description_id')];
					$dataRoll2[$row[csf('barcode_no')]]['gsm'] = $row[csf('gsm')];
					$dataRoll2[$row[csf('barcode_no')]]['dia'] = $row[csf('width')];
					$dataRoll2[$row[csf('barcode_no')]]['color_id'] = $row[csf('color_id')];
				}
				else
				{
					$dataRoll[$row[csf('mst_id')]]['febric_description_id'] = $dataRoll2[$row[csf('barcode_no')]]['febric_description_id'];
					$dataRoll[$row[csf('mst_id')]]['gsm'] = $dataRoll2[$row[csf('barcode_no')]]['gsm'];
					$dataRoll[$row[csf('mst_id')]]['dia'] = $dataRoll2[$row[csf('barcode_no')]]['dia'];
					$dataRoll[$row[csf('mst_id')]]['color_id'] = $dataRoll2[$row[csf('barcode_no')]]['color_id'];
				}
			}
		}
	}
	//echo "<pre>";
	//print_r($dataRoll);

	$con = connect();
	execute_query("DELETE FROM GBL_TEMP_ENGINE WHERE USER_ID = ".$user_id." and ENTRY_FORM=155");
	oci_commit($con);
	disconnect($con);

	ob_start();
	?>

    <fieldset style="width:2590px;"> <!-- 1960 -->
	 	<table width="2510" cellspacing="0" cellpadding="0" border="0" rules="all" >
	            <tr class="form_caption">
	                <td colspan="26" align="center" style="border:none;font-size:16px; font-weight:bold"> <? echo $report_title; ?></td>
	            </tr>
	            <tr class="form_caption">
	                <td colspan="26" align="center" style="border:none;font-size:16px; font-weight:bold"><? echo $company_array[$company_name]; ?></td>
	            </tr>
	            <tr class="form_caption">
	                <td colspan="26" align="center">
	                <strong>
					<?
					if(str_replace("'","",$txt_date_from)!="" && str_replace("'","",$txt_date_to)!="")
					{
					echo "From ".$start_date." To ".$end_date;
					}
					?>
	                </strong>
	                </td>
	            </tr>
	    </table>
	    <table cellspacing="0" cellpadding="0" border="1" rules="all" width="2590" class="rpt_table" >
	        <thead>
	            <th width="40">SL</th>
	            <th width="110">Trans. Date</th>
	            <th width="130">Trans. Ref.</th>

	            <th width="80">Challan No</th>
	            <th width="90">Service WO</th>
	            <th width="80">Year</th>
	            <th width="100">Job No</th>
	            <th width="100">Booking No</th>
	            <th width="100">Ref No</th>
	            <th width="100">File No</th>

	            <th width="100">Style No</th>
	            <th width="100">Order No</th>
	            <th width="90">Buyer</th>
	            <th width="150">Party Name</th>
	            <th width="125">Construction</th>
	            <th width="125">Composition</th>
	            <th width="50">GSM</th>
	            <th width="50">DIA</th>
	            <th width="80">Batch No</th>
	            <th width="80">Process Name</th>
	            <th width="80">Color</th>
	            <th width="80">Service Issue Qty</th>
	            <th width="80">Service Receive Grey</th>
	            <th width="80">Service Receive Finish</th>
	            <th width="80">Amount</th>
	            <th width="80">Insert By</th>
	            <th width="100">Insert Date and Time</th>
	            <th>Remarks</th>
	        </thead>
	    </table>
	    <div style="width:2610px; overflow-y:scroll; max-height:450px;" id="scroll_body">
		<table cellspacing="0" cellpadding="0" border="1" rules="all" width="2590" class="rpt_table" id="tbl_list_search">
	        <?


	        $i=1;
	        foreach($result_sql as $row)
	        {
	         	$order_id='';
				$order_no=explode(",",$row[csf('order_id')]);
				foreach($order_no as $val)
				{
					if($val>0) $order_id.=$val.",";
				}
				$order_id=chop($order_id,',');

	         	$job_no=$job_array[$order_id]['job'];
				$style_ref_no=$job_array[$order_id]['style_ref_no'];

				$ref_no=$job_array[$order_id]['ref_no'];
				$file_no=$job_array[$order_id]['file_no'];

				$buyer_name=$job_array[$order_id]['buyer_id'];
				$po_number=$job_array[$order_id]['po'];
				$year=$job_array[$order_id]['year'];
				$booking_no=$booking_array[$order_id]['booking_no'];

				//for weight level
				if($row[csf('entry_form')]==91 || $row[csf('entry_form')]==92)
				{
					// entry form 91= issue
					// entry form 92= receive
					if($row[csf('entry_form')]==91) $issue_qty=$row[csf('batch_issue_qty')]; else $issue_qty="";
					if($row[csf('entry_form')]==92) $receive_qty=$row[csf('batch_issue_qty')]; else $receive_qty="";
					if($row[csf('entry_form')]==92) $receive_amount=$row[csf('amount')]; else $receive_amount="";
					if($row[csf('entry_form')]==92) $recv_grey_qty=$row[csf('grey_used')]; else $recv_grey_qty="";
					$row[csf('gsm')] = ($row[csf('entry_form')]==91) ? $row[csf('gsm')] : $row[csf('fin_gsm')];

					/*if($row[csf('entry_form')]==92){
						$batch_name=$row[csf('outbound_batchname')];
					}elseif ($row[csf('entry_form')]==91) {
						$batch_name=$batch_name_array[$row[csf("batch_id")]];
					}*/
				}
				//for roll level
				else if($row[csf('entry_form')] == 63 || $row[csf('entry_form')] == 65)
				{
					// entry form 63 = Grey Roll Issue to Sub Contact
					// entry form 65 = AOP Roll Receive
					//for issue
					if($row[csf('entry_form')] == 63)
					{
						$issue_qty = $row[csf('roll_wgt')];
						$row[csf("febric_description_id")] = $dataRoll[$row[csf('id')]]['febric_description_id'];
						$row[csf("gsm")] = $dataRoll[$row[csf('id')]]['gsm'];
						$row[csf("dia")] = $dataRoll[$row[csf('id')]]['dia'];
						$row[csf("color_id")] = $dataRoll[$row[csf('id')]]['color_id'];
					}
					else
						$issue_qty="";

					//for receive
					if($row[csf('entry_form')] == 65)
						$receive_qty = $row[csf('roll_wgt')];
					else
						$receive_qty="";

					//if($row[csf('entry_form')]==92) $recv_grey_qty=$row[csf('grey_used')]; else $recv_grey_qty="";
				}

				//for color
				$color='';
				$color_id=explode(",",$row[csf('color_id')]);
				foreach($color_id as $val)
				{
					if($val>0)
						$color.=$color_arr[$val].",";
				}
				$color=chop($color,',');

				//for batch
				$batch_name = "";
				$batch_name.=$row[csf('outbound_batchname')];
				if($row[csf('outbound_batchname')]=='')
				{
				$batch_name.=$batch_name_array[$row[csf("batch_id")]];
				}

		        if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
		        ?>
		        <tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
		            <td width="40" align="center"><? echo $i; ?></td>

		            <td width="110" align="center"><p><? echo $row[csf('receive_date')]; ?></p></td>
		            <td width="130" align="center"><p><? echo $row[csf('recv_number')]; ?></p></td>

		            <td width="80" align="center"><p><? echo $row[csf('challan_no')]; ?></p></td>
		            <td width="90" align="center"><p><? echo $row[csf('booking_no')]; ?></p></td>
		            <td width="80" align="center"><p><? echo $year; ?></p></td>
		            <td width="100" align="center"><p><? echo $job_no; ?></p></td>
		            <td width="100" align="center"><p><? echo $booking_no; ?></p></td>
		            <td width="100" align="center"><p><? echo $ref_no; ?></p></td>
		            <td width="100" align="center"><p><? echo $file_no; ?></p></td>

		            <td width="100" align="center"><p><? echo $style_ref_no; ?></p></td>
		            <td width="100" align="center"><p><? echo $po_number; ?></p></td>
		             <td width="90" align="center"><p><? echo $buyer_array[$row[csf('buyer_id')]]; ?></p></td>
		            <td width="150" align="center"><p><? echo ($row[csf('dyeing_source')]==1)?$company_array[$row[csf('dyeing_company')]]:$supllier_arr[$row[csf('dyeing_company')]]; ?></p></td>

		            <td width="125" align="center"><p><? echo $constructtion_arr[$row[csf("febric_description_id")]]; ?></p></td>
		            <td width="125" align="center"><p><? echo $composition_arr[$row[csf('febric_description_id')]]; ?></p></td>

		            <td width="50" align="center"><p><? echo $row[csf('gsm')]; ?></p></td>
		            <td width="50" align="center"><p><? echo $row[csf('dia')]; ?></p></td>
		            <td width="80" align="center"><p><? echo $batch_name; ?></p></td>
		            <td width="80" align="center"><p><? echo $conversion_cost_head_array[$row[csf('process_id')]]; ?></p></td>
		            <td width="80" align="center"><p><? echo $color; ?></p></td>
		            <td width="80" align="right"><p><? echo number_format($issue_qty,2,'.',''); ?></p></td>
		            <td width="80" align="right"><p><? echo number_format($recv_grey_qty,2,'.',''); ?></p></td>
		            <td width="80" align="right"><p><? echo number_format($receive_qty,2,'.',''); ?></p></td>
		            <td width="80" align="right"><p><? echo number_format($receive_amount,2,'.',''); ?></p></td>
		            <td width="80" align="center"><p><? echo $user_arr[$row[csf('inserted_by')]]; ?></p></td>
		            <td align="center" width="100"><? echo $row[csf('insert_date')]; ?></td>
		            <td align="center"><p><? echo $row[csf('remarks')]; ?></p></td>
		        </tr>
				<?
                $total_issue += $issue_qty;//outbound_batchname
                $total_rcv += $receive_qty;
                $total_rec_grey += $recv_grey_qty;
                $i++;
		   }
		  ?>
	     </table>
	    </div>
	    <table cellspacing="0" cellpadding="0" border="1" rules="all" width="2590" class="rpt_table">
	       	<tfoot>
		       	<tr>
		            <th width="40"></th>
		            <th width="110"></th>
		            <th width="130"></th>
		            <th width="80"></th>
		            <th width="90"></th>
		            <th width="80"></th>
		            <th width="100"></th>
		            <th width="100"></th>
		            <th width="100"></th>
		            <th width="100"></th>
		            <th width="100"></th>
		            <th width="100"></th>
		            <th width="90"></th>
		            <th width="150"></th>
		            <th width="125"></th>
		            <th width="125"></th>
		            <th width="50"></th>
		            <th width="50"></th>
		            <th width="80"></th>
		            <th width="80"></th>
		            <th width="80">Total</th>
		            <th width="80" id="issue_qty"><? echo number_format($total_issue,2,'.',''); ?></th>
		            <th width="80" id="rcv_grey_qty"><? echo number_format($total_rec_grey,2,'.',''); ?></th>
		            <th width="80" id="rcv_finish_qty"><? echo number_format($total_rcv,2,'.',''); ?></th>
		            <th width="80"></th>
		            <th width="80"></th>
		            <th width="100"></th>
		            <th></th>
	            </tr>
	        </tfoot>
	    </table>
	</fieldset>

	<?
	foreach (glob("$user_id*.xls") as $filename) {
			if (@filemtime($filename) < (time() - $seconds_old))
				@unlink($filename);
	}
	//---------end------------//
	$name = time();
	$filename = $user_id . "_" . $name . ".xls";
	$create_new_doc = fopen($filename, 'w');
	$is_created = fwrite($create_new_doc, ob_get_contents());
	$filename = $user_id . "_" . $name . ".xls";
	echo "$total_data####$filename####$garph_caption####$garph_data";

	disconnect($con);
	exit();
}
?>