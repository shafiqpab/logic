<?php
header('Content-type:text/html; charset=utf-8');
session_start();
if( $_SESSION['logic_erp']['user_id'] == "" )
	header("location:login.php");

require_once('../../../includes/common.php');
$user_name=$_SESSION['logic_erp']['user_id'];

$data=$_REQUEST['data'];
$action=$_REQUEST['action'];

//------------------------------------------------------------------------------------------

/*
|--------------------------------------------------------------------------
| for load_drop_down_buyer
|--------------------------------------------------------------------------
|
*/
if ($action=="load_drop_down_buyer")
{
	echo create_drop_down( "cbo_buyer_name", 150, "select buy.id,buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company='".$data."' and buy.id in (select  buyer_id from lib_buyer_party_type where party_type in (1,3,21,90)) $buyer_cond order by buyer_name","id,buyer_name", 1, "-- Select --", $selected, "load_drop_down( 'requires/sample_fabric_production_status_controller', this.value, 'load_drop_down_season_buyer', 'season_td');" );     	 
	exit();
}

/*
|--------------------------------------------------------------------------
| for load_drop_down_season_buyer
|--------------------------------------------------------------------------
|
*/
if ($action=="load_drop_down_season_buyer")
{
	$sql="select a.id,a.season_name from lib_buyer_season a where a.status_active =1 and a.is_deleted=0 and a.buyer_id='$data'";
	if(count(sql_select($sql))==1)
	{
		echo create_drop_down( "cbo_season_name", 150, $sql,'id,season_name', 0, '--- Select Season ---', 1, ""  );
	}
	else
	{
		echo create_drop_down( "cbo_season_name", 150, $sql,'id,season_name', 1, '--- Select Season ---', 0, ""  );
	}

}

/*
|--------------------------------------------------------------------------
| for load_drop_down_buyer_req
|--------------------------------------------------------------------------
|
*/
if ($action=="load_drop_down_buyer_req")
{
	echo create_drop_down( "cbo_buyer_mst", 150, "select buy.id,buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company='".$data."' ".$buyer_cond." and buy.id in (select  buyer_id from  lib_buyer_party_type where party_type in (1,3,21,30,90)) order by buyer_name","id,buyer_name", 1, "-- Select Buyer --", $selected, "" );
}

/*
|--------------------------------------------------------------------------
| for popup_onDblClick_requisition
|--------------------------------------------------------------------------
|
*/
if($action=="popup_onDblClick_requisition")
{
	extract($_REQUEST);
	echo load_html_head_contents("Sample Requisition Info","../../../", 1, 1, $unicode);
	if($cbo_company_name>0)
		$isDis=1;
	else
		$isDis=0;
	?>
	<script>
		/*$(document).ready(function(e) {
            $("#txt_search_common").focus();
        });
		function search_populate(str)
		{
			//alert(str);
			if(str==0)
			{
				document.getElementById('search_by_th_up').innerHTML="Enter Style ID";
				document.getElementById('search_by_td').innerHTML='<input	type="text"	name="txt_search_common" style="width:130px " class="text_boxes" id="txt_search_common"	value=""  />';
			}
			else if(str==1)
			{
				document.getElementById('search_by_th_up').innerHTML="Enter Style Name";
				document.getElementById('search_by_td').innerHTML='<input	type="text"	name="txt_search_common" style="width:130px " class="text_boxes" id="txt_search_common"	value=""  />';
			}
		}*/

		function js_set_value( mst_id )
		{
			document.getElementById('selected_requisition_no').value=mst_id;
			parent.emailwindow.hide();
		}
    </script>
</head>
<body>
	<div align="center" style="width:100%;" >
	<form name="searchsampledevelopmentfrm_1"  id="searchsampledevelopmentfrm_1" autocomplete="off">
        <table width="740" cellspacing="0" cellpadding="0" border="1" rules="all" class="rpt_table" align="center">
            <thead>
                <th width="140" class="must_entry_caption">Company Name</th>
                <th width="157">Buyer Name</th>
                <th width="70">Requisition No</th>
                <th width="130" colspan="2">Requisition date</th>
                <th><input type="reset" name="reset" id="reset" class="formbutton" value="Reset" style="width:80px;" /></th>
            </thead>
            <tr class="general">
                <td>
                    <input type="hidden" id="selected_requisition_no">
                    <? echo create_drop_down( "cbo_company_mst", 150, "select id,company_name from lib_company comp where status_active =1 and is_deleted=0 and core_business not in(3) $company_credential_cond order by company_name","id,company_name",1, "-- Select Company --", $cbo_company_name,"load_drop_down( 'sample_fabric_production_status_controller', this.value, 'load_drop_down_buyer_req', 'buyer_td_req' );",$isDis ); ?> </td>
                <td id="buyer_td_req"><? echo create_drop_down( "cbo_buyer_mst", 150, $blank_array,'', 1, "-- Select Buyer --" ); ?></td>
                <td><input type="text" style="width:140px" class="text_boxes" name="txt_requisition_num" id="txt_requisition_num"  /></td>
                <td><input name="txt_date_from" id="txt_date_from" class="datepicker" style="width:80px" placeholder="From Date"></td>
                <td><input name="txt_date_to" id="txt_date_to" class="datepicker" style="width:80px" placeholder="To Date"></td>
                <td>
                    <input type="button" name="btn_show" id="btn_show" class="formbutton" value="Show" onClick="show_list_view ( document.getElementById('cbo_company_mst').value+'_'+document.getElementById('cbo_buyer_mst').value+'_'+document.getElementById('txt_requisition_num').value+'_'+document.getElementById('txt_date_from').value+'_'+document.getElementById('txt_date_to').value, 'listview_onDblClick_requisition', 'search_div', 'sample_fabric_production_status_controller', 'setFilterGrid(\'list_view\',-1)')" style="width:80px;" />
                </td>
            </tr>
            <tr>
                <td align="center" colspan="10" valign="middle"><? echo load_month_buttons(1);  ?></td>
            </tr>
        </table>
        <div id="search_div"></div>
    </form>
	</div>
</body>
<script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
</html>
<?
exit();
}

/*
|--------------------------------------------------------------------------
| for listview_onDblClick_requisition
|--------------------------------------------------------------------------
|
*/
if($action=="listview_onDblClick_requisition")
{
	$data=explode('_',$data);
	if ($data[0]!=0)
		$company=" and company_id='".$data[0]."'";
	else
	{
		echo "Please Select Company First.";
		die;
	}
	
	if ($data[1]!=0)
		$buyer=" and buyer_name='".$data[1]."'";
	else
		$buyer="";
		
	if($db_type==0)
	{
		if ($data[3]!="" &&  $data[4]!="")
			$estimated_shipdate  = "and requisition_date  between '".change_date_format($data[3], "yyyy-mm-dd", "-")."' and '".change_date_format($data[4], "yyyy-mm-dd", "-")."'";
		else
			$estimated_shipdate ="";
	}
	
	if($db_type==2)
	{
		if ($data[3]!="" &&  $data[4]!="")
			$estimated_shipdate  = "and requisition_date  between '".change_date_format($data[3], "yyyy-mm-dd", "-",1)."' and '".change_date_format($data[4], "yyyy-mm-dd", "-",1)."'";
		else
			$estimated_shipdate ="";
	}
	
	if ($data[2]!="")
		$requisition_num=" and requisition_number_prefix_num like '%$data[2]' ";
	else
		$requisition_num="";

	$buyer_arr=return_library_array( "select id, buyer_name from lib_buyer",'id','buyer_name');
	$comp=return_library_array( "select id, company_name from lib_company",'id','company_name');
	$dealing_marchant=return_library_array( "select id, team_member_name from lib_mkt_team_member_info",'id','team_member_name');
	$req_wise_booking=return_library_array( "select style_id, booking_no from wo_non_ord_samp_booking_dtls where status_active=1",'style_id','booking_no');
	$team_leader=return_library_array( "select id, team_leader_name from lib_marketing_team",'id','team_leader_name');

	$arr=array (2=>$buyer_arr,4=>$product_dept,5=>$dealing_marchant,6=>$sample_stage,7=>$req_wise_booking);
	$sql="";
	if($db_type==0)
	{
		$sql= "SELECT id,requisition_number_prefix_num,SUBSTRING_INDEX(insert_date, '-', 1) as year,company_id,buyer_name,style_ref_no,product_dept,dealing_marchant,sample_stage_id from sample_development_mst where entry_form_id=203 and status_active=1 and is_deleted=0 $company $buyer $estimated_shipdate $requisition_num order by id DESC";
	}
	else if($db_type==2)
	{
		$sql= "SELECT id,requisition_number_prefix_num,to_char(insert_date,'YYYY') as year,company_id,buyer_name,style_ref_no,product_dept,dealing_marchant,sample_stage_id from sample_development_mst where entry_form_id=203 and  status_active=1 and is_deleted=0 $company $buyer $estimated_shipdate $requisition_num order by id DESC";
	}

	echo  create_list_view("list_view", "Year,Requisition No,Buyer Name,Style Name,Product Department,Dealing Merchant,Sample Stage,Booking No", "60,140,140,100,90,90,100,100","900","240",0, $sql, "js_set_value", "id,requisition_number_prefix_num,dealing_marchant", "", 1, "0,0,buyer_name,0,product_dept,dealing_marchant,sample_stage_id,id", $arr , "year,requisition_number_prefix_num,buyer_name,style_ref_no,product_dept,dealing_marchant,sample_stage_id,id", "",'','0,0,0,0,0,0') ;
	exit();
}

/*
|--------------------------------------------------------------------------
| for popup_onDblClick_booking
|--------------------------------------------------------------------------
|
*/
if ($action=="load_dealing_marchant")
{
	$sqlRslt=sql_select("SELECT team_member_name AS NAME FROM lib_mkt_team_member_info WHERE id = '".$data."'");
	echo "document.getElementById('txt_dealing_Merchant').value = '".$sqlRslt[0]['NAME']."';\n";
}

