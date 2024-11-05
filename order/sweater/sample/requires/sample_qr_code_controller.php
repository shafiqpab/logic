<?
header('Content-type:text/html; charset=utf-8');
session_start();
include('../../../../includes/common.php');

$user_id = $_SESSION['logic_erp']["user_id"];
if( $_SESSION['logic_erp']['user_id'] == "" ) { header("location:login.php"); die; }
$permission=$_SESSION['page_permission'];
$data=$_REQUEST['data'];
$action=$_REQUEST['action'];

if ($action == "load_drop_down_knitting_com") {
	$data = explode("_", $data);
	$company_id = $data[1];
	//$company_id
	if ($data[0] == 1) { 
		echo create_drop_down("cbo_working_company_name", 142, "select comp.id, comp.company_name from lib_company comp where comp.status_active=1 and comp.is_deleted=0  order by comp.company_name", "id,company_name", 1, "--Select Knit Company--", $company_id, "load_location();", "");
	} else if ($data[0] == 3) {
		echo create_drop_down("cbo_working_company_name", 142, "select a.id,a.supplier_name from lib_supplier a, lib_supplier_party_type b where a.id=b.supplier_id and b.party_type=20 and a.status_active=1 group by a.id,a.supplier_name order by a.supplier_name", "id,supplier_name", 1, "--Select Knit Company--", 0, "load_location();");
	} else {
		echo create_drop_down("cbo_working_company_name", 142, $blank_array, "", 1, "--Select Knit Company--", 0, "load_location();");
	}
	exit();
}

 //--------------------------------------------------------------------------------------------
if ($action=="load_drop_down_wo_location")
{
	echo create_drop_down( "cbo_wo_location_name", 130, "select id,location_name from lib_location where status_active =1 and is_deleted=0 and company_id='$data' order by location_name","id,location_name", 1, "-- Select Location --", $selected, "" );     	 
	exit();
}

if ($action=="load_drop_down_location")
{
	echo create_drop_down( "cbo_location_name", 130, "select id,location_name from lib_location where status_active =1 and is_deleted=0 and company_id='$data' order by location_name","id,location_name", 1, "-- Select Location --", $selected, "" );     	 
	exit();
}

if ($action=="load_drop_down_floor")
{
	echo create_drop_down( "cbo_floor", 130, "select id,floor_name from lib_prod_floor where production_process=2 and status_active =1 and is_deleted=0 and location_id='$data' order by floor_name","id,floor_name", 1, "-- Select Floor --", $selected, "",0 );     	 
}

if ($action=="load_drop_down_buyer_req")
{
	echo create_drop_down( "cbo_buyer_name", 157, "select buy.id,buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company='$data' $buyer_cond  and buy.id in (select  buyer_id from  lib_buyer_party_type where party_type in (1,3,21,30,90)) order by buyer_name","id,buyer_name", 1, "-- Select Buyer --", $selected, "load_drop_down( 'sample_qr_code_controller', this.value, 'load_drop_down_season', 'season_td'); load_drop_down( 'sample_qr_code_controller', this.value+'*'+1, 'load_drop_down_brand', 'brand_td');" );
	exit();
}

if ($action=="load_drop_down_brand")
{
	$data_arr = explode("*", $data);
	if($data_arr[1] == 1) $width=90; else $width=150;
	echo create_drop_down( "cbo_brand_id", $width, "select id, brand_name from lib_buyer_brand brand where buyer_id='$data_arr[0]' and status_active =1 and is_deleted=0 $brand_cond order by brand_name ASC","id,brand_name", 1, "--Brand--", $selected, "" );
	exit();
}


if ($action=="load_drop_down_season")
{
	echo create_drop_down( "cbo_season_id", 90, "select id, season_name from lib_buyer_season where buyer_id='$data' and status_active =1 and is_deleted=0 order by season_name ASC","id,season_name", 1, "-- Select Season--", "", "" );
	exit();
}

