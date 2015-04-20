<!Doctype HTML>

<head>
  	

<script type="text/javascript" src="https://www.google.com/jsapi?autoload={'modules':[{'name':'visualization','version':'1.1','packages':['line', 'corechart']}]}"></script>
 	<script src="jquery/jquery-1.8.3.js"></script>
  <script src="jquery/ui/jquery.ui.core.js"></script>
  <script src="jquery/ui/jquery.ui.widget.js"></script>
  <script src="jquery/ui/jquery.ui.tabs.js"></script>
  <script src="utilities.js"></script>
  <script src="d.js"></script>


  <link rel="stylesheet" href="jquery/demos.css">
  

  <?php

  //to do: Hovering over attribute filter shows it's summary

  require_once("kaplanMeier.php");

  $dictionary = json_decode(file_get_contents("dictionary.json"));
  $db = new PDO("mysql:host=10.7.201.60;dbname=breastdata2", "breastuser", "YES");
  $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);



  $isNotFirstQuery = isset($_POST['is_not_first_query']) ? true : false;
  if(isset($mypost))
    $_POST = $mypost;
  else
   {
   if($isNotFirstQuery==true)
    {
      storeQuery();
    }
  }

  //Grab params.
  $cohort1Params = parseParams("cohort1-");
  $cohort2Params = parseParams("cohort2-");

  
  $cohort1 = queryData($cohort1Params, 'death_years');
  $cohort1dx = queryData($cohort1Params, 'br_dx_date');

  $cohort2 = queryData($cohort2Params, 'death_years');
  $cohort2dx = queryData($cohort2Params, 'br_dx_date');

  //Logrank.
  $cohort1LogDat = kaplanMeierLogRank(10, $cohort1, 'death_years');
  $cohort2LogDat = kaplanMeierLogRank(10, $cohort2, 'death_years');

  $logRank = logRankTest($cohort1LogDat, $cohort2LogDat, 10);


  //Survival.
  $cohort1Survival = kaplanMeier(365, 10, $cohort1, $cohort1dx, 'death_years', true);
  $cohort2Survival = kaplanMeier(365, 10, $cohort2, $cohort2dx, 'death_years', false);

  $nameCohort1 = 'Cohort 1 ('.$cohort1Survival[0].' / '.count($cohort1).')';
  $nameCohort2 = 'Cohort 2 ('.$cohort2Survival[0].' / '.count($cohort2).')';
  //$nameCohort2 = $nameCohort1;

  $dataToJoin = array($cohort1Survival[1], $cohort2Survival[1]);

  $names = array('cohort 1', 'cohort 2');

  $joinedData = joinKaplanData($dataToJoin, $names);

  $tsv = fopen('data.tsv', 'w');
  
  
  foreach ($joinedData as $year => $t_data)
  {
    fwrite($tsv, sprintf('%0.2f', $year).", ".sprintf('%0.2f', $t_data['cohort 1']).", ".sprintf('%0.2f', $t_data['cohort 2']));
    fwrite($tsv, "\n");
  }

  fclose($tsv);

//for some reason, it will only be able to get read if I make a copy...
  $file = 'data.tsv';
  $newfile = 'data2.tsv';

  if (!copy($file, $newfile)) {
      echo "failed to copy $file...\n";
  }

  // now for recurrence
  $cohort1rec = queryData($cohort1Params, 'srm_years');
 // $cohort1dx = queryData($cohort1Params, 'br_dx_date');

  $cohort2rec = queryData($cohort2Params, 'srm_years');
 // $cohort2dx = queryData($cohort2Params, 'br_dx_date');

  //Logrank.
  $cohort1LogDatrec = kaplanMeierLogRank(10, $cohort1rec, 'srm_years');
  $cohort2LogDatrec = kaplanMeierLogRank(10, $cohort2rec, 'srm_years');

  $logRankrec = logRankTest($cohort1LogDatrec, $cohort2LogDatrec, 10);


  //Survival.
  $cohort1Recurrence = kaplanMeier(365, 10, $cohort1rec, $cohort1dx, 'srm_years', true);
  $cohort2Recurrence = kaplanMeier(365, 10, $cohort2rec, $cohort2dx, 'srm_years', false);

  $nameCohort1 = 'Cohort 1 ('.$cohort1Survival[0].' / '.count($cohort1).')';
  $nameCohort2 = 'Cohort 2 ('.$cohort2Survival[0].' / '.count($cohort2).')';
  //$nameCohort2 = $nameCohort1;

  $dataToJoinrec = array($cohort1Recurrence[1], $cohort2Recurrence[1]);

  $names = array('cohort 1', 'cohort 2');

  $joinedData = joinKaplanData($dataToJoinrec, $names);

  $tsv = fopen('data.tsv', 'w');
  //fwrite($tsv, "year\tclose\topen");
  //fwrite($tsv, "\n");
  
  foreach ($joinedData as $year => $t_data)
  {
    fwrite($tsv, sprintf('%0.2f', $year).", ".sprintf('%0.2f', (100-$t_data['cohort 1'])).", ".sprintf('%0.2f', (100-$t_data['cohort 2'])));
    fwrite($tsv, "\n");
  }

  fclose($tsv);

