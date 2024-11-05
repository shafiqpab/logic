<?
header('Content-type:text/html; charset=utf-8');
session_start();
if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");
include('../../includes/common.php');
$permission=$_SESSION['page_permission'];
$data=$_REQUEST['data'];
$action=$_REQUEST['action'];

if ($action=="load_drop_down_buyer")
{
	echo create_drop_down( "cbo_buyer_name", 135, "select buy.id, buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company='$data' $buyer_cond and buy.id in (select buyer_id from lib_buyer_party_type where party_type in (1,3,21,90)) order by buy.buyer_name","id,buyer_name", 1, "-- Select Buyer --", $selected, "" );  
	exit();
}
if ($action=="system_popup")
{
	echo load_html_head_contents("Popup Info", "../../", 1, 1,'',1,'');
	extract($_REQUEST);
?>
	<script>
		  function js_set_value(id)
		  { 
			 // alert(id);
			  document.getElementById('system_id').value=id;
			  parent.emailwindow.hide();
		  }
	  </script>  
  </head>
  <body>
	<div align="center" style="width:100%;" >
        <form name="system_1" id="system_1" autocomplete="off">
            <table width="700" cellspacing="0" cellpadding="0" border="1" rules="all" class="rpt_table" align="center">
                <thead>                	 
                    <th>Buyer Name</th>
                    <th>Garments Item</th>
                    <th>Style Ref.</th>
                    <th>System ID</th>
                    <th><input type="reset" name="reset" id="reset" class="formbutton" value="Reset" style="width:100px;" /></th>           
                </thead>
                <tr class="general">
                    <td id="buyer_td">
						<?
                        	echo create_drop_down( "cbo_buyer_name", 160, "select buy.id, buy.buyer_name from lib_buyer buy where buy.status_active =1 and buy.is_deleted=0 $buyer_cond order by buy.buyer_name","id,buyer_name", 1, "-- Select Buyer --", $selected, "" );  
                        ?> 
                    </td>
                    <td>
                        <input type="hidden" id="system_id" style="width:100px;" >
                        <? echo create_drop_down( "cbo_gmt_item", 160, $garments_item,'', 1, "-Select Gmt. Item-","","","","" ); ?>
                    </td>
                    <td>
                    	<input type="text" style="width:130px" class="text_boxes"  name="txt_search_common" id="txt_search_common" />	
                    </td> 
                    <td>
                    	<input type="text" style="width:100px" class="text_boxes_numeric"  name="txt_system_id" id="txt_system_id" />	
                    </td>
                    <td align="center">
                    	<input type="button" name="button" class="formbutton" value="Show" onClick="show_list_view (document.getElementById('cbo_buyer_name').value+'_'+document.getElementById('txt_search_common').value+'_'+document.getElementById('cbo_gmt_item').value+'_'+document.getElementById('txt_system_id').value, 'system_list_view', 'search_div', 'gsd_entry_controller', 'setFilterGrid(\'list_view\',-1)')" style="width:100px;" />
                    </td>
                </tr>
            </table>
            <div id="search_div" style="margin-top:5px"></div>
        </form>
    </div>
    </body>
    <script src="../../includes/functions_bottom.js" type="text/javascript"></script>
    <script> document.getElementById('cbo_buyer_name').value='<? echo $buyer_id; ?>'; </script> 
    </html>
<?
exit();
}

if ($action=="system_list_view")
{
	$data=explode('_',$data);
	$buyer_name_arr=return_library_array( "select id,short_name from lib_buyer", "id","short_name"  );
	
	if ($data[0]!=0) $buyer_id_cond=" and c.buyer_name='$data[0]'"; else $buyer_id_cond="";
	if (trim($data[1])!="") $search_field_cond=" and LOWER(c.style_ref_no) like LOWER('%".trim($data[1])."%')"; else $search_field_cond=""; 
	if ($data[2]!=0) $gmt_item_cond=" and a.gmts_item_id='$data[2]'"; else { $gmt_item_cond=""; }
	if (trim($data[3])!="") $system_id_cond=" and a.system_no_prefix='".trim($data[3])."'"; else $system_id_cond=""; 
	
	$arr=array (2=>$buyer_name_arr,4=>$garments_item);
		
	
	
	if($db_type==0)
	{
		//$sql ="SELECT c.id as up_id,c.extention_no,b.id as id,a.job_no as po_job_no,a.company_name,a.job_no_prefix_num,a.buyer_name,a.style_ref_no,s.gmts_item_id,GROUP_CONCAT(b.po_number) AS po_number, min(b.pub_shipment_date) as shipment_date, $year_field as year FROM wo_po_break_down b, wo_po_details_master a, wo_po_details_mas_set_details s ,ppl_gsd_entry_mst c  where  s.job_no=c.po_job_no and s.gmts_item_id=c.gmts_item_id and c.is_deleted=0 and a.job_no=c.po_job_no and a.job_no=b.job_no_mst and a.job_no=s.job_no AND a.company_name=$data[0] $buyer_name $date_cond $search_field_cond $gmt_item_cond group by c.id,c.extention_no, a.job_no_prefix_num, a.job_no, a.company_name, a.buyer_name, a.style_ref_no,a.insert_date, b.id,s.gmts_item_id";
		/*$sql ="SELECT c.id as up_id,c.extention_no,b.id as id,a.job_no as po_job_no,a.company_name,a.job_no_prefix_num,a.buyer_name,a.style_ref_no,s.gmts_item_id,GROUP_CONCAT(b.po_number) AS po_number, min(b.pub_shipment_date) as shipment_date, $year_field as year FROM wo_po_break_down b, wo_po_details_master a, wo_po_details_mas_set_details s left join ppl_gsd_entry_mst c on s.job_no=c.po_job_no and s.gmts_item_id=c.gmts_item_id and c.is_deleted=0 where a.job_no= b.job_no_mst and a.job_no=s.job_no AND a.company_name=$data[0] $buyer_name $date_cond $search_field_cond $gmt_item_cond group by c.id,c.extention_no, a.job_no_prefix_num, a.job_no, a.company_name, a.buyer_name, a.style_ref_no,a.insert_date, b.id,s.gmts_item_id";
	*/
	}
	else
	{
	
		/*$sql ="SELECT c.id as up_id,b.id as id,a.job_no as po_job_no,a.job_no_prefix_num,a.company_name,a.buyer_name,a.style_ref_no,s.gmts_item_id,listagg(CAST(b.po_number as VARCHAR(4000)),',') within group (order by b.po_number) as po_number, min(b.pub_shipment_date) as shipment_date, $year_field as year FROM wo_po_break_down b, wo_po_details_master a, wo_po_details_mas_set_details s left join ppl_gsd_entry_mst c on s.job_no=c.po_job_no and s.gmts_item_id=c.gmts_item_id and c.is_deleted=0 where a.job_no= b.job_no_mst and a.job_no=s.job_no and a.company_name=$data[0] $buyer_name $date_cond $search_field_cond $gmt_item_cond group by c.id, a.job_no_prefix_num, a.job_no, a.company_name, a.buyer_name, a.style_ref_no, a.insert_date, b.id, s.gmts_item_id";*/
			/*$sql ="SELECT c.id as up_id,c.extention_no,c.is_copied,b.id as id,a.job_no as po_job_no,a.job_no_prefix_num,a.company_name,a.buyer_name,a.style_ref_no,s.gmts_item_id,listagg(CAST(b.po_number as VARCHAR(4000)),',') within group (order by b.po_number) as po_number, min(b.pub_shipment_date) as shipment_date, $year_field as year FROM wo_po_break_down b, wo_po_details_master a, wo_po_details_mas_set_details s , ppl_gsd_entry_mst c  where  s.job_no=c.po_job_no and s.gmts_item_id=c.gmts_item_id and c.is_deleted=0 and a.job_no=c.po_job_no  and a.job_no= b.job_no_mst and a.job_no=s.job_no and a.company_name=$data[0] $buyer_name $date_cond $search_field_cond $gmt_item_cond group by c.id, c.extention_no,c.is_copied,a.job_no_prefix_num, a.job_no, a.company_name, a.buyer_name, a.style_ref_no, a.insert_date, b.id, s.gmts_item_id";*/
			/*echo $sql ="SELECT c.id as up_id,max(d.row_sequence_no) as seq_no,c.extention_no,c.is_copied,c.po_job_no as po_job_no,c.buyer_id as buyer_name,c.style_ref,c.gmts_item_id as gmts_item_id,c.po_break_down_id as po_number FROM  ppl_gsd_entry_mst c, ppl_gsd_entry_dtls d  where  c.id=d.mst_id  and c.is_deleted=0    $date_cond  $gmt_item_cond group by c.id, c.extention_no,c.is_copied,c.po_job_no, c.company_id, c.buyer_id, c.style_ref, c.gmts_item_id,c.po_break_down_id";*/
	}
	//echo $sql;die;
	/*echo create_list_view("list_view", "GSD ID,Extention No,Buyer,Style Ref.,Gmt. Item,Year, Job No.,Shipment Date,PO Numbers
", "70,70,70,120,140,60,70,90","890","230",0, $sql , "js_set_value", "up_id,id,po_job_no,buyer_name,style_ref_no,gmts_item_id,po_number,extention_no,is_copied", "", 1, "0,0,buyer_name,0,gmts_item_id,0,0,0,0,", $arr , "up_id,extention_no,buyer_name,style_ref_no,gmts_item_id,year,job_no_prefix_num,shipment_date,po_number", "gsd_entry_controller","",'0,0,0,0,0,0,0,3,0') ;*/
if($db_type==2) $group_con="listagg(CAST(d.po_number as VARCHAR(4000)),',') within group (order by d.po_number) as po_number";
else if($db_type==0) $group_con="group_concat(distinct d.po_number) as po_number";
	
"listagg(CAST(d.po_number as VARCHAR(4000)),',') within group (order by d.po_number) as po_number";
$sql ="SELECT a.id as up_id,a.po_job_no,d.id as id, a.system_no_prefix, a.extention_no,$group_con, a.is_copied, c.buyer_name as buyer_id, c.style_ref_no as style_ref, a.working_hour, a.gmts_item_id, a.operation_count, a.mc_operation_count, a.total_smv, a.tot_mc_smv, a.tot_manual_smv, a.tot_finishing_smv, max(b.row_sequence_no) as seq_no
		FROM ppl_gsd_entry_mst a, ppl_gsd_entry_dtls b ,wo_po_details_master c,wo_po_break_down d
		where a.id=b.mst_id  and  c.job_no=a.po_job_no and d.job_no_mst=c.job_no $buyer_id_cond $search_field_cond $gmt_item_cond  $system_id_cond 
		group by a.id, a.po_job_no,a.system_no_prefix,d.id, a.extention_no, a.is_copied, c.buyer_name, c.style_ref_no, a.working_hour, a.gmts_item_id, a.operation_count, a.mc_operation_count, a.total_smv, a.tot_mc_smv, a.tot_manual_smv, a.tot_finishing_smv order by a.system_no_prefix";
	//echo $sql;
	echo create_list_view("list_view", "GSD ID, Extention No, Buyer, Style Ref., Gmt. Item, Working Hour, Total SMV", "60,80,70,150,130,90","750","250",0, $sql , "js_set_value", "up_id,id,po_job_no,buyer_id,style_ref,gmts_item_id,po_number,extention_no,is_copied,system_no_prefix","",1,"0,0,buyer_id,0,gmts_item_id,0,0", $arr,"system_no_prefix,extention_no,buyer_id,style_ref,gmts_item_id,working_hour,total_smv","ws_gsd_controller","",'0,0,0,0,0,1,2');
	exit();

exit();
} //System popUp End

if ($action=="style_ref_popup")
{
	echo load_html_head_contents("Popup Info", "../../", 1, 1,'',1,'');
	extract($_REQUEST);
?>
	<script>
		  function js_set_value(id)
		  { 
			  document.getElementById('style_ref_id').value=id;
			  parent.emailwindow.hide();
		  }
	  </script>  
  </head>
  <body>
	<div align="center" style="width:100%;" >
		<form name="gsdentry_1"  id="gsdentry_1" autocomplete="off">
            <table width="970" cellspacing="0" cellpadding="0" border="1" rules="all" class="rpt_table" align="center">
                <thead>                	 
                      <th>Company Name</th>
                      <th>Buyer Name</th>
                      <th>Gmt. Item</th>
                      <th>Date Range</th>
                      <th>Search By</th>
                      <th id="search_by_td_up" width="165">Please Enter Order Number</th>
                      <th><input type="reset" name="reset" id="reset" class="formbutton" value="Reset" style="width:100px;" /></th>           
                  </thead>
                  <tr class="general">
                      <td> <input type="hidden" id="style_ref_id" style="width:100px;" >  
                      <?   
                          echo create_drop_down( "cbo_company_id", 135, "select comp.id, comp.company_name from lib_company comp where comp.status_active=1 and comp.is_deleted=0 $company_cond order by comp.company_name","id,company_name",1, "-- Select Company --",$data,"load_drop_down( 'gsd_entry_controller', this.value, 'load_drop_down_buyer', 'buyer_td' );",1 );
                      ?>
                      </td>
                      <td id="buyer_td">
                        <?
                        	echo create_drop_down( "cbo_buyer_name", 135, "select buy.id, buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company='$data' $buyer_cond and buy.id in (select buyer_id from lib_buyer_party_type where party_type in (1,3,21,90)) order by buy.buyer_name","id,buyer_name", 1, "-- Select Buyer --", $selected, "" );  
                        ?> 
                      </td>
                       <td id="">
                        <?
                        	echo create_drop_down( "cbo_gmt_item", 142, $garments_item,'', 1, "-Select Gmt. Item-","","","","" );  
                        ?> 
                      </td>
                      
                      
                      <td>
                      <input name="txt_date_from" id="txt_date_from" class="datepicker" style="width:55px">To
                      <input name="txt_date_to" id="txt_date_to" class="datepicker" style="width:55px">
                      </td>
                      <td>
                        <?
                            $search_by_arr=array(1=>"Order Number",2=>"Job Number",3=>"Style Ref");
                            $dd="change_search_event(this.value, '0*0*0*0*0', '0*0*0*2*0', '../../') ";							
                            echo create_drop_down( "cbo_search_by", 115, $search_by_arr,"",0, "--Select--", "",$dd,0 );
                        ?> 
                      </td>
                      <td id="search_by_td">
                        <input type="text" style="width:130px" class="text_boxes"  name="txt_search_common" id="txt_search_common" />	
                      </td> 
                      <td align="center">
                          <input type="button" name="button" class="formbutton" value="Show" onClick="show_list_view ( document.getElementById('cbo_company_id').value+'_'+document.getElementById('cbo_buyer_name').value+'_'+document.getElementById('txt_date_from').value+'_'+document.getElementById('txt_date_to').value+'_'+document.getElementById('cbo_search_by').value+'_'+document.getElementById('txt_search_common').value+'_'+document.getElementById('cbo_gmt_item').value, 'style_ref_list_view', 'search_div', 'gsd_entry_controller', 'setFilterGrid(\'list_view\',-1)')" style="width:100px;" />
                      </td>
                  </tr>
                  <tr>
                      <td colspan="6" align="center" height="40" valign="middle">
                          <? echo load_month_buttons(1);  ?>
                      </td>
                  </tr>
              </table>
              <div id="search_div" style="margin-top:5px"></div>
		</form>
	</div>
    </body>
    <script src="../../includes/functions_bottom.js" type="text/javascript"></script>
    <script> document.getElementById('cbo_buyer_name').value='<? echo $buyer_id; ?>'; </script> 
    </html>
<?
exit();
}

