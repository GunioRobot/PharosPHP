/**
 * WordPress plugin.
 */

(function() {
	var DOM = tinymce.DOM;

	tinymce.create('tinymce.plugins.WordPress', {
		mceTout : 0,

		init : function(ed, url) {
			var t = this, tbId = ed.getParam('wordpress_adv_toolbar', 'toolbar2'), last = 0, moreHTML, nextpageHTML;
			moreHTML = '<img src="' + url + '/img/trans.gif" class="mceWPmore mceItemNoResize" title="'+ed.getLang('wordpress.wp_more_alt')+'" />';
			nextpageHTML = '<img src="' + url + '/img/trans.gif" class="mceWPnextpage mceItemNoResize" title="'+ed.getLang('wordpress.wp_page_alt')+'" />';

			/*
			if ( getUserSetting('hidetb', '0') == '1' )
				ed.settings.wordpress_adv_hidden = 0;
			*/

			// Hides the specified toolbar and resizes the iframe
			ed.onPostRender.add(function() {
				var adv_toolbar = ed.controlManager.get(tbId);
				if ( ed.getParam('wordpress_adv_hidden', 1) && adv_toolbar ) {
					DOM.hide(adv_toolbar.id);
					t._resizeIframe(ed, tbId, 28);
				}
			});

		

			// Add Media buttons
			ed.addButton('add_media', {
				title : 'wordpress.add_media',
				image : url + '/img/media.gif',
				onclick : function() {
					tb_show('', tinymce.DOM.get('add_media').href);
					tinymce.DOM.setStyle( ['TB_overlay','TB_window','TB_load'], 'z-index', '999999' );
				}
			});

			ed.addButton('add_image', {
				title : 'wordpress.add_image',
				image : url + '/img/image.gif',
				onclick : function() {
					tb_show('', tinymce.DOM.get('add_image').href);
					tinymce.DOM.setStyle( ['TB_overlay','TB_window','TB_load'], 'z-index', '999999' );
				}
			});

			ed.addButton('add_video', {
				title : 'wordpress.add_video',
				image : url + '/img/video.gif',
				onclick : function() {
					tb_show('', tinymce.DOM.get('add_video').href);
					tinymce.DOM.setStyle( ['TB_overlay','TB_window','TB_load'], 'z-index', '999999' );
				}
			});

			ed.addButton('add_audio', {
				title : 'wordpress.add_audio',
				image : url + '/img/audio.gif',
				onclick : function() {
					tb_show('', tinymce.DOM.get('add_audio').href);
					tinymce.DOM.setStyle( ['TB_overlay','TB_window','TB_load'], 'z-index', '999999' );
				}
			});

			// Add Media buttons to fullscreen
			ed.onBeforeExecCommand.add(function(ed, cmd, ui, val) {
				var DOM = tinymce.DOM;
				if ( 'mceFullScreen' != cmd ) return;
				if ( 'mce_fullscreen' != ed.id && DOM.get('add_audio') && DOM.get('add_video') && DOM.get('add_image') && DOM.get('add_media') )
					ed.settings.theme_advanced_buttons1 += ',|,add_image,add_video,add_audio,add_media';
			});

			// Add class "alignleft", "alignright" and "aligncenter" when selecting align for images.
			ed.addCommand('JustifyLeft', function() {
				var n = ed.selection.getNode();

				if ( n.nodeName != 'IMG' )
					ed.editorCommands.mceJustify('JustifyLeft', 'left');
				else ed.plugins.wordpress.do_align(n, 'alignleft');
			});

			ed.addCommand('JustifyRight', function() {
				var n = ed.selection.getNode();

				if ( n.nodeName != 'IMG' )
					ed.editorCommands.mceJustify('JustifyRight', 'right');
				else ed.plugins.wordpress.do_align(n, 'alignright');
			});

			ed.addCommand('JustifyCenter', function() {
				var n = ed.selection.getNode(), P = ed.dom.getParent(n, 'p'), DL = ed.dom.getParent(n, 'dl');

				if ( n.nodeName == 'IMG' && ( P || DL ) )
					ed.plugins.wordpress.do_align(n, 'aligncenter');
				else ed.editorCommands.mceJustify('JustifyCenter', 'center');
			});

			// Word count if script is loaded
			if ( 'undefined' != typeof wpWordCount ) {
				ed.onKeyUp.add(function(ed, e) {
					if ( e.keyCode == last ) return;
					if ( 13 == e.keyCode || 8 == last || 46 == last ) wpWordCount.wc( ed.getContent({format : 'raw'}) );
					last = e.keyCode;
				});
			};

			ed.onSaveContent.add(function(ed, o) {
				if ( typeof(switchEditors) == 'object' ) {
					if ( ed.isHidden() )
						o.content = o.element.value;
					else
						o.content = switchEditors.pre_wpautop(o.content);
				}
			});

			/* disable for now
			ed.onBeforeSetContent.add(function(ed, o) {
				o.content = t._setEmbed(o.content);
			});

			ed.onPostProcess.add(function(ed, o) {
				if ( o.get )
					o.content = t._getEmbed(o.content);
			});
			*/

			// Add listeners to handle more break
			t._handleMoreBreak(ed, url);

			// Add custom shortcuts
			ed.addShortcut('alt+shift+a', ed.getLang('link_desc'), 'mceLink');
			ed.addShortcut('alt+shift+s', ed.getLang('unlink_desc'), 'unlink');
			ed.addShortcut('alt+shift+g', ed.getLang('fullscreen.desc'), 'mceFullScreen');
			ed.addShortcut('ctrl+s', ed.getLang('save_desc'), function(){if('function'==typeof autosave)autosave();});

			if ( tinymce.isWebKit ) {
				ed.addShortcut('alt+shift+b', ed.getLang('bold_desc'), 'Bold');
				ed.addShortcut('alt+shift+i', ed.getLang('italic_desc'), 'Italic');
			}

			ed.onInit.add(function(ed) {
				tinymce.dom.Event.add(ed.getWin(), 'scroll', function(e) {
					ed.plugins.wordpress._hideButtons();
				});
				tinymce.dom.Event.add(ed.getBody(), 'dragstart', function(e) {
					ed.plugins.wordpress._hideButtons();
				});
			});

			ed.onBeforeExecCommand.add(function(ed, cmd, ui, val) {
				ed.plugins.wordpress._hideButtons();
			});

			ed.onSaveContent.add(function(ed, o) {
				ed.plugins.wordpress._hideButtons();
			});

			ed.onMouseDown.add(function(ed, e) {
				if ( e.target.nodeName != 'IMG' )
					ed.plugins.wordpress._hideButtons();
			});
		},

		getInfo : function() {
			return {
				longname : 'WordPress Plugin',
				author : 'WordPress', // add Moxiecode?
				authorurl : 'http://wordpress.org',
				infourl : 'http://wordpress.org',
				version : '3.0'
			};
		},

		// Internal functions
		_setEmbed : function(c) {
			return c.replace(/\[embed\]([\s\S]+?)\[\/embed\][\s\u00a0]*/g, function(a,b){
				return '<img width="300" height="200" src="' + tinymce.baseURL + '/plugins/wordpress/img/trans.gif" class="wp-oembed mceItemNoResize" alt="'+b+'" title="'+b+'" />';
			});
		},

		_getEmbed : function(c) {
			return c.replace(/<img[^>]+>/g, function(a) {
				if ( a.indexOf('class="wp-oembed') != -1 ) {
					var u = a.match(/alt="([^\"]+)"/);
					if ( u[1] )
						a = '[embed]' + u[1] + '[/embed]';
				}
				return a;
			});
		},

		_showButtons : function(n, id) {
			var ed = tinyMCE.activeEditor, p1, p2, vp, DOM = tinymce.DOM, X, Y;

			vp = ed.dom.getViewPort(ed.getWin());
			p1 = DOM.getPos(ed.getContentAreaContainer());
			p2 = ed.dom.getPos(n);

			X = Math.max(p2.x - vp.x, 0) + p1.x;
			Y = Math.max(p2.y - vp.y, 0) + p1.y;

			DOM.setStyles(id, {
				'top' : Y+5+'px',
				'left' : X+5+'px',
				'display' : 'block'
			});

			if ( this.mceTout )
				clearTimeout(this.mceTout);

			this.mceTout = setTimeout( function(){ed.plugins.wordpress._hideButtons();}, 5000 );
		},

		_hideButtons : function() {
			if ( !this.mceTout )
				return;

			if ( document.getElementById('wp_editbtns') )
				tinymce.DOM.hide('wp_editbtns');

			if ( document.getElementById('wp_gallerybtns') )
				tinymce.DOM.hide('wp_gallerybtns');

			clearTimeout(this.mceTout);
			this.mceTout = 0;
		},

		do_align : function(n, a) {
			var P, DL, DIV, cls, c, ed = tinyMCE.activeEditor;

			if ( /^(mceItemFlash|mceItemShockWave|mceItemWindowsMedia|mceItemQuickTime|mceItemRealMedia)$/.test(n.className) )
				return;

			P = ed.dom.getParent(n, 'p');
			DL = ed.dom.getParent(n, 'dl');
			DIV = ed.dom.getParent(n, 'div');

			if ( DL && DIV ) {
				cls = ed.dom.hasClass(DL, a) ? 'alignnone' : a;
				DL.className = DL.className.replace(/align[^ '"]+\s?/g, '');
				ed.dom.addClass(DL, cls);
				c = (cls == 'aligncenter') ? ed.dom.addClass(DIV, 'mceIEcenter') : ed.dom.removeClass(DIV, 'mceIEcenter');
			} else if ( P ) {
				cls = ed.dom.hasClass(n, a) ? 'alignnone' : a;
				n.className = n.className.replace(/align[^ '"]+\s?/g, '');
				ed.dom.addClass(n, cls);
				if ( cls == 'aligncenter' )
					ed.dom.setStyle(P, 'textAlign', 'center');
				else if (P.style && P.style.textAlign == 'center')
					ed.dom.setStyle(P, 'textAlign', '');
			}

			ed.execCommand('mceRepaint');
		},

		// Resizes the iframe by a relative height value
		_resizeIframe : function(ed, tb_id, dy) {
			var ifr = ed.getContentAreaContainer().firstChild;

			DOM.setStyle(ifr, 'height', ifr.clientHeight + dy); // Resize iframe
			ed.theme.deltaHeight += dy; // For resize cookie
		},

		_handleMoreBreak : function(ed, url) {
			var moreHTML, nextpageHTML;

			moreHTML = '<img src="' + url + '/img/trans.gif" alt="$1" class="mceWPmore mceItemNoResize" title="'+ed.getLang('wordpress.wp_more_alt')+'" />';
			nextpageHTML = '<img src="' + url + '/img/trans.gif" class="mceWPnextpage mceItemNoResize" title="'+ed.getLang('wordpress.wp_page_alt')+'" />';

			// Load plugin specific CSS into editor
			ed.onInit.add(function() {
				ed.dom.loadCSS(url + '/css/content.css');
			});

			// Display morebreak instead if img in element path
			ed.onPostRender.add(function() {
				if (ed.theme.onResolveName) {
					ed.theme.onResolveName.add(function(th, o) {
						if (o.node.nodeName == 'IMG') {
							if ( ed.dom.hasClass(o.node, 'mceWPmore') )
								o.name = 'wpmore';
							if ( ed.dom.hasClass(o.node, 'mceWPnextpage') )
								o.name = 'wppage';
						}

					});
				}
			});

			// Replace morebreak with images
			ed.onBeforeSetContent.add(function(ed, o) {
				o.content = o.content.replace(/<!--more(.*?)-->/g, moreHTML);
				o.content = o.content.replace(/<!--nextpage-->/g, nextpageHTML);
			});

			// Replace images with morebreak
			ed.onPostProcess.add(function(ed, o) {
				if (o.get)
					o.content = o.content.replace(/<img[^>]+>/g, function(im) {
						if (im.indexOf('class="mceWPmore') !== -1) {
							var m, moretext = (m = im.match(/alt="(.*?)"/)) ? m[1] : '';
							im = '<!--more'+moretext+'-->';
						}
						if (im.indexOf('class="mceWPnextpage') !== -1)
							im = '<!--nextpage-->';

						return im;
					});
			});

			// Set active buttons if user selected pagebreak or more break
			ed.onNodeChange.add(function(ed, cm, n) {
				cm.setActive('wp_page', n.nodeName === 'IMG' && ed.dom.hasClass(n, 'mceWPnextpage'));
				cm.setActive('wp_more', n.nodeName === 'IMG' && ed.dom.hasClass(n, 'mceWPmore'));
			});
		}
	});

	// Register plugin
	tinymce.PluginManager.add('wordpress', tinymce.plugins.WordPress);
})();