//for some reason, it will only be able to get read if I make a copy...
  $file = 'data.tsv';
  $newfile = 'data3.tsv';

  if (!copy($file, $newfile)) {
      echo "failed to copy $file...\n";
  }


  getData();

  function getData()
  {
    echo ' 
    <script>
    var data2 = new google.visualization.DataTable();
      data2.addColumn(\'number\', "Year");
      data2.addColumn(\'number\', "Cohort 1");
      data2.addColumn(\'number\', "Cohort 2");

      data2.addRows([';

        $handle = fopen("data3.tsv", "r");
          if ($handle) {
              while (($line = fgets($handle)) !== false) {
                  echo '['.$line.'],';
              }

              fclose($handle);
          } else {
              // error opening the file.
          } 
          
        
      echo ']);

      var data1 = new google.visualization.DataTable();
          data1.addColumn(\'number\', "Year");
          data1.addColumn(\'number\', "Cohort 1");
          data1.addColumn(\'number\', "Cohort 2");

          data1.addRows([';

        $handle = fopen("data2.tsv", "r");
          if ($handle) {
              while (($line = fgets($handle)) !== false) {
                  echo '['.$line.'],';
              }

              fclose($handle);
          } else {
              // error opening the file.
          } 
          
        
      echo ']);
      </script>';
  }

  ?>
	
	{{ HTML::style('assets/style.css') }}
	

  

  	<title>Breast Cancer Outcomes</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    {{HTML::style('http://maxcdn.bootstrapcdn.com/bootstrap/3.1.1/css/bootstrap.min.css')}}
    <style>
        body{
            padding-top: 70px;
        }
    </style>
</head>
<body>
<div class="page">
    <div class="container-fluid">
        <nav class="navbar navbar-default navbar-fixed-top" role="navigation">
            <div class="container">
                <div class="navbar-header">
                    <button type="button" class="navbar-toggle" data-toggle="collapse" data-target="#bs-example-navbar-collapse-1">
                        <span class="sr-only">Toggle navigation</span>
                        <span class="icon-bar"></span>
                        <span class="icon-bar"></span>
                        <span class="icon-bar"></span>
                    </button>
                    <a class="navbar-brand" href="/laravel/outcomes/public/">Breast Cancer Outcomes</a>
                </div>

                <div class="collapse navbar-collapse" id="bs-example-navbar-collapse-1">
                    <ul class="nav navbar-nav navbar-right">
                        @if (Auth::check())
                        <li><a style = "cursor:pointer;" onclick="show_tooltip(3);">Tutorial</a></li>
                        <li><a href="history">History</a></li>
                        <li><a href="logout">Log Out</a></li>
                        <li><a href="profile">{{ Auth::user()->email }}</a></li>
                        @else
                        <li><a href="/laravel/outcomes/public/login">Login</a></li>
                        <li><a href="/laravel/outcomes/public/user/create">Register</a></li>
                        @endif
                    </ul>

                </div><!-- /.navbar-collapse -->
            </div>
        </nav>
    </div>
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-4 col-md-offset-4">
                @if(Session::has('message'))
                <div class="alert-box success">
                    <h2>{{ Session::get('message') }}</h2>
                </div>
                @endif
            </div>
        </div>
    </div>
    @yield('body')
</div>

{{HTML::script('http://maxcdn.bootstrapcdn.com/bootstrap/3.1.1/js/bootstrap.min.js')}}
@show
<!--  
  Begin Attribute Collection tabbed panel, an implementation of the JQuery Easy Tabs plugin (open source)
-->  

<form role="form" id = "myform" method="POST" action="index.php">
<!-- start of visualizations-->
  <div id = "cohort_builder">
    <div id="tabs" class='tabbable'>
     <ul class='nav nav-tabs'>
      <!-- This will choose the correct attribute tree based on user oncology -->
      
     
     
      <li class = 'active' id='alltab'> <a href="#All" data-toggle="tab">Data Attributes</a></li>
      <!--
      <li id='surgerytab'><a href="#Surgical" data-toggle="tab">Surgical Oncology</a></li>
      <li id='radiationtab'><a href="#Radiation" data-toggle="tab">Radiation Oncology</a></li>
      <li id='medicaltab'><a href="#Medical" data-toggle="tab">Medical Oncology</a></li>
      -->
      <li class="queryButton" onClick="mySubmit();">Query</li>
      <li style = "margin-right:10px;">&nbsp;</li>
      <li onClick="window.location = '';" class="resetButton">Reset</li>
      <li><input type="text" name = "query_name" id = "query_name" maxlength="65" placeholder="Query Name" style ="margin-left:10px;"></li>
     </ul>


    <img src="images/help.png" alt="alternative text" title="The Cohort Filters show attributes you've set values for and filtered your cohort's with. Any attribute not in the Cohort Filters will not be used to filter your cohorts." style = "width:10px;height:10px;cursor:pointer;" onclick="show_tooltip(1);"/>
     <div class='tab-content'>

      <div class="tab-pane active" id="All">
        
        <div id ="All-all">
          <div id="age"class = "attr" href="#" data-showpopup="Age">Age</div>
          <div id="behaviour"class = "attr"href="#" data-showpopup="Behaviour">Behaviour</div>
          <div id="diagnosis_date"class = "attr" href="#" data-showpopup="Diagnosis_Date">Diagnosis Date</div>
          <div id="grade"class = "attr"href="#" data-showpopup="Grade">Grade</div>
          <div id="site"class = "attr" href="#" data-showpopup="Site">Site</div>          
        </div>
        <div id = "All-surgery">
          <div id="estrogen_receptor"class = "attrsurgery"  href="#" data-showpopup="Immuno_Stains">Estrogen Receptor</div>
          <div id="her2"class = "attrsurgery"  href="#" data-showpopup="Her2">Her2</div>
          <div id="histology"class = "attrsurgery"  href="#" data-showpopup="Hist">Histology</div>
          <div id="menopause"class = "attrsurgery" href="#" data-showpopup="Meno_Status">Menopause</div>
          <div id="nodes"class = "attrsurgery"  href="#" data-showpopup="Nodes">Nodes</div>
        
          <div id="progesterone_receptor"class = "attrsurgery"  href="#" data-showpopup="PGR">Progesterone Receptor</div>
          

        </div>
        <div>
          <div id="tstaging"class = "attrsurgery" href="#" data-showpopup="T_Staging">T Staging</div>
          <div id="mstaging"class = "attrsurgery" href="#" data-showpopup="N_Staging">N Staging</div>
          <div id="nstaging"class = "attrsurgery" href="#" data-showpopup="M_Staging">M Staging</div>
          
        </div>
        
        <div id = "All-medical">
         
          <div id="radiation"class = "attrmedical" href="#" data-showpopup="Radiation">Radiation</div>
          <div id="chemo"class = "attrmedical"  href="#" data-showpopup="Chemo">Chemo</div>
           <div id="surgery"class = "attrmedical" href="#" data-showpopup="Surgery">Surgery</div>
           <div id="recurrence"class = "attrmedical" href="#" data-showpopup="SRM_Date">Recurrence</div>
         
        </div>

      </div>
<!--
      <div class="tab-pane"  id="Surgical" collapsible=true>
        <div id ="All-all">
          <div id="age"class = "attr" href="#" data-showpopup="Age">Age</div>
          <div id="site"class = "attr" href="#" data-showpopup="Site">Site</div>
          <div id="diagnosis_date"class = "attr" href="#" data-showpopup="Diagnosis_Date">Diagnosis Date</div>
          <div id="grade"class = "attr"href="#" data-showpopup="Grade">Grade</div>
          <div id="behaviour"class = "attr"href="#" data-showpopup="Behaviour">Behaviour</div>
        </div>
        <div id = "All-surgery">
          <div id="recurrence"class = "attrsurgery" href="#" data-showpopup="SRM_Date">Recurrence</div>
          <div id="tstaging"class = "attrsurgery" href="#" data-showpopup="TNM_Staging">TNM Staging</div>
          <div id="surgery"class = "attrsurgery" href="#" data-showpopup="Surgery">Surgery</div>

        </div>
      </div>

      <div class="tab-pane" id="Radiation">
         <div id ="All-all">
          <div id="age"class = "attr" href="#" data-showpopup="Age">Age</div>
          <div id="site"class = "attr" href="#" data-showpopup="Site">Site</div>
          <div id="diagnosis_date"class = "attr" href="#" data-showpopup="Diagnosis_Date">Diagnosis Date</div>
          <div id="grade"class = "attr"href="#" data-showpopup="Grade">Grade</div>
          <div id="behaviour"class = "attr"href="#" data-showpopup="Behaviour">Behaviour</div>
        </div>
       
        <div id = "All-radiation">
          <div id="nodes"class = "attrradiation"  href="#" data-showpopup="Nodes">Nodes</div>
          <div id="histology"class = "attrradiation"  href="#" data-showpopup="Hist">Histology</div>
          <div id="progesterone_receptor"class = "attrradiation"  href="#" data-showpopup="PGR">Progesterone Receptor</div>
          <div id="radiation"class = "attrradiation" href="#" data-showpopup="Radiation">Radiation</div>

        </div>
        
      </div>

      <div class="tab-pane" id="Medical">
         <div id ="All-all">
          <div id="age"class = "attr" href="#" data-showpopup="Age">Age</div>
          <div id="site"class = "attr" href="#" data-showpopup="Site">Site</div>
          <div id="diagnosis_date"class = "attr" href="#" data-showpopup="Diagnosis_Date">Diagnosis Date</div>
          <div id="grade"class = "attr"href="#" data-showpopup="Grade">Grade</div>
          <div id="behaviour"class = "attr"href="#" data-showpopup="Behaviour">Behaviour</div>
        </div>
        <div id = "All-medical">
          <div id="menopause"class = "attrmedical" href="#" data-showpopup="Meno_Status">Menopause</div>
          <div id="estrogen_receptor"class = "attrmedical"  href="#" data-showpopup="Immuno_Stains">Estrogen Receptor</div>
          <div id="her2"class = "attrmedical"  href="#" data-showpopup="Her2">Her2</div>
          <div id="chemo"class = "attrmedical"  href="#" data-showpopup="Chemo">Chemo</div>
        </div>
      </div>
    -->
     

     </div>
    </div>

   

  <!-- 
    End Attribute Collection tabbed panel
  -->

  <!-- 
    Begin Cohort Builder Tree
  -->
   
    <div id="cohort_tree">
      <p style="display:inline-block;">Filtered Attributes</p>
    <img src="images/help.png" alt="alternative text" title="The Cohort Filters show attributes you've set values for and filtered your cohort's with. Any attribute not in the Cohort Filters will not be used to filter your cohorts." style = "width:10px;height:10px;cursor:pointer;" onclick="show_tooltip(2);"/>

    </div>

  </div>

  <!-- 
  End Cohort Builder Tree
-->

  

<!-- Start of popup windows -->
<div class="overlay-bg">
</div>

<div class="overlay-content popuptooltip1">
   
    <p>To add a Cohort Filter, simply click it. This will open a window which will allow you to select that filters value(s).</p>
    
</div>

<div class="overlay-content popuptooltip2">
    <p>Filtered Attributes have set value(s) and will be used to define patient cohorts.</p>
    <p>Clicking a Filtered Attribute will allow you to change it's value(s).</p>
    <p>Any attributes not present in the Filtered Attributes will not be used to filter patient cohorts.</p>
    <p>To remove an attribute filter, right click it.</p>
    <p>To define a second cohort using an attribute, click the "Compare" button of the attribute you wish to use. This is the key attribute used when comparing two cohorts.</p> 
   
    
</div>

<div class="overlay-content popuptooltip3">
  <h3>Tutorial</h3>
    <p><b>1)</b> To define a cohort, choose which attributes you want to define your cohort with and set values for them. Do this by clicking an attribute, afterwhich a window will appear where you can set specific values for this attribute.</p>
    <p><b>2)</b> Attributes you've modified will be kept track of in the "Filtered Attributes" section. To remove an attribute filter, right click it in the "Filtered Attributes" section.</p>
    <p><b>3)</b> To define a comparison cohort, choose the attribute you wish to compare both cohorts with and click the "Compare" button. This will bring up a new section where you can specify a second set of values for the chosen attribute. This can only be done once, and you can change which attribute to compare with by clicking "Uncompare" and then selecting again.</p>
    <p><b>4)</b> Once your cohorts are defined, you can click "Query" to see the survival and reccurence information on these cohorts, located in the tab-seperated displays below.</p>
    <p><b>5)</b> Each query is saved by date and time, but you can provide a name for each query so you can access it more easily in the future, if you wish to. Your query history can be found by clicking the "History" link on the menu bar.</p>
    <p><b>6)</b> Zoom in and out of each display using the mousewheel, and attain exact values by hovering over the curve with the mouse. Clicking and dragging the diplay will allow you to reposition it.</p>
    <p><b>7)</b> To close an overlay window, such as this one, click outside of it or click the "Close" button if such is there.</p>
    <h4>Demo Video</h4>
    <iframe src="https://player.vimeo.com/video/124559851" width="500" height="281" frameborder="0" webkitallowfullscreen mozallowfullscreen allowfullscreen></iframe> <p><a href="https://vimeo.com/124559851">Breast Outcomes Dashboard Demo</a> from <a href="https://vimeo.com/user22664621">Duncan Szarmes</a> on <a href="https://vimeo.com">Vimeo</a>.</p>
</div>

  <?php
  genInputElements($cohort1Params,$cohort2Params);
  ?>
  <input type="checkbox" name="is_not_first_query" style="display: none" checked/>
  <?php 
  $filter_list = "";
  if (isset($_POST['filter_list'])) {
    $filter_list=$_POST['filter_list'];
  }
  echo '<input type = "text" name="filter_list" style="display: none" value = "'.$filter_list.'"/>';

  $duplicated_id = "";
  if (isset($_POST['duplicated_id'])) {
    $duplicated_id=$_POST['duplicated_id'];
  }
  echo '<input type = "text" name="duplicated_id" style="display: none" value = "'.$duplicated_id.'"/>';
  ?>
</form>

<!-- end of popup windows -->

<!-- start of visualizations-->
<div class = "row">
   <div class="col-md-6">
      <div class="tabbable" id="visualization" style = "width:700px;">
          <ul class="nav nav-tabs">
              <li class="active"><a class="atab" href="#a_tab" data-toggle="tab">Survival</a></li>
             
              
              <li><a class="btab" href="#b_tab" data-toggle="tab">Recurrence</a></li>
              <li disabled="true"><a class="ctab" href="#c_tab" data-toggle="tab" >Time to Treatment</a></li>
              <li disabled="true"><a class="dtab" href="#d_tab" data-toggle="tab">Treatment Cost Estimation</a></li>
          </ul>
          <div class="tab-content">
              <div class="tab-pane active" id="a_tab">
                  <h1>Kaplan-Meier Survival Estimation</h1>
                  <acontent id = "acontent"></acontent>
                  <div id = "logranksurvival">
 LogRank Chi Square: <b><?php echo number_format($logRank, 5); ?></b> &nbsp;&nbsp; 
          P-value: <b><?php $pv = cdf_2tail($logRank); if ($pv < 0.0001) echo '<0.0001'; else echo number_format($pv, 5); ?></b>
 </div>
              </div>
              <div class="tab-pane" id="b_tab">
                  <h1>Kaplan-Meier Recurrence Estimation</h1>
                  <bcontent id = "bcontent"></bcontent>
                  <div id = "logranksurvival">
LogRank Chi Square: <b><?php echo number_format($logRankrec, 5); ?></b> &nbsp;&nbsp; 
          P-value: <b><?php $pv = cdf_2tail($logRankrec); if ($pv < 0.0001) echo '<0.0001'; else echo number_format($pv, 5); ?></b>
 </div>
              </div>
              <div class="tab-pane" id="c_tab">
                  <h1>Time to Treatment Estimation</h1>
                  <ccontent></ccontent>
              </div>
               <div class="tab-pane" id="d_tab">
                  <h1>Treatment Cost Estimation</h1>
                  <dcontent id = "dcontent"></dcontent>
              </div>
          </div>
      </div>
    </div>
     <div class="col-md-6">
      <h3>Cohort Difference</h3>
      <table>
        <tr>
          <td style = "width:200px;">
            <p style = "color:steelblue;"> Cohort 1 </p>
          </td>
           <td style = "width:200px;">
            <p style = "color:#ff7f0e;"> Cohort 2 </p>
          </td>
        </tr>
          <td>
            <div id = "keyfactor1" style = "max-width:300px;max-height: 300px;overflow: auto;">
            </div>
            
          </td>
           <td >
            <div id = "keyfactor2" style = "max-width:300px;max-height: 300px;overflow: auto;">
            </div>
            
          </td>
        <tr>
        </tr>
      </table>
    </div>
</div>


<h3 style ="margin-left:10px;" title="Data may have been lost due to patient death or to incomplete patient records.">Sample Size</h3>
<table class="table table-striped table-hover" style="width:627px; margin-left:20px;">
  <tr><th>Year</th><?php for ($i=1; $i < 11; $i++) echo '<th>'.$i.'</th>';?><th>Pop.</th></tr>
  <?php genCumSurvivalRow($cohort1Survival[1], $cohort1Survival[0], "Cohort 1", "#1f77b4"); ?>
  <?php genCumSurvivalRow($cohort2Survival[1], $cohort2Survival[0], "Cohort 2", "#ff7f0e"); ?>
</table>
 

<script src="http://d3js.org/d3.v3.js"></script>




<?php

  function storeQuery()
  {

    $dbquery = new PDO("mysql:host=10.7.201.49;dbname=outcomes", "breastuser1", "YES");
    $dbquery->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $qname = "none";
    if(isset($_POST['query_name']))
    {
      if($_POST['query_name']!="")
        $qname = $_POST['query_name'];
    }

    $c1params = "";
    $c2params = "";
    foreach ($_POST as $key => $value) 
    {
       
      if (strpos($key,'cohort2') !== false) 
      {
        $c2params.=$key."=".$value."&";
      }
      else
      {
        $c1params.=$key."=".$value."&";
      }
    }

    $query = "INSERT INTO queries (user_id,cohort1params,cohort2params,date_of_query,query_name) VALUES (".Auth::user()->id.",'".$c1params."','".$c2params."','".date("Y-m-d H:i:s")."','".$qname."');";
    $stmt = $dbquery->prepare($query);
    $stmt->execute();

  }

 function parseParams($prefCohort)
  {
    $dictionary = json_decode(file_get_contents("dictionary.json"));
    $params = array();
    //global $dictionary;
    $isNotFirstQuery = isset($_POST['is_not_first_query']) ? true : false;
 

    foreach ($dictionary as $group => $subgroup)
    {

      foreach ($subgroup->elements as $element)
      {
        foreach ($element as $subelement => $spec)
        {
          switch ($spec->input)
          {
            case 'range':
              $params[$subelement.'-min'] = isset($_POST[$prefCohort.$subelement.'-min']) ?
                  $_POST[$prefCohort.$subelement.'-min'] : $spec->defaultmin;

              $params[$subelement.'-max'] = isset($_POST[$prefCohort.$subelement.'-max']) ?
                  $_POST[$prefCohort.$subelement.'-max'] : $spec->defaultmax;
              break;

            case 'select':
              foreach ($spec->values as $dbVal=>$value)
              {
                $id = $subelement.'-'.$dbVal;

                if (isset($_POST[$prefCohort.$id]))
                  $params[$id] = 'on';
                else
                {
                  if ($isNotFirstQuery == true)
                    $params[$id] = 'off';
                  else
                    $params[$id] = 'on';
                }
              }
              break;

            case 'toggle':
              $params[$subelement] = isset($_POST[$prefCohort.$subelement]) ? true : false;
              break;
          }
        }
      }
    }

    return $params;
  }

  function queryData($params, $orderByColumn)
  {
    //global $dictionary;
    $dictionary = json_decode(file_get_contents("dictionary.json"));
    //global $db;
    $db = new PDO("mysql:host=10.7.201.60;dbname=breastdata2", "breastuser", "YES");
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);


    $qCount = 0;
    $query = 'SELECT id, death_years, br_dx_date, srm_years FROM data WHERE ';
    foreach ($dictionary as $group => $subgroup)
    {
      foreach ($subgroup->elements as $element)
      {
        foreach ($element as $subelement => $spec)
        {
          if ($qCount > 0)
            $query.=' AND ';

          switch ($spec->input)
          {
            case 'range':
              //Check for toggle.
              if (!isset($params['toggle_'.$subelement]) || $params['toggle_'.$subelement] == true)
              {
                
                if ($spec->type == "year")
                {
                  $query.=$subelement.' >= "'.$params[$subelement.'-min'].'-00-00" AND '.
                    $subelement.' <= "'.$params[$subelement.'-max'].'-12-31"';
                }
                else
                {
                  $query.=$subelement.' >= '.$params[$subelement.'-min'].' AND '.
                    $subelement.' <= '.$params[$subelement.'-max'];
                }
              }
              else
              {
                $query.= "0 = 0"; //Keep query string from breaking mysql.
              }
              break;

            case 'select':
              $firstSw = true;
              foreach ($spec->values as $dbVal=>$value)
              {
                //echo "test";
                $id = $subelement.'-'.$dbVal;

                if ($params[$id] == 'on')
                {
                  if (!$firstSw)
                    $query.=" OR ";
                  else
                    $query.="(";

                  switch ($spec->type)
                  {
                    case 'string':
                      if ($dbVal == "_empty_")
                      {
                        $query.=$subelement.' IS NULL OR '.$subelement.' = ""';
                      }
                      else
                      {
                        $query.=$subelement.' LIKE "'.$dbVal.'"';
                      }
                      break;

                    case 'aggregate':
                      $query.=$subelement.' >= '.$value->min.' AND '.$subelement.
                        '<= '.$value->max;
                      break;

                    default:
                      $query.=$subelement.' = '.$dbVal;
                  }

                  $firstSw = false;
                }
              }
              if (!$firstSw)
                $query.=")";
              else
                $query.= "0 = 0"; //Keep query string from breaking mysql.
              break;

            default:
              $query.= "0 = 0"; //Keep query string from breaking mysql.
          }

          $qCount++;
        }
      }
    }
    $query.= " ORDER BY ".$orderByColumn." ASC";

    $stmt = $db->prepare($query);
    $stmt->execute();

    $data = $stmt->fetchAll();

   // echo $query."<br><br><br>";

    return $data;
  }

  function genCumSurvivalRow($survivalData, $popCount, $label, $colour)
  {
    //Parse the data and find final survival rate for each year.
    $surv = array();

    foreach($survivalData as $year=>$perc)
    {
      $dYear = intval($year) + 1;
      $surv[$dYear] = $perc;
    }

    //Write the table.
    echo '<tr><td style="color:'.$colour.'"><i><b>'.$label.'</b></i></td>';
    foreach ($surv as $year => $perc)
      echo '<td style="color:'.$colour.'">'.number_format($perc, 0).'%</td>';
    echo '<td style="color:'.$colour.'"><b>'.$popCount.'</b></td></tr>';
  }
?>
<table>
<?php 

/*
    foreach ($_POST as $key => $value) {
        echo "<tr>";
        echo "<td>";
        echo $key;
        echo "</td>";
        echo "<td>";
        echo $value;
        echo "</td>";
        echo "</tr>";

    }
    
*/

?>

<?php

//do the listview stuff for tnm here
function getTNMhtml($cohort1, $cohort2)
{
echo '<div id="overlay-TNM_Staging" class="overlay-content popupT_Staging" style="display: none; top: 20px;"><div id="overlay-T_Stagingcontent" style="float:left;padding-right:10px;">';
echo '<label id="-gsublabel-tnm_t" style="cursor: pointer; cursor: hand; font-size:1.3em; margin-top:5px; margin-left:15px;">T Staging</label>';
echo '<a href="#" class="btn btn-success btn-xs" style="margin-right:40px;"'.
                  'onclick="selectallT1(true);"'
                    .'>Select All</a>';
                echo '<a href="#" class="btn btn-danger btn-xs" '.
                    'onclick="selectallT1(false);"'
                    .'>Select None</a>';
                echo '<div style="margin-bottom:10px;"></div>';
echo  '<ul class="treeview">
        <li>
          <input type="checkbox" name="cohort1-tnm_t-_empty_" id="cohort1-tnm_t-_empty_" '.($cohort1['tnm_t-_empty_'] == "on" ? 'checked' :  "").'>
          Missing
        </li>
        <li>
          <input type="checkbox" name="cohort1-tnm_t-X" id="cohort1-tnm_t-X" '.($cohort1['tnm_t-X'] == "on" ? 'checked' :  "").'>
          Tx: primary tumour cannot be assessed
        </li>
        <li>
          <input type="checkbox" name="cohort1-tnm_t-0" id="cohort1-tnm_t-0"'.($cohort1['tnm_t-0'] == "on" ? 'checked' :  "").'>
          T0: No evidence of primary
        </li>
        <lix class = "cohort1-tnm_t-1" style = "position:absolute;">+</lix>
        
        <li class = "cohort1-tnm_t-1">
          <input type="checkbox" name="cohort1-tnm_t-1" id="cohort1-tnm_t-1" '.($cohort1['tnm_t-1'] == "on" ? 'checked' :  "").'>
          T1: <= 2cm

            <ul>
                 <li>
                     <input type="checkbox" name="cohort1-tnm_t-1A" id="cohort1-tnm_t-1A" '.($cohort1['tnm_t-1A'] == "on" ? 'checked' :  "").'>
                     T1a: >0.1cm <= 0.5cm
                 </li>
                 <li>
                     <input type="checkbox" name="cohort1-tnm_t-1B" id="cohort1-tnm_t-1B" '.($cohort1['tnm_t-1B'] == "on" ? 'checked' :  "").'>
                     T1b: >0.5cm <= 1cm
                 </li>
                 <li>
                     <input type="checkbox" name="cohort1-tnm_t-1C" id="cohort1-tnm_t-1C" '.($cohort1['tnm_t-1C'] == "on" ? 'checked' :  "").'>
                     T1c: >1cm <= 2cm
                 </li>
                 <li>
                     <input type="checkbox" name="cohort1-tnm_t-1M" id="cohort1-tnm_t-1M" '.($cohort1['tnm_t-1M'] == "on" ? 'checked' :  "").'>
                     T1micro: microinvasion <= 0.1cm
                 </li>
                
            </ul>
        </li>
        <li>
          <input type="checkbox" name="cohort1-tnm_t-2" id="cohort1-tnm_t-2" '.($cohort1['tnm_t-2'] == "on" ? 'checked' :  "").'>
          T2: >2cm <= 5cm
        </li>
        <li>
          <input type="checkbox" name="cohort1-tnm_t-3" id="cohort1-tnm_t-3" '.($cohort1['tnm_t-3'] == "on" ? 'checked' :  "").'>
          T3: >5cm
        </li>
         
        <lix class = "cohort1-tnm_t-4" style = "position:absolute;">+</lix>
    <li class = "cohort1-tnm_t-4">

          <input type="checkbox" name="cohort1-tnm_t-4" id="cohort1-tnm_t-4"'.($cohort1['tnm_t-4'] == "on" ? 'checked' :  "").'>
          T4: any size + chest or skin extension

            <ul>
                 <li>
                     <input type="checkbox" name="cohort1-tnm_t-4A" id="cohort1-tnm_t-4A" '.($cohort1['tnm_t-4A'] == "on" ? 'checked' :  "").'>
                     T4a: chest wall extension
                 </li>
                 <li>
                     <input type="checkbox" name="cohort1-tnm_t-4B" id="cohort1-tnm_t-4B" '.($cohort1['tnm_t-4B'] == "on" ? 'checked' :  "").'>
                     T4b: skin extension
                 </li>
                 <li>
                     <input type="checkbox" name="cohort1-tnm_t-4C" id="cohort1-tnm_t-4C" '.($cohort1['tnm_t-4C'] == "on" ? 'checked' :  "").'>
                     T4c: both T4a and T4b
                 </li>
                 <li>
                     <input type="checkbox" name="cohort1-tnm_t-4D" id="cohort1-tnm_t-4D" '.($cohort1['tnm_t-4D'] == "on" ? 'checked' :  "").'>
                     T4d: inflammatory carcinoma
                 </li>
                
            </ul>
        </li>
        <li>
          <input type="checkbox" name="cohort1-tnm_t-IS" id="cohort1-tnm_t-IS" '.($cohort1['tnm_t-IS'] == "on" ? 'checked' :  "").'>
          Tis: cacrinoma in situ
        </li>
       
       
    </ul>
    </div>

    <div id="overlay-T_Stagingcontent-cohort2" style="float:left;padding-right:10px; display:none;">';
echo '<label id="-gsublabel-tnm_t" style="cursor: pointer; cursor: hand; font-size:1.3em; margin-top:5px; margin-left:15px;">Comparison</label>';
echo '<a href="#" class="btn btn-success btn-xs" style="margin-right:40px;"'.
                  'onclick="selectallT2(true);"'
                    .'>Select All</a>';
                echo '<a href="#" class="btn btn-danger btn-xs" '.
                    'onclick="selectallT2(false);"'
                    .'>Select None</a>';
                echo '<div style="margin-bottom:10px;"></div>';
echo  '<ul class="treeview">
        <li>
          <input type="checkbox" name="cohort2-tnm_t-_empty_" id="cohort2-tnm_t-_empty_" '.($cohort2['tnm_t-_empty_'] == "on" ? 'checked' :  "").'>
          Missing
        </li>
        <li>
          <input type="checkbox" name="cohort2-tnm_t-X" id="cohort2-tnm_t-X" '.($cohort2['tnm_t-X'] == "on" ? 'checked' :  "").'>
          Tx: primary tumour cannot be assessed
        </li>
        <li>
          <input type="checkbox" name="cohort2-tnm_t-0" id="cohort2-tnm_t-0"'.($cohort2['tnm_t-0'] == "on" ? 'checked' :  "").'>
          T0: No evidence of primary
        </li>
        <lix class = "cohort2-tnm_t-1" style = "position:absolute;">+</lix>
        
        <li class = "cohort2-tnm_t-1">
          <input type="checkbox" name="cohort2-tnm_t-1" id="cohort2-tnm_t-1" '.($cohort2['tnm_t-1'] == "on" ? 'checked' :  "").'>
          T1: <= 2cm

            <ul>
                 <li>
                     <input type="checkbox" name="cohort2-tnm_t-1A" id="cohort2-tnm_t-1A" '.($cohort2['tnm_t-1A'] == "on" ? 'checked' :  "").'>
                     T1a: >0.1cm <= 0.5cm
                 </li>
                 <li>
                     <input type="checkbox" name="cohort2-tnm_t-1B" id="cohort2-tnm_t-1B" '.($cohort2['tnm_t-1B'] == "on" ? 'checked' :  "").'>
                     T1b: >0.5cm <= 1cm
                 </li>
                 <li>
                     <input type="checkbox" name="cohort2-tnm_t-1C" id="cohort2-tnm_t-1C" '.($cohort2['tnm_t-1C'] == "on" ? 'checked' :  "").'>
                     T1c: >1cm <= 2cm
                 </li>
                 <li>
                     <input type="checkbox" name="cohort2-tnm_t-1M" id="cohort2-tnm_t-1M" '.($cohort2['tnm_t-1M'] == "on" ? 'checked' :  "").'>
                     T1micro: microinvasion <= 0.1cm
                 </li>
                
            </ul>
        </li>
        <li>
          <input type="checkbox" name="cohort2-tnm_t-2" id="cohort2-tnm_t-2" '.($cohort2['tnm_t-2'] == "on" ? 'checked' :  "").'>
          T2: >2cm <= 5cm
        </li>
        <li>
          <input type="checkbox" name="cohort2-tnm_t-3" id="cohort2-tnm_t-3" '.($cohort2['tnm_t-3'] == "on" ? 'checked' :  "").'>
          T3: >5cm
        </li>
         
        <lix class = "cohort2-tnm_t-4" style = "position:absolute;">+</lix>
    <li class = "cohort2-tnm_t-4">

          <input type="checkbox" name="cohort2-tnm_t-4" id="cohort2-tnm_t-4"'.($cohort2['tnm_t-4'] == "on" ? 'checked' :  "").'>
          T4: any size + chest or skin extension

            <ul>
                 <li>
                     <input type="checkbox" name="cohort2-tnm_t-4A" id="cohort2-tnm_t-4A" '.($cohort2['tnm_t-4A'] == "on" ? 'checked' :  "").'>
                     T4a: chest wall extension
                 </li>
                 <li>
                     <input type="checkbox" name="cohort2-tnm_t-4B" id="cohort2-tnm_t-4B" '.($cohort2['tnm_t-4B'] == "on" ? 'checked' :  "").'>
                     T4b: skin extension
                 </li>
                 <li>
                     <input type="checkbox" name="cohort2-tnm_t-4C" id="cohort2-tnm_t-4C" '.($cohort2['tnm_t-4C'] == "on" ? 'checked' :  "").'>
                     T4c: both T4a and T4b
                 </li>
                 <li>
                     <input type="checkbox" name="cohort2-tnm_t-4D" id="cohort2-tnm_t-4D" '.($cohort2['tnm_t-4D'] == "on" ? 'checked' :  "").'>
                     T4d: inflammatory carcinoma
                 </li>
                
            </ul>
        </li>
        <li>
          <input type="checkbox" name="cohort2-tnm_t-IS" id="cohort2-tnm_t-IS" '.($cohort2['tnm_t-IS'] == "on" ? 'checked' :  "").'>
          Tis: cacrinoma in situ
        </li>
       
       
    </ul>
   

    <div id ="T_Staging-duplicate" class="un-duplicate-btn" style = "margin-top:10px;margin-right:10px;">Un-Compare</div>
     </div>
    <div class="close-btn" style="margin-top:10px;margin-right:10px;" onclick="">Close</div>
    <div id="T_Staging-duplicate" class="duplicate-btn" style="margin-top:10px;margin-right:10px;">Compare</div>
    </div>';

echo '<div id="overlay-N_Staging" class="overlay-content popupN_Staging" style="display: none; top: 20px;"><div id="overlay-N_Stagingcontent" style="float:left;padding-right:10px;">';

echo '<label id="-gsublabel-tnm_t" style="cursor: pointer; cursor: hand; font-size:1.3em; margin-top:5px; margin-left:15px;">N Staging</label>';
echo '<a href="#" class="btn btn-success btn-xs" style="margin-right:40px;"'.
                  'onclick="selectallN1(true);"'
                    .'>Select All</a>';
                echo '<a href="#" class="btn btn-danger btn-xs" '.
                    'onclick="selectallN1(false);"'
                    .'>Select None</a>';
                echo '<div style="margin-bottom:10px;"></div>';
echo '<ul class="treeview">
    <li>
      <input type="checkbox" name="cohort1-tnm_n-_empty_" id="cohort1-tnm_n-_empty_" '.($cohort1['tnm_n-_empty_'] == "on" ? 'checked' :  "").'>
      Missing
    </li>
    <li>
      <input type="checkbox" name="cohort1-tnm_n-X" id="cohort1-tnm_n-X" '.($cohort1['tnm_n-X'] == "on" ? 'checked' :  "").'>
      Nx
    </li>

    <lix class = "cohort1-tnm_n-0" style = "position:absolute;">+</lix>
    <li class = "cohort1-tnm_n-0">
      <input type="checkbox" name="cohort1-tnm_n-0" id="cohort1-tnm_n-0" '.($cohort1['tnm_n-0'] == "on" ? 'checked' :  "").'>
      N0

       <ul>
             <li>
                 <input type="checkbox" name="cohort1-tnm_n-0NI" id="cohort1-tnm_n-0NI" '.($cohort1['tnm_n-0NI'] == "on" ? 'checked' :  "").'>
                 N0(i-)
             </li>
             <li>
                 <input type="checkbox" name="cohort1-tnm_n-0PI" id="cohort1-tnm_n-0PI" '.($cohort1['tnm_n-0PI'] == "on" ? 'checked' :  "").'>
                 N0(i+)
             </li>
            
            
        </ul>
    </li>
    <lix class = "cohort1-tnm_n-1" style = "position:absolute;">+</lix>
    <li class = "cohort1-tnm_n-1">
      <input type="checkbox" name="cohort1-tnm_n-1" id="cohort1-tnm_n-1" '.($cohort1['tnm_n-1'] == "on" ? 'checked' :  "").'>
      N1

        <ul>
             <li>
                 <input type="checkbox" name="cohort1-tnm_n-1A" id="cohort1-tnm_n-1A" '.($cohort1['tnm_n-1A'] == "on" ? 'checked' :  "").'>
                 N1a
             </li>
             <li>
                 <input type="checkbox" name="cohort1-tnm_n-1B" id="cohort1-tnm_n-1B" '.($cohort1['tnm_n-1B'] == "on" ? 'checked' :  "").'>
                 N1b

                   <ul>
                       <li>
                           <input type="checkbox" name="cohort1-tnm_n-1B1" id="cohort1-tnm_n-1B1" '.($cohort1['tnm_n-1B1'] == "on" ? 'checked' :  "").'>
                           N1b1
                       </li>
                       <li>
                           <input type="checkbox" name="cohort1-tnm_n-1B2" id="cohort1-tnm_n-1B2" '.($cohort1['tnm_n-1B2'] == "on" ? 'checked' :  "").'>
                           N1b2
                       </li>
                       <li>
                           <input type="checkbox" name="cohort1-tnm_n-1B3" id="cohort1-tnm_n-1B3" '.($cohort1['tnm_n-1B3'] == "on" ? 'checked' :  "").'>
                           N1b3
                       </li>
                       <li>
                           <input type="checkbox" name="cohort1-tnm_n-1B4" id="cohort1-tnm_n-1B4" '.($cohort1['tnm_n-1B4'] == "on" ? 'checked' :  "").'>
                            N1b4
                       </li>
                      
                  </ul>
             </li>
             <li>
                 <input type="checkbox" name="cohort1-tnm_n-1C" id="cohort1-tnm_n-1C" '.($cohort1['tnm_n-1C'] == "on" ? 'checked' :  "").'>
                 N1c
             </li>
             <li>
                 <input type="checkbox" name="cohort1-tnm_n-1M" id="cohort1-tnm_n-1M" '.($cohort1['tnm_n-1M'] == "on" ? 'checked' :  "").'>
                  N1micro
             </li>
            
        </ul>
    </li>
    <lix class = "cohort1-tnm_n-2" style = "position:absolute;">+</lix>
     <li class = "cohort1-tnm_n-2">
      <input type="checkbox" name="cohort1-tnm_n-2" id="cohort1-tnm_n-2" '.($cohort1['tnm_n-2'] == "on" ? 'checked' :  "").'>
      N2

        <ul>
             <li>
                 <input type="checkbox" name="cohort1-tnm_n-2A" id="cohort1-tnm_n-2A" '.($cohort1['tnm_n-2A'] == "on" ? 'checked' :  "").'>
                 N2a
             </li>
             <li>
                 <input type="checkbox" name="cohort1-tnm_n-2B" id="cohort1-tnm_n-2B" '.($cohort1['tnm_n-2B'] == "on" ? 'checked' :  "").'>
                 N2b
             </li>
            
            
        </ul>
    </li>

    <lix class = "cohort1-tnm_n-3" style = "position:absolute;">+</lix>
     <li class = "cohort1-tnm_n-3" >
      <input type="checkbox" name="cohort1-tnm_n-3" id="cohort1-tnm_n-3"'.($cohort1['tnm_n-3'] == "on" ? 'checked' :  "").'>
      N3

        <ul>
             <li>
                 <input type="checkbox" name="cohort1-tnm_n-3A" id="cohort1-tnm_n-3A" '.($cohort1['tnm_n-3A'] == "on" ? 'checked' :  "").'>
                 N3a
             </li>
             <li>
                 <input type="checkbox" name="cohort1-tnm_n-3B" id="cohort1-tnm_n-3B" '.($cohort1['tnm_n-3B'] == "on" ? 'checked' :  "").'>
                 N3b
             </li>
             <li>
                 <input type="checkbox" name="cohort1-tnm_n-3C" id="cohort1-tnm_n-3C" '.($cohort1['tnm_n-3C'] == "on" ? 'checked' :  "").'>
                 N3c
             </li>
            
            
        </ul>
    </li>

    <lix class = "cohort1-tnm_n-4" style = "position:absolute;">+</lix>
    <li class = "cohort1-tnm_n-4" >
      <input type="checkbox" name="cohort1-tnm_n-4" id="cohort1-tnm_n-4" '.($cohort1['tnm_n-4'] == "on" ? 'checked' :  "").'>
      N4

        <ul>
             <li>
                 <input type="checkbox" name="cohort1-tnm_n-4A" id="cohort1-tnm_n-4A" '.($cohort1['tnm_n-4A'] == "on" ? 'checked' :  "").'>
                 N4a
             </li>
             <li>
                 <input type="checkbox" name="cohort1-tnm_n-4B" id="cohort1-tnm_n-4B" '.($cohort1['tnm_n-4B'] == "on" ? 'checked' :  "").'>
                 N4b
             </li>
             <li>
                 <input type="checkbox" name="cohort1-tnm_n-4C" id="cohort1-tnm_n-4C" '.($cohort1['tnm_n-4C'] == "on" ? 'checked' :  "").'>
                 N4c
             </li>
             <li>
                 <input type="checkbox" name="cohort1-tnm_n-4D" id="cohort1-tnm_n-4D" '.($cohort1['tnm_n-4D'] == "on" ? 'checked' :  "").'>
                 N4d
             </li>
            
            
        </ul>
    </li>   
    <li>
      <input type="checkbox" name="cohort1-tnm_n-IS" id="cohort1-tnm_n-IS" '.($cohort1['tnm_n-IS'] == "on" ? 'checked' :  "").'>
      Nis
    </li>   
   
  </ul>
  </div>

  <div id="overlay-N_Stagingcontent-cohort2" style="float:left;padding-right:10px;display:none;">';
echo '<label id="-gsublabel-tnm_t" style="cursor: pointer; cursor: hand; font-size:1.3em; margin-top:5px; margin-left:15px;">Comparison</label>';
echo '<a href="#" class="btn btn-success btn-xs" style="margin-right:40px;"'.
                  'onclick="selectallN2(true);"'
                    .'>Select All</a>';
                echo '<a href="#" class="btn btn-danger btn-xs" '.
                    'onclick="selectallN2(false);"'
                    .'>Select None</a>';
                echo '<div style="margin-bottom:10px;"></div>';

echo'<ul class="treeview">
    <li>
      <input type="checkbox" name="cohort2-tnm_n-_empty_" id="cohort2-tnm_n-_empty_" '.($cohort2['tnm_n-_empty_'] == "on" ? 'checked' :  "").'>
      Missing
    </li>
    <li>
      <input type="checkbox" name="cohort2-tnm_n-X" id="cohort2-tnm_n-X" '.($cohort2['tnm_n-X'] == "on" ? 'checked' :  "").'>
      Nx
    </li>

    <lix class = "cohort2-tnm_n-0" style = "position:absolute;">+</lix>
    <li class = "cohort2-tnm_n-0">
      <input type="checkbox" name="cohort2-tnm_n-0" id="cohort2-tnm_n-0" '.($cohort2['tnm_n-0'] == "on" ? 'checked' :  "").'>
      N0

       <ul>
             <li>
                 <input type="checkbox" name="cohort2-tnm_n-0NI" id="cohort2-tnm_n-0NI" '.($cohort2['tnm_n-0NI'] == "on" ? 'checked' :  "").'>
                 N0(i-)
             </li>
             <li>
                 <input type="checkbox" name="cohort2-tnm_n-0PI" id="cohort2-tnm_n-0PI" '.($cohort2['tnm_n-0PI'] == "on" ? 'checked' :  "").'>
                 N0(i+)
             </li>
            
            
        </ul>
    </li>
    <lix class = "cohort2-tnm_n-1" style = "position:absolute;">+</lix>
    <li class = "cohort2-tnm_n-1">
      <input type="checkbox" name="cohort2-tnm_n-1" id="cohort2-tnm_n-1" '.($cohort2['tnm_n-1'] == "on" ? 'checked' :  "").'>
      N1

        <ul>
             <li>
                 <input type="checkbox" name="cohort2-tnm_n-1A" id="cohort2-tnm_n-1A" '.($cohort2['tnm_n-1A'] == "on" ? 'checked' :  "").'>
                 N1a
             </li>
             <li>
                 <input type="checkbox" name="cohort2-tnm_n-1B" id="cohort2-tnm_n-1B" '.($cohort2['tnm_n-1B'] == "on" ? 'checked' :  "").'>
                 N1b

                   <ul>
                       <li>
                           <input type="checkbox" name="cohort2-tnm_n-1B1" id="cohort2-tnm_n-1B1" '.($cohort2['tnm_n-1B1'] == "on" ? 'checked' :  "").'>
                           N1b1
                       </li>
                       <li>
                           <input type="checkbox" name="cohort2-tnm_n-1B2" id="cohort2-tnm_n-1B2" '.($cohort2['tnm_n-1B2'] == "on" ? 'checked' :  "").'>
                           N1b2
                       </li>
                       <li>
                           <input type="checkbox" name="cohort2-tnm_n-1B3" id="cohort2-tnm_n-1B3" '.($cohort2['tnm_n-1B3'] == "on" ? 'checked' :  "").'>
                           N1b3
                       </li>
                       <li>
                           <input type="checkbox" name="cohort2-tnm_n-1B4" id="cohort2-tnm_n-1B4" '.($cohort2['tnm_n-1B4'] == "on" ? 'checked' :  "").'>
                            N1b4
                       </li>
                      
                  </ul>
             </li>
             <li>
                 <input type="checkbox" name="cohort2-tnm_n-1C" id="cohort2-tnm_n-1C" '.($cohort2['tnm_n-1C'] == "on" ? 'checked' :  "").'>
                 N1c
             </li>
             <li>
                 <input type="checkbox" name="cohort2-tnm_n-1M" id="cohort2-tnm_n-1M" '.($cohort2['tnm_n-1M'] == "on" ? 'checked' :  "").'>
                  N1micro
             </li>
            
        </ul>
    </li>
    <lix class = "cohort2-tnm_n-2" style = "position:absolute;">+</lix>
     <li class = "cohort2-tnm_n-2">
      <input type="checkbox" name="cohort2-tnm_n-2" id="cohort2-tnm_n-2" '.($cohort2['tnm_n-2'] == "on" ? 'checked' :  "").'>
      N2

        <ul>
             <li>
                 <input type="checkbox" name="cohort2-tnm_n-2A" id="cohort2-tnm_n-2A" '.($cohort2['tnm_n-2A'] == "on" ? 'checked' :  "").'>
                 N2a
             </li>
             <li>
                 <input type="checkbox" name="cohort2-tnm_n-2B" id="cohort2-tnm_n-2B" '.($cohort2['tnm_n-2B'] == "on" ? 'checked' :  "").'>
                 N2b
             </li>
            
            
        </ul>
    </li>

    <lix class = "cohort2-tnm_n-3" style = "position:absolute;">+</lix>
     <li class = "cohort2-tnm_n-3" >
      <input type="checkbox" name="cohort2-tnm_n-3" id="cohort2-tnm_n-3"'.($cohort2['tnm_n-3'] == "on" ? 'checked' :  "").'>
      N3

        <ul>
             <li>
                 <input type="checkbox" name="cohort2-tnm_n-3A" id="cohort2-tnm_n-3A" '.($cohort2['tnm_n-3A'] == "on" ? 'checked' :  "").'>
                 N3a
             </li>
             <li>
                 <input type="checkbox" name="cohort2-tnm_n-3B" id="cohort2-tnm_n-3B" '.($cohort2['tnm_n-3B'] == "on" ? 'checked' :  "").'>
                 N3b
             </li>
             <li>
                 <input type="checkbox" name="cohort2-tnm_n-3C" id="cohort2-tnm_n-3C" '.($cohort2['tnm_n-3C'] == "on" ? 'checked' :  "").'>
                 N3c
             </li>
            
            
        </ul>
    </li>

    <lix class = "cohort2-tnm_n-4" style = "position:absolute;">+</lix>
    <li class = "cohort2-tnm_n-4" >
      <input type="checkbox" name="cohort2-tnm_n-4" id="cohort2-tnm_n-4" '.($cohort2['tnm_n-4'] == "on" ? 'checked' :  "").'>
      N4

        <ul>
             <li>
                 <input type="checkbox" name="cohort2-tnm_n-4A" id="cohort2-tnm_n-4A" '.($cohort2['tnm_n-4A'] == "on" ? 'checked' :  "").'>
                 N4a
             </li>
             <li>
                 <input type="checkbox" name="cohort2-tnm_n-4B" id="cohort2-tnm_n-4B" '.($cohort2['tnm_n-4B'] == "on" ? 'checked' :  "").'>
                 N4b
             </li>
             <li>
                 <input type="checkbox" name="cohort2-tnm_n-4C" id="cohort2-tnm_n-4C" '.($cohort2['tnm_n-4C'] == "on" ? 'checked' :  "").'>
                 N4c
             </li>
             <li>
                 <input type="checkbox" name="cohort2-tnm_n-4D" id="cohort2-tnm_n-4D" '.($cohort2['tnm_n-4D'] == "on" ? 'checked' :  "").'>
                 N4d
             </li>
            
            
        </ul>
    </li>   
    <li>
      <input type="checkbox" name="cohort2-tnm_n-IS" id="cohort2-tnm_n-IS" '.($cohort2['tnm_n-IS'] == "on" ? 'checked' :  "").'>
      Nis
    </li>   
   
  </ul>
   <div id ="N_Staging-duplicate" class="un-duplicate-btn" style = "margin-top:10px;margin-right:10px;">Un-Compare</div>
  </div>


  <div class="close-btn" style="margin-top:10px;margin-right:10px;" onclick="">Close</div>
  <div id="N_Staging-duplicate" class="duplicate-btn" style="margin-top:10px;margin-right:10px;">Compare</div>
  </div>';
//m staging
  echo '<div id="overlay-M_Staging" class="overlay-content popupM_Staging" style="display: none; top: 20px;"><div id="overlay-M_Stagingcontent" style="float:left;padding-right:10px;">';
 
  echo '<label id="-gsublabel-tnm_t" style="cursor: pointer; cursor: hand; font-size:1.3em; margin-top:5px; margin-left:15px;">M Staging</label>';
   echo '<a href="#" class="btn btn-success btn-xs" style="margin-right:40px;"'.
                  'onclick="selectallM1(true);"'
                    .'>Select All</a>';
                echo '<a href="#" class="btn btn-danger btn-xs" '.
                    'onclick="selectallM1(false);"'
                    .'>Select None</a>';
                echo '<div style="margin-bottom:10px;"></div>';
  echo '<ul class="treeview">
    <li>
      <input type="checkbox" name="cohort1-tnm_m-_empty_" id="cohort1-tnm_m-_empty_" '.($cohort1['tnm_m-_empty_'] == "on" ? 'checked' :  "").'>
      Missing
    </li>
    <li>
      <input type="checkbox" name="cohort1-tnm_m-X" id="cohort1-tnm_m-X" '.($cohort1['tnm_m-X'] == "on" ? 'checked' :  "").'>
      Mx
    </li>
    <li>
      <input type="checkbox" name="cohort1-tnm_m-0" id="cohort1-tnm_m-0" '.($cohort1['tnm_m-0'] == "on" ? 'checked' :  "").'>
      M0
    </li>
    <lix class = "cohort1-tnm_m-1" style = "position:absolute;">+</lix>
    <li class = "cohort1-tnm_m-1">
      <input type="checkbox" name="cohort1-tnm_m-1" id="cohort1-tnm_m-1" '.($cohort1['tnm_m-1'] == "on" ? 'checked' :  "").'>
      M1

        <ul>
             <li>
                 <input type="checkbox" name="cohort1-tnm_m-1A" id="cohort1-tnm_m-1A" '.($cohort1['tnm_m-1A'] == "on" ? 'checked' :  "").'>
                 M1a
             </li>
             <li>
                 <input type="checkbox" name="cohort1-tnm_m-1B" id="cohort1-tnm_m-1B" '.($cohort1['tnm_m-1B'] == "on" ? 'checked' :  "").'>
                 M1b
             </li>
             <li>
                 <input type="checkbox" name="cohort1-tnm_m-1C" id="cohort1-tnm_m-1C" '.($cohort1['tnm_m-1C'] == "on" ? 'checked' :  "").'>
                 M1c
             </li>
             <li>
                 <input type="checkbox" name="cohort1-tnm_m-1M" id="cohort1-tnm_m-1M" '.($cohort1['tnm_m-1M'] == "on" ? 'checked' :  "").'>
                  M1micro
             </li>
            
        </ul>
    </li>
     <li>
      <input type="checkbox" name="cohort1-tnm_m-2" id="cohort1-tnm_m-2" '.($cohort1['tnm_m-2'] == "on" ? 'checked' :  "").'>
      M2
    </li>
     <li>
      <input type="checkbox" name="cohort1-tnm_m-3" id="cohort1-tnm_m-3" '.($cohort1['tnm_m-3'] == "on" ? 'checked' :  "").'>
      M3
    </li>
    <lix class = "cohort1-tnm_m-4" style = "position:absolute;">+</lix>
    <li class = "cohort1-tnm_m-4">
      <input type="checkbox" name="cohort1-tnm_m-4" id="cohort1-tnm_m-4"'.($cohort1['tnm_m-4'] == "on" ? 'checked' :  "").'>
      M4

        <ul>
             <li>
                 <input type="checkbox" name="cohort1-tnm_m-4A" id="cohort1-tnm_m-4A" '.($cohort1['tnm_m-4A'] == "on" ? 'checked' :  "").'>
                 M4a
             </li>
             <li>
                 <input type="checkbox" name="cohort1-tnm_m-4B" id="cohort1-tnm_m-4B" '.($cohort1['tnm_m-4B'] == "on" ? 'checked' :  "").'>
                 M4b
             </li>
             <li>
                 <input type="checkbox" name="cohort1-tnm_m-4C" id="cohort1-tnm_m-4C" '.($cohort1['tnm_m-4C'] == "on" ? 'checked' :  "").'>
                 M4c
             </li>
             <li>
                 <input type="checkbox" name="cohort1-tnm_m-4D" id="cohort1-tnm_m-4D" '.($cohort1['tnm_m-4D'] == "on" ? 'checked' :  "").'>
                 M4d
             </li>
            
            
        </ul>
    </li>   
    <li>
      <input type="checkbox" name="cohort1-tnm_m-IS" id="cohort1-tnm_m-IS" '.($cohort1['tnm_m-IS'] == "on" ? 'checked' :  "").'>
      Mis
    </li>   
   
  </ul>
  </div>

  <div id="overlay-M_Stagingcontent-cohort2" style="float:left;padding-right:10px;display:none;">';
  echo '<label id="-gsublabel-tnm_t" style="cursor: pointer; cursor: hand; font-size:1.3em; margin-top:5px; margin-left:15px;">Comparison</label>';
  echo '<a href="#" class="btn btn-success btn-xs" style="margin-right:40px;"'.
                  'onclick="selectallM2(true);"'
                    .'>Select All</a>';
                echo '<a href="#" class="btn btn-danger btn-xs" '.
                    'onclick="selectallM2(false);"'
                    .'>Select None</a>';
                echo '<div style="margin-bottom:10px;"></div>';
  echo '<ul class="treeview">
    <li>
      <input type="checkbox" name="cohort2-tnm_m-_empty_" id="cohort2-tnm_m-_empty_" '.($cohort2['tnm_m-_empty_'] == "on" ? 'checked' :  "").'>
      Missing
    </li>
    <li>
      <input type="checkbox" name="cohort2-tnm_m-X" id="cohort2-tnm_m-X" '.($cohort2['tnm_m-X'] == "on" ? 'checked' :  "").'>
      Mx
    </li>
    <li>
      <input type="checkbox" name="cohort2-tnm_m-0" id="cohort2-tnm_m-0" '.($cohort2['tnm_m-0'] == "on" ? 'checked' :  "").'>
      M0
    </li>
    <lix class = "cohort2-tnm_m-1" style = "position:absolute;">+</lix>
    <li class = "cohort2-tnm_m-1">
      <input type="checkbox" name="cohort2-tnm_m-1" id="cohort2-tnm_m-1" '.($cohort2['tnm_m-1'] == "on" ? 'checked' :  "").'>
      M1

        <ul>
             <li>
                 <input type="checkbox" name="cohort2-tnm_m-1A" id="cohort2-tnm_m-1A" '.($cohort2['tnm_m-1A'] == "on" ? 'checked' :  "").'>
                 M1a
             </li>
             <li>
                 <input type="checkbox" name="cohort2-tnm_m-1B" id="cohort2-tnm_m-1B" '.($cohort2['tnm_m-1B'] == "on" ? 'checked' :  "").'>
                 M1b
             </li>
             <li>
                 <input type="checkbox" name="cohort2-tnm_m-1C" id="cohort2-tnm_m-1C" '.($cohort2['tnm_m-1C'] == "on" ? 'checked' :  "").'>
                 M1c
             </li>
             <li>
                 <input type="checkbox" name="cohort2-tnm_m-1M" id="cohort2-tnm_m-1M" '.($cohort2['tnm_m-1M'] == "on" ? 'checked' :  "").'>
                  M1micro
             </li>
            
        </ul>
    </li>
     <li>
      <input type="checkbox" name="cohort2-tnm_m-2" id="cohort2-tnm_m-2" '.($cohort2['tnm_m-2'] == "on" ? 'checked' :  "").'>
      M2
    </li>
     <li>
      <input type="checkbox" name="cohort2-tnm_m-3" id="cohort2-tnm_m-3" '.($cohort2['tnm_m-3'] == "on" ? 'checked' :  "").'>
      M3
    </li>
    <lix class = "cohort2-tnm_m-4" style = "position:absolute;">+</lix>
    <li class = "cohort2-tnm_m-4">
      <input type="checkbox" name="cohort2-tnm_m-4" id="cohort2-tnm_m-4"'.($cohort2['tnm_m-4'] == "on" ? 'checked' :  "").'>
      M4

        <ul>
             <li>
                 <input type="checkbox" name="cohort2-tnm_m-4A" id="cohort2-tnm_m-4A" '.($cohort2['tnm_m-4A'] == "on" ? 'checked' :  "").'>
                 M4a
             </li>
             <li>
                 <input type="checkbox" name="cohort2-tnm_m-4B" id="cohort2-tnm_m-4B" '.($cohort2['tnm_m-4B'] == "on" ? 'checked' :  "").'>
                 M4b
             </li>
             <li>
                 <input type="checkbox" name="cohort2-tnm_m-4C" id="cohort2-tnm_m-4C" '.($cohort2['tnm_m-4C'] == "on" ? 'checked' :  "").'>
                 M4c
             </li>
             <li>
                 <input type="checkbox" name="cohort2-tnm_m-4D" id="cohort2-tnm_m-4D" '.($cohort2['tnm_m-4D'] == "on" ? 'checked' :  "").'>
                 M4d
             </li>
            
            
        </ul>
    </li>   
    <li>
      <input type="checkbox" name="cohort2-tnm_m-IS" id="cohort2-tnm_m-IS" '.($cohort2['tnm_m-IS'] == "on" ? 'checked' :  "").'>
      Mis
    </li>   
   
  </ul>
   <div id ="M_Staging-duplicate" class="un-duplicate-btn" style = "margin-top:10px;margin-right:10px;">Un-Compare</div>
  </div>

  <div class="close-btn" style="margin-top:10px;margin-right:10px;" onclick="">Close</div>
  <div id="M_Staging-duplicate" class="duplicate-btn" style="margin-top:10px;margin-right:10px;">Compare</div>

  </div>';
}

//Modified from code provided by Stephen Smithbower
function genInputElements($cohort1, $cohort2)
{
  $dictionary = json_decode(file_get_contents("dictionary.json"));
  //global $dictionary;
  
  $count = 1;
  $cohort = $cohort1;
  //Generate inputs.
  foreach ($dictionary as $group => $subgroup)
  {
   
    if($group == 'TNM_Staging')
    {
      getTNMhtml($cohort1, $cohort2);
    }
    else
    {
       echo '<div id="overlay-'.$group.'" class= "overlay-content popup'.$group.'">';
      //echo '<p class = "popuptitle">Set '.$subgroup->display.' Value(s)</p>';

      $pref = 'cohort1';
      $cohort = $cohort1;
      echo '<div id="overlay-'.$group.'content" style="float:left;padding-right:10px;">';
        foreach ($subgroup->elements as $element)
        {
          foreach ($element as $subelement => $spec)
          {
            $subCheckName ='gsublabel-'.$subelement.'-display';
            echo '<label id="-gsublabel-'.$subelement.'" style="cursor: pointer; cursor: hand; font-size:1.3em; margin-top:5px;'.
            ($subgroup->display != "none" ? " margin-left:15px;" : "").'">'.$spec->display.'</label><br>';

            switch ($spec->input)
            {
              case 'range':
                echo '<label>Min</label>';
                echo '<input type="number" class="form-control" name="'.$pref.'-'.$subelement.'-min" placeholder="min" style="width:100px;" value="'.
                  $cohort[$subelement.'-min'].'">';
                echo '<label>Max</label>';
                echo '<input type="number" class="form-control" name="'.$pref.'-'.$subelement.'-max" placeholder="max" style="width:100px;" value="'.
                 $cohort[$subelement.'-max'].'">';
                echo '<div style="clear:both; margin-bottom:-22px;">&nbsp;</div>';
                break;
                
                case 'select':
                echo '<a href="#" class="btn btn-success btn-xs" style="margin-right:40px;"'.
                  'onclick="$(\'.'.$pref.'-'.$subelement.'\').each(function(){this.checked=true;});
                  $(\'.cohort2-'.$subelement.'\').each(function(){this.checked=true;});"'
                    .'>Select All</a>';
                echo '<a href="#" class="btn btn-danger btn-xs" '.
                    'onclick="$(\'.'.$pref.'-'.$subelement.'\').each(function(){this.checked=false;});
                    $(\'.cohort2-'.$subelement.'\').each(function(){this.checked=false;});"'
                    .'>Select None</a>';
                echo '<div style="margin-bottom:10px;"></div>';
                echo '<div class="scrollcombo" id = "scrollcombo-'.$subelement.'"">';
                  foreach ($spec->values as $dbVal=>$value)
                  {
                    $id = $subelement.'-'.$dbVal;
                    echo '<input type="checkbox" id ="'.$pref.'-'.$id.'" class="'.$pref.'-'.$subelement.'" name="'.$pref.'-'.$id.'" '.($cohort[$id] == "on" ? 'checked' :  "").'/>'.
                            ($spec->type == "aggregate" ? $value->display : $value)."<br>";
                  }
                echo '</div>';
                break;

              case 'toggle':
                echo '<input type="checkbox" name="'.$pref.'-'.$subelement.
                  '" '.(isset($cohort[$subelement]) && $cohort[$subelement] == true ?
                  'checked' : '').'> Included</br>';
                break;
            }
          }
        }
        echo '</div>';

        $pref = 'cohort2';
        $cohort = $cohort2;
        //re-create as hidden for 2nd cohort
        echo '<div id="overlay-'.$group.'content-cohort2" style="display:none;">';
        foreach ($subgroup->elements as $element)
        {
          foreach ($element as $subelement => $spec)
          {
            $subCheckName ='gsublabel-'.$subelement.'-display';
            echo '<label id="-gsublabel-'.$subelement.'" style="cursor: pointer; cursor: hand; font-size:1.3em; margin-top:5px;'.
            ($subgroup->display != "none" ? " margin-left:15px;" : "").'">Comparison</label><br>';
           
            switch ($spec->input)
            {
              case 'range':
                echo '<label>Min</label>';
                echo '<input type="number" class="form-control" name="'.$pref.'-'.$subelement.'-min" placeholder="min" style="width:100px;" value="'.
                  $cohort[$subelement.'-min'].'">';
                echo '<label>Max</label>';
                echo '<input type="number" class="form-control" name="'.$pref.'-'.$subelement.'-max" placeholder="max" style="width:100px;" value="'.
                 $cohort[$subelement.'-max'].'">';
                echo '<div style="clear:both; margin-bottom:-22px;">&nbsp;</div>';
                break;
                
                case 'select':
                echo '<a href="#" class="btn btn-success btn-xs" style="margin-right:40px;"'.
                  'onclick="$(\'.'.$pref.'-'.$subelement.'\').each(function(){this.checked=true;});"'
                    .'>Select All</a>';
                echo '<a href="#" class="btn btn-danger btn-xs" '.
                    'onclick="$(\'.'.$pref.'-'.$subelement.'\').each(function(){this.checked=false;});"'
                    .'>Select None</a>';
                echo '<div style="margin-bottom:10px;"></div>';
                echo '<div class="scrollcombo" id = "scrollcombo2-'.$subelement.'"">';
                  foreach ($spec->values as $dbVal=>$value)
                  {
                    $id = $subelement.'-'.$dbVal;
                    echo '<input type="checkbox" id ="'.$pref.'-'.$id.'"  class="'.$pref.'-'.$subelement.'" name="'.$pref.'-'.$id.'" '.($cohort[$id] == "on" ? 'checked' :  "").'/>'.
                            ($spec->type == "aggregate" ? $value->display : $value)."<br>";
                  }
                echo '</div>';
                break;

              case 'toggle':
                echo '<input type="checkbox" name="'.$pref.'-'.$subelement.
                  '" '.(isset($cohort[$subelement]) && $cohort[$subelement] == true ?
                  'checked' : '').'> Included</br>';
                break;
            }
          }
        }
        echo '<div id ="'.$group.'-duplicate" class="un-duplicate-btn" style = "margin-top:10px;margin-right:10px;">Un-compare</div>';

        echo '</div>';
        echo '<div style = "clear:both;">'; //div for buttons
        echo '<div class="close-btn" style = "margin-top:10px;margin-right:10px;" onClick = "">Close</div>';
        echo '<div id ="'.$group.'-duplicate" class="duplicate-btn" style = "margin-top:10px;margin-right:10px;">Compare</div>';
       
        echo '</div>'; //div for buttons
      echo '</div>';
      $count++;
    }
  }
}
?>


</table>

</body>
</html>
