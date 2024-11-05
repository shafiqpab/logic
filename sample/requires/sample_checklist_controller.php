<?
header('Content-type:text/html; charset=utf-8');
session_start();
if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");
extract($_REQUEST);
$permission=$_SESSION['page_permission'];
include('../../includes/common.php');
$data=$_REQUEST['data'];
$action=$_REQUEST['action'];
$company_arr=return_library_array( "select id, company_short_name from lib_company",'id','company_short_name');
$buyer_arr=return_library_array( "select id, short_name from lib_buyer", "id", "short_name");
if ($action=="load_drop_down_buyer")
{
	echo create_drop_down( "cbo_buyer_name", 140, "select buy.id,buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company='$data' $buyer_cond  and buy.id in (select  buyer_id from  lib_buyer_party_type where party_type in (1,3,21,30,90)) order by buyer_name","id,buyer_name", 1, "-- Select Buyer --", $selected, "" ); 
	exit();
}

if ($action=="load_drop_down_buyer_checklist")
{
	echo create_drop_down( "cbo_buyer_name", 140, "select buy.id,buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company='$data' $buyer_cond  and buy.id in (select  buyer_id from  lib_buyer_party_type where party_type in (1,3,21,30,90)) order by buyer_name","id,buyer_name", 1, "-- Select Buyer --", $selected, "" ); 
	exit();
}

if ($action=="load_drop_down_gmts")
{
	$sql="select a.id,a.item_name from lib_garment_item a,sample_development_dtls b where b.sample_mst_id=$data and b.gmts_item_id=a.id group by a.id,a.item_name";
	$a=count(sql_select($sql)); 
	if($a>1)
	{  
	   echo create_drop_down( "cbo_garments_item", 130, $sql,"id,item_name", 1, "-- Select Item --", $selected, "" );
	}
	else
	{  
	   echo create_drop_down( "cbo_garments_item", 130, $sql,"id,item_name", 0, "-- Select Item --", $selected, "" ); 
	}
	exit();
}
 
if ($action=="load_drop_down_gmts_ch")
{
	$sql="select a.id,a.item_name from lib_garment_item a,sample_checklist_mst b where b.id=$data and b.gmts_item_id=a.id group by a.id,a.item_name";
	$a=count(sql_select($sql)); 
	if($a>1)
	{  
	   echo create_drop_down( "cbo_garments_item", 157, $sql,"id,item_name", 1, "-- Select Item --", $selected, "" );
	}
	else
	{  
	   echo create_drop_down( "cbo_garments_item", 157, $sql,"id,item_name", 0, "-- Select Item --", $selected, "" ); 
	}
	exit();
}

if($action=="requisition_id_popup")
{
	extract($_REQUEST);
	echo load_html_head_contents("Sample Requisition Info","../../", 1, 1, $unicode);
	?>
	<script>
		$(document).ready(function(e) {
            $("#txt_search_common").focus();
        });
		
		function js_set_value( mst_id )
		{
			document.getElementById('selected_job').value=mst_id;
			parent.emailwindow.hide();
		}
    </script>
    </head>
    <body>
        <div align="center" style="width:100%;" >
            <form name="searchsampledevelopmentfrm_1"  id="searchsampledevelopmentfrm_1" autocomplete="off">
                <table width="900" cellspacing="0" cellpadding="0" border="1" class="rpt_table" rules="all" align="center">
                    <thead>
                        <tr>
                            <th colspan="8"><? echo create_drop_down( "cbo_search_category", 140, $string_search_type,'', 1, "-- Search Catagory --" );?></th>
                        </tr>
                        <tr>
                            <th width="140">Company Name</th>
                            <th width="140">Buyer Name</th> 
                            <th width="80">Requisition No</th>   
                            <th width="100">Int. Ref. No </th>            	 
                            <th width="100">Style ID</th>
                            <th width="100">Style Name</th>
                            <th width="130" colspan="2">Est. Ship Date</th>
                            <th><input type="reset" name="reset" id="reset" class="formbutton" value="Reset" style="width:70px;" /></th>
                        </tr>
                    </thead>
                    <tr class="general">
                        <td> 
                            <input type="hidden" id="selected_job">
                            <? echo create_drop_down( "cbo_company_mst", 140, "select id,company_name from lib_company comp where status_active =1 and is_deleted=0 $company_cond order by company_name","id,company_name",1, "-- Select Company --", '',"load_drop_down( 'sample_checklist_controller', this.value, 'load_drop_down_buyer', 'buyer_td_req' );" ); ?>
                        </td>
                        <td id="buyer_td_req"><? echo create_drop_down( "cbo_buyer_name", 140, $blank_array,'', 1, "-- Select Buyer --" ); ?></td>
                        <td><input type="text" style="width:70px" class="text_boxes" name="txt_requisition_num" id="txt_requisition_num" /></td>
                         <td><input type="text" style="width:90px;" name="txt_int_ref_no" id="txt_int_ref_no" class="text_boxes" placeholder="Write"  /></td>
                        <td><input type="text" style="width:90px" class="text_boxes"  name="txt_style_id" id="txt_style_id" /></td>
                        <td><input type="text" style="width:90px" class="text_boxes"  name="txt_style_name1" id="txt_style_name1" /></td>
                        <td><input name="txt_date_from" id="txt_date_from" class="datepicker" style="width:55px" placeholder="From"></td> 
                        <td><input name="txt_date_to" id="txt_date_to" class="datepicker" style="width:55px" placeholder="To"></td> 
                        <td>
                        <input type="button" name="btn_show" id="btn_show" class="formbutton" value="Show" onClick="show_list_view ( document.getElementById('cbo_search_category').value+'_'+document.getElementById('txt_style_id').value+'_'+document.getElementById('cbo_company_mst').value+'_'+document.getElementById('cbo_buyer_name').value+'_'+document.getElementById('txt_date_from').value+'_'+document.getElementById('txt_date_to').value+'_'+document.getElementById('txt_style_name1').value+'_'+document.getElementById('txt_requisition_num').value+'_'+document.getElementById('cbo_year_selection').value+'_'+document.getElementById('txt_int_ref_no').value, 'create_requisition_id_search_list_view', 'search_div', 'sample_checklist_controller', 'setFilterGrid(\'list_view\',-1)')" style="width:70px;" />
                        </td>
                    </tr>
                    <tr>
                        <td align="center" colspan="8" valign="middle"><? echo load_month_buttons(1); ?></td>
                    </tr>
                </table>
            </form>
            <div id="search_div"></div>
        </div>
    </body>           
    <script src="../../includes/functions_bottom.js" type="text/javascript"></script>
    </html>
    <?
    exit();
}

