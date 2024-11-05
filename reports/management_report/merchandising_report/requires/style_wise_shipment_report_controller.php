<?
header('Content-type:text/html; charset=utf-8');
session_start();
if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");
require_once('../../../../includes/common.php');

$_SESSION['page_permission']=$permission;
$user_id=$_SESSION['logic_erp']['user_id'];
$data=$_REQUEST['data'];
$action=$_REQUEST['action'];
//--------------------------------------------------------------------------------------------------------------------
$company_library=return_library_array( "select id,company_name from lib_company", "id", "company_name"  );
$buyer_library=return_library_array( "select id, buyer_name from lib_buyer", "id", "buyer_name"  );
$buyer_short_name_library=return_library_array( "select id, buyer_name from lib_buyer", "id", "buyer_name"  );
$item_library=return_library_array( "select id, item_name from lib_item_group", "id", "item_name"  );
$dealing_merchant_array = return_library_array("select id, team_member_name from lib_mkt_team_member_info","id","team_member_name");
$team_library=return_library_array( "select id, team_name from lib_marketing_team", "id", "team_name"  );
$costing_library=return_library_array( "select job_no, costing_date from wo_pre_cost_mst", "job_no", "costing_date"  );
$agent_arr_library=return_library_array( "select a.id,a.buyer_name from lib_buyer a, lib_buyer_tag_company b where a.status_active =1 and a.is_deleted=0 and b.buyer_id=a.id  and a.id in (select  buyer_id from lib_buyer_party_type where party_type in (20,21)) order by buyer_name", "id", "buyer_name"  );



if ($action=="load_drop_down_buyer")
{
	echo create_drop_down( "cbo_buyer_name", 150, "select buy.id, buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company='$data' $buyer_cond and buy.id in (select buyer_id from lib_buyer_party_type where party_type in (1,3,21,90)) group by buy.id, buy.buyer_name order by buy.buyer_name","id,buyer_name", 1, "-- All Buyer --", $selected, "" );     	 
	exit();
}

if ($action=="load_drop_down_team_member")
{
	echo create_drop_down( "cbo_team_member", 150, "select id,team_member_name from lib_mkt_team_member_info where team_id='$data' and status_active=1 and is_deleted=0 order by team_member_name","id,team_member_name", 1, "- Team Member-", $selected, "" ); 
}