/*
|--------------------------------------------------------------------------
| for popup_onDblClick_booking
|--------------------------------------------------------------------------
|
*/
if ($action=="popup_onDblClick_booking")
{
  	echo load_html_head_contents("Booking Search","../../../", 1, 1, $unicode);
  	extract($_REQUEST);
	?>
	<script>
		var company="<? echo $cbo_company_name; ?>";
		$('#cbo_company_mst').val(company);
		function js_set_value(booking_no)
		{
			document.getElementById('selected_booking').value=booking_no;
			parent.emailwindow.hide();
		}
    </script>
</head>
<body>
<div align="center" style="width:100%;" >
<form name="searchorderfrm_1"  id="searchorderfrm_1" autocomplete="off">
    <table width="750" cellspacing="0" cellpadding="0" border="1" class="rpt_table" rules="all">
       <thead>
            <th colspan="6">
              <?
               echo create_drop_down( "cbo_search_category", 130, $string_search_type,'', 1, "-- Search Catagory --" );
              ?>
            </th>
         </thead>
        <thead>
            <th width="150" class="must_entry_caption">Company Name</th>
            <th width="150">Buyer Name</th>
            <th width="100">Booking No</th>
            <th width="150" colspan="2">Date Range</th>
            <th>&nbsp;</th>
        </thead>
        <tr class="general">
            <td> <input type="hidden" id="selected_booking">
                <?
                echo create_drop_down( "cbo_company_mst", 150, "select id,company_name from lib_company comp where status_active=1 and core_business not in(3) $company_cond order by company_name","id,company_name",1, "--Select Company--", $cbo_company_name, "load_drop_down( 'sample_fabric_production_status_controller', this.value, 'load_drop_down_buyer', 'buyer_booking_td');");
                ?>
            </td>
            <td id="buyer_booking_td"><? echo create_drop_down( "cbo_buyer_name", 150, $blank_array,"", 1, "-- Select Buyer --" ); ?></td>
            
            <td><input name="txt_booking_prifix" id="txt_booking_prifix" class="text_boxes" style="width:100px"></td>
            <td><input name="txt_date_from" id="txt_date_from" class="datepicker" style="width:70px"></td>
            <td><input name="txt_date_to" id="txt_date_to" class="datepicker" style="width:70px"></td>
            <td align="center">
            <input type="button" name="button2" class="formbutton" value="Show" onClick="show_list_view ( document.getElementById('cbo_company_mst').value+'_'+document.getElementById('cbo_buyer_name').value+'_'+document.getElementById('txt_date_from').value+'_'+document.getElementById('txt_date_to').value+'_'+document.getElementById('cbo_year_selection').value+'_'+document.getElementById('txt_booking_prifix').value+'_'+document.getElementById('cbo_search_category').value, 'listview_onDblClick_booking', 'search_div', 'sample_fabric_production_status_controller','setFilterGrid(\'table_body\',1)')" style="width:100px;" /></td>
        </tr>
        <tr>
            <td align="center" colspan="7" valign="middle">
            <?
            echo create_drop_down( "cbo_year_selection", 70, $year,"", 1, "-- Select --", date('Y'), "",0 );
            echo load_month_buttons();
            ?>
            </td>
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

if ($action=="popup_onDblClick_booking--")
{
  	echo load_html_head_contents("Booking Search","../../../", 1, 1, $unicode);
  	extract($_REQUEST);
	?>
	<script>
		var company="<? echo $cbo_company_name; ?>";
		$('#cbo_company_mst').val(company);
		function js_set_value(booking_no)
		{
			document.getElementById('selected_booking').value=booking_no;
			parent.emailwindow.hide();
		}
    </script>
</head>
<body>
<div align="center" style="width:100%;" >
<form name="searchorderfrm_1"  id="searchorderfrm_1" autocomplete="off">
    <table width="800" cellspacing="0" cellpadding="0" border="1" class="rpt_table" rules="all">
       <thead>
            <th colspan="7">
              <?
               echo create_drop_down( "cbo_search_category", 130, $string_search_type,'', 1, "-- Search Catagory --" );
              ?>
            </th>
         </thead>
        <thead>
            <th width="150" class="must_entry_caption">Company Name</th>
            <th width="150">Buyer Name</th>
            <th width="100">Booking No</th>
            <th width="80">Style Desc.</th>
            <th width="150" colspan="2">Date Range</th>
            <th>&nbsp;</th>
        </thead>
        <tr class="general">
            <td> <input type="hidden" id="selected_booking">
                <?
                echo create_drop_down( "cbo_company_mst", 150, "select id,company_name from lib_company comp where status_active=1 and core_business not in(3) $company_cond order by company_name","id,company_name",1, "--Select Company--", $cbo_company_name, "load_drop_down( 'sample_fabric_production_status_controller', this.value, 'load_drop_down_buyer', 'buyer_booking_td');");
                ?>
            </td>
            <td id="buyer_booking_td"><? echo create_drop_down( "cbo_buyer_name", 150, $blank_array,"", 1, "-- Select Buyer --" ); ?></td>
            
            <td><input name="txt_booking_prifix" id="txt_booking_prifix" class="text_boxes" style="width:100px"></td>
            <td><input name="txt_style_desc" id="txt_style_desc" class="text_boxes" style="width:80px"></td>
            <td><input name="txt_date_from" id="txt_date_from" class="datepicker" style="width:70px"></td>
            <td><input name="txt_date_to" id="txt_date_to" class="datepicker" style="width:70px"></td>
            <td align="center">
            <input type="button" name="button2" class="formbutton" value="Show" onClick="show_list_view ( document.getElementById('cbo_company_mst').value+'_'+document.getElementById('cbo_buyer_name').value+'_'+document.getElementById('txt_date_from').value+'_'+document.getElementById('txt_date_to').value+'_'+document.getElementById('cbo_year_selection').value+'_'+document.getElementById('txt_booking_prifix').value+'_'+document.getElementById('cbo_search_category').value+'_'+document.getElementById('txt_style_desc').value, 'listview_onDblClick_booking', 'search_div', 'sample_fabric_production_status_controller','setFilterGrid(\'table_body\',1)')" style="width:100px;" /></td>
        </tr>
        <tr>
            <td align="center" colspan="7" valign="middle">
            <?
            echo create_drop_down( "cbo_year_selection", 70, $year,"", 1, "-- Select --", date('Y'), "",0 );
            echo load_month_buttons();
            ?>
            </td>
        </tr>
    </table>
    <div id="search_div"></div>
    </form>
   </div>
</body>
<script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
</html>
<?
exit();
}

/*
|--------------------------------------------------------------------------
| for listview_onDblClick_booking
|--------------------------------------------------------------------------
|
*/
if ($action=="listview_onDblClick_booking")
{
	$data=explode('_',$data);
	$style_desc=$data[7];
	
	if ($data[0]!=0)
		$company="  a.company_id='$data[0]'";
	else
	{
		echo "Please Select Company First.";
		die;
	}
	
	if(trim($data[2])=="" && trim($data[3])=="" && trim($data[5])=="")
	{
		echo "Please Select Date Range.";
		die;
	}
	
	if ($data[1]!=0)
	{
		$buyer=" and a.buyer_id='$data[1]'";
	}
	else
	{
		$buyer="";
	}

	if($db_type==0)
	{
		$booking_year_cond=" and YEAR(a.insert_date)=$data[4]";
		if ($data[2]!="" &&  $data[3]!="")
			$booking_date  = "and a.booking_date  between '".change_date_format($data[2], "yyyy-mm-dd", "-")."' and '".change_date_format($data[3], "yyyy-mm-dd", "-")."'";
		else
			$booking_date ="";
	}
	else if($db_type==2)
	{
		$booking_year_cond=" and to_char(a.insert_date,'YYYY')=$data[4]";
		if ($data[2]!="" &&  $data[3]!="")
			$booking_date  = "and a.booking_date  between '".change_date_format($data[2], "yyyy-mm-dd", "-",1)."' and '".change_date_format($data[3], "yyyy-mm-dd", "-",1)."'";
		else
			$booking_date ="";
	}
	if($data[6]==4 || $data[6]==0)
	{
		if (str_replace("'","",$data[5])!="")
			$booking_cond=" and a.booking_no_prefix_num like '%$data[5]%'  $booking_year_cond  ";
		else
			$booking_cond="";
	}
	else if($data[6]==1)
	{
		if (str_replace("'","",$data[5])!="") $booking_cond=" and a.booking_no_prefix_num ='$data[5]' "; else $booking_cond="";
		if (str_replace("'","",$data[7])!="") $style_des_cond=" and b.style_des='$data[7]' "; else $style_des_cond="";
	}
	else if($data[6]==2)
	{
		if (str_replace("'","",$data[5])!="")
			$booking_cond=" and a.booking_no_prefix_num like '$data[5]%'  $booking_year_cond ";
		else $booking_cond="";
	}
	else if($data[6]==3)
	{
		if (str_replace("'","",$data[5])!="")
			$booking_cond=" and a.booking_no_prefix_num like '%$data[5]'  $booking_year_cond  ";
		else $booking_cond="";
	}
	$style_library=return_library_array( "select id,style_ref_no from sample_development_mst", "id", "style_ref_no"  );
	$buyer_arr=return_library_array( "select id, short_name from lib_buyer",'id','short_name');
	$comp=return_library_array( "select id, company_short_name from lib_company",'id','company_short_name');
	$suplier=return_library_array( "select id, short_name from lib_supplier",'id','short_name');

	$sql= "select a.booking_no_prefix_num AS BOOKING_NO_PREFIX_NUM, a.booking_no AS BOOKING_NO, a.booking_date AS BOOKING_DATE, a.company_id AS COMPANY_ID, a.buyer_id AS BUYER_ID, b.style_id AS STYLE_ID from wo_non_ord_samp_booking_mst a, wo_non_ord_samp_booking_dtls b where a.booking_no = b.booking_no and $company". set_user_lavel_filtering(' and a.buyer_id','buyer_id')." $buyer $booking_date $booking_cond and a.booking_type=4 and a.status_active=1 and a.is_deleted=0 and a.entry_form_id = 140 order by a.id DESC";
	//echo $sql;
    $sql_data=sql_select($sql);
	$dataArr = array();
	foreach($sql_data as $row)
	{
		$dataArr[$row['BOOKING_NO_PREFIX_NUM']][$row['BUYER_ID']]['BOOKING_DATE'] = date("d-m-Y",strtotime($row['BOOKING_DATE']));
		$dataArr[$row['BOOKING_NO_PREFIX_NUM']][$row['BUYER_ID']]['COMPANY_ID'] = $row['COMPANY_ID'];
		$dataArr[$row['BOOKING_NO_PREFIX_NUM']][$row['BUYER_ID']]['BOOKING_NO'] = $row[csf('BOOKING_NO')];
		$dataArr[$row['BOOKING_NO_PREFIX_NUM']][$row['BUYER_ID']]['STYLE_ID'] = $row[csf('STYLE_ID')];
	}
	?>
    <table class="rpt_table scroll" width="550" cellpadding="0" cellspacing="0" border="1" rules="all" >
    	<thead>
            <th width="30">Sl</th>
            <th width="100">Booking No</th>
            <th width="150">Company</th>
            <th width="150">Buyer</th>
            <th>Booking Date</th>
        </thead>
    </table>
    <div style="max-height:300px; overflow-y:scroll; width:550px" >
    <table width="530" class="rpt_table" id="table_body" border="1" rules="all">
        <tbody>
		<?
        $i=1;
        foreach($dataArr as $bookingPrefix=>$bookingPrefixArr)
        {
			foreach($bookingPrefixArr as $buyerId=>$row)
			{
				if($i%2==0)
					$bgcolor="#E9F3FF";
				else
					$bgcolor="#FFFFFF";
				?>
				<tr bgcolor="<? echo $bgcolor; ?>" onClick="js_set_value('<? echo $row['BOOKING_NO'].'_'.$row['STYLE_ID'].'_'.$style_library[$row['STYLE_ID']]; ?>')" style="cursor:pointer">
					<td width="30"><? echo $i; ?></td>
					<td width="100"><? echo $bookingPrefix; ?></td>
					<td width="150"><? echo $comp[$row['COMPANY_ID']]; ?></td>
					<td width="150"><? echo $buyer_arr[$buyerId]; ?></td>
					<td><? echo $row['BOOKING_DATE'];?></td>
				</tr>
				<?
				$i++;
			}
        }
        ?>
        </tbody>
    </table>
    </div>
    <?
	exit();
}

if ($action=="listview_onDblClick_booking--")
{
	$data=explode('_',$data);
	$style_desc=$data[7];
	if ($data[0]!=0) $company="  a.company_id='$data[0]'"; else { echo "Please Select Company First."; die; }
	if($style_desc=="" && trim($data[5])=="" && (trim($data[2])=="" && trim($data[3])=="")) { echo "Please Select Date Range."; die; }
	if ($data[1]!=0){$buyer=" and a.buyer_id='$data[1]'";}
	else{$buyer="";}

	if($db_type==0)
	 {
		  $booking_year_cond=" and YEAR(a.insert_date)=$data[4]";
		  if ($data[2]!="" &&  $data[3]!="") $booking_date  = "and a.booking_date  between '".change_date_format($data[2], "yyyy-mm-dd", "-")."' and '".change_date_format($data[3], "yyyy-mm-dd", "-")."'"; else $booking_date ="";
     }
	else if($db_type==2)
	 {
		  $booking_year_cond=" and to_char(a.insert_date,'YYYY')=$data[4]";
		  if ($data[2]!="" &&  $data[3]!="") $booking_date  = "and a.booking_date  between '".change_date_format($data[2], "yyyy-mm-dd", "-",1)."' and '".change_date_format($data[3], "yyyy-mm-dd", "-",1)."'"; else $booking_date ="";
	 }
	if($data[6]==4 || $data[6]==0)
		{
			if (str_replace("'","",$data[5])!="") $booking_cond=" and a.booking_no_prefix_num like '%$data[5]%'  $booking_year_cond  "; else $booking_cond="";
			if (str_replace("'","",$data[7])!="") $style_des_cond=" and b.style_des like '%$data[7]%' "; else $style_des_cond="";
		}
    else if($data[6]==1)
		{
			if (str_replace("'","",$data[5])!="") $booking_cond=" and a.booking_no_prefix_num ='$data[5]'   "; else $booking_cond="";
			if (str_replace("'","",$data[7])!="") $style_des_cond=" and b.style_des='$data[7]' "; else $style_des_cond="";
		}
   else if($data[6]==2)
		{
			if (str_replace("'","",$data[5])!="") $booking_cond=" and a.booking_no_prefix_num like '$data[5]%'  $booking_year_cond  "; else $booking_cond="";
			if (str_replace("'","",$data[7])!="") $style_des_cond=" and b.style_des like '$data[7]%' "; else $style_des_cond="";
		}
	else if($data[6]==3)
		{
			if (str_replace("'","",$data[5])!="") $booking_cond=" and a.booking_no_prefix_num like '%$data[5]'  $booking_year_cond  "; else $booking_cond="";
			if (str_replace("'","",$data[7])!="") $style_des_cond=" and b.style_des like '%$data[7]' "; else $style_des_cond="";
		}
	$style_library=return_library_array( "select id,style_ref_no from sample_development_mst", "id", "style_ref_no"  );
    //$approved=array(0=>"No",1=>"Yes",3=>"Yes");
    //$is_ready=array(0=>"No",1=>"Yes",2=>"No");
	$buyer_arr=return_library_array( "select id, short_name from lib_buyer",'id','short_name');
	$comp=return_library_array( "select id, company_short_name from lib_company",'id','company_short_name');
	$suplier=return_library_array( "select id, short_name from lib_supplier",'id','short_name');

	$sql= "select a.booking_no_prefix_num, a.booking_no, a.booking_date, a.company_id, a.buyer_id, a.item_category, a.fabric_source, a.supplier_id, a.pay_mode, b.style_id, b.style_des,b.fabric_source as fabric_source_dtls from wo_non_ord_samp_booking_mst a left join wo_non_ord_samp_booking_dtls b on a.booking_no=b.booking_no and b.status_active=1 and b.is_deleted=0 where $company". set_user_lavel_filtering(' and a.buyer_id','buyer_id')." $buyer $booking_date $booking_cond $style_des_cond and a.booking_type=4 and a.status_active=1 and a.is_deleted=0 and a.entry_form_id = 140 order by a.id DESC";
	//echo $sql;
	?>
    <table class="rpt_table scroll" width="900" cellpadding="0" cellspacing="0" border="1" rules="all" >
    	<thead>
            <th width="30">Sl</th>
            <th width="100">Booking No</th>
            <th width="80">Booking Date</th>
            <th width="100">Company</th>
            <th width="100">Buyer</th>
            <th width="80">Fabric Nature</th>
            <th width="80">Fabric Source</th>
            <th width="80">Pay Mode</th>
            <th width="100">Supplier</th>
            <th width="50">Style</th>
            <th>Style Desc.</th>
        </thead>
    </table>
    <div style="max-height:300px; overflow-y:scroll; width:900px" >
    <table width="880" class="rpt_table" id="table_body" border="1" rules="all">
        <tbody>
		<?
        $i=1;
        $sql_data=sql_select($sql);
        foreach($sql_data as $row)
        {
			if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
			if($row[csf('fabric_source')]!=0)
			{
				$fabric_source_txt = $fabric_source[$row[csf('fabric_source')]];
			}
			else
			{
				$fabric_source_txt = $fabric_source[$row[csf('fabric_source_dtls')]];
			}
			?>
			<tr bgcolor="<? echo $bgcolor;?>" onClick="js_set_value('<? echo $row[csf('booking_no')]; ?>')" style="cursor:pointer">
                <td width="30"><? echo $i;?></td>
                <td width="100"><? echo $row[csf('booking_no_prefix_num')];?></td>
                <td width="80"><? echo date("d-m-Y",strtotime($row[csf('booking_date')]));?></td>
                <td width="100"><? echo $comp[$row[csf('company_id')]];?></td>
                <td width="100"><? echo $buyer_arr[$row[csf('buyer_id')]];?></td>
                <td width="80"><? echo $item_category[$row[csf('item_category')]];?></td>
                <td width="80"><? echo $fabric_source_txt;?></td>
                <td width="80"><? echo $pay_mode[$row[csf('pay_mode')]];?></td>
                <td width="100">
                <?
                if($row[csf('pay_mode')]==3 || $row[csf('pay_mode')]==5) echo $comp[$row[csf('supplier_id')]];
                else echo $suplier[$row[csf('supplier_id')]];
                ?>
                </td>
                <td width="50" style="word-wrap: break-word;word-break: break-all;"><? echo $style_library[$row[csf('style_id')]];?></td>
                <td style="word-wrap: break-word;word-break: break-all;"><? echo $row[csf('style_des')];?></td>
			</tr>
			<?
			$i++;
        }
        ?>
        </tbody>
    </table>
    </div>
    <?
	exit();
}

/*
|--------------------------------------------------------------------------
| for show button
|--------------------------------------------------------------------------
|
*/
if($action=="report_generate")
{
	//echo 'su..re';
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process )); 
	$cbo_year=str_replace("'", "", $cbo_year);
	$cbo_booking_type=str_replace("'", "", $cbo_booking_type);
	$txt_booking_no=str_replace("'", "", $txt_booking_no);
	$start_date=str_replace("'", "", $txt_date_from);
	$end_date=str_replace("'", "", $txt_date_to);

	//for company
	$company_id = str_replace("'", "", $cbo_company_name);
	
	//for buyer
	$cbo_buyer_name = str_replace("'", "", $cbo_buyer_name);
	$buyer_condition = ($cbo_buyer_name != 0 ? " AND a.buyer_name = ".$cbo_buyer_name : '');
	
	//for season
	$cbo_season_name = str_replace("'", "", $cbo_season_name);
	$season_condition = ($cbo_season_name != 0 ? " AND a.season = ".$cbo_season_name : '');
	
	//for requisition no
	$txt_requisition_no = str_replace("'", "", $txt_requisition_no);
	$hdn_requisition_id = str_replace("'", "", $hdn_requisition_id);
	$requisitionNo_condition = '';
	$requisitionNo_condition2 = '';
	$requisitionNo_condition3 = '';
	$requisitionNo_condition4 = '';
	if($hdn_requisition_id != '')
	{
		$requisitionNo_condition = " AND a.id IN( ".$hdn_requisition_id.")";
		$requisitionNo_condition2 = " AND b.style_id IN( ".$hdn_requisition_id.")";
		$requisitionNo_condition3 = " AND a.sample_mst_id IN( ".$hdn_requisition_id.")";
		$requisitionNo_condition4 = " AND b.mst_id IN( ".$hdn_requisition_id.")";
	}
	
	if($start_date!="" && $end_date!="")
	{
		if($db_type==0)
		{
			$fromDate = change_date_format(trim($start_date), "yyyy-mm-dd", "-");
			$toDate = change_date_format(trim($end_date), "yyyy-mm-dd", "-");
			
			$date_cond = "and a.receive_date between '".change_date_format(trim($start_date), "yyyy-mm-dd", "-")."' and '".change_date_format(trim($end_date), "yyyy-mm-dd", "-")."'";
		}
		else
		{
			$fromDate = change_date_format(trim($start_date),'','',1);
			$toDate = change_date_format(trim($end_date),'','',1);
			
			$date_cond = "and a.receive_date between '".change_date_format(trim($start_date),'','',1)."' and '".change_date_format(trim($end_date),'','',1)."'";
		}
	}
	else
	{
		$date_cond="";
	}
	
	//for date
	if($txt_requisition_no == '' && $txt_booking_no == '' && $hdn_requisition_id == '' && $start_date != '' && $end_date != '')
	{
		//for with order
		if($cbo_booking_type == 1)
		{
			$sql ="
				SELECT
					b.style_id AS STYLE_ID
				FROM wo_booking_mst a, wo_booking_dtls b
				WHERE
					a.booking_no = b.booking_no
					AND a.status_active = 1
					AND a.is_deleted = 0
					--AND a.entry_form_id = 140
					AND a.booking_type = 4
					AND b.status_active = 1
					AND b.is_deleted = 0
					--AND b.entry_form_id = 140
					AND a.booking_date BETWEEN '".$fromDate."' AND  '".$toDate."'
			";
			$sqlRslt = sql_select($sql);
			$reqIdArr = array();
			foreach ($sqlRslt as $row)
			{
				if($row['STYLE_ID']*1 != 0)
				{
					$reqIdArr[$row['STYLE_ID']] = $row['STYLE_ID'];
				}
			}
		}
		//for without order
		else if($cbo_booking_type == 2)
		{
			$sql ="
				SELECT
					b.style_id AS STYLE_ID
				FROM wo_non_ord_samp_booking_mst a, wo_non_ord_samp_booking_dtls b
				WHERE
					a.booking_no = b.booking_no
					AND a.status_active = 1
					AND a.is_deleted = 0
					AND a.entry_form_id = 140
					AND a.booking_type = 4
					AND b.status_active = 1
					AND b.is_deleted = 0
					AND b.entry_form_id = 140
					AND a.booking_date BETWEEN '".$fromDate."' AND  '".$toDate."'
			";
			$sqlRslt = sql_select($sql);
			$reqIdArr = array();
			foreach ($sqlRslt as $row)
			{
				if($row['STYLE_ID']*1 != 0)
				{
					$reqIdArr[$row['STYLE_ID']] = $row['STYLE_ID'];
				}
			}
		}
		//for both
		else
		{
			//for with order
			$sql ="
				SELECT
					b.style_id AS STYLE_ID
				FROM wo_booking_mst a, wo_booking_dtls b
				WHERE
					a.booking_no = b.booking_no
					AND a.status_active = 1
					AND a.is_deleted = 0
					--AND a.entry_form_id = 140
					AND a.booking_type = 4
					AND b.status_active = 1
					AND b.is_deleted = 0
					--AND b.entry_form_id = 140
					AND a.booking_date BETWEEN '".$fromDate."' AND  '".$toDate."'
			";
			$sqlRslt = sql_select($sql);
			$reqIdArr = array();
			foreach ($sqlRslt as $row)
			{
				if($row['STYLE_ID']*1 != 0)
				{
					$reqIdArr[$row['STYLE_ID']] = $row['STYLE_ID'];
				}
			}
			unset($sqlRslt);
			
			//for without order
			$sql ="
				SELECT
					b.style_id AS STYLE_ID
				FROM wo_non_ord_samp_booking_mst a, wo_non_ord_samp_booking_dtls b
				WHERE
					a.booking_no = b.booking_no
					AND a.status_active = 1
					AND a.is_deleted = 0
					AND a.entry_form_id = 140
					AND a.booking_type = 4
					AND b.status_active = 1
					AND b.is_deleted = 0
					AND b.entry_form_id = 140
					AND a.booking_date BETWEEN '".$fromDate."' AND  '".$toDate."'
			";
			$sqlRslt = sql_select($sql);
			foreach ($sqlRslt as $row)
			{
				if($row['STYLE_ID']*1 != 0)
				{
					$reqIdArr[$row['STYLE_ID']] = $row['STYLE_ID'];
				}
			}
		}
		
		if(!empty($reqIdArr))
		{
			$requisitionNo_condition = where_con_using_array($reqIdArr,0,'a.id');
			$requisitionNo_condition2 = where_con_using_array($reqIdArr,0,'b.style_id');
			$requisitionNo_condition3 = where_con_using_array($reqIdArr,0,'a.sample_mst_id');
			$requisitionNo_condition4 = where_con_using_array($reqIdArr,0,'b.mst_id');
		}
	}

	/*
	$search_field_cond="";
	if($txt_challan_no!= '')
	{
		$search_field_cond="and a.recv_number_prefix_num in(".$txt_challan_no.")";
	}
	*/
	/*if($db_type==0)
	{
		$year_field="YEAR(a.insert_date) AS YEAR,";
	}
	else if($db_type==2)
	{
		$year_field="to_char(a.insert_date,'YYYY') AS YEAR,";
	}
	else $year_field="";*/
	
	//$sql = "select a.id AS ID,a.recv_number as CHALLAN_NO, a.company_id AS COMPANY_ID, a.wo_no AS WO_NO, $year_field a.recv_number_prefix_num AS RECV_NUMBER_PREFIX_NUM, a.recv_number AS RECV_NUMBER, a.dyeing_source AS DYEING_SOURCE, a.dyeing_company AS DYEING_COMPANY, a.receive_date AS RECEIVE_DATE, a.process_id AS PROCESS_ID, b.batch_id AS BATCH_ID from inv_receive_mas_batchroll a INNER JOIN pro_grey_batch_dtls b ON a.id = b.mst_id where a.entry_form=63 and a.status_active=1 and a.is_deleted=0 and a.company_id = ".$company_id." $service_source_cond $service_company_cond $search_field_cond $date_cond order by a.id";
	
	//$field_array="id, requisition_number_prefix, requisition_number_prefix_num, requisition_number, sample_stage_id, requisition_date, quotation_id, style_ref_no, company_id, location_id, buyer_name, season, product_dept, dealing_marchant, agent_name, buyer_ref, bh_merchant, estimated_shipdate, remarks, inserted_by, insert_date, status_active, is_deleted, entry_form_id, is_copy, req_ready_to_approved, material_delivery_date";

	//$is_booking = sql_select("SELECT booking_no from wo_non_ord_samp_booking_dtls where style_id=$data and status_active=1 and is_deleted=0 and entry_form_id=140 group by booking_no  ");
	
	/*
	$id=return_next_id( "id", "wo_non_ord_samp_booking_mst", 1 ) ;
	$field_array="id,booking_type,booking_no_prefix,booking_no_prefix_num,booking_no,company_id,buyer_id,item_category,fabric_source,currency_id,source,buyer_req_no,revised_no,exchange_rate,pay_mode,booking_date,supplier_id,attention,ready_to_approved,team_leader,dealing_marchant,inserted_by,insert_date,entry_form_id,style_desc";
	$data_array ="(".$id.",4,'".$new_booking_no[1]."',".$new_booking_no[2].",'".$new_booking_no[0]."',".$cbo_company_name.",".$cbo_buyer_name.",'2',".$cbo_fabric_source.",".$cbo_currency.",".$cbo_sources.",".$txt_buyer_req_no.",".$txt_revise_no.",".$txt_exchange_rate.",".$cbo_pay_mode.",".$txt_booking_date.",".$cbo_supplier_name.",".$txt_attention.",".$cbo_ready_to_approved_book.",".$cbo_team_leader_book.",".$cbo_dealing_merchant_book.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."','140',".$txt_style_desc.")";

	*/
	
	/*
	$id_dtls=return_next_id( "id", "wo_non_ord_samp_booking_dtls", 1 ) ;
	$field_array_dtls= "id,booking_no,style_id,sample_type,gmts_item_id,body_part,fabric_source, fabric_description,gsm_weight,dia,color_all_data,color_type_id,dia_width,uom,req_dzn,finish_fabric,dtls_id,inserted_by,insert_date,status_active,is_deleted,entry_form_id,process_loss,grey_fabric,lib_yarn_count_deter_id,remarks,gmts_color,fabric_color,delivery_date";//wo_non_ord_samp_book_dtls_id
	$data_array_dtls .="(".$id_dtls.",'".$new_booking_no."',".$update_id.",".$$cboRfSampleName.",".$$cboRfGarmentItem.",".$$cboRfBodyPart.",".$$cboRfFabricSource.",".$$txtRfFabricDescription.",".$$txtRfGsm.",".$$txtRfDia.",".$$txtRfColorAllData.",".$$cboRfColorType.",".$$cboRfWidthDia.",".$$cboRfUom.",".$$txtRfReqDzn.",'".$qnty2."',".$$required_fab_id.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."','1','0','140','".$txtProcessLoss2."','".$txtGrayFabric2."',".$$libyarncountdeterminationid.",".$$txtRfRemarks.",'".$colorId."','".$fab_col_id."',".$$fabricDelvDate.")";
	
	*/

	/*
	$sql ="
		SELECT
			a.requisition_number, a.requisition_date, a.buyer_name, a.season, a.dealing_marchant
			, c.booking_no 
		FROM sample_development_mst a
			INNER JOIN sample_development_dtls b ON b.sample_mst_id = a. id
			INNER JOIN wo_non_ord_samp_booking_dtls c ON c.style_id = a. id
			INNER JOIN wo_non_ord_samp_booking_mst d ON d.booking_no = c.booking_no
		WHERE
			a.status_active = 1
			AND a.is_deleted = 0
			AND a.entry_form_id = 203
			AND a.company_id = ".$company_id."
			".$buyer_condition."
			".$season_condition."
			".$requisitionNo_condition."
			AND b.entry_form_id = 203
			AND b.status_active = 1
			AND b.is_deleted = 0
			AND c.status_active = 1
			AND c.is_deleted = 0
			AND c.entry_form_id = 140
			AND d.status_active = 1
			AND d.is_deleted = 0
			AND d.entry_form_id = 140
			AND d.booking_type = 4
	";
	*/
	
	$sqlMst ="
		SELECT
			a.id AS ID, a.company_id AS COMPANY_ID, a.requisition_number AS REQUISITION_NUMBER, a.requisition_date AS REQUISITION_DATE, a.buyer_name AS BUYER_NAME, a.season AS SEASON, a.dealing_marchant AS DEALING_MARCHANT, a.update_date AS UPDATE_DATE, a.style_ref_no AS STYLE_REF_NO
		FROM sample_development_mst a
		WHERE
			a.status_active = 1
			AND a.is_deleted = 0
			AND a.entry_form_id = 203
			AND a.company_id = ".$company_id."
			".$buyer_condition."
			".$season_condition."
			".$requisitionNo_condition."
		";
	//req entry_form = 117
	//echo $sqlMst;
	$sqlMstRslt = sql_select($sqlMst);
	$companyIdArr = array();
	$buyerIdArr = array();
	$dealingMarchantIdArr = array();
	$seasonIdArr = array();
	$dataMstArr = array();
	foreach ($sqlMstRslt as $row)
	{
		$companyIdArr[$row['COMPANY_ID']] = $row['COMPANY_ID'];
		$buyerIdArr[$row['BUYER_NAME']] = $row['BUYER_NAME'];
		$dealingMarchantIdArr[$row['DEALING_MARCHANT']] = $row['DEALING_MARCHANT'];
		$seasonIdArr[$row['SEASON']] = $row['SEASON'];
		
		$dataMstArr[$row['ID']]['COMPANY_ID'] = $row['COMPANY_ID'];
		$dataMstArr[$row['ID']]['REQUISITION_NUMBER'] = $row['REQUISITION_NUMBER'];
		$dataMstArr[$row['ID']]['REQUISITION_DATE'] = $row['REQUISITION_DATE'];
		$dataMstArr[$row['ID']]['BUYER_NAME'] = $row['BUYER_NAME'];
		$dataMstArr[$row['ID']]['SEASON'] = $row['SEASON'];
		$dataMstArr[$row['ID']]['DEALING_MARCHANT'] = $row['DEALING_MARCHANT'];
		$dataMstArr[$row['ID']]['STYLE_REF_NO'] = $row['STYLE_REF_NO'];
		$dataMstArr[$row['ID']]['UPDATE_DATE'] = ($row['UPDATE_DATE'] != '' ? date('d-m-Y', strtotime($row['UPDATE_DATE'])) : '');
	}
	unset($sqlMstRslt);
	
	//for booking info
	$sqlBooking ="
		SELECT
			a.id AS ID, a.booking_no AS BOOKING_NO, a.booking_date AS BOOKING_DATE
			, b.style_id AS STYLE_ID, b.sample_type AS SAMPLE_TYPE
		FROM wo_non_ord_samp_booking_mst a, wo_non_ord_samp_booking_dtls b
		WHERE
			a.booking_no = b.booking_no
			AND a.status_active = 1
			AND a.is_deleted = 0
			AND a.entry_form_id = 140
			AND a.booking_type = 4
			AND b.status_active = 1
			AND b.is_deleted = 0
			AND b.entry_form_id = 140
			".$requisitionNo_condition2."
	";
	//no entry_form Sample Fabric Booking -Without order
	//echo $sqlBooking;
	$sqlBookingRslt = sql_select($sqlBooking);
	$bookingIdArr = array();
	$sampleTypeIdArr = array();
	$dataBookingArr = array();
	$sampleTypeBookingArr = array();
	foreach ($sqlBookingRslt as $row)
	{
		$bookingIdArr[$row['ID']] = $row['ID'];
		$sampleTypeIdArr[$row['SAMPLE_TYPE']] = $row['SAMPLE_TYPE'];
		$sampleTypeBookingArr[$row['BOOKING_NO']] = $row['BOOKING_NO'];
	}

	if ($db_type == 0)
	{
		$all_knit_id = return_field_value("GROUP_CONCAT(DISTINCT(b.id)) AS knit_id", "ppl_planning_info_entry_mst a, ppl_planning_info_entry_dtls b", "a.id = b.mst_id ".where_con_using_array($sampleTypeBookingArr,1,'a.booking_no')." AND a.status_active = 1 AND a.is_deleted = 0 AND b.status_active = 1 AND b.is_deleted = 0", "knit_id");
	}
	else
	{
		$all_knit_id = return_field_value("LISTAGG(b.id, ',') WITHIN GROUP (ORDER BY b.id) as knit_id", "ppl_planning_info_entry_mst a, ppl_planning_info_entry_dtls b", "a.id = b.mst_id ".where_con_using_array($sampleTypeBookingArr,1,'a.booking_no')." AND a.status_active = 1 AND a.is_deleted = 0 AND b.status_active = 1 AND b.is_deleted = 0", "knit_id");
		$all_knit_id = implode(",", array_unique(explode(",", $all_knit_id)));
	}
	//var_dump($all_knit_id);

	if ($all_knit_id != "")
	{
		$req_sql = "SELECT a.booking_no,b.id as program_no, c.knit_id, c.prod_id, c.requisition_no, c.yarn_qnty
		FROM ppl_planning_info_entry_mst a, ppl_planning_info_entry_dtls b, ppl_yarn_requisition_entry c 
		WHERE a.id = b.mst_id AND b.id = c.knit_id AND b.id IN(".$all_knit_id.") AND a.status_active=1 AND a.is_deleted=0 AND b.status_active=1 AND b.is_deleted=0 AND c.status_active=1 AND c.is_deleted=0";
		//echo $req_sql;
		$req_result = sql_select($req_sql);
		$booking_requsition_arr=array();
		$sampleReqArr=array();
		foreach($req_result as $row)
		{
			$booking_requsition_arr[$row[csf("booking_no")]]["requisition_no"] = $row[csf("requisition_no")];
			$booking_requsition_arr[$row[csf("booking_no")]]["program_no"] = $row[csf("program_no")];
			$sampleReqArr[$row[csf("requisition_no")]] = $row[csf("requisition_no")];
			$sampleProgArr[$row[csf("program_no")]] = $row[csf("program_no")];
		}
	}

	$sample_type_arr = return_library_array( "select id, sample_name from lib_sample where 1=1 ".where_con_using_array($sampleTypeIdArr,0,'id'), "id", "sample_name");
	
	foreach ($sqlBookingRslt as $row)
	{
		$dataBookingArr[$row['STYLE_ID']]['BOOKING_NO'] = $row['BOOKING_NO'];
		$dataBookingArr[$row['STYLE_ID']]['BOOKING_DATE'] = ($row['BOOKING_DATE'] != '' ? date('d-m-Y', strtotime($row['BOOKING_DATE'])) : '');
		$dataBookingArr[$row['STYLE_ID']]['SAMPLE_TYPE'][$row['SAMPLE_TYPE']] = $sample_type_arr[$row['SAMPLE_TYPE']];
		

	}
	unset($sqlBookingRslt);
	
	//
	//for fabric required
	//$sqlRequired="SELECT a.sample_mst_id AS SAMPLE_MST_ID, a.sample_name AS SAMPLE_NAME, a.gmts_item_id AS GMTS_ITEM_ID, a.delivery_date AS DELIVERY_DATE, a.fabric_description AS FABRIC_DESCRIPTION, a.body_part_id AS BODY_PART_ID, a.fabric_source AS FABRIC_SOURCE, a.remarks_ra AS REMARKS_RA, a.gsm AS GSM, a.dia AS DIA, a.color_type_id AS COLOR_TYPE_ID, a.width_dia_id AS WIDTH_DIA_ID, a.uom_id AS UOM_ID, b.color_id AS COLOR_ID, b.contrast AS CONTRAST, b.process_loss_percent AS PROCESS_LOSS_PERCENT, c.grey_fabric AS GREY_FAB_QNTY, c.finish_fabric AS QNTY 
	$sqlRequired="SELECT a.sample_mst_id AS SAMPLE_MST_ID, a.body_part_id AS BODY_PART_ID, a.delivery_date AS DELIVERY_DATE, a.fabric_description AS FABRIC_DESCRIPTION, a.gsm AS GSM, a.color_type_id AS COLOR_TYPE_ID, a.determination_id AS DETERMINATION_ID, a.required_qty AS REQUIRED_QTY, b.color_id AS COLOR_ID, c.grey_fabric AS GREY_FAB_QNTY 
	FROM sample_development_fabric_acc a, sample_development_rf_color b, wo_non_ord_samp_booking_dtls c 
	WHERE a.id=b.dtls_id and  a.sample_mst_id=b.mst_id and a.id=c.dtls_id and c.fabric_color=b.fabric_color and c.gmts_color=b.color_id and c.dtls_id=b.dtls_id and c.style_id=a.sample_mst_id and c.style_id=b.mst_id and a.determination_id=c.lib_yarn_count_deter_id and a.form_type=1 and b.qnty>0 and c.grey_fabric>0 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0".$requisitionNo_condition3.$requisitionNo_condition4;
	//echo $sqlRequired;
	$sqlRequiredRslt = sql_select($sqlRequired);
	$dataRequiredArr = array();
	$colorIdArr = array();
	foreach ($sqlRequiredRslt as $row)
	{
		$colorIdArr[$row['COLOR_ID']] = $row['COLOR_ID'];
		
		$fab_des_gsm = $row['FABRIC_DESCRIPTION'].', '.$row['GSM'];
		$delivery_date = ($row['DELIVERY_DATE']!=''?date('d-m-Y', strtotime($row['DELIVERY_DATE'])):'');
		
		$dataRequiredArr[$row['SAMPLE_MST_ID']][$row['BODY_PART_ID']][$row['COLOR_ID']][$row['COLOR_TYPE_ID']][$row['DETERMINATION_ID']]['DELIVERY_DATE'] = $delivery_date;
		$dataRequiredArr[$row['SAMPLE_MST_ID']][$row['BODY_PART_ID']][$row['COLOR_ID']][$row['COLOR_TYPE_ID']][$row['DETERMINATION_ID']]['GREY_FAB_QNTY'] = $row['GREY_FAB_QNTY'];
		$dataRequiredArr[$row['SAMPLE_MST_ID']][$row['BODY_PART_ID']][$row['COLOR_ID']][$row['COLOR_TYPE_ID']][$row['DETERMINATION_ID']]['REQUIRED_QTY'] = $row['REQUIRED_QTY'];
		$dataRequiredArr[$row['SAMPLE_MST_ID']][$row['BODY_PART_ID']][$row['COLOR_ID']][$row['COLOR_TYPE_ID']][$row['DETERMINATION_ID']]['FABRIC_DESCRIPTION'] = $fab_des_gsm;
		$dataRequiredArr[$row['SAMPLE_MST_ID']][$row['BODY_PART_ID']][$row['COLOR_ID']][$row['COLOR_TYPE_ID']][$row['DETERMINATION_ID']]['GSM'] = $row['GSM'];
	}
	unset($sqlRequiredRslt);
	//echo "<pre>";
	//print_r($dataRequiredArr);
	//OG-YIS-20-00409, OG-GPE-20-00336
	
	//for yarn issue
	$sqlYarn = "
			SELECT
			a.booking_no AS BOOKING_NO, b.cons_quantity AS CONS_QUANTITY, b.requisition_no as REQUISITION_NO, b.receive_basis as RECEIVE_BASIS 
		FROM inv_issue_master a, inv_transaction b
		WHERE a.id = b.mst_id
			AND a.entry_form = 3
			AND a.item_category = 1
			AND issue_purpose IN(4,8)
			AND a.status_active = 1
			AND a.is_deleted = 0
			AND b.transaction_type = 2
			AND b.status_active = 1
			AND b.is_deleted = 0
			".where_con_using_array($bookingIdArr,0,'a.booking_id')."
			union all 
			SELECT
			a.booking_no AS BOOKING_NO, b.cons_quantity AS CONS_QUANTITY, b.requisition_no as REQUISITION_NO, b.receive_basis as RECEIVE_BASIS  
		FROM inv_issue_master a, inv_transaction b
		WHERE a.id = b.mst_id
			AND a.entry_form = 3
			AND a.item_category = 1
			AND issue_purpose IN(4,8)
			AND a.status_active = 1
			AND a.is_deleted = 0
			AND b.transaction_type = 2
			AND b.status_active = 1
			AND b.is_deleted = 0
			".where_con_using_array($sampleReqArr,0,'b.requisition_no')."
	";
	//echo $sqlYarn;
	$sqlYarnRslt = sql_select($sqlYarn);
	$dataYarnArr = array();
	foreach ($sqlYarnRslt as $row)
	{
		if($row['RECEIVE_BASIS']==3)
		{
			$dataYarnArr[$row['REQUISITION_NO']]['ISSUE_QTY'] += $row['CONS_QUANTITY'];
		}else{
			$dataYarnArr[$row['BOOKING_NO']]['ISSUE_QTY'] += $row['CONS_QUANTITY'];
		}

		//$dataYarnArr[$row['BOOKING_NO']]['ISSUE_QTY'] += $row['CONS_QUANTITY'];
	}
	unset($sqlYarnRslt);
	//echo "<pre>";
	//print_r($dataYarnArr);
	
	//for knitting
	$sqlKnitting = "
		SELECT
			a.booking_no AS BOOKING_NO, a.receive_date AS RECEIVE_DATE, b.febric_description_id AS FEBRIC_DESCRIPTION_ID, b.grey_receive_qnty AS GREY_RECEIVE_QNTY 
		FROM inv_receive_master a, pro_grey_prod_entry_dtls b
		WHERE a.id = b.mst_id
			AND a.entry_form = 2
			AND a.item_category = 13
			AND a.status_active = 1
        	AND a.is_deleted = 0
			AND b.status_active = 1
        	AND b.is_deleted = 0
			AND a.company_id = ".$company_id."
			".where_con_using_array($bookingIdArr,0,'a.booking_id')."			
	";
	//echo $sqlKnitting;
	$sqlKnittingRslt = sql_select($sqlKnitting);
	$dataKnittingArr = array();
	foreach ($sqlKnittingRslt as $row)
	{
		//bad practice
		/*$determination_sql = sql_select("select a.construction, b.copmposition_id, b.percent from lib_yarn_count_determina_mst a, lib_yarn_count_determina_dtls b where a.id=b.mst_id and a.id=".$row['FEBRIC_DESCRIPTION_ID']);
		if ($determination_sql[0][csf('construction')] != "")
		{
			$comp = $determination_sql[0][csf('construction')] . ", ";
		}
		foreach ($determination_sql as $d_row)
		{
			$comp .= $composition[$d_row[csf('copmposition_id')]] . " " . $d_row[csf('percent')] . "% ";
		}*/	
		
		$dataKnittingArr[$row['BOOKING_NO']][$row['FEBRIC_DESCRIPTION_ID']]['RECEIVE_DATE'] = ($row['RECEIVE_DATE'] != '' ? date('d-m-Y', strtotime($row['RECEIVE_DATE'])) : '');
		$dataKnittingArr[$row['BOOKING_NO']][$row['FEBRIC_DESCRIPTION_ID']]['GREY_RECEIVE_QNTY'] += $row['GREY_RECEIVE_QNTY'];
	}
	unset($sqlKnittingRslt);
	//echo "<pre>";
	//print_r($dataKnittingArr);

	//for program wise knitting
	$sqlProgKnitting = "
		SELECT
			a.booking_no AS BOOKING_NO, a.receive_date AS RECEIVE_DATE, b.febric_description_id AS FEBRIC_DESCRIPTION_ID, b.grey_receive_qnty AS GREY_RECEIVE_QNTY 
		FROM inv_receive_master a, pro_grey_prod_entry_dtls b
		WHERE a.id = b.mst_id
			AND a.entry_form = 2
			AND a.item_category = 13
			AND a.status_active = 1
        	AND a.is_deleted = 0
			AND b.status_active = 1
        	AND b.is_deleted = 0
			AND a.company_id = ".$company_id."
			".where_con_using_array($sampleProgArr,1,'a.booking_no')."			
	";
	//echo $sqlProgKnitting;
	$sqlProgKnittingRslt = sql_select($sqlProgKnitting);
	$dataKnittingArr = array();
	foreach ($sqlProgKnittingRslt as $row)
	{
		
		$dataKnittingArr[$row['BOOKING_NO']][$row['FEBRIC_DESCRIPTION_ID']]['RECEIVE_DATE'] = ($row['RECEIVE_DATE'] != '' ? date('d-m-Y', strtotime($row['RECEIVE_DATE'])) : '');
		$dataKnittingArr[$row['BOOKING_NO']][$row['FEBRIC_DESCRIPTION_ID']]['GREY_RECEIVE_QNTY'] += $row['GREY_RECEIVE_QNTY'];
	}
	unset($sqlProgKnittingRslt);
	
	//var_dump($dataKnittingArr);

	//for batch
	$sqlBatch = "
		SELECT
			a.id AS ID, a.batch_no AS BATCH_NO, a.batch_date AS BATCH_DATE, a.booking_no AS BOOKING_NO, a.color_id AS COLOR_ID, a.batch_weight AS BATCH_WEIGHT, b.body_part_id AS BODY_PART_ID
		FROM pro_batch_create_mst a, pro_batch_create_dtls b
		WHERE
			a.id = b.mst_id
			AND a.status_active = 1
        	AND a.is_deleted = 0
			AND a.company_id = ".$company_id."	
			".where_con_using_array($bookingIdArr,0,'a.booking_no_id')."					
	";
	//echo $sqlBatch;
	$sqlBatchRslt = sql_select($sqlBatch);
	$dataBatchArr = array();
	$batchIdArr = array();
	$batchDtls = array();
	foreach ($sqlBatchRslt as $row)
	{
		$batchIdArr[$row['ID']] = $row['ID'];
		$batchDtls[$row['ID']] = $row['BATCH_NO'];
		//$dataBatchArr[$row['BOOKING_NO']][$row['BODY_PART_ID']][$row['COLOR_ID']][$row['BATCH_NO']]['BATCH_DATE'] = ($row['BATCH_DATE'] != '' ? date('d-m-Y', strtotime($row['BATCH_DATE'])) : '');
		//$dataBatchArr[$row['BOOKING_NO']][$row['BODY_PART_ID']][$row['COLOR_ID']][$row['BATCH_NO']]['BATCH_WEIGHT'] += $row['BATCH_WEIGHT'];
		$dataBatchArr[$row['BOOKING_NO']][$row['BODY_PART_ID']][$row['COLOR_ID']]['BATCH_WEIGHT'] += $row['BATCH_WEIGHT'];
		$dataBatchArr[$row['BOOKING_NO']][$row['BODY_PART_ID']][$row['COLOR_ID']]['BATCH_NO'].= $row['BATCH_NO'].',';
		$dataBatchArr[$row['BOOKING_NO']][$row['BODY_PART_ID']][$row['COLOR_ID']]['BATCH_ID'].= $row['ID'].',';
	}
	unset($sqlBatchRslt);
	//echo "<pre>";
	//print_r($dataBatchArr);
	
	//for dyeing
	$sqlDyeing = "
		SELECT
			a.batch_id AS BATCH_ID, a.batch_no AS BATCH_NO, a.process_end_date AS PROCESS_END_DATE
		FROM pro_fab_subprocess a
		WHERE
			a.status_active = 1
        	AND a.is_deleted = 0
			AND a.entry_form = 35
			AND a.load_unload_id = 1
			AND a.company_id = ".$company_id."
			".where_con_using_array($batchIdArr,0,'a.batch_id')."
	";
	//echo $sqlDyeing;
	$sqlDyeingRslt = sql_select($sqlDyeing);
	$dataDyeingArr = array();
	foreach ($sqlDyeingRslt as $row)
	{
		$dataDyeingArr[$row['BATCH_NO']]['PROCESS_END_DATE'] = ($row['PROCESS_END_DATE'] != '' ? date('d-m-Y', strtotime($row['PROCESS_END_DATE'])) : '');
	}
	unset($sqlDyeingRslt);
	//echo "<pre>";
	//print_r($dataDyeingArr);

	//for finish febric
	$sqlFinish = "
		SELECT
			b.batch_id AS BATCH_ID, b.fabric_description_id AS FABRIC_DESCRIPTION_ID, b.gsm AS GSM, b.width AS WIDTH, b.color_id AS COLOR_ID, b.receive_qnty AS RECEIVE_QNTY, b.process_id AS PROCESS_ID, a.entry_form AS ENTRY_FORM, a.receive_date AS RECEIVE_DATE 
		FROM inv_receive_master a, pro_finish_fabric_rcv_dtls b
		WHERE a.id = b.mst_id
			AND a.entry_form in(7,37)
			AND item_category = 2
			AND a.status_active = 1
        	AND a.is_deleted = 0
			AND a.company_id = ".$company_id."
			".where_con_using_array($batchIdArr,0,'b.batch_id')."
	";
	//echo $sqlFinish;
	$sqlFinishRslt = sql_select($sqlFinish);
	$dataFinishArr = array();
	$processIdArr = array();
	$dataFabRcvArr = array();
	foreach ($sqlFinishRslt as $row)
	{
		if($row['ENTRY_FORM'] == 7)
		{
			$processIdArr[$row['PROCESS_ID']] = $row['PROCESS_ID'];
			/*
			$dataFinishArr[$batchDtls[$row['BATCH_ID']]][$row['FABRIC_DESCRIPTION_ID']]['RECEIVE_QNTY'] = $row['RECEIVE_QNTY'];
			$dataFinishArr[$batchDtls[$row['BATCH_ID']]][$row['FABRIC_DESCRIPTION_ID']]['GSM'] = $row['GSM'];
			$dataFinishArr[$batchDtls[$row['BATCH_ID']]][$row['FABRIC_DESCRIPTION_ID']]['WIDTH'] = $row['WIDTH'];
			$dataFinishArr[$batchDtls[$row['BATCH_ID']]][$row['FABRIC_DESCRIPTION_ID']]['PROCESS_ID'] = $row['PROCESS_ID'];
			*/
			$dataFinishArr[$row['COLOR_ID']][$row['FABRIC_DESCRIPTION_ID']]['RECEIVE_QNTY'] += $row['RECEIVE_QNTY'];
			$dataFinishArr[$row['COLOR_ID']][$row['FABRIC_DESCRIPTION_ID']]['PROCESS_ID'] = $row['PROCESS_ID'];
		}
		else if($row['ENTRY_FORM'] == 37)
		{
			$dataFabRcvArr[$row['COLOR_ID']][$row['FABRIC_DESCRIPTION_ID']]['RECEIVE_QNTY'] += $row['RECEIVE_QNTY'];
			$dataFabRcvArr[$row['COLOR_ID']][$row['FABRIC_DESCRIPTION_ID']]['RECEIVE_DATE'] = $row['RECEIVE_DATE'];
		}

	
	}
	unset($sqlFinishRslt);
	//echo "<pre>";
	//print_r($dataFinishArr);

	$sqlRollFinish = "
		SELECT
		b.id AS DTLS_ID, b.fabric_description_id AS FABRIC_DESCRIPTION_ID, b.color_id AS COLOR_ID, a.receive_date AS RECEIVE_DATE , c.cons_quantity AS RECEIVE_QNTY
		FROM inv_receive_master a, pro_finish_fabric_rcv_dtls b, inv_transaction c
		WHERE a.id = b.mst_id 
			AND a.id=c.mst_id
			AND a.entry_form in(68)
			AND a.item_category = 2
			AND a.status_active = 1
        	AND a.is_deleted = 0
			AND a.company_id = ".$company_id."
			".where_con_using_array($batchIdArr,0,'b.batch_id')."
			GROUP BY b.id, b.fabric_description_id, b.color_id, a.receive_date , c.cons_quantity
	";
	//echo $sqlRollFinish;
	$sqlRollFinishRslt = sql_select($sqlRollFinish);

	foreach ($sqlRollFinishRslt as $rows) 
	{
		$dataFabRcvArr[$rows['COLOR_ID']][$rows['FABRIC_DESCRIPTION_ID']]['RECEIVE_QNTY'] += $rows['RECEIVE_QNTY'];
		$dataFabRcvArr[$rrowsow['COLOR_ID']][$rows['FABRIC_DESCRIPTION_ID']]['RECEIVE_DATE'] = $rows['RECEIVE_DATE'];
	}
	unset($sqlRollFinishRslt);


	//for finish febric Issue
	$sqlfinIssue="SELECT b.batch_id AS BATCH_ID,b.issue_qnty AS QNTY,c.color AS COLOR,c.detarmination_id AS DETARMINATION_ID from inv_issue_master a, inv_finish_fabric_issue_dtls b,product_details_master c where a.id=b.mst_id AND b.prod_id=c.id AND a.status_active = 1 AND a.is_deleted = 0 AND b.status_active = 1 AND b.is_deleted = 0 AND c.status_active = 1 AND c.is_deleted = 0 AND a.company_id = ".$company_id." ".where_con_using_array($batchIdArr,0,'b.batch_id')." ";
	//echo $sql;
	$sqlfinIssueRslt = sql_select($sqlfinIssue);
	$finIssueDtls=array();
	foreach ($sqlfinIssueRslt as $rows) 
	{
		$finIssueDtls[$rows['COLOR']][$rows['DETARMINATION_ID']]['ISSUE_QNTY'] += $rows['QNTY'];
	}
	unset($sqlfinIssueRslt);
	// echo "SELECT b.id AS DTLS_ID, c.color AS COLOR,c.detarmination_id AS DETARMINATION_ID from inv_issue_master a, inv_finish_fabric_issue_dtls b, product_details_master c, inv_transaction d where a.id=b.mst_id AND b.prod_id=c.id AND a.id=d.mst_id AND a.entry_form=71 AND a.status_active=1 AND a.is_deleted=0 AND b.status_active=1 AND b.is_deleted=0 AND c.status_active=1 AND c.is_deleted=0 AND d.status_active=1 AND d.is_deleted=0 AND a.company_id = ".$company_id." ".where_con_using_array($batchIdArr,0,'d.pi_wo_batch_no')." group by b.id, c.color,c.detarmination_id ";
	$issued_data=sql_select("SELECT b.id AS DTLS_ID, c.color AS COLOR,c.detarmination_id AS DETARMINATION_ID, d.cons_quantity as QNTY from inv_issue_master a, inv_finish_fabric_issue_dtls b, product_details_master c, inv_transaction d where a.id=b.mst_id AND b.prod_id=c.id AND a.id=d.mst_id AND a.entry_form=71 AND a.status_active=1 AND a.is_deleted=0 AND b.status_active=1 AND b.is_deleted=0 AND c.status_active=1 AND c.is_deleted=0 AND d.status_active=1 AND d.is_deleted=0 AND a.company_id = ".$company_id." ".where_con_using_array($batchIdArr,0,'d.pi_wo_batch_no')." group by b.id, c.color,c.detarmination_id, d.cons_quantity ");
	
	
	foreach ($issued_data as $rows) 
	{
		$finIssueDtls[$rows['COLOR']][$rows['DETARMINATION_ID']]['ISSUE_QNTY'] += $rows['QNTY'];
	}

	unset($issued_data);
	//var_dump($finIssueDtls);

	//for finish delivery
	$sqlFinishDelivery = "
		SELECT
			a.delevery_date AS DELEVERY_DATE, b.determination_id AS DETERMINATION_ID, b.gsm AS GSM, b.dia AS DIA, b.batch_id as BATCH_ID, b.color_id AS COLOR_ID, b.grey_used_qnty AS GREY_USED_QNTY
		FROM pro_grey_prod_delivery_mst a, pro_grey_prod_delivery_dtls b
		WHERE a.id = b.mst_id
			AND a.entry_form = 54
			AND b.entry_form = 54
			".where_con_using_array($batchIdArr,0,'b.batch_id')."
	";
	//echo $sqlFinish;
	$sqlFinishDeliveryRslt = sql_select($sqlFinishDelivery);
	$dataFinishDeliveryArr = array();
	foreach ($sqlFinishDeliveryRslt as $row)
	{
		$dataFinishDeliveryArr[$row['COLOR_ID']][$row['DETERMINATION_ID']]['GREY_USED_QNTY'] = $row['GREY_USED_QNTY'];
		$dataFinishDeliveryArr[$row['COLOR_ID']][$row['DETERMINATION_ID']]['GSM'] = $row['GSM'];
		$dataFinishDeliveryArr[$row['COLOR_ID']][$row['DETERMINATION_ID']]['DIA'] = $row['DIA'];
		$dataFinishDeliveryArr[$row['COLOR_ID']][$row['DETERMINATION_ID']]['DELEVERY_DATE'] = ($row['DELEVERY_DATE'] != '' ? date('d-m-Y', strtotime($row['DELEVERY_DATE'])) : '');
	}
	unset($sqlFinishDeliveryRslt);
	//echo "<pre>";
	//print_r($dataFinishDeliveryArr);

	$color_arr = return_library_array( "select id, color_name from lib_color where 1=1 ".where_con_using_array($colorIdArr,0,'id'), "id", "color_name"  );
	$company_arr = return_library_array("select id, company_name from lib_company where 1=1 ".where_con_using_array($companyIdArr,0,'id'),'id','company_name');
	$buyer_arr = return_library_array("select id, buyer_name from lib_buyer where 1=1 ".where_con_using_array($buyerIdArr,0,'id'), "id", "buyer_name");
	$dealing_merchant_arr = return_library_array( "select id,team_member_name from lib_mkt_team_member_info where 1=1 ".where_con_using_array($dealingMarchantIdArr,0,'id'), "id", "team_member_name"  );
	$season_arr = return_library_array( "select id,season_name from lib_buyer_season  where status_active =1 and is_deleted=0 ".where_con_using_array($seasonIdArr,0,'id'), "id", "season_name"  );

	$tbl_width = 2750;
	?>
    <fieldset style="width:<?php echo $tbl_width+40; ?>px;">
    <table width="<?php echo $tbl_width+20; ?>" cellspacing="0" cellpadding="0" border="1" rules="all" class="rpt_table">
        <thead>
            <th width="40">SL</th>
            <th width="150">Dealing Merchant</th>
            <th width="100">buyer</th>
            <th width="100">Style</th>
            <th width="80">Requisition No</th>
            <th width="100">Booking No</th>
            <th width="70">Booking Date</th>
            <th width="100">Sample Type</th>
            <th width="70">Season 9</th>
            <th width="70">Sample Program Update Date</th>
            <th width="100">Fabric Color</th>
            <th width="80">Color Type</th>
            <th width="150">Fabrication & GSM</th>
            <th width="70">Fab Del Date</th>
            <th width="100">Grey Req. Qty</th>
            <th width="70">Yarn Issue</th>
            <th width="70">Knitting Qty</th>
            <th width="70">Knitting Date</th>
             <th width="70">Batch Qnty</th>
           <!-- <th width="70">Batch Date</th>
            <th width="70">Batch Qty</th>
            <th width="70">Dyeing Prod Date</th> -->
            <th width="70">AOP Production</th>
            <th width="70">Fin. Req. Qty</th>
            <th width="70">Fin Prod</th>
            <th width="70">Fab Rcv Date</th>
            <th width="70">Fin. Del Qty</th>
            <th width="70">Dia / GSM</th>
            <th width="70">Fin. Del Date</th>
            <th width="100">Finish Process</th>
            <th width="70">Fabric Recv</th>
            <th width="70">Fabric Issue</th>
            <th>Remarks</th>
        </thead>
	</table>
	<div style="width:<?php echo $tbl_width+20; ?>px; max-height:450px; overflow-y:scroll" id="list_container_batch" align="left">	 
        <table width="<?php echo $tbl_width; ?>" cellspacing="0" cellpadding="0" border="1" rules="all" class="rpt_table" id="tbl_list_search">  
            <tbody>
            <?
			//for row span
			$rowSpanArr = array();
			foreach($dataRequiredArr as $mstId=>$mstIdArr)
			{
				foreach($mstIdArr as $bodyPart=>$bodyPartArr)
				{
					foreach($bodyPartArr as $clr=>$clrArr)
					{
						foreach($clrArr as $clrType=>$clrTypeArr)
						{
							foreach($clrTypeArr as $fabId=>$fabArr)
							{
								$rowSpanArr[$mstId]++;
							}
						}
					}
				}
			}
			//echo "<pre>";
			//print_r($rowSpanArr);
			
            $i=1;
            foreach ($dataMstArr as $mstId=>$row)
            {  
                if ($i%2==0)
                    $bgcolor="#E9F3FF";
                else
                    $bgcolor="#FFFFFF";
                    
                $row_span = $rowSpanArr[$mstId];
				//echo $row_span.'=';
				//for booking no
				$bookinNo = $dataBookingArr[$mstId]['BOOKING_NO'];
			    $requisition_no = $booking_requsition_arr[$bookinNo]["requisition_no"];
			    $program_no = $booking_requsition_arr[$bookinNo]["program_no"];
				if($requisition_no !="")
				{
					$issue_qty = $dataYarnArr[$requisition_no]['ISSUE_QTY'];
				}
				else{
					$issue_qty = $dataYarnArr[$bookinNo]['ISSUE_QTY'];
				}
				
                ?>
                <tr valign="middle"> 
                    <td width="40" rowspan="<?php echo $row_span; ?>" align="center"><? echo $i; ?></td>
                    <td width="150" rowspan="<?php echo $row_span; ?>" style="word-break:break-all;"><?php echo $dealing_merchant_arr[$row['DEALING_MARCHANT']]; ?></td>
                    <td width="100" rowspan="<?php echo $row_span; ?>" style="word-break:break-all;"><?php echo $buyer_arr[$row['BUYER_NAME']]; ?></td>
                    <td width="100" rowspan="<?php echo $row_span; ?>" style="word-break:break-all;" align="center"><?php echo $row['STYLE_REF_NO']; ?></td>
                    <td width="80" rowspan="<?php echo $row_span; ?>" style="word-break:break-all;" align="center"><?php echo $row['REQUISITION_NUMBER']; ?></td>
                    <td width="100" rowspan="<?php echo $row_span; ?>" style="word-break:break-all;" align="center"><?php echo $bookinNo; ?></td>
                    <td width="70" rowspan="<?php echo $row_span; ?>" style="word-break:break-all;" align="center"><?php echo $dataBookingArr[$mstId]['BOOKING_DATE']; ?></td>
                    <td width="100" rowspan="<?php echo $row_span; ?>" style="word-break:break-all;"><?php echo implode(', ', $dataBookingArr[$mstId]['SAMPLE_TYPE']); ?></td>
                    <td width="70" rowspan="<?php echo $row_span; ?>" style="word-break:break-all;" align="center"><?php echo $season_arr[$row['SEASON']]; ?></td>
                    <td width="70" rowspan="<?php echo $row_span; ?>" style="word-break:break-all;" align="center"><?php echo $row['UPDATE_DATE']; ?></td>
					<?php
                    $isFirst = 1;
					$first = 1;
                    foreach($dataRequiredArr[$mstId] as $bodyPart=>$bodyPartArr)
                    {
					
						foreach($bodyPartArr as $clr=>$clrArr)
						{
							
							foreach($clrArr as $clrType=>$clrTypeArr)
							{
								foreach($clrTypeArr as $fabId=>$fabArr)
								{
									//echo "<pre>";
									//print_r($dataKnittingArr[$bookinNo][$fabId]);
									if($program_no != "")
									{
										$row_span_knit = count($dataKnittingArr[$program_no][$fabId]);
										$grey_receive_qnty = $dataKnittingArr[$program_no][$fabId]['GREY_RECEIVE_QNTY'];
										$receive_date = $dataKnittingArr[$program_no][$fabId]['RECEIVE_DATE'];
									}else{
										$row_span_knit = count($dataKnittingArr[$bookinNo][$fabId]);
										$grey_receive_qnty = $dataKnittingArr[$bookinNo][$fabId]['GREY_RECEIVE_QNTY'];
										$receive_date = $dataKnittingArr[$bookinNo][$fabId]['RECEIVE_DATE'];
									}

									//$row_span_knit = count($dataKnittingArr[$bookinNo][$fabId]);
									//$clr=2;
									$row_span_batch = count($dataBatchArr[$bookinNo][$bodyPart][$clr]);
									//$row_span_knit = ($row_span_knit==0?1:$row_span_knit);
									//$row_span_batch = ($row_span_batch==0?1:$row_span_batch);
									//echo $row_span_knit.'=';
									
									//for finish delivery
									$gsmDia = '';
									if($dataFinishDeliveryArr[$clr][$fabId]['GSM'] != '' || $dataFinishDeliveryArr[$clr][$fabId]['DIA'] != '')
									{
										$gsmDia = $dataFinishDeliveryArr[$clr][$fabId]['GSM'].'/'.$dataFinishDeliveryArr[$clr][$fabId]['DIA'];
									}
	
									$finishDeliveryQty = $dataFinishDeliveryArr[$clr][$fabId]['GREY_USED_QNTY'];
									$finishDeliveryDate = $dataFinishDeliveryArr[$clr][$fabId]['DELEVERY_DATE'];
									
									if($isFirst == 1)
									{
										?>
										<td width="100" style="word-break:break-all;"><?php echo $color_arr[$clr]; ?></td>
										<td width="80" style="word-break:break-all;"><?php echo $color_type[$clrType]; ?></td>
										<td width="150" style="word-break:break-all;"><?php echo $fabArr['FABRIC_DESCRIPTION']; ?></td>
										<td width="70" style="word-break:break-all;" align="center"><?php echo $fabArr['DELIVERY_DATE']; ?></td>
										<td width="100" style="word-break:break-all;" align="right"><?php echo number_format($fabArr['GREY_FAB_QNTY'], 4); ?></td>
										<td width="70" style="word-break:break-all;" align="right" rowspan="<?php echo ($row_span==0?1:$row_span); ?>">
											<?php 
											
											echo number_format($issue_qty, 2); 
											?>
										</td>
										<td width="70" style="word-break:break-all;" align="right" rowspan="<?php echo ($row_span_knit==0?1:$row_span_knit); ?>"><?php echo number_format($grey_receive_qnty, 2); ?></td>
										<td width="70" style="word-break:break-all;" align="center" rowspan="<?php echo ($row_span_knit==0?1:$row_span_knit); ?>"><?php echo $receive_date; ?></td>

										<td width="70" style="word-break:break-all;" align="center"><p><a href='#report_details' onClick="openmypage('<? echo chop($dataBatchArr[$bookinNo][ $bodyPart][$clr]['BATCH_ID'],","); ?>','<? echo $bookinNo; ?>','<? echo $bodyPart; ?>','<? echo $clr; ?>','batch_info_popup');"><? echo number_format($dataBatchArr[$bookinNo][ $bodyPart][$clr]['BATCH_WEIGHT'],2,'.',''); ?></a></p></td>
										
										<td width="70" style="word-break:break-all;" rowspan="<?php echo $row_span; ?>">AOP Production</td>
										<td width="70" style="word-break:break-all;" align="right"><?php echo number_format($fabArr['REQUIRED_QTY'], 2); ?></td>
										<td width="70" style="word-break:break-all;" align="right"><?php echo number_format($dataFinishArr[$clr][$fabId]['RECEIVE_QNTY'], 2); ?></td>
										<td width="70" style="word-break:break-all;" rowspan="<?php echo $row_span; ?>"><?php echo change_date_format($dataFabRcvArr[$clr][$fabId]['RECEIVE_DATE']); ?></td>
										<td width="70" style="word-break:break-all;" align="right"><?php echo number_format($finishDeliveryQty, 2); ?></td>
										<td width="70" style="word-break:break-all;" align="center"><?php echo $gsmDia; ?></td>
										<td width="70" style="word-break:break-all;" align="center"><?php echo $finishDeliveryDate; ?></td>
										<td width="100" style="word-break:break-all;"><?php echo $conversion_cost_head_array[$dataFinishArr[$clr][$fabId]['PROCESS_ID']]; ?></td>
										<td width="70" style="word-break:break-all;" rowspan="<?php echo $row_span; ?>" align="right"><?php $receive_qnty=$dataFabRcvArr[$clr][$fabId]['RECEIVE_QNTY'];echo number_format($receive_qnty, 2); ?></td>
										<td width="70" style="word-break:break-all;" rowspan="<?php echo $row_span; ?>" align="right"><?php $issue_qnty =  $finIssueDtls[$clr][$fabId]['ISSUE_QNTY'];echo number_format($issue_qnty,2);?></td>
										<td style="word-break:break-all;" rowspan="<?php echo $row_span; ?>">&nbsp;</td>
									</tr>
									<?php
									$total_knitting_qnty += number_format($dataKnittingArr[$bookinNo][$fabId]['GREY_RECEIVE_QNTY'], 2, '.', '');
									$total_finish_req_qnty += number_format($fabArr['REQUIRED_QTY'], 2, '.', '');
									$total_finish_dlv_qnty += number_format($finishDeliveryQty, 2, '.', '');
									$total_fab_receive_qnty += number_format($receive_qnty, 2, '.', '');
									$total_fab_issue_qnty += number_format($issue_qnty, 2, '.', '');
									$first++;
									}
									else
									{
										?>
										<tr valign="middle">
											<td width="100" style="word-break:break-all;"><?php echo $color_arr[$clr]; ?></td>
											<td width="80" style="word-break:break-all;"><?php echo $color_type[$clrType]; ?></td>
											<td width="150" style="word-break:break-all;"><?php echo $fabArr['FABRIC_DESCRIPTION']; ?></td>
											<td width="70" style="word-break:break-all;" align="center"><?php echo $fabArr['DELIVERY_DATE']; ?></td>
											<td width="100" style="word-break:break-all;" align="right"><?php echo number_format($fabArr['GREY_FAB_QNTY'], 4); ?></td>
											<?php
											if($row_span_knit < $isFirst)
											{
												?>
												<td width="70" align="right" rowspan="<?php echo ($row_span_knit==0?1:$row_span_knit); ?>"><?php echo number_format($dataKnittingArr[$bookinNo][$fabId]['GREY_RECEIVE_QNTY'], 2); ?></td>
												<td width="70" align="center" rowspan="<?php echo ($row_span_knit==0?1:$row_span_knit); ?>"><?php echo $dataKnittingArr[$bookinNo][$fabId]['RECEIVE_DATE']; ?></td>
												<?php
                                                $total_knitting_qnty += number_format($dataKnittingArr[$bookinNo][$fabId]['GREY_RECEIVE_QNTY'], 2, '.', '');
											}
											
											
											?>
											<td width="70" style="word-break:break-all;" align="center"><p><a href='#report_details' onClick="openmypage('<? echo chop($dataBatchArr[$bookinNo][ $bodyPart][$clr]['BATCH_ID'],","); ?>','<? echo $bookinNo; ?>','<? echo $bodyPart; ?>','<? echo $clr; ?>','batch_info_popup');"><? echo number_format($dataBatchArr[$bookinNo][ $bodyPart][$clr]['BATCH_WEIGHT'],2,'.',''); ?></a></p></td>
											





											<td width="70" align="right"><?php echo number_format($fabArr['REQUIRED_QTY'], 2); ?></td>
											<td width="70" align="right"><?php echo number_format($dataFinishArr[$clr][$fabId]['RECEIVE_QNTY'], 2); ?></td>
											<td width="70" align="right"><?php echo number_format($finishDeliveryQty, 2); ?></td>
											<td width="70" align="center"><?php echo $gsmDia; ?></td>
											<td width="70" align="center"><?php echo $finishDeliveryDate; ?></td>
											<td width="100"><?php echo $conversion_cost_head_array[$dataFinishArr[$clr][$fabId]['PROCESS_ID']]; ?></td>
										</tr>
										<?php
										$total_finish_req_qnty += number_format($fabArr['REQUIRED_QTY'], 2, '.', '');
										$total_finish_dlv_qnty += number_format($finishDeliveryQty, 2, '.', '');
									}
									$isFirst++;
									$total_grey_fab_qnty += number_format($fabArr['GREY_FAB_QNTY'], 4, '.', '');
									/*if($isFirst == 3)
									{
										die;
									}*/
								}
							}
						}
					}
                    $i++;
                    $total_issue_qnty += number_format($dataYarnArr[$bookinNo]['ISSUE_QTY'], 2, '.', '');
					$total_batch_qnty += number_format($dataBatchArr[$bookinNo][ $bodyPart][$clr]['BATCH_WEIGHT'], 2, '.', '');
            }
            ?>
            </tbody>
            <tfoot>
            	<tr>
                	<th colspan="14">Total</th>
                    <th><?php echo number_format($total_grey_fab_qnty, 4); ?></th>
                    <th><?php echo number_format($total_issue_qnty, 2); ?></th>
                    <th><?php echo number_format($total_knitting_qnty, 2); ?></th>
                    <th></th>
                    <th><?php echo number_format($total_batch_qnty, 2); ?></th>
                    <th></th>
                    <th><?php echo number_format($total_finish_req_qnty, 2); ?></th>
                    <th></th>
                    <th></th>
                    <th><?php echo number_format($total_finish_dlv_qnty, 2); ?></th>
					<th></th>
                    <th></th>
					<th></th>
                    <th><?php echo number_format($total_fab_receive_qnty, 2); ?></th>
					<th><?php echo number_format($total_fab_issue_qnty, 2); ?></th>
                    <th></th>
                </tr>
            </tfoot>
        </table>
    </div>
    </fieldset>
	<?	
    exit();
}

