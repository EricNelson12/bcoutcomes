<?php
  $db = new PDO("mysql:host=127.0.0.1;dbname=breastdata2", "breastuser", "breastpassword");
  $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

  //Grab the user data.
  $stmt = $db->prepare('SELECT id, br_dx_date, pat_status, death_date FROM data');
  $stmt->execute();

  $patients = $stmt->fetchAll();

  foreach ($patients as $patient)
  {
  	//Check to see if the patient is dead.
  	if ($patient['pat_status'] == 'D')
  	{
  		//Subtract the diagnosis date from the death date.
  		$diagnosisDate = new DateTime($patient['br_dx_date']);
  		$deathDate = new DateTime($patient['death_date']);

  		$diff = $deathDate->diff($diagnosisDate);

  		//Update database.
  		$stmt = $db->prepare('UPDATE data SET death_years = '.
  			($diff->days * 0.00273791).' WHERE id = '.$patient['id']);
  		$stmt->execute();

  		echo $patient['id']."\n";
  	}
  	else
  	{
  		//Update database.
  		$stmt = $db->prepare('UPDATE data SET death_years = 10000 WHERE id = '.$patient['id']);
  		$stmt->execute();
  	}
  }
?>