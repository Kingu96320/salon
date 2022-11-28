'use strict';


// Resizing Graphs
$(window).trigger('resize'); 



$(document).ready(function(){
	$('.dropdown-menu a.dropdown-toggle').on('click', function (e){
		var $el = $(this);
		var $parent = $(this).offsetParent(".dropdown-menu");
		if (!$(this).next().hasClass('show')){
			$(this).parents('.dropdown-menu').first().find('.show').removeClass("show");
		}
		var $subMenu = $(this).next(".dropdown-menu");
		$subMenu.toggleClass('show');
		
		$(this).parent("li").toggleClass('show');

		$(this).parents('li.nav-item.dropdown.show').on('hidden.bs.dropdown', function (e){
			$('.dropdown-menu .show').removeClass("show");
		});
		
		if (!$parent.parent().hasClass('navbar-nav')){
			$el.next().css({"top": $el[0].offsetTop, "left": $parent.outerWidth() - 4});
		}
		return false;
	});
});



// Bootstrap Tooltip
$(function () {
	$('[data-toggle="tooltip"]').tooltip()
})



// Bootstrap Popover
$(function () {
  $('[data-toggle="popover"]').popover()
})
$('.popover-dismiss').popover({
  trigger: 'focus'
})



// Task list
$('.task-list').on('click', 'li.list', function() {
	$(this).toggleClass('completed');
});



// Todo list
$('.todo-body').on('click', 'li.todo-list', function() {
	$(this).toggleClass('done');
});



// Loading
$(function() {
	$("#loading-wrapper").fadeOut(2000);
});



// Setting Main Content Dynamic Height
$(window).on("load resize",function(e){
  $('.main-content').css('min-height', (window.innerHeight) - 250);
});



// Textarea characters left
$(function() {
	$('#characterLeft').text('140 characters left');
	$('#message').keydown(function () {
		var max = 140;
		var len = $(this).val().length;
		if (len >= max) {
			$('#characterLeft').text('You have reached the limit');
			$('#characterLeft').addClass('red');
			$('#btnSubmit').addClass('disabled');            
		} 
		else {
			var ch = max - len;
			$('#characterLeft').text(ch + ' characters left');
			$('#btnSubmit').removeClass('disabled');
			$('#characterLeft').removeClass('red');            
		}
	});
});



// Tasks
(function($) {
  var checkList = $('.check'),
	toDoCheck = checkList.children('input[type="checkbox"]');
  toDoCheck.each(function(index, element) {
    var $this = $(element),
		taskItem = $this.closest('li');
    $this.on('click', function(e) {
			taskItem.toggleClass('task-checked');
    });
  });
})(jQuery);





// Setting Chat Container Max Height
$(window).on("load resize", function(e){
  $('.chat-box').css('max-height', (window.innerHeight) - 440);
});

$(window).on("load resize",function(e){
  $('.users-container .users').css('max-height', (window.innerHeight) - 390);
});