if ($action=="style_ref_list_view")
{
	$data=explode('_',$data);
	
	//$company_id=return_library_array( "select id, company_name from lib_company",'id','company_name');
	$buyer_name_arr=return_library_array( "select id,short_name from lib_buyer", "id","short_name"  );
	
	if ($data[0]!=0) $company_name=" and b.company_name='$data[0]'"; else { echo "Please Select Company First."; die; }
	if ($data[1]!=0) $buyer_name=" and a.buyer_name='$data[1]'"; else $buyer_name="";
	if ($data[6]!=0) $gmt_item_cond=" and s.gmts_item_id='$data[6]'"; else { $gmt_item_cond=""; }
	
	$start_date =trim($data[2]);
	$end_date =trim($data[3]);	
	
	if($start_date!="" && $end_date!="")
	{
		if($db_type==0)
		{
			$date_cond="and b.pub_shipment_date between '".change_date_format($start_date,"yyyy-mm-dd", "-")."' and '".change_date_format($end_date,"yyyy-mm-dd", "-")."'";
		}
		else
		{
			$date_cond="and b.pub_shipment_date between '".change_date_format($start_date,'','',1)."' and '".change_date_format($end_date,'','',1)."'";	
		}
	}
	else
	{
		$date_cond="";
	}
	
	$search_by=$data[4];
	$search_string=trim($data[5]);

	if($search_by==1) $search_field_cond=" and b.po_number like '%".$search_string."%'";
	else if($search_by==2) $search_field_cond=" and  a.job_no like '%".$search_string."'";
	else $search_field_cond=" and a.style_ref_no like '%".$search_string."%'"; 

	if($db_type==0) $year_field="YEAR(a.insert_date)"; 
	else if($db_type==2) $year_field="to_char(a.insert_date,'YYYY')";
	else $year_field="";//defined Later
	
	$arr=array (1=>$buyer_name_arr,3=>$garments_item);
		
	//$sql ="SELECT DISTINCT  id, up_id,po_job_no,company_name,buyer_name,style_ref_no,gmts_item_id,po_number,shipment_date FROM (SELECT 0 AS id, a.id AS up_id,a.po_job_no,b.company_name,b.buyer_name,b.style_ref_no,b.gmts_item_id,c.po_number,c.pub_shipment_date as shipment_date FROM ppl_gsd_entry_mst a,wo_po_details_master b, wo_po_break_down c WHERE a.po_job_no=b.job_no AND b.job_no=c.job_no_mst $company_name $buyer_name GROUP BY job_no UNION SELECT a.id AS id,0 AS up_id,a.job_no AS po_job_no,a.company_name,a.buyer_name,a.style_ref_no,a.gmts_item_id,GROUP_CONCAT(b.po_number) AS po_number,min(b.pub_shipment_date) as shipment_date FROM wo_po_details_master a, wo_po_break_down b WHERE a.job_no= b.job_no_mst AND a.company_name=$data[0] AND a.buyer_name=$data[1] GROUP BY job_no) AS t1 GROUP BY po_job_no";
	
	/*$sql ="SELECT DISTINCT po_job_no,company_name,buyer_name,style_ref_no,gmts_item_id,po_number,shipment_date FROM (SELECT a.po_job_no,b.company_name,b.buyer_name,b.style_ref_no,b.gmts_item_id,wm_concat(CAST(c.po_number  AS VARCHAR(4000))) AS po_number,min(c.pub_shipment_date) as shipment_date FROM ppl_gsd_entry_mst a,wo_po_details_master b, wo_po_break_down c WHERE a.po_job_no=b.job_no AND b.job_no=c.job_no_mst $company_name $buyer_name GROUP BY a.po_job_no,b.company_name,b.buyer_name,b.style_ref_no,b.gmts_item_id UNION SELECT a.job_no AS po_job_no,a.company_name,a.buyer_name,a.style_ref_no,a.gmts_item_id,wm_concat(CAST(po_number  AS VARCHAR(4000))) AS po_number,min(b.pub_shipment_date) as shipment_date FROM wo_po_details_master a, wo_po_break_down b WHERE a.job_no= b.job_no_mst AND a.company_name=$data[0] AND a.buyer_name=$data[1] GROUP BY a.job_no,a.company_name,a.buyer_name,a.style_ref_no,a.gmts_item_id) t group by po_job_no,company_name,buyer_name,style_ref_no,gmts_item_id,po_number,shipment_date";*/
	
	if($db_type==0)
	{
		//$sql ="SELECT c.id as up_id,a.id as id,a.job_no as po_job_no,a.company_name,a.buyer_name,a.style_ref_no,a.gmts_item_id,GROUP_CONCAT(b.po_number) AS po_number, min(b.pub_shipment_date) as shipment_date, $year_field as year FROM wo_po_break_down b, wo_po_details_master a left join ppl_gsd_entry_mst c on a.job_no=c.po_job_no and c.is_deleted=0 where a.job_no= b.job_no_mst AND a.company_name=$data[0] AND a.buyer_name=$data[1] $date_cond $search_field_cond group by c.id,a.id, a.job_no, a.company_name, a.buyer_name, a.style_ref_no, a.insert_date, a.gmts_item_id";
		$sql ="SELECT c.id as up_id,b.id as id,a.job_no as po_job_no,a.company_name,a.job_no_prefix_num,a.buyer_name,a.style_ref_no,s.gmts_item_id,GROUP_CONCAT(b.po_number) AS po_number, min(b.pub_shipment_date) as shipment_date, $year_field as year FROM wo_po_break_down b, wo_po_details_master a, wo_po_details_mas_set_details s left join ppl_gsd_entry_mst c on s.job_no=c.po_job_no and s.gmts_item_id=c.gmts_item_id and c.is_deleted=0 where a.job_no= b.job_no_mst and a.job_no=s.job_no AND a.company_name=$data[0] $buyer_name $date_cond $search_field_cond $gmt_item_cond group by c.id, a.job_no_prefix_num, a.job_no, a.company_name, a.buyer_name, a.style_ref_no,a.insert_date, b.id,s.gmts_item_id";
	
	}
	else
	{
		//$sql ="SELECT c.id as up_id,a.id as id,a.job_no as po_job_no,a.company_name,a.buyer_name,a.style_ref_no,a.gmts_item_id,listagg(CAST(b.po_number as VARCHAR(4000)),',') within group (order by b.po_number) as po_number, min(b.pub_shipment_date) as shipment_date, $year_field as year FROM wo_po_break_down b, wo_po_details_master a left join ppl_gsd_entry_mst c on a.job_no=c.po_job_no and c.is_deleted=0 where a.job_no= b.job_no_mst AND a.company_name=$data[0] AND a.buyer_name=$data[1] $date_cond $search_field_cond group by c.id, a.id, a.job_no, a.company_name, a.buyer_name, a.style_ref_no, a.insert_date, a.gmts_item_id";
		$sql ="SELECT c.id as up_id,b.id as id,a.job_no as po_job_no,a.job_no_prefix_num,a.company_name,a.buyer_name,a.style_ref_no,s.gmts_item_id,listagg(CAST(b.po_number as VARCHAR(4000)),',') within group (order by b.po_number) as po_number, min(b.pub_shipment_date) as shipment_date, $year_field as year FROM wo_po_break_down b, wo_po_details_master a, wo_po_details_mas_set_details s left join ppl_gsd_entry_mst c on s.job_no=c.po_job_no and s.gmts_item_id=c.gmts_item_id and c.is_deleted=0 where a.job_no= b.job_no_mst and a.job_no=s.job_no and a.company_name=$data[0] $buyer_name $date_cond $search_field_cond $gmt_item_cond group by c.id, a.job_no_prefix_num, a.job_no, a.company_name, a.buyer_name, a.style_ref_no, a.insert_date, b.id, s.gmts_item_id";
	}
	//echo $sql;die;
	echo create_list_view("list_view", "GSD ID, Buyer,Style Ref.,Gmt. Item,Year, Job No.,Shipment Date,PO Numbers
", "70,70,120,140,60,70,90","890","230",0, $sql , "js_set_value", "up_id,id,po_job_no,buyer_name,style_ref_no,gmts_item_id,po_number", "", 1, "0,buyer_name,0,gmts_item_id,0,0,0,0,", $arr , "up_id,buyer_name,style_ref_no,gmts_item_id,year,job_no_prefix_num,shipment_date,po_number", "gsd_entry_controller","",'0,0,0,0,0,0,3,0') ;

exit();
}

if ($action=="load_php_data_to_form_style")
{
	/*$data=explode(',',$data);
	
	//echo $data[2];

	$buyer_name_arr=return_library_array( "select id,buyer_name from lib_buyer", "id","buyer_name"  );
	
	if($db_type==0)
	{
		$nameArray =sql_select("SELECT a.id,a.job_no as po_job_no,a.company_name,a.buyer_name,a.style_ref_no,a.gmts_item_id,group_concat(b.po_number) as po_number from  wo_po_details_master a, wo_po_break_down b where a.job_no= b.job_no_mst and a.job_no ='$data[2]' group by a.id,a.job_no,a.company_name,a.buyer_name,a.style_ref_no,a.gmts_item_id");
		
	}
	else
	{
		$nameArray =sql_select("SELECT a.id,a.job_no as po_job_no,a.company_name,a.buyer_name,a.style_ref_no,a.gmts_item_id,listagg(CAST(b.po_number AS VARCHAR(4000)),',') within group (order by b.po_number) as po_number from  wo_po_details_master a, wo_po_break_down b where a.job_no= b.job_no_mst and a.job_no ='$data[2]' group by a.id,a.job_no,a.company_name,a.buyer_name,a.style_ref_no,a.gmts_item_id");
		
	}
	foreach ($nameArray as $row)
	{	
		echo "document.getElementById('cbo_company_id').value 				= '".$row[csf("company_name")]."';\n";
		echo "document.getElementById('txt_style_ref').value 				= '".$row[csf("style_ref_no")]."';\n";
		echo "document.getElementById('txt_job_no').value					= '".$row[csf("po_job_no")]."';\n"; 
		echo "document.getElementById('txt_order_no').value					= '".$row[csf("po_number")]."';\n"; 
		echo "document.getElementById('cbo_gmt_item').value					= '".$garments_item[$row[csf("gmts_item_id")]]."';\n"; 
		
		//$gsd_id=return_field_value("id", "ppl_gsd_entry_mst", "company_id='".$row[csf("company_name")]."' and po_job_no='$data[2]' and status_active=1 and is_deleted=0");
		
		if( $data[0]!="" && $data[0]!=0 )
		{
			$nameArraysub =sql_select("SELECT id,working_hour,allowance,total_smv,sam_style,operation_count,pitch_time,man_power_1,man_power_2 from ppl_gsd_entry_mst where id='$data[0]'");
			foreach ($nameArraysub as $rowdata)
			{
				echo "document.getElementById('update_id').value					= '".$rowdata[csf("id")]."';\n"; //wo_po_id
				echo "document.getElementById('txt_working_hour').value				= '".$rowdata[csf("working_hour")]."';\n";
				echo "document.getElementById('txt_allowance').value				= '".$rowdata[csf("allowance")]."';\n";
				echo "document.getElementById('txt_sam_for_style').value			= '".$rowdata[csf("sam_style")]."';\n";
				echo "document.getElementById('txt_operation_count').value			= '".$rowdata[csf("operation_count")]."';\n";
				echo "document.getElementById('txt_pitch_time').value				= '".$rowdata[csf("pitch_time")]."';\n";
				echo "document.getElementById('txt_where_man_power').value			= '".$rowdata[csf("man_power_1")]."';\n";
				echo "document.getElementById('txt_where_man_power1').value			= '".$rowdata[csf("man_power_2")]."';\n";
			}
		}
	}*/
	
	if( $data!="" && $data!=0 )
	{
		$nameArraysub=sql_select("SELECT id,working_hour,allowance,total_smv,sam_style,operation_count,pitch_time,man_power_1,man_power_2,ready_to_approved from ppl_gsd_entry_mst where id='$data'");
		foreach ($nameArraysub as $rowdata)
		{
			echo "document.getElementById('update_id').value					= '".$rowdata[csf("id")]."';\n"; //wo_po_id
			echo "document.getElementById('txt_working_hour').value				= '".$rowdata[csf("working_hour")]."';\n";
			echo "document.getElementById('cbo_ready_to_approved').value		= '".$rowdata[csf("ready_to_approved")]."';\n";
            echo "document.getElementById('txt_allowance').value				= '".$rowdata[csf("allowance")]."';\n";
			echo "document.getElementById('txt_sam_for_style').value			= '".$rowdata[csf("sam_style")]."';\n";
			echo "document.getElementById('txt_operation_count').value			= '".$rowdata[csf("operation_count")]."';\n";
			echo "document.getElementById('txt_pitch_time').value				= '".$rowdata[csf("pitch_time")]."';\n";
			echo "document.getElementById('txt_where_man_power').value			= '".$rowdata[csf("man_power_1")]."';\n";
			echo "document.getElementById('txt_where_man_power1').value			= '".$rowdata[csf("man_power_2")]."';\n";
		}
	}
	exit();	
}

if ($action=="operation_popup")
{
	echo load_html_head_contents("Popup Info", "../../", 1, 1,'',1,'');
	$data=explode('_',$data);
?>	
    <script>
		$(document).ready(function(e) {
            setFilterGrid('tbl_list_search',-1);
        });
	
		  function js_set_value(id)
		  { 
			  document.getElementById('operation_id').value=id;
			  parent.emailwindow.hide();
		  }
	</script>
</head>
<body>
    <div style="width:100%" align="center">
        <input type="hidden" id="operation_id" />
    <div style="width:100%;">
        <table cellspacing="0" cellpadding="0" border="1" rules="all" width="768" class="rpt_table">
            <thead>
                <th width="50">SL</th>
                <th width="220">Operation Name</th>
                <th width="150">Resource</th>
                <th width="100">Operator SMV</th>
                <th width="100">Helper SMV</th>
                <th>Total SMV</th>
            </thead>
        </table>
    </div>
    <div style="width:768px;max-height:300px; overflow-y:scroll" id="gsd_operator_list_view" align="left">
        <table cellspacing="0" cellpadding="0" border="1" rules="all" width="750" class="rpt_table" id="tbl_list_search">
            <?php  
            $supplier_library_arr=return_library_array( "select id,supplier_name from lib_supplier", "id","supplier_name"  );
            $color_library_arr=return_library_array( "select id,color_name from lib_color", "id","color_name"  );
            $i=1;
            $sql_result=sql_select("select id,operation_name,resource_sewing,operator_smv,helper_smv,total_smv from lib_sewing_operation_entry where status_active=1 and is_deleted=0 order by operation_name asc");
            foreach($sql_result as $row)
            {
				if ($i%2==0)  
					$bgcolor="#E9F3FF";
				else
					$bgcolor="#FFFFFF";
                ?>
                <tr bgcolor="<? echo $bgcolor; ?>" style="text-decoration:none;cursor:pointer" onClick="js_set_value('<? echo $row[csf('id')]."_".$row[csf('operation_name')]."_".$row[csf('resource_sewing')]."_".$row[csf('operator_smv')]."_".$row[csf('helper_smv')]."_".$row[csf('total_smv')]; ?>');" > 
                    <td width="50" align="center"><? echo $i; ?></td>
                    <td width="220" ><p><? echo $row[csf('operation_name')]; ?></p></td>
                    <td width="150"><? echo $production_resource[$row[csf('resource_sewing')]]; ?></td>
                    <td width="100" align="right"><? echo $row[csf('operator_smv')]; ?></td>
                    <td width="100" align="right"><? echo $row[csf('helper_smv')]; ?></td>
                    <td align="right"><? echo $row[csf('total_smv')]; ?></td>
                </tr>
            <?
                $i++;
            }
            ?>
        </table>
    </div>
    </div>
</body>           
<script src="../../includes/functions_bottom.js" type="text/javascript"></script>
</html>
	<?	
	die;
}

if ($action=="load_php_dtls_form")
{
	
	$attach_id=return_library_array( "select id,attachment_name from lib_attachment",'id','attachment_name');
	//$gsdArray1=sql_select( "select listagg(gsd_dtls,',') within group (order by gsd_dtls) as gsd_dtls_id from pro_operation_bar_code");
	
	if($db_type==0)
	{
		$gsdArray=sql_select( "select group_concat(distinct(gsd_dtls)) as gsd_dtls_id from pro_operation_bar_code");
	}
	else
	{
		$gsdArray=sql_select( "select listagg(CAST(gsd_dtls as VARCHAR(4000)),',') within group (order by gsd_dtls) as gsd_dtls_id from pro_operation_bar_code");
	}
	//$gsdArray=implode(",",array_unique(explode(",",$gsdArray1[0][csf('gsd_dtls_id')])));
	//$gsdArray=explode(",",$gsdArray1[0][csf('gsd_dtls_id')]);
	
	
	//echo $gsdArray;die;
	
	
	$attach_gsd_dtls_id_array=explode(",",$gsdArray[0][csf('gsd_dtls_id')]);
	$sql="SELECT id,mst_id,row_sequence_no,body_part_id,lib_sewing_id,resource_gsd,attachment_id,oparetion_type_id,operator_smv,helper_smv from ppl_gsd_entry_dtls where mst_id=$data order by row_sequence_no asc";
	$sql_result =sql_select($sql);
	//echo $sql;
				
	$k=1;
	$num_rows=count($sql_result);
	$operator_total=0;
	$helper_total=0;
	$total_total=0;
	?>
    <table id="gsd_tbl" width="100%" cellspacing="0" border="1" rules="all" class="rpt_table">
    <?
	$body_part[0]="  ---Select---";
	foreach ($sql_result as $row)
	{
		if(in_array($row[csf("id")],$attach_gsd_dtls_id_array))
		{
			$disable="disabled='disabled'";
			$not_delete_row=1;// 1 means can not remove row
		}
		else
		{
			$disable="";
			$not_delete_row=0;
		}
	 ?>
        <tr id="gsd_<? echo $k; ?>">
            <td align="center">
                <input type="text" name="txt_seq_[]" id="txt_seq_<? echo $k; ?>"  class="text_boxes" style="width:40px" value="<? echo $row[csf("row_sequence_no")]; ?>" onBlur="duplication_check(<? echo $k; ?>);" <? echo $disable; ?>/>							 
            </td>
            <td align="center">
            	<input type="hidden" name="cbo_body_part_id_[]" class="text_boxes" id="cbo_body_part_id_<? echo $k; ?>" value="<? echo $row[csf("body_part_id")]; ?>" style="width:80px " />
                <input type="text" name="cbo_body_part_[]" id="cbo_body_part_<? echo $k; ?>"  class="text_boxes" style="width:83px" value="<? echo $body_part[$row[csf("body_part_id")]]; ?>" readonly <? echo $disable; ?> />
                <input type="hidden" name="not_delete_row_[]" class="text_boxes" id="not_delete_row_<? echo $k; ?>" value="<? echo $not_delete_row; ?>" style="width:80px " />							
            </td>
            <td> 
				<?
            		$operation_arr=return_library_array( "select id,operation_name from lib_sewing_operation_entry", "id","operation_name"  );
				?>
                <input type="hidden" name="sewing_id_[]" class="text_boxes" id="sewing_id_<? echo $k; ?>" value="<? echo $row[csf("lib_sewing_id")]; ?>" style="width:40px;" />
                <input type="hidden" name="update_id_dtls_[]" class="text_boxes" id="update_id_dtls_<? echo $k; ?>" value="<? echo $row[csf("id")]; ?>"  style="width:40px;" />
                <input type="text" name="txt_operation_[]" id="txt_operation_<? echo $k; ?>"  class="text_boxes" style="width:100px" value="<? echo $operation_arr[$row[csf("lib_sewing_id")]]; ?>" readonly <? echo $disable; ?> />
                <input type="hidden" name="operation_id_[]" class="text_boxes" id="operation_id_<? echo $k; ?>" value="<? echo $row[csf("lib_sewing_id")]; ?>" style="width:70px;" />						
            </td>
            <td> 
                <input type="text" name="txt_resource_[]" id="txt_resource_<? echo $k; ?>"  class="text_boxes" style="width:78px" value="<? echo $production_resource[$row[csf("resource_gsd")]]; ?>" readonly <? echo $disable; ?> />	
                <input type="hidden" name="txt_resource_id_[]" class="text_boxes" id="txt_resource_id_<? echo $k; ?>" value="<? echo $row[csf("resource_gsd")]; ?>" style="width:70px;" readonly/>						 
            </td>
            <td> 
                <input type="text" name="txt_attachment_[]" id="txt_attachment_<? echo $k; ?>"  class="text_boxes" style="width:73px" value="<? echo $attach_id[$row[csf("attachment_id")]]; ?>" readonly <? echo $disable; ?> />
                 <input type="hidden" name="txt_attachment_id_[]" id="txt_attachment_id_<? echo $k; ?>"  value="<? echo $row[csf("attachment_id")]; ?>"/>						 			</td>
            <td> 
			<?
            	$operator_smv_arr=return_library_array( "select id,operator_smv from lib_sewing_operation_entry", "id","operator_smv"  );
			?>
                <input type="text" name="txt_operator_[]" id="txt_operator_<? echo $k; ?>"  class="text_boxes_numeric" style="width:65px" value="<? echo $row[csf("operator_smv")]; ?>" readonly <? echo $disable; ?> />							 
            </td>
            <td>
			<?  	 
            	$helper_smv_arr=return_library_array( "select id,helper_smv from lib_sewing_operation_entry", "id","helper_smv"  );
			?>
                <input type="text" name="txt_helper_[]" id="txt_helper_<? echo $k; ?>"  class="text_boxes_numeric" style="width:65px" value="<? echo $row[csf("helper_smv")]; ?>" readonly <? echo $disable; ?> />							 
            </td>
            <td>
			<?
            	$total_smv_arr=return_library_array( "select id,total_smv from lib_sewing_operation_entry", "id","total_smv"  );
			?>
                <input type="text" name="txt_total_[]" id="txt_total_<? echo $k; ?>"  class="text_boxes_numeric" style="width:70px" value="<? echo $row[csf("helper_smv")]+$row[csf("operator_smv")]; ?>" readonly <? echo $disable; ?> />							 
            </td>
            <td> 
            <?
				$operation_type=array(1=>"Body Part Starting",2=>"Body Part Ending",3=>"Gmt Last Operation");
			?>
            	<input type="hidden"  name="cbo_operation_type_id_[]" class="text_boxes" id="cbo_operation_type_id_<? echo $k; ?>" value="<? echo $row[csf("oparetion_type_id")]; ?>" style="width:80px " />
                <input type="text" name="cbo_operation_type_[]" id="cbo_operation_type_<? echo $k; ?>"  class="text_boxes" style="width:90px" value="<? echo $operation_type[$row[csf("oparetion_type_id")]]; ?>" readonly <? echo $disable; ?> />							 
            </td>
            <td> 
                <input type="text" name="txt_remove[]" id="txt_remove<? echo $k; ?>"  class="formbutton" onClick="remove_row( <? echo $k; ?>)" style="width:50px" value="Remove" readonly />							 
            </td>
         </tr>
    <? 
	$k++;
	}
	?>
    </table>
    <?
	die;
}

if ($action=="attachment_popup")
{
	echo load_html_head_contents("Popup Info", "../../", 1, 1,'',1,'');
?>	
    <script>
		  function js_set_value(id)
		  { 
			  document.getElementById('attachment_id').value=id;
			  parent.emailwindow.hide();
		  }
	</script>
    <input type="hidden" id="attachment_id" />
    <?
		$sql="SELECT id,attachment_name from  lib_attachment"; 
		
		echo  create_list_view("list_view", "Attachment Name", "350","390","350",0, $sql , "js_set_value", "id,attachment_name", "", 1, "", 0 , "attachment_name", "gsd_entry_controller",'setFilterGrid("list_view",-1);','0') ;
		 die; 
}

if ($action=="save_update_delete")
{
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process )); 
	$fraction_count=25;   // Comes from Variable Settings..
	
	if ( $operation==0 )   // Insert Here========================================================================================delivery_id
	{
		$con = connect();
		if($db_type==0)
		{
			mysql_query("BEGIN");
		}
		if(str_replace("'",'',$update_id)==0)
		{		
			$id=return_next_id( "id", "ppl_gsd_entry_mst", 1 ) ; 	
		}
		
		/*if(str_replace("'",'',$update_id)==0)
		{		
			$id=return_next_id( "id", "ppl_gsd_entry_mst", 1 ) ; 	
			$field_array="id,company_id,po_dtls_id,po_job_no,po_break_down_id,working_hour,total_smv,allowance,sam_style,operation_count,pitch_time,man_power_1,man_power_2,per_hour_gmt_target,inserted_by,insert_date,status_active,is_deleted";
			$data_array="(".$id.",".$cbo_company_id.",".$wo_po_id.",".$job_no.",".$ord_id.",".$txt_working_hour.",".$txt_total_tot.",".$txt_allowance.",".$txt_sam_for_style.",".$txt_operation_count.",".$txt_pitch_time.",".$txt_where_man_power.",".$txt_where_man_power1.",1,".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."',1,0)"; 
			$rID=sql_insert("ppl_gsd_entry_mst",$field_array,$data_array,0);
		}
		else
		{
			$field_array="company_id*po_dtls_id*po_break_down_id*working_hour*total_smv*allowance*sam_style*operation_count*pitch_time*man_power_1*man_power_2*per_hour_gmt_target*updated_by*update_date";
			$data_array="".$cbo_company_id."*".$wo_po_id."*".$ord_id."*".$txt_working_hour."*".$txt_total_tot."*".$txt_allowance."*".$txt_sam_for_style."*".$txt_operation_count."*".$txt_pitch_time."*".$txt_where_man_power."*".$txt_where_man_power1."*1*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'"; 
			$rID=sql_update("ppl_gsd_entry_mst",$field_array,$data_array,"id",$update_id,0);
			$id=str_replace("'",'',$update_id);
			if( $id!="" ) $d=execute_query("delete from ppl_gsd_entry_dtls where mst_id=$id",1);
			
		}*/
		
		$id1=return_next_id( "id","ppl_gsd_entry_dtls",1);
		$field_array1 ="id,mst_id,row_sequence_no,	resource_gsd,body_part_id,lib_sewing_id,attachment_id,oparetion_type_id,total_smv,no_of_worker_calculative,no_of_worker_rounding,target_per_hour_operation,target_per_day_operation,operation_id,operator_smv,helper_smv";
		$total_operator_smv=0;
		$data_array1=""; $tot_no_of_worker=0; $add_comma=0; 
		//echo "10**".$num_row;die;
		for($i=1; $i<=$num_row; $i++)
		{
			$seq_no="txt_seq_".$i;
			$body_part="cbo_body_part_id_".$i;
			$sewing_id="sewing_id_".$i;
			$resource_id="txt_resource_id_".$i;
			$attachment_id="txt_attachment_id_".$i;
			$operation_type_id="cbo_operation_type_id_".$i;
			
			$operation_id="operation_id_".$i;
			$operator_smv="txt_operator_".$i;
			$helper_smv="txt_helper_".$i;
			$total_smv="txt_total_".$i;
			$total_operator_smv+=str_replace("'","",$$operator_smv);
			$no_of_worker_calculative=str_replace("'","",$$total_smv)/str_replace("'","",$txt_pitch_time);
			$no_of_worker_rounding=get_total_worker($fraction_count,$no_of_worker_calculative);
			$target_per_hr_operation=round(60/str_replace("'","",$$total_smv));
			$target_per_day_operation=str_replace("'","",$txt_working_hour)*$target_per_hr_operation;
			
			$tot_no_of_worker+=$no_of_worker_rounding;
			
			$updateid_dtls="update_id_dtls_".$i;
			
			if ($add_comma!=0) $data_array1 .=",";
			$data_array1 .="(".$id1.",".$id.",".$$seq_no.",".$$resource_id.",".$$body_part.",".$$sewing_id.",".$$attachment_id.",".$$operation_type_id.",".$$total_smv.",'".$no_of_worker_calculative."',".$no_of_worker_rounding.",".$target_per_hr_operation.",".$target_per_day_operation.",".$$operation_id.",".$$operator_smv.",".$$helper_smv.")";
			$id1=$id1+1;
			$add_comma++;
		}
		//echo  $total_operator_smv;die;
		$day_target=round(((60/str_replace("'",'',$txt_sam_for_style))*$tot_no_of_worker)*str_replace("'",'',$txt_working_hour));
		//echo "10**".$data_array1;die;
		if(str_replace("'",'',$update_id)==0)
		{		
			//$id=return_next_id( "id", "ppl_gsd_entry_mst", 1 ) ;                        
			$field_array="id, company_id, po_dtls_id, po_job_no, po_break_down_id, gmts_item_id, working_hour, total_smv, allowance, sam_style, operation_count, pitch_time, day_target, man_power_1, man_power_2, per_hour_gmt_target, inserted_by, insert_date, status_active, is_deleted, ready_to_approved";
			$data_array="(".$id.",".$cbo_company_id.",".$wo_po_id.",".$job_no.",".$ord_id.",".$cbo_gmt_item.",".$txt_working_hour.",".$txt_total_tot.",".$txt_allowance.",".$txt_sam_for_style.",".$txt_operation_count.",".$txt_pitch_time.",'".$day_target."',".$txt_where_man_power.",".$txt_where_man_power1.",1,".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."',1,0,$cbo_ready_to_approved)";                         
            $rID=sql_insert("ppl_gsd_entry_mst",$field_array,$data_array,1); 
		}
		else
		{			
            $field_array="po_dtls_id*po_break_down_id*gmts_item_id*working_hour*total_smv*allowance*sam_style*operation_count*pitch_time*day_target*man_power_1*man_power_2*per_hour_gmt_target*updated_by*update_date";
			$data_array="".$wo_po_id."*".$ord_id."*".$cbo_gmt_item."*".$txt_working_hour."*".$txt_total_tot."*".$txt_allowance."*".$txt_sam_for_style."*".$txt_operation_count."*".$txt_pitch_time."*'".$day_target."'*".$txt_where_man_power."*".$txt_where_man_power1."*1*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'"; 
			$rID=sql_update("ppl_gsd_entry_mst",$field_array,$data_array,"id",$update_id,1);
			$id=str_replace("'",'',$update_id);
			if( $id!="" ) $d=execute_query("delete from ppl_gsd_entry_dtls where mst_id=$id",1);
			
		}
		//echo "INSERT INTO ppl_gsd_entry_mst (".$field_array.") VALUES ".$data_array; die;
		//$field_array_smv="set_smv*po_break_down_id*gmts_item_id";
		//$data_array_smv="".$txt_sam_for_style."*".$txt_operation_count."";
		//echo "update wo_po_details_master set set_smv=$total_operator_smv  where  where gmts_item_id=$cbo_gmt_item  and job_no=$job_no";
		if($rID) $flag=1; else $flag=0; 
		//$smv=execute_query("update wo_po_details_master set set_smv=$total_operator_smv  where gmts_item_id=$cbo_gmt_item  and job_no=$job_no",1);
		
		//$rID2=sql_update("wo_po_details_master",$field_array_smv,$data_array_smv,"job_no",$update_id,1);
		
		
		if($data_array1!="")
		{
			//echo "INSERT INTO ppl_gsd_entry_dtls (".$field_array1.") VALUES ".$data_array1; die;
			$rID1=sql_insert("ppl_gsd_entry_dtls",$field_array1,$data_array1,1);
		}
		
		$smv_style_int=return_field_value("publish_shipment_date", "variable_order_tracking", "company_name=$cbo_company_id and variable_list=47 and status_active=1 and is_deleted=0");
		if($smv_style_int==3 || $smv_style_int==8)
		{
			$smv=fnc_smv_style_integration($db_type,$cbo_company_id,$job_no,$cbo_gmt_item,$total_operator_smv,$update_id,8);
			//if($smv) $flag=1; else $flag=0;
		}

		if($db_type==0)
		{
			if( $rID && $rID1)
			{
				mysql_query("COMMIT");  
				echo "0**".str_replace("'",'',$id);
			}
			else
			{
				mysql_query("ROLLBACK"); 
				echo "10**".$id;
			}
		}
		else if($db_type==2 || $db_type==1 )
		{
			if($rID || $rID1)
			{
				oci_commit($con);    
				echo "0**".str_replace("'",'',$id);
			}
			else
			{
				oci_rollback($con);
				echo "10**".$id;
			}
		}
		disconnect($con);
		die;
	}
	else if ($operation==1)   // Update Here=============================================================================
	{		
		$con = connect();
		if($db_type==0)
		{
			mysql_query("BEGIN");
		}
			//$id=str_replace("'",'',$update_id);
		
		/*$rID=sql_update("ppl_gsd_entry_mst",$field_array,$data_array,"id",$update_id,0);
		//$d=execute_query("delete from ppl_gsd_entry_dtls where mst_id=$update_id");
		if($rID) $flag=1; else $flag=0; 
		
		$deleted_id=str_replace("'",'',$deleted_id);
		if($deleted_id!="")
		{
			$delete=execute_query("delete from ppl_gsd_entry_dtls where mst_id=$update_id and id in($deleted_id)",1);
			if($flag==1) 
			{
				if($delete) $flag=1; else $flag=0; 
			} 
		}*/
		
		$id1=return_next_id( "id","ppl_gsd_entry_dtls",1);
		$field_array1 ="id,mst_id,row_sequence_no,resource_gsd,body_part_id,lib_sewing_id,attachment_id,oparetion_type_id,total_smv,no_of_worker_calculative,no_of_worker_rounding,target_per_hour_operation,target_per_day_operation,operation_id,operator_smv,helper_smv";
		
		$field_array_update ="row_sequence_no*resource_gsd*body_part_id*lib_sewing_id*attachment_id*oparetion_type_id*total_smv*no_of_worker_calculative*no_of_worker_rounding*target_per_hour_operation*target_per_day_operation*operation_id*operator_smv*helper_smv";
		
		$data_array1=""; $tot_no_of_worker=0; $add_comma=0;$total_operator_smv=0;
		for($i=1; $i<=$num_row; $i++)
		{
			$seq_no="txt_seq_".$i;
			$body_part="cbo_body_part_id_".$i;
			$sewing_id="sewing_id_".$i;
			$resource_id="txt_resource_id_".$i;
			$attachment_id="txt_attachment_id_".$i;
			$operation_type_id="cbo_operation_type_id_".$i;
			
			$operation_id="operation_id_".$i;
			$operator_smv="txt_operator_".$i;
			$helper_smv="txt_helper_".$i;
			$total_smv="txt_total_".$i;
			$total_operator_smv+=str_replace("'","",$$operator_smv);
			$no_of_worker_calculative=str_replace("'","",$$total_smv)/str_replace("'","",$txt_pitch_time);
			$no_of_worker_rounding=get_total_worker($fraction_count,$no_of_worker_calculative);
			$target_per_hr_operation=round(60/str_replace("'","",$$total_smv));
			$target_per_day_operation=str_replace("'","",$txt_working_hour)*$target_per_hr_operation;
			
			$tot_no_of_worker+=$no_of_worker_rounding;
			
			$updateid_dtls="update_id_dtls_".$i;
			if(str_replace("'",'',$$updateid_dtls)!="")
			{
				$id_arr[]=str_replace("'",'',$$updateid_dtls);
				$data_array_update[str_replace("'",'',$$updateid_dtls)] = explode(",",($$seq_no.",".$$resource_id.",".$$body_part.",".$$sewing_id.",".$$attachment_id.",".$$operation_type_id.",".$$total_smv.",'".$no_of_worker_calculative."',".$no_of_worker_rounding.",".$target_per_hr_operation.",".$target_per_day_operation.",".$$operation_id.",".$$operator_smv.",".$$helper_smv));
			}
			else
			{
				if ($add_comma!=0) $data_array1 .=",";
				$data_array1 .="(".$id1.",".$update_id.",".$$seq_no.",".$$resource_id.",".$$body_part.",".$$sewing_id.",".$$attachment_id.",".$$operation_type_id.",".$$total_smv.",'".$no_of_worker_calculative."',".$no_of_worker_rounding.",".$target_per_hr_operation.",".$target_per_day_operation.",".$$operation_id.",".$$operator_smv.",".$$helper_smv.")";
				$id1=$id1+1;
				$add_comma++;
			}
		}
		
		$day_target=round(((60/str_replace("'",'',$txt_sam_for_style))*$tot_no_of_worker)*str_replace("'",'',$txt_working_hour));
		$field_array="company_id*po_dtls_id*po_break_down_id*gmts_item_id*working_hour*total_smv*allowance*sam_style*operation_count*pitch_time*day_target*man_power_1*man_power_2*per_hour_gmt_target*updated_by*update_date*ready_to_approved";

		$data_array="".$cbo_company_id."*".$wo_po_id."*".$ord_id."*".$cbo_gmt_item."*".$txt_working_hour."*".$txt_total_tot."*".$txt_allowance."*".$txt_sam_for_style."*".$txt_operation_count."*".$txt_pitch_time."*'".$day_target."'*".$txt_where_man_power."*".$txt_where_man_power1."*1*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'*$cbo_ready_to_approved"; 
		
		$rID=sql_update("ppl_gsd_entry_mst",$field_array,$data_array,"id",$update_id,1);
		if($rID) $flag=1; else $flag=0; 
		
		//$smv=execute_query("update wo_po_details_master set set_smv=$total_operator_smv  where gmts_item_id=$cbo_gmt_item  and job_no=$job_no",1);
		
		$deleted_id=str_replace("'",'',$deleted_id);
		
		
		if($deleted_id!="")
		{
			$delete=execute_query("delete from ppl_gsd_entry_dtls where mst_id=$update_id and id in($deleted_id)",1);
			if($flag==1) 
			{
				if($delete) $flag=1; else $flag=0; 
			} 
		}
		
		//echo "INSERT INTO ppl_gsd_entry_dtls (".$field_array1.") VALUES ".$data_array1; die;
		
		//echo "sajjad23_".$field_array_update.'*****';
		//print_r($data_array_update);die;
		
		
		if($data_array1!="")
		{
			$rID2=sql_insert("ppl_gsd_entry_dtls",$field_array1,$data_array1,1);
			if($flag==1) 
			{
				if($rID2) $flag=1; else $flag=0; 
			} 
		}
		
		//echo "INSERT INTO ppl_gsd_entry_dtls (".$field_array_update.") VALUES ".$data_array_update; die;
	//print_r($id_arr);die;
		
		if($data_array_update!="")
		{
			$rID3=execute_query(bulk_update_sql_statement( "ppl_gsd_entry_dtls", "id", $field_array_update, $data_array_update, $id_arr ));//echo "sajjad2_".$rID3;die;
			if($flag==1) 
			{
				if($rID3) $flag=1; else $flag=0; 
			}  
		}
		
		$smv_style_int=return_field_value("publish_shipment_date", "variable_order_tracking", "company_name=$cbo_company_id and variable_list=47 and status_active=1 and is_deleted=0");
		if($smv_style_int==3 || $smv_style_int==8)
		{
			$smv=fnc_smv_style_integration($db_type,$cbo_company_id,$job_no,$cbo_gmt_item,$total_operator_smv,$update_id,8);
			if($smv) $flag=1; else $flag=0;
		}

		if($db_type==0)
		{
			if($flag==1)
			{
				mysql_query("COMMIT");  
				echo "1**".str_replace("'",'',$update_id);
			}
			
			else
			{
				mysql_query("ROLLBACK"); 
				echo "10**".str_replace("'",'',$update_id);
			}
		}
		else if($db_type==2 || $db_type==1 )
		{
			if($flag==1)
			{
				oci_commit($con);  
				echo "1**".str_replace("'",'',$update_id);
			}
			
			else
			{
				oci_rollback($con);
				echo "10**".str_replace("'",'',$update_id);
			}
		}
		
		disconnect($con);
		die;
	}
}
//Save Update Delete End..
if ($action=="copy_bulletin")
{
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process )); 
	$con = connect();
	if($db_type==0)
	{
		mysql_query("BEGIN");
	}
	
	$id=return_next_id( "id", "ppl_gsd_entry_mst", 1 ) ;
	
	if(str_replace("'",'',$cbo_bulletin_copy)==2)
	{
		$mst_data=sql_select("select id,system_no_prefix, extention_no, system_no, extended_from,company_id,po_dtls_id,po_job_no,po_break_down_id,gmts_item_id,working_hour,total_smv,allowance,sam_style,operation_count,pitch_time,day_target,man_power_1,man_power_2,per_hour_gmt_target,inserted_by,insert_date,status_active,is_deleted,inserted_by,insert_date,is_copied from ppl_gsd_entry_mst where id=$update_id");
		$system_no_prefix=$mst_data[0][csf('id')];
		$extention_no=return_field_value("max(extention_no) as extention_no","ppl_gsd_entry_mst","extended_from=$update_id","extention_no")+1;
		$system_no=$system_no_prefix."-".$extention_no;
	}
	else
	{
		$mst_data=sql_select("select system_no_prefix, extention_no, system_no, extended_from,company_id,po_dtls_id,po_job_no,po_break_down_id,gmts_item_id,working_hour,total_smv,allowance,sam_style,operation_count,pitch_time,day_target,man_power_1,man_power_2,per_hour_gmt_target,inserted_by,insert_date,status_active,is_deleted,inserted_by,insert_date,is_copied from ppl_gsd_entry_mst where id=$update_id");
		$system_no_prefix=$id;
		$system_no=$id;
		$extention_no='';
	}
		$field_array="id,system_no_prefix, extention_no, system_no, extended_from,company_id,po_dtls_id,po_job_no,po_break_down_id,gmts_item_id,working_hour,total_smv,allowance,sam_style,operation_count,pitch_time,day_target,man_power_1,man_power_2,per_hour_gmt_target,inserted_by,insert_date,status_active,is_deleted,is_copied";
		$data_array="(".$id.",".$system_no_prefix.",'".$extention_no."','".$system_no."',".$update_id.",'".$mst_data[0][csf('company_id')]."','".$mst_data[0][csf('po_dtls_id')]."','".$mst_data[0][csf('po_job_no')]."','".$mst_data[0][csf('po_break_down_id')]."','".$mst_data[0][csf('gmts_item_id')]."',".$mst_data[0][csf('working_hour')].",".$mst_data[0][csf('total_smv')].",".$mst_data[0][csf('allowance')].",".$mst_data[0][csf('sam_style')].",".$mst_data[0][csf('operation_count')].",".$mst_data[0][csf('pitch_time')].",".$mst_data[0][csf('day_target')].",'".$mst_data[0][csf('man_power_1')]."','".$mst_data[0][csf('man_power_2')]."',".$mst_data[0][csf('per_hour_gmt_target')].",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."',1,0,".$cbo_bulletin_copy.")"; 
			//$rID=sql_insert("ppl_gsd_entry_mst",$field_array,$data_array,1); 
	
	/*$field_array="id, system_no_prefix, extention_no, system_no, extended_from, buyer_id, style_ref, gmts_item_id, working_hour, operation_count, mc_operation_count, total_smv, tot_mc_smv,tot_manual_smv,tot_finishing_smv, is_copied, inserted_by, insert_date";
	$data_array="(".$id.",".$system_no_prefix.",'".$extention_no."','".$system_no."',".$update_id.",'".$mst_data[0][csf('buyer_id')]."','".$mst_data[0][csf('style_ref')]."','".$mst_data[0][csf('gmts_item_id')]."','".$mst_data[0][csf('working_hour')]."','".$mst_data[0][csf('operation_count')]."','".$mst_data[0][csf('mc_operation_count')]."','".$mst_data[0][csf('total_smv')]."','".$mst_data[0][csf('tot_mc_smv')]."','".$mst_data[0][csf('tot_manual_smv')]."','".$mst_data[0][csf('tot_finishing_smv')]."',".$cbo_bulletin_copy.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."')";*/
	
	$dtls_id_arr=array();
	$id_dtls=return_next_id( "id","ppl_gsd_entry_dtls",1);
	//$field_array1 ="id,mst_id,row_sequence_no,	resource_gsd,body_part_id,lib_sewing_id,attachment_id,oparetion_type_id,total_smv,no_of_worker_calculative,no_of_worker_rounding,target_per_hour_operation,target_per_day_operation,operation_id,operator_smv,helper_smv";
	
	$field_array_dtls ="id, mst_id, row_sequence_no, resource_gsd, body_part_id, lib_sewing_id, attachment_id, efficiency, target_on_full_perc, target_on_effi_perc, operator_smv, helper_smv, total_smv";
	
	$sql_dtls="select id, mst_id, row_sequence_no, resource_gsd, body_part_id, lib_sewing_id, attachment_id, efficiency, target_on_full_perc, target_on_effi_perc, operator_smv, helper_smv, total_smv from ppl_gsd_entry_dtls where mst_id=$update_id order by row_sequence_no";
	$result=sql_select($sql_dtls);
	foreach($result as $row)
	{
		if($data_array_dtls!="") $data_array_dtls.=","; 
		$data_array_dtls.="(".$id_dtls.",".$id.",".$row[csf('row_sequence_no')].",'".$row[csf('resource_gsd')]."','".$row[csf('body_part_id')]."','".$row[csf('lib_sewing_id')]."','".$row[csf('attachment_id')]."','".$row[csf('efficiency')]."','".$row[csf('target_on_full_perc')]."','".$row[csf('target_on_effi_perc')]."','".$row[csf('operator_smv')]."','".$row[csf('helper_smv')]."','".$row[csf('total_smv')]."')"; 
		
		$next_seq_no=$row[csf('row_sequence_no')];
		$dtls_id_arr[$row[csf('id')]]=$id_dtls;
		$id_dtls++;
	}
	$next_seq_no+=1;
	
	
	//echo "10**insert into ppl_gsd_entry_mst (".$field_array.") values ".$data_array;die;
	//echo "10**insert into ppl_gsd_entry_dtls (".$field_array_dtls.") values ".$data_array_dtls;die;
	
	//echo "insert into ppl_gsd_entry_mst (".$field_array.") values ".$data_array;die;
	//echo "insert into ppl_gsd_entry_dtls (".$field_array_dtls.") values ".$data_array_dtls;die;  
	$rID=sql_insert("ppl_gsd_entry_mst",$field_array,$data_array,1);
	$rID2=sql_insert("ppl_gsd_entry_dtls",$field_array_dtls,$data_array_dtls,1); 
	
	//echo $rID ."&&". $rID2;die;
	if($db_type==0)
	{
		if($rID && $rID2)
		{
			mysql_query("COMMIT");  
			echo "100**".$id."**".$next_seq_no."**".$system_no_prefix."**".$extention_no;
		}
		else 
		{
			mysql_query("ROLLBACK"); 
			echo "10**".$id;
		}
	}
	else if($db_type==2 || $db_type==1 )
	{
		if($rID && $rID2)
		{
			oci_commit($con);  
			echo "100**".$id."**".$next_seq_no."**".$system_no_prefix."**".$extention_no;
		}
		else
		{
			oci_rollback($con);
			echo "10**".$id;
		}
	}
	disconnect($con);
	die;
}
//Copy End...
function get_total_worker($fraction_count,$no_of_worker)
{
	if ($no_of_worker<1) return 1;
	else
	{
		$no_of_worker2 = number_format($no_of_worker,2,'.','');
		
		$no_of_worker=explode(".",$no_of_worker2);
		
		if ($no_of_worker[1]<$fraction_count) return $no_of_worker[0];
		else return $no_of_worker[0]+1;
	}
	die;
}