if($action=="requisition_id_popup")
{
	extract($_REQUEST);
	echo load_html_head_contents("Sweater Sample Requisition Info","../../../../", 1, 1, $unicode);
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
			document.getElementById('selected_job').value=mst_id;
			parent.emailwindow.hide();
		}
		
	function show_system_id(){
		
		if(document.getElementById('txt_requisition_num').value=='' && document.getElementById('txt_style_id').value==''  &&  document.getElementById('txt_style_name1').value==''  &&  document.getElementById('cbo_sample_stage').value==0 && ( document.getElementById('txt_date_from').value=='' || document.getElementById('txt_date_to').value=='')){
			var fillData="cbo_company_mst*txt_date_from*txt_date_to";
			var fillMessage=" Company Name*Est. Ship From Date*Est. Ship To Date";
		}
		else
		{
			var fillData="cbo_company_mst";
			var fillMessage="Company Name Stage";
		}
		
		if (form_validation(fillData,fillMessage)==false)
		{
			return;
		}
		else{
			show_list_view ( document.getElementById('cbo_search_category').value+'_'+document.getElementById('txt_style_id').value+'_'+document.getElementById('cbo_company_mst').value+'_'+document.getElementById('cbo_buyer_name').value+'_'+document.getElementById('txt_date_from').value+'_'+document.getElementById('txt_date_to').value+'_'+document.getElementById('txt_style_name1').value+'_'+document.getElementById('txt_requisition_num').value+'_'+document.getElementById('cbo_sample_stage').value+'_'+document.getElementById('cbo_brand_id').value+'_'+document.getElementById('cbo_season_year').value+'_'+document.getElementById('cbo_season_id').value, 'create_requisition_id_search_list_view', 'search_div', 'sample_qr_code_controller', 'setFilterGrid(\'list_view\',-1)')
		}
	}
		
		
    </script> 
 </head>
 <body>
	<div align="center" style="width:1300px;" >
	<form name="searchsampledevelopmentfrm_1"  id="searchsampledevelopmentfrm_1" autocomplete="off">
		<table width="1300" cellspacing="0" cellpadding="0" align="center">
    		<tr>
        		<td align="center" width="100%">
            		<table cellspacing="0" cellpadding="0" border="1" rules="all" class="rpt_table" align="center">
                        <thead>
                        	<th  colspan="11">
                              <? echo create_drop_down( "cbo_search_category", 140, $string_search_type,'', 1, "-- Search Catagory --" ); ?>
                            </th>
                        </thead>
                        <thead>
                        	<th class="must_entry_caption" width="140">Company Name</th>
                            <th width="157">Buyer Name</th>
                            <th width="90">Brand</th>
                            <th width="70">Requisition No</th>
                            <th width="100">Style ID</th>
                            <th width="90">Season Year</th>
                            <th width="90">Season</th>
                            <th  width="120" >Style Name</th>
                            <th class="must_entry_caption" width="90">Sample Stage</th>
                            <th width="160">Requisition Date</th>
                            <th width="80"><input type="reset" name="reset" id="reset" class="formbutton" value="Reset" style="width:100px;" /></th>
                        </thead>
        				<tr>
                        	<td width="140">
                            	<input type="hidden" id="selected_job">
								<?
                                    echo create_drop_down( "cbo_company_mst", 140, "select id,company_name from lib_company comp where status_active =1 and is_deleted=0 and core_business not in(3) $company_cond order by company_name","id,company_name",1, "-- Select Company --", $company_id,"load_drop_down( 'sample_qr_code_controller', this.value, 'load_drop_down_buyer_req', 'buyer_td_req' );" );
                                ?>
                    		</td>
                   			<td id="buyer_td_req" width="157">
								 <?
                                    echo create_drop_down( "cbo_buyer_name", 157, $blank_array,'', 1, "-- Select Buyer --" );
                                ?>
                            </td>
                            <td id="brand_td"><? echo create_drop_down( "cbo_brand_id", 90, $blank_array,'', 1, "Brand",$selected, "" ); ?>
                            <td width="70">
								<input type="text" style="width:70px" class="text_boxes" name="txt_requisition_num" id="txt_requisition_num"  />
                            </td>

                            <td width="100">
								<input type="text" style="width:100px" class="text_boxes"  name="txt_style_id" id="txt_style_id"  />
                            </td>
        					<td><? echo create_drop_down( "cbo_season_year", 90, create_year_array(),"", 1,"-Year-", 1, "",0,"" ); ?></td>
                            <td id="season_td"><? echo create_drop_down( "cbo_season_id", 90, $blank_array,'', 1, "Season",$selected, "" ); ?></td>
                            <td width="90" align="center">
                                <input type="text" style="width:90px" class="text_boxes"  name="txt_style_name1" id="txt_style_name1"  />
                            </td>

                            <td width="90" align="center">
                                <?
                    				echo create_drop_down( "cbo_sample_stage", 90, $sample_stage, "", 1, "-Select Stage-", $selected, "", "", "" );
                    			?>
                            </td>

                            <td  width="160">
                                <input name="txt_date_from" id="txt_date_from" class="datepicker" style="width:55px"> To
                                <input name="txt_date_to" id="txt_date_to" class="datepicker" style="width:55px">
                            </td>
                            <td align="center" width="80">
                                <input type="button" name="btn_show" id="btn_show" class="formbutton" value="Show" onClick="show_system_id()" style="width:80px;" />
                            </td>
        				</tr>
                        <tr>
                            <td colspan="11" align="center"><?=load_month_buttons(1); ?></td>
                        </tr>
             		</table>
          		</td>
        	</tr>
        	<tr>
            	<td align="center" valign="top" id="search_div"></td>
        	</tr>
    	</table>
    </form>
	</div>
 </body>
 <script src="../../../../includes/functions_bottom.js" type="text/javascript"></script>
 </html>
 <?
 exit();
}

