<?php
	function kaplanMeier($step, $years, $data, $dxData, $dataParam, $debug = false)
	{
		$results = array();

		$step = 1 / $step;
		$years = ($years / $step) + 1;
		$population = count($data);
		$totalCount = $population;
		$track = 0;
		$lastSeen = 0;

		$year = 0;
		$censored = 0;

		$censoredDate = new DateTime('2012-12-31');
		$censoredData = array();


		$index = 0;
		foreach ($data as &$p)
		{
			//Figure out the number of days until censored.
			$dxDate = new DateTime($p['br_dx_date']);
			$p['censored'] = false;
			$p['dead'] = false;

			$interval = $censoredDate->diff($dxDate);

			//Only censor someone if they don't die in our timeframe.
			//if (($p['death_years'] * 365) > $years)
			//{
				if (!isset($censoredData[$interval->days]))
					$censoredData[$interval->days] = array();

				//$censoredData[$interval->days][] = array();
				//$censoredData[$interval->days]['id'] = $p['id'];
				//$censoredData[$interval->days]['index'] = $index;
				$censoredData[$interval->days][] = $index;
			//}
			$index++;
		}

		$totalCensored = 0;
		for ($i = 0; $i < $years; $i++)
		{
			$dieOff = 0;

			//Decrement population by censoring.
			if (isset($censoredData[$i]))
			{
				for ($c = 0; $c < count($censoredData[$i]); $c++)
				{
					if ($data[$censoredData[$i][$c]]['dead'] == false)
					{
						$data[$censoredData[$i][$c]]['censored'] = true;
						$population--;
						$totalCensored++;
					}
				}
			}

		    while ($lastSeen <= $year && $track < $totalCount)
		    {
		      	$lastSeen = $data[$track][$dataParam];

		      	if ($lastSeen >= $year)
		        	break;

		        //$dxDate = new DateTime($data[$track]['br_dx_date']);
		        //$interval = $censoredDate->diff($dxDate);
		        //if ($interval->days > $year)
		        //{
	      		if ($lastSeen > 0 && $data[$track]['censored'] == false) 
	      		{
	      			$dieOff++;
	      			$data[$track]['dead'] = true;
	      		}
		      	//}

		      	$track++;
		    }

		    if ($totalCount != 0)
		    {
		    	if ($population > 0)
		    		$survival = (($population - $dieOff) / $population);
		    	else
		    		$survival = 0;
		    }
		    else
		    {
		    	$survival = 0;
		    }

		    $population -= $dieOff;

		    $results[(string)$year] = $survival;

		    $year = $year + $step;

		    //echo $population." - ".$dieOff."<br>";

		    //if ($debug == true)
		    	//print $population."<br>";
		}

		//if ($debug == true)
			//echo 'Total censored: '.$totalCensored."<br>";


		//Survival for each time period is the product of all previous time periods.
		$val = array_values($results);
		$i = 0;
		foreach($results as $key => $value)
		{
			if ($i > 0)
			{
				$value = $val[$i - 1] * $val[$i];
				$results[$key] = $value;
				$val[$i] = $value;
			}
			$i++;
		}

		foreach ($results as $key => $value)
			$results[$key] = $value * 100;

		return array($population, $results);
	}


	function joinKaplanData($data, $names)
	{
		$joined = array();

		for ($i = 0; $i < count($data); $i++)
		{
			$dataSet = $data[$i];
			$dataSetName = $names[$i];

			foreach($dataSet as $year => $survival)
			{
				if (!isset($joined[(string)$year]))
				{
					$val = array();
					foreach($names as $name)
						$val[$name] = 0;

					$joined[(string)$year] = $val;
				}

				$joined[(string)$year][$dataSetName] = $survival;
			}
		}

		return $joined;
	}

	function kaplanMeierLogRank($years, $data, $dataParam)
	{
		$results = array();

		$population = count($data);
		$totalCount = $population;
		$track = 0;
		$lastSeen = 0;

		$year = 0;

		for ($i = 0; $i < $years; $i++)
		{
			$results[$year]['N'] = $population;

			$dieOff = 0;
		    while ($lastSeen <= $year && $track < $totalCount)
		    {
		      	$lastSeen = $data[$track][$dataParam];

		      	if ($lastSeen >= $year)
		        	break;
		      
		      	if ($lastSeen > 0) $dieOff++;

		      	$track++;
		    }

		    $population -= $dieOff;

		    $results[$year]['O'] = $dieOff;

		    $year++;
		}

		return $results;
	}


	//See: http://en.wikipedia.org/wiki/Log-rank_test
	function logRankTest($logr1, $logr2, $years)
	{
		$sumVj = 0;
		$sumNum = 0;

		for ($i = 1; $i <= $years; $i++)
		{
			$n1 = isset($logr1[$i]['N']) ? $logr1[$i]['N'] : 0;
			$n2 = isset($logr2[$i]['N']) ? $logr2[$i]['N'] : 0;

			$o1 = isset($logr1[$i]['O']) ? $logr1[$i]['O'] : 0;
			$o2 = isset($logr2[$i]['O']) ? $logr2[$i]['O'] : 0;

			$nj = $n1 + $n2;

			$oj = $o1 + $o2;
			$vj = ($nj - 1 == 0) ? 0 :  //Must handle div by zero.
				($oj * ($nj == 0 ? 0 : ($n1 / $nj)) * ($nj == 0 ? 1 : (1 - $n1 / $nj)) * ($nj - $oj)) / ($nj - 1);

			$sumVj += $vj;

			$e1j = ($nj == 0 ? 0 : ($oj / $nj)) * $n1;

			$sumNum += ($o1 - $e1j);
		}

		//Compute Z.
		return (abs($sumNum) + 0.000000001) / (sqrt(abs($sumVj)) + 0.000000001);
	}


	//http://stackoverflow.com/questions/20778878/php-two-tailed-z-score-to-p-probability-calculator
	function erf($x)
	{
	    $pi = 3.1415927;
	    $a = (8*($pi - 3))/(3*$pi*(4 - $pi));
	    $x2 = $x * $x;

	    $ax2 = $a * $x2;
	    $num = (4/$pi) + $ax2;
	    $denom = 1 + $ax2;

	    $inner = (-$x2)*$num/$denom;
	    $erf2 = 1 - exp($inner);

	    return sqrt($erf2);
	}

	function cdf($n)
	{
	    return (1 - erf($n / sqrt(2)))/2;
	}

	function cdf_2tail($n)
	{
	    return 2*cdf($n); //2x the CDF.
	}
?>