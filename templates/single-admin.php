<!-- Template for the google chart area in the edit post page -->
<?php
$the_naq_id = get_the_ID(); // Get the ID of the post that is currently edited
?>
<!-- Store the ID in a hidden field for use in myscript.js -->
<input type="hidden" name="hiddenField" id="hiddenField" value="<?php echo $the_naq_id ?>" />
<!-- Show the google chart -->
<div id='chart_div' style='width: 900px; height: 600px;'>This post has no views yet.</div>
