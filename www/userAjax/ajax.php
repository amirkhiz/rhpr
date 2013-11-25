<?php
	$conRehber = mysqli_connect("localhost","root","","rehber");
	if (mysqli_connect_errno($conRehber)) {
		die('Could not connect: ' . mysql_error());
	}

	$optId = $_POST['frmOpt'];
    $title = $_POST['frmTitle'];
    
    switch ($title)
    {
    	case 'region':
    		$whereField = 'city_id';
    		break;
    	case 'village':
    		$whereField = 'region_id';
    		break;
    }
    
    $query = "
    		SELECT *
    		FROM $title
    		WHERE status = 1
    		AND $whereField = $optId
    	";
    $result = mysqli_query($conRehber, $query);
    
    $aOpts = array();
    while($row = mysqli_fetch_array($result))
    {
    	$key = $title . '_id';
    	$aOpts[$row[$key]] = $row['title'];
    	
    }
    
   	$upTitle = ucfirst($title);
   	$select = '<label class="col-lg-5" for="user_' . $title . '-id">' . $upTitle . '</label>';
   	$select .= '<label class="col-lg-1" for="user_' . $title . '-id">:</label>';
   	$select .= '<select class="validate[required]" name="user[' . $title . '_id]" id="user_' . $title . '-id">
   		<option>Select ' . $upTitle . '</option>';
   	foreach ($aOpts as $key => $value)
   	{
   		$select .= '<option value="' . $key . '">' . $value . '</option>';
   	}
   	$select .= '</select>';
   	echo $select;
   	
   	mysqli_close($conRhpr);
?>