if($action=="create_requisition_id_search_list_view")
{
	$data=explode('_',$data);

    $variable_setting_app= "select b.approval_need as approval_need  from approval_setup_mst a, approval_setup_dtls b where a.id=b.mst_id and b.page_id=26 and a.company_id ='$data[2]' and  a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.setup_date=(select max(setup_date) from approval_setup_mst where company_id='$data[2]' and status_active=1 and is_deleted=0 )";
	$variable_setting_app_sql=sql_select($variable_setting_app);
	$variable_app_value=$variable_setting_app_sql[0][csf("approval_need")];
	$variable_cond=($variable_app_value==1)? " and is_approved=1 and req_ready_to_approved=1 and is_acknowledge=1   " : "   and req_ready_to_approved=1   ";

	if ($data[2]!=0) $company=" and company_id='$data[2]'"; else { echo "Please Select Company First."; die; }
	if ($data[3]!=0) $buyer=" and buyer_name='$data[3]'"; else $buyer="";//{ echo "Please Select Buyer First."; die; }
	
	//echo $txt_int_ref_no; die;
	if($data[0]==1)
	{
		if (trim($data[1])!="") $style_id_cond=" and id='$data[1]'"; else $style_id_cond="";
		if ($data[6]!="") $style_cond=" and style_ref_no='$data[6]'"; else $style_cond="";
	}
	else if($data[0]==4 || $data[0]==0)
	{
		if (trim($data[1])!="") $style_id_cond=" and id like '%$data[1]%' "; else $style_id_cond="";
		if ($data[6]!="") $style_cond=" and style_ref_no like '%$data[6]%' "; else $style_cond="";
	}
	else if($data[0]==2)
	{
		if (trim($data[1])!="") $style_id_cond=" and id like '$data[1]%' "; else $style_id_cond="";
		if ($data[6]!="") $style_cond=" and style_ref_no like '$data[6]%' "; else $style_cond="";
	}
	else if($data[0]==3)
	{
		if (trim($data[1])!="") $style_id_cond=" and id like '%$data[1]' "; else $style_id_cond="";
		if ($data[6]!="") $style_cond=" and style_ref_no like '%$data[6]' "; else $style_cond="";
	}
	
	$year_cond="";
	if($db_type==0)
	{
		if ($data[4]!="" &&  $data[5]!="") $estimated_shipdate  = "and estimated_shipdate  between '".change_date_format($data[4], "yyyy-mm-dd", "-")."' and '".change_date_format($data[5], "yyyy-mm-dd", "-")."'"; else $estimated_shipdate ="";
		$yearCond="year(insert_date)";
		if(str_replace("'", "",$data[8])) $year_cond=" and year(insert_date)=$data[8]";
	}
	else if($db_type==2)
	{
		if ($data[4]!="" &&  $data[5]!="") $estimated_shipdate  = "and estimated_shipdate  between '".change_date_format($data[4], "yyyy-mm-dd", "-",1)."' and '".change_date_format($data[5], "yyyy-mm-dd", "-",1)."'"; else $estimated_shipdate ="";
		
		$yearCond="to_char(insert_date,'YYYY')";
		
		if(str_replace("'", "",$data[8])) $year_cond=" and  extract(year from insert_date)=$data[8]";
	}
	if ($data[7]!="") $requisition_num=" and requisition_number_prefix_num like '%$data[7]' "; else $requisition_num="";
	
	
	$buyer_arr=return_library_array( "select id, buyer_name from lib_buyer",'id','buyer_name');
	$comp=return_library_array( "select id, company_name from lib_company",'id','company_name');
	
	$dealing_marchant=return_library_array( "select id, team_member_name from lib_mkt_team_member_info",'id','team_member_name');
	$team_leader=return_library_array( "select id, team_leader_name from lib_marketing_team",'id','team_leader_name');
	
	
	
		$txt_int_ref_no=trim(str_replace("'","",$data[9]));
		if($txt_int_ref_no!="") $ref_cond=" and internal_ref like '%$txt_int_ref_no%'";else $ref_cond="";
		
		
		/*$dtls_found= "SELECT  b.style_id,a.grouping from wo_non_ord_samp_booking_mst a ,wo_non_ord_samp_booking_dtls b  where  a.booking_no=b.booking_no  and b.status_active=1 and b.is_deleted=0  and   a.entry_form_id=140 and  b.entry_form_id=140 $ref_cond  $company  $buyer  and a.booking_type=4 and  a.status_active=1 and a.is_deleted=0   ";
		$booking_dtls_arr=array();
		$internal_ref_arr=array();
		$style_id_in_cond="";
		foreach(sql_select($dtls_found) as $v)
		{
			$booking_dtls_arr[$v[csf("style_id")]]=$v[csf("style_id")];
			$internal_ref_arr[$v[csf("style_id")]]=$v[csf("grouping")];
		}
		
		//echo "<pre>";
		//print_r($internal_ref_arr);
		if($txt_int_ref_no!="")
		{
			$style_id=implode(",",$booking_dtls_arr);
			$style_id_in_cond=" and id in ($style_id)";
		}*/
	
	$arr=array (1=>$comp,2=>$buyer_arr,4=>$product_dept,6=>$dealing_marchant);
//echo $style_id_in_cond; die;
	 $sql= "select id, requisition_number_prefix_num, $yearCond as year, company_id, buyer_name, style_ref_no, product_dept, dealing_marchant,internal_ref from sample_development_mst where entry_form_id in (117,203,449)  $variable_cond and status_active=1 and  id not in(select requisition_id from sample_checklist_mst where status_active=1 and is_deleted=0)  and is_deleted=0  $company $buyer $ref_cond $style_id_cond $style_cond $estimated_shipdate $requisition_num $year_cond order by id DESC";
	 /*$sql= "select id, requisition_number_prefix_num, $yearCond as year, company_id, buyer_name, style_ref_no, product_dept, dealing_marchant from sample_development_mst where entry_form_id in (117,203,449)  $variable_cond and status_active=1 and  id not in(select requisition_id from sample_checklist_mst where status_active=1 and is_deleted=0)  and is_deleted=0 $company $buyer $style_id_cond $style_cond $estimated_shipdate $requisition_num $year_cond order by id DESC";*/
	echo  create_list_view("list_view", "Year,Requisition No,Buyer Name,Style Name,Product Department,Int. Ref. No ,Dealing Merchant", "60,140,140,100,90,90,90","900","240",0, $sql , "js_set_value", "id", "", 1, "0,0,buyer_name,0,product_dept,0,dealing_marchant", $arr, "year,requisition_number_prefix_num,buyer_name,style_ref_no,product_dept,internal_ref,dealing_marchant", "",'','0,0,0,0,0,0,0');
	exit();
}

