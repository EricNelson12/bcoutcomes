<!Doctype HTML>

<head>
    
  {{ HTML::script('assets/jquery-1.7.1.min.js') }}
  {{ HTML::script('assets/jquery.hashchange.min.js') }}
  {{ HTML::script('assets/jquery.easytabs.min.js') }}
  {{ HTML::style('assets/style.css') }}
  

    <script type="text/javascript">
      
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

      function create_branch(){
          if(nodecount>0){


            var iDiv = document.createElement('div');
            iDiv.id = 'tree_branch'+nodecount;
            iDiv.className = 'tree_branch';
            document.getElementById('cohort_tree').appendChild(iDiv);
          }
        }

    function add_node(div){
      var new_id = div.id+"-clone";

      //check so that it only clones once
      if (!document.getElementById(new_id)) {

        //create a tree "branch"
        create_branch();

        $(div).clone().attr('id', new_id).appendTo('#cohort_tree');
        nodecount++;
        
        if(document.getElementById(new_id)) {

          //get the new cloned element
          var x = document.getElementById(new_id);

          //add functions to it
          
        }

      }

    }

      function show_tooltip(val){
        var docHeight = $(document).height(); //grab the height of the page
        var scrollTop = $(window).scrollTop(); //grab the px value from the top of the page to where you're scrolling
        $('.overlay-bg').show().css({'height' : docHeight}); //display your popup background and set height to the page height
        $('.popuptooltip'+val).show().css({'top': scrollTop+20+'px'}); //show the appropriate popup and set the content 20px from the window top
      }


      $(document).ready( function() {
       
        

        $('.attr').click(function(event){
          event.preventDefault(); // disable normal link function so that it doesn't refresh the page
          var docHeight = $(document).height(); //grab the height of the page
          var scrollTop = $(window).scrollTop(); //grab the px value from the top of the page to where you're scrolling
          var selectedPopup = $(this).data('showpopup'); //get the corresponding popup to show
           
          $('.overlay-bg').show().css({'height' : docHeight}); //display your popup background and set height to the page height
          $('.popup'+selectedPopup).show().css({'top': scrollTop+20+'px'}); //show the appropriate popup and set the content 20px from the window top
          add_node(this);
      });
     
      // hide popup when user clicks on close button or if user clicks anywhere outside the container
      $('.close-btn, .overlay-bg').click(function(){
          $('.overlay-bg, .overlay-content').hide(); // hide the overlay
      });
       
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
      <li class='active' id='alltab'> <a href="#All" data-toggle="tab">All</a></li>
      <li id='surgerytab'><a href="#Surgical" data-toggle="tab">Surgical Oncology</a></li>
      <li id='radiationtab'><a href="#Radiation" data-toggle="tab">Radiation Oncology</a></li>
      <li id='medicaltab'><a href="#Medical" data-toggle="tab">Medical Oncology</a></li>
       
     </ul>

    <img src="images/help.png" alt="alternative text" title="The Cohort Tree shows attributes you've set values for and filtered your cohort's with. Any attribute not in the Cohort Tree will not be used to filter your cohorts." style = "width:10px;height:10px;cursor:pointer;" onclick="show_tooltip(1);"/>
     <div class='tab-content'>

      <div class="tab-pane active" id="All">
        
        <div id ="All-all">
          <div id="age"class = "attr" href="#" data-showpopup="1">Age</div>
          <div id="site"class = "attr" href="#" data-showpopup="2">Site</div>
          <div id="diagnosis_date"class = "attr">Diagnosis Date</div>
          <div id="grade"class = "attr">Grade</div>
          <div id="behaviour"class = "attr">Behaviour</div>
        </div>
        <div id = "All-surgery">
          <div id="recurrence"class = "attrsurgery">Recurrence</div>
          <div id="tstaging"class = "attrsurgery">T Staging</div>
          <div id="mstaging"class = "attrsurgery">M Staging </div>
          <div id="nstaging"class = "attrsurgery">N Staging</div>
        </div>
        <div id = "All-radiation">
          <div id="nodes"class = "attrradiation">Nodes</div>
          <div id="histology"class = "attrradiation">Histology</div>
          <div id="progesterone_receptor"class = "attrradiation">Progesterone Receptor</div>
        </div>
        <div id = "All-medical">
          <div id="menopause"class = "attrmedical">Menopause</div>
          <div id="estrogen_receptor"class = "attrmedical">Estrogen Receptor</div>
          <div id="her2"class = "attrmedical">Her2</div>
        </div>

      </div>

      <div class="tab-pane"  id="Surgical" collapsible=true>
        <div id ="All-all">
          <div id="age"class = "attr">Age</div>
          <div id="site"class = "attr">Site</div>
          <div id="diagnosis_date"class = "attr">Diagnosis Date</div>
          <div id="grade"class = "attr">Grade</div>
          <div id="behaviour"class = "attr">Behaviour</div>
        </div>
        <div id = "All-surgery">
          <div id="recurrence"class = "attrsurgery">Recurrence</div>
          <div id="tstaging"class = "attrsurgery">T Staging</div>
          <div id="mstaging"class = "attrsurgery">M Staging </div>
          <div id="nstaging"class = "attrsurgery">N Staging</div>
        </div>
      </div>

      <div class="tab-pane" id="Radiation">
         <div id ="All-all">
          <div id="age"class = "attr">Age</div>
          <div id="site"class = "attr">Site</div>
          <div id="diagnosis_date"class = "attr">Diagnosis Date</div>
          <div id="grade"class = "attr">Grade</div>
          <div id="behaviour"class = "attr">Behaviour</div>
        </div>
       
        <div id = "All-radiation">
          <div id="nodes"class = "attrradiation">Nodes</div>
          <div id="histology"class = "attrradiation">Histology</div>
          <div id="progesterone_receptor"class = "attrradiation">Progesterone Receptor</div>
        </div>
        
      </div>

      <div class="tab-pane" id="Medical">
        <div id ="All-all">
          <div id="age"class = "attr">Age</div>
          <div id="site"class = "attr">Site</div>
          <div id="diagnosis_date"class = "attr">Diagnosis Date</div>
          <div id="grade"class = "attr">Grade</div>
          <div id="behaviour"class = "attr">Behaviour</div>
        </div>
        <div id = "All-medical">
          <div id="menopause"class = "attrmedical">Menopause</div>
          <div id="estrogen_receptor"class = "attrmedical">Estrogen Receptor</div>
          <div id="her2"class = "attrmedical">Her2</div>
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
    <p style="display:inline-block;">Cohort Filters</p>
    <img src="images/help.png" alt="alternative text" title="The Cohort Tree shows attributes you've set values for and filtered your cohort's with. Any attribute not in the Cohort Tree will not be used to filter your cohorts." style = "width:10px;height:10px;cursor:pointer;" onclick="show_tooltip(2);"/>

    <div id="cohort_tree">
     
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

<div class="overlay-content popup1">
    <p class = "popuptitle">Set Age Range</p>
    <p class = "inputtag">Min Age</p>
    <input type="number" name = "min_age" value = "0"></input>

    <p class = "inputtag">Max Age</p>
    <input type="number" name = "max_age" value = "110"></input>
    <button class="close-btn">Close</button>
</div>

<div class="overlay-content popup2">
    <p class = "popuptitle">Choose Site</p>
    <p class = "inputtag">Site</p>

    <!-- pull this from db -->
    <select>
      <option value="volvo">Volvo</option>
      <option value="saab">Saab</option>
      <option value="mercedes">Mercedes</option>
      <option value="audi">Audi</option>
    </select>

    <button class="close-btn">Close</button>
</div>

<!-- start of visualizations-->
<div class="tabbable" >
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