if ($action=="load_php_dtls_item")
{
	$data=explode("_",$data);
	$type=$data[0];
	if($type==0)
	{
		$sql_result =sql_select("select operation_name,bodypart_id,gmt_item_id,rate,uom,resource_sewing,operator_smv,helper_smv,total_smv,id from  lib_sewing_operation_entry where is_deleted=0 and bodypart_id=$data[1]");
	}
	else
	{
		$sql_result =sql_select("select operation_name,bodypart_id,gmt_item_id,rate,uom,resource_sewing,operator_smv,helper_smv,total_smv,id from  lib_sewing_operation_entry where is_deleted=0 and gmt_item_id=$data[1]");	
		
	}
				
	$k=1;
	$num_rows=count($sql_result);
	$operator_total=0;
	$helper_total=0;
	$total_total=0;
	if($num_rows>0)
	{
	?>
    <table id="tbl_body_item" width="100%" cellspacing="0" border="1" rules="all" class="rpt_table">
    	    <thead>
                <th width="40" align="center"></th>
                <th width="120" align="center">Body Part</th>
                <th width="160" align="center">Operation</th>
                <th width="120" align="center">Resource</th>
                <th width="70" align="center">Attachment</th>
                <th width="50" align="center">Operator SMV</th>
                <th width="50" align="center">Helper SMV</th>
                <th width="50" align="center">Total SMV</th>
                <th width="100" align="center">Operation Type</th>
              					
            </thead>
            <tbody id="">
    <?
	foreach ($sql_result as $row)
	{
		
	 ?>
        <tr id="gsdItem_<? echo $k; ?>">
           <td> <input type="checkbox" id="checkRow_<? echo $j; ?>" name="checkRow[]" checked="checked"  <? echo $subcon_cond;?> ></td>
            <td align="center" id="bodypart_td">
            	<? 
				if($row[csf("bodypart_id")]!=0) $dissable_id=1; else $dissable_id=0;
                  echo create_drop_down( "cbo_body_part_id", 142, $body_part,'', 1, "-- Select Body Part--","".$row[csf("bodypart_id")]."","",$dissable_id,"","","","","","","cbo_body_part_id[]" );
                ?> 
            </td>
            <td id="operation_td"> 
				
                <? echo $row[csf("operation_name")]; ?>
                <input type="hidden" name="operation_id[]" class="text_boxes" id="operation_id_<? echo $k; ?>" value="<? echo $row[csf("id")]; ?>"  />						
            </td>
            <td> 
              	<? echo $production_resource[$row[csf("resource_sewing")]]; ?>
                <input type="hidden" name="txt_resource_id[]" class="text_boxes" id="txt_resource_id_<? echo $k; ?>" value="<? echo $row[csf("resource_sewing")]; ?>" readonly/>	
                 <input type="hidden" name="txt_sewing_id[]" class="text_boxes" id="txt_sewing_id_<? echo $k; ?>" value="<? echo $row[csf("id")]; ?>" readonly/>						 
            </td>
            <td> 
                <input type="text" name="txt_attachment[]" id="txtAttachment_<? echo $k; ?>"  class="text_boxes" style="width:65px"  ondblclick="openmypage_attachment_multuple('txt_attachment_id_<? echo $k; ?>','txtAttachment_<? echo $k; ?>');" placeholder="Browse" />
                  <input type="hidden" name="txt_attachment_id[]" id="txt_attachment_id_<? echo $k; ?>" />						 			</td>
            <td>
                 <input type="text" name="txt_operator[]" id="txt_operator_<? echo $k; ?>" onKeyUp="math_operation( 'txt_total_<? echo $k; ?>', 'txt_operator_<? echo $k; ?>*txt_helper_<? echo $k; ?>', '+', '', ddd)"  class="text_boxes_numeric" style="width:50px" value="<? echo $row[csf("operator_smv")]; ?>"/>
            </td>
            <td>
                 <input type="text" name="txt_helper[]" id="txt_helper_<? echo $k; ?>" onKeyUp="math_operation( 'txt_total_<? echo $k; ?>', 'txt_operator_<? echo $k; ?>*txt_helper_<? echo $k; ?>', '+', '', ddd)"  class="text_boxes_numeric" style="width:50px" value="<? echo $row[csf("helper_smv")]; ?>"/>
            </td>
            <td>
                 <input type="text" name="txt_total[]" id="txt_total_<? echo $k; ?>"  class="text_boxes_numeric" style="width:50px" value="<? echo $row[csf("total_smv")]; ?>" disabled />
            </td>
            <td>
                <?
                $operation_type=array(1=>"Body Part Starting",2=>"Body Part Ending",3=>"Gmt Last Operation");
                   echo create_drop_down( "cbo_operation_type",115,$operation_type,"", 1, "--Select--", 0, "","","","","","","","","cbo_operation_type[]");
                ?>
            </td>
          
         </tr>
    <? 
	$k++;
	}
	?>
    
    </tbody>
    
    </table>
    
      <div style="width:100%; float:left" align="center">
                        <input type="button" name="close" id="close"  onClick="add_all_gsd_list();" class="formbutton" value="Close" style="width:100px" />
      </div>
    <?
	}
	die;
}