if($action=="populate_data_from_requisition_search_popup")
{  
 	$res = sql_select("select id, requisition_number from sample_development_mst where id=$data and entry_form_id in (117,203,449) and is_deleted=0 and status_active=1"); 
  	foreach($res as $result)
	{ 
 		echo "load_drop_down( 'requires/sample_checklist_controller', '".$result[csf('id')]."', 'load_drop_down_gmts', 'gmts_td' );\n";
 		echo "$('#txt_requisition_id').val('".$result[csf('requisition_number')]."');\n";
		echo "$('#requisition_hidden_id').val('".$result[csf('id')]."');\n";
   	}
   	unlink($res);
 	exit();	
}	

if($action=="checklist_id_popup")
{
	extract($_REQUEST);
	echo load_html_head_contents("Sample Checklist  Info","../../", 1, 1, $unicode);
	?>
	<script>
		$(document).ready(function(e) {
            $("#txt_search_common").focus();
        });
		
		function js_set_value( mst_id )
		{
			document.getElementById('selected_job').value=mst_id;
			parent.emailwindow.hide();
		}
    </script>
    </head>
    <body>
        <div align="center" style="width:100%;" >
            <form name="searchsampledevelopmentfrm_1"  id="searchsampledevelopmentfrm_1" autocomplete="off">
            	<table width="770" cellspacing="0" cellpadding="0" border="1" rules="all" class="rpt_table" align="center">
                	<thead>
                        <tr>
                            <th colspan="8"><? echo create_drop_down( "cbo_search_category", 140, $string_search_type,'', 1, "-- Search Catagory --" ); ?></th>
                        </tr>
                        <tr>
                            <th width="140">Company Name</th>
                            <th width="140">Buyer Name</th> 
                            <th width="80">Requisition No</th>               	 
                            <th width="100">Style ID</th>
                            <th width="100">Style Name</th>
                            <th width="130" colspan="2">Checklist Date</th>
                            <th><input type="reset" name="reset" id="reset" class="formbutton" value="Reset" style="width:70px;" /></th>
                        </tr>
                    </thead>
                    <tr class="general">
                        <td> 
                            <input type="hidden" id="selected_job">
                            <? echo create_drop_down( "cbo_company_mst", 140, "select id,company_name from lib_company comp where status_active =1 and is_deleted=0 $company_cond order by company_name","id,company_name",1, "-- Select Company --", '',"load_drop_down( 'sample_checklist_controller', this.value, 'load_drop_down_buyer_checklist', 'buyer_td_checklist' );" ); ?> </td>
                        <td id="buyer_td_checklist"><?  echo create_drop_down( "cbo_buyer_name", 140, $blank_array,'', 1, "-- Select Buyer --" );?></td>
                        <td><input type="text" style="width:70px" class="text_boxes" name="txt_requisition_num" id="txt_requisition_num"  /></td>
                        
                        <td><input type="text" style="width:90px" class="text_boxes"  name="txt_style_id" id="txt_style_id" /></td>
                        <td><input type="text" style="width:90px" class="text_boxes"  name="txt_style_name1" id="txt_style_name1"  /></td>
                        <td><input name="txt_date_from" id="txt_date_from" class="datepicker" style="width:55px" placeholder="From"></td>
                        <td><input name="txt_date_to" id="txt_date_to" class="datepicker" style="width:55px" placeholder="To"></td> 
                        <td>
                        <input type="button" name="btn_show" id="btn_show" class="formbutton" value="Show" onClick="show_list_view ( document.getElementById('cbo_search_category').value+'_'+document.getElementById('txt_style_id').value+'_'+document.getElementById('cbo_company_mst').value+'_'+document.getElementById('cbo_buyer_name').value+'_'+document.getElementById('txt_date_from').value+'_'+document.getElementById('txt_date_to').value+'_'+document.getElementById('txt_style_name1').value+'_'+document.getElementById('txt_requisition_num').value+'_'+document.getElementById('cbo_year_selection').value, 'create_checklist_id_search_list_view', 'search_div', 'sample_checklist_controller', 'setFilterGrid(\'list_view\',-1)')" style="width:70px;" />
                        </td>
                    </tr>
                    <tr>
                    	<td colspan="8" align="center" valign="middle"><? echo load_month_buttons(1); ?></td>
                    </tr>
                </table>
            </form>
            <div id="search_div"></div>
        </div>
    </body>           
    <script src="../../includes/functions_bottom.js" type="text/javascript"></script>
    </html>
    <?
    exit();
}

