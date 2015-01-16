<!Doctype HTML>

<head>
  	
 	<script src="jquery/jquery-1.8.3.js"></script>
  <script src="jquery/ui/jquery.ui.core.js"></script>
  <script src="jquery/ui/jquery.ui.widget.js"></script>
  <script src="jquery/ui/jquery.ui.tabs.js"></script>
  <link rel="stylesheet" href="jquery/demos.css">
  <script>
  $(function() {
    $( "#tabs" ).tabs({
      collapsible: true
    });
  });
  </script>
	
	{{ HTML::style('assets/style.css') }}
	

  	<script type="text/javascript">

    //to do = handle cloning branches, removing nodes with branches, removing clones
      
		function collision($div1, $div2) {

			var x1 = $div1.offset().left;
			var y1 = $div1.offset().top;
			var h1 = $div1.outerHeight(true);
			var w1 = $div1.outerWidth(true);

			var b1 = y1 + h1;
			var r1 = x1 + w1;
			var x2 = $div2.offset().left;
			var y2 = $div2.offset().top;
			var h2 = $div2.outerHeight(true);
			var w2 = $div2.outerWidth(true);
			var b2 = y2 + h2;
			var r2 = x2 + w2;


			if (b1 < y2 || y1 > b2 || r1 < x2 || x1 > r2) return false;
				return true;
		}

	     

	      //keep track of how many nodes are in the tree
	    var nodecount= 0;
      var attr_duplicated = false;

	  	function create_branch(){
	        if(nodecount>0){


	          var iDiv = document.createElement('div');
	          iDiv.id = 'tree_branch'+nodecount;
	          iDiv.className = 'tree_branch';
	          document.getElementById('cohort_tree').appendChild(iDiv);
	        }
	      }

		function add_node(div){
      if(div.id.indexOf("-clone") == -1){
  			var new_id = div.id+"-clone";

  			//check so that it only clones once
  			if (!document.getElementById(new_id)) {

  			  //create a tree "branch"
  			  create_branch();
          //.clone( [withDataAndEvents] [, deepWithDataAndEvents] )
  			  $(div).clone(true).attr('id', new_id).appendTo('#cohort_tree');
  			  nodecount++;
  			}
    }

   

		}

	    function show_tooltip(val){
	      var docHeight = $(document).height(); //grab the height of the page
	      var scrollTop = $(window).scrollTop(); //grab the px value from the top of the page to where you're scrolling
	      $('.overlay-bg').show().css({'height' : docHeight}); //display your popup background and set height to the page height
	      $('.popuptooltip'+val).show().css({'top': scrollTop+20+'px'}); //show the appropriate popup and set the content 20px from the window top
	    }
      function handle_duplicate(div){
        //if nothing is duplicated, duplicate and hide all duplicate buttons
        if(attr_duplicated==false){
          attr_duplicated=true;
          var duplicate_btns = document.getElementsByClassName('duplicate-btn'), i;

          for (var i = 0; i < duplicate_btns.length; i ++) {
              duplicate_btns[i].style.display = 'none';
          }
        }
        //if something has been duplicated, remove duplicated node and make duplicate buttons available
        else{
          attr_duplicated=false;
          //document.getElementsByClassName('duplicate-btn')[0].style.visibility='visible';
          var duplicate_btns = document.getElementsByClassName('duplicate-btn'), i;

          for (var i = 0; i < duplicate_btns.length; i ++) {
              duplicate_btns[i].style.display = 'inline';
          }
        }
      }

	    $(document).ready( function() {
	     
	      

        //no rightclick context menu for attributes
        $('.attr,.attrsurgery,.attrradiation,.attrmedical').bind('contextmenu', function(e){
            return false;
        }); 
        //handle left (1) and right clicks (3)
        $('.attr,.attrsurgery,.attrradiation,.attrmedical').mousedown(function(event) {
          switch (event.which) {
            case 1:
              event.preventDefault(); // disable normal link function so that it doesn't refresh the page
              var docHeight = $(document).height(); //grab the height of the page
              var scrollTop = $(window).scrollTop(); //grab the px value from the top of the page to where you're scrolling
              var selectedPopup = $(this).data('showpopup'); //get the corresponding popup to show
               
              $('.overlay-bg').show().css({'height' : docHeight}); //display your popup background and set height to the page height
              $('.popup'+selectedPopup).show().css({'top': scrollTop+20+'px'}); //show the appropriate popup and set the content 20px from the window top
              add_node(this); //add cloned node to cohort builder
                break;
            case 2:
                //alert('Middle Mouse button pressed.');
                break;
            case 3:
                //remove it only if it is a cloned attribute (in the cohort builder)
                if(this.id.indexOf("-clone") != -1)
                {
                  alert(this.nodecount);
                  this.remove();
                  nodecount--;
                }
                break;
            default:
                alert('You have a strange Mouse!');
            }
        });
  	   
  	    // hide popup when user clicks on close button or if user clicks anywhere outside the container
  	    $('.close-btn, .overlay-bg').click(function(){
  	        $('.overlay-bg, .overlay-content').hide(); // hide the overlay
  	    });

        //handle when the duplicate button is pressed
        $('.duplicate-btn').click(function(){
            handle_duplicate(this);
        });
        
        //select the appropriate attr group based on user type role1=radiation role2 = med and role3 = surg
        var role = <?php echo json_encode($role); ?>;
        if(role==1)
        {
           document.getElementById('ui-id-3').click();
        }
        else if(role==2)
        {
           document.getElementById('ui-id-4').click();
        }
        else if (role==3)
        {
          document.getElementById('ui-id-2').click();
        }

	    });
  	</script>
  	<style>

		body {
		  font: 10px sans-serif;
		}

		.axis path,
		.axis line {
		  fill: none;
		  stroke: #000;
		  shape-rendering: crispEdges;
		}

		.x.axis path {
		  display: none;
		}

		.line {
		  fill: none;
		  stroke: steelblue;
		  stroke-width: 1.5px;
		}

		</style>

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
                        <li><a href="logout">Log Out</a></li>
                        <li><a href="profile">{{ Auth::user()->username }}</a></li>
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

