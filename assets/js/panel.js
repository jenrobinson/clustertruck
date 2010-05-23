YUI.add("clustertruck-panel",function(Y) {

	// shortcuts
	var $ = Y.get, $j = Y.JSON;

	// base 
	CT.Panel = function(args) {
		this.init(args);
	}

	// base prototype
	CT.Panel.prototype = {
		
		// args
		args : {},
		
		// init 
		init : function(args) {
			
			// args
			this.args = args;
		
			// object
			this.obj = new Y.Overlay({
				'centered': true,
				'bodyContent': "hello"
			});
			
			// add our master class			
			if ( args && args.type != 'simple' ) {
			
				this.obj.get('boundingBox').append("<div class='back'></div><a class='close-panel'>close</a>");
				this.obj.get('boundingBox').addClass("panel");	
				
				// content
				this.obj.get('contentBox').append("<div class='loading_mask'></div><div class='loading_ind'></div>");
				
			}
			
			// add class
			if ( args && args['class'] ) {
				for ( var c in args['class'] ) {
					this.obj.get('boundingBox').addClass(args['class'][c]);
				}
			}
			
			// render
			this.obj.render("#doc");
			
			// hide
			this.obj.hide();
			
			// click
			this.obj.get('boundingBox').on('click',this.click,this);
			
			// events to publish
			this.publish('panel:click');
			this.publish('panel:open');
			this.publish('panel:close');			
			this.publish('panel:submit');			
			this.publish('panel:beforeload');			
			this.publish('panel:afterload');				
			
			// watch the xy change 
			this.obj.after('xyChange',function(e){
			
				// get the new xy
				var xy = e.newVal;
			
				// if the x is - reset to 10
				if ( xy[1] < 10 ) {		
					this.obj.move([xy[0],20]);
				}
			
			},this);		
		
			// scroll me
			Y.on('scroll',function(){
			
				// center
				this.obj.centered();
			
			},document,this);
		
		},
		
		// click
		click : function(e) { 
		
			// tar
			var tar = e.target;
			
			// fire
			this.fire('panel:click',{'target':tar,'event':e});
			
			// close
			if ( tar.hasClass('close-panel') ) {
				this.close();
			}
			
		},
		
		// open
		open : function() {
			
			// fire
			this.fire('panel:open');
		
			// open
			this.obj.show();
			
		},
		
		close : function(args) {
		
			// fire
			this.fire('panel:close',args);		

			// load
			CT.execute('u');
		
			// hide
			this.obj.hide();
			
		},
	
		// submit
		submit : function(e) { 
			
			// get target
			var tar = e.target;
			
			// has loading
			if ( tar.hasClass('loading') ) {
				return;
			}
			
			// loading
			tar.addClass('loading');
			
			Y.one('.error-msg').addClass('hidden');
			
			// get the action
			var url = tar.getAttribute('x-action');
						
			// fire
			this.fire('panel:submit');			

			// get the form
			this.load(url,{'form':tar}); 
			
		},
	
		// load
		load : function(url,args) {
		
			// loading
			this.obj.get('boundingBox').addClass('loading');
		
			// fire
			this.fire('panel:beforeload');		
			
			// url
			var url = CT.Obj.getUrl(url,{'.context':'xhr'});	
				
			// reg urk
			var reg_url = url.replace(/\.context=xhr/ig,'');
					
			// params
			var params = {
				'method': 'GET',
				'context': this,
				'arguments': args,
				'timeout': 10000,
				'on': {
					'failure': function() {
					//	window.location.href = reg_url;
					},
				 	'complete': function(id,o,a) {
						
						// get fata
						var json = false;
						
						// try to parse
						try {
							json = $j.parse(o.responseText);
						}
						catch (e) {}
						
						// need a good stat
						if ( !json || json.stat != 1 ) {
						//	window.location.href = reg_url; return;						
						}
						
						// not loading
						this.obj.get('boundingBox').removeClass('loading');
						
						// check for special actions
						if ( json['do'] ) {
							if ( json['do'] == 'redi' ) {
								this.close();
								window.location.href = json.url; return;
							}
							else if ( json['do'] == 'error' ) {
								
								Y.one('.error-msg').removeClass('hidden').set('innerHTML',json['msg']);
								
								//remove loading class to allow resubmit
								Y.one(args.form).removeClass('loading');
								
								return;
							
							}
							else if ( json['do'] == 'login' ) {
								CT.Obj.login(json.args); return;
							}
							else if ( json['do'] == 'load' ) {
													
								// load a page
								this.load(json.url+'&.context=xhr',{'openAfter':true}); return;
								
							}
							else if ( json['do'] == 'close' ) {
								this.close(json.args); return;
							}
							else if ( json['do'] == 'refresh' ) {
							
								// if tab
								if ( json.args && json.args.tab ) {
									
									this.close(json.args);
									
									// reload a tab
									CT.Obj.store.sheets[json.args.tab.id].load();
									CT.Obj.store.sheetStream.load({'tab':json.args.tab.id});
								
									// done
									return;
								
								}
							
								// window
								window.location.href = window.location.href;
								
							}
						}
						
						// set it 
						this.obj.set('bodyContent',json.html);
						
						//load calendar if we need it
						if (Y.one('#calendar-container')) { 
							
							YUI().use('base', function(Y) {
							    var cal1 = new YAHOO.widget.Calendar('cal1', 'calendar-container');
							    
							    /*cal1.renderEvent.subscribe(function() {
							        var dd = new Y.DD.Drag({
							            node: '#cal1Cont'
							        }).addHandle('div.calheader'); 
							    });*/
							    
							    cal1.selectEvent.subscribe(function(e, dates) { 
							        var d = dates[0][0];
							        var dateStr = d[1] + '/' + d[2] + '/' + d[0];
							        
							        if (document.getElementById('date-field')) { 
							        	document.getElementById('date-field').value = dateStr;
							        }
							        
							    });
							    cal1.render();
							});
							
							
						}
						
							// if bootstrap
							if ( json.bootstrap ) {
							
								// header content
								this.obj.set('headerContent',json.bootstrap.t);
								
								// boot me
								if ( json.bootstrap.js ) {
									for ( var el in json.bootstrap.js ) {
										eval(json.bootstrap.js[el]);
									}
								}
								
							}
						
						// look for forms in the head content
						this.obj.get('boundingBox').all('form').each(function(el){						
							
							//don't do it if it's a direct post
							if (!el.hasClass('direct')) {
								
								// get attr
								var action = el.getAttribute('action');
								
								// reset it 
								el.setAttribute('x-action', CT.Obj.getUrl(action,{'.context':'xhr'}));
								el.setAttribute('action','#');
								el.setAttribute('method','get');	
								
																				
															
									// attach to submit
									el.on('submit',function(e){	
										e.halt(); this.submit(e);								
									},this);
								
							}
							
						},this);
						
						this.obj.centered();
						
						// fire
						this.fire('panel:afterload');						
						
						// load
						CT.execute('l');
						
						// open
						if ( a && a.openAfter ) {
							this.open();
						}			 		
						
					}
				}			
			};
			
			// form
			if (args && args.form) {
				params['form'] = { 'id': args.form };
				params['method'] = 'POST';
			}
		
			// fire
			Y.io(url,params);
		
		}
	
	} 
	
	// we fire some custom events
	Y.augment(CT.Panel, Y.EventTarget);

});
