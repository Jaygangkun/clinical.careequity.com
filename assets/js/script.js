$(document).ready(function(){
	$(".form-control").click(function(){
		$(this).parents().addClass("focused");
	});

	$(".clear_filter_btn").click(function(){
		var current_btn_id = $(this).attr("ID");
		console.log(current_btn_id);
		switch (current_btn_id) {
			case "clear_filter_about":
			  $("#search-content_about").val('').empty();
			  $("#search-content_about").focus();
			  break;
			case "clear_filter_employment":
				$("#search-content_employment").val('').empty();
				$("#search-content_employment").focus();
			  break;
			case "clear_filter_education":
				$("#search-content_education").val('').empty();
				$("#search-content_education").focus();
			  break;
			case "clear_filter_endorsements":
				$("#search-content_endorsements").val('').empty();
				$("#search-content_endorsements").focus();
			  break;
			default:
			  break;
		  }
	});

	$(".sort_wrap").click(function(){

		//table Sort

		var table=$('#table');
		var tbody =$('#table1');

		tbody.find('tr').sort(function(a, b) 
		{
			if($('#school_order').val()=='asc') 
			{
				return $('.name', a).text().localeCompare($('.name', b).text());
			}
			else 
			{
				return $('.name', b).text().localeCompare($('.name', a).text());
			}
				
		}).appendTo(tbody);
			
		var sort_order=$('#school_order').val();
		if(sort_order=="asc")
		{
			$('#school_order').val("desc");
		}

		if(sort_order=="desc")
		{
			$('#school_order').val("asc");
		}


		//change icon
		/*
		if ($(this).hasClass("fa-chevron-down")) {
			$(this).removeClass("fa-chevron-down");
			$(this).addClass("fa-chevron-up");
		}else if ($(this).hasClass("fa-chevron-up")){
			$(this).removeClass("fa-chevron-up");
			$(this).addClass("fa-chevron-down");
		}
		*/
	});

	//checking weekly row

	$( "#table_list tbody .list_weekly_container " ).each(function( index ) {
		if ($(this).text() != ""){
			$(this).parent().parent().addClass("weekly_checked_row");
		}
	});


	//add selected class
	
	$(document).on('click', '#table_list1 tr', function(event){ 
		$( "#table_list1 tr" ).each(function( index ) {
			if ($(this).hasClass("selected"))
				$(this).removeClass("selected");
		  });

		  $(this).addClass("selected");

		  $("#table_insert").css("display", "none");
		  $("#table").css("display", "table");
		  
		  var selected_title = $(this).find(".input_list_name").val();


		  $(".showing_list_title").text(selected_title);
		// added disabled all input list select
		$( ".input_list_name" ).each(function( index ) {
			
			$(this).prop("disabled", true);
		});
   });

   $(document).on('click', '#table1 tr', function(event){ 
		$( "#table1 tr" ).each(function( index ) {
			if ($(this).hasClass("selected"))
				$(this).removeClass("selected");
		});

		$(this).addClass("selected");
		
	});



	// Delete Row
	 
	$(document).on('click', '.delete', function(event){ 
		var el = this;
		
		// Delete id
		var deleteid = $(this).data('id');
		
		// Confirm box
		bootbox.confirm("Do you really want to delete row?", function(result) {
		
			if(result){

			}
		
		});
   });


   //Click weekly report btn
   $(document).on('click', '.weekly_report_btn', function(event){ 
	
		if($(this).attr("data-toggle") == "true"){
			$(this).attr("data-toggle", "false");
			
			if($(this).parent().parent().parent().parent().parent().parent().hasClass("weekly_checked_row")){
				$(this).parent().parent().parent().parent().parent().parent().removeClass("weekly_checked_row")
			}

			$(this).parent().parent().parent().parent().parent().parent().find(".list_weekly div").removeClass();
			$(this).parent().parent().parent().parent().parent().parent().find(".list_weekly div").addClass("list_weekly_container");
			$(this).parent().parent().parent().parent().parent().parent().find(".list_weekly div").addClass("state_none");
			$(this).parent().parent().parent().parent().parent().parent().find(".list_weekly div").text("");

		}else {
			$(this).attr("data-toggle", "true");
			
			if(!$(this).parent().parent().parent().parent().parent().parent().hasClass("weekly_checked_row")){
				$(this).parent().parent().parent().parent().parent().parent().addClass("weekly_checked_row")
			}
			
			$(this).parent().parent().parent().parent().parent().parent().find(".list_weekly div").removeClass();
			$(this).parent().parent().parent().parent().parent().parent().find(".list_weekly div").addClass("list_weekly_container");
			$(this).parent().parent().parent().parent().parent().parent().find(".list_weekly div").addClass("state_active");
			$(this).parent().parent().parent().parent().parent().parent().find(".list_weekly div").text("Active");
		}


	});




	//Edit possible

	$(document).on('click', '.edit_btn', function(event){ 
		$( ".input_list_name" ).each(function( index ) {
			$(this).prop("disabled", true);			
		});
		event.stopPropagation();
		$(this).parent().find(".input_list_name").prop("disabled", false);
		$(this).parent().find(".input_list_name").focus(); 
   });

	$(document).on('click', '.input_list_name', function(event){ 
		event.stopPropagation();
   	});

   $(document).on('keypress', '.input_list_name', function(e){ 
	if (e.which == 13) {
		$(this).prop("disabled", true);
		 return false;    //<---- Add this line
	   }
	});

	

	//add new row on list

	

	$(".new_list_btn").click(function(event){
		$( "#table_list1 tr" ).each(function( index ) {
		if ($(this).hasClass("selected"))
			$(this).removeClass("selected");
		});

	
		var d = new Date();
		var n = d.getDate();
		var m = d.getMonth();
		var y = d.getFullYear();
		var created_date = m +'/'+ n +'/'+ y;
		var new_row = '<tr class="table_row selected">';
		new_row += '<td class="check_td">';
		new_row += '<ul class="header-dropdown left-pop_menu m-r--5">';
		new_row += '<li class="dropdown">';
		new_row += '<a href="javascript:void(0);" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="true">';		
		new_row += '<i class="material-icons">more_vert</i>';
		new_row += '</a>';
		new_row += '<ul class="dropdown-menu ">';
		new_row += '<li><a href="javascript:void(0);" class=" waves-effect waves-block weekly_report_btn" data-toggle="false"><i class="material-icons">check_circle</i>Reporting</a><hr class="menu_hr"></li>';
		new_row += '<li><a href="javascript:void(0);" class=" waves-effect waves-block duplicate_btn"><i class="material-icons">file_copy</i>Duplicate</a></li>';
		new_row += '<li><a href="javascript:void(0);" class=" waves-effect waves-block download_btn"><i class="material-icons">file_download</i>Export CSV</a><hr class="menu_hr"></li>';
		new_row += '<li><a href="javascript:void(0);" class=" waves-effect waves-block delete_btn"><i class="material-icons">delete</i>Delete</a></li>';
		new_row += '</ul>';
		new_row += '</li>';
		new_row += '</ul>';
		new_row += '</td>';
		new_row += '<td class="list_title"> <input type="text" name="developer" id="item" value="'+ $(".input_list").val()+'" disabled="disabled" class="input_list_name"/> <i class="material-icons edit_btn">edit</i></td>';
		new_row += '<td class="list_weekly">';
		new_row += '<div class="list_weekly_container state_none"></div>';
		new_row += '</td>';
		new_row += '<td class="updated_time">'+ created_date +'</td>';
		new_row += '<td class="created_time">'+ created_date +'</td>';
		new_row += '<td class="select_triangle"> <i class="material-icons">double_arrow</i></td>';
		new_row += '</tr>';		
		
	
		$("table tbody#table_list1").prepend(new_row);


		$("#table_insert").css("display", "block");
		$("#table").css("display", "none");


		//show title to right header
		var selected_title = $(".input_list").val();


		$(".showing_list_title").text(selected_title);
		
	});


	//when typing the title to right header
	$(".selected .input_list_name").on("keyup", function() {
		var value = $(this).val();
		$(".showing_list_title").text(value);
	});
	

	//add new url
	$(".add_link_btn").click(function(event){ 

		
		
		var new_url = '<tr class="table_row selected">';
		new_url += '<td class="check_td url_check_td">';
		new_url += '<ul class="header-dropdown left-pop_menu m-r--5">';
		new_url += '<li class="dropdown">';
		new_url += '<a href="javascript:void(0);" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="true">';
		new_url += '<i class="material-icons">more_vert</i>';
		new_url += '</a>';
		new_url += '<ul class="dropdown-menu ">	';
		new_url += '<li><a href="javascript:void(0);" class=" waves-effect waves-block delete_btn delete"><i class="material-icons">delete</i>Delete</a></li>';
		new_url += '</ul>';
		new_url += '</li>';
		new_url += '</ul>';
		new_url += '</td>';
		new_url += '<td class="list_title"> <input type="text" name="developer" id="item" value="'+ $(".input_linkedin_url").val()+'"  class="input_list_name"/> <i class="material-icons edit_btn">edit</i></td>			';
		new_url += '</tr>';
					
				
		$("table#table_insert tbody").prepend(new_url);

		$("#table_insert").css("display", "block");
		$("#table").css("display", "none");
	});


	 //Search Field


	 $( ".search_btn" ).on( "click", function() {

		
		var search_key = $(".search_field").val().toLowerCase();
	
		//document.getElementByClass("search_field").innerHTML = x;
	
		if (search_key != ""){
		  $(".close_search_btn").css("display", "block");
		  
		 
		  $("#table1 tr").filter(function() {
			$(this).toggle($(this).text().toLowerCase().indexOf(search_key) > -1)
		  });
		  
	
		}
	  
	
	  });
	
	
	  $( ".close_search_btn" ).on( "click", function() { 
		
		
		$(".search_field").val("");
		
		$(this).css("display","none");
	
		$("#table1 tr").filter(function() {
			$(this).toggle($(this).text().toLowerCase().indexOf("") > -1)
		  });
	
	  });
	
	
	
	  $('.search_field').keypress(function (e) {
		var key = e.which;
		if(key == 13)  // the enter key code
		{
			$(".search_btn").trigger("click");
			return false;  
		}
	  }); 	

});


