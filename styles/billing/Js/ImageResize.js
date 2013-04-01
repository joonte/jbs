//------------------------------------------------------------------------------
/** @author FreeBSP (bspala4@pochta.ru)  */
//------------------------------------------------------------------------------
$(document).ready(function(){
	//------------------------------------------------------------------------------
	// Click on 2nd img (with class .sampleimg")
	$(".sampleimg").click(function(){
		//------------------------------------------------------------------------------
		// if it has .sampleimg" and "big" classes - animate to width=100px 
		$(".sampleimg.big").animate({width:"200px",left:"0px",top:"0px"},400);
		// if it has .sampleimg" and "small" classes - animate to width=100%
		$(".sampleimg.small").animate({width:"100%",left:"0px",top:"0px"},400);
		// tocggle classes
		$(this).toggleClass("small").toggleClass("big");
		//------------------------------------------------------------------------------
	}); // end of handler
	//------------------------------------------------------------------------------
});
//------------------------------------------------------------------------------

