<?php
$fh=fopen('index.php', 'r');
//$route=require_once 'route.php';
//$output=require_once 'keys.php';
$data=fread($fh, filesize('index.php'));
preg_match_all('/(?<=post\(\')(.*)(?=\',)/', $data, $post);
preg_match_all('/(?<=get\(\')(.*)(?=\',)/', $data, $get);
preg_match_all('/(?<=any\(\')(.*)(?=\',)/', $data, $any);
preg_match_all('/(?<=put\(\')(.*)(?=\',)/', $data, $put);
preg_match_all('/(?<=delete\(\')(.*)(?=\',)/', $data, $delete);
?>
<!DOCTYPE html>
<html>
<head>
<meta content="text/html;charset=utf-8" http-equiv="Content-Type">
<meta content="utf-8" http-equiv="encoding">
<link rel="shortcut icon" href="/favicon.png" type="image/x-icon">
<script type="text/javascript" src="http://code.jquery.com/jquery-1.11.1.min.js"></script>
<title>Yahavi - API</title>
<style>
table, th, td {
  border: 1px solid black;
  border-collapse: collapse;
}
table{
  width: 80%
}
code{
  color:red;
}
</style>
</head>
<body>

</body>
</html>
GET:<select class="sel" data-type="GET">
      <option>--Select Url--</option>
   <?php
    foreach ($get[1] as $key => $value) {
       echo '<option>'.$value.'</option>';
    }
   ?>
</select>
POST:<select class="sel" data-type="POST">
  <option>--Select Url--</option>
   <?php
    foreach ($post[1] as $key => $value) {
       echo '<option>'.$value.'</option>';
    }
   ?>
</select>
PUT:<select class="sel" data-type="PUT">
  <option>--Select Url--</option>
   <?php
    foreach ($put[1] as $key => $value) {
       echo '<option>'.$value.'</option>';
    }
   ?>
</select>
DELETE:<select class="sel" data-type="DELETE">
  <option>--Select Url--</option>
   <?php
    foreach ($delete[1] as $key => $value) {
       echo '<option>'.$value.'</option>';
    }
   ?>
</select>


ANY:<select class="sel" data-type="ANY">
  <option>--Select Url--</option>
   <?php
    foreach ($any[1] as $key => $value) {
       echo '<option>'.$value.'</option>';
    }
   ?>
</select><br><br>

URL:<br> <input type="text" id="url" style="width:1000px;height:30px;"><br>
DATA:<br><textarea id="data" rows="5" cols="50"></textarea><br>

<input type="button" value="get" id="get">
<input type="button" value="post" id="post">
<input type="button" value="put" id="put">
<input type="button" value="delete" id="delete">
<br>RESULT:<br>
<textarea id="result" rows="20" cols="100"></textarea>
<button style="display:none"  id="add">Add</button>
<textarea  style="display:none" id="final" rows="10" cols="100"></textarea>
<table style="display:none">
  <tr>
    <th style="width:25%">Output</th>
    <th>Description</th> 
  </tr>
  <?php 
  foreach ($output as $key => $value) {?>
    <tr>
      <td><?=$key?></td>
      <td><?=$value?></td>
    </tr>
  <?php }?>
</table>
<script type="text/javascript">
   $(".sel").on("change , keyup",function(){
      $('#result').val('');
      $("#url").val(<?='\''.$_SERVER['SERVER_NAME'].'\''?>+$(this).val());
      var a=$(this).attr('data-type')+' '+$(this).val();
      $('[data-id]').hide();
      $('[data-id="'+a+'"]').show();
   });
  $("#get").click(function(){
      $("#result").val('');

      var d=$("#data").val();
      <?php if(!empty($_COOKIE['access_token'])){?>
         d=d+'&access_token=<?=$_COOKIE["access_token"]?>';
      <?php } ?>
    var u=$("#url").val();
    $.ajax({
         url: 'http://'+u,
         data: d,
         type: "GET",
         success: function(data) { 
          $("#result").val(data);
          return false;
          data=$.parseJSON(data);
          data.url='http://'+u;
          $("#result").val(JSON.stringify(data)); }
      });
  });


  $("#post").click(function(){
    $("#result").val('');
     var d=$("#data").val();
     <?php if(!empty($_COOKIE['access_token'])){?>
         d=d+'&access_token=<?=$_COOKIE["access_token"]?>';
      <?php } ?>
    var u=$("#url").val();
    $.ajax({
         url: 'http://'+u,
         data: d,
         type: "POST",
         success: function(data) { 
          $("#result").val(data); } 
      });
  });
  $("#put").click(function(){
    $("#result").val('');
     var d=$("#data").val();
     <?php if(!empty($_COOKIE['access_token'])){?>
        d=d+'&access_token=<?=$_COOKIE["access_token"]?>';
      <?php } ?>
    var u=$("#url").val();
    $.ajax({
         url: 'http://'+u,
         data: d,
         type: "PUT",
         success: function(data) { 
          $("#result").val(data); }
      });
  });
  $("#delete").click(function(){
    $("#result").val('');
     var d=$("#data").val();
     <?php if(!empty($_COOKIE['access_token'])){?>
         d=d+'&access_token=<?=$_COOKIE["access_token"]?>';
      <?php } ?>
    var u=$("#url").val();
    $.ajax({
         url: 'http://'+u,
         data: d,
         type: "delete",
         success: function(data) { 
          $("#result").val(data); }
      });
  });
</script>