<?php

	require 'rb.php';
	require 'config.php';
	$stickers = R::getAll( 'SELECT * FROM stickers' );

?>
<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<title>Document</title>
	<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.4.1/jquery.min.js"></script>
	<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous">
</head>
<body>
	<br><br>
	<div class="container">
		<div  style="display: inline-block;">
			<form action="">
				<input type="text" style="width: 400px;" id='stickerName' placeholder='stickerName'>
				<input type="text" id='stickerCount' placeholder='count'>
			</form>

		</div>
		<div  style="display: inline-block;">
			<input type="submit" value="insert" class="button">
		</div>

	</div>
	<br><br>
	<div class="container">
		<table class="table">
  <thead>
    <tr>
      <th scope="col">#</th>
      <th scope="col">Name</th>
      <th scope="col">Count</th>
      <th scope="col"></th>
    </tr>
  </thead>
  <tbody>
  	<?php
		$i = 1;
		foreach ($stickers as $key => $value) {
			echo '<tr>' .
			'<th scope="row">' . $value['id'] . '</th>' .
			'<td>' . str_replace('%20', ' ', $value['name']) .
			'<td>' . $value['count'] .'</td>' .
			'<td>' . '<input type="submit" value="x" class="del_button" ' . 'id = "' . $value['id'] . '">' . '</td>' .
			'</tr>';
			$i++;
		}
	?>
  </tbody>
</table>
	</div>
<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js" integrity="sha384-UO2eT0CpHqdSJQ6hJty5KVphtPhzWj9WO1clHTMGa3JDZwrnQq4sF86dIHNDz0W1" crossorigin="anonymous"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js" integrity="sha384-JjSmVgyd0p3pXB1rRibZUAYoIIy6OrQ6VrjIEaFf/nJGzIxFDsf4x0xIM+B07jRM" crossorigin="anonymous"></script>

<script>
	$(function() {
      $(".button").click(function() {

        //name
        var name = $("#stickerName").val();

        //description
        var count = $("#stickerCount").val();

        $.ajax({
	        type: "POST",
	        url: "for_db.php",
	        data: {name:name, count:count},
	        success: function(){
	        	location.reload()
	        }
	    });
    });
  });


    $(function() {
      $(".del_button").click(function() {
        id = this.getAttribute('id');
        $.ajax({
          type: "POST",
          url: "del_bean.php",
          data: {id:id},
          success: function(){
            location.reload()
          }
	        });
	    });
	});
</script>


</body>
</html>