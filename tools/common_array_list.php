<?
/*-------------------------------------------- Comments
Purpose			: 	This form will create common array list				
Functionality	:	
JS Functions	:
Created by		:	Zakaria joy
Creation date 	: 	04-06-2020
Updated by 		: 	
Update date		: 	
QC Performed BY	:		
QC Date			:	
Comments		:
*/
session_start();
if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:../login.php");
require_once('../includes/common.php');
//require_once('../../includes/common.php');
extract($_REQUEST);
$_SESSION['page_permission']=$permission;
//--------------------------------------------------------------------------------------------------------------------
echo load_html_head_contents("Array List", "../", 1, 0, $unicode,'','');
?>
<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.0/css/bootstrap.min.css">
<?
$ignore = array('_SESSION','_REQUEST','GLOBALS', '_FILES', '_COOKIE', '_POST', '_GET', '_SERVER', '_ENV', 'ignore','db_type','tna_process_type','select_job_year_all','tna_process_start_date','buyer_cond','company_cond','production_squence','blank_array','php_errormsg');
// diff the ignore list as keys after merging any missing ones with the defined list
$vars = array_diff_key(get_defined_vars() + array_flip($ignore), array_flip($ignore));
// should be left with the user defined var(s) (in this case $testVar)
/*echo '<pre>';
print_r($entry_form);*/
/*foreach ($vars as $key => $value) {
	echo '<pre>';
	print_r($entry_form);
}*/
?>
<table class="table table-striped">
	<thead>
        <tr>
            <th width="50">Id</th>
            <th width="170">Name</th>
        </tr>
    </thead>
    <tbody>
    	<?
    		foreach ($entry_form as $key => $value) { ?>
    		<tr>
    			<th width="50"><? echo $key ?></th>
            	<th width="370"><? echo $value ?></th>
    		</tr>	
    		<? } ?>
    </tbody>
</table>
