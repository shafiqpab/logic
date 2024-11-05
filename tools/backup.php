<?
	require_once('../includes/common.php');
	echo load_html_head_contents("Database Backup", "../", 1, 1,'','','');
?>

<script>
function fnc_create_backup()
{
	freeze_window(1);
	var d= $.ajax({
		  url: "backup_db.php",
		  async: false
		}).responseText
	release_freezing();
	var respnse=d.split("**");
	var w = window.open( respnse[1] , "#");
	return;
}

</script>
<body onLoad="set_hotkey();">
<div style="width:100%;" align="center">
<br />
<? echo load_freeze_divs ("../",$permission);  ?>
    <fieldset style="width:420px;">
    <legend>Database Backup</legend> 
		<form name="databaseBackup_1" id="databaseBackup_1"  method="post"> 
            <table width="400" align="center" border="0">
                <tr style="display:none">
                    <td width="110" class="must_entry_caption"><b>Database Name</b></td>
                    <td>
                        <input type="text" class="text_boxes" name="dbName" id="dbName"  value="testhrmup" style="width:200px" />
                    </td>
                </tr>
                <tr>
                    <td colspan="2" align="center">
                        <input type="button" name="submit" onClick="fnc_create_backup()" value="Create Backup" class="formbutton" style="width:100px" />
                    </td>
                </tr>
            </table>
        </form>
    </fieldset>
</div>
</body>
<script src="../includes/functions_bottom.js" type="text/javascript"></script>
</html>