if ($action=="print_gsd_report")
{
	$data=explode("*",$data);
	if($db_type==0){
		mysql_query("SET CHARACTER SET utf8");
		mysql_query("SET SESSION collation_connection ='utf8_general_ci'");
	}
 	$lib_country=return_library_array( "select id,country_name from lib_country","id", "country_name"  );
	$lib_buyer=return_library_array( "select id,buyer_name from lib_buyer","id", "buyer_name"  );
	$lib_attachment=return_library_array( "select id,attachment_name from lib_attachment","id", "attachment_name"  );
	
	$lib_sewing_operation=return_library_array( "select id,operation_name from lib_sewing_operation_entry","id", "operation_name"  );
	$user_name_arr=return_library_array( "select id,user_name from user_passwd","id", "user_name"  );
	$define_machine_id=array(1,2,3,4,5,6,7,8,9,10,11,12,13,14,15,16,17,18,19,20,21,22,23,42,45,46,47,49,50,51,52,57,58,59,60,61,62,63,64,65,66,67,71,72,73,74,75,76,77,78,79,80,81,82,83,84,85,86,87,89,91,92);




	ob_start();
?>		
	<div style="width:1250px" align="center">
<? 
		$row_data=sql_select("select id,country_id,company_name,plot_no,level_no,road_no,block_no,city,zip_code,province,email,website from lib_company where id='$data[0]' order by id");
			foreach($row_data as $row_com)
			{
	?>
    		<table width="100%" cellpadding="0" cellspacing="0" style="border:1px solid black">
            <tr>
                <td align="center" style="font-size:20px">
					<? echo $row_com[csf("company_name")];	?>
                </td>
            </tr>
            <tr>
                <td align="center" style="font-size:10px">
                 	Plot No: <? echo $row_com[csf("plot_no")]; ?> Level No: <? echo $row_com[csf("level_no")]?> Road No: <? echo $row_com[csf("road_no")]; ?> Block No: <? echo $row_com[csf("block_no")];?> City No: <? echo $row_com[csf("city")];?> Zip Code: <? echo $row_com[csf("zip_code")]; ?> Province No: <?php echo $row_com[csf("province")];?> Country: <? echo $lib_country[$row_com[csf("country_id")]]; ?><br> Email Address: <? echo $row_com[csf("email")];?> Website No: <? echo $row_com[csf("website")];?>
                </td>  
            </tr>
            <tr><td align="center"><b>Operation Bulletin</b></td></tr>
        </table>
        <?  }  ?>
        <style type="text/css">
            table.display { border-collapse: collapse; }
            table.display td { padding: .3em; border: 1px black solid; }                	                   	
        </style>
		<?php
			
			//"select a.buyer_name,a.style_ref_no,a.style_description,b.image_location, group_concat(distinct(po_number)) as order_number from  wo_po_details_master a, common_photo_library b, wo_po_break_down c where a.job_no=b.master_tble_id and a.job_no ='$data[1]' and b.pic_size=0 and a.job_no=c.job_no_mst group by a.job_no"
			$image_name_array=return_library_array( "select master_tble_id,image_location from  common_photo_library", "master_tble_id", "image_location"  );
			
			if($db_type==0)
			{
				$row_data=sql_select("select a.buyer_name,a.style_ref_no,a.style_description, group_concat(distinct(po_number)) as order_number,a.job_no from  wo_po_details_master a,  wo_po_break_down c where a.job_no ='$data[1]' and a.job_no=c.job_no_mst group by a.job_no");
			}
			else
			{
				$row_data=sql_select("select a.buyer_name,a.style_ref_no,a.style_description, listagg(CAST(po_number as VARCHAR(4000)),',') within group (order by po_number) as order_number,a.job_no from  wo_po_details_master a,  wo_po_break_down c where a.job_no ='$data[1]' and a.job_no=c.job_no_mst group by a.job_no,a.buyer_name,a.style_ref_no,a.style_description");
			}
			
			foreach($row_data as $row_wo)
			{
				$order_number=implode(",",array_unique(explode(",",$row_wo[csf("order_number")])));
				$gmts_item_id=return_field_value("gmts_item_id","ppl_gsd_entry_mst", "id ='$data[2]'");
			?>
			<table width="100%" style="border:1px solid black" class="display">
				<tr>
					<td width="100" style="font-size:12px"><b>Buyer Name</b></td>
					<td width="200"><? echo $lib_buyer[$row_wo[csf("buyer_name")]];?></td>
					<td width="100" style="font-size:12px"><b>Style</b></td>
					<td width="200"><? echo $row_wo[csf('style_ref_no')];?></td>
					<td rowspan="4" align="center"><img src="../<? echo $image_name_array[$row_wo[csf('job_no')]]; ?>" width="150" height="70"; border="2" /></td>
				</tr>
				<tr>
					<td  style="font-size:12px"><b>Item</b></td>
					<td >
						<?
						echo $garments_item[$gmts_item_id];
						?>
					</td>
					<td style="font-size:12px"><b>Style Details</b></td>
					<td >
						<?
						echo $row_wo[csf('style_description')];
						?>
					</td>
				</tr>
				<tr>
					<td  style="font-size:12px"><b>Order</b></td>
					<td colspan="3" ><? echo split_string($order_number,50); ?></td>
				</tr>
				<tr>
					<td  style="font-size:12px"><b>Prepared By</b></td>
					<td style="font-size:12px" align="center"><? echo $user_name_arr[$_SESSION['logic_erp']['user_id']];?></td>
					<td style="font-size:12px"><b>IE</b></td>
					<td style="font-size:12px" align="center"></td>
				</tr>
			</table>
			<?php
        	} // End of Work Order Mast
        // Start of Item Description
       ?>
	   <style type="text/css">
            table.display { border-collapse: collapse; }
            table.display td { padding: .3em; border: 1px black solid; }                	                   	
        </style>
        <?php
				$sam2="";
				$total_no_of_worker_real2="";
				$working_hour2="";
				$tar_per="";
				$tar_per2="";
				$tar_per3="";
				
				if($db_type==0)
				{
					$sql_data=sql_select("select sum(no_of_worker_rounding) as no_of_worker_rounding, a.sam_style, a.working_hour from ppl_gsd_entry_mst a, ppl_gsd_entry_dtls b where a.id=b.mst_id and a.po_job_no ='$data[1]' and a.id=$data[2]  and a.is_deleted=0  group by a.po_job_no ");
				}
				else
				{
					$sql_data=sql_select("select sum(no_of_worker_rounding) as no_of_worker_rounding, a.sam_style, a.working_hour from ppl_gsd_entry_mst a, ppl_gsd_entry_dtls b where a.id=b.mst_id and a.po_job_no ='$data[1]'  and a.id=$data[2] and a.is_deleted=0 and a.gmts_item_id=$gmts_item_id group by a.po_job_no,a.sam_style, a.working_hour");
				
				}
				
                foreach($sql_data as $tar_day )
                {
					$sam2=$tar_day[csf("sam_style")]; 
					$total_no_of_worker_real2=$tar_day[csf("no_of_worker_rounding")];
					$working_hour2=$tar_day[csf("working_hour")]; 
				}
				
				$tar_per=(60/$sam2)*$working_hour2*$total_no_of_worker_real2;
				$tar_per2=(60/$sam2)*$working_hour2*$txt_man_power2;
				$tar_per3=(60/$sam2)*$working_hour2*$txt_man_power3;
		?>
        <table width="100%" class="display">
            <tr>
                <td rowspan="2" width="40" align="center">Seq No</td>
                <td rowspan="2" width="" align="center">Process Name</td>
                <td rowspan="2" width="100" align="center">Resource/ MC Type</td>
                <td rowspan="2" width="100" align="center">Attachment</td>
                
                <td rowspan="2" width="60" align="center">Operator's SMV</td>
                <td rowspan="2" width="60" align="center">Helper'S SMV</td>
                <td rowspan="2" width="30" align="center">Total SMV</td>
                 <td rowspan="2" width="60" align="center">Target/Hr</td>
                <td rowspan="2" width="60" align="center">No of Worker</td>
                <td rowspan="2" width="60" align="center">No of Worker(Real)</td>
               
                <td colspan="3" width="180" align="center">Target/Day</td>
            </tr>
            <tr>
                <td width="60" align="center"><? echo $total_no_of_worker_real2;?></td>
                <td width="60" align="center"><? echo $txt_man_power2;?></td>
                <td width="60" align="center"><? echo $txt_man_power3;?></td>
            </tr>
			<?php
			 
				$i=0;
				$counter="";
				$total_smv="";
				$total_operator_smv="";
				$total_helper_smv="";
				$total_no_of_worker_cal="";
				$total_no_of_worker_real="";
				$allowance="";
				$sam="";
				$working_hour="";
				$pitch_time="";
				$all_machine="";
				$new_category=array();
				 
				$sql_ord=sql_select("select mst_id,row_sequence_no,body_part_id,lib_sewing_id,resource_gsd,attachment_id,oparetion_type_id,b.total_smv as b_total_smv,no_of_worker_calculative,no_of_worker_rounding,target_per_hour_operation,target_per_day_operation,operation_id,operator_smv,helper_smv,a.allowance,a.sam_style,a.working_hour,a.pitch_time,man_power_1,man_power_2 from ppl_gsd_entry_mst a, ppl_gsd_entry_dtls b where a.id=b.mst_id and a.po_job_no ='$data[1]'  and a.id=$data[2] and a.is_deleted=0 order by b.row_sequence_no, body_part_id asc");
				$counter=count($sql_ord); $k=1; $count_mc=0;
                foreach($sql_ord as $row_ord2 )
                {
                 	if(!in_array($row_ord2[csf("body_part_id")],$new_category))
					{
						 
						$new_category[]=$row_ord2[csf("body_part_id")];
						?>
                        <tr>
                        	<td colspan="13" height="10" style="padding-left:30px" bgcolor="#CCCCCC"><strong><? echo $body_part[$row_ord2[csf("body_part_id")]]; ?></strong></td>
                        </tr>
                        <?
					}
					$i++;
            ?>
            <tr bgcolor="<? echo $bgcolor; ?>">
                <td width="20"><? echo $row_ord2[csf("row_sequence_no")]; ?></td>
                <td width="">
					<? echo $lib_sewing_operation[$row_ord2[csf("operation_id")]]; ?>
                </td>
                <td width="100"><? 
				 
					echo $production_resource[$row_ord2[csf("resource_gsd")]];
					
					
					$machine_count[$row_ord2[csf("resource_gsd")]]=$machine_count[$row_ord2[csf("resource_gsd")]]+$row_ord2[csf("no_of_worker_rounding")];
					if(in_array($row_ord2[csf("resource_gsd")],$define_machine_id)){
						$machine_arr[$row_ord2[csf("resource_gsd")]]=$machine_count[$row_ord2[csf("resource_gsd")]];
					}
					
				?></td> 
                <td width="30" align="center"><? echo $lib_attachment[$row_ord2[csf("attachment_id")]];?></td>	
                
                <td width="60" align="right"><? echo number_format($row_ord2[csf("operator_smv")],3);?></td>
                <td width="60" align="right"><? echo $row_ord2[csf("helper_smv")];?></td>
                <td width="30" align="right"><? echo number_format($row_ord2[csf("b_total_smv")],3);?></td>
                <td width="60" align="right"><? echo $row_ord2[csf("target_per_hour_operation")];?></td>
                <td width="60" align="right"><? echo $row_ord2[csf("no_of_worker_calculative")];?></td>
                <td width="60" align="right"><? echo $row_ord2[csf("no_of_worker_rounding")];?></td>
               
					<?php
					
					if($row_ord2[csf("operator_smv")]!=0)
					{
						$count_mc=$count_mc+$row_ord2[csf("no_of_worker_rounding")];
					}
                    if($i==1)
                    {
						$count_header=return_field_value("count(distinct(body_part_id)) as body_part_id","ppl_gsd_entry_dtls"," mst_id=".$row_ord2[csf("mst_id")]." group by mst_id ","body_part_id");
						
						$counter=$count_header+$counter;
                    ?>
                        <td rowspan="<? echo $counter;?>" width="60" align="right" bgcolor="#FFFFFF"><? echo round($tar_per); ?></td>
                        <td rowspan="<? echo $counter;?>" width="60" align="right" bgcolor="#FFFFFF"><? echo $row_ord2[csf("man_power_1")]; ?></td>
                        <td rowspan="<? echo $counter;?>" width="60" align="right" bgcolor="#FFFFFF"><? echo $row_ord2[csf("man_power_2")]; ?></td>
                    <?
                    }
					//echo $counter;
					?>
               </tr>
               	<?
               		$total_smv=$total_smv+$row_ord2[csf("b_total_smv")];
					$total_operator_smv=$total_operator_smv+$row_ord2[csf("operator_smv")];
					$total_helper_smv=$total_helper_smv+$row_ord2[csf("helper_smv")];
					$total_no_of_worker_cal=$total_no_of_worker_cal+$row_ord2[csf("no_of_worker_calculative")];
					$total_no_of_worker_real=$total_no_of_worker_real+$row_ord2[csf("no_of_worker_rounding")];
					$allowance=$row_ord2[csf("allowance")]; 
					$sam=$row_ord2[csf("sam_style")];
					$working_hour=$row_ord2[csf("working_hour")];
					$pitch_time=$row_ord2[csf("pitch_time")];
					//$all_machine=$machine_count[1]+$machine_count[5]+$machine_count[9]+$machine_count[13];
					$all_man_machine=$machine_count[$row_ord2[csf("resource_gsd")]];
                 }
				// echo  $all_man_machine;die;
				//print_r($machine_count);die;
                ?>
               <tr>
                    <td colspan="4" align="right">Total</td>
                    <td align="right"><? echo number_format($total_operator_smv,3);?></td>
                    <td align="right"><? echo $total_helper_smv;?></td>
                    <td align="right"><? echo number_format($total_smv,3);?></td>
                    <td>&nbsp;</td>
                    <td align="right"><? echo $total_no_of_worker_cal;?></td>
                    <td align="right"><? echo $total_no_of_worker_real;?></td>
                    
                </tr>
               <tr>
                    <td colspan="4" align="right">Allowance(%)</td>
                    <td width="30" align="right"><? echo $allowance;?></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                </tr>
               <tr>
                    <td height="25" colspan="4" align="right">SAM</td>
                    <td width="30" align="right"><? echo number_format($sam,3);?></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                </tr>
             </table>
             
             <table>
               	<tr style="border-left:hidden; border-right:hidden"><td colspan="12">&nbsp;</td></tr>
               <? $tot_mechine=0;
					foreach ($machine_arr as $key=>$values)
					{
						$tot_mechine+=$machine_count[$key];
					}
						
				?>
          		<tr style="border:hidden">
                    <td colspan="4">
                        <table class="display">
                            <tr><td colspan="2" width="210" align="center">WORKERS SUMMARY</td><td width="120" align="center">PITCH TIME</td></tr>
                            <tr ><td width="160">Total Machine Operators</td><td width="50" align="center"><? echo $tot_mechine;?></td><td width="90" rowspan="4" align="center" bgcolor="#FFFFFF"><? echo $pitch_time;?></td></tr>
                            <tr ><td width="160">Total Helpers</td><td width="50" align="center"><? echo $machine_count[40]; ?></td></tr>
                            <tr ><td width="160">Total QI</td><td width="50" align="center"><? echo $machine_count[41]; ?></td></tr>
                            <tr><td width="160">Total Man Power</td><td width="50" align="center"><? echo $total_no_of_worker_real;?></td></tr>
                        </table>
                    </td>
                    <td style="border-left:hidden; border-right:hidden">&nbsp;</td>
                    <td colspan="3">
                        <table class="display">
                            <tr><td colspan="2" width="220" align="center">TARGET SUMMARY</td></tr>
                            <tr><td width="170"><font size="-1">SAM</font></td><td width="50" align="right"><? echo $sam;?></td></tr>
                            <tr><td width="170">Total Working Hour</td><td width="50" align="right"><? echo $working_hour; ?></td></tr>
                            <tr><td width="170">Target Per Hour</td><td align="right"><? $target_per_hr=(60/$sam)*$total_no_of_worker_real; echo round($target_per_hr); ?></td></tr>
                            <tr><td width="170">Target Per Day</td><td align="right"><? $target_per_day=($working_hour*$target_per_hr); echo round($target_per_day); ?></td></tr>
                        </table>
                    </td>
                    <td style="border-left:hidden; border-right:hidden">&nbsp;</td>
                    <td colspan="3" valign="top">
                        <table class="display">
                            <tr><td colspan="3" width="240" align="center">MACHINE SUMMARY</td></tr>
                        <?  $i=0;
							
							foreach ($machine_arr as $key=>$values)
                            {
                                    if ($i==0){$sum_td='<td  align="center" rowspan="'.count($machine_arr).'">'.$tot_mechine.'</td>';}else{$sum_td='';}
                                    $i++;
                                    echo '<tr>
									<td width="120">'.$production_resource[$key].'</td>
									<td width="50" align="center">'.$machine_count[$key].'</td>'.$sum_td.'</tr>';
                            }
                            ?> 
                      </table>
                    </td>
            	</tr>
             </table>
             <br>
		 <?
            echo signature_table(101, $data[0], "1250px");
         ?>
    </div>
<?php	   
   $html = ob_get_contents();
   ob_clean();
   //previous file delete code-----------------------------//
	foreach (glob(""."*.pdf") as $filename) 
	{			
		@unlink($filename);
	}
	echo "$html"."####".$name;
	exit();
}