if($action=="create_checklist_id_search_list_view")
{
	$data=explode('_',$data);
	if ($data[2]!=0) $company=" and b.company_id='$data[2]'"; else { echo "Please Select Company First."; die; }
	if ($data[3]!=0) $buyer=" and b.buyer_name='$data[3]'"; else $buyer="";//{ echo "Please Select Buyer First."; die; }
	if($data[0]==1)
	{
		if (trim($data[1])!="") $style_id_cond=" and b.id='$data[1]'"; else $style_id_cond="";
		if ($data[6]!="") $style_cond=" and b.style_ref_no='$data[6]'"; else $style_cond="";
		if ($data[7]!="") $requisition_num=" and b.requisition_number_prefix_num='$data[7]' "; else $requisition_num="";
	}
	else if($data[0]==4 || $data[0]==0)
	{
		if (trim($data[1])!="") $style_id_cond=" and b.id like '%$data[1]%' "; else $style_id_cond="";
		if ($data[6]!="") $style_cond=" and b.style_ref_no like '%$data[6]%' "; else $style_cond="";
		if ($data[7]!="") $requisition_num=" and b.requisition_number_prefix_num like '%$data[7]%' "; else $requisition_num="";
	}
	else if($data[0]==2)
	{
		if (trim($data[1])!="") $style_id_cond=" and id like '$data[1]%' "; else $style_id_cond="";
		if ($data[6]!="") $style_cond=" and b.style_ref_no like '$data[6]%' "; else $style_cond="";
		if ($data[7]!="") $requisition_num=" and b.requisition_number_prefix_num like '$data[7]%' "; else $requisition_num="";
	}
	else if($data[0]==3)
	{
		if (trim($data[1])!="") $style_id_cond=" and b.id like '%$data[1]' "; else $style_id_cond="";
		if ($data[6]!="") $style_cond=" and b.style_ref_no like '%$data[6]' "; else $style_cond="";
		if ($data[7]!="") $requisition_num=" and b.requisition_number_prefix_num like '%$data[7]' "; else $requisition_num="";
	}
	
	$year_cond="";
	if($db_type==0)
	{
		if ($data[4]!="" &&  $data[5]!="") $estimated_shipdate  = "and a.checklist_date  between '".change_date_format($data[4], "yyyy-mm-dd", "-")."' and '".change_date_format($data[5], "yyyy-mm-dd", "-")."'"; else $estimated_shipdate ="";
		if(str_replace("'", "", $data[8])) $year_cond=" and year(a.insert_date)=$data[8]";
		$yearCond="year(a.insert_date)";
	}
	else if($db_type==2)
	{
		if ($data[4]!="" &&  $data[5]!="") $estimated_shipdate  = "and a.checklist_date  between '".change_date_format($data[4], "yyyy-mm-dd", "-",1)."' and '".change_date_format($data[5], "yyyy-mm-dd", "-",1)."'"; else $estimated_shipdate ="";
		if(str_replace("'", "", $data[8])) $year_cond=" and  extract(year from a.insert_date)=$data[8]";
		$yearCond="to_char(a.insert_date,'YYYY')";
	}
	// if ($data[7]!="") $requisition_num=" and b.requisition_number_prefix_num like '%$data[7]' "; else $requisition_num="";
	
	$buyer_arr=return_library_array( "select id, buyer_name from lib_buyer",'id','buyer_name');
	$comp=return_library_array( "select id, company_name from lib_company",'id','company_name');
	
	$dealing_marchant=return_library_array( "select id, team_member_name from lib_mkt_team_member_info",'id','team_member_name');
	$team_leader=return_library_array( "select id, team_leader_name from lib_marketing_team",'id','team_leader_name');
	$gmts=return_library_array( "select id, item_name from lib_garment_item",'id','item_name');
	
	$arr=array (3=>$buyer_arr,5=>$gmts,6=>$team_leader,7=>$dealing_marchant);
	
	$sql= "select a.id, a.checklist_number_prefix_num, $yearCond as year, b.requisition_number_prefix_num, b.company_id, b.buyer_name, b.style_ref_no, a.gmts_item_id, b.dealing_marchant from sample_development_mst b,sample_checklist_mst a where b.entry_form_id in (117,203,449) and  a.status_active=1 and a.is_deleted=0 $company $buyer $style_id_cond $style_cond $estimated_shipdate $requisition_num $year_cond and a.requisition_id=b.id order by a.id DESC";
	 
	echo  create_list_view("list_view", "Year,Checklist No,Requisition No,Buyer Name,Style Name,Item,Dealing Merchant", "60,90,90,100,100,220,90","900","240",0, $sql , "js_set_value", "id", "", 1, "0,0,0,buyer_name,0,gmts_item_id,dealing_marchant", $arr , "year,checklist_number_prefix_num,requisition_number_prefix_num,buyer_name,style_ref_no,gmts_item_id,dealing_marchant", "",'','0,0,0,0,0,0,0') ;

	exit();
}

if($action=="populate_data_from_checklist_search_popup")
{  
 	$res = sql_select("select a.id,a.requisition_id,a.checklist_date,a.remarks,a.gmts_item_id,b.requisition_number,a.checklist_number,a.is_cutting,a.completion_status from sample_checklist_mst a,sample_development_mst b where a.id=$data and a.is_deleted=0 and a.status_active=1 and a.requisition_id=b.id");
 	 
  	foreach($res as $result)
	{ 
 		echo "load_drop_down( 'requires/sample_checklist_controller', '".$result[csf('id')]."', 'load_drop_down_gmts_ch', 'gmts_td' );\n";
 		
 		echo "$('#txt_requisition_id').val('".$result[csf('requisition_number')]."');\n";
 		echo "$('#txt_checklist_id').val('".$result[csf('checklist_number')]."');\n";
		echo "$('#requisition_hidden_id').val('".$result[csf('requisition_id')]."');\n";
		echo "$('#txt_remarks_mst').val('".$result[csf('remarks')]."');\n";
		echo "$('#update_id').val('".$result[csf('id')]."');\n";
		echo "$('#cbo_completion_status').val('".$result[csf('completion_status')]."');\n";
		echo "$('#txt_checklist_date').val('".change_date_format($result[csf('checklist_date')],'dd-mm-yyyy','-')."');\n";
		echo "fnc_load_tr('".$result[csf('id')]."');\n";
		if($result[csf('is_cutting')]==1)
		{
			echo "$('#cutting_approved_msg').html('Cutting Started...');\n";	
			echo "$('#txt_requisition_id').prop('disabled','true')".";\n";
			echo "$('#cbo_completion_status').prop('disabled','true')".";\n";
			echo "$('#cbo_garments_item').prop('disabled','true')".";\n";
			echo "$('#txt_checklist_date').prop('disabled','true')".";\n";
			//echo "$('#checklistDtls').prop('disabled','true')".";\n";  
			echo "$('#txt_checklist_id').removeAttr('disabled','')".";\n";
 		}

		if($result[csf('is_cutting')]!=1)
		{
			echo "$('#cutting_approved_msg').html('');\n";	
			echo "$('#txt_requisition_id').prop('disabled',false)".";\n";
			echo "$('#cbo_completion_status').prop('disabled',false)".";\n";
			echo "$('#cbo_garments_item').prop('disabled',false)".";\n";
			echo "$('#txt_checklist_date').prop('disabled',false)".";\n";
			echo "$('#checklistDtls').prop('disabled',false)".";\n";  
		}
    }
   	unlink($res);
 	exit();	
}	