function closeAllLists(elmnt){
    /*close all autocomplete lists in the document,
    except the one passed as an argument:*/
    var x = document.getElementsByClassName("form-control");
    for (var i = 0; i < x.length; i++) {
		if (elmnt != x[i]) {
			x[i].parentNode.classList.remove("focused");
		}
	}
}
/*execute a function when someone clicks in the document:*/
document.addEventListener("click", function (e) {
    closeAllLists(e.target);
});

function chevronUpDownToggle(chevron) {
	if(chevron.classList.contains("fa-chevron-down")) {
		chevron.classList.remove("fa-chevron-down");
		chevron.classList.add("fa-chevron-up");				
	} else if(chevron.classList.contains("fa-chevron-up")) {
		chevron.classList.remove("fa-chevron-up");
		chevron.classList.add("fa-chevron-down");					
	}
}

function mediaQuery(x) {
	if (x.matches) {
	
		var chevron = document.getElementById("chevron");
		var table = document.getElementById("table");
		var counter = table.children[0].children[0].children;
		var th = table.children[0].children[0].children;
		var tr = table.children[1].children;
		var td = table.children[1].children[0].children;	
		
		for (var i = 3; i < counter.length; i++){
			th[i].classList.add("hide");
		}			
		for (var i = 0; i < tr.length; i++){
			for (var j = 3; j < td.length; j++){
				tr[i].children[j].classList.add("hide");
			}
		}	



		var chevron_left = document.getElementById("chevron_left");
		var table_list = document.getElementById("table_list");
		var counter_list = table_list.children[0].children[0].children;
		var th_list = table_list.children[0].children[0].children;
		var tr_list = table_list.children[1].children;
		var td_list = table_list.children[1].children[0].children;	
		
		for (var i = 3; i < counter_list.length; i++){
			th_list[i].classList.add("hide");
		}			
		for (var i = 0; i < tr_list.length; i++){
			for (var j = 3; j < td_list.length; j++){
				tr_list[i].children[j].classList.add("hide");
			}
		}	

		$(".table_row").dblclick(function(){
			document.getElementById("main").style.display = "block";
		  	document.getElementById("mySidebar").style.display = "none";
		});
		
		/*
		document.getElementsByClassName("table_row").addEventListener("click", function(){
		  document.getElementById("main").style.display = "block";
		  document.getElementById("mySidebar").style.display = "none";
		});			
		*/
		document.getElementById("show-main").addEventListener("click", function(){
		  document.getElementById("main").style.display = "none";
		  document.getElementById("mySidebar").style.display = "block";
		});			
		
		document.getElementById("chevron").addEventListener("click", function(){
			
			if( $("#chevron").find(".fa").hasClass("fa-chevron-right")  ){
				
				chevron.firstChild.classList.remove("fa-chevron-right");
				chevron.firstChild.classList.add("fa-chevron-left");
				
				for (var i = 0; i < 3; i++){
					th[i].classList.add("hide");
				}			
				for (var i = 0; i < tr.length; i++){
					for (var j = 0; j < (td.length - 3); j++){
						tr[i].children[j].classList.add("hide");
					}
				}	

				for (var i = 3; i < counter.length; i++){
					th[i].classList.remove("hide");
				}			
				for (var i = 0; i < tr.length; i++){
					for (var j = 3; j < td.length; j++){
						tr[i].children[j].classList.remove("hide");
					}
				}					
					
			} else {
				
				
				chevron.firstChild.classList.remove("fa-chevron-left");
				chevron.firstChild.classList.add("fa-chevron-right");
				
				for (var i = 0; i < 3; i++){
					th[i].classList.remove("hide");
				}			
				for (var i = 0; i < tr.length; i++){
					for (var j = 0; j < (td.length - 3); j++){
						tr[i].children[j].classList.remove("hide");
					}
				}	

				for (var i = 3; i < counter.length; i++){
					th[i].classList.add("hide");
				}			
				for (var i = 0; i < tr.length; i++){
					for (var j = 3; j < td.length; j++){
						tr[i].children[j].classList.add("hide");
					}
				}				
				
			}
			
		});	



		document.getElementById("chevron_left").addEventListener("click", function(){
			
			if( $("#chevron_left").find(".fa").hasClass("fa-chevron-right")  ){
				
				chevron_left.firstChild.classList.remove("fa-chevron-right");
				chevron_left.firstChild.classList.add("fa-chevron-left");
				
				for (var i = 0; i < 3; i++){
					th_list[i].classList.add("hide");
				}			
				for (var i = 0; i < tr_list.length; i++){
					for (var j = 0; j < (td_list.length - 3); j++){
						tr_list[i].children[j].classList.add("hide");
					}
				}	

				for (var i = 3; i < counter_list.length; i++){
					th_list[i].classList.remove("hide");
				}			
				for (var i = 0; i < tr_list.length; i++){
					for (var j = 3; j < td_list.length; j++){
						tr_list[i].children[j].classList.remove("hide");
					}
				}					
					
			} else {
				
				
				chevron_left.firstChild.classList.remove("fa-chevron-left");
				chevron_left.firstChild.classList.add("fa-chevron-right");
				
				for (var i = 0; i < 3; i++){
					th_list[i].classList.remove("hide");
				}			
				for (var i = 0; i < tr_list.length; i++){
					for (var j = 0; j < (td_list.length - 3); j++){
						tr_list[i].children[j].classList.remove("hide");
					}
				}	

				for (var i = 3; i < counter_list.length; i++){
					th_list[i].classList.add("hide");
				}			
				for (var i = 0; i < tr_list.length; i++){
					for (var j = 3; j < td_list.length; j++){
						tr_list[i].children[j].classList.add("hide");
					}
				}				
				
			}
			
		});	

		
	} else {
		
		var chevron = document.getElementById("chevron");
		var table = document.getElementById("table");
		var counter = table.children[0].children[0].children;
		var th = table.children[0].children[0].children;
		var tr = table.children[1].children;
		var td = table.children[1].children[0].children;	
		
		document.getElementById("mySidebar").style.display = "block";
		for (var i = 0; i < counter.length; i++){
			th[i].classList.remove("hide");
		}			
		for (var i = 0; i < tr.length; i++){
			for (var j = 0; j < td.length; j++){
				tr[i].children[j].classList.remove("hide");
			}
		}	
		
		
		var chevron_left = document.getElementById("chevron_left");
		var table_list = document.getElementById("table_list");
		var counter_list = table_list.children[0].children[0].children;
		var th_list = table_list.children[0].children[0].children;
		var tr_list = table_list.children[1].children;
		var td_list = table_list.children[1].children[0].children;	
		
		for (var i = 0; i < counter_list.length; i++){
			th_list[i].classList.remove("hide");
		}			
		for (var i = 0; i < tr_list.length; i++){
			for (var j = 0; j < td_list.length; j++){
				tr_list[i].children[j].classList.remove("hide");
			}
		}	


	}
}
var x = window.matchMedia("(max-width: 790px)");
mediaQuery(x);
x.addListener(mediaQuery);