if($action=="create_requisition_id_search_list_view")
{
	$data=explode('_',$data);
	
	
	if ($data[2]!=0) $company=" and company_id='$data[2]'"; else 
	{ echo "<b style='color:crimson;'> Please Select Company First.</b>"; die; }
	
	
	
	if ($data[3]!=0) $buyer=" and buyer_name='$data[3]'"; else $buyer="";//{ echo "Please Select Buyer First."; die; }
	if($data[0]==1)
		{
		   if (trim($data[1])!="") $style_id_cond=" and id='$data[1]'"; else $style_id_cond="";
		   if ($data[6]!="") $style_cond=" and style_ref_no='$data[6]'"; else $style_cond="";
		}

	if($data[0]==4 || $data[0]==0)
		{
		  if (trim($data[1])!="") $style_id_cond=" and id like '%$data[1]%' "; else $style_id_cond="";
		  if ($data[6]!="") $style_cond=" and style_ref_no like '%$data[6]%' "; else $style_cond="";
		}

	if($data[0]==2)
		{
		  if (trim($data[1])!="") $style_id_cond=" and id like '$data[1]%' "; else $style_id_cond="";
		  if ($data[6]!="") $style_cond=" and style_ref_no like '$data[6]%' "; else $style_cond="";
		}

	if($data[0]==3)
		{
		  if (trim($data[1])!="") $style_id_cond=" and id like '%$data[1]' "; else $style_id_cond="";
		  if ($data[6]!="") $style_cond=" and style_ref_no like '%$data[6]' "; else $style_cond="";
		}


	if($db_type==0)
	{
	if ($data[4]!="" &&  $data[5]!="") $estimated_shipdate  = "and REQUISITION_DATE  between '".change_date_format($data[4], "yyyy-mm-dd", "-")."' and '".change_date_format($data[5], "yyyy-mm-dd", "-")."'"; else $estimated_shipdate ="";
	}
	if($db_type==2)
	{
	if ($data[4]!="" &&  $data[5]!="") $estimated_shipdate  = "and REQUISITION_DATE  between '".change_date_format($data[4], "yyyy-mm-dd", "-",1)."' and '".change_date_format($data[5], "yyyy-mm-dd", "-",1)."'"; else $estimated_shipdate ="";
	}
	if ($data[7]!="") $requisition_num=" and requisition_number_prefix_num like '%$data[7]' "; else $requisition_num="";

	if ($data[8]!=0) $stage_id=" and sample_stage_id= '$data[8]' "; else  $stage_id="";
	if ($data[9]!=0) $brand_id=" and brand_id= '$data[9]' "; else  $brand_id="";
	if ($data[10]!=0) $season_year=" and season_year= '$data[10]' "; else  $season_year="";
	if ($data[11]!=0) $season=" and season= '$data[11]' "; else  $season="";
	//if (!$data[8] && trim($data[7])=="") {echo "<b style='color:crimson;'> Please Select Sample Stage</b>";die;}


	$buyer_arr=return_library_array( "select id, buyer_name from lib_buyer",'id','buyer_name');
	$comp=return_library_array( "select id, company_name from lib_company",'id','company_name');

	$dealing_marchant=return_library_array( "select id, team_member_name from lib_mkt_team_member_info",'id','team_member_name');
	$team_leader=return_library_array( "select id, team_leader_name from lib_marketing_team",'id','team_leader_name');
	$brand_arr=return_library_array( "select id, brand_name from lib_buyer_brand",'id','brand_name');
	$season_arr=return_library_array( "select id,season_name from lib_buyer_season  where status_active =1 and is_deleted=0 ", "id", "season_name"  );

	$arr=array (2=>$buyer_arr,3=>$brand_arr,5=>$season_arr,7=>$product_dept,8=>$dealing_marchant,9=>$sample_stage);
	$sql="";
	if($db_type==0)
	{
		$sql= "select id,requisition_number,requisition_number_prefix_num,SUBSTRING_INDEX(insert_date, '-', 1) as year,company_id,buyer_name,style_ref_no,product_dept,dealing_marchant ,sample_stage_id, season, season_year, brand_id from sample_development_mst where entry_form_id=341 and status_active=1 and is_deleted=0 $company $buyer $style_id_cond $style_cond  $estimated_shipdate $requisition_num   $stage_id $brand_id $season_year $season order by id DESC";

	}
	else if($db_type==2)
	{
		$sql= "select id,requisition_number,requisition_number_prefix_num,to_char(insert_date,'YYYY') as year,company_id,buyer_name,style_ref_no,product_dept,dealing_marchant,sample_stage_id, season, season_year, brand_id from sample_development_mst where entry_form_id=341 and  status_active=1 and is_deleted=0 $company $buyer $style_id_cond $style_cond $estimated_shipdate $requisition_num  $stage_id $brand_id $season_year $season order by id DESC";
	}

	echo  create_list_view("list_view", "Year,Requisition No,Buyer Name,Brand,Season Year,Season,Style Name,Product Department,Dealing Merchant,Sample Stage", "60,100,120,90,90,90,100,90,90,100","950","240",0, $sql , "js_set_value", "id,requisition_number", "", 1, "0,0,buyer_name,brand_id,0,season,0,product_dept,dealing_marchant,sample_stage_id", $arr , "year,requisition_number_prefix_num,buyer_name,brand_id,season_year,season,style_ref_no,product_dept,dealing_marchant,sample_stage_id", "",'','0,0,0,0,0,0,0,0,0,0') ;

	exit();
}