if ($action=="save_update_delete_mst")
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
 		$id_mst=return_next_id( "id", "sample_checklist_mst", 1 ) ;
 		$cbo_company_name=return_field_value("company_id","sample_development_mst","entry_form_id in (117,203,449) and id=$requisition_hidden_id");

 		if($db_type==0) $yearCond="YEAR(insert_date)"; else if($db_type==2) $yearCond="to_char(insert_date,'YYYY')";
		
		$new_system_id=explode("*",return_mrr_number( str_replace("'","",$cbo_company_name), '', 'CK', date("Y",time()), 5, "select checklist_number_prefix, checklist_number_prefix_num from sample_checklist_mst where company_id=$cbo_company_name and $yearCond=".date('Y',time())." order by id desc", "checklist_number_prefix", "checklist_number_prefix_num" ));//txt_remarks_mst

 		$field_array="id, requisition_id, gmts_item_id, checklist_date, inserted_by, insert_date, status_active, is_deleted, completion_status, checklist_number_prefix, checklist_number_prefix_num, checklist_number, remarks,company_id";
		$data_array="(".$id_mst.",".$requisition_hidden_id.",".$cbo_garments_item.",".$txt_checklist_date.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."',1,0,".$cbo_completion_status.",'".$new_system_id[1]."',".$new_system_id[2].",'".$new_system_id[0]."',".$txt_remarks_mst.",".$cbo_company_name.")";
		$rID=sql_insert("sample_checklist_mst",$field_array,$data_array,1);
		 //echo $rID." data array ".$data_array; die;
		if($db_type==0)
		{
			if($rID )
			{
				mysql_query("COMMIT");  
				echo "0"."**".$id_mst."**".str_replace("'","",$requisition_hidden_id)."**".$new_system_id[0];
			}
			else
			{
				mysql_query("ROLLBACK"); 
				echo "10"."**".$id_mst;
			}
		}
		else if($db_type==2 || $db_type==1 )
		{
			if($rID )
			{
				oci_commit($con); 
				echo "0"."**".$id_mst."**".str_replace("'","",$requisition_hidden_id)."**".$new_system_id[0];;
			}
			else
			{
				oci_rollback($con);
				echo "10".$id_mst;
			}
		}
		disconnect($con);
		die;
	}
	else if ($operation==1)  // Update Here
	{	
		$con = connect();
		if($db_type==0)
		{
			mysql_query("BEGIN");
		}
  		$field_array="requisition_id*gmts_item_id*checklist_date*updated_by*update_date*status_active*is_deleted*remarks*completion_status";
		$data_array="".$requisition_hidden_id."*".$cbo_garments_item."*".$txt_checklist_date."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'*1*0*".$txt_remarks_mst."*".$cbo_completion_status."";
 		$rID=sql_update("sample_checklist_mst",$field_array,$data_array,"id","".$update_id."",1);
 		if($db_type==0)
		{
			if($rID )
			{
				mysql_query("COMMIT");  
				echo "1**".str_replace("'","",$update_id);
			}
			else
			{
				mysql_query("ROLLBACK"); 
				echo "10**".str_replace("'","",$update_id);
			}
		}
		else if($db_type==2 || $db_type==1 )
		{
			if($rID )
			{
				oci_commit($con);
 				echo "1**".str_replace("'","",$update_id)."**".str_replace("'","",$requisition_hidden_id);
			}
			else
			{
				oci_rollback($con);
				echo "10**".str_replace("'","",$update_id);
			}
		}
		disconnect($con);
		die;
	}
	else if ($operation==2)  // Delete Here
	{
		$con = connect();
		if($db_type==0)
		{
			mysql_query("BEGIN");
		}
		$field_array="updated_by*update_date*status_active*is_deleted";
		$data_array="".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'*0*1";
		$rID1=sql_delete("sample_checklist_mst",$field_array,$data_array,"id","".$update_id."",0);
		 
		if($db_type==0)
		{
			if($rID1)
			{
				mysql_query("COMMIT");  
				echo "2**".str_replace("'","",$update_id);
			}
			else
			{
				mysql_query("ROLLBACK"); 
				echo "10**".str_replace("'","",$update_id);
			}
		}
		else if($db_type==2 || $db_type==1 )
		{
			if($rID1)
			{
				oci_commit($con);
				echo "2**".str_replace("'","",$update_id);
			}
			else
			{
				oci_rollback($con); 
				echo "10**".str_replace("'","",$update_id);
			}
		}
		disconnect($con); die;
	}
}

