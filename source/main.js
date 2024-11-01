jQuery(document).ready(function($){
	$("[name=comment_type]").change(function(){
		$(".pi-comment-settings").fadeOut();

		if ( $(this).val() == 'facebook' )
		{
			$("#facebook-comment").fadeIn();
		}else if( $(this).val() == 'disqus' )
		{
			$("#disqus-comment").fadeIn();
		}
	}).trigger("change");
})