if($action=="job_no_popup")
{
	echo load_html_head_contents("Job Info", "../../../../", 1, 1,'','','');
	extract($_REQUEST);
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
								echo create_drop_down( "cbo_buyer_name", 140, "select buy.id, buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company=$companyID $buyer_cond and buy.id in (select buyer_id from lib_buyer_party_type where party_type in (1,3,21,90)) group by  buy.id, buy.buyer_name order by buy.buyer_name","id,buyer_name",1, "-- All Buyer--",$buyer_name,"",0 );
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
                        	<input type="button" name="button" class="formbutton" value="Show" onClick="show_list_view ('<? echo $companyID; ?>'+'**'+document.getElementById('cbo_buyer_name').value+'**'+document.getElementById('cbo_search_by').value+'**'+document.getElementById('txt_search_common').value+'**'+'<? echo $cbo_year_id; ?>'+'**'+'<? echo $cbo_month_id; ?>', 'create_job_no_search_list_view', 'search_div', 'style_wise_shipment_report_controller', 'setFilterGrid(\'tbl_list_search\',-1)');" style="width:100px;" />
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
if ($action=="style_popup")
{
	echo load_html_head_contents("Popup Info","../../../../", 1, 1, $unicode);
	extract($_REQUEST);
	$data=explode('_',$data);
	  $type=$data[4]; 
	?>
	 <script>
	var selected_id = new Array, selected_name = new Array(); selected_attach_id = new Array();
	 
	function toggle( x, origColor ) {
			var newColor = 'yellow';
			if ( x.style ) {
				x.style.backgroundColor = ( newColor == x.style.backgroundColor )? origColor : newColor;
			}
		}
		function check_all_data()
		{
			var row_num=$('#list_view tr').length-1;
			for(var i=1;  i<=row_num;  i++)
			{
				$("#tr_"+i).click();
			}
			
		}
		
	function js_set_value(id)
	{
		var str=id.split("_");
		toggle( document.getElementById( 'tr_' + str[0] ), '#FFFFFF' );
		var strdt=str[2];
		str=str[1];
	
		if( jQuery.inArray(  str , selected_id ) == -1 ) {
			selected_id.push( str );
			selected_name.push( strdt );
		}
		else {
			for( var i = 0; i < selected_id.length; i++ ) {
				if( selected_id[i] == str  ) break;
			}
			selected_id.splice( i, 1 );
			selected_name.splice( i,1 );
		}
		var id = '';
		var ddd='';
		for( var i = 0; i < selected_id.length; i++ ) {
			id += selected_id[i] + ',';
			ddd += selected_name[i] + ',';
		}
		id = id.substr( 0, id.length - 1 );
		ddd = ddd.substr( 0, ddd.length - 1 );
		$('#txt_po_id').val( id );
		$('#txt_po_val').val( ddd );
	} 
		  
	</script>
	

 <input type="hidden" id="txt_po_id" />
 <input type="hidden" id="txt_po_val" />
     <?
	if ($data[0]==0) $company_name=""; else $company_name=" and a.company_name='$data[0]'";
	if ($data[1]==0) $buyer_name=""; else $buyer_name=" and a.buyer_name='$data[1]'";
	if ($data[2]==0) $job_cond=""; else $job_cond=" and a.job_no_prefix_num='$data[2]'";
	
	$cbo_year=str_replace("'","",$cbo_year);
	if($db_type==0)
	{
		if(trim($data[3])!=0) $year_cond=" and YEAR(a.insert_date)=$data[3]"; else $year_cond="";
	}
	else if($db_type==2)
	{
		$year_field_con=" and to_char(a.insert_date,'YYYY')";
		if(trim($data[3])!=0) $year_cond=" $year_field_con=$data[3]"; else $year_cond="";
	}
	
	if($db_type==0) $year_field="YEAR(a.insert_date) as year"; 
	else if($db_type==2) $year_field="to_char(a.insert_date,'YYYY') as year";
	else $year_field="";

	if($type==1) //Style Ref
	{
		$sql ="select a.id,a.style_ref_no,a.job_no_prefix_num as job_prefix,$year_field from wo_po_details_master a where a.status_active=1  $company_name $buyer_name $job_cond $year_cond"; 
		echo create_list_view("list_view", "Style Ref. No.,Job No,Year","300,100,100","550","310",0, $sql , "js_set_value", "id,style_ref_no", "", 1, "0", $arr, "style_ref_no,job_prefix,year", "","setFilterGrid('list_view',-1)","0","",1) ;	
	}
	else{ //
		 $sql ="select a.id,a.style_ref_no,a.job_no_prefix_num as job_prefix,$year_field,b.id as poid,grouping as ref_no from wo_po_details_master a,wo_po_break_down b where  a.id=b.job_id  and b.status_active=1 and b.grouping is not null $company_name $buyer_name $job_cond $year_cond"; 
		echo create_list_view("list_view", "Style Ref. No.,Job No,Ref. NO,Year","200,100,100,100","550","310",0, $sql , "js_set_value", "poid,ref_no", "", 1, "0", $arr, "style_ref_no,job_prefix,ref_no,year", "","setFilterGrid('list_view',-1)","0","",1) ;	
	}

	exit();	 
}

$tmplte=explode("**",$data);

if ($tmplte[0]=="viewtemplate") $template=$tmplte[1]; else $template=$lib_report_template_array[$_SESSION['menu_id']]['0'];
if ($template=="") $template=1;

if( $action=="report_generate" )
{
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process )); 
	//echo $txt_style;die;

	$company_name=str_replace("'","",$cbo_company_name);
	if(str_replace("'","",$cbo_buyer_name)==0)
	{
		if ($_SESSION['logic_erp']["data_level_secured"]==1)
		{
			if($_SESSION['logic_erp']["buyer_id"]!="") $buyer_id_cond=" and a.buyer_name in (".$_SESSION['logic_erp']["buyer_id"].")"; else $buyer_id_cond="";
		}
		else
		{
			$buyer_id_cond="";
		}
	}
	else
	{
		$buyer_id_cond=" and a.buyer_name=$cbo_buyer_name";//.str_replace("'","",$cbo_buyer_name)
	}
	
	$date_cond='';
	if(str_replace("'","",$txt_date_from)!="" && str_replace("'","",$txt_date_to)!="")
	{
		$start_date=str_replace("'","",$txt_date_from);
		$end_date=str_replace("'","",$txt_date_to); 
		$date_cond=" and b.pub_shipment_date between '$start_date' and '$end_date'";
	}
	$job_no=str_replace("'","",$txt_job_no);
	if ($job_no=="") $job_no_cond=""; else $job_no_cond=" and a.job_no_prefix_num in ($job_no) ";
	if(str_replace("'","",$txt_style)!="") $style=" and a.id in(".str_replace("'","",$txt_style).")"; else $style="";
	
	$cbo_year=str_replace("'","",$cbo_year);
	if($db_type==0)
	{
		if(trim($cbo_year)!=0) $year_cond=" and YEAR(a.insert_date)=$cbo_year"; else $year_cond="";
	}
	else if($db_type==2)
	{
		$year_field_con=" and to_char(a.insert_date,'YYYY')";
		if(trim($cbo_year)!=0) $year_cond=" $year_field_con=$cbo_year"; else $year_cond="";
	}
	
	$cbo_team_name=str_replace("'","",$cbo_team_name);
	$cbo_team_member=str_replace("'","",$cbo_team_member);//txt_ref_no*txt_poid
	$txt_ref_no=str_replace("'","",$txt_ref_no);
	$txt_poid=str_replace("'","",$txt_poid);
	if($txt_poid!="" && $txt_ref_no!="") 
	{
		if($txt_poid!="")  $ref_po_cond=" and b.id in($txt_poid)";else $ref_po_cond="";
	}
	else if($txt_poid=="" && $txt_ref_no!="") 
	{
		if($txt_ref_no!="")  $ref_po_cond=" and b.gropuping='$txt_ref_no' ";else $ref_po_cond="";
	}
	
	if($cbo_team_name==0) $team_name_cond=""; else $team_name_cond=" and a.team_leader='$cbo_team_name'";
	if($cbo_team_member==0) $team_member_cond=""; else $team_member_cond=" and a.dealing_marchant='$cbo_team_member'";
	
	if($template==1)
	{
		
		?>
		<style type="text/css">
           
            hr {
                border: 0; 
                background-color: #000;
                height: 1px;
            }  
        </style> 
        <?
		$con = connect();
		execute_query("DELETE FROM GBL_TEMP_ENGINE WHERE USER_ID = ".$user_id."  and ENTRY_FORM=148");

		
		
	$sql_result=sql_select("select a.id as job_id,a.job_no_prefix_num,b.id, a.job_no, a.company_name, a.buyer_name,a.agent_name, a.team_leader, a.dealing_marchant, a.style_ref_no, a.order_uom,b.pub_shipment_date,a.job_quantity,(a.job_quantity*avg_unit_price) as style_po_qty_val, (a.total_set_qnty*b.po_quantity) as po_quantity,(b.po_quantity*b.unit_price) as po_value,b.grouping as ref_no from wo_po_details_master a, wo_po_break_down b where a.id=b.job_id and a.company_name='$company_name' and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $date_cond $buyer_id_cond $team_name_cond $team_member_cond $job_no_cond $style  $year_cond $ref_po_cond order by a.job_no, b.pub_shipment_date");
	 
	
	$shipment_date_arr=array();$job_no_arr=array();$agent_tem_arr=array();$dealing_marchant_arr=array();$buyer_name_arr=array();$style_ref_arr=array();$job_qty_arr=array();		
	$rate_data_arr=array();$month_year="";$delivery_value_arr=array();$uom_arr=array();$po_color_arr_data=array();
	$month_arr=array();$po_qty_arr=array();$job_qty_val_arr=array();
	foreach($sql_result as $row)
	{
	$shipment_date_arr[$row[csf("job_no")]]=$month_data;
	$month=date("Y-m",strtotime($row[csf("pub_shipment_date")]));
	$job_no_arr[$row[csf("job_no")]]=$row[csf("job_no")];
	$ref_no_arr[$row[csf("job_no")]].=$row[csf("ref_no")].',';
	$agent_tem_arr[$row[csf("job_no")]]=$row[csf("agent_name")];
	$dealing_marchant_arr[$row[csf("job_no")]]=$row[csf("dealing_marchant")];
	$buyer_name_arr[$row[csf("job_no")]]=$row[csf("buyer_name")];
	$style_ref_arr[$row[csf("job_no")]]=$row[csf("style_ref_no")];
	$job_qty_arr[$row[csf("job_no")]]=$row[csf("job_quantity")];
	$job_qty_val_arr[$row[csf("job_no")]]=$row[csf("style_po_qty_val")];
	$uom_arr[$row[csf("job_no")]]=$row[csf("order_uom")];
	$month_arr[$row[csf("job_no")]][$month]+=$row[csf("po_quantity")];
	$po_qty_arr[$row[csf("job_no")]][$month]+=$row[csf("po_value")];
	$po_id=$row[csf('id')];
	$jobIdArr[$row[csf('job_id')]]=$row[csf('job_id')];
	
	} //var_dump($po_color_arr_data);
	
	 
	fnc_tempengine("GBL_TEMP_ENGINE", $user_id, 150, 1, $jobIdArr, $empty_arr);//PO ID Ref from=1
	
	$sql_data_work=sql_select("select a.job_no, b.bulletin_type,b.system_no from  wo_po_details_master a,ppl_gsd_entry_mst b,gbl_temp_engine g  where a.style_ref_no=b.style_ref and a.id=g.ref_val  and g.user_id=".$user_id." and g.ref_from=1 and g.entry_form=150  and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0  ");
	foreach($sql_data_work as $row)
	{
		$work_study_arr[$row[csf('job_no')]]['bulletin_type']=$row[csf('bulletin_type')];
		$work_study_arr[$row[csf('job_no')]]['system_no']=$row[csf('system_no')];
	} 
	$costing_per_arr=array();
		$sql_data=sql_select("select a.job_no, a.costing_per from  wo_pre_cost_mst a,wo_po_details_master b,gbl_temp_engine g where a.id=b.job_id and b.id=g.ref_val  and g.user_id=".$user_id." and g.ref_from=1 and g.entry_form=150 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0  ");
		foreach($sql_data as $row)
		{
			 
			$costing_per_arr[$row[csf('job_no')]]['costing_per']=$row[csf('costing_per')];
		
		} 
			
		$po_color_arr=array();
		$sql_data=sql_select("select a.job_no_mst, a.color_number_id  from wo_po_color_size_breakdown a,wo_po_break_down b,gbl_temp_engine g where  a.job_id=b.job_id and b.id=a.po_break_down_id and a.job_id=g.ref_val  and g.user_id=".$user_id." and g.ref_from=1 and g.entry_form=150 and a.status_active=1 and a.is_deleted=0   and b.status_active=1 and b.is_deleted=0   group by a.job_no_mst, a.color_number_id ");
		foreach($sql_data as $row)
		{
			$tot_color=count($row[csf('color_number_id')]);
			$po_color_arr[$row[csf('job_no_mst')]]['color']+=$tot_color;
			
		}//var_dump($po_color_arr);die;
		 

	ob_start(); 

	 
	execute_query("DELETE FROM GBL_TEMP_ENGINE WHERE USER_ID = ".$user_id." and   ENTRY_FORM=150");
	oci_commit($con);
	disconnect($con); 
	?>
        <div style="width:1270px">
        <fieldset style="width:100%;">	
            <table width="1260">
                <tr class="form_caption">
                    <td colspan="13" align="center">Style Wise Shipment Report</td>
                </tr>
                <tr class="form_caption">
                    <td colspan="13" align="center"><? echo $company_library[$company_name]; ?></td>
                </tr>
            </table>
            <table id="table_header_1" class="rpt_table" width="1260" cellpadding="0" cellspacing="0" border="1" rules="all">
                <thead>
                    <th width="40">SL</th>
                    <th width="110">Job No</th> 
					<th width="70">In. Ref. No</th>
                    <th width="100">Buyer</th>
                    <th width="110"><div style="word-break:break-all">Dealing Merchant</div></th>
                    <th width="110">Agent</th>
                    <th width="110">Style Name</th>
                    <th width="80"><div style="word-break:break-all">No of Color</div></th>
                    <th width="100"><div style="word-break:break-all">Style Total Qty</div></th>
                    <th width="50">UOM</th>
                   
                    <th width="80">Shipment Month</th>
					 
					<th width="100">Bulletin ID</th>
					<th width="100">Bulletin Type</th>
                    <th width="">Order Qty</th>
                   
                </thead>
            </table>
            <div style="width:1280px; max-height:400px; overflow-y:scroll" id="scroll_body">
                <table class="rpt_table" width="1260" cellpadding="0" cellspacing="0" border="1" rules="all" id="table_body">
                <?
                $i=1; $total_order_qnty=0; 				  
              //  $tot_rows=count($nameArray);
			 	$month_check=array();$month_check2=array();$month_check3=array();
                foreach($job_no_arr as $job_no)
                {
                    if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
					
					$agent_name=$agent_tem_arr[$job_no];
					//echo $agent_name;
					$buyer_name=$buyer_name_arr[$job_no];
					$dealing_marchant=$dealing_marchant_arr[$job_no];
					$style_ref=$style_ref_arr[$job_no];
					$job_qty=$job_qty_arr[$job_no];
					$ref_no=rtrim($ref_no_arr[$job_no],',');
					$ref_nos=implode(",",array_unique(explode(",",$ref_no)));
					
					$job_qty_val=$job_qty_val_arr[$job_no];
					$uom_style=$uom_arr[$job_no];
					$rate_data=$rate_data_arr[$job_no];
					//$po_cond_id= explode(",",$po_color_arr_data2[$job_no]);
					$tot_po_color=$po_color_arr[$job_no]['color'];

					$system_no=$work_study_arr[$job_no]['system_no'];
					$bulletin_type=$work_study_arr[$job_no]['bulletin_type'];
					
					if($uom_style==58)
					{
					$pcs_qty="Order Qty in Pcs";	
					}
					else
					{
						$pcs_qty="";	
					}
                ?>
                    <tr bgcolor="<? echo $bgcolor;?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor;?>')" id="tr_<? echo $i; ?>">
                        <td width="40"><? echo $i; ?></td>
                        <td width="110"  align="center"><p><? echo $job_no;//$row[csf('job_no_prefix_num')]; ?></p></td>
						 <td width="70"  align="center"><p><? echo $ref_nos;//$row[csf('job_no_prefix_num')]; ?></p></td>
                        <td width="100" ><div style="word-break:break-all"><? echo $buyer_short_name_library[$buyer_name]; ?></div></td>
                        <td width="110"><div style="word-break:break-all"><? echo $dealing_merchant_array[$dealing_marchant]; ?></div></td>
                        <td width="110"><div style="word-break:break-all"><? echo $agent_arr_library[$agent_name]; ?></div></td>
                        <td width="110"><div style="word-break:break-all"><? echo $style_ref; ?></div></td>
                        <td width="80">
                            <p>
                             <? echo $tot_po_color; ?>  
                            </p>
                        </td>
                        <td width="100" align="right" >
                       <div style="word-break:break-all; font-size:small">
                            <?
							 //$avg_rate=$row[csf('avg_unit_price')]; 
                                echo number_format($job_qty,2); 
                                $total_job_qnty+=$job_qty;
                            ?>
                            </div>
                        </td>
                        <td width="50" align="center" title="<? echo $pcs_qty; ?>"><p><? echo $unit_of_measurement[$uom_style]; ?></p></td>
                       
                        <td width="80"   align="center">
                        <div style="word-break:break-all; font-size:small">
                        <?
							$month_full_name="";
							foreach($month_arr[$job_no] as $key=>$val)
							{ 
								$month_full_name=date("F",strtotime($key));
								 echo $month_full_name.'<hr><br />';
							}
						?>
                        </div>
                       </td>
					   
						<td width="100" align="right">   <div style="word-break:break-all; font-size:small">
						<? 
						  echo $system_no;
						?> </div></td>
						 <td width="100" align="center" title="<? //echo $pcs_qty; ?>"><p><?  echo $bulletin_type_arr[$bulletin_type]; ?></p></td>

                          <td width="" align="right" title="Order Qty in Pcs">
                          <div style="word-break:break-all; font-size:small">
                          <?
                          foreach($month_arr[$job_no] as $key=>$val)
							{ 
								$delivery_po_qty=$val;
								echo number_format($delivery_po_qty,2).'<hr><br />';
								$total_delivery_qty+=$delivery_po_qty;
							}
							//echo $job_no;
                          ?>
                            </div>
                        </td>
                       
                        
                    </tr>
                <?
                $i++;
                }
                ?>
                </table>
                <table class="rpt_table" width="1260" id="report_table_footer" cellpadding="0" cellspacing="0" border="1" rules="all">
                    <tfoot>
                        <th width="40"></th>
                        <th width="110"></th>
						<th width="70"></th>
                        <th width="100"></th>
                        <th width="110"></th>
                        <th width="110"></th>
                        <th width="110"></th>
                        <th width="80" align="right">Total</th>
                        <th width="100" align="right" id="">  <div style="word-break:break-all; font-size:small"><? echo number_format($total_job_qnty,2); ?></div></th>
                        <th width="50"></th>
						<th width="80"></th>
                        
                       
                       
                        <th width="100">  <div style="word-break:break-all; font-size:small"><? //echo number_format($total_delivery_val,2); ?></div></th>
						<th width="100">  <div style="word-break:break-all; font-size:small"><? //echo number_format($total_delivery_val,2); ?></div></th>
						<th width="">  <div style="word-break:break-all; font-size:small"><? echo number_format($total_delivery_qty,2); ?></div></th>
                    </tfoot>
                </table>
            </div>
            </fieldset>
        </div>
<?
	}
	
	$html = ob_get_contents();
    ob_clean();
    //$new_link=create_delete_report_file( $html, 2, $delete, "../../../" );
    foreach (glob("*.xls") as $filename) {
    //if( @filemtime($filename) < (time()-$seconds_old) )
    @unlink($filename);
    }
    //---------end------------//
    $name=time();
    $filename=$user_id."_".$name.".xls";
    $create_new_doc = fopen($filename, 'w');	
    $is_created = fwrite($create_new_doc, $html);
    echo "$html####$filename"; 
	exit();	
}
?>