if ($action=="save_update_delete_dtls")
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
 		$id_dtls=return_next_id( "id", "sample_checklist_dtls", 1 ) ;
 		$a=explode(',', $checkArrayNo);
 	   //  echo "1505***".$checkArrayNo;die;
  		$field_array="id,checklist_mst_id,checklist_id,submit_date,remarks,requisition_id,inserted_by,insert_date,status_active,is_deleted";
  		//$checklist_ids="";
		$z=1;
		for ($i=0;$i<count($a);$i++)
		{
			$checklist_ids=$a[$i];
			 
			$txtsubmitdate="txtsubmitdate_".$z;
			$txtremarks="txtremarks_".$z;
			$submitdated=str_replace("'","",$$txtsubmitdate);
			$submitdates=date('d-M-Y',strtotime($submitdated));
			if($submitdates =='' || $submitdates =='01-Jan-1970'){$submitdate=""; } else{ $submitdate=$submitdates;} 
			if ($i!=0) $data_array .=",";
			$data_array .="(".$id_dtls.",".$update_id.",".$checklist_ids.",'".$submitdate."',".$$txtremarks.",".$requisition_hidden_id.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."','1',0)";
			$id_dtls=$id_dtls+1;
			$z++;
			 
		 }
		  //echo "7200 ".$checklist_ids;die;
  		//echo "5**INSERT INTO sample_checklist_dtls(".$field_array.") VALUES ".$data_array; die;
 		$rID=sql_insert("sample_checklist_dtls",$field_array,$data_array,1);
 		if($db_type==0)
		{
			if($rID )
			{
				mysql_query("COMMIT");  
				echo "0**".$update_id;
			}
			else
			{
				mysql_query("ROLLBACK"); 
				echo "10**".$update_id;
			}
		}
		else if($db_type==2 || $db_type==1 )
		{
			if($rID )
			{
				oci_commit($con); 
				echo "0**".$update_id;
			}
			else
			{
				oci_rollback($con);
				echo "10**".$update_id;
			}
		}
		disconnect($con);
		die;
	}
	else if ($operation==1)  // Update Here
	{	
		$con = connect();
		if($db_type==0)
		{
			mysql_query("BEGIN");
		}
  		$field_array_st="is_deleted";
		$data_array_st="1";
		//  echo "3090ass ".trim($updateDtls,',');die;
		
		if(trim($updateDtls,',')!="") 
		{
 			$rID=sql_multirow_update("sample_checklist_dtls",$field_array_st,"1","checklist_mst_id",$update_id,0); 
			$rID2=sql_multirow_update("sample_checklist_dtls",$field_array_st,"0","id",trim($updateDtls,','),0);
		}

		if($updateDtls=="") 
		{
 			$rID=sql_multirow_update("sample_checklist_dtls",$field_array_st,"1","checklist_mst_id",$update_id,0); 
 		}
		$delete_dtls=execute_query( "delete from sample_checklist_dtls where id=$update_id",0);
		$id_dtls=return_next_id( "id", "sample_checklist_dtls", 1 ) ;
		$a=explode(',', $checkArrayNo);
   		$field_array_new="id,checklist_mst_id,checklist_id,submit_date,remarks,requisition_id,inserted_by,insert_date,status_active,is_deleted";
		$z=1;
		for ($i=0;$i<count($a);$i++)
    	{
			$txtsubmitdate="txtsubmitdate_".$z;
			$txtremarks="txtremarks_".$z;
			$submitdated=str_replace("'","",$$txtsubmitdate);
			$submitdates=date('d-M-Y',strtotime($submitdated));
			if($submitdates =='' || $submitdates =='01-Jan-1970'){$submitdate=""; } else{ $submitdate=$submitdates;} 
			/* $submitdated=str_replace("'","",$$txtsubmitdate);
			if($submitdated !='01-Jan-1970'){
				$submitdate=date('d-M-Y',strtotime($submitdated));
			} */
			
			$checklist_ids=$a[$i];
				if ($i!=0) $data_array_new .=",";
		    $data_array_new .="(".$id_dtls.",".$update_id.",".$checklist_ids.",'".$submitdate."',".$$txtremarks.",".$requisition_hidden_id.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."','1',0)";
			    $id_dtls=$id_dtls+1;

				$z++;
		
   		 }
 		/* $a=explode(',', $forNewSave);
   		$field_array_new="id,checklist_mst_id,checklist_id,submit_date,remarks,requisition_id,inserted_by,insert_date,status_active,is_deleted";
		$z=1;
		for ($i=0;$i<count($a);$i++)
    	{
			$txtsubmitdate="txtsubmitdate_".$z;
			$txtremarks="txtremarks_".$z;
			$submitdated=str_replace("'","",$$txtsubmitdate);
			$submitdates=date('d-M-Y',strtotime($submitdated));
			if($submitdates !=''){ $submitdate=$submitdates;} else{ $submitdate="";}
			
			$checklist_ids=$a[$i];
				if ($i!=0) $data_array_new .=",";
		    $data_array_new .="(".$id_dtls.",".$update_id.",".$checklist_ids.",'".$submitdate."',".$$txtremarks.",".$requisition_hidden_id.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."','1',0)";
			    $id_dtls=$id_dtls+1;

				$z++;
		
   		 } */
		
   		//echo "599**INSERT INTO sample_checklist_dtls(".$field_array_new.") VALUES ".$data_array_new; die;
		  
 		$rID3=sql_insert("sample_checklist_dtls",$field_array_new,$data_array_new,1);
		 //echo "77**".$rID."**".$rID2."**".$rID3."**".$delete_dtls; die;
  		if($db_type==0)
		{
			if($rID )
			{
				mysql_query("COMMIT");  
				echo "1**".str_replace("'","",$update_id);
			}
			else
			{
				mysql_query("ROLLBACK"); 
				echo "10**".str_replace("'","",$update_id);
			}
		}
		else if($db_type==2 || $db_type==1 )
		{
			if($rID)
			{
				oci_commit($con);
 				echo "1**".str_replace("'","",$update_id);
			}
			else
			{
				oci_rollback($con);
				echo "10**".str_replace("'","",$update_id);
			}
		}
		disconnect($con);
		die;
	}
	else if ($operation==2)  // Delete Here
	{
		$con = connect();
		if($db_type==0)
		{
			mysql_query("BEGIN");
		}
		$field_array="updated_by*update_date*status_active*is_deleted";
		$data_array="".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'*0*1";
		$rID1=sql_delete("sample_checklist_mst",$field_array,$data_array,"id","".$update_id."",0);
		 
		if($db_type==0)
		{
			if($rID1)
			{
				mysql_query("COMMIT");  
				echo "2**".str_replace("'","",$update_id);
			}
			else
			{
				mysql_query("ROLLBACK"); 
				echo "10**".str_replace("'","",$update_id);
			}
		}
		else if($db_type==2 || $db_type==1 )
		{
			if($rID1)
			{
				oci_commit($con);
				echo "2**".str_replace("'","",$update_id);
			}
			else
			{
				oci_rollback($con); 
				echo "10**".str_replace("'","",$update_id);
			}
		}
		disconnect($con); die;
	}
}

