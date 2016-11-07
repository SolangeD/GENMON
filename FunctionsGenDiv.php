<?php

//*****************************************************************************************************************************************
//***************************************				FUNCTIONS				***********************************************************
//*****************************************************************************************************************************************

// creates and writes the xml; not used for the moment
function createSLD($nameCol, $ClassNumber, $ClassColor,$wwwDirectory, $nameXML) { //not used for the moment. should change things if used
	$textXML="<?xml version=\"1.0\" encoding=\"ISO-8859-1\"?>
<StyledLayerDescriptor version=\"1.0.0\"
    xsi:schemaLocation=\"http://www.opengis.net/sld StyledLayerDescriptor.xsd\"
    xmlns=\"http://www.opengis.net/sld\" 
    xmlns:ogc=\"http://www.opengis.net/ogc\" 
    xmlns:xlink=\"http://www.w3.org/1999/xlink\"
    xmlns:xsi=\"http://www.w3.org/2001/XMLSchema-instance\">
	
  <NamedLayer>
    <Name>cite:plzo_plz</Name>
    <UserStyle>
      <Title>inbreeding</Title>
      <FeatureTypeStyle>
        <Rule>
	<ogc:Filter>
         <ogc:PropertyIsNull>
           <ogc:PropertyName>".$nameCol."</ogc:PropertyName>
           
         </ogc:PropertyIsNull>
       </ogc:Filter>
          <PolygonSymbolizer>
            <Fill>
              <CssParameter name=\"fill\">#ffffff</CssParameter>
            </Fill>
            <Stroke>
              <CssParameter name=\"stroke\">#000000</CssParameter>
              <CssParameter name=\"stroke-width\">0</CssParameter>
            </Stroke>
          </PolygonSymbolizer>
        </Rule>
		</FeatureTypeStyle>";
	for ($i=0; $i<count($ClassNumber); $i++){
	$textXML=$textXML." 
 <FeatureTypeStyle>
        <Rule>
	<ogc:Filter>
         <ogc:PropertyIsGreaterThan>
           <ogc:PropertyName>".$nameCol."</ogc:PropertyName>
           <ogc:Literal>".$ClassNumber[$i]."</ogc:Literal>
         </ogc:PropertyIsGreaterThan>
       </ogc:Filter>
          <PolygonSymbolizer>
            <Fill>
              <CssParameter name=\"fill\">".$ClassColor[$i]."</CssParameter>
            </Fill>
            <Stroke>
              <CssParameter name=\"stroke\">#000000</CssParameter>
              <CssParameter name=\"stroke-width\">0</CssParameter>
            </Stroke>
          </PolygonSymbolizer>
        </Rule>
      </FeatureTypeStyle>";
	}
	$textXML=$textXML."
	</UserStyle>
  </NamedLayer>
</StyledLayerDescriptor>";
	$myFile2 = $wwwDirectory.$nameXML.".xml";
	$fh = fopen($myFile2, 'w') or die("can't open file");
	fwrite($fh, $textXML);
	fclose($fh);
	return;
}

function createJSfile($nameCol,$nameXML,$wwwDirectory,$fileName) {//not used for the moment. should change things if used

	$textJScode="
	var map, layer, select, control, meaninb=0.00;
		
        function init(){
			OpenLayers.ProxyHost= \"http://localhost/cgi-bin/proxy.cgi?url=\";
            map = new OpenLayers.Map(\"map\", {
			projection: new OpenLayers.Projection(\"EPSG:3857\"),
			displayProjection: new OpenLayers.Projection(\"EPSG:3857\"),
			units: \"m\",

			maxExtent: new OpenLayers.Bounds(
                650527.0,5742685.0,1176362.0,6081613.0
                ),
                controls: [
                    new OpenLayers.Control.PanZoom(),
                    new OpenLayers.Control.Permalink(),
                    new OpenLayers.Control.Navigation()
                ]
            });
		
            //changed the projection and bounds of the map
			layer = new OpenLayers.Layer.WMS(
               \"States WMS/WFS\",
               \"http://localhost:8085/geoserver/cite/wms?\",
               {layers: \"cite:plzo_plz\",
				sld: 'http://localhost/genomap/".$nameXML.".xml',
				format: 'image/gif'
				}
            );
			
			//changed the url of the wms
            select = new OpenLayers.Layer.Vector(\"Selection\", {styleMap: 
                new OpenLayers.Style(OpenLayers.Feature.Vector.style[\"select\"])
            });

            map.addLayers([layer, select]); 
            
            control = new OpenLayers.Control.GetFeature({ 
                protocol: OpenLayers.Protocol.WFS.fromWMSLayer(layer),
                box: true,
				click: true,
                multipleKey: \"shiftKey\",
                toggleKey: \"ctrlKey\"
            });
            control.events.register(\"featureselected\", this, function(e) {
                select.addFeatures([e.feature]);
				boucle(map.layers[1].features);
            });
            control.events.register(\"featureunselected\", this, function(e) {
                select.removeFeatures([e.feature]);
            });

            map.addControl(control);
			
            control.activate();
			
			map.zoomToMaxExtent();

        }
		function boucle(obj){
		var i=0;
		var j=0;
		meaninb=0.00;
		for (i in obj){
		if (map.layers[1].features[i].attributes.".$nameCol."){
			meaninb=meaninb+1*map.layers[1].features[i].attributes.inbreeding;
			j=j+1;}}
		meaninb=meaninb/(j);
		var dummystring='mean inbreeding: ';
		showStatus(dummystring.concat(meaninb));
		}
		function showStatus(text) {
            document.getElementById(\"status\").innerHTML = text;            
		}";

	// javaScript file writing
	$myFile2 = $wwwDirectory .$fileName.".js";
	$fh = fopen($myFile2, 'w') or die("can't open file");
	fwrite($fh, $textJScode);
	fclose($fh);
	return;

}


function joinSQL($refstud){
	$sql_mean_inb="UPDATE plzo_plz 
		SET mean_inb_lastgi = (SELECT mean_inb_lastgi
			FROM breed".$refstud."_inb_plz p
			WHERE p.plz=plzo_plz.plz)";
	pg_query($sql_mean_inb);

	$sql_max_inb="UPDATE plzo_plz 
		SET max_inb_lastgi = (SELECT max_inb_lastgi
			FROM breed".$refstud."_inb_plz p
			WHERE p.plz=plzo_plz.plz)";
	pg_query($sql_max_inb);

	$sql_num_ind="UPDATE plzo_plz 
		SET num_ind_lastgi = (SELECT num_ind_lastgi
			FROM breed".$refstud."_inb_plz p
			WHERE p.plz=plzo_plz.plz)";
	pg_query($sql_num_ind);

	$sql_introgr="UPDATE plzo_plz 
		SET mean_introgr_lastgi = (SELECT mean_introgr_lastgi
			FROM breed".$refstud."_inb_plz p
			WHERE p.plz=plzo_plz.plz)";
	pg_query($sql_introgr);

	$sql_inb_gen="UPDATE plzo_plz 
		SET mean_inb_gen_lastgi = (SELECT mean_inb_gen_lastgi
			FROM breed".$refstud."_inb_plz p
			WHERE p.plz=plzo_plz.plz)";
	pg_query($sql_inb_gen);

	//To know which breed is stored in the plzo_plz => update the last_log table 
	$sql_log="UPDATE last_log SET db_breed = ".$refstud."";
	pg_query($sql_log);


	return;
}
?>
