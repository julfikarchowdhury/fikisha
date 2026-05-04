 
 
function btnHold(){

 
  
    let duration = 1600,
        success = button => {
            //Success function
            $('.progress').hide();
            button.classList.add('success');
            checkIn($('#checkin_url').data('url'));
        };
    document.querySelectorAll('.button-hold').forEach(button => {
        button.style.setProperty('--duration', duration + 'ms');
 
        ['mousedown', 'touchstart', 'keypress'].forEach(e => {
            button.addEventListener(e, ev => {
                if (e != 'keypress' || (e == 'keypress' && ev.which == 32 && !button
                        .classList.contains('process'))) {
                    button.classList.add('process'); 
                    button.timeout = setTimeout(success, duration, button);
 
                   
                
                }
            });
        });
        ['mouseup', 'mouseout', 'touchend', 'keyup'].forEach(e => {
            button.addEventListener(e, ev => {
                if (e != 'keyup' || (e == 'keyup' && ev.which == 32)) {
                    button.classList.remove('process');
                    clearTimeout(button.timeout); 
                }
            }, false);
        });
    });

}
btnHold();
var checkUrl;
var checkIn = (url) => {
    checkUrl = url; 
    attendanceStore();
}   
function attendanceStore(){  
    $.ajax({
        type: 'GET',
        url: checkUrl, 
        dataType: 'json',
        success: function (data) {  
            if (data?.success) { 
                toastr.success(data.message,'Success');
                setTimeout(function () {
                    window.location.reload();
                }, 1500)
            }else if(data?.already_attend){
                toastr.error(data.message,'Error'); 
            }else{ 
                toastr.error('Something went wrong!','Error'); 
            }
        },
        error: function (data) { 
            toastr.error('Something went wrong!','Error'); 
        }
    });
}
 