if ($action=="systemid_popup")
{
	echo load_html_head_contents("Popup Info", "../../", 1, 1,'',1,'');
	extract($_REQUEST);
?>
	<script>
	  function js_set_value(id)
	  { 
		 //alert(id);
		  document.getElementById('system_id').value=id;
		  parent.emailwindow.hide();
	  }
	</script>  
</head>
<body>
    <div align="center" style="width:100%;" >
        <form name="system_1" id="system_1" autocomplete="off">
            <table width="700" cellspacing="0" cellpadding="0" border="1" rules="all" class="rpt_table" align="center">
                <thead>                	 
                    <th>Buyer Name</th>
                    <th>Garments Item</th>
                    <th>Style Ref.</th>
                    <th>System ID</th>
                    <th><input type="reset" name="reset" id="reset" class="formbutton" value="Reset" style="width:100px;" /></th>           
                </thead>
                <tr class="general">
                    <td id="buyer_td">
						<?
                        	echo create_drop_down( "cbo_buyer_name", 160, "select buy.id, buy.buyer_name from lib_buyer buy where buy.status_active =1 and buy.is_deleted=0 $buyer_cond order by buy.buyer_name","id,buyer_name", 1, "-- Select Buyer --", $selected, "" );  
                        ?> 
                    </td>
                    <td>
                        <input type="hidden" id="system_id" style="width:100px;" >
                        <? echo create_drop_down( "cbo_gmt_item", 160, $garments_item,'', 1, "-Select Gmt. Item-","","","","" ); ?>
                    </td>
                    <td>
                    	<input type="text" style="width:130px" class="text_boxes"  name="txt_search_common" id="txt_search_common" />	
                    </td> 
                    <td>
                    	<input type="text" style="width:100px" class="text_boxes_numeric"  name="txt_system_id" id="txt_system_id" />	
                    </td>
                    <td align="center">
                    	<input type="button" name="button" class="formbutton" value="Show" onClick="show_list_view (document.getElementById('cbo_buyer_name').value+'_'+document.getElementById('txt_search_common').value+'_'+document.getElementById('cbo_gmt_item').value+'_'+document.getElementById('txt_system_id').value, 'systemId_list_view', 'search_div', 'gsd_entry_controller', 'setFilterGrid(\'list_view\',-1)')" style="width:100px;" />
                    </td>
                </tr>
            </table>
            <div id="search_div" style="margin-top:5px"></div>
        </form>
    </div>
</body>
<script src="../../includes/functions_bottom.js" type="text/javascript"></script>
</html>
<?
    exit();
}

