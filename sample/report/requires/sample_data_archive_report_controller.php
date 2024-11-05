<?
header('Content-type:text/html; charset=utf-8');
session_start();
if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");
include('../../../includes/common.php');
$permission=$_SESSION['page_permission'];
$data=$_REQUEST['data'];
$action=$_REQUEST['action'];
$user_name=$_SESSION['logic_erp']['user_id'];
$company_arr=return_library_array( "select id, company_name from lib_company where status_active=1 and is_deleted=0",'id','company_name');
$color_arr=return_library_array( "select id, color_name from lib_color where status_active=1 and is_deleted=0","id","color_name");

if($action=="booking_popup")
{
	extract($_REQUEST);
	echo load_html_head_contents("Booking Info","../../../", 1, 1, $unicode);
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
            	<table width="500" cellspacing="0" cellpadding="0" border="1" rules="all" class="rpt_table" align="center">
                	<thead>
                        <tr>
                            <th colspan="8"><? echo create_drop_down( "cbo_search_category", 140, $string_search_type,'', 1, "-- Search Catagory --" ); ?></th>
                        </tr>
                        <tr>
                            <th width="140">Company Name</th>
                            <th width="140">Buyer Name</th> 
                            <th width="80">Booking No</th> 
                            <th width="130" colspan="2">Booking Date</th>
                            <th><input type="reset" name="reset" id="reset" class="formbutton" value="Reset" style="width:70px;" /></th>
                        </tr>
                    </thead>
                    <tr class="general">
                        <td> 
                            <input type="hidden" id="update_id">
                            <? echo create_drop_down( "cbo_company_mst", 140, "select id,company_name from lib_company comp where status_active =1 and is_deleted=0 $company_cond order by company_name","id,company_name",1, "-- Select Company --", $company_id,"load_drop_down( 'sample_data_archive_report_controller', this.value, 'load_drop_down_buyer_checklist', 'buyer_td_checklist' );" ); ?> </td>
                        <td id="buyer_td_checklist"><?  echo create_drop_down( "cbo_buyer_name", 140, $blank_array,'', 1, "-- Select Buyer --" );?></td>
                        <td><input type="text" style="width:140px" class="text_boxes" name="txt_booking_num" id="txt_booking_num"  /></td>
                        <td><input name="txt_date_from" id="txt_date_from" class="datepicker" style="width:110px" placeholder="From"></td>
                        <td><input name="txt_date_to" id="txt_date_to" class="datepicker" style="width:110px" placeholder="To"></td> 
                        <td>
                        <input type="button" name="btn_show" id="btn_show" class="formbutton" value="Show" onClick="show_list_view ( document.getElementById('cbo_search_category').value+'_'+document.getElementById('cbo_company_mst').value+'_'+document.getElementById('cbo_buyer_name').value+'_'+document.getElementById('txt_date_from').value+'_'+document.getElementById('txt_date_to').value+'_'+document.getElementById('txt_booking_num').value+'_'+document.getElementById('cbo_year_selection').value, 'create_booking_id_search_list_view', 'search_div', 'sample_data_archive_report_controller', 'setFilterGrid(\'list_view\',-1)')" style="width:70px;" />
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
    <script> load_drop_down( 'sample_data_archive_report_controller', <?=$company_id;?>, 'load_drop_down_buyer_checklist', 'buyer_td_checklist' );</script>
    </html>
    <?
    exit();
}
if($action=="create_booking_id_search_list_view")
{
	$data=explode('_',$data);
    //print_r($data);die;
	if ($data[1]!=0) $company=" and b.company_id='$data[1]'"; else { echo "Please Select Company First."; die; }
	if ($data[2]!=0) $buyer=" and b.buyer_name='$data[2]'"; else $buyer="";
	
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

    $req_id=$res_mst[0]['REQ_ID'];
    $color_type_query= sql_select("SELECT COLOR_TYPE_ID from sample_development_fabric_acc where SAMPLE_MST_ID=$req_id ORDER BY COLOR_TYPE_ID  asc");
    $color_array=array();
	     foreach ($color_type_query as $color)
         {
            $color_ids.=$color['COLOR_TYPE_ID'].',';
            $color_array[$color["COLOR_TYPE_ID"]]=$color_type[$color["COLOR_TYPE_ID"]];
         }
           $color_id=rtrim($color_ids,",");

        //  echo "<pre>";
        //  print_r($color_arr);
       
 	 
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
    
	
//    print_r($colorTypeArr);die;
   $fab_color_drop=create_drop_down( "cbo_fab_color_code", 150, $fabric_colorArr,"", 1, "-- select --" );
   $fab_desc_drop=create_drop_down( "cbo_fabrication", 150, $fabricDescArr,"", 1, "-- select --","","fnc_color_type_load(this.value,1)" );
    $fab_color_type_drop=create_drop_down( "txt_color_type", 150, $color_array,"", 1, "-- select --" );
    
        echo "$('#txt_booking_no').val('".$booking_no."');\n";
	    echo "$('#txt_booking_id').val('".$booking_id."');\n";
	    echo "$('#company_id').val('".$company_id."');\n";
	    echo "$('#req_id').val('".$req_id."');\n";
 		echo "$('#txt_style_ref').val('".$style_ref_no."');\n";
		echo "$('#txt_buyer_name').val('".$buyer_name."');\n";
        $sample_typeArr_res=implode(",",$sample_typeArr);
		echo "$('#txt_sample_type').val('". chop($sample_typeArr_res,",")."');\n";
        //echo "$('#txt_color_type').val('".implode(",",$colorTypeArr)."');\n";
		//echo "$('#txt_color_id').val('".implode(",",$colorTypeIdArr)."');\n";
        echo "$('#finished_gsm').val('".implode(",",$GSMTypeArr)."');\n";
        echo "$('#finish_dia_type').val('".implode(",",array_unique($dia_typeArr))."');\n";
        echo "$('#finish_dia').val('".implode(",",array_unique($dia_Arr))."');\n";
        //basic_color_td
        //basic_color_td
		//echo "fnc_load_tr('".$result[csf('id')]."');\n";
        echo "document.getElementById('basic_color_td').innerHTML = '".$fab_color_drop."';\n";
        echo "document.getElementById('basic_fabric_td').innerHTML = '".$fab_desc_drop."';\n";
        echo "document.getElementById('basic_color_type_td').innerHTML = '".$fab_color_type_drop."';\n";

       
        

   	unlink($res_mst);
 	exit();	
}
if ($action=="load_drop_down_buyer_checklist")
{
	echo create_drop_down( "cbo_buyer_name", 140, "select buy.id,buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company='$data' $buyer_cond  and buy.id in (select  buyer_id from  lib_buyer_party_type where party_type in (1,3,21,30,90)) order by buyer_name","id,buyer_name", 1, "-- Select Buyer --", $selected, "" ); 
	exit();
}
if($action="report_generate")
{
    $process = array( &$_POST );
    extract(check_magic_quote_gpc( $process ));
    // echo "<pre>";
    // print_r($process);
    $booking_no=str_replace("'", "", $txt_booking_no);
    $cbo_fab_color_code=str_replace("'", "", $cbo_fab_color_code);
    $cbo_fabrication=str_replace("'", "", $cbo_fabrication);
    $txt_color_type=str_replace("'", "", $txt_color_type);
   //Search Conditions 
   if($booking_no!="" ){
   if($booking_no=="") $booking_no_cond=""; else $booking_no_cond=" and a.booking_no='$booking_no' ";}
   if($cbo_fab_color_code>0 && $cbo_fab_color_code!=""){
   if($cbo_fab_color_code=="") $fab_color_code_cond=""; else $fab_color_code_cond=" and a.fabric_color_id=$cbo_fab_color_code ";}
   if( $cbo_fabrication>0 && $cbo_fabrication!=""){
   if($cbo_fabrication=="" ) $fabrication_cond=""; else $fabrication_cond=" and a.deter_id =$cbo_fabrication ";}
   //if($txt_color_type=="" && $txt_color_type==0) $color_type_cond=""; else $color_type_cond=" and a.color_type_id like '%$txt_color_type%' ";

   $color_arr=return_library_array( "select id, color_name from lib_color where status_active=1", "id", "color_name");
   $buyer_arr=return_library_array( "select id, buyer_name from lib_buyer",'id','buyer_name');
   $count_arr = return_library_array("select id, yarn_count from lib_yarn_count", 'id', 'yarn_count');
   $brand_arr = return_library_array("select id, brand_name from lib_brand", 'id', 'brand_name');


   
   // $main_querry=sql_select("SELECT a.id,a.req_id,a.company_id,a.booking_no,a.booking_id, a.booking_sl_no,a.fabric_color_id,a.color_type_id,a.fabric_desc,a.fin_dia_type,a.dia,a.fin_gsm,a.deter_id from sample_archive_basic_info a where a.is_deleted=0  and a.status_active=1 $booking_no_cond $fab_color_code_cond $fabrication_cond order by a.id asc")
    //Main Query for Search pannel
    $main_querry=sql_select("SELECT b.buyer_name, b.style_ref_no,c.sample_type,a.id,a.req_id,a.company_id,a.booking_no,a.booking_id, a.booking_sl_no,a.fabric_color_id,a.color_type_id,a.fabric_desc,a.fin_dia_type,a.dia,a.fin_gsm,a.deter_id from sample_archive_basic_info a, sample_development_mst b, wo_non_ord_samp_booking_dtls c, wo_non_ord_samp_booking_mst d where d.entry_form_id = 140 and d.booking_no=c.booking_no and b.id=c.style_id and a.is_deleted=0  and a.status_active=1 and d.status_active = 1 and d.is_deleted = 0 AND a.booking_no=c.booking_no AND a.booking_no=d.booking_no  $booking_no_cond $fab_color_code_cond $fabrication_cond group by b.buyer_name, b.style_ref_no,c.sample_type,a.id,a.req_id,a.company_id,a.booking_no,a.booking_id, a.booking_sl_no,a.fabric_color_id,a.color_type_id,a.fabric_desc,a.fin_dia_type,a.dia,a.fin_gsm,a.deter_id");



    foreach($main_querry as $val)
    {
        $cbo_company_name=$val[csf('company_id')];
        $dia=$val[csf('dia')];
        $booking_no=$val[csf('booking_no')];
        $fin_dia_type=$val[csf('fin_dia_type')];
        $booking_sl_no=$val[csf('booking_sl_no')];
        $color_type_id=$val[csf('color_type_id')];
        $fin_gsm=$val[csf('fin_gsm')];
        $fabric_color_id=$val[csf('fabric_color_id')];
        $fabric_desc=$val[csf('fabric_desc')];
        $buyer_name=$val[csf('buyer_name')];
        $style_ref_no=$val[csf('style_ref_no')];
        //$sample_type=$val[csf('sample_type')].",";

        
        if($val[csf('sample_type')])
        {
           $sample_typeArr[$val[csf('sample_type')]]=$sample_type[$val[csf('sample_type')]];
        }
    }
    ob_start();
    $company_library=return_library_array( "select id,company_name from lib_company", "id", "company_name"  );
	?>
	<script type="text/javascript">setFilterGrid('table_body',-1);</script>
	<div>
        <table border="5px" width="1000" cellpadding="0" cellspacing="0" >
        <tr border="1px">
            <table width="100%">
                <tr  class="form_caption" style="border:none;">
                    <td colspan="6" align="center" style="border:none; font-size:24px;">
                        <b><? echo $company_library[$cbo_company_name]; ?></b>
                    </td>
                </tr>
                <tr  class="form_caption" style="border:none;">
                    <td align="center" width="100%" colspan="6" style="font-size:20px"><strong><? echo 'R&D REPORT '; ?></strong></td>
                </tr>
            </table>
        </tr>
        <tr>
            <table cellspacing="0" cellpadding="0" border="1" class="rpt_table" width="1000" rules="all" >
               
                    <tr colspan ="6" bgcolor="#CCCCCC"> 
                        <th colspan ="6" style="height: 25px;" >Basic Information</th>
                    </tr>
	                <tr>
                        <td width="120" bgcolor="#cee3e3 "><strong>Buyer</strong></td><td width="120"><?= $buyer_arr[$buyer_name] ?></td>
	                    <td width="120" bgcolor="#cee3e3 "><strong>Style/Article</strong></td><td><?=$style_ref_no ?></td>
                        <td width="120" bgcolor="#cee3e3 "><strong>Finished Dia</strong></td><td><? echo $dia;?></td>
	                </tr>
                    <tr>
                        <td width="120" bgcolor="#cee3e3 "><strong>Booking No</strong></td><td width="120"><? echo  $booking_no;?></td>
	                    <td width="120" bgcolor="#cee3e3 "><strong>Sample Type</strong></td><td >
                            <? $sample_typeArr_res=implode(",",$sample_typeArr); echo $sample_typeArr_res ?>
                        </td>
                        <td width="120" bgcolor="#cee3e3 "><strong>Dia Type</strong></td><td><? echo  $fin_dia_type;?></td>
	                </tr>
                    <tr>
                        <td width="120" bgcolor="#cee3e3 "><strong>Booking SL No</strong></td><td width="120"><? echo  $booking_sl_no;?></td>
	                    <td width="120" bgcolor="#cee3e3 "><strong>Color Type</strong></td><td><? echo $color_type_id;?></td>
                        <td width="120" bgcolor="#cee3e3 "><strong>Finished GSm</strong></td><td><? echo $fin_gsm;?></td>
	                </tr>
                    <tr>
                        <td width="120" bgcolor="#cee3e3 "><strong>Color/Code</strong></td><td width="120"><? echo $color_arr[$fabric_color_id];?></td>
	                    <td width="120" bgcolor="#cee3e3 " ><strong>Fabrication</strong></td><td colspan="4"><? echo $fabric_desc;?></td>
	                </tr>
            </table>
        </tr>
            <?
        	$sql_yarn_query =sql_select("SELECT b.id, b.req_id, b.company_id, b.booking_no, b.booking_id, b.fabric_color_id, b.color_type_id, b.fabric_desc, b.deter_id, b.compositiom_1, b.compositiom_2, b.compositiom_3, b.compositiom_4, b.count_1, b.count_2, b.count_3, b.count_4, b.brand_1, b.brand_2, b.brand_3, b.brand_4, b.lot_1, b.lot_2, b.lot_3, b.lot_4, b.ratio_1, b.ratio_2, b.ratio_3, b.ratio_4, b.actual_count_1, b.actual_count_2, b.actual_count_3, b.actual_count_4 from sample_archive_basic_info a,sample_archive_yarn_info b where b.is_deleted = 0 and b.status_active = 1 and a.booking_id=b.booking_id and a.deter_id=b.deter_id $booking_no_cond $fab_color_code_cond $fabrication_cond order by b.id asc ");

            foreach($sql_yarn_query as $val)
            {
                $compositiom_1=$val[csf('compositiom_1')];
                $compositiom_2=$val[csf('compositiom_2')];
                $compositiom_3=$val[csf('compositiom_3')];
                $compositiom_4=$val[csf('compositiom_4')];
                $count_1=$val[csf('count_1')];
                $count_2=$val[csf('count_2')];
                $count_3=$val[csf('count_3')];
                $count_4=$val[csf('count_4')];
                $brand_1=$val[csf('brand_1')];
                $brand_2=$val[csf('brand_2')];
                $brand_3=$val[csf('brand_3')];
                $brand_4=$val[csf('brand_4')];
                $lot_1=$val[csf('lot_1')];
                $lot_2=$val[csf('lot_2')];
                $lot_3=$val[csf('lot_3')];
                $lot_4=$val[csf('lot_4')];
                $ratio_1=$val[csf('ratio_1')];
                $ratio_2=$val[csf('ratio_2')];
                $ratio_3=$val[csf('ratio_3')];
                $ratio_4=$val[csf('ratio_4')];
                $actual_count_1=$val[csf('actual_count_1')];
                $actual_count_2=$val[csf('actual_count_2')];
                $actual_count_3=$val[csf('actual_count_3')];
                $actual_count_4=$val[csf('actual_count_4')];


            }
            $sql_allo_comp=sql_select("select b.yarn_comp_type1st,b.yarn_comp_percent1st,c.color_type_id from product_details_master b,inv_material_allocation_dtls a,wo_non_ord_samp_booking_dtls c where b.id=a.item_id and a.booking_no=c.booking_no and a.booking_no='$booking_no' and b.item_category_id=1 group by b.yarn_comp_type1st,b.yarn_comp_percent1st,c.color_type_id");
            foreach ($sql_allo_comp as $row)
            {
                $yarn_compositionArr[$row[csf('yarn_comp_type1st')]]=$composition[$row[csf('yarn_comp_type1st')]];
                $colorTypeArr[$color_type[$row[csf('color_type_id')]]]=$color_type[$row[csf('color_type_id')]];
            }
            ?>
        <tr>
            <table cellspacing="0" cellpadding="0" border="1" class="rpt_table" width="1000" rules="all" >
               
                    <tr colspan ="6" bgcolor="#CCCCCC"> 
                        <th colspan ="6" style="height: 25px;">Yarn Information</th>
                    </tr>
	                <tr>
                        <td width="88" bgcolor="#CCCCCC "></td>
	                    <td width="160" bgcolor="#cee3e3 " align="center"><strong>1st Yarn (Face)</strong></td>
                        <td width="160" bgcolor="#cee3e3 " align="center"><strong>2nd Yarn (Binding)</strong></td>
                        <td width="160" bgcolor="#cee3e3 " align="center"><strong>3rd Yarn (Back)</strong></td>
                        <td width="160" bgcolor="#cee3e3 " align="center"><strong>4th Yarn (Others)</strong></td>
	                </tr>
                    <tr>
                        <td width="88" bgcolor="#cee3e3 "><strong>Count</strong></td>
                        <td><?=$count_arr[$count_1] ?></td>
                        <td><?=$count_arr[$count_2] ?></td>
                        <td><?=$count_arr[$count_3] ?></td>
                        <td><?=$count_arr[$count_4] ?></td>
                    </tr>
                    <tr>
                        <td width="88" bgcolor="#cee3e3 "><strong>Composition</strong></td>
                        <td><?=$yarn_compositionArr[$compositiom_1] ?></td>
                        <td><?=$yarn_compositionArr[$compositiom_2] ?></td>
                        <td><?=$yarn_compositionArr[$compositiom_3] ?></td>
                        <td><?=$yarn_compositionArr[$compositiom_4] ?></td>
                    </tr>
                    <tr>
                        <td width="88" bgcolor="#cee3e3 "><strong>Brand</strong></td>
                        <td><?=$brand_1 ?></td>
                        <td><?=$brand_2 ?></td>
                        <td><?=$brand_3 ?></td>
                        <td><?=$brand_4 ?></td>
                    </tr>
                    <tr>
                        <td width="88" bgcolor="#cee3e3 "><strong>Lot</strong></td>
                        <td><?=$lot_1 ?></td>
                        <td><?=$lot_2 ?></td>
                        <td><?=$lot_3 ?></td>
                        <td><?=$lot_4 ?></td>
                    </tr>
                    <tr>
                        <td width="88" bgcolor="#cee3e3 "><strong>Ratio (%)</strong></td>
                        <td><?=$ratio_1 ?></td>
                        <td><?=$ratio_2 ?></td>
                        <td><?=$ratio_3 ?></td>
                        <td><?=$ratio_4 ?></td>
                    </tr>
                    <tr>
                        <td width="88" bgcolor="#cee3e3 "><strong>Actual Count</strong></td>
                        <td><?=$actual_count_1 ?></td>
                        <td><?=$actual_count_2 ?></td>
                        <td><?=$actual_count_3 ?></td>
                        <td><?=$actual_count_4 ?></td>
                    </tr>

            </table>
        </tr>
            <?
                 $sql_knitting_query =sql_select("SELECT  a.id, a.req_id, a.company_id, a.booking_no, a.booking_id, a.fabric_color_id, a.deter_id,a.cons_dia,a.greige_dia,a.constuction,a.program_no,a.mc_no,mc_dia,a.mc_dia_type,a.mc_req_gsm,a.mc_gauge,a.mc_type,a.lycra_feeding,a.greige_gsm,a.mc_brand,a.brand_dia_type,a.remarks,a.cotton,a.polyester,a.modal,a.viscose,a.nylon,a.elastane,a.others,a.knit,a.binding,a.loop,a.yarn_dyed,a.no_of_color,a.repeat_size,a.no_of_feeder from sample_archive_knitting_info a   where   a.is_deleted=0  and  a.status_active=1 $booking_no_cond $fab_color_code_cond order by  a.id asc");

                 foreach($sql_knitting_query as $val){
                    $fabric_color_id=$val[csf('fabric_color_id')];
                    $program_no=$val[csf('program_no')];
                    $constuction=$val[csf('constuction')];
                    $cons_dia=$val[csf('cons_dia')];
                    $greige_dia=$val[csf('greige_dia')];
                    $mc_no=$val[csf('mc_no')];
                    $mc_dia=$val[csf('mc_dia')];
                    $mc_dia_type=$val[csf('mc_dia_type')];
                    $mc_req_gsm=$val[csf('mc_req_gsm')];
                    $mc_gauge=$val[csf('mc_gauge')];
                    $mc_type=$val[csf('mc_type')];
                    $lycra_feeding=$val[csf('lycra_feeding')];
                    $greige_gsm=$val[csf('greige_gsm')];
                    $mc_brand=$val[csf('mc_brand')];
                    $remarks=$val[csf('remarks')];
                    $cotton=$val[csf('cotton')];
                    $polyester=$val[csf('polyester')];
                    $modal=$val[csf('modal')];
                    $viscose=$val[csf('viscose')];
                    $nylon=$val[csf('nylon')];
                    $elastane=$val[csf('elastane')];
                    $others=$val[csf('others')];
                    $knit=$val[csf('knit')];
                    $binding=$val[csf('binding')];
                    $loop=$val[csf('loop')];
                    $yarn_dyed=$val[csf('yarn_dyed')];
                    $no_of_color=$val[csf('no_of_color')];
                    $repeat_size=$val[csf('repeat_size')];
                    $no_of_feeder=$val[csf('no_of_feeder')];

                 }
            ?>
        <tr>
            <table cellspacing="0" cellpadding="0" border="1" class="rpt_table" width="1000"  >
               
               <tr colspan ="8" bgcolor="#CCCCCC"> 
                   <th colspan ="8" style="height: 25px;">Knitting Information</th>
               </tr>
               <tr>
                   <td width="120" bgcolor="#cee3e3 "><strong>Fab Color/Code</strong></td><td colspan ="3"><?=$color_arr[$fabric_color_id]; ?></td>
                   <td width="120" bgcolor="#cee3e3 "><strong>Programm No.</strong></td><td colspan ="3"><?=$program_no ?></td>
               </tr>
               <tr>
                   <td width="120" bgcolor="#cee3e3 "><strong>Construction</strong></td><td colspan ="3"><?=$constuction; ?></td>
                   <td width="120" bgcolor="#cee3e3 "><strong>Dia</strong></td><td colspan ="1"><?= $cons_dia ?></td>
                   <td width="120" bgcolor="#cee3e3 "><strong>Greige Dia</strong></td><td><?=$$greige_dia; ?></td>
               </tr>
               <tr>
                   <td width="120" bgcolor="#cee3e3 "><strong>M/C No</strong></td><td><?= $mc_no; ?></td>
                   <td width="120" bgcolor="#cee3e3 "><strong>M/C Dia</strong></td><td><?= $mc_dia ?></td>
                   <td width="120" bgcolor="#cee3e3 "><strong>Dia Type</strong></td><td><?=$mc_dia_type; ?></td>
                   <td width="120" bgcolor="#cee3e3 "><strong>Required GSM</strong></td><td><?=$mc_req_gsm; ?></td>
               </tr>
               <tr>
                   <td width="120" bgcolor="#cee3e3 "><strong>M/C Gauge</strong></td><td><?= $mc_gauge; ?></td>
                   <td width="120" bgcolor="#cee3e3 "><strong>M/C Type</strong></td><td><?= $mc_type ?></td>
                   <td width="120" bgcolor="#cee3e3 "><strong>Lycra Feeding</strong></td><td><?=$lycra_feeding; ?></td>
                   <td width="120" bgcolor="#cee3e3 "><strong>Greige GSM</strong></td><td><?=$greige_gsm; ?></td>
               </tr>
               <tr>
                   <td width="120" bgcolor="#cee3e3 "><strong>M/C Brand</strong></td><td><?= $mc_brand; ?></td>
                   <td width="120" bgcolor="#cee3e3 "><strong>Remarks</strong></td><td colspan="5"><?= $remarks ?></td>
               </tr>
               <tr rowspan="2" >
                   <td rowspan="2" width="120" bgcolor="#cee3e3 "><strong>Fabric Composition</strong></td>
                   <td align="center"bgcolor="#cee3e3 " width="120"><strong>Cotton</strong></td>
                   <td align="center"bgcolor="#cee3e3 " width="120"><strong>Polyester</strong></td>
                   <td align="center"bgcolor="#cee3e3 " width="120"><strong>Modal</strong></td>
                   <td align="center"bgcolor="#cee3e3 " width="120"><strong>Viscose</strong></td>
                   <td align="center"bgcolor="#cee3e3 " width="120"><strong>Nylon</strong></td>
                   <td align="center"bgcolor="#cee3e3 " width="120"><strong>Elastane</strong></td>
                   <td align="center"bgcolor="#cee3e3 " width="120"><strong>Others</strong></td>
               </tr>
               <tr>
                   <td align="center" width="120"><?=$cotton ?></td>
                   <td align="center" width="120"><?=$polyester ?></td>
                   <td align="center" width="120"><?=$modal ?></td>
                   <td align="center" width="120"><?=$viscose ?></td>
                   <td align="center" width="120"><?=$nylon ?></td>
                   <td align="center" width="120"><?=$elastane ?></td>
                   <td align="center" width="120"><?=$others ?></td>
               </tr>
               <tr rowspan="2" >
                   <td rowspan="2" width="120" bgcolor="#cee3e3 "><strong>Stitch length</strong></td>
                   <td align="center"bgcolor="#cee3e3 " width="120"><strong>Knit</strong></td>
                   <td align="center"bgcolor="#cee3e3 " width="120"><strong>Binding</strong></td>
                   <td align="center"bgcolor="#cee3e3 " width="120"><strong>Loop</strong></td>
                   <td align="center"bgcolor="#cee3e3 " width="120"><strong>Yarn Dyed Stripe Measurement</strong></td>
                   <td align="center"bgcolor="#cee3e3 " width="120"><strong>No. of Color</strong></td>
                   <td align="center"bgcolor="#cee3e3 " width="120"><strong>Repeat Size</strong></td>
                   <td align="center"bgcolor="#cee3e3 " width="120"><strong>No of Feeder</strong></td>
               </tr>
               <tr>
               <td align="center" width="120"><?=$knit ?></td>
               <td align="center" width="120"><?=$binding ?></td>
               <td align="center" width="120"><?=$loop ?></td>
               <td align="center" width="120"><?=$yarn_dyed ?></td>
               <td align="center" width="120"><?=$no_of_color ?></td>
               <td align="center" width="120"><?=$repeat_size ?></td>
               <td align="center" width="120"><?=$no_of_feeder ?></td>
               </tr>
    
            </table>
        </tr>
                 <?
                 	$sql_batching_query =sql_select("SELECT  a.id, a.req_id, a.company_id, a.booking_no, a.booking_id,a.fabric_color_id, a.batch_no,a.greige_dia,a.dia_setting,a.dia_extension,a.speed,a.speed_min,a.hs_dia,a.mc_name_brand,a.greige_gsm,a.temprature,a.hs_gsm,a.no_of_chamber,a.using_chemical,a.overfeed,a.remarks,a.no_of_burners,a.intensity,a.singeing_type,a.burner_distance,a.position from sample_archive_batch_info a   where   a.is_deleted=0  and a.status_active=1 $booking_no_cond $fab_color_code_cond order by  a.id asc");

                    foreach($sql_batching_query as $val)
                    {
                        $batch_no=$val[csf('batch_no')];
                        $greige_dia=$val[csf('greige_dia')];
                        $dia_setting=$val[csf('dia_setting')];
                        $dia_extension=$val[csf('dia_extension')];
                        $speed=$val[csf('speed')];
                        $speed_min=$val[csf('speed_min')];
                        $hs_dia=$val[csf('hs_dia')];
                        $mc_name_brand=$val[csf('mc_name_brand')];
                        $greige_gsm=$val[csf('greige_gsm')];
                        $temprature=$val[csf('temprature')];
                        $hs_gsm=$val[csf('hs_gsm')];
                        $no_of_chamber=$val[csf('no_of_chamber')];
                        $using_chemical=$val[csf('using_chemical')];
                        $overfeed=$val[csf('overfeed')];
                        $remarks=$val[csf('remarks')];
                        $no_of_burners=$val[csf('no_of_burners')];
                        $intensity=$val[csf('intensity')];
                        $singeing_type=$val[csf('singeing_type')];
                        $burner_distance=$val[csf('burner_distance')];
                        $position=$val[csf('position')];

                    }
                 ?>
         <tr>
            <table cellspacing="0" cellpadding="0" border="1" class="rpt_table" width="1000" rules="all" >
               
                    <tr colspan ="8" bgcolor="#CCCCCC"> 
                        <th colspan ="8" style="height: 25px;" >Batching Information</th>
                    </tr>
	                <tr>
                        <td width="120" bgcolor="#CCCCCC " align="center" colspan ="4"><strong>Heat Setting</strong></td>
                        <td width="120" bgcolor="#cee3e3 "><strong>Dia Setting</strong></td><td width="120"><? echo $dia_setting;?></td>
                        <td width="120" bgcolor="#cee3e3 "><strong>Dia Extension</strong></td><td><? echo $dia_extension;?></td>

	                </tr>
                    <tr>
                        <td width="120" bgcolor="#cee3e3 "><strong>Batch No.</strong></td><td width="120"><? echo  $batch_no;?></td>
	                    <td width="120" bgcolor="#cee3e3 "><strong>Greige Dia</strong></td><td><?=$greige_dia ?></td>
                        <td width="120" bgcolor="#cee3e3 "><strong>Speed</strong></td><td><? echo  $speed;?></td>
                        <td width="120" bgcolor="#cee3e3 "><strong>After H/S Dia</strong></td><td><? echo  $hs_dia;?></td>
	                </tr>
                    <tr>
                        <td width="120" bgcolor="#cee3e3 "><strong>M/C Brand</strong></td><td width="120"><? echo  $mc_name_brand;?></td>
	                    <td width="120" bgcolor="#cee3e3 "><strong>Greige GSM</strong></td><td><? echo $greige_gsm;?></td>
                        <td width="120" bgcolor="#cee3e3 "><strong>Temperature</strong></td><td><? echo $temprature;?></td>
                        <td width="120" bgcolor="#cee3e3 "><strong>After H/S GSM</strong></td><td><? echo  $hs_gsm;?></td>
	                </tr>
                    <tr>
                        <td width="120" bgcolor="#cee3e3 "><strong>No of Chamber</strong></td><td width="120"><? echo  $no_of_chamber;?></td>
	                    <td width="120" bgcolor="#cee3e3 "><strong>Using Chemical</strong></td><td><? echo $using_chemical;?></td>
                        <td width="120" bgcolor="#cee3e3 "><strong>Overfeed</strong></td><td><? echo $overfeed;?></td>
                        <td width="120" bgcolor="#cee3e3 "><strong>Remarks</strong></td><td><? echo  $remarks;?></td>
	                </tr>
                    <tr>
                        <td width="120" bgcolor="#CCCCCC " align="center" colspan ="4"><strong>Singeing</strong></td>
                        <td width="120" bgcolor="#cee3e3 "><strong>No. of Burners</strong></td><td width="120"><? echo $no_of_burners;?></td>
                        <td width="120" bgcolor="#cee3e3 "><strong>Speed (m/min)</strong></td><td><? echo $speed_min;?></td>
	                </tr>
                    <tr>
                        <td width="120" bgcolor="#cee3e3 "><strong>Intensity (Mbar)</strong></td><td width="120"><? echo  $intensity;?></td>
	                    <td width="120" bgcolor="#cee3e3 "><strong>Singeing Type</strong></td><td><? echo $singeing_type;?></td>
                        <td width="120" bgcolor="#cee3e3 "><strong>Burner Distance</strong></td><td><? echo $burner_distance;?></td>
                        <td width="120" bgcolor="#cee3e3 "><strong>Singeing Position</strong></td><td><? echo  $position;?></td>
	                </tr>
            </table>
        </tr>
            <?
            	$sql_dyeing_query =sql_select("SELECT  a.id, a.req_id, a.company_id, a.booking_no, a.booking_id,a.fabric_color_id,a.batch_no,a.recipe_no,a.only_wash,a.scouring,a.reactive,a.both_part,a.enzyme,a.dyes_orginal,a.direct,a.remarks,a.dyes_add_top,a.desperse,a.white from sample_archive_dyeing_info a  where a.is_deleted=0  and  a.status_active=1  $booking_no_cond $fab_color_code_cond order by  a.id asc");

                foreach($sql_dyeing_query as $val)
                {
                    $recipe_no=$val[csf('recipe_no')];
                    $only_wash=$val[csf('only_wash')];
                    $scouring=$val[csf('scouring')];
                    $reactive=$val[csf('reactive')];
                    $both_part=$val[csf('both_part')];
                    $enzyme=$val[csf('enzyme')];
                    $dyes_orginal=$val[csf('dyes_orginal')];
                    $dyes_orginal=$val[csf('dyes_orginal')];
                    $direct=$val[csf('direct')];
                    $desperse=$val[csf('desperse')];
                    $white=$val[csf('white')];
                    $remarks=$val[csf('remarks')];

                }
            ?>
        <tr>
            <table cellspacing="0" cellpadding="0" border="1" class="rpt_table" width="1000" rules="all" >
                
                <tr colspan ="8" bgcolor="#CCCCCC"> 
                    <th colspan ="8" style="height: 25px;">Dyeing Information</th>
                </tr>
                <tr>
                    <td width="120" bgcolor="#CCCCCC " align="center" colspan ="2"><strong>Pre-Treatment</strong></td>
                    <td width="120" bgcolor="#CCCCCC " align="center" colspan ="2"><strong>Dyeing Recipe</strong></td>
                    <td width="120" bgcolor="#CCCCCC " align="center" colspan ="4"><strong>Dyeing Type</strong></td>

                </tr>
                <tr>
                    <td width="120" bgcolor="#cee3e3 "><strong>Scouring Type</strong></td><td width="120"><? echo  $scouring;?></td>
                    <td width="120" bgcolor="#cee3e3 "><strong>Recipe No.</strong></td><td><?=$recipe_no ?></td>
                    <td width="120" bgcolor="#cee3e3 "><strong>Reactive</strong></td><td><? echo  $reactive;?></td>
                    <td width="120" bgcolor="#cee3e3 "><strong>Both Part</strong></td><td><? echo  $both_part;?></td>
                </tr>
                <tr>
                    <td width="120" bgcolor="#cee3e3 "><strong>Enzyme %</strong></td><td width="120"><? echo  $enzyme;?></td>
                    <td width="120" bgcolor="#cee3e3 "><strong>Dyes (Original)</strong></td><td><? echo $dyes_orginal;?></td>
                    <td width="120" bgcolor="#cee3e3 "><strong>Direct</strong></td><td><? echo $direct;?></td>
                    <td width="120" bgcolor="#cee3e3 "><strong>Only Wash</strong></td><td><? echo  $only_wash;?></td>
                </tr>
                <tr>
                    <td width="120" bgcolor="#cee3e3 "><strong>Remarks</strong></td><td width="120"><? echo  $remarks;?></td>
                    <td width="120" bgcolor="#cee3e3 "><strong>Dyes (Add/Top)</strong></td><td><? echo $dyes_add_top;?></td>
                    <td width="120" bgcolor="#cee3e3 "><strong>Disperse</strong></td><td><? echo $desperse;?></td>
                    <td width="120" bgcolor="#cee3e3 "><strong>White</strong></td><td><? echo  $white;?></td>
                </tr>
            </table>
        </tr>
            <?
            	 $sql_finishing_query =sql_select("SELECT  a.id, a.req_id, a.company_id, a.booking_no, a.booking_id, a.fabric_color_id, a.batch_no, a.slitting_machine_no, a.slitting_after_dia, a.peach_machine_no, a.peach_after_dia, a.slitting_process, a.slitting_remarks, a.peach_fabric_speed, a.peach_after_gsm, a.peach_drum_rpm, a.peach_tension, a.stenter_machine_no, a.stenter_dia_setting, a.peach_remarks, a.stenter_mc_brand, a.stenter_paddar_pressure, a.stenter_no_chamber, a.stenter_used_chemical, a.brushing_machine_no, a.brushing_pile_rpm_2nd, a.stenter_temperature, a.stenter_after_dia, a.brushing_fabric_speed, a.brushing_counter_pile_rpm_2nd, a.stenter_overfeed, a.stenter_after_gsm, a.brushing_tension, a.brushing_after_dia, a.stenter_speed, a.stenter_remarks, a.brushing_pile_rpm_1st, a.brushing_after_gsm, a.brushing_counter_pile_rpm_1st, a.brushing_remarks, a.dryer_machine_no, a.dryer_vibration, a.dryer_temperature, a.dryer_used_chemical, a.shearing_fabric_speed, a.shearing_drum_rpm, a.dryer_overfeed, a.dryer_after_dia, a.shearing_distance_blade, a.shearing_after_dia, a.dryer_speed, a.dryer_after_gsm, a.shearing_from_comber, a.shearing_after_gsm, a.dryer_dia_settings, a.dryer_remarks, a.shearing_remarks, a.stenter_machine_no_apb, a.stenter_dia_setting_apb, a.compacting_machine_no, a.compacting_steam_pressure, a.stenter_mc_brand_apb, a.stenter_padder_pressure_apb, a.compacting_mc_brand, a.compacting_blanket_pressure, a.stenter_no_chamber_apb, a.stenter_used_chemical_apb, a.compacting_temperature, a.compacting_after_dia, a.stenter_temperature_apb, a.stenter_after_dia_apb, a.compacting_speed, a.compacting_after_gsm, a.stenter_over_feed_apb, a.stenter_after_gsm_apb, a.compacting_over_feed, a.compacting_dia_setting, a.stenter_speed_apb, a.stenter_remarks_apb, a.compacting_remarks, a.fabwash_before_dia, a.fabwash_time, a.remarks, a.fabwash_before_gsm, a.fabwash_after_dia, a.fabwash_temperature, a.fabwash_after_gsm, a.fabwash_remarks from sample_archive_finishing_info a  where   a.is_deleted=0  and  a.status_active=1 $booking_no_cond $fab_color_code_cond order by   a.id asc");

                 foreach($sql_finishing_query as $val)
                 {
                     $slitting_machine_no=$val[csf('slitting_machine_no')];
                     $val[csf('slitting_after_dia')];
                     $val[csf('peach_machine_no')];
                     $val[csf('peach_after_dia')];
                     $val[csf('slitting_process')];
                     $val[csf('slitting_remarks')];
                     $val[csf('peach_fabric_speed')];
                     $val[csf('peach_after_gsm')];
                     $val[csf('peach_drum_rpm')];
                     $val[csf('peach_tension')];
                     $val[csf('stenter_machine_no')];
                     $val[csf('stenter_dia_setting')];
                     $val[csf('peach_remarks')];
                     $val[csf('stenter_mc_brand')];
                     $val[csf('stenter_paddar_pressure')];
                     $val[csf('stenter_no_chamber')];
                     $val[csf('stenter_used_chemical')];
                     $val[csf('brushing_machine_no')];
                     $val[csf('brushing_pile_rpm_2nd')];
                     $val[csf('stenter_temperature')];
                     $val[csf('stenter_after_dia')];
                     $val[csf('brushing_fabric_speed')];
                     $val[csf('brushing_counter_pile_rpm_2nd')];
                     $val[csf('stenter_overfeed')];
                     $val[csf('brushing_tension')];
                     $val[csf('stenter_speed')];
                     $val[csf('brushing_after_dia')];
                     $val[csf('stenter_remarks')];
                     $val[csf('brushing_pile_rpm_1st')];
                     $val[csf('brushing_after_gsm')];
                     $val[csf('brushing_counter_pile_rpm_1st')];
                     $val[csf('brushing_remarks')];
                     $val[csf('dryer_machine_no')];

                        $val[csf('dryer_vibration')];
                        $val[csf('dryer_temperature')];
                        $val[csf('dryer_used_chemical')];
                        $val[csf('shearing_fabric_speed')];

                        $val[csf('shearing_drum_rpm')];
                        $val[csf('dryer_overfeed')];

                         $val[csf('dryer_after_dia')];
                         $val[csf('shearing_distance_blade')];
                         $val[csf('shearing_after_dia')];
                         $val[csf('dryer_speed')];
                         $val[csf('dryer_after_gsm')];
                         $val[csf('shearing_from_comber')];
                         $val[csf('shearing_after_gsm')];
                         $val[csf('dryer_dia_settings')];
                         $val[csf('dryer_remarks')];
                          $val[csf('shearing_remarks')];
                           $val[csf('stenter_machine_no_apb')];
                            $val[csf('stenter_dia_setting_apb')];
                             $val[csf('compacting_machine_no')];
                             $val[csf('compacting_steam_pressure')];
                              $val[csf('stenter_mc_brand_apb')];
                              $val[csf('stenter_padder_pressure_apb')];
                              $val[csf('compacting_mc_brand')];
                              $val[csf('compacting_blanket_pressure')];
                              $val[csf('stenter_no_chamber_apb')];

                                $val[csf('stenter_used_chemical_apb')];
                                $val[csf('compacting_temperature')];
                                $val[csf('compacting_after_dia')];
                                $val[csf('stenter_temperature_apb')];
                                $val[csf('stenter_after_dia_apb')];
                                $val[csf('compacting_speed')];
                                $val[csf('compacting_after_gsm')];
                                $val[csf('stenter_over_feed_apb')];
                                $val[csf('stenter_after_gsm_apb')];
                                $val[csf('compacting_over_feed')];
                                $val[csf('compacting_dia_setting')];
                                $val[csf('stenter_speed_apb')];
                                $val[csf('stenter_remarks_apb')];
                                $val[csf('compacting_remarks')];
                                $val[csf('fabwash_before_dia')];
                                $val[csf('fabwash_time')];
                                $val[csf('remarks')];
                                $val[csf('fabwash_before_gsm')];
                                $val[csf('fabwash_after_dia')];
                                $val[csf('fabwash_temperature')];
                                $val[csf('fabwash_after_gsm')];
                                $val[csf('fabwash_remarks')];
                     
 
                 }
            ?>
        <tr>
            <table cellspacing="0" cellpadding="0" border="1" class="rpt_table" width="1000" rules="all" >
                <tr colspan ="8" bgcolor="#CCCCCC"> 
                    <th colspan ="8" style="height: 25px;">Finishing Information</th>
                </tr>
                <tr>
                    <td width="120" bgcolor="#CCCCCC " align="center" colspan ="4" style="height: 25px;"><strong>Slitting</strong></td>
                    <td width="120" bgcolor="#CCCCCC " align="center" colspan ="4" style="height: 25px;"><strong>Peach</strong></td>
                </tr>
                <tr>
                    <td width="120" bgcolor="#cee3e3 "><strong>Machine No</strong></td><td width="120"><? echo  $val[csf('slitting_machine_no')];?></td>
                    <td width="120" bgcolor="#cee3e3 "><strong>After Dia</strong></td><td><?=$val[csf('slitting_after_dia')]; ?></td>
                    <td width="120" bgcolor="#cee3e3 "><strong>Machine No</strong></td><td><? echo  $val[csf('peach_machine_no')];?></td>
                    <td width="120" bgcolor="#cee3e3 "><strong>After Dia</strong></td><td><? echo  $val[csf('peach_after_dia')];?></td>
                </tr>
                <tr>
                    <td width="120" bgcolor="#cee3e3 "><strong>Slitting Process</strong></td><td width="120"><? echo $val[csf('slitting_process')];?></td>
                    <td width="120" bgcolor="#cee3e3 "><strong>Remarks</strong></td><td><? echo $val[csf('slitting_remarks')];?></td>
                    <td width="120" bgcolor="#cee3e3 "><strong>Fabric Speed</strong></td><td><? echo $val[csf('peach_fabric_speed')];?></td>
                    <td width="120" bgcolor="#cee3e3 "><strong>After GSM</strong></td><td><? echo  $val[csf('peach_after_gsm')];?></td>
                </tr>
                <tr>
                    <td width="120" bgcolor="#CCCCCC " align="center" style="height: 25px;" colspan ="4"><strong>Stenter</strong></td>
                    <td width="120" bgcolor="#cee3e3 "><strong>Drum RPM</strong></td><td  width="120"><? echo $val[csf('peach_drum_rpm')];?></td>
                    <td width="120" bgcolor="#cee3e3 "><strong>Tension</strong></td><td  width="120"><? echo  $val[csf('peach_tension')];?></td>
                </tr>
                <tr>
                    <td width="120" bgcolor="#cee3e3 "><strong>Machine No</strong></td><td width="120"><? echo  $val[csf('stenter_machine_no')];?></td>
                    <td width="120" bgcolor="#cee3e3 "><strong>Dia Setting</strong></td><td><? echo $val[csf('stenter_dia_setting')];?></td>
                    <td width="120" bgcolor="#cee3e3 " ><strong>Remarks</strong></td><td colspan="3"><? echo $val[csf('peach_remarks')];?></td>
                </tr>
                <!-- till thish pause for this day -->
                <tr> 
                    <td width="120" bgcolor="#cee3e3 "><strong>M/C Brand</strong></td><td width="120"><? echo $val[csf('stenter_mc_brand')];?></td>
                    <td width="120" bgcolor="#cee3e3 "><strong>Padder Pressure (Kg)</strong></td><td  width="120"><? echo  $val[csf('stenter_paddar_pressure')];?></td>
                    <td width="120" bgcolor="#CCCCCC " align="center" style="height: 25px;" colspan ="4"><strong>Brushing</strong></td>
                </tr>
                <tr>
                    <td width="120" bgcolor="#cee3e3 "><strong>No of Chamber</strong></td><td width="120"><? echo  $val[csf('stenter_no_chamber')];?></td>
                    <td width="120" bgcolor="#cee3e3 "><strong>Used Chemical</strong></td><td><? echo $val[csf('stenter_used_chemical')];?></td>
                    <td width="120" bgcolor="#cee3e3 "><strong>Machine No</strong></td><td><? echo $val[csf('brushing_machine_no')];?></td>
                    <td width="120" bgcolor="#cee3e3 "><strong>Pile RPM (2nd Drum)</strong></td><td><? echo  $val[csf('brushing_pile_rpm_2nd')];?></td>
                </tr>
                <tr> 
                    <td width="120" bgcolor="#cee3e3 "><strong>Temperature</strong></td><td width="120"><? echo  $val[csf('stenter_temperature')];?></td>
                    <td width="120" bgcolor="#cee3e3 "><strong>After Dia</strong></td><td><? echo $val[csf('stenter_after_dia')];?></td>
                    <td width="120" bgcolor="#cee3e3 "><strong>Fabric Speed</strong></td><td><? echo $val[csf('brushing_fabric_speed')];?></td>
                    <td width="120" bgcolor="#cee3e3 "><strong>Counter Pile RPM (2nd Drum)</strong></td><td><? echo  $val[csf('brushing_counter_pile_rpm_2nd')];?></td>
                </tr>
                <tr>
                    <td width="120" bgcolor="#cee3e3 "><strong>Overfeed</strong></td><td width="120"><? echo  $val[csf('stenter_overfeed')];?></td>
                    <td width="120" bgcolor="#cee3e3 "><strong>After GSM</strong></td><td><? echo $val[csf('stenter_after_gsm')];?></td>
                    <td width="120" bgcolor="#cee3e3 "><strong>Tension</strong></td><td><? echo $val[csf('brushing_tension')];?></td>
                    <td width="120" bgcolor="#cee3e3 "><strong>After Dia</strong></td><td><? echo  $val[csf('brushing_after_dia')];?></td>
                </tr>
                <tr> 
                    <td width="120" bgcolor="#cee3e3 "><strong>Speed</strong></td><td width="120"><? echo  $val[csf('stenter_speed')];?></td>
                    <td width="120" bgcolor="#cee3e3 "><strong>Remarks</strong></td><td><? echo $val[csf('stenter_remarks')];?></td>
                    <td width="120" bgcolor="#cee3e3 " ><strong>Pile RPM (1st Drum)</strong></td><td><? echo $val[csf('brushing_pile_rpm_1st')];?></td>
                    <td width="120" bgcolor="#cee3e3 " ><strong>After GSM</strong></td><td><? echo $val[csf('brushing_after_gsm')];?></td>
                </tr>
                <tr>
                    <td width="120" bgcolor="#CCCCCC " align="center" style="height: 25px;" colspan ="4"><strong>Dryer</strong></td>
                    <td width="120" bgcolor="#cee3e3 "><strong>Counter Pile RPM (1st Drum)</strong></td><td  width="120"><? echo $val[csf('brushing_counter_pile_rpm_1st')];?></td>
                    <td width="120" bgcolor="#cee3e3 "><strong>Remarks</strong></td><td><? echo  $val[csf('brushing_remarks')];?></td>
                </tr>
                <tr>
                    <td width="120" bgcolor="#cee3e3 "><strong>Machine No</strong></td><td  width="120"><? echo $val[csf('dryer_machine_no')];?></td>
                    <td width="120" bgcolor="#cee3e3 "><strong>Vibration</strong></td><td  width="120"><? echo  $val[csf('dryer_vibration')];?></td>
                    <td width="120" bgcolor="#CCCCCC " align="center" style="height: 25px;" colspan ="4"><strong>Shearing</strong></td>
                </tr>
                <tr>
                    <td width="120" bgcolor="#cee3e3 "><strong>Temperature</strong></td><td width="120"><? echo  $val[csf('dryer_temperature')];?></td>
                    <td width="120" bgcolor="#cee3e3 "><strong>Used Chemical</strong></td><td><? echo $val[csf('dryer_used_chemical')];?></td>
                    <td width="120" bgcolor="#cee3e3 "><strong>Fabric Speed</strong></td><td><? echo $val[csf('shearing_fabric_speed')];?></td>
                    <td width="120" bgcolor="#cee3e3 "><strong>Drum RPM</strong></td><td><? echo  $val[csf('shearing_drum_rpm')];?></td>
                </tr>
                <tr>  
                    <td width="120" bgcolor="#cee3e3 "><strong>Overfeed</strong></td><td width="120"><? echo  $val[csf('dryer_overfeed')];?></td>
                    <td width="120" bgcolor="#cee3e3 "><strong>After Dia</strong></td><td><? echo $val[csf('dryer_used_chemical')];?></td>
                    <td width="120" bgcolor="#cee3e3 "><strong>Distance from Blade</strong></td><td><? echo $val[csf('shearing_distance_blade')];?></td>
                    <td width="120" bgcolor="#cee3e3 "><strong>After Dia</strong></td><td><? echo  $val[csf('shearing_after_dia')];?></td>
                </tr>
                <tr>
                    <td width="120" bgcolor="#cee3e3 "><strong>Speed</strong></td><td width="120"><? echo  $val[csf('dryer_speed')];?></td>
                    <td width="120" bgcolor="#cee3e3 "><strong>After GSM</strong></td><td><? echo $val[csf('dryer_after_gsm')];?></td>
                    <td width="120" bgcolor="#cee3e3 "><strong>Distance from Comber</strong></td><td><? echo $val[csf('shearing_from_comber')];?></td>
                    <td width="120" bgcolor="#cee3e3 "><strong>After GSM</strong></td><td><? echo  $val[csf('shearing_after_gsm')];?></td>
                </tr>
                <tr>
                    <td width="120" bgcolor="#cee3e3 "><strong>Dia Setting</strong></td><td width="120"><? echo  $val[csf('dryer_dia_settings')];?></td>
                    <td width="120" bgcolor="#cee3e3 "><strong>Remarks</strong></td><td><? echo $val[csf('dryer_remarks')];?></td>
                    <td width="120" bgcolor="#cee3e3 " ><strong>Remarks</strong></td><td colspan="3"><? echo $val[csf('shearing_remarks')];?></td>
                </tr>
                <tr> 
                    <td width="120" bgcolor="#CCCCCC " align="center" style="height: 25px;" colspan ="4"><strong>Stenter (After Peach/ Brush)</strong></td>
                    <td width="120" bgcolor="#CCCCCC " align="center" style="height: 25px;" colspan ="4"><strong>Compacting</strong></td>
                </tr>
                <tr>
                    <td width="120" bgcolor="#cee3e3 "><strong>Machine No</strong></td><td width="120"><? echo  $val[csf('stenter_machine_no_apb')];?></td>
                    <td width="120" bgcolor="#cee3e3 "><strong>Dia Setting</strong></td><td><? echo $val[csf('stenter_dia_setting_apb')];?></td>
                    <td width="120" bgcolor="#cee3e3 "><strong>Machine No</strong></td><td><? echo $val[csf('compacting_machine_no')];?></td>
                    <td width="120" bgcolor="#cee3e3 "><strong>Steam Pressure</strong></td><td><? echo  $val[csf('compacting_steam_pressure')];?></td>
                </tr>
                <tr>
                    <td width="120" bgcolor="#cee3e3 "><strong>M/C Brand</strong></td><td width="120"><? echo  $val[csf('stenter_mc_brand_apb')];?></td>
                    <td width="120" bgcolor="#cee3e3 "><strong>Padder Pressure (Kg)</strong></td><td><? echo $val[csf('stenter_padder_pressure_apb')];?></td>
                    <td width="120" bgcolor="#cee3e3 "><strong>M/C Brand</strong></td><td><? echo $val[csf('compacting_mc_brand')];?></td>
                    <td width="120" bgcolor="#cee3e3 "><strong>Blanket Pressure</strong></td><td><? echo  $val[csf('compacting_blanket_pressure')];?></td>
                </tr>
                <tr>
                    <td width="120" bgcolor="#cee3e3 "><strong>No of Chamber</strong></td><td width="120"><? echo  $val[csf('stenter_no_chamber_apb')];?></td>
                    <td width="120" bgcolor="#cee3e3 "><strong>Used Chemical</strong></td><td><? echo $val[csf('stenter_used_chemical_apb')];?></td>
                    <td width="120" bgcolor="#cee3e3 "><strong>Temperature</strong></td><td><? echo $val[csf('compacting_temperature')];?></td>
                    <td width="120" bgcolor="#cee3e3 "><strong>After Dia</strong></td><td><? echo  $val[csf('compacting_after_dia')];?></td>
                </tr>
                <tr>
                    <td width="120" bgcolor="#cee3e3 "><strong>Temperature</strong></td><td width="120"><? echo  $val[csf('stenter_temperature_apb')];?></td>
                    <td width="120" bgcolor="#cee3e3 "><strong>After Dia</strong></td><td><? echo $val[csf('stenter_after_dia_apb')];?></td>
                    <td width="120" bgcolor="#cee3e3 "><strong>Speed</strong></td><td><? echo $val[csf('compacting_speed')];?></td>
                    <td width="120" bgcolor="#cee3e3 "><strong>After GSM</strong></td><td><? echo  $val[csf('compacting_after_gsm')];?></td>
                </tr>
                <tr> 
                    <td width="120" bgcolor="#cee3e3 "><strong>Overfeed</strong></td><td width="120"><? echo  $val[csf('stenter_over_feed_apb')];?></td>
                    <td width="120" bgcolor="#cee3e3 "><strong>After GSM</strong></td><td><? echo $val[csf('stenter_after_gsm_apb')];?></td>
                    <td width="120" bgcolor="#cee3e3 "><strong>Over Feed %</strong></td><td><? echo $val[csf('compacting_over_feed')];?></td>
                    <td width="120" bgcolor="#cee3e3 "><strong>Dia Setting</strong></td><td><? echo  $val[csf('compacting_dia_setting')];?></td>
                </tr>
                <tr>
                    <td width="120" bgcolor="#cee3e3 " ><strong>Speed</strong></td><td width="120"><? echo  $val[csf('stenter_speed_apb')];?></td>
                    <td width="120" bgcolor="#cee3e3 "><strong>Remarks</strong></td><td><? echo $val[csf('stenter_remarks_apb')];?></td>
                    <td width="120" bgcolor="#cee3e3 " ><strong>Remarks</strong></td><td colspan="3"><? echo $val[csf('compacting_remarks')];?></td>
                </tr>
                <tr>
                    <td width="120" bgcolor="#CCCCCC " align="center" style="height: 25px;" colspan ="4"><strong>Fabric Wash-Tumble</strong></td>
                    <td width="120" bgcolor="CCCCCC " align="center" style="height: 25px;" colspan ="4"><strong>Remarks</strong></td>
                </tr>
                <tr> 
                    <td width="120" bgcolor="#cee3e3 "><strong>Before Dia</strong></td><td width="120"><? echo  $val[csf('fabwash_before_dia')];?></td>
                    <td width="120" bgcolor="#cee3e3 "><strong>Time</strong></td><td><? echo $val[csf('fabwash_time')];?></td>
                    <td colspan ="4" rowspan="4"><? echo $val[csf('remarks')];?></td>
                </tr>
                <tr>
                    <td width="120" bgcolor="#cee3e3 "><strong>Before GSM</strong></td><td width="120"><? echo  $val[csf('fabwash_before_gsm')];?></td>
                    <td width="120" bgcolor="#cee3e3 "><strong>After Dia</strong></td><td><? echo $val[csf('fabwash_after_dia')];?></td>
                </tr>
                <tr>
                    <td width="120" bgcolor="#cee3e3 "><strong>Temperature</strong></td><td width="120"><? echo  $val[csf('fabwash_temperature')];?></td>
                    <td width="120" bgcolor="#cee3e3 "><strong>After GSM</strong></td><td><? echo $val[csf('fabwash_after_gsm')];?></td>
                </tr>
                <tr>
                    <td width="120" bgcolor="#cee3e3 "><strong>Remarks</strong></td><td width="120"  colspan="3"><? echo  $val[csf('fabwash_remarks')];?></td>
                </tr>
            </table>
        </tr>
            <?
                	$sql_merce_sunfo_query =sql_select("SELECT   a.id, a.booking_id, a.booking_no, a.req_id, a.company_id, a.fabric_color_id, a.batch_no, a.total_liquare, a.caustic_solution, a.sunforized_temperature, a.taflon_pressurecompection, a.mercerized_temperature, a.mc_speed, a.over_feed, a.speed, a.mercerized_ph, a.normal_wash, a.steam, a.acetic_acid, a.unloading_ph, a.sunforized_remarks, a.mercerized_remarks from sample_archive_mercerized_sunforized_info a  where  a.is_deleted=0  and   a.status_active=1 $booking_no_cond $fab_color_code_cond order by   a.id asc");
                    foreach($sql_merce_sunfo_query as $val)
                    {
                        $total_liquare=$val[csf('total_liquare')];
                        $caustic_solution=$val[csf('caustic_solution')];
                        $sunforized_temperature=$val[csf('sunforized_temperature')];
                        $taflon_pressurecompection=$val[csf('taflon_pressurecompection')];
                        $mercerized_temperature=$val[csf('mercerized_temperature')];
                        $over_feed=$val[csf('over_feed')];
                        $mc_speed=$val[csf('mc_speed')];
                        $speed=$val[csf('speed')];
                        $mercerized_ph=$val[csf('mercerized_ph')];
                        $normal_wash=$val[csf('normal_wash')];
                        $steam=$val[csf('steam')];
                        $acetic_acid=$val[csf('acetic_acid')];
                        $unloading_ph=$val[csf('unloading_ph')];
                        $sunforized_remarks=$val[csf('sunforized_remarks')];
                        $mercerized_remarks=$val[csf('mercerized_remarks')];

                    }
            ?>
        <tr>
            <table cellspacing="0" cellpadding="0" border="1" class="rpt_table" width="1000" rules="all" >
                
                <tr colspan ="8" bgcolor="#CCCCCC"> 
                    <th colspan ="8" style="height: 25px;">Mercerized & Sunforized Information</th>
                </tr>
                <tr>
                    <td width="120" bgcolor="#CCCCCC " align="center" colspan ="4"><strong>Mercerized</strong></td>
                    <td width="120" bgcolor="#CCCCCC " align="center" colspan ="4"><strong>Sunforized</strong></td>
                </tr>
                <tr>
                    <td width="120" bgcolor="#cee3e3 "><strong>Total Liquare</strong></td><td width="120"><? echo  $total_liquare;?></td>
                    <td width="120" bgcolor="#cee3e3 "><strong>Caustic Solution in °B</strong></td><td width="120"><?=$caustic_solution ?></td>
                    <td width="120" bgcolor="#cee3e3 "><strong>Temperature</strong></td><td width="120"><? echo  $sunforized_temperature;?></td>
                    <td width="120" bgcolor="#cee3e3 "><strong>Taflon Pressure /Compection</strong></td><td width="120"><? echo  $taflon_pressurecompection;?></td>
                </tr>
                <tr>
                    <td width="120" bgcolor="#cee3e3 "><strong>Temperature</strong></td><td width="120"><? echo  $mercerized_temperature;?></td>
                    <td width="120" bgcolor="#cee3e3 "><strong>M/C speed</strong></td><td width="120"><? echo $mc_speed;?></td>
                    <td width="120" bgcolor="#cee3e3 "><strong>Over Feed</strong></td><td width="120"><? echo $over_feed;?></td>
                    <td width="120" bgcolor="#cee3e3 "><strong>Speed</strong></td><td width="120"><? echo  $speed;?></td>
                </tr>
                <tr>
                    <td width="120" bgcolor="#cee3e3 "><strong>Mecerized PH</strong></td><td width="120"><? echo  $mercerized_ph;?></td>
                    <td width="120" bgcolor="#cee3e3 "><strong>Normal wash</strong></td><td width="120"><? echo $normal_wash;?></td>
                    <td width="120" bgcolor="#cee3e3 "><strong>Steam</strong></td><td width="120"><? echo $steam;?></td>
                    <td width="120" bgcolor="#CCCCCC "><strong></strong></td><td bgcolor="#CCCCCC " width="120"></td>
                </tr>
                <tr>
                    <td width="120" bgcolor="#cee3e3 "><strong>Acetic Acid (G/L)</strong></td><td width="120"><? echo  $acetic_acid;?></td>
                    <td width="120" bgcolor="#cee3e3 "><strong>Unloading PH</strong></td><td width="120"><? echo $unloading_ph;?></td>
                    <td width="120" bgcolor="#cee3e3 " rowspan="2"><strong>Remarks</strong></td><td colspan="3" rowspan="2"><? echo $sunforized_remarks;?></td>
                </tr>
                <tr>
                    <td width="120" bgcolor="#cee3e3 " ><strong>Remarks</strong></td><td width="120" colspan="3"><? echo  $mercerized_remarks;?></td>
                </tr>
            </table>
        </tr>
            <?
            	 $sql_physical_result =sql_select("SELECT  a.id, a.fabric_color_id, a.batch_no, a.req_id, a.company_id, a.booking_no, a.booking_id, a.dry_process, a.actual_gsm, a.phenolic_yellowing, a.length, a.test_method, a.pilling, a.cf_to_light, a.width, a.req_dia, a.bursting_strength, a.cf_to_saliva, a.twisting, a.actual_dia, a.dry_rubbing, a.wpi, a.wash_temperature, a.req_gsm, a.wet_rubbing, a.cpi, a.acetate_1, a.acetate_2, a.acetate_3, a.acetate_4, a.acetate_5, a.acetate_6, a.Water_1, a.Water_2, a.Water_3, a.Water_4, a.Water_5, a.Water_6, a.perspiration_acid_1 , a.perspiration_acid_2, a.perspiration_acid_3, a.perspiration_acid_4, a.perspiration_acid_5, a.perspiration_acid_6, a.perspiration_alkali_1, a.perspiration_alkali_2, a. perspiration_alkali_3, a.perspiration_alkali_4, a.perspiration_alkali_5, a.perspiration_alkali_6, a.remarks, a.delivery_date from sample_archive_physical_test_info a  where    a.is_deleted=0  and   a.status_active=1  $booking_no_cond $fab_color_code_cond order by   a.id asc");

                 foreach($sql_physical_result as $val)
                 {
                    $dry_process=$val[csf('dry_process')];
                    $actual_gsm=$val[csf('actual_gsm')];
                    $phenolic_yellowing=$val[csf('phenolic_yellowing')];
                    $length=$val[csf('length')]; 
                    $length=$val[csf('test_method')];
                    $pilling=$val[csf('pilling')];
                    $pilling=$val[csf('cf_to_light')];
                    $width=$val[csf('width')];
                    $req_dia=$val[csf('req_dia')];
                    $bursting_strength=$val[csf('bursting_strength')];
                    $cf_to_saliva=$val[csf('cf_to_saliva')];
                    $twisting=$val[csf('twisting')];
                    $actual_dia=$val[csf('actual_dia')];
                    $dry_rubbing=$val[csf('dry_rubbing')];
                    $wpi=$val[csf('wpi')];
                    $wash_temperature=$val[csf('wash_temperature')];
                    $req_gsm=$val[csf('req_gsm')]; 
                    $wet_rubbing=$val[csf('wet_rubbing')];
                    $cpi=$val[csf('cpi')];
                    $acetate_1=$val[csf('acetate_1')];
                    $acetate_2=$val[csf('acetate_2')];
                    $acetate_3=$val[csf('acetate_3')];
                    $acetate_4=$val[csf('acetate_4')]; 
                    $acetate_5=$val[csf('acetate_5')];
                    $acetate_6=$val[csf('acetate_6')];
                    $Water_1=$val[csf('Water_1')];
                    $Water_2=$val[csf('Water_2')];
                    $Water_3=$val[csf('Water_3')];
                    $Water_4=$val[csf('Water_4')];
                    $Water_5=$val[csf('Water_5')]; 
                    $Water_6=$val[csf('Water_6')];
                    $perspiration_acid_1=$val[csf('perspiration_acid_1')];
                    $perspiration_acid_2=$val[csf('perspiration_acid_2')];
                    $perspiration_acid_3=$val[csf('perspiration_acid_3')];
                    $perspiration_acid_4=$val[csf('perspiration_acid_4')];
                    $perspiration_acid_5=$val[csf('perspiration_acid_5')];
                    $perspiration_acid_6=$val[csf('perspiration_acid_6')];
                    $perspiration_alkali_1=$val[csf('perspiration_alkali_1')];
                    $perspiration_alkali_2=$val[csf('perspiration_alkali_2')];
                    $perspiration_alkali_3=$val[csf('perspiration_alkali_3')];
                    $perspiration_alkali_4=$val[csf('perspiration_alkali_4')];
                    $perspiration_alkali_5=$val[csf('perspiration_alkali_5')];
                    $perspiration_alkali_6=$val[csf('perspiration_alkali_6')];
                    $remarks=$val[csf('remarks')];
                    $delivery_date=$val[csf('delivery_date')];

                 }

            ?>
        <tr>
            <table cellspacing="0" cellpadding="0" border="1" class="rpt_table" width="1000" rules="all" >
                
                <tr colspan ="8" bgcolor="#CCCCCC"> 
                    <th colspan ="8" style="height: 25px;">Physical Test Information</th>
                </tr>
                <tr>
                    <td width="120" bgcolor="#CCCCCC " align="center" colspan ="2"><strong>Dimensional Stability</strong></td>
                    <td width="120" bgcolor="#cee3e3 "><strong>Dry Process</strong></td><td width="120"><? echo  $dry_process;?></td>
                    <td width="120" bgcolor="#cee3e3 "><strong>Actual GSM</strong></td><td width="120"><? echo  $actual_gsm;?></td>
                    <td width="120" bgcolor="#cee3e3 "><strong>Phenolic Yellowing</strong></td><td width="120"><? echo  $phenolic_yellowing;?></td>
                </tr>
                <tr>
                    <td width="120" bgcolor="#cee3e3 "><strong>Length %</strong></td><td width="120"><? echo  $length;?></td>
                    <td width="120" bgcolor="#cee3e3 "><strong>Test Method</strong></td><td width="120"><?=$test_method ?></td>
                    <td width="120" bgcolor="#cee3e3 "><strong>Pilling</strong></td><td width="120"><? echo  $pilling;?></td>
                    <td width="120" bgcolor="#cee3e3 "><strong>C/F to Light</strong></td><td width="120"><? echo  $cf_to_light;?></td>
                </tr>
                <tr>
                    <td width="120" bgcolor="#cee3e3 "><strong>Width %</strong></td><td width="120"><? echo  $width;?></td>
                    <td width="120" bgcolor="#cee3e3 "><strong>Req. Dia</strong></td><td width="120"><? echo $req_dia;?></td>
                    <td width="120" bgcolor="#cee3e3 "><strong>Bursting Strength</strong></td><td width="120"><? echo $bursting_strength;?></td>
                    <td width="120" bgcolor="#cee3e3 "><strong>C/F to Saliva</strong></td><td width="120"><? echo  $cf_to_saliva;?></td>
                </tr>
                <tr>
                    <td width="120" bgcolor="#cee3e3 "><strong>Twisting %</strong></td><td width="120"><? echo  $twisting;?></td>
                    <td width="120" bgcolor="#cee3e3 "><strong>Actual Dia</strong></td><td width="120"><? echo $actual_dia;?></td>
                    <td width="120" bgcolor="#cee3e3 "><strong>Dry Rubbing</strong></td><td width="120"><? echo $dry_rubbing;?></td>
                    <td width="120" bgcolor="#cee3e3 "><strong>WPI</strong></td><td width="120"><? echo $wpi;?></td>
                </tr>
                <tr>
                    <td width="120" bgcolor="#cee3e3 "><strong>Wash Temperature</strong></td><td width="120"><? echo  $wash_temperature;?></td>
                    <td width="120" bgcolor="#cee3e3 "><strong>Req. GSM</strong></td><td width="120"><? echo $req_gsm;?></td>
                    <td width="120" bgcolor="#cee3e3 "><strong>Wet Rubbing</strong></td><td width="120"><? echo $wet_rubbing;?></td>
                    <td width="120" bgcolor="#cee3e3 "><strong>CPI</strong></td><td width="120"><? echo $cpi;?></td>
                </tr>
                <tr>
                    <td width="120" bgcolor="#CCCCCC " align="center" colspan ="2"><strong>Color Fastness Test</strong></td>
                    <td width="120" bgcolor="#CCCCCC " align="center"><strong>Acetate</strong></td><td width="120" bgcolor="#CCCCCC " align="center"><strong>Cotton</strong></td>
                    <td width="120" bgcolor="#CCCCCC " align="center"><strong>Polymide</strong></td><td width="120" bgcolor="#CCCCCC " align="center"><strong>Polyester</strong></td>
                    <td width="120" bgcolor="#CCCCCC " align="center"><strong>Acrylic</strong></td><td width="120" bgcolor="#CCCCCC " align="center"><strong>Wool</strong></td>
                </tr>
                <tr>
                    <td width="120" bgcolor="#cee3e3 " align="center" colspan ="2"><strong>C/F to Wash</strong></td>
                    <td width="120"><? echo $acetate_1;?></td><td width="120"><? echo $acetate_2;?></td>
                    <td width="120"><? echo $acetate_3;?></td><td width="120"><? echo $acetate_4;?></td>
                    <td width="120"><? echo $acetate_5;?></td><td width="120"><? echo $acetate_6;?></td>
                </tr>
                <tr>
                    <td width="120" bgcolor="#cee3e3 " align="center" colspan ="2"><strong>C/F to Water (Coss Staining)</strong></td>
                    <td width="120"><? echo $Water_1;?></td><td width="120"><? echo $Water_2;?></td>
                    <td width="120"><? echo $Water_3;?></td><td width="120"><? echo $Water_4;?></td>
                    <td width="120"><? echo $Water_5;?></td><td width="120"><? echo $Water_6;?></td>
                </tr>
                <tr>
                    <td width="120" bgcolor="#cee3e3 " align="center" colspan ="2"><strong>C/F to Perspiration (Acid)</strong></td>
                    <td width="120"><? echo $perspiration_acid_1;?></td><td width="120"><? echo $perspiration_acid_2;?></td>
                    <td width="120"><? echo $perspiration_acid_3;?></td><td width="120"><? echo $perspiration_acid_4;?></td>
                    <td width="120"><? echo $perspiration_acid_5;?></td><td width="120"><? echo $perspiration_acid_6;?></td>
                </tr>
                <tr>
                    <td width="120" bgcolor="#cee3e3 " align="center" colspan ="2"><strong>C/F to perspiration (Alkali)</strong></td>
                    <td width="120"><? echo $perspiration_alkali_1;?></td><td width="120"><? echo $perspiration_alkali_2;?></td>
                    <td width="120"><? echo $perspiration_alkali_3;?></td><td width="120"><? echo $perspiration_alkali_4;?></td>
                    <td width="120"><? echo $perspiration_alkali_5;?></td><td width="120"><? echo $perspiration_alkali_6;?></td>
                </tr>
                <tr>
                    <td width="120" bgcolor="#cee3e3 " align="center" colspan ="2"><strong>Remarks</strong></td>
                    <td width="120" colspan="4"><? echo $remarks;?></td>
                    <td width="120" bgcolor="#cee3e3 "><strong>Delivery Date</strong></td><td width="120"><? echo $delivery_date;?></td>
                </tr>
            </table>
        </tr>
            <?
            	$sql_washing_result =sql_select("SELECT  a.id,a.booking_id,a.booking_no,a.req_id,a.company_id,a.fabric_color_id,a.batch_id,a.mc_no,a.wash_rpm,a.chemical_name,a.tumble_dryer_no,a.process_name,a.pre_treatment,a.dyes_name,a.extra_process,a.wash_temperature,a.recepi_no,a.hydro_no,a.dry_process,a.remarks from sample_archive_washing_info a  where  a.is_deleted=0  and  a.status_active=1 $booking_no_cond $fab_color_code_cond order by  a.id asc");

                foreach($sql_washing_result as $val)
                {
                    $mc_no=$val[csf('mc_no')];
                    $wash_rpm=$val[csf('wash_rpm')];
                    $chemical_name=$val[csf('chemical_name')];
                    $tumble_dryer_no=$val[csf('tumble_dryer_no')];
                    $process_name=$val[csf('process_name')];
                    $pre_treatment=$val[csf('pre_treatment')];
                    $dyes_name=$val[csf('dyes_name')];
                    $extra_process=$val[csf('extra_process')];
                    $wash_temperature=$val[csf('wash_temperature')];
                    $recepi_no=$val[csf('recepi_no')];
                    $hydro_no=$val[csf('hydro_no')];
                    $dry_process=$val[csf('dry_process')];
                    $remarks=$val[csf('remarks')];

                }


             
            ?>
        <tr>
            <table cellspacing="0" cellpadding="0" border="1" class="rpt_table" width="1000" rules="all" >
                
                <tr colspan ="8" bgcolor="#CCCCCC"> 
                    <th colspan ="8" style="height: 25px;">Washing Information</th>
                </tr>
                <tr>
                    <td width="120" bgcolor="#cee3e3 "><strong>M/C No</strong></td><td width="120"><? echo  $mc_no;?></td>
                    <td width="120" bgcolor="#cee3e3 "><strong>RPM</strong></td><td width="120"><?=$wash_rpm ?></td>
                    <td width="120" bgcolor="#cee3e3 "><strong>Chemical Name</strong></td><td width="120"><? echo  $chemical_name;?></td>
                    <td width="120" bgcolor="#cee3e3 "><strong>Tumble Dryer No</strong></td><td width="120"><? echo  $tumble_dryer_no;?></td>
                </tr>
                <tr>
                    <td width="120" bgcolor="#cee3e3 "><strong>Process Name</strong></td><td width="120"><? echo  $process_name;?></td>
                    <td width="120" bgcolor="#cee3e3 "><strong>Pre-Treatment</strong></td><td width="120"><? echo $pre_treatment;?></td>
                    <td width="120" bgcolor="#cee3e3 "><strong>Dyes Name</strong></td><td width="120"><? echo $dyes_name;?></td>
                    <td width="120" bgcolor="#cee3e3 "><strong>Extra Process</strong></td><td width="120"><? echo  $extra_process;?></td>
                </tr>
                <tr>
                    <td width="120" bgcolor="#cee3e3 "><strong>Temperature</strong></td><td width="120"><? echo  $wash_temperature;?></td>
                    <td width="120" bgcolor="#cee3e3 "><strong>Recepi No.</strong></td><td width="120"><? echo $recepi_no;?></td>
                    <td width="120" bgcolor="#cee3e3 "><strong>Hydro No</strong></td><td width="120"><? echo $hydro_no;?></td>
                    <td width="120" bgcolor="#cee3e3 "><strong>Dry Process</strong></td><td width="120"><? echo $dry_process;?></td>
                </tr>
                <tr>
                    <td width="120" bgcolor="#cee3e3 "><strong>Remarks</strong></td><td colspan="6" width="120"><? echo  $remarks;?></td>
                </tr>
            </table>
        </tr>
        </table>

       <?
}


?>