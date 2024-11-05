<?
header('Content-type:text/html; charset=utf-8');
session_start();
if( $_SESSION['logic_erp']['user_id'] == "" ) { header("location:login.php"); die; }

include('../../../includes/common.php');
include('../../../includes/class4/class.conditions.php');
include('../../../includes/class4/class.reports.php');
include('../../../includes/class4/class.trims.php');

$permission=$_SESSION['page_permission'];
$data=$_REQUEST['data'];
$action=$_REQUEST['action'];
$user_id=$_SESSION['logic_erp']['user_id'];

//========== start ========

if ($action=="load_drop_down_buyer")
{
	//echo $data;
	echo create_drop_down( "cbo_buyer_name", 150, "select buy.id,buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company='$data' $buyer_cond and buy.id in (select  buyer_id from  lib_buyer_party_type where party_type in (1,3,21,90))  order by buyer_name","id,buyer_name", 1, "-- Select Buyer --", $selected, "load_drop_down( 'requires/demand_for_accessories_controller', this.value+'_'+document.getElementById('cbo_company_name').value, 'load_drop_down_season_buyer', 'season_td');load_drop_down( 'requires/demand_for_accessories_controller', this.value, 'load_drop_down_brand', 'brand_td');" );
	exit();
}

if ($action=="load_drop_down_season_buyer")
{
	$datas=explode('_',$data);
	echo create_drop_down( "cbo_season_name", 150, "select id, season_name from lib_buyer_season where buyer_id='$datas[0]' and status_active =1 and is_deleted=0 order by season_name ASC","id,season_name", 1, "-Season-", $selected, "" );
	exit();
}

if ($action=="load_drop_down_brand")
{
	list($buyer_id,$width)=explode('_',$data);
	$width=($width)?$width:150;
	echo create_drop_down( "cbo_brand", $width, "select id, brand_name from lib_buyer_brand brand where buyer_id='$buyer_id' and status_active =1 and is_deleted=0 $brand_cond order by brand_name ASC","id,brand_name", 1, "-Brand-", $selected, "" );
	exit();
}

if($action=="load_drop_down_dealing_merchant")
{
	echo create_drop_down( "cbo_dealing_merchant", 150, "select b.id,b.team_member_name from lib_marketing_team a,lib_mkt_team_member_info b where b.team_id='$data' and a.id=b.team_id and a.PROJECT_TYPE=2 and b.status_active =1 and a.is_deleted=0 and a.status_active =1 and b.is_deleted=0 order by b.team_member_name","id,team_member_name", 1, "--Select Merchant--", $selected, "" );
	exit();
}