<!-- start of visualizations-->
  <div id = "cohort_builder">
    <div id="tabs" class='tabbable'>
     <ul class='nav nav-tabs'>
      <!-- This will choose the correct attribute tree based on user oncology -->
      
     
     
      <li class = 'active' id='alltab'> <a href="#All" data-toggle="tab">All</a></li>
      <li id='surgerytab'><a href="#Surgical" data-toggle="tab">Surgical Oncology</a></li>
      <li id='radiationtab'><a href="#Radiation" data-toggle="tab">Radiation Oncology</a></li>
      <li id='medicaltab'><a href="#Medical" data-toggle="tab">Medical Oncology</a></li>


     </ul>

    <img src="images/help.png" alt="alternative text" title="The Cohort Tree shows attributes you've set values for and filtered your cohort's with. Any attribute not in the Cohort Tree will not be used to filter your cohorts." style = "width:10px;height:10px;cursor:pointer;" onclick="show_tooltip(1);"/>
     <div class='tab-content'>

      <div class="tab-pane active" id="All">
        
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
        </div>
        <div id = "All-radiation">
          <div id="nodes"class = "attrradiation"  href="#" data-showpopup="Nodes">Nodes</div>
          <div id="histology"class = "attrradiation"  href="#" data-showpopup="Hist">Histology</div>
          <div id="progesterone_receptor"class = "attrradiation"  href="#" data-showpopup="PGR">Progesterone Receptor</div>
        </div>
        <div id = "All-medical">
          <div id="menopause"class = "attrmedical" href="#" data-showpopup="Meno_Status">Menopause</div>
          <div id="estrogen_receptor"class = "attrmedical"  href="#" data-showpopup="Immuno_Stains">Estrogen Receptor</div>
          <div id="her2"class = "attrmedical"  href="#" data-showpopup="Her2">Her2</div>
          <div id="chemo"class = "attrmedical"  href="#" data-showpopup="Chemo">Chemo</div>
        </div>

      </div>

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
     

     </div>
    </div>

   

  <!-- 
    End Attribute Collection tabbed panel
  -->

  <!-- 
    Begin Cohort Builder Tree
  -->
   
    <div id="cohort_tree">
      <p style="display:inline-block;">Cohort Filters</p>
    <img src="images/help.png" alt="alternative text" title="The Cohort Tree shows attributes you've set values for and filtered your cohort's with. Any attribute not in the Cohort Tree will not be used to filter your cohorts." style = "width:10px;height:10px;cursor:pointer;" onclick="show_tooltip(2);"/>

    </div>

  </div>

  <!-- 
  End Cohort Builder Tree
-->

  

<!-- Start of popup windows -->
<div class="overlay-bg">
</div>

