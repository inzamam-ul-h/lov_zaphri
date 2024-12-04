jQuery(document).ready(function($) {

    'use strict';
    
    // Banner Slider Function
	if($('.ritekhela-banner-one'))
	{
		$('.ritekhela-banner-one').slick({
		dots: false,
		infinite: true,
		arrows: false,
		speed: 1000,
		autoplay: true,
		fade: true,
		autoplaySpeed: 2000,
		slidesToShow: 1,
		slidesToScroll: 1,
		responsive: [
			{
				breakpoint: 1024,
				settings: {
					slidesToShow: 1,
					slidesToScroll: 1,
					infinite: true,
				}
			},
			{
				breakpoint: 600,
				settings: {
					slidesToShow: 1,
					slidesToScroll: 1
				}
			},
			{
				breakpoint: 480,
				settings: {
					slidesToShow: 1,
					slidesToScroll: 1
				}
			}
		]
		});
	}

    // Banner Slider Function
	if($('.ritekhela-banner-two'))
	{
		$('.ritekhela-banner-two').slick({
		dots: false,
		infinite: true,
		arrows: false,
		speed: 1000,
		autoplay: true,
		fade: true,
		autoplaySpeed: 2000,
		slidesToShow: 1,
		slidesToScroll: 1,
		responsive: [
			{
				breakpoint: 1024,
				settings: {
					slidesToShow: 1,
					slidesToScroll: 1,
					infinite: true,
				}
			},
			{
				breakpoint: 600,
				settings: {
					slidesToShow: 1,
					slidesToScroll: 1
				}
			},
			{
				breakpoint: 480,
				settings: {
					slidesToShow: 1,
					slidesToScroll: 1
				}
			}
		]
		});
	}

    // Banner Slider Function
	if($('.ritekhela-fixture-slider'))
	{
		$('.ritekhela-fixture-slider').slick({
		dots: false,
		infinite: true,
		prevArrow: "<span class='slick-arrow-left'><i class='fa fa-chevron-left'></i></span>",
		nextArrow: "<span class='slick-arrow-right'><i class='fa fa-chevron-right'></i></span>",
		speed: 1000,
		autoplay: true,
		autoplaySpeed: 2000,
		slidesToShow: 5,
		slidesToScroll: 1,
		responsive: [
			{
				breakpoint: 1024,
				settings: {
					slidesToShow: 2,
					slidesToScroll: 1,
					infinite: true,
				}
			},
			{
				breakpoint: 600,
				settings: {
					slidesToShow: 2,
					slidesToScroll: 1
				}
			},
			{
				breakpoint: 480,
				settings: {
					slidesToShow: 1,
					slidesToScroll: 1
				}
			}
		]
		})
	};

    // Ticker Slider Function
	if($('.ritekhela-latest-news-slider'))
	{
		$('.ritekhela-latest-news-slider').slick({
		dots: false,
		infinite: true,
		arrows: false,
		speed: 1000,
		autoplay: true,
		fade: false,
		autoplaySpeed: 2000,
		slidesToShow: 1,
		slidesToScroll: 1,
		responsive: [
			{
				breakpoint: 1024,
				settings: {
					slidesToShow: 1,
					slidesToScroll: 1,
					infinite: true,
				}
			},
			{
				breakpoint: 600,
				settings: {
					slidesToShow: 1,
					slidesToScroll: 1
				}
			},
			{
				breakpoint: 480,
				settings: {
					slidesToShow: 1,
					slidesToScroll: 1
				}
			}
		]
		});
	}

    // Testimonials View1 Slider Function
	if($('.ritekhela-testimonials-view1'))
	{
		$('.ritekhela-testimonials-view1').slick({
			dots: false,
			infinite: true,
			prevArrow: "<span class='slick-arrow-left'><i class='fa fa-chevron-left'></i></span>",
			nextArrow: "<span class='slick-arrow-right'><i class='fa fa-chevron-right'></i></span>",
			speed: 1000,
			autoplay: true,
			autoplaySpeed: 2000,
			slidesToShow: 3,
			slidesToScroll: 1,
			responsive: [
				{
					breakpoint: 1024,
					settings: {
						slidesToShow: 2,
						slidesToScroll: 1,
						infinite: true,
					}
				},
				{
					breakpoint: 600,
					settings: {
						slidesToShow: 1,
						slidesToScroll: 1
					}
				},
				{
					breakpoint: 480,
					settings: {
						slidesToShow: 1,
						slidesToScroll: 1
					}
				}
			]
		});
	}

    // Responsive Main Menu Function
	if($('#main-menu'))
	{
    	jQuery('#main-menu').smartmenus({
			subMenusSubOffsetX: 1,
			subMenusSubOffsetY: -10
		});
	}

    //***************************
    // Parent AddClass Function
    //***************************
	if($('.sm-blue ul'))
	{
    	jQuery(".sm-blue ul").parent("li").addClass("subdropdown-addicon");
	}

    // Menu Link Function
	if($('.ritekhela-menu-link'))
	{
		jQuery( ".ritekhela-menu-link" ).on("click", function() {
			jQuery( "#main-nav" ).slideToggle( "slow", function() {});
		});
	}

    //*** Function CartToggle
	if($('a.ritekhela-open-cart'))
	{
    jQuery('a.ritekhela-open-cart').on("click", function(){
          jQuery('.ritekhela-cart-box').slideToggle('slow');
          return false;
      });
      jQuery('html').on("click", function() { jQuery(".ritekhela-cart-box").fadeOut(); });
	}
      

    //***************************
    // Countdown Function
    //***************************
    jQuery(function() {
        var austDay = new Date();
        austDay = new Date(austDay.getFullYear() + 2, 1 -1);
        jQuery('#ritekhela-match-countdown,#ritekhela-match-countdowntw').countdown({
            until: austDay
        });
        jQuery('#year').text(austDay.getFullYear());
    });

	//***************************
	// Fancybox Function
	//***************************
	if($('.fancybox'))
	{
		jQuery(".fancybox").fancybox({
		  openEffect  : 'elastic',
		  closeEffect : 'elastic',
		});
	}

	//***************************
	// Click to Top Button
	//***************************
	if($('.ritekhela-back-top'))
	{
		jQuery('.ritekhela-back-top').on("click", function() {
			jQuery('html, body').animate({
				scrollTop: 0
			}, 800);
			return false;
		});
	}

	if($('#ritekhela-loader'))
	{
		var remSlidrLodrInt = setInterval(function () {
			jQuery('#ritekhela-loader').hide();
			clearInterval(remSlidrLodrInt);
		}, 1500);
	}


});


