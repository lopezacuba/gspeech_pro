(function($) {
$(document).ready(function() {
	
	function check_pro_version_by_class() {
		return true;
	}
	
	function navigate_tooltip($tooltip) {
		
		
		var center_offset_x = parseFloat($tooltip.parent('span').find('.sound_container_pro ').width() / 2);
		var tooltip_width = parseFloat($tooltip.find('.tooltip_inner').width());
		
		if($tooltip.find('.the-tooltip').hasClass('center')) {
			var final_offset = -1 * ((tooltip_width / 2) * 1 + 14 * 1 - center_offset_x * 1);
			$tooltip.css({'left': final_offset, opacity: 0,display: 'block'});
		}
		else if($tooltip.find('.the-tooltip').hasClass('left')) {
			var final_offset = -32 * 1 + center_offset_x * 1;
			$tooltip.css({'left': final_offset, opacity: 0,display: 'block'});
		}
		else if($tooltip.find('.the-tooltip').hasClass('right')) {
			var final_offset = 32 * 1 + center_offset_x * 1;
			$tooltip.css({'left': final_offset, opacity: 0,display: 'block'});
		}
		
		
		if($tooltip.find('.the-tooltip').hasClass('top')) {
			$tooltip.css({'top': -200});
			var new_opacity = 0.95;
			$tooltip.stop(false,false).animate( {
				top: 1,
				opacity: new_opacity
			},300,'easeOutBack');
		}
		else if($tooltip.find('.the-tooltip').hasClass('bottom')) {
			if($tooltip.find('.tooltip_inner').hasClass('powered_by'))
				var new_opacity = 0.95;
			else
				var new_opacity = 0.95;
			
			var h = -1 * parseFloat($tooltip.parent('span').find('.sound_container_pro ').height());
			$tooltip.css({'bottom': -200});
			$tooltip.stop(false,false).animate( {
				bottom: h,
				opacity: new_opacity
			},300,'easeOutBack');
		}
	}
	function hide_tooltip($tooltip) {
		if($tooltip.find('.the-tooltip').hasClass('top')) {
			$tooltip.stop(true,true).delay(200).animate( {
				top: -200,
				opacity: 0
			},300,'easeInBack',function() {
				$(this).hide();
			});
		}
		else if($tooltip.find('.the-tooltip').hasClass('bottom')) {
			$tooltip.stop(true,true).delay(200).animate( {
				bottom: -200,
				opacity: 0
			},300,'easeInBack',function() {
				$(this).hide();
			});
		}
	}
	
	
	$('.sound_container_pro').each(function(i) {
		$elem = $(this);
		var autoplay = $(this).attr("autoplaypro");
		var speechtimeout =$(this).attr("speechtimeout");
		if(isNaN(speechtimeout))
			speechtimeout = 0;
		speechtimeout = parseFloat(speechtimeout);
		var selector =$(this).attr("selector");
		var event =$(this).attr("eventpro");
		
		if(selector != '' && event != '') {
			createtriggerspeech($elem,speechtimeout,selector,event);
			createtriggerspeechcount ++;
		}
		else if(autoplay == 1) {
			// 2.7.0
			var autoplay_ = parseInt($elem.data('autoplay_'));
			autoplay_ = isNaN(autoplay_) ? 1 : autoplay_;
			if(autoplay_ == 1) {
				triggerspeech($elem,speechtimeout);
			}
		};
		
	});
	
	function triggerspeech($elem,speechtimeout) {
		speechtimeoutfinal = speechtimeoutfinal + speechtimeout*1 + 200*1;
		setTimeout(function() {
			stop_all_speakers_pro($('.sound_container_pro'));
			clearAllPlayers();
			setTimeout(function() {
				make_speeching($elem);
			},10);
		},speechtimeoutfinal);
	};
	
	function createtriggerspeech($elem,speechtimeout,selector,event) {
		$("" + selector + "").bind(event, function() {
			var $active_elem = $(this);
			var new_htm = $active_elem.html().replace(/<\/?[^>]+>/gi, '');
			var new_val = $active_elem.val().replace(/<\/?[^>]+>/gi, '');
			stop_all_speakers_pro($('.sound_container_pro'));
			clearAllPlayers();
			
			var htm = $elem.children('.sound_text_pro').html();
			if(htm == 'gspeech_html' || $elem.children('.sound_text_pro').attr("htm") == 'gspeech_html') {
				$elem.children('.sound_text_pro').attr("htm","gspeech_html");
				$elem.children('.sound_text_pro').html(new_htm);
			}
			else if(htm == 'gspeech_value' || $elem.children('.sound_text_pro').attr("htm") == 'gspeech_value') {
					$elem.children('.sound_text_pro').attr("htm","gspeech_value");
					$elem.children('.sound_text_pro').html(new_val);
			}
			setTimeout(function() {
				make_speeching($elem);
			},speechtimeout);
		});
	};
	
	var speech_enable_count = 9999;
	$('.sound_container_pro').each(function(i) {
		$this = $(this);
		if($(this).hasClass("greeting_block"))
			++speech_enable_count;
		if(i < speech_enable_count) {
			var h1 = $(this).parent('span').height();
			var h2 = $(this).height();
			var w = $(this).width();
			var delta_w = w - 8;
			var delta = (h1 - h2)/2;
			$(this).parent('span').css({top: delta + 'px'});
			var delta_htm = '<label style="width: ' + delta_w + 'px;display: inline-block">&nbsp;</label>';
			$(this).parent('span').after(delta_htm);
			
		} else {
			$(this).remove();
		}
	});
	
	$('.sound_container_pro').hover(function() {
		var $selection = $(this).parent('span').prev('.gspeech_selection');
		var roll = $selection.attr("roll");
		roll = roll == '' ? 1 : roll;
		--roll;
		
		var an_time = parseFloat(gspeech_animation_time[roll]);
		var speaker_op = parseFloat(gspeech_spoa[roll] / 100);
		
		$(this).stop().animate({
			opacity: speaker_op
		},an_time);
		
		$selection.stop().animate({
			backgroundColor: gspeech_bca[roll],
			color: gspeech_ca[roll]
		},an_time);
		
		navigate_tooltip($(this).prev('.sexy_tooltip'));
		
	},function() {
		var $selection = $(this).parent('span').prev('.gspeech_selection');
		var roll = $selection.attr("roll");
		roll = roll == '' ? 1 : roll;
		--roll;
		
		
		
		//onmouseleave, fade back
		if(! $(this).hasClass('active')) {
			var an_time = parseFloat(gspeech_animation_time[roll]);
			var speaker_op = parseFloat(gspeech_spop[roll] / 100);
			$(this).stop().animate({
				opacity: speaker_op
			},an_time);
		};
		
		if(! $selection.hasClass('active')) {
			$selection.stop().animate({
				backgroundColor: gspeech_bcp[roll],
				color: gspeech_cp[roll]
			},an_time);
		};
		
		//hide_tooltip
		hide_tooltip($(this).prev('.sexy_tooltip'));
	});
	
	$('.sound_container_pro').mousedown(function() {
		pro_container_clicked = true;
		basic_plg_enable = false;
	});
	
	$("body").mousedown(function() {
		if($('.sound_container_pro.active').length > 0 && !pro_container_clicked)
			basic_plg_enable = true;
			
		stop_all_speakers_pro($('.sound_container_pro'));
		clearAllPlayers();
		reset_opacities();
		$('.sound_container_pro').removeClass('active');
		$('.gspeech_selection').removeClass('active');
	});
	
	$('.sound_container_pro').click(function() {
		make_speeching($(this));
	});
	
	function reset_opacities() {
		$('.sound_container_pro').each(function() {
			var $selection = $(this).parent('span').prev('.gspeech_selection');
			var roll = $selection.attr("roll");
			roll = roll == '' ? 1 : roll;
			--roll;
			var an_time = parseFloat(gspeech_animation_time[roll]);
			var speaker_op = parseFloat(gspeech_spop[roll] / 100);
			$(this).animate({opacity: speaker_op},an_time)
		})
	}
	
	function make_speeching($elem) {
		
		var sel_txt = $elem.children('.sound_text_pro').html();
		if(speechtxt != sel_txt) {
			var $selection = $elem.parent('span').prev('.gspeech_selection');
			$selection.stop().removeAttr("style").addClass('active');
			$elem.addClass('active');
			$elem.addClass('here');
			
			var lang = $elem.attr("language");
			lang = lang == '' ? lang_identifier : lang;
			
			blinking_enable_pro = true;
			blink_start_enable_pro = true;
			
			//animate to active opacity
			var roll = $elem.attr("roll");
			roll = roll == '' ? 1 : roll;
			--roll;
			var speaker_op = parseFloat(gspeech_spoa[roll] / 100);
			$elem.stop().fadeTo(400,speaker_op);
			
			//rotate me
			rotate_speaker_pro($elem);
			
			clearAllPlayers();
			make_audio_pro($elem.children('.sound_text_pro'),$elem,lang);
		}
		speechtxt = sel_txt;
		setTimeout(function() {
			speechtxt = '';
		},100);
	};
	
	function stop_speaker_pro($elem) {
		basic_plg_enable = true;
		clearAllPlayers();
		$elem.rotate({animateTo:360});
		
		var roll = $elem.attr("roll");
		roll = roll == '' ? 1 : roll;
		--roll;
		var speaker_op = parseFloat(gspeech_spop[roll] / 100);
		$elem.stop().fadeTo(400,speaker_op).removeClass('active');
		
		for(f in blink_timer) {
			clearTimeout(blink_timer[f]);
		}
		for(f in rotate_timer) {
			clearInterval(rotate_timer[f]);
		}
	};
	
	function stop_all_speakers_pro($elem) {
		clearAllPlayers();
		$('.sound_container_pro.active').rotate({animateTo:360});
		
		var roll = $elem.attr("roll");
		roll = roll == '' ? 1 : roll;
		--roll;
		
		var speaker_op = parseFloat(gspeech_spop[roll] / 100);
		$elem.stop().removeClass('active');
		
		for(f in blink_timer) {
			clearTimeout(blink_timer[f]);
		}
		for(f in rotate_timer) {
    		clearInterval(rotate_timer[f]);
		}
	};

	function rotate_speaker_pro($elem) {
		var angle = 0;
		rotate_timer_element = setInterval(function(){
		      angle+=3;
		      $elem.rotate(angle);
		},15);
		rotate_timer.push(rotate_timer_element);
	};
	
	function blink_speaker_pro($elem) {
		if(blink_start_enable_pro) {
			$elem.fadeTo(200,0.2);
			blink_start_enable_pro = false;
		}
		else {
			$elem.fadeTo(200,1);
			blink_start_enable_pro = true;
		}
		var t = setTimeout(function() {
			blink_speaker_pro($elem);
		},800);
		blink_timer.push(t);
	};
	
	function change_speaker_animation_pro($elem) {
		if(blinking_enable_pro) {
			for(f in rotate_timer) {
	    		clearInterval(rotate_timer[f]);
			}
			$elem.rotate({animateTo:0});
        	blink_speaker_pro($elem);
		}
		blinking_enable_pro = false;
	};

	//main function which creates audio
	function make_audio_pro($txt_element,$elem,lang) {

		// 2.7.0
		var hidespeaker = $elem.attr("hidespeaker");

		selected_txt = $txt_element.html();
		var 
			words_array = new Array(),
			sent_array = new Array(),
			sent_index = 0;
		
		words_array = selected_txt.split(/[^\S]+/);

		for(var i = 0; i < words_array.length; i++) {
			if(sent_array[sent_index] == undefined) {
				sent_array[sent_index] = '';
			}

			var total_l = sent_array[sent_index].length + words_array[i].length;
			if(sent_array[sent_index].length < speech_text_length && total_l < speech_text_length) {
    				sent_array[sent_index] += words_array[i] + ' ';
			}
			else {
				++sent_index;
				sent_array[sent_index] = words_array[i] + ' ';
			}
		};

		var players_count = sent_array.length;

		var htm_cont = '';
		for(var i = 0; i < players_count; i++) {
			htm_cont += '<audio id="player' + i + '" src="' + streamerphp_folder + 'gspeech.mpeg" type="' + translation_audio_type + '" controls="controls"></audio>';
		};
		$("#sound_audio").html(htm_cont);

		for(var i = 0; i < players_count; i++) {
			create_htm(i,players_count,lang);
		};

				// new version addings

		function gs_get_token(encoded_text) {
			var query = encoded_text;
			var cM = function(a) {
			    return function() {
			        return a
			    }
			};
			var of = "=";
			var dM = function(a, b) {
			    for (var c = 0; c < b.length - 2; c += 3) {
			        var d = b.charAt(c + 2),
			            d = d >= t ? d.charCodeAt(0) - 87 : Number(d),
			            d = b.charAt(c + 1) == Tb ? a >>> d : a << d;
			        a = b.charAt(c) == Tb ? a + d & 4294967295 : a ^ d
			    }
			    return a
			};

			var eM = null;
			var cb = 0;
			var k = "";
			var Vb = "+-a^+6";
			var Ub = "+-3^+b+-f";
			var t = "a";
			var Tb = "+";
			var dd = ".";
			var hoursBetween = Math.floor(Date.now() / 3600000);
			window.TKK = hoursBetween.toString();

			fM = function(a) {
			    var b;
			    if (null === eM) {
			        var c = cM(String.fromCharCode(84)); // char 84 is T
			        b = cM(String.fromCharCode(75)); // char 75 is K
			        c = [c(), c()];
			        c[1] = b();
			        // So basically we're getting window.TKK
			        eM = Number(window[c.join(b())]) || 0
			    }
			    b = eM;

			    // This piece of code is used to convert d into the utf-8 encoding of a
			    var d = cM(String.fromCharCode(116)),
			        c = cM(String.fromCharCode(107)),
			        d = [d(), d()];
			    d[1] = c();
			    for (var c = cb + d.join(k) +
			            of, d = [], e = 0, f = 0; f < a.length; f++) {
			        var g = a.charCodeAt(f);

			        128 > g ? d[e++] = g : (2048 > g ? d[e++] = g >> 6 | 192 : (55296 == (g & 64512) && f + 1 < a.length && 56320 == (a.charCodeAt(f + 1) & 64512) ? (g = 65536 + ((g & 1023) << 10) + (a.charCodeAt(++f) & 1023), d[e++] = g >> 18 | 240, d[e++] = g >> 12 & 63 | 128) : d[e++] = g >> 12 | 224, d[e++] = g >> 6 & 63 | 128), d[e++] = g & 63 | 128)
			    }


			    a = b || 0;
			    for (e = 0; e < d.length; e++) a += d[e], a = dM(a, Vb);
			    a = dM(a, Ub);
			    0 > a && (a = (a & 2147483647) + 2147483648);
			    a %= 1E6;
			    return a.toString() + dd + (a ^ b)
			};

			var token = fM(query);
			
			return token;
		}

		function gs_replace_ch(str) {
			var str = str.replace("'", "");
			str = str.replace('"', '');
			str = str.replace('', '');
			str = str.replace('/', '');
			str = str.replace('&nbsp;', '');
			// str = str.replace('&', '');

			return str;
		}

		function create_htm(i,players_count,lang) {
			$('#player' + i).mediaelementplayer({
				success: function (mediaElement, domObject) {
					//detect media end
					players[i] = mediaElement;


		            // sets src
					var encoded_text = gs_replace_ch(sent_array[i]);
					var token = gs_get_token(encoded_text);
					encoded_text = encodeURIComponent(encoded_text);
					var embed_url = streamerphp_folder + 'streamer.php?q=' + encoded_text + '&l=' + lang + '&tr_tool=' +translation_tool + '&token=' + token;
					mediaElement.setSrc(embed_url);

		            //play next audio, when current ends
		            mediaElement.addEventListener('pause', function(e) {
    	        		try {
    	        			players[i + 1].play()
    	        		} catch(e){}
		        	}, false);

		        	players[0].addEventListener('progress', function(e) {
    		            	change_speaker_animation_pro($elem);
		        	}, false);

		        	if(i == players_count - 1) {
    		        	players[players_count - 1].addEventListener('pause', function(e) {
    		        		if(hidespeaker == 0) { // fixed bug when events being overwritten
    		        			stop_speaker_pro($elem);
    		        		}
    		        	}, false);
		        	}
		        	
		            mediaElement.load();
		            
		            if(i == 0) {
		            	mediaElement.play();
		            }
		        }
			});
		}
		
	};
	
	function clearAllPlayers() {
		for(var c in players) {
			players[c] = '';
		}
		$('#sound_audio').html('');
	};
	
				
});
})(sexyJ);