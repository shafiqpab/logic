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

if($action=="booking_popup")
{
	extract($_REQUEST);
	echo load_html_head_contents("Booking Info","../../", 1, 1, $unicode);
	?>
	<script>
		$(document).ready(function(e) {
            $("#txt_search_common").focus();
        });
		
		function js_set_value( mst_id )
		{
			document.getElementById('update_id').value=mst_id;
			parent.emailwindow.hide();
		}
    </script>
     
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
                            <th width="80">Booking No</th> 
                            <!-- <th width="80">Requisition No</th>               	  -->
                            <!-- <th width="100">Style ID</th> -->
                            <!-- <th width="100">Style Name</th> -->
                            <th width="130" colspan="2">Booking Date</th>
                            <th><input type="reset" name="reset" id="reset" class="formbutton" value="Reset" style="width:70px;" /></th>
                        </tr>
                    </thead>
                    <tr class="general">
                        <td> 
                            <input type="hidden" id="update_id">
                            <? echo create_drop_down( "cbo_company_mst", 140, "select id,company_name from lib_company comp where status_active =1 and is_deleted=0 $company_cond order by company_name","id,company_name",1, "-- Select Company --", $company_id,"load_drop_down( 'sample_data_archive_entry_controller', this.value, 'load_drop_down_buyer_checklist', 'buyer_td_checklist' );" ); ?> </td>
                        <td id="buyer_td_checklist"><?  echo create_drop_down( "cbo_buyer_name", 140, $blank_array,'', 1, "-- Select Buyer --" );?></td>
                        <td><input type="text" style="width:140px" class="text_boxes" name="txt_booking_num" id="txt_booking_num"  /></td>
                        <!-- <td><input type="text" style="width:70px" class="text_boxes" name="txt_requisition_num" id="txt_requisition_num"  /></td> -->
                        <!-- <td><input type="text" style="width:90px" class="text_boxes"  name="txt_style_id" id="txt_style_id" /></td> -->
                        <!-- <td><input type="text" style="width:90px" class="text_boxes"  name="txt_style_name1" id="txt_style_name1"  /></td> -->
                        <td><input name="txt_date_from" id="txt_date_from" class="datepicker" style="width:110px" placeholder="From"></td>
                        <td><input name="txt_date_to" id="txt_date_to" class="datepicker" style="width:110px" placeholder="To"></td> 
                        <td>
                        <input type="button" name="btn_show" id="btn_show" class="formbutton" value="Show" onClick="show_list_view ( document.getElementById('cbo_search_category').value+'_'+document.getElementById('cbo_company_mst').value+'_'+document.getElementById('cbo_buyer_name').value+'_'+document.getElementById('txt_date_from').value+'_'+document.getElementById('txt_date_to').value+'_'+document.getElementById('txt_booking_num').value+'_'+document.getElementById('cbo_year_selection').value, 'create_booking_id_search_list_view', 'search_div', 'sample_data_archive_entry_controller', 'setFilterGrid(\'list_view\',-1)')" style="width:70px;" />
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
    <script> load_drop_down( 'sample_data_archive_entry_controller', <?=$company_id;?>, 'load_drop_down_buyer_checklist', 'buyer_td_checklist' );</script>
    </html>
    <?
    exit();
}
if($action=="create_booking_id_search_list_view")
{
	$data=explode('_',$data);
    //print_r($data);die;
	if ($data[1]!=0) $company=" and b.company_id='$data[1]'"; else { echo "Please Select Company First."; die; }
	if ($data[2]!=0) $buyer=" and b.buyer_name='$data[2]'"; else $buyer="";//{ echo "Please Select Buyer First."; die; }
	// if($data[0]==1)
	// {
    //     if ($data[5]!=0) $book_cond=" and id in(SELECT b.style_id from wo_non_ord_samp_booking_mst a,wo_non_ord_samp_booking_dtls b where a.booking_no=b.booking_no and a.status_active=1 and b.status_active=1 and a.booking_no_prefix_num='$data[5]' and a.entry_form_id=140) "; else  $book_cond="";
	// }
	// else if($data[0]==4 || $data[0]==0)
	// {
    //     if ($data[5]!=0) $book_cond=" and id in(SELECT b.style_id from wo_non_ord_samp_booking_mst a,wo_non_ord_samp_booking_dtls b where a.booking_no=b.booking_no and a.status_active=1 and b.status_active=1 and a.booking_no_prefix_num like '%$data[5]%' and a.entry_form_id=140) "; else  $book_cond="";
	// }
	// else if($data[0]==2)
	// {
    //     if ($data[5]!=0) $book_cond=" and id in(SELECT b.style_id from wo_non_ord_samp_booking_mst a,wo_non_ord_samp_booking_dtls b where a.booking_no=b.booking_no and a.status_active=1 and b.status_active=1 and a.booking_no_prefix_num like '%$data[5]%' and a.entry_form_id=140) "; else  $book_cond="";

	// }
	// else if($data[0]==3)
	// {
    //     if ($data[5]!=0) $book_cond=" and id in(SELECT b.style_id from wo_non_ord_samp_booking_mst a,wo_non_ord_samp_booking_dtls b where a.booking_no=b.booking_no and a.status_active=1 and b.status_active=1 and a.booking_no_prefix_num like '%$data[5]%' and a.entry_form_id=140) "; else  $book_cond="";

	// }
	
	$year_cond="";
	if ($data[3]!="" &&  $data[4]!="") $estimated_shipdate  = "and a.BOOKING_DATE  between '".change_date_format($data[4], "yyyy-mm-dd", "-",1)."' and '".change_date_format($data[3], "yyyy-mm-dd", "-",1)."'"; else $estimated_shipdate ="";
	if(str_replace("'", "", $data[3])) $year_cond=" and  to_char(a.insert_date,'YYYY')=$data[6]";
		$yearCond="to_char(a.insert_date,'YYYY')";
	if ($data[5]!="") $booking_no_cond=" and a.booking_no_prefix_num like '%$data[5]%' "; else $booking_no_cond="";
	
	
    $buyer_arr=return_library_array( "select id, buyer_name from lib_buyer",'id','buyer_name');
	$comp=return_library_array( "select id, company_name from lib_company",'id','company_name');
	$dealing_marchant=return_library_array( "select id, team_member_name from lib_mkt_team_member_info",'id','team_member_name');
	$team_leader=return_library_array( "select id, team_leader_name from lib_marketing_team",'id','team_leader_name');
	$gmts=return_library_array( "select id, item_name from lib_garment_item",'id','item_name');
	$sample_type_arr=return_library_array( "select id, sample_type from wo_non_ord_samp_booking_dtls",'id','sample_type');
	$arr=array (2=>$buyer_arr,4=>$sample_type);

     $sql= "SELECT a.id, a.booking_no_prefix_num, $yearCond as year, a.booking_no, b.company_id, b.buyer_name, b.style_ref_no, c.sample_type FROM sample_development_mst b, wo_non_ord_samp_booking_mst a, wo_non_ord_samp_booking_dtls c WHERE a.entry_form_id = 140 AND a.status_active = 1 AND a.booking_no=c.booking_no and b.id=c.style_id AND a.is_deleted = 0 $company $buyer  $booking_no_cond $year_cond group by a.id, a.booking_no_prefix_num, a.insert_date, a.booking_no, b.company_id, b.buyer_name, b.style_ref_no, c.sample_type ORDER BY a.booking_no DESC";
	 
	echo  create_list_view("list_view", "Year,Booking No,Buyer Name,Style/Article,Sample Type", "80,100,100,100,100","550","240",0, $sql , "js_set_value", "id,booking_no,style_ref_no", "", 1, "0,0,buyer_name,0,sample_type", $arr , "year,booking_no,buyer_name,style_ref_no,sample_type", "",'','0,0,0,0,0') ;

	exit();
}
if($action=="populate_data_from_booking_search_popup")
{  
 	
	//req_id,company_id,booking_no,booking_id, booking_sl_no,fabric_color_id,color_type_id,fabric_desc,fin_dia_type,fin_gsm,deter_id
	$data=explode("_",$data);
	$booking_id=$data[0];
    $res_basic = sql_select("select a.id,a.req_id,a.company_id,a.booking_no,b.booking_id,a.booking_sl_no,a.color_type_id,a.color_type_id,a.fabric_desc from sample_archive_basic_info a,sample_development_mst b where a.booking_id=$booking_id and a.is_deleted=0 and a.status_active=1 and a.req_id=b.id");
	
    $color_arr=return_library_array( "select id, color_name from lib_color",'id','color_name');
    $res_mst = sql_select("SELECT b.id as req_id,a.id,b.company_id, a.booking_no_prefix_num, a.booking_no, b.company_id, b.buyer_name, b.style_ref_no, c.color_type_id,c.sample_type,c.fabric_color,c.lib_yarn_count_deter_id as deter_id,c.fabric_description,c.composition,c.gsm_weight,c.dia_width,c.dtls_id,c.dia FROM sample_development_mst b, wo_non_ord_samp_booking_mst a, wo_non_ord_samp_booking_dtls c WHERE a.id=$booking_id and a.entry_form_id = 140 AND a.status_active = 1 AND a.booking_no=c.booking_no and b.id=c.style_id AND a.is_deleted = 0");

	 
    
 	 
  	foreach($res_mst as $result)
	{ 
 		//echo "load_drop_down( 'requires/sample_data_archive_entry_controller', '".$result[csf('id')]."', 'load_drop_down_gmts_ch', 'gmts_td' );\n";
 		
 		 $booking_id=$result[csf('id')];
		 $dtls_idArr[$result[csf('dtls_id')]]=$result[csf('dtls_id')];
		 $req_id=$result[csf('req_id')];
		 $booking_no=$result[csf('booking_no')];
		 $company_id=$result[csf('company_id')];
         $style_ref_no=$result[csf('style_ref_no')];
         $buyer_name=$buyer_arr[$result[csf('buyer_name')]];
         $fabric_colorArr[$result[csf('fabric_color')]]=$color_arr[$result[csf('fabric_color')]];
         if($result[csf('sample_type')])
         {
            $sample_typeArr[$result[csf('sample_type')]]=$sample_type[$result[csf('sample_type')]];
         }
         $fabricDescArr[$result[csf('deter_id')]]=$result[csf('fabric_description')];
         if($result[csf('color_type_id')])
         {
          $colorTypeArr[$color_type[$result[csf('color_type_id')]]]=$color_type[$result[csf('color_type_id')]];
		   $colorTypeIdArr[$result[csf('color_type_id')]]=$result[csf('color_type_id')];
         }
         if($result[csf('gsm_weight')])
         {
          $GSMTypeArr[$result[csf('gsm_weight')]]=$result[csf('gsm_weight')];
         }
         if($result[csf('dia_width')])
         {
          $DIAArr[$result[csf('dia_width')]]=$result[csf('dia_width')];
         }
         if($result[csf('dia')])
         {
          $DIA_val_Arr[$result[csf('dia')]]=$result[csf('dia')];
         }     
    }
	 
	$sql_fab=sql_select("select width_dia_id,dia from sample_development_fabric_acc where   status_active=1 and is_deleted=0 and width_dia_id>0  and id in(".implode(",",$dtls_idArr).")");
	foreach($sql_fab as $row)
	{
		$dia_typeArr[]=$fabric_typee[$row[csf('width_dia_id')]];
        $dia_Arr[]=$row[csf('dia')];
	}
	
   // print_r($colorTypeArr);die;
   $fab_color_drop=create_drop_down( "cbo_fab_color_code", 150, $fabric_colorArr,"", 1, "-- select --" );
   $fab_desc_drop=create_drop_down( "cbo_fabrication", 150, $fabricDescArr,"", 1, "-- select --","","fnc_color_type_load(this.value,1)" );
    
        echo "$('#txt_booking_no').val('".$booking_no."');\n";
	    echo "$('#txt_booking_id').val('".$booking_id."');\n";
	    echo "$('#company_id').val('".$company_id."');\n";
	    echo "$('#req_id').val('".$req_id."');\n";
 		echo "$('#txt_style_ref').val('".$style_ref_no."');\n";
		echo "$('#txt_buyer_name').val('".$buyer_name."');\n";
        $sample_typeArr_res=implode(",",$sample_typeArr);
		echo "$('#txt_sample_type').val('". chop($sample_typeArr_res,",")."');\n";
        echo "$('#txt_color_type').val('".implode(",",$colorTypeArr)."');\n";
		//echo "$('#txt_color_id').val('".implode(",",$colorTypeIdArr)."');\n";
        echo "$('#finished_gsm').val('".implode(",",$GSMTypeArr)."');\n";
        echo "$('#finish_dia_type').val('".implode(",",array_unique($dia_typeArr))."');\n";
        echo "$('#finish_dia').val('".implode(",",array_unique($dia_Arr))."');\n";
        //basic_color_td
        //basic_color_td
		//echo "fnc_load_tr('".$result[csf('id')]."');\n";
        echo "document.getElementById('basic_color_td').innerHTML = '".$fab_color_drop."';\n";
        echo "document.getElementById('basic_fabric_td').innerHTML = '".$fab_desc_drop."';\n";
       
        

   	unlink($res_mst);
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
	

?>