if ($action == "generate_bundle" )
{
	// var_dump($_REQUEST);
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process )); 

	$company_name		= str_replace( "'", "", $cbo_company_name );
	$location_name		= str_replace( "'", "", $cbo_location_name);
	$req_no				= str_replace( "'", "", $txt_req_no);
	$req_id				= str_replace( "'", "", $hidden_req_id );
	$date_from			= str_replace( "'", "", $txt_date_from );	
	$date_to			= str_replace( "'", "", $txt_date_to );
	
	$sql_cond	= "";
	$sql_cond .= ($company_name!=0) ? " and a.company_id=$company_name" : "";
	$sql_cond .= ($location_name!=0) ? " and a.location_id=$location_name" : "";
	$sql_cond .= ($req_id!="") ? " and a.id=$req_id" : "";
	// $sql_cond .= ($lot_ratio_no!="") ? " and a.cutting_no='$lot_ratio_no'" : "";
	
	// ========================================= MAIN QUERY ========================================		
	$sql="SELECT c.id AS qr_id,a.requisition_number,a.style_ref_no,b.SAMPLE_NAME,b.SAMPLE_COLOR,c.SIZE_ID,c.TOTAL_QTY
	FROM 
	SAMPLE_DEVELOPMENT_MST a, 
	SAMPLE_DEVELOPMENT_DTLS b, 
	SAMPLE_DEVELOPMENT_SIZE c
		WHERE   b.SAMPLE_MST_ID = a.id
        AND b.id = c.dtls_id
        AND a.id = c.mst_id
        AND a.status_active=1
        AND c.status_active=1
        AND b.status_active=1
        AND a.is_deleted=0
        AND b.is_deleted=0
        AND c.is_deleted=0
        $sql_cond
	ORDER BY a.id ASC";
	// echo $sql;die;	
	$result=sql_select($sql);	
	if(count($result)==0)
	{
		echo "<div style='text-align:center;color:red;font-size:20px;font-weight:bold;'>Data Not Found.</div>";
		die;
	}
	$lib_size = return_library_array("select id, size_name from  lib_size", "id", "size_name");
	$lib_color = return_library_array("select id, color_name from  lib_color", "id", "color_name");
	$dealing_marchant=return_library_array( "select id, team_member_name from lib_mkt_team_member_info",'id','team_member_name');
	$team_leader=return_library_array( "select id, team_leader_name from lib_marketing_team",'id','team_leader_name');
	$brand_arr=return_library_array( "select id, brand_name from lib_buyer_brand",'id','brand_name');
	$season_arr=return_library_array( "select id,season_name from lib_buyer_season  where status_active =1 and is_deleted=0 ", "id", "season_name"  );
	$lib_sample = return_library_array("select a.id,a.sample_name,b.sequ from lib_sample a,lib_buyer_tag_sample b where a.id=b.tag_sample and b.sequ is not null and a.status_active=1 and a.is_deleted=0  group by  a.id,a.sample_name,b.sequ order by b.sequ", "id", "sample_name");
	ob_start();	
	
	$table_width=600;
	$i=1;          
	?>
    <fieldset style="width:<? echo $table_width; ?>px;margin: 0 auto; margin-top: 10px;">
    	<center>
        	<button type="button" class="button" style="cursor: pointer;padding: 3px;background: #FFAD60;margin: 5px 0;border-radius: 4px; font-weight:bold;" onClick="fnc_bundle_report_qr_code(1);">Buyer Sticker</button>      
        	<button type="button" class="button" style="cursor: pointer;padding: 3px;background: #FFAD60;margin: 5px 0;border-radius: 4px; font-weight:bold;" onClick="fnc_bundle_report_qr_code(2);">Sticker</button>      
        </center>
        <table class="rpt_table" width="<? echo $table_width;?>" cellpadding="0" cellspacing="0" border="1" rules="all" align="center">
            <thead>      	
                <tr>
                    <th width="40">Sl</th>
                    <th width="100">Req. No</th>
                    <th width="100">Stlye</th>
                    <th width="100">Gmts Item</th>
                    <th width="100">Gmts Color</th>
                    <th width="80">Size</th>
                    <th width="50">Qty</th>
                    <th width="30"><input type="checkbox" name="check_all"  id="check_all" onClick="check_all_report()"></th>                 
                </tr>
	        </thead>
	        <tbody>
            	<?	
            	$i=1;   
            	$j = 1;        	 
            	foreach ($result as $row) 
            	{
            		$bgcolor = ($i%2==0) ? "#E9F3FF" : "#FFFFFF";
            		?>
            		<tr bgcolor="#FFAD60">
            			<td><?=$i;?></td>
            			<td align="center"><?=$row['REQUISITION_NUMBER'];?></td>
            			<td><?=$row['STYLE_REF_NO'];?></td>
            			<td><?=$lib_sample[$row['SAMPLE_NAME']];?></td>
            			<td><?=$lib_color[$row['SAMPLE_COLOR']];?></td>
            			<td><?=$lib_size[$row['SIZE_ID']];?></td>
            			<td align="right"><?=$row['TOTAL_QTY'];?></td>
            			<td align="center">
            				<input id="chk_bundle_<?=$i;?>" type="checkbox" name="chk_bundle" class="parent_bndl" onClick="select_chield()">
            				<input type="hidden" id="hiddenid_<?=$i; ?>" name="hiddenid_<?=$i; ?>" value="<?=$row['BNDL_ID']; ?>" data-sl="">
            			</td>
            		</tr>
            		<?
            		$i++;
            		$j=1;
            		for ($k=0; $k < $row['TOTAL_QTY']; $k++) 
            		{ 
            			
            			$bgcolor = ($i%2==0) ? "#E9F3FF" : "#FFFFFF";
            			?>
	            		<tr bgcolor="<?=$bgcolor;?>" id="tr_<?=$i;?>" onClick="change_color('tr_<?=$i; ?>','<?=$bgcolor; ?>')">
	            			<td><?=$i;?></td>
							<td align="center"><?=$row['REQUISITION_NUMBER'];?></td>
							<td><?=$row['STYLE_REF_NO'];?></td>
							<td><?=$lib_sample[$row['SAMPLE_NAME']];?></td>
							<td><?=$lib_color[$row['SAMPLE_COLOR']];?></td>
							<td><?=$lib_size[$row['SIZE_ID']];?></td>
							<td align="right">1</td>
	            			<td align="center"><input id="chk_bundle_<?=$i;?>" type="checkbox" name="chk_bundle" class="chield_bndl" ></td>
            				<input type="hidden" id="hiddenid_<?=$i; ?>" name="hiddenid_<?=$i; ?>" value="<?=$row['QR_ID'];  ?>" data-sl="<?=$j;?>">
	            		</tr>
	            		<?
	            		$j++;
	            		$i++;
            		}
            	}
            	?>
	        </tbody>
	    </table>
    </fieldset>
	<?

	foreach (glob("*.xls") as $filename)
	{		
		@unlink($filename);

	}
	$name=time().".xls";
	$create_new_excel = fopen($name, 'w');	
	$report_data=ob_get_contents();
	ob_clean();
	$is_created = fwrite($create_new_excel,$report_data);
	echo $report_data."####".$name;
	exit();
}

