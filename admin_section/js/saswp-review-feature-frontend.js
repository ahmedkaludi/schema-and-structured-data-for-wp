/**
 * Featherlight - ultra slim jQuery lightbox
 * Version 1.7.14 - http://noelboss.github.io/featherlight/
 *
 * Copyright 2019, Noël Raoul Bossart (http://www.noelboss.com)
 * MIT Licensed.
**/
!function(a){"function"==typeof define&&define.amd?define(["jquery"],a):"object"==typeof module&&module.exports?module.exports=function(b,c){return void 0===c&&(c="undefined"!=typeof window?require("jquery"):require("jquery")(b)),a(c),c}:a(jQuery)}(function(a){"use strict";function b(a,c){if(!(this instanceof b)){var d=new b(a,c);return d.open(),d}this.id=b.id++,this.setup(a,c),this.chainCallbacks(b._callbackChain)}function c(a,b){var c={};for(var d in a)d in b&&(c[d]=a[d],delete a[d]);return c}function d(a,b){var c={},d=new RegExp("^"+b+"([A-Z])(.*)");for(var e in a){var f=e.match(d);if(f){var g=(f[1]+f[2].replace(/([A-Z])/g,"-$1")).toLowerCase();c[g]=a[e]}}return c}if("undefined"==typeof a)return void("console"in window&&window.console.info("Too much lightness, Featherlight needs jQuery."));if(a.fn.jquery.match(/-ajax/))return void("console"in window&&window.console.info("Featherlight needs regular jQuery, not the slim version."));var e=[],f=function(b){return e=a.grep(e,function(a){return a!==b&&a.$instance.closest("body").length>0})},g={allow:1,allowfullscreen:1,frameborder:1,height:1,longdesc:1,marginheight:1,marginwidth:1,mozallowfullscreen:1,name:1,referrerpolicy:1,sandbox:1,scrolling:1,src:1,srcdoc:1,style:1,webkitallowfullscreen:1,width:1},h={keyup:"onKeyUp",resize:"onResize"},i=function(c){a.each(b.opened().reverse(),function(){return c.isDefaultPrevented()||!1!==this[h[c.type]](c)?void 0:(c.preventDefault(),c.stopPropagation(),!1)})},j=function(c){if(c!==b._globalHandlerInstalled){b._globalHandlerInstalled=c;var d=a.map(h,function(a,c){return c+"."+b.prototype.namespace}).join(" ");a(window)[c?"on":"off"](d,i)}};b.prototype={constructor:b,namespace:"featherlight",targetAttr:"data-featherlight",variant:null,resetCss:!1,background:null,openTrigger:"click",closeTrigger:"click",filter:null,root:"body",openSpeed:250,closeSpeed:250,closeOnClick:"background",closeOnEsc:!0,closeIcon:"&#10005;",loading:"",persist:!1,otherClose:null,beforeOpen:a.noop,beforeContent:a.noop,beforeClose:a.noop,afterOpen:a.noop,afterContent:a.noop,afterClose:a.noop,onKeyUp:a.noop,onResize:a.noop,type:null,contentFilters:["jquery","image","html","ajax","iframe","text"],setup:function(b,c){"object"!=typeof b||b instanceof a!=!1||c||(c=b,b=void 0);var d=a.extend(this,c,{target:b}),e=d.resetCss?d.namespace+"-reset":d.namespace,f=a(d.background||['<div class="'+e+"-loading "+e+'">','<div class="'+e+'-content">','<button class="'+e+"-close-icon "+d.namespace+'-close" aria-label="Close">',d.closeIcon,"</button>",'<div class="'+d.namespace+'-inner">'+d.loading+"</div>","</div>","</div>"].join("")),g="."+d.namespace+"-close"+(d.otherClose?","+d.otherClose:"");return d.$instance=f.clone().addClass(d.variant),d.$instance.on(d.closeTrigger+"."+d.namespace,function(b){if(!b.isDefaultPrevented()){var c=a(b.target);("background"===d.closeOnClick&&c.is("."+d.namespace)||"anywhere"===d.closeOnClick||c.closest(g).length)&&(d.close(b),b.preventDefault())}}),this},getContent:function(){if(this.persist!==!1&&this.$content)return this.$content;var b=this,c=this.constructor.contentFilters,d=function(a){return b.$currentTarget&&b.$currentTarget.attr(a)},e=d(b.targetAttr),f=b.target||e||"",g=c[b.type];if(!g&&f in c&&(g=c[f],f=b.target&&e),f=f||d("href")||"",!g)for(var h in c)b[h]&&(g=c[h],f=b[h]);if(!g){var i=f;if(f=null,a.each(b.contentFilters,function(){return g=c[this],g.test&&(f=g.test(i)),!f&&g.regex&&i.match&&i.match(g.regex)&&(f=i),!f}),!f)return"console"in window&&window.console.error("Featherlight: no content filter found "+(i?' for "'+i+'"':" (no target specified)")),!1}return g.process.call(b,f)},setContent:function(b){return this.$instance.removeClass(this.namespace+"-loading"),this.$instance.toggleClass(this.namespace+"-iframe",b.is("iframe")),this.$instance.find("."+this.namespace+"-inner").not(b).slice(1).remove().end().replaceWith(a.contains(this.$instance[0],b[0])?"":b),this.$content=b.addClass(this.namespace+"-inner"),this},open:function(b){var c=this;if(c.$instance.hide().appendTo(c.root),!(b&&b.isDefaultPrevented()||c.beforeOpen(b)===!1)){b&&b.preventDefault();var d=c.getContent();if(d)return e.push(c),j(!0),c.$instance.fadeIn(c.openSpeed),c.beforeContent(b),a.when(d).always(function(a){a&&(c.setContent(a),c.afterContent(b))}).then(c.$instance.promise()).done(function(){c.afterOpen(b)})}return c.$instance.detach(),a.Deferred().reject().promise()},close:function(b){var c=this,d=a.Deferred();return c.beforeClose(b)===!1?d.reject():(0===f(c).length&&j(!1),c.$instance.fadeOut(c.closeSpeed,function(){c.$instance.detach(),c.afterClose(b),d.resolve()})),d.promise()},resize:function(a,b){if(a&&b){this.$content.css("width","").css("height","");var c=Math.max(a/(this.$content.parent().width()-1),b/(this.$content.parent().height()-1));c>1&&(c=b/Math.floor(b/c),this.$content.css("width",""+a/c+"px").css("height",""+b/c+"px"))}},chainCallbacks:function(b){for(var c in b)this[c]=a.proxy(b[c],this,a.proxy(this[c],this))}},a.extend(b,{id:0,autoBind:"[data-featherlight]",defaults:b.prototype,contentFilters:{jquery:{regex:/^[#.]\w/,test:function(b){return b instanceof a&&b},process:function(b){return this.persist!==!1?a(b):a(b).clone(!0)}},image:{regex:/\.(png|jpg|jpeg|gif|tiff?|bmp|svg)(\?\S*)?$/i,process:function(b){var c=this,d=a.Deferred(),e=new Image,f=a('<img src="'+b+'" alt="" class="'+c.namespace+'-image" />');return e.onload=function(){f.naturalWidth=e.width,f.naturalHeight=e.height,d.resolve(f)},e.onerror=function(){d.reject(f)},e.src=b,d.promise()}},html:{regex:/^\s*<[\w!][^<]*>/,process:function(b){return a(b)}},ajax:{regex:/./,process:function(b){var c=a.Deferred(),d=a("<div></div>").load(b,function(a,b){"error"!==b&&c.resolve(d.contents()),c.reject()});return c.promise()}},iframe:{process:function(b){var e=new a.Deferred,f=a("<iframe/>"),h=d(this,"iframe"),i=c(h,g);return f.hide().attr("src",b).attr(i).css(h).on("load",function(){e.resolve(f.show())}).appendTo(this.$instance.find("."+this.namespace+"-content")),e.promise()}},text:{process:function(b){return a("<div>",{text:b})}}},functionAttributes:["beforeOpen","afterOpen","beforeContent","afterContent","beforeClose","afterClose"],readElementConfig:function(b,c){var d=this,e=new RegExp("^data-"+c+"-(.*)"),f={};return b&&b.attributes&&a.each(b.attributes,function(){var b=this.name.match(e);if(b){var c=this.value,g=a.camelCase(b[1]);if(a.inArray(g,d.functionAttributes)>=0)c=new Function(c);else try{c=JSON.parse(c)}catch(h){}f[g]=c}}),f},extend:function(b,c){var d=function(){this.constructor=b};return d.prototype=this.prototype,b.prototype=new d,b.__super__=this.prototype,a.extend(b,this,c),b.defaults=b.prototype,b},attach:function(b,c,d){var e=this;"object"!=typeof c||c instanceof a!=!1||d||(d=c,c=void 0),d=a.extend({},d);var f,g=d.namespace||e.defaults.namespace,h=a.extend({},e.defaults,e.readElementConfig(b[0],g),d),i=function(g){var i=a(g.currentTarget),j=a.extend({$source:b,$currentTarget:i},e.readElementConfig(b[0],h.namespace),e.readElementConfig(g.currentTarget,h.namespace),d),k=f||i.data("featherlight-persisted")||new e(c,j);"shared"===k.persist?f=k:k.persist!==!1&&i.data("featherlight-persisted",k),j.$currentTarget.blur&&j.$currentTarget.blur(),k.open(g)};return b.on(h.openTrigger+"."+h.namespace,h.filter,i),{filter:h.filter,handler:i}},current:function(){var a=this.opened();return a[a.length-1]||null},opened:function(){var b=this;return f(),a.grep(e,function(a){return a instanceof b})},close:function(a){var b=this.current();return b?b.close(a):void 0},_onReady:function(){var b=this;if(b.autoBind){var c=a(b.autoBind);c.each(function(){b.attach(a(this))}),a(document).on("click",b.autoBind,function(d){if(!d.isDefaultPrevented()){var e=a(d.currentTarget),f=c.length;if(c=c.add(e),f!==c.length){var g=b.attach(e);(!g.filter||a(d.target).parentsUntil(e,g.filter).length>0)&&g.handler(d)}}})}},_callbackChain:{onKeyUp:function(b,c){return 27===c.keyCode?(this.closeOnEsc&&a.featherlight.close(c),!1):b(c)},beforeOpen:function(b,c){return a(document.documentElement).addClass("with-featherlight"),this._previouslyActive=document.activeElement,this._$previouslyTabbable=a("a, input, select, textarea, iframe, button, iframe, [contentEditable=true]").not("[tabindex]").not(this.$instance.find("button")),this._$previouslyWithTabIndex=a("[tabindex]").not('[tabindex="-1"]'),this._previousWithTabIndices=this._$previouslyWithTabIndex.map(function(b,c){return a(c).attr("tabindex")}),this._$previouslyWithTabIndex.add(this._$previouslyTabbable).attr("tabindex",-1),document.activeElement.blur&&document.activeElement.blur(),b(c)},afterClose:function(c,d){var e=c(d),f=this;return this._$previouslyTabbable.removeAttr("tabindex"),this._$previouslyWithTabIndex.each(function(b,c){a(c).attr("tabindex",f._previousWithTabIndices[b])}),this._previouslyActive.focus(),0===b.opened().length&&a(document.documentElement).removeClass("with-featherlight"),e},onResize:function(a,b){return this.resize(this.$content.naturalWidth,this.$content.naturalHeight),a(b)},afterContent:function(a,b){var c=a(b);return this.$instance.find("[autofocus]:not([disabled])").focus(),this.onResize(b),c}}}),a.featherlight=b,a.fn.featherlight=function(a,c){return b.attach(this,a,c),this},a(document).ready(function(){b._onReady()})});

jQuery(document).ready(function($){

	saswp_load_single_rating( 0 );

	saswp_load_multi_rating( 0 );

	function saswp_load_single_rating( isEdit = 0 ) {
		let rating = saswp_rf_localize_data.default_rating;
		let adminRyRating 		=	0;
		if ( typeof saswp_rf_localize_data.admin_ry_rating != 'undefined' ) {
			adminRyRating 		=	saswp_rf_localize_data.admin_ry_rating;	
		}
		let id = '#saswp-rf-form-rating';
		if ( isEdit == 1 || adminRyRating == 1 ) {
			id = '#saswp-rf-edit-form-rating';
			if ( typeof id != 'undefined' ) {
				rating = $(id).next().next().val();
			}
		}
		// For Single layout
		if ( $(id).length > 0 ) {
			$(id ).rateYo({              
		      rating : rating,
		      spacing: "5px",                          
		      onSet: function (rating, rateYoInstance) {
		        $(this).next().next().val(rating);                
		        }                              
		    }).on("rateyo.change", function(e, data){
		        var rating = data.rating;              
		        $(this).next().text(rating);
		    });
		}
	}

	function saswp_load_multi_rating( isEdit = 0 ) {
		let multiCriteriaCnt 	=	saswp_rf_localize_data.saswp_multi_criteria_count;
		let adminRyRating 		=	0;
		if ( typeof saswp_rf_localize_data.admin_ry_rating != 'undefined' ) {
			adminRyRating 		=	saswp_rf_localize_data.admin_ry_rating;	
		}
		// For multi criteria layout
		if ( typeof saswp_localize_front_data != 'undefined' && multiCriteriaCnt > 0 ) {
			for ( i = 1; i <= multiCriteriaCnt; i++ ) {
				let id = "#saswp-rf-form-multi-criteria-" + i;
				let rating = saswp_rf_localize_data.default_rating;
				if ( isEdit == 1 || adminRyRating == 1 ) {
					id = "#saswp-rf-edit-form-multi-criteria-" + i;
					if ( typeof id != 'undefined' ) {
						rating = $(id).next().next().val();
					} 
				}
				$(id).rateYo({              
			      rating : rating,
			      spacing: "5px",                          
			      onSet: function (rating, rateYoInstance) {
			        $(this).next().next().val(rating);                
			        }                              
			    }).on("rateyo.change", function(e, data){
			        var rating = data.rating;              
			        $(this).next().text(rating);
			    });	
			}
		}
	}

	$(document).on('click', '.saswp-rf-form-remove-field', function(e) {
		e.preventDefault();
		$(this).parent().remove();
		let numFields 	=	parseInt( $('#saswp-rf-form-pros-field-wrapper').find('div').length ) + 1;
		if ( numFields <= saswp_rf_localize_data.saswp_rfpage_settings_pros_cons_limit ) {
			$('#saswp-rf-form-add-pros-field').show();
		}

		numFields 	=	parseInt( $('#saswp-rf-form-cons-field-wrapper').find('div').length ) + 1;
		if ( numFields <= saswp_rf_localize_data.saswp_rfpage_settings_pros_cons_limit ) {
			$('#saswp-rf-form-add-cons-field').show();
		}

	});

	$(document).on('click', '#saswp-rf-form-add-pros-field', function(e) {
		e.preventDefault();

		let numFields 	=	parseInt( $('#saswp-rf-form-pros-field-wrapper').find('div').length ) + 1;

		if ( numFields <= saswp_rf_localize_data.saswp_rfpage_settings_pros_cons_limit ) {
			let newField 	=	'';
			newField 		+=	`<div class="saswp-rf-form-input-filed">`;
			newField 		+=	`<span class="saswp-rf-form-remove-field">+</span>`;
			newField 		+=	`<input type="text" class="form-control" name="saswp_rf_form_pros[]" placeholder="Write Pros">`;
			newField 		+=	`</div>`;

			$('#saswp-rf-form-pros-field-wrapper').append(newField);
			if ( numFields == saswp_rf_localize_data.saswp_rfpage_settings_pros_cons_limit ) {
				$('#saswp-rf-form-add-pros-field').hide();
			}
		}else{
			$('#saswp-rf-form-add-pros-field').hide();
		}
	});

	$(document).on('click', '#saswp-rf-form-add-cons-field', function(e) {
		e.preventDefault();
		
		let numFields 	=	parseInt( $('#saswp-rf-form-cons-field-wrapper').find('div').length ) + 1;

		if ( numFields <= saswp_rf_localize_data.saswp_rfpage_settings_pros_cons_limit ) {
			let newField 	=	'';
			newField 		+=	`<div class="saswp-rf-form-input-filed">`;
			newField 		+=	`<span class="saswp-rf-form-remove-field">+</span>`;
			newField 		+=	`<input type="text" class="form-control" name="saswp_rf_form_cons[]" placeholder="Write Cons">`;
			newField 		+=	`</div>`;
			$('#saswp-rf-form-cons-field-wrapper').append(newField);
			if ( numFields == saswp_rf_localize_data.saswp_rfpage_settings_pros_cons_limit ) {
				$('#saswp-rf-form-add-cons-field').hide();
			}
		}else{
			$('#saswp-rf-form-add-cons-field').hide();
		}
	});


	function saswp_video_source_option() {
        let video_source = $("#saswp-rf-form-video-source").val();
        if (video_source == 'self') {
            $('.saswp-rf-form-source-video').show();
            $('.saswp-rf-form-source-external').hide();
        } else {
            $('.saswp-rf-form-source-video').hide();
            $('.saswp-rf-form-source-external').show();
        }
    }

    saswp_video_source_option();

    //edit review
    $(document).on('click', '.saswp-rf-template-review-edit-btn', function (e) {
        e.preventDefault();

        let comment_post_id = $(this).attr('data-comment-post-id');
        let comment_id = $(this).attr('data-comment-id');
        let $this = $(this);

        $.ajax({
            type: "post",
            dataType: "json",
            url: saswp_rf_localize_data.ajaxurl,
            data: {
                action: "saswp_rf_template_review_edit_form",
                comment_post_id: comment_post_id,
                comment_id: comment_id,
                saswp_rf_form_nonce: saswp_rf_localize_data.saswp_rf_page_security_nonce,
            },
            beforeSend: function () {
                $this.html('(' + saswp_rf_localize_data.loading + ')');
            },
            success: function (resp) {
                if (resp.success) {
                    $this.html('(' + saswp_rf_localize_data.edit + ')');
                    $('body').prepend(resp.data);
                    // Load star rating
                    saswp_load_single_rating( 1 );
                    // Load multi star rating
                    saswp_load_multi_rating( 1 );
                    //load again video sources
                    saswp_video_source_option();
                    $('#saswp-rf-form-video-source').on('change', function () {
                        saswp_video_source_option();
                    });
                } else {
                    console.log(resp.data);
                }
            },
        });
    });

    //edit review
    $(document).on('click', '.saswp-rf-edit-submit', function (e) {
        e.preventDefault();

        let form = $(this).parents('form').serialize();
        form += '&saswp_rf_form_nonce=' + saswp_rf_localize_data.saswp_rf_page_security_nonce
        
        $.ajax({
            type: "post",
            dataType: "json",
            url: saswp_rf_localize_data.ajaxurl,
            data: form,
            success: function (resp) {
                if (resp.success) {
                    location.reload();
                } else {
                    console.log(resp.data);
                }
            },
        });
    });

    //hide review outside click
    $(document).on('mouseup', function (e) {
        let container = $(".saswp-rf-review-popup");
        if (!container.is(e.target) && container.has(e.target).length === 0) {
            $(".saswp-rf-modal").remove();
        }
    });

    $('#saswp-rf-form-video-source').on('change', function () {
        saswp_video_source_option();
    });
	
	// Upload image and display preview
	$(document).on('click', '#saswp-rf-form-upload-box-image', function () { 
		$('#saswp-rf-form-image').trigger('click'); 
	});

	//self hosted video popup
    $(document).on('click', '.saswp-rf-template-review-play-self-video', function (e) {
        e.preventDefault();

        let video_url = $(this).attr('data-video-url');

        $.ajax({
            type: "post",
            dataType: "json",
            url: saswp_rf_localize_data.ajaxurl,
            data: {
                action: "saswp_rf_form_self_video_popup",
                saswp_rf_form_nonce: saswp_rf_localize_data.saswp_rf_page_security_nonce,
                video_url
            },
            success: function (resp) {
                if (resp.success) {
                    $('body').prepend(resp.data);
                }
            },
        });
    });

	// Click input button
	$(document).on('change', '#saswp-rf-form-image', function (e) {
        let file_data, form_data;
        file_data = $(this).prop('files')[0];
        form_data = new FormData();
        form_data.append('saswp-rf-form-image', file_data);
        form_data.append('saswp_rf_form_nonce', saswp_rf_localize_data.saswp_rf_page_security_nonce);
        form_data.append('action', 'saswp_rf_form_image_upload');

        $.ajax({
            url: saswp_rf_localize_data.ajaxurl,
            type: 'POST',
            contentType: false,
            processData: false,
            data: form_data,
            beforeSend: function () {
                $('.saswp-rf-form-image-error').html('');
                $('#saswp-rf-form-upload-box-image span').html(saswp_rf_localize_data.loading);
            },
            success: function (resp) {
                if (resp.success) {
                    $('.saswp-rf-form-preview-imgs').append("<div class='saswp-rf-form-preview-img'><img src='" + resp.data.file_info.url + "' /><input type='hidden' name='saswp_rf_form_attachment[imgs][]' value='" + resp.data.file_info.id + "'><span class='saswp-rf-form-file-remove' data-id='" + resp.data.file_info.id + "'>x</span></div>");
                } else {
                    $('.saswp-rf-form-image-error').html(resp.data.msg);
                }
                $('#saswp-rf-form-upload-box-image span').html(saswp_rf_localize_data.upload_img);
            }
        });
    });

    // Remove image
    $(document).on('click', '.saswp-rf-form-file-remove', function (e) {
        e.preventDefault();
        let attachment_id = $(this).data('id');
        if (confirm(saswp_rf_localize_data.sure_txt)) {
            $(this).parent().remove();
            $.ajax({
                type: "post",
                dataType: "json",
                url: saswp_rf_localize_data.ajaxurl,
                data: {
                    action: "saswp_rf_form_remove_file",
                    attachment_id: attachment_id,
                    saswp_rf_form_nonce: saswp_rf_localize_data.saswp_rf_page_security_nonce,
                },
                success: function () { },
            });
        }
    });

    //upload video
    $(document).on('click', '#saswp-rf-form-upload-box-video', function () { $('#saswp-rf-form-video').trigger('click'); });

    $(document).on('change', '#saswp-rf-form-video', function (e) {
        let file_data, form_data;
        file_data = $(this).prop('files')[0];
        
        form_data = new FormData();
        form_data.append('saswp-rf-form-video', file_data);
        form_data.append('saswp_rf_form_nonce', saswp_rf_localize_data.saswp_rf_page_security_nonce);
        form_data.append('action', 'saswp_rf_form_video_upload');

        $.ajax({
            url: saswp_rf_localize_data.ajaxurl,
            type: 'POST',
            contentType: false,
            processData: false,
            data: form_data,
            beforeSend: function () {
                $('.saswp-rf-form-video-error').html('');
                $('#saswp-rf-form-upload-box-video span').html(saswp_rf_localize_data.loading);
            },
            success: function (resp) {
                if (resp.success) {
                    $('.saswp-rf-form-preview-videos').append("<div class='saswp-rf-form-preview-video'><span class='name'>" + resp.data.file_info.name + "</span><input type='hidden' name='saswp_rf_form_attachment[videos][]' value='" + resp.data.file_info.id + "'><span class='saswp-rf-form-file-remove'>x</span></div>");
                } else {
                    $('.saswp-rf-form-video-error').html(resp.data.msg);
                }
                $('#saswp-rf-form-upload-box-video span').html(saswp_rf_localize_data.upload_video);
            },
            error: function (xhr, status, error) {
			    console.log('Error:', error);
			    console.log('Status:', status);
			    console.log('Response:', xhr.responseText);
			}
        });
    });

    // Hide all elements when clicked on reply
    $(document).on('click', '.saswp-rf-template-reply-btn', function(e){
    	$('.saswp-rf-form-hide-reply').hide();
    });

    // Show all elements when clicked on cancel reply
    $(document).on('click', '.saswp-rf-form #cancel-comment-reply-link', function(e){
    	$('.saswp-rf-form-hide-reply').show();
    });


    const url = new URL(window.location.href);

    // review filter
    $('.saswp_rf_template_review_filter').on('change', function (e) {
        let select_value = this.value;
        let data_type = $(this).data('type');

        if (data_type === 'sort') {
            url.searchParams.set('sort_by', select_value);
        } else {
            url.searchParams.set('filter_by', select_value);
        }
        window.history.replaceState(null, null, url);

        let sort_by = url.searchParams.get('sort_by');

        let filter_by = url.searchParams.get('filter_by');

        $.ajax({
            type: "post",
            dataType: "json",
            url: saswp_rf_localize_data.ajaxurl,
            data: {
                action: "saswp_rf_template_review_filter",
                post_id: saswp_rf_localize_data.post_id,
                saswp_rf_form_nonce: saswp_rf_localize_data.saswp_rf_page_security_nonce,
                sort_by,
                filter_by,
            },
            beforeSend: function () {
                $('.saswp-rf-template-paginate').html(saswp_rf_localize_data.loading);
            },
            success: function (resp) {
                if (resp.success) {
                    $('.saswp-rf-template-comment-list').html(resp.data.review);
                    $('.saswp-rf-template-paginate').html(resp.data.pagination);
                }

            },
        });
    });

    //Ajax load more review
    // we will remove the button and load its new copy with AJAX, that's why $('body').on()
    $('body').on('click', '#saswp-rf-template-load-more', function () {
        let max_page = $('#saswp-rf-template-load-more').attr('data-max');
        let btn = $('#saswp-rf-template-load-more');
        let sort_by = url.searchParams.get('sort_by');
        let current_page = parseInt( saswp_rf_localize_data.current_page );
        if ( current_page == 1 ) {
        	current_page++;	
        }

        $.ajax({
            url: saswp_rf_localize_data.ajaxurl,
            data: {
                action: 'saswp_rf_template_pagination',
                post_id: saswp_rf_localize_data.post_id,
                current_page: current_page,
                max_page: max_page,
                pagi_num: true,
                saswp_rf_form_nonce: saswp_rf_localize_data.saswp_rf_page_security_nonce,
                sort_by,
            },
            type: 'POST',
            dataType: "json",
            beforeSend: function () {
                btn.text(saswp_rf_localize_data.loading);
            },
            success: function (resp) {
                btn.text('Load More');
                if (resp.success) {
                    $('.saswp-rf-template-comment-list').append(resp.data.review);
                }

                saswp_rf_localize_data.current_page++;
                if (saswp_rf_localize_data.current_page == max_page) {
                    btn.remove();
                }
            }
        });
        return false;
    });

    //Ajax pagination with number
    // we will remove the button and load its new copy with AJAX, that's why $('body').on()
    $('body').on('click', '.saswp-rf-template-paginate-ajax a', function (e) {
        e.preventDefault();
        let pagi_url = $(this).attr('href');
        let prag_match = pagi_url.match('/comment-page-([0-9]+)/');
        let current_page = 1;
        if (prag_match) {
            current_page = prag_match[1];
        }

        let max_page = $(this).parent().attr('data-max');
        let sort_by = url.searchParams.get('sort_by');

        $.ajax({
            url: saswp_rf_localize_data.ajaxurl,
            data: {
                action: 'saswp_rf_template_pagination',
                post_id: saswp_rf_localize_data.post_id,
                current_page: current_page,
                max_page: max_page,
                pagi_num: true,
                saswp_rf_form_nonce: saswp_rf_localize_data.saswp_rf_page_security_nonce,
                sort_by,
            },
            type: 'POST',
            dataType: "json",
            beforeSend: function () {
                $('.saswp-rf-template-paginate-ajax').html(saswp_rf_localize_data.loading);
            },
            success: function (resp) {
                if (resp.success) {
                    $('.saswp-rf-template-comment-list').html(resp.data.review);
                    $('.saswp-rf-template-paginate-ajax').html(resp.data.pagination);
                }
            }
        });
        return false;
    });

    //on scroll pagination
    if ($(".saswp-rf-template-paginate-onscroll").length > 0) {
        let onScrollPagi = true;
        $(window).scroll(function () {

            if (!onScrollPagi) return;

            let bottomOffset = 2900; // the distance (in px) from the page bottom when you want to load more posts

            let max_page = $('.saswp-rf-template-paginate-onscroll').attr('data-max');
            let sort_by = url.searchParams.get('sort_by');

            if (saswp_rf_localize_data.current_page >= max_page) {
                onScrollPagi = false;
                return;
            }

            let current_page = parseInt( saswp_rf_localize_data.current_page );
	        if ( current_page == 1 ) {
	        	current_page++;	
	        }
            
            if ($(document).scrollTop() > ($(document).height() - bottomOffset) && onScrollPagi == true) {
                $.ajax({
                    url: saswp_rf_localize_data.ajaxurl,
                    data: {
                        action: 'saswp_rf_template_pagination',
                        post_id: saswp_rf_localize_data.post_id,
		                current_page: current_page,
		                max_page: max_page,
		                pagi_num: true,
                		saswp_rf_form_nonce: saswp_rf_localize_data.saswp_rf_page_security_nonce,
                        sort_by: sort_by,
                    },
                    type: 'POST',
                    dataType: "json",
                    beforeSend: function () {
                        $('.saswp-rf-template-paginate-onscroll').html(saswp_rf_localize_data.loading);
                        onScrollPagi = false;
                    },
                    success: function (resp) {
                        if (resp.success) {
                            $('.saswp-rf-template-comment-list').append(resp.data.review);
                            $('.saswp-rf-template-paginate-onscroll').html('');
                            saswp_rf_localize_data.current_page++;
                            onScrollPagi = true;

                        }
                    }
                });
            }
        });
    }

    $('#comment_form').removeAttr('novalidate');

    // Comment like and dislike
    $(document).on('click', '.saswp-rf-template-review-helpful label', function(e){

    	e.preventDefault();
    	$('.saswp-rf-template-review-helpful label input').removeAttr('checked');
    	let findRadio   =	$(this).find('.saswp-rf-form-helful-radio');
        findRadio.prop('checked', true);
        let radioVal    =	findRadio.val();

        let helpCount 	=	$(this).find('.saswp-rf-template-helpful-count').text();
        helpCount 		=	parseInt( helpCount );

        let commentID 	= 	$(findRadio).data('comment-id');
        let type 		= 	radioVal;

        $.ajax({
            type: "post",
            dataType: "json",
            url: saswp_rf_localize_data.ajaxurl,
            data: {
                action: "saswp_rf_template_review_helpful",
                comment_id: commentID,
                type: type,
                saswp_rf_form_nonce: saswp_rf_localize_data.saswp_rf_page_security_nonce,
            },
            beforeSend: function () {
            },
            success: function (resp) {
           		if ( resp.success ) {
           			if (resp.data) {
           				$('#saswp-rf-template-helpful-like-count').text(resp.data.likes);
           				$('#saswp-rf-template-helpful-dislike-count').text(resp.data.dislikes);
           			}
           		}   
            }
        });
    	
    });

    // Review Highlight changes
    $(document).on("change", '.saswp-rf-template-review-highlight', function (e) {
        let commentID = $(this).data('comment-id');
        let highlight = 'no';
        if ( $(this).is(':checked') ) {
        	highlight =	'yes';
        	$(this).closest(".saswp-rf-template-single-review").addClass('saswp-rf-template-top-review');
        }else{
        	$(this).closest(".saswp-rf-template-single-review").removeClass('saswp-rf-template-top-review');
        }

        $.ajax({
            type: "post",
            dataType: "json",
            url: saswp_rf_localize_data.ajaxurl,
            data: {
                action: "saswp_template_review_hightlight",
                comment_id: commentID,
                highlight: highlight,
                saswp_rf_form_nonce: saswp_rf_localize_data.saswp_rf_page_security_nonce,
            },
            beforeSend: function () {
            },
            success: function (resp) {
            },
        });
    });

    // Display highlight on hover
    $(document).on('mouseenter', '.saswp-rf-template-single-review', function(e) {

    	let findHighlight 	=	 $(this).find('.saswp-rf-form-highlight-wrapper');
    	if ( findHighlight.length > 0 ) {
    		findHighlight.removeClass('saswp-rf-template-hide');
    	}

    });

    // Hide highlight on mouse leave
    $(document).on('mouseleave', '.saswp-rf-template-single-review', function(e) {
    	
    	let findHighlight 	=	 $(this).find('.saswp-rf-form-highlight-wrapper');
    	if ( findHighlight.length > 0 ) {
    		findHighlight.addClass('saswp-rf-template-hide');
    	}

    });

});