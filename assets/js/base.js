var _yui_load_conf = {
	'combine': true,
	'filter': 'min',
	'force': false,
	'modules': {
		"clustertruck-map": {
			"requires": ['base','node','event','io','json','anim','selector-css3',"overlay","event-custom"],
			"fullpath": CT.Env.Urls.base + 'assets/js/map.js?v=' + CT.Env.AssetVersion
		},
		"clustertruck-panel": {
			"requires": ['base','node','event','io','json','anim','selector-css3',"overlay","event-custom"],
			"fullpath": CT.Env.Urls.base + 'assets/js/panel.js?v=' + CT.Env.AssetVersion
		}
	}
};

// YUI
YUI(_yui_load_conf).use('base','node','anim','event','io','json','selector-css3','clustertruck-map','clustertruck-panel',"get", function(Y) { 

	// called to load
	Y.on('domready',function(){ CT.Obj = new CT.Base(); CT.execute('l'); },window);

	// shortcuts
	var $ = Y.get, $j = Y.JSON;

	// base 
	CT.Base = function() {
		this.init();
	}

	// base prototype
	CT.Base.prototype = {
		
		// args
		store : { lockFlyout: false },
		
		// init 
		init : function() {
		
			// attach some stuff
			$('#doc').on('click',this.click,this);
			$('#doc').on('mouseover',this.mouse,this);			
			$('#doc').on('mouseout',this.mouse,this);
			$('#doc').on('keyup',this.keyup,this);
			
			// beed to check form tags to see if 
			// they should open in a panel
			$('#doc').all('form.open-panel').each(function(el){
				
				// get attr
				var action = el.getAttribute('action');
				
				// reset it 
				el.setAttribute('x-action', self.getUrl(action,{'.context':'xhr'}));
				el.setAttribute('action','#');
				el.setAttribute('method','get');													
											
				// attach to submit
				el.on('submit',function(e){	
				
					// halt what the browser wants to do
					e.halt(); 
							
					// get target
					var tar = e.target;
					
					// has class
					if ( tar.hasClass('loading') ) {
						return;
					}
					
					// loading
					tar.addClass('loading');
					
					// get the action
					var url = tar.getAttribute('x-action');
					
					// get the form
					CT.Obj.panel.load(url,{'form':tar,'openAfter':true});
		
					// remove
					tar.removeClass('loading');
					
					
				},this);				
			
			});
						
			// self
			var self = this;
			
			this.map = new CT.Map({}); 
			
			// generl panel
			this.panel = new CT.Panel({});
			
		},
		
		// load css
		loadCss : function(url) {
			Y.Get.css(url);
		},
		
		loadJs : function(url,args) {
			Y.Get.script(url,args);
		},
	
		// click
		click : function(e) {
		
			// target
			var tar = oTar = e.target;
			
			 // ! open a panel
			if ( tar.hasClass('open-panel') && tar.get('tagName') == 'A' ) {
			
				// stop
				e.halt();
			
				// open it in a panel
				this.panel.load( tar.get('href') ,{'openAfter':true});
			
			}
			
			// no tar
			if ( !tar ) { return; }			
		
		},
		
		// keydown
		keyup : function(e) {
		
			// target
			var tar = oTar = e.target;
			
			 // ! open a panel
			if ( tar.hasClass('edit-slug') ) {
			
				var slug = tar.get('value').replace(/ /g,'-').replace(/'/g,'');
					
				Y.one("#slug-container").set('innerHTML',slug);
				
				// validate slug
				this.validateSlug(slug);	
			
			}
			
			// no tar
			if ( !tar ) { return; }			
		
		},
		
		// mouse
		mouse : function(e,type) {
			
			// target
			var tar = oTar = e.target;
			
			// custom
			this.fire('CT-base:mouse',e);
		
		},
		
		getUrl : function(url,params) {
        
			// qp
			var qp = [];
			
				// add 
				for ( var p in params ) {
					qp.push(p+"="+ encodeURIComponent(params[p]) );
				}
        
        	// do it 
        	return url + (url.indexOf('?')==-1?'?':'&') + qp.join('&');
        
        },
		
		
		getXhrUrl : function(act,params) {
		
			// reurn
			return this.getUrl( CT.Env.Urls.xhr+act, params);
			
		},
		
		getParent : function(tar,g,max) {
       	       	
			// no tar
			if ( !tar )	{ return false; }
       	       	
       		// max
       		if ( !max ) { max = 10; }
        
            // local
            var gt = g;
           	var i = 0;            
           	var m = max;
            
            if ( typeof g == 'object' ) {
            
            	// current
            	if ( tar.get('tagName') == gt.tag.toUpperCase() ) { return tar; }
            
            	// reutrn
                return tar.ancestor(function(el){
                	if ( i++ > max ) { return false; }
					return (el.get('tagName') == gt.tag.toUpperCase()); }
				);
				
            }
            else {
            
            	// current
            	if ( tar.hasClass(gt) ) { return tar; }            
            
            	// moreve
                return tar.ancestor(function(el){ 
                	if ( i++ > max ) { return false; }                
                	return el.hasClass(gt); 
                });
                
            }
        },
        
        
        validateSlug : function(slug) { 
        	
        	// url
			var url = CT.Obj.getUrl(CT.Env.Urls.base+'trucks/validate-slug',{'.context':'xhr'});
        	
        	// params
			var params = {
				'method': 'GET',
				'context': this,
				'data': 'slug='+slug,
				'timeout': 10000,
				'on': {
					'failure': function() {
					//	window.location.href = reg_url;
					},
				 	'complete': function(id,o,a) {
						
						// get data
						var json = false;
						
						// try to parse
						//try {
							
							json = $j.parse(o.responseText);
								
							var slugContainer = Y.one("span#slug-result");	
																					
							if (json.validslug == 'good') {
								
								slugContainer.removeClass('bad');
								slugContainer.addClass('good');
								slugContainer.set('innerHTML','Available');
								
							
							} else { 
								
								slugContainer.removeClass('good');
								slugContainer.addClass('bad');
								slugContainer.set('innerHTML','Taken');
								
							}
							
						//}
						//catch (e) {}
						
						// need a good stat
						if ( !json || json.stat != 1 ) {
							return false;						
						}
						
						
					}
				}
			}        	
        	
        	// fire
			Y.io(url,params);
        
        }
        
			
	}

	// we fire some custom events
	Y.augment(CT.Base, Y.EventTarget);

});