<?
/*-------------------------------------------- Comments
Purpose			: 	This form will create  for Monthly Dyeing Production Entry
Functionality	:	
JS Functions	:
Created by		:	Md. Wasy Ul Amin
Creation date 	: 	25-10-2023
Updated by 		: 		
Update date		: 		   
QC Performed BY	:		
QC Date			:	
Comments		:
*/
session_start();
if ($_SESSION['logic_erp']['user_id'] == "") header("location:login.php");
require_once('../../includes/common.php');
extract($_REQUEST);
$_SESSION['page_permission'] = $permission;
//----------------------------------------------------------------------------------------------------------------
echo load_html_head_contents("Monthly Dyeing Production Entry", "../../", 1, 1, $unicode, '', '');
//$monthArr = array("January"=>"January", "February"=>"February", "March"=>"March","April"=>"April","May"=>"May","June"=>"June","July"=>"July","August"=>"August","September"=>"September","October"=>"October","November"=>"November","December"=>"December");

//$monthArr = array(1=>"January", 2=>"February", 3=>"March",4=>"April",5=>"May",6=>"June",7=>"July",8=>"August",9=>"September",10=>"October",11=>"November",12=>"December");
$monthArr = array("01"=>"January", "02"=>"February", "03"=>"March","04"=>"April","05"=>"May","06"=>"June","07"=>"July","08"=>"August","09"=>"September","10"=>"October","11"=>"November","12"=>"December");
?>
<script type="text/javascript" charset="utf-8">
  if ($('#index_page', window.parent.document).val() != 1) window.location.href = "../../logout.php";
  var permission = '<? echo $permission; ?>';

  function fnc_monthly_dyeing_production_entry(operation) {


          if (form_validation('cbo_company_name*cbo_year*txt_P_Qty*cbo_month_id', 'Company Name*Year*Production*Month') == false) {
            return;
          }
      
        var from_date=$("#txt_from_date").val();      
        var company=$("#cbo_company_name").val();   
        var updateId=$("#update_id").val(),response;   

        $.ajax({
              url: 'requires/monthly_dyeing_production_entry_controller.php',
              type: 'POST',
              data: {f_date:from_date,companyId:company},
              success: function(data) {	
            
              if (typeof data === 'string' || data instanceof String)
              {
                  response=data.split("*");
                  data=response[0];
              }
              
              if((operation && data==1 && updateId==response[1]) ||  data==0){ 				 
                  
                  var data = "action=save_update_delete&operation=" + operation + get_submitted_data_string('cbo_company_name*cbo_year*cbo_month_id*txt_from_date*txt_to_date*txt_P_Qty*update_id', "../../");
                  
                  freeze_window(operation);
                  
                  http.open("POST","requires/monthly_dyeing_production_entry_controller.php",true);
                  http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
                  http.send(data);
                  http.onreadystatechange = fnc_monthly_dyeing_production_entry_reponse;
              }                         			 
              
              else{            
                  alert("Duplicate From Date is Not Allow.");
              }						

              if(operation==2){
                  alert("Data delete is not possible.");return;
              }
            }
        });      
  }


  function fnc_monthly_dyeing_production_entry_reponse()
	{
		if(http.readyState == 4) 
		{
			var reponse=trim(http.responseText).split('**');
			if (reponse[0].length>2) reponse[0]=10;
			show_msg(reponse[0]);			 
			reset_form('subdepartment_1', '', '');
			set_button_status(0, permission, 'fnc_monthly_dyeing_production_entry', 1);
			show_list_view('', 'sub_department_list_view', 'sub_department_list_view', '../merchandising_details/requires/monthly_dyeing_production_entry_controller', 'setFilterGrid("list_view",-1)');			
			release_freezing();			 
		}
	}


  function apply_period_date_range(){
        var thisDate=($('#txt_from_date').val()).split('-');
        var last=new Date( (new Date(thisDate[2], thisDate[1],1))-1 );
        var last_date = last.getDate();
        var month = last.getMonth()+1;
        var year = last.getFullYear();
        if(month<10)
            var months='0'+month;
        else
            var months=month;

        var last_full_date=last_date+'-'+months+'-'+year;
        var first_full_date='01'+'-'+months+'-'+year;

        $('#txt_from_date').val(first_full_date);
        $('#txt_to_date').val(last_full_date);
    }

    function date_set(){

      //var monthsArr = new Array(['January',1],['February',2],['March',3], ['April',4], ['May',5],['June',6], ['July',7], ['August',8],['September',9], ['October',10], ['November',11], ['December',12] );
         
        /*var year= $("#cbo_year").val();
        let currentDate="";

        const date = new Date();
        let day = date.getDate();
        //let month = date.getMonth()+1;         
        let month = $("#cbo_month_id").val();     
        currentDate = `${day}-${month}-${year}`;         

        document.getElementById('txt_from_date').value  = currentDate;

        let lastDay = new Date(date.getFullYear(), date.getMonth() + 1, 0);
        let text = lastDay.toString();
        const myArray = text.split(" ");         
        currentDate = `${myArray[2]}-${month}-${year}`;         
        
        document.getElementById('txt_to_date').value  = currentDate;*/

        var year= $("#cbo_year").val();
        let currentDate="";

        if($("#cbo_month_id").val()==0){
          return;
        }

        const date = new Date();
        let day = '01';
        //let month = date.getMonth()+1;         
        let month = parseInt($("#cbo_month_id").val());  
        if(month<10)
             month='0'+month;
        else
             month=month;   
        currentDate = `${day}-${month}-${year}`;         

        document.getElementById('txt_from_date').value  = currentDate;

        let lastDay = new Date(year, month, 0);
        let text = lastDay.toString();
        const myArray = text.split(" ");         
        currentDate = `${myArray[2]}-${month}-${year}`;         
        
        document.getElementById('txt_to_date').value  = currentDate;
    }
	function fn_company_onchange()
	{
		//alert(1);
		//reset_form('subdepartment_1', '', '');
		document.getElementById('txt_from_date').value  = "";
		document.getElementById('txt_to_date').value  = "";
		document.getElementById('txt_P_Qty').value  = "";

	}