if($action=="batch_info_popup")
{
	echo load_html_head_contents("Receive Info", "../../../", 1, 1,'','','');
	extract($_REQUEST);
	//echo $batchId; +bookingNo+'&color
	?>
	<fieldset style="width:350px; margin-left:3px">
		<script>
			function print_window()
			{
				var w = window.open("Surprise", "#");
				var d = w.document.open();
				d.write ('<!DOCTYPE HTML PUBLIC "-//W3C//DTsD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+
					'<html><head><link rel="stylesheet" href="../../../css/style_common.css" type="text/css" media="print"/><title></title></head><body>'+document.getElementById('scroll_body').innerHTML+'</body</html>');
				d.close();
			}

		</script>
		<?
		ob_start();
		//$color_arr=return_library_array( "select id,color_name from lib_color where id in(".$color.")", "id", "color_name"  );
		//$batch_no_arr=return_library_array( "select id,batch_no from pro_batch_create_mst where id in(".$batchId.")", "id", "batch_no"  );

		?>
		<div id="scroll_body" align="center">
			<table border="0" class="rpt_table" rules="all" cellpadding="0" cellspacing="0">
				<tr>
					<td><input  type="button" value="Print Preview" onClick="print_window()" style="width:90px"  class="formbutton"/> &nbsp; </td>
					<td> <div id="report_container"> </div> </td>
				</tr>
			</table>
			<table border="1" class="rpt_table" rules="all" width="350" cellpadding="0" cellspacing="0" align="center">
				<thead>
					<tr>
						<th width="30">Sl</th>
						<th width="110">Batch No</th>
						<th width="70">Batch Date</th>
						<th>Batch Qnty</th>
					</tr>
				</thead>
				<tbody>
					<?
					
					$prod_array=array();
					
					$i=1;
					$sqlBatch = "
					SELECT
						a.batch_no AS BATCH_NO, a.batch_date AS BATCH_DATE, a.booking_no AS BOOKING_NO, a.color_id AS COLOR_ID, sum(a.batch_weight) AS BATCH_WEIGHT, b.body_part_id AS BODY_PART_ID
					FROM pro_batch_create_mst a, pro_batch_create_dtls b
					WHERE
						a.id = b.mst_id
						AND a.status_active = 1
						AND a.is_deleted = 0 
						and a.id in($batchId) and a.color_id=".$color." and b.body_part_id = ".$bodypart."
						AND a.company_id = ".$companyID."	and a.booking_no = '".$bookingNo."' group by  a.batch_no, a.batch_date, a.booking_no, a.color_id,  b.body_part_id";
					//echo $sqlBatch;
					$dtlsArray=sql_select($sqlBatch);
					$tot_qty=0;	
					foreach($dtlsArray as $row)
					{
						if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";

						
						?>
						<tr bgcolor="<? echo  $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor;?>')" id="tr_<? echo $i;?>">
							<td width="30"><p><? echo $i; ?></p></td>
							<td width="110"><p><? echo $row[csf('BATCH_NO')]; ?></p></td>
							<td align="center" width="70"><p><? echo change_date_format($row[csf('BATCH_DATE')]); ?></p></td>
							<td align="right"><p><? echo  number_format($row[csf('batch_weight')],2); ?></p></td>
						</tr>
						<?
						$tot_qty+=$row[csf('batch_weight')];
						
						$i++;
					}
					?>
				</tbody>
				<tfoot>
					<tr class="tbl_bottom">
						<td colspan="2" align="right"></td>
						<td align="right">Total<? //echo number_format($tot_booking_qty,2); ?> </td>
						<td align="right"><? echo number_format($tot_qty,2); ?> </td>
					</tr>
				</tfoot>
			</table>    
         </div>
     </fieldset>
     <?
     $html=ob_get_contents();
     ob_flush();

     foreach (glob(""."*.xls") as $filename)
     {
     	@unlink($filename);
     }

			//html to xls convert
     $name=time();
     $name=$user_id."_".$name.".xls";
     $create_new_excel = fopen(''.$name, 'w');
     $is_created = fwrite($create_new_excel,$html);

     ?>
     <input type="hidden" id="txt_excl_link" value="<? echo 'requires/'.$name; ?>" />
     <script>
     	$(document).ready(function(e) {
     		document.getElementById('report_container').innerHTML='<a href="<? echo $name?>" style="text-decoration:none"><input type="button" value="Excel Download" name="excel" id="excel" class="formbutton" style="width:90px;font-size:11px"/></a>&nbsp;&nbsp;';
     	});

     </script>
     <?
     exit();
}

