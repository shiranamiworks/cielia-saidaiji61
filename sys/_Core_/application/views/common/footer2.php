
<?php /*
<div id="gFooter">
<p class="copyright-text">Copyright &copy; System Corp. All Rights Reserved.</p>
</div>
*/?>
</div>


<?php
if(defined('ENVIRONMENT') && ENVIRONMENT === 'development') {
echo "<div id='dbDebug' style='background:white;color:black;position:absolute;margin:10px;padding:10px;bottom:10px;right:10px'>" . $this->db->last_query() . "</div>";
}
?>
</body>
</html>