if($action=="print_qrcode_operation")
{
	// print_r($_REQUEST);echo $_POST['data'];
	// echo "string".$data;die();
	$dataEx = explode(",", $data);
	$bndle_id_arr = array(); 
	$bndle_sl_arr = array();
	foreach ($dataEx as $value) 
	{
		$value_ex = explode("_", $value);
		if($value_ex[1]!="")
		{
			$bndle_id_arr[$value_ex[0]] = $value_ex[0];
			$bndle_sl_arr[$value_ex[0]][] = $value_ex[1];
		}
	}
	$bndle_id = implode(",", $bndle_id_arr);

	// print_r($bndle_sl_arr);
	// echo "string".count($bndle_sl_arr[391834]);

	$sql="SELECT a.ID, a.REQUISITION_NUMBER, a.STYLE_REF_NO, a.TEAM_LEADER, a.SEASON, a.SEASON_YEAR, a.TEAM_LEADER, a.DEALING_MARCHANT, b.GMTS_ITEM_ID, a.PRODUCT_DEPT, b.SAMPLE_NAME, b.SAMPLE_COLOR, c.id AS DTLS_ID, c.SIZE_ID, c.TOTAL_QTY
	FROM SAMPLE_DEVELOPMENT_MST a, SAMPLE_DEVELOPMENT_DTLS b, SAMPLE_DEVELOPMENT_SIZE c
		WHERE b.SAMPLE_MST_ID = a.id AND b.id = c.dtls_id AND a.id = c.mst_id AND a.status_active=1 AND c.status_active=1 AND b.status_active=1 AND a.is_deleted=0 AND b.is_deleted=0 AND c.is_deleted=0 and c.id in($bndle_id)
	ORDER BY a.id ASC";
	$res = sql_select($sql);
	
	$buyer_short_name=return_field_value("short_name","lib_buyer", "id=$buyer_name ");
	//return_library_array( "select id,short_name from lib_buyer where id=$buyer_name ", "id", "short_name");
	$lib_size = return_library_array("select id, size_name from  lib_size", "id", "size_name");
	$lib_color = return_library_array("select id, color_name from  lib_color", "id", "color_name");
	$dealing_marchant=return_library_array( "select id, team_member_name from lib_mkt_team_member_info",'id','team_member_name');
	$team_leader=return_library_array( "select id,team_name from  lib_sample_production_team where product_category=6 and is_deleted=0",'id','team_name');
	$brand_arr=return_library_array( "select id, brand_name from lib_buyer_brand",'id','brand_name');
	$season_arr=return_library_array( "select id,season_name from lib_buyer_season  where status_active =1 and is_deleted=0 ", "id", "season_name"  );
	$lib_sample = return_library_array("select a.id,a.sample_name,b.sequ from lib_sample a,lib_buyer_tag_sample b where a.id=b.tag_sample and b.sequ is not null and a.status_active=1 and a.is_deleted=0  group by  a.id,a.sample_name,b.sequ order by b.sequ", "id", "sample_name");

	$imge_arr=return_library_array( "select master_tble_id,image_location from common_photo_library where form_name='sample_details_1' and file_type=1",'master_tble_id','image_location');

	foreach($res as $val)
	{
		$req_no = $val['REQUISITION_NUMBER'];
		$mst_id_arr[$val["ID"]]=$val["ID"];
	}
	
	$mstid_cond=where_con_using_array($mst_id_arr,0,"SAMPLE_MST_ID");
	
	$smpdtlsSql="select SAMPLE_MST_ID, FABRIC_DESCRIPTION, GAUGE, NO_OF_ENDS from SAMPLE_DEVELOPMENT_FABRIC_ACC where FORM_TYPE=1 and STATUS_ACTIVE=1 and IS_DELETED=0 $mstid_cond";
	$smpdtlsSqlRes = sql_select($smpdtlsSql); $dtlsDataArr=array();
	foreach($smpdtlsSqlRes as $drow)
	{
		$dtlsDataArr[$drow["SAMPLE_MST_ID"]]['yarncon'].=', '.$drow["FABRIC_DESCRIPTION"];
		$dtlsDataArr[$drow["SAMPLE_MST_ID"]]['gauge'].=', '.$gauge_arr[$drow["GAUGE"]];
		$dtlsDataArr[$drow["SAMPLE_MST_ID"]]['ends'].=', '.$drow["NO_OF_ENDS"];
	}
	unset($smpdtlsSqlRes);
	
	$sql="select ORDER_ID, TASK_ID, COMMENTS from TNA_PROGRESS_COMMENTS where task_type=5 ".where_con_using_array($mst_id_arr,0,'ORDER_ID')." and TASK_ID in (11,12)";
	//echo $sql; die;
	$tnaDataArray=sql_select($sql); $saveComments_arr_task_id=array();
	foreach ($tnaDataArray as $rows){
		$saveComments_arr_task_id[$rows[ORDER_ID]][$rows[TASK_ID]][$rows[COMMENTS]]=$rows[COMMENTS];	
	}
	unset($tnaDataArray);

    $PNG_TEMP_DIR = dirname(__FILE__).DIRECTORY_SEPARATOR.'qrcode_image'.DIRECTORY_SEPARATOR.$req_no.DIRECTORY_SEPARATOR;
    $PNG_WEB_DIR = 'qrcode_image/'.$req_no.'/';

    foreach (glob($PNG_WEB_DIR."*.png") as $filename) {			
		@unlink($filename);
	}

    if (!file_exists($PNG_TEMP_DIR)) mkdir($PNG_TEMP_DIR);
     
    $filename = $PNG_TEMP_DIR.'test.png';
    $errorCorrectionLevel = 'L';
    $matrixPointSize = 4;

    include "../../../../ext_resource/phpqrcode/qrlib.php"; 
	require_once("../../../../ext_resource/mpdf60/mpdf.php");

	$mpdf = new mPDF('',    // mode - default ''
					array(100,70),		// array(65,210),    // format - A4, for example, default ''
					 5,     // font size - default 0
					 '',    // default font family
					 3,    // margin_left
					 3,    // margin right
					 3,     // margin top
					 0,    // margin bottom
					 0,     // margin header
					 0,     // margin footer
					 'L');
	$i=1; 
	foreach($res as $val)
	{
		for ($k=0; $k < count($bndle_sl_arr[$val['DTLS_ID']]); $k++) 
		{ 
			$barcode_no = $val[csf("requisition_number")];
			$filename = $PNG_TEMP_DIR.'test'.md5($barcode_no).'.png';
	    	QRcode::png($barcode_no, $filename, $errorCorrectionLevel, $matrixPointSize, 2);
					
			$mpdf->AddPage('',    // mode - default ''
				array(100,70),		// array(65,210),    // format - A4, for example, default ''
				 5,     // font size - default 0
				 '',    // default font family
				 3,    // margin_left
				 3,    // margin right
				 3,     // margin top
				 0,    // margin bottom
				 0,     // margin header
				 0,     // margin footer
				 'L');

				$html.='<div style="font-size:14px; font-weight:bold;margin:0px;text-align:center;">Sonia and Sweater Ltd</div>
				<div style="font-size:12px; font-weight:bold;margin:0px;text-align:center;">Kondolbahg,Savar</div>
				<table cellpadding="0" cellspacing="0" width="100%" class="" style="font-size:10px; font-weight:bold;margin:2px;" rules="all" id="" border="1">	
					<tr>
						<td width="50%">Season</td>
						<td width="50%">'.$season_arr[$val['SEASON']].'|'.$val['SEASON_YEAR'].'</td>
					</tr>
					<tr>
						<td>Style</td>
						<td>'.$val['STYLE_REF_NO'].'</td>
					</tr>
					<tr>
						<td>Product Dept</td>
						<td>'.$product_dept[$val['PRODUCT_DEPT']].'</td>
					</tr>
					<tr>
						<td>Garment Item</td>
						<td>'.$garments_item[$val['GMTS_ITEM_ID']].'</td>
					</tr>
					<tr>
						<td>Dealing Merchant</td>
						<td>'.$dealing_marchant[$val['DEALING_MARCHANT']].'</td>
					</tr>
					<tr>
						<td>Sample Team Name</td>
						<td>'.$team_leader[$val['TEAM_LEADER']].'</td>
					</tr>
					<tr>
						<td>Sample Name</td>
						<td>'.$lib_sample[$val['SAMPLE_NAME']].'</td>
					</tr>
					<tr>
						<td>Sample Color</td>
						<td>'.$lib_color[$val['SAMPLE_COLOR']].'</td>
					</tr>
					<tr>
						<td>Sample Size</td>
						<td>'.$lib_size[$val['SIZE_ID']].'</td>
					</tr>
					
					<tr>
						<td>Fabric Composition</td>
						<td>'.implode(', ',array_filter(array_unique(explode(', ',$dtlsDataArr[$val["ID"]]['yarncon'])))).'</td>
					</tr>
					<tr>
						<td>Guage</td>
						<td>'.implode(', ',array_filter(array_unique(explode(', ',$dtlsDataArr[$val["ID"]]['gauge'])))).'</td>
					</tr>
					<tr>
						<td>Ends or Ply</td>
						<td>'.implode(', ',array_filter(array_unique(explode(', ',$dtlsDataArr[$val["ID"]]['ends'])))).'</td>
					</tr>
					<tr>
						<td>Sample Time</td>
						<td>'.implode(',',$saveComments_arr_task_id[$val["ID"]][11]).'</td>
					</tr>
					<tr>
						<td>Sample Weight</td>
						<td>'.implode(',',$saveComments_arr_task_id[$val["ID"]][12]).'</td>
					</tr>		    	
					<tr>
						<td>
							<div id="div_'.$i.'"><img src="../../../../'.$imge_arr[$val['DTLS_ID']].'" height="100" width=""></div>			
						</td>
						<td align="center">
							<div id="div_'.$i.'"><img src="'.$PNG_WEB_DIR.basename($filename).'" height="100" width=""></div>
						</td>
					</tr>

				</table>';
			$mpdf->WriteHTML($html);
			$html='';
			$i++;
		}
	} 
	
	//$mpdf->WriteHTML($html);
	foreach (glob("*.pdf") as $filename) {			
		@unlink($filename);
	}
	$name = 'req_no_' . date('j-M-Y_h-iA') .'_'.$user_id.'.pdf';
	$mpdf->Output($name, 'F');	
	echo "1###$name";
			
	exit();

}