/*
|--------------------------------------------------------------------------
| for print button-1
|--------------------------------------------------------------------------
|
*/
if($action=="action_print_1")
{
	$expData = explode('*',$data);
    $company_id = $expData[0]; 
    $id = implode(',',explode("_",$expData[1]));
	
	$sqlBatchBarcode="SELECT r.barcode_no FROM pro_roll_details r WHERE r.entry_form = 63 AND r.roll_no>0 AND r.status_active = 1 AND r.is_deleted = 0 AND r.mst_id IN( ".$id.")";
	//echo $sqlBatchBarcode; die;
	
	$sql=" SELECT a.id AS ID, a.entry_form AS ENTRY_FORM, a.company_id AS COMPANY_ID, a.receive_basis AS RECEIVE_BASIS, a.booking_no AS RECEIVE_BASIS, a.booking_id AS BOOKING_ID, a.knitting_source AS KNITTING_SOURCE, a.knitting_company AS KNITTING_COMPANY, b.id as DTLS_ID, b.prod_id AS PROD_ID, b.body_part_id AS BODY_PART_ID, b.febric_description_id AS FEBRIC_DESCRIPTION_ID, b.gsm AS GSM, b.width AS WIDTH, b.color_id AS COLOR_ID, c.mst_id AS MST_ID, c.barcode_no AS BARCODE_NO, c.id as ROLL_ID, c.roll_no AS ROLL_NO, c.po_breakdown_id AS PO_BREAKDOWN_ID, c.qnty AS QNTY, c.qc_pass_qnty_pcs AS QC_PASS_QNTY_PCS FROM inv_receive_master a INNER JOIN pro_grey_prod_entry_dtls b ON a.id = b.mst_id INNER JOIN pro_roll_details c ON b.id = c.dtls_id WHERE a.receive_basis<>9 AND a.entry_form IN(2,22) AND c.entry_form IN(2,22) AND c.roll_no>0 AND c.status_active = 1 AND c.is_deleted = 0 AND c.barcode_no IN(".$sqlBatchBarcode.")";
	$sqlRslt=sql_select($sql);
	$barCode=array();
	$poBreakdownId=array();
	$yarnCountDeterminId=array();
	$bookingId=array();
	$dataArr = array();
	foreach($sqlRslt as $row)
	{
		$barCode[$row['BARCODE_NO']]=$row['BARCODE_NO'];
		$poBreakdownId[$row['PO_BREAKDOWN_ID']]=$row['PO_BREAKDOWN_ID'];
		$yarnCountDeterminId[$row['FEBRIC_DESCRIPTION_ID']]=$row['FEBRIC_DESCRIPTION_ID'];
		$bookingId[$row['BOOKING_ID']]=$row['BOOKING_ID'];
		
		$dataArr[$row['BARCODE_NO']]['body_part_id'] = $row['BODY_PART_ID'];
		$dataArr[$row['BARCODE_NO']]['febric_description_id'] = $row['FEBRIC_DESCRIPTION_ID'];
		$dataArr[$row['BARCODE_NO']]['width'] = $row['WIDTH'];
		$dataArr[$row['BARCODE_NO']]['gsm'] = $row['GSM'];
	}
	//echo "<pre>";
	//print_r($yarnCountDeterminId); die;
	
	$sql2 = "SELECT a.wo_no AS WO_NO, a.recv_number AS RECV_NUMBER, a.dyeing_source AS DYEING_SOURCE, a.dyeing_company AS DYEING_COMPANY, a.receive_date AS RECEIVE_DATE, b.roll_wgt AS ROLL_WGT, b.roll_id AS ROLL_ID, b.order_id AS ORDER_ID, b.color_id AS COLOR_ID, b.batch_id AS BATCH_ID, b.process_id AS PROCESS_ID, c.barcode_no AS BARCODE_NO FROM inv_receive_mas_batchroll a INNER JOIN pro_grey_batch_dtls b ON a.id = b.mst_id INNER JOIN pro_roll_details c ON a.id = c.mst_id AND b.id = c.dtls_id WHERE a.id IN(".$id.") AND a.company_id = ".$company_id." AND a.entry_form = 63 AND c.entry_form = 63 AND a.status_active = 1 AND a.is_deleted = 0 AND b.status_active = 1 AND b.is_deleted = 0 AND c.status_active = 1 AND c.is_deleted = 0 ORDER BY a.id"; 
	//echo $sql2;
	$sql2Rslt = sql_select($sql2);
	$colorIdArr = array();
	$companySupplierArr = array();
	foreach($sql2Rslt as $row)
	{
		//for company supplier
		$companySupplierArr[$row['DYEING_COMPANY']] = $row['DYEING_COMPANY'];
		
		//for color id
		$expColor = explode(',',$row['COLOR_ID']);
		foreach($expColor as $key=>$val)
		{
			$colorIdArr[$val] = $val;
		}
	}
	//echo "<pre>";
	//print_r($companySupplierArr);
	
	$color_arr = return_library_array("select id, color_name from lib_color where id in(".implode(',',$colorIdArr).")","id","color_name");
	$company_name_array = return_library_array("select id,company_name from lib_company where id in(".implode(',',$companySupplierArr).")", "id", "company_name");
	$supplier_arr=return_library_array( "select id, supplier_name from lib_supplier where id in(".implode(',',$companySupplierArr).")",'id','supplier_name');
	$supplier_address_arr=return_library_array( "select id, address_1 from lib_supplier where id in(".implode(',',$companySupplierArr).")",'id','address_1');
	$buyer_name_array=return_library_array("select id, short_name from lib_buyer", "id", "short_name");

	//for batch
	$batchArray = get_batchFor_GreyRollIssueToProcess($barCode);
	//echo "<pre>";
	//print_r($batchArray); die;
	
	//for Yarn Count Determin
	//$yarnCountDeterminArray = get_constructionComposition($yarnCountDeterminId);
	//echo "<pre>";
	//print_r($yarnCountDeterminArray);
	
	//for buyer
	$poArray = get_buyerFor_GreyRollIssueToProcess($poBreakdownId);
	//echo "<pre>";
	//print_r($poArray); die;
	
	//for dia type
	//$diaTypeArray = get_dia_type($bookingId);

	$rptArr = array();
	$noOfRoll = 0;
	$service_company = '';
	$issue_date = '';
	foreach($sql2Rslt as $row)
	{
		$service_company = get_knitting_company_details($row['DYEING_SOURCE'], $row['DYEING_COMPANY']);
		$service_company_address = $supplier_address_arr[$row['DYEING_COMPANY']];
		$issue_date = $row['RECEIVE_DATE'];

		$body_part_id = $dataArr[$row['BARCODE_NO']]['body_part_id'];
		$batch_no = $batchArray[$row['BARCODE_NO']][$row['ORDER_ID']]['batch_no'];
		$febric_description_id = $dataArr[$row['BARCODE_NO']]['febric_description_id'];
		$dia = $dataArr[$row['BARCODE_NO']]['width'];
		$gsm = $dataArr[$row['BARCODE_NO']]['gsm'];

		$rptArr[$row['RECV_NUMBER']][$body_part_id][$batch_no][$febric_description_id][$dia][$gsm]['booking_no'] = $row['WO_NO'];
		$rptArr[$row['RECV_NUMBER']][$body_part_id][$batch_no][$febric_description_id][$dia][$gsm]['process_id'] = $row['PROCESS_ID'];
		$rptArr[$row['RECV_NUMBER']][$body_part_id][$batch_no][$febric_description_id][$dia][$gsm]['buyer'] = $poArray[$row['ORDER_ID']]['buyer_name'];
		$rptArr[$row['RECV_NUMBER']][$body_part_id][$batch_no][$febric_description_id][$dia][$gsm]['file_no'] = $poArray[$row['ORDER_ID']]['file_no'];
		$rptArr[$row['RECV_NUMBER']][$body_part_id][$batch_no][$febric_description_id][$dia][$gsm]['ref_no'] = $poArray[$row['ORDER_ID']]['ref_no'];
		$rptArr[$row['RECV_NUMBER']][$body_part_id][$batch_no][$febric_description_id][$dia][$gsm]['color'] = get_color_details($row['COLOR_ID']);
		
		if(isset($rptArr[$row['RECV_NUMBER']][$body_part_id][$batch_no][$febric_description_id][$dia][$gsm]['no_of_roll']))
		{
			$noOfRoll = $rptArr[$row['RECV_NUMBER']][$body_part_id][$batch_no][$febric_description_id][$dia][$gsm]['no_of_roll'];
		}
		else
		{
			$noOfRoll = 0;	
		}
		
		$rptArr[$row['RECV_NUMBER']][$body_part_id][$batch_no][$febric_description_id][$dia][$gsm]['no_of_roll'] = $noOfRoll+1;
		$rptArr[$row['RECV_NUMBER']][$body_part_id][$batch_no][$febric_description_id][$dia][$gsm]['roll_wgt'] += $row['ROLL_WGT'];
	}
	//echo "<pre>";
	//print_r($rptArr);
	
	//for company details
	$company_array=array();
	$company_data=sql_select("SELECT id AS ID, company_name AS COMPANY_NAME, company_short_name AS COMPANY_SHORT_NAME, plot_no AS PLOT_NO, level_no AS LEVEL_NO, road_no AS ROAD_NO, block_no AS BLOCK_NO, country_id AS COUNTRY_ID, province AS PROVINCE, city AS CITY, zip_code AS ZIP_CODE, email AS EMAIL, website AS WEBSITE FROM lib_company WHERE id=".$company_id."");
	foreach($company_data as $row)
	{
		$company_array['name']=$row['COMPANY_NAME'];
		$company_array['shortname']=$row['COMPANY_SHORT_NAME'];
		$company_array['plot_no']=$row['PLOT_NO'];
		$company_array['level_no']=$row['LEVEL_NO'];
		$company_array['road_no']=$row['ROAD_NO'];
		$company_array['block_no']=$row['BLOCK_NO'];
		$company_array['city']=$row['CITY'];
		$company_array['zip_code']=$row['ZIP_CODE'];
		$company_array['province']=$row['PROVINCE'];
		$company_array['country_id']=$row['COUNTRY_ID'];
		$company_array['email']=$row['EMAIL'];
		$company_array['website']=$row['WEBSITE'];
	}
	
	//for company logo
	$image_location=return_field_value("image_location","common_photo_library","file_type=1 and form_name='company_details' and master_tble_id='".$expData[0]."'","image_location");
	?>
    <table width="1220" cellspacing="0">
        <tr>
            <td width="200" rowspan="3">
                <img src="../../<? echo $image_location; ?>" height="70" width="200" />
            </td>
            <td colspan="6" align="center" style="font-size:22px">
                <strong><? echo $company_array['name']; ?></strong>
            </td>
            <td width="200"></td>
        </tr>
        <tr class="form_caption">
            <td colspan="6" align="center" style="font-size:14px">  
                Plot No: <? echo $company_array['plot_no']; ?> 
                Level No: <? echo $company_array['level_no']?>
                Road No: <? echo $company_array['road_no']; ?> 
                Block No: <? echo $company_array['block_no'];?> 
                City No: <? echo $company_array['city'];?> 
                Zip Code: <? echo $company_array['zip_code']; ?><br> 
                Province No: <?php echo $company_array['province'];?> 
                Country: <? echo $country_arr[$company_array['country_id']]; ?> 
                Email Address: <? echo $company_array['email'];?> 
                Website No: <? echo $company_array['website'];?>
            </td>
            <td></td>  
        </tr>
        <tr>
            <td colspan="6" align="center" style="font-size:18px"><strong><u>Service  Fabric Delivery Challan</strong></td>
            <td></td>
        </tr>
        <tr>
            <td colspan="8">&nbsp;</td>
        </tr>
        <tr>
            <td><strong>Service Company</strong></td>
            <td width="10">:</td>
            <td width="150px" colspan="3"><? echo $service_company; ?></td>
            <td width="100"><strong>Issue Date</strong></td>
            <td width="10">:</td>
            <td><? echo change_date_format($issue_date); ?></td>
        </tr>
        <tr>
            <td><strong>Address</strong></td>
            <td>:</td>
            <td><?php echo $service_company_address; ?></td>
            <td>&nbsp;</td>
            <td>&nbsp;</td>
            <td><strong>Vechical No</strong></td>
            <td>:</td>
            <td></td>
        </tr>
        <tr>
            <td><strong>Receiver Name</strong></td>
            <td>:</td>
            <td></td>
            <td>&nbsp;</td>
            <td>&nbsp;</td>
            <td></td>
            <td></td>
            <td></td>
        </tr>
    </table>
    
    <table cellspacing="0" cellpadding="0" border="1" rules="all" width="1220" class="rpt_table">
        <thead bgcolor="#dddddd">
            <th width="40">SL</th>
            <th width="200">Challan No</th>
            <th width="200">Booking No</th>
            <th width="200">Process</th>
            <th width="150">Buyer</th>
            <th width="100">File No</th>
            <th width="100">Ref. No</th>
            <th width="120">Body Part</th>
            <th width="100">Batch No</th>
            <th width="100">Color</th>
            <th width="200">Fabrication</th>
            <th width="60">Dia</th>
            <th width="60">GSM</th>
            <th width="100">No Of Roll</th>
            <th>Wgt</th>
        </thead>
        <tbody>
        <?php
		$sl = 0;
		$total_roll = 0; 
		$total_wgt = 0; 
		foreach($rptArr as $challanNo=>$challanNoArr)
		{
			foreach($challanNoArr as $bodyPart=>$bodyPartArr)
			{
				foreach($bodyPartArr as $batchNo=>$batchNoArr)
				{
					foreach($batchNoArr as $fabric=>$fabricArr)
					{
						foreach($fabricArr as $dia=>$diaArr)
						{
							foreach($diaArr as $gsm=>$row)
							{
								$sl++;
								//tmp solution
								$yarnCountDeterminArray = get_constructionComposition($fabric);
								?>
                                <tr>
                                	<td align="center"><?php echo $sl; ?></td>
                                	<td align="center"><?php echo $challanNo; ?></td>
                                	<td align="center"><?php echo $row['booking_no']; ?></td>
                                	<td><?php echo $conversion_cost_head_array[$row['process_id']]; ?></td>
                                	<td><?php echo $row['buyer']; ?></td>
                                	<td><?php echo $row['file_no']; ?></td>
                                	<td><?php echo $row['ref_no']; ?></td>
                                	<td><?php echo $body_part[$bodyPart]; ?></td>
                                	<td><?php echo $batchNo; ?></td>
                                	<td><?php echo $row['color']; ?></td>
                                	<td><?php echo $yarnCountDeterminArray[$fabric]; ?></td>
                                	<td align="center"><?php echo $dia; ?></td>
                                	<td align="center"><?php echo $gsm; ?></td>
                                	<td align="center"><?php echo $row['no_of_roll']; ?></td>
                                	<td align="right"><?php echo number_format($row['roll_wgt'], 2); ?></td>
                                </tr>
                                <?php
								$total_roll += $row['no_of_roll'];
								$total_wgt += $row['roll_wgt'];							
							}
						}
					}
				}
			}
		}
		?>
        </tbody>
        <tfoot>
        	<tr>
            	<th colspan="13" align="right">Total&nbsp;</th>
                <th align="center"><?php echo number_format($total_roll); ?></th>
                <th align="center"><?php echo number_format($total_wgt, 2); ?></th>
            </tr>
        </tfoot>
	</table>
	<?php	
    exit();
}

