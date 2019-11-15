(function($) {

  // Navigation scrolls
  $(".navbar-nav li a").on('click', function(event) {
    $('.navbar-nav li').removeClass('active');
    $(this).closest('li').addClass('active');
    var $anchor = $(this);
    var nav = $($anchor.attr('href'));
    if (nav.length) {
      $('html, body').stop().animate({
        scrollTop: $($anchor.attr('href')).offset().top
      }, 1500, 'easeInOutExpo');

      event.preventDefault();
    }
  });
  $(".navbar-collapse a").on('click', function() {
    $(".navbar-collapse.collapse").removeClass('in');
  });

  // Add smooth scrolling to all links in navbar
  $("a.mouse-hover, a.get-quote").on('click', function(event) {
    var hash = this.hash;
    if (hash) {
      event.preventDefault();
      $('html, body').animate({
        scrollTop: $(hash).offset().top
      }, 1500, 'easeInOutExpo');
    }
  });
  $('#loginid').focusout(function(){

      $('#loginid').filter(function(){
          var emil=$('#loginid').val();
          // console.log(emil);
          // var emailReg = /^([\w-\.]+@([\w-]+\.)+[\w-]{2,4})?$/;
          var emailReg = new RegExp(/^(("[\w-\s]+")|([\w-]+(?:\.[\w-]+)*)|("[\w-\s]+")([\w-]+(?:\.[\w-]+)*))(@((?:[\w-]+\.)*\w[\w-]{0,66})\.([a-z]{2,6}(?:\.[a-z]{2})?)$)|(@\[?((25[0-5]\.|2[0-4][0-9]\.|1[0-9]{2}\.|[0-9]{1,2}\.))((25[0-5]|2[0-4][0-9]|1[0-9]{2}|[0-9]{1,2})\.){2}(25[0-5]|2[0-4][0-9]|1[0-9]{2}|[0-9]{1,2})\]?$)/i);

          var pattern = /^([a-z\d!#$%&'*+\-\/=?^_`{|}~\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF]+(\.[a-z\d!#$%&'*+\-\/=?^_`{|}~\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF]+)*|"((([ \t]*\r\n)?[ \t]+)?([\x01-\x08\x0b\x0c\x0e-\x1f\x7f\x21\x23-\x5b\x5d-\x7e\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF]|\\[\x01-\x09\x0b\x0c\x0d-\x7f\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF]))*(([ \t]*\r\n)?[ \t]+)?")@(([a-z\d\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF]|[a-z\d\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF][a-z\d\-._~\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF]*[a-z\d\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])\.)+([a-z\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF]|[a-z\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF][a-z\d\-._~\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF]*[a-z\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])\.?$/i;
          var re = /\S+@\S+\.\S+/;
          if( !emailReg.test( emil ) ) {
              // alert('Please enter valid email');
              $('.login-box-msg').html('Please enter valid email');
              $('.login-box-msg').css("color", "red");
          } else {
              // alert('Thank you for your valid email');
              $('.login-box-msg').html('Thank you for your valid email');
              $('.login-box-msg').css("color", "green");
          }
      })
  });
    // $('#loginpsw').validate({
    //     rules : {
    //         password : {
    //             minlength : 5
    //         },
    //         password_confirm : {
    //             minlength : 5,
    //             equalTo : "#password"
    //         }
    //     }})
    $('#loginForm > div.row > div:nth-child(2) > button').click(function(event){

        data = $('#loginpsw').val();
        var len = data.length;

        if(len < 6) {
            // alert("Password cannot be less than 5 characters long");
            // Prevent form submission
            event.preventDefault();
            $('.login-box-msg').html('Password cannot be less than 6 characters long');
            $('.login-box-msg').css("color", "red");
        }
        else{
            $('.login-box-msg').html('');
            $('.login-box-msg').css("color", "black");
        }

        // if($('.password').val() != $('.confpass').val()) {
        //     // alert("Password and Confirm Password don't match");
        //     $('.login-box-msg').html('Password and Confirm Password don\'t match');
        //     $('.login-box-msg').css("color", "red");
        //     // Prevent form submission
        //     event.preventDefault();
        // }

    });


})(jQuery);