</script>

<body onLoad="set_hotkey()">
  <div align="center" style="width:100%;">
    <? echo load_freeze_divs("../../", $permission);  ?>
    <fieldset style="width:700px; margin-top:10px;">
      <legend>Monthly Dyeing Production Entry</legend>
      <form id="subdepartment_1" name="subdepartment_1" autocomplete="off">
        <table width="100%" border="0" cellpadding="0" cellspacing="0">

          <tr>
            <td width="120" class="must_entry_caption" align="center">Company</td>
            <td width="80" class="must_entry_caption" align="center">Year</td>
            <td width="100" class="must_entry_caption" align="center">Month</td> 
            <td width="90" align="center">From Date</td>
            <td width="90" align="center">To Date</td>
            <td width="90" class="must_entry_caption" align="center">Production Qty.</td>

          </tr>
          <tr>
            <td width="120" class="must_entry_caption" align="center"><? echo create_drop_down("cbo_company_name", 150, "select comp.id, comp.company_name from lib_company comp where comp.status_active=1 and comp.is_deleted=0   order by comp.company_name", "id,company_name", 1, "-- Select Company --", $selected, "fn_company_onchange()", 0); ?></td>

            <td width="80" align="center"><? echo create_drop_down("cbo_year", 80, create_year_array(), "", 1, "-- All --", date("Y", time()), " ", 0, "");  //date("Y",time()) ?></td>

            <td width="100" id="week_id" align="center" ><? echo create_drop_down("cbo_month_id", 100, $monthArr, 0, 1, "--Select Month--",-1, "date_set()", "", ""); ?></td> 

            <td width="100" align="center"><input type="text" name="txt_from_date" class="datepicker" id="txt_from_date" disabled style="width: 90px;" onChange="apply_period_date_range()"/></td>

            <td width="100" align="center"> <input type="text" name="txt_to_date" class="datepicker" id="txt_to_date" disabled style="width: 90px;" />

            <td width="100" align="center"> <input type="text" name="txt_P_Qty" class="text_boxes_numeric" id="txt_P_Qty" style="width: 90px;" />

              <input type="hidden" id="update_id" name="update_id" value="">
            </td>


          </tr>


          <tr>
            <td colspan="6">&nbsp;</td>
          </tr>

          <tr>
            <td colspan="6" align="center" class="button_container">
              <?
              echo load_submit_buttons($permission, "fnc_monthly_dyeing_production_entry", 0, 0, "reset_form('subdepartment_1','','','','','')");
              ?>
            </td>
          </tr>

          <tr>
            <td colspan="6">&nbsp;</td>
          </tr>

          <tr>
            <td colspan="6" id="sub_department_list_view" align="center">
              <?

              $company_library = return_library_array("select id, company_name from lib_company", "id", "company_name");               
			        $arr = array(0 => $company_library);

              //$arr = array(0 => $company_library,2 => $monthArr);			                
              //$query ="SELECT a.id, a.company, a.years, a.month, a.from_date, a.to_date, a.p_qty FROM monthly_dyeing_production_entry a WHERE a.status_active =1 AND a.is_deleted =0  order by a.id";
              //echo  create_list_view ( "list_view", "Company,Year, Month,From Date,To Date, Production Qty", "140,70,100,110,110,70", "700","220",1, $query,"get_php_form_data","id", "", 1,"company,month", $arr, "company,years,month,from_date,to_date,p_qty", "requires/monthly_dyeing_production_entry_controller", "setFilterGrid('list_view',-1)","0","",1);
              
              
              echo  create_list_view("list_view", "Company,Year,From Date,To Date, Production Qty.", "140,70,110,110,70", "600", "220", 1, "SELECT a.id, a.company, a.years, a.from_date, a.to_date, a.p_qty FROM monthly_dyeing_production_entry a WHERE a.status_active =1 AND a.is_deleted =0  order by a.id", "get_php_form_data", "id", "'load_php_data_to_form'", 1, "company", $arr, "company,years,from_date,to_date,p_qty", "requires/monthly_dyeing_production_entry_controller", 'setFilterGrid("list_view",-1);', '');

              ?>
            </td>
          </tr>

        </table>
      </form>
    </fieldset>
  </div>
</body>
<script src="../../includes/functions_bottom.js" type="text/javascript"></script>
<!-- load_drop_down('requires/hnm_calendar_entry_controller', this.value, 'load_drop_down_week', 'week_id' ); -->

</html>