if($action=="print_qrcode_sticker")
{
	// print_r($_REQUEST);echo $_POST['data'];
	// echo "string".$data;die();
	$dataEx = explode(",", $data);
	$bndle_id_arr = array(); 
	$bndle_sl_arr = array();
	foreach ($dataEx as $value) 
	{
		$value_ex = explode("_", $value);
		if($value_ex[1]!="")
		{
			$bndle_id_arr[$value_ex[0]] = $value_ex[0];
			$bndle_sl_arr[$value_ex[0]][] = $value_ex[1];
		}
	}
	$bndle_id = implode(",", $bndle_id_arr);

	// print_r($bndle_sl_arr);
	// echo "string".count($bndle_sl_arr[391834]);

	$sql="SELECT c.id AS dtls_id,a.requisition_number,a.style_ref_no,a.team_leader,a.season,a.season_year,a.TEAM_LEADER,a.DEALING_MARCHANT,b.GMTS_ITEM_ID,a.product_dept,b.SAMPLE_NAME,b.SAMPLE_COLOR,c.SIZE_ID,c.TOTAL_QTY
	FROM 
	SAMPLE_DEVELOPMENT_MST a, 
	SAMPLE_DEVELOPMENT_DTLS b, 
	SAMPLE_DEVELOPMENT_SIZE c
		WHERE   b.SAMPLE_MST_ID = a.id
        AND b.id = c.dtls_id
        AND a.id = c.mst_id
        AND a.status_active=1
        AND c.status_active=1
        AND b.status_active=1
        AND a.is_deleted=0
        AND b.is_deleted=0
        AND c.is_deleted=0
        and c.id in($bndle_id)
	ORDER BY a.id ASC";
	$res = sql_select($sql);
	
	
	$buyer_short_name=return_field_value("short_name","lib_buyer", "id=$buyer_name ");
	//return_library_array( "select id,short_name from lib_buyer where id=$buyer_name ", "id", "short_name");
	$lib_size = return_library_array("select id, size_name from  lib_size", "id", "size_name");
	$lib_color = return_library_array("select id, color_name from  lib_color", "id", "color_name");
	$dealing_marchant=return_library_array( "select id, team_member_name from lib_mkt_team_member_info",'id','team_member_name');
	$team_leader=return_library_array( "select id,team_name from  lib_sample_production_team where product_category=6 and is_deleted=0",'id','team_name');
	$brand_arr=return_library_array( "select id, brand_name from lib_buyer_brand",'id','brand_name');
	$season_arr=return_library_array( "select id,season_name from lib_buyer_season  where status_active =1 and is_deleted=0 ", "id", "season_name"  );
	$lib_sample = return_library_array("select a.id,a.sample_name,b.sequ from lib_sample a,lib_buyer_tag_sample b where a.id=b.tag_sample and b.sequ is not null and a.status_active=1 and a.is_deleted=0  group by  a.id,a.sample_name,b.sequ order by b.sequ", "id", "sample_name");

	$imge_arr=return_library_array( "select master_tble_id,image_location from common_photo_library where form_name='sample_details_1' and file_type=1",'master_tble_id','image_location');

	foreach($res as $val)
	{
		$req_no = $val[csf('requisition_number')];
	}

    $PNG_TEMP_DIR = dirname(__FILE__).DIRECTORY_SEPARATOR.'qrcode_image'.DIRECTORY_SEPARATOR.$req_no.DIRECTORY_SEPARATOR;
    $PNG_WEB_DIR = 'qrcode_image/'.$req_no.'/';

    foreach (glob($PNG_WEB_DIR."*.png") as $filename) {			
		@unlink($filename);
	}

    if (!file_exists($PNG_TEMP_DIR)) mkdir($PNG_TEMP_DIR);
     
    $filename = $PNG_TEMP_DIR.'test.png';
    $errorCorrectionLevel = 'L';
    $matrixPointSize = 4;

    include "../../../../ext_resource/phpqrcode/qrlib.php"; 
	require_once("../../../../ext_resource/mpdf60/mpdf.php");

	$mpdf = new mPDF('',    // mode - default ''
					array(40,50),		// array(65,210),    // format - A4, for example, default ''
					 5,     // font size - default 0
					 '',    // default font family
					 3,    // margin_left
					 3,    // margin right
					 3,     // margin top
					 0,    // margin bottom
					 0,     // margin header
					 0,     // margin footer
					 'L');
	$i=1; 
	foreach($res as $val)
	{
		for ($k=0; $k < count($bndle_sl_arr[$val['DTLS_ID']]); $k++) 
		{ 
			$qr_info = 'Season:-'.$season_arr[$val['SEASON']].'|'.$val['SEASON_YEAR'].';Style:-'.$val['STYLE_REF_NO'].';Product Dept:-'.$product_dept[$val['PRODUCT_DEPT']].';Garment Item:-'.$garments_item[$val['GMTS_ITEM_ID']].';Dealing Merchant:-'.$dealing_marchant[$val['DEALING_MARCHANT']].';Sample Team Name:-'.$team_leader[$val['TEAM_LEADER']].';Sample Name:-'.$lib_sample[$val['SAMPLE_NAME']].';Sample Color:-'.$lib_color[$val['SAMPLE_COLOR']].';Sample Size:-'.$lib_size[$val['SIZE_ID']];
			$barcode_no = $val[csf("requisition_number")];
			$filename = $PNG_TEMP_DIR.'test'.md5($barcode_no).'.png';
	    	QRcode::png($qr_info, $filename, $errorCorrectionLevel, $matrixPointSize, 2); 
					
			$mpdf->AddPage('',    // mode - default ''
				array(40,50),		// array(65,210),    // format - A4, for example, default ''
				 5,     // font size - default 0
				 '',    // default font family
				 3,    // margin_left
				 3,    // margin right
				 3,     // margin top
				 0,    // margin bottom
				 0,     // margin header
				 0,     // margin footer
				 'L');

				$html.='<table cellpadding="0" cellspacing="0" width="100%" class="" style="font-size:10px; font-weight:bold;margin:2px;" rules="all" id="" border="0">	
						    	
					<tr>
						<td align="center">
							<div id="div_'.$i.'"><img src="'.$PNG_WEB_DIR.basename($filename).'" height="100%" width=""></div>
						</td>
					</tr>

				</table>';


			$mpdf->WriteHTML($html);
			$html='';
			$i++;
		}
		
		
	} 
	
	//$mpdf->WriteHTML($html);
	foreach (glob("*.pdf") as $filename) {			
			@unlink($filename);
		}
	$name = 'req_no_' . date('j-M-Y_h-iA') .'_'.$user_id.'.pdf';
	$mpdf->Output($name, 'F');	
	echo "1###$name";
			
	exit();

}
?>