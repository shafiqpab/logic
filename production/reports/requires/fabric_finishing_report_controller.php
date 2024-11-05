<? 
header('Content-type:text/html; charset=utf-8');
session_start();
if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");

require_once('../../../includes/common.php');

$user_name=$_SESSION['logic_erp']['user_id'];
$data=$_REQUEST['data'];
$action=$_REQUEST['action'];
$color_library=return_library_array( "select id,color_name from lib_color", "id", "color_name"  );
$buyer_list=return_library_array( "select id,short_name from lib_buyer", "id", "short_name"  );
$company_library=return_library_array( "select id,company_name from lib_company", "id", "company_name"  );
$floor_arr=return_library_array( "select id,floor_name from  lib_prod_floor",'id','floor_name');
$buyer_arr=return_library_array( "select id,short_name from lib_buyer", "id", "short_name"  );
$search_by_arr=array(0=>"--All--",1=>"Heat Setting",2=>"Slitting/Squeezing",3=>"Drying", 9=>"Stentering",4=>"Compacting",5=>"Special Finish",6=>"Wait For Slitting/Squeezing",10=>"Wait For Stentering",7=>"Wait For Drying",8=>"Wait For Compacting");//,9=>"Wait For Special Finish";
if($db_type==0) $group_concat="group_concat(c.po_number)"; 
else if($db_type==2) $group_concat="listagg(c.po_number,',' ) within group (order by c.po_number) AS po_number";


if ($action=="load_drop_down_location")
{
    echo create_drop_down( "cbo_location_id", 100, "select id,location_name from lib_location where status_active =1 and is_deleted=0 and company_id='$data' order by location_name","id,location_name", 1, "--Select Location--", $selected, "load_drop_down( 'requires/fabric_finishing_report_controller', document.getElementById('cbo_working_company_id').value+'_'+this.value, 'load_drop_down_floor', 'floor_td' );",0 );
    exit();          
}

if ($action=="load_drop_down_floor")
{
    $ex_data=explode('_',$data);
    if($ex_data[1]!=0) $location_cond=" and location_id='$ex_data[1]'"; else $location_cond="";
    echo create_drop_down( "cbo_floor_id", 100, "select id, floor_name from lib_prod_floor where production_process in (3,4) and company_id ='$ex_data[0]' and status_active =1 $location_cond group by id, floor_name order by floor_name","id,floor_name", 1, "-- Select Floor --", 0, "","" );//
  exit();    
}

//--------------------------------------------------------------------------------------------------------------------
if($action=="check_color_id")
{   echo load_html_head_contents("PO Info", "../../../", 1, 1,'','','');
    extract($_REQUEST);
        ?>
<script type="text/javascript">
  function js_set_value(id)
      { //alert(id);
    document.getElementById('selected_id').value=id;
    parent.emailwindow.hide();
      }

</script>
<input type="hidden" id="selected_id" name="selected_id" /> 
<?
    $sql="select id, color_name from lib_color where is_deleted=0 and status_active=1 order by id";
    $arr=array(1=>$color_library);
    echo  create_list_view("list_view", "ID,Color Name", "50,200","300","300",0, $sql, "js_set_value", "id,color_name", "", 1, "0,0", $arr , "id,color_name", "",'setFilterGrid("list_view",-1);','0') ;
exit(); 
}
//popup for booking number
if($action=="bookingnumbershow")
{
    echo load_html_head_contents("Booking Info", "../../../", 1, 1,'','','');
    extract($_REQUEST);
    ?>
    <script>
      function js_set_value(id)
      { 
        document.getElementById('selected_id').value=id;
        parent.emailwindow.hide();
      }
    </script>
    </head>
    <body>
    <div align="center">
        <form name="styleRef_form" id="styleRef_form">
        <fieldset style="width:500px;">
            <table width="96" cellspacing="0" cellpadding="0" border="1" rules="all" align="center" class="rpt_table" id="tbl_list">
                <thead>
                    <th>Booking No</th>
                    <th>Batch No</th>
                    <th><input type="reset" name="button" class="formbutton" value="Reset" style="width:100px;" onClick="reset_form('styleRef_form','search_div','','','','');"></th>
                </thead>
                <tbody>
                    <tr>
                        <td align="center"> 
                            <input type="text" style="width:130px" class="text_boxes" name="txt_booking_no" id="txt_booking_no" />
                        </td> 
                        <td align="center">
                            <input type="text" style="width:130px" class="text_boxes" name="txt_batch_no" id="txt_batch_no" />
                        </td>                 
                        <td align="center">
                            <input type="button" name="button" class="formbutton" value="Show" onClick="show_list_view ('<? echo $company_name; ?>'+'**'+document.getElementById('txt_batch_no').value+'**'+document.getElementById('txt_booking_no').value, 'bookingnumbershow_search_list_view', 'search_div', 'fabric_finishing_report_controller', 'setFilterGrid(\'list_view\',-1)');" style="width:100px;" />
                    </td>
                    </tr>
                </tbody>
            </table>
        </fieldset>
            <div style="margin-top:15px" id="search_div"></div>
    </form>


    </div>
    </body>           
    <script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
    </html>
    <?
    exit(); 
}


if($action=="bookingnumbershow_search_list_view")
{
    //echo load_html_head_contents("PO Info", "../../../", 1, 1,'','','');
    extract($_REQUEST);
    list($company_name,$txt_batch_no,$txt_booking_no)=explode('**',$data);
    if($txt_batch_no!=''){
        $search_con=" and batch_no like('%$txt_batch_no')"; 
    }
    if($txt_booking_no!=''){
        $search_con .=" and booking_no like('%$txt_booking_no%')";  
    }

    
?>
<input type="hidden" id="selected_id" name="selected_id" /> 
<? if($db_type==0) $field_grpby=" GROUP BY batch_no"; 
else if($db_type==2) $field_grpby="GROUP BY batch_no,id,batch_no,batch_for,booking_no_id,booking_no,color_id,batch_weight";
$sql="select booking_no_id,booking_no,batch_for,color_id,batch_weight from pro_batch_create_mst where company_id=$company_name and is_deleted = 0 $search_con $field_grpby "; 
$arr=array(1=>$color_library);
    echo  create_list_view("list_view", "Batch no,Color,Booking no, Batch for,Batch weight ", "100,100,100,100,170","620","290",0, $sql, "js_set_value", "booking_no_id,booking_no", "", 1, "0,color_id,0,0,0", $arr , "batch_no,color_id,booking_no,batch_for,batch_weight", "",'','0') ;
    exit();
}//bookingnumbershow;


if($action=="batchnumbershow")
{
    echo load_html_head_contents("Batch Info", "../../../", 1, 1,'','','');
    extract($_REQUEST);
    ?>
    <script>
      function js_set_value(id)
      { 
        document.getElementById('selected_id').value=id;
        parent.emailwindow.hide();
      }
    </script>
    </head>
    <body>
    <div align="center">
        <form name="styleRef_form" id="styleRef_form">
        <fieldset style="width:500px;">
            <table width="96" cellspacing="0" cellpadding="0" border="1" rules="all" align="center" class="rpt_table" id="tbl_list">
                <thead>
                    <th>Batch No</th>
                    <th>Booking No</th>
                    <th><input type="reset" name="button" class="formbutton" value="Reset" style="width:100px;" onClick="reset_form('styleRef_form','search_div','','','','');"></th>
                </thead>
                <tbody>
                    <tr>
                        <td align="center">
                            <input type="text" style="width:130px" class="text_boxes" name="txt_batch_no" id="txt_batch_no" />
                        </td>                 
                        <td align="center"> 
                            <input type="text" style="width:130px" class="text_boxes" name="txt_booking_no" id="txt_booking_no" />
                        </td>     
                        <td align="center">
                            <input type="button" name="button" class="formbutton" value="Show" onClick="show_list_view ('<? echo $company_name; ?>'+'**'+document.getElementById('txt_batch_no').value+'**'+document.getElementById('txt_booking_no').value, 'batchnumbershow_search_list_view', 'search_div', 'fabric_finishing_report_controller', 'setFilterGrid(\'list_view\',-1)');" style="width:100px;" />
                    </td>
                    </tr>
                </tbody>
            </table>
        </fieldset>
            <div style="margin-top:15px" id="search_div"></div>
    </form>


    </div>
    </body>           
    <script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
    </html>
    <?
    exit(); 
}


if($action=="batchnumbershow_search_list_view")
{
    //echo load_html_head_contents("PO Info", "../../../", 1, 1,'','','');
    extract($_REQUEST);
    list($company_name,$txt_batch_no,$txt_booking_no)=explode('**',$data);
    if($txt_batch_no!=''){
        $search_con=" and batch_no like('%$txt_batch_no')"; 
    }
    if($txt_booking_no!=''){
        $search_con .=" and booking_no like('%$txt_booking_no%')";  
    }
?>
<input type="hidden" id="selected_id" name="selected_id" /> 
<? if($db_type==0) $field_grpby=" GROUP BY batch_no"; 
else if($db_type==2) $field_grpby="GROUP BY batch_no,id,batch_no,batch_for,booking_no,color_id,batch_weight";
$sql="select id,batch_no,batch_for,booking_no,color_id,batch_weight from pro_batch_create_mst where company_id=$company_name and is_deleted = 0 $search_con $field_grpby "; 
$arr=array(1=>$color_library);
    echo  create_list_view("list_view", "Batch no,Color,Booking no, Batch for,Batch weight ", "100,100,100,100,170","620","290",0, $sql, "js_set_value", "id,batch_no", "", 1, "0,color_id,0,0,0", $arr , "batch_no,color_id,booking_no,batch_for,batch_weight", "",'','0') ;
    exit();
}//batchnumbershow;


if($action=="roll_maintained_data")
{
    if($db_type==2) $grp_cond="listagg(page_upto_id,',') within group (order by page_upto_id) as page_upto_id";
    else  $grp_cond="group_concat(page_upto_id,',') within group (order by page_upto_id) as page_upto_id";
    $roll_maintained=return_field_value("fabric_roll_level","variable_settings_production","company_name ='$data' and item_category_id=50 and variable_list=3 and is_deleted=0 and status_active=1");
    $page_upto_id=return_field_value("page_upto_id","variable_settings_production","company_name =$data and item_category_id=50 and variable_list=3 and is_deleted=0 and status_active=1","page_upto_id");
    if($roll_maintained=="" || $roll_maintained==2) $roll_maintained=0; else $roll_maintained=$roll_maintained;
    echo "document.getElementById('roll_maintained').value              = '".$roll_maintained."';\n";
    echo "document.getElementById('page_upto').value                = '".$page_upto_id."';\n";
    
    exit(); 
}

if($action=="load_drop_down_buyer")
{ 
    
    echo create_drop_down( "cbo_buyer_name", 110, "select buy.id,buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company='$data' $buyer_cond and buy.id in (select  buyer_id from  lib_buyer_party_type where party_type in (1,3,21,90))  order by buyer_name","id,buyer_name", 1, "-- Select Buyer --", 0, "" );
    exit();
}//cbo_buyer_name_td


if($action=="jobnumbershow")
{
    echo load_html_head_contents("Job Info", "../../../", 1, 1,'','','');
    extract($_REQUEST);
    ?>
    
    <script>
      function js_set_value(id)
      {
        document.getElementById('selected_id').value=id;
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
                    <th><input type="reset" name="button" class="formbutton" value="Reset" style="width:100px;" onClick="reset_form('styleRef_form','search_div','','','','');"></th>
                    <input type="hidden" name="hide_job_id" id="hide_job_id" value="" />
                    <input type="hidden" name="hide_job_no" id="hide_job_no" value="" />
                </thead>
                <tbody>
                    <tr>
                        <td align="center">
                             <? 
                                echo create_drop_down( "cbo_buyer_name", 140, "select buy.id, buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company=$company_id $buyer_cond and buy.id in (select buyer_id from lib_buyer_party_type where party_type in (1,3,21,90)) order by buy.buyer_name","id,buyer_name",1, "-- All Buyer--",$cbo_buyer_name,"",0 );
                            ?>
                        </td>                 
                        <td align="center"> 
                        <?
                            $search_by_arr=array(1=>"Job No",2=>"Order No");
                            $dd="change_search_event(this.value, '0*0', '0*0', '../../') ";                         
                            echo create_drop_down( "cbo_search_by", 130, $search_by_arr,"",0, "--Select--", "",$dd,0 );
                        ?>
                        </td>     
                        <td align="center" id="search_by_td">               
                            <input type="text" style="width:130px" class="text_boxes" name="txt_search_common" id="txt_search_common" />    
                        </td>   
                        <td align="center">
                            <input type="button" name="button" class="formbutton" value="Show" onClick="show_list_view ('<? echo $company_id; ?>'+'**'+'<? echo $year; ?>'+'**'+document.getElementById('cbo_buyer_name').value+'**'+document.getElementById('cbo_search_by').value+'**'+document.getElementById('txt_search_common').value, 'jobnumbershow_search_list_view', 'search_div', 'fabric_finishing_report_controller', 'setFilterGrid(\'table_body2\',-1)');" style="width:100px;" />
                    </td>
                    </tr>
                </tbody>
            </table>
        </fieldset>
        <div style="margin-top:15px" id="search_div"></div>
    </form>
    </div>
    </body>           
    <script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
    </html>
    <?
    exit(); 
}

if($action=="jobnumbershow_search_list_view")
{
    //echo load_html_head_contents("PO Info", "../../../", 1, 1,'','','');
    extract($_REQUEST);
    list($company_id,$year,$cbo_buyer_name,$search_type,$search_value)=explode('**',$data);
    
?>
<input type="hidden" id="selected_id" name="selected_id" /> 
<?

if($search_type==1 && $search_value!=''){
    $search_con=" and a.job_no like('%$search_value')"; 
}
else if($search_type==2 && $search_value!=''){
    $search_con=" and b.po_number like('%$search_value%')"; 
}


$year_job = str_replace("'","",$year);
if($db_type==0) $year_field_by="and YEAR(a.insert_date)"; 
else if($db_type==2) $year_field_by=" and to_char(a.insert_date,'YYYY')";
if($db_type==0) $year_field="SUBSTRING_INDEX(a.insert_date, '-', 1) as year"; 
else if($db_type==2) $year_field="to_char(a.insert_date,'YYYY') as year";
if($db_type==0) $field_grpby="GROUP BY a.job_no order by b.id desc"; 
else if($db_type==2) $field_grpby=" GROUP BY a.job_no,a.id,a.buyer_name,a.style_ref_no,a.gmts_item_id,b.po_number,a.job_no_prefix_num,a.insert_date,b.id order by b.id,a.job_no_prefix_num  desc ";
if(trim($year)!=0) $year_cond=" $year_field_by=$year_job"; else $year_cond="";
$cbo_buyer_name=($cbo_buyer_name==0)?"%%" : "%$cbo_buyer_name%";
$sql="select a.id,a.buyer_name,a.style_ref_no,a.gmts_item_id,b.po_number,a.job_no_prefix_num as job_prefix,$year_field from wo_po_details_master a,wo_po_break_down b where b.job_no_mst=a.job_no and a.company_name=$company_id and a.buyer_name like '%$cbo_buyer_name%' $search_con $year_cond and a.is_deleted = 0 $field_grpby ";
$buyer=return_library_array( "select id,buyer_name from lib_buyer", "id", "buyer_name"  );
?>
<table width="580" border="1" rules="all" class="rpt_table" align="left">
    <thead>
    <tr>
        <th width="35">SL</th>
        <th width="100">Po number</th>
        <th width="100">Job no</th>
        <th width="50">Year</th>
        <th width="80">Buyer</th>
        <th width="100">Style</th>
        <th>Item Name</th>
    </tr>
   </thead>
</table>
<div style="max-height:300px; overflow-y:scroll; width:600px; float:left">
<table id="table_body2" width="580" border="1" rules="all" class="rpt_table" align="left">
 <? $rows=sql_select($sql);
     $i=1;
 foreach($rows as $data)
 {
      if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
  ?>
    <tr bgcolor="<? echo  $bgcolor; ?>" onClick="js_set_value('<? echo $data[csf('job_prefix')]; ?>')" style="cursor:pointer;">
        <td width="35"><? echo $i; ?></td>
        <td width="100"><p><? echo $data[csf('po_number')]; ?></p></td>
        <td width="100"><p><? echo $data[csf('job_prefix')]; ?></p></td>
        <td width="50"><p><? echo $data[csf('year')]; ?></p></td>
        <td width="80"><p><? echo $buyer[$data[csf('buyer_name')]]; ?></p></td>
        <td width="100"><p><? echo $data[csf('style_ref_no')]; ?></p></td>
        <td><p><? 
        $itemid=explode(",",$data[csf('gmts_item_id')]);
        foreach($itemid as $index=>$id){
        echo ($itemid[$index]==end($itemid))? $garments_item[$id] : $garments_item[$id].', ';
        }
        ?></p></td>
    </tr>
    <? $i++; } ?>
</table>
</div>
<?
    exit();
}//JobNumberShow

if($action=="order_number_popup")
{
    echo load_html_head_contents("Job Info", "../../../", 1, 1,'','','');
    extract($_REQUEST);
    ?>
    
    <script>
      function js_set_value(id)
      { 
        document.getElementById('selected_id').value=id;
        parent.emailwindow.hide();
      }
    </script>
    </head>
    <body>
    <div align="center">
        <form name="styleRef_form" id="styleRef_form">
        <fieldset>
            <table cellspacing="0" cellpadding="0" border="1" rules="all" align="center" class="rpt_table" id="tbl_list">
                <thead>
                    <th>Buyer</th>
                    <th>Search By</th>
                    <th id="search_by_td_up" width="130">Please Enter Job No</th>
                    <th><input type="reset" name="button" class="formbutton" value="Reset" style="width:100px;" onClick="reset_form('styleRef_form','search_div','','','','');"></th>
                    <input type="hidden" name="hide_job_id" id="hide_job_id" value="" />
                    <input type="hidden" name="hide_job_no" id="hide_job_no" value="" />
                </thead>
                <tbody>
                    <tr>
                        <td align="center">
                             <? 
                                echo create_drop_down( "cbo_buyer_name", 110, "select buy.id, buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company=$company_name $buyer_cond and buy.id in (select buyer_id from lib_buyer_party_type where party_type in (1,3,21,90)) order by buy.buyer_name","id,buyer_name",1, "-- All Buyer--",$buyer_name,"",0 );
                            ?>
                        </td>                 
                        <td align="center"> 
                        <?
                            $search_by_arr=array(1=>"Job No",2=>"Order No");
                            $dd="change_search_event(this.value, '0*0', '0*0', '../../') ";                         
                            echo create_drop_down( "cbo_search_by", 110, $search_by_arr,"",0, "--Select--", "",$dd,0 );
                        ?>
                        </td>     
                        <td align="center" id="search_by_td">               
                            <input type="text" style="width:130px" class="text_boxes" name="txt_search_common" id="txt_search_common" />    
                        </td>   
                        <td align="center">
                            <input type="button" name="button" class="formbutton" value="Show" onClick="show_list_view ('<? echo $company_name; ?>'+'**'+'<? echo $year; ?>'+'**'+document.getElementById('cbo_buyer_name').value+'**'+document.getElementById('cbo_search_by').value+'**'+document.getElementById('txt_search_common').value, 'order_number_popup_search_list_view', 'search_div', 'fabric_finishing_report_controller', 'setFilterGrid(\'table_body2\',-1)');" style="width:100px;" />
                    </td>
                    </tr>
                </tbody>
            </table>
        </fieldset>
        <div style="margin-top:15px" id="search_div"></div>
    </form>
    </div>
    </body>           
    <script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
    </html>
    <?
    exit(); 
}



if($action=="order_number_popup_search_list_view")
{
    //echo load_html_head_contents("PO Info", "../../../", 1, 1,'','','');
    extract($_REQUEST);
    list($company_name,$year,$buyer_name,$search_type,$search_value)=explode('**',$data);
?>
<input type="hidden" id="selected_id" name="selected_id" /> 
<?
$buyer = str_replace("'","",$buyer_name);
$year = str_replace("'","",$year);
$buyer = str_replace("'","",$buyer_name);
$year_job = str_replace("'","",$year);

if($search_type==1 && $search_value!=''){
    $search_con=" and b.job_no like('%$search_value')"; 
}
else if($search_type==2 && $search_value!=''){
    $search_con=" and a.po_number like('%$search_value%')"; 
}

if($db_type==0) $year_field_by=" and YEAR(b.insert_date)"; 
else if($db_type==2) $year_field_by=" and to_char(b.insert_date,'YYYY')";
if($db_type==0) $year_field="SUBSTRING_INDEX(b.insert_date, '-', 1) as year "; 
else if($db_type==2) $year_field="to_char(b.insert_date,'YYYY') as year";
if ($company_name==0) $company=""; else $company=" and b.company_name=$company_name";
if(trim($year)!=0) $year_cond=" $year_field_by=$year"; else $year_cond="";
//echo $buyer;die;
if ($buyer==0) $buyername=""; else $buyername=" and b.buyer_name=$buyer";//$cbo_buyer_name=($cbo_buyer_name==0)?"%%" : "%$cbo_buyer_name%";
$sql = "select distinct a.id,b.job_no,a.po_number,b.company_name,b.buyer_name,b.job_no_prefix_num as job_prefix,$year_field from wo_po_break_down a, wo_po_details_master b where a.job_no_mst=b.job_no $company $search_con $buyername $year_cond order by a.id asc"; 
$buyer=return_library_array( "select id,buyer_name from lib_buyer", "id", "buyer_name"  );
?>
<table width="490" border="1" rules="all" class="rpt_table" align="left">
    <thead>
        <tr>
        <th width="30">SL</th>
        <th width="80">Order Number</th>
        <th width="50">Job no</th>
        <th width="80">Buyer</th>
        <th width="40">Year</th>
        </tr>
   </thead>
</table>
<div style="max-height:300px; overflow:auto; clear:both">
<table id="table_body2" width="490" border="1" rules="all" class="rpt_table" align="left">
 <? $rows=sql_select($sql);
     $i=1;
 foreach($rows as $data)
 { if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
  ?>
    <tr bgcolor="<? echo $bgcolor; ?>" onClick="js_set_value('<? echo $data[csf('po_number')]; ?>')" style="cursor:pointer;">
        <td width="30"><? echo $i; ?></td>
        <td width="80"><p><? echo $data[csf('po_number')]; ?></p></td>
        <td width="50"><p><? echo $data[csf('job_prefix')]; ?></p></td>
        <td width="80"><p><? echo $buyer[$data[csf('buyer_name')]]; ?></p></td>
        <td width="40" align="center"><p><? echo $data[csf('year')]; ?></p></td>
    </tr>
    <? $i++; } ?>
</table>
</div>
<?
    exit();
}
if($action=="fabric_finishing_report")
{   
    ?>
    <!-- <div style="text-align: center;color: red;font-weight: bold;font-size: 20px;">This page is under QC. Please be patience.</div> -->
    <?
    $process = array( &$_POST );
    extract(check_magic_quote_gpc( $process )); 
    if($db_type==0) $year_field_by="and YEAR(a.insert_date)"; 
    else if($db_type==2) $year_field_by=" and to_char(a.insert_date,'YYYY')";
    if($db_type==0) $field_concat="concat(machine_no,'-',brand) as machine_name"; 
    else if($db_type==2) $field_concat="machine_no || '-' || brand as machine_name";
    // machine_no || '-' || brand as machine_name
    $company = str_replace("'","",$cbo_company_name);
    $working_company = str_replace("'","",$cbo_working_company_id);
    $cbo_location_id = str_replace("'","",$cbo_location_id);
    $cbo_floor_id = str_replace("'","",$cbo_floor_id);
     $report_type= str_replace("'","",$report_type);
    if($company!=0) $company_cond="and a.company_id=$company";else $company_cond="";
    if($working_company!=0) $working_company_cond="and f.service_company=$working_company";else $working_company_cond="";

    $floor_no_cond="";
    if($cbo_location_id!=0) 
    {
        if($cbo_floor_id!=0)
        {
            $floor_no_cond=" and f.floor_id='$cbo_floor_id'";
        }
        else
        {
            $sql_floor = sql_select("select id from lib_prod_floor where production_process in (3,4) and company_id ='$working_company' and location_id=$cbo_location_id and status_active =1 and is_deleted=0 group by id"); //production_process =3 and

            foreach ($sql_floor as $val) 
            {
                $floor_array[$val[csf("id")]] = $val[csf("id")];
            }

            $floor_array = array_filter($floor_array);
            $floor_no_cond=" and f.floor_id in (".implode(',', $floor_array).")";
        }
    }

    $buyer = str_replace("'","",$cbo_buyer_name);
    $job_number = str_replace("'","",$job_number);
    $job_number_id = str_replace("'","",$job_number_show);
    $batch_no = str_replace("'","",$batch_number_show);
    $booking_no = str_replace("'","",$booking_number_show);
    $color = str_replace("'","",$txt_color);
    $cbo_shift = str_replace("'","",$cbo_shift);
    $txt_file_no = str_replace("'","",$txt_file_no);
    $txt_ref_no = str_replace("'","",$txt_ref_no);
    
    $page_upto = str_replace("'","",$page_upto);
    $roll_maintained = str_replace("'","",$roll_maintained);
    //echo $roll_maintained;die;
    $batch_number_hidden = str_replace("'","",$batch_number);
    $booking_number_hidden = str_replace("'","",$booking_number);
    $ext_num = str_replace("'","",$txt_ext_no);
    $hidden_ext = str_replace("'","",$hidden_ext_no);
    $txt_order = str_replace("'","",$order_no);
    $hidden_order = str_replace("'","",$hidden_order_no);
    $cbo_type = str_replace("'","",$cbo_type);
    $cbo_group_by = str_replace("'","",$cbo_group_by);
    $year = str_replace("'","",$cbo_year);
    //echo $cbo_type;die;
    $txt_date_from = str_replace("'","",$txt_date_from);
    $txt_date_to = str_replace("'","",$txt_date_to);
    if($job_number_id!="") $jobdata="and d.job_no_prefix_num='".$job_number_id ."'";else $jobdata="";
    //$jobdata=($job_number_id )? " and d.job_no_prefix_num='".$job_number_id ."'" : '';
    if($buyer!=0) $buyerdata="and d.buyer_name=$buyer";else $buyerdata="";
    //$buyerdata=($buyer)?' and d.buyer_name='.$buyer : '';
    //for non order booking sample
    if($buyer!=0) $buyerdata_non_ord="and j.buyer_id=$buyer";else $buyerdata_non_ord="";
    if($batch_no!="") $batch_num="and a.batch_no='".$batch_no."'";else $batch_num="";
     if($batch_no!="") $batch_num2="and f.batch_no='".$batch_no."'";else $batch_num2="";
    if($booking_no!="") $booking_num="and a.booking_no='".$booking_no."'";else $booking_num="";
    //$batch_num=($batch_no)?" and a.batch_no='".$batch_no."'" : '';
    if(trim($ext_no)!="") $ext_no_search="%".trim($ext_no)."%"; else $ext_no_search="%%";
    if ($txt_order=="") $order_no=""; else $order_no="  and c.po_number='$txt_order'";
    if ($color=="") $color_name=""; else $color_name="  and g.color_name='$color'";
    if ($cbo_shift==0) $shift_cond=""; else $shift_cond="  and f.shift_name='$cbo_shift'";
    if ($txt_file_no=="") $file_cond=""; else $file_cond="  and c.file_no=$txt_file_no";
    if ($txt_ref_no=="") $ref_cond=""; else $ref_cond="  and c.grouping='$txt_ref_no'";
    //echo $order_no;die;
    if($color!='')
    {
        $color_id = return_field_value("distinct(a.id) as id", "lib_color a ", "a.color_name='$color'", "id");
    }
    //echo $color_id.'dd';die;
    if($color_id!='') $color=$color_id;else $color="";
    if ($color=="") $color_name=""; else $color_name="  and a.color_id=$color"; 
    
    if ($ext_num=="") $ext_no=""; else $ext_no="  and a.extention_no=$ext_num ";
    if(trim($year)!=0) $year_cond=" $year_field_by=$year"; else $year_cond="";
    if(str_replace("'","",$cbo_group_by)==1)
    {
        $order_by="order by f.floor_id, a.batch_no";
        $order_by2="order by floor_id,process_start_date,start_minutes,start_hours";
    }
    
    else if(str_replace("'","",$cbo_group_by)==2)
    {
        $order_by="order by f.machine_id, a.batch_no";
        $order_by2="order by machine_id,process_start_date,start_minutes,start_hours";
    }
    else
    {
        if($cbo_shift==0)
        {
            $order_by="order by f.floor_id,f.process_start_date,f.start_minutes,f.start_hours ASC";
            $order_by2="order by machine_id,process_start_date,start_minutes,start_hours ASC";
            //$order_by3="order by f.floor_id,f.process_start_date,f.start_minutes,f.start_hours ASC";
            //f.process_start_date,f.production_date
        }
        else
        {
            $order_by="order by f.machine_id,f.floor_id,a.batch_no";
            $order_by2="order by machine_id,process_start_date,batch_no";
        }
        
        /*$order_by="order by f.machine_id,f.floor_id,a.batch_no";
        $order_by2="order by machine_id,floor_id,batch_no";*/
    }
    //echo $cbo_group_by;
    if($txt_date_from && $txt_date_to)
    {
        if($db_type==0)
        {
            $date_from=change_date_format($txt_date_from,'yyyy-mm-dd');
            $date_to=change_date_format($txt_date_to,'yyyy-mm-dd');
            $dates_com="and  f.process_end_date BETWEEN '$date_from' AND '$date_to'";
            $dates_com2="and  a.process_end_date BETWEEN '$date_from' AND '$date_to'";
        }
        if($db_type==2)
        {
            $date_from=change_date_format($txt_date_from,'','',1);
            $date_to=change_date_format($txt_date_to,'','',1);
            $dates_com="and  f.process_end_date BETWEEN '$date_from' AND '$date_to'";
            $dates_com2="and  a.process_end_date BETWEEN '$date_from' AND '$date_to'";
        }
    }
    $machine_arr=return_library_array( "select id,$field_concat from  lib_machine_name",'id','machine_name');
    
    // if($cbo_type!=0){$rollID="b.roll_id,";}else{$rollID="";}
    $rollID="b.roll_id,";
    $sql_batch="SELECT a.insert_date,a.batch_id,b.roll_no as roll_no_heat,b.prod_id,b.width_dia_type as dia_type,a.production_date,a.process_end_date,b.barcode_no,$rollID 
    (CASE WHEN b.entry_page=32 THEN b.production_qty ELSE 0 END) AS heat_qty,
    (CASE WHEN b.entry_page=30 THEN b.production_qty ELSE 0 END) AS sliting_qty,
    (CASE WHEN b.entry_page=31 THEN b.production_qty ELSE 0 END) AS drying_qty,
    (CASE WHEN b.entry_page=48 AND a.re_stenter_no=0 THEN b.production_qty ELSE 0 END) AS stenter_qty,
    (CASE WHEN b.entry_page=33 AND a.re_stenter_no=0 THEN b.production_qty ELSE 0 END) AS compact_qty,
    (CASE WHEN b.entry_page=33 AND a.re_stenter_no=0 THEN b.batch_qty ELSE 0 END) AS batch_compact_qty,
    (CASE WHEN b.entry_page=48 AND a.re_stenter_no!=0 THEN b.production_qty ELSE 0 END) AS re_stenter_qty,
    (CASE WHEN b.entry_page=33 AND a.re_stenter_no!=0 THEN b.production_qty ELSE 0 END) AS re_compact_qty,
    (CASE WHEN b.entry_page=34 THEN b.production_qty ELSE 0 END) AS special_qty,
    (CASE WHEN b.entry_page=35 and  a.load_unload_id in(2) THEN b.production_qty ELSE 0 END) AS unload_qty
     from pro_fab_subprocess a, pro_fab_subprocess_dtls b where a.id=b.mst_id and a.entry_form in(32,30,35,31,48,33,34) and b.entry_page in(32,30,35,31,48,33,34) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $batch_num $dates_com2 ";
    // echo $sql_batch;die();
    $batch_data=sql_select($sql_batch);
  
  	
    $batch_prod_qty_arr=array();$batch_prod_qty_arr2=array();

    if($report_type==1)
    {
        //echo $roll_maintained.'='.$cbo_type.'='.$page_upto.'<br>';
        //if ($roll_maintained==1 && $cbo_type!=0) 
        if(($page_upto==7 || $page_upto>7) && $roll_maintained==1)
        {
            
            foreach($batch_data as $row)
            { 
                $insert_date = "'".$row[csf('insert_date')]."'";     
                if ($roll_maintained==1 && $cbo_type==1) {
                $batch_prod_qty_heat_arr[$row[csf('batch_id')]][$row[csf('prod_id')]][$row[csf('dia_type')]][$row[csf('roll_id')]][$row[csf('roll_no_heat')]][$insert_date]['heat']+=$row[csf('heat_qty')];
                }
                
                $batch_prod_qty_arr[$row[csf('batch_id')]][$row[csf('prod_id')]][$row[csf('dia_type')]][$row[csf('roll_id')]][$row[csf('barcode_no')]][$insert_date]['sliting']+=$row[csf('sliting_qty')];
                $batch_prod_qty_arr[$row[csf('batch_id')]][$row[csf('prod_id')]][$row[csf('dia_type')]][$row[csf('roll_id')]][$row[csf('barcode_no')]][$insert_date]['drying']+=$row[csf('drying_qty')];
                $batch_prod_qty_arr[$row[csf('batch_id')]][$row[csf('prod_id')]][$row[csf('dia_type')]][$row[csf('roll_id')]][$row[csf('barcode_no')]][$insert_date]['stenter']+=$row[csf('stenter_qty')];
                if($page_upto==7 || $page_upto>7)
                {
                    if($row[csf('compact_qty')]>0)
                    {
                        $batch_prod_qty_arr[$row[csf('batch_id')]][$row[csf('prod_id')]][$row[csf('dia_type')]][$row[csf('roll_id')]][$row[csf('barcode_no')]][$insert_date]['compact']+=$row[csf('compact_qty')];   
                    }
                }
                else
                {
                    //$batch_prod_qty_arr3[$row[csf('batch_id')]][$row[csf('prod_id')]][$row[csf('dia_type')]]['compact']+=$row[csf('compact_qty')];   
                    if($row[csf('compact_qty')]>0)
                    {
                     $batch_prod_qty_arr3[$row[csf('batch_id')]][$row[csf('prod_id')]][$row[csf('dia_type')]][$row[csf('production_date')]][$insert_date]['compact']+=$row[csf('compact_qty')];
                    }
                    if($row[csf('batch_compact_qty')]>0)
                    {
                        $batch_prod_qty_arr3[$row[csf('batch_id')]][$row[csf('prod_id')]][$row[csf('dia_type')]][$row[csf('production_date')]][$insert_date]['batch_compact_qty']+=$row[csf('batch_compact_qty')];
                    }
                }
                //$batch_prod_qty_arr3[$row[csf('batch_id')]][$row[csf('prod_id')]][$row[csf('dia_type')]][$row[csf('production_date')]]['compact']+=$row[csf('compact_qty')];
                
                $batch_prod_qty_arr[$row[csf('batch_id')]][$row[csf('prod_id')]][$row[csf('dia_type')]][$row[csf('roll_id')]][$row[csf('barcode_no')]][$insert_date]['special']+=$row[csf('special_qty')];
                $batch_prod_qty_arr[$row[csf('batch_id')]][$row[csf('prod_id')]][$row[csf('dia_type')]][$insert_date]['unload']+=$row[csf('unload_qty')];
                
                $batch_prod_qty_arr2[$row[csf('batch_id')]][$row[csf('prod_id')]][$row[csf('dia_type')]][$insert_date]['stenter']+=$row[csf('re_stenter_qty')];
                if($row[csf('re_compact_qty')]>0){
                $batch_prod_qty_arr2[$row[csf('batch_id')]][$row[csf('prod_id')]][$row[csf('dia_type')]][$insert_date]['compact']+=$row[csf('re_compact_qty')];
               }
                 $batch_prod_qty_arr2[$row[csf('batch_id')]][$row[csf('prod_id')]][$row[csf('dia_type')]][$insert_date]['heat']+=$row[csf('heat_qty')];

            }
            //echo '<pre>';print_r($batch_prod_qty_heat_arr);
        }
        else
        {
            foreach($batch_data as $row)
            {  
                $insert_date = "'".$row[csf('insert_date')]."'";  
              if($row[csf('heat_qty')]>0){
                $batch_prod_qty_arr[$row[csf('batch_id')]][$row[csf('prod_id')]][$row[csf('dia_type')]][$insert_date]['heat']+=$row[csf('heat_qty')];
              }
               if($row[csf('sliting_qty')]>0){
                $batch_prod_qty_arr[$row[csf('batch_id')]][$row[csf('prod_id')]][$row[csf('dia_type')]][$insert_date]['sliting']+=$row[csf('sliting_qty')];
               }
                  if($row[csf('drying_qty')]>0){
                $batch_prod_qty_arr[$row[csf('batch_id')]][$row[csf('prod_id')]][$row[csf('dia_type')]][$insert_date]['drying']+=$row[csf('drying_qty')];
                $batch_prod_qty_arr3[$row[csf('batch_id')]][$row[csf('prod_id')]][$row[csf('dia_type')]][$row[csf('production_date')]][$insert_date]['drying']+=$row[csf('drying_qty')];
                  }
                    if($row[csf('stenter_qty')]>0){
                $batch_prod_qty_arr[$row[csf('batch_id')]][$row[csf('prod_id')]][$row[csf('dia_type')]][$insert_date]['stenter']+=$row[csf('stenter_qty')];
                    }
                if($row[csf('compact_qty')]>0){
                $batch_prod_qty_arr[$row[csf('batch_id')]][$row[csf('prod_id')]][$row[csf('dia_type')]][$insert_date]['compact']+=$row[csf('compact_qty')];
                $batch_prod_qty_arr3[$row[csf('batch_id')]][$row[csf('prod_id')]][$row[csf('dia_type')]][$row[csf('process_end_date')]][$insert_date]['compact']+=$row[csf('compact_qty')];
                }
                if($row[csf('batch_compact_qty')]>0){
                $batch_prod_qty_arr3[$row[csf('batch_id')]][$row[csf('prod_id')]][$row[csf('dia_type')]][$row[csf('process_end_date')]][$insert_date]['batch_compact_qty']+=$row[csf('batch_compact_qty')]; }
                 if($row[csf('special_qty')]>0){
                $batch_prod_qty_arr[$row[csf('batch_id')]][$row[csf('prod_id')]][$row[csf('dia_type')]][$insert_date]['special']+=$row[csf('special_qty')];
                 }
                  if($row[csf('unload_qty')]>0){
                $batch_prod_qty_arr[$row[csf('batch_id')]][$row[csf('prod_id')]][$row[csf('dia_type')]][$insert_date]['unload']+=$row[csf('unload_qty')];
                  }
                  if($row[csf('re_stenter_qty')]>0){
                $batch_prod_qty_arr2[$row[csf('batch_id')]][$row[csf('prod_id')]][$row[csf('dia_type')]][$insert_date]['stenter']+=$row[csf('re_stenter_qty')];
                  }
                if($row[csf('re_compact_qty')]>0){
                $batch_prod_qty_arr2[$row[csf('batch_id')]][$row[csf('prod_id')]][$row[csf('dia_type')]][$insert_date]['compact']+=$row[csf('re_compact_qty')];
                }
            }
        }
        // echo '<pre>';print_r($batch_prod_qty_arr);die;
        
        unset($batch_data);
        //print_r($batch_prod_qty_arr[10445][137092][3]);
        //echo $batch_prod_qty_arr[10445][137092][3][515]['drying'];//10445=137092=3=515
        if($db_type==0) $group_conct="group_concat(distinct c.po_number ) AS po_number,group_concat(distinct b.po_id ) AS po_id";
        else if($db_type==2) $group_conct="listagg(cast(c.po_number as varchar2(4000)),',') within group (order by c.po_number) AS po_number,listagg(b.po_id ,',') within group (order by b.po_id) as po_id";
        //echo $page_upto.'=='.$roll_maintained;

        // echo $cbo_type;
        if($cbo_type==1)//  For Heat Setting 
        {
            if(($page_upto==1 || $page_upto>1) && $roll_maintained==1)
            {
                $heat_set=",b.roll_no as roll_no_heat, b.roll_id as roll_no,b.barcode_no";
                $heat_group=",b.roll_no,b.roll_id,b.barcode_no";
            } 
            else
            {
                $heat_set=",count(b.roll_no) as roll_no";
                $heat_group=""; 
            }
        }
        else if($cbo_type==6)//  For Dying... 
        {
            if(($page_upto==2 || $page_upto>2) && $roll_maintained==1)
            {
                            
                $dyeing_pro=", b.roll_id as roll_no,b.barcode_no";
                $dyeing_group=",b.roll_id,b.barcode_no";    
                
            }
            else
            {
                $dyeing_pro=",count(b.roll_no) as roll_no";
                $dyeing_group="";
            }
        }
        else if($cbo_type==2) //Sliting...
        {
            if(($page_upto==3 || $page_upto>3) && $roll_maintained==1)
            {
                $sliting_sq=", b.roll_id as roll_no,b.barcode_no";
                $sliting_group=",b.roll_id,b.barcode_no";   
            }
            else
            {
                $sliting_sq=",count(b.roll_no) as roll_no";
                $sliting_group="";  
            }
        }
        else if($cbo_type==9) //Stentering...
        {
            if(($page_upto==4 || $page_upto>4) && $roll_maintained==1)
            {
                $stenter=", b.roll_id as roll_no,b.barcode_no";
                $stenter_group=",b.roll_id,b.barcode_no";
            }
            else
            {
                $stenter=",count(b.roll_no) as roll_no";
                $stenter_group="";  
            }
        }
        else if($cbo_type==3) //Drying...
        {
            if(($page_upto==5 || $page_upto>5) && $roll_maintained==1)
            {
                $drying=", b.roll_id as roll_no,b.barcode_no";
                $drying_group=",b.roll_id,b.barcode_no";    
                $drying_group2=",roll_id,barcode_no";   
                
            }
            else
            {
                $drying=",count(b.roll_no) as roll_no";
                $drying_group="";
            }
        }
        else if($cbo_type==5)// Special Finish...
        {
            if(($page_upto==6 || $page_upto>6) && $roll_maintained==1)
            {
                $sp_finish=", b.roll_id as roll_no,b.barcode_no";
                $finish_group=",b.roll_id,b.barcode_no";    
                
            }
            else
            {
                $sp_finish=",count(b.roll_no) as roll_no";
                $finish_group="";
            }
        }
        else if($cbo_type==4)// Compacting...
        {
            if(($page_upto==7 || $page_upto>7) && $roll_maintained==1)
            {
                $compact=", b.roll_id as roll_no,b.barcode_no";
                $compact_group=",b.roll_id,b.barcode_no";
                
            }
            else
            {
                $compact=",count(b.roll_no) as roll_no";
                $compact_group="";  

            }
        }
            
            
            
        if($cbo_type==1)//  For Heat Setting 
        {
          //wo_non_ord_samp_booking_mst
           $sql="(select a.id,a.batch_no,a.booking_no,a.company_id,a.batch_date,a.batch_weight,a.color_id,a.booking_no_id,a.extention_no,a.batch_against,SUM(b.batch_qnty) AS batch_qnty,b.item_description,b.po_id,b.prod_id,b.width_dia_type,$group_conct,c.job_no_mst,d.job_no_prefix_num,d.buyer_name,f.process_end_date,f.process_start_date,f.production_date as end_date,f.end_hours,f.end_minutes,f.start_minutes,c.file_no,c.grouping,f.start_hours,f.shift_name,f.machine_id,f.floor_id,f.remarks,f.re_stenter_no,f.insert_date $heat_set  from pro_batch_create_dtls b,wo_po_break_down c,wo_po_details_master d,pro_batch_create_mst a, pro_fab_subprocess f where  a.id=b.mst_id and f.batch_id=a.id and f.batch_id=b.mst_id  and  b.po_id=c.id and d.job_no=c.job_no_mst and f.entry_form=32 and  a.batch_against in(1,2,3)  $company_cond $working_company_cond   $dates_com $jobdata $batch_num $booking_num $buyerdata $order_no $year_cond $shift_cond $file_cond $ref_cond $color_name $floor_no_cond and a.entry_form=0  and b.status_active=1 and b.is_deleted=0 and a.status_active=1 and a.is_deleted=0 GROUP BY a.id, a.batch_no,a.booking_no,a.company_id, a.batch_date, a.batch_weight, a.color_id, a.booking_no_id, a.extention_no, a.batch_against,b.item_description, b.po_id, b.prod_id, b.width_dia_type,c.job_no_mst, d.job_no_prefix_num, d.buyer_name,c.file_no,c.grouping ,f.process_end_date,f.process_start_date,f.production_date, f.start_minutes,f.start_hours,f.end_hours, f.end_minutes, f.shift_name,f.machine_id,f.floor_id, f.remarks,f.re_stenter_no,f.insert_date $heat_group)
            union
            (
                select a.id,a.batch_no,a.booking_no,a.company_id,a.batch_date,a.batch_weight,a.color_id,a.booking_no_id,a.extention_no,a.batch_against,SUM(b.batch_qnty) AS batch_qnty, b.item_description, b.po_id, b.prod_id, b.width_dia_type,null as po_number,null as po_id,null as job_no_mst,null as job_no_prefix_num, d.buyer_id as buyer_name, f.process_end_date,f.process_start_date,f.production_date as end_date, f.end_hours,f.end_minutes,f.start_minutes,null as file_no,null as grouping, f.start_hours, f.shift_name, f.machine_id,f.floor_id,f.remarks,f.re_stenter_no,f.insert_date $heat_set  from pro_batch_create_dtls b,pro_batch_create_mst a, pro_fab_subprocess f, wo_non_ord_samp_booking_mst d    where  a.id=b.mst_id and f.batch_id=a.id  and f.batch_id=b.mst_id and f.entry_form=32 and a.booking_without_order=1 and a.booking_no is not null and  a.batch_against in(3) $company_cond $working_company_cond  $dates_com $batch_num $booking_num $buyerdata_non_ord $order_no $shift_cond $color_name $floor_no_cond and a.entry_form=0  and  b.status_active=1 and b.is_deleted=0 and a.status_active=1 and a.is_deleted=0 and b.po_id=0 and a.booking_no_id = d.id GROUP BY a.id, a.batch_no,a.booking_no,a.company_id, a.batch_date, a.batch_weight, a.color_id, a.booking_no_id, a.extention_no, a.batch_against,b.item_description, b.po_id, b.prod_id, b.width_dia_type, d.buyer_id, f.process_end_date, f.process_start_date, f.production_date, f.start_minutes,f.start_hours,f.end_hours, f.end_minutes, f.shift_name, f.machine_id, f.floor_id, f.remarks,f.re_stenter_no,f.insert_date $heat_group
            ) $order_by2"; 
            
        }
        else if($cbo_type==2) // Slitting/Squeezing
        {
          
            $sql="(select a.company_id,a.id,a.batch_no,a.booking_no,a.batch_date,a.color_id,a.extention_no,a.batch_against,SUM(b.batch_qnty) AS batch_qnty,b.item_description,b.prod_id,b.width_dia_type,$group_conct,c.job_no_mst,d.job_no_prefix_num,d.buyer_name,f.process_end_date,f.process_start_date,c.file_no,c.grouping,f.production_date as end_date,f.start_minutes,f.start_hours,f.end_hours,f.end_minutes,f.shift_name,f.machine_id,f.floor_id,f.remarks,f.re_stenter_no,f.insert_date $sliting_sq  from pro_batch_create_dtls b,wo_po_break_down c,wo_po_details_master d,pro_batch_create_mst a,pro_fab_subprocess f where a.id=b.mst_id and b.po_id=c.id and d.job_no=c.job_no_mst  and  f.batch_id=a.id and  f.batch_id=b.mst_id and f.entry_form=30 and  a.batch_against in(1,2,3)  $company_cond $working_company_cond  $dates_com $jobdata $batch_num $booking_num $buyerdata $order_no $year_cond $shift_cond $file_cond $ref_cond $color_name $floor_no_cond and a.entry_form=0 and  b.status_active=1 and b.is_deleted=0 and a.status_active=1 and a.is_deleted=0  GROUP BY  b.item_description,a.company_id, a.id, a.batch_no,a.booking_no, a.batch_date,a.color_id, a.extention_no, a.batch_against,b.prod_id, b.width_dia_type,c.job_no_mst, d.job_no_prefix_num, d.buyer_name, f.process_end_date,f.process_start_date,f.production_date,f.start_minutes,f.start_hours, f.end_hours, f.end_minutes,f.shift_name, f.machine_id,f.floor_id,c.file_no,c.grouping, f.floor_id,f.remarks,f.re_stenter_no,f.insert_date $sliting_group)
            union
            (
                select a.company_id,a.id,a.batch_no,a.booking_no,a.batch_date,a.color_id,a.extention_no,a.batch_against,SUM(b.batch_qnty) AS batch_qnty,b.item_description,b.prod_id,b.width_dia_type,null as po_number,null as po_id,null as job_no_mst,null as job_no_prefix_num,d.buyer_id as buyer_name, f.process_end_date, f.process_start_date,null as file_no,null as grouping,f.production_date as end_date,f.start_minutes,f.start_hours,f.end_hours,f.end_minutes,f.shift_name,f.machine_id,f.floor_id,f.remarks,f.re_stenter_no,f.insert_date $sliting_sq  from pro_batch_create_dtls b,pro_batch_create_mst a,pro_fab_subprocess f, wo_non_ord_samp_booking_mst d  where a.id=b.mst_id  and f.batch_id=a.id and  f.batch_id=b.mst_id and a.entry_form=0 and f.entry_form=30 and a.batch_against in(3) $company_cond $working_company_cond  $dates_com $buyerdata_non_ord $batch_num $booking_num  $shift_cond $color_name $floor_no_cond and b.status_active=1 and b.is_deleted=0 and a.status_active=1 and a.is_deleted=0 and b.po_id=0 and a.booking_no_id = d.id GROUP BY b.item_description,a.company_id, a.id, a.batch_no,a.booking_no, a.batch_date,a.color_id, a.extention_no, a.batch_against,b.prod_id, b.width_dia_type, d.buyer_id, f.process_end_date, f.process_start_date, f.production_date,f.start_minutes,f.start_hours, f.end_hours, f.end_minutes, f.shift_name, f.machine_id,f.floor_id,f.floor_id,f.remarks,f.re_stenter_no,f.insert_date $sliting_group
            )
             $order_by2 "; 
        }
        
        else if($cbo_type==3)//  Drying / Stentering 
        {
            $sql="(
            select a.company_id,a.id,a.batch_no,a.booking_no,a.batch_date,a.color_id,a.extention_no,a.batch_against,SUM(b.batch_qnty) AS batch_qnty,b.item_description,b.prod_id,b.width_dia_type,$group_conct,c.job_no_mst,d.job_no_prefix_num,d.buyer_name,f.process_end_date,f.process_start_date,c.file_no,c.grouping,f.production_date as end_date,f.start_minutes,f.start_hours,f.end_hours,f.end_minutes,f.shift_name,f.machine_id,f.floor_id,f.remarks,f.re_stenter_no,f.insert_date $drying from pro_batch_create_dtls b,wo_po_break_down c,wo_po_details_master d,pro_batch_create_mst a,pro_fab_subprocess f where  a.id=b.mst_id and f.batch_id=b.mst_id and b.po_id=c.id and d.job_no=c.job_no_mst and f.entry_form=31 and a.batch_against in(1,2,3)  $company_cond $working_company_cond  and  f.batch_id=a.id $dates_com $jobdata $batch_num $booking_num $buyerdata $order_no $year_cond $shift_cond $file_cond $ref_cond $color_name $floor_no_cond and a.entry_form=0   and b.status_active=1 and b.is_deleted=0 and a.status_active=1 and a.is_deleted=0 GROUP BY  b.item_description,a.company_id, a.id, a.batch_no,a.booking_no, a.batch_date,a.color_id, a.extention_no, a.batch_against,b.prod_id, b.width_dia_type,c.job_no_mst, d.job_no_prefix_num, d.buyer_name, f.process_end_date,f.process_start_date,f.production_date,f.start_minutes,f.start_hours, f.end_hours, f.end_minutes,c.file_no,c.grouping, f.shift_name,f.machine_id,f.floor_id,f.remarks,f.re_stenter_no,f.insert_date $drying_group)
            union
            (
                select a.company_id,a.id,a.batch_no,a.booking_no,a.batch_date,a.color_id,a.extention_no,a.batch_against,SUM(b.batch_qnty) AS batch_qnty,b.item_description,b.prod_id,b.width_dia_type,null as po_number,null as po_id,null as job_no_mst,null as job_no_prefix_num, d.buyer_id as buyer_name, f.process_end_date, f.process_start_date,null as file_no,null as grouping,f.production_date as end_date,f.start_minutes,f.start_hours,f.end_hours,f.end_minutes,f.shift_name,f.machine_id,f.floor_id,f.remarks,f.re_stenter_no,f.insert_date $drying from pro_batch_create_dtls b,pro_batch_create_mst a,pro_fab_subprocess f, wo_non_ord_samp_booking_mst d where  a.id=b.mst_id  and f.batch_id=a.id and b.po_id=0 and a.booking_no_id = d.id and  f.entry_form=31 and  a.batch_against in(3) $company_cond $working_company_cond  $dates_com  $buyerdata_non_ord $batch_num $booking_num $shift_cond $color_name $floor_no_cond and a.entry_form=0  and b.status_active=1 and b.is_deleted=0 and a.status_active=1 and a.is_deleted=0  GROUP BY b.item_description,a.company_id, a.id, a.batch_no, a.booking_no, a.batch_date,a.color_id, a.extention_no, a.batch_against,b.prod_id, b.width_dia_type, d.buyer_id, f.process_end_date, f.process_start_date, f.production_date, f.start_minutes, f.start_hours, f.end_hours, f.end_minutes, f.shift_name, f.machine_id, f.floor_id, f.remarks,f.re_stenter_no,f.insert_date $drying_group
            ) $order_by2 ";
        }
        
        else if($cbo_type==4)//  Compacting
        {
            /*$sql="(
            select a.company_id,a.id,a.batch_no,a.booking_no,a.batch_date,a.color_id,a.extention_no,a.batch_against,b.batch_qnty AS batch_qnty,b.item_description,b.prod_id,b.width_dia_type,$group_conct,c.job_no_mst,d.job_no_prefix_num,d.buyer_name,f.process_end_date,c.file_no,c.grouping,f.process_start_date,f.production_date as end_date,f.start_minutes,f.start_hours,f.end_hours,f.end_minutes,f.shift_name,f.machine_id,f.floor_id,f.remarks $compact  from pro_batch_create_dtls b,wo_po_break_down c,wo_po_details_master d,pro_batch_create_mst a,pro_fab_subprocess f,pro_fab_subprocess_dtls h where a.id=b.mst_id  and b.po_id=c.id and d.job_no=c.job_no_mst  $company_cond $working_company_cond  and f.batch_id=a.id and  f.batch_id=b.mst_id  and f.id=h.mst_id and h.prod_id=b.prod_id and  f.entry_form=h.entry_page $dates_com $jobdata $batch_num  $booking_num $buyerdata $order_no $year_cond $shift_cond $file_cond $ref_cond $color_name and a.entry_form=0   and f.entry_form=33 and f.re_stenter_no=0 and  a.batch_against in(1,2,3) and b.status_active=1 and b.is_deleted=0 and a.status_active=1 and a.is_deleted=0 GROUP BY b.item_description,a.company_id, a.id, a.batch_no,a.booking_no, a.batch_date,a.color_id, a.extention_no, a.batch_against,b.prod_id, b.width_dia_type,c.job_no_mst, d.job_no_prefix_num, d.buyer_name, f.process_end_date,f.process_start_date,f.production_date,f.start_minutes,f.start_hours, f.end_hours, f.end_minutes,f.shift_name, f.machine_id,f.floor_id,c.file_no,c.grouping,f.remarks,b.batch_qnty $compact_group )
            union
            (
            select a.company_id,a.id,a.batch_no,a.booking_no,a.batch_date,a.color_id,a.extention_no,a.batch_against,b.batch_qnty AS batch_qnty,b.item_description,b.prod_id,b.width_dia_type,null as po_number,null as po_id,null as job_no_mst,null as job_no_prefix_num,null as buyer_name,f.process_end_date,null as file_no,null as grouping,f.process_start_date,f.production_date as end_date,f.start_minutes,f.start_hours,f.end_hours,f.end_minutes,f.shift_name,f.machine_id,f.floor_id,f.remarks $compact  from pro_batch_create_dtls b,pro_batch_create_mst a,pro_fab_subprocess f,pro_fab_subprocess_dtls h where a.id=b.mst_id and  f.batch_id=a.id and  f.batch_id=b.mst_id and f.id=h.mst_id and h.prod_id=b.prod_id and  f.entry_form=h.entry_page $company_cond $working_company_cond   $dates_com $batch_num $booking_num $shift_cond $color_name $buyerdata_non_ord  and a.entry_form=0  and f.entry_form=33 and f.re_stenter_no=0 and  a.batch_against in(3)  and b.status_active=1 and b.is_deleted=0 and a.status_active=1 and a.is_deleted=0 and b.po_id=0 GROUP BY b.item_description,a.company_id, a.id, a.batch_no,a.booking_no, a.batch_date,a.color_id, a.extention_no, a.batch_against,b.prod_id, b.width_dia_type, f.process_end_date,f.process_start_date,f.production_date,f.start_minutes,f.start_hours, f.end_hours, f.end_minutes,f.shift_name, f.machine_id,f.floor_id,f.remarks,b.batch_qnty $compact_group
            ) $order_by2";*/
            
             $sql="(select a.company_id,a.id,a.batch_no,a.booking_no,a.batch_date,a.color_id,a.extention_no,a.batch_against,SUM(b.batch_qnty) AS batch_qnty,b.item_description,b.prod_id,b.width_dia_type,$group_conct,c.job_no_mst,d.job_no_prefix_num,d.buyer_name,c.file_no,c.grouping,f.shift_name,f.entry_form,f.process_end_date,f.process_start_date,f.production_date as end_date,f.start_minutes,f.start_hours,f.end_hours,f.end_minutes,f.machine_id,f.floor_id,f.remarks,f.re_stenter_no,f.insert_date $compact from pro_batch_create_dtls b,wo_po_break_down c,wo_po_details_master d,pro_batch_create_mst a,pro_fab_subprocess f where  b.po_id=c.id and d.job_no=c.job_no_mst and f.batch_id=a.id  and a.id=b.mst_id and f.entry_form in(33) and f.re_stenter_no=0 and  a.batch_against in(1,2,3) $company_cond  $working_company_cond $dates_com $jobdata $batch_num $booking_num $buyerdata $order_no $year_cond $color_name $floor_no_cond $shift_cond $file_cond $ref_cond and a.entry_form=0   and b.status_active=1 and b.is_deleted=0 and a.status_active=1 and a.is_deleted=0 GROUP BY b.item_description,a.company_id, a.id, a.batch_no,a.booking_no, a.batch_date,a.color_id, a.extention_no, a.batch_against,b.prod_id, b.width_dia_type,c.job_no_mst, d.job_no_prefix_num, d.buyer_name,f.entry_form, f.process_end_date,f.process_start_date,f.production_date,f.start_minutes,f.start_hours, f.end_hours,c.file_no,c.grouping,f.shift_name, f.end_minutes, f.floor_id,f.machine_id,f.remarks,f.re_stenter_no,f.insert_date $compact_group)
            union
            (
            select a.company_id,a.id,a.batch_no,a.booking_no,a.batch_date,a.color_id,a.extention_no,a.batch_against,SUM(b.batch_qnty) AS batch_qnty,b.item_description,b.prod_id,b.width_dia_type,null as po_number,null as po_id,null as job_no_mst,null as job_no_prefix_num, d.buyer_id as buyer_name,null as file_no,null as grouping,f.shift_name,f.entry_form,f.process_end_date,f.process_start_date,f.production_date as end_date,f.start_minutes,f.start_hours,f.end_hours,f.end_minutes,f.machine_id,f.floor_id,f.remarks,f.re_stenter_no,f.insert_date $compact from pro_batch_create_dtls b,pro_batch_create_mst a,pro_fab_subprocess f, wo_non_ord_samp_booking_mst d where a.id=b.mst_id and  f.batch_id=a.id and f.entry_form in(33) and f.re_stenter_no=0 and  a.batch_against in(3) and b.po_id=0 and a.booking_no_id = d.id $company_cond $working_company_cond $dates_com $buyerdata_non_ord  $batch_num $booking_num  $color_name $floor_no_cond $shift_cond and a.entry_form=0  and b.status_active=1 and b.is_deleted=0 and a.status_active=1 and a.is_deleted=0 GROUP BY  b.item_description, a.company_id, a.id, a.batch_no,a.booking_no, a.batch_date,a.color_id, a.extention_no, a.batch_against, b.prod_id, b.width_dia_type, d.buyer_id, f.entry_form, f.process_end_date, f.process_start_date, f.production_date,f.start_minutes,f.start_hours, f.end_hours,f.shift_name, f.end_minutes,f.floor_id, f.machine_id,f.remarks,f.re_stenter_no,f.insert_date $compact_group
            )  $order_by2 ";          
            
            
        }   
        else if($cbo_type==5)//  Special Finish
        {
            $sql="(select a.company_id,a.id,a.batch_no,a.booking_no,a.batch_date,a.color_id,a.extention_no,a.batch_against,SUM(b.batch_qnty) AS batch_qnty,b.item_description,b.po_id,b.prod_id,b.width_dia_type,$group_conct,c.job_no_mst,d.job_no_prefix_num,d.buyer_name,f.process_id,f.process_end_date,f.process_start_date,c.file_no,c.grouping,f.production_date as end_date,f.start_minutes,f.start_hours,f.end_hours,f.end_minutes,f.shift_name,f.machine_id,f.floor_id,f.remarks,f.re_stenter_no,f.insert_date $sp_finish from pro_batch_create_dtls b,wo_po_break_down c,wo_po_details_master d,pro_batch_create_mst a,pro_fab_subprocess f where  f.batch_id=a.id and a.id=b.mst_id and f.entry_form=34 and  a.batch_against in(1,2,3) and b.po_id=c.id and d.job_no=c.job_no_mst $company_cond $working_company_cond   $dates_com $jobdata $batch_num $booking_num $buyerdata $order_no $year_cond $shift_cond $file_cond $ref_cond $color_name $floor_no_cond and a.entry_form=0  and b.status_active=1 and b.is_deleted=0 and a.status_active=1 and a.is_deleted=0 GROUP BY b.po_id, b.item_description,a.company_id, a.id, a.batch_no,a.booking_no, a.batch_date,a.color_id, a.extention_no, a.batch_against,b.prod_id, b.width_dia_type,c.job_no_mst, d.job_no_prefix_num, d.buyer_name, f.process_end_date,c.file_no,c.grouping,f.process_id,f.process_start_date,f.production_date,f.start_minutes,f.start_hours, f.end_hours, f.end_minutes,f.shift_name, f.machine_id,f.floor_id,f.remarks,f.re_stenter_no,f.insert_date $finish_group)
            union
            (
                select a.company_id,a.id,a.batch_no,a.booking_no,a.batch_date,a.color_id,a.extention_no,a.batch_against,SUM(b.batch_qnty) AS batch_qnty,b.item_description,b.po_id,b.prod_id,b.width_dia_type,null as po_number,null as po_id,null as job_no_mst,null as job_no_prefix_num,d.buyer_id as buyer_name,f.process_id, f.process_end_date,f.process_start_date,null as file_no,null as grouping,f.production_date as end_date,f.start_minutes,f.start_hours,f.end_hours,f.end_minutes,f.shift_name,f.machine_id,f.floor_id,f.remarks,f.re_stenter_no,f.insert_date $sp_finish from pro_batch_create_dtls b,pro_batch_create_mst a,pro_fab_subprocess f, wo_non_ord_samp_booking_mst d  where a.id=b.mst_id and  f.batch_id=a.id and  f.batch_id=b.mst_id and f.entry_form=34 and  a.batch_against in(3)  $company_cond $working_company_cond $dates_com $buyerdata_non_ord  $batch_num $booking_num $shift_cond $color_name $floor_no_cond and a.entry_form=0 and b.status_active=1 and b.is_deleted=0 and a.status_active=1 and a.is_deleted=0 and b.po_id=0 and a.booking_no_id = d.id GROUP BY b.po_id, b.item_description, a.company_id, a.id, a.batch_no,a.booking_no, a.batch_date,a.color_id, a.extention_no, a.batch_against,b.prod_id, b.width_dia_type, d.buyer_id, f.process_end_date, f.process_start_date, f.production_date,f.start_minutes,f.start_hours, f.end_hours, f.end_minutes, f.shift_name, f.process_id,f.machine_id,f.floor_id,f.remarks,f.re_stenter_no,f.insert_date $finish_group
            ) $order_by2";
        }
        else if($cbo_type==9)//  Stentering 
        {
            $sql="(select a.company_id,a.id,a.batch_no,a.booking_no,a.batch_date,a.color_id,a.extention_no,a.batch_against,SUM(b.batch_qnty) AS batch_qnty,b.item_description,b.prod_id,b.width_dia_type,$group_conct,c.job_no_mst,d.job_no_prefix_num,d.buyer_name,f.process_end_date,f.process_start_date,c.file_no,c.grouping,f.production_date as end_date,f.start_minutes,f.start_hours,f.end_hours,f.end_minutes,f.shift_name,f.machine_id,f.floor_id,f.remarks,f.re_stenter_no,f.insert_date $stenter from pro_batch_create_dtls b,wo_po_break_down c,wo_po_details_master d,pro_batch_create_mst a,pro_fab_subprocess f where  f.batch_id=a.id and a.id=b.mst_id and f.entry_form=48 and  a.batch_against in(1,2,3) and b.po_id=c.id and d.job_no=c.job_no_mst and  f.batch_id=b.mst_id $company_cond $working_company_cond  $dates_com $jobdata $batch_num $booking_num $buyerdata $order_no $year_cond $shift_cond $file_cond $ref_cond $color_name $floor_no_cond and a.entry_form=0 and f.re_stenter_no=0 and b.status_active=1 and b.is_deleted=0 and a.status_active=1 and a.is_deleted=0 and f.status_active=1 and f.is_deleted=0 GROUP BY  b.item_description,a.company_id, a.id, a.batch_no,a.booking_no, a.batch_date,a.color_id, a.extention_no, a.batch_against,b.prod_id, b.width_dia_type,c.job_no_mst, d.job_no_prefix_num, d.buyer_name, f.process_end_date,f.process_start_date,c.file_no,c.grouping,f.production_date,f.start_minutes,f.start_hours, f.end_hours, f.end_minutes, f.shift_name,f.machine_id,f.floor_id,f.remarks,f.re_stenter_no,f.insert_date $stenter_group)
            union
            (
            select a.company_id,a.id,a.batch_no,a.booking_no,a.batch_date,a.color_id,a.extention_no,a.batch_against,SUM(b.batch_qnty) AS batch_qnty,b.item_description,  b.prod_id,b.width_dia_type,null as po_number,null as po_id,null as job_no_mst,null as job_no_prefix_num, d.buyer_id as buyer_name, f.process_end_date, f.process_start_date, null as file_no,null as grouping,f.production_date as end_date, f.start_minutes, f.start_hours,f.end_hours, f.end_minutes,f.shift_name,f.machine_id,f.floor_id,f.remarks,f.re_stenter_no,f.insert_date $stenter from pro_batch_create_dtls b,pro_batch_create_mst a,pro_fab_subprocess f, wo_non_ord_samp_booking_mst d  where   f.batch_id=a.id and f.batch_id=b.mst_id and a.id=b.mst_id and f.entry_form=48 $company_cond $working_company_cond  $dates_com $buyerdata_non_ord  $batch_num $booking_num  $shift_cond $color_name $floor_no_cond and a.entry_form=0 and f.re_stenter_no=0  and  a.batch_against in(3)  and b.status_active=1 and b.is_deleted=0 and a.status_active=1 and a.is_deleted=0 and f.status_active=1 and f.is_deleted=0 and b.po_id=0 and a.booking_no_id = d.id GROUP BY b.item_description,a.company_id, a.id, a.batch_no,a.booking_no, a.batch_date,a.color_id, a.extention_no, a.batch_against,b.prod_id, b.width_dia_type, d.buyer_id,  f.process_end_date, f.process_start_date,f.production_date,f.start_minutes,f.start_hours, f.end_hours, f.end_minutes, f.shift_name,f.machine_id,f.floor_id,f.remarks,f.re_stenter_no,f.insert_date $stenter_group ) $order_by2 
            ";
        }
        else if($cbo_type==11)//  Re Stentering 
        {
            $sql="(SELECT a.company_id,a.id,a.batch_no,a.booking_no,a.batch_date,a.color_id,a.extention_no,a.batch_against,SUM(b.batch_qnty) AS batch_qnty,b.item_description,b.prod_id,b.roll_id,b.barcode_no,b.width_dia_type,$group_conct,c.job_no_mst,d.job_no_prefix_num,d.buyer_name,f.process_end_date,f.process_start_date,c.file_no,c.grouping,f.production_date as end_date,f.start_minutes,f.start_hours,f.end_hours,f.end_minutes,f.shift_name,f.machine_id,f.floor_id,f.remarks,f.insert_date $stenter,f.re_stenter_no from pro_batch_create_dtls b,wo_po_break_down c,wo_po_details_master d,pro_batch_create_mst a,pro_fab_subprocess f  where   a.id=b.mst_id and f.entry_form=48 and  a.batch_against in(1,2,3) and b.po_id=c.id and d.job_no=c.job_no_mst and  f.batch_id=a.id $company_cond $working_company_cond   $dates_com $jobdata $batch_num $booking_num $buyerdata $order_no $year_cond $shift_cond $file_cond $ref_cond $color_name $floor_no_cond and a.entry_form=0   and b.status_active=1 and b.is_deleted=0 and a.status_active=1 and a.is_deleted=0 and f.status_active=1 and f.is_deleted=0 and f.re_stenter_no!=0 GROUP BY b.item_description,a.company_id, a.id, a.batch_no,a.booking_no, a.batch_date,a.color_id, a.extention_no, a.batch_against,b.prod_id,b.roll_id,b.barcode_no, b.width_dia_type,c.job_no_mst, d.job_no_prefix_num, d.buyer_name, f.process_end_date,f.process_start_date,c.file_no,c.grouping,f.production_date,f.start_minutes,f.start_hours, f.end_hours, f.end_minutes, f.shift_name,f.machine_id,f.floor_id,f.remarks,f.re_stenter_no,f.insert_date $stenter_group)
            union
            (
            select a.company_id,a.id,a.batch_no,a.booking_no,a.batch_date,a.color_id,a.extention_no,a.batch_against,SUM(b.batch_qnty) AS batch_qnty,b.item_description,b.prod_id,b.roll_id,b.barcode_no,b.width_dia_type,null as po_number,null as po_id,null as job_no_mst,null as job_no_prefix_num, d.buyer_id as buyer_name, f.process_end_date, f.process_start_date, null as file_no,null as grouping,f.production_date as end_date, f.start_minutes, f.start_hours, f.end_hours, f.end_minutes, f.shift_name, f.machine_id, f.floor_id, f.remarks,f.insert_date $stenter,f.re_stenter_no from pro_batch_create_dtls b,pro_batch_create_mst a,pro_fab_subprocess f, wo_non_ord_samp_booking_mst d where f.batch_id=a.id  and a.entry_form=0 and a.id=b.mst_id and f.entry_form=48 and  a.batch_against in(3) $company_cond $working_company_cond $dates_com  $buyerdata_non_ord  $batch_num $booking_num $shift_cond $color_name $floor_no_cond and b.status_active=1 and b.is_deleted=0 and a.status_active=1 and a.is_deleted=0 and f.status_active=1 and f.is_deleted=0 and b.po_id=0 and a.booking_no_id = d.id and f.re_stenter_no!=0 GROUP BY b.item_description,a.company_id, a.id, a.batch_no, a.booking_no, a.batch_date, a.color_id, a.extention_no, a.batch_against,b.prod_id,b.roll_id,b.barcode_no, b.width_dia_type, d.buyer_id, f.process_end_date, f.process_start_date, f.production_date, f.start_minutes,f.start_hours, f.end_hours, f.end_minutes, f.shift_name, f.machine_id, f.floor_id, f.remarks, f.re_stenter_no,f.insert_date $stenter_group ) $order_by2 ";
            //echo $sql;
        }
        else if($cbo_type==12)//  Re Compacting
        {
            $sql="(
            select a.company_id,a.id,a.batch_no,a.booking_no,a.batch_date,a.color_id,a.extention_no,a.batch_against,SUM(b.batch_qnty) AS batch_qnty,b.item_description,b.prod_id,b.width_dia_type,$group_conct,c.job_no_mst,d.job_no_prefix_num,d.buyer_name,f.process_end_date,c.file_no,c.grouping,f.process_start_date,f.production_date as end_date,f.start_minutes,f.start_hours,f.end_hours,f.end_minutes,f.shift_name,f.machine_id,f.floor_id,f.remarks,f.insert_date $compact,f.re_stenter_no  from pro_batch_create_dtls b,wo_po_break_down c,wo_po_details_master d,pro_batch_create_mst a,pro_fab_subprocess f,pro_fab_subprocess_dtls h where  a.id=b.mst_id and b.po_id=c.id and d.job_no=c.job_no_mst and f.batch_id=a.id and  f.batch_id=b.mst_id and f.id=h.mst_id and h.prod_id=b.prod_id and  f.entry_form=h.entry_page $company_cond $working_company_cond  and f.entry_form=33 and  a.entry_form=0  and f.re_stenter_no!=0 and  a.batch_against in(1,2,3)  $dates_com $jobdata $batch_num $booking_num $buyerdata $order_no $year_cond $shift_cond $file_cond $ref_cond $color_name $floor_no_cond and b.status_active=1 and b.is_deleted=0 and a.status_active=1 and a.is_deleted=0 GROUP BY b.item_description,a.company_id, a.id, a.batch_no,a.booking_no, a.batch_date,a.color_id, a.extention_no, a.batch_against,b.prod_id, b.width_dia_type,c.job_no_mst, d.job_no_prefix_num, d.buyer_name, f.process_end_date,f.process_start_date,f.production_date,f.start_minutes,f.start_hours, f.end_hours, f.end_minutes,f.shift_name, f.machine_id,f.floor_id,c.file_no,c.grouping,f.remarks,f.insert_date,f.re_stenter_no $compact_group )
            union
            (
            select a.company_id,a.id,a.batch_no,a.booking_no,a.batch_date,a.color_id,a.extention_no,a.batch_against,SUM(b.batch_qnty) AS batch_qnty,b.item_description,b.prod_id,b.width_dia_type,null as po_number,null as po_id,null as job_no_mst,null as job_no_prefix_num,d.buyer_id as buyer_name,f.process_end_date,null as file_no,null as grouping,f.process_start_date,f.production_date as end_date,f.start_minutes,f.start_hours,f.end_hours,f.end_minutes,f.shift_name,f.machine_id,f.floor_id,f.remarks,f.insert_date $compact,f.re_stenter_no  from pro_batch_create_dtls b,pro_batch_create_mst a,pro_fab_subprocess f,pro_fab_subprocess_dtls h, wo_non_ord_samp_booking_mst d where a.id=b.mst_id and f.entry_form=33 and f.batch_id=b.mst_id and  f.batch_id=a.id and f.id=h.mst_id and h.prod_id=b.prod_id and  f.entry_form=h.entry_page $company_cond $working_company_cond   $dates_com $batch_num $booking_num $shift_cond $buyerdata_non_ord  $color_name $floor_no_cond and a.entry_form=0  and f.re_stenter_no!=0 and  a.batch_against in(3)  and b.status_active=1 and b.is_deleted=0 and a.status_active=1 and a.is_deleted=0 and b.po_id=0 and a.booking_no_id = d.id GROUP BY b.item_description, a.company_id, a.id, a.batch_no,a.booking_no, a.batch_date,a.color_id, a.extention_no, a.batch_against, b.prod_id, b.width_dia_type, d.buyer_id, f.process_end_date, f.process_start_date, f.production_date, f.start_minutes,f.start_hours, f.end_hours, f.end_minutes,f.shift_name, f.machine_id, f.floor_id, f.remarks,f.re_stenter_no,f.insert_date $compact_group
            ) $order_by2";
            //echo $sql;
        }   
        if($cbo_type==0)//  For All Search
        {
            $sql_heat="(
            select a.company_id,a.id,a.batch_no,a.booking_no,a.batch_date,a.color_id,a.extention_no,a.batch_against,SUM(b.batch_qnty) AS batch_qnty,b.item_description,b.prod_id,b.roll_id,b.barcode_no,b.width_dia_type,$group_conct,c.job_no_mst,d.job_no_prefix_num,d.buyer_name,c.file_no,c.grouping,f.entry_form,f.process_end_date,f.process_start_date,f.production_date as end_date,f.start_minutes,f.start_hours,f.end_hours,f.end_minutes,f.shift_name,f.machine_id,f.floor_id,f.remarks,f.re_stenter_no,f.insert_date $heat_set from pro_batch_create_dtls b,wo_po_break_down c,wo_po_details_master d,pro_batch_create_mst a,pro_fab_subprocess f where  f.batch_id=a.id and b.po_id=c.id and d.job_no=c.job_no_mst and a.id=b.mst_id and f.entry_form in(32) and  a.batch_against in(1,2,3)  $company_cond $working_company_cond $dates_com $jobdata $batch_num $booking_num $buyerdata $order_no $year_cond $shift_cond $file_cond $ref_cond $color_name $floor_no_cond and a.entry_form=0 and b.status_active=1 and b.is_deleted=0 and a.status_active=1 and a.is_deleted=0 GROUP BY b.item_description,a.company_id, a.id, a.batch_no,a.booking_no, a.batch_date,a.color_id, a.extention_no, a.batch_against,b.prod_id,b.roll_id,b.barcode_no, b.width_dia_type,c.job_no_mst, d.job_no_prefix_num, d.buyer_name,f.entry_form, f.process_end_date, f.process_start_date,f.production_date,f.start_minutes,f.start_hours,f.end_hours, f.end_minutes,c.file_no,c.grouping,f.shift_name,f.machine_id,f.floor_id,f.remarks,f.re_stenter_no,f.insert_date $heat_group)
            union
            (
            select a.company_id,a.id,a.batch_no,a.booking_no,a.batch_date,a.color_id,a.extention_no,a.batch_against,SUM(b.batch_qnty) AS batch_qnty,b.item_description,b.prod_id,b.roll_id,b.barcode_no,b.width_dia_type,null as po_number,null as po_id,null as job_no_mst,null as job_no_prefix_num, d.buyer_id as buyer_name,null as file_no,null as grouping,f.entry_form,f.process_end_date,f.process_start_date,f.production_date as end_date,f.start_minutes,f.start_hours,f.end_hours,f.end_minutes,f.shift_name,f.machine_id,f.floor_id,f.remarks,f.re_stenter_no,f.insert_date $heat_set from pro_batch_create_dtls b,pro_batch_create_mst a,pro_fab_subprocess f, wo_non_ord_samp_booking_mst d where a.id=b.mst_id and  f.batch_id=a.id and f.entry_form in(32) and a.batch_against in(1,2,3) $company_cond  $working_company_cond  $dates_com  $batch_num $booking_num $buyerdata_non_ord  $shift_cond $color_name $floor_no_cond and a.entry_form=0 and b.po_id=0 and a.booking_no_id = d.id and b.status_active=1 and b.is_deleted=0 and a.status_active=1 and a.is_deleted=0 GROUP BY b.item_description,a.company_id, a.id, a.batch_no,a.booking_no, a.batch_date, a.color_id, a.extention_no, a.batch_against,b.prod_id,b.roll_id,b.barcode_no, b.width_dia_type, d.buyer_id, f.entry_form, f.process_end_date, f.process_start_date, f.production_date, f.start_minutes, f.start_hours, f.end_hours, f.end_minutes,f.shift_name,f.machine_id,f.floor_id,f.remarks,f.re_stenter_no,f.insert_date $heat_group
            )  $order_by2 ";
            
            $sql_slitting="(
            SELECT a.company_id,a.id,a.batch_no,a.booking_no,a.batch_date,a.color_id,a.extention_no,a.batch_against,SUM(b.batch_qnty) AS batch_qnty,b.item_description,b.prod_id,b.roll_id,b.barcode_no,b.width_dia_type,$group_conct,c.job_no_mst,d.job_no_prefix_num,d.buyer_name,c.file_no,c.grouping,f.shift_name,f.entry_form,f.process_end_date,f.process_start_date,f.production_date as end_date,f.start_minutes,f.start_hours,f.end_hours,f.end_minutes,f.machine_id,f.floor_id,f.remarks,f.re_stenter_no,f.insert_date 
            $sliting_sq,b.barcode_no,b.roll_id  from pro_batch_create_dtls b,wo_po_break_down c,wo_po_details_master d,pro_batch_create_mst a,pro_fab_subprocess f where    f.batch_id=a.id   and a.id=b.mst_id and f.entry_form in(30) and  a.batch_against in(1,2,3) and b.po_id=c.id and d.job_no=c.job_no_mst $company_cond  $working_company_cond $dates_com $jobdata $batch_num $booking_num $buyerdata $order_no $year_cond $shift_cond $file_cond $ref_cond $color_name $floor_no_cond and a.entry_form=0 and b.status_active=1 and b.is_deleted=0 and a.status_active=1 and a.is_deleted=0 GROUP BY b.item_description,a.company_id, a.id, a.batch_no,a.booking_no, a.batch_date,a.color_id, a.extention_no, a.batch_against,b.prod_id,b.roll_id,b.barcode_no, b.width_dia_type,c.job_no_mst, d.job_no_prefix_num, d.buyer_name,c.file_no,c.grouping,f.shift_name,f.entry_form, f.process_end_date,f.process_start_date,f.production_date,f.start_minutes,f.start_hours, f.end_hours, f.end_minutes, f.machine_id,f.floor_id,f.remarks,f.re_stenter_no,f.insert_date $sliting_group,b.barcode_no,b.roll_id)
            UNION
            (
                SELECT a.company_id,a.id,a.batch_no,a.booking_no,a.batch_date,a.color_id,a.extention_no,a.batch_against,SUM(b.batch_qnty) AS batch_qnty,b.item_description,b.prod_id,b.roll_id,b.barcode_no,b.width_dia_type,null as po_number,null as po_id,null as job_no_mst,null as job_no_prefix_num, d.buyer_id as buyer_name,null as file_no,null as grouping,f.shift_name,f.entry_form,f.process_end_date,f.process_start_date,f.production_date as end_date,f.start_minutes,f.start_hours,f.end_hours,f.end_minutes,f.machine_id,f.floor_id,f.remarks,f.re_stenter_no,f.insert_date 
            $sliting_sq,b.barcode_no,b.roll_id  from pro_batch_create_dtls b,pro_batch_create_mst a,pro_fab_subprocess f, wo_non_ord_samp_booking_mst d where a.id=b.mst_id and f.batch_id=a.id and f.entry_form in(30) and  a.batch_against in(3) $company_cond  $working_company_cond $dates_com $batch_num $booking_num  $shift_cond $buyerdata_non_ord  $color_name $floor_no_cond and a.entry_form=0 and b.po_id=0 and a.booking_no_id = d.id and b.status_active=1 and b.is_deleted=0 and a.status_active=1 and a.is_deleted=0   GROUP BY b.item_description,a.company_id, a.id, a.batch_no,a.booking_no, a.batch_date,a.color_id, a.extention_no, a.batch_against,b.prod_id,b.roll_id,b.barcode_no, b.width_dia_type, d.buyer_id, f.shift_name,f.entry_form, f.process_end_date,f.process_start_date,f.production_date,f.start_minutes,f.start_hours, f.end_hours, f.end_minutes, f.machine_id,f.floor_id,f.remarks,f.re_stenter_no,f.insert_date $sliting_group ,b.barcode_no,b.roll_id
            ) $order_by2 ";

            $sql_drying=" (
            select a.company_id,a.id,a.batch_no,a.booking_no,a.batch_date,a.color_id,a.extention_no,a.batch_against,SUM(b.batch_qnty) AS batch_qnty,b.item_description,b.prod_id,b.roll_id,b.barcode_no,b.width_dia_type,$group_conct,c.job_no_mst,d.job_no_prefix_num,d.buyer_name,c.file_no,c.grouping,f.shift_name,f.entry_form,f.process_end_date,f.process_start_date,f.production_date as end_date,f.start_minutes,f.start_hours,f.end_hours,f.end_minutes,f.machine_id,f.floor_id,f.remarks,f.re_stenter_no,f.insert_date $drying  from pro_batch_create_dtls b,wo_po_break_down c,wo_po_details_master d,pro_batch_create_mst a,pro_fab_subprocess f where  f.batch_id=a.id  and a.id=b.mst_id and f.entry_form in(31) and  a.batch_against in(1,2,3) and b.po_id=c.id and d.job_no=c.job_no_mst and a.entry_form=0  $company_cond  $working_company_cond $dates_com $jobdata $batch_num $booking_num $buyerdata $order_no $year_cond $shift_cond $file_cond $ref_cond $color_name  $floor_no_cond  and b.status_active=1 and b.is_deleted=0 and a.status_active=1 and a.is_deleted=0 GROUP BY  b.item_description,a.company_id, a.id, a.batch_no,a.booking_no, a.batch_date,a.color_id, a.extention_no, a.batch_against,b.prod_id, b.roll_id,b.barcode_no,b.width_dia_type,c.job_no_mst, d.job_no_prefix_num, d.buyer_name,f.entry_form, f.process_end_date ,f.process_start_date,f.production_date,f.start_minutes,f.start_hours,f.end_hours, f.end_minutes,c.file_no,c.grouping,f.shift_name,f.floor_id, f.machine_id,f.remarks,f.re_stenter_no,f.insert_date $drying_group)
            union
            (
            select a.company_id,a.id,a.batch_no,a.booking_no,a.batch_date,a.color_id,a.extention_no,a.batch_against,SUM(b.batch_qnty) AS batch_qnty,b.item_description,b.prod_id,b.roll_id,b.barcode_no,b.width_dia_type,null as po_number,null as po_id,null as job_no_mst,null as job_no_prefix_num,d.buyer_id as buyer_name,null as file_no,null as grouping,f.shift_name,f.entry_form,f.process_end_date,f.process_start_date,f.production_date as end_date,f.start_minutes,f.start_hours,f.end_hours,f.end_minutes,f.machine_id,f.floor_id,f.remarks,f.re_stenter_no,f.insert_date $drying  from pro_batch_create_dtls b,pro_batch_create_mst a,pro_fab_subprocess f, wo_non_ord_samp_booking_mst d where  a.id=b.mst_id and f.batch_id=a.id  $company_cond  $working_company_cond   $dates_com  $buyerdata_non_ord  $batch_num $booking_num  $shift_cond $color_name $floor_no_cond and a.entry_form=0 and b.po_id=0 and a.booking_no_id = d.id and   f.entry_form in(31) and  a.batch_against in(3) and  b.status_active=1 and b.is_deleted=0 and a.status_active=1 and a.is_deleted=0 GROUP BY b.item_description,a.company_id, a.id, a.batch_no, a.booking_no, a.batch_date,a.color_id, a.extention_no, a.batch_against,b.prod_id,b.roll_id,b.barcode_no, b.width_dia_type, d.buyer_id, f.entry_form, f.process_end_date , f.process_start_date, f.production_date, f.start_minutes,f.start_hours,f.end_hours, f.end_minutes,f.shift_name, f.machine_id,f.floor_id,f.remarks,f.re_stenter_no,f.insert_date $drying_group
            ) $order_by2 ";

            $sql_stentering="(select a.company_id,a.id,a.batch_no,a.booking_no,a.batch_date,a.color_id,a.extention_no,a.batch_against,SUM(b.batch_qnty) AS batch_qnty,b.item_description,b.prod_id,b.roll_id,b.barcode_no,b.width_dia_type,$group_conct,c.job_no_mst,d.job_no_prefix_num,d.buyer_name,c.file_no,c.grouping,f.shift_name,f.process_end_date,f.process_start_date,f.production_date as end_date,f.start_minutes,f.start_hours,f.end_hours,f.end_minutes,f.machine_id,f.floor_id,f.remarks,f.re_stenter_no,f.insert_date $stenter  from pro_batch_create_dtls b,wo_po_break_down c,wo_po_details_master d,pro_batch_create_mst a,pro_fab_subprocess f where f.batch_id=a.id and a.id=b.mst_id and f.entry_form=48 and f.re_stenter_no=0 and  a.batch_against in(1,2,3) and b.po_id=c.id and d.job_no=c.job_no_mst  $company_cond  $working_company_cond $dates_com $jobdata $batch_num $booking_num $buyerdata $order_no $year_cond $shift_cond $file_cond $ref_cond $color_name $floor_no_cond and a.entry_form=0 and   b.status_active=1 and b.is_deleted=0 and a.status_active=1 and a.is_deleted=0 and f.status_active=1 and f.is_deleted=0 GROUP BY  b.item_description,a.company_id, a.id, a.batch_no,a.booking_no, a.batch_date,a.color_id, a.extention_no, a.batch_against,b.prod_id,b.roll_id,b.barcode_no, b.width_dia_type,c.job_no_mst, d.job_no_prefix_num, d.buyer_name,c.file_no,c.grouping,f.shift_name, f.process_end_date,f.process_start_date,f.production_date,f.start_minutes,f.start_hours, f.end_hours, f.end_minutes, f.machine_id,f.floor_id,f.remarks,f.re_stenter_no,f.insert_date $stenter_group)
            union
            (
            select a.company_id,a.id,a.batch_no,a.booking_no,a.batch_date,a.color_id,a.extention_no,a.batch_against,SUM(b.batch_qnty) AS batch_qnty,b.item_description,b.prod_id,b.roll_id,b.barcode_no,b.width_dia_type,null as po_number,null as po_id,null as job_no_mst,null as job_no_prefix_num, d.buyer_id as buyer_name,null as file_no,null as grouping,f.shift_name,f.process_end_date,f.process_start_date,f.production_date as end_date,f.start_minutes,f.start_hours,f.end_hours,f.end_minutes,f.machine_id,f.floor_id,f.remarks,f.re_stenter_no,f.insert_date $stenter  from pro_batch_create_dtls b,pro_batch_create_mst a,pro_fab_subprocess f, wo_non_ord_samp_booking_mst d where  f.batch_id=a.id  and a.id=b.mst_id and f.entry_form=48 and f.re_stenter_no=0 and  a.batch_against in(3) and b.po_id=0 and a.booking_no_id = d.id $company_cond  $working_company_cond $dates_com  $batch_num $booking_num  $buyerdata_non_ord  $shift_cond $color_name $floor_no_cond and a.entry_form=0  and b.status_active=1 and b.is_deleted=0 and a.status_active=1 and a.is_deleted=0 and f.status_active=1 and f.is_deleted=0  GROUP BY b.item_description,a.company_id, a.id, a.batch_no,a.booking_no, a.batch_date,a.color_id, a.extention_no, a.batch_against,b.prod_id,b.roll_id,b.barcode_no, b.width_dia_type, d.buyer_id, f.shift_name, f.process_end_date,f.process_start_date,f.production_date,f.start_minutes,f.start_hours, f.end_hours, f.end_minutes, f.machine_id,f.floor_id,f.remarks,f.re_stenter_no,f.insert_date $stenter_group
            ) $order_by2 ";

            $sql_compacting="(select a.company_id,a.id,a.batch_no,a.booking_no,a.batch_date,a.color_id,a.extention_no,a.batch_against,SUM(b.batch_qnty) AS batch_qnty,b.item_description,b.prod_id,b.roll_id,b.barcode_no,b.width_dia_type,$group_conct,c.job_no_mst,d.job_no_prefix_num,d.buyer_name,c.file_no,c.grouping,f.shift_name,f.entry_form,f.process_end_date,f.process_start_date,f.production_date as end_date,f.start_minutes,f.start_hours,f.end_hours,f.end_minutes,f.machine_id,f.floor_id,f.remarks,f.re_stenter_no,f.insert_date $compact from pro_batch_create_dtls b,wo_po_break_down c,wo_po_details_master d,pro_batch_create_mst a,pro_fab_subprocess f where  b.po_id=c.id and d.job_no=c.job_no_mst and f.batch_id=a.id  and a.id=b.mst_id and f.entry_form in(33) and f.re_stenter_no=0 and  a.batch_against in(1,2,3) $company_cond  $working_company_cond $dates_com $jobdata $batch_num $booking_num $buyerdata $order_no $year_cond $color_name $floor_no_cond $shift_cond $file_cond $ref_cond and a.entry_form=0   and b.status_active=1 and b.is_deleted=0 and a.status_active=1 and a.is_deleted=0 GROUP BY b.item_description,a.company_id, a.id, a.batch_no,a.booking_no, a.batch_date,a.color_id, a.extention_no, a.batch_against,b.prod_id,b.roll_id,b.barcode_no, b.width_dia_type,c.job_no_mst, d.job_no_prefix_num, d.buyer_name,f.entry_form, f.process_end_date,f.process_start_date,f.production_date,f.start_minutes,f.start_hours, f.end_hours,c.file_no,c.grouping,f.shift_name, f.end_minutes, f.floor_id,f.machine_id,f.remarks,f.re_stenter_no,f.insert_date $compact_group)
            union
            (
            select a.company_id,a.id,a.batch_no,a.booking_no,a.batch_date,a.color_id,a.extention_no,a.batch_against,SUM(b.batch_qnty) AS batch_qnty,b.item_description,b.prod_id,b.roll_id,b.barcode_no,b.width_dia_type,null as po_number,null as po_id,null as job_no_mst,null as job_no_prefix_num, d.buyer_id as buyer_name,null as file_no,null as grouping,f.shift_name,f.entry_form,f.process_end_date,f.process_start_date,f.production_date as end_date,f.start_minutes,f.start_hours,f.end_hours,f.end_minutes,f.machine_id,f.floor_id,f.remarks,f.re_stenter_no,f.insert_date $compact from pro_batch_create_dtls b,pro_batch_create_mst a,pro_fab_subprocess f, wo_non_ord_samp_booking_mst d where a.id=b.mst_id and  f.batch_id=a.id and f.entry_form in(33) and f.re_stenter_no=0 and  a.batch_against in(3) and b.po_id=0 and a.booking_no_id = d.id $company_cond $working_company_cond $dates_com $buyerdata_non_ord  $batch_num $booking_num  $color_name $floor_no_cond $shift_cond and a.entry_form=0  and b.status_active=1 and b.is_deleted=0 and a.status_active=1 and a.is_deleted=0 GROUP BY  b.item_description, a.company_id, a.id, a.batch_no,a.booking_no, a.batch_date,a.color_id, a.extention_no, a.batch_against, b.prod_id,b.roll_id,b.barcode_no, b.width_dia_type, d.buyer_id, f.entry_form, f.process_end_date, f.process_start_date, f.production_date,f.start_minutes,f.start_hours, f.end_hours,f.shift_name, f.end_minutes,f.floor_id, f.machine_id,f.remarks,f.re_stenter_no,f.insert_date $compact_group
            )  $order_by2 ";
            
              $sql_special="(
            select a.company_id,a.id,a.batch_no,a.booking_no,a.batch_date,a.color_id,a.extention_no,a.batch_against,SUM(b.batch_qnty) AS batch_qnty,b.item_description,b.prod_id,b.roll_id,b.barcode_no,b.width_dia_type,$group_conct,c.job_no_mst,d.job_no_prefix_num,d.buyer_name,c.file_no,c.grouping,f.shift_name,f.entry_form,f.process_end_date,f.process_id,f.process_start_date,f.production_date as end_date,f.start_minutes,f.start_hours,f.end_hours,f.end_minutes,f.machine_id,f.floor_id,f.remarks,f.re_stenter_no,f.insert_date $sp_finish  from pro_batch_create_dtls b,wo_po_break_down c,wo_po_details_master d,pro_batch_create_mst a,pro_fab_subprocess f where a.id=b.mst_id and f.batch_id=a.id  and b.po_id=c.id and d.job_no=c.job_no_mst $company_cond   $working_company_cond  $dates_com $jobdata $batch_num $booking_num $buyerdata $order_no $year_cond $color_name $floor_no_cond $shift_cond $file_cond $ref_cond and a.entry_form=0 and f.entry_form in(34) and  a.batch_against in(1,2,3)  and b.status_active=1 and b.is_deleted=0 and a.status_active=1 and a.is_deleted=0 GROUP BY b.item_description,a.company_id, a.id, a.batch_no,a.booking_no, a.batch_date,a.color_id, a.extention_no, a.batch_against,b.prod_id,b.roll_id,b.barcode_no, b.width_dia_type,c.job_no_mst, d.job_no_prefix_num, d.buyer_name,f.entry_form,f.process_id, f.process_end_date,f.process_start_date,f.production_date,f.start_minutes,f.start_hours, f.end_hours,c.file_no,c.grouping,f.shift_name, f.end_minutes, f.machine_id,f.floor_id,f.remarks,f.re_stenter_no,f.insert_date $finish_group)          
            union
            (
            select a.company_id,a.id,a.batch_no,a.booking_no,a.batch_date,a.color_id,a.extention_no,a.batch_against,SUM(b.batch_qnty) AS batch_qnty,b.item_description,b.prod_id,b.roll_id,b.barcode_no,b.width_dia_type,null as po_number,null as po_id,null as job_no_mst,null as job_no_prefix_num, d.buyer_id as buyer_name,null as file_no,null as grouping,f.shift_name,f.entry_form,f.process_end_date,f.process_id,f.process_start_date,f.production_date as end_date, f.start_minutes,f.start_hours,f.end_hours,f.end_minutes,f.machine_id,f.floor_id,f.remarks,f.re_stenter_no,f.insert_date $sp_finish  from pro_batch_create_dtls b,pro_batch_create_mst a,pro_fab_subprocess f, wo_non_ord_samp_booking_mst d where a.id=b.mst_id and  f.batch_id=a.id and f.entry_form in(34) and  a.batch_against in(1,2,3) and b.po_id=0 and a.booking_no_id = d.id $company_cond  $working_company_cond  $dates_com  $batch_num $booking_num $buyerdata_non_ord  $color_name $floor_no_cond $shift_cond and a.entry_form=0 and b.status_active=1 and b.is_deleted=0 and a.status_active=1 and a.is_deleted=0 GROUP BY b.item_description,a.company_id,a.id,a.batch_no,a.booking_no,a.batch_date,a.color_id,a.extention_no,a.batch_against,b.prod_id,b.roll_id,b.barcode_no, b.width_dia_type, d.buyer_id, f.entry_form, f.process_end_date, f.process_start_date,f.process_id, f.production_date, f.start_minutes,f.start_hours, f.end_hours, f.shift_name, f.end_minutes, f.machine_id,f.floor_id,f.remarks,f.re_stenter_no,f.insert_date $finish_group
            ) $order_by2 ";
        }
        
        // echo $sql;die;
        
        ob_start();
        $type_array_check=array(0,6,7,8,10);
        if(!in_array($cbo_type,$type_array_check))
        {
            $batchdata=sql_select($sql);
        }

        $group_by=str_replace("'",'',$cbo_group_by);
        $po_id="";
        foreach($batchdata as $row)
        {
            $po_id.=$row[csf('po_id')].',';
        }
        $po_ids=rtrim($po_id,',');
        if($po_ids!='') $po_ids=$po_ids;else $po_ids=0;
        
        $yarn_lot_arr=array();
        if($db_type==0)
        {
        $yarn_lot_data=sql_select("select b.po_breakdown_id, a.prod_id, group_concat(distinct(a.yarn_lot)) as yarn_lot from pro_grey_prod_entry_dtls a, order_wise_pro_details b where a.id=b.dtls_id and b.entry_form in(2,22) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.yarn_lot!='' and b.po_breakdown_id in($po_ids)  group by a.prod_id, b.po_breakdown_id");
        }
        else if($db_type==2)
        {
        $yarn_lot_data=sql_select("select b.po_breakdown_id, a.prod_id, listagg(a.yarn_lot,',') within group (order by a.yarn_lot) as yarn_lot from pro_grey_prod_entry_dtls a, order_wise_pro_details b where a.id=b.dtls_id and b.entry_form in(2,22) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.yarn_lot!='0' group by a.prod_id, b.po_breakdown_id");
        }
        foreach($yarn_lot_data as $rows)
        {
            $yarn_lot=explode(",",$rows[csf('yarn_lot')]);
            $yarn_lot_arr[$rows['prod_id']][$rows['po_breakdown_id']]=implode(",",array_unique($yarn_lot));
        }
        unset($yarn_lot_data);
        
        
        
        if($cbo_type==1)
        {
            //echo $cbo_type;
            ?>
            <div style="width:1820px;">
            <fieldset style="width:1820px;">
            <div  align="center"> <strong> <? echo $company_library[$company]; ?> </strong><br><strong> <? echo $search_by_arr[$cbo_type];?> </strong>
            <br>
            <?
                echo  change_date_format($date_from).' '.To.' '.change_date_format($date_to);
            ?>
             </div>
             <table class="rpt_table" width="1920" cellpadding="0" cellspacing="0" border="1" rules="all" align="center" id="table_header_1">
            <thead>
            <tr>
                <th width="30">SL</th>
                
               <? if($group_by==2 || $group_by==0){ ?>
                 <th width="80">M/C No</th>
                 <? } ?>
                <? if($group_by==1 || $group_by==0){ ?>
                <th width="80">Floor</th>  
                 <? } 
                 ?>
                <th width="50">Shift</th>
                <th width="100">Buyer</th>
                <th width="80">Job</th>
                <th width="100">Booking</th>
                <th width="60">File No</th>
                <th width="70">Ref. No</th>
                <th width="90">Order No</th>
                <th width="100">Fabrics Desc</th>
                <th width="80">Dia/ Width Type</th>
                <th width="70">GSM</th>
                <th width="80">Color Name</th>
                <th width="90">Batch No</th>
                <th width="80">Ext. No</th>
                <th width="80">Batch Qty.</th>
                <th width="70">Prod. Qty.</th>
                <th width="60">Yarn Lot</th>
                <th width="100">Start Date & Time</th>
                <th width="100">End Date & Time</th>
                <th width="70">Time Used</th>
                <th width="80">Remark</th>
                <th width="">Reprocess</th>
            </tr>
            </thead>
            </table>
            <div style=" max-height:380px; width:1920px; overflow-y:scroll;" id="scroll_body">
            <table class="rpt_table" id="table_body" width="1900" cellpadding="0" cellspacing="0" border="1" rules="all" align="center">
            <tbody>
            <? 

            $i=1;$k=1;
            $f=0;
            $btq=0;$tot_prod_btq=0;$grand_tot_prod_btq=$grand_btg=0;
            $batch_chk_arr=array();$group_by_arr=array();$heat_prod_chk_arr=array();$heat_prod_qty=0;
            foreach($batchdata as $batch)
            { 
                if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
                $order_id=$batch[csf('po_id')];
                $color_id=$batch[csf('color_id')];
                $desc=explode(",",$batch[csf('item_description')]); 
                $insert_date = "'".$batch[csf('insert_date')]."'";
                $po_number=implode(",",array_unique(explode(",",$batch[csf('po_number')])));
            if($group_by!=0)
            {
                if($group_by==1)
                {
                    $group_value=$batch[csf('floor_id')];
                    $group_name="Floor";
                    $group_dtls_value=$floor_arr[$batch[csf('floor_id')]];
                }
                
                else if($group_by==2)
                {
                    $group_value=$batch[csf('machine_id')];
                    $group_name="Machine";
                    $group_dtls_value=$machine_arr[$batch[csf('machine_id')]];
                }
                if (!in_array($group_value,$group_by_arr) )
                    {
                        if($k!=1)
                        { 
                        ?>
                        <tr class="tbl_bottom">
                        <td width="30"></td>
                       <? if($group_by==2 || $group_by==0){ ?>
                         <td width="80"></td>
                         <? } ?>
                        <? if($group_by==1 || $group_by==0){ ?>
                        <td width="80"></td>  
                         <? } 
                         ?>
                        <td width="50"></td>
                        <td width="100"></td>
                        <td width="80"></td>
                        <td width="100"></td>
                        <td width="60"></td>
                        <td width="70"></td>
                        <td width="90"></td>
                        <td width="100"></td>
                        <td width="80"></td>
                        <td width="70"></td>
                        <td width="80"></td>
                        <td width="90"></td>
                        <td width="80">Sub Total</td>
                        <td width="80"><? echo number_format($btq,2);?></td>
                        <td width="70"><? echo number_format($tot_prod_btq,2);?></td>
                        <td width="60"></td>
                        <td width="100"></td>
                        <td width="100"></td>
                        <td width="70"></td>
                        <td width="80"></td>
                        <td width=""></td>
                        <tr bgcolor="#EFEFEF">
                            <td colspan="24" align="left" ><b><? echo $group_name; ?> : <? echo $group_dtls_value; ?></b></td>
                        </tr>
                        <?
                        unset($btq);unset($tot_prod_btq);
                        }
                        $group_by_arr[]=$group_value;            
                        $k++;
                    }
            } 
                $heat_grouping_arr_val=$batch[csf('batch_no')].$batch[csf('machine_id')].$batch[csf('floor_id')];
                //==check repeat prod qty 
               // $heat_prod_qty=$batch_prod_qty_arr[$batch[csf('id')]][$batch[csf('prod_id')]][$batch[csf('width_dia_type')]]['heat'];
                        //if ($roll_maintained==1 && $cbo_type!=0)
                         if(($page_upto==1 || $page_upto>1) && $roll_maintained==1 && $cbo_type!=0)
                         {
                             $heat_prod_qty=$batch_prod_qty_heat_arr[$batch[csf('id')]][$batch[csf('prod_id')]][$batch[csf('width_dia_type')]][$batch[csf('roll_no')]][$batch[csf('roll_no_heat')]][$insert_date]['heat'];
                             //echo $heat_prod_qty.'='.$roll_maintained.'='.$cbo_type.'<br/>';
                         }
                         else 
                         {
                             $heat_prod_qty=$batch_prod_qty_arr[$batch[csf('id')]][$batch[csf('prod_id')]][$batch[csf('width_dia_type')]][$insert_date]['heat'];
                             //echo $heat_prod_qty.'system';

                         }
                         
                $prod_qty_ids=$batch[csf('id')].$batch[csf('prod_id')].$batch[csf('width_dia_type')].$insert_date;
                if (!in_array($prod_qty_ids,$heat_prod_chk_arr))
                { //$b++;
                     $heat_prod_chk_arr[]=$prod_qty_ids;
                      $tot_prod_qty=$heat_prod_qty;
                }
                else
                {
                     $tot_prod_qty=0;
                }
                //==End check repeat prod qty 
            ?>
                    <tr bgcolor="<? echo $bgcolor;?>" onClick="change_color('tr<? echo $i;?>','<? echo $bgcolor;?>')" id="tr<? echo $i;?>">
                        <? if (!in_array($heat_grouping_arr_val,$batch_chk_arr) )
                                { $f++;
                                    ?>
                        <td width="30"><? echo $f; ?></td>
                                            
                         <? if($group_by==2 || $group_by==0){ ?>
                        <td  align="center" width="80"><p><? echo $machine_arr[$batch[csf('machine_id')]]; ?></p></td>
                        <?
                         }
                         if($group_by==1 || $group_by==0){ ?>
                       <td width="80"><p><? echo $floor_arr[$batch[csf('floor_id')]]; ?></p></td>
                        <? } ?>
                        <td  align="center" width="50"><p><? echo $shift_name[$batch[csf('shift_name')]]; ?></p></td>
                        <td  width="100" title="<? echo $buyer_arr[$batch[csf('buyer_name')]]; ?>"><p><? echo $buyer_arr[$batch[csf('buyer_name')]]; ?></p></td>
                        <td  width="80" title="<? //echo $color_library[$batch[csf('color_id')]]; ?>"><p><? echo $batch[csf('job_no_prefix_num')]; ?></p></td>
                        <td  width="100"><p><? echo $batch[csf('booking_no')]; ?></p></td>
                        <td  width="60"><p><? echo $batch[csf('file_no')]; ?></p></td>
                        <td  width="70"><p><? echo $batch[csf('grouping')]; ?></p></td>
                        <td width="90"><div style="width:90px; word-wrap:break-word;"><? echo $po_number; ?></div></td>
                            <?  
                            $batch_chk_arr[]=$heat_grouping_arr_val;
                                } 
                                else
                                   { ?>
                        <td width="30"><? //echo $sl; ?></td>
                        <? if($group_by==2 || $group_by==0){ ?>
                        <td  align="center" width="80"><p><? //echo $machine_arr[$batch[csf('machine_id')]]; ?></p></td>
                        <?
                         }
                         if($group_by==1 || $group_by==0){ ?>
                       <td width="80"><p><? //echo $floor_arr[$batch[csf('floor_id')]]; ?></p></td>
                        <? } ?>
                        <td  align="center" width="50"><p><? //echo $machine_id; ?></p></td>
                        <td  width="100"><p><? //echo $buyer_arr[$batch[csf('buyer_name')]]; ?></p></td>
                        <td  width="80" title="<? //echo $color_library[$batch[csf('color_id')]]; ?>"><p><? //echo $color_library[$batch[csf('color_id')]]; ?></p></td>
                        <td  width="100"><p><? //echo $special_row[csf('booking_no')]; ?></p></td>
                        <td  width="60"><p><? //echo $special_row[csf('file_no')]; ?></p></td>
                        <td  width="70"><p><? //echo $special_row[csf('grouping')]; ?></p></td>
                        <td width="90"><p><? //echo $batch[csf('po_number')]; ?></p></td>
                                <? }
                                ?>
                        <td  width="100" title="<? echo $desc[0]; ?>"><p><? echo $batch[csf('item_description')]; ?></p></td>
                        <td  width="80"><div style="width:80px; word-wrap:break-word;"><? echo $fabric_typee[$batch[csf('width_dia_type')]];  ?></div></td>
                        <td  width="70" title="<? echo  $desc[2]; ?>"><p><? echo $desc[2]; ?></p></td>
                        <td  width="80" title="<? echo $color_library[$batch[csf('color_id')]]; ?>"><p><? echo $color_library[$batch[csf('color_id')]]; ?></p></td>
                        <td  align="center" width="90" title="<? echo $batch[csf('batch_no')]; ?>"><p><? echo $batch[csf('batch_no')]; ?></p></td>
                        <td  align="center" width="80" title="<? echo $batch[csf('extention_no')]; ?>"><p><? echo $batch[csf('extention_no')]; ?></p></td>
                        <td align="right" width="80"><p><? echo number_format($batch[csf('batch_qnty')],2);  ?></p></td>
                        <td align="right" width="70" ><? //echo number_format($batch_prod_qty_arr[$batch[csf('id')]][$batch[csf('prod_id')]][$batch[csf('width_dia_type')]][$insert_date]['heat'],2);
                        echo number_format($tot_prod_qty,2);  ?></td>
                        <td align="left" width="60" title="<? echo $yarn_lot_arr[$batch[csf('prod_id')]][$batch[csf('po_id')]]; ?>"><p><? echo $yarn_lot_arr[$batch[csf('prod_id')]][$batch[csf('po_id')]];  ?></p></td>
                          <td width="100" title="Process Start Date"><div style="width:100px; word-wrap:break-word;"><?  echo change_date_format($batch[csf('process_start_date')]).', '.$batch[csf('start_hours')].':'.$batch[csf('start_minutes')]; ?></div></td>
                        <td width="100" title="Process End Date"><div style="width:100px; word-wrap:break-word;"><?  echo change_date_format($batch[csf('end_date')]).', '.$batch[csf('end_hours')].':'.$batch[csf('end_minutes')]; ?></div></td>
                            <td width="70" align="center"><div style="width:70px; word-wrap:break-word;"><?
                                $end_time=$batch[csf('end_hours')].':'.$batch[csf('end_minutes')];
                                $start_time=$batch[csf('start_hours')].':'.$batch[csf('start_minutes')];
                                
                                $new_date_time_start=($batch[csf('process_start_date')].' '.$start_time.':'.'00');
                                $new_date_time_end=($batch[csf('end_date')].' '.$end_time.':'.'00');
                                $total_time=datediff(n,$new_date_time_start,$new_date_time_end);
                                echo floor($total_time/60).":".$total_time%60; ?></div></td>
                         <td  align="center" width="80" title="<? echo $batch[csf('remarks')]; ?>"><p><? echo $batch[csf('remarks')]; ?></p></td>
                        <td align="center"  title="<? if($batch[csf('batch_against')]==2) echo $batch_against[$batch[csf('batch_against')]]; ?>"><p><?  if($batch[csf('batch_against')]==2) echo $batch_against[$batch[csf('batch_against')]];?> </p></td>
                    </tr>
                    <? 
                    $i++;
                    $btq+=$batch[csf('batch_qnty')];
                    $grand_btq+=$batch[csf('batch_qnty')];
                    
                    //$tot_prod_btq+=$batch_prod_qty_arr[$batch[csf('id')]][$batch[csf('prod_id')]][$batch[csf('width_dia_type')]]['heat'];
                    //$grand_tot_prod_btq+=$batch_prod_qty_arr[$batch[csf('id')]][$batch[csf('prod_id')]][$batch[csf('width_dia_type')]]['heat'];

                    $tot_prod_btq+=$tot_prod_qty;
                    $grand_tot_prod_btq+=$tot_prod_qty;
                   } //batchdata froeach
                        if($group_by!=0)
                        { 
                        ?>
                        <tr class="tbl_bottom">
                        <td width="30"></td>
                       <? if($group_by==2 || $group_by==0){ ?>
                         <td width="80"></td>
                         <? } ?>
                        <? if($group_by==1 || $group_by==0){ ?>
                        <td width="80"></td>  
                         <? } 
                         ?>
                        <td width="50"></td>
                        <td width="100"></td>
                        <td width="80"></td>
                        <td width="100"></td>
                        <td width="60"></td>
                        <td width="70"></td>
                        <td width="90"></td>
                        <td width="100"></td>
                        <td width="80"></td>
                        <td width="70"></td>
                        <td width="80"></td>
                        <td width="90"></td>
                        <td width="80">Sub Total</td>
                        <td width="80"><? echo number_format($btq,2);?></td>
                        <td width="70"><? echo number_format($tot_prod_btq,2);?></td>
                        <td width="60"></td>
                        <td width="100"></td>
                        <td width="100"></td>
                        <td width="70"></td>
                        <td width="80"></td>
                        <td width=""></td>
                        
                        <?
                        //unset($btq);unset($tot_prod_btq);
                        }
             ?>
             
            </tbody>
            </table>

             <table class="rpt_table" width="1900" cellpadding="0" cellspacing="0" border="1" rules="all" align="center" id="report_table_footer"> 
            <tfoot>
            <tr>
                <th width="30">&nbsp;</th>
                 <? if($group_by==2 || $group_by==0){ ?>
                    <th width="80">&nbsp;</th>
                    <? } ?>
                    <? if($group_by==1 || $group_by==0){ ?>
                    <th width="80">&nbsp;</th> 
                    <? } 
                    ?>
                <th width="50">&nbsp;</th>
                <th width="100">&nbsp;</th>
                <th width="80">&nbsp;</th>
                <th width="100">&nbsp;</th>
                <th width="60">&nbsp;</th>
                <th width="70">&nbsp;</th>
                <th width="90">&nbsp;</th>
                <th width="100">&nbsp;</th>
                <th width="80">&nbsp;</th>
                <th width="70">&nbsp;</th>
                <th width="80">&nbsp;</th>
                <th width="90">&nbsp;</th>
                <th width="80">&nbsp;</th>
                <th width="80"><? echo number_format($grand_btq,2); ?></th>
                <th width="70"><? echo number_format($grand_tot_prod_btq,2); ?></th>
                <th width="60">&nbsp;</th>
                <th width="100">&nbsp;</th>
                <th width="100">&nbsp;</th>
                <th width="70">&nbsp;</th>
                <th width="80">&nbsp;</th>
                <th>&nbsp;</th>
            </tr>
            </tfoot>
            </table>

            </div>
            </fieldset>
            </div>
            <?
        }
        else if($cbo_type==2)// Slitting/Squeezing
        {
            //echo $cbo_type;
            ?>
            <div>
            <fieldset style="width:1765px;">
            <div style="float: left;"><strong> <? echo $company_library[$company]; ?> </strong><br> <strong><? echo $search_by_arr[$cbo_type];?> </strong><br>
            <?
                echo  change_date_format($date_from).' '.To.' '.change_date_format($date_to);
            ?>
             </div>
             <table class="rpt_table" width="1835" cellpadding="0" cellspacing="0" border="1" rules="all" style="float: left;" id="table_header_1">
            <thead>
            <tr>
                <th width="30">SL</th>
                 <? if($group_by==2 || $group_by==0){ ?>
                 <th width="80">M/C No</th>
                 <? } ?>
                <? if($group_by==1 || $group_by==0){ ?>
                <th width="80">Floor</th>  
                 <? } 
                 ?>
                <th width="50">Shift</th>
                <th width="100">Buyer</th>
                <th width="80">Job</th>  
                <th width="100">Booking</th>  
                <th width="60">File No</th>
                <th width="70">Ref. No</th>
                <th width="90">Order No</th>
                <th width="100">Fabrics Desc</th>
                <th width="70">GSM</th>
                <th width="75">Dia/Width Type</th>
                <th width="80">Color Name</th>
                <th width="90">Batch No</th>
                <th width="40">Extn. No</th>
                <th width="70">Batch Qty.</th>
                <th width="70">Prod. Qty.</th>
                <th width="50">Lot No</th>
                <th width="75">Start Date & Time</th>
                <th width="75">End Date & Time</th>
                <th width="70">Time Used</th>
                <th width="60">Remark</th>
                <th>Reprocess</th>
            </tr>
            </thead>
            </table>
            <div style=" max-height:350px; width:1835px; overflow-y:scroll;float: left;" id="scroll_body">
            <table class="rpt_table" id="table_body" width="1815" cellpadding="0" cellspacing="0" border="1" rules="all" style="float: left;">
            <tbody>
            <? 
            //.$buyer_arr[$booking_Arr[$batch[csf('booking_no')]]['buyer_id']]
            $i=1;$k=1;
            $f=0;
            $btq=0;$tot_prod_btq=$grand_tot_prod_btq=$grand_btq=0;
            $batch_chk_arr=array();
            foreach($batchdata as $batch)
            { 
            if ($i%2==0)  
            $bgcolor="#E9F3FF";
            else
            $bgcolor="#FFFFFF";
            $order_id=$batch[csf('po_id')];
            $color_id=$batch[csf('color_id')];
            $desc=explode(",",$batch[csf('item_description')]); 
            $insert_date = "'".$batch[csf('insert_date')]."'";
            $po_number=implode(",",array_unique(explode(",",$batch[csf('po_number')]))); 
            if($group_by!=0)
            {
                if($group_by==1)
                {
                    $group_value=$batch[csf('floor_id')];
                    $group_name="Floor";
                    $group_dtls_value=$floor_arr[$batch[csf('floor_id')]];
                }
                
                else if($group_by==2)
                {
                    $group_value=$batch[csf('machine_id')];
                    $group_name="Machine";
                    $group_dtls_value=$machine_arr[$batch[csf('machine_id')]];
                }
                if (!in_array($group_value,$group_by_arr) )
                    {
                        if($k!=1)
                        { 
                        ?>  
                        <tr class="tbl_bottom">
                        <td width="30"></td>
                         <? if($group_by==2 || $group_by==0){ ?>
                         <td width="80">&nbsp;</td>
                         <? } ?>
                        <? if($group_by==1 || $group_by==0){ ?>
                        <td width="80">&nbsp;</td>  
                         <? } 
                         ?>
                        <td width="50">&nbsp;</td>
                        <td width="100">&nbsp;</td>
                        <td width="80">&nbsp;</th>  
                        <td width="100">&nbsp;</th>  
                        <td width="60">&nbsp;</td>
                        <td width="70">&nbsp;</td>
                        <td width="90">&nbsp;</td>
                        <td width="100">&nbsp;</td>
                        <td width="70">&nbsp;</td>
                        <td width="75">&nbsp;</td>
                        <td width="80">&nbsp;</td>
                       
                        <td colspan="2" align="right">Sub Total</td>
                        <td width="70"><?  echo number_format($btq,2);?></td>
                        <td width="70"><?  echo number_format($tot_prod_btq,2);?></td>
                        <td width="50">&nbsp;</td>
                        <td width="75">&nbsp;</td>
                        <td width="75">&nbsp;</td>
                        <td width="70">&nbsp;</td>
                        <td width="60">&nbsp;</td>
                        <td>&nbsp;</td>
                        </tr>
                
                        <tr bgcolor="#EFEFEF">
                            <td colspan="24" align="left" ><b><? echo $group_name; ?> : <? echo $group_dtls_value; ?></b></td>
                        </tr>
                        <?
                        unset($btq);unset($tot_prod_btq);
                        }
                        $group_by_arr[]=$group_value;            
                        $k++;
                    }
            } 
            ?>
            <tr bgcolor="<? echo $bgcolor;?>" onClick="change_color('tr<? echo $i;?>','<? echo $bgcolor;?>')" id="tr<? echo $i;?>">
                  <? if (!in_array($batch[csf('batch_no')],$batch_chk_arr) )
                        { $f++;
                            ?>
                <td width="30"><? echo $f; ?></td>
                <? if($group_by==2 || $group_by==0){ ?>
                 <td  align="center" width="80" title="<? echo $machine_arr[$batch[csf('machine_id')]]; ?>"><p><? echo $machine_arr[$batch[csf('machine_id')]]; ?></p></td>
                <?
                 }
                 if($group_by==1 || $group_by==0){ ?>
               <td width="80"><p><? echo $floor_arr[$batch[csf('floor_id')]]; ?></p></td>
                <? } ?>
                
                 <td  align="center" width="50"><p><? echo $shift_name[$batch[csf('shift_name')]]; ?></p></td>
                <td  width="100" title="<? echo $buyer_arr[$batch[csf('buyer_name')]]; ?>"><p><? echo $buyer_arr[$batch[csf('buyer_name')]]; ?></p></td>
                <td  width="80" title="<? //echo $color_library[$batch[csf('color_id')]]; ?>"><p><? echo $batch[csf('job_no_prefix_num')]; ?></p></td>
                <td  width="100"><p><? echo $batch[csf('booking_no')]; ?></p></td>
                <td  width="60"><p><? echo $batch[csf('file_no')]; ?></p></td>
                <td  width="70"><p><? echo $batch[csf('grouping')]; ?></p></td>
                <td width="90"><div style="width:90px; word-wrap:break-word;"><? echo $po_number; ?></div></td>
                    <?  
                    $batch_chk_arr[]=$batch[csf('batch_no')];
                        } 
                        else
                           { ?>
                <td width="30"><? //echo $sl; ?></td>
                <? if($group_by==2 || $group_by==0){ ?>
                 <td  align="center" width="80"><p><? //echo $machine_arr[$batch[csf('machine_id')]]; ?></p></td>
                <?
                 }
                 if($group_by==1 || $group_by==0){ ?>
               <td width="80"><p><? //echo $floor_arr[$batch[csf('floor_id')]]; ?></p></td>
                <? } ?>
                <td  align="center" width="50"><p><? //echo $machine_id; ?></p></td>
                <td  width="100"><p><? //echo $buyer_arr[$batch[csf('buyer_name')]]; ?></p></td>
                <td  width="80" title="<? //echo $color_library[$batch[csf('color_id')]]; ?>"><p><? //echo $color_library[$batch[csf('color_id')]]; ?></p></td>
                <td  width="100"><p><? //echo $special_row[csf('booking_no')]; ?></p></td>
                <td  width="60"><p><? //echo $special_row[csf('file_no')]; ?></p></td>
                <td  width="70"><p><? //echo $special_row[csf('grouping')]; ?></p></td>
                <td width="90"><p><? //echo $batch[csf('po_number')]; ?></p></td>
                        <? }
                        ?>
                <td  width="100" title="<? echo $desc[0]; ?>"><p><? echo $batch[csf('item_description')]; ?></p></td>
                <td  width="70" title="<? echo   $desc[2]; ?>"><p><? echo  $desc[2]; ?></p></td>
                <td width="75"><p><? echo $fabric_typee[$batch[csf('width_dia_type')]];;?></p></td>
                <td  width="80"><p><? echo $color_library[$batch[csf('color_id')]]; ?></p></td>
                <td  align="center" width="90" title="<? echo $batch[csf('batch_no')]; ?>"><p><? echo $batch[csf('batch_no')]; ?></p></td>
                <td  align="center" width="40"><p><? echo $batch[csf('extention_no')]; ?></p></td>
                <td align="right" width="70"><? echo number_format($batch[csf('batch_qnty')],2);  ?></td>
                <td align="right" width="70" >


                    <? 
                    //if ($roll_maintained==1 && $cbo_type!=0) {
                    if(($page_upto==7 || $page_upto>7) && $roll_maintained==1)
                    {
                        // $batch_prod_qty_arr[$row[csf('batch_id')]][$row[csf('prod_id')]][$row[csf('dia_type')]][$row[csf('roll_id')]][$row[csf('barcode_no')]]['sliting']
                        
                    echo number_format($batch_prod_qty_arr[$batch[csf('id')]][$batch[csf('prod_id')]][$batch[csf('width_dia_type')]][$batch[csf('roll_no')]][$batch[csf('barcode_no')]][$insert_date]['sliting'],2);  
                    }else
                    {
                       // echo $page_upto.'g';
                        echo number_format($batch_prod_qty_arr[$batch[csf('id')]][$batch[csf('prod_id')]][$batch[csf('width_dia_type')]][$insert_date]['sliting'],2);  
                    }

                    ?>
                        
                    </td>
                <td align="left" width="50" title="<? echo $yarn_lot_arr[$batch[csf('prod_id')]][$batch[csf('po_id')]]; ?>"><p><? echo $yarn_lot_arr[$batch[csf('prod_id')]][$batch[csf('po_id')]];  ?></p></td>
               <td width="75" title="Process Start Date"><div style="width:75px; word-wrap:break-word;"><?  echo change_date_format($batch[csf('process_start_date')]).', '.$batch[csf('start_hours')].':'.$batch[csf('start_minutes')]; ?></div></td>
                <td width="75" title="Process End Date"><div style="width:75px; word-wrap:break-word;"><?  echo change_date_format($batch[csf('end_date')]).', '.$batch[csf('end_hours')].':'.$batch[csf('end_minutes')]; ?></div></td>
                 <td width="70" align="center"><div style="width:70px; word-wrap:break-word;"><?
                        $end_time=$batch[csf('end_hours')].':'.$batch[csf('end_minutes')];
                        $start_time=$batch[csf('start_hours')].':'.$batch[csf('start_minutes')];
                        
                        $new_date_time_start=($batch[csf('process_start_date')].' '.$start_time.':'.'00');
                        $new_date_time_end=($batch[csf('end_date')].' '.$end_time.':'.'00');
                        $total_time=datediff(n,$new_date_time_start,$new_date_time_end);
                        echo floor($total_time/60).":".$total_time%60; ?></div></td>
                <td align="center" width="60"><p><?   echo $batch[csf('remarks')]; ?></p>
                 </td>
                <td align="center" title="<?   if($batch[csf('batch_against')]==2) echo $batch_against[$batch[csf('batch_against')]]; ?>"><p><?  if($batch[csf('batch_against')]==2) echo $batch_against[$batch[csf('batch_against')]]; ?></p> </td>
            </tr>
            <? 
            $i++;
            $btq+=$batch[csf('batch_qnty')];
            //if ($roll_maintained==1 && $cbo_type!=0) {
            if(($page_upto==7 || $page_upto>7) && $roll_maintained==1){
                $tot_prod_btq+=$batch_prod_qty_arr[$batch[csf('id')]][$batch[csf('prod_id')]][$batch[csf('width_dia_type')]][$batch[csf('roll_no')]][$batch[csf('barcode_no')]][$insert_date]['sliting'];
            }
            else
            {
                $tot_prod_btq+=$batch_prod_qty_arr[$batch[csf('id')]][$batch[csf('prod_id')]][$batch[csf('width_dia_type')]][$insert_date]['sliting'];  
            }

            $grand_btq+=$batch[csf('batch_qnty')];
              //  if ($roll_maintained==1 && $cbo_type!=0) {
                  if(($page_upto==7 || $page_upto>7) && $roll_maintained==1){
                    $grand_tot_prod_btq+=$batch_prod_qty_arr[$batch[csf('id')]][$batch[csf('prod_id')]][$batch[csf('width_dia_type')]][$batch[csf('roll_no')]][$batch[csf('barcode_no')]][$insert_date]['sliting'];
                }
                else
                {
                    $grand_tot_prod_btq+=$batch_prod_qty_arr[$batch[csf('id')]][$batch[csf('prod_id')]][$batch[csf('width_dia_type')]][$insert_date]['sliting'];

                }
            } //batchdata froeach

                if($group_by!=0)
                        { 
                        ?>  
                        <tr class="tbl_bottom">
                        <td width="30"></td>
                         <? if($group_by==2 || $group_by==0){ ?>
                         <td width="80">&nbsp;</td>
                         <? } ?>
                        <? if($group_by==1 || $group_by==0){ ?>
                        <td width="80">&nbsp;</td>  
                         <? } 
                         ?>
                        <td width="50">&nbsp;</td>
                        <td width="100">&nbsp;</td>
                        <td width="80">&nbsp;</th>  
                        <td width="100">&nbsp;</th>  
                        <td width="60">&nbsp;</td>
                        <td width="70">&nbsp;</td>
                        <td width="90">&nbsp;</td>
                        <td width="100">&nbsp;</td>
                        <td width="70">&nbsp;</td>
                        <td width="75">&nbsp;</td>
                        <td width="80">&nbsp;</td>
                       
                        <td  colspan="2">Sub Total</td>
                        <td width="70" align="right"><?  echo number_format($btq,2);?></td>
                        <td width="70" align="right"><?  echo number_format($tot_prod_btq,2);?></td>
                        <td width="50">&nbsp;</td>
                        <td width="75">&nbsp;</td>
                        <td width="75">&nbsp;</td>
                        <td width="70">&nbsp;</td>
                        <td width="60">&nbsp;</td>
                        <td>&nbsp;</td>
                        </tr>
                        <?
                        }
                
             ?>
                </tbody>
            </table>
            <table class="rpt_table" width="1815" cellpadding="0" cellspacing="0" border="1" rules="all" style="float: left;" id="report_table_footer">
            <tfoot>
            <tr>
                <th width="30">&nbsp;</th>
                 <? if($group_by==2 || $group_by==0){ ?>
                    <th width="80">&nbsp;</th>
                    <? } ?>
                    <? if($group_by==1 || $group_by==0){ ?>
                    <th width="80">&nbsp;</th> 
                    <? } 
                    ?>
                <th width="50">&nbsp;</th>
                <th width="100">&nbsp;</th>
                <th width="80">&nbsp;</th>
                <th width="100">&nbsp;</th>
                <th width="60">&nbsp;</th> 
                <th width="70">&nbsp;</th> 
                <th width="90">&nbsp;</th>
                <th width="100">&nbsp;</th>
                <th width="70">&nbsp;</th>
                <th width="75">&nbsp;</th>
                <th width="80">&nbsp;</th>
                <th width="90">&nbsp;</th>
                <th width="40">&nbsp;</th>
                <th width="70"><? echo number_format($grand_btq,2); ?></th>
                <th width="70"><? echo number_format($grand_tot_prod_btq,2); ?></th>
                <th width="50">&nbsp;</th>
                <th width="75">&nbsp;</th>
                <th width="75">&nbsp;</th>
                <th width="70">&nbsp;</th>
                <th width="60">&nbsp;</th>
                <th>&nbsp;</th>
            </tr>
            </tfoot>
            </table>
            </div>
            </fieldset>
            </div>
            <?
        }
        else if($cbo_type==3)// Drying Stentering
        {
            ?>
            <div>
            <fieldset style="width:1735px;">
            <div align="center"><strong> <? echo $company_library[$company]; ?> </strong><br> <strong><? echo $search_by_arr[$cbo_type];?> </strong><br>
            <?
                echo  change_date_format($date_from).' '.To.' '.change_date_format($date_to);
            ?>
             </div>
             <table class="rpt_table" width="1835" cellpadding="0" cellspacing="0" border="1" rules="all" align="center" id="table_header_1">
            <thead>
            <tr>
                <th width="30">SL</th>
            <? if($group_by==2 || $group_by==0){ ?>
                <th width="80">Machine</th>
                <? } ?>
                <? if($group_by==1 || $group_by==0){ ?>
                <th width="80">Floor</th> 
                <? } 
                ?> 
                <th width="50">Shift</th>
                <th width="100">Buyer</th>
                <th width="80">Job</th> 
                <th width="100">Booking</th> 
                <th width="60">File No</th> 
                <th width="70">Ref. No</th>
                <th width="90">Order No</th>
                <th width="100">Fabrics Desc</th>
                <th width="75">GSM</th>
                <th width="70">Dia/ Width Type</th>
                <th width="80">Color Name</th>
                <th width="90">Batch No</th>
                <th width="40">Extn. No</th>
                <th width="70">Batch Qty.</th>
                <th width="70">Prod Qty.</th>
                <th width="50">Lot No</th> 
                <th width="75">Start Date & Time</th> 
                <th width="75">End Date & Time</th>
                <th width="70">Time Used</th>
                <th width="60">Remark</th>
                <th>Reprocess</th>
            </tr>
            </thead>
            </table>
            <div style=" max-height:350px; width:1835px; overflow-y:scroll;;" id="scroll_body">
            <table class="rpt_table" id="table_body" width="1815" cellpadding="0" cellspacing="0" border="1" rules="all" align="center">
            <tbody>
            <? 
            $i=1;
            $f=0;$k=1;
            $btq=0;$tot_prod_btq=$grand_btq=$grand_tot_prod_btq=0;
            $batch_chk_arr=array();
            foreach($batchdata as $batch) 
            { 
            if ($i%2==0)  
            $bgcolor="#E9F3FF";
            else
            $bgcolor="#FFFFFF";
            $order_id=$batch[csf('po_id')];
            $color_id=$batch[csf('color_id')];
            $desc=explode(",",$batch[csf('item_description')]);
            $insert_date = "'".$batch[csf('insert_date')]."'"; 
            $po_number=implode(",",array_unique(explode(",",$batch[csf('po_number')])));
            if(($page_upto==5 || $page_upto>5) && $roll_maintained==1)
            {
                        $batch_prod_qty=$batch_prod_qty_arr[$batch[csf('id')]][$batch[csf('prod_id')]][$batch[csf('width_dia_type')]][$batch[csf('roll_no')]][$batch[csf('barcode_no')]][$insert_date]['drying'];
             }
             else
             {
                  $batch_prod_qty=$batch_prod_qty_arr3[$batch[csf('id')]][$batch[csf('prod_id')]][$batch[csf('width_dia_type')]][$batch[csf('end_date')]][$insert_date]['drying'];
             }  
            if($batch_prod_qty>0)
            {
            if($group_by!=0)
            {
                if($group_by==1)
                {
                    $group_value=$batch[csf('floor_id')];
                    $group_name="Floor";
                    $group_dtls_value=$floor_arr[$batch[csf('floor_id')]];
                }
                
                else if($group_by==2)
                {
                    $group_value=$batch[csf('machine_id')];
                    $group_name="Machine";
                    $group_dtls_value=$machine_arr[$batch[csf('machine_id')]];
                }
                if (!in_array($group_value,$group_by_arr) )
                    {
                        if($k!=1)
                        { 
                        ?>  
                        <tr class="tbl_bottom">
                        <td width="30">&nbsp;</td>
                    <? if($group_by==2 || $group_by==0){ ?>
                        <td width="80">&nbsp;</td>
                        <? } ?>
                        <? if($group_by==1 || $group_by==0){ ?>
                        <td width="80">&nbsp;</td> 
                        <? } 
                        ?> 
                        <td width="50">&nbsp;</td>
                        <td width="100">&nbsp;</td>
                        <td width="80">&nbsp;</td> 
                        <td width="100">&nbsp;</td> 
                        <td width="60">&nbsp;</td> 
                        <td width="70">&nbsp;</td>
                        <td width="90">&nbsp;</td>
                        <td width="100">&nbsp;</td>
                        <td width="75">&nbsp;</td>
                        <td width="70"></td>
                        <td width="80"></td>
                        <td  colspan="2" align="right">Sub Total</td>
                      
                        <td width="70"><? echo number_format($btq,2); ?></td>
                        <td width="70"><? echo number_format($tot_prod_btq,2); ?></td>
                        <td width="50">&nbsp;</td> 
                        <td width="75">&nbsp;</td> 
                        <td width="75">&nbsp;</td>
                        <td width="70">&nbsp;</td>
                        <td width="60">&nbsp;</td>
                        <td>&nbsp;</td>
                        </tr>
                        <tr bgcolor="#EFEFEF">
                            <td colspan="24" align="left" ><b><? echo $group_name; ?> : <? echo $group_dtls_value; ?></b></td>
                        </tr>
                        <?
                            unset($btq);unset($tot_prod_btq);
                        }
                        $group_by_arr[]=$group_value;            
                        $k++;
                    }
            }  
            ?>
            <tr bgcolor="<? echo $bgcolor;?>" onClick="change_color('tr<? echo $i;?>','<? echo $bgcolor;?>')" id="tr<? echo $i;?>">
                      <? if (!in_array($batch[csf('batch_no')],$batch_chk_arr) )
                        { $f++;
                            ?>
                <td width="30"><? echo $f; ?></td>
                
                 <? if($group_by==2 || $group_by==0){ ?>
               <td  align="center" width="80" ><p><? echo $machine_arr[$batch[csf('machine_id')]]; ?></p></td>
                <?
                 }
                 if($group_by==1 || $group_by==0){ ?>
               <td width="80"><p><? echo $floor_arr[$batch[csf('floor_id')]]; ?></p></td>
                <? } ?>
                 <td  align="center" width="50"><p><? echo $shift_name[$batch[csf('shift_name')]]; ?></p></td>
                <td  width="100" title="<? echo $buyer_arr[$batch[csf('buyer_name')]]; ?>"><p><? echo $buyer_arr[$batch[csf('buyer_name')]]; ?></p></td>
                <td  width="80" title="<? //echo $color_library[$batch[csf('color_id')]]; ?>"><p><? echo $batch[csf('job_no_prefix_num')]; ?></p></td>
                <td  width="100"><p><? echo $batch[csf('booking_no')]; ?></p></td>
                <td  width="60"><p><? echo $batch[csf('file_no')]; ?></p></td>
                <td  width="70"><p><? echo $batch[csf('grouping')]; ?></p></td>
                <td width="90"><div style="width:90px; word-wrap:break-word;"><? echo $po_number; ?></div></td>
                    <?  
                    $batch_chk_arr[]=$batch[csf('batch_no')];
                        } 
                        else
                           { ?>
                <td width="30"><? //echo $sl; ?></td>
                 <? if($group_by==2 || $group_by==0){ ?>
               <td  align="center" width="80" ><p><? //echo $machine_arr[$batch[csf('machine_id')]]; ?></p></td>
                <?
                 }
                 if($group_by==1 || $group_by==0){ ?>
               <td width="80"><p><? //echo $floor_arr[$batch[csf('floor_id')]]; ?></p></td>
                <? } ?>
                <td  align="center" width="50"><p><? //echo $machine_id; ?></p></td>
                <td  width="100"><p><? //echo $buyer_arr[$batch[csf('buyer_name')]]; ?></p></td>
                <td  width="80" title="<? //echo $color_library[$batch[csf('color_id')]]; ?>"><p><? //echo $color_library[$batch[csf('color_id')]]; ?></p></td>
                <td  width="100"><p><? //echo $special_row[csf('booking_no')]; ?></p></td>
                <td  width="60"><p><? //echo $special_row[csf('file_no')]; ?></p></td>
                <td  width="70"><p><? //echo $special_row[csf('grouping')]; ?></p></td>
                <td width="90"><p><? //echo $batch[csf('po_number')]; ?></p></td>
                        <? }
                        ?>
                <td  width="100" title="<? echo $desc[0]; ?>"><p><? echo $batch[csf('item_description')]; ?></p></td>
                <td  width="75" title="<? echo   $desc[2]; ?>"><p><? echo  $desc[2]; ?></p></td>
                <td  width="70" title="<? ?>"><p><? echo $fabric_typee[$batch[csf('width_dia_type')]];;?></p></td>
                <td  width="80" title="<? echo $color_library[$batch[csf('color_id')]]; ?>"><p><? echo $color_library[$batch[csf('color_id')]]; ?></p></td>
                <td  align="center" width="90" title="<? echo $batch[csf('batch_no')]; ?>"><p><? echo $batch[csf('batch_no')]; ?></p></td>
                <td  align="center" width="40" title="<? echo $batch[csf('extention_no')]; ?>"><p><? echo $batch[csf('extention_no')]; ?></p></td>
                <td align="right" width="70" title="<? echo $batch[csf('batch_qnty')];  ?>"><? echo number_format($batch[csf('batch_qnty')],2);  ?></td>
                   <td align="right" width="70" >
                    <?
                    //if ($roll_maintained==1 && $cbo_type!=0) {
                    if(($page_upto==5 || $page_upto>5) && $roll_maintained==1){
                        echo number_format($batch_prod_qty_arr[$batch[csf('id')]][$batch[csf('prod_id')]][$batch[csf('width_dia_type')]][$batch[csf('roll_no')]][$batch[csf('barcode_no')]][$insert_date]['drying'],2);
                     }
                     else
                     {
                         echo number_format($batch_prod_qty_arr3[$batch[csf('id')]][$batch[csf('prod_id')]][$batch[csf('width_dia_type')]][$batch[csf('end_date')]][$insert_date]['drying'],2);
                     }  
                     ?>
                         
                     </td>
             
                <td align="left" width="50" title="<? echo $yarn_lot_arr[$batch[csf('prod_id')]][$batch[csf('po_id')]]; ?>"><p><? echo $yarn_lot_arr[$batch[csf('prod_id')]][$batch[csf('po_id')]];  ?></p></td>
               <td width="75" title="Process Start Date"><div style="width:75px; word-wrap:break-word;"><?  echo change_date_format($batch[csf('process_start_date')]).', '.$batch[csf('start_hours')].':'.$batch[csf('start_minutes')]; ?></div></td>
                <td width="75" title="Process End Date"><div style="width:75px; word-wrap:break-word;"><?  echo change_date_format($batch[csf('end_date')]).', '.$batch[csf('end_hours')].':'.$batch[csf('end_minutes')]; ?></div></td>
                 <td width="70" align="center"><div style="width:70px; word-wrap:break-word;"><?
                        $end_time=$batch[csf('end_hours')].':'.$batch[csf('end_minutes')];
                        $start_time=$batch[csf('start_hours')].':'.$batch[csf('start_minutes')];
                        
                        $new_date_time_start=($batch[csf('process_start_date')].' '.$start_time.':'.'00');
                        $new_date_time_end=($batch[csf('end_date')].' '.$end_time.':'.'00');
                        $total_time=datediff(n,$new_date_time_start,$new_date_time_end);
                        echo floor($total_time/60).":".$total_time%60; ?></div></td>
                <td align="center" width="60"><p> <? echo $batch[csf('remarks')]; ?>
                    </p>
                 </td>
                <td align="center" title="<?   if($batch[csf('batch_against')]==2) echo $batch_against[$batch[csf('batch_against')]]; ?>"><p><?  if($batch[csf('batch_against')]==2) echo $batch_against[$batch[csf('batch_against')]]; ?></p> </td>
            </tr>
            <? 
            $i++;
            $btq+=$batch[csf('batch_qnty')];
            //if ($roll_maintained==1 && $cbo_type!=0) {
                if(($page_upto==5 || $page_upto>5) && $roll_maintained==1){
                $tot_prod_btq+=$batch_prod_qty_arr[$batch[csf('id')]][$batch[csf('prod_id')]][$batch[csf('width_dia_type')]][$batch[csf('roll_no')]][$batch[csf('barcode_no')]][$insert_date]['drying'];
            }
            else
            {
               $tot_prod_btq+=$batch_prod_qty_arr3[$batch[csf('id')]][$batch[csf('prod_id')]][$batch[csf('width_dia_type')]][$batch[csf('end_date')]][$insert_date]['drying'];
             
            }
            $grand_btq+=$batch[csf('batch_qnty')];
               // if ($roll_maintained==1 && $cbo_type!=0) {
                   if(($page_upto==5 || $page_upto>5) && $roll_maintained==1){
                    $grand_tot_prod_btq+=$batch_prod_qty_arr[$batch[csf('id')]][$batch[csf('prod_id')]][$batch[csf('width_dia_type')]][$batch[csf('roll_no')]][$batch[csf('barcode_no')]][$insert_date]['drying'];
                }
                else
                {
                    $grand_tot_prod_btq+=$batch_prod_qty_arr3[$batch[csf('id')]][$batch[csf('prod_id')]][$batch[csf('width_dia_type')]][$batch[csf('end_date')]][$insert_date]['drying'];
                }

            } //batchdata froeach
            } //Zero value check End
                        if($group_by!=0)
                        {
                 ?>
                         <tr class="tbl_bottom">
                        <td width="30">&nbsp;</td>
                    <? if($group_by==2 || $group_by==0){ ?>
                        <td width="80">&nbsp;</td>
                        <? } ?>
                        <? if($group_by==1 || $group_by==0){ ?>
                        <td width="80">&nbsp;</td> 
                        <? } 
                        ?> 
                        <td width="50">&nbsp;</td>
                        <td width="100">&nbsp;</td>
                        <td width="80">&nbsp;</td> 
                        <td width="100">&nbsp;</td> 
                        <td width="60">&nbsp;</td> 
                        <td width="70">&nbsp;</td>
                        <td width="90">&nbsp;</td>
                        <td width="100">&nbsp;</td>
                        <td width="75">&nbsp;</td>
                        <td width="70">&nbsp;</td>
                        <td width="80">&nbsp;</td>
                        <td  colspan="2" align="right">Sub Total</td>
                     
                        <td width="70"><? echo number_format($btq,2); ?></td>
                        <td width="70"><? echo number_format($tot_prod_btq,2); ?></td>
                        <td width="50">&nbsp;</td> 
                        <td width="75">&nbsp;</td> 
                        <td width="75">&nbsp;</td>
                        <td width="70">&nbsp;</td>
                        <td width="60">&nbsp;</td>
                        <td>&nbsp;</td>
                        </tr>
                        <?
                        }
                        ?>
                </tbody>
            </table>
            <table class="rpt_table" width="1815" cellpadding="0" cellspacing="0" border="1" rules="all" align="center" id="report_table_footer">
            <tfoot>
            <tr>
                <th width="30">&nbsp;</th>
               <? if($group_by==2 || $group_by==0){ ?>
                <th width="80">&nbsp;</th>
                <? } ?>
                <? if($group_by==1 || $group_by==0){ ?>
                <th width="80">&nbsp;</th> 
                <? } 
                ?> 
                <th width="50">&nbsp;</th>
                <th width="100">&nbsp;</th>
                <th width="80">&nbsp;</th>
                <th width="100">&nbsp;</th>
                <th width="60">&nbsp;</th>
                <th width="70">&nbsp;</th>
                <th width="90">&nbsp;</th>
                <th width="100">&nbsp;</th>
                <th width="75">&nbsp;</th>
                <th width="70">&nbsp;</th>
                <th width="80">&nbsp;</th>
                <th  width="90">Grand Total</th>
                <th width="40">&nbsp;</th>
                <th width="70"><? echo number_format($grand_btq,2); ?></th>
                <th width="70"><? echo number_format($grand_tot_prod_btq,2); ?></th>
                <th width="50">&nbsp;</th>
                <th width="75">&nbsp;</th>
                <th width="75">&nbsp;</th>
                <th width="70">&nbsp;</th>
                <th width="60">&nbsp;</th>
                <th>&nbsp;</th>
            </tr>
            </tfoot>
            </table>
            </div>
            </fieldset>
            </div>
            <?
        }
        else if($cbo_type==9)// Stentering
        {
            ?>
            <div>
            <fieldset style="width:1840px;">
            <div align="center"><strong> <? echo $company_library[$company]; ?> </strong><br> <strong><? echo $search_by_arr[$cbo_type];?> </strong><br>
            <?
                echo  change_date_format($date_from).' '.To.' '.change_date_format($date_to);
            ?>
             </div>
             <table class="rpt_table" width="1860" cellpadding="0" cellspacing="0" border="1" rules="all" align="center" id="table_header_1">
            <thead>
            <tr>
                <th width="30">SL</th>
                 <? if($group_by==2 || $group_by==0){ ?>
                <th width="80">M/C No</th>
                <? } ?>
                <? if($group_by==1 || $group_by==0){ ?>
                <th width="80">Floor</th> 
                <? } 
                ?> 
                
                <th width="50">Shift</th>
                <th width="100">Buyer</th>
                <th width="80">Job</th>
                <th width="100">Booking</th>
                 <th width="60">File No</th> 
                <th width="70">Ref. No</th>
                <th width="90">Order No</th>
                <th width="100">Fabrics Desc</th>
                <th width="75">GSM</th>
                <th width="70">Dia/ Width Type</th>
                <th width="80">Color Name</th>
                <th width="90">Batch No</th>
                <th width="40">Extn. No</th>
                <th width="70">Batch Qty.</th>
                <th width="70">Prod. Qty.</th>
                <th width="50">Lot No</th>
                <th width="100">Start Date & Time</th>
                <th width="100">End Date & Time</th>
                <th width="70">Time Used</th>
                <th width="60">Remark</th>
                <th>Reprocess</th>
            </tr>
            </thead>
            </table>
            <div style=" max-height:350px; width:1860px; overflow-y:scroll;;" id="scroll_body">
            <table class="rpt_table" id="table_body" width="1840" cellpadding="0" cellspacing="0" border="1" rules="all" align="center">
            <tbody>
            <? 
            $i=1;$f=0;$k=1;
            $btq=0;$tot_prod_btq=$grand_btq=$grand_tot_prod_btq=0;
            $batch_chk_arr=array();$group_by_arr=array();
            foreach($batchdata as $batch)
            { 
            if ($i%2==0)  
            $bgcolor="#E9F3FF";
            else
            $bgcolor="#FFFFFF";
            $order_id=$batch[csf('po_id')];
            $color_id=$batch[csf('color_id')];
            $desc=explode(",",$batch[csf('item_description')]);
            $insert_date = "'".$batch[csf('insert_date')]."'"; 
            $po_number=implode(",",array_unique(explode(",",$batch[csf('po_number')])));
            if($group_by!=0)
            {
                if($group_by==1)
                {
                    $group_value=$batch[csf('floor_id')];
                    $group_name="Floor";
                    $group_dtls_value=$floor_arr[$batch[csf('floor_id')]];
                }
                
                else if($group_by==2)
                {
                    $group_value=$batch[csf('machine_id')];
                    $group_name="Machine";
                    $group_dtls_value=$machine_arr[$batch[csf('machine_id')]];//
                }
                if (!in_array($group_value,$group_by_arr) )
                    {
                        if($k!=1)
                        {
                        ?>
                        <tr class="tbl_bottom">
                             <td width="30">&nbsp;</td>
                             <? if($group_by==2 || $group_by==0){ ?>
                            <td width="80">&nbsp;</td>
                            <? } ?>
                            <? if($group_by==1 || $group_by==0){ ?>
                            <td width="80">&nbsp;</td> 
                            <? } 
                            ?> 
                            <td width="50">&nbsp;</td>
                            <td width="100">&nbsp;</td>
                            <td width="80">&nbsp;</td>
                            <td width="100">&nbsp;</td>
                            <td width="60">&nbsp;</td> 
                            <td width="70">&nbsp;</td>
                            <td width="90">&nbsp;</td>
                            <td width="100">&nbsp;</td>
                            <td width="75">&nbsp;</td>
                            <td width="70">&nbsp;</td>
                            <td width="80">&nbsp;</td>
                            <td width="130" colspan="2">Sub Total</td>
                          
                            <td width="70"><? echo number_format($btq,2);?></td>
                            <td width="70"><? echo number_format($tot_prod_btq,2)?></td>
                            <td width="50">&nbsp;</td>
                            <td width="100">&nbsp;</td>
                            <td width="100">&nbsp;</td>
                            <td width="70">&nbsp;</td>
                            <td width="60">&nbsp;</td>
                            <td>&nbsp;</td>
                        </tr>   
                        <tr bgcolor="#EFEFEF">
                            <td colspan="24" align="left" ><b><? echo $group_name; ?> : <? echo $group_dtls_value; ?></b></td>
                        </tr>
                        <?
                        unset($btq);unset($tot_prod_btq);
                        }
                        $group_by_arr[]=$group_value; 
                       $k++;
                    }
            }
            $stenter_grouping_arr_val=$batch[csf('batch_no')].$batch[csf('machine_id')].$batch[csf('floor_id')]; 
            ?>
            <tr bgcolor="<? echo $bgcolor;?>" onClick="change_color('tr<? echo $i;?>','<? echo $bgcolor;?>')" id="tr<? echo $i;?>">
                 <? if (!in_array($stenter_grouping_arr_val,$batch_chk_arr) )
                        { $f++;
                            ?>
                <td width="30"><? echo $f; ?></td>
               
                 <? if($group_by==2 || $group_by==0){ ?>
               <td  align="center" width="80"><p><? echo $machine_arr[$batch[csf('machine_id')]]; ?></p></td>
                <?
                 }
                 if($group_by==1 || $group_by==0){ ?>
               <td width="80"><p><? echo $floor_arr[$batch[csf('floor_id')]]; ?></p></td>
                <? } ?>
                 <td  align="center" width="50"><p><? echo $shift_name[$batch[csf('shift_name')]]; ?></p></td>
                <td  width="100" title="<? echo $buyer_arr[$batch[csf('buyer_name')]]; ?>"><p><? echo $buyer_arr[$batch[csf('buyer_name')]]; ?></p></td>
                <td  width="80" title="<? //echo $color_library[$batch[csf('color_id')]]; ?>"><p><? echo $batch[csf('job_no_prefix_num')]; ?></p></td>
                <td  width="100"><p><? echo $batch[csf('booking_no')]; ?></p></td>
                <td  width="60"><p><? echo $batch[csf('file_no')]; ?></p></td>
                <td  width="70"><p><? echo $batch[csf('grouping')]; ?></p></td>
                <td width="90"><div style="width:90px; word-wrap:break-word;"><? echo $po_number; ?></div></td>
                    <?  
                    $batch_chk_arr[]=$stenter_grouping_arr_val;
                        } 
                        else
                           { ?>
                <td width="30"><? //echo $sl; ?></td>
                 <? if($group_by==2 || $group_by==0){ ?>
               <td  align="center" width="80"><p><? //echo $machine_arr[$batch[csf('machine_id')]]; ?></p></td>
                <?
                 }
                 if($group_by==1 || $group_by==0){ ?>
               <td width="80"><p><? //echo $floor_arr[$batch[csf('floor_id')]]; ?></p></td>
                <? } ?>
                <td  align="center" width="50"><p><? //echo $machine_id; ?></p></td>
                <td  width="100"><p><? //echo $buyer_arr[$batch[csf('buyer_name')]]; ?></p></td>
                <td  width="80" title="<? //echo $color_library[$batch[csf('color_id')]]; ?>"><p><? //echo $color_library[$batch[csf('color_id')]]; ?></p></td>
                <td  width="100"><p><? //echo $special_row[csf('booking_no')]; ?></p></td>
                <td  width="60"><p><? //echo $special_row[csf('file_no')]; ?></p></td>
                <td  width="70"><p><? //echo $special_row[csf('grouping')]; ?></p></td>
                <td width="90"><p><? //echo $batch[csf('po_number')]; ?></p></td>
                        <? }
                        ?>
                <td  width="100" title="<? echo $desc[0]; ?>"><p><? echo $batch[csf('item_description')]; ?></p></td>
                <td  width="75" title="<? echo   $desc[2]; ?>"><p><? echo  $desc[2]; ?></p></td>
                <td  width="70" title="<? ?>"><p><? echo $fabric_typee[$batch[csf('width_dia_type')]];;?></p></td>
                <td  width="80" title="<? echo $color_library[$batch[csf('color_id')]]; ?>"><p><? echo $color_library[$batch[csf('color_id')]]; ?></p></td>
                <td  align="center" width="90" title="<? echo $batch[csf('batch_no')]; ?>"><p><? echo $batch[csf('batch_no')]; ?></p></td>
                <td  align="center" width="40" title="<? echo $batch[csf('extention_no')]; ?>"><p><? echo $batch[csf('extention_no')]; ?></p></td>
                <td align="right" width="70" title="<? echo $batch[csf('batch_qnty')];  ?>"><p><? echo number_format($batch[csf('batch_qnty')],2);  ?></p></td>
                 <td align="right" width="70" >
                    <? 
                   // if ($roll_maintained==1 && $cbo_type!=0) {
                       if(($page_upto==4 || $page_upto>4) && $roll_maintained==1){
                        echo number_format($batch_prod_qty_arr[$batch[csf('id')]][$batch[csf('prod_id')]][$batch[csf('width_dia_type')]][$batch[csf('roll_no')]][$batch[csf('barcode_no')]][$insert_date]['stenter'],2); 
                    }
                    else
                    {
                        echo number_format($batch_prod_qty_arr[$batch[csf('id')]][$batch[csf('prod_id')]][$batch[csf('width_dia_type')]][$insert_date]['stenter'],2); 
                    } 
                    ?>
                        
                    </td>
                <td align="left" width="50" title="<? echo $yarn_lot_arr[$batch[csf('prod_id')]][$batch[csf('po_id')]]; ?>"><p><? echo $yarn_lot_arr[$batch[csf('prod_id')]][$batch[csf('po_id')]];  ?></p></td>
               <td width="100" title="Process Start Date"><div style="width:100px; word-wrap:break-word;"><?  echo change_date_format($batch[csf('process_start_date')]).', '.$batch[csf('start_hours')].':'.$batch[csf('start_minutes')]; ?></div></td>
                <td width="100" title="Process End Date"><div style="width:100px; word-wrap:break-word;"><?  echo change_date_format($batch[csf('end_date')]).', '.$batch[csf('end_hours')].':'.$batch[csf('end_minutes')]; ?></div></td>
                 <td width="70" align="center"><div style="width:70px; word-wrap:break-word;"><?
                        $end_time=$batch[csf('end_hours')].':'.$batch[csf('end_minutes')];
                        $start_time=$batch[csf('start_hours')].':'.$batch[csf('start_minutes')];
                        
                        $new_date_time_start=($batch[csf('process_start_date')].' '.$start_time.':'.'00');
                        $new_date_time_end=($batch[csf('end_date')].' '.$end_time.':'.'00');
                        $total_time=datediff(n,$new_date_time_start,$new_date_time_end);
                        echo floor($total_time/60).":".$total_time%60; ?></div></td>
                <td align="center" width="60"><p> <? echo $batch[csf('remarks')]; ?>
                    </p>
                 </td>
                <td align="center" title="<?   if($batch[csf('batch_against')]==2) echo $batch_against[$batch[csf('batch_against')]]; ?>"><p><?  if($batch[csf('batch_against')]==2) echo $batch_against[$batch[csf('batch_against')]]; ?></p> </td>
            </tr>
            <? 
            $i++;
            $btq+=$batch[csf('batch_qnty')];
            //if ($roll_maintained==1 && $cbo_type!=0) {
                 if(($page_upto==4 || $page_upto>4) && $roll_maintained==1){
                $tot_prod_btq+=$batch_prod_qty_arr[$batch[csf('id')]][$batch[csf('prod_id')]][$batch[csf('width_dia_type')]][$batch[csf('roll_no')]][$batch[csf('barcode_no')]][$insert_date]['stenter'];
            }
            else
            {
               $tot_prod_btq+=$batch_prod_qty_arr[$batch[csf('id')]][$batch[csf('prod_id')]][$batch[csf('width_dia_type')]][$insert_date]['stenter']; 
            }
            $grand_btq+=$batch[csf('batch_qnty')];
                //if ($roll_maintained==1 && $cbo_type!=0) {
                     if(($page_upto==4 || $page_upto>4) && $roll_maintained==1){
                    $grand_tot_prod_btq+=$batch_prod_qty_arr[$batch[csf('id')]][$batch[csf('prod_id')]][$batch[csf('width_dia_type')]][$batch[csf('roll_no')]][$batch[csf('barcode_no')]][$insert_date]['stenter'];
                }
                else
                {
                  $grand_tot_prod_btq+=$batch_prod_qty_arr[$batch[csf('id')]][$batch[csf('prod_id')]][$batch[csf('width_dia_type')]][$insert_date]['stenter']; 
                }
            } //batchdata froeach

                        if($group_by!=0)
                        {
                        ?>
                        <tr class="tbl_bottom">
                             <td width="30">&nbsp;</td>
                             <? if($group_by==2 || $group_by==0){ ?>
                            <td width="80">&nbsp;</td>
                            <? } ?>
                            <? if($group_by==1 || $group_by==0){ ?>
                            <td width="80">&nbsp;</td> 
                            <? } 
                            ?> 
                            <td width="50">&nbsp;</td>
                            <td width="100">&nbsp;</td>
                            <td width="80">&nbsp;</td>
                            <td width="100">&nbsp;</td>
                            <td width="60">&nbsp;</td> 
                            <td width="70">&nbsp;</td>
                            <td width="90">&nbsp;</td>
                            <td width="100">&nbsp;</td>
                            <td width="75">&nbsp;</td>
                            <td width="70">&nbsp;</td>
                            <td width="80">&nbsp;</td>
                            <td width="130" colspan="2">Sub Total</td>
                        
                            <td width="70"><? echo number_format($btq,2);?></td>
                            <td width="70"><? echo number_format($tot_prod_btq,2)?></td>
                            <td width="50">&nbsp;</td>
                            <td width="100">&nbsp;</td>
                            <td width="100">&nbsp;</td>
                            <td width="70">&nbsp;</td>
                            <td width="60">&nbsp;</td>
                            <td>&nbsp;</td>
                        </tr>   
                        <?
                        }
             ?>
                </tbody>
                <tfoot>
            <tr>
                <th width="30">&nbsp;</th>
                 <? if($group_by==2 || $group_by==0){ ?>
                <th width="80"></th>
                <? } ?>
                <? if($group_by==1 || $group_by==0){ ?>
                <th width="80"></th> 
                <? } 
                ?> 
                <th width="50">&nbsp;</th>
                <th width="100">&nbsp;</th>
                <th width="80">&nbsp;</th>
                <th width="100">&nbsp;</th>
                <th width="60">&nbsp;</th>
                <th width="70">&nbsp;</th>
                <th width="90">&nbsp;</th>
                <th width="100">&nbsp;</th>
                <th width="75">&nbsp;</th>
                <th width="70">&nbsp;</th>
                <th width="80">&nbsp;</th>
                <th width="130" colspan="2">Grand Total</th>
              
                <th width="70"><? echo number_format($grand_btq,2); ?></th>
                <th width="70"><? echo number_format($grand_tot_prod_btq,2); ?></th>
                <th width="50">&nbsp;</th>
                <th width="100">&nbsp;</th>
                <th width="100">&nbsp;</th>
                <th width="70">&nbsp;</th>
                <th width="60">&nbsp;</th>
                <th>&nbsp;</th>
            </tr>
            </tfoot>
            </table>

            </div>
            </fieldset>
            </div>
            <?
       }
            
        else if($cbo_type==11)// Re Stentering
        {
            ?>
            <div>
            <fieldset style="width:1880px;">
            <div align="center"><strong> <? echo $company_library[$company]; ?> </strong><br> <strong><? echo $search_by_arr[$cbo_type];?> </strong><br>
            <?
                echo  change_date_format($date_from).' '.To.' '.change_date_format($date_to);
            ?>
             </div>
             <table class="rpt_table" width="1880" cellpadding="0" cellspacing="0" border="1" rules="all" id="table_header_1">
            <thead>
            <tr>
                <th width="30">SL</th>
                 <? if($group_by==2 || $group_by==0){ ?>
                <th width="80">M/C No</th>
                <? } ?>
                <? if($group_by==1 || $group_by==0){ ?>
                <th width="80">Floor</th> 
                <? } 
                ?> 
                
                <th width="50">Shift</th>
                <th width="100">Buyer</th>
                <th width="80">Job</th>
                <th width="100">Booking</th>
                 <th width="60">File No</th> 
                <th width="70">Ref. No</th>
                <th width="90">Order No</th>
                <th width="120">Fabrics Desc</th>
                <th width="75">GSM</th>
                <th width="70">Dia/ Width Type</th>
                <th width="80">Color Name</th>
                <th width="90">Batch No</th>
                <th width="40">Extn. No</th>
                <th width="70">Batch Qty.</th>
                <th width="70">Prod. Qty.</th>
                <th width="50">Lot No</th>
                <th width="100">Start Date & Time</th>
                <th width="100">End Date & Time</th>
                <th width="70">Time Used</th>
                <th width="60">Reprocess</th>
                <th>Remark</th>
            </tr>
            </thead>
            </table>
            <div style=" max-height:350px; width:1880px; overflow-y:scroll;;" id="scroll_body">
            <table class="rpt_table" id="table_body" width="1860" cellpadding="0" cellspacing="0" border="1" rules="all" >
            <tbody>
            <? 
            $i=1;$f=0;$k=1;
            $btq=0;$tot_prod_btq=0;
            $batch_chk_arr=array();$group_by_arr=array();
            foreach($batchdata as $batch)
            { 
            if ($i%2==0)  
            $bgcolor="#E9F3FF";
            else
            $bgcolor="#FFFFFF";
            $order_id=$batch[csf('po_id')];
            $color_id=$batch[csf('color_id')];
            $desc=explode(",",$batch[csf('item_description')]); 
            $insert_date = "'".$batch[csf('insert_date')]."'";
            $po_number=implode(",",array_unique(explode(",",$batch[csf('po_number')])));
            if($group_by!=0)
            {
                if($group_by==1)
                {
                    $group_value=$batch[csf('floor_id')];
                    $group_name="Floor";
                    $group_dtls_value=$floor_arr[$batch[csf('floor_id')]];
                }
                
                else if($group_by==2)
                {
                    $group_value=$batch[csf('machine_id')];
                    $group_name="Machine";
                    $group_dtls_value=$machine_arr[$batch[csf('machine_id')]];//
                }
                if (!in_array($group_value,$group_by_arr) )
                    {
                        ?>  
                        <tr bgcolor="#EFEFEF">
                            <td colspan="23" align="left" ><b><? echo $group_name; ?> : <? echo $group_dtls_value; ?></b></td>
                        </tr>
                        <?
                        $group_by_arr[]=$group_value; 
                    }
            }
            $stenter_grouping_arr_val=$batch[csf('batch_no')].$batch[csf('machine_id')].$batch[csf('floor_id')]; 
            ?>
            <tr bgcolor="<? echo $bgcolor;?>" onClick="change_color('tr<? echo $i;?>','<? echo $bgcolor;?>')" id="tr<? echo $i;?>">
                 <? if (!in_array($stenter_grouping_arr_val,$batch_chk_arr) )
                        { $f++;
                            ?>
                <td width="30"><? echo $f; ?></td>
               
                 <? if($group_by==2 || $group_by==0){ ?>
               <td  align="center" width="80"><p><? echo $machine_arr[$batch[csf('machine_id')]]; ?></p></td>
                <?
                 }
                 if($group_by==1 || $group_by==0){ ?>
               <td width="80"><p><? echo $floor_arr[$batch[csf('floor_id')]]; ?></p></td>
                <? } ?>
                 <td  align="center" width="50"><p><? echo $shift_name[$batch[csf('shift_name')]]; ?></p></td>
                <td  width="100" style="word-wrap:break-word" title="<? echo $buyer_arr[$batch[csf('buyer_name')]]; ?>"><p><? echo $buyer_arr[$batch[csf('buyer_name')]]; ?></p></td>
                <td  width="80" title="<? //echo $color_library[$batch[csf('color_id')]]; ?>"><p><? echo $batch[csf('job_no_prefix_num')]; ?></p></td>
                <td  width="100"><p><? echo $batch[csf('booking_no')]; ?></p></td>
                <td  width="60"><p><? echo $batch[csf('file_no')]; ?></p></td>
                <td  width="70" style="word-wrap:break-word"><p><? echo $batch[csf('grouping')]; ?></p></td>
                <td width="90"><div style="width:90px; word-wrap:break-word;"><? echo $po_number; ?></div></td>
                    <?  
                    $batch_chk_arr[]=$stenter_grouping_arr_val;
                        } 
                        else
                           { ?>
                <td width="30"><? //echo $sl; ?></td>
                 <? if($group_by==2 || $group_by==0){ ?>
               <td  align="center" width="80"><p><? //echo $machine_arr[$batch[csf('machine_id')]]; ?></p></td>
                <?
                 }
                 if($group_by==1 || $group_by==0){ ?>
               <td width="80"><p><? //echo $floor_arr[$batch[csf('floor_id')]]; ?></p></td>
                <? } ?>
                <td  align="center" width="50"><p><? //echo $machine_id; ?></p></td>
                <td  width="100"><p><? //echo $buyer_arr[$batch[csf('buyer_name')]]; ?></p></td>
                <td  width="80" title="<? //echo $color_library[$batch[csf('color_id')]]; ?>"><p><? //echo $color_library[$batch[csf('color_id')]]; ?></p></td>
                <td  width="100"><p><? //echo $special_row[csf('booking_no')]; ?></p></td>
                <td  width="60"><p><? //echo $special_row[csf('file_no')]; ?></p></td>
                <td  width="70"><p><? //echo $special_row[csf('grouping')]; ?></p></td>
                <td width="90"><p><? //echo $batch[csf('po_number')]; ?></p></td>
                        <? }
                        ?>
                <td  width="120" style="word-wrap:break-word" title="<? echo $desc[0]; ?>"><p><? echo $batch[csf('item_description')]; ?></p></td>
                <td  width="75" title="<? echo   $desc[2]; ?>"><p><? echo  $desc[2]; ?></p></td>
                <td  width="70" title="<? ?>"><p><? echo $fabric_typee[$batch[csf('dia_type')]];;?></p></td>
                <td  width="80"  title="<? echo $color_library[$batch[csf('color_id')]]; ?>"><p><? echo $color_library[$batch[csf('color_id')]]; ?></p></td>
                <td  align="center" width="90" style="word-wrap:break-word" title="<? echo $batch[csf('batch_no')]; ?>"><p><? echo $batch[csf('batch_no')]; ?></p></td>
                <td  align="center" width="40" title="<? echo $batch[csf('extention_no')]; ?>"><p><? echo $batch[csf('extention_no')]; ?></p></td>
                <td align="right" width="70" title="<? echo $batch[csf('batch_qnty')];  ?>"><p><? echo number_format($batch[csf('batch_qnty')],2);  ?></p></td>
                 <td align="right" width="70" ><? echo number_format($batch_prod_qty_arr2[$batch[csf('id')]][$batch[csf('prod_id')]][$batch[csf('dia_type')]][$insert_date]['stenter'],2);  ?></td>
                <td align="left" width="50" title="<? echo $yarn_lot_arr[$batch[csf('prod_id')]][$batch[csf('po_id')]]; ?>"><p><? echo $yarn_lot_arr[$batch[csf('prod_id')]][$batch[csf('po_id')]];  ?></p></td>
               <td width="100" title="Process Start Date"><div style="width:100px; word-wrap:break-word;"><?  echo change_date_format($batch[csf('process_start_date')]).', '.$batch[csf('start_hours')].':'.$batch[csf('start_minutes')]; ?></div></td>
                <td width="100" title="Process End Date"><div style="width:100px; word-wrap:break-word;"><?  echo change_date_format($batch[csf('end_date')]).', '.$batch[csf('end_hours')].':'.$batch[csf('end_minutes')]; ?></div></td>
                 <td width="70" align="center"><div style="width:70px; word-wrap:break-word;"><?
                        $end_time=$batch[csf('end_hours')].':'.$batch[csf('end_minutes')];
                        $start_time=$batch[csf('start_hours')].':'.$batch[csf('start_minutes')];
                        
                        $new_date_time_start=($batch[csf('process_start_date')].' '.$start_time.':'.'00');
                        $new_date_time_end=($batch[csf('end_date')].' '.$end_time.':'.'00');
                        $total_time=datediff(n,$new_date_time_start,$new_date_time_end);
                        echo floor($total_time/60).":".$total_time%60; ?></div></td>
                <td width="60" align="center" title="<?   echo $batch[csf('re_stenter_no')]; ?>"><p><?
                    if($batch[csf('re_stenter_no')]>0) echo 'Re Stenter'; ?></p> </td>
                <td align="center" ><p> <? echo $batch[csf('remarks')]; ?> </p> </td>
            </tr>
            <? 
            $i++;
            $btq+=$batch[csf('batch_qnty')];
            $tot_prod_btq+=$batch_prod_qty_arr2[$batch[csf('id')]][$batch[csf('prod_id')]][$batch[csf('dia_type')]][$insert_date]['stenter'];
            } //batchdata froeach
             ?>
                </tbody>
            </table>

            <table class="rpt_table" width="1860" cellpadding="0" cellspacing="0" border="1" rules="all" align="center" id="report_table_footer">
            <tfoot>
            <tr>
                <th width="30">&nbsp;</th>
                 <? if($group_by==2 || $group_by==0){ ?>
                <th width="80"></th>
                <? } ?>
                <? if($group_by==1 || $group_by==0){ ?>
                <th width="80"></th> 
                <? } 
                ?> 
                <th width="50">&nbsp;</th>
                <th width="100">&nbsp;</th>
                <th width="80">&nbsp;</th>
                <th width="100">&nbsp;</th>
                <th width="60">&nbsp;</th>
                <th width="70">&nbsp;</th>
                <th width="90">&nbsp;</th>
                <th width="120">&nbsp;</th>
                <th width="75">&nbsp;</th>
                <th width="70">&nbsp;</th>
                <th width="80">&nbsp;</th>
                <th width="90">&nbsp;</th>
                <th width="40">&nbsp;</th>
                <th width="70"><? echo number_format($btq,2); ?></th>
                <th width="70"><? echo number_format($tot_prod_btq,2); ?></th>
                <th width="50">&nbsp;</th>
                <th width="100">&nbsp;</th>
                <th width="100">&nbsp;</th>
                <th width="70">&nbsp;</th>
                
                <th  width="60">&nbsp;</th>
                <th>&nbsp;</th>
            </tr>
            </tfoot>
            </table>
            </div>
            </fieldset>
            </div>
            <?
                }
        else if($cbo_type==12)//  Re Compacting
        {
            ?>
            <div>
            <fieldset style="width:1855px;">
            <div align="center"><strong> <? echo $company_library[$company]; ?> </strong><br> <strong><? echo $search_by_arr[$cbo_type];?> </strong><br>
            <?
                echo  change_date_format($date_from).' '.To.' '.change_date_format($date_to);
            ?>
             </div>
             <table class="rpt_table" width="1855" cellpadding="0" cellspacing="0" border="1" rules="all" align="center" id="table_header_1">
            <thead>
            <tr>
                <th width="30">SL</th>
               
                 <? if($group_by==2 || $group_by==0){ ?>
                <th width="80">M/C No</th>
                <? } ?>
                <? if($group_by==1 || $group_by==0){ ?>
                <th width="80">Floor</th> 
                <? } 
                ?> 
                <th width="50">Shift</th>
                <th width="100">Buyer</th>
                <th width="80">Job</th>
                <th width="100">Booking</th>
                <th width="60">File No</th>
                <th width="70">Ref. no</th>
                <th width="90">Order No</th>
                <th width="120">Fabrics Desc</th>
                <th width="75">GSM</th>
                <th width="70">Dia/Width Type</th>
                <th width="80">Color Name</th>
                <th width="90">Batch No</th>
                <th width="40">Extn. No</th>
                <th width="70">Batch Qty.</th>
                 <th width="70">Prod. Qty.</th>
                <th width="50">Lot No</th>
                <th width="75">Start Date & Time</th>
                <th width="75">End Date & Time</th> 
                <th width="70">Time Used</th>
                <th width="60">Reprocess</th>
                  <th>Remark</th>
            </tr>
            </thead>
            </table>
            <div style=" max-height:350px; width:1855px; overflow-y:scroll;;" id="scroll_body">
            <table class="rpt_table" id="table_body" width="1835" cellpadding="0" cellspacing="0" border="1" rules="all" align="center">
            <tbody>
            <? 
            $i=1;
            $f=0;
            $btq=0;$tot_prod_btq=0;
            $batch_chk_arr=array();$group_by_arr=array();

            foreach($batchdata as $batch)
            { 
            if ($i%2==0)  
            $bgcolor="#E9F3FF";
            else
            $bgcolor="#FFFFFF";
            $order_id=$batch[csf('po_id')];
            $color_id=$batch[csf('color_id')];
            $desc=explode(",",$batch[csf('item_description')]); 
            $insert_date = "'".$batch[csf('insert_date')]."'";

            $po_id=implode(",",array_unique(explode(",",$batch[csf('po_id')]))); 

            $po_number=implode(",",array_unique(explode(",",$batch[csf('po_number')]))); 
            if($group_by!=0)
            {
                if($group_by==1)
                {
                    $group_value=$batch[csf('floor_id')];
                    $group_name="Floor";
                    $group_dtls_value=$floor_arr[$batch[csf('floor_id')]];
                }
                
                else if($group_by==2)
                {
                    $group_value=$batch[csf('machine_id')];
                    $group_name="Machine";
                    $group_dtls_value=$machine_arr[$batch[csf('machine_id')]];//
                }
                if (!in_array($group_value,$group_by_arr) )
                    {
                        //if($k!=1)
                        //{
                        ?>  
                        <tr bgcolor="#EFEFEF">
                            <td colspan="23" align="left" ><b><? echo $group_name; ?> : <? echo $group_dtls_value; ?></b></td>
                        </tr>
                        <?
                        $group_by_arr[]=$group_value; 
                        //}
                       // $k++;
                    }
            } 
            $grouping_arr_val=$batch[csf('batch_no')].$batch[csf('machine_id')].$batch[csf('floor_id')];

            ?>
            <tr bgcolor="<? echo $bgcolor;?>" onClick="change_color('tr<? echo $i;?>','<? echo $bgcolor;?>')" id="tr<? echo $i;?>">
                <? if (!in_array($grouping_arr_val,$batch_chk_arr) )
                        { $f++;
                            ?>
                <td width="30"><? echo $f; ?></td>
               
                 <? if($group_by==2 || $group_by==0){ ?>
                <td  align="center" width="80"><div style="width:80px; word-wrap:break-word;"><? echo $machine_arr[$batch[csf('machine_id')]]; ?></div></td>
                <?
                 }
                 if($group_by==1 || $group_by==0){ ?>
               <td width="80"><div style="width:80px; word-wrap:break-word;"><? echo $floor_arr[$batch[csf('floor_id')]]; ?></div></td>
                <? } ?>
                 <td  align="center" width="50"><p><? echo $shift_name[$batch[csf('shift_name')]]; ?></p></td>
                <td  width="100" title="<? echo $buyer_arr[$batch[csf('buyer_name')]]; ?>"><p><? echo $buyer_arr[$batch[csf('buyer_name')]]; ?></p></td>
                <td  width="80" title="<? //echo $color_library[$batch[csf('color_id')]]; ?>"><p><? echo $batch[csf('job_no_prefix_num')]; ?></p></td>
                <td  width="100"><p><? echo $batch[csf('booking_no')]; ?></p></td>
                <td  width="60"><p><? echo $batch[csf('file_no')]; ?></p></td>
                <td  width="70"><p><? echo $batch[csf('grouping')]; ?></p></td>
                <td width="90"><div style="width:90px; word-wrap:break-word;"><? echo $po_number; ?></div></td>
                    <?  
                    $batch_chk_arr[]=$grouping_arr_val;
                        } 
                        else
                           { ?>
                <td width="30"><? //echo $sl; ?></td>
                  <? if($group_by==2 || $group_by==0){ ?>
                <td  align="center" width="80"><p><? //echo $machine_arr[$batch[csf('machine_id')]]; ?></p></td>
                <?
                 }
                 if($group_by==1 || $group_by==0){ ?>
               <td width="80"><p><? //echo $floor_arr[$batch[csf('floor_id')]]; ?></p></td>
                <? } ?>
                <td  align="center" width="50"><p><? //echo $machine_id; ?></p></td>
                <td  width="100"><p><? //echo $buyer_arr[$batch[csf('buyer_name')]]; ?></p></td>
                <td  width="80" title="<? //echo $color_library[$batch[csf('color_id')]]; ?>"><p><? //echo $color_library[$batch[csf('color_id')]]; ?></p></td>
                <td  width="100"><p><? //echo $special_row[csf('booking_no')]; ?></p></td>
                <td  width="60"><p><? //echo $special_row[csf('file_no')]; ?></p></td>
                <td  width="70"><p><? //echo $special_row[csf('grouping')]; ?></p></td>
                <td width="90"><p><? //echo $batch[csf('po_number')]; ?></p></td>
                        <? }
                        ?>
                <td  width="120" style="word-wrap:break-word" title="<? echo $desc[0]; ?>"><p><? echo $batch[csf('item_description')]; ?></p></td>
                <td  width="70" title="<? echo   $desc[2]; ?>"><p><? echo  $desc[2]; ?></p></td>
                <td width="75" title="<? ?>"><p><? echo $fabric_typee[$batch[csf('width_dia_type')]];;?></p></td>
                <td  width="80" style="word-wrap:break-word" title="<? echo $color_library[$batch[csf('color_id')]]; ?>"><p><? echo $color_library[$batch[csf('color_id')]]; ?></p></td>
                <td  align="center" width="90" title="<? echo $batch[csf('batch_no')]; ?>"><p><? echo $batch[csf('batch_no')]; ?></p></td>
                <td  align="center" width="40" title="<? echo $batch[csf('extention_no')]; ?>"><p><? echo $batch[csf('extention_no')]; ?></p></td>
                <td align="right" width="70" title="<? echo $batch[csf('batch_qnty')];  ?>"><? echo number_format($batch[csf('batch_qnty')],2);  ?></td>
                 <td align="right" width="70" ><? echo number_format($batch_prod_qty_arr2[$batch[csf('id')]][$batch[csf('prod_id')]][$batch[csf('width_dia_type')]][$insert_date]['compact'],2);  ?></td>
                <td align="left" width="50" title="<? echo $yarn_lot_arr[$batch[csf('prod_id')]][$batch[csf('po_id')]]; ?>"><p><?
                $yarn_lot='';
                foreach($po_id as $pid)
                {
                    if($yarn_lot=='') $yarn_lot=$yarn_lot_arr[$batch[csf('prod_id')]][$pid];else  $yarn_lot.=",".$yarn_lot_arr[$batch[csf('prod_id')]][$pid];
                     
                    
                }
                 echo $yarn_lot;//$yarn_lot_arr[$batch[csf('prod_id')]][$batch[csf('po_id')]]; 
                 
                  ?></p></td>
                <td width="75" title="Process Start Date"><div style="width:75px; word-wrap:break-word;"><?  echo change_date_format($batch[csf('process_start_date')]).', '.$batch[csf('start_hours')].':'.$batch[csf('start_minutes')]; ?></div></td>
                <td width="75" title="Process End Date"><div style="width:75px; word-wrap:break-word;"><?  echo change_date_format($batch[csf('end_date')]).', '.$batch[csf('end_hours')].':'.$batch[csf('end_minutes')]; ?></div></td>
                 <td width="70" align="center"><div style="width:70px; word-wrap:break-word;"><?
                        $end_time=$batch[csf('end_hours')].':'.$batch[csf('end_minutes')];
                        $start_time=$batch[csf('start_hours')].':'.$batch[csf('start_minutes')];
                        
                        $new_date_time_start=($batch[csf('process_start_date')].' '.$start_time.':'.'00');
                        $new_date_time_end=($batch[csf('end_date')].' '.$end_time.':'.'00');
                        $total_time=datediff(n,$new_date_time_start,$new_date_time_end);
                        echo floor($total_time/60).":".$total_time%60; ?></div></td>
               
                <td width="60" align="center"><p><?  if($batch[csf('re_stenter_no')]>0) echo 'Re Compact'; ?></p> </td>
                <td align="center" ><p> <?   echo $batch[csf('remarks')];?> </p>
                 </td>
            </tr>
            <? 
            $i++;
            $btq+=$batch[csf('batch_qnty')];
            $tot_prod_btq+=$batch_prod_qty_arr2[$batch[csf('id')]][$batch[csf('prod_id')]][$batch[csf('width_dia_type')]][$insert_date]['compact'];
            } //batchdata froeach
             ?>
                </tbody>
            </table>

            <table class="rpt_table" width="1835" cellpadding="0" cellspacing="0" border="1" rules="all" align="center" id="report_table_footer">
            <tfoot>
            <tr>
                <th width="30">&nbsp;</th>
              
                 <? if($group_by==2 || $group_by==0){ ?>
                <th width="80">&nbsp;</th>
                <? } ?>
                <? if($group_by==1 || $group_by==0){ ?>
                <th width="80">&nbsp;</th> 
                <? } 
                ?> 
                <th width="50">&nbsp;</th>
                <th width="100">&nbsp;</th>
                <th width="80">&nbsp;</th>  
                <th width="100">&nbsp;</th>  
                <th width="60">&nbsp;</th>  
                <th width="70">&nbsp;</th>
                <th width="90">&nbsp;</th>
                <th width="120">&nbsp;</th>
                <th width="70">&nbsp;</th>
                <th width="75">&nbsp;</th>
                <th width="80">&nbsp;</th>
                <th width="90">&nbsp;</th>
                <th width="40">&nbsp;</th>
                <th width="70"><? echo number_format($btq,2); ?></th>
                <th width="70"><? echo number_format($tot_prod_btq,2); ?></th>
                <th width="50">&nbsp;</th>
                <th width="75">&nbsp;</th>
                <th width="75">&nbsp;</th> 
                <th width="70">&nbsp;</th>
                <th width="60">&nbsp;</th>
                 <th >&nbsp;</th>
            </tr>
            </tfoot>
            </table>
            </div>
            </fieldset>
            </div>
            <? }
        else if($cbo_type==4)// Compacting
        {
            ?>
            <div>
            <fieldset style="width:1860px;">
            <div align="center">
                <strong><? echo $company_library[$company]; ?></strong><br> 
                <strong><? echo $search_by_arr[$cbo_type];?></strong><br>
                <? echo change_date_format($date_from).' '.To.' '.change_date_format($date_to); ?>
            </div>

            <table class="rpt_table" width="1860" cellpadding="0" cellspacing="0" border="1" rules="all" align="center" id="table_header_1">
            <thead>
            <tr>
                <th width="30">SL</th>
                <? if($group_by==2 || $group_by==0){ ?>
                    <th width="80">M/C No</th>
                <? } ?>
                <? if($group_by==1 || $group_by==0){ ?>
                    <th width="80">Floor</th> 
                <? } ?> 
                <th width="50">Shift</th>
                <th width="100">Buyer</th>
                <th width="80">Job</th>
                <th width="100">Booking</th>
                <th width="60">File No</th>
                <th width="70">Ref. no</th>
                <th width="90">Order No</th>
                <th width="150">Fabrics Desc</th>
                <th width="50">GSM</th>
                <th width="70">Dia/Width Type</th>
                <th width="80">Color Name</th>
                <th width="90">Batch No</th>
                <th width="40">Extn. No</th>
                <th width="70">Batch Qty.</th>
                <th width="70">Prod. Qty.</th>
                <th width="50">Lot No</th>
                <th width="75">Start Date & Time</th>
                <th width="75">End Date & Time</th> 
                <th width="70">Time Used</th>
                <th width="60">Remark</th>
                <th>Reprocess</th>
            </tr>
            </thead>
            </table>
            <div style=" max-height:350px; width:1860px; overflow-y:scroll;;" id="scroll_body">
            <table class="rpt_table" id="table_body" width="1840" cellpadding="0" cellspacing="0" border="1" rules="all" align="center">
            <tbody>
            <? 
            $i=1;$k=1;$z=1;
            $f=0;
            $btq=0;$tot_prod_btq=$grand_btq=$grand_tot_prod_btq=$tot_prod_compact_qty=0;
            $batch_chk_arr=array();$group_by_arr=array();$prod_batch_chk_arr=array();

            foreach($batchdata as $batch)
            { 
            if ($i%2==0)  
            $bgcolor="#E9F3FF";
            else
            $bgcolor="#FFFFFF";
            $order_id=$batch[csf('po_id')];
            $color_id=$batch[csf('color_id')];
            $desc=explode(",",$batch[csf('item_description')]); 
            $insert_date = "'".$batch[csf('insert_date')]."'";
            $po_id=implode(",",array_unique(explode(",",$batch[csf('po_id')]))); 
            $po_number=implode(",",array_unique(explode(",",$batch[csf('po_number')]))); 
            $com_group_arr=$batch[csf('prod_id')].$batch[csf('id')].$batch[csf('roll_id')].$batch[csf('barcode_no')].$batch[csf('machine_id')].$batch[csf('floor_id')].$batch[csf('shift_name')].$batch[csf('width_dia_type')].$batch[csf('end_date')];

            if ($roll_maintained==1 && ($page_upto==7 || $page_upto>7) )
            { 
               
                 $prod_compact_qty=$batch_prod_qty_arr[$batch[csf('id')]][$batch[csf('prod_id')]][$batch[csf('width_dia_type')]][$batch[csf('roll_id')]][$batch[csf('barcode_no')]][$insert_date]['compact'];
                // echo number_format($prod_compact_qty,2).'<br>'; 
                   
            }
            else
            {
                
                $prod_compact_qty=$batch_prod_qty_arr3[$batch[csf('id')]][$batch[csf('prod_id')]][$batch[csf('width_dia_type')]][$batch[csf('end_date')]][$insert_date]['compact'];
                //echo $prod_compact_qty.'='.$batch[csf('end_date')].',';
                //echo $prod_compact_qty.'DD'; process_end_date
                //$batch_prod_qty_arr3[$row[csf('batch_id')]][$row[csf('prod_id')]][$row[csf('dia_type')]][$row[csf('production_date')]]['compact']
                $batch_compact_qty=$batch_prod_qty_arr3[$batch[csf('id')]][$batch[csf('prod_id')]][$batch[csf('width_dia_type')]][$batch[csf('end_date')]][$insert_date]['batch_compact_qty'];
            }
                //$com_group=$comp_row[csf('batch_no')].$batch[csf('end_date')];
                    //$tot_prod_compact_qty=$prod_compact_qty;
            if (!in_array($com_group_arr,$prod_batch_chk_arr))
            {   
                $z++;
                 $prod_batch_chk_arr[]=$com_group_arr;
                 $tot_prod_compact_qty=$prod_compact_qty;
            }
            else
            {
                 $tot_prod_compact_qty=0;
            }
            //$batch[csf('batch_qnty')]=$batch_compact_qty;
            if($group_by!=0)
            {
                if($group_by==1)
                {
                    $group_value=$batch[csf('floor_id')];
                    $group_name="Floor";
                    $group_dtls_value=$floor_arr[$batch[csf('floor_id')]];
                }
                
                else if($group_by==2)
                {
                    $group_value=$batch[csf('machine_id')];
                    $group_name="Machine";
                    $group_dtls_value=$machine_arr[$batch[csf('machine_id')]];
                }
                if (!in_array($group_value,$group_by_arr) )
                    {
                        if($k!=1)
                        {
                        ?>  
                        <tr class="tbl_bottom">
                            <td width="30">&nbsp;</td>
                           
                             <? if($group_by==2 || $group_by==0){ ?>
                            <td width="80">&nbsp;</td>
                            <? } ?>
                            <? if($group_by==1 || $group_by==0){ ?>
                            <td width="80">&nbsp;</td> 
                            <? } 
                            ?> 
                            <td width="50">&nbsp;</td>
                            <td width="100">&nbsp;</td>
                            <td width="80">&nbsp;</td>
                            <td width="100">&nbsp;</td>
                            <td width="60">&nbsp;</td>
                            <td width="70">&nbsp;</td>
                            <td width="90">&nbsp;</td>
                            <td width="150">&nbsp;</td>
                            <td width="50">&nbsp;</td>
                            <td width="70">&nbsp;</td>
                            <td width="80">&nbsp;</td>
                            <td width="90">Sub Total</td>
                            <td width="40">&nbsp;</td>
                            <td width="70"> <? echo number_format($btq,2); ?></td>
                            <td width="70"><? echo number_format($tot_prod_btq,2); ?></td>
                            <td width="50">&nbsp;</td>
                            <td width="75">&nbsp;</td>
                            <td width="75">&nbsp;</td> 
                            <td width="70">&nbsp;</td>
                            <td width="60">&nbsp;</td>
                            <td>&nbsp;</td>
                        </tr>
                        <tr bgcolor="#EFEFEF">
                            <td colspan="24" align="left" ><b><? echo $group_name; ?> : <? echo $group_dtls_value; ?></b></td>
                        </tr>
                        <?
                        unset($btq);unset($tot_prod_btq);
                        }
                        $group_by_arr[]=$group_value; 
                        
                        $k++;
                    }
            } 
            $grouping_arr_val=$batch[csf('batch_no')].$batch[csf('machine_id')].$batch[csf('floor_id')];
            ?>
            <tr bgcolor="<? echo $bgcolor;?>" onClick="change_color('tr<? echo $i;?>','<? echo $bgcolor;?>')" id="tr<? echo $i;?>">
                <? if (!in_array($grouping_arr_val,$batch_chk_arr) )
                        { $f++;
                            ?>
                <td width="30"><? echo $f; ?></td>
               
                 <? if($group_by==2 || $group_by==0){ ?>
                <td  align="center" width="80"><div style="width:80px; word-wrap:break-word;"><? echo $machine_arr[$batch[csf('machine_id')]]; ?></div></td>
                <?
                 }
                 if($group_by==1 || $group_by==0){ ?>
               <td width="80"><div style="width:80px; word-wrap:break-word;"><? echo $floor_arr[$batch[csf('floor_id')]]; ?></div></td>
                <? } ?>
                 <td  align="center" width="50"><p><? echo $shift_name[$batch[csf('shift_name')]]; ?></p></td>
                <td  width="100" title="<? echo $buyer_arr[$batch[csf('buyer_name')]]; ?>"><p><? echo $buyer_arr[$batch[csf('buyer_name')]]; ?></p></td>
                <td  width="80" title="<? //echo $color_library[$batch[csf('color_id')]]; ?>"><p><? echo $batch[csf('job_no_prefix_num')]; ?></p></td>
                <td  width="100"><p><? echo $batch[csf('booking_no')]; ?></p></td>
                <td  width="60"><p><? echo $batch[csf('file_no')]; ?></p></td>
                <td  width="70"><p><? echo $batch[csf('grouping')]; ?></p></td>
                <td width="90"><div style="width:90px; word-wrap:break-word;"><? echo $po_number; ?></div></td>
               <?   
                   $batch_chk_arr[]=$grouping_arr_val;
                  }
                  else{ 
               ?>
                <td width="30"><? //echo $sl; ?></td>
                  <? if($group_by==2 || $group_by==0){ ?>
                <td  align="center" width="80"><p><? //echo $machine_arr[$batch[csf('machine_id')]]; ?></p></td>
                <?
                 }
                 if($group_by==1 || $group_by==0){ ?>
               <td width="80"><p><? //echo $floor_arr[$batch[csf('floor_id')]]; ?></p></td>
                <? } ?>
                <td  align="center" width="50"><p><? //echo $machine_id; ?></p></td>
                <td  width="100"><p><? //echo $buyer_arr[$batch[csf('buyer_name')]]; ?></p></td>
                <td  width="80" title="<? //echo $color_library[$batch[csf('color_id')]]; ?>"><p><? //echo $color_library[$batch[csf('color_id')]]; ?></p></td>
                <td  width="100"><p><? //echo $special_row[csf('booking_no')]; ?></p></td>
                <td  width="60"><p><? //echo $special_row[csf('file_no')]; ?></p></td>
                <td  width="70"><p><? //echo $special_row[csf('grouping')]; ?></p></td>
                <td width="90"><p><? //echo $batch[csf('po_number')]; ?></p></td>
                <? } ?>
                <td  width="150" title="<? echo $desc[0]; ?>"><div style="word-break:break-all"><? echo $batch[csf('item_description')]; ?></div></td>
                <td  width="50" title="<? echo   $desc[2]; ?>"><p><? echo  $desc[2]; ?></p></td>
                <td width="70" title="<? ?>"><p><? echo $fabric_typee[$batch[csf('width_dia_type')]];;?></p></td>
                <td  width="80" title="<? echo $color_library[$batch[csf('color_id')]]; ?>"><div style="word-break:break-all"><? echo $color_library[$batch[csf('color_id')]]; ?></div></td>
                <td  align="center" width="90" title="<? echo $batch[csf('batch_no')]; ?>"><div style="word-break:break-all"><? echo $batch[csf('batch_no')]; ?></div></td>
                <td  align="center" width="40" title="<? echo $batch[csf('extention_no')]; ?>"><p><? echo $batch[csf('extention_no')]; ?></p></td>
                <td align="right" width="70" title="<? echo $batch[csf('batch_qnty')];  ?>"><? echo number_format($batch[csf('batch_qnty')],2);  ?></td>
                 <td align="right" width="70" >
                    <? 
                    if ($roll_maintained==1 && $cbo_type!=0) { 
                        //$tot_prod_compact_qty=$batch_prod_qty_arr[$batch[csf('id')]][$batch[csf('prod_id')]][$batch[csf('width_dia_type')]][$batch[csf('roll_id')]][$batch[csf('barcode_no')]]['compact'];
                       // echo number_format($tot_prod_compact_qty,2).'A';  
                    }
                    else
                    {
                      // echo number_format($tot_prod_compact_qty,2).'B';  
                    }
                     echo number_format($prod_compact_qty,2);  
                    ?>
                </td>
                <td align="left" width="50" title="<? echo $yarn_lot_arr[$batch[csf('prod_id')]][$batch[csf('po_id')]]; ?>"><p><?
                $yarn_lot='';
                foreach($po_id as $pid)
                {
                    if($yarn_lot=='') $yarn_lot=$yarn_lot_arr[$batch[csf('prod_id')]][$pid];else  $yarn_lot.=",".$yarn_lot_arr[$batch[csf('prod_id')]][$pid];
                }
                 echo $yarn_lot;//$yarn_lot_arr[$batch[csf('prod_id')]][$batch[csf('po_id')]]; 
                  ?></p></td>
                <td width="75" title="Process Start Date"><div style="width:75px; word-wrap:break-word;"><?  echo change_date_format($batch[csf('process_start_date')]).', '.$batch[csf('start_hours')].':'.$batch[csf('start_minutes')]; ?></div></td>
                <td width="75" title="Process End Date"><div style="width:75px; word-wrap:break-word;"><?  echo change_date_format($batch[csf('end_date')]).', '.$batch[csf('end_hours')].':'.$batch[csf('end_minutes')]; ?></div></td>
                 <td width="70" align="center"><div style="width:70px; word-wrap:break-word;"><?
                        $end_time=$batch[csf('end_hours')].':'.$batch[csf('end_minutes')];
                        $start_time=$batch[csf('start_hours')].':'.$batch[csf('start_minutes')];
                        $new_date_time_start=($batch[csf('process_start_date')].' '.$start_time.':'.'00');
                        $new_date_time_end=($batch[csf('end_date')].' '.$end_time.':'.'00');
                        $total_time=datediff(n,$new_date_time_start,$new_date_time_end);
                        echo floor($total_time/60).":".$total_time%60; ?></div></td>
                <td align="center" width="60"><p> <?   echo $batch[csf('remarks')];?> </p>
                 </td>
                <td align="center" title="<? if($batch[csf('batch_against')]==2) echo $batch_against[$batch[csf('batch_against')]]; ?>"><p><?  if($batch[csf('batch_against')]==2) echo $batch_against[$batch[csf('batch_against')]]; ?></p> </td>
            </tr>
            <? 
            $i++;
            $btq+=$batch[csf('batch_qnty')];
            $tot_prod_btq+=$tot_prod_compact_qty;
            $grand_btq+=$batch[csf('batch_qnty')];
            $grand_tot_prod_btq+=$tot_prod_compact_qty;
            } //batchdata froeach
                        if($group_by!=0)
                        {
                        ?>  
                        <tr class="tbl_bottom">
                            <td width="30">&nbsp;</td>
                           
                             <? if($group_by==2 || $group_by==0){ ?>
                            <td width="80">&nbsp;</td>
                            <? } ?>
                            <? if($group_by==1 || $group_by==0){ ?>
                            <td width="80">&nbsp;</td> 
                            <? } 
                            ?> 
                            <td width="50">&nbsp;</td>
                            <td width="100">&nbsp;</td>
                            <td width="80">&nbsp;</td>
                            <td width="100">&nbsp;</td>
                            <td width="60">&nbsp;</td>
                            <td width="70">&nbsp;</td>
                            <td width="90">&nbsp;</td>
                            <td width="150">&nbsp;</td>
                            <td width="50">&nbsp;</td>
                            <td width="70">&nbsp;</td>
                            <td width="80">&nbsp;</td>
                            <td width="90">Sub Total</td>
                            <td width="40">&nbsp;</td>
                            <td width="70"> <? echo number_format($btq,2); ?></td>
                            <td width="70"><? echo number_format($tot_prod_btq,2); ?></td>
                            <td width="50">&nbsp;</td>
                            <td width="75">&nbsp;</td>
                            <td width="75">&nbsp;</td> 
                            <td width="70">&nbsp;</td>
                            <td width="60">&nbsp;</td>
                            <td>&nbsp;</td>
                        </tr>
                        
                        <?
                        }
                        ?>
                </tbody>
                <tfoot>
                    <tr>
                        <th width="30">&nbsp;</th>
                         <? if($group_by==2 || $group_by==0){ ?>
                        <th width="80">&nbsp;</th>
                        <? } ?>
                        <? if($group_by==1 || $group_by==0){ ?>
                        <th width="80">&nbsp;</th> 
                        <? } 
                        ?> 
                        <th width="50">&nbsp;</th>
                        <th width="100">&nbsp;</th>
                        <th width="80">&nbsp;</th>  
                        <th width="100">&nbsp;</th>  
                        <th width="60">&nbsp;</th>  
                        <th width="70">&nbsp;</th>
                        <th width="90">&nbsp;</th>
                        <th width="150">&nbsp;</th>
                        <th width="50">&nbsp;</th>
                        <th width="70">&nbsp;</th>
                        <th width="80">&nbsp;</th>
                        <th width="90">Grand Total</th>
                        <th width="40">&nbsp;</th>
                        <th width="70"><? echo number_format($grand_btq,2); ?></th>
                        <th width="70"><? echo number_format($grand_tot_prod_btq,2); ?></th>
                        <th width="50">&nbsp;</th>
                        <th width="75">&nbsp;</th>
                        <th width="75">&nbsp;</th> 
                        <th width="70">&nbsp;</th>
                        <th width="60">&nbsp;</th>
                        <th>&nbsp;</th>
                    </tr>
                </tfoot>
            </table>

            </div>
            </fieldset>
            </div>
                <? }
        else if($cbo_type==5) // Special Finishing
        {
            ?>
            <div>
            <fieldset style="width:1815px;">
            <div align="center"><strong> <? echo $company_library[$company]; ?> </strong><br> <strong><? echo $search_by_arr[$cbo_type];?> </strong><br>
            <?
                echo  change_date_format($date_from).' '.To.' '.change_date_format($date_to);
            ?>
             </div>
             <table class="rpt_table" width="1835" cellpadding="0" cellspacing="0" border="1" rules="all" align="center" id="table_header_1">
            <thead>
            <tr>
                <th width="30">SL</th>
                 <? if($group_by==2 || $group_by==0){ ?>
                <th width="80">M/C No</th>
                <? } ?>
                <? if($group_by==1 || $group_by==0){ ?>
                <th width="80">Floor</th> 
                <? } 
                ?> 
                <th width="50">Shift</th>
                <th width="100">Buyer</th>
                <th width="80">Job</th> 
                <th width="100">Booking</th> 
                <th width="60">File No</th> 
                <th width="70">Ref. No</th>
                <th width="90">Order No</th>
                <th width="100">Fabrics Desc</th>
                <th width="75">GSM</th>
                <th width="70">Dia/Width Type</th>
                <th width="80">Color Name</th>
                <th width="90">Batch No</th>
                <th width="40">Extn. No</th>
                <th width="70">Batch Qty.</th>
                <th width="70">Prod. Qty.</th>
                <th width="50">Lot No</th>
                <th width="75">Start Date & Time</th> 
                <th width="75">End Date & Time</th>
                <th width="70">Time Used</th>
                <th width="60">Remark</th>
                <th>Reprocess</th>
            </tr>
            </thead>
            </table>
            <div style=" max-height:350px; width:1835px; overflow-y:scroll;;" id="scroll_body">
            <table class="rpt_table" id="table_body" width="1815" cellpadding="0" cellspacing="0" border="1" rules="all" align="center">
            <tbody>
            <? 
            $i=1;$k=1;
            $f=0;
            $btq=0;$tot_prod_btq=0;
            $batch_chk_arr=array(); $process_group_by_arr=array();
            foreach($batchdata as $batch)
            { 
            if ($i%2==0)  
            $bgcolor="#E9F3FF";
            else
            $bgcolor="#FFFFFF";
            $process_id=$batch[csf('process_id')];
			$order_id=$batch[csf('po_id')];
            $color_id=$batch[csf('color_id')];
            $desc=explode(",",$batch[csf('item_description')]); 
            $insert_date = "'".$batch[csf('insert_date')]."'";
            $po_number=implode(",",array_unique(explode(",",$batch[csf('po_number')])));
            if($group_by!=0)
            {
                if($group_by==1)
                {
                    $group_value=$batch[csf('floor_id')];
                    $group_name="Floor";
                    $group_dtls_value=$floor_arr[$batch[csf('floor_id')]];
                }
                
                else if($group_by==2)
                {
                    $group_value=$batch[csf('machine_id')];
                    $group_name="Machine";
                    $group_dtls_value=$machine_arr[$batch[csf('machine_id')]];//
                }
                if (!in_array($group_value,$group_by_arr) )
                    {
                        //if($k!=1)
                        //{
                        ?>  
                        <tr bgcolor="#EFEFEF">
                            <td colspan="24" align="left" ><b><? echo $group_name; ?> : <? echo $group_dtls_value; ?></b></td>
                        </tr>
                        <?
                        $group_by_arr[]=$group_value; 
                        //}
                       // $k++;
                    }
            } 
			if($group_by==0)
            {
				 if (!in_array($process_id,$process_group_by_arr) )
                    {
                        if($k!=1)
                        { ?>
                        
                        <tr  bgcolor="#D4D4D4" >
                       <td align="left" colspan="24"><Strong> Sub Total:</Strong> <b><? echo number_format($tot_prod_btq,2); ?> </b>
                       
                       </td>
                        
                        </tr>                                
                            <?
                            unset($tot_prod_btq);
                        }
                        ?>  
                    
                    
                        <tr bgcolor="#EFEFEF">
                            <td colspan="24" align="left" ><b><? echo $conversion_cost_head_array[$process_id]; ?> : <? //echo $group_dtls_value; ?></b></td>
                        </tr>
                        <?
                        $process_group_by_arr[]=$process_id;            
                        $k++;
                    }
			}
			
            ?>
            <tr bgcolor="<? echo $bgcolor;?>" onClick="change_color('tr<? echo $i;?>','<? echo $bgcolor;?>')" id="tr<? echo $i;?>">
                <? if (!in_array($batch[csf('batch_no')],$batch_chk_arr) )
                        { $f++;
                            ?>
                <td width="30"><? echo $f; ?></td>
               
                  <? if($group_by==2 || $group_by==0){ ?>
                <td  align="center" width="80"><div style="width:80px; word-wrap:break-word;"><? echo $machine_arr[$batch[csf('machine_id')]]; ?></div></td>
                <?
                 }
                 if($group_by==1 || $group_by==0){ ?>
               <td width="80"><div style="width:80px; word-wrap:break-word;"><? echo $floor_arr[$batch[csf('floor_id')]]; ?></div></td>
                <? } ?>
                 <td  align="center" width="50"><p><? echo $shift_name[$batch[csf('shift_name')]]; ?></p></td>
                <td  width="100" title="<? echo $buyer_arr[$batch[csf('buyer_name')]]; ?>"><p><? echo $buyer_arr[$batch[csf('buyer_name')]]; ?></p></td>
                <td  width="80" title="<? //echo $color_library[$batch[csf('color_id')]]; ?>"><p><? echo $batch[csf('job_no_prefix_num')]; ?></p></td>
                <td  width="100"><p><? echo $batch[csf('booking_no')]; ?></p></td>
                <td  width="60"><p><? echo $batch[csf('file_no')]; ?></p></td>
                <td  width="70"><p><? echo $batch[csf('grouping')]; ?></p></td>
                <td width="90"><div style="width:90px; word-wrap:break-word;"><? echo $po_number; ?></div></td>
                    <?  
                    $batch_chk_arr[]=$batch[csf('batch_no')];
                        } 
                        else
                           { ?>
                <td width="30"><? //echo $sl; ?></td>
                <? if($group_by==2 || $group_by==0){ ?>
                <td  align="center" width="80"><div style="width:80px; word-wrap:break-word;"><? //echo $machine_arr[$batch[csf('machine_id')]]; ?></div></td>
                <?
                 }
                 if($group_by==1 || $group_by==0){ ?>
               <td width="80"><div style="width:80px; word-wrap:break-word;"><? //echo $floor_arr[$batch[csf('floor_id')]]; ?></div></td>
                <? } ?>
                <td  align="center" width="50"><p><? //echo $machine_id; ?></p></td>
                <td  width="100"><p><? //echo $buyer_arr[$batch[csf('buyer_name')]]; ?></p></td>
                <td  width="80" title="<? //echo $color_library[$batch[csf('color_id')]]; ?>"><p><? //echo $color_library[$batch[csf('color_id')]]; ?></p></td>
                <td  width="100"><p><? //echo $special_row[csf('booking_no')]; ?></p></td>
                <td  width="60"><p><? //echo $special_row[csf('file_no')]; ?></p></td>
                <td  width="70"><p><? //echo $special_row[csf('grouping')]; ?></p></td>
                <td width="90"><p><? //echo $batch[csf('po_number')]; ?></p></td>
                        <? }
                        ?>
                <td  width="100" title="<? echo $desc[0]; ?>"><div style="word-break:break-all"><? echo $batch[csf('item_description')]; ?></div></td>
                <td  width="75" title="<? echo   $desc[2]; ?>"><p><? echo  $desc[2]; ?></p></td>
                <td  width="70" title="<? ?>"><p><? echo $fabric_typee[$batch[csf('width_dia_type')]];?></p></td>
                <td  width="80" title="<? echo $color_library[$batch[csf('color_id')]]; ?>"><p><? echo $color_library[$batch[csf('color_id')]]; ?></p></td>
                <td  align="center" width="90" title="<? echo $batch[csf('batch_no')]; ?>"><p><? echo $batch[csf('batch_no')]; ?></p></td>
                <td  align="center" width="40" title="<? echo $batch[csf('extention_no')]; ?>"><p><? echo $batch[csf('extention_no')]; ?></p></td>
                <td align="right" width="70" title="<? echo $batch[csf('batch_qnty')];  ?>"><? echo number_format($batch[csf('batch_qnty')],2);  ?></td>
                <td align="right" width="70" ><? 
               // if ($roll_maintained==1 && $cbo_type!=0) {
                if(($page_upto==6 || $page_upto>6) && $roll_maintained==1)
                {
                    echo number_format($batch_prod_qty_arr[$batch[csf('id')]][$batch[csf('prod_id')]][$batch[csf('width_dia_type')]][$batch[csf('roll_no')]][$batch[csf('barcode_no')]][$insert_date]['special'],2); 
                }
                else
                {
                     echo number_format($batch_prod_qty_arr[$batch[csf('id')]][$batch[csf('prod_id')]][$batch[csf('width_dia_type')]][$insert_date]['special'],2); 
                } 

                ?></td>
                <td align="left" width="50" title="<? echo $yarn_lot_arr[$batch[csf('prod_id')]][$batch[csf('po_id')]]; ?>"><p><? echo $yarn_lot_arr[$batch[csf('prod_id')]][$batch[csf('po_id')]];  ?></p></td>
               <td width="75" title="Process Start Date"><div style="width:75px; word-wrap:break-word;"><?  echo change_date_format($batch[csf('process_start_date')]).', '.$batch[csf('start_hours')].':'.$batch[csf('start_minutes')]; ?></div></td>
                <td width="75" title="Process End Date"><div style="width:75px; word-wrap:break-word;"><?  echo change_date_format($batch[csf('end_date')]).', '.$batch[csf('end_hours')].':'.$batch[csf('end_minutes')]; ?></div></td>
                 <td width="70" align="center"><div style="width:70px; word-wrap:break-word;"><?
                        $end_time=$batch[csf('end_hours')].':'.$batch[csf('end_minutes')];
                        $start_time=$batch[csf('start_hours')].':'.$batch[csf('start_minutes')];
                        
                        $new_date_time_start=($batch[csf('process_start_date')].' '.$start_time.':'.'00');
                        $new_date_time_end=($batch[csf('end_date')].' '.$end_time.':'.'00');
                        $total_time=datediff(n,$new_date_time_start,$new_date_time_end);
                        echo floor($total_time/60).":".$total_time%60; ?></div></td>
                <td align="center" width="60"><p> <?  echo $batch[csf('remarks')]; ?>
                    </p>
                 </td>
                <td align="center" title="<?   if($batch[csf('batch_against')]==2) echo $batch_against[$batch[csf('batch_against')]]; ?>"><p><?  if($batch[csf('batch_against')]==2) echo $batch_against[$batch[csf('batch_against')]]; ?></p> </td>
            </tr>
            <? 
            $i++;
            $btq+=$batch[csf('batch_qnty')];
               if(($page_upto==6 || $page_upto>6) && $roll_maintained==1){
                    $tot_prod_btq+=$batch_prod_qty_arr[$batch[csf('id')]][$batch[csf('prod_id')]][$batch[csf('width_dia_type')]][$batch[csf('roll_no')]][$batch[csf('barcode_no')]][$insert_date]['special'];
                }
                else
                {
                    $tot_prod_btq+=$batch_prod_qty_arr[$batch[csf('id')]][$batch[csf('prod_id')]][$batch[csf('width_dia_type')]][$insert_date]['special'];
                }
            } //batchdata froeach
             ?>
                </tbody>
            </table>
            <table class="rpt_table" width="1815" cellpadding="0" cellspacing="0" border="1" rules="all" align="center" id="report_table_footer">
            <tfoot>
            <tr>
                <th width="30">&nbsp;</th>
               
                 <? if($group_by==2 || $group_by==0){ ?>
                <th width="80">&nbsp;</th>
                <? } ?>
                <? if($group_by==1 || $group_by==0){ ?>
                <th width="80">&nbsp;</th> 
                <? } 
                ?> 
                <th width="50">&nbsp;</th>
                <th width="100">&nbsp;</th>
                <th width="80">&nbsp;</th> 
                <th width="100">&nbsp;</th> 
                <th width="60">&nbsp;</th> 
                <th width="70">&nbsp;</th>
                <th width="90">&nbsp;</th>
                <th width="100">&nbsp;</th>
                <th width="75">&nbsp;</th>
                <th width="70">&nbsp;</th>
                <th width="80">&nbsp;</th>
                <th width="90">&nbsp;</th>
                <th width="40">&nbsp;</th>
                <th width="70"><? echo number_format($btq,2); ?></th>
                <th width="70"><? echo number_format($tot_prod_btq,2); ?></th>
                <th width="50">&nbsp;</th>
                <th width="75">&nbsp;</th> 
                <th width="75">&nbsp;</th>
                <th width="70">&nbsp;</th>
                <th width="60">&nbsp;</th>
                <th>&nbsp;</th>
            </tr>
            </tfoot>
            </table>
            </div>
            </fieldset>
            </div>
                <? 
         }
            
        else if($cbo_type==6) //Waiting For Slitting -Unload
        { ?>
            <div style="width:1670px;">
            <fieldset style="width:1670px;">
            <div  align="center"> <strong> <? echo $company_library[$company]; ?> </strong><br><strong> <? echo $search_by_arr[$cbo_type];?> </strong>
            <br>
            <?
                echo  change_date_format($date_from).' '.To.' '.change_date_format($date_to);
            ?>
             </div>
             <table class="rpt_table" width="1670" cellpadding="0" cellspacing="0" border="1" rules="all" align="center" id="table_header_1">
            <thead>
            <tr>
                <th width="30">SL</th>
               
                  <? if($group_by==2 || $group_by==0){ ?>
                <th width="80">M/C No</th> 
                <? } ?>
                <? if($group_by==1 || $group_by==0){ ?>
                <th width="80">Floor</th> 
                <? } 
                ?> 
                <th width="50">Shift</th>
                <th width="100">Buyer</th>
                <th width="100">Booking</th> 
                <th width="60">File No</th> 
                <th width="70">Ref. No</th> 
                <th width="90">Order No</th>
                <th width="100">Fabrics Desc</th>
                <th width="70">GSM</th>
                <th width="80">Color Name</th>
                <th width="90">Batch No</th>
                <th width="80">Ext. No</th>
                <th width="80">Batch Qty.</th>
                <th width="70">Prod. Qty.</th>
                <th width="100">Unloading Date</th>
                <th width="80">Unloading Time</th>
                <th width="80">Shade Position</th>
                <th width="60">Yarn Lot</th>
                <th>Reprocess</th>
            </tr>
            </thead>
            </table>
            <div style=" max-height:380px; width:1670px; overflow-y:scroll;" id="scroll_body">
            <table class="rpt_table" id="table_body" width="1650" cellpadding="0" cellspacing="0" border="1" rules="all" align="center">
            <tbody>
            <? 
            $i=1;
            $f=0;
            $btq=0;$tot_prod_btq=0;
            //if($db_type==0) $group_concat="group_concat(c.po_number)"; 
            //else if($db_type==2) $group_concat="listagg(c.po_number,',' ) within group (order by c.po_number) AS po_number";

            //print_r($w_siltting);die;,c.file_no,c.grouping 
             $sql_wait="SELECT a.company_id,a.id,a.batch_no, a.process_id,a.batch_date,a.color_id,a.extention_no,a.batch_against,SUM(b.batch_qnty) AS batch_qnty,b.item_description,b.po_id,b.prod_id,b.width_dia_type,$group_concat,c.job_no_mst,d.job_no_prefix_num,d.buyer_name,c.file_no,c.grouping,f.shift_name,f.process_end_date,f.process_start_date,f.production_date as end_date,f.start_minutes,f.start_hours,f.end_hours,f.end_minutes,f.machine_id,f.floor_id,f.result,f.remarks,f.re_stenter_no,f.insert_date $dyeing_pro from pro_batch_create_dtls b,wo_po_break_down c,wo_po_details_master d,pro_batch_create_mst a,pro_fab_subprocess f where  f.batch_id=a.id  and a.id=b.mst_id and b.po_id=c.id and d.job_no=c.job_no_mst and f.entry_form=35 $company_cond $working_company_cond  $dates_com $jobdata $batch_num $buyerdata $order_no $year_cond $color_name and a.entry_form=0  and f.load_unload_id in(2) and  a.batch_against in(1,2)  and b.status_active=1 and b.is_deleted=0 and a.status_active=1   and f.result=1 and a.is_deleted=0   GROUP BY b.po_id, b.item_description,a.company_id, a.id, a.batch_no,a.process_id, a.batch_date,a.color_id, a.extention_no, a.batch_against,b.prod_id, b.width_dia_type,c.job_no_mst, d.job_no_prefix_num, d.buyer_name,c.file_no,c.grouping,f.shift_name,f.result, f.process_end_date,f.process_start_date,f.production_date,f.start_minutes,f.start_hours, f.end_hours, f.end_minutes, f.machine_id,f.floor_id, f.remarks,f.re_stenter_no,f.insert_date $dyeing_group   $order_by";
                         
                $fab_data=sql_select($sql_wait);
                $batchIdArr=array();
                foreach($fab_data as $rows)
                { 
                    $batchIdArr[$rows[csf('id')]]=$rows[csf('id')];
                }
                
                
                $remove_batch_sql="select BATCH_ID from  pro_fab_subprocess where entry_form=30 and status_active=1 and is_deleted=0 and batch_id>0 ";

                $batch_array_chunk=array_chunk($batchIdArr,999);
                $p=1;
                foreach($batch_array_chunk as $bid)
                {
                    if($p==1)  $remove_batch_sql .="and (batch_id not in(".implode(',',$bid).")"; 
                    else  $remove_batch_sql .=" and batch_id not in(".implode(',',$bid).")";
                    
                    $p++;
                }
                $remove_batch_sql .=")";
                $remove_batch_sql_result=sql_select($remove_batch_sql);
                $remove_batch_arr=array();
                foreach($remove_batch_sql_result as $rows)
                { 
                    $remove_batch_arr[$rows[BATCH_ID]]=1;
                }
                        
                //print_r($remove_batch_arr);die;       
                        
                        
                         
            $batch_chk_arr=array();

            foreach($fab_data as $batch)
            { 
                if($remove_batch_arr[$batch[csf('id')]]==1){continue;}

            if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
            $order_id=$batch[csf('po_id')];
            $color_id=$batch[csf('color_id')];
            $desc=explode(",",$batch[csf('item_description')]);
            $insert_date = "'".$batch[csf('insert_date')]."'"; 
            $po_number=implode(",",array_unique(explode(",",$batch[csf('po_number')])));
            $processid=explode(",",$batch[csf('process_id')]);
            $result=$batch[csf('result')];
             
            if (in_array(63,$processid))
            {
            if($group_by!=0)
            {
                if($group_by==1)
                {
                    $group_value=$batch[csf('floor_id')];
                    $group_name="Floor";
                    $group_dtls_value=$floor_arr[$batch[csf('floor_id')]];
                }
                
                else if($group_by==2)
                {
                    $group_value=$batch[csf('machine_id')];
                    $group_name="Machine";
                    $group_dtls_value=$machine_arr[$batch[csf('machine_id')]];//
                }
                if (!in_array($group_value,$group_by_arr) )
                    {
                        //if($k!=1)
                        //{
                        ?>  
                        <tr bgcolor="#EFEFEF">
                            <td colspan="23" align="left" ><b><? echo $group_name; ?> : <? echo $group_dtls_value; ?></b></td>
                        </tr>
                        <?
                        $group_by_arr[]=$group_value; 
                        //}
                       // $k++;
                    }
            }
            ?>
            <tr bgcolor="<? echo $bgcolor;?>" onClick="change_color('tr<? echo $i;?>','<? echo $bgcolor;?>')" id="tr<? echo $i;?>">
                <? if (!in_array($batch[csf('batch_no')],$batch_chk_arr) )
                        { $f++;
                            ?>
                <td width="30"><? echo $f; ?></td>
                <? if($group_by==2 || $group_by==0){ ?>
                <td  align="center" width="80"><div style="width:80px; word-wrap:break-word;"><? echo $machine_arr[$batch[csf('machine_id')]]; ?></div></td>
                <?
                 }
                 if($group_by==1 || $group_by==0){ ?>
               <td width="80"><div style="width:80px; word-wrap:break-word;"><? echo $floor_arr[$batch[csf('floor_id')]]; ?></div></td>
                <? } ?>
                 <td  align="center" width="50"><p><? echo $shift_name[$batch[csf('shift_name')]]; ?></p></td>
                <td  width="100" title="<? echo $buyer_arr[$batch[csf('buyer_name')]]; ?>"><p><? echo $buyer_arr[$batch[csf('buyer_name')]]; ?></p></td>
                <td  width="100"><p><? echo $batch[csf('booking')]; ?></p></td>
                <td  width="60"><p><? echo $batch[csf('file_no')]; ?></p></td>
                <td  width="70"><p><? echo $batch[csf('grouping')]; ?></p></td>
                <td width="90">
            <div style="width:90px; word-wrap:break-word;"><? echo $po_number; ?></div></td>
                    <?  
                    $batch_chk_arr[]=$batch[csf('batch_no')];
                        } 
                        else
                           { ?>
                <td width="30"><? //echo $sl; ?></td>
                 <? if($group_by==2 || $group_by==0){ ?>
                <td  align="center" width="80"><div style="width:80px; word-wrap:break-word;"><? //echo $machine_arr[$batch[csf('machine_id')]]; ?></div></td>
                <?
                 }
                 if($group_by==1 || $group_by==0){ ?>
               <td width="80"><div style="width:80px; word-wrap:break-word;"><? //echo $floor_arr[$batch[csf('floor_id')]]; ?></div></td>
                <? } ?>
                <td  align="center" width="50"><p><? //echo $machine_id; ?></p></td>
                <td  width="100"><p><? //echo $buyer_arr[$batch[csf('buyer_name')]]; ?></p></td>
                <td  width="100"><p><? //echo $special_row[csf('booking_no')]; ?></p></td>
                <td  width="60"><p><? //echo $batch[csf('file_no')]; ?></p></td>
                <td  width="70"><p><? //echo $batch[csf('grouping')]; ?></p></td>
                <td width="90"><p><? //echo $batch[csf('po_number')]; ?></p></td>
                        <? }
                        ?>
                <td  width="100" title="<? echo $desc[0]; ?>"><p><? echo $batch[csf('item_description')]; ?></p></td>
                <td  width="70" title="<? echo  $desc[2]; ?>"><p><? echo $desc[2]; ?></p></td>
                <td  width="80" title="<? echo $color_library[$batch[csf('color_id')]]; ?>"><p><? echo $color_library[$batch[csf('color_id')]]; ?></p></td>
                <td  align="center" width="90" title="<? echo $batch[csf('batch_no')]; ?>"><p><? echo $batch[csf('batch_no')]; ?></p></td>
                <td  align="center" width="80" title="<? echo $batch[csf('extention_no')]; ?>"><p><? echo $batch[csf('extention_no')]; ?></p></td>
                <td align="right" width="80" title="<? echo $batch[csf('batch_qnty')];  ?>"><p><? echo number_format($batch[csf('batch_qnty')],2);  ?></p></td>
                <td align="right" width="70" ><? echo number_format($batch_prod_qty_arr[$batch[csf('id')]][$batch[csf('prod_id')]][$batch[csf('width_dia_type')]][$insert_date]['unload'],2);  ?></td>
                <td width="100" title="<? echo change_date_format($batch[csf('process_end_date')]); ?>"><p><?  echo change_date_format($batch[csf('process_end_date')])?></p></td>
                <td align="center" width="80" title="<? echo $batch[csf('end_hours')].':'.$batch[csf('end_minutes')];  ?>"><p><? echo $batch[csf('end_hours')].':'.$batch[csf('end_minutes')];   ?></p></td>
                <td align="center" width="80" title="Shade" ><p><? if($result==1) echo 'OK';  ?></p></td>
                <td align="right" width="60" title="Lot"><p><? echo $yarn_lot_arr[$batch[csf('prod_id')]][$batch[csf('po_id')]]; ?></p></td>
                <td align="center"  title="<? if($batch[csf('batch_against')]==2) echo $batch_against[$batch[csf('batch_against')]]; ?>"><p><?  if($batch[csf('batch_against')]==2) echo $batch_against[$batch[csf('batch_against')]]; else echo '';?> </p></td>
            </tr>
            <? 
            $i++;
            $btq+=$batch[csf('batch_qnty')];
             $tot_prod_btq+=$batch_prod_qty_arr[$batch[csf('id')]][$batch[csf('prod_id')]][$batch[csf('width_dia_type')]][$insert_date]['unload'];
            }
            } //batchdata froeach
             ?>
            </tbody>
            </table>
             <table class="rpt_table" width="1650" cellpadding="0" cellspacing="0" border="1" rules="all" align="center" id="report_table_footer">
            <tfoot>
            <tr>
                <th width="30">&nbsp;</th>
                  <? if($group_by==2 || $group_by==0){ ?>
                <th width="80">&nbsp;</th> 
                <? } ?>
                <? if($group_by==1 || $group_by==0){ ?>
                <th width="80">&nbsp;</th> 
                <? } 
                ?> 
                <th width="50">&nbsp;</th>
                <th width="100">&nbsp;</th>
                <th width="100">&nbsp;</th>
                <th width="60">&nbsp;</th>
                <th width="70">&nbsp;</th>
                <th width="90">&nbsp;</th>
                <th width="100">&nbsp;</th>
                <th width="70">&nbsp;</th>
                <th width="80">&nbsp;</th>
                <th width="90">&nbsp;</th>
                <th width="80">&nbsp;</th>
                <th width="80"><? echo number_format($btq,2); ?></th>
                <th width="70"><? echo number_format($tot_prod_btq,2); ?></th>
                <th width="100">&nbsp;</th>
                <th width="80">&nbsp;</th>
                <th width="80">&nbsp;</th>
                <th width="60">&nbsp;</th>
                <th>&nbsp;</th>
            </tr>
            </tfoot>
            </table>
            </div>
            </fieldset>
            </div>
      <? }
        else if($cbo_type==7) // Wait For Drying 
        { ?>

            <div style="width:1670px;">
            <fieldset style="width:1670px;">
            <div  align="center"> <strong> <? echo $company_library[$company]; ?> </strong><br><strong> <? echo $search_by_arr[$cbo_type];?> </strong>
            <br>
            <?
                echo  change_date_format($date_from).' '.To.' '.change_date_format($date_to);
            ?>
             </div>
             <table class="rpt_table" width="1670" cellpadding="0" cellspacing="0" border="1" rules="all" align="center" id="table_header_1">
            <thead>
            <tr>
                <th width="30">SL</th> 
               
                 <? if($group_by==2 || $group_by==0){ ?>
                 <th width="80">M/C No</th>
                <? } ?>
                <? if($group_by==1 || $group_by==0){ ?>
                <th width="80">Floor</th> 
                <? } 
                ?> 
                <th width="50">Shift</th>
                <th width="80">Job No</th>
                <th width="100">Buyer</th>  
                <th width="100">Booking</th>  
                <th width="60">File No</th> 
                <th width="70">Ref. No</th>
                <th width="90">Order No</th>
                <th width="100">Fabrics Desc</th>
                <th width="70">GSM</th>
                <th width="80">Color Name</th>
                <th width="90">Batch No</th>
                <th width="80">Ext. No</th>
                <th width="80">Batch Qty.</th>
                <th width="70">Prod Qty.</th>
                <th width="100">Process Date</th>
                <th width="80">Process Time</th>
                <th width="60">Yarn Lot</th>
                <th>Reprocess</th>
            </tr>
            </thead>
            </table>
            <div style=" max-height:380px; width:1670px; overflow-y:scroll;" id="scroll_body">
            <table class="rpt_table" id="table_body" width="1650" cellpadding="0" cellspacing="0" border="1" rules="all" align="center">
            <tbody>
            <? 
            $i=1;
            $f=0;
            $btq=0;$tot_prod_btq=0;

            //and a.process_id in(63)
            $sql_wait="SELECT a.company_id,a.id,a.batch_no, a.process_id,a.batch_date,a.color_id,a.extention_no,a.batch_against,SUM(b.batch_qnty) AS batch_qnty,b.item_description,b.po_id,b.prod_id,b.barcode_no,b.roll_id,b.width_dia_type,$group_concat,c.job_no_mst,d.job_no_prefix_num,d.buyer_name,c.file_no,c.grouping,f.shift_name,f.process_end_date,f.process_start_date,f.production_date as end_date,f.start_minutes,f.start_hours,f.end_hours,f.end_minutes,f.machine_id,f.floor_id,f.remarks,f.re_stenter_no,f.insert_date 
            $stenter  from pro_batch_create_dtls b,wo_po_break_down c,wo_po_details_master d,pro_batch_create_mst a,pro_fab_subprocess f where  f.batch_id=a.id and b.po_id=c.id and d.job_no=c.job_no_mst  and a.id=b.mst_id and f.entry_form=48 $company_cond $working_company_cond   $dates_com $jobdata $batch_num $buyerdata $order_no $year_cond $color_name and a.entry_form=0 and f.re_stenter_no=0 and  a.batch_against in(1,2) and b.status_active=1 and b.is_deleted=0 and a.status_active=1  and a.is_deleted=0 and f.status_active=1 and f.is_deleted=0 

            GROUP BY b.po_id, b.item_description,a.company_id, a.id, a.batch_no,a.process_id, a.batch_date,a.color_id, a.extention_no, a.batch_against,b.prod_id,b.barcode_no,b.roll_id, b.width_dia_type,c.job_no_mst, d.job_no_prefix_num, d.buyer_name,c.file_no,c.grouping,f.shift_name, f.process_end_date,f.process_start_date,f.production_date,f.start_minutes,f.start_hours, f.end_hours, f.end_minutes, f.machine_id,f.floor_id, f.remarks,f.re_stenter_no,f.insert_date $stenter_group  $order_by "; 
            //echo $sql_wait;
                $fab_wait=sql_select($sql_wait);
                $batchIdArr=array();
                foreach($fab_wait as $rows)
                { 
                    $batchIdArr[$rows[csf('id')]]=$rows[csf('id')];
                }
                
                
                $remove_batch_sql="select a.BATCH_ID from  pro_fab_subprocess a,pro_fab_subprocess_dtls b where a.entry_form=31 and a.id=b.mst_id and b.width_dia_type in(2) and a.status_active=1 and a.is_deleted=0 and a.batch_id>0 ";

                $batch_array_chunk=array_chunk($batchIdArr,999);
                $p=1;
                foreach($batch_array_chunk as $bid)
                {
                    if($p==1)  $remove_batch_sql .="and (a.batch_id not in(".implode(',',$bid).")"; 
                    else  $remove_batch_sql .=" and a.batch_id not in(".implode(',',$bid).")";
                    
                    $p++;
                }
                $remove_batch_sql .=")";
                $remove_batch_sql_result=sql_select($remove_batch_sql);
                $remove_batch_arr=array();
                foreach($remove_batch_sql_result as $rows)
                { 
                    $remove_batch_arr[$rows[BATCH_ID]]=1;
                }
                //print_r($remove_batch_arr);die;
                


            $batch_chk_arr=array();
            //$fab_wait=sql_select($sql_wait);
            foreach($fab_wait as $batch)
            {
                if($remove_batch_arr[$batch[csf('id')]]==1){continue;}
                 
            if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
            $order_id=$batch[csf('po_id')];
            $color_id=$batch[csf('color_id')];
            $desc=explode(",",$batch[csf('item_description')]); 
            $insert_date = "'".$batch[csf('insert_date')]."'";
            $po_number=implode(",",array_unique(explode(",",$batch[csf('po_number')])));
            $processid=explode(",",$batch[csf('process_id')]);
            //echo $batch[csf('process_id')];die;
            $result=$batch[csf('result')];
            //$process_arr='66,91';
            $process_name=array(66,91,125);
            $process_count=count($processid);
            $process = explode(",",str_replace("'","",$process_arr));
            $process_sql=count($processid);
            //print_r( $process);die;
            //print_r(array_diff($process_name,$processid));die;

            //if (in_array(65,$processid))
            //{
            if($group_by!=0)
            {
                if($group_by==1)
                {
                    $group_value=$batch[csf('floor_id')];
                    $group_name="Floor";
                    $group_dtls_value=$floor_arr[$batch[csf('floor_id')]];
                }
                
                else if($group_by==2)
                {
                    $group_value=$batch[csf('machine_id')];
                    $group_name="Machine";
                    $group_dtls_value=$machine_arr[$batch[csf('machine_id')]];//
                }
                if (!in_array($group_value,$group_by_arr) )
                    {
                        //if($k!=1)
                        //{
                        ?>  
                        <tr bgcolor="#EFEFEF">
                            <td colspan="23" align="left" ><b><? echo $group_name; ?> : <? echo $group_dtls_value; ?></b></td>
                        </tr>
                        <?
                        $group_by_arr[]=$group_value; 
                        //}
                       // $k++;
                    }
            }
            ?>
            <tr bgcolor="<? echo $bgcolor;?>" onClick="change_color('tr<? echo $i;?>','<? echo $bgcolor;?>')" id="tr<? echo $i;?>">
                <? if (!in_array($batch[csf('batch_no')],$batch_chk_arr) )
                        { $f++;
                            ?>
                <td width="30"><? echo $f; ?></td>
                <? if($group_by==2 || $group_by==0){ ?>
                <td  align="center" width="80"><div style="width:80px; word-wrap:break-word;"><? echo $machine_arr[$batch[csf('machine_id')]]; ?></div></td>
                <?
                 }
                 if($group_by==1 || $group_by==0){ ?>
               <td width="80"><div style="width:80px; word-wrap:break-word;"><? echo $floor_arr[$batch[csf('floor_id')]]; ?></div></td>
                <? } ?>
                <td  align="center" width="50"><p><? echo $shift_name[$batch[csf('shift_name')]]; ?></p></td>
                <td  align="center" width="80"><p><? echo $batch[csf('job_no_prefix_num')]; ?></p></td>
                <td  width="100" title="<? echo $buyer_arr[$batch[csf('buyer_name')]]; ?>"><p><? echo $buyer_arr[$batch[csf('buyer_name')]]; ?></p></td>
                <td  width="100"><p><? echo $batch[csf('booking_no')]; ?></p></td>
                <td  width="60"><p><? echo $batch[csf('file_no')]; ?></p></td>
                <td  width="70"><p><? echo $batch[csf('grouping')]; ?></p></td>
                <td width="90" title="<? echo $po_number; ?>"><p><? echo $po_number; ?></p></td>
                    <?  
                    $batch_chk_arr[]=$batch[csf('batch_no')];
                        } 
                        else
                           { ?>
                <td width="30"><? //echo $sl; ?></td>
                <? if($group_by==2 || $group_by==0){ ?>
                <td  align="center" width="80"><div style="width:80px; word-wrap:break-word;"><? //echo $machine_arr[$batch[csf('machine_id')]]; ?></div></td>
                <?
                 }
                 if($group_by==1 || $group_by==0){ ?>
               <td width="80"><div style="width:80px; word-wrap:break-word;"><? //echo $floor_arr[$batch[csf('floor_id')]]; ?></div></td>
                <? } ?>
                <td  align="center" width="50"><p><? //echo $machine_id; ?></p></td>
                <td  align="center" width="80"><p><? //echo $machine_id; ?></p></td>
                <td  width="100"><p><? //echo $buyer_arr[$batch[csf('buyer_name')]]; ?></p></td>
                <td  width="100"><p><? //echo $special_row[csf('booking_no')]; ?></p></td>
                <td  width="60"><p><? //echo $batch[csf('file_no')]; ?></p></td>
                <td  width="70"><p><? //echo $batch[csf('grouping')]; ?></p></td>
                <td width="90"><p><? //echo $batch[csf('po_number')]; ?></p></td>
                        <? }
                        ?>
                <td  width="100" title="<? echo $desc[0]; ?>"><p><? echo $batch[csf('item_description')]; ?></p></td>
                <td  width="70" title="<? echo  $desc[2]; ?>"><p><? echo $desc[2]; ?></p></td>
                <td  width="80" title="<? echo $color_library[$batch[csf('color_id')]]; ?>"><p><? echo $color_library[$batch[csf('color_id')]]; ?></p></td>
                <td  align="center" width="90" title="<? echo $batch[csf('batch_no')]; ?>"><p><? echo $batch[csf('batch_no')]; ?></p></td>
                <td  align="center" width="80" title="<? echo $batch[csf('extention_no')]; ?>"><p><? echo $batch[csf('extention_no')]; ?></p></td>
                <td align="right" width="80" title="<? echo $batch[csf('batch_qnty')];  ?>"><p><? echo number_format($batch[csf('batch_qnty')],2);  ?></p></td>
                
             <td align="right" width="70" ><? echo number_format($batch_prod_qty_arr[$batch[csf('id')]][$batch[csf('prod_id')]][$batch[csf('width_dia_type')]][$batch[csf('roll_id')]][$batch[csf('barcode_no')]][$insert_date]['stenter'],2);  ?></td>
                <td width="100" title="<? echo change_date_format($batch[csf('process_end_date')]); ?>"><p><?  echo change_date_format($batch[csf('process_end_date')])?></p></td>
                <td align="center" width="80" title="<? echo $batch[csf('end_hours')].':'.$batch[csf('end_minutes')];  ?>"><p><? echo $batch[csf('end_hours')].':'.$batch[csf('end_minutes')];   ?></p></td>
               
                <td align="right" width="60" title="Lot"><p><? echo $yarn_lot_arr[$batch[csf('prod_id')]][$batch[csf('po_id')]]; ?></p></td>
                <td align="center"  title="<? if($batch[csf('batch_against')]==2) echo $batch_against[$batch[csf('batch_against')]]; ?>"><p><?  if($batch[csf('batch_against')]==2) echo $batch_against[$batch[csf('batch_against')]]; else echo '';?> </p></td>
            </tr>
            <? 
            $i++;
            $btq+=$batch[csf('batch_qnty')];
             $tot_prod_btq+=$batch_prod_qty_arr[$batch[csf('id')]][$batch[csf('prod_id')]][$batch[csf('width_dia_type')]][$batch[csf('roll_id')]][$batch[csf('barcode_no')]][$insert_date]['stenter'];
            //}
            } //batchdata froeach
             ?>
            </tbody>
            </table>
             <table class="rpt_table" width="1650" cellpadding="0" cellspacing="0" border="1" rules="all" align="center" id="report_table_footer">
            <tfoot>
            <tr>
                <th width="30">&nbsp;</th>
                 <? if($group_by==2 || $group_by==0){ ?>
                 <th width="80">&nbsp;</th>
                <? } ?>
                <? if($group_by==1 || $group_by==0){ ?>
                <th width="80">&nbsp;</th> 
                <? } 
                ?> 
                <th width="50">&nbsp;</th>
                <th width="80">&nbsp;</th>
                <th width="100">&nbsp;</th>
                <th width="100">&nbsp;</th>
                <th width="60">&nbsp;</th>
                <th width="70">&nbsp;</th>
                <th width="90">&nbsp;</th>
                <th width="100">&nbsp;</th>
                <th width="70">&nbsp;</th>
                <th width="80">&nbsp;</th>
                <th width="90">&nbsp;</th>
                <th width="80">&nbsp;</th>
                <th width="80"><? echo number_format($btq,2); ?></th>
                <th width="70"><? echo number_format($tot_prod_btq,2); ?></th>
                <th width="100">&nbsp;</th>
                <th width="80">&nbsp;</th>
                <th width="60">&nbsp;</th>
                <th>&nbsp;</th>
            </tr>
            </tfoot>
            </table>
            </div>
            </fieldset>
            </div>
      <? }
        else if($cbo_type==8)//Wait for Compacting //Drying
        { 
        	?>
            <div style="width:1750px;">
            <fieldset style="width:1650px;">
            <div  align="center"> <strong> <? echo $company_library[$company]; ?> </strong><br><strong> <? echo $search_by_arr[$cbo_type];?> </strong>
            <br>
            <?
                echo  change_date_format($date_from).' '.To.' '.change_date_format($date_to);
            ?>
             </div>
             <table class="rpt_table" width="1750" cellpadding="0" cellspacing="0" border="1" rules="all" align="center" id="table_header_1">
            <thead>
            <tr>
                <th width="30">SL</th>
                <? if($group_by==2 || $group_by==0){ ?>
                 <th width="80">M/C No</th>
                <? } ?>
                <? if($group_by==1 || $group_by==0){ ?>
                <th width="80">Floor</th> 
                <? } 
                ?> 
                <th width="50">Shift</th>
                <th width="100">Buyer</th>
                <th width="100">Booking</th>
                <th width="60">File No</th> 
                <th width="70">Ref. No</th> 
                <th width="90">Order No</th>
                <th width="100">Fabrics Desc</th>
                <th width="70">GSM</th>
                <th width="80">Color Name</th>
                <th width="90">Batch No</th>
                <th width="80">Ext. No</th>
                <th width="80">Batch Qty.</th>
                <th width="70">Prod Qty.</th>
                <th width="100">Process Date</th>
                <th width="80">Process Time</th>
                <th width="60">Yarn Lot</th>
                <th>Reprocess</th>
            </tr>
            </thead>
            </table>
            <div style=" max-height:380px; width:1750px; overflow-y:scroll;" id="scroll_body">
            <table class="rpt_table" id="table_body" width="1730" cellpadding="0" cellspacing="0" border="1" rules="all" align="center">
            <tbody>
            <? 
            $i=1;
            $f=0;
            $btq=0;$tot_prod_btq=0;
            /*$sql_batch_h=sql_select("select batch_id from  pro_fab_subprocess where entry_form=33 and status_active=1 and is_deleted=0 and batch_id>0");
                $i=1;
                foreach($sql_batch_h as $row_h)
                {
                    if($i!==1) $row_com.=",";
                    $row_com.=$row_h[csf('batch_id')];
                    $i++;
                }
                $w_com=array_chunk(array_unique(explode(",",$row_com)),999);*/
                
            $sql_wait="select a.company_id,a.id,a.batch_no, a.process_id,a.batch_date,a.color_id,a.extention_no,a.batch_against,SUM(b.batch_qnty) AS batch_qnty,b.item_description,b.po_id,b.prod_id,b.width_dia_type,$group_concat,c.job_no_mst,d.job_no_prefix_num,d.buyer_name,c.file_no,c.grouping,f.shift_name,f.process_end_date,f.process_start_date,f.production_date as end_date,f.start_minutes,f.start_hours,f.end_hours,f.end_minutes,f.machine_id,f.floor_id,f.remarks,f.re_stenter_no,f.insert_date 
            $drying  from pro_batch_create_dtls b,wo_po_break_down c,wo_po_details_master d,pro_batch_create_mst a,pro_fab_subprocess f where a.id=b.mst_id and b.po_id=c.id and d.job_no=c.job_no_mst and f.entry_form=31 and f.batch_id=a.id $company_cond $working_company_cond   $dates_com $jobdata $batch_num $buyerdata $order_no $year_cond $color_name and a.entry_form=0 and  a.batch_against in(1,2)  and b.status_active=1 and b.is_deleted=0 and a.status_active=1   and a.is_deleted=0 
              GROUP BY b.po_id, b.item_description,a.company_id, a.id, a.batch_no,a.process_id, a.batch_date,a.color_id, a.extention_no, a.batch_against,b.prod_id, b.width_dia_type,c.job_no_mst, d.job_no_prefix_num, d.buyer_name,c.file_no,c.grouping,f.shift_name, f.process_end_date,f.process_start_date,f.production_date,f.start_minutes,f.start_hours, f.end_hours, f.end_minutes, f.machine_id,f.floor_id, f.remarks,f.re_stenter_no,f.insert_date $drying_group $order_by "; 
                
                $sql_wait_data=sql_select($sql_wait);
                $batchIdArr=array();
                foreach($sql_wait_data as $rows)
                { 
                    $batchIdArr[$rows[csf('id')]]=$rows[csf('id')];
                }
                
                
                $remove_batch_sql="select BATCH_ID from  pro_fab_subprocess where entry_form=33 and status_active=1 and is_deleted=0 and batch_id>0 ";

                $batch_array_chunk=array_chunk($batchIdArr,999);
                $p=1;
                foreach($batch_array_chunk as $bid)
                {
                    if($p==1)  $remove_batch_sql .="and (a.batch_id not in(".implode(',',$bid).")"; 
                    else  $remove_batch_sql .=" and a.batch_id not in(".implode(',',$bid).")";
                    $p++;
                }
                $remove_batch_sql .=")";
                $remove_batch_sql_result=sql_select($remove_batch_sql);
                $remove_batch_arr=array();
                foreach($remove_batch_sql_result as $rows)
                { 
                    $remove_batch_arr[$rows[BATCH_ID]]=1;
                }   
                
                
            $batch_chk_arr=array();$group_by_arr=array();

            foreach($sql_wait_data as $batch)
            { 
                if($remove_batch_arr[$batch[csf('id')]]==1){continue;}
            if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
            $order_id=$batch[csf('po_id')];
            $color_id=$batch[csf('color_id')];
            $desc=explode(",",$batch[csf('item_description')]); 
            $insert_date = "'".$batch[csf('insert_date')]."'";
            $po_number=implode(",",array_unique(explode(",",$batch[csf('po_number')])));
            $processid=explode(",",$batch[csf('process_id')]);
            $process_name=array(66,91,125);
            $process_count=count($processid);
            $process = explode(",",str_replace("'","",$process_arr));
            $process_sql=count($processid);
            //if (in_array(66,$processid))
            //{
            if($group_by!=0)
            {
                if($group_by==1)
                {
                    $group_value=$batch[csf('floor_id')];
                    $group_name="Floor";
                    $group_dtls_value=$floor_arr[$batch[csf('floor_id')]];
                }
                
                else if($group_by==2)
                {
                    $group_value=$batch[csf('machine_id')];
                    $group_name="Machine";
                    $group_dtls_value=$machine_arr[$batch[csf('machine_id')]];//
                }
                if (!in_array($group_value,$group_by_arr) )
                    {
                        //if($k!=1)
                        //{
                        ?>  
                        <tr bgcolor="#EFEFEF">
                            <td colspan="22" align="left" ><b><? echo $group_name; ?> : <? echo $group_dtls_value; ?></b></td>
                        </tr>
                        <?
                        $group_by_arr[]=$group_value; 
                        //}
                       // $k++;
                    }
            }
            $com_wait_grouping_arr_val=$batch[csf('batch_no')].$batch[csf('machine_id')].$batch[csf('floor_id')];
            ?>
            <tr bgcolor="<? echo $bgcolor;?>" onClick="change_color('tr<? echo $i;?>','<? echo $bgcolor;?>')" id="tr<? echo $i;?>">
                <? if (!in_array($com_wait_grouping_arr_val,$batch_chk_arr) )
                        { $f++;
                            ?>
                <td width="30"><? echo $f; ?></td>
                 <? if($group_by==2 || $group_by==0){ ?>
                <td  align="center" width="80"><div style="width:80px; word-wrap:break-word;"><? echo $machine_arr[$batch[csf('machine_id')]]; ?></div></td>
                <?
                 }
                 if($group_by==1 || $group_by==0){ ?>
               <td width="80"><div style="width:80px; word-wrap:break-word;"><? echo $floor_arr[$batch[csf('floor_id')]]; ?></div></td>
                <? } ?>
                 <td  align="center" width="50"><p><? echo $shift_name[$batch[csf('shift_name')]]; ?></p></td>
                <td  width="100" title="<? echo $buyer_arr[$batch[csf('buyer_name')]]; ?>"><p><? echo $buyer_arr[$batch[csf('buyer_name')]]; ?></p></td>
                <td  width="100"><p><? echo $batch[csf('booking_no')]; ?></p></td>
                <td  width="60"><p><? echo $batch[csf('file_no')]; ?></p></td>
                <td  width="70"><p><? echo $batch[csf('grouping')]; ?></p></td>
                <td width="90"><div style="width:90px; word-wrap:break-word;"><? echo $po_number; ?></div></td>

                    <?  
                    $batch_chk_arr[]=$com_wait_grouping_arr_val;
                        } 
                        else
                           { ?>
                <td width="30"><? //echo $sl; ?></td>
                <? if($group_by==2 || $group_by==0){ ?>
                <td  align="center" width="80"><div style="width:80px; word-wrap:break-word;"><? //echo $machine_arr[$batch[csf('machine_id')]]; ?></div></td>
                <?
                 }
                 if($group_by==1 || $group_by==0){ ?>
               <td width="80"><div style="width:80px; word-wrap:break-word;"><? //echo $floor_arr[$batch[csf('floor_id')]]; ?></div></td>
                <? } ?>
                 <td  align="center" width="50"><p><? //echo $machine_id; ?></p></td>
                <td  width="100"><p><? //echo $buyer_arr[$batch[csf('buyer_name')]]; ?></p></td>
                <td  width="100"><p><? //echo $special_row[csf('booking_no')]; ?></p></td>
                <td  width="60"><p><? //echo $batch[csf('file_no')]; ?></p></td>
                <td  width="70"><p><? //echo $batch[csf('grouping')]; ?></p></td>
                <td width="90"><p><? //echo $batch[csf('po_number')]; ?></p></td>
                        <? }
                        ?>
                <td  width="100" title="<? echo $desc[0]; ?>"><p><? echo $batch[csf('item_description')]; ?></p></td>
                <td  width="70" title="<? echo  $desc[2]; ?>"><p><? echo $desc[2]; ?></p></td>
                <td  width="80" title="<? echo $color_library[$batch[csf('color_id')]]; ?>"><p><? echo $color_library[$batch[csf('color_id')]]; ?></p></td>
                <td  align="center" width="90" title="<? echo $batch[csf('batch_no')]; ?>"><p><? echo $batch[csf('batch_no')]; ?></p></td>
                <td  align="center" width="80" title="<? echo $batch[csf('extention_no')]; ?>"><p><? echo $batch[csf('extention_no')]; ?></p></td>
                <td align="right" width="80" title="<? echo $batch[csf('batch_qnty')];  ?>"><p><? echo number_format($batch[csf('batch_qnty')],2);  ?></p></td>
                <td align="right" width="70" ><? echo number_format($batch_prod_qty_arr[$batch[csf('id')]][$batch[csf('prod_id')]][$batch[csf('width_dia_type')]][$insert_date]['drying'],2);  ?></td>
                <td width="100" title="<? echo change_date_format($batch[csf('process_end_date')]); ?>"><p><?  echo change_date_format($batch[csf('process_end_date')])?></p></td>
                <td align="center" width="80" title="<? echo $batch[csf('end_hours')].':'.$batch[csf('end_minutes')];  ?>"><p><? echo $batch[csf('end_hours')].':'.$batch[csf('end_minutes')];   ?></p></td>
                <td align="right" width="60" title="Lot"><p><? echo $yarn_lot_arr[$batch[csf('prod_id')]][$batch[csf('po_id')]]; ?></p></td>
                <td align="center"  title="<? if($batch[csf('batch_against')]==2) echo $batch_against[$batch[csf('batch_against')]]; ?>"><p><?  if($batch[csf('batch_against')]==2) echo $batch_against[$batch[csf('batch_against')]]; else echo '';?> </p></td>
            </tr>
            <? 
            $i++;
            $btq+=$batch[csf('batch_qnty')];
            $tot_prod_btq+=$batch_prod_qty_arr[$batch[csf('id')]][$batch[csf('prod_id')]][$batch[csf('width_dia_type')]][$insert_date]['drying'];

            //}
            } //batchdata froeach
             ?>
            </tbody>
            </table>
             <table class="rpt_table" width="1730" cellpadding="0" cellspacing="0" border="1" rules="all" align="center" id="report_table_footer">
            <tfoot>
            <tr>
                <th width="30">&nbsp;</th>
               <? if($group_by==2 || $group_by==0){ ?>
                 <th width="80">&nbsp;</th>
                <? } ?>
                <? if($group_by==1 || $group_by==0){ ?>
                <th width="80">&nbsp;</th> 
                <? } 
                ?> 
                <th width="50">&nbsp;</th>
                <th width="100">&nbsp;</th> 
                <th width="100">&nbsp;</th> 
                <th width="60">&nbsp;</th> 
                <th width="70">&nbsp;</th>
                <th width="90">&nbsp;</th>
                <th width="100">&nbsp;</th>
                <th width="70">&nbsp;</th>
                <th width="80">&nbsp;</th>
                <th width="90">&nbsp;</th>
                <th width="80">&nbsp;</th>
                <th width="80"><? echo number_format($btq,2); ?></th>
                <th width="70"><? echo number_format($tot_prod_btq,2); ?></th>
                <th width="100">&nbsp;</th>
                <th width="80">&nbsp;</th>
                <th width="60">&nbsp;</th>
                <th>&nbsp;</th>
            </tr>
            </tfoot>
            </table>
            </div>
            </fieldset>
            </div>
      <? }
        else if($cbo_type==9)// Stentering //Not used
        { 
        	?>
            <div style="width:1500px;">
            <fieldset style="width:1500px;">
            <div  align="center"> <strong> <? echo $company_library[$company]; ?> </strong><br><strong> <? echo $search_by_arr[$cbo_type];?> </strong>
            <br>
            <?
                echo  change_date_format($date_from).' '.To.' '.change_date_format($date_to);
            ?>
             </div>
             <table class="rpt_table" width="1520" cellpadding="0" cellspacing="0" border="1" rules="all" align="center" id="table_header_1">
            <thead>
            <tr>
                <th width="30">SL</th>
                   <? if($group_by==2 || $group_by==0){ ?>
                 <th width="80">M/C No</th>
                <? } ?>
                <? if($group_by==1 || $group_by==0){ ?>
                <th width="80">Floor</th> 
                <? } 
                ?> 
                <th width="80"></th>
                <th width="50">Shift</th>
                <th width="100">Buyer</th>
                <th width="100">Booking</th>
                <th width="60">File No</th> 
                <th width="70">Ref. No</th> 
                <th width="90">Order No</th>
                <th width="100">Fabrics Desc</th>
                <th width="70">GSM</th>
                <th width="80">Color Name</th>
                <th width="90">Batch No</th>
                <th width="80">Ext. No</th>
                <th width="80">Batch Qty.</th>
                <th width="100">Process Date</th>
                <th width="80">Process Time</th>
                <th width="60">Yarn Lot</th>
                <th width="80">Remark</th>
                <th>Reprocess</th>
            </tr>
            </thead>
            </table>
            <div style=" max-height:380px; width:1520px; overflow-y:scroll;" id="scroll_body">
            <table class="rpt_table" id="table_body" width="1580" cellpadding="0" cellspacing="0" border="1" rules="all" align="center">
            <tbody>
            <? 
            $i=1;
            $f=0;
            $btq=0;
            $sql_batch_h=sql_select("select batch_id from  pro_fab_subprocess where entry_form=34 and status_active=1 and is_deleted=0 and batch_id>0");
            if($db_type==0) $find_inset="and  FIND_IN_SET(67,68,69,70,73,74,75,77,83,88,92,94,127,128,a.process_id)"; 
                else if($db_type==2) $find_inset="and   ',' || a.process_id || ',' LIKE '%,67,68,69,70,73,74,75,77,83,88,92,94,127,128,%'";
                if($txt_date_from && $txt_date_to)
                {
                    if($db_type==0)
                    {
                $date_from=change_date_format($txt_date_from,'yyyy-mm-dd');
                $date_to=change_date_format($txt_date_to,'yyyy-mm-dd');
                $dates_batch="and  a.batch_date BETWEEN '$date_from' AND '$date_to'";
                    }
                    if($db_type==2)
                    {
                $date_from=change_date_format($txt_date_from,'','',1);
                $date_to=change_date_format($txt_date_to,'','',1);
                $dates_batch="and  a.batch_date BETWEEN '$date_from' AND '$date_to'";
                    }
                }
                $i=1;
                foreach($sql_batch_h as $row_h)
                {
                    if($i!==1) $row_sp.=",";
                    $row_sp.=$row_h[csf('batch_id')];
                    $i++;
                }
            //and a.process_id in(63)
            $w_special=array_chunk(array_unique(explode(",",$row_sp)),999);
            if($w_special!=0)
            {
            $sql_wait=("select a.company_id,a.id,a.batch_no, a.process_id,a.batch_date,a.color_id,a.extention_no,a.batch_against,SUM(b.batch_qnty) AS batch_qnty,b.item_description,b.po_id,b.prod_id,b.width_dia_type,$group_concat,c.job_no_mst,d.job_no_prefix_num,d.buyer_name,c.file_no,c.grouping,f.shift_name,f.process_end_date,f.process_start_date,f.production_date as end_date,f.start_minutes,f.start_hours,f.end_hours,f.end_minutes,f.machine_id,f.floor_id,f.remarks,f.re_stenter_no,f.insert_date $heat_set from pro_batch_create_dtls b,wo_po_break_down c,wo_po_details_master d,pro_batch_create_mst a,pro_fab_subprocess f,lib_color g where a.company_id=$company and  f.batch_id=a.id $dates_com $jobdata $batch_num $buyerdata $order_no $year_cond $color_name and a.entry_form=0 and  g.id=a.color_id and a.id=b.mst_id and f.entry_form=33 and  a.batch_against in(1,2) and b.po_id=c.id and d.job_no=c.job_no_mst and b.status_active=1 and b.is_deleted=0 and a.status_active=1   and a.is_deleted=0 ");
                    $p=1;
                    foreach($w_special as $sp_row)
                    {
                        if($p==1)  $sql_wait .="and (a.id not in(".implode(',',$sp_row).")"; else  $sql_wait .=" and a.id not in(".implode(',',$sp_row).")";
                        $p++;
                    }
                    $sql_wait .=")";
                    $sql_wait .=" GROUP BY b.po_id, b.item_description,a.company_id, a.id, a.batch_no,a.process_id, a.batch_date,a.color_id, a.extention_no, a.batch_against,b.prod_id, b.width_dia_type,c.job_no_mst, d.job_no_prefix_num, d.buyer_name,c.file_no,c.grouping,f.shift_name, f.process_end_date,f.process_start_date,f.production_date,f.start_minutes,f.start_hours, f.end_hours, f.end_minutes, f.machine_id,  f.floor_id,f.remarks,f.re_stenter_no,f.insert_date $heat_group  $order_by"; 
                    //echo $sql_wait;
            }
            $batch_chk_arr=array();
            $sql_wait_data=sql_select($sql_wait);
            foreach($sql_wait_data as $batch)
            { 
            if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
            $order_id=$batch[csf('po_id')];
            $color_id=$batch[csf('color_id')];
            $desc=explode(",",$batch[csf('item_description')]); 
            $insert_date = "'".$batch[csf('insert_date')]."'";
            $po_number=implode(",",array_unique(explode(",",$batch[csf('po_number')])));
            $processid=explode(",",$batch[csf('process_id')]);
            $process_name=array(67,68,69);
            $process_count=count($processid);
            //echo $process_count;die;
            $process = explode(",",str_replace("'","",$process_arr));
            //echo $process_sql=count($process_name);
            $arrdif=count(array_diff($processid,$process_name));
            //echo $arrdif;
            //echo $arrdif.'<br>'; 
            //print_r(array_diff($processid,$process_name));echo '<br>';
            //print_r( $process);die;
            //print_r(array_diff($process_name,$processid));die;
            //if (array_diff($processid,$process_name))
            //if ($process_count!==$arrdif)
            //{
            if($group_by!=0)
            {
                if($group_by==1)
                {
                    $group_value=$batch[csf('floor_id')];
                    $group_name="Floor";
                    $group_dtls_value=$floor_arr[$batch[csf('floor_id')]];
                }
                
                else if($group_by==2)
                {
                    $group_value=$batch[csf('machine_id')];
                    $group_name="Machine";
                    $group_dtls_value=$machine_arr[$batch[csf('machine_id')]];//
                }
                if (!in_array($group_value,$group_by_arr) )
                    {
                        //if($k!=1)
                        //{
                        ?>  
                        <tr bgcolor="#EFEFEF">
                            <td colspan="22" align="left" ><b><? echo $group_name; ?> : <? echo $group_dtls_value; ?></b></td>
                        </tr>
                        <?
                        $group_by_arr[]=$group_value; 
                        //}
                       // $k++;
                    }
            }
            ?>
            <tr bgcolor="<? echo $bgcolor;?>" onClick="change_color('tr<? echo $i;?>','<? echo $bgcolor;?>')" id="tr<? echo $i;?>">
                <? if (!in_array($batch[csf('batch_no')],$batch_chk_arr) )
                        { $f++;
                            ?>
                <td width="30"><? echo $f; ?></td>
               <? if($group_by==2 || $group_by==0){ ?>
                <td  align="center" width="80"><div style="width:80px; word-wrap:break-word;"><? echo $machine_arr[$batch[csf('machine_id')]]; ?></div></td>
                <?
                 }
                 if($group_by==1 || $group_by==0){ ?>
               <td width="80"><div style="width:80px; word-wrap:break-word;"><? echo $floor_arr[$batch[csf('floor_id')]]; ?></div></td>
                <? } ?>
                 <td  align="center" width="50"><p><? echo $shift_name[$batch[csf('shift_name')]]; ?></p></td>
                <td  width="100" title="<? echo $buyer_arr[$batch[csf('buyer_name')]]; ?>"><p><? echo $buyer_arr[$batch[csf('buyer_name')]]; ?></p></td>
                 <td  width="100"><p><? echo $batch[csf('booking_no')]; ?></p></td>
                 <td  width="60"><p><? echo $batch[csf('file_no')]; ?></p></td>
                <td  width="70"><p><? echo $batch[csf('grouping')]; ?></p></td>
                <td width="90"><div style="width:90px; word-wrap:break-word;"><? echo $po_number; ?></div></td>
                    <?  
                    $batch_chk_arr[]=$batch[csf('batch_no')];
                        } 
                        else
                           { ?>
                <td width="30"><? //echo $sl; ?></td>
               <? if($group_by==2 || $group_by==0){ ?>
                <td  align="center" width="80"><div style="width:80px; word-wrap:break-word;"><? //echo $machine_arr[$batch[csf('machine_id')]]; ?></div></td>
                <?
                 }
                 if($group_by==1 || $group_by==0){ ?>
               <td width="80"><div style="width:80px; word-wrap:break-word;"><? //echo $floor_arr[$batch[csf('floor_id')]]; ?></div></td>
                <? } ?>
                 <td  align="center" width="50"><p><? //echo $machine_id; ?></p></td>
                <td  width="100"><p><? //echo $buyer_arr[$batch[csf('buyer_name')]]; ?></p></td>
                 <td  width="60"><p><? //echo $batch[csf('file_no')]; ?></p></td>
                <td  width="70"><p><? //echo $batch[csf('grouping')]; ?></p></td>
                <td width="90"><p><? //echo $batch[csf('po_number')]; ?></p></td>
                        <? }
                        ?>
                <td  width="100" title="<? echo $desc[0]; ?>"><p><? echo $batch[csf('item_description')]; ?></p></td>
                <td  width="70" title="<? echo  $desc[2]; ?>"><p><? echo $desc[2]; ?></p></td>
                <td  width="80" title="<? echo $color_library[$batch[csf('color_id')]]; ?>"><p><? echo $color_library[$batch[csf('color_id')]]; ?></p></td>
                <td  align="center" width="90" title="<? echo $batch[csf('batch_no')]; ?>"><p><? echo $batch[csf('batch_no')]; ?></p></td>
                <td  align="center" width="80" title="<? echo $batch[csf('extention_no')]; ?>"><p><? echo $batch[csf('extention_no')]; ?></p></td>
                <td align="right" width="80" title="<? echo $batch[csf('batch_qnty')];  ?>"><p><? echo number_format($batch[csf('batch_qnty')],2);  ?></p></td>
                <td width="100" title="<? echo change_date_format($batch[csf('process_end_date')]); ?>"><p><?  echo change_date_format($batch[csf('process_end_date')])?></p></td>
                <td align="center" width="80" title="<? echo $batch[csf('end_hours')].':'.$batch[csf('end_minutes')];  ?>"><p><? echo $batch[csf('end_hours')].':'.$batch[csf('end_minutes')];   ?></p></td>
                <td align="right" width="60" title="Lot"><p><? echo $yarn_lot_arr[$batch[csf('prod_id')]][$batch[csf('po_id')]]; ?></p></td>
                 <td  width="80" title="<? echo  $batch[csf('remarks')]; ?>"><p><? echo $batch[csf('remarks')]; ?></p></td>
                <td align="center"  title="<? if($batch[csf('batch_against')]==2) echo $batch_against[$batch[csf('batch_against')]]; ?>"><p><?  if($batch[csf('batch_against')]==2) echo $batch_against[$batch[csf('batch_against')]]; else echo '';?> </p></td>
            </tr>
            <? 
            $i++;
            $btq+=$batch[csf('batch_qnty')];
            //}
            } //batchdata froeach
             ?>
            </tbody>
            </table>
             <table class="rpt_table" width="1480" cellpadding="0" cellspacing="0" border="1" rules="all" align="center" id="report_table_footer">
            <tfoot>
            <tr>
                <th width="30">&nbsp;</th>
                <? if($group_by==2 || $group_by==0){ ?>
                 <th width="80">&nbsp;</th>
                <? } ?>
                <? if($group_by==1 || $group_by==0){ ?>
                <th width="80">&nbsp;</th> 
                <? } 
                ?> 
                <th width="50">&nbsp;</th>
                <th width="100">&nbsp;</th> 
                <th width="100">&nbsp;</th> 
                <th width="60">&nbsp;</th> 
                <th width="70">&nbsp;</th>
                <th width="90">&nbsp;</th>
                <th width="100">&nbsp;</th>
                <th width="70">&nbsp;</th>
                <th width="80">&nbsp;</th>
                <th width="90">&nbsp;</th>
                <th width="80">&nbsp;</th>
                <th width="80"><? echo number_format($btq,2); ?></th>
                <th width="100">&nbsp;</th>
                <th width="80">&nbsp;</th>
                <th width="60">&nbsp;</th>
                <th>&nbsp;</th>
            </tr>
            </tfoot>
            </table>
            </div>
            </fieldset>
            </div>
            <? }
        else if($cbo_type==10)// Stentering // Data Comee From Slitting/Squeezing
        { 
            //echo "FDDD";
            // echo "<pre>"; print_r($batch_prod_qty_arr);
            ?>
            <div style="width:1650px;">
            <fieldset style="width:1650px;">
            <div  align="center"> <strong> <? echo $company_library[$company]; ?> </strong><br><strong> <? echo $search_by_arr[$cbo_type];?> </strong>
            <br>
            <?
                echo  change_date_format($date_from).' '.To.' '.change_date_format($date_to);
            ?>
             </div>
             <table class="rpt_table" width="1670" cellpadding="0" cellspacing="0" border="1" rules="all" align="center" id="table_header_1">
            <thead>
            <tr>
                <th width="30">SL</th>
               
                <? if($group_by==2 || $group_by==0){ ?>
                  <th width="80">M/C No</th>
                <? } ?>
                <? if($group_by==1 || $group_by==0){ ?>
                <th width="80">Floor</th> 
                <? } 
                ?> 
                <th width="50">Shift</th>
                <th width="80">Job No</th>
                <th width="100">Buyer</th>
                <th width="100">Booking</th>
                <th width="60">File No</th> 
                <th width="70">Ref. No</th> 
                <th width="90">Order No</th>
                <th width="100">Fabrics Desc</th>
                <th width="70">GSM</th>
                <th width="80">Color Name</th>
                <th width="90">Batch No</th>
                <th width="80">Ext. No</th>
                <th width="80">Batch Qty.</th>
                <th width="70">Prod. Qty.</th>
                <th width="100">Process Date</th>
                <th width="80">Process Time</th>
                <th width="60">Yarn Lot</th>
                <th>Reprocess</th>
            </tr>
            </thead>
            </table>
            <div style=" max-height:380px; width:1670px; overflow-y:scroll;" id="scroll_body">
            <table class="rpt_table" id="table_body" width="1650" cellpadding="0" cellspacing="0" border="1" rules="all" align="center">
            <tbody>
            <? 
            $i=1;
            $f=0;
            $btq=0;$tot_prod_btq=0;
            /*$sql_batch_h=sql_select("select a.batch_id from  pro_fab_subprocess a,pro_fab_subprocess_dtls b where a.entry_form=48 and a.id=b.mst_id and b.width_dia_type in(1,3) and a.status_active=1 and a.is_deleted=0 and a.batch_id>0");
            $i=1;
            foreach($sql_batch_h as $row_h)
            {
                if($i!==1) $row_sent.=",";
                $row_sent.=$row_h[csf('batch_id')];
                $i++;
            }
            //and a.process_id in(63)
            $w_sent=array_chunk(array_unique(explode(",",$row_sent)),999);
            $w_sent=array_chunk(array_unique(explode(",",$row_sent)),999);*/
            $sql_wait="SELECT a.company_id,a.id,a.batch_no, a.process_id,a.batch_date,a.color_id,a.extention_no,a.batch_against,SUM(b.batch_qnty) AS batch_qnty,b.item_description,b.po_id,b.prod_id,b.width_dia_type,b.barcode_no,b.roll_id,$group_concat,c.job_no_mst,d.job_no_prefix_num,d.buyer_name,c.file_no,c.grouping,f.shift_name,f.process_end_date,f.process_start_date,f.production_date as end_date,f.start_minutes,f.start_hours,f.end_hours,f.end_minutes,f.machine_id,f.floor_id,f.remarks,f.re_stenter_no,f.insert_date $sliting_sq from pro_batch_create_dtls b,wo_po_break_down c,wo_po_details_master d,pro_batch_create_mst a,pro_fab_subprocess f where f.batch_id=a.id  and a.id=b.mst_id and b.po_id=c.id and d.job_no=c.job_no_mst  and f.entry_form=30 $company_cond $working_company_cond   $dates_com $jobdata $batch_num $buyerdata $order_no $year_cond $color_name and a.entry_form=0   and  a.batch_against in(1,2) and b.status_active=1 and b.is_deleted=0 and a.status_active=1  and a.is_deleted=0 and b.width_dia_type in(1,3) 

            GROUP BY b.po_id, b.item_description,a.company_id, a.id, a.batch_no,a.process_id, a.batch_date,a.color_id, a.extention_no, a.batch_against,b.prod_id, b.barcode_no,b.roll_id,b.width_dia_type,c.job_no_mst, d.job_no_prefix_num, d.buyer_name, c.file_no,c.grouping,f.shift_name,f.process_end_date,f.process_start_date,f.production_date,f.start_minutes,f.start_hours, f.end_hours, f.end_minutes, f.machine_id,f.floor_id, f.remarks,f.re_stenter_no,f.insert_date $sliting_group  $order_by "; 
            // echo $sql_wait;  
              //-------------------------------------------------
                $fab_wait=sql_select($sql_wait);
                $batchIdArr=array();
                foreach($fab_wait as $rows)
                { 
                    $batchIdArr[$rows[csf('id')]]=$rows[csf('id')];
                }
                
                
                $remove_batch_sql="select a.BATCH_ID from  pro_fab_subprocess a,pro_fab_subprocess_dtls b where a.entry_form=48 and a.id=b.mst_id and b.width_dia_type in(1,3) and a.status_active=1 and a.is_deleted=0 and a.batch_id>0 ";

                $batch_array_chunk=array_chunk($batchIdArr,999);
                $p=1;
                foreach($batch_array_chunk as $bid)
                {
                    if($p==1)  $remove_batch_sql .="and (a.batch_id not in(".implode(',',$bid).")"; 
                    else  $remove_batch_sql .=" and a.batch_id not in(".implode(',',$bid).")";
                    
                    $p++;
                }
                $remove_batch_sql .=")";
                $remove_batch_sql_result=sql_select($remove_batch_sql);
                $remove_batch_arr=array();
                foreach($remove_batch_sql_result as $rows)
                { 
                    $remove_batch_arr[$rows[BATCH_ID]]=1;
                }
            //---------------------------------------
              
              
              
            //echo $sql_wait;
            $batch_chk_arr=array();

            foreach($fab_wait as $batch)
            { 
                if($remove_batch_arr[$batch[csf('id')]]==1){continue;}
	            if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
	            $order_id=$batch[csf('po_id')];
	            $color_id=$batch[csf('color_id')];
	            $desc=explode(",",$batch[csf('item_description')]); 
	            $insert_date = "'".$batch[csf('insert_date')]."'";
	            $po_number=implode(",",array_unique(explode(",",$batch[csf('po_number')])));
	            $processid=explode(",",$batch[csf('process_id')]);
	            //echo $batch[csf('process_id')];die;
	            $result=$batch[csf('result')];
	            //$process_arr='66,91';
	            $process_name=array(66,91,125);
	            $process_count=count($processid);
	            $process = explode(",",str_replace("'","",$process_arr));
	            $process_sql=count($processid);
	            //print_r( $process);die;
	            //print_r(array_diff($process_name,$processid));die;

	            //if (in_array(65,$processid))
	            //{
	            if($group_by!=0)
	            {
	                if($group_by==1)
	                {
	                    $group_value=$batch[csf('floor_id')];
	                    $group_name="Floor";
	                    $group_dtls_value=$floor_arr[$batch[csf('floor_id')]];
	                }
	                
	                else if($group_by==2)
	                {
	                    $group_value=$batch[csf('machine_id')];
	                    $group_name="Machine";
	                    $group_dtls_value=$machine_arr[$batch[csf('machine_id')]];//
	                }
	                if (!in_array($group_value,$group_by_arr) )
	                    {
	                        //if($k!=1)
	                        //{
	                        ?>  
	                        <tr bgcolor="#EFEFEF">
	                            <td colspan="23" align="left" ><b><? echo $group_name; ?> : <? echo $group_dtls_value; ?></b></td>
	                        </tr>
	                        <?
	                        $group_by_arr[]=$group_value; 
	                        //}
	                       // $k++;
	                    }
	            }
	            ?>
	            <tr bgcolor="<? echo $bgcolor;?>" onClick="change_color('tr<? echo $i;?>','<? echo $bgcolor;?>')" id="tr<? echo $i;?>">
	                <? if (!in_array($batch[csf('batch_no')],$batch_chk_arr) )
	                        { $f++;
	                            ?>
	                <td width="30"><? echo $f; ?></td>
	                 <? if($group_by==2 || $group_by==0){ ?>
	                <td  align="center" width="80"><div style="width:80px; word-wrap:break-word;"><? echo $machine_arr[$batch[csf('machine_id')]]; ?></div></td>
	                <?
	                 }
	                 if($group_by==1 || $group_by==0){ ?>
	               <td width="80"><div style="width:80px; word-wrap:break-word;"><? echo $floor_arr[$batch[csf('floor_id')]]; ?></div></td>
	                <? } ?>
	                <td  align="center" width="50"><p><? echo $shift_name[$batch[csf('shift_name')]]; ?></p></td> 
	                <td  align="center" width="80"><p><? echo $batch[csf('job_no_prefix_num')]; ?></p></td>
	                <td  width="100" title="<? echo $buyer_arr[$batch[csf('buyer_name')]]; ?>"><p><? echo $buyer_arr[$batch[csf('buyer_name')]]; ?></p></td>
	                 <td  width="100"><p><? echo $batch[csf('booking_no')]; ?></p></td>
	                 <td  width="60"><p><? echo $batch[csf('file_no')]; ?></p></td>
	                <td  width="70"><p><? echo $batch[csf('grouping')]; ?></p></td>
	                <td width="90" title="<? echo $po_number; ?>"><p><? echo $po_number; ?></p></td>
	                    <?  
	                    $batch_chk_arr[]=$batch[csf('batch_no')];
	                        } 
	                        else
	                           { ?>
	                <td width="30"><? //echo $sl; ?></td>
	                <? if($group_by==2 || $group_by==0){ ?>
	                <td  align="center" width="80"><div style="width:80px; word-wrap:break-word;"><? //echo $machine_arr[$batch[csf('machine_id')]]; ?></div></td>
	                <?
	                 }
	                 if($group_by==1 || $group_by==0){ ?>
	               <td width="80"><div style="width:80px; word-wrap:break-word;"><? //echo $floor_arr[$batch[csf('floor_id')]]; ?></div></td>
	                <? } ?>
	                <td  align="center" width="50"><p><? //echo $machine_id; ?></p></td>
	                <td  align="center" width="80"><p><? //echo $machine_id; ?></p></td>
	                <td  width="100"><p><? //echo $buyer_arr[$batch[csf('buyer_name')]]; ?></p></td>
	                <td  width="100"><p><? //echo $special_row[csf('booking_no')]; ?></p></td>
	                <td  width="60"><p><? //echo $batch[csf('file_no')]; ?></p></td>
	                <td  width="70"><p><? //echo $batch[csf('grouping')]; ?></p></td>
	                <td width="90"><p><? //echo $batch[csf('po_number')]; ?></p></td>
	                        <? }
	                        ?>
	                <td  width="100" title="<? echo $desc[0]; ?>"><p><? echo $batch[csf('item_description')]; ?></p></td>
	                <td  width="70" title="<? echo  $desc[2]; ?>"><p><? echo $desc[2]; ?></p></td>
	                <td  width="80" title="<? echo $color_library[$batch[csf('color_id')]]; ?>"><p><? echo $color_library[$batch[csf('color_id')]]; ?></p></td>
	                <td  align="center" width="90" title="<? echo $batch[csf('batch_no')]; ?>"><p><? echo $batch[csf('batch_no')]; ?></p></td>
	                <td  align="center" width="80" title="<? echo $batch[csf('extention_no')]; ?>"><p><? echo $batch[csf('extention_no')]; ?></p></td>
	                <td align="right" width="80" title="<? echo $batch[csf('batch_qnty')];  ?>"><p><? echo number_format($batch[csf('batch_qnty')],2);  ?></p></td>
	                <td align="right" width="70" ><? 
	                
	                 echo number_format($batch_prod_qty_arr[$batch[csf('id')]][$batch[csf('prod_id')]][$batch[csf('width_dia_type')]][$batch[csf('roll_id')]][$batch[csf('barcode_no')]][$insert_date]['sliting'],2);  ?></td>
	                <td width="100" title="<? echo change_date_format($batch[csf('process_end_date')]); ?>"><p><?  echo change_date_format($batch[csf('process_end_date')])?></p></td>
	                <td align="center" width="80" title="<? echo $batch[csf('end_hours')].':'.$batch[csf('end_minutes')];  ?>"><p><? echo $batch[csf('end_hours')].':'.$batch[csf('end_minutes')];   ?></p></td>
	                <td align="right" width="60" title="Lot"><p><? echo $yarn_lot_arr[$batch[csf('prod_id')]][$batch[csf('po_id')]]; ?></p></td>
	                <td align="center"  title="<? if($batch[csf('batch_against')]==2) echo $batch_against[$batch[csf('batch_against')]]; ?>"><p><?  if($batch[csf('batch_against')]==2) echo $batch_against[$batch[csf('batch_against')]]; else echo '';?> </p></td>
	            </tr>
            <? 
            $i++;
            $btq+=$batch[csf('batch_qnty')];
            // $tot_prod_btq+=$batch_prod_qty_arr[$batch[csf('id')]][$batch[csf('prod_id')]][$batch[csf('width_dia_type')]][$insert_date]['sliting'];
            $tot_prod_btq+=$batch_prod_qty_arr[$batch[csf('id')]][$batch[csf('prod_id')]][$batch[csf('width_dia_type')]][$batch[csf('roll_id')]][$batch[csf('barcode_no')]][$insert_date]['sliting'];
            //}
            } //batchdata froeach
             ?>
            </tbody>
            </table>
             <table class="rpt_table" width="1650" cellpadding="0" cellspacing="0" border="1" rules="all" align="center" id="report_table_footer">
            <tfoot>
            <tr>
                <th width="30">&nbsp;</th>
                 <? if($group_by==2 || $group_by==0){ ?>
                 <th width="80">&nbsp;</th>
                <? } ?>
                <? if($group_by==1 || $group_by==0){ ?>
                <th width="80">&nbsp;</th> 
                <? } 
                ?> 
                <th width="50">&nbsp;</th>
                 <th width="80">&nbsp;</th>
                <th width="100">&nbsp;</th>
                <th width="100">&nbsp;</th>
                <th width="60">&nbsp;</th>  
                <th width="70">&nbsp;</th> 
                 <th width="90">&nbsp;</th>
                <th width="100">&nbsp;</th>
                <th width="70">&nbsp;</th>
                <th width="80">&nbsp;</th>
                <th width="90">&nbsp;</th>
                <th width="80">&nbsp;</th>
                <th width="80"><? echo number_format($btq,2); ?></th>
                <th width="70"><? echo number_format($tot_prod_btq,2); ?></th>
                <th width="100">&nbsp;</th>
                <th width="80">&nbsp;</th>
                <th width="60">&nbsp;</th>
                <th>&nbsp;</th>
            </tr>
            </tfoot>
            </table>
            </div>
            </fieldset>
            </div>
        <? }
        else if($cbo_type==0) // All Search Fab. Finishing
        {
            $group_by=str_replace("'",'',$cbo_group_by);
            //echo $group_by;
            ?>
            <div>
            <fieldset style="width:1765px;">
            <div align="center"><strong> <? echo $company_library[$company]; ?> <br>
            <?
                echo  change_date_format($date_from).' '.To.' '.change_date_format($date_to);
            ?>
            </strong><br> <strong>Heat Setting </strong>
             </div>

             <table class="rpt_table" width="1835" cellpadding="0" cellspacing="0" border="1" rules="all" align="center" id="table_header_1">
            <thead>
            <tr>
                <th width="30">SL</th>
                 <? if($group_by==2 || $group_by==0){ ?>
                 <th width="80">M/C No</th>
                 <? } ?>
                <? if($group_by==1 || $group_by==0){ ?>
                <th width="80">Floor</th>  
                 <? } ?>
                
                <th width="50">Shift</th>
                <th width="100">Buyer</th>
                <th width="80">Job</th>
                <th width="100">Booking</th>
                <th width="60">File No</th>
                <th width="70">Ref. No</th>
                <th width="90">Order No</th>
                <th width="100">Fabrics Desc</th>
                <th width="75">GSM</th>
                <th width="70">Dia/Width Type</th>
                <th width="80">Color Name</th>
                <th width="90">Batch No</th>
                <th width="40">Extn. No</th>
                <th width="70">Batch Qty.</th>
                <th width="70">Prod. Qty.</th>
                <th width="50">Lot No</th>
                <th width="75">Start Date & Time</th>
                <th width="75">End Date & Time</th>
                <th width="70">Time Used</th>
                <th width="60">Remark</th>
                <th>Reprocess</th>
            </tr>
            </thead>
            </table>
            <div style=" max-height:350px; width:1835px; overflow-y:scroll;;" id="scroll_body">
            <table class="rpt_table" id="table_body" width="1815" cellpadding="0" cellspacing="0" border="1" rules="all" align="center">
            <tbody>
            <? 
            $i=1;
            $f=0;$k=1;
            $btq=0;
            $batch_chk_arr=array(); $group_by_arr=array();
            //echo $sql_heat;
            $heatsetting=sql_select($sql_heat); $tot_prod_qty_heat=0;
            foreach($heatsetting as $heat_row)
            { 
            if ($i%2==0)  
            $bgcolor="#E9F3FF";
            else
            $bgcolor="#FFFFFF";
            if($group_by!=0)
            {
                if($group_by==1)
                {
                    $group_value=$heat_row[csf('floor_id')];
                    $group_name="Floor";
                    $group_dtls_value=$floor_arr[$heat_row[csf('floor_id')]];
                }
                
                else if($group_by==2)
                {
                    $group_value=$heat_row[csf('machine_id')];
                    $group_name="Machine";
                    $group_dtls_value=$machine_arr[$heat_row[csf('machine_id')]];
                }
                if (!in_array($group_value,$group_by_arr) )
                    {
                        if($k!=1)
                        { ?>
                        
                        <tr  bgcolor="#D4D4D4" >
                       <td align="left" colspan="24"><Strong> Batch Sub Total:</Strong> <b><? echo number_format($btq_heat,2); ?> </b> &nbsp;&nbsp;&nbsp;<b> Prod. Sub Total:<? echo number_format($tot_prod_qty_heat,2); ?> </b></td>
                       
                        </tr>                                
                            <?
                            unset($btq_heat);unset($tot_prod_qty_heat);
                        }
                        ?>  
                    
                    
                        <tr bgcolor="#EFEFEF">
                            <td colspan="24" align="left" ><b><? echo $group_name; ?> : <? echo $group_dtls_value; ?></b></td>
                        </tr>
                        <?
                        $group_by_arr[]=$group_value;            
                        $k++;
                        
                    }
                    
            }

            $order_id=$heat_row[csf('po_id')];
            $color_id=$heat_row[csf('color_id')];
            $desc=explode(",",$heat_row[csf('item_description')]); 
            $insert_date = "'".$heat_row[csf('insert_date')]."'";
            $po_number=implode(",",array_unique(explode(",",$heat_row[csf('po_number')]))); 
            ?>
            <tr bgcolor="<? echo $bgcolor;?>" onClick="change_color('tr<? echo $i;?>','<? echo $bgcolor;?>')" id="tr<? echo $i;?>">
                  <? if (!in_array($heat_row[csf('batch_no')],$batch_chk_arr) )
                        { $f++;
                            ?>
                <td width="30"><? echo $f; ?></td>
                <? if($group_by==2 || $group_by==0){ ?>
                <td  align="center" width="80" title="<? echo $machine_arr[$heat_row[csf('machine_id')]]; ?>"><p><? echo $machine_arr[$heat_row[csf('machine_id')]]; ?></p></td>
                <?
                 }
                 if($group_by==1 || $group_by==0){ ?>
                <td width="80"><p><? echo $floor_arr[$heat_row[csf('floor_id')]]; ?></p></td>
                <? }
                ?>
                <td  align="center" width="50" ><p><? echo $shift_name[$heat_row[csf('shift_name')]]; ?></p></td>
                
                <td  width="100" title="<? echo $buyer_arr[$heat_row[csf('buyer_name')]]; ?>"><p><? echo $buyer_arr[$heat_row[csf('buyer_name')]]; ?></p></td>
                <td  width="80" title="<? echo  $heat_row[csf('job_no_prefix_num')]; ?>"><p><? echo $heat_row[csf('job_no_prefix_num')]; ?></p></td>
                 <td  width="100"><p><? echo $heat_row[csf('booking_no')]; ?></p></td>
                <td  width="60"><p><? echo $heat_row[csf('file_no')]; ?></p></td>
                <td  width="70"><p><? echo $heat_row[csf('grouping')]; ?></p></td>
                <td  width="90"><div style="width:90px; word-wrap:break-word;"><? echo $po_number; ?></div></td>
                
                <?  $batch_chk_arr[]=$heat_row[csf('batch_no')];
                        }
                        else
                        { ?>
                 <td width="30"><? //echo $f; ?></td>
                <? if($group_by==2 || $group_by==0){ ?>
                <td  align="center" width="80" ><p><? //echo $machine_arr[$heat_row[csf('machine_id')]]; ?></p></td>
                <?
                 }
                 if($group_by==1 || $group_by==0){ ?>
                <td width="80"><p><? //echo $floor_arr[$heat_row[csf('floor_id')]]; ?></p></td>
                <? }
                ?>
                <td  align="center" width="50" ><p><? //echo $shift_name[$heat_row[csf('shift_name')]]; ?></p></td>
                
                <td  width="100"><p><? //echo $buyer_arr[$heat_row[csf('buyer_name')]]; ?></p></td>
                <td  width="80"><p><? //echo $heat_row[csf('job_no_prefix_num')]; ?></p></td>
                <td  width="100"><p><? //echo $heat_row[csf('booking_no')]; ?></p></td>
                <td  width="60"><p><? //echo $heat_row[csf('file_no')]; ?></p></td>
                <td  width="70"><p><? //echo $heat_row[csf('grouping')]; ?></p></td>
                <td  width="90"><div style="width:90px; word-wrap:break-word;"><? //echo $po_number; ?></div></td>
                            
                <? }
                ?>
                <td  width="100" title="<? echo $desc[0]; ?>"><p><? echo $heat_row[csf('item_description')]; ?></p></td>
                <td  width="70" title="<? echo   $desc[2]; ?>"><p><? echo  $desc[2]; ?></p></td>
                <td  width="75" title="<? ?>"><p><? echo $fabric_typee[$heat_row[csf('width_dia_type')]];;?></p></td>
                <td  width="80" title="<? echo $color_library[$heat_row[csf('color_id')]]; ?>"><p><? echo $color_library[$heat_row[csf('color_id')]]; ?></p></td>
                <td  align="center" width="90" title="<? echo $heat_row[csf('batch_no')]; ?>"><p><? echo $heat_row[csf('batch_no')]; ?></p></td>
                <td  align="center" width="40" title="<? echo $heat_row[csf('extention_no')]; ?>"><p><? echo $heat_row[csf('extention_no')]; ?></p></td>
                <td align="right" width="70"><? echo number_format($heat_row[csf('batch_qnty')],2);  ?></td>
                 <td align="right" width="70" ><? 
                    
                        // if ($roll_maintained==1 && $cbo_type!=0) 
                        if(($page_upto==1 || $page_upto>1) && $roll_maintained==1)
                         {
                             echo number_format($batch_prod_qty_arr[$heat_row[csf('id')]][$heat_row[csf('prod_id')]][$heat_row[csf('width_dia_type')]][$heat_row[csf('roll_no')]][$insert_date]['heat'],2);
                         }
                         else
                         {
                             echo number_format($batch_prod_qty_arr[$heat_row[csf('id')]][$heat_row[csf('prod_id')]][$heat_row[csf('width_dia_type')]][$insert_date]['heat'],2);

                         }
                 
                   ?></td>
                <td align="left" width="50" title="<? echo $yarn_lot_arr[$heat_row[csf('prod_id')]][$heat_row[csf('po_id')]]; ?>"><p><? echo $yarn_lot_arr[$heat_row[csf('prod_id')]][$heat_row[csf('po_id')]];  ?></p></td>
                <td width="75" title="Process Start Date"><div style="width:75px; word-wrap:break-word;"><?  echo change_date_format($heat_row[csf('process_start_date')]).', '.$heat_row[csf('start_hours')].':'.$heat_row[csf('start_minutes')]; ?></div></td>
                <td width="75" title="Process End Date"><div style="width:75px; word-wrap:break-word;"><?  echo change_date_format($heat_row[csf('end_date')]).', '.$heat_row[csf('end_hours')].':'.$heat_row[csf('end_minutes')]; ?></div></td>
                 <td width="70" align="center"><div style="width:70px; word-wrap:break-word;"><?
                        $end_time=$heat_row[csf('end_hours')].':'.$heat_row[csf('end_minutes')];
                        $start_time=$heat_row[csf('start_hours')].':'.$heat_row[csf('start_minutes')];
                        
                        $new_date_time_start=($heat_row[csf('process_start_date')].' '.$start_time.':'.'00');
                        $new_date_time_end=($heat_row[csf('end_date')].' '.$end_time.':'.'00');
                        $total_time=datediff(n,$new_date_time_start,$new_date_time_end);
                        echo floor($total_time/60).":".$total_time%60; ?></div></td>
                <td align="center" width="60"><p><?   echo $batch[csf('remarks')]; ?> </p>
                 </td>
                <td align="center" title="<?   if($heat_row[csf('batch_against')]==2) echo $batch_against[$heat_row[csf('batch_against')]]; ?>"><p><?  if($heat_row[csf('batch_against')]==2) echo $batch_against[$heat_row[csf('batch_against')]]; ?></p> </td>
            </tr>
            <? 
            $i++;
                if(($page_upto==1 || $page_upto>1) && $roll_maintained==1)
                 {
                     $tot_prod_qty_heat+=$batch_prod_qty_arr[$heat_row[csf('id')]][$heat_row[csf('prod_id')]][$heat_row[csf('width_dia_type')]][$heat_row[csf('roll_no')]][$insert_date]['heat'];
                 }
                 else
                 {
                    $tot_prod_qty_heat+=$batch_prod_qty_arr[$heat_row[csf('id')]][$heat_row[csf('prod_id')]][$heat_row[csf('width_dia_type')]][$insert_date]['heat'];


                 }

            $btq_heat+=$heat_row[csf('batch_qnty')];
            } //batchdata froeach

            if($group_by!=0)
            {
                
                ?>
                        <tr  bgcolor="#D4D4D4" >
                       <td align="left" colspan="24"><Strong> Batch Sub Total:</Strong> <b><? echo number_format($btq_heat,2); ?> </b> &nbsp;&nbsp;&nbsp;<b>Prod. Sub Total:&nbsp;<? echo number_format($tot_prod_qty_heat,2); ?> </b> </td>
                      
                        </tr>                                
                            <?
            }

             ?>
                  <tr bgcolor="#C2DCFF">
                       <td colspan="24" align="center"><strong>Slitting/Squeezing</strong></td>
                 </tr>
                 <?
                 // echo $sql_slitting;
                 $f=0;$k=1;$tot_prod_qty_siltting=0;
                 $batch_chk_arr=array(); $group_by_arr=array();
                 $slitting_data=sql_select($sql_slitting);
                 foreach($slitting_data as $slitting_row)
                 {
                if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
                $order_id=$slitting_row[csf('po_id')];
                $color_id=$slitting_row[csf('color_id')];
                $desc=explode(",",$slitting_row[csf('item_description')]); 
                $insert_date = "'".$slitting_row[csf('insert_date')]."'";
                $po_number=implode(",",array_unique(explode(",",$slitting_row[csf('po_number')])));
                
            if($group_by!=0)
            {
                if($group_by==1)
                {
                    $group_value=$slitting_row[csf('floor_id')];
                    $group_name="Floor";
                    $group_dtls_value=$floor_arr[$slitting_row[csf('floor_id')]];
                }
                
                else if($group_by==2)
                {
                    $group_value=$slitting_row[csf('machine_id')];
                    $group_name="Machine";
                    $group_dtls_value=$machine_arr[$slitting_row[csf('machine_id')]];
                }
                if (!in_array($group_value,$group_by_arr) )
                    {
                        if($k!=1)
                        { ?>
                        
                        <tr  bgcolor="#D4D4D4" >
                       <td align="left" colspan="24"><Strong> Sub Total:</Strong> <b><? echo number_format($btq_siltting,2); ?> </b>&nbsp;&nbsp;&nbsp;<b> Prod. Sub Total:<? echo number_format($tot_prod_qty_siltting,2); ?> </b></td>
                        
                        </tr>                                
                            <?
                            unset($btq_siltting);unset($tot_prod_qty_siltting);
                        }
                        ?>  
                    
                    
                        <tr bgcolor="#EFEFEF">
                            <td colspan="24" align="left" ><b><? echo $group_name; ?> : <? echo $group_dtls_value; ?></b></td>
                        </tr>
                        <?
                        $group_by_arr[]=$group_value;            
                        $k++;
                        
                    }
                    
            }
            ?>
            <tr bgcolor="<? echo $bgcolor;?>" onClick="change_color('tr<? echo $i;?>','<? echo $bgcolor;?>')" id="tr<? echo $i;?>">
               
                <? if (!in_array($slitting_row[csf('batch_no')],$batch_chk_arr) )
                        { $f++;
                        ?>
                <td width="30"><? echo $f; ?></td>
                 <? if($group_by==2 || $group_by==0){ ?>
                <td  align="center" width="80" title="<? echo $machine_arr[$slitting_row[csf('machine_id')]]; ?>"><p><? echo $machine_arr[$slitting_row[csf('machine_id')]]; ?></p></td>
                <?
                 }
                 if($group_by==1 || $group_by==0){ ?>
                <td width="80"><p><? echo $floor_arr[$slitting_row[csf('floor_id')]]; ?></p></td>
                <? }
                ?>
                <td  align="center" width="50" ><p><? echo $shift_name[$slitting_row[csf('shift_name')]]; ?></p></td>
                <td  width="100" title="<? echo $buyer_arr[$slitting_row[csf('buyer_name')]]; ?>"><p><? echo $buyer_arr[$slitting_row[csf('buyer_name')]]; ?></p></td>
                <td  width="80"><p><? echo $slitting_row[csf('job_no_prefix_num')]; ?></p></td>
                <td  width="100"><p><? echo $slitting_row[csf('booking_no')]; ?></p></td>
                <td  width="60"><p><? echo $slitting_row[csf('file_no')]; ?></p></td>
                <td  width="70"><p><? echo $slitting_row[csf('grouping')]; ?></p></td>
                <td  width="90" title="<? echo $po_number; ?>"><p><? echo $po_number; ?></p></td>
                <?
                        $batch_chk_arr[]=$slitting_row[csf('batch_no')];
                        }
                        else
                        { ?>
                <td width="30"><? //echo $f; ?></td>
                  <? if($group_by==2 || $group_by==0){ ?>
                <td  align="center" width="80" ><p><? //echo $machine_arr[$slitting_row[csf('machine_id')]]; ?></p></td>
                <?
                 }
                 if($group_by==1 || $group_by==0){ ?>
                <td width="80"><p><? //echo $floor_arr[$slitting_row[csf('floor_id')]]; ?></p></td>
                <? }
                ?>
                <td  align="center" width="50" ><p><? //echo $shift_name[$slitting_row[csf('shift_name')]]; ?></p></td>
                <td  width="100"><p><? //echo $buyer_arr[$slitting_row[csf('buyer_name')]]; ?></p></td>
                <td  width="80"><p><? //echo $slitting_row[csf('job_no_prefix_num')]; ?></p></td>
                <td  width="100"><p><? //echo $heat_row[csf('booking_no')]; ?></p></td>
                <td  width="60"><p><? //echo $slitting_row[csf('file_no')]; ?></p></td>
                <td  width="70"><p><? //echo $slitting_row[csf('grouping')]; ?></p></td>
                <td  width="90"><p><? //echo $po_number; ?></p></td>
                          
                        <? }
                ?>
                <td  width="100" title="<? echo $desc[0]; ?>"><p><? echo $desc[0]; ?></p></td>
                <td  width="70" title="<? echo   $desc[2]; ?>"><p><? echo  $desc[2]; ?></p></td>
                <td  width="75" title="<? ?>"><p><? echo $fabric_typee[$slitting_row[csf('width_dia_type')]];?></p></td>
                <td  width="80" title="<? echo $color_library[$slitting_row[csf('color_id')]]; ?>"><p><? echo $color_library[$slitting_row[csf('color_id')]]; ?></p></td>
                <td  align="center" width="90" title="<? echo $slitting_row[csf('batch_no')]; ?>"><p><? echo $slitting_row[csf('batch_no')]; ?></p></td>
                <td  align="center" width="40" title="<? echo $slitting_row[csf('extention_no')]; ?>"><p><? echo $slitting_row[csf('extention_no')]; ?></p></td>
                <td align="right" width="70" title="<? echo $slitting_row[csf('batch_qnty')];  ?>"><? echo number_format($slitting_row[csf('batch_qnty')],2);  ?></td>
                 <td align="right" width="70" >

                    <? 
                    if(($page_upto==7 || $page_upto>7) && $roll_maintained==1)
                    {
                        echo number_format($batch_prod_qty_arr[$slitting_row[csf('id')]][$slitting_row[csf('prod_id')]][$slitting_row[csf('width_dia_type')]][$slitting_row[csf('roll_id')]][$slitting_row[csf('barcode_no')]][$insert_date]['sliting'],2);  
                    }
                    else
                    {
                        echo number_format($batch_prod_qty_arr[$slitting_row[csf('id')]][$slitting_row[csf('prod_id')]][$slitting_row[csf('width_dia_type')]][$insert_date]['sliting'],2);  
                    }
                    ?>
                        
                </td>
                <td align="left" width="50" title="<? echo $yarn_lot_arr[$slitting_row[csf('prod_id')]][$slitting_row[csf('po_id')]]; ?>"><p><? echo $yarn_lot_arr[$slitting_row[csf('prod_id')]][$slitting_row[csf('po_id')]];  ?></p></td>
                <td width="75" title="Process Start Date"><div style="width:75px; word-wrap:break-word;"><?  echo change_date_format($slitting_row[csf('process_start_date')]).', '.$slitting_row[csf('start_hours')].':'.$slitting_row[csf('start_minutes')]; ?></div></td>
                <td width="75" title="Process End Date"><div style="width:75px; word-wrap:break-word;"><?  echo change_date_format($slitting_row[csf('end_date')]).', '.$slitting_row[csf('end_hours')].':'.$slitting_row[csf('end_minutes')]; ?></div></td>
                 <td width="70" align="center"><div style="width:70px; word-wrap:break-word;"><?
                        $end_time=$slitting_row[csf('end_hours')].':'.$slitting_row[csf('end_minutes')];
                        $start_time=$slitting_row[csf('start_hours')].':'.$slitting_row[csf('start_minutes')];
                        
                        $new_date_time_start=($slitting_row[csf('process_start_date')].' '.$start_time.':'.'00');
                        $new_date_time_end=($slitting_row[csf('end_date')].' '.$end_time.':'.'00');
                        $total_time=datediff(n,$new_date_time_start,$new_date_time_end);
                        echo floor($total_time/60).":".$total_time%60; ?></div></td>
                <td align="center" width="60"><p><?   echo $slitting_row[csf('remarks')];?> </p>
                 </td>
                <td align="center" title="<?   if($slitting_row[csf('batch_against')]==2) echo $batch_against[$slitting_row[csf('batch_against')]]; ?>"><p><?  if($slitting_row[csf('batch_against')]==2) echo $batch_against[$slitting_row[csf('batch_against')]]; ?></p> </td>
            </tr>
            <?
            $i++;
            $btq_siltting+=$slitting_row[csf('batch_qnty')];
            $tot_prod_qty_siltting+=$batch_prod_qty_arr[$slitting_row[csf('id')]][$slitting_row[csf('prod_id')]][$slitting_row[csf('width_dia_type')]][$insert_date]['sliting'];
             }
             if($group_by!=0)
            {
                ?>
                    <tr  bgcolor="#D4D4D4" >
                       <td align="left" colspan="24"><Strong> Sub Total:</Strong> <b><? echo number_format($btq_siltting,2); ?> </b>
                       &nbsp;&nbsp;&nbsp;<b> Prod. Sub Total:<? echo number_format($tot_prod_qty_siltting,2); ?> </b>
                       </td>
                    </tr>                                
                            <?
            }
            ?>
                  <tr bgcolor="#C2DCFF">
                  <td colspan="24" align="center"><strong>Drying</strong></td>
                 </tr>
                  <?
                  $f=0;$tot_prod_qty_drying=0;
                 $drying_data=sql_select($sql_drying);$batch_chk_arr=array();$group_by_arr=array();
                 foreach($drying_data as $drying_row)
                 {
                 if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
                $order_id=$drying_row[csf('po_id')];
                $color_id=$drying_row[csf('color_id')];
                $desc=explode(",",$drying_row[csf('item_description')]); 
                $insert_date = "'".$drying_row[csf('insert_date')]."'";
                $po_number=implode(",",array_unique(explode(",",$drying_row[csf('po_number')]))); 
                $batch_prod_qty=$batch_prod_qty_arr3[$drying_row[csf('id')]][$drying_row[csf('prod_id')]][$drying_row[csf('width_dia_type')]][$drying_row[csf('end_date')]][$insert_date]['drying'];
                
            if($batch_prod_qty>0)
            {
            if($group_by!=0)
            {
                if($group_by==1)
                {
                    $group_value=$slitting_row[csf('floor_id')];
                    $group_name="Floor";
                    $group_dtls_value=$floor_arr[$slitting_row[csf('floor_id')]];
                }
                
                else if($group_by==2)
                {
                    $group_value=$slitting_row[csf('machine_id')];
                    $group_name="Machine";
                    $group_dtls_value=$machine_arr[$slitting_row[csf('machine_id')]];
                }
                if (!in_array($group_value,$group_by_arr) )
                    {
                        if($k!=1)
                        { ?>
                        
                        <tr  bgcolor="#D4D4D4" >
                       <td align="left" colspan="24"><Strong> Sub Total:</Strong> <b><? echo number_format($btq_drying,2); ?> </b>
                       &nbsp;&nbsp;&nbsp;<b> Prod. Sub Total:<? echo number_format($tot_prod_qty_drying,2); ?> </b>
                       </td>
                        
                        </tr>                                
                            <?
                            unset($btq_drying);unset($tot_prod_qty_drying);
                        }
                        ?>  
                    
                    
                        <tr bgcolor="#EFEFEF">
                            <td colspan="24" align="left" ><b><? echo $group_name; ?> : <? echo $group_dtls_value; ?></b></td>
                        </tr>
                        <?
                        $group_by_arr[]=$group_value;            
                        $k++;
                    }
            }
            ?>
            <tr bgcolor="<? echo $bgcolor;?>" onClick="change_color('tr<? echo $i;?>','<? echo $bgcolor;?>')" id="tr<? echo $i;?>">
                  <? if (!in_array($drying_row[csf('batch_no')],$batch_chk_arr) )
                        { $f++;
                            ?>
                <td width="30"><? echo $f; ?></td>
               
                 
                  <? if($group_by==2 || $group_by==0){ ?>
                 <td  align="center" width="80" title="<? echo $machine_arr[$drying_row[csf('machine_id')]]; ?>"><p><? echo $machine_arr[$drying_row[csf('machine_id')]]; ?></p></td>
                <?
                 }
                 if($group_by==1 || $group_by==0){ ?>
               <td width="80"><p><? echo $floor_arr[$drying_row[csf('floor_id')]]; ?></p></td>
                <? }
                ?>
                
                 <td  align="center" width="50" ><p><? echo $shift_name[$drying_row[csf('shift_name')]]; ?></p></td>
                <td  width="100" title="<? echo $buyer_arr[$drying_row[csf('buyer_name')]]; ?>"><p><? echo $buyer_arr[$drying_row[csf('buyer_name')]]; ?></p></td>
                <td  width="80" title="<? echo  $drying_row[csf('job_no_prefix_num')]; ?>"><p><? echo $drying_row[csf('job_no_prefix_num')]; ?></p></td>
                <td  width="100"><p><? echo $drying_row[csf('booking_no')]; ?></p></td>
                <td  width="60"><p><? echo $drying_row[csf('file_no')]; ?></p></td>
                <td  width="70"><p><? echo $drying_row[csf('grouping')]; ?></p></td>
                <td  width="90" title="<? echo $po_number; ?>"><p><? echo $po_number; ?></p></td>
                <?      $batch_chk_arr[]=$drying_row[csf('batch_no')];
                        }
                        else
                        { ?>
                <td width="30"><? //echo $f; ?></td>
                  <? if($group_by==2 || $group_by==0){ ?>
                 <td  align="center" width="80"><p><? //echo $machine_arr[$drying_row[csf('machine_id')]]; ?></p></td>
                <?
                 }
                 if($group_by==1 || $group_by==0){ ?>
               <td width="80"><p><? // echo $floor_arr[$drying_row[csf('floor_id')]]; ?></p></td>
                <? }
                ?>
                <td  align="center" width="50" ><p><? //echo $shift_name[$drying_row[csf('shift_name')]]; ?></p></td>
                <td  width="100"><p><? //echo $buyer_arr[$drying_row[csf('buyer_name')]]; ?></p></td>
                <td  width="80"><p><? //echo $drying_row[csf('job_no_prefix_num')]; ?></p></td>
                <td  width="100"><p><? //echo $heat_row[csf('booking_no')]; ?></p></td>
                <td  width="60"><p><? //echo $drying_row[csf('file_no')]; ?></p></td>
                <td  width="70"><p><? //echo $drying_row[csf('grouping')]; ?></p></td>
                <td  width="90"><p><? //echo $po_number; ?></p></td>    
                            
                        <? }
                ?>
                <td  width="100" title="<? echo $desc[0]; ?>"><p><? echo $drying_row[csf('item_description')]; ?></p></td>
                <td  width="70" title="<? echo   $desc[2]; ?>"><p><? echo  $desc[2]; ?></p></td>
                <td  width="75" title="<? ?>"><p><? echo $fabric_typee[$drying_row[csf('width_dia_type')]];?></p></td>
                <td  width="80" title="<? echo $color_library[$drying_row[csf('color_id')]]; ?>"><p><? echo $color_library[$drying_row[csf('color_id')]]; ?></p></td>
                <td  align="center" width="90" title="<? echo $drying_row[csf('batch_no')]; ?>"><p><? echo $drying_row[csf('batch_no')]; ?></p></td>
                <td  align="center" width="40" title="<? echo $drying_row[csf('extention_no')]; ?>"><p><? echo $drying_row[csf('extention_no')]; ?></p></td>
                <td align="right" width="70" title="<? echo $drying_row[csf('batch_qnty')];  ?>"><? echo number_format($drying_row[csf('batch_qnty')],2);  ?></td>
                 <td align="right" width="70" ><? echo number_format($batch_prod_qty_arr3[$drying_row[csf('id')]][$drying_row[csf('prod_id')]][$drying_row[csf('width_dia_type')]][$drying_row[csf('end_date')]][$insert_date]['drying'],2);  ?></td>
                <td align="left" width="50" title="<? echo $yarn_lot_arr[$drying_row[csf('prod_id')]][$drying_row[csf('po_id')]]; ?>"><p><? echo $yarn_lot_arr[$drying_row[csf('prod_id')]][$drying_row[csf('po_id')]];  ?></p></td>
                <td width="75" title="Process Start Date"><div style="width:75px; word-wrap:break-word;"><?  echo change_date_format($drying_row[csf('process_start_date')]).', '.$drying_row[csf('start_hours')].':'.$drying_row[csf('start_minutes')]; ?></div></td>
                <td width="75" title="Process End Date"><div style="width:75px; word-wrap:break-word;"><?  echo change_date_format($drying_row[csf('end_date')]).', '.$drying_row[csf('end_hours')].':'.$drying_row[csf('end_minutes')]; ?></div></td>
                 <td width="70" align="center"><div style="width:70px; word-wrap:break-word;"><?
                        $end_time=$drying_row[csf('end_hours')].':'.$drying_row[csf('end_minutes')];
                        $start_time=$drying_row[csf('start_hours')].':'.$drying_row[csf('start_minutes')];
                        
                        $new_date_time_start=($drying_row[csf('process_start_date')].' '.$start_time.':'.'00');
                        $new_date_time_end=($drying_row[csf('end_date')].' '.$end_time.':'.'00');
                        $total_time=datediff(n,$new_date_time_start,$new_date_time_end);
                        echo floor($total_time/60).":".$total_time%60; ?></div></td>
                <td align="center" width="60"><p><?  echo $drying_row[csf('remarks')]; ?> </p>
                 </td>
                <td align="center" title="<?   if($drying_row[csf('batch_against')]==2) echo $batch_against[$drying_row[csf('batch_against')]]; ?>"><p><?  if($drying_row[csf('batch_against')]==2) echo $batch_against[$drying_row[csf('batch_against')]]; ?></p> </td>
            </tr>
            <?
            $i++;
            $btq_drying+=$drying_row[csf('batch_qnty')];
            $tot_prod_qty_drying+=$batch_prod_qty_arr3[$drying_row[csf('id')]][$drying_row[csf('prod_id')]][$drying_row[csf('width_dia_type')]][$drying_row[csf('end_date')]][$insert_date]['drying'];

             }
             if($group_by!=0)
            {
                ?>
                    <tr  bgcolor="#D4D4D4" >
                       <td align="left" colspan="24"><Strong> Sub Total:</Strong> <b><? echo number_format($btq_drying,2); ?> </b>
                        &nbsp;&nbsp;&nbsp;<b> Prod. Sub Total:<? echo number_format($tot_prod_qty_drying,2); ?> </b>
                       </td>
                       
                    </tr>                                
                            <?
            }
            } //Zeror Qty End
            ?>
                
                  <tr bgcolor="#C2DCFF">
                      <td colspan="24" align="center"><strong>Stentering</strong></td>
                 </tr>
                   <?
                   $f=0;$tot_prod_qty_stenter=0;
                 $stentering_data=sql_select($sql_stentering); $batch_chk_arr=array();
                 foreach($stentering_data as $sten_row)
                 {
                 if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
                $order_id=$sten_row[csf('po_id')];
                $color_id=$sten_row[csf('color_id')];
                $desc=explode(",",$sten_row[csf('item_description')]); 
                $insert_date = "'".$sten_row[csf('insert_date')]."'";
                $po_number=implode(",",array_unique(explode(",",$sten_row[csf('po_number')])));
            if($group_by!=0)
            {
                if($group_by==1)
                {
                    $group_value=$sten_row[csf('floor_id')];
                    $group_name="Floor";
                    $group_dtls_value=$floor_arr[$sten_row[csf('floor_id')]];
                }
                
                else if($group_by==2)
                {
                    $group_value=$sten_row[csf('machine_id')];
                    $group_name="Machine";
                    $group_dtls_value=$machine_arr[$sten_row[csf('machine_id')]];
                }
                if (!in_array($group_value,$group_by_arr) )
                    {
                        if($k!=1)
                        { ?>
                        
                        <tr  bgcolor="#D4D4D4" >
                       <td align="left" colspan="23"><Strong> Sub Total:</Strong> <b><? echo number_format($tot_btq_stenter,2); ?> </b>
                        &nbsp;&nbsp;&nbsp;<b> Prod. Sub Total:<? echo number_format($tot_prod_qty_stenter,2); ?> </b>
                       </td>
                        </tr>                                
                            <?
                            unset($tot_btq_stenter);unset($tot_prod_qty_stenter);
                        }
                        ?>  
                    
                    
                        <tr bgcolor="#EFEFEF">
                            <td colspan="24" align="left" ><b><? echo $group_name; ?> : <? echo $group_dtls_value; ?></b></td>
                        </tr>
                        <?
                        $group_by_arr[]=$group_value;            
                        $k++;
                    }
            }
                 $stenter_grouping_arr_val=$sten_row[csf('batch_no')].$sten_row[csf('machine_id')].$sten_row[csf('floor_id')]; 
                 ?>
            <tr bgcolor="<? echo $bgcolor;?>" onClick="change_color('tr<? echo $i;?>','<? echo $bgcolor;?>')" id="tr<? echo $i;?>">
                 <? if (!in_array($stenter_grouping_arr_val,$batch_chk_arr) )
                        { $f++;
                            ?>
               
                <td width="30"><? echo $f; ?></td>
                <? if($group_by==2 || $group_by==0){ ?>
                  <td  align="center" width="80" title="<? echo $machine_arr[$sten_row[csf('machine_id')]]; ?>"><p><? echo $machine_arr[$sten_row[csf('machine_id')]]; ?></p></td>
                <?
                 }
                 if($group_by==1 || $group_by==0){ ?>
                 <td width="80"><p><? echo $floor_arr[$sten_row[csf('floor_id')]]; ?></p></td>
                <? }
                ?>
                <td  align="center" width="50" ><p><? echo $shift_name[$sten_row[csf('shift_name')]]; ?></p></td>
                <td  width="100" title="<? echo $buyer_arr[$sten_row[csf('buyer_name')]]; ?>"><p><? echo $buyer_arr[$sten_row[csf('buyer_name')]]; ?></p></td>
                <td  width="80" title="<? echo  $sten_row[csf('job_no_prefix_num')]; ?>"><p><? echo $sten_row[csf('job_no_prefix_num')]; ?></p></td>
                <td  width="100"><p><? echo $sten_row[csf('booking_no')]; ?></p></td>
                <td  width="60"><p><? echo $sten_row[csf('file_no')]; ?></p></td>
                <td  width="70"><p><? echo $sten_row[csf('grouping')]; ?></p></td>
                <td  width="90"><div style="width:90px; word-wrap:break-word;"><? echo $po_number; ?></div></td>
                <?
                        $batch_chk_arr[]=$stenter_grouping_arr_val;
                        }
                        else
                        { ?>
                <td width="30"><? //echo $i; ?></td>
                <? if($group_by==2 || $group_by==0){ ?>
                  <td  align="center" width="80"><p><? //echo $machine_arr[$sten_row[csf('machine_id')]]; ?></p></td>
                <?
                 }
                 if($group_by==1 || $group_by==0){ ?>
                 <td width="80"><p><? //echo $floor_arr[$sten_row[csf('floor_id')]]; ?></p></td>
                <? }
                ?>
                <td  align="center" width="50" ><p><? // echo $shift_name[$drying_row[csf('shift_name')]]; ?></p></td>
                <td  width="100"><p><? //echo $buyer_arr[$sten_row[csf('buyer_name')]]; ?></p></td>
                <td  width="80"><p><? //echo $sten_row[csf('job_no_prefix_num')]; ?></p></td>
                <td  width="100"><p><? //echo $heat_row[csf('booking_no')]; ?></p></td>
                <td  width="60"><p><? // echo $sten_row[csf('file_no')]; ?></p></td>
                <td  width="70"><p><? //echo $sten_row[csf('grouping')]; ?></p></td>
                <td  width="90"><div style="width:90px; word-wrap:break-word;"><? //echo $po_number; ?></div></td>  
                    <?  }
                ?>
                <td  width="100" title="<? echo $desc[0]; ?>"><p><? echo $sten_row[csf('item_description')]; ?></p></td>
                <td  width="70" title="<? echo   $desc[2]; ?>"><p><? echo  $desc[2]; ?></p></td>
                <td  width="75" title="<? ?>"><p><? echo $fabric_typee[$sten_row[csf('width_dia_type')]];?></p></td>
                <td  width="80" title="<? echo $color_library[$sten_row[csf('color_id')]]; ?>"><p><? echo $color_library[$sten_row[csf('color_id')]]; ?></p></td>
                <td  align="center" width="90" title="<? echo $sten_row[csf('batch_no')]; ?>"><p><? echo $sten_row[csf('batch_no')]; ?></p></td>
                <td  align="center" width="40" title="<? echo $sten_row[csf('extention_no')]; ?>"><p><? echo $sten_row[csf('extention_no')]; ?></p></td>
                <td align="right" width="70" title="<? echo $sten_row[csf('batch_qnty')];  ?>"><? echo number_format($sten_row[csf('batch_qnty')],2);  ?></td>
                <td align="right" width="70" ><? echo number_format($batch_prod_qty_arr[$sten_row[csf('id')]][$sten_row[csf('prod_id')]][$sten_row[csf('width_dia_type')]][$sten_row[csf('roll_id')]][$sten_row[csf('barcode_no')]][$insert_date]['stenter'],2);  ?></td>
                <td align="left" width="50" title="<? echo $yarn_lot_arr[$sten_row[csf('prod_id')]][$sten_row[csf('po_id')]]; ?>"><p><? echo $yarn_lot_arr[$sten_row[csf('prod_id')]][$sten_row[csf('po_id')]];  ?></p></td>
                <td width="75" title="Process Start Date"><div style="width:75px; word-wrap:break-word;"><?  echo change_date_format($sten_row[csf('process_start_date')]).', '.$sten_row[csf('start_hours')].':'.$sten_row[csf('start_minutes')]; ?></div></td>
                <td width="75" title="Process End Date"><div style="width:75px; word-wrap:break-word;"><?  echo change_date_format($sten_row[csf('end_date')]).', '.$sten_row[csf('end_hours')].':'.$sten_row[csf('end_minutes')]; ?></div></td>
                 <td width="70" align="center"><div style="width:70px; word-wrap:break-word;"><?
                        $end_time=$sten_row[csf('end_hours')].':'.$sten_row[csf('end_minutes')];
                        $start_time=$sten_row[csf('start_hours')].':'.$sten_row[csf('start_minutes')];
                        
                        $new_date_time_start=($sten_row[csf('process_start_date')].' '.$start_time.':'.'00');
                        $new_date_time_end=($sten_row[csf('end_date')].' '.$end_time.':'.'00');
                        $total_time=datediff(n,$new_date_time_start,$new_date_time_end);
                        echo floor($total_time/60).":".$total_time%60; ?></div></td>
                <td align="center" width="60"><p><?  echo $sten_row[csf('remarks')]; ?> </p>
                 </td>
                <td align="center" title="<?   if($sten_row[csf('batch_against')]==2) echo $batch_against[$sten_row[csf('batch_against')]]; ?>"><p><?  if($sten_row[csf('batch_against')]==2) echo $batch_against[$sten_row[csf('batch_against')]]; ?></p> </td>
            </tr>
            <?
            $i++;
            $tot_btq_stenter+=$sten_row[csf('batch_qnty')];
            $tot_prod_qty_stenter+=$batch_prod_qty_arr[$sten_row[csf('id')]][$sten_row[csf('prod_id')]][$sten_row[csf('width_dia_type')]][$sten_row[csf('roll_id')]][$sten_row[csf('barcode_no')]][$insert_date]['stenter'];

             }
             if($group_by!=0)
            {
                ?>
                    <tr  bgcolor="#D4D4D4" >
                       <td align="left" colspan="24"><Strong> Batch Sub Total:</Strong> <b><? echo number_format($tot_btq_stenter,2); ?> </b>
                         &nbsp;&nbsp;&nbsp;<b> Prod. Sub Total:<? echo number_format($tot_prod_qty_stenter,2); ?> </b>
                       </td> 
                       
                    </tr>                                
                            <?
            }
            ?>
                
                  <tr bgcolor="#C2DCFF">
                      <td colspan="24" align="center"><strong>Compacting</strong></td>
                 </tr>
                  <?
                  $f=0; $z=0; $tot_prod_compact_qty=0;
                // echo $sql_compacting;
                 $compacting_data=sql_select($sql_compacting);$batch_chk_arr=array();$prod_batch_chk_arr=array();
                 foreach($compacting_data as $comp_row)
                 {
                if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
                $order_id=$comp_row[csf('po_id')];
                $color_id=$comp_row[csf('color_id')];
                $desc=explode(",",$comp_row[csf('item_description')]); 
                $insert_date = "'".$comp_row[csf('insert_date')]."'";
                $po_number=implode(",",array_unique(explode(",",$comp_row[csf('po_number')])));
                
                $com_group_arr=$comp_row[csf('prod_id')].$comp_row[csf('batch_no')].$comp_row[csf('machine_id')].$comp_row[csf('floor_id')].$comp_row[csf('shift_name')].$comp_row[csf('end_date')];
                // $prod_compact_qty=$batch_prod_qty_arr3[$comp_row[csf('id')]][$comp_row[csf('prod_id')]][$comp_row[csf('width_dia_type')]][$comp_row[csf('end_date')]][$insert_date]['compact'];
                if ($roll_maintained==1 && ($page_upto==7 || $page_upto>7) )
                { 
                   
                     $prod_compact_qty=$batch_prod_qty_arr[$comp_row[csf('id')]][$comp_row[csf('prod_id')]][$comp_row[csf('width_dia_type')]][$comp_row[csf('roll_id')]][$comp_row[csf('barcode_no')]][$insert_date]['compact'];
                    // echo number_format($prod_compact_qty,2).'<br>'; 
                       
                }
                else
                {
                    
                    $prod_compact_qty=$comp_row_prod_qty_arr3[$comp_row[csf('id')]][$comp_row[csf('prod_id')]][$comp_row[csf('width_dia_type')]][$comp_row[csf('end_date')]][$insert_date]['compact'];
                    //echo $prod_compact_qty.'='.$comp_row[csf('end_date')].',';
                    //echo $prod_compact_qty.'DD'; process_end_date
                    //$comp_row_prod_qty_arr3[$row[csf('comp_row_id')]][$row[csf('prod_id')]][$row[csf('dia_type')]][$row[csf('production_date')]]['compact']
                    $comp_row_compact_qty=$comp_row_prod_qty_arr3[$comp_row[csf('id')]][$comp_row[csf('prod_id')]][$comp_row[csf('width_dia_type')]][$comp_row[csf('end_date')]][$insert_date]['batch_compact_qty'];
                }
                //$com_group=$comp_row[csf('batch_no')].$batch[csf('end_date')];
                if (!in_array($com_group_arr,$prod_batch_chk_arr))
                        { $z++;
                            
                            
                             $prod_batch_chk_arr[]=$com_group_arr;
                              $tot_prod_compact_qty=$prod_compact_qty;
                        }
                        else
                        {
                             $tot_prod_compact_qty=0;
                        }
                        
            if($group_by!=0)
            {
                if($group_by==1)
                {
                    $group_value=$comp_row[csf('floor_id')];
                    $group_name="Floor";
                    $group_dtls_value=$floor_arr[$comp_row[csf('floor_id')]];
                }
                
                else if($group_by==2)
                {
                    $group_value=$comp_row[csf('machine_id')];
                    $group_name="Machine";
                    $group_dtls_value=$machine_arr[$comp_row[csf('machine_id')]];
                }
                if (!in_array($group_value,$group_by_arr) )
                    {
                        if($k!=1)
                        { ?>
                        
                        <tr  bgcolor="#D4D4D4" >
                       <td align="left" colspan="24"><Strong> Sub Total:</Strong> <b><? echo number_format($btq_com,2); ?> </b>
                        &nbsp;&nbsp;&nbsp;<b> Prod. Sub Total:<? echo number_format($tot_qty_com,2); ?> </b>
                       </td>
                        
                        </tr>                                
                            <?
                            unset($btq_com);unset($tot_qty_com);
                        }
                        ?>  
                    
                    
                        <tr bgcolor="#EFEFEF">
                            <td colspan="24" align="left" ><b><? echo $group_name; ?> : <? echo $group_dtls_value; ?></b></td>
                        </tr>
                        <?
                        $group_by_arr[]=$group_value;            
                        $k++;
                    }
            }
            $com_grouping_arr_val=$comp_row[csf('batch_no')].$comp_row[csf('machine_id')].$comp_row[csf('floor_id')].$comp_row[csf('shift_name')];
            ?>
                <tr bgcolor="<? echo $bgcolor;?>" onClick="change_color('tr<? echo $i;?>','<? echo $bgcolor;?>')" id="tr<? echo $i;?>">
                 <? if (!in_array($com_grouping_arr_val,$batch_chk_arr) )
                        { $f++;
                            ?>
                <td width="30"><? echo $f; ?></td>
                
                
                 
                  <? if($group_by==2 || $group_by==0){ ?>
                 <td  align="center" width="80" title="<? echo $machine_arr[$comp_row[csf('machine_id')]]; ?>"><p><? echo $machine_arr[$comp_row[csf('machine_id')]]; ?></p></td>
                <?
                 }
                 if($group_by==1 || $group_by==0){ ?>
                 <td width="80"><p><? echo $floor_arr[$comp_row[csf('floor_id')]]; ?></p></td>
                <? }
                ?>
                
                <td  align="center" width="50" ><p><? echo $shift_name[$comp_row[csf('shift_name')]]; ?></p></td>
                <td  width="100" title="<? echo $buyer_arr[$comp_row[csf('buyer_name')]]; ?>"><p><? echo $buyer_arr[$comp_row[csf('buyer_name')]]; ?></p></td>
                <td  width="80" title="<? echo  $comp_row[csf('job_no_prefix_num')]; ?>"><p><? echo $comp_row[csf('job_no_prefix_num')]; ?></p></td>
                <td  width="100"><p><? echo $comp_row[csf('booking_no')]; ?></p></td>
                <td  width="60"><p><? echo $comp_row[csf('file_no')]; ?></p></td>
                <td  width="70"><p><? echo $comp_row[csf('grouping')]; ?></p></td>
                <td  width="90"><p><? echo $po_number; ?></p></td>
                <?      $batch_chk_arr[]=$com_grouping_arr_val;
                        }
                        else
                        { ?>
                <td width="30"><? //echo $i; ?></td>
                 <? if($group_by==2 || $group_by==0){ ?>
                 <td  align="center" width="80"><p><? //echo $machine_arr[$comp_row[csf('machine_id')]]; ?></p></td>
                <?
                 }
                 if($group_by==1 || $group_by==0){ ?>
                 <td width="80"><p><? //echo $floor_arr[$sten_row[csf('floor_id')]]; ?></p></td>
                <? }
                ?>
                <td  align="center" width="50" ><p><? //echo $shift_name[$drying_row[csf('shift_name')]]; ?></p></td>
                <td  width="100"><p><? //echo $buyer_arr[$comp_row[csf('buyer_name')]]; ?></p></td>
                <td  width="80"><p><? //echo $comp_row[csf('job_no_prefix_num')]; ?></p></td>
                <td  width="100"><p><? //echo $heat_row[csf('booking_no')]; ?></p></td>
                <td  width="60"><p><? //echo $comp_row[csf('file_no')]; ?></p></td>
                <td  width="70"><p><? //echo $comp_row[csf('grouping')]; ?></p></td>
                <td  width="90"><p><? //echo $po_number; ?></p></td>    
                    <?  }
                ?>
                <td  width="100" title="<? echo $desc[0]; ?>"><p><? echo $comp_row[csf('item_description')]; ?></p></td>
                <td  width="70" title="<? echo   $desc[2]; ?>"><p><? echo  $desc[2]; ?></p></td>
                <td  width="75" title="<? ?>"><p><? echo $fabric_typee[$comp_row[csf('width_dia_type')]];?></p></td>
                <td  width="80" title="<? echo $color_library[$comp_row[csf('color_id')]]; ?>"><p><? echo $color_library[$comp_row[csf('color_id')]]; ?></p></td>
                <td  align="center" width="90" title="<? echo $comp_row[csf('batch_no')]; ?>"><p><? echo $comp_row[csf('batch_no')]; ?></p></td>
                <td  align="center" width="40" title="<? echo $comp_row[csf('extention_no')]; ?>"><p><? echo $comp_row[csf('extention_no')]; ?></p></td>
                <td align="right" width="70" title="<? echo $comp_row[csf('batch_qnty')];  ?>"><? echo number_format($comp_row[csf('batch_qnty')],2);  ?></td>
                <td align="right" width="70" ><? echo number_format($prod_compact_qty,2);  ?></td>
                <td align="left" width="50" title="<? echo $yarn_lot_arr[$comp_row[csf('prod_id')]][$comp_row[csf('po_id')]]; ?>"><p><? echo $yarn_lot_arr[$comp_row[csf('prod_id')]][$comp_row[csf('po_id')]];  ?></p></td>
               <td width="75" title="Process Start Date"><div style="width:75px; word-wrap:break-word;"><?  echo change_date_format($comp_row[csf('process_start_date')]).', '.$comp_row[csf('start_hours')].':'.$comp_row[csf('start_minutes')]; ?></div></td>
                <td width="75" title="Process End Date"><div style="width:75px; word-wrap:break-word;"><?  echo change_date_format($comp_row[csf('end_date')]).', '.$comp_row[csf('end_hours')].':'.$comp_row[csf('end_minutes')]; ?></div></td>
                 <td width="70" align="center"><div style="width:70px; word-wrap:break-word;"><?
                        $end_time=$comp_row[csf('end_hours')].':'.$comp_row[csf('end_minutes')];
                        $start_time=$comp_row[csf('start_hours')].':'.$comp_row[csf('start_minutes')];
                        
                        $new_date_time_start=($comp_row[csf('process_start_date')].' '.$start_time.':'.'00');
                        $new_date_time_end=($comp_row[csf('end_date')].' '.$end_time.':'.'00');
                        $total_time=datediff(n,$new_date_time_start,$new_date_time_end);
                        echo floor($total_time/60).":".$total_time%60; ?></div></td>
                <td align="center" width="60"><p>
               <?     
            echo $comp_row[csf('remarks')];
                    ?>
                    </p>
                 </td>
                <td align="center" title="<?   if($comp_row[csf('batch_against')]==2) echo $batch_against[$comp_row[csf('batch_against')]]; ?>"><p><?  if($comp_row[csf('batch_against')]==2) echo $batch_against[$comp_row[csf('batch_against')]]; ?></p> </td>
            </tr>
            <?
            $i++;
            $btq_com+=$comp_row[csf('batch_qnty')];
            $tot_qty_com+=$tot_prod_compact_qty;

             }
            if($group_by!=0)
            {
                ?>
                    <tr  bgcolor="#D4D4D4" >
                       <td align="left" colspan="24"><Strong> Sub Total:</Strong> <b><? echo number_format($btq_com,2); ?> </b>
                         &nbsp;&nbsp;&nbsp;<b> Prod. Sub Total:<? echo number_format($tot_qty_com,2); ?> </b>
                       </td>
                    
                    </tr>                                
                            <?
            }
            ?>
                
                  <tr bgcolor="#C2DCFF">
                       <td colspan="24" align="center"><strong>Special Finish </strong></td>
                 </tr>
                  <?
                  $f=0;$k=1;$tot_prod_qty_special=0;
                // echo $sql_special;
                 $special_data=sql_select($sql_special);$batch_chk_arr=array();$process_group_by_arr=array();
                 foreach($special_data as $special_row)
                 {
                if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
                $order_id=$special_row[csf('po_id')];
				$process_id=$special_row[csf('process_id')];
                $color_id=$special_row[csf('color_id')];
                $desc=explode(",",$special_row[csf('item_description')]);
                $insert_date = "'".$special_row[csf('insert_date')]."'"; 
                $po_number=implode(",",array_unique(explode(",",$special_row[csf('po_number')])));
				if($group_by!=0)
				{
					if($group_by==1)
					{
						$group_value=$special_row[csf('floor_id')];
						$group_name="Floor";
						$group_dtls_value=$floor_arr[$special_row[csf('floor_id')]];
					}
					
					else if($group_by==2)
					{
						$group_value=$special_row[csf('machine_id')];
						$group_name="Machine";
						$group_dtls_value=$machine_arr[$special_row[csf('machine_id')]];
					}
					if (!in_array($group_value,$group_by_arr) )
						{
							if($k!=1)
							{ ?>
							
							<tr  bgcolor="#D4D4D4" >
						   <td align="left" colspan="24"><Strong> Sub Total:</Strong> <b><? echo number_format($btq_special,2); ?> </b>
							&nbsp;&nbsp;&nbsp;<b> Prod. Sub Total:<? echo number_format($tot_prod_qty_special,2); ?> </b>
						   </td>
							
							</tr>                                
								<?
								unset($btq_special);unset($tot_prod_qty_special);
							}
							?>  
						
						
							<tr bgcolor="#EFEFEF">
								<td colspan="24" align="left" ><b><? echo $group_name; ?> : <? echo $group_dtls_value; ?></b></td>
							</tr>
							<?
							$group_by_arr[]=$group_value;            
							$k++;
						}
				}
			if($group_by==0)
            {
				 if (!in_array($process_id,$process_group_by_arr) )
                    {
                        if($k!=1)
                        { ?>
                        
                        <tr  bgcolor="#D4D4D4" >
                       <td align="left" colspan="24"><Strong> Sub Total:</Strong> <b><? echo number_format($btq_special,2); ?> </b>
                        &nbsp;&nbsp;&nbsp;<b> Prod. Sub Total:<? echo number_format($tot_prod_qty_special,2); ?> </b>
                       </td>
                        
                        </tr>                                
                            <?
                            unset($btq_special);unset($tot_prod_qty_special);
                        }
                        ?>  
                    
                    
                        <tr bgcolor="#EFEFEF">
                            <td colspan="24" align="left" ><b><? echo $conversion_cost_head_array[$process_id]; ?> : <? //echo $group_dtls_value; ?></b></td>
                        </tr>
                        <?
                        $process_group_by_arr[]=$process_id;            
                        $k++;
                    }
			}
					
            ?>
            <tr bgcolor="<? echo $bgcolor;?>" onClick="change_color('tr<? echo $i;?>','<? echo $bgcolor;?>')" id="tr<? echo $i;?>">
                 <? if (!in_array($special_row[csf('batch_no')],$batch_chk_arr) )
                        { $f++;
                            ?>
                <td width="30"><? echo $i; ?></td>
               
                
                   <? if($group_by==2 || $group_by==0){ ?>
                 <td  align="center" width="80" title="<? echo $machine_arr[$special_row[csf('machine_id')]]; ?>"><p><? echo $machine_arr[$special_row[csf('machine_id')]]; ?></p></td>
                <?
                 }
                 if($group_by==1 || $group_by==0){ ?>
                 <td width="80"><p><? echo $floor_arr[$special_row[csf('floor_id')]]; ?></p></td>
                <? }
                ?>
                 
                <td  align="center" width="50" ><p><? echo $shift_name[$special_row[csf('shift_name')]]; ?></p></td>
                <td  width="100" title="<? echo $buyer_arr[$special_row[csf('buyer_name')]]; ?>"><p><? echo $buyer_arr[$special_row[csf('buyer_name')]]; ?></p></td>
                <td  width="80" title="<? echo  $special_row[csf('job_no_prefix_num')]; ?>"><p><? echo $special_row[csf('job_no_prefix_num')]; ?></p></td>
                <td  width="100"><p><? echo $special_row[csf('booking_no')]; ?></p></td>
                <td  width="60"><p><? echo $special_row[csf('file_no')]; ?></p></td>
                <td  width="70"><p><? echo $special_row[csf('grouping')]; ?></p></td>
                <td  width="90"><div style="width:90px; word-wrap:break-word;"><? echo $po_number; ?></div></td>
                <?      $batch_chk_arr[]=$special_row[csf('batch_no')];
                        }
                        else
                        { ?>
                <td width="30"><? //echo $f; ?></td>
               
                   <? if($group_by==2 || $group_by==0){ ?>
                 <td  align="center" width="80"><p><? //echo $machine_arr[$special_row[csf('machine_id')]]; ?></p></td>
                <?
                 }
                 if($group_by==1 || $group_by==0){ ?>
                 <td width="80"><p><? //echo $floor_arr[$special_row[csf('floor_id')]]; ?></p></td>
                <? }
                ?>
                <td  align="center" width="50" ><p><? //echo $shift_name[$drying_row[csf('shift_name')]]; ?></p></td>
                <td  width="100"><p><? //echo $buyer_arr[$special_row[csf('buyer_name')]]; ?></p></td>
                <td  width="80"><p><? //echo $special_row[csf('job_no_prefix_num')]; ?></p></td>
                <td  width="100"><p><? //echo $heat_row[csf('booking_no')]; ?></p></td>
                <td  width="60"><p><? //echo $special_row[csf('file_no')]; ?></p></td>
                <td  width="70"><p><? //echo $special_row[csf('grouping')]; ?></p></td>
                <td  width="90"><div style="width:90px; word-wrap:break-word;"><? //echo $po_number; ?></div></td>  
                    <?  }
                ?>
                <td  width="100" title="<? echo $desc[0]; ?>"><p><? echo $special_row[csf('item_description')]; ?></p></td>
                <td  width="70" title="<? echo   $desc[2]; ?>"><p><? echo  $desc[2]; ?></p></td>
                <td  width="75" title="<? ?>"><p><? echo $fabric_typee[$special_row[csf('width_dia_type')]];?></p></td>
                <td  width="80" title="<? echo $color_library[$special_row[csf('color_id')]]; ?>"><p><? echo $color_library[$special_row[csf('color_id')]]; ?></p></td>
                <td  align="center" width="90" title="<? echo $special_row[csf('batch_no')]; ?>"><p><? echo $special_row[csf('batch_no')]; ?></p></td>
                <td  align="center" width="40" title="<? echo $special_row[csf('extention_no')]; ?>"><p><? echo $special_row[csf('extention_no')]; ?></p></td>
                <td align="right" width="70" title="<? echo $special_row[csf('batch_qnty')];  ?>"><? echo number_format($special_row[csf('batch_qnty')],2);  ?></td>
                <td align="right" width="70" >

                <? 
                // $batch_prod_qty_arr[$row[csf('batch_id')]][$row[csf('prod_id')]][$row[csf('dia_type')]][$row[csf('roll_id')]][$row[csf('barcode_no')]][$insert_date]['special']+=$row[csf('special_qty')];
                // echo $special_row[csf('id')]."=".$special_row[csf('prod_id')]."=".$special_row[csf('width_dia_type')]."=".$special_row[csf('roll_id')]."=".$special_row[csf('barcode_no')]."=".$insert_date."<br";
                if(($page_upto==6 || $page_upto>6) && $roll_maintained==1)
                {
                    echo number_format($batch_prod_qty_arr[$special_row[csf('id')]][$special_row[csf('prod_id')]][$special_row[csf('width_dia_type')]][$special_row[csf('roll_id')]][$special_row[csf('barcode_no')]][$insert_date]['special'],2); 
                }
                else
                {
                     echo number_format($batch_prod_qty_arr[$special_row[csf('id')]][$special_row[csf('prod_id')]][$special_row[csf('width_dia_type')]][$insert_date]['special'],2); 
                } 

                // echo number_format($batch_prod_qty_arr[$special_row[csf('id')]][$special_row[csf('prod_id')]][$special_row[csf('width_dia_type')]][$insert_date]['special'],2);  ?>
                    
                </td>
                <td align="left" width="50" title="<? echo $yarn_lot_arr[$special_row[csf('prod_id')]][$special_row[csf('po_id')]]; ?>"><p><? echo $yarn_lot_arr[$special_row[csf('prod_id')]][$special_row[csf('po_id')]];  ?></p></td>
                <td width="75" title="Process Start Date"><div style="width:75px; word-wrap:break-word;"><?  echo change_date_format($special_row[csf('process_start_date')]).', '.$special_row[csf('start_hours')].':'.$special_row[csf('start_minutes')]; ?></div></td>
                <td width="75" title="Process End Date"><div style="width:75px; word-wrap:break-word;"><?  echo change_date_format($special_row[csf('end_date')]).', '.$special_row[csf('end_hours')].':'.$special_row[csf('end_minutes')]; ?></div></td>
                 <td width="70" align="center"><div style="width:70px; word-wrap:break-word;"><?
                        $end_time=$special_row[csf('end_hours')].':'.$special_row[csf('end_minutes')];
                        $start_time=$special_row[csf('start_hours')].':'.$special_row[csf('start_minutes')];
                        
                        $new_date_time_start=($special_row[csf('process_start_date')].' '.$start_time.':'.'00');
                        $new_date_time_end=($special_row[csf('end_date')].' '.$end_time.':'.'00');
                        $total_time=datediff(n,$new_date_time_start,$new_date_time_end);
                        echo floor($total_time/60).":".$total_time%60; ?></div></td>
                <td align="center" width="60"><p>
               <?     
            echo $special_row[csf('remarks')];
                    ?>
                    </p>
                 </td>
                <td align="center" title="<?   if($special_row[csf('batch_against')]==2) echo $batch_against[$special_row[csf('batch_against')]]; ?>"><p><?  if($comp_row[csf('batch_against')]==2) echo $batch_against[$special_row[csf('batch_against')]]; ?></p> </td>
            </tr>
            <?
            $i++;
            $btq_special+=$special_row[csf('batch_qnty')];
            $tot_prod_qty_special+=$batch_prod_qty_arr[$special_row[csf('id')]][$special_row[csf('prod_id')]][$special_row[csf('width_dia_type')]][$insert_date]['special'];
             }
             //echo $tot_prod_qty_heat.'='.$tot_prod_qty_siltting.'='.$tot_prod_qty_drying.'='.$tot_prod_qty_stenter.'='.$tot_qty_com.'='.$tot_prod_qty_special;
             $grand_total=$btq_heat+$btq_siltting+$btq_drying+$btq_com+$btq_special+$tot_btq_stenter;
             $grand_prod_total=$tot_prod_qty_heat+$tot_prod_qty_siltting+$tot_prod_qty_drying+$tot_prod_qty_stenter+$tot_qty_com+$tot_prod_qty_special;

            if($group_by!=0)
            {
                ?>
                    <tr  bgcolor="#D4D4D4" >
                       <td align="left" colspan="24"><Strong> Sub Total:</Strong> <b><? echo number_format($btq_special,2); ?> </b>
                        &nbsp;&nbsp;&nbsp;<b> Prod. Sub Total:<? echo number_format($tot_prod_qty_special,2); ?> </b>
                       </td>
                    
                    </tr>                                
                            <?
            }
            ?>
                
                  <tr  bgcolor="#C2DCFF">
                       <td align="left"  colspan="24"><Strong> Grand Total:</Strong> <b>Batch Qty:<? echo number_format($grand_total,2).'&nbsp;&nbsp;Prod Qty:&nbsp;&nbsp;'.$grand_prod_total; ?> </b></td>
                      
                 </tr>  
                </tbody>
            </table>
            </div>
            </fieldset>
            </div>
                <? 
        }//All Search End

        foreach (glob("$user_name*.xls") as $filename) 
        {
            @unlink($filename);
        }
        $name=time();
        $filename=$user_name."_".$name.".xls";
        $create_new_doc = fopen($filename, 'w');
        $is_created = fwrite($create_new_doc,ob_get_contents());
        echo "$total_data****$filename";
        exit();
        //Fabric Finishing Report end
    } // First Show report end

      else if($report_type==2) //2nd Show report Start 
      {
        // echo $roll_maintained.'='.$cbo_type.'='.$page_upto.'<br>';
        //if ($roll_maintained==1 && $cbo_type!=0) 

        foreach($batch_data as $row)
        {
          if($row[csf('heat_qty')]>0){
            $batch_prod_qty_arr[$row[csf('batch_id')]][$row[csf('prod_id')]][$row[csf('dia_type')]]['heat']+=$row[csf('heat_qty')];
          }
           if($row[csf('sliting_qty')]>0){
            $batch_prod_qty_arr[$row[csf('batch_id')]][$row[csf('prod_id')]][$row[csf('dia_type')]]['sliting']+=$row[csf('sliting_qty')];
           }
              if($row[csf('drying_qty')]>0){
            $batch_prod_qty_arr[$row[csf('batch_id')]][$row[csf('prod_id')]][$row[csf('dia_type')]]['drying']+=$row[csf('drying_qty')];
              }
                if($row[csf('stenter_qty')]>0){
            $batch_prod_qty_arr[$row[csf('batch_id')]][$row[csf('prod_id')]][$row[csf('dia_type')]]['stenter']+=$row[csf('stenter_qty')];
                }
            if($row[csf('compact_qty')]>0){
            $batch_prod_qty_arr[$row[csf('batch_id')]][$row[csf('prod_id')]][$row[csf('dia_type')]]['compact']+=$row[csf('compact_qty')];
            $batch_prod_qty_arr3[$row[csf('batch_id')]][$row[csf('prod_id')]][$row[csf('dia_type')]][$row[csf('production_date')]]['compact']+=$row[csf('compact_qty')];
            }
            if($row[csf('batch_compact_qty')]>0){
            $batch_prod_qty_arr3[$row[csf('batch_id')]][$row[csf('prod_id')]][$row[csf('dia_type')]][$row[csf('production_date')]]['batch_compact_qty']+=$row[csf('batch_compact_qty')]; }
             if($row[csf('special_qty')]>0){
            $batch_prod_qty_arr[$row[csf('batch_id')]][$row[csf('prod_id')]][$row[csf('dia_type')]]['special']+=$row[csf('special_qty')];
             }
              if($row[csf('unload_qty')]>0){
            $batch_prod_qty_arr[$row[csf('batch_id')]][$row[csf('prod_id')]][$row[csf('dia_type')]]['unload']+=$row[csf('unload_qty')];
              }
              if($row[csf('re_stenter_qty')]>0){
            $batch_prod_qty_arr2[$row[csf('batch_id')]][$row[csf('prod_id')]][$row[csf('dia_type')]]['stenter']+=$row[csf('re_stenter_qty')];
              }
            if($row[csf('re_compact_qty')]>0){
            $batch_prod_qty_arr2[$row[csf('batch_id')]][$row[csf('prod_id')]][$row[csf('dia_type')]]['compact']+=$row[csf('re_compact_qty')];
            }
        }
        unset($batch_data);
        if($db_type==0) $group_conct="group_concat(distinct c.po_number ) AS po_number,group_concat(distinct b.po_id ) AS po_id";
        else if($db_type==2) $group_conct="listagg(cast(c.po_number as varchar2(4000)),',') within group (order by c.po_number) AS po_number,listagg(b.po_id ,',') within group (order by b.po_id) as po_id";
         //$group_conct="c.po_number,b.po_id";

        //echo $page_upto.'=='.$roll_maintained;
        // echo $cbo_type;
        if($cbo_type==1)//  For Heat Setting 
        {
                $heat_set=",count(b.roll_no) as roll_no";
                $heat_group=""; 
        }
        else if($cbo_type==6)//  For Dying... 
        {
                $dyeing_pro=",count(b.roll_no) as roll_no";
                $dyeing_group="";
        }
        else if($cbo_type==2) //Sliting...
        {
                $sliting_sq=",count(b.roll_no) as roll_no";
                $sliting_group="";  
        }
        else if($cbo_type==9) //Stentering...
        {
                $stenter=",count(b.roll_no) as roll_no";
                $stenter_group="";  
        }
        else if($cbo_type==3) //Drying...
        {
                $drying=",count(b.roll_no) as roll_no";
                $drying_group="";
        }
        else if($cbo_type==5)// Special Finish...
        {
                $sp_finish=",count(b.roll_no) as roll_no";
                $finish_group="";
        }
        else if($cbo_type==4)// Compacting...
        {
                $compact=",count(b.roll_no) as roll_no";
                $compact_group="";  
        }
        if($cbo_type==1)//  For Heat Setting 
        {
           //wo_non_ord_samp_booking_mst
           $sql="(select a.id,a.batch_no,a.booking_no,a.company_id,a.batch_date,a.batch_weight,a.color_id,a.booking_no_id,a.extention_no,a.batch_against,SUM(b.batch_qnty) AS batch_qnty,b.item_description,b.prod_id,b.width_dia_type,$group_conct,c.job_no_mst,d.job_no_prefix_num,d.buyer_name,f.process_end_date,f.process_start_date,f.production_date as end_date,f.end_hours,f.end_minutes,f.start_minutes,c.file_no,c.grouping,f.start_hours,f.shift_name,f.machine_id,f.floor_id,f.remarks $heat_set  from pro_batch_create_dtls b,wo_po_break_down c,wo_po_details_master d,pro_batch_create_mst a, pro_fab_subprocess f where  a.id=b.mst_id and f.batch_id=a.id and f.batch_id=b.mst_id  and  b.po_id=c.id and d.job_no=c.job_no_mst and f.entry_form=32 and  a.batch_against in(1,2,3)  $company_cond $working_company_cond   $dates_com $jobdata $batch_num $booking_num $buyerdata $order_no $year_cond $shift_cond $file_cond $ref_cond $color_name $floor_no_cond and a.entry_form=0  and b.status_active=1 and b.is_deleted=0 and a.status_active=1 and a.is_deleted=0 GROUP BY a.id, a.batch_no,a.booking_no,a.company_id, a.batch_date, a.batch_weight, a.color_id, a.booking_no_id, a.extention_no, a.batch_against,b.item_description, b.prod_id, b.width_dia_type,c.job_no_mst, d.job_no_prefix_num, d.buyer_name,c.file_no,c.grouping ,f.process_end_date,f.process_start_date,f.production_date, f.start_minutes,f.start_hours,f.end_hours, f.end_minutes, f.shift_name,f.machine_id,f.floor_id, f.remarks $heat_group)
            union
            (
                select a.id,a.batch_no,a.booking_no,a.company_id,a.batch_date,a.batch_weight,a.color_id,a.booking_no_id,a.extention_no,a.batch_against,SUM(b.batch_qnty) AS batch_qnty, b.item_description, b.prod_id,b.width_dia_type,null as po_number,null as po_id,null as job_no_mst,null as job_no_prefix_num, d.buyer_id as buyer_name, f.process_end_date, f.process_start_date, f.production_date as end_date,f.end_hours,f.end_minutes,f.start_minutes,null as file_no,null as grouping, f.start_hours, f.shift_name,f.machine_id,f.floor_id,f.remarks $heat_set from pro_batch_create_dtls b,pro_batch_create_mst a, pro_fab_subprocess f, wo_non_ord_samp_booking_mst d where  a.id=b.mst_id and f.batch_id=a.id  and f.batch_id=b.mst_id and f.entry_form=32 and a.booking_without_order=1 and a.booking_no is not null and  a.batch_against in(3) $company_cond $working_company_cond  $dates_com $batch_num $booking_num $buyerdata_non_ord $order_no $shift_cond $color_name $floor_no_cond and a.entry_form=0  and  b.status_active=1 and b.is_deleted=0 and a.status_active=1 and a.is_deleted=0 and b.po_id=0 and a.booking_no_id = d.id GROUP BY a.id, a.batch_no, a.booking_no, a.company_id, a.batch_date, a.batch_weight, a.color_id, a.booking_no_id, a.extention_no, a.batch_against, b.item_description,  b.prod_id, b.width_dia_type, d.buyer_id, f.process_end_date, f.process_start_date,f.production_date, f.start_minutes,f.start_hours,f.end_hours, f.end_minutes, f.shift_name,f.machine_id,f.floor_id, f.remarks $heat_group
            ) $order_by2"; //b.po_id
        }
        else if($cbo_type==2) // Slitting/Squeezing
        {
            $sql="(select a.company_id,a.id,a.batch_no,a.booking_no,a.batch_date,a.color_id,a.extention_no,a.batch_against,SUM(b.batch_qnty) AS batch_qnty,b.item_description,b.prod_id,b.width_dia_type,$group_conct,c.job_no_mst,d.job_no_prefix_num,d.buyer_name,f.process_end_date,f.process_start_date,c.file_no,c.grouping,f.production_date as end_date,f.start_minutes,f.start_hours,f.end_hours,f.end_minutes,f.shift_name,f.machine_id,f.floor_id,f.remarks $sliting_sq  from pro_batch_create_dtls b,wo_po_break_down c,wo_po_details_master d,pro_batch_create_mst a,pro_fab_subprocess f where   a.id=b.mst_id and b.po_id=c.id and d.job_no=c.job_no_mst  and  f.batch_id=a.id and  f.batch_id=b.mst_id and f.entry_form=30 and  a.batch_against in(1,2,3)  $company_cond $working_company_cond  $dates_com $jobdata $batch_num $booking_num $buyerdata $order_no $year_cond $shift_cond $file_cond $ref_cond $color_name $floor_no_cond and a.entry_form=0 and  b.status_active=1 and b.is_deleted=0 and a.status_active=1 and a.is_deleted=0  GROUP BY  b.item_description,a.company_id, a.id, a.batch_no,a.booking_no, a.batch_date,a.color_id, a.extention_no, a.batch_against,b.prod_id, b.width_dia_type,c.job_no_mst, d.job_no_prefix_num, d.buyer_name, f.process_end_date,f.process_start_date,f.production_date,f.start_minutes,f.start_hours, f.end_hours, f.end_minutes,f.shift_name, f.machine_id,f.floor_id,c.file_no,c.grouping, f.floor_id,f.remarks $sliting_group)
            union
            (
                select a.company_id,a.id,a.batch_no,a.booking_no,a.batch_date,a.color_id,a.extention_no,a.batch_against,SUM(b.batch_qnty) AS batch_qnty,b.item_description,b.prod_id,b.width_dia_type,null as po_number,null as po_id,null as job_no_mst,null as job_no_prefix_num, d.buyer_id as buyer_name, f.process_end_date,f.process_start_date,null as file_no,null as grouping,f.production_date as end_date,f.start_minutes,f.start_hours,f.end_hours,f.end_minutes,f.shift_name,f.machine_id,f.floor_id,f.remarks $sliting_sq  from pro_batch_create_dtls b,pro_batch_create_mst a,pro_fab_subprocess f, wo_non_ord_samp_booking_mst d  where a.id=b.mst_id  and f.batch_id=a.id and  f.batch_id=b.mst_id and a.entry_form=0 and f.entry_form=30 and a.batch_against in(3) $company_cond $working_company_cond  $dates_com $buyerdata_non_ord $batch_num $booking_num  $shift_cond $color_name $floor_no_cond and b.status_active=1 and b.is_deleted=0 and a.status_active=1 and a.is_deleted=0 and b.po_id=0 and a.booking_no_id = d.id GROUP BY b.item_description, a.company_id, a.id, a.batch_no,a.booking_no, a.batch_date,a.color_id, a.extention_no, a.batch_against,b.prod_id, b.width_dia_type, d.buyer_id, f.process_end_date, f.process_start_date,f.production_date,f.start_minutes,f.start_hours, f.end_hours, f.end_minutes, f.shift_name, f.machine_id,f.floor_id,f.floor_id,f.remarks $sliting_group
            )
             $order_by2 "; 
          }
        else if($cbo_type==3)//  Drying / Stentering 
        {
            $sql="(
            select a.company_id,a.id,a.batch_no,a.booking_no,a.batch_date,a.color_id,a.extention_no,a.batch_against,SUM(b.batch_qnty) AS batch_qnty,b.item_description,b.prod_id,b.width_dia_type,$group_conct,c.job_no_mst,d.job_no_prefix_num,d.buyer_name,f.process_end_date,f.process_start_date,c.file_no,c.grouping,f.production_date as end_date,f.start_minutes,f.start_hours,f.end_hours,f.end_minutes,f.shift_name,f.machine_id,f.floor_id,f.remarks $drying from pro_batch_create_dtls b,wo_po_break_down c,wo_po_details_master d,pro_batch_create_mst a,pro_fab_subprocess f where  a.id=b.mst_id and f.batch_id=b.mst_id and b.po_id=c.id and d.job_no=c.job_no_mst and f.entry_form=31 and a.batch_against in(1,2,3)  $company_cond $working_company_cond  and  f.batch_id=a.id $dates_com $jobdata $batch_num $booking_num $buyerdata $order_no $year_cond $shift_cond $file_cond $ref_cond $color_name $floor_no_cond and a.entry_form=0   and b.status_active=1 and b.is_deleted=0 and a.status_active=1 and a.is_deleted=0 GROUP BY  b.item_description,a.company_id, a.id, a.batch_no,a.booking_no, a.batch_date,a.color_id, a.extention_no, a.batch_against,b.prod_id, b.width_dia_type,c.job_no_mst, d.job_no_prefix_num, d.buyer_name, f.process_end_date,f.process_start_date,f.production_date,f.start_minutes,f.start_hours, f.end_hours, f.end_minutes,c.file_no,c.grouping, f.shift_name,f.machine_id,f.floor_id,f.remarks $drying_group)
            union
            (
                select a.company_id,a.id,a.batch_no,a.booking_no,a.batch_date,a.color_id,a.extention_no,a.batch_against,SUM(b.batch_qnty) AS batch_qnty,b.item_description,b.prod_id,b.width_dia_type,null as po_number,null as po_id,null as job_no_mst,null as job_no_prefix_num,d.buyer_id as buyer_name, f.process_end_date, f.process_start_date,null as file_no,null as grouping,f.production_date as end_date,f.start_minutes,f.start_hours,f.end_hours,f.end_minutes,f.shift_name,f.machine_id,f.floor_id,f.remarks $drying from pro_batch_create_dtls b,pro_batch_create_mst a,pro_fab_subprocess f, wo_non_ord_samp_booking_mst d where  a.id=b.mst_id  and f.batch_id=a.id and b.po_id=0 and a.booking_no_id = d.id and  f.entry_form=31 and  a.batch_against in(3) $company_cond $working_company_cond  $dates_com  $buyerdata_non_ord $batch_num $booking_num $shift_cond $color_name $floor_no_cond and a.entry_form=0  and b.status_active=1 and b.is_deleted=0 and a.status_active=1 and a.is_deleted=0  GROUP BY b.item_description, a.company_id, a.id, a.batch_no, a.booking_no, a.batch_date,a.color_id, a.extention_no, a.batch_against, b.prod_id, b.width_dia_type, d.buyer_id, f.process_end_date, f.process_start_date, f.production_date, f.start_minutes, f.start_hours, f.end_hours, f.end_minutes, f.shift_name, f.machine_id, f.floor_id, f.remarks $drying_group
            ) $order_by2 ";
        }
        else if($cbo_type==4)//  Compacting
        {
            /*$sql="(
            select a.company_id,a.id,a.batch_no,a.booking_no,a.batch_date,a.color_id,a.extention_no,a.batch_against,b.batch_qnty AS batch_qnty,b.item_description,b.prod_id,b.width_dia_type,$group_conct,c.job_no_mst,d.job_no_prefix_num,d.buyer_name,f.process_end_date,c.file_no,c.grouping,f.process_start_date,f.production_date as end_date,f.start_minutes,f.start_hours,f.end_hours,f.end_minutes,f.shift_name,f.machine_id,f.floor_id,f.remarks $compact  from pro_batch_create_dtls b,wo_po_break_down c,wo_po_details_master d,pro_batch_create_mst a,pro_fab_subprocess f,pro_fab_subprocess_dtls h where a.id=b.mst_id  and b.po_id=c.id and d.job_no=c.job_no_mst  $company_cond $working_company_cond  and f.batch_id=a.id and  f.batch_id=b.mst_id  and f.id=h.mst_id and h.prod_id=b.prod_id and  f.entry_form=h.entry_page $dates_com $jobdata $batch_num  $booking_num $buyerdata $order_no $year_cond $shift_cond $file_cond $ref_cond $color_name and a.entry_form=0   and f.entry_form=33 and f.re_stenter_no=0 and  a.batch_against in(1,2,3) and b.status_active=1 and b.is_deleted=0 and a.status_active=1 and a.is_deleted=0 GROUP BY b.item_description,a.company_id, a.id, a.batch_no,a.booking_no, a.batch_date,a.color_id, a.extention_no, a.batch_against,b.prod_id, b.width_dia_type,c.job_no_mst, d.job_no_prefix_num, d.buyer_name, f.process_end_date,f.process_start_date,f.production_date,f.start_minutes,f.start_hours, f.end_hours, f.end_minutes,f.shift_name, f.machine_id,f.floor_id,c.file_no,c.grouping,f.remarks,b.batch_qnty $compact_group )
            union
            (
            select a.company_id,a.id,a.batch_no,a.booking_no,a.batch_date,a.color_id,a.extention_no,a.batch_against,b.batch_qnty AS batch_qnty,b.item_description,b.prod_id,b.width_dia_type,null as po_number,null as po_id,null as job_no_mst,null as job_no_prefix_num,null as buyer_name,f.process_end_date,null as file_no,null as grouping,f.process_start_date,f.production_date as end_date,f.start_minutes,f.start_hours,f.end_hours,f.end_minutes,f.shift_name,f.machine_id,f.floor_id,f.remarks $compact  from pro_batch_create_dtls b,pro_batch_create_mst a,pro_fab_subprocess f,pro_fab_subprocess_dtls h where a.id=b.mst_id and  f.batch_id=a.id and  f.batch_id=b.mst_id and f.id=h.mst_id and h.prod_id=b.prod_id and  f.entry_form=h.entry_page $company_cond $working_company_cond   $dates_com $batch_num $booking_num $shift_cond $color_name $buyerdata_non_ord  and a.entry_form=0  and f.entry_form=33 and f.re_stenter_no=0 and  a.batch_against in(3)  and b.status_active=1 and b.is_deleted=0 and a.status_active=1 and a.is_deleted=0 and b.po_id=0 GROUP BY b.item_description,a.company_id, a.id, a.batch_no,a.booking_no, a.batch_date,a.color_id, a.extention_no, a.batch_against,b.prod_id, b.width_dia_type, f.process_end_date,f.process_start_date,f.production_date,f.start_minutes,f.start_hours, f.end_hours, f.end_minutes,f.shift_name, f.machine_id,f.floor_id,f.remarks,b.batch_qnty $compact_group
            ) $order_by2";*/
            $sql="(
            select a.company_id,a.id,a.batch_no,a.booking_no,a.batch_date,a.color_id,a.extention_no,a.batch_against,sum(b.batch_qnty) AS batch_qnty,b.item_description,b.prod_id,b.width_dia_type,$group_conct,c.job_no_mst,d.job_no_prefix_num,d.buyer_name,f.process_end_date,c.file_no,c.grouping,f.process_start_date,f.production_date as end_date,f.start_minutes,f.start_hours,f.end_hours,f.end_minutes,f.shift_name,f.machine_id,f.floor_id,f.remarks $compact  from pro_batch_create_dtls b,wo_po_break_down c,wo_po_details_master d,pro_batch_create_mst a,pro_fab_subprocess f where a.id=b.mst_id  and b.po_id=c.id and d.job_no=c.job_no_mst  $company_cond $working_company_cond  and f.batch_id=a.id and  f.batch_id=b.mst_id   $dates_com $jobdata $batch_num  $booking_num $buyerdata $order_no $year_cond $shift_cond $file_cond $ref_cond $color_name $floor_no_cond and a.entry_form=0   and f.entry_form=33 and f.re_stenter_no=0 and  a.batch_against in(1,2,3) and b.status_active=1 and b.is_deleted=0 and a.status_active=1 and a.is_deleted=0 GROUP BY b.item_description,a.company_id, a.id, a.batch_no,a.booking_no, a.batch_date,a.color_id, a.extention_no, a.batch_against,b.prod_id, b.width_dia_type,c.job_no_mst, d.job_no_prefix_num, d.buyer_name, f.process_end_date,f.process_start_date,f.production_date,f.start_minutes,f.start_hours, f.end_hours, f.end_minutes,f.shift_name, f.machine_id,f.floor_id,c.file_no,c.grouping,f.remarks $compact_group )
            union
            (
            select a.company_id,a.id,a.batch_no,a.booking_no,a.batch_date,a.color_id,a.extention_no,a.batch_against,sum(b.batch_qnty) AS batch_qnty,b.item_description,b.prod_id,b.width_dia_type,null as po_number,null as po_id,null as job_no_mst,null as job_no_prefix_num, d.buyer_id as buyer_name,f.process_end_date,null as file_no,null as grouping,f.process_start_date,f.production_date as end_date,f.start_minutes,f.start_hours,f.end_hours,f.end_minutes,f.shift_name,f.machine_id,f.floor_id,f.remarks $compact  from pro_batch_create_dtls b,pro_batch_create_mst a,pro_fab_subprocess f, wo_non_ord_samp_booking_mst d where a.id=b.mst_id and  f.batch_id=a.id and  f.batch_id=b.mst_id $company_cond $working_company_cond   $dates_com $batch_num $booking_num $shift_cond $color_name $floor_no_cond $buyerdata_non_ord  and a.entry_form=0  and f.entry_form=33 and f.re_stenter_no=0 and  a.batch_against in(3)  and b.status_active=1 and b.is_deleted=0 and a.status_active=1 and a.is_deleted=0 and b.po_id=0 and a.booking_no_id = d.id GROUP BY b.item_description, a.company_id, a.id, a.batch_no,a.booking_no, a.batch_date,a.color_id, a.extention_no, a.batch_against, b.prod_id, b.width_dia_type, d.buyer_id, f.process_end_date, f.process_start_date, f.production_date, f.start_minutes, f.start_hours, f.end_hours, f.end_minutes,f.shift_name, f.machine_id,f.floor_id,f.remarks $compact_group
            ) $order_by2";          
        }   
        else if($cbo_type==5)//  Special Finish
        {
            $sql="(select a.company_id,a.id,a.batch_no,a.booking_no,a.batch_date,a.color_id,a.extention_no,a.batch_against,SUM(b.batch_qnty) AS batch_qnty,b.item_description,b.po_id,b.prod_id,b.width_dia_type,c.job_no_mst,d.job_no_prefix_num,d.buyer_name,f.process_end_date,f.process_start_date,c.file_no,c.grouping,f.production_date as end_date,f.start_minutes,f.start_hours,f.end_hours,f.end_minutes,f.shift_name,f.machine_id,f.floor_id,f.remarks $sp_finish from pro_batch_create_dtls b,wo_po_break_down c,wo_po_details_master d,pro_batch_create_mst a,pro_fab_subprocess f where  f.batch_id=a.id and a.id=b.mst_id and f.entry_form=34 and  a.batch_against in(1,2,3) and b.po_id=c.id and d.job_no=c.job_no_mst $company_cond $working_company_cond   $dates_com $jobdata $batch_num $booking_num $buyerdata $order_no $year_cond $shift_cond $file_cond $ref_cond $color_name $floor_no_cond and a.entry_form=0  and b.status_active=1 and b.is_deleted=0 and a.status_active=1 and a.is_deleted=0 GROUP BY b.po_id, b.item_description,a.company_id, a.id, a.batch_no,a.booking_no, a.batch_date,a.color_id, a.extention_no, a.batch_against,b.prod_id, b.width_dia_type,c.job_no_mst, d.job_no_prefix_num, d.buyer_name, f.process_end_date,c.file_no,c.grouping,f.process_start_date,f.production_date,f.start_minutes,f.start_hours, f.end_hours, f.end_minutes,f.shift_name, f.machine_id,f.floor_id,f.remarks $finish_group)
            union
            (
            select a.company_id,a.id,a.batch_no,a.booking_no,a.batch_date,a.color_id,a.extention_no,a.batch_against,SUM(b.batch_qnty) AS batch_qnty,b.item_description,b.po_id,b.prod_id,b.width_dia_type,null as job_no_mst,null as job_no_prefix_num, d.buyer_id as buyer_name, f.process_end_date,f.process_start_date,null as file_no,null as grouping,f.production_date as end_date,f.start_minutes,f.start_hours,f.end_hours,f.end_minutes,f.shift_name,f.machine_id,f.floor_id,f.remarks $sp_finish from pro_batch_create_dtls b,pro_batch_create_mst a,pro_fab_subprocess f, wo_non_ord_samp_booking_mst d  where a.id=b.mst_id and  f.batch_id=a.id and  f.batch_id=b.mst_id and f.entry_form=34 and  a.batch_against in(3)  $company_cond $working_company_cond   $dates_com $buyerdata_non_ord  $batch_num $booking_num $shift_cond $color_name $floor_no_cond and a.entry_form=0 and b.status_active=1 and b.is_deleted=0 and a.status_active=1 and a.is_deleted=0 and b.po_id=0 and a.booking_no_id = d.id GROUP BY b.po_id, b.item_description,a.company_id, a.id, a.batch_no, a.booking_no, a.batch_date,a.color_id, a.extention_no, a.batch_against,b.prod_id, b.width_dia_type, d.buyer_id, f.process_end_date, f.process_start_date, f.production_date, f.start_minutes, f.start_hours, f.end_hours, f.end_minutes,f.shift_name, f.machine_id, f.floor_id, f.remarks $finish_group
            ) $order_by2";
        }
        else if($cbo_type==9)//  Stentering 
        {
            $sql="(select a.company_id,a.id,a.batch_no,a.booking_no,a.batch_date,a.color_id,a.extention_no,a.batch_against,SUM(b.batch_qnty) AS batch_qnty,b.item_description,b.prod_id,b.width_dia_type,$group_conct,c.job_no_mst,d.job_no_prefix_num,d.buyer_name,f.process_end_date,f.process_start_date,c.file_no,c.grouping,f.production_date as end_date,f.start_minutes,f.start_hours,f.end_hours,f.end_minutes,f.shift_name,f.machine_id,f.floor_id,f.remarks $stenter from pro_batch_create_dtls b,wo_po_break_down c,wo_po_details_master d,pro_batch_create_mst a,pro_fab_subprocess f where  f.batch_id=a.id and a.id=b.mst_id and f.entry_form=48 and  a.batch_against in(1,2,3) and b.po_id=c.id and d.job_no=c.job_no_mst and  f.batch_id=b.mst_id $company_cond $working_company_cond  $dates_com $jobdata $batch_num $booking_num $buyerdata $order_no $year_cond $shift_cond $file_cond $ref_cond $color_name $floor_no_cond and a.entry_form=0 and f.re_stenter_no=0 and b.status_active=1 and b.is_deleted=0 and a.status_active=1 and a.is_deleted=0 and f.status_active=1 and f.is_deleted=0 GROUP BY  b.item_description,a.company_id, a.id, a.batch_no,a.booking_no, a.batch_date,a.color_id, a.extention_no, a.batch_against,b.prod_id, b.width_dia_type,c.job_no_mst, d.job_no_prefix_num, d.buyer_name, f.process_end_date,f.process_start_date,c.file_no,c.grouping,f.production_date,f.start_minutes,f.start_hours, f.end_hours, f.end_minutes, f.shift_name,f.machine_id,f.floor_id,f.remarks $stenter_group)
            union
            (
            select a.company_id,a.id,a.batch_no,a.booking_no,a.batch_date,a.color_id,a.extention_no,a.batch_against,SUM(b.batch_qnty) AS batch_qnty,b.item_description,b.prod_id,b.width_dia_type,null as po_number,null as po_id,null as job_no_mst,null as job_no_prefix_num,d.buyer_id as buyer_name, f.process_end_date, f.process_start_date,null as file_no,null as grouping,f.production_date as end_date,f.start_minutes, f.start_hours, f.end_hours, f.end_minutes, f.shift_name, f.machine_id, f.floor_id,f.remarks $stenter from pro_batch_create_dtls b,pro_batch_create_mst a,pro_fab_subprocess f, wo_non_ord_samp_booking_mst d  where   f.batch_id=a.id and f.batch_id=b.mst_id and a.id=b.mst_id and f.entry_form=48 $company_cond $working_company_cond  $dates_com $buyerdata_non_ord  $batch_num $booking_num  $shift_cond $color_name $floor_no_cond and a.entry_form=0 and f.re_stenter_no=0  and  a.batch_against in(3)  and b.status_active=1 and b.is_deleted=0 and a.status_active=1 and a.is_deleted=0 and f.status_active=1 and f.is_deleted=0 and b.po_id=0 and a.booking_no_id = d.id  GROUP BY  b.item_description,a.company_id, a.id, a.batch_no,a.booking_no, a.batch_date,a.color_id, a.extention_no, a.batch_against,b.prod_id, b.width_dia_type, d.buyer_id, f.process_end_date, f.process_start_date,f.production_date,f.start_minutes,f.start_hours, f.end_hours, f.end_minutes, f.shift_name, f.machine_id,f.floor_id,f.remarks $stenter_group ) $order_by2 ";
        }
        else if($cbo_type==11)//  Re Stentering 
        {
            $sql="(select a.company_id,a.id,a.batch_no,a.booking_no,a.batch_date,a.color_id,a.extention_no,a.batch_against,SUM(b.batch_qnty) AS batch_qnty,b.item_description,b.prod_id,b.width_dia_type,$group_conct,c.job_no_mst,d.job_no_prefix_num,d.buyer_name,f.process_end_date,f.process_start_date,c.file_no,c.grouping,f.production_date as end_date,f.start_minutes,f.start_hours,f.end_hours,f.end_minutes,f.shift_name,f.machine_id,f.floor_id,f.remarks $stenter,f.re_stenter_no from pro_batch_create_dtls b,wo_po_break_down c,wo_po_details_master d,pro_batch_create_mst a,pro_fab_subprocess f  where   a.id=b.mst_id and f.entry_form=48 and  a.batch_against in(1,2,3) and b.po_id=c.id and d.job_no=c.job_no_mst and  f.batch_id=a.id $company_cond $working_company_cond   $dates_com $jobdata $batch_num $booking_num $buyerdata $order_no $year_cond $shift_cond $file_cond $ref_cond $color_name $floor_no_cond and a.entry_form=0   and b.status_active=1 and b.is_deleted=0 and a.status_active=1 and a.is_deleted=0 and f.status_active=1 and f.is_deleted=0 and f.re_stenter_no!=0 GROUP BY  b.item_description,a.company_id, a.id, a.batch_no,a.booking_no, a.batch_date,a.color_id, a.extention_no, a.batch_against,b.prod_id, b.width_dia_type,c.job_no_mst, d.job_no_prefix_num, d.buyer_name, f.process_end_date,f.process_start_date,c.file_no,c.grouping,f.production_date,f.start_minutes,f.start_hours, f.end_hours, f.end_minutes, f.shift_name,f.machine_id,f.floor_id,f.remarks,f.re_stenter_no $stenter_group)
            union
            (
            select a.company_id,a.id,a.batch_no,a.booking_no,a.batch_date,a.color_id,a.extention_no,a.batch_against,SUM(b.batch_qnty) AS batch_qnty,b.item_description,b.prod_id,b.width_dia_type,null as po_number,null as po_id,null as job_no_mst,null as job_no_prefix_num, d.buyer_id as buyer_name, f.process_end_date, f.process_start_date,null as file_no,null as grouping,f.production_date as end_date, f.start_minutes, f.start_hours,f.end_hours, f.end_minutes, f.shift_name, f.machine_id, f.floor_id,f.remarks $stenter,f.re_stenter_no from pro_batch_create_dtls b,pro_batch_create_mst a,pro_fab_subprocess f, wo_non_ord_samp_booking_mst d where f.batch_id=a.id  and a.entry_form=0 and a.id=b.mst_id and f.entry_form=48 and  a.batch_against in(3) $company_cond $working_company_cond $dates_com  $buyerdata_non_ord  $batch_num $booking_num $shift_cond $color_name $floor_no_cond and b.status_active=1 and b.is_deleted=0 and a.status_active=1 and a.is_deleted=0 and f.status_active=1 and f.is_deleted=0 and b.po_id=0 and a.booking_no_id = d.id and f.re_stenter_no!=0   GROUP BY b.po_id, b.item_description,a.company_id, a.id, a.batch_no, a.booking_no, a.batch_date,a.color_id, a.extention_no, a.batch_against,b.prod_id, b.width_dia_type, d.buyer_id, f.process_end_date, f.process_start_date, f.production_date, f.start_minutes, f.start_hours, f.end_hours, f.end_minutes, f.shift_name, f.machine_id, f.floor_id, f.remarks,f.re_stenter_no $stenter_group ) $order_by2 
            "; //echo $sql;
        }
        else if($cbo_type==12)//  Re Compacting
        {
            $sql="(
            select a.company_id,a.id,a.batch_no,a.booking_no,a.batch_date,a.color_id,a.extention_no,a.batch_against,SUM(b.batch_qnty) AS batch_qnty,b.item_description,b.prod_id,b.width_dia_type,$group_conct,c.job_no_mst,d.job_no_prefix_num,d.buyer_name,f.process_end_date,c.file_no,c.grouping,f.process_start_date,f.production_date as end_date,f.start_minutes,f.start_hours,f.end_hours,f.end_minutes,f.shift_name,f.machine_id,f.floor_id,f.remarks $compact,f.re_stenter_no  from pro_batch_create_dtls b,wo_po_break_down c,wo_po_details_master d,pro_batch_create_mst a,pro_fab_subprocess f where  a.id=b.mst_id and b.po_id=c.id and d.job_no=c.job_no_mst and f.batch_id=a.id and  f.batch_id=b.mst_id  $company_cond $working_company_cond  and f.entry_form=33 and  a.entry_form=0  and f.re_stenter_no!=0 and  a.batch_against in(1,2,3)  $dates_com $jobdata $batch_num $booking_num $buyerdata $order_no $year_cond $shift_cond $file_cond $ref_cond $color_name $floor_no_cond and b.status_active=1 and b.is_deleted=0 and a.status_active=1 and a.is_deleted=0 GROUP BY b.item_description,a.company_id, a.id, a.batch_no,a.booking_no, a.batch_date,a.color_id, a.extention_no, a.batch_against,b.prod_id, b.width_dia_type,c.job_no_mst, d.job_no_prefix_num, d.buyer_name, f.process_end_date,f.process_start_date,f.production_date,f.start_minutes,f.start_hours, f.end_hours, f.end_minutes,f.shift_name, f.machine_id,f.floor_id,c.file_no,c.grouping,f.remarks,f.re_stenter_no $compact_group )
            union
            (
            select a.company_id,a.id,a.batch_no,a.booking_no,a.batch_date,a.color_id,a.extention_no,a.batch_against,SUM(b.batch_qnty) AS batch_qnty,b.item_description,b.prod_id,b.width_dia_type,null as po_number,null as po_id,null as job_no_mst,null as job_no_prefix_num, d.buyer_id as buyer_name,f.process_end_date,null as file_no,null as grouping,f.process_start_date,f.production_date as end_date,f.start_minutes,f.start_hours,f.end_hours,f.end_minutes,f.shift_name,f.machine_id,f.floor_id,f.remarks $compact,f.re_stenter_no  from pro_batch_create_dtls b,pro_batch_create_mst a,pro_fab_subprocess f, wo_non_ord_samp_booking_mst d where a.id=b.mst_id and f.entry_form=33 and f.batch_id=b.mst_id and  f.batch_id=a.id  $company_cond $working_company_cond   $dates_com $batch_num $booking_num $shift_cond $buyerdata_non_ord  $color_name $floor_no_cond and a.entry_form=0  and f.re_stenter_no!=0 and  a.batch_against in(3)  and b.status_active=1 and b.is_deleted=0 and a.status_active=1 and a.is_deleted=0 and b.po_id=0 and a.booking_no_id = d.id GROUP BY b.item_description,a.company_id, a.id, a.batch_no,a.booking_no, a.batch_date,a.color_id, a.extention_no, a.batch_against,b.prod_id, b.width_dia_type, d.buyer_id, f.process_end_date, f.process_start_date, f.production_date, f.start_minutes,f.start_hours, f.end_hours, f.end_minutes,f.shift_name, f.machine_id, f.floor_id, f.remarks,f.re_stenter_no $compact_group
            ) $order_by2";
            //echo $sql;
        }   
        if($cbo_type==0)//  For All Search
        {
            $sql_heat="(
            select a.company_id,a.id,a.batch_no,a.booking_no,a.batch_date,a.color_id,a.extention_no,a.batch_against,SUM(b.batch_qnty) AS batch_qnty,b.item_description,b.po_id,b.prod_id,b.width_dia_type,$group_conct,c.job_no_mst,d.job_no_prefix_num,d.buyer_name,c.file_no,c.grouping,f.entry_form,f.process_end_date,f.process_start_date,f.production_date as end_date,f.start_minutes,f.start_hours,f.end_hours,f.end_minutes,f.shift_name,f.machine_id,f.floor_id,f.remarks $heat_set from pro_batch_create_dtls b,wo_po_break_down c,wo_po_details_master d,pro_batch_create_mst a,pro_fab_subprocess f where  f.batch_id=a.id and b.po_id=c.id and d.job_no=c.job_no_mst and a.id=b.mst_id and f.entry_form in(32) and  a.batch_against in(1,2,3)  $company_cond $working_company_cond $dates_com $jobdata $batch_num $booking_num $buyerdata $order_no $year_cond $shift_cond $file_cond $ref_cond $color_name $floor_no_cond and a.entry_form=0 and b.status_active=1 and b.is_deleted=0 and a.status_active=1 and a.is_deleted=0 GROUP BY  b.item_description,a.company_id, a.id, a.batch_no,a.booking_no, a.batch_date,a.color_id, a.extention_no, a.batch_against,b.prod_id, b.width_dia_type,c.job_no_mst, d.job_no_prefix_num, d.buyer_name,f.entry_form, f.process_end_date, f.process_start_date,f.production_date,f.start_minutes,f.start_hours,f.end_hours, f.end_minutes,c.file_no,c.grouping,f.shift_name,f.machine_id,f.floor_id,f.remarks $heat_group)
            union
            (
            select a.company_id,a.id,a.batch_no,a.booking_no,a.batch_date,a.color_id,a.extention_no,a.batch_against,SUM(b.batch_qnty) AS batch_qnty,b.item_description,b.prod_id,b.width_dia_type,null as po_number,null as po_id,null as job_no_mst,null as job_no_prefix_num, d.buyer_id as buyer_name,null as file_no,null as grouping,f.entry_form,f.process_end_date,f.process_start_date,f.production_date as end_date,f.start_minutes,f.start_hours,f.end_hours,f.end_minutes,f.shift_name,f.machine_id,f.floor_id,f.remarks $heat_set from pro_batch_create_dtls b,pro_batch_create_mst a,pro_fab_subprocess f, wo_non_ord_samp_booking_mst d where a.id=b.mst_id and  f.batch_id=a.id and f.entry_form in(32) and a.batch_against in(1,2,3) $company_cond  $working_company_cond  $dates_com  $batch_num $booking_num $buyerdata_non_ord  $shift_cond $color_name $floor_no_cond and a.entry_form=0 and b.po_id=0 and a.booking_no_id = d.id and b.status_active=1 and b.is_deleted=0 and a.status_active=1 and a.is_deleted=0 GROUP BY b.po_id, b.item_description,a.company_id, a.id, a.batch_no,a.booking_no, a.batch_date,a.color_id, a.extention_no, a.batch_against,b.prod_id, b.width_dia_type, d.buyer_id, f.entry_form, f.process_end_date, f.process_start_date, f.production_date, f.start_minutes,f.start_hours,f.end_hours, f.end_minutes,f.shift_name,f.machine_id,f.floor_id,f.remarks $heat_group
            )  $order_by2 ";
            $sql_slitting="(
            select a.company_id,a.id,a.batch_no,a.booking_no,a.batch_date,a.color_id,a.extention_no,a.batch_against,SUM(b.batch_qnty) AS batch_qnty,b.item_description,b.prod_id,b.width_dia_type,$group_conct,c.job_no_mst,d.job_no_prefix_num,d.buyer_name,c.file_no,c.grouping,f.shift_name,f.entry_form,f.process_end_date,f.process_start_date,f.production_date as end_date,f.start_minutes,f.start_hours,f.end_hours,f.end_minutes,f.machine_id,f.floor_id,f.remarks 
            $sliting_sq  from pro_batch_create_dtls b,wo_po_break_down c,wo_po_details_master d,pro_batch_create_mst a,pro_fab_subprocess f where    f.batch_id=a.id   and a.id=b.mst_id and f.entry_form in(30) and  a.batch_against in(1,2,3) and b.po_id=c.id and d.job_no=c.job_no_mst $company_cond  $working_company_cond $dates_com $jobdata $batch_num $booking_num $buyerdata $order_no $year_cond $shift_cond $file_cond $ref_cond $color_name $floor_no_cond and a.entry_form=0 and b.status_active=1 and b.is_deleted=0 and a.status_active=1 and a.is_deleted=0 GROUP BY b.item_description,a.company_id, a.id, a.batch_no,a.booking_no, a.batch_date,a.color_id, a.extention_no, a.batch_against,b.prod_id, b.width_dia_type,c.job_no_mst, d.job_no_prefix_num, d.buyer_name,c.file_no,c.grouping,f.shift_name,f.entry_form, f.process_end_date,f.process_start_date,f.production_date,f.start_minutes,f.start_hours, f.end_hours, f.end_minutes, f.machine_id,f.floor_id,f.remarks $sliting_group)
            union
            (
                select a.company_id,a.id,a.batch_no,a.booking_no,a.batch_date,a.color_id,a.extention_no,a.batch_against,SUM(b.batch_qnty) AS batch_qnty,b.item_description,b.prod_id,b.width_dia_type,null as po_number,null as po_id,null as job_no_mst,null as job_no_prefix_num, d.buyer_id as buyer_name,null as file_no,null as grouping,f.shift_name,f.entry_form,f.process_end_date,f.process_start_date,f.production_date as end_date, f.start_minutes,f.start_hours,f.end_hours,f.end_minutes,f.machine_id,f.floor_id,f.remarks 
            $sliting_sq  from pro_batch_create_dtls b,pro_batch_create_mst a,pro_fab_subprocess f, wo_non_ord_samp_booking_mst d where a.id=b.mst_id and f.batch_id=a.id and f.entry_form in(30) and  a.batch_against in(3) $company_cond  $working_company_cond $dates_com $batch_num $booking_num  $shift_cond $buyerdata_non_ord  $color_name $floor_no_cond and a.entry_form=0 and b.po_id=0 and a.booking_no_id = d.id and b.status_active=1 and b.is_deleted=0 and a.status_active=1 and a.is_deleted=0 GROUP BY b.item_description, a.company_id, a.id, a.batch_no,a.booking_no, a.batch_date,a.color_id, a.extention_no, a.batch_against, b.prod_id, b.width_dia_type, d.buyer_id, f.shift_name,f.entry_form, f.process_end_date, f.process_start_date, f.production_date, f.start_minutes,f.start_hours, f.end_hours, f.end_minutes, f.machine_id, f.floor_id,f.remarks $sliting_group
            ) $order_by2 ";

            $sql_drying=" (
            select a.company_id,a.id,a.batch_no,a.booking_no,a.batch_date,a.color_id,a.extention_no,a.batch_against,SUM(b.batch_qnty) AS batch_qnty,b.item_description,b.prod_id,b.width_dia_type,$group_conct,c.job_no_mst,d.job_no_prefix_num,d.buyer_name,c.file_no,c.grouping,f.shift_name,f.entry_form,f.process_end_date,f.process_start_date,f.production_date as end_date,f.start_minutes,f.start_hours,f.end_hours,f.end_minutes,f.machine_id,f.floor_id,f.remarks $drying  from pro_batch_create_dtls b,wo_po_break_down c,wo_po_details_master d,pro_batch_create_mst a,pro_fab_subprocess f where  f.batch_id=a.id  and a.id=b.mst_id and f.entry_form in(31) and  a.batch_against in(1,2,3) and b.po_id=c.id and d.job_no=c.job_no_mst and a.entry_form=0  $company_cond  $working_company_cond $dates_com $jobdata $batch_num $booking_num $buyerdata $order_no $year_cond $shift_cond $file_cond $ref_cond $color_name  $floor_no_cond  and b.status_active=1 and b.is_deleted=0 and a.status_active=1 and a.is_deleted=0 GROUP BY  b.item_description,a.company_id, a.id, a.batch_no,a.booking_no, a.batch_date,a.color_id, a.extention_no, a.batch_against,b.prod_id, b.width_dia_type,c.job_no_mst, d.job_no_prefix_num, d.buyer_name,f.entry_form, f.process_end_date ,f.process_start_date,f.production_date,f.start_minutes,f.start_hours,f.end_hours, f.end_minutes,c.file_no,c.grouping,f.shift_name,f.floor_id, f.machine_id,f.remarks $drying_group)
            union
            (
            select a.company_id,a.id,a.batch_no,a.booking_no,a.batch_date,a.color_id,a.extention_no,a.batch_against,SUM(b.batch_qnty) AS batch_qnty,b.item_description,b.prod_id,b.width_dia_type,null as po_number,null as po_id,null as job_no_mst,null as job_no_prefix_num, d.buyer_id as buyer_name,null as file_no,null as grouping,f.shift_name,f.entry_form,f.process_end_date,f.process_start_date,f.production_date as end_date,f.start_minutes,f.start_hours,f.end_hours,f.end_minutes,f.machine_id,f.floor_id,f.remarks $drying  from pro_batch_create_dtls b,pro_batch_create_mst a,pro_fab_subprocess f, wo_non_ord_samp_booking_mst d where  a.id=b.mst_id and f.batch_id=a.id  $company_cond  $working_company_cond   $dates_com  $buyerdata_non_ord  $batch_num $booking_num  $shift_cond $color_name $floor_no_cond and a.entry_form=0 and b.po_id=0 and a.booking_no_id = d.id and f.entry_form in(31) and  a.batch_against in(3) and  b.status_active=1 and b.is_deleted=0 and a.status_active=1 and a.is_deleted=0 GROUP BY b.item_description,a.company_id, a.id, a.batch_no,a.booking_no, a.batch_date,a.color_id, a.extention_no, a.batch_against,b.prod_id, b.width_dia_type, d.buyer_id, f.entry_form, f.process_end_date ,f.process_start_date,f.production_date,f.start_minutes,f.start_hours,f.end_hours, f.end_minutes,f.shift_name, f.machine_id,f.floor_id,f.remarks $drying_group
            ) $order_by2 ";

            $sql_stentering="(select a.company_id,a.id,a.batch_no,a.booking_no,a.batch_date,a.color_id,a.extention_no,a.batch_against,SUM(b.batch_qnty) AS batch_qnty,b.item_description,b.prod_id,b.width_dia_type,$group_conct,c.job_no_mst,d.job_no_prefix_num,d.buyer_name,c.file_no,c.grouping,f.shift_name,f.process_end_date,f.process_start_date,f.production_date as end_date,f.start_minutes,f.start_hours,f.end_hours,f.end_minutes,f.machine_id,f.floor_id,f.remarks $stenter  from pro_batch_create_dtls b,wo_po_break_down c,wo_po_details_master d,pro_batch_create_mst a,pro_fab_subprocess f where f.batch_id=a.id and a.id=b.mst_id and f.entry_form=48 and f.re_stenter_no=0 and  a.batch_against in(1,2,3) and b.po_id=c.id and d.job_no=c.job_no_mst  $company_cond  $working_company_cond $dates_com $jobdata $batch_num $booking_num $buyerdata $order_no $year_cond $shift_cond $file_cond $ref_cond $color_name $floor_no_cond and a.entry_form=0 and   b.status_active=1 and b.is_deleted=0 and a.status_active=1 and a.is_deleted=0 and f.status_active=1 and f.is_deleted=0 GROUP BY  b.item_description,a.company_id, a.id, a.batch_no,a.booking_no, a.batch_date,a.color_id, a.extention_no, a.batch_against,b.prod_id, b.width_dia_type,c.job_no_mst, d.job_no_prefix_num, d.buyer_name,c.file_no,c.grouping,f.shift_name, f.process_end_date,f.process_start_date,f.production_date,f.start_minutes,f.start_hours, f.end_hours, f.end_minutes, f.machine_id,f.floor_id,f.remarks $stenter_group)
            union
            (
            select a.company_id,a.id,a.batch_no,a.booking_no,a.batch_date,a.color_id,a.extention_no,a.batch_against,SUM(b.batch_qnty) AS batch_qnty,b.item_description,null as po_id,b.prod_id,b.width_dia_type,null as po_number,null as po_id,null as job_no_mst,null as job_no_prefix_num, d.buyer_id as buyer_name,null as file_no,null as grouping,f.shift_name,f.process_end_date,f.process_start_date,f.production_date as end_date,f.start_minutes,f.start_hours,f.end_hours,f.end_minutes,f.machine_id,f.floor_id,f.remarks $stenter  from pro_batch_create_dtls b,pro_batch_create_mst a,pro_fab_subprocess f, wo_non_ord_samp_booking_mst d where  f.batch_id=a.id  and a.id=b.mst_id and f.entry_form=48 and f.re_stenter_no=0 and  a.batch_against in(3) and b.po_id=0 and a.booking_no_id = d.id $company_cond  $working_company_cond $dates_com  $batch_num $booking_num  $buyerdata_non_ord  $shift_cond $color_name $floor_no_cond and a.entry_form=0  and b.status_active=1 and b.is_deleted=0 and a.status_active=1 and a.is_deleted=0 and f.status_active=1 and f.is_deleted=0  GROUP BY b.po_id, b.item_description,a.company_id, a.id, a.batch_no,a.booking_no, a.batch_date,a.color_id, a.extention_no, a.batch_against,b.prod_id, b.width_dia_type, d.buyer_id, f.shift_name, f.process_end_date,f.process_start_date,f.production_date,f.start_minutes,f.start_hours, f.end_hours, f.end_minutes, f.machine_id,f.floor_id,f.remarks $stenter_group
            ) $order_by2 ";

           $sql_compacting="(select a.company_id,a.id,a.batch_no,a.booking_no,a.batch_date,a.color_id,a.extention_no,a.batch_against,SUM(b.batch_qnty) AS batch_qnty,b.item_description,b.prod_id,b.width_dia_type,$group_conct,c.job_no_mst,d.job_no_prefix_num,d.buyer_name,c.file_no,c.grouping,f.shift_name,f.entry_form,f.process_end_date,f.process_start_date,f.production_date as end_date,f.start_minutes,f.start_hours,f.end_hours,f.end_minutes,f.machine_id,f.floor_id,f.remarks $compact from pro_batch_create_dtls b,wo_po_break_down c,wo_po_details_master d,pro_batch_create_mst a,pro_fab_subprocess f where  b.po_id=c.id and d.job_no=c.job_no_mst and f.batch_id=a.id  and a.id=b.mst_id and f.entry_form in(33) and f.re_stenter_no=0 and  a.batch_against in(1,2,3) $company_cond  $working_company_cond $dates_com $jobdata $batch_num $booking_num $buyerdata $order_no $year_cond $color_name $floor_no_cond $shift_cond $file_cond $ref_cond and a.entry_form=0   and b.status_active=1 and b.is_deleted=0 and a.status_active=1 and a.is_deleted=0 GROUP BY  b.item_description,a.company_id, a.id, a.batch_no,a.booking_no, a.batch_date,a.color_id, a.extention_no, a.batch_against,b.prod_id, b.width_dia_type,c.job_no_mst, d.job_no_prefix_num, d.buyer_name,f.entry_form, f.process_end_date,f.process_start_date,f.production_date,f.start_minutes,f.start_hours, f.end_hours,c.file_no,c.grouping,f.shift_name, f.end_minutes, f.floor_id,f.machine_id,f.remarks $compact_group)
            union
            (
            select a.company_id,a.id,a.batch_no,a.booking_no,a.batch_date,a.color_id,a.extention_no,a.batch_against,SUM(b.batch_qnty) AS batch_qnty,b.item_description,b.prod_id,b.width_dia_type,null as po_number,null as po_id,null as job_no_mst,null as job_no_prefix_num, d.buyer_id as buyer_name,null as file_no,null as grouping,f.shift_name,f.entry_form,f.process_end_date,f.process_start_date,f.production_date as end_date,f.start_minutes,f.start_hours,f.end_hours,f.end_minutes,f.machine_id,f.floor_id,f.remarks $compact from pro_batch_create_dtls b,pro_batch_create_mst a,pro_fab_subprocess f, wo_non_ord_samp_booking_mst d where a.id=b.mst_id and  f.batch_id=a.id and f.entry_form in(33) and f.re_stenter_no=0 and  a.batch_against in(3) and b.po_id=0 and a.booking_no_id = d.id $company_cond $working_company_cond $dates_com $buyerdata_non_ord  $batch_num $booking_num  $color_name $floor_no_cond $shift_cond and a.entry_form=0  and b.status_active=1 and b.is_deleted=0 and a.status_active=1 and a.is_deleted=0 GROUP BY b.item_description,a.company_id, a.id, a.batch_no,a.booking_no, a.batch_date,a.color_id, a.extention_no, a.batch_against,b.prod_id, b.width_dia_type, d.buyer_id, f.entry_form, f.process_end_date, f.process_start_date, f.production_date,f.start_minutes,f.start_hours, f.end_hours,f.shift_name, f.end_minutes, f.floor_id, f.machine_id,f.remarks $compact_group
            )  $order_by2 ";
            
           $sql_special="(
            select a.company_id,a.id,a.batch_no,a.booking_no,a.batch_date,a.color_id,a.extention_no,a.batch_against,SUM(b.batch_qnty) AS batch_qnty,b.item_description,b.po_id,b.prod_id,b.width_dia_type,$group_conct,c.job_no_mst,d.job_no_prefix_num,d.buyer_name,c.file_no,c.grouping,f.shift_name,f.entry_form,f.process_end_date,f.process_start_date,f.production_date as end_date,f.start_minutes,f.start_hours,f.end_hours,f.end_minutes,f.machine_id,f.floor_id,f.remarks $sp_finish  from pro_batch_create_dtls b,wo_po_break_down c,wo_po_details_master d,pro_batch_create_mst a,pro_fab_subprocess f where a.id=b.mst_id and f.batch_id=a.id  and b.po_id=c.id and d.job_no=c.job_no_mst $company_cond   $working_company_cond  $dates_com $jobdata $batch_num $booking_num $buyerdata $order_no $year_cond $color_name $floor_no_cond $shift_cond $file_cond $ref_cond and a.entry_form=0 and f.entry_form in(34) and  a.batch_against in(1,2,3)  and b.status_active=1 and b.is_deleted=0 and a.status_active=1 and a.is_deleted=0 GROUP BY  b.item_description,a.company_id, a.id, a.batch_no,a.booking_no, a.batch_date,a.color_id, a.extention_no, a.batch_against,b.prod_id, b.width_dia_type,c.job_no_mst, d.job_no_prefix_num, d.buyer_name,f.entry_form, f.process_end_date,f.process_start_date,f.production_date,f.start_minutes,f.start_hours, f.end_hours,c.file_no,c.grouping,f.shift_name, f.end_minutes, f.machine_id,f.floor_id,f.remarks $finish_group)         
            union
            (
            select a.company_id,a.id,a.batch_no,a.booking_no,a.batch_date,a.color_id,a.extention_no,a.batch_against,SUM(b.batch_qnty) AS batch_qnty,b.item_description,b.prod_id,b.width_dia_type,null as po_number,null as po_id,null as job_no_mst,null as job_no_prefix_num, d.buyer_id as buyer_name,null as file_no,null as grouping,f.shift_name,f.entry_form,f.process_end_date,f.process_start_date,f.production_date as end_date,f.start_minutes,f.start_hours,f.end_hours,f.end_minutes,f.machine_id,f.floor_id,f.remarks $sp_finish  from pro_batch_create_dtls b,pro_batch_create_mst a,pro_fab_subprocess f, wo_non_ord_samp_booking_mst d where a.id=b.mst_id and  f.batch_id=a.id and f.entry_form in(34) and  a.batch_against in(1,2,3) and b.po_id=0 and a.booking_no_id = d.id $company_cond  $working_company_cond  $dates_com  $batch_num $booking_num $buyerdata_non_ord  $color_name $floor_no_cond $shift_cond and a.entry_form=0 and b.status_active=1 and b.is_deleted=0 and a.status_active=1 and a.is_deleted=0   GROUP BY b.item_description,a.company_id,a.id,a.batch_no,a.booking_no,a.batch_date,a.color_id,a.extention_no,a.batch_against,b.prod_id, b.width_dia_type, d.buyer_id, f.entry_form, f.process_end_date, f.process_start_date, f.production_date, f.start_minutes, f.start_hours, f.end_hours,f.shift_name, f.end_minutes, f.machine_id,f.floor_id,f.remarks $finish_group
            ) $order_by2 ";
        }
        //echo $sql;
        ob_start();
        $type_array_check=array(0,6,7,8,10);
        if(!in_array($cbo_type,$type_array_check))
        {
            $batchdata=sql_select($sql);
        }
        else
        {
            $search_by_arr=array(0=>"--All--",1=>"Heat Setting",2=>"Slitting/Squeezing",3=>"Drying", 9=>"Stentering",4=>"Compacting",5=>"Special Finish",6=>"Wait For Slitting/Squeezing(Unload)",10=>"Wait For Stentering(Slitting/Squeezing)",7=>"Wait For Drying(Stentering)",8=>"Wait For Compacting(Drying)",11=>"Re Stentering(Multi)",12=>"Re Compacting(Multi)");
            ?>
            <div style="text-align: center;color: red;font-weight: bold;font-size: 20px;"><? echo ucfirst($search_by_arr[$cbo_type]);?> is not work this button.</div>
            <?
            die();
        }
        $group_by=str_replace("'",'',$cbo_group_by);
        $po_id="";
        foreach($batchdata as $row)
        {
            $po_id.=$row[csf('po_id')].',';
        }
        $po_ids=rtrim($po_id,',');
        if($po_ids!='') $po_ids=$po_ids;else $po_ids=0;
        $yarn_lot_arr=array();
        if($db_type==0)
        {
        $yarn_lot_data=sql_select("select b.po_breakdown_id, a.prod_id, group_concat(distinct(a.yarn_lot)) as yarn_lot from pro_grey_prod_entry_dtls a, order_wise_pro_details b where a.id=b.dtls_id and b.entry_form in(2,22) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.yarn_lot!='' and b.po_breakdown_id in($po_ids)  group by a.prod_id, b.po_breakdown_id");
        }
        else if($db_type==2)
        {
        $yarn_lot_data=sql_select("select b.po_breakdown_id, a.prod_id, listagg(a.yarn_lot,',') within group (order by a.yarn_lot) as yarn_lot from pro_grey_prod_entry_dtls a, order_wise_pro_details b where a.id=b.dtls_id and b.entry_form in(2,22) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.yarn_lot!='0' group by a.prod_id, b.po_breakdown_id");
        }
        foreach($yarn_lot_data as $rows)
        {
            $yarn_lot=explode(",",$rows[csf('yarn_lot')]);
            $yarn_lot_arr[$rows['prod_id']][$rows['po_breakdown_id']]=implode(",",array_unique($yarn_lot));
        }
        unset($yarn_lot_data);
        if($cbo_type==1)
        {
            //echo $cbo_type;
            ?>
            <div style="width:1820px;">
            <fieldset style="width:1820px;">
            <div  align="center"> <strong> <? echo $company_library[$company]; ?> </strong><br><strong> <? echo $search_by_arr[$cbo_type];?> </strong>
            <br>
            <?
                echo  change_date_format($date_from).' '.To.' '.change_date_format($date_to);
            ?>
             </div>
             <table class="rpt_table" width="1920" cellpadding="0" cellspacing="0" border="1" rules="all" align="center" id="table_header_1">
            <thead>
            <tr>
                <th width="30">SL</th>
               <? if($group_by==2 || $group_by==0){ ?>
                 <th width="80">M/C No</th>
                 <? } ?>
                <? if($group_by==1 || $group_by==0){ ?>
                <th width="80">Floor</th>  
                 <? } 
                 ?>
                <th width="50">Shift</th>
                <th width="100">Buyer</th>
                <th width="80">Job</th>
                <th width="100">Booking</th>
                <th width="60">File No</th>
                <th width="70">Ref. No</th>
                <th width="90">Order No</th>
                <th width="100">Fabrics Desc</th>
                <th width="80">Dia/ Width Type</th>
                <th width="70">GSM</th>
                <th width="80">Color Name</th>
                <th width="90">Batch No</th>
                <th width="80">Ext. No</th>
                <th width="80">Batch Qty.</th>
                <th width="70">Prod. Qty.</th>
                <th width="60">Yarn Lot</th>
                <th width="100">Start Date & Time</th>
                <th width="100">End Date & Time</th>
                <th width="70">Time Used</th>
                <th width="80">Remark</th>
                <th width="">Reprocess</th>
            </tr>
            </thead>
            </table>
            <div style=" max-height:380px; width:1920px; overflow-y:scroll;" id="scroll_body">
            <table class="rpt_table" id="table_body" width="1900" cellpadding="0" cellspacing="0" border="1" rules="all" align="center">
            <tbody>
            <? 
            $i=1;$k=1;
            $f=0;
            $btq=0;$tot_prod_btq=0;$grand_tot_prod_btq=$grand_btg=0;
            $batch_chk_arr=array();$group_by_arr=array();$heat_prod_chk_arr=array();$heat_prod_qty=0;
            foreach($batchdata as $batch)
            { 
            if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
            $order_id=$batch[csf('po_id')];
            $color_id=$batch[csf('color_id')];
            $desc=explode(",",$batch[csf('item_description')]); 
            $po_number=implode(",",array_unique(explode(",",$batch[csf('po_number')])));
            if($group_by!=0)
            {
                if($group_by==1)
                {
                    $group_value=$batch[csf('floor_id')];
                    $group_name="Floor";
                    $group_dtls_value=$floor_arr[$batch[csf('floor_id')]];
                }
                else if($group_by==2)
                {
                    $group_value=$batch[csf('machine_id')];
                    $group_name="Machine";
                    $group_dtls_value=$machine_arr[$batch[csf('machine_id')]];
                }
                if (!in_array($group_value,$group_by_arr) )
                    {
                        if($k!=1)
                        { 
                        ?>
                        <tr class="tbl_bottom">
                        <td width="30"></td>
                       <? if($group_by==2 || $group_by==0){ ?>
                         <td width="80"></td>
                         <? } ?>
                        <? if($group_by==1 || $group_by==0){ ?>
                        <td width="80"></td>  
                         <? } 
                         ?>
                        <td width="50"></td>
                        <td width="100"></td>
                        <td width="80"></td>
                        <td width="100"></td>
                        <td width="60"></td>
                        <td width="70"></td>
                        <td width="90"></td>
                        <td width="100"></td>
                        <td width="80"></td>
                        <td width="70"></td>
                        <td width="80"></td>
                        <td width="90"></td>
                        <td width="80">Sub Total</td>
                        <td width="80"><? echo number_format($btq,2);?></td>
                        <td width="70"><? echo number_format($tot_prod_btq,2);?></td>
                        <td width="60"></td>
                        <td width="100"></td>
                        <td width="100"></td>
                        <td width="70"></td>
                        <td width="80"></td>
                        <td width=""></td>
                        <tr bgcolor="#EFEFEF">
                            <td colspan="24" align="left" ><b><? echo $group_name; ?> : <? echo $group_dtls_value; ?></b></td>
                        </tr>
                        <?
                        unset($btq);unset($tot_prod_btq);
                        }
                        $group_by_arr[]=$group_value;            
                        $k++;
                    }
            } 
                $heat_grouping_arr_val=$batch[csf('batch_no')].$batch[csf('machine_id')].$batch[csf('floor_id')];
                $heat_prod_qty=$batch_prod_qty_arr[$batch[csf('id')]][$batch[csf('prod_id')]][$batch[csf('width_dia_type')]]['heat'];
                $prod_qty_ids=$batch[csf('id')].$batch[csf('prod_id')].$batch[csf('width_dia_type')];
                if (!in_array($prod_qty_ids,$heat_prod_chk_arr))
                { //$b++;
                     $heat_prod_chk_arr[]=$prod_qty_ids;
                      $tot_prod_qty=$heat_prod_qty;
                }
                else
                {
                     $tot_prod_qty=0;
                }
                //==End check repeat prod qty 
            ?>
                    <tr bgcolor="<? echo $bgcolor;?>" onClick="change_color('tr<? echo $i;?>','<? echo $bgcolor;?>')" id="tr<? echo $i;?>">
                        <? if (!in_array($heat_grouping_arr_val,$batch_chk_arr) )
                                { $f++;
                                    ?>
                        <td width="30"><? echo $f; ?></td>
                                            
                         <? if($group_by==2 || $group_by==0){ ?>
                        <td  align="center" width="80"><p><? echo $machine_arr[$batch[csf('machine_id')]]; ?></p></td>
                        <?
                         }
                         if($group_by==1 || $group_by==0){ ?>
                       <td width="80"><p><? echo $floor_arr[$batch[csf('floor_id')]]; ?></p></td>
                        <? } ?>
                        <td  align="center" width="50"><p><? echo $shift_name[$batch[csf('shift_name')]]; ?></p></td>
                        <td  width="100" title="<? echo $buyer_arr[$batch[csf('buyer_name')]]; ?>"><p><? echo $buyer_arr[$batch[csf('buyer_name')]]; ?></p></td>
                        <td  width="80" title="<? //echo $color_library[$batch[csf('color_id')]]; ?>"><p><? echo $batch[csf('job_no_prefix_num')]; ?></p></td>
                        <td  width="100"><p><? echo $batch[csf('booking_no')]; ?></p></td>
                        <td  width="60"><p><? echo $batch[csf('file_no')]; ?></p></td>
                        <td  width="70"><p><? echo $batch[csf('grouping')]; ?></p></td>
                        <td width="90"><div style="width:90px; word-wrap:break-word;"><? echo $po_number; ?></div></td>
                            <?  
                            $batch_chk_arr[]=$heat_grouping_arr_val;
                                } 
                                else
                                   { ?>
                        <td width="30"><? //echo $sl; ?></td>
                        <? if($group_by==2 || $group_by==0){ ?>
                        <td  align="center" width="80"><p><? //echo $machine_arr[$batch[csf('machine_id')]]; ?></p></td>
                        <?
                         }
                         if($group_by==1 || $group_by==0){ ?>
                       <td width="80"><p><? //echo $floor_arr[$batch[csf('floor_id')]]; ?></p></td>
                        <? } ?>
                        <td  align="center" width="50"><p><? //echo $machine_id; ?></p></td>
                        <td  width="100"><p><? //echo $buyer_arr[$batch[csf('buyer_name')]]; ?></p></td>
                        <td  width="80" title="<? //echo $color_library[$batch[csf('color_id')]]; ?>"><p><? //echo $color_library[$batch[csf('color_id')]]; ?></p></td>
                        <td  width="100"><p><? //echo $special_row[csf('booking_no')]; ?></p></td>
                        <td  width="60"><p><? //echo $special_row[csf('file_no')]; ?></p></td>
                        <td  width="70"><p><? //echo $special_row[csf('grouping')]; ?></p></td>
                        <td width="90"><p><? //echo $batch[csf('po_number')]; ?></p></td>
                                <? }
                                ?>
                        <td  width="100" title="<? echo $desc[0]; ?>"><p><? echo $batch[csf('item_description')]; ?></p></td>
                        <td  width="80"><div style="width:80px; word-wrap:break-word;"><? echo $fabric_typee[$batch[csf('width_dia_type')]];  ?></div></td>
                        <td  width="70" title="<? echo  $desc[2]; ?>"><p><? echo $desc[2]; ?></p></td>
                        <td  width="80" title="<? echo $color_library[$batch[csf('color_id')]]; ?>"><p><? echo $color_library[$batch[csf('color_id')]]; ?></p></td>
                        <td  align="center" width="90" title="<? echo $batch[csf('batch_no')]; ?>"><p><? echo $batch[csf('batch_no')]; ?></p></td>
                        <td  align="center" width="80" title="<? echo $batch[csf('extention_no')]; ?>"><p><? echo $batch[csf('extention_no')]; ?></p></td>
                        <td align="right" width="80"><p><? echo number_format($batch[csf('batch_qnty')],2);  ?></p></td>
                        <td align="right" width="70" ><? //echo number_format($batch_prod_qty_arr[$batch[csf('id')]][$batch[csf('prod_id')]][$batch[csf('width_dia_type')]]['heat'],2);
                        echo $tot_prod_qty;  ?></td>
                        <td align="left" width="60" title="<? echo $yarn_lot_arr[$batch[csf('prod_id')]][$batch[csf('po_id')]]; ?>"><p><? echo $yarn_lot_arr[$batch[csf('prod_id')]][$batch[csf('po_id')]];  ?></p></td>
                          <td width="100" title="Process Start Date"><div style="width:100px; word-wrap:break-word;"><?  echo change_date_format($batch[csf('process_start_date')]).', '.$batch[csf('start_hours')].':'.$batch[csf('start_minutes')]; ?></div></td>
                        <td width="100" title="Process End Date"><div style="width:100px; word-wrap:break-word;"><?  echo change_date_format($batch[csf('end_date')]).', '.$batch[csf('end_hours')].':'.$batch[csf('end_minutes')]; ?></div></td>
                            <td width="70" align="center"><div style="width:70px; word-wrap:break-word;"><?
                                $end_time=$batch[csf('end_hours')].':'.$batch[csf('end_minutes')];
                                $start_time=$batch[csf('start_hours')].':'.$batch[csf('start_minutes')];
                                
                                $new_date_time_start=($batch[csf('process_start_date')].' '.$start_time.':'.'00');
                                $new_date_time_end=($batch[csf('end_date')].' '.$end_time.':'.'00');
                                $total_time=datediff(n,$new_date_time_start,$new_date_time_end);
                                echo floor($total_time/60).":".$total_time%60; ?></div></td>
                         <td  align="center" width="80" title="<? echo $batch[csf('remarks')]; ?>"><p><? echo $batch[csf('remarks')]; ?></p></td>
                        <td align="center"  title="<? if($batch[csf('batch_against')]==2) echo $batch_against[$batch[csf('batch_against')]]; ?>"><p><?  if($batch[csf('batch_against')]==2) echo $batch_against[$batch[csf('batch_against')]];?> </p></td>
                    </tr>
                    <? 
                    $i++;
                    $btq+=$batch[csf('batch_qnty')];
                    $grand_btq+=$batch[csf('batch_qnty')];
                    $tot_prod_btq+=$tot_prod_qty;
                    $grand_tot_prod_btq+=$tot_prod_qty;
                   } //batchdata froeach
                        if($group_by!=0)
                        { 
                        ?>
                        <tr class="tbl_bottom">
                        <td width="30"></td>
                       <? if($group_by==2 || $group_by==0){ ?>
                         <td width="80"></td>
                         <? } ?>
                        <? if($group_by==1 || $group_by==0){ ?>
                        <td width="80"></td>  
                         <? } 
                         ?>
                        <td width="50"></td>
                        <td width="100"></td>
                        <td width="80"></td>
                        <td width="100"></td>
                        <td width="60"></td>
                        <td width="70"></td>
                        <td width="90"></td>
                        <td width="100"></td>
                        <td width="80"></td>
                        <td width="70"></td>
                        <td width="80"></td>
                        <td width="90"></td>
                        <td width="80">Sub Total</td>
                        <td width="80"><? echo number_format($btq,2);?></td>
                        <td width="70"><? echo number_format($tot_prod_btq,2);?></td>
                        <td width="60"></td>
                        <td width="100"></td>
                        <td width="100"></td>
                        <td width="70"></td>
                        <td width="80"></td>
                        <td width=""></td>
                        <?
                        //unset($btq);unset($tot_prod_btq);
                        }
             ?>
             
            </tbody>
            </table>

             <table class="rpt_table" width="1900" cellpadding="0" cellspacing="0" border="1" rules="all" align="center" id="report_table_footer">
            <tfoot>
            <tr>
                <th width="30">&nbsp;</th>
                 <? if($group_by==2 || $group_by==0){ ?>
                    <th width="80">&nbsp;</th>
                    <? } ?>
                    <? if($group_by==1 || $group_by==0){ ?>
                    <th width="80">&nbsp;</th> 
                    <? } 
                    ?>
                <th width="50">&nbsp;</th>
                <th width="100">&nbsp;</th>
                <th width="80">&nbsp;</th>
                <th width="100">&nbsp;</th>
                <th width="60">&nbsp;</th>
                <th width="70">&nbsp;</th>
                <th width="90">&nbsp;</th>
                <th width="100">&nbsp;</th>
                <th width="80">&nbsp;</th>
                <th width="70">&nbsp;</th>
                <th width="80">&nbsp;</th>
                <th width="90">&nbsp;</th>
                <th width="80">&nbsp;</th>
                <th width="80"><? echo number_format($grand_btq,2); ?></th>
                <th width="70"><? echo number_format($grand_tot_prod_btq,2); ?></th>
                <th width="60">&nbsp;</th>
                <th width="100">&nbsp;</th>
                <th width="100">&nbsp;</th>
                <th width="70">&nbsp;</th>
                <th width="80">&nbsp;</th>
                <th>&nbsp;</th>
            </tr>
            </tfoot>
            </table>

            </div>
            </fieldset>
            </div>
            <?
        }
        else if($cbo_type==2)// Slitting/Squeezing
        {
            //echo $cbo_type;
            ?>
            <div>
            <fieldset style="width:1765px;">
            <div style="float: left;"><strong> <? echo $company_library[$company]; ?> </strong><br> <strong><? echo $search_by_arr[$cbo_type];?> </strong><br>
            <?
                echo  change_date_format($date_from).' '.To.' '.change_date_format($date_to);
            ?>
             </div>
             <table class="rpt_table" width="1835" cellpadding="0" cellspacing="0" border="1" rules="all" style="float: left;" id="table_header_1">
            <thead>
            <tr>
                <th width="30">SL</th>
                 <? if($group_by==2 || $group_by==0){ ?>
                 <th width="80">M/C No</th>
                 <? } ?>
                <? if($group_by==1 || $group_by==0){ ?>
                <th width="80">Floor</th>  
                 <? } 
                 ?>
                <th width="50">Shift</th>
                <th width="100">Buyer</th>
                <th width="80">Job</th>  
                <th width="100">Booking</th>  
                <th width="60">File No</th>
                <th width="70">Ref. No</th>
                <th width="90">Order No</th>
                <th width="100">Fabrics Desc</th>
                <th width="70">GSM</th>
                <th width="75">Dia/Width Type</th>
                <th width="80">Color Name</th>
                <th width="90">Batch No</th>
                <th width="40">Extn. No</th>
                <th width="70">Batch Qty.</th>
                <th width="70">Prod. Qty.</th>
                <th width="50">Lot No</th>
                <th width="75">Start Date & Time</th>
                <th width="75">End Date & Time</th>
                <th width="70">Time Used</th>
                <th width="60">Remark</th>
                <th>Reprocess</th>
            </tr>
            </thead>
            </table>
            <div style=" max-height:350px; width:1835px; overflow-y:scroll;float: left;" id="scroll_body">
            <table class="rpt_table" id="table_body" width="1815" cellpadding="0" cellspacing="0" border="1" rules="all" style="float: left;">
            <tbody>
            <? 
            $i=1;$k=1;
            $f=0;
            $btq=0;$tot_prod_btq=$grand_tot_prod_btq=$grand_btq=0;
            $batch_chk_arr=array();
            foreach($batchdata as $batch)
            { 
            if ($i%2==0)  
            $bgcolor="#E9F3FF";
            else
            $bgcolor="#FFFFFF";
            $order_id=$batch[csf('po_id')];
            $color_id=$batch[csf('color_id')];
            $desc=explode(",",$batch[csf('item_description')]); 
            $po_number=implode(",",array_unique(explode(",",$batch[csf('po_number')]))); 
            if($group_by!=0)
            {
                if($group_by==1)
                {
                    $group_value=$batch[csf('floor_id')];
                    $group_name="Floor";
                    $group_dtls_value=$floor_arr[$batch[csf('floor_id')]];
                }
                
                else if($group_by==2)
                {
                    $group_value=$batch[csf('machine_id')];
                    $group_name="Machine";
                    $group_dtls_value=$machine_arr[$batch[csf('machine_id')]];
                }
                if (!in_array($group_value,$group_by_arr) )
                    {
                        if($k!=1)
                        { 
                        ?>  
                        <tr class="tbl_bottom">
                        <td width="30"></td>
                         <? if($group_by==2 || $group_by==0){ ?>
                         <td width="80">&nbsp;</td>
                         <? } ?>
                        <? if($group_by==1 || $group_by==0){ ?>
                        <td width="80">&nbsp;</td>  
                         <? } 
                         ?>
                        <td width="50">&nbsp;</td>
                        <td width="100">&nbsp;</td>
                        <td width="80">&nbsp;</th>  
                        <td width="100">&nbsp;</th>  
                        <td width="60">&nbsp;</td>
                        <td width="70">&nbsp;</td>
                        <td width="90">&nbsp;</td>
                        <td width="100">&nbsp;</td>
                        <td width="70">&nbsp;</td>
                        <td width="75">&nbsp;</td>
                        <td width="80">&nbsp;</td>
                       
                        <td colspan="2" align="right">Sub Total</td>
                        <td width="70"><?  echo number_format($btq,2);?></td>
                        <td width="70"><?  echo number_format($tot_prod_btq,2);?></td>
                        <td width="50">&nbsp;</td>
                        <td width="75">&nbsp;</td>
                        <td width="75">&nbsp;</td>
                        <td width="70">&nbsp;</td>
                        <td width="60">&nbsp;</td>
                        <td>&nbsp;</td>
                        </tr>
                
                        <tr bgcolor="#EFEFEF">
                            <td colspan="24" align="left" ><b><? echo $group_name; ?> : <? echo $group_dtls_value; ?></b></td>
                        </tr>
                        <?
                        unset($btq);unset($tot_prod_btq);
                        }
                        $group_by_arr[]=$group_value;            
                        $k++;
                    }
            } 
            ?>
            <tr bgcolor="<? echo $bgcolor;?>" onClick="change_color('tr<? echo $i;?>','<? echo $bgcolor;?>')" id="tr<? echo $i;?>">
                  <? if (!in_array($batch[csf('batch_no')],$batch_chk_arr) )
                        { $f++;
                            ?>
                <td width="30"><? echo $f; ?></td>
                <? if($group_by==2 || $group_by==0){ ?>
                 <td  align="center" width="80" title="<? echo $machine_arr[$batch[csf('machine_id')]]; ?>"><p><? echo $machine_arr[$batch[csf('machine_id')]]; ?></p></td>
                <?
                 }
                 if($group_by==1 || $group_by==0){ ?>
               <td width="80"><p><? echo $floor_arr[$batch[csf('floor_id')]]; ?></p></td>
                <? } ?>
                
                 <td  align="center" width="50"><p><? echo $shift_name[$batch[csf('shift_name')]]; ?></p></td>
                <td  width="100" title="<? echo $buyer_arr[$batch[csf('buyer_name')]]; ?>"><p><? echo $buyer_arr[$batch[csf('buyer_name')]]; ?></p></td>
                <td  width="80" title="<? //echo $color_library[$batch[csf('color_id')]]; ?>"><p><? echo $batch[csf('job_no_prefix_num')]; ?></p></td>
                <td  width="100"><p><? echo $batch[csf('booking_no')]; ?></p></td>
                <td  width="60"><p><? echo $batch[csf('file_no')]; ?></p></td>
                <td  width="70"><p><? echo $batch[csf('grouping')]; ?></p></td>
                <td width="90"><div style="width:90px; word-wrap:break-word;"><? echo $po_number; ?></div></td>
                    <?  
                    $batch_chk_arr[]=$batch[csf('batch_no')];
                        } 
                        else
                           { ?>
                <td width="30"><? //echo $sl; ?></td>
                <? if($group_by==2 || $group_by==0){ ?>
                 <td  align="center" width="80"><p><? //echo $machine_arr[$batch[csf('machine_id')]]; ?></p></td>
                <?
                 }
                 if($group_by==1 || $group_by==0){ ?>
               <td width="80"><p><? //echo $floor_arr[$batch[csf('floor_id')]]; ?></p></td>
                <? } ?>
                <td  align="center" width="50"><p><? //echo $machine_id; ?></p></td>
                <td  width="100"><p><? //echo $buyer_arr[$batch[csf('buyer_name')]]; ?></p></td>
                <td  width="80" title="<? //echo $color_library[$batch[csf('color_id')]]; ?>"><p><? //echo $color_library[$batch[csf('color_id')]]; ?></p></td>
                <td  width="100"><p><? //echo $special_row[csf('booking_no')]; ?></p></td>
                <td  width="60"><p><? //echo $special_row[csf('file_no')]; ?></p></td>
                <td  width="70"><p><? //echo $special_row[csf('grouping')]; ?></p></td>
                <td width="90"><p><? //echo $batch[csf('po_number')]; ?></p></td>
                        <? }
                        ?>
                <td  width="100" title="<? echo $desc[0]; ?>"><p><? echo $batch[csf('item_description')]; ?></p></td>
                <td  width="70" title="<? echo   $desc[2]; ?>"><p><? echo  $desc[2]; ?></p></td>
                <td width="75"><p><? echo $fabric_typee[$batch[csf('width_dia_type')]];;?></p></td>
                <td  width="80"><p><? echo $color_library[$batch[csf('color_id')]]; ?></p></td>
                <td  align="center" width="90" title="<? echo $batch[csf('batch_no')]; ?>"><p><? echo $batch[csf('batch_no')]; ?></p></td>
                <td  align="center" width="40"><p><? echo $batch[csf('extention_no')]; ?></p></td>
                <td align="right" width="70"><? echo number_format($batch[csf('batch_qnty')],2);  ?></td>
                <td align="right" width="70" >
                    <? 
                        echo number_format($batch_prod_qty_arr[$batch[csf('id')]][$batch[csf('prod_id')]][$batch[csf('width_dia_type')]]['sliting'],2);  
                    ?>
                    </td>
                <td align="left" width="50" title="<? echo $yarn_lot_arr[$batch[csf('prod_id')]][$batch[csf('po_id')]]; ?>"><p><? echo $yarn_lot_arr[$batch[csf('prod_id')]][$batch[csf('po_id')]];  ?></p></td>
               <td width="75" title="Process Start Date"><div style="width:75px; word-wrap:break-word;"><?  echo change_date_format($batch[csf('process_start_date')]).', '.$batch[csf('start_hours')].':'.$batch[csf('start_minutes')]; ?></div></td>
                <td width="75" title="Process End Date"><div style="width:75px; word-wrap:break-word;"><?  echo change_date_format($batch[csf('end_date')]).', '.$batch[csf('end_hours')].':'.$batch[csf('end_minutes')]; ?></div></td>
                 <td width="70" align="center"><div style="width:70px; word-wrap:break-word;"><?
                        $end_time=$batch[csf('end_hours')].':'.$batch[csf('end_minutes')];
                        $start_time=$batch[csf('start_hours')].':'.$batch[csf('start_minutes')];
                        $new_date_time_start=($batch[csf('process_start_date')].' '.$start_time.':'.'00');
                        $new_date_time_end=($batch[csf('end_date')].' '.$end_time.':'.'00');
                        $total_time=datediff(n,$new_date_time_start,$new_date_time_end);
                        echo floor($total_time/60).":".$total_time%60; ?></div></td>
                <td align="center" width="60"><p><?   echo $batch[csf('remarks')]; ?></p>
                 </td>
                <td align="center" title="<?   if($batch[csf('batch_against')]==2) echo $batch_against[$batch[csf('batch_against')]]; ?>"><p><?  if($batch[csf('batch_against')]==2) echo $batch_against[$batch[csf('batch_against')]]; ?></p> </td>
            </tr>
            <? 
            $i++;
            $btq+=$batch[csf('batch_qnty')];
            //if ($roll_maintained==1 && $cbo_type!=0) {
             $tot_prod_btq+=$batch_prod_qty_arr[$batch[csf('id')]][$batch[csf('prod_id')]][$batch[csf('width_dia_type')]]['sliting'];  
            $grand_btq+=$batch[csf('batch_qnty')];
              //  if ($roll_maintained==1 && $cbo_type!=0) {
                    $grand_tot_prod_btq+=$batch_prod_qty_arr[$batch[csf('id')]][$batch[csf('prod_id')]][$batch[csf('width_dia_type')]]['sliting'];
            } //batchdata froeach

                if($group_by!=0)
                        { 
                        ?>  
                        <tr class="tbl_bottom">
                        <td width="30"></td>
                         <? if($group_by==2 || $group_by==0){ ?>
                         <td width="80">&nbsp;</td>
                         <? } ?>
                        <? if($group_by==1 || $group_by==0){ ?>
                        <td width="80">&nbsp;</td>  
                         <? } 
                         ?>
                        <td width="50">&nbsp;</td>
                        <td width="100">&nbsp;</td>
                        <td width="80">&nbsp;</th>  
                        <td width="100">&nbsp;</th>  
                        <td width="60">&nbsp;</td>
                        <td width="70">&nbsp;</td>
                        <td width="90">&nbsp;</td>
                        <td width="100">&nbsp;</td>
                        <td width="70">&nbsp;</td>
                        <td width="75">&nbsp;</td>
                        <td width="80">&nbsp;</td>
                        <td  colspan="2">Sub Total</td>
                        <td width="70" align="right"><?  echo number_format($btq,2);?></td>
                        <td width="70" align="right"><?  echo number_format($tot_prod_btq,2);?></td>
                        <td width="50">&nbsp;</td>
                        <td width="75">&nbsp;</td>
                        <td width="75">&nbsp;</td>
                        <td width="70">&nbsp;</td>
                        <td width="60">&nbsp;</td>
                        <td>&nbsp;</td>
                        </tr>
                        <?
                        }
                
             ?>
                </tbody>
            </table>
            <table class="rpt_table" width="1815" cellpadding="0" cellspacing="0" border="1" rules="all" style="float: left;" id="report_table_footer">
            <tfoot>
            <tr>
                <th width="30">&nbsp;</th>
                 <? if($group_by==2 || $group_by==0){ ?>
                    <th width="80">&nbsp;</th>
                    <? } ?>
                    <? if($group_by==1 || $group_by==0){ ?>
                    <th width="80">&nbsp;</th> 
                    <? } 
                    ?>
                <th width="50">&nbsp;</th>
                <th width="100">&nbsp;</th>
                <th width="80">&nbsp;</th>
                <th width="100">&nbsp;</th>
                <th width="60">&nbsp;</th> 
                <th width="70">&nbsp;</th> 
                <th width="90">&nbsp;</th>
                <th width="100">&nbsp;</th>
                <th width="70">&nbsp;</th>
                <th width="75">&nbsp;</th>
                <th width="80">&nbsp;</th>
                <th width="90">&nbsp;</th>
                <th width="40">&nbsp;</th>
                <th width="70"><? echo number_format($grand_btq,2); ?></th>
                <th width="70"><? echo number_format($grand_tot_prod_btq,2); ?></th>
                <th width="50">&nbsp;</th>
                <th width="75">&nbsp;</th>
                <th width="75">&nbsp;</th>
                <th width="70">&nbsp;</th>
                <th width="60">&nbsp;</th>
                <th>&nbsp;</th>
            </tr>
            </tfoot>
            </table>
            </div>
            </fieldset>
            </div>
            <?
        }
        else if($cbo_type==3)// Drying Stentering
        {
            ?>
            <div>
            <fieldset style="width:1735px;">
            <div align="center"><strong> <? echo $company_library[$company]; ?> </strong><br> <strong><? echo $search_by_arr[$cbo_type];?> </strong><br>
            <?
                echo  change_date_format($date_from).' '.To.' '.change_date_format($date_to);
            ?>
             </div>
             <table class="rpt_table" width="1835" cellpadding="0" cellspacing="0" border="1" rules="all" align="center" id="table_header_1">
            <thead>
            <tr>
                <th width="30">SL</th>
            <? if($group_by==2 || $group_by==0){ ?>
                <th width="80">Machine</th>
                <? } ?>
                <? if($group_by==1 || $group_by==0){ ?>
                <th width="80">Floor</th> 
                <? } 
                ?> 
                <th width="50">Shift</th>
                <th width="100">Buyer</th>
                <th width="80">Job</th> 
                <th width="100">Booking</th> 
                <th width="60">File No</th> 
                <th width="70">Ref. No</th>
                <th width="90">Order No</th>
                <th width="100">Fabrics Desc</th>
                <th width="75">GSM</th>
                <th width="70">Dia/ Width Type</th>
                <th width="80">Color Name</th>
                <th width="90">Batch No</th>
                <th width="40">Extn. No</th>
                <th width="70">Batch Qty.</th>
                <th width="70">Prod Qty.</th>
                <th width="50">Lot No</th> 
                <th width="75">Start Date & Time</th> 
                <th width="75">End Date & Time</th>
                <th width="70">Time Used</th>
                <th width="60">Remark</th>
                <th>Reprocess</th>
            </tr>
            </thead>
            </table>
            <div style=" max-height:350px; width:1835px; overflow-y:scroll;;" id="scroll_body">
            <table class="rpt_table" id="table_body" width="1815" cellpadding="0" cellspacing="0" border="1" rules="all" align="center">
            <tbody>
            <? 
            $i=1;
            $f=0;$k=1;
            $btq=0;$tot_prod_btq=$grand_btq=$grand_tot_prod_btq=0;
            $batch_chk_arr=array();
            foreach($batchdata as $batch) 
            { 
            if ($i%2==0)  
            $bgcolor="#E9F3FF";
            else
            $bgcolor="#FFFFFF";
            $order_id=$batch[csf('po_id')];
            $color_id=$batch[csf('color_id')];
            $desc=explode(",",$batch[csf('item_description')]); 
            $po_number=implode(",",array_unique(explode(",",$batch[csf('po_number')])));
            if($group_by!=0)
            {
                if($group_by==1)
                {
                    $group_value=$batch[csf('floor_id')];
                    $group_name="Floor";
                    $group_dtls_value=$floor_arr[$batch[csf('floor_id')]];
                }
                
                else if($group_by==2)
                {
                    $group_value=$batch[csf('machine_id')];
                    $group_name="Machine";
                    $group_dtls_value=$machine_arr[$batch[csf('machine_id')]];
                }
                if (!in_array($group_value,$group_by_arr) )
                    {
                        if($k!=1)
                        { 
                        ?>  
                        <tr class="tbl_bottom">
                        <td width="30">&nbsp;</td>
                    <? if($group_by==2 || $group_by==0){ ?>
                        <td width="80">&nbsp;</td>
                        <? } ?>
                        <? if($group_by==1 || $group_by==0){ ?>
                        <td width="80">&nbsp;</td> 
                        <? } 
                        ?> 
                        <td width="50">&nbsp;</td>
                        <td width="100">&nbsp;</td>
                        <td width="80">&nbsp;</td> 
                        <td width="100">&nbsp;</td> 
                        <td width="60">&nbsp;</td> 
                        <td width="70">&nbsp;</td>
                        <td width="90">&nbsp;</td>
                        <td width="100">&nbsp;</td>
                        <td width="75">&nbsp;</td>
                        <td width="70"></td>
                        <td width="80"></td>
                        <td  colspan="2" align="right">Sub Total</td>
                        <td width="70"><? echo number_format($btq,2); ?></td>
                        <td width="70"><? echo number_format($tot_prod_btq,2); ?></td>
                        <td width="50">&nbsp;</td> 
                        <td width="75">&nbsp;</td> 
                        <td width="75">&nbsp;</td>
                        <td width="70">&nbsp;</td>
                        <td width="60">&nbsp;</td>
                        <td>&nbsp;</td>
                        </tr>
                        <tr bgcolor="#EFEFEF">
                            <td colspan="24" align="left" ><b><? echo $group_name; ?> : <? echo $group_dtls_value; ?></b></td>
                        </tr>
                        <?
                            unset($btq);unset($tot_prod_btq);
                        }
                        $group_by_arr[]=$group_value;            
                        $k++;
                    }
            }  
            ?>
            <tr bgcolor="<? echo $bgcolor;?>" onClick="change_color('tr<? echo $i;?>','<? echo $bgcolor;?>')" id="tr<? echo $i;?>">
                      <? if (!in_array($batch[csf('batch_no')],$batch_chk_arr) )
                        { $f++;
                            ?>
                <td width="30"><? echo $f; ?></td>
                
                 <? if($group_by==2 || $group_by==0){ ?>
               <td  align="center" width="80" ><p><? echo $machine_arr[$batch[csf('machine_id')]]; ?></p></td>
                <?
                 }
                 if($group_by==1 || $group_by==0){ ?>
               <td width="80"><p><? echo $floor_arr[$batch[csf('floor_id')]]; ?></p></td>
                <? } ?>
                 <td  align="center" width="50"><p><? echo $shift_name[$batch[csf('shift_name')]]; ?></p></td>
                <td  width="100" title="<? echo $buyer_arr[$batch[csf('buyer_name')]]; ?>"><p><? echo $buyer_arr[$batch[csf('buyer_name')]]; ?></p></td>
                <td  width="80" title="<? //echo $color_library[$batch[csf('color_id')]]; ?>"><p><? echo $batch[csf('job_no_prefix_num')]; ?></p></td>
                <td  width="100"><p><? echo $batch[csf('booking_no')]; ?></p></td>
                <td  width="60"><p><? echo $batch[csf('file_no')]; ?></p></td>
                <td  width="70"><p><? echo $batch[csf('grouping')]; ?></p></td>
                <td width="90"><div style="width:90px; word-wrap:break-word;"><? echo $po_number; ?></div></td>
                    <?  
              $batch_chk_arr[]=$batch[csf('batch_no')];
                  } 
                 else
                  { ?>
                <td width="30"><? //echo $sl; ?></td>
                 <? if($group_by==2 || $group_by==0){ ?>
               <td  align="center" width="80" ><p><? //echo $machine_arr[$batch[csf('machine_id')]]; ?></p></td>
                <?
                 }
                 if($group_by==1 || $group_by==0){ ?>
               <td width="80"><p><? //echo $floor_arr[$batch[csf('floor_id')]]; ?></p></td>
                <? } ?>
                <td  align="center" width="50"><p><? //echo $machine_id; ?></p></td>
                <td  width="100"><p><? //echo $buyer_arr[$batch[csf('buyer_name')]]; ?></p></td>
                <td  width="80" title="<? //echo $color_library[$batch[csf('color_id')]]; ?>"><p><? //echo $color_library[$batch[csf('color_id')]]; ?></p></td>
                <td  width="100"><p><? //echo $special_row[csf('booking_no')]; ?></p></td>
                <td  width="60"><p><? //echo $special_row[csf('file_no')]; ?></p></td>
                <td  width="70"><p><? //echo $special_row[csf('grouping')]; ?></p></td>
                <td width="90"><p><? //echo $batch[csf('po_number')]; ?></p></td>
               <? }
                ?>
                <td  width="100" title="<? echo $desc[0]; ?>"><p><? echo $batch[csf('item_description')]; ?></p></td>
                <td  width="75" title="<? echo   $desc[2]; ?>"><p><? echo  $desc[2]; ?></p></td>
                <td  width="70" title="<? ?>"><p><? echo $fabric_typee[$batch[csf('width_dia_type')]];;?></p></td>
                <td  width="80" title="<? echo $color_library[$batch[csf('color_id')]]; ?>"><p><? echo $color_library[$batch[csf('color_id')]]; ?></p></td>
                <td  align="center" width="90" title="<? echo $batch[csf('batch_no')]; ?>"><p><? echo $batch[csf('batch_no')]; ?></p></td>
                <td  align="center" width="40" title="<? echo $batch[csf('extention_no')]; ?>"><p><? echo $batch[csf('extention_no')]; ?></p></td>
                <td align="right" width="70" title="<? echo $batch[csf('batch_qnty')];  ?>"><? echo number_format($batch[csf('batch_qnty')],2);  ?></td>
                   <td align="right" width="70" >
                    <?
                         echo number_format($batch_prod_qty_arr[$batch[csf('id')]][$batch[csf('prod_id')]][$batch[csf('width_dia_type')]]['drying'],2);
                     ?>
                     </td>
                <td align="left" width="50" title="<? echo $yarn_lot_arr[$batch[csf('prod_id')]][$batch[csf('po_id')]]; ?>"><p><? echo $yarn_lot_arr[$batch[csf('prod_id')]][$batch[csf('po_id')]];  ?></p></td>
               <td width="75" title="Process Start Date"><div style="width:75px; word-wrap:break-word;"><?  echo change_date_format($batch[csf('process_start_date')]).', '.$batch[csf('start_hours')].':'.$batch[csf('start_minutes')]; ?></div></td>
                <td width="75" title="Process End Date"><div style="width:75px; word-wrap:break-word;"><?  echo change_date_format($batch[csf('end_date')]).', '.$batch[csf('end_hours')].':'.$batch[csf('end_minutes')]; ?></div></td>
                 <td width="70" align="center"><div style="width:70px; word-wrap:break-word;"><?
                        $end_time=$batch[csf('end_hours')].':'.$batch[csf('end_minutes')];
                        $start_time=$batch[csf('start_hours')].':'.$batch[csf('start_minutes')];
                        
                        $new_date_time_start=($batch[csf('process_start_date')].' '.$start_time.':'.'00');
                        $new_date_time_end=($batch[csf('end_date')].' '.$end_time.':'.'00');
                        $total_time=datediff(n,$new_date_time_start,$new_date_time_end);
                        echo floor($total_time/60).":".$total_time%60; ?></div></td>
                <td align="center" width="60"><p> <? echo $batch[csf('remarks')]; ?>
                    </p>
                 </td>
                <td align="center" title="<?   if($batch[csf('batch_against')]==2) echo $batch_against[$batch[csf('batch_against')]]; ?>"><p><?  if($batch[csf('batch_against')]==2) echo $batch_against[$batch[csf('batch_against')]]; ?></p> </td>
            </tr>
            <? 
            $i++;
            $btq+=$batch[csf('batch_qnty')];
            //if ($roll_maintained==1 && $cbo_type!=0) {
             $tot_prod_btq+=$batch_prod_qty_arr[$batch[csf('id')]][$batch[csf('prod_id')]][$batch[csf('width_dia_type')]]['drying'];
            $grand_btq+=$batch[csf('batch_qnty')];
               // if ($roll_maintained==1 && $cbo_type!=0) {
             $grand_tot_prod_btq+=$batch_prod_qty_arr[$batch[csf('id')]][$batch[csf('prod_id')]][$batch[csf('width_dia_type')]]['drying'];
            } //batchdata froeach
                        if($group_by!=0)
                        {
                 ?>
                         <tr class="tbl_bottom">
                        <td width="30">&nbsp;</td>
                    <? if($group_by==2 || $group_by==0){ ?>
                        <td width="80">&nbsp;</td>
                        <? } ?>
                        <? if($group_by==1 || $group_by==0){ ?>
                        <td width="80">&nbsp;</td> 
                        <? } 
                        ?> 
                        <td width="50">&nbsp;</td>
                        <td width="100">&nbsp;</td>
                        <td width="80">&nbsp;</td> 
                        <td width="100">&nbsp;</td> 
                        <td width="60">&nbsp;</td> 
                        <td width="70">&nbsp;</td>
                        <td width="90">&nbsp;</td>
                        <td width="100">&nbsp;</td>
                        <td width="75">&nbsp;</td>
                        <td width="70">&nbsp;</td>
                        <td width="80">&nbsp;</td>
                        <td  colspan="2" align="right">Sub Total</td>
                     
                        <td width="70"><? echo number_format($btq,2); ?></td>
                        <td width="70"><? echo number_format($tot_prod_btq,2); ?></td>
                        <td width="50">&nbsp;</td> 
                        <td width="75">&nbsp;</td> 
                        <td width="75">&nbsp;</td>
                        <td width="70">&nbsp;</td>
                        <td width="60">&nbsp;</td>
                        <td>&nbsp;</td>
                        </tr>
                        <?
                        }
                        ?>
                </tbody>
            </table>
            <table class="rpt_table" width="1815" cellpadding="0" cellspacing="0" border="1" rules="all" align="center" id="report_table_footer">
            <tfoot>
            <tr>
                <th width="30">&nbsp;</th>
               <? if($group_by==2 || $group_by==0){ ?>
                <th width="80">&nbsp;</th>
                <? } ?>
                <? if($group_by==1 || $group_by==0){ ?>
                <th width="80">&nbsp;</th> 
                <? } 
                ?> 
                <th width="50">&nbsp;</th>
                <th width="100">&nbsp;</th>
                <th width="80">&nbsp;</th>
                <th width="100">&nbsp;</th>
                <th width="60">&nbsp;</th>
                <th width="70">&nbsp;</th>
                <th width="90">&nbsp;</th>
                <th width="100">&nbsp;</th>
                <th width="75">&nbsp;</th>
                <th width="70">&nbsp;</th>
                <th width="80">&nbsp;</th>
                <th  width="90">Grand Total</th>
                <th width="40">&nbsp;</th>
                <th width="70"><? echo number_format($grand_btq,2); ?></th>
                <th width="70"><? echo number_format($grand_tot_prod_btq,2); ?></th>
                <th width="50">&nbsp;</th>
                <th width="75">&nbsp;</th>
                <th width="75">&nbsp;</th>
                <th width="70">&nbsp;</th>
                <th width="60">&nbsp;</th>
                <th>&nbsp;</th>
            </tr>
            </tfoot>
            </table>
            </div>
            </fieldset>
            </div>
            <?
        }
        else if($cbo_type==9)// Stentering
        {
            ?>
            <div>
            <fieldset style="width:1840px;">
            <div align="center"><strong> <? echo $company_library[$company]; ?> </strong><br> <strong><? echo $search_by_arr[$cbo_type];?> </strong><br>
            <?
                echo  change_date_format($date_from).' '.To.' '.change_date_format($date_to);
            ?>
             </div>
            <table class="rpt_table" width="1860" cellpadding="0" cellspacing="0" border="1" rules="all" align="center" id="table_header_1">
                <thead>
                    <tr>
                        <th width="30">SL</th>
                         <? if($group_by==2 || $group_by==0){ ?>
                        <th width="80">M/C No</th>
                        <? } ?>
                        <? if($group_by==1 || $group_by==0){ ?>
                        <th width="80">Floor</th> 
                        <? } 
                        ?> 
                        <th width="50">Shift</th>
                        <th width="100">Buyer</th>
                        <th width="80">Job</th>
                        <th width="100">Booking</th>
                        <th width="60">File No</th> 
                        <th width="70">Ref. No</th>
                        <th width="90">Order No</th>
                        <th width="100">Fabrics Desc</th>
                        <th width="75">GSM</th>
                        <th width="70">Dia/ Width Type</th>
                        <th width="80">Color Name</th>
                        <th width="90">Batch No</th>
                        <th width="40">Extn. No</th>
                        <th width="70">Batch Qty.</th>
                        <th width="70">Prod. Qty.</th>
                        <th width="50">Lot No</th>
                        <th width="100">Start Date & Time</th>
                        <th width="100">End Date & Time</th>
                        <th width="70">Time Used</th>
                        <th width="60">Remark</th>
                        <th>Reprocess</th>
                    </tr>
                </thead>
            </table>
            <div style=" max-height:350px; width:1860px; overflow-y:scroll;;" id="scroll_body">
                <table class="rpt_table" id="table_body" width="1840" cellpadding="0" cellspacing="0" border="1" rules="all" align="center">
                    <tbody>
                        <? 
                        $i=1;$f=0;$k=1;
                        $btq=0;$tot_prod_btq=$grand_btq=$grand_tot_prod_btq=0;
                        $batch_chk_arr=array();$group_by_arr=array();
                        /*echo "<pre>";
                        print_r($batchdata);*/
                        foreach($batchdata as $batch)
                        { 
                            if ($i%2==0)  
                            $bgcolor="#E9F3FF";
                            else
                            $bgcolor="#FFFFFF";
                            $order_id=$batch[csf('po_id')];
                            $color_id=$batch[csf('color_id')];
                            $desc=explode(",",$batch[csf('item_description')]); 
                            $po_number=implode(",",array_unique(explode(",",$batch[csf('po_number')])));
                            if($group_by!=0)
                            {
                                if($group_by==1)
                                {
                                    $group_value=$batch[csf('floor_id')];
                                    $group_name="Floor";
                                    $group_dtls_value=$floor_arr[$batch[csf('floor_id')]];
                                }
                                
                                else if($group_by==2)
                                {
                                    $group_value=$batch[csf('machine_id')];
                                    $group_name="Machine";
                                    $group_dtls_value=$machine_arr[$batch[csf('machine_id')]];//
                                }
                                if (!in_array($group_value,$group_by_arr) )
                                {
                                    if($k!=1)
                                    {
                                        ?>
                                        <tr class="tbl_bottom">
                                            <td width="30">&nbsp;</td>
                                            <? if($group_by==2 || $group_by==0){ ?>
                                            <td width="80">&nbsp;</td>
                                            <? } ?>
                                            <? if($group_by==1 || $group_by==0){ ?>
                                            <td width="80">&nbsp;</td> 
                                            <? } 
                                            ?> 
                                            <td width="50">&nbsp;</td>
                                            <td width="100">&nbsp;</td>
                                            <td width="80">&nbsp;</td>
                                            <td width="100">&nbsp;</td>
                                            <td width="60">&nbsp;</td> 
                                            <td width="70">&nbsp;</td>
                                            <td width="90">&nbsp;</td>
                                            <td width="100">&nbsp;</td>
                                            <td width="75">&nbsp;</td>
                                            <td width="70">&nbsp;</td>
                                            <td width="80">&nbsp;</td>
                                            <td width="130" colspan="2">Sub Total</td>
                                          
                                            <td width="70"><? echo number_format($btq,2);?></td>
                                            <td width="70"><? echo number_format($tot_prod_btq,2)?></td>
                                            <td width="50">&nbsp;</td>
                                            <td width="100">&nbsp;</td>
                                            <td width="100">&nbsp;</td>
                                            <td width="70">&nbsp;</td>
                                            <td width="60">&nbsp;</td>
                                            <td>&nbsp;</td>
                                        </tr>   
                                        <tr bgcolor="#EFEFEF">
                                            <td colspan="24" align="left" ><b><? echo $group_name; ?> : <? echo $group_dtls_value; ?></b></td>
                                        </tr>
                                        <?
                                        unset($btq);unset($tot_prod_btq);
                                    }
                                    $group_by_arr[]=$group_value; 
                                    $k++;
                                }
                            }
                            $stenter_grouping_arr_val=$batch[csf('batch_no')].$batch[csf('machine_id')].$batch[csf('floor_id')]; 
                            ?>
                            <tr bgcolor="<? echo $bgcolor;?>" onClick="change_color('tr<? echo $i;?>','<? echo $bgcolor;?>')" id="tr<? echo $i;?>">
                                <? if (!in_array($stenter_grouping_arr_val,$batch_chk_arr) )
                                { 
                                    $f++;
                                    ?>
                                    <td width="30"><? echo $f; ?></td>
                                    <? if($group_by==2 || $group_by==0){ ?>
                                    <td  align="center" width="80"><p><? echo $machine_arr[$batch[csf('machine_id')]]; ?></p></td>
                                    <?}
                                    if($group_by==1 || $group_by==0){ ?>
                                    <td width="80"><p><? echo $floor_arr[$batch[csf('floor_id')]]; ?></p></td>
                                    <? } ?>
                                    <td  align="center" width="50"><p><? echo $shift_name[$batch[csf('shift_name')]]; ?></p></td>
                                    <td  width="100" title="<? echo $buyer_arr[$batch[csf('buyer_name')]]; ?>"><p><? echo $buyer_arr[$batch[csf('buyer_name')]]; ?></p></td>
                                    <td  width="80" title="<? //echo $color_library[$batch[csf('color_id')]]; ?>"><p><? echo $batch[csf('job_no_prefix_num')]; ?></p></td>
                                    <td  width="100"><p><? echo $batch[csf('booking_no')]; ?></p></td>
                                    <td  width="60"><p><? echo $batch[csf('file_no')]; ?></p></td>
                                    <td  width="70"><p><? echo $batch[csf('grouping')]; ?></p></td>
                                    <td width="90"><div style="width:90px; word-wrap:break-word;"><? echo $po_number; ?></div></td>
                                    <?  $batch_chk_arr[]=$stenter_grouping_arr_val;
                                } 
                                else
                                {
                                    ?>
                                    <td width="30"><? //echo $sl; ?></td>
                                    <? if($group_by==2 || $group_by==0){ ?>
                                    <td  align="center" width="80"><p><? //echo $machine_arr[$batch[csf('machine_id')]]; ?></p></td>
                                    <?
                                     }
                                     if($group_by==1 || $group_by==0){ ?>
                                   <td width="80"><p><? //echo $floor_arr[$batch[csf('floor_id')]]; ?></p></td>
                                    <? } ?>
                                    <td  align="center" width="50"><p><? //echo $machine_id; ?></p></td>
                                    <td  width="100"><p><? //echo $buyer_arr[$batch[csf('buyer_name')]]; ?></p></td>
                                    <td  width="80" title="<? //echo $color_library[$batch[csf('color_id')]]; ?>"><p><? //echo $color_library[$batch[csf('color_id')]]; ?></p></td>
                                    <td  width="100"><p><? //echo $special_row[csf('booking_no')]; ?></p></td>
                                    <td  width="60"><p><? //echo $special_row[csf('file_no')]; ?></p></td>
                                    <td  width="70"><p><? //echo $special_row[csf('grouping')]; ?></p></td>
                                    <td width="90"><p><? //echo $po_number;//echo $batch[csf('po_number')]; ?></p></td>
                                    <? 
                                }
                                ?>
                                <td  width="100" title="<? echo $desc[0]; ?>"><p><? echo $batch[csf('item_description')]; ?></p></td>
                                <td  width="75" title="<? echo   $desc[2]; ?>"><p><? echo  $desc[2]; ?></p></td>
                                <td  width="70" title="<? ?>"><p><? echo $fabric_typee[$batch[csf('width_dia_type')]];;?></p></td>
                                <td  width="80" title="<? echo $color_library[$batch[csf('color_id')]]; ?>"><p><? echo $color_library[$batch[csf('color_id')]]; ?></p></td>
                                <td  align="center" width="90" title="<? echo $batch[csf('batch_no')]; ?>"><p><? echo $batch[csf('batch_no')]; ?></p></td>
                                <td  align="center" width="40" title="<? echo $batch[csf('extention_no')]; ?>"><p><? echo $batch[csf('extention_no')]; ?></p></td>
                                <td align="right" width="70" title="<? echo $batch[csf('batch_qnty')];  ?>"><p><? echo number_format($batch[csf('batch_qnty')],2);  ?></p></td>
                                <td align="right" width="70" >
                                    <? 
                                        echo number_format($batch_prod_qty_arr[$batch[csf('id')]][$batch[csf('prod_id')]][$batch[csf('width_dia_type')]]['stenter'],2); 
                                    ?>
                                </td>
                                <td align="left" width="50" title="<? echo $yarn_lot_arr[$batch[csf('prod_id')]][$batch[csf('po_id')]]; ?>"><p><? echo $yarn_lot_arr[$batch[csf('prod_id')]][$batch[csf('po_id')]];  ?></p></td>
                               <td width="100" title="Process Start Date"><div style="width:100px; word-wrap:break-word;"><?  echo change_date_format($batch[csf('process_start_date')]).', '.$batch[csf('start_hours')].':'.$batch[csf('start_minutes')]; ?></div></td>
                                <td width="100" title="Process End Date"><div style="width:100px; word-wrap:break-word;"><?  echo change_date_format($batch[csf('end_date')]).', '.$batch[csf('end_hours')].':'.$batch[csf('end_minutes')]; ?></div></td>
                                <td width="70" align="center"><div style="width:70px; word-wrap:break-word;"><?
                                    $end_time=$batch[csf('end_hours')].':'.$batch[csf('end_minutes')];
                                    $start_time=$batch[csf('start_hours')].':'.$batch[csf('start_minutes')];
                                    $new_date_time_start=($batch[csf('process_start_date')].' '.$start_time.':'.'00');
                                    $new_date_time_end=($batch[csf('end_date')].' '.$end_time.':'.'00');
                                    $total_time=datediff(n,$new_date_time_start,$new_date_time_end);
                                        echo floor($total_time/60).":".$total_time%60; ?></div></td>
                                <td align="center" width="60"><p> <? echo $batch[csf('remarks')]; ?>
                                    </p>
                                 </td>
                                <td align="center" title="<?   if($batch[csf('batch_against')]==2) echo $batch_against[$batch[csf('batch_against')]]; ?>"><p><?  if($batch[csf('batch_against')]==2) echo $batch_against[$batch[csf('batch_against')]]; ?></p></td>
                            </tr>
                            <? 
                            $i++;
                            $btq+=$batch[csf('batch_qnty')];

                            $tot_prod_btq+=$batch_prod_qty_arr[$batch[csf('id')]][$batch[csf('prod_id')]][$batch[csf('width_dia_type')]]['stenter']; 
                            $grand_btq+=$batch[csf('batch_qnty')];
                            $grand_tot_prod_btq+=$batch_prod_qty_arr[$batch[csf('id')]][$batch[csf('prod_id')]][$batch[csf('width_dia_type')]]['stenter']; 
                        } //batchdata froeach

                        if($group_by!=0)
                        {
                            ?>
                            <tr class="tbl_bottom">
                                 <td width="30">&nbsp;</td>
                                 <? if($group_by==2 || $group_by==0){ ?>
                                <td width="80">&nbsp;</td>
                                <? } ?>
                                <? if($group_by==1 || $group_by==0){ ?>
                                <td width="80">&nbsp;</td> 
                                <? } 
                                ?> 
                                <td width="50">&nbsp;</td>
                                <td width="100">&nbsp;</td>
                                <td width="80">&nbsp;</td>
                                <td width="100">&nbsp;</td>
                                <td width="60">&nbsp;</td> 
                                <td width="70">&nbsp;</td>
                                <td width="90">&nbsp;</td>
                                <td width="100">&nbsp;</td>
                                <td width="75">&nbsp;</td>
                                <td width="70">&nbsp;</td>
                                <td width="80">&nbsp;</td>
                                <td width="130" colspan="2">Sub Total</td>
                            
                                <td width="70"><? echo number_format($btq,2);?></td>
                                <td width="70"><? echo number_format($tot_prod_btq,2)?></td>
                                <td width="50">&nbsp;</td>
                                <td width="100">&nbsp;</td>
                                <td width="100">&nbsp;</td>
                                <td width="70">&nbsp;</td>
                                <td width="60">&nbsp;</td>
                                <td>&nbsp;</td>
                            </tr>   
                            <?
                        }
                        ?>
                    </tbody>
                    <tfoot>
                        <tr>
                            <th width="30">&nbsp;</th>
                             <? if($group_by==2 || $group_by==0){ ?>
                            <th width="80"></th>
                            <? } ?>
                            <? if($group_by==1 || $group_by==0){ ?>
                            <th width="80"></th> 
                            <? } 
                            ?> 
                            <th width="50">&nbsp;</th>
                            <th width="100">&nbsp;</th>
                            <th width="80">&nbsp;</th>
                            <th width="100">&nbsp;</th>
                            <th width="60">&nbsp;</th>
                            <th width="70">&nbsp;</th>
                            <th width="90">&nbsp;</th>
                            <th width="100">&nbsp;</th>
                            <th width="75">&nbsp;</th>
                            <th width="70">&nbsp;</th>
                            <th width="80">&nbsp;</th>
                            <th width="130" colspan="2">Grand Total</th>
                          
                            <th width="70"><? echo number_format($grand_btq,2); ?></th>
                            <th width="70"><? echo number_format($grand_tot_prod_btq,2); ?></th>
                            <th width="50">&nbsp;</th>
                            <th width="100">&nbsp;</th>
                            <th width="100">&nbsp;</th>
                            <th width="70">&nbsp;</th>
                            <th width="60">&nbsp;</th>
                            <th>&nbsp;</th>
                        </tr>
                    </tfoot>
                </table>
            </div>
            </fieldset>
            </div>
            <?
        }
        else if($cbo_type==11)// Re Stentering
        {
            ?>
            <div>
            <fieldset style="width:1880px;">
            <div align="center"><strong> <? echo $company_library[$company]; ?> </strong><br> <strong><? echo $search_by_arr[$cbo_type];?> </strong><br>
                <?
                echo  change_date_format($date_from).' '.To.' '.change_date_format($date_to);
                ?>
            </div>
            <table class="rpt_table" width="1880" cellpadding="0" cellspacing="0" border="1" rules="all" id="table_header_1" >
                <thead>
                    <tr>
                        <th width="30">SL</th>
                         <? if($group_by==2 || $group_by==0){ ?>
                        <th width="80">M/C No</th>
                        <? } ?>
                        <? if($group_by==1 || $group_by==0){ ?>
                        <th width="80">Floor</th> 
                        <? } 
                        ?> 
                        
                        <th width="50">Shift</th>
                        <th width="100">Buyer</th>
                        <th width="80">Job</th>
                        <th width="100">Booking</th>
                        <th width="60">File No</th> 
                        <th width="70">Ref. No</th>
                        <th width="90">Order No</th>
                        <th width="120">Fabrics Desc</th>
                        <th width="75">GSM</th>
                        <th width="70">Dia/ Width Type</th>
                        <th width="80">Color Name</th>
                        <th width="90">Batch No</th>
                        <th width="40">Extn. No</th>
                        <th width="70">Batch Qty.</th>
                        <th width="70">Prod. Qty.</th>
                        <th width="50">Lot No</th>
                        <th width="100">Start Date & Time</th>
                        <th width="100">End Date & Time</th>
                        <th width="70">Time Used</th>
                        <th width="60">Reprocess</th>
                        <th>Remark</th>
                    </tr>
                </thead>
            </table>
            <div style=" max-height:350px; width:1880px; overflow-y:scroll;;" id="scroll_body">
            <table class="rpt_table" id="table_body" width="1860" cellpadding="0" cellspacing="0" border="1" rules="all" >
                <tbody>
                    <? 
                    $i=1;$f=0;$k=1;
                    $btq=0;$tot_prod_btq=$grand_btq=$grand_tot_prod_btq=0;
                    $batch_chk_arr=array();$group_by_arr=array();
                    foreach($batchdata as $batch)
                    { 
                        if ($i%2==0)  
                        $bgcolor="#E9F3FF";
                        else
                        $bgcolor="#FFFFFF";
                        $order_id=$batch[csf('po_id')];
                        $color_id=$batch[csf('color_id')];
                        $desc=explode(",",$batch[csf('item_description')]); 
                        $po_number=implode(",",array_unique(explode(",",$batch[csf('po_number')])));
                        if($group_by!=0)
                        {
                            if($group_by==1)
                            {
                                $group_value=$batch[csf('floor_id')];
                                $group_name="Floor";
                                $group_dtls_value=$floor_arr[$batch[csf('floor_id')]];
                            }
                            
                            else if($group_by==2)
                            {
                                $group_value=$batch[csf('machine_id')];
                                $group_name="Machine";
                                $group_dtls_value=$machine_arr[$batch[csf('machine_id')]];//
                            }
                            if (!in_array($group_value,$group_by_arr) )
                            {
                                if($k!=1)
                                {
                                    ?>  
                                    <tr class="tbl_bottom">
                                        <td width="30">&nbsp;</td>
                                        <? if($group_by==2 || $group_by==0){ ?>
                                        <td width="80">&nbsp;</td>
                                        <? } ?>
                                        <? if($group_by==1 || $group_by==0){ ?>
                                        <td width="80">&nbsp;</td> 
                                        <? } 
                                        ?> 
                                        <td width="50">&nbsp;</td>
                                        <td width="100">&nbsp;</td>
                                        <td width="80">&nbsp;</td>
                                        <td width="100">&nbsp;</td>
                                        <td width="60">&nbsp;</td>
                                        <td width="70">&nbsp;</td>
                                        <td width="90">&nbsp;</td>
                                        <td width="120">&nbsp;</td>
                                        <td width="75">&nbsp;</td>
                                        <td width="70">&nbsp;</td>
                                        <td width="80">&nbsp;</td>
                                        <td width="90">Sub Total</td>
                                        <td width="40">&nbsp;</td>
                                        <td width="70"> <? echo number_format($btq,2); ?></td>
                                        <td width="70"><? echo number_format($tot_prod_btq,2); ?></td>
                                        <td width="50">&nbsp;</td>
                                        <td width="100">&nbsp;</td>
                                        <td width="100">&nbsp;</td> 
                                        <td width="70">&nbsp;</td>
                                        <td width="60">&nbsp;</td>
                                        <td>&nbsp;</td>
                                    </tr>
                                   
                                    <?
                                    unset($btq);unset($tot_prod_btq);
                                }

                                ?>
                                <tr bgcolor="#EFEFEF">
                                    <td colspan="24" align="left" ><b><? echo $group_name; ?> : <? echo $group_dtls_value; ?></b></td>
                                </tr>
                                <?
                                $group_by_arr[]=$group_value; 
                                $k++; 
                            }
                        }
                        $stenter_grouping_arr_val=$batch[csf('batch_no')].$batch[csf('machine_id')].$batch[csf('floor_id')]; 
                        ?>
                        <tr bgcolor="<? echo $bgcolor;?>" onClick="change_color('tr<? echo $i;?>','<? echo $bgcolor;?>')" id="tr<? echo $i;?>">
                            <?
                            if (!in_array($stenter_grouping_arr_val,$batch_chk_arr) )
                            {
                                $f++;
                                ?>
                                <td width="30"><? echo $f; ?></td>
                                <? if($group_by==2 || $group_by==0){ ?>
                                <td  align="center" width="80"><p><? echo $machine_arr[$batch[csf('machine_id')]]; ?></p></td>
                                <?
                                }
                                if($group_by==1 || $group_by==0)
                                {
                                    ?>
                                    <td width="80"><p><? echo $floor_arr[$batch[csf('floor_id')]]; ?></p></td>
                                    <? 
                                } ?>
                                 <td  align="center" width="50"><p><? echo $shift_name[$batch[csf('shift_name')]]; ?></p></td>
                                <td  width="100" style="word-wrap:break-word" title="<? echo $buyer_arr[$batch[csf('buyer_name')]]; ?>"><p><? echo $buyer_arr[$batch[csf('buyer_name')]]; ?></p></td>
                                <td  width="80" title="<? //echo $color_library[$batch[csf('color_id')]]; ?>"><p><? echo $batch[csf('job_no_prefix_num')]; ?></p></td>
                                <td  width="100"><p><? echo $batch[csf('booking_no')]; ?></p></td>
                                <td  width="60"><p><? echo $batch[csf('file_no')]; ?></p></td>
                                <td  width="70" style="word-wrap:break-word"><p><? echo $batch[csf('grouping')]; ?></p></td>
                                <td width="90"><div style="width:90px; word-wrap:break-word;"><? echo $po_number; ?></div></td>
                                <?  
                                $batch_chk_arr[]=$stenter_grouping_arr_val;
                            }
                            else
                            {
                                ?>
                                <td width="30"><? //echo $sl; ?></td>
                                <? 
                                if($group_by==2 || $group_by==0)
                                { ?>
                                    <td  align="center" width="80"><p><? //echo $machine_arr[$batch[csf('machine_id')]]; ?></p>  </td>
                                    <?
                                }
                                if($group_by==1 || $group_by==0)
                                {
                                    ?>
                                    <td width="80"><p><? //echo $floor_arr[$batch[csf('floor_id')]]; ?></p></td>
                                    <? 
                                }?>
                                <td  align="center" width="50"><p><? //echo $machine_id; ?></p></td>
                                <td  width="100"><p><? //echo $buyer_arr[$batch[csf('buyer_name')]]; ?></p></td>
                                <td  width="80" title="<? //echo $color_library[$batch[csf('color_id')]]; ?>"><p><? //echo $color_library[$batch[csf('color_id')]]; ?></p></td>
                                <td  width="100"><p><? //echo $special_row[csf('booking_no')]; ?></p></td>
                                <td  width="60"><p><? //echo $special_row[csf('file_no')]; ?></p></td>
                                <td  width="70"><p><? //echo $special_row[csf('grouping')]; ?></p></td>
                                <td width="90"><p><? //echo $batch[csf('po_number')]; ?></p></td>
                                <? 
                            }
                            ?>
                            <td  width="120" style="word-wrap:break-word" title="<? echo $desc[0]; ?>"><p><? echo $batch[csf('item_description')]; ?></p></td>
                            <td  width="75" title="<? echo   $desc[2]; ?>"><p><? echo  $desc[2]; ?></p></td>
                            <td  width="70" title="<? ?>"><p><? echo $fabric_typee[$batch[csf('width_dia_type')]];;?></p></td>
                            <td  width="80"  title="<? echo $color_library[$batch[csf('color_id')]]; ?>"><p><? echo $color_library[$batch[csf('color_id')]]; ?></p></td>
                            <td  align="center" width="90" style="word-wrap:break-word" title="<? echo $batch[csf('batch_no')]; ?>"><p><? echo $batch[csf('batch_no')]; ?></p></td>
                            <td  align="center" width="40" title="<? echo $batch[csf('extention_no')]; ?>"><p><? echo $batch[csf('extention_no')]; ?></p></td>
                            <td align="right" width="70" title="<? echo $batch[csf('batch_qnty')];  ?>"><p><? echo number_format($batch[csf('batch_qnty')],2);  ?></p></td>
                             <td align="right" width="70" ><? echo number_format($batch_prod_qty_arr2[$batch[csf('id')]][$batch[csf('prod_id')]][$batch[csf('width_dia_type')]]['stenter'],2);  ?></td>
                            <td align="left" width="50" title="<? echo $yarn_lot_arr[$batch[csf('prod_id')]][$batch[csf('po_id')]]; ?>"><p><? echo $yarn_lot_arr[$batch[csf('prod_id')]][$batch[csf('po_id')]];  ?></p></td>
                            <td width="100" title="Process Start Date"><div style="width:100px; word-wrap:break-word;"><?  echo change_date_format($batch[csf('process_start_date')]).', '.$batch[csf('start_hours')].':'.$batch[csf('start_minutes')]; ?></div></td>
                            <td width="100" title="Process End Date"><div style="width:100px; word-wrap:break-word;"><?  echo change_date_format($batch[csf('end_date')]).', '.$batch[csf('end_hours')].':'.$batch[csf('end_minutes')]; ?></div></td>
                            <td width="70" align="center"><div style="width:70px; word-wrap:break-word;"><?
                            $end_time=$batch[csf('end_hours')].':'.$batch[csf('end_minutes')];
                            $start_time=$batch[csf('start_hours')].':'.$batch[csf('start_minutes')];
                            $new_date_time_start=($batch[csf('process_start_date')].' '.$start_time.':'.'00');
                            $new_date_time_end=($batch[csf('end_date')].' '.$end_time.':'.'00');
                            $total_time=datediff(n,$new_date_time_start,$new_date_time_end);
                            echo floor($total_time/60).":".$total_time%60; ?></div></td>
                            <td width="60" align="center" title="<?   echo $batch[csf('re_stenter_no')]; ?>"><p><?
                                if($batch[csf('re_stenter_no')]>0) echo 'Re Stenter'; ?></p> </td>
                            <td align="center" ><p> <? echo $batch[csf('remarks')]; ?> </p> </td>
                        </tr>
                        <? 
                        $i++;
                        $btq+=$batch[csf('batch_qnty')];
                        $tot_prod_btq+=$batch_prod_qty_arr2[$batch[csf('id')]][$batch[csf('prod_id')]][$batch[csf('width_dia_type')]]['stenter'];
                        $grand_btq+=$batch[csf('batch_qnty')];
                        $grand_tot_prod_btq+=$batch_prod_qty_arr2[$batch[csf('id')]][$batch[csf('prod_id')]][$batch[csf('width_dia_type')]]['stenter'];
                    } //batchdata froeach
                    if($group_by!=0)
                    {
                        ?>  
                        <tr class="tbl_bottom">
                            <td width="30">&nbsp;</td>
                            <? if($group_by==2 || $group_by==0){ ?>
                            <td width="80">&nbsp;</td>
                            <? } ?>
                            <? if($group_by==1 || $group_by==0){ ?>
                            <td width="80">&nbsp;</td> 
                            <? } 
                            ?> 
                            <td width="50">&nbsp;</td>
                            <td width="100">&nbsp;</td>
                            <td width="80">&nbsp;</td>
                            <td width="100">&nbsp;</td>
                            <td width="60">&nbsp;</td>
                            <td width="70">&nbsp;</td>
                            <td width="90">&nbsp;</td>
                            <td width="120">&nbsp;</td>
                            <td width="75">&nbsp;</td>
                            <td width="70">&nbsp;</td>
                            <td width="80">&nbsp;</td>
                            <td width="90">Sub Total</td>
                            <td width="40">&nbsp;</td>
                            <td width="70"> <? echo number_format($btq,2); ?></td>
                            <td width="70"><? echo number_format($tot_prod_btq,2); ?></td>
                            <td width="50">&nbsp;</td>
                            <td width="100">&nbsp;</td>
                            <td width="100">&nbsp;</td> 
                            <td width="70">&nbsp;</td>
                            <td width="60">&nbsp;</td>
                            <td>&nbsp;</td>
                        </tr>
                        <?
                    }
                    ?>
                </tbody>
            </table>

            <table class="rpt_table" width="1860" cellpadding="0" cellspacing="0" border="1" rules="all" align="center" id="report_table_footer">
                <tfoot>
                    <tr>
                        <th width="30">&nbsp;</th>
                         <? if($group_by==2 || $group_by==0){ ?>
                        <th width="80"></th>
                        <? } ?>
                        <? if($group_by==1 || $group_by==0){ ?>
                        <th width="80"></th> 
                        <? } 
                        ?> 
                        <th width="50">&nbsp;</th>
                        <th width="100">&nbsp;</th>
                        <th width="80">&nbsp;</th>
                        <th width="100">&nbsp;</th>
                        <th width="60">&nbsp;</th>
                        <th width="70">&nbsp;</th>
                        <th width="90">&nbsp;</th>
                        <th width="120">&nbsp;</th>
                        <th width="75">&nbsp;</th>
                        <th width="70">&nbsp;</th>
                        <th width="80">&nbsp;</th>
                        <th width="90">Grand Total</th>
                        <th width="40">&nbsp;</th>
                        <th width="70"><? echo number_format($grand_btq,2); ?></th>
                        <th width="70"><? echo number_format($grand_tot_prod_btq,2); ?></th>
                        <th width="50">&nbsp;</th>
                        <th width="100">&nbsp;</th>
                        <th width="100">&nbsp;</th>
                        <th width="70">&nbsp;</th>
                        
                        <th  width="60">&nbsp;</th>
                        <th>&nbsp;</th>
                    </tr>
                </tfoot>
            </table>
            </div>
            </fieldset>
            </div>
            <?
        }
        else if($cbo_type==12)//  Re Compacting
        {
            ?>
            <div>
            <fieldset style="width:1855px;">
            <div align="center"><strong> <? echo $company_library[$company]; ?> </strong><br> <strong><? echo $search_by_arr[$cbo_type];?> </strong><br>
            <?
                echo  change_date_format($date_from).' '.To.' '.change_date_format($date_to);
            ?>
            </div>
            <table class="rpt_table" width="1855" cellpadding="0" cellspacing="0" border="1" rules="all" align="center" id="table_header_1">
                <thead>
                    <tr>
                        <th width="30">SL</th>
                        <? if($group_by==2 || $group_by==0){ ?>
                        <th width="80">M/C No</th>
                        <? } ?>
                        <? if($group_by==1 || $group_by==0){ ?>
                        <th width="80">Floor</th> 
                        <? } 
                        ?> 
                        <th width="50">Shift</th>
                        <th width="100">Buyer</th>
                        <th width="80">Job</th>
                        <th width="100">Booking</th>
                        <th width="60">File No</th>
                        <th width="70">Ref. no</th>
                        <th width="90">Order No</th>
                        <th width="120">Fabrics Desc</th>
                        <th width="75">GSM</th>
                        <th width="70">Dia/Width Type</th>
                        <th width="80">Color Name</th>
                        <th width="90">Batch No</th>
                        <th width="40">Extn. No</th>
                        <th width="70">Batch Qty.</th>
                        <th width="70">Prod. Qty.</th>
                        <th width="50">Lot No</th>
                        <th width="75">Start Date & Time</th>
                        <th width="75">End Date & Time</th> 
                        <th width="70">Time Used</th>
                        <th width="60">Reprocess</th>
                        <th>Remark</th>
                    </tr>
                </thead>
            </table>
            <div style=" max-height:350px; width:1855px; overflow-y:scroll;;" id="scroll_body">
            <table class="rpt_table" id="table_body" width="1835" cellpadding="0" cellspacing="0" border="1" rules="all" align="center">
            <tbody>
            <? 
            $i=1;
            $f=0;$k=1;
            $btq=0;$tot_prod_btq=$grand_btq=$grand_tot_prod_btq=0;
            $batch_chk_arr=array();$group_by_arr=array();

            foreach($batchdata as $batch)
            {
                if ($i%2==0)  
                $bgcolor="#E9F3FF";
                else
                $bgcolor="#FFFFFF";
                $order_id=$batch[csf('po_id')];
                $color_id=$batch[csf('color_id')];
                $desc=explode(",",$batch[csf('item_description')]); 
                $po_id=implode(",",array_unique(explode(",",$batch[csf('po_id')]))); 
                $po_number=implode(",",array_unique(explode(",",$batch[csf('po_number')]))); 
                if($group_by!=0)
                {
                    if($group_by==1)
                    {
                        $group_value=$batch[csf('floor_id')];
                        $group_name="Floor";
                        $group_dtls_value=$floor_arr[$batch[csf('floor_id')]];
                    }
                    else if($group_by==2)
                    {
                        $group_value=$batch[csf('machine_id')];
                        $group_name="Machine";
                        $group_dtls_value=$machine_arr[$batch[csf('machine_id')]];//
                    }
                    if (!in_array($group_value,$group_by_arr) )
                    {
                        if($k!=1)
                        {
                            ?>  
                            <tr class="tbl_bottom">
                                <td width="30">&nbsp;</td>
                                <? if($group_by==2 || $group_by==0){ ?>
                                <td width="80">&nbsp;</td>
                                <? } ?>
                                <? if($group_by==1 || $group_by==0){ ?>
                                <td width="80">&nbsp;</td> 
                                <? } 
                                ?> 
                                <td width="50">&nbsp;</td>
                                <td width="100">&nbsp;</td>
                                <td width="80">&nbsp;</td>
                                <td width="100">&nbsp;</td>
                                <td width="60">&nbsp;</td>
                                <td width="70">&nbsp;</td>
                                <td width="90">&nbsp;</td>
                                <td width="120">&nbsp;</td>
                                <td width="75">&nbsp;</td>
                                <td width="70">&nbsp;</td>
                                <td width="80">&nbsp;</td>
                                <td width="90">Sub Total</td>
                                <td width="40">&nbsp;</td>
                                <td width="70"> <? echo number_format($btq,2); ?></td>
                                <td width="70"><? echo number_format($tot_prod_btq,2); ?></td>
                                <td width="50">&nbsp;</td>
                                <td width="75">&nbsp;</td>
                                <td width="75">&nbsp;</td> 
                                <td width="70">&nbsp;</td>
                                <td width="60">&nbsp;</td>
                                <td>&nbsp;</td>
                            </tr>
                           
                            <?
                            unset($btq);unset($tot_prod_btq);
                        }
                        ?>  
                        <tr bgcolor="#EFEFEF">
                            <td colspan="23" align="left" ><b><? echo $group_name; ?> : <? echo $group_dtls_value; ?></b></td>
                        </tr>
                        <?
                        $group_by_arr[]=$group_value;
                        $k++;
                    }
                } 
                $grouping_arr_val=$batch[csf('batch_no')].$batch[csf('machine_id')].$batch[csf('floor_id')];
                ?>
                <tr bgcolor="<? echo $bgcolor;?>" onClick="change_color('tr<? echo $i;?>','<? echo $bgcolor;?>')" id="tr<? echo $i;?>">
                    <? if (!in_array($grouping_arr_val,$batch_chk_arr) )
                    {
                        $f++;
                        ?>
                        <td width="30"><? echo $f; ?></td>
                       
                        <? if($group_by==2 || $group_by==0){ ?>
                        <td  align="center" width="80"><div style="width:80px; word-wrap:break-word;"><? echo $machine_arr[$batch[csf('machine_id')]]; ?></div></td>
                        <?
                        }
                        if($group_by==1 || $group_by==0){ ?>
                        <td width="80"><div style="width:80px; word-wrap:break-word;"><? echo $floor_arr[$batch[csf('floor_id')]]; ?></div></td>
                        <? } ?>
                         <td  align="center" width="50"><p><? echo $shift_name[$batch[csf('shift_name')]]; ?></p></td>
                        <td  width="100" title="<? echo $buyer_arr[$batch[csf('buyer_name')]]; ?>"><p><? echo $buyer_arr[$batch[csf('buyer_name')]]; ?></p></td>
                        <td  width="80" title="<? //echo $color_library[$batch[csf('color_id')]]; ?>"><p><? echo $batch[csf('job_no_prefix_num')]; ?></p></td>
                        <td  width="100"><p><? echo $batch[csf('booking_no')]; ?></p></td>
                        <td  width="60"><p><? echo $batch[csf('file_no')]; ?></p></td>
                        <td  width="70"><p><? echo $batch[csf('grouping')]; ?></p></td>
                        <td width="90"><div style="width:90px; word-wrap:break-word;"><? echo $po_number; ?></div></td>
                        <?  
                        $batch_chk_arr[]=$grouping_arr_val;
                    }
                    else
                    { ?>
                        <td width="30"><? //echo $sl; ?></td>
                        <? if($group_by==2 || $group_by==0){ ?>
                        <td  align="center" width="80"><p><? //echo $machine_arr[$batch[csf('machine_id')]]; ?></p></td>
                        <?
                        }
                        if($group_by==1 || $group_by==0){ ?>
                        <td width="80"><p><? //echo $floor_arr[$batch[csf('floor_id')]]; ?></p></td>
                        <? } ?>
                        <td  align="center" width="50"><p><? //echo $machine_id; ?></p></td>
                        <td  width="100"><p><? //echo $buyer_arr[$batch[csf('buyer_name')]]; ?></p></td>
                        <td  width="80" title="<? //echo $color_library[$batch[csf('color_id')]]; ?>"><p><? //echo $color_library[$batch[csf('color_id')]]; ?></p></td>
                        <td  width="100"><p><? //echo $special_row[csf('booking_no')]; ?></p></td>
                        <td  width="60"><p><? //echo $special_row[csf('file_no')]; ?></p></td>
                        <td  width="70"><p><? //echo $special_row[csf('grouping')]; ?></p></td>
                        <td width="90"><p><? //echo $batch[csf('po_number')]; ?></p></td>
                        <? 
                    }
                    ?>
                    <td  width="120" style="word-wrap:break-word" title="<? echo $desc[0]; ?>"><p><? echo $batch[csf('item_description')]; ?></p></td>
                    <td  width="75" title="<? echo   $desc[2]; ?>"><p><? echo  $desc[2]; ?></p></td>
                    <td width="70" title="<? ?>"><p><? echo $fabric_typee[$batch[csf('width_dia_type')]];;?></p></td>
                    <td  width="80" style="word-wrap:break-word" title="<? echo $color_library[$batch[csf('color_id')]]; ?>"><p><? echo $color_library[$batch[csf('color_id')]]; ?></p></td>
                    <td  align="center" width="90" title="<? echo $batch[csf('batch_no')]; ?>"><p><? echo $batch[csf('batch_no')]; ?></p></td>
                    <td  align="center" width="40" title="<? echo $batch[csf('extention_no')]; ?>"><p><? echo $batch[csf('extention_no')]; ?></p></td>
                    <td align="right" width="70" title="<? echo $batch[csf('batch_qnty')];  ?>"><? echo number_format($batch[csf('batch_qnty')],2);  ?></td>
                    <td align="right" width="70" ><? echo number_format($batch_prod_qty_arr2[$batch[csf('id')]][$batch[csf('prod_id')]][$batch[csf('width_dia_type')]]['compact'],2);  ?></td>
                    <td align="left" width="50" title="<? echo $yarn_lot_arr[$batch[csf('prod_id')]][$batch[csf('po_id')]]; ?>"><p><?
                    $yarn_lot='';
                    foreach($po_id as $pid)
                    {
                        if($yarn_lot=='') $yarn_lot=$yarn_lot_arr[$batch[csf('prod_id')]][$pid];else  $yarn_lot.=",".$yarn_lot_arr[$batch[csf('prod_id')]][$pid];
                    }
                    echo $yarn_lot;//$yarn_lot_arr[$batch[csf('prod_id')]][$batch[csf('po_id')]]; 
                    ?></p></td>
                    <td width="75" title="Process Start Date"><div style="width:75px; word-wrap:break-word;"><?  echo change_date_format($batch[csf('process_start_date')]).', '.$batch[csf('start_hours')].':'.$batch[csf('start_minutes')]; ?></div></td>
                    <td width="75" title="Process End Date"><div style="width:75px; word-wrap:break-word;"><?  echo change_date_format($batch[csf('end_date')]).', '.$batch[csf('end_hours')].':'.$batch[csf('end_minutes')]; ?></div></td>
                    <td width="70" align="center"><div style="width:70px; word-wrap:break-word;"><?
                        $end_time=$batch[csf('end_hours')].':'.$batch[csf('end_minutes')];
                        $start_time=$batch[csf('start_hours')].':'.$batch[csf('start_minutes')];
                        $new_date_time_start=($batch[csf('process_start_date')].' '.$start_time.':'.'00');
                        $new_date_time_end=($batch[csf('end_date')].' '.$end_time.':'.'00');
                        $total_time=datediff(n,$new_date_time_start,$new_date_time_end);
                        echo floor($total_time/60).":".$total_time%60; ?></div></td>
                    <td width="60" align="center"><p><?  if($batch[csf('re_stenter_no')]>0) echo 'Re Compact'; ?></p> </td>
                    <td align="center" ><p> <?   echo $batch[csf('remarks')];?> </p>
                     </td>
                </tr>
                <? 
                $i++;
                $btq+=$batch[csf('batch_qnty')];
                $tot_prod_btq+=$batch_prod_qty_arr2[$batch[csf('id')]][$batch[csf('prod_id')]][$batch[csf('width_dia_type')]]['compact'];
                $grand_btq+=$batch[csf('batch_qnty')];
                $grand_tot_prod_btq+=$batch_prod_qty_arr2[$batch[csf('id')]][$batch[csf('prod_id')]][$batch[csf('width_dia_type')]]['compact'];
            } //batchdata froeach
            if($group_by!=0)
            {
                ?>  
                <tr class="tbl_bottom">
                    <td width="30">&nbsp;</td>
                    <? if($group_by==2 || $group_by==0){ ?>
                    <td width="80">&nbsp;</td>
                    <? } ?>
                    <? if($group_by==1 || $group_by==0){ ?>
                    <td width="80">&nbsp;</td> 
                    <? } 
                    ?> 
                    <td width="50">&nbsp;</td>
                    <td width="100">&nbsp;</td>
                    <td width="80">&nbsp;</td>
                    <td width="100">&nbsp;</td>
                    <td width="60">&nbsp;</td>
                    <td width="70">&nbsp;</td>
                    <td width="90">&nbsp;</td>
                    <td width="120">&nbsp;</td>
                    <td width="75">&nbsp;</td>
                    <td width="70">&nbsp;</td>
                    <td width="80">&nbsp;</td>
                    <td width="90">Sub Total</td>
                    <td width="40">&nbsp;</td>
                    <td width="70"> <? echo number_format($btq,2); ?></td>
                    <td width="70"><? echo number_format($tot_prod_btq,2); ?></td>
                    <td width="50">&nbsp;</td>
                    <td width="75">&nbsp;</td>
                    <td width="75">&nbsp;</td> 
                    <td width="70">&nbsp;</td>
                    <td width="60">&nbsp;</td>
                    <td>&nbsp;</td>
                </tr>
                <?
            }
            ?>
                </tbody>
            </table>
            <table class="rpt_table" width="1835" cellpadding="0" cellspacing="0" border="1" rules="all" align="center" id="report_table_footer">
                <tfoot>
                    <tr>
                        <th width="30">&nbsp;</th>
                        <? if($group_by==2 || $group_by==0){ ?>
                        <th width="80">&nbsp;</th>
                        <? } ?>
                        <? if($group_by==1 || $group_by==0){ ?>
                        <th width="80">&nbsp;</th> 
                        <? } 
                        ?> 
                        <th width="50">&nbsp;</th>
                        <th width="100">&nbsp;</th>
                        <th width="80">&nbsp;</th>  
                        <th width="100">&nbsp;</th>  
                        <th width="60">&nbsp;</th>  
                        <th width="70">&nbsp;</th>
                        <th width="90">&nbsp;</th>
                        <th width="120">&nbsp;</th>
                        <th width="75">&nbsp;</th>
                        <th width="70">&nbsp;</th>
                        <th width="80">&nbsp;</th>
                        <th width="90">Grand Total</th>
                        <th width="40">&nbsp;</th>
                        <th width="70"><? echo number_format($grand_btq,2); ?></th>
                        <th width="70"><? echo number_format($grand_tot_prod_btq,2); ?></th>
                        <th width="50">&nbsp;</th>
                        <th width="75">&nbsp;</th>
                        <th width="75">&nbsp;</th> 
                        <th width="70">&nbsp;</th>
                        <th width="60">&nbsp;</th>
                         <th >&nbsp;</th>
                    </tr>
                </tfoot>
            </table>
            </div>
            </fieldset>
            </div>
            <? }
        else if($cbo_type==4)// Compacting
        {
            ?>
            <div>
            <fieldset style="width:1860px;">
            <div align="center">
                <strong><? echo $company_library[$company]; ?></strong><br> 
                <strong><? echo $search_by_arr[$cbo_type];?></strong><br>
                <? echo change_date_format($date_from).' '.To.' '.change_date_format($date_to); ?>
            </div>

            <table class="rpt_table" width="1860" cellpadding="0" cellspacing="0" border="1" rules="all" align="center" id="table_header_1">
            <thead>
            <tr>
                <th width="30">SL</th>
                <? if($group_by==2 || $group_by==0){ ?>
                    <th width="80">M/C No</th>
                <? } ?>
                <? if($group_by==1 || $group_by==0){ ?>
                    <th width="80">Floor</th> 
                <? } ?> 
                <th width="50">Shift</th>
                <th width="100">Buyer</th>
                <th width="80">Job</th>
                <th width="100">Booking</th>
                <th width="60">File No</th>
                <th width="70">Ref. no</th>
                <th width="90">Order No</th>
                <th width="150">Fabrics Desc</th>
                <th width="50">GSM</th>
                <th width="70">Dia/Width Type</th>
                <th width="80">Color Name</th>
                <th width="90">Batch No</th>
                <th width="40">Extn. No</th>
                <th width="70">Batch Qty.</th>
                <th width="70">Prod. Qty.</th>
                <th width="50">Lot No</th>
                <th width="75">Start Date & Time</th>
                <th width="75">End Date & Time</th> 
                <th width="70">Time Used</th>
                <th width="60">Remark</th>
                <th>Reprocess</th>
            </tr>
            </thead>
            </table>
            <div style=" max-height:350px; width:1860px; overflow-y:scroll;;" id="scroll_body">
            <table class="rpt_table" id="table_body" width="1840" cellpadding="0" cellspacing="0" border="1" rules="all" align="center">
            <tbody>
            <? 
            $i=1;$k=1;$z=1;
            $f=0;
            $btq=0;$tot_prod_btq=$grand_btq=$grand_tot_prod_btq=$tot_prod_compact_qty=0;
            $batch_chk_arr=array();$group_by_arr=array();$prod_batch_chk_arr=array();

            foreach($batchdata as $batch)
            { 
            if ($i%2==0)  
            $bgcolor="#E9F3FF";
            else
            $bgcolor="#FFFFFF";
            $order_id=$batch[csf('po_id')];
            $color_id=$batch[csf('color_id')];
            $desc=explode(",",$batch[csf('item_description')]); 
            $po_id=implode(",",array_unique(explode(",",$batch[csf('po_id')]))); 
            $po_number=implode(",",array_unique(explode(",",$batch[csf('po_number')]))); 
            $com_group_arr=$batch[csf('prod_id')].$batch[csf('id')].$batch[csf('machine_id')].$batch[csf('floor_id')].$batch[csf('shift_name')].$batch[csf('width_dia_type')].$batch[csf('end_date')];
            $prod_compact_qty=$batch_prod_qty_arr3[$batch[csf('id')]][$batch[csf('prod_id')]][$batch[csf('width_dia_type')]][$batch[csf('end_date')]]['compact'];
            //echo $prod_compact_qty.'DD';
            $batch_compact_qty=$batch_prod_qty_arr3[$batch[csf('id')]][$batch[csf('prod_id')]][$batch[csf('width_dia_type')]][$batch[csf('end_date')]]['batch_compact_qty'];
                //$com_group=$comp_row[csf('batch_no')].$batch[csf('end_date')];
            if (!in_array($com_group_arr,$prod_batch_chk_arr))
                    { $z++;
                         $prod_batch_chk_arr[]=$com_group_arr;
                         $tot_prod_compact_qty=$prod_compact_qty;
                    }
                    else
                    {
                         $tot_prod_compact_qty=0;
                    }
            if($group_by!=0)
            {
                if($group_by==1)
                {
                    $group_value=$batch[csf('floor_id')];
                    $group_name="Floor";
                    $group_dtls_value=$floor_arr[$batch[csf('floor_id')]];
                }
                
                else if($group_by==2)
                {
                    $group_value=$batch[csf('machine_id')];
                    $group_name="Machine";
                    $group_dtls_value=$machine_arr[$batch[csf('machine_id')]];
                }
                if (!in_array($group_value,$group_by_arr) )
                    {
                        if($k!=1)
                        {
                        ?>  
                        <tr class="tbl_bottom">
                            <td width="30">&nbsp;</td>
                           
                             <? if($group_by==2 || $group_by==0){ ?>
                            <td width="80">&nbsp;</td>
                            <? } ?>
                            <? if($group_by==1 || $group_by==0){ ?>
                            <td width="80">&nbsp;</td> 
                            <? } 
                            ?> 
                            <td width="50">&nbsp;</td>
                            <td width="100">&nbsp;</td>
                            <td width="80">&nbsp;</td>
                            <td width="100">&nbsp;</td>
                            <td width="60">&nbsp;</td>
                            <td width="70">&nbsp;</td>
                            <td width="90">&nbsp;</td>
                            <td width="150">&nbsp;</td>
                            <td width="50">&nbsp;</td>
                            <td width="70">&nbsp;</td>
                            <td width="80">&nbsp;</td>
                            <td width="90">Sub Total</td>
                            <td width="40">&nbsp;</td>
                            <td width="70"> <? echo number_format($btq,2); ?></td>
                            <td width="70"><? echo number_format($tot_prod_btq,2); ?></td>
                            <td width="50">&nbsp;</td>
                            <td width="75">&nbsp;</td>
                            <td width="75">&nbsp;</td> 
                            <td width="70">&nbsp;</td>
                            <td width="60">&nbsp;</td>
                            <td>&nbsp;</td>
                        </tr>
                        <tr bgcolor="#EFEFEF">
                            <td colspan="24" align="left" ><b><? echo $group_name; ?> : <? echo $group_dtls_value; ?></b></td>
                        </tr>
                        <?
                        unset($btq);unset($tot_prod_btq);
                        }
                        $group_by_arr[]=$group_value; 
                        $k++;
                    }
            } 
            $grouping_arr_val=$batch[csf('batch_no')].$batch[csf('machine_id')].$batch[csf('floor_id')];
            ?>
            <tr bgcolor="<? echo $bgcolor;?>" onClick="change_color('tr<? echo $i;?>','<? echo $bgcolor;?>')" id="tr<? echo $i;?>">
                <? if (!in_array($grouping_arr_val,$batch_chk_arr) )
                        { $f++;
                            ?>
                <td width="30"><? echo $f; ?></td>
                 <? if($group_by==2 || $group_by==0){ ?>
                <td  align="center" width="80"><div style="width:80px; word-wrap:break-word;"><? echo $machine_arr[$batch[csf('machine_id')]]; ?></div></td>
                <?
                 }
                 if($group_by==1 || $group_by==0){ ?>
               <td width="80"><div style="width:80px; word-wrap:break-word;"><? echo $floor_arr[$batch[csf('floor_id')]]; ?></div></td>
                <? } ?>
                 <td  align="center" width="50"><p><? echo $shift_name[$batch[csf('shift_name')]]; ?></p></td>
                <td  width="100" title="<? echo $buyer_arr[$batch[csf('buyer_name')]]; ?>"><p><? echo $buyer_arr[$batch[csf('buyer_name')]]; ?></p></td>
                <td  width="80" title="<? //echo $color_library[$batch[csf('color_id')]]; ?>"><p><? echo $batch[csf('job_no_prefix_num')]; ?></p></td>
                <td  width="100"><p><? echo $batch[csf('booking_no')]; ?></p></td>
                <td  width="60"><p><? echo $batch[csf('file_no')]; ?></p></td>
                <td  width="70"><p><? echo $batch[csf('grouping')]; ?></p></td>
                <td width="90"><div style="width:90px; word-wrap:break-word;"><? echo $po_number; ?></div></td>
               <?   
                   $batch_chk_arr[]=$grouping_arr_val;
                  }
                  else{ 
               ?>
                <td width="30"><? //echo $sl; ?></td>
                  <? if($group_by==2 || $group_by==0){ ?>
                <td  align="center" width="80"><p><? //echo $machine_arr[$batch[csf('machine_id')]]; ?></p></td>
                <?
                 }
                 if($group_by==1 || $group_by==0){ ?>
               <td width="80"><p><? //echo $floor_arr[$batch[csf('floor_id')]]; ?></p></td>
                <? } ?>
                <td  align="center" width="50"><p><? //echo $machine_id; ?></p></td>
                <td  width="100"><p><? //echo $buyer_arr[$batch[csf('buyer_name')]]; ?></p></td>
                <td  width="80" title="<? //echo $color_library[$batch[csf('color_id')]]; ?>"><p><? //echo $color_library[$batch[csf('color_id')]]; ?></p></td>
                <td  width="100"><p><? //echo $special_row[csf('booking_no')]; ?></p></td>
                <td  width="60"><p><? //echo $special_row[csf('file_no')]; ?></p></td>
                <td  width="70"><p><? //echo $special_row[csf('grouping')]; ?></p></td>
                <td width="90"><p><? //echo $batch[csf('po_number')]; ?></p></td>
                <? } ?>
                <td  width="150" title="<? echo $desc[0]; ?>"><div style="word-break:break-all"><? echo $batch[csf('item_description')]; ?></div></td>
                <td  width="50" title="<? echo   $desc[2]; ?>"><p><? echo  $desc[2]; ?></p></td>
                <td width="70" title="<? ?>"><p><? echo $fabric_typee[$batch[csf('width_dia_type')]];;?></p></td>
                <td  width="80" title="<? echo $color_library[$batch[csf('color_id')]]; ?>"><div style="word-break:break-all"><? echo $color_library[$batch[csf('color_id')]]; ?></div></td>
                <td  align="center" width="90" title="<? echo $batch[csf('batch_no')]; ?>"><div style="word-break:break-all"><? echo $batch[csf('batch_no')]; ?></div></td>
                <td  align="center" width="40" title="<? echo $batch[csf('extention_no')]; ?>"><p><? echo $batch[csf('extention_no')]; ?></p></td>
                <td align="right" width="70" title="<? echo $batch[csf('batch_qnty')];  ?>"><? echo number_format($batch[csf('batch_qnty')],2);  ?></td>
                 <td align="right" width="70" >
                    <? 
                     echo number_format($tot_prod_compact_qty,2);  
                    ?>
                </td>
                <td align="left" width="50" title="<? echo $yarn_lot_arr[$batch[csf('prod_id')]][$batch[csf('po_id')]]; ?>"><p><?
                $yarn_lot='';
                foreach($po_id as $pid)
                {
                    if($yarn_lot=='') $yarn_lot=$yarn_lot_arr[$batch[csf('prod_id')]][$pid];else  $yarn_lot.=",".$yarn_lot_arr[$batch[csf('prod_id')]][$pid];
                }
                 echo $yarn_lot;//$yarn_lot_arr[$batch[csf('prod_id')]][$batch[csf('po_id')]]; 
                  ?></p></td>
                <td width="75" title="Process Start Date"><div style="width:75px; word-wrap:break-word;"><?  echo change_date_format($batch[csf('process_start_date')]).', '.$batch[csf('start_hours')].':'.$batch[csf('start_minutes')]; ?></div></td>
                <td width="75" title="Process End Date"><div style="width:75px; word-wrap:break-word;"><?  echo change_date_format($batch[csf('end_date')]).', '.$batch[csf('end_hours')].':'.$batch[csf('end_minutes')]; ?></div></td>
                 <td width="70" align="center"><div style="width:70px; word-wrap:break-word;"><?
                        $end_time=$batch[csf('end_hours')].':'.$batch[csf('end_minutes')];
                        $start_time=$batch[csf('start_hours')].':'.$batch[csf('start_minutes')];
                        $new_date_time_start=($batch[csf('process_start_date')].' '.$start_time.':'.'00');
                        $new_date_time_end=($batch[csf('end_date')].' '.$end_time.':'.'00');
                        $total_time=datediff(n,$new_date_time_start,$new_date_time_end);
                        echo floor($total_time/60).":".$total_time%60; ?></div></td>
                <td align="center" width="60"><p> <?   echo $batch[csf('remarks')];?> </p>
                 </td>
                <td align="center" title="<? if($batch[csf('batch_against')]==2) echo $batch_against[$batch[csf('batch_against')]]; ?>"><p><?  if($batch[csf('batch_against')]==2) echo $batch_against[$batch[csf('batch_against')]]; ?></p> </td>
            </tr>
            <? 
            $i++;
            $btq+=$batch[csf('batch_qnty')];
            $tot_prod_btq+=$tot_prod_compact_qty;
            $grand_btq+=$batch[csf('batch_qnty')];
            $grand_tot_prod_btq+=$tot_prod_compact_qty;
            } //batchdata froeach
                        if($group_by!=0)
                        {
                        ?>  
                        <tr class="tbl_bottom">
                            <td width="30">&nbsp;</td>
                           
                             <? if($group_by==2 || $group_by==0){ ?>
                            <td width="80">&nbsp;</td>
                            <? } ?>
                            <? if($group_by==1 || $group_by==0){ ?>
                            <td width="80">&nbsp;</td> 
                            <? } 
                            ?> 
                            <td width="50">&nbsp;</td>
                            <td width="100">&nbsp;</td>
                            <td width="80">&nbsp;</td>
                            <td width="100">&nbsp;</td>
                            <td width="60">&nbsp;</td>
                            <td width="70">&nbsp;</td>
                            <td width="90">&nbsp;</td>
                            <td width="150">&nbsp;</td>
                            <td width="50">&nbsp;</td>
                            <td width="70">&nbsp;</td>
                            <td width="80">&nbsp;</td>
                            <td width="90">Sub Total</td>
                            <td width="40">&nbsp;</td>
                            <td width="70"> <? echo number_format($btq,2); ?></td>
                            <td width="70"><? echo number_format($tot_prod_btq,2); ?></td>
                            <td width="50">&nbsp;</td>
                            <td width="75">&nbsp;</td>
                            <td width="75">&nbsp;</td> 
                            <td width="70">&nbsp;</td>
                            <td width="60">&nbsp;</td>
                            <td>&nbsp;</td>
                        </tr>
                        
                        <?
                        }
                        ?>
                </tbody>
                <tfoot>
                    <tr>
                        <th width="30">&nbsp;</th>
                         <? if($group_by==2 || $group_by==0){ ?>
                        <th width="80">&nbsp;</th>
                        <? } ?>
                        <? if($group_by==1 || $group_by==0){ ?>
                        <th width="80">&nbsp;</th> 
                        <? } 
                        ?> 
                        <th width="50">&nbsp;</th>
                        <th width="100">&nbsp;</th>
                        <th width="80">&nbsp;</th>  
                        <th width="100">&nbsp;</th>  
                        <th width="60">&nbsp;</th>  
                        <th width="70">&nbsp;</th>
                        <th width="90">&nbsp;</th>
                        <th width="150">&nbsp;</th>
                        <th width="50">&nbsp;</th>
                        <th width="70">&nbsp;</th>
                        <th width="80">&nbsp;</th>
                        <th width="90">Grand Total</th>
                        <th width="40">&nbsp;</th>
                        <th width="70"><? echo number_format($grand_btq,2); ?></th>
                        <th width="70"><? echo number_format($grand_tot_prod_btq,2); ?></th>
                        <th width="50">&nbsp;</th>
                        <th width="75">&nbsp;</th>
                        <th width="75">&nbsp;</th> 
                        <th width="70">&nbsp;</th>
                        <th width="60">&nbsp;</th>
                        <th>&nbsp;</th>
                    </tr>
                </tfoot>
            </table>

            </div>
            </fieldset>
            </div>
                <? }
        else if($cbo_type==5) // Special Finishing
        {
            ?>
            <div>
            <fieldset style="width:1815px;">
            <div align="center"><strong> <? echo $company_library[$company]; ?> </strong><br> <strong><? echo $search_by_arr[$cbo_type];?> </strong><br>
                <?
                echo  change_date_format($date_from).' '.To.' '.change_date_format($date_to);
                ?>
            </div>
            <table class="rpt_table" width="1835" cellpadding="0" cellspacing="0" border="1" rules="all" align="center" id="table_header_1">
                <thead>
                    <tr>
                        <th width="30">SL</th>
                         <? if($group_by==2 || $group_by==0){ ?>
                        <th width="80">M/C No</th>
                        <? } ?>
                        <? if($group_by==1 || $group_by==0){ ?>
                        <th width="80">Floor</th> 
                        <? } 
                        ?> 
                        <th width="50">Shift</th>
                        <th width="100">Buyer</th>
                        <th width="80">Job</th> 
                        <th width="100">Booking</th> 
                        <th width="60">File No</th> 
                        <th width="70">Ref. No</th>
                        <th width="90">Order No</th>
                        <th width="100">Fabrics Desc</th>
                        <th width="75">GSM</th>
                        <th width="70">Dia/Width Type</th>
                        <th width="80">Color Name</th>
                        <th width="90">Batch No</th>
                        <th width="40">Extn. No</th>
                        <th width="70">Batch Qty.</th>
                        <th width="70">Prod. Qty.</th>
                        <th width="50">Lot No</th>
                        <th width="75">Start Date & Time</th> 
                        <th width="75">End Date & Time</th>
                        <th width="70">Time Used</th>
                        <th width="60">Remark</th>
                        <th>Reprocess</th>
                    </tr>
                </thead>
            </table>
            <div style=" max-height:350px; width:1835px; overflow-y:scroll;;" id="scroll_body">
            <table class="rpt_table" id="table_body" width="1815" cellpadding="0" cellspacing="0" border="1" rules="all" align="center">
                <tbody>
                    <? 
                    $i=1;
                    $f=0;$k=1;
                    $btq=0;$tot_prod_btq=$grand_btq=$grand_tot_prod_btq=0;
                    $batch_chk_arr=array();
                    foreach($batchdata as $batch)
                    { 
                        if ($i%2==0)  
                        $bgcolor="#E9F3FF";
                        else
                        $bgcolor="#FFFFFF";
                        $order_id=$batch[csf('po_id')];
                        $color_id=$batch[csf('color_id')];
                        $desc=explode(",",$batch[csf('item_description')]); 
                        $po_number=implode(",",array_unique(explode(",",$batch[csf('po_number')])));
                        if($group_by!=0)
                        {
                            if($group_by==1)
                            {
                                $group_value=$batch[csf('floor_id')];
                                $group_name="Floor";
                                $group_dtls_value=$floor_arr[$batch[csf('floor_id')]];
                            }
                            
                            else if($group_by==2)
                            {
                                $group_value=$batch[csf('machine_id')];
                                $group_name="Machine";
                                $group_dtls_value=$machine_arr[$batch[csf('machine_id')]];//
                            }
                            if (!in_array($group_value,$group_by_arr) )
                            {
                                if($k!=1)
                                {
                                    ?>  
                                    <tr class="tbl_bottom">
                                        <td width="30">&nbsp;</td>
                                        <? if($group_by==2 || $group_by==0){ ?>
                                        <td width="80">&nbsp;</td>
                                        <? } ?>
                                        <? if($group_by==1 || $group_by==0){ ?>
                                        <td width="80">&nbsp;</td> 
                                        <? } 
                                        ?> 
                                        <td width="50">&nbsp;</td>
                                        <td width="100">&nbsp;</td>
                                        <td width="80">&nbsp;</td>
                                        <td width="100">&nbsp;</td>
                                        <td width="60">&nbsp;</td>
                                        <td width="70">&nbsp;</td>
                                        <td width="90">&nbsp;</td>
                                        <td width="100">&nbsp;</td>
                                        <td width="75">&nbsp;</td>
                                        <td width="70">&nbsp;</td>
                                        <td width="80">&nbsp;</td>
                                        <td width="90">Sub Total</td>
                                        <td width="40">&nbsp;</td>
                                        <td width="70"> <? echo number_format($btq,2); ?></td>
                                        <td width="70"><? echo number_format($tot_prod_btq,2); ?></td>
                                        <td width="50">&nbsp;</td>
                                        <td width="75">&nbsp;</td>
                                        <td width="75">&nbsp;</td> 
                                        <td width="70">&nbsp;</td>
                                        <td width="60">&nbsp;</td>
                                        <td>&nbsp;</td>
                                    </tr>
                                    <?
                                    unset($btq);unset($tot_prod_btq);
                                }
                                ?>  
                                <tr bgcolor="#EFEFEF">
                                    <td colspan="23" align="left" ><b><? echo $group_name; ?> : <? echo $group_dtls_value; ?></b></td>
                                </tr>
                                <?
                                $group_by_arr[]=$group_value;
                                $k++;
                            }
                        } 
                        ?>
                        <tr bgcolor="<? echo $bgcolor;?>" onClick="change_color('tr<? echo $i;?>','<? echo $bgcolor;?>')" id="tr<? echo $i;?>">
                            <? if (!in_array($batch[csf('batch_no')],$batch_chk_arr) )
                                    { $f++;
                                        ?>
                            <td width="30"><? echo $f; ?></td>
                           
                              <? if($group_by==2 || $group_by==0){ ?>
                            <td  align="center" width="80"><div style="width:80px; word-wrap:break-word;"><? echo $machine_arr[$batch[csf('machine_id')]]; ?></div></td>
                            <?
                             }
                             if($group_by==1 || $group_by==0){ ?>
                           <td width="80"><div style="width:80px; word-wrap:break-word;"><? echo $floor_arr[$batch[csf('floor_id')]]; ?></div></td>
                            <? } ?>
                             <td  align="center" width="50"><p><? echo $shift_name[$batch[csf('shift_name')]]; ?></p></td>
                            <td  width="100" title="<? echo $buyer_arr[$batch[csf('buyer_name')]]; ?>"><p><? echo $buyer_arr[$batch[csf('buyer_name')]]; ?></p></td>
                            <td  width="80" title="<? //echo $color_library[$batch[csf('color_id')]]; ?>"><p><? echo $batch[csf('job_no_prefix_num')]; ?></p></td>
                            <td  width="100"><p><? echo $batch[csf('booking_no')]; ?></p></td>
                            <td  width="60"><p><? echo $batch[csf('file_no')]; ?></p></td>
                            <td  width="70"><p><? echo $batch[csf('grouping')]; ?></p></td>
                            <td width="90"><div style="width:90px; word-wrap:break-word;"><? echo $po_number; ?></div></td>
                                <?  
                                $batch_chk_arr[]=$batch[csf('batch_no')];
                                    } 
                                    else
                                       { ?>
                            <td width="30"><? //echo $sl; ?></td>
                            <? if($group_by==2 || $group_by==0){ ?>
                            <td  align="center" width="80"><div style="width:80px; word-wrap:break-word;"><? //echo $machine_arr[$batch[csf('machine_id')]]; ?></div></td>
                            <?
                             }
                             if($group_by==1 || $group_by==0){ ?>
                           <td width="80"><div style="width:80px; word-wrap:break-word;"><? //echo $floor_arr[$batch[csf('floor_id')]]; ?></div></td>
                            <? } ?>
                            <td  align="center" width="50"><p><? //echo $machine_id; ?></p></td>
                            <td  width="100"><p><? //echo $buyer_arr[$batch[csf('buyer_name')]]; ?></p></td>
                            <td  width="80" title="<? //echo $color_library[$batch[csf('color_id')]]; ?>"><p><? //echo $color_library[$batch[csf('color_id')]]; ?></p></td>
                            <td  width="100"><p><? //echo $special_row[csf('booking_no')]; ?></p></td>
                            <td  width="60"><p><? //echo $special_row[csf('file_no')]; ?></p></td>
                            <td  width="70"><p><? //echo $special_row[csf('grouping')]; ?></p></td>
                            <td width="90"><p><? //echo $batch[csf('po_number')]; ?></p></td>
                                    <? }
                                    ?>
                            <td  width="100" title="<? echo $desc[0]; ?>"><div style="word-break:break-all"><? echo $batch[csf('item_description')]; ?></div></td>
                            <td  width="75" title="<? echo   $desc[2]; ?>"><p><? echo  $desc[2]; ?></p></td>
                            <td  width="70" title="<? ?>"><p><? echo $fabric_typee[$batch[csf('width_dia_type')]];?></p></td>
                            <td  width="80" title="<? echo $color_library[$batch[csf('color_id')]]; ?>"><p><? echo $color_library[$batch[csf('color_id')]]; ?></p></td>
                            <td  align="center" width="90" title="<? echo $batch[csf('batch_no')]; ?>"><p><? echo $batch[csf('batch_no')]; ?></p></td>
                            <td  align="center" width="40" title="<? echo $batch[csf('extention_no')]; ?>"><p><? echo $batch[csf('extention_no')]; ?></p></td>
                            <td align="right" width="70" title="<? echo $batch[csf('batch_qnty')];  ?>"><? echo number_format($batch[csf('batch_qnty')],2);  ?></td>
                            <td align="right" width="70" ><? 
                                 echo number_format($batch_prod_qty_arr[$batch[csf('id')]][$batch[csf('prod_id')]][$batch[csf('width_dia_type')]]['special'],2); 
                            ?></td>
                            <td align="left" width="50" title="<? echo $yarn_lot_arr[$batch[csf('prod_id')]][$batch[csf('po_id')]]; ?>"><p><? echo $yarn_lot_arr[$batch[csf('prod_id')]][$batch[csf('po_id')]];  ?></p></td>
                           <td width="75" title="Process Start Date"><div style="width:75px; word-wrap:break-word;"><?  echo change_date_format($batch[csf('process_start_date')]).', '.$batch[csf('start_hours')].':'.$batch[csf('start_minutes')]; ?></div></td>
                            <td width="75" title="Process End Date"><div style="width:75px; word-wrap:break-word;"><?  echo change_date_format($batch[csf('end_date')]).', '.$batch[csf('end_hours')].':'.$batch[csf('end_minutes')]; ?></div></td>
                             <td width="70" align="center"><div style="width:70px; word-wrap:break-word;"><?
                                    $end_time=$batch[csf('end_hours')].':'.$batch[csf('end_minutes')];
                                    $start_time=$batch[csf('start_hours')].':'.$batch[csf('start_minutes')];
                                    
                                    $new_date_time_start=($batch[csf('process_start_date')].' '.$start_time.':'.'00');
                                    $new_date_time_end=($batch[csf('end_date')].' '.$end_time.':'.'00');
                                    $total_time=datediff(n,$new_date_time_start,$new_date_time_end);
                                    echo floor($total_time/60).":".$total_time%60; ?></div></td>
                            <td align="center" width="60"><p> <?  echo $batch[csf('remarks')]; ?>
                                </p>
                             </td>
                            <td align="center" title="<?   if($batch[csf('batch_against')]==2) echo $batch_against[$batch[csf('batch_against')]]; ?>"><p><?  if($batch[csf('batch_against')]==2) echo $batch_against[$batch[csf('batch_against')]]; ?></p> </td>
                        </tr>
                        <? 
                        $i++;
                        $btq+=$batch[csf('batch_qnty')];
                        $tot_prod_btq+=$batch_prod_qty_arr[$batch[csf('id')]][$batch[csf('prod_id')]][$batch[csf('width_dia_type')]]['special'];
                        $grand_btq+=$batch[csf('batch_qnty')];
                        $grand_tot_prod_btq+=$batch_prod_qty_arr[$batch[csf('id')]][$batch[csf('prod_id')]][$batch[csf('width_dia_type')]]['special'];
                    } //batchdata froeach
                    if($group_by!=0)
                    {
                        ?>  
                        <tr class="tbl_bottom">
                            <td width="30">&nbsp;</td>
                            <? if($group_by==2 || $group_by==0){ ?>
                            <td width="80">&nbsp;</td>
                            <? } ?>
                            <? if($group_by==1 || $group_by==0){ ?>
                            <td width="80">&nbsp;</td> 
                            <? } 
                            ?> 
                            <td width="50">&nbsp;</td>
                            <td width="100">&nbsp;</td>
                            <td width="80">&nbsp;</td>
                            <td width="100">&nbsp;</td>
                            <td width="60">&nbsp;</td>
                            <td width="70">&nbsp;</td>
                            <td width="90">&nbsp;</td>
                            <td width="100">&nbsp;</td>
                            <td width="75">&nbsp;</td>
                            <td width="70">&nbsp;</td>
                            <td width="80">&nbsp;</td>
                            <td width="90">Sub Total</td>
                            <td width="40">&nbsp;</td>
                            <td width="70"> <? echo number_format($btq,2); ?></td>
                            <td width="70"><? echo number_format($tot_prod_btq,2); ?></td>
                            <td width="50">&nbsp;</td>
                            <td width="75">&nbsp;</td>
                            <td width="75">&nbsp;</td> 
                            <td width="70">&nbsp;</td>
                            <td width="60">&nbsp;</td>
                            <td>&nbsp;</td>
                        </tr>
                        <?
                    }
                    ?>
                </tbody>
            </table>
            <table class="rpt_table" width="1815" cellpadding="0" cellspacing="0" border="1" rules="all" align="center" id="report_table_footer">
                <tfoot>
                    <tr>
                        <th width="30">&nbsp;</th>
                       
                         <? if($group_by==2 || $group_by==0){ ?>
                        <th width="80">&nbsp;</th>
                        <? } ?>
                        <? if($group_by==1 || $group_by==0){ ?>
                        <th width="80">&nbsp;</th> 
                        <? } 
                        ?> 
                        <th width="50">&nbsp;</th>
                        <th width="100">&nbsp;</th>
                        <th width="80">&nbsp;</th> 
                        <th width="100">&nbsp;</th> 
                        <th width="60">&nbsp;</th> 
                        <th width="70">&nbsp;</th>
                        <th width="90">&nbsp;</th>
                        <th width="100">&nbsp;</th>
                        <th width="75">&nbsp;</th>
                        <th width="70">&nbsp;</th>
                        <th width="80">&nbsp;</th>
                        <th width="90">Grand Total</th>
                        <th width="40">&nbsp;</th>
                        <th width="70"><? echo number_format($grand_btq,2); ?></th>
                        <th width="70"><? echo number_format($grand_tot_prod_btq,2); ?></th>
                        <th width="50">&nbsp;</th>
                        <th width="75">&nbsp;</th> 
                        <th width="75">&nbsp;</th>
                        <th width="70">&nbsp;</th>
                        <th width="60">&nbsp;</th>
                        <th>&nbsp;</th>
                    </tr>
                </tfoot>
            </table>
            </div>
            </fieldset>
            </div>
                <? }
            
        else if($cbo_type==6) //Waiting For Slitting -Unload
        { ?>
            <div style="width:1670px;">
            <fieldset style="width:1670px;">
            <div  align="center"> <strong> <? echo $company_library[$company]; ?> </strong><br><strong> <? echo $search_by_arr[$cbo_type];?> </strong>
            <br>
            <?
                echo  change_date_format($date_from).' '.To.' '.change_date_format($date_to);
            ?>
             </div>
             <table class="rpt_table" width="1670" cellpadding="0" cellspacing="0" border="1" rules="all" align="center" id="table_header_1">
            <thead>
            <tr>
                <th width="30">SL</th>
               
                  <? if($group_by==2 || $group_by==0){ ?>
                <th width="80">M/C No</th> 
                <? } ?>
                <? if($group_by==1 || $group_by==0){ ?>
                <th width="80">Floor</th> 
                <? } 
                ?> 
                <th width="50">Shift</th>
                <th width="100">Buyer</th>
                <th width="100">Booking</th> 
                <th width="60">File No</th> 
                <th width="70">Ref. No</th> 
                <th width="90">Order No</th>
                <th width="100">Fabrics Desc</th>
                <th width="70">GSM</th>
                <th width="80">Color Name</th>
                <th width="90">Batch No</th>
                <th width="80">Ext. No</th>
                <th width="80">Batch Qty.</th>
                <th width="70">Prod. Qty.</th>
                <th width="100">Unloading Date</th>
                <th width="80">Unloading Time</th>
                <th width="80">Shade Position</th>
                <th width="60">Yarn Lot</th>
                <th>Reprocess</th>
            </tr>
            </thead>
            </table>
            <div style=" max-height:380px; width:1670px; overflow-y:scroll;" id="scroll_body">
            <table class="rpt_table" id="table_body" width="1650" cellpadding="0" cellspacing="0" border="1" rules="all" align="center">
            <tbody>
            <? 
            $i=1;
            $f=0;
            $btq=0;$tot_prod_btq=0;
            //if($db_type==0) $group_concat="group_concat(c.po_number)"; 
            //else if($db_type==2) $group_concat="listagg(c.po_number,',' ) within group (order by c.po_number) AS po_number";
            /*$sql_batch_h=sql_select("select batch_id from  pro_fab_subprocess where entry_form=30 and status_active=1 and is_deleted=0 and batch_id>0");
                $i=1;
                foreach($sql_batch_h as $row_h)
                {
                    if($i!==1) $row_siltting.=",";
                    $row_siltting.=$row_h[csf('batch_id')];
                    $i++;
                }
            //and a.process_id in(63)
            $w_siltting=array_chunk(array_unique(explode(",",$row_siltting)),999);*/
            //print_r($w_siltting);die;,c.file_no,c.grouping 
             $sql_wait="SELECT a.company_id,a.id,a.batch_no, a.process_id,a.batch_date,a.color_id,a.extention_no,a.batch_against,SUM(b.batch_qnty) AS batch_qnty,b.item_description,b.po_id,b.prod_id,b.width_dia_type,$group_concat,c.job_no_mst,d.job_no_prefix_num,d.buyer_name,c.file_no,c.grouping,f.shift_name,f.process_end_date,f.process_start_date,f.production_date as end_date,f.start_minutes,f.start_hours,f.end_hours,f.end_minutes,f.machine_id,f.floor_id,f.result,f.remarks $dyeing_pro from pro_batch_create_dtls b,wo_po_break_down c,wo_po_details_master d,pro_batch_create_mst a,pro_fab_subprocess f where  f.batch_id=a.id  and a.id=b.mst_id and b.po_id=c.id and d.job_no=c.job_no_mst and f.entry_form=35 $company_cond $working_company_cond  $dates_com $jobdata $batch_num $buyerdata $order_no $year_cond $color_name and a.entry_form=0  and f.load_unload_id in(2) and  a.batch_against in(1,2)  and b.status_active=1 and b.is_deleted=0 and a.status_active=1   and f.result=1 and a.is_deleted=0    GROUP BY b.po_id, b.item_description,a.company_id, a.id, a.batch_no,a.process_id, a.batch_date,a.color_id, a.extention_no, a.batch_against,b.prod_id, b.width_dia_type,c.job_no_mst, d.job_no_prefix_num, d.buyer_name,c.file_no,c.grouping,f.shift_name,f.result, f.process_end_date,f.process_start_date,f.production_date,f.start_minutes,f.start_hours, f.end_hours, f.end_minutes, f.machine_id,f.floor_id, f.remarks $dyeing_group   $order_by";
                         
                $fab_data=sql_select($sql_wait);
                $batchIdArr=array();
                foreach($fab_data as $rows)
                { 
                    $batchIdArr[$rows[csf('id')]]=$rows[csf('id')];
                }
                
                
                $remove_batch_sql="select BATCH_ID from  pro_fab_subprocess where entry_form=30 and status_active=1 and is_deleted=0 and batch_id>0 ";

                $batch_array_chunk=array_chunk($batchIdArr,999);
                $p=1;
                foreach($batch_array_chunk as $bid)
                {
                    if($p==1)  $remove_batch_sql .="and (a.batch_id not in(".implode(',',$bid).")"; 
                    else  $remove_batch_sql .=" and a.batch_id not in(".implode(',',$bid).")";
                    $p++;
                }
                $remove_batch_sql .=")";
                $remove_batch_sql_result=sql_select($remove_batch_sql);
                $remove_batch_arr=array();
                foreach($remove_batch_sql_result as $rows)
                { 
                    $remove_batch_arr[$rows[BATCH_ID]]=1;
                }
                         
            $batch_chk_arr=array();

            foreach($fab_data as $batch)
            {
                if($remove_batch_arr[$batch[csf('id')]]==1){continue;} 
            if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
            $order_id=$batch[csf('po_id')];
            $color_id=$batch[csf('color_id')];
            $desc=explode(",",$batch[csf('item_description')]); 
            $po_number=implode(",",array_unique(explode(",",$batch[csf('po_number')])));
            $processid=explode(",",$batch[csf('process_id')]);
            $result=$batch[csf('result')];
             
            if (in_array(63,$processid))
            {
            if($group_by!=0)
            {
                if($group_by==1)
                {
                    $group_value=$batch[csf('floor_id')];
                    $group_name="Floor";
                    $group_dtls_value=$floor_arr[$batch[csf('floor_id')]];
                }
                
                else if($group_by==2)
                {
                    $group_value=$batch[csf('machine_id')];
                    $group_name="Machine";
                    $group_dtls_value=$machine_arr[$batch[csf('machine_id')]];//
                }
                if (!in_array($group_value,$group_by_arr) )
                    {
                        //if($k!=1)
                        //{
                        ?>  
                        <tr bgcolor="#EFEFEF">
                            <td colspan="23" align="left" ><b><? echo $group_name; ?> : <? echo $group_dtls_value; ?></b></td>
                        </tr>
                        <?
                        $group_by_arr[]=$group_value; 
                        //}
                       // $k++;
                    }
            }
            ?>
            <tr bgcolor="<? echo $bgcolor;?>" onClick="change_color('tr<? echo $i;?>','<? echo $bgcolor;?>')" id="tr<? echo $i;?>">
                <? if (!in_array($batch[csf('batch_no')],$batch_chk_arr) )
                        { $f++;
                            ?>
                <td width="30"><? echo $f; ?></td>
                <? if($group_by==2 || $group_by==0){ ?>
                <td  align="center" width="80"><div style="width:80px; word-wrap:break-word;"><? echo $machine_arr[$batch[csf('machine_id')]]; ?></div></td>
                <?
                 }
                 if($group_by==1 || $group_by==0){ ?>
               <td width="80"><div style="width:80px; word-wrap:break-word;"><? echo $floor_arr[$batch[csf('floor_id')]]; ?></div></td>
                <? } ?>
                 <td  align="center" width="50"><p><? echo $shift_name[$batch[csf('shift_name')]]; ?></p></td>
                <td  width="100" title="<? echo $buyer_arr[$batch[csf('buyer_name')]]; ?>"><p><? echo $buyer_arr[$batch[csf('buyer_name')]]; ?></p></td>
                <td  width="100"><p><? echo $batch[csf('booking')]; ?></p></td>
                <td  width="60"><p><? echo $batch[csf('file_no')]; ?></p></td>
                <td  width="70"><p><? echo $batch[csf('grouping')]; ?></p></td>
                <td width="90">
            <div style="width:90px; word-wrap:break-word;"><? echo $po_number; ?></div></td>
                    <?  
                    $batch_chk_arr[]=$batch[csf('batch_no')];
                        } 
                        else
                           { ?>
                <td width="30"><? //echo $sl; ?></td>
                 <? if($group_by==2 || $group_by==0){ ?>
                <td  align="center" width="80"><div style="width:80px; word-wrap:break-word;"><? //echo $machine_arr[$batch[csf('machine_id')]]; ?></div></td>
                <?
                 }
                 if($group_by==1 || $group_by==0){ ?>
               <td width="80"><div style="width:80px; word-wrap:break-word;"><? //echo $floor_arr[$batch[csf('floor_id')]]; ?></div></td>
                <? } ?>
                <td  align="center" width="50"><p><? //echo $machine_id; ?></p></td>
                <td  width="100"><p><? //echo $buyer_arr[$batch[csf('buyer_name')]]; ?></p></td>
                <td  width="100"><p><? //echo $special_row[csf('booking_no')]; ?></p></td>
                <td  width="60"><p><? //echo $batch[csf('file_no')]; ?></p></td>
                <td  width="70"><p><? //echo $batch[csf('grouping')]; ?></p></td>
                <td width="90"><p><? //echo $batch[csf('po_number')]; ?></p></td>
                        <? }
                        ?>
                <td  width="100" title="<? echo $desc[0]; ?>"><p><? echo $batch[csf('item_description')]; ?></p></td>
                <td  width="70" title="<? echo  $desc[2]; ?>"><p><? echo $desc[2]; ?></p></td>
                <td  width="80" title="<? echo $color_library[$batch[csf('color_id')]]; ?>"><p><? echo $color_library[$batch[csf('color_id')]]; ?></p></td>
                <td  align="center" width="90" title="<? echo $batch[csf('batch_no')]; ?>"><p><? echo $batch[csf('batch_no')]; ?></p></td>
                <td  align="center" width="80" title="<? echo $batch[csf('extention_no')]; ?>"><p><? echo $batch[csf('extention_no')]; ?></p></td>
                <td align="right" width="80" title="<? echo $batch[csf('batch_qnty')];  ?>"><p><? echo number_format($batch[csf('batch_qnty')],2);  ?></p></td>
                <td align="right" width="70" ><? echo number_format($batch_prod_qty_arr[$batch[csf('id')]][$batch[csf('prod_id')]][$batch[csf('width_dia_type')]]['unload'],2);  ?></td>
                <td width="100" title="<? echo change_date_format($batch[csf('process_end_date')]); ?>"><p><?  echo change_date_format($batch[csf('process_end_date')])?></p></td>
                <td align="center" width="80" title="<? echo $batch[csf('end_hours')].':'.$batch[csf('end_minutes')];  ?>"><p><? echo $batch[csf('end_hours')].':'.$batch[csf('end_minutes')];   ?></p></td>
                <td align="center" width="80" title="Shade" ><p><? if($result==1) echo 'OK';  ?></p></td>
                <td align="right" width="60" title="Lot"><p><? echo $yarn_lot_arr[$batch[csf('prod_id')]][$batch[csf('po_id')]]; ?></p></td>
                <td align="center"  title="<? if($batch[csf('batch_against')]==2) echo $batch_against[$batch[csf('batch_against')]]; ?>"><p><?  if($batch[csf('batch_against')]==2) echo $batch_against[$batch[csf('batch_against')]]; else echo '';?> </p></td>
            </tr>
            <? 
            $i++;
            $btq+=$batch[csf('batch_qnty')];
             $tot_prod_btq+=$batch_prod_qty_arr[$batch[csf('id')]][$batch[csf('prod_id')]][$batch[csf('width_dia_type')]]['unload'];
            }
            } //batchdata froeach
             ?>
            </tbody>
            </table>
             <table class="rpt_table" width="1650" cellpadding="0" cellspacing="0" border="1" rules="all" align="center" id="report_table_footer">
            <tfoot>
            <tr>
                <th width="30">&nbsp;</th>
                  <? if($group_by==2 || $group_by==0){ ?>
                <th width="80">&nbsp;</th> 
                <? } ?>
                <? if($group_by==1 || $group_by==0){ ?>
                <th width="80">&nbsp;</th> 
                <? } 
                ?> 
                <th width="50">&nbsp;</th>
                <th width="100">&nbsp;</th>
                <th width="100">&nbsp;</th>
                <th width="60">&nbsp;</th>
                <th width="70">&nbsp;</th>
                <th width="90">&nbsp;</th>
                <th width="100">&nbsp;</th>
                <th width="70">&nbsp;</th>
                <th width="80">&nbsp;</th>
                <th width="90">&nbsp;</th>
                <th width="80">&nbsp;</th>
                <th width="80"><? echo number_format($btq,2); ?></th>
                <th width="70"><? echo number_format($tot_prod_btq,2); ?></th>
                <th width="100">&nbsp;</th>
                <th width="80">&nbsp;</th>
                <th width="80">&nbsp;</th>
                <th width="60">&nbsp;</th>
                <th>&nbsp;</th>
            </tr>
            </tfoot>
            </table>
            </div>
            </fieldset>
            </div>
            <? }
        else if($cbo_type==7) // Wait For Drying 
        { ?>

            <div style="width:1670px;">
            <fieldset style="width:1670px;">
            <div  align="center"> <strong> <? echo $company_library[$company]; ?> </strong><br><strong> <? echo $search_by_arr[$cbo_type];?> </strong>
            <br>
            <?
                echo  change_date_format($date_from).' '.To.' '.change_date_format($date_to);
            ?>
             </div>
             <table class="rpt_table" width="1670" cellpadding="0" cellspacing="0" border="1" rules="all" align="center" id="table_header_1">
            <thead>
            <tr>
                <th width="30">SL</th> 
               
                 <? if($group_by==2 || $group_by==0){ ?>
                 <th width="80">M/C No</th>
                <? } ?>
                <? if($group_by==1 || $group_by==0){ ?>
                <th width="80">Floor</th> 
                <? } 
                ?> 
                <th width="50">Shift</th>
                <th width="80">Job No</th>
                <th width="100">Buyer</th>  
                <th width="100">Booking</th>  
                <th width="60">File No</th> 
                <th width="70">Ref. No</th>
                <th width="90">Order No</th>
                <th width="100">Fabrics Desc</th>
                <th width="70">GSM</th>
                <th width="80">Color Name</th>
                <th width="90">Batch No</th>
                <th width="80">Ext. No</th>
                <th width="80">Batch Qty.</th>
                <th width="70">Prod Qty.</th>
                <th width="100">Process Date</th>
                <th width="80">Process Time</th>
                <th width="60">Yarn Lot</th>
                <th>Reprocess</th>
            </tr>
            </thead>
            </table>
            <div style=" max-height:380px; width:1670px; overflow-y:scroll;" id="scroll_body">
            <table class="rpt_table" id="table_body" width="1650" cellpadding="0" cellspacing="0" border="1" rules="all" align="center">
            <tbody>
            <? 
            $i=1;
            $f=0;
            $btq=0;$tot_prod_btq=0;
            /*$sql_batch_h=sql_select("select a.batch_id from  pro_fab_subprocess a,pro_fab_subprocess_dtls b where a.entry_form=31 and a.id=b.mst_id and b.width_dia_type in(2) and a.status_active=1 and a.is_deleted=0 and a.batch_id>0");
                $i=1;
                foreach($sql_batch_h as $row_h)
                {
                    if($i!==1) $row_sent.=",";
                    $row_sent.=$row_h[csf('batch_id')];
                    $i++;
                }
            //and a.process_id in(63)
            $w_sent=array_chunk(array_unique(explode(",",$row_sent)),999);*/
            $sql_wait="select a.company_id,a.id,a.batch_no, a.process_id,a.batch_date,a.color_id,a.extention_no,a.batch_against,SUM(b.batch_qnty) AS batch_qnty,b.item_description,b.po_id,b.prod_id,b.width_dia_type,$group_concat,c.job_no_mst,d.job_no_prefix_num,d.buyer_name,c.file_no,c.grouping,f.shift_name,f.process_end_date,f.process_start_date,f.production_date as end_date,f.start_minutes,f.start_hours,f.end_hours,f.end_minutes,f.machine_id,f.floor_id,f.remarks 
            $stenter  from pro_batch_create_dtls b,wo_po_break_down c,wo_po_details_master d,pro_batch_create_mst a,pro_fab_subprocess f where  f.batch_id=a.id and b.po_id=c.id and d.job_no=c.job_no_mst  and a.id=b.mst_id and f.entry_form=48 $company_cond $working_company_cond   $dates_com $jobdata $batch_num $buyerdata $order_no $year_cond $color_name and a.entry_form=0 and f.re_stenter_no=0 and  a.batch_against in(1,2) and b.status_active=1 and b.is_deleted=0 and a.status_active=1  and a.is_deleted=0 and f.status_active=1 and f.is_deleted=0   GROUP BY b.po_id, b.item_description,a.company_id, a.id, a.batch_no,a.process_id, a.batch_date,a.color_id, a.extention_no, a.batch_against,b.prod_id, b.width_dia_type,c.job_no_mst, d.job_no_prefix_num, d.buyer_name,c.file_no,c.grouping,f.shift_name, f.process_end_date,f.process_start_date,f.production_date,f.start_minutes,f.start_hours, f.end_hours, f.end_minutes, f.machine_id,f.floor_id, f.remarks $stenter_group  $order_by "; 
            //echo $sql_wait;
                $fab_wait=sql_select($sql_wait);
                $batchIdArr=array();
                foreach($fab_wait as $rows)
                { 
                    $batchIdArr[$rows[csf('id')]]=$rows[csf('id')];
                }
                
                
                $remove_batch_sql="select a.BATCH_ID from  pro_fab_subprocess a,pro_fab_subprocess_dtls b where a.entry_form=31 and a.id=b.mst_id and b.width_dia_type in(2) and a.status_active=1 and a.is_deleted=0 and a.batch_id>0 ";

                $batch_array_chunk=array_chunk($batchIdArr,999);
                $p=1;
                foreach($batch_array_chunk as $bid)
                {
                    if($p==1)  $remove_batch_sql .="and (a.batch_id not in(".implode(',',$bid).")"; 
                    else  $remove_batch_sql .=" and a.batch_id not in(".implode(',',$bid).")";
                    $p++;
                }
                $remove_batch_sql .=")";
                $remove_batch_sql_result=sql_select($remove_batch_sql);
                $remove_batch_arr=array();
                foreach($remove_batch_sql_result as $rows)
                { 
                    $remove_batch_arr[$rows[BATCH_ID]]=1;
                }



            $batch_chk_arr=array();

            foreach($fab_wait as $batch)
            { 
                if($remove_batch_arr[$batch[csf('id')]]==1){continue;}
            if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
            $order_id=$batch[csf('po_id')];
            $color_id=$batch[csf('color_id')];
            $desc=explode(",",$batch[csf('item_description')]); 
            $po_number=implode(",",array_unique(explode(",",$batch[csf('po_number')])));
            $processid=explode(",",$batch[csf('process_id')]);
            //echo $batch[csf('process_id')];die;
            $result=$batch[csf('result')];
            //$process_arr='66,91';
            $process_name=array(66,91,125);
            $process_count=count($processid);
            $process = explode(",",str_replace("'","",$process_arr));
            $process_sql=count($processid);
            //print_r( $process);die;
            //print_r(array_diff($process_name,$processid));die;

            //if (in_array(65,$processid))
            //{
            if($group_by!=0)
            {
                if($group_by==1)
                {
                    $group_value=$batch[csf('floor_id')];
                    $group_name="Floor";
                    $group_dtls_value=$floor_arr[$batch[csf('floor_id')]];
                }
                
                else if($group_by==2)
                {
                    $group_value=$batch[csf('machine_id')];
                    $group_name="Machine";
                    $group_dtls_value=$machine_arr[$batch[csf('machine_id')]];//
                }
                if (!in_array($group_value,$group_by_arr) )
                    {
                        //if($k!=1)
                        //{
                        ?>  
                        <tr bgcolor="#EFEFEF">
                            <td colspan="23" align="left" ><b><? echo $group_name; ?> : <? echo $group_dtls_value; ?></b></td>
                        </tr>
                        <?
                        $group_by_arr[]=$group_value; 
                        //}
                       // $k++;
                    }
            }
            ?>
            <tr bgcolor="<? echo $bgcolor;?>" onClick="change_color('tr<? echo $i;?>','<? echo $bgcolor;?>')" id="tr<? echo $i;?>">
                <? if (!in_array($batch[csf('batch_no')],$batch_chk_arr) )
                        { $f++;
                            ?>
                <td width="30"><? echo $f; ?></td>
                <? if($group_by==2 || $group_by==0){ ?>
                <td  align="center" width="80"><div style="width:80px; word-wrap:break-word;"><? echo $machine_arr[$batch[csf('machine_id')]]; ?></div></td>
                <?
                 }
                 if($group_by==1 || $group_by==0){ ?>
               <td width="80"><div style="width:80px; word-wrap:break-word;"><? echo $floor_arr[$batch[csf('floor_id')]]; ?></div></td>
                <? } ?>
                <td  align="center" width="50"><p><? echo $shift_name[$batch[csf('shift_name')]]; ?></p></td>
                <td  align="center" width="80"><p><? echo $batch[csf('job_no_prefix_num')]; ?></p></td>
                <td  width="100" title="<? echo $buyer_arr[$batch[csf('buyer_name')]]; ?>"><p><? echo $buyer_arr[$batch[csf('buyer_name')]]; ?></p></td>
                <td  width="100"><p><? echo $batch[csf('booking_no')]; ?></p></td>
                <td  width="60"><p><? echo $batch[csf('file_no')]; ?></p></td>
                <td  width="70"><p><? echo $batch[csf('grouping')]; ?></p></td>
                <td width="90" title="<? echo $po_number; ?>"><p><? echo $po_number; ?></p></td>
                    <?  
                    $batch_chk_arr[]=$batch[csf('batch_no')];
                        } 
                        else
                           { ?>
                <td width="30"><? //echo $sl; ?></td>
                <? if($group_by==2 || $group_by==0){ ?>
                <td  align="center" width="80"><div style="width:80px; word-wrap:break-word;"><? //echo $machine_arr[$batch[csf('machine_id')]]; ?></div></td>
                <?
                 }
                 if($group_by==1 || $group_by==0){ ?>
               <td width="80"><div style="width:80px; word-wrap:break-word;"><? //echo $floor_arr[$batch[csf('floor_id')]]; ?></div></td>
                <? } ?>
                <td  align="center" width="50"><p><? //echo $machine_id; ?></p></td>
                <td  align="center" width="80"><p><? //echo $machine_id; ?></p></td>
                <td  width="100"><p><? //echo $buyer_arr[$batch[csf('buyer_name')]]; ?></p></td>
                <td  width="100"><p><? //echo $special_row[csf('booking_no')]; ?></p></td>
                <td  width="60"><p><? //echo $batch[csf('file_no')]; ?></p></td>
                <td  width="70"><p><? //echo $batch[csf('grouping')]; ?></p></td>
                <td width="90"><p><? //echo $batch[csf('po_number')]; ?></p></td>
                        <? }
                        ?>
                <td  width="100" title="<? echo $desc[0]; ?>"><p><? echo $batch[csf('item_description')]; ?></p></td>
                <td  width="70" title="<? echo  $desc[2]; ?>"><p><? echo $desc[2]; ?></p></td>
                <td  width="80" title="<? echo $color_library[$batch[csf('color_id')]]; ?>"><p><? echo $color_library[$batch[csf('color_id')]]; ?></p></td>
                <td  align="center" width="90" title="<? echo $batch[csf('batch_no')]; ?>"><p><? echo $batch[csf('batch_no')]; ?></p></td>
                <td  align="center" width="80" title="<? echo $batch[csf('extention_no')]; ?>"><p><? echo $batch[csf('extention_no')]; ?></p></td>
                <td align="right" width="80" title="<? echo $batch[csf('batch_qnty')];  ?>"><p><? echo number_format($batch[csf('batch_qnty')],2);  ?></p></td>
                
             <td align="right" width="70" ><? echo number_format($batch_prod_qty_arr[$batch[csf('id')]][$batch[csf('prod_id')]][$batch[csf('width_dia_type')]]['stenter'],2);  ?></td>
                <td width="100" title="<? echo change_date_format($batch[csf('process_end_date')]); ?>"><p><?  echo change_date_format($batch[csf('process_end_date')])?></p></td>
                <td align="center" width="80" title="<? echo $batch[csf('end_hours')].':'.$batch[csf('end_minutes')];  ?>"><p><? echo $batch[csf('end_hours')].':'.$batch[csf('end_minutes')];   ?></p></td>
               
                <td align="right" width="60" title="Lot"><p><? echo $yarn_lot_arr[$batch[csf('prod_id')]][$batch[csf('po_id')]]; ?></p></td>
                <td align="center"  title="<? if($batch[csf('batch_against')]==2) echo $batch_against[$batch[csf('batch_against')]]; ?>"><p><?  if($batch[csf('batch_against')]==2) echo $batch_against[$batch[csf('batch_against')]]; else echo '';?> </p></td>
            </tr>
            <? 
            $i++;
            $btq+=$batch[csf('batch_qnty')];
             $tot_prod_btq+=$batch_prod_qty_arr[$batch[csf('id')]][$batch[csf('prod_id')]][$batch[csf('width_dia_type')]]['stenter'];
            //}
            } //batchdata froeach
             ?>
            </tbody>
            </table>
             <table class="rpt_table" width="1650" cellpadding="0" cellspacing="0" border="1" rules="all" align="center" id="report_table_footer">
            <tfoot>
            <tr>
                <th width="30">&nbsp;</th>
                 <? if($group_by==2 || $group_by==0){ ?>
                 <th width="80">&nbsp;</th>
                <? } ?>
                <? if($group_by==1 || $group_by==0){ ?>
                <th width="80">&nbsp;</th> 
                <? } 
                ?> 
                <th width="50">&nbsp;</th>
                <th width="80">&nbsp;</th>
                <th width="100">&nbsp;</th>
                <th width="100">&nbsp;</th>
                <th width="60">&nbsp;</th>
                <th width="70">&nbsp;</th>
                <th width="90">&nbsp;</th>
                <th width="100">&nbsp;</th>
                <th width="70">&nbsp;</th>
                <th width="80">&nbsp;</th>
                <th width="90">&nbsp;</th>
                <th width="80">&nbsp;</th>
                <th width="80"><? echo number_format($btq,2); ?></th>
                <th width="70"><? echo number_format($tot_prod_btq,2); ?></th>
                <th width="100">&nbsp;</th>
                <th width="80">&nbsp;</th>
                <th width="60">&nbsp;</th>
                <th>&nbsp;</th>
            </tr>
            </tfoot>
            </table>
            </div>
            </fieldset>
            </div>
            <? }
        else if($cbo_type==8)//Wait for Compacting //Drying
        { ?>
            <div style="width:1750px;">
            <fieldset style="width:1650px;">
            <div  align="center"> <strong> <? echo $company_library[$company]; ?> </strong><br><strong> <? echo $search_by_arr[$cbo_type];?> </strong>
            <br>
            <?
                echo  change_date_format($date_from).' '.To.' '.change_date_format($date_to);
            ?>
             </div>
             <table class="rpt_table" width="1750" cellpadding="0" cellspacing="0" border="1" rules="all" align="center" id="table_header_1">
            <thead>
            <tr>
                <th width="30">SL</th>
                <? if($group_by==2 || $group_by==0){ ?>
                 <th width="80">M/C No</th>
                <? } ?>
                <? if($group_by==1 || $group_by==0){ ?>
                <th width="80">Floor</th> 
                <? } 
                ?> 
                <th width="50">Shift</th>
                <th width="100">Buyer</th>
                <th width="100">Booking</th>
                <th width="60">File No</th> 
                <th width="70">Ref. No</th> 
                <th width="90">Order No</th>
                <th width="100">Fabrics Desc</th>
                <th width="70">GSM</th>
                <th width="80">Color Name</th>
                <th width="90">Batch No</th>
                <th width="80">Ext. No</th>
                <th width="80">Batch Qty.</th>
                <th width="70">Prod Qty.</th>
                <th width="100">Process Date</th>
                <th width="80">Process Time</th>
                <th width="60">Yarn Lot</th>
                <th>Reprocess</th>
            </tr>
            </thead>
            </table>
            <div style=" max-height:380px; width:1750px; overflow-y:scroll;" id="scroll_body">
            <table class="rpt_table" id="table_body" width="1730" cellpadding="0" cellspacing="0" border="1" rules="all" align="center">
            <tbody>
            <? 
            $i=1;
            $f=0;
            $btq=0;$tot_prod_btq=0;
            /*$sql_batch_h=sql_select("select batch_id from  pro_fab_subprocess where entry_form=33 and status_active=1 and is_deleted=0 and batch_id>0");
                $i=1;
                foreach($sql_batch_h as $row_h)
                {
                    if($i!==1) $row_com.=",";
                    $row_com.=$row_h[csf('batch_id')];
                    $i++;
                }
                $w_com=array_chunk(array_unique(explode(",",$row_com)),999);*/
                
            $sql_wait="select a.company_id,a.id,a.batch_no, a.process_id,a.batch_date,a.color_id,a.extention_no,a.batch_against,SUM(b.batch_qnty) AS batch_qnty,b.item_description,b.po_id,b.prod_id,b.width_dia_type,$group_concat,c.job_no_mst,d.job_no_prefix_num,d.buyer_name,c.file_no,c.grouping,f.shift_name,f.process_end_date,f.process_start_date,f.production_date as end_date,f.start_minutes,f.start_hours,f.end_hours,f.end_minutes,f.machine_id,f.floor_id,f.remarks 
            $drying  from pro_batch_create_dtls b,wo_po_break_down c,wo_po_details_master d,pro_batch_create_mst a,pro_fab_subprocess f where a.id=b.mst_id and b.po_id=c.id and d.job_no=c.job_no_mst and f.entry_form=31 and f.batch_id=a.id $company_cond $working_company_cond   $dates_com $jobdata $batch_num $buyerdata $order_no $year_cond $color_name and a.entry_form=0 and  a.batch_against in(1,2)  and b.status_active=1 and b.is_deleted=0 and a.status_active=1   and a.is_deleted=0 GROUP BY b.po_id, b.item_description,a.company_id, a.id, a.batch_no,a.process_id, a.batch_date,a.color_id, a.extention_no, a.batch_against,b.prod_id, b.width_dia_type,c.job_no_mst, d.job_no_prefix_num, d.buyer_name,c.file_no,c.grouping,f.shift_name, f.process_end_date,f.process_start_date,f.production_date,f.start_minutes,f.start_hours, f.end_hours, f.end_minutes, f.machine_id,f.floor_id, f.remarks $drying_group $order_by "; 
                
                $sql_wait_data=sql_select($sql_wait);
                $batchIdArr=array();
                foreach($sql_wait_data as $rows)
                { 
                    $batchIdArr[$rows[csf('id')]]=$rows[csf('id')];
                }
                
                
                $remove_batch_sql="select BATCH_ID from  pro_fab_subprocess where entry_form=33 and status_active=1 and is_deleted=0 and batch_id>0 ";

                $batch_array_chunk=array_chunk($batchIdArr,999);
                $p=1;
                foreach($batch_array_chunk as $bid)
                {
                    if($p==1)  $remove_batch_sql .="and (a.batch_id not in(".implode(',',$bid).")"; 
                    else  $remove_batch_sql .=" and a.batch_id not in(".implode(',',$bid).")";
                    
                    $p++;
                }
                $remove_batch_sql .=")";
                $remove_batch_sql_result=sql_select($remove_batch_sql);
                $remove_batch_arr=array();
                foreach($remove_batch_sql_result as $rows)
                { 
                    $remove_batch_arr[$rows[BATCH_ID]]=1;
                }
                
                
                
                
            $batch_chk_arr=array();$group_by_arr=array();

            foreach($sql_wait_data as $batch)
            { 
                if($remove_batch_arr[$batch[csf('id')]]==1){continue;}
            if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
            $order_id=$batch[csf('po_id')];
            $color_id=$batch[csf('color_id')];
            $desc=explode(",",$batch[csf('item_description')]); 
            $po_number=implode(",",array_unique(explode(",",$batch[csf('po_number')])));
            $processid=explode(",",$batch[csf('process_id')]);
            $process_name=array(66,91,125);
            $process_count=count($processid);
            $process = explode(",",str_replace("'","",$process_arr));
            $process_sql=count($processid);
            //if (in_array(66,$processid))
            //{
            if($group_by!=0)
            {
                if($group_by==1)
                {
                    $group_value=$batch[csf('floor_id')];
                    $group_name="Floor";
                    $group_dtls_value=$floor_arr[$batch[csf('floor_id')]];
                }
                
                else if($group_by==2)
                {
                    $group_value=$batch[csf('machine_id')];
                    $group_name="Machine";
                    $group_dtls_value=$machine_arr[$batch[csf('machine_id')]];//
                }
                if (!in_array($group_value,$group_by_arr) )
                    {
                        //if($k!=1)
                        //{
                        ?>  
                        <tr bgcolor="#EFEFEF">
                            <td colspan="22" align="left" ><b><? echo $group_name; ?> : <? echo $group_dtls_value; ?></b></td>
                        </tr>
                        <?
                        $group_by_arr[]=$group_value; 
                        //}
                       // $k++;
                    }
            }
            $com_wait_grouping_arr_val=$batch[csf('batch_no')].$batch[csf('machine_id')].$batch[csf('floor_id')];
            ?>
            <tr bgcolor="<? echo $bgcolor;?>" onClick="change_color('tr<? echo $i;?>','<? echo $bgcolor;?>')" id="tr<? echo $i;?>">
                <? if (!in_array($com_wait_grouping_arr_val,$batch_chk_arr) )
                        { $f++;
                            ?>
                <td width="30"><? echo $f; ?></td>
                 <? if($group_by==2 || $group_by==0){ ?>
                <td  align="center" width="80"><div style="width:80px; word-wrap:break-word;"><? echo $machine_arr[$batch[csf('machine_id')]]; ?></div></td>
                <?
                 }
                 if($group_by==1 || $group_by==0){ ?>
               <td width="80"><div style="width:80px; word-wrap:break-word;"><? echo $floor_arr[$batch[csf('floor_id')]]; ?></div></td>
                <? } ?>
                 <td  align="center" width="50"><p><? echo $shift_name[$batch[csf('shift_name')]]; ?></p></td>
                <td  width="100" title="<? echo $buyer_arr[$batch[csf('buyer_name')]]; ?>"><p><? echo $buyer_arr[$batch[csf('buyer_name')]]; ?></p></td>
                <td  width="100"><p><? echo $batch[csf('booking_no')]; ?></p></td>
                <td  width="60"><p><? echo $batch[csf('file_no')]; ?></p></td>
                <td  width="70"><p><? echo $batch[csf('grouping')]; ?></p></td>
                <td width="90"><div style="width:90px; word-wrap:break-word;"><? echo $po_number; ?></div></td>

                    <?  
                    $batch_chk_arr[]=$com_wait_grouping_arr_val;
                        } 
                        else
                           { ?>
                <td width="30"><? //echo $sl; ?></td>
                <? if($group_by==2 || $group_by==0){ ?>
                <td  align="center" width="80"><div style="width:80px; word-wrap:break-word;"><? //echo $machine_arr[$batch[csf('machine_id')]]; ?></div></td>
                <?
                 }
                 if($group_by==1 || $group_by==0){ ?>
               <td width="80"><div style="width:80px; word-wrap:break-word;"><? //echo $floor_arr[$batch[csf('floor_id')]]; ?></div></td>
                <? } ?>
                 <td  align="center" width="50"><p><? //echo $machine_id; ?></p></td>
                <td  width="100"><p><? //echo $buyer_arr[$batch[csf('buyer_name')]]; ?></p></td>
                <td  width="100"><p><? //echo $special_row[csf('booking_no')]; ?></p></td>
                <td  width="60"><p><? //echo $batch[csf('file_no')]; ?></p></td>
                <td  width="70"><p><? //echo $batch[csf('grouping')]; ?></p></td>
                <td width="90"><p><? //echo $batch[csf('po_number')]; ?></p></td>
                        <? }
                        ?>
                <td  width="100" title="<? echo $desc[0]; ?>"><p><? echo $batch[csf('item_description')]; ?></p></td>
                <td  width="70" title="<? echo  $desc[2]; ?>"><p><? echo $desc[2]; ?></p></td>
                <td  width="80" title="<? echo $color_library[$batch[csf('color_id')]]; ?>"><p><? echo $color_library[$batch[csf('color_id')]]; ?></p></td>
                <td  align="center" width="90" title="<? echo $batch[csf('batch_no')]; ?>"><p><? echo $batch[csf('batch_no')]; ?></p></td>
                <td  align="center" width="80" title="<? echo $batch[csf('extention_no')]; ?>"><p><? echo $batch[csf('extention_no')]; ?></p></td>
                <td align="right" width="80" title="<? echo $batch[csf('batch_qnty')];  ?>"><p><? echo number_format($batch[csf('batch_qnty')],2);  ?></p></td>
                <td align="right" width="70" ><? echo number_format($batch_prod_qty_arr[$batch[csf('id')]][$batch[csf('prod_id')]][$batch[csf('width_dia_type')]]['drying'],2);  ?></td>
                <td width="100" title="<? echo change_date_format($batch[csf('process_end_date')]); ?>"><p><?  echo change_date_format($batch[csf('process_end_date')])?></p></td>
                <td align="center" width="80" title="<? echo $batch[csf('end_hours')].':'.$batch[csf('end_minutes')];  ?>"><p><? echo $batch[csf('end_hours')].':'.$batch[csf('end_minutes')];   ?></p></td>
                <td align="right" width="60" title="Lot"><p><? echo $yarn_lot_arr[$batch[csf('prod_id')]][$batch[csf('po_id')]]; ?></p></td>
                <td align="center"  title="<? if($batch[csf('batch_against')]==2) echo $batch_against[$batch[csf('batch_against')]]; ?>"><p><?  if($batch[csf('batch_against')]==2) echo $batch_against[$batch[csf('batch_against')]]; else echo '';?> </p></td>
            </tr>
            <? 
            $i++;
            $btq+=$batch[csf('batch_qnty')];
            $tot_prod_btq+=$batch_prod_qty_arr[$batch[csf('id')]][$batch[csf('prod_id')]][$batch[csf('width_dia_type')]]['drying'];

            //}
            } //batchdata froeach
             ?>
            </tbody>
            </table>
             <table class="rpt_table" width="1730" cellpadding="0" cellspacing="0" border="1" rules="all" align="center" id="report_table_footer">
            <tfoot>
            <tr>
                <th width="30">&nbsp;</th>
               <? if($group_by==2 || $group_by==0){ ?>
                 <th width="80">&nbsp;</th>
                <? } ?>
                <? if($group_by==1 || $group_by==0){ ?>
                <th width="80">&nbsp;</th> 
                <? } 
                ?> 
                <th width="50">&nbsp;</th>
                <th width="100">&nbsp;</th> 
                <th width="100">&nbsp;</th> 
                <th width="60">&nbsp;</th> 
                <th width="70">&nbsp;</th>
                <th width="90">&nbsp;</th>
                <th width="100">&nbsp;</th>
                <th width="70">&nbsp;</th>
                <th width="80">&nbsp;</th>
                <th width="90">&nbsp;</th>
                <th width="80">&nbsp;</th>
                <th width="80"><? echo number_format($btq,2); ?></th>
                <th width="70"><? echo number_format($tot_prod_btq,2); ?></th>
                <th width="100">&nbsp;</th>
                <th width="80">&nbsp;</th>
                <th width="60">&nbsp;</th>
                <th>&nbsp;</th>
            </tr>
            </tfoot>
            </table>
            </div>
            </fieldset>
            </div>
            <? }
        else if($cbo_type==9)// Stentering //Not used
        { ?>
            <div style="width:1500px;">
            <fieldset style="width:1500px;">
            <div  align="center"> <strong> <? echo $company_library[$company]; ?> </strong><br><strong> <? echo $search_by_arr[$cbo_type];?> </strong>
            <br>
            <?
                echo  change_date_format($date_from).' '.To.' '.change_date_format($date_to);
            ?>
             </div>
             <table class="rpt_table" width="1520" cellpadding="0" cellspacing="0" border="1" rules="all" align="center" id="table_header_1">
            <thead>
            <tr>
                <th width="30">SL</th>
                   <? if($group_by==2 || $group_by==0){ ?>
                 <th width="80">M/C No</th>
                <? } ?>
                <? if($group_by==1 || $group_by==0){ ?>
                <th width="80">Floor</th> 
                <? } 
                ?> 
                <th width="80"></th>
                <th width="50">Shift</th>
                <th width="100">Buyer</th>
                <th width="100">Booking</th>
                <th width="60">File No</th> 
                <th width="70">Ref. No</th> 
                <th width="90">Order No</th>
                <th width="100">Fabrics Desc</th>
                <th width="70">GSM</th>
                <th width="80">Color Name</th>
                <th width="90">Batch No</th>
                <th width="80">Ext. No</th>
                <th width="80">Batch Qty.</th>
                <th width="100">Process Date</th>
                <th width="80">Process Time</th>
                <th width="60">Yarn Lot</th>
                <th width="80">Remark</th>
                <th>Reprocess</th>
            </tr>
            </thead>
            </table>
            <div style=" max-height:380px; width:1520px; overflow-y:scroll;" id="scroll_body">
            <table class="rpt_table" id="table_body" width="1580" cellpadding="0" cellspacing="0" border="1" rules="all" align="center">
            <tbody>
            <? 
            $i=1;
            $f=0;
            $btq=0;
            $sql_batch_h=sql_select("select batch_id from  pro_fab_subprocess where entry_form=34 and status_active=1 and is_deleted=0 and batch_id>0");
            if($db_type==0) $find_inset="and  FIND_IN_SET(67,68,69,70,73,74,75,77,83,88,92,94,127,128,a.process_id)"; 
                else if($db_type==2) $find_inset="and   ',' || a.process_id || ',' LIKE '%,67,68,69,70,73,74,75,77,83,88,92,94,127,128,%'";
                if($txt_date_from && $txt_date_to)
                {
                    if($db_type==0)
                    {
                $date_from=change_date_format($txt_date_from,'yyyy-mm-dd');
                $date_to=change_date_format($txt_date_to,'yyyy-mm-dd');
                $dates_batch="and  a.batch_date BETWEEN '$date_from' AND '$date_to'";
                    }
                    if($db_type==2)
                    {
                $date_from=change_date_format($txt_date_from,'','',1);
                $date_to=change_date_format($txt_date_to,'','',1);
                $dates_batch="and  a.batch_date BETWEEN '$date_from' AND '$date_to'";
                    }
                }
                $i=1;
                foreach($sql_batch_h as $row_h)
                {
                    if($i!==1) $row_sp.=",";
                    $row_sp.=$row_h[csf('batch_id')];
                    $i++;
                }
            //and a.process_id in(63)
            $w_special=array_chunk(array_unique(explode(",",$row_sp)),999);
            if($w_special!=0)
            {
            $sql_wait=("select a.company_id,a.id,a.batch_no, a.process_id,a.batch_date,a.color_id,a.extention_no,a.batch_against,SUM(b.batch_qnty) AS batch_qnty,b.item_description,b.po_id,b.prod_id,b.width_dia_type,$group_concat,c.job_no_mst,d.job_no_prefix_num,d.buyer_name,c.file_no,c.grouping,f.shift_name,f.process_end_date,f.process_start_date,f.production_date as end_date,f.start_minutes,f.start_hours,f.end_hours,f.end_minutes,f.machine_id,f.floor_id,f.remarks $heat_set from pro_batch_create_dtls b,wo_po_break_down c,wo_po_details_master d,pro_batch_create_mst a,pro_fab_subprocess f,lib_color g where a.company_id=$company and  f.batch_id=a.id $dates_com $jobdata $batch_num $buyerdata $order_no $year_cond $color_name and a.entry_form=0 and  g.id=a.color_id and a.id=b.mst_id and f.entry_form=33 and  a.batch_against in(1,2) and b.po_id=c.id and d.job_no=c.job_no_mst and b.status_active=1 and b.is_deleted=0 and a.status_active=1   and a.is_deleted=0 ");
                    $p=1;
                    foreach($w_special as $sp_row)
                    {
                        if($p==1)  $sql_wait .="and (a.id not in(".implode(',',$sp_row).")"; else  $sql_wait .=" and a.id not in(".implode(',',$sp_row).")";
                        $p++;
                    }
                    $sql_wait .=")";
                    $sql_wait .=" GROUP BY b.po_id, b.item_description,a.company_id, a.id, a.batch_no,a.process_id, a.batch_date,a.color_id, a.extention_no, a.batch_against,b.prod_id, b.width_dia_type,c.job_no_mst, d.job_no_prefix_num, d.buyer_name,c.file_no,c.grouping,f.shift_name, f.process_end_date,f.process_start_date,f.production_date,f.start_minutes,f.start_hours, f.end_hours, f.end_minutes, f.machine_id,  f.floor_id,f.remarks $heat_group  $order_by"; 
                    //echo $sql_wait;
            }
            $batch_chk_arr=array();
            $sql_wait_data=sql_select($sql_wait);
            foreach($sql_wait_data as $batch)
            { 
            if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
            $order_id=$batch[csf('po_id')];
            $color_id=$batch[csf('color_id')];
            $desc=explode(",",$batch[csf('item_description')]); 
            $po_number=implode(",",array_unique(explode(",",$batch[csf('po_number')])));
            $processid=explode(",",$batch[csf('process_id')]);
            $process_name=array(67,68,69);
            $process_count=count($processid);
            //echo $process_count;die;
            $process = explode(",",str_replace("'","",$process_arr));
            //echo $process_sql=count($process_name);
            $arrdif=count(array_diff($processid,$process_name));
            //echo $arrdif;
            //echo $arrdif.'<br>'; 
            //print_r(array_diff($processid,$process_name));echo '<br>';
            //print_r( $process);die;
            //print_r(array_diff($process_name,$processid));die;
            //if (array_diff($processid,$process_name))
            //if ($process_count!==$arrdif)
            //{
            if($group_by!=0)
            {
                if($group_by==1)
                {
                    $group_value=$batch[csf('floor_id')];
                    $group_name="Floor";
                    $group_dtls_value=$floor_arr[$batch[csf('floor_id')]];
                }
                
                else if($group_by==2)
                {
                    $group_value=$batch[csf('machine_id')];
                    $group_name="Machine";
                    $group_dtls_value=$machine_arr[$batch[csf('machine_id')]];//
                }
                if (!in_array($group_value,$group_by_arr) )
                    {
                        //if($k!=1)
                        //{
                        ?>  
                        <tr bgcolor="#EFEFEF">
                            <td colspan="22" align="left" ><b><? echo $group_name; ?> : <? echo $group_dtls_value; ?></b></td>
                        </tr>
                        <?
                        $group_by_arr[]=$group_value; 
                        //}
                       // $k++;
                    }
            }
            ?>
            <tr bgcolor="<? echo $bgcolor;?>" onClick="change_color('tr<? echo $i;?>','<? echo $bgcolor;?>')" id="tr<? echo $i;?>">
                <? if (!in_array($batch[csf('batch_no')],$batch_chk_arr) )
                        { $f++;
                            ?>
                <td width="30"><? echo $f; ?></td>
               <? if($group_by==2 || $group_by==0){ ?>
                <td  align="center" width="80"><div style="width:80px; word-wrap:break-word;"><? echo $machine_arr[$batch[csf('machine_id')]]; ?></div></td>
                <?
                 }
                 if($group_by==1 || $group_by==0){ ?>
               <td width="80"><div style="width:80px; word-wrap:break-word;"><? echo $floor_arr[$batch[csf('floor_id')]]; ?></div></td>
                <? } ?>
                 <td  align="center" width="50"><p><? echo $shift_name[$batch[csf('shift_name')]]; ?></p></td>
                <td  width="100" title="<? echo $buyer_arr[$batch[csf('buyer_name')]]; ?>"><p><? echo $buyer_arr[$batch[csf('buyer_name')]]; ?></p></td>
                 <td  width="100"><p><? echo $batch[csf('booking_no')]; ?></p></td>
                 <td  width="60"><p><? echo $batch[csf('file_no')]; ?></p></td>
                <td  width="70"><p><? echo $batch[csf('grouping')]; ?></p></td>
                <td width="90"><div style="width:90px; word-wrap:break-word;"><? echo $po_number; ?></div></td>
                    <?  
                    $batch_chk_arr[]=$batch[csf('batch_no')];
                        } 
                        else
                           { ?>
                <td width="30"><? //echo $sl; ?></td>
               <? if($group_by==2 || $group_by==0){ ?>
                <td  align="center" width="80"><div style="width:80px; word-wrap:break-word;"><? //echo $machine_arr[$batch[csf('machine_id')]]; ?></div></td>
                <?
                 }
                 if($group_by==1 || $group_by==0){ ?>
               <td width="80"><div style="width:80px; word-wrap:break-word;"><? //echo $floor_arr[$batch[csf('floor_id')]]; ?></div></td>
                <? } ?>
                 <td  align="center" width="50"><p><? //echo $machine_id; ?></p></td>
                <td  width="100"><p><? //echo $buyer_arr[$batch[csf('buyer_name')]]; ?></p></td>
                 <td  width="60"><p><? //echo $batch[csf('file_no')]; ?></p></td>
                <td  width="70"><p><? //echo $batch[csf('grouping')]; ?></p></td>
                <td width="90"><p><? //echo $batch[csf('po_number')]; ?></p></td>
                        <? }
                        ?>
                <td  width="100" title="<? echo $desc[0]; ?>"><p><? echo $batch[csf('item_description')]; ?></p></td>
                <td  width="70" title="<? echo  $desc[2]; ?>"><p><? echo $desc[2]; ?></p></td>
                <td  width="80" title="<? echo $color_library[$batch[csf('color_id')]]; ?>"><p><? echo $color_library[$batch[csf('color_id')]]; ?></p></td>
                <td  align="center" width="90" title="<? echo $batch[csf('batch_no')]; ?>"><p><? echo $batch[csf('batch_no')]; ?></p></td>
                <td  align="center" width="80" title="<? echo $batch[csf('extention_no')]; ?>"><p><? echo $batch[csf('extention_no')]; ?></p></td>
                <td align="right" width="80" title="<? echo $batch[csf('batch_qnty')];  ?>"><p><? echo number_format($batch[csf('batch_qnty')],2);  ?></p></td>
                <td width="100" title="<? echo change_date_format($batch[csf('process_end_date')]); ?>"><p><?  echo change_date_format($batch[csf('process_end_date')])?></p></td>
                <td align="center" width="80" title="<? echo $batch[csf('end_hours')].':'.$batch[csf('end_minutes')];  ?>"><p><? echo $batch[csf('end_hours')].':'.$batch[csf('end_minutes')];   ?></p></td>
                <td align="right" width="60" title="Lot"><p><? echo $yarn_lot_arr[$batch[csf('prod_id')]][$batch[csf('po_id')]]; ?></p></td>
                 <td  width="80" title="<? echo  $batch[csf('remarks')]; ?>"><p><? echo $batch[csf('remarks')]; ?></p></td>
                <td align="center"  title="<? if($batch[csf('batch_against')]==2) echo $batch_against[$batch[csf('batch_against')]]; ?>"><p><?  if($batch[csf('batch_against')]==2) echo $batch_against[$batch[csf('batch_against')]]; else echo '';?> </p></td>
            </tr>
            <? 
            $i++;
            $btq+=$batch[csf('batch_qnty')];
            //}
            } //batchdata froeach
             ?>
            </tbody>
            </table>
             <table class="rpt_table" width="1480" cellpadding="0" cellspacing="0" border="1" rules="all" align="center" id="report_table_footer">
            <tfoot>
            <tr>
                <th width="30">&nbsp;</th>
                <? if($group_by==2 || $group_by==0){ ?>
                 <th width="80">&nbsp;</th>
                <? } ?>
                <? if($group_by==1 || $group_by==0){ ?>
                <th width="80">&nbsp;</th> 
                <? } 
                ?> 
                <th width="50">&nbsp;</th>
                <th width="100">&nbsp;</th> 
                <th width="100">&nbsp;</th> 
                <th width="60">&nbsp;</th> 
                <th width="70">&nbsp;</th>
                <th width="90">&nbsp;</th>
                <th width="100">&nbsp;</th>
                <th width="70">&nbsp;</th>
                <th width="80">&nbsp;</th>
                <th width="90">&nbsp;</th>
                <th width="80">&nbsp;</th>
                <th width="80"><? echo number_format($btq,2); ?></th>
                <th width="100">&nbsp;</th>
                <th width="80">&nbsp;</th>
                <th width="60">&nbsp;</th>
                <th>&nbsp;</th>
            </tr>
            </tfoot>
            </table>
            </div>
            </fieldset>
            </div>
            <? }
        else if($cbo_type==10)// Stentering // Data Comee From Slitting/Squeezing
        { 
            //echo "FDDD";
            ?>
            <div style="width:1650px;">
            <fieldset style="width:1650px;">
            <div  align="center"> <strong> <? echo $company_library[$company]; ?> </strong><br><strong> <? echo $search_by_arr[$cbo_type];?> </strong>
            <br>
            <?
                echo  change_date_format($date_from).' '.To.' '.change_date_format($date_to);
            ?>
             </div>
             <table class="rpt_table" width="1670" cellpadding="0" cellspacing="0" border="1" rules="all" align="center" id="table_header_1">
            <thead>
            <tr>
                <th width="30">SL</th>
               
                <? if($group_by==2 || $group_by==0){ ?>
                  <th width="80">M/C No</th>
                <? } ?>
                <? if($group_by==1 || $group_by==0){ ?>
                <th width="80">Floor</th> 
                <? } 
                ?> 
                <th width="50">Shift</th>
                <th width="80">Job No</th>
                <th width="100">Buyer</th>
                <th width="100">Booking</th>
                <th width="60">File No</th> 
                <th width="70">Ref. No</th> 
                <th width="90">Order No</th>
                <th width="100">Fabrics Desc</th>
                <th width="70">GSM</th>
                <th width="80">Color Name</th>
                <th width="90">Batch No</th>
                <th width="80">Ext. No</th>
                <th width="80">Batch Qty.</th>
                <th width="70">Prod. Qty.</th>
                <th width="100">Process Date</th>
                <th width="80">Process Time</th>
                <th width="60">Yarn Lot</th>
                <th>Reprocess</th>
            </tr>
            </thead>
            </table>
            <div style=" max-height:380px; width:1670px; overflow-y:scroll;" id="scroll_body">
            <table class="rpt_table" id="table_body" width="1650" cellpadding="0" cellspacing="0" border="1" rules="all" align="center">
            <tbody>
            <? 
            $i=1;
            $f=0;
            $btq=0;$tot_prod_btq=0;
            $sql_batch_h=sql_select("select a.batch_id from  pro_fab_subprocess a,pro_fab_subprocess_dtls b where a.entry_form=48 and a.id=b.mst_id and b.width_dia_type in(1,3) and a.status_active=1 and a.is_deleted=0 and a.batch_id>0");
            $i=1;
            foreach($sql_batch_h as $row_h)
            {
                if($i!==1) $row_sent.=",";
                $row_sent.=$row_h[csf('batch_id')];
                $i++;
            }
            //and a.process_id in(63)
            $w_sent=array_chunk(array_unique(explode(",",$row_sent)),999);
            $w_sent=array_chunk(array_unique(explode(",",$row_sent)),999);
            $sql_wait=("select a.company_id,a.id,a.batch_no, a.process_id,a.batch_date,a.color_id,a.extention_no,a.batch_against,SUM(b.batch_qnty) AS batch_qnty,b.item_description,b.po_id,b.prod_id,b.width_dia_type,$group_concat,c.job_no_mst,d.job_no_prefix_num,d.buyer_name,c.file_no,c.grouping,f.shift_name,f.process_end_date,f.process_start_date,f.production_date as end_date,f.start_minutes,f.start_hours,f.end_hours,f.end_minutes,f.machine_id,f.floor_id,f.remarks $sliting_sq from pro_batch_create_dtls b,wo_po_break_down c,wo_po_details_master d,pro_batch_create_mst a,pro_fab_subprocess f where f.batch_id=a.id  and a.id=b.mst_id and b.po_id=c.id and d.job_no=c.job_no_mst  and f.entry_form=30 $company_cond $working_company_cond   $dates_com $jobdata $batch_num $buyerdata $order_no $year_cond $color_name and a.entry_form=0   and  a.batch_against in(1,2) and b.status_active=1 and b.is_deleted=0 and a.status_active=1  and a.is_deleted=0 and b.width_dia_type in(1,3) ");$p=1;
            foreach($w_sent as $dry_row)
            {
                if($p==1)  $sql_wait .="and (a.id not in(".implode(',',$dry_row).")"; else  $sql_wait .=" and a.id not in(".implode(',',$dry_row).")";
                $p++;
            }
            $sql_wait .=")";
              $sql_wait .=" GROUP BY b.po_id, b.item_description,a.company_id, a.id, a.batch_no,a.process_id, a.batch_date,a.color_id, a.extention_no, a.batch_against,b.prod_id, b.width_dia_type,c.job_no_mst, d.job_no_prefix_num, d.buyer_name, c.file_no,c.grouping,f.shift_name,f.process_end_date,f.process_start_date,f.production_date,f.start_minutes,f.start_hours, f.end_hours, f.end_minutes, f.machine_id,f.floor_id, f.remarks $sliting_group  $order_by "; 
            //echo $sql_wait;
            $batch_chk_arr=array();
            $fab_wait=sql_select($sql_wait);
            foreach($fab_wait as $batch)
            { 
            if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
            $order_id=$batch[csf('po_id')];
            $color_id=$batch[csf('color_id')];
            $desc=explode(",",$batch[csf('item_description')]); 
            $po_number=implode(",",array_unique(explode(",",$batch[csf('po_number')])));
            $processid=explode(",",$batch[csf('process_id')]);
            //echo $batch[csf('process_id')];die;
            $result=$batch[csf('result')];
            //$process_arr='66,91';
            $process_name=array(66,91,125);
            $process_count=count($processid);
            $process = explode(",",str_replace("'","",$process_arr));
            $process_sql=count($processid);
            //print_r( $process);die;
            //print_r(array_diff($process_name,$processid));die;

            //if (in_array(65,$processid))
            //{
            if($group_by!=0)
            {
                if($group_by==1)
                {
                    $group_value=$batch[csf('floor_id')];
                    $group_name="Floor";
                    $group_dtls_value=$floor_arr[$batch[csf('floor_id')]];
                }
                
                else if($group_by==2)
                {
                    $group_value=$batch[csf('machine_id')];
                    $group_name="Machine";
                    $group_dtls_value=$machine_arr[$batch[csf('machine_id')]];//
                }
                if (!in_array($group_value,$group_by_arr) )
                    {
                        //if($k!=1)
                        //{
                        ?>  
                        <tr bgcolor="#EFEFEF">
                            <td colspan="23" align="left" ><b><? echo $group_name; ?> : <? echo $group_dtls_value; ?></b></td>
                        </tr>
                        <?
                        $group_by_arr[]=$group_value; 
                        //}
                       // $k++;
                    }
            }
            ?>
            <tr bgcolor="<? echo $bgcolor;?>" onClick="change_color('tr<? echo $i;?>','<? echo $bgcolor;?>')" id="tr<? echo $i;?>">
                <? if (!in_array($batch[csf('batch_no')],$batch_chk_arr) )
                        { $f++;
                            ?>
                <td width="30"><? echo $f; ?></td>
                 <? if($group_by==2 || $group_by==0){ ?>
                <td  align="center" width="80"><div style="width:80px; word-wrap:break-word;"><? echo $machine_arr[$batch[csf('machine_id')]]; ?></div></td>
                <?
                 }
                 if($group_by==1 || $group_by==0){ ?>
               <td width="80"><div style="width:80px; word-wrap:break-word;"><? echo $floor_arr[$batch[csf('floor_id')]]; ?></div></td>
                <? } ?>
                <td  align="center" width="50"><p><? echo $shift_name[$batch[csf('shift_name')]]; ?></p></td> 
                <td  align="center" width="80"><p><? echo $batch[csf('job_no_prefix_num')]; ?></p></td>
                <td  width="100" title="<? echo $buyer_arr[$batch[csf('buyer_name')]]; ?>"><p><? echo $buyer_arr[$batch[csf('buyer_name')]]; ?></p></td>
                 <td  width="100"><p><? echo $batch[csf('booking_no')]; ?></p></td>
                 <td  width="60"><p><? echo $batch[csf('file_no')]; ?></p></td>
                <td  width="70"><p><? echo $batch[csf('grouping')]; ?></p></td>
                <td width="90" title="<? echo $po_number; ?>"><p><? echo $po_number; ?></p></td>
                    <?  
                    $batch_chk_arr[]=$batch[csf('batch_no')];
                        } 
                        else
                           { ?>
                <td width="30"><? //echo $sl; ?></td>
                <? if($group_by==2 || $group_by==0){ ?>
                <td  align="center" width="80"><div style="width:80px; word-wrap:break-word;"><? //echo $machine_arr[$batch[csf('machine_id')]]; ?></div></td>
                <?
                 }
                 if($group_by==1 || $group_by==0){ ?>
               <td width="80"><div style="width:80px; word-wrap:break-word;"><? //echo $floor_arr[$batch[csf('floor_id')]]; ?></div></td>
                <? } ?>
                <td  align="center" width="50"><p><? //echo $machine_id; ?></p></td>
                <td  align="center" width="80"><p><? //echo $machine_id; ?></p></td>
                <td  width="100"><p><? //echo $buyer_arr[$batch[csf('buyer_name')]]; ?></p></td>
                <td  width="100"><p><? //echo $special_row[csf('booking_no')]; ?></p></td>
                <td  width="60"><p><? //echo $batch[csf('file_no')]; ?></p></td>
                <td  width="70"><p><? //echo $batch[csf('grouping')]; ?></p></td>
                <td width="90"><p><? //echo $batch[csf('po_number')]; ?></p></td>
                        <? }
                        ?>
                <td  width="100" title="<? echo $desc[0]; ?>"><p><? echo $batch[csf('item_description')]; ?></p></td>
                <td  width="70" title="<? echo  $desc[2]; ?>"><p><? echo $desc[2]; ?></p></td>
                <td  width="80" title="<? echo $color_library[$batch[csf('color_id')]]; ?>"><p><? echo $color_library[$batch[csf('color_id')]]; ?></p></td>
                <td  align="center" width="90" title="<? echo $batch[csf('batch_no')]; ?>"><p><? echo $batch[csf('batch_no')]; ?></p></td>
                <td  align="center" width="80" title="<? echo $batch[csf('extention_no')]; ?>"><p><? echo $batch[csf('extention_no')]; ?></p></td>
                <td align="right" width="80" title="<? echo $batch[csf('batch_qnty')];  ?>"><p><? echo number_format($batch[csf('batch_qnty')],2);  ?></p></td>
                <td align="right" width="70" ><? echo number_format($batch_prod_qty_arr[$batch[csf('id')]][$batch[csf('prod_id')]][$batch[csf('width_dia_type')]]['sliting'],2);  ?></td>
                <td width="100" title="<? echo change_date_format($batch[csf('process_end_date')]); ?>"><p><?  echo change_date_format($batch[csf('process_end_date')])?></p></td>
                <td align="center" width="80" title="<? echo $batch[csf('end_hours')].':'.$batch[csf('end_minutes')];  ?>"><p><? echo $batch[csf('end_hours')].':'.$batch[csf('end_minutes')];   ?></p></td>
                <td align="right" width="60" title="Lot"><p><? echo $yarn_lot_arr[$batch[csf('prod_id')]][$batch[csf('po_id')]]; ?></p></td>
                <td align="center"  title="<? if($batch[csf('batch_against')]==2) echo $batch_against[$batch[csf('batch_against')]]; ?>"><p><?  if($batch[csf('batch_against')]==2) echo $batch_against[$batch[csf('batch_against')]]; else echo '';?> </p></td>
            </tr>
            <? 
            $i++;
            $btq+=$batch[csf('batch_qnty')];
             $tot_prod_btq+=$batch_prod_qty_arr[$batch[csf('id')]][$batch[csf('prod_id')]][$batch[csf('width_dia_type')]]['sliting'];
            //}
            } //batchdata froeach
             ?>
            </tbody>
            </table>
             <table class="rpt_table" width="1650" cellpadding="0" cellspacing="0" border="1" rules="all" align="center" id="report_table_footer">
            <tfoot>
            <tr>
                <th width="30">&nbsp;</th>
                 <? if($group_by==2 || $group_by==0){ ?>
                 <th width="80">&nbsp;</th>
                <? } ?>
                <? if($group_by==1 || $group_by==0){ ?>
                <th width="80">&nbsp;</th> 
                <? } 
                ?> 
                <th width="50">&nbsp;</th>
                 <th width="80">&nbsp;</th>
                <th width="100">&nbsp;</th>
                <th width="100">&nbsp;</th>
                <th width="60">&nbsp;</th>  
                <th width="70">&nbsp;</th> 
                 <th width="90">&nbsp;</th>
                <th width="100">&nbsp;</th>
                <th width="70">&nbsp;</th>
                <th width="80">&nbsp;</th>
                <th width="90">&nbsp;</th>
                <th width="80">&nbsp;</th>
                <th width="80"><? echo number_format($btq,2); ?></th>
                <th width="70"><? echo number_format($tot_prod_btq,2); ?></th>
                <th width="100">&nbsp;</th>
                <th width="80">&nbsp;</th>
                <th width="60">&nbsp;</th>
                <th>&nbsp;</th>
            </tr>
            </tfoot>
            </table>
            </div>
            </fieldset>
            </div>
      <? }
        else if($cbo_type==0) // All Search Fab. Finishing
        {
            $group_by=str_replace("'",'',$cbo_group_by);
            //echo $group_by;
            ?>
            <div>
            <fieldset style="width:1765px;">
            <div align="center"><strong> <? echo $company_library[$company]; ?> <br>
            <?
                echo  change_date_format($date_from).' '.To.' '.change_date_format($date_to);
            ?>
            </strong><br> <strong>Heat Setting </strong>
             </div>

             <table class="rpt_table" width="1835" cellpadding="0" cellspacing="0" border="1" rules="all" align="center" id="table_header_1">
            <thead>
            <tr>
                <th width="30">SL</th>
                 <? if($group_by==2 || $group_by==0){ ?>
                 <th width="80">M/C No</th>
                 <? } ?>
                <? if($group_by==1 || $group_by==0){ ?>
                <th width="80">Floor</th>  
                 <? } ?>
                
                <th width="50">Shift</th>
                <th width="100">Buyer</th>
                <th width="80">Job</th>
                <th width="100">Booking</th>
                <th width="60">File No</th>
                <th width="70">Ref. No</th>
                <th width="90">Order No</th>
                <th width="100">Fabrics Desc</th>
                <th width="75">GSM</th>
                <th width="70">Dia/Width Type</th>
                <th width="80">Color Name</th>
                <th width="90">Batch No</th>
                <th width="40">Extn. No</th>
                <th width="70">Batch Qty.</th>
                <th width="70">Prod. Qty.</th>
                <th width="50">Lot No</th>
                <th width="75">Start Date & Time</th>
                <th width="75">End Date & Time</th>
                <th width="70">Time Used</th>
                <th width="60">Remark</th>
                <th>Reprocess</th>
            </tr>
            </thead>
            </table>
            <div style=" max-height:350px; width:1835px; overflow-y:scroll;" id="scroll_body">
            <table class="rpt_table" id="table_body" width="1815" cellpadding="0" cellspacing="0" border="1" rules="all" align="center">
            <tbody>
            <? 
            $i=1;
            $f=0;$k=1;
            $btq=0;
            $batch_chk_arr=array(); $group_by_arr=array();
            //echo $sql_heat;
            $heatsetting=sql_select($sql_heat); $tot_prod_qty_heat=0;
            foreach($heatsetting as $heat_row)
            { 
            if ($i%2==0)  
            $bgcolor="#E9F3FF";
            else
            $bgcolor="#FFFFFF";
            if($group_by!=0)
            {
                if($group_by==1)
                {
                    $group_value=$heat_row[csf('floor_id')];
                    $group_name="Floor";
                    $group_dtls_value=$floor_arr[$heat_row[csf('floor_id')]];
                }
                
                else if($group_by==2)
                {
                    $group_value=$heat_row[csf('machine_id')];
                    $group_name="Machine";
                    $group_dtls_value=$machine_arr[$heat_row[csf('machine_id')]];
                }
                if (!in_array($group_value,$group_by_arr) )
                    {
                        if($k!=1)
                        { ?>
                        
                        <tr  bgcolor="#D4D4D4" >
                       <td align="left" colspan="24"><Strong> Batch Sub Total:</Strong> <b><? echo number_format($btq_heat,2); ?> </b> &nbsp;&nbsp;&nbsp;<b> Prod. Sub Total:<? echo number_format($tot_prod_qty_heat,2); ?> </b></td>
                       
                        </tr>                                
                            <?
                            unset($btq_heat);unset($tot_prod_qty_heat);
                        }
                        ?>  
                    
                    
                        <tr bgcolor="#EFEFEF">
                            <td colspan="24" align="left" ><b><? echo $group_name; ?> : <? echo $group_dtls_value; ?></b></td>
                        </tr>
                        <?
                        $group_by_arr[]=$group_value;            
                        $k++;
                        
                    }
                    
            }

            $order_id=$heat_row[csf('po_id')];
            $color_id=$heat_row[csf('color_id')];
            $desc=explode(",",$heat_row[csf('item_description')]); 
            $po_number=implode(",",array_unique(explode(",",$heat_row[csf('po_number')]))); 
            ?>
            <tr bgcolor="<? echo $bgcolor;?>" onClick="change_color('tr<? echo $i;?>','<? echo $bgcolor;?>')" id="tr<? echo $i;?>">
                  <? if (!in_array($heat_row[csf('batch_no')],$batch_chk_arr) )
                        { $f++;
                            ?>
                <td width="30"><? echo $f; ?></td>
                <? if($group_by==2 || $group_by==0){ ?>
                <td  align="center" width="80" title="<? echo $machine_arr[$heat_row[csf('machine_id')]]; ?>"><p><? echo $machine_arr[$heat_row[csf('machine_id')]]; ?></p></td>
                <?
                 } 
                 if($group_by==1 || $group_by==0){ ?>
                <td width="80"><p><? echo $floor_arr[$heat_row[csf('floor_id')]]; ?></p></td>
                <? }
                ?>
                <td  align="center" width="50" ><p><? echo $shift_name[$heat_row[csf('shift_name')]]; ?></p></td>
                
                <td  width="100" title="<? echo $buyer_arr[$heat_row[csf('buyer_name')]]; ?>"><p><? echo $buyer_arr[$heat_row[csf('buyer_name')]]; ?></p></td>
                <td  width="80" title="<? echo  $heat_row[csf('job_no_prefix_num')]; ?>"><p><? echo $heat_row[csf('job_no_prefix_num')]; ?></p></td>
                 <td  width="100"><p><? echo $heat_row[csf('booking_no')]; ?></p></td>
                <td  width="60"><p><? echo $heat_row[csf('file_no')]; ?></p></td>
                <td  width="70"><p><? echo $heat_row[csf('grouping')]; ?></p></td>
                <td  width="90"><div style="width:90px; word-wrap:break-word;"><? echo $po_number; ?></div></td>
                
                <?  $batch_chk_arr[]=$heat_row[csf('batch_no')];
                        }
                        else
                        { ?>
                 <td width="30"><? //echo $f; ?></td>
                <? if($group_by==2 || $group_by==0){ ?>
                <td  align="center" width="80" ><p><? //echo $machine_arr[$heat_row[csf('machine_id')]]; ?></p></td>
                <?
                 }
                 if($group_by==1 || $group_by==0){ ?>
                <td width="80"><p><? //echo $floor_arr[$heat_row[csf('floor_id')]]; ?></p></td>
                <? }
                ?>
                <td  align="center" width="50" ><p><? //echo $shift_name[$heat_row[csf('shift_name')]]; ?></p></td>
                
                <td  width="100"><p><? //echo $buyer_arr[$heat_row[csf('buyer_name')]]; ?></p></td>
                <td  width="80"><p><? //echo $heat_row[csf('job_no_prefix_num')]; ?></p></td>
                <td  width="100"><p><? //echo $heat_row[csf('booking_no')]; ?></p></td>
                <td  width="60"><p><? //echo $heat_row[csf('file_no')]; ?></p></td>
                <td  width="70"><p><? //echo $heat_row[csf('grouping')]; ?></p></td>
                <td  width="90"><div style="width:90px; word-wrap:break-word;"><? //echo $po_number; ?></div></td>
                            
                <? }
                ?>
                <td  width="100" title="<? echo $desc[0]; ?>"><p><? echo $heat_row[csf('item_description')]; ?></p></td>
                <td  width="70" title="<? echo   $desc[2]; ?>"><p><? echo  $desc[2]; ?></p></td>
                <td  width="75" title="<? ?>"><p><? echo $fabric_typee[$heat_row[csf('width_dia_type')]];;?></p></td>
                <td  width="80" title="<? echo $color_library[$heat_row[csf('color_id')]]; ?>"><p><? echo $color_library[$heat_row[csf('color_id')]]; ?></p></td>
                <td  align="center" width="90" title="<? echo $heat_row[csf('batch_no')]; ?>"><p><? echo $heat_row[csf('batch_no')]; ?></p></td>
                <td  align="center" width="40" title="<? echo $heat_row[csf('extention_no')]; ?>"><p><? echo $heat_row[csf('extention_no')]; ?></p></td>
                <td align="right" width="70"><? echo number_format($heat_row[csf('batch_qnty')],2);  ?></td>
                 <td align="right" width="70" ><? echo number_format($batch_prod_qty_arr[$heat_row[csf('id')]][$heat_row[csf('prod_id')]][$heat_row[csf('width_dia_type')]]['heat'],2);
                   ?></td>
                <td align="left" width="50" title="<? echo $yarn_lot_arr[$heat_row[csf('prod_id')]][$heat_row[csf('po_id')]]; ?>"><p><? echo $yarn_lot_arr[$heat_row[csf('prod_id')]][$heat_row[csf('po_id')]];  ?></p></td>
                <td width="75" title="Process Start Date"><div style="width:75px; word-wrap:break-word;"><?  echo change_date_format($heat_row[csf('process_start_date')]).', '.$heat_row[csf('start_hours')].':'.$heat_row[csf('start_minutes')]; ?></div></td>
                <td width="75" title="Process End Date"><div style="width:75px; word-wrap:break-word;"><?  echo change_date_format($heat_row[csf('end_date')]).', '.$heat_row[csf('end_hours')].':'.$heat_row[csf('end_minutes')]; ?></div></td>
                 <td width="70" align="center"><div style="width:70px; word-wrap:break-word;"><?
                        $end_time=$heat_row[csf('end_hours')].':'.$heat_row[csf('end_minutes')];
                        $start_time=$heat_row[csf('start_hours')].':'.$heat_row[csf('start_minutes')];
                        
                        $new_date_time_start=($heat_row[csf('process_start_date')].' '.$start_time.':'.'00');
                        $new_date_time_end=($heat_row[csf('end_date')].' '.$end_time.':'.'00');
                        $total_time=datediff(n,$new_date_time_start,$new_date_time_end);
                        echo floor($total_time/60).":".$total_time%60; ?></div></td>
                <td align="center" width="60"><p><?   echo $batch[csf('remarks')]; ?> </p>
                 </td>
                <td align="center" title="<?   if($heat_row[csf('batch_against')]==2) echo $batch_against[$heat_row[csf('batch_against')]]; ?>"><p><?  if($heat_row[csf('batch_against')]==2) echo $batch_against[$heat_row[csf('batch_against')]]; ?></p> </td>
            </tr>
            <? 
            $i++;
                $tot_prod_qty_heat+=$batch_prod_qty_arr[$heat_row[csf('id')]][$heat_row[csf('prod_id')]][$heat_row[csf('width_dia_type')]]['heat'];
            $btq_heat+=$heat_row[csf('batch_qnty')];
            } //batchdata froeach

            if($group_by!=0)
            {
                ?>
                        <tr  bgcolor="#D4D4D4" >
                       <td align="left" colspan="24"><Strong> Batch Sub Total:</Strong> <b><? echo number_format($btq_heat,2); ?> </b> &nbsp;&nbsp;&nbsp;<b>Prod. Sub Total:&nbsp;<? echo number_format($tot_prod_qty_heat,2); ?> </b> </td>
                        </tr>                                
            <?
            }
             ?>
                  <tr bgcolor="#C2DCFF">
                       <td colspan="24" align="center"><strong>Slitting/Squeezing</strong></td>
                 </tr>
                 <?
                 $f=0;$k=1;$tot_prod_qty_siltting=0;
                 $batch_chk_arr=array(); $group_by_arr=array();
                 $slitting_data=sql_select($sql_slitting);
                 foreach($slitting_data as $slitting_row)
                 {
                if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
                $order_id=$slitting_row[csf('po_id')];
                $color_id=$slitting_row[csf('color_id')];
                $desc=explode(",",$slitting_row[csf('item_description')]); 
                $po_number=implode(",",array_unique(explode(",",$slitting_row[csf('po_number')])));
                
            if($group_by!=0)
            {
                if($group_by==1)
                {
                    $group_value=$slitting_row[csf('floor_id')];
                    $group_name="Floor";
                    $group_dtls_value=$floor_arr[$slitting_row[csf('floor_id')]];
                }
                
                else if($group_by==2)
                {
                    $group_value=$slitting_row[csf('machine_id')];
                    $group_name="Machine";
                    $group_dtls_value=$machine_arr[$slitting_row[csf('machine_id')]];
                }
                if (!in_array($group_value,$group_by_arr) )
                    {
                        if($k!=1)
                        { ?>
                        
                        <tr  bgcolor="#D4D4D4" >
                       <td align="left" colspan="24"><Strong> Sub Total:</Strong> <b><? echo number_format($btq_siltting,2); ?> </b>&nbsp;&nbsp;&nbsp;<b> Prod. Sub Total:<? echo number_format($tot_prod_qty_siltting,2); ?> </b></td>
                        </tr>                                
                            <?
                            unset($btq_siltting);unset($tot_prod_qty_siltting);
                        }
                        ?>  
                        <tr bgcolor="#EFEFEF">
                            <td colspan="24" align="left" ><b><? echo $group_name; ?> : <? echo $group_dtls_value; ?></b></td>
                        </tr>
                        <?
                        $group_by_arr[]=$group_value;            
                        $k++;
                    }
            }
            ?>
            <tr bgcolor="<? echo $bgcolor;?>" onClick="change_color('tr<? echo $i;?>','<? echo $bgcolor;?>')" id="tr<? echo $i;?>">
                <? if (!in_array($slitting_row[csf('batch_no')],$batch_chk_arr) )
                        { $f++;
                        ?>
                <td width="30"><? echo $f; ?></td>
                 <? if($group_by==2 || $group_by==0){ ?>
                <td  align="center" width="80" title="<? echo $machine_arr[$slitting_row[csf('machine_id')]]; ?>"><p><? echo $machine_arr[$slitting_row[csf('machine_id')]]; ?></p></td>
                <?
                 }
                 if($group_by==1 || $group_by==0){ ?>
                <td width="80"><p><? echo $floor_arr[$slitting_row[csf('floor_id')]]; ?></p></td>
                <? }
                ?>
                <td  align="center" width="50" ><p><? echo $shift_name[$slitting_row[csf('shift_name')]]; ?></p></td>
                <td  width="100" title="<? echo $buyer_arr[$slitting_row[csf('buyer_name')]]; ?>"><p><? echo $buyer_arr[$slitting_row[csf('buyer_name')]]; ?></p></td>
                <td  width="80"><p><? echo $slitting_row[csf('job_no_prefix_num')]; ?></p></td>
                <td  width="100"><p><? echo $slitting_row[csf('booking_no')]; ?></p></td>
                <td  width="60"><p><? echo $slitting_row[csf('file_no')]; ?></p></td>
                <td  width="70"><p><? echo $slitting_row[csf('grouping')]; ?></p></td>
                <td  width="90" title="<? echo $po_number; ?>"><p><? echo $po_number; ?></p></td>
                <?
                $batch_chk_arr[]=$slitting_row[csf('batch_no')];
                }
                else
                { ?>
                <td width="30"><? //echo $f; ?></td>
                <? if($group_by==2 || $group_by==0){ ?>
                <td  align="center" width="80" ><p><? //echo $machine_arr[$slitting_row[csf('machine_id')]]; ?></p></td>
                <?
                 }
                 if($group_by==1 || $group_by==0){ ?>
                <td width="80"><p><? //echo $floor_arr[$slitting_row[csf('floor_id')]]; ?></p></td>
                <? }
                ?>
                <td  align="center" width="50" ><p><? //echo $shift_name[$slitting_row[csf('shift_name')]]; ?></p></td>
                <td  width="100"><p><? //echo $buyer_arr[$slitting_row[csf('buyer_name')]]; ?></p></td>
                <td  width="80"><p><? //echo $slitting_row[csf('job_no_prefix_num')]; ?></p></td>
                <td  width="100"><p><? //echo $heat_row[csf('booking_no')]; ?></p></td>
                <td  width="60"><p><? //echo $slitting_row[csf('file_no')]; ?></p></td>
                <td  width="70"><p><? //echo $slitting_row[csf('grouping')]; ?></p></td>
                <td  width="90"><p><? //echo $po_number; ?></p></td>
                <? }
                ?>
                <td  width="100" title="<? echo $desc[0]; ?>"><p><? echo $desc[0]; ?></p></td>
                <td  width="70" title="<? echo   $desc[2]; ?>"><p><? echo  $desc[2]; ?></p></td>
                <td  width="75" title="<? ?>"><p><? echo $fabric_typee[$slitting_row[csf('width_dia_type')]];?></p></td>
                <td  width="80" title="<? echo $color_library[$slitting_row[csf('color_id')]]; ?>"><p><? echo $color_library[$slitting_row[csf('color_id')]]; ?></p></td>
                <td  align="center" width="90" title="<? echo $slitting_row[csf('batch_no')]; ?>"><p><? echo $slitting_row[csf('batch_no')]; ?></p></td>
                <td  align="center" width="40" title="<? echo $slitting_row[csf('extention_no')]; ?>"><p><? echo $slitting_row[csf('extention_no')]; ?></p></td>
                <td align="right" width="70" title="<? echo $slitting_row[csf('batch_qnty')];  ?>"><? echo number_format($slitting_row[csf('batch_qnty')],2);  ?></td>
                 <td align="right" width="70" ><? echo number_format($batch_prod_qty_arr[$slitting_row[csf('id')]][$slitting_row[csf('prod_id')]][$slitting_row[csf('width_dia_type')]]['sliting'],2);  ?></td>
                <td align="left" width="50" title="<? echo $yarn_lot_arr[$slitting_row[csf('prod_id')]][$slitting_row[csf('po_id')]]; ?>"><p><? echo $yarn_lot_arr[$slitting_row[csf('prod_id')]][$slitting_row[csf('po_id')]];  ?></p></td>
                <td width="75" title="Process Start Date"><div style="width:75px; word-wrap:break-word;"><?  echo change_date_format($slitting_row[csf('process_start_date')]).', '.$slitting_row[csf('start_hours')].':'.$slitting_row[csf('start_minutes')]; ?></div></td>
                <td width="75" title="Process End Date"><div style="width:75px; word-wrap:break-word;"><?  echo change_date_format($slitting_row[csf('end_date')]).', '.$slitting_row[csf('end_hours')].':'.$slitting_row[csf('end_minutes')]; ?></div></td>
                 <td width="70" align="center"><div style="width:70px; word-wrap:break-word;"><?
                        $end_time=$slitting_row[csf('end_hours')].':'.$slitting_row[csf('end_minutes')];
                        $start_time=$slitting_row[csf('start_hours')].':'.$slitting_row[csf('start_minutes')];
                        
                        $new_date_time_start=($slitting_row[csf('process_start_date')].' '.$start_time.':'.'00');
                        $new_date_time_end=($slitting_row[csf('end_date')].' '.$end_time.':'.'00');
                        $total_time=datediff(n,$new_date_time_start,$new_date_time_end);
                        echo floor($total_time/60).":".$total_time%60; ?></div></td>
                <td align="center" width="60"><p><?   echo $slitting_row[csf('remarks')];?> </p>
                 </td>
                <td align="center" title="<?   if($slitting_row[csf('batch_against')]==2) echo $batch_against[$slitting_row[csf('batch_against')]]; ?>"><p><?  if($slitting_row[csf('batch_against')]==2) echo $batch_against[$slitting_row[csf('batch_against')]]; ?></p> </td>
            </tr>
            <?
            $i++;
            $btq_siltting+=$slitting_row[csf('batch_qnty')];
            $tot_prod_qty_siltting+=$batch_prod_qty_arr[$slitting_row[csf('id')]][$slitting_row[csf('prod_id')]][$slitting_row[csf('width_dia_type')]]['sliting'];
             }
             if($group_by!=0)
            {
                ?>
                    <tr  bgcolor="#D4D4D4" >
                       <td align="left" colspan="24"><Strong> Sub Total:</Strong> <b><? echo number_format($btq_siltting,2); ?> </b>
                       &nbsp;&nbsp;&nbsp;<b> Prod. Sub Total:<? echo number_format($tot_prod_qty_siltting,2); ?> </b>
                       </td>
                    </tr>                                
                            <?
            }
            ?>
                  <tr bgcolor="#C2DCFF">
                  <td colspan="24" align="center"><strong>Drying</strong></td>
                 </tr>
                  <?
                  $f=0;$tot_prod_qty_drying=0;
                 $drying_data=sql_select($sql_drying);$batch_chk_arr=array();$group_by_arr=array();
                 foreach($drying_data as $drying_row)
                 {
                 if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
                $order_id=$drying_row[csf('po_id')];
                $color_id=$drying_row[csf('color_id')];
                $desc=explode(",",$drying_row[csf('item_description')]); 
                $po_number=implode(",",array_unique(explode(",",$drying_row[csf('po_number')]))); 
                
            if($group_by!=0)
            {
                if($group_by==1)
                {
                    $group_value=$slitting_row[csf('floor_id')];
                    $group_name="Floor";
                    $group_dtls_value=$floor_arr[$slitting_row[csf('floor_id')]];
                }
                
                else if($group_by==2)
                {
                    $group_value=$slitting_row[csf('machine_id')];
                    $group_name="Machine";
                    $group_dtls_value=$machine_arr[$slitting_row[csf('machine_id')]];
                }
                if (!in_array($group_value,$group_by_arr) )
                    {
                        if($k!=1)
                        { ?>
                        
                        <tr  bgcolor="#D4D4D4" >
                       <td align="left" colspan="24"><Strong> Sub Total:</Strong> <b><? echo number_format($btq_drying,2); ?> </b>
                       &nbsp;&nbsp;&nbsp;<b> Prod. Sub Total:<? echo number_format($tot_prod_qty_drying,2); ?> </b>
                       </td>
                        
                        </tr>                                
                            <?
                            unset($btq_drying);unset($tot_prod_qty_drying);
                        }
                        ?>  
                    
                    
                        <tr bgcolor="#EFEFEF">
                            <td colspan="24" align="left" ><b><? echo $group_name; ?> : <? echo $group_dtls_value; ?></b></td>
                        </tr>
                        <?
                        $group_by_arr[]=$group_value;            
                        $k++;
                    }
            }
            ?>
            <tr bgcolor="<? echo $bgcolor;?>" onClick="change_color('tr<? echo $i;?>','<? echo $bgcolor;?>')" id="tr<? echo $i;?>">
                  <? if (!in_array($drying_row[csf('batch_no')],$batch_chk_arr) )
                        { $f++;
                            ?>
                <td width="30"><? echo $f; ?></td>
               
                 
                  <? if($group_by==2 || $group_by==0){ ?>
                 <td  align="center" width="80" title="<? echo $machine_arr[$drying_row[csf('machine_id')]]; ?>"><p><? echo $machine_arr[$drying_row[csf('machine_id')]]; ?></p></td>
                <?
                 }
                 if($group_by==1 || $group_by==0){ ?>
               <td width="80"><p><? echo $floor_arr[$drying_row[csf('floor_id')]]; ?></p></td>
                <? }
                ?>
                
                 <td  align="center" width="50" ><p><? echo $shift_name[$drying_row[csf('shift_name')]]; ?></p></td>
                <td  width="100" title="<? echo $buyer_arr[$drying_row[csf('buyer_name')]]; ?>"><p><? echo $buyer_arr[$drying_row[csf('buyer_name')]]; ?></p></td>
                <td  width="80" title="<? echo  $drying_row[csf('job_no_prefix_num')]; ?>"><p><? echo $drying_row[csf('job_no_prefix_num')]; ?></p></td>
                <td  width="100"><p><? echo $drying_row[csf('booking_no')]; ?></p></td>
                <td  width="60"><p><? echo $drying_row[csf('file_no')]; ?></p></td>
                <td  width="70"><p><? echo $drying_row[csf('grouping')]; ?></p></td>
                <td  width="90" title="<? echo $po_number; ?>"><p><? echo $po_number; ?></p></td>
                <?      $batch_chk_arr[]=$drying_row[csf('batch_no')];
                        }
                        else
                        { ?>
                <td width="30"><? //echo $f; ?></td>
                  <? if($group_by==2 || $group_by==0){ ?>
                 <td  align="center" width="80"><p><? //echo $machine_arr[$drying_row[csf('machine_id')]]; ?></p></td>
                <?
                 }
                 if($group_by==1 || $group_by==0){ ?>
               <td width="80"><p><? // echo $floor_arr[$drying_row[csf('floor_id')]]; ?></p></td>
                <? }
                ?>
                <td  align="center" width="50" ><p><? //echo $shift_name[$drying_row[csf('shift_name')]]; ?></p></td>
                <td  width="100"><p><? //echo $buyer_arr[$drying_row[csf('buyer_name')]]; ?></p></td>
                <td  width="80"><p><? //echo $drying_row[csf('job_no_prefix_num')]; ?></p></td>
                <td  width="100"><p><? //echo $heat_row[csf('booking_no')]; ?></p></td>
                <td  width="60"><p><? //echo $drying_row[csf('file_no')]; ?></p></td>
                <td  width="70"><p><? //echo $drying_row[csf('grouping')]; ?></p></td>
                <td  width="90"><p><? //echo $po_number; ?></p></td>    
                            
                <? }
                ?>
                <td  width="100" title="<? echo $desc[0]; ?>"><p><? echo $drying_row[csf('item_description')]; ?></p></td>
                <td  width="70" title="<? echo   $desc[2]; ?>"><p><? echo  $desc[2]; ?></p></td>
                <td  width="75" title="<? ?>"><p><? echo $fabric_typee[$drying_row[csf('width_dia_type')]];?></p></td>
                <td  width="80" title="<? echo $color_library[$drying_row[csf('color_id')]]; ?>"><p><? echo $color_library[$drying_row[csf('color_id')]]; ?></p></td>
                <td  align="center" width="90" title="<? echo $drying_row[csf('batch_no')]; ?>"><p><? echo $drying_row[csf('batch_no')]; ?></p></td>
                <td  align="center" width="40" title="<? echo $drying_row[csf('extention_no')]; ?>"><p><? echo $drying_row[csf('extention_no')]; ?></p></td>
                <td align="right" width="70" title="<? echo $drying_row[csf('batch_qnty')];  ?>"><? echo number_format($drying_row[csf('batch_qnty')],2);  ?></td>
                 <td align="right" width="70" ><? echo number_format($batch_prod_qty_arr[$drying_row[csf('id')]][$drying_row[csf('prod_id')]][$drying_row[csf('width_dia_type')]]['drying'],2);  ?></td>
                <td align="left" width="50" title="<? echo $yarn_lot_arr[$drying_row[csf('prod_id')]][$drying_row[csf('po_id')]]; ?>"><p><? echo $yarn_lot_arr[$drying_row[csf('prod_id')]][$drying_row[csf('po_id')]];  ?></p></td>
                <td width="75" title="Process Start Date"><div style="width:75px; word-wrap:break-word;"><?  echo change_date_format($drying_row[csf('process_start_date')]).', '.$drying_row[csf('start_hours')].':'.$drying_row[csf('start_minutes')]; ?></div></td>
                <td width="75" title="Process End Date"><div style="width:75px; word-wrap:break-word;"><?  echo change_date_format($drying_row[csf('end_date')]).', '.$drying_row[csf('end_hours')].':'.$drying_row[csf('end_minutes')]; ?></div></td>
                 <td width="70" align="center"><div style="width:70px; word-wrap:break-word;"><?
                        $end_time=$drying_row[csf('end_hours')].':'.$drying_row[csf('end_minutes')];
                        $start_time=$drying_row[csf('start_hours')].':'.$drying_row[csf('start_minutes')];
                        
                        $new_date_time_start=($drying_row[csf('process_start_date')].' '.$start_time.':'.'00');
                        $new_date_time_end=($drying_row[csf('end_date')].' '.$end_time.':'.'00');
                        $total_time=datediff(n,$new_date_time_start,$new_date_time_end);
                        echo floor($total_time/60).":".$total_time%60; ?></div></td>
                <td align="center" width="60"><p><?  echo $drying_row[csf('remarks')]; ?> </p>
                 </td>
                <td align="center" title="<?   if($drying_row[csf('batch_against')]==2) echo $batch_against[$drying_row[csf('batch_against')]]; ?>"><p><?  if($drying_row[csf('batch_against')]==2) echo $batch_against[$drying_row[csf('batch_against')]]; ?></p> </td>
            </tr>
            <?
            $i++;
            $btq_drying+=$drying_row[csf('batch_qnty')];
            $tot_prod_qty_drying+=$batch_prod_qty_arr[$drying_row[csf('id')]][$drying_row[csf('prod_id')]][$drying_row[csf('width_dia_type')]]['drying'];

             }
             if($group_by!=0)
            {
                ?>
                    <tr  bgcolor="#D4D4D4" >
                       <td align="left" colspan="24"><Strong> Sub Total:</Strong> <b><? echo number_format($btq_drying,2); ?> </b>
                        &nbsp;&nbsp;&nbsp;<b> Prod. Sub Total:<? echo number_format($tot_prod_qty_drying,2); ?> </b>
                       </td>
                       
                    </tr>                                
                            <?
            }
            ?>
                
                  <tr bgcolor="#C2DCFF">
                      <td colspan="24" align="center"><strong>Stentering</strong></td>
                 </tr>
                   <?
                   $f=0;$tot_prod_qty_stenter=0;
                 $stentering_data=sql_select($sql_stentering); $batch_chk_arr=array();
                 foreach($stentering_data as $sten_row)
                 {
                 if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
                $order_id=$sten_row[csf('po_id')];
                $color_id=$sten_row[csf('color_id')];
                $desc=explode(",",$sten_row[csf('item_description')]); 
                $po_number=implode(",",array_unique(explode(",",$sten_row[csf('po_number')])));
            if($group_by!=0)
            {
                if($group_by==1)
                {
                    $group_value=$sten_row[csf('floor_id')];
                    $group_name="Floor";
                    $group_dtls_value=$floor_arr[$sten_row[csf('floor_id')]];
                }
                
                else if($group_by==2)
                {
                    $group_value=$sten_row[csf('machine_id')];
                    $group_name="Machine";
                    $group_dtls_value=$machine_arr[$sten_row[csf('machine_id')]];
                }
                if (!in_array($group_value,$group_by_arr) )
                    {
                        if($k!=1)
                        { ?>
                        
                        <tr  bgcolor="#D4D4D4" >
                       <td align="left" colspan="23"><Strong> Sub Total:</Strong> <b><? echo number_format($tot_btq_stenter,2); ?> </b>
                        &nbsp;&nbsp;&nbsp;<b> Prod. Sub Total:<? echo number_format($tot_prod_qty_stenter,2); ?> </b>
                       </td>
                        </tr>                                
                            <?
                            unset($tot_btq_stenter);unset($tot_prod_qty_stenter);
                        }
                        ?>  
                    
                    
                        <tr bgcolor="#EFEFEF">
                            <td colspan="24" align="left" ><b><? echo $group_name; ?> : <? echo $group_dtls_value; ?></b></td>
                        </tr>
                        <?
                        $group_by_arr[]=$group_value;            
                        $k++;
                    }
            }
                 $stenter_grouping_arr_val=$sten_row[csf('batch_no')].$sten_row[csf('machine_id')].$sten_row[csf('floor_id')]; 
                 ?>
            <tr bgcolor="<? echo $bgcolor;?>" onClick="change_color('tr<? echo $i;?>','<? echo $bgcolor;?>')" id="tr<? echo $i;?>">
                 <? if (!in_array($stenter_grouping_arr_val,$batch_chk_arr) )
                        { $f++;
                            ?>
               
                <td width="30"><? echo $f; ?></td>
                <? if($group_by==2 || $group_by==0){ ?>
                  <td  align="center" width="80" title="<? echo $machine_arr[$sten_row[csf('machine_id')]]; ?>"><p><? echo $machine_arr[$sten_row[csf('machine_id')]]; ?></p></td>
                <?
                 }
                 if($group_by==1 || $group_by==0){ ?>
                 <td width="80"><p><? echo $floor_arr[$sten_row[csf('floor_id')]]; ?></p></td>
                <? }
                ?>
                <td  align="center" width="50" ><p><? echo $shift_name[$sten_row[csf('shift_name')]]; ?></p></td>
                <td  width="100" title="<? echo $buyer_arr[$sten_row[csf('buyer_name')]]; ?>"><p><? echo $buyer_arr[$sten_row[csf('buyer_name')]]; ?></p></td>
                <td  width="80" title="<? echo  $sten_row[csf('job_no_prefix_num')]; ?>"><p><? echo $sten_row[csf('job_no_prefix_num')]; ?></p></td>
                <td  width="100"><p><? echo $sten_row[csf('booking_no')]; ?></p></td>
                <td  width="60"><p><? echo $sten_row[csf('file_no')]; ?></p></td>
                <td  width="70"><p><? echo $sten_row[csf('grouping')]; ?></p></td>
                <td  width="90"><div style="width:90px; word-wrap:break-word;"><? echo $po_number; ?></div></td>
                <?
                        $batch_chk_arr[]=$stenter_grouping_arr_val;
                        }
                        else
                        { ?>
                <td width="30"><? //echo $i; ?></td>
                <? if($group_by==2 || $group_by==0){ ?>
                  <td  align="center" width="80"><p><? //echo $machine_arr[$sten_row[csf('machine_id')]]; ?></p></td>
                <?
                 }
                 if($group_by==1 || $group_by==0){ ?>
                 <td width="80"><p><? //echo $floor_arr[$sten_row[csf('floor_id')]]; ?></p></td>
                <? }
                ?>
                <td  align="center" width="50" ><p><? // echo $shift_name[$drying_row[csf('shift_name')]]; ?></p></td>
                <td  width="100"><p><? //echo $buyer_arr[$sten_row[csf('buyer_name')]]; ?></p></td>
                <td  width="80"><p><? //echo $sten_row[csf('job_no_prefix_num')]; ?></p></td>
                <td  width="100"><p><? //echo $heat_row[csf('booking_no')]; ?></p></td>
                <td  width="60"><p><? // echo $sten_row[csf('file_no')]; ?></p></td>
                <td  width="70"><p><? //echo $sten_row[csf('grouping')]; ?></p></td>
                <td  width="90"><div style="width:90px; word-wrap:break-word;"><? //echo $po_number; ?></div></td>  
                    <?  }
                ?>
                <td  width="100" title="<? echo $desc[0]; ?>"><p><? echo $sten_row[csf('item_description')]; ?></p></td>
                <td  width="70" title="<? echo   $desc[2]; ?>"><p><? echo  $desc[2]; ?></p></td>
                <td  width="75" title="<? ?>"><p><? echo $fabric_typee[$sten_row[csf('width_dia_type')]];?></p></td>
                <td  width="80" title="<? echo $color_library[$sten_row[csf('color_id')]]; ?>"><p><? echo $color_library[$sten_row[csf('color_id')]]; ?></p></td>
                <td  align="center" width="90" title="<? echo $sten_row[csf('batch_no')]; ?>"><p><? echo $sten_row[csf('batch_no')]; ?></p></td>
                <td  align="center" width="40" title="<? echo $sten_row[csf('extention_no')]; ?>"><p><? echo $sten_row[csf('extention_no')]; ?></p></td>
                <td align="right" width="70" title="<? echo $sten_row[csf('batch_qnty')];  ?>"><? echo number_format($sten_row[csf('batch_qnty')],2);  ?></td>
                <td align="right" width="70" ><? echo number_format($batch_prod_qty_arr[$sten_row[csf('id')]][$sten_row[csf('prod_id')]][$sten_row[csf('width_dia_type')]]['stenter'],2);  ?></td>
                <td align="left" width="50" title="<? echo $yarn_lot_arr[$sten_row[csf('prod_id')]][$sten_row[csf('po_id')]]; ?>"><p><? echo $yarn_lot_arr[$sten_row[csf('prod_id')]][$sten_row[csf('po_id')]];  ?></p></td>
                <td width="75" title="Process Start Date"><div style="width:75px; word-wrap:break-word;"><?  echo change_date_format($sten_row[csf('process_start_date')]).', '.$sten_row[csf('start_hours')].':'.$sten_row[csf('start_minutes')]; ?></div></td>
                <td width="75" title="Process End Date"><div style="width:75px; word-wrap:break-word;"><?  echo change_date_format($sten_row[csf('end_date')]).', '.$sten_row[csf('end_hours')].':'.$sten_row[csf('end_minutes')]; ?></div></td>
                 <td width="70" align="center"><div style="width:70px; word-wrap:break-word;"><?
                        $end_time=$sten_row[csf('end_hours')].':'.$sten_row[csf('end_minutes')];
                        $start_time=$sten_row[csf('start_hours')].':'.$sten_row[csf('start_minutes')];
                        
                        $new_date_time_start=($sten_row[csf('process_start_date')].' '.$start_time.':'.'00');
                        $new_date_time_end=($sten_row[csf('end_date')].' '.$end_time.':'.'00');
                        $total_time=datediff(n,$new_date_time_start,$new_date_time_end);
                        echo floor($total_time/60).":".$total_time%60; ?></div></td>
                <td align="center" width="60"><p><?  echo $sten_row[csf('remarks')]; ?> </p>
                 </td>
                <td align="center" title="<?   if($sten_row[csf('batch_against')]==2) echo $batch_against[$sten_row[csf('batch_against')]]; ?>"><p><?  if($sten_row[csf('batch_against')]==2) echo $batch_against[$sten_row[csf('batch_against')]]; ?></p> </td>
            </tr>
            <?
            $i++;
            $tot_btq_stenter+=$sten_row[csf('batch_qnty')];
            $tot_prod_qty_stenter+=$batch_prod_qty_arr[$sten_row[csf('id')]][$sten_row[csf('prod_id')]][$sten_row[csf('width_dia_type')]]['stenter'];
             }
            if($group_by!=0)
            {
                ?>
                    <tr  bgcolor="#D4D4D4" >
                       <td align="left" colspan="24"><Strong> Batch Sub Total:</Strong> <b><? echo number_format($tot_btq_stenter,2); ?> </b>
                         &nbsp;&nbsp;&nbsp;<b> Prod. Sub Total:<? echo number_format($tot_prod_qty_stenter,2); ?> </b>
                       </td> 
                    </tr>                                
            <?
            }
            ?>
                  <tr bgcolor="#C2DCFF">
                      <td colspan="24" align="center"><strong>Compacting</strong></td>
                 </tr>
                  <?
                  $f=0; $z=0; $tot_prod_compact_qty=0;
                // echo $sql_compacting;
                 $compacting_data=sql_select($sql_compacting);$batch_chk_arr=array();$prod_batch_chk_arr=array();
                 foreach($compacting_data as $comp_row)
                 {
                if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
                $order_id=$comp_row[csf('po_id')];
                $color_id=$comp_row[csf('color_id')];
                $desc=explode(",",$comp_row[csf('item_description')]); 
                $po_number=implode(",",array_unique(explode(",",$comp_row[csf('po_number')])));
                
                $com_group_arr=$comp_row[csf('prod_id')].$comp_row[csf('batch_no')].$comp_row[csf('machine_id')].$comp_row[csf('floor_id')].$comp_row[csf('shift_name')].$comp_row[csf('end_date')];
                $prod_compact_qty=$batch_prod_qty_arr3[$comp_row[csf('id')]][$comp_row[csf('prod_id')]][$comp_row[csf('width_dia_type')]][$comp_row[csf('end_date')]]['compact'];
                //$com_group=$comp_row[csf('batch_no')].$batch[csf('end_date')];
                if (!in_array($com_group_arr,$prod_batch_chk_arr))
                        { $z++;
                            
                            
                             $prod_batch_chk_arr[]=$com_group_arr;
                              $tot_prod_compact_qty=$prod_compact_qty;
                        }
                        else
                        {
                             $tot_prod_compact_qty=0;
                        }
                        
            if($group_by!=0)
            {
                if($group_by==1)
                {
                    $group_value=$comp_row[csf('floor_id')];
                    $group_name="Floor";
                    $group_dtls_value=$floor_arr[$comp_row[csf('floor_id')]];
                }
                
                else if($group_by==2)
                {
                    $group_value=$comp_row[csf('machine_id')];
                    $group_name="Machine";
                    $group_dtls_value=$machine_arr[$comp_row[csf('machine_id')]];
                }
                if (!in_array($group_value,$group_by_arr) )
                    {
                        if($k!=1)
                        { ?>
                        
                        <tr  bgcolor="#D4D4D4" >
                       <td align="left" colspan="24"><Strong> Sub Total:</Strong> <b><? echo number_format($btq_com,2); ?> </b>
                        &nbsp;&nbsp;&nbsp;<b> Prod. Sub Total:<? echo number_format($tot_qty_com,2); ?> </b>
                       </td>
                        </tr>                                
                            <?
                            unset($btq_com);unset($tot_qty_com);
                        }
                        ?>  
                        <tr bgcolor="#EFEFEF">
                            <td colspan="24" align="left" ><b><? echo $group_name; ?> : <? echo $group_dtls_value; ?></b></td>
                        </tr>
                        <?
                        $group_by_arr[]=$group_value;            
                        $k++;
                    }
            }
            $com_grouping_arr_val=$comp_row[csf('batch_no')].$comp_row[csf('machine_id')].$comp_row[csf('floor_id')].$comp_row[csf('shift_name')];
            ?>
                <tr bgcolor="<? echo $bgcolor;?>" onClick="change_color('tr<? echo $i;?>','<? echo $bgcolor;?>')" id="tr<? echo $i;?>">
                 <? if (!in_array($com_grouping_arr_val,$batch_chk_arr) )
                        { $f++;
                            ?>
                <td width="30"><? echo $f; ?></td>
                 <? if($group_by==2 || $group_by==0){ ?>
                 <td  align="center" width="80" title="<? echo $machine_arr[$comp_row[csf('machine_id')]]; ?>"><p><? echo $machine_arr[$comp_row[csf('machine_id')]]; ?></p></td>
                <?
                 }
                 if($group_by==1 || $group_by==0){ ?>
                 <td width="80"><p><? echo $floor_arr[$comp_row[csf('floor_id')]]; ?></p></td>
                <? }
                ?>
                <td  align="center" width="50" ><p><? echo $shift_name[$comp_row[csf('shift_name')]]; ?></p></td>
                <td  width="100" title="<? echo $buyer_arr[$comp_row[csf('buyer_name')]]; ?>"><p><? echo $buyer_arr[$comp_row[csf('buyer_name')]]; ?></p></td>
                <td  width="80" title="<? echo  $comp_row[csf('job_no_prefix_num')]; ?>"><p><? echo $comp_row[csf('job_no_prefix_num')]; ?></p></td>
                <td  width="100"><p><? echo $comp_row[csf('booking_no')]; ?></p></td>
                <td  width="60"><p><? echo $comp_row[csf('file_no')]; ?></p></td>
                <td  width="70"><p><? echo $comp_row[csf('grouping')]; ?></p></td>
                <td  width="90"><p><? echo $po_number; ?></p></td>
                <?      $batch_chk_arr[]=$com_grouping_arr_val;
                        }
                        else
                        { ?>
                <td width="30"><? //echo $i; ?></td>
                 <? if($group_by==2 || $group_by==0){ ?>
                 <td  align="center" width="80"><p><? //echo $machine_arr[$comp_row[csf('machine_id')]]; ?></p></td>
                <?
                 }
                 if($group_by==1 || $group_by==0){ ?>
                 <td width="80"><p><? //echo $floor_arr[$sten_row[csf('floor_id')]]; ?></p></td>
                <? }
                ?>
                <td  align="center" width="50" ><p><? //echo $shift_name[$drying_row[csf('shift_name')]]; ?></p></td>
                <td  width="100"><p><? //echo $buyer_arr[$comp_row[csf('buyer_name')]]; ?></p></td>
                <td  width="80"><p><? //echo $comp_row[csf('job_no_prefix_num')]; ?></p></td>
                <td  width="100"><p><? //echo $heat_row[csf('booking_no')]; ?></p></td>
                <td  width="60"><p><? //echo $comp_row[csf('file_no')]; ?></p></td>
                <td  width="70"><p><? //echo $comp_row[csf('grouping')]; ?></p></td>
                <td  width="90"><p><? //echo $po_number; ?></p></td>    
                    <?  }
                ?>
                <td  width="100" title="<? echo $desc[0]; ?>"><p><? echo $comp_row[csf('item_description')]; ?></p></td>
                <td  width="70" title="<? echo   $desc[2]; ?>"><p><? echo  $desc[2]; ?></p></td>
                <td  width="75" title="<? ?>"><p><? echo $fabric_typee[$comp_row[csf('width_dia_type')]];?></p></td>
                <td  width="80" title="<? echo $color_library[$comp_row[csf('color_id')]]; ?>"><p><? echo $color_library[$comp_row[csf('color_id')]]; ?></p></td>
                <td  align="center" width="90" title="<? echo $comp_row[csf('batch_no')]; ?>"><p><? echo $comp_row[csf('batch_no')]; ?></p></td>
                <td  align="center" width="40" title="<? echo $comp_row[csf('extention_no')]; ?>"><p><? echo $comp_row[csf('extention_no')]; ?></p></td>
                <td align="right" width="70" title="<? echo $comp_row[csf('batch_qnty')];  ?>"><? echo number_format($comp_row[csf('batch_qnty')],2);  ?></td>
                <td align="right" width="70" ><? echo number_format($tot_prod_compact_qty,2);  ?></td>
                <td align="left" width="50" title="<? echo $yarn_lot_arr[$comp_row[csf('prod_id')]][$comp_row[csf('po_id')]]; ?>"><p><? echo $yarn_lot_arr[$comp_row[csf('prod_id')]][$comp_row[csf('po_id')]];  ?></p></td>
               <td width="75" title="Process Start Date"><div style="width:75px; word-wrap:break-word;"><?  echo change_date_format($comp_row[csf('process_start_date')]).', '.$comp_row[csf('start_hours')].':'.$comp_row[csf('start_minutes')]; ?></div></td>
                <td width="75" title="Process End Date"><div style="width:75px; word-wrap:break-word;"><?  echo change_date_format($comp_row[csf('end_date')]).', '.$comp_row[csf('end_hours')].':'.$comp_row[csf('end_minutes')]; ?></div></td>
                 <td width="70" align="center"><div style="width:70px; word-wrap:break-word;"><?
                        $end_time=$comp_row[csf('end_hours')].':'.$comp_row[csf('end_minutes')];
                        $start_time=$comp_row[csf('start_hours')].':'.$comp_row[csf('start_minutes')];
                        
                        $new_date_time_start=($comp_row[csf('process_start_date')].' '.$start_time.':'.'00');
                        $new_date_time_end=($comp_row[csf('end_date')].' '.$end_time.':'.'00');
                        $total_time=datediff(n,$new_date_time_start,$new_date_time_end);
                        echo floor($total_time/60).":".$total_time%60; ?></div></td>
                <td align="center" width="60"><p>
               <?     
            echo $comp_row[csf('remarks')];
                    ?>
                    </p>
                 </td>
                <td align="center" title="<?   if($comp_row[csf('batch_against')]==2) echo $batch_against[$comp_row[csf('batch_against')]]; ?>"><p><?  if($comp_row[csf('batch_against')]==2) echo $batch_against[$comp_row[csf('batch_against')]]; ?></p> </td>
            </tr>
            <?
            $i++;
            $btq_com+=$comp_row[csf('batch_qnty')];
            $tot_qty_com+=$tot_prod_compact_qty;

             }
            if($group_by!=0)
            {
                ?>
                    <tr  bgcolor="#D4D4D4" >
                       <td align="left" colspan="24"><Strong> Sub Total:</Strong> <b><? echo number_format($btq_com,2); ?> </b>
                         &nbsp;&nbsp;&nbsp;<b> Prod. Sub Total:<? echo number_format($tot_qty_com,2); ?> </b>
                       </td>
                    </tr>                                
            <?
            }
            ?>
                  <tr bgcolor="#C2DCFF">
                       <td colspan="24" align="center"><strong>Special Finish</strong></td>
                 </tr>
                  <?
                  $f=0;$k=1;$tot_prod_qty_special=0;
                // echo $sql_special;
                 $special_data=sql_select($sql_special);$batch_chk_arr=array();
                 foreach($special_data as $special_row)
                 {
                if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
                $order_id=$special_row[csf('po_id')];
                $color_id=$special_row[csf('color_id')];
                $desc=explode(",",$special_row[csf('item_description')]); 
                $po_number=implode(",",array_unique(explode(",",$special_row[csf('po_number')])));
            if($group_by!=0)
            {
                if($group_by==1)
                {
                    $group_value=$special_row[csf('floor_id')];
                    $group_name="Floor";
                    $group_dtls_value=$floor_arr[$special_row[csf('floor_id')]];
                }
                
                else if($group_by==2)
                {
                    $group_value=$special_row[csf('machine_id')];
                    $group_name="Machine";
                    $group_dtls_value=$machine_arr[$special_row[csf('machine_id')]];
                }
                if (!in_array($group_value,$group_by_arr) )
                    {
                        if($k!=1)
                        { ?>
                        
                        <tr  bgcolor="#D4D4D4" >
                       <td align="left" colspan="24"><Strong> Sub Total:</Strong> <b><? echo number_format($btq_special,2); ?> </b>
                        &nbsp;&nbsp;&nbsp;<b> Prod. Sub Total:<? echo number_format($tot_prod_qty_special,2); ?> </b>
                       </td>
                        </tr>                                
                            <?
                            unset($btq_special);unset($tot_prod_qty_special);
                        }
                        ?>  
                        <tr bgcolor="#EFEFEF">
                            <td colspan="24" align="left" ><b><? echo $group_name; ?> : <? echo $group_dtls_value; ?></b></td>
                        </tr>
                        <?
                        $group_by_arr[]=$group_value;            
                        $k++;
                    }
            }
            ?>
            <tr bgcolor="<? echo $bgcolor;?>" onClick="change_color('tr<? echo $i;?>','<? echo $bgcolor;?>')" id="tr<? echo $i;?>">
                 <? if (!in_array($special_row[csf('batch_no')],$batch_chk_arr) )
                        { $f++;
                            ?>
                <td width="30"><? echo $i; ?></td>
                <? if($group_by==2 || $group_by==0){ ?>
                 <td  align="center" width="80" title="<? echo $machine_arr[$special_row[csf('machine_id')]]; ?>"><p><? echo $machine_arr[$special_row[csf('machine_id')]]; ?></p></td>
                <?
                 }
                 if($group_by==1 || $group_by==0){ ?>
                 <td width="80"><p><? echo $floor_arr[$special_row[csf('floor_id')]]; ?></p></td>
                <? }
                ?>
                <td  align="center" width="50" ><p><? echo $shift_name[$special_row[csf('shift_name')]]; ?></p></td>
                <td  width="100" title="<? echo $buyer_arr[$special_row[csf('buyer_name')]]; ?>"><p><? echo $buyer_arr[$special_row[csf('buyer_name')]]; ?></p></td>
                <td  width="80" title="<? echo  $special_row[csf('job_no_prefix_num')]; ?>"><p><? echo $special_row[csf('job_no_prefix_num')]; ?></p></td>
                <td  width="100"><p><? echo $special_row[csf('booking_no')]; ?></p></td>
                <td  width="60"><p><? echo $special_row[csf('file_no')]; ?></p></td>
                <td  width="70"><p><? echo $special_row[csf('grouping')]; ?></p></td>
                <td  width="90"><div style="width:90px; word-wrap:break-word;"><? echo $po_number; ?></div></td>
                <?      $batch_chk_arr[]=$special_row[csf('batch_no')];
                        }
                        else
                        { ?>
                <td width="30"><? //echo $f; ?></td>
                   <? if($group_by==2 || $group_by==0){ ?>
                 <td  align="center" width="80"><p><? //echo $machine_arr[$special_row[csf('machine_id')]]; ?></p></td>
                <?
                 }
                 if($group_by==1 || $group_by==0){ ?>
                 <td width="80"><p><? //echo $floor_arr[$special_row[csf('floor_id')]]; ?></p></td>
                <? }
                ?>
                <td  align="center" width="50" ><p><? //echo $shift_name[$drying_row[csf('shift_name')]]; ?></p></td>
                <td  width="100"><p><? //echo $buyer_arr[$special_row[csf('buyer_name')]]; ?></p></td>
                <td  width="80"><p><? //echo $special_row[csf('job_no_prefix_num')]; ?></p></td>
                <td  width="100"><p><? //echo $heat_row[csf('booking_no')]; ?></p></td>
                <td  width="60"><p><? //echo $special_row[csf('file_no')]; ?></p></td>
                <td  width="70"><p><? //echo $special_row[csf('grouping')]; ?></p></td>
                <td  width="90"><div style="width:90px; word-wrap:break-word;"><? //echo $po_number; ?></div></td>  
                <?  }
                ?>
                <td  width="100" title="<? echo $desc[0]; ?>"><p><? echo $special_row[csf('item_description')]; ?></p></td>
                <td  width="70" title="<? echo   $desc[2]; ?>"><p><? echo  $desc[2]; ?></p></td>
                <td  width="75" title="<? ?>"><p><? echo $fabric_typee[$special_row[csf('width_dia_type')]];?></p></td>
                <td  width="80" title="<? echo $color_library[$special_row[csf('color_id')]]; ?>"><p><? echo $color_library[$special_row[csf('color_id')]]; ?></p></td>
                <td  align="center" width="90" title="<? echo $special_row[csf('batch_no')]; ?>"><p><? echo $special_row[csf('batch_no')]; ?></p></td>
                <td  align="center" width="40" title="<? echo $special_row[csf('extention_no')]; ?>"><p><? echo $special_row[csf('extention_no')]; ?></p></td>
                <td align="right" width="70" title="<? echo $special_row[csf('batch_qnty')];  ?>"><? echo number_format($special_row[csf('batch_qnty')],2);  ?></td>
                <td align="right" width="70" ><? echo number_format($batch_prod_qty_arr[$special_row[csf('id')]][$special_row[csf('prod_id')]][$special_row[csf('width_dia_type')]]['special'],2);  ?></td>
                <td align="left" width="50" title="<? echo $yarn_lot_arr[$special_row[csf('prod_id')]][$special_row[csf('po_id')]]; ?>"><p><? echo $yarn_lot_arr[$special_row[csf('prod_id')]][$special_row[csf('po_id')]];  ?></p></td>
                <td width="75" title="Process Start Date"><div style="width:75px; word-wrap:break-word;"><?  echo change_date_format($special_row[csf('process_start_date')]).', '.$special_row[csf('start_hours')].':'.$special_row[csf('start_minutes')]; ?></div></td>
                <td width="75" title="Process End Date"><div style="width:75px; word-wrap:break-word;"><?  echo change_date_format($special_row[csf('end_date')]).', '.$special_row[csf('end_hours')].':'.$special_row[csf('end_minutes')]; ?></div></td>
                 <td width="70" align="center"><div style="width:70px; word-wrap:break-word;"><?
                        $end_time=$special_row[csf('end_hours')].':'.$special_row[csf('end_minutes')];
                        $start_time=$special_row[csf('start_hours')].':'.$special_row[csf('start_minutes')];
                        
                        $new_date_time_start=($special_row[csf('process_start_date')].' '.$start_time.':'.'00');
                        $new_date_time_end=($special_row[csf('end_date')].' '.$end_time.':'.'00');
                        $total_time=datediff(n,$new_date_time_start,$new_date_time_end);
                        echo floor($total_time/60).":".$total_time%60; ?></div></td>
                <td align="center" width="60"><p>
               <?     
            echo $special_row[csf('remarks')];
                    ?>
                    </p>
                 </td>
                <td align="center" title="<?   if($special_row[csf('batch_against')]==2) echo $batch_against[$special_row[csf('batch_against')]]; ?>"><p><?  if($comp_row[csf('batch_against')]==2) echo $batch_against[$special_row[csf('batch_against')]]; ?></p> </td>
            </tr>
            <?
            $i++;
            $btq_special+=$special_row[csf('batch_qnty')];
            $tot_prod_qty_special+=$batch_prod_qty_arr[$special_row[csf('id')]][$special_row[csf('prod_id')]][$special_row[csf('width_dia_type')]]['special'];
             }
             //echo $tot_prod_qty_heat.'='.$tot_prod_qty_siltting.'='.$tot_prod_qty_drying.'='.$tot_prod_qty_stenter.'='.$tot_qty_com.'='.$tot_prod_qty_special;
             $grand_total=$btq_heat+$btq_siltting+$btq_drying+$btq_com+$btq_special+$tot_btq_stenter;
             $grand_prod_total=$tot_prod_qty_heat+$tot_prod_qty_siltting+$tot_prod_qty_drying+$tot_prod_qty_stenter+$tot_qty_com+$tot_prod_qty_special;

            if($group_by!=0)
            {
                ?>
                    <tr  bgcolor="#D4D4D4" >
                       <td align="left" colspan="24"><Strong> Sub Total:</Strong> <b><? echo number_format($btq_special,2); ?> </b>
                        &nbsp;&nbsp;&nbsp;<b> Prod. Sub Total:<? echo number_format($tot_prod_qty_special,2); ?> </b>
                       </td>
                    
                    </tr>                                
                            <?
            }
            ?>
                
                  <tr bgcolor="#CCCCCC">
                       <td align="left"  colspan="24"><Strong> Grand Total:</Strong> <b>Batch Qty:<? echo number_format($grand_total,2).'&nbsp;&nbsp;Prod Qty:&nbsp;&nbsp;'.$grand_prod_total; ?> </b></td>
                      
                 </tr>  
                </tbody>
            </table>
            </div>
            </fieldset>
            </div>
                <? 
        }//All Search End
        foreach (glob("$user_name*.xls") as $filename) 
        {
            @unlink($filename);
        }
        $name=time();
        $filename=$user_name."_".$name.".xls";
        $create_new_doc = fopen($filename, 'w');
        $is_created = fwrite($create_new_doc,ob_get_contents());
        echo "$total_data****$filename";
        exit();
        //Fabric Finishing Report end
    } // 2nd Show report end
} 

if($action=="wip_fabric_finishing_report")
{   
    ?>
    <!-- <div style="text-align: center;color: red;font-weight: bold;font-size: 20px;">This page is under QC. Please be patience.</div> -->
    <?
    $process = array( &$_POST );
    extract(check_magic_quote_gpc( $process )); 
    if($db_type==0) $year_field_by="and YEAR(a.insert_date)"; 
    else if($db_type==2) $year_field_by=" and to_char(a.insert_date,'YYYY')";
    if($db_type==0) $field_concat="concat(machine_no,'-',brand) as machine_name"; 
    else if($db_type==2) $field_concat="machine_no || '-' || brand as machine_name";
    // machine_no || '-' || brand as machine_name
    $company = str_replace("'","",$cbo_company_name);
    $working_company = str_replace("'","",$cbo_working_company_id);
    $cbo_location_id = str_replace("'","",$cbo_location_id);
    $cbo_floor_id = str_replace("'","",$cbo_floor_id);
     $report_type= str_replace("'","",$report_type);
    if($company!=0) $company_cond="and a.company_id=$company";else $company_cond="";
    if($working_company!=0) $working_company_cond="and f.service_company=$working_company";else $working_company_cond="";

    $floor_no_cond="";
    if($cbo_location_id!=0) 
    {
        if($cbo_floor_id!=0)
        {
            $floor_no_cond=" and a.floor_id='$cbo_floor_id'";
        }
        else
        {
            $sql_floor = sql_select("select id from lib_prod_floor where production_process in (3,4) and company_id ='$working_company' and location_id=$cbo_location_id and status_active =1 and is_deleted=0 group by id"); //production_process =3 and

            foreach ($sql_floor as $val) 
            {
                $floor_array[$val[csf("id")]] = $val[csf("id")];
            }

            $floor_array = array_filter($floor_array);
            $floor_no_cond=" and a.floor_id in (".implode(',', $floor_array).")";
        }
    }

    $buyer = str_replace("'","",$cbo_buyer_name);
    $job_number = str_replace("'","",$job_number);
    $job_number_id = str_replace("'","",$job_number_show);
    $batch_no = str_replace("'","",$batch_number_show);
    $booking_no = str_replace("'","",$booking_number_show);
    $color = str_replace("'","",$txt_color);
    $cbo_shift = str_replace("'","",$cbo_shift);
    $txt_file_no = str_replace("'","",$txt_file_no);
    $txt_ref_no = str_replace("'","",$txt_ref_no);
    
  //  $page_upto = str_replace("'","",$page_upto);
  //  $roll_maintained = str_replace("'","",$roll_maintained);
    //echo $roll_maintained;die;
    $batch_number_hidden = str_replace("'","",$batch_number);
    $booking_number_hidden = str_replace("'","",$booking_number);
    $ext_num = str_replace("'","",$txt_ext_no);
    $hidden_ext = str_replace("'","",$hidden_ext_no);
    $txt_order = str_replace("'","",$order_no);
    $hidden_order = str_replace("'","",$hidden_order_no);
    $cbo_type = str_replace("'","",$cbo_type);
    $cbo_group_by = str_replace("'","",$cbo_group_by);
    $year = str_replace("'","",$cbo_year);
    //echo $cbo_type;die;
    $txt_date_from = str_replace("'","",$txt_date_from);
    $txt_date_to = str_replace("'","",$txt_date_to);
    if($job_number_id!="") $jobdata="and d.job_no_prefix_num='".$job_number_id ."'";else $jobdata="";
    //$jobdata=($job_number_id )? " and d.job_no_prefix_num='".$job_number_id ."'" : '';
    if($buyer!=0) $buyerdata="and d.buyer_name=$buyer";else $buyerdata="";
    //$buyerdata=($buyer)?' and d.buyer_name='.$buyer : '';
    //for non order booking sample
    if($buyer!=0) $buyerdata_non_ord="and j.buyer_id=$buyer";else $buyerdata_non_ord="";
    if($batch_no!="") $batch_num="and a.batch_no='".$batch_no."'";else $batch_num="";
     if($batch_no!="") $batch_num2="and f.batch_no='".$batch_no."'";else $batch_num2="";
    if($booking_no!="") $booking_num="and a.booking_no='".$booking_no."'";else $booking_num="";
    //$batch_num=($batch_no)?" and a.batch_no='".$batch_no."'" : '';
    if(trim($ext_no)!="") $ext_no_search="%".trim($ext_no)."%"; else $ext_no_search="%%";
    if ($txt_order=="") $order_no=""; else $order_no="  and c.po_number='$txt_order'";
    if ($color=="") $color_name=""; else $color_name="  and g.color_name='$color'";
    if ($cbo_shift==0) $shift_cond=""; else $shift_cond="  and f.shift_name='$cbo_shift'";
    if ($txt_file_no=="") $file_cond=""; else $file_cond="  and c.file_no=$txt_file_no";
    if ($txt_ref_no=="") $ref_cond=""; else $ref_cond="  and c.grouping='$txt_ref_no'";
    //echo $order_no;die;
    if($color!='')
    {
        $color_id = return_field_value("distinct(a.id) as id", "lib_color a ", "a.color_name='$color'", "id");
    }
    //echo $color_id.'dd';die;
    if($color_id!='') $color=$color_id;else $color="";
    if ($color=="") $color_name=""; else $color_name="  and a.color_id=$color"; 
    
    if ($ext_num=="") $ext_no=""; else $ext_no="  and a.extention_no=$ext_num ";
    if(trim($year)!=0) $year_cond=" $year_field_by=$year"; else $year_cond="";
    if($txt_date_from && $txt_date_to)
    {
        if($db_type==0)
        {
            $date_from=change_date_format($txt_date_from,'yyyy-mm-dd');
            $date_to=change_date_format($txt_date_to,'yyyy-mm-dd');
            $batch_date_cond="and  b.batch_date >= '$date_from' AND b.batch_date<='$date_to'";
            $batch_date_cond2="and  c.batch_date >= '$date_from' AND c.batch_date<='$date_to'";
            $prod_date_cond="and  a.production_date >= '$date_from' AND a.production_date<='$date_to'";
        }
        if($db_type==2)
        {
            $date_from=change_date_format($txt_date_from,'','',1);
            $date_to=change_date_format($txt_date_to,'','',1);
            $batch_date_cond=" and  b.batch_date >= '$date_from' AND b.batch_date<='$date_to'";
            $batch_date_cond2=" and  c.batch_date >= '$date_from' AND c.batch_date<='$date_to'";
            $prod_date_cond=" and  a.production_date >= '$date_from' AND a.production_date<='$date_to'";
        }
        
        //echo $batch_date_cond;die;
        $get_batch_ids_by_batch_date=sql_select("select entry_form, b.id,b.process_id from pro_batch_create_mst b where b.status_active=1 $batch_cond $batch_date_cond $floor_cond3");
        //echo "select b.id,b.process_id from pro_batch_create_mst b where b.status_active=1 $batch_cond $batch_date_cond $floor_cond3";

        foreach($get_batch_ids_by_batch_date as $row)
        {
            if($row[csf("entry_form")]!=36)
            {
                $batch_process = explode(",",$row[csf("process_id")]);
                if(in_array(33, $batch_process)){
                    $production_batch_arr[$row[csf("id")]] = $row[csf("id")];
                }
            }
            else
            {
                $sub_batch_process = explode(",",$row[csf("process_id")]);
                if(in_array(33, $sub_batch_process)){
                    $sub_production_batch_arr[$row[csf("id")]] = $row[csf("id")];
                }
            }
        }
        //print_r($sub_production_batch_arr);
        $get_batch_ids_from_subprocess = sql_select("select a.batch_id from pro_fab_subprocess a,pro_fab_subprocess_dtls b where a.id=b.mst_id and a.status_active=1  and b.status_active=1 and a.entry_form=35 and a.load_unload_id=2 $prod_date_cond $floor_cond2 $batch_cond3 group by a.batch_id");
        //echo "select a.batch_id from pro_fab_subprocess a,pro_fab_subprocess_dtls b where a.id=b.mst_id and a.status_active=1  and b.status_active=1 and a.entry_form=35 and a.load_unload_id=2 $prod_date_cond $floor_cond2 $batch_cond3 group by a.batch_id";

        foreach($get_batch_ids_from_subprocess as $row)
        {
            $production_batch_arr[$row[csf("batch_id")]] = $row[csf("batch_id")];
            //$batch_id_arr[$row[csf("batch_id")]] = $row[csf("batch_id")];
        }

        $get_batch_ids_from_subprocess_chk = sql_select("select max(a.id) as ids,a.batch_id,a.entry_form,a.result from pro_fab_subprocess a  ,pro_fab_subprocess_dtls b where a.id=b.mst_id  and a.status_active=1  and b.status_active=1 $prod_date_cond  $batch_cond3 group by a.batch_id,a.entry_form,a.result order by ids desc");
        //echo "select max(a.id) as ids,a.batch_id,a.entry_form,a.result from pro_fab_subprocess a  ,pro_fab_subprocess_dtls b where a.id=b.mst_id  and a.status_active=1  and b.status_active=1 $prod_date_cond  $batch_cond3 group by a.batch_id,a.entry_form,a.result order by ids desc";

        foreach($get_batch_ids_from_subprocess_chk as $row)
        {
            $batch_sub_process_arr[$row[csf("batch_id")]]['entry_form'] = $row[csf("entry_form")];
            $batch_sub_process_arr[$row[csf("batch_id")]]['result'] = $row[csf("result")];
            $batch_sub_process_arr2[$row[csf("ids")]][$row[csf("batch_id")]]['entry_form'] = $row[csf("entry_form")];
            $batch_sub_process_arr2[$row[csf("ids")]][$row[csf("batch_id")]]['result'] = $row[csf("result")];
                //$batch_id_arr[$row[csf("batch_id")]] = $row[csf("batch_id")];
        }
        //print_r($batch_id_arr);
        if(empty($production_batch_arr))
        {
            $prod_date_cond="";
        }

        if($db_type==2 && count($production_batch_arr)>1000)
        {
            $production_batch_id_cond=" and (";
            $batIdsArr=array_chunk($production_batch_arr,999);
            foreach($batIdsArr as $ids)
            {
                $ids=implode(",",$ids);
                $production_batch_id_cond.=" b.id in($ids) or";
            }
            $production_batch_id_cond=chop($production_batch_id_cond,'or ');
            $production_batch_id_cond.=")";
        }
        else
        {
            $production_batch_id_cond=" and b.id in(".implode(",",$production_batch_arr).")";
        }

        if(empty($production_batch_arr))
        {
            $production_batch_id_cond="";
            $production_batch_id_cond="and b.id=0";
            //echo "<b style='color:red'>No Data Found.</b>";die;
        }
        
    } //Date end
    
      
      
      
      
      
      //Excel
      foreach (glob("$user_name*.xls") as $filename) 
        {
            @unlink($filename);
        }
        $name=time();
        $filename=$user_name."_".$name.".xls";
        $create_new_doc = fopen($filename, 'w');
        $is_created = fwrite($create_new_doc,ob_get_contents());
        echo "$total_data****$filename";
        exit();
        
}
       
?>