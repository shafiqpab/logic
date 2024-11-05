<script>
	const intervalID = setInterval(myCallback, 3000, 1);

var flag=0;
function myCallback(a)
{
	flag+=a;
	if(flag==2){alert(flag);flag=0;}
	else{alert(flag);}
	 

}

</script>