$( window ).resize(function() {
	
	var current_width=  $(window).width();
 
	if (current_width < 790) {

		location.reload();
	
		var chevron = document.getElementById("chevron");
		var table = document.getElementById("table");
		var counter = table.children[0].children[0].children;
		var th = table.children[0].children[0].children;
		var tr = table.children[1].children;
		var td = table.children[1].children[0].children;	
		
		for (var i = 3; i < counter.length; i++){
			th[i].classList.add("hide");
		}			
		for (var i = 0; i < tr.length; i++){
			for (var j = 3; j < td.length; j++){
				tr[i].children[j].classList.add("hide");
			}
		}	
		
		
		var chevron_left = document.getElementById("chevron_left");
		var table_list = document.getElementById("table_list");
		var counter_list = table_list.children[0].children[0].children;
		var th_list = table_list.children[0].children[0].children;
		var tr_list = table_list.children[1].children;
		var td_list = table_list.children[1].children[0].children;	
		
		for (var i = 3; i < counter_list.length; i++){
			th_list[i].classList.add("hide");
		}			
		for (var i = 0; i < tr_list.length; i++){
			for (var j = 3; j < td_list.length; j++){
				tr_list[i].children[j].classList.add("hide");
			}
		}	

		$(".table_row").dblclick(function(){
			document.getElementById("main").style.display = "block";
		  	document.getElementById("mySidebar").style.display = "none";
		});
		
		/*
		document.getElementsByClassName("table_row").addEventListener("click", function(){
		  document.getElementById("main").style.display = "block";
		  document.getElementById("mySidebar").style.display = "none";
		});			
		*/

				
		document.getElementById("show-main").addEventListener("click", function(){
		  document.getElementById("main").style.display = "none";
		  document.getElementById("mySidebar").style.display = "block";
		 
		});			
		
		
		$( "#chevron" ).click(function() {

			if( $("#chevron").find(".fa").hasClass("fa-chevron-right") ){
				
				$("this").find(".fa").removeClass("fa-chevron-right");
				$("this").find(".fa").addClass("fa-chevron-left");
				
				
				for (var i = 0; i < 3; i++){
					th[i].classList.add("hide");
				}			
				for (var i = 0; i < tr.length; i++){
					for (var j = 0; j < (td.length - 3); j++){
						tr[i].children[j].classList.add("hide");
					}
				}	

				for (var i = 3; i < counter.length; i++){
					th[i].classList.remove("hide");
				}			
				for (var i = 0; i < tr.length; i++){
					for (var j = 3; j < td.length; j++){
						tr[i].children[j].classList.remove("hide");
					}
				}					
					
			} else {
				
				console.log("bbbb");
				$("this").find(".fa").removeClass("fa-chevron-left");
				$("this").find(".fa").addClass("fa-chevron-right");
				
				for (var i = 0; i < 3; i++){
					th[i].classList.remove("hide");
				}			
				for (var i = 0; i < tr.length; i++){
					for (var j = 0; j < (td.length - 3); j++){
						tr[i].children[j].classList.remove("hide");
					}
				}	

				for (var i = 3; i < counter.length; i++){
					th[i].classList.add("hide");
				}			
				for (var i = 0; i < tr.length; i++){
					for (var j = 3; j < td.length; j++){
						tr[i].children[j].classList.add("hide");
					}
				}				
				
			}
			
		});	

		$( "#chevron_left" ).click(function() {

			if( $("#chevron_left").find(".fa").hasClass("fa-chevron-right") ){
				
				$("this").find(".fa").removeClass("fa-chevron-right");
				$("this").find(".fa").addClass("fa-chevron-left");
				
				
				for (var i = 0; i < 3; i++){
					th_list[i].classList.add("hide");
				}			
				for (var i = 0; i < tr_list.length; i++){
					for (var j = 0; j < (td_list.length - 3); j++){
						tr_list[i].children[j].classList.add("hide");
					}
				}	

				for (var i = 3; i < counter_list.length; i++){
					th_list[i].classList.remove("hide");
				}			
				for (var i = 0; i < tr_list.length; i++){
					for (var j = 3; j < td_list.length; j++){
						tr_list[i].children[j].classList.remove("hide");
					}
				}					
					
			} else {
				
				
				$("this").find(".fa").removeClass("fa-chevron-left");
				$("this").find(".fa").addClass("fa-chevron-right");
				
				for (var i = 0; i < 3; i++){
					th_list[i].classList.remove("hide");
				}			
				for (var i = 0; i < tr_list.length; i++){
					for (var j = 0; j < (td_list.length - 3); j++){
						tr_list[i].children[j].classList.remove("hide");
					}
				}	

				for (var i = 3; i < counter_list.length; i++){
					th_list[i].classList.add("hide");
				}			
				for (var i = 0; i < tr_list.length; i++){
					for (var j = 3; j < td_list.length; j++){
						tr_list[i].children[j].classList.add("hide");
					}
				}				
				
			}
			
		});	
		
		$("#main").css("display", "none");
	} else {
		
		var chevron = document.getElementById("chevron");
		var table = document.getElementById("table");
		var counter = table.children[0].children[0].children;
		var th = table.children[0].children[0].children;
		var tr = table.children[1].children;
		var td = table.children[1].children[0].children;	
		
		document.getElementById("mySidebar").style.display = "block";
		for (var i = 0; i < counter.length; i++){
			th[i].classList.remove("hide");
		}			
		for (var i = 0; i < tr.length; i++){
			for (var j = 0; j < td.length; j++){
				tr[i].children[j].classList.remove("hide");
			}
		}	
		
		
		
		  

		  $(".table_row").dblclick(function(){
			document.getElementById("main").style.display = "block";
		  	document.getElementById("mySidebar").style.display = "block";
		});

		  var chevron_left = document.getElementById("chevron_left");
		var table_list = document.getElementById("table_list");
		var counter_list = table_list.children[0].children[0].children;
		var th_list = table_list.children[0].children[0].children;
		var tr_list = table_list.children[1].children;
		var td_list = table_list.children[1].children[0].children;	
		
		for (var i = 0; i < counter_list.length; i++){
			th_list[i].classList.remove("hide");
		}			
		for (var i = 0; i < tr_list.length; i++){
			for (var j = 0; j < td_list.length; j++){
				tr_list[i].children[j].classList.remove("hide");
			}
		}	


		  
		  $("#main").css("display", "block");
			
		if ($("#chevron").find(".fa").hasClass("fa-chevron-left")){
			
			$("#chevron").find(".fa").removeClass("fa-chevron-left");
			$("#chevron").find(".fa").addClass("fa-chevron-right");
		}


		if ($("#chevron_left").find(".fa").hasClass("fa-chevron-left")){
			
			$("#chevron").find(".fa").removeClass("fa-chevron-left");
			$("#chevron").find(".fa").addClass("fa-chevron-right");
		}


	}

});
