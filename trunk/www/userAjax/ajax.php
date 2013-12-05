<?php
	$conRehber = mysqli_connect("localhost","root","","rehber");
	if (mysqli_connect_errno($conRehber)) {
		die('Could not connect: ' . mysql_error());
	}
	$action = $_POST['action'];

	/*
	 * *********** Get Branchs
	 */
	if ($action == 'getBranch')
	{
		$companyId = $_POST['frmCompanyID'];
		$editUrl = $_POST['frmEdit'];
		$isOwner = $_POST['frmOwner'];
		
		$query = "
				SELECT b.*, c.title AS city, v.title AS village, r.title AS region
				FROM branch AS b
				LEFT JOIN city AS c 
					ON c.city_id = b.city_id
				LEFT JOIN region AS r 
					ON r.region_id = b.region_id
				LEFT JOIN village AS v 
					ON v.village_id = b.village_id
				WHERE b.company_id = $companyId
			";
		$result = mysqli_query($conRehber, $query);

		$data = '';
		while ($row = mysqli_fetch_object($result))
		{
			$row->telephone_1 = substr_replace(substr_replace(substr_replace($row->telephone_1, ' ', 8, 0), ' ', 6, 0), ' ', 3, 0) ;
			$row->telephone_2 = substr_replace(substr_replace(substr_replace($row->telephone_2, ' ', 8, 0), ' ', 6, 0), ' ', 3, 0) ;
			$row->fax = substr_replace(substr_replace(substr_replace($row->fax, ' ', 8, 0), ' ', 6, 0), ' ', 3, 0) ;
			$row->mobile = substr_replace(substr_replace(substr_replace($row->mobile, ' ', 8, 0), ' ', 6, 0), ' ', 3, 0) ;
			$data .= '
				<h5>' . $row->name . '</h5>
				<dl style="overflow:hidden;">
					<dt class="col-lg-5">Address</dt>
			        <dd class="col-lg-5">' . $row->addr . '&nbsp;,&nbsp;' . $row->region . '&nbsp;/&nbsp;' . $row->city . '</dd>
					<dt class="col-lg-5">Village</dt>
			        <dd class="col-lg-5">' . $row->village . '</dd>
					<dt class="col-lg-5">Contact Person</dt>
			        <dd class="col-lg-5">' . $row->contact_person_1 . '</dd>
					<dt class="col-lg-5">Contact Person 2</dt>
			        <dd class="col-lg-5">' . $row->contact_person_2 . '</dd>
			        <dt class="col-lg-5">Telephone</dt>
			        <dd class="col-lg-5">0&nbsp;' . $row->telephone_1 . '&nbsp;</dd>
					<dt class="col-lg-5">Telephone 2</dt>
			        <dd class="col-lg-5">0&nbsp;' . $row->telephone_2 . '&nbsp;</dd>
					<dt class="col-lg-5">Fax</dt>
			        <dd class="col-lg-5">0&nbsp;' . $row->fax . '&nbsp;</dd>
			        <dt class="col-lg-5">Mobile</dt>
			        <dd class="col-lg-5">0&nbsp;' . $row->mobile . '&nbsp;</dd>
			    </dl>';
			if ($isOwner == 'true')
				$data .= '
			    	<div class="branchEditBtn"><a class="greyBtn" href="' . $editUrl . 'frmBranchID/' . $row->branch_id . '">Edit Branch</a></div>
				';
		}
		echo $data;
	}
	
	/*
	 * *********** Get For Address
	 */
	if ($action == 'getAddr'){
		$optId = $_POST['frmOpt'];
	    $title = $_POST['frmTitle'];
	    $table = $_POST['frmTable'];
	    
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
	   	$select = '<label class="col-lg-5" for="' . $table . '_' . $title . '-id">' . $upTitle . '</label>';
	   	$select .= '<label class="col-lg-1" for="' . $table . '_' . $title . '-id">:</label>';
	   	$select .= '<select class="validate[required] ' . $title . 'Select" data="' . $table . '" name="' . $table . '[' . $title . '_id]" id="' . $table . '_' . $title . '-id">
	   		<option>Select ' . $upTitle . '</option>';
	   	foreach ($aOpts as $key => $value)
	   	{
	   		$select .= '<option value="' . $key . '">' . $value . '</option>';
	   	}
	   	$select .= '</select>';
	   	echo $select;
	}
   	
   	mysqli_close($conRhpr);
?>