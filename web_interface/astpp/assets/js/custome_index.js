
// Example starter JavaScript for disabling form submissions if there are invalid fields
(function() {
  'use strict';
  window.addEventListener('load', function() {
    // Fetch all the forms we want to apply custom Bootstrap validation styles to
    var forms = document.getElementsByClassName('needs-validation');
    // Loop over them and prevent submission
    var validation = Array.prototype.filter.call(forms, function(form) {
      form.addEventListener('submit', function(event) {
        if (form.checkValidity() === false) {
          event.preventDefault();
          event.stopPropagation();
        }
        form.classList.add('was-validated');
      }, false);
    });
  }, false);

  $('.carousel').carousel({
  interval: 4000
});

  $("input").change(function() {
  if ($(this).val() != "") {
    $(this).closest('.form-group').addClass('control-focus');
  } else {
    $(this).closest('.form-group').removeClass('control-focus');
  }
});

})();



  $(document).ready(function() {

    $('.selectpicker').selectpicker();
    
    $("button, a, .btn").attr("data-ripple"," "); 

    $(".logo_title").removeAttr("data-ripple","");
    
  });

$(function(){
  function rescaleCaptcha(){
    var width = $('.g-recaptcha').parent().width();
    var scale;
    if (width < 302) {
      scale = width / 302;
    } else{
      scale = 1.0; 
    }

    $('.g-recaptcha').css('transform', 'scale(' + scale + ')');
    $('.g-recaptcha').css('-webkit-transform', 'scale(' + scale + ')');
    $('.g-recaptcha').css('transform-origin', '0 0');
    $('.g-recaptcha').css('-webkit-transform-origin', '0 0');
    $('.g-recaptcha').css('-webkit-transform-origin', '0 0');
    $('.g-recaptcha div').css('margin', '0 auto');
  }

  rescaleCaptcha();
  $( window ).resize(function() { rescaleCaptcha(); });

});

jQuery(function($) {

  // MAD-RIPPLE // (jQ+CSS)
  $(document).on("mousedown", "[data-ripple]", function(e) {
    
    var $self = $(this);
    
    if($self.is(".btn-disabled")) {
      return;
    }
    if($self.closest("[data-ripple]")) {
      e.stopPropagation();
    }
    
    var initPos = $self.css("position"),
        offs = $self.offset(),
        x = e.pageX - offs.left,
        y = e.pageY - offs.top,
        dia = Math.min(this.offsetHeight, this.offsetWidth, 100), // start diameter
        $ripple = $('<div/>', {class : "ripple",appendTo : $self });
    
    if(!initPos || initPos==="static") {
      $self.css({position:"relative"});
    }
    
    $('<div/>', {
      class : "rippleWave",
      css : {
        background: $self.data("ripple"),
        width: dia,
        height: dia,
        left: x - (dia/2),
        top: y - (dia/2),
      },
      appendTo : $ripple,
      one : {
        animationend : function(){
          $ripple.remove();
        }
      }
    });
  });

});



  (function ($) {
            'use strict';

            $.fn.floatingLabel = function (option) {
                var parent = $(this).closest('.form-group');

                if (parent.length) {
                    switch (option) {
                        case 'focusin':
                            $(this).closest('.form-group').addClass('control-focus');
                            break;
                        case 'focusout':
                            $(this).closest('.form-group').removeClass('control-focus');
                            break;
                        case 'ChangeFortText':
                            if (this.val()) {
                                parent.addClass('control-highlight');
                            }
                            else {
                                parent.removeClass('control-highlight');
                            }
                            break;
                        default:
                            $(this).closest('.form-group').addClass('control-highlight');
                            break;
                    }
                }

                return this;
            };
        }($));

        $(document).ready(function () {
            'use strict';
            $(document).on('change', function () {
                $('.form-group .form-control').each(function () {
                    $(this).floatingLabel('ChangeFortText');
                });
            });

            $('.form-group .form-control').each(function () {
                $(this).floatingLabel('ChangeFortText');
            });

            $(document).on('change', '.form-group .form-control', function () {
                $(this).floatingLabel('ChangeFortText');
            });

            $(document).on('focusin', '.form-group .form-control', function () {
                $(this).floatingLabel('focusin');
            });

            $(document).on('focusout', '.form-group .form-control', function () {
                $(this).floatingLabel('focusout');
            });

        });