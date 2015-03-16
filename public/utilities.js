
//keep track of how many nodes are in the tree
var nodecount= 0;
//keep track to see if an attr has been duplicated
var duplicated_id = "none";

var being_reset=false;

function create_branch(){
  if(nodecount>0){


    var iDiv = document.createElement('div');
    iDiv.id = 'tree_branch'+nodecount;
    iDiv.className = 'tree_branch';
    document.getElementById('cohort_tree').appendChild(iDiv);
  }
}
//add node to cohort tree
function add_node(div){
  if(div.id.indexOf("-clone") == -1){
    var new_id = div.id+"-clone";

    //check so that it only clones once
    if (!document.getElementById(new_id)) {

      //create a tree "branch"
      create_branch();
      //.clone( [withDataAndEvents] [, deepWithDataAndEvents] )
      $(div).clone(true).attr('id', new_id).appendTo('#cohort_tree');

      //store the nodecount for removing branches
      $('#'+new_id).data('nodecount', nodecount);
      //increment nodecount
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
  

  //TODO ADD VISUAL INDICATION THAT NODE HAS BEEN DUPLICATED
  function handle_duplicate(div){
    //if nothing is duplicated, duplicate and hide all duplicate buttons
    if(duplicated_id=="none"){
      duplicated_id=div.id;
      //alert('duplicated id: '+duplicated_id);
      //hide the duplicate buttons
      var duplicate_btns = document.getElementsByClassName('duplicate-btn'), i;

      for (var i = 0; i < duplicate_btns.length; i ++) {
          duplicate_btns[i].style.display = 'none';
      }
      
      //duplicate the attribute in the tree and create a new value input window
      var popup_id = div.id.substring(0,div.id.indexOf('-duplicate'));
      $('#overlay-'+popup_id+'content-cohort2').show();

      //find the node in the cohort tree and indicate it's been duplicated
      $('.attr,.attrsurgery,.attrradiation,.attrmedical').each(function(){
        if($(this).data("showpopup")==popup_id&&this.id.indexOf("clone")!=-1){
            var iDiv = document.createElement('div');
            iDiv.id = 'star';
            iDiv.className = 'star';
            this.appendChild(iDiv);

        }
      });
     
    }
    //if something has been duplicated, remove duplicated node and make duplicate buttons available
    else{
      duplicated_id="none";
      //document.getElementsByClassName('duplicate-btn')[0].style.visibility='visible';
      var duplicate_btns = document.getElementsByClassName('duplicate-btn'), i;
      

      for (var i = 0; i < duplicate_btns.length; i ++) {
          duplicate_btns[i].style.display = 'inline';
      }

      var popup_id = div.id.substring(0,div.id.indexOf('-duplicate'));
     
      $('#overlay-'+popup_id+'content-cohort2').hide();
      $('.attr,.attrsurgery,.attrradiation,.attrmedical').each(function(){
        if($(this).data("showpopup")==popup_id&&this.id.indexOf("clone")!=-1){
          var star = document.getElementById('star');
          this.removeChild(star);
        }
      });

    }
  }

  function reset_attribute_values(div){
    var popup = $(div).data('showpopup')
    being_reset=true;
    //not the best way to do this but oh well
    //however this should handle the reseting of duplicates since their value will be changed as well
    switch(popup){
      case "Age":
        $("input[name*=cohort1-age]").each(function(){
            this.value = this.defaultValue;
        });
        break;
      case "Diagnosis_Date":
        
        $("input[name*=cohort1-br_dx]").each(function(){
            this.value = this.defaultValue;
        });
        break;
      case "SRM_Date":
         $("input[name*=cohort1-min_srm]").each(function(){
            this.value = this.defaultValue;
        });
        break;
      case "TNM_Staging":

       
        $('input[type=checkbox]').each(function(){
            if(this.id.indexOf("cohort1-tnm_t")!=-1)
              if(this.defaultValue=="on")
                this.checked=true;
              else
                this.checked=false;
        });
         $('input[type=checkbox]').each(function(){
            if(this.id.indexOf("cohort1-tnm_n")!=-1)
               if(this.defaultValue=="on")
                this.checked=true;
              else
                this.checked=false;
        });
          $('input[type=checkbox]').each(function(){
            if(this.id.indexOf("cohort1-tnm_m")!=-1)
              if(this.defaultValue=="on")
                this.checked=true;
              else
                this.checked=false;
        });

        break;
      case "PGR":
        
        $('input[type=checkbox]').each(function(){
            if(this.id.indexOf("cohort1-br_pgr")!=-1)
               if(this.defaultValue=="on")
                this.checked=true;
              else
                this.checked=false;
        });

        break;
      case "Nodes":
        
        $('input[type=checkbox]').each(function(){
            if(this.id.indexOf("cohort1-br_num_pos")!=-1)
               if(this.defaultValue=="on")
                this.checked=true;
              else
                this.checked=false;
        });

        break;
      case "Meno_Status":
        
        $('input[type=checkbox]').each(function(){
            if(this.id.indexOf("cohort1-meno")!=-1)
               if(this.defaultValue=="on")
                this.checked=true;
              else
                this.checked=false;
        });

        break;
      case "Hist":
        
        $('input[type=checkbox]').each(function(){
            if(this.id.indexOf("cohort1-hist")!=-1)
               if(this.defaultValue=="on")
                this.checked=true;
              else
                this.checked=false;
        });

        break;
      case "Her2":
        
        $('input[type=checkbox]').each(function(){
            if(this.id.indexOf("cohort1-br_her2")!=-1)
               if(this.defaultValue=="on")
                this.checked=true;
              else
                this.checked=false;
        });

        break;
      case "Site":
        
        $('input[type=checkbox]').each(function(){
            if(this.id.indexOf("cohort1-site")!=-1)
               if(this.defaultValue=="on")
                this.checked=true;
              else
                this.checked=false;
        });

        break;
      case "Immuno_Stains":
        
        $('input[type=checkbox]').each(function(){
            if(this.id.indexOf("cohort1-br_immuno")!=-1)
               if(this.defaultValue=="on")
                this.checked=true;
              else
                this.checked=false;
        });

        break;
      case "Grade":
        
        $('input[type=checkbox]').each(function(){
            if(this.id.indexOf("cohort1-grade")!=-1)
               if(this.defaultValue=="on")
                this.checked=true;
              else
                this.checked=false;
        });

        break;
      case "Behaviour":
        
        $('input[type=checkbox]').each(function(){
            if(this.id.indexOf("cohort1-behavior")!=-1)
               if(this.defaultValue=="on")
                this.checked=true;
              else
                this.checked=false;
        });

        break;
      case "Radiation":
        
        $('input[type=checkbox]').each(function(){
            if(this.id.indexOf("cohort1-bcca_rad")!=-1)
               if(this.defaultValue=="on")
                this.checked=true;
              else
                this.checked=false;
        });

        break;
      case "Chemo":
        
        $('input[type=checkbox]').each(function(){
            if(this.id.indexOf("cohort1-chemo")!=-1)
               if(this.defaultValue=="on")
                this.checked=true;
              else
                this.checked=false;
        });

        break;
      case "Surgery":
        
        $('input[type=checkbox]').each(function(){
            if(this.id.indexOf("cohort1-bcca_surg")!=-1)
               if(this.defaultValue=="on")
                this.checked=true;
              else
                this.checked=false;
        });

        break;

      default:
        break;

    }

    being_reset = false;
    
  }

  function repopulate_filters(){
    var list = $("input[name='filter_list']").val();
    var arr = list.split(',');
    
    $(arr).each(function() { 
      var id = this.replace("-clone","");

      var div = document.getElementById(id);
      
      add_node(div);
    });

    //repopulate handle duplicate as well
    var did = $("input[name='duplicated_id']").val();
    //handle_duplicate(document.getElementById(did));
    
     
  }

  function mySubmit(){
    var filter_list = ""
    $("[id$='-clone']").each(function() {

      filter_list+=this.id+",";
    });
    filter_list=filter_list.substring(0,filter_list.length-1);
    $("input[name='filter_list']").val(filter_list);
    $("input[name='duplicated_id']").val(duplicated_id);
    
    
    document.forms['myform'].submit();
  }

  function check_duplicate_on_removal(div){
    var removed_div = div.id.substring(0,div.id.indexOf("-clone")).toLowerCase();
    var duplicated_div = duplicated_id.substring(0,duplicated_id.indexOf("-duplicate")).toLowerCase();
    
    if(removed_div==duplicated_div)
    {
      
      //document.getElementsByClassName('duplicate-btn')[0].style.visibility='visible';
      var duplicate_btns = document.getElementsByClassName('duplicate-btn'), i;
      

      for (var i = 0; i < duplicate_btns.length; i ++) {
          duplicate_btns[i].style.display = 'inline';
      }

      var popup_id = duplicated_id.substring(0,duplicated_id.indexOf('-duplicate'));
     
      $('#overlay-'+popup_id+'content-cohort2').hide();
      duplicated_id="none";
    }
  }
  function updateTitle(div){
    var popup = $(div).data('showpopup')
    var title = "";

    //not the best way to do this but oh well
    switch(popup){
      case "Age":
        title="Min: "+ $("input[name=cohort1-age_at_diagnosis-min]").val()+" Max: "+ $("input[name=cohort1-age_at_diagnosis-max]").val();
        break;
      case "Diagnosis_Date":
        title="Min: "+ $("input[name=cohort1-br_dx_date-min]").val()+" Max: "+ $("input[name=cohort1-br_dx_date-max]").val();
        break;
      case "SRM_Date":
        
        title += "Min: "+ $("input[name=cohort1-min_srm-min]").val()+ " Max: "+  $("input[name=cohort1-min_srm-max]").val();
        break;
      case "TNM_Staging":

        
        $('input[type=checkbox]').each(function(){
            if(this.id.indexOf("cohort1-tnm_t")!=-1&&this.checked)
              title= title+ this.nextSibling.data+"\n";
        });
         $('input[type=checkbox]').each(function(){
            if(this.id.indexOf("cohort1-tnm_n")!=-1&&this.checked)
              title= title+ this.nextSibling.data+"\n";
        });
          $('input[type=checkbox]').each(function(){
            if(this.id.indexOf("cohort1-tnm_m")!=-1&&this.checked)
              title= title+ this.nextSibling.data+"\n";
        });

        break;
      case "PGR":
        
        $('input[type=checkbox]').each(function(){
            if(this.id.indexOf("cohort1-br_pgr")!=-1&&this.checked)
              title= title+ this.nextSibling.data+"\n";
        });

        break;
      case "Nodes":
        
        $('input[type=checkbox]').each(function(){
            if(this.id.indexOf("cohort1-br_num_pos")!=-1&&this.checked)
              title= title+ this.nextSibling.data+"\n";
        });

        break;
      case "Meno_Status":
        
        $('input[type=checkbox]').each(function(){
            if(this.id.indexOf("cohort1-meno")!=-1&&this.checked)
              title= title+ this.nextSibling.data+"\n";
        });

        break;
      case "Hist":
        
        $('input[type=checkbox]').each(function(){
            if(this.id.indexOf("cohort1-hist")!=-1&&this.checked)
              title= title+ this.nextSibling.data+"\n";
        });

        break;
      case "Her2":
        
        $('input[type=checkbox]').each(function(){
            if(this.id.indexOf("cohort1-br_her2")!=-1&&this.checked)
              title= title+ this.nextSibling.data+"\n";
        });

        break;
      case "Site":
        
        $('input[type=checkbox]').each(function(){
            if(this.id.indexOf("cohort1-site")!=-1&&this.checked)
              title= title+ this.nextSibling.data+"\n";
        });

        break;
      case "Immuno_Stains":
        
        $('input[type=checkbox]').each(function(){
            if(this.id.indexOf("cohort1-br_immuno")!=-1&&this.checked)
              title= title+ this.nextSibling.data+"\n";
        });

        break;
      case "Grade":
        
        $('input[type=checkbox]').each(function(){
            if(this.id.indexOf("cohort1-grade")!=-1&&this.checked)
              title= title+ this.nextSibling.data+"\n";
        });

        break;
      case "Behaviour":
        
        $('input[type=checkbox]').each(function(){
            if(this.id.indexOf("cohort1-behavior")!=-1&&this.checked)
              title= title+ this.nextSibling.data+"\n";
        });

        break;
      case "Radiation":
        
        $('input[type=checkbox]').each(function(){
            if(this.id.indexOf("cohort1-bcca_rad")!=-1&&this.checked)
              title= title+ this.nextSibling.data+"\n";
        });

        break;
      case "Chemo":
        
        $('input[type=checkbox]').each(function(){
            if(this.id.indexOf("cohort1-chemo")!=-1&&this.checked)
              title= title+ this.nextSibling.data+"\n";
        });

        break;
      case "Surgery":
        
        $('input[type=checkbox]').each(function(){
            if(this.id.indexOf("cohort1-bcca_surg")!=-1&&this.checked)
              title= title+ this.nextSibling.data+"\n";
        });

        break;

      default:
        title = "";

    }
    div.title = title;
  }
  
  function get_keyfactor(div, cohort){
    var popup = $(div).data('showpopup')
    var title = "";

    //not the best way to do this but oh well
    switch(popup){
      case "Age":
        title="Min: "+ $("input[name="+cohort+"-age_at_diagnosis-min]").val()+" Max: "+ $("input[name="+cohort+"-age_at_diagnosis-max]").val();
        break;
      case "Diagnosis_Date":
        title="Min: "+ $("input[name="+cohort+"-br_dx_date-min]").val()+" Max: "+ $("input[name="+cohort+"-br_dx_date-max]").val();
        break;
      case "SRM_Date":
        
        title += "Min: "+ $("input[name="+cohort+"-min_srm-min]").val()+ " Max: "+  $("input[name="+cohort+"-min_srm-max]").val();
        break;
      case "TNM_Staging":

        
        $('input[type=checkbox]').each(function(){
            if(this.id.indexOf(""+cohort+"-tnm_t")!=-1&&this.checked)
              title= title+ this.nextSibling.data+"\n";
        });
         $('input[type=checkbox]').each(function(){
            if(this.id.indexOf(""+cohort+"-tnm_n")!=-1&&this.checked)
              title= title+ this.nextSibling.data+"\n";
        });
          $('input[type=checkbox]').each(function(){
            if(this.id.indexOf(""+cohort+"-tnm_m")!=-1&&this.checked)
              title= title+ this.nextSibling.data+"\n";
        });

        break;
      case "PGR":
        
        $('input[type=checkbox]').each(function(){
            if(this.id.indexOf(""+cohort+"-br_pgr")!=-1&&this.checked)
              title= title+ this.nextSibling.data+"\n";
        });

        break;
      case "Nodes":
        
        $('input[type=checkbox]').each(function(){
            if(this.id.indexOf(""+cohort+"-br_num_pos")!=-1&&this.checked)
              title= title+ this.nextSibling.data+"\n";
        });

        break;
      case "Meno_Status":
        
        $('input[type=checkbox]').each(function(){
            if(this.id.indexOf(""+cohort+"-meno")!=-1&&this.checked)
              title= title+ this.nextSibling.data+"\n";
        });

        break;
      case "Hist":
        
        $('input[type=checkbox]').each(function(){
            if(this.id.indexOf(""+cohort+"-hist")!=-1&&this.checked)
              title= title+ this.nextSibling.data+"\n";
        });

        break;
      case "Her2":
        
        $('input[type=checkbox]').each(function(){
            if(this.id.indexOf(""+cohort+"-br_her2")!=-1&&this.checked)
              title= title+ this.nextSibling.data+"\n";
        });

        break;
      case "Site":
        
        $('input[type=checkbox]').each(function(){
            if(this.id.indexOf(""+cohort+"-site")!=-1&&this.checked)
              title= title+ this.nextSibling.data+"\n";
        });

        break;
      case "Immuno_Stains":
        
        $('input[type=checkbox]').each(function(){
            if(this.id.indexOf(""+cohort+"-br_immuno")!=-1&&this.checked)
              title= title+ this.nextSibling.data+"\n";
        });

        break;
      case "Grade":
        
        $('input[type=checkbox]').each(function(){
            if(this.id.indexOf(""+cohort+"-grade")!=-1&&this.checked)
              title= title+ this.nextSibling.data+"\n";
        });

        break;
      case "Behaviour":
        
        $('input[type=checkbox]').each(function(){
            if(this.id.indexOf(""+cohort+"-behavior")!=-1&&this.checked)
              title= title+ this.nextSibling.data+"\n";
        });

        break;
      case "Radiation":
        
        $('input[type=checkbox]').each(function(){
            if(this.id.indexOf(""+cohort+"-bcca_rad")!=-1&&this.checked)
              title= title+ this.nextSibling.data+"\n";
        });

        break;
      case "Chemo":
        
        $('input[type=checkbox]').each(function(){
            if(this.id.indexOf(""+cohort+"-chemo")!=-1&&this.checked)
              title= title+ this.nextSibling.data+"\n";
        });

        break;
      case "Surgery":
        
        $('input[type=checkbox]').each(function(){
            if(this.id.indexOf(""+cohort+"-bcca_surg")!=-1&&this.checked)
              title= title+ this.nextSibling.data+"\n";
        });

        break;

      default:
        title = "";

    }
    return title;
  }

  function update_key_factor()
  {
    
    var did = $("input[name='duplicated_id']").val().replace("-duplicate","");
    var keyfactor1 = get_keyfactor($(did),"cohort1");
    var keyfactor2 = get_keyfactor($(did),"cohort2");
    //alert(did+keyfactor1+"\n 2) "+keyfactor2);
  }

  $(document).ready( function() {
     
     //set up the mouseover for attributes
    $( ".attr,.attrsurgery,.attrradiation,.attrmedical" ).mouseover(function() {
        updateTitle(this);
    });

    //no rightclick context menu for attributes
    $('.attr,.attrsurgery,.attrradiation,.attrmedical,#cohort_tree').bind('contextmenu', function(e){
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
              //remove the corresponding tree branch
              var branch_num = $('#'+this.id).data('nodecount');
              $('#tree_branch'+branch_num).remove();

              //handle if a duplicated node is removed
              check_duplicate_on_removal(this);
              //reset values for the removed attribute (not neccessary)
              reset_attribute_values(this);
              //remove the node, decrement the nodecount
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

    //when cohort 1's value is changed, change cohort 2's value as well (if it's not been duplicated)
    $("input[name^='cohort1']").change(function(event) {
      //get the parentnode
      var pnode = this.parentNode;
      while(pnode.id.indexOf("overlay-")==-1){
        pnode = pnode.parentNode;
      }
      pnode_id=pnode.id.replace("overlay-","").replace("content","-duplicate");
      
      if( (pnode_id!=duplicated_id&&being_reset==false) || being_reset==true){
        var name = this.name;
        name = name.replace("cohort1", "cohort2");
       // alert(name);
        $("input[name="+name+"]").val(this.value);
        
        if($(this).attr('checked')){
          $("input[name="+name+"]").prop('checked', true);
          }
        else{
          $("input[name="+name+"]").prop('checked', false);
        }
      }        
      

    });

    //handle when the duplicate button is pressed
    $('.duplicate-btn, .un-duplicate-btn').click(function(){
        handle_duplicate(this);
    });


    //set all the default values for resetting purposes
    $("form#myform :input").each(function(){
       this.defaultValue = $(this).val();
       //alert(this.defaultValue);
    });

    //repopulate duplicates
   

    /*
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
    */

});
$(window).load(function() {
   // executes when complete page is fully loaded, including all frames, objects and images
  repopulate_filters(); 

  update_key_factor();
});

$(function() {
  $( "#tabs" ).tabs({
    collapsible: true
  });

});