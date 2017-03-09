<?php
$fh=fopen('index.php', 'r');
$data=fread($fh, filesize('index.php'));
preg_match_all('/(?<=post\(\')(.*)(?=\',)/', $data, $post);
preg_match_all('/(?<=get\(\')(.*)(?=\',)/', $data, $get);
preg_match_all('/(?<=any\(\')(.*)(?=\',)/', $data, $any);
$url=$_SERVER['SERVER_NAME'];
?>
<!DOCTYPE html>
<html>
<head>
<script type="text/javascript" src="http://code.jquery.com/jquery-1.11.1.min.js"></script>
	<title></title>
</head>
<body>

</body>
</html>
GET:<select class="sel">
      <option>--Select Url--</option>
   <?php
    foreach ($get[1] as $key => $value) {
       echo '<option>'.$value.'</option>';
    }
   ?>
</select>
POST:<select class="sel">
  <option>--Select Url--</option>
   <?php
    foreach ($post[1] as $key => $value) {
       echo '<option>'.$value.'</option>';
    }
   ?>
</select>
ANY:<select class="sel">
  <option>--Select Url--</option>
   <?php
    foreach ($any[1] as $key => $value) {
       echo '<option>'.$value.'</option>';
    }
   ?>
</select><br><br>
URL:<br> <input type="text" id="url"><br>
DATA:<br><textarea id="data" rows="5" cols="50"></textarea><br>

<input type="button" value="get" id="get">
<input type="button" value="post" id="post">
<br>RESULT:<br>
<textarea id="result" rows="50" cols="100"></textarea>

<script >
   $(".sel").change(function(){
      $("#url").val('<?=$url?>'+$(this).val());
   });
	$("#get").click(function(){
      $("#result").val('');
		var d=$("#data").val();

		var u=$("#url").val();
		$.ajax({
         url: 'http://'+u,
         data: d,
         type: "GET",
         success: function(data) { 

         	$("#result").val(data); }
      });
	});


	$("#post").click(function(){
		$("#result").val('');
      var d=$("#data").val();

		var u=$("#url").val();
		$.ajax({
         url: 'http://'+u,
         data: d,
         type: "POST",
         success: function(data) { 
         	$("#result").val(data); }
         
      });
	});
</script>