<!-- Template for the google chart area in the admin area -->
<h1>Filox Count Views</h1>
<!-- Select box with all the posts -->
Select a post:
<select id="naq_id">
<?php
$args = array(
    'posts_per_page'=>-1,
);
$posts = get_posts ($args);
foreach ( $posts as $post ) {
     echo "<option value='{$post->ID}'>{$post->post_title}</option>";
   }
?>
</select>
<br>
<br>

<!-- Show the google chart -->
<div id='chart_div' style='width: 900px; height: 600px;'>This post has no views yet.</div>
