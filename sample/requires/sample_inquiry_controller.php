<?
header('Content-type:text/html; charset=utf-8');
session_start();
if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");
include('../../includes/common.php');
$data=$_REQUEST['data'];
$action=$_REQUEST['action'];
$permission=$_SESSION['page_permission'];
//...............................................
if ($action=="barcode_list_view")
{
	echo load_html_head_contents("Sample List View","../../", 1, 1, $unicode);
?>	
	<script> 
	function js_set_value(data)
	{
		document.getElementById('update_id').value=data;
		parent.emailwindow.hide();
	}
	
	</script> 
	<input type="hidden" id="update_id"	 value="">
<?	
	echo load_html_head_contents("Sample inquiry", "../../", 1, 1,$unicode,'','');
		
	$purpose=array(1=>"Presentation",2=>"Buyer Selected",3=>"Unknown and  Adjustment");
	$team_leader=return_library_array( "select id,team_leader_name from lib_marketing_team", "id", "team_leader_name"  );
	
	$arr=array (3=>$team_leader,4=>$dealing_merchant,5=>$purpose,7=>$yes_no);
			  
	echo  create_list_view ( "list_view","Item Barcode,Item Name,Quantity,Team Leader,Dealing Merchant,Purpose,Gifted To,Returnable,Posiable Return date", "100,80,50,100,100,100,80,80","890","220",0, "select id,item_barcode,issue_date,issue_qty,team_leader,dealing_merchant,issue_purpose,gifted_to,issue_returnable,pos_re_date from sample_issue_mst where is_deleted=0", "js_set_value", "item_barcode", "", 1, "0,0,0,team_leader,dealing_merchant,issue_purpose,0,issue_returnable", $arr , "item_barcode,issue_date,issue_qty,team_leader,dealing_merchant,issue_purpose,gifted_to,issue_returnable,pos_re_date", "requires/sample_issue_controller", 'setFilterGrid("list_view",-1);','0,3,1,0,0,0,0,0,3' ) ;
}


if ($action=="sample_list_view")
{
		$purpose=array(1=>"Presentation",2=>"Buyer Selected",3=>"Unknown and  Adjustment");
		$history=sql_select("SELECT a.id,a.pos_re_date,a.issue_returnable,a.dealing_merchant,a.issue_date,a.gifted_to,a.issue_purpose,b.receive_date from sample_issue_mst a,sample_receive_mst b  where a.id=b.id and   item_barcode='$data'   order by issue_date Asc");
	?>
    <table border="1" cellspacing="0" class="rpt_table" rules="all" width="850" >
		<tr style="background-color:#A7C0DC !important; height:20px;">
			<th align="center">SL</th>
            <th align="center">Receive Date</th>
            <th align="center">Issue Date</th>
            <th align="center">Returable</th>
			<th align="center">Possible Return date</th>
			<th align="center">Issue To</th>
			<th align="center">Purpose</th>
            <th align="center">Return Date</th>
			<th align="center">Dealing Merchant</th>
		</tr>
		
    <?
		$dealing_merchant=return_library_array( "select id,team_member_name from lib_mkt_team_member_info", "id", "team_member_name"  );
		
	$i=1;
		foreach ($history as $row)
		{
			
			?>
            <tr style="background-color:#A7C0DC !important; height:5px;">
			<th align="center"></th>
            <th align="center"></th>
            <th align="center"></th>
            <th align="center"></th>
			<th align="center"></th>
			<th align="center"></th>
			<th align="center"></th>
            <th align="center"></th>
			<th align="center"></th>
		</tr>
        <?

		$i++;
			?>
			
                 <?php
				
				if($row[csf("receive_date")]==1)
				{
				?>
                
				<tr>  
					<td align="center"><? echo 1;?></td> 
					<td align="center"><? echo change_date_format($row[csf("receive_date")]); ?></td>
					<td align="center"></td>
					<td align="center"></td>
					<td align="center"></td>
					<td align="center"></td>
					<td align="center"></td>
					<td align="center"></td>
					<td align="center"></td>
				</tr>
                
                <? }?>
				<tr>
					<td align="center"><? echo $i;?></td>
					<td align="center"></td>
					<td align="center"><? echo change_date_format($row[csf("issue_date")]); ?></td>
					<td align="center"><? echo $yes_no[$row[csf("issue_returnable")]]; ?></td>
					<td align="center"><? echo change_date_format($row[csf("pos_re_date")]); ?></td>
					<td align="center"><? echo $row[csf("gifted_to")]; ?></td>
					<td align="center"><? echo $purpose[$row[csf("issue_purpose")]]; ?></td>
                    <td align="center"><? echo $row[csf("return_date")]; ?></td>
					<td align="center"><? echo $dealing_merchant[$row[csf("dealing_merchant")]]; ?></td>
				</tr>
                <?
		}
		echo "</table>";
}
	

if ($action=="load_php_data_to_form")
{
	$nameArray=sql_select( "select id,item_barcode,issue_date,issue_qty,team_leader,dealing_merchant,issue_purpose,gifted_to,issue_returnable,pos_re_date from sample_issue_mst where id='$data'" );
	
	
	foreach ($nameArray as $inf)
	{
		echo "document.getElementById('system_id').value 			= '".($inf[csf("id")])."';\n";
		echo "document.getElementById('item_barcode').value 		= '".($inf[csf("item_barcode")])."';\n";    
		echo "document.getElementById('issue_date').value  			= '".change_date_format($inf[csf("issue_date")])."';\n";
		echo "document.getElementById('txtissued_qty').value  		= '".($inf[csf("issue_qty")])."';\n";
		echo "document.getElementById('cbo_team_leader').value  	= '".($inf[csf("team_leader")])."';\n";
		echo "load_drop_down( 'requires/sample_inquiry_controller', '".$inf[csf("team_leader")]."', 'cbo_dealing_merchant', 'div_marchant' );\n"; 
		echo "document.getElementById('cbo_dealing_merchant').value = '".($inf[csf("dealing_merchant")])."';\n";
		echo "document.getElementById('cbo_Purpose').value  		= '".($inf[csf("issue_purpose")])."';\n";
		echo "document.getElementById('txt_gifted_to').value  		= '".($inf[csf("gifted_to")])."';\n";
		echo "document.getElementById('returnable').value  			= '".($inf[csf("issue_returnable")])."';\n";
		echo "document.getElementById('pos_return_date').value  	= '".change_date_format($inf[csf("pos_re_date")])."';\n";
		echo "document.getElementById('update_id').value  			= '".($inf[csf("id")])."';\n"; 
		echo "set_button_status(1, '".$_SESSION['page_permission']."', 'fnc_sample_issue',1);\n";  
	}
}


if ($action=="cbo_dealing_merchant")
{
	echo create_drop_down( "cbo_dealing_merchant", 210, "select id,team_member_name from lib_mkt_team_member_info where team_id='$data' and status_active =1 and is_deleted=0 order by team_member_name","id,team_member_name", 1, "-- Select Team Member --", $selected, "" ,1);
}