<div class="overlay-content popuptooltip1">
    <p>Patient attributes are grouped into collections based on the user's field of oncology.</p>
    <p>To add an attribute to the Cohort Tree, which will be used to filter patient cohorts, simply click it. This will open a window which will allow you to select that filter's value(s).</p>
    <button class="close-btn">Close</button>
</div>

<div class="overlay-content popuptooltip2">
    <p>The Cohort Tree shows any attributes that have set value(s) and will be used to filter patient cohorts.</p>
    <p>Any attributes not present in the Cohort Builder will not be used to filter patient cohorts.</p>
    <p>To remove an attribute filter, click the "-" button of the attribute you wish to no longer filter with.</p>
    <p>To duplicate an attribute filter, which is only allowed once, click the "+" button of the attribute you wish to duplicate. This is the key attribute used when comparing two cohorts.</p>
    <p>Clicking an attribute in the Cohort Tree directly will allow you to change it's value(s).</p>
    <button class="close-btn">Close</button>
</div>

<?php


//Modified from code provided by Stephen Smithbower
function genInputElements()
{
  $dictionary = json_decode(file_get_contents("dictionary.json"));
  
  $count = 1;
  //Generate inputs.
  foreach ($dictionary as $group => $subgroup)
  {
    echo '<div id="overlay-'.$group.'" class= "overlay-content popup'.$group.'">';
      //echo '<p class = "popuptitle">Set '.$subgroup->display.' Value(s)</p>';

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
              echo '<input class="form-control" name="-'.$subelement.'-min" placeholder="min" style="width:60px;" value="'.
                $spec->defaultmin.'">';
              echo '<label>Max</label>';
              echo '<input class="form-control" name="-'.$subelement.'-max" placeholder="max" style="width:60px;" value="'.
               $spec->defaultmax.'">';
              echo '<div style="clear:both; margin-bottom:-22px;">&nbsp;</div>';
              break;

            case 'select':
              echo '<a href="#" class="btn btn-success btn-xs" style="margin-right:40px;"'.
                'onclick="$(\'.-'.$subelement.'\').each(function(){this.checked=true;});"'
                  .'>Select All</a>';
              echo '<a href="#" class="btn btn-danger btn-xs" '.
                  'onclick="$(\'.-'.$subelement.'\').each(function(){this.checked=false;});"'
                  .'>Select None</a>';
              echo '<div style="margin-bottom:10px;"></div>';
              echo '<div class="scrollcombo">';
                foreach ($spec->values as $dbVal=>$value)
                {
                  $id = $subelement.'-'.$dbVal;
                  echo '<input type="checkbox" class="-'.$subelement.'" name="-'.$id.'"'.'/>'.
                          ($spec->type == "aggregate" ? $value->display : $value)."<br>";
                }
              echo '</div>';
              break;

            case 'toggle':
              echo '<input type="checkbox" name="-'.$subelement.
                '"> Included</br>';
              break;
          }
        }
      }

      echo '<button class="close-btn" style = "margin-top:10px;margin-right:10px;">Close</button>';
      echo '<button id ="'.$group.'-duplicate" class="duplicate-btn" style = "margin-top:10px;margin-right:10px;">Duplicate</button>';
      
    echo '</div>';
    $count++;
  }
}

genInputElements();

?>

<!-- end of popup windows -->

<!-- start of visualizations-->
<div class="tabbable" id="visualization" >
    <ul class="nav nav-tabs">
        <li class="active"><a class="atab" href="#a_tab" data-toggle="tab">Survival Curves</a></li>
        <li><a class="btab" href="#b_tab" data-toggle="tab">Recurrence Curves</a></li>
    </ul>
    <div class="tab-content">
        <div class="tab-pane active" id="a_tab">
            <h1>Survival Curves</h1>
            <acontent></acontent>
        </div>
        <div class="tab-pane" id="b_tab">
            <h1>Recurrence Curves</h1>
            <bcontent></bcontent>
        </div>
    </div>
</div>

<script src="http://d3js.org/d3.v3.js"></script>


<script>
    $.ajaxSetup ({
        // Disable caching of AJAX responses
        // Used when debugging
        cache: false
    });

    $.getScript("a.js");
    $(".atab").click(function() {
        $.getScript("a.js");
    })
</script>
<script>
    $(".btab").click(function() {
        $.getScript("b.js");
    })
</script>

</body>
</html>
