<?php
/* Developed by Kernel Team.
   http://kernel-team.com
*/
if (isset($_REQUEST['force_embed']))
{
	header("Location: kt_player.swf?$_SERVER[QUERY_STRING]"); die;
}
?>