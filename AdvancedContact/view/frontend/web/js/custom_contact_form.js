require(['jquery'], function($){
  	$(document).ready( function() {

		$(document).on('submit','form.mz-contact-form',function(e){
		    e.preventDefault();

		    if (typeof grecaptcha != "undefined") {
			    var response = grecaptcha.getResponse();
			    if(response.length == 0) {
			    	alert('Google reCAPTCHA fail');
			    	return false;
			    }
		    }
		    
		    $('.mz_loadfr').css('display', 'block');
		    $.ajax({
           		type: "POST",
	           	url: mgzContactPost,
	           	data: $(this).serialize(),
	           	success: function(data)
	           	{
           			$('.mz_loadfr').css('display', 'none');
					$("html, body").animate({scrollTop:0}, 500, 'swing');
	               	$('.mz-contact-form').trigger("reset");
	           	}
         	});
		});

  	});

});
 
function verifyCaptcha() {
    document.getElementById('g-recaptcha-error').innerHTML = '';
}