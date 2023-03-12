/**
 *  @author    DeoTemplate <deotemplate@gmail.com>
 *  @copyright by DeoTemplate
 *  @license   http://deotemplate.com - prestashop template provider
 */

// http://demo.tinywall.net/numscroller/
$(document).ready(function(){
    $(document).ready(function(){
        $(document).rollerInit();
    });

    $(window).on("load scroll resize", function(){
        $('.numscroller').scrollzip({
            showFunction : function() {
                numberRoller($(this).attr('data-slno'));
            },
            wholeVisible : true,
        });
    });

    $.fn.digits = function(){ 
	    return this.each(function(){ 
	        $(this).text( $(this).text().replace(/(\d)(?=(\d\d\d)+(?!\d))/g, "$1,") ); 
	    })
	}
    $.fn.rollerInit=function(){
        let i=0;
        $('.numscroller').each(function() {
            i++;
			$(this).attr('data-slno',i); 
			$(this).addClass("roller-title-number-"+i);
			$(this).text( $(this).text().replace(/%/g, "").replace(/(\d)(?=(\d\d\d)+(?!\d))/g, "$1,") );
        });        
    };
    $.fn.scrollzip = function(options){
        let settings = $.extend({
            showFunction    : false,
            hideFunction    : null,
            showShift       : 0,
            wholeVisible    : false,
            hideShift       : 0,
        }, options);
        return this.each(function(i,obj){
            $(this).addClass('scrollzip');

            if ($.isFunction( settings.showFunction)){
                if (!$(this).hasClass('isShown') && 
                    ($(window).outerHeight() + $(window).scrollTop() - settings.showShift) > ($(this).offset().top + ((settings.wholeVisible) ? $(this).outerHeight() : 0)) &&
                    ($(window).scrollTop() + ((settings.wholeVisible) ? $(this).outerHeight() : 0)) < ($(this).outerHeight() + $(this).offset().top - settings.showShift)
                ){
                    $(this).addClass('isShown');
                    settings.showFunction.call( this );
                }
            }
            if ($.isFunction(settings.hideFunction)){
                if ($(this).hasClass('isShown') &&  
                    (($(window).outerHeight() + $(window).scrollTop() - settings.hideShift) < ($(this).offset().top + ((settings.wholeVisible) ? $(this).outerHeight() : 0)) ||
                    ($(window).scrollTop() + ((settings.wholeVisible) ? $(this).outerHeight() : 0)) > ($(this).outerHeight() + $(this).offset().top - settings.hideShift))
                ){
                    $(this).removeClass('isShown');
                    settings.hideFunction.call( this );
                }
            }

            return this;
        });
    };
    function numberRoller(slno){
    	let min,max,timediff,increment;
    	if($('.roller-title-number-'+slno).attr('data-min')){
        	min=$('.roller-title-number-'+slno).attr('data-min');
    	}else{
    		min="0";
    	}
    	min=min.replace(/[^\d.]/g, "");

    	if($('.roller-title-number-'+slno).attr('data-max')){
        	max=$('.roller-title-number-'+slno).attr('data-max');
    	}else{
    		max=$('.roller-title-number-'+slno).html();
    	}
    	max=max.replace(/[^\d.]/g, "");

    	if($('.roller-title-number-'+slno).attr('data-delay')){
        	timediff=$('.roller-title-number-'+slno).attr('data-delay');
    	}else{
    		timediff=1;
    	}

    	if($('.roller-title-number-'+slno).attr('data-increment')){
    		increment=$('.roller-title-number-'+slno).attr('data-increment');
    	}else{
    		increment=parseInt((max-min)/100);
    		increment=increment == 0 ? 1 : increment;
    	}


        let numdiff=max-min;
        let timeout=(timediff*1000)/numdiff;
        //if(numinc<10){
            //increment=Math.floor((timediff*1000)/10);
        //}//alert(increment);
        $('.roller-title-number-'+slno).addClass('increment-start');
        numberRoll(slno,min,max,increment,timeout);

    }
    function numberRoll(slno,min,max,increment,timeout){//alert(slno+"="+min+"="+max+"="+increment+"="+timeout);
        if(min<=max){
            $('.roller-title-number-'+slno).html(min);
            min=min+increment;
            setTimeout(function(){numberRoll(eval(slno),eval(min),eval(max),eval(increment),eval(timeout))},timeout);
        }else{
        	$('.roller-title-number-'+slno).html(max);
        }
        $('.roller-title-number-'+slno).digits();

        //add class when finish
        if(min>=max){
            $('.roller-title-number-'+slno).addClass('increment-done');
        }
    }
});
