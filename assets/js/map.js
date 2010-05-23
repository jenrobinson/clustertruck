YUI.add("clustertruck-map",function(Y) {

	// shortcuts
	var $ = Y.get, $j = Y.JSON;

	// base 
	CT.Map = function(args) {
		this.init(args);
	}

	// base prototype
	CT.Map.prototype = {
		
		// args
		args : {},
		
		// init 
		init : function(args) {
			
			// args
			this.args = args; 
			
			// map
			this.map = false;
			
			// zindex
			this.zindex = 1000;
			
			//get config
			if (window.mapConfig) { 
				this.config = window.mapConfig;
			} else { 
				this.config = false;
			}
						
			//get points
			if (window.points) { 
				this.points = window.points;
			}
	        
	        if (typeof google != 'undefined' && this.points) {    
	        
	        	this.infoWindow = new google.maps.InfoWindow();
				this.markerBounds = new google.maps.LatLngBounds();
				this.markerArray = [];                                 
	        
	        	//setup geocoder
				this.geocoder = new google.maps.Geocoder();
	        	
	        	//load the map
	        	this.loadMap();  
	        
	        	if (Y.one('.filter')) { 
	        		Y.one('.filter').on('change',this.changeFilter,this);
	        	}
	        
	        }    
	        	                                    
		
		},
		
		// click
		click : function(e) {
		
			// tar
			var tar = e.target;
			
			// fire
			this.fire('map:click',{'target':tar,'event':e});
			
			// close
			if ( tar.hasClass('close-map') ) {
				this.close();
			}
			
		},
		
		
		loadMap: function() {
			
			if (this.config) { 
				var configCenter = new google.maps.LatLng(this.config.lat,this.config.lon);
				var configZoom = this.config.zoom;
			} else { 
				var configCenter = new google.maps.LatLng();
				var configZoom = 10;
			}
			
		    var mapOptions = {
		      zoom: configZoom,
		      mapTypeId: google.maps.MapTypeId.ROADMAP,
		      mapTypeControl: false,
		      center: configCenter
		    };
		    
		    var map = this.map = new google.maps.Map(document.getElementById("map"), mapOptions);
		    
		    this.drawPoints();
		
		},
		
		
		drawPoints: function() { 
			
			if (this.points.length > 0) { 
				
				//bind events to all locations on right
				Y.all("div.locations ul li").each(function(loc) { 
				
					loc.on('click',this.highlightLocation,this);
				
				},this);
				
				var d = new Date();
		        var now = Math.round((d.getTime() / 1000));
				
				//find closest start time
				var closestOne = this.findClosestStartTime(this.points,now);
				
				//loop through points passed by php and map them
				for ( var p = 0; p < this.points.length; p++ ) { 
					
					var myLatlng = new google.maps.LatLng(this.points[p].lat,this.points[p].lon);

					var truckInfo = $j.parse(this.points[p].truck_info);
					
					//check for more than one point
					if (this.points.length > 1) { 	 	
						var zoom = 11;
					} else { 
						var zoom = 15;
					}
					
					if (p === closestOne) { 			
						
						if (typeof this.config.lat == 'undefined') { 
							this.map.setCenter(myLatlng);
							this.map.setZoom(zoom);
						}
					}
					   			
	    			var locationContent = '<div class="location-info"><h4><strong>'+this.points[p].truck_name+'</strong> at ' + this.points[p].name+'</h4><h3>'+this.points[p].datetime+'</h3><h5>'+this.points[p].address+'<br /><strong>'+this.points[p].notes+'</strong> <a href="'+CT.Env.Urls.base+truckInfo.slug+'">View Profile &#187;</a></h5></div>';
	    			
	    			var truck = $j.parse(this.points[p].truck_info); 
	    			
	    			if (!this.config.zoom) { 
	    				var openDefault = true;
	    			} else { 
	    				var openDefault = false;
	    			}
	    				    			  			
	    			var pushPin = this.makeMarker({
					  position: myLatlng,
					  title: this.points[p].truck_name,
					  content: locationContent,
					  icon: new google.maps.MarkerImage(truck.twitter_image,
					      new google.maps.Size(48,48),
					      new google.maps.Point(0,0),
					      new google.maps.Point(0,14)
					      ),
					  shadow: new google.maps.MarkerImage('http://www.clustertruck.org/assets/images/shadow.png',
					      new google.maps.Size(75,75),
					      new google.maps.Point(0,0),
					      new google.maps.Point(5,32)
					      ),
					  sortCount:p,
					  totalCount:this.points.length,
					  markerId:'s'+this.points[p].id,
					  zIndex:this.zindex,
					  closestOne:closestOne,
					  openDefault:openDefault
					}); 
											    				    				
	    			
	    		}
			
			}
		
		},
		
		
		/**
		 * creates Marker and InfoWindow on a Map() named 'map'
		 * saves marker to markerArray and markerBounds
		 * @param options object for Marker and InfoWindow
		 * @author Esa 2009
		 */
		
		 
		makeMarker : function(options){
		  
		  var zindex = this.zindex;
		  
		  var pushPin = new google.maps.Marker({map:this.map});
		  pushPin.setOptions(options);
		  var infoWindow = this.infoWindow;
		  
		  	  if (options.sortCount === options.closestOne && options.totalCount > 1 && options.openDefault == true) { 
		  	  	this.clearHighlights();
		  	  	Y.one('#'+options.markerId).addClass('selected');
		  	  	infoWindow.setOptions(options); 
		  	  	infoWindow.open(this.map, pushPin);
		  	  	var d = new Date();
		        zindex = Math.round((d.getTime() / 1000)); 
		        pushPin.setZIndex(zindex); 
		  	  }
		  
		  google.maps.event.addListener(pushPin, "click", function(){
		    
		    infoWindow.setOptions(options);
		    infoWindow.open(this.map, pushPin);
		    this.map.setCenter(options.position);
		    
		    var d = new Date();
		    
		    zindex = Math.round((d.getTime() / 1000)); 
		    
		    pushPin.setZIndex(zindex);
		    
		  });
		  
		  this.markerBounds.extend(options.position);
		  this.markerArray[options.markerId.replace(/s/g,'')] = pushPin;
		  		  
		  return pushPin;
		},
		
		
		highlightLocation : function(e) { 
		
			// get location li
			var tar = CT.Obj.getParent(e.target,{'tag':'li'});
			
			this.clearHighlights();
			
			tar.addClass('selected');
			
			var id = tar.get('id').replace(/s/g,'');
			
			google.maps.event.trigger( this.markerArray[id], "click" );
		
		
		},
		
		
		clearHighlights: function() { 
		
			//bind events to all locations on right
			Y.all("div.locations ul li").each(function(loc) { 
			
				loc.removeClass('selected');
			
			},this);
		
		},
		
		
		changeFilter : function(e) { 
		
			// get location li
			var tar = CT.Obj.getParent(e.target,{'tag':'form'});
			
			tar.submit();
		
		
		},
		
		
		findClosestStartTime : function(haystack,needle) {
					
			var closest = haystack[0].time_start;
			var closestCount = 0;
			
			for ( var i = 0; i < haystack.length; i++ ) {
				
				//console.log('comparing ' + Math.abs( parseInt(haystack[ i ].time_start) - parseInt(needle) ) + ' with ' + Math.abs( parseInt(closest) - parseInt(needle) ));
			
			  if ( Math.abs( parseInt(haystack[ i ].time_start) - parseInt(needle) ) < Math.abs( parseInt(closest) - parseInt(needle) ) ) { 
			  	closestCount = i;
			  	closest = haystack[i].time_start;
			  }
			}
			
			return closestCount;
		
		
		}
		
			
	} 
	
	// we fire some custom events
	Y.augment(CT.Map, Y.EventTarget);

});