if ($action=="order_popup")
{
  	echo load_html_head_contents("Popup Info","../../../", 1, 1, $unicode);
	extract($_REQUEST);
	?>
    <script>
	var selected_id = new Array;
	var selected_name = new Array;
	var selected_style_name = new Array();

	function check_all_data()
	{

		var tbl_row_count = document.getElementById( 'list_view' ).rows.length;
		tbl_row_count = tbl_row_count - 0;
		//alert(tbl_row_count);
		for( var i = 1; i <= tbl_row_count; i++ ) 
		{
			//if($("#search"+i).is(':visible'))
			//{
				var onclickString = $('#tr_' + i).attr('onclick');
				var paramArr = onclickString.split("'");
				var functionParam = paramArr[1];
				js_set_value( functionParam );
			//}
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
	
	function js_set_value( strCon ) 
	{
		var splitSTR = strCon.split("_");
		var str = splitSTR[0];
		var selectID = splitSTR[1];
		var selectStyle = splitSTR[2];
		toggle( document.getElementById( 'tr_' + str ), '#FFFFCC' );
		//alert(selectID+"="+selected_id+"="+jQuery.inArray( selectID,selected_id));
				
		if( jQuery.inArray( selectID,selected_id) == -1 )
		{
			selected_id.push( selectID );
			selected_name.push( str );					
			selected_style_name.push( selectStyle );					
		}
		else
		{
			for( var i = 0; i < selected_id.length; i++ )
			{
				if( selected_id[i] == selectID ) break;
			}
			selected_id.splice( i, 1 );
			selected_name.splice( i, 1 ); 
			selected_style_name.splice( i, 1 ); 
		}
		
		var id = ''; var name = ''; var style = '';
		for( var i = 0; i < selected_id.length; i++ )
		{
			id += selected_id[i] + ',';
			name += selected_name[i] + ','; 
			style += selected_style_name[i] + ','; 
		}
		id 	 = id.substr( 0, id.length - 1 );
		name = name.substr( 0, name.length - 1 ); 
		style = style.substr( 0, style.length - 1 ); 
		$('#txt_job_id').val( id );
		$('#txt_job_sl_no').val( name );
		$('#txt_style_ref').val( style );
		//alert(style)
	}
	
	function fn_set_prev_data()
	{
		//alert(1);return;
		var prev_id_arr=$('#txt_job_id').val().split(",");
		var prev_sl_arr=$('#txt_job_sl_no').val().split(",");
		var prev_stype_arr=$('#txt_style_ref').val().split(",");
		var id_ref
		for( var i = 0; i < prev_id_arr.length; i++ )
		{
			id_ref = prev_sl_arr[i]+'_'+prev_id_arr[i]+'_'+prev_stype_arr[i]; 
			js_set_value(id_ref);
		}
	}
	</script>
    <?
	$company_id=trim(str_replace("'","",$company_id));
	$cbo_buyer_name=trim(str_replace("'","",$cbo_buyer_name));
	$cbo_team_leader=trim(str_replace("'","",$cbo_team_leader));
	$cbo_dealing_merchant=trim(str_replace("'","",$cbo_dealing_merchant));
	$cbo_season_name=trim(str_replace("'","",$cbo_season_name));
	$cbo_season_year=trim(str_replace("'","",$cbo_season_year));
	$cbo_brand=trim(str_replace("'","",$cbo_brand));
	$hidd_job_sl_no=trim(str_replace("'","",$hidd_job_sl_no));
	$hidd_job_id=trim(str_replace("'","",$hidd_job_id));
	$txt_style_no=trim(str_replace("'","",$txt_style_no));
	$update_id=trim(str_replace("'","",$update_id));
	
	//echo $txt_style_no;
	//echo $company_id.'='.$cbo_buyer_name.'='.$cbo_team_leader.'='.$cbo_dealing_merchant.'='.$cbo_season_name.'='.$cbo_season_year.'='.$cbo_brand;die;
	//body_wash_color
	//$prev_cond="";
	//if($update_id!="") $prev_cond=" and id <> $update_id";
	$prev_sql= "select job_id from scm_demand_mst where status_active=1 and is_deleted=0 and company_id=$company_id and buyer_id=$cbo_buyer_name and team_leader_id=$cbo_team_leader and deling_merchant_id=$cbo_dealing_merchant";
	//echo $prev_sql;die;
	$prev_sql_result=sql_select($prev_sql);
	$prev_job_id_arr=array();
	foreach($prev_sql_result as $row)
	{
		$job_id_arr=explode(",",$row[csf("job_id")]);
		foreach($job_id_arr as $job_id)
		{
			$prev_job_id_arr[$job_id]=$job_id;
		}
	}
	//echo "<pre>";print_r($prev_job_id_arr);die;
	$sql_cond="";
	if($cbo_season_name) $sql_cond.=" and a.season_buyer_wise=$cbo_season_name";
	if($cbo_season_year) $sql_cond.=" and a.season_year=$cbo_season_year";
	if($cbo_brand) $sql_cond.=" and a.brand_id=$cbo_brand";
	if(count($prev_job_id_arr)>0) $sql_cond.=" and a.id not in(".implode(",",$prev_job_id_arr).")";
	$sql= "select a.id as job_id, a.job_no_prefix_num, a.job_no, a.style_ref_no, a.body_wash_color, b.GROUPING as int_ref_no, sum(b.po_quantity) as job_qnty 
	from wo_po_details_master a, wo_po_break_down b, wo_pre_cost_mst c
	where a.job_no=b.job_no_mst and b.job_no_mst=c.job_no and a.job_no=c.job_no and a.garments_nature=3 and a.status_active=1 and b.status_active=1 and c.status_active=1 and c.is_deleted=0 and b.shiping_status<>3 and a.company_name=$company_id and a.buyer_name=$cbo_buyer_name and a.team_leader=$cbo_team_leader and a.dealing_marchant=$cbo_dealing_merchant $sql_cond 
	group by a.id, a.job_no_prefix_num, a.job_no, a.style_ref_no, a.body_wash_color, b.GROUPING
	order by a.id desc";
	//echo $sql;//die;
	$color_arr=return_library_array( "select id, color_name from lib_color  where status_active=1 and is_deleted=0", "id", "color_name");
	$arr=array(2=>$color_arr);
	echo  create_list_view("list_view", "Master Style,Style,Body/Wash Color,Job,Job Qty", "120,150,150,110","720","360",0, $sql , "js_set_value", "job_id,style_ref_no", "", 1, "0,0,body_wash_color,0,0", $arr , "int_ref_no,style_ref_no,body_wash_color,job_no,job_qnty", "",'setFilterGrid("list_view",-1);','0,0,0,0,2','',1) ;
	echo "<input type='hidden' id='txt_job_id' value='".$hidd_job_id."' />";
	echo "<input type='hidden' id='txt_job_sl_no' value='".$hidd_job_sl_no."' />";
	echo "<input type='hidden' id='txt_style_ref' value='".$txt_style_no."' />";
	?>
    <script>fn_set_prev_data()</script>
    <?
    exit();
}


//========== start CS Number ========
if ($action=="system_popup")
{

	echo load_html_head_contents("Popup Info", "../../../", 1, 1,$unicode,'','');
	extract($_REQUEST);
	?>
	<script>
		
	
		function js_set_value( id, com_id, job_id )
		{
			document.getElementById('selected_id').value=id;
			document.getElementById('selected_company').value=com_id;
			document.getElementById('selected_job').value=job_id;
			parent.emailwindow.hide();
		}
		function fn_show()
		{
			var cs_no=$("#cs_no").val();
			if(cs_no=="")
			{
				if(form_validation('txt_date_from*txt_date_to','CS Date Range')==false )
				{
					return;
				}
			}
			show_list_view ( document.getElementById('cs_no').value+'_'+document.getElementById('txt_date_from').value+'_'+document.getElementById('txt_date_to').value+'_'+document.getElementById('cbo_company_id').value, 'create_cs_search_list_view', 'search_div', 'demand_for_accessories_controller', 'setFilterGrid(\'search_div\',-1)');
			setFilterGrid('tbl_list_search',-1);
		}
    </script>
    </head>
    <body>
    <div align="center" style="width:100%;" >
    <form name="searchorderfrm_1"  id="searchorderfrm_1" autocomplete="off">
        <table width="750" cellspacing="0" cellpadding="0" border="1" rules="all" class="rpt_table" align="center">
            <thead>
                <tr>
                    <th>Demand No</th>
                    <th colspan="2">Demand Date Range</th>
					<th><input type="reset" name="reset" id="reset" value="Reset" style="width:100px" class="formbutton" />
					<input type="hidden" name="id_field" id="id_field" value="" />
                    <input type="hidden" name="cbo_company_id" id="cbo_company_id" value="<?= $company_id;?>" />
                    </th>
                </tr>        
            </thead>
            <tr class="general">
                <td> 
					<input name="cs_no" id="cs_no" class="text_boxes" style="width:120px">
                </td>
                <td><input name="txt_date_from" id="txt_date_from" class="datepicker" style="width:120px" placeholder="From Date"></td>
                <td><input name="txt_date_to" id="txt_date_to" class="datepicker" style="width:120px" placeholder="To Date"></td> 
                <td align="center">
                	<input type="button" name="button2" class="formbutton" value="Show" onClick="fn_show()" style="width:100px;" />
                    <input type="hidden" id="selected_id">
                    <input type="hidden" id="selected_company">
                    <input type="hidden" id="selected_job">
                </td>
        	</tr>
            <tr>
                <td align="center" colspan="4"><? echo load_month_buttons(1); ?></td>
            </tr>
        </table>
		<br>
        <div id="search_div"></div>
    </form>
    </div>
    </body>
      <script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
    </html>
    <?
    exit();
}

if($action=="create_cs_search_list_view")
{
	//echo $data;die;
	$cs_num="";$date_cond ="";$year_cond="";
	list($cs_no,$cs_start_date,$cs_end_date,$cbo_company_id) = explode('_', $data);
	if ($cs_no!='') {$cs_num=" and sys_number like '%$cs_no'";}
	if ($cs_start_date != '' && $cs_end_date != '') 
	{
        if ($db_type == 0) {
            $date_cond = "and demand_date '" . change_date_format($cs_start_date, 'yyyy-mm-dd') . "' and '" . change_date_format($cs_end_date, 'yyyy-mm-dd') . "'";
        } else if ($db_type == 2) {
            $date_cond = "and demand_date between '" . change_date_format($cs_start_date, '', '', 1) . "' and '" . change_date_format($cs_end_date, '', '', 1) . "'";
		}

    }
	
	/*if($cbo_year_selection>0)
	{
		if($db_type==0)
		{
			$year_cond=" and YEAR(cs_date) =$cbo_year_selection ";
		}
		else
		{	
			$year_cond=" and to_char(cs_date,'YYYY') =$cbo_year_selection ";
		}
	}*/

	$company_arr=return_library_array( "select id, company_name from lib_company",'id','company_name');
	$buyer_arr=return_library_array( "select id, buyer_name from lib_buyer",'id','buyer_name');
	$sql_cond="";
	if($cbo_company_id) $sql_cond=" and company_id=$cbo_company_id";
	$sql= "select id, sys_number, sys_number_prefix_num, demand_date, company_id, buyer_id, job_id from scm_demand_mst where status_active=1 and is_deleted=0 $cs_num $date_cond $sql_cond order by id DESC";
	//echo $sql;die;
	$sql_result= sql_select($sql);
	
	?>
	<table width="750" cellpadding="0" cellspacing="0" border="1" class="rpt_table" rules="all">
        <thead>
            <tr>
                <th width="50">SL</th>
                <th width="150">Demand No</th>
                <th width="80">Demand Suffix</th>
                <th width="100">Demand Date</th>
                <th width="150">Company</th>
                <th>Buyer</th>
            </tr>
        </thead>
	</table>
	<table width="750" cellpadding="0" cellspacing="0" border="1" class="rpt_table" rules="all" id="tbl_list_search">
		<tbody>
		<div style="width:730px; overflow-y:scroll; max-height:280px">
		<?			
            $i = 1;
            foreach($sql_result as $row)
            {
                if ($i%2==0) {$bgcolor="#FFFFFF";} else{ $bgcolor="#E9F3FF";}
                ?>
                <tr bgcolor="<? echo $bgcolor;?>" style="text-decoration:none; cursor:pointer;" id="search<? echo $i;?>" onClick="js_set_value( '<? echo $row[csf('id')]; ?>','<? echo $row[csf('company_id')]; ?>','<? echo $row[csf('job_id')]; ?>')" >  
                    <td align="center" width="50"><? echo $i; ?></td>
                    <td align="center" width="150"><p><? echo $row[csf('sys_number')]; ?></p></td>
                    <td align="center"  width="80"><p><? echo $row[csf('sys_number_prefix_num')]; ?></p></td>
                    <td align="center" width="100"><p><? echo change_date_format($row[csf('demand_date')]); ?></td>
                    <td align="center" width="150"><p><? echo $company_arr[$row[csf('company_id')]]; ?></p></td>
                    <td align="center"><p><? echo $buyer_arr[$row[csf('buyer_id')]]; ?></p></td>

                </tr>
                <?
                $i++;
            }
            ?>
        </div>
		</tbody>
	</table>
	<?
	exit();
} 

if ($action=="populate_data_from_search_popup")
{
	$data_array=sql_select("select id, sys_number, sys_number_prefix, sys_number_prefix_num, entry_form, company_id, buyer_id, season_id, season_year, brand_id, team_leader_id, deling_merchant_id, job_id, style_ref_no, demand_date, remarks from scm_demand_mst where id='$data' and is_deleted=0 and status_active=1");
	$supp_mult_arr='';
	$basis='';
	foreach ($data_array as $row)
	{
		echo "document.getElementById('update_id').value = '".$row[csf("id")]."';\n"; 
		echo "document.getElementById('txt_system_id').value = '".$row[csf("sys_number")]."';\n";  
		echo "document.getElementById('cbo_company_name').value = '".$row[csf("company_id")]."';\n";  
		echo "document.getElementById('cbo_buyer_name').value = '".$row[csf("buyer_id")]."';\n";
		echo "load_drop_down( 'requires/demand_for_accessories_controller', ".$row[csf("buyer_id")].", 'load_drop_down_brand', 'brand_td'); \n";
		echo "load_drop_down( 'requires/demand_for_accessories_controller', ".$row[csf("buyer_id")]."+'_'+".$row[csf("company_id")].", 'load_drop_down_season_buyer', 'season_td'); \n";
		echo "document.getElementById('cbo_season_name').value = '".$row[csf("season_id")]."';\n";  
		echo "document.getElementById('cbo_season_year').value = '".$row[csf("season_year")]."';\n";  
		echo "document.getElementById('cbo_brand').value = '".$row[csf("brand_id")]."';\n";
		echo "document.getElementById('cbo_team_leader').value = '".$row[csf("team_leader_id")]."';\n";  
		echo "document.getElementById('cbo_dealing_merchant').value = '".$row[csf("deling_merchant_id")]."';\n";
		echo "document.getElementById('hidd_job_id').value = '".$row[csf("job_id")]."';\n";  
		echo "document.getElementById('txt_style_no').value = '".$row[csf("style_ref_no")]."';\n";
		  
		echo "document.getElementById('txt_demand_date').value = '".change_date_format($row[csf("demand_date")])."';\n";
		echo "document.getElementById('txt_remarks').value = '".$row[csf("remarks")]."';\n";
	}
	exit();
}
//========== End CS Number ========

if ($action=="save_update_delete")
{
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process )); 
	
	//echo "10**5=$operation=";die;
	

	if($operation==0) // Insert Here----------------------------------------------------------
	{
		$con = connect();
		if($db_type==0)
		{
			mysql_query("BEGIN");
		}
	
		$mst_id=return_next_id("id", "scm_demand_mst", 1);
		
		if($db_type==0) $insert_date_con="YEAR(insert_date)=".date('Y',time())."";
		else if($db_type==2) $insert_date_con="TO_CHAR(insert_date,'YYYY')=".date('Y',time())."";
		
		$new_sys_no=explode("*",return_mrr_number( str_replace("'","",$cbo_company_name), '', 'DFA', date("Y",time()), 5, "select sys_number_prefix,sys_number_prefix_num from scm_demand_mst where $insert_date_con and entry_form=479 order by id desc ", "sys_number_prefix", "sys_number_prefix_num" ));

		$field_array_mst="id, sys_number, sys_number_prefix, sys_number_prefix_num, entry_form, company_id, buyer_id, season_id, season_year, brand_id, team_leader_id, deling_merchant_id, job_id, style_ref_no, demand_date,cs_req_date, remarks, inserted_by, insert_date, status_active, is_deleted";
		
		$txt_requisition_mst=implode(",",array_unique(explode(",",chop(str_replace("'","",$txt_requisition_mst),","))));
		$data_array_mst="(".$mst_id.",'".$new_sys_no[0]."','".$new_sys_no[1]."','".$new_sys_no[2]."',479,".$cbo_company_name.",".$cbo_buyer_name.",".$cbo_season_name.",".$cbo_season_year.",".$cbo_brand.",".$cbo_team_leader.",".$cbo_dealing_merchant.",".$hidd_job_id.",".$txt_style_no.",".$txt_demand_date.",".$txt_cs_req_date.",".$txt_remarks.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."',1,0)";
		//  echo "10**INSERT INTO scm_demand_mst (".$field_array_mst.") VALUES ".$data_array_mst; 
		// die;

		$field_array_dtls="id, mst_id, main_group_id, item_group_id, pre_cost_dtls_id, brand_supplier, item_description, nominate_supplier_id, uom, req_qty, stock_qty, req_rate, req_amount, job_id, job_no, style_no, sub_date, remarks, inserted_by, insert_date, status_active, is_deleted";
		
		$id_dtls=return_next_id("id", "scm_demand_dtls", 1);
		$data_array_dtls='';
		$dtls_id_arr=array();
		for($i=1; $i<=$row_num; $i++)
		{
			$dtlsUpdateId="dtlsUpdateId_".$i;
			$preCostDtlsId="preCostDtlsId_".$i;
			$itemGroupId="itemGroupId_".$i;
			$mainGroupId="mainGroupId_".$i;
			$nominatedSup="nominatedSup_".$i;
			$uom ="uom_".$i;
			$brandSup="brandSup_".$i;
			$description="description_".$i;
			
			$txtReqQty="txtReqQty_".$i;
			$txtStockQty ="txtStockQty_".$i;
			$txtRate="txtRate_".$i;
			$txtAmount="txtAmount_".$i;
			
			$jobId ="jobId_".$i;
			$jobNo ="jobNo_".$i;
			$styleNo ="styleNo_".$i;

			$txtDate ="txtDate_".$i;
			$txtRemarks ="txtRemarks_".$i;
			
			
			if ($data_array_dtls!='') {$data_array_dtls .=",";}
			$data_array_dtls .="(".$id_dtls.",".$mst_id.",'".$$mainGroupId."','".$$itemGroupId."','".$$preCostDtlsId."','".$$brandSup."','".$$description."','".$$nominatedSup."','".$$uom."','".$$txtReqQty."','".$$txtStockQty."','".$$txtRate."','".$$txtAmount."','".$$jobId."','".$$jobNo."','".$$styleNo."','".$$txtDate."','".$$txtRemarks."',".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."',1,0)";
				$dtls_id_arr[$id_dtls]=$id_dtls;
				$id_dtls++;
			
			
		}
		
		// echo "10**INSERT INTO scm_demand_mst (".$field_array_mst.") VALUES ".$data_array_mst;oci_rollback($con);disconnect($con);die; 
		// echo "10**INSERT INTO scm_demand_dtls (".$field_array_dtls.") VALUES ".$data_array_dtls; oci_rollback($con);disconnect($con);die; 
		
		$rID=sql_insert("scm_demand_mst",$field_array_mst,$data_array_mst,0);
		$rID1=sql_insert("scm_demand_dtls",$field_array_dtls,$data_array_dtls,0);
		// echo '10**'.$rID.'**'.$rID1;oci_rollback($con);disconnect($con);die;
		
		if($db_type==0)
		{
			if($rID==1 && $rID1==1)
			{
				mysql_query("COMMIT");  
				echo "0**".$new_sys_no[0]."**".$mst_id."**".implode(',',$dtls_id_arr);
			}
			else
			{
				mysql_query("ROLLBACK"); 
				echo "10**";
			}
		}
		else if($db_type==2 || $db_type==1 )
		{
			if($rID==1 && $rID1==1)
			{
				oci_commit($con);  
				echo "0**".$new_sys_no[0]."**".$mst_id."**".implode(',',$dtls_id_arr);
			}
			else
			{
				oci_rollback($con);
				echo "10**";
			}
		}
		disconnect($con);
		die;
	}
	elseif($operation==1) // Update Here----------------------------------------------------------
	{
		
		$con = connect();
		if($db_type==0)
		{
			mysql_query("BEGIN");
		}
		//cbo_company_name*cbo_buyer_name*cbo_season_name*cbo_season_year*cbo_brand*cbo_team_leader*cbo_dealing_merchant*hidd_job_id*txt_style_no*txt_demand_date*txt_remarks*update_id*txt_system_id
	//'&preCostDtlsId_' + i + '=' + $('#preCostDtlsId_'+i).val() + '&itemGroupId_' + i + '=' + $('#itemGroupId_'+i).val() + '&mainGroupId_' + i + '=' + $('#mainGroupId_'+i).val() + '&nominatedSup_' + i + '=' + $('#nominatedSup_'+i).val()+ '&uom_' + i + '=' + $('#uom_'+i).val()+ '&brandSup_' + i + '=' + $('#brandSup_'+i).text()+ '&description_' + i + '=' + $('#description_'+i).text()+ '&txtReqQty_' + i + '=' + $('#txtReqQty_'+i).val() + '&txtStockQty_' + i + '=' + $('#txtStockQty_'+i).val() + '&txtRate_' + i + '=' + $('#txtRate_'+i).val()+ '&txtAmount_' + i + '=' + $('#txtAmount_'+i).val();
		$field_array_mst="demand_date*cs_req_date*remarks*updated_by*update_date";
		$data_array_mst="".$txt_demand_date."*".$txt_cs_req_date."*".$txt_remarks."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'";

		$field_array_dtls="id, mst_id, main_group_id, item_group_id, pre_cost_dtls_id, brand_supplier, item_description, nominate_supplier_id, uom, req_qty, stock_qty, req_rate, req_amount, job_id, job_no, style_no, sub_date, remarks, inserted_by, insert_date, status_active, is_deleted";
		
		
		$field_array_dtls_update="sub_date*remarks*updated_by*update_date";
		
		
		$id_dtls=return_next_id("id", "scm_demand_dtls", 1);
		$data_array_dtls='';
		$dtls_id_arr=array();
		$mst_id=str_replace("'","",$update_id);
		for($i=1; $i<=$row_num; $i++)
		{
			$dtlsUpdateId="dtlsUpdateId_".$i;
			$preCostDtlsId="preCostDtlsId_".$i;
			$itemGroupId="itemGroupId_".$i;
			$mainGroupId="mainGroupId_".$i;
			$nominatedSup="nominatedSup_".$i;
			$uom ="uom_".$i;
			$brandSup="brandSup_".$i;
			$description="description_".$i;
			
			$txtReqQty="txtReqQty_".$i;
			$txtStockQty ="txtStockQty_".$i;
			$txtRate="txtRate_".$i;
			$txtAmount="txtAmount_".$i;
			
			$jobId ="jobId_".$i;
			$jobNo ="jobNo_".$i;
			$styleNo ="styleNo_".$i;

			$txtDate ="txtDate_".$i;
			$txtRemarks ="txtRemarks_".$i;
			
			if($$dtlsUpdateId==""){
				if ($data_array_dtls!='') {$data_array_dtls .=",";}
				$data_array_dtls .="(".$id_dtls.",".$mst_id.",'".$$mainGroupId."','".$$itemGroupId."','".$$preCostDtlsId."','".$$brandSup."','".$$description."','".$$nominatedSup."','".$$uom."','".$$txtReqQty."','".$$txtStockQty."','".$$txtRate."','".$$txtAmount."','".$$jobId."','".$$jobNo."','".$$styleNo."','".$$txtDate."','".$$txtRemarks."',".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."',1,0)";
				$dtls_id_arr[$id_dtls]=$id_dtls;
				$id_dtls++;
			}
			else{
				$dtls_update_ID[]=$$dtlsUpdateId;
				$data_array_dtls_update[$$dtlsUpdateId]=explode("*",("'".$$txtDate."'*'".$$txt_remarks."'*"."".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'"));
				$dtls_id_arr[$$dtlsUpdateId]=$$dtlsUpdateId;
			}
			
			
		}

		
		$flag=1;
		$rID=sql_update("scm_demand_mst",$field_array_mst,$data_array_mst,"id","".$update_id."",0);
		if($flag==1 && $rID==1){$flag=1;}else{$flag=0;}
		
		
		if(count($dtls_update_ID)){$deleteWhereCon=" and id not in(".implode(',',$dtls_update_ID).")";}
		$rID1=execute_query("delete from scm_demand_dtls where mst_id =".$update_id." $deleteWhereCon ",0);
		if($flag==1 && $rID1==1){$flag=1;}else{$flag=0;}
		
		if($data_array_dtls!=''){
			$rID2=sql_insert("scm_demand_dtls",$field_array_dtls,$data_array_dtls,0);
			if($flag==1 && $rID2==1){$flag=1;}else{$flag=0;}	
		}
		
		if(count($dtls_update_ID)>0)
		{
			$rID3=execute_query(bulk_update_sql_statement("scm_demand_dtls","id",$field_array_dtls_update,$data_array_dtls_update,$dtls_update_ID),1);
			if($flag==1 && $rID3==1){$flag=1;}else{$flag=0;}
		}
			
		//echo "10**$rID**$rID1**$rID2**$rID3";oci_rollback($con);die;
		
		//execute_query("delete from COMMON_PHOTO_LIBRARY where FORM_NAME='demand_for_accessories' and MASTER_TBLE_ID in(".implode(',',$dtls_id_arr).")",0);
		
		// echo "10**INSERT INTO scm_demand_dtls (".$field_array_dtls.") VALUES ".$data_array_dtls; 
		// echo "10**".$rID.'='.$rID1.'='.$rID2."</br>";oci_rollback($con);disconnect($con);die;
		if($db_type==0)
		{
			if($flag==1)
			{
				mysql_query("COMMIT");  
				echo "1**".str_replace("'",'',$txt_system_id)."**".str_replace("'",'',$update_id)."**".implode(',',$dtls_id_arr);
			}
			else
			{
				mysql_query("ROLLBACK"); 
				echo "10**";
			}
		}
		else if($db_type==2 || $db_type==1 )
		{
			if($flag==1)
			{
				oci_commit($con);  
				echo "1**".str_replace("'",'',$txt_system_id)."**".str_replace("'",'',$update_id)."**".implode(',',$dtls_id_arr);
			}
			else
			{
				oci_rollback($con);
				echo "10**";
			}
		}
	
		//check_table_status( $_SESSION['menu_id'],0);
		disconnect($con);
		die;
	}
	else if ($operation==2) // Delete Here----------------------------------------------------------  
	{
		$con = connect();
		if($db_type==0)
		{
			mysql_query("BEGIN");
		}
		
		/*$cs_approved=return_field_value("approved","scm_demand_mst","id=$update_id","approved");
		if($cs_approved==1)
		{
			echo "11**CS Approved, Delete Not Allow";disconnect($con);oci_rollback($con);die;
		}*/
		

		$field_array="status_active*is_deleted*updated_by*update_date";
		$data_array="0*1*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'";
		
		$rID=sql_delete("scm_demand_mst",$field_array,$data_array,"id","".$update_id."",0);
		$rID1=sql_delete("scm_demand_dtls",$field_array,$data_array,"mst_id","".$update_id."",0);
		// echo "10**".$rID.'='.$rID1.'='.$rID2."</br>"; die;
		if($db_type==0)
		{
			if($rID==1 && $rID1==1)
			{
				mysql_query("COMMIT");  
				echo "2**".str_replace("'",'',$txt_system_id)."**".str_replace("'",'',$update_id);;
			}
			else
			{
				mysql_query("ROLLBACK"); 
				echo "10**";
			}
		}
		else if($db_type==2 || $db_type==1 )
		{
			if($rID==1 && $rID1==1)
			{
				oci_commit($con);  
				echo "2**".str_replace("'",'',$txt_system_id)."**".str_replace("'",'',$update_id);;
			}
			else
			{
				oci_rollback($con);
				echo "10**";
			}
		}
		
		disconnect($con);
	}// Delete Here End ----------------------------------------------------------
	
}

//========== Generate CS ========
if($action=="load_cs_table")
{
	list($hidd_job_id, $company_id, $update_id) = explode('**', $data);
	$condition= new condition();
	if($hidd_job_id !=''){
		$condition->jobid_in ("$hidd_job_id");
	}
	$condition->init();
	$trim=new trims($condition);
	//echo $trim->getQuery();die;
	$totalqtyarray_arr=$trim->getQtyArray_by_precostdtlsid();
	//$totalqtyarray_arr=$trim->getQtyArray_by_jobAndPrecostdtlsid();
	//echo "<pre>";print_r($totalqtyarray_arr);die;
	$main_group_arr = return_library_array("select id, main_group_name from lib_main_group where is_deleted=0","id","main_group_name");
	$supplier_arr = return_library_array("select id, supplier_name from lib_supplier where is_deleted=0","id","supplier_name");
	
	
	//####### previous sql ########////
	//$sql = "select a.id, a.trim_group, b.item_name, a.brand_sup_ref, a.description, a.nominated_supp_multi as nominated_supp, a.cons_uom, a.rate, a.amount, a.remark, b.main_group_id
	//from wo_pre_cost_trim_cost_dtls a, lib_item_group b 
	//where a.trim_group=b.id and a.job_id in($hidd_job_id) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0
	//order by a.rate desc";
	
	$sql = "select m.id as job_id, m.job_no_prefix_num as job_no, m.style_ref_no as style_no, a.id, a.trim_group, b.item_name, a.brand_sup_ref, a.description, a.nominated_supp_multi as nominated_supp, a.cons_uom, a.rate, a.amount, a.remark, b.main_group_id
	from wo_po_details_master m, wo_pre_cost_trim_cost_dtls a, lib_item_group b 
	where m.id=a.job_id and a.trim_group=b.id and a.job_id in($hidd_job_id) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 
	order by a.rate desc";
	
	//echo $sql;die;
	$sql_result=sql_select($sql);
	$dtls_data=array();$dup_job_data=array();
	foreach($sql_result as $row)
	{
		$budge_group_arr[$row[csf("trim_group")]]=$row[csf("trim_group")];
	}
	
	if(count($budge_group_arr)>0)
	{
		$sql_item="select a.item_group_id as ITEM_GROUP_ID, a.brand_supplier as BRAND_SUPPLIER, a.item_description as ITEM_DESCRIPTION, a.unit_of_measure as UNIT_OF_MEASURE, d.order_uom as ORDER_UOM, a.current_stock as CURRENT_STOCK, b.id as PROP_ID, b.trans_type as TRANS_TYPE, b.quantity as QUANTITY
		from lib_item_group d, product_details_master a, order_wise_pro_details b, wo_po_break_down c 
		where d.id=a.item_group_id and a.id=b.prod_id and b.po_breakdown_id=c.id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.shiping_status=3 and a.company_id=$company_id and a.entry_form=24 and a.item_category_id=4 and a.item_group_id in(".implode(",",$budge_group_arr).")
		order by b.id";
		//echo $sql_item;die;
		$sql_item_result=sql_select($sql_item);
		$stock_data=array();
		foreach($sql_item_result as $row)
		{
			$ref=$row["ITEM_GROUP_ID"]."**".$row["BRAND_SUPPLIER"]."**".$row["ITEM_DESCRIPTION"]."**".$row["ORDER_UOM"];
			//$stock_data[$ref] +=$row["QUANTITY"];
			if($propotion_check[$row["PROP_ID"]]=="")
			{
				$propotion_check[$row["PROP_ID"]]=$row["PROP_ID"];
				if($row["TRANS_TYPE"]==1 || $row["TRANS_TYPE"]==4 || $row["TRANS_TYPE"]==5)
				{
					$stock_data[$ref] +=$row["QUANTITY"];
				}
				else
				{
					$stock_data[$ref] -=$row["QUANTITY"];
				}
			}
		}
	}
	
	foreach($sql_result as $row)
	{
		
		$data_ref=$row[csf("trim_group")]."**".$row[csf("brand_sup_ref")]."**".$row[csf("description")]."**".$row[csf("cons_uom")]."**".$row[csf("nominated_supp")];
		$stock_ref=$row[csf("trim_group")]."**".$row[csf("brand_sup_ref")]."**".$row[csf("description")]."**".$row[csf("cons_uom")];
		$dtls_data[$data_ref]["item_name"]=$row[csf("item_name")];
		$dtls_data[$data_ref]["trim_group"]=$row[csf("trim_group")];
		$dtls_data[$data_ref]["remark"]=$row[csf("remark")];
		$dtls_data[$data_ref]["main_group_id"]=$row[csf("main_group_id")];
		$dtls_data[$data_ref]["qnty"]+=$totalqtyarray_arr[$row[csf("id")]];
		$dtls_data[$data_ref]["stock_qnty"]+=$stock_data[$stock_ref];
		$dtls_data[$data_ref]["rate"]=$row[csf("rate")];
		$dtls_id_data[$data_ref]["dtls_id"][]=$row[csf("id")];
		if($dup_job_data[$data_ref][$row[csf("job_id")]]=="")
		{
			$dup_job_data[$data_ref][$row[csf("job_id")]]=$row[csf("job_id")];
			$dtls_data[$data_ref]["job_id"].=$row[csf("job_id")].",";
			$dtls_data[$data_ref]["job_no"].=$row[csf("job_no")].",";
			$dtls_data[$data_ref]["style_no"].=$row[csf("style_no")].",";
		}
	}
	//echo "<pre>";print_r($dtls_data);die;



	$dtls_table_sql="SELECT b.id,b.main_group_id,b.item_group_id, b.pre_cost_dtls_id, b.brand_supplier, b.item_description, b.nominate_supplier_id, b.uom, b.req_qty, b.stock_qty, b.req_rate, b.req_amount,a.job_id, b.job_no, b.sub_date, b.remarks from scm_demand_mst a, scm_demand_dtls b where a.id='$update_id' and a.id=b.mst_id and b.is_deleted=0 and b.status_active=1 and a.is_deleted=0 and a.status_active=1"; //mst_id='$update_id' //and to_char(a.job_id) in($hidd_job_id)
	//echo $dtls_table_sql; die;
	$dtls_table_result=sql_select($dtls_table_sql);

	$dlts_table_data=array();
	foreach($dtls_table_result as $row)
	{
		
		$data_ref=$row[csf("item_group_id")]."**".$row[csf("brand_supplier")]."**".$row[csf("item_description")]."**".$row[csf("uom")]."**".$row[csf("nominate_supplier_id")];
		
		$dlts_table_data[$data_ref]["sub_date"]=$row[csf("sub_date")];
		$dlts_table_data[$data_ref]["remarks"]=$row[csf("remarks")];
		$dlts_table_data[$data_ref]["id"]=$row[csf("id")];
		$all_dtls_id[$row[csf("id")]]=$row[csf("id")];
		
		
	}
	/*echo "<pre>";
	print_r($dlts_table_data);
	echo "</pre>"; die;*/

$imgSql="select MASTER_TBLE_ID,IMAGE_LOCATION from COMMON_PHOTO_LIBRARY where  FORM_NAME = 'demand_for_accessories' ".where_con_using_array($all_dtls_id,1,'MASTER_TBLE_ID')."";
//echo $imgSql;die;
$imgSqlResult=sql_select($imgSql);
$uploadedImagArr=array();
foreach($imgSqlResult as $row)
{
	$uploadedImagArr[$row[MASTER_TBLE_ID]]=$row[IMAGE_LOCATION];	
}


	?>
	<table cellpadding="0" cellspacing="0" border="1" class="rpt_table" rules="all"  id="tbl_details">
		<thead>
			<tr>
				<th width="30">SL</th>
				<th width="100">Main Group</th>
				<th width="140">Items Name</th>
				<th width="60">Item Ref/Code</th>
				<th width="120">Items Description</th>
                <th width="150">Nominated Supp.</th>
                <th width="50">UOM</th>
				<th width="80">Req. Qty.</th>
				<th width="80">Leftover Stock Qty</th>
				<th width="80" title="Minimum rate of selected styles">Costing Price</th>
				<th width="80">TTL. Amount</th>
				<th width="70" title="Merchandising Sample Submit Date To Procurement Dept"><input type="checkbox" onClick="all_date()" value='0' id="check_date"> Samp Sub Date</th>
				<th width="100">Remarks</th>
				<th>File</th>
			</tr>
		</thead>
		<tbody>
		<?
		$i=1;
		foreach($dtls_data as $data_key=>$data_val)
		{
			//echo $data_key."tttt"; 
			$data_ref=explode("**",$data_key);
			$item_group=$data_ref[0];
			$brand_sup=$data_ref[1];
			$description=$data_ref[2];
			$cons_uom=$data_ref[3];
			$nominated_sup=$data_ref[4];
			$dtls_id=implode(",",$dtls_id_data[$data_key]["dtls_id"]);
			$req_amount=$data_val['qnty']*$data_val['rate'];
			$job_id=chop($data_val['job_id'],",");
			$job_no=chop($data_val['job_no'],",");
			$style_no=chop($data_val['style_no'],",");
			$sub_date=$dlts_table_data[$data_key]["sub_date"];
			$remarks=$dlts_table_data[$data_key]["remarks"];
			$dtls_update_id=$dlts_table_data[$data_key]["id"];
			//echo $sub_date."ok"; 
			
			if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
			$supplier_all_arr=explode(',',$nominated_sup);
			$supp_name='';
			foreach($supplier_all_arr as $row)
			{
				if($supp_name!=''){$supp_name.=', '.$supplier_arr[$row];}else{$supp_name=$supplier_arr[$row];}
			}
			
				$bgcolor=($uploadedImagArr[$dtls_update_id])?"green":$bgcolor;
			?>
			<tr id="<? echo $i;?>" bgcolor="<? echo $bgcolor; ?>" style="">
				<td align="center" title="<?= $data_key;?>"><?= $i;?>
                <input type="hidden" value="<?=$dtls_update_id;?>" id="dtlsUpdateId_<?= $i;?>">
				<input type="hidden" name="preCostDtlsId_<?= $i;?>" id="preCostDtlsId_<?= $i;?>" value="<? echo $dtls_id;?>" >
                <input type="hidden" name="itemGroupId_<?= $i;?>" id="itemGroupId_<?= $i;?>" value="<? echo $item_group;?>" >
                <input type="hidden" name="mainGroupId_<?= $i;?>" id="mainGroupId_<?= $i;?>" value="<? echo $data_val["main_group_id"];?>" >
                <input type="hidden" name="uom_<?= $i;?>" id="uom_<?= $i;?>" value="<? echo $cons_uom ;?>" >
                <input type="hidden" name="nominatedSup_<?= $i;?>" id="nominatedSup_<?= $i;?>" value="<? echo $nominated_sup;?>" >
                <input type="hidden" name="jobId_<?= $i;?>" id="jobId_<?= $i;?>" value="<? echo $job_id ;?>" >
                <input type="hidden" name="jobNo_<?= $i;?>" id="jobNo_<?= $i;?>" value="<? echo $job_no ;?>" >
                <input type="hidden" name="styleNo_<?= $i;?>" id="styleNo_<?= $i;?>" value="<? echo $style_no ;?>" >
				</td>
				<td align="center" title="<?= $data_val["main_group_id"];?>"><p><? echo $main_group_arr[$data_val["main_group_id"]]; ?></p></td>
				<td align="center"  title="<?= $data_val["trim_group"];?>"><p><? echo $data_val["item_name"]; ?></p></td>
				<td id="brandSup_<?= $i;?>"><p><? echo $brand_sup; ?></p></td>
                <td id="description_<?= $i;?>"><p><? echo $description; ?></p></td>
                <td title="<?= $nominated_sup;?>"><p><? echo $supp_name; ?></p></td>
				<td align="center"><p><? echo $unit_of_measurement[$cons_uom]; ?></p></td>
				<td align="center"><input type="text" style="width:70px" name="txtReqQty_<?= $i;?>" id="txtReqQty_<?= $i;?>" class="text_boxes_numeric" value="<? echo number_format($data_val['qnty'],2,".","");?>" readonly></td>
				<td align="center"><input type="text" style="width:70px" name="txtStockQty_<?= $i;?>" id="txtStockQty_<?= $i;?>" class="text_boxes_numeric" value="<? echo number_format($data_val['stock_qnty'],2,".","") ;?>" onDblClick="fn_leftover('<?= $company_id."**".$data_key;?>')" readonly></td>
				<td align="center" title="<?= "Minimum Rate";?>"><input type="text" style="width:70px" name="txtRate_<?= $i;?>" id="txtRate_<?= $i;?>" class="text_boxes_numeric" value="<? echo number_format($data_val['rate'],2,".","");?>" readonly></td>
				<td align="center"><input type="text" style="width:70px" name="txtAmount_<?= $i;?>" id="txtAmount_<?= $i;?>" class="text_boxes_numeric" value="<? echo number_format($req_amount,2,".","");?>" readonly></td>
				<td align="center"><input type="text" style="width:70px" name="txtDate_<?= $i;?>" id="txtDate_<?= $i;?>" class="datepicker" value="<? echo change_date_format($sub_date);?>" readonly></td>
				<td align="center"><input type="text" style="width:70px" name="txtRemarks_<?= $i;?>" id="txtRemarks_<?= $i;?>" class="text_boxes" value="<? echo $remarks;?>" ></td>
                <td>
                <input type="file" name="demandFile_<?= $i;?>" id="demandFile_<?= $i;?>" style="width:60px;" accept=".doc,.pdf,.xls,.xlsx,.docx" onChange="document.getElementById('<? echo $i;?>').style.backgroundColor = 'green';">

                </td>
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

if($action=="leftover_popup")
{
	echo load_html_head_contents("Popup Info", "../../../", 1, 1,$unicode,'','');
	extract($_REQUEST);
	list($company_id, $trim_group, $brand_sup_ref, $description, $order_uom) = explode('**', $row_ref);
	
	$sql_item="select a.item_group_id as ITEM_GROUP_ID, a.brand_supplier as BRAND_SUPPLIER, a.item_description as ITEM_DESCRIPTION, a.unit_of_measure as UNIT_OF_MEASURE, d.order_uom as ORDER_UOM, a.current_stock as CURRENT_STOCK, b.id as PROP_ID, b.trans_type as TRANS_TYPE, b.quantity as QUANTITY, b.po_breakdown_id as PO_ID, f.job_no as JOB_NO, f.style_ref_no as STYLE_REF_NO, c.po_number as PO_NUMBER, d.item_name as ITEM_NAME
	from lib_item_group d, product_details_master a, order_wise_pro_details b, wo_po_break_down c, wo_po_details_master f 
	where d.id=a.item_group_id and a.id=b.prod_id and b.po_breakdown_id=c.id and c.job_no_mst=f.job_no and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.shiping_status=3 and a.company_id=$company_id and a.entry_form=24 and a.item_category_id=4 and a.item_group_id=$trim_group and a.brand_supplier='$brand_sup_ref' and a.item_description='$description' and d.order_uom=$order_uom
	order by b.id";
	//echo $sql_item;die;
	$sql_item_result=sql_select($sql_item);
	$po_stock_data=array();
	foreach($sql_item_result as $row)
	{
		if($propotion_check[$row["PROP_ID"]]=="")
		{
			$propotion_check[$row["PROP_ID"]]=$row["PROP_ID"];
			$po_stock_data[$row["PO_ID"]]["JOB_NO"] =$row["JOB_NO"];
			$po_stock_data[$row["PO_ID"]]["STYLE_REF_NO"] =$row["STYLE_REF_NO"];
			$po_stock_data[$row["PO_ID"]]["PO_NUMBER"] =$row["PO_NUMBER"];
			if($row["TRANS_TYPE"]==1 || $row["TRANS_TYPE"]==4 || $row["TRANS_TYPE"]==5)
			{
				$po_stock_data[$row["PO_ID"]]["STOCK"] +=$row["QUANTITY"];
			}
			else
			{
				$po_stock_data[$row["PO_ID"]]["STOCK"] -=$row["QUANTITY"];
			}
		}
		$item_name=$row["ITEM_NAME"];
	}
	//echo "<pre>";print_r($po_stock_data);die;
	?>
    <p style="font-size:14px; font-weight:bold;">Item Group : <?= $item_name;?>, Description : <?= $description;?>, Item Ref/Code : <?= $brand_sup_ref;?></p>
	<table width="600"cellpadding="0" cellspacing="0" border="1" class="rpt_table" rules="all" id="tbl_details">
		<thead>
			<tr>
				<th width="50">SL</th>
				<th width="140">Style</th>
				<th width="140">Job</th>
				<th width="140">Order</th>
				<th>Stock</th>
			</tr>
		</thead>
		<tbody>
		<?
		$i=1;
		foreach($po_stock_data as $po_id=>$data_val)
		{
			if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
			?>
			<tr id="<? echo $i;?>" bgcolor="<? echo $bgcolor; ?>">
				<td align="center" title="<?= $po_id;?>"><?= $i;?></td>
				<td><p><? echo $data_val["STYLE_REF_NO"]; ?></p></td>
				<td><p><? echo $data_val["JOB_NO"]; ?></p></td>
                <td><p><? echo $data_val["PO_NUMBER"]; ?></p></td>
                <td align="right"><? echo number_format($data_val["STOCK"],2); ?></p></td>
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



if($action=="print_button_variable_setting")
{
    $print_report_format=0;
    $print_report_format=return_field_value("format_id","lib_report_template","template_name ='".$data."' and module_id=19 and report_id=152 and is_deleted=0 and status_active=1");
   	$printButton=explode(',',$print_report_format);

	foreach($printButton as $id){

		if($id==78)$buttonHtml.='<input type="button" style="width:80px;" id="btn_Print"  onClick="fnc_print(1)" class="formbutton printReport" name="btn_Print" value="Print" />';
		
	}

   echo "document.getElementById('button_data_panel').innerHTML = '".$buttonHtml."';\n";
    exit();
}

if($action=="report_generate")
{
	$data=explode("**",$data);
	if($data[0]==1){
		$data_array=sql_select("select id as ID, sys_number as SYS_NUMBER, buyer_id as BUYER_ID, deling_merchant_id as DELING_MERCHANT_ID, job_id as JOB_ID, style_ref_no as STYLE_REF_NO, demand_date as DEMAND_DATE, remarks as REMARKS, inserted_by as INSERTED_BY,CS_REQ_DATE from scm_demand_mst where id='$data[1]' and is_deleted=0 and status_active=1");
		$inserted_by=$data_array[0]['INSERTED_BY'];
		$user_lib_name=return_library_array("SELECT id,user_full_name from user_passwd", "id", "user_full_name");
		$buyer_arr =return_library_array('SELECT id, buyer_name FROM lib_buyer where is_deleted=0 and status_active =1','id','buyer_name');
		$merchant_arr =return_library_array('SELECT b.id,b.team_member_name from lib_marketing_team a, lib_mkt_team_member_info b where a.id=b.team_id and a.PROJECT_TYPE=2 and b.status_active =1 and a.is_deleted=0 and a.status_active =1 and b.is_deleted=0','id','team_member_name');
		?>
		<style>
		table tr td{
			word-break: break-all;
		}
		*{
			margin:0;
			padding: 0;
		}
		</style>
		<table width="1150" cellpadding="0" align="left" cellspacing="0" border="0">
			<tr><td colspan="7" height="50"></td></tr>
			<tr>
				<td width="100"></td>
				<td width="100" align="right"><strong>Buyer: &nbsp;</strong></td>
				<td width="300" ><strong><?= $buyer_arr[$data_array[0]['BUYER_ID']]; ?></strong></td>
				<td width="25"></td>
				<td width="130" align="right"><strong>Demand No.: &nbsp;</strong></td>
				<td width="300" ><strong><?= $data_array[0]['SYS_NUMBER']; ?></strong></td>
				<td></td>
			</tr>
			<tr>
				<td></td>
				<td align="right"><strong>Merchandiser: &nbsp;</strong></td>
				<td ><strong><?= $merchant_arr[$data_array[0]['DELING_MERCHANT_ID']]; ?></strong></td>
				<td></td>
				<td align="right"><strong>Date: &nbsp;</strong></td>
				<td><strong><?= change_date_format($data_array[0]['DEMAND_DATE']); ?></strong></td>
				<td></td>
			</tr>
			<tr>
				<td></td>
				<td align="right"><strong>Remarks: &nbsp;</strong></td>
				<td><strong><?= $data_array[0]['REMARKS']; ?></strong></td>
				<td></td>
				<td><strong>CS Required Date</strong></td>
				<td><?=$data_array[0][CS_REQ_DATE];?></td>
				<td></td>
			</tr>
			<tr><td colspan="7" height="10"></td></tr>
		</table>
		<?
		$data_dtls_array=sql_select("SELECT id as ID,main_group_id as MAIN_GROUP_ID,item_group_id as ITEM_GROUP_ID, pre_cost_dtls_id as PRE_COST_DTLS_ID, brand_supplier as BRAND_SUPPLIER, item_description as ITEM_DESCRIPTION, nominate_supplier_id as NOMINATE_SUPPLIER_ID, uom as UOM, req_qty as REQ_QTY, stock_qty as STOCK_QTY, req_rate as REQ_RATE, req_amount as REQ_AMOUNT ,job_id as JOB_ID, job_no as JOB_NO, sub_date as SUB_DATE, remarks as REMARKS
		from scm_demand_dtls where mst_id='$data[1]' and is_deleted=0 and status_active=1");

		$supplier_data=sql_select("SELECT id as ID,supplier_name as NAME, contact_no as CONTACT_NO, email as EMAIL from lib_supplier where is_deleted=0 and status_active=1");
		$supp_info=array();
		foreach($supplier_data as $value){
			$supp_info[$value['ID']]['name']=$value['NAME'];
			$supp_info[$value['ID']]['contact']=$value['CONTACT_NO'];
			$supp_info[$value['ID']]['email']=$value['EMAIL'];
		}
		$sql_sc = sql_select("SELECT  b.id as ID, d.contract_no as CONTRACT_NO
		from wo_po_break_down a, wo_po_details_master b, com_sales_contract_order_info c,com_sales_contract d
		where a.job_no_mst = b.job_no and a.id=c.wo_po_break_down_id and c.com_sales_contract_id=d.id and b.id in(".$data_array[0]['JOB_ID'].") and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 and c.is_deleted=0 and c.status_active=1 group by b.id, d.contract_no ");
		$sql_lc = sql_select("SELECT  b.id as ID, d.export_lc_no as EXPORT_LC_NO
		from wo_po_break_down a, wo_po_details_master b, com_export_lc_order_info c,com_export_lc d
		where a.job_no_mst = b.job_no and a.id=c.wo_po_break_down_id and c.com_export_lc_id=d.id and b.id in(".$data_array[0]['JOB_ID'].") and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 and c.is_deleted=0 and c.status_active=1 group by b.id, d.export_lc_no");

		$sql_sc_arr=array();$sql_lc_arr=array();
		if(count($sql_sc)>0){
			foreach($sql_sc as $val){
				$sql_sc_arr[$val['ID']]=$val['CONTRACT_NO'];
			}
		}

		if(count($sql_lc)>0){
			foreach($sql_lc as $val){
				$sql_lc_arr[$val['ID']]=$val['EXPORT_LC_NO'];
			}
		}
		$main_group_arr = return_library_array("select id, main_group_name from lib_main_group where is_deleted=0","id","main_group_name");
		$item_arr = return_library_array("select id, item_name from lib_item_group where is_deleted=0","id","item_name");
		?>
			<table cellspacing="0" width="1450"  border="1" rules="all" class="rpt_table" >
				<thead bgcolor="#dddddd" align="center">
					<tr>
						<th colspan="18" align="center" ><strong>DEMAND/ REQUISITION</strong></th>
					</tr>
					<th width="30">SL No.</th>
					<th width="100" >Master LC Number</th>
					<th width="60" >Job NO</th>
					<th width="80" >Buyer Style</th>
					<th width="80" >Samp sub date</th>
					<th width="100" >Main Group</th>
					<th width="150" >Items Name</th>
					<th width="80" >Item Ref/Code</th>
					<th width="100" >Items Details</th>
					<th width="50" >UOM</th>
					<th width="60" >Required Qty </th>
					<th width="80" >Leftover Stock Qty</th>
					<th width="80" >For CS qty</th>
					<th width="50" >Costing Price</th>
					<th width="90" >Nominated/ Non-Nominated</th>
					<th width="90" >Vendor Contact number</th>
					<th width="80">Vendor Mail ID</th>
					<th width="100">Remarks</th>
				</thead>
				<?
				$i=1;
				$array_chk=array();
				foreach($data_dtls_array as $row)
				{
					if ($i%2==0)
					$bgcolor="#E9F3FF";
					else
					$bgcolor="#FFFFFF";
					if($row['NOMINATE_SUPPLIER_ID']==0){
						$nominate_supplier="Non-Nominated";
					}else{
						$supplier_all_arr=explode(',',$row['NOMINATE_SUPPLIER_ID']);
						$nominate_supplier=$supp_contact=$supp_email='';
						foreach($supplier_all_arr as $rows)
						{
							if($nominate_supplier!=''){$nominate_supplier.=', '.$supp_info[$rows]['name'];}else{$nominate_supplier=$supp_info[$rows]['name'];}
							if($supp_contact!='' && $supp_info[$rows]['contact']!=''){$supp_contact.=', '.$supp_info[$rows]['contact'];}else{$supp_contact=$supp_info[$rows]['contact'];}
							if($supp_email!='' && $supp_info[$rows]['email']!=''){$supp_email.=', '.$supp_info[$rows]['email'];}else{$supp_email=$supp_info[$rows]['email'];}
						}
					}
					$job_id=explode(',',$row['JOB_ID']);
					$master_lc_sc='';
					foreach($job_id as $value)
					{
						if($sql_sc_arr[$value]!=''){$master_lc_sc.=$sql_sc_arr[$value].", ";}
						if($sql_lc_arr[$value]!=''){$master_lc_sc.=$sql_lc_arr[$value].", ";}
					}
					$cs_qty=$row['REQ_QTY']-$row['STOCK_QTY'];

					$supplier_all_arr=explode(',',$row['NOMINATE_SUPPLIER_ID']);
					$rowspan=count($supplier_all_arr);
					foreach($supplier_all_arr as $val)
					{
						?>
							<tr bgcolor="<? echo $bgcolor; ?>">
						<?
						
						if(!in_array($row['ID'],$array_chk))
						{
							$array_chk[]=$row['ID'];
							?>
								<td rowspan="<?=$rowspan;?>"  align="center"><? echo $i; ?></td>
								<td rowspan="<?=$rowspan;?>" ><? echo chop($master_lc_sc,', ');?></td>
								<td rowspan="<?=$rowspan;?>" ><? echo $row['JOB_NO']; ?></td>
								<td rowspan="<?=$rowspan;?>" ><? echo $data_array[0]['STYLE_REF_NO']; ?></td>
								<td rowspan="<?=$rowspan;?>" ><? echo change_date_format($row['SUB_DATE']); ?></td>
								<td rowspan="<?=$rowspan;?>" ><? echo $main_group_arr[$row['MAIN_GROUP_ID']]; ?></td>
								<td rowspan="<?=$rowspan;?>" ><? echo $item_arr[$row['ITEM_GROUP_ID']]; ?></td>
								<td rowspan="<?=$rowspan;?>" ><? echo $row['BRAND_SUPPLIER']; ?></td>
								<td rowspan="<?=$rowspan;?>" ><? echo $row['ITEM_DESCRIPTION']; ?></td>
								<td rowspan="<?=$rowspan;?>" align="center"><? echo $unit_of_measurement[$row['UOM']]; ?></td>
								<td rowspan="<?=$rowspan;?>" align="right"><? echo number_format($row['REQ_QTY'],2); ?></td>
								<td rowspan="<?=$rowspan;?>" align="right"><? echo number_format($row['STOCK_QTY'],2); ?></td>
								<td rowspan="<?=$rowspan;?>" align="right"><? echo number_format($cs_qty,2); ?></td>
								<td rowspan="<?=$rowspan;?>" align="right"><? echo number_format($row['REQ_RATE'],2); ?></td>
							<?
						}
						
						?>
							<td ><? 
									if($val==0){ echo "Non-Nominated";}
									else{echo $supp_info[$val]['name'];}
								?>
                            </td>
							<td ><? echo $supp_info[$val]['contact'];?></td>
							<td ><? echo $supp_info[$val]['email'];?></td>
							<td ><? echo $row['REMARKS'];?></td>
							</tr>
						<?

					}
					 $i++;
				}
				?>
			</table><br>
		<?
		echo signature_table(235, $data[2], "1300",$data[3],50,$user_lib_name[$inserted_by]);
	}
    exit();
}