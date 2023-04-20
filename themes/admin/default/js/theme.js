$(document).ready(function(){

	//$(window).load(function() { $("#loading").fadeOut("slow"); })
    $('div#error-alert').hide();

	setTimeout(function(){ $('.alert-msg').fadeOut('slow'); }, 5000);

    $(".btn, .link, .table-header").click(function() {
        $('#loading').show();
        setTimeout(function(){
            $('#loading').fadeOut();}, 500);
        return true;
    });

	$('#confirm-delete').on('show.bs.modal', function(e) {
    	$(this).find('.danger').attr('href', $(e.relatedTarget).data('href'));
	});

	$(".check-all").click(function () {
	  if ($(".check-all").is(':checked')) {
		  $("input[type=checkbox]").each(function () {
			  $(this).prop("checked", true);
		  });
	
	  } else {
		  $("input[type=checkbox]").each(function () {
			  $(this).prop("checked", false);
		  });
	  }
	});
	
	$("select.show-block").change(function(){
		$( "select.show-block option:selected").each(function(){
			if($(this).attr("value")=="1"){
				$(".show-div").show(300);
			}
			if($(this).attr("value")=="0"){
				$(".show-div").hide(300);
	
			}
		});
	}).change();

    //show tabs directly when accessed via URL
	var url = document.location.toString();
	if (url.match('#')) {
		$('.nav-tabs a[href=#'+url.split('#')[1]+']').tab('show') ;
	} 
	
	// Change hash for page-reload
	$('.nav-tabs a').on('shown', function (e) {
		window.location.hash = e.target.hash;
	})
		
	//SLIM SCROLL
	$('.slimscroller').slimscroll({
		height: 'auto',
		size: '3px',
		railOpacity: 0.3,
		wheelStep: 5
	});

    //TOOLTIP
	$('.tip').tooltip();
	
	//RESPONSIVE SIDEBAR
	$("button.show-sidebar").click(function(){
	$("div.left").toggleClass("mobile-sidebar");
	$("div.right").toggleClass("mobile-content");
	$("div.logo-brand").toggleClass("logo-brand-toggle");
	});


	//SIDEBAR MENU
    $('#menu').find('li').has('ul').children('a').on('click', function() {
        if ($('#column-left').hasClass('active')) {
            $(this).parent('li').toggleClass('open').children('ul').collapse('toggle');
            $(this).parent('li').siblings().removeClass('open').children('ul.in').collapse('hide');
        } else if (!$(this).parent().parent().is('#menu')) {
            $(this).parent('li').toggleClass('open').children('ul').collapse('toggle');
            $(this).parent('li').siblings().removeClass('open').children('ul.in').collapse('hide');
        }
    });

	//SELECT
	//$('.selectpicker').selectpicker();
	//FILE INPUT
	//$('input[type=file]').bootstrapFileInput();

	//ICHECK
	$('input[type=checkbox]').iCheck({
	checkboxClass: 'icheckbox_minimal-grey',
	radioClass: 'iradio_minimal-grey',
	increaseArea: '20%' // optional
	});

 	$('.check-all')
    .on('ifChecked', function(event) {
        $('input').iCheck('check');
    })
    .on('ifUnchecked', function() {
        $('input').iCheck('uncheck');
    });

    //for showing generic colorbox iframes
    $(".iframe").colorbox({iframe:true, width:"50%", height:"50%"});
    $(".add_product_groups").colorbox({iframe:true, width:"46%", height:"42%"});

    //for left hand admin menu
    $('#menu').metisMenu();

    //for setting tabs to dropdowns on smalle screens (phones)
    $('.resp-tabs').tabdrop();

    $(".s2").select2();

    //ajax tabs
    $('a[data-toggle=tab]').on('show.bs.tab', function (e) {
        var currTabTarget = $(e.target).attr('href');

        var remoteUrl = $(this).attr('data-tab-remote');
        var loadedOnce = $(this).data('loaded');
        if (remoteUrl !== '' && !loadedOnce) {
            $(currTabTarget).load(remoteUrl)
            $(this).data('loaded',true);
        }
    })

    //set list group divs active on click
    $(function(){
        $('.list-group a').click(function(e) {
            e.preventDefault()

            $that = $(this);

            $that.parent().find('a').removeClass('active');
            $that.addClass('active');
        });
    })

    $('#prod_form').validate();
});

$(document).ajaxStart(function() {
    $('#loading').show();
    setTimeout(function(){
        $('#loading').fadeOut();}, 500);
});

$(document).ajaxStop(function() {
    $('#loading').hide();
});

$(function () {
    $('[data-toggle="popover"]').popover()
})

function show_password(id)
{
    $('#toggle-'+id).click(function() {
        $('#'+id).attr('type', 'text');
    });
}

function remove_div(id) {
    $(id).fadeOut(300,function(){$(this).remove();});
}

function resizeIframe(obj) {
    var iframeHeight = obj.contentWindow.document.body.scrollHeight - 20;
    obj.style.height = iframeHeight + 'px';
}

function responsive_filemanager_callback(field_id) {
    var url = jQuery('#' + field_id).val();
    $('#' + field_id).val();
    $('#image-' + field_id).attr('src', url);
    $.colorbox.close();
}

function PopupCenter(url, title, w, h) {
    // Fixes dual-screen position                         Most browsers      Firefox
    var dualScreenLeft = window.screenLeft != undefined ? window.screenLeft : screen.left;
    var dualScreenTop = window.screenTop != undefined ? window.screenTop : screen.top;

    width = window.innerWidth ? window.innerWidth : document.documentElement.clientWidth ? document.documentElement.clientWidth : screen.width;
    height = window.innerHeight ? window.innerHeight : document.documentElement.clientHeight ? document.documentElement.clientHeight : screen.height;

    var left = ((width / 2) - (w / 2)) + dualScreenLeft;
    var top = ((height / 2) - (h / 2)) + dualScreenTop;
    var newWindow = window.open(url, title, 'scrollbars=yes, width=' + w + ', height=' + h + ', top=' + top + ', left=' + left);

    // Puts focus on the newWindow
    if (window.focus) {
        newWindow.focus();
    }
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