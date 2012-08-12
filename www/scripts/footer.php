<?php
if (!defined("inWeSkateCheck")) { die("Acces respins."); }
$continut = ob_get_contents();
ob_end_clean();
render_page($continut);
?>
<script type="text/javascript">

  var _gaq = _gaq || [];
  _gaq.push(['_setAccount', 'UA-5465156-1']);
  _gaq.push(['_trackPageview']);

  (function() {
    var ga = document.createElement('script'); ga.type = 'text/javascript'; ga.async = true;
    ga.src = ('https:' == document.location.protocol ? 'https://ssl' : 'http://www') + '.google-analytics.com/ga.js';
    var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(ga, s);
  })();

</script>
<?php
echo "</body>\n</html>\n";
$output = ob_get_contents();
ob_end_clean();
echo handle_output($output);

if(ob_get_length () !== FALSE){
	ob_end_flush();
}
mysql_close();
?>