/*
|--------------------------------------------------------------------------
| for print button-2
|--------------------------------------------------------------------------
|
*/
if($action=="action_print_2")
{
	$expData = explode('*',$data);
    $company_id = $expData[0]; 
    $id = implode(',',explode("_",$expData[1]));
	
	$sqlBatchBarcode="SELECT r.barcode_no FROM pro_roll_details r WHERE r.entry_form = 63 AND r.roll_no>0 AND r.status_active = 1 AND r.is_deleted = 0 AND r.mst_id IN( ".$id.")";
	//echo $sqlBatchBarcode; die;
	
	$sql=" SELECT a.id AS ID, a.entry_form AS ENTRY_FORM, a.company_id AS COMPANY_ID, a.receive_basis AS RECEIVE_BASIS, a.booking_no AS RECEIVE_BASIS, a.booking_id AS BOOKING_ID, a.knitting_source AS KNITTING_SOURCE, a.knitting_company AS KNITTING_COMPANY, b.id as DTLS_ID, b.prod_id AS PROD_ID, b.body_part_id AS BODY_PART_ID, b.febric_description_id AS FEBRIC_DESCRIPTION_ID, b.gsm AS GSM, b.width AS WIDTH, b.color_id AS COLOR_ID, c.mst_id AS MST_ID, c.barcode_no AS BARCODE_NO, c.id as ROLL_ID, c.roll_no AS ROLL_NO, c.po_breakdown_id AS PO_BREAKDOWN_ID, c.qnty AS QNTY, c.qc_pass_qnty_pcs AS QC_PASS_QNTY_PCS FROM inv_receive_master a INNER JOIN pro_grey_prod_entry_dtls b ON a.id = b.mst_id INNER JOIN pro_roll_details c ON b.id = c.dtls_id WHERE a.receive_basis<>9 AND a.entry_form IN(2,22) AND c.entry_form IN(2,22) AND c.roll_no>0 AND c.status_active = 1 AND c.is_deleted = 0 AND c.barcode_no IN(".$sqlBatchBarcode.")";
	$sqlRslt=sql_select($sql);
	$barCode=array();
	$poBreakdownId=array();
	$yarnCountDeterminId=array();
	$bookingId=array();
	$dataArr = array();
	foreach($sqlRslt as $row)
	{
		$barCode[$row['BARCODE_NO']]=$row['BARCODE_NO'];
		$poBreakdownId[$row['PO_BREAKDOWN_ID']]=$row['PO_BREAKDOWN_ID'];
		$yarnCountDeterminId[$row['FEBRIC_DESCRIPTION_ID']]=$row['FEBRIC_DESCRIPTION_ID'];
		$bookingId[$row['BOOKING_ID']]=$row['BOOKING_ID'];
		
		$dataArr[$row['BARCODE_NO']]['body_part_id'] = $row['BODY_PART_ID'];
		$dataArr[$row['BARCODE_NO']]['febric_description_id'] = $row['FEBRIC_DESCRIPTION_ID'];
		$dataArr[$row['BARCODE_NO']]['width'] = $row['WIDTH'];
		$dataArr[$row['BARCODE_NO']]['gsm'] = $row['GSM'];
	}
	//echo "<pre>";
	//print_r($barCode); die;

	$sql2 = "SELECT a.wo_no AS WO_NO, a.recv_number AS RECV_NUMBER, a.dyeing_source AS DYEING_SOURCE, a.dyeing_company AS DYEING_COMPANY, a.receive_date AS RECEIVE_DATE, b.roll_wgt AS ROLL_WGT, b.roll_id AS ROLL_ID, b.order_id AS ORDER_ID, b.color_id AS COLOR_ID, b.batch_id AS BATCH_ID, b.process_id AS PROCESS_ID, c.barcode_no AS BARCODE_NO FROM inv_receive_mas_batchroll a INNER JOIN pro_grey_batch_dtls b ON a.id = b.mst_id INNER JOIN pro_roll_details c ON a.id = c.mst_id AND b.id = c.dtls_id WHERE a.id IN(".$id.") AND a.company_id = ".$company_id." AND a.entry_form = 63 AND c.entry_form = 63 AND a.status_active = 1 AND a.is_deleted = 0 AND b.status_active = 1 AND b.is_deleted = 0 AND c.status_active = 1 AND c.is_deleted = 0 ORDER BY a.id"; 
	//echo $sql2;
	$sql2Rslt = sql_select($sql2);
	$colorIdArr = array();
	$companySupplierArr = array();
	foreach($sql2Rslt as $row)
	{
		//for company supplier
		$companySupplierArr[$row['DYEING_COMPANY']] = $row['DYEING_COMPANY'];
		//for color id
		$expColor = explode(',',$row['COLOR_ID']);
		foreach($expColor as $key=>$val)
		{
			$colorIdArr[$val] = $val;
		}
	}
	
	$color_arr = return_library_array("select id, color_name from lib_color where id in(".implode(',',$colorIdArr).")","id","color_name");
	$company_name_array = return_library_array("select id,company_name from lib_company where id in(".implode(',',$companySupplierArr).")", "id", "company_name");
	$supplier_arr=return_library_array( "select id, supplier_name from lib_supplier where id in(".implode(',',$companySupplierArr).")",'id','supplier_name');
	$supplier_address_arr=return_library_array( "select id, address_1 from lib_supplier where id in(".implode(',',$companySupplierArr).")",'id','address_1');
	$buyer_name_array=return_library_array("select id, short_name from lib_buyer", "id", "short_name");

	//for batch
	$batchArray = get_batchFor_GreyRollIssueToProcess($barCode);
	//echo "<pre>";
	//print_r($batchArray); die;
	
	//for Yarn Count Determin
	//$yarnCountDeterminArray = get_constructionComposition($yarnCountDeterminId);
	//echo "<pre>";
	//print_r($yarnCountDeterminArray);
	
	//for buyer
	$poArray = get_buyerFor_GreyRollIssueToProcess($poBreakdownId);
	//echo "<pre>";
	//print_r($poArray); die;
	
	//for dia type
	//$diaTypeArray = get_dia_type($bookingId);
	
	$rptArr = array();
	$noOfRoll = 0;
	$service_company = '';
	$issue_date = '';
	foreach($sql2Rslt as $row)
	{
		$service_company = get_knitting_company_details($row['DYEING_SOURCE'], $row['DYEING_COMPANY']);
		$service_company_address = $supplier_address_arr[$row['DYEING_COMPANY']];
		$issue_date = $row['RECEIVE_DATE'];
		
		$body_part_id = $dataArr[$row['BARCODE_NO']]['body_part_id'];
		$batch_no = $batchArray[$row['BARCODE_NO']][$row['ORDER_ID']]['batch_no'];
		$febric_description_id = $dataArr[$row['BARCODE_NO']]['febric_description_id'];
		$dia = $dataArr[$row['BARCODE_NO']]['width'];
		$gsm = $dataArr[$row['BARCODE_NO']]['gsm'];

		$rptArr[$row['RECV_NUMBER']][$body_part_id][$batch_no][$febric_description_id][$dia][$gsm][$row['BARCODE_NO']]['booking_no'] = $row['WO_NO'];
		$rptArr[$row['RECV_NUMBER']][$body_part_id][$batch_no][$febric_description_id][$dia][$gsm][$row['BARCODE_NO']]['process_id'] = $row['PROCESS_ID'];
		$rptArr[$row['RECV_NUMBER']][$body_part_id][$batch_no][$febric_description_id][$dia][$gsm][$row['BARCODE_NO']]['buyer'] = $poArray[$row['ORDER_ID']]['buyer_name'];
		$rptArr[$row['RECV_NUMBER']][$body_part_id][$batch_no][$febric_description_id][$dia][$gsm][$row['BARCODE_NO']]['file_no'] = $poArray[$row['ORDER_ID']]['file_no'];
		$rptArr[$row['RECV_NUMBER']][$body_part_id][$batch_no][$febric_description_id][$dia][$gsm][$row['BARCODE_NO']]['ref_no'] = $poArray[$row['ORDER_ID']]['ref_no'];
		$rptArr[$row['RECV_NUMBER']][$body_part_id][$batch_no][$febric_description_id][$dia][$gsm][$row['BARCODE_NO']]['color'] = get_color_details($row['COLOR_ID']);
		$rptArr[$row['RECV_NUMBER']][$body_part_id][$batch_no][$febric_description_id][$dia][$gsm][$row['BARCODE_NO']]['roll_wgt'] += $row['ROLL_WGT'];
	}
	
	//for company details
	$company_array=array();
	$company_data=sql_select("SELECT id AS ID, company_name AS COMPANY_NAME, company_short_name AS COMPANY_SHORT_NAME, plot_no AS PLOT_NO, level_no AS LEVEL_NO, road_no AS ROAD_NO, block_no AS BLOCK_NO, country_id AS COUNTRY_ID, province AS PROVINCE, city AS CITY, zip_code AS ZIP_CODE, email AS EMAIL, website AS WEBSITE FROM lib_company WHERE id=".$company_id."");
	foreach($company_data as $row)
	{
		$company_array['name']=$row['COMPANY_NAME'];
		$company_array['shortname']=$row['COMPANY_SHORT_NAME'];
		$company_array['plot_no']=$row['PLOT_NO'];
		$company_array['level_no']=$row['LEVEL_NO'];
		$company_array['road_no']=$row['ROAD_NO'];
		$company_array['block_no']=$row['BLOCK_NO'];
		$company_array['city']=$row['CITY'];
		$company_array['zip_code']=$row['ZIP_CODE'];
		$company_array['province']=$row['PROVINCE'];
		$company_array['country_id']=$row['COUNTRY_ID'];
		$company_array['email']=$row['EMAIL'];
		$company_array['website']=$row['WEBSITE'];
	}

	//for company logo
	$image_location=return_field_value("image_location","common_photo_library","file_type=1 and form_name='company_details' and master_tble_id='".$expData[0]."'","image_location");
	?>
    <table width="1220" cellspacing="0">
        <tr>
            <td width="200" rowspan="3">
                <img src="../../<? echo $image_location; ?>" height="70" width="200" />
            </td>
            <td colspan="6" align="center" style="font-size:22px">
                <strong><? echo $company_array['name']; ?></strong>
            </td>
            <td width="200"></td>
        </tr>
        <tr class="form_caption">
            <td colspan="6" align="center" style="font-size:14px">  
                Plot No: <? echo $company_array['plot_no']; ?> 
                Level No: <? echo $company_array['level_no']?>
                Road No: <? echo $company_array['road_no']; ?> 
                Block No: <? echo $company_array['block_no'];?> 
                City No: <? echo $company_array['city'];?> 
                Zip Code: <? echo $company_array['zip_code']; ?><br> 
                Province No: <?php echo $company_array['province'];?> 
                Country: <? echo $country_arr[$company_array['country_id']]; ?> 
                Email Address: <? echo $company_array['email'];?> 
                Website No: <? echo $company_array['website'];?>
            </td>
            <td></td>  
        </tr>
        <tr>
            <td colspan="6" align="center" style="font-size:18px"><strong><u>Service  Fabric Delivery Challan</strong></td>
            <td></td>
        </tr>
        <tr>
            <td colspan="8">&nbsp;</td>
        </tr>
        <tr>
            <td><strong>Service Company</strong></td>
            <td width="10">:</td>
            <td width="150px" colspan="3"><? echo $service_company; ?></td>
            <td width="100"><strong>Issue Date</strong></td>
            <td width="10">:</td>
            <td><? echo change_date_format($issue_date); ?></td>
        </tr>
        <tr>
            <td><strong>Address</strong></td>
            <td>:</td>
            <td><?php echo $service_company_address; ?></td>
            <td>&nbsp;</td>
            <td>&nbsp;</td>
            <td><strong>Vechical No</strong></td>
            <td>:</td>
            <td></td>
        </tr>
        <tr>
            <td><strong>Receiver Name</strong></td>
            <td>:</td>
            <td></td>
            <td>&nbsp;</td>
            <td>&nbsp;</td>
            <td></td>
            <td></td>
            <td></td>
        </tr>
    </table>
    
    <table cellspacing="0" cellpadding="0" border="1" rules="all" width="1220" class="rpt_table">
        <thead bgcolor="#dddddd">
            <th width="40">SL</th>
            <th width="200">Challan No</th>
            <th width="200">Booking No</th>
            <th width="200">Process</th>
            <th width="150">Buyer</th>
            <th width="100">File</th>
            <th width="100">Ref. No</th>
            <th width="120">Body Part</th>
            <th width="100">Batch No</th>
            <th width="100">Color</th>
            <th width="200">Fabrication</th>
            <th width="60">Dia</th>
            <th width="60">GSM</th>
            <th width="100">Barcode No</th>
            <th>Wgt</th>
        </thead>
        <tbody>
        <?php
		$sl = 0;
		$total_wgt = 0; 
		foreach($rptArr as $challanNo=>$challanNoArr)
		{
			foreach($challanNoArr as $bodyPart=>$bodyPartArr)
			{
				foreach($bodyPartArr as $batchNo=>$batchNoArr)
				{
					foreach($batchNoArr as $fabric=>$fabricArr)
					{
						foreach($fabricArr as $dia=>$diaArr)
						{
							foreach($diaArr as $gsm=>$gsmArr)
							{
								foreach($gsmArr as $barcode=>$row)
								{
									$sl++;
									//tmp solution
									$yarnCountDeterminArray = get_constructionComposition($fabric);
									?>
									<tr>
										<td align="center"><?php echo $sl; ?></td>
										<td align="center"><?php echo $challanNo; ?></td>
										<td align="center"><?php echo $row['booking_no']; ?></td>
										<td><?php echo $conversion_cost_head_array[$row['process_id']]; ?></td>
										<td><?php echo $row['buyer']; ?></td>
										<td><?php echo $row['file_no']; ?></td>
										<td><?php echo $row['ref_no']; ?></td>
										<td><?php echo $body_part[$bodyPart]; ?></td>
										<td><?php echo $batchNo; ?></td>
										<td><?php echo $row['color']; ?></td>
										<td><?php echo $yarnCountDeterminArray[$fabric]; ?></td>
										<td align="center"><?php echo $dia; ?></td>
										<td align="center"><?php echo $gsm; ?></td>
										<td align="center"><?php echo $barcode; ?></td>
										<td align="right"><?php echo number_format($row['roll_wgt'], 2); ?></td>
									</tr>
									<?php
									$total_wgt += $row['roll_wgt'];							
								}
							}
						}
					}
				}
			}
		}
		?>
        </tbody>
        <tfoot>
        	<tr>
            	<th colspan="14" align="right">Total&nbsp;</th>
                <th align="center"><?php echo number_format($total_wgt, 2); ?></th>
            </tr>
        </tfoot>
	</table>
	<?php	
    exit();
}

