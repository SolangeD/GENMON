var map, layer, layer2, layer2b, layer3, layer4, select, control, meaninb=0.00, meanintr=0.00, sumind=0.00, sumind2=0.00, meanindex_socioec=0.00, image, year, plzsel;
		
function init(){
//proxy to allow to go to the port 8080. Find the proxy.cgi file in the genmon-ch folder
	OpenLayers.ProxyHost= "proxy.cgi?url="; 
//Definition of the global parameters of the map
	map = new OpenLayers.Map("map", {
		scales: [5000000, 3500000, 2000000, 1000000, 400000, 100000],//30000000, 10000000, 5000000],
		projection: new OpenLayers.Projection("EPSG:3857"),
		displayProjection: new OpenLayers.Projection("EPSG:3857"),
		units: "m",
		maxExtent: new OpenLayers.Bounds(
                664000,5751226,1170000,6075048//485410.0,75285.0,833838.0,295935.03125
                ),
                controls: [
			new OpenLayers.Control.PanZoom(),
			new OpenLayers.Control.Permalink(),
			new OpenLayers.Control.Navigation()
                ]
	});
		
//Definition of the layers: mean inbreeding, max inb, introgr, socio ec
	layer = new OpenLayers.Layer.WMS(
		"Mean Inbreeding",
		"http://localhost:8080/geoserver/cite/wms?",
			{layers: "cite:plzo_plz",
			sld: 'http://localhost/genmon-ch/mean_inb_lastgi.xml',
			format: 'image/gif'
			},{

                getURL: function ( bounds ){
                    var url = OpenLayers.Layer.WMS.prototype.getURL.call( this, bounds );
                    if( OpenLayers.ProxyHost && OpenLayers.String.startsWith( url, "http" ) ) {
                    url = OpenLayers.ProxyHost + encodeURIComponent(decodeURIComponent(url)); 
                }
                return url;
            }

	}); //The getURL forces the URL to go through the proxy (since port 8080 is not open) and encodes the final URL. Note that by default, WFS goes through the proxy but WMS does not...


	//for the legend. By default, the base layer. See also the map.events.register
	var sld="mean_inb_lastgi";
	var legendbegin="<img src=\"proxy.cgi?url=http://localhost:8080/geoserver/wms%3FREQUEST=GetLegendGraphic%26VERSION=1.0.0%26FORMAT=image/png%26WIDTH=20%26HEIGHT=20%26LAYER=cite:plzo_plz%26sld=http://localhost/genmon-ch/";
	var legendend=".xml\") />";
	document.getElementById("legendImage").innerHTML = legendbegin + sld + legendend;


	layer2 = new OpenLayers.Layer.WMS(
		"Max Inbreeding",
		"http://localhost:8080/geoserver/cite/wms?",
			{layers: "cite:plzo_plz",
			sld: 'http://localhost/genmon-ch/max_inb_lastgi.xml',
			format: 'image/gif', 
			transparent: 'false'
			},{

                getURL: function ( bounds ){
                    var url = OpenLayers.Layer.WMS.prototype.getURL.call( this, bounds );
                    if( OpenLayers.ProxyHost && OpenLayers.String.startsWith( url, "http" ) ) {
                    url = OpenLayers.ProxyHost + encodeURIComponent(decodeURIComponent(url)); 
                }
                return url;
            }

	}
	);

	layer2b = new OpenLayers.Layer.WMS(
		"Mean introgression",
		"http://localhost:8080/geoserver/cite/wms?",
			{layers: "cite:plzo_plz",
			sld: 'http://localhost/genmon-ch/mean_introgr_lastgi.xml',
			format: 'image/gif', 
			transparent: 'false'
			},{

                getURL: function ( bounds ){
                    var url = OpenLayers.Layer.WMS.prototype.getURL.call( this, bounds );
                    if( OpenLayers.ProxyHost && OpenLayers.String.startsWith( url, "http" ) ) {
                    url = OpenLayers.ProxyHost + encodeURIComponent(decodeURIComponent(url)); 
                }
                return url;
            }

	}
	);

	layer3 = new OpenLayers.Layer.WMS(
		"Number of animals",
		"http://localhost:8080/geoserver/cite/wms?",
			{layers: "cite:plzo_plz",
			sld: 'http://localhost/genmon-ch/num_ind_lastgi.xml',
			format: 'image/gif', 
			transparent: 'false'
			},{

                getURL: function ( bounds ){
                    var url = OpenLayers.Layer.WMS.prototype.getURL.call( this, bounds );
                    if( OpenLayers.ProxyHost && OpenLayers.String.startsWith( url, "http" ) ) {
                    url = OpenLayers.ProxyHost + encodeURIComponent(decodeURIComponent(url)); 
                }
                return url;
            }

	}
	);
	layer4 = new OpenLayers.Layer.WMS(
		"Socio-Economical Index",
		"http://localhost:8080/geoserver/cite/wms?",
			{layers: "cite:plzo_plz",
			sld: 'http://localhost/genmon-ch/index_socioec.xml',
			format: 'image/gif', 
			transparent: 'false'
			},{

                getURL: function ( bounds ){
                    var url = OpenLayers.Layer.WMS.prototype.getURL.call( this, bounds );
                    if( OpenLayers.ProxyHost && OpenLayers.String.startsWith( url, "http" ) ) {
                    url = OpenLayers.ProxyHost + encodeURIComponent(decodeURIComponent(url)); 
                }
                return url;
            }

	}
	);

	select = new OpenLayers.Layer.Vector("Selection", {styleMap: 
		new OpenLayers.Style(OpenLayers.Feature.Vector.style["select"])
	});

	map.addLayers([layer, select, layer2, layer2b, layer3, layer4]); 
        map.addControl(new OpenLayers.Control.LayerSwitcher()); 

//WFS to select polygons   
	control = new OpenLayers.Control.GetFeature({ 
		protocol: OpenLayers.Protocol.WFS.fromWMSLayer(layer),
		box: true,
		click: true,
		multipleKey: "shiftKey",
		toggleKey: "ctrlKey"
	});

//When WFS request, show the plz, inb,... of the selected feature	
	control.events.register("featureselected", this, function(e) {
		select.addFeatures([e.feature]);
		//function boucle: when several features are selected
		boucle(map.layers[1].features);

		plzsel=e.feature.attributes.plz;
		var plzselstr="PLZ: ";
		document.getElementById("showPLZ").innerHTML = plzselstr + plzsel;

		var meaninbsel=e.feature.attributes.mean_inb_lastgi;
		meaninbsel=Math.round(meaninbsel*1000)/1000;
		var meaninbselstr="Mean inbreeding: ";
		document.getElementById("MeanInb").innerHTML = meaninbselstr + meaninbsel;

		var maxinbsel=e.feature.attributes.max_inb_lastgi;
		maxinbsel=Math.round(maxinbsel*1000)/1000;
		var maxinbselstr="Max inbreeding: ";
		document.getElementById("MaxInb").innerHTML = maxinbselstr + maxinbsel;

		var meanintrsel=e.feature.attributes.mean_introgr_lastgi;
		meanintrsel=Math.round(meanintrsel*1000)/1000;
		var meanintrselstr="Mean introgression: ";
		document.getElementById("MeanIntr").innerHTML = meanintrselstr + meanintrsel;

		var numbindsel=e.feature.attributes.num_ind_lastgi;
		var numbindselstr="Number of animals: ";
		document.getElementById("NumbInd").innerHTML = numbindselstr + numbindsel;	
	});

	control.events.register("featureunselected", this, function(e) {
		select.removeFeatures([e.feature]);
	});

	map.addControl(control);		
	control.activate();
			
	//map.zoomToMaxExtent();
	map.zoomTo(2);

//Know which layer is active to display the right legend.
	map.events.register("changelayer", null, function(e){
		if(map.baseLayer.name=="Mean Inbreeding"){
			sld="mean_inb_lastgi";
		}

		else if(map.baseLayer.name=="Max Inbreeding"){
			sld="max_inb_lastgi";
		}
		else if(map.baseLayer.name=="Mean introgression"){
			sld="mean_introgr_lastgi";
		}
		else if(map.baseLayer.name=="Number of animals"){
			sld="num_ind_lastgi";
		}
		else if(map.baseLayer.name=="Socio-Economical Index"){
			sld="index_socioec";
		}
		
		document.getElementById("legendImage").innerHTML = legendbegin + sld + legendend;
	});
}

