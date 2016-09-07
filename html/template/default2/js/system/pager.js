// ページング処理
$(function(){
	$('.item-list .info:gt(19)').hide(); 	// 初期表示時20件以降は非表示にする

	$(document).on('click','.item-list-pager a',function(){
		current_page = parseInt($('.item-list-pager li.current span').text());
		$('.item-list .info').hide();
		if($(this).parent().hasClass('prev')){
			show_end = ((current_page-1)*20)-1;
			show_start = show_end-20;
			if(show_start <= 0){
				$('.item-list .info:lt('+(show_end+1)+')').show();
			}else{
				$('.item-list .info:gt('+show_start+'):lt(20)').show();
			}
			if((current_page-1) == 1){
				$(this).parent().html('<span>前へ</span>').addClass('disabled');
			}
			$('.item-list-pager li.next').html('<a href="javascript:void(0)">次へ</a>').removeClass('disabled');

			$('.item-list-pager li.current').removeClass('current').html('<a href="javascript:void(0)">'+current_page+'</a>');
			$('.item-list-pager li[data='+(current_page-1)+']').addClass('current').html('<span>'+(current_page-1)+'</span>');

		}else if($(this).parent().hasClass('next')){
			show_end = ((current_page+1)*20)-1;
			show_start = show_end-20;
			$('.item-list .info:gt('+show_start+'):lt(20)').show();

			if((current_page+1) == $(this).parent().prev().text()){
				$(this).parent().html('<span>次へ</span>').addClass('disabled');
				show_end = ($('.item-list .info:visible').length-1)+(show_start+1);
			}
			$('.item-list-pager li.prev').html('<a href="javascript:void(0)">前へ</a>').removeClass('disabled');

			$('.item-list-pager li.current').removeClass('current').html('<a href="javascript:void(0)">'+current_page+'</a>');
			$('.item-list-pager li[data='+(current_page+1)+']').addClass('current').html('<span>'+(current_page+1)+'</span>');

		}else{
			show_end = ($(this).text()*20)-1;
			show_start = show_end-20;
			next_page = parseInt($(this).text());
			if(show_start <= 0){
				$('.item-list .info:lt('+(show_end+1)+')').show();
			}else{
				$('.item-list .info:gt('+show_start+'):lt(20)').show();
			}

			if(next_page == parseInt($('.item-list-pager li.next').prev().text())){
				$('.item-list-pager li.next').html('<span>次へ</span>').addClass('disabled');
				show_end = ($('.item-list .info:visible').length-1)+(show_start+1);
			}else{
				$('.item-list-pager li.next').html('<a href="javascript:void(0)">次へ</a>').removeClass('disabled');
			}
			if(next_page == 1){
				$('.item-list-pager li.prev').html('<span>前へ</span>').addClass('disabled');
			}else{
				$('.item-list-pager li.prev').html('<a href="javascript:void(0)">前へ</a>').removeClass('disabled');
			}
			$('.item-list-pager li.current').removeClass('current').html('<a href="javascript:void(0)">'+current_page+'</a>');
			$('.item-list-pager li[data='+next_page+']').addClass('current').html('<span>'+next_page+'</span>');

		}
		$('p#num span.disp_no').text((show_start+2)+'～'+(show_end+1));
		$("html,body").animate({scrollTop:$('#contents-right').offset().top});
		return false;
	});

});
