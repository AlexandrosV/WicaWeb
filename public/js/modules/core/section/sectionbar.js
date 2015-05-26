$(document).ready(function() {
    //new section
	$('#new_section').bind('click',function(){
		//select the option in the section bar
		remove_selected_option();
		$(this).parent('li').addClass('selected');
		//load the corresponding view
		$('#cms_container').load("/core/section_section/new", {
		}, function() {
			$.getScript('/js/modules/core/section/new.js');								
		});
	});
	
	//new content
	$('#new_content').bind('click',function(){
		//select the option in the section bar
		remove_selected_option();
		$(this).parent('li').addClass('selected');
		//load the corresponding view
		$('#cms_container').load("/core/content_content/index", {
		}, function() {
			setSectionTreeHeight();
			$.getScript('/js/modules/core/content/index.js');								
		});
	});
	
	//new article
	$('#new_article').bind('click',function(){
		//select the option in the section bar
		remove_selected_option();
		$(this).parent('li').addClass('selected');
		//load the corresponding view
		$('#cms_container').load("/core/article_article/new", {
		}, function() {			
			$.getScript('/js/modules/core/article/new.js');								
		});		
	});
	
	//link content
	$('#content_link').bind('click',function(){
		//select the option in the section bar
		remove_selected_option();
		$(this).parent('li').addClass('selected');
		//load the corresponding view
		$('#cms_container').load("/core/content_content/link", {
			search_content: 0
		}, function() {
			setSectionTreeHeight();
			$.getScript('/js/modules/core/content/link.js');												
		});
	});
	
});