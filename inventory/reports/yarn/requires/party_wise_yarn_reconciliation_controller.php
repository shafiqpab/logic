<?
header('Content-type:text/html; charset=utf-8');
session_start();
include('../../../../includes/common.php');

$user_id=$_SESSION['logic_erp']['user_id'];
if( $_SESSION['logic_erp']['user_id'] == "" ) { header("location:login.php"); die; }
$permission=$_SESSION['page_permission'];

$data=$_REQUEST['data'];
$action=$_REQUEST['action'];

if($action=="print_button_variable_setting")
{
	//$print_report_format=0;
	//echo "select format_id from lib_report_template where template_name ='".$data."' and module_id=6 and report_id=34 and is_deleted=0 and status_active=1";
	$print_report_format=return_field_value("format_id","lib_report_template","template_name ='".$data."' and module_id=6 and report_id=34 and is_deleted=0 and status_active=1");
	if($print_report_format=='') $print_report_format=0;else $print_report_format=$print_report_format;
	echo "document.getElementById('hidden_report_ids').value = '".$print_report_format."';\n";
	echo "print_report_button_setting('".$print_report_format."');\n";
	exit();	
}
/*if($action=="load_drop_down_knitting_com")
{
	$data = explode("_",$data);
	$company_id=$data[1];
	
	if($data[0]==1)
	{
		echo create_drop_down( "txt_knitting_com_id", 140, "select comp.id, comp.company_name from lib_company comp where comp.status_active=1 and comp.is_deleted=0 $company_cond order by comp.company_name","id,company_name",0, "--Select Party--", "$company_id", "","" );
	}
	else if($data[0]==3)
	{	
		//select DISTINCT(c.id),c.supplier_name from lib_supplier_tag_company a,lib_supplier_party_type b, lib_supplier c where c.id=b.supplier_id and a.supplier_id = b.supplier_id and a.tag_company='$companyID' and b.party_type=2
		echo create_drop_down( "txt_knitting_com_id", 140, "select a.id,a.supplier_name from lib_supplier a, lib_supplier_party_type b,lib_supplier_tag_company c where a.id=b.supplier_id and c.supplier_id=b.supplier_id and c.tag_company=$company_id and b.party_type in(1,9,20) and a.status_active=1 group by a.id,a.supplier_name order by a.supplier_name","id,supplier_name",0, "-- Select --", 1, "" );
	}
	else
	{
		echo create_drop_down( "txt_knitting_com_id", 140, $blank_array,"",1, "--Select Party--", 1, "" );
	}
	
	exit();
}*/

/*if ($action=="eval_multi_select")
{
 	echo "set_multiselect('txt_knitting_com_id','0','0','','0');\n";
	$data = explode("_",$data);
	
	if($data[0]==1)
	{
		echo "set_multiselect('txt_knitting_com_id','0','1','".$data[1]."','0');\n";
	}
	exit();
}*/

if($action=="job_no_popup")
{
	echo load_html_head_contents("Job Info", "../../../../", 1, 1,'','','');
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
			
			$('#hide_job_id').val( id );
			$('#hide_job_no').val( name );
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
                    <th><input type="reset" name="button" class="formbutton" value="Reset"  style="width:100px;"></th> 
                    <input type="hidden" name="hide_job_no" id="hide_job_no" value="" />
                    <input type="hidden" name="hide_job_id" id="hide_job_id" value="" />
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
                        	<input type="button" name="button" class="formbutton" value="Show" onClick="show_list_view ('<? echo $companyID; ?>'+'**'+document.getElementById('cbo_buyer_name').value+'**'+document.getElementById('cbo_search_by').value+'**'+document.getElementById('txt_search_common').value+'**'+'<? echo $cbo_year_id; ?>'+'**'+'<? echo $cbo_month_id; ?>', 'create_job_no_search_list_view', 'search_div', 'party_wise_yarn_reconciliation_controller', 'setFilterGrid(\'tbl_list_search\',-1)');" style="width:100px;" />
                    </td>
                    </tr>
            	</tbody>
           	</table>
            <div style="margin-top:15px" id="search_div"></div>
		</fieldset>
	</form>
</div>
</body>           
<script src="../../../../includes/functions_bottom.js" type="text/javascript"></script>
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

	if($search_by==2) $search_field="style_ref_no"; else $search_field="job_no_prefix_num";
	//$year="year(insert_date)";
	if($db_type==0) $year_field_by="and YEAR(insert_date)"; 
	else if($db_type==2) $year_field_by=" and to_char(insert_date,'YYYY')";
	else $year_field_by="";
	if($db_type==0) $month_field_by="and month(insert_date)"; 
	else if($db_type==2) $month_field_by=" and to_char(insert_date,'MM')";
	else $month_field_by="";
	if($db_type==0) $year_field=" YEAR(insert_date) as year"; 
	else if($db_type==2) $year_field="  to_char(insert_date,'YYYY') as year";
	else $year_field="";

	if($year_id!=0) $year_cond=" $year_field_by=$year_id"; else $year_cond="";
	if($month_id!=0) $month_cond=" $month_field_by=$month_id"; else $month_cond="";
	
	
	$arr=array (0=>$company_arr,1=>$buyer_arr);
		
	$sql= "select id, job_no, job_no_prefix_num, company_name, buyer_name, style_ref_no, $year_field  from wo_po_details_master where status_active=1 and is_deleted=0 and company_name=$company_id and $search_field like '$search_string' $buyer_id_cond $year_cond $month_cond order by job_no";
		
	echo create_list_view("tbl_list_search", "Company,Buyer Name,Job No,Year,Style Ref. No", "120,130,80,60","600","240",0, $sql , "js_set_value", "id,job_no_prefix_num", "", 1, "company_name,buyer_name,0,0,0", $arr , "company_name,buyer_name,job_no_prefix_num,year,style_ref_no", "",'','0,0,0,0,0','',1) ;
	
   exit(); 
} 

if($action=="party_popup")
{
	echo load_html_head_contents("Party Info", "../../../../", 1, 1,'','','');
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
			
			$('#hide_party_id').val( id );
			$('#hide_party_name').val( name );
		}
    </script>
        <input type="hidden" name="hide_party_name" id="hide_party_name" value="" />
        <input type="hidden" name="hide_party_id" id="hide_party_id" value="" />
	<?

	if ($cbo_knitting_source==3)
	{
		$sql="select a.id, a.supplier_name as party_name from lib_supplier a, lib_supplier_party_type b,lib_supplier_tag_company c where a.id=b.supplier_id and c.supplier_id=b.supplier_id and c.tag_company=$companyID and b.party_type in(1,9,20) and a.status_active=1  group by a.id, a.supplier_name order by a.supplier_name";
	}
	elseif($cbo_knitting_source==1)
	{
		$sql="select id, company_name as party_name from lib_company where status_active=1 and is_deleted=0 order by company_name";
	}

	echo create_list_view("tbl_list_search", "Party Name", "380","380","270",0, $sql , "js_set_value", "id,party_name", "", 1, "0", $arr , "party_name", "",'setFilterGrid("tbl_list_search",-1);','0','',1) ;
	
   exit(); 
} 

if($action=="report_generate")
{ 
	//echo $type;die;
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process ));

	$company_arr=return_library_array( "select id, company_name from lib_company", "id", "company_name");
	$supplier_arr=return_library_array( "select id, supplier_name from lib_supplier", "id", "supplier_name");
	$buyer_arr=return_library_array( "select id, short_name from  lib_buyer", "id", "short_name");
	$brand_arr=return_library_array( "select id, brand_name from lib_brand",'id','brand_name');
	$store_arr=return_library_array( "select id, store_name from lib_store_location", "id", "store_name");
	$count_arr=return_library_array( "select id, yarn_count from lib_yarn_count",'id','yarn_count');
	$color_arr=return_library_array( "select id, color_name from lib_color", "id", "color_name");
		
	if($db_type==0)
	{
		$from_date=change_date_format(str_replace("'","",$txt_date_from),'yyyy-mm-dd');
		$to_date=change_date_format(str_replace("'","",$txt_date_to),'yyyy-mm-dd');
	}
	elseif($db_type==2)
	{
		$from_date=change_date_format(str_replace("'","",$txt_date_from),'','',1);
		$to_date=change_date_format(str_replace("'","",$txt_date_to),'','',1);
	}
	$cbo_value_with=str_replace("'","",$cbo_value_with);
	$type=str_replace("'","",$type);
	$txt_knitting_com_id=str_replace("'","",$txt_knitting_com_id);
	if ($txt_knitting_com_id=="") $knitting_company_cond_1=""; else $knitting_company_cond_1=" and a.knit_dye_company in ($txt_knitting_com_id)";
	if ($txt_knitting_com_id=="") $knitting_company_cond_2=""; else $knitting_company_cond_2=" and a.knitting_company in ($txt_knitting_com_id)";
	if ($txt_knitting_com_id=="") $knitting_company_cond_3=""; else $knitting_company_cond_3=" and a.supplier_id in ($txt_knitting_com_id)";
	if (str_replace("'","",$cbo_knitting_source)==0) $knitting_source_cond=""; else $knitting_source_cond=" and a.knit_dye_source=$cbo_knitting_source";
	if (str_replace("'","",$cbo_knitting_source)==0) $knitting_source_rec_cond=""; else $knitting_source_rec_cond=" and a.knitting_source=$cbo_knitting_source";
	if ($knitting_company=='') $knitting_company_cond=""; else  $knitting_company_cond="  and a.knit_dye_company in ($knitting_company)";
	if (str_replace("'","",$txt_internal_ref)=="") $internal_ref_cond=""; else $internal_ref_cond=" and b.grouping=$txt_internal_ref";
	if (str_replace("'","",$cbo_issue_purpose)==0) $issue_purpose_cond=""; else $issue_purpose_cond=" and a.issue_purpose=$cbo_issue_purpose";

	ob_start();
	
	if($type==1)
	{
		?>
        <fieldset style="width:1070px">
            <table width="1060" cellpadding="0" cellspacing="0" id="caption"><!-- style="visibility:hidden; border:none"-->
                <tr>
                   <td align="center" width="100%" colspan="11" style="font-size:16px"><strong><? echo $company_arr[str_replace("'","",$cbo_company_name)]; ?></strong></td>
                </tr> 
                <tr>  
                   <td align="center" width="100%" colspan="11" style="font-size:16px"><strong><? echo $report_title; ?> (Summary)</strong></td>
                </tr>  
                <tr> 
                   <td align="center" width="100%" colspan="11" style="font-size:16px"><strong><? echo "From ".change_date_format(str_replace("'","",$txt_date_from))." To ".change_date_format(str_replace("'","",$txt_date_to)); ?></strong></td>
                </tr>
            </table>
            <br />
            <table width="1060" cellpadding="0" cellspacing="0" border="1" rules="all" class="rpt_table">
                <thead>
                    <th width="30">SL</th>
                    <th width="150">Party Name</th>
                    <th width="60">UOM</th>
                    <th width="100" title="Iss.-Rec.">Opening Balance</th>
                    <th width="100">Yarn Issued</th>
                    <th width="100">Fabric Received</th>
                    <th width="100">Reject Fabric Received</th>
                    <th width="100">DY/TW/ WX/RCon Rec.</th>
                    <th width="100">Yarn Returned</th>
                    <th width="100">Reject Yarn Returned</th>
                    <th>Balance</th>
                </thead>
            </table>
            <div style="width:1060px; overflow-y: scroll; max-height:380px;" id="scroll_body">
                <table width="1040" cellpadding="0" cellspacing="0" border="1" rules="all" class="rpt_table"> 
				<?
				$party_data=array(); $party_opening_arr=array();
				if (str_replace("'","",$cbo_knitting_source)==3)
				{
                    if ($knitting_company=='') $party_cond=""; else  $party_cond="  and a.supplier_id in ($knitting_company)";
                    if ($txt_knitting_com_id=='') $party_cond_2=""; else  $party_cond_2="  and a.supplier_id in ($txt_knitting_com_id)";
					$sql_yrec="select a.supplier_id, a.receive_date, sum(b.cons_quantity) as cons_quantity
						
						from inv_receive_master a, inv_transaction b where a.id=b.mst_id and a.company_id=$cbo_company_name and a.receive_purpose in (2,12,15,38) and a.item_category =1 and b.item_category =1 and a.entry_form=1 and b.transaction_type=1 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $party_cond $party_cond_2 group by a.supplier_id, a.receive_date"; //$knitting_source_rec_cond
					//echo $sql_yrec; die;
					$sql_yrec_res=sql_select($sql_yrec);
					
					foreach($sql_yrec_res as $rowyRec)
					{
						$trns_date=''; $date_frm=''; $date_to='';
						$trns_date=date('Y-m-d',strtotime($rowyRec[csf('receive_date')]));
						$date_frm=date('Y-m-d',strtotime($from_date));
						$date_to=date('Y-m-d',strtotime($to_date));
						
						if($trns_date<$date_frm)
						{
							$party_opening_arr[$rowyRec[csf('supplier_id')]]['yrOpening']+=$rowyRec[csf('cons_quantity')];
						}
						if($trns_date>=$date_frm && $trns_date<=$date_to)
						{
							//echo $rowyRec[csf('cons_quantity')].'d';
							$party_data[$rowyRec[csf('supplier_id')]]['yarn_rec']+=$rowyRec[csf('cons_quantity')];
						}
					}
					unset($sql_yrec_res);
				}
				if ($knitting_company=='') $party_cond=""; else  $party_cond="  and a.knitting_company in ($knitting_company)";
				if ($txt_knitting_com_id=='') $party_cond_1=""; else  $party_cond_1="  and a.knitting_company in ($txt_knitting_com_id)";
				if (str_replace("'","",$cbo_knitting_source)==0) $knit_source_cond=""; else $knit_source_cond=" and a.knitting_source=$cbo_knitting_source";

				$sql_rec="select a.knitting_company, a.receive_date, a.entry_form,b.item_category,b.cons_quantity,b.return_qnty, b.cons_reject_qnty from inv_receive_master a, inv_transaction b where a.item_category in(1,13) and a.entry_form in(2,9,22) and a.company_id=$cbo_company_name and a.id=b.mst_id and b.item_category in(1,13) and b.transaction_type in(1,4) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $knit_source_cond $party_cond $party_cond_1 $issue_challan_cond order by a.knitting_company, a.receive_date ";
				//echo $sql_rec; die;
				$sql_rec_res=sql_select($sql_rec);
				
				foreach($sql_rec_res as $rowRec)
				{
					$trns_date=''; $date_frm=''; $date_to='';
					$trns_date=date('Y-m-d',strtotime($rowRec[csf('receive_date')]));
					$date_frm=date('Y-m-d',strtotime($from_date));
					$date_to=date('Y-m-d',strtotime($to_date));
					$item_category=$rowRec[csf('item_category')];
					if($trns_date<$date_frm)
					{
						$opening_balance_rec=0;
						$opening_balance_rec=$rowRec[csf('cons_quantity')]+$rowRec[csf('cons_reject_qnty')]+$rowRec[csf('return_qnty')];
						$party_opening_arr[$rowRec[csf('knitting_company')]]['recOpening']+=$opening_balance_rec;
					}
					//echo $trns_date.">=".$date_frm ."&&". $trns_date."<=".$date_to;
					if($trns_date>=$date_frm && $trns_date<=$date_to)
					{
						if($item_category==13)
						{
							$party_data[$rowRec[csf('knitting_company')]]['fRec']+=$rowRec[csf('cons_quantity')];
						}

						if($rowRec[csf('entry_form')]==9)
						{
							$party_data[$rowRec[csf('knitting_company')]]['ret_yarn']+=$rowRec[csf('cons_quantity')];
							$party_data[$rowRec[csf('knitting_company')]]['rej_yarn']+=$rowRec[csf('cons_reject_qnty')];
						}
						else
						{
							$party_data[$rowRec[csf('knitting_company')]]['rej_fab']+=$rowRec[csf('cons_reject_qnty')];
							//echo $rowRec[csf('cons_reject_qnty')];
						}

						$party_data[$rowRec[csf('knitting_company')]]['return']+=$rowRec[csf('return_qnty')];
					}
					
				}//print_r($party_data);
				unset($sql_rec_res);

				$issue_qty_arr=array();
				$sql_iss="select a.knit_dye_company, a.issue_date, sum(b.cons_quantity) as cons_quantity, sum(b.return_qnty) as return_qnty from inv_issue_master a, inv_transaction b where a.item_category=1 and a.entry_form=3 and a.company_id=$cbo_company_name and a.id=b.mst_id and b.item_category=1 and b.transaction_type=2 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0  $knitting_company_cond_1 $knitting_source_cond $knitting_company_cond group by a.knit_dye_company, a.issue_date";
				//echo $sql_iss; die;
				$sql_iss_res=sql_select($sql_iss);
				foreach($sql_iss_res as $rowIss)
				{
					$trns_date=''; $date_frm=''; $date_to='';
					$trns_date=date('Y-m-d',strtotime($rowIss[csf('issue_date')]));
					$date_frm=date('Y-m-d',strtotime($from_date));
					$date_to=date('Y-m-d',strtotime($to_date));

					if($trns_date<$date_frm)
					{
						$party_opening_arr[$rowIss[csf('knit_dye_company')]]['issOpening']+=$rowIss[csf('cons_quantity')];
					}
					if($trns_date>=$date_frm && $trns_date<=$date_to)
					{
						$party_data[$rowIss[csf('knit_dye_company')]]['issue_qnty']+=$rowIss[csf('cons_quantity')];
						$party_data[$rowIss[csf('knit_dye_company')]]['return_qnty']+=$rowIss[csf('return_qnty')];
					}

				}
				unset($sql_iss_res);


				$i=1;
					
                    foreach($party_data as $party_id=>$party_datas)
                    {
                        if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
							
						if(str_replace("'","",$cbo_knitting_source)==1)
							$knitting_party=$company_arr[$party_id];
						else if(str_replace("'","",$cbo_knitting_source)==3)
							$knitting_party=$supplier_arr[$party_id];
						else
							$knitting_party="&nbsp;";

						//echo $yarn_rec_arr[$row[csf('knit_dye_company')]]['yropening'].'-'.$rec_qty_arr[$row[csf('knit_dye_company')]]['opening'].'<br>';
						//echo $party_opening_arr[$party_id]['recOpening'].'<br>';

						$opening_balance=0; $yarn_issue=0; $yarn_returnable_qty=0; $dy_tx_wx_rcon=0;
						$opening_balance= $party_opening_arr[$party_id]['issOpening']-($party_opening_arr[$party_id]['recOpening']+$party_opening_arr[$party_id]['yrOpening']);

						$yarn_issue=$party_datas['issue_qnty'];
						$yarn_returnable_qty=$party_datas['return_qnty'];
						
						$dy_tx_wx_rcon=$party_datas['yarn_rec'];
						$grey_receive_qnty=$party_datas['fRec'];
						$reject_fabric_receive=$party_datas['rej_fab'];
						
						$yarn_return_qnty=$party_datas['ret_yarn'];
						$yarn_return_reject_qnty=$party_datas['rej_yarn'];
						$returnable_balance=$yarn_returnable_qty-$yarn_return_qnty;
						
						$balance=($opening_balance+$yarn_issue)-($dy_tx_wx_rcon+$grey_receive_qnty+$reject_fabric_receive+$yarn_return_qnty+$yarn_return_reject_qnty);

						if ($cbo_value_with == 0) 
						{
	 						if (number_format($balance, 2) > 0.00) 
	 						{
			                    ?>
			                        <tr bgcolor="<? echo $bgcolor;?>" onClick="change_color('tr<? echo $i;?>','<? echo $bgcolor;?>')" id="tr<? echo $i;?>">
			                            <td width="30"><? echo $i; ?></td>
			                            <td width="150"><p><? echo $knitting_party; ?>&nbsp;</p></td>
			                            <td width="60" align="center"><? echo 'KG';//$unit_of_measurement[$row[csf('cons_uom')]]; ?>&nbsp;</td>
			                            <td width="100" align="right"><? echo number_format($opening_balance,2); ?></td>
			                            <td width="100" align="right"><? echo number_format($yarn_issue,2); ?></td>
			                            <td width="100" align="right"><? echo number_format($grey_receive_qnty,2); ?></td>
			                            <td width="100" align="right"><? echo number_format($reject_fabric_receive,2); ?></td>
			                            <td width="100" align="right"><? echo number_format($dy_tx_wx_rcon,2); ?></td>   
			                            <td width="100" align="right"><? echo number_format($yarn_return_qnty,2); ?></td>
			                            <td width="100" align="right"><? echo number_format($yarn_return_reject_qnty,2); ?></td>
			                            <td align="right"><? echo number_format($balance,2); ?></td> 
			                        </tr>
			                    <?
			                    $i++;
			                }
			            }
			            else
			            {
			            	?>
	                        <tr bgcolor="<? echo $bgcolor;?>" onClick="change_color('tr<? echo $i;?>','<? echo $bgcolor;?>')" id="tr<? echo $i;?>">
	                            <td width="30"><? echo $i; ?></td>
	                            <td width="150"><p><? echo $knitting_party; ?>&nbsp;</p></td>
	                            <td width="60" align="center"><? echo 'KG';//$unit_of_measurement[$row[csf('cons_uom')]]; ?>&nbsp;</td>
	                            <td width="100" align="right"><? echo number_format($opening_balance,2); ?></td>
	                            <td width="100" align="right"><? echo number_format($yarn_issue,2); ?></td>
	                            <td width="100" align="right"><? echo number_format($grey_receive_qnty,2); ?></td>
	                            <td width="100" align="right"><? echo number_format($reject_fabric_receive,2); ?></td>
	                            <td width="100" align="right"><? echo number_format($dy_tx_wx_rcon,2); ?></td>
	                            <td width="100" align="right"><? echo number_format($yarn_return_qnty,2); ?></td>
	                            <td width="100" align="right"><? echo number_format($yarn_return_reject_qnty,2); ?></td>
	                            <td align="right"><? echo number_format($balance,2); ?></td>
	                        </tr>
			                <?
			                $i++;
			            }                       
						
						$tot_opening_bal+=$opening_balance;
						$tot_issue+=$yarn_issue;
						$tot_receive+=$grey_receive_qnty;
						$tot_rejFab_rec+=$reject_fabric_receive;
						$tot_dy_tx_wx_rcon+=$dy_tx_wx_rcon;
						$tot_yarn_return+=$yarn_return_qnty;
						$tot_yarn_retReject+=$yarn_return_reject_qnty;
						$tot_balance+=$balance;
						$tot_returnable+=$yarn_returnable_qty;
						$tot_process+=$process_loss;
						$tot_balance_after_process_loss += $balance_after_process_loss;
                    }
					//unset($result);
                    ?>
                </table>       
            </div>
            <table width="1060" cellpadding="0" cellspacing="0" border="1" rules="all" class="tbl_bottom">
                <tr>
                    <td width="30">&nbsp;</td>
                    <td width="150">&nbsp;</td>
                    <td width="60">Total</th>
                    <td width="100"><? echo number_format($tot_opening_bal,2); ?></td>
                    <td width="100"><? echo number_format($tot_issue,2); ?></td>
                    <td width="100"><? echo number_format($tot_receive,2); ?></td>
                    <td width="100"><? echo number_format($tot_rejFab_rec,2); ?></td>
                    <td width="100"><? echo number_format($tot_dy_tx_wx_rcon,2); ?></td>
                    <td width="100"><? echo number_format($tot_yarn_return,2); ?></td>
                    <td width="100"><? echo number_format($tot_yarn_retReject,2); ?></td>
                    <td><? echo number_format($tot_balance,2); ?></td>
                </tr>
            </table>
        </fieldset>      
		<?
	}
	else if($type==2) //Party Wise
	{
		if($internal_ref_cond!="")
		{
			$jobNo=sql_select("select b.id as po_breakdown_id  from wo_po_break_down b,wo_po_details_master a where b.job_no_mst=a.job_no $internal_ref_cond");
			$allJobNo="";
			foreach($jobNo as $row)
			{
				$allJobNo.=$row[csf('po_breakdown_id')].',';
			}
			$allJobNos= chop($allJobNo,',');
			$propotionate_tbl=',order_wise_pro_details c';
			$where_cond="  and   b.id=c.trans_id and c.po_breakdown_id in ($allJobNos)"; 
		}
		
		//echo "selectb.id as po_breakdown_id  from wo_po_break_down b,wo_po_details_master a where b.job_no_mst=a.job_no $internal_ref_cond";
		//$all_party=explode(",",$knitting_company);
		$po_arr=array();
		$datapoArray=sql_select("select a.buyer_name, a.style_ref_no,b.id, b.po_number, b.po_quantity,b.grouping from wo_po_break_down b,wo_po_details_master a where b.job_no_mst=a.job_no $internal_ref_cond");
		foreach($datapoArray as $row)
		{
			$po_arr[$row[csf('id')]]['name']=$row[csf('po_number')];
			$po_arr[$row[csf('id')]]['qnty']=$row[csf('po_quantity')];
			$po_arr[$row[csf('id')]]['style']=$row[csf('style_ref_no')];
			$po_arr[$row[csf('id')]]['buyer']=$row[csf('buyer_name')];
			$po_arr[$row[csf('id')]]['internal_ref']=$row[csf('grouping')];
		}	
		unset($datapoArray);
			
		if($db_type==0) $grpby_field="group by trans_id";
		if($db_type==2) $grpby_field="group by trans_id,entry_form,trans_type";
		else $grpby_field="";
		
		if (str_replace("'","",$txt_challan)=="") $challan_cond="";
		else $challan_cond=" and a.issue_number_prefix_num=$txt_challan";
	
		$order_nos_data_array=array();
		$prop_date_cond="";
		if($from_date!="" && $to_date!="") $prop_date_cond=" and b.transaction_date between '$from_date' and '$to_date'";
		$datapropArray=sql_select("select a.trans_id as TRANS_ID, a.po_breakdown_id as PO_BREAKDOWN_ID  from order_wise_pro_details a, inv_transaction b where a.trans_id=b.id and a.trans_id<>0 and a.entry_form in(2,3,9,22,58) and a.status_active=1 and a.is_deleted=0 and a.status_active=1 and a.is_deleted=0 and a.trans_type in(1,2,4) and b.transaction_type in(1,2,4) $prop_date_cond ");
							 
		foreach($datapropArray as $row)
		{
			$order_nos_data_array[$row[('TRANS_ID')]][]=$row[('PO_BREAKDOWN_ID')];
			//$order_nos_array[$row[csf('trans_id')]]['grey_recv']=$row[csf('grey_order_id')];
			//$order_nos_array[$row[csf('trans_id')]]['yarn_return']=$row[csf('yarn_return_order_id')];
		}	
		unset($datapropArray);
		//print_r($order_nos_data_array);die;
		
		?>
        <div>
            <table width="2340" cellpadding="0" cellspacing="0" id="caption"><!-- style="visibility:hidden; border:none"-->
                <tr>
                   <td align="center" width="100%" colspan="23" style="font-size:16px"><strong><? echo $company_arr[str_replace("'","",$cbo_company_name)]; ?></strong></td>
                </tr> 
                <tr>  
                   <td align="center" width="100%" colspan="23" style="font-size:16px"><strong><? echo $report_title; ?> (Party Wise)</strong></td>
                </tr>  
                <tr> 
                   <td align="center" width="100%" colspan="23" style="font-size:16px"><strong><? echo "From ".change_date_format(str_replace("'","",$txt_date_from))." To ".change_date_format(str_replace("'","",$txt_date_to)); ?></strong></td>
                </tr>
            </table>
            <table width="2340" cellpadding="0" cellspacing="0" border="1" rules="all" class="rpt_table">
                <thead>
                    <th width="40">SL</th>
                    <th width="80">Date</th>
                    <th width="125">Transaction Ref.</th>
                    <th width="115">Recv. Challan No</th>
                    <th width="115">Issue Challan No</th>
                    <th width="130">Booking/Reqsn. No</th>
                    <th width="100">Program No</th>
                    <th width="80">Buyer</th>
                    <th width="130">Style Ref.</th>
                    <th width="130">Order Numbers</th>
                    <th width="90">Internal Ref</th>
                    <th width="90">Order Qnty.</th>
                    <th width="80">Brand</th>
                    <th width="150">Item Description</th>
                    <th width="80">Lot</th>
                    <th width="60">UOM</th>
                    <th width="100">Yarn Issued</th>
                   <!-- <th width="100">Returnable Qty.</th>-->
                    <th width="100">Fabric Received</th>
                    <th width="100">Reject Fabric Received</th>
                    <th width="100">DY/TW/ WX/RCon Rec.</th>
                    <th width="100">Yarn Returned</th>
                    <th width="100">Reject Yarn Returned</th>
                    <th width="">Balance</th> 
                   <!-- <th>Returnable Balance</th>-->
                </thead>
            </table>
            <div style="width:2340px; overflow-y: scroll; max-height:380px;" id="scroll_body">
                <table width="2320" cellpadding="2" cellspacing="0" border="1" rules="all" class="rpt_table" id="table_body">
				<?
					$product_arr=array();
					$sql_prod="select id as id, product_name_details as product_name_details, lot as lot from product_details_master where item_category_id in (1,2,13) ";
					$sql_prod_res=sql_select($sql_prod);
					foreach($sql_prod_res as $rowp)
					{
						$product_arr[$rowp[csf('id')]]['name']=$rowp[csf('product_name_details')];
						$product_arr[$rowp[csf('id')]]['lot']=$rowp[csf('lot')];
					}
					unset($sql_prod_res);
					//echo "test";die;

					if (str_replace("'","",$cbo_knitting_source)==0) $knit_source_cond=""; else $knit_source_cond=" and a.knitting_source=$cbo_knitting_source";
					
					if ($knitting_company=='') 
					{
						$party_cond=""; 
					}
					else  
					{
						if(str_replace("'","",$cbo_knitting_source)==1)
						{
							$supplier_id=str_replace("'","",$cbo_company_name);
							$party_cond="  and a.supplier_id in ($supplier_id)";
						}else{
							$supplier_id=str_replace("'","",$txt_knitting_com_id);
							$party_cond="  and a.supplier_id in ($supplier_id)";
						}
						
					}

					$yarnRec_qty_arr=array();
					$all_data_arr=array();
					if (str_replace("'","",$cbo_knitting_source)==3)
					{
						$sql_yrec="select a.recv_number, a.recv_number_prefix_num, a.entry_form,a.buyer_id, a.booking_no, a.receive_date, a.item_category, a.challan_no, a.yarn_issue_challan_no,  a.knitting_source, a.supplier_id, a.receive_basis, a.entry_form, b.id as trans_id,b.transaction_type, b.prod_id, b.cons_uom,  b.brand_id, b.cons_quantity, b.return_qnty, b.cons_reject_qnty 
						from inv_receive_master a, inv_transaction b $propotionate_tbl where a.item_category=1 and a.entry_form=1 and a.company_id=$cbo_company_name and a.receive_purpose in (2,12,15,38) and a.id=b.mst_id and b.item_category=1 and b.transaction_type=1 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 
						 $party_cond $knitting_company_cond_3 $issue_challan_cond $where_cond 
						
						order by a.receive_date desc, a.supplier_id, a.yarn_issue_challan_no, b.transaction_type ";
						//echo $sql_yrec; die;
						$sql_yRec_res=sql_select($sql_yrec);
						foreach ($sql_yRec_res as $rowyRec )
						{
							$trns_date=''; $date_frm=''; $date_to='';
							$trns_date=date('Y-m-d',strtotime($rowyRec[csf('receive_date')]));
							$date_frm=date('Y-m-d',strtotime($from_date));
							$date_to=date('Y-m-d',strtotime($to_date));
							//echo $trns_date.'--'.$date_frm;// die;
							$opening_balRec=0;
							if($trns_date<$date_frm)
							{
								 $opening_balRec=$rowyRec[csf('cons_quantity')];//+$rowyRec[csf('return_qnty')]+$rowyRec[csf('cons_reject_qnty')];
								$yarnRec_qty_arr[$rowyRec[csf('supplier_id')]]['0']['0']['opening_bal_yRec']+=$opening_balRec;
							}
							//if($trns_date>=$date_frm && $trns_date<=$date_to)
							//echo $trns_date.'>='.'&&'.$trns_date.'<='.$date_to;
							if($trns_date>=$date_frm && $trns_date<=$date_to)
							{
								//echo $rowyRec[csf('cons_quantity')].'dddd';
								$yarnRec_qty_arr[$rowyRec[csf('supplier_id')]][$rowyRec[csf('trans_id')]][$rowyRec[csf('prod_id')]]['yRec']+=$rowyRec[csf('cons_quantity')];
								
								$all_data_arr[$rowyRec[csf('supplier_id')]]['yrec'].=$rowyRec[csf('recv_number')].'!!'.$rowyRec[csf('recv_number_prefix_num')].'!!'.$rowyRec[csf('buyer_id')].'!!'.$rowyRec[csf('booking_no')].'!!'.$rowyRec[csf('receive_date')].'!!'.$rowyRec[csf('challan_no')].'!!'.$rowyRec[csf('receive_basis')].'!!'.$rowyRec[csf('knitting_source')].'!!'.$rowyRec[csf('trans_id')].'!!'.$rowyRec[csf('prod_id')].'!!'.$rowyRec[csf('cons_uom')].'!!'.$rowyRec[csf('brand_id')].'!!'.$rowyRec[csf('yarn_issue_challan_no')].'!!'.$rowyRec[csf('item_category')].'!!'.$rowyRec[csf('entry_form')].'!!'.$rowyRec[csf('transaction_type')].'___';
							}
						}
						unset($sql_yRec_res);
					}
					
					//print_r($all_data_arr); 

					$rec_qty_arr=array();
					/*$sql_rec="select a.recv_number, a.recv_number_prefix_num,a.entry_form, a.buyer_id, a.booking_no, a.receive_date, a.item_category, a.challan_no, a.yarn_issue_challan_no,  a.knitting_source, a.knitting_company, a.receive_basis, a.entry_form, b.id as trans_id, b.prod_id, b.cons_uom, b.brand_id, b.cons_quantity, b.return_qnty, b.cons_reject_qnty, b.transaction_type 
					from inv_receive_master a, inv_transaction b $propotionate_tbl 
					where a.item_category in(1,13) and a.entry_form in(2,9,22,58) and a.company_id=$cbo_company_name and a.id=b.mst_id and b.item_category in(1,13) and b.transaction_type in(1,4) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0
					$knit_source_cond $knit_company_cond $issue_challan_cond $where_cond  
					order by a.knitting_company, a.yarn_issue_challan_no, b.transaction_type, a.receive_date ";
					$sql_rec="select a.recv_number as RECV_NUMBER, a.recv_number_prefix_num as RECV_NUMBER_PREFIX_NUM, a.entry_form as ENTRY_FORM, a.buyer_id as BUYER_ID, a.booking_no as BOOKING_NO, a.receive_date as RECEIVE_DATE, a.item_category as ITEM_CATEGORY, a.challan_no as CHALLAN_NO, a.yarn_issue_challan_no as YARN_ISSUE_CHALLAN_NO,  a.knitting_source as KNITTING_SOURCE, a.knitting_company as KNITTING_COMPANY, a.receive_basis as RECEIVE_BASIS, b.id as TRANS_ID, b.prod_id as PROD_ID, b.cons_uom as CONS_UOM, b.brand_id as BRAND_ID, b.cons_quantity as CONS_QUANTITY, b.return_qnty as RETURN_QNTY, b.cons_reject_qnty as CONS_REJECT_QNTY, b.transaction_type as TRANSACTION_TYPE 
					from inv_receive_master a, inv_transaction b $propotionate_tbl 
					where a.item_category in(1,13) and a.entry_form in(2,9,22,58) and a.company_id=$cbo_company_name and a.id=b.mst_id and b.item_category in(1,13) and b.transaction_type in(1,4) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0
					$knit_source_cond $knit_company_cond $issue_challan_cond $where_cond  
					order by a.knitting_company, a.yarn_issue_challan_no, b.transaction_type, a.receive_date";*/
					
					$sql_rec="
					select max(a.recv_number) as RECV_NUMBER, max(a.recv_number_prefix_num) as RECV_NUMBER_PREFIX_NUM, max(a.entry_form) as ENTRY_FORM, max(a.buyer_id) as BUYER_ID, max(a.booking_no) as BOOKING_NO, max(a.receive_date) as RECEIVE_DATE, max(a.item_category) as ITEM_CATEGORY, max(a.challan_no) as CHALLAN_NO, max(a.yarn_issue_challan_no) as YARN_ISSUE_CHALLAN_NO,  a.knitting_source as KNITTING_SOURCE, a.knitting_company as KNITTING_COMPANY, max(a.receive_basis) as RECEIVE_BASIS, max(b.id) as TRANS_ID, max(b.prod_id) as PROD_ID, max(b.cons_uom) as CONS_UOM, max(b.brand_id) as BRAND_ID,
					sum(case when a.entry_form=9 and b.transaction_type=4 then b.cons_quantity else 0 end) as YARN_ISSUE_RETURN,
					sum(case when b.transaction_type=1 then b.cons_quantity else 0 end) as RCV_QNTY,
					sum(b.return_qnty) as RETURN_QNTY, sum(b.cons_reject_qnty) as CONS_REJECT_QNTY, max(b.transaction_type) as TRANSACTION_TYPE,
					1 as TYPE
					from inv_receive_master a, inv_transaction b $propotionate_tbl 
					where a.item_category in(1,13) and a.entry_form in(2,9,22,58) and a.company_id=$cbo_company_name and a.id=b.mst_id and b.item_category in(1,13) and b.transaction_type in(1,4) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and b.transaction_date < '$from_date'
					$knit_source_cond $knit_company_cond $knitting_company_cond_2 $issue_challan_cond $where_cond
					group by a.knitting_source, a.knitting_company
					union all
					select a.recv_number as RECV_NUMBER, a.recv_number_prefix_num as RECV_NUMBER_PREFIX_NUM, a.entry_form as ENTRY_FORM, a.buyer_id as BUYER_ID, a.booking_no as BOOKING_NO, a.receive_date as RECEIVE_DATE, a.item_category as ITEM_CATEGORY, a.challan_no as CHALLAN_NO, a.yarn_issue_challan_no as YARN_ISSUE_CHALLAN_NO,  a.knitting_source as KNITTING_SOURCE, a.knitting_company as KNITTING_COMPANY, a.receive_basis as RECEIVE_BASIS, b.id as TRANS_ID, b.prod_id as PROD_ID, b.cons_uom as CONS_UOM, b.brand_id as BRAND_ID,
					(case when a.entry_form=9 and b.transaction_type=4 then b.cons_quantity else 0 end) as YARN_ISSUE_RETURN,
					(case when b.transaction_type=1 then b.cons_quantity else 0 end) as RCV_QNTY,
					b.return_qnty as RETURN_QNTY, b.cons_reject_qnty as CONS_REJECT_QNTY, b.transaction_type as TRANSACTION_TYPE, 2 as TYPE
					from inv_receive_master a, inv_transaction b $propotionate_tbl 
					where a.item_category in(1,13) and a.entry_form in(2,9,22,58) and a.company_id=$cbo_company_name and a.id=b.mst_id and b.item_category in(1,13) and b.transaction_type in(1,4) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and b.transaction_date between '$from_date' and '$to_date'
					$knit_source_cond $knit_company_cond $knitting_company_cond_2 $issue_challan_cond $where_cond  
					order by RECEIVE_DATE DESC,KNITTING_COMPANY, RECEIVE_BASIS, TRANSACTION_TYPE";
					//echo $sql_rec;die;
					$sql_rec_res=sql_select($sql_rec);
					//echo "<pre>";
					//print_r($sql_rec_res);die;
					$tot_opening_bal_rej_yarn=$tot_bal_opening_balfRec=$tot_opening_bal_ret_yarn=0;
					foreach ($sql_rec_res as $rowRec )
					{
						/*$trns_date=''; $date_frm=''; $date_to='';
						$trns_date=date('Y-m-d',strtotime($rowRec[('RECEIVE_DATE')]));
						$date_frm=date('Y-m-d',strtotime($from_date));
						$date_to=date('Y-m-d',strtotime($to_date));*/
						//echo $trns_date."=".$date_frm."=".$trns_date."=".$date_to.test;die;
						
						//if($trns_date<$date_frm)
						if($rowRec[('TYPE')]==1)
						{
							//echo $trns_date.'<'.$date_frm.', '; 
							$opening_balRec=0;
							//if($rowRec[('ENTRY_FORM')]==9) $return_qnty=$rowRec[('CONS_QUANTITY')];
							$opening_balRec=$rowRec[('RCV_QNTY')]-$rowRec[('YARN_ISSUE_RETURN')];//+$rowRec[csf('return_qnty')]+$rowRec[csf('cons_reject_qnty')]
							//echo $opening_balRec.', '; 
							//echo $rowRec[csf('return_qnty')].'d';
							$tot_opening_bal_rej_yarn+=$rowRec[('CONS_REJECT_QNTY')];
							$tot_bal_opening_balfRec+=$opening_balRec;	
							$tot_opening_bal_ret_yarn+=$return_qnty;	
							  
							$rec_qty_arr[$rowRec[('KNITTING_COMPANY')]]['0']['0']['opening_bal_fRec']+=$opening_balRec;
							//$yarnRec_qty_arr[$rowRec[csf('knitting_company')]]['0']['0']['opening_bal_yRec']+=$rowRec[csf('cons_quantity')];
							$rec_qty_arr[$rowRec[('KNITTING_COMPANY')]]['0']['0']['opening_bal_rej_yarn']+=$rowRec[('CONS_REJECT_QNTY')];
							$rec_qty_arr[$rowRec[('KNITTING_COMPANY')]]['0']['0']['opening_bal_ret_yarn']+=$return_qnty;
						}
						else
						{
							if($rowRec[('ENTRY_FORM')]==9)
							{
								$rec_qty_arr[$rowRec[('KNITTING_COMPANY')]][$rowRec[('TRANS_ID')]][$rowRec[('PROD_ID')]]['ret_po']=$order_nos_array[$rowRec[('TRANS_ID')]]['yarn_return'];
								//echo $rowyRec[csf('cons_quantity')].'sss';
								$rec_qty_arr[$rowRec[('KNITTING_COMPANY')]][$rowRec[('TRANS_ID')]][$rowRec[('PROD_ID')]]['rej_yarn']+=$rowRec[('CONS_REJECT_QNTY')];
								$rec_qty_arr[$rowRec[('KNITTING_COMPANY')]][$rowRec[('TRANS_ID')]][$rowRec[('PROD_ID')]]['ret_yarn']+=$rowRec[('YARN_ISSUE_RETURN')];
								$yarnRec_qty_arr[$rowRec[('KNITTING_COMPANY')]][$rowRec[('TRANS_ID')]][$rowRec[('PROD_ID')]]['yRec']+=$rowRec[('YARN_ISSUE_RETURN')];
							}
							else
							{

								$rec_qty_arr[$rowRec[('KNITTING_COMPANY')]][$rowRec[('TRANS_ID')]][$rowRec[('PROD_ID')]]['rej_fab']+=$rowRec[('CONS_REJECT_QNTY')];
								$rec_qty_arr[$rowRec[('KNITTING_COMPANY')]][$rowRec[('TRANS_ID')]][$rowRec[('PROD_ID')]]['fRec']+=$rowRec[('RCV_QNTY')];
							}
							
							$all_data_arr[$rowRec[('KNITTING_COMPANY')]]['rec'].=$rowRec[('RECV_NUMBER')].'**'.$rowRec[('RECV_NUMBER_PREFIX_NUM')].'**'.$rowRec[('BUYER_ID')].'**'.$rowRec[('BOOKING_NO')].'**'.$rowRec[('RECEIVE_DATE')].'**'.$rowRec[('CHALLAN_NO')].'**'.$rowRec[('RECEIVE_BASIS')].'**'.$rowRec[('KNITTING_SOURCE')].'**'.$rowRec[('TRANS_ID')].'**'.$rowRec[('PROD_ID')].'**'.$rowRec[('CONS_UOM')].'**'.$rowRec[('BRAND_ID')].'**'.$rowRec[('YARN_ISSUE_CHALLAN_NO')].'**'.$rowRec[('ITEM_CATEGORY')].'**'.$rowRec[('ENTRY_FORM')].'**'.$rowRec[('TRANSACTION_TYPE')].'___';
						}
					}
					unset($sql_rec_res);
					//echo "<pre>";  print_r($rec_qty_arr);die;
					if (str_replace("'","",$cbo_knitting_source)==0) $knit_source_cond_party=""; else $knit_source_cond_party=" and a.knit_dye_source=$cbo_knitting_source";
					$knitting_company=str_replace("'","",$txt_knitting_com_id);
					if ($knitting_company=='') $knit_company_cond_party=""; else $knit_company_cond_party=" and a.knit_dye_company in ($knitting_company)";
					$iss_qty_arr=array(); 
					/*$sql_iss="select a.issue_number, a.issue_number_prefix_num,a.entry_form, a.buyer_id, a.booking_no, a.issue_date, a.challan_no, a.issue_basis, a.knit_dye_source, a.knit_dye_company, b.id as trans_id, b.prod_id, b.transaction_date, b.cons_uom, b.requisition_no,b.transaction_type, b.brand_id, b.cons_quantity, b.return_qnty
					
					from inv_issue_master a, inv_transaction b  $propotionate_tbl 
					where a.item_category=1 and a.entry_form=3 and a.company_id=$cbo_company_name and a.id=b.mst_id and b.item_category=1 and b.transaction_type=2 
					and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0
					$knit_source_cond_party $knit_company_cond_party $challan_cond $where_cond  $issue_purpose_cond
					order by a.knit_dye_company, a.issue_number_prefix_num, a.issue_date";*/
					//A.ISSUE_NUMBER, A.ISSUE_NUMBER_PREFIX_NUM,A.ENTRY_FORM, A.BUYER_ID, A.BOOKING_NO, A.ISSUE_DATE, A.CHALLAN_NO, A.ISSUE_BASIS, A.KNIT_DYE_SOURCE, A.KNIT_DYE_COMPANY, B.ID AS TRANS_ID, 
					//B.PROD_ID, B.TRANSACTION_DATE, B.CONS_UOM, B.REQUISITION_NO,B.TRANSACTION_TYPE, B.BRAND_ID, B.CONS_QUANTITY, B.RETURN_QNTY
					$sql_iss="select max(a.issue_number) as ISSUE_NUMBER, max(a.issue_number_prefix_num) as ISSUE_NUMBER_PREFIX_NUM, max(a.entry_form) as ENTRY_FORM, max(a.buyer_id) as BUYER_ID, max(a.booking_no) as BOOKING_NO, max(a.issue_date) as ISSUE_DATE, max(a.challan_no) as CHALLAN_NO, max(a.issue_basis) as ISSUE_BASIS, a.knit_dye_source as KNIT_DYE_SOURCE, a.knit_dye_company as KNIT_DYE_COMPANY, max(b.id) as TRANS_ID, max(b.prod_id) as PROD_ID, max(b.transaction_date) as TRANSACTION_DATE, max(b.cons_uom) as CONS_UOM, max(b.requisition_no) as REQUISITION_NO, max(b.transaction_type) as TRANSACTION_TYPE, max(b.brand_id) as BRAND_ID, sum(b.cons_quantity) as CONS_QUANTITY, sum(b.return_qnty) as RETURN_QNTY, 1 as TYPE
					from inv_issue_master a, inv_transaction b  $propotionate_tbl 
					where a.item_category=1 and a.entry_form=3 and a.company_id=$cbo_company_name and a.id=b.mst_id and b.item_category=1 and b.transaction_type=2 
					and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and b.transaction_date < '$from_date'
					$knit_source_cond_party $knit_company_cond_party $challan_cond $where_cond  $issue_purpose_cond
					group by a.knit_dye_source, a.knit_dye_company
					union all
					select a.issue_number as ISSUE_NUMBER, a.issue_number_prefix_num as ISSUE_NUMBER_PREFIX_NUM, a.entry_form as ENTRY_FORM, a.buyer_id as BUYER_ID, a.booking_no as BOOKING_NO, a.issue_date as ISSUE_DATE, a.challan_no as CHALLAN_NO, a.issue_basis as ISSUE_BASIS, a.knit_dye_source as KNIT_DYE_SOURCE, a.knit_dye_company as KNIT_DYE_COMPANY, b.id as TRANS_ID, b.prod_id as PROD_ID, b.transaction_date as TRANSACTION_DATE, b.cons_uom as CONS_UOM, b.requisition_no as REQUISITION_NO, b.transaction_type as TRANSACTION_TYPE, b.brand_id as BRAND_ID, b.cons_quantity as CONS_QUANTITY, b.return_qnty as RETURN_QNTY, 2 as TYPE
					from inv_issue_master a, inv_transaction b  $propotionate_tbl 
					where a.item_category=1 and a.entry_form=3 and a.company_id=$cbo_company_name and a.id=b.mst_id and b.item_category=1 and b.transaction_type=2 
					and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and b.transaction_date between '$from_date' and '$to_date'
					$knit_source_cond_party $knit_company_cond_party $challan_cond $where_cond $issue_purpose_cond
					order by ISSUE_DATE DESC,KNIT_DYE_COMPANY, ISSUE_NUMBER_PREFIX_NUM";
					//echo $sql_iss;die;
					$sql_iss_res=sql_select($sql_iss);
					$tot_opening_balIssue=0;
					$tot_opening_bal_return=0;
					foreach ($sql_iss_res as $rowIss )
					{
						/*$trns_date=''; $date_frm=''; $date_to='';
						$trns_date=date('Y-m-d',strtotime($rowIss[csf('issue_date')]));
						$date_frm=date('Y-m-d',strtotime($from_date));
						$date_to=date('Y-m-d',strtotime($to_date));*/
						
						$opening_balIssue=0;
						//if($trns_date<$date_frm)
						if($rowIss[('TYPE')]==1)
						{
							//echo $trns_date.'--'.$date_frm.'--'.$date_to.'<br>'; 
							 $opening_balIssue=$rowIss[('CONS_QUANTITY')];//+$rowIss[csf('return_qnty')];
							 $tot_opening_balIssue+=$opening_balIssue;
							 $tot_opening_bal_return+=$rowIss[('RETURN_QNTY')];
							 
							$iss_qty_arr[$rowIss[('KNIT_DYE_COMPANY')]]['0']['0']['opening_bal']+=$opening_balIssue;
							$iss_qty_arr[$rowIss[('KNIT_DYE_COMPANY')]]['0']['0']['opening_bal_return']+=$rowIss[('RETURN_QNTY')];
							//echo  $opening_balIssue.'<br>';
						}
						else
						{
							$order_ids=$order_nos_array[$rowIss[('TRANS_ID')]]['yarn_issue'];
							// echo $order_ids.', ';
							$iss_qty_arr[$rowIss[('KNIT_DYE_COMPANY')]][$rowIss[('TRANS_ID')]][$rowIss[('PROD_ID')]]['issue']+=$rowIss[('CONS_QUANTITY')];
							$iss_qty_arr[$rowIss[('KNIT_DYE_COMPANY')]][$rowIss[('TRANS_ID')]][$rowIss[('PROD_ID')]]['return']+=$rowIss[('RETURN_QNTY')];
							
							$all_data_arr[$rowIss[('KNIT_DYE_COMPANY')]]['iss'].=$rowIss[('ISSUE_NUMBER')].'**'.$rowIss[('ISSUE_NUMBER_PREFIX_NUM')].'**'.$rowIss[('BUYER_ID')].'**'.$rowIss[('BOOKING_NO')].'**'.$rowIss[('ISSUE_DATE')].'**'.$rowIss[('CHALLAN_NO')].'**'.$rowIss[('ISSUE_BASIS')].'**'.$rowIss[('KNIT_DYE_SOURCE')].'**'.$rowIss[('TRANS_ID')].'**'.$rowIss[('PROD_ID')].'**'.$rowIss[('CONS_UOM')].'**'.$rowIss[('REQUISITION_NO')].'**'.$rowIss[('BRAND_ID')].'**'.$rowIss[('ENTRY_FORM')].'**'.$rowIss[('TRANSACTION_TYPE')].'___';
						}
					}
					//print_r($iss_qty_arr);die;
					
					//for program information
					$sql_prog = "SELECT KNIT_ID, REQUISITION_NO FROM PPL_YARN_REQUISITION_ENTRY WHERE STATUS_ACTIVE = 1 AND IS_DELETED = 0";
					$sql_prog_rslt = sql_select($sql_prog);
					$req_prog_arr = array();
					$prog_req_arr = array();
					foreach($sql_prog_rslt as $row)
					{
						$req_prog_arr[$row['REQUISITION_NO']] = $row['KNIT_ID'];
						$prog_req_arr[$row['KNIT_ID']] = $row['REQUISITION_NO'];
					}
					unset($sql_prog_rslt);

					//for grey delivery to store roll program information
					$sql_dsrr = "SELECT A.SYS_NUMBER, B.GREY_SYS_ID, C.BOOKING_NO FROM PRO_GREY_PROD_DELIVERY_MST A, PRO_GREY_PROD_DELIVERY_DTLS B, INV_RECEIVE_MASTER C WHERE A.ID = B.MST_ID AND B.GREY_SYS_ID = C.ID AND A.ENTRY_FORM = 56 AND A.COMPANY_ID = ".$cbo_company_name." AND C.ENTRY_FORM = 2 AND C.ITEM_CATEGORY = 13 AND C.RECEIVE_BASIS = 2 AND A.STATUS_ACTIVE = 1 AND A.IS_DELETED = 0 AND B.STATUS_ACTIVE = 1 AND B.IS_DELETED = 0 AND C.STATUS_ACTIVE = 1 AND C.IS_DELETED = 0";
					$sql_dsrr_rslt = sql_select($sql_dsrr);
					$dsrr_prog_arr = array();
					foreach($sql_dsrr_rslt as $row)
					{
						$dsrr_prog_arr[$row['SYS_NUMBER']][$row['GREY_SYS_ID']] = $row['BOOKING_NO'];
					}
					unset($sql_dsrr_rslt);

					//print_r($iss_qty_arr);
					$empty_opening_balance=$tot_opening_balIssue-($tot_bal_opening_balfRec+$tot_opening_bal_rej_yarn+$tot_opening_bal_ret_yarn);
					$empty_retable_opening_balance=$tot_opening_bal_return-$tot_opening_bal_ret_yarn;
					unset($sql_iss_res); $i=1;
					if(empty($all_data_arr)) 
					{
						echo "<tr>
								<td width='2230' colspan='23' align='right'> ".number_format($empty_opening_balance,2)."</td>
								<td  align='right' width='100'>  ".number_format($empty_retable_opening_balance,2)."</td>
							  </tr>";	
					}
					$partyId=str_replace("'","",$txt_knitting_com_id);
					$partyId=explode(",", $partyId);
					foreach ($all_data_arr as $party_id=>$party_data )
					{
						
						if(str_replace("'","",$cbo_knitting_source)==1) $knitting_party=$company_arr[$party_id]; 
						else if(str_replace("'","",$cbo_knitting_source)==3) $knitting_party=$supplier_arr[$party_id];
						else $knitting_party="&nbsp;";	
						 $balance=0; $return_balance=0;
						$party_issue_qty=$party_returnable_qty=$party_rec_qty=$party_fab_rej_qty=$party_return_qty=$party_yarn_rej_qty=$party_yarnRec_qty=0;
						//echo $rec_qty_arr[$party_id]['0']['0']['opening_bal_fRec'].'<br>';
						
						//$opening_balance=$open_issue_qty-$returnable_qty;
						//$rec_qty_arr[$rowRec[csf('knitting_company')]]['0']['0']['opening_bal_fRec']
						$opening_bal_ret_yarn=$rec_qty_arr[$party_id]['0']['0']['opening_bal_ret_yarn'];//$rec_qty_arr[$rowRec[csf('knitting_company')]]['0']['0']['opening_bal_return_yarn']
						$opening_bal_return=$iss_qty_arr[$party_id]['0']['0']['opening_bal_return'];
						
						$opening_bal_fRec=$rec_qty_arr[$party_id]['0']['0']['opening_bal_fRec'];
						//$opening_bal_yRec=$rec_qty_arr[$party_id]['0']['0']['opening_bal_yRec'];
						//$opening_bal=$rec_qty_arr[$party_id]['0']['0']['opening_bal'];
						$opening_bal_issue= $iss_qty_arr[$party_id]['0']['0']['opening_bal'];
						//echo $party_id.'='.$opening_bal_issue;
						$opening_bal_yRec_dye_twist= $yarnRec_qty_arr[$party_id]['0']['0']['opening_bal_yRec']; 
						$opening_bal_rej_yarn= $rec_qty_arr[$party_id]['0']['0']['opening_bal_rej_yarn'];
						//$rec_qty_arr[$rowyRec[csf('knitting_company')]]['0']['0']['opening_bal_rej_yarn']
						$retable_opening_balance=$opening_bal_return-$opening_bal_ret_yarn;
						$opening_balance=$opening_bal_issue-($opening_bal_fRec+$opening_bal_rej_yarn+$opening_bal_ret_yarn+$opening_bal_yRec_dye_twist);
						//echo $opening_bal_issue.'='.$opening_bal_fRec.'=-'.$opening_bal_ret_yarn;
						//$opening_balance=$iss_qty_arr[$party_id]['0']['0']['opening_bal']-($rec_qty_arr[$party_id]['0']['0']['opening_bal_fRec']+$yarnRec_qty_arr[$party_id]['0']['0']['opening_bal_yRec']);

						//if(in_array($party_id, $partyId)) {

							?>
							<tr bgcolor="#EFEFEF"><td colspan="23"><b>Party name: <? echo $knitting_party; ?></b></td></tr>
							<tr bgcolor="#EFEFEF"><td colspan="21" align="left"><b>Opening Balance: <? echo change_date_format($from_date); ?></b></td><td align="right"><? //echo number_format($opening_balance,2); ?></b></td><td align="right"><? echo number_format($opening_balance,2);?></td></tr>
							<?
							//Issue Data
							$ex_partyIssData='';
							$ex_partyIssData=array_filter(array_unique(explode('___',$party_data['iss']))); 
							foreach($ex_partyIssData as $dataIss)
							{
								$ex_data_iss='';
								$ex_data_iss=explode('**',$dataIss);
								$iss_num=''; $iss_no_pre=''; $buyer_id=''; $booking_no=''; $iss_date=''; $challan_no=''; $iss_basis=''; $party_source=''; $trns_id=''; $prod_id=''; $cons_uom=''; $req_no=''; $brand_id=''; $opening_balance_issue=0; $issue_qty=0; $returnable_qty=0;
								$iss_num=$ex_data_iss[0]; 
								$iss_no_pre=$ex_data_iss[1]; 
								$buyer_id=$ex_data_iss[2]; 
								$booking_no=$ex_data_iss[3]; 
								$iss_date=$ex_data_iss[4]; 
								$challan_no=$ex_data_iss[5]; 
								$iss_basis=$ex_data_iss[6]; 
								$party_source=$ex_data_iss[7]; 
								$trns_id=$ex_data_iss[8]; 
								$prod_id=$ex_data_iss[9]; 
								$cons_uom=$ex_data_iss[10];  
								$req_no=$ex_data_iss[11]; 
								$brand_id=$ex_data_iss[12];
								$entry_form=$ex_data_iss[13];
								$trans_type=$ex_data_iss[14];
								
								$issue_qty=$iss_qty_arr[$party_id][$trns_id][$prod_id]['issue'];
								$returnable_qty=$iss_qty_arr[$party_id][$trns_id][$prod_id]['return'];
								//$iss_qty_arr[$rowIss[csf('knit_dye_company')]][$rowIss[csf('trans_id')]][$rowIss[csf('prod_id')]]['return']
							
								
								$balance=($opening_balance+$balance+$issue_qty);
								$opening_balance=0;
								//$balance=$val;
								$return_balance=$return_balance+$returnable_qty; 
								 
								$prog_no_str = '';
								if($iss_basis==1)
								{
									$booking_reqsn_no=$booking_no;
									//$req_prog_arr[$row['REQUISITION_NO']]
								}
								else if($iss_basis==3)
								{
									$booking_reqsn_no=$req_no;
									$prog_no_str = $req_prog_arr[$req_no];
								}
								else
								{
									$booking_reqsn_no="&nbsp;";
								}
									
								//$all_po_id_ret=array_unique(explode(",",$order_nos_array[$trns_id]['yarn_return']));
								//echo $trns_id.'A';
								$all_po_id=$order_nos_data_array[$trns_id];
								$order_nos='';$style_ref='';
								$internal_ref = '';
								if(!empty($all_po_id))
								{
									foreach($all_po_id as $po_id) //$po_arr[$row[csf('id')]]['style']
									{
										if($order_nos=='') $order_nos=$po_arr[$po_id]['name']; else $order_nos.=",".$po_arr[$po_id]['name'];
										if($style_ref=='') $style_ref=$po_arr[$po_id]['style']; else $style_ref.=",".$po_arr[$po_id]['style'];
										if($internal_ref=='') $internal_ref=$po_arr[$po_id]['internal_ref']; else $internal_ref.=",".$po_arr[$po_id]['internal_ref'];

										$order_qnty+=$po_arr[$po_id]['qnty'];
										$buyer=$buyer_arr[$po_arr[$po_id]['buyer']];
									}
								}
								else
								{
									//echo  $po_id.'DD';
									foreach($all_po_id_ret as $po_id) //$po_arr[$row[csf('id')]]['style']
									{
										if($order_nos=='') $order_nos=$po_arr[$po_id]['name']; else $order_nos.=",".$po_arr[$po_id]['name'];
										if($style_ref=='') $style_ref=$po_arr[$po_id]['style']; else $style_ref.=",".$po_arr[$po_id]['style'];

										if($internal_ref=='') $internal_ref=$po_arr[$po_id]['internal_ref']; else $internal_ref.=",".$po_arr[$po_id]['internal_ref'];

										$order_qnty+=$po_arr[$po_id]['qnty'];
										$buyer=$buyer_arr[$po_arr[$po_id]['buyer']];
										
									}
								}
								$styles_ref=implode(",",array_unique(explode(",",$style_ref)));
								$order_nos=implode(",",array_unique(explode(",",$order_nos)));
								
								if ($i%2==0) $bgcolor="#E9F3FF";
								else $bgcolor="#FFFFFF";
								?>
								<tr bgcolor="<? echo $bgcolor;?>" onClick="change_color('tr<? echo $i;?>','<? echo $bgcolor;?>')" id="tr<? echo $i;?>">
									<td width="40"><? echo $i; ?></td>
									<td width="80" align="center"><? echo change_date_format($iss_date); ?></td>
									<td width="125"><div style="word-wrap:break-word; width:125px"><? echo $iss_num; ?></div></td>
									<td width="115">&nbsp;</td>
									<td width="115"><p>&nbsp;<? echo $challan_no; ?></p></td>
									<td width="130"><p>&nbsp;<? echo $booking_reqsn_no; ?></p></td>
									<td width="100"><p>&nbsp;<? echo $prog_no_str; ?></p></td>
									<td width="80"><p><? echo $buyer; ?>&nbsp;</p></td>
	                                <td width="130"><div style="word-wrap:break-word; width:130px"><? echo $styles_ref; ?></div></td>
									<td width="130"><div style="word-wrap:break-word; width:130px"><? echo $order_nos; ?></div></td>
									<td width="90"><div style="word-wrap:break-word; width:90px"><? echo $internal_ref; ?></div></td>
									<td width="90" align="right"><? echo number_format($order_qnty,0,'.',''); ?></td>
									<td width="80"><p>&nbsp;<? echo $brand_arr[$brand_id]; ?></p></td>
									<td width="150"><div style="word-wrap:break-word; width:150px"><? echo $product_arr[$prod_id]['name']; ?></div></td>
									<td width="80"><p><? echo $product_arr[$prod_id]['lot']; ?></p></td>
									<td width="60" align="center"><? echo $unit_of_measurement[$cons_uom]; ?>&nbsp;</td>
									<td width="100" align="right"><? echo number_format($issue_qty,2); ?></td>
	                               <!-- <td width="100" align="right"><? //echo number_format($returnable_qty,2); ?></td>-->
									<td width="100" align="right">&nbsp;</td>
									<td width="100" align="right">&nbsp;</td>
	                                <td width="100" align="right"></td>
									<td width="100" align="right">&nbsp;</td>
									<td width="100" align="right">&nbsp;</td>
									<td width="" align="right"><? echo number_format($balance,2); ?></td>
	                                <!--<td align="right"><? //echo number_format($return_balance,2);//$return_balance=0; ?></td>-->
								</tr>
								<?
								
								$party_issue_qty+=$issue_qty;
								$tot_issue_qty+=$issue_qty;
								$party_returnable_qty+=$returnable_qty;
								$tot_returnable_qty+=$returnable_qty;
								$i++;
							}
							// Receive Data
							$ex_partyRecData='';
							$ex_partyRecData=array_filter(array_unique(explode('___',$party_data['rec']))); 
							foreach($ex_partyRecData as $dataRec)
							{
								$ex_data_rec='';
								$ex_data_rec=explode('**',$dataRec);
								
								$rec_num=''; $rec_num_pre=''; $buyer_id=''; $booking_no=''; $rec_date=''; $challan_no=''; $rec_basis=''; $party_source=''; $trns_id=''; $prod_id=''; $cons_uom=''; $yarn_iss_challlan=''; $brand_id=''; $item_category=''; $receive_qty=0; $return_qty=0; $fab_reject_qty=0; $yarn_reject_qty=0; $yarnReceive_qty=0;
								$rec_num=$ex_data_rec[0]; 
								$rec_num_pre=$ex_data_rec[1]; 
								$buyer_id=$ex_data_rec[2]; 
								$booking_no=$ex_data_rec[3]; 
								$rec_date=$ex_data_rec[4]; 
								$challan_no=$ex_data_rec[5]; 
								$rec_basis=$ex_data_rec[6]; 
								$party_source=$ex_data_rec[7]; 
								$trns_id=$ex_data_rec[8]; 
								$prod_id=$ex_data_rec[9]; 
								$cons_uom=$ex_data_rec[10];  
								$brand_id=$ex_data_rec[11];
								$yarn_iss_challlan=$ex_data_rec[12]; 
								$item_category=$ex_data_rec[13]; 
								$entry_form=$ex_data_rec[14]; 
								$trans_type=$ex_data_rec[15]; 
															
								$receive_qty=$rec_qty_arr[$party_id][$trns_id][$prod_id]['fRec'];
								$yarnReceive_qty=$rec_qty_arr[$party_id][$trns_id][$prod_id]['yRec'];
								$return_qty=$rec_qty_arr[$party_id][$trns_id][$prod_id]['ret_yarn'];
								$fab_reject_qty=$rec_qty_arr[$party_id][$trns_id][$prod_id]['rej_fab'];
								$yarn_reject_qty=$rec_qty_arr[$party_id][$trns_id][$prod_id]['rej_yarn'];
								
								$all_po_id=$order_nos_data_array[$trns_id];
								$balance=$opening_balance+$balance-($receive_qty+$yarnReceive_qty+$fab_reject_qty+$yarn_reject_qty+$return_qty);
								$opening_balance=0;
								$return_balance=$return_balance-($return_qty+$yarn_reject_qty); 
								
								/*if($iss_basis==1)
								{
									$booking_reqsn_no=$booking_no;
								}
								else if($iss_basis==3)
								{
									$booking_reqsn_no=$req_no;
								}
								else
								{
									$booking_reqsn_no="&nbsp;";
								}*/
								
								//for program no
								$prog_no_str = '';
								if($rec_basis == 2)
								{
									$booking_reqsn_no = $prog_req_arr[$booking_no];
									$prog_no_str = $booking_no;
								}
								else if($rec_basis == 3)
								{
									$prog_no_str = $req_prog_arr[$booking_no];
								}
								else if($rec_basis == 10)
								{
									$booking_reqsn_no = $booking_no;
									$prog_no_str = implode(', ', $dsrr_prog_arr[$booking_no]);
								}
								
									
								$all_po_id='';
								$all_po_id=$order_nos_data_array[$trns_id];
																	
								$order_nos=''; $style_ref=''; $order_qty=0;$po_idss='';$buyer=''; $internal_ref = '';
								if(!empty($all_po_id))
								{
									foreach($all_po_id as $po_id)
									{
										if($order_nos=='') $order_nos=$po_arr[$po_id]['name']; else $order_nos.=",".$po_arr[$po_id]['name'];
										if($style_ref=='') $style_ref=$po_arr[$po_id]['style']; else $style_ref.=",".$po_arr[$po_id]['style'];
										if($internal_ref=='') $internal_ref=$po_arr[$po_id]['internal_ref']; else $internal_ref.=",".$po_arr[$po_id]['internal_ref'];

										$order_qnty+=$po_arr[$po_id]['qnty'];
										$buyer=$buyer_arr[$po_arr[$po_id]['buyer']];
										//$po_idss.=$po_id.',';
									}
								}
								else
								{
									//echo $rec_qty_arr[$party_id][$trns_id][$prod_id]['ret_po'].'fff';
									$order_qnty=0;
									foreach($all_po_id_ret as $po_id)
									{
										if($order_nos=='') $order_nos=$po_arr[$po_id]['name']; else $order_nos.=",".$po_arr[$po_id]['name'];
										if($style_ref=='') $style_ref=$po_arr[$po_id]['style']; else $style_ref.=",".$po_arr[$po_id]['style'];
										if($internal_ref=='') $internal_ref=$po_arr[$po_id]['internal_ref']; else $internal_ref.=",".$po_arr[$po_id]['internal_ref'];
										$order_qnty+=$po_arr[$po_id]['qnty'];
										$buyer=$buyer_arr[$po_arr[$po_id]['buyer']];
										//$po_idss.=$po_id.',';
									}
								}
								$styles_ref=implode(",",array_unique(explode(",",$style_ref)));	
								$order_nos=implode(",",array_unique(explode(",",$order_nos)));
								
								if ($i%2==0) $bgcolor="#E9F3FF";
								else $bgcolor="#FFFFFF";
								?>
								<tr bgcolor="<? echo $bgcolor;?>" onClick="change_color('tr<? echo $i;?>','<? echo $bgcolor;?>')" id="tr<? echo $i;?>">
									<td width="40"><? echo $i; ?></td>
									<td width="80" align="center"><? echo change_date_format($rec_date); ?></td>
									<td width="125"><div style="word-wrap:break-word; width:125px"><? echo $rec_num; ?></div></td>
									<td width="115"><p>&nbsp;<? echo $challan_no; ?></p></td>
									<td width="115"><p>&nbsp;<? echo $yarn_iss_challlan; ?></p></td>
									<td width="130"><p>&nbsp;<? echo $booking_reqsn_no; ?></p></td>
                                    <td width="100"><p>&nbsp;<? echo $prog_no_str; ?></p></td>
									<td width="80"><p><? echo $buyer; ?>&nbsp;</p></td>
	                                <td width="130"><div style="word-wrap:break-word; width:130px"><? echo $styles_ref; ?></div></td>
									<td width="130"><div style="word-wrap:break-word; width:130px"><? echo $order_nos; ?></div></td>
									<td width="90"><div style="word-wrap:break-word; width:90px"><? echo $internal_ref; ?></div></td>
									<td width="90" align="right"><? echo number_format($order_qnty,0); ?></td>
									<td width="80"><p>&nbsp;<? echo $brand_arr[$brand_id]; ?></p></td>
									

									<td width="150"><div style="word-wrap:break-word; width:150px"><? echo $product_arr[$prod_id]['name']; ?></div></td>
									<td width="80"><p><? echo $product_arr[$prod_id]['lot']; ?></p></td>


									<td width="60" align="center"><? echo $unit_of_measurement[$cons_uom]; ?>&nbsp;</td>
									<!--<td width="100" align="right">&nbsp;</td>-->
	                                <td width="100" align="right">&nbsp;</td>
									<td width="100" align="right"><? echo number_format($receive_qty,2); ?></td>
									<td width="100" align="right"><? echo number_format($fab_reject_qty,2); ?></td>
	                                <td width="100" align="right"><? echo number_format($yarnReceive_qty,2); ?></td>
									<td width="100" align="right"><? echo number_format($return_qty,2); ?></td>
									<td width="100" align="right"><? echo number_format($yarn_reject_qty,2); ?></td>
									<td width="" align="right"><? echo number_format($balance,2); ?></td>
	                                <!--<td align="right"><? //echo number_format($return_balance,2); ?></td>-->
								</tr>
								<?
								//$party_issue_qty=$party_returnable_qty=$party_rec_qty=$party_fab_rej_qty=$party_return_qty=$party_yarn_rej_qty
								$party_rec_qty+=$receive_qty;
								$tot_rec_qty+=$receive_qty;
								$party_fab_rej_qty+=$fab_reject_qty;
								$tot_fab_rej_qty+=$fab_reject_qty;
								$party_yarnRec_qty+=$yarnReceive_qty;
								$tot_yarnRec_qty+=$yarnReceive_qty;
								$party_return_qty+=$return_qty;
								$tot_return_qty+=$return_qty;
								$party_yarn_rej_qty+=$yarn_reject_qty;
								$tot_yarn_rej_qty+=$yarn_reject_qty;
								$i++;
							}

							$ex_partyyRecData='';
							$ex_partyyRecData=array_filter(array_unique(explode('___',$party_data['yrec']))); 
							foreach($ex_partyyRecData as $datayRec)
							{
								//echo $datayRec.'==<br>';
								$ex_data_yrec='';
								$ex_data_yrec=explode('!!',$datayRec);
								
								$rec_num=''; $rec_num_pre=''; $buyer_id=''; $booking_no=''; $rec_date=''; $challan_no=''; $rec_basis=''; $party_source=''; $trns_id=''; $prod_id=''; $cons_uom=''; $yarn_iss_challlan=''; $brand_id=''; $item_category=''; $receive_qty=0; $return_qty=0; $fab_reject_qty=0; $yarn_reject_qty=0; $yarnReceive_qty=0;
								$rec_num=$ex_data_yrec[0]; 
								$rec_num_pre=$ex_data_yrec[1]; 
								$buyer_id=$ex_data_yrec[2]; 
								$booking_no=$ex_data_yrec[3]; 
								$rec_date=$ex_data_yrec[4]; 
								$challan_no=$ex_data_yrec[5]; 
								$rec_basis=$ex_data_yrec[6]; 
								$party_source=$ex_data_yrec[7]; 
								$trns_id=$ex_data_yrec[8]; 
								$prod_id=$ex_data_yrec[9]; 
								$cons_uom=$ex_data_yrec[10];  
								$brand_id=$ex_data_yrec[11];
								$yarn_iss_challlan=$ex_data_yrec[12]; 
								$item_category=$ex_data_yrec[13]; 
								$entry_form=$ex_data_yrec[14]; 
								$trans_type=$ex_data_yrec[15]; 
								
								$yarnReceive_qty=$yarnRec_qty_arr[$party_id][$trns_id][$prod_id]['yRec'];
								$return_qty=$rec_qty_arr[$party_id][$trns_id][$prod_id]['ret_yarn'];
								$balance=$opening_balance+$balance-($yarnReceive_qty);
								$opening_balance=0;
								$return_balance=$return_balance; 
								
								$prog_no_str = '';
								if($iss_basis==1)
									$booking_reqsn_no=$booking_no;
								else if($iss_basis==3)
									$booking_reqsn_no=$req_no;
								else
									$booking_reqsn_no="&nbsp;";
									
							
								/*if($item_category==13)
									$all_po_id=explode(",",$order_nos_array[$trns_id]['grey_recv']);
								else
									$all_po_id=explode(",",$order_nos_array[$trns_id]['yarn_issue']);*/
								$all_po_id=$order_nos_data_array[$trns_id];
								$order_nos='';$style_ref=''; $order_qty=0;$buyer=''; $internal_ref = '';
								foreach($all_po_id as $po_id)
								{
									if($order_nos=='') $order_nos=$po_arr[$po_id]['name']; else $order_nos.=",".$po_arr[$po_id]['name'];
									if($style_ref=='') $style_ref=$po_arr[$po_id]['style']; else $style_ref.=",".$po_arr[$po_id]['style'];

									if($internal_ref=='') $internal_ref=$po_arr[$po_id]['internal_ref']; else $internal_ref.=",".$po_arr[$po_id]['internal_ref'];
									//echo $po_id.'d';
									$buyer=$buyer_arr[$po_arr[$po_id]['buyer']];
									$order_qnty+=$po_arr[$po_id]['qnty'];
								}	
								$styles_ref=implode(",",array_unique(explode(",",$style_ref)));
								if ($i%2==0) $bgcolor="#E9F3FF";
								else $bgcolor="#FFFFFF";
								?>
								<tr bgcolor="<? echo $bgcolor;?>" onClick="change_color('tr<? echo $i;?>','<? echo $bgcolor;?>')" id="tr<? echo $i;?>">
									<td width="40"><? echo $i; ?></td>
									<td width="80" align="center"><? echo change_date_format($rec_date); ?></td>
									<td width="125"><div style="word-wrap:break-word; width:125px"><? echo $rec_num; ?></div></td>
									<td width="115"><p>&nbsp;<? echo $challan_no; ?></p></td>
									<td width="115"><p>&nbsp;<? echo $yarn_iss_challlan; ?></p></td>
									<td width="130"><p>&nbsp;<? echo $booking_no; ?></p></td>
                                    <td width="100"><p>&nbsp;<? echo $prog_no_str; ?></p></td>
									<td width="80"><p>F<? echo $buyer; ?>&nbsp;</p></td>
	                                <td width="130"><div style="word-wrap:break-word; width:130px"><? $styles_ref; ?></div></td>
									<td width="130"><div style="word-wrap:break-word; width:130px"><? echo $order_nos; ?></div></td>
									<td width="90"><div style="word-wrap:break-word; width:90px"><? echo $internal_ref; ?></div></td>
									<td width="90" align="right"><? echo number_format($order_qnty,0); ?></td>
									<td width="80"><p>&nbsp;<? echo $brand_arr[$brand_id]; ?></p></td>
									<td width="150"><div style="word-wrap:break-word; width:150px"><? echo $product_arr[$prod_id]['name']; ?></div></td>
									<td width="80"><p><? echo $product_arr[$prod_id]['lot']; ?></p></td>
									<td width="60" align="center"><? echo $unit_of_measurement[$cons_uom]; ?>&nbsp;</td>
									<!--<td width="100" align="right">&nbsp;</td>-->
	                                <td width="100" align="right">&nbsp;</td>
									<td width="100" align="right">&nbsp;</td>
									<td width="100" align="right">&nbsp;</td>
	                                <td width="100" align="right"><? echo number_format($yarnReceive_qty,2); ?></td>
									<td width="100" align="right">&nbsp;</td>
									<td width="100" align="right">&nbsp;</td>
									<td width="" align="right"><? echo number_format($balance,2); ?></td>
	                                <!--<td align="right"><? //echo number_format($return_balance,2); ?></td>-->
								</tr>
								<?
								//$party_issue_qty=$party_returnable_qty=$party_rec_qty=$party_fab_rej_qty=$party_return_qty=$party_yarn_rej_qty
								$party_rec_qty+=$receive_qty;
								$tot_rec_qty+=$receive_qty;
								$party_fab_rej_qty+=$fab_reject_qty;
								$tot_fab_rej_qty+=$fab_reject_qty;
								$party_yarnRec_qty+=$yarnReceive_qty;
								$tot_yarnRec_qty+=$yarnReceive_qty;
								$party_return_qty+=$return_qty;
								$tot_return_qty+=$return_qty;
								$party_yarn_rej_qty+=$yarn_reject_qty;
								$tot_yarn_rej_qty+=$yarn_reject_qty;
								$i++;
							}
							
							?>
	                        <tr bgcolor="#CCCCCC" >
	                            <td>&nbsp;</td>
	                            <td>&nbsp;</td>
	                            <td>&nbsp;</td>
	                            <td>&nbsp;</td>
	                            <td>&nbsp;</td>
	                            <td>&nbsp;</td>
	                            <td>&nbsp;</td>
	                            <td>&nbsp;</td>
	                            <td>&nbsp;</td>
	                            <td>&nbsp;</td>
	                            <td>&nbsp;</td>
	                            <td>&nbsp;</td>
	                            <td>&nbsp;</td>
	                            <td>&nbsp;</td>
	                            <td>&nbsp;</td>
	                            <td><strong>Party Total</strong></td> 
	                            <td align="right"><? echo number_format($party_issue_qty,2); ?></td>
	                           <!-- <td align="right"><? //echo number_format($party_returnable_qty,2); ?></td>-->
	                            <td align="right"><? echo number_format($party_rec_qty,2); ?></td>
	                            <td align="right"><? echo number_format($party_fab_rej_qty,2); ?></td>
	                            <td align="right"><? echo number_format($party_yarnRec_qty,2); ?></td>
	                            
	                            <td align="right"><? echo number_format($party_return_qty,2); ?></td>
	                            <td align="right"><? echo number_format($party_yarn_rej_qty,2); ?></td>
	                            <td align="right"><? echo number_format($balance,2); ?></td>
	                            <!--<td align="right"><? //echo number_format($return_balance,2); ?></td>-->
	                        </tr>
	                        <?
							$tot_balance+=$balance;
							$tot_return_balance+=$return_balance;
						//}
					}

					?>
                </table>
            </div>
            <table width="2320" cellpadding="0" cellspacing="0" border="1" rules="all" class="tbl_bottom">
                <tr>
                    <td width="40">&nbsp;</td>
                    <td width="80">&nbsp;</td>
                    <td width="125">&nbsp;</td>
                    <td width="115">&nbsp;</td>
                    <td width="115">&nbsp;</td>
                    <td width="130">&nbsp;</td>
                    <td width="100">&nbsp;</td>
                    <td width="80">&nbsp;</td>
                    <td width="130">&nbsp;</td>
                    <td width="130">&nbsp;</td>
                    <td width="90">&nbsp;</td>
                    <td width="90">&nbsp;</td>
                    <td width="80">&nbsp;</td>
                    <td width="150">&nbsp;</td>
                    <td width="80">&nbsp;</td>
                    <td width="60"><strong>Grand Total</strong></td>
                    <td width="100" align="right"><? echo number_format($tot_issue_qty,2); ?></td>
                  <!--  <td width="100" align="right"><? //echo number_format($tot_returnable_qty,2); ?></td>-->
                    <td width="100" align="right"><? echo number_format($tot_rec_qty,2); ?></td>
                    <td width="100" align="right"><? echo number_format($tot_fab_rej_qty,2); ?></td>
                    <td width="100" align="right"><? echo number_format($tot_yarnRec_qty,2); ?></td>
                    <td width="100" align="right"><? echo number_format($tot_return_qty,2); ?></td>
                    <td width="100" align="right"><? echo number_format($tot_yarn_rej_qty,2); ?></td>
                    <td width="" align="right"><? echo number_format($tot_balance,2); ?></td>
                   <!-- <td align="right"><? //echo number_format($tot_return_balance,2); ?></td>-->
                </tr>
            </table>
        </div>
		<?
	}
	else if($type==3)
	{
		//$all_party=explode(",",$knitting_company);
		
		$po_arr=array();
		$datapoArray=sql_select("select id, po_number, po_quantity from wo_po_break_down");
		
		foreach($datapoArray as $row)
		{
			$po_arr[$row[csf('id')]]['name']=$row[csf('po_number')];
			$po_arr[$row[csf('id')]]['qnty']=$row[csf('po_quantity')];
		}		
		if($db_type==0) $grpby_field="group by trans_id";
		if($db_type==2) $grpby_field="group by trans_id,entry_form,trans_type";
		else $grpby_field="";
		
		if (str_replace("'","",$txt_challan)=="") $challan_cond=""; else $challan_cond=" and a.issue_number_prefix_num=$txt_challan";
		
		$order_nos_array=array();
		if($db_type==0)
		{
			$datapropArray=sql_select("select trans_id,
				CASE WHEN entry_form='3' and trans_type=2 THEN group_concat(po_breakdown_id) END AS yarn_order_id,
				CASE WHEN entry_form in (2,22) and trans_type=1 THEN group_concat(po_breakdown_id) END AS grey_order_id,
				CASE WHEN entry_form='9' and trans_type=4 THEN group_concat(po_breakdown_id) END AS yarn_return_order_id 
				from order_wise_pro_details where trans_id<>0 and quantity>0 and entry_form in (2,3,9,22) and status_active=1 and is_deleted=0 group by trans_id");
		}
		else
		{
			$datapropArray=sql_select("select trans_id,
				listagg(CASE WHEN entry_form='3' and trans_type=2 THEN po_breakdown_id END,',') within group (order by po_breakdown_id) AS yarn_order_id,
				listagg(CASE WHEN entry_form in (2,22) and trans_type=1 THEN po_breakdown_id END,',') within group (order by po_breakdown_id) AS grey_order_id,
				listagg(CASE WHEN entry_form='9' and trans_type=4 THEN po_breakdown_id END,',') within group (order by po_breakdown_id) AS yarn_return_order_id 
				from order_wise_pro_details where trans_id<>0 and quantity>0 and entry_form in (2,3,9,22) and status_active=1 and is_deleted=0 group by trans_id,entry_form,trans_type");
		}
							 
		foreach($datapropArray as $row)
		{
			$order_nos_array[$row[csf('trans_id')]]['yarn_issue']=$row[csf('yarn_order_id')];
			$order_nos_array[$row[csf('trans_id')]]['grey_recv']=$row[csf('grey_order_id')];
			$order_nos_array[$row[csf('trans_id')]]['yarn_return']=$row[csf('yarn_return_order_id')];
		}	
	
		?>
         <div>
            <table width="2117" cellpadding="0" cellspacing="0" id="caption"><!-- style="visibility:hidden; border:none"-->
                <tr>
                   <td align="center" width="100%" colspan="21" style="font-size:16px"><strong><? echo $company_arr[str_replace("'","",$cbo_company_name)]; ?></strong></td>
                </tr> 
                <tr>  
                   <td align="center" width="100%" colspan="21" style="font-size:16px"><strong><? echo $report_title; ?> (Challan Wise)</strong></td>
                </tr>  
                <tr> 
                   <td align="center" width="100%" colspan="21" style="font-size:16px"><strong><? echo "From ".change_date_format(str_replace("'","",$txt_date_from))." To ".change_date_format(str_replace("'","",$txt_date_to)); ?></strong></td>
                </tr>
            </table>
            <br />
            <table width="2117" cellpadding="0" cellspacing="0" border="1" rules="all" class="rpt_table">
                <thead>
                    <th width="40">SL</th>
                    <th width="80">Date</th>
                    <th width="125">Transaction Ref.</th>
                    <th width="115">Recv. Challan No</th>
                    <th width="115">Issue Challan No</th>
                    <th width="130">Booking/Reqsn. No</th>
                    <th width="80">Buyer</th>
                    <th width="130">Order Numbers</th>
                    <th width="90">Order Qnty.</th>
                    <th width="80">Brand</th>
                    <th width="150">Item Description</th>
                    <th width="80">Lot</th>
                    <th width="60">UOM</th>
                    <th width="100">Yarn Issued</th>
                    <th width="100">Returnable Qty.</th>
                    <th width="100">Fabric Received</th>
                    <th width="100">Reject Fabric Received</th>
                    <th width="100">Yarn Returned</th>
                    <th width="100">Reject Yarn Returned</th>
                    <th width="100">Balance</th> 
                    <th width="">Returnable Balanace</th>
                </thead>
            </table>
            <div style="width:2117px; overflow-y: scroll; max-height:380px;" id="scroll_body">
                <table width="2100" cellpadding="0" cellspacing="0" border="1" rules="all" class="rpt_table" id="table_body"> 
				<?
                $company_arr=return_library_array( "select id, company_name from lib_company", "id", "company_name");
                $supplier_arr=return_library_array( "select id, supplier_name from lib_supplier", "id", "supplier_name");
        
                if (str_replace("'","",$cbo_knitting_source)==0) $knitting_source_cond_party=""; else $knitting_source_cond_party=" and knit_dye_source=$cbo_knitting_source";
                if ($knitting_company=='') $knitting_company_cond_party=""; else  $knitting_company_cond_party=" and a.id in ($knitting_company)";
                if ($knitting_company=='') $knitting_company_cond_comp=""; else  $knitting_company_cond_comp=" and id in ($knitting_company)";
				if ($txt_knitting_com_id=="") $knitting_receive_company_cond_1=""; else $knitting_receive_company_cond_1=" and a.supplier_id in ($txt_knitting_com_id)";
				
                $knit_source=str_replace("'","",$cbo_knitting_source);
				
				$i=1; $k=1; $j=1; $balance=0; $tot_iss_qnty=0; $tot_recv_qnty=0; $tot_rej_qnty=0; $tot_ret_qnty=0; $tot_reject_yarn_qnty=0; $challan_array=array(); $party_array=array(); $receive_array=array();
				if (str_replace("'","",$txt_challan)=="") $issue_challan_cond=""; else $issue_challan_cond=" and a.yarn_issue_challan_no=$txt_challan";
				if ($from_date!='' && $to_date!='') $rec_date_cond=" and a.receive_date between '$from_date' and '$to_date'"; else $rec_date_cond="";
				$sql_rec="select a.recv_number, a.booking_no, a.buyer_id, a.receive_date, a.knitting_source, a.knitting_company, a.item_category, a.challan_no, a.yarn_issue_challan_no, b.id as trans_id, b.cons_uom,  b.brand_id, b.cons_quantity, b.return_qnty, b.cons_reject_qnty, c.product_name_details, c.lot from inv_receive_master a, inv_transaction b, product_details_master c where a.item_category in(1,13) and a.entry_form in(2,22) and a.company_id=$cbo_company_name and a.id=b.mst_id and b.item_category in(1,13) and b.transaction_type in(1) and b.prod_id=c.id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $knitting_receive_company_cond_1 $knit_source_cond_party_rec  $knit_company_cond_party_rec $issue_challan_cond $issue_purpose_cond $rec_date_cond order by a.knitting_company";//and a.knitting_source=$cbo_knitting_source and a.knitting_company=$party_name
				$sql_rec_result=sql_select($sql_rec);
				foreach($sql_rec_result as $row)
				{
					if($row[csf('item_category')]==13)
					{
						$all_po_id=explode(",",$order_nos_array[$row[csf('trans_id')]]['grey_recv']);
						$tot_recv_qnty+=$row[csf('cons_quantity')];
						$tot_rej_qnty+=$row[csf('cons_reject_qnty')];
						$balance=$balance-($row[csf('cons_quantity')]+$row[csf('cons_reject_qnty')]);
					}
					else
					{
						$all_po_id=explode(",",$order_nos_array[$row[csf('trans_id')]]['yarn_return']);
						$tot_ret_qnty+=$row[csf('cons_quantity')];
						$tot_reject_yarn_qnty+=$row[csf('cons_reject_qnty')];
						$balance=$balance-($row[csf('cons_quantity')]+$row[csf('cons_reject_qnty')]);
					}
					$order_nos=''; $order_qnty=0;
					foreach($all_po_id as $po_id)
					{
						if($order_nos=='') $order_nos=$po_arr[$po_id]['name']; else $order_nos.=",".$po_arr[$po_id]['name'];
						$order_qnty+=$po_arr[$po_id]['qnty'];
					}
					$tot_returnable_qnty+=$row[csf('return_qnty')];
					
					$receive_array[$row[csf('yarn_issue_challan_no')]].=change_date_format($row[csf('receive_date')],'dd-mm-yyyy')."_".$row[csf('recv_number')]."_".$row[csf('challan_no')]."_".$row[csf('yarn_issue_challan_no')]."_".$row[csf('booking_no')]."_".$row[csf('buyer_id')]."_".$order_nos."_".$order_qnty."_".$row[csf('brand_id')]."_".$row[csf('product_name_details')]."_".$row[csf('lot')]."_".$row[csf('cons_uom')]."_".$row[csf('cons_quantity')]."_".$row[csf('cons_reject_qnty')]."_".$row[csf('return_qnty')]."***";
				}
				//var_dump($receive_array);die;
				
				$receive_ret_array=array();
				if (str_replace("'","",$txt_challan)=="") $issue_challan_ret_cond=""; else $issue_challan_ret_cond=" and b.issue_challan_no=$txt_challan";
				if ($from_date!='' && $to_date!='') $ret_date_cond=" and a.receive_date between '$from_date' and '$to_date'"; else $ret_date_cond="";
				if ($txt_knitting_com_id=="") $knitting_receive_return_company_cond_1=""; else $knitting_receive_return_company_cond_1=" and a.supplier_id in ($txt_knitting_com_id)";
				
				$sql_return="select a.recv_number, a.booking_no, a.buyer_id, a.receive_date, a.knitting_source, a.knitting_company, a.item_category, a.challan_no, b.issue_challan_no, b.id as trans_id, b.cons_uom,  b.brand_id, b.cons_quantity, b.return_qnty, b.cons_reject_qnty, c.product_name_details, c.lot from inv_receive_master a, inv_transaction b, product_details_master c where a.item_category in(1,13) and a.entry_form in(9) and a.company_id=$cbo_company_name and a.id=b.mst_id and b.item_category in(1,13) and b.transaction_type in(4) and b.prod_id=c.id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $knitting_receive_return_company_cond_1 $knit_source_cond_party_rec  $knit_company_cond_party_rec  $ret_date_cond $issue_challan_ret_cond order by a.knitting_company";//and a.knitting_source=$cbo_knitting_source and a.knitting_company=$party_name
				
				$sql_return_result=sql_select($sql_return);
				foreach($sql_return_result as $row)
				{
					if($row[csf('item_category')]==13)
					{
						$all_po_id=explode(",",$order_nos_array[$row[csf('trans_id')]]['grey_recv']);
						$tot_recv_qnty+=$row[csf('cons_quantity')];
						$tot_rej_qnty+=$row[csf('cons_reject_qnty')];
						$balance=$balance-($row[csf('cons_quantity')]+$row[csf('cons_reject_qnty')]);
					}
					else
					{
						$all_po_id=explode(",",$order_nos_array[$row[csf('trans_id')]]['yarn_return']);
						$tot_ret_qnty+=$row[csf('cons_quantity')];
						$tot_reject_yarn_qnty+=$row[csf('cons_reject_qnty')];
						$balance=$balance-($row[csf('cons_quantity')]+$row[csf('cons_reject_qnty')]);
					}
					$order_nos=''; $order_qnty=0;
					foreach($all_po_id as $po_id)
					{
						if($order_nos=='') $order_nos=$po_arr[$po_id]['name']; else $order_nos.=",".$po_arr[$po_id]['name'];
						$order_qnty+=$po_arr[$po_id]['qnty'];
					}
					$tot_returnable_qnty+=$row[csf('return_qnty')];
					
					$receive_ret_array[$row[csf('issue_challan_no')]].=change_date_format($row[csf('receive_date')],'dd-mm-yyyy')."_".$row[csf('recv_number')]."_".$row[csf('challan_no')]."_".$row[csf('issue_challan_no')]."_".$row[csf('booking_no')]."_".$row[csf('buyer_id')]."_".$order_nos."_".$order_qnty."_".$row[csf('brand_id')]."_".$row[csf('product_name_details')]."_".$row[csf('lot')]."_".$row[csf('cons_uom')]."_".$row[csf('cons_quantity')]."_".$row[csf('cons_reject_qnty')]."_".$row[csf('return_qnty')]."***";
				}
				//var_dump($receive_ret_array);die;
				if ($knit_source==0) $knit_source_cond_party=""; else $knit_source_cond_party=" and a.knit_dye_source in ($knit_source)";
				if ($knitting_company=='') $knit_company_cond_party=""; else  $knit_company_cond_party=" and a.knit_dye_company in ($knitting_company)";
				if ($from_date!='' && $to_date!='') $iss_date_cond=" and a.issue_date between '$from_date' and '$to_date'"; else $iss_date_cond="";
				
				$sql="select a.issue_number, a.issue_number_prefix_num, a.buyer_id, a.knit_dye_company, a.knit_dye_source, a.booking_id, a.booking_no, a.issue_date, a.challan_no, a.issue_basis, b.id as trans_id, b.cons_uom, b.requisition_no, b.brand_id, b.cons_quantity as issue_qnty, b.return_qnty, c.product_name_details, c.lot from inv_issue_master a, inv_transaction b, product_details_master c where a.item_category=1 and a.entry_form=3 and a.company_id=$cbo_company_name and a.id=b.mst_id and b.item_category=1 and b.transaction_type=2 and b.prod_id=c.id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $knitting_company_cond_1 $knit_source_cond_party  $knit_company_cond_party $challan_cond $iss_date_cond order by a.knit_dye_company, a.issue_number_prefix_num";
				$result=sql_select($sql); $rec_dtls_array=array(); $rec_issue_challan_arr=array(); $ret_issue_challan_arr=array(); $challan_arr=array(); $trans_ret_array=array();
				foreach($result as $row)
				{
					if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
						
					if($row[csf('issue_basis')]==1)
						$booking_reqsn_no=$row[csf('booking_no')];
					else if($row[csf('issue_basis')]==3)
						$booking_reqsn_no=$row[csf('requisition_no')];
					else
						$booking_reqsn_no="&nbsp;";	
					
					$order_nos=''; $order_qnty=0;
					$all_po_id=explode(",",$order_nos_array[$row[csf('trans_id')]]['yarn_issue']);
					foreach($all_po_id as $po_id)
					{
						if($order_nos=='') $order_nos=$po_arr[$po_id]['name']; else $order_nos.=",".$po_arr[$po_id]['name'];
						$order_qnty+=$po_arr[$po_id]['qnty'];
					}

					//$trans_data_in=$row[csf('issue_number_prefix_num')]."_".$row[csf('booking_no')]."_".$row[csf('buyer_id')]."_".$order_nos."_".$row[csf('brand_id')]."_".$row[csf('product_name_details')]."_".$row[csf('lot')];
					//$rec_issue_challan_arr[]=$trans_data_in;
					
					if (!in_array( $row[csf("knit_dye_company")],$party_array) )
					{
						if($k!=1)
						{ 
							$dataArray=array_filter(explode("***",substr($receive_array[$iss_challan],0,-1)));
							foreach($dataArray as $key=>$val2)
							{
								if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
								$iss_chln_no=$value[3];
								$value=explode("_",$val2);
								$new_value=$value[3]."_".$value[4]."_".$value[5]."_".$value[6]."_".$value[8]."_".$value[9]."_".$value[10];
								if($iss_chln_no==$prev_challan_no)
								{
									if(!in_array($new_value,$rec_issue_challan_arr))
									{
										$rec_date=$value[0];
										$rec_no=$value[1];
										$rec_chln=$value[2];
										$iss_chln=$value[3];
										$rec_booking=$value[4];
										$rec_buyer=$value[5];
										$rec_order=$value[6];
										$rec_po_qty=$value[7];
										$rec_brand=$value[8];
										$rec_product=$value[9];
										$rec_lot=$value[10];
										$rec_uom=$value[11];
										$rec_qty=$value[12];
										$rec_rej_qty=$value[13];
										$rec_returnable_qty=$value[14];
										
										$balance_rec=$balance_rec+($ch_issue_qty_tot-($rec_qty));
										$balance_return=$rec_qty-$rec_returnable_qty;
										?>
										<tr bgcolor="<? echo $bgcolor;?>" onClick="change_color('tr<? echo $i;?>','<? echo $bgcolor;?>')" id="tr<? echo $i;?>">
											<td width="40"><? echo $i; ?></td>
											<td width="80" align="center"><? echo $rec_date; ?></td>
											<td width="125"><div style="word-wrap:break-word; width:125px"><? echo $rec_no; ?></div></td>
											<td width="115"><p><? echo $rec_chln; ?></p></td>
											<td width="115"><p><? echo $iss_chln; ?></td>
											<td width="130"><p>&nbsp;<? echo $rec_booking; ?></p></td>
											<td width="80"><p><? echo $buyer_arr[$rec_buyer]; ?>&nbsp;</p></td>
											<td width="130"><div style="word-wrap:break-word; width:130px"><? echo $rec_order; ?></div></td>
											<td width="90" align="right"><? echo number_format($rec_po_qty,0,'.',''); ?></td>
											<td width="80"><p>&nbsp;<? echo $brand_arr[$rec_brand]; ?></p></td>
											<td width="150"><div style="word-wrap:break-word; width:150px"><? echo $rec_product; ?></div></td>
											<td width="80"><p><? echo $rec_lot; ?></p></td>
											<td width="60" align="center"><? echo $unit_of_measurement[$rec_uom]; ?>&nbsp;</td>
											<td width="100" align="right">&nbsp;</td>
											<td width="100" align="right">&nbsp;</td>
											
											<td width="100" align="right"><? echo number_format($rec_qty,2,'.',''); ?></td>
											<td width="100" align="right"><? echo number_format($rec_rej_qty,2,'.',''); ?></td>
											<td width="100" align="right">&nbsp;</td>
											<td width="100" align="right">&nbsp;</td>
											<td width="100" align="right"><? //echo number_format($balance_rec,2,'.',''); ?>&nbsp;</td>
											<td align="right"><? //echo number_format($balance_return,2,'.',''); ?>&nbsp;</td>
										</tr>
										<?
										$i++;
										$ch_rec_qty+=$rec_qty;
										$ch_rec_returnable_qty+=$rec_returnable_qty;
										$ch_rec_rej_qty+=$rec_rej_qty;
										$ch_balance+=$balance;
										$ch_balance_return+=$balance_return;
										
										$party_rec_qty+=$rec_qty;
										$party_rec_returnable_qty+=$rec_returnable_qty;
										$party_rec_rej_qty+=$rec_rej_qty;
										$party_balance+=$balance;
										$party_balance_return+=$balance_return;
										
										$grand_rec_qty+=$rec_qty;
										$grand_rec_returnable_qty+=$rec_returnable_qty;
										$grand_rec_rej_qty+=$rec_rej_qty;
										$grand_balance+=$balance;
										$grand_balance_return+=$balance_return;
										
										$challan_arr[]=$new_value;
									}
								}
							}
							
							$dataArray_ret=array_filter(explode("***",substr($receive_ret_array[$iss_challan],0,-1)));
							foreach($dataArray_ret as $key=>$val_ret)
							{
								$value=explode("_",$val_ret);
								$iss_chlln_ret=$value[3];
								$new_ret_value=$value[3]."_".$value[4]."_".$value[5]."_".$value[6]."_".$value[8]."_".$value[9]."_".$value[10];
								if($iss_chlln_ret==$prev_challan_no)
								{
									if(!in_array($new_ret_value,$trans_ret_array))
									{
										if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
										
										$ret_date=$value[0];
										$ret_no=$value[1];
										$ret_chln=$value[2];
										$iss_chln=$value[3];
										$ret_booking=$value[4];
										$ret_buyer=$value[5];
										$ret_order=$value[6];
										$ret_po_qty=$value[7];
										$ret_brand=$value[8];
										$ret_product=$value[9];
										$ret_lot=$value[10];
										$ret_uom=$value[11];
										$ret_qty=$value[12];
										$ret_rej_qty=$value[13];
										$ret_returnable_qty=$value[14];
										
				
										$balance_ret=$balance_ret+($balance_issue-($ret_qty+$ret_rej_qty));
										$balance_return_ret=$ret_returnable_qty;
										?>
										<tr bgcolor="<? echo $bgcolor;?>" onClick="change_color('tr<? echo $i;?>','<? echo $bgcolor;?>')" id="tr<? echo $i;?>">
											<td width="40"><? echo $i; ?></td>
											<td width="80" align="center"><? echo $ret_date; ?></td>
											<td width="125"><div style="word-wrap:break-word; width:125px"><? echo $ret_no; ?></div></td>
											<td width="115"><p><? echo $ret_chln; ?></p></td>
											<td width="115"><p><? echo $iss_chln; ?></p></td>
											<td width="130"><p>&nbsp;<? echo $ret_booking; ?></p></td>
											<td width="80"><p><? echo $buyer_arr[$ret_buyer]; ?>&nbsp;</p></td>
											<td width="130"><div style="word-wrap:break-word; width:130px"><? echo $ret_order; ?></div></td>
											<td width="90" align="right"><? echo number_format($ret_po_qty,0,'.',''); ?></td>
											<td width="80"><p>&nbsp;<? echo $brand_arr[$ret_brand]; ?></p></td>
											<td width="150"><div style="word-wrap:break-word; width:150px"><? echo $ret_product; ?></div></td>
											<td width="80"><p><? echo $ret_lot; ?></p></td>
											<td width="60" align="center"><? echo $unit_of_measurement[$ret_uom]; ?>&nbsp;</td>
											<td width="100" align="right">&nbsp;</td>
											<td width="100" align="right">&nbsp;</td>
											
											<td width="100" align="right">&nbsp;</td>
											<td width="100" align="right">&nbsp;</td>
											<td width="100" align="right"><? echo number_format($ret_qty,2,'.',''); ?></td>
											<td width="100" align="right"><? echo number_format($ret_rej_qty,2,'.',''); ?></td>
											<td width="100" align="right"><? //echo number_format($balance_ret,2,'.',''); ?>&nbsp;</td>
											<td align="right"><? //echo number_format($balance_return_ret,2,'.',''); ?>&nbsp;</td>
										</tr>
										<?
										$i++;
										$ch_ret_qty+=$ret_qty;
										$ch_ret_returnable_qty+=$ret_returnable_qty;
										$ch_ret_rej_qty+=$ret_rej_qty;
										$ch_balance_ret+=$balance_ret;
										$ch_balance_return_ret+=$balance_return_ret;
										
										$party_ret_qty+=$ret_qty;
										$party_ret_returnable_qty+=$ret_returnable_qty;
										$party_ret_rej_qty+=$ret_rej_qty;
										$party_balance_ret+=$balance_ret;
										$party_balance_return_ret+=$balance_return_ret;
										
										$grand_ret_qty+=$ret_qty;
										$grand_ret_returnable_qty+=$ret_returnable_qty;
										$grand_ret_rej_qty+=$ret_rej_qty;
										$grand_balance_ret+=$balance_ret;
										$grand_balance_return_ret+=$balance_return_ret;
										
										$trans_ret_array[]=$new_ret_value;
									}
								}
							}
							$ch_balance=$ch_issue_qty_tot-($ch_rec_qty+$ch_rec_rej_qty+$ch_ret_qty+$ch_ret_rej_qty);
							$ch_balance_returnable=$ch_returnable_qnty_tot-($ch_ret_qty+$ch_ret_rej_qty);
							
							$party_balance=$party_issue_qty_tot-($party_rec_qty+$party_rec_rej_qty+$party_ret_qty+$party_ret_rej_qty);
							$party_balance_returnable=$party_returnable_qnty_tot-($party_ret_qty+$party_ret_rej_qty);
						
						?>
							<tr class="tbl_bottom">
								<td colspan="13" align="right"><b>Challan Total</b></td>
								<td align="right"><? echo number_format($ch_issue_qty_tot,2,'.',''); ?>&nbsp;</td>
								<td align="right"><? echo number_format($ch_returnable_qnty_tot,2,'.',''); ?>&nbsp;</td>
								<td align="right"><? echo number_format($ch_rec_qty,2,'.',''); ?>&nbsp;</td>
								<td align="right"><? echo number_format($ch_rec_rej_qty,2,'.',''); ?>&nbsp;</td>
								<td align="right"><? echo number_format($ch_ret_qty,2,'.',''); ?>&nbsp;</td>
								<td align="right"><? echo number_format($ch_ret_rej_qty,2,'.',''); ?>&nbsp;</td>
								<td align="right"><? echo number_format($ch_balance,2,'.',''); ?>&nbsp;</td>
								<td align="right"><? echo number_format($ch_balance_returnable,2,'.',''); ?>&nbsp;</td>
							</tr>
							<tr class="tbl_bottom">
								<td colspan="13" align="right">Party Total</td>
								<td align="right"><? echo number_format($party_issue_qty_tot,2); ?></td>
								<td align="right"><? echo number_format($party_returnable_qnty_tot,2); ?></td>
								<td align="right"><? echo number_format($party_rec_qty,2); ?></td>
								<td align="right"><? echo number_format($party_rec_rej_qty,2); ?></td>
								<td align="right"><? echo number_format($party_ret_qty,2); ?></td>
								<td align="right"><? echo number_format($party_ret_rej_qty,2); ?></td>
								<td align="right"><? echo number_format($party_balance,2); ?></td>
								<td align="right"><? echo number_format($party_balance_returnable,2); ?></td>
							</tr>
						<?
							//unset($po_qty_tot);
							unset($ch_issue_qty_tot);
							unset($ch_returnable_qnty_tot);
							unset($ch_rec_qty);
							unset($ch_rec_rej_qty);
							unset($ch_ret_qty);
							unset($ch_ret_rej_qty);
							unset($ch_balance);
							unset($ch_balance_returnable);
						
							
							unset($party_issue_qty_tot);
							unset($party_returnable_qnty_tot);
							unset($party_balance_qty_tot);
							unset($party_balance);
						}
						?>
							<tr bgcolor="#dddddd">
								<td colspan="21" align="left" ><b>Party Name: <? if ($row[csf("knit_dye_source")]==1) echo $company_arr[$row[csf("knit_dye_company")]]; else if ($row[csf("knit_dye_source")]==3) echo $supplier_arr[$row[csf("knit_dye_company")]]; ?></b></td>
							</tr>
						<?
						$party_array[$k]=$row[csf("knit_dye_company")];
						$k++;
					}
									
					if(!in_array($row[csf('issue_number_prefix_num')],$challan_array))
					{
						if($j!=1)
						{
							$dataArray=array_filter(explode("***",substr($receive_array[$iss_challan],0,-1)));
							foreach($dataArray as $key=>$val2)
							{
								if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
								
								$value=explode("_",$val2);
								$iss_chln_no=$value[3];
								$new_value=$value[3]."_".$value[4]."_".$value[5]."_".$value[6]."_".$value[8]."_".$value[9]."_".$value[10];
								if($iss_chln_no==$prev_challan_no)
								{
									if(!in_array($new_value,$rec_issue_challan_arr))
									{
										$rec_date=$value[0];
										$rec_no=$value[1];
										$rec_chln=$value[2];
										$iss_chln=$value[3];
										$rec_booking=$value[4];
										$rec_buyer=$value[5];
										$rec_order=$value[6];
										$rec_po_qty=$value[7];
										$rec_brand=$value[8];
										$rec_product=$value[9];
										$rec_lot=$value[10];
										$rec_uom=$value[11];
										$rec_qty=$value[12];
										$rec_rej_qty=$value[13];
										$rec_returnable_qty=$value[14];
										
										$balance_rec=$balance_rec+($ch_issue_qty_tot-($rec_qty));
										$balance_return=$rec_qty-$rec_returnable_qty;
										?>
										<tr bgcolor="<? echo $bgcolor;?>" onClick="change_color('tr<? echo $i;?>','<? echo $bgcolor;?>')" id="tr<? echo $i;?>">
											<td width="40"><? echo $i; ?></td>
											<td width="80" align="center"><? echo $rec_date; ?></td>
											<td width="125"><div style="word-wrap:break-word; width:125px"><? echo $rec_no; ?></div></td>
											<td width="115"><p><? echo $rec_chln; ?></p></td>
											<td width="115"><p><? echo $iss_chln; ?></td>
											<td width="130"><p>&nbsp;<? echo $rec_booking; ?></p></td>
											<td width="80"><p><? echo $buyer_arr[$rec_buyer]; ?>&nbsp;</p></td>
											<td width="130"><div style="word-wrap:break-word; width:130px"><? echo $rec_order; ?></div></td>
											<td width="90" align="right"><? echo number_format($rec_po_qty,0,'.',''); ?></td>
											<td width="80"><p>&nbsp;<? echo $brand_arr[$rec_brand]; ?></p></td>
											<td width="150"><div style="word-wrap:break-word; width:150px"><? echo $rec_product; ?></div></td>
											<td width="80"><p><? echo $rec_lot; ?></p></td>
											<td width="60" align="center"><? echo $unit_of_measurement[$rec_uom]; ?>&nbsp;</td>
											<td width="100" align="right">&nbsp;</td>
											<td width="100" align="right">&nbsp;</td>
											
											<td width="100" align="right"><? echo number_format($rec_qty,2,'.',''); ?></td>
											<td width="100" align="right"><? echo number_format($rec_rej_qty,2,'.',''); ?></td>
											<td width="100" align="right">&nbsp;</td>
											<td width="100" align="right">&nbsp;</td>
											<td width="100" align="right"><? //echo number_format($balance_rec,2,'.',''); ?>&nbsp;</td>
											<td align="right"><? //echo number_format($balance_return,2,'.',''); ?>&nbsp;</td>
										</tr>
										<?
										$i++;
										$ch_rec_qty+=$rec_qty;
										$ch_rec_returnable_qty+=$rec_returnable_qty;
										$ch_rec_rej_qty+=$rec_rej_qty;
										$ch_balance+=$balance;
										$ch_balance_return+=$balance_return;
										
										$party_rec_qty+=$rec_qty;
										$party_rec_returnable_qty+=$rec_returnable_qty;
										$party_rec_rej_qty+=$rec_rej_qty;
										$party_balance+=$balance;
										$party_balance_return+=$balance_return;
										
										$grand_rec_qty+=$rec_qty;
										$grand_rec_returnable_qty+=$rec_returnable_qty;
										$grand_rec_rej_qty+=$rec_rej_qty;
										$grand_balance+=$balance;
										$grand_balance_return+=$balance_return;
										$challan_arr[]=$new_value;
									}
								}
							}
							
							$dataArray_ret=array_filter(explode("***",substr($receive_ret_array[$iss_challan],0,-1)));
							foreach($dataArray_ret as $key=>$val_ret)
							{
								if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
								
								$value=explode("_",$val_ret);
								$iss_chlln=$value[3];
								$new_ret_value=$value[3]."_".$value[4]."_".$value[5]."_".$value[6]."_".$value[8]."_".$value[9]."_".$value[10];
								if($iss_chlln==$prev_challan_no)
								{
									if(!in_array($new_ret_value,$trans_ret_array))
									{
										$ret_date=$value[0];
										$ret_no=$value[1];
										$ret_chln=$value[2];
										$iss_chln=$value[3];
										$ret_booking=$value[4];
										$ret_buyer=$value[5];
										$ret_order=$value[6];
										$ret_po_qty=$value[7];
										$ret_brand=$value[8];
										$ret_product=$value[9];
										$ret_lot=$value[10];
										$ret_uom=$value[11];
										$ret_qty=$value[12];
										$ret_rej_qty=$value[13];
										$ret_returnable_qty=$value[14];
										
										$balance_ret=$balance_ret+($balance_issue-($ret_qty+$ret_rej_qty));
										$balance_return_ret=$ret_returnable_qty;
										?>
										<tr bgcolor="<? echo $bgcolor;?>" onClick="change_color('tr<? echo $i;?>','<? echo $bgcolor;?>')" id="tr<? echo $i;?>">
											<td width="40"><? echo $i; ?></td>
											<td width="80" align="center"><? echo $ret_date; ?></td>
											<td width="125"><div style="word-wrap:break-word; width:125px"><? echo $ret_no; ?></div></td>
											<td width="115"><p><? echo $ret_chln; ?></p></td>
											<td width="115"><p><? echo $iss_chln; ?></td>
											<td width="130"><p>&nbsp;<? echo $ret_booking; ?></p></td>
											<td width="80"><p><? echo $buyer_arr[$ret_buyer]; ?>&nbsp;</p></td>
											<td width="130"><div style="word-wrap:break-word; width:130px"><? echo $ret_order; ?></div></td>
											<td width="90" align="right"><? echo number_format($ret_po_qty,0,'.',''); ?></td>
											<td width="80"><p>&nbsp;<? echo $brand_arr[$ret_brand]; ?></p></td>
											<td width="150"><div style="word-wrap:break-word; width:150px"><? echo $ret_product; ?></div></td>
											<td width="80"><p><? echo $ret_lot; ?></p></td>
											<td width="60" align="center"><? echo $unit_of_measurement[$ret_uom]; ?>&nbsp;</td>
											<td width="100" align="right">&nbsp;</td>
											<td width="100" align="right">&nbsp;</td>
											
											<td width="100" align="right">&nbsp;</td>
											<td width="100" align="right">&nbsp;</td>
											<td width="100" align="right"><? echo number_format($ret_qty,2,'.',''); ?></td>
											<td width="100" align="right"><? echo number_format($ret_rej_qty,2,'.',''); ?></td>
											<td width="100" align="right"><? //echo number_format($balance_ret,2,'.',''); ?>&nbsp;</td>
											<td align="right"><? //echo number_format($balance_return_ret,2,'.',''); ?>&nbsp;</td>
										</tr>
										<?
										$i++;
										$ch_ret_qty+=$ret_qty;
										$ch_ret_returnable_qty+=$ret_returnable_qty;
										$ch_ret_rej_qty+=$ret_rej_qty;
										$ch_balance_ret+=$balance_ret;
										$ch_balance_return_ret+=$balance_return_ret;
										
										$party_ret_qty+=$ret_qty;
										$party_ret_returnable_qty+=$ret_returnable_qty;
										$party_ret_rej_qty+=$ret_rej_qty;
										$party_balance_ret+=$balance_ret;
										$party_balance_return_ret+=$balance_return_ret;
										
										$grand_ret_qty+=$ret_qty;
										$grand_ret_returnable_qty+=$ret_returnable_qty;
										$grand_ret_rej_qty+=$ret_rej_qty;
										$grand_balance_ret+=$balance_ret;
										$grand_balance_return_ret+=$balance_return_ret;
										$trans_ret_array[]=$new_ret_value;
									}
								}
							}
							$ch_balance=$ch_issue_qty_tot-($ch_rec_qty+$ch_rec_rej_qty+$ch_ret_qty+$ch_ret_rej_qty);
							$ch_balance_returnable=$ch_returnable_qnty_tot-($ch_ret_qty+$ch_ret_rej_qty);
						?>
							<tr class="tbl_bottom">
								<td colspan="13" align="right"><b>Challan Total</b></td>
								<td align="right"><? echo number_format($ch_issue_qty_tot,2,'.',''); ?>&nbsp;</td>
								<td align="right"><? echo number_format($ch_returnable_qnty_tot,2,'.',''); ?>&nbsp;</td>
								<td align="right"><? echo number_format($ch_rec_qty,2,'.',''); ?>&nbsp;</td>
								<td align="right"><? echo number_format($ch_rec_rej_qty,2,'.',''); ?>&nbsp;</td>
								<td align="right"><? echo number_format($ch_ret_qty,2,'.',''); ?>&nbsp;</td>
								<td align="right"><? echo number_format($ch_ret_rej_qty,2,'.',''); ?>&nbsp;</td>
								
								<td align="right"><? echo number_format($ch_balance,2,'.',''); ?>&nbsp;</td>
								<td align="right"><? echo number_format($ch_balance_returnable,2,'.',''); ?>&nbsp;</td>
							</tr>
					<?
							//unset($po_qty_tot);
							unset($ch_issue_qty_tot);
							unset($ch_returnable_qnty_tot);
							unset($ch_rec_qty);
							unset($ch_rec_rej_qty);
							unset($ch_ret_qty);
							unset($ch_ret_rej_qty);
							unset($ch_balance);
							unset($ch_balance_returnable);
						}	
					?>
						<tr><td colspan="21" style="font-size:14px" bgcolor="#CCCCAA">&nbsp;<b> Challan No:&nbsp;&nbsp;<?php echo $row[csf('issue_number_prefix_num')]; ?></b></td></tr>
					<?	
						$challan_array[$j]=$row[csf('issue_number_prefix_num')];
						$j++;
					}
					$iss_challan=$row[csf('issue_number_prefix_num')];
				?>
					<tr bgcolor="<? echo $bgcolor;?>" onClick="change_color('tr<? echo $i;?>','<? echo $bgcolor;?>')" id="tr<? echo $i;?>">
						<td width="40"><? echo $i; ?></td>
						<td width="80" align="center"><? echo change_date_format($row[csf('issue_date')]); ?></td>
						<td width="125"><div style="word-wrap:break-word; width:125px"><? echo $row[csf('issue_number')]; ?></div></td>
						<td width="115">&nbsp;</td>
						<td width="115"><p>&nbsp;<? echo $row[csf('issue_number_prefix_num')]; ?></p></td>
						<td width="130"><p>&nbsp;<? echo $booking_reqsn_no; ?></p></td>
						<td width="80"><p><? echo $buyer_arr[$row[csf('buyer_id')]]; ?>&nbsp;</p></td>
						<td width="130"><div style="word-wrap:break-word; width:130px"><? echo $order_nos; ?></div></td>
						<td width="90" align="right"><? echo number_format($order_qnty,0,'.',''); ?></td>
						<td width="80"><p>&nbsp;<? echo $brand_arr[$row[csf('brand_id')]]; ?></p></td>
						<td width="150"><div style="word-wrap:break-word; width:150px"><? echo $row[csf('product_name_details')]; ?></div></td>
						<td width="80"><p><? echo $row[csf('lot')]; ?></p></td>
						<td width="60" align="center"><? echo $unit_of_measurement[$row[csf('cons_uom')]]; ?>&nbsp;</td>
						<td width="100" align="right"><? echo number_format($row[csf('issue_qnty')],2,'.',''); ?></td>
						<td width="100" align="right"><? echo number_format($row[csf('return_qnty')],2,'.',''); ?></td>
						<td width="100" align="right">&nbsp;</td>
						<td width="100" align="right">&nbsp;</td>
						<td width="100" align="right">&nbsp;</td>
						<td width="100" align="right">&nbsp;</td>
						<td width="100" align="right"><? $balance_issue=$balance_issue+$row[csf('issue_qnty')]; ?></td>
						<td align="right"><? //$return_balance=$row[csf('return_qnty')]; echo number_format($return_balance,2,'.',''); ?></td>
					</tr>
				<?
					$i++;
					$prev_challan_no=$row[csf('issue_number_prefix_num')];
					
					$ch_issue_qty_tot+=$row[csf('issue_qnty')];
					$ch_returnable_qnty_tot+=$row[csf('return_qnty')];
					$ch_balance_qty_tot+=$balance_issue;
					$ch_returnable_balance+=$row[csf('return_qnty')];
					
					$party_issue_qty_tot+=$row[csf('issue_qnty')];
					$party_returnable_qnty_tot+=$row[csf('return_qnty')];
					$party_balance_qty_tot+=$row[csf('issue_qnty')];
					$party_returnable_balance+=$row[csf('return_qnty')];
					
					$grand_issue_qty_tot+=$row[csf('issue_qnty')];
					$grand_returnable_qnty_tot+=$row[csf('return_qnty')];
					$grand_balance_qty_tot+=$row[csf('issue_qnty')];
					$grand_returnable_balance+=$row[csf('return_qnty')];
				}
				//$dataArray2=array_filter(explode("***",substr($receive_array[$row[csf('yarn_issue_challan_no')]],0,-1)));
				foreach($receive_array as $key=>$val)
				{
					$dataArray2=array_filter(explode("***",substr($val,0,-1)));
					foreach($dataArray2 as $id=>$val1)
					{
						$value=explode("_",$val1);
						$iss_chln_no=$value[3];
						$new_value=$value[3]."_".$value[4]."_".$value[5]."_".$value[6]."_".$value[8]."_".$value[9]."_".$value[10];
						if($iss_chln_no==$prev_challan_no)
						{
							if(!in_array($new_value,$rec_issue_challan_arr))
							{
		
							//if(in_array($value[3],$rec_issue_challan_arr))
							//{
								if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
								//echo $val1;
								$rec_date=$value[0];
								$rec_no=$value[1];
								$rec_chln=$value[2];
								$iss_chln=$value[3];
								$rec_booking=$value[4];
								$rec_buyer=$value[5];
								$rec_order=$value[6];
								$rec_po_qty=$value[7];
								$rec_brand=$value[8];
								$rec_product=$value[9];
								$rec_lot=$value[10];
								$rec_uom=$value[11];
								$rec_qty=$value[12];
								$rec_rej_qty=$value[13];
								$rec_returnable_qty=$value[14];
								
								$balance=$balance+($rec_qty+$rec_rej_qty);
								$balance_return=$rec_qty-$rec_returnable_qty;
								?>
								<tr bgcolor="<? echo $bgcolor;?>" onClick="change_color('tr<? echo $i;?>','<? echo $bgcolor;?>')" id="tr<? echo $i;?>">
									<td width="40"><? echo $i; ?></td>
									<td width="80" align="center"><? echo $rec_date; ?></td>
									<td width="125"><div style="word-wrap:break-word; width:125px"><? echo $rec_no; ?></div></td>
									<td width="115"><p><? echo $rec_chln; ?></p></td>
									<td width="115"><p><? echo $iss_chln; ?></td>
									<td width="130"><p>&nbsp;<? echo $rec_booking; ?></p></td>
									<td width="80"><p><? echo $buyer_arr[$rec_buyer]; ?>&nbsp;</p></td>
									<td width="130"><div style="word-wrap:break-word; width:130px"><? echo $rec_order; ?></div></td>
									<td width="90" align="right"><? echo number_format($rec_po_qty,0,'.',''); ?>&nbsp;</td>
									<td width="80"><p>&nbsp;<? echo $brand_arr[$rec_brand]; ?></p></td>
									<td width="150"><div style="word-wrap:break-word; width:150px"><? echo $rec_product; ?></div></td>
									<td width="80"><p><? echo $rec_lot; ?></p></td>
									<td width="60" align="center"><? echo $unit_of_measurement[$rec_uom]; ?>&nbsp;</td>
									<td width="100" align="right">&nbsp;</td>
									<td width="100" align="right">&nbsp;</td>
									
									<td width="100" align="right"><? echo number_format($rec_qty,2,'.',''); ?></td>
									<td width="100" align="right"><? echo number_format($rec_rej_qty,2,'.',''); ?></td>
									<td width="100" align="right">&nbsp;</td>
									<td width="100" align="right"><? //echo number_format($rec_rej_qty,2,'.',''); ?></td>
									<td width="100" align="right"><? //echo number_format($balance,2,'.',''); ?></td>
									<td align="right"><? //echo number_format($balance_return,2,'.',''); ?></td>
								</tr>
								<?
								$i++;
								$ch_rec_qty+=$rec_qty;
								$ch_rec_returnable_qty+=$rec_returnable_qty;
								$ch_rec_rej_qty+=$rec_rej_qty;
								$ch_balance+=$balance;
								$ch_balance_return+=$balance_return;
								
								$party_rec_qty+=$rec_qty;
								$party_rec_returnable_qty+=$rec_returnable_qty;
								$party_rec_rej_qty+=$rec_rej_qty;
								$party_balance+=$balance;
								$party_balance_return+=$balance_return;
								
								$grand_rec_qty+=$rec_qty;
								$grand_rec_returnable_qty+=$rec_returnable_qty;
								$grand_rec_rej_qty+=$rec_rej_qty;
								$grand_balance+=$balance;
								$grand_balance_return+=$balance_return;
								$challan_arr[]=$new_value;
							//}
						}
						}
					}
				}
				foreach($receive_ret_array as $key=>$val2)
				{
					$dataArray3=array_filter(explode("***",substr($val2,0,-1)));
					foreach($dataArray3 as $id=>$val3)
					{
						//$value=explode("_",$val3);
						$value_ret=explode("_",$val3);
						//echo $value_ret[3];
						//print_r($trans_ret_array);
						$iss_chlln_ret=$value_ret[3];
						$new_ret_value=$value_ret[3]."_".$value_ret[4]."_".$value_ret[5]."_".$value_ret[6]."_".$value_ret[8]."_".$value_ret[9]."_".$value_ret[10];
						if($iss_chlln_ret==$prev_challan_no)
						{
							if(!in_array($new_ret_value,$trans_ret_array))
							{
								if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
								//echo $value_ret[3];
								$ret_date=$value_ret[0];
								$ret_no=$value_ret[1];
								$ret_chln=$value_ret[2];
								$iss_chln=$value_ret[3];
								$ret_booking=$value_ret[4];
								$ret_buyer=$value_ret[5];
								$ret_order=$value_ret[6];
								$ret_po_qty=$value_ret[7];
								$ret_brand=$value_ret[8];
								$ret_product=$value_ret[9];
								$ret_lot=$value_ret[10];
								$ret_uom=$value_ret[11];
								$ret_qty=$value_ret[12];
								$ret_rej_qty=$value_ret[13];
								$ret_returnable_qty=$value_ret[14];
			
								$balance_ret=$balance_ret+($balance_issue-($ret_qty+$ret_rej_qty));
								$balance_return_ret=$ret_returnable_qty;
								?>
								<tr bgcolor="<? echo $bgcolor;?>" onClick="change_color('tr<? echo $i;?>','<? echo $bgcolor;?>')" id="tr<? echo $i;?>">
									<td width="40"><? echo $i; ?></td>
									<td width="80" align="center"><? echo $ret_date; ?></td>
									<td width="125"><div style="word-wrap:break-word; width:125px"><? echo $ret_no; ?></div></td>
									<td width="115"><p><? echo $ret_chln; ?></p></td>
									<td width="115"><p><? echo $iss_chln; ?></td>
									<td width="130"><p>&nbsp;<? echo $ret_booking; ?></p></td>
									<td width="80"><p><? echo $buyer_arr[$ret_buyer]; ?>&nbsp;</p></td>
									<td width="130"><div style="word-wrap:break-word; width:130px"><? echo $ret_order; ?></div></td>
									<td width="90" align="right"><? echo number_format($ret_po_qty,0,'.',''); ?></td>
									<td width="80"><p>&nbsp;<? echo $brand_arr[$ret_brand]; ?></p></td>
									<td width="150"><div style="word-wrap:break-word; width:150px"><? echo $ret_product; ?></div></td>
									<td width="80"><p><? echo $ret_lot; ?></p></td>
									<td width="60" align="center"><? echo $unit_of_measurement[$ret_uom]; ?>&nbsp;</td>
									<td width="100" align="right">&nbsp;</td>
									<td width="100" align="right">&nbsp;</td>
									
									<td width="100" align="right">&nbsp;</td>
									<td width="100" align="right">&nbsp;</td>
									<td width="100" align="right"><? echo number_format($ret_qty,2,'.',''); ?></td>
									<td width="100" align="right"><? echo number_format($ret_rej_qty,2,'.',''); ?></td>
									<td width="100" align="right"><? //echo number_format($balance_ret,2,'.',''); ?>&nbsp;</td>
									<td align="right"><? //echo number_format($balance_return_ret,2,'.',''); ?>&nbsp;</td>
								</tr>
								<?
								$i++;
								
								$ch_ret_qty+=$ret_qty;
								$ch_ret_returnable_qty+=$ret_returnable_qty;
								$ch_ret_rej_qty+=$ret_rej_qty;
								$ch_balance_ret+=$balance_ret;
								$ch_balance_return_ret+=$balance_return_ret;
								
								$party_ret_qty+=$ret_qty;
								$party_ret_returnable_qty+=$ret_returnable_qty;
								$party_ret_rej_qty+=$ret_rej_qty;
								$party_balance_ret+=$balance_ret;
								$party_balance_return_ret+=$balance_return_ret;
								
								$grand_ret_qty+=$ret_qty;
								$grand_ret_returnable_qty+=$ret_returnable_qty;
								$grand_ret_rej_qty+=$ret_rej_qty;
								$grand_balance_ret+=$balance_ret;
								$grand_balance_return_ret+=$balance_return_ret;
								$trans_ret_array[]=$new_ret_value;
							}
						}
					}
				}
				//var_dump($trans_ret_array);
				$ch_balance=$ch_issue_qty_tot-($ch_rec_qty+$ch_rec_rej_qty+$ch_ret_qty+$ch_ret_rej_qty);
				$ch_balance_returnable=$ch_returnable_qnty_tot-($ch_ret_qty+$ch_ret_rej_qty);
				
				$party_balance=$party_issue_qty_tot-($party_rec_qty+$party_rec_rej_qty+$party_ret_qty+$party_ret_rej_qty);
				$party_balance_returnable=$party_returnable_qnty_tot-($party_ret_qty+$party_ret_rej_qty);
		
			?>
				<tr class="tbl_bottom">
					<td colspan="13" align="right"><b>Challan Total</b></td>
					<td align="right"><? echo number_format($ch_issue_qty_tot,2,'.',''); ?>&nbsp;</td>
					<td align="right"><? echo number_format($ch_returnable_qnty_tot,2,'.',''); ?>&nbsp;</td>
					<td align="right"><? echo number_format($ch_rec_qty,2,'.',''); ?>&nbsp;</td>
					<td align="right"><? echo number_format($ch_rec_rej_qty,2,'.',''); ?>&nbsp;</td>
					<td align="right"><? echo number_format($ch_ret_qty,2,'.',''); ?>&nbsp;</td>
					<td align="right"><? echo number_format($ch_ret_rej_qty,2,'.',''); ?>&nbsp;</td>
					<td align="right"><? echo number_format($ch_balance,2,'.',''); ?>&nbsp;</td>
					<td align="right"><? echo number_format($ch_balance_returnable,2,'.',''); ?>&nbsp;</td>
				</tr>
				<tr class="tbl_bottom">
					<td colspan="13" align="right">Party Total</td>
					<td align="right"><? echo number_format($party_issue_qty_tot,2); ?></td>
					<td align="right"><? echo number_format($party_returnable_qnty_tot,2); ?></td>
					<td align="right"><? echo number_format($party_rec_qty,2); ?></td>
					<td align="right"><? echo number_format($party_rec_rej_qty,2); ?></td>
					<td align="right"><? echo number_format($party_ret_qty,2); ?></td>
					<td align="right"><? echo number_format($party_ret_rej_qty,2); ?></td>
					<td align="right"><? echo number_format($party_balance,2); ?></td>
					<td align="right"><? echo number_format($party_balance_returnable,2); ?></td>
				</tr>
				<tfoot>
					<th colspan="13" align="right">Total</th>
					<th align="right"><? echo number_format($grand_issue_qty_tot,2); ?></th>
					<th align="right"><? echo number_format($grand_returnable_qnty_tot,2); ?></th>
					<th align="right"><? echo number_format($grand_rec_qty,2); ?></th>
					<th align="right"><? echo number_format($grand_rec_rej_qty,2); ?></th>
					<th align="right"><? echo number_format($grand_ret_qty,2); ?></th>
					<th align="right"><? echo number_format($grand_ret_rej_qty,2); ?></th>
					<th align="right"><? 
						$grand_balance=$grand_issue_qty_tot-($grand_rec_qty+$grand_rec_rej_qty+$grand_ret_qty+$grand_ret_rej_qty);
						$grand_returnable_balance=$grand_returnable_qnty_tot-$grand_ret_qty;
						echo number_format($grand_balance,2); ?></th>
					<th align="right"><? echo number_format($grand_returnable_balance,2); ?></th>
				</tfoot>
			</table>       
		</div>
    	</div>      
    	<?
	}
	else if($type==4) //Returnable Button
	{
		//$all_party=explode(",",$knitting_company);
		$po_arr=array();
		$datapoArray=sql_select("select id, job_no_mst, po_number, po_quantity from wo_po_break_down");
		
		foreach($datapoArray as $row)
		{
			$po_arr[$row[csf('id')]]['job']=$row[csf('job_no_mst')];
			$po_arr[$row[csf('id')]]['name']=$row[csf('po_number')];
			$po_arr[$row[csf('id')]]['qnty']=$row[csf('po_quantity')];

		}


		if($db_type==0) $grpby_field="group by trans_id";
		if($db_type==2) $grpby_field="group by trans_id,entry_form,trans_type";
		else $grpby_field="";
		
		if (str_replace("'","",$txt_challan)=="") $challan_cond=""; else $challan_cond=" and a.issue_number_prefix_num=$txt_challan";
	
		$order_nos_array=array();
		if($db_type==0)
		{
			$datapropArray=sql_select("select trans_id,
				CASE WHEN entry_form='3' and trans_type=2 THEN group_concat(po_breakdown_id) END AS yarn_order_id,
				CASE WHEN entry_form='9' and trans_type=4 THEN group_concat(po_breakdown_id) END AS yarn_return_order_id 
				from order_wise_pro_details where trans_id<>0 and quantity>0 and entry_form in (3,9) and status_active=1 and is_deleted=0 group by trans_id");
		}
		else
		{
			$datapropArray=sql_select("select trans_id,
				listagg(CASE WHEN entry_form='3' and trans_type=2 THEN po_breakdown_id END,',') within group (order by po_breakdown_id) AS yarn_order_id,
				listagg(CASE WHEN entry_form='9' and trans_type=4 THEN po_breakdown_id END,',') within group (order by po_breakdown_id) AS yarn_return_order_id 
				from order_wise_pro_details where trans_id<>0 and quantity>0 and entry_form in (3,9) and status_active=1 and is_deleted=0 group by trans_id,entry_form,trans_type");
		}
							 
		foreach($datapropArray as $row) 
		{
			$order_nos_array[$row[csf('trans_id')]]['yarn_issue']=$row[csf('yarn_order_id')];
			$order_nos_array[$row[csf('trans_id')]]['yarn_return']=$row[csf('yarn_return_order_id')];
		}	
		?>
        <div>
            <table width="1480" cellpadding="0" cellspacing="0" id="caption" align="left"><!-- style="visibility:hidden; border:none"-->
                <tr>
                   <td align="center" width="100%" colspan="15" style="font-size:16px"><strong><? echo $company_arr[str_replace("'","",$cbo_company_name)]; ?></strong></td>
                </tr> 
                <tr>  
                   <td align="center" width="100%" colspan="15" style="font-size:16px"><strong><? echo $report_title; ?> (Returnable)</strong></td>
                </tr>  
                <tr> 
                   <td align="center" width="100%" colspan="15" style="font-size:16px"><strong><? echo "From ".change_date_format(str_replace("'","",$txt_date_from))." To ".change_date_format(str_replace("'","",$txt_date_to)); ?></strong></td>
                </tr>
            </table>
            <br />
            <table width="1590" cellpadding="0" cellspacing="0" border="1" rules="all" class="rpt_table" align="left">
                <thead>
                    <th width="40">SL</th>
                    <th width="70">Date</th>
                    <th width="110">Issue ID/Return ID.</th>
                    <th width="110">Return Ref.no.</th>
                    <th width="110">Job No.</th>
                    <th width="110">Order No</th>
                    <th width="80">Buyer</th>
                    <th width="80">Count</th>
                    <th width="80">Supplier</th>
                    <th width="80">Type</th>
                    <th width="80">Lot</th>
                    <th width="130">Booking/Reqsn. No</th>
                    <th width="100">Issue Qty.</th>
                    <th width="100">Returnable Qty.</th>
                    <th width="100">Returned Qty</th>
                    <th width="100">Reject Qty</th>
                    <th width="">Returnable Balanace</th>
                </thead>
            </table>
            <div style="width:1610px; overflow-y: scroll; max-height:380px;" id="scroll_body">
                <table width="1592" cellpadding="0" cellspacing="0" border="1" rules="all" class="rpt_table" id="table_body">
				<?
				$company_arr=return_library_array("select id, company_name from lib_company", "id", "company_name");
				$supplier_arr=return_library_array("select id, short_name from lib_supplier", "id", "short_name");
				$count_arr=return_library_array("select id, yarn_count from lib_yarn_count", "id", "yarn_count");
				if (str_replace("'","",$cbo_knitting_source)==0) $knitting_source_cond_party=""; else $knitting_source_cond_party=" and knit_dye_source=$cbo_knitting_source";
				if ($knitting_company=='') $knitting_company_cond_party=""; else  $knitting_company_cond_party=" and a.id in ($knitting_company)";
				if ($knitting_company=='') $knitting_company_cond_comp=""; else  $knitting_company_cond_comp=" and id in ($knitting_company)";
				$knit_source=str_replace("'","",$cbo_knitting_source);
				if ($knit_source==0) $knit_source_cond_party=""; else $knit_source_cond_party=" and a.knit_dye_source in ($knit_source)";
				if ($knitting_company=='') $knit_company_cond_party=""; else  $knit_company_cond_party=" and a.knit_dye_company in ($knitting_company)";
				if ($from_date!='' && $to_date!='') $iss_date_cond=" and a.issue_date between '$from_date' and '$to_date'"; else $iss_date_cond="";
				
				/*$sql_ref=sql_select("select a.id as issue_id, a.issue_number
				 from inv_issue_master a, inv_transaction b, product_details_master c 
				 where a.id=b.mst_id and b.prod_id=c.id and a.item_category=1 and a.entry_form=3 and a.company_id=$cbo_company_name and  b.item_category=1 and b.transaction_type=2  and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and b.return_qnty!=0 $knit_source_cond_party  $knit_company_cond_party $challan_cond $iss_date_cond");
				 foreach($sql_ref as $row)
				 {
					 $issue_id_arr[$row[csf("issue_id")]]=$row[csf("issue_id")];
				 }*/
				
				//var_dump($receive_ret_array);die;
				
				
				$sql="select a.id as issue_id, a.issue_number, a.issue_number_prefix_num, a.issue_basis, a.issue_purpose, a.buyer_id, a.knit_dye_company, a.knit_dye_source, a.booking_id, a.booking_no, a.issue_date, b.id as trans_id, b.requisition_no, b.supplier_id, b.cons_quantity as issue_qnty, b.return_qnty, c.yarn_count_id, c.yarn_type, c.lot 
				from inv_issue_master a, inv_transaction b, product_details_master c 
				where a.item_category=1 and a.entry_form=3 and a.company_id=$cbo_company_name and a.id=b.mst_id and  b.item_category=1 and b.transaction_type=2 and b.prod_id=c.id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and b.return_qnty!=0 $knit_source_cond_party $knit_company_cond_party $challan_cond $iss_date_cond $issue_purpose_cond 
				order by a.knit_dye_company, a.issue_number_prefix_num";
				//echo $sql;
				$result=sql_select($sql);
				$all_issue_data=array();
				foreach($result as $row)
				{
					$all_po_id=explode(",",$order_nos_array[$row[csf('trans_id')]]['yarn_issue']);
					$all_job_no=""; $order_nos=''; $order_qnty=0; $all_return_ref='';
					foreach($all_po_id as $po_id)
					{
						if($order_nos=='') $order_nos=$po_arr[$po_id]['name']; else $order_nos.=",".$po_arr[$po_id]['name'];
						if($all_job_no=='') $all_job_no=$po_arr[$po_id]['job']; else $all_job_no.=",".$po_arr[$po_id]['job'];
						$order_qnty+=$po_arr[$po_id]['qnty'];
					}
					//var_dump($all_job_no);//die;
					$job_no=implode(",",array_unique(explode(",",$all_job_no)));
					$issue_id_arr[$row[csf("issue_id")]]=$row[csf("issue_id")];
					$issue_id_num_arr[$row[csf("issue_id")]]=$row[csf("issue_number")];
					$all_issue_data[$row[csf("knit_dye_company")]][$row[csf("issue_number_prefix_num")]][$row[csf("trans_id")]]["issue_number"]=$row[csf("issue_number")];
					$all_issue_data[$row[csf("knit_dye_company")]][$row[csf("issue_number_prefix_num")]][$row[csf("trans_id")]]["issue_number_prefix_num"]=$row[csf("issue_number_prefix_num")];
					$all_issue_data[$row[csf("knit_dye_company")]][$row[csf("issue_number_prefix_num")]][$row[csf("trans_id")]]["issue_purpose"]=$row[csf("issue_purpose")];
					$all_issue_data[$row[csf("knit_dye_company")]][$row[csf("issue_number_prefix_num")]][$row[csf("trans_id")]]["buyer_id"]=$row[csf("buyer_id")];
					$all_issue_data[$row[csf("knit_dye_company")]][$row[csf("issue_number_prefix_num")]][$row[csf("trans_id")]]["knit_dye_company"]=$row[csf("knit_dye_company")];
					$all_issue_data[$row[csf("knit_dye_company")]][$row[csf("issue_number_prefix_num")]][$row[csf("trans_id")]]["knit_dye_source"]=$row[csf("knit_dye_source")];
					if($row[csf("issue_basis")]==1 || $row[csf("issue_basis")]==4)
					{
						$all_issue_data[$row[csf("knit_dye_company")]][$row[csf("issue_number_prefix_num")]][$row[csf("trans_id")]]["booking_id"]=$row[csf("booking_id")];
						$all_issue_data[$row[csf("knit_dye_company")]][$row[csf("issue_number_prefix_num")]][$row[csf("trans_id")]]["booking_no"]=$row[csf("booking_no")];
					}
					else
					{
						$all_issue_data[$row[csf("knit_dye_company")]][$row[csf("issue_number_prefix_num")]][$row[csf("trans_id")]]["booking_id"]=$row[csf("requisition_no")];
						$all_issue_data[$row[csf("knit_dye_company")]][$row[csf("issue_number_prefix_num")]][$row[csf("trans_id")]]["booking_no"]=$row[csf("requisition_no")];
					}
					
					$all_issue_data[$row[csf("knit_dye_company")]][$row[csf("issue_number_prefix_num")]][$row[csf("trans_id")]]["issue_date"]=$row[csf("issue_date")];
					$all_issue_data[$row[csf("knit_dye_company")]][$row[csf("issue_number_prefix_num")]][$row[csf("trans_id")]]["issue_basis"]=$row[csf("issue_basis")];
					$all_issue_data[$row[csf("knit_dye_company")]][$row[csf("issue_number_prefix_num")]][$row[csf("trans_id")]]["trans_id"]=$row[csf("trans_id")];
					$all_issue_data[$row[csf("knit_dye_company")]][$row[csf("issue_number_prefix_num")]][$row[csf("trans_id")]]["issue_qnty"]=$row[csf("issue_qnty")];
					$all_issue_data[$row[csf("knit_dye_company")]][$row[csf("issue_number_prefix_num")]][$row[csf("trans_id")]]["returnable_qnty"]=$row[csf("return_qnty")];
					$all_issue_data[$row[csf("knit_dye_company")]][$row[csf("issue_number_prefix_num")]][$row[csf("trans_id")]]["yarn_count_id"]=$row[csf("yarn_count_id")];
					$all_issue_data[$row[csf("knit_dye_company")]][$row[csf("issue_number_prefix_num")]][$row[csf("trans_id")]]["yarn_type"]=$row[csf("yarn_type")];
					$all_issue_data[$row[csf("knit_dye_company")]][$row[csf("issue_number_prefix_num")]][$row[csf("trans_id")]]["lot"]=$row[csf("lot")];
					$all_issue_data[$row[csf("knit_dye_company")]][$row[csf("issue_number_prefix_num")]][$row[csf("trans_id")]]["job_no"]=$job_no;
					$all_issue_data[$row[csf("knit_dye_company")]][$row[csf("issue_number_prefix_num")]][$row[csf("trans_id")]]["order_nos"]=$order_nos;
					$all_issue_data[$row[csf("knit_dye_company")]][$row[csf("issue_number_prefix_num")]][$row[csf("trans_id")]]["type"]="2";
					
					$issue_purpose[$row[csf("issue_number_prefix_num")]]=$row[csf("issue_purpose")];
				}

				$receive_ret_array=array();
				 $sql_return="select a.recv_number, a.knitting_source as knit_dye_source, a.knitting_company as knit_dye_company, a.booking_no, a.buyer_id, a.receive_date,a.recv_number, a.item_category, b.issue_challan_no as issue_number_prefix_num, b.issue_id, b.id as trans_id, c.supplier_id, b.cons_quantity, b.return_qnty, b.cons_reject_qnty, c.yarn_count_id, c.yarn_type, c.lot
				from inv_receive_master a, inv_transaction b, product_details_master c 
				where a.id=b.mst_id and b.prod_id=c.id and  a.entry_form=9 and a.company_id=$cbo_company_name and  b.item_category=1 and b.transaction_type=4 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0";
				
				/*$p=1;
				if(!empty($issue_id_arr))
				{
					$issue_id_arr_chank=array_chunk($issue_id_arr,999);
					foreach($issue_id_arr_chank as $issue_id_ar)
					{
						if($p==1) $sql_return .=" and (b.issue_id in(".implode(',',$issue_id_ar).")"; else $sql_return .=" or b.issue_id in(".implode(',',$issue_id_ar).")";
						$p++;
					}
					$sql_return .=" )"; 
				}*/
				
				//echo $sql_return;//die;
				
				$sql_return_result=sql_select($sql_return);
				//echo count($sql_return_result);
				foreach($sql_return_result as $row)
				{
					if($issue_id_arr[$row[csf("issue_id")]]!="")
					{
						$all_po_id=explode(",",$order_nos_array[$row[csf('trans_id')]]['yarn_return']);
						$all_job_no=""; $order_nos=''; $order_qnty=0; $return_ref_no='';
						foreach($all_po_id as $po_id)
						{
							if($order_nos=='') $order_nos=$po_arr[$po_id]['name']; else $order_nos.=",".$po_arr[$po_id]['name'];
							if($all_job_no=='') $all_job_no=$po_arr[$po_id]['job']; else $all_job_no.=",".$po_arr[$po_id]['job'];
							$order_qnty+=$po_arr[$po_id]['qnty'];
						}
						$job_no=implode(",",array_unique(explode(",",$all_job_no)));
                        $return_ref=implode(",",array_unique(explode(",",$return_ref_no)));

						$receive_ret_array[$row[csf('issue_challan_no')]].=change_date_format($row[csf('receive_date')],'dd-mm-yyyy')."_".$row[csf('booking_no')]."_".$row[csf('buyer_id')]."_".$order_nos."_".$order_qnty."_".$row[csf('yarn_count_id')]."_".$row[csf('supplier_id')]."_".$row[csf('yarn_type')]."_".$row[csf('lot')]."_".$row[csf('cons_quantity')]."_".$row[csf('cons_reject_qnty')]."_".$row[csf('return_qnty')]."_".$job_no."_".$row[csf('recv_number')]."***";
						
						$all_issue_data[$row[csf("knit_dye_company")]][$row[csf("issue_number_prefix_num")]][$row[csf("trans_id")]]["issue_number"]=$row[csf("recv_number")];
						$all_issue_data[$row[csf("knit_dye_company")]][$row[csf("issue_number_prefix_num")]][$row[csf("trans_id")]]["issue_challan_no"]=$row[csf("issue_challan_no")];
						$all_issue_data[$row[csf("knit_dye_company")]][$row[csf("issue_number_prefix_num")]][$row[csf("trans_id")]]["challan_issue_id"]=$row[csf("issue_id")];
						$all_issue_data[$row[csf("knit_dye_company")]][$row[csf("issue_number_prefix_num")]][$row[csf("trans_id")]]["knit_dye_company"]=$row[csf("knit_dye_company")];
						$all_issue_data[$row[csf("knit_dye_company")]][$row[csf("issue_number_prefix_num")]][$row[csf("trans_id")]]["knit_dye_company"]=$row[csf("knit_dye_company")];
						$all_issue_data[$row[csf("knit_dye_company")]][$row[csf("issue_number_prefix_num")]][$row[csf("trans_id")]]["knit_dye_source"]=$row[csf("knit_dye_source")];
						$all_issue_data[$row[csf("knit_dye_company")]][$row[csf("issue_number_prefix_num")]][$row[csf("trans_id")]]["booking_no"]=$row[csf("booking_no")];
						$all_issue_data[$row[csf("knit_dye_company")]][$row[csf("issue_number_prefix_num")]][$row[csf("trans_id")]]["buyer_id"]=$row[csf("buyer_id")];
						$all_issue_data[$row[csf("knit_dye_company")]][$row[csf("issue_number_prefix_num")]][$row[csf("trans_id")]]["issue_date"]=$row[csf("receive_date")];
						$all_issue_data[$row[csf("knit_dye_company")]][$row[csf("issue_number_prefix_num")]][$row[csf("trans_id")]]["issue_number_prefix_num"]=$row[csf("recv_number")];
						$all_issue_data[$row[csf("knit_dye_company")]][$row[csf("issue_number_prefix_num")]][$row[csf("trans_id")]]["supplier_id"]=$row[csf("supplier_id")];
						$all_issue_data[$row[csf("knit_dye_company")]][$row[csf("issue_number_prefix_num")]][$row[csf("trans_id")]]["return_qnty"]=$row[csf("cons_quantity")];
						$all_issue_data[$row[csf("knit_dye_company")]][$row[csf("issue_number_prefix_num")]][$row[csf("trans_id")]]["cons_reject_qnty"]=$row[csf("cons_reject_qnty")];
						$all_issue_data[$row[csf("knit_dye_company")]][$row[csf("issue_number_prefix_num")]][$row[csf("trans_id")]]["yarn_count_id"]=$row[csf("yarn_count_id")];
						$all_issue_data[$row[csf("knit_dye_company")]][$row[csf("issue_number_prefix_num")]][$row[csf("trans_id")]]["yarn_type"]=$row[csf("yarn_type")];
						$all_issue_data[$row[csf("knit_dye_company")]][$row[csf("issue_number_prefix_num")]][$row[csf("trans_id")]]["lot"]=$row[csf("lot")];
						$all_issue_data[$row[csf("knit_dye_company")]][$row[csf("issue_number_prefix_num")]][$row[csf("trans_id")]]["job_no"]=$job_no;
						$all_issue_data[$row[csf("knit_dye_company")]][$row[csf("issue_number_prefix_num")]][$row[csf("trans_id")]]["order_nos"]=$order_nos;
						$all_issue_data[$row[csf("knit_dye_company")]][$row[csf("issue_number_prefix_num")]][$row[csf("trans_id")]]["type"]="4";
					}
					
				}


				$i=1; $k=1; $j=1; $balance=0; $tot_iss_qnty=0; $tot_recv_qnty=0; $tot_rej_qnty=0; $tot_ret_qnty=0; $tot_reject_yarn_qnty=0; $challan_array=array(); $party_array=array(); 
				foreach($all_issue_data as $knit_company=>$knit_company_data)
				{
					?>
                    <tr bgcolor="#dddddd">
                        <td colspan="16" align="left" ><b>Party Name: <? echo $company_arr[$knit_company]; ?></b></td>
                    </tr>
                    <?
					foreach($knit_company_data as $issue_chalan_no=>$issue_chalan_no_data)
					{
						?>
                        <tr><td colspan="16" style="font-size:14px" bgcolor="#CCCCAA">&nbsp;<b> Challan No:&nbsp;&nbsp;<?php echo $issue_chalan_no.'; Issue Purpose:  '.$yarn_issue_purpose[$issue_purpose[$issue_chalan_no]]; ?></b></td></tr>
                        <?
						foreach($issue_chalan_no_data as $trans_id=>$value)
						{
							if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
							$return_balance=($value['returnable_qnty']-($value['return_qnty']+$value['cons_reject_qnty']));
							?>
                            <tr bgcolor="<? echo $bgcolor;?>" onClick="change_color('tr<? echo $i;?>','<? echo $bgcolor;?>')" id="tr<? echo $i;?>">
                                <td width="40"><? echo $i; ?></td>
                                <td width="70" align="center"><? echo change_date_format($value['issue_date']); ?></td>
                                <td width="110"><p><? echo $value['issue_number']; ?>&nbsp;</p></td>
                                <td width="110"><p><? echo $issue_id_num_arr[$value['challan_issue_id']]; ?></p></td>
                                <td width="110"><p><? echo $value['job_no']; ?>&nbsp;</p></td>
                                <td width="110"><p><? echo $value['order_nos']; ?>&nbsp;</p></td>
                                <td width="80"><p><? echo $buyer_arr[$value['buyer_id']]; ?>&nbsp;</p></td>
                                <td width="80"><p><? echo $count_arr[$value['yarn_count_id']]; ?>&nbsp;</p></td>
                                <td width="80"><p><? echo $supplier_arr[$value['supplier_id']]; ?>&nbsp;</p></td>
                                <td width="80"><p><? echo $yarn_type[$value['yarn_type']]; ?>&nbsp;</p></td>
                                <td width="80"><p><? echo $value['lot']; ?>&nbsp;</p></td>
                                <td width="130" align="center"><p><? echo $value['booking_no']; ?>&nbsp;</p></td>
                                <td width="100" align="right"><? echo number_format($value['issue_qnty'],2,'.',''); ?></td>
                                <td width="100" align="right"><? echo number_format($value['returnable_qnty'],2,'.',''); ?></td>
                                <td width="100" align="right"><? echo number_format($value['return_qnty'],2,'.',''); ?></td>
                                <td width="100" align="right"><? echo number_format($value['cons_reject_qnty'],2,'.',''); ?></td>
                                <td align="right"><? echo number_format($return_balance,2,'.',''); ?></td>
                            </tr>
							<?
							$i++;
							$challan_issue_qnty+=$value['issue_qnty'];
							$challan_returnable_qnty+=$value['returnable_qnty'];
							$challan_return_qnty+=$value['return_qnty'];
							$challan_cons_reject_qnty+=$value['cons_reject_qnty'];
							$challan_return_balance+=$return_balance;
							
							$party_issue_qnty+=$value['issue_qnty'];
							$party_returnable_qnty+=$value['returnable_qnty'];
							$party_return_qnty+=$value['return_qnty'];
							$party_cons_reject_qnty+=$value['cons_reject_qnty'];
							$party_return_balance+=$return_balance;
							
							$gt_issue_qnty+=$value['issue_qnty'];
							$gt_returnable_qnty+=$value['returnable_qnty'];
							$gt_return_qnty+=$value['return_qnty'];
							$gt_cons_reject_qnty+=$value['cons_reject_qnty'];
							$gt_return_balance+=$return_balance;
						}
						?>
                        <tr class="tbl_bottom">
                            <td colspan="11" align="right"><b>Challan Total</b></td>
                            <td align="right"><? echo number_format($challan_issue_qnty,2,'.',''); ?></td>
                            <td align="right"><? echo number_format($challan_returnable_qnty,2,'.',''); ?></td>
                            <td align="right"><? echo number_format($challan_return_qnty,2,'.',''); ?></td>
                            <td align="right"><? echo number_format($challan_cons_reject_qnty,2,'.',''); ?></td>
                            <td align="right"><? echo number_format($challan_return_balance,2,'.',''); ?></td>
                        </tr>
                        <?
						$challan_issue_qnty=$challan_returnable_qnty=$challan_return_qnty=$challan_cons_reject_qnty=$challan_return_balance=0;
					}
					
					?>
                    <tr class="tbl_bottom">
                        <td colspan="11" align="right">Party Total</td>
                        <td align="right"><? echo number_format($party_issue_qnty,2); ?></td>
                        <td align="right"><? echo number_format($party_returnable_qnty,2); ?></td>
                        <td align="right"><? echo number_format($party_return_qnty,2); ?></td>
                        <td align="right"><? echo number_format($party_cons_reject_qnty,2); ?></td>
                        <td align="right"><? echo number_format($party_return_balance,2); ?></td>
                    </tr>
                    <?
					$party_issue_qnty=$party_returnable_qnty=$party_return_qnty=$party_cons_reject_qnty=$party_return_balance=0;
				}
				
				?>
			<tfoot>
				<th colspan="12" align="right">Total</th>
				<th align="right"><? echo number_format($gt_issue_qnty,2); ?></th>
				<th align="right"><? echo number_format($gt_returnable_qnty,2); ?></th>
				<th align="right"><? echo number_format($gt_return_qnty,2); ?></th>
				<th align="right"><? echo number_format($gt_cons_reject_qnty,2); ?></th>
				<th align="right"><? echo number_format($gt_return_balance,2); ?></th>
			</tfoot>
			</table>       
			</div>
    	</div>      
    	<?
	}
	else if($type==6) // Summary2 Buttom with YD
	{
			?>
			<fieldset style="width:1270px">
				<table width="1260" cellpadding="0" cellspacing="0" id="caption"><!-- style="visibility:hidden; border:none"-->
					<tr>
					   <td align="center" width="100%" style="font-size:16px"><strong><? echo $company_arr[str_replace("'","",$cbo_company_name)]; ?></strong></td>
					</tr> 
					<tr>  
					   <td align="center" width="100%" style="font-size:16px"><strong><? echo $report_title; ?> (Summary)</strong></td>
					</tr>  
					<tr> 
					   <td align="center" width="100%" style="font-size:16px"><strong><? echo "From ".change_date_format(str_replace("'","",$txt_date_from))." To ".change_date_format(str_replace("'","",$txt_date_to)); ?></strong></td>
					</tr>
				</table>
				<br />
				<table width="1260" cellpadding="0" cellspacing="0" border="1" rules="all" class="rpt_table">
					<thead>
						<th width="30">SL</th>
						<th width="150">Party Name</th>
						<th width="60">UOM</th>
						<th width="100" title="Iss.-Rec.">Opening Balance</th>
						<th width="100">Yarn Issued</th>
						<th width="100">Fabric Received</th>
						<th width="100">Reject Fabric Received</th>
						<th width="100">DY/TW/ WX/RCon Rec.</th>
						<th width="100">Yarn Returned</th>
						<th width="100">Reject Yarn Returned</th>
						<th width="100">Balance</th>
						<th width="100">Process Loss</th>
						<th>After Process Loss Balance</th>
					</thead>
				</table>
				<div style="width:1260px; overflow-y: scroll; max-height:380px;" id="scroll_body">
					<table width="1240" cellpadding="0" cellspacing="0" border="1" rules="all" class="rpt_table"> 
					<?
					$party_data=array();
					$party_opening_arr=array();
					$yarnRcvIdArr = array();
					
					//for Out-bound Subcontract
					if (str_replace("'","",$cbo_knitting_source)==3)
					{
						if ($knitting_company=='')
							$party_cond="";
						else
							$party_cond="  and a.supplier_id in ($knitting_company)";
						
						if ($txt_knitting_com_id=='')
							$party_cond_2="";
						else
							$party_cond_2="  and a.supplier_id in ($txt_knitting_com_id)";
						
						$sql_yrec="select a.id, a.supplier_id, a.receive_date, a.ref_closing_status, sum(b.cons_quantity) as cons_quantity
						from inv_receive_master a, inv_transaction b where a.id=b.mst_id and a.company_id=$cbo_company_name and a.receive_purpose in (2,12,15,38) and a.item_category =1 and b.item_category =1 and a.entry_form=1 and b.transaction_type=1 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $party_cond $party_cond_2 group by a.id, a.supplier_id, a.receive_date, a.ref_closing_status"; //$knitting_source_rec_cond
						//echo $sql_yrec; die;
						$sql_yrec_res=sql_select($sql_yrec);
						foreach($sql_yrec_res as $rowyRec)
						{
							$trns_date=''; $date_frm=''; $date_to='';
							$trns_date=date('Y-m-d',strtotime($rowyRec[csf('receive_date')]));
							$date_frm=date('Y-m-d',strtotime($from_date));
							$date_to=date('Y-m-d',strtotime($to_date));
							
							if($trns_date<$date_frm)
							{
								$party_opening_arr[$rowyRec[csf('supplier_id')]]['yrOpening']+=$rowyRec[csf('cons_quantity')];
							}
							if($trns_date>=$date_frm && $trns_date<=$date_to)
							{
								$party_data[$rowyRec[csf('supplier_id')]]['yarn_rec']+=$rowyRec[csf('cons_quantity')];
								$yarnRcvIdArr[$rowyRec[csf('supplier_id')]][$rowyRec[csf('id')]] =$rowyRec[csf('id')];
							}
						}
						unset($sql_yrec_res);
					}
					//for Out-bound Subcontract end
	
					if ($knitting_company=='')
						$party_cond="";
					else
						$party_cond="  and a.knitting_company in ($knitting_company)";
						
					if ($txt_knitting_com_id=='')
						$party_cond_1="";
					else
						$party_cond_1="  and a.knitting_company in ($txt_knitting_com_id)";
						
					if (str_replace("'","",$cbo_knitting_source)==0)
						$knit_source_cond="";
					else
						$knit_source_cond=" and a.knitting_source=$cbo_knitting_source";
	
					//====
					$issue_qty_arr=array();
					//$sql_iss="select a.id, a.knit_dye_company, a.issue_date, sum(b.cons_quantity) as cons_quantity, sum(b.return_qnty) as return_qnty from inv_issue_master a, inv_transaction b where a.id=b.mst_id and a.item_category=1 and a.entry_form=3 and b.item_category=1 and b.transaction_type=2 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.company_id=".$cbo_company_name."  $knitting_company_cond_1 $knitting_source_cond $knitting_company_cond group by a.id, a.knit_dye_company, a.issue_date";
					$sql_iss="select a.id, a.knit_dye_company, a.issue_date, b.cons_quantity as cons_quantity, b.return_qnty as return_qnty from inv_issue_master a, inv_transaction b where a.id=b.mst_id and a.item_category=1 and a.entry_form=3 and b.item_category=1 and b.transaction_type=2 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.company_id=".$cbo_company_name." $knitting_company_cond_1 $knitting_source_cond $knitting_company_cond";
					//echo $sql_iss; die;
					$sql_iss_res=sql_select($sql_iss);
					$popupIssueIdArr = array();
					foreach($sql_iss_res as $rowIss)
					{
						$trns_date='';
						$date_frm='';
						$date_to='';
						$trns_date=date('Y-m-d',strtotime($rowIss[csf('issue_date')]));
						$date_frm=date('Y-m-d',strtotime($from_date));
						$date_to=date('Y-m-d',strtotime($to_date));
	
						if($trns_date<$date_frm)
						{
							$party_opening_arr[$rowIss[csf('knit_dye_company')]]['issOpening']+=$rowIss[csf('cons_quantity')];
						}
						if($trns_date>=$date_frm && $trns_date<=$date_to)
						{
							$popupIssueIdArr[$rowIss[csf('knit_dye_company')]][$rowIss[csf('id')]] = $rowIss[csf('id')];
							$party_data[$rowIss[csf('knit_dye_company')]]['issue_qnty'] += $rowIss[csf('cons_quantity')];
							$party_data[$rowIss[csf('knit_dye_company')]]['return_qnty'] += $rowIss[csf('return_qnty')];
						}
					}
					unset($sql_iss_res);
					//echo "<pre>";
					//print_r($party_data); die;
					
					//====
					$prog_production_sql="SELECT a.knitting_company, b.grey_receive_qnty as grey_receive_qnty, b.reject_fabric_receive as reject_fabric_receive, c.id as prog_no from inv_receive_master a, pro_grey_prod_entry_dtls b, ppl_planning_info_entry_dtls c 
					where a.id=b.mst_id and c.id=a.booking_id and a.is_deleted=0 and a.status_active=1 and a.entry_form=2 and a.receive_basis=2 and a.company_id=$cbo_company_name $knit_source_cond $party_cond $party_cond_1 and a.ref_closing_status=1
					order by c.id asc";
					$result_prog_production = sql_select($prog_production_sql);
					foreach($result_prog_production as $row)
					{
						if($dtls_check[$row[csf('prog_no')]][$row[csf('dtls_id')]]=="")
						{
							$prog_dataArr[$row[csf('knitting_company')]][$row[csf('prog_no')]]['refcls_frcv']+=$row[csf('grey_receive_qnty')];
							$prog_dataArr[$row[csf('knitting_company')]][$row[csf('prog_no')]]['refcls_frejrcv']+=$row[csf('reject_fabric_receive')];
						}
					}
	
					$sql_rec="select a.id, a.knitting_company, a.receive_date, a.receive_basis, a.ref_closing_status, a.entry_form,b.item_category,b.cons_quantity,b.return_qnty, b.cons_reject_qnty from inv_receive_master a, inv_transaction b where a.item_category in(1,13) and a.entry_form in(2,9,22,58) and a.company_id=$cbo_company_name and a.id=b.mst_id and b.item_category in(1,13) and b.transaction_type in(1,4) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $knit_source_cond $party_cond $party_cond_1 $issue_challan_cond order by a.knitting_company, a.receive_date ";
					//echo $sql_rec; die;
					$sql_rec_res=sql_select($sql_rec);
					$popupFabricReceiveIdArr = array();
					$fabRcvIdArr = array();
					$popupYarnReturnIdArr = array();
					foreach($sql_rec_res as $rowRec)
					{
						$trns_date=''; $date_frm=''; $date_to='';
						$trns_date=date('Y-m-d',strtotime($rowRec[csf('receive_date')]));
						$date_frm=date('Y-m-d',strtotime($from_date));
						$date_to=date('Y-m-d',strtotime($to_date));
						$item_category=$rowRec[csf('item_category')];
						if($trns_date<$date_frm)
						{
							$opening_balance_rec=0;
							$opening_balance_rec=$rowRec[csf('cons_quantity')]+$rowRec[csf('cons_reject_qnty')]+$rowRec[csf('return_qnty')];
							$party_opening_arr[$rowRec[csf('knitting_company')]]['recOpening']+=$opening_balance_rec;
						}
						//echo $trns_date.">=".$date_frm ."&&". $trns_date."<=".$date_to;
						if($trns_date>=$date_frm && $trns_date<=$date_to)
						{
							$fabRcvIdArr[$rowRec[csf('knitting_company')]][$rowRec[csf('id')]] = $rowRec[csf('id')];
							if($item_category==13)
							{
								$popupFabricReceiveIdArr[$rowRec[csf('knitting_company')]][$rowRec[csf('id')]] = $rowRec[csf('id')];							
								$party_data[$rowRec[csf('knitting_company')]]['fRec']+=$rowRec[csf('cons_quantity')];
	
								if($rowRec[csf('ref_closing_status')]==1) // knitting pland
								{
									$party_data[$rowRec[csf('knitting_company')]]['ref_closing_fbrcv']+=$rowRec[csf('cons_quantity')];
									$party_data[$rowRec[csf('knitting_company')]]['ref_closing_reject_fbrcv']+=$rowRec[csf('cons_reject_qnty')];
								}
							}
	
							if($rowRec[csf('entry_form')]==9)
							{
								$popupYarnReturnIdArr[$rowRec[csf('knitting_company')]][$rowRec[csf('id')]] = $rowRec[csf('id')];
								$party_data[$rowRec[csf('knitting_company')]]['ret_yarn']+=$rowRec[csf('cons_quantity')];
								$party_data[$rowRec[csf('knitting_company')]]['rej_yarn']+=$rowRec[csf('cons_reject_qnty')];
							}
							else
							{
								$party_data[$rowRec[csf('knitting_company')]]['rej_fab']+=$rowRec[csf('cons_reject_qnty')];
							}
	
							$party_data[$rowRec[csf('knitting_company')]]['ref_closing_status']=$rowRec[csf('ref_closing_status')];
							$party_data[$rowRec[csf('knitting_company')]]['return']+=$rowRec[csf('return_qnty')];
						}
					}
					//echo "<pre>";
					//print_r($popupYarnReturnIdArr);
					unset($sql_rec_res);
					
					//for reference close
					//if($trns_date>=$date_frm && $trns_date<=$date_to)
					$wo_date_cond="";
					if( $from_date != "" && $to_date != "" )
						$wo_date_cond = " and a.receive_date between '".$from_date."' and '".$to_date."'";

					$result_prog=sql_select("SELECT a.id AS ID, a.knitting_company AS KNIT_COMPANY, b.id AS DTLS_ID, b.grey_receive_qnty AS GREY_RCV_QTY, b.reject_fabric_receive AS REJECT_FAB_RCV, c.id AS PROG_NO FROM inv_receive_master a, pro_grey_prod_entry_dtls b, ppl_planning_info_entry_dtls c WHERE a.id=b.mst_id AND c.id=a.booking_id AND a.is_deleted=0 AND a.status_active=1 AND a.entry_form=2 AND a.receive_basis=2 AND a.ref_closing_status = 1 $knit_source_cond $party_cond $party_cond_1 $issue_challan_cond $wo_date_cond");
					$progIdArr = array();
					$refCloseDataArr = array();
					$greyRcvIdArr = array();
					foreach($result_prog as $row)
					{
						$progIdArr[$row['PROG_NO']] = $row['PROG_NO'];
						//$popupFabricReceiveIdArr[$row['KNIT_COMPANY']][$row['ID']] = $row['ID'];
						
						if($dtls_check[$row['KNIT_COMPANY']][$row['DTLS_ID']]=="")
						{
							$dtls_check[$row['KNIT_COMPANY']][$row['DTLS_ID']] = $row['DTLS_ID'];
							$refCloseDataArr[$row['KNIT_COMPANY']]['grey_receive_qnty'] += $row['GREY_RCV_QTY'];
							$refCloseDataArr[$row['KNIT_COMPANY']]['reject_fabric_receive'] += $row['REJECT_FAB_RCV'];
							$greyRcvIdArr[$row['KNIT_COMPANY']][$row['ID']] = $row['ID'];
						}
					}
					unset($result_prog);
					//echo "<pre>";
					//print_r($progIdArr);
					
					//for reference close requisition
					$req_sql = "SELECT c.requisition_no AS REQ_NO FROM ppl_planning_info_entry_mst a, ppl_planning_info_entry_dtls b, ppl_yarn_requisition_entry c WHERE a.id=b.mst_id AND b.id=c.knit_id AND a.status_active=1 AND a.is_deleted=0 AND b.status_active=1 AND b.is_deleted=0 AND c.status_active=1 AND c.is_deleted=0".where_con_using_array($progIdArr, '0', 'b.id');
					$req_result = sql_select($req_sql);
					$reqNoArr = array();
					foreach($req_result as $row)
					{
						$reqNoArr[$row['REQ_NO']] = $row['REQ_NO'];
					}
					unset($req_result);
					//echo "<pre>";
					//print_r($reqNoArr);
					
					//for reference close yarn issue
					$yarn_issue="SELECT a.id AS ID, a.knit_dye_company AS KNIT_COMPANY, b.cons_quantity AS CONS_QTY, b.cons_reject_qnty AS CONS_REJECT_QTY, b.requisition_no AS REQUISITION_NO, c.knit_id AS KNIT_ID FROM inv_issue_master a, inv_transaction b, ppl_yarn_requisition_entry c WHERE a.id=b.mst_id AND b.requisition_no=c.requisition_no AND a.entry_form=3 AND b.transaction_type=2 AND a.status_active=1 AND a.is_deleted=0 AND b.status_active=1 AND b.is_deleted=0 AND c.status_active=1 AND c.is_deleted=0 and a.company_id=".$cbo_company_name."  ".where_con_using_array($reqNoArr, '0', 'b.requisition_no').$$knitting_source_cond.$knitting_company_cond.$knitting_company_cond_1." GROUP BY a.id, a.knit_dye_company, b.cons_quantity, b.cons_reject_qnty, b.requisition_no, c.knit_id";
					//echo $yarn_issue;
					$yarn_result = sql_select($yarn_issue);
					$issueIdArr = array();
					$issueIdArrRef = array();
					foreach($yarn_result as $row)
					{
						$issueIdArr[$row['ID']] = $row['ID'];
						$issueIdArrRef[$row['KNIT_COMPANY']][$row['ID']] = $row['ID'];
						
						$refCloseDataArr[$row['KNIT_COMPANY']]['issue_qty'] += $row['CONS_QTY'];
						$refCloseDataArr[$row['KNIT_COMPANY']]['issue_reject_qty'] += $row['CONS_REJECT_QTY'];
						$refCloseDataArr[$row['KNIT_COMPANY']]['prog_no'][$row['KNIT_ID']]= $row['KNIT_ID'];
						$pogArr[$row['KNIT_ID']] = $row['KNIT_ID'];
						//$TEST[$row['KNIT_COMPANY']][$row['ID']]['issue_qty'] += $row['CONS_QTY'];
					}
					unset($yarn_result);
					//echo "<pre>";
					//print_r($refCloseDataArr);
					
					//for reference close yarn issue return
					$yarn_issue_ret="SELECT a.knitting_company AS KNIT_COMPANY, b.cons_quantity AS CONS_QTY, b.cons_reject_qnty AS CONS_REJECT_QTY, c.knit_id AS KNIT_ID FROM inv_receive_master a, inv_transaction b, ppl_yarn_requisition_entry c WHERE a.id=b.mst_id AND a.booking_id=c.requisition_no AND a.entry_form=9 AND b.transaction_type=4 AND b.item_category=1 AND a.status_active=1 AND a.is_deleted=0 AND b.status_active=1 AND b.is_deleted=0 AND c.status_active=1 AND c.is_deleted=0".where_con_using_array($issueIdArr, '0', 'a.issue_id').where_con_using_array($pogArr, '0', 'c.knit_id')." GROUP BY a.knitting_company, b.cons_quantity, b.cons_reject_qnty, c.knit_id";
					//echo $yarn_issue_ret;
					$yarn_ret_result = sql_select($yarn_issue_ret);
					foreach($yarn_ret_result as $row)
					{
						$refCloseDataArr[$row['KNIT_COMPANY']]['issue_return_qty'] += $row['CONS_QTY'];
						$refCloseDataArr[$row['KNIT_COMPANY']]['issue_reject_return_qty'] += $row['CONS_REJECT_QTY'];
						$refCloseDataArr[$row['KNIT_COMPANY']]['prog_no'][$row['KNIT_ID']]= $row['KNIT_ID'];
					}
					unset($yarn_ret_result);
					//for reference close end
	
					//---------------old---------------
					$i=1;
					foreach($party_data as $party_id=>$party_datas)
					{
						if ($i%2==0)
							$bgcolor="#E9F3FF";
						else
							$bgcolor="#FFFFFF";
							
						if(str_replace("'","",$cbo_knitting_source)==1)
							$knitting_party=$company_arr[$party_id];
						else if(str_replace("'","",$cbo_knitting_source)==3)
							$knitting_party=$supplier_arr[$party_id];
						else
							$knitting_party="&nbsp;";

						$opening_balance=0;
						$yarn_issue=0;
						$yarn_returnable_qty=0;
						$dy_tx_wx_rcon=0;
						$opening_balance= $party_opening_arr[$party_id]['issOpening']-($party_opening_arr[$party_id]['recOpening']+$party_opening_arr[$party_id]['yrOpening']);
	
						$yarn_issue=$party_datas['issue_qnty'];
						$yarn_returnable_qty=$party_datas['return_qnty'];
						
						$dy_tx_wx_rcon=$party_datas['yarn_rec'];
						$grey_receive_qnty=$party_datas['fRec'];
						$reject_fabric_receive=$party_datas['rej_fab'];
						
						$yarn_return_qnty=$party_datas['ret_yarn'];
						$yarn_return_reject_qnty=$party_datas['rej_yarn'];
						$returnable_balance=$yarn_returnable_qty-$yarn_return_qnty;
						
						$balance=($opening_balance+$yarn_issue)-($dy_tx_wx_rcon+$grey_receive_qnty+$reject_fabric_receive+$yarn_return_qnty+$yarn_return_reject_qnty);
						
						//for reference close
						//$process_loss = $yarn_issue-($yarn_return_qnty+$ref_closing_grey_fbrrcv+$ref_closing_grey_reject_fbrcv);
						//echo $refCloseDataArr[$party_id]['issue_qty'].'='.$refCloseDataArr[$party_id]['issue_return_qty'].'='.$refCloseDataArr[$party_id]['grey_receive_qnty'].'='.$refCloseDataArr[$party_id]['reject_fabric_receive'];						
						$process_loss = $refCloseDataArr[$party_id]['issue_qty']-($refCloseDataArr[$party_id]['issue_return_qty']+$refCloseDataArr[$party_id]['grey_receive_qnty']+$refCloseDataArr[$party_id]['reject_fabric_receive']);
						$balance_after_process_loss = $balance-$process_loss;
						
						/*$refCloseDataArr[$party_id]['issue_qty'] = 0;
						$refCloseDataArr[$party_id]['issue_return_qty'] = 0;
						$refCloseDataArr[$party_id]['grey_receive_qnty'] = 0;
						$refCloseDataArr[$party_id]['reject_fabric_receive'] = 0;*/
						
						//for popup
						$prog_no = implode(',', $refCloseDataArr[$party_id]['prog_no']);
						$popupIssueId = implode(',', $popupIssueIdArr[$party_id]);
						//$popupYarnReturnId = implode(',', $popupYarnReturnIdArr[$party_id]);
						$popupYarnReturnId = implode(',', $popupYarnReturnIdArr[$party_id]);
						$popupRejectYarnReturnId = implode(',', $popupYarnReturnIdArr[$party_id]);
						$popupFabricReceiveId = implode(',', $popupFabricReceiveIdArr[$party_id]);
						$popupRejectFabricReceiveId = implode(',', $popupFabricReceiveIdArr[$party_id]);
						$popupYarnRcvId = implode(',', $yarnRcvIdArr[$party_id]);
						$popupFabRcvId = implode(',', $fabRcvIdArr[$party_id]);
						$popupGreyRcvId = implode(',', $greyRcvIdArr[$party_id]);
						//ref
						$popupIssueIdRef = implode(',', $issueIdArrRef[$party_id]);
	
						if ($cbo_value_with == 0) 
						{
							if (number_format($balance, 2) > 0.00) 
							{
								?>
								<tr bgcolor="<? echo $bgcolor;?>" onClick="change_color('tr<? echo $i;?>','<? echo $bgcolor;?>')" id="tr<? echo $i;?>">
									<td width="30"><? echo $i; ?></td>
									<td width="150"><p><? echo $knitting_party; ?>&nbsp;</p></td>
									<td width="60" align="center"><? echo 'KG';//$unit_of_measurement[$row[csf('cons_uom')]]; ?>&nbsp;</td>
									<td width="100" align="right"><? echo number_format($opening_balance,2); ?></td>
									<td width="100" align="right"><a href='#' onClick="func_onclick_issue_qty('<?php echo $popupIssueId; ?>')"><? echo number_format($yarn_issue,2); ?><a/></td>                                
									<td width="100" align="right"><a href='#' onClick="func_onclick_fabric_receive('<?php echo $popupFabricReceiveId; ?>')"><? echo number_format($grey_receive_qnty,2); ?><a/></td>
									<td width="100" align="right"><a href='#' onClick="func_onclick_reject_fabric_receive('<?php echo $popupFabricReceiveId; ?>')"><? echo number_format($reject_fabric_receive,2); ?><a/></td>
									<td width="100" align="right"><? echo number_format($dy_tx_wx_rcon,2); ?></td>
									<td width="100" align="right"><a href='#' onClick="func_onclick_yarn_return('<?php echo $popupYarnReturnId; ?>')"><? echo number_format($yarn_return_qnty,2); ?><a/></td>
									<td width="100" align="right"><a href='#' onClick="func_onclick_reject_yarn_return('<?php echo $popupRejectYarnReturnId; ?>')"><? echo number_format($yarn_return_reject_qnty,2); ?><a/></td>
									<td width="100" align="right"><? echo number_format($balance,2); ?></td> 
									<td width="100" align="right" title="Process Loss Qty Formula: [Yarn Issue qty-(Issue Return Qnty+Knitting Qnty+Reject Fabric Qnty)]"><a href='#' onClick="func_onclick_process_loss('<?php echo $popupGreyRcvId; ?>', '<?php echo $popupIssueIdRef; ?>')"><? echo number_format($process_loss,2); ?><a/></td>
									<td align="right"><a href='#' onClick="func_onclick_balance_after_process_loss('<?php echo $popupYarnRcvId; ?>', '<?php echo $popupIssueId; ?>', '<?php echo $popupFabRcvId; ?>', '<?php echo $popupGreyRcvId; ?>', '<?php echo $knitting_party; ?>', '<?php echo $opening_balance; ?>', '<?php echo $popupIssueIdRef; ?>')"><? echo number_format($balance_after_process_loss,2); ?><a/></td> 
									<!--<td align="right"><? echo number_format($balance_after_process_loss,2); ?></td> -->
								</tr>
								<?
								//.'/'.$refCloseDataArr[$party_id]['issue_qty'].'='.$refCloseDataArr[$party_id]['issue_return_qty'].'='.$refCloseDataArr[$party_id]['grey_receive_qnty'].'='.$refCloseDataArr[$party_id]['reject_fabric_receive']
								$i++;
							}
						}
						else
						{
							?>
							<tr bgcolor="<? echo $bgcolor;?>" onClick="change_color('tr<? echo $i;?>','<? echo $bgcolor;?>')" id="tr<? echo $i;?>">
								<td width="30"><? echo $i; ?></td>
								<td width="150"><p><? echo $knitting_party; ?>&nbsp;</p></td>
								<td width="60" align="center"><? echo 'KG';//$unit_of_measurement[$row[csf('cons_uom')]]; ?>&nbsp;</td>
								<td width="100" align="right"><? echo number_format($opening_balance,2); ?></td>
								<td width="100" align="right"><a href='#' onClick="func_onclick_issue_qty('<?php echo $popupIssueId; ?>')"><? echo number_format($yarn_issue,2); ?><a/></td>	               
								<td width="100" align="right"><a href='#' onClick="func_onclick_fabric_receive('<?php echo $popupFabricReceiveId; ?>')"><? echo number_format($grey_receive_qnty,2); ?><a/></td>
								<td width="100" align="right"><a href='#' onClick="func_onclick_reject_fabric_receive('<?php echo $popupRejectFabricReceiveId; ?>')"><? echo number_format($reject_fabric_receive,2); ?><a/></td>
								<td width="100" align="right"><? echo number_format($dy_tx_wx_rcon,2); ?></td>
								<td width="100" align="right"><a href='#' onClick="func_onclick_yarn_return('<?php echo $popupYarnReturnId; ?>')"><? echo number_format($yarn_return_qnty,2); ?><a/></td>
								<td width="100" align="right"><a href='#' onClick="func_onclick_reject_yarn_return('<?php echo $popupRejectYarnReturnId; ?>')"><? echo number_format($yarn_return_reject_qnty,2); ?><a/></td>
								<td width="100" align="right"><? echo number_format($balance,2); ?></td>
									<td width="100" align="right" title="Process Loss Qty Formula: [Yarn Issue qty-(Issue Return Qnty+Knitting Qnty+Reject Fabric Qnty)]"><a href='#' onClick="func_onclick_process_loss('<?php echo $popupGreyRcvId; ?>', '<?php echo $popupIssueIdRef; ?>')"><? echo number_format($process_loss,2); ?><a/></td>
								<td align="right"><a href='#' onClick="func_onclick_balance_after_process_loss('<?php echo $popupYarnRcvId; ?>', '<?php echo $popupIssueId; ?>', '<?php echo $popupFabRcvId; ?>', '<?php echo $popupGreyRcvId; ?>', '<?php echo $knitting_party; ?>', '<?php echo $opening_balance; ?>', '<?php echo $popupIssueIdRef; ?>')"><? echo number_format($balance_after_process_loss,2); ?><a/></td> 
								<!--<td align="right"><? echo number_format($balance_after_process_loss,2); ?></td>--> 
							</tr>
							<?
							//.'/'.$refCloseDataArr[$party_id]['issue_qty'].'='.$refCloseDataArr[$party_id]['issue_return_qty'].'='.$refCloseDataArr[$party_id]['grey_receive_qnty'].'='.$refCloseDataArr[$party_id]['reject_fabric_receive']
							$i++;
						}                       
						
						$tot_opening_bal+=$opening_balance;
						$tot_issue+=$yarn_issue;
						$tot_receive+=$grey_receive_qnty;
						$tot_rejFab_rec+=$reject_fabric_receive;
						$tot_dy_tx_wx_rcon+=$dy_tx_wx_rcon;
						$tot_yarn_return+=$yarn_return_qnty;
						$tot_yarn_retReject+=$yarn_return_reject_qnty;
						$tot_balance+=$balance;
						$tot_returnable+=$yarn_returnable_qty;
						$tot_process_loss+=$process_loss;
						$tot_balance_after_process_loss += $balance_after_process_loss;
					}
					//unset($result);
					?>
				</table>       
				</div>
				<table width="1260" cellpadding="0" cellspacing="0" border="1" rules="all" class="tbl_bottom">
					<tr>
						<td width="30">&nbsp;</td>
						<td width="150">&nbsp;</td>
						<td width="60">Total</th>
						<td width="100" align="right"><? echo number_format($tot_opening_bal,2); ?></td>
						<td width="100" align="right"><? echo number_format($tot_issue,2); ?></td>
						<td width="100" align="right"><? echo number_format($tot_receive,2); ?></td>
						<td width="100" align="right"><? echo number_format($tot_rejFab_rec,2); ?></td>
						<td width="100" align="right"><? echo number_format($tot_dy_tx_wx_rcon,2); ?></td>
						<td width="100" align="right"><? echo number_format($tot_yarn_return,2); ?></td>
						<td width="100" align="right"><? echo number_format($tot_yarn_retReject,2); ?></td>
						<td width="100" align="right"><? echo number_format($tot_balance,2); ?></td>
						<td width="100" align="right"><? echo number_format($tot_process_loss,2); ?></td>
						<td style="padding-right:18px;" align="right"><? echo number_format($tot_balance_after_process_loss,2); ?></td>
					</tr>
				</table>
			</fieldset>      
			<?
			//$yarnRcvIdArr
		}
	
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

//action_issue_qty
if($action=="action_issue_qty")
{
	echo load_html_head_contents("Issue Qty", "../../../../", 1, 1, '', '', '');
	extract($_REQUEST);
	$company_arr=return_library_array( "select id, company_name from lib_company", "id", "company_name");
	$yarn_count_dtls = get_yarn_count_array();
	$supplier_dtls = get_supplier_array();
	$popupIssueId = explode(',', $popupIssueId);
	
	$issue_qty_arr=array();
	$yarn_issue="SELECT 
	a.id AS ID, a.issue_number AS ISSUE_NUMBER, a.knit_dye_company AS KNIT_COMPANY, a.issue_date AS ISSUE_DATE, a.booking_no AS BOOKING_NO, 
	b.requisition_no AS REQ_NO, b.cons_quantity AS CONS_QTY, b.cons_reject_qnty AS CONS_REJECT_QTY 
	FROM inv_issue_master a, inv_transaction b 
	WHERE a.id=b.mst_id AND a.entry_form=3 AND b.transaction_type=2 AND a.status_active=1 AND a.is_deleted=0 AND b.status_active=1 AND b.is_deleted=0".where_con_using_array($popupIssueId, '0', 'a.id');
	//echo $yarn_issue; die;
	$sql_iss_res=sql_select($yarn_issue);
	$dataArr = array();
	$reqNoArr = array();
	foreach($sql_iss_res as $row)
	{
		if($row['REQ_NO'] != '')
		{
			$reqNoArr[$row['REQ_NO']] = $row['REQ_NO'];
		}
	}
	
	//for requisition information
	$sqlReq="SELECT 
	c.requisition_no AS REQ_NO, c.knit_id AS PROG_NO,
	d.lot AS LOT, d.yarn_comp_type1st AS YARN_COMP_TYPE1ST, d.yarn_comp_percent1st AS YARN_COMP_PERCENT1ST, d.yarn_comp_type2nd AS YARN_COMP_TYPE2ND, d.yarn_comp_percent2nd AS YARN_COMP_PERCENT2ND, d.yarn_count_id AS YARN_COUNT_ID, d.yarn_type AS YARN_TYPE, d.color AS  COLOR, d.supplier_id AS SUPPLIER_ID
	FROM ppl_yarn_requisition_entry c, product_details_master d 
	WHERE c.prod_id=d.id AND c.status_active=1 AND c.is_deleted=0".where_con_using_array($reqNoArr, '0', 'c.requisition_no');
	//echo $sqlReq;
	$sqlReqRslt=sql_select($sqlReq);
	$reqData = array();
	foreach($sqlReqRslt as $row)
	{
		//for composition
		$composition_str = '';
		if ($row['YARN_COMP_PERCENT2ND'] != 0) {
			$composition_str = $composition[$row['YARN_COMP_TYPE1ST']] . " " . $row['YARN_COMP_PERCENT1ST'] . "%" . " " . $composition[$row['YARN_COMP_TYPE2ND']] . " " . $row['YARN_COMP_PERCENT2ND'] . "%";
		}
		else
		{
			$composition_str = $composition[$row['YARN_COMP_TYPE1ST']] . " " . $row['YARN_COMP_PERCENT1ST'] . "%" . " " . $composition[$row['YARN_COMP_TYPE2ND']];
		}
		//for composition end
		
		$reqData[$row['REQ_NO']]['PROG_NO'] = $row['PROG_NO'];
		$reqData[$row['REQ_NO']]['COMPOSITION'] = $composition_str;
		$reqData[$row['REQ_NO']]['YARN_COUNT_ID'] = $yarn_count_dtls[$row['YARN_COUNT_ID']];
		$reqData[$row['REQ_NO']]['SUPPLIER_ID'] = $supplier_dtls[$row['SUPPLIER_ID']];
		$reqData[$row['REQ_NO']]['YARN_TYPE'] = $yarn_type[$row['YARN_TYPE']];
		$reqData[$row['REQ_NO']]['LOT'] = $row['LOT'];
	}
	//echo "<pre>";
	//print_r($reqData); die;

	foreach($sql_iss_res as $row)
	{
		$issue_date =date('d-m-Y',strtotime($row['ISSUE_DATE']));
		if(!empty($reqData[$row['REQ_NO']]))
		{
			$dataArr[$issue_date][$row['ISSUE_NUMBER']]['issue_qnty'] += $row['CONS_QTY'];
			$dataArr[$issue_date][$row['ISSUE_NUMBER']]['req_no'] = $row['REQ_NO'];
			$dataArr[$issue_date][$row['ISSUE_NUMBER']]['prog_no'] = $reqData[$row['REQ_NO']]['PROG_NO'];
			$dataArr[$issue_date][$row['ISSUE_NUMBER']]['yarn_count_id'] = $reqData[$row['REQ_NO']]['YARN_COUNT_ID'];
			$dataArr[$issue_date][$row['ISSUE_NUMBER']]['yarn_composition'] = $reqData[$row['REQ_NO']]['COMPOSITION'];
			$dataArr[$issue_date][$row['ISSUE_NUMBER']]['supplier_id'] = $reqData[$row['REQ_NO']]['SUPPLIER_ID'];
			$dataArr[$issue_date][$row['ISSUE_NUMBER']]['yarn_type'] = $reqData[$row['REQ_NO']]['YARN_TYPE'];
			$dataArr[$issue_date][$row['ISSUE_NUMBER']]['lot'] = $reqData[$row['REQ_NO']]['LOT'];
		}
		else
		{
			$dataArr[$issue_date][$row['ISSUE_NUMBER']]['issue_qnty'] += $row['CONS_QTY'];
			$dataArr[$issue_date][$row['ISSUE_NUMBER']]['req_no'] = '';
			$dataArr[$issue_date][$row['ISSUE_NUMBER']]['prog_no'] = $row['BOOKING_NO'];
			$dataArr[$issue_date][$row['ISSUE_NUMBER']]['yarn_count_id'] = '';
			$dataArr[$issue_date][$row['ISSUE_NUMBER']]['yarn_composition'] = '';
			$dataArr[$issue_date][$row['ISSUE_NUMBER']]['supplier_id'] = '';
			$dataArr[$issue_date][$row['ISSUE_NUMBER']]['yarn_type'] = '';
			$dataArr[$issue_date][$row['ISSUE_NUMBER']]['lot'] = '';
		}
	}
	unset($sql_iss_res);
	?>
</head>
<body>
	<div align="center">
        <table width="940" cellpadding="0" cellspacing="0" border="1" rules="all" class="rpt_table"> 
            <thead>
                <tr>
                	<th width="30">Sl</th>
                	<th width="70">Issue Date</th>
                	<th width="100">Issue No</th>
                	<th width="70">Req. No</th>
                	<th width="120">Prog. No/ Booking No</th>
                	<th width="70">Count</th>
                	<th width="100">Yarn Composition</th>
                	<th width="100">Yarn Supplier</th>
                	<th width="100">Yarn Type</th>
                	<th width="100">Yarn Lot</th>
                	<th width="80">Issue Qty</th>
                </tr>
            </thead>
            <tbody>
				<?php
				if(empty($dataArr))
				{
					?>
                    <tr><td colspan="11"><?php echo get_empty_data_msg(); ?></td></tr>
                    <?php
					die;
				}
				
				$sl = 0;
                foreach($dataArr as $issueDate=>$issueDateArr)
				{
					foreach($issueDateArr as $issueNo=>$row)
					{
						$sl++;
						if ($sl%2==0)
							$bgcolor="#E9F3FF";
						else
							$bgcolor="#FFFFFF";
						?>
						<tr bgcolor="<?php echo $bgcolor;?>" height="20">
							<td align="center"><?php echo $sl; ?></td>
							<td align="center"><?php echo $issueDate; ?></td>
							<td align="center"><?php echo $issueNo; ?></th>
							<td align="center"><?php echo $row['req_no']; ?></th>
							<td align="center"><?php echo $row['prog_no']; ?></th>
							<td align="center"><?php echo $row['yarn_count_id']; ?></td>
							<td><p><?php echo $row['yarn_composition']; ?></p></td>
							<td><p><?php echo $row['supplier_id']; ?></p></td>
							<td><p><?php echo $row['yarn_type']; ?></p></td>
							<td><?php echo $row['lot']; ?></td>
							<td align="right"><?php echo number_format($row['issue_qnty'],2); ?></td>
						</tr>
						<?php
						$total_issue_qnty +=number_format($row['issue_qnty'],2,'.','');
					}
				}
				?>
            </tbody>
            <tfoot>
            	<tr>
                	<th colspan="10" align="right">Total</th>
                	<th align="right"><?php echo number_format($total_issue_qnty,2); ?></th>
                </tr>
            </tfoot>
        </table>
    </div>
</body>
<script src="../../../../includes/functions_bottom.js" type="text/javascript"></script>
</html>
<?
exit();
}

//action_fabric_receive
if($action=="action_fabric_receive")
{
	echo load_html_head_contents("Fabric Receive", "../../../../", 1, 1, '', '', '');
	extract($_REQUEST);
	$company_arr=return_library_array( "select id, company_name from lib_company", "id", "company_name");
	$popupFabricReceiveId = explode(',', $popupFabricReceiveId);
	
/*	$sql = "SELECT a.id AS ID, a.knitting_company AS KNIT_COMPANY, a.recv_number AS RECV_NUMBER, a.receive_date AS RECEIVE_DATE,
	b.id AS DTLS_ID, b.grey_receive_qnty AS GREY_RCV_QTY, b.reject_fabric_receive AS REJECT_FAB_RCV,
	c.id AS PROG_NO 
	FROM inv_receive_master a, pro_grey_prod_entry_dtls b, ppl_planning_info_entry_dtls c 
	WHERE a.id=b.mst_id AND c.id=a.booking_id AND a.is_deleted=0 AND a.status_active=1 AND a.entry_form in(2,9,22,58) AND a.item_category = 13".where_con_using_array($popupFabricReceiveId, '0', 'a.id');
*/	// AND a.receive_basis=2

	/*$sql = "SELECT a.id AS ID, a.knitting_company AS KNIT_COMPANY, a.recv_number AS RECV_NUMBER, a.receive_date AS RECEIVE_DATE, a.booking_no AS BOOKING_NO, a.entry_form AS ENTRY_FORM,
	b.id AS DTLS_ID, b.grey_receive_qnty AS GREY_RCV_QTY, b.reject_fabric_receive AS REJECT_FAB_RCV,
	c.dtls_id AS PROG_NO, c.fabric_desc AS FABRIC_DESC, c.gsm_weight AS GSM_WEIGHT, c.dia AS DIA 
	FROM inv_receive_master a, pro_grey_prod_entry_dtls b, ppl_planning_entry_plan_dtls c 
	WHERE a.id=b.mst_id AND a.booking_id=c.dtls_id AND a.is_deleted=0 AND a.status_active=1 AND a.entry_form in(2,9,22,58) AND a.item_category = 13".where_con_using_array($popupFabricReceiveId, '0', 'a.id');*/
	
	$sql = "SELECT a.id AS ID, a.knitting_company AS KNIT_COMPANY, a.recv_number AS RECV_NUMBER, a.receive_date AS RECEIVE_DATE, a.booking_id AS BOOKING_ID, a.booking_no AS BOOKING_NO, a.entry_form AS ENTRY_FORM,
	b.id AS DTLS_ID, b.grey_receive_qnty AS GREY_RCV_QTY, b.reject_fabric_receive AS REJECT_FAB_RCV
	FROM inv_receive_master a, pro_grey_prod_entry_dtls b 
	WHERE a.id=b.mst_id AND a.is_deleted=0 AND a.status_active=1 AND a.entry_form in(2,9,22,58) AND a.item_category = 13".where_con_using_array($popupFabricReceiveId, '0', 'a.id');
	//echo $sql;
	$result_prog=sql_select($sql);
	$rollTblMstId = array();
	$progIdArr = array();
	$rcvIdArr = array();
	foreach($result_prog as $row)
	{
		if($row['ENTRY_FORM'] == 2)
		{
			$progIdArr[$row['ID']] = $row['ID'];
		}
		
		if($row['ENTRY_FORM'] == 22)
		{
			$rcvIdArr[$row['BOOKING_ID']] = $row['BOOKING_ID'];
		}
		
		if($row['ENTRY_FORM'] == 58)
		{
			$rollTblMstId[$row['ID']] = $row['ID'];
		}
	}
	
	//for ppl_planning_entry_plan_dtls
	$sql_2 = sql_select("SELECT a.id AS MST_ID, c.dtls_id AS PROG_NO, c.fabric_desc AS FABRIC_DESC, c.gsm_weight AS GSM_WEIGHT, c.dia AS DIA 
	FROM inv_receive_master a, ppl_planning_entry_plan_dtls c 
	WHERE a.booking_no = c.dtls_id AND a.entry_form = 2".where_con_using_array($progIdArr, '0', 'c.dtls_id'));
	$progData_entryForm_2 = array();
	foreach($sql_2 as $row)
	{
		$progData_entryForm_2[$row['MST_ID']]['PROG_NO'] = $row['PROG_NO'];
		$progData_entryForm_2[$row['MST_ID']]['FABRIC_DESC'] = $row['FABRIC_DESC'];
		$progData_entryForm_2[$row['MST_ID']]['GSM_WEIGHT'] = $row['GSM_WEIGHT'];
		$progData_entryForm_2[$row['MST_ID']]['DIA'] = $row['DIA'];
	}
	unset($sql_2);
	
	//for ppl_planning_entry_plan_dtls
	$sql_22 = sql_select("SELECT a.id AS MST_ID, c.dtls_id AS PROG_NO, c.fabric_desc AS FABRIC_DESC, c.gsm_weight AS GSM_WEIGHT, c.dia AS DIA 
	FROM inv_receive_master a, ppl_planning_entry_plan_dtls c 
	WHERE a.booking_no = c.dtls_id AND a.entry_form = 2".where_con_using_array($rcvIdArr, '0', 'a.id'));
	$progData_entryForm_22 = array();
	foreach($sql_22 as $row)
	{
		$progData_entryForm_22[$row['MST_ID']]['PROG_NO'] = $row['PROG_NO'];
		$progData_entryForm_22[$row['MST_ID']]['FABRIC_DESC'] = $row['FABRIC_DESC'];
		$progData_entryForm_22[$row['MST_ID']]['GSM_WEIGHT'] = $row['GSM_WEIGHT'];
		$progData_entryForm_22[$row['MST_ID']]['DIA'] = $row['DIA'];
	}
	unset($sql_22);

	//for pro_roll_details
	$sql_58 = sql_select("SELECT a.mst_id AS MST_ID, c.dtls_id AS PROG_NO, c.fabric_desc AS FABRIC_DESC, c.gsm_weight AS GSM_WEIGHT, c.dia AS DIA 
	FROM pro_roll_details a, ppl_planning_entry_plan_dtls c 
	WHERE a.booking_no = c.dtls_id AND a.entry_form = 58".where_con_using_array($rollTblMstId, '0', 'a.mst_id'));
	$progData_entryForm_58 = array();
	foreach($sql_58 as $row)
	{
		$progData_entryForm_58[$row['MST_ID']]['PROG_NO'] = $row['PROG_NO'];
		$progData_entryForm_58[$row['MST_ID']]['FABRIC_DESC'] = $row['FABRIC_DESC'];
		$progData_entryForm_58[$row['MST_ID']]['GSM_WEIGHT'] = $row['GSM_WEIGHT'];
		$progData_entryForm_58[$row['MST_ID']]['DIA'] = $row['DIA'];
	}
	unset($sql_58);

	$dataArr = array();
	$refCloseDataArr = array();
	$popupFabricReceiveIdArr = array();
	
	foreach($result_prog as $row)
	{
		if($row['ENTRY_FORM'] == 2)
		{
			$row['PROG_NO'] = $progData_entryForm_2[$row['ID']]['PROG_NO'];
			$row['FABRIC_DESC'] = $progData_entryForm_2[$row['ID']]['FABRIC_DESC'];
			$row['GSM_WEIGHT'] = $progData_entryForm_2[$row['ID']]['GSM_WEIGHT'];
			$row['DIA'] = $progData_entryForm_2[$row['ID']]['DIA'];
		}
		
		if($row['ENTRY_FORM'] == 22)
		{
			$row['PROG_NO'] = $progData_entryForm_22[$row['BOOKING_ID']]['PROG_NO'];
			$row['FABRIC_DESC'] = $progData_entryForm_22[$row['BOOKING_ID']]['FABRIC_DESC'];
			$row['GSM_WEIGHT'] = $progData_entryForm_22[$row['BOOKING_ID']]['GSM_WEIGHT'];
			$row['DIA'] = $progData_entryForm_22[$row['BOOKING_ID']]['DIA'];
		}
		
		if($row['ENTRY_FORM'] == 58)
		{
			$row['PROG_NO'] = $progData_entryForm_58[$row['ID']]['PROG_NO'];
			$row['FABRIC_DESC'] = $progData_entryForm_58[$row['ID']]['FABRIC_DESC'];
			$row['GSM_WEIGHT'] = $progData_entryForm_58[$row['ID']]['GSM_WEIGHT'];
			$row['DIA'] = $progData_entryForm_58[$row['ID']]['DIA'];
		}
		
		$receive_date =date('d-m-Y',strtotime($row['RECEIVE_DATE']));
		$dataArr[$receive_date][$row['PROG_NO']][$row['RECV_NUMBER']]['receive_qty'] += $row['GREY_RCV_QTY'];
		$dataArr[$receive_date][$row['PROG_NO']][$row['RECV_NUMBER']]['fabric_desc'] = $row['FABRIC_DESC'];
		$dataArr[$receive_date][$row['PROG_NO']][$row['RECV_NUMBER']]['gsm'] = $row['GSM_WEIGHT'];
		$dataArr[$receive_date][$row['PROG_NO']][$row['RECV_NUMBER']]['dia'] = $row['DIA'];
		$dataArr[$receive_date][$row['PROG_NO']][$row['RECV_NUMBER']]['challan_no'] = $row['BOOKING_NO'];
	}
	unset($result_prog);
	//echo "<pre>";
	//print_r($refCloseDataArr);
	?>
</head>
<body>
	<div align="center">
        <table width="780" cellpadding="0" cellspacing="0" border="1" rules="all" class="rpt_table"> 
            <thead>
            	<tr>
                	<th colspan="9"><?php echo $company_arr[$company_id]; ?></th>
                </tr>
                <tr>
                	<th width="30">Sl</th>
                	<th width="70">Receive Date</th>
                	<th width="70">Prog. No</th>
                	<th width="120">Receive Id</th>
                	<th width="150">Fabric Description</th>
                	<th width="70">Fab. GSM</th>
                	<th width="70">Receive MC/F.Dia</th>
                	<th width="100">Receive Ch. No</th>
                	<th width="100">Receive Qty</th>
                </tr>
            </thead>
            <tbody>
				<?php
				$sl = 0;
                foreach($dataArr as $rcvDate=>$rcvDateArr)
				{
					foreach($rcvDateArr as $progNo=>$progNoArr)
					{
						foreach($progNoArr as $rcvNo=>$row)
						{
							$sl++;
							if ($sl%2==0)
								$bgcolor="#E9F3FF";
							else
								$bgcolor="#FFFFFF";
							
							?>
							<tr bgcolor="<?php echo $bgcolor;?>" height="20">
								<td align="center"><?php echo $sl; ?></td>
								<td align="center"><?php echo $rcvDate; ?></td>
								<td align="center"><?php echo $progNo; ?></td>
								<td align="center"><?php echo $rcvNo; ?></td>
                                <td><p><?php echo $row['fabric_desc']; ?></p></td>
                                <td align="center"><?php echo $row['gsm']; ?></td>
                                <td align="center"><?php echo $row['dia']; ?></td>
                                <td><?php echo $row['challan_no']; ?></td>
								<td align="right"><?php echo number_format($row['receive_qty'],2); ?></td>
							</tr>
							<?php
							$totalReceiveQty +=number_format($row['receive_qty'],2,'.','');
						}
					}
				}
				?>
            </tbody>
            <tfoot>
            	<tr>
                	<th colspan="8" align="right">Total</th>
                	<th align="right"><?php echo number_format($totalReceiveQty,2); ?></th>
                </tr>
            </tfoot>
        </table>
    </div>
</body>
<script src="../../../../includes/functions_bottom.js" type="text/javascript"></script>
</html>
<?
exit();
}

//action_reject_fabric_receive
if($action=="action_reject_fabric_receive")
{
	echo load_html_head_contents("Reject Fabric Receive", "../../../../", 1, 1, '', '', '');
	extract($_REQUEST);
	$company_arr=return_library_array( "select id, company_name from lib_company", "id", "company_name");
	$yarn_count_dtls = get_yarn_count_array();
	$supplier_dtls = get_supplier_array();
	$popupRejectFabricReceiveId = explode(',', $popupRejectFabricReceiveId);
	$sql = "SELECT a.id AS ID, a.knitting_company AS KNIT_COMPANY, a.recv_number AS RECV_NUMBER, a.receive_date AS RECEIVE_DATE, a.booking_id AS BOOKING_ID, a.booking_no AS BOOKING_NO, a.entry_form AS ENTRY_FORM,
	b.id AS DTLS_ID, b.grey_receive_qnty AS GREY_RCV_QTY, b.reject_fabric_receive AS REJECT_FAB_RCV
	FROM inv_receive_master a, pro_grey_prod_entry_dtls b
	WHERE a.id=b.mst_id AND a.is_deleted=0 AND a.status_active=1 AND a.entry_form in(2,22,58) AND a.item_category = 13".where_con_using_array($popupRejectFabricReceiveId, '0', 'a.id');
	//echo $sql;
	$result_prog=sql_select($sql);
	$rollTblMstId = array();
	$progIdArr = array();
	$rcvIdArr = array();
	foreach($result_prog as $row)
	{
		if($row['ENTRY_FORM'] == 2)
		{
			$progIdArr[$row['ID']] = $row['ID'];
		}
		
		if($row['ENTRY_FORM'] == 22)
		{
			$rcvIdArr[$row['BOOKING_ID']] = $row['BOOKING_ID'];
		}
		
		if($row['ENTRY_FORM'] == 58)
		{
			$rollTblMstId[$row['ID']] = $row['ID'];
		}
	}
	//echo "<pre>";
	//print_r($rcvIdArr);
	
	//for ppl_planning_entry_plan_dtls
	$sql_2 = sql_select("SELECT a.id AS MST_ID, c.dtls_id AS PROG_NO, c.fabric_desc AS FABRIC_DESC, c.gsm_weight AS GSM_WEIGHT, c.dia AS DIA 
	FROM inv_receive_master a, ppl_planning_entry_plan_dtls c 
	WHERE a.booking_no = c.dtls_id AND a.entry_form = 2".where_con_using_array($progIdArr, '0', 'c.dtls_id'));
	$progData_entryForm_2 = array();
	foreach($sql_2 as $row)
	{
		$progData_entryForm_2[$row['MST_ID']]['PROG_NO'] = $row['PROG_NO'];
		$progData_entryForm_2[$row['MST_ID']]['FABRIC_DESC'] = $row['FABRIC_DESC'];
		$progData_entryForm_2[$row['MST_ID']]['GSM_WEIGHT'] = $row['GSM_WEIGHT'];
		$progData_entryForm_2[$row['MST_ID']]['DIA'] = $row['DIA'];
	}
	unset($sql_2);
	
	//for ppl_planning_entry_plan_dtls
	$sql_22 = sql_select("SELECT a.id AS MST_ID, c.dtls_id AS PROG_NO, c.fabric_desc AS FABRIC_DESC, c.gsm_weight AS GSM_WEIGHT, c.dia AS DIA 
	FROM inv_receive_master a, ppl_planning_entry_plan_dtls c 
	WHERE a.booking_no = c.dtls_id AND a.entry_form = 2".where_con_using_array($rcvIdArr, '0', 'a.id'));
	$progData_entryForm_22 = array();
	foreach($sql_22 as $row)
	{
		$progData_entryForm_22[$row['MST_ID']]['PROG_NO'] = $row['PROG_NO'];
		$progData_entryForm_22[$row['MST_ID']]['FABRIC_DESC'] = $row['FABRIC_DESC'];
		$progData_entryForm_22[$row['MST_ID']]['GSM_WEIGHT'] = $row['GSM_WEIGHT'];
		$progData_entryForm_22[$row['MST_ID']]['DIA'] = $row['DIA'];
	}
	unset($sql_22);
	//echo "<pre>";
	//print_r($progData_entryForm_22);

	//for pro_roll_details
	$sql_58 = sql_select("SELECT a.mst_id AS MST_ID, c.dtls_id AS PROG_NO, c.fabric_desc AS FABRIC_DESC, c.gsm_weight AS GSM_WEIGHT, c.dia AS DIA 
	FROM pro_roll_details a, ppl_planning_entry_plan_dtls c 
	WHERE a.booking_no = c.dtls_id AND a.entry_form = 58".where_con_using_array($rollTblMstId, '0', 'a.mst_id'));
	$progData_entryForm_58 = array();
	foreach($sql_58 as $row)
	{
		$progData_entryForm_58[$row['MST_ID']]['PROG_NO'] = $row['PROG_NO'];
		$progData_entryForm_58[$row['MST_ID']]['FABRIC_DESC'] = $row['FABRIC_DESC'];
		$progData_entryForm_58[$row['MST_ID']]['GSM_WEIGHT'] = $row['GSM_WEIGHT'];
		$progData_entryForm_58[$row['MST_ID']]['DIA'] = $row['DIA'];
	}
	unset($sql_58);

	$dataArr = array();
	$refCloseDataArr = array();
	$popupFabricReceiveIdArr = array();
	foreach($result_prog as $row)
	{
		if($row['ENTRY_FORM'] == 2)
		{
			$row['PROG_NO'] = $progData_entryForm_2[$row['ID']]['PROG_NO'];
			$row['FABRIC_DESC'] = $progData_entryForm_2[$row['ID']]['FABRIC_DESC'];
			$row['GSM_WEIGHT'] = $progData_entryForm_2[$row['ID']]['GSM_WEIGHT'];
			$row['DIA'] = $progData_entryForm_2[$row['ID']]['DIA'];
		}
		
		if($row['ENTRY_FORM'] == 22)
		{
			$row['PROG_NO'] = $progData_entryForm_22[$row['BOOKING_ID']]['PROG_NO'];
			$row['FABRIC_DESC'] = $progData_entryForm_22[$row['BOOKING_ID']]['FABRIC_DESC'];
			$row['GSM_WEIGHT'] = $progData_entryForm_22[$row['BOOKING_ID']]['GSM_WEIGHT'];
			$row['DIA'] = $progData_entryForm_22[$row['BOOKING_ID']]['DIA'];
		}
		
		if($row['ENTRY_FORM'] == 58)
		{
			$row['PROG_NO'] = $progData_entryForm_58[$row['ID']]['PROG_NO'];
			$row['FABRIC_DESC'] = $progData_entryForm_58[$row['ID']]['FABRIC_DESC'];
			$row['GSM_WEIGHT'] = $progData_entryForm_58[$row['ID']]['GSM_WEIGHT'];
			$row['DIA'] = $progData_entryForm_58[$row['ID']]['DIA'];
		}
		
		$receive_date =date('d-m-Y',strtotime($row['RECEIVE_DATE']));
		$dataArr[$receive_date][$row['PROG_NO']][$row['RECV_NUMBER']]['receive_qty'] += $row['REJECT_FAB_RCV'];
		$dataArr[$receive_date][$row['PROG_NO']][$row['RECV_NUMBER']]['fabric_desc'] = $row['FABRIC_DESC'];
		$dataArr[$receive_date][$row['PROG_NO']][$row['RECV_NUMBER']]['gsm'] = $row['GSM_WEIGHT'];
		$dataArr[$receive_date][$row['PROG_NO']][$row['RECV_NUMBER']]['dia'] = $row['DIA'];
		$dataArr[$receive_date][$row['PROG_NO']][$row['RECV_NUMBER']]['challan_no'] = $row['BOOKING_NO'];
	}
	unset($result_prog);
	//echo "<pre>";
	//print_r($refCloseDataArr);
	?>
</head>
<body>
	<div align="center">
        <table width="780" cellpadding="0" cellspacing="0" border="1" rules="all" class="rpt_table"> 
            <thead>
            	<tr>
                	<th colspan="10"><?php echo $company_arr[$company_id]; ?></th>
                </tr>
                <tr>
                	<th width="30">Sl</th>
                	<th width="70">Receive Date</th>
                	<th width="70">Prog. No</th>
                	<th width="120">Receive Id</th>
                	<th width="150">Fabric Description</th>
                	<th width="70">Fab. GSM</th>
                	<th width="70">Receive MC/F.Dia</th>
                	<th width="100">Receive Ch. No</th>
                	<th width="100">Reject Rcv. Qty</th>
                </tr>
            </thead>
            <tbody>
				<?php
				$sl = 0;
                foreach($dataArr as $rcvDate=>$rcvDateArr)
				{
					foreach($rcvDateArr as $progNo=>$progNoArr)
					{
						foreach($progNoArr as $rcvNo=>$row)
						{
							$sl++;
							if ($sl%2==0)
								$bgcolor="#E9F3FF";
							else
								$bgcolor="#FFFFFF";
							
							?>
							<tr bgcolor="<?php echo $bgcolor;?>" height="20">
								<td align="center"><?php echo $sl; ?></td>
								<td align="center"><?php echo $rcvDate; ?></td>
								<td align="center"><?php echo $progNo; ?></td>
								<td align="center"><?php echo $rcvNo; ?></td>
                                <td><p><?php echo $row['fabric_desc']; ?></p></td>
                                <td align="center"><?php echo $row['gsm']; ?></td>
                                <td align="center"><?php echo $row['dia']; ?></td>
                                <td><?php echo $row['challan_no']; ?></td>
								<td align="right"><?php echo number_format($row['receive_qty'],2); ?></td>
							</tr>
							<?php
							$totalReceiveQty +=number_format($row['receive_qty'],2,'.','');
						}
					}
				}
				?>
            </tbody>
            <tfoot>
            	<tr>
                	<th colspan="8" align="right">Total</th>
                	<th align="right"><?php echo number_format($totalReceiveQty,2); ?></th>
                </tr>
            </tfoot>
    	</table>
    </div>
</body>
<script src="../../../../includes/functions_bottom.js" type="text/javascript"></script>
</html>
<?
exit();
}

//action_yarn_return
if($action=="action_yarn_return")
{
	echo load_html_head_contents("Yarn Return", "../../../../", 1, 1, '', '', '');
	extract($_REQUEST);
	$company_arr=return_library_array( "select id, company_name from lib_company", "id", "company_name");
	$yarn_count_dtls = get_yarn_count_array();
	$supplier_dtls = get_supplier_array();
	$popupYarnReturnId = explode(',', $popupYarnReturnId);
	
	//for reference close yarn issue return
	$yarn_issue_ret="SELECT 
	a.knitting_company AS KNIT_COMPANY, a.recv_number AS RECV_NUMBER, a.receive_date AS RECEIVE_DATE, a.booking_no AS BOOKING_NO, a.receive_basis AS RECEIVE_BASIS, 
	b.requisition_no AS REQ_NO, b.cons_quantity AS CONS_QTY, b.cons_reject_qnty AS CONS_REJECT_QTY
	FROM inv_receive_master a, inv_transaction b
	WHERE a.id=b.mst_id AND a.entry_form=9 AND b.transaction_type=4 AND b.item_category=1 AND a.status_active=1 AND a.is_deleted=0 AND b.status_active=1 AND b.is_deleted=0".where_con_using_array($popupYarnReturnId, '0', 'a.id');
	//echo $yarn_issue_ret;
	$yarn_ret_result = sql_select($yarn_issue_ret);
	$dataArr = array();
	$reqNoArr = array();
	foreach($yarn_ret_result as $row)
	{
		if($row['REQ_NO'] != '' && $row['REQ_NO'] != '0')
		{
			$reqNoArr[$row['REQ_NO']] = $row['REQ_NO'];
		}
		
		if($row['RECEIVE_BASIS'] == 3)
		{
			$reqNoArr[$row['BOOKING_NO']] = $row['BOOKING_NO'];
		}
	}
	//echo "<pre>";
	//print_r($reqNoArr); die;
	
	//for requisition information
	$sqlReq="SELECT 
	c.requisition_no AS REQ_NO, c.knit_id AS PROG_NO,
	d.lot AS LOT, d.yarn_comp_type1st AS YARN_COMP_TYPE1ST, d.yarn_comp_percent1st AS YARN_COMP_PERCENT1ST, d.yarn_comp_type2nd AS YARN_COMP_TYPE2ND, d.yarn_comp_percent2nd AS YARN_COMP_PERCENT2ND, d.yarn_count_id AS YARN_COUNT_ID, d.yarn_type AS YARN_TYPE, d.color AS  COLOR, d.supplier_id AS SUPPLIER_ID
	FROM ppl_yarn_requisition_entry c, product_details_master d 
	WHERE c.prod_id=d.id AND c.status_active=1 AND c.is_deleted=0".where_con_using_array($reqNoArr, '0', 'c.requisition_no');
	//echo $sqlReq;
	$sqlReqRslt=sql_select($sqlReq);
	$reqData = array();
	foreach($sqlReqRslt as $row)
	{
		//for composition
		$composition_str = '';
		if ($row['YARN_COMP_PERCENT2ND'] != 0)
		{
			$composition_str = $composition[$row['YARN_COMP_TYPE1ST']] . " " . $row['YARN_COMP_PERCENT1ST'] . "%" . " " . $composition[$row['YARN_COMP_TYPE2ND']] . " " . $row['YARN_COMP_PERCENT2ND'] . "%";
		}
		else
		{
			$composition_str = $composition[$row['YARN_COMP_TYPE1ST']] . " " . $row['YARN_COMP_PERCENT1ST'] . "%" . " " . $composition[$row['YARN_COMP_TYPE2ND']];
		}
		//for composition end
		
		$reqData[$row['REQ_NO']]['PROG_NO'] = $row['PROG_NO'];
		$reqData[$row['REQ_NO']]['COMPOSITION'] = $composition_str;
		$reqData[$row['REQ_NO']]['YARN_COUNT_ID'] = $yarn_count_dtls[$row['YARN_COUNT_ID']];
		$reqData[$row['REQ_NO']]['SUPPLIER_ID'] = $supplier_dtls[$row['SUPPLIER_ID']];
		$reqData[$row['REQ_NO']]['YARN_TYPE'] = $yarn_type[$row['YARN_TYPE']];
		$reqData[$row['REQ_NO']]['LOT'] = $row['LOT'];
	}
	//echo "<pre>";
	//print_r($reqData); die;
	
	foreach($yarn_ret_result as $row)
	{
		$receive_date =date('d-m-Y',strtotime($row['RECEIVE_DATE']));
		
		if($row['RECEIVE_BASIS'] == 3)
		{
			$row['REQ_NO'] = $row['BOOKING_NO'];
		}
		
		if(!empty($reqData[$row['REQ_NO']]))
		{
			$dataArr[$receive_date][$row['RECV_NUMBER']]['issue_qnty'] += $row['CONS_QTY'];
			$dataArr[$receive_date][$row['RECV_NUMBER']]['req_no'] = $row['REQ_NO'];
			$dataArr[$receive_date][$row['RECV_NUMBER']]['prog_no'] = $reqData[$row['REQ_NO']]['PROG_NO'];
			$dataArr[$receive_date][$row['RECV_NUMBER']]['yarn_count_id'] = $reqData[$row['REQ_NO']]['YARN_COUNT_ID'];
			$dataArr[$receive_date][$row['RECV_NUMBER']]['yarn_composition'] = $reqData[$row['REQ_NO']]['COMPOSITION'];
			$dataArr[$receive_date][$row['RECV_NUMBER']]['supplier_id'] = $reqData[$row['REQ_NO']]['SUPPLIER_ID'];
			$dataArr[$receive_date][$row['RECV_NUMBER']]['yarn_type'] = $reqData[$row['REQ_NO']]['YARN_TYPE'];
			$dataArr[$receive_date][$row['RECV_NUMBER']]['lot'] = $reqData[$row['REQ_NO']]['LOT'];
		}
		else
		{
			$dataArr[$receive_date][$row['RECV_NUMBER']]['issue_qnty'] += $row['CONS_QTY'];
			$dataArr[$receive_date][$row['RECV_NUMBER']]['req_no'] = '';
			$dataArr[$receive_date][$row['RECV_NUMBER']]['prog_no'] = $row['BOOKING_NO'];
			$dataArr[$receive_date][$row['RECV_NUMBER']]['yarn_count_id'] = '';
			$dataArr[$receive_date][$row['RECV_NUMBER']]['yarn_composition'] = '';
			$dataArr[$receive_date][$row['RECV_NUMBER']]['supplier_id'] = '';
			$dataArr[$receive_date][$row['RECV_NUMBER']]['yarn_type'] = '';
			$dataArr[$receive_date][$row['RECV_NUMBER']]['lot'] = '';
		}
	}
	unset($yarn_ret_result);
	?>
</head>
<body>
    <div align="center">
        <table width="890" cellpadding="0" cellspacing="0" border="1" rules="all" class="rpt_table"> 
            <thead>
                <tr>
                    <th width="30">Sl</th>
                    <th width="70">Issue Date</th>
                    <th width="100">Issue No</th>
                    <th width="70">Req. No</th>
                    <th width="70">Prog. No</th>
                    <th width="70">Count</th>
                    <th width="100">Yarn Composition</th>
                    <th width="100">Yarn Supplier</th>
                    <th width="100">Yarn Type</th>
                    <th width="100">Yarn Lot</th>
                    <th width="80">Issue Qty</th>
                </tr>
            </thead>
            <tbody>
                <?php
				if(empty($dataArr))
				{
					?>
                    <tr><td colspan="11"><?php echo get_empty_data_msg(); ?></td></tr>
                    <?php
					die;
				}
                $sl = 0;
                foreach($dataArr as $receiveDate=>$receiveDateArr)
                {
                    foreach($receiveDateArr as $receiveNo=>$row)
                    {
						$sl++;
						if ($sl%2==0)
							$bgcolor="#E9F3FF";
						else
							$bgcolor="#FFFFFF";
						?>
						<tr bgcolor="<?php echo $bgcolor;?>" height="20">
							<td align="center"><?php echo $sl; ?></td>
							<td align="center"><?php echo $receiveDate; ?></td>
							<td align="center"><?php echo $receiveNo; ?></th>
							<td align="center"><?php echo $row['req_no']; ?></th>
							<td align="center"><?php echo $row['prog_no']; ?></th>
							<td align="center"><?php echo $row['yarn_count_id']; ?></td>
							<td><p><?php echo $row['yarn_composition']; ?></p></td>
							<td><p><?php echo $row['supplier_id']; ?></p></td>
							<td><p><?php echo $row['yarn_type']; ?></p></td>
							<td><?php echo $row['lot']; ?></td>
							<td align="right"><?php echo number_format($row['issue_qnty'],2); ?></td>
						</tr>
						<?php
						$total_issue_qnty +=number_format($row['issue_qnty'],2,'.','');
                    }
                }
                ?>
            </tbody>
            <tfoot>
                <tr>
                    <th colspan="10" align="right">Total</th>
                    <th align="right"><?php echo number_format($total_issue_qnty,2); ?></th>
                </tr>
            </tfoot>
        </table>
    </div>
</body>
<script src="../../../../includes/functions_bottom.js" type="text/javascript"></script>
</html>
<?
exit();
}

//action_reject_yarn_return
if($action=="action_reject_yarn_return")
{
	echo load_html_head_contents("Reject Yarn Return", "../../../../", 1, 1, '', '', '');
	extract($_REQUEST);
	$company_arr=return_library_array( "select id, company_name from lib_company", "id", "company_name");
	$yarn_count_dtls = get_yarn_count_array();
	$supplier_dtls = get_supplier_array();
	$popupRejectYarnReturnId = explode(',', $popupRejectYarnReturnId);

	//for reference close yarn issue return
	$yarn_issue_ret="SELECT 
	a.knitting_company AS KNIT_COMPANY, a.recv_number AS RECV_NUMBER, a.receive_date AS RECEIVE_DATE, a.booking_no AS BOOKING_NO, a.receive_basis AS RECEIVE_BASIS, 
	b.requisition_no AS REQ_NO, b.cons_quantity AS CONS_QTY, b.cons_reject_qnty AS CONS_REJECT_QTY
	FROM inv_receive_master a, inv_transaction b
	WHERE a.id=b.mst_id AND a.entry_form=9 AND b.transaction_type=4 AND b.item_category=1 AND a.status_active=1 AND a.is_deleted=0 AND b.status_active=1 AND b.is_deleted=0".where_con_using_array($popupRejectYarnReturnId, '0', 'a.id');
	//echo $yarn_issue_ret;
	$yarn_ret_result = sql_select($yarn_issue_ret);
	$dataArr = array();
	$reqNoArr = array();
	foreach($yarn_ret_result as $row)
	{
		if($row['REQ_NO'] != '' && $row['REQ_NO'] != '0')
		{
			$reqNoArr[$row['REQ_NO']] = $row['REQ_NO'];
		}
		
		if($row['RECEIVE_BASIS'] == 3)
		{
			$reqNoArr[$row['BOOKING_NO']] = $row['BOOKING_NO'];
		}
	}
	//echo "<pre>";
	//print_r($reqNoArr); die;
	
	//for requisition information
	$sqlReq="SELECT 
	c.requisition_no AS REQ_NO, c.knit_id AS PROG_NO,
	d.lot AS LOT, d.yarn_comp_type1st AS YARN_COMP_TYPE1ST, d.yarn_comp_percent1st AS YARN_COMP_PERCENT1ST, d.yarn_comp_type2nd AS YARN_COMP_TYPE2ND, d.yarn_comp_percent2nd AS YARN_COMP_PERCENT2ND, d.yarn_count_id AS YARN_COUNT_ID, d.yarn_type AS YARN_TYPE, d.color AS  COLOR, d.supplier_id AS SUPPLIER_ID
	FROM ppl_yarn_requisition_entry c, product_details_master d 
	WHERE c.prod_id=d.id AND c.status_active=1 AND c.is_deleted=0".where_con_using_array($reqNoArr, '0', 'c.requisition_no');
	//echo $sqlReq;
	$sqlReqRslt=sql_select($sqlReq);
	$reqData = array();
	foreach($sqlReqRslt as $row)
	{
		//for composition
		$composition_str = '';
		if ($row['YARN_COMP_PERCENT2ND'] != 0)
		{
			$composition_str = $composition[$row['YARN_COMP_TYPE1ST']] . " " . $row['YARN_COMP_PERCENT1ST'] . "%" . " " . $composition[$row['YARN_COMP_TYPE2ND']] . " " . $row['YARN_COMP_PERCENT2ND'] . "%";
		}
		else
		{
			$composition_str = $composition[$row['YARN_COMP_TYPE1ST']] . " " . $row['YARN_COMP_PERCENT1ST'] . "%" . " " . $composition[$row['YARN_COMP_TYPE2ND']];
		}
		//for composition end
		
		$reqData[$row['REQ_NO']]['PROG_NO'] = $row['PROG_NO'];
		$reqData[$row['REQ_NO']]['COMPOSITION'] = $composition_str;
		$reqData[$row['REQ_NO']]['YARN_COUNT_ID'] = $yarn_count_dtls[$row['YARN_COUNT_ID']];
		$reqData[$row['REQ_NO']]['SUPPLIER_ID'] = $supplier_dtls[$row['SUPPLIER_ID']];
		$reqData[$row['REQ_NO']]['YARN_TYPE'] = $yarn_type[$row['YARN_TYPE']];
		$reqData[$row['REQ_NO']]['LOT'] = $row['LOT'];
	}
	//echo "<pre>";
	//print_r($reqData); die;
	
	foreach($yarn_ret_result as $row)
	{
		$receive_date =date('d-m-Y',strtotime($row['RECEIVE_DATE']));
		
		if($row['RECEIVE_BASIS'] == 3)
		{
			$row['REQ_NO'] = $row['BOOKING_NO'];
		}
		
		if(!empty($reqData[$row['REQ_NO']]))
		{
			$dataArr[$receive_date][$row['RECV_NUMBER']]['issue_qnty'] += $row['CONS_REJECT_QTY'];
			$dataArr[$receive_date][$row['RECV_NUMBER']]['req_no'] = $row['REQ_NO'];
			$dataArr[$receive_date][$row['RECV_NUMBER']]['prog_no'] = $reqData[$row['REQ_NO']]['PROG_NO'];
			$dataArr[$receive_date][$row['RECV_NUMBER']]['yarn_count_id'] = $reqData[$row['REQ_NO']]['YARN_COUNT_ID'];
			$dataArr[$receive_date][$row['RECV_NUMBER']]['yarn_composition'] = $reqData[$row['REQ_NO']]['COMPOSITION'];
			$dataArr[$receive_date][$row['RECV_NUMBER']]['supplier_id'] = $reqData[$row['REQ_NO']]['SUPPLIER_ID'];
			$dataArr[$receive_date][$row['RECV_NUMBER']]['yarn_type'] = $reqData[$row['REQ_NO']]['YARN_TYPE'];
			$dataArr[$receive_date][$row['RECV_NUMBER']]['lot'] = $reqData[$row['REQ_NO']]['LOT'];
		}
		else
		{
			$dataArr[$receive_date][$row['RECV_NUMBER']]['issue_qnty'] += $row['CONS_REJECT_QTY'];
			$dataArr[$receive_date][$row['RECV_NUMBER']]['req_no'] = '';
			$dataArr[$receive_date][$row['RECV_NUMBER']]['prog_no'] = $row['BOOKING_NO'];
			$dataArr[$receive_date][$row['RECV_NUMBER']]['yarn_count_id'] = '';
			$dataArr[$receive_date][$row['RECV_NUMBER']]['yarn_composition'] = '';
			$dataArr[$receive_date][$row['RECV_NUMBER']]['supplier_id'] = '';
			$dataArr[$receive_date][$row['RECV_NUMBER']]['yarn_type'] = '';
			$dataArr[$receive_date][$row['RECV_NUMBER']]['lot'] = '';
		}
	}
	unset($yarn_ret_result);
	?>
</head>
<body>
    <div align="center">
        <table width="940" cellpadding="0" cellspacing="0" border="1" rules="all" class="rpt_table"> 
            <thead>
                <tr>
                    <th width="30">Sl</th>
                	<th width="70">Issue Return Date</th>
                	<th width="100">Issue Return No</th>
                	<th width="70">Req. No</th>
                	<th width="70">Prog. No/ Booking No</th>
                	<th width="70">Count</th>
                	<th width="100">Yarn Composition</th>
                	<th width="100">Yarn Supplier</th>
                	<th width="100">Yarn Type</th>
                	<th width="100">Yarn Lot</th>
                	<th width="80">Rej. Yarn Qty</th>
                </tr>
            </thead>
            <tbody>
                <?php
				if(empty($dataArr))
				{
					?>
                    <tr><td colspan="11"><?php echo get_empty_data_msg(); ?></td></tr>
                    <?php
					die;
				}
                $sl = 0;
                foreach($dataArr as $receiveDate=>$receiveDateArr)
                {
                    foreach($receiveDateArr as $receiveNo=>$row)
                    {
						$sl++;
						if ($sl%2==0)
							$bgcolor="#E9F3FF";
						else
							$bgcolor="#FFFFFF";
						?>
						<tr bgcolor="<?php echo $bgcolor;?>" height="20">
							<td align="center"><?php echo $sl; ?></td>
							<td align="center"><?php echo $receiveDate; ?></td>
							<td align="center"><?php echo $receiveNo; ?></th>
							<td align="center"><?php echo $row['req_no']; ?></th>
							<td align="center"><?php echo $row['prog_no']; ?></th>
							<td align="center"><?php echo $row['yarn_count_id']; ?></td>
							<td><p><?php echo $row['yarn_composition']; ?></p></td>
							<td><p><?php echo $row['supplier_id']; ?></p></td>
							<td><p><?php echo $row['yarn_type']; ?></p></td>
							<td><?php echo $row['lot']; ?></td>
							<td align="right"><?php echo number_format($row['issue_qnty'],2); ?></td>
						</tr>
						<?php
						$total_issue_qnty +=number_format($row['issue_qnty'],2,'.','');
                    }
                }
                ?>
            </tbody>
            <tfoot>
                <tr>
                    <th colspan="10" align="right">Total</th>
                    <th align="right"><?php echo number_format($total_issue_qnty,2); ?></th>
                </tr>
            </tfoot>
        </table>
    </div>
</body>
<script src="../../../../includes/functions_bottom.js" type="text/javascript"></script>
</html>
<?
exit();
}

//action_process_loss
if($action=="action_process_loss")
{
	echo load_html_head_contents("Process Loss", "../../../../", 1, 1, '', '', '');
	extract($_REQUEST);
	$company_arr=return_library_array( "select id, company_name from lib_company", "id", "company_name");
	//$prog_no = explode(',', $prog_no);
	$popupGreyRcvId = explode(',', $popupGreyRcvId);
	$popupIssueIdRef = explode(',', $popupIssueIdRef);
	
	//for reference close
	$sql = "SELECT a.receive_date AS RECEIVE_DATE, a.knitting_company AS KNIT_COMPANY, b.id AS DTLS_ID, b.grey_receive_qnty AS GREY_RCV_QTY, b.reject_fabric_receive AS REJECT_FAB_RCV, c.id AS PROG_NO FROM inv_receive_master a, pro_grey_prod_entry_dtls b, ppl_planning_info_entry_dtls c WHERE a.id=b.mst_id AND c.id=a.booking_id AND a.is_deleted=0 AND a.status_active=1 AND a.entry_form=2 AND a.receive_basis=2 AND a.ref_closing_status = 1".where_con_using_array($popupGreyRcvId, '0', 'a.id');
	//echo $sql;
	$result_prog=sql_select($sql);
	$progIdArr = array();
	$refCloseDataArr = array();
	$progReceiveDate = array();
	foreach($result_prog as $row)
	{
		if($dtls_check[$row['DTLS_ID']]=="")
		{
			$dtls_check[$row['DTLS_ID']] = $row['DTLS_ID'];
			//$refCloseDataArr[$row['RECEIVE_DATE']][$row['PROG_NO']]['grey_receive_qnty'] += $row['GREY_RCV_QTY'];
			//$refCloseDataArr[$row['RECEIVE_DATE']][$row['PROG_NO']]['reject_fabric_receive'] += $row['REJECT_FAB_RCV'];
			$progReceiveDate[$row['PROG_NO']] = $row['RECEIVE_DATE'];
			$progIdArr[$row['PROG_NO']] = $row['PROG_NO'];
			
			
			$refCloseDataArr[$row['PROG_NO']]['grey_receive_qnty'] += $row['GREY_RCV_QTY'];
			$refCloseDataArr[$row['PROG_NO']]['reject_fabric_receive'] += $row['REJECT_FAB_RCV'];
		}
	}
	unset($result_prog);
	//echo "<pre>";
	//print_r($refCloseDataArr); die;
	
	//for reference close yarn issue
	$yarn_issue="SELECT a.id AS ID, a.knit_dye_company AS KNIT_COMPANY, b.cons_quantity AS CONS_QTY, b.cons_reject_qnty AS CONS_REJECT_QTY, c.knit_id AS PROG_NO FROM inv_issue_master a, inv_transaction b, ppl_yarn_requisition_entry c WHERE a.id=b.mst_id AND b.requisition_no=c.requisition_no AND a.entry_form=3 AND b.transaction_type=2 AND a.status_active=1 AND a.is_deleted=0 AND b.status_active=1 AND b.is_deleted=0 AND c.status_active=1 AND c.is_deleted=0".where_con_using_array($popupIssueIdRef, '0', 'a.id')." GROUP BY a.id, a.knit_dye_company, b.cons_quantity, b.cons_reject_qnty, c.knit_id";
	//echo $yarn_issue;
	$yarn_result = sql_select($yarn_issue);
	$issueIdArr = array();
	foreach($yarn_result as $row)
	{
		$issueIdArr[$row['ID']] = $row['ID'];
		$row['PROGRAM_DATE'] = $progReceiveDate[$row['PROG_NO']];
		//$refCloseDataArr[$row['PROGRAM_DATE']][$row['PROG_NO']]['issue_qty'] += $row['CONS_QTY'];
		//$refCloseDataArr[$row['PROGRAM_DATE']][$row['PROG_NO']]['issue_reject_qty'] += $row['CONS_REJECT_QTY'];
		
		$refCloseDataArr_Yarnissue[$row['PROG_NO']]['issue_qty'] += $row['CONS_QTY'];
		$refCloseDataArr_Yarnissue[$row['PROG_NO']]['issue_reject_qty'] += $row['CONS_REJECT_QTY'];
		
		$pogArr[$row['PROG_NO']] = $row['PROG_NO'];
		//$test[$row['ID']]['issue_qty'] += $row['CONS_QTY'];
	}
	unset($yarn_result);
	//echo "<pre>";
	//print_r($test); die;
	
	//for reference close yarn issue return
	$yarn_issue_ret="SELECT a.receive_date AS RECEIVE_DATE, a.knitting_company AS KNIT_COMPANY, b.cons_quantity AS CONS_QTY, b.cons_reject_qnty AS CONS_REJECT_QTY, c.knit_id AS PROG_NO FROM inv_receive_master a, inv_transaction b, ppl_yarn_requisition_entry c WHERE a.id=b.mst_id AND a.booking_id=c.requisition_no AND a.entry_form=9 AND b.transaction_type=4 AND b.item_category=1 AND a.status_active=1 AND a.is_deleted=0 AND b.status_active=1 AND b.is_deleted=0 AND c.status_active=1 AND c.is_deleted=0".where_con_using_array($issueIdArr, '0', 'a.issue_id').where_con_using_array($pogArr, '0', 'c.knit_id')." GROUP BY a.receive_date, a.knitting_company, b.cons_quantity, b.cons_reject_qnty, c.knit_id";
	//echo $yarn_issue_ret;
	$yarn_ret_result = sql_select($yarn_issue_ret);
	foreach($yarn_ret_result as $row)
	{
		//$refCloseDataArr[$row['RECEIVE_DATE']][$row['PROG_NO']]['issue_return_qty'] += $row['CONS_QTY'];
		//$refCloseDataArr[$row['RECEIVE_DATE']][$row['PROG_NO']]['issue_reject_return_qty'] += $row['CONS_REJECT_QTY'];
		
		$refCloseDataArr_YarnissueReturn[$row['PROG_NO']]['issue_return_qty'] += $row['CONS_QTY'];
		$refCloseDataArr_YarnissueReturn[$row['PROG_NO']]['issue_reject_return_qty'] += $row['CONS_REJECT_QTY'];
	}
	unset($yarn_ret_result);
	//echo "<pre>";
	//print_r($refCloseDataArr);
	//for reference close end
	?>
</head>
<body>
	<div align="center">
        <table width="230" cellpadding="0" cellspacing="0" border="1" rules="all" class="rpt_table"> 
            <thead>
            	<tr>
                	<th colspan="5"><?php echo $company_arr[$company_id]; ?></th>
                </tr>
                <tr>
                	<th width="30">Sl</th>
                	<!--<th width="100">Receive Date</th>-->
                	<th width="100">Prog. No</th>
                	<th width="100">Process Loss Qty</th>
                </tr>
            </thead>
            <tbody>
				<?php
				$sl = 0;
                //foreach($refCloseDataArr as $rcvDate=>$rcvDateArr)
				//{
					foreach($refCloseDataArr as $progNo=>$row)
					{
						$sl++;
						if ($sl%2==0)
							$bgcolor="#E9F3FF";
						else
							$bgcolor="#FFFFFF";
						
						//echo $row['issue_qty'].'='.$row['issue_return_qty'].'='.$row['grey_receive_qnty'].'='.$row['reject_fabric_receive'];
						//$process_loss = $row['issue_qty']-($row['issue_return_qty']+$row['grey_receive_qnty']+$row['reject_fabric_receive']);
						
						//echo $refCloseDataArr_Yarnissue[$progNo]['issue_qty'].'='.$refCloseDataArr_YarnissueReturn[$progNo]['issue_return_qty'].'='.$row['grey_receive_qnty'].'='.$row['reject_fabric_receive'].'/';
						$process_loss = $refCloseDataArr_Yarnissue[$progNo]['issue_qty']-($refCloseDataArr_YarnissueReturn[$progNo]['issue_return_qty']+$row['grey_receive_qnty']+$row['reject_fabric_receive']);
						?>
						<tr bgcolor="<?php echo $bgcolor;?>" height="20">
							<td align="center"><?php echo $sl; ?></td>
							<!--<td align="center"><?php echo date('d-m-Y', strtotime($rcvDate)); ?></td>-->
							<td align="center"><?php echo $progNo; ?></th>
							<td align="right"><?php echo number_format($process_loss,2); ?></td>
						</tr>
						<?php
						$totalProcessLoss += number_format($process_loss,2,'.','');
					}
				//}
				?>
            </tbody>
            <tfoot>
            	<tr>
                	<th colspan="2">Total</th>
                    <th align="right"><?php echo number_format($totalProcessLoss,2); ?></th>
                </tr>
            </tfoot>
        </table>
    
    </div>
</body>
<script src="../../../../includes/functions_bottom.js" type="text/javascript"></script>
</html>
<?
exit();
}

//action_balance_after_process_loss
if($action=="action_balance_after_process_loss")
{
	echo load_html_head_contents("Balance After Process Loss", "../../../../", 1, 1, '', '', '');
	extract($_REQUEST);
	$company_arr=return_library_array( "select id, company_name from lib_company", "id", "company_name");
	$popupYarnRcvId = explode(',', $popupYarnRcvId);
	$popupIssueId = explode(',', $popupIssueId);
	$popupFabRcvId = explode(',', $popupFabRcvId);
	$popupGreyRcvId = explode(',', $popupGreyRcvId);
	
	$popupIssueIdRef = explode(',', $popupIssueIdRef);

	//for yarn receive
	$sql_yrec="select a.id, a.booking_id, a.booking_no, a.supplier_id, a.receive_date, a.ref_closing_status, sum(b.cons_quantity) as cons_quantity, a.entry_form, a.receive_basis, a.receive_purpose
	from inv_receive_master a, inv_transaction b where a.id=b.mst_id and a.company_id = ".$company_id." and a.receive_purpose in (2,12,15,38) and a.item_category =1 and b.item_category =1 and a.entry_form=1 and b.transaction_type=1 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0".where_con_using_array($popupYarnRcvId, '0', 'a.id')." group by a.id, a.booking_id, a.booking_no, a.supplier_id, a.receive_date, a.ref_closing_status, a.entry_form, a.receive_basis, a.receive_purpose";
	//echo $sql_yrec;
	$sql_yrec_res=sql_select($sql_yrec);
	$yarnDyeingId = array();
	foreach($sql_yrec_res as $rowyRec)
	{
		if($rowyRec[csf('entry_form')] == 1 && $rowyRec[csf('receive_basis')] == 2 && $rowyRec[csf('receive_purpose')] == 2)
		{
			$yarnDyeingId[$rowyRec[csf('booking_id')]] = $rowyRec[csf('booking_id')];
		}
	}
	
	$sql_yarn_dyeing = sql_select("SELECT MST_ID, FAB_BOOKING_NO FROM WO_YARN_DYEING_DTLS WHERE STATUS_ACTIVE = 1 AND IS_DELETED = 0".where_con_using_array($yarnDyeingId, '0', 'MST_ID'));
	$yarn_dyeing_data = array();
	foreach($sql_yarn_dyeing as $row)
	{
		$yarn_dyeing_data[$row['MST_ID']] = $row['FAB_BOOKING_NO'];
	}
	
	$party_data = array();
	$rcvIdRefClose = array();
	$yarnRcvIdArr = array();
	foreach($sql_yrec_res as $rowyRec)
	{
		if($rowyRec[csf('entry_form')] == 1 && $rowyRec[csf('receive_basis')] == 2 && $rowyRec[csf('receive_purpose')] == 2)
		{
			$rowyRec[csf('booking_no')] = $yarn_dyeing_data[$rowyRec[csf('booking_id')]];
		}
		
		$party_data[$rowyRec[csf('booking_no')]]['yarn_rec']+=$rowyRec[csf('cons_quantity')];
	}
	unset($sql_yrec_res);
	//echo "<pre>";
	//print_r($party_data); die;
	
	//for yarn issue
	$issue_qty_arr=array();
	$sql_req="select b.requisition_no as REQUISITION_NO, d.booking_no as BOOKING_NO from inv_issue_master a, inv_transaction b, ppl_yarn_requisition_entry c, ppl_planning_entry_plan_dtls d where a.id=b.mst_id and b.requisition_no=c.requisition_no and c.knit_id = d.dtls_id and a.item_category=1 and a.entry_form=3 and a.company_id = ".$company_id." and b.item_category=1 and b.transaction_type=2 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.issue_basis = 3".where_con_using_array($popupIssueId, '0', 'a.id')." group by b.requisition_no, d.booking_no";
	//echo $sql_req;
	$sql_req_rslt=sql_select($sql_req);
	$bookingArr = array();
	foreach($sql_req_rslt as $row)
	{
		$bookingArr[$row['REQUISITION_NO']] = $row['BOOKING_NO'];
	}
	unset($sql_req_rslt);

	$sql_iss="select a.id, a.booking_id, a.booking_no, a.knit_dye_company, a.issue_date, a.issue_basis, a.issue_purpose, b.requisition_no, sum(b.cons_quantity) as cons_quantity, sum(b.return_qnty) as return_qnty from inv_issue_master a, inv_transaction b where a.item_category=1 and a.entry_form=3 and a.company_id = ".$company_id." and a.id=b.mst_id and b.item_category=1 and b.transaction_type=2 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0".where_con_using_array($popupIssueId, '0', 'a.id')." group by a.id, a.booking_id, a.booking_no, a.knit_dye_company, a.issue_date, a.issue_basis, a.issue_purpose, b.requisition_no";
	//echo $sql_iss; die;
	$sql_iss_res=sql_select($sql_iss);
	$yarnDyeingId = array();
	foreach($sql_iss_res as $rowIss)
	{
		if($rowIss[csf('issue_basis')] == 1)
		{
			if($rowIss[csf('issue_purpose')] == 2 || $rowIss[csf('issue_purpose')] == 7 || $rowIss[csf('issue_purpose')] == 12 || $rowIss[csf('issue_purpose')] == 15 || $rowIss[csf('issue_purpose')] == 38 || $rowIss[csf('issue_purpose')] == 46 || $rowIss[csf('issue_purpose')] == 50 || $rowIss[csf('issue_purpose')] == 51)
			{
				$yarnDyeingId[$rowIss[csf('booking_id')]] = $rowIss[csf('booking_id')];
			}
		}
	}
	//echo "<pre>";
	//print_r($yarnDyeingId); die;
	
	$sql_yarn_dyeing = sql_select("SELECT MST_ID, FAB_BOOKING_NO FROM WO_YARN_DYEING_DTLS WHERE STATUS_ACTIVE = 1 AND IS_DELETED = 0".where_con_using_array($yarnDyeingId, '0', 'MST_ID'));
	$yarn_dyeing_data = array();
	foreach($sql_yarn_dyeing as $row)
	{
		$yarn_dyeing_data[$row['MST_ID']] = $row['FAB_BOOKING_NO'];
	}
	//echo "<pre>";
	//print_r($yarn_dyeing_data); die;
	

	$popupIssueIdArr = array();
	foreach($sql_iss_res as $rowIss)
	{
		if($rowIss[csf('issue_basis')] == 1)
		{
			if($rowIss[csf('issue_purpose')] == 2 || $rowIss[csf('issue_purpose')] == 7 || $rowIss[csf('issue_purpose')] == 12 || $rowIss[csf('issue_purpose')] == 15 || $rowIss[csf('issue_purpose')] == 38 || $rowIss[csf('issue_purpose')] == 46 || $rowIss[csf('issue_purpose')] == 50 || $rowIss[csf('issue_purpose')] == 51)
			{
				$rowIss[csf('booking_no')] = $yarn_dyeing_data[$rowIss[csf('booking_id')]];
			}
		}
		elseif($rowIss[csf('issue_basis')] == 3)
		{
			$rowIss[csf('booking_no')] = $bookingArr[$rowIss[csf('requisition_no')]];
		}
		$party_data[$rowIss[csf('booking_no')]]['issue_qnty']+=$rowIss[csf('cons_quantity')];
		$party_data[$rowIss[csf('booking_no')]]['return_qnty']+=$rowIss[csf('return_qnty')];
	}
	unset($sql_iss_res);
	//echo "<pre>";
	//print_r($party_data); die;

	//for fabric receive
	$sql="select a.id as ID, c.booking_no as BOOKING_NO
	from inv_receive_master a, pro_roll_details b, ppl_planning_entry_plan_dtls c 
	where a.id=b.mst_id and b.booking_no = c.dtls_id and a.item_category in(1,13) and a.company_id = ".$company_id." and a.entry_form = 58 and a.receive_basis = 10 and b.entry_form = 58 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0".where_con_using_array($popupFabRcvId, '0', 'a.id');
	$sql_rslt=sql_select($sql);
	$fBookingArr = array();
	foreach($sql_rslt as $row)
	{
		$fBookingArr[$row['ID']] = $row['BOOKING_NO'];
	}

	//pro_roll_details	
	$sql_rec="select a.id, a.booking_id, a.booking_no, a.knitting_company, a.receive_date, a.receive_basis, a.ref_closing_status, a.entry_form, b.item_category, b.cons_quantity, b.return_qnty, b.cons_reject_qnty from inv_receive_master a, inv_transaction b where a.item_category in(1,13) and a.entry_form in(2,9,22,58) and a.company_id = ".$company_id." and a.id=b.mst_id and b.item_category in(1,13) and b.transaction_type in(1,4) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0".where_con_using_array($popupFabRcvId, '0', 'a.id')." order by a.booking_no, a.receive_date ";
	//echo $sql_rec; die;
	$sql_rec_res=sql_select($sql_rec);
	$requisitionNoArr = array();
	foreach($sql_rec_res as $rowRec)
	{
		if($rowRec[csf('receive_basis')] == 3)
		{
			$requisitionNoArr[$rowRec[csf('booking_no')]] = $rowRec[csf('booking_no')];
		}
		if($rowRec[csf('entry_form')] == 22)
		{
			$rcvIdArr[$rowRec[csf('booking_id')]] = $rowRec[csf('booking_id')];
		}
	}
	
	//for ppl_planning_entry_plan_dtls
	$sql_22 = sql_select("SELECT a.id AS MST_ID, c.dtls_id AS PROG_NO, c.fabric_desc AS FABRIC_DESC, c.gsm_weight AS GSM_WEIGHT, c.dia AS DIA, c.booking_no AS BOOKING_NO
	FROM inv_receive_master a, ppl_planning_entry_plan_dtls c 
	WHERE a.booking_no = c.dtls_id AND a.entry_form = 2".where_con_using_array($rcvIdArr, '0', 'a.id'));
	$progData_entryForm_22 = array();
	foreach($sql_22 as $row)
	{
		$progData_entryForm_22[$row['MST_ID']]['BOOKING_NO'] = $row['BOOKING_NO'];
		/*$progData_entryForm_22[$row['MST_ID']]['PROG_NO'] = $row['PROG_NO'];
		$progData_entryForm_22[$row['MST_ID']]['FABRIC_DESC'] = $row['FABRIC_DESC'];
		$progData_entryForm_22[$row['MST_ID']]['GSM_WEIGHT'] = $row['GSM_WEIGHT'];
		$progData_entryForm_22[$row['MST_ID']]['DIA'] = $row['DIA'];*/
	}
	unset($sql_22);
	//echo "<pre>";
	//print_r($progData_entryForm_22);
	
	$sql_req="select c.requisition_no as REQUISITION_NO, d.booking_no as BOOKING_NO from ppl_yarn_requisition_entry c, ppl_planning_entry_plan_dtls d where c.knit_id = d.dtls_id and d.status_active=1 and d.is_deleted=0".where_con_using_array($requisitionNoArr, '0', 'c.requisition_no')." group by c.requisition_no, d.booking_no";
	//echo $sql_req;
	$sql_req_rslt=sql_select($sql_req);
	$requisitionBookingArr = array();
	foreach($sql_req_rslt as $row)
	{
		$requisitionBookingArr[$row['REQUISITION_NO']] = $row['BOOKING_NO'];
	}
	unset($sql_req_rslt);
	//echo "<pre>";
	//print_r($requisitionBookingArr); die;
	
	//$popupFabricReceiveIdArr = array();
	$fabRcvIdArr = array();
	foreach($sql_rec_res as $rowRec)
	{
		if($rowRec[csf('receive_basis')] == 10)
		{
			$rowRec[csf('booking_no')] = $fBookingArr[$rowRec[csf('id')]];
		}
		
		if($rowRec[csf('item_category')] == 13)
		{
			//$popupFabricReceiveIdArr[$rowRec[csf('knitting_company')]][$rowRec[csf('id')]] = $rowRec[csf('id')];
			if($rowRec[csf('entry_form')]==22)
			{
				$rowRec[csf('booking_no')] = $progData_entryForm_22[$rowRec[csf('booking_id')]]['BOOKING_NO'];
				$party_data[$rowRec[csf('booking_no')]]['fRec']+=$rowRec[csf('cons_quantity')];
			}
			else
			{
				$party_data[$rowRec[csf('booking_no')]]['fRec']+=$rowRec[csf('cons_quantity')];
			}
			
			if($rowRec[csf('ref_closing_status')]==1)
			{
				$party_data[$rowRec[csf('booking_no')]]['ref_closing_fbrcv']+=$rowRec[csf('cons_quantity')];
				$party_data[$rowRec[csf('booking_no')]]['ref_closing_reject_fbrcv']+=$rowRec[csf('cons_reject_qnty')];
			}
		}

		if($rowRec[csf('entry_form')]==9)
		{
			if($rowRec[csf('receive_basis')] == 3)
			{
				$rowRec[csf('booking_no')] = $requisitionBookingArr[$rowRec[csf('booking_no')]];
			}
			
			$party_data[$rowRec[csf('booking_no')]]['ret_yarn']+=$rowRec[csf('cons_quantity')];
			$party_data[$rowRec[csf('booking_no')]]['rej_yarn']+=$rowRec[csf('cons_reject_qnty')];
		}
		elseif($rowRec[csf('entry_form')]==22)
		{
			$rowRec[csf('booking_no')] = $progData_entryForm_22[$rowRec[csf('booking_id')]]['BOOKING_NO'];
			$party_data[$rowRec[csf('booking_no')]]['rej_fab']+=$rowRec[csf('cons_reject_qnty')];
		}
		else
		{
			$party_data[$rowRec[csf('booking_no')]]['rej_fab']+=$rowRec[csf('cons_reject_qnty')];
		}

		$party_data[$rowRec[csf('booking_no')]]['ref_closing_status']=$rowRec[csf('ref_closing_status')];
		$party_data[$rowRec[csf('booking_no')]]['return']+=$rowRec[csf('return_qnty')];
	}
	unset($sql_rec_res);
	//echo "<pre>";
	//print_r($party_data); die;
	
	//for grey receive
	$result_prog=sql_select("SELECT a.id AS ID, a.knitting_company AS KNIT_COMPANY, b.id AS DTLS_ID, b.grey_receive_qnty AS GREY_RCV_QTY, b.reject_fabric_receive AS REJECT_FAB_RCV, c.dtls_id AS PROG_NO, c.booking_no AS BOOKING_NO FROM inv_receive_master a, pro_grey_prod_entry_dtls b, ppl_planning_entry_plan_dtls c WHERE a.id=b.mst_id AND c.dtls_id=a.booking_id AND a.is_deleted=0 AND a.status_active=1 AND a.entry_form=2 AND a.receive_basis=2 AND a.ref_closing_status = 1".where_con_using_array($popupGreyRcvId, '0', 'a.id'));
	$progIdArr = array();
	$refCloseDataArr = array();
	$greyRcvIdArr = array();
	foreach($result_prog as $row)
	{
		$progIdArr[$row['PROG_NO']] = $row['PROG_NO'];
		if($dtls_check[$row['BOOKING_NO']][$row['DTLS_ID']]=="")
		{
			$dtls_check[$row['BOOKING_NO']][$row['DTLS_ID']] = $row['DTLS_ID'];
			$refCloseDataArr[$row['BOOKING_NO']]['grey_receive_qnty'] += $row['GREY_RCV_QTY'];
			$refCloseDataArr[$row['BOOKING_NO']]['reject_fabric_receive'] += $row['REJECT_FAB_RCV'];
			$greyRcvIdArr[$row['BOOKING_NO']][$row['ID']] = $row['ID'];
		}
	}
	unset($result_prog);
	//echo "<pre>";
	//print_r($refCloseDataArr); die;
	
	//for reference close yarn issue
	$yarn_issue="SELECT a.id AS ID, a.knit_dye_company AS KNIT_COMPANY, b.cons_quantity AS CONS_QTY, b.cons_reject_qnty AS CONS_REJECT_QTY, c.knit_id AS KNIT_ID, d.booking_no AS BOOKING_NO FROM inv_issue_master a, inv_transaction b, ppl_yarn_requisition_entry c, ppl_planning_entry_plan_dtls d WHERE a.id=b.mst_id AND b.requisition_no=c.requisition_no AND c.knit_id = d.dtls_id AND a.entry_form=3 AND b.transaction_type=2 AND a.status_active=1 AND a.is_deleted=0 AND b.status_active=1 AND b.is_deleted=0 AND c.status_active=1 AND c.is_deleted=0".where_con_using_array($popupIssueIdRef, '0', 'a.id')." GROUP BY a.id, a.knit_dye_company, b.cons_quantity, b.cons_reject_qnty, c.knit_id, d.booking_no";
	//echo $yarn_issue;
	$yarn_result = sql_select($yarn_issue);
	$issueIdArr = array();
	$popupYarnReturnIdArr = array();
	foreach($yarn_result as $row)
	{
		$issueIdArr[$row['ID']] = $row['ID'];
		$popupYarnReturnIdArr[$row['BOOKING_NO']][$rowIss['ID']] = $rowIss['ID'];
		$refCloseDataArr[$row['BOOKING_NO']]['issue_qty'] += $row['CONS_QTY'];
		$refCloseDataArr[$row['BOOKING_NO']]['issue_reject_qty'] += $row['CONS_REJECT_QTY'];
		$refCloseDataArr[$row['BOOKING_NO']]['prog_no'][$row['KNIT_ID']]= $row['KNIT_ID'];
		
		//$pogArr[$row['PROG_NO']] = $row['PROG_NO'];
	}
	unset($yarn_result);
	//echo "<pre>";
	//print_r($refCloseDataArr);

	//for reference close yarn issue return
	$yarn_issue_ret="SELECT a.knitting_company AS KNIT_COMPANY, b.cons_quantity AS CONS_QTY, b.cons_reject_qnty AS CONS_REJECT_QTY, c.knit_id AS KNIT_ID, d.booking_no AS BOOKING_NO 
	FROM inv_receive_master a, inv_transaction b, ppl_yarn_requisition_entry c, ppl_planning_entry_plan_dtls d 
	WHERE a.id=b.mst_id AND a.booking_id=c.requisition_no AND c.knit_id = d.dtls_id AND a.entry_form=9 AND b.transaction_type=4 AND b.item_category=1 AND a.status_active=1 AND a.is_deleted=0 AND b.status_active=1 AND b.is_deleted=0 AND c.status_active=1 AND c.is_deleted=0".where_con_using_array($issueIdArr, '0', 'a.issue_id').where_con_using_array($progIdArr, '0', 'c.knit_id')." GROUP BY a.knitting_company, b.cons_quantity, b.cons_reject_qnty, c.knit_id, d.booking_no";
	//echo $yarn_issue_ret;
	$yarn_ret_result = sql_select($yarn_issue_ret);
	foreach($yarn_ret_result as $row)
	{
		$refCloseDataArr[$row['BOOKING_NO']]['issue_return_qty'] += $row['CONS_QTY'];
		$refCloseDataArr[$row['BOOKING_NO']]['issue_reject_return_qty'] += $row['CONS_REJECT_QTY'];
		$refCloseDataArr[$row['BOOKING_NO']]['prog_no'][$row['KNIT_ID']]= $row['KNIT_ID'];
	}
	unset($yarn_ret_result);
	//echo "<pre>";
	//print_r($refCloseDataArr);
	//for reference close end
	
	//for booking no
	$bookingNoArray = array();
	$sampleBookingNoArray = array();
	foreach($party_data as $key=>$val)
	{
		$expBooking = array();
		$expBooking = explode('-', $key);
		if($expBooking[1] != 'SMN')
		{
			$bookingNoArray[$key] = $key;
		}
		else
		{
			$sampleBookingNoArray[$key] = $key;
		}
	}
	
	//for booking information
	$sql = "SELECT a.BOOKING_NO, b.STYLE_REF_NO, c.PO_NUMBER, d.BUYER_NAME FROM WO_BOOKING_DTLS a, WO_PO_DETAILS_MASTER b, WO_PO_BREAK_DOWN c, LIB_BUYER d WHERE a.JOB_NO = b.JOB_NO AND a.PO_BREAK_DOWN_ID = c.ID AND b.BUYER_NAME=d.id".where_con_using_array($bookingNoArray, '1', 'a.BOOKING_NO')." GROUP BY a.BOOKING_NO, b.STYLE_REF_NO, c.PO_NUMBER, d.BUYER_NAME";
	//echo $sql;
	$sql_result = sql_select($sql);
	$bookingInfo = array();
	foreach($sql_result as $row)
	{
		$bookingInfo[$row['BOOKING_NO']]['po_no'][$row['PO_NUMBER']] = $row['PO_NUMBER'];
		$bookingInfo[$row['BOOKING_NO']]['style_no'][$row['STYLE_REF_NO']] = $row['STYLE_REF_NO'];
		$bookingInfo[$row['BOOKING_NO']]['buyer_name'][$row['BUYER_NAME']] = $row['BUYER_NAME'];
	}
	unset($sql_result);
	
	//for sample booking information
	$sql = "SELECT a.BOOKING_NO, b.STYLE_REF_NO, d.BUYER_NAME FROM WO_NON_ORD_SAMP_BOOKING_DTLS a, SAMPLE_DEVELOPMENT_MST b, LIB_BUYER d WHERE a.STYLE_ID = b.ID AND b.BUYER_NAME=d.id".where_con_using_array($sampleBookingNoArray, '1', 'a.BOOKING_NO')." GROUP BY a.BOOKING_NO, b.STYLE_REF_NO, d.BUYER_NAME";
	//echo $sql;
	$sql_result = sql_select($sql);
	foreach($sql_result as $row)
	{
		$bookingInfo[$row['BOOKING_NO']]['style_no'][$row['STYLE_REF_NO']] = $row['STYLE_REF_NO'];
		$bookingInfo[$row['BOOKING_NO']]['buyer_name'][$row['BUYER_NAME']] = $row['BUYER_NAME'];
	}
	unset($sql_result);
	?>
</head>
<body>
	<div align="center">
        <table width="1230" cellpadding="0" cellspacing="0" border="1" rules="all" class="rpt_table"> 
            <thead>
            	<tr>
                	<th colspan="13"><?php echo $company_arr[$company_id]; ?></th>
                </tr>
                <tr>
                	<th width="30">Sl</th>
                	<th width="100">Booking No</th>
                	<th width="100">Order No</th>
                	<th width="100">Buyer</th>
                	<th width="100">Style Ref.</th>
                	<th width="100">Yarn Issued</th>
                	<th width="100">Fabric Received</th>
                	<th width="100">Reject Fabric Received</th>
                	<th width="100">Yarn Returned</th>
                	<th width="100">Reject Yarn Returned</th>
                	<th width="100">Balance</th>
                	<th width="100">Process Loss Qty.</th>
                	<th width="100">After Process Loss Balance</th>
                </tr>
            </thead>
            <tbody>
				<tr style="font-weight:bold;">
                	<td colspan="10">Party Name : <?php echo $knitting_party; ?></td>
                	<td colspan="2" align="right">Opening Balanced&nbsp;</td>
                	<td align="right"><?php echo number_format($opening_balance,2); ?></td>
                </tr>
				
				<?php
				$i=0;
				foreach($party_data as $bookingNo=>$bookingNoArr)
				{
					$i++;
					if ($i%2==0)
						$bgcolor="#E9F3FF";
					else
						$bgcolor="#FFFFFF";

					//$opening_balance=0;
					$yarn_issue=0;
					$yarn_returnable_qty=0;
					$dy_tx_wx_rcon=0;
					
					//$opening_balance= $party_opening_arr[$bookingNo]['issOpening']-($party_opening_arr[$bookingNo]['recOpening']+$party_opening_arr[$bookingNo]['yrOpening']);

					$yarn_issue=$bookingNoArr['issue_qnty'];
					$yarn_returnable_qty=$bookingNoArr['return_qnty'];
					
					$dy_tx_wx_rcon=$bookingNoArr['yarn_rec'];
					$grey_receive_qnty=$bookingNoArr['fRec'];
					$reject_fabric_receive=$bookingNoArr['rej_fab'];
					
					$yarn_return_qnty=$bookingNoArr['ret_yarn'];
					$yarn_return_reject_qnty=$bookingNoArr['rej_yarn'];
					$returnable_balance=$yarn_returnable_qty-$yarn_return_qnty;
					//$balance=($opening_balance+$yarn_issue)-($dy_tx_wx_rcon+$grey_receive_qnty+$reject_fabric_receive+$yarn_return_qnty+$yarn_return_reject_qnty);
					$balance=$yarn_issue-($dy_tx_wx_rcon+$grey_receive_qnty+$reject_fabric_receive+$yarn_return_qnty+$yarn_return_reject_qnty);
					
					//for reference close
					//$process_loss = $yarn_issue-($yarn_return_qnty+$ref_closing_grey_fbrrcv+$ref_closing_grey_reject_fbrcv);
					//echo $refCloseDataArr[$bookingNo]['issue_qty'].'='.$refCloseDataArr[$bookingNo]['issue_return_qty'].'='.$refCloseDataArr[$bookingNo]['grey_receive_qnty'].'='.$refCloseDataArr[$bookingNo]['reject_fabric_receive']."<br>";
					$process_loss = $refCloseDataArr[$bookingNo]['issue_qty']-($refCloseDataArr[$bookingNo]['issue_return_qty']+$refCloseDataArr[$bookingNo]['grey_receive_qnty']+$refCloseDataArr[$bookingNo]['reject_fabric_receive']);
					$balance_after_process_loss = $balance-$process_loss;
					?>
					<tr bgcolor="<? echo $bgcolor;?>">
                        <td><? echo $i; ?></td>
                        <td><? echo $bookingNo; ?></td>
                        <td><? echo implode(', ', $bookingInfo[$bookingNo]['po_no']); ?></td>
                        <td><? echo implode(', ', $bookingInfo[$bookingNo]['buyer_name']); ?></td>
                        <td><? echo implode(', ', $bookingInfo[$bookingNo]['style_no']); ?></td>                                
                        <td align="right"><? echo number_format($yarn_issue,2); ?></td>
                        <td align="right"><? echo number_format($grey_receive_qnty,2); ?></td>
                        <td align="right"><? echo number_format($reject_fabric_receive,2); ?></td>
                        <td align="right"><? echo number_format($yarn_return_qnty,2); ?></td>
                        <td align="right"><? echo number_format($yarn_return_reject_qnty,2); ?></td>
                        <td align="right"><? echo number_format($balance,2); ?></td>
                        <td align="right"><? echo number_format($process_loss,2); ?></td>
                        <td align="right"><? echo number_format($balance_after_process_loss,2); ?></td>
					</tr>
					<?php
					$tot_opening_bal+=$opening_balance;
					$tot_issue+=$yarn_issue;
					$tot_receive+=$grey_receive_qnty;
					$tot_rejFab_rec+=$reject_fabric_receive;
					$tot_dy_tx_wx_rcon+=$dy_tx_wx_rcon;
					$tot_yarn_return+=$yarn_return_qnty;
					$tot_yarn_retReject+=$yarn_return_reject_qnty;
					$tot_balance+=$balance;
					//$tot_returnable+=$yarn_returnable_qty;
					$tot_process_loss+=$process_loss;
					$tot_balance_after_process_loss += $balance_after_process_loss;
				}
				
				$gtot_balance = number_format($opening_balance,2,'.','')+number_format($tot_issue,2,'.','')-(number_format($tot_dy_tx_wx_rcon,2,'.','')+number_format($tot_receive,2,'.','')+number_format($tot_rejFab_rec,2,'.','')+number_format($tot_yarn_return,2,'.','')+number_format($tot_yarn_retReject,2,'.',''));
				$gtot_balance_after_process_loss = $gtot_balance-$tot_process_loss;
				?>
            </tbody>
            <tfoot>
            	<tr>
                	<th colspan="5">Total</th>
                    <th align="right"><?php echo number_format($tot_issue,2); ?></th>
                    <th align="right"><?php echo number_format($tot_receive,2); ?></th>
                    <th align="right"><?php echo number_format($tot_rejFab_rec,2); ?></th>
                    <th align="right"><?php echo number_format($tot_yarn_return,2); ?></th>
                    <th align="right"><?php echo number_format($tot_yarn_retReject,2); ?></th>
                    <th align="right"><?php echo number_format($tot_balance,2); ?></th>
                    <th align="right"><?php echo number_format($tot_process_loss,2); ?></th>
                    <th align="right"><?php echo number_format($tot_balance_after_process_loss,2); ?></th>
                </tr>
            	<tr>
                	<th colspan="4">Openning With Party Total Balanced</th>
                    <th align="right"><?php echo number_format($opening_balance,2); ?></th>
                    <th align="right"><?php echo number_format($tot_issue,2); ?></th>
                    <th align="right"><?php echo number_format($tot_receive,2); ?></th>
                    <th align="right"><?php echo number_format($tot_rejFab_rec,2); ?></th>
                    <th align="right"><?php echo number_format($tot_yarn_return,2); ?></th>
                    <th align="right"><?php echo number_format($tot_yarn_retReject,2); ?></th>
                    <th align="right"><?php echo number_format($gtot_balance,2); ?></th>
                    <th align="right"><?php echo number_format($tot_process_loss,2); ?></th>
                    <th align="right"><?php echo number_format($gtot_balance_after_process_loss,2); ?></th>
                </tr>
            </tfoot>
        </table>
    </div>
</body>
<script src="../../../../includes/functions_bottom.js" type="text/javascript"></script>
</html>
<?
exit();
}

if($action=="report_generate_job")
{ 
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process ));
	$buyer_arr=return_library_array( "select id, buyer_name from lib_buyer",'id','buyer_name');
	
	if($db_type==0)
	{
		$from_date=change_date_format(str_replace("'","",$txt_date_from),'yyyy-mm-dd');
		$to_date=change_date_format(str_replace("'","",$txt_date_to),'yyyy-mm-dd');
	}
	if($db_type==2)
	{
		$from_date=change_date_format(str_replace("'","",$txt_date_from),'','',1);
		$to_date=change_date_format(str_replace("'","",$txt_date_to),'','',1);
	}
	if ($from_date!='' && $to_date!='') $issue_date_cond=" and e.issue_date between '$from_date' and '$to_date'"; else $issue_date_cond="";
	if ($from_date!='' && $to_date!='') $recv_date_cond=" and e.receive_date between '$from_date' and '$to_date'"; else $recv_date_cond="";
	
	$knitting_company=str_replace("'","",$txt_knitting_com_id);
	$type=str_replace("'","",$type);
	$job_no=str_replace("'","",$txt_job_no);
	if ($job_no=="") $job_no_cond=""; else $job_no_cond=" and c.job_no_prefix_num in ($job_no) ";//and FIND_IN_SET(c.job_no_prefix_num,'$job_no')
	$txt_internal_ref=str_replace("'","",$txt_internal_ref);
	if ($txt_internal_ref=="") $internal_ref_cond=""; else $internal_ref_cond=" and b.grouping = '$txt_internal_ref'";

	
	ob_start();
	
	$all_party=explode(",",$knitting_company);
		
	?>
        <div>
            <table width="2147" cellpadding="0" cellspacing="0" id="caption"><!-- style="visibility:hidden; border:none"-->
                <tr>
                   <td align="center" width="100%" colspan="22" style="font-size:16px"><strong><? echo $company_arr[str_replace("'","",$cbo_company_name)]; ?></strong></td>
                </tr> 
                <tr>  
                   <td align="center" width="100%" colspan="22" style="font-size:14px"><strong><? echo $report_title; ?></strong></td>
                </tr>  
                <tr> 
                   <td align="center" width="100%" colspan="22" style="font-size:12px"><strong><? echo "From ".change_date_format(str_replace("'","",$txt_date_from))." To ".change_date_format(str_replace("'","",$txt_date_to)); ?></strong></td>
                </tr>
            </table>
            <table width="2147" cellpadding="0" cellspacing="0" border="1" rules="all" class="rpt_table">
                <thead>
                    <th width="30">SL</th>
                    <th width="70">Date</th>
                    <th width="120">Transaction Ref.</th>
                    <th width="100">Recv. Challan No</th>
                    <th width="100">Issue Challan No</th>
                    <th width="120">Booking/ Req. No</th>
                    <th width="80">Buyer</th>
                    <th width="130">Style Ref.</th>
                    <th width="130">Order Numbers</th>
                    <th width="90">Order Qty.</th>
                    <th width="80">Brand</th>
                    <th width="150">Item Description</th>
                    <th width="80">Lot</th>
                    <th width="50">UOM</th>
                    <th width="100">Yarn Issued</th>
                    <th width="100">Returnable Qty.</th>
                    <th width="100">Fabric Received</th>
                    <th width="100">Reject Fabric Received</th>
                    <th width="100">Yarn Returned</th>
                    <th width="100">Reject Yarn Returned</th>
                    <th width="100">Balance</th> 
                    <th width="">Returnable Balanace</th>
                </thead>
            </table>
            <div style="width:2147px; overflow-y: scroll; max-height:380px;" id="scroll_body">
                <table width="2130" cellpadding="0" cellspacing="0" border="1" rules="all" class="rpt_table" id="table_body"> 
				<?
				$po_arr=array();
				$datapoArray=sql_select("select id, po_number, po_quantity from wo_po_break_down");
				
				foreach($datapoArray as $row)
				{
					$po_arr[$row[csf('id')]]['name']=$row[csf('po_number')];
					$po_arr[$row[csf('id')]]['qnty']=$row[csf('po_quantity')];
				}		
				
				$order_nos_array=array();
				if($db_type==0)
				{
					$datapropArray=sql_select("select trans_id,
						CASE WHEN entry_form='3' and trans_type=2 THEN group_concat(po_breakdown_id) END AS yarn_order_id,
						CASE WHEN entry_form in (2,22) and trans_type=1 THEN group_concat(po_breakdown_id) END AS grey_order_id,
						CASE WHEN entry_form='9' and trans_type=4 THEN group_concat(po_breakdown_id) END AS yarn_return_order_id 
						from order_wise_pro_details where trans_id<>0 and quantity>0 and entry_form in (2,3,9,22) and status_active=1 and is_deleted=0 group by trans_id ");
				}
				elseif($db_type==2)
				{
					$datapropArray=sql_select("select trans_id,
						listagg(CASE WHEN entry_form='3' and trans_type=2 THEN  po_breakdown_id END,',') within group (order by po_breakdown_id) AS yarn_order_id,
						listagg(CASE WHEN entry_form in (2,22) and trans_type=1 THEN po_breakdown_id END,',') within group (order by po_breakdown_id) END AS grey_order_id,
						listagg(CASE WHEN entry_form='9' and trans_type=4 THEN po_breakdown_id END,',') within group (order by po_breakdown_id) END AS yarn_return_order_id 
						from order_wise_pro_details where trans_id<>0 and quantity>0 and entry_form in (2,3,9,22) and status_active=1 and is_deleted=0 group by trans_id,entry_form,trans_type ");
				}
									 
				foreach($datapropArray as $row)
				{
					$order_nos_array[$row[csf('trans_id')]]['yarn_issue']=$row[csf('yarn_order_id')];
					$order_nos_array[$row[csf('trans_id')]]['grey_recv']=$row[csf('grey_order_id')];
					$order_nos_array[$row[csf('trans_id')]]['yarn_return']=$row[csf('yarn_return_order_id')];
				}
				
				if (str_replace("'","",$cbo_knitting_source)==0) $knitting_source_cond_party=""; else $knitting_source_cond_party=" and knit_dye_source=$cbo_knitting_source";
				if ($knitting_company=='') $knitting_company_cond_party=""; else  $knitting_company_cond_party="  and a.id in ($knitting_company)";
				if ($knitting_company=='') $knitting_company_cond_comp=""; else  $knitting_company_cond_comp="  and id in ($knitting_company)";
				$knit_source=str_replace("'","",$cbo_knitting_source);
				//echo $cbo_knitting_source;
				if ($knit_source==3)
				{
					$sql_party="select a.id, a.supplier_name from lib_supplier a, lib_supplier_party_type b,lib_supplier_tag_company c where a.id=b.supplier_id and c.supplier_id=b.supplier_id and c.tag_company=$cbo_company_name and b.party_type in(1,9,20) and a.status_active=1 $knitting_company_cond_party group by a.id, a.supplier_name order by a.supplier_name";
				}
				elseif($knit_source==1)
				{
					$sql_party="select comp.id, comp.company_name from lib_company comp where comp.status_active=1 and comp.is_deleted=0 $knitting_company_cond_comp $company_cond order by comp.company_name";
				}
				
				$all_party=sql_select($sql_party);
							
				
                    $i=1; $balance=0; $tot_iss_qnty=0; $tot_recv_qnty=0; $tot_rej_qnty=0; $tot_ret_qnty=0; $tot_reject_yarn_qnty=0;
					foreach($all_party as $party)//($j=0;$j<=count($all_party)-1;$j++)
					{
						$party_name=$party[csf('id')];
						
						if($knit_source==1) 
							$knitting_party=$company_arr[$party_name]; 
						else if($knit_source==3) 
							$knitting_party=$supplier_arr[$party_name];
						else
							$knitting_party="&nbsp;";	
							
						echo '<tr bgcolor="#EFEFEF"><td colspan="22"><b>Party name: '.$knitting_party.'</b></td></tr>';
						
						if (str_replace("'","",$txt_challan)=="") $issue_challan_cond=""; else $issue_challan_cond=" and e.challan_no=$txt_challan";
						
						if ($knit_source==0) $knit_source_cond_party=""; else $knit_source_cond_party=" and e.knit_dye_source=$cbo_knitting_source";
						if ($party_name=='') $knit_company_cond_party=""; else  $knit_company_cond_party=" and e.knit_dye_company in ($party_name)";
						
							 if($db_type==0)
							 {
								 $sql_job="select a.id, a.trans_id, a.po_breakdown_id,
									CASE WHEN a.entry_form='3' and a.trans_type=2 THEN group_concat(a.po_breakdown_id) END AS yarn_order_id,
									CASE WHEN a.entry_form in (2,22) and a.trans_type=1 THEN group_concat(a.po_breakdown_id) END AS grey_order_id,
									CASE WHEN a.entry_form='9' and a.trans_type=4 THEN group_concat(a.po_breakdown_id) END AS yarn_return_order_id ,
									b.id, group_concat(distinct(b.po_number)) as po_number, b.po_quantity,
									c.job_no, c.buyer_name, c.style_ref_no,
									d.id as trans_id, d.cons_uom, d.requisition_no, d.brand_id, d.cons_quantity as issue_qnty, d.return_qnty,
									e.issue_number, e.buyer_id, e.booking_id, e.booking_no, e.buyer_id, e.issue_date, e.challan_no, e.issue_basis,
									f.product_name_details, f.lot 
									from order_wise_pro_details a, wo_po_break_down b, wo_po_details_master c, inv_transaction d, inv_issue_master e, product_details_master f
									where a.trans_id<>0 and a.quantity>0 and a.entry_form in (2,3,9,22) and a.status_active=1 and a.is_deleted=0
									and a.po_breakdown_id=b.id and b.status_active=1
									and b.job_no_mst=c.job_no and c.status_active=1 and c.is_deleted=0 and c.company_name=$cbo_company_name
									and a.trans_id=d.id and d.item_category=1 and d.transaction_type=2 and d.status_active=1 and d.is_deleted=0
									and d.mst_id=e.id and e.entry_form in (2,3,9,22)  and e.status_active=1 and e.is_deleted=0
									and d.prod_id=f.id $knit_source_cond_party $knit_company_cond_party $job_no_cond $issue_challan_cond $issue_date_cond $internal_ref_cond 
									group by c.job_no";
								
							 }
							 else if($db_type==2)
							 {
									$sql_job="select  min(a.trans_id) as trans_id ,sum(b.po_quantity) as po_quantity,
									c.job_no, c.buyer_name, c.style_ref_no,
									min(d.cons_uom) as cons_uom, max(d.requisition_no) as requisition_no, min(d.brand_id) as brand_id, sum(d.cons_quantity) as issue_qnty, sum( d.return_qnty) as return_qnty,
									e.issue_number, min(e.booking_id),min(e.booking_no), max(e.issue_date) as issue_date, min(e.challan_no) as challan_no,min(e.issue_basis) as issue_basis,
									f.product_name_details, min(f.lot) as lot ,
									listagg( CASE WHEN a.entry_form='3' and a.trans_type=2 THEN a.po_breakdown_id END,',') within group (order by a.po_breakdown_id)  AS yarn_order_id, 						listagg(CASE WHEN a.entry_form in (2,22) and a.trans_type=1 THEN  a.po_breakdown_id END,',') within group (order by a.po_breakdown_id)  AS grey_order_id,
									listagg(CASE WHEN a.entry_form='9' and a.trans_type=4 THEN a.po_breakdown_id END,',') within group (order by a.po_breakdown_id)  AS yarn_return_order_id,
									b.po_number
									from order_wise_pro_details a, wo_po_break_down b, wo_po_details_master c, inv_transaction d, inv_issue_master e, product_details_master f
									where a.trans_id<>0 and a.quantity>0 and a.entry_form in (2,3,9,22) and a.status_active=1 and a.is_deleted=0
									and a.po_breakdown_id=b.id and b.status_active=1
									and b.job_no_mst=c.job_no and c.status_active=1 and c.is_deleted=0 and c.company_name=$cbo_company_name
									and a.trans_id=d.id and d.item_category=1 and d.transaction_type=2 and d.status_active=1 and d.is_deleted=0
									and d.mst_id=e.id and e.entry_form in (2,3,9,22) and e.issue_date between '$from_date' and '$to_date' and e.status_active=1 and e.is_deleted=0 and d.prod_id=f.id $knit_source_cond_party $knit_company_cond_party $job_no_cond $issue_challan_cond $issue_date_cond $internal_ref_cond group by c.job_no, c.buyer_name, c.style_ref_no, b.po_number, f.product_name_details, e.issue_number order by c.job_no, c.buyer_name, c.style_ref_no, b.po_number, f.product_name_details, e.issue_number ";
							 }
						
						//echo $sql_job;die;//and e.issue_date between '$from_date' and '$to_date'
						
						$result_job=sql_select($sql_job); $job_array=array();
						foreach($result_job as $row)
						{
							if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
								
							if($row[csf('issue_basis')]==1)
								$booking_reqsn_no=$row[csf('booking_no')];
							else if($row[csf('issue_basis')]==3)
								$booking_reqsn_no=$row[csf('requisition_no')];
							else
								$booking_reqsn_no="&nbsp;";	
								
								
							$balance=$balance+$row[csf('issue_qnty')];
                    		$tot_iss_qnty=$tot_iss_qnty+$row[csf('issue_qnty')];
							
							$po_num=$row[csf('po_number')];
							$po_number=implode(",",array_unique(explode(",",$po_num)));
							

							$order_nos=''; $order_qnty=0;
							if(!in_array($row[csf('job_no')],$job_array))
							{
								if($i!=1)
								{
								?>
									<tr class="tbl_bottom">
                                        <td colspan="9" align="right"><b>Job Total</b></td>
                                        <?
										//$po_qty_tot=0;
										?>
                                        <td align="right"><? echo number_format($po_qty_tot,2,'.',''); ?>&nbsp;</td>
                                        <td align="right" colspan="4">&nbsp;</td>
                                        <td align="right"><? echo number_format($issue_qty_tot,2,'.',''); ?>&nbsp;</td>
                                        <td align="right"><? echo number_format($returnable_qnty_tot,2,'.',''); ?>&nbsp;</td>
                                        <td align="right"><? //echo number_format($issue_qty_tot,2,'.',''); ?>&nbsp;</td>
                                        <td align="right"><? //echo number_format($issue_qty_tot,2,'.',''); ?>&nbsp;</td>
                                        <td align="right"><? //echo number_format($issue_qty_tot,2,'.',''); ?>&nbsp;</td>
                                        <td align="right"><? //echo number_format($issue_qty_tot,2,'.',''); ?>&nbsp;</td>
                                        <td align="right"><? echo number_format($balance_qty_tot,2,'.',''); ?>&nbsp;</td>
                                        <td align="right"><? echo number_format($returnable_balance,2,'.',''); ?>&nbsp;</td>
                                    </tr>
							<?
									unset($po_qty_tot);
									unset($issue_qty_tot);
									unset($returnable_qnty_tot);
									unset($balance_qty_tot);
									unset($returnable_balance);
								}	
							?>
								<tr><td colspan="22" style="font-size:14px" bgcolor="#CCCCAA">&nbsp;<b><?php echo $row[csf('job_no')]; ?></b></td></tr>
							<?	
								$job_array[$i]=$row[csf('job_no')];
							}
	
							?>
							<tr bgcolor="<? echo $bgcolor;?>" onClick="change_color('tr<? echo $i;?>','<? echo $bgcolor;?>')" id="tr<? echo $i;?>">
								<td width="30"><? echo $i; ?></td>
								<td width="70" align="center"><? echo change_date_format($row[csf('issue_date')]); ?></td>
								<td width="120"><div style="word-wrap:break-word; width:120px"><? echo $row[csf('issue_number')]; ?></div></td>
								<td width="100">&nbsp;</td>
								<td width="100"><p>&nbsp;<? echo $row[csf('challan_no')]; ?></p></td>
								<td width="120"><p>&nbsp;<? echo $booking_reqsn_no; ?></p></td>
								<td width="80"><p><? echo $buyer_arr[$row[csf('buyer_name')]]; ?>&nbsp;</p></td>
                                <td width="130"><div style="word-wrap:break-word; width:130px"><? echo $row[csf('style_ref_no')]; ?></div></td>
								<td width="130"><div style="word-wrap:break-word; width:130px"><? echo $po_number; ?></div></td>
								<td width="90" align="right"><? echo number_format($row[csf('po_quantity')],0,'.',''); ?></td>
								<td width="80"><p>&nbsp;<? echo $brand_arr[$row[csf('brand_id')]]; ?></p></td>
								<td width="150"><p><? echo $row[csf('product_name_details')]; ?></p></td>
								<td width="80"><p><? echo $row[csf('lot')]; ?></p></td>
								<td width="50" align="center"><? echo $unit_of_measurement[$row[csf('cons_uom')]]; ?>&nbsp;</td>
								<td width="100" align="right"><? echo number_format($row[csf('issue_qnty')],2,'.',''); ?></td>
                                <td width="100" align="right"><? echo number_format($row[csf('return_qnty')],2,'.',''); ?></td>
								<td width="100" align="right">&nbsp;</td>
								<td width="100" align="right">&nbsp;</td>
								<td width="100" align="right">&nbsp;</td>
								<td width="100" align="right">&nbsp;</td>
								<td width="100" align="right"><? echo number_format($balance,2,'.',''); ?></td>
                                <td align="right"><? $return_balance=$row[csf('return_qnty')]; echo number_format($return_balance,2,'.',''); ?></td>
							</tr>
						<?
							$po_qty_tot+=$row[csf('po_quantity')];
							$issue_qty_tot+=$row[csf('issue_qnty')];
							$balance_qty_tot+=$balance;
							
							$returnable_qnty_tot+=$row[csf('return_qnty')];
							$tot_returnable_qnty+=$row[csf('return_qnty')];
							$returnable_balance+=$row[csf('return_qnty')];
							$tot_returnable_balance+=$row[csf('return_qnty')];
							$i++;
						}
						
						if (str_replace("'","",$txt_challan)=="") $recissue_challan_cond=""; else $recissue_challan_cond=" and a.yarn_issue_challan_no=$txt_challan";
						
						//echo $query="select a.recv_number, a.buyer_id, a.booking_no, a.receive_date, a.item_category, a.challan_no, a.yarn_issue_challan_no, b.id as trans_id, b.cons_uom,  b.brand_id, b.cons_quantity, b.cons_reject_qnty, c.product_name_details, c.lot from inv_receive_master a, inv_transaction b, product_details_master c, order_wise_pro_details d, wo_po_break_down e where b.id=d.trans_id and d.trans_type in (1,4) and d.entry_form in (2,22,9) and d.po_breakdown_id=e.id and a.item_category in(1,13) and a.entry_form in(2,22,9) and a.company_id=$cbo_company_name and a.knitting_source=$cbo_knitting_source and a.knitting_company=$party_name  and a.id=b.mst_id and b.item_category in(1,13) and b.transaction_type in(1,4) and b.prod_id=c.id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and d.status_active=1 and d.is_deleted=0 and e.status_active=1 and e.is_deleted=0 $recissue_challan_cond order by b.transaction_type, a.receive_date";//and a.receive_date between '$from_date' and '$to_date'
						
						if ($knit_source==0) $knit_source_cond_party_rec=""; else $knit_source_cond_party_rec=" and e.knitting_source in ($knit_source)";
						if ($party_name=='') $knit_company_cond_party_rec=""; else  $knit_company_cond_party_rec=" and e.knitting_company in ($party_name)";

						if($db_type==0)
						{
							$query="select a.trans_id, b.po_number, sum(b.po_quantity) as po_quantity,
								c.job_no,c.style_ref_no, c.buyer_name, c.style_ref_no,
								d.cons_uom,d.requisition_no, d.brand_id, sum(d.cons_quantity) as receive_qnty, sum( d.return_qnty) as return_qnty, sum(d.cons_quantity) as cons_quantity, sum(d.cons_reject_qnty) as cons_reject_qnty, 
								e.booking_id, max(e.receive_date) as receive_date, e.challan_no, e.receive_basis, e.recv_number,
								group_concat(e.yarn_issue_challan_no) as yarn_issue_challan_no,
								group_concat(e.item_category) as item_category,
								group_concat(e.booking_no) as booking_no,
								f.product_name_details,
								group_concat(f.lot) as lot,
								CASE WHEN a.entry_form in (2,22) and a.trans_type=1 THEN  group_concat(a.po_breakdown_id) END  AS grey_order_id,
								CASE WHEN a.entry_form='9' and a.trans_type=4 THEN group_concat(a.po_breakdown_id) END  AS yarn_return_order_id
								from order_wise_pro_details a, wo_po_break_down b, wo_po_details_master c, inv_transaction d, inv_receive_master e, product_details_master f
								where a.trans_id<>0 and a.quantity>0 and a.entry_form in (2,22,9) and a.status_active=1 and a.is_deleted=0
								and a.po_breakdown_id=b.id and b.status_active=1
								and b.job_no_mst=c.job_no and c.status_active=1 and c.is_deleted=0 and c.company_name=$cbo_company_name
								and a.trans_id=d.id and d.item_category=1 and d.transaction_type in (1,4) and d.status_active=1 and d.is_deleted=0
								and d.mst_id=e.id and e.entry_form in (2,22,9)  and e.status_active=1 and e.is_deleted=0 and d.prod_id=f.id $knit_source_cond_party_rec $knit_company_cond_party_rec $job_no_cond $recissue_challan_cond  $recv_date_cond group by c.job_no,c.style_ref_no, c.buyer_name, c.style_ref_no, b.po_number, f.product_name_details, e.recv_number, e.item_category order by c.job_no, c.buyer_name, c.style_ref_no, b.po_number, f.product_name_details, e.recv_number";	
						}
						elseif($db_type==2)
						{
							$query="select  min(a.trans_id) as trans_id, b.po_number, sum(b.po_quantity) as po_quantity,
								c.job_no, c.buyer_name, c.style_ref_no,
								min(d.cons_uom) as cons_uom, max(d.requisition_no) as requisition_no, min(d.brand_id) as brand_id, sum(d.cons_quantity) as receive_qnty, sum(d.cons_quantity) as cons_quantity, sum(d.cons_reject_qnty) as cons_reject_qnty, sum( d.return_qnty) as return_qnty,
								min(e.booking_id), max(e.receive_date) as receive_date, min(e.challan_no) as challan_no, min(e.receive_basis) as receive_basis, e.recv_number,
								listagg(CAST(e.yarn_issue_challan_no as varchar2(4000)),',') within group (order by e.yarn_issue_challan_no) as yarn_issue_challan_no,
								listagg(CAST(e.item_category as varchar2(4000)),',') within group (order by e.item_category) as item_category,
								listagg(CAST(e.booking_no as varchar2(4000)),',') within group (order by e.booking_no) as booking_no, e.item_category,
								f.product_name_details,
								listagg(CAST(f.lot as varchar2(4000)),',') within group (order by f.lot) as lot,
								listagg(CASE WHEN a.entry_form in (2,22) and a.trans_type=1 THEN  a.po_breakdown_id END,',') within group (order by a.po_breakdown_id)  AS grey_order_id,
								listagg(CASE WHEN a.entry_form='9' and a.trans_type=4 THEN a.po_breakdown_id END,',') within group (order by a.po_breakdown_id)  AS yarn_return_order_id
								from order_wise_pro_details a, wo_po_break_down b, wo_po_details_master c, inv_transaction d, inv_receive_master e, product_details_master f
								where a.trans_id<>0 and a.quantity>0 and a.entry_form in (2,22,9) and a.status_active=1 and a.is_deleted=0
								and a.po_breakdown_id=b.id and b.status_active=1
								and b.job_no_mst=c.job_no and c.status_active=1 and c.is_deleted=0 and c.company_name=$cbo_company_name
								and a.trans_id=d.id and d.item_category=1 and d.transaction_type in (1,4) and d.status_active=1 and d.is_deleted=0
								and d.mst_id=e.id and e.entry_form in (2,22,9)  and e.status_active=1 and e.is_deleted=0 and d.prod_id=f.id $knit_source_cond_party_rec $knit_company_cond_party_rec $job_no_cond $recissue_challan_cond  $recv_date_cond group by c.job_no, c.buyer_name, c.style_ref_no, b.po_number, f.product_name_details, e.recv_number, e.item_category order by c.job_no, c.buyer_name, c.style_ref_no, b.po_number, f.product_name_details, e.recv_number";
						}
						$result2=sql_select($query); //$job_rec_array=array();
						foreach($result2 as $row)
						{
							if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
								
							if($row[csf('item_category')]==13)
							{
								$all_po_id=explode(",",$order_nos_array[$row[csf('trans_id')]]['grey_recv']);
								$tot_recv_qnty+=$row[csf('cons_quantity')];
								$tot_rej_qnty+=$row[csf('cons_reject_qnty')];
								$balance=$balance-($row[csf('cons_quantity')]+$row[csf('cons_reject_qnty')]);
							}
							else
							{
								$all_po_id=explode(",",$order_nos_array[$row[csf('trans_id')]]['yarn_return']);
								$tot_ret_qnty+=$row[csf('cons_quantity')];
								$tot_reject_yarn_qnty+=$row[csf('cons_reject_qnty')];
								$balance=$balance-($row[csf('cons_quantity')]+$row[csf('cons_reject_qnty')]);
							}
							
							$order_nos=''; $order_qnty=0;
							foreach($all_po_id as $po_id)
							{
								if($order_nos=='') $order_nos=$po_arr[$po_id]['name']; else $order_nos.=",".$po_arr[$po_id]['name'];
								$order_qnty+=$po_arr[$po_id]['qnty'];
							}
							$po_number=implode(",",array_unique(explode(",",$row[csf('po_number')])));
							$returnable_tot+=$row[csf('return_qnty')];
							$grand_tot_balance+=$row[csf('cons_quantity')]+$row[csf('cons_reject_qnty')];
							
							if(!in_array($row[csf('job_no')],$job_rec_array))
							{
								if($i!=1)
								{
								?>
									<tr class="tbl_bottom">
                                        <td colspan="9" align="right"><b>Job Total</b></td>
                                        <?
										//$po_qty_tot=0;
										?>
                                        <td align="right"><? //echo number_format($po_qty_tot,2,'.',''); ?>&nbsp;</td>
                                        <td align="right" colspan="4">&nbsp;</td>
                                        <td align="right"><? echo number_format($returnable_tot,2,'.',''); ?>&nbsp;</td>
                                        <td align="right"><? echo number_format($receive_qty_tot,2,'.',''); ?>&nbsp;</td>
                                        <td align="right"><? //echo number_format($issue_qty_tot,2,'.',''); ?>&nbsp;</td>
                                        <td align="right"><? //echo number_format($issue_qty_tot,2,'.',''); ?>&nbsp;</td>
                                        <td align="right"><? //echo number_format($issue_qty_tot,2,'.',''); ?>&nbsp;</td>
                                        <td align="right"><? //echo number_format($issue_qty_tot,2,'.',''); ?>&nbsp;</td>
                                        <td align="right"><? echo number_format($balance_qty_tot,2,'.',''); ?>&nbsp;</td>
                                        <td align="right"><? echo number_format($tot_returnable_balance,2,'.',''); ?>&nbsp;</td>
                                    </tr>
								<?
									unset($po_qty_tot);
									unset($receive_qty_tot);
									unset($returnable_tot);
									unset($balance_qty_tot);
									unset($tot_returnable_balance);
								}	
							?>
								<tr><td colspan="22" style="font-size:14px" bgcolor="#CCCCAA">&nbsp;<b><?php echo $row[csf('job_no')]; ?></b></td></tr>
							<?	
								$job_rec_array[$i]=$row[csf('job_no')];
							}
							?>							
							<tr bgcolor="<? echo $bgcolor;?>" onClick="change_color('tr<? echo $i;?>','<? echo $bgcolor;?>')" id="tr<? echo $i;?>">
								<td width="30"><? echo $i; ?></td>
								<td width="70" align="center"><? echo change_date_format($row[csf('receive_date')]); ?></td>
								<td width="120"><div style="word-wrap:break-word; width:120px"><? echo $row[csf('recv_number')]; ?></div></td>
								<td width="100"><p>&nbsp;<? echo $row[csf('challan_no')]; ?></p></td>
								<td width="100"><p>&nbsp;<? echo $row[csf('yarn_issue_challan_no')]; ?></p></td>
								<td width="120"><p>&nbsp;<? echo $row[csf('booking_no')]; ?></p></td>
								<td width="80"><p><? echo $buyer_arr[$row[csf('buyer_name')]]; ?>&nbsp;</p></td>
                                <td width="130"><div style="word-wrap:break-word; width:130px"><? echo $row[csf('style_ref_no')]; ?></div></td>
								<td width="130"><div style="word-wrap:break-word; width:130px"><? echo $po_number; ?></div></td>
								<td width="90" align="right"><? echo number_format($row[csf('po_quantity')],0,'.',''); ?>&nbsp;</td>
								<td width="80"><p>&nbsp;<? echo $brand_arr[$row[csf('brand_id')]]; ?></p></td>
								<td width="150"><p><? echo $row[csf('product_name_details')]; ?></p></td>
								<td width="80"><p><? echo $row[csf('lot')]; ?></p></td>
								<td width="50" align="center"><? echo $unit_of_measurement[$row[csf('cons_uom')]]; ?>&nbsp;</td>
								<td width="100" align="right">&nbsp;</td>
                                <td width="100" align="right"><? echo number_format($row[csf('return_qnty')],2,'.',''); ?>&nbsp;</td>
								<td width="100" align="right"><? if($row[csf('item_category')]==13) echo number_format($row[csf('cons_quantity')],2,'.',''); $fab_rec_tot=$row[csf('cons_quantity')]; ?></td>
								<td width="100" align="right"><? if($row[csf('item_category')]==13) echo number_format($row[csf('cons_reject_qnty')],2,'.',''); ?></td>
								<td width="100" align="right"><? if($row[csf('item_category')]==1) echo number_format($row[csf('cons_quantity')],2,'.',''); ?></td>
								<td width="100" align="right"><? if($row[csf('item_category')]==1) echo number_format($row[csf('cons_reject_qnty')],2,'.',''); ?></td>
								<td width="100" align="right"><? echo number_format($balance,2,'.',''); ?></td>
                                <td align="right"><? $return_balance=$row[csf('return_qnty')]-$row[csf('cons_quantity')]; echo number_format($return_balance,2,'.',''); ?></td>
							</tr>
						<?
							$po_qty_tot+=$row[csf('po_quantity')];
							$receive_qty_tot+=$row[csf('receive_qnty')];
							$balance_qty_tot+=$balance;
							$tot_returnable_qnty+=$row[csf('return_qnty')];
							$returnable_balance+=$row[csf('return_qnty')]-$row[csf('cons_quantity')];
							$tot_returnable_balance+=$row[csf('return_qnty')]-$row[csf('cons_quantity')];
							$grand_tot_balance+=$balance_qty_tot;
							$i++;
						}
					}
                    ?>
                        <tr class="tbl_bottom">
                            <td colspan="9" align="right"><b>Job Total</b></td>
                            <?
							unset($tot_returnable_balance);
                            //$po_qty_tot=0;
                            ?>
                            <td align="right"><? //echo number_format($po_qty_tot,2,'.',''); ?>&nbsp;</td>
                            <td align="right" colspan="4">&nbsp;</td>
                            <td align="right"><? echo number_format($issue_qty_tot,2,'.',''); ?>&nbsp;</td>
                            <td align="right"><? echo number_format($returnable_tot,2,'.',''); ?>&nbsp;</td>
                            <td align="right"><? //echo number_format($issue_qty_tot,2,'.',''); ?>&nbsp;</td>
                            <td align="right"><? //echo number_format($issue_qty_tot,2,'.',''); ?>&nbsp;</td>
                            <td align="right"><? //echo number_format($issue_qty_tot,2,'.',''); ?>&nbsp;</td>
                            <td align="right"><? //echo number_format($issue_qty_tot,2,'.',''); ?>&nbsp;</td>
                            <td align="right"><? echo number_format($balance_qty_tot,2,'.',''); ?>&nbsp;</td>
                            <td align="right"><? echo number_format($tot_returnable_balance,2,'.',''); ?>&nbsp;</td>
                        </tr>
                    <tfoot>
                        <th colspan="14" align="right">Total</th>
                        <th align="right"><? echo number_format($tot_iss_qnty,2); ?></th>
                        <th align="right"><? echo number_format($tot_returnable_qnty,2); ?></th>
                        <th align="right"><? echo number_format($tot_recv_qnty,2); ?></th>
                        <th align="right"><? echo number_format($tot_rej_qnty,2); ?></th>
                        <th align="right"><? echo number_format($tot_ret_qnty,2); ?></th>
                        <th align="right"><? echo number_format($tot_reject_yarn_qnty,2); ?></th>
                        <th align="right"><? echo number_format($tot_iss_qnty-($tot_recv_qnty+$tot_rej_qnty+$tot_ret_qnty+$tot_reject_yarn_qnty),2); ?></th>
                        <th align="right"><? echo number_format($tot_returnable_qnty-$tot_ret_qnty,2); ?></th>
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

if($action=="report_generate_excel")
{

	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process ));

	$company_arr=return_library_array( "select id, company_name from lib_company", "id", "company_name");
	$supplier_arr=return_library_array( "select id, supplier_name from lib_supplier", "id", "supplier_name");
	$buyer_arr=return_library_array( "select id, short_name from  lib_buyer", "id", "short_name");
	$brand_arr=return_library_array( "select id, brand_name from lib_brand",'id','brand_name');
	
	$store_arr=return_library_array( "select id, store_name from lib_store_location", "id", "store_name");
	$count_arr=return_library_array( "select id, yarn_count from lib_yarn_count",'id','yarn_count');
	$color_arr=return_library_array( "select id, color_name from lib_color", "id", "color_name");
		

		
	if($db_type==0)
	{
		$from_date=change_date_format(str_replace("'","",$txt_date_from),'yyyy-mm-dd');
		$to_date=change_date_format(str_replace("'","",$txt_date_to),'yyyy-mm-dd');
	}
	elseif($db_type==2)
	{
		$from_date=change_date_format(str_replace("'","",$txt_date_from),'','',1);
		$to_date=change_date_format(str_replace("'","",$txt_date_to),'','',1);
	}
	$knitting_company=str_replace("'","",$txt_knitting_com_id);
	$type=str_replace("'","",$type);
	if (str_replace("'","",$cbo_knitting_source)==0) $knitting_source_cond=""; else $knitting_source_cond=" and a.knit_dye_source=$cbo_knitting_source";
	if (str_replace("'","",$cbo_knitting_source)==0) $knitting_source_rec_cond=""; else $knitting_source_rec_cond=" and a.knitting_source=$cbo_knitting_source";
	if ($knitting_company=='') $knitting_company_cond=""; else  $knitting_company_cond="  and a.knit_dye_company in ($knitting_company)";
	
	 
	
	ob_start();
	
		$po_arr=array();
		$datapoArray=sql_select("select id, job_no_mst, po_number, po_quantity from wo_po_break_down");
		
		foreach($datapoArray as $row)
		{
			$po_arr[$row[csf('id')]]['job']=$row[csf('job_no_mst')];
			$po_arr[$row[csf('id')]]['name']=$row[csf('po_number')];
			$po_arr[$row[csf('id')]]['qnty']=$row[csf('po_quantity')];
		}		
		if($db_type==0) $grpby_field="group by trans_id";
		if($db_type==2) $grpby_field="group by trans_id,entry_form,trans_type";
		else $grpby_field="";
		
		if (str_replace("'","",$txt_challan)=="") $challan_cond=""; else $challan_cond=" and a.issue_number_prefix_num=$txt_challan";
	
		$order_nos_array=array();
		if($db_type==0)
		{
			$datapropArray=sql_select("select trans_id,
				CASE WHEN entry_form='3' and trans_type=2 THEN group_concat(po_breakdown_id) END AS yarn_order_id,
				CASE WHEN entry_form='9' and trans_type=4 THEN group_concat(po_breakdown_id) END AS yarn_return_order_id 
				from order_wise_pro_details where trans_id<>0 and quantity>0 and entry_form in (3,9) and status_active=1 and is_deleted=0 group by trans_id");
		}
		else
		{
			$datapropArray=sql_select("select trans_id,
				listagg(CASE WHEN entry_form='3' and trans_type=2 THEN po_breakdown_id END,',') within group (order by po_breakdown_id) AS yarn_order_id,
				listagg(CASE WHEN entry_form='9' and trans_type=4 THEN po_breakdown_id END,',') within group (order by po_breakdown_id) AS yarn_return_order_id 
				from order_wise_pro_details where trans_id<>0 and quantity>0 and entry_form in (3,9) and status_active=1 and is_deleted=0 group by trans_id,entry_form,trans_type");
		}
							 
		foreach($datapropArray as $row) 
		{
			$order_nos_array[$row[csf('trans_id')]]['yarn_issue']=$row[csf('yarn_order_id')];
			$order_nos_array[$row[csf('trans_id')]]['yarn_return']=$row[csf('yarn_return_order_id')];
		}	

		

		if (str_replace("'","",$cbo_knitting_source)==0) $knitting_source_cond_party=""; else $knitting_source_cond_party=" and knit_dye_source=$cbo_knitting_source";
		if ($knitting_company=='') $knitting_company_cond_party=""; else  $knitting_company_cond_party=" and a.id in ($knitting_company)";
		if ($knitting_company=='') $knitting_company_cond_comp=""; else  $knitting_company_cond_comp=" and id in ($knitting_company)";
		$knit_source=str_replace("'","",$cbo_knitting_source);
		if ($knit_source==0) $knit_source_cond_party=""; else $knit_source_cond_party=" and a.knit_dye_source in ($knit_source)";
		if ($knitting_company=='') $knit_company_cond_party=""; else  $knit_company_cond_party=" and a.knit_dye_company in ($knitting_company)";
		if ($from_date!='' && $to_date!='') $iss_date_cond=" and a.issue_date between '$from_date' and '$to_date'"; else $iss_date_cond="";
		
		
		$sql="select a.id as issue_id, a.issue_number, a.issue_number_prefix_num, a.issue_purpose, a.buyer_id, a.knit_dye_company, a.knit_dye_source, a.booking_id, a.booking_no, a.issue_date, a.issue_basis, b.id as trans_id, b.requisition_no, b.supplier_id, b.cons_quantity as issue_qnty, b.return_qnty, c.yarn_count_id, c.yarn_type, c.lot 
		from inv_issue_master a, inv_transaction b, product_details_master c 
		where a.item_category=1 and a.entry_form=3 and a.company_id=$cbo_company_name and a.id=b.mst_id and b.item_category=1 and b.transaction_type=2 and b.prod_id=c.id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and b.return_qnty!=0 $knit_source_cond_party  $knit_company_cond_party $challan_cond $iss_date_cond order by a.knit_dye_company, a.issue_number_prefix_num";
		$result=sql_select($sql);
		$all_issue_data=array();
		foreach($result as $row)
		{
			$all_po_id=explode(",",$order_nos_array[$row[csf('trans_id')]]['yarn_issue']);
			$all_job_no=""; $order_nos=''; $order_qnty=0;
			foreach($all_po_id as $po_id)
			{
				if($order_nos=='') $order_nos=$po_arr[$po_id]['name']; else $order_nos.=",".$po_arr[$po_id]['name'];
				if($all_job_no=='') $all_job_no=$po_arr[$po_id]['job']; else $all_job_no.=",".$po_arr[$po_id]['job'];
				$order_qnty+=$po_arr[$po_id]['qnty'];
			}
			$job_no=implode(",",array_unique(explode(",",$all_job_no)));
			$issue_id_arr[$row[csf("issue_id")]]=$row[csf("issue_id")];
			$all_issue_data[$row[csf("knit_dye_company")]][$row[csf("issue_number_prefix_num")]][$row[csf("trans_id")]]["issue_number"]=$row[csf("issue_number")];
			$all_issue_data[$row[csf("knit_dye_company")]][$row[csf("issue_number_prefix_num")]][$row[csf("trans_id")]]["issue_number_prefix_num"]=$row[csf("issue_number_prefix_num")];
			$all_issue_data[$row[csf("knit_dye_company")]][$row[csf("issue_number_prefix_num")]][$row[csf("trans_id")]]["issue_purpose"]=$row[csf("issue_purpose")];
			$all_issue_data[$row[csf("knit_dye_company")]][$row[csf("issue_number_prefix_num")]][$row[csf("trans_id")]]["buyer_id"]=$row[csf("buyer_id")];
			$all_issue_data[$row[csf("knit_dye_company")]][$row[csf("issue_number_prefix_num")]][$row[csf("trans_id")]]["knit_dye_company"]=$row[csf("knit_dye_company")];
			$all_issue_data[$row[csf("knit_dye_company")]][$row[csf("issue_number_prefix_num")]][$row[csf("trans_id")]]["knit_dye_source"]=$row[csf("knit_dye_source")];
			if($row[csf("issue_basis")]==1 || $row[csf("issue_basis")]==4)
			{
				$all_issue_data[$row[csf("knit_dye_company")]][$row[csf("issue_number_prefix_num")]][$row[csf("trans_id")]]["booking_id"]=$row[csf("booking_id")];
				$all_issue_data[$row[csf("knit_dye_company")]][$row[csf("issue_number_prefix_num")]][$row[csf("trans_id")]]["booking_no"]=$row[csf("booking_no")];
			}
			else
			{
				$all_issue_data[$row[csf("knit_dye_company")]][$row[csf("issue_number_prefix_num")]][$row[csf("trans_id")]]["booking_id"]=$row[csf("requisition_no")];
				$all_issue_data[$row[csf("knit_dye_company")]][$row[csf("issue_number_prefix_num")]][$row[csf("trans_id")]]["booking_no"]=$row[csf("requisition_no")];
			}
			
			$all_issue_data[$row[csf("knit_dye_company")]][$row[csf("issue_number_prefix_num")]][$row[csf("trans_id")]]["issue_date"]=$row[csf("issue_date")];
			$all_issue_data[$row[csf("knit_dye_company")]][$row[csf("issue_number_prefix_num")]][$row[csf("trans_id")]]["issue_basis"]=$row[csf("issue_basis")];
			$all_issue_data[$row[csf("knit_dye_company")]][$row[csf("issue_number_prefix_num")]][$row[csf("trans_id")]]["trans_id"]=$row[csf("trans_id")];
			$all_issue_data[$row[csf("knit_dye_company")]][$row[csf("issue_number_prefix_num")]][$row[csf("trans_id")]]["issue_qnty"]=$row[csf("issue_qnty")];
			$all_issue_data[$row[csf("knit_dye_company")]][$row[csf("issue_number_prefix_num")]][$row[csf("trans_id")]]["returnable_qnty"]=$row[csf("return_qnty")];
			$all_issue_data[$row[csf("knit_dye_company")]][$row[csf("issue_number_prefix_num")]][$row[csf("trans_id")]]["yarn_count_id"]=$row[csf("yarn_count_id")];
			$all_issue_data[$row[csf("knit_dye_company")]][$row[csf("issue_number_prefix_num")]][$row[csf("trans_id")]]["yarn_type"]=$row[csf("yarn_type")];
			$all_issue_data[$row[csf("knit_dye_company")]][$row[csf("issue_number_prefix_num")]][$row[csf("trans_id")]]["lot"]=$row[csf("lot")];
			$all_issue_data[$row[csf("knit_dye_company")]][$row[csf("issue_number_prefix_num")]][$row[csf("trans_id")]]["job_no"]=$job_no;
			$all_issue_data[$row[csf("knit_dye_company")]][$row[csf("issue_number_prefix_num")]][$row[csf("trans_id")]]["order_nos"]=$order_nos;
			$all_issue_data[$row[csf("knit_dye_company")]][$row[csf("issue_number_prefix_num")]][$row[csf("trans_id")]]["type"]="2";
			
			$all_issue_data[$row[csf("knit_dye_company")]][$row[csf("issue_number_prefix_num")]][$row[csf("trans_id")]]["supplier_id"]=$row[csf("supplier_id")];
			$issue_purpose[$row[csf("issue_number_prefix_num")]]=$row[csf("issue_purpose")];
		}
		
		
		
		$receive_ret_array=array();
		 $sql_return="select a.recv_number, a.knitting_source as knit_dye_source, a.knitting_company as knit_dye_company, a.booking_no, a.buyer_id, a.receive_date,a.recv_number, a.item_category, b.issue_challan_no as issue_number_prefix_num, b.issue_id, b.id as trans_id, c.supplier_id, b.cons_quantity, b.return_qnty, b.cons_reject_qnty, c.yarn_count_id, c.yarn_type, c.lot 
		from inv_receive_master a, inv_transaction b, product_details_master c 
		where a.id=b.mst_id and b.prod_id=c.id and  a.entry_form=9 and a.company_id=$cbo_company_name and  b.item_category=1 and b.transaction_type=4 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0";// echo $sql_return;
		$sql_return_result=sql_select($sql_return);
		foreach($sql_return_result as $row)
		{
			
			$issue_ret_data[$row[csf("knit_dye_company")]][$row[csf("issue_number_prefix_num")]][$row[csf("lot")]]["return_qnty"]+=$row[csf("cons_quantity")];
			$issue_ret_data[$row[csf("knit_dye_company")]][$row[csf("issue_number_prefix_num")]][$row[csf("lot")]]["cons_reject_qnty"]+=$row[csf("cons_reject_qnty")];
			
		}
		
		
		if($knit_source==1){
			$knitting_party=$company_arr; 
		}
		else if($knit_source==3) {
			$knitting_party=$supplier_arr;	
		}
		
		
		?>
        <div>
            <table width="1380" cellpadding="0" cellspacing="0" id="caption" align="left" style="font-size:16px">
                <tr>
                   <td align="center" colspan="17"><strong><? echo $company_arr[str_replace("'","",$cbo_company_name)]; ?></strong></td>
                </tr> 
                <tr>  
                   <td align="center" colspan="17"><strong><? echo $report_title; ?> (Returnable Without Challan)</strong></td>
                </tr>  
                <tr> 
                   <td align="center" colspan="17"><strong><? echo "From ".change_date_format(str_replace("'","",$txt_date_from))." To ".change_date_format(str_replace("'","",$txt_date_to)); ?></strong></td>
                </tr>
            </table>
            <br />
            <table width="1380" cellpadding="0" cellspacing="0" border="1" rules="all" class="rpt_table" align="left">
                <thead>
                    <th width="30">SL</th>
                    <th width="100">Party name</th>	
                    <th width="100">Challan No</th>
                    <th width="100">Issue Purpose</th>
                    <th width="100">Job No.</th>
                    <th width="100">Order No</th>
                    <th width="100">Buyer</th>
                    <th width="50">Count</th>
                    <th width="100">Supplier</th>
                    <th width="100">Type</th>
                    <th width="50">Lot</th>
                    <th width="100">Booking/Reqsn. No</th>
                    <th width="60">Issue Qty.</th>
                    <th width="60">Returnable Qty.</th>
                    <th width="60">Returned Qty</th>
                    <th width="60">Reject Qty</th>
                    <th width="">Returnable Balanace</th>
                </thead>
            </table>
            <div style="width:1398px; overflow-y: scroll; max-height:380px;" id="scroll_body">
                <table width="1380" cellpadding="0" cellspacing="0" border="1" rules="all" class="rpt_table" id="table_body" align="left"> 
				<?
				
				
				
				
				$i=1; $k=1; $j=1; $balance=0; $tot_iss_qnty=0; $tot_recv_qnty=0; $tot_rej_qnty=0; $tot_ret_qnty=0; $tot_reject_yarn_qnty=0; $challan_array=array(); $party_array=array(); 
				foreach($all_issue_data as $knit_company=>$knit_company_data)
				{
					foreach($knit_company_data as $issue_chalan_no=>$issue_chalan_no_data)
					{
						foreach($issue_chalan_no_data as $trans_id=>$value)
						{
							if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
							$return_balance=($value['returnable_qnty']-($value['return_qnty']+$value['cons_reject_qnty']));
							?>
                            <tr bgcolor="<? echo $bgcolor;?>" onClick="change_color('tr<? echo $i;?>','<? echo $bgcolor;?>')" id="tr<? echo $i;?>">
                                <td width="30"><? echo $i; ?></td>
                                <td width="100"><? echo $knitting_party[$knit_company];?></td>
                                <td width="100"><? echo $issue_chalan_no; ?></td>                               
                                <td width="100"><? echo $yarn_issue_purpose[$issue_purpose[$issue_chalan_no]]; ?></td>                                <td width="100"><p><? echo $value['job_no']; ?>&nbsp;</p></td>
                                <td width="100"><p><? echo $value['order_nos']; ?>&nbsp;</p></td>
                                <td width="100"><p><? echo $buyer_arr[$value['buyer_id']]; ?>&nbsp;</p></td>
                                <td width="50"><p><? echo $count_arr[$value['yarn_count_id']]; ?>&nbsp;</p></td>
                                <td width="100"><p><? echo $supplier_arr[$value['supplier_id']]; ?>&nbsp;</p></td>
                                <td width="100"><p><? echo $yarn_type[$value['yarn_type']]; ?>&nbsp;</p></td> 
                                <td width="50"><p><? echo $value['lot']; ?>&nbsp;</p></td>
                                <td width="100" align="center"><p><? echo $value['booking_no']; ?>&nbsp;</p></td>
                                <td width="60" align="right"><? echo number_format($value['issue_qnty'],2,'.',''); ?></td>
                                <td width="60" align="right"><? echo number_format($value['returnable_qnty'],2,'.',''); ?></td>
                                <td width="60" align="right"><? echo number_format($issue_ret_data[$knit_company][$issue_chalan_no][$value['lot']]["return_qnty"],2);//echo number_format($value['return_qnty'],2,'.',''); ?></td>
                                <td width="60" align="right"><? echo number_format($issue_ret_data[$knit_company][$issue_chalan_no][$value['lot']]["cons_reject_qnty"],2);//number_format($value['cons_reject_qnty'],2,'.',''); ?></td>
                                <td align="right"><? 
								$tot_ret_rej_qty = $issue_ret_data[$knit_company][$issue_chalan_no][$value['lot']]["return_qnty"]+ $issue_ret_data[$knit_company][$issue_chalan_no][$value['lot']]["cons_reject_qnty"];
								$balance=$return_balance-$tot_ret_rej_qty;
								echo number_format($balance,2,'.','');
								 ?></td>
                                
                            </tr>
							<?
							$i++;
							$challan_issue_qnty+=$value['issue_qnty'];
							$challan_returnable_qnty+=$value['returnable_qnty'];
							$challan_return_qnty+=$issue_ret_data[$knit_company][$issue_chalan_no][$value['lot']]["return_qnty"];
							$challan_cons_reject_qnty+=$issue_ret_data[$knit_company][$issue_chalan_no][$value['lot']]["cons_reject_qnty"];
							$challan_return_balance+=$balance;
							
						}
					}
				}
				
				?>
    
                
			<tfoot>
                <th colspan="12" align="right"><b>Total</b></th>
                <th align="right"><? echo number_format($challan_issue_qnty,2,'.',''); ?></th>
                <th align="right"><? echo number_format($challan_returnable_qnty,2,'.',''); ?></th>
                <th align="right"><? echo number_format($challan_return_qnty,2,'.',''); ?></th>
                <th align="right"><? echo number_format($challan_cons_reject_qnty,2,'.',''); ?></th>
                <th align="right"><? echo number_format($challan_return_balance,2,'.',''); ?></th>
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