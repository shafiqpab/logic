<?
header('Content-type:text/html; charset=utf-8');
session_start();
if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");
include('../../../includes/common.php');
$permission=$_SESSION['page_permission'];
$data=$_REQUEST['data'];
$action=$_REQUEST['action'];
$user_name=$_SESSION['logic_erp']['user_id'];
$buyer_arr=return_library_array( "select id, buyer_name from lib_buyer where status_active=1 and is_deleted=0",'id','buyer_name');
$company_arr=return_library_array( "select id, company_name from lib_company where status_active=1 and is_deleted=0",'id','company_name');
$dealing_arr=return_library_array( "select id, team_member_name from lib_mkt_team_member_info where status_active=1 and is_deleted=0",'id','team_member_name');
$sample_name_arr=return_library_array( "select id,sample_name  from lib_sample where is_deleted=0 and status_active=1 order by sample_name", "id", "sample_name"  );
$color_arr=return_library_array( "select id, color_name from lib_color where status_active=1 and is_deleted=0","id","color_name");
$size_arr=return_library_array( "select id, size_name from lib_size where status_active=1 and is_deleted=0","id","size_name");
$season_arr=return_library_array( "select id, season_name from lib_buyer_season where status_active=1 and is_deleted=0","id","season_name");

if ($action=="load_drop_down_buyer")
{
	echo create_drop_down( "cbo_buyer_name", 130, "select buy.id,buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company='$data' and buy.id in (select  buyer_id from  lib_buyer_party_type where party_type in (1,3,21,90)) $buyer_cond order by buyer_name","id,buyer_name", 1, "-- Select --", $selected, "load_drop_down( 'requires/sample_requisition_wise_delivery_status_report_controller', this.value, 'load_drop_down_sample_for_buyer', 'sample_td');" );
	exit();
}
if ($action=="load_drop_down_sample_for_buyer")
{
	echo create_drop_down( "cboSampleName_1", 100, "select a.id,a.sample_name,b.sequ from lib_sample a,lib_buyer_tag_sample b where a.id=b.tag_sample and  b.buyer_id=$data and b.sequ  is not null and
 a.status_active=1 and a.is_deleted=0  group by  a.id,a.sample_name,b.sequ order by b.sequ ","id,sample_name", 1, "-- Select Buyer --", $selected, "" );
}
if($action=="requisition_id_popup")
{
	extract($_REQUEST);
	echo load_html_head_contents("Sample Requisition Info","../../../", 1, 1, $unicode);
	if($cbo_company_name>0) $isDis=1; else $isDis=0;
?>
	<script>
		$(document).ready(function(e) {
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
		}

		function js_set_value( mst_id )
		{
			var data = mst_id.split("_");
			$("#hidden_req_id").val(data[0]);
			$("#hidden_req_no").val(data[1]);
			//document.getElementById('selected_job').value=mst_id;
			parent.emailwindow.hide();
		}
    </script>
</head>
<body>
	<div align="center" style="width:100%;" >
    <input type="hidden" id="hidden_req_id" name="hidden_req_id">
	<input type="hidden" id="hidden_req_no" name="hidden_req_no">
	<form name="searchsampledevelopmentfrm_1"  id="searchsampledevelopmentfrm_1" autocomplete="off">
        <table width="950" cellspacing="0" cellpadding="0" border="1" rules="all" class="rpt_table" align="center">
            <thead>
                <th colspan="10"><? echo create_drop_down( "cbo_search_category", 140, $string_search_type,'', 1, "-- Search Catagory --" ); ?></th>
            </thead>
            <thead>
                <th width="140" class="must_entry_caption">Company Name</th>
                <th width="157">Buyer Name</th>
                <th width="70">Requisition No</th>
                <th width="70">Booking No</th>
                <th width="70">Style ID</th>
                <th width="80">Style Name</th>
                <th width="90">Sample Stage</th>
                <th width="130" colspan="2">Requisition date</th>
                <th><input type="reset" name="reset" id="reset" class="formbutton" value="Reset" style="width:80px;" /></th>
            </thead>
            <tr class="general">
                <td>
                    <input type="hidden" id="selected_job">
                    <? echo create_drop_down( "cbo_company_mst", 140, "select id,company_name from lib_company comp where status_active =1 and is_deleted=0 and core_business not in(3) $company_credential_cond  order by company_name","id,company_name",1, "-- Select Company --", $cbo_company_name,"load_drop_down( 'sample_requisition_wise_delivery_status_report_controller', this.value, 'load_drop_down_buyer_req', 'buyer_td_req' );",$isDis ); ?> </td>
                <td id="buyer_td_req"><? echo create_drop_down( "cbo_buyer_name", 157, $blank_array,'', 1, "-- Select Buyer --" ); ?></td>
                <td><input type="text" style="width:60px" class="text_boxes" name="txt_requisition_num" id="txt_requisition_num"  /></td>
                <td><input type="text" style="width:60px" class="text_boxes" name="txt_booking_num" id="txt_booking_num"  /></td>
                <td><input type="text" style="width:60px" class="text_boxes"  name="txt_style_id" id="txt_style_id"  /></td>
                <td><input type="text" style="width:70px" class="text_boxes"  name="txt_style_name1" id="txt_style_name1"  /></td>
                <td><? echo create_drop_down( "cbo_sample_stage", 90, $sample_stage, "", 1, "-Select Stage-", $selected, "", "", "1,2,3","" ); ?></td>

                <td><input name="txt_date_from" id="txt_date_from" class="datepicker" style="width:55px" placeholder="From Date"></td>
                <td><input name="txt_date_to" id="txt_date_to" class="datepicker" style="width:55px" placeholder="To Date"></td>
                <td>
                    <input type="button" name="btn_show" id="btn_show" class="formbutton" value="Show" onClick="show_list_view ( document.getElementById('cbo_search_category').value+'_'+document.getElementById('txt_style_id').value+'_'+document.getElementById('cbo_company_mst').value+'_'+document.getElementById('cbo_buyer_name').value+'_'+document.getElementById('txt_date_from').value+'_'+document.getElementById('txt_date_to').value+'_'+document.getElementById('txt_style_name1').value+'_'+document.getElementById('txt_requisition_num').value+'_'+document.getElementById('cbo_sample_stage').value+'_'+document.getElementById('txt_booking_num').value+'_'+<? echo $type ?>, 'create_requisition_id_search_list_view', 'search_div', 'sample_requisition_wise_delivery_status_report_controller', 'setFilterGrid(\'list_view\',-1)')" style="width:80px;" />
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
<script>
 var company=$("#cbo_company_mst").val();
 load_drop_down( 'sample_requisition_wise_delivery_status_report_controller', company, 'load_drop_down_buyer', 'buyer_td_req' )
 </script>
</html>
<?
exit();
}

if($action=="create_requisition_id_search_list_view")
{
	$data=explode('_',$data);
	if ($data[2]!=0) $company=" and a.company_id='$data[2]'"; else { echo "Please Select Company First."; die; }
	if ($data[3]!=0) $buyer=" and a.buyer_name='$data[3]'"; else $buyer="";//{ echo "Please Select Buyer First."; die; }
	if($data[0]==1)
		{
		   if (trim($data[1])!="") $style_id_cond=" and a.id='$data[1]'"; else $style_id_cond="";
		   if ($data[6]!="") $style_cond=" and a.style_ref_no='$data[6]'"; else $style_cond="";
		}

	if($data[0]==4 || $data[0]==0)
		{
		  if (trim($data[1])!="") $style_id_cond=" and a.id like '%$data[1]%' "; else $style_id_cond="";
		  if ($data[6]!="") $style_cond=" and a.style_ref_no like '%$data[6]%' "; else $style_cond="";
		}

	if($data[0]==2)
		{
		  if (trim($data[1])!="") $style_id_cond=" and a.id like '$data[1]%' "; else $style_id_cond="";
		  if ($data[6]!="") $style_cond=" and a.style_ref_no like '$data[6]%' "; else $style_cond="";
		}

	if($data[0]==3)
		{
		  if (trim($data[1])!="") $style_id_cond=" and a.id like '%$data[1]' "; else $style_id_cond="";
		  if ($data[6]!="") $style_cond=" and a.style_ref_no like '%$data[6]' "; else $style_cond="";
		}
$type_id=$data[10];
//echo $type_id.'d,';

	if($db_type==0)
	{
	if ($data[4]!="" &&  $data[5]!="") $estimated_shipdate  = "and a.requisition_date  between '".change_date_format($data[4], "yyyy-mm-dd", "-")."' and '".change_date_format($data[5], "yyyy-mm-dd", "-")."'"; else $estimated_shipdate ="";
	}
	if($db_type==2)
	{
	if ($data[4]!="" &&  $data[5]!="") $estimated_shipdate  = "and a.requisition_date  between '".change_date_format($data[4], "yyyy-mm-dd", "-",1)."' and '".change_date_format($data[5], "yyyy-mm-dd", "-",1)."'"; else $estimated_shipdate ="";
	}
	if ($data[7]!="") $requisition_num=" and a.requisition_number_prefix_num like '%$data[7]' "; else $requisition_num="";

	if ($data[8]!=0) $stage_id=" and a.sample_stage_id= '$data[8]' "; else  $stage_id="";
	if ($data[9]!=0) $book_cond=" and id in(SELECT b.style_id from wo_non_ord_samp_booking_mst a,wo_non_ord_samp_booking_dtls b where a.booking_no=b.booking_no and a.status_active=1 and b.status_active=1 and a.booking_no_prefix_num='$data[9]') "; else  $book_cond="";


	$buyer_arr=return_library_array( "select id, buyer_name from lib_buyer",'id','buyer_name');
	$comp=return_library_array( "select id, company_name from lib_company",'id','company_name');

	$dealing_marchant=return_library_array( "select id, team_member_name from lib_mkt_team_member_info",'id','team_member_name');
	$req_wise_booking=return_library_array( "select style_id, booking_no from wo_non_ord_samp_booking_dtls where status_active=1",'style_id','booking_no');
	$team_leader=return_library_array( "select id, team_leader_name from lib_marketing_team",'id','team_leader_name');

	$arr=array (2=>$buyer_arr,4=>$product_dept,5=>$dealing_marchant,6=>$sample_stage,7=>$req_wise_booking);
	if($type_id==1)
	{
		$search_type="id,req_no";
	}
	else if($type_id==2)
	{
		$search_type="id,b_no";
	}
	else if($type_id==3)
	{
		$search_type="id,style_ref_no";
	}
	if($db_type==0) $yr_cond="SUBSTRING_INDEX(a.insert_date, '-', 1) as year";
	else if($db_type==2) $yr_cond="to_char(a.insert_date,'YYYY') as year";
	$sql="";
	if($type_id==1 || $type_id==3)
	{
		$sql= "SELECT a.id,a.requisition_number_prefix_num as req_no,$yr_cond,a.company_id,a.buyer_name,a.style_ref_no as style_ref_no,a.product_dept,a.dealing_marchant,a.sample_stage_id,b.booking_no,c.booking_no_prefix_num as b_no from sample_development_mst a, wo_non_ord_samp_booking_dtls b,wo_non_ord_samp_booking_mst c where  a.id=b.style_id and c.booking_no=b.booking_no and  a.entry_form_id=203 and  a.status_active=1 and a.is_deleted=0 $company $buyer $style_id_cond $style_cond $estimated_shipdate $requisition_num  $stage_id $book_cond order by a.id DESC";
	
	}
	else
	{
		$sql= "SELECT a.id,a.requisition_number_prefix_num as req_no,$yr_cond,a.company_id,a.buyer_name,a.style_ref_no as style_ref_no,a.product_dept,a.dealing_marchant,a.sample_stage_id,b.booking_no,c.booking_no_prefix_num as b_no from sample_development_mst a , wo_non_ord_samp_booking_dtls b,wo_non_ord_samp_booking_mst c where  a.id=b.style_id and c.booking_no=b.booking_no and  a.entry_form_id=203 and  a.status_active=1 and a.is_deleted=0 $company $buyer $style_id_cond $style_cond $estimated_shipdate $requisition_num  $stage_id $book_cond order by a.id DESC";
	}
	

	echo  create_list_view("list_view", "Year,Requisition No,Buyer Name,Style Name,Product Department,Dealing Merchant,Sample Stage,Booking No", "60,140,140,100,90,90,100,100","950","240",0, $sql , "js_set_value", "$search_type", "", 1, "0,0,buyer_name,0,product_dept,dealing_marchant,sample_stage_id,0", $arr , "year,req_no,buyer_name,style_ref_no,product_dept,dealing_marchant,sample_stage_id,booking_no", "",'','0,0,0,0,0,0') ;

	exit();
}

if($action=="report_generate")
{
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process ));
	$req_no=str_replace("'", "", $txt_req_no);
	$req_id=str_replace("'", "", $txt_req_id);
	$txt_booking_no=str_replace("'", "", $txt_booking_no);
	$style_ref=str_replace("'", "", $txt_style_ref);
	//$req_id=str_replace("'", "", $txt_req_id);
	$txt_style_ref=str_replace("'", "", $txt_style_ref);
	$cbo_company_name=str_replace("'", "", $cbo_company_name);
	$cbo_sample_stage=str_replace("'", "", $cbo_sample_stage);
	$cbo_sample_name=str_replace("'", "", $cbo_sample_name);
	 $req_id_cond="";
	if($req_id!="") 
	 {
		  $req_id_cond="and a.id in($req_id) ";
	 }
	else 
	{
		if($req_no!="") $req_no_cond="and a.requisition_number_prefix_num in($req_no)";else $req_no_cond="";
		if($style_ref!="") $style_cond="and a.style_ref_no in('$style_ref')";else $style_cond="";
		if($txt_booking_no!="") $book_no_cond="and d.booking_no like '%$txt_booking_no%'";else $book_no_cond="";
	}
	if(str_replace("'","",$cbo_company_name)==0) $company_name=""; else $company_name=" and a.company_id=$cbo_company_name";
	if(str_replace("'","",$cbo_buyer_name)==0) $buyer_name="";else $buyer_name=" and a.buyer_name=$cbo_buyer_name";
	if(str_replace("'","",trim($txt_date_from))=="" || str_replace("'","",trim($txt_date_to))=="")
		$date_cond="";
	else
		$date_cond=" and c.delivery_date between $txt_date_from and $txt_date_to";

	if(str_replace("'","",$txt_req_no)=="") $req_no=""; else $req_no=" and a.requisition_number_prefix_num like '%$req_no%' ";
	
	if(str_replace("'","",$cbo_sample_stage)==0) $sample_stage_cond="";else $sample_stage_cond=" and a.sample_stage_id in($cbo_sample_stage)";
	if($cbo_sample_name==0) $sample_cond="";else $sample_cond=" and c.sample_name in($cbo_sample_name)";

	$fromDate = change_date_format( str_replace("'","",trim($txt_date_from)) );
	$toDate = change_date_format( str_replace("'","",trim($txt_date_to)) );
	//$style=str_replace("'", "", $txt_style_ref);
	//if($style=='') $style_ref="";else $style_ref=" and a.style_ref_no like '%$style%'";

	/*$booking_without_order_sql=sql_select("SELECT b.style_id,a.booking_no
		from wo_non_ord_samp_booking_mst a,wo_non_ord_samp_booking_dtls b
		where a.booking_no=b.booking_no and a.status_active=1 and b.status_active=1  group by  b.style_id,a.booking_no");
	foreach($booking_without_order_sql as $vals)
	{
		$booking_without_order_arr[$vals[csf("style_id")]]=$vals[csf("booking_no")];
	}*/
	
	ob_start();
	$company_library=return_library_array( "select id,company_name from lib_company", "id", "company_name"  );
	?>
	<script type="text/javascript">setFilterGrid('table_body',-1);</script>
	<div>
        <table cellpadding="0" cellspacing="0" width="1850">
            <tr  class="form_caption" style="border:none;">
           		 <td align="center" width="100%" colspan="18" style="font-size:20px"><strong><? echo str_replace("'","",$report_title); ?></strong></td>
            </tr>
            <tr  class="form_caption" style="border:none;">
                <td colspan="18" align="center" style="border:none; font-size:14px;">
                <b><? echo $company_library[$cbo_company_name]; ?></b>
                </td>
            </tr>
            <tr  class="form_caption" style="border:none;">
                <td align="center" width="100%" colspan="18" style="font-size:12px">
                <? if(str_replace("'","",$fromDate)!="" && str_replace("'","",$toDate)!="") echo "From ".change_date_format(str_replace("'","",$fromDate),'dd-mm-yyyy')." To ".change_date_format(str_replace("'","",$toDate),'dd-mm-yyyy')."" ;?>
                </td>
            </tr>
        </table>
        <?
         
		   	  $sql_book="select a.id as req_id, a.dealing_marchant, a.requisition_number_prefix_num as req_no,a.requisition_number,to_char(a.insert_date,'YYYY') as year,
					a.buyer_name, a.style_ref_no, a.season, b.delivery_to,c.remarks,c.sample_name,c.delivery_date,c.gmts_item_id as item_id,d.booking_no,f.smv,f.sample_color
					from sample_development_mst a,sample_ex_factory_mst b,sample_ex_factory_dtls c,wo_non_ord_samp_booking_dtls d,sample_development_dtls f
					where  c.sample_development_id=a.id  and b.id=c.sample_ex_factory_mst_id  and d.style_id=a.id and c.sample_development_id=d.style_id and f.sample_mst_id=a.id and f.sample_mst_id=c.sample_development_id and d.gmts_color=f.sample_color and c.sample_name=c.sample_name and a.entry_form_id in(203) and b.entry_form_id=132 
					and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 and c.is_deleted=0 and c.status_active=1  $company_name $date_cond $buyer_name $sample_stage_cond  $req_no_cond $req_id_cond  $book_no_cond $style_cond $sample_cond order by a.id"; 
					
			$sql_book_result=sql_select($sql_book);
			foreach($sql_book_result as $row)
			{
				//$sample_delivery_booking_arr[$row[csf('req_id')]][$row[csf('sample_name')]][$row[csf('gmts_item_id')]][$row[csf('sample_color')]]['booking_no']= $row[csf('booking_no')];
				if($row[csf('smv')]>0)
				{
				$sample_delivery_booking_arr[$row[csf('req_id')]][$row[csf('sample_name')]][$row[csf('item_id')]][$row[csf('sample_color')]]['smv']= $row[csf('smv')];
				}
				$sample_req_id_booking_arr[$row[csf('req_id')]]= $row[csf('req_id')];
				if($row[csf('booking_no')]!='')
				{
				$sample_req_booking_arr[$row[csf('req_id')]]= $row[csf('booking_no')];
				}
			}
				$bookreqIds = implode(",", array_unique($sample_req_id_booking_arr));
				
				$ReqIds=chop($bookreqIds,','); //$po_cond_for_in=""; $order_cond1=""; $order_cond2=""; $precost_po_cond="";
				$Req_ids=count(array_unique(explode(",",$bookreqIds)));//
					if($db_type==2 && $Req_ids>1000)
					{
						$req_cond_for_in=" and (";
						$req_cond_for_in2=" and (";
						$req_cond_for_in3=" and (";
						$reqIdsArr=array_chunk(explode(",",$ReqIds),999);
						foreach($reqIdsArr as $ids)
						{
							$ids=implode(",",$ids);
							$req_cond_for_in.=" a.id in($ids) or"; 
							$req_cond_for_in2.=" a.sample_development_id in($ids) or"; 
							$req_cond_for_in3.=" a.requisition_id in($ids) or"; 
						}
						$req_cond_for_in=chop($req_cond_for_in,'or ');
						$req_cond_for_in.=")";
						$req_cond_for_in2=chop($req_cond_for_in2,'or ');
						$req_cond_for_in2.=")";
						$req_cond_for_in3=chop($req_cond_for_in3,'or ');
						$req_cond_for_in3.=")";
					}
					else
					{
						$req_cond_for_in=" and a.id in($ReqIds)";
						$req_cond_for_in2=" and a.sample_development_id in($ReqIds)";
						$req_cond_for_in3=" and a.requisition_id in($ReqIds)";
						//$req_cond_for_in=" and a.id  in($ReqIds)";
					}
						
						
					  
		  
		    $sql_sample="select a.id as req_id, a.dealing_marchant, a.requisition_number_prefix_num as req_no,a.requisition_number,to_char(a.insert_date,'YYYY') as year,
					a.buyer_name, a.style_ref_no, a.season, b.id as delivery_id,b.delivery_to,c.remarks,c.sample_name,c.delivery_date,c.gmts_item_id as item_id,e.size_pass_qty,e.color_id as color_id,e.size_id
					from sample_development_mst a,sample_ex_factory_mst b,sample_ex_factory_dtls c,sample_ex_factory_colorsize e
					where  c.sample_development_id=a.id  and b.id=c.sample_ex_factory_mst_id and c.id=e.sample_ex_factory_dtls_id and  a.entry_form_id in(203) and b.entry_form_id=132 
					and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 and c.is_deleted=0 and c.status_active=1 and e.is_deleted=0 and e.status_active=1 $company_name $date_cond $buyer_name $sample_stage_cond  $req_no_cond $req_id_cond $req_cond_for_in $style_cond $sample_cond  order by a.id";
				$sql_sample_result=sql_select($sql_sample);
				$sample_name_ids=array(); $sample_item_ids=array(); $sample_req_id=array(); 
				$sample_color_ids=array();
				foreach($sql_sample_result as $row)
				{
				$booking_no=$sample_req_booking_arr[$row[csf('req_id')]];
				$sample_smv=$sample_delivery_booking_arr[$row[csf('req_id')]][$row[csf('sample_name')]][$row[csf('item_id')]][$row[csf('color_id')]]['smv'];
				//echo $sample_smv.', ';
				$sample_delivery_arr[$row[csf('req_id')]][$row[csf('sample_name')]][$row[csf('item_id')]][$row[csf('color_id')]]['req_no']= $row[csf('requisition_number')];
				$sample_delivery_arr[$row[csf('req_id')]][$row[csf('sample_name')]][$row[csf('item_id')]][$row[csf('color_id')]]['style_ref_no']= $row[csf('style_ref_no')];
				$sample_delivery_arr[$row[csf('req_id')]][$row[csf('sample_name')]][$row[csf('item_id')]][$row[csf('color_id')]]['buyer_name']= $row[csf('buyer_name')];
				$sample_delivery_arr[$row[csf('req_id')]][$row[csf('sample_name')]][$row[csf('item_id')]][$row[csf('color_id')]]['season']= $row[csf('season')];
				$sample_delivery_arr[$row[csf('req_id')]][$row[csf('sample_name')]][$row[csf('item_id')]][$row[csf('color_id')]]['delivery_date'].=$row[csf('delivery_date')].',';
				$sample_delivery_arr[$row[csf('req_id')]][$row[csf('sample_name')]][$row[csf('item_id')]][$row[csf('color_id')]]['remarks'].=$row[csf('remarks')].',';
				$sample_delivery_arr[$row[csf('req_id')]][$row[csf('sample_name')]][$row[csf('item_id')]][$row[csf('color_id')]]['delivery_id'].=$row[csf('delivery_id')].',';
				$sample_delivery_arr[$row[csf('req_id')]][$row[csf('sample_name')]][$row[csf('item_id')]][$row[csf('color_id')]]['size_name'].=$size_arr[$row[csf('size_id')]].',';
				$sample_delivery_arr[$row[csf('req_id')]][$row[csf('sample_name')]][$row[csf('item_id')]][$row[csf('color_id')]]['booking_no']=$booking_no;
				$sample_delivery_arr[$row[csf('req_id')]][$row[csf('sample_name')]][$row[csf('item_id')]][$row[csf('color_id')]]['sample_smv']=$sample_smv;
				
				$sample_delivery_to_arr[$row[csf('delivery_to')]]= $row[csf('delivery_to')];
				
				$sample_delivery_distribute_arr[$row[csf('req_id')]][$row[csf('sample_name')]][$row[csf('item_id')]][$row[csf('color_id')]][$row[csf('delivery_to')]]['size_pass_qty']+=$row[csf('size_pass_qty')];
				
				$sample_delivery_to_distribute_arr[$row[csf('delivery_to')]]['size_pass_qty']+=$row[csf('size_pass_qty')];
				$sample_req_id_arr[$row[csf('req_id')]]= $row[csf('req_id')];
				$sample_req_id[]= $row[csf('req_id')];
				$sample_name_ids[]= $row[csf('sample_name')];
				$sample_item_ids[]= $row[csf('item_id')];
				$sample_color_ids[]= $row[csf('color_id')];
				}
				
				$sample_names=where_con_using_array($sample_name_ids,1,'b.sample_name');
				$sample_req_ids=where_con_using_array($sample_req_id,1,'a.sample_development_id');
				$sample_items=where_con_using_array($sample_item_ids,1,'b.item_number_id');
				$color_ids=where_con_using_array($sample_color_ids,1,'c.color_id');


				$sewing_out_sql="SELECT  a.sample_development_id, b.sample_name, b.item_number_id, b.qc_pass_qty as qc_pass_qty, c.color_id, c.size_id from sample_sewing_output_mst a, sample_sewing_output_dtls b, sample_sewing_output_colorsize c where a.id=b.sample_sewing_output_mst_id and a.id=c.sample_sewing_output_mst_id and b.id=c.sample_sewing_output_dtls_id and b.entry_form_id in (130) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $sample_names $sample_req_ids $sample_items $color_ids ";//$sample_items
				//echo $sewing_out_sql; die;
				$sewing_result=sql_select($sewing_out_sql);
				$sample_data_arr=array();
				foreach ($sewing_result as $row) {
					$sample_data_arr[$row[csf("sample_development_id")]][$row[csf('sample_name')]][$row[csf('item_number_id')]][$row[csf('color_id')]]+= $row[csf('qc_pass_qty')];
					
				}

				/*echo "<pre>";
				print_r($sample_data_arr);
				echo "</pre>";

				echo "<pre>";
				print_r($sample_delivery_arr);
				echo "</pre>";*/

				//$req_id_cond = str_replace("a.sample_development_id", "a.requisition_id", $req_cond);
				//$cm_po_cond2 = str_replace("id", "b.po_id", $po_cond);
				if($ReqIds!="")
				{
					 $sql_sew="select a.sample_development_id as req_id,b.sample_name,b.item_number_id as item_id, max(b.sewing_date) as max_sewing_date,c.color_id from sample_sewing_output_mst a,sample_sewing_output_dtls b,sample_sewing_output_colorsize c where a.id=b.sample_sewing_output_mst_id and b.id=c.sample_sewing_output_dtls_id and a.status_active=1 and b.status_active=1 and c.status_active=1 $req_cond_for_in2 group by a.sample_development_id,b.sample_name, b.sample_name,b.item_number_id,c.color_id";
					$sql_sew_result=sql_select($sql_sew);
					foreach($sql_sew_result as $row)
					{
						$sew_out_arr[$row[csf('req_id')]][$row[csf('sample_name')]][$row[csf('item_id')]][$row[csf('color_id')]]=$row[csf('max_sewing_date')];
					}
					$sql_appro="select a.requisition_id as req_id,a.color_number_id as color_id,a.gmts_item_id as item_id, a.sample_comments,a.approval_status,a.approval_status_date from wo_po_sample_approval_info a where a.status_active=1 $req_cond_for_in3 ";
					$sql_appro_result=sql_select($sql_appro);
					foreach($sql_appro_result as $row)
					{
						$sample_approve_arr[$row[csf('req_id')]][$row[csf('item_id')]][$row[csf('color_id')]]['approval_status_date']=$row[csf('approval_status_date')];
						$sample_approve_arr[$row[csf('req_id')]][$row[csf('item_id')]][$row[csf('color_id')]]['approval_status']=$row[csf('approval_status')];
						$sample_approve_arr[$row[csf('req_id')]][$row[csf('item_id')]][$row[csf('color_id')]]['sample_comments']=$row[csf('sample_comments')];
					}
				}
				 
				 //$sample_sent_to_list
				//print_r($sew_out_arr);
				
				$tot_row=count($sample_delivery_to_arr);
				$width_size=1750+80*$tot_row;
		?>
		<table cellspacing="0" cellpadding="0" border="1" class="rpt_table" width="<? echo $width_size;?>" rules="all" id="table_header" >
			<thead>
				<tr>
                    <th  width="" colspan="17" >&nbsp;</th>
					<th   align="center"  colspan="<? echo $tot_row;?>">
					 Total Sample Quantity & Distribution 
					</th>
                   <th width="100">&nbsp;</th>
                   <th width="100">&nbsp;</th>
                   <th>&nbsp;</th>
                </tr>
                 <tr>
                    <th  width="" colspan="17" >&nbsp;</th>
                     <?
					foreach($sample_delivery_to_arr as $sent_to=>$sent_to_val)
					{
					?>
					<th   align="center" colspan="<? echo count($sent_to_val);?>">
					<? 
					$tot_summary_sent_to=$sample_delivery_to_distribute_arr[$sent_to]['size_pass_qty'];
					echo $tot_summary_sent_to;
					?>
					</th>
					<?
					}
            ?>
                   <th width="100">&nbsp;</th>
                   <th width="100">&nbsp;</th>
                   <th>&nbsp;</th>
                </tr>
                <tr>
					<th width="30">Sl No</th>
					<th width="110">Buyer Name</th>
					<th width="110">Requisition No.</th>
					<th width="100">Style Name</th>
					<th width="60">Season</th>
					<th width="100">Booking No</th>
                    <th width="80">Sample Name</th>
					<th width="110">Garments Item</th>
					<th width="70">SMV</th>
                    
					<th width="100">Colour</th>
					<th width="80">Size</th>
					<th width="80">Spl Sweing Date</th>
					<th width="80">Last Delivery Date</th>
					<th width="80">Approval Status</th>
					<th width="80">Status Date</th>
					<th width="80">Comments</th>
					<th width="100">Total Sewing Out</th>
                    <?
                    foreach($sample_delivery_to_arr as $sent_to=>$val)
					{ ?>
					<th width="80"><? if($sent_to==0) echo "Select All"; else echo $sample_sent_to_list[$sent_to];?></th>
                   <? } ?>
					<th width="100">Total Delivery</th>
                   	<th width="100">Balance</th>
					<th>Remaks</th>
					
				</tr>
			</thead>
		</table>
		<div style="max-height:380px; overflow-y:scroll; width:<? echo $width_size+20;?>px;" id="scroll_body">
			<table cellspacing="0" cellpadding="0" border="1" class="rpt_table"  width="<? echo $width_size;?>" rules="all" id="table_body">
				<tbody>
					<?
					
					$k=0; 
					$books_ar=array();
					$j=1;
					$book_rowspan="";
					$receive_qty=0;
					$grand_total_size_qty=0;
					$total_balance=0;
					foreach ($sample_delivery_arr as $req_id => $req_data)
					{
						foreach ($req_data as $sample_name_id => $sample_data)
					    {
						   foreach ($sample_data as $item_id => $item_data)
					       {
						    foreach ($item_data as $color_id => $row)
					        {

					   	 $sew_out_max_date=$sew_out_arr[$req_id][$sample_name_id][$item_id][$color_id];
						if ($k%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
						$booking_no = $row[csf("booking_no")];	
						$delivery_date = rtrim($row[("delivery_date")],',');
						$delivery_dateArr=explode(",",$delivery_date);
						$delivery_date_max=max($delivery_dateArr);
						$approval_status_id=$sample_approve_arr[$req_id][$item_id][$color_id]['approval_status'];
						$approval_status_date=$sample_approve_arr[$req_id][$item_id][$color_id]['approval_status_date'];
						$sample_comments=$sample_approve_arr[$req_id][$item_id][$color_id]['sample_comments'];
						
					
						
						?>
						<tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_2nd<? echo $k; ?>','<? echo $bgcolor; ?>')" id="tr_2nd<? echo $k; ?>">
							<? 
									?>
									<td width="30"  align="center" ><? echo $k; ?></td>
									<td width="110" style="word-break:break-all" ><? echo $buyer_arr[$row[('buyer_name')]]; ?></td>
									<td width="110" style="word-break:break-all"><? echo $row[('req_no')]; ?></td>
									<td width="100" style="word-break:break-all" ><? echo  $row[('style_ref_no')] ; ?></td>
									<td width="60" align="center"><? echo  $season_arr[$row[('season')]]; ?></td>
									<td width="100" style="word-break:break-all">
										<? echo $row[('booking_no')]; ?>
									</td>
                                    <td width="80" style="word-break:break-all">
										<? echo $sample_name_arr[$sample_name_id]; ?>
									</td>
									<td width="110" style="word-break:break-all" ><? echo $garments_item[$item_id]; ?></td>
									<td width="70" style="word-break:break-all" ><? echo $row[('sample_smv')] ; ?></td>
									<?
								//$color_date_index=$value[csf('delivery_date')]."_".$value[csf('sample_color')];
								 $delivery_id=rtrim($row[('delivery_id')],',');
								 $delivery_ids=implode(",",array_unique(explode(",",$delivery_id)));
								?>
								<td width="100" style="word-break:break-all"><?  //
									echo  $color_arr[$color_id] ; ?></td>
								<td width="80" style="word-break:break-all" align="center"> <? echo rtrim($row[('size_name')],',');?></td>
                               
								<td width="80" style="word-break:break-all"><? echo  change_date_format($sew_out_max_date); ?></td>
								<td width="80" style="word-break:break-all"  title="<? echo $req_id.'_'.$sample_name_id.'_'.$item_id.'_'.$color_id;?>" align="center" ><a href="javascript:open_delivery_popup('<? echo $req_id.'_'.$sample_name_id.'_'.$item_id.'_'.$color_id.'_'.$delivery_ids;?>','Delivery Info','delivery_date_popup',1)"><? echo change_date_format($delivery_date_max); ?></a></td>
								<td width="80" style="word-break:break-all" align="center"><? echo $approval_status[$approval_status_id]; ?></td>
								<td width="80" style="word-break:break-all" align="center"><? echo change_date_format($approval_status_date); ?></td>
								<td width="80" style="word-break:break-all" align="center"><? echo $sample_comments;?> </td>
								<td width="100" style="word-break:break-all" align="center"><?
								$sewing_output=$sample_data_arr[$req_id][$sample_name_id][$item_id][$color_id];
								 echo $sewing_output; ?> </td>
                                  <?
                                  $total_size_qty=0;
								foreach($sample_delivery_to_arr as $sent_to=>$val)
								{ 
								$size_pass_qty=$sample_delivery_distribute_arr[$req_id][$sample_name_id][$item_id][$color_id][$sent_to]['size_pass_qty'];
								
								$size_pass_qty_arr[$sent_to]+=$size_pass_qty;
								?>
								<td width="80" align="right"><? echo number_format($size_pass_qty,0);?></td>
                                <?
                                $total_size_qty+=$size_pass_qty;
								}
								$remarks=implode(",",array_unique(explode(",",$row[('remarks')])));
								?>
								
							<td width="100" align="right" style="word-break:break-all"><? 
							$grand_total_size_qty+=$total_size_qty;
							 echo number_format($total_size_qty,0); ?></td>
							<td width="100" align="right" style="word-break:break-all"><?
							$balance=$sewing_output-$total_size_qty;
							$total_balance+=$balance;
							 echo number_format($balance,0); ?></td>
							<td width="" style="word-break:break-all"><? echo $remarks; ?></td>
							</tr>
							<?
							$k++;
						    }
						   }
						  }
						}
						?>
					</tbody>
				</table>
                 <table width="<? echo $width_size;?>" border="1" cellpadding="0" cellspacing="0" rules="all"> 
                 <tr class="tbl_bottom">
					<td width="30">&nbsp;</td>
					<td width="110">&nbsp;</td>
					<td width="110">&nbsp;</td>
					<td width="100">&nbsp;</td>
					<td width="60">&nbsp;</td>
					<td width="100">&nbsp;</td>
                    
                    <td width="80">&nbsp;</td>
					<td width="110">&nbsp;</td>
					<td width="70">&nbsp;</td>
					<td width="100">&nbsp;</td>
                    
					<td width="80">&nbsp;</td>
					<td width="80">&nbsp;</td>
					<td width="80"></td>
					<td width="80" align="right"><? //echo $receive_qty ; ?></td>
					<td width="80">&nbsp;</td>
					<td width="80">&nbsp;</td>
                    <td width="100">Total</td>
                       <?
					foreach($sample_delivery_to_arr as $sent_to=>$val)
					{ 
					?>
					<td width="80" align="right"><? echo number_format($size_pass_qty_arr[$sent_to],0);?></td>
                    <?
					}
					?>
                    
					
                    
					
					<td width="100" align="right"><? echo number_format($grand_total_size_qty,0);?></td>
					<td width="100" align="right"><? echo number_format($total_balance,0);?></td>
					<td>&nbsp;</td>
                    </tr>
				</table>
			</div>
		</div>
		<?
	foreach (glob("$user_name*.xls") as $filename) {
	if (@filemtime($filename) < (time() - $seconds_old))
		@unlink($filename);
	}
	//---------end------------//
	$name = time();
	$filename = $user_name . "_" . $name . ".xls";
	$create_new_doc = fopen($filename, 'w');
	$is_created = fwrite($create_new_doc, ob_get_contents());
	//$filename=$user_id."_".$name.".xls";
	echo "$total_data####$filename";
	}
if($action=="delivery_date_popup")
{
 	echo load_html_head_contents("Delivery Details", "../../../", 1, 1,$unicode,'','');
	extract($_REQUEST);
	/*echo $from.'_'.$to;//$job_no;
	die;*/
	$data=explode("_",$req_data);
	$req_id=$data[0];
	$sample_id=$data[1];
	$item_id=$data[2];
	$color_id=$data[3];
	$delivery_id=$data[4];
	?>
	<div style="width:100%" align="center">
		<fieldset style="width:530px">
            <div style="width:100%">
            <?
     $sql_sample="select a.id as req_id,b.sys_number, a.requisition_number_prefix_num as req_no,a.requisition_number,to_char(a.insert_date,'YYYY') as year,
					a.buyer_name, a.style_ref_no, a.season, b.delivery_to,c.remarks,c.sample_name,c.delivery_date,c.gmts_item_id as item_id,e.size_pass_qty,e.color_id as color_id,e.size_id
					from sample_development_mst a,sample_ex_factory_mst b,sample_ex_factory_dtls c,sample_ex_factory_colorsize e
					where  c.sample_development_id=a.id  and b.id=c.sample_ex_factory_mst_id and c.id=e.sample_ex_factory_dtls_id and  a.entry_form_id in(203) and b.entry_form_id=132 
					and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 and c.is_deleted=0 and c.status_active=1 and e.is_deleted=0 and e.status_active=1 and b.id in($delivery_id) and a.id=$req_id  and c.sample_name=$sample_id  and e.color_id=$color_id  order by a.id";
				$sql_sample_result=sql_select($sql_sample);
				foreach($sql_sample_result as $row)
				{
				$booking_no=$sample_req_booking_arr[$row[csf('req_id')]];
				$sample_delivery_arr[$row[csf('sys_number')]]['req_no']= $row[csf('requisition_number')];
				$sample_delivery_arr[$row[csf('sys_number')]]['deliver_no']= $row[csf('sys_number')];
				$sample_delivery_arr[$row[csf('sys_number')]]['delivery_date']= $row[csf('delivery_date')];
				$sample_delivery_arr[$row[csf('sys_number')]]['delivery_to']= $row[csf('delivery_to')];
				
				$sample_delivery_to_distribute_arr[$row[csf('sys_number')]][$row[csf('delivery_to')]]['size_pass_qty']+=$row[csf('size_pass_qty')];
				$sample_delivery_to_arr[$row[csf('delivery_to')]]= $row[csf('delivery_to')];
				}
				ksort($sample_delivery_to_arr);
	
	
			?>
          <table cellpadding="0" width="100%" class="rpt_table" rules="all" border="1">
            <caption> Sample Delivery Details</caption>
                <thead>
                    <tr>
                        <th width="20">SL</th>
                        <th width="100">Delivery ID</th>
                        <th width="100">Delivery Date</th>
                        <th width="100">Req. No</th>
                        <?
                        foreach($sample_delivery_to_arr as $sent_to=>$row)
						{
						?>
                         <th width="100"><? if($sent_to==0) echo "Select All"; else echo $sample_sent_to_list[$sent_to]; ?></th>
                         <?
						}
						 ?>
                         
                     </tr>
                </thead>
            </table>
        </div>
        <div style="width:100%; max-height:400px;">
            <table cellpadding="0" width="100%" cellspacing="0" border="1" rules="all" class="rpt_table">
                <?
                $i=1;
				$tot_grey_main=0;
                foreach($sample_delivery_arr as $delivery_no=>$row)
                {
                    if ($i%2==0) $bgcolor="#EFEFEF"; else $bgcolor="#FFFFFF";
                    ?>
                    <tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('trm_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="trm_<? echo $i; ?>">
                        <td width="20"><? echo $i; ?></td>
                        <td width="100"><? echo $delivery_no; ?></td>
                        <td width="100" align="right"><? echo change_date_format($row[("delivery_date")]); ?></td>
                        <td width="100" align="right"><? echo $row[("req_no")]; ?></td>
                          <?
                        foreach($sample_delivery_to_arr as $sent_to=>$row)
						{
							$delivery_pass_qty=$sample_delivery_to_distribute_arr[$delivery_no][$sent_to]['size_pass_qty'];
							$delivery_pass_qty_arr[$sent_to]+=$delivery_pass_qty;
						?>
                        <td width="100" align="right"><? echo number_format($delivery_pass_qty,0); ?></td>
                        <? 
						}
						?>
                    </tr>
                    <?
                   // $tot_grey_main+=$row[("main")];
					
                    $i++;
                }
                ?>
                <tfoot>
                <tr>
                    <th colspan="4">Total</th>
                   
				   <?
                    foreach($sample_delivery_to_arr as $sent_to=>$row)
                    {
						
                    ?>
                    <th align="right"><? echo $delivery_pass_qty_arr[$sent_to]; ?></th>
                    <?
					}
					?>
                </tr>
                
                </tfoot>
            </table>
            
        </div>
        
		</fieldset>
	</div>
	<?
    exit();
}
	?>