if($('.progressbar1'))
{
	jQuery('.progressbar1').progressBar({
		shadow : false,
		percentage : true,
		animation : true,
	});
}

// Counter
// var a = 0;
// $(window).scroll(function() {
// 	if($('#counter'))
// 	{
// 	  var oTop = $('#counter').offset().top - window.innerHeight;
// 	  if (a == 0 && $(window).scrollTop() > oTop) {
//     $('.counter-value').each(function() {
//       var $this = $(this),
//         countTo = $this.attr('data-count');
//       $({
//         countNum: $this.text()
//       }).animate({
//           countNum: countTo
//         },

//         {

//           duration: 5000,
//           easing: 'swing',
//           step: function() {
//             $this.text(Math.floor(this.countNum));
//           },
//           complete: function() {
//             $this.text(this.countNum);
//             //alert('finished');
//           }

//         });
//     });
//     a = 1;
//   }
// 	}

// });

//***************************
// FilterAble Function
//***************************
jQuery(window).on('load', function() {
	// if($('#counter'))
	// {
	// 	var $grid = $('.health-project-modren,.health-gallery-simple').isotope({
	// 		itemSelector: '.element-item',
	// 		layoutMode: 'fitRows'
	// 	});
	// }
    // filter functions
    var filterFns = {
		// show if number is greater than 50
		numberGreaterThan50: function() {
			var number = $(this).find('.number').text();
			return parseInt( number, 10 ) > 50;
		},
		// show if name ends with -ium
		ium: function() {
			var name = $(this).find('.name').text();
			return name.match( /ium$/ );
		}
    };
	
    // bind filter button click
	if($('.filters-button-group'))
	{
		$('.filters-button-group').on( 'click', 'a', function() {
			var filterValue = $( this ).attr('data-filter');
			// use filterFn if matches value
			filterValue = filterFns[ filterValue ] || filterValue;
			$grid.isotope({ filter: filterValue });
		});
	}
	
    // change is-checked class on buttons
	if($('.filters-button-group'))
	{
		$('.filters-button-group').each( function( i, buttonGroup ) {
			var $buttonGroup = $( buttonGroup );
			$buttonGroup.on( 'click', 'a', function() {
				$buttonGroup.find('.is-checked').removeClass('is-checked');
				$( this ).addClass('is-checked');
			});
		});
	}
});