//function boucle: when several features are selected		
function boucle(obj){
	var i=0;
	var j=0;
	meaninb=0.00;
	sumind=0.00;
	sumind2=0.00;
	meanindex_socioec=0.00;
	meanintr=0.00;
	for (i in obj){
		//calc mean socio ec index
		if (obj[i].attributes.index_socioec){
			meanindex_socioec=meanindex_socioec+1*obj[i].attributes.index_socioec;
			j=j+1;
		}
		//calc mean inb weighted by the number of animals (division outside the loop)
		if (obj[i].attributes.mean_inb_lastgi){
			meaninb=meaninb+1*obj[i].attributes.mean_inb_lastgi*obj[i].attributes.num_ind_lastgi;
			sumind=sumind+1*obj[i].attributes.num_ind_lastgi;
		}
		//calc mean inb weighted by the number of animals(division outside the loop)
		if (obj[i].attributes.mean_introgr_lastgi){
			meanintr=meanintr+1*obj[i].attributes.mean_introgr_lastgi*obj[i].attributes.num_ind_lastgi;
			sumind2=sumind2+1*obj[i].attributes.num_ind_lastgi;
		}
	}
	meaninb=meaninb/sumind;
	meanintr=meanintr/sumind2;
	meaninb=Math.round(meaninb*1000)/1000;
	meanintr=Math.round(meanintr*1000)/1000;
	meanindex_socioec=Math.round(meanindex_socioec/j*1000)/1000;
//display the result in the html code (FinalMap.php)
	var meaninbstr='Mean inbreeding: ';
	document.getElementById("Inb").innerHTML = meaninbstr + meaninb;
	var meanintrstr='Mean introgression: ';
	document.getElementById("Intr").innerHTML = meanintrstr + meanintr;
	var sumindstr="Number of animals: ";
	document.getElementById("Sumind").innerHTML = sumindstr + sumind;
	var meanSEIstr="Mean Socio-Economical Index: ";
	document.getElementById("MeanSEI").innerHTML = meanSEIstr + meanindex_socioec;
}