if($action=="load_php_dtls_form")
{
	$sql_sam="select id,checklist_mst_id,checklist_id,requisition_id,submit_date,remarks from sample_checklist_dtls where checklist_mst_id='$data' and is_deleted=0  and status_active=1 order by id ASC"; 
	//$existArr=return_library_array( "select checklist_id,id  from sample_checklist_dtls where checklist_mst_id='$data' and is_deleted=0  and status_active=1 ", "checklist_id", "id" );
	$sql_chk="select checklist_id,id,submit_date,remarks  from sample_checklist_dtls where checklist_mst_id='$data' and is_deleted=0  and status_active=1 ";
	$sql_result_chk =sql_select($sql_chk);
	foreach($sql_result_chk as $row)
	{
		$existArr[$row[csf('checklist_id')]]=$row[csf('id')];

		$submitRemarkArr[$row[csf('checklist_id')]]['submit_date']=$row[csf('submit_date')];
		$submitRemarkArr[$row[csf('checklist_id')]]['remarks']=$row[csf('remarks')];
	}

	 

	$sql_result =sql_select($sql_sam); 
	$i=1;
	//echo count($sql_result);die;
	if(count($sql_result)>0)
	{	
		foreach($sample_checklist_set as $id=>$name)
		{
			?>
			<tr id="tr_<? echo $i; ?>" style="height:10px;" >
                <th align="left"> 
					<?
                    if($existArr[$id]!='')
                    {
						//echo $submitRemarkArr[$id]['submit_date'].'=A';
                        ?>
						<b><input type="text" style="width: 80px;" class="datepicker" name="txtsubmitdate_<?php echo $i ?>" id="txtsubmitdate_<?php echo $i ?>" placeholder="Date" value="<? if($submitRemarkArr[$id]['submit_date']!='') echo $submitRemarkArr[$id]['submit_date']; else echo '';?>" />  &nbsp; 
						<input type="text" style="width: 100px;"  class="text_boxes" name="txtremarks_<?php echo $i ?>"  id="txtremarks_<?php echo $i ?>" placeholder="Remarks" value="<? if($submitRemarkArr[$id]['remarks']!='') echo $submitRemarkArr[$id]['remarks']; else echo '';?>"  />  <b>	
                        <input type="checkbox" checked name="txtCheckBoxId_<? echo $i ?>" id="txtCheckBoxId_<? echo $i ?>" />
                        <?
                    }
                    else
                    {
                        ?>
						<b><input type="text" style="width: 80px;" class="datepicker" name="txtsubmitdate_<?php echo $i ?>" id="txtsubmitdate_<?php echo $i ?>" placeholder="Date" />  &nbsp; 
						<input type="text" style="width: 100px;"  class="text_boxes" name="txtremarks_<?php echo $i ?>"  id="txtremarks_<?php echo $i ?>" placeholder="Remarks" />  <b>	
                        <input type="checkbox" name="txtCheckBoxId_<? echo $i ?>" id="txtCheckBoxId_<? echo $i ?>" />
                        <?
                    }
                    ?>
                    &nbsp; <? echo $name; ?> 
                    <input type="hidden" name="txtDocumentSetArrayid_<?php echo $i ?>" id="txtDocumentSetArrayid_<?php echo $i ?>" value="<? echo $id; ?>"/>
                    <input type="hidden" name="updateDtlsId_<?php echo $i ?>" id="updateDtlsId_<?php echo $i ?>" value="<? if($existArr[$id]!='') echo $existArr[$id]; else echo '';?>" />
                </th>
			</tr>
			<?
			$i++;
		}
	}
	exit();
}

if($action=="booking_data")
{
	$print_report_format=return_field_value("format_id","lib_report_template","template_name ='".$data."' and module_id=2 and report_id=90 and is_deleted=0 and status_active=1");
	
	$sql= "SELECT a.company_id, a.is_approved, b.booking_no from wo_non_ord_samp_booking_mst a, wo_non_ord_samp_booking_dtls b where  a.booking_no=b.booking_no and b.style_id='$data' and a.status_active=1 and a.is_deleted=0 and a.entry_form_id=140 and b.status_active=1 and b.is_deleted=0 and b.entry_form_id=140 group by a.company_id,  a.is_approved, b.booking_no";
	$sql_res=sql_select($sql);
	
	$company_id=$sql_res[0][csf('company_id')];
	$booking_no=$sql_res[0][csf('booking_no')];
	$is_approved=$sql_res[0][csf('is_approved')];
	$print_report_format=return_field_value("format_id","lib_report_template","template_name ='".$company_id."' and module_id=2 and report_id=90 and is_deleted=0 and status_active=1");
	
	$print_report_id=explode(",",$print_report_format);
	foreach($print_report_id as $button_id)
	{
		if($button_id==10) $booking_btn='1';
		if($button_id==17) $booking_btn='2';
	}
	
	echo $booking_no.'__'.$booking_btn.'__'.$company_id.'__'.$is_approved.'__2';
	exit();
}

