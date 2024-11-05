<? 
header('Content-type:text/html; charset=utf-8');
session_start();
if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");

require_once('../../../includes/common.php');


include('../../../includes/class4/class.conditions.php');
include('../../../includes/class4/class.reports.php');
include('../../../includes/class4/class.fabrics.php');
include('../../../includes/class4/class.yarns.php');
include('../../../includes/class4/class.conversions.php');
include('../../../includes/class4/class.trims.php');
include('../../../includes/class4/class.emblishments.php');
include('../../../includes/class4/class.washes.php');
include('../../../includes/class4/class.commercials.php');
include('../../../includes/class4/class.commisions.php');
include('../../../includes/class4/class.others.php');

$user_id=$_SESSION['logic_erp']['user_id'];

$data=$_REQUEST['data'];

$action=$_REQUEST['action'];
$appStatusArr=[0=>'Pending',1=>'Full App.',3=>'Partial'];

$company_arr=return_library_array( "select id, company_name from lib_company", "id", "company_name");
$buyer_arr=return_library_array( "select id, short_name from lib_buyer", "id", "short_name");
//--------------------------------------------------------------------------------------------------------------------

if ($action=="load_drop_down_buyer")
{
	echo create_drop_down( "cbo_buyer_name", 130, "select buy.id,buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company='$data' and buy.id in (select buyer_id from lib_buyer_party_type where party_type in (1,3,21,90)) $buyer_cond order by buyer_name","id,buyer_name", 1, "-- All Buyer --", $selected, "load_drop_down( 'requires/precost_approval_status_report_controller', this.value, 'load_drop_down_season', 'season_td');" );     	 
	exit();
}
if ($action=="load_drop_down_season")
{
	$data_arr = explode("*", $data);
	if($data_arr[1] == 1) $width=70; else $width=140;
	echo create_drop_down( "cbo_season_id", $width, "select id, season_name from lib_buyer_season where buyer_id='$data_arr[0]' and status_active =1 and is_deleted=0 order by season_name ASC","id,season_name", 1, "-- Select Season--", "", "" );
	exit();
}
if ($action == "load_drop_down_user") {
  echo create_drop_down("cbo_user_id", 150, "SELECT a.id,a.sequence_no,a.user_id,a.page_id,entry_form,b.user_name FROM electronic_approval_setup a,user_passwd b
  WHERE  a.user_id=b.id and a.company_id = '$data' AND a.entry_form =15 AND a.is_deleted = 0  and b.is_deleted=0
  ORDER BY sequence_no", 'user_id,user_name', 1, '-- All --', 0, '', 0);
  //and b.party_type =9

  
  exit();
}

if($action=="search_popup")
{
	extract($_REQUEST);
	echo load_html_head_contents($tittle." No Info", "../../../", 1, 1,'','','');
	?>
	<script>
		var selected_id = new Array; var selected_name = new Array;
		
		function check_all_data()
		{
			var tbl_row_count = document.getElementById( 'tbl_list_search' ).rows.length;
			tbl_row_count = tbl_row_count - 1;

			for( var i = 1; i <= tbl_row_count; i++ )
			{
				$('#tr_'+i).trigger('click'); 
			}
		}
		
		function toggle( x, origColor ) {
			var newColor = 'yellow';
			if ( x.style ) {
				x.style.backgroundColor = ( newColor == x.style.backgroundColor )? origColor : newColor;
			}
		}
		
		function js_set_value( str ) {
			
			if (str!="") str=str.split("_");
			 
			toggle( document.getElementById( 'tr_' + str[0] ), '#FFFFCC' );
			 
			if( jQuery.inArray( str[1], selected_id ) == -1 ) {
				selected_id.push( str[1] );
				selected_name.push( str[2] );
				
			}
			else {
				for( var i = 0; i < selected_id.length; i++ ) {
					if( selected_id[i] == str[1] ) break;
				}
				selected_id.splice( i, 1 );
				selected_name.splice( i, 1 );
			}
			var id = ''; var name = '';
			for( var i = 0; i < selected_id.length; i++ ) {
				id += selected_id[i] + ',';
				name += selected_name[i] + '*';
			}
			
			id = id.substr( 0, id.length - 1 );
			name = name.substr( 0, name.length - 1 );
			
			$('#hide_id').val( id );
			$('#hide_no').val( name );
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
                      <th id="search_by_td_up" width="170">Please Enter <? echo $tittle; ?> No </th>
                      <th><input type="reset" name="button" class="formbutton" value="Reset"  style="width:100px;"></th> 
                      <input type="hidden" name="hide_no" id="hide_no" value="" />
                      <input type="hidden" name="hide_id" id="hide_id" value="" />
                  </thead>
                  <tbody>
                    <tr>
                          <td align="center">
                            <? 
                  echo create_drop_down( "cbo_buyer_name", 140, "select buy.id, buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company=$companyID $buyer_cond and buy.id in (select buyer_id from lib_buyer_party_type where party_type in (1,3,21,90)) order by buy.buyer_name","id,buyer_name",1, "-- All Buyer--",$buyerID,"",0 );
                ?>
                          </td>                 
                          <td align="center">	
                        <?
                  $search_by_arr=array(1=>"Job No",2=>"Style Ref",3=>"Internal Ref");
                $dd="change_search_event(this.value, '0*0*', '0*0*', '../../../') ";							
                echo create_drop_down( "cbo_search_by", 130, $search_by_arr,"",0, "--Select--", "",$dd,0 );
              ?>
                          </td>     
                          <td align="center" id="search_by_td">				
                              <input type="text" style="width:130px" class="text_boxes" name="txt_search_common" id="txt_search_common" />	
                          </td> 	
                          <td align="center">
                            <input type="button" name="button" class="formbutton" value="Show" onClick="show_list_view ('<? echo $companyID; ?>'+'**'+document.getElementById('cbo_buyer_name').value+'**'+document.getElementById('cbo_search_by').value+'**'+document.getElementById('txt_search_common').value+'**'+<? echo $type; ?>, 'create_job_booking_no_search_list_view', 'search_div', 'precost_approval_status_report_controller', 'setFilterGrid(\'tbl_list_search\',-1)');" style="width:100px;" />
                      </td>
                      </tr>
                </tbody>
              </table>
              <div style="margin-top:15px" id="search_div"></div>
      </fieldset>
    </form>
  </div>
  </body>           
  <script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
  </html>
  <?
    exit(); 
}

if($action=="create_job_booking_no_search_list_view")
{
	$data=explode('**',$data);
	$company_id=$data[0];
	$search_by=$data[2];
	
	$search_string="%".trim($data[3])."%";
	$type_id=$data[4];

	$arr=array (0=>$company_arr,1=>$buyer_arr);
	if($search_by==1) $search_field="a.job_no"; 
	else if($search_by==2) $search_field="a.style_ref_no"; 
	else $search_field="b.grouping";
	if($data[1]==0)
	{
		if ($_SESSION['logic_erp']["data_level_secured"]==1)
		{
			if($_SESSION['logic_erp']["buyer_id"]!="") $buyer_id_cond=" and a.buyer_name in (".$_SESSION['logic_erp']["buyer_id"].")"; else $buyer_id_cond="";
		}
		else $buyer_id_cond="";
	}
	else $buyer_id_cond=" and a.buyer_name=$data[1]";
		
	if($type_id==2)
	{
		$sql= "select a.id, a.job_no, a.company_name, a.buyer_name, a.style_ref_no from wo_po_details_master a where a.status_active=1 and a.is_deleted=0 and a.company_name=$company_id and $search_field like '$search_string' $buyer_id_cond order by a.id DESC";
			
		echo create_list_view("tbl_list_search", "Company,Buyer Name,Job No,Style Ref. No,", "120,120,120","600","240",0, $sql , "js_set_value", "id,job_no", "", 1, "company_name,buyer_name,0,0", $arr , "company_name,buyer_name,job_no,style_ref_no", "",'','0,0,0,0','',1) ;
	}
	else
	{
		 $sql= "select a.id, a.job_no, a.company_name, a.buyer_name, a.style_ref_no,b.grouping from wo_po_details_master a ,wo_po_break_down b where a.job_no=b.job_no_mst and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.company_name=$company_id and $search_field like '$search_string' $buyer_id_cond group by a.id, a.job_no, a.company_name, a.buyer_name, a.style_ref_no,b.grouping  order by a.id DESC";
		
		echo create_list_view("tbl_list_search", "Company,Buyer Name,Job No,Style Ref. No,Internal Ref. No", "120,120,120,100","600","240",0, $sql , "js_set_value", "id,grouping", "", 1, "company_name,buyer_name,0,0,0", $arr , "company_name,buyer_name,job_no,style_ref_no,grouping", "",'','0,0,0,0','',1) ;
	}
   exit(); 
} 

$tmplte=explode("**",$data);

if ($tmplte[0]=="viewtemplate") $template=$tmplte[1]; else $template=$lib_report_template_array[$_SESSION['menu_id']]['0'];
if ($template=="") $template=1;

if($action=="report_generate")
{  
	$process = array( &$_POST );

  //print_r($process);
	extract(check_magic_quote_gpc( $process )); 
 
 	$date_cond=""; $from_date=""; $to_date="";
 	if(str_replace("'","",trim($txt_date_from))!="" && str_replace("'","",trim($txt_date_to))!="")
	{
		if(str_replace("'","",trim($cbo_date_by))==1)
		{
			$date_cond=" and a.costing_date between $txt_date_from and $txt_date_to";
			$date_cond1=" and b.costing_date between $txt_date_from and $txt_date_to";
		}
		else if(str_replace("'","",trim($cbo_date_by))==2)
		{
      $txt_date_to=date("d-M-Y 11:59:59 A",strtotime(str_replace("'","",trim($txt_date_to))));   
      $date_cond=" and a.insert_date between $txt_date_from and '$txt_date_to'";
			$date_cond1=" and b.insert_date between $txt_date_from and '$txt_date_to'";
		}
		else
		{
			$from_date=date("Y-m-d",strtotime(str_replace("'","",trim($txt_date_from))));
			$to_date=date("Y-m-d",strtotime(str_replace("'","",trim($txt_date_to))));
      
      $txt_date_to=date("d-M-Y 11:59:59 A",strtotime(str_replace("'","",trim($txt_date_to))));   
      $app_date_con = "and a.id in(select x.mst_id from APPROVAL_MST x where x.APPROVED_DATE  between $txt_date_from and '$txt_date_to' )";
		}
	}

 

	
	$cbo_date_by=str_replace("'","",trim($cbo_date_by));
	//if($from_date>$to_date)
	$type = str_replace("'","",$cbo_type);
  $cbo_season_id = str_replace("'","",$cbo_season_id);
  $cbo_user_id = str_replace("'","",$cbo_user_id);
 // print_r($cbo_user_id);
  $year_selection = str_replace("'","",$cbo_year_selection);
	$txt_ref_no = str_replace("'","",$txt_ref_no);
 
	$buyer_id_cond="";
	
	$txt_job_no=str_replace("'","",$txt_job_no);
	if($txt_job_no=="") $job_cond=""; else $job_cond=" and a.job_no in('".implode("','",explode("*",$txt_job_no))."')";
	if($txt_ref_no=="") $ref_cond=""; else $ref_cond=" and b.grouping in('".implode("','",explode("*",$txt_ref_no))."')";
  if($cbo_season_id==0) $season_cond=""; else $season_cond=" and b.season_buyer_wise=$cbo_season_id";
  if($txt_ref_no=="") $ref_cond=""; else $ref_cond=" and b.grouping in('".implode("','",explode("*",$txt_ref_no))."')";
  

	
  //echo $user_cond.'sqqsss';
  if ($year_selection=="" || $year_selection==0) $select_year_cond="";
	else
	{
		if($db_type==2) $select_year_cond=" and to_char(b.insert_date,'YYYY')='".trim($year_selection)."' ";
		else $select_year_cond=" and YEAR(b.insert_date)='".trim($year_selection)."' ";
	}

      // echo $select_year_cond;die;

 
	
	if(str_replace("'","",$cbo_buyer_name)!=0){ $buyer_id_cond=" and b.buyer_name=$cbo_buyer_name";$buyer_id_cond1=" and a.buyer_name=$cbo_buyer_name";}else{  $buyer_id_cond="";$buyer_id_cond1="";};

	$dealing_merchant_array = return_library_array("select id, team_member_name from lib_mkt_team_member_info","id","team_member_name");
	$designation_array=return_library_array( "select id, custom_designation from lib_designation", "id", "custom_designation" );
  $season_array = return_library_array("select id, season_name from lib_buyer_season","id","season_name");
	
	$user_name_array=array();
	$userData=sql_select( "select id, user_name, user_full_name, designation, buyer_id from user_passwd");
	foreach($userData as $user_row)
	{
		$user_name_array[$user_row[csf('id')]]['name']=$user_row[csf('user_name')];
		$user_name_array[$user_row[csf('id')]]['full_name']=$user_row[csf('user_full_name')];
		$user_name_array[$user_row[csf('id')]]['designation']=$designation_array[$user_row[csf('designation')]];
		$user_name_array[$user_row[csf('id')]]['buyer_id']=$user_row[csf('buyer_id')];	
	}
//   echo "<pre>";
// print_r($user_name_array); 
//   echo "</pre>";die();
	if($db_type==0) $group_con="group_concat( distinct b.id) AS po_id";
	if($db_type==2) $group_con="listagg(b.id ,',') within group (order by b.id) AS po_id";
			
 
  if($cbo_date_by ==1 && str_replace("'","",$txt_date_from)!='' && str_replace("'","",$txt_date_to)!='' ){
      $date_cond3=" and a.costing_date between $txt_date_from and $txt_date_to";
  }
  elseif($cbo_date_by ==2 && str_replace("'","",$txt_date_from)!='' && str_replace("'","",$txt_date_to)!='' ){
    $txt_date_to=date("d-M-Y 11:59:59 A",strtotime(str_replace("'","",trim($txt_date_to))));   
    $date_cond3=" and a.insert_date between $txt_date_from and $txt_date_to ";
  
  }
  
 

	
	$buyer_id_arr=return_library_array( "select user_id, buyer_id from electronic_approval_setup where company_id=$cbo_company_name and is_deleted=0 and entry_form in(15,46) and bypass=2", "user_id", "buyer_id" );
	
	$signatory_data_arr=sql_select("select user_id as user_id, buyer_id, sequence_no,bypass from electronic_approval_setup where company_id=$cbo_company_name and is_deleted=0 and entry_form in(15,46,11) order by sequence_no");	
	
	foreach($signatory_data_arr as $sval)
	{
		if($sval[csf('buyer_id')]!="")
		{
			$exbid=explode(",",$sval[csf('buyer_id')]);
			foreach($exbid as $elecBid)
			{
				$signatory_main[$elecBid][$sval[csf('user_id')]]=$sval[csf('bypass')];
			}
		}
		else
		{
			$adminUserBuyerId=$user_name_array[$sval[csf('user_id')]]['buyer_id'];
			if($adminUserBuyerId!="")
			{
				$exadminbid=explode(",",$adminUserBuyerId);
				foreach($exadminbid as $adminBid)
				{
					$signatory_main[$adminBid][$sval[csf('user_id')]]=$sval[csf('bypass')];
				}
			}
			else
			{
				foreach($buyer_arr as $libBid=>$libbname)
				{
					$signatory_main[$libBid][$sval[csf('user_id')]]=$sval[csf('bypass')];
				}
			}
		}
	}
// 	echo "<pre>";
// print_r($signatory_main); 
//   echo "</pre>";die();
	$bypass_no_user_id_main=$signatory_data_arr[0][csf('user_idby')];

  if($cbo_user_id !=0){
    $app_by_con = "and a.id in(select x.mst_id from APPROVAL_MST x where x.APPROVED_BY=$cbo_user_id)";
  }
  $query="select b.MST_ID,b.APPROVED_DATE,b.APPROVED_BY from APPROVAL_MST b,wo_pre_cost_mst a where a.id=b.mst_id and b.ENTRY_FORM in (46,15,11)  $app_by_con $job_cond $approved_cond $ready_to_approved $app_date_con";
 //echo $query;die;

	$user_approval_array=array(); $user_ip_array=array(); $max_approval_date_array=array(); $approved_no_array=array();
	$result=sql_select( $query );
	foreach ($result as $row)
	{
    $pro_cost_id_arr[$row['MST_ID']]=$row['MST_ID'];
		$user_approval_array[$row['MST_ID']][$row['APPROVED_BY']]=$row['APPROVED_DATE'];
		$user_ip_array[$row['MST_ID']][$row['APPROVED_BY']]=$row[csf('user_ip')];
		$approved_date=date("Y-m-d",strtotime($row['APPROVED_DATE']));
		
		if($max_approval_date_array[$row['MST_ID']]=="")
		{
			$max_approval_date_array[$row['MST_ID']]=$approved_date;
		}
		else
		{
			if($approved_date>$max_approval_date_array[$row['MST_ID']])
			{
				$max_approval_date_array[$row['MST_ID']]=$approved_date;
			}
		}
	}


  $appHis = "select MST_ID,APPROVED_BY,approved_no from APPROVAL_HISTORY where entry_form = 15 ".where_con_using_array($pro_cost_id_arr,0,'mst_id')."";
  //echo $appHis;die;

  $appHisRes=sql_select( $appHis );
	foreach ($appHisRes as $his_row)
	{
    $approved_no_array[$his_row['MST_ID']][$his_row['APPROVED_BY']]=$his_row['approved_no'];
  }



 




 //print_r($user_approval_array[$row[csf('mst_id')]]);
 $tWidth=2720;
	ob_start();
	?>
    <fieldset style="width:<?=$tWidth;?>px;">
        <table cellpadding="0" cellspacing="0" width="100%">
            <tr>
               <td align="center" width="100%" colspan="15" style="font-size:16px"><strong><?=$report_title; ?></strong></td>
            </tr>
            <tr>
               <td align="center" width="100%" colspan="15" style="font-size:16px"><strong><?=$company_library[str_replace("'","",$cbo_company_name)]; ?></strong></td>
            </tr>
        </table>	
        <table cellspacing="0" cellpadding="0" border="1" rules="all" width="<?=$tWidth;?>" class="rpt_table" align="left" >
            <thead>
                <th width="40">SL</th>
                <th width="100">Job No</th>
			        	<th width="100">Last Version</th>
                <th width="100">Style Ref.</th>
                <th width="100">Approval History</th>
                <th width="100">Season</th>
                <th width="80">FOB</th>
                <th width="80">CM Cost</th>
                <th width="80">EPM</th>
                <th width="80">SMV</th>
                <th width="100">Internal Ref</th>
                <th width="80">Buyer Name</th>
                <th width="110">Dealing Merchant</th>
                <th width="50">Image</th>
                <th width="50">File</th>
                <th width="100">Comm File NO</th>
                <th width="170">Order No</th>
                <th width="80">Shipment Date [Min.]</th>
                <th width="120">Costing Date</th>
                <th width="100">Ready To App Yes Date</th>
                <th width="60">Comments</th>
                <th width="140">Signatory</th>
                <th width="130">Designation</th>
                <th width="50">Can Bypass</th>
                <th width="50">App Status</th>
                <th width="100">IP Address</th>
                <th width="100">Approval Date</th>
                <th width="100">Approval Time</th>
                <th width="50">Duration</th>
                <th>Approve No</th>
            </thead>
        </table>
        <div style="width:<?=$tWidth+20;?>px; overflow-y:scroll; max-height:310px;" id="scroll_body">
            <table cellspacing="0" cellpadding="0" border="1" rules="all" width="<?=$tWidth;?>" class="rpt_table" id="tbl_list_search" align="left">
                <tbody>
                    <? 
                    $print_reportSql="Select report_id, format_id from lib_report_template where template_name =".$cbo_company_name." and module_id=2 and report_id in(22,43,122) and is_deleted=0 and status_active=1";
					$print_reportSqlRes=sql_select( $print_reportSql);
                    foreach($print_reportSqlRes as $prow)
                    {
						            if($prow[csf('report_id')]==22)//BOM Old knit
                        { 
                            $exformatid=explode(",",$prow[csf('format_id')]);
                            $format_idknitold=$exformatid[0];
                        }
                        if($prow[csf('report_id')]==43)//BOM V2 knit
                        { 
                            $exformatid=explode(",",$prow[csf('format_id')]);
                            $format_idknit=$exformatid[0];
                        }
                        else if($prow[csf('report_id')]==122)//BOM V2 Woven
                        {
                             $exformatid=explode(",",$prow[csf('format_id')]);
                             $format_idwvn=$exformatid[0];
                        }
						            else//BOM V2 Sweater
                        {
                             $exformatid=explode(",",$prow[csf('format_id')]);
                             $format_idsweater=$exformatid[0];
                        }
                    }
                   
					
                   
                        
            $bypass_no_user_id_main=explode(",",$bypass_no_user_id_main);
            // if( $type==0 && $cbo_user_id!==0) $approved_cond=" and a.approved in (1,3)";
            // else if($type==2) $approved_cond=" and a.approved=1";
            // elseif($type==1) $approved_cond=" and a.approved=3";
            // elseif($type==3) $approved_cond=" and a.approved=2 and a.ready_to_approved<>1";
            // elseif($type==4)  $approved_cond=" and a.approved in (0,2,5)";
           
            // else $approved_cond=" and a.approved in (0,1,2,3,5)";

            if($type==2) $approved_cond=" and a.approved=1";
            elseif($type==1) $approved_cond=" and a.approved=3";
            elseif($type==3) $approved_cond=" and a.approved=5 and a.ready_to_approved<>1";
            elseif($type==4)  $approved_cond=" and a.approved in (0,2,5)";
            else $approved_cond=" and a.approved in (0,1,2,3,5)";
                   
           if($type<>3){$ready_to_approved=" and a.ready_to_approved=1";}
           
                   
           if($type<>3){$ready_to_approved=" and a.ready_to_approved=1";}

           if($cbo_user_id!=0 && ($type != 4 && $type != 3)) {$user_cond=" and a.id in(select a.mst_id from APPROVAL_MST a,wo_pre_cost_mst b where b.id=a.mst_id and a.ENTRY_FORM=15 and a.APPROVED_BY=$cbo_user_id $job_cond $approved_cond $ready_to_approved )";}
        
	 

          $sql="select a.APPROVED_DATE,a.APPROVED_BY,a.id,b.id as job_id, b.garments_nature, b.company_name, b.buyer_name,b.season_buyer_wise, b.style_ref_no, a.entry_from, a.costing_date,a.UPDATE_DATE, a.job_no, a.approved, a.insert_date,c.cm_cost,a.sew_smv,c.price_dzn,a.costing_per ,(select max(h.approved_no) from approval_history h where a.id=h.mst_id and h.entry_form in(46,15) ) as approved_no
        ,b.quotation_id from  wo_pre_cost_mst a left join wo_pre_cost_dtls c on c.job_no=a.job_no, wo_po_details_master b  where a.job_no=b.job_no and b.company_name=$cbo_company_name $select_year_cond and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $ready_to_approved $buyer_id_cond $job_cond $approved_cond $date_cond3 $po_cond_for_in $season_cond $user_cond $app_date_con  group by a.id,a.APPROVED_DATE,a.APPROVED_BY, b.garments_nature, b.company_name, b.buyer_name, b.id,b.style_ref_no,b.season_buyer_wise, a.entry_from, a.costing_date,a.UPDATE_DATE, a.job_no, a.approved, a.insert_date,c.cm_cost,a.sew_smv,c.price_dzn,a.costing_per,b.quotation_id order by a.id desc";
			 //echo $sql;die(); 
           $nameArray=sql_select( $sql);
           $job_arr=array();$pre_cost_id_arr=array();
           foreach( $nameArray as $rows){
              $job_arr[$rows[csf('job_id')]]=$rows[csf('job_id')];		
           }

           $electronicSetupSql = "select DEPARTMENT as COMPONENT_ID,USER_ID from electronic_approval_setup where company_id = $cbo_company_name and entry_form=11  and is_deleted=0";
           $electronicSetupSqlResult = sql_select($electronicSetupSql);
           foreach($electronicSetupSqlResult as $row){
             $elec_component_id_arr[$row['USER_ID']] = explode(',',$row['COMPONENT_ID']);
           }

           $component_his_sql = "select b.JOB_ID,b.APPROVED_BY,count(COST_COMPONENT_ID) as TOTAL_COMPONENT_ID,max(b.CURRENT_APPROVAL_STATUS) as CURRENT_APPROVAL_STATUS from CO_COM_PRE_COSTING_APPROVAL b  where b.ENTRY_FORM=11 ".where_con_using_array($job_arr,0,'b.JOB_ID')." group by b.JOB_ID,b.APPROVED_BY";
           //echo  $component_his_sql;die;
           $component_his_sql_res=sql_select( $component_his_sql);
           $user_app_status_arr=array();
           foreach( $component_his_sql_res as $rows){
            if(count($elec_component_id_arr[$rows['APPROVED_BY']]) > $rows['TOTAL_COMPONENT_ID'] ){
              $rows['CURRENT_APPROVAL_STATUS'] =3;
            }
            $user_app_status_arr[$rows['JOB_ID']][$rows['APPROVED_BY']]=$rows['CURRENT_APPROVAL_STATUS'];			
          }

          

           $jobSql="select a.id as job_id, a.dealing_marchant, a.job_no, b.id as po_id, b.grouping, b.po_number as po_number, min(b.pub_shipment_date) as min_ship_date, b.file_no from wo_po_break_down b, wo_po_details_master a where  a.id=b.job_id $job_cond $ref_cond and a.company_name=$cbo_company_name ".where_con_using_array($job_arr,0,'a.id')." group by a.id, a.dealing_marchant, b.id, a.job_no, b.po_number, a.style_ref_no, b.grouping, b.file_no";
          // echo $jobSql;die;
          $jobSql=sql_select($jobSql);
          $jobArr=array();
          foreach($jobSql as $inf)
          {
            $jobArr[$inf[csf('job_no')]]['order_no'].=$inf[csf('po_number')].',';
            $jobArr[$inf[csf('job_no')]]['grouping'].=$inf[csf('grouping')].',';
            $jobArr[$inf[csf('job_no')]]['order_id'].=$inf[csf('po_id')].',';
            $jobArr[$inf[csf('job_no')]]['file_no'].=$inf[csf('file_no')].',';
            $jobArr[$inf[csf('job_no')]]['pono'].=$inf[csf('po_number')].'**';
            $jobArr[$inf[csf('job_no')]]['min_ship_date']=$inf[csf('min_ship_date')];
            $jobArr[$inf[csf('job_no')]]['dealing_marchant']=$inf[csf('dealing_marchant')];
            $po_array[$inf[csf('po_id')]]=$inf[csf('po_number')];
          }
          unset($jobSql);


				 

					$costing_for="";
					$i=1;
					foreach ($nameArray as $row)
					{

						$full_approval='';

						$costingPer=$row[csf("costing_per")];
						if($costingPer==1){
							$order_price_per_dzn=12;
							$costing_for=" DZN";
						}
						else if($costingPer==2){
							$order_price_per_dzn=1;
							$costing_for=" PCS";
						}
						else if($costingPer==3){
							$order_price_per_dzn=24;
							$costing_for=" 2 DZN";
						}
						else if($costingPer==4){
							$order_price_per_dzn=36;
							$costing_for=" 3 DZN";
						}
						else if($costingPer==5){
							$order_price_per_dzn=48;
							$costing_for=" 4 DZN";
						}



						//print_r($bypass_no_user_id_main);
						//$signatory=$signatory_main;
						$rowspanMain=$rowspan=0;
						$rowspanMain=count($signatory_main[$row[csf('buyer_name')]]);
						$rowspan=$rowspanMain;
						$refno=rtrim($jobArr[$row[csf('job_no')]][grouping],',');
						$internal_ref=implode(",",array_unique(explode(",",$refno)));
						//echo $internal_ref.'X';
						$full_approval=true; $approvedStatus="";
						foreach($bypass_no_user_id_main as $uId)
						{
							$buyer_ids=$buyer_id_arr[$uId];
							$buyer_ids_array=explode(",",$buyer_id_arr[$uId]);
						}
						
						if($cbo_date_by==3 && $from_date!="" && $to_date!="")
						{
							$max_approved_date=$max_approval_date_array[$row[csf('id')]];
							if($max_approved_date>=$from_date && $max_approved_date<=$to_date)
							{
								$print_cond=1;
							}
							else $print_cond=0;
						}
						else $print_cond=1;


      
						
						if(((($type==1) || $row[csf('approved')]==2 || $row[csf('approved')]==5 || $row[csf('approved')]==0) || ($type==2)) || $type==3 || $type==0)
						{
							if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
							
							$dealing_merchant=$dealing_merchant_array[$jobArr[$row[csf('job_no')]][dealing_marchant]];
							$order_no=rtrim($jobArr[$row[csf('job_no')]][order_no],',');
							$file_no=rtrim($jobArr[$row[csf('job_no')]][file_no],',');
							$order_id=rtrim($jobArr[$row[csf('job_no')]][order_id],',');
							$poNos=array_unique(explode(",",$order_no));
							$fileno=array_unique(explode(",",$file_no));
						  //	$order_id=array_unique(explode(",",$order_id));
							$poIds=array_unique(explode(",",$order_id));
              $orderIds=implode(",",array_unique(explode("**",$jobArr[$row[csf('job_no')]][pono])));
              	
							$z=0; 
							foreach($signatory_main[$row[csf('buyer_name')]] as $user_id=>$val)
							{
								?>
								<tr bgcolor="<?=$bgcolor; ?>" onClick="change_color('tr_<?=$i; ?>','<?=$bgcolor; ?>');" id="tr_<?=$i; ?>"> 
								<?
								if($z==0)
								{
									if(str_replace("'","",trim($cbo_date_by))==1)
									{
										$date_all="C Date : ".change_date_format($row[csf('costing_date')]);
									}
									else if(str_replace("'","",trim($cbo_date_by))=='2')
									{
										$insert_date=$row[csf('insert_date')];
										$date_all="In Date: ".date("d-m-Y",strtotime($insert_date)); 
									}
									?>
									<td width="40" rowspan="<?=$rowspan; ?>" align="center"><?=$i; ?></td>
									<td width="100" title="<?=$row[csf('entry_from')];?>" rowspan="<?=$rowspan; ?>" align="center" style="word-break:break-all">
									<?
									$job_id=$row[csf('job_id')];
									$rptformatName="";

                 // $format_idknit=730;
                  
									if($row[csf('entry_from')]==158 || $row[csf('entry_from')]==425 || $row[csf('entry_from')]==521)//Pre Cost V2//Wvn V2
									{ 
										if($row[csf('garments_nature')]==2)//Knit BOM
										{   
											if($format_idknit==50) $rptformatName="preCostRpt";//Cost Rpt
											else if($format_idknit==51) $rptformatName="preCostRpt2";//Cost Rpt2
											else if($format_idknit==52) $rptformatName="bomRpt";//BOM Rpt
											else if($format_idknit==63) $rptformatName="bomRpt2";//BOM Rpt 2
											else if($format_idknit==129) $rptformatName="budget5";//budget5
											else if($format_idknit==156) $rptformatName="accessories_details";//Acce. Dtls
											else if($format_idknit==157) $rptformatName="accessories_details2";//Acce. Dtls 2
											else if($format_idknit==158) $rptformatName="preCostRptWoven";//Cost Woven
											else if($format_idknit==159) $rptformatName="bomRptWoven";//Bom Woven
											else if($format_idknit==170) $rptformatName="preCostRpt3";//Cost Rpt3
											else if($format_idknit==171) $rptformatName="preCostRpt4";//Cost Rpt4
											else if($format_idknit==142) $rptformatName="preCostRptBpkW";//Rpt Bpkw
											else if($format_idknit==192) $rptformatName="checkListRpt";//BOM Dtls
											else if($format_idknit==197) $rptformatName="bomRpt3";//BOM Rpt 3
											else if($format_idknit==211) $rptformatName="mo_sheet";//MO Sheet
											else if($format_idknit==221) $rptformatName="fabric_cost_detail";//Fab. Pre-Cost
											else if($format_idknit==173) $rptformatName="preCostRpt5";//Cost Rpt5
											else if($format_idknit==238) $rptformatName="summary";//Summary
											else if($format_idknit==215) $rptformatName="budget3_details";//Budget3 Details
											else if($format_idknit==270) $rptformatName="preCostRpt6";//Cost Rpt6
											else if($format_idknit==581) $rptformatName="costsheet";//Cost sheet
											else if($format_idknit==268) $rptformatName="budget_4";//budget 4
											else if($format_idknit==769) $rptformatName="preCostRpt7";
											else if($format_idknit==129) $rptformatName="report_generate";
                      else if($format_idknit==498) $rptformatName="preCostRpt10";
								      else if($format_idknit==235) $rptformatName="preCostRpt9";
								      else if($format_idknit==800) $rptformatName="preCostRpt11";
                      else if($format_idknit==730) $rptformatName="budgetsheet";

                    
										}
										else if($row[csf('garments_nature')]==3)//Woven BOM
										{
											if($format_idwvn==311) $rptformatName="bom_epm_woven";//BOM EPM
											else if($format_idwvn==51) $rptformatName="preCostRpt2";//Cost Rpt2
											else if($format_idwvn==158) $rptformatName="preCostRptWoven";//Cost Woven
											else if($format_idwvn==159) $rptformatName="bomRptWoven";//Bom Woven
											else if($format_idwvn==192) $rptformatName="checkListRpt";//BOM Dtls
											else if($format_idwvn==307) $rptformatName="basic_cost";//Basic Cost
											else if($format_idwvn==313) $rptformatName="mkt_source_cost";//MKT Vs Source
                      
										}
										else if($row[csf('garments_nature')]==100)//Sweater BOM
										{
											if($format_idsweater==51) $rptformatName="preCostRpt2";//Cost Rpt2
											else if($format_idsweater==211) $rptformatName="mo_sheet";//MO Sheet
										}
										
                    if($rptformatName!="") 
										{ 
											?><a href="##" title="Pre Cost V2" onClick="generate_report(<?=$cbo_company_name; ?>,'<?=$row[csf('job_no')]; ?>','<?=$job_id; ?>',<?=$row[csf('buyer_name')]; ?>,'<?=$row[csf('style_ref_no')]; ?>','<?=$row[csf('costing_date')]; ?>','<?=$rptformatName; ?>',<?=$row[csf('entry_from')]; ?>,<?=$row[csf('garments_nature')]; ?>);" ><?=$row[csf('job_no')]; ?></a><? 
										
										

												//=====================revise no===================================	
											
												$function2="";
												if($row[csf('approved_no')]>0)
												{
													for($version=1; $version<=$row[csf('approved_no')]; $version++)
													{
														if($function2=="") $function2="<a href='#' onClick=\"history_budget_sheet('".$row[csf('job_no')]."',".$cbo_company_name.",".$row[csf('buyer_name')].",'".$row[csf('style_ref_no')]."','".$row[csf('costing_date')]."',".$row[csf('entry_from')].",'".$row[csf('quotation_id')]."','".$version."'".")\"> ".$version."<a/>";
														else $function2.=", "."<a href='#' onClick=\"history_budget_sheet('".$row[csf('job_no')]."',".$cbo_company_name.",".$row[csf('buyer_name')].",'".$row[csf('style_ref_no')]."','".$row[csf('costing_date')]."',".$row[csf('entry_from')].",'".$row[csf('quotation_id')]."','".$version."'".")\"> ".$version."<a/>";
														
													}
												}
												
											//=====================revise no===================================	
										
										}
										else echo $row[csf('job_no')];
									}
									else //Pre Cost
									{
										if($row[csf('garments_nature')]==2)//Knit BOM
										{
											if($format_idknitold==50) $rptformatName="preCostRpt";//Cost Rpt
											else if($format_idknitold==51) $rptformatName="preCostRpt2";//Cost Rpt2
											else if($format_idknitold==52) $rptformatName="bomRpt";//BOM Rpt
											else if($format_idknitold==63) $rptformatName="bomRpt2";//BOM Rpt 2
											else if($format_idknitold==142) $rptformatName="preCostRptBpkW";//Acce. Dtls
											else if($format_idknitold==173) $rptformatName="preCostRpt5";//Acce. Dtls 2
										}
										if($rptformatName!="") 
										{
											?><a href="##" title="Pre Cost Old" onClick="generate_report(<?=$cbo_company_name; ?>,'<?=$row[csf('job_no')]; ?>','<?=$job_id; ?>',<?=$row[csf('buyer_name')]; ?>,'<?=$row[csf('style_ref_no')]; ?>','<?=$row[csf('costing_date')]; ?>','<?=$rptformatName; ?>',<?=$row[csf('entry_from')]; ?>,<?=$row[csf('garments_nature')]; ?>);" ><?=$row[csf('job_no')]; ?></a><?

											
											//=====================revise no===================================	
											
                    $function2="";
                    if($row[csf('approved_no')]>0)
                    {
                      for($version=1; $version<=$row[csf('approved_no')]; $version++)
                      {
                       
                        if($function2=="") $function2="<a href='#' onClick=\"history_budget_sheet('".$row[csf('job_no')]."',".$cbo_company_name.",".$row[csf('buyer_name')].",'".$row[csf('style_ref_no')]."','".$row[csf('costing_date')]."',".$row[csf('entry_from')].",'".$row[csf('quotation_id')]."','".$version."'".")\"> ".$version."<a/>";
                        else $function2.=", "."<a href='#' onClick=\"history_budget_sheet('".$row[csf('job_no')]."',".$cbo_company_name.",".$row[csf('buyer_name')].",'".$row[csf('style_ref_no')]."','".$row[csf('costing_date')]."',".$row[csf('entry_from')].",'".$row[csf('quotation_id')]."','".$version."'".")\"> ".$version."<a/>";


                        
                      }
                    }
									
											//=====================revise no===================================	
										}
										else echo $row[csf('job_no')]; 
									} 
									
							
									//=====================revise no===================================	
						
                  if($type==3){
                    echo '<br><a href="#" onClick="get_deny_cause_his('.$row[csf('id')].')">Deny History<a/>';
                  }

									?>


                  </td>

									<td width="100" rowspan="<?=$rowspan; ?>" style="word-break:break-all"><?=$function2;?>&nbsp;  </td>
                  <td width="100" rowspan="<?=$rowspan; ?>" style="word-break:break-all"><?=$row[csf('style_ref_no')]; ?>&nbsp;</td>
        
                  <td width="100" rowspan="<? echo $rowspan; ?>" style="word-break:break-all"><p>
												<a href="javascript:generate_mkt_report('<?=$row[csf('id')]; ?>','<?=$row[csf('job_no')]; ?>','show_fabric_approval_report')">
												View
												</a>
											</p></td>
                  <td width="100" rowspan="<?=$rowspan; ?>" style="word-break:break-all"><?=$season_array[$row[csf('season_buyer_wise')]]; ?>&nbsp;</td>
									<td width="80" rowspan="<?=$rowspan; ?>" align="right"><?=number_format($row[csf('price_dzn')]/$order_price_per_dzn, 4,'.','') ; ?></td>
									<td width="80" rowspan="<?=$rowspan; ?>" align="right"><?=number_format($row[csf('cm_cost')], 6,'.','') ; ?></td>
									<td width="80" rowspan="<?=$rowspan; ?>" align="right"><?=fn_number_format(($row[csf('cm_cost')]/12)/$row[csf('sew_smv')],6); ?></td>
									<td width="80" rowspan="<?=$rowspan; ?>" align="right"><?=number_format($row[csf('sew_smv')], 4,'.','') ; ?></td>
									<td width="100" rowspan="<?=$rowspan; ?>" style="word-break:break-all"><?=$internal_ref; ?>&nbsp;</td>
									<td width="80" rowspan="<?=$rowspan; ?>" style="word-break:break-all"><?=$buyer_arr[$row[csf('buyer_name')]]; ?>&nbsp;</td>
									<td width="110" rowspan="<?=$rowspan; ?>" style="word-break:break-all"><?=$dealing_merchant; ?>&nbsp;</td>
									<td width="50" rowspan="<?=$rowspan; ?>" align="center" style="word-break:break-all"><a href="##" onClick="openImgFile('<?=$row[csf('job_no')]; ?>','img');">View</a></td>
									<td width="50" rowspan="<?=$rowspan; ?>" align="center" style="word-break:break-all"><a href="##" onClick="openImgFile('<?=$row[csf('job_no')]; ?>','file');">View</a></td>
									<td width="100" rowspan="<?=$rowspan; ?>" style="word-break:break-all"><?=implode(", ",$fileno); ?></td>
									<td width="170" rowspan="<?=$rowspan; ?>" style="word-break:break-all"><?=$orderIds; ?></td>
									<td width="80" rowspan="<?=$rowspan; ?>" align="center" style="word-break:break-all"><?= change_date_format($jobArr[$row[csf('job_no')]]['min_ship_date']); ?></td>
									<td width="120" rowspan="<?=$rowspan; ?>" style="word-break:break-all" align="center"><?= change_date_format($row[csf('costing_date')]); ?></td>
									<td width="100" rowspan="<?=$rowspan; ?>" style="word-break:break-all" align="center"><?= $row[csf('UPDATE_DATE')]; ?></td>
                  <td width="60" rowspan="<? echo $rowspan; ?>" style="word-break:break-all"><p>
												<a href="javascript:generate_comment_report('<?=$row[csf('id')]; ?>','<?=$row[csf('job_id')]; ?>','po_wise_approval_report')">
												View
												</a>
											</p></td>
								<?
								}
								$approved_no=''; $user_ip='';
								$approval_date=$user_approval_array[$row[csf('id')]][$user_id];
							  	//$approval_date=$row[csf('APPROVED_DATE')];


								$user_ip=$user_ip_array[$row[csf('id')]][$user_id];
								//if($approval_date!="") $approved_no=$approved_no_array[$row[csf('id')]][$user_id][$approval_date];
								if($approval_date!="") $approved_no=$approved_no_array[$row[csf('id')]][$user_id];
								
								$date=''; $time=''; 
								if($approval_date!="") 
								{
									$date=date("d-m-Y",strtotime($approval_date)); 
									$time=date("h:i:s A",strtotime($approval_date)); 
								}
                $duration=datediff('d',$row[csf('costing_date')],$date);
								?>
									<td width="140" title="<?=$user_id; ?>" style="word-break:break-all"><?=$user_name_array[$user_id]['full_name']." [".$user_name_array[$user_id]['name']."];"; ?>&nbsp;</td>
									<td width="130" style="word-break:break-all"><?=$user_name_array[$user_id]['designation']; ?>&nbsp;</td>			
									<td width="50" align="center"><?=$yes_no[$val]; ?></td>

                	<td width="50" align="center"><a href="##" onClick="openApprovedHis('<?=$row[csf('job_id')];?>','<?=$user_id; ?>','user_wise_app_his');"><?=$appStatusArr[$user_app_status_arr[$row[csf('job_id')]][$user_id]*1]; ?></a></td>
											 
									<td width="100" align="center" style="word-break:break-all"><?=$user_ip; ?>&nbsp;</td>
									<td width="100" align="center" style="word-break:break-all"><? if($row[csf('approved')]!=0) echo $date; ?>&nbsp;</td>
									<td width="100" align="center" style="word-break:break-all"><? if($row[csf('approved')]!=0) echo $time; ?>&nbsp;</td>
                  <td width="50" align="center"><? if($row[csf('approved')]!=0)echo $duration;?></td>
									<td align="center" style="word-break:break-all"><a href="##" onClick="openapproved_no('<?=$row[csf('id')];?>','approve_no_popup');"><? if($row[csf('approved')]!=0) echo $approved_no; ?></a></td>
								</tr>
								<?
								$z++;
							}
							$i++;
						}
					}
					?>
                </tbody>
            </table>
        </div>
    </fieldset>      
	<?
	foreach (glob("$user_name*.xls") as $filename) 
	{
		if( @filemtime($filename) < (time()-$seconds_old) )
		@unlink($filename);
	}
	//---------end------------//
	$name=time();
	$filename=$user_name."_".$name.".xls";
	$create_new_doc = fopen($filename, 'w');
	$is_created = fwrite($create_new_doc,ob_get_contents());
	$filename="requires/".$user_name."_".$name.".xls";
	echo "$total_data####$filename";
	exit();
}

if($action=="show_fabric_approval_report")
{
	extract($_REQUEST);
	

	$txt_job_no=$job_no;

//print_r($txt_job_no);die;
	
	?>
    <br>
         <?

		 $lib_designation=return_library_array(" select id,custom_designation from lib_designation","id","custom_designation");

	 $data_array=sql_select("select b.approved_by,b.approved_no, b.approved_date, c.user_full_name,c.designation  from  wo_pre_cost_mst a , approval_history b, user_passwd c where a.id=b.mst_id and b.approved_by=c.id and a.job_no='$txt_job_no' and b.entry_form in(15,46,11) order by b.id asc");
	 
	//  echo "select b.approved_by,b.approved_no, b.approved_date, c.user_full_name,c.designation  from  wo_pre_cost_mst a , approval_history b, user_passwd c where a.id=b.mst_id and b.approved_by=c.id and a.job_no='$txt_job_no' and b.entry_form in(15,46,11) order by b.id asc";die;

 	?>
       <table  width="100%" class="rpt_table"   border="1" cellpadding="0" cellspacing="0" rules="all">
            <thead>
            <tr style="border:1px solid black;">
                <th colspan="4" style="border:1px solid black;">Approval Status</th>
                </tr>
                <tr style="border:1px solid black;">
                <th width="3%" style="border:1px solid black;">Sl</th>
                <th width="50%" style="border:1px solid black;">Name/Designation</th>
                <th width="27%" style="border:1px solid black;">Approval Date</th>
                <th width="20%" style="border:1px solid black;">Approval No</th>
                </tr>
            </thead>
            <tbody>
            <?
			$i=1;
			foreach($data_array as $row){
				if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
			?>
            <tr style="border:1px solid black;" bgcolor="<? echo $bgcolor;?>">
                <td width="3%" style="border:1px solid black;"><? echo $i;?></td>
                <td width="50%" style="border:1px solid black;"><? echo $row[csf('user_full_name')]." / ". $lib_designation[$row[csf('designation')]];?></td>
                <td width="27%" style="border:1px solid black;"><? echo date ("d-m-Y h:i:s",strtotime($row[csf('approved_date')])); //echo change_date_format($row[csf('approved_date')],"dd-mm-yyyy","-");?></td>
                <td width="20%" style="border:1px solid black;"><? echo $row[csf('approved_no')];?></td>
                </tr>
                <?
				$i++;
			}
				?>
            </tbody>
        </table>
    </div>
    <?
	
	 disconnect($con);
	 exit();
}

if($action=="po_wise_approval_report")
{
	extract($_REQUEST);
	

	$txt_job_no=$job_no;

//print_r($txt_job_no);die;
	
	?>
    <br>
  <?

  $poSql = "select ID,PO_NUMBER,JOB_ID from WO_PO_BREAK_DOWN where job_id=$job_id and is_deleted=0 and status_active=1"; 


$poSqlRes = sql_select($poSql);
$po_number_arr = array(); 
  foreach($poSqlRes as $rows){
  $po_number_arr[$rows['ID']] = $rows['PO_NUMBER'];
  }
  
	
 
  $user_lib = return_library_array("select ID,USER_FULL_NAME from USER_PASSWD", 'ID', 'USER_FULL_NAME');
	$commentsSql = "select ID ,MST_ID ,MST_DTLS_ID ,FORM_NAME,TYPE,COMMENTS,INSERTED_BY,INSERT_DATE  FROM  COMMON_COMMENTS_LIBRARY where MST_ID=$job_id and TYPE=1 and FORM_NAME='component_wise_precost_app' order by id desc,INSERTED_BY";

  //echo $commentsSql;die;
	 
  $commentsSqlRes = sql_select($commentsSql);
	?>

	<table border="1" rules="all" cellpadding="0" cellspacing="0" width="100%" class="rpt_table">
		<thead>
			<th>User</th>
			<th>Date</th>
			<th>Po Number</th>
			<th>Comments</th>
		</thead>
		<?
		$i=1;
		foreach($commentsSqlRes as $commentsRow){ 
		
			$bgcolor=($i%2==0)?"#E9F3FF":"#FFFFFF";
		?>
		<tr onclick="<?= $fn;?>" style="cursor:pointer" bgcolor="<?=$bgcolor; ?>">
			<td><?= $user_lib[$commentsRow['INSERTED_BY']];?></td>
			<td><?= $commentsRow['INSERT_DATE'];?></td>
			<td><?= $po_number_arr[$commentsRow['MST_DTLS_ID']];?></td>
			<td><?= $commentsRow['COMMENTS'];?></td>
		</tr>
		<? 
		$i++;
		} 
		?>
	</table>

<?

	 exit();
}


if ($action=='approve_no_popup')
{
	echo load_html_head_contents("Approve Details", "../../../", 1, 1,'','','');
	extract($_REQUEST);
	$sql="SELECT approval_no, approval_cause from fabric_booking_approval_cause where booking_id=$job_id and entry_form=15 and approval_type=2 order by approval_no";
	$sql_res=sql_select($sql);
	?>
	<fieldset style="width:620px;">
        <table width="600" cellspacing="0" cellpadding="0" border="1" rules="all" align="center" class="rpt_table" id="tbl_list">
        	<thead>
        		<tr>
	                <th colspan="3">Un Approve Request</th>               
                </tr>
        		<tr>
	                <th width="50">SL</th>
	                <th width="200">Approve No</th>
	                <th>Un-approve Request Cause</th>
                </tr>
            </thead>
            <tbody>
            	<?
            	$i=1;
            	foreach ($sql_res as $row)
            	{
            		?>
	            	<tr>
	                    <td width="50" align="center"><? echo $i; ?></td>                 
	                    <td width="200" align="center"><? echo $row[csf('approval_no')]; ?></td>
	                    <td><? echo $row[csf('approval_cause')]; ?></td> 
	                </tr>
	                <?
	                $i++;
	            }
	            ?>    
        	</tbody>
       	</table>
	</fieldset>
	<?
	exit();
}


if ($action=='user_wise_app_his')
{
	echo load_html_head_contents("Approve Details", "../../../", 1, 1,'','','');
	extract($_REQUEST);
  $user_arr = return_library_array("SELECT ID,USER_NAME  FROM USER_PASSWD WHERE VALID=1 and id=$user_id", "ID", "USER_NAME");

  $component_his_sql = "select APPROVED_BY,COST_COMPONENT_ID,APPROVED_DATE,CURRENT_APPROVAL_STATUS from CO_COM_PRE_COSTING_APPROVAL where ENTRY_FORM=11 and JOB_ID=$job_id and APPROVED_BY=$user_id";
  // echo $component_his_sql;die;
  // $component_his_sql = "select a.APPROVED_BY,b.COST_COMPONENT_ID,b.APPROVED_DATE,b.CURRENT_APPROVAL_STATUS from PRECOST_COMPONENT_APP_MST a
  // left join CO_COM_PRE_COSTING_APPROVAL b on a.JOB_ID=b.JOB_ID and b.COST_COMPONENT_ID=a.COST_COMPONENT_ID    
  //  where a.ENTRY_FORM=11  and a.APPROVED_BY=$user_id and a.job_id=$job_id";

  //echo $component_his_sql;
  $component_his_sql_res = sql_select($component_his_sql);
  $component_app_his_data_arr=array();
  foreach ($component_his_sql_res as $row) {
    $component_app_his_data_arr[$row['COST_COMPONENT_ID']]=$row;
  }


  $electronicSetupSql = "select DEPARTMENT as COMPONENT_ID,BUYER_ID,USER_ID,BYPASS,SEQUENCE_NO,APPROVED_BY from electronic_approval_setup where company_id = $company_id and entry_form=11 and USER_ID=$user_id and is_deleted=0";
  //echo $electronicSetupSql;
  $electronicSetupSqlResult = sql_select($electronicSetupSql);
  $component_id_arr = explode(',',$electronicSetupSqlResult[0]['COMPONENT_ID']);
  

	?>
	<fieldset style="width:420px;">
        <table width="400" cellspacing="0" cellpadding="0" border="1" rules="all" align="center" class="rpt_table" id="tbl_list">
        	<thead>
        		<tr>
	                <td colspan="4">User:<?=$user_arr[$user_id];?></td>               
                </tr>
        		<tr>
	                <th>SL</th>
	                <th>Component</th>
	                <th>App. Date</th>
	                <th>App. Status</th>
                </tr>
            </thead>
            <tbody>
            	<?
            	$i=1;
             
            	foreach ($component_id_arr as $component_id)
            	{
            		?>
	            	<tr>
	                    <td align="center"><? echo $i; ?></td>                 
	                    <td><?=$cost_components[$component_id]; ?></td>
	                    <td align="center"><?=$component_app_his_data_arr[$component_id]['APPROVED_DATE'];?></td>
	                    <td><?=$appStatusArr[$component_app_his_data_arr[$component_id]['CURRENT_APPROVAL_STATUS']*1];?></td> 
	                </tr>
	                <?
	                $i++;
	            }
	            ?>    
        	</tbody>
       	</table>
	</fieldset>
	<?
	exit();
}



if($action=="img")
{
	echo load_html_head_contents("Image View", "../../../", 1, 1,'','','');
	extract($_REQUEST);
	
?>
	<fieldset style="width:600px; margin-left:5px">
		<div style="width:100%; word-wrap:break-word" id="scroll_body">
             <table border="0" rules="all" width="100%" cellpadding="2" cellspacing="2">
             	<tr>
					<?
					$i=0;
                    $sql="select image_location from common_photo_library where master_tble_id='$job_no' and form_name='knit_order_entry' and file_type=1";
                    $result=sql_select($sql);
                    foreach($result as $row)
                    {
						$i++;
                    ?>
                    	<td align="center"><img width="300px" height="180px" src="../../../<? echo $row[csf('image_location')];?>" /></td>
                    <?
						if($i%2==0) echo "</tr><tr>";
                    }
                    ?>
                </tr>
            </table>
        </div>	
	</fieldset>     
	<?
    exit();
}

if($action=="file")
{
	echo load_html_head_contents("File View", "../../../", 1, 1,'','','');
	extract($_REQUEST);
	
?>
	<fieldset style="width:600px; margin-left:5px">
		<div style="width:100%; word-wrap:break-word" id="scroll_body">
             <table border="0" rules="all" width="100%" cellpadding="2" cellspacing="2">
             	<tr>
					<?
					$i=0;
                    $sql="select image_location from common_photo_library where master_tble_id='$job_no' and form_name='knit_order_entry' and file_type=2";
                    $result=sql_select($sql);
                    foreach($result as $row)
                    {
						$i++;
                    ?>
                    	<td width="100" align="center"><a href="../../../<? echo $row[csf('image_location')]; ?>"><img width="89" height="97" src="../../../file_upload/blank_file.png"><br>File-<? echo $i; ?></a></td>
                    <?
						if($i%6==0) echo "</tr><tr>";
                    }
                    ?>
                </tr>
            </table>
        </div>	
	</fieldset>     
	<?
    exit();
}

if($action=="budgetsheet")
{
	///extract($_REQUEST);
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process )); 
	$txt_costing_date=change_date_format(str_replace("'","",$txt_costing_date),'yyyy-mm-dd','-');
	if($txt_job_no=="") $job_no=''; else $job_no=" and a.job_no='".$txt_job_no."'";
	if($cbo_company_name=="") $company_name=''; else $company_name=" and a.company_name=".$cbo_company_name."";
	if($cbo_buyer_name=="") $cbo_buyer_name=''; else $cbo_buyer_name=" and a.buyer_name=".$cbo_buyer_name."";
	if($txt_style_ref=="") $txt_style_ref=''; else $txt_style_ref=" and a.style_ref_no='".$txt_style_ref."'";
	if($txt_costing_date=="") $txt_costing_date=''; else $txt_costing_date=" and c.costing_date='".$txt_costing_date."'";
	$txt_po_breack_down_id=str_replace("'",'',$txt_po_breack_down_id);
	if(str_replace("'",'',$txt_po_breack_down_id)=="") 
	{
		$txt_po_breack_down_id_cond='';  $txt_po_breack_down_id_cond1='';  $txt_po_breack_down_id_cond2='';  $txt_po_breack_down_id_cond3=''; 
	}
	else
	{
		$txt_po_breack_down_id_cond=" and b.id in(".$txt_po_breack_down_id.")";
		$txt_po_breack_down_id_cond1=" and po_id in(".$txt_po_breack_down_id.")";
		$txt_po_breack_down_id_cond2=" and po_break_down_id in(".$txt_po_breack_down_id.")";
		$txt_po_breack_down_id_cond3=" and b.id in(".$txt_po_breack_down_id.")";
	}
  
	//array for display name
	$buyer_arr=return_library_array( "select id, buyer_name from lib_buyer",'id','buyer_name');
	$sesson_arr=return_library_array( "select id, season_name from lib_buyer_season",'id','season_name');
	$brand_arr=return_library_array( "select id, brand_name from lib_buyer_brand",'id','brand_name');
	$comp=return_library_array( "select id, company_name from lib_company",'id','company_name');
	$color_library=return_library_array( "select id, color_name from lib_color where status_active=1 and is_deleted=0", "id", "color_name");
	$fabric_composition_arr=return_library_array( "select id, fabric_composition_name from lib_fabric_composition",'id','fabric_composition_name');
	//$imge_arr=return_library_array( "select master_tble_id,image_location from   common_photo_library where form_name='company_details' and file_type=1",'master_tble_id','image_location');
	
	$photo_data_array = sql_select("SELECT id,master_tble_id,image_location from common_photo_library where master_tble_id='$txt_job_no' and file_type=1  and rownum=1");
	
	if($db_type==0) $group_gsm="group_concat( distinct b.gsm_weight) AS gsm_weight";
	if($db_type==2) $group_gsm="listagg(b.gsm_weight ,',') within group (order by b.gsm_weight) AS gsm_weight";
  
	$gsm_weight_top=return_field_value("$group_gsm", "lib_body_part a,wo_pre_cost_fabric_cost_dtls_h b", "a.id=b.body_part_id and b.job_no='$txt_job_no' and b.status_active=1 and b.is_deleted=0 and a.body_part_type in(1,20) and b.approved_no=$revised_no and b.approval_page=15","gsm_weight");
	//$gsm_weight_bottom=return_field_value("$group_gsm", "lib_body_part a,wo_pre_cost_fabric_cost_dtls b", "a.id=b.body_part_id and b.job_no=$txt_job_no and a.body_part_type=20 ","gsm_weight");
	//echo $gsm_weight_bottom.'DD';
	$gmtsitem_ratio_array=array(); $grmnt_items = "";
	$grmts_sql = sql_select("select job_no, gmts_item_id, set_item_ratio from wo_po_dtls_item_set_his where job_no='$txt_job_no' and approved_no=$revised_no and approval_page=15");
	//echo "select job_no, gmts_item_id, set_item_ratio from wo_po_dtls_item_set_his where job_no='$txt_job_no' and approved_no=$revised_no and approval_page=15";
	foreach($grmts_sql as $key=>$val)
	{
		$grmnt_items .=$garments_item[$val[csf("gmts_item_id")]].",";
		$gmtsitem_ratio_array[$val[csf('job_no')]][$val[csf('gmts_item_id')]]=$val[csf('set_item_ratio')];
		$set_item_ratio += $val[csf('set_item_ratio')]; 
	}
	$grmnt_items = rtrim($grmnt_items,","); 
  
  $sql = "SELECT a.job_id, a.job_no, a.company_name, a.buyer_name, a.ship_mode, a.total_set_qnty, a.style_ref_no, a.gmts_item_id, a.order_uom, a.avg_unit_price, a.product_dept, a.season_buyer_wise, a.brand_id, a.style_description, a.job_quantity as job_qty, sum(b.plan_cut) as job_quantity, sum(b.po_quantity) as ord_qty, listagg(cast(b.sc_lc as varchar2(4000)),',') within group (order by b.sc_lc) as sc_lc, c.costing_per, c.costing_date, c.budget_minute, c.approved, a.quotation_id, c.exchange_rate, c.incoterm, c.sew_effi_percent, c.remarks, c.sew_smv, '' as refusing_cause, d.fab_knit_fin_req_kg, d.fab_knit_req_kg, d.fab_woven_req_yds, d.fab_woven_fin_req_yds, d.fab_yarn_req_kg
    from wo_po_dtls_mst_his a, wo_po_break_down_his b, wo_pre_cost_mst_histry c left join wo_pre_cost_sum_dtls_histroy d on  c.job_no=d.job_no and d.status_active=1 and d.is_deleted=0 and d.approved_no=$revised_no and d.approved_no=$revised_no and d.approval_page=15
    where a.job_no=b.job_no_mst and b.job_no_mst=c.job_no and a.status_active=1 and a.is_deleted =0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and c.approved_no =$revised_no and c.approved_no = $revised_no 
	and a.approved_no=b.approved_no and b.approved_no=c.approved_no and a.approval_page=15
	and a.approval_page=b.approval_page and b.approval_page=c.approval_page
	
	
	$job_no $txt_po_breack_down_id_cond $company_name $cbo_buyer_name 
	group by a.job_id, a.job_no, a.company_name, a.buyer_name, a.style_ref_no, a.gmts_item_id, a.order_uom, a.ship_mode, a.avg_unit_price, a.product_dept, c.incoterm, c.costing_date, c.exchange_rate, a.quotation_id, c.costing_per, c.sew_effi_percent, c.approved, c.budget_minute, d.fab_knit_req_kg, d.fab_woven_req_yds, d.fab_knit_fin_req_kg, d.fab_woven_fin_req_yds, d.fab_yarn_req_kg, a.job_quantity, a.season_buyer_wise, a.brand_id, a.total_set_qnty, a.style_description, c.remarks, c.sew_smv  order by a.job_no"; //a.job_quantity as job_quantity,
	
 //echo $sql;die;
  $data_array=sql_select($sql);
  $plan_cut_qty=$data_array[0][csf('job_quantity')];
  $total_set_qnty=$data_array[0][csf('total_set_qnty')];
  $exchange_rate=$data_array[0][csf('exchange_rate')];
  $sew_effi_percent=$data_array[0][csf('sew_effi_percent')];
  $sew_smv=$preCost_histry_row[csf('sew_smv')];
  
  $is_approved=return_field_value("approved", "wo_pre_cost_mst", "job_no='$txt_job_no' and  status_active=1 and is_deleted=0"); 
  
	$preCost_histry=sql_select( "SELECT b.margin_dzn_percent as MARGIN_DZN_PERCENT, b.fabric_cost_percent as FABRIC_COST_PERCENT, b.trims_cost_percent as TRIMS_COST_PERCENT, b.embel_cost_percent as EMBEL_COST_PERCENT, b.wash_cost_percent as WASH_COST_PERCENT, b.comm_cost_percent as COMM_COST_PERCENT, b.commission_percent as COMMISSION_PERCENT, b.lab_test_percent as LAB_TEST_PERCENT, b.inspection_percent as INSPECTION_PERCENT, b.cm_cost_percent as CM_COST_PERCENT, b.freight_percent as FREIGHT_PERCENT, b.currier_percent as CURRIER_PERCENT, b.certificate_percent as CERTIFICATE_PERCENT, b.common_oh_percent as COMMON_OH_PERCENT from wo_pre_cost_dtls_histry b where b.job_no='$txt_job_no' and b.approved_no=$revised_no"); 
	
	list($preCost_histry_row)=$preCost_histry;
	$opert_profitloss_percent=$preCost_histry_row[csf('margin_dzn_percent')];
	$fabric_cost_percent=$preCost_histry_row[csf('fabric_cost_percent')];
	$trims_cost_percent=$preCost_histry_row[csf('trims_cost_percent')];
	$embel_cost_percent=$preCost_histry_row[csf('embel_cost_percent')];
	$wash_cost_percent=$preCost_histry_row[csf('wash_cost_percent')];
	$comm_cost_percent=$preCost_histry_row[csf('comm_cost_percent')];
	$commission_percent=$preCost_histry_row[csf('commission_percent')];
	$common_oh_percent=$preCost_histry_row[csf('common_oh_percent')];
	
	$lab_test_percent=$preCost_histry_row[csf('lab_test_percent')];
	$inspection_percent=$preCost_histry_row[csf('inspection_percent')];
	$cm_cost_percent=$preCost_histry_row[csf('cm_cost_percent')];
	$freight_percent=$preCost_histry_row[csf('freight_percent')];
	$currier_percent=$preCost_histry_row[csf('currier_percent')];
	$certificate_percent=$preCost_histry_row[csf('certificate_percent')];
	//$currier_percent=$preCost_histry_row[csf('currier_percent')];
	
	$hissew_effi_percent=$preCost_histry_row[csf('sew_effi_percent')];
	//
	$first_app_date=""; $last_app_date="";
	$preCost_approved=sql_select( "select max(b.approved_no) as approved_no, min(b.approved_date) as first_app_date, max(b.approved_date) as last_app_date,a.id from wo_pre_cost_mst a, approval_history b where a.id=b.mst_id and a.job_no='$txt_job_no' and b.entry_form=15 group by a.id"); 
	//echo  "select max(b.approved_no) as approved_no, min(b.approved_date) as first_app_date, max(b.approved_date) as last_app_date,a.id from wo_pre_cost_mst a, approval_history b where   a.id=b.mst_id and a.job_no=$txt_job_no and b.entry_form=15 group by a.id";
	//echo  "select max(b.approved_no) as approved_no, min(b.approved_date) as first_app_date, max(b.approved_date) as last_app_date,a.id from wo_pre_cost_mst a, approval_history b where b.un_approved_by>0 and  a.id=b.mst_id and a.job_no=$txt_job_no and b.entry_form=15 group by a.id";
	if(count($preCost_approved)>0)
	{
		foreach($preCost_approved as $preCost_approved_row)
		{
			$approved_no_row=$preCost_approved_row[csf('approved_no')];
			$fst_date=$preCost_approved_row[csf('first_app_date')];
			$fstapp_date=$fst_date[0];
			
			$last_date=$preCost_approved_row[csf('last_app_date')];
			$lstapp_date=$last_date[0];
			$precost_id=$preCost_approved_row[csf('id')];
		}
	}
  
	$img_path = (str_replace("'", "", $img_path))? str_replace("'", "", $img_path):'../../';
	//echo $img_path;
	$costing_date=$data_array[0][csf('costing_date')];
	if(is_infinite($costing_date) || is_nan($costing_date)){$costing_date=0;}
	
	$approval_allow=sql_select("select b.id, b.page_id, b.approval_need, b.allow_partial, b.validate_page,a.setup_date from approval_setup_mst a, approval_setup_dtls b 
	where a.id=b.mst_id and a.company_id=$cbo_company_name and a.status_active=1 and b.page_id=15 and b.status_active=1 and b.is_deleted=0 order by b.id desc ");
	$appMsg="";
	if( $is_approved==1) 
	{
		$appMsg="This Budget is Approved.";
		$appcolor="color: green;";
	}
	else if( $is_approved==3)
	{
		if($approval_allow[0][csf("approval_need")]==1 && $approval_allow[0][csf("allow_partial")]==1){
			$appMsg="This Budget is Approved.";
			$appcolor="color: green;";
		}
		else{
			$appMsg="This Budget is Partially Approved.";
			$appcolor="color: green;";
		}
	}
	else
	{
		$appMsg="This Budget is Not Approved.";
		$appcolor="color: red;";
	}
	
	?>
	<div style="width:972px; margin:0 auto; font-family: 'Arial Narrow', Arial, sans-serif;">
        <div style="width:970px; font-size:20px; font-weight:bold">
            <b style="float: left"><?=$comp[str_replace("'","",$cbo_company_name)]; ?><br>Budget Sheet</b>
				<b style="left: 50%; margin-left: 240px; <?=$appcolor; ?>"><?=$appMsg; ?></b>
            <b style="float:right;"><?='Budget Date: ';?><?=date('d-M-y',strtotime($costing_date)); ?> <br><?='Revised No:'.$revised_no; ?>  </b>
        </div>
	
        <div style="width:970px; font-size:18px; font-weight:bold">
            <b style="float: left"></b>
            <b style="float:right; font-size:18px; font-weight:bold">   &nbsp;  </b>
        </div>
        <?
		
		$sqlpo="select a.job_id as JOB_ID, a.approved_no AS APPROVEDNO, a.job_no AS JOB_NO, b.po_id AS POID, b.po_number as PO_NUMBER, b.po_received_date as PO_RECEIVED_DATE, c.item_number_id AS ITEM_NUMBER_ID, c.country_id AS COUNTRY_ID, c.color_number_id AS COLOR_NUMBER_ID, c.size_number_id AS SIZE_NUMBER_ID, c.order_quantity AS ORDER_QUANTITY, c.plan_cut_qnty AS PLAN_CUT_QNTY, c.country_ship_date AS COUNTRY_SHIP_DATE, c.article_number AS ARTICLE_NUMBER, d.costing_per_id AS COSTING_PER from wo_po_dtls_mst_his a, wo_po_break_down_his b, wo_po_color_size_his c, wo_pre_cost_dtls_histry d where a.job_id=b.job_id and b.po_id=c.po_break_down_id and a.job_id=d.job_id and a.status_active=1 and b.is_deleted=0 and b.status_active=1 and c.is_deleted=0 and c.status_active=1 and d.is_deleted=0 and d.status_active=1 and a.approved_no=$revised_no and b.approved_no=$revised_no and c.approved_no=$revised_no and d.approved_no=$revised_no and a.job_no='".$txt_job_no."' order by b.po_received_date DESC";
		//echo $sqlpo; die; //and a.job_no='$job_no'
		$sqlpoRes = sql_select($sqlpo);
		//print_r($sqlpoRes); die;
		$po_arr=array(); $poCountryArr=array(); $reqQtyAmtArr=array(); $costingPerArr=array(); $jobid=""; $jobQtyArr=array();
		foreach($sqlpoRes as $row)
		{
			$costingPerQty=0;
			if($row['COSTING_PER']==1) $costingPerQty=12;
			elseif($row['COSTING_PER']==2) $costingPerQty=1;	
			elseif($row['COSTING_PER']==3) $costingPerQty=24;
			elseif($row['COSTING_PER']==4) $costingPerQty=36;
			elseif($row['COSTING_PER']==5) $costingPerQty=48;
			else $costingPerQty=0;
			
			$costingPerArr[$row['JOB_ID']]=$costingPerQty;
			$jobDataArr[$row['JOB_ID']]['plan']+=$row['PLAN_CUT_QNTY'];
			$jobDataArr[$row['JOB_ID']]['poqty']+=$row['ORDER_QUANTITY'];
			$poArr['pono'][$row['POID']]=$row['PO_NUMBER'];
			$poArr['porecdate'][$row['POID']]=$row['PO_RECEIVED_DATE'];
			$poArr['poshipdate'][$row['POID']]=$row['PO_RECEIVED_DATE'];
			
			
			$po_arr[$row['JOB_ID']][$row['POID']][$row['ITEM_NUMBER_ID']][$row['COLOR_NUMBER_ID']][$row['SIZE_NUMBER_ID']]['poqty']+=$row['ORDER_QUANTITY'];
			$po_arr[$row['JOB_ID']][$row['POID']][$row['ITEM_NUMBER_ID']][$row['COLOR_NUMBER_ID']][$row['SIZE_NUMBER_ID']]['planqty']+=$row['PLAN_CUT_QNTY'];
			
			$po_arr[$row['JOB_ID']][$row['POID']][$row['ITEM_NUMBER_ID']][$row['COLOR_NUMBER_ID']][$row['SIZE_NUMBER_ID']]['county_id'].=$row['COUNTRY_ID'].',';
			
			$poCountryArr[$row['JOB_ID']][$row['POID']][$row['ITEM_NUMBER_ID']][$row['COUNTRY_ID']][$row['COLOR_NUMBER_ID']][$row['SIZE_NUMBER_ID']]['poqty']+=$row['ORDER_QUANTITY'];
			$poCountryArr[$row['JOB_ID']][$row['POID']][$row['ITEM_NUMBER_ID']][$row['COUNTRY_ID']][$row['COLOR_NUMBER_ID']][$row['SIZE_NUMBER_ID']]['planqty']+=$row['PLAN_CUT_QNTY'];
			
			$reqQtyAmtArr[$row['JOB_ID']][$row['POID']]['poqty']+=$row['ORDER_QUANTITY'];
			$reqQtyAmtArr[$row['JOB_ID']][$row['POID']]['planqty']+=$row['PLAN_CUT_QNTY'];
			if($jobid=="") $jobid=$row['JOB_ID']; else $jobid.=','.$row['JOB_ID'];
		}
		unset($sqlpoRes);
		$ujobid=array_unique(explode(",",$jobid));
		$cjobid=count($ujobid);
		$jobIds=implode(",",$ujobid);
		$jobidCond=''; $jobidCondition='';
		if($db_type==2 && $cjobid>1000)
		{
			$jobidCond=" and (";
			$jobidCondition=" and (";
			$jobIdsArr=array_chunk(explode(",",$jobIds),999);
			foreach($jobIdsArr as $ids)
			{
				$ids=implode(",",$ids);
				$jobidCond.=" a.job_id in($ids) or"; 
				$jobidCondition.=" job_id in($ids) or"; 
			}
			$jobidCond=chop($jobidCond,'or ');
			$jobidCond.=")";
			
			$jobidCondition=chop($jobidCondition,'or ');
			$jobidCondition.=")";
		}
		else
		{
			if($jobIds==""){ $jobidCond=""; } else { $jobidCond=" and a.job_id in($jobIds)"; }
			if($jobIds==""){ $jobidCondition=""; } else { $jobidCondition=" and job_id in($jobIds)"; }
		}
		
		$pre_cost_dtls = "SELECT pre_cost_dtls_id as dtls_id, job_id as job_id, job_no, costing_per_id as costing_per, order_uom_id, fabric_cost, fabric_cost_percent, trims_cost, trims_cost_percent, embel_cost, embel_cost_percent, currier_pre_cost, certificate_pre_cost, design_cost, studio_cost, depr_amor_pre_cost, interest_cost, incometax_cost, deffdlc_cost, comm_cost, comm_cost_percent, commission, commission_percent, lab_test, lab_test_percent, inspection, inspection_percent, cm_cost, cm_cost_percent, freight, freight_percent, common_oh, common_oh_percent, total_cost, total_cost_percent, price_dzn, price_dzn_percent, margin_dzn, margin_dzn_percent, price_pcs_or_set, price_pcs_or_set_percent, margin_pcs_set, margin_pcs_set_percent, cm_for_sipment_sche from wo_pre_cost_dtls_histry where job_no='".$txt_job_no."' and status_active=1 and is_deleted=0 and approved_no=$revised_no"; 
		$pre_cost_dtls_arr=sql_select($pre_cost_dtls);
		foreach ($pre_cost_dtls_arr as $row) {
			if($row[csf("costing_per")]==1){$order_price_per_dzn=12;$costing_for="1 DZN";}
			else if($row[csf("costing_per")]==2){$order_price_per_dzn=1;$costing_for="1 PCS";}
			else if($row[csf("costing_per")]==3){$order_price_per_dzn=24;$costing_for="2 DZN";}
			else if($row[csf("costing_per")]==4){$order_price_per_dzn=36;$costing_for="3 DZN";}
			else if($row[csf("costing_per")]==5){$order_price_per_dzn=48;$costing_for="4 DZN";}
			else {$order_price_per_dzn=0; $costing_for="DZN";}
			$job_id=$row[csf("job_id")];
			$planqty=$jobDataArr[$job_id]['plan'];
			$poQty=$jobDataArr[$job_id]['poqty'];
			
			if( ($row[csf("lab_test")]*1)!=0) $labAmt=($row[csf("lab_test")]/$order_price_per_dzn)*$poQty;
			if( ($row[csf("currier_pre_cost")]*1)!=0) $currierAmt=($row[csf("currier_pre_cost")]/$order_price_per_dzn)*$poQty;
			if( ($row[csf("inspection")]*1)!=0) $inspectionAmt=($row[csf("inspection")]/$order_price_per_dzn)*$poQty;
			if( ($row[csf("commission")]*1)!=0) $commissionAmt=($row[csf("commission")]/$order_price_per_dzn)*$poQty;
			if( ($row[csf("comm_cost")]*1)!=0) $commlAmt=($row[csf("comm_cost")]/$order_price_per_dzn)*$poQty;
			if( ($row[csf("freight")]*1)!=0) $freightAmt=($row[csf("freight")]/$order_price_per_dzn)*$poQty;
			if( ($row[csf("certificate_pre_cost")]*1)!=0) $certificateAmt=($row[csf("certificate_pre_cost")]/$order_price_per_dzn)*$poQty;
			if( ($row[csf("deffdlc_cost")]*1)!=0) $deffdlcAmt=($row[csf("deffdlc_cost")]/$order_price_per_dzn)*$poQty;
			if( ($row[csf("design_cost")]*1)!=0) $designAmt=($row[csf("design_cost")]/$order_price_per_dzn)*$poQty;
			if( ($row[csf("studio_cost")]*1)!=0) $studioAmt=($row[csf("studio_cost")]/$order_price_per_dzn)*$poQty;
			if( ($row[csf("depr_amor_pre_cost")]*1)!=0) $deprAmt=($row[csf("depr_amor_pre_cost")]/$order_price_per_dzn)*$poQty;
			if( ($row[csf("common_oh")]*1)!=0) $commonOhAmt=($row[csf("common_oh")]/$order_price_per_dzn)*$poQty;
			if( ($row[csf("interest_cost")]*1)!=0) $interestAmt=($row[csf("interest_cost")]/$order_price_per_dzn)*$poQty;
			if( ($row[csf("incometax_cost")]*1)!=0) $incometaxAmt=($row[csf("incometax_cost")]/$order_price_per_dzn)*$poQty;
			
			if( ($row[csf("cm_cost")]*1)!=0) $cmAmt=($row[csf("cm_cost")]/$order_price_per_dzn)*$poQty;
			
			$other_costing_arr[$job_id]['comm_cost']=$commlAmt;
			$other_costing_arr[$job_id]['commission']=$commissionAmt;
			$other_costing_arr[$job_id]['inspection']=$inspectionAmt;
			$other_costing_arr[$job_id]['freight']=$freightAmt;
			$other_costing_arr[$job_id]['certificate_pre_cost']=$certificateAmt;
			$other_costing_arr[$job_id]['deffdlc_cost']=$deffdlcAmt;
			$other_costing_arr[$job_id]['design_cost']=$designAmt;
			$other_costing_arr[$job_id]['studio_cost']=$studioAmt;
			$other_costing_arr[$job_id]['common_oh']=$commonOhAmt;
			$other_costing_arr[$job_id]['interest_cost']=$interestAmt;
			$other_costing_arr[$job_id]['incometax_cost']=$incometaxAmt;
			$other_costing_arr[$job_id]['depr_amor_pre_cost']=$deprAmt;
			$other_costing_arr[$job_id]['cm_cost']=$cmAmt;
			$other_costing_arr[$job_id]['lab_test']=$labAmt;
			
			
			$total_cost = $row[csf("total_cost")];
			$price_dzn = $row[csf("price_dzn")];
		}
		
		$gmtsitemRatioSql="select approved_no as APPROVENO, job_id AS JOB_ID, gmts_item_id AS GMTS_ITEM_ID, set_item_ratio AS SET_ITEM_RATIO, smv_pcs as SMV_PCS from wo_po_dtls_item_set_his where 1=1 and approved_no=$revised_no $jobCondS $jobidCondition";
		//echo $gmtsitemRatioSql; die;
		$gmtsitemRatioSqlRes = sql_select($gmtsitemRatioSql);
		$jobItemRatioArr=array();
		foreach($gmtsitemRatioSqlRes as $row)
		{
			$jobItemRatioArr[$row['JOB_ID']][$row['GMTS_ITEM_ID']]=$row['SET_ITEM_RATIO'];
			$jobDataArr[$row['JOB_ID']]['smv']=$row['SMV_PCS'];
		}
		unset($gmtsitemRatioSqlRes);
		
		//Contrast Details
		$sqlContrast="select a.approved_no as APPROVENO, a.job_id AS JOB_ID, a.pre_cost_fabric_cost_dtls_id as PRECOSTID, a.gmts_color_id as COLOR_NUMBER_ID, a.contrast_color_id AS CONTRAST_COLOR_ID from wo_pre_fab_concolor_dtls_h a where 1=1 and a.approved_no=$revised_no and a.status_active=1 and a.is_deleted=0 $jobCond $jobidCond";
		//echo $sqlContrast; die;
		$sqlContrastRes = sql_select($sqlContrast);
		$sqlContrastArr=array();
		foreach($sqlContrastRes as $row)
		{
			$sqlContrastArr[$row['JOB_ID']][$row['PRECOSTID']][$row['COLOR_NUMBER_ID']]=$row['CONTRAST_COLOR_ID'];
		}
		unset($sqlContrastRes);
		
		//Stripe Details
		$sqlStripe="select a.approved_no as APPROVENO, a.job_id AS JOB_ID, a.pre_cost_fabric_cost_dtls_id as PRECOSTID, a.po_break_down_id as POID, a.item_number_id AS ITEM_NUMBER_ID, a.color_number_id as COLOR_NUMBER_ID, a.stripe_color as STRIPE_COLOR, a.size_number_id as SIZE_NUMBER_ID, a.fabreq as FABREQ, a.yarn_dyed as YARN_DYED from wo_pre_stripe_color_h a where 1=1 and a.status_active=1 and a.is_deleted=0 and a.approved_no=$revised_no $jobCond $jobidCond";
		//echo $sqlStripe; die;
		$sqlStripeRes = sql_select($sqlStripe);
		$sqlStripeArr=array();
		foreach($sqlStripeRes as $row)
		{
			$sqlStripeArr[$row['JOB_ID']][$row['PRECOSTID']][$row['COLOR_NUMBER_ID']]['strip'][$row['STRIPE_COLOR']]=$row['STRIPE_COLOR'];
			$sqlStripeArr[$row['JOB_ID']][$row['PRECOSTID']][$row['COLOR_NUMBER_ID']]['fabreq'][$row['STRIPE_COLOR']]=$row['FABREQ'];
		}
		unset($sqlStripeRes);
		
		
		//Fabric Details
		$sqlfab="select a.approved_no as APPROVENO, a.job_id AS JOB_ID, a.pre_cost_fabric_cost_dtls_id AS FABID, a.item_number_id AS ITEM_NUMBER_ID, a.fab_nature_id AS FAB_NATURE_ID, a.color_type_id AS COLOR_TYPE_ID, a.fabric_source as FABRIC_SOURCE, a.color_size_sensitive AS COLOR_SIZE_SENSITIVE, a.construction AS CONSTRUCTION, a.fabric_description as FABRIC_DESCRIPTION, a.gsm_weight AS GSM_WEIGHT, a.uom AS UOM, a.budget_on as BUDGET_ON, b.po_break_down_id AS POID, b.color_number_id AS COLOR_NUMBER_ID, b.gmts_sizes AS SIZE_NUMBER_ID, b.cons AS CONS, b.requirment AS REQUIRMENT, b.rate as RATE, b.amount AS AMOUNT
		from wo_pre_cost_fabric_cost_dtls_h a, wo_pre_fab_avg_con_dtls_h b
		where 1=1 and a.pre_cost_fabric_cost_dtls_id=b.pre_cost_fabric_cost_dtls_id and b.cons!=0 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.approved_no=b.approved_no and a.approved_no=$revised_no $jobCond $jobidCond";
		//echo $sqlfab; die;
		$sqlfabRes = sql_select($sqlfab);
		$fabIdWiseGmtsDataArr=array();
		foreach($sqlfabRes as $row)
		{
			$poQty=$planQty=$costingPer=$itemRatio=$finReq=$greyReq=$finAmt=$greyAmt=0;
			
			$fabIdWiseGmtsDataArr[$row['FABID']]['item']=$row['ITEM_NUMBER_ID'];
			$fabIdWiseGmtsDataArr[$row['FABID']]['fnature']=$row['FAB_NATURE_ID'];
			$fabIdWiseGmtsDataArr[$row['FABID']]['sensitive']=$row['COLOR_SIZE_SENSITIVE'];
			$fabIdWiseGmtsDataArr[$row['FABID']]['color_type']=$row['COLOR_TYPE_ID'];
			$fabIdWiseGmtsDataArr[$row['FABID']]['uom']=$row['UOM'];
			$fabIdWiseGmtsDataArr[$row['FABID']]['budget_on']=$row['BUDGET_ON'];
			
			$poQty=$po_arr[$row['JOB_ID']][$row['POID']][$row['ITEM_NUMBER_ID']][$row['COLOR_NUMBER_ID']][$row['SIZE_NUMBER_ID']]['poqty'];
			$planQty=$po_arr[$row['JOB_ID']][$row['POID']][$row['ITEM_NUMBER_ID']][$row['COLOR_NUMBER_ID']][$row['SIZE_NUMBER_ID']]['planqty'];
			$costingPer=$costingPerArr[$row['JOB_ID']];
			$itemRatio=$jobItemRatioArr[$row['JOB_ID']][$row['ITEM_NUMBER_ID']];
			if($row['BUDGET_ON']==1) $poPlanQty=$poQty; else $poPlanQty=$planQty;
			
			$finReq=($poPlanQty/$itemRatio)*($row['CONS']/$costingPer);
			$greyReq=($poPlanQty/$itemRatio)*($row['REQUIRMENT']/$costingPer);
			
			$finAmt=$finReq*$row['RATE'];
			$greyAmt=$greyReq*$row['RATE'];
			
			//echo $planQty.'='.$itemRatio.'='.$row['CONS'].'='.$row['REQUIRMENT'].'='.$costingPer.'='.$finReq.'='.$greyReq.'<br>';
			$fabQtyAmtArr[$row['JOB_ID']]['fabric']=$row['FABRIC_DESCRIPTION'];
			$fabQtyAmtArr[$row['JOB_ID']]['uom']=$row['UOM'];
			
			$fabQtyAmtArr[$row['JOB_ID']]['qty']+=$greyReq;
			$fabQtyAmtArr[$row['JOB_ID']]['amt']+=$greyAmt;
			$fabQtyAmtArr[$row['JOB_ID']]['dzn']=$row['AMOUNT'];
			$fabQtyAmtArr[$row['JOB_ID']]['rate']=$row['RATE'];
			
			if($row['FABRIC_SOURCE']==2)
			{
				$fabQtyAmtArr[$row['JOB_ID']]['purqty']+=$greyReq;
				$fabQtyAmtArr[$row['JOB_ID']]['puramt']+=$greyAmt;	
			}
			else
			{
				$fabQtyAmtArr[$row['JOB_ID']]['prodqty']+=$greyReq;
				$fabQtyAmtArr[$row['JOB_ID']]['prodamt']+=$greyAmt;
			}
			
			if($row['FAB_NATURE_ID']==2)
			{
				$fabric_qty_arr['knit']['finish'][$row['FABID']][$row['UOM']]+=$finReq;
				$fabric_qty_arr['knit']['grey'][$row['FABID']][$row['UOM']]+=$greyReq;
				$fabric_amount_arr['knit']['grey'][$row['FABID']][$row['UOM']]+=$greyAmt;
			}
			if($row['FAB_NATURE_ID']==3)
			{
				$fabric_qty_arr['woven']['finish'][$row['FABID']][$row['UOM']]+=$finReq;
				$fabric_qty_arr['woven']['grey'][$row['FABID']][$row['UOM']]+=$greyReq;
				$fabric_amount_arr['woven']['grey'][$row['FABID']][$row['UOM']]+=$greyAmt;
			}
		}
		unset($sqlfabRes);
		//print_r($fabQtyAmtArr[27617]['puramt']); die; 
		
		//Yarn Details
		$sqlYarn="select a.job_id AS JOB_ID, a.pre_cost_fabric_cost_dtls_id as PRECOSTID, a.po_break_down_id as POID, a.color_number_id as COLOR_NUMBER_ID, a.gmts_sizes as SIZE_NUMBER_ID, a.cons AS CONS, a.requirment AS REQUIRMENT, b.pre_cost_fab_yarn_cost_dtls_id AS YARN_ID, b.count_id AS COUNT_ID, b.copm_one_id AS COPM_ONE_ID, b.percent_one AS PERCENT_ONE, b.type_id AS TYPE_ID, b.color AS COLOR, b.cons_ratio AS CONS_RATIO, b.cons_qnty AS CONS_QNTY, b.avg_cons_qnty AS AVG_CONS_QNTY, b.rate AS RATE, b.amount AS AMOUNT 
		
		from wo_pre_fab_avg_con_dtls_h a, wo_pre_cost_fab_yarn_cst_dtl_h b where 1=1 and a.job_id=b.job_id and a.pre_cost_fabric_cost_dtls_id=b.fabric_cost_dtls_id and a.cons!=0 and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 and a.approved_no=b.approved_no and a.approved_no=$revised_no $jobCond $jobidCond";
		//echo $sqlYarn;
		$sqlYarnRes = sql_select($sqlYarn);
		foreach($sqlYarnRes as $row)
		{
			$poQty=$planQty=$costingPer=$itemRatio=$consQnty=$yarnReq=$yarnAmt=0;
			
			$gmtsItem=$fabIdWiseGmtsDataArr[$row['PRECOSTID']]['item'];
			
			$poQty=$po_arr[$row['JOB_ID']][$row['POID']][$gmtsItem][$row['COLOR_NUMBER_ID']][$row['SIZE_NUMBER_ID']]['poqty'];
			$planQty=$po_arr[$row['JOB_ID']][$row['POID']][$gmtsItem][$row['COLOR_NUMBER_ID']][$row['SIZE_NUMBER_ID']]['planqty'];
			$costingPer=$costingPerArr[$row['JOB_ID']];
			$itemRatio=$jobItemRatioArr[$row['JOB_ID']][$gmtsItem];
			
			$consQnty=($row['REQUIRMENT']*$row['CONS_RATIO'])/100;
			
			$yarnReq=($planQty/$itemRatio)*($consQnty/$costingPer);
			
			$yarnAmt=$yarnReq*$row['RATE'];
			
			//echo $planQty.'='.$itemRatio.'='.$row['REQUIRMENT'].'='.$costingPer.'='.$yarnReq.'='.$yarnAmt.'<br>';
			$yarnQtyAmtArr[$row['JOB_ID']]['yarn_qty']+=$yarnReq;
			$yarnQtyAmtArr[$row['JOB_ID']]['yarn_amt']+=$yarnAmt;
			$yarnDataWithFabricidArr[$row['PRECOSTID']]['amount']+=$yarnAmt;
			$yarnDataWithFabricidArr[$row['PRECOSTID']]['qty']+=$yarnReq;
			
			$yarn_data_array[$row['COUNT_ID']][$row['COPM_ONE_ID']][$row['PERCENT_ONE']][$row['TYPE_ID']][$row['COLOR']][$row['RATE']]['qty']+=$yarnReq;
        	$yarn_data_array[$row['COUNT_ID']][$row['COPM_ONE_ID']][$row['PERCENT_ONE']][$row['TYPE_ID']][$row['COLOR']][$row['RATE']]['amount']+=$yarnAmt;
		}
		unset($sqlYarnRes); 
		//print_r($reqQtyAmtArr); die;
		
		//Convaersion Details
		$sqlConv="select a.job_id AS JOB_ID, a.pre_cost_fabric_cost_dtls_id AS PRECOSTID, a.po_break_down_id as POID, a.color_number_id as COLOR_NUMBER_ID, a.gmts_sizes as SIZE_NUMBER_ID, a.dia_width AS DIA_WIDTH, a.cons AS CONS, a.requirment AS REQUIRMENT, b.pre_cost_fab_conv_cst_dtls_id AS CONVERTION_ID, b.cons_process AS CONS_PROCESS, b.req_qnty AS REQ_QNTY, b.process_loss AS PROCESS_LOSS, b.avg_req_qnty AS AVG_REQ_QNTY, b.charge_unit AS CHARGE_UNIT, b.amount as AMOUNT, b.color_break_down AS COLOR_BREAK_DOWN
		from wo_pre_fab_avg_con_dtls_h a, wo_pre_cost_fab_con_cst_dtls_h b where 1=1 and a.pre_cost_fabric_cost_dtls_id=b.fabric_description and a.cons!=0 and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 and a.approved_no=b.approved_no and a.approved_no=$revised_no $jobCond $jobidCond";
		//echo $sqlConv; die;
		$sqlConvRes = sql_select($sqlConv);
		$convConsRateArr=array();
		foreach($sqlConvRes as $row)
		{
			$id=$row['CONVERTION_ID'];
			$colorBreakDown=$row['COLOR_BREAK_DOWN'];
			if($colorBreakDown !="")
			{
				$arr_1=explode("__",$colorBreakDown);
				for($ci=0;$ci<count($arr_1);$ci++)
				{
					$arr_2=explode("_",$arr_1[$ci]);
					$convConsRateArr[$id][$arr_2[0]][$arr_2[3]]['rate']=$arr_2[1];
					$convConsRateArr[$id][$arr_2[0]][$arr_2[3]]['cons']=$arr_2[4];
				}
			}
		}
		//echo "ff"; die;
		foreach($sqlConvRes as $row)
		{
			$poQty=$planQty=$costingPer=$itemRatio=$consQnty=$reqqnty=$convAmt=0;
			$gmtsItem=$fabIdWiseGmtsDataArr[$row['PRECOSTID']]['item'];
			
			$poQty=$po_arr[$row['JOB_ID']][$row['POID']][$gmtsItem][$row['COLOR_NUMBER_ID']][$row['SIZE_NUMBER_ID']]['poqty'];
			$planQty=$po_arr[$row['JOB_ID']][$row['POID']][$gmtsItem][$row['COLOR_NUMBER_ID']][$row['SIZE_NUMBER_ID']]['planqty'];
			
			$costingPer=$costingPerArr[$row['JOB_ID']];
			$itemRatio=$jobItemRatioArr[$row['JOB_ID']][$gmtsItem];
			
			$colorTypeId=$fabIdWiseGmtsDataArr[$row['PRECOSTID']]['color_type']; 
			$colorSizeSensitive=$fabIdWiseGmtsDataArr[$row['PRECOSTID']]['sensitive'];
			$budget_on=$fabIdWiseGmtsDataArr[$row['PRECOSTID']]['budget_on'];
			if($budget_on==1) $poPlanQty=$poQty; else $poPlanQty=$planQty;
			
			$consProcessId=$row['CONS_PROCESS'];
			$stripe_color=$sqlStripeArr[$row['JOB_ID']][$row['PRECOSTID']][$row['COLOR_NUMBER_ID']]['strip'];
			
			if(($colorTypeId==2 || $colorTypeId==3 || $colorTypeId==4 || $colorTypeId==6 || $colorTypeId==31 || $colorTypeId==32 || $colorTypeId==33 || $colorTypeId==34) && $consProcessId==30 && count($stripe_color)>0)
			{
				$qnty=0; $convrate=0;
				foreach($stripe_color as $stripe_color_id)
				{
					$stripe_color_cons_dzn=$convConsRateArr[$row['CONVERTION_ID']][$row['COLOR_NUMBER_ID']][$stripe_color_id]['cons'];
					$convrate=$convConsRateArr[$row['CONVERTION_ID']][$row['COLOR_NUMBER_ID']][$stripe_color_id]['rate'];
					
					$requirment=$stripe_color_cons_dzn-($stripe_color_cons_dzn*$row['PROCESS_LOSS'])/100;
					$qnty=($poPlanQty/$itemRatio)*($requirment/$costingPer);
					//echo $convrate.'=';
					if($convrate>0){
						$reqqnty+=$qnty;
						$convAmt+=$qnty*$convrate;
					}
				}
			}
			else
			{
				$convrate=$requirment=$reqqnty=0;
				$rateColorId=$row['COLOR_NUMBER_ID'];
				if($colorSizeSensitive==3) $rateColorId=$sqlContrastArr[$row['JOB_ID']][$row['PRECOSTID']][$row['COLOR_NUMBER_ID']]; else $rateColorId=$row['COLOR_NUMBER_ID'];
		
				if($row['COLOR_BREAK_DOWN']!="")
				{
					$convDtlsRate=$convConsRateArr[$row['CONVERTION_ID']][$row['COLOR_NUMBER_ID']][$rateColorId]['rate']; 
					if($convDtlsRate>0) $convrate=$convDtlsRate; else $convrate=$row['CHARGE_UNIT']; 
				}else $convrate=$row['CHARGE_UNIT']; 
				
				//echo $row['CHARGE_UNIT'].'='.$row['CONVERTION_ID'].'=';
				if($convrate>0){
					$requirment=$row['REQUIRMENT']-($row['REQUIRMENT']*($row['PROCESS_LOSS']*1))/100;
					$qnty=($poPlanQty/$itemRatio)*($row['REQUIRMENT']/$costingPer);
					$reqqnty+=$qnty;
					$convAmt+=$qnty*$convrate;
				}
			}
			
			//echo $planQty.'='.$itemRatio.'='.$row['REQUIRMENT'].'='.$costingPer.'='.$yarnReq.'='.$yarnAmt.'<br>';
			$convQtyAmtArr[$row['JOB_ID']]['conv_qty'][$consProcessId]+=$reqqnty;
			$convQtyAmtArr[$row['JOB_ID']]['conv_amt'][$consProcessId]+=$convAmt;
			
			$con_amount_fabric_process[$row['PRECOSTID']][$consProcessId]['conv_amt']+=$convAmt;
        	$con_qty_fabric_process[$row['PRECOSTID']][$consProcessId]['conv_qty']+=$reqqnty;
		}
		unset($sqlConvRes);
		//echo "kauar"; 
		//print_r($convQtyAmtArr); die;
		
		//Trims Details
		$sqlTrim="select a.job_id AS JOB_ID, a.pre_cost_trim_cost_dtls_id AS TRIMID, a.trim_group AS TRIM_GROUP, a.description AS DESCRIPTION, a.cons_uom AS CONS_UOM, a.cons_dzn_gmts CONS_DZN_GMTS, a.rate AS RATEMST, a.amount AS AMOUNT, b.po_break_down_id as POID, b.item_number_id as ITEM_NUMBER_ID, b.color_number_id as COLOR_NUMBER_ID, b.size_number_id as SIZE_NUMBER_ID, b.cons AS CONS, b.tot_cons AS TOT_CONS, b.rate AS RATE, b.country_id AS COUNTRY_ID_TRIMS, b.color_size_table_id as COLOR_SIZE_ID
		from wo_pre_cost_trim_cost_dtls_his a, wo_pre_cost_trim_co_cons_dtl_h b
		where 1=1 and a.pre_cost_trim_cost_dtls_id=b.wo_pre_cost_trim_cost_dtls_id and b.cons>0 and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 and a.approved_no=b.approved_no and a.approved_no=$revised_no and b.approved_no=$revised_no $jobCond $jobidCond";
		//echo $sqlTrim; die;
		$sqlTrimRes = sql_select($sqlTrim);
		
		foreach($sqlTrimRes as $row)
		{
			$poQty=$planQty=$costingPer=$itemRatio=$consQnty=$consTotQnty=$consAmt=$consTotAmt=0;
			
			$costingPer=$costingPerArr[$row['JOB_ID']];
			$itemRatio=$jobItemRatioArr[$row['JOB_ID']][$row['ITEM_NUMBER_ID']];
			
			$poCountryId=array_filter(array_unique(explode(",",$po_arr[$row['JOB_ID']][$row['POID']][$row['ITEM_NUMBER_ID']][$row['COLOR_NUMBER_ID']][$row['SIZE_NUMBER_ID']]['county_id'])));
			//print_r($poCountryId);
			
			if($row['COUNTRY_ID_TRIMS']=="" || $row['COUNTRY_ID_TRIMS']==0)
			{
				$poQty=$po_arr[$row['JOB_ID']][$row['POID']][$row['ITEM_NUMBER_ID']][$row['COLOR_NUMBER_ID']][$row['SIZE_NUMBER_ID']]['poqty'];
				$planQty=$po_arr[$row['JOB_ID']][$row['POID']][$row['ITEM_NUMBER_ID']][$row['COLOR_NUMBER_ID']][$row['SIZE_NUMBER_ID']]['planqty'];
				
				$consQnty=($poQty/$itemRatio)*($row['CONS']/$costingPer);
				$consTotQnty=($poQty/$itemRatio)*($row['TOT_CONS']/$costingPer);
				
				$consAmt=$consQnty*$row['RATE'];
				$consTotAmt=$consTotQnty*$row['RATE'];
			}
			else
			{
				$countryIdArr=explode(",",$row['COUNTRY_ID_TRIMS']);
				$consQnty=$consTotQnty=$consAmt=$consTotAmt=0;
				foreach($poCountryId as $countryId)
				{
					if(in_array($countryId, $countryIdArr))
					{
						$poQty=$poCountryArr[$row['JOB_ID']][$row['POID']][$row['ITEM_NUMBER_ID']][$countryId][$row['COLOR_NUMBER_ID']][$row['SIZE_NUMBER_ID']]['poqty'];
						$planQty=$poCountryArr[$row['JOB_ID']][$row['POID']][$row['ITEM_NUMBER_ID']][$countryId][$row['COLOR_NUMBER_ID']][$row['SIZE_NUMBER_ID']]['planqty'];
						$consQty=$consTotQty=0;
						
						$consQty=($poQty/$itemRatio)*($row['CONS']/$costingPer);
						$consTotQty=($poQty/$itemRatio)*($row['TOT_CONS']/$costingPer);
						
						$consQnty+=$consQty;
						$consTotQnty+=$consTotQty;
						//echo $poQty.'-'.$itemRatio.'-'.$row['CONS'].'-'.$costingPer.'<br>';
						$consAmt+=$consQty*$row['RATE'];
						$consTotAmt+=$consTotQty*$row['RATE'];
					}
				}
			}
			
			//echo $planQty.'='.$itemRatio.'='.$row['REQUIRMENT'].'='.$costingPer.'='.$yarnReq.'='.$yarnAmt.'<br>';
			//$reqQtyAmtArr[$row['JOB_ID']][$row['POID']]['trimqty']+=$consQnty;
			$trimQtyAmtArr[$row['JOB_ID']]['trimtotqty']+=$consQnty;
			
			//$reqQtyAmtArr[$row['JOB_ID']][$row['POID']]['trimamt']+=$consAmt;
			$trimQtyAmtArr[$row['JOB_ID']]['trimtotamt']+=$consAmt;
			$trim_qty_arr[$row['TRIMID']]+=$consQnty;
			$trim_amount_arr[$row['TRIMID']]+=$consAmt;
		}
		unset($sqlTrimRes); 
		//print_r($reqQtyAmtArr); die;
		
		$sqlEmb="select a.job_id AS JOB_ID, a.pre_cost_embe_cost_dtls_id AS EMB_ID, a.emb_name AS EMB_NAME, a.emb_type AS EMB_TYPE, a.cons_dzn_gmts AS CONS_DZN_GMTS_MST, a.rate AS RATE_MST, a.amount AS AMOUNT_MST, a.budget_on AS BUDGET_ON, b.po_break_down_id as POID, b.item_number_id as ITEM_NUMBER_ID, b.color_number_id as COLOR_NUMBER_ID, b.size_number_id as SIZE_NUMBER_ID, b.requirment AS CONS_DZN_GMTS, b.rate AS RATE, b.amount AS AMOUNT, b.country_id AS COUNTRY_ID_EMB 
	from wo_pre_cost_embe_cost_dtls_his a, wo_pre_emb_avg_con_dtls_h b 
	where 1=1 and a.cons_dzn_gmts>0 and b.requirment>0 and
	a.job_id=b.job_id and a.pre_cost_embe_cost_dtls_id=b.pre_cost_emb_cost_dtls_id and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 and a.approved_no=b.approved_no and a.approved_no=$revised_no and a.approval_page=15 $jobCond $jobidCond";
		//echo $sqlEmb; die;
		$sqlEmbRes = sql_select($sqlEmb);
		
		foreach($sqlEmbRes as $row)
		{
			$poQty=$planQty=$costingPer=$itemRatio=$consQnty=$consTotQnty=$consAmt=$consTotAmt=0;
			
			$costingPer=$costingPerArr[$row['JOB_ID']];
			$itemRatio=$jobItemRatioArr[$row['JOB_ID']][$row['ITEM_NUMBER_ID']];
			$budget_on=$row['BUDGET_ON'];
			
			$poCountryId=array_filter(array_unique(explode(",",$po_arr[$row['JOB_ID']][$row['POID']][$row['ITEM_NUMBER_ID']][$row['COLOR_NUMBER_ID']][$row['SIZE_NUMBER_ID']]['county_id'])));
			//print_r($poCountryId);
			$calPoPlanQty=0;
			
			if($row['COUNTRY_ID_EMB']=="" || $row['COUNTRY_ID_EMB']==0)
			{
				$poQty=$po_arr[$row['JOB_ID']][$row['POID']][$row['ITEM_NUMBER_ID']][$row['COLOR_NUMBER_ID']][$row['SIZE_NUMBER_ID']]['poqty'];
				$planQty=$po_arr[$row['JOB_ID']][$row['POID']][$row['ITEM_NUMBER_ID']][$row['COLOR_NUMBER_ID']][$row['SIZE_NUMBER_ID']]['planqty'];
				
				if($budget_on==1) $calPoPlanQty=$poQty; else $calPoPlanQty=$planQty;
				$consQty=0;
				$consQty=($calPoPlanQty/$itemRatio)*($row['CONS_DZN_GMTS']/$costingPer);
				$consQnty+=$consQty;
				
				$consAmt=$consQty*$row['RATE'];
			}
			else
			{
				$countryIdArr=explode(",",$row['COUNTRY_ID_EMB']);
				$consQnty=$consAmt=0;
				foreach($poCountryId as $countryId)
				{
					if(in_array($countryId, $countryIdArr))
					{
						$poQty=$poCountryArr[$row['JOB_ID']][$row['POID']][$row['ITEM_NUMBER_ID']][$countryId][$row['COLOR_NUMBER_ID']][$row['SIZE_NUMBER_ID']]['poqty'];
						$planQty=$poCountryArr[$row['JOB_ID']][$row['POID']][$row['ITEM_NUMBER_ID']][$countryId][$row['COLOR_NUMBER_ID']][$row['SIZE_NUMBER_ID']]['planqty'];
						
						if($budget_on==1) $calPoPlanQty=$poQty; else $calPoPlanQty=$planQty;
						$consQty=0;
						$consQty=($calPoPlanQty/$itemRatio)*($row['CONS_DZN_GMTS']/$costingPer);
						$consQnty+=$consQty;
						//echo $poQty.'-'.$itemRatio.'-'.$row['CONS_DZN_GMTS'].'-'.$costingPer.'<br>';
						$consAmt+=$consQty*$row['RATE'];
					}
				}
			}
			
			//echo $planQty.'='.$itemRatio.'='.$row['CONS_DZN_GMTS'].'='.$costingPer.'='.$consQty.'='.$consAmt.'<br>';
			$embQtyAmtArr[$row['JOB_ID']][$row['EMB_NAME']]['qty']+=$consQnty;
			$embQtyAmtArr[$row['JOB_ID']][$row['EMB_NAME']]['amt']+=$consAmt;
			/*if($row['EMB_NAME']==1)
			{
				$reqQtyAmtArr[$row['JOB_ID']]['print_qty']+=$consQnty;
				$reqQtyAmtArr[$row['JOB_ID']]['print_amt']+=$consAmt;
			}
			else if($row['EMB_NAME']==2)
			{
				$reqQtyAmtArr[$row['JOB_ID']]['embqty']+=$consQnty;
				$reqQtyAmtArr[$row['JOB_ID']]['embamt']+=$consAmt;
			}
			else if($row['EMB_NAME']==3)
			{
				$reqQtyAmtArr[$row['JOB_ID']]['washqty']+=$consQnty;
				$reqQtyAmtArr[$row['JOB_ID']]['washamt']+=$consAmt;
			}
			else if($row['EMB_NAME']==4)
			{
				$reqQtyAmtArr[$row['JOB_ID']]['special_works_qty']+=$consQnty;
				$reqQtyAmtArr[$row['JOB_ID']]['special_works_amt']+=$consAmt;
			}
			else if($row['EMB_NAME']==5)
			{
				$reqQtyAmtArr[$row['JOB_ID']]['gmts_dyeing_qty']+=$consQnty;
				$reqQtyAmtArr[$row['JOB_ID']]['gmts_dyeing_amt']+=$consAmt;
			}
			else
			{
				//$row['EMB_NAME']==99;
				$reqQtyAmtArr[$row['JOB_ID']]['others_qty']+=$consQnty;
				$reqQtyAmtArr[$row['JOB_ID']]['others_amt']+=$consAmt;
			}*/
		}
		unset($sqlEmbRes); 
		//echo "<pre>";
		//print_r($reqQtyAmtArr); die;
		
		$result =sql_select("select po_id as id, po_number, pub_shipment_date, file_no, excess_cut, grouping, po_received_date, plan_cut from wo_po_break_down_his where job_no_mst='$txt_job_no' $txt_po_breack_down_id_cond1 and status_active=1 and is_deleted=0 and approved_no=$revised_no and approval_page=15 order by po_received_date DESC");
		
		$job_in_orders = ''; $public_ship_date=''; $job_in_ref = ''; $job_in_file = '';
		$tot_excess_cut=0;$tot_row=0;
		foreach ($result as $val)
		{
			$job_in_orders .= $val[csf('po_number')].", ";
			$public_ship_date = $val[csf('pub_shipment_date')];
			$po_received_date = $val[csf('po_received_date')];
			$txt_order_no_arr[$val[csf('id')]] = $val[csf('id')];
			if($val[csf('excess_cut')]>0)
			{
				$tot_row++; 
			}
			$tot_excess_cut+= $val[csf('excess_cut')];
			$plancutqty +=$val[csf('plan_cut')];
		}
		$txt_order_no_id=implode(",", $txt_order_no_arr);
  $total_other_cost = 0;
  foreach ($data_array as $row)
  { 
    $order_price_per_dzn=0;
    $order_job_qnty=0;
    $ord_qty=0;
    $avg_unit_price=0;
    $uom=$row[csf("order_uom")]; 
    $sew_smv=$row[csf("sew_smv")]; 
    $order_values = $row[csf("job_qty")]*$row[csf("avg_unit_price")];   
  
    $job_in_orders = substr(trim($job_in_orders),0,-1);
    if($row[csf("costing_per")]==1){$order_price_per_dzn=12;$costing_for="1 DZN";}
    else if($row[csf("costing_per")]==2){$order_price_per_dzn=1;$costing_for="1 PCS";}
    else if($row[csf("costing_per")]==3){$order_price_per_dzn=24;$costing_for="2 DZN";}
    else if($row[csf("costing_per")]==4){$order_price_per_dzn=36;$costing_for="3 DZN";}
    else if($row[csf("costing_per")]==5){$order_price_per_dzn=48;$costing_for="4 DZN";}
    else {$order_price_per_dzn=0; $costing_for="DZN";}
    $order_job_qnty=$row[csf("job_qty")];
    //$order_qty = $row[csf("job_qty")]*$set_item_ratio;
    $po_no=str_replace("'","",$txt_po_breack_down_id);
    /*$condition= new condition();
    if(str_replace("'","",$txt_job_no) !=''){
        $condition->job_no("='$txt_job_no'");
     }
     
      if(str_replace("'","",$txt_po_breack_down_id)!='')
     {
      $condition->po_id("in($po_no)"); 
     }
    $condition->init();   
    $fabric= new fabric($condition);
    $yarn= new yarn($condition);
    $yarn_costing_arr=$yarn->getJobWiseYarnAmountArray();
    $yarn_qty_amount_arr=$yarn->getJobWiseYarnQtyAndAmountArray();

    $yarnDataWithFabricidArr=$yarn->get_By_Precostfabricdtlsid_YarnQtyAmountArray();

    $fabric= new fabric($condition);
    $fabricAmoutByFabricSource= $fabric->getAmountArray_by_job_knitAndwoven_greyAndfinish();
    $fabricQtyByFabricSource= $fabric->getQtyArray_by_job_knitAndwoven_greyAndfinish_purchase();
    
    $fabric_qty_arr=$fabric->getQtyArray_by_Fabriccostid_knitAndwoven_greyAndfinish();
    $fabric_amount_arr=$fabric->getAmountArray_by_Fabriccostid_knitAndwoven_greyAndfinish();
    $conversion= new conversion($condition);
    $conversion_costing_arr_process=$conversion->getAmountArray_by_job();
    $conv_qty_job_process= $conversion->getQtyArray_by_jobAndProcess();
    $conv_amount_job_process= $conversion->getAmountArray_by_jobAndProcess();
    $con_qty_fabric_process = $conversion->getQtyArray_by_fabricAndProcess();
    $con_amount_fabric_process = $conversion->getAmountArray_by_fabricAndProcess();

    $trims= new trims($condition);
    $trims_costing_arr=$trims->getAmountArray_by_job();
    $trims_qty_arr=$trims->getQtyArray_by_job();

    $emblishment= new emblishment($condition);
    $emblishment_costing_arr=$emblishment->getAmountArray_by_job();
    $emb_qty_job_name_arr = $emblishment->getQtyArray_by_jobAndEmbname();
    $emb_amount_job_name_arr = $emblishment->getAmountArray_by_jobAndEmbname();

    $wash= new wash($condition);
    $emblishment_costing_arr_wash=$wash->getAmountArray_by_job();
    $wash_qty_job_name_arr =$wash->getQtyArray_by_jobAndEmbname();
    $wash_amount_job_name_arr =$wash->getAmountArray_by_jobAndEmbname();


    $commercial= new commercial($condition);
    $commercial_costing_arr=$commercial->getAmountArray_by_job();
    $commission= new commision($condition);
    $commission_costing_arr=$commission->getAmountArray_by_job();
    $other= new other($condition);
    $other_costing_arr=$other->getAmountArray_by_job();*/
    /*echo '<pre>';
    print_r($fabric_amount_arr); die;*/
	
    $job_id= $row[csf("job_id")];
    $finishing_arr = array('209','165','33','94','63','171','65','170','156','179','200','208','127','125','84','68','128','190','242','240','192','172','90','218','67','197','73','66','185','142','193');
	
    $total_finishing_amount=0; $total_finishing_qty=0;
	
    $other_cost_attr = array('inspection','freight','certificate_pre_cost','deffdlc_cost','design_cost','studio_cost','common_oh','interest_cost','incometax_cost','depr_amor_pre_cost');
	
    foreach ($other_cost_attr as $attr) {
      $total_other_cost+=$other_costing_arr[$job_id][$attr];
    }
    $misc_cost=$other_costing_arr[$job_id]['lab_test']+$other_costing_arr[$job_id]['comm_cost']+$other_costing_arr[$job_id]['commission']+$total_other_cost;

    foreach ($finishing_arr as $fid) {
      $total_finishing_amount +=$convQtyAmtArr[$job_id]['conv_amt'][$fid];
      $total_finishing_qty += $convQtyAmtArr[$job_id]['conv_qty'][$fid];
	  //echo $convQtyAmtArr[$job_id]['conv_amt'][$fid].'='.$fid.'<br>';
    }

    $total_fabic_cost=0;
    if(count($convQtyAmtArr[$job_id]['conv_qty'][31])>0){
      $total_fabic_cost+=$convQtyAmtArr[$job_id]['conv_amt'][31]/$convQtyAmtArr[$job_id]['conv_qty'][31];
    }
    $total_fabric_amount +=$convQtyAmtArr[$job_id]['conv_amt'][31];
    $total_fabric_per +=$convQtyAmtArr[$job_id]['conv_amt'][31]/$order_values*100;
    if(count($convQtyAmtArr[$job_id]['conv_amt'][30])>0){
      $total_fabic_cost+=$convQtyAmtArr[$job_id]['conv_amt'][30]/$convQtyAmtArr[$job_id]['conv_qty'][30];
    }
    if($yarnQtyAmtArr[$job_id]['yarn_amt']!=''){
      $total_fabic_cost+=$yarnQtyAmtArr[$job_id]['yarn_amt']/$yarnQtyAmtArr[$job_id]['yarn_qty'];
    }
    $total_fabric_amount +=$yarnQtyAmtArr[$job_id]['yarn_amt']; 
    $total_fabric_per +=$yarnQtyAmtArr[$job_id]['yarn_amt']/$order_values*100;
    if($total_finishing_amount!=0){
      $total_fabic_cost+=$total_finishing_amount/$total_finishing_qty;
    } 
    $total_fabric_amount +=$total_finishing_amount;
    $total_fabric_per +=$total_finishing_amount/$order_values*100;
    $total_fabric_amount +=$convQtyAmtArr[$job_id]['conv_amt'][30];
    $total_fabric_per +=$convQtyAmtArr[$job_id]['conv_amt'][30]/$order_values*100;
    if($convQtyAmtArr[$job_id]['conv_amt'][35]>0){
      $total_fabic_cost+=$convQtyAmtArr[$job_id]['conv_amt'][35]/$convQtyAmtArr[$job_id]['conv_qty'][30];
    }
    $total_fabric_amount +=$convQtyAmtArr[$job_id]['conv_amt'][35]; 
    $total_fabric_per +=$convQtyAmtArr[$job_id]['conv_amt'][35]/$order_values*100;
    if($convQtyAmtArr[$job_id]['conv_amt'][1]>0){
      $total_fabic_cost+=$convQtyAmtArr[$job_id]['conv_amt'][1]/$convQtyAmtArr[$job_id]['conv_qty'][1];
    }
    $total_fabric_amount +=$convQtyAmtArr[$job_id]['conv_amt'][1];
    $total_fabric_per +=$convQtyAmtArr[$job_id]['conv_amt'][1]/$order_values*100; 

    $purchase_amount = $fabQtyAmtArr[$job_id]['puramt'];
    $purchase_qty = $fabQtyAmtArr[$job_id]['purqty'];

    $ather_emb_attr = array(4,5,6,99);
    foreach ($ather_emb_attr as $att) {
      $others_emb_amount += $embQtyAmtArr[$job_id][$att]['amt'];
      $others_emb_qty += $embQtyAmtArr[$job_id][$att]['qty'];
    }
    $knitting_amount_summ=''; $dyeing_amount_summ=''; $yds_amount_summ=''; $aop_amount_summ='';
    if($convQtyAmtArr[$job_id]['conv_amt'][1]>0) {
      $knitting_amount_summ = fn_number_format($convQtyAmtArr[$job_id]['conv_amt'][1],2);
    }
    $yarn_amount_summ = $yarnQtyAmtArr[$job_id]['yarn_amt'];
    $print_amount_summ =$embQtyAmtArr[$job_id][1]['amt'];    
    $emb_amount_summ= $embQtyAmtArr[$job_id][2]['amt'];
    $wash_amount_summ = $embQtyAmtArr[$job_id][3]['amt'];
    if(count($convQtyAmtArr[$job_id]['conv_amt'][31])>0) {
      $dyeing_amount_summ=  $convQtyAmtArr[$job_id]['conv_amt'][31];
    }
    if(count($convQtyAmtArr[$job_id]['conv_amt'][30])>0) {
      $yds_amount_summ =$convQtyAmtArr[$job_id]['conv_amt'][30];
    }
    if(count($convQtyAmtArr[$job_id]['conv_amt'][35])>0) {
      $aop_amount_summ = $convQtyAmtArr[$job_id]['conv_amt'][35];
    }
    
    $total_budget_value = $yarn_amount_summ+$total_finishing_amount+$print_amount_summ+$trimQtyAmtArr[$job_id]['trimtotamt']+$yds_amount_summ+$aop_amount_summ+$emb_amount_summ+$knitting_amount_summ+$purchase_amount+$wash_amount_summ+$other_costing_arr[$job_id]['cm_cost']+$dyeing_amount_summ+$others_emb_amount+$misc_cost;
	//echo $total_budget_value; die;
    ?>
      <table class="rpt_table" border="1" cellpadding="1" cellspacing="1" style="width:970px; font-family: 'Arial Narrow', Arial, sans-serif;" rules="all">
          <tr>
              <th rowspan="7">
              <? foreach($photo_data_array as $inf){ ?>
              <img  src='<?=$img_path; ?><? echo $inf[csf("image_location")]; ?>' height='100px' width='100px' />
              <? } ?>
              </th>
              <th style="background: #D7ECD9">Job No</th>
                <th><?=$row[csf("job_no")]; ?></th>
                <th style="background: #D7ECD9">OR. Rcv Date</th>
                <th><?=date('d-M-y',strtotime($po_received_date)); ?></th>
                <th style="background: #D7ECD9">Order Quantity</th>
                <th style="background: yellow; color: #8B0000;">Price/Pcs</th>
                <th align="right" style="background: yellow; color: #8B0000;">&#36; <?=$row[csf("avg_unit_price")]; ?> </th>
            </tr>
            <tr>                      
                <th style="background: #D7ECD9">Buyer</th>
                <th><?=$buyer_arr[$row[csf("buyer_name")]]; ?></th>
                <th style="background: #D7ECD9">Ship. Date</th>
                <th><?=date('d-M-y',strtotime($public_ship_date)); ?></th>
              	<th align="center" style="color: #8B0000"><?=$row[csf("job_qty")];?> <?=$unit_of_measurement[$row[csf("order_uom")]]; ?></th>
                <th style="background: yellow; color: #8B0000;">Order Value</th>                      
                <th align="right" style="background: yellow; color: #8B0000;">&#36; <?=number_format($order_values,2);  ?></th>
            </tr>
            <tr>
              <th style="background: #D7ECD9">Prod. Dept</th>
                <th><?=$product_dept[$row[csf("product_dept")]]; ?></th>
                <th style="background: #D7ECD9">Garments Item</th>
              <th> 
				<?
                $grmnt_items = "";
                if($garments_item[$row[csf("gmts_item_id")]]=="")
                {
					$grmts_sql = sql_select("select job_no, gmts_item_id, set_item_ratio from wo_po_dtls_item_set_his where job_no='$txt_job_no'");
						foreach($grmts_sql as $key=>$val){
							$grmnt_items .=$garments_item[$val[csf("gmts_item_id")]].", ";
							$gmts_item[]=$val[csf("gmts_item_id")];
						}
						$grmnt_items = substr_replace($grmnt_items,"",-1,1);
					}else{
						$gmts_item=explode(',',$row[csf("gmts_item_id")]);
						$grmnt_items = $garments_item[$row[csf("gmts_item_id")]];
                }
                echo $grmnt_items;
                ?>
        	</th>
              <th align="center" style="color: #8B0000"><?= $row[csf("job_qty")]*$set_item_ratio.' Pcs' ?></th>
                <th style="background: yellow; color: #8B0000;"> <? if($zero_value==0) echo "Budget Value"; ?></th>                      
                <th align="right" style="background: yellow; color: #8B0000;"><? if($zero_value==0){ ?>
                <? if($total_budget_value>0){ echo '&dollar;'.fn_number_format($total_budget_value,2); } ?><br/>
                <? if($total_budget_value>0){ echo fn_number_format($total_budget_value/$order_values*100,2).'%'; } ?>
                <? } ?>
                </th>
            </tr>
            <tr>
              <th style="background: #D7ECD9">Season / Brand</th>
                <th><?=$sesson_arr[$row[csf("season_buyer_wise")]].'&nbsp'.$brand_arr[$row[csf("brand_id")]]; ?></th>
                <th>Costing Per: <br><?= $costing_for;  ?></th>
                <th style="background: #D7ECD9">Plan Cut Quantity (<?=$tot_excess_cut.'%' ?>) </td>
              <th align="center" style="color: #8B0000"><?= $row[csf("job_quantity")]*$total_set_qnty.' Pcs';//." ". $unit_of_measurement[$row[csf("order_uom")]]; ?></th>
                <th rowspan="2" style="background: yellow; color: #8B0000;"><? if($zero_value==0) echo "Open Value %"; ?></th>                      
                <th rowspan="2" align="right" style="background: yellow; color: #8B0000;"><? if($zero_value==0) { ?> &#36;<? 
                  $margin_val = $order_values-$total_budget_value; 
                  echo fn_number_format($margin_val,2).'<br>'.fn_number_format($margin_val/$order_values*100,2).'%';
                  }
                 ?></th>
            </tr>
            <tr>
              <th style="background: #D7ECD9">Style No</th>
                <th><? $style_no= $row[csf("style_ref_no")]; echo $row[csf("style_ref_no")]; ?></th>
                <th style="background: #D7ECD9">App. Status</th>
                <th colspan="2"><?=$appMsg; ?></th>
            </tr>
            <tr>
              <th rowspan="2" style="background: #D7ECD9">Style Description</th>
                <th rowspan="2" colspan="2"><? echo $row[csf("style_description")]; ?></th>
                <th style="background: #D7ECD9">Remarks</th>
                <th colspan="3"><? echo $row[csf("remarks")]; ?></th>
            </tr>
            <tr>
              <th style="background: #D7ECD9">Refusing Cause</th>
                <th colspan="3"><? echo $row[csf("refusing_cause")]; ?></th>
            </tr>
        </table>

            <?        
      $avg_unit_price=$row[csf("avg_unit_price")];
      $ord_qty=$row[csf("ord_qty")];
  }//end first foearch
  /*echo '<pre>';
  print_r($conv_amount_job_process); die;*/
  
  $yarnPer=$yarnQtyAmtArr[$job_id]['yarn_amt']/$yarnQtyAmtArr[$job_id]['yarn_qty'];
  $finishPer=$total_finishing_amount/$total_finishing_qty;
  $ydsPer=$convQtyAmtArr[$job_id]['conv_amt'][30]/$convQtyAmtArr[$job_id]['conv_qty'][30];
  $aopPer=$convQtyAmtArr[$job_id]['conv_amt'][35]/$convQtyAmtArr[$job_id]['conv_qty'][35];
  $knitPer=$convQtyAmtArr[$job_id]['conv_amt'][1]/$convQtyAmtArr[$job_id]['conv_qty'][1];
  $purchPer=$purchase_qty/$purchase_amount;
  $dyePer=$convQtyAmtArr[$job_id]['conv_amt'][31]/$convQtyAmtArr[$job_id]['conv_qty'][31];
  
  $totFabPer=$yarnPer+$finishPer+$ydsPer+$aopPer+$knitPer+$purchPer+$dyePer;
  
  //echo $yarnPer.'='.$finishPer.'='.$ydsPer.'='.$aopPer.'='.$knitPer.'='.$purchPer.'='.$dyePer.'='.$totFabPer;
  //echo $other_costing_arr[$job_id]['cm_cost'].'='.$plancutqty.'='.$set_item_ratio;
  
    ?>
    <br>
    <label  style="float:left;background:#CCCCCC; font-size:larger;"><b>Summary </b> </label> 
    <table class="rpt_table" border="1" cellpadding="1" cellspacing="1" style="width:970px; margin-top: 10px; font-family: 'Arial Narrow', Arial, sans-serif;" rules="all">
    
      <tr style="background: #D7ECD9">
        <th colspan="8" width="320">Fabric </th>
        <th colspan="4" width="160">Embellishment</th>
        <th colspan="4" width="160">Trims + CM + Misc</th>
        <th style="background: yellow">TTL COST &dollar;</th>
      </tr>
      <tr style="background: #D7ECD9">
        <th align="center">Item</th>
        <th align="center">Cost/Uom</th>
        <th align="center">Amount</th>
        <th align="center">&percnt;</th>
        <th align="center">Item</th>
        <th align="center">Cost/Uom</th>
        <th align="center">Amount</th>
        <th align="center">&percnt;</th>
        <th align="center">Item</th>
        <th align="center">Cost/Dz</th>
        <th align="center">Amount</th>
        <th align="center">&percnt;</th>
        <th align="center">Item</th>
        <th align="center">Cost/Dz</th>
        <th align="center">Amount</th>
        <th align="center">&percnt;</th>
        <th rowspan="5" align="right" style="background: yellow; color: #8B0000"><b>
          <? if($total_budget_value>0) { echo fn_number_format($total_budget_value,2,'',''); } ?><br/><br/>
                <? if($total_budget_value>0){ echo fn_number_format($total_budget_value/$order_values*100,2).'%'; } ?></b>
        </th>
      </tr>
      <tr>
        <th align="center">Yarn</th>
        <td align="center"><? if($yarn_amount_summ>0) { echo fn_number_format($yarnPer,2); } ?></td>
        <td align="right" style="color: #8B0000"><? if($yarn_amount_summ>0) { echo '&dollar;'.fn_number_format($yarn_amount_summ,2); } ?></td>
        <td align="right"><? if($yarn_amount_summ>0) { echo fn_number_format($yarnQtyAmtArr[$job_id]['yarn_amt']/$order_values*100,2).'%';}; ?></td>

        <th align="center">Finishing</th>
        <td align="center"><? if($total_finishing_amount>0) { echo fn_number_format($total_finishing_amount/$total_finishing_qty,2); } ?></td>
        <td align="right" style="color: #8B0000"><? if($total_finishing_amount>0) { echo '&dollar;'.fn_number_format($total_finishing_amount,2); } ?></td>
        <td align="right"><? if($total_finishing_amount>0) { echo fn_number_format($total_finishing_amount/$order_values*100,2).'%';} ?></td>

        <th align="center">Print</th>
        <td align="center"><? if($print_amount_summ>0) { echo fn_number_format($embQtyAmtArr[$job_id][1]['amt']/$embQtyAmtArr[$job_id][1]['qty'],2);}  ?></td>
        <td align="right" style="color: #8B0000"><? if($print_amount_summ>0) { echo '&dollar;'.fn_number_format($print_amount_summ,2);}  ?></td>
        <td align="right"><? if($print_amount_summ>0) { echo fn_number_format($embQtyAmtArr[$job_id][1]['amt']/$order_values*100,2).'%';} ?></td>

        <th align="center">Trim</th>
        <td align="center"><? if($trimQtyAmtArr[$job_id]['trimtotamt']>0) { echo fn_number_format($trimQtyAmtArr[$job_id]['trimtotamt']/$order_job_qnty,2); } ?></td>
        <td align="right" style="color: #8B0000"><? if($trimQtyAmtArr[$job_id]['trimtotamt']>0) { echo '&dollar;'.fn_number_format($trimQtyAmtArr[$job_id]['trimtotamt'],2);} ?></td>
        <td align="right"><? if($trimQtyAmtArr[$job_id]['trimtotamt']>0) { echo fn_number_format($trimQtyAmtArr[$job_id]['trimtotamt']/$order_values*100,2).'%';} ?></td>
      </tr>
      <tr>
        <th align="center">Yds</th>
        <td align="center"><? if($yds_amount_summ>0) { echo fn_number_format($convQtyAmtArr[$job_id]['conv_amt'][30]/$convQtyAmtArr[$job_id]['conv_qty'][30],2); } ?></td>
        <td align="right" style="color: #8B0000"><? if($yds_amount_summ>0) { echo '&dollar;'.fn_number_format($yds_amount_summ,2);} ?></td>
        <td align="right"><? if($yds_amount_summ>0) { echo fn_number_format($convQtyAmtArr[$job_id]['conv_amt'][30]/$order_values*100,2).'%';} ?></td>

        <th align="center">AOP</th>
        <td align="center"><? if($aop_amount_summ>0) { echo fn_number_format($convQtyAmtArr[$job_id]['conv_amt'][35]/$convQtyAmtArr[$job_id]['conv_qty'][35],2); } ?></td>
        <td align="right" style="color: #8B0000"><? if($aop_amount_summ>0) { echo '&dollar;'.fn_number_format($aop_amount_summ,2);} ?></td>
        <td align="right"><? if($aop_amount_summ>0) { echo fn_number_format($convQtyAmtArr[$job_id]['conv_amt'][35]/$order_values*100,2).'%';} ?></td>

        <th align="center">EMB</th>
        <td align="center"><? if($emb_amount_summ>0) { echo fn_number_format($embQtyAmtArr[$job_id][2]['amt']/$embQtyAmtArr[$job_id][2]['qty'],2);}  ?></td>
        <td align="right" style="color: #8B0000"><? if($emb_amount_summ>0) { echo '&dollar;'.fn_number_format($emb_amount_summ,2);}  ?></td>
        <td align="right"><? if($emb_amount_summ>0) { echo fn_number_format($embQtyAmtArr[$job_id][2]['amt']/$order_values*100,2).'%';} ?></td>
        <th align="center">MISC</th>
        <td align="center"><?  if($misc_cost>0) { echo fn_number_format($misc_cost/$order_job_qnty*12,2); } ?></td>
        <td align="right" style="color: #8B0000"><? if($misc_cost>0) { echo '&dollar;'.fn_number_format($misc_cost,2);}  ?></td>
        <td align="right"><? if($misc_cost>0) { echo fn_number_format($misc_cost/$order_values*100,2).'%';} ?></td>
      </tr>
      <tr>
        <th align="center">Knitting</th>
        <td align="center"><? if($knitting_amount_summ !='') { echo fn_number_format($convQtyAmtArr[$job_id]['conv_amt'][1]/$convQtyAmtArr[$job_id]['conv_qty'][1],2); } ?></td>
        <td align="right" style="color: #8B0000"><? if($knitting_amount_summ !='') { echo  '&dollar;'.$knitting_amount_summ;}   ?></td>
        <td align="right"><? if($knitting_amount_summ !=''){echo fn_number_format($convQtyAmtArr[$job_id]['conv_amt'][1]/$order_values*100,2).'%'; } ?></td>

        <th align="center">P. Fabric</th>
        <td align="center"><? $total_fabic_cost+=$purchase_qty/$purchase_amount; if($purchase_qty>0 && $purchase_amount>0){ echo fn_number_format($purchase_qty/$purchase_amount,2);} ?></td>
        <td align="right"><? $total_fabric_amount+=$purchase_amount; if($purchase_amount){echo '&dollar;'.fn_number_format($purchase_amount,2); } ?></td>
        <td align="right"><? $total_fabric_per+=$purchase_amount/$order_values*100; if($purchase_amount>0){ echo fn_number_format($purchase_amount/$order_values*100,2).'%'; }  ?></td>

        <th align="center">Wash</th>
        <td align="center"><? if($wash_amount_summ>0) {echo fn_number_format($embQtyAmtArr[$job_id][3]['amt']/$embQtyAmtArr[$job_id][3]['qty'],2); };  ?></td>
        <td align="right" style="color: #8B0000"><? if($wash_amount_summ>0) { echo '&dollar;'.fn_number_format($wash_amount_summ,2);}  ?></td>
        <td align="right"><? if($wash_amount_summ>0) { echo fn_number_format($embQtyAmtArr[$job_id][3]['amt']/$order_values*100,2).'%';} ?></td>

        <th align="center" style="color: #8B0000">F.CM</th>
        <td align="center" style="color: #8B0000" title="(CM Cost/Order Qty Pcs)x12"><? if($other_costing_arr[$job_id]['cm_cost']>0){echo fn_number_format(($other_costing_arr[$job_id]['cm_cost']/($plancutqty*$set_item_ratio))*12,2); } ?></td>
        <td align="right" style="color: #8B0000"><? if($other_costing_arr[$job_id]['cm_cost']>0){ echo fn_number_format($other_costing_arr[$job_id]['cm_cost'],2); } ?></td>
        <td align="right"><? if($other_costing_arr[$job_id]['cm_cost']>0){ echo fn_number_format($other_costing_arr[$job_id]['cm_cost']/$order_values*100,2).'%'; } ?></td>
      </tr>
      <tr>
        <th align="center">Dyeing</th>
        <td align="center"><? if($dyeing_amount_summ>0) {echo fn_number_format($convQtyAmtArr[$job_id]['conv_amt'][31]/$convQtyAmtArr[$job_id]['conv_qty'][31],2);} ?></td>
        <td align="right" style="color: #8B0000"><? if($dyeing_amount_summ>0) { echo '&dollar;'.fn_number_format($dyeing_amount_summ,2);} ?></td>
        <td align="right"><? if($dyeing_amount_summ>0) { echo fn_number_format($convQtyAmtArr[$job_id]['conv_amt'][31]/$order_values*100,2).'%';} ?></td>

        <th align="center" style="color: #8B0000">TOTAL</th>
        <th align="center" style="color: #8B0000"><? if($total_fabic_cost>0){ echo fn_number_format($totFabPer,2); } ?></th>
        <th align="right" style="color: #8B0000"><? if($total_fabric_amount>0){ echo '&dollar;'.fn_number_format($total_fabric_amount,2); }  ?></th>
        <th align="right" style="color: #8B0000"><? if($total_fabric_per>0){ echo fn_number_format($total_fabric_per,2); }  ?></th>

        <th align="center" title="Special works, Garments dyeing, UV print and others.">Others</th>
        <td align="center"><? if($others_emb_amount>0) {echo fn_number_format($others_emb_amount/$others_emb_qty,2); } ?></td>
        <td align="right"><? if($others_emb_amount>0) { echo '&dollar;'.fn_number_format($others_emb_amount,2); }  ?></td>
        <td align="right"><? if($others_emb_amount>0) { echo fn_number_format($others_emb_amount/$order_values*100,2);}  ?></td>
        <th></th>
        <td></td>
        <td></td>
        <td></td>
      </tr>
    </table>    
    <?
	$location_cpm_cost=0;
	$cm_min_variable=return_field_value("yarn_iss_with_serv_app as cost_per_minute","variable_order_tracking","company_name =".$cbo_company_name." and variable_list=67 and is_deleted=0 and status_active=1","cost_per_minute");
	if($cm_min_variable=="" || $cm_min_variable==0) $location_cpm_cost=0; else $location_cpm_cost=$cm_min_variable;
	if($location_cpm_cost!=1)
	{
		$sql_std_para=sql_select("select interest_expense, income_tax, cost_per_minute, applying_period_date, applying_period_to_date from lib_standard_cm_entry where company_id=$cbo_company_name and status_active=1 and is_deleted=0 order by id");
		
		foreach($sql_std_para as $row )
		{
			$applying_period_date=change_date_format($row[csf('applying_period_date')],'','',1);
			$applying_period_to_date=change_date_format($row[csf('applying_period_to_date')],'','',1);
			$diff=datediff('d',$applying_period_date,$applying_period_to_date);
			for($j=0;$j<$diff;$j++)
			{
				//$newdate =change_date_format(add_date(str_replace("'","",$applying_period_date),$j),'','',1);
				$date_all=add_date(str_replace("'","",$applying_period_date),$j);
				$newdate =change_date_format($date_all,'','',1);
				$financial_para[$newdate][interest_expense]=$row[csf('interest_expense')];
				$financial_para[$newdate][income_tax]=$row[csf('income_tax')];
				$financial_para[$newdate][cost_per_minute]=$row[csf('cost_per_minute')];
			}
		}
	}
	else
	{
		$sql_std_para=sql_select( "select a.id, b.id as dtls_id, b.location_id, b.applying_period_date, b.applying_period_to_date, b.monthly_cm_expense, b.no_factory_machine, b.working_hour, b.cost_per_minute from lib_standard_cm_entry a, lib_standard_cm_entry_dtls b where a.id=b.mst_id and b.location_id=$location_name_id and a.company_id=$cbo_company_name" );
		foreach($sql_std_para as $row)
		{
			$applying_period_date=change_date_format($row[csf('applying_period_date')],'','',1);
			$applying_period_to_date=change_date_format($row[csf('applying_period_to_date')],'','',1);
			$diff=datediff('d',$applying_period_date,$applying_period_to_date);
			for($j=0;$j<$diff;$j++)
			{
				$date_all=add_date(str_replace("'","",$applying_period_date),$j);
				$newdate =change_date_format($date_all,'','',1);
				$financial_para[$newdate][interest_expense]=$row[csf('interest_expense')];
				$financial_para[$newdate][income_tax]=$row[csf('income_tax')];
				$financial_para[$newdate][cost_per_minute]=$row[csf('cost_per_minute')];
			}
		}
	}

    $pre_costing_date=change_date_format($costing_date,'','',1);
    ?>
    <? if($zero_value==0){ ?>
    <br/>
    <label  style="text-align:left; background:#CCCCCC; font-size:larger;"><b>CM Details </b> </label>
    <div style="width:970px; margin-top: 10px; font-family: 'Arial Narrow', Arial, sans-serif;">
    <table class="rpt_table" border="1" cellpadding="1" cellspacing="1" style="width:720px;float: left;" rules="all">
      <tr>
        <th colspan="13">&nbsp;</th>
      </tr>
      <tr style="background: #D7ECD9">
        <th>Style NO.</th>
        <th>MC</th>
        <th>Prd/Hr</th>
        <th>SMV</th>
        <th>BCM</th>
        <th>F.CM</th>
        <th>TTL Min</th>
        <th align="center">CPM</th>
        <th>RL</th>
        <th>RD</th>
        <th>A Eff%</th>
        <th>Layout No</th>
        <th>Alloc Qty</th>
      </tr>
      <tr align="center">
        <td><?= $style_no  ?></td>
        <td>&nbsp;</td>
        <td>&nbsp;</td>
        <td><?= $sew_smv ?></td>
        <td>&nbsp;</td>
        <td>&nbsp;</td>
        <td>&nbsp;</td>
        <td>&#36; <? echo fn_number_format($financial_para[$pre_costing_date][cost_per_minute],4); ?></td>
        <td>&nbsp;</td>
        <td>&nbsp;</td>
        <td>&nbsp;</td>
        <td>&nbsp;</td>
        <td>&nbsp;</td>
      </tr>
      <tr>
        <th>Grand Total</th>
        <th>&nbsp;</th>
        <th>&nbsp;</th>
        <th><?= $sew_smv ?></th>
        <th>&nbsp;</th>
        <th>&nbsp;</th>
        <th>&nbsp;</th>
        <th>&#36; <? echo fn_number_format($financial_para[$pre_costing_date][cost_per_minute],4); ?></th>
        <th>&nbsp;</th>
        <th>&nbsp;</th>
        <th>&nbsp;</th>
        <th>&nbsp;</th>
        <th>&nbsp;</th>
      </tr>
    </table>
    <table class="rpt_table" border="1" cellpadding="1" cellspacing="1" style="width:248px; margin-left: 2px; float: right;" rules="all">
      <tr>
        <th colspan="3" bgcolor="yellow">Embellishment[DZN]</th>
      </tr>
      <tr>
        <th>Print Qty</th>
        <th>Emb Qty</th>
        <th>Wash Qty</th>
      </tr>
      <tr align="center">
        <td><? if($embQtyAmtArr[$job_id][1]['qty']>0){echo fn_number_format($embQtyAmtArr[$job_id][1]['qty'],2); } else { echo '&nbsp;'; } ?></td>
        <td><? if($embQtyAmtArr[$job_id][2]['qty']>0){echo fn_number_format($embQtyAmtArr[$job_id][1]['qty'],2); } else { echo '&nbsp;'; } ?></td>
        <td><? if($embQtyAmtArr[$job_id][3]['qty']>0){echo fn_number_format($embQtyAmtArr[$job_id][3]['qty'],2); } else { echo '&nbsp;'; } ?></td>
      </tr>
      <tr>
        <th><? if($embQtyAmtArr[$job_id][1]['qty']>0){echo fn_number_format($embQtyAmtArr[$job_id][1]['qty'],2); } else { echo '&nbsp;'; } ?></th>
        <th><? if($embQtyAmtArr[$job_id][2]['qty']>0){echo fn_number_format($embQtyAmtArr[$job_id][2]['qty'],2); } else { echo '&nbsp;'; } ?></th>
        <th><? if($embQtyAmtArr[$job_id][3]['qty']>0){echo fn_number_format($embQtyAmtArr[$job_id][3]['qty'],2); } else { echo '&nbsp;'; } ?></th>
      </tr>
    </table>    
    </div>
    <br>
    <? } ?>
    <?
      $nameArray_fabric_description= sql_select("SELECT (a.pre_cost_fabric_cost_dtls_id) as fabric_cost_dtls_id, a.item_number_id, max(a.lib_yarn_count_deter_id) as determin_id, a.body_part_id, a.uom, a.color_type_id, a.fabric_source, a.construction, a.composition, a.gsm_weight, min(a.width_dia_type) as width_dia_type, b.dia_width,avg(b.cons) as cons, avg(b.process_loss_percent) as process_loss_percent, a.fab_nature_id, avg(b.requirment) as requirment, d.fabric_composition_id FROM wo_pre_cost_fabric_cost_dtls_h a, wo_po_color_size_his c, wo_pre_fab_avg_con_dtls_h b, lib_yarn_count_determina_mst d WHERE a.job_no=b.job_no and a.pre_cost_fabric_cost_dtls_id=b.pre_cost_fabric_cost_dtls_id and c.job_no_mst=a.job_no and  c.color_size_id=b.color_size_table_id and a.lib_yarn_count_deter_id=d.id and c.status_active=1 and c.is_deleted=0  and b.status_active=1 and b.is_deleted=0 and a.status_active=1 and a.is_deleted=0 and a.job_no ='$txt_job_no' and a.approved_no=$revised_no and b.cons>0 group by a.body_part_id, a.uom, a.pre_cost_fabric_cost_dtls_id, a.item_number_id, a.color_type_id, a.fabric_source, a.construction, a.composition, a.gsm_weight, b.dia_width, a.fab_nature_id, d.fabric_composition_id order by fabric_cost_dtls_id, a.body_part_id, b.dia_width");
		
      //a.fabric_source=1 and
      foreach ($nameArray_fabric_description as $row) {
        $fabric_id=$row[csf('fabric_cost_dtls_id')];
        $yarn_amount= $yarnDataWithFabricidArr[$fabric_id]['amount'];
        $yarn_qty= $yarnDataWithFabricidArr[$fabric_id]['qty'];

        $yds_amount = array_sum($con_amount_fabric_process[$fabric_id][30]);
        $yds_qty = array_sum($con_qty_fabric_process[$fabric_id][30]);

        $knitting_amount = array_sum($con_amount_fabric_process[$fabric_id][1]);
        $knitting_qty = array_sum($con_qty_fabric_process[$fabric_id][1]);
        $dyeing_amount = array_sum($con_amount_fabric_process[$fabric_id][31]);
        $dyeing_qty = array_sum($con_qty_fabric_process[$fabric_id][31]);
        $aop_amount = array_sum($con_amount_fabric_process[$fabric_id][35]);
        $aop_qty = array_sum($con_qty_fabric_process[$fabric_id][35]);

        $total_finishing_amount=0;
        $total_finishing_qty=0;
        foreach ($finishing_arr as $fid) {
          $total_finishing_amount += array_sum($con_amount_fabric_process[$fabric_id][$fid]);
          $total_finishing_qty += array_sum($con_qty_fabric_process[$fabric_id][$fid]);
        }
        
        $fabric_data_arr[$row[csf('fabric_cost_dtls_id')]]['body_part_id'] = $row[csf('body_part_id')];
        $fabric_data_arr[$row[csf('fabric_cost_dtls_id')]]['description'] = $row[csf('construction')].', '.$fabric_composition_arr[$row[csf('fabric_composition_id')]];
        if($row[csf('fab_nature_id')]==2)
        {
          $fabric_data_arr[$row[csf('fabric_cost_dtls_id')]]['fqty'] = array_sum($fabric_qty_arr['knit']['finish'][$row[csf('fabric_cost_dtls_id')]]);
          $fabric_data_arr[$row[csf('fabric_cost_dtls_id')]]['gqty'] = array_sum($fabric_qty_arr['knit']['grey'][$row[csf('fabric_cost_dtls_id')]]);
        }
        if($row[csf('fab_nature_id')]==3)
        {
          $fabric_data_arr[$row[csf('fabric_cost_dtls_id')]]['fqty'] = array_sum($fabric_qty_arr['woven']['finish'][$row[csf('fabric_cost_dtls_id')]]);
          $fabric_data_arr[$row[csf('fabric_cost_dtls_id')]]['gqty'] = array_sum($fabric_qty_arr['woven']['grey'][$row[csf('fabric_cost_dtls_id')]]);
        }
        
        $fabric_data_arr[$row[csf('fabric_cost_dtls_id')]]['funit'] = $row[csf('uom')];
        $fabric_data_arr[$row[csf('fabric_cost_dtls_id')]]['cons'] = $row[csf('cons')];
        
        $fabric_data_arr[$row[csf('fabric_cost_dtls_id')]]['process_loss'] = $row[csf('process_loss_percent')];
        $fabric_data_arr[$row[csf('fabric_cost_dtls_id')]]['yarn_amount'] = $yarn_amount;
        $fabric_data_arr[$row[csf('fabric_cost_dtls_id')]]['yarn_per'] = $yarn_amount/$yarn_qty;

        $fabric_data_arr[$row[csf('fabric_cost_dtls_id')]]['yds_amount'] = $yds_amount;
        $fabric_data_arr[$row[csf('fabric_cost_dtls_id')]]['yds_per'] = $yds_amount/$yds_qty;
        $fabric_data_arr[$row[csf('fabric_cost_dtls_id')]]['knitting_amount'] = $knitting_amount;
        $fabric_data_arr[$row[csf('fabric_cost_dtls_id')]]['knitting_per'] = $knitting_amount/$knitting_qty;
        $fabric_data_arr[$row[csf('fabric_cost_dtls_id')]]['dyeing_amount'] = $dyeing_amount;
        $fabric_data_arr[$row[csf('fabric_cost_dtls_id')]]['dyeing_per'] = $dyeing_amount/$dyeing_qty;
        $fabric_data_arr[$row[csf('fabric_cost_dtls_id')]]['aop_amount'] = $aop_amount;
        $fabric_data_arr[$row[csf('fabric_cost_dtls_id')]]['aop_per'] = $aop_amount/$aop_qty;
        $fabric_data_arr[$row[csf('fabric_cost_dtls_id')]]['finishing_amount'] = $total_finishing_amount;
        $fabric_data_arr[$row[csf('fabric_cost_dtls_id')]]['finishing_per'] = $total_finishing_amount/$total_finishing_qty;
        if($row[csf('fabric_source')]==1)
        {
          $fabric_data_arr[$row[csf('fabric_cost_dtls_id')]]['ttl_cost'] = $yarn_amount+$yds_amount+$knitting_amount+$dyeing_amount+$aop_amount+$total_finishing_amount;
        }
        if($row[csf('fabric_source')]==2)
        {
          if($row[csf('fab_nature_id')]==2)
          {
            $fabric_data_arr[$row[csf('fabric_cost_dtls_id')]]['ttl_cost']=array_sum($fabric_amount_arr['knit']['grey'][$row[csf('fabric_cost_dtls_id')]]);
          }
          if($row[csf('fab_nature_id')]==3)
          {
            $fabric_data_arr[$row[csf('fabric_cost_dtls_id')]]['ttl_cost']=array_sum($fabric_amount_arr['woven']['grey'][$row[csf('fabric_cost_dtls_id')]]);
          }
        }
      }
	  //echo "kkkk1";
      if($zero_value==0){ ?>
      <br>
      <table class="rpt_table" border="1" cellpadding="1" cellspacing="1" style="width:970px; margin-top: 10px; font-family: 'Arial Narrow', Arial, sans-serif;" rules="all">
      <label  style="float:left;background:#CCCCCC; font-size:larger;"><b>Fabric Details </b> </label>  
        <tr style="background: #D7ECD9">
          <th rowspan="2">Garments Part Name</th>
          <th rowspan="2">Fabric Details</th>
          <th rowspan="2">Con</th>
          <th>F. QTY</th>
          <th rowspan="2">Process Loss</th>
          <th>G. QTY</th>
          <th colspan="7">Cost/Uom (Fabric)</th>
          <th rowspan="2">Cost/Dz</th>
          <th rowspan="2" style="background: yellow;">TTL Cost $</th>
        </tr>
        <tr style="background: #D7ECD9">
          <th>Unit</th>
          <th>Unit</th>
          <th>Yarn</th>
          <th>Yds</th>
          <th>Knitting</th>
          <th>Dyeing</th>
          <th>AOP</th>
          <th>Finishing</th>
          <th>Cost/Uom</th>
        </tr>
        <?
          foreach ($fabric_data_arr as $value) {?>
            <tr>
              <td rowspan="2"><?= $body_part[$value['body_part_id']] ?></td>
              <td rowspan="2"><?= $value['description'] ?></td>
              <td rowspan="2" align="center"><?= fn_number_format($value['cons'],2); ?></td>
              <td align="center"><? $total_fqty+=$value['fqty']; echo fn_number_format($value['fqty'],2); ?></td>
              <td rowspan="2" align="center"><? if($value['process_loss']>0){ echo fn_number_format($value['process_loss'],2);} ?></td>
              <td align="center"><? $total_gqty+=$value['gqty']; echo fn_number_format($value['gqty'],2) ?></td>
              <td rowspan="2" align="right"><? $total_yarn_amount += $value['yarn_amount']; if($value['yarn_amount']>0){echo fn_number_format($value['yarn_per'],2); }?><br><? if($value['yarn_amount']>0){ echo fn_number_format($value['yarn_amount'],2);} ?></td>
              <td rowspan="2" align="right"><? $total_yds_amount += $value['yds_amount']; if($value['yds_per']>0){ echo fn_number_format($value['yds_per'],2);}?><br><? if($value['yds_amount']>0){ echo fn_number_format($value['yds_amount'],2);} ?></td>
              <td rowspan="2" align="right"><? $total_knitting_amount += $value['knitting_amount']; if($value['knitting_per']>0){ echo fn_number_format($value['knitting_per'],2);}?><br><? if($value['knitting_amount']>0){ echo fn_number_format($value['knitting_amount'],2);} ?></td>
              <td rowspan="2" align="right"><? $total_dyeing_amount += $value['dyeing_amount']; if($value['dyeing_per']>0){ echo fn_number_format($value['dyeing_per'],2);} ?><br><? if($value['dyeing_amount']>0){echo fn_number_format($value['dyeing_amount'],2);} ?></td>
              <td rowspan="2" align="right"><? $total_aop_amount += $value['aop_amount']; if($value['aop_per']>0){ echo fn_number_format($value['aop_per'],2);} ?><br><? if($value['aop_amount']>0){ echo fn_number_format($value['aop_amount'],2);} ?></td>
              <td rowspan="2" align="right"><? $total_finishing_amount += $value['finishing_amount']; if($value['finishing_per']>0){ echo fn_number_format($value['finishing_per'],2);}?><br><? if($value['finishing_amount']>0){fn_number_format($value['finishing_amount'],2);} ?></td>
              <td rowspan="2" align="right" title="TTL Cost/Finish Quantity"><?= fn_number_format($value['ttl_cost']/$value['fqty'],2) ?></td>
              <td rowspan="2" align="right"><?= fn_number_format($value['ttl_cost']/$order_job_qnty*12,2) ?></td>
              <th rowspan="2" style="background: yellow;" align="right"><? $total_ttl_cost += $value['ttl_cost'];  echo fn_number_format($value['ttl_cost'],2) ?></th>
            </tr>
            <tr>
              <td align="center"><?= $unit_of_measurement[$value['funit']] ?></td>
              <td align="center"><?= $unit_of_measurement[$value['funit']] ?></td>              
            </tr>
          <? }
        ?>
        <tr>
          <th colspan="2">Fabric  Total</th>
          <td></td>
          <th align="center"><? if($total_fqty>0){echo fn_number_format($total_fqty,2);} ?></th>
          <td></td>
          <th align="right"><? if($total_gqty){ echo fn_number_format($total_gqty,2); } ?></th>
          <th align="right"><? if($total_yarn_amount){ echo fn_number_format($total_yarn_amount,2); } ?></th>
          <th align="right"><? if($total_yds_amount){ echo fn_number_format($total_yds_amount,2); } ?></th>
          <th align="right"><? if($total_knitting_amount){ echo fn_number_format($total_knitting_amount,2); } ?></th>
          <th align="right"><? if($total_dyeing_amount){ echo fn_number_format($total_dyeing_amount,2); } ?></th>
          <th align="right"><? if($total_aop_amount){ echo fn_number_format($total_aop_amount,2); } ?></th>
          <th align="right"><? if($total_finishing_amount){ echo fn_number_format($total_finishing_amount,2); } ?></th>
          <th></th>
          <th></th>
          <th style="background: yellow;" align="right"><? if($total_ttl_cost){ echo '&dollar;'.fn_number_format($total_ttl_cost,2); } ?> <br><? if($total_ttl_cost){ echo fn_number_format($total_ttl_cost/$order_values*100,2).'%'; } ?></th>
        </tr>
      </table>
      <? } ?>
      <?
      //end   All Fabric Cost part report-------------------------------------------
      $lib_yarn_count=return_library_array( "select yarn_count,id from lib_yarn_count", "id", "yarn_count"  );
      $sql = "select min(pre_cost_fab_yarn_cost_dtls_id) as id, count_id, copm_one_id, percent_one, copm_two_id, percent_two, color,type_id, min(cons_ratio) as cons_ratio, sum(cons_qnty) as cons_qnty, rate, sum(amount) as amount from wo_pre_cost_fab_yarn_cst_dtl_h where job_no='".$txt_job_no."' and approved_no=$revised_no and status_active=1 and is_deleted=0 group by count_id, copm_one_id, percent_one, copm_two_id, percent_two, color,type_id, rate";
       //echo $sql;
      $data_array=sql_select($sql); 
      //$yarn_data_array=$yarn->getCountCompositionPercentTypeColorAndRateWiseYarnQtyAndAmountArray();
      //print_r($yarn_data_array);
    ?>
    <br>
    <div style="margin-top:15px; font-family: 'Arial Narrow', Arial, sans-serif;">
        <table class="rpt_table" border="1" cellpadding="1" cellspacing="1" style="width:970px;text-align:center;" rules="all">
            <label style="float:left;background:#CCCCCC; font-size:larger;"><b>Yarn Details </b> </label>  
            <tr style="font-weight:bold;">
                <td width="540" style="background: #D7ECD9">Yarn Description</td>
                <td width="80" style="background: #D7ECD9">Yarn Qty/<?=$costing_for; ?></td> 
                <td width="80" style="background: #D7ECD9">TTL Yarn Qty</td>                 
                <td width="80" style="background: #D7ECD9">Rate &#36;</td>
                <td width="80" style="background: yellow">Amount &#36;</td>
                <td width="80" style="background: #D7ECD9">% to Ord. Value</td>
            </tr>
            <?
            $total_yarn_qty = 0; $total_yarn_amount = 0; $total_yarn_cost_dzn=$total_yarn_qty_dzn=0; $total_yarn_cost_kg=0; $total_yarn_avg_cons_qty=0;
            foreach( $data_array as $row )
            { 
				if($row[csf("percent_one")]==100)
					$item_descrition = $lib_yarn_count[$row[csf("count_id")]]." ".$composition[$row[csf("copm_one_id")]]." ".$row[csf("percent_one")]."% ".$color_library[$row[csf("color")]]." ".$yarn_type[$row[csf("type_id")]];
				else
					$item_descrition = $lib_yarn_count[$row[csf("count_id")]]." ".$composition[$row[csf("copm_one_id")]]." ".$row[csf("percent_one")]."% ".$composition[$row[csf("copm_two_id")]]." ".$row[csf("percent_two")]."% ".$color_library[$row[csf("color")]]." ".$yarn_type[$row[csf("type_id")]];
				$rowcons_qnty = $yarn_data_array[$row[csf("count_id")]][$row[csf("copm_one_id")]][$row[csf("percent_one")]][$row[csf("type_id")]][$row[csf("color")]][$row[csf("rate")]]['qty'];
				$rowavgcons_qnty = $yarn_data_array[$row[csf("count_id")]][$row[csf("copm_one_id")]][$row[csf("percent_one")]][$row[csf("type_id")]][$row[csf("color")]][$row[csf("rate")]]['qty'];
				$rowamount = $yarn_data_array[$row[csf("count_id")]][$row[csf("copm_one_id")]][$row[csf("percent_one")]][$row[csf("type_id")]][$row[csf("color")]][$row[csf("rate")]]['amount'];
				if(is_infinite($rowamount) || is_nan($rowamount)){$rowamount=0;}
				?>   
				<tr>
                    <td align="left"><? echo $item_descrition; ?></td>
                    <td align="right"><? echo fn_number_format($row[csf("cons_qnty")],3); ?></td>
                    <td align="right"><? echo fn_number_format($rowcons_qnty,2); ?></td>
                    
                    <td align="right"><? if($row[csf("rate")]>0){ echo fn_number_format($row[csf("rate")],3);} ?></td>
                    <td align="right" style="background: yellow"><? if($rowamount>0){ echo fn_number_format($rowamount,2);} ?></td>
                    <td align="right"><? 
                    $cv=($row[csf("amount")]/$price_dzn)*100;
                    if(is_infinite($cv) || is_nan($cv)){$cv=0;}
                    if($cv>0){echo fn_number_format($cv,2); }
                    ?></td>
				</tr>
				<?  
				$total_yarn_qty+=$rowcons_qnty;
				$total_yarn_qty_dzn+=$row[csf("cons_qnty")];
				$total_avg_yarn_qty+=$rowavgcons_qnty;
				$total_yarn_amount +=$rowamount;
				$total_yarn_cost_dzn+=$row[csf("amount")];
				$total_yarn_avg_cons_qty+=$rowavgcons_qnty;
				$total_yarn_cost_kg=$total_yarn_amount/$total_yarn_qty;
				if(is_infinite($total_yarn_cost_kg) || is_nan($total_yarn_cost_kg)){$total_yarn_cost_kg=0;}
            }
            ?>
            <tr class="rpt_bottom" style="font-weight:bold">
                <td>Yarn Total</td>
                <td align="right"><? if($total_yarn_qty_dzn>0){ echo fn_number_format($total_yarn_qty_dzn,4); } ?></td>
                <td align="right"><? if($total_yarn_qty>0){ echo fn_number_format($total_yarn_qty,2); } ?></td>                    
                <td></td>
                <td align="right" bgcolor="yellow"><? if($total_yarn_amount>0){ echo '&dollar;'.fn_number_format($total_yarn_amount,2); } ?></td>
                <td align="right"><? 
                $cv=($total_yarn_cost_dzn/$price_dzn)*100;
                if(is_infinite($cv) || is_nan($cv)){$cv=0;}
                if($cv>0){ echo fn_number_format($cv,2).' %';  }
                ?></td>
            </tr>
        </table>
    </div>
    <?
    //End Yarn Cost part report here -------------------------------------------

	//start Trims Cost part report here -------------------------------------------
	$supplier_library_fabric=return_library_array( "select a.id, a.supplier_name from lib_supplier a where a.is_deleted=0  and a.status_active=1 group by a.id,a.supplier_name order by a.supplier_name", "id", "supplier_name");
  
    $sql = "select pre_cost_trim_cost_dtls_id as id, job_no, trim_group,description,brand_sup_ref, cons_uom, cons_dzn_gmts, rate, amount, apvl_req, nominated_supp_multi, status_active from wo_pre_cost_trim_cost_dtls_his  where job_no='".$txt_job_no."' and approved_no=$revised_no and status_active=1 and is_deleted=0";
    $data_array=sql_select($sql);
  ?>
    <div style="margin-top:15px">
        <table class="rpt_table" border="1" cellpadding="1" cellspacing="1" style="width:970px;text-align:center;font-family: 'Arial Narrow', Arial, sans-serif;" rules="all">
            <label  style="float:left;background:#CCCCCC; font-size:larger"><b>Trims Details</b> </label> 
            <tr style="font-weight:bold; background: #D7ECD9" >
                <td width="110" style="background: #D7ECD9">Item Group</td>
                <td width="110" style="background: #D7ECD9">Item Description</td>
                <td width="100" style="background: #D7ECD9">Supplier</td>
                <td width="60" style="background: #D7ECD9">UOM</td>
                <td width="80" style="background: #D7ECD9">Cons/<?=$costing_for; ?>[Qnty]</td>
                <td width="100" style="background: #D7ECD9">TTL Required[Qnty]</td>
                <td width="80" style="background: #D7ECD9">Rate &#36;</td>
                <td width="80" style="background: #D7ECD9">Amount/<?=$costing_for; ?>&#36;</td>
                <td width="80" style="background: yellow">Amount &#36;</td>
                <td width="60" style="background: #D7ECD9">% to Ord. Value</td>
            </tr>
            <?
           // $trim_qty_arr=$trims->getQtyArray_by_precostdtlsid();
            //print_r($trim_qty);
            //$trim_amount_arr=$trims->getAmountArray_precostdtlsid();
            $total_trims_cost=0;  $total_trims_qty=$total_trims_cost_dzn=0;$total_trims_cost_dzn=0;$total_trims_cost_kg=0;
            foreach( $data_array as $row ){ 
				$trim_group=return_library_array( "select item_name,id from  lib_item_group where id=".$row[csf("trim_group")], "id", "item_name" ); 
				$cons_dzn_gmts= $row[csf("cons_dzn_gmts")];
				$amount_dzn= $row[csf("amount")];
				$pre_trims_qty=$trim_qty_arr[$row[csf("id")]];
				$pre_trims_amount=$trim_amount_arr[$row[csf("id")]];  
				
				$nominated_supp_str="";
				$exsupp=explode(",",$row[csf("nominated_supp_multi")]);
				foreach($exsupp as $sid)
				{
					if($nominated_supp_str=="") $nominated_supp_str=$supplier_library_fabric[$sid]; else $nominated_supp_str.=','.$supplier_library_fabric[$sid];
				}            
				?>   
				<tr>
                    <td align="left"><? echo $trim_group[$row[csf("trim_group")]]; ?></td>
                    <td align="left"><? echo $row[csf("description")]; ?></td>
                    <td align="left"><?=$nominated_supp_str; ?></td>
                    <td align="center"><? echo $unit_of_measurement[$row[csf("cons_uom")]]; ?></td>
                    <td align="right"><? echo fn_number_format($cons_dzn_gmts,3); ?></td>
                    <td align="right"><? echo fn_number_format($pre_trims_qty,4); ?></td>
                    <td align="right"><? echo fn_number_format($row[csf("rate")],3); ?></td>
                    <td align="right"><? echo fn_number_format($amount_dzn,4); ?></td>
                    <td align="right" style="background: yellow"><? echo fn_number_format($pre_trims_amount,2); ?></td>
                    <td align="right"  title="<? echo $amount_dzn.'='.$price_dzn;?>">
                    <? 
                    $cv=($amount_dzn/$price_dzn)*100;
                    if(is_infinite($cv) || is_nan($cv)){$cv=0;}
                    echo fn_number_format($cv,2); 
                    //echo fn_number_format(($amount_dzn/$price_dzn)*100,2); 
                    ?></td>
				</tr>
				<?
				$total_trims_cost += $pre_trims_amount;
				$total_trims_cost_dzn += $amount_dzn;
				$total_trims_qty += $pre_trims_qty;
            }
            ?>
            <tr class="rpt_bottom" style="font-weight:bold" >
                <td>Trims Total</td>
                <td colspan="4"></td>
                <td align="right"><? if($total_trims_qty>0){ echo fn_number_format($total_trims_qty,4); } ?></td>
                <td align="right"><? //echo fn_number_format($total_trims_cost_dzn,4); ?></td>                   
                
                <td align="right"><? if($total_trims_cost_dzn>0){ echo '&dollar;'.fn_number_format($total_trims_cost_dzn,4); } ?></td>
                <td align="right" style="background: yellow"><? if($total_trims_cost>0){ echo '&dollar;'.fn_number_format($total_trims_cost,2); } ?></td>
                <td align="right" title="<? echo $total_trims_cost_dzn.'='.$price_dzn;?>">
                <? 
                $cv=($total_trims_cost_dzn/$price_dzn)*100;
                if(is_infinite($cv) || is_nan($cv)){$cv=0;}
                if($cv){ echo fn_number_format($cv,2).' %'; }
                ?>
                </td>
            </tr>                
        </table>
    </div>
	<?
    $pre_cost_dtls_arr = sql_select("SELECT pre_cost_dtls_id as id, job_no, costing_per_id, order_uom_id, fabric_cost, fabric_cost_percent, trims_cost, depr_amor_pre_cost, deffdlc_cost, studio_cost, design_cost, trims_cost_percent, embel_cost, embel_cost_percent, comm_cost, comm_cost_percent, commission, incometax_cost, interest_cost, interest_percent, commission_percent, lab_test, lab_test_percent, inspection, inspection_percent, cm_cost, cm_cost_percent, freight, freight_percent, common_oh, common_oh_percent, design_percent, studio_percent, currier_pre_cost, currier_percent, certificate_pre_cost, certificate_percent, total_cost, total_cost_percent, price_dzn, price_dzn_percent, margin_dzn, margin_dzn_percent, price_pcs_or_set, price_pcs_or_set_percent, margin_pcs_set, margin_pcs_set_percent, cm_for_sipment_sche from wo_pre_cost_dtls_histry where job_no='".$txt_job_no."' and approved_no=$revised_no and status_active=1 and is_deleted=0");
    foreach ($pre_cost_dtls_arr as $row) {
		$price_dzn=$row[csf("price_dzn")];
		$lab_test_dzn=$row[csf("lab_test")];
		$commission_cost_dzn=$row[csf("commission")];
		$commercial_cost_dzn = $row[csf("comm_cost")];
		
		$inspection_dzn=$row[csf("inspection")];
		$cm_cost_dzn =$row[csf("cm_cost")];
		$common_oh_dzn =$row[csf("common_oh")];
		$freight_dzn =$row[csf("freight")];
		$currier_pre_cost_dzn = $row[csf("currier_pre_cost")];
		$certificate_pre_cost_dzn = $row[csf("certificate_pre_cost")];
		$deffdlc_cost_dzn = $row[csf("deffdlc_cost")];
		$depr_amor_pre_cost_dzn = $row[csf("depr_amor_pre_cost")];
		$interest_cost_dzn=$row[csf("interest_cost")];
		$interest_cost_percent=$row[csf("interest_percent")];
		$incometax_cost_dzn=$row[csf("incometax_cost")];
		$studio_cost_dzn=$row[csf("studio_cost")];
		$design_cost_dzn=$row[csf("design_cost")];        
		$studio_cost_percent=$row[csf("studio_percent")];
		$design_cost_percent=$row[csf("design_percent")]; 
		
		$other_cost_per = $inspection_dzn+$freight_dzn+$certificate_pre_cost_dzn+$deffdlc_cost_dzn+$design_cost_dzn+$studio_cost_dzn+$common_oh_dzn+$interest_cost_dzn+$incometax_cost_dzn+$depr_amor_pre_cost_dzn;
    }      
     ?>
    <table  class="rpt_table"  border="1" align="left" cellpadding="0" width="350" cellspacing="0" rules="all" style="margin-top: 10px;font-family: 'Arial Narrow', Arial, sans-serif;">
        <tr style="background: #D7ECD9">
            <th>MISC/Others Cost</th>
            <th>%</th>
            <th>TTL Cost $</th>
        </tr>
        <tr>
            <td>Test cost</td>
            <td align="right"><?
            $lab_test_per=($other_costing_arr[$job_id]['lab_test']/$order_values)*100;
            if(is_infinite($lab_test_per) || is_nan($lab_test_per)) $lab_test_per=0;
            
            if($lab_test_per>0){echo fn_number_format($lab_test_per,2);}
            $total_misc_per += $lab_test_per;
            ?></td>
            <th align="right"><? if($other_costing_arr[$job_id]['lab_test']>0){ echo fn_number_format($other_costing_arr[$job_id]['lab_test'],2);} ?></th>
        </tr>
        <tr>
            <td>Buying commission</td>
            <td align="right"><?
            $commission_cost_per=($other_costing_arr[$job_id]['commission']/$order_values)*100;
            if(is_infinite($commission_cost_per) || is_nan($commission_cost_per)) $commission_cost_per=0;
            
            if($commission_cost_per>0){ echo fn_number_format($commission_cost_per,2);}
            $total_misc_per +=$commission_cost_per;
            ?></td>
            <th align="right"><? if($other_costing_arr[$job_id]['commission']>0){ echo fn_number_format($other_costing_arr[$job_id]['commission'],2);} ?></th>
        </tr>
        <tr>
            <td>Commercial cost</td>
            <td align="right"><?
            $commercial_cost_per=($other_costing_arr[$job_id]['comm_cost']/$order_values)*100;
            if(is_infinite($commercial_cost_per) || is_nan($commercial_cost_per)) $commercial_cost_per=0;
            
            if($commercial_cost_per>0){ echo fn_number_format($commercial_cost_per,2); }
            $total_misc_per +=$commercial_cost_per;
            ?>            
            </td>
            <th align="right"><? if($other_costing_arr[$job_id]['comm_cost']>0) { echo fn_number_format($other_costing_arr[$job_id]['comm_cost'],2);} ?></th>
        </tr>
        <tr>
            <td>Other costs</td>
            <td align="right"><?
            $other_cost_per=($total_other_cost/$order_values)*100;
            if(is_infinite($other_cost_per) || is_nan($other_cost_per)) $other_cost_per=0;
            
            if($other_cost_per>0){ echo fn_number_format($other_cost_per,2);}
            $total_misc_per +=$other_cost_per;
            ?>            
            </td>
            <th align="right"><? if($total_other_cost>0){ echo fn_number_format($total_other_cost,2);} ?></th>
        </tr>
        <tr>
            <th>MISC/Others Cost Sub Total</th>
            <th align="right"><? if($total_misc_per>0){ echo fn_number_format($total_misc_per,2).'%'; }  ?></th>
            <th align="right"><? if($misc_cost>0){ echo '&dollar;'.fn_number_format($misc_cost,2); } ?></th>
        </tr>
    </table>
    <div id="div_size_color_matrix" style="float:left; max-width:1000; font-family: 'Arial Narrow', Arial, sans-serif;">
        <fieldset id="div_size_color_matrix" style="max-width:1000;">
			<?
            $color_library=return_library_array( "select id, color_name from lib_color where status_active=1 and is_deleted=0", "id", "color_name");
            $size_library=return_library_array( "select id, size_name from lib_size where status_active=1 and is_deleted=0", "id", "size_name");
            $nameArray_size=sql_select( "select  size_number_id, min(color_size_id) as id,  min(size_order) as size_order from wo_po_color_size_his where po_break_down_id in(".$txt_order_no_id.") and  job_no_mst='$txt_job_no' and approved_no=$revised_no and is_deleted=0 and status_active=1 group by size_number_id order by size_order");
            //echo "select  size_number_id,min(id) as id, min(size_order) as size_order from wo_po_color_size_breakdown where po_break_down_id in(".$txt_order_no_id.") and  job_no_mst=$txt_job_no and is_deleted=0 and status_active=1 group by size_number_id order by size_order"; die;
            ?>
            <legend>Size and Color Breakdown</legend>
                <table class="rpt_table"  border="1" align="left" cellpadding="0" width="750" cellspacing="0" rules="all" >
                    <tr>
                        <td style="border:1px solid black"><strong>Color/Size</strong></td>
                        <?          
                        foreach($nameArray_size  as $result_size)
                        { ?>
                        <td align="center" style="border:1px solid black"><strong><? echo $size_library[$result_size[csf('size_number_id')]];?></strong></td>
                        <? } ?>       
                        <td style="border:1px solid black; width:130px" align="center"><strong> Total Order Qty(Pcs)</strong></td>
                        <td style="border:1px solid black; width:80px" align="center"><strong> Excess %</strong></td>
                        <td style="border:1px solid black; width:130px" align="center"><strong> Total Plan Cut Qty(Pcs)</strong></td>
                    </tr>
                    <?
                    $color_size_order_qnty_array=array(); $color_size_qnty_array=array();  $size_tatal=array(); $size_tatal_order=array();
                    for($c=0;$c<count($gmts_item); $c++)
                    {
						$item_size_tatal=array(); $item_size_tatal_order=array(); $item_grand_total=0; $item_grand_total_order=0;
						$nameArray_color=sql_select( "select color_number_id, min(color_size_id) as id,min(color_order) as color_order from wo_po_color_size_his where item_number_id=$gmts_item[$c] and po_break_down_id in(".$txt_order_no_id.")  and approved_no=$revised_no and is_deleted=0 and status_active=1 group by color_number_id order by color_order");
						?>
						<tr>
							<td style="border:1px solid black" colspan="<? echo count($nameArray_size)+3;?>"><strong><? echo $garments_item[$gmts_item[$c]];?></strong></td>
						</tr>
						<?
						foreach($nameArray_color as $result_color)
						{           
							?>
							<tr>
                                <td align="center" style="border:1px solid black"><? echo $color_library[$result_color[csf('color_number_id')]]; // echo $row_num_tr; ?></td>
                                <? 
                                $color_total=0; $color_total_order=0;
                                foreach($nameArray_size  as $result_size)
                                {
									$nameArray_color_size_qnty=sql_select( "select sum(plan_cut_qnty) as plan_cut_qnty, sum(order_quantity) as  order_quantity from wo_po_color_size_his where  po_break_down_id in(".str_replace("'","",$txt_order_no_id).") and  size_number_id =".$result_size[csf('size_number_id')]." and color_number_id =".$result_color[csf('color_number_id')]." and approved_no=$revised_no and item_number_id=$gmts_item[$c] and  status_active=1 and is_deleted =0");                          
									foreach($nameArray_color_size_qnty as $result_color_size_qnty)
									{
										?>
										<td style="border:1px solid black; text-align:right">
										<? 
										if($result_color_size_qnty[csf('plan_cut_qnty')]!= "")
										{
											echo fn_number_format($result_color_size_qnty[csf('order_quantity')],0);
											$color_total += $result_color_size_qnty[csf('plan_cut_qnty')] ;
											$color_total_order += $result_color_size_qnty[csf('order_quantity')] ;
											$item_grand_total+=$result_color_size_qnty[csf('plan_cut_qnty')];
											$item_grand_total_order+=$result_color_size_qnty[csf('order_quantity')];
											$grand_total +=$result_color_size_qnty[csf('plan_cut_qnty')];
											$grand_total_order +=$result_color_size_qnty[csf('order_quantity')];
											
											$color_size_qnty_array[$result_size[csf('size_number_id')]][$result_color[csf('color_number_id')]]=$result_color_size_qnty[csf('plan_cut_qnty')];
											$color_size_order_qnty_array[$result_size[csf('size_number_id')]][$result_color[csf('color_number_id')]]=$result_color_size_qnty[csf('order_quantity')];
											if (array_key_exists($result_size[csf('size_number_id')], $size_tatal))
											{
												$size_tatal[$result_size[csf('size_number_id')]]+=$result_color_size_qnty[csf('plan_cut_qnty')];
												$size_tatal_order[$result_size[csf('size_number_id')]]+=$result_color_size_qnty[csf('order_quantity')];
											}
											else
											{
												$size_tatal[$result_size[csf('size_number_id')]]=$result_color_size_qnty[csf('plan_cut_qnty')]; 
												$size_tatal_order[$result_size[csf('size_number_id')]]=$result_color_size_qnty[csf('order_quantity')]; 
											}
											if (array_key_exists($result_size[csf('size_number_id')], $item_size_tatal))
											{
												$item_size_tatal[$result_size[csf('size_number_id')]]+=$result_color_size_qnty[csf('plan_cut_qnty')];
												$item_size_tatal_order[$result_size[csf('size_number_id')]]+=$result_color_size_qnty[csf('order_quantity')];
											}
											else
											{
												$item_size_tatal[$result_size[csf('size_number_id')]]=$result_color_size_qnty[csf('plan_cut_qnty')]; 
												$item_size_tatal_order[$result_size[csf('size_number_id')]]=$result_color_size_qnty[csf('order_quantity')]; 
											}
										}
										else echo " ";
										?>
										</td>
										<?   
									}
                                }
                                ?>
                                <td style="border:1px solid black; text-align:right"><? if(round($color_total_order)>0){ echo fn_number_format(round($color_total_order),0);} ?></td>
                                <td style="border:1px solid black; text-align:right"><? $excexss_per=($color_total-$color_total_order)/$color_total_order*100; if(round($excexss_per)>0){ echo fn_number_format($excexss_per,2)." %";} ?></td>
                                <td style="border:1px solid black; text-align:right"><? if(round($color_total)>0){ echo fn_number_format(round($color_total),0);} ?></td>
                            </tr>
                            <?
						}
						?>
                        <tr>
                            <td align="center" style="border:1px solid black"><strong>Sub Total</strong></td>
                            <?
                            foreach($nameArray_size  as $result_size)
                            {
								?><td style="border:1px solid black;  text-align:right"><? echo $item_size_tatal_order[$result_size[csf('size_number_id')]];  ?></td><?
                            }
                            ?>
                            <td  style="border:1px solid black;  text-align:right"><? if(round($item_grand_total_order)>0){ echo fn_number_format(round($item_grand_total_order),0); } ?></td>
                            <td  style="border:1px solid black;  text-align:right"><? $excess_item_gra_tot=($item_grand_total-$item_grand_total_order)/$item_grand_total_order*100; if($excess_item_gra_tot>0){echo fn_number_format($excess_item_gra_tot,2)." %"; } ?></td>
                            <td  style="border:1px solid black;  text-align:right"><?  if(round($item_grand_total)>0){echo fn_number_format(round($item_grand_total),0); } ?></td>
						</tr>
						<?
                    }
                    ?>
                    <tr>
                    	<td style="border:1px solid black" align="center" colspan="<? echo count($nameArray_size)+3; ?>"><strong>&nbsp;</strong></td>
                    </tr>
                    <tr>
                        <td align="center" style="border:1px solid black"><strong>Grand Total</strong></td>
                        <?
                        foreach($nameArray_size  as $result_size)
                        {
                        	?><td style="border:1px solid black;  text-align:right"><? echo $size_tatal_order[$result_size[csf('size_number_id')]]; ?></td><?
                        }
                        ?>
                        <td style="border:1px solid black;  text-align:right"><? if(round($grand_total_order)>0){ echo fn_number_format(round($grand_total_order),0); } ?></td>
                        <td style="border:1px solid black;  text-align:right"><? $excess_gra_tot= ($grand_total-$grand_total_order)/$grand_total_order*100; if($excess_gra_tot>0) { echo fn_number_format($excess_gra_tot,2)." %"; } ?></td>
                        <td style="border:1px solid black;  text-align:right"><?  if(round($grand_total)>0) { echo fn_number_format(round($grand_total),0); } ?></td>
                    </tr>
            </table>
        </fieldset>
    </div>
    <br/><br/>
    <div>
    <br/>
	<?
    $width=990; $padding_top = 70; $prepared_by='';
    $sql = sql_select("select designation,name,activities,prepared_by from variable_settings_signature where report_id=109 and company_id=$cbo_company_name order by sequence_no");
    
    if($sql[0][csf("prepared_by")]==1){
		list($prepared_by,$activities)=explode('**',$prepared_by);
		$sql_2[100] = array ( DESIGNATION => 'Prepared By' ,NAME => $prepared_by, ACTIVITIES =>$activities, PREPARED_BY => 0 );
		$sql=$sql_2+$sql;
    }
    
    $count = count($sql);
    $td_width = floor($width / $count);
    $standard_width = $count * 120;
    if ($standard_width > $width) $td_width = 120;
    
	$no_coloumn_per_tr = floor($width / $td_width);
	$i = 1;
	if ($count == 0) {$message = "<b>Note: This is Software Generated Copy , Signature is not Required.</b>";}
	echo '<table id="signatureTblId" width="' . $width . '" style="padding-top:' . $padding_top . 'px;"><tr><td width="100%" height="' . $padding_top . '" colspan="' . $count . '">' . $message . '</td></tr><tr>';
	foreach ($sql as $row) {
		echo '<td width="' . $td_width . '" align="center" valign="top">
		<strong>' . $row[csf("activities")] . '</strong><br>
		<strong style="text-decoration:overline">' . $row[csf("designation")] . "</strong><br>" . $row[csf("name")] . '</td>';
		if ($i % $no_coloumn_per_tr == 0) {
			echo '</tr><tr><td width="100%" height="70" colspan="' . $no_coloumn_per_tr . '"></td></tr>';
		}
		$i++;
	}
	echo '</tr></table>';
	?>
	</div>
	<?
	disconnect($con);
	exit();
}

if($action=="budgetshee__________")
{
  ///extract($_REQUEST);
  $process = array( &$_POST );
  extract(check_magic_quote_gpc( $process )); 
  $txt_costing_date=change_date_format(str_replace("'","",$txt_costing_date),'yyyy-mm-dd','-');
  if($txt_job_no=="") $job_no=''; else $job_no=" and a.job_no='".$txt_job_no."'";
  if($cbo_company_name=="") $company_name=''; else $company_name=" and a.company_name=".$cbo_company_name."";
  if($cbo_buyer_name=="") $cbo_buyer_name=''; else $cbo_buyer_name=" and a.buyer_name=".$cbo_buyer_name."";
  if($txt_style_ref=="") $txt_style_ref=''; else $txt_style_ref=" and a.style_ref_no='".$txt_style_ref."'";
  if($txt_costing_date=="") $txt_costing_date=''; else $txt_costing_date=" and c.costing_date='".$txt_costing_date."'";
  $txt_po_breack_down_id=str_replace("'",'',$txt_po_breack_down_id);
  if(str_replace("'",'',$txt_po_breack_down_id)=="") 
  {
    $txt_po_breack_down_id_cond='';  $txt_po_breack_down_id_cond1='';  $txt_po_breack_down_id_cond2='';  $txt_po_breack_down_id_cond3=''; 
  }
  else
  {
    $txt_po_breack_down_id_cond=" and b.id in(".$txt_po_breack_down_id.")";
    $txt_po_breack_down_id_cond1=" and id in(".$txt_po_breack_down_id.")";
    $txt_po_breack_down_id_cond2=" and po_break_down_id in(".$txt_po_breack_down_id.")";
    $txt_po_breack_down_id_cond3=" and b.id in(".$txt_po_breack_down_id.")";
  }
  
  //array for display name
  $buyer_arr=return_library_array( "select id, buyer_name from lib_buyer",'id','buyer_name');
  $sesson_arr=return_library_array( "select id, season_name from lib_buyer_season",'id','season_name');
  $brand_arr=return_library_array( "select id, brand_name from lib_buyer_brand",'id','brand_name');
  $comp=return_library_array( "select id, company_name from lib_company",'id','company_name');
  $color_library=return_library_array( "select id, color_name from lib_color where status_active=1 and is_deleted=0", "id", "color_name");
  $fabric_composition_arr=return_library_array( "select id, fabric_composition_name from lib_fabric_composition",'id','fabric_composition_name');
  //$imge_arr=return_library_array( "select master_tble_id,image_location from   common_photo_library where form_name='company_details' and file_type=1",'master_tble_id','image_location');

   $photo_data_array = sql_select("SELECT id,master_tble_id,image_location from common_photo_library where master_tble_id='$txt_job_no' and file_type=1  and rownum=1");
  
  if($db_type==0) $group_gsm="group_concat( distinct b.gsm_weight) AS gsm_weight";
  if($db_type==2) $group_gsm="listagg(b.gsm_weight ,',') within group (order by b.gsm_weight) AS gsm_weight";
  
  $gsm_weight_top=return_field_value("$group_gsm", "lib_body_part a,wo_pre_cost_fabric_cost_dtls b", "a.id=b.body_part_id and b.job_no='$txt_job_no' and b.status_active=1 and b.is_deleted=0 and a.body_part_type in(1,20)","gsm_weight");
  //$gsm_weight_bottom=return_field_value("$group_gsm", "lib_body_part a,wo_pre_cost_fabric_cost_dtls b", "a.id=b.body_part_id and b.job_no=$txt_job_no and a.body_part_type=20 ","gsm_weight");
  //echo $gsm_weight_bottom.'DD';
  $gmtsitem_ratio_array=array();
  $grmnt_items = "";
    $grmts_sql = sql_select("select job_no,gmts_item_id,set_item_ratio from wo_po_details_mas_set_details where job_no='$txt_job_no'");
  foreach($grmts_sql as $key=>$val)
  {
    $grmnt_items .=$garments_item[$val[csf("gmts_item_id")]].",";
    $gmtsitem_ratio_array[$val[csf('job_no')]][$val[csf('gmts_item_id')]]=$val[csf('set_item_ratio')];
    $set_item_ratio += $val[csf('set_item_ratio')]; 
  }
  $grmnt_items = rtrim($grmnt_items,","); 
  
  if($db_type==0) ///fab_knit_fin_req_kg,fab_knit_req_kg
  { 
     $sql = "SELECT a.job_no,a.company_name, a.buyer_name,a.total_set_qnty,a.style_ref_no,a.ship_mode, a.gmts_item_id,a.order_uom, a.avg_unit_price,sum(b.plan_cut) as job_quantity,sum(b.po_quantity) as ord_qty ,   c.costing_per,c.budget_minute,c.costing_date,c.approved,c.exchange_rate ,a.quotation_id,c.incoterm,c.sew_effi_percent,group_concat(b.sc_lc) as sc_lc, d.fab_knit_req_kg,d.fab_knit_fin_req_kg, d.fab_woven_req_yds,d.fab_woven_fin_req_yds, d.fab_yarn_req_kg
      from wo_po_details_master a,wo_po_break_down b, wo_pre_cost_mst_histry c left join wo_pre_cost_sum_dtls_histroy d on   c.job_no=d.job_no and d.status_active=1 and d.is_deleted=0 and c.approved_no=$revised_no and d.approved_no=$revised_no
      where a.job_no=b.job_no_mst and b.job_no_mst=c.job_no  and a.status_active=1 and a.is_deleted =0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0   and c.approved_no =$revised_no and d.approved_no =$revised_no $job_no $txt_po_breack_down_id_cond $company_name $cbo_buyer_name $txt_style_ref $txt_costing_date group by a.job_no,a.company_name, a.buyer_name,a.style_ref_no, a.gmts_item_id,a.order_uom, a.avg_unit_price, c.costing_per,c.approved,c.budget_minute,c.incoterm,c.sew_effi_percent, d.fab_knit_req_kg, d.fab_woven_req_yds,d.fab_knit_fin_req_kg,d.fab_woven_fin_req_yds, d.fab_yarn_req_kg order by a.job_no"; 
  }
  else if($db_type==2)
  { 
    $sql = "SELECT a.job_no,a.company_name, a.buyer_name,a.ship_mode,a.total_set_qnty,a.style_ref_no, a.gmts_item_id,a.order_uom, a.avg_unit_price, a.product_dept, a.season_buyer_wise, a.brand_id, a.style_description, a.job_quantity as job_qty, sum(b.plan_cut) as job_quantity, sum(b.po_quantity) as ord_qty, listagg(cast(b.sc_lc as varchar2(4000)),',') within group (order by b.sc_lc) as sc_lc, c.costing_per,c.costing_date,c.budget_minute,c.approved,a.quotation_id,c.exchange_rate ,c.incoterm,c.sew_effi_percent, c.remarks,c.sew_smv, '' as refusing_cause, d.fab_knit_fin_req_kg, d.fab_knit_req_kg, d.fab_woven_req_yds,d.fab_woven_fin_req_yds, d.fab_yarn_req_kg
    from wo_po_details_master a, wo_po_break_down b, wo_pre_cost_mst_histry c left join  wo_pre_cost_sum_dtls_histroy d on  c.job_no=d.job_no and d.status_active=1 and d.is_deleted=0 and c.approved_no=$revised_no and d.approved_no=$revised_no
    where a.job_no=b.job_no_mst and b.job_no_mst=c.job_no  and a.status_active=1 and a.is_deleted =0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and c.approved_no =$revised_no and d.approved_no = $revised_no $job_no $txt_po_breack_down_id_cond $company_name $cbo_buyer_name $txt_style_ref group by a.job_no,a.company_name, a.buyer_name,a.style_ref_no, a.gmts_item_id,a.order_uom,a.ship_mode, a.avg_unit_price, a.product_dept, c.incoterm,c.costing_date,c.exchange_rate ,a.quotation_id,c.costing_per,c.sew_effi_percent,c.approved,c.budget_minute, d.fab_knit_req_kg, d.fab_woven_req_yds,d.fab_knit_fin_req_kg,d.fab_woven_fin_req_yds, d.fab_yarn_req_kg,a.job_quantity,a.season_buyer_wise, a.brand_id, a.total_set_qnty,a.style_description, c.remarks,c.sew_smv  order by a.job_no"; //a.job_quantity as job_quantity,
  }
 //echo $sql;die;
  $data_array=sql_select($sql);
  $plan_cut_qty=$data_array[0][csf('job_quantity')];
  $total_set_qnty=$data_array[0][csf('total_set_qnty')];
  $exchange_rate=$data_array[0][csf('exchange_rate')];
  $is_approved=return_field_value("approved", "wo_pre_cost_mst", "job_no='$txt_job_no'  and  status_active=1 and is_deleted=0"); 
  
  $preCost_histry=sql_select( "SELECT min(a.sew_smv) as sew_smv,min(a.sew_effi_percent) as sew_effi_percent,min(b.margin_dzn_percent) as margin_dzn_percent ,min(b.fabric_cost_percent) as  fabric_cost_percent,min(b.trims_cost_percent ) as trims_cost_percent,min(b.embel_cost_percent) as embel_cost_percent,min(b.wash_cost_percent) as wash_cost_percent ,min(b.comm_cost_percent) as comm_cost_percent ,min(b.commission_percent) as commission_percent,min(b.lab_test_percent) as lab_test_percent,min(b.inspection_percent) as inspection_percent,min(b.cm_cost_percent) as cm_cost_percent,min(b.freight_percent) as freight_percent,min(b.currier_percent) as currier_percent,min(b.certificate_percent) as certificate_percent,min(b.common_oh_percent) as common_oh_percent    from  wo_pre_cost_mst_histry a, wo_pre_cost_dtls_histry b where a.job_no=b.job_no and a.job_no='$txt_job_no' and a.approved_no=$revised_no and b.approved_no=$revised_no"); 
  
  
  list($preCost_histry_row)=$preCost_histry;
  $opert_profitloss_percent=$preCost_histry_row[csf('margin_dzn_percent')];
  $fabric_cost_percent=$preCost_histry_row[csf('fabric_cost_percent')];
  $trims_cost_percent=$preCost_histry_row[csf('trims_cost_percent')];
  $embel_cost_percent=$preCost_histry_row[csf('embel_cost_percent')];
  $wash_cost_percent=$preCost_histry_row[csf('wash_cost_percent')];
  $comm_cost_percent=$preCost_histry_row[csf('comm_cost_percent')];
  $commission_percent=$preCost_histry_row[csf('commission_percent')];
  $common_oh_percent=$preCost_histry_row[csf('common_oh_percent')];
  
  $lab_test_percent=$preCost_histry_row[csf('lab_test_percent')];
  $inspection_percent=$preCost_histry_row[csf('inspection_percent')];
  $cm_cost_percent=$preCost_histry_row[csf('cm_cost_percent')];
  $freight_percent=$preCost_histry_row[csf('freight_percent')];
  $currier_percent=$preCost_histry_row[csf('currier_percent')];
  $certificate_percent=$preCost_histry_row[csf('certificate_percent')];
  //$currier_percent=$preCost_histry_row[csf('currier_percent')];
  $sew_effi_percent=$data_array[0][csf('sew_effi_percent')];//
  $hissew_effi_percent=$preCost_histry_row[csf('sew_effi_percent')];
  //$sew_smv=$preCost_histry_row[csf('sew_smv')];
  $first_app_date="";
  $last_app_date="";
  $preCost_approved=sql_select( "select max(b.approved_no) as approved_no, min(b.approved_date) as first_app_date, max(b.approved_date) as last_app_date,a.id from wo_pre_cost_mst a, approval_history b where   a.id=b.mst_id and a.job_no='$txt_job_no' and b.entry_form=15 group by a.id"); 
  //echo  "select max(b.approved_no) as approved_no, min(b.approved_date) as first_app_date, max(b.approved_date) as last_app_date,a.id from wo_pre_cost_mst a, approval_history b where   a.id=b.mst_id and a.job_no=$txt_job_no and b.entry_form=15 group by a.id";
  //echo  "select max(b.approved_no) as approved_no, min(b.approved_date) as first_app_date, max(b.approved_date) as last_app_date,a.id from wo_pre_cost_mst a, approval_history b where b.un_approved_by>0 and  a.id=b.mst_id and a.job_no=$txt_job_no and b.entry_form=15 group by a.id";
  
  if(count($preCost_approved)>0)
  {
    foreach($preCost_approved as $preCost_approved_row)
    {
      $approved_no_row=$preCost_approved_row[csf('approved_no')];
      $fst_date=$preCost_approved_row[csf('first_app_date')];
      $fstapp_date=$fst_date[0];
      
      $last_date=$preCost_approved_row[csf('last_app_date')];
      $lstapp_date=$last_date[0];
      $precost_id=$preCost_approved_row[csf('id')];
    }
  }
  
  $img_path = (str_replace("'", "", $img_path))? str_replace("'", "", $img_path):'../../';
  //echo $img_path;
  $costing_date=$data_array[0][csf('costing_date')];
  if(is_infinite($costing_date) || is_nan($costing_date)){$costing_date=0;}

  $pre_cost_dtls = "SELECT id,job_no,costing_per_id,order_uom_id,fabric_cost,fabric_cost_percent,trims_cost,trims_cost_percent,embel_cost,embel_cost_percent,comm_cost,comm_cost_percent,commission,commission_percent,lab_test,lab_test_percent,inspection,inspection_percent,cm_cost,cm_cost_percent,freight,freight_percent,common_oh,common_oh_percent,total_cost,total_cost_percent,price_dzn,price_dzn_percent,margin_dzn,margin_dzn_percent,price_pcs_or_set,price_pcs_or_set_percent,margin_pcs_set,margin_pcs_set_percent,cm_for_sipment_sche from wo_pre_cost_dtls_histry where job_no='".$txt_job_no."' and status_active=1 and is_deleted=0 and approved_no=$revised_no";
   
  $pre_cost_dtls_arr=sql_select($pre_cost_dtls);
  foreach ($pre_cost_dtls_arr as $row) {
    $total_cost = $row[csf("total_cost")];
    $price_dzn = $row[csf("price_dzn")];
  }
  $approval_allow=sql_select("select b.id, b.page_id, b.approval_need, b.allow_partial, b.validate_page,a.setup_date from approval_setup_mst a,approval_setup_dtls b 
      where a.id=b.mst_id and a.company_id=$cbo_company_name and a.status_active=1 and b.page_id=25 and b.status_active=1 and b.is_deleted=0 order by b.id desc ");
	  
  ?>
    <div style="width:972px; margin:0 auto; font-family: 'Arial Narrow', Arial, sans-serif;">
   
      <div style="width:970px; font-size:20px; font-weight:bold">
      <b style="float: left"><? echo $comp[str_replace("'","",$cbo_company_name)]; ?><br>Budget Sheet</b>
      <? if( $is_approved==1){ ?> <b style="left: 50%; margin-left: 240px; color: green;"> This Budget is Approved  </b> <? }elseif( $is_approved==3){
        if($approval_allow[0][csf("approval_need")]==1 && $approval_allow[0][csf("allow_partial")]==1){
          $ap_msg="<b style='left: 50%; margin-left: 240px; color: green;'>This Budget is Approved </b>";
        }else{
          $ap_msg="<b style='left: 50%; margin-left: 240px; color: green;'>This Budget is Partially Approved </b>";
        }
        echo $ap_msg;
    } else{ ?><b style="left: 50%; margin-left: 240px; color: red;">  
        This Budget is Not Approved   
    
    </b> <? } ?>
      <b style="float:right;"> <?  echo 'Budget Date: ';?><? echo  date('d-M-y',strtotime($costing_date)); ?> <br><? echo 'Revised No:'.$revised_no; ?>  </b>
      </div>

      <div style="width:970px; font-size:18px; font-weight:bold">
      <b style="float: left"></b>
      <b style="float:right; font-size:18px; font-weight:bold">   &nbsp;  </b> </div>
    
  <?
  $result =sql_select("select id,po_number,pub_shipment_date,file_no,excess_cut,grouping,po_received_date, plan_cut from wo_po_break_down where job_no_mst='$txt_job_no' $txt_po_breack_down_id_cond1 and status_active=1 and is_deleted=0 order by po_received_date DESC");
  
  $job_in_orders = '';$public_ship_date='';$job_in_ref = '';$job_in_file = '';
    $tot_excess_cut=0;$tot_row=0;
    foreach ($result as $val)
    {
      $job_in_orders .= $val[csf('po_number')].", ";
      $public_ship_date = $val[csf('pub_shipment_date')];
      $po_received_date = $val[csf('po_received_date')];
      $txt_order_no_arr[$val[csf('id')]] = $val[csf('id')];
      if($val[csf('excess_cut')]>0)
      {
        $tot_row++; 
      }
      $tot_excess_cut+= $val[csf('excess_cut')];
      $plancutqty +=$val[csf('plan_cut')];
    }
    $txt_order_no_id=implode(",", $txt_order_no_arr);
 
  foreach ($data_array as $row)
  { 
    $order_price_per_dzn=0;
    $order_job_qnty=0;
    $ord_qty=0;
    $avg_unit_price=0;
    $uom=$row[csf("order_uom")]; 
    $sew_smv=$row[csf("sew_smv")]; 
    $order_values = $row[csf("job_qty")]*$row[csf("avg_unit_price")];   
  
    $job_in_orders = substr(trim($job_in_orders),0,-1);
    if($row[csf("costing_per")]==1){$order_price_per_dzn=12;$costing_for="1 DZN";}
    else if($row[csf("costing_per")]==2){$order_price_per_dzn=1;$costing_for="1 PCS";}
    else if($row[csf("costing_per")]==3){$order_price_per_dzn=24;$costing_for="2 DZN";}
    else if($row[csf("costing_per")]==4){$order_price_per_dzn=36;$costing_for="3 DZN";}
    else if($row[csf("costing_per")]==5){$order_price_per_dzn=48;$costing_for="4 DZN";}
    else {$order_price_per_dzn=0; $costing_for="DZN";}
    $order_job_qnty=$row[csf("job_qty")];
    //$order_qty = $row[csf("job_qty")]*$set_item_ratio;
    $po_no=str_replace("'","",$txt_po_breack_down_id);
	

    $condition= new condition();
    if(str_replace("'","",$txt_job_no) !=''){
        $condition->job_no("='$txt_job_no'");
     }
    
      if(str_replace("'","",$txt_po_breack_down_id)!='')
     {
      $condition->po_id("in($po_no)"); 
     }

    $condition->init();   
    $fabric= new fabric($condition);
    $yarn= new yarn($condition);
    $yarn_costing_arr=$yarn->getJobWiseYarnAmountArray();
    $yarn_qty_amount_arr=$yarn->getJobWiseYarnQtyAndAmountArray();

    $yarnDataWithFabricidArr=$yarn->get_By_Precostfabricdtlsid_YarnQtyAmountArray();

    $fabric= new fabric($condition);
    $fabricAmoutByFabricSource= $fabric->getAmountArray_by_job_knitAndwoven_greyAndfinish();
    $fabricQtyByFabricSource= $fabric->getQtyArray_by_job_knitAndwoven_greyAndfinish_purchase();
    
    $fabric_qty_arr=$fabric->getQtyArray_by_Fabriccostid_knitAndwoven_greyAndfinish();
    $fabric_amount_arr=$fabric->getAmountArray_by_Fabriccostid_knitAndwoven_greyAndfinish();
    $conversion= new conversion($condition);
    $conversion_costing_arr_process=$conversion->getAmountArray_by_job();
    $conv_qty_job_process= $conversion->getQtyArray_by_jobAndProcess();
    $conv_amount_job_process= $conversion->getAmountArray_by_jobAndProcess();
    $con_qty_fabric_process = $conversion->getQtyArray_by_fabricAndProcess();
    $con_amount_fabric_process = $conversion->getAmountArray_by_fabricAndProcess();

    $trims= new trims($condition);
    $trims_costing_arr=$trims->getAmountArray_by_job();
    $trims_qty_arr=$trims->getQtyArray_by_job();

    $emblishment= new emblishment($condition);
    $emblishment_costing_arr=$emblishment->getAmountArray_by_job();
    $emb_qty_job_name_arr = $emblishment->getQtyArray_by_jobAndEmbname();
    $emb_amount_job_name_arr = $emblishment->getAmountArray_by_jobAndEmbname();

    $wash= new wash($condition);
    $emblishment_costing_arr_wash=$wash->getAmountArray_by_job();
    $wash_qty_job_name_arr =$wash->getQtyArray_by_jobAndEmbname();
    $wash_amount_job_name_arr =$wash->getAmountArray_by_jobAndEmbname();


    $commercial= new commercial($condition);
    $commercial_costing_arr=$commercial->getAmountArray_by_job();
    $commission= new commision($condition);
    $commission_costing_arr=$commission->getAmountArray_by_job();
    $other= new other($condition);
    $other_costing_arr=$other->getAmountArray_by_job();
    /*echo '<pre>';
    print_r($fabric_amount_arr); die;*/
    $job_no= str_replace("'","",$txt_job_no);
    $finishing_arr = array('209','165','33','94','63','171','65','170','156','179','200','208','127','125','84','68','128','190','242','240','192','172','90','218','67','197','73','66','185','142');
    $total_finishing_amount=0;
    $total_finishing_qty=0;
    $other_cost_attr = array('inspection','freight','certificate_pre_cost','deffdlc_cost','design_cost','studio_cost','common_oh','interest_cost','incometax_cost','depr_amor_pre_cost');
    $total_other_cost = 0;
    foreach ($other_cost_attr as $attr) {
      $total_other_cost+=$other_costing_arr[$job_no][$attr];
    }
    $misc_cost=$other_costing_arr[$job_no]['lab_test']+$commercial_costing_arr[$job_no]+$commission_costing_arr[$job_no]+$total_other_cost;

    foreach ($finishing_arr as $fid) {
      $total_finishing_amount += array_sum($conv_amount_job_process[$job_no][$fid]);
      $total_finishing_qty += array_sum($conv_qty_job_process[$job_no][$fid]);
    }

    $total_fabic_cost=0;
    if(count($conv_amount_job_process[$job_no][31])>0){
      $total_fabic_cost+=array_sum($conv_amount_job_process[$job_no][31])/array_sum($conv_qty_job_process[$job_no][31]);
    }
    $total_fabric_amount +=array_sum($conv_amount_job_process[$job_no][31]);
    $total_fabric_per +=array_sum($conv_amount_job_process[$job_no][31])/$order_values*100;
    if(count($conv_amount_job_process[$job_no][30])>0){
      $total_fabic_cost+=array_sum($conv_amount_job_process[$job_no][30])/array_sum($conv_qty_job_process[$job_no][30]);
    }
    if($yarn_qty_amount_arr[$job_no]['amount']!=''){
      $total_fabic_cost+=$yarn_qty_amount_arr[$job_no]['amount']/$yarn_qty_amount_arr[$job_no]['qty'];
    }
    $total_fabric_amount +=$yarn_qty_amount_arr[$job_no]['amount']; 
    $total_fabric_per +=$yarn_qty_amount_arr[$job_no]['amount']/$order_values*100;
    if($total_finishing_amount!=0){
      $total_fabic_cost+=$total_finishing_amount/$total_finishing_qty;
    } 
    $total_fabric_amount +=$total_finishing_amount;
    $total_fabric_per +=$total_finishing_amount/$order_values*100;
    $total_fabric_amount +=array_sum($conv_amount_job_process[$job_no][30]);
    $total_fabric_per +=array_sum($conv_amount_job_process[$job_no][30])/$order_values*100;
    if(count($conv_amount_job_process[$job_no][35])>0){
      $total_fabic_cost+=array_sum($conv_amount_job_process[$job_no][35])/array_sum($conv_qty_job_process[$job_no][35]);
    }
    $total_fabric_amount +=array_sum($conv_amount_job_process[$job_no][35]); 
    $total_fabric_per +=array_sum($conv_amount_job_process[$job_no][35])/$order_values*100;
    if(count($conv_amount_job_process[$job_no][1])>0){
      $total_fabic_cost+=array_sum($conv_amount_job_process[$job_no][1])/array_sum($conv_qty_job_process[$job_no][1]);
    }
    $total_fabric_amount +=array_sum($conv_amount_job_process[$job_no][1]);
    $total_fabric_per +=array_sum($conv_amount_job_process[$job_no][1])/$order_values*100; 

    $purchase_amount = array_sum($fabricAmoutByFabricSource['knit']['grey'][$job_no])+array_sum($fabricAmoutByFabricSource['woven']['grey'][$job_no]);
    $purchase_qty = array_sum($fabricQtyByFabricSource['knit']['grey'][$job_no])+array_sum($fabricQtyByFabricSource['woven']['grey'][$job_no]);

    $ather_emb_attr = array(4,5,6,99);
    foreach ($ather_emb_attr as $att) {
      $others_emb_amount += $emb_amount_job_name_arr[$job_no][$att];
      $others_emb_qty += $emb_qty_job_name_arr[$job_no][$att];
    }
    $knitting_amount_summ=''; $dyeing_amount_summ=''; $yds_amount_summ=''; $aop_amount_summ='';
    if(count($conv_amount_job_process[$job_no][1])>0) {
      $knitting_amount_summ = fn_number_format(array_sum($conv_amount_job_process[$job_no][1]),2);
    }
    $yarn_amount_summ = $yarn_qty_amount_arr[$job_no]['amount'];
    $print_amount_summ = $emb_amount_job_name_arr[$job_no][1];    
    $emb_amount_summ= $emb_amount_job_name_arr[$job_no][2];
    $wash_amount_summ = $wash_amount_job_name_arr[$job_no][3];
    if(count($conv_amount_job_process[$job_no][31])>0) {
      $dyeing_amount_summ=  array_sum($conv_amount_job_process[$job_no][31]);
    }
    if(count($conv_amount_job_process[$job_no][30])>0) {
      $yds_amount_summ = array_sum($conv_amount_job_process[$job_no][30]);
    }
    if(count($conv_amount_job_process[$job_no][35])>0) {
      $aop_amount_summ = array_sum($conv_amount_job_process[$job_no][35]);
    }
    
    $total_budget_value = $yarn_amount_summ+$total_finishing_amount+$print_amount_summ+$trims_costing_arr[$job_no]+$yds_amount_summ+$aop_amount_summ+$emb_amount_summ+$knitting_amount_summ+$purchase_amount+$wash_amount_summ+$other_costing_arr[$job_no]['cm_cost']+$dyeing_amount_summ+$others_emb_amount+$misc_cost;

    ?>
      <table class="rpt_table" border="1" cellpadding="1" cellspacing="1" style="width:970px; font-family: 'Arial Narrow', Arial, sans-serif;" rules="all">

          <tr>
              <th rowspan="7">
              <? foreach($photo_data_array AS $inf){ ?>
              <img  src='<?=$img_path?><? echo $inf[csf("image_location")]; ?>' height='100px' width='100px' />
              <? } ?>
              </th>
              <th style="background: #D7ECD9">Job No</th>
                <th><? echo $row[csf("job_no")]; ?></th>
                <th style="background: #D7ECD9">OR. Rcv Date</th>
                <th><? echo  date('d-M-y',strtotime($po_received_date)); ?></th>
                <th style="background: #D7ECD9">Order Quantity</th>
                <th style="background: yellow; color: #8B0000;">Price/Pcs</th>
                <th align="right" style="background: yellow; color: #8B0000;">&#36; <? echo $row[csf("avg_unit_price")]; ?> </th>
            </tr>
            <tr>                      
                <th style="background: #D7ECD9">Buyer</th>
                <th><? echo $buyer_arr[$row[csf("buyer_name")]]; ?></th>
                <th style="background: #D7ECD9">Ship. Date</th>
                <th><? echo  date('d-M-y',strtotime($public_ship_date)); ?></th>
              <th align="center" style="color: #8B0000"><? echo $row[csf("job_qty")];?> <?  echo $unit_of_measurement[$row[csf("order_uom")]]; ?></th>
                <th style="background: yellow; color: #8B0000;">Order Value</th>                      
                <th align="right" style="background: yellow; color: #8B0000;">&#36; <?= number_format($order_values,2);  ?></th>
            </tr>
            <tr>
              <th style="background: #D7ECD9">Prod. Dept</th>
                <th><? echo $product_dept[$row[csf("product_dept")]]; ?></th>
                <th style="background: #D7ECD9">Garments Item</th>
              <th> 
              <?
          $grmnt_items = "";
          if($garments_item[$row[csf("gmts_item_id")]]=="")
          {

            $grmts_sql = sql_select("select job_no,gmts_item_id,set_item_ratio from wo_po_details_mas_set_details where job_no='$txt_job_no'");
            foreach($grmts_sql as $key=>$val){
              $grmnt_items .=$garments_item[$val[csf("gmts_item_id")]].", ";
              $gmts_item[]=$val[csf("gmts_item_id")];
              
            }
            $grmnt_items = substr_replace($grmnt_items,"",-1,1);
          }else{
            $gmts_item=explode(',',$row[csf("gmts_item_id")]);
            $grmnt_items = $garments_item[$row[csf("gmts_item_id")]];
          }
          echo $grmnt_items;
        ?>
        </th>
              <th align="center" style="color: #8B0000"><?= $row[csf("job_qty")]*$set_item_ratio.' Pcs' ?></th>
                <th style="background: yellow; color: #8B0000;"> <? if($zero_value==0) echo "Budget Value"; ?></th>                      
                <th align="right" style="background: yellow; color: #8B0000;"><? if($zero_value==0){ ?>
                <? if($total_budget_value>0){ echo '&dollar;'.fn_number_format($total_budget_value,2); } ?><br/>
                <? if($total_budget_value>0){ echo fn_number_format($total_budget_value/$order_values*100,2).'%'; } ?>
                <? } ?>
                </th>
            </tr>
            <tr>
              <th style="background: #D7ECD9">Season / Brand</th>
                <th><? echo $sesson_arr[$row[csf("season_buyer_wise")]].'&nbsp'.$brand_arr[$row[csf("brand_id")]]; ?></th>
                <th>Costing Per: <br><?= $costing_for;  ?></th>
                <th style="background: #D7ECD9">Plan Cut quantity (<? echo $tot_excess_cut.'%' ?>) </td>
              <th align="center" style="color: #8B0000"><?= $row[csf("job_quantity")]*$total_set_qnty.' Pcs';//." ". $unit_of_measurement[$row[csf("order_uom")]]; ?></th>
                <th rowspan="2" style="background: yellow; color: #8B0000;"><? if($zero_value==0) echo "Open Value %"; ?></th>                      
                <th rowspan="2" align="right" style="background: yellow; color: #8B0000;"><? if($zero_value==0) { ?> &#36;<? 
                  $margin_val = $order_values-$total_budget_value; 
                  echo fn_number_format($margin_val,2).'<br>'.fn_number_format($margin_val/$order_values*100,2).'%';
                  }
                 ?></th>
            </tr>
            <tr>
              <th style="background: #D7ECD9">Style No</th>
                <th><? $style_no= $row[csf("style_ref_no")]; echo $row[csf("style_ref_no")]; ?></th>
                <th style="background: #D7ECD9">App. Status</th>
                <th colspan="2"><? if( $row[csf("approved")]==1){echo "This Budget is Approved ";} elseif ($row[csf("approved")]==3) {
                  echo "This Budget is partial Approved";
                } else {echo "This Budget is Not  Approved";} ?></th>
            </tr>
            <tr>
              <th rowspan="2" style="background: #D7ECD9">Style Description</th>
                <th rowspan="2" colspan="2"><? echo $row[csf("style_description")]; ?></th>
                <th style="background: #D7ECD9">Remarks</th>
                <th colspan="3"><? echo $row[csf("remarks")]; ?></th>
            </tr>
            <tr>
              <th style="background: #D7ECD9">Refusing Cause</th>
                <th colspan="3"><? echo $row[csf("refusing_cause")]; ?></th>
            </tr>
        </table>

            <?        
      $avg_unit_price=$row[csf("avg_unit_price")];
      $ord_qty=$row[csf("ord_qty")];
  }//end first foearch
  /*echo '<pre>';
  print_r($conv_amount_job_process); die;*/
  
    ?>
    <br>
    <table class="rpt_table" border="1" cellpadding="1" cellspacing="1" style="width:970px; margin-top: 10px; font-family: 'Arial Narrow', Arial, sans-serif;" rules="all">
    <label  style="float:left;background:#CCCCCC; font-size:larger;"><b>Summary </b> </label> 
      <tr style="background: #D7ECD9">
        <th colspan="8" width="320">Fabric </th>
        <th colspan="4" width="160">Embellishment</th>
        <th colspan="4" width="160">Trims + CM + Misc</th>
        <th style="background: yellow">TTL COST &dollar;</th>
      </tr>
      <tr style="background: #D7ECD9">
        <th align="center">Item</th>
        <th align="center">Cost/Uom</th>
        <th align="center">Amount</th>
        <th align="center">&percnt;</th>
        <th align="center">Item</th>
        <th align="center">Cost/Uom</th>
        <th align="center">Amount</th>
        <th align="center">&percnt;</th>
        <th align="center">Item</th>
        <th align="center">Cost/Dz</th>
        <th align="center">Amount</th>
        <th align="center">&percnt;</th>
        <th align="center">Item</th>
        <th align="center">Cost/Dz</th>
        <th align="center">Amount</th>
        <th align="center">&percnt;</th>
        <th rowspan="5" align="right" style="background: yellow; color: #8B0000"><b>
          <? if($total_budget_value>o){ echo fn_number_format($total_budget_value,2,'',''); } ?><br/><br/>
                <? if($total_budget_value>o){ echo fn_number_format($total_budget_value/$order_values*100,2).'%'; } ?></b>
        </th>
      </tr>
      <tr>
        <th align="center">Yarn</th>
        <td align="center"><? if($yarn_amount_summ>0) { echo fn_number_format($yarn_qty_amount_arr[$job_no]['amount']/$yarn_qty_amount_arr[$job_no]['qty'],2); } ?></td>
        <td align="right" style="color: #8B0000"><? if($yarn_amount_summ>0) { echo '&dollar;'.fn_number_format($yarn_amount_summ,2); } ?></td>
        <td align="right"><? if($yarn_amount_summ>0) { echo fn_number_format($yarn_qty_amount_arr[$job_no]['amount']/$order_values*100,2).'%';}; ?></td>

        <th align="center">Finishing</th>
        <td align="center"><? if($total_finishing_amount>0) { echo fn_number_format($total_finishing_amount/$total_finishing_qty,2); } ?></td>
        <td align="right" style="color: #8B0000"><? if($total_finishing_amount>0) { echo '&dollar;'.fn_number_format($total_finishing_amount,2); } ?></td>
        <td align="right"><? if($total_finishing_amount>0) { echo fn_number_format($total_finishing_amount/$order_values*100,2).'%';} ?></td>

        <th align="center">Print</th>
        <td align="center"><? if($print_amount_summ>0) { echo fn_number_format($emb_amount_job_name_arr[$job_no][1]/$emb_qty_job_name_arr[$job_no][1],2);}  ?></td>
        <td align="right" style="color: #8B0000"><? if($print_amount_summ>0) { echo '&dollar;'.fn_number_format($print_amount_summ,2);}  ?></td>
        <td align="right"><? if($print_amount_summ>0) { echo fn_number_format($emb_amount_job_name_arr[$job_no][1]/$order_values*100,2).'%';} ?></td>

        <th align="center">Trim</th>
        <td align="center"><? if($trims_costing_arr[$job_no]>0) { echo fn_number_format($trims_costing_arr[$job_no]/$order_job_qnty,2); } ?></td>
        <td align="right" style="color: #8B0000"><? if($trims_costing_arr[$job_no]>0) { echo '&dollar;'.fn_number_format($trims_costing_arr[$job_no],2);} ?></td>
        <td align="right"><? if($trims_costing_arr[$job_no]>0) { echo fn_number_format($trims_costing_arr[$job_no]/$order_values*100,2).'%';} ?></td>
      </tr>
      <tr>
        <th align="center">Yds</th>
        <td align="center"><? if($yds_amount_summ>0) { echo fn_number_format(array_sum($conv_amount_job_process[$job_no][30])/array_sum($conv_qty_job_process[$job_no][30]),2); } ?></td>
        <td align="right" style="color: #8B0000"><? if($yds_amount_summ>0) { echo '&dollar;'.fn_number_format($yds_amount_summ,2);} ?></td>
        <td align="right"><? if($yds_amount_summ>0) { echo fn_number_format(array_sum($conv_amount_job_process[$job_no][30])/$order_values*100,2).'%';} ?></td>

        <th align="center">AOP</th>
        <td align="center"><? if($aop_amount_summ>0) { echo fn_number_format(array_sum($conv_amount_job_process[$job_no][35])/array_sum($conv_qty_job_process[$job_no][35]),2); } ?></td>
        <td align="right" style="color: #8B0000"><? if($aop_amount_summ>0) { echo '&dollar;'.fn_number_format($aop_amount_summ,2);} ?></td>
        <td align="right"><? if($aop_amount_summ>0) { echo fn_number_format(array_sum($conv_amount_job_process[$job_no][35])/$order_values*100,2).'%';} ?></td>

        <th align="center">EMB</th>
        <td align="center"><? if($emb_amount_summ>0) { echo fn_number_format($emb_amount_job_name_arr[$job_no][2]/$emb_qty_job_name_arr[$job_no][2],2);}  ?></td>
        <td align="right" style="color: #8B0000"><? if($emb_amount_summ>0) { echo '&dollar;'.fn_number_format($emb_amount_summ,2);}  ?></td>
        <td align="right"><? if($emb_amount_summ>0) { echo fn_number_format($emb_amount_job_name_arr[$job_no][2]/$order_values*100,2).'%';} ?></td>
        <th align="center">MISC</th>
        <td align="center"><?  if($misc_cost>0) { echo fn_number_format($misc_cost/$order_job_qnty*12,2); } ?></td>
        <td align="right" style="color: #8B0000"><? if($misc_cost>0) { echo '&dollar;'.fn_number_format($misc_cost,2);}  ?></td>
        <td align="right"><? if($misc_cost>0) { echo fn_number_format($misc_cost/$order_values*100,2).'%';} ?></td>
      </tr>
      <tr>
        <th align="center">Knitting</th>
        <td align="center"><? if($knitting_amount_summ !='') { echo fn_number_format(array_sum($conv_amount_job_process[$job_no][1])/array_sum($conv_qty_job_process[$job_no][1]),2); } ?></td>
        <td align="right" style="color: #8B0000"><? if($knitting_amount_summ !='') { echo  '&dollar;'.$knitting_amount_summ;}   ?></td>
        <td align="right"><? if($knitting_amount_summ !=''){echo fn_number_format(array_sum($conv_amount_job_process[$job_no][1])/$order_values*100,2).'%'; } ?></td>

        <th align="center">P. Fabric</th>
        <td align="center"><? $total_fabic_cost+=$purchase_qty/$purchase_amount; if($purchase_qty>0 && $purchase_amount>0){ echo fn_number_format($purchase_qty/$purchase_amount,2);} ?></td>
        <td align="right"><? $total_fabric_amount+=$purchase_amount; if($purchase_amount){echo '&dollar;'.fn_number_format($purchase_amount,2); } ?></td>
        <td align="right"><? $total_fabric_per+=$purchase_amount/$order_values*100; if($purchase_amount>0){ echo fn_number_format($purchase_amount/$order_values*100,2).'%'; }  ?></td>

        <th align="center">Wash</th>
        <td align="center"><? if($wash_amount_summ>0) {echo fn_number_format($wash_amount_job_name_arr[$job_no][3]/$wash_qty_job_name_arr[$job_no][3],2); };  ?></td>
        <td align="right" style="color: #8B0000"><? if($wash_amount_summ>0) { echo '&dollar;'.fn_number_format($wash_amount_summ,2);}  ?></td>
        <td align="right"><? if($wash_amount_summ>0) { echo fn_number_format($wash_amount_job_name_arr[$job_no][3]/$order_values*100,2).'%';} ?></td>

        <th align="center" style="color: #8B0000">F.CM</th>
        <td align="center" style="color: #8B0000" title="(CM Cost/Order Qty Pcs)x12"><? if($other_costing_arr[$job_no]['cm_cost']>0){echo fn_number_format(($other_costing_arr[$job_no]['cm_cost']/($plancutqty*$set_item_ratio))*12,2); } ?></td>
        <td align="right" style="color: #8B0000"><? if($other_costing_arr[$job_no]['cm_cost']>0){ echo fn_number_format($other_costing_arr[$job_no]['cm_cost'],2); } ?></td>
        <td align="right"><? if($other_costing_arr[$job_no]['cm_cost']>0){ echo fn_number_format($other_costing_arr[$job_no]['cm_cost']/$order_values*100,2).'%'; } ?></td>
      </tr>
      <tr>
        <th align="center">Dyeing</th>
        <td align="center"><? if($dyeing_amount_summ>0) {echo fn_number_format(array_sum($conv_amount_job_process[$job_no][31])/array_sum($conv_qty_job_process[$job_no][31]),2);} ?></td>
        <td align="right" style="color: #8B0000"><? if($dyeing_amount_summ>0) { echo '&dollar;'.fn_number_format($dyeing_amount_summ,2);} ?></td>
        <td align="right"><? if($dyeing_amount_summ>0) { echo fn_number_format(array_sum($conv_amount_job_process[$job_no][31])/$order_values*100,2).'%';} ?></td>

        <th align="center" style="color: #8B0000">TOTAL</th>
        <th align="center" style="color: #8B0000"><? if($total_fabic_cost>0){ echo fn_number_format($total_fabic_cost,2); } ?></th>
        <th align="right" style="color: #8B0000"><? if($total_fabric_amount>0){ echo '&dollar;'.fn_number_format($total_fabric_amount,2); }  ?></th>
        <th align="right" style="color: #8B0000"><? if($total_fabric_per>0){ echo fn_number_format($total_fabric_per,2); }  ?></th>

        <th align="center" title="Special works, Garments dyeing, UV print and others.">Others</th>
        <td align="center"><? if($others_emb_amount>0) {echo fn_number_format($others_emb_amount/$others_emb_qty,2); } ?></td>
        <td align="right"><? if($others_emb_amount>0) { echo '&dollar;'.fn_number_format($others_emb_amount,2); }  ?></td>
        <td align="right"><? if($others_emb_amount>0) { echo fn_number_format($others_emb_amount/$order_values*100,2);}  ?></td>
        <th></th>
        <td></td>
        <td></td>
        <td></td>
      </tr>
    </table>    
    <?
    $location_cpm_cost=0;
    $cm_min_variable=return_field_value("yarn_iss_with_serv_app as cost_per_minute","variable_order_tracking","company_name =".$cbo_company_name." and variable_list=67 and is_deleted=0 and status_active=1","cost_per_minute");
    if($cm_min_variable=="" || $cm_min_variable==0) $location_cpm_cost=0; else $location_cpm_cost=$cm_min_variable;
    if($location_cpm_cost!=1)
    {
      $sql_std_para=sql_select("select interest_expense, income_tax, cost_per_minute, applying_period_date, applying_period_to_date from lib_standard_cm_entry where company_id=$cbo_company_name and status_active=1 and is_deleted=0 order by id");
      
      foreach($sql_std_para as $row )
      {
        $applying_period_date=change_date_format($row[csf('applying_period_date')],'','',1);
        $applying_period_to_date=change_date_format($row[csf('applying_period_to_date')],'','',1);
        $diff=datediff('d',$applying_period_date,$applying_period_to_date);
        for($j=0;$j<$diff;$j++)
        {
          //$newdate =change_date_format(add_date(str_replace("'","",$applying_period_date),$j),'','',1);
          $date_all=add_date(str_replace("'","",$applying_period_date),$j);
          $newdate =change_date_format($date_all,'','',1);
          $financial_para[$newdate][interest_expense]=$row[csf('interest_expense')];
          $financial_para[$newdate][income_tax]=$row[csf('income_tax')];
          $financial_para[$newdate][cost_per_minute]=$row[csf('cost_per_minute')];
        }
      }
    }
    else
    {
      $sql_std_para=sql_select( "select a.id, b.id as dtls_id, b.location_id, b.applying_period_date, b.applying_period_to_date, b.monthly_cm_expense, b.no_factory_machine, b.working_hour, b.cost_per_minute from lib_standard_cm_entry a, lib_standard_cm_entry_dtls b where a.id=b.mst_id and b.location_id=$location_name_id and a.company_id=$cbo_company_name" );
      foreach($sql_std_para as $row)
      {
        $applying_period_date=change_date_format($row[csf('applying_period_date')],'','',1);
        $applying_period_to_date=change_date_format($row[csf('applying_period_to_date')],'','',1);
        $diff=datediff('d',$applying_period_date,$applying_period_to_date);
        for($j=0;$j<$diff;$j++)
        {
          $date_all=add_date(str_replace("'","",$applying_period_date),$j);
          $newdate =change_date_format($date_all,'','',1);
          $financial_para[$newdate][interest_expense]=$row[csf('interest_expense')];
          $financial_para[$newdate][income_tax]=$row[csf('income_tax')];
          $financial_para[$newdate][cost_per_minute]=$row[csf('cost_per_minute')];
        }
      }
    }

    $pre_costing_date=change_date_format($costing_date,'','',1);
    ?>
    <? if($zero_value==0){ ?>
    <br/>
    <label  style="text-align:left; background:#CCCCCC; font-size:larger;"><b>CM Details </b> </label>
    <div style="width:970px; margin-top: 10px; font-family: 'Arial Narrow', Arial, sans-serif;">
    <table class="rpt_table" border="1" cellpadding="1" cellspacing="1" style="width:720px;float: left;" rules="all">
      <tr>
        <th colspan="13">&nbsp;</th>
      </tr>
      <tr style="background: #D7ECD9">
        <th>Style NO.</th>
        <th>MC</th>
        <th>Prd/Hr</th>
        <th>SMV</th>
        <th>BCM</th>
        <th>F.CM</th>
        <th>TTL Min</th>
        <th align="center">CPM</th>
        <th>RL</th>
        <th>RD</th>
        <th>A Eff%</th>
        <th>Layout No</th>
        <th>Alloc Qty</th>
      </tr>
      <tr align="center">
        <td><?= $style_no  ?></td>
        <td>&nbsp;</td>
        <td>&nbsp;</td>
        <td><?= $sew_smv ?></td>
        <td>&nbsp;</td>
        <td>&nbsp;</td>
        <td>&nbsp;</td>
        <td>&#36; <? echo fn_number_format($financial_para[$pre_costing_date][cost_per_minute],4); ?></td>
        <td>&nbsp;</td>
        <td>&nbsp;</td>
        <td>&nbsp;</td>
        <td>&nbsp;</td>
        <td>&nbsp;</td>
      </tr>
      <tr>
        <th>Grand Total</th>
        <th>&nbsp;</th>
        <th>&nbsp;</th>
        <th><?= $sew_smv ?></th>
        <th>&nbsp;</th>
        <th>&nbsp;</th>
        <th>&nbsp;</th>
        <th>&#36; <? echo fn_number_format($financial_para[$pre_costing_date][cost_per_minute],4); ?></th>
        <th>&nbsp;</th>
        <th>&nbsp;</th>
        <th>&nbsp;</th>
        <th>&nbsp;</th>
        <th>&nbsp;</th>
      </tr>
    </table>
    <table class="rpt_table" border="1" cellpadding="1" cellspacing="1" style="width:248px; margin-left: 2px; float: right;" rules="all">
      <tr>
        <th colspan="3" bgcolor="yellow">Embellishment[DZN]</th>
      </tr>
      <tr>
        <th>Print Qty</th>
        <th>Emb Qty</th>
        <th>Wash Qty</th>
      </tr>
      <tr align="center">
        <td><? if($emb_qty_job_name_arr[$job_no][1]>0){echo fn_number_format($emb_qty_job_name_arr[$job_no][1],2); } else { echo '&nbsp;'; } ?></td>
        <td><? if($emb_qty_job_name_arr[$job_no][2]>0){echo fn_number_format($emb_qty_job_name_arr[$job_no][2],2); } else { echo '&nbsp;'; } ?></td>
        <td><? if($emb_qty_job_name_arr[$job_no][3]>0){echo fn_number_format($wash_qty_job_name_arr[$job_no][3],2); } else { echo '&nbsp;'; } ?></td>
      </tr>
      <tr>
        <th><? if($emb_qty_job_name_arr[$job_no][1]>0){echo fn_number_format($emb_qty_job_name_arr[$job_no][1],2); } else { echo '&nbsp;'; } ?></th>
        <th><? if($emb_qty_job_name_arr[$job_no][2]>0){echo fn_number_format($emb_qty_job_name_arr[$job_no][2],2); } else { echo '&nbsp;'; } ?></th>
        <th><? if($emb_qty_job_name_arr[$job_no][3]>0){echo fn_number_format($wash_qty_job_name_arr[$job_no][3],2); } else { echo '&nbsp;'; } ?></th>
      </tr>
    </table>    
    </div>
    <br>
    <? } ?>
    <?
      $nameArray_fabric_description= sql_select("SELECT (a.id) as fabric_cost_dtls_id,a.item_number_id, max(a.lib_yarn_count_deter_id) as determin_id,a.body_part_id,a.uom,a.color_type_id,a.fabric_source, a.construction, a.composition, a.gsm_weight,min(a.width_dia_type) as width_dia_type, b.dia_width,avg(b.cons) as cons  , avg(b.process_loss_percent) as process_loss_percent , a.fab_nature_id,  avg(b.requirment) as requirment, d.fabric_composition_id FROM wo_pre_cost_fabric_cost_dtls_h a, wo_po_color_size_breakdown c, wo_pre_cos_fab_co_avg_con_dtls b, lib_yarn_count_determina_mst d WHERE a.job_no=b.job_no and a.id=b.pre_cost_fabric_cost_dtls_id and c.job_no_mst=a.job_no and  c.id=b.color_size_table_id and a.lib_yarn_count_deter_id=d.id and c.status_active=1 and c.is_deleted=0  and b.status_active=1 and b.is_deleted=0 and a.status_active=1 and a.is_deleted=0 and a.job_no ='$txt_job_no' and a.approved_no=$revised_no and  b.cons>0 group by a.body_part_id,a.uom,a.id,a.item_number_id,a.color_type_id,a.fabric_source,a.construction,a.composition,a.gsm_weight,b.dia_width,a.fab_nature_id, d.fabric_composition_id order by fabric_cost_dtls_id,a.body_part_id,b.dia_width");

      //a.fabric_source=1 and
      foreach ($nameArray_fabric_description as $row) {
        $fabric_id=$row[csf('fabric_cost_dtls_id')];
        $yarn_amount= $yarnDataWithFabricidArr[$fabric_id]['amount'];
        $yarn_qty= $yarnDataWithFabricidArr[$fabric_id]['qty'];

        $yds_amount = array_sum($con_amount_fabric_process[$fabric_id][30]);
        $yds_qty = array_sum($con_qty_fabric_process[$fabric_id][30]);

        $knitting_amount = array_sum($con_amount_fabric_process[$fabric_id][1]);
        $knitting_qty = array_sum($con_qty_fabric_process[$fabric_id][1]);
        $dyeing_amount = array_sum($con_amount_fabric_process[$fabric_id][31]);
        $dyeing_qty = array_sum($con_qty_fabric_process[$fabric_id][31]);
        $aop_amount = array_sum($con_amount_fabric_process[$fabric_id][35]);
        $aop_qty = array_sum($con_qty_fabric_process[$fabric_id][35]);

        $total_finishing_amount=0;
        $total_finishing_qty=0;
        foreach ($finishing_arr as $fid) {
          $total_finishing_amount += array_sum($con_amount_fabric_process[$fabric_id][$fid]);
          $total_finishing_qty += array_sum($con_qty_fabric_process[$fabric_id][$fid]);
        }
        
        $fabric_data_arr[$row[csf('fabric_cost_dtls_id')]]['body_part_id'] = $row[csf('body_part_id')];
        $fabric_data_arr[$row[csf('fabric_cost_dtls_id')]]['description'] = $row[csf('construction')].', '.$fabric_composition_arr[$row[csf('fabric_composition_id')]];
        if($row[csf('fab_nature_id')]==2)
        {
          $fabric_data_arr[$row[csf('fabric_cost_dtls_id')]]['fqty'] = array_sum($fabric_qty_arr['knit']['finish'][$row[csf('fabric_cost_dtls_id')]]);
          $fabric_data_arr[$row[csf('fabric_cost_dtls_id')]]['gqty'] = array_sum($fabric_qty_arr['knit']['grey'][$row[csf('fabric_cost_dtls_id')]]);
        }
        if($row[csf('fab_nature_id')]==3)
        {
          $fabric_data_arr[$row[csf('fabric_cost_dtls_id')]]['fqty'] = array_sum($fabric_qty_arr['woven']['finish'][$row[csf('fabric_cost_dtls_id')]]);
          $fabric_data_arr[$row[csf('fabric_cost_dtls_id')]]['gqty'] = array_sum($fabric_qty_arr['woven']['grey'][$row[csf('fabric_cost_dtls_id')]]);
        }
        
        $fabric_data_arr[$row[csf('fabric_cost_dtls_id')]]['funit'] = $row[csf('uom')];
        $fabric_data_arr[$row[csf('fabric_cost_dtls_id')]]['cons'] = $row[csf('cons')];
        
        $fabric_data_arr[$row[csf('fabric_cost_dtls_id')]]['process_loss'] = $row[csf('process_loss_percent')];
        $fabric_data_arr[$row[csf('fabric_cost_dtls_id')]]['yarn_amount'] = $yarn_amount;
        $fabric_data_arr[$row[csf('fabric_cost_dtls_id')]]['yarn_per'] = $yarn_amount/$yarn_qty;

        $fabric_data_arr[$row[csf('fabric_cost_dtls_id')]]['yds_amount'] = $yds_amount;
        $fabric_data_arr[$row[csf('fabric_cost_dtls_id')]]['yds_per'] = $yds_amount/$yds_qty;
        $fabric_data_arr[$row[csf('fabric_cost_dtls_id')]]['knitting_amount'] = $knitting_amount;
        $fabric_data_arr[$row[csf('fabric_cost_dtls_id')]]['knitting_per'] = $knitting_amount/$knitting_qty;
        $fabric_data_arr[$row[csf('fabric_cost_dtls_id')]]['dyeing_amount'] = $dyeing_amount;
        $fabric_data_arr[$row[csf('fabric_cost_dtls_id')]]['dyeing_per'] = $dyeing_amount/$dyeing_qty;
        $fabric_data_arr[$row[csf('fabric_cost_dtls_id')]]['aop_amount'] = $aop_amount;
        $fabric_data_arr[$row[csf('fabric_cost_dtls_id')]]['aop_per'] = $aop_amount/$aop_qty;
        $fabric_data_arr[$row[csf('fabric_cost_dtls_id')]]['finishing_amount'] = $total_finishing_amount;
        $fabric_data_arr[$row[csf('fabric_cost_dtls_id')]]['finishing_per'] = $total_finishing_amount/$total_finishing_qty;
        if($row[csf('fabric_source')]==1)
        {
          $fabric_data_arr[$row[csf('fabric_cost_dtls_id')]]['ttl_cost'] = $yarn_amount+$yds_amount+$knitting_amount+$dyeing_amount+$aop_amount+$total_finishing_amount;
        }
        if($row[csf('fabric_source')]==2)
        {
          if($row[csf('fab_nature_id')]==2)
          {
            $fabric_data_arr[$row[csf('fabric_cost_dtls_id')]]['ttl_cost']=array_sum($fabric_amount_arr['knit']['grey'][$row[csf('fabric_cost_dtls_id')]]);
          }
          if($row[csf('fab_nature_id')]==3)
          {
            $fabric_data_arr[$row[csf('fabric_cost_dtls_id')]]['ttl_cost']=array_sum($fabric_amount_arr['woven']['grey'][$row[csf('fabric_cost_dtls_id')]]);
          }
        }

        

      }
      ?>
      <? if($zero_value==0){ ?>
      <br>
      <table class="rpt_table" border="1" cellpadding="1" cellspacing="1" style="width:970px; margin-top: 10px; font-family: 'Arial Narrow', Arial, sans-serif;" rules="all">
      <label  style="float:left;background:#CCCCCC; font-size:larger;"><b>Fabric Details </b> </label>  
        <tr style="background: #D7ECD9">
          <th rowspan="2">Garments Part Name</th>
          <th rowspan="2">Fabric Details</th>
          <th rowspan="2">Con</th>
          <th>F. QTY</th>
          <th rowspan="2">Process Loss</th>
          <th>G. QTY</th>
          <th colspan="7">Cost/Uom (Fabric)</th>
          <th rowspan="2">Cost/Dz</th>
          <th rowspan="2" style="background: yellow;">TTL Cost $</th>
        </tr>
        <tr style="background: #D7ECD9">
          <th>Unit</th>
          <th>Unit</th>
          <th>Yarn</th>
          <th>Yds</th>
          <th>Knitting</th>
          <th>Dyeing</th>
          <th>AOP</th>
          <th>Finishing</th>
          <th>Cost/Uom</th>
        </tr>
        <?
          foreach ($fabric_data_arr as $value) {?>
            <tr>
              <td rowspan="2"><?= $body_part[$value['body_part_id']] ?></td>
              <td rowspan="2"><?= $value['description'] ?></td>
              <td rowspan="2" align="center"><?= fn_number_format($value['cons'],2); ?></td>
              <td align="center"><? $total_fqty+=$value['fqty']; echo fn_number_format($value['fqty'],2); ?></td>
              <td rowspan="2" align="center"><? if($value['process_loss']>0){ echo fn_number_format($value['process_loss'],2);} ?></td>
              <td align="center"><? $total_gqty+=$value['gqty']; echo fn_number_format($value['gqty'],2) ?></td>
              <td rowspan="2" align="right"><? $total_yarn_amount += $value['yarn_amount']; if($value['yarn_amount']>0){echo fn_number_format($value['yarn_per'],2); }?><br><? if($value['yarn_amount']>0){ echo fn_number_format($value['yarn_amount'],2);} ?></td>
              <td rowspan="2" align="right"><? $total_yds_amount += $value['yds_amount']; if($value['yds_per']>0){ echo fn_number_format($value['yds_per'],2);}?><br><? if($value['yds_amount']>0){ echo fn_number_format($value['yds_amount'],2);} ?></td>
              <td rowspan="2" align="right"><? $total_knitting_amount += $value['knitting_amount']; if($value['knitting_per']>0){ echo fn_number_format($value['knitting_per'],2);}?><br><? if($value['knitting_amount']>0){ echo fn_number_format($value['knitting_amount'],2);} ?></td>
              <td rowspan="2" align="right"><? $total_dyeing_amount += $value['dyeing_amount']; if($value['dyeing_per']>0){ echo fn_number_format($value['dyeing_per'],2);} ?><br><? if($value['dyeing_amount']>0){echo fn_number_format($value['dyeing_amount'],2);} ?></td>
              <td rowspan="2" align="right"><? $total_aop_amount += $value['aop_amount']; if($value['aop_per']>0){ echo fn_number_format($value['aop_per'],2);} ?><br><? if($value['aop_amount']>0){ echo fn_number_format($value['aop_amount'],2);} ?></td>
              <td rowspan="2" align="right"><? $total_finishing_amount += $value['finishing_amount']; if($value['finishing_per']>0){ echo fn_number_format($value['finishing_per'],2);}?><br><? if($value['finishing_amount']>0){fn_number_format($value['finishing_amount'],2);} ?></td>
              <td rowspan="2" align="right" title="TTL Cost/Finish Quantity"><?= fn_number_format($value['ttl_cost']/$value['fqty'],2) ?></td>
              <td rowspan="2" align="right"><?= fn_number_format($value['ttl_cost']/$order_job_qnty*12,2) ?></td>
              <th rowspan="2" style="background: yellow;" align="right"><? $total_ttl_cost += $value['ttl_cost'];  echo fn_number_format($value['ttl_cost'],2) ?></th>
            </tr>
            <tr>
              <td align="center"><?= $unit_of_measurement[$value['funit']] ?></td>
              <td align="center"><?= $unit_of_measurement[$value['funit']] ?></td>              
            </tr>
          <? }
        ?>
        <tr>
          <th colspan="2">Fabric  Total</th>
          <td></td>
          <th align="center"><? if($total_fqty>0){echo fn_number_format($total_fqty,2);} ?></th>
          <td></td>
          <th align="right"><? if($total_gqty){ echo fn_number_format($total_gqty,2); } ?></th>
          <th align="right"><? if($total_yarn_amount){ echo fn_number_format($total_yarn_amount,2); } ?></th>
          <th align="right"><? if($total_yds_amount){ echo fn_number_format($total_yds_amount,2); } ?></th>
          <th align="right"><? if($total_knitting_amount){ echo fn_number_format($total_knitting_amount,2); } ?></th>
          <th align="right"><? if($total_dyeing_amount){ echo fn_number_format($total_dyeing_amount,2); } ?></th>
          <th align="right"><? if($total_aop_amount){ echo fn_number_format($total_aop_amount,2); } ?></th>
          <th align="right"><? if($total_finishing_amount){ echo fn_number_format($total_finishing_amount,2); } ?></th>
          <th></th>
          <th></th>
          <th style="background: yellow;" align="right"><? if($total_ttl_cost){ echo '&dollar;'.fn_number_format($total_ttl_cost,2); } ?> <br><? if($total_ttl_cost){ echo fn_number_format($total_ttl_cost/$order_values*100,2).'%'; } ?></th>
        </tr>
      </table>
      <? } ?>
      <?
                
        //end   All Fabric Cost part report-------------------------------------------
      $lib_yarn_count=return_library_array( "select yarn_count,id from lib_yarn_count", "id", "yarn_count"  );
         $sql = "select min(id) as id, count_id, copm_one_id, percent_one, copm_two_id, percent_two, color,type_id, min(cons_ratio) as cons_ratio, sum(cons_qnty) as cons_qnty, rate, sum(amount) as amount from wo_pre_cost_fab_yarn_cost_dtls where job_no='".$txt_job_no."'   and status_active=1 and is_deleted=0 group by count_id, copm_one_id, percent_one, copm_two_id, percent_two, color,type_id, rate";
         //echo $sql;
            $data_array=sql_select($sql); 
        $yarn_data_array=$yarn->getCountCompositionPercentTypeColorAndRateWiseYarnQtyAndAmountArray();
        //print_r($yarn_data_array);
      
    ?>
    <br>
        <div style="margin-top:15px; font-family: 'Arial Narrow', Arial, sans-serif;">
          <table class="rpt_table" border="1" cellpadding="1" cellspacing="1" style="width:970px;text-align:center;" rules="all">
    
      <label  style="float:left;background:#CCCCCC; font-size:larger;"><b>Yarn Details </b> </label>  
                <tr style="font-weight:bold;">
                   
                    <td width="540" style="background: #D7ECD9">Yarn Description</td>
                    <td width="80" style="background: #D7ECD9">Yarn Qty/<?=$costing_for; ?></td> 
          <td width="80" style="background: #D7ECD9">TTL Yarn Qty</td>                 
                    <td width="80" style="background: #D7ECD9">Rate &#36;</td>
          <td width="80" style="background: yellow">Amount &#36;</td>
          <td width="80" style="background: #D7ECD9">% to Ord. Value</td>
                </tr>
      <?
      $total_yarn_qty = 0;
            $total_yarn_amount = 0; $total_yarn_cost_dzn=$total_yarn_qty_dzn=0; $total_yarn_cost_kg=0; $total_yarn_avg_cons_qty=0;
      foreach( $data_array as $row )
            { 
        if($row[csf("percent_one")]==100)
          $item_descrition = $lib_yarn_count[$row[csf("count_id")]]." ".$composition[$row[csf("copm_one_id")]]." ".$row[csf("percent_one")]."% ".$color_library[$row[csf("color")]]." ".$yarn_type[$row[csf("type_id")]];
              else
          $item_descrition = $lib_yarn_count[$row[csf("count_id")]]." ".$composition[$row[csf("copm_one_id")]]." ".$row[csf("percent_one")]."% ".$composition[$row[csf("copm_two_id")]]." ".$row[csf("percent_two")]."% ".$color_library[$row[csf("color")]]." ".$yarn_type[$row[csf("type_id")]];
        $rowcons_qnty = $yarn_data_array[$row[csf("count_id")]][$row[csf("copm_one_id")]][$row[csf("percent_one")]][$row[csf("type_id")]][$row[csf("color")]][$row[csf("rate")]]['qty'];
        $rowavgcons_qnty = $yarn_data_array[$row[csf("count_id")]][$row[csf("copm_one_id")]][$row[csf("percent_one")]][$row[csf("type_id")]][$row[csf("color")]][$row[csf("rate")]]['qty'];
        $rowamount = $yarn_data_array[$row[csf("count_id")]][$row[csf("copm_one_id")]][$row[csf("percent_one")]][$row[csf("type_id")]][$row[csf("color")]][$row[csf("rate")]]['amount'];
        if(is_infinite($rowamount) || is_nan($rowamount)){$rowamount=0;}
      ?>   
                <tr>
                    <td align="left"><? echo $item_descrition; ?></td>
                    <td align="right"><? echo fn_number_format($row[csf("cons_qnty")],3); ?></td>
          <td align="right"><? echo fn_number_format($rowcons_qnty,2); ?></td>
                   
                    <td align="right"><? if($row[csf("rate")]>0){ echo fn_number_format($row[csf("rate")],3);} ?></td>
                    <td align="right" style="background: yellow"><? if($rowamount>0){ echo fn_number_format($rowamount,2);} ?></td>
          <td align="right"><? 
          $cv=($row[csf("amount")]/$price_dzn)*100;
          if(is_infinite($cv) || is_nan($cv)){$cv=0;}
          if($cv>0){echo fn_number_format($cv,2); }
          ?></td>
                </tr>
            <?  
            $total_yarn_qty+=$rowcons_qnty;
          $total_yarn_qty_dzn+=$row[csf("cons_qnty")];
          $total_avg_yarn_qty+=$rowavgcons_qnty;
          $total_yarn_amount +=$rowamount;
          $total_yarn_cost_dzn+=$row[csf("amount")];
          $total_yarn_avg_cons_qty+=$rowavgcons_qnty;
          $total_yarn_cost_kg=$total_yarn_amount/$total_yarn_qty;
          if(is_infinite($total_yarn_cost_kg) || is_nan($total_yarn_cost_kg)){$total_yarn_cost_kg=0;}
            }
            ?>
              <tr class="rpt_bottom" style="font-weight:bold">
                    <td>Yarn Total</td>
                    <td align="right"><? if($total_yarn_qty_dzn>0){ echo fn_number_format($total_yarn_qty_dzn,4); } ?></td>
          <td align="right"><? if($total_yarn_qty>0){ echo fn_number_format($total_yarn_qty,2); } ?></td>                    
                    <td></td>
                    <td align="right" bgcolor="yellow"><? if($total_yarn_amount>0){ echo '&dollar;'.fn_number_format($total_yarn_amount>0,2); } ?></td>
          <td align="right"><? 
          $cv=($total_yarn_cost_dzn/$price_dzn)*100;
          if(is_infinite($cv) || is_nan($cv)){$cv=0;}
          if($cv>0){ echo fn_number_format($cv,2).' %';  }
          ?></td>
                </tr>
          </table>
      </div>
      <?
    //End Yarn Cost part report here -------------------------------------------
    

  //start Trims Cost part report here -------------------------------------------
  $supplier_library_fabric=return_library_array( "select a.id, a.supplier_name from lib_supplier a where a.is_deleted=0  and a.status_active=1 group by a.id,a.supplier_name order by a.supplier_name", "id", "supplier_name");
  
      $sql = "select id, job_no, trim_group,description,brand_sup_ref, cons_uom, cons_dzn_gmts, rate, amount, apvl_req, nominated_supp_multi, status_active from wo_pre_cost_trim_cost_dtls  where job_no='".$txt_job_no."' and status_active=1 and is_deleted=0";
    $data_array=sql_select($sql);
  ?>
        <div style="margin-top:15px">
            <table class="rpt_table" border="1" cellpadding="1" cellspacing="1" style="width:970px;text-align:center;font-family: 'Arial Narrow', Arial, sans-serif;" rules="all">
           
      <label  style="float:left;background:#CCCCCC; font-size:larger"><b>Trims Details</b> </label> 
                <tr style="font-weight:bold; background: #D7ECD9" >
                    <td width="110" style="background: #D7ECD9">Item Group</td>
                    <td width="110" style="background: #D7ECD9">Item Description</td>
          <td width="100" style="background: #D7ECD9">Supplier</td>
                    <td width="60" style="background: #D7ECD9">UOM</td>
                    <td width="80" style="background: #D7ECD9">Cons/<?=$costing_for; ?>[Qnty]</td>
          <td width="100" style="background: #D7ECD9">TTL Required[Qnty]</td>
                    <td width="80" style="background: #D7ECD9">Rate &#36;</td>
                    <td width="80" style="background: #D7ECD9">Amount/<?=$costing_for; ?>&#36;</td>
          <td width="80" style="background: yellow">Amount &#36;</td>
          <td width="60" style="background: #D7ECD9">% to Ord. Value</td>
                </tr>
            <?
      $trim_qty_arr=$trims->getQtyArray_by_precostdtlsid();
      //print_r($trim_qty);
      $trim_amount_arr=$trims->getAmountArray_precostdtlsid();
            $total_trims_cost=0;  $total_trims_qty=$total_trims_cost_dzn=0;$total_trims_cost_dzn=0;$total_trims_cost_kg=0;
            foreach( $data_array as $row ){ 
        
        $trim_group=return_library_array( "select item_name,id from  lib_item_group where id=".$row[csf("trim_group")], "id", "item_name" ); 
        $cons_dzn_gmts= $row[csf("cons_dzn_gmts")];
        $amount_dzn= $row[csf("amount")];
        $pre_trims_qty=$trim_qty_arr[$row[csf("id")]];
        $pre_trims_amount=$trim_amount_arr[$row[csf("id")]];  
        
        $nominated_supp_str="";
        $exsupp=explode(",",$row[csf("nominated_supp_multi")]);
        foreach($exsupp as $sid)
        {
          if($nominated_supp_str=="") $nominated_supp_str=$supplier_library_fabric[$sid]; else $nominated_supp_str.=','.$supplier_library_fabric[$sid];
        }            
      ?>   
                <tr>
                    <td align="left"><? echo $trim_group[$row[csf("trim_group")]]; ?></td>
                    <td align="left"><? echo $row[csf("description")]; ?></td>
                    <td align="left"><?=$nominated_supp_str; ?></td>
                    <td align="center"><? echo $unit_of_measurement[$row[csf("cons_uom")]]; ?></td>
                    <td align="right"><? echo fn_number_format($cons_dzn_gmts,3); ?></td>
          <td align="right"><? echo fn_number_format($pre_trims_qty,4); ?></td>
                    <td align="right"><? echo fn_number_format($row[csf("rate")],3); ?></td>
           <td align="right"><? echo fn_number_format($amount_dzn,4); ?></td>
                    <td align="right" style="background: yellow"><? echo fn_number_format($pre_trims_amount,2); ?></td>
          <td align="right"  title="<? echo $amount_dzn.'='.$price_dzn;?>">
          <? 
          $cv=($amount_dzn/$price_dzn)*100;
          if(is_infinite($cv) || is_nan($cv)){$cv=0;}
          echo fn_number_format($cv,2); 
          //echo fn_number_format(($amount_dzn/$price_dzn)*100,2); 
          ?></td>
                </tr>
            <?
                 $total_trims_cost += $pre_trims_amount;
         $total_trims_cost_dzn += $amount_dzn;
          $total_trims_qty += $pre_trims_qty;
            }
            ?>
                <tr class="rpt_bottom" style="font-weight:bold" >
                    <td>Trims Total</td>
          <td colspan="4"></td>
          <td align="right"><? if($total_trims_qty>0){ echo fn_number_format($total_trims_qty,4); } ?></td>
          <td align="right"><? //echo fn_number_format($total_trims_cost_dzn,4); ?></td>                   
                    
          <td align="right"><? if($total_trims_cost_dzn>0){ echo '&dollar;'.fn_number_format($total_trims_cost_dzn,4); } ?></td>
          <td align="right" style="background: yellow"><? if($total_trims_cost>0){ echo '&dollar;'.fn_number_format($total_trims_cost,2); } ?></td>
          <td align="right" title="<? echo $total_trims_cost_dzn.'='.$price_dzn;?>">
          <? 
          $cv=($total_trims_cost_dzn/$price_dzn)*100;
          if(is_infinite($cv) || is_nan($cv)){$cv=0;}
          if($cv){ echo fn_number_format($cv,2).' %'; }
          ?>
                    </td>
                </tr>                
            </table>
      </div>
      <?
      $pre_cost_dtls_arr = sql_select("SELECT id, job_no, costing_per_id, order_uom_id, fabric_cost, fabric_cost_percent, trims_cost,depr_amor_pre_cost,deffdlc_cost,studio_cost,design_cost,trims_cost_percent,embel_cost,embel_cost_percent,comm_cost,comm_cost_percent,commission,incometax_cost,interest_cost,interest_percent,commission_percent,lab_test,lab_test_percent,inspection,inspection_percent,cm_cost,cm_cost_percent,freight,freight_percent,common_oh,common_oh_percent,design_percent, studio_percent, currier_pre_cost, currier_percent, certificate_pre_cost, certificate_percent, total_cost, total_cost_percent, price_dzn, price_dzn_percent, margin_dzn, margin_dzn_percent, price_pcs_or_set, price_pcs_or_set_percent, margin_pcs_set, margin_pcs_set_percent, cm_for_sipment_sche from wo_pre_cost_dtls_histry where job_no='".$txt_job_no."' and approved_no=$revised_no and status_active=1 and is_deleted=0");
      foreach ($pre_cost_dtls_arr as $row) {
        $price_dzn=$row[csf("price_dzn")];
    $lab_test_dzn=$row[csf("lab_test")];
    $commission_cost_dzn=$row[csf("commission")];
    $commercial_cost_dzn = $row[csf("comm_cost")];

    $inspection_dzn=$row[csf("inspection")];
    $cm_cost_dzn =$row[csf("cm_cost")];
    $common_oh_dzn =$row[csf("common_oh")];
    $freight_dzn =$row[csf("freight")];
    $currier_pre_cost_dzn = $row[csf("currier_pre_cost")];
    $certificate_pre_cost_dzn = $row[csf("certificate_pre_cost")];
    $deffdlc_cost_dzn = $row[csf("deffdlc_cost")];
    $depr_amor_pre_cost_dzn = $row[csf("depr_amor_pre_cost")];
    $interest_cost_dzn=$row[csf("interest_cost")];
    $interest_cost_percent=$row[csf("interest_percent")];
    $incometax_cost_dzn=$row[csf("incometax_cost")];
    $studio_cost_dzn=$row[csf("studio_cost")];
    $design_cost_dzn=$row[csf("design_cost")];        
    $studio_cost_percent=$row[csf("studio_percent")];
    $design_cost_percent=$row[csf("design_percent")]; 

    $other_cost_per = $inspection_dzn+$freight_dzn+$certificate_pre_cost_dzn+$deffdlc_cost_dzn+$design_cost_dzn+$studio_cost_dzn+$common_oh_dzn+$interest_cost_dzn+$incometax_cost_dzn+$depr_amor_pre_cost_dzn;
      }      
      ?>
      <table  class="rpt_table"  border="1" align="left" cellpadding="0" width="350" cellspacing="0" rules="all" style="margin-top: 10px;font-family: 'Arial Narrow', Arial, sans-serif;">
        <tr style="background: #D7ECD9">
          <th>MISC/Others Cost</th>
          <th>%</th>
          <th>TTL Cost $</th>
        </tr>
        <tr>
          <td>Test cost</td>
          <td align="right"><?
            $lab_test_per=($other_costing_arr[$job_no]['lab_test']/$order_values)*100;
        if(is_infinite($lab_test_per) || is_nan($lab_test_per))
        {
          $lab_test_per=0;
        }
        if($lab_test_per>0){echo fn_number_format($lab_test_per,2);}
        $total_misc_per += $lab_test_per;
          ?></td>
          <th align="right"><? if($other_costing_arr[$job_no]['lab_test']>0){ echo fn_number_format($other_costing_arr[$job_no]['lab_test'],2);} ?></th>
        </tr>
        <tr>
          <td>Buying commission</td>
          <td align="right"><?
            $commission_cost_per=($commission_costing_arr[$job_no]/$order_values)*100;
        if(is_infinite($commission_cost_per) || is_nan($commission_cost_per))
        {
          $commission_cost_per=0;
        }
        if($commission_cost_per>0){ echo fn_number_format($commission_cost_per,2);}
        $total_misc_per +=$commission_cost_per;
          ?></td>
          <th align="right"><? if($commission_costing_arr[$job_no]>0){ echo fn_number_format($commission_costing_arr[$job_no],2);} ?></th>
        </tr>
        <tr>
          <td>Commercial cost</td>
          <td align="right"><?
            $commercial_cost_per=($commercial_costing_arr[$job_no]/$order_values)*100;
        if(is_infinite($commercial_cost_per) || is_nan($commercial_cost_per))
        {
          $commercial_cost_per=0;
        }
        if($commercial_cost_per>0){ echo fn_number_format($commercial_cost_per,2); }
        $total_misc_per +=$commercial_cost_per;
            ?>            
          </td>
          <th align="right"><? if($commercial_costing_arr[$job_no]>0) { echo fn_number_format($commercial_costing_arr[$job_no],2);} ?></th>
        </tr>
        <tr>
          <td>Other costs</td>
          <td align="right"><?
            $other_cost_per=($total_other_cost/$order_values)*100;
        if(is_infinite($other_cost_per) || is_nan($other_cost_per))
        {
          $other_cost_per=0;
        }
        if($other_cost_per>0){ echo fn_number_format($other_cost_per,2);}
        $total_misc_per +=$other_cost_per;
            ?>            
          </td>
          <th align="right"><? if($total_other_cost>0){ echo fn_number_format($total_other_cost,2);} ?></th>
        </tr>
        <tr>
          <th>MISC/Others Cost Sub Total</th>
          <th align="right"><? if($total_misc_per>0){ echo fn_number_format($total_misc_per,2).'%'; }  ?></th>
          <th align="right"><? if($misc_cost>0){ echo '&dollar;'.fn_number_format($misc_cost,2); } ?></th>
        </tr>
      </table>
      <div id="div_size_color_matrix" style="float:left; max-width:1000; font-family: 'Arial Narrow', Arial, sans-serif;">
              <fieldset id="div_size_color_matrix" style="max-width:1000;">
        <?
        $color_library=return_library_array( "select id, color_name from lib_color where status_active=1 and is_deleted=0", "id", "color_name");
        $size_library=return_library_array( "select id, size_name from lib_size where status_active=1 and is_deleted=0", "id", "size_name");
        $nameArray_size=sql_select( "select  size_number_id,min(id) as id,  min(size_order) as size_order from wo_po_color_size_breakdown where po_break_down_id in(".$txt_order_no_id.") and  job_no_mst='$txt_job_no' and is_deleted=0 and status_active=1 group by size_number_id order by size_order");
        //echo "select  size_number_id,min(id) as id, min(size_order) as size_order from wo_po_color_size_breakdown where po_break_down_id in(".$txt_order_no_id.") and  job_no_mst=$txt_job_no and is_deleted=0 and status_active=1 group by size_number_id order by size_order"; die;
        ?>
        <legend>Size and Color Breakdown</legend>
        <table  class="rpt_table"  border="1" align="left" cellpadding="0" width="750" cellspacing="0" rules="all" >
                    <tr>
                        <td style="border:1px solid black"><strong>Color/Size</strong></td>
                    <?          
            foreach($nameArray_size  as $result_size)
                        {      ?>
                        <td align="center" style="border:1px solid black"><strong><? echo $size_library[$result_size[csf('size_number_id')]];?></strong></td>
                    <?  }    ?>       
                        <td style="border:1px solid black; width:130px" align="center"><strong> Total Order Qty(Pcs)</strong></td>
                        <td style="border:1px solid black; width:80px" align="center"><strong> Excess %</strong></td>
                        <td style="border:1px solid black; width:130px" align="center"><strong> Total Plan Cut Qty(Pcs)</strong></td>
                    </tr>
                    <?
          $color_size_order_qnty_array=array();
          $color_size_qnty_array=array();
          $size_tatal=array();
          $size_tatal_order=array();
          for($c=0;$c<count($gmts_item); $c++)
            {
          $item_size_tatal=array();
          $item_size_tatal_order=array();
          $item_grand_total=0;
          $item_grand_total_order=0;
          $nameArray_color=sql_select( "select  color_number_id,min(id) as id,min(color_order) as color_order from wo_po_color_size_breakdown where  item_number_id=$gmts_item[$c] and po_break_down_id in(".$txt_order_no_id.") and is_deleted=0 and status_active=1 group by color_number_id  order by color_order");
          ?>
                    <tr>
                      <td style="border:1px solid black" colspan="<? echo count($nameArray_size)+3;?>"><strong><? echo $garments_item[$gmts_item[$c]];?></strong></td>
                    </tr>
                    <?
          foreach($nameArray_color as $result_color)
                    {           
                    ?>
                    <tr>
                        <td align="center" style="border:1px solid black"><? echo $color_library[$result_color[csf('color_number_id')]]; // echo $row_num_tr; ?></td>
                        <? 
            $color_total=0; $color_total_order=0;
            
            foreach($nameArray_size  as $result_size)
            {
            $nameArray_color_size_qnty=sql_select( "select sum(plan_cut_qnty) as plan_cut_qnty, sum(order_quantity) as  order_quantity  from wo_po_color_size_breakdown where  po_break_down_id in(".str_replace("'","",$txt_order_no_id).") and  size_number_id =".$result_size[csf('size_number_id')]." and color_number_id =".$result_color[csf('color_number_id')]."  and item_number_id=$gmts_item[$c] and  status_active=1 and is_deleted =0");                          
            foreach($nameArray_color_size_qnty as $result_color_size_qnty)
                        {
                        ?>
                            <td style="border:1px solid black; text-align:right">
              <? 
                if($result_color_size_qnty[csf('plan_cut_qnty')]!= "")
                {
                   echo fn_number_format($result_color_size_qnty[csf('order_quantity')],0);
                   $color_total += $result_color_size_qnty[csf('plan_cut_qnty')] ;
                   $color_total_order += $result_color_size_qnty[csf('order_quantity')] ;
                   $item_grand_total+=$result_color_size_qnty[csf('plan_cut_qnty')];
                   $item_grand_total_order+=$result_color_size_qnty[csf('order_quantity')];
                     $grand_total +=$result_color_size_qnty[csf('plan_cut_qnty')];
                   $grand_total_order +=$result_color_size_qnty[csf('order_quantity')];
                   
                   $color_size_qnty_array[$result_size[csf('size_number_id')]][$result_color[csf('color_number_id')]]=$result_color_size_qnty[csf('plan_cut_qnty')];
                   $color_size_order_qnty_array[$result_size[csf('size_number_id')]][$result_color[csf('color_number_id')]]=$result_color_size_qnty[csf('order_quantity')];
                   if (array_key_exists($result_size[csf('size_number_id')], $size_tatal))
                   {
                    $size_tatal[$result_size[csf('size_number_id')]]+=$result_color_size_qnty[csf('plan_cut_qnty')];
                    $size_tatal_order[$result_size[csf('size_number_id')]]+=$result_color_size_qnty[csf('order_quantity')];
                   }
                   else
                   {
                    $size_tatal[$result_size[csf('size_number_id')]]=$result_color_size_qnty[csf('plan_cut_qnty')]; 
                    $size_tatal_order[$result_size[csf('size_number_id')]]=$result_color_size_qnty[csf('order_quantity')]; 
                   }
                   if (array_key_exists($result_size[csf('size_number_id')], $item_size_tatal))
                   {
                    $item_size_tatal[$result_size[csf('size_number_id')]]+=$result_color_size_qnty[csf('plan_cut_qnty')];
                    $item_size_tatal_order[$result_size[csf('size_number_id')]]+=$result_color_size_qnty[csf('order_quantity')];
                   }
                   else
                   {
                    $item_size_tatal[$result_size[csf('size_number_id')]]=$result_color_size_qnty[csf('plan_cut_qnty')]; 
                    $item_size_tatal_order[$result_size[csf('size_number_id')]]=$result_color_size_qnty[csf('order_quantity')]; 
                   }
                }
                else echo " ";
               ?>
              </td>
                    <?   
            }
                        }
                        ?>
                          <td style="border:1px solid black; text-align:right"><? if(round($color_total_order)>0){ echo fn_number_format(round($color_total_order),0);} ?></td>
                          
                         <td style="border:1px solid black; text-align:right"><? $excexss_per=($color_total-$color_total_order)/$color_total_order*100; if(round($excexss_per)>0){ echo fn_number_format($excexss_per,2)." %";} ?>
                         </td>
                        <td style="border:1px solid black; text-align:right"><? if(round($color_total)>0){ echo fn_number_format(round($color_total),0);} ?></td>
                    </tr>
                    <?
                    }
          ?>
                        <td align="center" style="border:1px solid black"><strong>Sub Total</strong></td>
                        <?
            foreach($nameArray_size  as $result_size)
                        {
                        ?>
                        <td style="border:1px solid black;  text-align:right"><? echo $item_size_tatal_order[$result_size[csf('size_number_id')]];  ?></td>
                        <?
                        }
                        ?>
                        <td  style="border:1px solid black;  text-align:right"><?  if(round($item_grand_total_order)>0){ echo fn_number_format(round($item_grand_total_order),0); } ?></td>
                        <td  style="border:1px solid black;  text-align:right"><? $excess_item_gra_tot=($item_grand_total-$item_grand_total_order)/$item_grand_total_order*100; if($excess_item_gra_tot>0){echo fn_number_format($excess_item_gra_tot,2)." %"; } ?></td>
                        <td  style="border:1px solid black;  text-align:right"><?  if(round($item_grand_total)>0){echo fn_number_format(round($item_grand_total),0); } ?></td>
                    </tr>
                    <?
          }
                    ?>
                     <tr>
                        <td style="border:1px solid black" align="center" colspan="<? echo count($nameArray_size)+3; ?>"><strong>&nbsp;</strong></td>
                        </tr>
                    <tr>
                    <tr>
                        <td align="center" style="border:1px solid black"><strong>Grand Total</strong></td>
                        <?
            foreach($nameArray_size  as $result_size)
                        {
                        ?>
                        <td style="border:1px solid black;  text-align:right"><? echo $size_tatal_order[$result_size[csf('size_number_id')]];  ?></td>
                        <?
                        }
                        ?>
                        <td  style="border:1px solid black;  text-align:right"><? if(round($grand_total_order)>0){ echo fn_number_format(round($grand_total_order),0); } ?></td>
                        <td  style="border:1px solid black;  text-align:right"><? $excess_gra_tot= ($grand_total-$grand_total_order)/$grand_total_order*100; if($excess_gra_tot>0) { echo fn_number_format($excess_gra_tot,2)." %"; } ?></td>
                        <td  style="border:1px solid black;  text-align:right"><?  if(round($grand_total)>0) { echo fn_number_format(round($grand_total),0); } ?></td>
                    </tr>
                </table>
                </fieldset>
                </div>
        <br/><br/>
        <div>
        
     <br/>
     <?  //echo signature_table(109, $cbo_company_name, "970px");?>
     <?
     $width=990;
     $padding_top = 70;
     $prepared_by='';
     $sql = sql_select("select designation,name,activities,prepared_by from variable_settings_signature where report_id=109 and company_id=$cbo_company_name order by sequence_no");


  if($sql[0][csf("prepared_by")]==1){
    list($prepared_by,$activities)=explode('**',$prepared_by);
    $sql_2[100] = array ( DESIGNATION => 'Prepared By' ,NAME => $prepared_by, ACTIVITIES =>$activities, PREPARED_BY => 0 );
    $sql=$sql_2+$sql;
  }

  $count = count($sql);
  $td_width = floor($width / $count);
  $standard_width = $count * 120;
  if ($standard_width > $width) {
    $td_width = 120;
  }
  $no_coloumn_per_tr = floor($width / $td_width);
  $i = 1;
  if ($count == 0) {$message = "<b>Note: This is Software Generated Copy , Signature is not Required.</b>";}
  echo '<table id="signatureTblId" width="' . $width . '" style="padding-top:' . $padding_top . 'px;"><tr><td width="100%" height="' . $padding_top . '" colspan="' . $count . '">' . $message . '</td></tr><tr>';
  foreach ($sql as $row) {
    echo '<td width="' . $td_width . '" align="center" valign="top">
    <strong>' . $row[csf("activities")] . '</strong><br>
    <strong style="text-decoration:overline">' . $row[csf("designation")] . "</strong><br>" . $row[csf("name")] . '</td>';
    if ($i % $no_coloumn_per_tr == 0) {
      echo '</tr><tr><td width="100%" height="70" colspan="' . $no_coloumn_per_tr . '"></td></tr>';
    }
    $i++;
  }
  echo '</tr></table>';
  ?>
    </div>
   <?
   disconnect($con);
   exit();
}

if($action=="budgetsheet2")
{
      $process = array( &$_POST );
			extract(check_magic_quote_gpc( $process ));
			$reporttype=str_replace("'","",$reporttype);
			$cbo_company_name=str_replace("'","",$cbo_company_name);
			$cbo_style_owner=str_replace("'","",$cbo_style_owner);
			$txt_order=str_replace("'","",$txt_order);
			$txt_order_id=str_replace("'","",$txt_order_id);
			$file_no=str_replace("'","",$txt_file_no);
			$cbo_file_year=str_replace("'","",$cbo_file_year);
			$file_po_id=str_replace("'","",$txt_file_id);
			$file_no=rtrim($file_no,',');
			$txt_style_ref=str_replace("'","",$txt_style_ref);
			$txt_season_id=str_replace("'","",$txt_season_id);
			$cbo_buyer_name=str_replace("'","",$cbo_buyer_name);
			$style_ref_id=str_replace("'","",$txt_style_ref_id);
			$txt_quotation_id=str_replace("'","",$txt_quotation_id);
			$txt_hidden_quot_id=str_replace("'","",$txt_hidden_quot_id);
			$comments_head=str_replace("'","",$comments_head);
			$revised_no=str_replace("'","",$revised_no);
			//echo $file_po_id;die;
			if($txt_hidden_quot_id!='')
			{
				$qoutation_id=$txt_hidden_quot_id;
			}
			else
			{
				$qoutation_id=$txt_quotation_id;//implode(",",array_unique(explode("*",$txt_quotation_id)));
			}
			//echo $reporttype.'-'.$txt_quotation_id.'-'.$txt_hidden_quot_id;
			if($reporttype!=5 && $reporttype!=6) //Quotation Button
			{
				if($qoutation_id!='' && str_replace("'","",$sign)==1 )
				{
					echo "<p style='font-size:30px; color:red', align='center'>Search by Quotation Id  is not allowed for this button.<p/>";die;
				}
			}


          if(str_replace("'","",$cbo_buyer_name)==0)
          {
            if ($_SESSION['logic_erp']["data_level_secured"]==1)
            {
              if($_SESSION['logic_erp']["buyer_id"]!="")
              {
                $buyer_id_cond=" and a.buyer_name in (".$_SESSION['logic_erp']["buyer_id"].")";
                $buyer_id_cond2=" and buyer_id in (".$_SESSION['logic_erp']["buyer_id"].")";
              }
              else{
                  $buyer_id_cond="";
                  $buyer_id_cond2="";
              }
            }
            else
            {
              $buyer_id_cond="";
              $buyer_id_cond2="";
            }
          }
          else
          {
            $buyer_id_cond=" and a.buyer_name=$cbo_buyer_name";
            $buyer_id_cond2=" and buyer_id=$cbo_buyer_name";
          }

          $job_style_cond="";
          if(trim(str_replace("'","",$txt_style_ref))!="")
          {
            if(str_replace("'","",$style_ref_id)!="")
            {
              $job_style_cond=" and a.id in(".str_replace("'","",$style_ref_id).")";
            }
            else
            {
              $job_style_cond=" and a.style_ref_no = '".trim(str_replace("'","",$txt_style_ref))."'";
            }
          }

          $order_cond="";
          if(trim(str_replace("'","",$txt_order))!="")
          {
            if(str_replace("'","",$txt_order_id)!="")
            {
              $order_cond=" and b.id in(".str_replace("'","",$txt_order_id).")";
            }
            else
            {
              $order_cond=" and b.po_number = '".trim(str_replace("'","",$txt_order))."'";
            }
          }
          $season_cond2=$season_cond='';
          if($txt_season_id!="")
          {
            $season_cond="and a.season_matrix in($txt_season_id)";
            $season_cond2="and season_buyer_wise in($txt_season_id)";
            //
          }
          if($file_po_id!="")
          {
            $file_po_idCond="and b.id in($file_po_id)";
          } 
          else {
            $file_no_cond="";
            if(!empty($file_no))
            {
              $file_nos=explode(",", $file_no);
              $file_no_cond=where_con_using_array($file_nos,1,"b.file_no");
            }
            $file_po_idCond="";
          }

          

          $file_year_cond="";
          if(!empty($cbo_file_year))
          {
            $file_year_cond=" and b.file_year ='".$cbo_file_year."'";
          } 
          $file_year_cond="";
          if(!empty($cbo_file_year))
          {
            $file_year_cond=" and b.file_year ='".$cbo_file_year."'";
          } 
          

        ob_start();

        $lib_yarn_count=return_library_array( "select yarn_count,id from lib_yarn_count", "id", "yarn_count");
        $color_library=return_library_array( "select id,color_name from lib_color", "id", "color_name"  );



		
				  $sql="select a.id as job_id,a.job_no_prefix_num as job_prefix,a.job_no, a.company_name, a.buyer_name, a.style_description as style_desc, a.style_ref_no, a.order_uom, a.total_set_qnty as ratio, b.id as po_id,b.po_quantity,b.po_total_price, b.po_number,b.unit_price, b.pub_shipment_date from wo_po_details_master a, wo_po_break_down b  where a.job_no=b.job_no_mst  and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $company_name_cond $style_owner_cond $job_no_cond $job_style_cond $order_cond $buyer_id_cond $season_cond $file_no_cond $file_po_idCond order  by b.id";

				$sql_po_result=sql_select($sql);
				$all_po_id="";$all_job="";$all_full_job="";$all_style="";$all_style_desc=""; $all_buyer="";
				$order_qty_pcs=0;$total_fob_value=0;$total_order_qty=0;$total_unit_price=0;
				//echo $buyer_name;die;
				$job_idArr=array();
				foreach($sql_po_result as $row)
				{
					if($all_po_id=="") $all_po_id=$row[csf("po_id")]; else $all_po_id.=",".$row[csf("po_id")];
					if($all_job=="") $all_job=$row[csf("job_prefix")]; else $all_job.=",".$row[csf("job_prefix")];
					if($all_buyer=="") $all_buyer=$buyer_arr[$row[csf("buyer_name")]]; else $all_buyer.=",".$buyer_arr[$row[csf("buyer_name")]];
					if($all_full_job=="") $all_full_job="'".$row[csf('job_no')]."'"; else $all_full_job.=","."'".$row[csf('job_no')]."'";
					if($all_style=="") $all_style=$row[csf("style_ref_no")]; else $all_style.=",".$row[csf("style_ref_no")];
					if($all_style_desc=="") $all_style_desc=$row[csf("style_desc")]; else $all_style_desc.=",".$row[csf("style_desc")];

					/*$order_qty_pcs+=$row[csf('po_quantity')]*$row[csf('ratio')];
					$total_order_qty+=$row[csf('po_quantity')];
					$total_unit_price+=$row[csf('unit_price')];
					$total_fob_value+=$row[csf('po_total_price')];*/
					$po_qty_by_job[$row[csf("job_no")]]+=$row[csf('po_quantity')]*$row[csf('ratio')];
					$job_idArr[$row[csf("job_id")]]=$row[csf('job_id')];
				}
				$sql_po="select a.job_no_prefix_num as job_prefix,a.job_no, a.company_name, a.buyer_name, a.style_description as style_desc, a.style_ref_no, a.order_uom, a.total_set_qnty as ratio, b.id as po_id,b.po_quantity,b.po_total_price, b.po_number,b.unit_price,c.order_rate, b.pub_shipment_date,c.order_total,c.order_quantity from wo_po_details_master a, wo_po_break_down b,wo_po_color_size_breakdown c   where a.job_no=b.job_no_mst and a.job_no=c.job_no_mst and c.po_break_down_id=b.id  and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 $company_name_cond $style_owner_cond $job_no_cond $job_style_cond $order_cond $buyer_id_cond $season_cond $file_no_cond    order  by b.id";

				$sql_po_color_result=sql_select($sql_po);
				foreach($sql_po_color_result as $row)
				{
					$order_qty_pcs+=$row[csf('order_quantity')];
					$total_order_qty+=$row[csf('order_quantity')];
					$total_unit_price+=$row[csf('order_rate')];
					$total_fob_value+=$row[csf('order_total')];
				}
				unset($sql_po_color_result);
				
				//print_r($po_qty_by_job);
				$all_job_no=array_unique(explode(",",$all_full_job));
				$all_jobs="";
				foreach($all_job_no as $jno)
				{
						if($all_jobs=="") $all_jobs=$jno; else $all_jobs.=",".$jno;
				}
			//echo $all_jobs;
				$financial_para=array();
				$sql_std_para=sql_select("select cost_per_minute,applying_period_date as from_period_date from lib_standard_cm_entry where company_id=$cbo_company_name and status_active=1 and is_deleted=0  order by id desc");
				foreach($sql_std_para as $row)
				{
					$period_date=date("m-Y", strtotime($row[csf('from_period_date')]));
					$financial_para[$period_date]['cost_per_minute']=$row[csf('cost_per_minute')];
				}
				unset($sql_std_para);
				   $nameArray=sql_select( "select commercial_cost_method,id,commercial_cost_percent from  variable_order_tracking where company_name=$cbo_company_name and variable_list=27 order by id" );
				   $commercial_cost_method=$commercial_cost_percent=0;
				   foreach($nameArray as $row)
					{
						$commercial_cost_method=$row[csf('commercial_cost_method')];
						$commercial_cost_percent=$row[csf('commercial_cost_percent')];
					}
					//echo $commercial_cost_method.'=';
					unset($nameArray);

				$sql_pre="select a.job_no,a.approved,a.costing_date,a.machine_line as machine_line,a.job_no, a.prod_line_hr, a.sew_smv, a.sew_effi_percent as sew_effi_percent, a.budget_minute,b.cost_pcs_set,b.price_pcs_or_set,remarks from wo_pre_cost_mst a,wo_pre_cost_dtls b where  a.job_no=b.job_no and  a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.job_no in(".$all_full_job.")  order  by a.id";
				

				    $sql_pre_result=sql_select($sql_pre);
					$sew_smv='';$machine_line='';$prod_line_hr='';$prod_line_hr='';$sew_effi_percent='';$budget_minute=0;
					$approved_msg='';
				  foreach($sql_pre_result as $row)
					{
							$machine_line.=$row[csf("machine_line")].',';
							$prod_line_hr.=$row[csf("prod_line_hr")].',';
							$sew_smv.=$row[csf("sew_smv")].',';
							$sew_effi_percent.=$row[csf("sew_effi_percent")].',';
							$smv_avg_by_job[$row[csf("job_no")]]=$row[csf("sew_smv")];
							$efficincy_hr_mc_by_job[$row[csf("job_no")]]=$row[csf("machine_line")].'**'.$row[csf("prod_line_hr")];
							$smv_avg_by_job[$row[csf("job_no")]]=$row[csf("sew_smv")];
							$costing_date=date("m-Y", strtotime($row[csf('costing_date')]));
							$cost_per_minute.=$financial_para[$costing_date]['cost_per_minute'].',';
							$remarks.=$row[csf("remarks")].'.';
							if($row[csf("approved")]==1) 
							{
								$approved_msg="This Job Is Approved.";
							}
							else if($row[csf("approved")]==3) 
							{
								$approved_msg="This Job Is Partial Approved";
							}
							//$price_pcs_or_set+=$row[csf('price_pcs_or_set')];
							//$cost_pcs_set+=$row[csf('cost_pcs_set')];
					}
					unset($sql_pre_result);
					//print_r($smv_avg_by_job);
					//echo $sew_smv;
					//print_r($costing_date_arr);
					$condition= new condition();
					$condition->company_name("=$cbo_company_name");
				  if(str_replace("'","",$cbo_buyer_name)>0){
					  $condition->buyer_name("=$cbo_buyer_name");
				 }
				 if($txt_order_id!='' || $txt_order_id!=0)
				 {
					$condition->po_id("in($txt_order_id)");
				 } 
				 if($file_po_id!='' || $file_po_id!=0)
				 {
					$condition->po_id("in($file_po_id)");
				 }
				 if(str_replace("'","",$txt_style_ref)!='')
				 {
					$condition->job_no("in($all_jobs)");
				 }
				 if(str_replace("'","",$file_no)!='')
				 {
					$condition->file_no("in($file_no)");
				 }
				$condition->init();
				$fabric= new fabric($condition);
				$yarn= new yarn($condition);
				//echo $yarn->getQuery();die;
				$conversion= new conversion($condition);
				$trim= new trims($condition);
				$emblishment= new emblishment($condition);
				$wash= new wash($condition);
				$commercial= new commercial($condition);
				$commission= new commision($condition);

				$fabric= new fabric($condition);
				$fabric_costing_arr=$fabric->getAmountArray_by_order_knitAndwoven_greyAndfinish();
				$fabric_qty=$fabric->getQtyArray_by_Fabriccostid_knitAndwoven_greyAndfinish();


				$fabric_amount=$fabric->getAmountArray_by_Fabriccostid_knitAndwoven_greyAndfinish();
				$yarn_data_arr=$yarn->getCountCompositionPercentTypeColorAndRateWiseYarnQtyAndAmountArray();
				//$yarn_fabric_cost_data_arr=$yarn->get_By_Precostfabricdtlsid_YarnAmountArray();
				$yarn_fabric_cost_data_arr=$yarn->get_By_Precostdtlsid_YarnQtyAmountArray();
				//print_r($yarn_fabric_cost_data_arr);die;
				$yarn_costing_arr=$yarn->getOrderWiseYarnAmountArray();
				$conv_data_qty_arr=$conversion->getQtyArray_by_conversionid();
				$conv_data_amount_arr=$conversion->getAmountArray_by_conversionid();
				$other= new other($condition);
				$other_costing_arr=$other->getAmountArray_by_order();
				$conversion_costing_arr=$conversion->getAmountArray_by_order();
				$conversion_process_costing_arr=$conversion->getAmountArray_by_orderAndProcess();
				$trim_arr_qty=$trim->getQtyArray_by_precostdtlsid();
				$trim_arr_amount=$trim->getAmountArray_precostdtlsid();
				$trims_costing_arr=$trim->getAmountArray_by_order();
				$trim= new trims($condition);
				$trims_item_qty_arr=$trim->getQtyArray_by_itemidAndDescription();
				$trim= new trims($condition);
				$trims_item_amount_arr=$trim->getAmountArray_by_itemidAndDescription();

				$emblishment_qty_arr=$emblishment->getQtyArray_by_jobAndEmblishmentid();
				$emblishment_amount_arr=$emblishment->getAmountArray_by_jobAndEmblishmentid();
				
				$emblishment_job_amount_arr=$emblishment->getAmountArray_by_jobEmbnameAndEmbtypeColor();
				$emblishment_job_qty_arr=$emblishment->getQtyArray_by_jobEmbnameAndEmbtypeColor();
				
				$emblishment_costing_arr=$emblishment->getAmountArray_by_order();
				$emblishment_qty_name_type_arr=$emblishment->getQtyArray_by_jobEmbnameAndEmbtype();
				$emblishment_amount_name_type_arr=$emblishment->getAmountArray_by_jobEmbnameAndEmbtype();
				$wash_qty_arr=$wash->getQtyArray_by_jobAndEmblishmentid();
				$wash_amount_arr=$wash->getAmountArray_by_jobAndEmblishmentid();
				$wash_type_name_qty_arr=$wash->getQtyArray_by_jobEmbnameAndEmbtype();
				$wash_type_name_amount_arr=$wash->getAmountArray_by_jobEmbnameAndEmbtype();
			
				$wash_job_type_name_qty_arr=$wash->getQtyArray_by_jobEmbnameAndEmbtypeColor();
				$wash_job_type_name_amount_arr=$wash->getAmountArray_by_jobEmbnameAndEmbtypeColor();
				
				
				$wash_costing_arr=$wash->getAmountArray_by_order();
				$commercial_amount_arr=$commercial->getAmountArray_by_jobAndPrecostdtlsid();
				$commercial_costing_arr=$commercial->getAmountArray_by_order();
				$commercial_item_amount_arr=$commercial->getAmountArray_by_jobAndItemid();
				$commission_amount_arr=$commission->getAmountArray_by_jobAndPrecostdtlsid();
				$commission_costing_arr=$commission->getAmountArray_by_orderAndItemid();
				$commission_costing_sum_arr=$commission->getAmountArray_by_order();
				$commission_costing_item_arr=$commission->getAmountArray_by_jobAndItemid();

				$total_job_unit_price=($total_fob_value/$total_order_qty);

				 if($revised_no>0){
					// $sql_fab="select a.job_no_prefix_num as job_prefix,a.job_no, a.company_name, a.buyer_name, a.style_description as style_desc, a.style_ref_no, a.order_uom, a.total_set_qnty as ratio, b.id as po_id,b.po_quantity,b.po_total_price, b.po_number, b.pub_shipment_date,c.id, c.job_no,c.item_number_id, c.body_part_id as body_id, c.fab_nature_id as nat_id, c.color_type_id as color_type, c.fabric_description as fab_desc, c.avg_cons,c.uom, c.fabric_source as fab_source, c.rate, c.amount, c.avg_finish_cons, c.status_active from wo_po_details_master a, wo_po_break_down b,wo_pre_cost_fabric_cost_dtls c  where a.job_no=b.job_no_mst and c.job_no=b.job_no_mst and c.job_no=a.job_no  and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $company_name_cond $style_owner_cond $job_no_cond $job_style_cond $order_cond $buyer_id_cond $season_cond $file_no_cond $file_po_idCond order  by b.id,c.fab_nature_id, c.fabric_description,c.uom";
          $sql_fab= "select a.job_no_prefix_num as job_prefix,a.job_no, a.company_name, a.buyer_name, a.style_description as style_desc,a.style_ref_no, a.order_uom, a.total_set_qnty as ratio, b.id as po_id,b.po_quantity,sum(d.plan_cut_qnty) as plan_qty,b.po_total_price, b.po_number, b.pub_shipment_date,c.id, e.job_no,e.item_number_id, e.body_part_id as body_id, e.fab_nature_id as nat_id, e.color_type_id as color_type, e.fabric_description as fab_desc, e.avg_cons,c.uom, e.fabric_source as fab_source, c.rate, c.amount, e.avg_finish_cons, c.status_active ,e.approved_no from wo_po_details_master a, wo_po_break_down b left join wo_po_color_size_breakdown d  on  b.id=d.po_break_down_id,wo_pre_cost_fabric_cost_dtls c  join wo_pre_cost_fabric_cost_dtls_h e on e.job_no=c.job_no and c.id=e.pre_cost_fabric_cost_dtls_id  where a.job_no=b.job_no_mst and c.job_no=b.job_no_mst and c.job_no=a.job_no and a.status_active=1 and a.is_deleted=0 
           and b.status_active=1 and b.is_deleted=0 $company_name_cond $style_owner_cond $job_no_cond $job_style_cond $order_cond $buyer_id_cond $season_cond $file_no_cond $file_po_idCond  and e.approved_no=$revised_no  group by a.job_no_prefix_num ,a.job_no, a.company_name, a.buyer_name, a.style_description , a.style_ref_no, a.order_uom, a.total_set_qnty, b.id ,b.po_quantity,b.po_total_price, b.po_number, b.pub_shipment_date,c.id, e.job_no,e.item_number_id, e.body_part_id , e.fab_nature_id , e.color_type_id , e.fabric_description , e.avg_cons,c.uom, e.fabric_source , c.rate, c.amount, e.avg_finish_cons, c.status_active ,e.approved_no     
     order by b.id,e.fab_nature_id, e.fabric_description,c.uom";
				 }

				 	// echo $sql_fab;

				  $sql_fabs_result=sql_select($sql_fab);
				  $fabric_detail_arr=array();  $fabric_job_check_arr=array();
				$total_purchase_amt=0;
				foreach($sql_fabs_result as $row)
				{
					$row[csf("fab_source")]=$row[csf("fab_source")];
					$set_ratio=$row[csf("ratio")];
					$item_desc= $body_part[$row[csf("body_id")]].",".$color_type[$row[csf("color_type")]].",".$row[csf("fab_desc")];
					$fabric_detail_arr[$row[csf("nat_id")]][$row[csf("uom")]][$item_desc][$row[csf("fab_source")]]['amount']=$row[csf("amount")];
					$fabric_detail_arr[$row[csf("nat_id")]][$row[csf("uom")]][$item_desc][$row[csf("fab_source")]]['rate']=$row[csf("rate")];
					//$fabric_detail_arr[$row[csf("nat_id")]][$item_desc][$row[csf("fab_source")]][$row[csf("uom")]]['rate']=$row[csf("rate")];
					$fabric_detail_arr[$row[csf("nat_id")]][$row[csf("uom")]][$item_desc][$row[csf("fab_source")]]['pre_fab_id'].=$row[csf("id")].',';

					if($row[csf("fab_source")]==2)
					{
				
						$total_purchase_amt+=$fabric_amount['knit']['grey'][$row[csf("id")]][$row[csf("uom")]]+$fabric_amount['woven']['grey'][$row[csf("id")]][$row[csf("uom")]];
            
					}
				
				}
				if(empty($set_ratio))
				{
					$sql_ratio=sql_select( "SELECT a.total_set_qnty as ratio from wo_po_details_master a, wo_po_break_down b where a.job_no=b.job_no_mst and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $company_name_cond $style_owner_cond $job_no_cond $job_style_cond $order_cond $buyer_id_cond $season_cond $file_no_cond $file_po_idCond group by a.total_set_qnty");
					if(count($sql_ratio))
					{
						$set_ratio=$sql_ratio[0][csf('ratio')];
					}
				}
				unset($sql_fabs_result);
				 // print($fabric_btb_amt);
								//print_r($fabric_detail_arr);die;
								//echo $total_fob_value.'/'.$total_order_qty;
						$styleRef=explode(",",$txt_style_ref);
						$all_style_job="";
						foreach($styleRef as $sid)
						{
								if($all_style_job=="") $all_style_job="'".$sid."'"; else $all_style_job.=","."'".$sid."'";
						}
						$fabric_rowspan_arr=array();$uom_rowspan_arr=array();
						foreach($fabric_detail_arr as $fab_nat_key=>$fab_data)
						{
							$fabrice_rowspan=0;
							foreach($fab_data as $uom_key=>$uom_data)
							{
								$uom_rowspan=0;
								foreach($uom_data as $desc_key=>$desc_data)
								{

									foreach($desc_data as $source_key=>$val)
									{
										$uom_rowspan++;
										$fabrice_rowspan++;
									}

									$fabric_rowspan_arr[$fab_nat_key]=$fabrice_rowspan;
									$uom_rowspan_arr[$fab_nat_key][$uom_key]=$uom_rowspan;
								}
							}
						}


							$style1="#E9F3FF";
							$style="#FFFFFF";

				// $sql_yarn="select c.id as id,c.fabric_cost_dtls_id,c.count_id, c.copm_one_id, c.percent_one,c.color,c.type_id,min(c.cons_ratio) as cons_ratio,sum(c.cons_qnty) as cons_qnty,sum(c.amount) as amount,c.rate from wo_po_details_master a, wo_po_break_down b,wo_pre_cost_fab_yarn_cost_dtls c  where a.job_no=b.job_no_mst and c.job_no=b.job_no_mst and c.job_no=a.job_no  and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 $company_name_cond $style_owner_cond $job_no_cond $job_style_cond $order_cond $buyer_id_cond $season_cond $file_no_cond $file_po_idCond group by c.id,c.count_id,c.fabric_cost_dtls_id, c.copm_one_id, c.percent_one,  c.color,c.type_id, c.rate order  by c.count_id, c.copm_one_id,c.percent_one";

        $sql_yarn= "select c.id as id,c.fabric_cost_dtls_id,c.count_id, c.copm_one_id, c.percent_one,c.color,c.type_id,min(c.cons_ratio) as cons_ratio,sum(c.cons_qnty) as cons_qnty,sum(c.amount) as amount,c.rate,e.approved_no from wo_po_details_master a, wo_po_break_down b,wo_pre_cost_fab_yarn_cost_dtls c join wo_pre_cost_fabric_cost_dtls_h e on e.job_no=c.job_no and c.fabric_cost_dtls_id=e.pre_cost_fabric_cost_dtls_id where a.job_no=b.job_no_mst and c.job_no=b.job_no_mst and c.job_no=a.job_no and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 $company_name_cond $style_owner_cond $job_no_cond $job_style_cond $order_cond $buyer_id_cond $season_cond $file_no_cond $file_po_idCond and e.approved_no=$revised_no  group by c.id,c.count_id,c.fabric_cost_dtls_id, c.copm_one_id, c.percent_one, c.color,c.type_id, c.rate,e.approved_no order by c.count_id, c.copm_one_id,c.percent_one";


					$result_yarn=sql_select($sql_yarn);
					$yarn_detail_arr=array();
					$yarnamount=$total_yarn_costing=0;
					foreach($result_yarn as $row)
					{
						$item_descrition = $lib_yarn_count[$row[csf("count_id")]].",".$composition[$row[csf("copm_one_id")]].",".$row[csf("percent_one")]."%,".$color_library[$row[csf("color")]].",".$yarn_type[$row[csf("type_id")]];
						//echo $item_descrition.'<br>';
						//echo $yarn_fabric_cost_data_arr[$row[csf("fabric_cost_dtls_id")]].', ';
						$total_yarn_costing+=$yarn_fabric_cost_data_arr[$row[csf("id")]]['amount'];
						$row_span+=1;
						$yarn_detail_arr[$item_descrition]['rate']=$row[csf("rate")];
						$yarn_detail_arr[$item_descrition]['count_id']=$row[csf("count_id")];
						$yarn_detail_arr[$item_descrition]['copm_one_id']=$row[csf("copm_one_id")];
						$yarn_detail_arr[$item_descrition]['percent_one']=$row[csf("percent_one")];
						$yarn_detail_arr[$item_descrition]['color']=$row[csf("color")];
						$yarn_detail_arr[$item_descrition]['type_id']=$row[csf("type_id")];
						$yarn_detail_arr[$item_descrition]['color']=$row[csf("color")];
						$yarnamount=$yarn_fabric_cost_data_arr[$row[csf("id")]]['amount'];//$yarn_data_arr[$row[csf("count_id")]][$row[csf("copm_one_id")]][$row[csf("percent_one")]][$row[csf("type_id")]][$row[csf("color")]][$row[csf("rate")]]['amount'];
						$yarncons_qntys=$yarn_fabric_cost_data_arr[$row[csf("id")]]['qty'];//$yarn_data_arr[$row[csf("count_id")]][$row[csf("copm_one_id")]][$row[csf("percent_one")]][$row[csf("type_id")]][$row[csf("color")]][$row[csf("rate")]]['qty'];
						//echo $yarnamount.'<br/>';
						$yarn_detail_arr[$item_descrition]['yarn_cost']+=$yarnamount;
						$yarn_detail_arr[$item_descrition]['yarn_qty']+=$yarncons_qntys;

						$totalyarn_detail_arr[100]['amount']+=$yarnamount;
					}
					unset($result_yarn);

							$machine_line=rtrim($machine_line,',');
							$machine_line=implode(",",array_unique(explode(",",$machine_line)));
							$prod_line_hr=rtrim($prod_line_hr,',');
							$prod_line_hr=implode(",",array_unique(explode(",",$prod_line_hr)));
							$sew_effi_percent=rtrim($sew_effi_percent,',');
							$sew_effi_percent=implode(",",array_unique(explode(",",$sew_effi_percent)));
							$cost_per_minute=rtrim($cost_per_minute,',');
							$cost_per_minute=implode(",",array_unique(explode(",",$cost_per_minute)));
							$sew_smv=rtrim($sew_smv,',');
							$sew_smv=implode(",",array_unique(explode(",",$sew_smv)));
							$po_ids=array_unique(explode(",",$all_po_id));
						  $total_embell_cost=$total_cm_cost=$total_lab_test_cost=$total_inspection_cost=$total_currier_cost=$total_certificate_cost=$total_common_oh_cost=$total_freight_cost=$total_wash_costing=0;
						  $total_commisssion=$total_fabric_amt=$total_conversion_cost=$total_trims_amt=$total_embl_amt=$total_comercial_amt=$total_commisssion=0;
						  $foreign=0;$local=$total_studio_cost=$total_design_cost=0;
						 // print_r($po_ids);
						$tot_conversion_aop_costing=$tot_conversion_yarn_dyeing_costing=0;
						  foreach($po_ids as $pid)
						  {

							   $foreign_local=$commission_costing_sum_arr[$pid];
								$total_wash_costing+=$wash_costing_arr[$pid];
								$total_commisssion+=$foreign_local;
							    $total_embl_amt+=$emblishment_costing_arr[$pid];
								$total_comercial_amt+=$commercial_costing_arr[$pid];
								$tot_fabric=array_sum($fabric_costing_arr['knit']['grey'][$pid])+array_sum($fabric_costing_arr['woven']['grey'][$pid]);
							    $total_fabric_amt+=$tot_fabric;
								$conversion_costing=array_sum($conversion_costing_arr[$pid]);
								$tot_conversion_aop_costing+=array_sum($conversion_process_costing_arr[$pid][35]);
								$tot_conversion_yarn_dyeing_costing+=array_sum($conversion_process_costing_arr[$pid][30]);
								$yarn_costing=$yarn_costing_arr[$pid];

								$total_conversion_cost+=$conversion_costing;
							    $total_trims_amt+=$trims_costing_arr[$pid];

								//$total_raw_metarial_cost=$total_finish_amt+$total_embl_amt+$total_trims_amt;
								$total_cm_cost+=$other_costing_arr[$pid]['cm_cost'];
								$total_lab_test_cost+=$other_costing_arr[$pid]['lab_test'];
								$total_inspection_cost+=$other_costing_arr[$pid]['inspection'];
								$total_currier_cost+=$other_costing_arr[$pid]['currier_pre_cost'];
								$total_certificate_cost+=$other_costing_arr[$pid]['certificate_pre_cost'];
								$total_studio_cost+=$other_costing_arr[$pid]['studio_cost'];
								$total_design_cost+=$other_costing_arr[$pid]['design_cost'];
								$total_common_oh_cost+=$other_costing_arr[$pid]['common_oh'];
								$total_freight_cost+=$other_costing_arr[$pid]['freight'];
						  }
					//	echo $total_comercial_amt.'DDDDDDDDDDDD'.$reporttype;
						$total_raw_metarial_cost= $total_fabric_amt+$total_yarn_costing+$total_conversion_cost+$total_trims_amt;
						$total_all_cost= $total_fabric_amt+$total_yarn_costing+$total_conversion_cost+$total_trims_amt+$total_embl_amt+$total_comercial_amt+$total_commisssion+$total_wash_costing+$total_cm_cost+$total_lab_test_cost+$total_inspection_cost+$total_currier_cost+$total_certificate_cost+$total_common_oh_cost+$total_freight_cost;
						 // echo number_format($total_commisssion,2);
					
						 $tot_aop_trim_yd_cost=$tot_conversion_aop_costing+$total_trims_amt+$tot_conversion_yarn_dyeing_costing;
						 $total_aop_trim_yd_cost=($tot_aop_trim_yd_cost*10)/100;
						 $total_all_cost+=$total_aop_trim_yd_cost;
						


					 $sql_commi="select c.id, c.job_no, c.particulars_id,c.commission_base_id,avg(c.commision_rate) as rate, sum(c.commission_amount) as amount from wo_po_details_master a, wo_po_break_down b,wo_pre_cost_commiss_cost_dtls c  where a.job_no=b.job_no_mst and c.job_no=b.job_no_mst and c.job_no=a.job_no  and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.commission_base_id>0 $company_name_cond $style_owner_cond $job_no_cond $job_style_cond $order_cond $buyer_id_cond $season_cond $file_no_cond $file_po_idCond group by c.id, c.job_no, c.particulars_id,c.commission_base_id order by c.id";

					$result_commi=sql_select($sql_commi);
					$commi_detail_arr=array();$tot_commission_rate=0;
					foreach($result_commi as $row)
					{

						$commi_rowspan+=1;
						$commi_detail_arr[$row[csf("particulars_id")]]['particulars_id']=$row[csf("particulars_id")];
						$commi_detail_arr[$row[csf("particulars_id")]]['amount']=$row[csf("amount")];
						$commi_detail_arr[$row[csf("particulars_id")]]['rate']=$row[csf("rate")];
						$commi_detail_arr[$row[csf("particulars_id")]]['job_no'].=$row[csf("job_no")].',';
						$commi_detail_arr[$row[csf("particulars_id")]]['commission_base_id']=$row[csf("commission_base_id")];
						//$emblishment_qty_arr
						$commiamount=$commission_costing_item_arr[$row[csf("job_no")]][$row[csf("particulars_id")]];
						$totalcommi_detail_arr[100]['amount']+=$commiamount;
						$tot_commission_rate+=$row[csf("rate")];
					}
					unset($result_commi);
					$sql_comm="select c.id, c.job_no, c.item_id,avg(c.rate) as rate,sum(c.rate) as tot_rate, sum(c.amount) as amount from wo_po_details_master a, wo_po_break_down b,wo_pre_cost_comarci_cost_dtls c  where a.job_no=b.job_no_mst and c.job_no=b.job_no_mst and c.job_no=a.job_no  and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $company_name_cond $style_owner_cond $job_no_cond $job_style_cond $order_cond $buyer_id_cond $season_cond $file_no_cond $file_po_idCond group by c.id, c.job_no, c.item_id  order by c.id";


					

					$result_comm=sql_select($sql_comm);
					$comm_detail_arr=array();$tot_comm_rate=0;
					foreach($result_comm as $row)
					{
						$item_descrition =$row[csf("description")];
						$comm_rowspan+=1;
						$comm_detail_arr[$row[csf("item_id")]]['item_id']=$row[csf("item_id")];
						$comm_detail_arr[$row[csf("item_id")]]['amount']=$row[csf("amount")];
						$comm_detail_arr[$row[csf("item_id")]]['rate']=$row[csf("rate")];
						$comm_detail_arr[$row[csf("item_id")]]['job_no'].=$row[csf("job_no")].',';
						$comm_detail_arr[$row[csf("item_id")]]['desc']=$item_descrition;
						//$emblishment_qty_arr
						$commamount+=$commercial_amount_arr[$row[csf("job_no")]][$row[csf("id")]];
						$totalcomm_detail_arr[100]['amount']+=$commamount;
						$tot_comm_rate+=$row[csf("rate")];
					}
					//echo $commercial_cost_method.'DD';
					$tot_commercial_cost_amount=$total_comercial_amt=0;
					if($commercial_cost_method==1)
					{
						 $tot_commercial_cost_amount=$total_yarn_costing+$total_trims_amt+$total_purchase_amt;
						 $total_comercial_amt=($tot_commercial_cost_amount*$tot_comm_rate)/100;
					}
					else if($commercial_cost_method==2)// On Selling
					{
						// $commercial_cost_percent_amount=$total_yarn_costing+$total_trims_amt+$total_purchase_amt;
						//($commercial_cost_percent_amount*$tot_comm_rate)/100;
						//echo $total_job_unit_price.'='.$commercial_cost_percent;
						  $tot_commercial_cost_amount=($total_job_unit_price*$commercial_cost_percent)/100;
						   $total_comercial_amt=$tot_commercial_cost_amount;
					}
					else if($commercial_cost_method==3) // Net Selling
					{
					 	$net_commi_rate=$total_job_unit_price-$tot_commission_rate;
					 	 $tot_commercial_cost_amount=($net_commi_rate*$commercial_cost_percent)/100;
						$total_comercial_amt=$tot_commercial_cost_amount;

					}
					else if($commercial_cost_method==5)
					{
					 	 $tot_commercial_cost_amount=$total_embl_amt+$total_trims_amt+$total_purchase_amt+$total_wash_costing+$total_lab_test_cost+$total_inspection_cost+$total_cm_cost+$total_freight_cost+$total_currier_cost+$total_certificate_cost+$total_common_oh_cost+$total_studio_cost+$total_design_cost;
						$total_comercial_amt=($tot_commercial_cost_amount*$commercial_cost_percent)/100;
					}

		?>
        <div style="width:100%">
        <style>
		@media print {
			  #page_break_div {
				page-break-before: always;
			  }

				.footer_signature {
				position:fixed;
				height:auto;
				bottom:0;
				width:100%;
				}
			}
		</style>
       <!-- <div class="footer_signature" >
         <?
          //echo signature_table(109, $cbo_company_name, "850px");
		 ?>
      	</div>-->

             <table width="800px" style="margin-left:10px">
             <tr class="form_caption">
                    <td colspan="8" align="center">
                    
                    <strong style=" font-size:18px"><? echo $report_title;?></strong></td>
                    
                </tr>
                <tr>
                    <td align="center" colspan="8" class="form_caption">
                    <strong><? echo $company_library[$cbo_company_name].'<br>';if($cbo_style_owner!=0) echo 'Style Owner: '.$company_library[$cbo_style_owner]; ?></strong>
                    <b  style="color:#FF0000; float:right; font-size:large;"><? echo $approved_msg;?><br>Revised No: <? echo $revised_no;?></b>
                    </td>
                    
                </tr>
            </table>
             <table width="850" style="margin-left:10px;" class="rpt_table" cellpadding="0" cellspacing="0" border="2" rules="all" id="table_header_1">
                <tr>
                 <th  colspan="2" align="center" style="font-size:16px"> <strong>Summary</strong></th>
               </tr>
             <tr>
             <td style="border:none">
            	<table width="600"  class="rpt_table" cellpadding="0" cellspacing="0" border="1" rules="all">
                    <tr  bgcolor="<? echo $style; ?>">
                        <td width="120"> <strong>Buyer</strong> </td>
                        <td width=""><? if($cbo_buyer_name==0) echo implode(",",array_unique(explode(",",$all_buyer)));else echo $buyer_arr[$cbo_buyer_name];?> </td>
                        <td width="140" ><strong>Sew. SMV(Avg).</strong></td>
                        <td width="" title="SMV*PO Qty/Total PO Qty(Pcs)">&nbsp; <?
							$tot_avg_sew_smv=0;
							foreach($po_qty_by_job as $jobno=>$poQty)
							{
								$smv_avg=$smv_avg_by_job[$jobno];
								//echo $po_qty_by_job[$jobno].'='.$jobno;
								//echo $poQty.',';
								$tot_avg_sew_smv+=($poQty*$smv_avg)/$order_qty_pcs;
							}
							echo number_format($tot_avg_sew_smv,2);
							$available_min=$prod_min=0;
							foreach($efficincy_hr_mc_by_job as $jobno=>$mc_hr)
							{
								$mc_hr_data=explode("**",$mc_hr);
								//echo $mc_hr_data[0].'m'.$mc_hr_data[1];
								$prd_min_smv_avg=$smv_avg_by_job[$jobno];
								$mc_no=$mc_hr_data[0];
								$hr_line_no=$mc_hr_data[1];
								$available_min+=$mc_no*10*60;
								$prod_min+=($hr_line_no*10)*$prd_min_smv_avg;
							}
						//$efficincy_hr_mc_by_job[$row[csf("job_no")]];

						?> </td>
                    </tr>
                    <tr  bgcolor="<? echo $style1; ?>">
                        <td width="120"> <strong>Job No.</strong> </td>
                        <td width=""><? echo implode(",",array_unique(explode(",",$all_job)));?></td>
                        <td width="140"><strong>Sew Efficiency(Avg)%</strong></td>
                        <td width="" title="<? echo 'Prod Min='.$prod_min.'/Avilable Min='.$available_min?>"><? echo number_format($prod_min/$available_min,2);?></td>
                    </tr>
                     <tr  bgcolor="<? echo $style; ?>">
                        <td width="120"><strong>Style Ref.</strong></td>
                        <td width=""><p><? echo implode(",",array_unique(explode(",",$all_style)));?></p></td>
                        <td width="140"> <strong>Style Desc.</strong> </td>
                        <td width=""><p><? echo implode(",",array_unique(explode(",",$all_style_desc)));?></p></td>
                    </tr>
                     <td>
                     <tr  bgcolor="<? echo $style1; ?>">
                        <td width="140"><strong>Avg FOB/UNIT Price[$]</strong></td>
                        <td width=""><? echo number_format($total_job_unit_price,2); ?></td>
                        <td width="140"><strong> Cost Per Minute(TK)</strong> </td>
                        <td width=""><? echo $cost_per_minute;?></td>
                    </tr>
                    <tr  bgcolor="<? echo $style; ?>">
                        <td width="100"><strong>Total Qty.(Pcs)</strong></td>
                        <td><? echo $order_qty_pcs;?></td>
                        <td width="140"><b>Total FOB[$]:</b></td>
                         <td  align="left">  <? echo number_format($total_fob_value,2);?></td>

                    </tr>
                     <tr  bgcolor="<? echo $style; ?>">
                        <td width="100"><strong>Comission [$] :</strong></td>
                        <td><? echo  number_format($total_commisssion,2);?></td>
                         <td width="140"> <b>CM Cost/Dzn(Avg)[$] : </b></td>
                          <td title="Total CM/Total Po qty(<? echo $total_order_qty;?>))*12">  <?
					          	  echo number_format((($total_cm_cost/$total_order_qty*12)*$set_ratio),2);?>
                         </td>
                    </tr>
                       <tr bgcolor="<? echo $style1?>"  align="left">
                           <td width="100" title=""><b>Total CM Cost[$] :</b></td>
                           <td width="" id="gross_cm_total"> <? echo number_format($total_cm_cost,2);?> </td>
                          <td width="140"  title="Fabric+Yarn+Conversion+Trims Cost"><b>Total Raw Material Cost[$]:</b></td>
                         <td id="td_sum_raw_material_cost">  <?  echo number_format($total_raw_metarial_cost,2);?></td>
                    </tr>
                     <tr bgcolor="<? echo $style ?>" align="left">
                         <td width="100"><b>Total Cost[$] :</b></td>
                         <td title="Trims+Emblish+Fabric+Conversion+Lab Test+Commercial+Commission">  <?
						               echo number_format($total_all_cost,2);?></td>
                         <td width="140"><b>Total Margin[$] :</b></td>
                         <td title="Total Fob-Total Cost">  <?  $total_margin=$total_fob_value-$total_all_cost;
				            		 echo number_format($total_fob_value-$total_all_cost,2);?></td>
                    </tr>
                     <tr bgcolor="<? echo $style1 ?>" align="left">
                         <td width="100"  title="Total Margin/PO Qty Pcs*12"><b>Margin/Dzn :</b></td>
                         <td  >  <?  echo number_format(($total_margin/$order_qty_pcs)*12,2);?></td>
                         <td  title="CM Cost/Dzn(Avg)[$]/(Sew. SMV(Avg)*12)" >
                         	<b>
                         		<?                      
	                         		echo 'EPM';
	                         	
	                         	?>
                         	</b>
                         	
                         </td>
                         <td>
                         	<?
                        
                         		$cm_cost_d_avg=(($total_cm_cost/$total_order_qty*12)*$set_ratio);
                         		echo fn_number_format($cm_cost_d_avg/($tot_avg_sew_smv*12),3);
                         	
                         	?>
                         </td>
                    </tr>
					<tr bgcolor="<? echo $style ?>" align="left">
                         <td width="100"><b>Remarks :</b></td>
                         <td colspan="3"><? echo $remarks;?></td>                         
                    </tr>
                </table>
             </td>
             <td   width="250" height="50px" valign="middle">
                   <table width="100%"   cellpadding="0" class="rpt_table"  rules="all" cellspacing="0" border="1">
                       <tr>
                       	<td colspan="2" align="center">  <strong> Material Value For BTB</strong> </td>
                       </tr>
                        <tr>
                        	<td align="center"> <strong>Item</strong></td>
                            <td  align="center"> <strong>Value[$]</strong></td>
                        </tr>
                        <tr>
                        	<td> <strong>Yarn</strong> </td>
                            <td  align="right"><? echo number_format($total_yarn_costing,2);?> </td>
                        </tr>
                         <tr>
                        	<td> <strong>Trim </strong></td>
                            <td  align="right"><? echo number_format($total_trims_amt,2);?> </td>
                        </tr>
                         <tr>
                        	<td> <strong>Fabric(Purchase)</strong> </td>
                            <td  align="right"><? echo number_format($total_purchase_amt,2);?> </td>
                        </tr>

                         <tr bgcolor="#CCCCCC">
                        	<td> <strong>Total</strong> </td>
                            <td  align="right"><? echo number_format($total_yarn_costing+$total_trims_amt+$total_purchase_amt,2);?></td>
                        </tr>
                         <tr>
                            <td><strong> Machine/Line</strong></td>
                            <td align="center"><? echo $machine_line;?></td>
                        </tr>
                         <tr>
                            <td> <strong>Prod/Line/Hr</strong></td>
                            <td  align="center"><? echo $prod_line_hr;?></td>
                        </tr>
                      </table>
             </td>
                </tr>
            </table>
            <br/>
            <table width="600" style="margin-left:10px; font-size:16px" class="rpt_table" cellpadding="0" cellspacing="0" border="1" rules="all">
            <thead>
                <th> SL </th>
                <th>Particulars </th>
                <th>Cost[$] </th>
                <th>Amount[$] </th>
                <th>% </th>
            </thead>
            <tbody>

            <tr  bgcolor="<? echo $style1; ?>">
              <td>1  </td>
              <td><strong>Total FOB[$]: </strong> </td>
              <td  align="right">  <? // echo number_format($total_fob_value,2);?></td>
              <td  align="right">  <? echo number_format($total_fob_value,2);?></td>
              <td  align="right">  <? echo '100';?></td>
            </tr>
             <tr  bgcolor="<? echo $style; ?>">
              <td>2  </td>
              <td><strong>Fabric Cost: </strong> </td>
              <td  align="right">  <? echo number_format($total_fabric_amt,2);?></td>
              <td  align="right">  </td>
              <td  align="right">  <? echo number_format(($total_fabric_amt/$total_fob_value)*100,2);?></td>
            </tr>
             <tr bgcolor="<? echo $style1; ?>">
              <td >3  </td>
              <td><strong>Yarn Cost: </strong> </td>
              <td  align="right">   <? echo number_format($total_yarn_costing,2);?></td>
              <td  align="right"> </td>
              <td  align="right">  <? echo number_format(($total_yarn_costing/$total_fob_value)*100,2);?></td>
            </tr>
             <tr bgcolor="<? echo $style; ?>">
              <td >4  </td>
              <td><strong>Conversion Cost to Fabric: </strong> </td>
              <td  align="right">  <? echo number_format($total_conversion_cost,2);?></td>
              <td  align="right">  </td>
              <td  align="right">  <? echo number_format(($total_conversion_cost/$total_fob_value)*100,2);?></td>
            </tr>
            <tr bgcolor="<? echo $style1; ?>">
              <td >5  </td>
              <td><strong>Trims Cost: </strong> </td>
              <td  align="right">  <? echo number_format($total_trims_amt,2);?></td>
              <td  align="right">  </td>
              <td  align="right">  <? echo number_format(($total_trims_amt/$total_fob_value)*100,2);?></td>
            </tr>
            <tr bgcolor="<? echo $style; ?>">
              <td >6  </td>
              <td><strong>Emblishment Cost: </strong> </td>
              <td  align="right">  <? echo number_format($total_embl_amt+$total_wash_costing,2);?></td>
              <td  align="right">  </td>
              <td  align="right">  <? echo number_format((($total_embl_amt+$total_wash_costing)/$total_fob_value)*100,2);?></td>
            </tr>
             <tr bgcolor="<? echo $style1; ?>">
              <td >7  </td>
              <td><strong>Commercial Cost: </strong> </td>
              <td  align="right">  <? echo number_format($commamount,2);?></td>
              <td  align="right">  </td>
              <td  align="right">  <? echo number_format(($commamount/$total_fob_value)*100,2);?></td>
            </tr>
             <tr bgcolor="<? echo $style; ?>">
              <td >8  </td>
              <td><strong>Commission Cost: </strong> </td>
              <td  align="right">  <? echo number_format($total_commisssion,2);?></td>
              <td  align="right">  </td>
              <td  align="right">  <? echo number_format(($total_commisssion/$total_fob_value)*100,2);?></td>
            </tr>
             <tr bgcolor="<? echo $style1; ?>">
              <td >9  </td>
              <td><strong>Lab Test Cost: </strong> </td>
              <td  align="right">  <? echo number_format($total_lab_test_cost,2);?></td>
              <td  align="right">  </td>
              <td  align="right">  <? echo number_format(($total_lab_test_cost/$total_fob_value)*100,2);?></td>
            </tr>
             <tr bgcolor="<? echo $style; ?>">
              <td >10  </td>
              <td><strong>Inspection Cost: </strong> </td>
              <td  align="right">  <? echo number_format($total_inspection_cost,2);?></td>
              <td  align="right">  </td>
              <td  align="right">  <? echo number_format(($total_inspection_cost/$total_fob_value)*100,2);?></td>
            </tr>
             <tr bgcolor="<? echo $style1; ?>">
              <td >11  </td>
              <td><strong>CM Cost - IE: </strong> </td>
              <td  align="right">  <? echo number_format($total_cm_cost,2);?></td>
              <td  align="right">  </td>
              <td  align="right">  <? echo number_format(($total_cm_cost/$total_fob_value)*100,2);?></td>
            </tr>
             <tr bgcolor="<? echo $style; ?>">
              <td >12  </td>
              <td><strong>Freight Cost: </strong> </td>
              <td  align="right">  <? echo number_format($total_freight_cost,2);?></td>
              <td  align="right">  </td>
              <td  align="right">  <? echo number_format(($total_freight_cost/$total_fob_value)*100,2);?></td>
            </tr>
             <tr bgcolor="<? echo $style1; ?>">
              <td >13  </td>
              <td><strong>Currier Cost: </strong> </td>
              <td  align="right">  <? echo number_format($total_currier_cost,2);?></td>
              <td  align="right">  </td>
              <td  align="right">  <? echo number_format(($total_currier_cost/$total_fob_value)*100,2);?></td>
            </tr>
             <tr bgcolor="<? echo $style; ?>">
              <td >14  </td>
              <td><strong>Certificate Cost: </strong> </td>
              <td  align="right">  <? echo number_format($total_certificate_cost,2);?></td>
              <td  align="right">  </td>
              <td  align="right">  <? echo number_format(($total_certificate_cost/$total_fob_value)*100,2);?></td>
            </tr>
             <tr bgcolor="<? echo $style1; ?>">
              <td >15  </td>
              <td><strong>Office OH: </strong> </td>
              <td  align="right">  <? echo number_format($total_common_oh_cost,2);?></td>
              <td  align="right">&nbsp;  </td>
              <td  align="right">  <? echo number_format(($total_common_oh_cost/$total_fob_value)*100,2);?></td>
            </tr>
            <?
		
			//$tot_aop_trim_yd_cost=$tot_conversion_aop_costing+$total_trims_amt+$tot_conversion_yarn_dyeing_costing;
			//$total_aop_trim_yd_cost=($tot_aop_trim_yd_cost*10)/100;
			?>
			  <tr bgcolor="<? echo $style; ?>">
              <td >16  </td>
              <td><strong>Trims +AOP+Y/D (10%) </strong> </td>
              <td  align="right" title="10% on Total Trims+AOP+Y/D(<? echo $tot_aop_trim_yd_cost;?>)">  <? echo number_format($total_aop_trim_yd_cost,2);?></td>
              <td  align="right">  </td>
              <td  align="right">  <? echo '10';?></td>
            </tr>
			<?
			//$total_all_cost+=$total_aop_trim_yd_cost;
			
			?>

             <tr bgcolor="<? echo $style; ?>">
              <td >17  </td>
              <td><strong>Total Cost:</strong> </td>
              <td  align="right">&nbsp;  </td>
              <td  align="right">  <? echo number_format($total_all_cost,2);?></td>
              <td  align="right">  <? $tot_cost_percent=($total_all_cost/$total_fob_value)*100;echo number_format($tot_cost_percent,2);?></td>
            </tr>
             <tr bgcolor="<? echo $style1; ?>">
              <td >18  </td>
              <td><strong>Total Margin: </strong> </td>
              <td  align="right">&nbsp;  </td>
              <td  align="right" title="Total FOB Value-Total Cost"> <? $tot_margin=$total_fob_value-$total_all_cost; echo number_format($tot_margin,2);?> </td>
              <td  align="right">  <? $tot_margin_percent=($tot_margin/$total_fob_value)*100;echo number_format($tot_margin_percent,2);?></td>
            </tr>
             <tr bgcolor="<? echo $style; ?>">
              <td >19  </td>
              <td><strong>Total(%): </strong> </td>
              <td  align="right" colspan="2">  <? //echo number_format($total_embl_amt,2);?></td>
              <td  align="right">  <? echo number_format(100-($tot_cost_percent+$tot_margin_percent),2);?></td>
            </tr>
             <tr bgcolor="<? echo $style1; ?>">
              <td >20  </td>
              <td><strong>Margin/ DZN: </strong> </td>
              <td  align="right"  title="Total Margin/PoQty Pcs*12">  <? echo number_format(($tot_margin/$order_qty_pcs)*12,2);?></td>
              <td  align="right">  </td>
              <td  align="right">  <? //echo number_format(($total_embl_amt/$total_fob_value)*100,2);?></td>
            </tr>
             <tr bgcolor="<? echo $style; ?>">
              <td >21  </td>
              <td><strong>Price / Set: </strong> </td>
              <td  align="right" title="Total FOB Value/Po Qty">
			  <? $price_pcs_or_set=$total_fob_value/$order_qty_pcs;
			  	$cost_pcs_set=$total_all_cost/$order_qty_pcs;
			  	echo number_format($price_pcs_or_set,2);?></td>
              <td  align="right">  </td>
              <td  align="right">  <? //echo number_format(($total_embl_amt/$total_fob_value)*100,2);?></td>
            </tr>
             <tr bgcolor="<? echo $style1; ?>">
              <td >22  </td>
              <td><strong>Cost /Set: </strong> </td>
              <td  align="right" title="Total Cost/Po Qty">  <? echo number_format($cost_pcs_set,2);?></td>
              <td  align="right">  </td>
              <td  align="right">  <? //echo number_format(($total_embl_amt/$total_fob_value)*100,2);?></td>
            </tr>


            </tbody>
            </table>
           <br/>

	            <?
				$job_id_cond=where_con_using_array($job_idArr,0,'a.job_id');
			 	$lib_designation=return_library_array(" select id,custom_designation from lib_designation","id","custom_designation");

		 		//$data_array=sql_select("select b.approved_by,b.approved_no, b.approved_date,b.un_approved_reason, c.user_full_name,c.designation  from  wo_booking_mst a , approval_history b, user_passwd c where a.id=b.mst_id and b.approved_by=c.id and a.booking_no=$txt_booking_no and b.entry_form=7 order by b.id asc");
				$approv_data_array=sql_select(" select a.job_no,b.id,b.approved_by,b.approved_no, b.approved_date,b.un_approved_reason, c.user_full_name,c.designation,d.approval_cause from wo_pre_cost_mst a join approval_history b on a.id=b.mst_id join  user_passwd c on b.approved_by=c.id left join fabric_booking_approval_cause d on a.id =d.booking_id  where   b.entry_form=15 $job_id_cond order by b.id asc");
			  //echo " select a.job_no,b.id,b.approved_by,b.approved_no, b.approved_date,b.un_approved_reason, c.user_full_name,c.designation,d.approval_cause from wo_pre_cost_mst a join approval_history b on a.id=b.mst_id join  user_passwd c on b.approved_by=c.id left join fabric_booking_approval_cause d on a.id =d.booking_id  where   b.entry_form=15 $job_id_cond order by b.id asc";
      // echo " select a.job_no,b.id,b.approved_by,b.approved_no, b.approved_date,b.un_approved_reason, c.user_full_name,c.designation,d.approval_cause from wo_pre_cost_mst a join approval_history b on a.id=b.mst_id join  user_passwd c on b.approved_by=c.id left join fabric_booking_approval_cause d on a.id =d.booking_id  where   b.entry_form=15 $job_id_cond order by b.id asc";
			foreach($approv_data_array as $row)
			{
				$job_wise_approv[$row[csf('id')]]['job_no']=$row[csf('job_no')];
				$job_wise_approv[$row[csf('id')]]['approved_by']=$row[csf('approved_by')];
				$job_wise_approv[$row[csf('id')]]['approved_no']=$row[csf('approved_no')];
				$job_wise_approv[$row[csf('id')]]['approved_date']=$row[csf('approved_date')];
				$job_wise_approv[$row[csf('id')]]['un_approved_reason']=$row[csf('un_approved_reason')];
				$job_wise_approv[$row[csf('id')]]['user_full_name']=$row[csf('user_full_name')];
				$job_wise_approv[$row[csf('id')]]['designation']=$row[csf('designation')];
				$job_wise_approv[$row[csf('id')]]['approval_cause']=$row[csf('approval_cause')];
			}

	 	?>
	 	<table  width="650" style=" margin:5px;" class="rpt_table"   border="1" cellpadding="0" cellspacing="0" rules="all">
            <thead>
            <tr style="border:1px solid black;">
                <th colspan="6" style="border:1px solid black;">Approval Status</th>
                </tr>
                <tr style="border:1px solid black;">
                <th width="3%" style="border:1px solid black;">Sl</th>
                <th width="25%" style="border:1px solid black;">Job no</th>
                <th width="25%" style="border:1px solid black;">Name/Designation</th>
                <th width="27%" style="border:1px solid black;">Approval Date</th>
                <th width="15%" style="border:1px solid black;">Approval No</th>
                <th width="30%" style="border:1px solid black;">Un Approval Cause</th>
                </tr>
            </thead>
            <tbody>
            <?
			$i=1;
			foreach($job_wise_approv as $id=>$row){
				if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
			?>
          <tr style="border:1px solid black;" bgcolor="<? echo $bgcolor; ?>" onClick="change_color('trapp_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="trapp_<? echo $i; ?>" align="center">
                <td width="3%" style="border:1px solid black;"><? echo $i;?></td>
                 <td width="25%" style="border:1px solid black;"><? echo $row[('job_no')];?></td>
                <td width="25%" style="border:1px solid black;"><? echo $row[('user_full_name')]." / ". $lib_designation[$row[('designation')]];?></td>
                <td width="27%" style="border:1px solid black;"><? echo date("d-m-Y h:i:s",strtotime($row[('approved_date')]));?></td>
                <td width="15%" style="border:1px solid black;"><? echo $row[('approved_no')];?></td>
                <td width="30%" style="border:1px solid black;"><? echo $row[('approval_cause')];?></td>
                </tr>
                <?
				$i++;
			}
				?>
            </tbody>
        </table>
 	 	<br>
             <?
                 echo signature_table(109, $cbo_company_name, "850px");
            ?>
           <div id="page_break_div">

            </div>
           <table id="table_header_1" style="margin-left:10px" class="rpt_table" width="980" cellpadding="0" cellspacing="0" border="1" rules="all">
           	<caption> <b style="float:left">PO Details</b></caption>
					<thead>
                    	<th width="30">SL</th>
						<th width="80">Job</th>
						<th width="100">PO Number</th>
						<th width="100">PO Qty.</th>
						<th width="60">UOM</th>
                    	<th width="100">PO Qty.[Pcs]</th>
                        <th width="100">FOB/Pcs</th>
                        <th width="100"> FOB Value[$]</th>
						<th width="">CM Value</th>
                    </thead>
            </table>
                    <div class="scroll_div_inner"  style="width:980px;margin-left:10px; max-height:400px;overflow-y:scroll;" align="left" id="scroll_body1">
					<table class="rpt_table" width="980" cellpadding="0" cellspacing="0" border="1" rules="all" id="table_body1">
                    <?
					$i=1;$total_order_qty_pcss=0;$total_fob_val=$total_cm_value=0;$total_po_qty=0;
					foreach($sql_po_result as $row)
					{
						if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
						$order_qty_pcss=$row[csf('po_quantity')]*$row[csf('ratio')];
						$avg_unit=$row[csf('unit_price')];

					?>

						<tr bgcolor="<? echo $bgcolor;?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor;?>')" id="tr_<? echo $i; ?>">
							<td width="30"><? echo $i; ?></td>
							<td width="80"  align="center"><? echo $row[csf('job_prefix')]; ?></td>
							<td width="100" align="center"><div style="word-break:break-all"><? echo $row[csf('po_number')]; ?></div></td>
							<td width="100" align="right" ><div style="word-break:break-all"><? echo number_format($row[csf('po_quantity')],0); ?></div></td>
							<td width="60" align="center"><div style="word-break:break-all"><? echo $unit_of_measurement[$row[csf('order_uom')]]; ?></div></td>
                            <td width="100"  align="right"><div style="word-break:break-all"><? echo number_format($order_qty_pcss,0) ?></div></td>
                             <td width="100"  align="right"><div style="word-break:break-all"><? echo number_format($avg_unit,2); ?></div></td>

                            <td width="100" align="right"><div style="word-break:break-all"><? echo number_format($row[csf('po_quantity')]*$avg_unit,2); ?></div></td>
							<td width="" align="right"><div style="word-break:break-all"><? echo number_format($other_costing_arr[$row[csf('po_id')]]['cm_cost'],2); ?></div></td>


                            </tr>

                            <?

							$total_fob_val+=$row[csf('po_quantity')]*$avg_unit;
							$total_po_qty+=$row[csf('po_quantity')];
							$total_order_qty_pcss+=$order_qty_pcss;
							$total_cm_value+=$other_costing_arr[$row[csf('po_id')]]['cm_cost'];

							$i++;

					}
							?>
                            <tfoot>
                            <tr>
                            <th colspan="3"><strong>Total</strong> </th>
                            <th align="right"><strong><? echo number_format($total_po_qty,0);?> </strong></th>
                             <th align="right"><strong><? //echo number_format($total_up_charge_val,2);?> </strong></th>
                            <th align="right"><strong><? echo number_format($total_order_qty_pcss,0);?></strong> </th>
                            <th align="right"><strong><? //echo number_format($total_exfact_qty,0);?></strong> </th>
                             <th align="right"><strong><? echo number_format($total_fob_val,2);?></strong> </th>
							  <th align="right"><strong><? echo number_format($total_cm_value,2);?></strong> </th>
                            </tr>
                            </tfoot>

                    </table>
                    </div>
           <br/><br/>
           <table id="table_header_1" style="margin-left:10px" class="rpt_table" width="980" cellpadding="0" cellspacing="0" border="1" rules="all">
           	<caption> <b style="float:left">Fabric Details :</b></caption>
					<thead>
                    	<th width="30">SL</th>
                        <th width="100">Fab. Nature</th>
						<th width="200">Description</th>
						<th width="100">Source</th>
						<th width="100">Grey Qty</th>
						<th width="100">Fin. Qty </th>
                        <th width="50">UOM</th>
                        <th width="100">Rate</th>
                        <th width="100">Amount[$]</th>
                         <th width=""> %</th>
                    </thead>
            </table>
                    <div class="scroll_div_inner" style="width:1000px; max-height:400px;overflow-y:scroll;margin-left:10px" align="left" id="scroll_body1">
					<table class="rpt_table"   width="980" cellpadding="0" cellspacing="0" border="1" rules="all" id="table_body1">
                    <?
					$i=$m=1;$total_greycons=$total_fincons=$total_amount=$grand_total_greycons=$grand_total_fincons=$grand_total_amount=0;
					foreach($fabric_detail_arr as $fab_nat_key=>$fab_data)
					{
						foreach($fab_data as $uom_key=>$uom_data)
						{
							$y=1;
							foreach($uom_data as $desc_key=>$desc_data)
							{

								foreach($desc_data as $source_key=>$val)
								{

								if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
								$pre_fab_id=rtrim($val['pre_fab_id'],',');
								$pre_fab_ids=array_unique(explode(",",$pre_fab_id));
								$rate=$val['rate'];
								//$amount=$val['amount'];
								$fincons=$greycons=$amount=0;
								foreach($pre_fab_ids as $fab_id)
								{
									if($fab_nat_key==2) //Purchase
									{
										$fincons+=$fabric_qty['knit']['finish'][$fab_id][$uom_key];
										$greycons+=$fabric_qty['knit']['grey'][$fab_id][$uom_key];
										$amount+=$fabric_amount['knit']['grey'][$fab_id][$uom_key];
									}
									else
									{
										$fincons+=$fabric_qty['woven']['finish'][$fab_id][$uom_key];
										$greycons+=$fabric_qty['woven']['grey'][$fab_id][$uom_key];
										$amount+=$fabric_amount['woven']['grey'][$fab_id][$uom_key];
									}
								}

					?>

						<tr bgcolor="<? echo $bgcolor;?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor;?>')" id="tr_<? echo $i; ?>">  <?
                      	 if($y==1){
						?>
							<td width="30" rowspan="<? echo $uom_rowspan_arr[$fab_nat_key][$uom_key];?>"><? echo $m; ?></td>
							<td width="100" rowspan="<? echo $uom_rowspan_arr[$fab_nat_key][$uom_key];?>">
							<? echo $item_category[$fab_nat_key]; ?></td>
                             <?
							  }
							?>
							<td width="200" align="center"><div style="word-break:break-all"><? echo $desc_key; ?></div></td>
							<td width="100" align="center" ><div style="word-break:break-all"><? echo $fabric_source[$source_key]; ?></div></td>
							<td width="100" title="" align="right"><div style="word-break:break-all"><? echo number_format($greycons,4); ?></div></td>
                            <td width="100" title="" align="right"><div style="word-break:break-all"><? echo number_format($fincons,4); ?></div></td>

                            <td width="50" align="center"><? echo $unit_of_measurement[$uom_key]; ?></td>
                            <td width="100"  align="right"><div style="word-break:break-all"><? echo number_format($rate,4); ?></div></td>
                            <td width="100"  align="right"><div style="word-break:break-all"><? echo number_format($amount,4); ?></div></td>
                            <td width=""  align="right"><div style="word-break:break-all"><? echo number_format(($amount/$total_fob_value)*100,4); ?></div></td>
                            </tr>
                            <?
								$total_greycons+=$greycons;
								$total_fincons+=$fincons;
								$total_amount+=$amount;

								$grand_total_greycons+=$greycons;
								$grand_total_fincons+=$fincons;
								$grand_total_amount+=$amount;
								$y++;
								$i++;
									}
								}
								$m++;
							?>
                            <tr bgcolor="#F4F3C4">
                                <td>&nbsp; </td>
                                <td>&nbsp; </td>
                                <td>&nbsp;</td>
                                <td align="right"><strong>Sub Total</strong> </td>
                                <td align="right"><strong><? echo number_format($total_greycons,4);$total_greycons=0;?> </strong></td>
                                <td align="right"><strong><? echo number_format($total_fincons,4);$total_fincons=0;?> </strong></td>
                                <td align="right">&nbsp;</td>
                                <td>&nbsp; </td>
                                <td align="right"><strong><? echo number_format($total_amount,4);?></strong> </td>
                                <td align="right"><?  echo number_format(($total_amount/$total_fob_value)*100,4);$total_amount=0;?> </td>
                                </tr>
                            <?
							}
						}
							?>
                            <tfoot>
                            <tr>
                            <th colspan="4" ><strong>Grand Total</strong> </th>
                            <th align="right"><strong><? echo number_format($grand_total_greycons,4);?> </strong></th>
                            <th align="right"><strong><? echo number_format($grand_total_fincons,4);?> </strong></th>
                            <th align="right">&nbsp;</th>
                            <th>&nbsp; </th>
                            <th align="right"><strong><? echo number_format($grand_total_amount,4);?></strong> </th>
                            <th align="right"><?  echo number_format(($grand_total_amount/$total_fob_value)*100,4);?> </th>
                            </tr>
                            </tfoot>
                    </table>
                    </div><!--Fabtic Details End-->
            <br/><br/>
            <table id="table_header_1" style="margin-left:10px"  class="rpt_table" width="850" cellpadding="0" cellspacing="0" border="1" rules="all">
           	<caption> <b style="float:left">Yarn Details :</b></caption>
					<thead>
                    	<th width="100"></th>
                        <th width="250">Yarn Description</th>
						<th width="100">Yarn Qty.</th>
						<th width="100">Avg.Yarn Qnty</th>
                        <th width="100">Rate</th>
                        <th width="100">Amount[$]</th>
                        <th width="">%</th>
                    </thead>
            </table>
                    <div class="scroll_div_inner" style="width:870px; max-height:400px;overflow-y:scroll;margin-left:10px" align="left" id="scroll_body1">
					<table class="rpt_table" width="850" cellpadding="0" cellspacing="0" border="1" rules="all" id="table_body1">
                    <?


					$i=$m=1;$grand_total_yarncons=$grand_total_yarnavgcons=$grand_total_amount=$grand_total_yarn_per=0;
					foreach($yarn_detail_arr as $desc_key=>$val)
					{
					if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
					$yarn_cost=$val['yarn_cost'];
					$yarn_qty=$val['yarn_qty'];
					$yarncons_qnty=$yarn_qty;//$yarn_data_arr[$val["count_id"]][$val["copm_one_id"]][$val["percent_one"]][$val["type_id"]][$val["color"]][$val["rate"]]['qty'];
					$yarnavgcons_qnty=$yarn_qty;//$yarn_data_arr[$val["count_id"]][$val["copm_one_id"]][$val["percent_one"]][$val["type_id"]][$val["color"]][$val["rate"]]['qty'];
					$yarn_amount=$yarn_cost;//$yarn_data_arr[$val["count_id"]][$val["copm_one_id"]][$val["percent_one"]][$val["type_id"]][$val["color"]][$val["rate"]]['amount'];
					$totalyarn_amount=$totalyarn_detail_arr[100]['amount'];
					?>
						<tr bgcolor="<? echo $bgcolor;?>" onClick="change_color('try_<? echo $i; ?>','<? echo $bgcolor;?>')" id="try_<? echo $i; ?>"> 					 <?
                      	 if($m==1){
						?>
							<td width="100" valign="middle" rowspan="<? echo $row_span;?>"><? echo 'Yarn Cost'; ?></td>
                             <?
							 }
							?>
							<td width="250"><div style="word-break:break-all"><? echo $desc_key; ?></div></td>
							<td width="100" align="right"><div style="word-break:break-all"><? echo number_format($yarncons_qnty,4); ?></div></td>
							<td width="100" align="right" ><div style="word-break:break-all"><? echo number_format($yarnavgcons_qnty,4); ?></div></td>                       <td width="100" align="right"><? echo number_format($val["rate"],4); ?></td>
                            <td width="100"  align="right"><div style="word-break:break-all"><? echo number_format($yarn_amount,4); ?></div></td>
                             <?
							// $total_fob=$totalyarn_amount;
                      	//if($m==1){
						?>
                             <td width="" align="right" title="Yarn Amout/Total Fob*100" ><? echo number_format(($yarn_amount/$total_fob_value)*100,4); ?></td>
                              <?
							//}
							  ?>
                            </tr>
                            <?
								$grand_total_yarnavgcons+=$yarnavgcons_qnty;
								$grand_total_amount+=$yarn_amount;
								$grand_total_yarncons+=$yarncons_qnty;
								$grand_total_yarn_per+=($totalyarn_amount/$total_fob_value)*100;
								//$y++;
								$i++;
							$m++;
						}
							?>
                            <tfoot>
                            <tr>
                                <th><strong>Grand Total</strong> </th>
                                <th align="right"><strong><? //echo number_format($grand_total_amount,4);?></strong> </th>
                                <th align="right"><strong><? echo number_format($grand_total_yarncons,4);?> </strong></th>
                                <th align="right"><strong><? echo number_format($grand_total_yarnavgcons,4);?> </strong></th>
                                <th align="right"><strong><? //echo number_format($grand_total_amount,4);?></strong> </th>
                                <th align="right"><? echo number_format($grand_total_amount,4);?></th>
                                <th align="center"><strong><? echo number_format(($totalyarn_amount/$total_fob_value)*100,4);?></strong> </th>
                            </tr>
                            </tfoot>
                    </table>
                    </div>
                     <br/><br/>
            <table id="table_header_1" style="margin-left:10px"   class="rpt_table" width="850" cellpadding="0" cellspacing="0" border="1" rules="all">
           	<caption> <b style="float:left">Conversion Cost to Fabric :</b></caption>
					<thead>
                    	<th width="100"></th>
                        <th width="250">Particulars</th>
						<th width="100">Process</th>
						<th width="100">Required</th>
                        <th width="50">UOM</th>
                        <th width="100">Rate</th>
                        <th width="100">Amount[$]</th>
                        <th width="">%</th>
                    </thead>
            </table>
                    <div class="scroll_div_inner" style="width:870px; max-height:400px;overflow-y:scroll;margin-left:10px" align="left" id="scroll_body1">
					<table class="rpt_table" width="850" cellpadding="0" cellspacing="0" border="1" rules="all" id="table_body1">
                    <?
				   /*$sql_conv="select c.id as id,c.fabric_description as pre_costdtl_id, a.job_no, c.cons_process,c.req_qnty,c.charge_unit,c.amount,c.color_break_down, c.status_active,d.body_part_id,d.fab_nature_id,d.color_type_id,d.fabric_description,d.item_number_id,d.uom from wo_po_details_master a, wo_po_break_down b,wo_pre_cost_fab_conv_cost_dtls c left join wo_pre_cost_fabric_cost_dtls d on c.job_no=d.job_no and c.fabric_description=d.id  where a.job_no=b.job_no_mst and c.job_no=b.job_no_mst and c.job_no=a.job_no  and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $company_name_cond $style_owner_cond $job_no_cond $job_style_cond $order_cond $buyer_id_cond $season_cond  order by c.cons_process";*/

				    // $sql_conv="select c.id as id,c.fabric_description as pre_costdtl_id, a.job_no, c.cons_process,sum(c.req_qnty) as req_qnty,avg(c.charge_unit) as charge_unit,sum(c.amount) as amount,c.color_break_down,d.body_part_id,d.fab_nature_id,d.color_type_id,d.item_number_id,d.uom from wo_po_details_master a, wo_po_break_down b,wo_pre_cost_fab_conv_cost_dtls c left join wo_pre_cost_fabric_cost_dtls d on c.job_no=d.job_no and c.fabric_description=d.id  where a.job_no=b.job_no_mst and c.job_no=b.job_no_mst and c.job_no=a.job_no  and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0  $company_name_cond $style_owner_cond $job_no_cond $job_style_cond $order_cond $buyer_id_cond $season_cond $file_no_cond $file_po_idCond group by c.id,c.fabric_description,a.job_no, c.cons_process,c.color_break_down,d.body_part_id,d.fab_nature_id,d.color_type_id,d.item_number_id,d.uom  order by c.cons_process";

            $sql_conv="select c.id as id,c.fabric_description as pre_costdtl_id, a.job_no, c.cons_process,sum(c.req_qnty) as req_qnty,avg(c.charge_unit) as charge_unit,sum(c.amount) as amount
            ,c.color_break_down,e.body_part_id,e.fab_nature_id,e.color_type_id,e.item_number_id,d.uom,e.approved_no from wo_po_details_master a, wo_po_break_down b,wo_pre_cost_fab_conv_cost_dtls c left join wo_pre_cost_fabric_cost_dtls d on c.job_no=d.job_no and c.fabric_description=d.id   left join wo_pre_cost_fabric_cost_dtls_h e on d.id=e.PRE_COST_FABRIC_COST_DTLS_ID  where a.job_no=b.job_no_mst and c.job_no=b.job_no_mst and c.job_no=a.job_no and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 $company_name_cond $style_owner_cond $job_no_cond $job_style_cond $order_cond $buyer_id_cond $season_cond $file_no_cond $file_po_idCond and e.approved_no=$revised_no  group by c.id,c.fabric_description,a.job_no, c.cons_process,c.color_break_down,e.body_part_id,e.fab_nature_id,e.color_type_id,e.item_number_id,d.uom ,e.approved_no order by c.cons_process";

          
					$result_conv=sql_select($sql_conv);
					$conv_detail_arr=array();
					$totalconv_amount=0;
					foreach($result_conv as $row)
					{
						$item_descrition = $body_part[$row[csf("body_part_id")]].", ".$color_type[$row[csf("color_type_id")]].", ".$row[csf("pre_costdtl_id")];
						$row_span+=1;
						/*$conv_detail_arr[$row[csf("cons_process")]][$row[csf("id")]]['id']=$row[csf("id")];
						$conv_detail_arr[$row[csf("cons_process")]][$row[csf("id")]]['uom']=$row[csf("uom")];
						$conv_detail_arr[$row[csf("cons_process")]][$row[csf("id")]]['charge_unit']=$row[csf("charge_unit")];
						$conv_detail_arr[$row[csf("cons_process")]][$row[csf("id")]]['amount']=$row[csf("amount")];
						$conv_detail_arr[$row[csf("cons_process")]][$row[csf("id")]]['cons_process']=$row[csf("cons_process")];
						$conv_detail_arr[$row[csf("cons_process")]][$row[csf("id")]]['desc']=$item_descrition;

						$conv_detail_process_arr[$row[csf("cons_process")]]=$row[csf("cons_process")];
						$convamount=$conv_data_amount_arr[$row[csf("id")]][$row[csf("uom")]];
						$totalconv_detail_arr[100]['amount']+=$convamount;*/
						$conv_detail_arr[$item_descrition][$row[csf("cons_process")]]['id'].=$row[csf("id")].',';
						$conv_detail_arr[$item_descrition][$row[csf("cons_process")]]['uom']=$row[csf("uom")];
						$conv_detail_arr[$item_descrition][$row[csf("cons_process")]]['charge_unit']=$row[csf("charge_unit")];
						$conv_detail_arr[$item_descrition][$row[csf("cons_process")]]['amount']=$row[csf("amount")];
						$conv_detail_arr[$item_descrition][$row[csf("cons_process")]]['cons_process']=$row[csf("cons_process")];
						$conv_detail_arr[$item_descrition][$row[csf("cons_process")]]['desc']=$item_descrition;

						$conv_detail_process_arr[$row[csf("cons_process")]]=$row[csf("cons_process")];

						$convamount=$conv_data_amount_arr[$row[csf("id")]][$row[csf("uom")]];
						$convQty=$conv_data_qty_arr[$row[csf("id")]][$row[csf("uom")]];
						$totalconv_detail_arr[100]['amount']+=$convamount;
						$conv_detail_arrData[$item_descrition][$row[csf("cons_process")]]['amt']+=$convamount;
						$conv_detail_arrData[$item_descrition][$row[csf("cons_process")]]['req_qty']+=$convQty;

						//$totalconv_amount+=$convamount;
					}
					//echo $totalconv_amount;
					//print_r($totalconv_detail_arr);
					$conv_rowspan_arr=array();

					foreach($conv_detail_arr as $desc_key=>$desc_data)
					{

							$conv_row_span=0;
							foreach($desc_data as $process_key=>$val)
							{
								$conv_row_span++;
							}
							$conv_rowspan_arr[$desc_key]=$conv_row_span;
					}
					//print_r($conv_rowspan_arr);

					$i=$m=1;$grand_total_conv_qty=$grand_total_conv_amount=$grand_total_amount=$total_conv_qty=$total_conv_amount=0;
					foreach($conv_detail_arr as $desc_key=>$desc_data)
					{
						$z=1;

						foreach($desc_data as $process_key=>$val)
						{
						if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";

						$conv_id=rtrim($val[('id')],',');
						$conv_ids=array_unique(explode(",",$conv_id));
						//$desc_key=$val[('desc')];
						/*$convsion_qty=$conversion_amt=0;
						foreach($conv_ids as $cid)
						{
							$convsion_qty+=$conv_data_qty_arr[$cid][$val[('uom')]];
							$conversion_amt+= $conv_data_amount_arr[$cid][$val[('uom')]];
						}*/
						$conversion_amt=$conv_detail_arrData[$desc_key][$process_key]['amt'];
						$convsion_qty=$conv_detail_arrData[$desc_key][$process_key]['req_qty'];

						$totalconv_amount=$totalconv_detail_arr[100]['amount'];
						$process_name=$conversion_cost_head_array[$process_key];
					?>
						<tr bgcolor="<? echo $bgcolor;?>" onClick="change_color('trconv_<? echo $i; ?>','<? echo $bgcolor;?>')" id="trconv_<? echo $i; ?>"> 					 <?
                      	 if($z==1){
						?>
							<td width="100" valign="middle" rowspan="<? echo $conv_rowspan_arr[$desc_key];?>"><? echo 'Conversion Cost'; ?></td>
                             <?
							 }
							?>
							<td width="250"><div style="word-break:break-all"><? echo $desc_key; ?></div></td>
							<td width="100" align="right"><div style="word-break:break-all"><? echo $process_name; ?></div></td>
							<td width="100" align="right" ><div style="word-break:break-all"><? echo number_format($convsion_qty,4); ?></div>
                            <td width="50" align="right"><? echo $unit_of_measurement[$val["uom"]]; ?></td></td>
                            <td width="100" align="right"><? echo number_format($val["charge_unit"],4); ?></td>
                            <td width="100"  align="right"><div style="word-break:break-all"><? echo number_format($conversion_amt,4); ?></div></td>
                             <?
                      //	if($z==1){
						?>
                             <td width="" valign="middle" align="center" title="Conv. Amout(<? echo $totalconv_amount?>)/Total Fob*100" rowspan="<? //echo $conv_rowspan_arr[$desc_key];?>"><? echo  number_format(($conversion_amt/$total_fob_value)*100,4);//number_format(($totalconv_amount/$total_fob_value)*100,2); ?></td>
                              <?
							//}
							  ?>
                            </tr>
                            <?
								$total_conv_qty+=$convsion_qty;
								$total_conv_amount+=$conversion_amt;
								$grand_total_conv_qty+=$convsion_qty;
								$grand_total_conv_amount+=$conversion_amt;

								$z++;
								$i++;

								}
								?>
                               <tr  bgcolor="#F4F3C4" class="tbl_bottom">
                                <td colspan="3" align="right"><strong>Sub Total</strong> </td>
                                <td align="right"><strong><? echo number_format($total_conv_qty,4);$total_conv_qty=0;?> </strong></td>
                                <td align="right"><strong>&nbsp; </strong></td>

                                <td align="right">&nbsp;</td>
                                <td align="right"><? $sub_tot_fab_conv_cost_per=($total_conv_amount/$total_fob_value)*100;echo number_format($total_conv_amount,4);$total_conv_amount=0;?></td>
                                <td align="right"><? echo number_format($sub_tot_fab_conv_cost_per,4);?></td>
                            </tr>
                                <?
							}

							?>

                            <tfoot>
                            <tr>
                                <th colspan="3"><strong>Grand Total</strong> </th>
                                <th align="right"><strong><? echo number_format($grand_total_conv_qty,4);?> </strong></th>
                                <th align="right"><strong>&nbsp; </strong></th>

                                <th align="right">&nbsp;</th>
                                <th align="right"><? echo number_format($grand_total_conv_amount,4);?></th>
                                <th align="right"><? echo number_format(($grand_total_conv_amount/$total_fob_value)*100,2);?> </th>
                            </tr>
                            </tfoot>
                    </table>
                    </div>
                      <br/><br/>
            <table id="table_header_1" style="margin-left:10px"   class="rpt_table" width="610" cellpadding="0" cellspacing="0" border="1" rules="all">
           	<caption> <b style="float:left">Conversion Cost to Fabric Summary:</b></caption>
					<thead>
                    	<th width="100">Particulars</th>

						<th width="100">Process</th>
						<th width="100">Required</th>
                        <th width="50">UOM</th>
                        <th width="100">Rate</th>
                        <th width="100">Amount($)</th>
                        <th width="">%</th>
                    </thead>
            </table>
                    <div class="scroll_div_inner" style="width:630px; max-height:400px;overflow-y:scroll;margin-left:10px" align="left" id="scroll_body1">
					<table class="rpt_table" width="610" cellpadding="0" cellspacing="0" border="1" rules="all" id="table_body1">
                    <?
				  $sql_conv_sum="select c.id as id,c.fabric_description as pre_costdtl_id, a.job_no, c.cons_process,sum(c.req_qnty) as req_qnty,avg(c.charge_unit) as charge_unit,sum(c.amount) as amount,c.color_break_down,d.body_part_id,d.fab_nature_id,d.color_type_id,d.item_number_id,d.uom from wo_po_details_master a, wo_po_break_down b,wo_pre_cost_fab_conv_cost_dtls c left join wo_pre_cost_fabric_cost_dtls d on c.job_no=d.job_no and c.fabric_description=d.id  where a.job_no=b.job_no_mst and c.job_no=b.job_no_mst and c.job_no=a.job_no  and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 $company_name_cond $style_owner_cond $job_no_cond $job_style_cond $order_cond $buyer_id_cond $season_cond $file_no_cond $file_po_idCond group by c.id,c.fabric_description,a.job_no, c.cons_process,c.color_break_down,d.body_part_id,d.fab_nature_id,d.color_type_id,d.item_number_id,d.uom order by c.cons_process";

					$result_conv_sum=sql_select($sql_conv_sum);
					$conv_detail_arr=array();
					foreach($result_conv_sum as $row)
					{
						$item_descrition = $body_part[$row[csf("body_part_id")]].", ".$color_type[$row[csf("color_type_id")]].", ".$row[csf("fabric_description")];
						//$row_span+=1;
						/*$conv_detail_arr[$row[csf("cons_process")]][$row[csf("id")]]['id']=$row[csf("id")];
						$conv_detail_arr[$row[csf("cons_process")]][$row[csf("id")]]['uom']=$row[csf("uom")];
						$conv_detail_arr[$row[csf("cons_process")]][$row[csf("id")]]['charge_unit']=$row[csf("charge_unit")];
						$conv_detail_arr[$row[csf("cons_process")]][$row[csf("id")]]['amount']=$row[csf("amount")];
						$conv_detail_arr[$row[csf("cons_process")]][$row[csf("id")]]['cons_process']=$row[csf("cons_process")];
						$conv_detail_arr[$row[csf("cons_process")]][$row[csf("id")]]['desc']=$item_descrition;

						$conv_detail_process_arr[$row[csf("cons_process")]]=$row[csf("cons_process")];
						$convamount=$conv_data_amount_arr[$row[csf("id")]][$row[csf("uom")]];
						$totalconv_detail_arr[100]['amount']+=$convamount;*/
						$sum_conv_detail_arr[$row[csf("cons_process")]]['id'].=$row[csf("id")].',';
						$sum_conv_detail_arr[$row[csf("cons_process")]]['uom']=$row[csf("uom")];
						//$sum_conv_detail_arr[$row[csf("cons_process")]]['charge_unit']=$row[csf("charge_unit")];
						$sum_conv_detail_arr[$row[csf("cons_process")]]['amount']=$row[csf("amount")];
						$sum_conv_detail_arr[$row[csf("cons_process")]]['cons_process']=$row[csf("cons_process")];
						$sum_conv_detail_arr[$row[csf("cons_process")]]['desc']=$item_descrition;

						$conv_detail_process_arr[$row[csf("cons_process")]]=$row[csf("cons_process")];
						$convamount=$conv_data_amount_arr[$row[csf("id")]][$row[csf("uom")]];
						$convQty=$conv_data_qty_arr[$row[csf("id")]][$row[csf("uom")]];
						$totalconv_detail_arr2[100]['amount']+=$convamount;

						$sum_conv_detail_arr[$row[csf("cons_process")]]['amt']+=$convamount;
						$sum_conv_detail_arr[$row[csf("cons_process")]]['req_qty']+=$convQty;

						$sum_conv_rowspan_arr[$row[csf("cons_process")]]+=1;
					}
							$sconv_row_span=1;$row_span=0;
							foreach($sum_conv_detail_arr as $process_key=>$val)
							{
								$row_span+=$sconv_row_span;
								$sum_conv_detail_arr[$process_key]['charge_unit']=($val['amt']/$val['req_qty']);
							}
					//print_r($sum_conv_rowspan_arr);
						$i=$m=1;$sum_grand_total_conv_qty=$sum_grand_total_conv_amount=$grand_total_amount=$total_conv_qty=$total_conv_amount=0;
						foreach($sum_conv_detail_arr as $process_key=>$val)
						{
							if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";

							$conv_id=rtrim($val[('id')],',');
							$conv_ids=array_unique(explode(",",$conv_id));
							$desc_key=$val[('desc')];/*$sum_convsion_qty=$sum_conversion_amt=0;
							foreach($conv_ids as $cid)
							{
								$sum_convsion_qty+=$conv_data_qty_arr[$cid][$val[('uom')]];
								$sum_conversion_amt+= $conv_data_amount_arr[$cid][$val[('uom')]];
							}*/
							$sum_convsion_qty=$val['req_qty'];
							$sum_conversion_amt=$val['amt'];

							$totalconv_amount_sum=$totalconv_detail_arr2[100]['amount'];
							$process_name=$conversion_cost_head_array[$process_key];
					?>
						<tr bgcolor="<? echo $bgcolor;?>" onClick="change_color('trconvs_<? echo $i; ?>','<? echo $bgcolor;?>')" id="trconvs_<? echo $i; ?>"> 					 <?
                      	 if($m==1){
						?>
							<td width="100" valign="middle" rowspan="<? echo $row_span;?>"><? echo 'Conversion Cost'; ?></td>
                             <?
							 }
							?>

							<td width="100" align="right"><div style="word-break:break-all"><? echo $process_name; ?></div></td>
							<td width="100" align="right" ><div style="word-break:break-all"><? echo number_format($sum_convsion_qty,4); ?></div>
                            <td width="50" align="right"><? echo $unit_of_measurement[$val["uom"]]; ?></td></td>
                            <td width="100" align="right"><? echo number_format($val["charge_unit"],4); ?></td>
                            <td width="100"  align="right"><div style="word-break:break-all"><? echo number_format($sum_conversion_amt,4); ?></div></td>
                             <?
                      //	if($m==1){
						?>
                             <td width="" valign="middle" align="center" title="Total Conv. Amout(<? echo $totalconv_amount_sum ?>)/Total Fob*100" rowspan="<? //echo $row_span;?>"><? echo number_format(($sum_conversion_amt/$total_fob_value)*100,4); ?></td>
                              <?
							//}
							  ?>
                            </tr>
                            <?
								//$total_conv_qty+=$sum_convsion_qty;
								//$total_conv_amount+=$sum_conversion_amt;
								$sum_grand_total_conv_qty+=$sum_convsion_qty;
								$sum_grand_total_conv_amount+=$sum_conversion_amt;

								$m++;
								$i++;


								?>

                                <?
							}

							?>

                            <tfoot>
                            <tr>
                                <th colspan="2"><strong>Grand Total</strong> </th>
                                <th align="right"><strong><? echo number_format($sum_grand_total_conv_qty,4);?> </strong></th>
                                <th align="right"><strong>&nbsp; </strong></th>

                                <th align="right">&nbsp;</th>
                                <th align="right"><? echo number_format($sum_grand_total_conv_amount,4);?></th>
                                <th align="right"><? echo number_format(($sum_grand_total_conv_amount/$total_fob_value)*100,4); ?> </th>
                            </tr>
                            </tfoot>
                    </table>
                    </div>
                     <br/><br/>
            <table id="table_header_1" style="margin-left:10px"   class="rpt_table" width="890" cellpadding="0" cellspacing="0" border="1" rules="all">
           	<caption> <b style="float:left">Trims Cost Details :</b></caption>
					<thead>
                    	<th width="30">SL</th>
                        <th width="110">Item Group</th>
						<th width="200">Description</th>
						<th width="130">Nominated Supp</th>
                        <th width="50">UOM</th>
                        <th width="100">Consumption</th>
                        <th width="100">Avg Rate</th>
                        <th width="100">Amount($)</th>
                        <th width="">%</th>
                    </thead>
            </table>
                    <div class="scroll_div_inner" style="width:910px; max-height:400px;overflow-y:scroll;margin-left:10px" align="left" id="scroll_body1">
					<table class="rpt_table" width="890" cellpadding="0" cellspacing="0" border="1" rules="all" id="table_body1">
                    <?
					$trim_group_arr=return_library_array( "select item_name,id from  lib_item_group", "id", "item_name" );
				/*   $sql_trims="select c.id, c.job_no, c.trim_group,c.description,c.brand_sup_ref,c.cons_uom, c.cons_dzn_gmts, c.rate, c.amount, c.apvl_req, c.nominated_supp,c.status_active from wo_po_details_master a, wo_po_break_down b,wo_pre_cost_trim_cost_dtls c  where a.job_no=b.job_no_mst and c.job_no=b.job_no_mst and c.job_no=a.job_no  and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $company_name_cond $style_owner_cond $job_no_cond $job_style_cond $order_cond $buyer_id_cond $season_cond $file_no_cond  order by c.id";*/
				 // print_r($trims_item_amount_arr);

				//  $sql_trims="select c.trim_group,c.description,c.cons_uom, c.nominated_supp from wo_po_details_master a, wo_po_break_down b,wo_pre_cost_trim_cost_dtls c  where a.job_no=b.job_no_mst and c.job_no=b.job_no_mst and c.job_no=a.job_no  and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0  and c.status_active=1 and c.is_deleted=0 $company_name_cond $style_owner_cond $job_no_cond $job_style_cond $order_cond $buyer_id_cond $season_cond $file_no_cond $file_po_idCond group by  c.trim_group,c.description,c.cons_uom,c.nominated_supp  order by c.trim_group";
        $sql_trims="select d.trim_group,d.description,d.cons_uom, d.nominated_supp,d.approved_no from wo_po_details_master a, wo_po_break_down b,wo_pre_cost_trim_cost_dtls c join wo_pre_cost_trim_cost_dtls_his d on d.PRE_COST_TRIM_COST_DTLS_ID=c.id and c.job_no=d.job_no where a.job_no=b.job_no_mst and c.job_no=b.job_no_mst and c.job_no=a.job_no and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 $company_name_cond $style_owner_cond $job_no_cond $job_style_cond $order_cond $buyer_id_cond $season_cond $file_no_cond $file_po_idCond and d.approved_no=$revised_no and a.buyer_name=26 group by d.trim_group,d.description,d.cons_uom,d.nominated_supp,d.approved_no order by d.trim_group ";


        

					$result_trims=sql_select($sql_trims);
					$trims_detail_arr=array();
					foreach($result_trims as $row)
					{
						$item_descrition =$row[csf("description")];
						$trims_rowspan+=1;
						$trims_detail_arr[$row[csf("trim_group")]][$item_descrition][$row[csf("cons_uom")]]['nominated_supp']=$row[csf("nominated_supp")];
						$trims_detail_arr[$row[csf("trim_group")]][$item_descrition][$row[csf("cons_uom")]]['uom']=$row[csf("cons_uom")];
						$trims_detail_arr[$row[csf("trim_group")]][$item_descrition][$row[csf("cons_uom")]]['trim_group']=$row[csf("trim_group")];
						$trims_detail_arr[$row[csf("trim_group")]][$item_descrition][$row[csf("cons_uom")]]['amount']=$row[csf("amount")];
						$trims_detail_arr[$row[csf("trim_group")]][$item_descrition][$row[csf("cons_uom")]]['rate']=$row[csf("rate")];
						$trims_detail_arr[$row[csf("trim_group")]][$item_descrition][$row[csf("cons_uom")]]['id'].=$row[csf("id")].',';
						$trims_detail_arr[$row[csf("trim_group")]][$item_descrition][$row[csf("cons_uom")]]['desc']=$item_descrition;
						//$conv_detail_process_arr[$row[csf("cons_process")]]=$row[csf("cons_process")];
						//$trimsamount=$trims_item_amount_arr[$row[csf("trim_group")]][$item_descrition];//$trim_arr_amount[$row[csf("id")]];
						//$totaltrims_detail_arr[100]['amount']+=$trimsamount;
					}
					//echo $trims_rowspan;
					//print_r($totalconv_detail_arr);
					/*$trim_rowspan_arr=array();
					foreach($trims_detail_arr as $trims_key=>$trims_data)
					{
							$conv_row_span=0;
							foreach($trims_data as $desc_key=>$val)
							{
								$conv_row_span++;
							}
							$trim_rowspan_arr[$trims_key]=$conv_row_span;
					}*/
					//echo $conv_row_span;
					//print_r($conv_rowspan_arr);
					$i=$z=1;$grand_total_trim_amount=0;
					foreach($trims_detail_arr as $trims_key=>$trims_data)
					{
						foreach($trims_data as $desc_key=>$desc_data)
						{
							foreach($desc_data as $uom_key=>$trims_data)
							{

							if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
							$trim_amount=$trims_item_amount_arr[$trims_key][$desc_key];
							$cons_dzn_gmts=$trims_item_qty_arr[$trims_key][$desc_key];
							//$trim_group=$val[('trim_group')];
							$nominated_supp=$val[('nominated_supp')];
						//	$totaltrims_amount=$totaltrims_detail_arr[100]['amount'];
							//$trims_rowspan=$trim_rowspan_arr[$trims_key];
							$avg_rate=$trim_amount/$cons_dzn_gmts;
					?>
						<tr bgcolor="<? echo $bgcolor;?>" onClick="change_color('trtrim_<? echo $i; ?>','<? echo $bgcolor;?>')" id="trtrim_<? echo $i; ?>">
							<td width="30">
							<? echo $i; ?></td>
                            <td width="110"><div style="word-break:break-all"><? echo $trim_group_arr[$trims_key]; ?></div></td>
							<td width="200" align="right"><div style="word-break:break-all"><? echo $desc_key; ?></div></td>
							<td width="130" align="right" ><div style="word-break:break-all"><? echo $supplier_library[$nominated_supp]; ?></div>
                            <td width="50" align="right"><? echo $unit_of_measurement[$uom_key]; ?></td></td>
                            <td width="100" align="right"><? echo number_format($cons_dzn_gmts,4); ?></td></td>
                            <td width="100" align="right"><? echo number_format($avg_rate,4); ?></td>
                            <td width="100"  align="right"><div style="word-break:break-all">
							<? echo number_format($trim_amount,4); ?> </div></td>
                            <? //if($z==1) { ?>
                             <td width=""  align="center" title="Trims Amount/Total Fob Value*100">
							<? echo number_format(($trim_amount/$total_fob_value)*100,4); ?></td>
                            <?
							//}
							?>
                            </tr>
                            <?
								$grand_total_trim_amount+=$trim_amount;
								$i++;//$z++;
										}
									}
							}	?>
                            <tfoot>
                            <tr>
                                <th colspan="4"><strong>Grand Total</strong> </th>
                                <th align="right"><strong><? //echo number_format($grand_total_conv_qty,4);?> </strong></th>
                                <th align="right"><strong>&nbsp; </strong></th>

                                <th align="right">&nbsp;</th>
                                <th align="right"><? echo number_format($grand_total_trim_amount,4);?></th>
                                <th align="right"><? echo number_format(($grand_total_trim_amount/$total_fob_value)*100,4); ?></th>
                            </tr>
                            </tfoot>
                    </table>
                    </div>
               <br/><br/>
            <table id="table_header_1" style="margin-left:10px"   class="rpt_table" width="700" cellpadding="0" cellspacing="0" border="1" rules="all">
           	<caption> <b style="float:left">Embellishment Details :</b></caption>
					<thead>
                    	<th width="30">SL</th>
                        <th width="120">Particulars</th>
						<th width="100">Type</th>
						<th width="100">Gmts. Qnty (Dzn)</th>
                        <th width="100">Color</th>
                        <th width="100">Avg Rate</th>
                        <th width="100">Amount($)</th>
                        <th width="">%</th>
                    </thead>
            </table>
                    <div class="scroll_div_inner" style="width:720px; max-height:400px;overflow-y:scroll;margin-left:10px" align="left" id="scroll_body1">
					<table class="rpt_table" width="700" cellpadding="0" cellspacing="0" border="1" rules="all" id="table_body1">
                    <?
			$color_library=return_library_array( "select id,color_name from  lib_color", "id", "color_name"  );
				  /* $sql_emblish="select c.id, c.job_no, c.emb_name,c.emb_type,c.cons_dzn_gmts,c.rate, c.amount from wo_po_details_master a, wo_po_break_down b,wo_pre_cost_embe_cost_dtls c  where a.job_no=b.job_no_mst and c.job_no=b.job_no_mst and c.job_no=a.job_no  and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $company_name_cond $style_owner_cond $job_no_cond $job_style_cond $order_cond $buyer_id_cond $season_cond $file_no_cond $file_po_idCond order by c.id";*/

				    //  $sql_emblish="select b.id as po_id,c.id, c.job_no, c.emb_name,c.emb_type,d.color_number_id,e.requirment as cons_dzn_gmts,e.rate, e.amount from wo_po_details_master a, wo_po_break_down b,wo_pre_cost_embe_cost_dtls c,wo_po_color_size_breakdown d,wo_pre_cos_emb_co_avg_con_dtls e  where a.id=b.job_id and c.job_id=b.job_id and c.job_id=a.id  and d.po_break_down_id=b.id and d.item_number_id= e.item_number_id and d.color_number_id=e.color_number_id and d.size_number_id=e.size_number_id and c.id=e.pre_cost_emb_cost_dtls_id  and  a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0  and d.status_active=1 and d.is_deleted=0  and e.status_active=1 and e.is_deleted=0  $company_name_cond $style_owner_cond $job_no_cond $job_style_cond $order_cond $buyer_id_cond $season_cond $file_no_cond $file_po_idCond order by c.id";


             $sql_emblish="select b.id as po_id,c.id, c.job_no, f.emb_name,f.emb_type,d.color_number_id,e.requirment as cons_dzn_gmts,e.rate, e.amount,f.approved_no from wo_po_details_master a, wo_po_break_down b,wo_pre_cost_embe_cost_dtls c join wo_pre_cost_embe_cost_dtls_his f on f.PRE_COST_EMBE_COST_DTLS_ID=c.id ,wo_po_color_size_breakdown d,wo_pre_cos_emb_co_avg_con_dtls e where a.id=b.job_id and c.job_id=b.job_id and c.job_id=a.id and d.po_break_down_id=b.id and d.item_number_id= e.item_number_id and d.color_number_id=e.color_number_id and d.size_number_id=e.size_number_id and c.id=e.pre_cost_emb_cost_dtls_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and d.status_active=1 and d.is_deleted=0 and e.status_active=1 and e.is_deleted=0  $company_name_cond $style_owner_cond $job_no_cond $job_style_cond $order_cond $buyer_id_cond $season_cond $file_no_cond $file_po_idCond and f.approved_no=$revised_no group by  b.id ,c.id, c.job_no, f.emb_name,f.emb_type,d.color_number_id,e.requirment ,e.rate, e.amount,f.approved_no 
               order by c.id ";

         //    echo $sql_emblish;


					$result_emblish=sql_select($sql_emblish);
					$emblish_detail_arr=array();
					foreach($result_emblish as $row)
					{
						$item_descrition =$row[csf("description")];
						$color_id =$row[csf("color_number_id")];
						$embData =$row[csf("emb_name")];
						$embl_rowspan+=1;
						$emblish_detail_arr[$embData][$row[csf("emb_type")]][$color_id]['emb_name']=$row[csf("emb_name")];
						$emblish_detail_arr[$embData][$row[csf("emb_type")]][$color_id]['emb_type']=$row[csf("emb_type")];
						$emblish_detail_arr[$embData][$row[csf("emb_type")]][$color_id]['cons_dzn_gmts']+=$row[csf("cons_dzn_gmts")];
						$emblish_detail_arr[$embData][$row[csf("emb_type")]][$color_id]['amount']+=$row[csf("amount")];
						$emblish_detail_arr[$embData][$row[csf("emb_type")]][$color_id]['rate']=$row[csf("rate")];
						$emblish_detail_arr[$embData][$row[csf("emb_type")]][$color_id]['job_no'].=$row[csf("job_no")].',';
						$emblish_detail_arr[$embData][$row[csf("emb_type")]][$color_id]['po_id'].=$row[csf("po_id")].',';
						$emblish_detail_arr[$embData][$row[csf("emb_type")]][$color_id]['desc']=$item_descrition;
						//$emblishment_qty_arr
						
						$embsamount=$emblishment_amount_arr[$row[csf("job_no")]][$row[csf("id")]];
						$totalemb_detail_arr[100]['amount']+=$embsamount;
					}
					//echo $embl_rowspan;
					//print_r($conv_rowspan_arr);
					
					//$emblishment_po_amount_arr=$emblishment->getAmountArray_by_OrderEmblishmentidAndGmtscolor();
				//$emblishment_po_qty_arr
					foreach($emblish_detail_arr as $emb_name=>$enm_val)
					{
						$embl_row_span=0;
						foreach($enm_val as $emb_type=>$emb_typeData)
						{
							$embl_typerow_span=0;
							foreach($emb_typeData as $color_id=>$val)
							{
								$embl_row_span++;$embl_typerow_span++;
							}
							$emb_rowspan_arr[$emb_name]=$embl_row_span;
							$emb_rowspan_arr[$emb_name][$embl_typerow_span]=$embl_typerow_span;
						
						}
						
					}
					//print_r($emb_rowspan_arr);
				
					$i=$m=1;$grand_total_embl_amount=$grand_total_cons_dzn_gmts=0;
					foreach($emblish_detail_arr as $emb_name=>$enm_val)
					{
						$emb=0;
						foreach($enm_val as $emb_type=>$emb_typeData)
						{
						foreach($emb_typeData as $color_id=>$val)
						{

						if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
						$job_no=rtrim($val[('job_no')],',');
						$job_nos=array_unique(explode(",",$job_no));
						$po_id=rtrim($val[('po_id')],',');
						$po_ids=array_unique(explode(",",$po_id));
						//$emb_name=$val[('emb_name')];$emb_type=$val[('emb_type')];
						 $totalembl_amount=$totalemb_detail_arr[100]['amount'];
						if($emb_name==1) $em_type = $emblishment_print_type[$emb_type];
						else if($emb_name==2) $em_type = $emblishment_embroy_type[$emb_type];
						else if($emb_name==3) $em_type = $emblishment_wash_type[$emb_type];
						else if($emb_name==4) $em_type = $emblishment_spwork_type[$emb_type];
						else if($emb_name==5) $em_type = $emblishment_gmts_type[$emb_type];
						else $em_type="";

		//getAmountArray_by_jobEmbnameAndEmbtypeColor
						$cons_dzn_gmts=0;$embl_amount=0;
						foreach($job_nos as $jno)
						{
							if($emb_name !=3){
								$wash_qty=$emblishment_job_qty_arr[$jno][$emb_name][$emb_type][$color_id];
								$wash_amt=$emblishment_job_amount_arr[$jno][$emb_name][$emb_type][$color_id];
								if($wash_amt) $wash_amt=$wash_amt;else $wash_amt=0;
								if($wash_qty) $wash_qty=$wash_qty;else $wash_qty=0;
								if(($wash_qty!="" || $wash_amt!=0) && ($wash_qty!="" || $wash_amt!=0))
								{
									$em_amount=$emblishment_job_amount_arr[$jno][$emb_name][$emb_type][$color_id];
									$cons_dzn=$emblishment_job_qty_arr[$jno][$emb_name][$emb_type][$color_id];
									if($em_amount) $em_amount=$em_amount;else $em_amount=0;
									if($cons_dzn) $cons_dzn=$cons_dzn;else $cons_dzn=0;

									$cons_dzn_gmts+=$cons_dzn;
									$embl_amount+=$em_amount;
								}
							}
							else if($emb_name ==3){
								$wash_qty=$$wash_job_type_name_qty_arr[$jno][$emb_name][$emb_type][$color_id];
								$wash_amt=$wash_job_type_name_amount_arr[$jno][$emb_name][$emb_type][$color_id];
								if($wash_amt) $wash_amt=$wash_amt;else $wash_amt=0;
								if($wash_qty) $wash_qty=$wash_qty;else $wash_qty=0;
								if(($wash_qty!="" || $wash_amt!=0) && ($wash_qty!="" || $wash_amt!=0))
								{
									$embl_amt=$wash_job_type_name_amount_arr[$jno][$emb_name][$emb_type][$color_id];
									$cons_dzn=$wash_job_type_name_qty_arr[$jno][$emb_name][$emb_type][$color_id];
									if($embl_amt) $embl_amt=$embl_amt;else $embl_amt=0;
									if($cons_dzn) $cons_dzn=$cons_dzn;else $cons_dzn=0;
									$cons_dzn_gmts+=$wash_job_type_name_qty_arr[$jno][$emb_name][$emb_type][$color_id];
									$embl_amount+=$embl_amt;
								}
							//echo 2;
							}
						}
						//$emb_rowspan=$emb_rowspan_arr[$emb_name];
						//wash_type_name_amount_arr
						//echo $embl_amount.',';
					?>
						<tr bgcolor="<? echo $bgcolor;?>" onClick="change_color('tremb_<? echo $i; ?>','<? echo $bgcolor;?>')" id="tremb_<? echo $i; ?>">
							<?
                            if($emb==0)
							{
							?>
                            <td width="30" rowspan="<? echo $emb_rowspan_arr[$emb_name];?>">
							<? echo $i; ?></td>
                            <td width="120" rowspan="<? echo $emb_rowspan_arr[$emb_name];?>"><div style="word-break:break-all"><? echo $emblishment_name_array[$emb_name];; ?></div></td>
							<td width="100" rowspan="<? echo $emb_rowspan_arr[$emb_name];?>" align="center"><div style="word-break:break-all"><? echo $em_type; ?></div></td>
                            <?
							}
							?>
							<td width="100" align="right" ><div style="word-break:break-all"><? echo number_format($cons_dzn_gmts,4); ?></div>
                            <td width="100" align="center"><div style="word-break:break-all"><? echo $color_library[$color_id]; ?></div></td>

                            <td width="100" align="right"><? echo number_format($embl_amount/$cons_dzn_gmts,4); ?></td>
                            <td width="100"  align="right"><div style="word-break:break-all">
							<? echo number_format($embl_amount,4); ?> </div></td>
                            <? //if($m==1) { ?>
                             <td width="" rowspan="<? //echo $embl_rowspan;?>" valign="middle" align="center" title="Total Embl Amout/Total Fob*100">
							<? echo number_format(($embl_amount/$total_fob_value)*100,4); ?></td>
                            <?
							//}
							?>
                            </tr>
                            <?
								$grand_total_embl_amount+=$embl_amount;
								$grand_total_cons_dzn_gmts+=$cons_dzn_gmts;
								$i++;$emb++;
								}
							}
						  }
							?>
                            <tfoot>
                            <tr>
                                <th colspan="3"><strong>Grand Total</strong> </th>

                                <th align="right"><strong><? echo number_format($grand_total_cons_dzn_gmts,4);?></strong></th>

                                <th align="right">&nbsp;</th>
                                  <th align="right">&nbsp;</th>
                                <th align="right"><? echo number_format($grand_total_embl_amount,4);?></th>
                                <th align="right"><? echo number_format(($grand_total_embl_amount/$total_fob_value)*100,4); ?> </th>
                            </tr>
                            </tfoot>
                    </table>
                    </div>
              <br/><br/>
            <table id="table_header_1" style="margin-left:10px"   class="rpt_table" width="470" cellpadding="0" cellspacing="0" border="1" rules="all">
           	<caption> <b style="float:left">Commercial Cost:</b></caption>
					<thead>
                    	<th width="30">SL</th>
                        <th width="200">Particulars</th>

                        <th width="100">Rate In %</th>
                        <th width="100">Amount($)</th>
                        <th width="">%</th>
                    </thead>
            </table>
                    <div class="scroll_div_inner" style="width:490px; max-height:400px;overflow-y:scroll;margin-left:10px" align="left" id="scroll_body1">
					<table class="rpt_table" width="470" cellpadding="0" cellspacing="0" border="1" rules="all" id="table_body1">
                    <?

					$i=$m=1;$grand_total_comm_amount=0;
					foreach($comm_detail_arr as $item_id=>$val)
					{

						if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
						$job_no=rtrim($val[('job_no')],',');
						$job_nos=array_unique(explode(",",$job_no));

						 $total_comm_amount=$totalcomm_detail_arr[100]['amount'];
						//$comm_amount=$commercial_amount_arr[$val[('job_no')]][$comm_key];
						$comm_amount=0;
						foreach($job_nos as $jno)
						{
							//$comm_amount+=$commercial_item_amount_arr[$jno][$item_id];
						}
						//echo $commercial_cost_percent_amount.'='.$val['rate'].', ';
						$comm_amount=(($val['rate']*$tot_commercial_cost_amount)/100);
					?>
						<tr bgcolor="<? echo $bgcolor;?>" onClick="change_color('trcomm_<? echo $i; ?>','<? echo $bgcolor;?>')" id="trcomm_<? echo $i; ?>">
							<td width="30">
							<? echo $i; ?></td>
                            <td width="200"><div style="word-break:break-all"><? echo $camarcial_items[$item_id];; ?></div></td>


                            <td width="100" align="right"><? echo number_format($val['rate'],4); ?></td>
                            <td width="100"  align="right" title="Commercial Cost Predefined Method"><div style="word-break:break-all">
							<? echo number_format($commamount,4); ?> </div></td>
                            <? //if($m==1) { ?>
                             <td width="" rowspan="<? //echo $comm_rowspan;?>" valign="middle" align="center" title="Commercial Amount=(<? echo $comm_amount; ?>)/Total Fob*100">
							<? echo number_format(($commamount/$total_fob_value)*100,4); ?></td>
                            <?
							//}
							?>
                            </tr>
                            <?
								$grand_total_comm_amount+=$commamount;
								//$grand_total_cons_dzn_gmts+=$cons_dzn_gmts;
								$i++;$m++;
							}
							?>
                            <tfoot>
                            <tr>
                                <th colspan="3"><strong>Grand Total</strong> </th>

                                <th align="right"><? echo number_format($grand_total_comm_amount,4);?></th>
                                <th align="center"><? echo number_format(($grand_total_comm_amount/$total_fob_value)*100,4); ?> </th>
                            </tr>
                            </tfoot>
                    </table>
                    </div>

              <br/><br/>
            <table id="table_header_1" style="margin-left:10px"   class="rpt_table" width="570" cellpadding="0" cellspacing="0" border="1" rules="all">
           	<caption> <b style="float:left">Commission Cost:</b></caption>
					<thead>
                    	<th width="30">SL</th>
                        <th width="200">Particulars</th>
                        <th width="100">Commission Basis</th>
                        <th width="100">Rate</th>
                        <th width="100">Amount($)</th>
                        <th width="">%</th>
                    </thead>
            </table>
                    <div class="scroll_div_inner" style="width:590px; max-height:400px;overflow-y:scroll;margin-left:10px" align="left" id="scroll_body1">
					<table class="rpt_table" width="570" cellpadding="0" cellspacing="0" border="1" rules="all" id="table_body1">
                    <?
					// 	$sql = "select id,job_no,particulars_id,commission_base_id,commision_rate,commission_amount, status_active from  wo_pre_cost_commiss_cost_dtls  where job_no=".$txt_job_no."";

					$i=$m=1;$grand_total_commi_amount=0;
					foreach($commi_detail_arr as $particulars_id=>$val)
					{

						if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
						$job_no=rtrim($val[('job_no')],',');
						$job_nos=array_unique(explode(",",$job_no));
						//$particulars_id=$val[('particulars_id')];
						$commission_base_id=$val[('commission_base_id')];
						 $total_commi_amount=$totalcommi_detail_arr[100]['amount'];
						 $commi_amount=0;
						 foreach($job_nos as $jno)
						 {
							$commi_amount+=$commission_costing_item_arr[$jno][$particulars_id];//$commission_amount_arr[$job_no][$commi_key];
						 }
					?>
						<tr bgcolor="<? echo $bgcolor;?>" onClick="change_color('trcommi_<? echo $i; ?>','<? echo $bgcolor;?>')" id="trcommi_<? echo $i; ?>">
							<td width="30">
							<? echo $i; ?></td>
                            <td width="200"><div style="word-break:break-all"><? echo $commission_particulars[$particulars_id];; ?></div></td>
							<td width="100" align="center"><? echo $commission_base_array[$val['commission_base_id']]; ?></td>
                            <td width="100" align="right"><? echo number_format($val['rate'],4); ?></td>
                            <td width="100"  align="right"><div style="word-break:break-all">
							<? echo number_format($commi_amount,4); ?> </div></td>
                            <? //if($m==1) { ?>
                             <td width="" rowspan="<? //echo $commi_rowspan;?>" valign="middle" align="center" title="Commission Amount/Total Fob*100">
							<? echo number_format(($commi_amount/$total_fob_value)*100,4); ?></td>
                            <?
							//}
							?>
                            </tr>
                            <?
								$grand_total_commi_amount+=$commi_amount;
								$i++;//$m++;
							}
							?>
                            <tfoot>
                            <tr>
                                <th colspan="4"><strong>Grand Total</strong> </th>
                                <th align="right"><? echo number_format($grand_total_commi_amount,4);?></th>
                                <th align="center"><? echo number_format(($grand_total_commi_amount/$total_fob_value)*100,4); ?> </th>
                            </tr>
                            </tfoot>
                    </table>
                    </div>
                      <br/><br/>
                      <?
				  //start	Other Components part report here -------------------------------------------
			?>

        <div style="margin-left:10px">
            <table   class="rpt_table" width="450" cellpadding="0" cellspacing="0" border="1" rules="all" id="table_body1">
            <label><b>Others Components</b></label>
            <thead>
                    <th width="150">Particulars</th>
                    <th width="100">Amount($)</th>
                    <th width="50">%</th>
            </thead>
            <?
          		$style1="#E9F3FF";
				$style2="#FFFFFF";
				 $total_other_components = $total_lab_test_cost+$total_inspection_cost+$total_cm_cost+$total_freight_cost+$total_currier_cost+$total_certificate_cost+$total_common_oh_cost;
   			?>
                <tr bgcolor="<? echo $style1 ?>" onClick="change_color('troh_<? echo 1; ?>','<? echo $bgcolor;?>')" id="troh_<? echo 1; ?>">
                    <td align="left"s>Lab Test </td>
                    <td align="right"><? echo number_format($total_lab_test_cost,4); ?></td>
                    <td align="right" title="Lab Cost/Total FOB*100"><? echo number_format(($total_lab_test_cost/$total_fob_value)*100,4); ?></td>
                </tr>
                <tr bgcolor="<? echo $style2 ?>" onClick="change_color('troh_<? echo 2; ?>','<? echo $bgcolor;?>')" id="troh_<? echo 2; ?>">
                    <td align="left">Inspection Cost</td>
                    <td align="right"><? echo number_format($total_inspection_cost,4); ?></td>
                    <td align="right" title="Inspection Cost/Total FOB*100"><? echo number_format(($total_inspection_cost/$total_fob_value)*100,4); ?></td>
                </tr>
                <tr bgcolor="<? echo $style1 ?>" onClick="change_color('troh_<? echo 3; ?>','<? echo $bgcolor;?>')" id="troh_<? echo 3; ?>">
                    <td align="left">CM Cost - IE</td>
                    <td align="right"><? echo number_format($total_cm_cost,4); ?></td>
                    <td align="right" title="CM Cost/Total FOB*100"><? echo number_format(($total_cm_cost/$total_fob_value)*100,4); ?></td>
                </tr bgcolor="><? echo $style2 ?>">
                <tr  bgcolor="<? echo $style1 ?>" onClick="change_color('troh_<? echo 4; ?>','<? echo $bgcolor;?>')" id="troh_<? echo 4; ?>">
                    <td align="left">Freight Cost</td>
                    <td align="right"><? echo number_format($total_freight_cost,4); ?></td>
                    <td align="right" title="Freight Cost/Total FOB*100"><? echo number_format(($total_freight_cost/$total_fob_value)*100,4); ?></td>
                </tr>
                 <tr bgcolor="<? echo $style2 ?>" onClick="change_color('troh_<? echo 5; ?>','<? echo $bgcolor;?>')" id="troh_<? echo 5; ?>">
                    <td align="left">Currier Cost </td>
                    <td align="right"><? echo number_format($total_currier_cost,4); ?></td>
                    <td align="right" title="Currier Cost/Total FOB*100"><? echo number_format(($total_currier_cost/$total_fob_value)*100,4); ?></td>
                </tr>
                 <tr bgcolor="<? echo $style1; ?>" onClick="change_color('troh_<? echo 6; ?>','<? echo $bgcolor;?>')" id="troh_<? echo 6; ?>">
                    <td align="left">Certificate Cost </td>
                    <td align="right"><? echo number_format($total_certificate_cost,4); ?></td>
                    <td align="right" title="Certificate Cost/Total FOB*100"><? echo number_format(($total_certificate_cost/$total_fob_value)*100,4); ?></td>
                </tr>
                <tr bgcolor="<? echo $style2 ?>" onClick="change_color('troh_<? echo 7; ?>','<? echo $bgcolor;?>')" id="troh_<? echo 7; ?>">
                    <td align="left">Office OH</td>
                    <td align="right"><? echo number_format($total_common_oh_cost,4); ?></td>
                    <td align="right" title="Office OH Cost/Total FOB*100"><? echo number_format(($total_common_oh_cost/$total_fob_value)*100,4); ?></td>
                </tr>

                <tfoot>
                <tr>
                    <th>Total</th>
                    <th align="right"><? echo number_format($total_other_components,4); ?></th>
                    <th align="right" title="Total Other Components Cost/Total FOB*100"><? echo number_format(($total_other_components/$total_fob_value)*100,4); ?> </th>
                </tr>
                </tfoot>
            </table>
            </div>
             <br/><br/>
            <table id="table_header_1" style="margin-left:10px"   class="rpt_table" width="870" cellpadding="0" cellspacing="0" border="1" rules="all">
           	<caption> <b style="float:left">Fabric Dyeing Cost Details:</b></caption>
					<thead>
                    	<th width="100"></th>
                        <th width="250">Particulars</th>
						<th width="100">Process</th>
                        <th width="100">Fab. Color</th>
						<th width="100">Color Qty.</th>
                        <th width="50">UOM</th>
                        <th width="60">Rate</th>
                        <th width="">Total Value</th>
                    </thead>
            </table>
                    <div class="scroll_div_inner" style="width:890px; max-height:400px;overflow-y:scroll;margin-left:10px" align="left" id="scroll_body1">
					<table class="rpt_table" width="870" cellpadding="0" cellspacing="0" border="1" rules="all" id="table_body1">
                    <?
					$pre_cost=sql_select("select job_no,costing_date, costing_per from wo_pre_cost_mst where job_no in(".$all_jobs.")");
					foreach($pre_cost as $row)
					{
						$costing_per_arr[$row[csf('job_no')]]=$row[csf('costing_per')];
					}
					$sql_color="select a.job_no, a.total_set_qnty as ratio, b.id as po_id,c.color_number_id,c.plan_cut_qnty as po_qty from wo_po_details_master a, wo_po_break_down b,wo_po_color_size_breakdown c  where a.job_no=b.job_no_mst and a.job_no=c.job_no_mst  and c.po_break_down_id=b.id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 $company_name_cond $style_owner_cond $job_no_cond $job_style_cond $order_cond $buyer_id_cond $season_cond $file_no_cond  $file_po_idCond order  by b.id";
					$result_color=sql_select($sql_color);
					foreach($result_color as $row)
					{
						$costing_per=$costing_per_arr[$row[csf('job_no')]];
						if($costing_per==1) $order_price_per_dzn=12;
						else if($costing_per==2) $order_price_per_dzn=1;
						else if($costing_per==3) $order_price_per_dzn=24;
						else if($costing_per==4) $order_price_per_dzn=36;
						else if($costing_per==5) $order_price_per_dzn=48;
					//echo $order_price_per_dzn.'ffd';
						$color_po_qty_arr[$row[csf("job_no")]][$row[csf("color_number_id")]]['po_qty']+=$row[csf("po_qty")];
						$color_po_qty_arr[$row[csf("job_no")]][$row[csf("color_number_id")]]['costing_per']=$order_price_per_dzn;
						
						$job_po_qty_arr[$row[csf("job_no")]]['po_qty']+=$row[csf("po_qty")];
						$job_po_qty_arr[$row[csf("job_no")]]['costing_per']=$order_price_per_dzn;
					}

				    $sql_conv="select c.id as id,c.fabric_description as pre_costdtl_id, a.job_no, c.cons_process,c.req_qnty,c.charge_unit,c.amount,c.color_break_down, c.status_active,d.body_part_id,d.fab_nature_id,d.color_type_id,d.fabric_description,d.item_number_id,d.uom from wo_po_details_master a, wo_po_break_down b,wo_pre_cost_fab_conv_cost_dtls c left join wo_pre_cost_fabric_cost_dtls d on c.job_no=d.job_no and c.fabric_description=d.id  where a.job_no=b.job_no_mst and c.job_no=b.job_no_mst and c.job_no=a.job_no  and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and d.is_deleted=0 and d.status_active=1 and b.is_deleted=0  and c.cons_process in(31) $company_name_cond $style_owner_cond $job_no_cond $job_style_cond $order_cond $buyer_id_cond $season_cond $file_no_cond  $file_po_idCond order by c.color_break_down";

					$result_conv=sql_select($sql_conv);
					$conv_detail_arr=array();

					foreach($result_conv as $row)
					{
						$item_descrition = $body_part[$row[csf("body_part_id")]].", ".$color_type[$row[csf("color_type_id")]].", ".$row[csf("fabric_description")]."***".$row[csf("job_no")];
						$color_break_down=explode("__",$row[csf("color_break_down")]);
						$cons_qty=0;
						foreach($color_break_down as $fcolor)
						{
							$color_down=explode("_",$fcolor);

							$gmt_color=$color_down[0];
							$unit_charge=$color_down[1];
							$fab_color=$color_down[3];
							$cons_qty=$color_down[4];
							//echo $cons_qty.'='.'<br>';
							$conv_detail_arr[$item_descrition][$fab_color]['job_no'].=$row[csf("job_no")].',';
							$conv_detail_arr[$item_descrition][$fab_color]['uom']=$row[csf("uom")];
							$conv_detail_arr[$item_descrition][$fab_color]['charge_unit']=$row[csf("charge_unit")];
							$conv_detail_arr[$item_descrition][$fab_color]['amount']=$row[csf("amount")];
							$conv_detail_arr[$item_descrition][$fab_color]['cons_process']=$row[csf("cons_process")];
							$conv_detail_arr[$item_descrition][$fab_color]['desc']=$item_descrition;
							$conv_detail_arr[$item_descrition][$fab_color]['gmt_color']=$gmt_color;
							$conv_detail_arr[$item_descrition][$fab_color]['unit_charge']=$unit_charge;
							$conv_detail_arr[$item_descrition][$fab_color]['cons_qty']=$cons_qty;
						}
					}

					//print_r($totalconv_detail_arr);
					$conv_rowspan_arr=array();
					foreach($conv_detail_arr as $fab_key=>$fab_data)
					{
						$conv_row_span=0;
						foreach($fab_data as $color_key=>$val)
						{
							$conv_row_span++;
						}
						$conv_rowspan_arr[$fab_key]=$conv_row_span;
					}

					//print_r($conv_rowspan_arr);

					$i=$m=1;$grand_total_dyeing_conv_qty=$grand_total_dyeing_conv_amount=$grand_total_amount=$total_dyeing_conv_qty=$total_dyeing_conv_amount=0;
					foreach($conv_detail_arr as $fab_key=>$fab_data)
					{
						$z=1;
						foreach($fab_data as $color_key=>$val)
						{
							if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";

						$job_no=rtrim($val[('job_no')],',');
						$job_nos=array_unique(explode(",",$job_no));

						$cons_qty=$val[('cons_qty')];
						$cons_process=$val[('cons_process')];
						$unit_charge=$val[('unit_charge')];
						$gmt_color=$val[('gmt_color')];

						$color_po_qty=$costing_per=0;
						foreach($job_nos as $jno)
						{

							$costing_per=$color_po_qty_arr[$jno][$gmt_color]['costing_per'];
							if($costing_per!='') $costing_per=$costing_per;else $costing_per=0;
							if($color_po_qty_arr[$jno][$gmt_color]['po_qty']!="" || $color_po_qty_arr[$jno][$gmt_color]['po_qty']!=0) {

							//echo $color_po_qty_arr[$jno][$gmt_color]['po_qty'].', ';
							$color_po_qty+=$color_po_qty_arr[$jno][$gmt_color]['po_qty']/$costing_per;
							}
						}
						$convsion_qty=$cons_qty*$color_po_qty;
						$conversion_amt= $convsion_qty*$unit_charge;

						$totalconv_amount=$totalconv_detail_arr[100]['amount'];
						$process_name=$conversion_cost_head_array[$cons_process];
					?>
						<tr bgcolor="<? echo $bgcolor;?>" onClick="change_color('trconvf_<? echo $i; ?>','<? echo $bgcolor;?>')" id="trconvf_<? echo $i; ?>"> 					 <?
                      	 if($z==1){
                      	 	$job_fab=explode("***", $fab_key);
						?>
							<td width="100" valign="middle" align="center" rowspan="<? echo $conv_rowspan_arr[$fab_key];?>"><? echo $m; ?></td>
                            <td width="250" rowspan="<? echo $conv_rowspan_arr[$fab_key];?>" title="<?=$job_fab[1]?>"><div style="word-break:break-all"><? echo $job_fab[0]; ?></div></td>
							<td width="100" rowspan="<? echo $conv_rowspan_arr[$fab_key];?>" align="right"><div style="word-break:break-all"><? echo $process_name; ?></div></td>
                             <?
							 }
							?>

                            <td width="100" align="right" title="<? echo $cons_qty.'=='.$color_po_qty;?>"><div style="word-break:break-all"><? echo $color_library[$color_key]; ?></div></td>
							<td width="100" align="right" title="<? echo $color_po_qty;?>"><div style="word-break:break-all" ><? echo number_format($convsion_qty,4); ?></div>
                            <td width="50" align="right"><? echo $unit_of_measurement[$val["uom"]]; ?></td></td>
                            <td width="60" align="right"><? echo number_format($unit_charge,4); ?></td>
                            <td width=""  align="right"><div style="word-break:break-all"><? echo number_format($conversion_amt,4); ?></div></td>

                            </tr>
                            <?
								$total_dyeing_conv_qty+=$convsion_qty;
								$total_dyeing_conv_amount+=$conversion_amt;
								$grand_total_dyeing_conv_qty+=$convsion_qty;
								$grand_total_dyeing_conv_amount+=$conversion_amt;

								$z++;
								$i++;
									}
									$m++;
									?>
                             	 <tr  bgcolor="#F4F3C4" class="tbl_bottom">
                                    <td colspan="4" align="right"><strong>Sub Total</strong> </td>
                                    <td align="right"><strong><? echo number_format($total_dyeing_conv_qty,4);$total_dyeing_conv_qty=0;?> </strong></td>
                                    <td align="right"><strong>&nbsp; </strong></td>
                                    <td align="right">&nbsp;</td>
                                    <td align="right"><? echo number_format($total_dyeing_conv_amount,4);$total_dyeing_conv_amount=0;?></td>
                                	</tr>
                                <?
							}
							?>
                            <tfoot>
                            <tr>
                                <th colspan="4"><strong>Grand Total</strong> </th>
                                <th align="right"><strong><? echo number_format($grand_total_dyeing_conv_qty,4);?> </strong></th>
                                <th align="right"><strong>&nbsp; </strong></th>

                                <th align="right">&nbsp;</th>
                                <th align="right"><? echo number_format($grand_total_dyeing_conv_amount,4);?></th>

                            </tr>
                            </tfoot>
                    </table>
                    </div>
                    
                 <br/><br/>
            <table id="table_header_1" style="margin-left:10px"   class="rpt_table" width="870" cellpadding="0" cellspacing="0" border="1" rules="all">
           	<caption> <b style="float:left">Knitting Cost Details:</b></caption>
					<thead>
                    	<th width="100"></th>
                        <th width="250">Particulars</th>
						<th width="100">Process</th>
						<th width="100">Req. Qty.</th>
                        <th width="50">UOM</th>
                        <th width="60">Rate</th>
                        <th width="">Total Value</th>
                    </thead>
            </table>
                    <div class="scroll_div_inner" style="width:890px; max-height:400px;overflow-y:scroll;margin-left:10px" align="left" id="scroll_body1">
					<table class="rpt_table" width="870" cellpadding="0" cellspacing="0" border="1" rules="all" id="table_body1">
                    <?
					/*$pre_cost=sql_select("select job_no,costing_date, costing_per from wo_pre_cost_mst where job_no in(".$all_jobs.")");
					foreach($pre_cost as $row)
					{
						$costing_per_arr[$row[csf('job_no')]]=$row[csf('costing_per')];
					}
					$sql_color="select a.job_no, a.total_set_qnty as ratio, b.id as po_id,c.color_number_id,c.plan_cut_qnty as po_qty from wo_po_details_master a, wo_po_break_down b,wo_po_color_size_breakdown c  where a.job_no=b.job_no_mst and a.job_no=c.job_no_mst  and c.po_break_down_id=b.id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 $company_name_cond $style_owner_cond $job_no_cond $job_style_cond $order_cond $buyer_id_cond $season_cond $file_no_cond  $file_po_idCond order  by b.id";
					$result_color=sql_select($sql_color);
					foreach($result_color as $row)
					{
						$costing_per=$costing_per_arr[$row[csf('job_no')]];
						if($costing_per==1) $order_price_per_dzn=12;
						else if($costing_per==2) $order_price_per_dzn=1;
						else if($costing_per==3) $order_price_per_dzn=24;
						else if($costing_per==4) $order_price_per_dzn=36;
						else if($costing_per==5) $order_price_per_dzn=48;
					//echo $order_price_per_dzn.'ffd';
						$color_po_qty_arr[$row[csf("job_no")]][$row[csf("color_number_id")]]['po_qty']+=$row[csf("po_qty")];
						$color_po_qty_arr[$row[csf("job_no")]][$row[csf("color_number_id")]]['costing_per']=$order_price_per_dzn;
					}*/

				    $sql_convKnit="select c.id as id,c.fabric_description as pre_costdtl_id, a.job_no, c.cons_process,c.req_qnty,c.charge_unit,c.amount,c.color_break_down, c.status_active,d.body_part_id,d.fab_nature_id,d.color_type_id,d.fabric_description,d.item_number_id,d.uom from wo_po_details_master a, wo_po_break_down b,wo_pre_cost_fab_conv_cost_dtls c left join wo_pre_cost_fabric_cost_dtls d on c.job_no=d.job_no and c.fabric_description=d.id  where a.job_no=b.job_no_mst and c.job_no=b.job_no_mst and c.job_no=a.job_no  and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and d.is_deleted=0 and d.status_active=1 and b.is_deleted=0  and c.cons_process in(1) $company_name_cond $style_owner_cond $job_no_cond $job_style_cond $order_cond $buyer_id_cond $season_cond $file_no_cond  $file_po_idCond order by c.color_break_down";

					$result_convKnit=sql_select($sql_convKnit);
					$conv_detail_arr=array();

					foreach($result_convKnit as $row)
					{
						$item_descrition = $body_part[$row[csf("body_part_id")]].", ".$color_type[$row[csf("color_type_id")]].", ".$row[csf("fabric_description")]."***".$row[csf("job_no")];
						//$color_break_down=explode("__",$row[csf("color_break_down")]);
						 
						 
						$process_id=1;
							$knit_conv_detail_arr[$item_descrition][$process_id]['job_no'].=$row[csf("job_no")].',';
							$knit_conv_detail_arr[$item_descrition][$process_id]['uom']=$row[csf("uom")];
							$knit_conv_detail_arr[$item_descrition][$process_id]['charge_unit']=$row[csf("charge_unit")];
							$knit_conv_detail_arr[$item_descrition][$process_id]['amount']=$row[csf("amount")];
							$knit_conv_detail_arr[$item_descrition][$process_id]['cons_process']=$row[csf("cons_process")];
							$knit_conv_detail_arr[$item_descrition][$process_id]['desc']=$item_descrition;
							//$conv_detail_arr[$item_descrition][$process_id]['gmt_color']=$gmt_color;
							//$knit_conv_detail_arr[$item_descrition][$process_id]['unit_charge']=$unit_charge;
							$knit_conv_detail_arr[$item_descrition][$process_id]['unit_charge']=$row[csf("charge_unit")];
							$knit_conv_detail_arr[$item_descrition][$process_id]['cons_qty']=$row[csf("req_qnty")];
					}

					//print_r($totalconv_detail_arr);
					$knitconv_rowspan_arr=array();
					foreach($knit_conv_detail_arr as $fab_key=>$fab_data)
					{
						$knitconv_row_span=0;
						foreach($fab_data as $color_key=>$val)
						{
							$knitconv_row_span++;
						}
						$knitconv_rowspan_arr[$fab_key]=$knitconv_row_span;
					}

					//print_r($conv_rowspan_arr);

					$i=$m=1;$grand_total_dyeing_conv_qty=$grand_total_dyeing_conv_amount=$grand_total_amount=$total_dyeing_conv_qty=$total_dyeing_conv_amount=0;
					foreach($knit_conv_detail_arr as $fab_key=>$fab_data)
					{
						$z=1;
						foreach($fab_data as $process_key=>$val)
						{
							if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";

						$job_no=rtrim($val[('job_no')],',');
						$job_nos=array_unique(explode(",",$job_no));

						$cons_qty=$val[('cons_qty')];
						$cons_process=$val[('cons_process')];
						$unit_charge=$val[('unit_charge')];
						$gmt_color=$val[('gmt_color')];

						$color_po_qty=$costing_per=0;
						foreach($job_nos as $jno)
						{

							$costing_per=$job_po_qty_arr[$jno]['costing_per'];
							if($costing_per!='') $costing_per=$costing_per;else $costing_per=0;
							if($job_po_qty_arr[$jno]['po_qty']!="" || $job_po_qty_arr[$jno]['po_qty']!=0) {

							//echo $color_po_qty_arr[$jno][$gmt_color]['po_qty'].', ';
							$color_po_qty+=$job_po_qty_arr[$jno]['po_qty']/$costing_per;
							}
						}
						$convsion_qty=$cons_qty*$color_po_qty;
						$conversion_amt= $convsion_qty*$unit_charge;
						
						$totalconv_amount=$totalconv_detail_arr[100]['amount'];
						$process_name=$conversion_cost_head_array[$cons_process];
					?>
						<tr bgcolor="<? echo $bgcolor;?>" onClick="change_color('trconvk_<? echo $i; ?>','<? echo $bgcolor;?>')" id="trconvk_<? echo $i; ?>"> 					 <?
                      	 if($z==1){
                      	 	$job_fab=explode("***", $fab_key);
						?>
							<td width="100" valign="middle" align="center" rowspan="<? echo $knitconv_rowspan_arr[$fab_key];?>"><? echo $m; ?></td>
                            <td width="250" rowspan="<? echo $knitconv_rowspan_arr[$fab_key];?>" title="<?=$job_fab[1]?>"><div style="word-break:break-all"><? echo $job_fab[0]; ?></div></td>
							<td width="100" rowspan="<? echo $knitconv_rowspan_arr[$fab_key];?>" align="right"><div style="word-break:break-all"><? echo $process_name; ?></div></td>
                             <?
							 }
							?>

                            
							<td width="100" align="right" title="<? echo $color_po_qty;?>"><div style="word-break:break-all" ><? echo number_format($convsion_qty,4); ?></div>
                            <td width="50" align="right"><? echo $unit_of_measurement[$val["uom"]]; ?></td></td>
                            <td width="60" align="right"><? echo number_format($unit_charge,4); ?></td>
                            <td width=""  align="right"><div style="word-break:break-all"><? echo number_format($conversion_amt,4); ?></div></td>

                            </tr>
                            <?
								$total_dyeing_conv_qty+=$convsion_qty;
								$total_dyeing_conv_amount+=$conversion_amt;
								$grand_total_dyeing_conv_qty+=$convsion_qty;
								$grand_total_dyeing_conv_amount+=$conversion_amt;

								$z++;
								$i++;
									}
									$m++;
									?>
                             	 <tr  bgcolor="#F4F3C4" class="tbl_bottom">
                                    <td colspan="3" align="right"><strong>Sub Total</strong> </td>
                                    <td align="right"><strong><? echo number_format($total_dyeing_conv_qty,4);$total_dyeing_conv_qty=0;?> </strong></td>
                                    <td align="right"><strong>&nbsp; </strong></td>
                                    <td align="right">&nbsp;</td>
                                    <td align="right"><? echo number_format($total_dyeing_conv_amount,4);$total_dyeing_conv_amount=0;?></td>
                                	</tr>
                                <?
							}
							?>
                            <tfoot>
                            <tr>
                                <th colspan="3"><strong>Grand Total</strong> </th>
                                <th align="right"><strong><? echo number_format($grand_total_dyeing_conv_qty,4);?> </strong></th>
                                <th align="right"><strong>&nbsp; </strong></th>

                                <th align="right">&nbsp;</th>
                                <th align="right"><? echo number_format($grand_total_dyeing_conv_amount,4);?></th>

                            </tr>
                            </tfoot>
                    </table>
                    </div>
                     <br/>
             		<?
                		 echo signature_table(109, $cbo_company_name, "850px");
           			 ?>


        </div> <!--Main Div End-->
		<?
	 
	}


  if($action=="budgetsheet")
  {
    ///extract($_REQUEST);
    $process = array( &$_POST );
    extract(check_magic_quote_gpc( $process )); 
    $txt_costing_date=change_date_format(str_replace("'","",$txt_costing_date),'yyyy-mm-dd','-');
    if($txt_job_no=="") $job_no=''; else $job_no=" and a.job_no='".$txt_job_no."'";
    if($cbo_company_name=="") $company_name=''; else $company_name=" and a.company_name=".$cbo_company_name."";
    if($cbo_buyer_name=="") $cbo_buyer_name=''; else $cbo_buyer_name=" and a.buyer_name=".$cbo_buyer_name."";
    if($txt_style_ref=="") $txt_style_ref=''; else $txt_style_ref=" and a.style_ref_no='".$txt_style_ref."'";
    if($txt_costing_date=="") $txt_costing_date=''; else $txt_costing_date=" and c.costing_date='".$txt_costing_date."'";
    $txt_po_breack_down_id=str_replace("'",'',$txt_po_breack_down_id);
    if(str_replace("'",'',$txt_po_breack_down_id)=="") 
    {
      $txt_po_breack_down_id_cond='';  $txt_po_breack_down_id_cond1='';  $txt_po_breack_down_id_cond2='';  $txt_po_breack_down_id_cond3=''; 
    }
    else
    {
      $txt_po_breack_down_id_cond=" and b.id in(".$txt_po_breack_down_id.")";
      $txt_po_breack_down_id_cond1=" and po_id in(".$txt_po_breack_down_id.")";
      $txt_po_breack_down_id_cond2=" and po_break_down_id in(".$txt_po_breack_down_id.")";
      $txt_po_breack_down_id_cond3=" and b.id in(".$txt_po_breack_down_id.")";
    }
    
    //array for display name
    $buyer_arr=return_library_array( "select id, buyer_name from lib_buyer",'id','buyer_name');
    $sesson_arr=return_library_array( "select id, season_name from lib_buyer_season",'id','season_name');
    $brand_arr=return_library_array( "select id, brand_name from lib_buyer_brand",'id','brand_name');
    $comp=return_library_array( "select id, company_name from lib_company",'id','company_name');
    $color_library=return_library_array( "select id, color_name from lib_color where status_active=1 and is_deleted=0", "id", "color_name");
    $fabric_composition_arr=return_library_array( "select id, fabric_composition_name from lib_fabric_composition",'id','fabric_composition_name');
    //$imge_arr=return_library_array( "select master_tble_id,image_location from   common_photo_library where form_name='company_details' and file_type=1",'master_tble_id','image_location');
    
    $photo_data_array = sql_select("SELECT id,master_tble_id,image_location from common_photo_library where master_tble_id='$txt_job_no' and file_type=1  and rownum=1");
    
    if($db_type==0) $group_gsm="group_concat( distinct b.gsm_weight) AS gsm_weight";
    if($db_type==2) $group_gsm="listagg(b.gsm_weight ,',') within group (order by b.gsm_weight) AS gsm_weight";
    
    $gsm_weight_top=return_field_value("$group_gsm", "lib_body_part a,wo_pre_cost_fabric_cost_dtls_h b", "a.id=b.body_part_id and b.job_no='$txt_job_no' and b.status_active=1 and b.is_deleted=0 and a.body_part_type in(1,20) and b.approved_no=$revised_no and b.approval_page=15","gsm_weight");
    //$gsm_weight_bottom=return_field_value("$group_gsm", "lib_body_part a,wo_pre_cost_fabric_cost_dtls b", "a.id=b.body_part_id and b.job_no=$txt_job_no and a.body_part_type=20 ","gsm_weight");
    //echo $gsm_weight_bottom.'DD';
    $gmtsitem_ratio_array=array(); $grmnt_items = "";
    $grmts_sql = sql_select("select job_no, gmts_item_id, set_item_ratio from wo_po_dtls_item_set_his where job_no='$txt_job_no' and approved_no=$revised_no and approval_page=15");
    //echo "select job_no, gmts_item_id, set_item_ratio from wo_po_dtls_item_set_his where job_no='$txt_job_no' and approved_no=$revised_no and approval_page=15";
    foreach($grmts_sql as $key=>$val)
    {
      $grmnt_items .=$garments_item[$val[csf("gmts_item_id")]].",";
      $gmtsitem_ratio_array[$val[csf('job_no')]][$val[csf('gmts_item_id')]]=$val[csf('set_item_ratio')];
      $set_item_ratio += $val[csf('set_item_ratio')]; 
    }
    $grmnt_items = rtrim($grmnt_items,","); 
    
    $sql = "SELECT a.job_id, a.job_no, a.company_name, a.buyer_name, a.ship_mode, a.total_set_qnty, a.style_ref_no, a.gmts_item_id, a.order_uom, a.avg_unit_price, a.product_dept, a.season_buyer_wise, a.brand_id, a.style_description, a.job_quantity as job_qty, sum(b.plan_cut) as job_quantity, sum(b.po_quantity) as ord_qty, listagg(cast(b.sc_lc as varchar2(4000)),',') within group (order by b.sc_lc) as sc_lc, c.costing_per, c.costing_date, c.budget_minute, c.approved, a.quotation_id, c.exchange_rate, c.incoterm, c.sew_effi_percent, c.remarks, c.sew_smv, '' as refusing_cause, d.fab_knit_fin_req_kg, d.fab_knit_req_kg, d.fab_woven_req_yds, d.fab_woven_fin_req_yds, d.fab_yarn_req_kg
      from wo_po_dtls_mst_his a, wo_po_break_down_his b, wo_pre_cost_mst_histry c left join wo_pre_cost_sum_dtls_histroy d on  c.job_no=d.job_no and d.status_active=1 and d.is_deleted=0 and d.approved_no=$revised_no and d.approved_no=$revised_no and d.approval_page=15
      where a.job_no=b.job_no_mst and b.job_no_mst=c.job_no and a.status_active=1 and a.is_deleted =0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and c.approved_no =$revised_no and c.approved_no = $revised_no 
    and a.approved_no=b.approved_no and b.approved_no=c.approved_no and a.approval_page=15
    and a.approval_page=b.approval_page and b.approval_page=c.approval_page
    
    
    $job_no $txt_po_breack_down_id_cond $company_name $cbo_buyer_name 
    group by a.job_id, a.job_no, a.company_name, a.buyer_name, a.style_ref_no, a.gmts_item_id, a.order_uom, a.ship_mode, a.avg_unit_price, a.product_dept, c.incoterm, c.costing_date, c.exchange_rate, a.quotation_id, c.costing_per, c.sew_effi_percent, c.approved, c.budget_minute, d.fab_knit_req_kg, d.fab_woven_req_yds, d.fab_knit_fin_req_kg, d.fab_woven_fin_req_yds, d.fab_yarn_req_kg, a.job_quantity, a.season_buyer_wise, a.brand_id, a.total_set_qnty, a.style_description, c.remarks, c.sew_smv  order by a.job_no"; //a.job_quantity as job_quantity,
    
   //echo $sql;die;
    $data_array=sql_select($sql);
    $plan_cut_qty=$data_array[0][csf('job_quantity')];
    $total_set_qnty=$data_array[0][csf('total_set_qnty')];
    $exchange_rate=$data_array[0][csf('exchange_rate')];
    $sew_effi_percent=$data_array[0][csf('sew_effi_percent')];
    $sew_smv=$preCost_histry_row[csf('sew_smv')];
    
    $is_approved=return_field_value("approved", "wo_pre_cost_mst", "job_no='$txt_job_no' and  status_active=1 and is_deleted=0"); 
    
    $preCost_histry=sql_select( "SELECT b.margin_dzn_percent as MARGIN_DZN_PERCENT, b.fabric_cost_percent as FABRIC_COST_PERCENT, b.trims_cost_percent as TRIMS_COST_PERCENT, b.embel_cost_percent as EMBEL_COST_PERCENT, b.wash_cost_percent as WASH_COST_PERCENT, b.comm_cost_percent as COMM_COST_PERCENT, b.commission_percent as COMMISSION_PERCENT, b.lab_test_percent as LAB_TEST_PERCENT, b.inspection_percent as INSPECTION_PERCENT, b.cm_cost_percent as CM_COST_PERCENT, b.freight_percent as FREIGHT_PERCENT, b.currier_percent as CURRIER_PERCENT, b.certificate_percent as CERTIFICATE_PERCENT, b.common_oh_percent as COMMON_OH_PERCENT from wo_pre_cost_dtls_histry b where b.job_no='$txt_job_no' and b.approved_no=$revised_no"); 
    
    list($preCost_histry_row)=$preCost_histry;
    $opert_profitloss_percent=$preCost_histry_row[csf('margin_dzn_percent')];
    $fabric_cost_percent=$preCost_histry_row[csf('fabric_cost_percent')];
    $trims_cost_percent=$preCost_histry_row[csf('trims_cost_percent')];
    $embel_cost_percent=$preCost_histry_row[csf('embel_cost_percent')];
    $wash_cost_percent=$preCost_histry_row[csf('wash_cost_percent')];
    $comm_cost_percent=$preCost_histry_row[csf('comm_cost_percent')];
    $commission_percent=$preCost_histry_row[csf('commission_percent')];
    $common_oh_percent=$preCost_histry_row[csf('common_oh_percent')];
    
    $lab_test_percent=$preCost_histry_row[csf('lab_test_percent')];
    $inspection_percent=$preCost_histry_row[csf('inspection_percent')];
    $cm_cost_percent=$preCost_histry_row[csf('cm_cost_percent')];
    $freight_percent=$preCost_histry_row[csf('freight_percent')];
    $currier_percent=$preCost_histry_row[csf('currier_percent')];
    $certificate_percent=$preCost_histry_row[csf('certificate_percent')];
    //$currier_percent=$preCost_histry_row[csf('currier_percent')];
    
    $hissew_effi_percent=$preCost_histry_row[csf('sew_effi_percent')];
    //
    $first_app_date=""; $last_app_date="";
    $preCost_approved=sql_select( "select max(b.approved_no) as approved_no, min(b.approved_date) as first_app_date, max(b.approved_date) as last_app_date,a.id from wo_pre_cost_mst a, approval_history b where a.id=b.mst_id and a.job_no='$txt_job_no' and b.entry_form=15 group by a.id"); 
    //echo  "select max(b.approved_no) as approved_no, min(b.approved_date) as first_app_date, max(b.approved_date) as last_app_date,a.id from wo_pre_cost_mst a, approval_history b where   a.id=b.mst_id and a.job_no=$txt_job_no and b.entry_form=15 group by a.id";
    //echo  "select max(b.approved_no) as approved_no, min(b.approved_date) as first_app_date, max(b.approved_date) as last_app_date,a.id from wo_pre_cost_mst a, approval_history b where b.un_approved_by>0 and  a.id=b.mst_id and a.job_no=$txt_job_no and b.entry_form=15 group by a.id";
    if(count($preCost_approved)>0)
    {
      foreach($preCost_approved as $preCost_approved_row)
      {
        $approved_no_row=$preCost_approved_row[csf('approved_no')];
        $fst_date=$preCost_approved_row[csf('first_app_date')];
        $fstapp_date=$fst_date[0];
        
        $last_date=$preCost_approved_row[csf('last_app_date')];
        $lstapp_date=$last_date[0];
        $precost_id=$preCost_approved_row[csf('id')];
      }
    }
    
    $img_path = (str_replace("'", "", $img_path))? str_replace("'", "", $img_path):'../../';
    //echo $img_path;
    $costing_date=$data_array[0][csf('costing_date')];
    if(is_infinite($costing_date) || is_nan($costing_date)){$costing_date=0;}
    
    $approval_allow=sql_select("select b.id, b.page_id, b.approval_need, b.allow_partial, b.validate_page,a.setup_date from approval_setup_mst a, approval_setup_dtls b 
    where a.id=b.mst_id and a.company_id=$cbo_company_name and a.status_active=1 and b.page_id=15 and b.status_active=1 and b.is_deleted=0 order by b.id desc ");
    $appMsg="";
    if( $is_approved==1) 
    {
      $appMsg="This Budget is Approved.";
      $appcolor="color: green;";
    }
    else if( $is_approved==3)
    {
      if($approval_allow[0][csf("approval_need")]==1 && $approval_allow[0][csf("allow_partial")]==1){
        $appMsg="This Budget is Approved.";
        $appcolor="color: green;";
      }
      else{
        $appMsg="This Budget is Partially Approved.";
        $appcolor="color: green;";
      }
    }
    else
    {
      $appMsg="This Budget is Not Approved.";
      $appcolor="color: red;";
    }
    
    ?>
    <div style="width:972px; margin:0 auto; font-family: 'Arial Narrow', Arial, sans-serif;">
          <div style="width:970px; font-size:20px; font-weight:bold">
              <b style="float: left"><?=$comp[str_replace("'","",$cbo_company_name)]; ?><br>Budget Sheet</b>
          <b style="left: 50%; margin-left: 240px; <?=$appcolor; ?>"><?=$appMsg; ?></b>
              <b style="float:right;"><?='Budget Date: ';?><?=date('d-M-y',strtotime($costing_date)); ?> <br><?='Revised No:'.$revised_no; ?>  </b>
          </div>
    
          <div style="width:970px; font-size:18px; font-weight:bold">
              <b style="float: left"></b>
              <b style="float:right; font-size:18px; font-weight:bold">   &nbsp;  </b>
          </div>
          <?
      
      $sqlpo="select a.job_id as JOB_ID, a.approved_no AS APPROVEDNO, a.job_no AS JOB_NO, b.po_id AS POID, b.po_number as PO_NUMBER, b.po_received_date as PO_RECEIVED_DATE, c.item_number_id AS ITEM_NUMBER_ID, c.country_id AS COUNTRY_ID, c.color_number_id AS COLOR_NUMBER_ID, c.size_number_id AS SIZE_NUMBER_ID, c.order_quantity AS ORDER_QUANTITY, c.plan_cut_qnty AS PLAN_CUT_QNTY, c.country_ship_date AS COUNTRY_SHIP_DATE, c.article_number AS ARTICLE_NUMBER, d.costing_per_id AS COSTING_PER from wo_po_dtls_mst_his a, wo_po_break_down_his b, wo_po_color_size_his c, wo_pre_cost_dtls_histry d where a.job_id=b.job_id and b.po_id=c.po_break_down_id and a.job_id=d.job_id and a.status_active=1 and b.is_deleted=0 and b.status_active=1 and c.is_deleted=0 and c.status_active=1 and d.is_deleted=0 and d.status_active=1 and a.approved_no=$revised_no and b.approved_no=$revised_no and c.approved_no=$revised_no and d.approved_no=$revised_no and a.job_no='".$txt_job_no."' order by b.po_received_date DESC";
      //echo $sqlpo; die; //and a.job_no='$job_no'
      $sqlpoRes = sql_select($sqlpo);
      //print_r($sqlpoRes); die;
      $po_arr=array(); $poCountryArr=array(); $reqQtyAmtArr=array(); $costingPerArr=array(); $jobid=""; $jobQtyArr=array();
      foreach($sqlpoRes as $row)
      {
        $costingPerQty=0;
        if($row['COSTING_PER']==1) $costingPerQty=12;
        elseif($row['COSTING_PER']==2) $costingPerQty=1;	
        elseif($row['COSTING_PER']==3) $costingPerQty=24;
        elseif($row['COSTING_PER']==4) $costingPerQty=36;
        elseif($row['COSTING_PER']==5) $costingPerQty=48;
        else $costingPerQty=0;
        
        $costingPerArr[$row['JOB_ID']]=$costingPerQty;
        $jobDataArr[$row['JOB_ID']]['plan']+=$row['PLAN_CUT_QNTY'];
        $jobDataArr[$row['JOB_ID']]['poqty']+=$row['ORDER_QUANTITY'];
        $poArr['pono'][$row['POID']]=$row['PO_NUMBER'];
        $poArr['porecdate'][$row['POID']]=$row['PO_RECEIVED_DATE'];
        $poArr['poshipdate'][$row['POID']]=$row['PO_RECEIVED_DATE'];
        
        
        $po_arr[$row['JOB_ID']][$row['POID']][$row['ITEM_NUMBER_ID']][$row['COLOR_NUMBER_ID']][$row['SIZE_NUMBER_ID']]['poqty']+=$row['ORDER_QUANTITY'];
        $po_arr[$row['JOB_ID']][$row['POID']][$row['ITEM_NUMBER_ID']][$row['COLOR_NUMBER_ID']][$row['SIZE_NUMBER_ID']]['planqty']+=$row['PLAN_CUT_QNTY'];
        
        $po_arr[$row['JOB_ID']][$row['POID']][$row['ITEM_NUMBER_ID']][$row['COLOR_NUMBER_ID']][$row['SIZE_NUMBER_ID']]['county_id'].=$row['COUNTRY_ID'].',';
        
        $poCountryArr[$row['JOB_ID']][$row['POID']][$row['ITEM_NUMBER_ID']][$row['COUNTRY_ID']][$row['COLOR_NUMBER_ID']][$row['SIZE_NUMBER_ID']]['poqty']+=$row['ORDER_QUANTITY'];
        $poCountryArr[$row['JOB_ID']][$row['POID']][$row['ITEM_NUMBER_ID']][$row['COUNTRY_ID']][$row['COLOR_NUMBER_ID']][$row['SIZE_NUMBER_ID']]['planqty']+=$row['PLAN_CUT_QNTY'];
        
        $reqQtyAmtArr[$row['JOB_ID']][$row['POID']]['poqty']+=$row['ORDER_QUANTITY'];
        $reqQtyAmtArr[$row['JOB_ID']][$row['POID']]['planqty']+=$row['PLAN_CUT_QNTY'];
        if($jobid=="") $jobid=$row['JOB_ID']; else $jobid.=','.$row['JOB_ID'];
      }
      unset($sqlpoRes);
      $ujobid=array_unique(explode(",",$jobid));
      $cjobid=count($ujobid);
      $jobIds=implode(",",$ujobid);
      $jobidCond=''; $jobidCondition='';
      if($db_type==2 && $cjobid>1000)
      {
        $jobidCond=" and (";
        $jobidCondition=" and (";
        $jobIdsArr=array_chunk(explode(",",$jobIds),999);
        foreach($jobIdsArr as $ids)
        {
          $ids=implode(",",$ids);
          $jobidCond.=" a.job_id in($ids) or"; 
          $jobidCondition.=" job_id in($ids) or"; 
        }
        $jobidCond=chop($jobidCond,'or ');
        $jobidCond.=")";
        
        $jobidCondition=chop($jobidCondition,'or ');
        $jobidCondition.=")";
      }
      else
      {
        if($jobIds==""){ $jobidCond=""; } else { $jobidCond=" and a.job_id in($jobIds)"; }
        if($jobIds==""){ $jobidCondition=""; } else { $jobidCondition=" and job_id in($jobIds)"; }
      }
      
      $pre_cost_dtls = "SELECT pre_cost_dtls_id as dtls_id, job_id as job_id, job_no, costing_per_id as costing_per, order_uom_id, fabric_cost, fabric_cost_percent, trims_cost, trims_cost_percent, embel_cost, embel_cost_percent, currier_pre_cost, certificate_pre_cost, design_cost, studio_cost, depr_amor_pre_cost, interest_cost, incometax_cost, deffdlc_cost, comm_cost, comm_cost_percent, commission, commission_percent, lab_test, lab_test_percent, inspection, inspection_percent, cm_cost, cm_cost_percent, freight, freight_percent, common_oh, common_oh_percent, total_cost, total_cost_percent, price_dzn, price_dzn_percent, margin_dzn, margin_dzn_percent, price_pcs_or_set, price_pcs_or_set_percent, margin_pcs_set, margin_pcs_set_percent, cm_for_sipment_sche from wo_pre_cost_dtls_histry where job_no='".$txt_job_no."' and status_active=1 and is_deleted=0 and approved_no=$revised_no"; 
      $pre_cost_dtls_arr=sql_select($pre_cost_dtls);
      foreach ($pre_cost_dtls_arr as $row) {
        if($row[csf("costing_per")]==1){$order_price_per_dzn=12;$costing_for="1 DZN";}
        else if($row[csf("costing_per")]==2){$order_price_per_dzn=1;$costing_for="1 PCS";}
        else if($row[csf("costing_per")]==3){$order_price_per_dzn=24;$costing_for="2 DZN";}
        else if($row[csf("costing_per")]==4){$order_price_per_dzn=36;$costing_for="3 DZN";}
        else if($row[csf("costing_per")]==5){$order_price_per_dzn=48;$costing_for="4 DZN";}
        else {$order_price_per_dzn=0; $costing_for="DZN";}
        $job_id=$row[csf("job_id")];
        $planqty=$jobDataArr[$job_id]['plan'];
        $poQty=$jobDataArr[$job_id]['poqty'];
        
        if( ($row[csf("lab_test")]*1)!=0) $labAmt=($row[csf("lab_test")]/$order_price_per_dzn)*$poQty;
        if( ($row[csf("currier_pre_cost")]*1)!=0) $currierAmt=($row[csf("currier_pre_cost")]/$order_price_per_dzn)*$poQty;
        if( ($row[csf("inspection")]*1)!=0) $inspectionAmt=($row[csf("inspection")]/$order_price_per_dzn)*$poQty;
        if( ($row[csf("commission")]*1)!=0) $commissionAmt=($row[csf("commission")]/$order_price_per_dzn)*$poQty;
        if( ($row[csf("comm_cost")]*1)!=0) $commlAmt=($row[csf("comm_cost")]/$order_price_per_dzn)*$poQty;
        if( ($row[csf("freight")]*1)!=0) $freightAmt=($row[csf("freight")]/$order_price_per_dzn)*$poQty;
        if( ($row[csf("certificate_pre_cost")]*1)!=0) $certificateAmt=($row[csf("certificate_pre_cost")]/$order_price_per_dzn)*$poQty;
        if( ($row[csf("deffdlc_cost")]*1)!=0) $deffdlcAmt=($row[csf("deffdlc_cost")]/$order_price_per_dzn)*$poQty;
        if( ($row[csf("design_cost")]*1)!=0) $designAmt=($row[csf("design_cost")]/$order_price_per_dzn)*$poQty;
        if( ($row[csf("studio_cost")]*1)!=0) $studioAmt=($row[csf("studio_cost")]/$order_price_per_dzn)*$poQty;
        if( ($row[csf("depr_amor_pre_cost")]*1)!=0) $deprAmt=($row[csf("depr_amor_pre_cost")]/$order_price_per_dzn)*$poQty;
        if( ($row[csf("common_oh")]*1)!=0) $commonOhAmt=($row[csf("common_oh")]/$order_price_per_dzn)*$poQty;
        if( ($row[csf("interest_cost")]*1)!=0) $interestAmt=($row[csf("interest_cost")]/$order_price_per_dzn)*$poQty;
        if( ($row[csf("incometax_cost")]*1)!=0) $incometaxAmt=($row[csf("incometax_cost")]/$order_price_per_dzn)*$poQty;
        
        if( ($row[csf("cm_cost")]*1)!=0) $cmAmt=($row[csf("cm_cost")]/$order_price_per_dzn)*$poQty;
        
        $other_costing_arr[$job_id]['comm_cost']=$commlAmt;
        $other_costing_arr[$job_id]['commission']=$commissionAmt;
        $other_costing_arr[$job_id]['inspection']=$inspectionAmt;
        $other_costing_arr[$job_id]['freight']=$freightAmt;
        $other_costing_arr[$job_id]['certificate_pre_cost']=$certificateAmt;
        $other_costing_arr[$job_id]['deffdlc_cost']=$deffdlcAmt;
        $other_costing_arr[$job_id]['design_cost']=$designAmt;
        $other_costing_arr[$job_id]['studio_cost']=$studioAmt;
        $other_costing_arr[$job_id]['common_oh']=$commonOhAmt;
        $other_costing_arr[$job_id]['interest_cost']=$interestAmt;
        $other_costing_arr[$job_id]['incometax_cost']=$incometaxAmt;
        $other_costing_arr[$job_id]['depr_amor_pre_cost']=$deprAmt;
        $other_costing_arr[$job_id]['cm_cost']=$cmAmt;
        $other_costing_arr[$job_id]['lab_test']=$labAmt;
        
        
        $total_cost = $row[csf("total_cost")];
        $price_dzn = $row[csf("price_dzn")];
      }
      
      $gmtsitemRatioSql="select approved_no as APPROVENO, job_id AS JOB_ID, gmts_item_id AS GMTS_ITEM_ID, set_item_ratio AS SET_ITEM_RATIO, smv_pcs as SMV_PCS from wo_po_dtls_item_set_his where 1=1 and approved_no=$revised_no $jobCondS $jobidCondition";
      //echo $gmtsitemRatioSql; die;
      $gmtsitemRatioSqlRes = sql_select($gmtsitemRatioSql);
      $jobItemRatioArr=array();
      foreach($gmtsitemRatioSqlRes as $row)
      {
        $jobItemRatioArr[$row['JOB_ID']][$row['GMTS_ITEM_ID']]=$row['SET_ITEM_RATIO'];
        $jobDataArr[$row['JOB_ID']]['smv']=$row['SMV_PCS'];
      }
      unset($gmtsitemRatioSqlRes);
      
      //Contrast Details
      $sqlContrast="select a.approved_no as APPROVENO, a.job_id AS JOB_ID, a.pre_cost_fabric_cost_dtls_id as PRECOSTID, a.gmts_color_id as COLOR_NUMBER_ID, a.contrast_color_id AS CONTRAST_COLOR_ID from wo_pre_fab_concolor_dtls_h a where 1=1 and a.approved_no=$revised_no and a.status_active=1 and a.is_deleted=0 $jobCond $jobidCond";
      //echo $sqlContrast; die;
      $sqlContrastRes = sql_select($sqlContrast);
      $sqlContrastArr=array();
      foreach($sqlContrastRes as $row)
      {
        $sqlContrastArr[$row['JOB_ID']][$row['PRECOSTID']][$row['COLOR_NUMBER_ID']]=$row['CONTRAST_COLOR_ID'];
      }
      unset($sqlContrastRes);
      
      //Stripe Details
      $sqlStripe="select a.approved_no as APPROVENO, a.job_id AS JOB_ID, a.pre_cost_fabric_cost_dtls_id as PRECOSTID, a.po_break_down_id as POID, a.item_number_id AS ITEM_NUMBER_ID, a.color_number_id as COLOR_NUMBER_ID, a.stripe_color as STRIPE_COLOR, a.size_number_id as SIZE_NUMBER_ID, a.fabreq as FABREQ, a.yarn_dyed as YARN_DYED from wo_pre_stripe_color_h a where 1=1 and a.status_active=1 and a.is_deleted=0 and a.approved_no=$revised_no $jobCond $jobidCond";
      //echo $sqlStripe; die;
      $sqlStripeRes = sql_select($sqlStripe);
      $sqlStripeArr=array();
      foreach($sqlStripeRes as $row)
      {
        $sqlStripeArr[$row['JOB_ID']][$row['PRECOSTID']][$row['COLOR_NUMBER_ID']]['strip'][$row['STRIPE_COLOR']]=$row['STRIPE_COLOR'];
        $sqlStripeArr[$row['JOB_ID']][$row['PRECOSTID']][$row['COLOR_NUMBER_ID']]['fabreq'][$row['STRIPE_COLOR']]=$row['FABREQ'];
      }
      unset($sqlStripeRes);
      
      
      //Fabric Details
      $sqlfab="select a.approved_no as APPROVENO, a.job_id AS JOB_ID, a.pre_cost_fabric_cost_dtls_id AS FABID, a.item_number_id AS ITEM_NUMBER_ID, a.fab_nature_id AS FAB_NATURE_ID, a.color_type_id AS COLOR_TYPE_ID, a.fabric_source as FABRIC_SOURCE, a.color_size_sensitive AS COLOR_SIZE_SENSITIVE, a.construction AS CONSTRUCTION, a.fabric_description as FABRIC_DESCRIPTION, a.gsm_weight AS GSM_WEIGHT, a.uom AS UOM, a.budget_on as BUDGET_ON, b.po_break_down_id AS POID, b.color_number_id AS COLOR_NUMBER_ID, b.gmts_sizes AS SIZE_NUMBER_ID, b.cons AS CONS, b.requirment AS REQUIRMENT, b.rate as RATE, b.amount AS AMOUNT
      from wo_pre_cost_fabric_cost_dtls_h a, wo_pre_fab_avg_con_dtls_h b
      where 1=1 and a.pre_cost_fabric_cost_dtls_id=b.pre_cost_fabric_cost_dtls_id and b.cons!=0 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.approved_no=b.approved_no and a.approved_no=$revised_no $jobCond $jobidCond";
      //echo $sqlfab; die;
      $sqlfabRes = sql_select($sqlfab);
      $fabIdWiseGmtsDataArr=array();
      foreach($sqlfabRes as $row)
      {
        $poQty=$planQty=$costingPer=$itemRatio=$finReq=$greyReq=$finAmt=$greyAmt=0;
        
        $fabIdWiseGmtsDataArr[$row['FABID']]['item']=$row['ITEM_NUMBER_ID'];
        $fabIdWiseGmtsDataArr[$row['FABID']]['fnature']=$row['FAB_NATURE_ID'];
        $fabIdWiseGmtsDataArr[$row['FABID']]['sensitive']=$row['COLOR_SIZE_SENSITIVE'];
        $fabIdWiseGmtsDataArr[$row['FABID']]['color_type']=$row['COLOR_TYPE_ID'];
        $fabIdWiseGmtsDataArr[$row['FABID']]['uom']=$row['UOM'];
        $fabIdWiseGmtsDataArr[$row['FABID']]['budget_on']=$row['BUDGET_ON'];
        
        $poQty=$po_arr[$row['JOB_ID']][$row['POID']][$row['ITEM_NUMBER_ID']][$row['COLOR_NUMBER_ID']][$row['SIZE_NUMBER_ID']]['poqty'];
        $planQty=$po_arr[$row['JOB_ID']][$row['POID']][$row['ITEM_NUMBER_ID']][$row['COLOR_NUMBER_ID']][$row['SIZE_NUMBER_ID']]['planqty'];
        $costingPer=$costingPerArr[$row['JOB_ID']];
        $itemRatio=$jobItemRatioArr[$row['JOB_ID']][$row['ITEM_NUMBER_ID']];
        if($row['BUDGET_ON']==1) $poPlanQty=$poQty; else $poPlanQty=$planQty;
        
        $finReq=($poPlanQty/$itemRatio)*($row['CONS']/$costingPer);
        $greyReq=($poPlanQty/$itemRatio)*($row['REQUIRMENT']/$costingPer);
        
        $finAmt=$finReq*$row['RATE'];
        $greyAmt=$greyReq*$row['RATE'];
        
        //echo $planQty.'='.$itemRatio.'='.$row['CONS'].'='.$row['REQUIRMENT'].'='.$costingPer.'='.$finReq.'='.$greyReq.'<br>';
        $fabQtyAmtArr[$row['JOB_ID']]['fabric']=$row['FABRIC_DESCRIPTION'];
        $fabQtyAmtArr[$row['JOB_ID']]['uom']=$row['UOM'];
        
        $fabQtyAmtArr[$row['JOB_ID']]['qty']+=$greyReq;
        $fabQtyAmtArr[$row['JOB_ID']]['amt']+=$greyAmt;
        $fabQtyAmtArr[$row['JOB_ID']]['dzn']=$row['AMOUNT'];
        $fabQtyAmtArr[$row['JOB_ID']]['rate']=$row['RATE'];
        
        if($row['FABRIC_SOURCE']==2)
        {
          $fabQtyAmtArr[$row['JOB_ID']]['purqty']+=$greyReq;
          $fabQtyAmtArr[$row['JOB_ID']]['puramt']+=$greyAmt;	
        }
        else
        {
          $fabQtyAmtArr[$row['JOB_ID']]['prodqty']+=$greyReq;
          $fabQtyAmtArr[$row['JOB_ID']]['prodamt']+=$greyAmt;
        }
        
        if($row['FAB_NATURE_ID']==2)
        {
          $fabric_qty_arr['knit']['finish'][$row['FABID']][$row['UOM']]+=$finReq;
          $fabric_qty_arr['knit']['grey'][$row['FABID']][$row['UOM']]+=$greyReq;
          $fabric_amount_arr['knit']['grey'][$row['FABID']][$row['UOM']]+=$greyAmt;
        }
        if($row['FAB_NATURE_ID']==3)
        {
          $fabric_qty_arr['woven']['finish'][$row['FABID']][$row['UOM']]+=$finReq;
          $fabric_qty_arr['woven']['grey'][$row['FABID']][$row['UOM']]+=$greyReq;
          $fabric_amount_arr['woven']['grey'][$row['FABID']][$row['UOM']]+=$greyAmt;
        }
      }
      unset($sqlfabRes);
      //print_r($fabQtyAmtArr[27617]['puramt']); die; 
      
      //Yarn Details
      $sqlYarn="select a.job_id AS JOB_ID, a.pre_cost_fabric_cost_dtls_id as PRECOSTID, a.po_break_down_id as POID, a.color_number_id as COLOR_NUMBER_ID, a.gmts_sizes as SIZE_NUMBER_ID, a.cons AS CONS, a.requirment AS REQUIRMENT, b.pre_cost_fab_yarn_cost_dtls_id AS YARN_ID, b.count_id AS COUNT_ID, b.copm_one_id AS COPM_ONE_ID, b.percent_one AS PERCENT_ONE, b.type_id AS TYPE_ID, b.color AS COLOR, b.cons_ratio AS CONS_RATIO, b.cons_qnty AS CONS_QNTY, b.avg_cons_qnty AS AVG_CONS_QNTY, b.rate AS RATE, b.amount AS AMOUNT 
      
      from wo_pre_fab_avg_con_dtls_h a, wo_pre_cost_fab_yarn_cst_dtl_h b where 1=1 and a.job_id=b.job_id and a.pre_cost_fabric_cost_dtls_id=b.fabric_cost_dtls_id and a.cons!=0 and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 and a.approved_no=b.approved_no and a.approved_no=$revised_no $jobCond $jobidCond";
      //echo $sqlYarn;
      $sqlYarnRes = sql_select($sqlYarn);
      foreach($sqlYarnRes as $row)
      {
        $poQty=$planQty=$costingPer=$itemRatio=$consQnty=$yarnReq=$yarnAmt=0;
        
        $gmtsItem=$fabIdWiseGmtsDataArr[$row['PRECOSTID']]['item'];
        
        $poQty=$po_arr[$row['JOB_ID']][$row['POID']][$gmtsItem][$row['COLOR_NUMBER_ID']][$row['SIZE_NUMBER_ID']]['poqty'];
        $planQty=$po_arr[$row['JOB_ID']][$row['POID']][$gmtsItem][$row['COLOR_NUMBER_ID']][$row['SIZE_NUMBER_ID']]['planqty'];
        $costingPer=$costingPerArr[$row['JOB_ID']];
        $itemRatio=$jobItemRatioArr[$row['JOB_ID']][$gmtsItem];
        
        $consQnty=($row['REQUIRMENT']*$row['CONS_RATIO'])/100;
        
        $yarnReq=($planQty/$itemRatio)*($consQnty/$costingPer);
        
        $yarnAmt=$yarnReq*$row['RATE'];
        
        //echo $planQty.'='.$itemRatio.'='.$row['REQUIRMENT'].'='.$costingPer.'='.$yarnReq.'='.$yarnAmt.'<br>';
        $yarnQtyAmtArr[$row['JOB_ID']]['yarn_qty']+=$yarnReq;
        $yarnQtyAmtArr[$row['JOB_ID']]['yarn_amt']+=$yarnAmt;
        $yarnDataWithFabricidArr[$row['PRECOSTID']]['amount']+=$yarnAmt;
        $yarnDataWithFabricidArr[$row['PRECOSTID']]['qty']+=$yarnReq;
        
        $yarn_data_array[$row['COUNT_ID']][$row['COPM_ONE_ID']][$row['PERCENT_ONE']][$row['TYPE_ID']][$row['COLOR']][$row['RATE']]['qty']+=$yarnReq;
            $yarn_data_array[$row['COUNT_ID']][$row['COPM_ONE_ID']][$row['PERCENT_ONE']][$row['TYPE_ID']][$row['COLOR']][$row['RATE']]['amount']+=$yarnAmt;
      }
      unset($sqlYarnRes); 
      //print_r($reqQtyAmtArr); die;
      
      //Convaersion Details
      $sqlConv="select a.job_id AS JOB_ID, a.pre_cost_fabric_cost_dtls_id AS PRECOSTID, a.po_break_down_id as POID, a.color_number_id as COLOR_NUMBER_ID, a.gmts_sizes as SIZE_NUMBER_ID, a.dia_width AS DIA_WIDTH, a.cons AS CONS, a.requirment AS REQUIRMENT, b.pre_cost_fab_conv_cst_dtls_id AS CONVERTION_ID, b.cons_process AS CONS_PROCESS, b.req_qnty AS REQ_QNTY, b.process_loss AS PROCESS_LOSS, b.avg_req_qnty AS AVG_REQ_QNTY, b.charge_unit AS CHARGE_UNIT, b.amount as AMOUNT, b.color_break_down AS COLOR_BREAK_DOWN
      from wo_pre_fab_avg_con_dtls_h a, wo_pre_cost_fab_con_cst_dtls_h b where 1=1 and a.pre_cost_fabric_cost_dtls_id=b.fabric_description and a.cons!=0 and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 and a.approved_no=b.approved_no and a.approved_no=$revised_no $jobCond $jobidCond";
      //echo $sqlConv; die;
      $sqlConvRes = sql_select($sqlConv);
      $convConsRateArr=array();
      foreach($sqlConvRes as $row)
      {
        $id=$row['CONVERTION_ID'];
        $colorBreakDown=$row['COLOR_BREAK_DOWN'];
        if($colorBreakDown !="")
        {
          $arr_1=explode("__",$colorBreakDown);
          for($ci=0;$ci<count($arr_1);$ci++)
          {
            $arr_2=explode("_",$arr_1[$ci]);
            $convConsRateArr[$id][$arr_2[0]][$arr_2[3]]['rate']=$arr_2[1];
            $convConsRateArr[$id][$arr_2[0]][$arr_2[3]]['cons']=$arr_2[4];
          }
        }
      }
      //echo "ff"; die;
      foreach($sqlConvRes as $row)
      {
        $poQty=$planQty=$costingPer=$itemRatio=$consQnty=$reqqnty=$convAmt=0;
        $gmtsItem=$fabIdWiseGmtsDataArr[$row['PRECOSTID']]['item'];
        
        $poQty=$po_arr[$row['JOB_ID']][$row['POID']][$gmtsItem][$row['COLOR_NUMBER_ID']][$row['SIZE_NUMBER_ID']]['poqty'];
        $planQty=$po_arr[$row['JOB_ID']][$row['POID']][$gmtsItem][$row['COLOR_NUMBER_ID']][$row['SIZE_NUMBER_ID']]['planqty'];
        
        $costingPer=$costingPerArr[$row['JOB_ID']];
        $itemRatio=$jobItemRatioArr[$row['JOB_ID']][$gmtsItem];
        
        $colorTypeId=$fabIdWiseGmtsDataArr[$row['PRECOSTID']]['color_type']; 
        $colorSizeSensitive=$fabIdWiseGmtsDataArr[$row['PRECOSTID']]['sensitive'];
        $budget_on=$fabIdWiseGmtsDataArr[$row['PRECOSTID']]['budget_on'];
        if($budget_on==1) $poPlanQty=$poQty; else $poPlanQty=$planQty;
        
        $consProcessId=$row['CONS_PROCESS'];
        $stripe_color=$sqlStripeArr[$row['JOB_ID']][$row['PRECOSTID']][$row['COLOR_NUMBER_ID']]['strip'];
        
        if(($colorTypeId==2 || $colorTypeId==3 || $colorTypeId==4 || $colorTypeId==6 || $colorTypeId==31 || $colorTypeId==32 || $colorTypeId==33 || $colorTypeId==34) && $consProcessId==30 && count($stripe_color)>0)
        {
          $qnty=0; $convrate=0;
          foreach($stripe_color as $stripe_color_id)
          {
            $stripe_color_cons_dzn=$convConsRateArr[$row['CONVERTION_ID']][$row['COLOR_NUMBER_ID']][$stripe_color_id]['cons'];
            $convrate=$convConsRateArr[$row['CONVERTION_ID']][$row['COLOR_NUMBER_ID']][$stripe_color_id]['rate'];
            
            $requirment=$stripe_color_cons_dzn-($stripe_color_cons_dzn*$row['PROCESS_LOSS'])/100;
            $qnty=($poPlanQty/$itemRatio)*($requirment/$costingPer);
            //echo $convrate.'=';
            if($convrate>0){
              $reqqnty+=$qnty;
              $convAmt+=$qnty*$convrate;
            }
          }
        }
        else
        {
          $convrate=$requirment=$reqqnty=0;
          $rateColorId=$row['COLOR_NUMBER_ID'];
          if($colorSizeSensitive==3) $rateColorId=$sqlContrastArr[$row['JOB_ID']][$row['PRECOSTID']][$row['COLOR_NUMBER_ID']]; else $rateColorId=$row['COLOR_NUMBER_ID'];
      
          if($row['COLOR_BREAK_DOWN']!="")
          {
            $convDtlsRate=$convConsRateArr[$row['CONVERTION_ID']][$row['COLOR_NUMBER_ID']][$rateColorId]['rate']; 
            if($convDtlsRate>0) $convrate=$convDtlsRate; else $convrate=$row['CHARGE_UNIT']; 
          }else $convrate=$row['CHARGE_UNIT']; 
          
          //echo $row['CHARGE_UNIT'].'='.$row['CONVERTION_ID'].'=';
          if($convrate>0){
            $requirment=$row['REQUIRMENT']-($row['REQUIRMENT']*($row['PROCESS_LOSS']*1))/100;
            $qnty=($poPlanQty/$itemRatio)*($row['REQUIRMENT']/$costingPer);
            $reqqnty+=$qnty;
            $convAmt+=$qnty*$convrate;
          }
        }
        
        //echo $planQty.'='.$itemRatio.'='.$row['REQUIRMENT'].'='.$costingPer.'='.$yarnReq.'='.$yarnAmt.'<br>';
        $convQtyAmtArr[$row['JOB_ID']]['conv_qty'][$consProcessId]+=$reqqnty;
        $convQtyAmtArr[$row['JOB_ID']]['conv_amt'][$consProcessId]+=$convAmt;
        
        $con_amount_fabric_process[$row['PRECOSTID']][$consProcessId]['conv_amt']+=$convAmt;
            $con_qty_fabric_process[$row['PRECOSTID']][$consProcessId]['conv_qty']+=$reqqnty;
      }
      unset($sqlConvRes);
      //echo "kauar"; 
      //print_r($convQtyAmtArr); die;
      
      //Trims Details
      $sqlTrim="select a.job_id AS JOB_ID, a.pre_cost_trim_cost_dtls_id AS TRIMID, a.trim_group AS TRIM_GROUP, a.description AS DESCRIPTION, a.cons_uom AS CONS_UOM, a.cons_dzn_gmts CONS_DZN_GMTS, a.rate AS RATEMST, a.amount AS AMOUNT, b.po_break_down_id as POID, b.item_number_id as ITEM_NUMBER_ID, b.color_number_id as COLOR_NUMBER_ID, b.size_number_id as SIZE_NUMBER_ID, b.cons AS CONS, b.tot_cons AS TOT_CONS, b.rate AS RATE, b.country_id AS COUNTRY_ID_TRIMS, b.color_size_table_id as COLOR_SIZE_ID
      from wo_pre_cost_trim_cost_dtls_his a, wo_pre_cost_trim_co_cons_dtl_h b
      where 1=1 and a.pre_cost_trim_cost_dtls_id=b.wo_pre_cost_trim_cost_dtls_id and b.cons>0 and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 and a.approved_no=b.approved_no and a.approved_no=$revised_no and b.approved_no=$revised_no $jobCond $jobidCond";
      //echo $sqlTrim; die;
      $sqlTrimRes = sql_select($sqlTrim);
      
      foreach($sqlTrimRes as $row)
      {
        $poQty=$planQty=$costingPer=$itemRatio=$consQnty=$consTotQnty=$consAmt=$consTotAmt=0;
        
        $costingPer=$costingPerArr[$row['JOB_ID']];
        $itemRatio=$jobItemRatioArr[$row['JOB_ID']][$row['ITEM_NUMBER_ID']];
        
        $poCountryId=array_filter(array_unique(explode(",",$po_arr[$row['JOB_ID']][$row['POID']][$row['ITEM_NUMBER_ID']][$row['COLOR_NUMBER_ID']][$row['SIZE_NUMBER_ID']]['county_id'])));
        //print_r($poCountryId);
        
        if($row['COUNTRY_ID_TRIMS']=="" || $row['COUNTRY_ID_TRIMS']==0)
        {
          $poQty=$po_arr[$row['JOB_ID']][$row['POID']][$row['ITEM_NUMBER_ID']][$row['COLOR_NUMBER_ID']][$row['SIZE_NUMBER_ID']]['poqty'];
          $planQty=$po_arr[$row['JOB_ID']][$row['POID']][$row['ITEM_NUMBER_ID']][$row['COLOR_NUMBER_ID']][$row['SIZE_NUMBER_ID']]['planqty'];
          
          $consQnty=($poQty/$itemRatio)*($row['CONS']/$costingPer);
          $consTotQnty=($poQty/$itemRatio)*($row['TOT_CONS']/$costingPer);
          
          $consAmt=$consQnty*$row['RATE'];
          $consTotAmt=$consTotQnty*$row['RATE'];
        }
        else
        {
          $countryIdArr=explode(",",$row['COUNTRY_ID_TRIMS']);
          $consQnty=$consTotQnty=$consAmt=$consTotAmt=0;
          foreach($poCountryId as $countryId)
          {
            if(in_array($countryId, $countryIdArr))
            {
              $poQty=$poCountryArr[$row['JOB_ID']][$row['POID']][$row['ITEM_NUMBER_ID']][$countryId][$row['COLOR_NUMBER_ID']][$row['SIZE_NUMBER_ID']]['poqty'];
              $planQty=$poCountryArr[$row['JOB_ID']][$row['POID']][$row['ITEM_NUMBER_ID']][$countryId][$row['COLOR_NUMBER_ID']][$row['SIZE_NUMBER_ID']]['planqty'];
              $consQty=$consTotQty=0;
              
              $consQty=($poQty/$itemRatio)*($row['CONS']/$costingPer);
              $consTotQty=($poQty/$itemRatio)*($row['TOT_CONS']/$costingPer);
              
              $consQnty+=$consQty;
              $consTotQnty+=$consTotQty;
              //echo $poQty.'-'.$itemRatio.'-'.$row['CONS'].'-'.$costingPer.'<br>';
              $consAmt+=$consQty*$row['RATE'];
              $consTotAmt+=$consTotQty*$row['RATE'];
            }
          }
        }
        
        //echo $planQty.'='.$itemRatio.'='.$row['REQUIRMENT'].'='.$costingPer.'='.$yarnReq.'='.$yarnAmt.'<br>';
        //$reqQtyAmtArr[$row['JOB_ID']][$row['POID']]['trimqty']+=$consQnty;
        $trimQtyAmtArr[$row['JOB_ID']]['trimtotqty']+=$consQnty;
        
        //$reqQtyAmtArr[$row['JOB_ID']][$row['POID']]['trimamt']+=$consAmt;
        $trimQtyAmtArr[$row['JOB_ID']]['trimtotamt']+=$consAmt;
        $trim_qty_arr[$row['TRIMID']]+=$consQnty;
        $trim_amount_arr[$row['TRIMID']]+=$consAmt;
      }
      unset($sqlTrimRes); 
      //print_r($reqQtyAmtArr); die;
      
      $sqlEmb="select a.job_id AS JOB_ID, a.pre_cost_embe_cost_dtls_id AS EMB_ID, a.emb_name AS EMB_NAME, a.emb_type AS EMB_TYPE, a.cons_dzn_gmts AS CONS_DZN_GMTS_MST, a.rate AS RATE_MST, a.amount AS AMOUNT_MST, a.budget_on AS BUDGET_ON, b.po_break_down_id as POID, b.item_number_id as ITEM_NUMBER_ID, b.color_number_id as COLOR_NUMBER_ID, b.size_number_id as SIZE_NUMBER_ID, b.requirment AS CONS_DZN_GMTS, b.rate AS RATE, b.amount AS AMOUNT, b.country_id AS COUNTRY_ID_EMB 
    from wo_pre_cost_embe_cost_dtls_his a, wo_pre_emb_avg_con_dtls_h b 
    where 1=1 and a.cons_dzn_gmts>0 and b.requirment>0 and
    a.job_id=b.job_id and a.pre_cost_embe_cost_dtls_id=b.pre_cost_emb_cost_dtls_id and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 and a.approved_no=b.approved_no and a.approved_no=$revised_no and a.approval_page=15 $jobCond $jobidCond";
      //echo $sqlEmb; die;
      $sqlEmbRes = sql_select($sqlEmb);
      
      foreach($sqlEmbRes as $row)
      {
        $poQty=$planQty=$costingPer=$itemRatio=$consQnty=$consTotQnty=$consAmt=$consTotAmt=0;
        
        $costingPer=$costingPerArr[$row['JOB_ID']];
        $itemRatio=$jobItemRatioArr[$row['JOB_ID']][$row['ITEM_NUMBER_ID']];
        $budget_on=$row['BUDGET_ON'];
        
        $poCountryId=array_filter(array_unique(explode(",",$po_arr[$row['JOB_ID']][$row['POID']][$row['ITEM_NUMBER_ID']][$row['COLOR_NUMBER_ID']][$row['SIZE_NUMBER_ID']]['county_id'])));
        //print_r($poCountryId);
        $calPoPlanQty=0;
        
        if($row['COUNTRY_ID_EMB']=="" || $row['COUNTRY_ID_EMB']==0)
        {
          $poQty=$po_arr[$row['JOB_ID']][$row['POID']][$row['ITEM_NUMBER_ID']][$row['COLOR_NUMBER_ID']][$row['SIZE_NUMBER_ID']]['poqty'];
          $planQty=$po_arr[$row['JOB_ID']][$row['POID']][$row['ITEM_NUMBER_ID']][$row['COLOR_NUMBER_ID']][$row['SIZE_NUMBER_ID']]['planqty'];
          
          if($budget_on==1) $calPoPlanQty=$poQty; else $calPoPlanQty=$planQty;
          $consQty=0;
          $consQty=($calPoPlanQty/$itemRatio)*($row['CONS_DZN_GMTS']/$costingPer);
          $consQnty+=$consQty;
          
          $consAmt=$consQty*$row['RATE'];
        }
        else
        {
          $countryIdArr=explode(",",$row['COUNTRY_ID_EMB']);
          $consQnty=$consAmt=0;
          foreach($poCountryId as $countryId)
          {
            if(in_array($countryId, $countryIdArr))
            {
              $poQty=$poCountryArr[$row['JOB_ID']][$row['POID']][$row['ITEM_NUMBER_ID']][$countryId][$row['COLOR_NUMBER_ID']][$row['SIZE_NUMBER_ID']]['poqty'];
              $planQty=$poCountryArr[$row['JOB_ID']][$row['POID']][$row['ITEM_NUMBER_ID']][$countryId][$row['COLOR_NUMBER_ID']][$row['SIZE_NUMBER_ID']]['planqty'];
              
              if($budget_on==1) $calPoPlanQty=$poQty; else $calPoPlanQty=$planQty;
              $consQty=0;
              $consQty=($calPoPlanQty/$itemRatio)*($row['CONS_DZN_GMTS']/$costingPer);
              $consQnty+=$consQty;
              //echo $poQty.'-'.$itemRatio.'-'.$row['CONS_DZN_GMTS'].'-'.$costingPer.'<br>';
              $consAmt+=$consQty*$row['RATE'];
            }
          }
        }
        
        //echo $planQty.'='.$itemRatio.'='.$row['CONS_DZN_GMTS'].'='.$costingPer.'='.$consQty.'='.$consAmt.'<br>';
        $embQtyAmtArr[$row['JOB_ID']][$row['EMB_NAME']]['qty']+=$consQnty;
        $embQtyAmtArr[$row['JOB_ID']][$row['EMB_NAME']]['amt']+=$consAmt;
        /*if($row['EMB_NAME']==1)
        {
          $reqQtyAmtArr[$row['JOB_ID']]['print_qty']+=$consQnty;
          $reqQtyAmtArr[$row['JOB_ID']]['print_amt']+=$consAmt;
        }
        else if($row['EMB_NAME']==2)
        {
          $reqQtyAmtArr[$row['JOB_ID']]['embqty']+=$consQnty;
          $reqQtyAmtArr[$row['JOB_ID']]['embamt']+=$consAmt;
        }
        else if($row['EMB_NAME']==3)
        {
          $reqQtyAmtArr[$row['JOB_ID']]['washqty']+=$consQnty;
          $reqQtyAmtArr[$row['JOB_ID']]['washamt']+=$consAmt;
        }
        else if($row['EMB_NAME']==4)
        {
          $reqQtyAmtArr[$row['JOB_ID']]['special_works_qty']+=$consQnty;
          $reqQtyAmtArr[$row['JOB_ID']]['special_works_amt']+=$consAmt;
        }
        else if($row['EMB_NAME']==5)
        {
          $reqQtyAmtArr[$row['JOB_ID']]['gmts_dyeing_qty']+=$consQnty;
          $reqQtyAmtArr[$row['JOB_ID']]['gmts_dyeing_amt']+=$consAmt;
        }
        else
        {
          //$row['EMB_NAME']==99;
          $reqQtyAmtArr[$row['JOB_ID']]['others_qty']+=$consQnty;
          $reqQtyAmtArr[$row['JOB_ID']]['others_amt']+=$consAmt;
        }*/
      }
      unset($sqlEmbRes); 
      //echo "<pre>";
      //print_r($reqQtyAmtArr); die;
      
      $result =sql_select("select po_id as id, po_number, pub_shipment_date, file_no, excess_cut, grouping, po_received_date, plan_cut from wo_po_break_down_his where job_no_mst='$txt_job_no' $txt_po_breack_down_id_cond1 and status_active=1 and is_deleted=0 and approved_no=$revised_no and approval_page=15 order by po_received_date DESC");
      
      $job_in_orders = ''; $public_ship_date=''; $job_in_ref = ''; $job_in_file = '';
      $tot_excess_cut=0;$tot_row=0;
      foreach ($result as $val)
      {
        $job_in_orders .= $val[csf('po_number')].", ";
        $public_ship_date = $val[csf('pub_shipment_date')];
        $po_received_date = $val[csf('po_received_date')];
        $txt_order_no_arr[$val[csf('id')]] = $val[csf('id')];
        if($val[csf('excess_cut')]>0)
        {
          $tot_row++; 
        }
        $tot_excess_cut+= $val[csf('excess_cut')];
        $plancutqty +=$val[csf('plan_cut')];
      }
      $txt_order_no_id=implode(",", $txt_order_no_arr);
    $total_other_cost = 0;
    foreach ($data_array as $row)
    { 
      $order_price_per_dzn=0;
      $order_job_qnty=0;
      $ord_qty=0;
      $avg_unit_price=0;
      $uom=$row[csf("order_uom")]; 
      $sew_smv=$row[csf("sew_smv")]; 
      $order_values = $row[csf("job_qty")]*$row[csf("avg_unit_price")];   
    
      $job_in_orders = substr(trim($job_in_orders),0,-1);
      if($row[csf("costing_per")]==1){$order_price_per_dzn=12;$costing_for="1 DZN";}
      else if($row[csf("costing_per")]==2){$order_price_per_dzn=1;$costing_for="1 PCS";}
      else if($row[csf("costing_per")]==3){$order_price_per_dzn=24;$costing_for="2 DZN";}
      else if($row[csf("costing_per")]==4){$order_price_per_dzn=36;$costing_for="3 DZN";}
      else if($row[csf("costing_per")]==5){$order_price_per_dzn=48;$costing_for="4 DZN";}
      else {$order_price_per_dzn=0; $costing_for="DZN";}
      $order_job_qnty=$row[csf("job_qty")];
      //$order_qty = $row[csf("job_qty")]*$set_item_ratio;
      $po_no=str_replace("'","",$txt_po_breack_down_id);
      /*$condition= new condition();
      if(str_replace("'","",$txt_job_no) !=''){
          $condition->job_no("='$txt_job_no'");
       }
       
        if(str_replace("'","",$txt_po_breack_down_id)!='')
       {
        $condition->po_id("in($po_no)"); 
       }
      $condition->init();   
      $fabric= new fabric($condition);
      $yarn= new yarn($condition);
      $yarn_costing_arr=$yarn->getJobWiseYarnAmountArray();
      $yarn_qty_amount_arr=$yarn->getJobWiseYarnQtyAndAmountArray();
  
      $yarnDataWithFabricidArr=$yarn->get_By_Precostfabricdtlsid_YarnQtyAmountArray();
  
      $fabric= new fabric($condition);
      $fabricAmoutByFabricSource= $fabric->getAmountArray_by_job_knitAndwoven_greyAndfinish();
      $fabricQtyByFabricSource= $fabric->getQtyArray_by_job_knitAndwoven_greyAndfinish_purchase();
      
      $fabric_qty_arr=$fabric->getQtyArray_by_Fabriccostid_knitAndwoven_greyAndfinish();
      $fabric_amount_arr=$fabric->getAmountArray_by_Fabriccostid_knitAndwoven_greyAndfinish();
      $conversion= new conversion($condition);
      $conversion_costing_arr_process=$conversion->getAmountArray_by_job();
      $conv_qty_job_process= $conversion->getQtyArray_by_jobAndProcess();
      $conv_amount_job_process= $conversion->getAmountArray_by_jobAndProcess();
      $con_qty_fabric_process = $conversion->getQtyArray_by_fabricAndProcess();
      $con_amount_fabric_process = $conversion->getAmountArray_by_fabricAndProcess();
  
      $trims= new trims($condition);
      $trims_costing_arr=$trims->getAmountArray_by_job();
      $trims_qty_arr=$trims->getQtyArray_by_job();
  
      $emblishment= new emblishment($condition);
      $emblishment_costing_arr=$emblishment->getAmountArray_by_job();
      $emb_qty_job_name_arr = $emblishment->getQtyArray_by_jobAndEmbname();
      $emb_amount_job_name_arr = $emblishment->getAmountArray_by_jobAndEmbname();
  
      $wash= new wash($condition);
      $emblishment_costing_arr_wash=$wash->getAmountArray_by_job();
      $wash_qty_job_name_arr =$wash->getQtyArray_by_jobAndEmbname();
      $wash_amount_job_name_arr =$wash->getAmountArray_by_jobAndEmbname();
  
  
      $commercial= new commercial($condition);
      $commercial_costing_arr=$commercial->getAmountArray_by_job();
      $commission= new commision($condition);
      $commission_costing_arr=$commission->getAmountArray_by_job();
      $other= new other($condition);
      $other_costing_arr=$other->getAmountArray_by_job();*/
      /*echo '<pre>';
      print_r($fabric_amount_arr); die;*/
    
      $job_id= $row[csf("job_id")];
      $finishing_arr = array('209','165','33','94','63','171','65','170','156','179','200','208','127','125','84','68','128','190','242','240','192','172','90','218','67','197','73','66','185','142','193');
    
      $total_finishing_amount=0; $total_finishing_qty=0;
    
      $other_cost_attr = array('inspection','freight','certificate_pre_cost','deffdlc_cost','design_cost','studio_cost','common_oh','interest_cost','incometax_cost','depr_amor_pre_cost');
    
      foreach ($other_cost_attr as $attr) {
        $total_other_cost+=$other_costing_arr[$job_id][$attr];
      }
      $misc_cost=$other_costing_arr[$job_id]['lab_test']+$other_costing_arr[$job_id]['comm_cost']+$other_costing_arr[$job_id]['commission']+$total_other_cost;
  
      foreach ($finishing_arr as $fid) {
        $total_finishing_amount +=$convQtyAmtArr[$job_id]['conv_amt'][$fid];
        $total_finishing_qty += $convQtyAmtArr[$job_id]['conv_qty'][$fid];
      //echo $convQtyAmtArr[$job_id]['conv_amt'][$fid].'='.$fid.'<br>';
      }
  
      $total_fabic_cost=0;
      if(count($convQtyAmtArr[$job_id]['conv_qty'][31])>0){
        $total_fabic_cost+=$convQtyAmtArr[$job_id]['conv_amt'][31]/$convQtyAmtArr[$job_id]['conv_qty'][31];
      }
      $total_fabric_amount +=$convQtyAmtArr[$job_id]['conv_amt'][31];
      $total_fabric_per +=$convQtyAmtArr[$job_id]['conv_amt'][31]/$order_values*100;
      if(count($convQtyAmtArr[$job_id]['conv_amt'][30])>0){
        $total_fabic_cost+=$convQtyAmtArr[$job_id]['conv_amt'][30]/$convQtyAmtArr[$job_id]['conv_qty'][30];
      }
      if($yarnQtyAmtArr[$job_id]['yarn_amt']!=''){
        $total_fabic_cost+=$yarnQtyAmtArr[$job_id]['yarn_amt']/$yarnQtyAmtArr[$job_id]['yarn_qty'];
      }
      $total_fabric_amount +=$yarnQtyAmtArr[$job_id]['yarn_amt']; 
      $total_fabric_per +=$yarnQtyAmtArr[$job_id]['yarn_amt']/$order_values*100;
      if($total_finishing_amount!=0){
        $total_fabic_cost+=$total_finishing_amount/$total_finishing_qty;
      } 
      $total_fabric_amount +=$total_finishing_amount;
      $total_fabric_per +=$total_finishing_amount/$order_values*100;
      $total_fabric_amount +=$convQtyAmtArr[$job_id]['conv_amt'][30];
      $total_fabric_per +=$convQtyAmtArr[$job_id]['conv_amt'][30]/$order_values*100;
      if($convQtyAmtArr[$job_id]['conv_amt'][35]>0){
        $total_fabic_cost+=$convQtyAmtArr[$job_id]['conv_amt'][35]/$convQtyAmtArr[$job_id]['conv_qty'][30];
      }
      $total_fabric_amount +=$convQtyAmtArr[$job_id]['conv_amt'][35]; 
      $total_fabric_per +=$convQtyAmtArr[$job_id]['conv_amt'][35]/$order_values*100;
      if($convQtyAmtArr[$job_id]['conv_amt'][1]>0){
        $total_fabic_cost+=$convQtyAmtArr[$job_id]['conv_amt'][1]/$convQtyAmtArr[$job_id]['conv_qty'][1];
      }
      $total_fabric_amount +=$convQtyAmtArr[$job_id]['conv_amt'][1];
      $total_fabric_per +=$convQtyAmtArr[$job_id]['conv_amt'][1]/$order_values*100; 
  
      $purchase_amount = $fabQtyAmtArr[$job_id]['puramt'];
      $purchase_qty = $fabQtyAmtArr[$job_id]['purqty'];
  
      $ather_emb_attr = array(4,5,6,99);
      foreach ($ather_emb_attr as $att) {
        $others_emb_amount += $embQtyAmtArr[$job_id][$att]['amt'];
        $others_emb_qty += $embQtyAmtArr[$job_id][$att]['qty'];
      }
      $knitting_amount_summ=''; $dyeing_amount_summ=''; $yds_amount_summ=''; $aop_amount_summ='';
      if($convQtyAmtArr[$job_id]['conv_amt'][1]>0) {
        $knitting_amount_summ = fn_number_format($convQtyAmtArr[$job_id]['conv_amt'][1],2);
      }
      $yarn_amount_summ = $yarnQtyAmtArr[$job_id]['yarn_amt'];
      $print_amount_summ =$embQtyAmtArr[$job_id][1]['amt'];    
      $emb_amount_summ= $embQtyAmtArr[$job_id][2]['amt'];
      $wash_amount_summ = $embQtyAmtArr[$job_id][3]['amt'];
      if(count($convQtyAmtArr[$job_id]['conv_amt'][31])>0) {
        $dyeing_amount_summ=  $convQtyAmtArr[$job_id]['conv_amt'][31];
      }
      if(count($convQtyAmtArr[$job_id]['conv_amt'][30])>0) {
        $yds_amount_summ =$convQtyAmtArr[$job_id]['conv_amt'][30];
      }
      if(count($convQtyAmtArr[$job_id]['conv_amt'][35])>0) {
        $aop_amount_summ = $convQtyAmtArr[$job_id]['conv_amt'][35];
      }
      
      $total_budget_value = $yarn_amount_summ+$total_finishing_amount+$print_amount_summ+$trimQtyAmtArr[$job_id]['trimtotamt']+$yds_amount_summ+$aop_amount_summ+$emb_amount_summ+$knitting_amount_summ+$purchase_amount+$wash_amount_summ+$other_costing_arr[$job_id]['cm_cost']+$dyeing_amount_summ+$others_emb_amount+$misc_cost;
    //echo $total_budget_value; die;
      ?>
        <table class="rpt_table" border="1" cellpadding="1" cellspacing="1" style="width:970px; font-family: 'Arial Narrow', Arial, sans-serif;" rules="all">
            <tr>
                <th rowspan="7">
                <? foreach($photo_data_array as $inf){ ?>
                <img  src='<?=$img_path; ?><? echo $inf[csf("image_location")]; ?>' height='100px' width='100px' />
                <? } ?>
                </th>
                <th style="background: #D7ECD9">Job No</th>
                  <th><?=$row[csf("job_no")]; ?></th>
                  <th style="background: #D7ECD9">OR. Rcv Date</th>
                  <th><?=date('d-M-y',strtotime($po_received_date)); ?></th>
                  <th style="background: #D7ECD9">Order Quantity</th>
                  <th style="background: yellow; color: #8B0000;">Price/Pcs</th>
                  <th align="right" style="background: yellow; color: #8B0000;">&#36; <?=$row[csf("avg_unit_price")]; ?> </th>
              </tr>
              <tr>                      
                  <th style="background: #D7ECD9">Buyer</th>
                  <th><?=$buyer_arr[$row[csf("buyer_name")]]; ?></th>
                  <th style="background: #D7ECD9">Ship. Date</th>
                  <th><?=date('d-M-y',strtotime($public_ship_date)); ?></th>
                  <th align="center" style="color: #8B0000"><?=$row[csf("job_qty")];?> <?=$unit_of_measurement[$row[csf("order_uom")]]; ?></th>
                  <th style="background: yellow; color: #8B0000;">Order Value</th>                      
                  <th align="right" style="background: yellow; color: #8B0000;">&#36; <?=number_format($order_values,2);  ?></th>
              </tr>
              <tr>
                <th style="background: #D7ECD9">Prod. Dept</th>
                  <th><?=$product_dept[$row[csf("product_dept")]]; ?></th>
                  <th style="background: #D7ECD9">Garments Item</th>
                <th> 
          <?
                  $grmnt_items = "";
                  if($garments_item[$row[csf("gmts_item_id")]]=="")
                  {
            $grmts_sql = sql_select("select job_no, gmts_item_id, set_item_ratio from wo_po_dtls_item_set_his where job_no='$txt_job_no'");
              foreach($grmts_sql as $key=>$val){
                $grmnt_items .=$garments_item[$val[csf("gmts_item_id")]].", ";
                $gmts_item[]=$val[csf("gmts_item_id")];
              }
              $grmnt_items = substr_replace($grmnt_items,"",-1,1);
            }else{
              $gmts_item=explode(',',$row[csf("gmts_item_id")]);
              $grmnt_items = $garments_item[$row[csf("gmts_item_id")]];
                  }
                  echo $grmnt_items;
                  ?>
            </th>
                <th align="center" style="color: #8B0000"><?= $row[csf("job_qty")]*$set_item_ratio.' Pcs' ?></th>
                  <th style="background: yellow; color: #8B0000;"> <? if($zero_value==0) echo "Budget Value"; ?></th>                      
                  <th align="right" style="background: yellow; color: #8B0000;"><? if($zero_value==0){ ?>
                  <? if($total_budget_value>0){ echo '&dollar;'.fn_number_format($total_budget_value,2); } ?><br/>
                  <? if($total_budget_value>0){ echo fn_number_format($total_budget_value/$order_values*100,2).'%'; } ?>
                  <? } ?>
                  </th>
              </tr>
              <tr>
                <th style="background: #D7ECD9">Season / Brand</th>
                  <th><?=$sesson_arr[$row[csf("season_buyer_wise")]].'&nbsp'.$brand_arr[$row[csf("brand_id")]]; ?></th>
                  <th>Costing Per: <br><?= $costing_for;  ?></th>
                  <th style="background: #D7ECD9">Plan Cut Quantity (<?=$tot_excess_cut.'%' ?>) </td>
                <th align="center" style="color: #8B0000"><?= $row[csf("job_quantity")]*$total_set_qnty.' Pcs';//." ". $unit_of_measurement[$row[csf("order_uom")]]; ?></th>
                  <th rowspan="2" style="background: yellow; color: #8B0000;"><? if($zero_value==0) echo "Open Value %"; ?></th>                      
                  <th rowspan="2" align="right" style="background: yellow; color: #8B0000;"><? if($zero_value==0) { ?> &#36;<? 
                    $margin_val = $order_values-$total_budget_value; 
                    echo fn_number_format($margin_val,2).'<br>'.fn_number_format($margin_val/$order_values*100,2).'%';
                    }
                   ?></th>
              </tr>
              <tr>
                <th style="background: #D7ECD9">Style No</th>
                  <th><? $style_no= $row[csf("style_ref_no")]; echo $row[csf("style_ref_no")]; ?></th>
                  <th style="background: #D7ECD9">App. Status</th>
                  <th colspan="2"><?=$appMsg; ?></th>
              </tr>
              <tr>
                <th rowspan="2" style="background: #D7ECD9">Style Description</th>
                  <th rowspan="2" colspan="2"><? echo $row[csf("style_description")]; ?></th>
                  <th style="background: #D7ECD9">Remarks</th>
                  <th colspan="3"><? echo $row[csf("remarks")]; ?></th>
              </tr>
              <tr>
                <th style="background: #D7ECD9">Refusing Cause</th>
                  <th colspan="3"><? echo $row[csf("refusing_cause")]; ?></th>
              </tr>
          </table>
  
              <?        
        $avg_unit_price=$row[csf("avg_unit_price")];
        $ord_qty=$row[csf("ord_qty")];
    }//end first foearch
    /*echo '<pre>';
    print_r($conv_amount_job_process); die;*/
    
    $yarnPer=$yarnQtyAmtArr[$job_id]['yarn_amt']/$yarnQtyAmtArr[$job_id]['yarn_qty'];
    $finishPer=$total_finishing_amount/$total_finishing_qty;
    $ydsPer=$convQtyAmtArr[$job_id]['conv_amt'][30]/$convQtyAmtArr[$job_id]['conv_qty'][30];
    $aopPer=$convQtyAmtArr[$job_id]['conv_amt'][35]/$convQtyAmtArr[$job_id]['conv_qty'][35];
    $knitPer=$convQtyAmtArr[$job_id]['conv_amt'][1]/$convQtyAmtArr[$job_id]['conv_qty'][1];
    $purchPer=$purchase_qty/$purchase_amount;
    $dyePer=$convQtyAmtArr[$job_id]['conv_amt'][31]/$convQtyAmtArr[$job_id]['conv_qty'][31];
    
    $totFabPer=$yarnPer+$finishPer+$ydsPer+$aopPer+$knitPer+$purchPer+$dyePer;
    
    //echo $yarnPer.'='.$finishPer.'='.$ydsPer.'='.$aopPer.'='.$knitPer.'='.$purchPer.'='.$dyePer.'='.$totFabPer;
    //echo $other_costing_arr[$job_id]['cm_cost'].'='.$plancutqty.'='.$set_item_ratio;
    
      ?>
      <br>
      <label  style="float:left;background:#CCCCCC; font-size:larger;"><b>Summary </b> </label> 
      <table class="rpt_table" border="1" cellpadding="1" cellspacing="1" style="width:970px; margin-top: 10px; font-family: 'Arial Narrow', Arial, sans-serif;" rules="all">
      
        <tr style="background: #D7ECD9">
          <th colspan="8" width="320">Fabric </th>
          <th colspan="4" width="160">Embellishment</th>
          <th colspan="4" width="160">Trims + CM + Misc</th>
          <th style="background: yellow">TTL COST &dollar;</th>
        </tr>
        <tr style="background: #D7ECD9">
          <th align="center">Item</th>
          <th align="center">Cost/Uom</th>
          <th align="center">Amount</th>
          <th align="center">&percnt;</th>
          <th align="center">Item</th>
          <th align="center">Cost/Uom</th>
          <th align="center">Amount</th>
          <th align="center">&percnt;</th>
          <th align="center">Item</th>
          <th align="center">Cost/Dz</th>
          <th align="center">Amount</th>
          <th align="center">&percnt;</th>
          <th align="center">Item</th>
          <th align="center">Cost/Dz</th>
          <th align="center">Amount</th>
          <th align="center">&percnt;</th>
          <th rowspan="5" align="right" style="background: yellow; color: #8B0000"><b>
            <? if($total_budget_value>0) { echo fn_number_format($total_budget_value,2,'',''); } ?><br/><br/>
                  <? if($total_budget_value>0){ echo fn_number_format($total_budget_value/$order_values*100,2).'%'; } ?></b>
          </th>
        </tr>
        <tr>
          <th align="center">Yarn</th>
          <td align="center"><? if($yarn_amount_summ>0) { echo fn_number_format($yarnPer,2); } ?></td>
          <td align="right" style="color: #8B0000"><? if($yarn_amount_summ>0) { echo '&dollar;'.fn_number_format($yarn_amount_summ,2); } ?></td>
          <td align="right"><? if($yarn_amount_summ>0) { echo fn_number_format($yarnQtyAmtArr[$job_id]['yarn_amt']/$order_values*100,2).'%';}; ?></td>
  
          <th align="center">Finishing</th>
          <td align="center"><? if($total_finishing_amount>0) { echo fn_number_format($total_finishing_amount/$total_finishing_qty,2); } ?></td>
          <td align="right" style="color: #8B0000"><? if($total_finishing_amount>0) { echo '&dollar;'.fn_number_format($total_finishing_amount,2); } ?></td>
          <td align="right"><? if($total_finishing_amount>0) { echo fn_number_format($total_finishing_amount/$order_values*100,2).'%';} ?></td>
  
          <th align="center">Print</th>
          <td align="center"><? if($print_amount_summ>0) { echo fn_number_format($embQtyAmtArr[$job_id][1]['amt']/$embQtyAmtArr[$job_id][1]['qty'],2);}  ?></td>
          <td align="right" style="color: #8B0000"><? if($print_amount_summ>0) { echo '&dollar;'.fn_number_format($print_amount_summ,2);}  ?></td>
          <td align="right"><? if($print_amount_summ>0) { echo fn_number_format($embQtyAmtArr[$job_id][1]['amt']/$order_values*100,2).'%';} ?></td>
  
          <th align="center">Trim</th>
          <td align="center"><? if($trimQtyAmtArr[$job_id]['trimtotamt']>0) { echo fn_number_format($trimQtyAmtArr[$job_id]['trimtotamt']/$order_job_qnty,2); } ?></td>
          <td align="right" style="color: #8B0000"><? if($trimQtyAmtArr[$job_id]['trimtotamt']>0) { echo '&dollar;'.fn_number_format($trimQtyAmtArr[$job_id]['trimtotamt'],2);} ?></td>
          <td align="right"><? if($trimQtyAmtArr[$job_id]['trimtotamt']>0) { echo fn_number_format($trimQtyAmtArr[$job_id]['trimtotamt']/$order_values*100,2).'%';} ?></td>
        </tr>
        <tr>
          <th align="center">Yds</th>
          <td align="center"><? if($yds_amount_summ>0) { echo fn_number_format($convQtyAmtArr[$job_id]['conv_amt'][30]/$convQtyAmtArr[$job_id]['conv_qty'][30],2); } ?></td>
          <td align="right" style="color: #8B0000"><? if($yds_amount_summ>0) { echo '&dollar;'.fn_number_format($yds_amount_summ,2);} ?></td>
          <td align="right"><? if($yds_amount_summ>0) { echo fn_number_format($convQtyAmtArr[$job_id]['conv_amt'][30]/$order_values*100,2).'%';} ?></td>
  
          <th align="center">AOP</th>
          <td align="center"><? if($aop_amount_summ>0) { echo fn_number_format($convQtyAmtArr[$job_id]['conv_amt'][35]/$convQtyAmtArr[$job_id]['conv_qty'][35],2); } ?></td>
          <td align="right" style="color: #8B0000"><? if($aop_amount_summ>0) { echo '&dollar;'.fn_number_format($aop_amount_summ,2);} ?></td>
          <td align="right"><? if($aop_amount_summ>0) { echo fn_number_format($convQtyAmtArr[$job_id]['conv_amt'][35]/$order_values*100,2).'%';} ?></td>
  
          <th align="center">EMB</th>
          <td align="center"><? if($emb_amount_summ>0) { echo fn_number_format($embQtyAmtArr[$job_id][2]['amt']/$embQtyAmtArr[$job_id][2]['qty'],2);}  ?></td>
          <td align="right" style="color: #8B0000"><? if($emb_amount_summ>0) { echo '&dollar;'.fn_number_format($emb_amount_summ,2);}  ?></td>
          <td align="right"><? if($emb_amount_summ>0) { echo fn_number_format($embQtyAmtArr[$job_id][2]['amt']/$order_values*100,2).'%';} ?></td>
          <th align="center">MISC</th>
          <td align="center"><?  if($misc_cost>0) { echo fn_number_format($misc_cost/$order_job_qnty*12,2); } ?></td>
          <td align="right" style="color: #8B0000"><? if($misc_cost>0) { echo '&dollar;'.fn_number_format($misc_cost,2);}  ?></td>
          <td align="right"><? if($misc_cost>0) { echo fn_number_format($misc_cost/$order_values*100,2).'%';} ?></td>
        </tr>
        <tr>
          <th align="center">Knitting</th>
          <td align="center"><? if($knitting_amount_summ !='') { echo fn_number_format($convQtyAmtArr[$job_id]['conv_amt'][1]/$convQtyAmtArr[$job_id]['conv_qty'][1],2); } ?></td>
          <td align="right" style="color: #8B0000"><? if($knitting_amount_summ !='') { echo  '&dollar;'.$knitting_amount_summ;}   ?></td>
          <td align="right"><? if($knitting_amount_summ !=''){echo fn_number_format($convQtyAmtArr[$job_id]['conv_amt'][1]/$order_values*100,2).'%'; } ?></td>
  
          <th align="center">P. Fabric</th>
          <td align="center"><? $total_fabic_cost+=$purchase_qty/$purchase_amount; if($purchase_qty>0 && $purchase_amount>0){ echo fn_number_format($purchase_qty/$purchase_amount,2);} ?></td>
          <td align="right"><? $total_fabric_amount+=$purchase_amount; if($purchase_amount){echo '&dollar;'.fn_number_format($purchase_amount,2); } ?></td>
          <td align="right"><? $total_fabric_per+=$purchase_amount/$order_values*100; if($purchase_amount>0){ echo fn_number_format($purchase_amount/$order_values*100,2).'%'; }  ?></td>
  
          <th align="center">Wash</th>
          <td align="center"><? if($wash_amount_summ>0) {echo fn_number_format($embQtyAmtArr[$job_id][3]['amt']/$embQtyAmtArr[$job_id][3]['qty'],2); };  ?></td>
          <td align="right" style="color: #8B0000"><? if($wash_amount_summ>0) { echo '&dollar;'.fn_number_format($wash_amount_summ,2);}  ?></td>
          <td align="right"><? if($wash_amount_summ>0) { echo fn_number_format($embQtyAmtArr[$job_id][3]['amt']/$order_values*100,2).'%';} ?></td>
  
          <th align="center" style="color: #8B0000">F.CM</th>
          <td align="center" style="color: #8B0000" title="(CM Cost/Order Qty Pcs)x12"><? if($other_costing_arr[$job_id]['cm_cost']>0){echo fn_number_format(($other_costing_arr[$job_id]['cm_cost']/($plancutqty*$set_item_ratio))*12,2); } ?></td>
          <td align="right" style="color: #8B0000"><? if($other_costing_arr[$job_id]['cm_cost']>0){ echo fn_number_format($other_costing_arr[$job_id]['cm_cost'],2); } ?></td>
          <td align="right"><? if($other_costing_arr[$job_id]['cm_cost']>0){ echo fn_number_format($other_costing_arr[$job_id]['cm_cost']/$order_values*100,2).'%'; } ?></td>
        </tr>
        <tr>
          <th align="center">Dyeing</th>
          <td align="center"><? if($dyeing_amount_summ>0) {echo fn_number_format($convQtyAmtArr[$job_id]['conv_amt'][31]/$convQtyAmtArr[$job_id]['conv_qty'][31],2);} ?></td>
          <td align="right" style="color: #8B0000"><? if($dyeing_amount_summ>0) { echo '&dollar;'.fn_number_format($dyeing_amount_summ,2);} ?></td>
          <td align="right"><? if($dyeing_amount_summ>0) { echo fn_number_format($convQtyAmtArr[$job_id]['conv_amt'][31]/$order_values*100,2).'%';} ?></td>
  
          <th align="center" style="color: #8B0000">TOTAL</th>
          <th align="center" style="color: #8B0000"><? if($total_fabic_cost>0){ echo fn_number_format($totFabPer,2); } ?></th>
          <th align="right" style="color: #8B0000"><? if($total_fabric_amount>0){ echo '&dollar;'.fn_number_format($total_fabric_amount,2); }  ?></th>
          <th align="right" style="color: #8B0000"><? if($total_fabric_per>0){ echo fn_number_format($total_fabric_per,2); }  ?></th>
  
          <th align="center" title="Special works, Garments dyeing, UV print and others.">Others</th>
          <td align="center"><? if($others_emb_amount>0) {echo fn_number_format($others_emb_amount/$others_emb_qty,2); } ?></td>
          <td align="right"><? if($others_emb_amount>0) { echo '&dollar;'.fn_number_format($others_emb_amount,2); }  ?></td>
          <td align="right"><? if($others_emb_amount>0) { echo fn_number_format($others_emb_amount/$order_values*100,2);}  ?></td>
          <th></th>
          <td></td>
          <td></td>
          <td></td>
        </tr>
      </table>    
      <?
    $location_cpm_cost=0;
    $cm_min_variable=return_field_value("yarn_iss_with_serv_app as cost_per_minute","variable_order_tracking","company_name =".$cbo_company_name." and variable_list=67 and is_deleted=0 and status_active=1","cost_per_minute");
    if($cm_min_variable=="" || $cm_min_variable==0) $location_cpm_cost=0; else $location_cpm_cost=$cm_min_variable;
    if($location_cpm_cost!=1)
    {
      $sql_std_para=sql_select("select interest_expense, income_tax, cost_per_minute, applying_period_date, applying_period_to_date from lib_standard_cm_entry where company_id=$cbo_company_name and status_active=1 and is_deleted=0 order by id");
      
      foreach($sql_std_para as $row )
      {
        $applying_period_date=change_date_format($row[csf('applying_period_date')],'','',1);
        $applying_period_to_date=change_date_format($row[csf('applying_period_to_date')],'','',1);
        $diff=datediff('d',$applying_period_date,$applying_period_to_date);
        for($j=0;$j<$diff;$j++)
        {
          //$newdate =change_date_format(add_date(str_replace("'","",$applying_period_date),$j),'','',1);
          $date_all=add_date(str_replace("'","",$applying_period_date),$j);
          $newdate =change_date_format($date_all,'','',1);
          $financial_para[$newdate][interest_expense]=$row[csf('interest_expense')];
          $financial_para[$newdate][income_tax]=$row[csf('income_tax')];
          $financial_para[$newdate][cost_per_minute]=$row[csf('cost_per_minute')];
        }
      }
    }
    else
    {
      $sql_std_para=sql_select( "select a.id, b.id as dtls_id, b.location_id, b.applying_period_date, b.applying_period_to_date, b.monthly_cm_expense, b.no_factory_machine, b.working_hour, b.cost_per_minute from lib_standard_cm_entry a, lib_standard_cm_entry_dtls b where a.id=b.mst_id and b.location_id=$location_name_id and a.company_id=$cbo_company_name" );
      foreach($sql_std_para as $row)
      {
        $applying_period_date=change_date_format($row[csf('applying_period_date')],'','',1);
        $applying_period_to_date=change_date_format($row[csf('applying_period_to_date')],'','',1);
        $diff=datediff('d',$applying_period_date,$applying_period_to_date);
        for($j=0;$j<$diff;$j++)
        {
          $date_all=add_date(str_replace("'","",$applying_period_date),$j);
          $newdate =change_date_format($date_all,'','',1);
          $financial_para[$newdate][interest_expense]=$row[csf('interest_expense')];
          $financial_para[$newdate][income_tax]=$row[csf('income_tax')];
          $financial_para[$newdate][cost_per_minute]=$row[csf('cost_per_minute')];
        }
      }
    }
  
      $pre_costing_date=change_date_format($costing_date,'','',1);
      ?>
      <? if($zero_value==0){ ?>
      <br/>
      <label  style="text-align:left; background:#CCCCCC; font-size:larger;"><b>CM Details </b> </label>
      <div style="width:970px; margin-top: 10px; font-family: 'Arial Narrow', Arial, sans-serif;">
      <table class="rpt_table" border="1" cellpadding="1" cellspacing="1" style="width:720px;float: left;" rules="all">
        <tr>
          <th colspan="13">&nbsp;</th>
        </tr>
        <tr style="background: #D7ECD9">
          <th>Style NO.</th>
          <th>MC</th>
          <th>Prd/Hr</th>
          <th>SMV</th>
          <th>BCM</th>
          <th>F.CM</th>
          <th>TTL Min</th>
          <th align="center">CPM</th>
          <th>RL</th>
          <th>RD</th>
          <th>A Eff%</th>
          <th>Layout No</th>
          <th>Alloc Qty</th>
        </tr>
        <tr align="center">
          <td><?= $style_no  ?></td>
          <td>&nbsp;</td>
          <td>&nbsp;</td>
          <td><?= $sew_smv ?></td>
          <td>&nbsp;</td>
          <td>&nbsp;</td>
          <td>&nbsp;</td>
          <td>&#36; <? echo fn_number_format($financial_para[$pre_costing_date][cost_per_minute],4); ?></td>
          <td>&nbsp;</td>
          <td>&nbsp;</td>
          <td>&nbsp;</td>
          <td>&nbsp;</td>
          <td>&nbsp;</td>
        </tr>
        <tr>
          <th>Grand Total</th>
          <th>&nbsp;</th>
          <th>&nbsp;</th>
          <th><?= $sew_smv ?></th>
          <th>&nbsp;</th>
          <th>&nbsp;</th>
          <th>&nbsp;</th>
          <th>&#36; <? echo fn_number_format($financial_para[$pre_costing_date][cost_per_minute],4); ?></th>
          <th>&nbsp;</th>
          <th>&nbsp;</th>
          <th>&nbsp;</th>
          <th>&nbsp;</th>
          <th>&nbsp;</th>
        </tr>
      </table>
      <table class="rpt_table" border="1" cellpadding="1" cellspacing="1" style="width:248px; margin-left: 2px; float: right;" rules="all">
        <tr>
          <th colspan="3" bgcolor="yellow">Embellishment[DZN]</th>
        </tr>
        <tr>
          <th>Print Qty</th>
          <th>Emb Qty</th>
          <th>Wash Qty</th>
        </tr>
        <tr align="center">
          <td><? if($embQtyAmtArr[$job_id][1]['qty']>0){echo fn_number_format($embQtyAmtArr[$job_id][1]['qty'],2); } else { echo '&nbsp;'; } ?></td>
          <td><? if($embQtyAmtArr[$job_id][2]['qty']>0){echo fn_number_format($embQtyAmtArr[$job_id][1]['qty'],2); } else { echo '&nbsp;'; } ?></td>
          <td><? if($embQtyAmtArr[$job_id][3]['qty']>0){echo fn_number_format($embQtyAmtArr[$job_id][3]['qty'],2); } else { echo '&nbsp;'; } ?></td>
        </tr>
        <tr>
          <th><? if($embQtyAmtArr[$job_id][1]['qty']>0){echo fn_number_format($embQtyAmtArr[$job_id][1]['qty'],2); } else { echo '&nbsp;'; } ?></th>
          <th><? if($embQtyAmtArr[$job_id][2]['qty']>0){echo fn_number_format($embQtyAmtArr[$job_id][2]['qty'],2); } else { echo '&nbsp;'; } ?></th>
          <th><? if($embQtyAmtArr[$job_id][3]['qty']>0){echo fn_number_format($embQtyAmtArr[$job_id][3]['qty'],2); } else { echo '&nbsp;'; } ?></th>
        </tr>
      </table>    
      </div>
      <br>
      <? } ?>
      <?
        $nameArray_fabric_description= sql_select("SELECT (a.pre_cost_fabric_cost_dtls_id) as fabric_cost_dtls_id, a.item_number_id, max(a.lib_yarn_count_deter_id) as determin_id, a.body_part_id, a.uom, a.color_type_id, a.fabric_source, a.construction, a.composition, a.gsm_weight, min(a.width_dia_type) as width_dia_type, b.dia_width,avg(b.cons) as cons, avg(b.process_loss_percent) as process_loss_percent, a.fab_nature_id, avg(b.requirment) as requirment, d.fabric_composition_id FROM wo_pre_cost_fabric_cost_dtls_h a, wo_po_color_size_his c, wo_pre_fab_avg_con_dtls_h b, lib_yarn_count_determina_mst d WHERE a.job_no=b.job_no and a.pre_cost_fabric_cost_dtls_id=b.pre_cost_fabric_cost_dtls_id and c.job_no_mst=a.job_no and  c.color_size_id=b.color_size_table_id and a.lib_yarn_count_deter_id=d.id and c.status_active=1 and c.is_deleted=0  and b.status_active=1 and b.is_deleted=0 and a.status_active=1 and a.is_deleted=0 and a.job_no ='$txt_job_no' and a.approved_no=$revised_no and b.cons>0 group by a.body_part_id, a.uom, a.pre_cost_fabric_cost_dtls_id, a.item_number_id, a.color_type_id, a.fabric_source, a.construction, a.composition, a.gsm_weight, b.dia_width, a.fab_nature_id, d.fabric_composition_id order by fabric_cost_dtls_id, a.body_part_id, b.dia_width");
      
        //a.fabric_source=1 and
        foreach ($nameArray_fabric_description as $row) {
          $fabric_id=$row[csf('fabric_cost_dtls_id')];
          $yarn_amount= $yarnDataWithFabricidArr[$fabric_id]['amount'];
          $yarn_qty= $yarnDataWithFabricidArr[$fabric_id]['qty'];
  
          $yds_amount = array_sum($con_amount_fabric_process[$fabric_id][30]);
          $yds_qty = array_sum($con_qty_fabric_process[$fabric_id][30]);
  
          $knitting_amount = array_sum($con_amount_fabric_process[$fabric_id][1]);
          $knitting_qty = array_sum($con_qty_fabric_process[$fabric_id][1]);
          $dyeing_amount = array_sum($con_amount_fabric_process[$fabric_id][31]);
          $dyeing_qty = array_sum($con_qty_fabric_process[$fabric_id][31]);
          $aop_amount = array_sum($con_amount_fabric_process[$fabric_id][35]);
          $aop_qty = array_sum($con_qty_fabric_process[$fabric_id][35]);
  
          $total_finishing_amount=0;
          $total_finishing_qty=0;
          foreach ($finishing_arr as $fid) {
            $total_finishing_amount += array_sum($con_amount_fabric_process[$fabric_id][$fid]);
            $total_finishing_qty += array_sum($con_qty_fabric_process[$fabric_id][$fid]);
          }
          
          $fabric_data_arr[$row[csf('fabric_cost_dtls_id')]]['body_part_id'] = $row[csf('body_part_id')];
          $fabric_data_arr[$row[csf('fabric_cost_dtls_id')]]['description'] = $row[csf('construction')].', '.$fabric_composition_arr[$row[csf('fabric_composition_id')]];
          if($row[csf('fab_nature_id')]==2)
          {
            $fabric_data_arr[$row[csf('fabric_cost_dtls_id')]]['fqty'] = array_sum($fabric_qty_arr['knit']['finish'][$row[csf('fabric_cost_dtls_id')]]);
            $fabric_data_arr[$row[csf('fabric_cost_dtls_id')]]['gqty'] = array_sum($fabric_qty_arr['knit']['grey'][$row[csf('fabric_cost_dtls_id')]]);
          }
          if($row[csf('fab_nature_id')]==3)
          {
            $fabric_data_arr[$row[csf('fabric_cost_dtls_id')]]['fqty'] = array_sum($fabric_qty_arr['woven']['finish'][$row[csf('fabric_cost_dtls_id')]]);
            $fabric_data_arr[$row[csf('fabric_cost_dtls_id')]]['gqty'] = array_sum($fabric_qty_arr['woven']['grey'][$row[csf('fabric_cost_dtls_id')]]);
          }
          
          $fabric_data_arr[$row[csf('fabric_cost_dtls_id')]]['funit'] = $row[csf('uom')];
          $fabric_data_arr[$row[csf('fabric_cost_dtls_id')]]['cons'] = $row[csf('cons')];
          
          $fabric_data_arr[$row[csf('fabric_cost_dtls_id')]]['process_loss'] = $row[csf('process_loss_percent')];
          $fabric_data_arr[$row[csf('fabric_cost_dtls_id')]]['yarn_amount'] = $yarn_amount;
          $fabric_data_arr[$row[csf('fabric_cost_dtls_id')]]['yarn_per'] = $yarn_amount/$yarn_qty;
  
          $fabric_data_arr[$row[csf('fabric_cost_dtls_id')]]['yds_amount'] = $yds_amount;
          $fabric_data_arr[$row[csf('fabric_cost_dtls_id')]]['yds_per'] = $yds_amount/$yds_qty;
          $fabric_data_arr[$row[csf('fabric_cost_dtls_id')]]['knitting_amount'] = $knitting_amount;
          $fabric_data_arr[$row[csf('fabric_cost_dtls_id')]]['knitting_per'] = $knitting_amount/$knitting_qty;
          $fabric_data_arr[$row[csf('fabric_cost_dtls_id')]]['dyeing_amount'] = $dyeing_amount;
          $fabric_data_arr[$row[csf('fabric_cost_dtls_id')]]['dyeing_per'] = $dyeing_amount/$dyeing_qty;
          $fabric_data_arr[$row[csf('fabric_cost_dtls_id')]]['aop_amount'] = $aop_amount;
          $fabric_data_arr[$row[csf('fabric_cost_dtls_id')]]['aop_per'] = $aop_amount/$aop_qty;
          $fabric_data_arr[$row[csf('fabric_cost_dtls_id')]]['finishing_amount'] = $total_finishing_amount;
          $fabric_data_arr[$row[csf('fabric_cost_dtls_id')]]['finishing_per'] = $total_finishing_amount/$total_finishing_qty;
          if($row[csf('fabric_source')]==1)
          {
            $fabric_data_arr[$row[csf('fabric_cost_dtls_id')]]['ttl_cost'] = $yarn_amount+$yds_amount+$knitting_amount+$dyeing_amount+$aop_amount+$total_finishing_amount;
          }
          if($row[csf('fabric_source')]==2)
          {
            if($row[csf('fab_nature_id')]==2)
            {
              $fabric_data_arr[$row[csf('fabric_cost_dtls_id')]]['ttl_cost']=array_sum($fabric_amount_arr['knit']['grey'][$row[csf('fabric_cost_dtls_id')]]);
            }
            if($row[csf('fab_nature_id')]==3)
            {
              $fabric_data_arr[$row[csf('fabric_cost_dtls_id')]]['ttl_cost']=array_sum($fabric_amount_arr['woven']['grey'][$row[csf('fabric_cost_dtls_id')]]);
            }
          }
        }
      //echo "kkkk1";
        if($zero_value==0){ ?>
        <br>
        <table class="rpt_table" border="1" cellpadding="1" cellspacing="1" style="width:970px; margin-top: 10px; font-family: 'Arial Narrow', Arial, sans-serif;" rules="all">
        <label  style="float:left;background:#CCCCCC; font-size:larger;"><b>Fabric Details </b> </label>  
          <tr style="background: #D7ECD9">
            <th rowspan="2">Garments Part Name</th>
            <th rowspan="2">Fabric Details</th>
            <th rowspan="2">Con</th>
            <th>F. QTY</th>
            <th rowspan="2">Process Loss</th>
            <th>G. QTY</th>
            <th colspan="7">Cost/Uom (Fabric)</th>
            <th rowspan="2">Cost/Dz</th>
            <th rowspan="2" style="background: yellow;">TTL Cost $</th>
          </tr>
          <tr style="background: #D7ECD9">
            <th>Unit</th>
            <th>Unit</th>
            <th>Yarn</th>
            <th>Yds</th>
            <th>Knitting</th>
            <th>Dyeing</th>
            <th>AOP</th>
            <th>Finishing</th>
            <th>Cost/Uom</th>
          </tr>
          <?
            foreach ($fabric_data_arr as $value) {?>
              <tr>
                <td rowspan="2"><?= $body_part[$value['body_part_id']] ?></td>
                <td rowspan="2"><?= $value['description'] ?></td>
                <td rowspan="2" align="center"><?= fn_number_format($value['cons'],2); ?></td>
                <td align="center"><? $total_fqty+=$value['fqty']; echo fn_number_format($value['fqty'],2); ?></td>
                <td rowspan="2" align="center"><? if($value['process_loss']>0){ echo fn_number_format($value['process_loss'],2);} ?></td>
                <td align="center"><? $total_gqty+=$value['gqty']; echo fn_number_format($value['gqty'],2) ?></td>
                <td rowspan="2" align="right"><? $total_yarn_amount += $value['yarn_amount']; if($value['yarn_amount']>0){echo fn_number_format($value['yarn_per'],2); }?><br><? if($value['yarn_amount']>0){ echo fn_number_format($value['yarn_amount'],2);} ?></td>
                <td rowspan="2" align="right"><? $total_yds_amount += $value['yds_amount']; if($value['yds_per']>0){ echo fn_number_format($value['yds_per'],2);}?><br><? if($value['yds_amount']>0){ echo fn_number_format($value['yds_amount'],2);} ?></td>
                <td rowspan="2" align="right"><? $total_knitting_amount += $value['knitting_amount']; if($value['knitting_per']>0){ echo fn_number_format($value['knitting_per'],2);}?><br><? if($value['knitting_amount']>0){ echo fn_number_format($value['knitting_amount'],2);} ?></td>
                <td rowspan="2" align="right"><? $total_dyeing_amount += $value['dyeing_amount']; if($value['dyeing_per']>0){ echo fn_number_format($value['dyeing_per'],2);} ?><br><? if($value['dyeing_amount']>0){echo fn_number_format($value['dyeing_amount'],2);} ?></td>
                <td rowspan="2" align="right"><? $total_aop_amount += $value['aop_amount']; if($value['aop_per']>0){ echo fn_number_format($value['aop_per'],2);} ?><br><? if($value['aop_amount']>0){ echo fn_number_format($value['aop_amount'],2);} ?></td>
                <td rowspan="2" align="right"><? $total_finishing_amount += $value['finishing_amount']; if($value['finishing_per']>0){ echo fn_number_format($value['finishing_per'],2);}?><br><? if($value['finishing_amount']>0){fn_number_format($value['finishing_amount'],2);} ?></td>
                <td rowspan="2" align="right" title="TTL Cost/Finish Quantity"><?= fn_number_format($value['ttl_cost']/$value['fqty'],2) ?></td>
                <td rowspan="2" align="right"><?= fn_number_format($value['ttl_cost']/$order_job_qnty*12,2) ?></td>
                <th rowspan="2" style="background: yellow;" align="right"><? $total_ttl_cost += $value['ttl_cost'];  echo fn_number_format($value['ttl_cost'],2) ?></th>
              </tr>
              <tr>
                <td align="center"><?= $unit_of_measurement[$value['funit']] ?></td>
                <td align="center"><?= $unit_of_measurement[$value['funit']] ?></td>              
              </tr>
            <? }
          ?>
          <tr>
            <th colspan="2">Fabric  Total</th>
            <td></td>
            <th align="center"><? if($total_fqty>0){echo fn_number_format($total_fqty,2);} ?></th>
            <td></td>
            <th align="right"><? if($total_gqty){ echo fn_number_format($total_gqty,2); } ?></th>
            <th align="right"><? if($total_yarn_amount){ echo fn_number_format($total_yarn_amount,2); } ?></th>
            <th align="right"><? if($total_yds_amount){ echo fn_number_format($total_yds_amount,2); } ?></th>
            <th align="right"><? if($total_knitting_amount){ echo fn_number_format($total_knitting_amount,2); } ?></th>
            <th align="right"><? if($total_dyeing_amount){ echo fn_number_format($total_dyeing_amount,2); } ?></th>
            <th align="right"><? if($total_aop_amount){ echo fn_number_format($total_aop_amount,2); } ?></th>
            <th align="right"><? if($total_finishing_amount){ echo fn_number_format($total_finishing_amount,2); } ?></th>
            <th></th>
            <th></th>
            <th style="background: yellow;" align="right"><? if($total_ttl_cost){ echo '&dollar;'.fn_number_format($total_ttl_cost,2); } ?> <br><? if($total_ttl_cost){ echo fn_number_format($total_ttl_cost/$order_values*100,2).'%'; } ?></th>
          </tr>
        </table>
        <? } ?>
        <?
        //end   All Fabric Cost part report-------------------------------------------
        $lib_yarn_count=return_library_array( "select yarn_count,id from lib_yarn_count", "id", "yarn_count"  );
        $sql = "select min(pre_cost_fab_yarn_cost_dtls_id) as id, count_id, copm_one_id, percent_one, copm_two_id, percent_two, color,type_id, min(cons_ratio) as cons_ratio, sum(cons_qnty) as cons_qnty, rate, sum(amount) as amount from wo_pre_cost_fab_yarn_cst_dtl_h where job_no='".$txt_job_no."' and approved_no=$revised_no and status_active=1 and is_deleted=0 group by count_id, copm_one_id, percent_one, copm_two_id, percent_two, color,type_id, rate";
        //echo $sql;
        $data_array=sql_select($sql); 
        //$yarn_data_array=$yarn->getCountCompositionPercentTypeColorAndRateWiseYarnQtyAndAmountArray();
        //print_r($yarn_data_array);
      ?>
      <br>
      <div style="margin-top:15px; font-family: 'Arial Narrow', Arial, sans-serif;">
          <table class="rpt_table" border="1" cellpadding="1" cellspacing="1" style="width:970px;text-align:center;" rules="all">
              <label style="float:left;background:#CCCCCC; font-size:larger;"><b>Yarn Details </b> </label>  
              <tr style="font-weight:bold;">
                  <td width="540" style="background: #D7ECD9">Yarn Description</td>
                  <td width="80" style="background: #D7ECD9">Yarn Qty/<?=$costing_for; ?></td> 
                  <td width="80" style="background: #D7ECD9">TTL Yarn Qty</td>                 
                  <td width="80" style="background: #D7ECD9">Rate &#36;</td>
                  <td width="80" style="background: yellow">Amount &#36;</td>
                  <td width="80" style="background: #D7ECD9">% to Ord. Value</td>
              </tr>
              <?
              $total_yarn_qty = 0; $total_yarn_amount = 0; $total_yarn_cost_dzn=$total_yarn_qty_dzn=0; $total_yarn_cost_kg=0; $total_yarn_avg_cons_qty=0;
              foreach( $data_array as $row )
              { 
          if($row[csf("percent_one")]==100)
            $item_descrition = $lib_yarn_count[$row[csf("count_id")]]." ".$composition[$row[csf("copm_one_id")]]." ".$row[csf("percent_one")]."% ".$color_library[$row[csf("color")]]." ".$yarn_type[$row[csf("type_id")]];
          else
            $item_descrition = $lib_yarn_count[$row[csf("count_id")]]." ".$composition[$row[csf("copm_one_id")]]." ".$row[csf("percent_one")]."% ".$composition[$row[csf("copm_two_id")]]." ".$row[csf("percent_two")]."% ".$color_library[$row[csf("color")]]." ".$yarn_type[$row[csf("type_id")]];
          $rowcons_qnty = $yarn_data_array[$row[csf("count_id")]][$row[csf("copm_one_id")]][$row[csf("percent_one")]][$row[csf("type_id")]][$row[csf("color")]][$row[csf("rate")]]['qty'];
          $rowavgcons_qnty = $yarn_data_array[$row[csf("count_id")]][$row[csf("copm_one_id")]][$row[csf("percent_one")]][$row[csf("type_id")]][$row[csf("color")]][$row[csf("rate")]]['qty'];
          $rowamount = $yarn_data_array[$row[csf("count_id")]][$row[csf("copm_one_id")]][$row[csf("percent_one")]][$row[csf("type_id")]][$row[csf("color")]][$row[csf("rate")]]['amount'];
          if(is_infinite($rowamount) || is_nan($rowamount)){$rowamount=0;}
          ?>   
          <tr>
                      <td align="left"><? echo $item_descrition; ?></td>
                      <td align="right"><? echo fn_number_format($row[csf("cons_qnty")],3); ?></td>
                      <td align="right"><? echo fn_number_format($rowcons_qnty,2); ?></td>
                      <td align="right"><? if($row[csf("rate")]>0){ echo fn_number_format($row[csf("rate")],3);} ?></td>
                      <td align="right" style="background: yellow"><? if($rowamount>0){ echo fn_number_format($rowamount,2);} ?></td>
                      <td align="right"><? 
                      $cv=($row[csf("amount")]/$price_dzn)*100;
                      if(is_infinite($cv) || is_nan($cv)){$cv=0;}
                      if($cv>0){echo fn_number_format($cv,2); }
                      ?></td>
          </tr>
          <?  
          $total_yarn_qty+=$rowcons_qnty;
          $total_yarn_qty_dzn+=$row[csf("cons_qnty")];
          $total_avg_yarn_qty+=$rowavgcons_qnty;
          $total_yarn_amount +=$rowamount;
          $total_yarn_cost_dzn+=$row[csf("amount")];
          $total_yarn_avg_cons_qty+=$rowavgcons_qnty;
          $total_yarn_cost_kg=$total_yarn_amount/$total_yarn_qty;
          if(is_infinite($total_yarn_cost_kg) || is_nan($total_yarn_cost_kg)){$total_yarn_cost_kg=0;}
              }
              ?>
              <tr class="rpt_bottom" style="font-weight:bold">
                  <td>Yarn Total</td>
                  <td align="right"><? if($total_yarn_qty_dzn>0){ echo fn_number_format($total_yarn_qty_dzn,4); } ?></td>
                  <td align="right"><? if($total_yarn_qty>0){ echo fn_number_format($total_yarn_qty,2); } ?></td>                    
                  <td></td>
                  <td align="right" bgcolor="yellow"><? if($total_yarn_amount>0){ echo '&dollar;'.fn_number_format($total_yarn_amount,2); } ?></td>
                  <td align="right"><? 
                  $cv=($total_yarn_cost_dzn/$price_dzn)*100;
                  if(is_infinite($cv) || is_nan($cv)){$cv=0;}
                  if($cv>0){ echo fn_number_format($cv,2).' %';  }
                  ?></td>
              </tr>
          </table>
      </div>
      <?
      //End Yarn Cost part report here -------------------------------------------
  
    //start Trims Cost part report here -------------------------------------------
    $supplier_library_fabric=return_library_array( "select a.id, a.supplier_name from lib_supplier a where a.is_deleted=0  and a.status_active=1 group by a.id,a.supplier_name order by a.supplier_name", "id", "supplier_name");
    
      $sql = "select pre_cost_trim_cost_dtls_id as id, job_no, trim_group,description,brand_sup_ref, cons_uom, cons_dzn_gmts, rate, amount, apvl_req, nominated_supp_multi, status_active from wo_pre_cost_trim_cost_dtls_his  where job_no='".$txt_job_no."' and approved_no=$revised_no and status_active=1 and is_deleted=0";
      $data_array=sql_select($sql);
    ?>
      <div style="margin-top:15px">
          <table class="rpt_table" border="1" cellpadding="1" cellspacing="1" style="width:970px;text-align:center;font-family: 'Arial Narrow', Arial, sans-serif;" rules="all">
              <label  style="float:left;background:#CCCCCC; font-size:larger"><b>Trims Details</b> </label> 
              <tr style="font-weight:bold; background: #D7ECD9" >
                  <td width="110" style="background: #D7ECD9">Item Group</td>
                  <td width="110" style="background: #D7ECD9">Item Description</td>
                  <td width="100" style="background: #D7ECD9">Supplier</td>
                  <td width="60" style="background: #D7ECD9">UOM</td>
                  <td width="80" style="background: #D7ECD9">Cons/<?=$costing_for; ?>[Qnty]</td>
                  <td width="100" style="background: #D7ECD9">TTL Required[Qnty]</td>
                  <td width="80" style="background: #D7ECD9">Rate &#36;</td>
                  <td width="80" style="background: #D7ECD9">Amount/<?=$costing_for; ?>&#36;</td>
                  <td width="80" style="background: yellow">Amount &#36;</td>
                  <td width="60" style="background: #D7ECD9">% to Ord. Value</td>
              </tr>
              <?
             // $trim_qty_arr=$trims->getQtyArray_by_precostdtlsid();
              //print_r($trim_qty);
              //$trim_amount_arr=$trims->getAmountArray_precostdtlsid();
              $total_trims_cost=0;  $total_trims_qty=$total_trims_cost_dzn=0;$total_trims_cost_dzn=0;$total_trims_cost_kg=0;
              foreach( $data_array as $row ){ 
          $trim_group=return_library_array( "select item_name,id from  lib_item_group where id=".$row[csf("trim_group")], "id", "item_name" ); 
          $cons_dzn_gmts= $row[csf("cons_dzn_gmts")];
          $amount_dzn= $row[csf("amount")];
          $pre_trims_qty=$trim_qty_arr[$row[csf("id")]];
          $pre_trims_amount=$trim_amount_arr[$row[csf("id")]];  
          
          $nominated_supp_str="";
          $exsupp=explode(",",$row[csf("nominated_supp_multi")]);
          foreach($exsupp as $sid)
          {
            if($nominated_supp_str=="") $nominated_supp_str=$supplier_library_fabric[$sid]; else $nominated_supp_str.=','.$supplier_library_fabric[$sid];
          }            
          ?>   
          <tr>
                      <td align="left"><? echo $trim_group[$row[csf("trim_group")]]; ?></td>
                      <td align="left"><? echo $row[csf("description")]; ?></td>
                      <td align="left"><?=$nominated_supp_str; ?></td>
                      <td align="center"><? echo $unit_of_measurement[$row[csf("cons_uom")]]; ?></td>
                      <td align="right"><? echo fn_number_format($cons_dzn_gmts,3); ?></td>
                      <td align="right"><? echo fn_number_format($pre_trims_qty,4); ?></td>
                      <td align="right"><? echo fn_number_format($row[csf("rate")],3); ?></td>
                      <td align="right"><? echo fn_number_format($amount_dzn,4); ?></td>
                      <td align="right" style="background: yellow"><? echo fn_number_format($pre_trims_amount,2); ?></td>
                      <td align="right"  title="<? echo $amount_dzn.'='.$price_dzn;?>">
                      <? 
                      $cv=($amount_dzn/$price_dzn)*100;
                      if(is_infinite($cv) || is_nan($cv)){$cv=0;}
                      echo fn_number_format($cv,2); 
                      //echo fn_number_format(($amount_dzn/$price_dzn)*100,2); 
                      ?></td>
          </tr>
          <?
          $total_trims_cost += $pre_trims_amount;
          $total_trims_cost_dzn += $amount_dzn;
          $total_trims_qty += $pre_trims_qty;
              }
              ?>
              <tr class="rpt_bottom" style="font-weight:bold" >
                  <td>Trims Total</td>
                  <td colspan="4"></td>
                  <td align="right"><? if($total_trims_qty>0){ echo fn_number_format($total_trims_qty,4); } ?></td>
                  <td align="right"><? //echo fn_number_format($total_trims_cost_dzn,4); ?></td>                   
                  
                  <td align="right"><? if($total_trims_cost_dzn>0){ echo '&dollar;'.fn_number_format($total_trims_cost_dzn,4); } ?></td>
                  <td align="right" style="background: yellow"><? if($total_trims_cost>0){ echo '&dollar;'.fn_number_format($total_trims_cost,2); } ?></td>
                  <td align="right" title="<? echo $total_trims_cost_dzn.'='.$price_dzn;?>">
                  <? 
                  $cv=($total_trims_cost_dzn/$price_dzn)*100;
                  if(is_infinite($cv) || is_nan($cv)){$cv=0;}
                  if($cv){ echo fn_number_format($cv,2).' %'; }
                  ?>
                  </td>
              </tr>                
          </table>
      </div>
    <?
      $pre_cost_dtls_arr = sql_select("SELECT pre_cost_dtls_id as id, job_no, costing_per_id, order_uom_id, fabric_cost, fabric_cost_percent, trims_cost, depr_amor_pre_cost, deffdlc_cost, studio_cost, design_cost, trims_cost_percent, embel_cost, embel_cost_percent, comm_cost, comm_cost_percent, commission, incometax_cost, interest_cost, interest_percent, commission_percent, lab_test, lab_test_percent, inspection, inspection_percent, cm_cost, cm_cost_percent, freight, freight_percent, common_oh, common_oh_percent, design_percent, studio_percent, currier_pre_cost, currier_percent, certificate_pre_cost, certificate_percent, total_cost, total_cost_percent, price_dzn, price_dzn_percent, margin_dzn, margin_dzn_percent, price_pcs_or_set, price_pcs_or_set_percent, margin_pcs_set, margin_pcs_set_percent, cm_for_sipment_sche from wo_pre_cost_dtls_histry where job_no='".$txt_job_no."' and approved_no=$revised_no and status_active=1 and is_deleted=0");
      foreach ($pre_cost_dtls_arr as $row) {
      $price_dzn=$row[csf("price_dzn")];
      $lab_test_dzn=$row[csf("lab_test")];
      $commission_cost_dzn=$row[csf("commission")];
      $commercial_cost_dzn = $row[csf("comm_cost")];
      
      $inspection_dzn=$row[csf("inspection")];
      $cm_cost_dzn =$row[csf("cm_cost")];
      $common_oh_dzn =$row[csf("common_oh")];
      $freight_dzn =$row[csf("freight")];
      $currier_pre_cost_dzn = $row[csf("currier_pre_cost")];
      $certificate_pre_cost_dzn = $row[csf("certificate_pre_cost")];
      $deffdlc_cost_dzn = $row[csf("deffdlc_cost")];
      $depr_amor_pre_cost_dzn = $row[csf("depr_amor_pre_cost")];
      $interest_cost_dzn=$row[csf("interest_cost")];
      $interest_cost_percent=$row[csf("interest_percent")];
      $incometax_cost_dzn=$row[csf("incometax_cost")];
      $studio_cost_dzn=$row[csf("studio_cost")];
      $design_cost_dzn=$row[csf("design_cost")];        
      $studio_cost_percent=$row[csf("studio_percent")];
      $design_cost_percent=$row[csf("design_percent")]; 
      
      $other_cost_per = $inspection_dzn+$freight_dzn+$certificate_pre_cost_dzn+$deffdlc_cost_dzn+$design_cost_dzn+$studio_cost_dzn+$common_oh_dzn+$interest_cost_dzn+$incometax_cost_dzn+$depr_amor_pre_cost_dzn;
      }      
       ?>
      <table  class="rpt_table"  border="1" align="left" cellpadding="0" width="350" cellspacing="0" rules="all" style="margin-top: 10px;font-family: 'Arial Narrow', Arial, sans-serif;">
          <tr style="background: #D7ECD9">
              <th>MISC/Others Cost</th>
              <th>%</th>
              <th>TTL Cost $</th>
          </tr>
          <tr>
              <td>Test cost</td>
              <td align="right"><?
              $lab_test_per=($other_costing_arr[$job_id]['lab_test']/$order_values)*100;
              if(is_infinite($lab_test_per) || is_nan($lab_test_per)) $lab_test_per=0;
              
              if($lab_test_per>0){echo fn_number_format($lab_test_per,2);}
              $total_misc_per += $lab_test_per;
              ?></td>
              <th align="right"><? if($other_costing_arr[$job_id]['lab_test']>0){ echo fn_number_format($other_costing_arr[$job_id]['lab_test'],2);} ?></th>
          </tr>
          <tr>
              <td>Buying commission</td>
              <td align="right"><?
              $commission_cost_per=($other_costing_arr[$job_id]['commission']/$order_values)*100;
              if(is_infinite($commission_cost_per) || is_nan($commission_cost_per)) $commission_cost_per=0;
              
              if($commission_cost_per>0){ echo fn_number_format($commission_cost_per,2);}
              $total_misc_per +=$commission_cost_per;
              ?></td>
              <th align="right"><? if($other_costing_arr[$job_id]['commission']>0){ echo fn_number_format($other_costing_arr[$job_id]['commission'],2);} ?></th>
          </tr>
          <tr>
              <td>Commercial cost</td>
              <td align="right"><?
              $commercial_cost_per=($other_costing_arr[$job_id]['comm_cost']/$order_values)*100;
              if(is_infinite($commercial_cost_per) || is_nan($commercial_cost_per)) $commercial_cost_per=0;
              
              if($commercial_cost_per>0){ echo fn_number_format($commercial_cost_per,2); }
              $total_misc_per +=$commercial_cost_per;
              ?>            
              </td>
              <th align="right"><? if($other_costing_arr[$job_id]['comm_cost']>0) { echo fn_number_format($other_costing_arr[$job_id]['comm_cost'],2);} ?></th>
          </tr>
          <tr>
              <td>Other costs</td>
              <td align="right"><?
              $other_cost_per=($total_other_cost/$order_values)*100;
              if(is_infinite($other_cost_per) || is_nan($other_cost_per)) $other_cost_per=0;
              
              if($other_cost_per>0){ echo fn_number_format($other_cost_per,2);}
              $total_misc_per +=$other_cost_per;
              ?>            
              </td>
              <th align="right"><? if($total_other_cost>0){ echo fn_number_format($total_other_cost,2);} ?></th>
          </tr>
          <tr>
              <th>MISC/Others Cost Sub Total</th>
              <th align="right"><? if($total_misc_per>0){ echo fn_number_format($total_misc_per,2).'%'; }  ?></th>
              <th align="right"><? if($misc_cost>0){ echo '&dollar;'.fn_number_format($misc_cost,2); } ?></th>
          </tr>
      </table>
      <div id="div_size_color_matrix" style="float:left; max-width:1000; font-family: 'Arial Narrow', Arial, sans-serif;">
          <fieldset id="div_size_color_matrix" style="max-width:1000;">
        <?
              $color_library=return_library_array( "select id, color_name from lib_color where status_active=1 and is_deleted=0", "id", "color_name");
              $size_library=return_library_array( "select id, size_name from lib_size where status_active=1 and is_deleted=0", "id", "size_name");
              $nameArray_size=sql_select( "select  size_number_id, min(color_size_id) as id,  min(size_order) as size_order from wo_po_color_size_his where po_break_down_id in(".$txt_order_no_id.") and  job_no_mst='$txt_job_no' and approved_no=$revised_no and is_deleted=0 and status_active=1 group by size_number_id order by size_order");
              //echo "select  size_number_id,min(id) as id, min(size_order) as size_order from wo_po_color_size_breakdown where po_break_down_id in(".$txt_order_no_id.") and  job_no_mst=$txt_job_no and is_deleted=0 and status_active=1 group by size_number_id order by size_order"; die;
              ?>
              <legend>Size and Color Breakdown</legend>
                  <table class="rpt_table"  border="1" align="left" cellpadding="0" width="750" cellspacing="0" rules="all" >
                      <tr>
                          <td style="border:1px solid black"><strong>Color/Size</strong></td>
                          <?          
                          foreach($nameArray_size  as $result_size)
                          { ?>
                          <td align="center" style="border:1px solid black"><strong><? echo $size_library[$result_size[csf('size_number_id')]];?></strong></td>
                          <? } ?>       
                          <td style="border:1px solid black; width:130px" align="center"><strong> Total Order Qty(Pcs)</strong></td>
                          <td style="border:1px solid black; width:80px" align="center"><strong> Excess %</strong></td>
                          <td style="border:1px solid black; width:130px" align="center"><strong> Total Plan Cut Qty(Pcs)</strong></td>
                      </tr>
                      <?
                      $color_size_order_qnty_array=array(); $color_size_qnty_array=array();  $size_tatal=array(); $size_tatal_order=array();
                      for($c=0;$c<count($gmts_item); $c++)
                      {
              $item_size_tatal=array(); $item_size_tatal_order=array(); $item_grand_total=0; $item_grand_total_order=0;
              $nameArray_color=sql_select( "select color_number_id, min(color_size_id) as id,min(color_order) as color_order from wo_po_color_size_his where item_number_id=$gmts_item[$c] and po_break_down_id in(".$txt_order_no_id.")  and approved_no=$revised_no and is_deleted=0 and status_active=1 group by color_number_id order by color_order");
              ?>
              <tr>
                <td style="border:1px solid black" colspan="<? echo count($nameArray_size)+3;?>"><strong><? echo $garments_item[$gmts_item[$c]];?></strong></td>
              </tr>
              <?
              foreach($nameArray_color as $result_color)
              {           
                ?>
                <tr>
                                  <td align="center" style="border:1px solid black"><? echo $color_library[$result_color[csf('color_number_id')]]; // echo $row_num_tr; ?></td>
                                  <? 
                                  $color_total=0; $color_total_order=0;
                                  foreach($nameArray_size  as $result_size)
                                  {
                    $nameArray_color_size_qnty=sql_select( "select sum(plan_cut_qnty) as plan_cut_qnty, sum(order_quantity) as  order_quantity from wo_po_color_size_his where  po_break_down_id in(".str_replace("'","",$txt_order_no_id).") and  size_number_id =".$result_size[csf('size_number_id')]." and color_number_id =".$result_color[csf('color_number_id')]." and approved_no=$revised_no and item_number_id=$gmts_item[$c] and  status_active=1 and is_deleted =0");                          
                    foreach($nameArray_color_size_qnty as $result_color_size_qnty)
                    {
                      ?>
                      <td style="border:1px solid black; text-align:right">
                      <? 
                      if($result_color_size_qnty[csf('plan_cut_qnty')]!= "")
                      {
                        echo fn_number_format($result_color_size_qnty[csf('order_quantity')],0);
                        $color_total += $result_color_size_qnty[csf('plan_cut_qnty')] ;
                        $color_total_order += $result_color_size_qnty[csf('order_quantity')] ;
                        $item_grand_total+=$result_color_size_qnty[csf('plan_cut_qnty')];
                        $item_grand_total_order+=$result_color_size_qnty[csf('order_quantity')];
                        $grand_total +=$result_color_size_qnty[csf('plan_cut_qnty')];
                        $grand_total_order +=$result_color_size_qnty[csf('order_quantity')];
                        
                        $color_size_qnty_array[$result_size[csf('size_number_id')]][$result_color[csf('color_number_id')]]=$result_color_size_qnty[csf('plan_cut_qnty')];
                        $color_size_order_qnty_array[$result_size[csf('size_number_id')]][$result_color[csf('color_number_id')]]=$result_color_size_qnty[csf('order_quantity')];
                        if (array_key_exists($result_size[csf('size_number_id')], $size_tatal))
                        {
                          $size_tatal[$result_size[csf('size_number_id')]]+=$result_color_size_qnty[csf('plan_cut_qnty')];
                          $size_tatal_order[$result_size[csf('size_number_id')]]+=$result_color_size_qnty[csf('order_quantity')];
                        }
                        else
                        {
                          $size_tatal[$result_size[csf('size_number_id')]]=$result_color_size_qnty[csf('plan_cut_qnty')]; 
                          $size_tatal_order[$result_size[csf('size_number_id')]]=$result_color_size_qnty[csf('order_quantity')]; 
                        }
                        if (array_key_exists($result_size[csf('size_number_id')], $item_size_tatal))
                        {
                          $item_size_tatal[$result_size[csf('size_number_id')]]+=$result_color_size_qnty[csf('plan_cut_qnty')];
                          $item_size_tatal_order[$result_size[csf('size_number_id')]]+=$result_color_size_qnty[csf('order_quantity')];
                        }
                        else
                        {
                          $item_size_tatal[$result_size[csf('size_number_id')]]=$result_color_size_qnty[csf('plan_cut_qnty')]; 
                          $item_size_tatal_order[$result_size[csf('size_number_id')]]=$result_color_size_qnty[csf('order_quantity')]; 
                        }
                      }
                      else echo " ";
                      ?>
                      </td>
                      <?   
                    }
                                  }
                                  ?>
                                  <td style="border:1px solid black; text-align:right"><? if(round($color_total_order)>0){ echo fn_number_format(round($color_total_order),0);} ?></td>
                                  <td style="border:1px solid black; text-align:right"><? $excexss_per=($color_total-$color_total_order)/$color_total_order*100; if(round($excexss_per)>0){ echo fn_number_format($excexss_per,2)." %";} ?></td>
                                  <td style="border:1px solid black; text-align:right"><? if(round($color_total)>0){ echo fn_number_format(round($color_total),0);} ?></td>
                              </tr>
                              <?
              }
              ?>
                          <tr>
                              <td align="center" style="border:1px solid black"><strong>Sub Total</strong></td>
                              <?
                              foreach($nameArray_size  as $result_size)
                              {
                  ?><td style="border:1px solid black;  text-align:right"><? echo $item_size_tatal_order[$result_size[csf('size_number_id')]];  ?></td><?
                              }
                              ?>
                              <td  style="border:1px solid black;  text-align:right"><? if(round($item_grand_total_order)>0){ echo fn_number_format(round($item_grand_total_order),0); } ?></td>
                              <td  style="border:1px solid black;  text-align:right"><? $excess_item_gra_tot=($item_grand_total-$item_grand_total_order)/$item_grand_total_order*100; if($excess_item_gra_tot>0){echo fn_number_format($excess_item_gra_tot,2)." %"; } ?></td>
                              <td  style="border:1px solid black;  text-align:right"><?  if(round($item_grand_total)>0){echo fn_number_format(round($item_grand_total),0); } ?></td>
              </tr>
              <?
                      }
                      ?>
                      <tr>
                        <td style="border:1px solid black" align="center" colspan="<? echo count($nameArray_size)+3; ?>"><strong>&nbsp;</strong></td>
                      </tr>
                      <tr>
                          <td align="center" style="border:1px solid black"><strong>Grand Total</strong></td>
                          <?
                          foreach($nameArray_size  as $result_size)
                          {
                            ?><td style="border:1px solid black;  text-align:right"><? echo $size_tatal_order[$result_size[csf('size_number_id')]]; ?></td><?
                          }
                          ?>
                          <td style="border:1px solid black;  text-align:right"><? if(round($grand_total_order)>0){ echo fn_number_format(round($grand_total_order),0); } ?></td>
                          <td style="border:1px solid black;  text-align:right"><? $excess_gra_tot= ($grand_total-$grand_total_order)/$grand_total_order*100; if($excess_gra_tot>0) { echo fn_number_format($excess_gra_tot,2)." %"; } ?></td>
                          <td style="border:1px solid black;  text-align:right"><?  if(round($grand_total)>0) { echo fn_number_format(round($grand_total),0); } ?></td>
                      </tr>
              </table>
          </fieldset>
      </div>
      <br/><br/>
      <div>
      <br/>
    <?
      $width=990; $padding_top = 70; $prepared_by='';
      $sql = sql_select("select designation,name,activities,prepared_by from variable_settings_signature where report_id=109 and company_id=$cbo_company_name order by sequence_no");
      
      if($sql[0][csf("prepared_by")]==1){
      list($prepared_by,$activities)=explode('**',$prepared_by);
      $sql_2[100] = array ( DESIGNATION => 'Prepared By' ,NAME => $prepared_by, ACTIVITIES =>$activities, PREPARED_BY => 0 );
      $sql=$sql_2+$sql;
      }
      
      $count = count($sql);
      $td_width = floor($width / $count);
      $standard_width = $count * 120;
      if ($standard_width > $width) $td_width = 120;
      
    $no_coloumn_per_tr = floor($width / $td_width);
    $i = 1;
    if ($count == 0) {$message = "<b>Note: This is Software Generated Copy , Signature is not Required.</b>";}
    echo '<table id="signatureTblId" width="' . $width . '" style="padding-top:' . $padding_top . 'px;"><tr><td width="100%" height="' . $padding_top . '" colspan="' . $count . '">' . $message . '</td></tr><tr>';
    foreach ($sql as $row) {
      echo '<td width="' . $td_width . '" align="center" valign="top">
      <strong>' . $row[csf("activities")] . '</strong><br>
      <strong style="text-decoration:overline">' . $row[csf("designation")] . "</strong><br>" . $row[csf("name")] . '</td>';
      if ($i % $no_coloumn_per_tr == 0) {
        echo '</tr><tr><td width="100%" height="70" colspan="' . $no_coloumn_per_tr . '"></td></tr>';
      }
      $i++;
    }
    echo '</tr></table>';
    ?>
    </div>
    <?
    disconnect($con);
    exit();
  }



  if($action == 'view_comments_list'){
    extract($_REQUEST);
    echo load_html_head_contents($tittle." No Info", "../../../", 1, 1,'','','');

    $poSql = "select ID,PO_NUMBER,JOB_ID from WO_PO_BREAK_DOWN where job_id=$job_id and is_deleted=0 and status_active=1"; 
    //echo $poSql;die;
    $poSqlRes = sql_select($poSql);
    $po_number_arr = array(); 
    foreach($poSqlRes as $rows){
      $po_number_arr[$rows['ID']] = $rows['PO_NUMBER'];
    }
  
    
    $commentsSql = "select ID ,MST_ID ,MST_DTLS_ID ,FORM_NAME,TYPE,COMMENTS,INSERTED_BY,INSERT_DATE  FROM  COMMON_COMMENTS_LIBRARY where MST_ID=$job_id and TYPE=1 and FORM_NAME='component_wise_precost_app' order by id desc,INSERTED_BY";
    //echo $commentsSql;die;
    $commentsSqlRes = sql_select($commentsSql);
    $user_arr = return_library_array("select ID,USER_FULL_NAME from USER_PASSWD", 'ID', 'USER_FULL_NAME');

   // print_r($user_arr);



    ?>
  
    <table border="1" rules="all" cellpadding="0" cellspacing="0" width="100%" class="rpt_table">
      <thead>
        <th>User</th>
        <th>Date</th>
        <th>Po Number</th>
        <th>Comments</th>
      </thead>
      <?
      $i=1;
      foreach($commentsSqlRes as $commentsRow){ 
        $bgcolor=($i%2==0)?"#E9F3FF":"#FFFFFF";
      ?>
      <tr style="cursor:pointer" bgcolor="<?=$bgcolor; ?>">
        <td><?= $user_arr[$commentsRow['INSERTED_BY']];?></td>
        <td><?= $commentsRow['INSERT_DATE'];?></td>
        <td><?= $po_number_arr[$commentsRow['MST_DTLS_ID']];?></td>
        <td><?= $commentsRow['COMMENTS'];?></td>
      </tr>
      <? 
      $i++;
      } 
      ?>
    </table>
  
  <?
  
    exit();


  }


?>