//all function
//batch
function get_batchFor_GreyRollIssueToProcess($barCode)
{
	$data=array();
	$sqlBatch=sql_select("SELECT a.id AS ID, a.batch_no AS BATCH_NO, a.color_id AS COLOR_ID, b.po_id AS PO_ID, b.barcode_no AS BARCODE_NO  
	FROM pro_batch_create_mst a 
	INNER JOIN pro_batch_create_dtls b ON a.id = b.mst_id
	WHERE a.status_active=1 
	AND a.is_deleted = 0 
	AND b.barcode_no IN(".implode(",",$barCode).")");

	foreach($sqlBatch as $row)
	{
		$data[$row['BARCODE_NO']][$row['PO_ID']]['batch_id']=$row['ID'];
		$data[$row['BARCODE_NO']][$row['PO_ID']]['batch_no']=$row['BATCH_NO'];	
	}
	
	return $data;
}

//Yarn Count Determin
function get_constructionComposition($yarnCountDeterminId)
{
	$i = 0;
	$id = '';
	$data = array();
	$construction = '';
	$composition_name = '';
	/*
	$sqlYarnCount = sql_select("SELECT a.id AS ID, a.construction AS CONSTRUCTION, b.percent AS PERCENT, c.composition_name AS COMPOSITION_NAME 
	FROM lib_yarn_count_determina_mst a 
	INNER JOIN lib_yarn_count_determina_dtls b ON a.id = b.mst_id
	INNER JOIN lib_composition_array c ON b.copmposition_id = c.id 
	WHERE a.id IN(".implode(",",$yarnCountDeterminId).")");
	*/
	$sqlYarnCount = sql_select("SELECT a.id AS ID, a.construction AS CONSTRUCTION, b.percent AS PERCENT, c.composition_name AS COMPOSITION_NAME 
	FROM lib_yarn_count_determina_mst a 
	INNER JOIN lib_yarn_count_determina_dtls b ON a.id = b.mst_id
	INNER JOIN lib_composition_array c ON b.copmposition_id = c.id 
	WHERE a.id IN(".$yarnCountDeterminId.")");
	foreach( $sqlYarnCount as $row )
	{
		$id=$row['ID'];
		if($i==0)
		{
			$construction.= $row['CONSTRUCTION'].", ";
			$i++;
		}
		
		if($composition_name != '')
		{
			$composition_name .= ', ';
		}
		$composition_name .= $row['COMPOSITION_NAME']." ".$row['PERCENT']."%";
	}
	$data[$id] = $construction.$composition_name;
	return $data;
}

//buyer
function get_buyerFor_GreyRollIssueToProcess($poBreakdownId)
{
	global $buyer_name_array;
	$data=array();
	$sqlPo=sql_select("SELECT a.job_no AS JOB_NO, a.job_no_prefix_num AS JOB_NO_PREFIX_NUM, a.buyer_name AS BUYER_NAME, a.style_ref_no AS STYLE_REF_NO, a.insert_date AS INSERT_DATE, b.po_number AS PO_NUMBER, b.id AS ID, b.file_no AS FILE_NO, b.grouping AS REF_NO 
	FROM wo_po_details_master a 
	INNER JOIN wo_po_break_down b ON a.job_no = b.job_no_mst
	WHERE b.id IN(".implode(",",$poBreakdownId).")");
	foreach($sqlPo as $row)
	{
		$data[$row['ID']]['job_no']=$row['JOB_NO'];
		$data[$row['ID']]['buyer_name']=$buyer_name_array[$row['BUYER_NAME']];
		$data[$row['ID']]['style_ref_no']=$row['STYLE_REF_NO'];
		$data[$row['ID']]['year']=date('Y',strtotime($row['INSERT_DATE']));
		$data[$row['ID']]['po_number']=$row['PO_NUMBER'];
		$data[$row['ID']]['file_no']=$row['FILE_NO'];
		$data[$row['ID']]['ref_no']=$row['REF_NO'];
	}
	return $data;
}

function get_color_details($colorId)
{
	global $color_arr;
	$colorName='';
	$expColorId=explode(",",$colorId);
	foreach($expColorId as $id)
	{
		if($id>0)
			$colorName.=$color_arr[$id].", ";
	}
	$colorName=chop($colorName,', ');
	return $colorName;
}

//knitting_company
function get_knitting_company_details($knittingSource, $knittingCompany)
{ 
	global $company_name_array;
	global $supplier_arr;
	$data='';
	if($knittingSource == 1)
	{
		$data=$company_name_array[$knittingCompany];
	}
	else if($knittingSource == 3 )
	{
		$data=$supplier_arr[$knittingCompany];
	}
	return $data;
}

//receive_basis
function get_receive_basis($entryForm, $receiveBasis)
{
	$data=array();
	if(($entryForm==2 && $receiveBasis==0) || ($entryForm==22 && ($receiveBasis==4 || $receiveBasis==6)))
	{
		$data['id']=0;
		$data['dtls']='Independent';
	}
	else if(($entryForm==2 && $receiveBasis==1) || ($entryForm==22 && $receiveBasis==2)) 
	{
		$data['id']=2;
		$data['dtls']="Booking";
	}
	else if($entryForm==2 && $receiveBasis==2) 
	{
		$data['id']=3;
		$data['dtls']="Knitting Plan";
	}
	else if($entryForm==22 && $receiveBasis==1) 
	{
		$data['id']=1;
		$data['dtls']="PI";
	}
	return $data;
}

//dia type
function get_dia_type($bookingId)
{
	$sqlDiaType="SELECT id AS ID, width_dia_type AS WIDTH_DIA_TYPE 
		FROM ppl_planning_info_entry_dtls 
		WHERE id IN(".implode(",",$bookingId).")";
	$resultdiaType=sql_select($sqlDiaType);
	$data_diaType = array();
	foreach($resultdiaType as $row)
	{
		$data_diaType[$row['ID']]=$row['WIDTH_DIA_TYPE'];
	}
	return $data_diaType;
}
?>