if($action=="receive_popup")
{
	extract($_REQUEST); 
    echo load_html_head_contents("Finish Fabric Receive Details", "../../", 1, 1,$unicode,'','');
	$color_array=return_library_array( "select id, color_name from lib_color", "id", "color_name");
	$product_details=return_library_array( "select id, product_name_details from product_details_master", "id", "product_name_details");
	?>
	<script>

		function print_window()
		{
			document.getElementById('scroll_body').style.overflow="auto";
			document.getElementById('scroll_body').style.maxHeight="none";
			
			var w = window.open("Surprise", "#");
			var d = w.document.open();
			d.write ('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+
		'<html><head><link rel="stylesheet" href="../../../css/style_common.css" type="text/css" media="print"/><title></title></head><body>'+document.getElementById('report_container').innerHTML+'</body</html>');
		
			d.close();
			document.getElementById('scroll_body').style.overflowY="scroll";
			document.getElementById('scroll_body').style.maxHeight="230px";
		}	
		
	</script>	
		<div style="width:985px" align="center"><input type="button" value="Print Preview" onClick="print_window()" style="width:100px"  class="formbutton"/></div>
		<fieldset style="width:980px; margin-left:3px">
			<div id="report_container">
				<table border="1" class="rpt_table" rules="all" width="970" cellpadding="0" cellspacing="0">
					<thead>
						<th colspan="10"><b>Fabric Receive Info</b></th>
					</thead>
					<thead>
	                	<th width="30">SL</th>
	                    <th width="110">Receive ID</th>
	                    <th width="130">Insart Date and Time</th>
	                    <th width="90">Batch No.</th>
	                    <th width="170">Fabric Description</th>
	                    <th width="40">Uom</th>
	                    <th width="100">Fabric Color</th>
                        <th width="40">F. Shade</th>
	                    <th width="70">Receive Qty</th>
	                    <th>Remarks</th>
					</thead>
	             </table>
	             <div style="width:987px; max-height:320px; overflow-y:scroll" id="scroll_body">
	                 <table border="1" class="rpt_table" rules="all" width="970" cellpadding="0" cellspacing="0">
	                    <?
	                    $i=1;
	                    $total_fabric_recv_qnty=0; $dye_company='';
	                    $sql="(select a.recv_number, a.receive_date, a.receive_basis, a.knitting_source, a.knitting_company, a.insert_date, c.batch_no, b.prod_id, b.uom, b.color_id, b.fabric_shade, b.remarks, sum(b.receive_qnty) as quantity from inv_receive_master a, pro_finish_fabric_rcv_dtls b, pro_batch_create_mst c where a.id=b.mst_id and b.batch_id=c.id  and a.receive_basis=5 and a.entry_form in (7,37) and c.booking_without_order=1 and c.booking_no='$booking_no' and b.trans_id!=0 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 group by a.recv_number, a.receive_date, a.receive_basis, a.knitting_source, a.knitting_company, a.insert_date, c.batch_no, b.prod_id, b.uom, b.color_id, b.fabric_shade, b.remarks)
						union all
						( select a.recv_number, a.receive_date, a.receive_basis, a.knitting_source, a.knitting_company, a.insert_date, c.batch_no, b.prod_id, b.uom, b.color_id, b.fabric_shade, b.remarks, sum(b.receive_qnty) as quantity from inv_receive_master a, pro_finish_fabric_rcv_dtls b, pro_batch_create_mst c, inv_receive_master d where a.id=b.mst_id and b.batch_id=c.id and a.booking_no=d.recv_number and a.receive_basis=9 and d.receive_basis=5 and a.entry_form =37 and d.entry_form =7 and c.booking_without_order=1 and c.booking_no='$booking_no' and b.trans_id!=0 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 group by a.recv_number, a.receive_date, a.receive_basis, a.knitting_source, a.knitting_company, a.insert_date, c.batch_no, b.prod_id, b.uom, b.color_id, b.fabric_shade, b.remarks)";
						
						//echo $sql;
	                    $result=sql_select($sql);
	        			foreach($result as $row)
	                    {
	                        if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";	
	                    
	                        $total_fabric_recv_qnty+=$row[csf('quantity')];
	                    ?>
	                        <tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor;?>')" id="tr_<? echo $i;?>">
	                            <td width="30"><? echo $i; ?></td>
	                            <td width="110" style="word-break:break-all"><? echo $row[csf('recv_number')]; ?></td>
	                            <td width="130" align="center"><? echo $row[csf('insert_date')]; ?></td>
                                <td width="90" style="word-break:break-all"><? echo $row[csf('batch_no')]; ?></p></td>
                                <td width="170" style="word-break:break-all"><? echo $product_details[$row[csf('prod_id')]]; ?></td>
	                            <td width="40"><? echo $unit_of_measurement[$row[csf('uom')]]; ?></td>
                                <td width="100" style="word-break:break-all"><? echo $color_array[$row[csf('color_id')]]; ?></td>
	                            <td width="40"><? echo $fabric_shade[$row[csf('fabric_shade')]]; ?></td>
                                <td width="70" align="right"><? echo number_format($row[csf('quantity')],2); ?></td>
	                            <td style="word-break:break-all"><? echo $row[csf('remarks')]; ?></td>
	                        </tr>
	                    <?
	                    $i++;
	                    }
	                    ?>
	                    <tfoot>
	                        <th colspan="8" align="right">Total</th>
	                        <th align="right"><? echo number_format($total_fabric_recv_qnty,2); ?></th>
	                        <th>&nbsp;</th>
	                    </tfoot>
	                </table>
	            </div>	
	        </div>
		</fieldset>   
	<?
	exit();
}
if($action=="Image_check"){

	$nameArray=sql_select( "select id,image_location,master_tble_id,details_tble_id,form_name,file_type,real_file_name,INSERT_DATE from common_photo_library where master_tble_id='$data' and form_name='sample_checklist_pattern_img' and file_type=1 " );

		$flag=0;
			if (count($nameArray)>0) 
			{
				echo 1;die;
				/* foreach ($nameArray as $inf)
				{
					$inf[csf("INSERT_DATE")]=date('d-m-Y h:i:s a',strtotime($inf[csf("INSERT_DATE")]));
					$ext =strtolower( get_file_ext($inf[csf("image_location")]));
					//echo $ext;die;
					if($ext=="jpg" || $ext=="jpeg" || $ext=="png" || $ext=="bmp")
					{
						
						
					}
				} */
			

			}else{
				echo 0;die;
			}
			//echo $flag;
			
}

?>