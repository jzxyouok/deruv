var isInitSkin = false;
function skinSwitch(){
    if (!isInitSkin){
        var skin = Cookies.get('skin');

        $.each(SkinConfs,function(i,o){
            var selected = "";
            if (skin == o.class){
                selected =  'selected';
            }

            $('#SkinBox').append('<li><img class="'+selected+'" src="' + o.thumb + '" skin="' + o.class + '" ><span class="tempName">' + o.name + '</span><div class="mask"></div></li>');
        });
        
        $('#SkinBox img').click(function(){
            var skin = $(this).attr("skin");
            $('body:first').attr("class", skin);
            Cookies.set('skin', skin, { expires: 365, path: '/' });

            $('#SkinBox img').removeClass("selected");
            $(this).addClass("selected");
        });

    }
    isInitSkin = true;

    $('#skinModal').modal({
      backdrop: false
    });
    $('#skinModal').modal('show');        
}

function initSkin(){
    var skin = Cookies.get('skin');
    if (!skin){
        skin = SkinConfs[0].class;
    }    

    $('body:first').attr("class", skin);
}

function initTop(){
    if ($(window).width() > 1000)
    {
        $('#sidepanel').css('right', ($(window).width()-$('.site-main').outerWidth(true)) / 2 - 35 );
        $(window).on('scroll', function(){
            if ($(document).scrollTop() > 300) {
                $("#sidepanel").show();
            } else {
                $("#sidepanel").hide();
            }
        })

        $("#sidepanel a").click(function(){
            $("html,body").animate({scrollTop:0}, 180);
        })
    }
}

function getNotice() {
	$.getJSON('index.php?r=notice/list',function(data){
		if (!data || !data.length) {
			return;
		}
		$.each(data, function(k,v){
			var html = '<li><a href="index.php?r=log-action/view&ajax=1&id=' + v.id + '" target="_blank" ><i class="glyphicon glyphicon-exclamation-sign '+ (toInt(v.level) == 2 ? 'text-danger' : '' ) +'"></i> <span class="notice-title">' +
			v.title + '</span><span class="pull-right text-muted small">' + v.ago + '</span></a></li>';
			$('.headnotice-menu').append(html);			
		});
		$('#headnotice-count').text(data.length);
	});
}

$(function(){
	getNotice();
    initSkin();
    initTop();
});