 AOS.init({
 	duration: 800,
 	easing: 'slide'
 });

(function($) {

	"use strict";

	$(window).stellar({
    responsive: true,
    parallaxBackgrounds: true,
    parallaxElements: true,
    horizontalScrolling: false,
    hideDistantElements: false,
    scrollProperty: 'scroll',
    horizontalOffset: 0,
	  verticalOffset: 0
  });

  // Scrollax
  $.Scrollax();


	var fullHeight = function() {

		$('.js-fullheight').css('height', $(window).height());
		$(window).resize(function(){
			$('.js-fullheight').css('height', $(window).height());
		});

	};
	fullHeight();

	// loader
	var loader = function() {
		setTimeout(function() { 
			if($('#ftco-loader').length > 0) {
				$('#ftco-loader').removeClass('show');
			}
		}, 1);
	};
	loader();

	// Scrollax
   $.Scrollax();

	var carousel = function() {
		$('.home-slider').owlCarousel({
	    loop:true,
	    autoplay: true,
	    margin:0,
	    animateOut: 'fadeOut',
	    animateIn: 'fadeIn',
	    nav:false,
	    autoplayHoverPause: false,
	    items: 1,
	    navText : ["<span class='ion-md-arrow-back'></span>","<span class='ion-chevron-right'></span>"],
	    responsive:{
	      0:{
	        items:1,
	        nav:false
	      },
	      600:{
	        items:1,
	        nav:false
	      },
	      1000:{
	        items:1,
	        nav:false
	      }
	    }
		});
		// $('.carousel-work').owlCarousel({
		// 	autoplay: true,
		// 	center: true,
		// 	loop: true,
		// 	items:1,
		// 	margin: 30,
		// 	stagePadding:0,
		// 	nav: true,
		// 	navText: ['<span class="ion-ios-arrow-back">', '<span class="ion-ios-arrow-forward">'],
		// 	responsive:{
		// 		0:{
		// 			items: 1,
		// 			stagePadding: 0
		// 		},
		// 		600:{
		// 			items: 2,
		// 			stagePadding: 50
		// 		},
		// 		1000:{
		// 			items: 3,
		// 			stagePadding: 100
		// 		}
		// 	}
		// });
 		$('.carousel-package-program').owlCarousel({
			center: false,
			loop: true,
			autoplay: true,
			items:1,
			margin: 0,
			stagePadding: 0,
			nav: false,
			navText: ['<span class="ion-ios-arrow-back">', '<span class="ion-ios-arrow-forward">'],
			responsive:{
				0:{
					items: 1
				},
				600:{
					items: 2
				},
				1000:{
					items: 4
				}
			}
		});
		$('.carousel-testimony').owlCarousel({
			center: true,
			loop: true,
			items:1,
			margin: 30,
			stagePadding: 0,
			nav: false,
			navText: ['<span class="ion-ios-arrow-back">', '<span class="ion-ios-arrow-forward">'],
			responsive:{
				0:{
					items: 1
				},
				600:{
					items: 3
				},
				1000:{
					items: 3
				}
			}
		});

	};
	carousel();

	$('nav .dropdown').hover(function(){
		var $this = $(this);
		// 	 timer;
		// clearTimeout(timer);
		$this.addClass('show');
		$this.find('> a').attr('aria-expanded', true);
		// $this.find('.dropdown-menu').addClass('animated-fast fadeInUp show');
		$this.find('.dropdown-menu').addClass('show');
	}, function(){
		var $this = $(this);
			// timer;
		// timer = setTimeout(function(){
			$this.removeClass('show');
			$this.find('> a').attr('aria-expanded', false);
			// $this.find('.dropdown-menu').removeClass('animated-fast fadeInUp show');
			$this.find('.dropdown-menu').removeClass('show');
		// }, 100);
	});


	// scroll
	var scrollWindow = function() {
		$(window).scroll(function(){
			var $w = $(this),
					st = $w.scrollTop(),
					navbar = $('.ftco_navbar'),
					sd = $('.js-scroll-wrap');

			if (st > 150) {
				if ( !navbar.hasClass('scrolled') ) {
					navbar.addClass('scrolled');	
				}
			} 
			if (st < 150) {
				if ( navbar.hasClass('scrolled') ) {
					navbar.removeClass('scrolled sleep');
				}
			} 
			if ( st > 350 ) {
				if ( !navbar.hasClass('awake') ) {
					navbar.addClass('awake');	
				}
				
				if(sd.length > 0) {
					sd.addClass('sleep');
				}
			}
			if ( st < 350 ) {
				if ( navbar.hasClass('awake') ) {
					navbar.removeClass('awake');
					navbar.addClass('sleep');
				}
				if(sd.length > 0) {
					sd.removeClass('sleep');
				}
			}
		});
	};
	scrollWindow();

	
	var counter = function() {
		
		$('#section-counter').waypoint( function( direction ) {

			if( direction === 'down' && !$(this.element).hasClass('ftco-animated') ) {

				var comma_separator_number_step = $.animateNumber.numberStepFactories.separator(',')
				$('.number').each(function(){
					var $this = $(this),
						num = $this.data('number');
					$this.animateNumber(
					  {
					    number: num,
					    numberStep: comma_separator_number_step
					  }, 7000
					);
				});
				
			}

		} , { offset: '95%' } );

	}
	counter();

	var contentWayPoint = function() {
		var i = 0;
		$('.ftco-animate').waypoint( function( direction ) {

			if( direction === 'down' && !$(this.element).hasClass('ftco-animated') ) {
				
				i++;

				$(this.element).addClass('item-animate');
				setTimeout(function(){

					$('body .ftco-animate.item-animate').each(function(k){
						var el = $(this);
						setTimeout( function () {
							var effect = el.data('animate-effect');
							if ( effect === 'fadeIn') {
								el.addClass('fadeIn ftco-animated');
							} else if ( effect === 'fadeInLeft') {
								el.addClass('fadeInLeft ftco-animated');
							} else if ( effect === 'fadeInRight') {
								el.addClass('fadeInRight ftco-animated');
							} else {
								el.addClass('fadeInUp ftco-animated');
							}
							el.removeClass('item-animate');
						},  k * 50, 'easeInOutExpo' );
					});
					
				}, 100);
				
			}

		} , { offset: '95%' } );
	};
	contentWayPoint();


	// navigation
	var OnePageNav = function() {
		$(".smoothscroll[href^='#'], #ftco-nav ul li a[href^='#']").on('click', function(e) {
		 	e.preventDefault();

		 	var hash = this.hash,
		 			navToggler = $('.navbar-toggler');
		 	$('html, body').animate({
		    scrollTop: $(hash).offset().top
		  }, 700, 'easeInOutExpo', function(){
		    window.location.hash = hash;
		  });


		  if ( navToggler.is(':visible') ) {
		  	navToggler.click();
		  }
		});
	};
	OnePageNav();


	// magnific popup
	$('.image-popup').magnificPopup({
    type: 'image',
    closeOnContentClick: true,
    closeBtnInside: true,
    fixedContentPos: true,
    mainClass: 'mfp-no-margins mfp-with-zoom', // class to remove default margin from left and right side
     gallery: {
      enabled: true,
      navigateByImgClick: true,
      preload: [0,1] // Will preload 0 - before current, and 1 after the current image
    },
    image: {
      verticalFit: true
    },
    zoom: {
      enabled: true,
      duration: 300 // don't foget to change the duration also in CSS
    }
  });

  $('.popup-youtube, .popup-vimeo, .popup-gmaps').magnificPopup({
    disableOn: 700,
    type: 'iframe',
    mainClass: 'mfp-fade',
    removalDelay: 160,
    preloader: false,

    fixedContentPos: false
  });


  $('.appointment_date').datepicker({
	  'format': 'm/d/yyyy',
	  'autoclose': true
	});

	$('.appointment_time').timepicker();
	

	/* -- auto adjust user page height */

	function adjustUserPageHeight() {
		var boxTop = ($(window).height() - $(".user-page-box").height()) / 2;
		$(".user-page").height($(window).height());
		$(".user-page").css("padding-top", 0);
		$(".user-page-section").css("padding-top", 0);
		$(".user-page-section").css("top", boxTop);
	}

	$(window).resize(function() {
		adjustUserPageHeight();
	});
	adjustUserPageHeight();

	/* auto adjust user page height -- */


	/* -- change avatar control */

	function displayAvatarWindow() {
		var html = $("#change-avatar-content").html();
		$("body").prepend("<div id='change-avatar-window' class='media-window' style='background: rgba(0, 0, 0, 0.7);'><div class='zoom-media-container'><div class='change-avatar'><div class='close-zoom'>X</div>" + html + "</div></div></div>");
		$("#change-avatar-window").fadeIn("fast");
		$("#change-avatar-window .close-zoom").click(function() {
			$("#change-avatar-window").fadeOut("fast");
			$("#change-avatar-window").remove();
		});
	}

	$("#js-change-avatar").click(function() {
		displayAvatarWindow();
	})

	if($("#change-avatar-submit").data("submitted") == true && $("#change-avatar-submit").data("valid") == "") {
		displayAvatarWindow();
		if($("#change-avatar-window .invalid-feedback .d-block").length > 1) {
			$("#change-avatar-window .invalid-feedback .d-block").last().remove();
		}
	}

	/* change avatar control -- */


	/* -- go top button */

	// When the user scrolls down 20px from the top of the document, show the button
	window.onscroll = function() {scrollFunction()};

	function scrollFunction() {
		if ($(document).scrollTop() >= $(".home-slider").height()) {
			$("#go-top").show();
		} else {
			$("#go-top").hide();
		}
	}

	// When the user clicks on the button, scroll to the top of the document
	$("#go-top").click(function() {
		$('html, body').animate( { scrollTop: 0 }, 500, "easeOutQuad" );
	});

	/* go top button -- */


	/* -- go down button */

	function goDown() {
		$('html, body').animate( { scrollTop: $(".home-slider").height() }, 500, "easeOutQuad" );
	}

	$(".go-down").click(function() {
		goDown();
	});

	/* go top down -- */


	/* -- fix scrollTop bug from Google Chrome */
	function getLoadMoreScrollTop()
	{	
		var height = 0;
		$('section').each(function() {
			height = height + $(this).height() + parseInt($(this).css("margin-top"));
		});
		return height;
	}
	/* fix scrollTop bug from Google Chrome -- */


	

	/* -- Ajax load results functions */	

	function loadResults(requestParameters, divId, reset = false, action = 'load')
	{

		// load default parameters and replace by request parameters
		if(divId == "js-tricks-list") {
			var parameters = loadTrickParameters(reset);		
		} else if(divId == "js-comment-list") {
			var parameters = loadCommentParameters(reset);		
		}

		for(var key in requestParameters) {
		  parameters[key] = requestParameters[key];
		}		

		// do ajax request
		if(divId == "js-tricks-list") {
			var url = "/load-results/" + parameters["category"] + "/" + parameters["newFirstResult"] + "/" + parameters["orderBy"];
		} else if(divId == "js-comment-list") {
			var url = "/load-comments/" + parameters["trick"] + "/" + parameters["newFirstResult"];
		}

		$.ajax({
			method: "GET",
			url: url,
			success: function(data){
				if(data != "" && parameters["reset"] == true) {
					replaceData(data, parameters, divId);
				} else if(data != "") {
					appendData(data, parameters, divId);
				}
				$("#" + divId).hide();
				$("#" + divId).fadeIn("slow");
				if(action == "add") {
					addResultEffect(divId);
				}
			}
		});

	}

	function replaceData(data, parameters, divId)
	{

		$(".load-more-ajax").show();

		// animate data replace
		$("#" + divId).html(data);
		contentWayPoint();

		// store new order-by value
		$("#js-load-parameters").data("order-by", parameters["orderBy"]);

		// store new first result value
		$("#js-load-parameters").data("first-result", parameters["newFirstResult"] + parameters["maxResults"]);

		// hide load more button if no more results to display
		if( $("#js-load-parameters").data("first-result") >= parameters["totalResults"]) {
			$(".load-more-ajax").attr("style", "display: none !important;");
		} else {
			$(".load-more-ajax").attr("style", "display: flex !important;");
		}

	}

	function appendData(data, parameters, divId)
	{

		// store actual scroll position
		var scrollPosition = getLoadMoreScrollTop() - $(".load-more-ajax").height();

		// animate data append
		$(".load-more-ajax .loader").animate({
			opacity: 1
		}, 200, function() {

			// append data with animation
			$("#" + divId).append(data);
			contentWayPoint();
			
			// store new first result value
			$("#js-load-parameters").data("first-result", parameters["newFirstResult"] + parameters["maxResults"]); 

			// hide load more button if no more results to display
			if( $("#js-load-parameters").data("first-result") >= parameters["totalResults"]) {
				$(".load-more-ajax").attr("style", "display: none !important;");
			} else {
				$(".load-more-ajax").attr("style", "display: flex !important;");
			}

			// animate scroll down
			$('html, body').animate({
				scrollTop: scrollPosition
			}, 500, "easeOutQuad", function() {
				// hide spinner
				$(".load-more-ajax .loader").css('opacity', 0);	
			});

		});

	}

	function addResultEffect(divId) {
		$("#" + divId + " li").first().hide();
		$("#" + divId + " li").first().css("background-color", "#ececec");
		$("#" + divId + " li").first().fadeIn(2000, function() {
			$("#" + divId + " li").first().css("background-color", "#ffffff");
		});
	}

	/* Ajax load results functions -- */



	/* -- Load tricks */

	function loadTrickParameters(reset = false)
	{
		var category = "all";
		if($("#js-load-parameters").data("category")) {
			if($("#js-load-parameters").data("category") != "") {
				var category = $("#js-load-parameters").data("category");
			}
		}
		var newFirstResult = 1;
		if($("#js-load-parameters").data("first-result")) {
			var newFirstResult = $("#js-load-parameters").data("first-result");
		}
		var maxResults = 15;
		if($("#js-load-parameters").data("max-results")) {
			var maxResults = $("#js-load-parameters").data("max-results");
		}
		var totalResults = 0;
		if($("#js-load-parameters").data("total-results")) {
			var totalResults = $("#js-load-parameters").data("total-results");
		}
		var orderBy = "creationDate-DESC";
		if($("#js-load-parameters").data("order-by")) {
			var orderBy = $("#js-load-parameters").data("order-by");
		}
		return {
			"category" : category,
			"newFirstResult" : newFirstResult,
			"maxResults" : maxResults,
			"totalResults" : totalResults,
			"orderBy" : orderBy,
			"reset" : reset
		};
	}
	
	$(".tricks-page .tricks-menu .select-category").change(function() {
		document.location.href = $(this).val();
	})
	
	$(".sort-tricks").change(function() {
		loadResults({ "newFirstResult" : 0, "orderBy" : $(this).val() }, "js-tricks-list", true);
	});

	$("#js-load-more").click(function() {
		loadResults({ "newFirstResult" : $("#js-load-parameters").data("first-result") }, "js-tricks-list");
	});

	/* Load tricks -- */


	/* -- Add trick */
	$("#js-add-trick").click(function() {
		document.location.href = $(this).data("url");
	})
	/* Add trick -- */



	/* -- Load comments */	

	function loadCommentParameters(reset = false)
	{
		var trick = 0;
		if($("#js-load-parameters").data("trick")) {
			var trick = $("#js-load-parameters").data("trick");
		}
		var newFirstResult = 1;
		if($("#js-load-parameters").data("first-result")) {
			var newFirstResult = $("#js-load-parameters").data("first-result");
		}
		var maxResults = 4;
		if($("#js-load-parameters").data("max-results")) {
			var maxResults = $("#js-load-parameters").data("max-results");
		}
		var totalResults = 0;
		if($("#js-load-parameters").data("total-results")) {
			var totalResults = $("#js-load-parameters").data("total-results");
		}
		var orderBy = "creationDate-ASC";
		return {
			"trick" : trick,
			"newFirstResult" : newFirstResult,
			"maxResults" : maxResults,
			"totalResults" : totalResults,
			"orderBy" : orderBy,
			"reset" : reset
		};
	}

	$("#js-load-more-comment").click(function() {
		loadResults({ "newFirstResult" : $("#js-load-parameters").data("first-result") }, "js-comment-list");
	});
	
	if(document.getElementById("js-load-more-comment")) {
		loadResults({ "newFirstResult" : 0 }, "js-comment-list", true);
	}

	if(document.getElementById("js-load-more-comment")) {
		if( $("#js-load-parameters").data("total-results") == 0) {
			$("#js-load-more-comment").attr("style", "display: none !important;");
		}
	}

	/* Load comments -- */

	/* -- display medias in mobile version */

	function bindDisplayMedia() {
		$("#js-display-media").click(function() {
			$("#js-display-media .loader").css("opacity", 1);
			$("#media-list").toggle("fast", function(){
				$("#js-display-media .loader").css("opacity", 0);
				if($("#media-list").is(":visible")) {
					$("#js-display-media .txt").html("Hide medias");
				} else {
					$("#js-display-media .txt").html("See medias");
				}
			});
		});
	}
	bindDisplayMedia();

	function bindWindowResize() {
		$( window ).resize(function() {
			if($(window).width() > 576) {
				$("#media-list").show();
				$("#media-list").css("display", "flex");
			} else {
				$("#media-list").hide();
			}
		});
	}
	bindWindowResize();

	/* display medias in mobile version -- */


	/* -- media thumbnail control */

	function bindMediaHover() {
		$("#media-list .media-thumbnail").mouseenter(function() {
			$(this).children(".media-hover")
				.css("display", "flex")
				.hide()
				.fadeIn("fast");
		});
		
		$("#media-list .media-thumbnail").mouseleave(function() {
			$(this).children(".media-hover").fadeOut("fast");
		});
	}
	bindMediaHover();

	function bindMediaClick() {
		$("#media-list .media-thumbnail").click(function() {
		
			if($(this).children(".media").data("type") == "image") {
				var mediaHtml = "<img class='image' src='" + $(this).children(".media").data("media") + "' alt='' />";
			} else {
				var mediaHtml = $(this).html();
			}
			$("body").prepend("<div id='media-zoom-window' class='media-window'><div class='close-zoom'>X</div><div class='zoom-media-container'>" + mediaHtml + "</div></div>");

			if($(this).children(".media").data("type") != "image") {
				$("#media-zoom-window .media-hover").remove();
				$("#media-zoom-window .media").attr("class", "video");
			}
			$("#media-zoom-window").fadeIn("fast");
			$("#media-zoom-window .close-zoom").click(function() {
				$("#media-zoom-window").fadeOut("fast");
				$("#media-zoom-window").remove();
			});

		});
	}
	bindMediaClick();

	/* media thumbnail control -- */


	/* -- Ajax media form functions */	

	// popin media form ajax loader
	function loadMediaForm(url, mediaListUrl) {

		$.ajax({
			method: "GET",
			url: url,
			success: function(html){
				if(html != "") {
					displayMediaFormPopin(html);
					bindMediaFormRadioClick();
					bindMediaFormSubmit(url, mediaListUrl);
				}
			}
		});	

	}

	// click on media edit pen load the form in new popin
	function bindMediaEditClick() {

		$("#media-list .edit").click(function() {		
			var url = $(this).data("url");
			var mediaListUrl = $("#media-list").data("media-list-url");
			loadMediaForm(url, mediaListUrl);
		});	

	}
	bindMediaEditClick();

	// click on media delete trash load the form in new popin
	function bindMediaDeleteClick() {

		$("#media-list .delete").click(function() {		
			var url = $(this).data("url");
			var mediaListUrl = $("#media-list").data("media-list-url");
			loadMediaForm(url, mediaListUrl);
		});	

	}
	bindMediaDeleteClick();

	function bindMediaAddClick() {

		$("#media-list .add").click(function() {
			var url = $(this).data("url");
			var mediaListUrl = $("#media-list").data("media-list-url");
			loadMediaForm(url, mediaListUrl);
		});	

	}
	bindMediaAddClick();

	// popin creation function
	function displayMediaFormPopin(html) {

		$("body").prepend("<div id='media-edit-window' class='media-window' style='background: rgba(0, 0, 0, 0.7);'><div class='zoom-media-container'><div class='media-form'><div class='close-zoom'>X</div><div id='js-form-result'>" + html + "</div></div></div></div>");
		$("#media-edit-window").fadeIn("fast");
		$("#media-edit-window .close-zoom").click(function() {
			$("#media-edit-window").fadeOut("fast");
			$("#media-edit-window").remove();
		});

		bindConfirmMediaDeleteClick();
		bindCancelMediaDeleteClick();
		
	}

	// bind the change form radio click
	function bindMediaFormRadioClick() {

		$("input[type=radio][name=mediaType]").change(function() {
			switchMediaForm($(this).val());
		});

	}

	// switch media form picture or video
	function switchMediaForm(formName) {

		if(formName == 'picture') {
			$(".media-form .video").hide();
			$(".media-form .picture").fadeIn("fast");
		} else if(formName == 'video') {
			$(".media-form .picture").hide();
			$(".media-form .video").fadeIn("fast");
			$(".media-form .video input").focus();
		}
		$("#js-radio-" + formName).prop("checked", true);

	}

	// bind the form submit
	function bindMediaFormSubmit(url, mediaListUrl) {

		$("form[name='picture']").submit(function(e) {
			var formData = new FormData(this);
			mediaFormSubmit(formData, url, mediaListUrl, "picture");
			e.preventDefault(); // prevent normal submit
		});
		$("form[name='video']").submit(function(e) {
			var formData = new FormData(this);
			mediaFormSubmit(formData, url, mediaListUrl, "video");			
			e.preventDefault(); // prevent normal submit
		});

	}

	// bind confirm delete media click
	function bindConfirmMediaDeleteClick() {
		$("#delete-media-confirm").click(function() {
			$.ajax({
				method: "GET",
				url: $(this).data("url"),
				success: function(html){
					if(html != "") {
						$("#js-form-result").html(html);
						loadMediaList($("#media-list").data("media-list-url"));
					}
				}
			});
		});
	}

	// bind cancel delete media click
	function bindCancelMediaDeleteClick() {
		$("#delete-media-cancel").click(function() {
			$("#media-edit-window").fadeOut("fast");
			$("#media-edit-window").remove();
		});
	}

	// ajax form submit function
	function mediaFormSubmit(formData, url, mediaListUrl, formName) {		

		// process the form
		$.ajax({ 
			url: url,
			type: 'POST',
			data: formData,
			success: function (data) {
				$("#js-form-result").html(data);
				switchMediaForm(formName);
				bindMediaFormRadioClick();
				bindMediaFormSubmit(url, mediaListUrl);
				loadMediaList(mediaListUrl);
				if($("#js-form-result .invalid-feedback .d-block").length > 1) {
					$("#js-form-result .invalid-feedback .d-block").last().remove();
				}
			},
			cache: false,
			contentType: false,
			processData: false
		});
	
	}

	// ajax reload trick media list
	function loadMediaList(url) {
		$.ajax({
			method: "GET",
			url: url,
			success: function(html){
				if(html != "") {
					$("#js-media-list-container").html(html);
					$("#js-media-list-container").hide();
					bindMediaEditClick();
					bindMediaDeleteClick();
					bindMediaAddClick();
					bindDisplayMedia();
					bindWindowResize();
					bindMediaHover();
					bindMediaClick();				
					$("#js-media-list-container").fadeIn("slow");
					contentWayPoint();
				}
			}
		});
	}
	
	/* Ajax media form functions -- */



	/* -- Trick form functions */

	$(".js-delete-trick").click(function() {

		var html = $("#trick-delete-content").html();
		var urlConfirmation = $(this).data("url-confirmation");

		$("body").prepend("<div id='media-edit-window' class='media-window' style='background: rgba(0, 0, 0, 0.7);'><div class='zoom-media-container'><div class='media-form'><div class='close-zoom'>X</div><div id='js-form-result'>" + html + "</div></div></div></div>");
		$("#media-edit-window").fadeIn("fast");
		$("#media-edit-window .close-zoom").click(function() {
			$("#media-edit-window").fadeOut("fast");
			$("#media-edit-window").remove();
		});

		$("#delete-trick-cancel").click(function() {
			$("#media-edit-window").fadeOut("fast");
			$("#media-edit-window").remove();
		});

		$("#delete-trick-confirm").click(function() {
			document.location.href = urlConfirmation;
		});
	
		return false;
	});

	$("#trick_name").keypress(function(e) {
		if (e.which == 13) {
			e.preventDefault();
			$("#js-save-trick-button").click();
		 }
	});

	$("#js-cancel-trick").click(function() {
		document.location.href = $(this).data("url");
	});
	
	/* Trick form functions -- */



	/* -- Ajax comment form functions */

	
	// load ajax comment form
	function loadCommentForm(url) {

		$.ajax({
			method: "GET",
			url: url,
			success: function(html){
				if(html != "") {
					$("#js-comment-form-container").html(html);
					$("#js-comment-form-container").hide();
					$("#js-comment-form-container").fadeIn("slow");
					bindCommentFormSubmit(url);					
				}
			}
		});	

	}
	if(document.getElementById("js-comment-form-container")) {
		loadCommentForm($("#js-comment-form-container").data("url"));
	}

	// bind the form submit
	function bindCommentFormSubmit(url) {

		$("form[name='comment']").submit(function(e) {
			var formData = new FormData(this);
			commentFormSubmit(formData, url);
			e.preventDefault(); // prevent normal submit
		});

	}

	// ajax form submit function
	function commentFormSubmit(formData, url) {		
	
		// process the form
		$.ajax({
			url: url,
			type: 'POST',
			data: formData,
			success: function (html) {
				$("#js-comment-form-container").html(html);;
				bindCommentFormSubmit(url);
				loadCommentForm($("#js-comment-form-container").data("url"));
				loadResults({ "newFirstResult" : 0 }, "js-comment-list", true, 'add');
			},
			cache: false,
			contentType: false,
			processData: false
		});
	
	}



 	/* Ajax comment form functions -- */

})(jQuery);