if ($action=="systemId_list_view")
{
	$data=explode('_',$data);
	$buyer_name_arr=return_library_array( "select id,short_name from lib_buyer", "id","short_name"  );
	
	if ($data[0]!=0) $buyer_id_cond=" and a.buyer_id='$data[0]'"; else $buyer_id_cond="";
	if (trim($data[1])!="") $search_field_cond=" and LOWER(a.style_ref) like LOWER('%".trim($data[1])."%')"; else $search_field_cond=""; 
	if ($data[2]!=0) $gmt_item_cond=" and a.gmts_item_id='$data[2]'"; else { $gmt_item_cond=""; }
	if (trim($data[3])!="") $system_id_cond=" and a.id='".trim($data[3])."'"; else $system_id_cond=""; 
	
	$arr=array (2=>$buyer_name_arr,4=>$garments_item);
	
	$sql ="SELECT a.id, a.system_no_prefix, a.extention_no, a.is_copied, a.buyer_id, a.style_ref, a.working_hour, a.gmts_item_id, a.operation_count, a.mc_operation_count, a.total_smv, a.tot_mc_smv, a.tot_manual_smv, a.tot_finishing_smv, max(b.row_sequence_no) as seq_no,a.company_id
		FROM ppl_gsd_entry_mst a, ppl_gsd_entry_dtls b 
		where a.id=b.mst_id $buyer_id_cond $search_field_cond $gmt_item_cond  $system_id_cond
		group by a.id,a.company_id,a.system_no_prefix, a.extention_no, a.is_copied, a.buyer_id, a.style_ref, a.working_hour, a.gmts_item_id, a.operation_count, a.mc_operation_count, a.total_smv, a.tot_mc_smv, a.tot_manual_smv, a.tot_finishing_smv order by a.system_no_prefix";
	//echo $sql;
	echo create_list_view("list_view", "GSD ID, Extention No, Buyer, Style Ref., Gmt. Item, Working Hour, Total SMV", "60,80,70,150,130,90","750","250",0, $sql , "js_set_value", "id,buyer_id,style_ref,gmts_item_id,working_hour,seq_no,operation_count,mc_operation_count,total_smv,tot_mc_smv,tot_manual_smv,tot_finishing_smv,system_no_prefix,extention_no,company_id","",1,"0,0,buyer_id,0,gmts_item_id,0,0", $arr,"system_no_prefix,extention_no,buyer_id,style_ref,gmts_item_id,working_hour,total_smv","gsd_entry_controller","",'0,0,0,0,0,1,2');
	exit();
}

