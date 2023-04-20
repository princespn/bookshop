$(document).ajaxStart(function() {
    $('#loading').show();
    //setTimeout(function(){ $('#loading').fadeOut();}, 500);
});

$(document).ajaxStop(function() {
    $('#loading').hide();
});

$('#confirm-delete').on('show.bs.modal', function(e) {
    $(this).find('.danger').attr('href', $(e.relatedTarget).data('href'));
});

var url = document.location.toString();
if (url.match('#reset_password')) {
    $('#reset_password_tab').trigger('click') ;
}

$('a.ajax-link').on('show.bs.tab', function (e) {
    var currTabTarget = $(this).attr('data-remote-div');
    var remoteUrl = $(this).attr('data-tab-remote');
    var loadedOnce = $(this).data('loaded');
    if (remoteUrl !== '' && !loadedOnce) {
        $(currTabTarget).load(remoteUrl)
        $(this).data('loaded',true);
    }
})

$(document).ready(function(){
    fadeoutdiv('#response .alert-success');

    $('.gallery-item').hover(function () {
        $(this).find('.hover-box').fadeIn(300);
    }, function () {
        $(this).find('.hover-box').fadeOut(100);
    });

    $('#sidebar-button').click(function () {
        $('.sidebar-offcanvas').toggleClass('active');
        $('#navbar-nav-dropdown').collapse('hide');
    });

    $(function () {
        $('[data-toggle="tooltip"]').tooltip();
    });

    //for setting tabs to dropdowns on smalle screens (phones)
    $('.resp-tabs').tabdrop();
});

function fadeoutdiv(div) {
    setTimeout(function(){ $(div).fadeOut('slow'); }, 4000);
};


function remove_div(id) {
    $(id).fadeOut(300,function(){$(this).remove();});
}

function updateReportDate(select) {
    var index;

    for(index=0; index<select.options.length; index++)
        if(select.options[index].selected)
        {
            if(select.options[index].value!="")
                window.location.href= select.options[index].value;
            break;
        }
}
/*
$(window).scroll(function() {
    if ($(window).scrollTop() > 100) {
        $('div.top-header').addClass('fixed-top');
    }
    else {
        $('div.top-header').removeClass('fixed-top');
    }
});
*/
