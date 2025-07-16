/******************************************
 *
 *   ShowMoreLess v1.0 - jQuery
 *
 ******************************************/
(function ($) {
    let pluginName = 'myOwnLineShowMoreLess';

    let d = 0;

    let initShowMoreLess=function(data){
        let $that=$(this);
        let $thatDisplayVal=$that.css("display");
        let $thatFontSize=parseInt($that.css("font-size"),10);
        if($thatDisplayVal!=="inline"){
            $that.css({'display':'inline-block'});
        }
        let $span=$('<span>');
        let jsElem=$that.get(0);
        let compStyles = window.getComputedStyle(jsElem);
        let $thatLineHeight=compStyles.getPropertyValue('line-height');

        if($thatLineHeight=='normal'){
            var nodeName = jsElem.nodeName;
            var _node = document.createElement(nodeName);
            _node.innerHTML = '&nbsp;';

            let compStyles1 = window.getComputedStyle(jsElem);
            let fontSizeStr=compStyles1.getPropertyValue('font-size');
            _node.style.fontSize = fontSizeStr;

            _node.style.padding = '0px';
            _node.style.border = '0px';

            var body = document.body;
            body.appendChild(_node);

            var height = _node.offsetHeight;
            $thatLineHeight = height;

            body.removeChild(_node);
        }
        let $thatLineHeightInPx=parseInt($thatLineHeight);
        let restrictedLine=parseInt(data.settings.showLessLine);
        console.log($thatLineHeightInPx,restrictedLine);

        let currentState=(data.settings.lessAtInitial)?'less':'more';

        if(data.settings.lessAtInitial){
            $that.css({
                'max-height':($thatLineHeightInPx*restrictedLine)+'px',
                'overflow':'hidden',
                'position':'relative',
            });
            $span.html(data.settings.showMoreText);
            $span.attr('class','show-more-less-handler');
            $span.attr('style',' background: rgb(255,255,255);background: linear-gradient(90deg, rgba(255,255,255,0) 0%, rgba(255,255,255,1) 17%, rgba(255,255,255,1) 100%); ');
            $span.css({
                'position':'absolute',
                'right':'0',
                'bottom':'0',
                'padding-left': '22px',
                'display':'inline-block',
                'cursor':'pointer',
            });
        }
        else{
            $that.css({
                'position':'relative'
            });
            $span.html(data.settings.showLessText);
            $span.attr('class','show-more-less-handler');
            $span.attr('style',' background: rgb(255,255,255);background: linear-gradient(90deg, rgba(255,255,255,0) 0%, rgba(255,255,255,1) 17%, rgba(255,255,255,1) 100%); ');
            $span.css({
                'position':'static',
                'right':'0',
                'bottom':'0',
                'padding-left': '22px',
                'display':'inline-block',
                'cursor':'pointer'
            });
        }

        $span.on('click',function(){
            if (currentState=='less') {
                currentState='more';
                $that.css({
                    'position':'relative',
                    'overflow':'auto',
                    'max-height':'none'
                });
                $span.html(data.settings.showLessText);
                $span.attr('class','show-more-less-handler');
                $span.attr('style',' background: rgb(255,255,255);background: linear-gradient(90deg, rgba(255,255,255,0) 0%, rgba(255,255,255,1) 17%, rgba(255,255,255,1) 100%); ');
                $span.css({
                    'position':'static',
                    'right':'0',
                    'bottom':'0',
                    'padding-left': '22px',
                    'display':'inline-block',
                    'cursor':'pointer'
                });
                if(data.settings.lessAtInitial && !data.settings.showLessAfterMore){
                    $span.remove();
                }
            }
            else{
                currentState='less';
                $that.css({
                    'max-height':($thatLineHeightInPx*restrictedLine)+'px',
                    'overflow':'hidden',
                    'position':'relative'
                });
                $span.html(data.settings.showMoreText);
                $span.css({
                    'position':'absolute',
                    'right':'0',
                    'bottom':'0',
                    'padding-left': '22px',
                    'display':'inline-block',
                    'cursor':'pointer'
                });
            }

        });
        $that.append($span);
    };

    let methods =
        {
            init : function(options)
            {
                //"this" is a jquery object on which this plugin has been invoked.
                return this.each(function(index)
                {
                    let $this = $(this);
                    let data = $this.data(pluginName);
                    // If the plugin hasn't been initialized yet
                    if (!data)
                    {
                        let settings =
                            {
                                showLessLine:1,
                                showLessText:'Show Less',
                                showMoreText:'Show More',
                                lessAtInitial:true,
                                showLessAfterMore:true,
                            };

                        if(options)
                        {
                            $.extend(true, settings, options);
                        }

                        $this.data(pluginName,
                            {
                                target : $this,
                                settings: settings
                            });

                        var $this2 = $(this)
                        var data2 = $this.data(pluginName);

                        initShowMoreLess.call($this2, data2);
                    }
                });
            }
        };

    $.fn[pluginName] = function( method )
    {
        if ( methods[method] )
        {
            return methods[method].apply( this, Array.prototype.slice.call( arguments, 1 ));
        } else if ( typeof method === 'object' || !method )
        {
            return methods.init.apply( this, arguments );
        } else
        {
            $.error( 'Method ' + method + ' does not exist in jQuery.' + pluginName );
        }
    };
}(jQuery));