function fnc_smv_style_integration($db_type,$cbo_company_name,$update_id,$gmts_item,$sewSmv,$cutSmv,$page)
{
	if($page==8)
	{
		$gmts_item=str_replace("'","",$gmts_item);
		$upid=str_replace("'","",$cutSmv);
		$job_num=$update_id;
		
		if($job_num!='')
		{
			$job_no_all=array_unique(explode(",",$update_id));
			$job_str="";
			foreach($job_no_all as $job)
			{
				if($job_str=="") $job_str="'".$job."'"; else $job_str.=",'".$job."'";
			}
			$wo_po_set=sql_select("select a.id, a.job_no, a.gmts_item_id, a.set_item_ratio, a.smv_pcs, a.smv_set, a.smv_pcs_precost, a.smv_set_precost, a.complexity, a.embelishment, a.cutsmv_pcs, a.cutsmv_set, a.finsmv_pcs, a.finsmv_set, a.printseq, a.embro, a.embroseq, a.wash, a.washseq, a.spworks, a.spworksseq, a.gmtsdying, a.gmtsdyingseq, a.quot_id, b.set_break_down, b.total_set_qnty, b.set_smv, b.company_name, currency_id from wo_po_details_mas_set_details a, wo_po_details_master b where a.job_no=b.job_no and a.job_no in ($job_str) and b.is_deleted=0 and b.status_active=1");
			$cbo_company_name=str_replace("'","",$cbo_company_name);;
			$txt_job_no=$wo_po_set[0][csf("job_no")];
			$currercy=$wo_po_set[0][csf("currency_id")];
			$set_breck_down_sql=""; $job_arr=array(); $break_down_data='';
			$job_data_arr=array(); $add=0;
			foreach($wo_po_set as $row)
			{
				if($row[csf("cutsmv_pcs")]=='') $row[csf("cutsmv_pcs")]=0;
				if($row[csf("cutsmv_set")]=='') $row[csf("cutsmv_set")]=0;
				if($row[csf("finsmv_pcs")]=='') $row[csf("finsmv_pcs")]=0;
				if($row[csf("finsmv_set")]=='') $row[csf("finsmv_set")]=0;
				
				if($row[csf("printseq")]=='') $row[csf("printseq")]=1;
				if($row[csf("embroseq")]=='') $row[csf("embroseq")]=2;
				if($row[csf("washseq")]=='') $row[csf("washseq")]=3;
				if($row[csf("spworksseq")]=='') $row[csf("spworksseq")]=4;
				if($row[csf("gmtsdyingseq")]=='') $row[csf("gmtsdyingseq")]=5;
				$smv_set=0; $smv=0;
				if($row[csf("gmts_item_id")]==$gmts_item) $smv=$sewSmv;
				else $smv=$row[csf("smv_pcs")];
				
				$pre_smv=$row[csf("total_set_qnty")]*$row[csf("smv_pcs")];
				$smv_set=$row[csf("set_smv")]*$row[csf("set_item_ratio")];
				$jobset_smv=($smv*$row[csf("set_item_ratio")]);
				//echo $row[csf("set_smv")]."=".$smv_set;
				
				if(!in_array($row[csf('job_no')],$job_arr))
				{
					$add=0;
					$job_arr[]=$row[csf('job_no')];
					$break_down_data='';
				}
				//echo $k; //die;
				if ($add!=0) $break_down_data.="__";
				$break_down_data.=$row[csf("gmts_item_id")].'_'.$row[csf("set_item_ratio")].'_'.$row[csf("smv_pcs")].'_'.$row[csf("smv_set")].'_'.$row[csf("complexity")].'_'.$row[csf("embelishment")].'_'.$row[csf("cutsmv_pcs")].'_'.$row[csf("cutsmv_set")].'_'.$row[csf("finsmv_pcs")].'_'.$row[csf("finsmv_set")].'_'.$row[csf("printseq")].'_'.$row[csf("embro")].'_'.$row[csf("embroseq")].'_'.$row[csf("wash")].'_'.$row[csf("washseq")].'_'.$row[csf("spworks")].'_'.$row[csf("spworksseq")].'_'.$row[csf("gmtsdying")].'_'.$row[csf("gmtsdyingseq")].'_'.$row[csf("quot_id")];
				$add++;
				
				$job_data_arr[$row[csf('job_no')]]['str']=$break_down_data;//explode("*",("'".$break_down_data."'*'".$jobset_smv."'"));
				$job_data_arr[$row[csf('job_no')]]['smv']=$jobset_smv;
				
				if($set_breck_down_sql=="") $set_breck_down_sql=$row[csf("id")].'**'.$row[csf("gmts_item_id")].'**'.$row[csf("set_item_ratio")].'**'.$row[csf("smv_pcs")].'**'.$row[csf("smv_set")].'**'.$row[csf("smv_pcs_precost")].'**'.$row[csf("smv_set_precost")].'**'.$row[csf("quot_id")].'**'.$row[csf("job_no")].'**'.$smv_set.'**'.$smv.'**'.$jobset_smv;
				else $set_breck_down_sql.="***".$row[csf("id")].'**'.$row[csf("gmts_item_id")].'**'.$row[csf("set_item_ratio")].'**'.$row[csf("smv_pcs")].'**'.$row[csf("smv_set")].'**'.$row[csf("smv_pcs_precost")].'**'.$row[csf("smv_set_precost")].'**'.$row[csf("quot_id")].'**'.$row[csf("job_no")].'**'.$smv_set.'**'.$smv.'**'.$jobset_smv;
			}
			//print_r($job_data_arr); die;
			
			$field_arr_set="smv_pcs*smv_set*smv_pcs_precost*smv_set_precost";
			$set_breck_down_array=explode('***',str_replace("'",'',$set_breck_down_sql));
			for($c=0; $c < count($set_breck_down_array); $c++)
			{
				$set_breck_down_arr=explode('**',$set_breck_down_array[$c]);
				$idSet_arr[]=$set_breck_down_arr[0];
				
				$data_arr_set[$set_breck_down_arr[0]] =explode("*",("'".$set_breck_down_arr[10]."'*'".$set_breck_down_arr[9]."'*'".$set_breck_down_arr[10]."'*'".$set_breck_down_arr[9]."'"));
			}
			$update_ws_to_ord=execute_query(bulk_update_sql_statement("wo_po_details_mas_set_details", "id",$field_arr_set,$data_arr_set,$idSet_arr ));
			
			$field_arr_job="set_break_down*set_smv";
			foreach($job_data_arr as $jobno=>$data)
			{
				execute_query( "update wo_po_details_master set set_break_down='".$data['str']."', set_smv='".$data['smv']."' where  job_no ='".$jobno."'",0);
			}
			//print_r($cbo_company_name);
			//echo bulk_update_sql_statement("wo_po_details_master", "job_no",$field_arr_job,$data_arrjob,$jobSet_arr );
		
			
		
			$is_pre_cost="";
			//echo "select job_no, cm_cost_predefined_method_id, sew_smv, cut_smv, sew_effi_percent, cut_effi_percent, exchange_rate, machine_line, prod_line_hr, costing_per, costing_date from wo_pre_cost_mst where job_no='$txt_job_no' and is_deleted=0 and status_active=1";die;
			$pre_cost_data=sql_select("select job_no, cm_cost_predefined_method_id, sew_smv, cut_smv, sew_effi_percent, cut_effi_percent, exchange_rate, machine_line, prod_line_hr, costing_per, costing_date from wo_pre_cost_mst where job_no='$txt_job_no' and is_deleted=0 and status_active=1");
			$cm_cost=0;
			
			$cm_cost_predefined_method_id=$pre_cost_data[0][csf("cm_cost_predefined_method_id")]*1;
			$txt_sew_smv=str_replace("'","",$sewSmv)*1;//$pre_cost_data[0][csf("sew_smv")];
			$txt_cut_smv=$pre_cost_data[0][csf("cut_smv")];
			$txt_sew_efficiency_per=$pre_cost_data[0][csf("sew_effi_percent")]*1;
			$txt_cut_efficiency_per=$pre_cost_data[0][csf("cut_effi_percent")]*1;
			//var txt_efficiency_wastage= parseFloat(document.getElementById('txt_efficiency_wastage').value);
			
			$cbo_currercy=str_replace("'","",$currercy);
			$txt_exchange_rate= $pre_cost_data[0][csf("exchange_rate")]*1;
			$txt_machine_line= $pre_cost_data[0][csf("machine_line")];
			$txt_prod_line_hr= $pre_cost_data[0][csf("prod_line_hr")];
			$cbo_costing_per= $pre_cost_data[0][csf("costing_per")];
			$costing_date= $pre_cost_data[0][csf("costing_date")];
			//var txt_job_no= document.getElementById('txt_job_no').value;
			
			$cbo_costing_per_value=0;
			if($cbo_costing_per==1) $cbo_costing_per_value=12;
			else if($cbo_costing_per==2) $cbo_costing_per_value=1;
			else if($cbo_costing_per==3) $cbo_costing_per_value=24;
			else if($cbo_costing_per==4) $cbo_costing_per_value=36;
			else if($cbo_costing_per==5) $cbo_costing_per_value=48;
			
			$cm_cost_method_based_on=return_field_value("cm_cost_method_based_on", "variable_order_tracking", "company_name=$cbo_company_name and variable_list=22 and status_active=1 and is_deleted=0");
			if($cm_cost_method_based_on=="") $cm_cost_method_based_on=1;
			
			if($cm_cost_method_based_on==1)
			{
				if($costing_date=="" || $costing_date==0)
				{
					if($db_type==0) $txt_costing_date=change_date_format(date('d-m-Y'), "yyyy-mm-dd", "-");	
					else if($db_type==2) $txt_costing_date=change_date_format(date('d-m-Y'), "yyyy-mm-dd", "-",1);
				}
				else
				{
					if($db_type==0) $txt_costing_date=change_date_format($costing_date, "yyyy-mm-dd", "-");	
					else if($db_type==2) $txt_costing_date=change_date_format($costing_date, "yyyy-mm-dd", "-",1)	;
				}
			}
			else if($cm_cost_method_based_on==2)
			{
				$min_shipment_sql=sql_select("select job_no_mst, min(shipment_date) as min_shipment_date from wo_po_break_down where job_no_mst='$txt_job_no' and status_active=1 and is_deleted=0 group by job_no_mst");
				$min_shipment_date="";
				foreach($min_shipment_sql as $row){ $min_shipment_date=$row[csf('min_shipment_date')]; }
				if($db_type==0) $txt_costing_date=change_date_format($min_shipment_date, "yyyy-mm-dd", "-");	
				else if($db_type==2) $txt_costing_date=change_date_format($min_shipment_date, "yyyy-mm-dd", "-",1)	;
			}
			else if($cm_cost_method_based_on==3)
			{
				$max_shipment_sql=sql_select("select job_no_mst, max(shipment_date) as max_shipment_date from wo_po_break_down where job_no_mst='$txt_job_no' and status_active=1 and is_deleted=0 group by job_no_mst");
				$max_shipment_date="";
				foreach($max_shipment_sql as $row){ $max_shipment_date=$row[csf('max_shipment_date')]; }
				
				if($db_type==0) $txt_costing_date=change_date_format($max_shipment_date, "yyyy-mm-dd", "-");	
				else if($db_type==2) $txt_costing_date=change_date_format($max_shipment_date, "yyyy-mm-dd", "-",1)	;
			}
			else if($cm_cost_method_based_on==4)
			{
				$max_shipment_sql=sql_select("select job_no_mst, min(pub_shipment_date) as min_pub_shipment_date from wo_po_break_down where job_no_mst='$txt_job_no' and status_active=1 and is_deleted=0 group by job_no_mst");
				$min_pub_shipment_date="";
				foreach($max_shipment_sql as $row){ $min_pub_shipment_date=$row[csf('min_pub_shipment_date')]; }
				
				if($db_type==0) $txt_costing_date=change_date_format($min_pub_shipment_date, "yyyy-mm-dd", "-");	
				else if($db_type==2) $txt_costing_date=change_date_format($min_pub_shipment_date, "yyyy-mm-dd", "-",1)	;
			}
			else if($cm_cost_method_based_on==4)
			{
				$max_shipment_sql=sql_select("select job_no_mst, max(pub_shipment_date) as max_pub_shipment_date from wo_po_break_down where job_no_mst='$txt_job_no' and status_active=1 and is_deleted=0 group by job_no_mst");
				$max_pub_shipment_date="";
				foreach($max_shipment_sql as $row){ $max_pub_shipment_date=$row[csf('max_pub_shipment_date')]; }
				
				if($db_type==0) $txt_costing_date=change_date_format($max_pub_shipment_date, "yyyy-mm-dd", "-");	
				else if($db_type==2) $txt_costing_date=change_date_format($max_pub_shipment_date, "yyyy-mm-dd", "-",1)	;
			}
			
			$monthly_cm_expense=0; $no_factory_machine=0; $working_hour=0; $cost_per_minute=0; $depreciation_amorti=0; $operating_expn=0;
			$limit="";
			if($db_type==0) $limit="LIMIT 1"; else if($db_type==2) $limit="";
			$sqlstnd_cm="select monthly_cm_expense, no_factory_machine, working_hour, cost_per_minute, depreciation_amorti, operating_expn from lib_standard_cm_entry where company_id=$cbo_company_name and '$txt_costing_date' between applying_period_date and applying_period_to_date and status_active=1 and is_deleted=0 $limit";
			$sqlstnd_cm_arr=sql_select($sqlstnd_cm);
			foreach ($sqlstnd_cm_arr as $row)
			{
				if($row[csf("monthly_cm_expense")] !="") $monthly_cm_expense=$row[csf("monthly_cm_expense")];
				if($row[csf("no_factory_machine")] !="") $no_factory_machine=$row[csf("no_factory_machine")];
				if($row[csf("working_hour")] !="") $working_hour=$row[csf("working_hour")];
				if($row[csf("cost_per_minute")] !="") $cost_per_minute=$row[csf("cost_per_minute")];
				if($row[csf("depreciation_amorti")] !="") $depreciation_amorti=$row[csf("depreciation_amorti")];
				if($row[csf("operating_expn")] !="")$operating_expn=$row[csf("operating_expn")];
			}
			//$data=$monthly_cm_expense."_".$no_factory_machine."_".$working_hour."_".$cost_per_minute."_".$depreciation_amorti."_".$operating_expn;
			
			$sql_pre_cost_dtls="select sum(price_dzn) as price_dzn, sum(price_pcs_or_set) as price_pcs_set, sum(total_cost-cm_cost) as prev_tot_cost from wo_pre_cost_dtls where job_no='$txt_job_no' and is_deleted=0 and status_active=1 group by job_no";
			$sql_pre_cost_dtls_arr=sql_select($sql_pre_cost_dtls);
			$price_dzn=0; $cost_pcs_set=0; $prev_tot_cost=0;
			
			$price_dzn=$sql_pre_cost_dtls_arr[0][csf("price_dzn")]*1;
			$price_pcs_set=$sql_pre_cost_dtls_arr[0][csf("price_pcs_set")]*1;
			$prev_tot_cost=$sql_pre_cost_dtls_arr[0][csf("prev_tot_cost")]*1;
			
			
			if (count($pre_cost_data)>0)
			{
				execute_query( "update wo_pre_cost_mst set sew_smv='$txt_sew_smv', cut_smv='$txt_cut_smv' where job_no ='".$txt_job_no."'",1);
				if($cm_cost_predefined_method_id==1)
				{
					$txt_efficiency_wastage=100-$txt_sew_efficiency_per;
					//document.getElementById('txt_efficiency_wastage').value=txt_efficiency_wastage;
					$cm_cost=($txt_sew_smv*$cost_per_minute*$cbo_costing_per_value)+(($txt_sew_smv*$cost_per_minute*$cbo_costing_per_value)*($txt_efficiency_wastage/100));
					//alert(txt_exchange_rate)
					$cm_cost=$cm_cost/$txt_exchange_rate;
				}
				else if($cm_cost_predefined_method_id==2)
				{
					$cu=0; $su=0;
					$cut_per=$txt_cut_efficiency_per/100;
					$sew_per=$txt_sew_efficiency_per/100;
					$cu=($txt_cut_smv*trim(($cost_per_minute*1))*$cbo_costing_per_value)/($cut_per*1);
					if($cu=="") $cu=0;
					
					$su=($txt_sew_smv*trim(($cost_per_minute*1))*$cbo_costing_per_value)/($sew_per*1);
					if($su=='') $su=0;
					$cm_cost=($cu+$su)/$txt_exchange_rate;
				}
				else if($cm_cost_predefined_method_id==3)
				{
					//3. CM Cost = {(MCE/26)/NFM)*MPL)}/[{(PHL)*WH}]*Costing Per/Exchange Rate
					$per_day_cost=$monthly_cm_expense/26;
					$per_machine_cost=$per_day_cost/$no_factory_machine;
					$per_line_cost=$per_machine_cost*$txt_machine_line;
					$total_production_per_line=$txt_prod_line_hr*$working_hour;
					$per_product_cost=$per_line_cost/$total_production_per_line;
					
					$cm_cost=($per_product_cost*$cbo_costing_per_value)/$txt_exchange_rate;
				}
				else if($cm_cost_predefined_method_id==4)
				{
					$sew_per=$txt_sew_efficiency_per/100;
					$su=((trim(($cost_per_minute*1))/$sew_per)*($txt_sew_smv*$cbo_costing_per_value));
					$cm_cost=$su/$txt_exchange_rate;
				}
				
				$dec_type=0;
				if (str_replace("'","",$currercy)==1) $dec_type=4; else $dec_type=5;
				
				$cm_cost=number_format($cm_cost,4,'.','');
				$cm_cost_per=number_format((($cm_cost/$price_dzn)*100),2,'.','');
				
				$tot_cost=number_format(($prev_tot_cost+$cm_cost),4,'.','');
				$tot_cost_per=number_format((($tot_cost/$price_dzn)*100),2,'.','');
				
				$margin_dzn=number_format(($price_dzn-$tot_cost),4,'.','');
				$margin_dzn_per=number_format((100-$tot_cost_per),2,'.','');
				
				$cost_pcs_set=number_format(($tot_cost/$cbo_costing_per_value),4,'.','');
				$cost_pcs_set_percent=number_format((($cost_pcs_set/$price_pcs_set)*100),2,'.','');
				
				$margin_pcs_set=number_format(($price_pcs_set-$cost_pcs_set),4,'.','');
				$margin_pcs_set_per=number_format((100-$cost_pcs_set_percent),2,'.','');
				
				$field_arr_pre_cost="cm_cost*cm_cost_percent*total_cost*total_cost_percent*margin_dzn*margin_dzn_percent*cost_pcs_set*cost_pcs_set_percent*margin_pcs_set*margin_pcs_set_percent";
				$data_arr_pre_cost="'".$cm_cost."'*'".$cm_cost_per."'*'".$tot_cost."'*'".$tot_cost_per."'*'".$margin_dzn."'*'".$margin_dzn_per."'*'".$cost_pcs_set."'*'".$cost_pcs_set_percent."'*'".$margin_pcs_set."'*'".$margin_pcs_set_per."'";
				
				$rID2=sql_update("wo_pre_cost_dtls",$field_arr_pre_cost,$data_arr_pre_cost,"job_no","'".$txt_job_no."'",1);
			}
			else
			{
				return;
			}
		}
		//return $field_arr_pre_cost.'='.$data_arr_pre_cost; 
	}
}

?>