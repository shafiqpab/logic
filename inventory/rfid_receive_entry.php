<?
/*-------------------------------------------- Comments
Purpose			: 	This form will create Store Item Inquiry

Functionality	:
JS Functions	:
Created by		:	Rezoanul
Creation date 	: 	22-11-2023
Updated by 		:
Update date		:
QC Performed BY	:
QC Date			:
Comments		:
*/

session_start();
if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");

require_once('../includes/common.php');
extract($_REQUEST); 
$_SESSION['page_permission']=$permission;
//--------------------------------------------------------------------------------------------------------------------
echo load_html_head_contents("Yarn allocation data Reconciliation","../", 1, 1, $unicode);
?>

<script>
	var permission='<? echo $permission; ?>';
	if( $('#index_page', window.parent.document).val()!=1) window.location.href = "../logout.php";

	function fnc_rfid_receive_entry( operation )
  {
    if(operation == 2)
    {
      alert("Delete is not permitted");
      return;
    }
    if (form_validation('rfid_no*cbo_rfid_type*txt_receive_date*cbo_status','RFID No*RFID Type*Receive Date*Status')==false)
    {
    return;
    }

    var data="action=save_update_delete&operation="+operation+get_submitted_data_string('rfid_no*cbo_rfid_type*txt_receive_date*cbo_status*update_id',"../../");
    freeze_window(operation);
    http.open("POST","requires/rfid_receive_entry_controller.php",true);
    http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
    http.send(data);
    http.onreadystatechange = fnc_rfid_receive_entry_response;
  }
	
	function fnc_rfid_receive_entry_response()
	{
		if(http.readyState == 4) 
		{  
			var response=trim(http.responseText).split('**');
			show_msg(trim(response[0]));
      
			if(response[0]==11)
			{
				alert("Duplicate data found");
                release_freezing();
			}
			if(response[0]==0 || response[0]==1 || response[0]==2)
			{
                // alert("saved");
				show_list_view('','rfid_list_view','rfid_list','requires/rfid_receive_entry_controller','setFilterGrid("list_view",-1)');        				
				reset_form('rfid_receive_entry_1','','');
				set_button_status(0, permission, 'fnc_rfid_receive_entry',1);
        release_freezing();
			} 
            release_freezing();

		}
	} 
    function fnResetForm()
    {
        set_button_status(0, permission, 'fnc_rfid_receive_entry',1);
    }
</script>
</head>
<body onLoad="set_hotkey()">
    <? echo load_freeze_divs ("../",$permission);  ?>
	<div align="center" style="width:100%;">
    
      <fieldset style="width:600px;">
        <legend>RFID Receive</legend>
        <form name="rfid_receive_entry_1" id="rfid_receive_entry_1" autocomplete = "off"> 
          <table cellpadding="0" cellspacing="2" width="80%">
            <tr>
              <td style="width: 25%;"  class="must_entry_caption">RFID No</td>
              <td style="width: 25%;" ><input  class="text_boxes" type="text" id="rfid_no" name="rfid_no" title="Maximum 50 Character"></td>
              <input type="hidden" name="update_id" id="update_id">
              <td style="width: 25%;" class="must_entry_caption">RFID Type</td>
              <td style="width: 25%;" >
                <!-- <input class="text_boxes" type="text" id="rfid_type" placeholder="loose bag" name="rfid_type" title="Maximum 50 Character"> -->
                <?
                $rfid_type_arr = array(1=>"Regular",2=>"Loose Bag");
                echo create_drop_down( "cbo_rfid_type", 143, $rfid_type_arr,'', '', '', 1 );
                ?>
              </td>
            </tr>
            <tr>
              <td style="width: 25%;"  class="must_entry_caption">Receive Date</td>
              <td style="width: 25%;">
                    <input type="text" name="txt_receive_date" id="txt_receive_date" class="datepicker" style="width:130px;" placeholder="Select Date" value="<? echo date('d-m-Y');?>" readonly/>
                </td>
              <td style="width: 25%;" class="must_entry_caption">Status</td>
              <td style="width: 25%;"> <?php 
                  $status_arr = array(1=>"Active",2=>"Inactive");
                  echo create_drop_down( "cbo_status", 143, $status_arr,'', '', '', 1 ); 
                ?></td>
            </tr>

            <!-- button container  -->
            <tr style="position: relative;left: 71px;">
               
                <td  colspan="3" height="40" valign="bottom" align="center" class="button_container">
                <? 
                  echo load_submit_buttons( $permission, "fnc_rfid_receive_entry", 0,0 ,"reset_form('rfid_receive_entry_1','','','');fnResetForm();");
                ?>
              </td>		
             		
            </tr>
          </table>
        </form>
        <!-- data table  -->
        <table cellpadding="0" cellspacing="2" width="520px" align="center">
          <tr>
            <td valign="bottom" align="center"  id="rfid_list">
              <?
                $arr = array(1 => $rfid_type_arr,2 => $status_arr);
                
                echo  create_list_view ( "list_view", "RFID No,RFID Type, Status", "120,150,180","450","150",1, "SELECT id, rfid_number,rfid_type, status_active  from lib_rfid_mst where is_deleted=0 order by id desc", "get_php_form_data", "id","'load_php_data_to_form'", 1, "0,rfid_type,status_active", $arr , "rfid_number,rfid_type,status_active", "requires/rfid_receive_entry_controller", 'setFilterGrid("list_view",-1);' ) ;
              ?>
            </td>					
          </tr>
        </table>
        <br>
      </fieldset>
    </div>
</body>
<script src="../includes/functions_bottom.js" type="text/javascript"></script>
</html>
