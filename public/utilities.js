
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
    //keep track to see if an attr has been duplicated
    var duplicated_id = "none";

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
        //to do (possibly unneccessary)
        var showpopup = $(div).data("showpopup").toLowerCase();
        
        //this can be made to work (rename all inputs to include showpopup in name and give them default values)
        $("input[name*='"+showpopup+"']" ).each(function(){
            this.value=this.defaultvalue; alert(this);
          });
        
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
      

	    $(document).ready( function() {
	     
	      

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
                  //reset_attribute_values(this);
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

        //handle when the duplicate button is pressed
        $('.duplicate-btn, .un-duplicate-btn').click(function(){
            handle_duplicate(this);
        });

        
        //select the appropriate attr group based on user type role1=radiation role2 = med and role3 = surg
        

	    });
  	
  